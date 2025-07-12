<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\WebhookResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function checkout()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Premium Access',
                    ],
                    'unit_amount' => 100, // $1 = 100 cents
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
        return redirect('/')->with('success', '✅ Payment successful. Premium Membership activated.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload);

        if ($event && $event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if (isset($session->metadata->user_id)) {
                $userId = $session->metadata->user_id;

                // Mark user as premium
                DB::table('users')->where('id', $userId)->update(['is_premium' => 1]);

                // Log the webhook
                WebhookResponse::create([
                    'user_id' => $userId,
                    'payload' => $payload,
                ]);
            }
        } else {
            // Store all events anyway for logging
            WebhookResponse::create([
                'user_id' => null,
                'payload' => $payload,
            ]);
        }

        return response('Webhook Handled', 200);
    }
}
