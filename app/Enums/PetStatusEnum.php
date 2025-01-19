<?php declare(strict_types=1);

namespace App\Enums;

enum PetStatusEnum: string
{
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case SOLD = 'sold';
}
