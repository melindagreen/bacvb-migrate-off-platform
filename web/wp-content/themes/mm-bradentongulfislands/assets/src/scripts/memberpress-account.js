import '../styles/memberpress-account.scss';

//
// Memberpress Account javascript
//

(function ($) {
  
    /*** THEME FRAMEWORK FUNCTIONS *************************************************/

    /**
	 * Fires on initial document load
	 */
	function themeOnLoad() {
		
        // Modifications to the Partner Portal -- this is short term to save time
	    //$('.mepr-profile-wrapper__footer a:lt(2)').hide();
	    $('#mepr-account-nav .mepr-payments, #mepr-account-nav .mepr-subscriptions').hide();

        // Select the dt and dd containing the Business Address
        var $businessAddressDt = $('dt:contains("Business Address")');
        var $businessAddressDd = $businessAddressDt.next('dd');

        // Move dt and dd to the bottom of the dl
        var $dl = $businessAddressDt.parent();
        $businessAddressDt.appendTo($dl);
        $businessAddressDd.appendTo($dl);

        // Change Profile Header
        $('.mepr_page_header').text('Your Business Profile');

        //Tab Info
         // Show the first tab by default
            $('.tab-content').hide();
            $('.tab-content').first().show();
            $('.tab-button').first().addClass('active');

            // Handle tab click
            $('.tab-button').click(function(e) {
                e.preventDefault();
                // Remove 'active' class from all tab buttons
                $('.tab-button').removeClass('active');
                
                // Hide all tab content
                $('.tab-content').hide();
                
                // Show the clicked tab content
                var target = $(this).attr('onclick').split("'")[1]; // Extract tab ID from onclick attribute
                $('#' + target).show();
                
                // Add 'active' class to the clicked button
                $(this).addClass('active');
            });
    }
	  
    $(document).ready(function ($) {
        /*** EVENT LISTENERS **************************************************************/
        themeOnLoad();

        // Event All Day is Checked Hide Start & End Time Fields
        $('#eventastic_event_all_day').change(function() {

            if ($(this).prop('checked')) {
                // If checked, hide the start and end time fields
                $('#eventastic_start_col, #eventastic_end_col').hide();
            } else {
                // If not checked, show the start and end time fields
                $('#eventastic_start_col, #eventastic_end_col').show();
            }
        });
    });
})(jQuery);