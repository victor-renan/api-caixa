<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCreateRequest;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct()
    {
        parent::__construct(Transaction::class);
    }

    public function search(Request $request, Builder $builder)
    {
        $builder->where('user_id', $request->user()->id);

        if ($amountMin = $request->query('amount_min')) {
            $builder->where('amount', '>=', $amountMin);
        }

        if ($amountMax = $request->query('amount_max')) {
            $builder->where('amount', '<=', $amountMax);
        }

        if ($clientId = $request->query('client_id')) {
            $builder->where('client_id', '=', $clientId);
        }
    }

    public function create(TransactionCreateRequest $request): JsonResponse
    {
        return $this->createFunc($request);
    }
}
