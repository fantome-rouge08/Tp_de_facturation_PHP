<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

// Initialisation panier si inexistant
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Logique AJAX pour chercher
if (isset($_GET['action']) && $_GET['action'] === 'chercher_produit') {
    $code = $_GET['code'] ?? '';
    echo json_encode(chercherProduit($code));
    exit;
}

// Logique ajout article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $produit = chercherProduit($_POST['code_barre']);
    if ($produit && $_POST['quantite'] > 0) {
        $_SESSION['panier'][] = [
            'code_barre' => $produit['code_barre'],
            'nom' => $produit['nom'],
            'prix_unitaire_ht' => $produit['prix_unitaire_ht'],
            'quantite' => (int)$_POST['quantite'],
            'sous_total_ht' => $produit['prix_unitaire_ht'] * (int)$_POST['quantite']
        ];
    }
}

// Logique validation facture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider'])) {
    $total_ht = 0;
    foreach ($_SESSION['panier'] as $item) $total_ht += $item['sous_total_ht'];
    $tva = $total_ht * TVA_RATE;
    
    $facture = [
        'id_facture' => genererIdFacture(),
        'date' => date('Y-m-d'),
        'heure' => date('H:i:s'),
        'caissier' => $_SESSION['utilisateur']['identifiant'],
        'articles' => $_SESSION['panier'],
        'total_ht' => $total_ht,
        'tva' => $tva,
        'total_ttc' => $total_ht + $tva
    ];
    sauvegarderFacture($facture);
    $_SESSION['panier'] = []; // Vider le panier
    $_SESSION['message_succes'] = "Facture validée avec succès !";
    header('Location: nouvelle-facture.php');
    exit;
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h2>Nouvelle Facture</h2>
<?php if (isset($_SESSION['message_succes'])): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px;">
        <?php echo $_SESSION['message_succes']; unset($_SESSION['message_succes']); ?>
    </div>
<?php endif; ?>
<div style="position: relative; margin-bottom: 2rem;">
    <div id="scanner"></div>
    <!-- Guide visuel pour le scan -->
    <div class="scanner-guide">
        <div class="laser"></div>
    </div>
    <div style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.6); color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; pointer-events: none; z-index: 10;">
        Alignez le code-barres avec la ligne rouge
    </div>
</div>
<div id="resultat"></div>

<div style="margin-bottom: 2rem; background: #fffbeb; border-left: 4px solid var(--accent); padding: 1rem; border-radius: 8px;">
    <p style="font-size: 0.9rem; color: #92400e; margin: 0;">
        <strong>💡 Conseil :</strong> Pour un meilleur scan sur PC, approchez le produit doucement de la caméra jusqu'à ce qu'il soit net. Assurez-vous d'avoir un bon éclairage.
    </p>
</div>


<form method="POST" id="form-ajout">
    <!-- Champ pour saisie manuelle -->
    <input type="text" name="code_barre" id="code_barre" placeholder="Scanner ou saisir le code-barres" required>
    <input type="number" name="quantite" placeholder="Quantité" required>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<h3>Panier</h3>
<ul>
    <?php foreach ($_SESSION['panier'] as $item): ?>
        <li><?php echo $item['nom']; ?> x <?php echo $item['quantite']; ?> = <?php echo $item['sous_total_ht']; ?> CDF</li>
    <?php endforeach; ?>
</ul>

<form method="POST">
    <button type="submit" name="valider">Valider la facture</button>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
function startScanner() {
    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector('#scanner'),
            constraints: {
                width: { min: 640 },
                height: { min: 480 },
                aspectRatio: { min: 1.33, max: 1.77 },
                facingMode: "user" // Pour PC, on utilise la caméra frontale
            }
        },
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        decoder: {
            readers: [
                "ean_reader",      // Très commun pour les produits (EAN-13)
                "ean_8_reader",    // Variantes plus courtes
                "code_128_reader", // Courant dans la logistique
                "code_39_reader",
                "upc_reader",
                "upc_e_reader"
            ],
            multiple: false
        },
        locate: true
    }, function(err) {
        if (err) {
            console.error(err);
            document.getElementById('scanner').innerHTML = `<div style="padding: 20px; color: #ef4444; text-align: center;">Erreur caméra : ${err.message}</div>`;
            return;
        }
        Quagga.start();
    });
}

startScanner();

// Filtre pour éviter les faux positifs (vérifie la validité du code scanné)
Quagga.onDetected(function(data) {
    if (!data.codeResult || !data.codeResult.code) return;
    
    var code = data.codeResult.code;
    
    // Si c'est un EAN-13, on vérifie qu'il a bien 13 chiffres
    // (Optionnel : vous pouvez ajouter une validation de checksum ici)
    
    Quagga.stop();
    
    fetch('?action=chercher_produit&code=' + code)
        .then(r => r.json())
        .then(p => {
            if (p) {
                document.getElementById('resultat').innerHTML = `
                    <div style="background: var(--primary); color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; animation: slideDown 0.3s ease;">
                        <div style="font-size: 0.8rem; opacity: 0.9;">Produit identifié</div>
                        <div style="font-size: 1.1rem; font-weight: 700;">${p.nom}</div>
                        <div style="font-size: 1rem;">${p.prix_unitaire_ht} CDF</div>
                    </div>
                `;
                document.getElementById('code_barre').value = p.code_barre;
            } else {
                document.getElementById('resultat').innerHTML = `
                    <div style="background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="font-size: 0.8rem; opacity: 0.9;">Code scanné : ${code}</div>
                        <div style="font-weight: 700;">Produit inconnu dans la base</div>
                        <div style="font-size: 0.8rem; margin-top: 5px;">Vérifiez que le produit est enregistré.</div>
                    </div>
                `;
            }
            setTimeout(startScanner, 2500); // Délai pour laisser le temps de voir le résultat
        });
});
</script>


<?php include __DIR__ . '/../../includes/footer.php'; ?>

