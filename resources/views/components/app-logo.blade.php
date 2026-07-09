@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Barakah Ranna Ghor" {{ $attributes }}>
        <x-slot name="logo">
            <x-brand-logo class="size-8" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Barakah Ranna Ghor" {{ $attributes }}>
        <x-slot name="logo">
            <x-brand-logo class="size-8" />
        </x-slot>
    </flux:brand>
@endif
