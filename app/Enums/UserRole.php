<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case PU_PUSAT = 'PU_PUSAT';
    case PEMDA = 'PEMDA';
    case KL = 'KL';
    case ITJEN = 'ITJEN';
    case BPK = 'BPK';
}
