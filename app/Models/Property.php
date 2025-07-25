<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'code',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
