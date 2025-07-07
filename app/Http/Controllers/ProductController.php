<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Storage;

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
    $data = $request->validated();

    $image = $data['image'];

    $path = $image->store();

    if (!$path) {
      return response()->json([
        'message' => 'Falha ao enviar imagem',
      ], 500);
    }

    $request->merge([
      'image_url' => $path
    ]);

    return $this->createFunc($request);
  }

  public function update(ProductUpdateRequest $request): JsonResponse
  {
    $user = User::find($request->integer('id'));

    if ($user) {
      return response()->json([
        'message' => 'Usuário não encontrado',
      ], 404);
    }

    $data = $request->validated();

    $image = $data['image'];

    if ($image) {
      $path = $image->store('products');

      if (!$path) {
        return response()->json([
          'message' => 'Falha ao enviar imagem',
        ], 500);
      }

      Storage::delete("products/$user->image_url");

      $request->merge([
        'image_url' => $path
      ]);
    }

    return $this->updateFunc($request);
  }
}
