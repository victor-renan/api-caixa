<?php

namespace App\Http\Controllers;

use App\Helpers\Res;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;
use Exception;
use Log;
use Validator;

abstract class Controller
{
    public function __construct(
        protected Model $model,
    ) {
    }

    public array $joins = [];

    public function list(Request $request): JsonResponse
    {
        try {
            $query = $this->model
                ->newQuery()
                ->with($this->joins);

            $this->search($request, $query);

            return response()->json([
                'data' => $query->get(),
            ], 200);

        } catch (Exception $e) {

            Log::info($e->getMessage());
            
            return response()->json([
                'error' => 'Falha ao buscar dados no servidor',
            ], 500);
        }
    }

    public function details(Request $request)
    {
        $instance = $this->model
            ->with($this->joins)
            ->find($request->id);

        if (!$instance) {
            return response()->json([
                'error' => 'O item procurado não existe'
            ], 404);
        }

        return response()->json([
            'data' => $instance,
        ], 200);
    }

    public function create(Request $request)
    {
        return $this->createFunc($request);
    }

    public function update(Request $request)
    {
        return $this->updateFunc($request);
    }

    public function delete(Request $request)
    {
        return $this->deleteFunc($request);
    }

    public function createFunc(Request $request, array $additional = [])
    {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters(),
            $additional
        );

        $validation = Validator::make($data, $this->createValidions());

        if ($validation->fails()) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $validation->errors(),
            ], 400);
        }

        try {
            DB::transaction(function () use ($data) {
                $this->model->fill($data)->save();
            });

            $builder = $this->model->with($this->joins);

            return response()->json([
                'data' => $builder->find($this->model->id),
            ], 200);

        } catch (UniqueConstraintViolationException $e) {
            Log::info($e->getMessage());

            return response()->json([
                'error' => 'O item não pode ser criado pois já existe um com o mesmo identificador',
            ], 400);

        } catch (Exception $e) {
            Log::info($e->getMessage());

            return response()->json([
                'error' => 'Falha no servidor ao criar item',
            ], 500);
        }
    }

    public function updateFunc(Request $request, array $additional = [])
    {
        $instance = $this->model->find($request->id);

        if (!$instance) {
            return response()->json([
                'error' => 'O item procurado não existe',
            ], 404);
        }

        $data = array_merge($request->all(), $additional);

        $validation = Validator::make($data, $this->updateValidions());
        if ($validation->fails()) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $validation->errors(),
            ], 400);
        }

        try {
            DB::transaction(function () use ($instance, $data) {
                $instance->update($data);
            });

            $builder = $instance->with($this->joins);

            return response()->json([
                'message' => 'Item atualizado com sucesso',
                'data' => $builder->find($this->model->id),
            ], 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());

            return response()->json([
                'error' => 'Falha no servidor ao atualizar item!',
            ], 500);
        }
    }

    public function deleteFunc(Request $request)
    {
        $instance = $this->model->find($request->id);

        if (!$instance) {
            return response()->json([
                'error' => 'O item procurado não existe',
            ], 404);
        }

        try {
            DB::transaction(function () use ($instance) {
                $instance->delete();
            });

            return response()->json([
                'message' => 'Item deletado com sucesso',
            ], 200);

        } catch (Exception $e) {
            Log::info($e);

            return response()->json([
                'error' => 'Falha no servidor ao deletar item!',
            ], 500);
        }
    }

    public function createValidions(): array
    {
        return [];
    }

    public function updateValidions(): array
    {
        return [];
    }

    public function search(Request $request, Builder $builder)
    {
        return;
    }

    public function selects(Request $request): array
    {
        return [];
    }
}