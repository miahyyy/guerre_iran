<?php
// Fonctions utilitaires pour le Back Office

/**
 * Génère un slug SEO-friendly à partir d'une chaîne
 */
function slugify($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Nettoie et sécurise les entrées utilisateur
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Redirige vers une URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Vérifie les permissions d'administrateur
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

/**
 * Génère un token CSRF
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie un token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}


/**
 * Génère les balises meta pour une page
 */
function generateMetaTags($title, $description, $keywords = '', $image = '') {
    $html = '<title>' . htmlspecialchars($title) . ' - ' . SITE_NAME . '</title>' . "\n";
    $html .= '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
    
    if($keywords) {
        $html .= '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">' . "\n";
    }
    
    $html .= '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
    $html .= '<meta property="og:type" content="article">' . "\n";
    
    if($image) {
        $html .= '<meta property="og:image" content="' . SITE_URL . '/images/' . htmlspecialchars($image) . '">' . "\n";
    }
    
    return $html;
}

?>
