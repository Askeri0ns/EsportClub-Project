<?php

use src\enum\PlayerRole;
use src\enum\StaffRole;

require_once __DIR__ . '/../src/autoloader.php';
session_start();

function escape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function requiredText(string $field, string $label): string
{
    $value = trim(postString($field));
    if ($value === '') {
        throw new InvalidArgumentException("Le champ « {$label} » est obligatoire.");
    }

    return $value;
}

function postString(string $field): string
{
    $value = $_POST[$field] ?? '';
    if (!is_string($value)) {
        throw new InvalidArgumentException('Une valeur de formulaire est invalide.');
    }

    return $value;
}

function validDate(string $value, string $label): string
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    if (!$date || $date->format('Y-m-d') !== $value) {
        throw new InvalidArgumentException("La date « {$label} » est invalide.");
    }

    return $value;
}

function validId(string $field, string $label): int
{
    $value = filter_var(postString($field), FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    if ($value === false) {
        throw new InvalidArgumentException("Sélectionnez {$label}.");
    }

    return $value;
}

function validScore(string $field, string $label): int
{
    $value = filter_var(postString($field), FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0],
    ]);
    if ($value === false) {
        throw new InvalidArgumentException("Le score « {$label} » doit être un entier positif ou nul.");
    }

    return $value;
}

function ensureColumn(PDO $pdo, string $table, string $column, string $definition): void
{
    $columns = $pdo->query('PRAGMA table_info("' . $table . '")')->fetchAll();
    foreach ($columns as $existingColumn) {
        if ($existingColumn['name'] === $column) {
            return;
        }
    }

    $pdo->exec('ALTER TABLE "' . $table . '" ADD COLUMN "' . $column . '" ' . $definition);
}

function displayDate(string $value): string
{
    try {
        return (new DateTimeImmutable($value))->format('d/m/Y');
    } catch (Exception) {
        return $value;
    }
}

function displayRole(string $role): string
{
    return ucfirst(strtolower($role));
}

function dateInputValue(string $value): string
{
    try {
        return (new DateTimeImmutable($value))->format('Y-m-d');
    } catch (Exception) {
        return '';
    }
}

$formError = null;
$failedAction = null;
$failedEntity = null;
$failedValues = [];
$databaseError = null;
$successMessages = [
    'player' => 'Le joueur a été ajouté.',
    'team' => 'L’équipe a été ajoutée.',
    'association' => 'Le joueur a été associé à l’équipe.',
    'staff' => 'Le membre du staff a été ajouté.',
    'game' => 'Le match a été ajouté.',
    'club' => 'Le club opposant a été ajouté.',
    'updated' => 'Les modifications ont été enregistrées.',
    'deleted' => 'L’élément a été supprimé.',
];
$successMessage = $successMessages[$_GET['success'] ?? ''] ?? null;
$csrfToken = $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

try {
    $pdo = require __DIR__ . '/../private/bdd.php';
    $pdo->beginTransaction();
    try {
        ensureColumn($pdo, 'PlayerHasTeam', 'role', "TEXT NOT NULL DEFAULT 'FLEX'");
        ensureColumn($pdo, 'Game', 'city', "TEXT NOT NULL DEFAULT ''");
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            if (!hash_equals($csrfToken, postString('_csrf'))) {
                http_response_code(400);
                throw new InvalidArgumentException('La session du formulaire a expiré. Rechargez la page et réessayez.');
            }

            $action = postString('action');
            switch ($action) {
                case 'player':
                    $firstname = requiredText('firstname', 'prénom');
                    $lastname = requiredText('lastname', 'nom');
                    $birthdate = validDate(requiredText('birthdate', 'date de naissance'), 'naissance');
                    $picture = trim(postString('picture'));
                    if ($picture !== '' && filter_var($picture, FILTER_VALIDATE_URL) === false) {
                        throw new InvalidArgumentException('L’adresse de la photo doit être une URL valide.');
                    }

                    $statement = $pdo->prepare(
                        'INSERT INTO Player (firstname, lastname, birtdate, picture) VALUES (:firstname, :lastname, :birtdate, :picture)'
                    );
                    $statement->execute([
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'birtdate' => $birthdate,
                        'picture' => $picture !== '' ? $picture : null,
                    ]);
                    $successKey = 'player';
                    $anchor = 'joueurs';
                    break;

                case 'team':
                    $statement = $pdo->prepare('INSERT INTO Team (name) VALUES (:name)');
                    $statement->execute(['name' => requiredText('name', 'nom de l’équipe')]);
                    $successKey = 'team';
                    $anchor = 'equipes';
                    break;

                case 'association':
                    $playerId = validId('player_id', 'un joueur');
                    $teamId = validId('team_id', 'une équipe');
                    $role = PlayerRole::tryFrom(postString('role'));
                    if ($role === null) {
                        throw new InvalidArgumentException('Sélectionnez un rôle valide pour le joueur.');
                    }

                    $check = $pdo->prepare('SELECT COUNT(*) FROM Player WHERE id = :id');
                    $check->execute(['id' => $playerId]);
                    if ((int) $check->fetchColumn() === 0) {
                        throw new InvalidArgumentException('Le joueur sélectionné n’existe plus.');
                    }
                    $check = $pdo->prepare('SELECT COUNT(*) FROM Team WHERE id = :id');
                    $check->execute(['id' => $teamId]);
                    if ((int) $check->fetchColumn() === 0) {
                        throw new InvalidArgumentException('L’équipe sélectionnée n’existe plus.');
                    }
                    $check = $pdo->prepare(
                        'SELECT COUNT(*) FROM PlayerHasTeam WHERE idPlayer = :player AND idTeam = :team'
                    );
                    $check->execute(['player' => $playerId, 'team' => $teamId]);
                    if ((int) $check->fetchColumn() > 0) {
                        throw new InvalidArgumentException('Ce joueur fait déjà partie de cette équipe.');
                    }

                    $statement = $pdo->prepare(
                        'INSERT INTO PlayerHasTeam (idPlayer, idTeam, role) VALUES (:player, :team, :role)'
                    );
                    $statement->execute([
                        'player' => $playerId,
                        'team' => $teamId,
                        'role' => $role->value,
                    ]);
                    $successKey = 'association';
                    $anchor = 'equipes';
                    break;

                case 'staff':
                    $role = StaffRole::tryFrom(postString('role'));
                    if ($role === null) {
                        throw new InvalidArgumentException('Sélectionnez une fonction valide pour le membre du staff.');
                    }

                    $statement = $pdo->prepare(
                        'INSERT INTO Staff (firstname, lastname, role) VALUES (:firstname, :lastname, :role)'
                    );
                    $statement->execute([
                        'firstname' => requiredText('firstname', 'prénom'),
                        'lastname' => requiredText('lastname', 'nom'),
                        'role' => $role->value,
                    ]);
                    $successKey = 'staff';
                    $anchor = 'staff';
                    break;

                case 'club':
                    $statement = $pdo->prepare(
                        'INSERT INTO OpposingClub (name, adress, city) VALUES (:name, :address, :city)'
                    );
                    $statement->execute([
                        'name' => requiredText('name', 'nom du club'),
                        'address' => requiredText('address', 'adresse du club'),
                        'city' => requiredText('city', 'ville du club'),
                    ]);
                    $successKey = 'club';
                    $anchor = 'clubs';
                    break;

                case 'game':
                    $teamId = validId('team_id', 'une équipe');
                    $clubId = validId('club_id', 'un club opposant');
                    $teamScore = validScore('score_team', 'de l’équipe');
                    $opponentScore = validScore('score_opponent', 'du club opposant');
                    $gameDate = validDate(requiredText('date', 'date du match'), 'du match');
                    $city = requiredText('city', 'lieu du match');

                    $check = $pdo->prepare('SELECT COUNT(*) FROM Team WHERE id = :id');
                    $check->execute(['id' => $teamId]);
                    if ((int) $check->fetchColumn() === 0) {
                        throw new InvalidArgumentException('L’équipe sélectionnée n’existe plus.');
                    }
                    $check = $pdo->prepare('SELECT COUNT(*) FROM OpposingClub WHERE id = :id');
                    $check->execute(['id' => $clubId]);
                    if ((int) $check->fetchColumn() === 0) {
                        throw new InvalidArgumentException('Le club opposant sélectionné n’existe plus.');
                    }

                    $statement = $pdo->prepare(
                        'INSERT INTO Game (date, scoreTeam, scoreOpposing, idTeam, idOpposingClub, city)
                         VALUES (:date, :team_score, :opponent_score, :team, :club, :city)'
                    );
                    $statement->execute([
                        'date' => $gameDate,
                        'team_score' => $teamScore,
                        'opponent_score' => $opponentScore,
                        'team' => $teamId,
                        'club' => $clubId,
                        'city' => $city,
                    ]);
                    $successKey = 'game';
                    $anchor = 'matchs';
                    break;

                case 'update':
                    $entity = postString('entity');
                    $id = validId('id', 'un élément valide');
                    switch ($entity) {
                        case 'player':
                            $picture = trim(postString('picture'));
                            if ($picture !== '' && filter_var($picture, FILTER_VALIDATE_URL) === false) {
                                throw new InvalidArgumentException('L’adresse de la photo doit être une URL valide.');
                            }
                            $statement = $pdo->prepare(
                                'UPDATE Player SET firstname = :firstname, lastname = :lastname,
                                 birtdate = :birtdate, picture = :picture WHERE id = :id'
                            );
                            $statement->execute([
                                'id' => $id,
                                'firstname' => requiredText('firstname', 'prénom'),
                                'lastname' => requiredText('lastname', 'nom'),
                                'birtdate' => validDate(requiredText('birthdate', 'date de naissance'), 'naissance'),
                                'picture' => $picture !== '' ? $picture : null,
                            ]);
                            $anchor = 'joueurs';
                            break;

                        case 'team':
                            $statement = $pdo->prepare('UPDATE Team SET name = :name WHERE id = :id');
                            $statement->execute([
                                'id' => $id,
                                'name' => requiredText('name', 'nom de l’équipe'),
                            ]);
                            $anchor = 'equipes';
                            break;

                        case 'association':
                            $playerId = validId('player_id', 'un joueur');
                            $teamId = validId('team_id', 'une équipe');
                            $role = PlayerRole::tryFrom(postString('role'));
                            if ($role === null) {
                                throw new InvalidArgumentException('Sélectionnez un rôle valide pour le joueur.');
                            }
                            $originalPlayerId = validId('original_player_id', 'le joueur associé');
                            $originalTeamId = validId('original_team_id', 'l’équipe associée');
                            $check = $pdo->prepare('SELECT COUNT(*) FROM Player WHERE id = :id');
                            $check->execute(['id' => $playerId]);
                            if ((int) $check->fetchColumn() === 0) {
                                throw new InvalidArgumentException('Le joueur sélectionné n’existe plus.');
                            }
                            $check = $pdo->prepare('SELECT COUNT(*) FROM Team WHERE id = :id');
                            $check->execute(['id' => $teamId]);
                            if ((int) $check->fetchColumn() === 0) {
                                throw new InvalidArgumentException('L’équipe sélectionnée n’existe plus.');
                            }
                            $check = $pdo->prepare(
                                'SELECT COUNT(*) FROM PlayerHasTeam WHERE idPlayer = :player AND idTeam = :team'
                            );
                            $check->execute(['player' => $originalPlayerId, 'team' => $originalTeamId]);
                            if ((int) $check->fetchColumn() === 0) {
                                throw new InvalidArgumentException('Cette association n’existe plus.');
                            }
                            if ($playerId !== $originalPlayerId || $teamId !== $originalTeamId) {
                                $check = $pdo->prepare(
                                    'SELECT COUNT(*) FROM PlayerHasTeam WHERE idPlayer = :player AND idTeam = :team'
                                );
                                $check->execute(['player' => $playerId, 'team' => $teamId]);
                                if ((int) $check->fetchColumn() > 0) {
                                    throw new InvalidArgumentException('Cette association existe déjà.');
                                }
                            }
                            $statement = $pdo->prepare(
                                'UPDATE PlayerHasTeam SET idPlayer = :player, idTeam = :team, role = :role
                                 WHERE idPlayer = :original_player AND idTeam = :original_team'
                            );
                            $statement->execute([
                                'player' => $playerId,
                                'team' => $teamId,
                                'role' => $role->value,
                                'original_player' => $originalPlayerId,
                                'original_team' => $originalTeamId,
                            ]);
                            $anchor = 'equipes';
                            break;

                        case 'staff':
                            $role = StaffRole::tryFrom(postString('role'));
                            if ($role === null) {
                                throw new InvalidArgumentException('Sélectionnez une fonction valide pour le membre du staff.');
                            }
                            $statement = $pdo->prepare(
                                'UPDATE Staff SET firstname = :firstname, lastname = :lastname, role = :role WHERE id = :id'
                            );
                            $statement->execute([
                                'id' => $id,
                                'firstname' => requiredText('firstname', 'prénom'),
                                'lastname' => requiredText('lastname', 'nom'),
                                'role' => $role->value,
                            ]);
                            $anchor = 'staff';
                            break;

                        case 'club':
                            $statement = $pdo->prepare(
                                'UPDATE OpposingClub SET name = :name, adress = :address, city = :city WHERE id = :id'
                            );
                            $statement->execute([
                                'id' => $id,
                                'name' => requiredText('name', 'nom du club'),
                                'address' => requiredText('address', 'adresse du club'),
                                'city' => requiredText('city', 'ville du club'),
                            ]);
                            $anchor = 'clubs';
                            break;

                        case 'game':
                            $teamId = validId('team_id', 'une équipe');
                            $clubId = validId('club_id', 'un club opposant');
                            $check = $pdo->prepare('SELECT COUNT(*) FROM Team WHERE id = :id');
                            $check->execute(['id' => $teamId]);
                            if ((int) $check->fetchColumn() === 0) {
                                throw new InvalidArgumentException('L’équipe sélectionnée n’existe plus.');
                            }
                            $check = $pdo->prepare('SELECT COUNT(*) FROM OpposingClub WHERE id = :id');
                            $check->execute(['id' => $clubId]);
                            if ((int) $check->fetchColumn() === 0) {
                                throw new InvalidArgumentException('Le club opposant sélectionné n’existe plus.');
                            }
                            $statement = $pdo->prepare(
                                'UPDATE Game SET date = :date, city = :city, idTeam = :team,
                                 idOpposingClub = :club, scoreTeam = :team_score, scoreOpposing = :opponent_score
                                 WHERE id = :id'
                            );
                            $statement->execute([
                                'id' => $id,
                                'date' => validDate(requiredText('date', 'date du match'), 'du match'),
                                'city' => requiredText('city', 'lieu du match'),
                                'team' => $teamId,
                                'club' => $clubId,
                                'team_score' => validScore('score_team', 'de l’équipe'),
                                'opponent_score' => validScore('score_opponent', 'du club opposant'),
                            ]);
                            $anchor = 'matchs';
                            break;

                        default:
                            throw new InvalidArgumentException('Type d’élément à modifier invalide.');
                    }
                    $successKey = 'updated';
                    break;

                case 'delete':
                    $entity = postString('entity');
                    $id = validId('id', 'un élément valide');
                    switch ($entity) {
                        case 'player':
                            $pdo->beginTransaction();
                            $statement = $pdo->prepare('DELETE FROM PlayerHasTeam WHERE idPlayer = :id');
                            $statement->execute(['id' => $id]);
                            $statement = $pdo->prepare('DELETE FROM Player WHERE id = :id');
                            $statement->execute(['id' => $id]);
                            $pdo->commit();
                            $anchor = 'joueurs';
                            break;

                        case 'team':
                            $check = $pdo->prepare('SELECT COUNT(*) FROM Game WHERE idTeam = :id');
                            $check->execute(['id' => $id]);
                            if ((int) $check->fetchColumn() > 0) {
                                throw new InvalidArgumentException('Cette équipe est utilisée dans des matchs. Modifiez ou supprimez d’abord ces matchs.');
                            }
                            $pdo->beginTransaction();
                            $statement = $pdo->prepare('DELETE FROM PlayerHasTeam WHERE idTeam = :id');
                            $statement->execute(['id' => $id]);
                            $statement = $pdo->prepare('DELETE FROM Team WHERE id = :id');
                            $statement->execute(['id' => $id]);
                            $pdo->commit();
                            $anchor = 'equipes';
                            break;

                        case 'association':
                            $statement = $pdo->prepare(
                                'DELETE FROM PlayerHasTeam WHERE idPlayer = :player AND idTeam = :team'
                            );
                            $statement->execute([
                                'player' => validId('player_id', 'le joueur associé'),
                                'team' => validId('team_id', 'l’équipe associée'),
                            ]);
                            $anchor = 'equipes';
                            break;

                        case 'staff':
                            $statement = $pdo->prepare('DELETE FROM Staff WHERE id = :id');
                            $statement->execute(['id' => $id]);
                            $anchor = 'staff';
                            break;

                        case 'club':
                            $check = $pdo->prepare('SELECT COUNT(*) FROM Game WHERE idOpposingClub = :id');
                            $check->execute(['id' => $id]);
                            if ((int) $check->fetchColumn() > 0) {
                                throw new InvalidArgumentException('Ce club apparaît dans des matchs. Modifiez ou supprimez d’abord ces matchs.');
                            }
                            $statement = $pdo->prepare('DELETE FROM OpposingClub WHERE id = :id');
                            $statement->execute(['id' => $id]);
                            $anchor = 'clubs';
                            break;

                        case 'game':
                            $statement = $pdo->prepare('DELETE FROM Game WHERE id = :id');
                            $statement->execute(['id' => $id]);
                            $anchor = 'matchs';
                            break;

                        default:
                            throw new InvalidArgumentException('Type d’élément à supprimer invalide.');
                    }
                    $successKey = 'deleted';
                    break;

                default:
                    throw new InvalidArgumentException('Action inconnue. Rechargez la page et réessayez.');
            }

            header('Location: ./index.php?success=' . rawurlencode($successKey) . '#' . $anchor);
            exit;
        } catch (InvalidArgumentException $error) {
            $formError = $error->getMessage();
            $failedAction = is_string($_POST['action'] ?? null) ? $_POST['action'] : null;
            $failedEntity = is_string($_POST['entity'] ?? null) ? $_POST['entity'] : null;
            $failedValues = array_map(
                static fn(mixed $value): string => is_scalar($value) ? (string) $value : '',
                $_POST
            );
        } catch (PDOException $error) {
            error_log('EsportClub write failed: ' . $error->getMessage());
            $formError = 'Enregistrement impossible pour le moment. Vérifiez les informations puis réessayez.';
            $failedAction = is_string($_POST['action'] ?? null) ? $_POST['action'] : null;
            $failedEntity = is_string($_POST['entity'] ?? null) ? $_POST['entity'] : null;
            $failedValues = array_map(
                static fn(mixed $value): string => is_scalar($value) ? (string) $value : '',
                $_POST
            );
        }
    }

    $players = $pdo->query(
        'SELECT id, firstname, lastname, birtdate, picture FROM Player ORDER BY lastname, firstname'
    )->fetchAll();
    $teams = $pdo->query('SELECT id, name FROM Team ORDER BY name')->fetchAll();
    $opposingClubs = $pdo->query(
        'SELECT id, name, adress, city FROM OpposingClub ORDER BY name'
    )->fetchAll();
    $staffMembers = $pdo->query('SELECT id, firstname, lastname, role FROM Staff ORDER BY lastname, firstname')->fetchAll();
    $games = $pdo->query(
        'SELECT g.id, g.date, g.scoreTeam, g.scoreOpposing, g.city,
                g.idTeam AS team_id, g.idOpposingClub AS club_id, t.name AS team_name,
                o.name AS club_name, o.city AS club_city
         FROM Game g
         LEFT JOIN Team t ON t.id = g.idTeam
         LEFT JOIN OpposingClub o ON o.id = g.idOpposingClub
         ORDER BY g.date DESC, g.id DESC'
    )->fetchAll();
    $roster = $pdo->query(
        'SELECT pht.idPlayer AS player_id, p.firstname, p.lastname,
                t.id AS team_id, t.name AS team_name, pht.role
         FROM PlayerHasTeam pht
         JOIN Player p ON p.id = pht.idPlayer
         JOIN Team t ON t.id = pht.idTeam
         ORDER BY t.name, p.lastname, p.firstname'
    )->fetchAll();
} catch (PDOException $error) {
    error_log('EsportClub database unavailable: ' . $error->getMessage());
    http_response_code(500);
    $databaseError = 'La base de données est indisponible. Vérifiez sa configuration, puis rechargez la page.';
    $players = $teams = $opposingClubs = $staffMembers = $games = $roster = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fc">
    <title>EsportClub — Gestion du club</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="#accueil" aria-label="EsportClub, accueil">
            <span class="brand-mark" aria-hidden="true">E</span>
            <span>Esport<span class="brand-accent">Club</span></span>
        </a>
        <nav class="main-nav" aria-label="Navigation principale">
            <a href="#matchs">Matchs</a>
            <a href="#joueurs">Joueurs</a>
            <a href="#equipes">Équipes</a>
            <a href="#staff">Staff</a>
            <a href="#clubs">Clubs opposants</a>
        </nav>
        <button class="header-action" type="button" data-admin-open>+ Ajouter</button>
    </header>

    <main id="accueil" class="page-shell">
        <section class="welcome">
            <div>
                <p class="eyebrow"><span class="status-dot"></span> Tableau de bord du club</p>
                <h1>Gérez votre club esport, <span>visez la victoire.</span></h1>
                <p class="welcome-copy">Retrouvez les effectifs et les résultats. Gérez les informations utiles au même endroit.</p>
            </div>
            <button class="primary-button" type="button" data-admin-open>Gérer le club <span aria-hidden="true">→</span></button>
        </section>

        <?php if ($databaseError !== null): ?>
            <p class="notice notice-error" role="alert"><?= escape($databaseError) ?></p>
        <?php elseif ($formError !== null): ?>
            <p class="notice notice-error" role="alert"><?= escape($formError) ?></p>
        <?php elseif ($successMessage !== null): ?>
            <p class="notice notice-success" role="status"><?= escape($successMessage) ?></p>
        <?php endif; ?>

        <section class="stats" aria-label="Résumé du club">
            <a class="stat-card stat-purple" href="#matchs"><span>Matchs</span><strong><?= count($games ?? []) ?></strong><span class="stat-mark" aria-hidden="true">↗</span></a>
            <a class="stat-card stat-blue" href="#joueurs"><span>Joueurs</span><strong><?= count($players ?? []) ?></strong><span class="stat-mark" aria-hidden="true">↗</span></a>
            <a class="stat-card stat-orange" href="#equipes"><span>Équipes</span><strong><?= count($teams ?? []) ?></strong><span class="stat-mark" aria-hidden="true">↗</span></a>
            <a class="stat-card stat-green" href="#staff"><span>Membres du staff</span><strong><?= count($staffMembers ?? []) ?></strong><span class="stat-mark" aria-hidden="true">↗</span></a>
            <a class="stat-card stat-pink" href="#clubs"><span>Clubs opposants</span><strong><?= count($opposingClubs ?? []) ?></strong><span class="stat-mark" aria-hidden="true">↗</span></a>
        </section>

        <section class="content-section" id="matchs">
            <div class="section-heading">
                <div><p class="eyebrow">Sur le terrain</p><h2>Derniers matchs</h2></div>
                <a class="text-link" href="#ajouter-match" data-admin-form>Créer un match <span aria-hidden="true">→</span></a>
            </div>
            <?php if ($games === []): ?>
                <p class="empty-state">Aucun match enregistré pour le moment.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Date</th><th>Rencontre</th><th>Résultat</th><th>Lieu</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td class="muted-cell"><?= escape(displayDate((string) $game['date'])) ?></td>
                                <td><strong><?= escape($game['team_name'] ?? 'Équipe supprimée') ?></strong><span class="versus"> contre </span><?= escape($game['club_name'] ?? 'Club supprimé') ?></td>
                                <td><span class="score"><?= escape($game['scoreTeam'] ?? '—') ?> <i>–</i> <?= escape($game['scoreOpposing'] ?? '—') ?></span></td>
                                <td><?= escape($game['city'] !== '' ? $game['city'] : ($game['club_city'] ?? '—')) ?></td>
                                <td>
                                    <div class="item-actions">
                                        <button class="item-action" type="button" data-edit="game"
                                            data-id="<?= escape($game['id']) ?>"
                                            data-date="<?= escape(dateInputValue((string) $game['date'])) ?>"
                                            data-city="<?= escape($game['city']) ?>"
                                            data-team-id="<?= escape($game['team_id']) ?>"
                                            data-club-id="<?= escape($game['club_id']) ?>"
                                            data-score-team="<?= escape($game['scoreTeam']) ?>"
                                            data-score-opponent="<?= escape($game['scoreOpposing']) ?>">Modifier</button>
                                        <form method="post" data-confirm="Supprimer ce match ?">
                                            <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="entity" value="game">
                                            <input type="hidden" name="id" value="<?= escape($game['id']) ?>">
                                            <button class="item-action item-action-danger" type="submit">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section class="content-section" id="joueurs">
            <div class="section-heading">
                <div><p class="eyebrow">Effectif</p><h2>Joueurs</h2></div>
                <a class="text-link" href="#ajouter-joueur" data-admin-form>Ajouter un joueur <span aria-hidden="true">→</span></a>
            </div>
            <?php if ($players === []): ?>
                <p class="empty-state">Aucun joueur enregistré pour le moment.</p>
            <?php else: ?>
                <div class="person-grid">
                    <?php foreach ($players as $player): ?>
                        <article class="person-card">
                            <span class="avatar avatar-blue"><?= escape(mb_strtoupper(mb_substr($player['firstname'], 0, 1) . mb_substr($player['lastname'], 0, 1))) ?></span>
                            <div class="person-details">
                                <div class="item-heading">
                                    <h3><?= escape($player['firstname'] . ' ' . $player['lastname']) ?></h3>
                                    <div class="item-actions">
                                        <button class="item-action" type="button" data-edit="player"
                                            data-id="<?= escape($player['id']) ?>"
                                            data-firstname="<?= escape($player['firstname']) ?>"
                                            data-lastname="<?= escape($player['lastname']) ?>"
                                            data-birthdate="<?= escape(dateInputValue((string) $player['birtdate'])) ?>"
                                            data-picture="<?= escape($player['picture'] ?? '') ?>">Modifier</button>
                                        <form method="post" data-confirm="Supprimer ce joueur et ses associations aux équipes ?">
                                            <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="entity" value="player">
                                            <input type="hidden" name="id" value="<?= escape($player['id']) ?>">
                                            <button class="item-action item-action-danger" type="submit">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                                <p>Né(e) le <?= escape(displayDate((string) $player['birtdate'])) ?></p>
                                <div class="role-list">
                                    <?php
                                    $playerAssignments = array_filter(
                                        $roster,
                                        static fn(array $member): bool => (int) $member['player_id'] === (int) $player['id']
                                    );
                                    ?>
                                    <?php if ($playerAssignments === []): ?>
                                        <span class="role-chip role-chip-empty">Sans équipe</span>
                                    <?php else: ?>
                                        <?php foreach ($playerAssignments as $assignment): ?>
                                            <span class="role-chip">
                                                <?= escape($assignment['team_name']) ?>
                                                <strong><?= escape(displayRole((string) $assignment['role'])) ?></strong>
                                                <button class="role-edit" type="button" aria-label="Modifier l’association de <?= escape($player['firstname'] . ' ' . $player['lastname']) ?> à <?= escape($assignment['team_name']) ?>"
                                                    data-edit="association"
                                                    data-id="<?= escape($player['id']) ?>"
                                                    data-player-id="<?= escape($player['id']) ?>"
                                                    data-team-id="<?= escape($assignment['team_id']) ?>"
                                                    data-role="<?= escape($assignment['role']) ?>">✎</button>
                                            </span>
                                            <form class="inline-delete" method="post" data-confirm="Retirer ce joueur de l’équipe ?">
                                                <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="entity" value="association">
                                                <input type="hidden" name="id" value="<?= escape($player['id']) ?>">
                                                <input type="hidden" name="player_id" value="<?= escape($player['id']) ?>">
                                                <input type="hidden" name="team_id" value="<?= escape($assignment['team_id']) ?>">
                                                <button class="role-edit role-remove" type="submit" aria-label="Retirer <?= escape($player['firstname'] . ' ' . $player['lastname']) ?> de <?= escape($assignment['team_name']) ?>">×</button>
                                            </form>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="content-section" id="equipes">
            <div class="section-heading">
                <div><p class="eyebrow">Collectifs du club</p><h2>Équipes internes</h2></div>
                <a class="text-link" href="#ajouter-equipe" data-admin-form>Ajouter une équipe <span aria-hidden="true">→</span></a>
            </div>
            <?php if ($teams === []): ?>
                <p class="empty-state">Aucune équipe enregistrée pour le moment.</p>
            <?php else: ?>
                <div class="team-grid">
                    <?php foreach ($teams as $team): ?>
                        <article class="team-card">
                            <div class="team-card-heading">
                                <span class="team-badge" aria-hidden="true">T</span>
                                <div><h3><?= escape($team['name']) ?></h3><p><?= count(array_filter($roster, static fn(array $member): bool => (int) $member['team_id'] === (int) $team['id'])) ?> joueur(s)</p></div>
                                <div class="item-actions">
                                    <button class="item-action" type="button" data-edit="team" data-id="<?= escape($team['id']) ?>" data-name="<?= escape($team['name']) ?>">Modifier</button>
                                    <form method="post" data-confirm="Supprimer cette équipe et ses associations ?">
                                        <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="entity" value="team">
                                        <input type="hidden" name="id" value="<?= escape($team['id']) ?>">
                                        <button class="item-action item-action-danger" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                            <ul>
                            <?php foreach ($roster as $member): ?>
                                <?php if ((int) $member['team_id'] === (int) $team['id']): ?>
                                    <li><?= escape($member['firstname'] . ' ' . $member['lastname']) ?><span><?= escape(displayRole((string) $member['role'])) ?></span></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </ul>
                            <?php if (!array_filter($roster, static fn(array $member): bool => (int) $member['team_id'] === (int) $team['id'])): ?>
                                <p class="team-empty">Aucun joueur associé pour le moment.</p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="content-section" id="staff">
            <div class="section-heading">
                <div><p class="eyebrow">Encadrement</p><h2>Membres du staff</h2></div>
                <a class="text-link" href="#ajouter-staff" data-admin-form>Ajouter un membre <span aria-hidden="true">→</span></a>
            </div>
            <?php if ($staffMembers === []): ?>
                <p class="empty-state">Aucun membre du staff enregistré pour le moment.</p>
            <?php else: ?>
                <div class="person-grid">
                    <?php foreach ($staffMembers as $member): ?>
                        <article class="person-card">
                            <span class="avatar avatar-green"><?= escape(mb_strtoupper(mb_substr($member['firstname'], 0, 1) . mb_substr($member['lastname'], 0, 1))) ?></span>
                            <div class="person-details">
                                <div class="item-heading">
                                    <h3><?= escape($member['firstname'] . ' ' . $member['lastname']) ?></h3>
                                    <div class="item-actions">
                                        <button class="item-action" type="button" data-edit="staff"
                                            data-id="<?= escape($member['id']) ?>"
                                            data-firstname="<?= escape($member['firstname']) ?>"
                                            data-lastname="<?= escape($member['lastname']) ?>"
                                            data-role="<?= escape($member['role']) ?>">Modifier</button>
                                        <form method="post" data-confirm="Supprimer ce membre du staff ?">
                                            <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="entity" value="staff">
                                            <input type="hidden" name="id" value="<?= escape($member['id']) ?>">
                                            <button class="item-action item-action-danger" type="submit">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                                <p><?= escape($member['role']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="content-section" id="clubs">
            <div class="section-heading">
                <div><p class="eyebrow">Adversaires</p><h2>Clubs opposants</h2></div>
                <a class="text-link" href="#ajouter-club" data-admin-form>Ajouter un club <span aria-hidden="true">→</span></a>
            </div>
            <?php if ($opposingClubs === []): ?>
                <p class="empty-state">Aucun club opposant enregistré pour le moment.</p>
            <?php else: ?>
                <div class="opponent-grid">
                    <?php foreach ($opposingClubs as $club): ?>
                        <article class="opponent-card">
                            <span class="opponent-mark" aria-hidden="true">VS</span>
                            <div class="person-details">
                                <div class="item-heading">
                                    <h3><?= escape($club['name']) ?></h3>
                                    <div class="item-actions">
                                        <button class="item-action" type="button" data-edit="club"
                                            data-id="<?= escape($club['id']) ?>"
                                            data-name="<?= escape($club['name']) ?>"
                                            data-address="<?= escape($club['adress']) ?>"
                                            data-city="<?= escape($club['city']) ?>">Modifier</button>
                                        <form method="post" data-confirm="Supprimer ce club opposant ?">
                                            <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="entity" value="club">
                                            <input type="hidden" name="id" value="<?= escape($club['id']) ?>">
                                            <button class="item-action item-action-danger" type="submit">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                                <p><?= escape($club['city']) ?> · <?= escape($club['adress']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <dialog class="admin-dialog" id="admin-panel" aria-labelledby="admin-title">
            <div class="admin-dialog-heading">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h2 id="admin-title">Gérer le club</h2>
                </div>
                <button class="dialog-close" type="button" data-admin-close aria-label="Fermer le panneau">
                    <svg aria-hidden="true" viewBox="0 0 20 20"><path d="m5 5 10 10M15 5 5 15"></path></svg>
                </button>
            </div>
            <?php if ($formError !== null): ?>
                <p class="notice notice-error" role="alert"><?= escape($formError) ?></p>
            <?php endif; ?>
        <section class="content-section add-section" id="ajouter">
            <div class="section-heading">
                <div><p class="eyebrow">Nouvelles entrées</p><h2>Ajouter aux données du club</h2></div>
                <p class="section-note">Ajoutez les informations essentielles à votre espace.</p>
            </div>
            <div class="form-grid">
                <form class="form-card" method="post" id="ajouter-joueur">
                    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                    <input type="hidden" name="action" value="player">
                    <h3>Ajouter un joueur</h3>
                    <div class="field-row">
                        <label>Prénom<input name="firstname" required maxlength="100" autocomplete="given-name"></label>
                        <label>Nom<input name="lastname" required maxlength="100" autocomplete="family-name"></label>
                    </div>
                    <label>Date de naissance<input type="date" name="birthdate" required></label>
                    <label>Photo (URL, facultative)<input type="url" name="picture" maxlength="2048" placeholder="https://..."></label>
                    <button class="form-button" type="submit">Enregistrer le joueur <span aria-hidden="true">→</span></button>
                </form>

                <form class="form-card" method="post" id="ajouter-equipe">
                    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                    <input type="hidden" name="action" value="team">
                    <h3>Ajouter une équipe</h3>
                    <label>Nom de l’équipe<input name="name" required maxlength="100" placeholder="Ex. Équipe principale"></label>
                    <button class="form-button" type="submit">Créer l’équipe <span aria-hidden="true">→</span></button>
                </form>

                <form class="form-card" method="post" id="associer-joueur">
                    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                    <input type="hidden" name="action" value="association">
                    <h3>Associer un joueur</h3>
                    <label>Joueur
                        <select name="player_id" required>
                            <option value="">Choisir un joueur</option>
                            <?php foreach ($players as $player): ?><option value="<?= escape($player['id']) ?>"><?= escape($player['firstname'] . ' ' . $player['lastname']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Équipe
                        <select name="team_id" required>
                            <option value="">Choisir une équipe</option>
                            <?php foreach ($teams as $team): ?><option value="<?= escape($team['id']) ?>"><?= escape($team['name']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Rôle en jeu
                        <select name="role" required>
                            <option value="">Choisir un rôle</option>
                            <?php foreach (PlayerRole::cases() as $role): ?><option value="<?= escape($role->value) ?>"><?= escape(ucfirst($role->value)) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <button class="form-button" type="submit">Associer le joueur <span aria-hidden="true">→</span></button>
                </form>

                <form class="form-card" method="post" id="ajouter-staff">
                    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                    <input type="hidden" name="action" value="staff">
                    <h3>Ajouter un membre du staff</h3>
                    <div class="field-row">
                        <label>Prénom<input name="firstname" required maxlength="100" autocomplete="given-name"></label>
                        <label>Nom<input name="lastname" required maxlength="100" autocomplete="family-name"></label>
                    </div>
                    <label>Fonction
                        <select name="role" required>
                            <option value="">Choisir une fonction</option>
                            <?php foreach (StaffRole::cases() as $role): ?><option value="<?= escape($role->value) ?>"><?= escape($role->value) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <button class="form-button" type="submit">Enregistrer le membre <span aria-hidden="true">→</span></button>
                </form>

                <form class="form-card" method="post" id="ajouter-club">
                    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                    <input type="hidden" name="action" value="club">
                    <h3>Ajouter un club opposant</h3>
                    <label>Nom du club<input name="name" required maxlength="100" placeholder="Ex. Team Spirit"></label>
                    <label>Adresse<input name="address" required maxlength="150" placeholder="Salle ou adresse"></label>
                    <label>Ville<input name="city" required maxlength="100" placeholder="Ex. Paris"></label>
                    <button class="form-button" type="submit">Enregistrer le club <span aria-hidden="true">→</span></button>
                </form>

                <form class="form-card form-card-wide" method="post" id="ajouter-match">
                    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                    <input type="hidden" name="action" value="game">
                    <h3>Créer un match</h3>
                    <div class="field-row field-row-three">
                        <label>Date<input type="date" name="date" required></label>
                        <label>Lieu<input name="city" required maxlength="100" placeholder="Ville ou salle"></label>
                        <label>Équipe
                            <select name="team_id" required>
                                <option value="">Choisir une équipe</option>
                                <?php foreach ($teams as $team): ?><option value="<?= escape($team['id']) ?>"><?= escape($team['name']) ?></option><?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div class="field-row field-row-three">
                        <label>Club opposant
                            <select name="club_id" required>
                                <option value="">Choisir un club</option>
                                <?php foreach ($opposingClubs as $club): ?><option value="<?= escape($club['id']) ?>"><?= escape($club['name']) ?></option><?php endforeach; ?>
                            </select>
                        </label>
                        <label>Score de l’équipe<input type="number" name="score_team" required min="0" step="1" value="0"></label>
                        <label>Score adverse<input type="number" name="score_opponent" required min="0" step="1" value="0"></label>
                    </div>
                    <button class="form-button" type="submit">Enregistrer le match <span aria-hidden="true">→</span></button>
                </form>
            </div>
        </section>
        </dialog>

        <dialog class="admin-dialog edit-dialog" id="edit-panel" aria-labelledby="edit-title">
            <div class="admin-dialog-heading">
                <div>
                    <p class="eyebrow">Administration</p>
                    <h2 id="edit-title">Modifier l’élément</h2>
                </div>
                <button class="dialog-close" type="button" data-edit-close aria-label="Fermer le panneau">
                    <svg aria-hidden="true" viewBox="0 0 20 20"><path d="m5 5 10 10M15 5 5 15"></path></svg>
                </button>
            </div>
            <form class="form-card edit-form" method="post">
                <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="entity">
                <input type="hidden" name="id">

                <div class="edit-fields" data-fields="player">
                    <h3>Modifier le joueur</h3>
                    <div class="field-row">
                        <label>Prénom<input name="firstname" required maxlength="100"></label>
                        <label>Nom<input name="lastname" required maxlength="100"></label>
                    </div>
                    <label>Date de naissance<input type="date" name="birthdate" required></label>
                    <label>Photo (URL, facultative)<input type="url" name="picture" maxlength="2048" placeholder="https://..."></label>
                </div>

                <div class="edit-fields" data-fields="team">
                    <h3>Modifier l’équipe interne</h3>
                    <label>Nom de l’équipe<input name="name" required maxlength="100"></label>
                </div>

                <div class="edit-fields" data-fields="association">
                    <h3>Modifier l’association joueur-équipe</h3>
                    <input type="hidden" name="original_player_id">
                    <input type="hidden" name="original_team_id">
                    <label>Joueur
                        <select name="player_id" required>
                            <?php foreach ($players as $player): ?><option value="<?= escape($player['id']) ?>"><?= escape($player['firstname'] . ' ' . $player['lastname']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Équipe
                        <select name="team_id" required>
                            <?php foreach ($teams as $team): ?><option value="<?= escape($team['id']) ?>"><?= escape($team['name']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Rôle en jeu
                        <select name="role" required>
                            <?php foreach (PlayerRole::cases() as $role): ?><option value="<?= escape($role->value) ?>"><?= escape(displayRole($role->value)) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="edit-fields" data-fields="staff">
                    <h3>Modifier le membre du staff</h3>
                    <div class="field-row">
                        <label>Prénom<input name="firstname" required maxlength="100"></label>
                        <label>Nom<input name="lastname" required maxlength="100"></label>
                    </div>
                    <label>Fonction
                        <select name="role" required>
                            <?php foreach (StaffRole::cases() as $role): ?><option value="<?= escape($role->value) ?>"><?= escape($role->value) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="edit-fields" data-fields="club">
                    <h3>Modifier le club opposant</h3>
                    <label>Nom du club<input name="name" required maxlength="100"></label>
                    <label>Adresse<input name="address" required maxlength="150"></label>
                    <label>Ville<input name="city" required maxlength="100"></label>
                </div>

                <div class="edit-fields" data-fields="game">
                    <h3>Modifier le match</h3>
                    <div class="field-row">
                        <label>Date<input type="date" name="date" required></label>
                        <label>Lieu<input name="city" required maxlength="100"></label>
                    </div>
                    <label>Équipe
                        <select name="team_id" required>
                            <?php foreach ($teams as $team): ?><option value="<?= escape($team['id']) ?>"><?= escape($team['name']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Club opposant
                        <select name="club_id" required>
                            <?php foreach ($opposingClubs as $club): ?><option value="<?= escape($club['id']) ?>"><?= escape($club['name']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <div class="field-row">
                        <label>Score de l’équipe<input type="number" name="score_team" required min="0" step="1"></label>
                        <label>Score adverse<input type="number" name="score_opponent" required min="0" step="1"></label>
                    </div>
                </div>
                <button class="form-button" type="submit">Enregistrer les modifications <span aria-hidden="true">→</span></button>
            </form>
        </dialog>
    </main>

    <footer class="site-footer">
        <a class="brand" href="#accueil"><span class="brand-mark" aria-hidden="true">E</span><span>Esport<span class="brand-accent">Club</span></span></a>
        <p>Tout le club, au même endroit.</p>
        <a href="#accueil">Retour en haut ↑</a>
    </footer>
    <script>
        const adminPanel = document.querySelector('#admin-panel');

        document.querySelectorAll('[data-admin-open]').forEach((button) => {
            button.addEventListener('click', () => adminPanel.showModal());
        });

        document.querySelectorAll('[data-admin-form]').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                adminPanel.showModal();
                adminPanel.querySelector(link.getAttribute('href'))?.scrollIntoView({ block: 'start' });
            });
        });

        document.querySelector('[data-admin-close]').addEventListener('click', () => adminPanel.close());

        const editPanel = document.querySelector('#edit-panel');
        const editForm = editPanel.querySelector('.edit-form');
        const fieldNames = {
            player: ['firstname', 'lastname', 'birthdate', 'picture'],
            team: ['name'],
            association: ['original_player_id', 'original_team_id', 'player_id', 'team_id', 'role'],
            staff: ['firstname', 'lastname', 'role'],
            club: ['name', 'address', 'city'],
            game: ['date', 'city', 'team_id', 'club_id', 'score_team', 'score_opponent'],
        };

        function showEditPanel(entity, values, id = '') {
            const activeFields = editForm.querySelector(`[data-fields="${entity}"]`);
            if (!activeFields) {
                return;
            }

            editForm.querySelector('[name="entity"]').value = entity;
            editForm.querySelector('[name="id"]').value = id;
            editForm.querySelectorAll('.edit-fields').forEach((group) => {
                const active = group === activeFields;
                group.hidden = !active;
                group.querySelectorAll('input, select').forEach((field) => {
                    field.disabled = !active;
                });
            });

            for (const name of fieldNames[entity]) {
                const field = activeFields.querySelector(`[name="${name}"]`);
                if (field && Object.hasOwn(values, name)) {
                    field.value = values[name];
                }
            }
            editPanel.showModal();
            activeFields.querySelector('input:not([type="hidden"]), select')?.focus();
        }

        document.querySelectorAll('[data-edit]').forEach((button) => {
            button.addEventListener('click', () => {
                const entity = button.dataset.edit;
                const values = {
                    player: {
                        firstname: button.dataset.firstname,
                        lastname: button.dataset.lastname,
                        birthdate: button.dataset.birthdate,
                        picture: button.dataset.picture,
                    },
                    team: { name: button.dataset.name },
                    association: {
                        original_player_id: button.dataset.playerId,
                        original_team_id: button.dataset.teamId,
                        player_id: button.dataset.playerId,
                        team_id: button.dataset.teamId,
                        role: button.dataset.role,
                    },
                    staff: {
                        firstname: button.dataset.firstname,
                        lastname: button.dataset.lastname,
                        role: button.dataset.role,
                    },
                    club: {
                        name: button.dataset.name,
                        address: button.dataset.address,
                        city: button.dataset.city,
                    },
                    game: {
                        date: button.dataset.date,
                        city: button.dataset.city,
                        team_id: button.dataset.teamId,
                        club_id: button.dataset.clubId,
                        score_team: button.dataset.scoreTeam,
                        score_opponent: button.dataset.scoreOpponent,
                    },
                }[entity];

                showEditPanel(entity, values, button.dataset.id);
            });
        });

        document.querySelector('[data-edit-close]').addEventListener('click', () => editPanel.close());
        document.querySelectorAll('[data-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm(form.dataset.confirm)) {
                    event.preventDefault();
                }
            });
        });

        <?php if ($formError !== null && $failedAction === 'update' && $failedEntity !== null): ?>
        showEditPanel(<?= json_encode($failedEntity, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            <?= json_encode($failedValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            <?= json_encode($failedValues['id'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>);
        <?php elseif ($formError !== null && $failedAction !== 'delete'): ?>
        adminPanel.showModal();
        <?php endif; ?>
    </script>
</body>
</html>
