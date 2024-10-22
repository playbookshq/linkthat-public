<?php

use App\Http\Controllers\SubscriptionController;

Route::post('/subscription/create', [SubscriptionController::class, 'create'])
    ->middleware(['auth'])
    ->name('subscription.create');
