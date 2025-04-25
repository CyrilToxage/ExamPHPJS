<?php
// Fichier: config/gestionAuthentification.php
// Gestionnaire d'authentification

/**
 * Connecte un utilisateur en enregistrant son ID en session
 * 
 * @param int $utilisateurId L'identifiant de l'utilisateur
 * @param string $utilisateurPseudo Le pseudo de l'utilisateur
 * @return bool Retourne true si l'utilisateur a été connecté avec succès
 */
function connecter_utilisateur($utilisateurId, $utilisateurPseudo) {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Enregistrer l'ID et le pseudo de l'utilisateur en session
    $_SESSION['utilisateurId'] = $utilisateurId;
    $_SESSION['utilisateurPseudo'] = $utilisateurPseudo;
    
    // Définir également la date de connexion
    $_SESSION['dateConnexion'] = time();
    
    return true;
}

/**
 * Vérifie si un utilisateur est connecté
 * 
 * @return bool Retourne true si l'utilisateur est connecté
 */
function est_connecte() {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérifier si l'ID de l'utilisateur est présent en session
    return isset($_SESSION['utilisateurId']) && !empty($_SESSION['utilisateurId']);
}

/**
 * Déconnecte l'utilisateur en supprimant ses données de session
 * 
 * @return bool Retourne true si l'utilisateur a été déconnecté avec succès
 */
function deconnecter_utilisateur() {
    // Vérifier que la session est démarrée
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Supprimer les variables de session liées à l'utilisateur
    unset($_SESSION['utilisateurId']);
    unset($_SESSION['utilisateurPseudo']);
    unset($_SESSION['dateConnexion']);
    
    // Alternative: détruire complètement la session
    // session_destroy();
    
    return true;
}

/**
 * Récupère les informations de l'utilisateur connecté depuis la base de données
 * 
 * @return array|false Les informations de l'utilisateur ou false si non connecté
 */
function obtenir_utilisateur_connecte() {
    // Vérifier si l'utilisateur est connecté
    if (!est_connecte()) {
        return false;
    }
    
    // Inclure la connexion à la base de données
    require_once 'db.php';
    
    try {
        $pdo = getConnexion();
        
        // Préparer la requête pour récupérer les informations de l'utilisateur
        $stmt = $pdo->prepare("
            SELECT uti_id, uti_pseudo, uti_email 
            FROM t_utilisateur_uti 
            WHERE uti_id = ? AND uti_compte_active = 1
        ");
        
        // Exécuter la requête
        $stmt->execute([$_SESSION['utilisateurId']]);
        
        // Récupérer les données
        $utilisateur = $stmt->fetch();
        
        return $utilisateur ?: false;
    } catch (PDOException $e) {
        // En cas d'erreur, retourner false
        return false;
    }
}

/**
 * Vérifie si l'utilisateur a le droit d'accéder à une page protégée
 * Si l'utilisateur n'est pas connecté, le redirige vers la page de connexion
 */
function proteger_page() {
    if (!est_connecte()) {
        // Stocker l'URL actuelle pour y revenir après la connexion
        $_SESSION['url_retour'] = $_SERVER['REQUEST_URI'];
        
        // Rediriger vers la page de connexion
        header('Location: connexion.php');
        exit;
    }
}