<?php

namespace App\Enums;

enum TransactionType: string
{
    case Earned = 'earned';
    case Redeemed = 'redeemed';
    case Expired = 'expired';
}
