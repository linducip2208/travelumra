<?php

namespace App\Enums;

enum PilgrimSource: string
{
    case WalkIn = 'walk_in';
    case Referral = 'referral';
    case Agent = 'agent';
    case Website = 'website';
    case SocialMedia = 'social_media';
    case Event = 'event';
}
