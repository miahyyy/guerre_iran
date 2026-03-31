<?php
require_once 'includes/config.php';
require_once 'includes/fonctions.php';

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
redirect('/');
?>