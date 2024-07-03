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
	    $('.mepr-profile-wrapper__footer a:lt(2)').hide();
	    $('#mepr-account-nav .mepr-payments, #mepr-account-nav .mepr-subscriptions').hide();

        // Select the dt and dd containing the Business Address
        var $businessAddressDt = $('dt:contains("Business Address")');
        var $businessAddressDd = $businessAddressDt.next('dd');

        // Move dt and dd to the bottom of the dl
        var $dl = $businessAddressDt.parent();
        $businessAddressDt.appendTo($dl);
        $businessAddressDd.appendTo($dl);
    }
	  
    $(document).ready(function ($) {
        /*** EVENT LISTENERS **************************************************************/
        themeOnLoad();

    });
})(jQuery);