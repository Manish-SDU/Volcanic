<?php

namespace App\Support;

class CountryAcronymMapper
{
    /**
     * Mapping of country acronyms to full country names
     * Includes common abbreviations and ISO codes
     */
    private static $countryMap = [
        // North America
        'usa' => 'United States',
        'us' => 'United States',
        'america' => 'United States',
        'states' => 'United States',
        'canada' => 'Canada',
        'ca' => 'Canada',
        'mexico' => 'Mexico',
        'mx' => 'Mexico',
        'guatemala' => 'Guatemala',
        'gt' => 'Guatemala',
        
        // Europe
        'uk' => 'United Kingdom',
        'britain' => 'United Kingdom',
        'england' => 'United Kingdom',
        'gb' => 'United Kingdom',
        'italy' => 'Italy',
        'it' => 'Italy',
        'france' => 'France',
        'fr' => 'France',
        'germany' => 'Germany',
        'de' => 'Germany',
        'spain' => 'Spain',
        'es' => 'Spain',
        'iceland' => 'Iceland',
        'is' => 'Iceland',
        'turkey' => 'Turkey',
        'tr' => 'Turkey',
        'russia' => 'Russia',
        'ru' => 'Russia',
        'greece' => 'Greece',
        'gr' => 'Greece',
        
        // Asia
        'japan' => 'Japan',
        'jp' => 'Japan',
        'china' => 'China',
        'cn' => 'China',
        'india' => 'India',
        'in' => 'India',
        'indonesia' => 'Indonesia',
        'id' => 'Indonesia',
        'philippines' => 'Philippines',
        'ph' => 'Philippines',
        'south korea' => 'South Korea',
        'korea' => 'South Korea',
        'kr' => 'South Korea',
        'thailand' => 'Thailand',
        'th' => 'Thailand',
        'vietnam' => 'Vietnam',
        'vn' => 'Vietnam',
        
        // South America
        'chile' => 'Chile',
        'cl' => 'Chile',
        'brazil' => 'Brazil',
        'br' => 'Brazil',
        'argentina' => 'Argentina',
        'ar' => 'Argentina',
        'peru' => 'Peru',
        'pe' => 'Peru',
        'colombia' => 'Colombia',
        'co' => 'Colombia',
        'ecuador' => 'Ecuador',
        'ec' => 'Ecuador',
        'bolivia' => 'Bolivia',
        'bo' => 'Bolivia',
        
        // Oceania
        'australia' => 'Australia',
        'au' => 'Australia',
        'new zealand' => 'New Zealand',
        'nz' => 'New Zealand',
        
        // Africa
        'south africa' => 'South Africa',
        'za' => 'South Africa',
        'kenya' => 'Kenya',
        'ke' => 'Kenya',
        'tanzania' => 'Tanzania',
        'tz' => 'Tanzania',
        'ethiopia' => 'Ethiopia',
        'et' => 'Ethiopia',
        
        // Special cases
        'pacific' => 'Pacific Ocean',
        'ocean' => 'Pacific Ocean',
        'pacific ocean' => 'Pacific Ocean',
        'atlantic' => 'Atlantic Ocean',
        'atlantic ocean' => 'Atlantic Ocean',
    ];
    
    /**
     * Get possible country names for a search term
     * Returns an array of potential country matches
     */
    public static function getCountryMatches(string $searchTerm): array
    {
        $searchTerm = strtolower(trim($searchTerm));
        $matches = [];
        
        // Direct acronym match
        if (isset(self::$countryMap[$searchTerm])) {
            $matches[] = self::$countryMap[$searchTerm];
        }
        
        // Only do exact acronym matches - no partial matches
        foreach (self::$countryMap as $acronym => $country) {
            if ($acronym === $searchTerm) {
                if (!in_array($country, $matches)) {
                    $matches[] = $country;
                }
            }
        }
        
        // If the search term is a country name (value), include it
        if (in_array($searchTerm, self::$countryMap)) {
            $matches[] = $searchTerm;
        }
        
        return array_unique($matches);
    }
    
    /**
     * Check if a search term might be a country acronym
     */
    public static function isLikelyCountryAcronym(string $searchTerm): bool
    {
        $searchTerm = strtolower(trim($searchTerm));
        
        // Check if it's in our mapping
        if (isset(self::$countryMap[$searchTerm])) {
            return true;
        }
        
        // Check if it's a short term that might be an acronym
        if (strlen($searchTerm) <= 3 && preg_match('/^[a-z]+$/', $searchTerm)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get all available country mappings (for debugging)
     */
    public static function getAllMappings(): array
    {
        return self::$countryMap;
    }
}