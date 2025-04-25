<?php
// Démarrer la session
session_start();

// Inclure le gestionnaire d'authentification et la sécurité
require_once 'config/gestionAuthentification.php';
require_once 'config/security.php';

// Définir les en-têtes de sécurité
definir_en_tetes_securite();

// Vérifier si l'utilisateur est bien connecté
if (!est_connecte()) {
    header('Location: index.php');
    exit;
}

// Vérifier que la requête est bien POST pour éviter les déconnexions par GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si l'accès est direct, rediriger vers une page de confirmation
    $csrf_token = generer_csrf_token();
    
    include('header.php');
    ?>
    
    <section class="hero">
        <div class="container">
            <h2>Déconnexion</h2>
            <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
        </div>
    </section>
    
    <section class="deconnexion-content">
        <div class="container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="deconnexion-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="form-group">
                    <button type="submit" class="btn-deconnexion">Confirmer la déconnexion</button>
                </div>
                <div class="form-group">
                    <a href="index.php" class="btn-annuler">Annuler</a>
                </div>
            </form>
        </div>
    </section>
    
    <?php
    include('footer.php');
    exit;
}

// Vérifier le jeton CSRF
if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'])) {
    // Erreur CSRF, rediriger avec un message d'erreur
    $_SESSION['error_message'] = "Erreur de sécurité. Veuillez réessayer.";
    header('Location: index.php');
    exit;
}

// Déconnecter l'utilisateur
deconnecter_utilisateur();

// Rediriger vers la page d'accueil
header("Location: index.php");
exit;
?>