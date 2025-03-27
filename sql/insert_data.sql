-- Insertion d'utilisateurs
INSERT INTO Utilisateurs (nom, email, mot_de_passe, adresse) VALUES
('Marie Martin', 'mariemartin@gmail.com', SHA2('password123', 256), '123 rue principale'),
('Jane Masse', 'janemasse@gmail.com', SHA2('mypassword', 256), '456 avenue des champs');

-- Insertion de produits
INSERT INTO Produits (nom, description, prix, stock) VALUES
('Echarpe Louis Vuitton', 'Echarpe LV', 299.99, 100),
('Gants Giorgio Armani', 'Gants noires GA', 499.99, 50);
