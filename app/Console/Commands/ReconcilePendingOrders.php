<?php

namespace App\Console\Commands;

use App\Actions\ReconcileOrderWithStripe;
use App\Models\Order;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('orders:reconcile {--minutes=15 : Only check orders that have been pending for at least this many minutes}')]
#[Description('Re-check orders stuck pending against Stripe, for when a webhook was missed')]
class ReconcilePendingOrders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ReconcileOrderWithStripe $reconcile): int
    {
        $stuck = Order::query()
            ->pending()
            ->whereNotNull('stripe_checkout_session_id')
            ->where('created_at', '<=', now()->subMinutes((int) $this->option('minutes')))
            ->get();

        foreach ($stuck as $order) {
            $reconcile($order);
        }

        $this->info("Checked {$stuck->count()} pending order(s) against Stripe.");

        return self::SUCCESS;
    }
}
