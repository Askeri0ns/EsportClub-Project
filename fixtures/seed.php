<?php

require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/Team.php';
require_once __DIR__ . '/../classes/OpposingClub.php';
require_once __DIR__ . '/../classes/PlayerHasTeam.php';
require_once __DIR__ . '/../classes/StaffMember.php';
require_once __DIR__ . '/../classes/Game.php';

// Jeu d'essai (fixtures)

// 5 instances de Player
$player1 = new Player("Lucas", "Martin", new DateTime("2004-05-12"), "lucas_martin.jpg");
$player2 = new Player("Nathan", "Leroy", new DateTime("2003-08-21"), "nathan_leroy.jpg");
$player3 = new Player("Ethan", "Morel", new DateTime("2005-02-17"));
$player4 = new Player("Hugo", "Bernard", new DateTime("2004-11-03"));
$player5 = new Player("Alex", "Dubois", new DateTime("2005-07-29"), "alex_dubois.jpg");


// 5 instances de Team
$team1 = new Team("Karmine Corp");
$team2 = new Team("Gentle Mates");
$team3 = new Team("Vitality");
$team4 = new Team("BDS");
$team5 = new Team("Solary");


// 5 instances de OpposingClub
$opposingClub1 = new OpposingClub("Team Liquid", "Science Park 400", "Amsterdam");
$opposingClub2 = new OpposingClub("Fnatic", "20 Farringdon Road", "Londres");
$opposingClub3 = new OpposingClub("G2 Esports", "Rosenthaler Strasse 43-45", "Berlin");
$opposingClub4 = new OpposingClub("Natus Vincere", "Khreshchatyk Street 19", "Kyiv");
$opposingClub5 = new OpposingClub("SK Gaming", "Schanzenstraße 28", "Cologne");


// 5 instances de PlayerHasTeam
$playerHasTeam1 = new PlayerHasTeam($player1, $team1, PlayerRole::ENTRY);
$playerHasTeam2 = new PlayerHasTeam($player2, $team1, PlayerRole::SUPPORT);
$playerHasTeam3 = new PlayerHasTeam($player3, $team2, PlayerRole::DPS);
$playerHasTeam4 = new PlayerHasTeam($player4, $team2, PlayerRole::TANK);
$playerHasTeam5 = new PlayerHasTeam($player5, $team3, PlayerRole::FLEX);


// 5 instances de StaffMember
$staff1 = new StaffMember("Arthur", "Lefevre", new DateTime("1988-03-14"), StaffRole::Manager, "arthur_lefevre.jpg");
$staff2 = new StaffMember("Thomas", "Renaud", new DateTime("1990-06-22"), StaffRole::Coach);
$staff3 = new StaffMember("Julien", "Marchand", new DateTime("1992-09-10"), StaffRole::Analyste, "julien_marchang.jpg");
$staff4 = new StaffMember("Camille", "Robert", new DateTime("1995-01-27"), StaffRole::Commentateur, "camille_robert.jpg");
$staff5 = new StaffMember("Maxime", "Petit", new DateTime("1987-12-05"), StaffRole::Coach);


// 5 instances de Game
$game1 = new Game(2, 1, $team1, $opposingClub1, new DateTime("2026-10-10"), "Paris");
$game2 = new Game(0, 2, $team2, $opposingClub2, new DateTime("2026-10-12"), "Londres");
$game3 = new Game(3, 1, $team3, $opposingClub3, new DateTime("2026-10-15"), "Berlin");
$game4 = new Game(1, 2, $team4, $opposingClub4, new DateTime("2026-10-18"), "Kyiv");
$game5 = new Game(3, 2, $team5, $opposingClub5, new DateTime("2026-10-20"), "Cologne");