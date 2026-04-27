<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

$id = $_GET['id'] ?? '';
$factures = lireFactures();
$facture = null;

foreach ($factures as $f) {
    if ($f['id_facture'] === $id) {
        $facture = $f;
        break;
    }
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h2>Détail Facture <?php echo htmlspecialchars($id); ?></h2>
<?php if ($facture): ?>
    <p>Date: <?php echo $facture['date']; ?> <?php echo $facture['heure']; ?></p>
    <p>Caissier: <?php echo htmlspecialchars($facture['caissier']); ?></p>
    <table>
        <tr><th>Article</th><th>Prix Unit</th><th>Qté</th><th>Sous-total</th></tr>
        <?php foreach ($facture['articles'] as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nom']); ?></td>
                <td><?php echo $item['prix_unitaire_ht']; ?></td>
                <td><?php echo $item['quantite']; ?></td>
                <td><?php echo $item['sous_total_ht']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p><strong>Total HT:</strong> <?php echo $facture['total_ht']; ?> CDF</p>
    <p><strong>TVA (18%):</strong> <?php echo $facture['tva']; ?> CDF</p>
    <p><strong>Total TTC:</strong> <?php echo $facture['total_ttc']; ?> CDF</p>
<?php else: ?>
    <p>Facture introuvable.</p>
<?php endif; ?>
<a href="liste.php">Retour à la liste</a>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
