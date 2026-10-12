# EsportClub-Project
Site de gestion d'un club d'E-sport, de ses joueurs, des équipes et des matchs.

---
Schéma relationnel de la base de données et du projet :
```mermaid
erDiagram
	PLAYER {
		id INT PK
		firstname VARCHAR(255)
		lastname VARCHAR(255)
		birthdate DATETIME
		picture VARCHAR(255)
	}
	PLAYERHASTEAM {
		player_id INT FK
		team_id INT FK
		role VARCHAR(255)
	}
	TEAM {
		id INT PK
		name VARCHAR(255)
	}
	GAME {
		id INT PK
		team_score INT
		opponent_score INT
		date DATETIME
		team_id INT FK
		city VARCHAR(255)
		opposing_club_id INT FK
	}
	OPPOSINGCLUB {
		id INT PK
		name VARCHAR(255)
		adress VARCHAR(255)
		city VARCHAR(255)
	}
	STAFFMEMBER {
		id INT PK
		firstname VARCHAR(255)
		lastname VARCHAR(255)
		birthdate DATETIME
		picture VARCHAR(255)
		role VARCHAR(255)
	}
	
	TEAM ||--o{ PLAYERHASTEAM : ""
	PLAYER ||--o{ PLAYERHASTEAM : ""
	TEAM ||--o{ GAME : ""
	OPPOSINGCLUB ||--o{ GAME : ""
```

## Chargement automatique des classes

Les classes PHP sont chargées par `src/autoloader.php`. Le préfixe `src\` correspond au dossier `src/`, puis les namespaces suivent les sous-dossiers : par exemple, `src\classes\Game` se trouve dans `src/classes/Game.php` et `src\controllers\GameDatabase` dans `src/controllers/GameDatabase.php`.

Le point d'entrée `public/index.php` charge déjà l'autoloader. Pour un autre script PHP, incluez-le une fois au début du script, puis utilisez les classes avec leur namespace (ou une instruction `use`). Évitez les `require` individuels pour les classes de l'application.
