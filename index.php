<?php
// Démarrer la session
session_start();

// Inclure le gestionnaire d'authentification
require_once 'config/gestionAuthentification.php';

// Définition des variables pour l'en-tête
$pageTitre = "Accueil - Mon Site Web";
$metaDescription = "Bienvenue sur la page d'accueil de mon site web dynamique avec PHP";

// Inclusion de l'en-tête
include('header.php');
?>


<section class="hero">
    <div class="container" data-aos="fade-up">
        <h2>Accueil</h2>
        <?php if (est_connecte()): ?>
            <p>Bienvenue <?php echo htmlspecialchars($_SESSION['utilisateurPseudo']); ?> !</p>
            <p>Vous êtes connecté et avez accès à toutes les fonctionnalités du site.</p>
            <p><a href="profil.php">Accéder à votre profil</a></p>
        <?php else: ?>
            <p>Bienvenue sur mon site web dynamique développé avec PHP!</p>
            <p><a href="connexion.php">Connectez-vous</a> ou <a href="inscription.php">inscrivez-vous</a> pour accéder à toutes les fonctionnalités.</p>
        <?php endif; ?>
    </div>
</section>

<section class="content">
    <div class="container" data-aos="fade-up" data-aos-delay="200">
        <h3>À propos du projet</h3>
        <p>Ce projet progressif démontre l'utilisation des inclusions PHP pour créer un modèle 
        de pages dynamiques avec en-tête et pied de page communs.</p>
        
        <h3>Fonctionnalités</h3>
        <ul>
            <li data-aos="fade-right" data-aos-delay="300">Pages dynamiques avec PHP</li>
            <li data-aos="fade-right" data-aos-delay="400">Gestion des formulaires</li>
            <li data-aos="fade-right" data-aos-delay="500">Base de données utilisateurs</li>
            <li data-aos="fade-right" data-aos-delay="600">Sessions et cookies</li>
            <li data-aos="fade-right" data-aos-delay="700">Sécurisation de l'application</li>
            <li data-aos="fade-right" data-aos-delay="800">Intégration JavaScript et API</li>
        </ul>
    </div>
</section>

<section class="faq-section">
    <div class="container" data-aos="fade-up" data-aos-delay="400">
        <h3>Foire aux questions</h3>
        <div class="accordion" id="faq-accordion">
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="500">
                <div class="accordion-header">
                    <h3>Comment créer un compte ?</h3>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-content-inner">
                        <p>Pour créer un compte, cliquez sur le lien "Inscription" dans le menu en haut de la page. Remplissez le formulaire avec vos informations personnelles et choisissez un mot de passe sécurisé. Une fois inscrit, vous pourrez vous connecter et accéder à toutes les fonctionnalités de notre site.</p>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="600">
                <div class="accordion-header">
                    <h3>Comment se connecter à son compte ?</h3>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-content-inner">
                        <p>Pour vous connecter, cliquez sur le lien "Connexion" dans le menu en haut de la page. Entrez votre pseudo et votre mot de passe. Si vous avez oublié votre mot de passe, contactez l'administrateur du site.</p>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="700">
                <div class="accordion-header">
                    <h3>Comment nous contacter ?</h3>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-content-inner">
                        <p>Vous pouvez nous contacter en utilisant notre <a href="contact.php">formulaire de contact</a>. Nous nous efforçons de répondre à toutes les demandes dans un délai de 48 heures.</p>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="800">
                <div class="accordion-header">
                    <h3>Le site est-il sécurisé ?</h3>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-content-inner">
                        <p>Oui, notre site utilise des techniques modernes de sécurisation comme la protection contre les injections SQL, le cryptage des mots de passe et la protection contre les attaques XSS et CSRF. Vos données sont en sécurité avec nous.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Inclusion du pied de page
include('footer.php');
?>