<?php

namespace App\Observers;

use App\Models\CircleMember;

class CircleMemberObserver
{
    /**
     * Handle the CircleMember "creating" event.
     */
    public function creating(CircleMember $circleMember): void
    {
        $circle = $circleMember->circle;
        
        // Skip check if user is the owner
        if ($circle->owner_id === $circleMember->user_id) {
            return;
        }

        // If circle is public, no voucher needed
        if ($circle->is_public) {
            return;
        }

        if (is_null($circleMember->vouched_by)) {
             abort(403, 'You must be vouched by a member to join this private circle.');
        }
    }

    /**
     * Handle the CircleMember "created" event.
     */
    public function created(CircleMember $circleMember): void
    {
        //
    }

    /**
     * Handle the CircleMember "updated" event.
     */
    public function updated(CircleMember $circleMember): void
    {
        //
    }

    /**
     * Handle the CircleMember "deleted" event.
     */
    public function deleted(CircleMember $circleMember): void
    {
        //
    }

    /**
     * Handle the CircleMember "restored" event.
     */
    public function restored(CircleMember $circleMember): void
    {
        //
    }

    /**
     * Handle the CircleMember "force deleted" event.
     */
    public function forceDeleted(CircleMember $circleMember): void
    {
        //
    }
}
