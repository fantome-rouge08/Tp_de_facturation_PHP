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

?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container">
    <div style="margin-bottom: 2.5rem;">
        <h2 style="margin-bottom: 0.5rem;">Rapport Mensuel</h2>
        <p style="color: var(--text-muted); font-size: 1.1rem;">
            Analyse de performance pour la période de <strong><?php echo date('m/Y'); ?></strong>
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--primary);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Chiffre d'Affaires Mensuel (TTC)</div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary); font-family: 'Outfit', sans-serif;">
                <?php echo number_format($stats['total_ttc'], 0, ',', ' '); ?> <span style="font-size: 0.9rem; color: var(--text-muted);">CDF</span>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--accent);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Moyenne Quotidienne</div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--accent); font-family: 'Outfit', sans-serif;">
                <?php echo number_format($moyenne_quotidienne, 0, ',', ' '); ?> <span style="font-size: 0.9rem; color: var(--text-muted);">CDF / jour</span>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem; border-left: 4px solid var(--secondary);">
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Volume de Transactions</div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--secondary); font-family: 'Outfit', sans-serif;">
                <?php echo $stats['nb_factures']; ?> <span style="font-size: 0.9rem; color: var(--text-muted);">Ventes</span>
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
                                <td><span style="background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 700;"><?php echo $d['nb_factures']; ?></span></td>
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

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
