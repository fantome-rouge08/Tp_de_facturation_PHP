<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

$produits = lireProduits();
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h2>Liste des produits</h2>
<div class="table-container">
    <table>
        <tr><th>Code-barres</th><th>Nom</th><th>Prix</th></tr>
        <?php foreach ($produits as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['code_barre']); ?></td>
                <td><?php echo htmlspecialchars($p['nom']); ?></td>
                <td><?php echo htmlspecialchars($p['prix_unitaire_ht']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
