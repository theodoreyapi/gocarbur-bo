<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $primaryKey = 'id_app_setting';

    protected $fillable = [
        'key', 'value', 'type', 'group',
        'label', 'description', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /* ── Cast valeur selon type ──────────────────── */

    public function getCastedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'decimal' => (float) $this->value,
            'json'    => json_decode($this->value, true),
            default   => $this->value,
        };
    }

    /* ── Façade statique ─────────────────────────── */

    /**
     * Lire un paramètre (avec cache 10 min).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->casted_value : $default;
        });
    }

    /**
     * Écrire un paramètre et invalider le cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget("setting:{$key}");
    }

    /**
     * Retourner tous les paramètres d'un groupe, indexés par clé.
     */
    public static function group(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->get()->keyBy('key');
    }

    /* ── Seeder des valeurs par défaut ──────────── */

    public static function defaults(): array
    {
        return [
            // ── Général ─────────────────────────────────
            ['key' => 'app_name',             'group' => 'general', 'type' => 'string',  'label' => "Nom de l'application",               'value' => 'GoCarbu',                              'is_public' => true],
            ['key' => 'app_slogan',           'group' => 'general', 'type' => 'string',  'label' => 'Slogan',                             'value' => 'Votre compagnon automobile en Côte d\'Ivoire','is_public' => true],
            ['key' => 'support_email',        'group' => 'general', 'type' => 'string',  'label' => 'Email de support',                   'value' => 'support@GoCarbu.ci',                   'is_public' => true],
            ['key' => 'support_whatsapp',     'group' => 'general', 'type' => 'string',  'label' => 'WhatsApp support',                   'value' => '+225 07 00 00 00',                          'is_public' => true],
            ['key' => 'search_radius_km',     'group' => 'general', 'type' => 'integer', 'label' => 'Rayon de recherche par défaut (km)', 'value' => '10',                                        'is_public' => true],
            ['key' => 'maintenance_mode',     'group' => 'general', 'type' => 'boolean', 'label' => 'Mode maintenance',                   'value' => '0',                                         'is_public' => true],
            ['key' => 'registration_open',    'group' => 'general', 'type' => 'boolean', 'label' => 'Inscription ouverte',                'value' => '1',                                         'is_public' => true],

            // ── Tarification ────────────────────────────
            ['key' => 'price_user_premium_monthly',    'group' => 'pricing', 'type' => 'decimal', 'label' => 'User Premium — Mensuel (FCFA)',      'value' => '1500',  'is_public' => false],
            ['key' => 'price_user_premium_quarterly',  'group' => 'pricing', 'type' => 'decimal', 'label' => 'User Premium — Trimestriel (FCFA)',  'value' => '4000',  'is_public' => false],
            ['key' => 'price_user_premium_annual',     'group' => 'pricing', 'type' => 'decimal', 'label' => 'User Premium — Annuel (FCFA)',       'value' => '14000', 'is_public' => false],
            ['key' => 'price_station_pro_monthly',     'group' => 'pricing', 'type' => 'decimal', 'label' => 'Station Pro — Mensuel (FCFA)',       'value' => '12500', 'is_public' => false],
            ['key' => 'price_station_premium_monthly', 'group' => 'pricing', 'type' => 'decimal', 'label' => 'Station Premium — Mensuel (FCFA)',   'value' => '32500', 'is_public' => false],
            ['key' => 'price_garage_pro_monthly',      'group' => 'pricing', 'type' => 'decimal', 'label' => 'Garage Pro — Mensuel (FCFA)',        'value' => '12500', 'is_public' => false],
            ['key' => 'price_garage_premium_monthly',  'group' => 'pricing', 'type' => 'decimal', 'label' => 'Garage Premium — Mensuel (FCFA)',    'value' => '32500', 'is_public' => false],

            // ── Notifications ────────────────────────────
            ['key' => 'notif_document_expiry',   'group' => 'notifications', 'type' => 'boolean', 'label' => 'Rappel expiration documents',       'value' => '1', 'is_public' => false],
            ['key' => 'notif_fuel_alert',        'group' => 'notifications', 'type' => 'boolean', 'label' => 'Alerte baisse prix carburant',      'value' => '1', 'is_public' => false],
            ['key' => 'notif_sub_expiry',        'group' => 'notifications', 'type' => 'boolean', 'label' => 'Rappel expiration abonnement',      'value' => '1', 'is_public' => false],
            ['key' => 'notif_promotions',        'group' => 'notifications', 'type' => 'boolean', 'label' => 'Notification nouvelles promotions', 'value' => '1', 'is_public' => false],
            ['key' => 'notif_welcome',           'group' => 'notifications', 'type' => 'boolean', 'label' => 'Bienvenue nouveaux inscrits',       'value' => '1', 'is_public' => false],
            ['key' => 'notif_doc_days_before',   'group' => 'notifications', 'type' => 'integer', 'label' => 'Rappel document — jours avant',    'value' => '30','is_public' => false],

            // ── Sécurité ─────────────────────────────────
            ['key' => 'otp_validity_minutes',    'group' => 'security', 'type' => 'integer', 'label' => 'Durée validité OTP (minutes)',           'value' => '5',  'is_public' => false],
            ['key' => 'otp_max_attempts',        'group' => 'security', 'type' => 'integer', 'label' => 'Max tentatives OTP / heure',             'value' => '3',  'is_public' => false],
            ['key' => 'auth_token_days',         'group' => 'security', 'type' => 'integer', 'label' => 'Durée token auth (jours)',               'value' => '30', 'is_public' => false],
            ['key' => 'otp_length',              'group' => 'security', 'type' => 'integer', 'label' => 'Longueur code OTP',                      'value' => '6',  'is_public' => false],
            ['key' => 'review_moderation',       'group' => 'security', 'type' => 'boolean', 'label' => 'Modération avis obligatoire',            'value' => '1',  'is_public' => false],
            ['key' => 'admin_activity_log',      'group' => 'security', 'type' => 'boolean', 'label' => "Journal d'activité admin activé",        'value' => '1',  'is_public' => false],
            ['key' => 'ip_bruteforce_block',     'group' => 'security', 'type' => 'boolean', 'label' => 'Blocage IP après 10 tentatives',         'value' => '1',  'is_public' => false],

            // ── SMS & Firebase ───────────────────────────
            ['key' => 'sms_provider',      'group' => 'integrations', 'type' => 'string', 'label' => 'Fournisseur SMS OTP',         'value' => 'Infobip',           'is_public' => false],
            ['key' => 'sms_api_key',       'group' => 'integrations', 'type' => 'string', 'label' => 'Infobip API Key',             'value' => '',                  'is_public' => false],
            ['key' => 'sms_sender_id',     'group' => 'integrations', 'type' => 'string', 'label' => 'Expéditeur SMS (SenderID)',   'value' => 'AutoPlat',          'is_public' => false],
            ['key' => 'fcm_server_key',    'group' => 'integrations', 'type' => 'string', 'label' => 'Firebase Server Key (FCM)',   'value' => '',                  'is_public' => false],
            ['key' => 'fcm_project_id',    'group' => 'integrations', 'type' => 'string', 'label' => 'Firebase Project ID',        'value' => 'gocarbu-ci-prod','is_public' => false],

            // ── Paiements ────────────────────────────────
            ['key' => 'payment_orange_enabled', 'group' => 'payments', 'type' => 'boolean', 'label' => 'Orange Money activé',  'value' => '1', 'is_public' => false],
            ['key' => 'payment_orange_site_id', 'group' => 'payments', 'type' => 'string',  'label' => 'Orange — Site ID',     'value' => '',  'is_public' => false],
            ['key' => 'payment_orange_secret',  'group' => 'payments', 'type' => 'string',  'label' => 'Orange — Clé secrète', 'value' => '',  'is_public' => false],
            ['key' => 'payment_wave_enabled',   'group' => 'payments', 'type' => 'boolean', 'label' => 'Wave activé',          'value' => '1', 'is_public' => false],
            ['key' => 'payment_wave_api_key',   'group' => 'payments', 'type' => 'string',  'label' => 'Wave — API Key',       'value' => '',  'is_public' => false],
            ['key' => 'payment_wave_webhook',   'group' => 'payments', 'type' => 'string',  'label' => 'Wave — Webhook Secret','value' => '',  'is_public' => false],
            ['key' => 'payment_mtn_enabled',    'group' => 'payments', 'type' => 'boolean', 'label' => 'MTN MoMo activé',      'value' => '1', 'is_public' => false],
            ['key' => 'payment_mtn_api_key',    'group' => 'payments', 'type' => 'string',  'label' => 'MTN — API Key',        'value' => '',  'is_public' => false],
            ['key' => 'payment_mtn_sub_key',    'group' => 'payments', 'type' => 'string',  'label' => 'MTN — Subscription Key','value' => '', 'is_public' => false],
            ['key' => 'payment_moov_enabled',   'group' => 'payments', 'type' => 'boolean', 'label' => 'Moov Money activé',    'value' => '0', 'is_public' => false],
            ['key' => 'payment_moov_token',     'group' => 'payments', 'type' => 'string',  'label' => 'Moov — Token',         'value' => '',  'is_public' => false],
            ['key' => 'payment_moov_secret',    'group' => 'payments', 'type' => 'string',  'label' => 'Moov — Secret',        'value' => '',  'is_public' => false],

            // ── Limites & quotas ─────────────────────────
            ['key' => 'quota_vehicles_free',        'group' => 'quotas', 'type' => 'integer', 'label' => 'Véhicules max (plan Gratuit)',     'value' => '1',  'is_public' => false],
            ['key' => 'quota_vehicles_premium',     'group' => 'quotas', 'type' => 'integer', 'label' => 'Véhicules max (Premium)',          'value' => '10', 'is_public' => false],
            ['key' => 'quota_photos_station_pro',   'group' => 'quotas', 'type' => 'integer', 'label' => 'Photos max par station (Pro)',     'value' => '6',  'is_public' => false],
            ['key' => 'quota_photos_station_prem',  'group' => 'quotas', 'type' => 'integer', 'label' => 'Photos max station (Premium)',     'value' => '20', 'is_public' => false],
            ['key' => 'quota_reviews_per_user',     'group' => 'quotas', 'type' => 'integer', 'label' => 'Avis max par utilisateur',         'value' => '1',  'is_public' => false],
            ['key' => 'quota_featured_articles',    'group' => 'quotas', 'type' => 'integer', 'label' => 'Articles en vedette max',          'value' => '10', 'is_public' => false],
        ];
    }
}
