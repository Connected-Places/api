<?php

namespace App\Http\Filters\Service;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filters\Filter;

class OrganisationNameFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Don't treat comma's as an array separator.
        $value = implode(',', Arr::wrap($value));

        return $query->whereHas('organisation', function (Builder $query) use ($value) {
            $query->where('organisations.name', 'LIKE', "%{$value}%");
        });
    }
}
