(function($) {
    $(document).ready(function() {
        // Place Map
        $('#bradensotaMap').appendTo('.mapContainer');

        function populateContent(element) {
            var title = $(element).data('title');
            var location = $(element).data('location');
            var description = $(element).data('description');
        
            $('.bradensota-map-content__title').text(title);
            $('.bradensota-map-content__location').text(location);
            $('.bradensota-map-content__description').text(description);
        }

        // Populate content with the first element's data on page load
        var firstElement = $('.mapContainer #anna-maria-city-pier');
        populateContent(firstElement);

        // Update content on hover over any SVG element
        $('.mapContainer image').on('mouseenter', function() {
            populateContent(this);
        });

        const $svgImage = $("#bradensotaMap");
        const $svgContainer = $(".mapContainer");

        var viewBox = { x: 0, y: 0, w: 1600, h: 900 };
        const svgSize = { w: $svgImage[0].clientWidth, h: $svgImage[0].clientHeight };
        var zoomFactor = 3; 
        var canZoom = true;

        $svgImage.attr('viewBox', `${viewBox.x} ${viewBox.y} ${viewBox.w} ${viewBox.h}`);

        // Zoom into a specific element when clicked
        $(".mapContainer image").on("click", function() {
            const clickedElement = $(this);
            const transform = clickedElement.attr('transform');
            const dimensions = {
                width: parseFloat(clickedElement.attr('width')),
                height: parseFloat(clickedElement.attr('height'))
            };

            if (transform) {
                // Extract the translate(x, y) values
                const translateMatch = transform.match(/translate\(([^ ]+)\s+([^)]+)\)/);
                if (translateMatch) {
                    const elementX = parseFloat(translateMatch[1]);
                    const elementY = parseFloat(translateMatch[2]);
                    
                    // Calculate the center of the element
                    const centerX = (elementX + dimensions.width / 2) / 1.11;
                    const centerY = (elementY + dimensions.height / 2) / ($(this).attr('id') === 'cooltoday-park' ? 1 : 1.2);

                    // Adjust viewBox to center on the element
                    viewBox = {
                        x: centerX - (viewBox.w / (2 * zoomFactor)),
                        y: centerY - (viewBox.h / (2 * zoomFactor)),
                        w: viewBox.w / zoomFactor,
                        h: viewBox.h / zoomFactor
                    };

                    // Ensure viewBox values stay within bounds
                    viewBox.x = Math.max(0, viewBox.x);
                    viewBox.y = Math.max(0, viewBox.y);

                    $svgImage.attr('viewBox', `${viewBox.x} ${viewBox.y} ${viewBox.w} ${viewBox.h}`);
                    $("#resetZoom").show();
                    $(".zoomInfo").hide();
                    zoomFactor = 1;
                }
            }

            // Populate content for the clicked element
            populateContent(this);
        });

        // Optional: Reset zoom functionality
        $("#resetZoom").on("click", function() {
            viewBox = { x: 0, y: 0, w: 1600, h: 900 };
            $svgImage.attr('viewBox', `${viewBox.x} ${viewBox.y} ${viewBox.w} ${viewBox.h}`);
            zoomFactor = 3;
            $(this).hide();
        });
    });
})(jQuery);
