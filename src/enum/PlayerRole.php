<?php

namespace src\enum;

enum PlayerRole: string
{
    case DPS = 'dps';
    case TANK = 'tank';
    case SUPPORT = 'support';
    case FLEX = 'flex';
    case SNIPER = 'sniper';
    case ENTRY = 'entry';
    case SUBSTITUE = 'substitue';
    case LEADER = 'In-Game Leader';
    case RIFLER = 'rifler';
}