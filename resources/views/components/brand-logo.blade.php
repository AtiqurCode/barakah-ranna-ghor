{{--
    Barakah Ranna Ghor brand mark: a rounded badge with a green gradient,
    a soft top sheen and inner ring, the Bengali "ব", and a bowl cradle
    beneath it (a subtle kitchen cue). A self-contained <svg> (fixed brand
    colours, sizes from the passed class via its viewBox) so it renders
    identically on the storefront and in the Flux admin/auth screens.
--}}
<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
    {{ $attributes->class('inline-block') }}>
    <defs>
        <linearGradient id="brandBadge" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#18a978" />
            <stop offset="1" stop-color="#0a6a49" />
        </linearGradient>
        <linearGradient id="brandSheen" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#ffffff" stop-opacity=".28" />
            <stop offset=".6" stop-color="#ffffff" stop-opacity="0" />
        </linearGradient>
    </defs>

    <rect width="40" height="40" rx="11.5" fill="url(#brandBadge)" />
    <rect width="40" height="40" rx="11.5" fill="url(#brandSheen)" />
    <rect x=".75" y=".75" width="38.5" height="38.5" rx="10.75" fill="none" stroke="#ffffff" stroke-opacity=".2" />

    {{-- bowl / cradle --}}
    <path d="M12.4 26.2C13.7 29.7 16.6 31.4 20 31.4C23.4 31.4 26.3 29.7 27.6 26.2"
        fill="none" stroke="#ffffff" stroke-opacity=".95" stroke-width="2.1" stroke-linecap="round" />

    {{-- Bengali "ba" --}}
    <text x="20" y="16.3" text-anchor="middle" dominant-baseline="central"
        font-family="'Hind Siliguri','Noto Sans Bengali',serif" font-weight="700" font-size="19" fill="#ffffff">ব</text>
</svg>
