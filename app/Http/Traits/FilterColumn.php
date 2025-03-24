<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

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

    public function includePriceRange()
    {
        $price = request()->query('price');
        if (!$price) {
            return false;
        }
        $allPrice = array_map('trim', explode(',', $price));

        $filteredPrice = array_filter($allPrice, 'is_numeric');

        if (empty($filteredPrice)) {
            return false;
        }

        return [
            'min' => min($filteredPrice),
            'max' => max($filteredPrice),
        ];
    }

    public function includeDateRange()
    {
        $date = request()->query('date');
        return $date ?: false;
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

    public function applyPriceRange(EloquentBuilder|QueryBuilder|Model $model): Model|EloquentBuilder|QueryBuilder
    {
        $priceRange = $this->includePriceRange();

        if ($priceRange) {
            $model->whereHas('tickets', function ($query) use ($priceRange) {
                $query->whereBetween('price', [$priceRange['min'], $priceRange['max']]);
            });
        }

        return $model;
    }


    public function applyDateRange(EloquentBuilder|QueryBuilder|Model $model, string $column): Model|EloquentBuilder|QueryBuilder
    {
        $date = $this->includeDateRange();

        if (!$date) {
            return $model;
        }

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        if ($date === 'today') {
            return $model->whereDate($column, '=', $today);
        } elseif ($date === 'tomorrow') {
            return $model->whereDate($column, '=', $tomorrow);
        } elseif ($date === 'this_week') {
            return $model->whereBetween($column, [$startOfWeek, $endOfWeek]);
        }

        return $model;
    }
}

