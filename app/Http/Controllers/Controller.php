<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Throwable;

abstract class Controller
{
  public function __construct(
    public string $modelClass
  ) {
  }

  public const PaginationCount = 15;

  public function search(Request $request, Builder $builder)
  {
  }

  public function list(Request $request)
  {
    $builder = $this->modelClass::query();

    $this->search($request, $builder);

    if ($request->integer('per_page') > 100) {
      $request->perPage = 100;
    }

    if ($request->integer('per_page') < 1) {
      $request->perPage = 1;
    }

    return $builder->paginate(
      perPage: $request->input('per_page', self::PaginationCount),
      pageName: $request->input('page', 1),
    );
  }


  public function details(Request $request): JsonResponse
  {
    $model = $this->modelClass::find($request->route('id'));
    if (!$model) {
      return response()->json([
        'message' => 'Item não encontrado'
      ], 404);
    }
    return response()->json($model);
  }

  public function createFunc(Request $request): JsonResponse
  {
    $model = new $this->modelClass($request->all());
    try {
      DB::transaction(function () use ($model) {
        $model->save();
      });
      return response()->json([
        'message' => 'Item criado com sucesso',
        'data' => $model
      ]);
    } catch (Throwable $e) {
      Log::error($e->getMessage());
      return response()->json([
        'message' => 'Falha ao criar item, tente mais tarde',
      ], 500);
    }
  }

  public function updateFunc(Request $request): JsonResponse
  {
    $model = $this->modelClass::find($request->route('id'));
    try {
      DB::transaction(function () use ($model, $request) {
        $model->update($request->all());
      });
      return response()->json([
        'message' => 'Item atualizado com sucesso',
        'data' => $model
      ]);
    } catch (Exception $e) {
      Log::error($e);
      return response()->json([
        'message' => 'Falha ao atualizar item, tente mais tarde',
      ], 500);
    }
  }

  public function delete(Request $request): JsonResponse
  {
    $model = $this->modelClass::find($request->route('id'));
    try {
      DB::transaction(function () use ($model) {
        $model->delete();
      });
      return response()->json([
        'message' => 'Item apagado com sucesso',
      ]);
    } catch (Exception $e) {
      Log::error($e);
      return response()->json([
        'message' => 'Falha ao apagar item',
      ], 500);
    }
  }
}