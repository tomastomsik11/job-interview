<?php

namespace Tests\Feature;

use App\Exceptions\CountriesApiException;
use App\Models\Country;
use App\Services\CountriesApi;
use Tests\TestCase;

/**
 * Class CountriesApiTest
 * @package Tests\Feature
 */
final class CountriesApiTest extends TestCase
{
    /**
     * const for check if ISO exist after update countries
     * @var string
     */
    protected const ASSERT_ISO = 'AFE';

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
    }

    /**
     * Test for Countries Api Service
     *
     * @return void
     * @throws CountriesApiException
     */
    public function test_service(): void
    {
        (new CountriesApi(1))->updateCountries();

        $this->assertDatabaseHas(Country::class, [
            'iso' => self::ASSERT_ISO,
        ]);
    }
}
