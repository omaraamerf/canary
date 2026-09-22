<?php

namespace App\Enums;

enum BirdStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
}
