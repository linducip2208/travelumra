<?php

namespace App\Enums;

enum PackageType: string
{
    case Umrah = 'umrah';
    case Haji = 'haji';
    case UmrahPlus = 'umrah_plus';
    case HajiPlus = 'haji_plus';
    case Ziarah = 'ziarah';
}
