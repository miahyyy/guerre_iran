<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/fonctions.php';

$db = new Database();
$articles = $db->getArticles(10);
$categories = $db->getCategories();

$meta_title = "Accueil - Actualités du conflit en Iran";
$meta_desc = "Suivez en temps réel l'actualité du conflit en Iran : analyses, témoignages, reportages et informations vérifiées.";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($meta_title, $meta_desc) ?>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>Guerre en Iran - Informations et analyses</h1>
        <nav>
            <ul>
                <li><a href="/">Accueil</a></li>
                <?php foreach($categories as $cat): ?>
                <li><a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>">
                    <?= htmlspecialchars($cat['nom']) ?>
                </a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="articles">
            <h2>Dernières actualités</h2>
            <?php foreach($articles as $article): ?>
            <article>
                <?php if($article['image']): ?>
                <img src="/images/<?= htmlspecialchars($article['image']) ?>" 
                     alt="<?= htmlspecialchars($article['image_alt'] ?: $article['titre']) ?>">
                <?php endif; ?>
                <h3><a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                    <?= htmlspecialchars($article['titre']) ?>
                </a></h3>
                <p class="meta">
                    Catégorie : <?= htmlspecialchars($article['categorie_nom']) ?> | 
                    Date : <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                </p>
                <p><?= truncate(strip_tags($article['contenu']), 200) ?></p>
                <a href="/article/<?= htmlspecialchars($article['slug']) ?>" class="read-more">Lire la suite</a>
            </article>
            <?php endforeach; ?>
        </section>
        
        <aside>
            <h3>Catégories</h3>
            <ul>
                <?php foreach($categories as $cat): ?>
                <li><a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>">
                    <?= htmlspecialchars($cat['nom']) ?>
                </a></li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </main>
    
    <footer>
        <p>&copy; 2026 - Informations sur la guerre en Iran</p>
    </footer>
    
    <script src="/js/main.js"></script>
</body>
</html>