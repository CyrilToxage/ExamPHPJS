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
$pageTitre = "Inscription - Mon Site Web";
$metaDescription = "Créez votre compte pour accéder à toutes les fonctionnalités";

// Inclure le fichier de configuration
require_once 'config/db.php';

// Générer un jeton CSRF
$csrf_token = generer_csrf_token();

// Variables pour gérer les messages et les données du formulaire
$errors = [];
$success = false;
$formData = [
    'inscription_pseudo' => '',
    'inscription_email' => '',
    'inscription_motDePasse' => '',
    'inscription_motDePasse_confirmation' => ''
];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier le jeton CSRF
    if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'])) {
        $errors['csrf'] = "Erreur de sécurité. Veuillez réessayer.";
    } else {
        // Récupérer et nettoyer les données du formulaire
        $formData['inscription_pseudo'] = isset($_POST['inscription_pseudo']) ? nettoyer_donnee($_POST['inscription_pseudo']) : '';
        $formData['inscription_email'] = isset($_POST['inscription_email']) ? filter_var($_POST['inscription_email'], FILTER_SANITIZE_EMAIL) : '';
        $formData['inscription_motDePasse'] = isset($_POST['inscription_motDePasse']) ? $_POST['inscription_motDePasse'] : '';
        $formData['inscription_motDePasse_confirmation'] = isset($_POST['inscription_motDePasse_confirmation']) ? $_POST['inscription_motDePasse_confirmation'] : '';
        
        // Validation du pseudo
        if (empty($formData['inscription_pseudo'])) {
            $errors['inscription_pseudo'] = "Le pseudo est obligatoire.";
        } elseif (strlen($formData['inscription_pseudo']) < 2) {
            $errors['inscription_pseudo'] = "Le pseudo doit comporter au moins 2 caractères.";
        } elseif (strlen($formData['inscription_pseudo']) > 255) {
            $errors['inscription_pseudo'] = "Le pseudo ne doit pas dépasser 255 caractères.";
        } else {
            // Vérifier si le pseudo existe déjà dans la base de données
            try {
                $pdo = getConnexion();
                $stmt = $pdo->prepare("SELECT uti_id FROM t_utilisateur_uti WHERE uti_pseudo = ?");
                $stmt->execute([$formData['inscription_pseudo']]);
                
                if ($stmt->rowCount() > 0) {
                    $errors['inscription_pseudo'] = "Ce pseudo est déjà utilisé.";
                }
            } catch (PDOException $e) {
                $errors['db'] = "Erreur de base de données: " . $e->getMessage();
            }
        }
        
        // Validation de l'email
        if (empty($formData['inscription_email'])) {
            $errors['inscription_email'] = "L'email est obligatoire.";
        } elseif (!valider_email($formData['inscription_email'])) {
            $errors['inscription_email'] = "Veuillez entrer une adresse email valide.";
        } else {
            // Vérifier si l'email existe déjà dans la base de données
            try {
                $pdo = getConnexion();
                $stmt = $pdo->prepare("SELECT uti_id FROM t_utilisateur_uti WHERE uti_email = ?");
                $stmt->execute([$formData['inscription_email']]);
                
                if ($stmt->rowCount() > 0) {
                    $errors['inscription_email'] = "Cette adresse email est déjà utilisée.";
                }
            } catch (PDOException $e) {
                $errors['db'] = "Erreur de base de données: " . $e->getMessage();
            }
        }
        
        // Validation du mot de passe
        if (empty($formData['inscription_motDePasse'])) {
            $errors['inscription_motDePasse'] = "Le mot de passe est obligatoire.";
        } elseif (strlen($formData['inscription_motDePasse']) < 8) {
            $errors['inscription_motDePasse'] = "Le mot de passe doit comporter au moins 8 caractères.";
        } elseif (strlen($formData['inscription_motDePasse']) > 72) {
            $errors['inscription_motDePasse'] = "Le mot de passe ne doit pas dépasser 72 caractères.";
        } elseif (!preg_match('/[A-Z]/', $formData['inscription_motDePasse']) || 
                 !preg_match('/[a-z]/', $formData['inscription_motDePasse']) || 
                 !preg_match('/[0-9]/', $formData['inscription_motDePasse'])) {
            $errors['inscription_motDePasse'] = "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.";
        }
        
        // Validation de la confirmation du mot de passe
        if (empty($formData['inscription_motDePasse_confirmation'])) {
            $errors['inscription_motDePasse_confirmation'] = "Veuillez confirmer votre mot de passe.";
        } elseif ($formData['inscription_motDePasse'] !== $formData['inscription_motDePasse_confirmation']) {
            $errors['inscription_motDePasse_confirmation'] = "Les mots de passe ne correspondent pas.";
        }
        
        // Si aucune erreur, traiter l'inscription
        if (empty($errors)) {
            try {
                $pdo = getConnexion();
                
                // Hasher le mot de passe avec un coût plus élevé pour plus de sécurité
                $motDePasseHash = password_hash($formData['inscription_motDePasse'], PASSWORD_DEFAULT, ['cost' => 12]);
                
                // Préparer la requête d'insertion
                $stmt = $pdo->prepare("
                    INSERT INTO t_utilisateur_uti 
                    (uti_pseudo, uti_email, uti_motdepasse, uti_compte_active)
                    VALUES (?, ?, ?, 1)
                ");
                
                // Exécuter la requête
                $stmt->execute([
                    $formData['inscription_pseudo'],
                    $formData['inscription_email'],
                    $motDePasseHash
                ]);
                
                // Si l'insertion a réussi
                $success = true;
                
                // Réinitialiser les données du formulaire après succès
                $formData = [
                    'inscription_pseudo' => '',
                    'inscription_email' => '',
                    'inscription_motDePasse' => '',
                    'inscription_motDePasse_confirmation' => ''
                ];
                
                // Régénérer le jeton CSRF
                $csrf_token = regenerer_csrf_token();
            } catch (PDOException $e) {
                $errors['db'] = "Erreur lors de l'inscription: " . $e->getMessage();
            }
        }
    }
    
    // Régénérer le jeton CSRF après traitement
    if (!$success) {
        $csrf_token = regenerer_csrf_token();
    }
}

// Inclure l'en-tête
include('header.php');
?>

<section class="hero">
    <div class="container">
        <h2>Inscription</h2>
        <p>Créez votre compte pour accéder à toutes les fonctionnalités.</p>
    </div>
</section>

<section class="inscription-form">
    <div class="container">
        <?php if ($success): ?>
            <div class="message success">
                <p>Votre compte a été créé avec succès ! Vous pouvez maintenant vous <a href="connexion.php">connecter</a>.</p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($errors['db']) || isset($errors['csrf'])): ?>
            <div class="message error">
                <p><?php echo isset($errors['csrf']) ? htmlspecialchars($errors['csrf']) : htmlspecialchars($errors['db']); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="inscriptionForm">
                <!-- Champ caché pour le jeton CSRF -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div class="form-group">
                    <label for="inscription_pseudo">Pseudo: <span class="required">*</span></label>
                    <input type="text" id="inscription_pseudo" name="inscription_pseudo" required minlength="2" maxlength="255" 
                           value="<?php echo htmlspecialchars($formData['inscription_pseudo']); ?>">
                    <?php if (isset($errors['inscription_pseudo'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['inscription_pseudo']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="inscription_email">Email: <span class="required">*</span></label>
                    <input type="email" id="inscription_email" name="inscription_email" required 
                           value="<?php echo htmlspecialchars($formData['inscription_email']); ?>">
                    <?php if (isset($errors['inscription_email'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['inscription_email']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="inscription_motDePasse">Mot de passe: <span class="required">*</span></label>
                    <input type="password" id="inscription_motDePasse" name="inscription_motDePasse" required minlength="8" maxlength="72">
                    <small>Le mot de passe doit contenir au moins 8 caractères, incluant majuscules, minuscules et chiffres.</small>
                    <?php if (isset($errors['inscription_motDePasse'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['inscription_motDePasse']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="inscription_motDePasse_confirmation">Confirmer le mot de passe: <span class="required">*</span></label>
                    <input type="password" id="inscription_motDePasse_confirmation" name="inscription_motDePasse_confirmation" required minlength="8" maxlength="72">
                    <?php if (isset($errors['inscription_motDePasse_confirmation'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['inscription_motDePasse_confirmation']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit">S'inscrire</button>
                </div>
                
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($errors) && !isset($errors['csrf']) && !isset($errors['db'])): ?>
                    <div class="message error">
                        <p>L'inscription a échoué ! Veuillez corriger les erreurs dans le formulaire.</p>
                    </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>
        
        <div class="form-links">
            <p>Vous avez déjà un compte ? <a href="connexion.php">Connexion</a></p>
        </div>
    </div>
</section>

<?php
// Inclure le pied de page
include('footer.php');
?>