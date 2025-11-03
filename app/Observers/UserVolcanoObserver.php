<?php

namespace App\Observers;

use App\Models\UserVolcano;
use App\Models\Achievement;

class UserVolcanoObserver
{
    /**
     * Handle the UserVolcano "created" event.
     */
    public function created(UserVolcano $userVolcano): void
    {
        $user = $userVolcano->user;
        if (!$user) {
            \Log::error('UserVolcanoObserver: No user found for userVolcano ID ' . $userVolcano->id);
            return;
        }

        $volcano = $userVolcano->volcano;
        if (!$volcano) {
            \Log::error('UserVolcanoObserver: No volcano found for userVolcano ID ' . $userVolcano->id);
            return;
        }

        \Log::info('UserVolcanoObserver: Processing visit for user ' . $user->id . ' to volcano ' . $volcano->id);

        // Get all achievements that haven't been earned yet
        $unachievedAchievements = Achievement::whereDoesntHave('users', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })->get();

        \Log::info('UserVolcanoObserver: Found ' . $unachievedAchievements->count() . ' unachieved achievements');

        foreach ($unachievedAchievements as $achievement) {
            $earned = false;

            switch ($achievement->metric) {
                case 'total_visits':
                    $visitCount = $user->userVolcanoes()->count();
                    $earned = $visitCount >= $achievement->threshold;
                    break;

                case 'visits_by_continent':
                    if ($achievement->aggregator === 'count_distinct') {
                        $distinctContinents = $user->userVolcanoes()
                            ->join('volcanoes', 'user_volcanoes.volcano_id', '=', 'volcanoes.id')
                            ->distinct()
                            ->pluck('continent')
                            ->count();
                        $earned = $distinctContinents >= $achievement->threshold;
                    }
                    break;

                // Not working for now
                case 'visits_by_activity':
                    if ($achievement->dimensions && isset($achievement->dimensions['activity'])) {
                        $requiredActivity = $achievement->dimensions['activity'];
                        $activityCount = $user->userVolcanoes()
                            ->where(function($query) use ($volcano, $requiredActivity) {
                                $query->where('volcano_id', $volcano->id)
                                    ->orWhereHas('volcano', fn($q) => $q->where('activity', $requiredActivity));
                            })->count();
                        $earned = $activityCount >= $achievement->threshold;
                    }
                    break;

                case 'visits_by_type':
                    if ($achievement->dimensions && isset($achievement->dimensions['type'])) {
                        $typeCount = $user->userVolcanoes()
                            ->whereHas('volcano', function($query) use ($achievement) {
                                $query->where('type', $achievement->dimensions['type']);
                            })->count();
                        $earned = $typeCount >= $achievement->threshold;
                    }
                    break;
            }
            if ($earned) {
                \Log::info('UserVolcanoObserver: User ' . $user->id . ' earned achievement: ' . $achievement->name);
                $user->achievements()->attach($achievement->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                \Log::info('UserVolcanoObserver: Achievement not earned: ' . $achievement->name . ', metric: ' . $achievement->metric);
            }
        }
    }
}