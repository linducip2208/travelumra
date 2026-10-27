<?php

namespace App\Enums;

enum StaffRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Finance = 'finance';
    case BranchManager = 'branch_manager';
    case Agent = 'agent';
    case Muthawif = 'muthawif';
}
