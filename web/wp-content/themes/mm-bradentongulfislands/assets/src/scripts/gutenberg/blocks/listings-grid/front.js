/** VARIABLES *****(*******************************************************************/
var pageLength;
var prevMonth;

(function ($) {
	/** FUNCTIONS *********************************************************************/

	/**
	 * Generate an HTML string for the given listing object
	 * @param {Object} listing          The listing data
	 * @param {String} postType         The listing post type
	 * @returns {String}                The HTML string representing the listing
	 */
	function templateListing(listing, postType) {
		let description = '';
		let date = '';
		let category = '';
		let month = '';
		let placeHolder = $(".wp-block-mm-bradentongulfislands-listings-grid").attr("data-default-thumb");

		let thumbUrl = listing?.yoast_head_json?.og_image?.[0]?.url || placeHolder;

		console.log(listing);

		switch (postType) {
			case 'event':
				if (listing?.meta_fields?.idss_event_start_date_time) {
					// format date
					const parsedDate = new Date(listing.meta_fields.idss_event_start_date_time);

					const monthNames = ["January", "February", "March", "April", "May", "June",
					"July", "August", "September", "October", "November", "December"
					];
					const currMonth = parsedDate.getMonth();
					let monthName = monthNames[currMonth].toUpperCase();

					if(prevMonth !== currMonth) {
					
						month = `<h2 class="listing listing--month">${monthName}</h2>`;
					}
					else {

						month = '';
					}

					prevMonth = currMonth;
					
					date = `<div class='date'>
						<span class='date__month'>
							${parsedDate.toLocaleString("en-US", { month: "short" })}
						</span>
						<span class='date__day'>
							${parsedDate.toLocaleString("en-US", { day: "numeric" })}
						</span>
					</div>`;
				}
				category = '';
				break;

			case 'listing':
				description += listing?.excerpt.rendered || '';
				date = '';
	
				let cat_names = listing?.listing_categories;
				let primary_cat_id = listing?.listing_categories;
				let cat_name = null;

				for(let i = 0; i < cat_names.length; i++) {
						
					if(!(cat_names[i].name === 'undefined')) {

						if(!primary_cat_id) {
							
							cat_name = cat_names[i].name;
							break;
						}

						//Add Primary Category
						else if(primary_cat_id == cat_names[i].term_id){
							cat_name = cat_names[i].name;
							break;
						}
					}
				};

				if(cat_name !== null) {
					category = `<div class='category-tag'>
						<span class='category-tag__name'>
							${cat_name}
						</span>
					</div>`;
				}

			break;
		}

		let html = `${month}
			<article class='listing listing--${postType}'>
			<a aria-label='${listing.title.rendered}' href='${listing.link}'>
				${date}
				${category}
				<div class='listing__thumb'>`;
					
					// Add an `onerror` event handler to the `img` tag
					html += `<img src='${thumbUrl}' alt='${listing.title.rendered}' onerror="this.onerror=null; this.src='${placeHolder}'" />`;

				html += `</div>
				<div class="listing__info">
					<h3 class='listing__title'>${listing.title.rendered}</h3>`;
				if(description.length != 0){
					html += `<div class='listing__description'>${description}</div>`;
				}
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
		prevMonth = undefined;
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
				if (element.meta_fields?.idss_listing_account_types?.[0] === 'Restricted') {
				    return false; // exclude this element from the filtered array
				}
					return true; // include this element in the filtered array
				});

				if(listings.length > 0) {
					// console.log(listings.map(listing => templateListing(listing, postType)));
					$('.listings-container--grid')
						.append(listings.map(listing => templateListing(listing, postType)));
				}
				else {
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

		loadPage();
	}

	/** LISTENERS *********************************************************************/
	$(document).ready(function () {
		perPage = parseInt($('#listings-grid').attr('data-perpage'));
		loadPage();
		
		// var dateFormat = "mm/d/yy",
		// from = $( "#control__input--start-date" ).datepicker({
		// 	defaultDate: "+1w",
		// 	changeMonth: true
	    // }).on( "change", function() {
		// 	from.datepicker( "option", getDate( this ) );
		// 	loadPage();
	    // }),
		// to = $( "#control__input--end-date" ).datepicker({
		// 	defaultDate: "+1w",
		// 	changeMonth: true
		// }).on( "change", function() {
		// 	to.datepicker( "option", getDate( this ) );
		// 	loadPage();
		// });

		// function getDate( element ) {
	    //   var date;
	    //   try {
	    //     date = $.datepicker.parseDate( dateFormat, element.value );
	    //   } catch( error ) {
	    //     date = null;
	    //   }
	    //   return element.value;
	    // }


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

		//Category Dropdown
		if($('.categoriesWrap .control__label').length > 3) {

			$('#categoriesDropdownButton').on('click', ()=> {

				$('#categoriesDropdownButton .arrow').toggleClass('is-active');
				$('.control__label:not(:nth-child(-n+3))').toggleClass('show');
				$('#categoriesDropdownButtonText').text($('#categoriesDropdownButtonText').text() === 'More' ? 'Close' : 'More');
			});		

			$('.categoriesDropdown').removeClass('categoriesDropdown--hide');	
		}
	});
})(jQuery);
