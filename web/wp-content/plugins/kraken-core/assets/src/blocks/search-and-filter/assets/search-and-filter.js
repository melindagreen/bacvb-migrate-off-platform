import $ from "jquery";
import apiFetch from "@wordpress/api-fetch";
import { addQueryArgs } from "@wordpress/url";

//only needed for events
import datepicker from "js-datepicker";

//only enable this if map view is needed
//import L from "leaflet";

//Leaflet images have to be imported or they will appear as broken
//import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
//import markerIcon from 'leaflet/dist/images/marker-icon.png';
//import markerShadow from 'leaflet/dist/images/marker-shadow.png';
//delete L.Icon.Default.prototype._getIconUrl;

//L.Icon.Default.mergeOptions({
//  iconRetinaUrl: markerIcon2x,
//  iconUrl: markerIcon,
//  shadowUrl: markerShadow
//});

import "../styles/style.scss";

window.addEventListener("DOMContentLoaded", () => {
  initSearchResults();
});

const blockSelector = `.wp-block-kraken-core-search-and-filter`;

function initSearchResults() {
  let blocks = document.querySelectorAll(blockSelector);

  blocks.forEach((block, index) => {
    const resultsWrapper = block.querySelector(".search-results");
    const resultsGrid = block.querySelector(".results-grid");
    const resultsMap = block.querySelector(".results-map");
    const filterBar = block.querySelector(".filter-bar");
    const filterOrderby = block.querySelector(".filter-bar .filter-orderby");
    const loadingSpinner = block.querySelector(".loading-spinner");
    const resultsInfo = block.querySelector(".results-count");
    const pagination = block.querySelector(".pagination");
    const pageNumbers = block.querySelector(".pagination.page-numbers");
    const loadMoreBtn = block.querySelector(".load-more .wp-element-button");
    const viewToggles = block.querySelectorAll(".view-toggle");
    const datePickers = [];

    class ListingsSearch {
      constructor() {
        this.uid = null;

        // Store the initial args
        this.queryArgs = null;

        //query
        this.results = [];
        this.posttype = null;
        this.orderby = "date";
        this.order = "desc";
        this.cardjson = null;

        //filters
        this.filters = {
          search: "",
          date_query: {
            date_filter: false,
            date_range: "",
            start_date: "",
            end_date: "",
          },
          tax_query: {},
          custom: {},
        };

        //init filters
        this.initTaxQuery = false;
        this.initMetaQuery = false;
        this.requireAllTerms = false;

        //pagination
        this.page = 1;
        this.perPage = 6;
        this.perPageMobile = 3;
        this.totalPages = null;
        this.totalResults = null;
        this.dots1 = false;
        this.dots2 = false;

        //map
        this.markers = [];
        this.map = null;
        this.mapPopup = null;
        this.bounds = null;
        this.mapLoaded = false;

        //other
        this.currentView = "grid";
        this.isSearching = [];

        // prevent staggered searches, where 2 searches can
        // clear the grid before the 1st has populated the grid
        const handleSearch = () => {
          if (this.isSearching[0]?.started === false) {
            this.isSearching[0].started = true;
            const search = this.isSearching[0];
            this.filters.search = search.val;

            resultsWrapper.dataset.page = 1;
            this.initialize("refresh");
          }
        };

        resultsGrid.addEventListener("mm-search-filter/search", () => handleSearch.apply(this));
      }

      /*
			refresh will empty the current results & replace them with new results
			load-more will append new results to the existing results
			*/
      initialize = (type = "init") => {
        if (type === "refresh" || type === "popstate") {
          //remove results and start fresh
          if (resultsGrid) {
            resultsGrid.innerHTML = "";
          }
          //go to top of block before refresh so loading spinner is in view
          if (pageNumbers) {
            block.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
          }
        }

        const data = resultsWrapper.dataset;

        this.uid = data.uid;
        this.queryArgs = JSON.parse(data.queryargs);

        this.page = parseInt(data.page);
        this.perPage = parseInt(data.perpage);
        this.perPageMobile = parseInt(data.perpagemobile);
        this.totalPages = parseInt(data.totalpages);
        this.totalResults = parseInt(data.totalresults);
        this.posttype = data.posttype;
        this.cardjson = window.krakenSearchFilter.blocks[this.uid].cardJson;
        this.requireAllTerms = data.requireallterms;

        if (this.filters.search !== "") {
          //if user is searching for a term; reorder by relevance
          this.orderby = "relevance";
          this.order = "desc";
        } else if (data.orderby === "rand") {
          //random order is not valid for the rest api w/o custom endpoint
          this.orderby = "title";
          this.order = "asc";
        } else {
          this.orderby = data.orderby;
          this.order = data.order;
        }

        if (this.queryArgs.tax_query) {
          this.initTaxQuery = this.queryArgs.tax_query;
        }

        if (this.queryArgs.meta_query) {
          this.initMetaQuery = this.queryArgs.meta_query;
        }

        if (resultsGrid) {
          this.results = resultsGrid.querySelectorAll("article");
        }

        //for event post types & event date filtering
        if (this.posttype.includes("event")) {
          if (datePickers.length) {
            this.filters.date_query.date_filter = true;
            this.filters.date_query.date_range = data.date_range;
            if (type === "init") {
              if (!this.filters.date_query.start_date) {
                this.filters.date_query.start_date = data.start_date;
              }
              if (!this.filters.date_query.end_date) {
                this.filters.date_query.end_date = data.end_date;
              }
            }
          }
        }

        //if the only available view is map; switch the current view & init map
        if (type === "init" && !resultsGrid && resultsMap && !this.mapLoaded) {
          loadingSpinner.style.display = "";
          this.currentView = "map";
          resultsMap.classList.add("active");
          resultsMap.classList.add("loading");
          this.initMap();
        }

        // Only refresh browser state on non-popstate events
        // If this runs on popstate event it will create a loop
        if (type === "refresh" || type === "load-more") {
          this.refreshBrowserState();
        }

        if (type !== "init") {
          loadingSpinner.style.display = "";
          if (resultsMap) {
            resultsMap.classList.add("loading");
          }

          if (resultsInfo) {
            resultsInfo.style.display = "none";
          }

          if (pagination) {
            pagination.style.display = "none";
          }

          this.fetchResults().then(() => {
            if (this.currentView === "map") {
              this.displayMapMarkers();
            } else {
              this.displayResults();
            }
            this.updatePagination();

            // process next searchTerm
            this.isSearching.shift();
            if (this.isSearching.length > 0) {
              resultsGrid.dispatchEvent(new Event("mm-search-filter/search"));
            }

            //go to top of block again after the content refreshes
            if (pageNumbers) {
              block.scrollIntoView({
                behavior: "smooth",
                block: "start",
              });
            }
          });
        } else {
          if (pageNumbers) {
            this.enableNavigationEvents();
          }
        }
      };

      //type = grid will replace the results
      //type = map will keep appending the results until all results are loaded
      fetchResults = (type = "grid") => {
        return new Promise(async (resolve, reject) => {
          try {
            const updatedQueryArgs = this.queryArgs;
            updatedQueryArgs.orderby = this.orderby;
            updatedQueryArgs.order = this.order;

            updatedQueryArgs.posts_per_page = this.getResultsPerPage();
            updatedQueryArgs.paged = this.page;

            // Tax query
            updatedQueryArgs.tax_query = [];
            if (this.initTaxQuery) {
              updatedQueryArgs.tax_query.push(this.initTaxQuery);
            }
            if (this.filters.tax_query) {
              const taxQueryFilter = [{ relation: this.requireAllTerms ? "AND" : "OR" }];
              Object.entries(this.filters.tax_query).forEach(([key, value]) => {
                const updatedKey = key === "categories" ? "category" : key;
                taxQueryFilter.push({
                  taxonomy: updatedKey,
                  terms: value,
                  operator: this.requireAllTerms ? "AND" : "IN",
                });
              });
              updatedQueryArgs.tax_query.push(taxQueryFilter);
            }
            if (this.filters.custom) {
              updatedQueryArgs.custom_filters = this.filters.custom;
            }

            // Meta query
            updatedQueryArgs.meta_query = [];
            if (this.initMetaQuery) {
              updatedQueryArgs.meta_query.push(this.initMetaQuery);
            }
            if (this.filters.meta_query) {
              const metaQueryFilter = [];
              Object.entries(this.filters.meta_query).forEach(([key, value]) => {
                metaQueryFilter.push({
                  key: key,
                  value: value,
                });
              });
              updatedQueryArgs.meta_query.push(metaQueryFilter);
            }

            // Search
            if (this.filters.search && this.filters.search.trim() !== "") {
              updatedQueryArgs.s = this.filters.search;
            } else {
              // Explicitly remove search parameter when cleared
              delete updatedQueryArgs.s;
            }

            // Card attributes
            updatedQueryArgs.cardjson = this.cardjson;

            const results = await apiFetch({
              path: addQueryArgs(`/kraken-core/v1/searchFilterResults`, {
                queryArgs: updatedQueryArgs,
                dateFilters:
                  this.filters.date_query && this.filters.date_query.date_filter
                    ? {
                        enableDateQuery: true,
                        selectedDateRange: this.filters.date_query.date_range,
                        start_date: this.filters.date_query.start_date,
                        end_date: this.filters.date_query.end_date,
                      }
                    : null,
              }),
            });

            // Store results and resolve
            this.totalPages = parseInt(results.total_pages);
            this.totalResults = parseInt(results.total_posts);
            this.results = results.post_data;

            //this.displayResults();

            resolve(results); // 👈 Restore the same resolve() behavior
          } catch (error) {
            console.error("Fetch error:", error);
            reject(error); // Allow error handling upstream
          }
        });
      };

      displayResults() {
        if (loadingSpinner) {
          loadingSpinner.style.display = "none";
        }

        // Empty the grid
        // if (!loadMoreBtn) {
        // 	$(resultsGrid).empty();
        // }

        if (this.results.length) {
          //output the results
          this.results.forEach((post) => {
            let cardHtml = "";

            //use content card block output if possible
            if (post.content_card) {
              cardHtml += post.content_card;
            } else {
              let image = "";
              if (post.featured_media !== 0) {
                image = post._embedded["wp:featuredmedia"][0].source_url;
              }
              cardHtml += '<article class="grid-item">';
              cardHtml += '<a href="' + post.link + '" title="' + post.title.rendered + '">';
              cardHtml += '<div class="featured-media">';
              if (image) {
                cardHtml +=
                  '<img decoding="async" src="' +
                  image +
                  '" class="attachment-large size-large wp-post-image" title="' +
                  post.title.rendered +
                  '">';
              }
              cardHtml += "</div>";
              cardHtml += '<div class="content">';
              cardHtml += '<h3 class="post-title">' + post.title.rendered + "</h3>";
              cardHtml += '<div class="read-more">LEARN MORE →</div>';
              cardHtml += "</div></a></article>";
            }

            $(resultsGrid).append(cardHtml);
          });
        }
      }

      updatePagination() {
        if (this.results.length) {
          //remove no results warning if applicable
          if (resultsWrapper.querySelector(".no-results-found")) {
            resultsWrapper.querySelector(".no-results-found").remove();
          }

          //output the result count info
          if (resultsInfo) {
            resultsInfo.style.display = "";
            resultsInfo.innerHTML = this.calculateResultsInfo();
          }

          //re-show and/or output pagination info
          if (this.totalPages > 1 && pagination) {
            pagination.style.display = "";
            if (loadMoreBtn) {
              this.updateLoadMoreBtn();
              if (this.page < this.totalPages) {
                loadMoreBtn.style.display = "";
              }
            } else {
              pagination.innerHTML = this.calculatePaginationInfo();
              this.enableNavigationEvents();
              //if the user lost focus when clicking the pagination refocus the pagination links
              if (document.activeElement === document.body || document.activeElement === null) {
                block.querySelector('.go-to-page[data-page="' + this.page + '"]').focus();
              }
            }
          }
        } else {
          //show no results found
          $(resultsWrapper)
            .find(".active")
            .prepend('<p class="no-results-found">No Results Found.</p>');

          //hide result count info
          if (resultsInfo) {
            resultsInfo.style.display = "none";
          }

          //hide pagination
          if (pagination) {
            pagination.style.display = "none";
          }
        }
      }

      calculateResultsInfo() {
        let resultsLow = this.page * this.perPage - (this.perPage - 1);
        let resultsHigh = this.page * this.perPage;
        let resultsTotal = this.totalResults;

        if (resultsTotal < resultsHigh) {
          resultsHigh = resultsTotal;
        }

        if (loadMoreBtn) {
          resultsLow = 1;
        }

        return resultsLow + " - " + resultsHigh + " of " + resultsTotal + " Results";
      }

      calculatePaginationInfo() {
        let path = window.location.pathname;
        let baseUrl = path.replace(/\/page\/\d+\/?$/, "/");
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }

        let paginationHtml = "";

        if (this.totalPages > 1) {
          let range = [];
          let page = parseInt(this.page);

          if (this.totalPages > 6) {
            if (page - 3 > 1) {
              this.dots1 = true;
            } else {
              this.dots1 = false;
            }

            for (let i = 2; i < this.totalPages; i++) {
              if (range.length === 5) {
                break;
              }
              if (i > page - 3 && i < page + 5 && i < this.totalPages) {
                range.push(i);
              }
            }
          } else {
            for (let i = 2; i < this.totalPages; i++) {
              range.push(i);
            }
          }

          if (page + 3 < this.totalPages) {
            this.dots2 = true;
          } else {
            this.dots2 = false;
          }

          let previousPage = page - 1 <= 1 ? baseUrl : baseUrl + "page/" + (page - 1) + "/";

          paginationHtml += `<a href="${previousPage}" aria-label="Go to previous page" class="go-to-page go-to-prev ${
            page === 1 && "disabled"
          }" data-page="${page - 1}" >${krakenSearchFilter.svgs.prev}</a>`;

          paginationHtml += `<a href="${baseUrl}" aria-label="Go to page 1 of results" class="go-to-page ${
            page === 1 ? "active" : ""
          }" data-page="1">1</a>`;

          if (this.dots1) {
            paginationHtml += '<div class="pagination-dots">…</div>';
          }

          range.forEach((number) => {
            paginationHtml += `<a href="${baseUrl}page/${number}/" aria-label="Go to page ${number} of results" class="go-to-page ${
              page === number ? "active" : ""
            }" data-page="${number}">${number}</a>`;
          });

          if (this.dots2) {
            paginationHtml += '<div class="pagination-dots">…</div>';
          }

          paginationHtml += `<a href="${baseUrl}page/${this.totalPages}/" class="go-to-page ${
            page === this.totalPages ? "active" : ""
          }" aria-label="Go to page ${this.totalPages} of results" data-page="${this.totalPages}">${
            this.totalPages
          }</a>`;

          let nextPage =
            page + 1 > this.totalPages
              ? baseUrl + "page/" + this.totalPages + "/"
              : baseUrl + "page/" + (page + 1) + "/";

          paginationHtml += `<a href="${nextPage}" aria-label="Go to next page" class="go-to-page go-to-next ${
            this.totalPages === page && "disabled"
          }" data-page="${this.totalPages === page ? this.totalPages : page + 1}">${
            krakenSearchFilter.svgs.next
          }</a>`;
        }

        return paginationHtml;
      }

      updateLoadMoreBtn() {
        let path = window.location.pathname;
        let baseUrl = path.replace(/\/page\/\d+\/?$/, "/");
        if (!baseUrl.endsWith("/")) {
          baseUrl += "/";
        }

        if (this.totalPages > 1) {
          let page = parseInt(this.page);
          let nextPage =
            page + 1 > this.totalPages
              ? baseUrl + "page/" + this.totalPages + "/"
              : baseUrl + "page/" + (page + 1) + "/";
          loadMoreBtn.setAttribute("href", nextPage);
        }
      }

      enableNavigationEvents() {
        let navBtns = block.querySelectorAll(".go-to-page");
        navBtns.forEach((btn) => {
          btn.addEventListener("click", (e) => {
            e.preventDefault();
            let goToPage = btn.dataset.page;
            this.navigatePages(goToPage);
          });
        });
      }

      navigatePages(pageNumber) {
        resultsWrapper.dataset.page = pageNumber;
        this.initialize("refresh");
      }

      loadMoreResults() {
        if (this.page < this.totalPages) {
          resultsWrapper.dataset.page = this.page + 1;

          if (this.page + 1 === this.totalPages) {
            loadMoreBtn.style.display = "none";
          }

          this.initialize("load-more");
        } else {
          loadMoreBtn.style.display = "none";
        }
      }

      refreshBrowserState = () => {
        // Updates the browser URL and page history to emulate pagination
        const params = new URLSearchParams();
        const state = {};

        // --- Get today's date in the required YYYY/MM/DD format ---
        const today = new Date();
        const formattedToday = [
          today.getFullYear(),
          String(today.getMonth() + 1).padStart(2, "0"),
          String(today.getDate()).padStart(2, "0"),
        ].join("/");

        // Handle tax query filters
        for (const key in this.filters.tax_query) {
          const value = this.filters.tax_query[key];
          if (value && value.length > 0) {
            params.set(key, value.join(","));
            state[key] = value;
          }
        }

        // Handle custom filters
        for (const key in this.filters.custom) {
          const value = this.filters.custom[key];
          if (value && value.length > 0) {
            params.set(key, value.join(","));
            state[key] = value;
          }
        }

        // Handle date query filters
        const { date_range, start_date, end_date } = this.filters.date_query;
        let hasDateFilter = false;

        // --- Conditional Start Date ---
        // Only add the start_date to the URL if it's not today's date.
        if (start_date && start_date !== formattedToday) {
          params.set("start_date", start_date);
          state.start_date = start_date;
          hasDateFilter = true;
        }

        if (end_date) {
          params.set("end_date", end_date);
          state.end_date = end_date;
          hasDateFilter = true;
        }

        if (date_range && parseInt(date_range, 10) > 0) {
          params.set("date_range", date_range);
          state.date_range = date_range;
          hasDateFilter = true;
        }

        // Only include the date_filter parameter if a non-default date has been set
        if (hasDateFilter) {
          params.set("date_filter", "true");
          state.date_filter = true;
        }

        // Handle search filter
        if (this.filters.search) {
          params.set("search", this.filters.search);
          state.search = this.filters.search;
        }

        // Handle order by option
        if (this.orderby === "title" && filterOrderby) {
          params.set("orderby", this.orderby);
          state.orderby = this.orderby;
        }

        // Handle pagination state for non-"load more" pagination
        if (!loadMoreBtn) {
          state.page = this.page;
        }

        // Only push a new state to browser history if the state has changed
        if (JSON.stringify(state) !== JSON.stringify(window.history.state)) {
          const pathname = window.location.pathname.split("page")[0];
          let stateURL = pathname;

          if (this.page > 1 && !loadMoreBtn) {
            stateURL += "page/" + this.page + "/";
          }

          const queryString = params.toString();
          if (queryString) {
            stateURL += "?" + queryString;
          }

          window.history.pushState(state, "", stateURL);
        }
      };

      restoreBrowserState = (state) => {
        //Clear all filters before applying the new state
        this.clearAllFilters(false);

        //Loop through the state object and apply the filters
        Object.keys(state).forEach((key) => {
          const filters = block.querySelectorAll(`input[data-type="${key}"]`);

          if (filters.length) {
            const stateValue = state[key];

            filters.forEach((input) => {
              if (["start_date", "end_date"].includes(key)) {
                if (stateValue !== "") {
                  const date = new Date(stateValue);
                  input.value = date.toDateString();
                } else {
                  input.value = stateValue;
                }
              } else if (input.type === "checkbox") {
                if (Array.isArray(stateValue)) {
                  input.checked = stateValue.some((val) => String(val) === input.value);
                } else {
                  input.checked = input.value == stateValue;
                }
              } else if (input.type === "radio") {
                if (input.value === stateValue) {
                  input.checked = true;
                } else {
                  input.checked = false;
                }
              } else {
                input.value = stateValue;
              }
              //Apply the filters but do not refresh results until everything is applied
              this.applyFilters(key, input, false);
            });
          }
        });

        //when using the load more button, we aren't exposing the page numbers to regular users
        //only filters are saved in the state for the load more button
        //so we reset the page whenever the user wants to restore a previous state
        if (loadMoreBtn) {
          resultsWrapper.dataset.page = 1;
        }

        //Refresh the results
        this.initialize("popstate");
      };

      applyFilters = (type, input, refreshResults = true) => {
        if (type === "search") {
          // Only refresh the results if the search value has actually changed
          if (this.filters.search !== input.value) {
            this.isSearching = this?.isSearching ?? [];
            this.filters.search = input.value;
            this.isSearching.push({
              started: false,
              val: this.filters.search,
            });
            // replacing but still calling this.initialize("refresh");
            resultsGrid.dispatchEvent(new Event("mm-search-filter/search"));
            return;
          } else {
            return;
          }
        } else if (type === "orderby") {
          if (input.checked) {
            resultsWrapper.dataset.orderby = input.value;
            if (input.value === "title") {
              resultsWrapper.dataset.order = "asc";
            } else {
              resultsWrapper.dataset.order = "desc";
            }
          }
        } else if (["event_date", "start_date", "end_date"].includes(type)) {
          // Find both date picker inputs to ensure we always have the latest values
          const startDateInput = block.querySelector('input[name="start_date"]');
          const endDateInput = block.querySelector('input[name="end_date"]');

          const formatDate = (dateValue) => {
            if (!dateValue) return "";
            const newDate = new Date(dateValue);
            if (!isNaN(newDate)) {
              return [
                newDate.getFullYear(),
                String(newDate.getMonth() + 1).padStart(2, "0"),
                String(newDate.getDate()).padStart(2, "0"),
              ].join("/");
            }
            return "";
          };

          // Always update both start and end dates in our filters object
          if (startDateInput) {
            this.filters.date_query.start_date = formatDate(startDateInput.value);
          }
          if (endDateInput) {
            this.filters.date_query.end_date = formatDate(endDateInput.value);
          }
        } else {
          const filterType = input.dataset.filtertype;

          if ("taxonomy" === filterType) {
            //check for existing filter values
            let filterValues = this.filters.tax_query[type];

            if (!filterValues) {
              filterValues = [];
            }

            //if tax query is enabled
            //if filter type matches the tax query taxonomy
            //if filterValues is already set & if the terms match the default terms
            //reset to empty so we can filter by only the selected terms
            //the initialize function will re-add the default terms if nothing is selected
            if (resultsWrapper.dataset.taxquery && resultsWrapper.dataset.taxonomy === type) {
              if (filterValues) {
                let terms = filterValues.join(", ");
                if (terms === resultsWrapper.dataset.terms) {
                  filterValues = [];
                }
              }
            }

            //add or remove the new value
            if (input.checked) {
              filterValues.push(input.value);
              this.addActiveFilterButton(type, input);
            } else {
              filterValues = filterValues.filter((item) => {
                return item !== input.value;
              });
              this.removeActiveFilterButton(input);
            }

            //update the results
            if (filterValues.length > 0) {
              this.filters.tax_query[type] = filterValues;
            } else {
              delete this.filters.tax_query[type];
            }

            //display the active filter count
            let filterWrapper = document.querySelector(
              '.filter-taxonomy[data-type="' + type + '"]',
            );
            this.updateActiveFilterText(filterWrapper);
          } else if ("custom" == filterType) {
            //check for existing filter values
            let filterValues = this.filters.custom[type];

            if (!filterValues) {
              filterValues = [];
            }

            //add or remove the new value
            if (input.checked) {
              filterValues.push(input.value);
              this.addActiveFilterButton(type, input);
            } else {
              filterValues = filterValues.filter((item) => {
                return item !== input.value;
              });
              this.removeActiveFilterButton(input);
            }

            //update the results
            if (filterValues.length > 0) {
              this.filters.custom[type] = filterValues;
            } else {
              delete this.filters.custom[type];
            }

            //display the active filter count
            let filterWrapper = document.querySelector('.filter-custom[data-type="' + type + '"]');
            this.updateActiveFilterText(filterWrapper);
          }
        }

        if (refreshResults) {
          //always reset to the first page when updating filters
          resultsWrapper.dataset.page = 1;
          this.initialize("refresh");
        }
      };

      clearAllFilters = (refreshResults = true) => {
        //empty all filter values & refresh
        this.filters.search = "";
        this.filters.tax_query = {};
        this.filters.custom = {};

        //reset date queries for events
        if (this.posttype.includes("event")) {
          const today = new Date();
          const formattedToday = [
            today.getFullYear(),
            String(today.getMonth() + 1).padStart(2, "0"),
            String(today.getDate()).padStart(2, "0"),
          ].join("/");

          // Reset the date pickers in the UI
          if (datePickers.length) {
            datePickers.forEach((picker) => {
              if (picker.el.name === "start_date") {
                // Set the start date picker to today
                picker.setDate(today, true);
              } else if (picker.el.name === "end_date") {
                // Clear the end date picker
                picker.setDate(null, true);
              }
            });
          }

          // Reset the internal filter state
          this.filters.date_query = {
            date_filter: true, // Keep this true as we have a start date
            start_date: formattedToday, // Set start_date to today
            end_date: "", // Clear the end_date
            date_range: resultsWrapper.dataset.date_range || "", // Reset to initial date_range if it exists
          };
        }

        //reset search input
        let searchInput = block.querySelector(".filter-search-input");
        if (searchInput) {
          searchInput.value = "";
          // Also clear the internal search filter
          this.filters.search = "";
        }

        //reset all taxonomy filters
        let filterInputs = block.querySelectorAll('.filter-wrapper input[type="checkbox"]');

        if (filterInputs) {
          filterInputs.forEach((input) => {
            input.checked = false;
          });
        }

        //reset any active filter counts
        let activeFilters = block.querySelectorAll(".filter-active-count");
        if (activeFilters) {
          activeFilters.forEach((filter) => {
            filter.innerText = "";
            filter.style.display = "none";
          });
        }

        //reset any active filter buttons
        let activeFilterBtns = block.querySelector(".active-filters");
        if (activeFilterBtns) {
          activeFilterBtns.innerHTML = "";
        }

        //reset orderby
        if (filterOrderby) {
          let orderByInputs = filterOrderby.querySelectorAll("input");
          orderByInputs.forEach((input) => {
            //reset to initial value if possible
            if (input.value === this.queryArgs.orderby) {
              input.click();
            } else {
              input.checked = false;
            }
          });
        }

        //refresh the results
        if (refreshResults) {
          resultsWrapper.dataset.page = 1;
          this.initialize("refresh");
        }
      };

      enableActiveFilterEvent = (filter) => {
        let id = filter.dataset.term_id;
        let type = filter.dataset.type;
        let input = block.querySelector('input[data-type="' + type + '"][value="' + id + '"]');
        if (input) {
          filter.addEventListener("click", () => {
            input.checked = false;
            filter.remove();
            this.applyFilters(type, input);
          });
        }
      };

      addActiveFilterButton = (type, input) => {
        let activeFilters = block.querySelector(".active-filters");
        if (activeFilters) {
          let existing = block.querySelector(
            '.active-filters button[data-term_id="' + input.value + '"]',
          );
          if (!existing) {
            let label = document.querySelector('label[for="' + input.id + '"]');
            let filter = document.createElement("button");
            filter.dataset.type = type;
            filter.dataset.term_id = input.value;
            filter.innerHTML = `${label.textContent.trim()}${krakenSearchFilter.svgs.close}`;
            activeFilters.appendChild(filter);
            this.enableActiveFilterEvent(filter);
          }
        }
      };

      removeActiveFilterButton = (input) => {
        let activeFilters = block.querySelector(".active-filters");
        if (activeFilters) {
          let existing = block.querySelector(
            '.active-filters button[data-term_id="' + input.value + '"]',
          );
          if (existing) {
            existing.remove();
          }
        }
      };

      updateActiveFilterText = (filter) => {
        let btn = filter.querySelector(".filter-active-count");
        if (btn) {
          let inputs = filter.querySelectorAll('input[type="checkbox"]:checked');
          let btnText = "";
          if (inputs.length > 0) {
            btnText = inputs.length;
            btn.innerText = btnText;
            btn.style.display = "flex";
          } else {
            btn.innerText = btnText;
            btn.style.display = "none";
          }
        }
      };

      getResultsPerPage() {
        if (window.matchMedia("(max-width: 782px)").matches) {
          return this.perPageMobile;
        }
        return this.perPage;
      }

      switchViews = (view) => {
        this.currentView = view;

        if (this.currentView === "grid") {
          resultsMap.classList.remove("active");
          resultsGrid.classList.add("active");
          this.initialize("refresh");
        } else {
          resultsGrid.classList.remove("active");
          loadingSpinner.style.display = "";
          resultsMap.classList.add("loading");
          resultsMap.classList.add("active");
          //when switching views
          //if map has not been loaded; load it
          //if map is already loaded; refresh the markers
          if (!this.mapLoaded) {
            this.initMap();
          } else {
            this.loadMapMarkers();
          }
        }
      };

      initMap() {
        //init map with center & zoom
        this.map = L.map("map", {
          center: [41.6475876, -91.5819347],
          zoom: 12,
        });

        //add tile layer
        L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
          maxZoom: 19,
          attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        }).addTo(this.map);

        //check off as loaded so it does not init again
        this.mapLoaded = true;

        //load markers based on results array
        this.loadMapMarkers();
      }

      loadMapMarkers() {
        /*
			  //if wanting to display all ALL available results
			  //keep fetching if there are more than 100 (api limit?)
			  this.page = 1;

			  if (this.totalPages > this.page) {
				  this.page = this.page + 1;
				  this.fetchResults("map")
			  }

			  this.fetchResults().then(() => {
				  console.log(this.totalPages);
				  console.log(this.page);
				  if (this.totalPages > this.page) {
					  for (let i = 0; i < this.totalPages; i++) {
						  //fetch & append more results
					  }
				  }
			  });
			  */

        //clear current markers
        this.clearMapMarkers();
        //fetch new markers & display
        this.initialize("refresh");
      }

      clearMapMarkers() {
        //remove any/all markers currently on the map
        if (this.markers.length) {
          for (let i = 0; i < this.markers.length; i++) {
            if (this.markers[i]) {
              this.markers[i].remove();
            }
          }
          this.markers = [];
        }
      }

      displayMapMarkers() {
        this.clearMapMarkers();

        //create new markers
        if (this.results.length) {
          //create a new bounds object every refresh
          this.bounds = L.latLngBounds();

          this.results.forEach((result, index) => {
            if (!result.latitude || !result.longitude) {
              console.log("lat/lng missing for " + result.id);
              return;
            }

            let latLng = [parseFloat(result.latitude), parseFloat(result.longitude)];

            //Grab the lat/lng coordinates & create the marker
            this.markers[index] = L.marker(latLng);

            //Bind popup
            let popupContent = result.content_card;
            this.markers[index].bindPopup(popupContent).openPopup();

            //Extend map bounds
            this.bounds.extend(latLng);

            //Add marker to the map
            this.markers[index].addTo(this.map);
          });

          //center the map on the new markers
          this.map.fitBounds(this.bounds);
        }

        resultsMap.classList.remove("loading");
        loadingSpinner.style.display = "none";
      }
    }

    //if filters or pagination aren't enabled, we don't need to run any ajax calls
    const listingResults = new ListingsSearch();

    if (filterBar || pagination) {
      listingResults.initialize();

      // If using regular pagination, watch for forward/back events to emulate a true paginated page.
      if (pagination) {
        window.addEventListener("popstate", (e) => {
          if (e.state) {
            const data = resultsWrapper;
            data.dataset.page = e.state.page;
            listingResults.restoreBrowserState(e.state);
          }
        });
      }
    }

    //Search & filter inputs
    if (filterBar) {
      const searchInput = block.querySelector(".filter-search-input");
      const filterBtns = block.querySelectorAll(".filter-toggle-btn");
      const filterInputs = block.querySelectorAll(".filter-wrapper input");
      const filterClearAll = block.querySelector(".filter-clear-all");
      const filterEventDates = block.querySelectorAll(".filter-event-dates input");
      const activeFilters = block.querySelectorAll(".active-filters button");

      if (filterBtns) {
        /* Show any active filter counts on page load */
        const filterWrappers = block.querySelectorAll(".filter-taxonomy, .filter-custom");

        filterWrappers.forEach((filter) => {
          listingResults.updateActiveFilterText(filter);
        });

        /* Filter active class for dropdowns */
        filterBtns.forEach((filterBtn) => {
          let wrapper = filterBtn.parentElement;
          let dropdown = filterBtn.nextElementSibling;

          /* Toggle event */
          filterBtn.addEventListener("click", (event) => {
            event.stopPropagation();
            if (wrapper.classList.contains("active")) {
              closeFilterDropdown(wrapper, filterBtn, dropdown);
            } else {
              openFilterDropdown(wrapper, filterBtn, dropdown);
            }

            // hide datepickers
            for (const datepicker of datePickers) {
              if (!datepicker.calendarContainer.classList.contains("qs-hidden")) {
                datepicker.calendarContainer.classList.add("qs-hidden");
                return;
              }
            }
          });

          /* Close on click outside of area event */
          document.addEventListener("click", (e) => {
            if (!filterBtn.contains(e.target) && !(dropdown && dropdown.contains(e.target))) {
              closeFilterDropdown(wrapper, filterBtn, dropdown);
            }
          });

          /* Close on escape events */
          filterBtn.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
              closeFilterDropdown(wrapper, filterBtn, dropdown);
              filterBtn.blur();
            }
          });

          dropdown.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
              closeFilterDropdown(wrapper, filterBtn, dropdown);
              filterBtn.focus();
            }
          });

          /* Close dropdown if focus leaves both filter and dropdown
           */
          document.addEventListener("focusin", (e) => {
            if (!filterBtn.contains(e.target) && !dropdown.contains(e.target)) {
              closeFilterDropdown(wrapper, filterBtn, dropdown);
            }
          });
        });

        /* Filter event listeners */
        filterInputs.forEach((filter) => {
          let type = filter.dataset.type;
          if (filter.type === "checkbox") {
            if (filter.checked) {
              listingResults.applyFilters(type, filter, false);
            }
          } else if (filter.type === "radio") {
            if (filter.checked) {
              listingResults.applyFilters(type, filter, false);
            }
          }
          filter.addEventListener("change", () => {
            listingResults.applyFilters(type, filter);
          });
        });
      }

      if (searchInput) {
        //set the initial state of the search filter
        listingResults.filters.search = searchInput.value;
        searchInput.addEventListener(
          "keyup",
          debounce(() => {
            listingResults.applyFilters("search", searchInput);
          }, 250),
        );
      }

      if (filterClearAll) {
        filterClearAll.addEventListener("click", () => {
          listingResults.clearAllFilters();
        });
      }

      if (filterEventDates) {
        //set up date pickers
        filterEventDates.forEach((input) => {
          let picker = datepicker(input, {
            id: "event-date-range",
            alwaysShow: false,
            customDays: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
            onSelect: (instance, date) => {
              listingResults.applyFilters("event_date", input);
            },
            minDate: new Date(),
          });

          //set the prefiltered date if applicable
          if (input.value) {
            picker.setDate(new Date(input.value), true);
          }

          //store date pickers for clearing filter dates
          datePickers.push(picker);
        });
      }

      if (activeFilters) {
        activeFilters.forEach((btn) => {
          listingResults.enableActiveFilterEvent(btn);
        });
      }

      //sets the initial browser state once all initial filters have been set-up
      if (!window.history.state) {
        console.log("Refreshing browser state");
        listingResults.refreshBrowserState();
      }
    }

    /* Pagination event listeners */
    if (loadMoreBtn) {
      loadMoreBtn.addEventListener("click", (e) => {
        e.preventDefault();
        listingResults.loadMoreResults();
      });
    }

    /* View toggle event listener */
    if (viewToggles) {
      viewToggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
          viewToggles.forEach((x) => {
            x.classList.remove("selected");
          });
          toggle.classList.add("selected");
          listingResults.switchViews(toggle.dataset.view);
        });
      });
    }

    function openFilterDropdown(wrapper, filterBtn, dropdown) {
      wrapper.classList.add("active");
      filterBtn.setAttribute("aria-expanded", true);
      // Focus the first input inside the dropdown
      const firstInput = dropdown.querySelector("input");
      if (firstInput) {
        firstInput.focus();
      } else {
        dropdown.focus();
      }
    }

    function closeFilterDropdown(wrapper, filterBtn, dropdown) {
      wrapper.classList.remove("active");
      filterBtn.setAttribute("aria-expanded", false);
    }

    function debounce(func, wait, immediate) {
      let timeout;
      return function () {
        let context = this,
          args = arguments;
        let later = function () {
          timeout = null;
          if (!immediate) func.apply(context, args);
        };
        let callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
      };
    }
  });
}
