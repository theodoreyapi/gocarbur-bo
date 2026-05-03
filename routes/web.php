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

// ⚠️ Export AVANT resource (sinon conflit avec {id})
Route::get('admin/users/export', [UsersController::class, 'export'])->name('users.export');
Route::resource('users', UsersController::class);
Route::post('admin/users/{id}/grant-premium', [UsersController::class, 'grantPremium'])->name('users.grantPremium');
Route::post('admin/users/{id}/suspend', [UsersController::class, 'suspend'])->name('users.suspend');
Route::post('admin/users/{id}/reactivate', [UsersController::class, 'reactivate'])->name('users.reactivate');
Route::delete('admin/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
Route::get('admin/users/{id}', [UsersController::class, 'show'])->name('users.show');
Route::post('admin/users', [UsersController::class, 'store'])->name('users.store');

Route::resource('activity-logs', JournauxController::class);
Route::resource('app-versions', VersionsController::class);

Route::resource('articles', ConseilsController::class);
Route::post('admin/articles', [ConseilsController::class, 'store'])->name('articles.store');
Route::get('admin/articles/{id}', [ConseilsController::class, 'show'])->name('articles.show');
Route::put('admin/articles/{id}', [ConseilsController::class, 'update'])->name('articles.update');
Route::delete('admin/articles/{id}', [ConseilsController::class, 'destroy'])->name('articles.destroy');
Route::post('admin/articles/{id}/publish', [ConseilsController::class, 'togglePublish'])->name('articles.publish');

Route::resource('banners', BannieresController::class);

// Actions custom utilisées dans ton JS
Route::post('admin/stations', [StationsController::class, 'store'])->name('stations.store');
Route::post('admin/stations/{id}/prices', [StationsController::class, 'updatePrices'])->name('stations.prices');
Route::post('admin/stations/{id}/verify', [StationsController::class, 'verify'])->name('stations.verify');
Route::post('admin/stations/{id}/toggle', [StationsController::class, 'toggle'])->name('stations.toggle');
Route::delete('admin/stations/{id}', [StationsController::class, 'destroy'])->name('stations.delete');
Route::get('admin/stations/{id}', [StationsController::class, 'show'])->name('stations.show');
Route::resource('stations', StationsController::class);
Route::get('admin/stations/export', [StationsController::class, 'export'])->name('stations.export');

Route::resource('garages', GaragesController::class);
Route::get('admin/garages/export', [GaragesController::class, 'export'])->name('garages.export');
Route::get('admin/garages/{id}', [GaragesController::class, 'show'])->name('garages.show');
Route::post('admin/garages/', [GaragesController::class, 'store'])->name('garages.store');
Route::post('admin/garages/{id}/services', [GaragesController::class, 'updateServices'])->name('garages.services');
Route::post('admin/garages/{id}/verify', [GaragesController::class, 'verify'])->name('garages.verify');
Route::post('admin/garages/{id}/toggle', [GaragesController::class, 'toggle'])->name('garages.toggle');
Route::delete('admin/garages/{id}', [GaragesController::class, 'destroy'])->name('garages.delete');

Route::resource('notifications', NotificationsController::class);

Route::resource('partner-requests', DemandesController::class);
Route::post('admin/partner-requests/{id}/approve', [DemandesController::class, 'approve'])->name('partner-requests.approve');
Route::post('admin/partner-requests/{id}/contact', [DemandesController::class, 'contact'])->name('partner-requests.contact');
Route::post('admin/partner-requests/{id}/reject', [DemandesController::class, 'reject'])->name('partner-requests.reject');
Route::delete('admin/partner-requests/{id}', [DemandesController::class, 'destroy'])->name('partner-requests.delete');


Route::resource('payments', PaiementsController::class);

Route::resource('promotions', PromotionsController::class);
Route::post('admin/promotions', [PromotionsController::class, 'store'])->name('promotions.store');
Route::get('admin/promotions/{id}', [PromotionsController::class, 'show'])->name('promotions.show');
Route::put('admin/promotions/{id}', [PromotionsController::class, 'update'])->name('promotions.update');
Route::delete('admin/promotions/{id}', [PromotionsController::class, 'destroy'])->name('promotions.destroy');
Route::post('admin/promotions/{id}/toggle', [PromotionsController::class, 'toggle'])->name('promotions.toggle');
Route::post('admin/promotions/{id}/send-push', [PromotionsController::class, 'sendPush'])->name('promotions.sendPush');
Route::post('admin/promotions/{id}/duplicate', [PromotionsController::class, 'duplicate'])->name('promotions.duplicate');

Route::resource('reviews', AvisController::class);
Route::resource('settings', ParametresController::class);
Route::resource('subscriptions', AbonnementsController::class);
