

// this is the front-end script for the block example-dynamic

(function($) {
  $(document).ready(function() {
      
    const heroShowcaseElements = $(".hero-showcase__background, .hero-showcase-amenities, .hero-showcase-body");

      $(heroShowcaseElements).each(function (index, element) {

          const observer = new IntersectionObserver(entries => {
              entries.forEach(entry => {
                  if (entry.isIntersecting) {
                      $(entry.target).css({
                          opacity: 1,
                          transform: "translateX(0px)"
                      });
                  }
              });
          }, { threshold: 0.1 });

          observer.observe(element);
      });
  });
})(jQuery);
