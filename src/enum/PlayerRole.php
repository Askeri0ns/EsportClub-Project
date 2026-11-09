<?php

namespace src\enum;

use src\attributes\DisplayName;

enum PlayerRole: string
{
    use HasDisplayName;

    #[DisplayName('DPS')]
    case DPS = 'DPS';
    #[DisplayName('Tank')]
    case TANK = 'Tank';
    #[DisplayName('Support')]
    case SUPPORT = 'Support';
    #[DisplayName('Flex')]
    case FLEX = 'Flex';
    #[DisplayName('Sniper')]
    case SNIPER = 'Sniper';
    #[DisplayName('Entry')]
    case ENTRY = 'Entry';
    #[DisplayName('Substitute')]
    case SUBSTITUE = 'Substitue';
    #[DisplayName('In-Game Leader')]
    case LEADER = 'In-Game Leader';
    #[DisplayName('Rifler')]
    case RIFLER = 'Rifler';
}
