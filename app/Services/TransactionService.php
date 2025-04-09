<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionService extends Crud
{
  public function __construct()
  {
    parent::__construct(Transaction::class);
  }
}