<?php

use App\Http\Controllers\Api\Admin\AdminArticleController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminGarageController;
use App\Http\Controllers\Api\Admin\AdminNotificationController;
use App\Http\Controllers\Api\Admin\AdminPartnerRequestController;
use App\Http\Controllers\Api\Admin\AdminReviewController;
use App\Http\Controllers\Api\Admin\AdminStationController;
use App\Http\Controllers\Api\Admin\AdminSubscriptionController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

// Controllers Auth
use App\Http\Controllers\Api\Auth\ApiAuthController;
// Controllers User
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\VehicleController;
use App\Http\Controllers\Api\User\DocumentController;
use App\Http\Controllers\Api\User\MaintenanceLogController;
use App\Http\Controllers\Api\User\FuelLogController;
use App\Http\Controllers\Api\User\ReminderController;
use App\Http\Controllers\Api\User\NotificationController;
use App\Http\Controllers\Api\User\FavoriteController;
use App\Http\Controllers\Api\User\SubscriptionUserController;

// Controllers Map / Geo
use App\Http\Controllers\Api\Map\StationController;
use App\Http\Controllers\Api\Map\GarageController;
use App\Http\Controllers\Api\Map\ReviewController;

// Controllers Content
use App\Http\Controllers\Api\Content\ArticleController;
use App\Http\Controllers\Api\Content\AppVersionController;

// Controllers Pro (stations & garages owners)
use App\Http\Controllers\Api\Pro\ProAuthController;
use App\Http\Controllers\Api\Pro\ProStationController;
use App\Http\Controllers\Api\Pro\ProGarageController;
use App\Http\Controllers\Api\Pro\ProFuelPriceController;
use App\Http\Controllers\Api\Pro\ProPromotionController;
use App\Http\Controllers\Api\Pro\ProStatsController;
use App\Http\Controllers\Api\Pro\ProSubscriptionController;
use App\Http\Controllers\Api\Pub\BannerController;
use App\Http\Controllers\Api\Pub\PromotionController;
use App\Http\Controllers\Api\Webhook\CinetPayController;
use App\Http\Controllers\Api\Webhook\MtnMomoController;
use App\Http\Controllers\Api\Webhook\OrangeMoneyController;
use App\Http\Controllers\Api\Webhook\WaveController;

/*
|--------------------------------------------------------------------------
| API Routes — GoCarbu
| Base URL : /api/v1/gocarbu
| Auth      : Laravel Sanctum (Bearer token)
| Version   : 1.0
|--------------------------------------------------------------------------
*/

Route::prefix('v1/gocarbu/')->group(function () {

    /*
    |------------------------------------------------------------------
    | BLOC 1 — AUTHENTIFICATION UTILISATEUR (public)
    |------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {

        // ── Routes publiques ───────────────────────────────────────────────

        // Inscription classique (name + phone + password)
        Route::post('register', [ApiAuthController::class, 'register']);

        // Connexion classique (email ou phone + password)
        Route::post('login', [ApiAuthController::class, 'login']);

        // Envoyer un code OTP par email ou WhatsApp
        Route::post('request-otp', [ApiAuthController::class, 'requestOtp']);

        // Vérifier le code OTP → retourne un token Sanctum
        Route::post('verify-otp', [ApiAuthController::class, 'verifyOtp']);

        // ── Routes protégées (token Sanctum requis) ────────────────────────
        Route::middleware('auth:sanctum')->group(function () {

            // Rafraîchir le token (rotation)
            Route::post('refresh-token', [ApiAuthController::class, 'refreshToken']);

            // Déconnexion (révoque le token courant)
            Route::post('logout', [ApiAuthController::class, 'logout']);

            // Déconnexion de tous les appareils
            Route::post('logout-all', [ApiAuthController::class, 'logoutAll']);
        });
    });

    /*
    |------------------------------------------------------------------
    | BLOC 2 — ROUTES PUBLIQUES (sans authentification)
    |------------------------------------------------------------------
    */

    // ── Stations (lecture publique) ──────────────────────────────────
    Route::prefix('stations')->group(function () {

        // Liste paginée avec filtres
        // GET /stations?lat=5.3&lng=-4.0&radius=5&fuel_type=essence&city=Abidjan&verified=1&sort=distance
        Route::get('/', [StationController::class, 'index']);

        // Stations les plus proches (géoloc)
        // GET /stations/nearby?lat=&lng=&radius=5
        Route::get('nearby', [StationController::class, 'nearby']);

        // Stations avec les prix les moins chers
        // GET /stations/cheapest?fuel_type=essence&lat=&lng=&radius=10
        Route::get('cheapest', [StationController::class, 'cheapest']);

        // Stations vérifiées uniquement
        Route::get('verified', [StationController::class, 'verified']);

        // Inscription partenaire
        Route::post('register', [StationController::class, 'register']);

        // Détail d'une station
        Route::get('{id}', [StationController::class, 'show']);

        // Prix carburant d'une station
        Route::get('{id}/prices', [StationController::class, 'prices']);

        // Promotions actives d'une station
        Route::get('{id}/promotions', [StationController::class, 'promotions']);

        // Avis d'une station
        Route::get('{id}/reviews', [StationController::class, 'reviews']);

        // Services disponibles
        Route::get('{id}/services', [StationController::class, 'services']);
    });

    // ── Garages (lecture publique) ───────────────────────────────────
    Route::prefix('garages')->group(function () {

        // Liste paginée avec filtres
        // GET /garages?lat=&lng=&radius=&type=vidange&city=
        Route::get('/', [GarageController::class, 'index']);

        // Garages les plus proches
        Route::get('nearby', [GarageController::class, 'nearby']);

        // Par type de service
        // GET /garages/by-type?type=depannage
        Route::get('by-type', [GarageController::class, 'byType']);

        // Dépanneurs disponibles (urgence)
        Route::get('emergency', [GarageController::class, 'emergency']);

        // Inscription partenaire
        Route::post('register', [GarageController::class, 'register']);

        // Détail d'un garage
        Route::get('{id}', [GarageController::class, 'show']);

        // Services d'un garage
        Route::get('{id}/services', [GarageController::class, 'services']);

        // Promotions actives d'un garage
        Route::get('{id}/promotions', [GarageController::class, 'promotions']);

        // Avis d'un garage
        Route::get('{id}/reviews', [GarageController::class, 'reviews']);
    });

    // ── Articles & Conseils (lecture publique) ───────────────────────
    Route::prefix('articles')->group(function () {

        // Liste paginée
        // GET /articles?category=entretien_auto&page=1&limit=10&sponsored=1
        Route::get('/', [ArticleController::class, 'index']);

        // Articles à la une / sponsorisés
        Route::get('featured', [ArticleController::class, 'featured']);

        // Articles les plus consultés
        Route::get('trending', [ArticleController::class, 'trending']);

        // Liste des catégories disponibles
        Route::get('categories', [ArticleController::class, 'categories']);

        // Détail d'un article (par id ou slug)
        Route::get('{idOrSlug}', [ArticleController::class, 'show']);

        // Articles similaires (même catégorie)
        Route::get('{id}/related', [ArticleController::class, 'related']);
    });

    // ── Promotions globales ──────────────────────────────────────────
    Route::prefix('promotions')->group(function () {

        // Toutes les promos actives autour de moi
        // GET /promotions?lat=&lng=&radius=5
        Route::get('/', [PromotionController::class, 'index']);

        // Promos d'une station ou garage (polymorphique)
        Route::get('{id}', [PromotionController::class, 'show']);
    });

    // ── Bannières publicitaires ──────────────────────────────────────
    Route::prefix('banners')->group(function () {

        // GET /banners?position=home_top&city=Abidjan
        Route::get('/', [BannerController::class, 'index']);

        // Enregistrer une impression
        Route::post('{id}/impression', [BannerController::class, 'impression']);

        // Enregistrer un clic
        Route::post('{id}/click', [BannerController::class, 'click']);
    });

    // ── Versions de l'application ────────────────────────────────────
    Route::prefix('app')->group(function () {

        // Vérifier si une mise à jour est disponible
        // GET /app/version?platform=android&current_version=1.0.0
        Route::get('version', [AppVersionController::class, 'check']);

        // Configuration publique de l'app (prix plans, etc.)
        Route::get('settings', [AppVersionController::class, 'publicSettings']);
    });

    /*
    |------------------------------------------------------------------
    | BLOC 3 — ROUTES UTILISATEUR AUTHENTIFIÉ
    |------------------------------------------------------------------
    */
    Route::prefix('connecte')->group(function () {

        // ── Profil utilisateur ───────────────────────────────────────
        Route::prefix('user')->group(function () {

            // Profil complet
            Route::get('profile', [UserController::class, 'profile']);

            // Modifier le profil (nom, ville, avatar)
            Route::put('profile', [UserController::class, 'update']);

            // Uploader un avatar
            Route::post('profile/avatar', [UserController::class, 'updateAvatar']);

            // Enregistrer le token FCM Firebase pour les push
            Route::post('fcm-token', [UserController::class, 'updateFcmToken']);

            // Supprimer son compte
            Route::delete('account', [UserController::class, 'deleteAccount']);

            // Abonnement premium de l'utilisateur
            Route::get('subscription', [UserController::class, 'subscription']);
        });

        // ── Véhicules ────────────────────────────────────────────────
        Route::prefix('vehicles')->group(function () {

            // Lister mes véhicules
            Route::get('/', [VehicleController::class, 'index']);

            // Ajouter un véhicule
            Route::post('/', [VehicleController::class, 'store']);

            // Détail d'un véhicule
            Route::get('{id}', [VehicleController::class, 'show']);

            // Modifier un véhicule
            Route::put('{id}', [VehicleController::class, 'update']);

            // Supprimer un véhicule
            Route::delete('{id}', [VehicleController::class, 'destroy']);

            // Définir comme véhicule principal
            Route::patch('{id}/set-primary', [VehicleController::class, 'setPrimary']);

            // ── Documents d'un véhicule ──────────────────────────────
            Route::prefix('{vehicleId}/documents')->group(function () {

                // Lister les documents
                Route::get('/', [DocumentController::class, 'index']);

                // Ajouter un document (avec upload photo/scan)
                Route::post('/', [DocumentController::class, 'store']);

                // Détail d'un document
                Route::get('{id}', [DocumentController::class, 'show']);

                // Modifier un document
                Route::put('{id}', [DocumentController::class, 'update']);

                // Supprimer un document
                Route::delete('{id}', [DocumentController::class, 'destroy']);

                // Upload/remplacer la photo du document
                Route::post('{id}/upload', [DocumentController::class, 'uploadFile']);
            });

            // ── Carnet d'entretien d'un véhicule ────────────────────
            Route::prefix('{vehicleId}/maintenance')->group(function () {

                // Historique d'entretien
                Route::get('/', [MaintenanceLogController::class, 'index']);

                // Ajouter une entrée
                Route::post('/', [MaintenanceLogController::class, 'store']);

                // Détail d'une entrée
                Route::get('{id}', [MaintenanceLogController::class, 'show']);

                // Modifier une entrée
                Route::put('{id}', [MaintenanceLogController::class, 'update']);

                // Supprimer une entrée
                Route::delete('{id}', [MaintenanceLogController::class, 'destroy']);
            });

            // ── Suivi carburant d'un véhicule ────────────────────────
            Route::prefix('{vehicleId}/fuel-logs')->group(function () {

                // Historique des pleins
                Route::get('/', [FuelLogController::class, 'index']);

                // Enregistrer un plein
                Route::post('/', [FuelLogController::class, 'store']);

                // Statistiques mensuelles (Premium uniquement)
                Route::get('stats', [FuelLogController::class, 'stats']);

                // Détail d'un plein
                Route::get('{id}', [FuelLogController::class, 'show']);

                // Modifier un enregistrement
                Route::put('{id}', [FuelLogController::class, 'update']);

                // Supprimer un enregistrement
                Route::delete('{id}', [FuelLogController::class, 'destroy']);
            });
        });

        // ── Rappels ──────────────────────────────────────────────────
        Route::prefix('reminders')->group(function () {

            // Liste des rappels actifs
            Route::get('/', [ReminderController::class, 'index']);

            // Rappels à venir (pour le dashboard)
            Route::get('upcoming', [ReminderController::class, 'upcoming']);

            // Créer un rappel
            Route::post('/', [ReminderController::class, 'store']);

            // Détail d'un rappel
            Route::get('{id}', [ReminderController::class, 'show']);

            // Modifier un rappel
            Route::put('{id}', [ReminderController::class, 'update']);

            // Supprimer un rappel
            Route::delete('{id}', [ReminderController::class, 'destroy']);

            // Ignorer / dismiss un rappel
            Route::patch('{id}/dismiss', [ReminderController::class, 'dismiss']);
        });

        // ── Notifications ────────────────────────────────────────────
        Route::prefix('notifications')->group(function () {

            // Historique des notifications
            Route::get('/', [NotificationController::class, 'index']);

            // Nombre de non-lues
            Route::get('unread-count', [NotificationController::class, 'unreadCount']);

            // Marquer une notification comme lue
            Route::patch('{id}/read', [NotificationController::class, 'markAsRead']);

            // Tout marquer comme lu
            Route::patch('read-all', [NotificationController::class, 'markAllAsRead']);

            // Supprimer une notification
            Route::delete('{id}', [NotificationController::class, 'destroy']);

            // Supprimer toutes les notifications
            Route::delete('/', [NotificationController::class, 'destroyAll']);
        });

        // ── Favoris ──────────────────────────────────────────────────
        Route::prefix('favorites')->group(function () {

            // Liste des favoris (stations + garages)
            Route::get('/', [FavoriteController::class, 'index']);

            // Ajouter en favori
            // POST /favorites {type: "station", id: 5}
            Route::post('/', [FavoriteController::class, 'store']);

            // Retirer un favori
            Route::delete('{type}/{id}', [FavoriteController::class, 'destroy']);

            // Vérifier si un lieu est en favori
            Route::get('check/{type}/{id}', [FavoriteController::class, 'check']);
        });

        // ── Avis & Notes (authentifié) ───────────────────────────────
        Route::prefix('reviews')->group(function () {

            // Déposer un avis sur une station ou garage
            // POST /reviews {type: "station", id: 5, rating: 4, comment: "..."}
            Route::post('/', [ReviewController::class, 'store']);

            // Modifier mon avis
            Route::put('{id}', [ReviewController::class, 'update']);

            // Supprimer mon avis
            Route::delete('{id}', [ReviewController::class, 'destroy']);

            // Mes avis
            Route::get('my', [ReviewController::class, 'myReviews']);
        });

        // ── Abonnement Premium utilisateur ───────────────────────────
        Route::prefix('subscription')->group(function () {

            // Plans disponibles
            Route::get('plans', [SubscriptionUserController::class, 'plans']);

            // Initier un paiement (Orange Money, MTN, Wave...)
            Route::post('initiate', [SubscriptionUserController::class, 'initiate']);

            // Vérifier le statut d'un paiement
            Route::get('status/{reference}', [SubscriptionUserController::class, 'status']);

            // Annuler l'abonnement
            Route::post('cancel', [SubscriptionUserController::class, 'cancel']);

            // Historique des paiements
            Route::get('history', [SubscriptionUserController::class, 'history']);
        });
    });

    /*
    |------------------------------------------------------------------
    | BLOC 4 — ROUTES ESPACE PRO (Station & Garage owners)
    |------------------------------------------------------------------
    */
    Route::prefix('pro')->group(function () {

        // Auth Pro (compte séparé des users)
        Route::prefix('auth')->group(function () {
            Route::post('login', [ProAuthController::class, 'login']);
            Route::post('forgot-password', [ProAuthController::class, 'forgotPassword']);
            Route::post('reset-password', [ProAuthController::class, 'resetPassword']);
            Route::post('logout', [ProAuthController::class, 'logout']);
        });

        // Toutes les routes pro nécessitent auth + abonnement actif
        Route::prefix('subscription')->group(function () {

            // ── Profil Pro ───────────────────────────────────────────
            Route::get('profile', [ProAuthController::class, 'profile']);
            Route::put('profile', [ProAuthController::class, 'updateProfile']);
            Route::post('profile/logo', [ProAuthController::class, 'updateLogo']);

            // ── Gestion Station ──────────────────────────────────────
            Route::prefix('stations')->group(function () {

                // Mes stations
                Route::get('/', [ProStationController::class, 'index']);

                // Détail d'une station (ma station)
                Route::get('{id}', [ProStationController::class, 'show']);

                // Modifier les infos (nom, adresse, horaires, description)
                Route::put('{id}', [ProStationController::class, 'update']);

                // Uploader des photos (galerie)
                Route::post('{id}/photos', [ProStationController::class, 'uploadPhotos']);

                // Supprimer une photo
                Route::delete('{id}/photos/{photoIndex}', [ProStationController::class, 'deletePhoto']);

                // Modifier les services disponibles
                Route::put('{id}/services', [ProStationController::class, 'updateServices']);

                // Modifier les horaires
                Route::put('{id}/hours', [ProStationController::class, 'updateHours']);
            });

            // ── Prix carburant ───────────────────────────────────────
            Route::prefix('stations/{stationId}/prices')->group(function () {

                // Voir les prix actuels
                Route::get('/', [ProFuelPriceController::class, 'index']);

                // Mettre à jour un prix (plan Pro/Premium uniquement)
                Route::put('{fuelType}', [ProFuelPriceController::class, 'update']);

                // Mettre à jour tous les prix en une fois
                Route::put('/', [ProFuelPriceController::class, 'updateAll']);

                // Historique des modifications de prix (plan Pro/Premium uniquement)
                Route::get('history', [ProFuelPriceController::class, 'history']);
            });

            // ── Promotions Station ───────────────────────────────────
            Route::prefix('stations/{stationId}/promotions')->group(function () {

                // Liste des promotions
                Route::get('/', [ProPromotionController::class, 'indexStation']);

                // Créer une promo (plan Pro/Premium)
                Route::post('/', [ProPromotionController::class, 'storeStation']);

                // Modifier une promo (plan Pro/Premium)
                Route::put('{id}', [ProPromotionController::class, 'updateStation']);

                // Activer / désactiver (plan Pro/Premium)
                Route::patch('{id}/toggle', [ProPromotionController::class, 'toggle']);

                // Supprimer (plan Pro/Premium)
                Route::delete('{id}', [ProPromotionController::class, 'destroyStation']);
            });

            // ── Gestion Garage ───────────────────────────────────────
            Route::prefix('garages')->group(function () {

                // Mes garages
                Route::get('/', [ProGarageController::class, 'index']);

                // Détail d'un garage
                Route::get('{id}', [ProGarageController::class, 'show']);

                // Modifier les infos
                Route::put('{id}', [ProGarageController::class, 'update']);

                // Uploader des photos
                Route::post('{id}/photos', [ProGarageController::class, 'uploadPhotos']);

                // Supprimer une photo
                Route::delete('{id}/photos/{photoIndex}', [ProGarageController::class, 'deletePhoto']);

                // Modifier les services
                Route::put('{id}/services', [ProGarageController::class, 'updateServices']);

                // Modifier les horaires
                Route::put('{id}/hours', [ProGarageController::class, 'updateHours']);
            });

            // ── Promotions Garage ────────────────────────────────────
            Route::prefix('garages/{garageId}/promotions')->group(function () {

                Route::get('/', [ProPromotionController::class, 'indexGarage']);

                Route::post('/', [ProPromotionController::class, 'storeGarage']);

                Route::put('{id}', [ProPromotionController::class, 'updateGarage']);

                Route::patch('{id}/toggle', [ProPromotionController::class, 'toggle']);

                Route::delete('{id}', [ProPromotionController::class, 'destroyGarage']);
            });

            // ── Statistiques Pro ─────────────────────────────────────
            Route::prefix('stats')->group(function () {

                // Vue d'ensemble (vues, clics, appels ce mois)
                Route::get('overview', [ProStatsController::class, 'overview']);

                // Stats d'une station spécifique
                Route::get('stations/{id}', [ProStatsController::class, 'station']);

                // Stats d'un garage spécifique
                Route::get('garages/{id}', [ProStatsController::class, 'garage']);

                // Statistiques avancées (vues par jour/semaine)
                Route::get('advanced', [ProStatsController::class, 'advanced']);

                // Avis reçus
                Route::get('reviews', [ProStatsController::class, 'reviews']);
            });

            // ── Abonnement Pro ───────────────────────────────────────
            Route::prefix('subscription')->group(function () {

                // Plans professionnels disponibles
                Route::get('plans', [ProSubscriptionController::class, 'plans']);

                // Abonnement actif
                Route::get('current', [ProSubscriptionController::class, 'current']);

                // Initier un paiement abonnement
                Route::post('initiate', [ProSubscriptionController::class, 'initiate']);

                // Vérifier le statut du paiement
                Route::get('status/{reference}', [ProSubscriptionController::class, 'status']);

                // Historique des paiements
                Route::get('history', [ProSubscriptionController::class, 'history']);

                // Annuler l'abonnement
                Route::post('cancel', [ProSubscriptionController::class, 'cancel']);
            });
        });
    });

    /*
    |------------------------------------------------------------------
    | BLOC 5 — WEBHOOKS PAIEMENT (sans auth, signature HMAC)
    |------------------------------------------------------------------
    */
    Route::prefix('webhooks')->group(function () {

        // Webhook CinetPay
        Route::post('cinetpay', [CinetPayController::class, 'handle']);

        // Webhook Orange Money
        Route::post('orange-money', [OrangeMoneyController::class, 'handle']);

        // Webhook MTN MoMo
        Route::post('mtn-momo', [MtnMomoController::class, 'handle']);

        // Webhook Wave
        Route::post('wave', [WaveController::class, 'handle']);
    });

    /*
    |------------------------------------------------------------------
    | BLOC 6 — ADMINISTRATION (admin dashboard)
    |------------------------------------------------------------------
    */
    // Route::prefix('admin')->group(function () {

    //     // Auth admin
    //     Route::post('auth/login', [AdminAuthController::class, 'login']);
    //     Route::post('auth/logout', [AdminAuthController::class, 'logout'])
    //         ->middleware('auth:admin');

    //     Route::middleware(['auth:admin'])->group(function () {

    //         // ── Dashboard ────────────────────────────────────────────
    //         Route::prefix('dashboard')->group(function () {

    //             // Métriques globales
    //             Route::get('overview', [AdminDashboardController::class, 'overview']);

    //             // Revenus par période
    //             Route::get('revenue', [AdminDashboardController::class, 'revenue']);

    //             // Croissance utilisateurs
    //             Route::get('growth', [AdminDashboardController::class, 'growth']);

    //             // Activité temps réel
    //             Route::get('activity', [AdminDashboardController::class, 'activity']);
    //         });

    //         // ── Gestion Utilisateurs ─────────────────────────────────
    //         Route::prefix('users')->group(function () {

    //             // Liste avec filtres et pagination
    //             Route::get('/', [AdminUserController::class, 'index']);

    //             // Détail d'un utilisateur
    //             Route::get('{id}', [AdminUserController::class, 'show']);

    //             // Modifier un utilisateur
    //             Route::put('{id}', [AdminUserController::class, 'update']);

    //             // Suspendre / réactiver
    //             Route::patch('{id}/toggle-active', [AdminUserController::class, 'toggleActive']);

    //             // Attribuer premium manuellement
    //             Route::post('{id}/grant-premium', [AdminUserController::class, 'grantPremium']);

    //             // Supprimer le compte
    //             Route::delete('{id}', [AdminUserController::class, 'destroy']);

    //             // Export CSV
    //             Route::get('export/csv', [AdminUserController::class, 'exportCsv']);
    //         });

    //         // ── Gestion Stations ─────────────────────────────────────
    //         Route::prefix('stations')->group(function () {

    //             // Liste
    //             Route::get('/', [AdminStationController::class, 'index']);

    //             // Créer une station (admin)
    //             Route::post('/', [AdminStationController::class, 'store']);

    //             // Détail
    //             Route::get('{id}', [AdminStationController::class, 'show']);

    //             // Modifier
    //             Route::put('{id}', [AdminStationController::class, 'update']);

    //             // Vérifier (badge vérifié)
    //             Route::patch('{id}/verify', [AdminStationController::class, 'verify']);

    //             // Retirer la vérification
    //             Route::patch('{id}/unverify', [AdminStationController::class, 'unverify']);

    //             // Activer / désactiver
    //             Route::patch('{id}/toggle-active', [AdminStationController::class, 'toggleActive']);

    //             // Forcer les prix carburant
    //             Route::put('{id}/prices', [AdminStationController::class, 'updatePrices']);

    //             // Supprimer
    //             Route::delete('{id}', [AdminStationController::class, 'destroy']);
    //         });

    //         // ── Gestion Garages ──────────────────────────────────────
    //         Route::prefix('garages')->group(function () {

    //             Route::get('/', [AdminGarageController::class, 'index']);
    //             Route::post('/', [AdminGarageController::class, 'store']);
    //             Route::get('{id}', [AdminGarageController::class, 'show']);
    //             Route::put('{id}', [AdminGarageController::class, 'update']);
    //             Route::patch('{id}/verify', [AdminGarageController::class, 'verify']);
    //             Route::patch('{id}/toggle-active', [AdminGarageController::class, 'toggleActive']);
    //             Route::delete('{id}', [AdminGarageController::class, 'destroy']);
    //         });

    //         // ── Demandes partenaires ─────────────────────────────────
    //         Route::prefix('partner-requests')->group(function () {

    //             // Liste des demandes
    //             Route::get('/', [AdminPartnerRequestController::class, 'index']);

    //             // Détail
    //             Route::get('{id}', [AdminPartnerRequestController::class, 'show']);

    //             // Approuver et créer le compte pro
    //             Route::post('{id}/approve', [AdminPartnerRequestController::class, 'approve']);

    //             // Rejeter
    //             Route::post('{id}/reject', [AdminPartnerRequestController::class, 'reject']);

    //             // Marquer comme contacté
    //             Route::patch('{id}/contacted', [AdminPartnerRequestController::class, 'contacted']);
    //         });

    //         // ── Gestion Articles ─────────────────────────────────────
    //         Route::prefix('articles')->group(function () {

    //             Route::get('/', [AdminArticleController::class, 'index']);
    //             Route::post('/', [AdminArticleController::class, 'store']);
    //             Route::get('{id}', [AdminArticleController::class, 'show']);
    //             Route::put('{id}', [AdminArticleController::class, 'update']);

    //             // Publier / dépublier
    //             Route::patch('{id}/publish', [AdminArticleController::class, 'publish']);
    //             Route::patch('{id}/unpublish', [AdminArticleController::class, 'unpublish']);

    //             // Upload image de couverture
    //             Route::post('{id}/cover', [AdminArticleController::class, 'uploadCover']);

    //             Route::delete('{id}', [AdminArticleController::class, 'destroy']);
    //         });

    //         // ── Gestion Abonnements ──────────────────────────────────
    //         Route::prefix('subscriptions')->group(function () {

    //             // Tous les abonnements actifs
    //             Route::get('/', [AdminSubscriptionController::class, 'index']);

    //             // Détail
    //             Route::get('{id}', [AdminSubscriptionController::class, 'show']);

    //             // Annuler manuellement
    //             Route::post('{id}/cancel', [AdminSubscriptionController::class, 'cancel']);

    //             // Prolonger manuellement
    //             Route::post('{id}/extend', [AdminSubscriptionController::class, 'extend']);

    //             // Stats abonnements (revenus par plan)
    //             Route::get('stats/revenue', [AdminSubscriptionController::class, 'revenueStats']);
    //         });

    //         // ── Gestion Paiements ────────────────────────────────────
    //         Route::prefix('payments')->group(function () {

    //             Route::get('/', [AdminPaymentController::class, 'index']);
    //             Route::get('{id}', [AdminPaymentController::class, 'show']);

    //             // Relancer un paiement échoué
    //             Route::post('{id}/retry', [AdminPaymentController::class, 'retry']);

    //             // Rembourser
    //             Route::post('{id}/refund', [AdminPaymentController::class, 'refund']);

    //             // Export CSV
    //             Route::get('export/csv', [AdminPaymentController::class, 'exportCsv']);
    //         });

    //         // ── Gestion Bannières Pub ────────────────────────────────
    //         Route::prefix('banners')->group(function () {

    //             Route::get('/', [AdminBannerController::class, 'index']);
    //             Route::post('/', [AdminBannerController::class, 'store']);
    //             Route::get('{id}', [AdminBannerController::class, 'show']);
    //             Route::put('{id}', [AdminBannerController::class, 'update']);
    //             Route::patch('{id}/toggle-active', [AdminBannerController::class, 'toggleActive']);
    //             Route::delete('{id}', [AdminBannerController::class, 'destroy']);

    //             // Stats impressions/clics
    //             Route::get('{id}/stats', [AdminBannerController::class, 'stats']);
    //         });

    //         // ── Gestion Avis ─────────────────────────────────────────
    //         Route::prefix('reviews')->group(function () {

    //             // Avis en attente de modération
    //             Route::get('pending', [AdminReviewController::class, 'pending']);

    //             Route::get('/', [AdminReviewController::class, 'index']);
    //             Route::get('{id}', [AdminReviewController::class, 'show']);

    //             // Approuver un avis
    //             Route::patch('{id}/approve', [AdminReviewController::class, 'approve']);

    //             // Rejeter / supprimer
    //             Route::delete('{id}', [AdminReviewController::class, 'destroy']);
    //         });

    //         // ── Notifications Push (broadcast) ───────────────────────
    //         Route::prefix('notifications')->group(function () {

    //             // Envoyer à tous les utilisateurs
    //             Route::post('broadcast', [AdminNotificationController::class, 'broadcast']);

    //             // Envoyer à une ville
    //             Route::post('broadcast-city', [AdminNotificationController::class, 'broadcastCity']);

    //             // Envoyer à un utilisateur spécifique
    //             Route::post('send-to-user', [AdminNotificationController::class, 'sendToUser']);

    //             // Historique des broadcasts
    //             Route::get('history', [AdminNotificationController::class, 'history']);
    //         });

    //         // ── Propriétaires Pro ────────────────────────────────────
    //         Route::prefix('pro-owners')->group(function () {

    //             // Tous les owners (stations + garages)
    //             Route::get('/', [AdminProOwnerController::class, 'index']);
    //             Route::get('{type}/{id}', [AdminProOwnerController::class, 'show']);

    //             // Activer / suspendre
    //             Route::patch('{type}/{id}/toggle-active', [AdminProOwnerController::class, 'toggleActive']);

    //             // Réinitialiser le mot de passe
    //             Route::post('{type}/{id}/reset-password', [AdminProOwnerController::class, 'resetPassword']);
    //         });

    //         // ── Configuration application ────────────────────────────
    //         Route::prefix('settings')->group(function () {

    //             // Toutes les configurations
    //             Route::get('/', [AdminSettingController::class, 'index']);

    //             // Modifier une configuration
    //             Route::put('{key}', [AdminSettingController::class, 'update']);

    //             // Modifier en masse
    //             Route::put('/', [AdminSettingController::class, 'updateBulk']);
    //         });

    //         // ── Versions App ─────────────────────────────────────────
    //         Route::prefix('app-versions')->group(function () {

    //             Route::get('/', [AdminAppVersionController::class, 'index']);
    //             Route::post('/', [AdminAppVersionController::class, 'store']);
    //             Route::put('{id}', [AdminAppVersionController::class, 'update']);
    //             Route::patch('{id}/set-current', [AdminAppVersionController::class, 'setCurrent']);
    //             Route::delete('{id}', [AdminAppVersionController::class, 'destroy']);
    //         });

    //         // ── Journaux d'activité ──────────────────────────────────
    //         Route::prefix('activity-logs')->group(function () {

    //             Route::get('/', [AdminActivityLogController::class, 'index']);
    //             Route::get('{id}', [AdminActivityLogController::class, 'show']);

    //             // Export CSV
    //             Route::get('export/csv', [AdminActivityLogController::class, 'exportCsv']);
    //         });
    //     });
    // });
});
