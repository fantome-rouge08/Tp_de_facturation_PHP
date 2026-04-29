


SYSTÈME DE FACTURATION EN PHP 

1. PRÉREQUIS

Avant de lancer le projet, assurez-vous d’avoir installé :

- WampServer (Windows) ou XAMPP
- Un navigateur web (Chrome recommandé)
- PHP version 7 ou supérieure

---

2. INSTALLATION DU PROJET

3. Extraire le dossier du projet "facturation"

4. Copier le dossier dans le répertoire :
   
   - Pour WampServer :
     C:/wamp64/www/
   
   - Pour XAMPP :
     C:/xampp/htdocs/

5. Vérifier que la structure du projet est intacte

---

3. LANCEMENT DU SERVEUR

4. Démarrer WampServer ou XAMPP

5. Vérifier que le serveur est en état "vert" (Wamp)

6. Ouvrir un navigateur web

7. Accéder à l’application via l’URL :
   
   http://localhost/Tp_de_facturation_PHO/

---

4. IDENTIFIANTS PAR DÉFAUT

Compte super administrateur :

- Nom d’utilisateur : admin
- Mot de passe : password 

---

5. STRUCTURE DES DONNÉES

Les données sont stockées dans des fichiers JSON :

- data/produits.json → Produits
- data/factures.json → Factures
- data/utilisateurs.json → Comptes utilisateurs

Important :
Ne pas supprimer ces fichiers, sinon les données seront perdues.

---

6. FONCTIONNALITÉS PRINCIPALES

- Authentification avec gestion des rôles
- Enregistrement des produits
- Création de factures
- Calcul automatique (HT, TVA, TTC)
- Mise à jour automatique du stock
- Gestion des comptes utilisateurs
- Génération de rapports

---

7. SCANNER DE CODE-BARRES

Le fichier scanner.js est une base.

Pour activer le scan réel :

- intégrer une bibliothèque JavaScript comme ZXing ou QuaggaJS
- autoriser l’accès à la caméra dans le navigateur

---

8. REMARQUES IMPORTANTES

- Ce projet utilise PHP procédural uniquement
- Aucune base de données n’est utilisée
- Toutes les données sont stockées en fichiers JSON

9. PROBLÈMES COURANTS

Problème : Page introuvable
Solution : Vérifier que le projet est bien dans "www" ou "htdocs"

Problème : Données non enregistrées
Solution : Vérifier les permissions des dossiers "data/"

Problème : Session ne fonctionne pas
Solution : Vérifier que session_start() est activé

