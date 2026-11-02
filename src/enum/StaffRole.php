<?php

namespace src\enum;

use src\attributes\DisplayName;

enum StaffRole: string
{
    use HasDisplayName;

    #[DisplayName('Manager')]
    case MANAGER = 'Manager';
    #[DisplayName('Coach')]
    case COACH = 'Coach';
    #[DisplayName('Analyst')]
    case ANALYST = 'Analyst';
    #[DisplayName('Commentator')]
    case COMMENTATOR = 'Commentator';
    #[DisplayName('Trainer')]
    case TRAINER = 'Trainer';
}