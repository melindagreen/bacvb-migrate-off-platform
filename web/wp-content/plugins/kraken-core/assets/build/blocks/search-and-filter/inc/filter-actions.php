<?php if ($attrs['enableActiveFilterDisplay'] || $attrs['enableClearAllButton']) { ?>
    <div class="filter-actions">
      <?php if ($attrs['enableActiveFilterDisplay']) { ?>
      <div class="active-filters"><?php 
        foreach($activeFilters as $filter) {
          echo '<button data-term_id="'.$filter['id'].'" data-type="'.$filter['type'].'">'.$filter['name'];
          include __DIR__ . '/../icons/close.php';
          echo '</button>';
        }
        ?></div>
      <?php } ?>

      <?php if ($attrs['enableClearAllButton']) { ?>
      <button class="filter-clear-all">
        Reset Filters
      </button>
      <?php } ?>
    </div>
<?php } ?>