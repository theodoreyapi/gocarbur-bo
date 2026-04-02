<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Planification des tâches automatiques
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérification des documents expirants — chaque jour à 8h
        $schedule->command('documents:check-expiry')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/document-expiry.log'));

        // Alertes de prix carburant — toutes les heures
        $schedule->command('fuel:send-alerts')
            ->hourly()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/fuel-alerts.log'));

        // Nettoyage OTP expirés — toutes les 30 minutes
        $schedule->command('otp:clean')
            ->everyThirtyMinutes()
            ->withoutOverlapping();

        // Vérification et expiration des abonnements — chaque nuit à minuit
        $schedule->call(function () {
            \App\Models\Subscription::where('status', 'active')
                ->where('expires_at', '<', now())
                ->update(['status' => 'expired']);

            // Rétrograder les users premium expirés
            \App\Models\User::where('subscription_type', 'premium')
                ->where('subscription_expires_at', '<', now())
                ->update(['subscription_type' => 'free']);

            // Rétrograder les stations/garages pro expirés
            \App\Models\Station::where('subscription_type', '!=', 'free')
                ->where('subscription_expires_at', '<', now())
                ->update(['subscription_type' => 'free']);

            \App\Models\Garage::where('subscription_type', '!=', 'free')
                ->where('subscription_expires_at', '<', now())
                ->update(['subscription_type' => 'free']);
        })
        ->dailyAt('00:01')
        ->name('expire-subscriptions')
        ->withoutOverlapping();

        // Rappels utilisateurs — chaque jour à 9h
        $schedule->call(function () {
            $dueReminders = \App\Models\Reminder::with(['user', 'vehicle', 'document'])
                ->where('is_sent', false)
                ->where('is_dismissed', false)
                ->whereDate('remind_at', now()->toDateString())
                ->get();

            $firebase = app(\App\Services\FirebaseService::class);

            foreach ($dueReminders as $reminder) {
                $user = $reminder->user;
                if (!$user || !$user->fcm_token) continue;

                $firebase->sendToDevice($user->fcm_token, [
                    'title' => $reminder->title,
                    'body'  => $reminder->notes ?? "N'oubliez pas : {$reminder->title}",
                ], [
                    'type'        => 'reminder',
                    'reminder_id' => (string) $reminder->id,
                ]);

                $reminder->update(['is_sent' => true, 'sent_at' => now()]);

                // Notif en base
                $user->notifications()->create([
                    'type'         => 'reminder',
                    'title'        => $reminder->title,
                    'body'         => $reminder->notes ?? "Rappel : {$reminder->title}",
                    'is_push_sent' => true,
                    'push_sent_at' => now(),
                ]);
            }
        })
        ->dailyAt('09:00')
        ->name('send-reminders')
        ->withoutOverlapping();

        // Nettoyage des journaux d'activité anciens (> 6 mois)
        $schedule->call(function () {
            \App\Models\ActivityLog::where('occurred_at', '<', now()->subMonths(6))->delete();
        })
        ->monthly()
        ->name('clean-activity-logs');

        // Nettoyage des vues de stations et garages anciens (> 1 an)
        $schedule->call(function () {
            \App\Models\StationView::where('viewed_at', '<', now()->subYear())->delete();
            \App\Models\GarageView::where('viewed_at', '<', now()->subYear())->delete();
        })
        ->monthly()
        ->name('clean-old-views');
    }

    /**
     * Commandes Artisan personnalisées
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
