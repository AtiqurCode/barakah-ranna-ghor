<?php

namespace App\Ai\Agents;

use App\Models\Product;
use Illuminate\Support\Facades\App;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

/**
 * The storefront's product-advice agent.
 *
 * The catalogue is small, so the whole localized product list is stuffed into
 * the instructions rather than retrieved via embeddings. `instructions()` is
 * the seam to swap in a filtered/vector query if the catalogue ever grows.
 */
#[Timeout(120)]
class ProductAdvisor implements Agent, Conversational
{
    use Promptable;

    /**
     * @param  list<array{role: string, content: string}>  $history  Completed turns, oldest first.
     */
    public function __construct(
        protected array $history = [],
        protected string $locale = 'en',
    ) {}

    /**
     * The grounding system prompt: brand persona plus the localized catalogue.
     */
    public function instructions(): Stringable|string
    {
        return $this->withLocale($this->locale, function (): string {
            $brand = __('site.brand');
            $catalogue = Product::ordered()->get()
                ->map(fn (Product $product): string => $this->productLine($product))
                ->implode("\n");

            $language = App::getLocale() === 'bn' ? 'Bengali' : 'English';

            return <<<PROMPT
                You are the friendly shopping assistant for {$brand}, a Bangladeshi store selling pure, natural kitchen products. Help customers choose the best product for their needs.

                Rules:
                - Only recommend products from the catalogue below. Never invent products, prices, or claims.
                - Reply in {$language}, matching the customer's language and tone. Keep answers short and warm.
                - When you suggest a product, mention its name and price, and briefly why it fits.
                - For orders, delivery, or anything not in the catalogue, invite them to message on WhatsApp or use the Contact page.

                Catalogue:
                {$catalogue}
                PROMPT;
        });
    }

    /**
     * The conversation so far, excluding the turn currently being answered.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return array_map(
            fn (array $message): Message => new Message($message['role'], $message['content']),
            $this->history,
        );
    }

    /**
     * One catalogue line describing a product in the active locale.
     */
    protected function productLine(Product $product): string
    {
        return sprintf(
            '- %s [%s] — %s%s. %s %s Highlights: %s',
            $this->text($product, 'name'),
            $product->categoryLabel(),
            $product->priceLabel(),
            $this->text($product, 'unit'),
            $this->text($product, 'tag'),
            $this->text($product, 'description'),
            $this->text($product, 'details'),
        );
    }

    /**
     * Resolve a translatable attribute to a single localized string,
     * joining list-shaped values (e.g. `details`) with separators.
     */
    protected function text(Product $product, string $attribute): string
    {
        $value = $product->translate($attribute);

        return is_array($value) ? implode('; ', $value) : $value;
    }

    /**
     * Run a callback with the app locale temporarily switched, then restore it.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    protected function withLocale(string $locale, callable $callback): mixed
    {
        $previous = App::getLocale();
        App::setLocale($locale);

        try {
            return $callback();
        } finally {
            App::setLocale($previous);
        }
    }
}
