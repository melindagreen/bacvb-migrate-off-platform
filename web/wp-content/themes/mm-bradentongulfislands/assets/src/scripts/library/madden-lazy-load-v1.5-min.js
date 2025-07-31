/**
 * Copyright 2022 Madden Media - All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Supports lazy loading of multimedia page content based on usage of data attributes.
 */
(function (root, factory) {
	if (typeof define === "function" && define.amd) {
		define([], factory(root));
	} else if (typeof exports === "object") {
		module.exports = factory(root);
	} else {
		root.MMLazyLoad = factory(root);
	}
})(
	typeof global !== "undefined" ? global : this.window || this.global,
	function (root) {
		"use strict";
		var IS_BOT =
			/bot|google|baidu|bing|msn|duckduckgo|teoma|slurp|yandex/i.test(
				navigator.userAgent
			);
		var smTestEl = null;
		var mdTestEl = null;
		var lgTestEl = null;
		var _isSmall = false;
		var _isMedium = false;
		var _isLarge = false;
		var MMLazyLoad = {};
		var supports = !!document.querySelector && !!window.IntersectionObserver;
		var settings = {};
		var windowHeight = window.innerHeight;
		var scrollTop = 0;
		var defaults = {
			loadElements: null,
			root: null,
			rootMargin: "500px",
			threshold: 0,
			debug: false,
		};
		var MMLazyLoadEls = [];
		MMLazyLoad.init = function (options) {
			if (!supports) {
				console.warn("LAZYLOAD: IntersectionObserver not supported!");
				return;
			}
			MMLazyLoad.destroy();
			settings = options;
			for (var opt in defaults) {
				if (typeof settings[opt] === "undefined") {
					settings[opt] = defaults[opt];
				}
			}
			smTestEl = document.getElementById("isSmall");
			mdTestEl = document.getElementById("isMedium");
			lgTestEl = document.getElementById("isLarge");
			getDocumentSize();
			window.addEventListener("resize", windowResizeListener, false);
			window.addEventListener("orientationchange", windowResizeListener, false);
			if (settings.loadElements == null) {
				console.warn(
					"LAZYLOAD: No elements provided - please do so during config with 'loadElements'. Exiting now."
				);
				return;
			}
			if (!smTestEl || !mdTestEl || !lgTestEl) {
				console.warn(
					"LAZYLOAD: Missing one or more size testers. Exiting now."
				);
				return;
			}
			for (var i = 0; i < settings.loadElements.length; i += 1) {
				if (
					IS_BOT ||
					settings.loadElements[i].getAttribute("data-load-onload")
				) {
					loadContent(settings.loadElements[i], settings.debug);
				} else {
					MMLazyLoadEls.push(settings.loadElements[i]);
				}
			}
			const MMObserver = new IntersectionObserver(
				(entries) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							loadContent(entry.target, settings.debug);
							MMObserver.unobserve(entry.target);
						}
					});
				},
				{
					root: settings.root,
					rootMargin: settings.rootMargin,
					threshold: settings.threshold,
				}
			);
			MMLazyLoadEls.forEach((el) => {
				if (!el.getAttribute("data-load-manual")) {
					MMObserver.observe(el);
				} else {
					if (settings.debug) {
						console.log("LAZYLOAD: Not observing manual element");
						console.log(el);
					}
				}
			});
			return MMLazyLoad;
		};
		MMLazyLoad.destroy = function () {
			if (!settings) {
				return;
			}
			settings = null;
		};
		MMLazyLoad.getDebugMode = function () {
			if (!settings) {
				return;
			}
			return settings.debug;
		};
		MMLazyLoad.setDebugMode = function (debug) {
			if (!settings) {
				return;
			}
			settings.debug = debug;
		};
		MMLazyLoad.manual = function (id) {
			for (let i = 0; i < MMLazyLoadEls.length; i += 1) {
				if (MMLazyLoadEls[i].getAttribute("id") === id) {
					loadContent(MMLazyLoadEls[i], settings.debug);
					MMLazyLoadEls.splice(i, 1);
				}
			}
		};
		function loadContent(el, debug) {
			var sizeData = false;
			if (el.getAttribute("data-load-all")) {
				sizeData = el.getAttribute("data-load-all");
			} else if (_isSmall) {
				sizeData = el.getAttribute("data-load-sm");
			} else if (_isMedium) {
				sizeData = el.getAttribute("data-load-md");
			} else {
				sizeData = el.getAttribute("data-load-lg");
			}
			if (sizeData) {
				if (el.getAttribute("data-load-type") == "img") {
					el.setAttribute("src", sizeData);
					if (el.getAttribute("data-load-alt")) {
						el.setAttribute("alt", el.getAttribute("data-load-alt"));
					}
					if (debug) {
						console.log("LAZYLOAD img: " + sizeData);
					}
				} else if (
					el.getAttribute("data-load-type") == "class" &&
					!el.classList.contains(sizeData)
				) {
					el.classList.add(sizeData);
					if (debug) {
						console.log("LAZYLOAD class: " + sizeData);
					}
				} else if (el.getAttribute("data-load-type") == "bg") {
					el.style.backgroundImage = "url('" + sizeData + "')";
					var bgPositionData = false;
					if (el.getAttribute("data-load-all-bg-position")) {
						bgPositionData = el.getAttribute("data-load-all-bg-position");
					} else if (_isSmall) {
						bgPositionData = el.getAttribute("data-load-sm-bg-position");
					} else if (_isMedium) {
						bgPositionData = el.getAttribute("data-load-md-bg-position");
					} else {
						bgPositionData = el.getAttribute("data-load-lg-bg-position");
					}
					if (bgPositionData) {
						el.style.backgroundPosition = bgPositionData;
					}
					if (debug) {
						console.log("LAZYLOAD bg: " + sizeData);
						console.log("LAZYLOAD bg position: " + bgPositionData);
					}
				} else if (el.getAttribute("data-load-type") == "poster") {
					el.setAttribute("poster", sizeData);
					if (debug) {
						console.log("LAZYLOAD poster: " + sizeData);
					}
				}
			} else {
				if (debug) {
					console.log("LAZYLOAD not loading: no size found for this view");
					console.log(el);
				}
			}
		}
		function getDocumentSize() {
			var sSm = window.getComputedStyle
				? getComputedStyle(smTestEl, null)
				: smTestEl.currentStyle;
			var sMd = window.getComputedStyle
				? getComputedStyle(mdTestEl, null)
				: mdTestEl.currentStyle;
			var sLg = window.getComputedStyle
				? getComputedStyle(lgTestEl, null)
				: lgTestEl.currentStyle;
			_isSmall =
				smTestEl && sSm.getPropertyValue("float") != "none" ? true : false;
			_isMedium =
				mdTestEl && sMd.getPropertyValue("float") != "none" ? true : false;
			_isLarge =
				lgTestEl && sLg.getPropertyValue("float") != "none" ? true : false;
			if (settings.debug) {
				console.log("_isSmall: " + _isSmall);
				console.log("_isMedium: " + _isMedium);
				console.log("_isLarge: " + _isLarge);
			}
		}
		function windowResizeListener(e) {
			getDocumentSize();
			windowHeight = Math.max(
				document.documentElement.clientHeight,
				window.innerHeight || 0
			);
			scrollTop = window.pageYOffset;
		}
		return MMLazyLoad;
	}
);

export default MMLazyLoad;
