<?php

namespace App\Http\Controllers;

use App\Actions\CreateStripeCheckoutSession;
use App\Actions\ReconcileOrderWithStripe;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function store(Product $product, CreateStripeCheckoutSession $createCheckoutSession): RedirectResponse
    {
        return redirect()->away($createCheckoutSession($product));
    }

    public function success(Request $request, ReconcileOrderWithStripe $reconcile): View
    {
        $order = Order::query()
            ->with('product')
            ->where('stripe_checkout_session_id', $request->string('session_id')->value())
            ->firstOrFail();

        // The webhook is the usual path to "paid"; this catches the shopper
        // landing here before it arrives (or if it never does).
        $order = $reconcile($order);

        return view('checkout.success', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request): View
    {
        $order = Order::query()
            ->with('product')
            ->where('stripe_checkout_session_id', $request->string('session_id')->value())
            ->firstOrFail();

        return view('checkout.cancel', [
            'order' => $order,
        ]);
    }
}
