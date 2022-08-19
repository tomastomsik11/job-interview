<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;

/**
 * Class BaseRepository
 * @package App\Repositories
 */
abstract class BaseRepository
{
    /**
     * Repository constructor.
     *
     * @param Model $model
     */
    public function __construct(protected Model $model)
    {
    }

    /**
     * Get all rows
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model::all();
    }

    /**
     * Paginate collection with $options
     * @param array $options
     * @return Paginator
     */
    public function paginate(array $options): Paginator
    {
        $defaults = [
            'per_page' => 5,
            'filters'  => [],
        ];

        $options = array_merge($defaults, $options);
        $query   = $this->model->paginateFilters($options['filters'], $this->model::with('Populations'));

        return $query->simplePaginate($options['per_page']);
    }
}
