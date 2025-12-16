<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\Volcano;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    /**
     * Check and attach any achievements the user has earned.
     * If a Volcano instance is provided, the service may short-circuit some checks
     * (for example an 'exists' aggregator when the current volcano already matches dimensions).
     *
     * @param User $user
     * @param Volcano|null $volcano
     * @return void
     */
    public function checkAchievements(User $user, ?Volcano $volcano = null): void
    {
        $achievements = Achievement::whereDoesntHave('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->get();

        foreach ($achievements as $achievement) {
            // Normalize dimensions (DB may store null, array, or JSON string)
            $raw = $achievement->dimensions ?? [];
            if (is_string($raw)) {
                $dimensions = json_decode($raw, true) ?: [];
            } elseif (is_array($raw)) {
                $dimensions = $raw;
            } else {
                $dimensions = [];
            }

            $earned = false;

            switch ($achievement->metric) {
                case 'total_visits':
                    $count = DB::table('user_volcanoes')
                        ->where('user_id', $user->id)
                        ->where('status', 'visited')
                        ->count();
                    $earned = $count >= $achievement->threshold;
                    break;

                case 'visits_by_continent':
                    if (!empty($dimensions['continent'])) {
                        $requiredContinents = $dimensions['continent'];
                        
                        if ($achievement->aggregator === 'count_distinct') {
                            $distinct = DB::table('user_volcanoes')
                                ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                                ->where('user_volcanoes.user_id', $user->id)
                                ->where('user_volcanoes.status', 'visited')
                                ->whereIn('volcanoes.continent', $requiredContinents)
                                ->distinct()
                                ->count('volcanoes.continent');
                            $earned = $distinct >= $achievement->threshold;
                        }
                    }
                    break;

                case 'visits_by_activity':
                    if (!empty($dimensions['activity'])) {
                        $required = $dimensions['activity'];

                        // If a Volcano instance was supplied and matches, we can set short-circuit
                        if ($volcano && $this->matchesDimensions($volcano, ['activity' => $required]) && $achievement->aggregator === 'exists') {
                            $earned = true;
                            break;
                        }

                        $count = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id)
                            ->where('user_volcanoes.status', 'visited')
                            ->where('volcanoes.activity', $required)
                            ->count();
                        $earned = $count >= $achievement->threshold;
                    }
                    break;

                case 'visits_by_type':
                    if (!empty($dimensions['type'])) {
                        $requiredType = $dimensions['type'];
                        $count = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id)
                            ->where('user_volcanoes.status', 'visited')
                            ->where('volcanoes.type', $requiredType)
                            ->count();
                        $earned = $count >= $achievement->threshold;
                    }
                    break;

                default:
                    // Generic: if dimensions provided, count matching visited volcanoes
                    if (!empty($dimensions)) {
                        $query = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id)
                            ->where('user_volcanoes.status', 'visited');

                        foreach ($dimensions as $col => $val) {
                            $query->where("volcanoes.$col", $val);
                        }

                        $cnt = $query->count();
                        $earned = $cnt >= $achievement->threshold;
                    }
                    break;
            }

            if ($earned) {
                Log::info("AchievementService: User {$user->id} earned achievement: {$achievement->name}");
                if (!$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                    $user->achievements()->attach($achievement->id, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                Log::info("AchievementService: Achievement not earned: {$achievement->name}, metric: {$achievement->metric}");
            }
        }
    }

    protected function matchesDimensions(Volcano $volcano, array $dimensions)
    {
        foreach ($dimensions as $key => $value) {
            if (!isset($volcano->$key) || strtolower($volcano->$key) != strtolower($value)) {
                return false;
            }
        }
        return true;
    }
    public function revokeInvalidAchievements(User $user): void
    {
        // Get user's achievements
        $userAchievements = $user->achievements;

        foreach ($userAchievements as $achievement) {
            $earned = false;
            
            // Normalize dimensions (DB may store null, array, or JSON string)
            $raw = $achievement->dimensions ?? [];
            if (is_string($raw)) {
                $dimensions = json_decode($raw, true) ?: [];
            } elseif (is_array($raw)) {
                $dimensions = $raw;
            } else {
                $dimensions = [];
            }
            
            switch ($achievement->metric) {
                case 'total_visits':
                    $count = DB::table('user_volcanoes')
                        ->where('user_id', $user->id)
                        ->where('status', 'visited')
                        ->count();
                    $earned = $count >= $achievement->threshold;
                    break;

                case 'visits_by_continent':
                    if (!empty($dimensions['continent'])) {
                        $requiredContinents = $dimensions['continent'];
                        
                        if ($achievement->aggregator === 'count_distinct') {
                            $distinct = DB::table('user_volcanoes')
                                ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                                ->where('user_volcanoes.user_id', $user->id)
                                ->where('user_volcanoes.status', 'visited')
                                ->whereIn('volcanoes.continent', $requiredContinents)
                                ->distinct()
                                ->count('volcanoes.continent');
                            $earned = $distinct >= $achievement->threshold;
                        }
                    }
                    break;

                case 'visits_by_activity':
                    if (!empty($dimensions['activity'])) {
                        $count = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id)
                            ->where('user_volcanoes.status', 'visited')
                            ->where('volcanoes.activity', $dimensions['activity'])
                            ->count();
                        $earned = $count >= $achievement->threshold;
                    }
                    break;
            }

            // If achievement is no longer valid, detach it
            if (!$earned) {
                $user->achievements()->detach($achievement->id);
                Log::info("Achievement {$achievement->name} revoked from user {$user->id}");
            }
        }
    }
}
