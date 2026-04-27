<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

$factures = lireFactures();
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h2>Historique des factures</h2>
<div class="table-container">
    <table>
        <tr><th>ID Facture</th><th>Date</th><th>Caissier</th><th>Total TTC</th><th>Action</th></tr>
        <?php foreach ($factures as $f): ?>
            <tr>
                <td><?php echo htmlspecialchars($f['id_facture']); ?></td>
                <td><?php echo htmlspecialchars($f['date']); ?></td>
                <td><?php echo htmlspecialchars($f['caissier']); ?></td>
                <td><?php echo htmlspecialchars($f['total_ttc']); ?> CDF</td>
                <td><a href="afficher-facture.php?id=<?php echo urlencode($f['id_facture']); ?>">Voir</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
