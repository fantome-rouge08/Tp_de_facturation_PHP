<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

if ($_SESSION['utilisateur']['role'] !== 'Manager' && $_SESSION['utilisateur']['role'] !== 'Super Administrateur') {
    die("Accès refusé.");
}

// Logique AJAX pour vérifier le produit
if (isset($_GET['action']) && $_GET['action'] === 'verifier_produit') {
    $code = $_GET['code'] ?? '';
    $produit = chercherProduit($code);
    echo json_encode(['existe' => $produit !== null, 'donnees' => $produit]);
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['code_barre']) && !empty($_POST['nom']) && is_numeric($_POST['prix_unitaire_ht'])) {
        $produit = [
            'code_barre' => $_POST['code_barre'],
            'nom' => $_POST['nom'],
            'prix_unitaire_ht' => (float)$_POST['prix_unitaire_ht'],
            'date_expiration' => $_POST['date_expiration'], // Format MM-JJ-AAAA demandé
            'stock' => (int)$_POST['stock'],
            'date_enregistrement' => date('Y-m-d')
        ];
        ajouterProduit($produit);
        $message = "Produit enregistré avec succès !";
    }
}
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="card">
    <h2>Scanner un produit</h2>
    
    <div style="position: relative; margin-bottom: 2rem;">
        <div id="scanner"></div>
        <div class="scanner-guide">
            <div class="laser"></div>
        </div>
    </div>

    <div id="resultat-scan">
        <!-- Rempli par JS -->
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Formulaire caché par défaut, affiché si le produit est inconnu -->
    <div id="form-enregistrement" style="display: none; border-top: 2px solid var(--border); pt-4: 2rem; margin-top: 2rem;">
        <h3 style="margin-bottom: 1.5rem;">Nouveau Produit détecté</h3>
        <form method="POST">
            <div class="form-group">
                <label>Code-barres</label>
                <input type="text" name="code_barre" id="input-code" readonly>
            </div>
            <div class="form-group">
                <label>Nom du produit</label>
                <input type="text" name="nom" required placeholder="Ex: Savon Le Coq">
            </div>
            <div class="form-group">
                <label>Prix unitaire HT (CDF)</label>
                <input type="number" name="prix_unitaire_ht" step="0.01" required placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Date d'expiration (MM-JJ-AAAA)</label>
                <input type="text" name="date_expiration" placeholder="MM-JJ-AAAA" pattern="\d{2}-\d{2}-\d{4}">
            </div>
            <div class="form-group">
                <label>Quantité initiale en stock</label>
                <input type="number" name="stock" required value="1">
            </div>
            <button type="submit">Enregistrer le produit</button>
        </form>
    </div>

    <!-- Affichage si produit déjà connu -->
    <div id="info-produit" style="display: none; background: #f1f5f9; padding: 2rem; border-radius: var(--radius-md); border-left: 5px solid var(--primary);">
        <h3 style="color: var(--primary); margin-bottom: 1rem;">Produit déjà référencé</h3>
        <div id="details-produit"></div>
        <button onclick="location.reload()" style="margin-top: 1.5rem; background: var(--secondary);">Scanner un autre produit</button>
    </div>
</div>

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
                facingMode: "user"
            }
        },
        decoder: {
            readers: ["ean_reader", "ean_8_reader", "code_128_reader", "upc_reader"]
        }
    }, function(err) {
        if (!err) Quagga.start();
    });
}

startScanner();

Quagga.onDetected(function(data) {
    let code = data.codeResult.code;
    Quagga.stop();
    
    // Bip sonore simulé ou feedback visuel
    document.getElementById('scanner').style.borderColor = "var(--primary)";
    
    fetch('?action=verifier_produit&code=' + code)
        .then(r => r.json())
        .then(res => {
            if (res.existe) {
                // Produit connu
                document.getElementById('form-enregistrement').style.display = 'none';
                document.getElementById('info-produit').style.display = 'block';
                document.getElementById('details-produit').innerHTML = `
                    <p><strong>Nom :</strong> ${res.donnees.nom}</p>
                    <p><strong>Code :</strong> ${res.donnees.code_barre}</p>
                    <p><strong>Prix :</strong> ${res.donnees.prix_unitaire_ht} CDF</p>
                    <p><strong>Stock :</strong> ${res.donnees.stock || 'N/A'}</p>
                    <p><strong>Expiration :</strong> ${res.donnees.date_expiration || 'Non définie'}</p>
                `;
            } else {
                // Produit inconnu
                document.getElementById('info-produit').style.display = 'none';
                document.getElementById('form-enregistrement').style.display = 'block';
                document.getElementById('input-code').value = code;
                // Scroll vers le formulaire
                document.getElementById('form-enregistrement').scrollIntoView({ behavior: 'smooth' });
            }
        });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
