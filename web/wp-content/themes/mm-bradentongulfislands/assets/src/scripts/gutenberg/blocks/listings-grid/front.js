const PAGE_LENGTH = 12;
var STARTING_ZOOM = 11;
var STARTING_COORDS = [27.4190314, -82.3921034];
var map = false;
var allListings = false;
var markers = false;
var markersObject = {};

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

	/**
	 * Load the leaflet map to its container
	 * @param {String} mapId
	 */
	function loadMap(mapId) {
		if (!$("#" + mapId).length) return false;
	
		// init map object
		var map = L.map(mapId, {
			zoomControl: true,
		}).setView(STARTING_COORDS, STARTING_ZOOM);

		// set tileset
		// NOT USING STADIA FOR THIS BUILD
		L.tileLayer(
			"https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
			{
				maxZoom: 18,
				minZoom: 9,
				attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}
		).addTo(map);
		
		// add zoom cntrls back in preferable corner
		L.control
			.zoom({
				position: "bottomright",
			})
			.addTo(map);

		markers = L.layerGroup();

		return map;
	}

	/**
	 * Load map points from listings data
	 * @param {Object[]} listings       The listings data
	 */
	function loadMapPoints(listings) {

		// clear out map pointers
		if (markers) markers.clearLayers();

		var icon = L.icon({
			iconUrl:
				"/wp-content/themes/mm-bradentongulfislands/assets/images/icons/map-pin.svg",
			iconSize: [24, 48],
			iconAnchor: [12, 24],
		});
		console.log(listings);
		// Icon size and anchor size added to ensure correct placement of icons and markers throughout zoom positions
		listings.forEach(function (listing) {

			// Check for NaN and get lat/long
			if (
				typeof listing.meta_fields.partnerportal_longitude === "string" &&
				typeof listing.meta_fields.partnerportal_latitude === "string"
			) {
				var latitude = parseFloat(listing.meta_fields.partnerportal_latitude);
				var longitude = parseFloat(listing.meta_fields.partnerportal_longitude);

				// Ensure latitude and longitude are valid numbers
				if (!isNaN(latitude) && !isNaN(longitude)) {
					var coords = [latitude, longitude];
					console.log(coords);

					// Add marker to layer group
					var marker = L.marker(coords, {
						icon: icon,
					}).on("click", markerOnClick);

					marker.listingID = listing.id;
					markersObject[listing.id] = marker;

					marker.bindPopup(
						`<a href="${listing.link}">${listing.title.rendered}</a>`
					);
					markers.addLayer(marker);
				} else {
					console.error(`Invalid coordinates for listing ID: ${listing.id}`);
				}
			}

			// Add all markers to map
			map.addLayer(markers);

		})
	}

	function markerOnClick(e) {
		let showCaseSlider = jQuery("#listings-container--map");
		if (
			typeof showCaseSlider[0].swiper !== "undefined" &&
			showCaseSlider[0].swiper !== null
		) {
			// FUTURE no card scrolling thing for large - it doesn't work so good
			var cardIndex = jQuery('[data-listingID="' + this.listingID + '"]').attr(
				"data-swiper-slide-index"
			);
			if (cardIndex) {
				showCaseSlider[0].swiper.slideToLoop(cardIndex);
			}
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
		let accommodationIcons = '';
		var viewType = $(".view.active").data("view-type");
		let thumbUrl = listing?.thumb_url || listing?.yoast_head_json?.og_image?.[0]?.url || placeHolder;
		let mapClasses = viewType === "map" ? "swiper-slide" : ""; 

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

				let amenities = unserialize(listing?.meta_fields?.['partnerportal_accomodations-facility-amenities']);
	
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
			<article data-listingID='${listing.id
			}' class='listing listing--${postType} ${mapClasses}'>
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
		const re1 = event?.meta_fields?.eventastic_event_end;
		const re2 = event?.meta_fields?.eventastic_recurring_days;
		
		if(
			(re1 !== 'finite') &&
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
		
		// Gets instances without recurring date
		for (const [_, value] of Object.entries(instanceData.event_objects)) {

			const date = value.meta.end_date;
			if(end && date > end) break; //if end date is set, skip any dates after it
		
			instances[count] = {
				id: value.ID,
				date: date
			}
			count++;
			if(count > 692) break; //Gotta stop somewhere... This allows roughly 500+ events to be displayed/searched
		}
	
		return instances;
	}
	async function loadAllListings() {

		const endpoint = "wp/v2/listing";
		const order = "asc";
		const orderBy = "date";
	  
		let url = `/wp-json/${endpoint}?order=${order}&orderby=${orderBy}&activity=active&`;

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
		let page = 1;
		const perPage = 100;
		let allEvents = [];
		let moreEventsAvailable = true;
	  
		// Loop through pages until no more events are available
		while (moreEventsAvailable) {
		  try {
			const currentUrl = `${url}&page=${page}&per_page=${perPage}`;
			const response = await $.get(currentUrl);
			if (response.length === 0) {
			  moreEventsAvailable = false; // Exit the loop if no more events
			} else {
			  allEvents = allEvents.concat(response);
			  page++;
			}
		  } catch (error) {
			console.error(error);
			moreEventsAvailable = false; 
		  }
		}
	  
		return allEvents;
	}
	async function loadAllEvents() {

		const endpoint = "wp/v2/event";
		const order = "asc";
		const orderBy = "date";
	  
		let url = `/wp-json/${endpoint}?order=${order}&orderby=${orderBy}`;
		const filters = $(".filters").serializeArray();
		if (filters) {
		  filters.forEach(filter => {
			if (filter.value) {
			  url += "&" + filter.name + "=" + filter.value;
			}
		  });
		}
		console.log(url);
		let page = 1;
		const perPage = 100;
		let allEvents = [];
		let moreEventsAvailable = true;
	  
		// Loop through pages until no more events are available
		while (moreEventsAvailable) {
		  try {
			const currentUrl = `${url}&page=${page}&per_page=${perPage}`;
			const response = await $.get(currentUrl);
			if (response.length === 0) {
			  moreEventsAvailable = false; // Exit the loop if no more events
			} else {
			  allEvents = allEvents.concat(response);
			  page++;
			}
		  } catch (error) {
			console.error(error);
			moreEventsAvailable = false; 
		  }
		}
	  
		return allEvents;
	}	  
	async function reconcileEvents(events, instances) {
		await Promise.all([events, instances]);
		events = Object.values(events);
		instances = Object.values(instances);
		//filter instance object as array to check if events contains an event with the same id as the instance
		let filtered = instances.filter(i => {
			return events.some(e => e.id === i.id);
		});

		// Sort the dates and removes duplicates
		filtered.sort((a, b) => new Date(a.date) - new Date(b.date));
		filtered = filtered.filter((value, index, self) =>
			index === self.findIndex((t) => (
				t.id === value.id && t.date === value.date
			))
		);

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

		var viewType = $(".view.active").data("view-type");
		var listingsContainer = $(".listings-container.listings-container--grid");


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
			if (viewType) {
				url += `&activity=active&`
			}
			console.log(url);
			$.get(url)
				.done(function (listings, status, xhr) {

					// update totals
					var total = parseInt(xhr.getResponseHeader("X-WP-Total"));
					var totalPages = parseInt(xhr.getResponseHeader("X-WP-TotalPages"));
					$(".count__page-total").text(total);
					$(".pagination__button--last").attr("data-page", totalPages);
					console.log(url);
					// update pagination
					updatePagination(page);
					$(".counts").addClass("show");

					// load listings
					$(".loading, .pagination__loading").removeClass("show");

					listings = listings.filter(element => {
						return true; 
					});



					if (viewType === "map") {
						listingsContainer = $(
							".view.active .listings-container .swiper-wrapper"
						);
					}
					console.log(listingsContainer);

					if(listings.length > 0) {
						listingsContainer.empty();
						listingsContainer
							.append(listings.map(listing => templateListing(listing, postType)));
					}
					else {
						listingsContainer.empty();
						listingsContainer.addClass('listings-container--no-listings')
						.append(`<h2>No ${postType}s available at this time</h2>`);
					}

					// load map points
					// if (map) loadMapPoints(listings);

					//slider for mobile listings
					if (viewType === "map") {
						enableListingSlider();
					}

					// listen to the listings grid scroll and update the map
					$(".listings-container--map").on("scroll", function () {
					
						// get the scrolling div element
						var $scrollingDiv = $(this);

						// get the current vertical scroll position of the div
						var scrollTop = $scrollingDiv.scrollLeft();

						// loop through all the elements inside the div and check which one is at the top
						var $currentElement;
						$scrollingDiv.find(".listing--listing").each(function () {
							var $this = $(this);

							if (($this.offset().left) <= scrollTop) {
								$currentElement = $this;
								return;
							} else {
								// break out of the loop if the element is below the view
								return false;
							}
						});
						
						// now $currentElement is the element currently at the top of the view
						markersObject[$($currentElement).attr("data-listingid")].openPopup();
					});

				});
		}
	}

	/**
	 * Check 'all' checkbox if none are selected, otherwise uncheck it
	 */
	async function updateCatChecks() {
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

		if(map)	{
			allListings = await loadAllListings();
			loadMapPoints(allListings);
		}

		loadPage();
	}

	/**
	 * Unserialize
	 */
	function unserialize(serializedString) {
		var result = [];
		var match;
	
		// Regex to match strings and integers
		var regex = /s:\d+:"([^"]+)";|i:(\d+);|b:(\d);/g;
		
		// Iterate over the matches and push them to the result array
		while ((match = regex.exec(serializedString)) !== null) {
			if (match[1] !== undefined) {
				result.push(match[1]); // Push string
			} else if (match[2] !== undefined) {
				result.push(parseInt(match[2], 10)); // Push integer
			} else if (match[3] !== undefined) {
				result.push(Boolean(parseInt(match[3], 10))); // Push boolean
			}
		}
		
		return result;
	}

	/** LISTENERS *********************************************************************/
	$(document).ready(async function () {

		const getIsLarge = () =>
			jQuery("#isLarge").length && jQuery("#isLarge").css("float") !== "none";
		
		const getIsSmall = () =>
			jQuery("#isSmall").length && jQuery("#isSmall").css("float") !== "none";
		
		console.log(jQuery("#isLarge").css("float"));
		

		// Map Coordinate start point
		if(!getIsLarge()) {
		
			STARTING_COORDS = [27.4590324, -82.6521034];
		}

		perPage = parseInt($('#listings-grid').attr('data-perpage'));
			
		if ($('.view--map')) {
			console.log('Test');
			$('.view--map').addClass("active");
			$('.view--grid').removeClass("active");
			if (map) map.invalidateSize();
			loadPage(1, false);	
			map = loadMap("listings-grid__map-container");	
			if(map)	{
			allListings = await loadAllListings();
			loadMapPoints(allListings);
			}
		}
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

		// Resizing 
		$(window).resize(()=> {

			STARTING_COORDS = getIsLarge() ? [27.4190314, -82.3921034] : [27.4590324, -82.6521034];
			map.setView(STARTING_COORDS, STARTING_ZOOM);
		})

	});
})(jQuery);

let listingCards = document.getElementById("listings-container--map");
let swiperListingCard;
window.onresize = enableListingSlider;
window.onresize = enableListingSlider;

function enableListingSlider() {
	var postType = jQuery(".madden-block-listings-grid").attr("data-postType");
	// if (postType !== "listing") {
	// 	window.onresize = null;
	// 	return false;
	// }
	var viewType = jQuery(".view.active").data("view-type");
	if (viewType === "map") {
		listingsContainer = jQuery(
			".view.active .listings-container .swiper-wrapper"
		);
	}

	// if (mql.matches) {
	if (listingCards.classList.contains("swiper-initialized")) {
		swiperListingCard.destroy();
	}
	// args
	let swiperArgs = {
			slidesPerView: 3,
			centeredSlides: true,
			freeMode: true,
			loop: false,
			direction: "horizontal",
			spaceBetween: 35,
			slideClass: "listing-holder",
			allowTouchMove: true
		};
	

	if (!listingCards.classList.contains("swiper-initialized")) {
		
		swiperListingCard = new Swiper(listingCards, swiperArgs);
		console.log(swiperListingCard);
		var listingID = jQuery(
			swiperListingCard.slides[swiperListingCard.activeIndex]
		).attr("data-listingID");
		if (typeof listingID !== "undefined") {
			markersObject[listingID].openPopup();
		}
		swiperListingCard.on("slideChange", function (e) {
			var listingID = jQuery(
				swiperListingCard.slides[swiperListingCard.activeIndex]
			).attr("data-listingID");
			if (typeof listingID !== "undefined") {
				markersObject[listingID].openPopup();
			}
		});
	}
	// }
}