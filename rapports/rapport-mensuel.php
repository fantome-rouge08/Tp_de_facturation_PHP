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
$ce_mois = date('Y-m');

$stats = [
    'total_ht' => 0,
    'total_ttc' => 0,
    'nb_factures' => 0,
    'jours' => []
];

foreach ($factures as $f) {
    if (strpos($f['date'], $ce_mois) === 0) {
        $stats['total_ht'] += $f['total_ht'];
        $stats['total_ttc'] += $f['total_ttc'];
        $stats['nb_factures']++;
        
        $jour = $f['date'];
        if (!isset($stats['jours'][$jour])) {
            $stats['jours'][$jour] = [
                'nb_factures' => 0,
                'total_ht' => 0,
                'total_ttc' => 0
            ];
        }
        $stats['jours'][$jour]['nb_factures']++;
        $stats['jours'][$jour]['total_ht'] += $f['total_ht'];
        $stats['jours'][$jour]['total_ttc'] += $f['total_ttc'];
    }
}

// Trier par date
ksort($stats['jours']);
$nb_jours_actifs = count($stats['jours']);
$moyenne_quotidienne = $nb_jours_actifs > 0 ? ($stats['total_ttc'] / $nb_jours_actifs) : 0;

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="header-section" style="margin-bottom: 2.5rem;">
        <h2>Rapport Mensuel</h2>
        <p class="text-muted" style="font-size: 1.1rem;">
            Analyse de performance pour la période de <strong><?php echo date('m/Y'); ?></strong>
        </p>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <div class="stat-label">Chiffre d'Affaires (TTC)</div>
            <div class="stat-value" style="color: var(--primary);">
                <?php echo number_format($stats['total_ttc'], 0, ',', ' '); ?> 
                <span class="stat-unit">CDF</span>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid var(--accent);">
            <div class="stat-label">Moyenne Quotidienne</div>
            <div class="stat-value" style="color: var(--accent);">
                <?php echo number_format($moyenne_quotidienne, 0, ',', ' '); ?> 
                <span class="stat-unit">CDF / jour</span>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid var(--secondary);">
            <div class="stat-label">Volume de Transactions</div>
            <div class="stat-value" style="color: var(--secondary);">
                <?php echo $stats['nb_factures']; ?> 
                <span class="stat-unit">Ventes</span>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.5rem; margin-bottom: 1.5rem;">Évolution quotidienne des ventes</h3>
        <?php if (empty($stats['jours'])): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <p>Aucune donnée disponible pour ce mois.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Ventes</th>
                            <th>Total HT</th>
                            <th>Total TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['jours'] as $date => $d): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo date('d/m/Y', strtotime($date)); ?></td>
                                <td><span class="badge-qty"><?php echo $d['nb_factures']; ?></span></td>
                                <td><?php echo number_format($d['total_ht'], 0, ',', ' '); ?> CDF</td>
                                <td style="font-weight: 700; color: var(--primary);"><?php echo number_format($d['total_ttc'], 0, ',', ' '); ?> CDF</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
