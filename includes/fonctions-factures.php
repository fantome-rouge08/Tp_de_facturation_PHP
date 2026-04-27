<?php
require_once __DIR__ . '/../config/config.php';

function lireFactures() {
    if (!file_exists(FACTURES_FILE)) {
        return [];
    }
    $data = file_get_contents(FACTURES_FILE);
    return json_decode($data, true) ?: [];
}

function sauvegarderFacture($facture) {
    $factures = lireFactures();
    $factures[] = $facture;
    file_put_contents(FACTURES_FILE, json_encode($factures, JSON_PRETTY_PRINT));
}

function genererIdFacture() {
    return 'FAC-' . date('Ymd') . '-' . str_pad(count(lireFactures()) + 1, 3, '0', STR_PAD_LEFT);
}
?>
