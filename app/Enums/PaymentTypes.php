<?php

namespace App\Enums;

enum PaymentTypes
{
    case Pix = 'pix';
    case Money = 'money';
    case Card = 'card';
}
