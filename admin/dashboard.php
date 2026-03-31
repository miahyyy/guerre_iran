<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/fonctions.php';

if(!isLoggedIn()) {
    redirect('/');
}

$db = new Database();

// Statistiques pour le dashboard
$stats = [
    'total_articles' => $db->getConnection()->query("SELECT COUNT(*) as count FROM articles")->fetch()['count'],
    'published_articles' => $db->getConnection()->query("SELECT COUNT(*) as count FROM articles WHERE status = 'published'")->fetch()['count'],
    'draft_articles' => $db->getConnection()->query("SELECT COUNT(*) as count FROM articles WHERE status = 'draft'")->fetch()['count'],
    'total_views' => $db->getConnection()->query("SELECT SUM(views) as sum FROM articles")->fetch()['sum'] ?? 0
];

$recent_articles = $db->getArticles(5); // 5 articles récents

$meta_title = "Dashboard - Administration";
$meta_desc = "Tableau de bord d'administration du site Guerre en Iran";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($meta_title, $meta_desc) ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <title>Dashboard - Administration</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">Administration</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">← Retour au site</a>
                <a class="nav-link" href="/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Tableau de bord</h1>

                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Articles</h5>
                                <h2 class="card-text"><?= $stats['total_articles'] ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Articles Publiés</h5>
                                <h2 class="card-text"><?= $stats['published_articles'] ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Brouillons</h5>
                                <h2 class="card-text"><?= $stats['draft_articles'] ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Vues Totales</h5>
                                <h2 class="card-text"><?= number_format($stats['total_views']) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Actions Rapides</h5>
                    </div>
                    <div class="card-body">
                        <a href="/admin/articles/ajouter" class="btn btn-primary me-2">Ajouter un article</a>
                        <a href="/admin/articles" class="btn btn-secondary">Gérer les articles</a>
                    </div>
                </div>

                <!-- Articles récents -->
                <div class="card">
                    <div class="card-header">
                        <h5>Articles Récents</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Statut</th>
                                        <th>Vues</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_articles as $article): ?>
                                    <tr>
                                        <td>
                                            <a href="/article/<?= htmlspecialchars($article['slug']) ?>" target="_blank">
                                                <?= htmlspecialchars(substr($article['titre'], 0, 50)) ?>...
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $article['status'] == 'published' ? 'success' : 'warning' ?>">
                                                <?= $article['status'] == 'published' ? 'Publié' : 'Brouillon' ?>
                                            </span>
                                        </td>
                                        <td><?= $article['views'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($article['created_at'])) ?></td>
                                        <td>
                                            <a href="/admin/articles/modifier/<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>