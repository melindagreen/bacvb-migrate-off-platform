<?php
/*
 Template Name: PartnerPortal Listings Page
*/
get_header();
global $post;
?>

<div class="main partner-portal-wrapper">
    <div class="filters">
        <p class="title">Search</p>
        <div class="dateAndKeyworkFilters">
            <div class="keywordFilter">
                <label for="keyword">Search By Name:</label>
                <input type="text" id="Keyword" name="keyword" value="" maxlength="50" class="keywords">
            </div>
        </div>
        <?php if ($categories): ?>
        <div class="categoryFilters">
            <div class="filterToggle">Categories:</div>
            <div class="categories">
                <?php foreach ($categories as $cat): ?>
                <div class="checkbox">
                    <input type="checkbox" name="category" value="<?php echo $cat->term_id; ?>">
                    <label for="category"><?php echo $cat->name; ?></label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <button class="filterSubmit">Filter</button>
    </div>
    <div id="partner-portal-listings">
    </div>
</div>
<?php get_footer(); ?>
