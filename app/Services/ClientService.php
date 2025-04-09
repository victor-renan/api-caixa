<?php

namespace App\Services;

use App\Models\Client;

class ClientService extends Crud
{
  public function __construct()
  {
    parent::__construct(Client::class);
  }
}