<?php

namespace App\Enums;

enum BroadcastChannel: string
{
    case WhatsApp = 'whatsapp';
    case Email = 'email';
    case PushNotification = 'push_notification';
    case All = 'all';
}
