<?php if ($attrs['enabledView'] === 'grid-map') { ?>
<div class="view-toggle-wrapper">      
    <button class="selected view-toggle" data-view="grid" type="button" aria-label="View results as grid">
    Grid
    <?php include __DIR__ . '/../icons/view-toggle-grid.php'; ?>
    </button>
    <button class="view-toggle" data-view="map" type="button" aria-label="View results on a map">
    Map
    <?php include __DIR__ . '/../icons/view-toggle-map.php'; ?>
    </button>
</div>
<?php } ?>