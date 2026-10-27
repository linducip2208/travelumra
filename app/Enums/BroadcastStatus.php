<?php

namespace App\Enums;

enum BroadcastStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sent = 'sent';
    case Failed = 'failed';
}
