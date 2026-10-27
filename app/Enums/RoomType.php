<?php

namespace App\Enums;

enum RoomType: string
{
    case Single = 'single';
    case Double = 'double';
    case Triple = 'triple';
    case Quad = 'quad';
}
