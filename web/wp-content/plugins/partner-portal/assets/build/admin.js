/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/scripts/admin.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/scripts/admin.js":
/*!******************************!*\
  !*** ./src/scripts/admin.js ***!
  \******************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _styles_admin_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../styles/admin.scss */ "./src/styles/admin.scss");
/* harmony import */ var _styles_admin_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_styles_admin_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _inc_constants__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./inc/constants */ "./src/scripts/inc/constants.js");



(function ($) {
  /////// FUNCTIONS //////

  /**
  * Reset the settings form
  * @param {Event} e             The triggering event
   * @return {null}
  */
  function resetForm(e) {
    e.preventDefault();

    if (window.confirm('Are you sure? This will overwrite your current settings.')) {
      madden_plugin_options_defaults.forEach(function (optionGroup) {
        optionGroup.fields.forEach(function (field) {
          var fieldName = "".concat(_inc_constants__WEBPACK_IMPORTED_MODULE_1__["PLUGIN_SETTNGS_SLUG"], "[").concat(optionGroup.id, "][").concat(field.id, "]");
          $("[name=\"".concat(fieldName, "\"]")).val(field.args.default);
        });
      });
      $("#submit").click();
    }
  }

  $(document).ready(function () {
    // Reset defaults
    $("#".concat(_inc_constants__WEBPACK_IMPORTED_MODULE_1__["PLUGIN_PREFIX"], "-reset")).click(resetForm);
  });
})(jQuery);

/***/ }),

/***/ "./src/scripts/inc/constants.js":
/*!**************************************!*\
  !*** ./src/scripts/inc/constants.js ***!
  \**************************************/
/*! exports provided: PLUGIN_PREFIX, PLUGIN_SETTNGS_SLUG */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PLUGIN_PREFIX", function() { return PLUGIN_PREFIX; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PLUGIN_SETTNGS_SLUG", function() { return PLUGIN_SETTNGS_SLUG; });
var PLUGIN_PREFIX = 'madden-plugin';
var PLUGIN_SETTNGS_SLUG = 'plugin:madden_plugin';

/***/ }),

/***/ "./src/styles/admin.scss":
/*!*******************************!*\
  !*** ./src/styles/admin.scss ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ })

/******/ });
//# sourceMappingURL=admin.js.map