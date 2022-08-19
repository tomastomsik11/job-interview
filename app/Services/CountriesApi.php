<?php

namespace App\Services;

use App\Exceptions\CountriesApiException;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Service class allows a retrieval of information about countries
 *
 * @see https://datahelpdesk.worldbank.org/knowledgebase/articles/898581-api-basic-call-structures
 * @package App\Services
 */
final class CountriesApi
{
    /**
     * URL for API
     *
     * @var string
     */
    protected const URL = 'https://api.worldbank.org/v2/country/all/indicators/SP.POP.TOTL';

    /**
     * Microseconds for slow down api requests
     *
     * @var int
     */
    protected const SLEEP_REQUEST_US = 100_000;

    /**
     * rows per page
     *
     * @var int
     */
    protected const PER_PAGE = 1000;

    /**
     * actual page
     *
     * @var int
     */
    private int $page = 1;

    /**
     * page limit
     * @var int|null
     */
    private ?int $page_limit;

    /**
     * CountriesApi constructor.
     * @param int|null $page_limit
     */
    public function __construct(?int $page_limit = null)
    {
        $this->page_limit = $page_limit;
    }

    /**
     *
     * Makes recursive request to Worldbank API endpoint and save/update data to the database
     *
     * @throws CountriesApiException
     */
    public function updateCountries(): void
    {
        $response_data = $this->getDataFromApi();

        $this->processData($response_data);

        if ((isset($response_data[0]['pages'], $response_data[0]['page'])
                && $response_data[0]['pages'] === $response_data[0]['page'])
            || ($this->page_limit !== null && $this->page_limit === $this->page)) {
            //we are on the last page
            return;
        }

        $this->page++;

        $this->updateCountries();
    }

    /**
     * Get data From Worldbank API
     *
     * @return array
     * @throws CountriesApiException
     */
    private function getDataFromApi(): array
    {
        $response = Http::get(self::URL, [
            'format'   => 'json',
            'per_page' => self::PER_PAGE,
            'page'     => $this->page,
        ]);

        if ($response->failed()) {
            throw new CountriesApiException('Get request failed.');
        }

        usleep(self::SLEEP_REQUEST_US);

        return $response->json();
    }


    /**
     * Process data, check and save it to the database
     *
     * @param array $data
     * @throws CountriesApiException
     */
    private function processData(array $data): void
    {
        if (!isset($data[1])) {
            throw new CountriesApiException('Countries data not found in response.');
        }

        $countries       = $data[1];
        $exist_countries = Country::whereIn('api_id', array_unique(Arr::pluck($countries, 'country.id')))->get();

        foreach ($countries as $country) {
            $exist_country = $this->searchCountryInCollection($exist_countries, $country);

            if (isset($exist_country['is_new_country']) && $exist_country['is_new_country']) {
                $exist_countries->push($exist_country['country']);
            }

            if ($exist_country['country']->Populations()->where('year', $country['date'])->get()->isEmpty()) {
                $this->createNewPopulation($exist_country['country'], $country);
            }
        }
    }

    /**
     * Search or create country in database
     *
     * @param Collection $exist_country
     * @param array $country
     * @return array<string, mixed>
     * @throws CountriesApiException
     */
    private function searchCountryInCollection(Collection $exist_country, array $country): array
    {
        $is_new_country = false;

        $exist_country->search(function (Country $item) use ($country) {
            if ((int) $country['country']['id'] !== $item->getAttribute('api_id')) {
                return null;
            }

            return $item;
        });

        if ($exist_country->isEmpty()) {
            $result_country = $this->createNewCountry($country);
            $is_new_country = true;
        } else {
            $result_country = $exist_country[0];
        }

        return [
            'country'        => $result_country,
            'is_new_country' => $is_new_country
        ];
    }

    /**
     * Create new population row
     *
     * @param Country $country
     * @param array $country_population<Country, array>
     * @throws CountriesApiException
     */
    private function createNewPopulation(Country $country, array $country_population): void
    {
        $population = $country->Populations()->create([
            'year' => $country_population['date'],
            'population' => $country_population['value'],
        ]);

        if ($population === null) {
            throw new CountriesApiException('Country population create failed.');
        }
    }

    /**
     * Create new country
     *
     * @param array $country
     * @return Country
     * @throws CountriesApiException
     */
    private function createNewCountry(array $country): Country
    {
        $new_country = Country::create([
            'name'   => $country['country']['value'],
            'api_id' => $country['country']['id'],
            'iso'    => $country['countryiso3code'],
        ]);

        if ($new_country === null) {
            throw new CountriesApiException('Country create failed.');
        }

        return $new_country;
    }
}
