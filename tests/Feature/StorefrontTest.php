<?php

use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\Subscriber;
use Database\Seeders\ProductSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

it('renders the home page with featured products', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Barakah Ranna Ghor')
        ->assertSee('Cold-Pressed Mustard Oil');
});

it('renders the products page', function () {
    $this->get(route('products'))
        ->assertOk()
        ->assertSee('All products');
});

it('renders a product detail page bound by slug', function () {
    $this->get(route('product', Product::where('slug', 'honey')->first()))
        ->assertOk()
        ->assertSee('Wild Natural Honey')
        ->assertSee('Order on WhatsApp');
});

it('renders about and contact pages', function () {
    $this->get(route('about'))->assertOk()->assertSee('Real food, the way it used to be.');
    $this->get(route('contact'))->assertOk()->assertSee('Let');
});

it('filters products by category', function () {
    Livewire::test('pages::products')
        ->assertSee('Pure Cow Ghee')
        ->set('filter', 'spices')
        ->assertSee('Red Chili Powder')
        ->assertDontSee('Pure Cow Ghee');
});

it('stores a contact message when the form is submitted', function () {
    Livewire::test('pages::contact')
        ->set('name', 'Atiqur')
        ->set('phone', '01700000000')
        ->set('message', 'Do you deliver to Chittagong?')
        ->call('submit')
        ->assertSet('sent', true);

    expect(ContactMessage::where('name', 'Atiqur')->exists())->toBeTrue();
});

it('validates the contact form', function () {
    Livewire::test('pages::contact')
        ->call('submit')
        ->assertHasErrors(['name', 'phone', 'message']);
});

it('subscribes an email to the newsletter', function () {
    Livewire::test('newsletter')
        ->set('email', 'reader@example.com')
        ->call('subscribe')
        ->assertSet('subscribed', true);

    expect(Subscriber::where('email', 'reader@example.com')->exists())->toBeTrue();
});

it('switches locale to Bengali and shows translated content', function () {
    $this->get(route('language.switch', 'bn'))->assertRedirect();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('বরকত রান্নাঘর', false)
        ->assertSee('কোল্ড-প্রেসড সরিষার তেল', false);
});
