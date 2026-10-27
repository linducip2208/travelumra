<?php

namespace App\Enums;

enum BranchType: string
{
    case Main = 'main';
    case Branch = 'branch';
    case Agent = 'agent';
}
