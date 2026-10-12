<?php

namespace src\enum;

enum StaffRole: string
{
    case Manager = 'Manager';
    case Coach = 'Coach';
    case Analyste = 'Analyste';
    case Commentateur = 'Commentateur';
}