<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius_meters',
    ];

    /**
     * Get the active/first office setting or default.
     */
    public static function getActiveSetting(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Kantor Pusat',
                'latitude' => -6.9217000,
                'longitude' => 107.6071000,
                'radius_meters' => 50,
            ]
        );
    }
}
