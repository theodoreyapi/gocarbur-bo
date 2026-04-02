<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\Reminder;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Console\Command;

// ═══════════════════════════════════════════════════════════════
// CheckDocumentExpiry
// Planification: daily → php artisan documents:check-expiry
// ═══════════════════════════════════════════════════════════════

class CheckDocumentExpiry extends Command
{
    protected $signature   = 'documents:check-expiry';
    protected $description = 'Vérifie les documents qui expirent et envoie des notifications';

    public function handle(FirebaseService $firebase): void
    {
        $this->info('Vérification des expirations de documents...');

        // Documents expirant dans 30 jours (rappel J-30)
        $expiringSoon = Document::with(['vehicle.user'])
            ->whereDate('expiry_date', now()->addDays(30)->toDateString())
            ->where('status', '!=', 'expired')
            ->get();

        // Documents expirant dans 7 jours (rappel urgent J-7)
        $expiringUrgent = Document::with(['vehicle.user'])
            ->whereDate('expiry_date', now()->addDays(7)->toDateString())
            ->get();

        // Documents expirés aujourd'hui
        $expiredToday = Document::with(['vehicle.user'])
            ->whereDate('expiry_date', now()->toDateString())
            ->get();

        $count = 0;

        foreach ($expiringSoon as $document) {
            $document->update(['status' => 'expiring_soon']);
            $this->notifyDocumentExpiry($document, $firebase, 30);
            $count++;
        }

        foreach ($expiringUrgent as $document) {
            $this->notifyDocumentExpiry($document, $firebase, 7, urgent: true);
            $count++;
        }

        foreach ($expiredToday as $document) {
            $document->update(['status' => 'expired']);
            $this->notifyDocumentExpiry($document, $firebase, 0, expired: true);
            $count++;
        }

        $this->info("{$count} notifications envoyées.");
    }

    private function notifyDocumentExpiry(
        Document $document,
        FirebaseService $firebase,
        int $daysLeft,
        bool $urgent = false,
        bool $expired = false
    ): void {
        $user = $document->vehicle?->user;
        if (!$user || !$user->fcm_token) return;

        $docLabel = match ($document->type) {
            'permis_conduire'    => 'Permis de conduire',
            'assurance'          => 'Assurance',
            'carte_grise'        => 'Carte grise',
            'visite_technique'   => 'Visite technique',
            'vignette'           => 'Vignette',
            default              => 'Document',
        };

        $vehicle = $document->vehicle;
        $vehicleLabel = "{$vehicle->brand} {$vehicle->model}";

        if ($expired) {
            $title = "Document expiré !";
            $body  = "{$docLabel} de votre {$vehicleLabel} a expiré aujourd'hui.";
        } elseif ($urgent) {
            $title = "Urgent : Document bientôt expiré";
            $body  = "{$docLabel} ({$vehicleLabel}) expire dans {$daysLeft} jours !";
        } else {
            $title = "Rappel : Document à renouveler";
            $body  = "{$docLabel} ({$vehicleLabel}) expire dans {$daysLeft} jours.";
        }

        $firebase->sendToDevice($user->fcm_token, ['title' => $title, 'body' => $body], [
            'type'        => 'document_expiry',
            'document_id' => (string) $document->id,
            'vehicle_id'  => (string) $vehicle->id,
        ]);

        // Persister la notification
        $user->notifications()->create([
            'type'       => 'document_expiry',
            'title'      => $title,
            'body'       => $body,
            'data'       => ['document_id' => $document->id, 'vehicle_id' => $vehicle->id],
            'is_push_sent' => true,
            'push_sent_at' => now(),
        ]);

        // Marquer le rappel comme envoyé
        Reminder::where('document_id', $document->id)
            ->where('is_sent', false)
            ->update(['is_sent' => true, 'sent_at' => now()]);
    }
}
