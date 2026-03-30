<?php
class Database {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // Méthodes CRUD pour les articles (BO - complet)
    public function getArticles($limit = null, $offset = 0, $status = null) {
        $sql = "SELECT a.*, c.nom as categorie_nom, c.slug as categorie_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.categorie_id = c.id";
        
        $conditions = [];
        if ($status !== null) {
            $conditions[] = "a.status = :status";
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        if($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($status !== null) {
            $stmt->bindValue(':status', $status);
        }
        
        if($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getArticleById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createArticle($data) {
        $stmt = $this->pdo->prepare("INSERT INTO articles (titre, slug, contenu, image, image_alt, categorie_id, meta_title, meta_description, meta_keywords, status) 
                                   VALUES (:titre, :slug, :contenu, :image, :image_alt, :categorie_id, :meta_title, :meta_description, :meta_keywords, :status)");
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':slug' => $data['slug'],
            ':contenu' => $data['contenu'],
            ':image' => $data['image'] ?? null,
            ':image_alt' => $data['image_alt'] ?? null,
            ':categorie_id' => $data['categorie_id'] ?? null,
            ':meta_title' => $data['meta_title'] ?? null,
            ':meta_description' => $data['meta_description'] ?? null,
            ':meta_keywords' => $data['meta_keywords'] ?? null,
            ':status' => $data['status'] ?? 'draft'
        ]);
    }
    
    public function updateArticle($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE articles SET titre = :titre, slug = :slug, contenu = :contenu, image = :image, image_alt = :image_alt, 
                                   categorie_id = :categorie_id, meta_title = :meta_title, meta_description = :meta_description, 
                                   meta_keywords = :meta_keywords, status = :status, updated_at = CURRENT_TIMESTAMP 
                                   WHERE id = :id");
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':slug' => $data['slug'],
            ':contenu' => $data['contenu'],
            ':image' => $data['image'] ?? null,
            ':image_alt' => $data['image_alt'] ?? null,
            ':categorie_id' => $data['categorie_id'] ?? null,
            ':meta_title' => $data['meta_title'] ?? null,
            ':meta_description' => $data['meta_description'] ?? null,
            ':meta_keywords' => $data['meta_keywords'] ?? null,
            ':status' => $data['status'] ?? 'draft',
            ':id' => $id
        ]);
    }
    
    public function deleteArticle($id) {
        $stmt = $this->pdo->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    public function getCategories() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Authentification
    public function authenticateUser($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin'");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
