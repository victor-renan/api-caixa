<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCreateRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ServiceController extends Controller
{
  public function __construct(
    public ServiceService $serviceRepo
  ) {
  }

  public function list(Request $request): JsonResponse
  {
    $builder = Service::query();

    if ($name = $request->query('name')) {
      $builder->where('name', 'like', "%$name%");
    }

    if ($description = $request->query('description')) {
      $builder->where('description', 'like', "%$description%");
    }

    return response()->json(
      $this->preparePagination($request, $builder)
    );
  }

  public function details(Request $request): JsonResponse
  {
    return response()->json($request->service);
  }

  public function create(ServiceCreateRequest $request): JsonResponse
  {
    try {
      $model = $this->serviceRepo->create(
        $request->safe()->all()
      );
      return response()->json([
        'message' => 'Serviço adicionado com sucesso',
        'data' => $model->toArray(),
      ]);
    } catch (Exception) {
      return response()->json([
        'message' => 'Falha ao adicionar serviço',
      ], 500);
    }
  }

  public function update(ServiceUpdateRequest $request): JsonResponse
  {
    try {
      $model = $this->serviceRepo->update(
        $request->service->id,
        $request->safe()->all()
      );
      return response()->json([
        'message' => 'Serviço atualizado com sucesso',
        'data' => $model->toArray(),
      ]);
    } catch (Exception) {
      return response()->json([
        'message' => 'Falha ao atualizar serviço',
      ], 500);
    }
  }

  public function delete(Request $request): JsonResponse
  {
    try {
      $this->serviceRepo->delete(
        $request->service->id,
      );
      return response()->json([
        'message' => 'Serviço deletado com sucesso',
      ]);
    } catch (Exception) {
      return response()->json([
        'message' => 'Falha ao deletar serviço',
      ], 500);
    }
  }
}
