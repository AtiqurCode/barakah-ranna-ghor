<?php

namespace App\Support;

class WhatsApp
{
    /**
     * Build a wa.me deep link to the business number with an optional prefilled message.
     */
    public static function link(?string $message = null): string
    {
        $number = preg_replace('/\D/', '', (string) config('barakah.whatsapp'));

        $url = 'https://wa.me/'.$number;

        if ($message !== null && $message !== '') {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    /**
     * A generic "I have a question" greeting link for the active locale.
     */
    public static function greeting(): string
    {
        return self::link(__('site.whatsapp.greeting'));
    }

    /**
     * An order link prefilled with the given product name.
     */
    public static function order(string $productName): string
    {
        return self::link(__('site.whatsapp.order_prefix').$productName);
    }
}
