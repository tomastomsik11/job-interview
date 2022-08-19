<?php

namespace App\Models\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class CountryFilter
 * @package App\Models\Filters
 */
final class CountryFilter extends BaseFilter
{
    /**
     * @param array $options
     * @return Builder
     */
    public function handle(array $options): Builder
    {
        if (isset($options['iso']) && $options['iso'] !== null) {
            $param = $this->handleFilterParam($options['iso']);

            $this->query->whereIn('iso', $param);
        }

        return $this->query;
    }
}
