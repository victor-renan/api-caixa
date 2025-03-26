<?php

namespace App\Repo;

use App\Models\Client;

class ClientRepo extends GenericRepo
{
  public function __construct() {
    parent::__construct(Client::class);
  }
}