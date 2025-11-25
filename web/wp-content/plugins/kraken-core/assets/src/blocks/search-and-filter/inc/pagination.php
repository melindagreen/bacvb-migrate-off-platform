<div class="pagination <?php echo $attrs['paginationStyle']; ?>">
    <?php
    if ($attrs['paginationStyle'] === 'load-more') {
        $nextPage = $paged < $totalPages ? $paged + 1 : $totalPages;
        if ($totalResults <= $attrs['perPage']) {
            $hideLoadMore = true;
        }

        echo do_blocks(
            '<!-- wp:buttons {"className":"center-on-mobile","layout":{"type":"flex","justifyContent":"center"}} -->'
            . '<div class="wp-block-buttons center-on-mobile">'
            . '<!-- wp:button -->'
            . '<div class="wp-block-button">'
            . '<a href="' . get_pagenum_link($nextPage) . '" class="wp-block-button__link wp-element-button go-to-page" style="' . ($hideLoadMore ? 'display: none;' : '') . '">Load More</a>'
            . '</div><!-- /wp:button -->'
            . '</div><!-- /wp:buttons -->'
        );
    } else if ($attrs['paginationStyle'] === 'page-numbers') {
        $paginationHtml = '';

        if ($totalPages > 1) {
            $range = array();

            if ($totalPages > 6) {
                // Determine if dots should appear after the first page
                $dots1 = ($paged - 3 > 1);

                // Build a range of page numbers
                for ($i = 2; $i < $totalPages; $i++) {
                    if (count($range) === 5) {
                        break;
                    }
                    if ($i > $paged - 3 && $i < $paged + 5 && $i < $totalPages) {
                        $range[] = $i;
                    }
                }
            } else {
                // If there aren't many pages, show all pages between first and last.
                for ($i = 2; $i < $totalPages; $i++) {
                    $range[] = $i;
                }
                $dots1 = false;
            }

            // Determine if dots should appear before the last page
            $dots2 = ($paged + 3 < $totalPages);

            // Previous page link
            $prevDisabled = ($paged === 1) ? 'disabled' : '';
            $prevPage = $paged - 1;
            $paginationHtml .= '<a href="'.get_pagenum_link($prevPage).'" aria-label="Go to previous page" class="go-to-page go-to-prev ' . $prevDisabled . '" data-page="' . $prevPage . '">';
            ob_start();

            echo apply_filters('kraken-core/search-and-filter/pagination_prev_icon', file_get_contents(__DIR__ . '/../icons/pagination-prev.php'));

            $paginationHtml .= ob_get_clean();
            $paginationHtml .= '</a>';

            // First page link
            $active = ($paged === 1) ? 'active' : '';
            $paginationHtml .= '<a href="' . get_pagenum_link(1) . '" aria-label="Go to page 1 of results" class="go-to-page ' . $active . '" data-page="1">1</a>';

            // Add dots after the first page if needed
            if ($dots1) {
                $paginationHtml .= '<div class="pagination-dots">…</div>';
            }

            // Links for the pages in the range
            foreach ($range as $number) {
                $active = ($paged === $number) ? 'active' : '';
                $paginationHtml .= '<a href="' . get_pagenum_link($number) . '" aria-label="Go to page ' . $number . ' of results" class="go-to-page ' . $active . '" data-page="' . $number . '">' . $number . '</a>';
            }

            // Add dots before the last page if needed
            if ($dots2) {
                $paginationHtml .= '<div class="pagination-dots">…</div>';
            }

            // Last page link
            $active = ($paged === $totalPages) ? 'active' : '';
            $paginationHtml .= '<a href="' . get_pagenum_link($totalPages) . '" aria-label="Go to page ' . $totalPages . ' of results" class="go-to-page ' . $active . '" data-page="' . $totalPages . '">' . $totalPages . '</a>';

            // Next page link
            $nextDisabled = ($paged === $totalPages) ? 'disabled' : '';
            $nextPage = $paged === $totalPages ? $totalPages : $paged + 1;

            $paginationHtml .= '<a href="' . get_pagenum_link($nextPage) . '" aria-label="Go to next page" class="go-to-page go-to-next ' . $nextDisabled . '" data-page="' . $nextPage . '">';
            ob_start();

            echo apply_filters('kraken-core/search-and-filter/pagination_next_icon', file_get_contents(__DIR__ . '/../icons/pagination-next.php'));

            $paginationHtml .= ob_get_clean();
            $paginationHtml .= '</a>';
        }

        echo $paginationHtml;
    }
    ?>
</div>
