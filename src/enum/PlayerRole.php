<?php

namespace src\enum;

enum PlayerRole: string
{
    case DPS = 'DPS';
    case TANK = 'Tank';
    case SUPPORT = 'Support';
    case FLEX = 'Flex';
    case SNIPER = 'Sniper';
    case ENTRY = 'Entry';
    case SUBSTITUE = 'Substitue';
    case LEADER = 'In-Game Leader';
    case RIFLER = 'Rifler';
}