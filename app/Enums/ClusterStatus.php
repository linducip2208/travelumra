<?php

namespace App\Enums;

enum ClusterStatus: string
{
    case Scheduled = 'scheduled';
    case Departed = 'departed';
    case Completed = 'completed';
}
