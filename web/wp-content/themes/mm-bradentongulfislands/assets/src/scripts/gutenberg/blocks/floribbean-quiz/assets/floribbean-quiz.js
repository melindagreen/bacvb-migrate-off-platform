import $ from "jquery"; // Keep this if you're using a module bundler like Webpack

const imageBaseUrl = '/wp-content/themes/mm-bradentongulfislands/assets/src/scripts/gutenberg/blocks/floribbean-quiz/assets/images/';

$(window).on("load", () => {
    initFloribbeanQuiz();
});

export const initFloribbeanQuiz = () => {

    let currentQuestion = 0; // 0 for start screen, 1-5 for questions, 6 for results
    let userAnswers = {}; // Store user's selected options: { '1': 'A', '2': 'B', ... }

    const questionsData = [
        { // Question 1
            question: "What are your mains?",
            image: `${imageBaseUrl}q1.png`, // Image for this question
            imageCaption: "Sausage, Seafood & Spuds", // Caption for the image
            options: {
                A: "Seafood and fish",
                B: "Veggies, please",
                C: "Surf & Turf",
                D: "All of the above sounds great"
            }
        },
        { // Question 2
            question: "Pick your spices",
            image: `${imageBaseUrl}q2.png`, // Image for this question",
            imageCaption: "A variety of spices",
            options: {
                A: "Citrus-y and Bright",
                B: "Herbaceous and Zesty",
                C: "Indulgent and Rich",
                D: "Bold and Smoky"
            }
        },
        { // Question 3
            question: "What are your sides?",
            image: `${imageBaseUrl}q3.png`,
            imageCaption: "Delicious side dishes",
            options: {
                A: "Fresh tropical fruits",
                B: "Garlic rice & beans",
                C: "A delicious dipping sauce",
                D: "Something deep-fried"
            }
        },
        { // Question 4
            question: "Find your digs",
            image: `${imageBaseUrl}q4.png`,
            imageCaption: "A beautiful setting",
            options: {
                A: "Toes in the sand",
                B: "A casual gastro-pub",
                C: "The fancier, the better",
                D: "A beloved seafood shack"
            }
        },
        { // Question 5
            question: "Drink of choice?",
            image: `${imageBaseUrl}q5.png`,
            imageCaption: "Refreshing drinks",
            options: {
                A: "A fruity daiquiri or piña colada",
                B: "A local craft beer",
                C: "A key lime margarita",
                D: "Iced tea or sparkling water"
            }
        }
    ];

    function showPage(pageNumber) {
        $('.quiz-page').removeClass('active');
        $('.dot').removeClass('active');

        if (pageNumber === 0) { // Start screen
            $('#start-screen').addClass('active');
            $('.quiz-header').addClass('quiz-header--start');
            $('.dot[data-page="0"]').addClass('active');
            currentQuestion = 0;
            userAnswers = {}; // Reset answers on retake
        } else if (pageNumber >= 1 && pageNumber <= 5) { // Questions
            currentQuestion = pageNumber;
            loadQuestion(currentQuestion);
            $('.dot[data-page="' + currentQuestion + '"]').addClass('active');
            $('.quiz-header').removeClass('quiz-header--start');
        } else if (pageNumber === 6) { // Results screen
            currentQuestion = 6;
            submitQuiz(); // Process answers and show results
            $('.dot[data-page="6"]').addClass('active');
            $('.quiz-header').removeClass('quiz-header--start');
        }

        // The updateNavigationButtons() call is less critical here as auto-advance is happening.
        // If you still have other navigation controls that need enabling/disabling, keep it.
        // For strictly auto-advance, you might remove it or simplify it.
        // updateNavigationButtons();
    }

    function loadQuestion(qNum) {
        const qData = questionsData[qNum - 1]; // qNum is 1-indexed, array is 0-indexed
        let questionHtml = `
            <div id="question-${qNum}" class="quiz-page question-page active">
                <div class="question-text">${qData.question}</div>
                <div class="question-row">
                    <figure class="question-image-container">
                        <img src="${qData.image}" alt="Question Image" class="question-image">
                        <figcaption class="image-caption">${qData.imageCaption}</figcaption>
                    </figure>
                    <ul class="option-list">`;

                    for (const [key, value] of Object.entries(qData.options)) {
                        const isSelected = userAnswers[qNum] === key ? 'selected' : '';
                        questionHtml += `
                        <li class="option-item ${isSelected}" data-question="${qNum}" data-option="${key}">
                            <span class="option-label ${isSelected}"><img src="${imageBaseUrl}${key.toLowerCase()}.svg" alt="Option ${key}" class="${key.toLowerCase()} letter" /></span>
                            <span>${value}</span>
                        </li>`;
                    }
                    questionHtml += `
                    </ul>
                </div>
            <div class="quiz-navigation">
            </div>
            </div>`;

        $('#question-pages-container').html(questionHtml);

        // Re-attach event listeners for options
        $('.option-item').off('click').on('click', function() {
            const questionId = $(this).data('question');
            const option = $(this).data('option');

            // Prevent re-clicking the same option multiple times from triggering advance
            if (userAnswers[questionId] === option) {
                return; // Option already selected, do nothing
            }

            // Remove selected from other options in the same question
            $(`.option-item[data-question="${questionId}"]`).removeClass('selected');
            $(`.option-item[data-question="${questionId}"] .option-label`).removeClass('selected');

            // Add selected to the clicked option
            $(this).addClass('selected');
            $(this).find('.option-label').addClass('selected');

            userAnswers[questionId] = option;

            // --- AUTO-ADVANCE LOGIC ---
            setTimeout(() => { // Add a slight delay for visual feedback before advancing
                if (currentQuestion < 5) {
                    showPage(currentQuestion + 1); // Go to the next question
                } else {
                    showPage(6); // Go to results if it's the last question
                }
            }, 300); // 300ms delay, adjust as needed for user experience
        });
    }

    // This function is less critical for auto-advance, but can be kept for other controls if needed.
    function updateNavigationButtons() {
        // If you had any other buttons or elements that depend on an answer being selected,
        // you would manage their state here. For purely auto-advance, this might be empty.
    }


    function submitQuiz() {
        $.ajax({
            url: '/wp-content/themes/mm-bradentongulfislands/assets/src/scripts/gutenberg/blocks/floribbean-quiz/quiz/quiz-processor.php', // Ensure this path is correct based on your setup
            type: 'POST',
            data: { answers: userAnswers },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.result.image && response.result.image !== '') {
                        $('#result-image').attr('src', response.result.image);
                        $('#result-image').removeClass('inactive'); 
                    } 
                    else {
                        $('#result-image').attr('src', `${imageBaseUrl}pixel.png`); // Fallback image
                        $('#result-image').addClass('inactive'); 
                    }

                    $('#result-name').text(`${response.result.name}!`);
                    $('#result-recipe-link').attr('href', response.result.recipe_link);
                } 
                $('#results-screen').addClass('active');
            },
            error: function(xhr, status, error) {
                console.error("Error submitting quiz:", status, error);
                // Display a generic error message or fallback result
                $('#result-name').text("Oops! Something went wrong. Please try again.");
                // Potentially hide recipe link or provide a generic one
            }
        });
    }

    // Event Listeners

    // Start Quiz Button
    $('#start-quiz-btn').on('click', function() {
        showPage(1); // Go to the first question
    });

    // Remove these listeners as "Next" and "Submit" buttons are removed from question pages
    // $(document).on('click', '.next-btn', function() { ... });
    // $(document).on('click', '#submit-quiz-btn', function() { ... });

    $(document).on('click', '.prev-btn', function() {
        if (currentQuestion > 1) { // Can't go back from start screen or first question
            showPage(currentQuestion - 1);
        } else if (currentQuestion === 1) { // Allow going back to start from Q1
            showPage(0);
        }
    });

    // Pagination dots
    $('.dot').on('click', function() {
        const targetPage = $(this).data('page');
        if (targetPage <= currentQuestion) { // Only allow going back or to current question
            showPage(targetPage);
        }
    });

    // Retake Quiz button
    $('#retake-quiz-btn').on('click', function() {
        showPage(0); // Go back to the start screen
    });

    // Initial load: show the start screen
    showPage(0);
};