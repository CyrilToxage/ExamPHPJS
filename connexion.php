<?php
// Démarrer la session
session_start();

// Inclure le gestionnaire d'authentification et la sécurité
require_once 'config/gestionAuthentification.php';
require_once 'config/security.php';

// Définir les en-têtes de sécurité
definir_en_tetes_securite();

// Si l'utilisateur est déjà connecté, le rediriger vers la page de profil
if (est_connecte()) {
    header('Location: profil.php');
    exit;
}

// Définition des variables pour l'en-tête
$pageTitre = "Connexion - Mon Site Web";
$metaDescription = "Connectez-vous à votre compte pour accéder à toutes les fonctionnalités";

// Inclure le fichier de configuration
require_once 'config/db.php';

// Générer un jeton CSRF
$csrf_token = generer_csrf_token();

// Variables pour gérer les messages et les données du formulaire
$errors = [];
$formData = [
    'connexion_pseudo' => '',
];

// Vérifier si la limite de tentatives de connexion est atteinte
if (limite_tentatives_connexion($_SERVER['REMOTE_ADDR'])) {
    $errors['limite'] = "Trop de tentatives de connexion. Veuillez réessayer plus tard.";
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && !limite_tentatives_connexion($_SERVER['REMOTE_ADDR'])) {
    // Vérifier le jeton CSRF
    if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'])) {
        $errors['csrf'] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        // Vérifier les en-têtes
        verifier_en_tetes();
        
        // Récupérer et nettoyer les données du formulaire
        $formData['connexion_pseudo'] = isset($_POST['connexion_pseudo']) ? nettoyer_donnee($_POST['connexion_pseudo']) : '';
        $connexion_motDePasse = isset($_POST['connexion_motDePasse']) ? $_POST['connexion_motDePasse'] : '';
        
        // Validation des champs
        if (empty($formData['connexion_pseudo'])) {
            $errors['connexion_pseudo'] = "Le pseudo est obligatoire.";
        } elseif (strlen($formData['connexion_pseudo']) < 2) {
            $errors['connexion_pseudo'] = "Le pseudo doit comporter au moins 2 caractères.";
        } elseif (strlen($formData['connexion_pseudo']) > 255) {
            $errors['connexion_pseudo'] = "Le pseudo ne doit pas dépasser 255 caractères.";
        }
        
        if (empty($connexion_motDePasse)) {
            $errors['connexion_motDePasse'] = "Le mot de passe est obligatoire.";
        } elseif (strlen($connexion_motDePasse) < 8) {
            $errors['connexion_motDePasse'] = "Le mot de passe doit comporter au moins 8 caractères.";
        } elseif (strlen($connexion_motDePasse) > 72) {
            $errors['connexion_motDePasse'] = "Le mot de passe ne doit pas dépasser 72 caractères.";
        }
        
        // Si aucune erreur de validation, vérifier l'authentification
        if (empty($errors)) {
            try {
                $pdo = getConnexion();
                
                // Rechercher l'utilisateur par son pseudo
                $stmt = $pdo->prepare("SELECT uti_id, uti_pseudo, uti_motdepasse, uti_compte_active FROM t_utilisateur_uti WHERE uti_pseudo = ?");
                $stmt->execute([$formData['connexion_pseudo']]);
                $user = $stmt->fetch();
                
                // Vérifier si l'utilisateur existe et si le mot de passe est correct
                if ($user && password_verify($connexion_motDePasse, $user['uti_motdepasse'])) {
                    // Vérifier si le compte est actif
                    if ($user['uti_compte_active']) {
                        // Réinitialiser le compteur de tentatives de connexion
                        reinitialiser_tentatives_connexion();
                        
                        // Régénérer l'ID de session pour éviter les attaques par fixation de session
                        session_regenerate_id(true);
                        
                        // Connecter l'utilisateur
                        if (connecter_utilisateur($user['uti_id'], $user['uti_pseudo'])) {
                            // Régénérer le jeton CSRF
                            regenerer_csrf_token();
                            
                            // Si une URL de retour est définie, rediriger vers cette URL
                            if (isset($_SESSION['url_retour'])) {
                                $url_retour = $_SESSION['url_retour'];
                                unset($_SESSION['url_retour']);
                                header('Location: ' . $url_retour);
                            } else {
                                // Sinon, rediriger vers la page de profil
                                header('Location: profil.php');
                            }
                            exit;
                        } else {
                            $errors['login'] = "Erreur lors de la connexion. Veuillez réessayer.";
                        }
                    } else {
                        $errors['login'] = "Votre compte n'est pas activé. Veuillez contacter l'administrateur.";
                    }
                } else {
                    // Incrémenter le compteur de tentatives de connexion
                    incrementer_tentatives_connexion();
                    
                    $errors['login'] = "Pseudo ou mot de passe incorrect.";
                }
            } catch (PDOException $e) {
                $errors['db'] = "Erreur de base de données: " . $e->getMessage();
            }
        }
    }
    
    // Régénérer le jeton CSRF après traitement
    $csrf_token = regenerer_csrf_token();
}

// Inclure l'en-tête
include('header.php');
?>

<section class="hero">
    <div class="container">
        <h2>Connexion</h2>
        <p>Connectez-vous pour accéder à votre compte.</p>
    </div>
</section>

<section class="connexion-form">
    <div class="container">
        <?php if (isset($errors['login']) || isset($errors['db']) || isset($errors['csrf']) || isset($errors['limite'])): ?>
            <div class="message error">
                <p><?php 
                    if (isset($errors['limite'])) {
                        echo htmlspecialchars($errors['limite']);
                    } elseif (isset($errors['csrf'])) {
                        echo htmlspecialchars($errors['csrf']);
                    } elseif (isset($errors['login'])) {
                        echo htmlspecialchars($errors['login']);
                    } else {
                        echo htmlspecialchars($errors['db']);
                    }
                ?></p>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="connexionForm">
            <!-- Champ caché pour le jeton CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="form-group">
                <label for="connexion_pseudo">Pseudo: <span class="required">*</span></label>
                <input type="text" id="connexion_pseudo" name="connexion_pseudo" required minlength="2" maxlength="255" 
                       value="<?php echo htmlspecialchars($formData['connexion_pseudo']); ?>">
                <?php if (isset($errors['connexion_pseudo'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($errors['connexion_pseudo']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="connexion_motDePasse">Mot de passe: <span class="required">*</span></label>
                <input type="password" id="connexion_motDePasse" name="connexion_motDePasse" required minlength="8" maxlength="72">
                <?php if (isset($errors['connexion_motDePasse'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($errors['connexion_motDePasse']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit">Se connecter</button>
            </div>
        </form>
        
        <div class="form-links">
            <p>Vous n'avez pas de compte ? <a href="inscription.php">Inscription</a></p>
        </div>
    </div>
</section>

<?php
// Inclure le pied de page
include('footer.php');
?>