<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait FilterColumn
{
    public function includeFilters()
    {
        $filter = request()->query('filter');
        if (!$filter) {
            return false;
        }
        return $filter;
    }

    public function canLoadFilter(EloquentBuilder|QueryBuilder|Model $model, $column): Model|EloquentBuilder|QueryBuilder
    {
        $filter = $this->includeFilters();
        if ($filter) {
            $model->where($column, $filter)->latest();
        }
        return $model;
    }
}

