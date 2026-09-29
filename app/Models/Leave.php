<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment',
        'status',
        'rejection_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if ($this->attachment && Storage::disk('public')->exists($this->attachment)) {
            return asset('storage/' . $this->attachment);
        }
        return null;
    }

    public function getDurationDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 1;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getIsImageAttachmentAttribute(): bool
    {
        if (!$this->attachment) {
            return false;
        }
        $ext = strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }

    public function getIsPdfAttachmentAttribute(): bool
    {
        if (!$this->attachment) {
            return false;
        }
        $ext = strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }
}

