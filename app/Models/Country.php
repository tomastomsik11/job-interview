<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 * @package App\Models
 */
final class Country extends BaseModel
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'api_id',
        'iso',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'string',
        'api_id' => 'string',
        'iso' => 'string',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return HasMany
     */
    public function Populations(): HasMany
    {
        return $this->hasMany(CountryPopulation::class)->orderBy('year');
    }
}
