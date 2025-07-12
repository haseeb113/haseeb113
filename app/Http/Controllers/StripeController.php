<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\WebhookResponse;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function checkout()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Premium Access',
                    ],
                    'unit_amount' => 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('premium.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('home'),
            'metadata' => [
                'user_id' => auth()->id(),
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        return view('premium.success');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $userId = $session->metadata->user_id;

            \DB::table('users')->where('id', $userId)->update(['is_premium' => 1]);
        }

        WebhookResponse::create([
            'user_id' => $session->metadata->user_id ?? null,
            'payload' => $payload,
        ]);

        return response('Webhook Handled', 200);
    }
}