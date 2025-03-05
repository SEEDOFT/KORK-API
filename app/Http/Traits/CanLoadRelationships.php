<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait CanLoadRelationships
{
    public function loadRelationship(
        Model|EloquentBuilder|QueryBuilder|HasMany|HasOne $for,
        ?array $relations = null
    ): Model|EloquentBuilder|QueryBuilder|HasMany|HasOne {

        $relations = $relations ?? $this->$relations ?? []; //check if relations is passed or not

        foreach ($relations as $relation) {
            $for->when(
                $this->includeRelations($relation),
                fn($q) => $for instanceof Model ? $for->load($relation) : $q->with($relation),
            );
        }

        return $for;
    }

    public function includeRelations(string $relation): bool
    {
        $include = request()->query('include');
        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }
}

