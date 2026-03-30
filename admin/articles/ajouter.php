<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/fonctions.php';

if(!isLoggedIn()) {
    redirect('/admin/login');
}

$db = new Database();
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
    
    // Gestion de l'upload d'image
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if(in_array($extension, $allowed)) {
            $image = $slug . '_' . time() . '.' . $extension;
            $upload_path = '../../public/images/' . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
        } else {
            $error = 'Format d\'image non autorisé';
        }
    }
    
    if(!$error) {
        $stmt = $db->getConnection()->prepare("
            INSERT INTO articles (titre, slug, contenu, image, image_alt, categorie_id, 
                                 meta_title, meta_description, meta_keywords, status)
            VALUES (:titre, :slug, :contenu, :image, :image_alt, :categorie_id,
                    :meta_title, :meta_description, :meta_keywords, :status)
        ");
        
        $result = $stmt->execute([
            ':titre' => $titre,
            ':slug' => $slug,
            ':contenu' => $contenu,
            ':image' => $image,
            ':image_alt' => $image_alt,
            ':categorie_id' => $categorie_id ?: null,
            ':meta_title' => $meta_title,
            ':meta_description' => $meta_description,
            ':meta_keywords' => $meta_keywords,
            ':status' => $status
        ]);
        
        if($result) {
            $success = 'Article créé avec succès !';
        } else {
            $error = 'Erreur lors de la création de l\'article';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article - Administration</title>
    <link rel="stylesheet" href="/css/admin.css">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <h1>Ajouter un article</h1>
    
    <?php if($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Titre *</label>
            <input type="text" name="titre" required>
        </div>
        
        <div>
            <label>Catégorie</label>
            <select name="categorie_id">
                <option value="">Sans catégorie</option>
                <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
            <input type="text" name="image_alt" placeholder="Texte alternatif (SEO)">
        </div>
        
        <div>
            <label>Contenu *</label>
            <textarea name="contenu" id="contenu" rows="20" required></textarea>
        </div>
        
        <div>
            <label>SEO - Meta Title</label>
            <input type="text" name="meta_title" maxlength="60" placeholder="60 caractères max">
        </div>
        
        <div>
            <label>SEO - Meta Description</label>
            <textarea name="meta_description" rows="3" maxlength="160" placeholder="160 caractères max"></textarea>
        </div>
        
        <div>
            <label>SEO - Meta Keywords</label>
            <input type="text" name="meta_keywords" placeholder="Mots-clés séparés par des virgules">
        </div>
        
        <div>
            <label>Status</label>
            <select name="status">
                <option value="draft">Brouillon</option>
                <option value="published">Publié</option>
            </select>
        </div>
        
        <button type="submit">Publier</button>
        <a href="liste.php">Annuler</a>
    </form>
    
    <script>
        CKEDITOR.replace('contenu');
    </script>
</body>
</html>