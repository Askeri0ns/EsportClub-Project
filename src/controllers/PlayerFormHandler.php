<?php

namespace src\controllers;

use DateTime;
use src\classes\Player;

class PlayerFormHandler
{
    // On injecte PlayerDatabase pour pouvoir enregistrer et vérifier les doublons
    public function __construct(private PlayerDatabase $playerDb)
    {
    }

    /**
     * Traite les données du formulaire ($_POST)
     * Retourne un tableau d'erreurs (vide s'il n'y a pas d'erreur et que l'enregistrement a réussi)
     */
    public function handle(array $data): array
    {
        $errors = [];

        // Nettoyage des données reçues
        $firstname = trim($data['firstname'] ?? '');
        $lastname  = trim($data['lastname'] ?? '');
        $birthdate = trim($data['birthdate'] ?? '');
        $picture   = trim($data['picture'] ?? '');

        // Vérification des champs requis
        if (empty($firstname)) {
            $errors['firstname'] = "Le prénom est obligatoire.";
        }
        if (empty($lastname)) {
            $errors['lastname'] = "Le nom est obligatoire.";
        }
        if (empty($birthdate)) {
            $errors['birthdate'] = "La date de naissance est obligatoire.";
        }

        // Validation du format de la date
        $dateObject = null;
        if (!empty($birthdate)) {
            $dateObject = DateTime::createFromFormat('Y-m-d', $birthdate);
            if (!$dateObject || $dateObject->format('Y-m-d') !== $birthdate) {
                $errors['birthdate'] = "Format de date invalide (AAAA-MM-JJ attendu).";
            }
        }

        // Vérifier si le joueur existe déjà
        if (empty($errors)) {
            if ($this->playerDb->exists($firstname, $lastname, $birthdate)) {
                $errors['general'] = "Ce joueur existe déjà dans la base de données.";
            }
        }

        // Si aucune erreur n'a été trouvée, on crée l'objet et on sauvegarde
        if (empty($errors)) {
            $player = new Player(
                $firstname,
                $lastname,
                $dateObject,
                !empty($picture) ? $picture : null
            );

            $this->playerDb->create($player);
        }

        // Retourne le tableau d'erreurs (vide si succès)
        return $errors;
    }
}