<?php

declare(strict_types=1);

namespace App\Models\Traits;

trait Searchable
{
    abstract public function scopeSearch($query, $queryParams);
}
