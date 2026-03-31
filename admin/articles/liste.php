<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/fonctions.php';

if(!isLoggedIn()) {
    redirect('/');
}

$db = new Database();

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filtres
$status_filter = isset($_GET['status']) ? cleanInput($_GET['status']) : '';
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';

// Construction de la requête
$query = "SELECT a.*, c.nom as categorie_nom FROM articles a LEFT JOIN categories c ON a.categorie_id = c.id";
$where = [];
$params = [];

if($status_filter) {
    $where[] = "a.status = :status";
    $params[':status'] = $status_filter;
}

if($search) {
    $where[] = "(a.titre LIKE :search OR a.contenu LIKE :search)";
    $params[':search'] = "%$search%";
}

if($where) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";

// Comptage total
$count_query = str_replace("SELECT a.*, c.nom as categorie_nom FROM articles a LEFT JOIN categories c ON a.categorie_id = c.id", "SELECT COUNT(*) as total FROM articles a", $query);
$count_query = preg_replace('/ORDER BY.*$/', '', $count_query);
$count_query = preg_replace('/LIMIT.*$/', '', $count_query);

$stmt = $db->getConnection()->prepare($count_query);
foreach($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_articles = $stmt->fetch()['total'];
$total_pages = ceil($total_articles / $per_page);

// Récupération des articles
$stmt = $db->getConnection()->prepare($query);
foreach($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meta_title = "Gestion des Articles - Administration";
$meta_desc = "Interface de gestion des articles du site Guerre en Iran";
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
    <title>Gestion des Articles - Administration</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard">Administration</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                <a class="nav-link" href="/">← Retour au site</a>
                <a class="nav-link" href="/admin/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Gestion des Articles</h1>
                    <a href="/admin/articles/ajouter" class="btn btn-primary">Ajouter un article</a>
                </div>

                <!-- Filtres et recherche -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Rechercher</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="<?= htmlspecialchars($search) ?>" placeholder="Titre ou contenu...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tous</option>
                                    <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>>Publié</option>
                                    <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>>Brouillon</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-secondary w-100">Filtrer</button>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <a href="/admin/articles" class="btn btn-outline-secondary w-100">Réinitialiser</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Liste des articles -->
                <div class="card">
                    <div class="card-header">
                        <h5>Articles (<?= $total_articles ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Statut</th>
                                        <th>Vues</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($articles as $article): ?>
                                    <tr>
                                        <td><?= $article['id'] ?></td>
                                        <td>
                                            <a href="/article/<?= htmlspecialchars($article['slug']) ?>" target="_blank">
                                                <?= htmlspecialchars(substr($article['titre'], 0, 50)) ?>...
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($article['categorie_nom'] ?: 'Sans catégorie') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $article['status'] == 'published' ? 'success' : 'warning' ?>">
                                                <?= $article['status'] == 'published' ? 'Publié' : 'Brouillon' ?>
                                            </span>
                                        </td>
                                        <td><?= $article['views'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($article['created_at'])) ?></td>
                                        <td>
                                            <a href="/admin/articles/modifier/<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Modifier</a>
                                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $article['id'] ?>, '<?= htmlspecialchars(addslashes($article['titre'])) ?>')">Supprimer</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($total_pages > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-center">
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer l'article "<span id="articleTitle"></span>" ?
                    Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a id="deleteLink" href="#" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, title) {
            document.getElementById('articleTitle').textContent = title;
            document.getElementById('deleteLink').href = '/admin/articles/supprimer/' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>