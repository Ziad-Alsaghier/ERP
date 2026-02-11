<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [RegisteredUserController::class, 'showRegistrationForm'])
                ->middleware(['XSS'])
                ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
                ->middleware(['XSS','guest']);

Route::get('/login', [AuthenticatedSessionController::class, 'showLoginForm'])
                ->middleware(['XSS'])
                ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware(['XSS','guest']);

Route::get('/forgot-password', [AuthenticatedSessionController::class, 'showLinkRequestForm'])
                ->middleware(['XSS','guest'])
                ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
                ->middleware(['XSS','guest'])
                ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.update');

Route::get('/verify', [EmailVerificationPromptController::class, '__invoke'])
                ->middleware(['auth','XSS'])
                ->name('verification.notice');

Route::get('/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['XSS','auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->middleware(['auth','XSS'])
                ->name('password.confirm');

                Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
                ->middleware('auth');

                Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');

Route::get('/choose', [AuthenticatedSessionController::class, 'chooseAccount'])
                                ->middleware(['auth','XSS'])
                                ->name('choose.account');
