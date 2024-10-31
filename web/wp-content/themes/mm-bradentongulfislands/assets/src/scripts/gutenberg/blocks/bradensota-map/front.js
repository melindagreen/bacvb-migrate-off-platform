
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
      var firstElement = $('.mapContainer #anna-maria-city-pier')
      populateContent(firstElement);
    
      // Update content on hover over any SVG element
      $('.mapContainer image').on('mouseenter', function() {
        populateContent(this);
      });
    })
})(jQuery);