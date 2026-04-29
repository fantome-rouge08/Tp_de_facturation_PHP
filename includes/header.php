<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <title>Système de Facturation</title>
</head>
<body>
    <div class="container">
        <nav>
            <div class="nav-brand">
                <span style="font-weight: 800; color: var(--primary); font-size: 1.25rem;">FACTU</span>PRO
            </div>
            
            <div class="nav-links">
                <?php $role = $_SESSION['utilisateur']['role']; ?>
                
                <?php if ($role === 'Manager' || $role === 'Super Administrateur'): ?>
                    <a href="<?php echo BASE_URL; ?>/modules/produits/liste.php">Produits</a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>/modules/facturation/nouvelle-facture.php">Facturation</a>
                <a href="<?php echo BASE_URL; ?>/modules/facturation/liste.php">Factures</a>
                
                <?php if ($role === 'Manager' || $role === 'Super Administrateur'): ?>
                    <a href="<?php echo BASE_URL; ?>/rapports/rapport-journalier.php">Rapport Jour</a>
                    <a href="<?php echo BASE_URL; ?>/rapports/rapport-mensuel.php">Rapport Mois</a>
                <?php endif; ?>
                
                <?php if ($role === 'Super Administrateur'): ?>
                    <a href="<?php echo BASE_URL; ?>/modules/admin/gestion-comptes.php">Admin</a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="logout-btn">Déconnexion</a>
            </div>
        </nav>
