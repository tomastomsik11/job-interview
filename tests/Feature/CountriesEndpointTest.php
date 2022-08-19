<?php

namespace Tests\Feature;

use App\Models\Country;
use Illuminate\Http\Response;
use Tests\TestCase;
use Throwable;

/**
 * Class CountriesEndpointTest
 * @package Tests\Feature
 */
class CountriesEndpointTest extends TestCase
{
    /**
     * @var array
     */
    protected const DATA_MOCKUP = [
        [
            'name' => 'World',
            'api_id' => '1W',
            'iso' => 'WLD',
            'populations' => [
                2021 => 1000000,
                2022 => 1100000,
            ],
        ],
        [
            'name' => 'Bangladesh',
            'api_id' => 'BD',
            'iso' => 'BGD',
            'populations' => [
                2021 => 1000000,
                2022 => 1100000,
            ],
        ],
    ];

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
        $this->runManualSeed();
    }

    /**
     * Check status and structure of endpoint
     */
    public function test_endpoint(): void
    {
        $response = $this->json('get', '/api/v1/countries');
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'current_page',
            'data' => [
                0 => [
                    'id',
                    'name',
                    'iso',
                    'api_id',
                    'populations' => [
                        0 => [
                            'year',
                            'population',
                        ],
                    ],
                ],
            ],
            'first_page_url',
            'from',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
        ]);
    }

    /**
     * Check if filters is working
     * @throws Throwable
     */
    public function test_filter_endpoint(): void
    {
        $response = $this->json('get', '/api/v1/countries?filters[iso]=WLD');
        $data     = $response->decodeResponseJson();

        $this->assertEquals('WLD', $data['data'][0]['iso']);
    }

    /**
     * Manual seed for db
     */
    private function runManualSeed(): void
    {
        foreach (self::DATA_MOCKUP as $item) {
            $country = Country::create([
                'name'   => $item['name'],
                'iso'    => $item['iso'],
                'api_id' => $item['api_id'],
            ]);

            foreach ($item['populations'] as $year => $population) {
                $country->Populations()->create([
                    'year' => $year,
                    'population' => $population,
                ]);
            }
        }
    }
}
