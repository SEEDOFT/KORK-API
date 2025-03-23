<?php

namespace App\Http\Traits;

trait FilterColumn
{
    public function includeFilters(string $filter)
    {
        $filter = request()->query('filter');
        if (!$filter) {
            return false;
        }

        $filters = array_map('trim', explode(',', $filter));

        return in_array($filter, $filters);
    }

    public function canLoadFilter(){

    }
}

