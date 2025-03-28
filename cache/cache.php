<?php
class Cache {
    private $cacheDir;

    public function __construct($cacheDir = 'cache/') {
        $this->cacheDir = $cacheDir;
        // Créer le dossier de cache s'il n'existe pas
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    // Récupérer un produit depuis le cache
    public function getProduit($id) {
        $file = $this->cacheDir . 'produit_' . $id . '.cache';
        if (file_exists($file)) {
            $data = file_get_contents($file);
            $cacheData = unserialize($data);
            if (time() < $cacheData['expiration']) {
                echo "Produit $id récupéré depuis le cache.<br>";
                return $cacheData['data'];
            } else {
                // Cache expiré
                unlink($file);
                echo "Cache expiré pour produit $id.<br>";
            }
        }
        echo "Aucun cache trouvé pour produit $id.<br>";
        return false;
    }
    

    // Stocker un produit dans le cache
    public function setProduit($id, $produit, $ttl = 3600) {
        $file = $this->cacheDir . 'produit_' . $id . '.cache';
        $cacheData = [
            'expiration' => time() + $ttl,
            'data' => $produit,
        ];
        file_put_contents($file, serialize($cacheData));
    }
}
?>
