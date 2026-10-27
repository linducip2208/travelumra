<?php

namespace App\Enums;

enum LeadSource: string
{
    case Website = 'website';
    case Instagram = 'instagram';
    case Facebook = 'facebook';
    case WaAds = 'wa_ads';
    case Referral = 'referral';
    case Event = 'event';
    case WalkIn = 'walk_in';
}
