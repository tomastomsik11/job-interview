<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{
    /**
     * Custom paginate filters
     *
     * @param array $options
     * @param Builder|null $query
     * @return Builder
     */
    public function paginateFilters(array $options, Builder $query = null): Builder
    {
        if ($query === null) {
            $query = $this->newQuery();
        }

        $filter_class = '\App\Models\Filters\\' . class_basename(get_class($this)) . 'Filter';

        if (!class_exists($filter_class)) {
            return $query;
        }

        return (new $filter_class($query))->handle($options);
    }
}
