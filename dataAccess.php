<?php
class DataAccess {
    private $pdo;
    
    public function __construct() {
        // Connexion sécurisée via PDO pour la base ShopLuxe
        $dsn = 'mysql:host=localhost;dbname=ShopLuxe;charset=utf8';
        $username = 'root';
        $password = '';  // Pas de mot de passe en local
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base : " . $e->getMessage());
        }
    }
    
    // Permet d'accéder à l'objet PDO
    public function getPDO() {
        return $this->pdo;
    }
    
    // Récupérer tous les produits
    public function getAllProduits() {
        $stmt = $this->pdo->query("SELECT * FROM Produits");
        return $stmt->fetchAll();
    }
    
    // Récupérer un produit par son ID
    public function getProduitById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Produits WHERE id_produit = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    // Créer une commande avec transaction (exemple)
    public function createCommande($id_utilisateur, $total) {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO Commandes (id_utilisateur, date_commande, total) VALUES (:id_utilisateur, NOW(), :total)");
            $stmt->execute([
                'id_utilisateur' => $id_utilisateur,
                'total' => $total
            ]);
            $commande_id = $this->pdo->lastInsertId();
            $this->pdo->commit();
            return $commande_id;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur lors de la création de la commande : " . $e->getMessage());
            return false;
        }
    }
}
?>
