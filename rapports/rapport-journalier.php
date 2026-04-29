<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/fonctions-factures.php';

// Restriction aux managers et admins
if ($_SESSION['utilisateur']['role'] !== 'Manager' && $_SESSION['utilisateur']['role'] !== 'Super Administrateur') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$factures = lireFactures();
$aujourdhui = date('Y-m-d');

$stats = [
    'total_ht' => 0,
    'total_tva' => 0,
    'total_ttc' => 0,
    'nb_factures' => 0,
    'produits' => []
];

foreach ($factures as $f) {
    if ($f['date'] === $aujourdhui) {
        $stats['total_ht'] += $f['total_ht'];
        $stats['total_tva'] += $f['tva'];
        $stats['total_ttc'] += $f['total_ttc'];
        $stats['nb_factures']++;
        
        foreach ($f['articles'] as $art) {
            $cb = $art['code_barre'];
            if (!isset($stats['produits'][$cb])) {
                $stats['produits'][$cb] = [
                    'nom' => $art['nom'],
                    'quantite' => 0,
                    'total_ht' => 0
                ];
            }
            $stats['produits'][$cb]['quantite'] += $art['quantite'];
            $stats['produits'][$cb]['total_ht'] += $art['sous_total_ht'];
        }
    }
}

// Trier les produits par quantité vendue
uasort($stats['produits'], function($a, $b) {
    return $b['quantite'] <=> $a['quantite'];
});

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="header-section" style="margin-bottom: 2.5rem;">
        <h2>Rapport Journalier</h2>
        <p class="text-muted" style="font-size: 1.1rem;">
            Résumé de l'activité pour le <strong><?php echo date('d/m/Y'); ?></strong>
        </p>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <div class="stat-label">Total HT</div>
            <div class="stat-value">
                <?php echo number_format($stats['total_ht'], 0, ',', ' '); ?> 
                <span class="stat-unit">CDF</span>
            </div>
        </div>
        
        <div class="stat-card" style="border-left: 4px solid var(--primary-light);">
            <div class="stat-label">Total TTC</div>
            <div class="stat-value" style="color: var(--primary);">
                <?php echo number_format($stats['total_ttc'], 0, ',', ' '); ?> 
                <span class="stat-unit">CDF</span>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid var(--accent);">
            <div class="stat-label">TVA (<?php echo TVA_RATE * 100; ?>%)</div>
            <div class="stat-value" style="color: var(--accent);">
                <?php echo number_format($stats['total_tva'], 0, ',', ' '); ?> 
                <span class="stat-unit">CDF</span>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid var(--secondary);">
            <div class="stat-label">Ventes</div>
            <div class="stat-value" style="color: var(--secondary);">
                <?php echo $stats['nb_factures']; ?> 
                <span class="stat-unit">Factures</span>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.5rem; margin-bottom: 1.5rem;">Détails des produits vendus</h3>
        <?php if (empty($stats['produits'])): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <p>Aucune transaction enregistrée pour cette journée.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Quantité</th>
                            <th>Total HT</th>
                            <th>Part du CA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['produits'] as $p): ?>
                            <?php $part = $stats['total_ht'] > 0 ? ($p['total_ht'] / $stats['total_ht'] * 100) : 0; ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($p['nom']); ?></td>
                                <td><span class="badge-qty"><?php echo $p['quantite']; ?></span></td>
                                <td style="font-weight: 600;"><?php echo number_format($p['total_ht'], 0, ',', ' '); ?> CDF</td>
                                <td>
                                    <div class="progress-container">
                                        <div class="progress-track">
                                            <div class="progress-bar" style="width: <?php echo $part; ?>%;"></div>
                                        </div>
                                        <span style="font-size: 0.8rem; font-weight: 600; min-width: 40px;"><?php echo round($part, 1); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
