// Intégration de QuaggaJS
// Assurez-vous d'inclure la bibliothèque via CDN dans votre HTML ou localement
// Pour cet exemple, nous simulons la réception du code barre

Quagga.init({
    inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector('#scanner')
    },
    decoder: {
        readers: ["ean_reader", "ean_8_reader", "code_128_reader"]
    }
}, function(err) {
    if (err) {
        console.error(err);
        return;
    }
    Quagga.start();
});

Quagga.onDetected(function(data) {
    var code = data.codeResult.code;
    console.log("Code scanné: " + code);
    // Envoyer au serveur PHP
    fetch('?action=chercher_produit&code=' + code)
        .then(response => response.json())
        .then(produit => {
            if (produit) {
                document.getElementById('resultat').innerText = "Produit: " + produit.nom + " - Prix: " + produit.prix_unitaire_ht + " CDF";
            } else {
                document.getElementById('resultat').innerText = "Produit inconnu.";
            }
        });
});
