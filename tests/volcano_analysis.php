<?php
/**
 * Volcano Data Analysis Script Test
 * 🚩 TO RUN: php tests/volcano_analysis.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Volcano;

echo "===========================================\n";
echo "VOLCANO DATABASE ANALYSIS\n";
echo "===========================================\n\n";

// Get all volcanoes
$volcanoes = Volcano::all();
$total = $volcanoes->count();

echo "Total volcanoes analyzed: {$total}\n\n";

// Country distribution
echo "COUNTRIES:\n";
echo "==========\n";
$countries = $volcanoes->groupBy('country')
    ->map(fn($group) => $group->count())
    ->sortDesc();

foreach ($countries as $country => $count) {
    printf("%-30s: %4d\n", $country, $count);
}

// Continent distribution
echo "\n\nCONTINENTS:\n";
echo "===========\n";
$continents = $volcanoes->groupBy('continent')
    ->map(fn($group) => $group->count())
    ->sortDesc();

foreach ($continents as $continent => $count) {
    printf("%-30s: %4d\n", $continent, $count);
}

// Activity level distribution
echo "\n\nACTIVITY LEVELS:\n";
echo "================\n";
$activities = $volcanoes->groupBy('activity')
    ->map(fn($group) => $group->count())
    ->sortDesc();

foreach ($activities as $activity => $count) {
    printf("%-30s: %4d\n", $activity, $count);
}

// Type distribution
echo "\n\nVOLCANO TYPES:\n";
echo "==============\n";
$types = $volcanoes->groupBy('type')
    ->map(fn($group) => $group->count())
    ->sortDesc();

foreach ($types as $type => $count) {
    printf("%-30s: %4d\n", $type, $count);
}

// Data completeness check
echo "\n\nDATA COMPLETENESS CHECK:\n";
echo "========================\n";

$missingImages = $volcanoes->filter(fn($v) => empty($v->image_url))->count();
$missingLatitude = $volcanoes->filter(fn($v) => is_null($v->latitude))->count();
$missingLongitude = $volcanoes->filter(fn($v) => is_null($v->longitude))->count();
$missingElevation = $volcanoes->filter(fn($v) => is_null($v->elevation))->count();
$missingDescription = $volcanoes->filter(fn($v) => empty($v->description))->count();

echo "Missing image URLs       : " . ($missingImages > 0 ? "❌ {$missingImages}" : "✅ 0") . "\n";
echo "Missing latitudes        : " . ($missingLatitude > 0 ? "❌ {$missingLatitude}" : "✅ 0") . "\n";
echo "Missing longitudes       : " . ($missingLongitude > 0 ? "❌ {$missingLongitude}" : "✅ 0") . "\n";
echo "Missing elevations       : " . ($missingElevation > 0 ? "❌ {$missingElevation}" : "✅ 0") . "\n";
echo "Missing descriptions     : " . ($missingDescription > 0 ? "❌ {$missingDescription}" : "✅ 0") . "\n";

// Geographic coverage
echo "\n\nGEOGRAPHIC COVERAGE:\n";
echo "====================\n";
$withCoordinates = $volcanoes->filter(fn($v) => !is_null($v->latitude) && !is_null($v->longitude))->count();
$percentage = round(($withCoordinates / $total) * 100, 2);
echo "Volcanoes with coordinates: {$withCoordinates} / {$total} ({$percentage}%)\n";

// Elevation statistics
echo "\n\nELEVATION STATISTICS:\n";
echo "=====================\n";
$elevations = $volcanoes->pluck('elevation')->filter();
if ($elevations->count() > 0) {
    $highest = $elevations->max();
    $lowest = $elevations->min();
    $average = round($elevations->avg());
    
    echo "Highest volcano: " . number_format($highest) . " m\n";
    echo "Lowest volcano:  " . number_format($lowest) . " m\n";
    echo "Average height:  " . number_format($average) . " m\n";
    
    // Find the highest volcano
    $highestVolcano = $volcanoes->where('elevation', $highest)->first();
    if ($highestVolcano) {
        echo "\nHighest: {$highestVolcano->name} ({$highestVolcano->country}) - " . number_format($highest) . " m\n";
    }
    
    // Find the lowest volcano
    $lowestVolcano = $volcanoes->where('elevation', $lowest)->first();
    if ($lowestVolcano) {
        echo "Lowest:  {$lowestVolcano->name} ({$lowestVolcano->country}) - " . number_format($lowest) . " m\n";
    }
}

echo "\n===========================================\n";
echo "        🤗 ANALYSIS COMPLETE 🤗\n";
echo "===========================================\n";
