<?php
// Configuration du site - Back Office
define('SITE_NAME', 'Guerre en Iran - Administration');
define('SITE_URL', 'http://localhost:8091');
define('ADMIN_URL', SITE_URL);

// Configuration base de données
define('DB_HOST', 'mysql');
define('DB_NAME', 'guerre_iran');
define('DB_USER', 'root');
define('DB_PASS', 'password');

// Configuration SEO
define('DEFAULT_META_TITLE', 'Administration - Guerre en Iran');
define('DEFAULT_META_DESC', 'Panneau d\'administration du site');

// Démarrage session
session_start();
?>
