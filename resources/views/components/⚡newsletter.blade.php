<?php

use App\Models\Subscriber;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public bool $subscribed = false;

    public function subscribe(): void
    {
        $this->validate();

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
            <input
                type="email"
                wire:model="email"
                placeholder="{{ __('site.f_email_ph') }}"
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
