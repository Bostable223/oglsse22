<?php
// app/Console/Commands/SendPackageExpirationWarnings.php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use App\Models\Notification;

class SendPackageExpirationWarnings extends Command
{
    protected $signature = 'notifications:package-expiring';
    protected $description = 'Send notifications for packages expiring soon';

    public function handle(NotificationService $notificationService): void
    {
        // Find listings with packages expiring in 3 days
        $expiringListings = Listing::where('status', 'active')
            ->where(function($query) {
                $query->where('featured_until', '>=', now())
                      ->where('featured_until', '<=', now()->addDays(3))
                      ->orWhere(function($q) {
                          $q->where('top_until', '>=', now())
                            ->where('top_until', '<=', now()->addDays(3));
                      });
            })
            ->get();

        foreach ($expiringListings as $listing) {
            $expiresAt = $listing->featured_until ?? $listing->top_until;
            $daysLeft = now()->diffInDays($expiresAt);

            // Check if notification already sent today
            $alreadyNotified = Notification::where('user_id', $listing->user_id)
                ->where('type', 'package_expiring')
                ->where('data->listing_id', $listing->id)
                ->whereDate('created_at', today())
                ->exists();

            if (!$alreadyNotified) {
                $notificationService->packageExpiring($listing, (int)$daysLeft);
                $this->info("Sent expiration warning to user {$listing->user_id} for listing {$listing->id}");
            }
        }

        $this->info("Package expiration warnings sent successfully!");
    }
}