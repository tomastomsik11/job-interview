<?php

namespace App\Models;

/**
 * Class CountryPopulation
 * @package App\Models
 */
final class CountryPopulation extends BaseModel
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'country_id',
        'year',
        'population',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'country_id' => 'string',
        'api_id' => 'string',
        'iso' => 'string',
    ];
}
