<?php

namespace App\Repo;

use App\Models\Service;

class ServiceRepo extends GenericRepo
{
  public function __construct()
  {
    parent::__construct(Service::class);
  }
}