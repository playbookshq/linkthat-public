<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $priceId = config('stripe.price_id');

        return $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('dashboard'),
                'cancel_url' => route('dashboard'),
            ]);
    }
}
