<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Repo\ClientRepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ClientController extends Controller
{
    public int $paginationCount = 15;

    public function __construct(
        public ClientRepo $clientRepo
    ) {}

    public function list(Request $request): JsonResponse
    {
        $builder = Client::query();

        if ($name = $request->query('name')) {
            $builder->where('name', 'like', "%$name%");
        }

        if ($phone = $request->query('phone')) {
            $builder->where('phone', 'like', "%$phone%");
        }

        $paginatorCount = $this->paginationCount;

        if ($perPage = $request->query('per_page')) {
            if (is_numeric($perPage) && intval($perPage) >= 5 && intval($perPage) <= 100) {
                $paginatorCount = intval($perPage);
            }
        }

        return response()->json(
            $builder->paginate($paginatorCount)
        );
    }

    public function details(Request $request): JsonResponse
    {
        return response()->json($request->client);
    }

    public function create(ClientCreateRequest $request): JsonResponse
    {
        try {
            $model = $this->clientRepo->create(
                $request->safe()->all()
            );
            return response()->json([
                'message' => 'Cliente criada com sucesso',
                'data' => $model->toArray(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar cliente',
            ], 500);
        }
    }

    public function update(ClientUpdateRequest $request): JsonResponse
    {
        try {
            $model = $this->clientRepo->update(
                $request->client->id,
                $request->safe()->all()
            );
            return response()->json([
                'message' => 'Cliente atualizada com sucesso',
                'data' => $model->toArray(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar cliente',
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->clientRepo->delete(
                $request->client->id,
            );
            return response()->json([
                'message' => 'Cliente deletada com sucesso',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha ao criar cliente',
            ], 500);
        }
    }
}
