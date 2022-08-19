<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CountriesPaginateResource
 * @package App\Http\Resources
 */
final class CountriesPaginateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $paginator = $this->resource->toArray();

        $result = [
            'current_page'   => $paginator['current_page'],
            'data'           => [],
            'first_page_url' => $paginator['first_page_url'],
            'from'           => $paginator['from'],
            'next_page_url'  => $paginator['next_page_url'],
            'path'           => $paginator['path'],
            'per_page'       => $paginator['per_page'] ?? 1,
            'prev_page_url'  => $paginator['prev_page_url'],
            'to'             => $paginator['to'],
        ];

        foreach ($paginator['data'] as $item) {
            $new_item = [
                'id'     => $item['id'],
                'name'   => $item['name'],
                'api_id' => $item['api_id'],
                'iso'    => $item['iso'],
                'populations' => [],
            ];

            foreach ($item['populations'] as $population) {
                $new_item['populations'][] = [
                    'year'       => $population['year'],
                    'population' => $population['population'],
                ];
            }

            $result['data'][] = $new_item;
        }

        return $result;
    }
}
