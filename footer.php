</main>
<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> - Mon Site Web</p>
        <div class="footer-links">
            <a href="index.php">Accueil</a>
            <a href="contact.php">Contact</a>
            <a href="#">Mentions légales</a>
        </div>
    </div>
</footer>
<!-- Script JavaScript principal -->
<script src="assets/js/main.js"></script>

<?php
// Chargement conditionnel des scripts en fonction de la page actuelle
$page = basename($_SERVER['PHP_SELF']);
?>

<!-- Script pour l'accordéon - uniquement sur la page d'accueil -->
<?php if ($page === 'index.php'): ?>
    <script src="assets/js/accordion.js"></script>
<?php endif; ?>

<!-- Script pour les blagues - uniquement sur la page blagues -->
<?php if ($page === 'blagues.php'): ?>
    <script src="assets/js/jokes.js"></script>
<?php endif; ?>

<!-- Scripts pour AOS (Animate On Scroll) - sur toutes les pages -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    // Initialisation de AOS
    document.addEventListener('DOMContentLoaded', function () {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    });
</script>
</body>

</html>