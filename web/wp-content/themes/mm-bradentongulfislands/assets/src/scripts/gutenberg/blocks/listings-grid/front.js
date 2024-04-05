(function ($) {
	/** FUNCTIONS *********************************************************************/

    function truncateText(description, maxWords) {
	    // Split the description into an array of words
	    let words = description.split(' ');

	    // Check if the number of words is greater than the specified maximum
	    if (words.length > maxWords) {
	        // Join only the first 'maxWords' words and append ellipses
	        return words.slice(0, maxWords).join(' ') + "...";
	    } else {
	        // If the number of words is within the limit, return the original description
	        return description;
	    }
	}
	const PAGE_LENGTH = 12;


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
		let accommodationIcons = '';

		let testing = '';

		let thumbUrl = listing?.thumb_url || listing?.yoast_head_json?.og_image?.[0]?.url || placeHolder;

		switch (postType) {
			case 'event':
				description = listing?.excerpt.rendered || '';
				description = truncateText(description, 15);
				if (listing?.meta_fields?.eventastic_start_date) {

					let startDate = listing?.startDate;
					let endDate = listing?.endDate;

					// Start day Format
					let startdateObject = new Date(startDate);
					startdateObject = new Date( startdateObject.getTime() - startdateObject.getTimezoneOffset() * -60000 );
					// let startDay = startdateObject.getDate() + 1;
					let startDay = startdateObject.getDate();
					// if date is the first day of the month
					// if (startDay > new Date(startdateObject.getFullYear(), startdateObject.getMonth() + 1, 0).getDate()) {
					//     startDay = 1;
					//     startdateObject.setMonth(startdateObject.getMonth() + 1);
					// }
					const startMonth = new Intl.DateTimeFormat('en-US', { month: 'short' }).format(startdateObject);

					startDate = `${startMonth} ${startDay}`;

					// End day Format
					let enddateObject = new Date(endDate);
					enddateObject = new Date( enddateObject.getTime() - enddateObject.getTimezoneOffset() * -60000 );
					// let endDay = enddateObject.getDate() + 1;
					let endDay = enddateObject.getDate();
					// if date is the first day of the month
					// if (endDay > new Date(enddateObject.getFullYear(), enddateObject.getMonth() + 1, 0).getDate()) {
					//     endDay = 1;
					//     enddateObject.setMonth(enddateObject.getMonth() + 1);
					// }
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
				description = truncateText(description, 15);
				date = '';

				let amenities = listing?.meta_fields?.['partnerportal_accomodations-facility-amenities'];
				if(typeof amenities !== 'undefined') {
				amenities.forEach(amenity => {
					if(amenity == 'pet-friendly' || amenity =='eco-friendly' || amenity == 'on-site-dining') {
						accommodationIcons += `<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/${amenity}.png" alt="${amenity} icon" title="${amenity}">`
					}
				});
				}

				accommodations = listing?.meta_fields?.['partnerportal_accomodations-location'];
				if(typeof accommodations !== 'undefined') {
				accommodations.forEach(accommodation => {
					if(accommodation === 'beachfront' || accommodation === 'waterfront') {
						accommodationIcons += `<img src="/wp-content/themes/mm-bradentongulfislands/assets/images/icons/${accommodation}.png" alt="${accommodation} icon" title="${accommodation}">`
					}
				});
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
					<div class="accomodations-icons">
					${accommodationIcons}
					</div>
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
	/*
	 * Event functions
	 *
	 */
	async function loadAllInstances() {
		var start = jQuery('input[name="eventastic_start_date"]').val();
		var end = jQuery('input[name="eventastic_end_date"]').val();
		var eventInstanceQuery = {
			action: 'get_events_date_ordered'
		}
		if(start) eventInstanceQuery.start_date = start;
		if(end) eventInstanceQuery.end_date = end;
		let instances;
		await $.post('/wp-admin/admin-ajax.php', eventInstanceQuery, async function(response) {
			var instanceData = JSON.parse( response );
			instances = await processInstances(instanceData);
		});
		return instances;
	}
	function isRecurring(event) {
		const re1 = event?.meta_fields?.eventastic_recurring_repeat;
		const re2 = event?.meta_fields?.eventastic_recurring_days;
		if(
			(re1 && re1.length > 0 && re1[0] !== '') ||
			(re2 && re2.length > 0 && re2[0] !== '')
		) {
			return true;
		} else {
			return false;
		}
	}
	async function processInstances(instanceData) {
		await instanceData;
		var instances = {};
		var count = 0;
		var end = jQuery('input[name="eventastic_end_date"]').val();
		for (const [_, value] of Object.entries(instanceData.days)) {
			const date = value.meta.date;
			if(end && date > end) break; //if end date is set, skip any dates after it
			value.events.forEach(event => {
				instances[count] = {
					id: event,
					date: date
				}
				count++;
			});
			if(count > 692) break; //Gotta stop somewhere... This allows roughly 500+ events to be displayed/searched

		}
		return instances;
	}
	async function loadAllEvents() {
		// Update the URL to use the new custom endpoint
		var endpoint = "wp/v2/event";
		var order = "asc";
		var orderBy = "date";

		var url = `/wp-json/${endpoint}?order=${order}&orderby=${orderBy}`;
		var filters = $(".filters").serializeArray();
		if (filters) {
			filters.forEach(function (filter) {
				if (!!filter.value) {
					url += "&" + filter.name + "=" + filter.value;
				}
			});
		}

		return $.get(url)
			.done(function (events, status, xhr) {
				return events.slice(0, 100); // slice the events array to only include the first 100 events
			})
			.fail(function (err) {
				console.error(err);
			});
	}
	async function reconcileEvents(events, instances) {
		await Promise.all([events, instances]);
		events = Object.values(events);
		instances = Object.values(instances);
		//filter instance object as array to check if events contains an event with the same id as the instance
		let filtered = instances.filter(i => {
			return events.some(e => e.id === i.id);
		});

		//map over filtered instances and add the event data to each instance
		let singleEvents = [];
		let reconciled = filtered.map(i => {
			let event = {...events.find(e => e.id === i.id)};
			const recurs = isRecurring(event);
			event.endDate = recurs ? i.date : event?.meta_fields?.eventastic_end_date;
			event.startDate = recurs ? i.date : event?.meta_fields?.eventastic_start_date;
			event.recurring = recurs;
			return event;
		});

		//filter so that single events only have one instance!
		let pruned = reconciled.filter(i => {
			if(i.recurring) {
				return true;
			} else {
				if(singleEvents.includes(i.id)) {
					return false; //single event has already appeared
				} else {
					singleEvents.push(i.id); //first appearance of single event
					return true;
				}
			}
		});

		
		return pruned;
	}
	async function getEvents(page) {
		//Run both queries simultaneously
		const events = await loadAllEvents(); //Return all events that match all filters
		const instances = await loadAllInstances(); //Returns all instances that match dates
		const result = await reconcileEvents(events, instances);
		//use page and PAGE_LENGTH to slice result
		const start = (page - 1) * PAGE_LENGTH;
		const end = start + PAGE_LENGTH;
		const slicedResult = result.slice(start, end);

		return {
			total: result.length,
			events: slicedResult,
			first: start + 1,
			last: end
		}

	}

	/*
	 * End event functions
	 */

	/**
	 * Jump to the requested page when pagination buttons are clicked
	 * @param {Number} page              The page to load
	 * @param {Boolean} adjustScroll     Scroll the page to the top of the grid?
	 */
	async function loadPage(page = 1, adjustScroll = false) {
		// clear existing listings and show loader
		$('.listing').remove();
		$(".loading, .pagination__loading").addClass("show");

		// create wp-json URL for query
		var postType = $(".wp-block-mm-bradentongulfislands-listings-grid").attr("data-postType");
		var order = ['posts'].includes(postType) ? 'desc' : 'asc';
		var orderBy = ['listing',].includes(postType) ? 'title' : 'date';

		if(postType == 'event'){
			// get the page back up where it needs to be for viewing (it's slightly less jarring to do this pre-ajax call)
			if (adjustScroll) {
				$("html, body").animate({
					scrollTop: $('.grid-body').offset().top
				}, "10");
			}

			const {total, events} = await getEvents(page);
			var totalPages = parseInt(total / PAGE_LENGTH + 1);
			$(".count__page-total").text(total);
			$(".pagination__button--last").attr("data-page", totalPages);

			// update pagination
			updatePagination(page);
			$(".counts").addClass("show");

			// load listings
			$(".loading, .pagination__loading").removeClass("show");

			if(events.length > 0) {
				$('.listings-container--grid').empty();
				$('.listings-container--grid')
					.append(events.map(listing => templateListing(listing, postType)));
			}
			else {
				$('.listings-container--grid').empty();
				$('.listings-container--grid').addClass('listings-container--no-listings')
				.append(`<h2>No ${postType}s available at this time</h2>`);
			}

		} else {
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
			// if (adjustScroll) {
			// 	$("html, body").animate({
			// 		scrollTop: ($('.grid-body').offset().top - $(".header").height() - $(".grid-body").height())
			// 	}, "100");
			// }
			if (adjustScroll) {
				$("html, body").animate({
					scrollTop: $('.grid-body').offset().top
				}, "10");
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
	}

	/**
	 * Check 'all' checkbox if none are selected, otherwise uncheck it
	 */
	function updateCatChecks() {
		$('#control__input--categories-all.control__input--catscheck').prop(
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
	$(document).ready(async function () {
		perPage = parseInt($('#listings-grid').attr('data-perpage'));
		await loadPage();
		
		// var dateFormat = "mm/dd/yy";
		var dateFormat = "yy-mm-dd";
		let from = $( "#control__input--start-date" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat
	    }).on( "change", function() {
			from.datepicker( "option", getDate( this ) );
			loadPage();
	    });

		let to = $( "#control__input--end-date" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat
	    }).on( "change", function() {
			to.datepicker( "option", getDate( this ) );
			loadPage();
	    });

		function getDate( element ) {
	      var date;
	      try {
	        date = $.datepicker.parseDate( dateFormat, element.value );
	      } catch( error ) {
	        date = null;
	      }
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
