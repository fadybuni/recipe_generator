-- Create `app_users` table
CREATE TABLE IF NOT EXISTS app_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Create `app_recipes` table
CREATE TABLE IF NOT EXISTS app_recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    diet VARCHAR(50) NOT NULL,
    skill_level VARCHAR(50) NOT NULL,
    ingredients TEXT NOT NULL
);

-- Insert sample data into `app_users`
INSERT INTO app_users (username, password)
VALUES ('test_user', MD5('password123')); -- Replace MD5 with secure hashing in production

-- Insert sample data into `app_recipes`
INSERT INTO app_recipes (recipe_name, description, diet, skill_level, ingredients)
VALUES 
('Spaghetti Bolognese', 'A classic Italian pasta dish.', 'non-veg', 'beginner', 'spaghetti, beef, tomato, onion, garlic'),
('Vegetable Stir Fry', 'Quick and healthy stir fry.', 'vegan', 'intermediate', 'broccoli, carrot, soy sauce, tofu'),
('Cheese Omelette', 'Simple and cheesy omelette.', 'vegetarian', 'beginner', 'egg, cheese, salt, pepper');
