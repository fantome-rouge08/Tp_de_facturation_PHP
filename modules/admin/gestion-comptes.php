<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';

if ($_SESSION['utilisateur']['role'] !== 'Super Administrateur') {
    die("Accès refusé.");
}

$utilisateurs = lireUtilisateurs();
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h2>Gestion des Comptes</h2>
<a href="ajouter-compte.php">Ajouter un compte</a>
<table border="1" style="width:100%; border-collapse: collapse; margin-top: 15px;">
    <tr><th>Identifiant</th><th>Rôle</th></tr>
    <?php foreach ($utilisateurs as $u): ?>
        <tr><td><?php echo htmlspecialchars($u['identifiant']); ?></td><td><?php echo htmlspecialchars($u['role']); ?></td></tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
