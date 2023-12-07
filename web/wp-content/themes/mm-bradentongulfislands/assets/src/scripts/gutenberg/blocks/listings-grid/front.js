(function ($) {
	/** FUNCTIONS *********************************************************************/


    function truncateText(description, maxLength) {
		if (description.length > maxLength) {
			return description.substring(0, maxLength) + "...read more.";
		} else {
			return description;
		}
    }

	/**
	 * Generate an HTML string for the given listing object
	 * @param {Object} listing          The listing data
	 * @param {String} postType         The listing post type
	 * @returns {String}                The HTML string representing the listing
	 */
	function templateListing(listing, postType) {
		let description = '';
		let date = '';
		let month = '';
		let placeHolder = $(".wp-block-mm-bradentongulfislands-listings-grid").attr("data-default-thumb");
		let accommodations = '';
		let petfriendly = '';

		let testing = '';

		let newThumb = '';
		if(listing?.thumb_url) {
			newThumb = listing?.thumb_url;
			// newThumb = newThumb.replace(/-\d+x\d+(\.\w+)$/, '$1');
		}

		let thumbUrl = newThumb || listing?.yoast_head_json?.og_image?.[0]?.url || placeHolder;

		// console.log(listing);

		switch (postType) {
			case 'event':
				description = listing?.excerpt.rendered || '';
				description = truncateText(description, 50);
				if (listing?.meta_fields?.eventastic_start_date) {

					let startDate = listing?.meta_fields?.eventastic_start_date;
					let endDate = listing?.meta_fields?.eventastic_end_date;

					// Start day Format
					const startdateObject = new Date(startDate);
					let startDay = startdateObject.getDate() + 1;
					// if date is the first day of the month
					if (startDay > new Date(startdateObject.getFullYear(), startdateObject.getMonth() + 1, 0).getDate()) {
					    startDay = 1;
					    startdateObject.setMonth(startdateObject.getMonth() + 1);
					}
					const startMonth = new Intl.DateTimeFormat('en-US', { month: 'short' }).format(startdateObject);

					startDate = `${startMonth} ${startDay}`;

					// End day Format
					const enddateObject = new Date(endDate);
					let endDay = enddateObject.getDate() + 1;
					// if date is the first day of the month
					if (endDay > new Date(enddateObject.getFullYear(), enddateObject.getMonth() + 1, 0).getDate()) {
					    endDay = 1;
					    enddateObject.setMonth(enddateObject.getMonth() + 1);
					}
					const endMonth = new Intl.DateTimeFormat('en-US', { month: 'short' }).format(enddateObject);

					endDate = `${endMonth} ${endDay}`;

					date = `<div class='date'>
						<span class='date__start'>${startDate}</span>`;

					if(startDate != endDate) {
						date += ` - <span class='date__end'>${endDate}</span>`;
					}
					
					date += `</div>`;
				}
				break;

			case 'listing':
				description = listing?.excerpt.rendered || '';
				description = truncateText(description, 50);
				date = '';

				accommodations = listing?.meta_fields?.['partnerportal_accomodations-location']?.[0];

				let amenities = listing?.meta_fields?.['partnerportal_accomodations-facility-amenities']?.[0];
				
				if(amenities == 'pet-friendly') {
					petfriendly = `<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/pet-friendly.png" alt="pet friendly icon" title="Pet Friendly" class="petfriendly">`
				}
				if(accommodations == 'beachfront') {
					testing += `<span>beachfront</span>`;
				}

			break;
		}

		let html = `${month}
			<article class='listing listing--${postType}'>
			<a aria-label='${listing.title.rendered}' href='${listing.link}'>
				${date}
				<div class='listing__thumb'>`;
					
					// Add an `onerror` event handler to the `img` tag
					html += `<img src='${thumbUrl}' alt='${listing.title.rendered}' onerror="this.onerror=null; this.src='${placeHolder}'" />`;

				html += `</div>
				<div class="listing__info">
					${petfriendly}
					<h3 class='listing__title'>${listing.title.rendered}</h3>`;
				if(description.length != 0){
					html += `<div class='listing__description'>${description}</div>`;
				}
				html += `${testing}`;
				html += `<div class="details_btn">Details</div>
				</div>`
		
		html += `</a>
		</article>`;

		return html;

	}

	/**
	 * Update pagination numbers to reflect current state
	 * @param {Number} page         The current page
	 */
	function updatePagination(page) {
		// update buttons
		var prev = page > 1 ? page - 1 : 1;
		var lastPage = parseInt($(".pagination__button--last").attr("data-page"));
		var next = page < lastPage ? page + 1 : lastPage;
		$(".pagination__button--prev").attr("data-page", prev);
		$(".pagination__button--next").attr("data-page", next);

		// update counts
		var total = parseInt($(".count__page-total:first").text());
		var countStart = (page - 1) * perPage + 1;
		var countEnd = page * perPage > total ? total : page * perPage;
		$(".count__page-start").text(countStart);
		$(".count__page-end").text(countEnd);

		// enable appropraite buttons
		if (page > 1) {
			$(".pagination__button--first, .pagination__button--prev").prop(
				"disabled",
				false
			);
		}
		else {
			$(".pagination__button--first, .pagination__button--prev").prop(
				"disabled",
				true
			);
		}
		if (!((perPage*lastPage)-countEnd)) {
			$(".pagination__button--next, .pagination__button--last").prop(
				"disabled",
				true
			);
		}
		else {
			$(".pagination__button--next, .pagination__button--last").prop(
				"disabled",
				false
			);
		}
	}

	/**
	 * Jump to the requested page when pagination buttons are clicked
	 * @param {Number} page              The page to load
	 * @param {Boolean} adjustScroll     Scroll the page to the top of the grid?
	 */
	function loadPage(page = 1, adjustScroll = false) {
		// clear existing listings and show loader
		$('.listing').remove();
		$(".loading, .pagination__loading").addClass("show");

		// create wp-json URL for query
		var postType = $(".wp-block-mm-bradentongulfislands-listings-grid").attr("data-postType");
		var order = ['posts'].includes(postType) ? 'desc' : 'asc';
		var orderBy = ['listing',].includes(postType) ? 'title' : 'date';
		var url = `/wp-json/wp/v2/${postType}?order=${order}&orderby=${orderBy}&page=${page}&per_page=${perPage}&include_child_terms=true&`;

		// add filters
		var filters = $(".filters")
			.serializeArray()
			.reduce(function (prev, current) {
				if (!!current.value) {
					if (prev[current.name]) {
						prev[current.name].push(encodeURIComponent(current.value));
					} else prev[current.name] = [current.value];
				}
				return prev;
			}, {});


		url += Object.keys(filters)
			.map(function (key) {
				return `${key}=${filters[key].join(',')}`
			})
			.join('&');

		console.log(url);

		// get the page back up where it needs to be for viewing (it's slightly less jarring to do this pre-ajax call)
		if (adjustScroll) {
			$("html, body").animate({
				scrollTop: ($('.grid-body').offset().top - $(".header").height() - $(".listings-grid__header").height())
			}, "100");
		}

		$.get(url)
			.done(function (listings, status, xhr) {

				// update totals
				var total = parseInt(xhr.getResponseHeader("X-WP-Total"));
				var totalPages = parseInt(xhr.getResponseHeader("X-WP-TotalPages"));
				$(".count__page-total").text(total);
				$(".pagination__button--last").attr("data-page", totalPages);

				// update pagination
				updatePagination(page);
				$(".counts").addClass("show");

				// load listings
				$(".loading, .pagination__loading").removeClass("show");

				listings = listings.filter(element => {
					return true; 
				});

				if(listings.length > 0) {
					// console.log(listings.map(listing => templateListing(listing, postType)));
					$('.listings-container--grid').empty();
					$('.listings-container--grid')
						.append(listings.map(listing => templateListing(listing, postType)));
				}
				else {
					$('.listings-container--grid').empty();
					$('.listings-container--grid').addClass('listings-container--no-listings')
					.append(`<h2>No ${postType}s available at this time</h2>`);
				}
			});
	}

	/**
	 * Check 'all' checkbox if none are selected, otherwise uncheck it
	 */
	function updateCatChecks() {
		$('#control__input--categories-all').prop(
			'checked',
			!$('.control__input--categories:not(#control__input--categories-all):checked').length
		);

		$('.control__input--categories:not(#control__input--categories-all):checked').each(function() {
		    $(this).closest('.control__label').addClass('active');
		});

		$('.control__input--categories:not(#control__input--categories-all):not(:checked)').each(function() {
		    $(this).closest('.control__label').removeClass('active');
		});

		loadPage();
	}

	/** LISTENERS *********************************************************************/
	$(document).ready(function () {
		perPage = parseInt($('#listings-grid').attr('data-perpage'));
		loadPage();
		
		var dateFormat = "mm/dd/yy",
		// var dateFormat = "yy-mm-dd",
		from = $( "#control__input--start-date" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true
	    }).on( "change", function() {
			from.datepicker( "option", getDate( this ) );
			loadPage();
	    });

		function getDate( element ) {
	      var date;
	      try {
	        date = $.datepicker.parseDate( dateFormat, element.value );
	      } catch( error ) {
	        date = null;
	      }
		  console.log(element.value);
	      return element.value;
	    }


		// Paging
		$(".pagination__button").on('click', function () {
			var page = parseInt($(this).attr("data-page"));
			loadPage(page, true);
		});

		// Form filters
		$('.control--categories').on('change', updateCatChecks);
		$('.filters').on('submit', function (e) {
			e.preventDefault();
			loadPage();
		});

	});
})(jQuery);
