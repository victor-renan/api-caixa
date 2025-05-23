<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  public function __construct()
  {
    parent::__construct(Product::class);
  }

  public function search(Request $request, Builder $builder)
  {
    if ($name = $request->query('name')) {
      $builder->where('name', 'like', "%$name%");
    }

    if ($description = $request->query('description')) {
      $builder->where('description', 'like', "%$description%");
    }

    if ($code = $request->query('code')) {
      $builder->where('code', 'like', "%$code%");
    }
  }

  public function create(ProductCreateRequest $request): JsonResponse
  {
    return $this->createFunc($request);
  }

  public function update(ProductUpdateRequest $request): JsonResponse
  {
    return $this->updateFunc($request);
  }
}
