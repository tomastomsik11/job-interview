<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountriesPaginateResource;
use App\Repositories\CountryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CountryController
 * @package App\Http\Controllers
 */
final class CountryController extends Controller
{
    /**
     * CountryController constructor.
     * @param CountryRepository $countryRepository
     */
    public function __construct(protected CountryRepository $countryRepository)
    {
    }


    /**
     * Get all countries
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return response()->json([
            'data' => $this->countryRepository->all(),
        ]);
    }

    /**
     * Paginator for countries
     * @param Request $request
     * @return JsonResponse
     */
    public function paginator(Request $request): JsonResponse
    {
        return response()->json(CountriesPaginateResource::make($this->countryRepository->paginate($request->all())));
    }
}
