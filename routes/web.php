<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\OrderPage;
use App\Livewire\CheckoutPage;
use App\Livewire\SuccessPage;

// Arahkan halaman utama langsung ke OrderPage
Route::get('/', OrderPage::class)->name('order');

Route::get('/checkout', CheckoutPage::class)->name('checkout');

Route::get('/success/{id}', SuccessPage::class)->name('success');