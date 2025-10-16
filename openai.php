<?php
require 'vendor/autoload.php';

use OpenAI\Client;

OPENAI_KEY = "YOUR_KEY_HERE"; // replace at runtime with environment variable

function generateRecipes($prompt, $diet, $skill) {
    global $client;

    $response = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant generating recipe names only.'],
            ['role' => 'user', 'content' => "Generate a list of $skill-level $diet recipes for: $prompt."]
        ],
        'max_tokens' => 100,
    ]);

    return array_filter(array_map('trim', explode("\n", $response['choices'][0]['message']['content'])));
}

function generateRecipeInstructions($recipe) {
    global $client;

    $response = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant providing detailed cooking instructions.'],
            ['role' => 'user', 'content' => "Provide detailed cooking instructions for the recipe: $recipe."]
        ],
        'max_tokens' => 300,
    ]);

    return trim($response['choices'][0]['message']['content']);
}

function generateRandomRecipe($cookTime, $mealType) {
    global $client;

    $response = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant providing complete recipe details.'],
            ['role' => 'user', 'content' => "Generate a $mealType recipe that can be cooked in $cookTime. Provide the recipe name, ingredients, and detailed instructions."]
        ],
        'max_tokens' => 300,
    ]);

    $content = $response['choices'][0]['message']['content'];

    preg_match('/Recipe Name:\s*(.+)\nIngredients:\s*(.+)\nInstructions:\s*(.+)/s', $content, $matches);

    return [
        'name' => $matches[1] ?? 'Unknown Recipe',
        'ingredients' => $matches[2] ?? 'No ingredients provided.',
        'instructions' => $matches[3] ?? 'No instructions provided.',
    ];
}
