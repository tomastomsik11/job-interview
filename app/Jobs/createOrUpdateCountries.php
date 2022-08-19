<?php

namespace App\Jobs;

use App\Exceptions\CountriesApiException;
use App\Services\CountriesApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class getAndUpdateCountries
 * @package App\Jobs
 */
final class createOrUpdateCountries implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws CountriesApiException
     */
    public function handle(): void
    {
        (new CountriesApi())->updateCountries();
    }
}
