# Cooking Chaos - Recipe Generator

## Project Overview
Cooking Chaos is a web-based Recipe Generator application that allows users to generate recipes based on the ingredients they have on hand. Users can log in, input available ingredients, select meal types, dietary preferences, and skill levels, and the app generates possible recipes using OpenAI's API. Users can also bookmark their favorite recipes for later. The application is built with PHP, uses MySQL for the database, and requires a web server environment (like MAMP) to run locally.

## Features
- User authentication: login and registration system with session management
- Generate recipes dynamically based on ingredients, meal type, diet, and skill level
- Bookmark recipes to save for later
- Admin or user management to view users (via database)
- Interactive web interface styled with CSS
- Database-driven storage of recipes, ingredients, and user bookmarks
- Integration with OpenAI API to dynamically generate recipes and instructions

## Folder Structure
CookingChaos/
- db.php               # Database connection and sanitization
- openai.php           # Functions to generate recipes using OpenAI API
- index.php            # Login page
- register.php         # User registration page
- home.php             # Main dashboard for logged-in users
- selectMeal.php       # Form to input ingredients and select meal/diet/skill
- randomRecipe.php     # Generate random recipes
- uploadRecipe.php     # Upload custom recipes (optional)
- bookmarks.php        # View bookmarked recipes
- styles.css           # CSS for styling the interface
- README.md            # Project documentation

## Requirements
- PHP 7+
- MySQL (via MAMP or local server)
- Web server environment (MAMP/XAMPP/etc.)
- OpenAI API key (for recipe generation)

## Setup and Installation
1. Clone the repository: git clone https://github.com/fadybuni/CookingChaos.git
2. Install and start MAMP (or another local web server) to run PHP and MySQL.
3. Create a database in MySQL and import the provided schema to set up app_users, bookmarks, and recipes tables.
4. Update db.php with your database credentials.
5. Update openai.php with your OpenAI API key.
6. Open the project in your local web browser via MAMP (e.g., http://localhost:8888/CookingChaos/index.php)
7. Register a new user or log in with existing credentials to start generating recipes.

## Author
Fady Buni
- Developed the database integration with MySQL
- Built the web interface with PHP, HTML, and CSS
- Implemented recipe generation and bookmarking functionality
- Managed session handling and user authentication

