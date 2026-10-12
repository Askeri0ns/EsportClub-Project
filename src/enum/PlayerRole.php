<?php

enum PlayerRole: string
{
    case DPS = 'dps';
    case TANK = 'tank';
    case SUPPORT = 'support';
    case FLEX = 'flex';
    case SNIPER = 'sniper';
    case ENTRY = 'entry';
}