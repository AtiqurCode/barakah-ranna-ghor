<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Attach hardening response headers to every web response.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($this->headers($request) as $name => $value) {
            if ($value !== null && ! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }

    /**
     * @return array<string, string|null>
     */
    private function headers(Request $request): array
    {
        return [
            'Content-Security-Policy' => $this->contentSecurityPolicy($request),
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Permissions-Policy' => 'accelerometer=(), autoplay=(), camera=(), display-capture=(), encrypted-media=(), fullscreen=(self), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), midi=(), payment=(), usb=()',
            // Only advertise HSTS over HTTPS so local http development is unaffected.
            'Strict-Transport-Security' => $request->secure()
                ? 'max-age=31536000; includeSubDomains; preload'
                : null,
        ];
    }

    /**
     * Build the Content-Security-Policy.
     *
     * Livewire and Alpine require inline + eval script execution, so script-src
     * cannot be fully locked down without their CSP builds; the remaining
     * directives (frame-ancestors, object-src, base-uri, form-action, and the
     * source allow-lists) still provide meaningful protection.
     */
    private function contentSecurityPolicy(Request $request): string
    {
        $viteOrigin = $this->viteDevServerOrigin();
        $reverbOrigin = $this->reverbDevServerOrigin();

        $connectSrc = array_filter([
            "'self'",
            $viteOrigin,
            $viteOrigin ? $this->toWebSocketOrigin($viteOrigin) : null,
            $reverbOrigin,
        ]);

        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'".($viteOrigin ? " {$viteOrigin}" : ''),
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com".($viteOrigin ? " {$viteOrigin}" : ''),
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            'connect-src '.implode(' ', $connectSrc),
            // Stripe Checkout redirects the browser to its own hosted payment
            // page; form-action must allow that target or the redirect is blocked.
            "form-action 'self' https://checkout.stripe.com",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "object-src 'none'",
        ];

        // Force sub-resource upgrades only over HTTPS, so local http development
        // (same-origin assets) is not broken by an upgrade attempt.
        if ($request->secure()) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }

    /**
     * Resolve the running Vite dev server's origin so its cross-origin
     * script/style/HMR requests aren't blocked by the CSP in local
     * development. Returns null in any non-local environment or when
     * the dev server isn't running (no "public/hot" file).
     */
    private function viteDevServerOrigin(): ?string
    {
        if (! app()->environment('local')) {
            return null;
        }

        if (! is_file(public_path('hot'))) {
            return null;
        }

        return rtrim(trim(file_get_contents(public_path('hot'))), '/');
    }

    private function toWebSocketOrigin(string $origin): string
    {
        return preg_replace('/^http/', 'ws', $origin);
    }

    /**
     * Resolve the local Reverb broadcasting server's websocket origin so
     * Laravel Echo's connection isn't blocked by the CSP in local
     * development. Returns null in any non-local environment.
     */
    private function reverbDevServerOrigin(): ?string
    {
        if (! app()->environment('local')) {
            return null;
        }

        $options = config('broadcasting.connections.reverb.options', []);

        if (empty($options['host'])) {
            return null;
        }

        $scheme = ($options['scheme'] ?? 'https') === 'https' ? 'wss' : 'ws';

        return "{$scheme}://{$options['host']}:{$options['port']}";
    }
}
