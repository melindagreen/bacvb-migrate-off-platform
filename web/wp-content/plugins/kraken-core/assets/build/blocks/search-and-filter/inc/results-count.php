<div class="results-count">
    <?php 
    if ($totalResults > 0) {
        $resultsLow = ($paged - 1) * $attrs['perPage'] + 1;
        $resultsHigh = $resultsLow + $attrs['perPage'] - 1;
    
        if ($totalResults < $resultsLow) {
            $resultsLow = $totalResults;
        }
    
        if ($totalResults < $resultsHigh) {
            $resultsHigh = $totalResults;
        }
    
        echo $resultsLow.' - '.$resultsHigh;
        echo ' of '.$totalResults.' Results';
    }
    ?>
</div>