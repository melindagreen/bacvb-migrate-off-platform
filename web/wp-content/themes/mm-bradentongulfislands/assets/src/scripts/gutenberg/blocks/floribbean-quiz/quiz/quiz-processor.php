<?php
header('Content-Type: application/json');

// Define the base URL for images
$image_base_url = '/wp-content/themes/mm-bradentongulfislands/assets/src/scripts/gutenberg/blocks/floribbean-quiz/assets/images/dishes/';

// Define the questions and their options (no change needed here)
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

// Define the results matrix
// The image paths here are still relative to the base, e.g., 'fish_tacos.png'
$results_matrix = [
    // 1. Fish Tacos
    'fish_tacos' => [
        'name' => 'Fish Tacos',
        'image' => 'fish_tacos.png', // Just the filename
        'recipe_link' => '', // Replace with actual link
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
        'image' => '', // Just the filename
        'recipe_link' => '',
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
        'image' => '', // Just the filename
        'recipe_link' => '',
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
        'image' => 'oysters.png', // Just the filename
        'recipe_link' => '',
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
        'image' => 'key_lime_pie.png', // Just the filename
        'recipe_link' => '',
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
        'image' => 'fish_dip.png', // Just the filename
        'recipe_link' => '',
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
        'image' => '', // Just the filename
        'recipe_link' => '',
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
        'image' => '', // Just the filename
        'recipe_link' => '',
        'conditions' => [
            'q1' => ['A', 'D'],
            'q2' => ['C'],
            'q3' => ['C'],
            'q4' => ['B', 'C'],
            'q5' => ['B', 'C']
        ]
    ],
    // 9. Seafood Gumbo
    'seafood_gumbo' => [
        'name' => 'Seafood Gumbo',
        'image' => '', // Just the filename
        'recipe_link' => '',
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
        'image' => '', // Just the filename
        'recipe_link' => '',
        'conditions' => [
            'q1' => ['C', 'D'],
            'q2' => ['D'],
            'q3' => ['B', 'D'],
            'q4' => ['A', 'B'],
            'q5' => ['B', 'C']
        ]
    ],
    // 11. Coconut Shrimp
    'coconut_shrimp' => [
        'name' => 'Coconut Shrimp',
        'image' => 'coconut_shrimp.png', // Just the filename
        'recipe_link' => '/',
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

$dish_scores = [];

// Calculate scores for each dish
foreach ($results_matrix as $dish_key => $dish_info) {
    $score = 0;
    foreach ($dish_info['conditions'] as $question_key => $allowed_options) {
        // Extract the question number from the key (e.g., 'q1' -> '1')
        $q_num = substr($question_key, 1);
        if (isset($user_answers[$q_num]) && in_array($user_answers[$q_num], $allowed_options)) {
            $score++; // Increment score if user's answer matches an allowed option
        }
    }
    $dish_scores[$dish_key] = $score;
}

// Find the dish(es) with the maximum score
$max_score = 0;
if (!empty($dish_scores)) {
    $max_score = max($dish_scores);
}

$best_match_dishes = [];
foreach ($dish_scores as $dish_key => $score) {
    if ($score === $max_score) {
        // Prepend the base URL to the image path right here
        $temp_dish_info = $results_matrix[$dish_key];
        $temp_dish_info['image'] = !empty($temp_dish_info['image']) ? $image_base_url . $temp_dish_info['image'] : '';
        $best_match_dishes[] = $temp_dish_info;
    }
}

// If multiple dishes have the same highest score, pick one (e.g., the first one found)
$final_result = null;
if (!empty($best_match_dishes)) {
    // Optionally, you could add more logic here to pick a "tie-breaker" if needed
    // For now, we just take the first one that scored highest.
    $final_result = $best_match_dishes[0];
}

if ($final_result) {
    echo json_encode([
        'success' => true,
        'message' => ($max_score === count($user_answers)) ? 'Perfect match found!' : 'Closest match found!',
        'result' => $final_result
    ]);
} else {
    // This case should ideally not be reached if there's at least one dish in the matrix,
    // as max_score would be at least 0. But as a safeguard:
    // Ensure the fallback also uses the image_base_url
    $fallback_dish = $results_matrix['fish_tacos'];
    $fallback_dish['image'] = !empty($fallback_dish['image']) ? $image_base_url . $fallback_dish['image'] : '';

    echo json_encode([
        'success' => false,
        'message' => 'Could not determine a suitable dish.',
        'result' => $fallback_dish
    ]);
}
?>