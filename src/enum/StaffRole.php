<?php

namespace src\enum;

enum StaffRole: string
{
    case MANAGER = 'Manager';
    case COACH = 'Coach';
    case ANALYST = 'Analyst';
    case COMMENTATOR = 'Commentator';
    case TRAINER = 'Trainer';
}