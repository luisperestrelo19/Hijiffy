<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        return $query->when($request->input('property_id'), fn ($q, $search) => $q->whereLikeInsensitive('properties.code', $search))
            ->when($request->input('number_of_guests'), function ($q, $search) {
                //this way i can only get properties that have rooms with the exact number of guests
                $q->with(['rooms' => fn ($q) => $q->where('rooms.max_guests', '>=', $search)])
                    ->whereHas('rooms', fn ($q) => $q->where('rooms.max_guests', '>=', $search));
            })
            ->when($request->input('check_in') && $request->input('check_out'), function ($query) use ($request) {
                $checkIn  = $request->input('check_in');
                $checkOut = $request->input('check_out');
                $days     = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

                $query->whereHas('rooms.availabilities', fn ($q) => $q->betweenDates($checkIn, $checkOut))
                    ->with([
                        'rooms' => function ($roomQuery) use ($checkIn, $checkOut, $days) {
                            $roomQuery->whereHas('availabilities', function ($q) use ($checkIn, $checkOut) {
                                $q->betweenDates($checkIn, $checkOut)->whereNull('deleted_at');
                            })
                                //count the number of distinct available dates in the range
                                ->withCount([
                                    'availabilities as available_dates_count' => fn ($q) => $q->select(DB::raw('COUNT(DISTINCT date)'))
                                        ->betweenDates($checkIn, $checkOut),
                                ])
                                ->with([
                                    'availabilities' => fn ($q) => $q->betweenDates($checkIn, $checkOut)->orderBy('date'),
                                ])
                                //compare the available_dates_count with the number of days in the range
                                ->having('available_dates_count', '=', $days + 1);
                        },
                    ]);
            });
    }
}
