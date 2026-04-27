<?php
require_once __DIR__ . '/../config/config.php';

function lireUtilisateurs() {
    if (!file_exists(UTILISATEURS_FILE)) {
        return [];
    }
    $data = file_get_contents(UTILISATEURS_FILE);
    return json_decode($data, true) ?: [];
}

function verifierUtilisateur($identifiant, $mot_de_passe) {
    $utilisateurs = lireUtilisateurs();
    foreach ($utilisateurs as $utilisateur) {
        if ($utilisateur['identifiant'] === $identifiant && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            return $utilisateur;
        }
    }
    return null;
}
