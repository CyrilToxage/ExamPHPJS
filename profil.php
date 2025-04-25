<?php
// Démarrer la session
session_start();

// Inclure le gestionnaire d'authentification et la sécurité
require_once 'config/gestionAuthentification.php';
require_once 'config/security.php';

// Définir les en-têtes de sécurité
definir_en_tetes_securite();

// Protéger cette page, rediriger si l'utilisateur n'est pas connecté
proteger_page();

// Récupérer les informations de l'utilisateur connecté
$utilisateur = obtenir_utilisateur_connecte();

// Si utilisateur non trouvé dans la base de données malgré la session, déconnecter et rediriger
if (!$utilisateur) {
    deconnecter_utilisateur();
    header('Location: connexion.php');
    exit;
}

// Définition des variables pour l'en-tête
$pageTitre = "Profil - " . htmlspecialchars($utilisateur['uti_pseudo']);
$metaDescription = "Gérez votre profil et vos informations personnelles";

// Générer un jeton CSRF pour le formulaire de déconnexion
$csrf_token = generer_csrf_token();

// Traiter la déconnexion si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deconnexion'])) {
    // Vérifier le jeton CSRF
    if (isset($_POST['csrf_token']) && verifier_csrf_token($_POST['csrf_token'])) {
        deconnecter_utilisateur();
        header('Location: index.php');
        exit;
    } else {
        $error = "Erreur de sécurité. Veuillez réessayer.";
    }
}

// Inclure l'en-tête
include('header.php');
?>

<section class="hero">
    <div class="container">
        <h2>Profil</h2>
        <p>Gérez vos informations personnelles et votre compte.</p>
    </div>
</section>

<section class="profil-content">
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="message error">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="profil-card">
            <h3>Informations personnelles</h3>
            
            <div class="profil-info">
                <div class="info-group">
                    <label>Pseudo:</label>
                    <p><?php echo htmlspecialchars($utilisateur['uti_pseudo']); ?></p>
                </div>
                
                <div class="info-group">
                    <label>Email:</label>
                    <p><?php echo htmlspecialchars($utilisateur['uti_email']); ?></p>
                </div>
                
                <div class="info-group">
                    <label>Date de connexion:</label>
                    <p>
                        <?php 
                        if (isset($_SESSION['dateConnexion'])) {
                            echo date('d/m/Y H:i', $_SESSION['dateConnexion']);
                        } else {
                            echo "Information non disponible";
                        }
                        ?>
                    </p>
                </div>
            </div>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="deconnexion-form">
                <input type="hidden" name="deconnexion" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="btn-deconnexion">Déconnexion</button>
            </form>
        </div>
    </div>
</section>

<?php
// Inclure le pied de page
include('footer.php');
?>