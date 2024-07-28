<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $table = 'tractors';

    protected $fillable = [
        'tractor_number',
        'user_id',
        'yard_id',
        'status',
        'maximum_capacity',
        'load_status'
    ];


    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id','users');
    }

    public function tractor_repairs(): HasMany
    {
        return $this->hasMany(RepairRoom::class);
    }

    public function tractor_status_updates(): HasMany
    {
        return $this->hasMany(TractorStatusUpdate::class);
    }
}
