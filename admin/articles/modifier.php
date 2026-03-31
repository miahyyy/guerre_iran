<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/fonctions.php';

if(!isLoggedIn()) {
    redirect('/');
}

$db = new Database();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if(!$id) {
    header('HTTP/1.0 404 Not Found');
    exit('Article non trouvé');
}

// Récupération de l'article
$stmt = $db->getConnection()->prepare("SELECT * FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$article) {
    header('HTTP/1.0 404 Not Found');
    exit('Article non trouvé');
}

$categories = $db->getCategories();

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = cleanInput($_POST['titre']);
    $contenu = $_POST['contenu'];
    $categorie_id = intval($_POST['categorie_id'] ?? 0);
    $status = cleanInput($_POST['status'] ?? 'draft');
    $meta_title = cleanInput($_POST['meta_title']);
    $meta_description = cleanInput($_POST['meta_description']);
    $meta_keywords = cleanInput($_POST['meta_keywords']);
    $image_alt = cleanInput($_POST['image_alt']);

    $slug = slugify($titre);

    // Vérifier si le slug existe déjà pour un autre article
    $stmt = $db->getConnection()->prepare("SELECT id FROM articles WHERE slug = :slug AND id != :id");
    $stmt->execute([':slug' => $slug, ':id' => $id]);
    if($stmt->fetch()) {
        $slug = $slug . '-' . time();
    }

    // Gestion de l'upload d'image
    $image = $article['image']; // Garder l'ancienne image par défaut
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if(in_array($extension, $allowed)) {
            // Supprimer l'ancienne image si elle existe
            if($article['image'] && file_exists('../public/images/' . $article['image'])) {
                unlink('../public/images/' . $article['image']);
            }

            $image = $slug . '_' . time() . '.' . $extension;
            $upload_path = '../public/images/' . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
        } else {
            $error = 'Format d\'image non autorisé';
        }
    }

    if(!$error) {
        $stmt = $db->getConnection()->prepare("
            UPDATE articles SET
                titre = :titre,
                slug = :slug,
                contenu = :contenu,
                image = :image,
                image_alt = :image_alt,
                categorie_id = :categorie_id,
                meta_title = :meta_title,
                meta_description = :meta_description,
                meta_keywords = :meta_keywords,
                status = :status,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $result = $stmt->execute([
            ':titre' => $titre,
            ':slug' => $slug,
            ':contenu' => $contenu,
            ':image' => $image,
            ':image_alt' => $image_alt,
            ':categorie_id' => $categorie_id,
            ':meta_title' => $meta_title,
            ':meta_description' => $meta_description,
            ':meta_keywords' => $meta_keywords,
            ':status' => $status,
            ':id' => $id
        ]);

        if($result) {
            $success = 'Article modifié avec succès !';
            // Recharger les données de l'article
            $stmt = $db->getConnection()->prepare("SELECT * FROM articles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Erreur lors de la modification de l\'article';
        }
    }
}

$meta_title = "Modifier Article - Administration";
$meta_desc = "Modification de l'article: " . htmlspecialchars($article['titre']);
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
    <title>Modifier Article - Administration</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard">Administration</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                <a class="nav-link" href="/admin/articles">Articles</a>
                <a class="nav-link" href="/">← Retour au site</a>
                <a class="nav-link" href="/admin/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Modifier l'article</h1>
                    <a href="/article/<?= htmlspecialchars($article['slug']) ?>" target="_blank" class="btn btn-outline-primary">Voir l'article</a>
                </div>

                <?php if($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <?php if($success): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Contenu de l'article</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre *</label>
                                        <input type="text" class="form-control" id="titre" name="titre"
                                               value="<?= htmlspecialchars($article['titre']) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="contenu" class="form-label">Contenu *</label>
                                        <textarea class="form-control" id="contenu" name="contenu" rows="15" required><?= htmlspecialchars($article['contenu']) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="categorie_id" class="form-label">Catégorie</label>
                                        <select class="form-select" id="categorie_id" name="categorie_id">
                                            <option value="">Sans catégorie</option>
                                            <?php foreach($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $article['categorie_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['nom']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft" <?= $article['status'] == 'draft' ? 'selected' : '' ?>>Brouillon</option>
                                            <option value="published" <?= $article['status'] == 'published' ? 'selected' : '' ?>>Publié</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>Image</h5>
                                </div>
                                <div class="card-body">
                                    <?php if($article['image']): ?>
                                    <div class="mb-3">
                                        <img src="/images/<?= htmlspecialchars($article['image']) ?>" class="img-fluid mb-2" alt="Image actuelle">
                                        <small class="text-muted">Image actuelle</small>
                                    </div>
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Changer l'image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <small class="text-muted">Formats acceptés: JPG, PNG, WebP, SVG</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="image_alt" class="form-label">Texte alternatif</label>
                                        <input type="text" class="form-control" id="image_alt" name="image_alt"
                                               value="<?= htmlspecialchars($article['image_alt']) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>SEO</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                                               value="<?= htmlspecialchars($article['meta_title']) ?>">
                                        <small class="text-muted">60 caractères max recommandé</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                                  maxlength="160"><?= htmlspecialchars($article['meta_description']) ?></textarea>
                                        <small class="text-muted">160 caractères max</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                               value="<?= htmlspecialchars($article['meta_keywords']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/admin/articles" class="btn btn-secondary">Retour à la liste</a>
                        <button type="submit" class="btn btn-primary">Modifier l'article</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>