<?php

use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['email_verified_at' => now()]);
});

it('blocks guests from the admin area', function () {
    $this->get(route('admin.products'))->assertRedirect(route('login'));
});

it('lists products for an authenticated user', function () {
    $product = Product::factory()->create(['name' => ['en' => 'Test Oil', 'bn' => 'টেস্ট']]);

    $this->actingAs($this->admin)
        ->get(route('admin.products'))
        ->assertOk()
        ->assertSee('Test Oil');
});

it('creates a product and auto-generates the slug', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.product-create')
        ->set('form.name_en', 'Black Seed Oil')
        ->set('form.name_bn', 'কালোজিরার তেল')
        ->set('form.category', 'oils')
        ->set('form.price', 550)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products'));

    $product = Product::firstWhere('slug', 'black-seed-oil');

    expect($product)->not->toBeNull()
        ->and($product->price)->toBe(550)
        ->and($product->name['bn'])->toBe('কালোজিরার তেল');
});

it('validates required fields when creating', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.product-create')
        ->set('form.name_en', '')
        ->set('form.price', null)
        ->call('save')
        ->assertHasErrors(['form.name_en', 'form.name_bn', 'form.price']);
});

it('parses detail lines into an array', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.product-create')
        ->set('form.name_en', 'Honey')
        ->set('form.name_bn', 'মধু')
        ->set('form.slug', 'raw-honey')
        ->set('form.price', 700)
        ->set('form.details_en', "Raw & unprocessed\nNever heated\n")
        ->call('save')
        ->assertHasNoErrors();

    expect(Product::firstWhere('slug', 'raw-honey')->details['en'])
        ->toBe(['Raw & unprocessed', 'Never heated']);
});

it('updates an existing product', function () {
    $product = Product::factory()->create(['price' => 100]);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.product-edit', ['product' => $product])
        ->set('form.price', 275)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products'));

    expect($product->refresh()->price)->toBe(275);
});

it('toggles the featured flag', function () {
    $product = Product::factory()->create(['is_featured' => false]);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.products')
        ->call('toggleFeatured', $product)
        ->assertOk();

    expect($product->refresh()->is_featured)->toBeTrue();
});

it('deletes a product', function () {
    $product = Product::factory()->create();

    Livewire::actingAs($this->admin)
        ->test('pages::admin.products')
        ->call('delete', $product);

    expect(Product::find($product->id))->toBeNull();
});

it('shows and deletes contact messages', function () {
    $message = ContactMessage::create(['name' => 'Rahim', 'phone' => '0170', 'message' => 'Hello']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.messages')
        ->assertSee('Rahim')
        ->call('delete', $message);

    expect(ContactMessage::find($message->id))->toBeNull();
});

it('shows and deletes subscribers', function () {
    $subscriber = Subscriber::create(['email' => 'reader@example.com']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.subscribers')
        ->assertSee('reader@example.com')
        ->call('delete', $subscriber);

    expect(Subscriber::find($subscriber->id))->toBeNull();
});
