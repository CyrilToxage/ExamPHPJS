<?php
// Fichier: config/security.php
// Fonctions de sécurité pour l'application

/**
 * Génère un jeton CSRF pour protéger les formulaires
 * 
 * @return string Le jeton CSRF
 */
function generer_csrf_token() {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Si le token n'existe pas encore, en générer un nouveau
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie si le jeton CSRF est valide
 * 
 * @param string $token Le jeton CSRF à vérifier
 * @return bool Retourne true si le jeton est valide
 */
function verifier_csrf_token($token) {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Si le token n'est pas défini en session, c'est une erreur
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    // Vérifier que le token transmis correspond au token en session
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Régénère le jeton CSRF après utilisation pour éviter les attaques de replay
 */
function regenerer_csrf_token() {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Générer un nouveau token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie si le nombre de tentatives de connexion a dépassé la limite
 * 
 * @param string $ip Adresse IP de l'utilisateur
 * @return bool Retourne true si la limite est dépassée
 */
function limite_tentatives_connexion($ip) {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Initialiser le compteur et le timestamp si nécessaire
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_time'] = time();
    }
    
    // Si plus de 30 minutes se sont écoulées depuis la dernière tentative, réinitialiser le compteur
    if (time() - $_SESSION['login_time'] > 1800) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_time'] = time();
    }
    
    // Vérifier si le nombre de tentatives est supérieur à 5
    return $_SESSION['login_attempts'] >= 5;
}

/**
 * Incrémente le compteur de tentatives de connexion
 */
function incrementer_tentatives_connexion() {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Initialiser le compteur et le timestamp si nécessaire
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_time'] = time();
    }
    
    // Incrémenter le compteur et mettre à jour le timestamp
    $_SESSION['login_attempts']++;
    $_SESSION['login_time'] = time();
}

/**
 * Réinitialise le compteur de tentatives de connexion
 */
function reinitialiser_tentatives_connexion() {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Réinitialiser le compteur
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_time'] = time();
}

/**
 * Nettoie et valide une entrée utilisateur
 * 
 * @param string $data Donnée à nettoyer
 * @return string Donnée nettoyée
 */
function nettoyer_donnee($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Valide une adresse email
 * 
 * @param string $email Email à valider
 * @return bool Retourne true si l'email est valide
 */
function valider_email($email) {
    // Nettoyer l'email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // Valider l'email
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un mot de passe selon des critères de sécurité
 * 
 * @param string $password Mot de passe à valider
 * @return array Tableau contenant le résultat et un message d'erreur éventuel
 */
function valider_mot_de_passe($password) {
    $result = [
        'valide' => true,
        'message' => ''
    ];
    
    // Vérifier la longueur
    if (strlen($password) < 8) {
        $result['valide'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins 8 caractères.";
        return $result;
    }
    
    // Vérifier les majuscules
    if (!preg_match('/[A-Z]/', $password)) {
        $result['valide'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins une lettre majuscule.";
        return $result;
    }
    
    // Vérifier les minuscules
    if (!preg_match('/[a-z]/', $password)) {
        $result['valide'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins une lettre minuscule.";
        return $result;
    }
    
    // Vérifier les chiffres
    if (!preg_match('/[0-9]/', $password)) {
        $result['valide'] = false;
        $result['message'] = "Le mot de passe doit contenir au moins un chiffre.";
        return $result;
    }
    
    return $result;
}

/**
 * Vérifie les en-têtes pour détecter un potentiel XSS
 */
function verifier_en_tetes() {
    // Vérifier le contenu de l'en-tête Content-Type
    if (isset($_SERVER['HTTP_CONTENT_TYPE']) && stripos($_SERVER['HTTP_CONTENT_TYPE'], 'application/x-www-form-urlencoded') === false) {
        // Content-Type non conforme à ce qui est attendu pour les formulaires
        http_response_code(403);
        die("Requête non autorisée.");
    }
}

/**
 * Définit des en-têtes de sécurité pour renforcer la sécurité des pages
 */
function definir_en_tetes_securite() {
    // Protection contre le clickjacking
    header('X-Frame-Options: DENY');
    
    // Protection contre les attaques MIME-type
    header('X-Content-Type-Options: nosniff');
    
    // Protection contre les attaques XSS (pour les navigateurs qui le supportent)
    header('X-XSS-Protection: 1; mode=block');
    
    // Générer un nonce unique pour les scripts inline
    $nonce = base64_encode(random_bytes(16));
    
    // Stocker le nonce en session pour pouvoir l'utiliser dans les scripts
    $_SESSION['csp_nonce'] = $nonce;
    
    // Politique de sécurité du contenu (CSP) - mise à jour pour autoriser les API, CDN et scripts inline
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self'; connect-src 'self' https://v2.jokeapi.dev; font-src 'self'; frame-src 'self'");
}