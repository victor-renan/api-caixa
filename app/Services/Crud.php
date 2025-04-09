<?php

namespace App\Services;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Log;

class Crud
{
  public function __construct(
    public string $modelClass
  ) {
  }

  public function getById(int $id): Model
  {
    $model = $this->modelClass::firstWhere('id', '=', $id);
    if (!$model) {
      throw new Exception('Item solicitado não existe');
    }
    return $model;
  }

  public function create(array $attributes): Model
  {
    $model = new $this->modelClass($attributes);
    try {
      DB::transaction(function () use ($model) {
        $model->save();
      });
      return $model;
    } catch (Exception $e) {
      Log::error($e);
      throw new Exception('Falha ao criar o item');
    }
  }

  public function update(int $id, array $attributes): Model
  {
    $model = $this->modelClass::find($id);
    try {
      DB::transaction(function () use ($model, $attributes) {
        $model->update($attributes);
      });
      return $model;
    } catch (Exception $e) {
      Log::error($e);
      throw new Exception('Falha ao atualizar o item');
    }
  }

  public function delete(int $id): void
  {
    $model = $this->modelClass::find($id);
    try {
      DB::transaction(function () use ($model) {
        $model->delete();
      });
    } catch (Exception $e) {
      Log::error($e);
      throw new Exception('Falha ao deletar o item');
    }
  }
}