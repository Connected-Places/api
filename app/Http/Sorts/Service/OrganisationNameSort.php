<?php

namespace App\Http\Sorts\Service;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class OrganisationNameSort implements Sort
{
    public function __invoke(Builder $query, $descending, string $property): Builder
    {
        $descending = $descending ? 'DESC' : 'ASC';

        $subQuery = DB::table('organisations')
            ->select('organisations.name')
            ->whereRaw('`services`.`organisation_id` = `organisations`.`id`')
            ->take(1);

        return $query->orderByRaw("({$subQuery->toSql()}) $descending", $subQuery->getBindings());
    }
}
