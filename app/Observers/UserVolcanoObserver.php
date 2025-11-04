<?php

namespace App\Observers;

use App\Models\UserVolcano;
use App\Models\Achievement;
use Illuminate\Support\Facades\DB;
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

        Log::info("UserVolcanoObserver: Processing visit for user {$user->id} to volcano {$volcanoId}");

        // Get achievements the user hasn't earned yet
        $unachieved = Achievement::whereDoesntHave('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->get();

        Log::info('UserVolcanoObserver: Found ' . $unachieved->count() . ' unachieved achievements');

        foreach ($unachieved as $achievement) {
            $earned = false;

            switch ($achievement->metric) {
                case 'total_visits':
                    $visitCount = DB::table('user_volcanoes')
                        ->where('user_id', $user->id)
                        ->count();
                    $earned = $visitCount >= $achievement->threshold;
                    break;

                case 'visits_by_continent':
                    if ($achievement->aggregator === 'count_distinct') {
                        $distinctContinents = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id)
                            ->distinct()
                            ->count('volcanoes.continent');
                        $earned = $distinctContinents >= $achievement->threshold;
                    }
                    break;

                case 'visits_by_activity':
                    if ($achievement->dimensions && isset($achievement->dimensions['activity'])) {
                        $required = $achievement->dimensions['activity'];
                        $activityCount = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id)
                            ->where('volcanoes.activity', $required)
                            ->count();
                        $earned = $activityCount >= $achievement->threshold;
                    }
                    break;

                case 'visits_by_type':
                    if ($achievement->dimensions && isset($achievement->dimensions['type'])) {
                        $requiredType = $achievement->dimensions['type'];
                        $typeCount = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id)
                            ->where('volcanoes.type', $requiredType)
                            ->count();
                        $earned = $typeCount >= $achievement->threshold;
                    }
                    break;

                // fallback: if metric = 'volcano_visit' and dimensions exist (generic)
                case 'volcano_visit':
                    $dimensions = $achievement->dimensions ?? [];
                    if (empty($dimensions)) {
                        // simple exists
                        $visitCount = DB::table('user_volcanoes')
                            ->where('user_id', $user->id)
                            ->count();
                        $earned = $visitCount >= $achievement->threshold;
                    } else {
                        $query = DB::table('user_volcanoes')
                            ->join('volcanoes', 'user_volcanoes.volcanoes_id', '=', 'volcanoes.id')
                            ->where('user_volcanoes.user_id', $user->id);

                        foreach ($dimensions as $col => $val) {
                            $query->where("volcanoes.$col", $val);
                        }

                        $cnt = $query->count();
                        $earned = $cnt >= $achievement->threshold;
                    }
                    break;
            }

            if ($earned) {
                Log::info("UserVolcanoObserver: User {$user->id} earned achievement: {$achievement->name}");
                // attach if not attached (double-check)
                if (!$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                    $user->achievements()->attach($achievement->id, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                Log::info("UserVolcanoObserver: Achievement not earned: {$achievement->name}, metric: {$achievement->metric}");
            }
        }
    }
}
