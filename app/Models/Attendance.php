<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'date',
        'time_in',
        'time_out',
        'photo_in',
        'photo_out',
        'lat_in',
        'long_in',
        'lat_out',
        'long_out',
        'status',
        'distance_in_meters',
    ];

    protected $casts = [
        'date' => 'date',
        'lat_in' => 'float',
        'long_in' => 'float',
        'lat_out' => 'float',
        'long_out' => 'float',
        'distance_in_meters' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getPhotoInUrlAttribute(): ?string
    {
        if ($this->photo_in && Storage::disk('public')->exists($this->photo_in)) {
            return asset('storage/' . $this->photo_in);
        }
        return null;
    }

    public function getPhotoOutUrlAttribute(): ?string
    {
        if ($this->photo_out && Storage::disk('public')->exists($this->photo_out)) {
            return asset('storage/' . $this->photo_out);
        }
        return null;
    }
}
