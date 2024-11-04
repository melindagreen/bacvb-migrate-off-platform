
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

      const $svgImage = $("#bradensotaMap");
      const $svgContainer = $(".mapContainer");
      
      var viewBox = {x: 0, y: 0, w: 1600, h: 900};
      $svgImage.attr('viewBox', `${viewBox.x} ${viewBox.y} ${viewBox.w} ${viewBox.h}`);
      const svgSize = {w: $svgImage[0].clientWidth, h: $svgImage[0].clientHeight};
      var isPanning = false;
      var startPoint = {x: 0, y: 0};
      var endPoint = {x: 0, y: 0};
      var scale = 1;

      $svgContainer.on("wheel", function(e) {
          e.preventDefault();
      
          // Determine zoom direction and scale factor
          var zoomIntensity = 0.05;
          var w = viewBox.w;
          var h = viewBox.h;
          var mx = e.offsetX;  // mouse x
          var my = e.offsetY;  // mouse y
          var dw = w * (e.originalEvent.deltaY > 0 ? zoomIntensity : -zoomIntensity);
          var dh = h * (e.originalEvent.deltaY > 0 ? zoomIntensity : -zoomIntensity);
          var dx = dw * mx / svgSize.w;
          var dy = dh * my / svgSize.h;
      
          viewBox = {x: viewBox.x + dx, y: viewBox.y + dy, w: viewBox.w - dw, h: viewBox.h - dh};
          scale = svgSize.w / viewBox.w;
      
          $("#zoomValue").text(`${Math.round(scale * 100) / 100}`);
          $svgImage.attr('viewBox', `${viewBox.x} ${viewBox.y} ${viewBox.w} ${viewBox.h}`);
      });
      
      $svgContainer.on("mousedown", function(e) {

          isPanning = true;
          startPoint = {x: e.clientX, y: e.clientY};   
      });
      
      $svgContainer.on("mousemove", function(e) {
          if (isPanning) {
              endPoint = {x: e.clientX, y: e.clientY};
              var dx = (startPoint.x - endPoint.x) / scale;
              var dy = (startPoint.y - endPoint.y) / scale;
              var movedViewBox = {x: viewBox.x + dx, y: viewBox.y + dy, w: viewBox.w, h: viewBox.h};
              $svgImage.attr('viewBox', `${movedViewBox.x} ${movedViewBox.y} ${movedViewBox.w} ${movedViewBox.h}`);
          }
      });
      
      $svgContainer.on("mouseup", function(e) {
          if (isPanning) { 
              endPoint = {x: e.clientX, y: e.clientY};
              var dx = (startPoint.x - endPoint.x) / scale;
              var dy = (startPoint.y - endPoint.y) / scale;
              viewBox = {x: viewBox.x + dx, y: viewBox.y + dy, w: viewBox.w, h: viewBox.h};
              $svgImage.attr('viewBox', `${viewBox.x} ${viewBox.y} ${viewBox.w} ${viewBox.h}`);
              isPanning = false;
          }
      });
      
      $svgContainer.on("mouseleave", function() {
          isPanning = false;
      });
      
    })
})(jQuery);