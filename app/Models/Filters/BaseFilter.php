<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class BaseFilter
 * @package App\Models\Filters
 */
abstract class BaseFilter
{
    /**
     * @var Builder
     */
    public Builder $query;

    /**
     * BaseFilter constructor.
     *
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * @param string $param
     * @return array
     */
    public function handleFilterParam(string $param): array
    {
        return explode(',', $param);
    }
}
