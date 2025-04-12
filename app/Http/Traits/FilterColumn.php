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
        $filterType = '';
        if ($filter == "តន្ត្រី") {
            $filterType = 'concert';
        } elseif ($filter == "ហ្គេម") {
            $filterType = 'game';
        } elseif ($filter == "ម៉ូដ") {
            $filterType = 'fashion';
        } elseif ($filter == "កីឡា") {
            $filterType = 'sport';
        } elseif ($filter == "ការច្នៃប្រឌិត") {
            $filterType = 'innovation';
        } else {
            $filterType = $filter;
        }
        return $filterType ?: false;
    }

    public function includeSearch()
    {
        $search = request()->query('search');
        return $search ?: false;
    }

    public function includeMinPrice()
    {
        $minPrice = request()->query('min_price');
        if (!$minPrice) {
            return false;
        }
        return $minPrice;
    }

    public function includeMaxPrice()
    {
        $maxPrice = request()->query('max_price');
        if (!$maxPrice) {
            return false;
        }
        return $maxPrice;
    }


    public function includeDateRange(): ?string
    {
        $date = request()->query('date');
        return $date ?: null;
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

    public function applyPriceRange(EloquentBuilder|QueryBuilder|Model $model, $relationship = null, $column = null): Model|EloquentBuilder|QueryBuilder
    {
        $min = $this->includeMinPrice();
        $max = $this->includeMaxPrice();

        if (($min || $max) && isset($min, $max)) {
            return $model->whereHas($relationship, function ($query) use ($min, $max, $column) {
                $query->whereBetween($column, [
                    (float) $min,
                    (float) $max,
                ]);
            });
        }
        return $model;
    }


    public function applyDateRange(EloquentBuilder|QueryBuilder|Model $model, string $column)
    {
        $date = $this->includeDateRange();
        $result = $model;

        if ($date) {
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            if ($date === 'today') {
                $result = $model->whereDate($column, '=', $today);
            } elseif ($date === 'tomorrow') {
                $result = $model->whereDate($column, '=', $tomorrow);
            } elseif ($date === 'this_week') {
                $result = $model->whereBetween($column, [$startOfWeek, $endOfWeek]);
            } else {
                $result = $model->whereDate($column, '=', $date);
            }
        }

        return $result;
    }

}

