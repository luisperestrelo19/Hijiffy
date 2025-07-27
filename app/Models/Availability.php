<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'date',
        'room_id',
    ];

    public function scopeBetweenDates($query, $checkIn, $checkOut)
    {
        return $query->whereBetween('date', [$checkIn, $checkOut]);
    }
}
