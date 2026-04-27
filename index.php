<?php
require_once __DIR__ . '/config/config.php';
// On n'inclut pas auth/session.php directement car il redirige vers login.php 
// Et si on est pas connecté, login.php redirige ici, créant une boucle.
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="card" style="padding: 2rem;">
    <h2 style="margin-bottom: 1rem;">Tableau de bord</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
        Bienvenue, <strong style="color: var(--text-main);"><?php echo htmlspecialchars($_SESSION['utilisateur']['nom_complet']); ?></strong> !
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <div style="background: #f1f5f9; padding: 1.5rem; border-radius: 0.5rem; border-left: 4px solid var(--primary);">
            <div style="font-size: 0.875rem; color: var(--text-muted);">Rôle actuel</div>
            <div style="font-size: 1.25rem; font-weight: 600;"><?php echo htmlspecialchars($_SESSION['utilisateur']['role']); ?></div>
        </div>
        
        <div style="background: #f1f5f9; padding: 1.5rem; border-radius: 0.5rem; border-left: 4px solid var(--success);">
            <div style="font-size: 0.875rem; color: var(--text-muted);">Status</div>
            <div style="font-size: 1.25rem; font-weight: 600;">Actif</div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
