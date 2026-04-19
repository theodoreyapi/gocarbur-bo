<?php

use App\Http\Controllers\AbonnementsController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\BannieresController;
use App\Http\Controllers\ConseilsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandesController;
use App\Http\Controllers\GaragesController;
use App\Http\Controllers\JournauxController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\StationsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VersionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.login');
});

Route::resource('index', DashboardController::class);
Route::resource('users', UsersController::class);
Route::resource('activity-logs', JournauxController::class);
Route::resource('app-versions', VersionsController::class);
Route::resource('articles', ConseilsController::class);
Route::resource('banners', BannieresController::class);
Route::resource('stations', StationsController::class);
Route::resource('garages', GaragesController::class);
Route::resource('notifications', NotificationsController::class);
Route::resource('partner-requests', DemandesController::class);
Route::resource('payments', PaiementsController::class);
Route::resource('promotions', PromotionsController::class);
Route::resource('reviews', AvisController::class);
Route::resource('settings', ParametresController::class);
Route::resource('subscriptions', AbonnementsController::class);
