<?php

namespace MaddenNino\Blocks\FloribbeanQuiz;
use MaddenNino\Library\Constants as Constants;

/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {
   ob_start();
 
  /**
   * ==========================
   * QUIZ START
   * ==========================
   */
    ?>
    <div class="quiz-container">
        <div class="quiz-header">
            <img src="images/floribbean-feast-logo.png" alt="Pick Your Floribbean Feast!">

        <div class="quiz-content">
            <div id="start-screen" class="quiz-page active">
                <div class="intro-visuals">
                    <img src="images/tacos.png" alt="Tacos" class="taco-img">
                    <img src="images/shrimp-salad.png" alt="Shrimp Salad" class="shrimp-salad-img">
                </div>
                <p>With so many amazing flavors to savor, we know that your Floribbean spread will be nothing short of delicious. If you're ready to dig in, let us help you find the perfect dish that will leave you wanting seconds (and thirds)!</p>
                <button id="start-quiz-btn" class="btn">Take the Quiz!</button>
            </div>

            <div id="question-pages-container">
                </div>

            <div id="results-screen" class="quiz-page">
                <h2 class="section-title">Your Floribbean Feast!</h2>
                <div class="result-dish">
                    <img id="result-image" src="" alt="Result Dish">
                    <h3 id="result-name"></h3>
                    <a id="result-recipe-link" href="#" target="_blank" class="btn recipe-btn">Here's the Recipe!</a>
                </div>
                <button id="explore-dishes-btn" class="btn">Explore Floribbean Dishes</button>
                <button id="retake-quiz-btn" class="btn">Retake Quiz</button>
            </div>
        </div>

        <div class="progress-dots">
                <span class="dot active" data-page="0"></span> <span class="dot" data-page="1"></span> <span class="dot" data-page="2"></span> <span class="dot" data-page="3"></span> <span class="dot" data-page="4"></span> <span class="dot" data-page="5"></span> <span class="dot" data-page="6"></span> </div>
        </div>
    </div>
    <?php
    
   /** ==========================
   * QUIZ END
   * ==========================
   */


   $output = ob_get_contents();
   ob_end_clean();
   return $output;
}