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
        return $filter ?: false;
    }

    public function includeSearch()
    {
        $search = request()->query('search');
        return $search ?: false;
    }

    public function applyFilter(EloquentBuilder|QueryBuilder|Model $model, $column): Model|EloquentBuilder|QueryBuilder
    {
        $filter = $this->includeFilters();
        if ($filter) {
            $model->where($column, $filter);
        }
        return $model;
    }

    public function applySearch(EloquentBuilder|QueryBuilder|Model $model, $column): Model|EloquentBuilder|QueryBuilder
    {
        $search = $this->includeSearch();
        if ($search) {
            $model->where($column, 'LIKE', '%' . $search . '%');
        }
        return $model;
    }
}

