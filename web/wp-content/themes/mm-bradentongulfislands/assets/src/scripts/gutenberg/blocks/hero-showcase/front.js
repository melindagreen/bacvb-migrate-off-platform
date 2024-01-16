

// this is the front-end script for the block example-dynamic

(function($) {
  $(document).ready(function() {
      
    const heroShowcaseElements = $(".hero-showcase");

      $(heroShowcaseElements).each(function (index, element) {
        console.log('Test');
 
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
