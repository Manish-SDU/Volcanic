<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Volcano;

class VolcanoSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the database with some test volcanoes
        Volcano::create([
            'name' => 'Mount Fuji',
            'country' => 'Japan',
            'continent' => 'Asia',
            'activity' => 'Active',
            'type' => 'Stratovolcano',
            'elevation' => 3776,
            'latitude' => 35.36,
            'longitude' => 138.72,
            'description' => 'Mount Fuji is an active volcano about 100 kilometers southwest of Tokyo.',
            'image_url' => 'fuji.jpg'
        ]);

        Volcano::create([
            'name' => 'Mount Etna',
            'country' => 'Italy',
            'continent' => 'Europe',
            'activity' => 'Active',
            'type' => 'Stratovolcano',
            'elevation' => 3329,
            'latitude' => 37.75,
            'longitude' => 14.99,
            'description' => 'Mount Etna is an active stratovolcano on the east coast of Sicily, Italy.',
            'image_url' => 'etna.jpg'
        ]);

        Volcano::create([
            'name' => 'Mauna Loa',
            'country' => 'United States',
            'continent' => 'North America',
            'activity' => 'Active',
            'type' => 'Shield',
            'elevation' => 4169,
            'latitude' => 19.47,
            'longitude' => -155.60,
            'description' => 'Mauna Loa is one of five volcanoes that form the Island of Hawaii.',
            'image_url' => 'maunaloa.jpg'
        ]);
    }

    /** @test */
    public function test_exact_match_searching_for_mount_fuji_returns_the_correct_volcano()
    {
        $response = $this->getJson('/api/volcanoes/search?query=Mount Fuji');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mount Fuji');
    }

    /** @test */
    public function test_partial_match_searching_for_fuji_also_returns_mount_fuji()
    {
        $response = $this->getJson('/api/volcanoes/search?query=Fuji');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mount Fuji');
    }

    /** @test */
    public function test_case_insensitivity_searching_for_mount_fuji_lowercase_works_correctly()
    {
        $response = $this->getJson('/api/volcanoes/search?query=mount fuji');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mount Fuji');
    }

    /** @test */
    public function test_stop_word_handling_searching_for_the_mount_fuji_ignores_stop_words()
    {
        // "Mount" is a stop word, so searching for "Mount Fuji" should effectively search for "Fuji"
        // But let's try a case where the stop word is the only difference or part of the query.
        // The controller logic removes stop words.
        // If I search for "The Mount Fuji", "The" and "Mount" are stop words.
        
        $response = $this->getJson('/api/volcanoes/search?query=The Mount Fuji');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mount Fuji');
    }

    /** @test */
    public function test_country_acronyms_searching_for_jp_correctly_finds_volcanoes_in_japan()
    {
        // "JP" maps to "Japan"
        $response = $this->getJson('/api/volcanoes/search?query=JP');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mount Fuji');
    }

    /** @test */
    public function test_full_country_names_searching_for_italy_finds_volcanoes_in_italy()
    {
        $response = $this->getJson('/api/volcanoes/search?query=Italy');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mount Etna');
    }

    /** @test */
    public function test_broad_queries_searching_for_active_returns_all_active_volcanoes()
    {
        // "Active" is in the activity field for all 3 seeded volcanoes
        $response = $this->getJson('/api/volcanoes/search?query=Active');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function test_error_handling_verifies_that_missing_queries_return_an_appropriate_error_message()
    {
        $response = $this->getJson('/api/volcanoes/search');

        $response->assertStatus(200)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Query parameter is required');
    }

    /** @test */
    public function test_single_volcano_retrieval_fetching_a_single_volcano_by_id_works()
    {
        $volcano = Volcano::first();

        $response = $this->getJson("/api/volcanoes/{$volcano->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('volcano.id', $volcano->id)
            ->assertJsonPath('volcano.name', $volcano->name);
    }

    /** @test */
    public function test_single_volcano_retrieval_handles_non_existent_ids_correctly()
    {
        $response = $this->getJson("/api/volcanoes/99999");

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error', 'Volcano not found');
    }

    /** @test */
    public function test_api_returns_fields_needed_for_frontend_recommendations()
    {
        // The frontend uses 'name' and 'country' to generate recommendation chips.
        // This test ensures those fields are always present in the response.
        $response = $this->getJson('/api/volcanoes/search?query=Fuji');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'country',
                        'activity',
                        'type'
                    ]
                ]
            ]);
    }
}
