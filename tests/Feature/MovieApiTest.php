<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MovieApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function returns_movies_in_english_language()
    {
        //  Data preparation
        $movie = Movie::create([
            'external_id' => 100,
            'original_title' => 'Original English Title',
            'original_language' => 'en',
        ]);

        //  Added English translation
        $movie->languages()->create([
            'locale' => 'en',
            'title' => 'English Title',
            'overview' => 'English description here.',
        ]);

        //  Added Polish translation (for contrast)
        $movie->languages()->create([
            'locale' => 'pl',
            'title' => 'Polski Tytuł',
            'overview' => 'Polski opis.',
        ]);

        //  Executing a query with an English header
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
        ])->getJson('/api/movies');

        //  Results verification
        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'English Title',
                'locale' => 'en'
            ])
            ->assertJsonMissing([
                'title' => 'Polski Tytuł'
            ]);
    }
}
