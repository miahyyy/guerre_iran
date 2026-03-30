<?php
// Fonctions utilitaires pour le SEO et le formatage - Front Office

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
 * Tronque un texte à une longueur donnée
 */
function truncate($text, $length = 150, $append = '...') {
    if(strlen($text) <= $length) return $text;
    $text = substr($text, 0, strpos($text, ' ', $length));
    return $text . $append;
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

/**
 * Redirige vers une URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}
?>
