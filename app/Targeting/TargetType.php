<?php

namespace App\Targeting;

enum TargetType
{
    case OPPONENT;
    case YOU;
    case UNDAMAGED;
    case READY;
    case NOT_READY;
    case UNPROTECTED;
    case PERSON;
    case CAMP;
    case DAMAGED;
    case EMPTY;
    case ANY_CARD;
}