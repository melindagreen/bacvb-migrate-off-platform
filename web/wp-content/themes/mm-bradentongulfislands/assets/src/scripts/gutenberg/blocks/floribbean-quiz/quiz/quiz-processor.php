<?php
header('Content-Type: application/json');

$questions = [
    'q1' => [
        'A' => 'Seafood and fish',
        'B' => 'Veggies, please',
        'C' => 'No surf, just turf',
        'D' => 'All of the above sounds great'
    ],
    'q2' => [
        'A' => 'Citrus-y and Bright',
        'B' => 'Herbaceous and Zesty',
        'C' => 'Indulgent and Rich',
        'D' => 'Bold and Smoky'
    ],
    'q3' => [
        'A' => 'Fresh tropical fruits',
        'B' => 'Garlic rice & beans',
        'C' => 'A delicious dipping sauce',
        'D' => 'Something deep-fried'
    ],
    'q4' => [
        'A' => 'Toes in the sand',
        'B' => 'A casual gastro-pub',
        'C' => 'The fancier, the better',
        'D' => 'A beloved seafood shack'
    ],
    'q5' => [
        'A' => 'A fruity daiquiri or piña colada',
        'B' => 'A local craft beer',
        'C' => 'A key lime margarita',
        'D' => 'Iced tea or sparkling water'
    ]
];

$results_matrix = [
    // 1. Fish Tacos
    'fish_tacos' => [
        'name' => 'Fish Tacos',
        'image' => 'images/fish_tacos.png',
        'recipe_link' => 'https://example.com/fish-tacos-recipe', // Replace with actual link
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['A', 'B'],
            'q3' => ['B', 'C'],
            'q4' => ['A', 'B'],
            'q5' => ['A', 'B']
        ]
    ],
    // 2. Grouper Sandwich
    'grouper_sandwich' => [
        'name' => 'Grouper Sandwich',
        'image' => 'images/grouper_sandwich.png',
        'recipe_link' => 'https://example.com/grouper-sandwich-recipe',
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['A', 'C'],
            'q3' => ['D'],
            'q4' => ['A', 'B'],
            'q5' => ['B', 'D']
        ]
    ],
    // 3. Peel & Eat Shrimp
    'peel_eat_shrimp' => [
        'name' => 'Peel & Eat Shrimp',
        'image' => 'images/peel_eat_shrimp.png',
        'recipe_link' => 'https://example.com/peel-eat-shrimp-recipe',
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['B'],
            'q3' => ['B'],
            'q4' => ['A', 'D'],
            'q5' => ['B', 'C']
        ]
    ],
    // 4. Oysters on the half shell
    'oysters_half_shell' => [
        'name' => 'Oysters on the Half Shell',
        'image' => 'images/oysters_half_shell.png',
        'recipe_link' => 'https://example.com/oysters-half-shell-recipe',
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['A', 'B'],
            'q3' => ['A', 'C'],
            'q4' => ['A', 'D'],
            'q5' => ['B', 'D']
        ]
    ],
    // 5. Key Lime Pie
    'key_lime_pie' => [
        'name' => 'Key Lime Pie',
        'image' => 'images/key_lime_pie.png',
        'recipe_link' => 'https://example.com/key-lime-pie-recipe',
        'conditions' => [
            'q1' => ['B', 'D'],
            'q2' => ['A', 'B'],
            'q3' => ['C'],
            'q4' => ['B', 'C'],
            'q5' => ['A', 'D']
        ]
    ],
    // 6. Smoked Fish Dip
    'smoked_fish_dip' => [
        'name' => 'Smoked Fish Dip',
        'image' => 'images/smoked_fish_dip.png',
        'recipe_link' => 'https://example.com/smoked-fish-dip-recipe',
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['C', 'D'],
            'q3' => ['A', 'C'],
            'q4' => ['A', 'D'],
            'q5' => ['C', 'D']
        ]
    ],
    // 7. Stone Crab Claws
    'stone_crab_claws' => [
        'name' => 'Stone Crab Claws',
        'image' => 'images/stone_crab_claws.png',
        'recipe_link' => 'https://example.com/stone-crab-claws-recipe',
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['B', 'C'],
            'q3' => ['B', 'C'],
            'q4' => ['A', 'B', 'C'],
            'q5' => ['B', 'C']
        ]
    ],
    // 8. Conch Fritters
    'conch_fritters' => [
        'name' => 'Conch Fritters',
        'image' => 'images/conch_fritters.png',
        'recipe_link' => 'https://example.com/conch-fritters-recipe',
        'conditions' => [
            'q1' => ['A', 'D'], // Assuming "Protein" from your notes maps to "Mains" (q1)
            'q2' => ['C'],
            'q3' => ['C'], // Assuming "Sidekick" maps to "Sides" (q3)
            'q4' => ['B', 'C'],
            'q5' => ['B', 'C']
        ]
    ],
    // 9. Seafood Gumbo
    'seafood_gumbo' => [
        'name' => 'Seafood Gumbo',
        'image' => 'images/seafood_gumbo.png',
        'recipe_link' => 'https://example.com/seafood-gumbo-recipe',
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['C'],
            'q3' => ['B'],
            'q4' => ['A'],
            'q5' => ['B', 'D']
        ]
    ],
    // 10. Cuban Sandwich
    'cuban_sandwich' => [
        'name' => 'Cuban Sandwich',
        'image' => 'images/cuban_sandwich.png',
        'recipe_link' => 'https://example.com/cuban-sandwich-recipe',
        'conditions' => [
            'q1' => ['C', 'D'], // Assuming "Main" maps to "Mains" (q1)
            'q2' => ['D'],
            'q3' => ['B', 'D'],
            'q4' => ['A', 'B'],
            'q5' => ['B', 'C']
        ]
    ],
    // 11. Coconut Shrimp
    'coconut_shrimp' => [
        'name' => 'Coconut Shrimp',
        'image' => 'images/coconut_shrimp.png',
        'recipe_link' => 'https://example.com/coconut-shrimp-recipe',
        'conditions' => [
            'q1' => ['A'],
            'q2' => ['C'],
            'q3' => ['A', 'C'],
            'q4' => ['A', 'C'],
            'q5' => ['A', 'C']
        ]
    ]
];

$user_answers = $_POST['answers'] ?? []; // Get answers from POST request

$matched_dishes = [];

// Loop through each dish and check if user answers match the conditions
foreach ($results_matrix as $dish_key => $dish_info) {
    $matches = true;
    foreach ($dish_info['conditions'] as $question_key => $allowed_options) {
        // Extract the question number from the key (e.g., 'q1' -> '1')
        $q_num = substr($question_key, 1);
        if (!isset($user_answers[$q_num]) || !in_array($user_answers[$q_num], $allowed_options)) {
            $matches = false;
            break;
        }
    }
    if ($matches) {
        $matched_dishes[] = $dish_info;
    }
}

// Simple logic: if multiple dishes match, pick the first one.
// For a more sophisticated quiz, you might implement a scoring system.
if (!empty($matched_dishes)) {
    echo json_encode([
        'success' => true,
        'result' => $matched_dishes[0] // Return the first matched dish
    ]);
} else {
    // Fallback: If no perfect match, you might have a default or suggest a popular item
    echo json_encode([
        'success' => false,
        'message' => 'No perfect match found, but here\'s a popular Floribbean dish!',
        'result' => $results_matrix['fish_tacos'] // Example fallback
    ]);
}
?>