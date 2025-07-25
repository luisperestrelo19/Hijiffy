<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'code',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function scopeSearch($query, $request)
    {
        $query->when($request->input('property_id'), fn ($q, $search) => $q->where('code', $search))
            ->when($request->input('check_in'), fn ($q, $search) => $q->whereHas('rooms.availabilities', fn ($q) => $q->whereDate('date', $search)))
            ->when($request->input('check_out'), fn ($q, $search) => $q->whereHas('rooms.availabilities', fn ($q) => $q->whereDate('date', $search)))
            ->when($request->input('number_of_guests'), fn ($q, $search) => $q->whereHas('rooms', fn ($q) => $q->where('max_guests', $search)));

        return $query;
    }
}
