<?php
// Démarrer la session
session_start();

// Inclure le gestionnaire d'authentification et la sécurité
require_once 'config/gestionAuthentification.php';
require_once 'config/security.php';

// Définir les en-têtes de sécurité
definir_en_tetes_securite();

// Définition des variables pour l'en-tête
$pageTitre = "Blagues - Mon Site Web";
$metaDescription = "Découvrez des blagues aléatoires sur la programmation et d'autres thèmes";

// Inclusion de l'en-tête
include('header.php');
?>

<section class="hero">
    <div class="container" data-aos="fade-up">
        <h2>Générateur de blagues</h2>
        <p>Cliquez sur les boutons ci-dessous pour générer des blagues aléatoires.</p>
    </div>
</section>

<section class="jokes-section">
    <div class="container" data-aos="fade-up" data-aos-delay="200">
        <div class="joke-controls">
            <div class="joke-categories">
                <label for="joke-category">Catégorie:</label>
                <select id="joke-category">
                    <option value="Any">Toutes</option>
                    <option value="Programming">Programmation</option>
                    <option value="Misc">Divers</option>
                    <option value="Dark">Humour noir</option>
                    <option value="Pun">Jeux de mots</option>
                    <option value="Spooky">Effrayant</option>
                    <option value="Christmas">Noël</option>
                </select>
            </div>
            
            <div class="joke-language">
                <label>Langue:</label>
                <div class="language-options">
                    <label>
                        <input type="radio" name="language" value="en" checked> Anglais
                    </label>
                    <label>
                        <input type="radio" name="language" value="fr"> Français
                    </label>
                </div>
            </div>
            
            <button id="get-joke" class="btn-joke">Générer une blague</button>
        </div>
        
        <div class="joke-display" data-aos="fade-up" data-aos-delay="300">
            <div id="joke-content" class="joke-content">
                <p>Cliquez sur le bouton pour générer une blague...</p>
            </div>
            <div id="joke-loader" class="joke-loader hidden">
                <div class="spinner"></div>
                <p>Chargement de la blague...</p>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/jokes.js"></script>

<?php
// Inclusion du pied de page
include('footer.php');
?>