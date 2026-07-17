<?php

use App\Http\Controllers\AbonnementsController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\BannieresController;
use App\Http\Controllers\ConseilsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandesController;
use App\Http\Controllers\GarageOwnersController;
use App\Http\Controllers\GaragesController;
use App\Http\Controllers\GarageServicesController;
use App\Http\Controllers\GarageTeamAssignmentsController;
use App\Http\Controllers\JournauxController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\PaymentWaveController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\StationOwnersController;
use App\Http\Controllers\StationsController;
use App\Http\Controllers\StationServicesController;
use App\Http\Controllers\TeamAssignmentsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VersionsController;
use Illuminate\Support\Facades\Route;

// Wave abonnement (success / error)
Route::get('/payment/wave/success/{id}', [PaymentWaveController::class, 'success'])
    ->name('wave.success');
Route::get('/payment/wave/error/{id}', [PaymentWaveController::class, 'error'])
    ->name('wave.error');
Route::get('/pro/wave/success/{id}', [PaymentWaveController::class, 'successpro'])
    ->name('wave.successpro');
Route::get('/pro/wave/error/{id}', [PaymentWaveController::class, 'errorpro'])
    ->name('wave.errorpro');

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

Route::resource('activity-logs', JournauxController::class);Route::resource('activity-logs', JournauxController::class)->only(['index', 'show']);
Route::get('/activity-logs/export', [JournauxController::class, 'export'])->name('activity-logs.export');

Route::resource('app-versions', VersionsController::class)->only(['index', 'show', 'store']);
// Config par plateforme (toggle force update, store URL)
Route::patch('/app-versions/{platform}/config', [VersionsController::class, 'saveConfig'])
    ->name('app-versions.config')
    ->where('platform', 'android|ios');

Route::resource('articles', ConseilsController::class);
Route::post('admin/articles', [ConseilsController::class, 'store'])->name('articles.store');
Route::get('admin/articles/{id}', [ConseilsController::class, 'show'])->name('articles.show');
Route::put('admin/articles/{id}', [ConseilsController::class, 'update'])->name('articles.update');
Route::delete('admin/articles/{id}', [ConseilsController::class, 'destroy'])->name('articles.destroy');
Route::post('admin/articles/{id}/publish', [ConseilsController::class, 'togglePublish'])->name('articles.publish');

Route::resource('banners', BannieresController::class);

// Actions custom utilisées dans ton JS
Route::resource('stations', StationsController::class);
Route::post('admin/stations', [StationsController::class, 'store'])->name('stations.store');
Route::get('admin/stations/export', [StationsController::class, 'export'])->name('stations.export');
Route::get('admin/stations/{station}', [StationsController::class, 'show'])->name('stations.show');
Route::post('admin/stations/{station}/prices', [StationsController::class, 'updatePrices'])->name('stations.prices');
Route::post('admin/stations/{station}/services', [StationsController::class, 'updateServices'])->name('stations.services');
Route::post('admin/stations/{station}/team', [StationsController::class, 'addTeamMember'])->name('stations.team.add');
Route::delete('admin/stations/{station}/team/{member}', [StationsController::class, 'removeTeamMember'])->name('stations.team.remove');
Route::post('admin/stations/{station}/verify', [StationsController::class, 'verify'])->name('stations.verify');
Route::post('admin/stations/{station}/toggle', [StationsController::class, 'toggle'])->name('stations.toggle');
Route::delete('admin/stations/{station}', [StationsController::class, 'destroy'])->name('stations.delete');

Route::get('team-assignments', [TeamAssignmentsController::class, 'index'])->name('team-assignments.index');
Route::post('admin/team-assignments', [TeamAssignmentsController::class, 'store'])->name('team-assignments.store');
Route::get('admin/team-assignments/{assignment}', [TeamAssignmentsController::class, 'show'])->name('team-assignments.show');
Route::put('admin/team-assignments/{assignment}', [TeamAssignmentsController::class, 'update'])->name('team-assignments.update');
Route::delete('admin/team-assignments/{assignment}', [TeamAssignmentsController::class, 'destroy'])->name('team-assignments.delete');

Route::get('station-services', [StationServicesController::class, 'index'])->name('station-services.index');
Route::post('admin/station-services', [StationServicesController::class, 'store'])->name('station-services.store');
Route::get('admin/station-services/{stationService}', [StationServicesController::class, 'show'])->name('station-services.show');
Route::put('admin/station-services/{stationService}', [StationServicesController::class, 'update'])->name('station-services.update');
Route::delete('admin/station-services/{stationService}', [StationServicesController::class, 'destroy'])->name('station-services.delete');

Route::get('owners', [StationOwnersController::class, 'index'])->name('owners.index');
Route::post('admin/owners', [StationOwnersController::class, 'store'])->name('owners.store');
Route::get('admin/owners/{owner}', [StationOwnersController::class, 'show'])->name('owners.show');
Route::put('admin/owners/{owner}', [StationOwnersController::class, 'update'])->name('owners.update');
Route::post('admin/owners/{owner}/approve', [StationOwnersController::class, 'approve'])->name('owners.approve');
Route::post('admin/owners/{owner}/suspend', [StationOwnersController::class, 'suspend'])->name('owners.suspend');
Route::delete('admin/owners/{owner}', [StationOwnersController::class, 'destroy'])->name('owners.delete');


Route::resource('garages', GaragesController::class);
Route::get('admin/garages/export', [GaragesController::class, 'export'])->name('garages.export');
Route::get('admin/garages/{id}', [GaragesController::class, 'show'])->name('garages.show');
Route::post('admin/garages/', [GaragesController::class, 'store'])->name('garages.store');
Route::post('admin/garages/{id}/services', [GaragesController::class, 'updateServices'])->name('garages.services');
Route::post('admin/garages/{id}/verify', [GaragesController::class, 'verify'])->name('garages.verify');
Route::post('admin/garages/{id}/toggle', [GaragesController::class, 'toggle'])->name('garages.toggle');
Route::delete('admin/garages/{id}', [GaragesController::class, 'destroy'])->name('garages.delete');

Route::get('garage-owners', [GarageOwnersController::class, 'index'])->name('garage-owners.index');
Route::post('admin/garage-owners', [GarageOwnersController::class, 'store'])->name('garage-owners.store');
Route::get('admin/garage-owners/{garageOwner}', [GarageOwnersController::class, 'show'])->name('garage-owners.show');
Route::put('admin/garage-owners/{garageOwner}', [GarageOwnersController::class, 'update'])->name('garage-owners.update');
Route::post('admin/garage-owners/{garageOwner}/approve', [GarageOwnersController::class, 'approve'])->name('garage-owners.approve');
Route::post('admin/garage-owners/{garageOwner}/suspend', [GarageOwnersController::class, 'suspend'])->name('garage-owners.suspend');
Route::delete('admin/garage-owners/{garageOwner}', [GarageOwnersController::class, 'destroy'])->name('garage-owners.delete');

/*
|--------------------------------------------------------------------------
| Affectations équipe garages (garage_owner_garage)
|--------------------------------------------------------------------------
*/
Route::get('garage-team-assignments', [GarageTeamAssignmentsController::class, 'index'])->name('garage-team-assignments.index');
Route::post('admin/garage-team-assignments', [GarageTeamAssignmentsController::class, 'store'])->name('garage-team-assignments.store');
Route::get('admin/garage-team-assignments/{assignment}', [GarageTeamAssignmentsController::class, 'show'])->name('garage-team-assignments.show');
Route::put('admin/garage-team-assignments/{assignment}', [GarageTeamAssignmentsController::class, 'update'])->name('garage-team-assignments.update');
Route::delete('admin/garage-team-assignments/{assignment}', [GarageTeamAssignmentsController::class, 'destroy'])->name('garage-team-assignments.delete');

/*
|--------------------------------------------------------------------------
| Services garages (garage_services)
|--------------------------------------------------------------------------
*/
Route::get('garage-services', [GarageServicesController::class, 'index'])->name('garage-services.index');
Route::post('admin/garage-services', [GarageServicesController::class, 'store'])->name('garage-services.store');
Route::get('admin/garage-services/{garageService}', [GarageServicesController::class, 'show'])->name('garage-services.show');
Route::put('admin/garage-services/{garageService}', [GarageServicesController::class, 'update'])->name('garage-services.update');
Route::delete('admin/garage-services/{garageService}', [GarageServicesController::class, 'destroy'])->name('garage-services.delete');


Route::resource('notifications', NotificationsController::class);

Route::resource('partner-requests', DemandesController::class);
Route::post('admin/partner-requests/{id}/approve', [DemandesController::class, 'approve'])->name('partner-requests.approve');
Route::post('admin/partner-requests/{id}/contact', [DemandesController::class, 'contact'])->name('partner-requests.contact');
Route::post('admin/partner-requests/{id}/reject', [DemandesController::class, 'reject'])->name('partner-requests.reject');
Route::delete('admin/partner-requests/{id}', [DemandesController::class, 'destroy'])->name('partner-requests.delete');


Route::resource('payments', PaiementsController::class)->only(['index', 'show']);
// Actions custom
Route::post('/payments/{id}/refund', [PaiementsController::class, 'refund'])->name('payments.refund');
Route::post('/payments/{id}/retry',  [PaiementsController::class, 'retry'])->name('payments.retry');
Route::post('/payments/{id}/cancel', [PaiementsController::class, 'cancel'])->name('payments.cancel');
Route::get('/payments/export',       [PaiementsController::class, 'export'])->name('payments.export');

Route::resource('promotions', PromotionsController::class);
Route::post('admin/promotions', [PromotionsController::class, 'store'])->name('promotions.store');
Route::get('admin/promotions/{id}', [PromotionsController::class, 'show'])->name('promotions.show');
Route::put('admin/promotions/{id}', [PromotionsController::class, 'update'])->name('promotions.update');
Route::delete('admin/promotions/{id}', [PromotionsController::class, 'destroy'])->name('promotions.destroy');
Route::post('admin/promotions/{id}/toggle', [PromotionsController::class, 'toggle'])->name('promotions.toggle');
Route::post('admin/promotions/{id}/send-push', [PromotionsController::class, 'sendPush'])->name('promotions.sendPush');
Route::post('admin/promotions/{id}/duplicate', [PromotionsController::class, 'duplicate'])->name('promotions.duplicate');

Route::get('/reviews', [AvisController::class, 'index'])->name('reviews.index');
Route::post('/reviews/{id}/approve', [AvisController::class, 'approve'])->name('reviews.approve');
Route::post('/reviews/approve-all', [AvisController::class, 'approveAll'])->name('reviews.approve-all');
Route::delete('/reviews/{id}', [AvisController::class, 'destroy'])->name('reviews.destroy');

Route::resource('settings', ParametresController::class)->only(['index']);
// Sauvegarde par groupe (tarification, general, notifications, security, payments, integrations, quotas)
Route::post('/settings/{group}', [ParametresController::class, 'saveGroup'])
    ->name('settings.group')
    ->where('group', '[a-z_]+');

Route::resource('subscriptions', AbonnementsController::class);
// Actions custom (hors CRUD standard)
Route::post('/subscriptions/{id}/extend', [AbonnementsController::class, 'extend'])->name('subscriptions.extend');
Route::post('/subscriptions/{id}/cancel', [AbonnementsController::class, 'cancel'])->name('subscriptions.cancel');
