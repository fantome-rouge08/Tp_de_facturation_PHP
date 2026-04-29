<?php
require_once __DIR__ . '/../config/config.php';

function lireProduits() {
    if (!file_exists(PRODUITS_FILE)) {
        return [];
    }
    $data = file_get_contents(PRODUITS_FILE);
    return json_decode($data, true) ?: [];
}

function sauvegarderProduits($produits) {
    file_put_contents(PRODUITS_FILE, json_encode($produits, JSON_PRETTY_PRINT));
}

function ajouterProduit($produit) {
    $produits = lireProduits();
    $produits[] = $produit;
    sauvegarderProduits($produits);
}

function chercherProduit($code_barre) {
    $produits = lireProduits();
    $code_barre = trim($code_barre);
    foreach ($produits as $produit) {
        if (trim($produit['code_barre']) === $code_barre) {
            return $produit;
        }
    }
    return null;
}
?>
