<?php
// Vérification si les variables sont définies
if (!isset($pageTitre)) {
    $pageTitre = "Mon Site";
}
if (!isset($metaDescription)) {
    $metaDescription = "Description par défaut du site";
}

// Inclure le gestionnaire d'authentification s'il n'est pas déjà inclus
if (!function_exists('est_connecte')) {
    require_once 'config/gestionAuthentification.php';
}

// Détermine la page active
$pageActuelle = $_SERVER['PHP_SELF'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <title><?php echo htmlspecialchars($pageTitre); ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <!-- Lien CSS pour AOS (Animate On Scroll) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
</head>

<body>
    <header>
        <div class="logo">
            <h1>Mon Site Web</h1>
        </div>
        <nav>
            <ul>
                <?php
                // Fonction pour ajouter la classe active
                function isActive($lien)
                {
                    global $pageActuelle;
                    $page = basename($pageActuelle);
                    if ($lien === $page) {
                        return ' class="active"';
                    } elseif ($lien === 'index.php' && $page === '') {
                        return ' class="active"';
                    }
                    return '';
                }
                ?>
                <li<?php echo isActive('index.php'); ?>><a href="index.php">Accueil</a></li>
                    <li<?php echo isActive('contact.php'); ?>><a href="contact.php">Contact</a></li>
                        <li<?php echo isActive('blagues.php'); ?>><a href="blagues.php">Blagues</a></li>
                            <?php if (est_connecte()): ?>
                                <li<?php echo isActive('profil.php'); ?>><a href="profil.php">Profil</a></li>
                                    <li><a href="deconnexion.php">Déconnexion</a></li>
                                <?php else: ?>
                                    <li<?php echo isActive('connexion.php'); ?>><a href="connexion.php">Connexion</a></li>
                                        <li<?php echo isActive('inscription.php'); ?>><a
                                                href="inscription.php">Inscription</a></li>
                                        <?php endif; ?>
            </ul>
        </nav>
    </header>