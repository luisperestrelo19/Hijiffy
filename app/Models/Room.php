<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
       'code',
       'date',
       'max_guests',
       'price',
    ];

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }
}
