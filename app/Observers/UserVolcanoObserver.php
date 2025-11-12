<?php

namespace App\Observers;

use App\Models\UserVolcano;
use App\Models\Achievement;
use App\Services\AchievementService;
use Illuminate\Support\Facades\Log;

class UserVolcanoObserver
{
    public function created(UserVolcano $userVolcano): void
    {
        $this->processIfVisited($userVolcano);
    }

    public function updated(UserVolcano $userVolcano): void
    {
        // Only process if status was changed to 'visited'
        if ($userVolcano->isDirty('status') && $userVolcano->status === 'visited') {
            $this->processIfVisited($userVolcano);
        }
    }

    protected function processIfVisited(UserVolcano $userVolcano): void
    {
        $user = $userVolcano->user;
        if (!$user) {
            Log::error('UserVolcanoObserver: No user found for userVolcano ID ' . $userVolcano->id);
            return;
        }
        $volcanoId = $userVolcano->volcanoes_id ?? $userVolcano->volcano_id ?? null;
        if (!$volcanoId) {
            Log::error('UserVolcanoObserver: No volcano id found on userVolcano ID ' . $userVolcano->id);
            return;
        }

        $volcano = $userVolcano->volcano;
        Log::info("UserVolcanoObserver: Processing visit for user {$user->id} to volcano {$volcanoId}");

        // Delegate achievement logic to the service (single source of truth)
        app(AchievementService::class)->checkAchievements($user, $volcano);
    }
    
    public function deleted(UserVolcano $userVolcano): void
    {
        if ($userVolcano->status === 'visited') {
            $user = $userVolcano->user;
            if (!$user) {
                Log::error('UserVolcanoObserver: No user found for userVolcano being deleted');
                return;
            }

            // Recheck all achievements
            app(AchievementService::class)->revokeInvalidAchievements($user);
        }
    }
}
