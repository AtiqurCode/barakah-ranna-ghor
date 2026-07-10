<?php

use App\Models\ContactMessage;
use App\Models\Subscriber;
use App\Models\User;
use Livewire\Livewire;

it('sends hardening security headers on responses', function () {
    $response = $this->get(route('home'))
        ->assertOk()
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    expect($response->headers->get('Content-Security-Policy'))
        ->toContain("frame-ancestors 'none'")
        ->toContain("object-src 'none'")
        ->toContain("default-src 'self'");
});

it('never exposes is_admin to mass assignment', function () {
    expect((new User)->getFillable())->not->toContain('is_admin');
});

it('drops bot contact submissions caught by the honeypot', function () {
    Livewire::test('pages::contact')
        ->set('name', 'Bot')
        ->set('phone', '01700000000')
        ->set('message', 'spam spam spam')
        ->set('website', 'http://spam.example') // honeypot filled
        ->call('submit')
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(0);
});

it('rejects contact messages with an invalid phone number', function () {
    Livewire::test('pages::contact')
        ->set('name', 'Real Person')
        ->set('phone', 'not-a-phone!!')
        ->set('message', 'Hello there')
        ->call('submit')
        ->assertHasErrors(['phone']);

    expect(ContactMessage::count())->toBe(0);
});

it('drops bot newsletter submissions caught by the honeypot', function () {
    Livewire::test('newsletter')
        ->set('email', 'bot@spam.example')
        ->set('website', 'filled') // honeypot filled
        ->call('subscribe')
        ->assertSet('subscribed', true);

    expect(Subscriber::count())->toBe(0);
});

it('rate limits repeated newsletter submissions', function () {
    $component = Livewire::test('newsletter');

    for ($i = 0; $i < 5; $i++) {
        $component->set('email', "reader{$i}@example.com")->call('subscribe');
    }

    // The 6th attempt within the window is throttled.
    $component->set('email', 'reader-blocked@example.com')
        ->call('subscribe')
        ->assertHasErrors('email');

    expect(Subscriber::whereEmail('reader-blocked@example.com')->exists())->toBeFalse();
});
