<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Interested = 'interested';
    case FollowUp = 'follow_up';
    case Converted = 'converted';
    case ClosedLost = 'closed_lost';
}
