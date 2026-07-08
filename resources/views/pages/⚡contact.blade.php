<?php

use App\Models\ContactMessage;
use App\Support\WhatsApp;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.storefront')] class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:50')]
    public string $phone = '';

    #[Validate('required|string|max:2000')]
    public string $message = '';

    public bool $sent = false;

    public function submit(): void
    {
        $validated = $this->validate();

        ContactMessage::create($validated);

        $this->sent = true;
        $this->reset('name', 'phone', 'message');
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'info' => __('site.c_info'),
            'whatsappLink' => WhatsApp::greeting(),
        ];
    }
};
?>

<div>
    <section class="mx-auto max-w-[1180px] px-6 pb-[76px] pt-16">
        <div class="max-w-[560px]">
            <h1 class="font-newsreader text-[50px] font-normal -tracking-[.01em]">{{ __('site.c_title') }}</h1>
            <p class="mt-3 text-base leading-relaxed text-brand-muted text-pretty">{{ __('site.c_sub') }}</p>
        </div>

        <div class="mt-10 grid grid-cols-1 items-start gap-11 lg:grid-cols-[1.2fr_.8fr]">
            {{-- Form --}}
            <div class="rounded-[20px] border border-brand-border bg-brand-surface p-8">
                @if ($sent)
                    <div class="animate-pop flex flex-col items-center py-8 text-center">
                        <span class="mb-4 grid size-[58px] place-items-center rounded-full bg-brand-accent-soft text-brand-accent">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                        </span>
                        <p class="text-lg font-semibold">{{ __('site.c_sent') }}</p>
                    </div>
                @else
                    <form wire:submit="submit" class="flex flex-col gap-4">
                        <label class="flex flex-col gap-[7px] text-[13px] font-semibold">{{ __('site.c_name') }}
                            <input type="text" wire:model="name" class="h-12 rounded-[11px] border border-brand-border bg-brand-bg px-3.5 text-[15px] text-brand-text outline-none focus:border-brand-accent">
                            @error('name') <span class="text-xs font-normal text-red-500">{{ $message }}</span> @enderror
                        </label>
                        <label class="flex flex-col gap-[7px] text-[13px] font-semibold">{{ __('site.c_phone') }}
                            <input type="tel" wire:model="phone" class="h-12 rounded-[11px] border border-brand-border bg-brand-bg px-3.5 text-[15px] text-brand-text outline-none focus:border-brand-accent">
                            @error('phone') <span class="text-xs font-normal text-red-500">{{ $message }}</span> @enderror
                        </label>
                        <label class="flex flex-col gap-[7px] text-[13px] font-semibold">{{ __('site.c_msg') }}
                            <textarea rows="4" wire:model="message" class="resize-y rounded-[11px] border border-brand-border bg-brand-bg px-3.5 py-3 text-[15px] text-brand-text outline-none focus:border-brand-accent"></textarea>
                            @error('message') <span class="text-xs font-normal text-red-500">{{ $message }}</span> @enderror
                        </label>
                        <button type="submit" class="h-[52px] rounded-xl bg-brand-accent text-[15px] font-semibold text-brand-accent-ink transition hover:brightness-105">{{ __('site.c_send') }}</button>
                    </form>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="flex flex-col gap-4">
                <div class="rounded-[20px] bg-brand-accent p-7 text-brand-accent-ink">
                    <h3 class="mb-1.5 text-[19px] font-semibold">{{ __('site.c_wa_title') }}</h3>
                    <p class="mb-[18px] text-[14.5px] leading-snug opacity-90">{{ __('site.c_wa_body') }}</p>
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="flex h-12 w-full items-center justify-center gap-2.5 rounded-[11px] bg-brand-accent-ink text-[14.5px] font-semibold text-brand-accent transition-transform hover:-translate-y-0.5">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-8.5 8.5 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7a8.5 8.5 0 0 1 3.6-11.3 8.4 8.4 0 0 1 12.5 7.5z" /></svg>{{ __('site.c_wa_btn') }}
                    </a>
                </div>

                <div class="flex flex-col gap-[18px] rounded-[20px] border border-brand-border bg-brand-surface p-[26px]">
                    @foreach ($info as $item)
                        <div class="flex items-start gap-[13px]">
                            <span class="grid size-10 flex-none place-items-center rounded-[11px] bg-brand-accent-soft text-xs font-bold text-brand-accent">{{ $item['k'] }}</span>
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[.04em] text-brand-muted">{{ $item['label'] }}</div>
                                <div class="mt-[3px] text-[15px] font-medium">{{ $item['value'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
