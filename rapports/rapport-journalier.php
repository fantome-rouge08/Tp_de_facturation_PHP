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

?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container">
    <div style="margin-bottom: 2.5rem;">
        <h2 style="margin-bottom: 0.5rem;">Rapport Journalier</h2>
        <p style="color: var(--text-muted); font-size: 1.1rem;">
            Résumé de l'activité pour le <strong><?php echo date('d/m/Y'); ?></strong>
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--primary);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total HT</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); font-family: 'Outfit', sans-serif;">
                <?php echo number_format($stats['total_ht'], 0, ',', ' '); ?> <span style="font-size: 0.9rem; color: var(--text-muted);">CDF</span>
            </div>
        </div>
        
        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--primary-light);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total TTC</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--primary); font-family: 'Outfit', sans-serif;">
                <?php echo number_format($stats['total_ttc'], 0, ',', ' '); ?> <span style="font-size: 0.9rem; color: var(--text-muted);">CDF</span>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--accent);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">TVA (18%)</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--accent); font-family: 'Outfit', sans-serif;">
                <?php echo number_format($stats['total_tva'], 0, ',', ' '); ?> <span style="font-size: 0.9rem; color: var(--text-muted);">CDF</span>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--secondary);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Nombre de Ventes</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--secondary); font-family: 'Outfit', sans-serif;">
                <?php echo $stats['nb_factures']; ?> <span style="font-size: 0.9rem; color: var(--text-muted);">Factures</span>
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
                                <td><span style="background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 700;"><?php echo $p['quantite']; ?></span></td>
                                <td><?php echo number_format($p['total_ht'], 0, ',', ' '); ?> CDF</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="flex-grow: 1; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                            <div style="width: <?php echo $part; ?>%; height: 100%; background: var(--primary);"></div>
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

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
