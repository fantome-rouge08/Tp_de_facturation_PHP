<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions-auth.php';

if (isset($_SESSION['utilisateur'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = $_POST['identifiant'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    $utilisateur = verifierUtilisateur($identifiant, $mot_de_passe);
    if ($utilisateur) {
        $_SESSION['utilisateur'] = $utilisateur;
        
        $redirect = BASE_URL . '/index.php';
        if ($utilisateur['role'] === 'Caissier') {
            $redirect = BASE_URL . '/modules/facturation/nouvelle-facture.php';
        } elseif ($utilisateur['role'] === 'Manager') {
            $redirect = BASE_URL . '/modules/produits/liste.php';
        }
        
        header('Location: ' . $redirect);
        exit;
    } else {
        $erreur = 'Identifiants invalides.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <title>Connexion</title>
</head>
<body class="login-page">
    <div class="login-card">
        <h2>Bienvenue</h2>
        <p>Connectez-vous à votre espace</p>
        
        <?php if ($erreur): ?>
            <div class="alert alert-error"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="identifiant">Identifiant</label>
                <input type="text" name="identifiant" id="identifiant" placeholder="Ex: admin" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="••••••••" required>
            </div>

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
