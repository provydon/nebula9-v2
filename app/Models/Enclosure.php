<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enclosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    // Query Scopes
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->withCount('animals')
            ->havingRaw('capacity > animals_count');
    }

    public function scopeFull(Builder $query): Builder
    {
        return $query->withCount('animals')
            ->havingRaw('capacity <= animals_count');
    }

    // Accessors - use withCount for better performance
    public function getCurrentOccupancyAttribute(): int
    {
        return $this->animals_count ?? $this->animals()->count();
    }

    public function getAvailableSpaceAttribute(): int
    {
        return max(0, $this->capacity - $this->current_occupancy);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->current_occupancy >= $this->capacity;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->current_occupancy < $this->capacity;
    }
}
