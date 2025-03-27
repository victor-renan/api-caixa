<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Controller
{
  public const PaginationCount = 15;

  public function preparePagination(Request $request, Builder $builder)
  {
    $paginatorCount = self::PaginationCount;

    if ($perPage = $request->query('per_page')) {
      if (is_numeric($perPage) && intval($perPage) >= 5 && intval($perPage) <= 100) {
        $paginatorCount = intval($perPage);
      }
    }

    return $builder->paginate($paginatorCount);
  }
}