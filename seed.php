<?php

// 1. Inclusion des classes
require_once 'Player.php';
require_once 'Team.php';
require_once 'StaffMember.php';
require_once 'PlayerHasTeam.php';
require_once 'Game.php';

// 1. ENTITÉ : TEAM (5 équipes CS2)
$teams = [
    new Team("Vitality", "France"),
    new Team("G2 Esports", "Allemagne"),
    new Team("FaZe Clan", "USA"),
    new Team("Natus Vincere", "Ukraine"),
    new Team("Team Spirit", "Serbie")
];

// 2. ENTITÉ : PLAYER (5 joueurs CS2)
$players = [
    new Player("Mathieu", "Herbaut", new DateTime("2000-10-28"), "zywoo.png"),
    new Player("Dan", "Madesclaire", new DateTime("1994-02-22"), "apex.png"),
    new Player("Ilya", "Osipov", new DateTime("2005-05-01"), "monesy.png"),
    new Player("Nikola", "Kovač", new DateTime("1997-02-16"), "niko.png"),
    new Player("Robin", "Kool", new DateTime("1999-12-22"), "ropz.png")
];

// 3. ENTITÉ : STAFF MEMBER (5 membres du staff)
$staffMembers = [
    new StaffMember("Rémy", "Quoniam", "Head Coach", $teams[0]),      // Vitality
    new StaffMember("Jan", "Duch", "Assistant Coach", $teams[0]),
    new StaffMember("Wiktor", "Wojtas", "Head Coach", $teams[1]),     // G2
    new StaffMember("Filip", "Kubski", "Head Coach", $teams[2]),      // FaZe
    new StaffMember("Andrey", "Gorodenskiy", "Head Coach", $teams[3])  // NaVi
];

// 4. ENTITÉ : PLAYER HAS TEAM (5 associations Joueur <-> Équipe)
$playerHasTeams = [
    new PlayerHasTeam($players[0], $teams[0]), // ZywOo -> Vitality
    new PlayerHasTeam($players[1], $teams[0]), // apEX  -> Vitality
    new PlayerHasTeam($players[2], $teams[1]), // m0NESY -> G2
    new PlayerHasTeam($players[3], $teams[1]), // NiKo   -> G2
    new PlayerHasTeam($players[4], $teams[2])  // ropz   -> FaZe
];

// 5. ENTITÉ : GAME (5 Matchs de CS2)
// Score Équipe / Score Adversaire / Date / Équipe / Ville / Adversaire
$games = [
    new Game(13, 11, new DateTime("2024-03-15 18:00:00"), $teams[0], "Copenhague", "Heroic"),
    new Game(16, 14, new DateTime("2024-04-10 20:30:00"), $teams[1], "Katowice", "Astralis"),
    new Game(13, 8,  new DateTime("2024-05-20 16:00:00"), $teams[2], "Cologne", "MOUZ"),
    new Game(9,  13, new DateTime("2024-06-12 19:00:00"), $teams[3], "Londres", "Virtus.pro"),
    new Game(13, 5,  new DateTime("2024-07-04 21:00:00"), $teams[4], "Paris", "Complexity")
];
?>