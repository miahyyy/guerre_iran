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
    
    // Méthodes CRUD pour les articles
    public function getArticles($limit = null, $offset = 0, $status = 'published') {
        $sql = "SELECT a.*, c.nom as categorie_nom, c.slug as categorie_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.categorie_id = c.id 
                WHERE a.status = :status 
                ORDER BY a.created_at DESC";
        
        if($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        
        if($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getArticleBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT a.*, c.nom as categorie_nom 
                                     FROM articles a 
                                     LEFT JOIN categories c ON a.categorie_id = c.id 
                                     WHERE a.slug = :slug AND a.status = 'published'");
        $stmt->execute([':slug' => $slug]);
        
        // Incrémenter les vues
        if($article = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->incrementViews($article['id']);
        }
        
        return $article;
    }
    
    public function incrementViews($id) {
        $stmt = $this->pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
    
    public function getArticlesByCategorie($categorie_slug, $limit = null) {
        $sql = "SELECT a.*, c.nom as categorie_nom 
                FROM articles a 
                JOIN categories c ON a.categorie_id = c.id 
                WHERE c.slug = :slug AND a.status = 'published' 
                ORDER BY a.created_at DESC";
        
        if($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':slug', $categorie_slug);
        
        if($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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