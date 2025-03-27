<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Client;
use App\Repo\ProductRepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ProductController extends Controller
{
  public int $paginationCount = 15;

  public function __construct(
    public ProductRepo $productRepo
  ) {
  }

  public function list(Request $request): JsonResponse
  {
    $builder = Client::query();

    if ($name = $request->query('name')) {
      $builder->where('name', 'like', "%$name%");
    }

    if ($description = $request->query('description')) {
      $builder->where('description', 'like', "%$description%");
    }

    if ($code = $request->query('code')) {
      $builder->where('code', 'like', "%$code%");
    }

    return response()->json(
      $this->preparePagination($request, $builder)
    );
  }

  public function details(Request $request): JsonResponse
  {
    return response()->json($request->product);
  }

  public function create(ProductCreateRequest $request): JsonResponse
  {
    try {
      $model = $this->productRepo->create(
        $request->safe()->all()
      );
      return response()->json([
        'message' => 'Produto adicionado com sucesso',
        'data' => $model->toArray(),
      ]);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'Falha ao adicionar produto',
      ], 500);
    }
  }

  public function update(ProductUpdateRequest $request): JsonResponse
  {
    try {
      $model = $this->productRepo->update(
        $request->client->id,
        $request->safe()->all()
      );
      return response()->json([
        'message' => 'Produto atualizado com sucesso',
        'data' => $model->toArray(),
      ]);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'Falha ao atualizar produto',
      ], 500);
    }
  }

  public function delete(Request $request): JsonResponse
  {
    try {
      $this->productRepo->delete(
        $request->client->id,
      );
      return response()->json([
        'message' => 'Produto deletado com sucesso',
      ]);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'Falha ao deletar produto',
      ], 500);
    }
  }
}
