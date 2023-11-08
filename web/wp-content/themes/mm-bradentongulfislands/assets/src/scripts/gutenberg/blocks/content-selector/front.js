
jQuery(document).ready(function ($) {

       // Find the container and extract data-title values
    var container = $('.wp-block-mm-bradentongulfislands-content-selector');
    var dropdown = $('#content-dropdown');
    var sections = container.find('.wp-block-mm-bradentongulfislands-content-section');

    sections.slice(1).hide();

    sections.each(function() {
      var dataTitle = $(this).data('title');
      dropdown.append('<option value="' + dataTitle + '">' + dataTitle + '</option>');
    });


    dropdown.on('change', function() {
      var selectedTitle = $(this).val();
      container.find('.wp-block-mm-bradentongulfislands-content-section').hide();
      container.find('[data-title="' + selectedTitle + '"]').show();
    });
});