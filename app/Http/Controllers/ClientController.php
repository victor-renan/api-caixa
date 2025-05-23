<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Models\Product;
use App\Services\ClientService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ClientController extends Controller
{
  public function __construct()
  {
    parent::__construct(Client::class);
  }

  public function search(Request $request, Builder $builder)
  {
    if ($name = $request->query('name')) {
      $builder->where('name', 'like', "%$name%");
    }

    if ($phone = $request->query('phone')) {
      $builder->where('phone', 'like', "%$phone%");
    }
  }


  public function create(ClientCreateRequest $request): JsonResponse
  {
    return $this->createFunc($request);
  }

  
  public function update(ClientUpdateRequest $request): JsonResponse
  {
    return $this->updateFunc($request);
  }
}
