<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Listing;

class NotificationService
{
    /**
     * Notify user when listing is approved
     */
    public function listingApproved(Listing $listing): void
    {
        Notification::create([
            'user_id' => $listing->user_id,
            'type' => 'listing_approved',
            'title' => 'Oglas odobren',
            'message' => "Vaš oglas \"{$listing->title}\" je odobren i sada je aktivan!",
            'action_url' => route('listings.show', $listing->slug),
            'data' => [
                'listing_id' => $listing->id,
                'listing_title' => $listing->title,
            ],
        ]);
    }

    /**
     * Notify user when listing is rejected
     */
    public function listingRejected(Listing $listing, ?string $reason = null): void
    {
        $message = "Vaš oglas \"{$listing->title}\" je odbijen.";
        if ($reason) {
            $message .= " Razlog: {$reason}";
        }

        Notification::create([
            'user_id' => $listing->user_id,
            'type' => 'listing_rejected',
            'title' => 'Oglas odbijen',
            'message' => $message,
            'action_url' => route('listings.edit', $listing->slug),
            'data' => [
                'listing_id' => $listing->id,
                'listing_title' => $listing->title,
                'reason' => $reason,
            ],
        ]);
    }

    /**
     * Notify user when package is expiring soon
     */
    public function packageExpiring(Listing $listing, int $daysLeft): void
    {
        Notification::create([
            'user_id' => $listing->user_id,
            'type' => 'package_expiring',
            'title' => 'Paket ističe uskoro',
            'message' => "Vaš paket za oglas \"{$listing->title}\" ističe za {$daysLeft} dana. Produžite ga da ostanete na vrhu!",
            'action_url' => route('listings.promote', $listing->id),
            'data' => [
                'listing_id' => $listing->id,
                'listing_title' => $listing->title,
                'days_left' => $daysLeft,
                'expires_at' => $listing->featured_until ?? $listing->top_until,
            ],
        ]);
    }

    /**
     * Notify user of new message (placeholder for future messaging system)
     */
    public function newMessage(User $user, string $from, string $regarding): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'new_message',
            'title' => 'Nova poruka',
            'message' => "Imate novu poruku od {$from} vezano za: {$regarding}",
            'action_url' => route('dashboard.index'), // Update when messaging is implemented
            'data' => [
                'from' => $from,
                'regarding' => $regarding,
            ],
        ]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Delete old read notifications (cleanup)
     */
    public function cleanupOldNotifications(int $daysOld = 60): void
    {
        Notification::where('is_read', true)
            ->where('read_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}