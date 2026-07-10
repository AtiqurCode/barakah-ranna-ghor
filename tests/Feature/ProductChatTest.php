<?php

use App\Ai\Agents\ProductAdvisor;
use Database\Seeders\ProductSeeder;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Prompts\AgentPrompt;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

it('renders the chat widget on the storefront', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSeeLivewire('product-chat')
        ->assertSee('Ask our product guide');
});

it('grounds the agent with the localized product catalogue', function () {
    ProductAdvisor::fake(['Try the mustard oil.']);

    Livewire::test('product-chat')
        ->set('draft', 'Which oil is best for frying?')
        ->call('send')
        ->call('ask');

    ProductAdvisor::assertPrompted(function (AgentPrompt $prompt) {
        $instructions = (string) $prompt->agent->instructions();

        return $prompt->contains('Which oil is best for frying?')
            && str_contains($instructions, 'Cold-Pressed Mustard Oil')
            && str_contains($instructions, '৳320');
    });
});

it('streams the reply and stores the conversation', function () {
    ProductAdvisor::fake(['Try the mustard oil.']);

    $component = Livewire::test('product-chat')
        ->set('draft', 'Which oil is best?')
        ->call('send')
        ->assertSet('question', 'Which oil is best?')
        ->assertSet('draft', '')
        ->call('ask')
        ->assertSet('question', '');

    expect($component->get('messages'))
        ->toHaveCount(2)
        ->and($component->get('messages')[0])->toMatchArray(['role' => 'user', 'content' => 'Which oil is best?'])
        ->and($component->get('messages')[1])->toMatchArray(['role' => 'assistant', 'content' => 'Try the mustard oil.']);
});

it('passes prior turns to the agent as conversation context', function () {
    ProductAdvisor::fake(['First reply.', 'Second reply.']);

    $component = Livewire::test('product-chat')
        ->set('draft', 'Do you sell honey?')
        ->call('send')
        ->call('ask')
        ->set('draft', 'How much is it?')
        ->call('send')
        ->call('ask');

    expect($component->get('messages'))->toHaveCount(4);

    // The second prompt must carry the first turn's two messages as history.
    ProductAdvisor::assertPrompted(function (AgentPrompt $prompt) {
        $agent = $prompt->agent;

        if (! $agent instanceof Conversational) {
            return false;
        }

        $history = collect($agent->messages());

        return $prompt->contains('How much is it?')
            && $history->count() === 2
            && $history->first()?->content === 'Do you sell honey?';
    });
});

it('ignores empty submissions', function () {
    ProductAdvisor::fake();

    Livewire::test('product-chat')
        ->set('draft', '   ')
        ->call('send')
        ->assertSet('question', '');

    ProductAdvisor::assertNeverPrompted();
});

it('rate limits rapid submissions', function () {
    ProductAdvisor::fake();

    $key = 'chat:127.0.0.1';
    for ($i = 0; $i < 20; $i++) {
        RateLimiter::hit($key, 60);
    }

    Livewire::test('product-chat')
        ->set('draft', 'Hello?')
        ->call('send')
        ->assertSet('question', '')
        ->assertSet('error', __('site.chat.rate_limited'));

    ProductAdvisor::assertNeverPrompted();
});

it('shows a friendly error when the provider is unreachable', function () {
    config()->set('ai.providers.ollama.url', 'http://127.0.0.1:1');

    $component = Livewire::test('product-chat')
        ->set('draft', 'Which honey do you sell?')
        ->call('send')
        ->call('ask');

    expect($component->get('messages')[1]['content'])->toBe(__('site.chat.error'));
});

it('builds a Bengali system prompt with localized names and prices', function () {
    $instructions = (string) (new ProductAdvisor(locale: 'bn'))->instructions();

    expect($instructions)
        ->toContain('কোল্ড-প্রেসড সরিষার তেল')
        ->toContain('৳৩২০');

    // The app locale must be restored afterwards.
    expect(app()->getLocale())->toBe(config('app.locale'));
});
