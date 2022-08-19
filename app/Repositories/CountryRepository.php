<?php

namespace App\Repositories;

use App\Models\Country;

/**
 * Class CountryRepository
 * @package App\Repositories
 */
final class CountryRepository extends BaseRepository
{
    /**
     * CountryRepository constructor.
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }
}
