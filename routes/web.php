<?php

use App\Http\Controllers\Admin\ChatbotController;
use App\Http\Controllers\Admin\ChatbotRuleController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MessageTemplateController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WhatsAppWebController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
        Route::resource('products', ProductController::class);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('customers', CustomerController::class)->only(['index', 'show']);
        Route::resource('templates', MessageTemplateController::class);
        Route::resource('chatbot-rules', ChatbotRuleController::class);
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('whatsapp-web', [WhatsAppWebController::class, 'index'])->name('whatsapp-web.index');
    });
});

require __DIR__.'/auth.php';
