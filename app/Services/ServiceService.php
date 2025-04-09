<?php

namespace App\Services;

use App\Models\Service;

class ServiceService extends Crud
{
  public function __construct()
  {
    parent::__construct(Service::class);
  }
}