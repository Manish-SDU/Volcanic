<?php

// This script reads the VolcanoesTableSeeder.php file and analyzes the volcano data
// ⚠️ EXECUTE: php volcano_analysis.php

echo "=== VOLCANO DATA ANALYSIS ===\n\n";

// Read
$seederFile = __DIR__ . '/database/seeders/VolcanoesTableSeeder.php';
if (!file_exists($seederFile)) {
    echo "Error: VolcanoesTableSeeder.php not found!\n";
    exit(1);
}

$content = file_get_contents($seederFile);

// Extract
preg_match('/\$volcanoes\s*=\s*\[([\s\S]*?)\];/', $content, $matches);
if (!$matches) {
    echo "Error: Could not extract volcanoes array from seeder file!\n";
    exit(1);
}

// Clean up
$arrayContent = $matches[1];
$arrayContent = preg_replace('/,\s*\]/', ']', $arrayContent); // Remove trailing comma
$arrayContent = '[' . $arrayContent . ']';

// Evaluate
$volcanoes = eval('return ' . $arrayContent . ';');

if (!$volcanoes) {
    echo "Error: Could not parse volcanoes array!\n";
    exit(1);
}

// counters
$countries = [];
$continents = [];
$activities = [];
$types = [];
$missingImages = [];

foreach ($volcanoes as $volcano) {
    $country = $volcano['country'] ?? 'Unknown';
    $countries[$country] = ($countries[$country] ?? 0) + 1;

    $continent = $volcano['continent'] ?? 'Unknown';
    $continents[$continent] = ($continents[$continent] ?? 0) + 1;

    $activity = $volcano['activity'] ?? 'Unknown';
    $activities[$activity] = ($activities[$activity] ?? 0) + 1;

    $type = $volcano['type'] ?? 'Unknown';
    $types[$type] = ($types[$type] ?? 0) + 1;

    // missing images ????
    $imageUrl = $volcano['image_url'] ?? '';
    if (empty(trim($imageUrl))) {
        $missingImages[] = $volcano['name'] ?? 'Unknown';
    }
}

function printCategory($title, $data) {
    echo strtoupper($title) . ":\n";
    echo str_repeat("=", strlen($title) + 1) . "\n";

    // Sort by count descending
    arsort($data);

    foreach ($data as $item => $count) {
        printf("%-25s : %3d\n", $item, $count);
    }
    echo "\n";
}

// print everything
function printMissingImages($missingImages) {
    echo "VOLCANOS WITH MISSING IMAGES:\n";
    echo "=============================\n";

    if (empty($missingImages)) {
        echo "None - All volcanoes have image URLs!\n";
    } else {
        echo "Found " . count($missingImages) . " volcanoes with missing images:\n\n";
        foreach ($missingImages as $volcanoName) {
            echo "• " . $volcanoName . "\n";
        }
    }
    echo "\n";
}

echo "Total volcanoes analyzed: " . count($volcanoes) . "\n\n";

printCategory("Countries", $countries);
printCategory("Continents", $continents);
printCategory("Activity Levels", $activities);
printCategory("Volcano Types", $types);
printMissingImages($missingImages);

echo "=== ANALYSIS COMPLETE ===\n";

?>