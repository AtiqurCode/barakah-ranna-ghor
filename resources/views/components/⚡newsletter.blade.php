<?php

use App\Models\Subscriber;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|email:rfc,strict|max:255')]
    public string $email = '';

    /** Honeypot — must stay empty for real users. */
    public string $website = '';

    public bool $subscribed = false;

    public function subscribe(): void
    {
        // Silently absorb bot submissions that fill the hidden honeypot.
        if ($this->website !== '') {
            $this->subscribed = true;

            return;
        }

        $key = 'newsletter:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many attempts. Please try again in a minute.'),
            ]);
        }

        $this->validate();

        RateLimiter::hit($key, decaySeconds: 60);

        Subscriber::firstOrCreate(['email' => $this->email]);

        $this->subscribed = true;
        $this->reset('email');
    }
};
?>

<div>
    @if ($subscribed)
        <div class="animate-pop rounded-[11px] bg-brand-accent-soft px-3.5 py-3 text-sm font-semibold text-brand-accent">
            {{ __('site.f_subbed') }}
        </div>
    @else
        <form wire:submit="subscribe" class="flex gap-2">
            {{-- Honeypot: hidden from users, tempting to bots. --}}
            <input
                type="text"
                wire:model="website"
                name="website"
                tabindex="-1"
                autocomplete="off"
                class="sr-only"
                aria-hidden="true"
            >
            <input
                type="email"
                wire:model="email"
                placeholder="{{ __('site.f_email_ph') }}"
                autocomplete="email"
                class="h-[46px] min-w-0 flex-1 rounded-[11px] border border-brand-border bg-brand-bg px-3.5 text-[14.5px] text-brand-text outline-none focus:border-brand-accent"
            >
            <button
                type="submit"
                class="btn-press btn-glow h-[46px] whitespace-nowrap rounded-[11px] bg-brand-accent px-[18px] text-sm font-semibold text-brand-accent-ink"
            >
                {{ __('site.f_sub_btn') }}
            </button>
        </form>
        @error('email')
            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
        @enderror
    @endif
</div>
