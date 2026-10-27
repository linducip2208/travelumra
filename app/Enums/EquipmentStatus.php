<?php

namespace App\Enums;

enum EquipmentStatus: string
{
    case Allocated = 'allocated';
    case PickedUp = 'picked_up';
}
