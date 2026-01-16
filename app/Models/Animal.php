<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specie',
        'preferred_environment',
        'enclosure_id',
    ];

    public function enclosure(): BelongsTo
    {
        return $this->belongsTo(Enclosure::class);
    }
}
