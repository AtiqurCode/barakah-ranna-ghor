<?php

use App\Ai\Agents\ProductAdvisor;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Ai\Streaming\Events\TextDelta;
use Livewire\Component;

new class extends Component
{
    /** @var list<array{role: string, content: string}> */
    public array $messages = [];

    public string $draft = '';

    /** The question for the turn currently being answered ('' when idle). */
    public string $question = '';

    /** The assistant reply as it streams in for the current turn. */
    public string $answer = '';

    public string $error = '';

    /**
     * Accept the customer's message, then trigger the streamed reply.
     */
    public function send(): void
    {
        $this->error = '';
        $prompt = trim($this->draft);

        // Ignore empty input or a submit while a turn is still streaming.
        if ($prompt === '' || $this->question !== '') {
            return;
        }

        $key = 'chat:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 20)) {
            $this->error = __('site.chat.rate_limited');

            return;
        }

        RateLimiter::hit($key, decaySeconds: 60);

        $this->question = mb_substr($prompt, 0, 500);
        $this->draft = '';

        // Kick off a second request that streams the model's reply.
        $this->js('$wire.ask()');
    }

    /**
     * Stream the agent's grounded reply into the current assistant bubble.
     */
    public function ask(): void
    {
        $this->answer = '';

        try {
            $advisor = new ProductAdvisor($this->messages, app()->getLocale());

            foreach ($advisor->stream($this->question) as $event) {
                if (! $event instanceof TextDelta) {
                    continue;
                }

                $this->answer .= $event->delta;
                $this->stream(to: 'answer', content: $event->delta);
            }
        } catch (\Throwable $e) {
            report($e);
            $this->answer = __('site.chat.error');
        }

        $this->messages[] = ['role' => 'user', 'content' => $this->question];
        $this->messages[] = ['role' => 'assistant', 'content' => $this->answer !== '' ? $this->answer : __('site.chat.error')];

        $this->question = '';
        $this->answer = '';
    }
};
?>

<div
    x-data="{ open: false }"
    x-init="
        $watch('open', value => value && $nextTick(() => { $refs.scroll.scrollTop = $refs.scroll.scrollHeight }));
        new MutationObserver(() => { if ($refs.scroll) $refs.scroll.scrollTop = $refs.scroll.scrollHeight })
            .observe($refs.scroll, { childList: true, subtree: true, characterData: true });
    "
    class="fixed bottom-20 right-5 z-80 sm:right-6"
>
    {{-- Launcher --}}
    <button
        type="button"
        x-show="!open"
        x-on:click="open = true"
        title="{{ __('site.chat.launcher') }}"
        aria-label="{{ __('site.chat.launcher') }}"
        class="btn-tap grid size-14 place-items-center rounded-full bg-brand-accent text-brand-accent-ink shadow-lg sm:size-14"
    >
        <x-icon.chat class="size-7" />
    </button>

    {{-- Panel --}}
    <div
        x-show="open"
        x-cloak
        x-transition.origin.bottom.right
        class="flex h-3/4 w-11/12 max-w-sm flex-col overflow-hidden rounded-2xl border border-brand-border bg-brand-surface shadow-2xl"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 border-b border-brand-border bg-brand-s2 px-4 py-3">
            <div class="flex items-center gap-2.5">
                <span class="grid size-9 place-items-center rounded-full bg-brand-accent text-brand-accent-ink">
                    <x-icon.chat class="size-5" />
                </span>
                <div>
                    <p class="text-[15px] font-semibold leading-tight">{{ __('site.chat.title') }}</p>
                    <p class="text-[12px] text-brand-muted">{{ __('site.chat.subtitle') }}</p>
                </div>
            </div>
            <button
                type="button"
                x-on:click="open = false"
                aria-label="{{ __('site.chat.close') }}"
                class="btn-tap grid size-8 place-items-center rounded-full text-brand-muted hover:bg-brand-bg"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div x-ref="scroll" class="flex-1 space-y-3 overflow-y-auto px-4 py-4">
            <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-brand-s2 px-3.5 py-2.5 text-[14px] leading-relaxed text-brand-text">
                {{ __('site.chat.greeting') }}
            </div>

            @foreach ($messages as $message)
                <div
                    wire:key="msg-{{ $loop->index }}"
                    class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}"
                >
                    <div class="max-w-[85%] whitespace-pre-line rounded-2xl px-3.5 py-2.5 text-[14px] leading-relaxed {{ $message['role'] === 'user' ? 'rounded-tr-sm bg-brand-accent text-brand-accent-ink' : 'rounded-tl-sm bg-brand-s2 text-brand-text' }}">{{ $message['content'] }}</div>
                </div>
            @endforeach

            {{-- The turn currently being answered --}}
            @if ($question !== '')
                <div class="flex justify-end">
                    <div class="max-w-[85%] whitespace-pre-line rounded-2xl rounded-tr-sm bg-brand-accent px-3.5 py-2.5 text-[14px] leading-relaxed text-brand-accent-ink">{{ $question }}</div>
                </div>
                <div class="flex justify-start">
                    <div class="max-w-[85%] whitespace-pre-line rounded-2xl rounded-tl-sm bg-brand-s2 px-3.5 py-2.5 text-[14px] leading-relaxed text-brand-text">
                        <span wire:stream="answer">{{ $answer }}</span>
                        <span wire:loading wire:target="ask" class="ml-0.5 inline-block animate-pulse">▍</span>
                    </div>
                </div>
            @endif
        </div>

        @if ($error !== '')
            <p class="px-4 pt-1 text-[12px] text-red-500">{{ $error }}</p>
        @endif

        {{-- Composer --}}
        <form wire:submit="send" class="flex items-center gap-2 border-t border-brand-border px-3 py-3">
            <input
                wire:model="draft"
                type="text"
                maxlength="500"
                autocomplete="off"
                placeholder="{{ __('site.chat.placeholder') }}"
                x-bind:disabled="$wire.question !== ''"
                class="flex-1 rounded-full border border-brand-border bg-brand-bg px-4 py-2 text-[14px] text-brand-text outline-none focus:border-brand-accent disabled:opacity-60"
            >
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="send,ask"
                x-bind:disabled="! $wire.draft.trim() || $wire.question !== ''"
                aria-label="{{ __('site.chat.send') }}"
                class="btn-tap grid size-10 shrink-0 place-items-center rounded-full bg-brand-accent text-brand-accent-ink disabled:opacity-50"
            >
                <x-icon.send class="size-5" />
            </button>
        </form>
    </div>
</div>
