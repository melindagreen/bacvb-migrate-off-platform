wp.domReady(() => {
    function removeTaxonomyPanels() {
        const postType = wp.data.select('core/editor').getCurrentPostType();
        const taxonomies = wp.data.select('core').getTaxonomies();

        // Ensure both post type and taxonomies are available
        if (!postType || !taxonomies || taxonomies.length === 0) return;

        const restrictedPostTypes = krakenEvents.postTypes || []; 
        
        if (restrictedPostTypes.includes(postType)) {
            //console.log(`Hiding taxonomy panels for post type: ${postType}`);

            taxonomies.forEach((taxonomy) => {
                const panelId = `taxonomy-panel-${taxonomy.slug}`;
                wp.data.dispatch('core/edit-post').removeEditorPanel(panelId);
                //console.log(`Removed panel: ${panelId}`);
            });

            // Unsubscribe to avoid unnecessary executions
            unsubscribe();
        }
    }

    // Subscribe to changes in the editor state until post type and taxonomies are available
    const unsubscribe = wp.data.subscribe(() => {
        removeTaxonomyPanels();
    });
});
