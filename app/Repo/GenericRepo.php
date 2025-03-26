<?php

namespace App\Repo;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class GenericRepo
{
  public function __construct(
    public string $modelClass
  ) {
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
      throw $e;
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
      throw $e;
    }
  }

  public function delete(int $id): bool
  {
    $model = $this->modelClass::find($id);
    try {
      DB::transaction(function () use ($model) {
        $model->delete();
      });
      return true;
    } catch (Exception $e) {
      throw $e;
    }
  }
}