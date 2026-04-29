<?php
// Détection automatique de l'URL de base
$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$base_dir = str_replace('\\', '/', dirname($script_name));
if ($base_dir === '/' || $base_dir === '\\') $base_dir = '';
// Si on est dans le dossier auth ou modules, on remonte d'un ou deux niveaux
$base_url = preg_replace('/\/(auth|modules\/.*|rapports)$/', '', $base_dir);
define('BASE_URL', rtrim($base_url, '/'));

define('TVA_RATE', 0.16);
define('DATA_PATH', __DIR__ . '/../data/');
define('PRODUITS_FILE', DATA_PATH . 'produits.json');
define('FACTURES_FILE', DATA_PATH . 'factures.json');
define('UTILISATEURS_FILE', DATA_PATH . 'utilisateurs.json');
