<?php

namespace MaddenNino\Blocks\FloribbeanQuiz;
use MaddenNino\Library\Constants as Constants;

$attrs = $attributes;
$image_base_url = '/wp-content/themes/mm-bradentongulfislands/assets/src/scripts/gutenberg/blocks/floribbean-quiz/assets/images/';

/**
 * ==========================
 * QUIZ START
 * ==========================
 */
?>
<div class="<?php echo Constants::BLOCK_CLASS; ?>-floribbean-quiz quiz-container">
    <div class="quiz-header quiz-header--start">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/pixel.png"
            data-load-type="img"
            data-load-offset="lg"
            data-load-lg="<?php echo esc_url($image_base_url . 'floribbean-feast-logo.png'); ?>" alt="Pick Your Floribbean Feast!">

    <div class="quiz-content">
        <div id="start-screen" class="quiz-page active">
            <div class="intro-visuals">
                <p>With so many amazing flavors to savor, we know that your Floribbean spread will be nothing short of delicious. If you're ready to dig in, let us help you find the perfect dish that will leave you wanting seconds (and thirds)!</p>
                <img src="<?php echo esc_url($image_base_url . 'dishes/fish_tacos.png'); ?>" alt="Fish Tacos" class="taco-img">
                <img src="<?php echo esc_url($image_base_url . 'dishes/fish_dip.png'); ?>" alt="Fish Dip" class="shrimp-salad-img">
            </div>
            <button id="start-quiz-btn" class="btn" aria-label="Take The Quiz!"><img src="<?php echo esc_url($image_base_url . 'take-quiz-btn.png'); ?>" alt="Take Quiz Button" class="take-quiz-btn-img"></button>
        </div>

        <div id="question-pages-container">
        </div>

        <div id="results-screen" class="quiz-page">
            <h2 id="result-name" class="section-title">Your Floribbean Feast!</h2>
            <div class="result-dish">
                <img id="result-image" class="inactive" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/pixel.png" alt="Result Dish">
                <a id="result-recipe-link" href="#" target="_blank" class="btn recipe-btn"><img src="<?php echo esc_url($image_base_url . 'recipe-btn.png'); ?>" alt="Here's the Recipe Button" class="recipe-btn-img"></a>
            </div>
            <button id="explore-dishes-btn" class="btn"><a href="/">Explore Floribbean Dishes</a></button>
            <button id="retake-quiz-btn" class="btn">Retake Quiz</button>
        </div>
    </div>

    <div class="progress-dots">
            <span class="dot active" data-page="0"></span> <span class="dot" data-page="1"></span> <span class="dot" data-page="2"></span> <span class="dot" data-page="3"></span> <span class="dot" data-page="4"></span> <span class="dot" data-page="5"></span> <span class="dot" data-page="6"></span> </div>
    </div>
</div>