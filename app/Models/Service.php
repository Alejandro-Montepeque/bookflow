<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'name',
    'slug',
    'description',
    'duration_minutes',
    'price_cents',
    'currency',
    'color',
    'buffer_minutes',
    'is_active',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'price_cents' => 'integer',
            'buffer_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // Auto-generate a URL-safe slug from the name when the caller did not set one.
    protected static function booted(): void
    {
        static::creating(function (Service $service): void {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    // Named `user` (not `owner`) to follow Laravel's relationship naming convention,
    // which lets factories use `->for($user)` without specifying the relation name.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(AvailabilityRule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // Convenience accessor used by the frontend to render prices without doing math in Vue.
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_cents / 100, 2).' '.$this->currency;
    }
}
