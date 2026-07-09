@props([
    'src' => '',
    'alt' => '',
    'eager' => false,
])

{{--
    Lazy image with a visible loading state. Shows an animated shimmer until
    the image finishes decoding, then fades it in. Pass `eager` for
    above-the-fold images (loads immediately, high priority); everything else
    defers via loading="lazy" and decodes off the main thread.
--}}
<div
    x-data="{ loaded: false }"
    {{ $attributes->class('lazy-img relative overflow-hidden') }}
>
    <div
        class="lazy-shimmer absolute inset-0"
        x-show="!loaded"
        x-transition:leave="transition-opacity duration-500 ease-out"
        x-transition:leave-end="opacity-0"
    ></div>

    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        @if ($eager) fetchpriority="high" @else loading="lazy" @endif
        decoding="async"
        x-init="$el.complete && $el.naturalWidth > 0 ? loaded = true : null"
        x-on:load="loaded = true"
        x-on:error="loaded = true"
        :class="loaded ? 'opacity-100' : 'opacity-0'"
        class="size-full object-cover transition-opacity duration-700 ease-out"
    >
</div>
