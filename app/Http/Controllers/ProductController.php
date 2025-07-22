<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $request->merge([
            'image_url' => $this->handleFile('products', $data['image'] ?? null, null)
        ]);

        return $this->createFunc($request);
    }

    public function update(ProductUpdateRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->id);

        $data = $request->validated();

        $request->merge([
            'image_url' => $this->handleFile('products', $data['image'] ?? null, $product->image_url)
        ]);

        return $this->updateFunc($request);
    }

    public function delete(Request $request): JsonResponse
    {
        $product = Product::findOrFail($request->id);

        if ($product) {
            Storage::delete($product->image_url);
        }
        
        return parent::delete($request);
    }

    public function handleFile(string $path, ?UploadedFile $file, ?string $old): ?string
    {
        if (!$file) {
            return $old;
        }

        $upload = $file->store($path);

        if (!$upload) {
            throw new HttpException(500, 'Falha ao salvar foto');
        }

        if ($old) {
            Storage::delete($old);
        }

        return $upload;
    }
}
