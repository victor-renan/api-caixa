<?php

namespace App\Enums;

enum PaymentTypes: string
{
    case Pix = 'pix';
    case Money = 'money';
    case Card = 'card';
}
