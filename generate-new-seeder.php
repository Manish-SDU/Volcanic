<?php

/**
 * Script to generate a new VolcanoesTableSeeder.php with updated image URLs
 * that include file extensions from the volcanic-images repository
 */

// Read the image list
$imageListFile = 'volcano-images-list.txt';
if (!file_exists($imageListFile)) {
    die("Error: volcano-images-list.txt not found!\n");
}

$imageList = file($imageListFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Create a mapping: basename => full filename
$imageMap = [];
foreach ($imageList as $filename) {
    // Remove extension to get basename
    $basename = preg_replace('/\.(jpg|jpeg|png|gif|webp|JPG|JPEG|PNG)$/i', '', $filename);
    $imageMap[$basename] = $filename;
}

echo "✓ Loaded " . count($imageMap) . " image mappings\n";

// Read the old seeder
$oldSeederFile = 'database/seeders/VolcanoesTableSeeder.php_OLD.md';
if (!file_exists($oldSeederFile)) {
    die("Error: VolcanoesTableSeeder.php_OLD.md not found!\n");
}

$oldSeederContent = file_get_contents($oldSeederFile);

echo "✓ Read old seeder file\n";

// Update image_url values with full filenames
$updatedCount = 0;
$notFoundCount = 0;
$notFound = [];

$newSeederContent = preg_replace_callback(
    "/'image_url'\s*=>\s*'([^']+)'/",
    function($matches) use ($imageMap, &$updatedCount, &$notFoundCount, &$notFound) {
        $oldValue = $matches[1];

        if (isset($imageMap[$oldValue])) {
            $newValue = $imageMap[$oldValue];
            $updatedCount++;
            return "'image_url' => '{$newValue}'";
        } else {
            $notFoundCount++;
            $notFound[] = $oldValue;
            // Keep old value if no match found
            return $matches[0];
        }
    },
    $oldSeederContent
);

// Write the new seeder
$newSeederFile = 'database/seeders/VolcanoesTableSeeder.php';
file_put_contents($newSeederFile, $newSeederContent);

echo "✓ Generated new seeder: {$newSeederFile}\n";
echo "\n";
echo "📊 Summary:\n";
echo "  - Updated: {$updatedCount} image URLs\n";
echo "  - Not found: {$notFoundCount} image URLs\n";

if ($notFoundCount > 0) {
    echo "\n⚠️  Images not found in repository:\n";
    foreach (array_slice($notFound, 0, 10) as $missing) {
        echo "  - {$missing}\n";
    }
    if ($notFoundCount > 10) {
        echo "  ... and " . ($notFoundCount - 10) . " more\n";
    }
    echo "\nThese will need to be manually checked or images added to GitHub.\n";
}

echo "\n✅ Done! New seeder ready at: {$newSeederFile}\n";
