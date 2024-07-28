<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairRoom extends Model
{
    use HasFactory;

    protected $table = 'repair_tractors';

    protected $fillable = [
        'user_id',
        'tractor_id',
        'start_of_repair',
        'end_of_repair',
        'availability_date',
        'status',
    ];


    public function tractors(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
