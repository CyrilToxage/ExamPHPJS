<?php
// Démarrer la session
session_start();

// Définition des variables pour l'en-tête
$pageTitre = "Contact - Mon Site Web";
$metaDescription = "Contactez-nous via notre formulaire - Mon Site Web";

// Variables pour gérer les messages et les données du formulaire
$errors = [];
$success = false;
$formData = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'message' => ''
];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et nettoyer les données du formulaire
    $formData['nom'] = isset($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : '';
    $formData['prenom'] = isset($_POST['prenom']) ? trim(htmlspecialchars($_POST['prenom'])) : '';
    $formData['email'] = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
    $formData['message'] = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';

    // Validation du nom
    if (empty($formData['nom'])) {
        $errors['nom'] = "Le nom est obligatoire.";
    } elseif (strlen($formData['nom']) < 2) {
        $errors['nom'] = "Le nom doit comporter au moins 2 caractères.";
    } elseif (strlen($formData['nom']) > 255) {
        $errors['nom'] = "Le nom ne doit pas dépasser 255 caractères.";
    }

    // Validation du prénom (facultatif)
    if (!empty($formData['prenom'])) {
        if (strlen($formData['prenom']) < 2) {
            $errors['prenom'] = "Le prénom doit comporter au moins 2 caractères.";
        } elseif (strlen($formData['prenom']) > 255) {
            $errors['prenom'] = "Le prénom ne doit pas dépasser 255 caractères.";
        }
    }

    // Validation de l'email
    if (empty($formData['email'])) {
        $errors['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Veuillez entrer une adresse email valide.";
    }

    // Validation du message
    if (empty($formData['message'])) {
        $errors['message'] = "Le message est obligatoire.";
    } elseif (strlen($formData['message']) < 10) {
        $errors['message'] = "Le message doit comporter au moins 10 caractères.";
    } elseif (strlen($formData['message']) > 3000) {
        $errors['message'] = "Le message ne doit pas dépasser 3000 caractères.";
    }

    // Si aucune erreur, traiter le formulaire
    if (empty($errors)) {
        // Dans un cas réel, vous enverriez un email ou enregistreriez les données dans une base de données
        // Pour cet exercice, nous simulons simplement une réussite
        $success = true;

        // Réinitialiser les données du formulaire après succès
        $formData = [
            'nom' => '',
            'prenom' => '',
            'email' => '',
            'message' => ''
        ];
    }
}

// Inclusion de l'en-tête
include('header.php');
?>

<section class="hero">
    <div class="container">
        <h2>Contact</h2>
        <p>Utilisez le formulaire ci-dessous pour nous contacter.</p>
    </div>
</section>

<section class="contact-form">
    <div class="container">
        <?php if ($success): ?>
            <div class="message success">
                <p>Le formulaire a bien été envoyé !</p>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="contactForm">
            <div class="form-group">
                <label for="nom">Nom: <span class="required">*</span></label>
                <input type="text" id="nom" name="nom" required minlength="2" maxlength="255"
                    value="<?php echo htmlspecialchars($formData['nom']); ?>">
                <?php if (isset($errors['nom'])): ?>
                    <div class="error-message"><?php echo $errors['nom']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" minlength="2" maxlength="255"
                    value="<?php echo htmlspecialchars($formData['prenom']); ?>">
                <?php if (isset($errors['prenom'])): ?>
                    <div class="error-message"><?php echo $errors['prenom']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email: <span class="required">*</span></label>
                <input type="email" id="email" name="email" required maxlength="255"
                    value="<?php echo htmlspecialchars($formData['email']); ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="error-message"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="message">Message: <span class="required">*</span></label>
                <textarea id="message" name="message" required minlength="10" maxlength="3000"
                    rows="5"><?php echo htmlspecialchars($formData['message']); ?></textarea>
                <?php if (isset($errors['message'])): ?>
                    <div class="error-message"><?php echo $errors['message']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit">Envoyer</button>
            </div>

            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($errors)): ?>
                <div class="message error">
                    <p>Le formulaire n'a pas été envoyé !</p>
                </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<?php
// Inclusion du pied de page
include('footer.php');
?>