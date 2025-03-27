// Lorsque le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", function() {
    // Afficher une alerte de bienvenue
    alert("Bienvenue sur ShopLuxe !");

    // Ajouter un effet d'animation au survol des produits
    const produits = document.querySelectorAll('li');
    
    produits.forEach(produit => {
        produit.addEventListener('mouseover', () => {
            produit.style.transform = "scale(1.05)";  // Agrandir légèrement l'élément
            produit.style.transition = "transform 0.3s ease-in-out";
        });

        produit.addEventListener('mouseout', () => {
            produit.style.transform = "scale(1)";  // Revenir à la taille d'origine
        });
    });

    // Ajouter un événement pour le bouton d'ajout au panier (exemple simple)
    const ajouterAuPanierButtons = document.querySelectorAll('.ajouter-panier');
    
    ajouterAuPanierButtons.forEach(button => {
        button.addEventListener('click', function() {
            alert("Produit ajouté au panier !");
        });
    });
});
