<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/fonctions.php';

$slug = isset($_GET['slug']) ? cleanInput($_GET['slug']) : null;

if(!$slug) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$db = new Database();
$article = $db->getArticleBySlug($slug);

if(!$article) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Données structurées JSON-LD pour le SEO
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article['titre'],
    'description' => $article['meta_description'] ?: truncate(strip_tags($article['contenu']), 160),
    'datePublished' => $article['created_at'],
    'dateModified' => $article['updated_at'],
    'author' => [
        '@type' => 'Organization',
        'name' => SITE_NAME
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => SITE_NAME
    ]
];

if($article['image']) {
    $schema['image'] = SITE_URL . '/images/' . $article['image'];
}

$meta_title = $article['meta_title'] ?: $article['titre'];
$meta_desc = $article['meta_description'] ?: truncate(strip_tags($article['contenu']), 160);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($meta_title, $meta_desc, $article['meta_keywords'], $article['image']) ?>
    <link rel="canonical" href="<?= SITE_URL ?>/article/<?= htmlspecialchars($article['slug']) ?>">
    <script type="application/ld+json">
    <?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($article['titre']) ?></h1>
        <nav>
            <a href="/">← Retour à l'accueil</a>
        </nav>
    </header>
    
    <main>
        <article>
            <div class="meta">
                <span>Catégorie : <?= htmlspecialchars($article['categorie_nom']) ?></span>
                <span>Publié le : <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?></span>
                <span>Vues : <?= $article['views'] ?></span>
            </div>
            
            <?php if($article['image']): ?>
            <figure>
                <img src="/images/<?= htmlspecialchars($article['image']) ?>" 
                     alt="<?= htmlspecialchars($article['image_alt'] ?: $article['titre']) ?>">
                <?php if($article['image_alt']): ?>
                <figcaption><?= htmlspecialchars($article['image_alt']) ?></figcaption>
                <?php endif; ?>
            </figure>
            <?php endif; ?>
            
            <div class="content">
                <?= $article['contenu'] ?>
            </div>
        </article>
    </main>
    
    <footer>
        <p>&copy; 2026 - Informations sur la guerre en Iran</p>
    </footer>
</body>
</html>