/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./controls/index.js":
/*!***************************!*\
  !*** ./controls/index.js ***!
  \***************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _inspector__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./inspector */ "./controls/inspector.js");
/* harmony import */ var _toolbar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./toolbar */ "./controls/toolbar.js");

// WordPress dependencies



// Local Dependencies
// Inspector - used for controls in inspector


// Toolbar - used for controls in toolbar


/*** CONSTANTS **************************************************************/

const Controls = props => {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.BlockControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_toolbar__WEBPACK_IMPORTED_MODULE_3__["default"], {
    ...props
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_inspector__WEBPACK_IMPORTED_MODULE_2__["default"], {
    ...props
  }));
};

/*** EXPORTS ****************************************************************/

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Controls);

/***/ }),

/***/ "./controls/inspector.js":
/*!*******************************!*\
  !*** ./controls/inspector.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wizard__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./wizard */ "./controls/wizard.js");

/*** IMPORTS ****************************************************************/

// WordPress dependencies




// Local Dependencies
// Controls - add block/inspector controls here


/*** CONSTANTS **************************************************************/

/*** COMPONENTS **************************************************************/

const Inspector = props => {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wizard__WEBPACK_IMPORTED_MODULE_4__["default"], {
    ...props
  }));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Inspector);

/***/ }),

/***/ "./controls/token-multiselect-control/index.js":
/*!*****************************************************!*\
  !*** ./controls/token-multiselect-control/index.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/keycodes */ "@wordpress/keycodes");
/* harmony import */ var _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/is-shallow-equal */ "@wordpress/is-shallow-equal");
/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _token__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./token */ "./controls/token-multiselect-control/token.js");
/* harmony import */ var _token_input__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./token-input */ "./controls/token-multiselect-control/token-input.js");
/* harmony import */ var _suggestions_list__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./suggestions-list */ "./controls/token-multiselect-control/suggestions-list.js");

/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



const initialState = {
  incompleteTokenValue: '',
  inputOffsetFromEnd: 0,
  isActive: false,
  isExpanded: false,
  selectedSuggestionIndex: -1,
  selectedSuggestionScroll: false
};
class TokenMultiSelectControl extends _wordpress_element__WEBPACK_IMPORTED_MODULE_4__.Component {
  constructor() {
    super(...arguments);
    this.state = initialState;
    this.onKeyDown = this.onKeyDown.bind(this);
    this.onKeyPress = this.onKeyPress.bind(this);
    this.onFocus = this.onFocus.bind(this);
    this.onClick = this.onClick.bind(this);
    this.onBlur = this.onBlur.bind(this);
    this.deleteTokenBeforeInput = this.deleteTokenBeforeInput.bind(this);
    this.deleteTokenAfterInput = this.deleteTokenAfterInput.bind(this);
    this.addCurrentToken = this.addCurrentToken.bind(this);
    this.onContainerTouched = this.onContainerTouched.bind(this);
    this.renderToken = this.renderToken.bind(this);
    this.onTokenClickRemove = this.onTokenClickRemove.bind(this);
    this.onSuggestionHovered = this.onSuggestionHovered.bind(this);
    this.onSuggestionSelected = this.onSuggestionSelected.bind(this);
    this.onInputChange = this.onInputChange.bind(this);
    this.bindInput = this.bindInput.bind(this);
    this.bindTokensAndInput = this.bindTokensAndInput.bind(this);
    this.updateSuggestions = this.updateSuggestions.bind(this);
    this.addNewTokens = this.addNewTokens.bind(this);
    this.getValueFromLabel = this.getValueFromLabel.bind(this);
    this.getLabelFromValue = this.getLabelFromValue.bind(this);
  }
  componentDidUpdate(prevProps) {
    // Make sure to focus the input when the isActive state is true.
    if (this.state.isActive && !this.input.hasFocus()) {
      this.input.focus();
    }
    const {
      options,
      value
    } = this.props;
    const suggestionsDidUpdate = !_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_7___default()(options, prevProps.options);
    if (suggestionsDidUpdate || value !== prevProps.value) {
      this.updateSuggestions(suggestionsDidUpdate);
    }
  }
  static getDerivedStateFromProps(props, state) {
    if (!props.disabled || !state.isActive) {
      return null;
    }
    return {
      isActive: false,
      incompleteTokenValue: ''
    };
  }
  bindInput(ref) {
    this.input = ref;
  }
  bindTokensAndInput(ref) {
    this.tokensAndInput = ref;
  }
  onFocus(event) {
    // If focus is on the input or on the container, set the isActive state to true.
    if (this.input.hasFocus() || event.target === this.tokensAndInput) {
      this.setState({
        isActive: true /* , isExpanded: true */
      });
    } else {
      /*
       * Otherwise, focus is on one of the token "remove" buttons and we
       * set the isActive state to false to prevent the input to be
       * re-focused, see componentDidUpdate().
       */
      this.setState({
        isActive: false
      });
    }
    if ('function' === typeof this.props.onFocus) {
      this.props.onFocus(event);
    }
  }
  onClick(event) {
    // If focus is on the input or on the container, set the isActive state to true.
    // don't open if we clicked a suggestion
    if (!event.target.classList.contains('components-form-token-field__suggestion')) {
      this.setState({
        isExpanded: true,
        isActive: true
      });
    }
  }
  onBlur() {
    if (this.inputHasValidToken()) {
      this.setState({
        isActive: false,
        isExpanded: false
      });
    } else {
      this.setState(initialState);
    }
  }
  onKeyDown(event) {
    let preventDefault = false;
    switch (event.keyCode) {
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.BACKSPACE:
        preventDefault = this.handleDeleteKey(this.deleteTokenBeforeInput);
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.ENTER:
        preventDefault = this.addCurrentToken();
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.LEFT:
        preventDefault = this.handleLeftArrowKey();
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.UP:
        preventDefault = this.handleUpArrowKey();
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.RIGHT:
        preventDefault = this.handleRightArrowKey();
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.DOWN:
        preventDefault = this.handleDownArrowKey();
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.DELETE:
        preventDefault = this.handleDeleteKey(this.deleteTokenAfterInput);
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.SPACE:
        if (this.props.tokenizeOnSpace) {
          preventDefault = this.addCurrentToken();
        }
        break;
      case _wordpress_keycodes__WEBPACK_IMPORTED_MODULE_6__.ESCAPE:
        preventDefault = this.handleEscapeKey(event);
        event.stopPropagation();
        break;
      default:
        break;
    }
    if (preventDefault) {
      event.preventDefault();
    }
  }
  onKeyPress(event) {
    if (!this.state.isExpanded) {
      this.setState({
        isExpanded: true
      });
    }
  }
  onContainerTouched(event) {
    // Prevent clicking/touching the tokensAndInput container from blurring
    // the input and adding the current token.
    if (event.target === this.tokensAndInput && this.state.isActive) {
      event.preventDefault();
    }
    //this.setState( { isExpanded: true } );
  }
  onTokenClickRemove(event) {
    this.deleteToken(event.value);
    this.input.focus();
  }
  onSuggestionHovered(suggestion) {
    const index = this.getMatchingSuggestions().indexOf(suggestion);
    if (index >= 0) {
      this.setState({
        selectedSuggestionIndex: index,
        selectedSuggestionScroll: false
      });
    }
  }
  onSuggestionSelected(suggestion) {
    this.addNewToken(suggestion);
  }
  onInputChange(event) {
    const tokenValue = event.value;
    this.setState({
      incompleteTokenValue: tokenValue
    }, this.updateSuggestions);
    this.props.onInputChange(tokenValue);
  }
  handleDeleteKey(deleteToken) {
    let preventDefault = false;
    if (this.input.hasFocus() && this.isInputEmpty()) {
      deleteToken();
      preventDefault = true;
    }
    return preventDefault;
  }
  handleLeftArrowKey() {
    let preventDefault = false;
    if (this.isInputEmpty()) {
      this.moveInputBeforePreviousToken();
      preventDefault = true;
    }
    return preventDefault;
  }
  handleRightArrowKey() {
    let preventDefault = false;
    if (this.isInputEmpty()) {
      this.moveInputAfterNextToken();
      preventDefault = true;
    }
    return preventDefault;
  }
  getOptionsLabels(options) {
    return options.map(option => {
      return option.label;
    });
  }
  getValueFromLabel(optionLabel) {
    const foundOption = this.props.options.find(option => option.label.toLocaleLowerCase() === optionLabel.toLocaleLowerCase());
    if (foundOption) {
      return foundOption.value;
    }
    return null;
  }
  getLabelFromValue(optionValue) {
    const foundOption = this.props.options.find(option => option.value === optionValue);
    if (foundOption) {
      return foundOption.label;
    }
    return null;
  }
  getOptionsValues(options) {
    return options.map(option => {
      return option.value;
    });
  }
  handleUpArrowKey() {
    this.setState((state, props) => ({
      selectedSuggestionIndex: (state.selectedSuggestionIndex === 0 ? this.getMatchingSuggestions(state.incompleteTokenValue, this.getOptionsLabels(props.options), props.value, props.maxSuggestions, props.saveTransform).length : state.selectedSuggestionIndex) - 1,
      selectedSuggestionScroll: true
    }));
    return true; // preventDefault
  }
  handleDownArrowKey() {
    this.setState((state, props) => ({
      selectedSuggestionIndex: (state.selectedSuggestionIndex + 1) % this.getMatchingSuggestions(state.incompleteTokenValue, this.getOptionsLabels(props.options), props.value, props.maxSuggestions, props.saveTransform).length,
      selectedSuggestionScroll: true,
      isExpanded: true
    }));
    return true; // preventDefault
  }
  handleEscapeKey(event) {
    this.setState({
      incompleteTokenValue: event.target.value,
      isExpanded: false,
      selectedSuggestionIndex: -1,
      selectedSuggestionScroll: false
    });
    return true; // preventDefault
  }
  moveInputToIndex(index) {
    this.setState((state, props) => ({
      inputOffsetFromEnd: props.value.length - Math.max(index, -1) - 1
    }));
  }
  moveInputBeforePreviousToken() {
    this.setState((state, props) => ({
      inputOffsetFromEnd: Math.min(state.inputOffsetFromEnd + 1, props.value.length)
    }));
  }
  moveInputAfterNextToken() {
    this.setState(state => ({
      inputOffsetFromEnd: Math.max(state.inputOffsetFromEnd - 1, 0)
    }));
  }
  deleteTokenBeforeInput() {
    const index = this.getIndexOfInput() - 1;
    if (index > -1) {
      this.deleteToken(this.props.value[index]);
    }
  }
  deleteTokenAfterInput() {
    const index = this.getIndexOfInput();
    if (index < this.props.value.length) {
      this.deleteToken(this.props.value[index]);
      // update input offset since it's the offset from the last token
      this.moveInputToIndex(index);
    }
  }
  addCurrentToken() {
    let preventDefault = false;
    const selectedSuggestion = this.getSelectedSuggestion();
    if (selectedSuggestion) {
      this.addNewToken(selectedSuggestion);
      preventDefault = true;
    } else if (this.inputHasValidToken()) {
      this.addNewToken(this.state.incompleteTokenValue);
      preventDefault = true;
    }
    return preventDefault;
  }
  addNewTokens(tokens) {
    const tokensToAdd = (0,lodash__WEBPACK_IMPORTED_MODULE_1__.uniq)(tokens.map(this.props.saveTransform).filter(Boolean).filter(token => !this.valueContainsToken(token)));
    if (tokensToAdd.length > 0) {
      const tokenValuesToAdd = tokensToAdd.map(tokenLabel => {
        return this.getValueFromLabel(tokenLabel);
      });
      let newValue = (0,lodash__WEBPACK_IMPORTED_MODULE_1__.clone)(this.props.value);
      newValue.splice.apply(newValue, [this.getIndexOfInput(), 0].concat(tokenValuesToAdd));
      // now remove duplicates if required
      newValue = [...new Set(newValue)];
      this.props.onChange(newValue);
    }
  }
  addNewToken(token) {
    this.addNewTokens([token]);
    this.props.speak(this.props.messages.added, 'assertive');
    this.setState({
      incompleteTokenValue: '',
      selectedSuggestionIndex: -1,
      selectedSuggestionScroll: false,
      isExpanded: false
    });
    if (this.state.isActive) {
      this.input.focus();
    }
  }
  deleteToken(token) {
    const newTokens = this.props.value.filter(item => {
      return this.getTokenValue(item) !== this.getTokenValue(token);
    });
    this.props.onChange(newTokens);
    this.props.speak(this.props.messages.removed, 'assertive');
  }
  getTokenValue(token) {
    if (token && token.value) {
      return token.value;
    }
    return token;
  }
  getMatchingSuggestions(searchValue = this.state.incompleteTokenValue, suggestions = this.getOptionsLabels(this.props.options), value = this.props.value, maxSuggestions = this.props.maxSuggestions, saveTransform = this.props.saveTransform) {
    let match = saveTransform(searchValue);
    const startsWithMatch = [];
    const containsMatch = [];
    const activeLabels = value.map(optionValue => {
      return this.getLabelFromValue(optionValue);
    });
    if (match.length > 0) {
      match = match.toLocaleLowerCase();
      (0,lodash__WEBPACK_IMPORTED_MODULE_1__.each)(suggestions, suggestion => {
        const index = suggestion.toLocaleLowerCase().indexOf(match);
        if (value.indexOf(suggestion) === -1) {
          if (index === 0) {
            startsWithMatch.push(suggestion);
          } else if (index > 0) {
            containsMatch.push(suggestion);
          }
        }
      });
      suggestions = startsWithMatch.concat(containsMatch);
    }
    // remove selected labels from suggestions
    suggestions = (0,lodash__WEBPACK_IMPORTED_MODULE_1__.difference)(suggestions, activeLabels);
    return (0,lodash__WEBPACK_IMPORTED_MODULE_1__.take)(suggestions, maxSuggestions);
  }
  getSelectedSuggestion() {
    if (this.state.selectedSuggestionIndex !== -1) {
      return this.getMatchingSuggestions()[this.state.selectedSuggestionIndex];
    }
  }
  valueContainsToken(token) {
    return (0,lodash__WEBPACK_IMPORTED_MODULE_1__.some)(this.props.value, item => {
      return this.getTokenValue(token) === this.getTokenValue(item);
    });
  }
  getIndexOfInput() {
    return this.props.value.length - this.state.inputOffsetFromEnd;
  }
  isInputEmpty() {
    return this.state.incompleteTokenValue.length === 0;
  }
  inputHasValidToken() {
    const incompleteTokenValue = this.state.incompleteTokenValue;
    let foundMatch = false;
    if (incompleteTokenValue && incompleteTokenValue.length > 0) {
      this.props.options.forEach(option => {
        if (option.label.trim().toLocaleLowerCase() === incompleteTokenValue.trim().toLocaleLowerCase()) {
          foundMatch = true;
          // return true; //not working?
        }
      });
    }
    return foundMatch;
  }
  updateSuggestions(resetSelectedSuggestion = true) {
    const {
      incompleteTokenValue
    } = this.state;
    const inputHasMinimumChars = true; //incompleteTokenValue.trim().length > 1;
    const matchingSuggestions = this.getMatchingSuggestions(incompleteTokenValue);
    const hasMatchingSuggestions = matchingSuggestions.length > 0;
    const newState = {
      // isExpanded: inputHasMinimumChars && hasMatchingSuggestions,
    };
    if (resetSelectedSuggestion) {
      newState.selectedSuggestionIndex = -1;
      newState.selectedSuggestionScroll = false;
    }
    this.setState(newState);
    if (inputHasMinimumChars) {
      const {
        debouncedSpeak
      } = this.props;
      const message = hasMatchingSuggestions ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.sprintf)(/* translators: %d: number of results. */
      (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__._n)('%d result found, use up and down arrow keys to navigate.', '%d results found, use up and down arrow keys to navigate.', matchingSuggestions.length), matchingSuggestions.length) : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('No results.');
      debouncedSpeak(message, 'assertive');
    }
  }
  renderTokensAndInput() {
    const components = (0,lodash__WEBPACK_IMPORTED_MODULE_1__.map)(this.props.value, this.renderToken);
    components.splice(this.getIndexOfInput(), 0, this.renderInput());
    return components;
  }
  renderToken(token, index, tokens) {
    const value = this.getTokenValue(token);
    const label = this.getLabelFromValue(value); //todo - optimize
    const status = token.status ? token.status : undefined;
    const termPosition = index + 1;
    const termsCount = tokens.length;
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_token__WEBPACK_IMPORTED_MODULE_9__["default"], {
      key: 'token-' + value,
      value: value,
      label: label,
      status: status,
      title: token.title,
      displayTransform: this.props.displayTransform,
      onClickRemove: this.onTokenClickRemove,
      isBorderless: token.isBorderless || this.props.isBorderless,
      onMouseEnter: token.onMouseEnter,
      onMouseLeave: token.onMouseLeave,
      disabled: 'error' !== status && this.props.disabled,
      messages: this.props.messages,
      termsCount: termsCount,
      termPosition: termPosition
    });
  }
  renderInput() {
    const {
      autoCapitalize,
      autoComplete,
      maxLength,
      value,
      instanceId
    } = this.props;
    let props = {
      instanceId,
      autoCapitalize,
      autoComplete,
      ref: this.bindInput,
      key: 'input',
      disabled: this.props.disabled,
      value: this.state.incompleteTokenValue,
      onBlur: this.onBlur,
      isExpanded: this.state.isExpanded,
      selectedSuggestionIndex: this.state.selectedSuggestionIndex
    };
    if (!(maxLength && value.length >= maxLength)) {
      props = {
        ...props,
        onChange: this.onInputChange
      };
    }
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_token_input__WEBPACK_IMPORTED_MODULE_10__["default"], {
      ...props
    });
  }
  render() {
    const {
      disabled,
      label = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Add item'),
      instanceId,
      className
    } = this.props;
    const {
      isExpanded
    } = this.state;
    const classes = classnames__WEBPACK_IMPORTED_MODULE_2___default()(className, 'components-form-token-field__input-container', {
      'is-active': this.state.isActive,
      'is-disabled': disabled
    });
    let tokenFieldProps = {
      className: 'components-form-token-field',
      tabIndex: '-1'
    };
    const matchingSuggestions = this.getMatchingSuggestions();
    if (!disabled) {
      tokenFieldProps = Object.assign({}, tokenFieldProps, {
        onKeyDown: this.onKeyDown,
        onKeyPress: this.onKeyPress,
        onFocus: this.onFocus,
        onClick: this.onClick
      });
    }

    // Disable reason: There is no appropriate role which describes the
    // input container intended accessible usability.
    // TODO: Refactor click detection to use blur to stop propagation.
    /* eslint-disable jsx-a11y/no-static-element-interactions */
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      ...tokenFieldProps
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      htmlFor: `components-form-token-input-${instanceId}`,
      className: "components-form-token-field__label"
    }, label), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      ref: this.bindTokensAndInput,
      className: classes,
      tabIndex: "-1",
      onMouseDown: this.onContainerTouched,
      onTouchStart: this.onContainerTouched
    }, this.renderTokensAndInput(), isExpanded && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_suggestions_list__WEBPACK_IMPORTED_MODULE_11__["default"], {
      instanceId: instanceId,
      match: this.props.saveTransform(this.state.incompleteTokenValue),
      displayTransform: this.props.displayTransform,
      suggestions: matchingSuggestions,
      selectedIndex: this.state.selectedSuggestionIndex,
      scrollIntoView: this.state.selectedSuggestionScroll,
      onHover: this.onSuggestionHovered,
      onSelect: this.onSuggestionSelected
    })));
    /* eslint-enable jsx-a11y/no-static-element-interactions */
  }
}
TokenMultiSelectControl.defaultProps = {
  options: Object.freeze([]),
  maxSuggestions: 100,
  value: Object.freeze([]),
  displayTransform: lodash__WEBPACK_IMPORTED_MODULE_1__.identity,
  saveTransform: token => token ? token.trim() : '',
  onChange: () => {},
  onInputChange: () => {},
  isBorderless: false,
  disabled: false,
  tokenizeOnSpace: false,
  messages: {
    added: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Item added.'),
    removed: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Item removed.'),
    remove: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Remove item')
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((0,_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.withSpokenMessages)((0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__.withInstanceId)(TokenMultiSelectControl)));

/***/ }),

/***/ "./controls/token-multiselect-control/suggestions-list.js":
/*!****************************************************************!*\
  !*** ./controls/token-multiselect-control/suggestions-list.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var dom_scroll_into_view__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! dom-scroll-into-view */ "./node_modules/dom-scroll-into-view/dist-web/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);

/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */


class SuggestionsList extends _wordpress_element__WEBPACK_IMPORTED_MODULE_3__.Component {
  constructor() {
    super(...arguments);
    this.handleMouseDown = this.handleMouseDown.bind(this);
    this.bindList = this.bindList.bind(this);
  }
  componentDidUpdate() {
    // only have to worry about scrolling selected suggestion into view
    // when already expanded
    if (this.props.selectedIndex > -1 && this.props.scrollIntoView) {
      this.scrollingIntoView = true;
      (0,dom_scroll_into_view__WEBPACK_IMPORTED_MODULE_5__["default"])(this.list.children[this.props.selectedIndex], this.list, {
        onlyScrollIfNeeded: true
      });
      this.props.setTimeout(() => {
        this.scrollingIntoView = false;
      }, 100);
    }
  }
  bindList(ref) {
    this.list = ref;
  }
  handleHover(suggestion) {
    return () => {
      if (!this.scrollingIntoView) {
        this.props.onHover(suggestion);
      }
    };
  }
  handleClick(suggestion) {
    return () => {
      this.props.onSelect(suggestion);
    };
  }
  handleMouseDown(e) {
    // By preventing default here, we will not lose focus of <input> when clicking a suggestion
    e.preventDefault();
  }
  computeSuggestionMatch(suggestion) {
    const match = this.props.displayTransform(this.props.match || '').toLocaleLowerCase();
    if (match.length === 0) {
      return null;
    }
    suggestion = this.props.displayTransform(suggestion);
    const indexOfMatch = suggestion.toLocaleLowerCase().indexOf(match);
    return {
      suggestionBeforeMatch: suggestion.substring(0, indexOfMatch),
      suggestionMatch: suggestion.substring(indexOfMatch, indexOfMatch + match.length),
      suggestionAfterMatch: suggestion.substring(indexOfMatch + match.length)
    };
  }
  render() {
    // We set `tabIndex` here because otherwise Firefox sets focus on this
    // div when tabbing off of the input in `TokenField` -- not really sure
    // why, since usually a div isn't focusable by default
    // TODO does this still apply now that it's a <ul> and not a <div>?
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
      ref: this.bindList,
      className: "components-form-token-field__suggestions-list",
      id: `components-form-token-suggestions-${this.props.instanceId}`,
      role: "listbox"
    }, (0,lodash__WEBPACK_IMPORTED_MODULE_1__.map)(this.props.suggestions, (suggestion, index) => {
      const match = this.computeSuggestionMatch(suggestion);
      const classeName = classnames__WEBPACK_IMPORTED_MODULE_2___default()('components-form-token-field__suggestion', {
        'is-selected': index === this.props.selectedIndex
      });

      /* eslint-disable jsx-a11y/click-events-have-key-events */
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
        id: `components-form-token-suggestions-${this.props.instanceId}-${index}`,
        role: "option",
        className: classeName,
        key: this.props.displayTransform(suggestion),
        onMouseDown: this.handleMouseDown,
        onClick: this.handleClick(suggestion),
        onMouseEnter: this.handleHover(suggestion),
        "aria-selected": index === this.props.selectedIndex
      }, match ? (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
        "aria-label": this.props.displayTransform(suggestion)
      }, match.suggestionBeforeMatch, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", {
        className: "components-form-token-field__suggestion-match"
      }, match.suggestionMatch), match.suggestionAfterMatch) : this.props.displayTransform(suggestion));
      /* eslint-enable jsx-a11y/click-events-have-key-events */
    }));
  }
}
SuggestionsList.defaultProps = {
  match: '',
  onHover: () => {},
  onSelect: () => {},
  suggestions: Object.freeze([])
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__.withSafeTimeout)(SuggestionsList));

/***/ }),

/***/ "./controls/token-multiselect-control/token-input.js":
/*!***********************************************************!*\
  !*** ./controls/token-multiselect-control/token-input.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */

class TokenInput extends _wordpress_element__WEBPACK_IMPORTED_MODULE_2__.Component {
  constructor() {
    super(...arguments);
    this.onChange = this.onChange.bind(this);
    this.bindInput = this.bindInput.bind(this);
  }
  focus() {
    this.input.focus();
  }
  hasFocus() {
    return this.input === this.input.ownerDocument.activeElement;
  }
  bindInput(ref) {
    this.input = ref;
  }
  onChange(event) {
    this.props.onChange({
      value: event.target.value
    });
  }
  render() {
    const {
      value,
      isExpanded,
      instanceId,
      selectedSuggestionIndex,
      className,
      ...props
    } = this.props;
    const size = value ? value.length + 1 : 0;
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
      ref: this.bindInput,
      id: `components-form-token-input-${instanceId}`,
      type: "text",
      ...props,
      value: value || '',
      onChange: this.onChange,
      size: size,
      className: classnames__WEBPACK_IMPORTED_MODULE_1___default()(className, 'components-form-token-field__input'),
      autoComplete: "off",
      role: "combobox",
      "aria-expanded": isExpanded,
      "aria-autocomplete": "list",
      "aria-owns": isExpanded ? `components-form-token-suggestions-${instanceId}` : undefined,
      "aria-activedescendant": selectedSuggestionIndex !== -1 ? `components-form-token-suggestions-${instanceId}-${selectedSuggestionIndex}` : undefined,
      "aria-describedby": `components-form-token-suggestions-howto-${instanceId}`
    });
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (TokenInput);

/***/ }),

/***/ "./controls/token-multiselect-control/token.js":
/*!*****************************************************!*\
  !*** ./controls/token-multiselect-control/token.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Token)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


//import { closeSmall } from '@wordpress/icons';

/**
 * Internal dependencies
 */

function Token({
  value,
  label,
  status,
  title,
  displayTransform,
  isBorderless = false,
  disabled = false,
  onClickRemove = lodash__WEBPACK_IMPORTED_MODULE_2__.noop,
  onMouseEnter,
  onMouseLeave,
  messages,
  termPosition,
  termsCount
}) {
  const instanceId = (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__.useInstanceId)(Token);
  const tokenClasses = classnames__WEBPACK_IMPORTED_MODULE_1___default()('components-form-token-field__token', {
    'is-error': 'error' === status,
    'is-success': 'success' === status,
    'is-validating': 'validating' === status,
    'is-borderless': isBorderless,
    'is-disabled': disabled
  });
  const onClick = () => onClickRemove({
    value
  });
  const transformedValue = displayTransform(label);
  const termPositionAndCount = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.sprintf)(/* translators: 1: term name, 2: term position in a set of terms, 3: total term set count. */
  (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('%1$s (%2$s of %3$s)'), transformedValue, termPosition, termsCount);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: tokenClasses,
    onMouseEnter: onMouseEnter,
    onMouseLeave: onMouseLeave,
    title: title
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "components-form-token-field__token-text",
    id: `components-form-token-field__token-text-${instanceId}`
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.VisuallyHidden, {
    as: "span"
  }, termPositionAndCount), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    "aria-hidden": "true"
  }, transformedValue)), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.Button, {
    className: "components-form-token-field__remove-token",
    onClick: !disabled && onClick,
    label: messages.remove,
    "aria-describedby": `components-form-token-field__token-text-${instanceId}`
  }));
}

/***/ }),

/***/ "./controls/toolbar.js":
/*!*****************************!*\
  !*** ./controls/toolbar.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);

/*** IMPORTS ****************************************************************/

// WordPress Dependencies



// Local dependencies
const THEME_PREFIX = "eventastic_calendar";

/*** COMPONENTS **************************************************************/

const TogglePreview = props => {
  const {
    attributes,
    setAttributes
  } = props;
  const isEditing = attributes.mode === 'edit';
  const onClick = () => {
    setAttributes({
      mode: isEditing ? 'preview' : 'edit'
    });
  };
  const PreviewButton = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Preview', THEME_PREFIX),
    icon: "visibility",
    onClick: onClick
  });
  const EditButton = (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Edit', THEME_PREFIX),
    icon: "edit",
    onClick: onClick
  });
  return isEditing ? PreviewButton : EditButton;
};
const Tools = props => {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Toolbar, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(TogglePreview, {
    ...props
  }));
};

/*** EXPORTS ****************************************************************/

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Tools);

/***/ }),

/***/ "./controls/wizard.js":
/*!****************************!*\
  !*** ./controls/wizard.js ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/date */ "@wordpress/date");
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_date__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/core-data */ "@wordpress/core-data");
/* harmony import */ var _wordpress_core_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_core_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _token_multiselect_control__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./token-multiselect-control */ "./controls/token-multiselect-control/index.js");

/*** IMPORTS ****************************************************************/
// WordPress dependencies









/*** CONSTANTS **************************************************************/
/*** FUNCTIONS **************************************************************/

const Wizard = props => {
  const {
    attributes,
    setAttributes,
    terms
  } = props;
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: `content-selector eventastic-calendar-block ${props.className}`
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Content"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Limit the Categories to Show?"),
    checked: attributes.contentConfig_useAllCategories,
    onChange: () => {
      setAttributes({
        contentConfig_useAllCategories: !attributes.contentConfig_useAllCategories
      });
    }
  })), attributes.contentConfig_useAllCategories && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_token_multiselect_control__WEBPACK_IMPORTED_MODULE_7__["default"], {
    value: attributes.contentConfig_categories,
    label: "Only Show the Following Categories",
    options: [
    // Map terms to option objects
    ...terms.map(term => {
      return {
        value: term.id,
        label: term.name.replace('&amp;', '&')
      };
    })],
    onChange: value => setAttributes({
      contentConfig_categories: value
    })
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Message to show when no events found"),
    value: attributes.contentConfig_failureMessage,
    onChange: val => {
      setAttributes({
        contentConfig_failureMessage: val
      });
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Layout"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Cards Per Row "),
    value: attributes.listConfig_cardsPerRow,
    options: [{
      label: "1",
      value: "1"
    }, {
      label: "2",
      value: "2"
    }, {
      label: "3",
      value: "3"
    }, {
      label: "4",
      value: "4"
    }],
    onChange: val => {
      setAttributes({
        listConfig_cardsPerRow: val
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalNumberControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Max # Events to Show in Grid"),
    value: attributes.maxNumberOfGridEventsToShow,
    onChange: val => {
      setAttributes({
        maxNumberOfGridEventsToShow: val
      });
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Style"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Stylesheet Options"),
    value: attributes.styleSheet,
    options: [{
      label: "Default",
      value: "default"
    }, {
      label: "Option One",
      value: "style-one"
    }, {
      label: "None",
      value: "none"
    }],
    onChange: val => {
      setAttributes({
        styleSheet: val
      });
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Filter & Search"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Use Filters?"),
    checked: attributes.useFilters,
    onChange: () => {
      setAttributes({
        useFilters: !attributes.useFilters
      });
    }
  })), attributes.useFilters && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Filters Location"),
    value: attributes.filterLocation,
    options: [{
      label: "Left Sidebar",
      value: "sidebar"
    }, {
      label: "Above",
      value: "above"
    }],
    onChange: val => {
      setAttributes({
        filterLocation: val
      });
    }
  })), attributes.useFilters && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Use Date Filters?"),
    checked: attributes.filterConfig_useDates,
    onChange: () => {
      setAttributes({
        filterConfig_useDates: !attributes.filterConfig_useDates
      });
    }
  })), attributes.filterConfig_useDates && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("End Date Default For Queries?"),
    value: attributes.filterConfig_endDateDefault,
    options: [{
      label: "Same as Start Date",
      value: "start_date"
    }, {
      label: "Through Current Month",
      value: "one_month"
    }, {
      label: "Through the Next Month",
      value: "two_month"
    }, {
      label: "Through Two More Months",
      value: "three_month"
    }],
    onChange: val => {
      setAttributes({
        filterConfig_endDateDefault: val
      });
    }
  })), attributes.filterConfig_useDates && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Prepopulate End Date Input?"),
    checked: attributes.filterConfig_fillEndDateInput,
    onChange: () => {
      setAttributes({
        filterConfig_fillEndDateInput: !attributes.filterConfig_fillEndDateInput
      });
    }
  })), attributes.useFilters && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Use Keyword Filter?"),
    checked: attributes.filterConfig_useKeyword,
    onChange: () => {
      setAttributes({
        filterConfig_useKeyword: !attributes.filterConfig_useKeyword
      });
    }
  })), attributes.useFilters && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Use Category Filter?"),
    checked: attributes.filterConfig_useCategories,
    onChange: () => {
      setAttributes({
        filterConfig_useCategories: !attributes.filterConfig_useCategories
      });
    }
  })), attributes.useFilters && attributes.filterConfig_useCategories && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Category Filter Type"),
    value: attributes.filterConfig_categoryElementType,
    options: [{
      label: "Buttons",
      value: "buttons"
    }, {
      label: "Dropdown Select",
      value: "select"
    }, {
      label: "Checkboxes",
      value: "checkboxes"
    }],
    onChange: val => {
      setAttributes({
        filterConfig_categoryElementType: val
      });
    }
  })), attributes.useFilters && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Use Filter Reset?"),
    checked: attributes.filterConfig_useFilterReset,
    onChange: () => {
      setAttributes({
        filterConfig_useFilterReset: !attributes.filterConfig_useFilterReset
      });
    }
  })), attributes.useFilters && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Use Search Submit?"),
    checked: attributes.filterConfig_useFilterSubmit,
    onChange: () => {
      setAttributes({
        filterConfig_useFilterSubmit: !attributes.filterConfig_useFilterSubmit
      });
    }
  }))), attributes.layoutStyle !== "list" && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Calendar Configurations"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("How to Display Events on Calendar"),
    value: attributes.calendarConfig_eventRenderType,
    options: [{
      label: "Empty Circle",
      value: "emptyCircle"
    }, {
      label: "Circle with Number of Events",
      value: "numberedCircle"
    }, {
      label: "List of events",
      value: "list"
    }],
    onChange: val => {
      setAttributes({
        calendarConfig_eventRenderType: val
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Calendar Header - Left"),
    value: attributes.calendarConfig_headerToolbarStart,
    onChange: val => {
      setAttributes({
        calendarConfig_headerToolbarStart: val
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Calendar Header - Center"),
    value: attributes.calendarConfig_headerToolbarCenter,
    onChange: val => {
      setAttributes({
        calendarConfig_headerToolbarCenter: val
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Calendar Header - Right"),
    value: attributes.calendarConfig_headerToolbarEnd,
    onChange: val => {
      setAttributes({
        calendarConfig_headerToolbarEnd: val
      });
    }
  }))), attributes.layoutStyle !== "calendar" && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "List/Grid Configurations"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show List Title?"),
    checked: attributes.listConfig_showTitle,
    onChange: () => {
      setAttributes({
        listConfig_showTitle: !attributes.listConfig_showTitle
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("How to Display Recurring Events in List"),
    value: attributes.listConfig_recurringDateHandler,
    options: [{
      label: "Show Event Card Once",
      value: "showOnce"
    }, {
      label: "Show Event Card for Each Date",
      value: "showEachDate"
    }],
    onChange: val => {
      setAttributes({
        listConfig_recurringDateHandler: val
      });
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Event Card Content"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show Categories?"),
    checked: attributes.cardConfig_showCategories,
    onChange: () => {
      setAttributes({
        cardConfig_showCategories: !attributes.cardConfig_showCategories
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show Address?"),
    checked: attributes.cardConfig_showAddress,
    onChange: () => {
      setAttributes({
        cardConfig_showAddress: !attributes.cardConfig_showAddress
      });
    }
  })), attributes.cardConfig_showAddress && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Address Format"),
    value: attributes.cardConfig_addressFormat,
    options: [{
      label: "Street",
      value: "street"
    }, {
      label: "Street & City",
      value: "street_city"
    }, {
      label: "Street, City & State",
      value: "street_city_state"
    }, {
      label: "Street, City, State & Zip",
      value: "street_city_state_zip"
    }, {
      label: "City & State",
      value: "city_state"
    }],
    onChange: val => {
      setAttributes({
        cardConfig_addressFormat: val
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show Date?"),
    checked: attributes.cardConfig_showDate,
    onChange: () => {
      setAttributes({
        cardConfig_showDate: !attributes.cardConfig_showDate
      });
    }
  })), attributes.cardConfig_showDate && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Date Style"),
    value: attributes.cardConfig_dateStyle,
    options: [{
      label: "Start Date",
      value: "startDate"
    }, {
      label: "Next Date",
      value: "nextDate"
    }, {
      label: "Start Date through End Date",
      value: "range"
    }],
    onChange: val => {
      setAttributes({
        cardConfig_dateStyle: val
      });
    }
  })), attributes.cardConfig_showDate && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
    className: "sub-row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Date Format"),
    value: attributes.cardConfig_dateFormat,
    options: [{
      label: "2024-03-21",
      value: "Y-M-D"
    }, {
      label: "03-21",
      value: "M-D"
    }, {
      label: "03/21",
      value: "M/D"
    }, {
      label: "Mar 3",
      value: "m D"
    }],
    onChange: val => {
      setAttributes({
        cardConfig_dateFormat: val
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show Time?"),
    checked: attributes.cardConfig_showTime,
    onChange: () => {
      setAttributes({
        cardConfig_showTime: !attributes.cardConfig_showTime
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Describe Reccurence Pattern?"),
    checked: attributes.cardConfig_showPatternString,
    onChange: () => {
      setAttributes({
        cardConfig_showPatternString: !attributes.cardConfig_showPatternString
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show Upcoming Dates?"),
    checked: attributes.cardConfig_showUpcomingDates,
    onChange: () => {
      setAttributes({
        cardConfig_showUpcomingDates: !attributes.cardConfig_showUpcomingDates
      });
    }
  })), attributes.cardConfig_showUpcomingDates && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Date Format for Upcoming Dates"),
    value: attributes.cardConfig_upcomingDateFormat,
    options: [{
      label: "2024-03-21",
      value: "Y-M-D"
    }, {
      label: "03-21",
      value: "M-D"
    }, {
      label: "03/21",
      value: "M/D"
    }, {
      label: "Mar 3",
      value: "m D"
    }],
    onChange: val => {
      setAttributes({
        cardConfig_upcomingDateFormat: val
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show Thumbnail?"),
    checked: attributes.cardConfig_showThumbnail,
    onChange: () => {
      setAttributes({
        cardConfig_showThumbnail: !attributes.cardConfig_showThumbnail
      });
    }
  })), attributes.cardConfig_showThumbnail && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Image Size"),
    value: attributes.cardConfig_imageSize,
    onChange: val => {
      setAttributes({
        cardConfig_imageSize: val
      });
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Functionality"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Show Past Events?"),
    checked: attributes.showPastEvents,
    onChange: () => {
      setAttributes({
        showPastEvents: !attributes.showPastEvents
      });
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
    title: "Developer Configurations"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Block Namespace for JS Overrides"),
    value: attributes.blockConfig_jsOverrideNamespace,
    onChange: val => {
      setAttributes({
        blockConfig_jsOverrideNamespace: val
      });
    }
  }))));
};
const edit = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.withSelect)(select => {
  const data = {};
  // Select all terms for given taxonomy
  data.terms = select('core').getEntityRecords('taxonomy', 'event_category', {
    per_page: -1
  });
  if (!data.terms || !data.terms.length) {
    data.terms = [];
  }
  return data;
})(Wizard);

/*** EXPORTS ****************************************************************/

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (edit);

/***/ }),

/***/ "./src/calendar.js":
/*!*************************!*\
  !*** ./src/calendar.js ***!
  \*************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initializeEventasticCalendarBlock: () => (/* binding */ initializeEventasticCalendarBlock)
/* harmony export */ });
/**
 * Eventastic Calendar Block v2.0
 *
 * This script handles the frontend functionality for the Eventastic Calendar block.
 * It uses a pre-loaded `window.preLoadData` object and fetches subsequent months'
 * data on demand. It displays events in a sidebar list when a user clicks on a
 * day in the FullCalendar instance.
 */

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

/**
 * Parses a date string in YYYYMMDD format into a valid JavaScript Date object.
 * @param {string} dateString The date string (e.g., "20250709").
 * @returns {Date} A new Date object in the local timezone.
 */
function parseYMDString(dateString) {
  if (!dateString || typeof dateString !== 'string' || dateString.length !== 8) {
    return null;
  }
  const year = parseInt(dateString.substring(0, 4), 10);
  const month = parseInt(dateString.substring(4, 6), 10) - 1; // Month is 0-indexed
  const day = parseInt(dateString.substring(6, 8), 10);
  return new Date(year, month, day);
}

/**
 * Formats a Date object or a date string into various string representations.
 * @param {object} args - Arguments for formatting.
 * @param {Date|string} args.input - The date to format.
 * @param {string} [args.dateFormat="Y-M-D"] - The target format.
 * @returns {string} The formatted date string.
 */
function formatDate({
  input,
  dateFormat = "Y-M-D"
}) {
  const date = input instanceof Date ? input : new Date(input);
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  const shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const year = date.getFullYear();
  const monthIndex = date.getMonth();
  const day = date.getDate();
  const zeroMonth = ('0' + (monthIndex + 1)).slice(-2);
  const zeroDay = ('0' + day).slice(-2);
  switch (dateFormat) {
    case "Y-M-D":
      return `${year}-${zeroMonth}-${zeroDay}`;
    case "Ymd":
      return `${year}${zeroMonth}${zeroDay}`;
    case "Y-M":
      return `${year}-${zeroMonth}`;
    case "M D":
      return `${months[monthIndex]} ${day}`;
    case "m D":
      return `${shortMonths[monthIndex]} ${day}`;
    case "monthName":
      return months[monthIndex];
    case "fullDate":
      return `${months[monthIndex]} ${day}, ${year}`;
    default:
      return `${year}-${zeroMonth}-${zeroDay}`;
  }
}

/**
 * Parses a time string (e.g., "07:00 pm") into a Date object for time-based sorting.
 * @param {string} timeStr The time string to parse.
 * @returns {Date|false} A Date object, or false if input is invalid.
 */
function parseTime(timeStr) {
  if (typeof timeStr !== 'string' || timeStr.trim() === '') return false;
  const [time, period] = timeStr.toLowerCase().split(' ');
  const [hours, minutes] = time.split(':');
  let h = parseInt(hours, 10);
  if (period === 'pm' && h !== 12) h += 12;
  if (period === 'am' && h === 12) h = 0;
  const date = new Date();
  date.setHours(h, parseInt(minutes, 10), 0, 0);
  return date;
}

// =============================================================================
// EVENT DATA PROCESSING
// =============================================================================

/**
 * Processes raw event objects and expands recurring ones into individual instances.
 * @param {Array<object>} eventObjects - The array of raw event objects from the API.
 * @returns {Array<object>} A new array of events formatted for FullCalendar.
 */
function processAndExpandEvents(eventObjects) {
  const processedEvents = [];
  if (!Array.isArray(eventObjects)) return processedEvents;
  eventObjects.forEach(event => {
    const startDate = parseYMDString(event.events_meta.event_start_date);
    if (!startDate) return; // Skip if start date is invalid

    const endDate = event.events_meta.event_end_date ? parseYMDString(event.events_meta.event_end_date) : startDate;
    const isDaily = event.events_meta.events_recurrence_options === 'daily';

    // For daily events, create an instance for each day in the range
    if (isDaily && endDate) {
      let loopDate = new Date(startDate);
      while (loopDate <= endDate) {
        processedEvents.push({
          id: `${event.id}_${formatDate({
            input: loopDate,
            dateFormat: 'Y-M-D'
          })}`,
          title: event.title.rendered,
          start: new Date(loopDate),
          // Use a new Date object
          allDay: event.events_meta.events_event_all_day === 'true',
          extendedProps: {
            ...event
          }
        });
        loopDate.setDate(loopDate.getDate() + 1);
      }
    } else {
      // Handle one-day and specific-date events
      processedEvents.push({
        id: event.id,
        title: event.title.rendered,
        start: startDate,
        end: endDate && endDate > startDate ? new Date(endDate.setDate(endDate.getDate() + 1)) : null,
        // Set end date for multi-day non-daily events
        allDay: event.events_meta.events_event_all_day === 'true',
        extendedProps: {
          ...event
        }
      });
    }
  });
  return processedEvents;
}

// =============================================================================
// DOM & UI FUNCTIONS
// =============================================================================

/**
 * Builds and renders the grid of event cards in the sidebar.
 * @param {Array} events - An array of event objects to display.
 * @param {object} args - Display options.
 */
function buildEventsGrid(events, args = {}) {
  const $target = jQuery('#calendarList');
  const config = jQuery('#calendar-container').data();
  const maxEvents = config.maxnumberofgrideventstoshow || 10;
  let output = "";
  events.sort((a, b) => {
    if (a.start < b.start) return -1;
    if (a.start > b.start) return 1;
    const timeA = parseTime(a.extendedProps.events_meta.event_start_time);
    const timeB = parseTime(b.extendedProps.events_meta.event_start_time);
    if (!timeA || !timeB) return 0;
    return timeA - timeB;
  });
  if (events.length > 0) {
    events.forEach((event, index) => {
      const props = event.extendedProps;

      // If a specific day was clicked, use that date for display. Otherwise, use the event's start date.
      const dateToDisplay = args.clickedDate ? args.clickedDate : event.start;
      const eventDate = formatDate({
        input: dateToDisplay,
        dateFormat: config.cardconfig_dateformat || 'M D'
      });
      const eventTime = props.events_meta.event_start_time ? props.events_meta.event_start_time : '';
      const categories = Array.isArray(props.categories) ? props.categories.map(c => c.name).join(', ') : '';
      const location = props.events_meta.events_addr_multi || '';
      const template = `
                <div class="events-card ${index >= maxEvents ? 'overflow-card' : ''}">
                    <a href="${props.permalink}" target="_blank">
                        <div class="wrapper">
                            ${config.cardconfig_showthumbnail && props.featured_image ? `<div class="image-wrapper" style="background-image:url(${props.featured_image});"></div>` : ''}
                            <div class="content">
                                ${config.cardconfig_showcategories && categories ? `<div class="categories">${categories}</div>` : ''}
                                <div class="date">${eventDate} ${eventTime}</div>
                                <div class="title">${props.title.rendered}</div>
                                ${location ? `<div class="location">${location}</div>` : ''}
                            </div>
                        </div>
                    </a>
                </div>
            `;
      output += template;
    });
    if (events.length > maxEvents) {
      output += "<button id='eventastic-calendar-view-more'>View More</button>";
    }
  } else {
    const mssg = config.contentconfig_failuremessage || "There are no events for this day.";
    output = `<div class='ajax-message'>${mssg}</div>`;
  }
  $target.html(output);
}

/**
 * Toggles the visibility of overflow event cards.
 */
function toggleViewMore() {
  const $button = jQuery('#eventastic-calendar-view-more');
  $button.toggleClass('active');
  jQuery('.overflow-card').toggleClass('active');
  $button.text($button.hasClass('active') ? 'Show Less' : 'View More');
}

// =============================================================================
// FILTERING & DATA RETRIEVAL
// =============================================================================

/**
 * Fetches and processes events for a given month and adds them to the calendar.
 * @param {Date} date - A date within the target month.
 * @param {object} calendar - The FullCalendar instance.
 * @param {Array} loadedMonths - An array tracking which months have been loaded.
 */
async function fetchEventsForMonth(date, calendar, loadedMonths) {
  const targetMonth = formatDate({
    input: date,
    dateFormat: 'Y-M'
  });
  if (loadedMonths.includes(targetMonth)) return;

  // Get a reference to the loader element
  const $loader = jQuery('.eventastic-sidebar .eventastic-loader');
  try {
    // Show the loader before starting the fetch
    $loader.addClass('is-loading');
    const startDate = formatDate({
      input: new Date(date.getFullYear(), date.getMonth(), 1),
      dateFormat: 'Ymd'
    });
    const endDate = formatDate({
      input: new Date(date.getFullYear(), date.getMonth() + 1, 0),
      dateFormat: 'Ymd'
    });
    const apiBase = window.preLoadData.rest_url;
    const apiUrl = `${apiBase}kraken/v1/events?date_filter=true&start_date=${startDate}&end_date=${endDate}`;
    const response = await fetch(apiUrl);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const data = await response.json();
    loadedMonths.push(targetMonth);
    if (data && Array.isArray(data.events) && data.events.length > 0) {
      const newEvents = processAndExpandEvents(data.events);
      calendar.addEventSource(newEvents);
      newEvents.forEach(event => {
        const dateStr = formatDate({
          input: event.start,
          dateFormat: 'Y-M-D'
        });
        const $dayCell = jQuery(`.fc-daygrid-day[data-date="${dateStr}"]`);
        if ($dayCell.length && !$dayCell.find('.event-dot').length) {
          const dotEl = document.createElement('div');
          dotEl.className = 'event-dot';
          $dayCell.find('.fc-daygrid-day-frame').append(dotEl);
        }
      });
    }
  } catch (error) {
    console.error('Failed to fetch events for month:', error);
  } finally {
    // Always hide the loader after the operation is complete
    $loader.removeClass('is-loading');
  }
}

/**
 * Retrieves the currently selected category slugs from the filter controls.
 * @returns {Array<string>} An array of selected category slugs.
 */
function getSelectedCategories() {
  const config = jQuery('#calendar-container').data();
  const slugs = [];
  const filterType = config.filterconfig_categoryelementtype || 'buttons';
  if (filterType === "select") {
    jQuery(".category-filter > option:selected").each((_, el) => {
      if (jQuery(el).val()) slugs.push(jQuery(el).val());
    });
  } else if (filterType === "checkboxes") {
    jQuery('.category-checkbox:checked').each((_, el) => slugs.push(jQuery(el).val()));
  } else {
    jQuery(".event-category-filter.button.active").each((_, el) => slugs.push(jQuery(el).data('category')));
  }
  return slugs;
}

/**
 * Filters all loaded events based on date, category, and keyword.
 * @param {object} args - Filtering criteria.
 * @returns {Array} The filtered array of event objects.
 */
function getFilteredEvents(args) {
  const allEvents = window.eventasticCalendar.getEvents();
  const {
    targetDate,
    categories,
    keyword
  } = args;
  const targetDateStr = formatDate({
    input: targetDate,
    dateFormat: 'Y-M-D'
  });
  return allEvents.filter(event => {
    // Date filter
    const eventDateStr = formatDate({
      input: event.start,
      dateFormat: 'Y-M-D'
    });
    if (eventDateStr !== targetDateStr) return false;

    // Keyword filter
    if (keyword) {
      const searchPattern = new RegExp(keyword, "i");
      const title = event.title;
      const content = event.extendedProps.content || '';
      if (!searchPattern.test(title) && !searchPattern.test(content)) return false;
    }

    // Category filter
    if (categories && categories.length > 0) {
      if (!event.extendedProps.categories || event.extendedProps.categories.length === 0) return false;
      const eventCategories = event.extendedProps.categories.map(c => c.slug);
      if (!categories.some(slug => eventCategories.includes(slug))) return false;
    }
    return true;
  });
}

// =============================================================================
// MAIN INITIALIZATION
// =============================================================================
function initializeEventasticCalendarBlock() {
  const $calendarContainer = jQuery('#calendar-container');
  if (!$calendarContainer.length) {
    console.error("Eventastic Error: Missing #calendar-container element.");
    return;
  }
  const config = $calendarContainer.data() || {};
  const loadedMonths = []; // Track fetched months

  // 1. Process initial pre-loaded data
  const initialEvents = processAndExpandEvents(window.preLoadData.event_objects || []);
  const initialMonth = formatDate({
    input: new Date(),
    dateFormat: 'Y-M'
  });
  loadedMonths.push(initialMonth);

  // 2. Initialize FullCalendar
  const calendarEl = document.getElementById('calendar');
  const eventasticCalendar = new FullCalendar.Calendar(calendarEl, {
    events: initialEvents,
    height: 'auto',
    headerToolbar: {
      start: 'title',
      center: null,
      end: 'prev,next'
    },
    eventDisplay: 'none',
    // HIDE events from the calendar grid
    datesSet: async dateInfo => {
      // 1. Make the function async
      // This fires on initial load and when the view changes.
      // 2. Await the fetch to ensure events are loaded before proceeding.
      await fetchEventsForMonth(dateInfo.view.currentStart, eventasticCalendar, loadedMonths);

      // Set the title for the whole month
      const monthName = formatDate({
        input: dateInfo.view.currentStart,
        dateFormat: 'monthName'
      });
      jQuery('#events-list-title').html(`<h3>Upcoming ${monthName} Events</h3>`);

      // 3. Get all events from the calendar and filter them for the current month.
      const allEvents = window.eventasticCalendar.getEvents();
      const viewStart = dateInfo.view.currentStart;
      const viewMonth = viewStart.getMonth();
      const viewYear = viewStart.getFullYear();
      const eventsForMonth = allEvents.filter(event => {
        const eventDate = event.start;
        return eventDate.getMonth() === viewMonth && eventDate.getFullYear() === viewYear;
      });

      // 4. Build the grid with all of this month's events.
      buildEventsGrid(eventsForMonth);
    },
    dayCellDidMount: arg => {
      // Get all events currently loaded in the calendar instance
      const allEvents = window.eventasticCalendar.getEvents();
      const dateStr = formatDate({
        input: arg.date,
        dateFormat: 'Y-M-D'
      });

      // Check all events to see if any fall on the current day
      const eventsOnDay = allEvents.filter(e => formatDate({
        input: e.start,
        dateFormat: 'Y-M-D'
      }) === dateStr);
      if (eventsOnDay.length > 0) {
        const dotEl = document.createElement('div');
        dotEl.className = 'event-dot';
        // Check if a dot already exists to prevent duplicates
        if (!arg.el.querySelector('.event-dot')) {
          arg.el.querySelector('.fc-daygrid-day-frame').appendChild(dotEl);
        }
      }

      // Inside the dayCellDidMount function...
      arg.el.addEventListener('click', () => {
        jQuery('.fc-daygrid-day').removeClass('active');
        arg.el.classList.add('active');
        const eventsForDay = getFilteredEvents({
          targetDate: arg.date,
          categories: getSelectedCategories(),
          keyword: jQuery("#Keyword").val()
        });
        jQuery('#events-list-title').html(`<h3>Events for ${formatDate({
          input: arg.date,
          dateFormat: 'fullDate'
        })}</h3>`);

        // Pass the clicked date into buildEventsGrid
        buildEventsGrid(eventsForDay, {
          clickedDate: arg.date
        });
      });
    }
  });
  window.eventasticCalendar = eventasticCalendar;
  eventasticCalendar.render();

  // 3. Bind Global Event Handlers
  function reRenderGrid() {
    const activeDayEl = jQuery('.fc-daygrid-day.active');
    const activeDate = activeDayEl.length ? new Date(activeDayEl.data('date') + 'T00:00:00') : null;
    if (!activeDate) return;
    const eventsForDay = getFilteredEvents({
      targetDate: activeDate,
      categories: getSelectedCategories(),
      keyword: jQuery("#Keyword").val()
    });
    buildEventsGrid(eventsForDay);
  }
  jQuery('.event-category-filter, .category-filter, .category-checkbox').on('change click', function (e) {
    if (e.type === 'click' && jQuery(this).is('.event-category-filter')) {
      jQuery(this).toggleClass('active');
    }
    reRenderGrid();
  });
  jQuery(".eventFilterSubmit").on('click', reRenderGrid);
  jQuery(".resetFilters").on('click', () => {
    jQuery('.event-category-filter.button.active').removeClass('active');
    jQuery('.category-checkbox:checked').prop('checked', false);
    jQuery('.category-filter').val('');
    jQuery("#Keyword").val('');
    reRenderGrid();
  });
  jQuery('body').on('click', '#eventastic-calendar-view-more', e => {
    e.preventDefault();
    toggleViewMore();
  });
}

/***/ }),

/***/ "./src/edit.js":
/*!*********************!*\
  !*** ./src/edit.js ***!
  \*********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./editor.scss */ "./src/editor.scss");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _controls__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../controls */ "./controls/index.js");
/* harmony import */ var _controls_wizard__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../controls/wizard */ "./controls/wizard.js");
/* harmony import */ var _calendar__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./calendar */ "./src/calendar.js");








const Editor = props => {
  const {
    attributes,
    className
  } = props;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: className,
    ...blockProps
  }, attributes.mode === "edit" ? (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_wizard__WEBPACK_IMPORTED_MODULE_6__["default"], {
    ...props
  }) : (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default()), {
    block: props.name,
    ...props
  }));
};
const edit = props => {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    ...(0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)()
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls__WEBPACK_IMPORTED_MODULE_5__["default"], {
    ...props
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Editor, {
    ...props
  }));
};

/*** EXPORTS ****************************************************************/

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (edit);

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/style.scss");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./save */ "./src/save.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./block.json */ "./src/block.json");
/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */


/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */


/**
 * Internal dependencies
 */




/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_4__.name, {
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  /**
   * @see ./save.js
   */
  save: _save__WEBPACK_IMPORTED_MODULE_3__["default"]
});

/***/ }),

/***/ "./src/save.js":
/*!*********************!*\
  !*** ./src/save.js ***!
  \*********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ save)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */


/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
function save() {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    ..._wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps.save()
  }, 'Eventastic Calendar – hello from the saved content!');
}

/***/ }),

/***/ "./node_modules/dom-scroll-into-view/dist-web/index.js":
/*!*************************************************************!*\
  !*** ./node_modules/dom-scroll-into-view/dist-web/index.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    if (enumerableOnly) symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    });
    keys.push.apply(keys, symbols);
  }

  return keys;
}

function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};

    if (i % 2) {
      ownKeys(source, true).forEach(function (key) {
        _defineProperty(target, key, source[key]);
      });
    } else if (Object.getOwnPropertyDescriptors) {
      Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
    } else {
      ownKeys(source).forEach(function (key) {
        Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
      });
    }
  }

  return target;
}

var RE_NUM = /[\-+]?(?:\d*\.|)\d+(?:[eE][\-+]?\d+|)/.source;

function getClientPosition(elem) {
  var box;
  var x;
  var y;
  var doc = elem.ownerDocument;
  var body = doc.body;
  var docElem = doc && doc.documentElement; // 根据 GBS 最新数据，A-Grade Browsers 都已支持 getBoundingClientRect 方法，不用再考虑传统的实现方式

  box = elem.getBoundingClientRect(); // 注：jQuery 还考虑减去 docElem.clientLeft/clientTop
  // 但测试发现，这样反而会导致当 html 和 body 有边距/边框样式时，获取的值不正确
  // 此外，ie6 会忽略 html 的 margin 值，幸运地是没有谁会去设置 html 的 margin

  x = box.left;
  y = box.top; // In IE, most of the time, 2 extra pixels are added to the top and left
  // due to the implicit 2-pixel inset border.  In IE6/7 quirks mode and
  // IE6 standards mode, this border can be overridden by setting the
  // document element's border to zero -- thus, we cannot rely on the
  // offset always being 2 pixels.
  // In quirks mode, the offset can be determined by querying the body's
  // clientLeft/clientTop, but in standards mode, it is found by querying
  // the document element's clientLeft/clientTop.  Since we already called
  // getClientBoundingRect we have already forced a reflow, so it is not
  // too expensive just to query them all.
  // ie 下应该减去窗口的边框吧，毕竟默认 absolute 都是相对窗口定位的
  // 窗口边框标准是设 documentElement ,quirks 时设置 body
  // 最好禁止在 body 和 html 上边框 ，但 ie < 9 html 默认有 2px ，减去
  // 但是非 ie 不可能设置窗口边框，body html 也不是窗口 ,ie 可以通过 html,body 设置
  // 标准 ie 下 docElem.clientTop 就是 border-top
  // ie7 html 即窗口边框改变不了。永远为 2
  // 但标准 firefox/chrome/ie9 下 docElem.clientTop 是窗口边框，即使设了 border-top 也为 0

  x -= docElem.clientLeft || body.clientLeft || 0;
  y -= docElem.clientTop || body.clientTop || 0;
  return {
    left: x,
    top: y
  };
}

function getScroll(w, top) {
  var ret = w["page".concat(top ? 'Y' : 'X', "Offset")];
  var method = "scroll".concat(top ? 'Top' : 'Left');

  if (typeof ret !== 'number') {
    var d = w.document; // ie6,7,8 standard mode

    ret = d.documentElement[method];

    if (typeof ret !== 'number') {
      // quirks mode
      ret = d.body[method];
    }
  }

  return ret;
}

function getScrollLeft(w) {
  return getScroll(w);
}

function getScrollTop(w) {
  return getScroll(w, true);
}

function getOffset(el) {
  var pos = getClientPosition(el);
  var doc = el.ownerDocument;
  var w = doc.defaultView || doc.parentWindow;
  pos.left += getScrollLeft(w);
  pos.top += getScrollTop(w);
  return pos;
}

function _getComputedStyle(elem, name, computedStyle_) {
  var val = '';
  var d = elem.ownerDocument;
  var computedStyle = computedStyle_ || d.defaultView.getComputedStyle(elem, null); // https://github.com/kissyteam/kissy/issues/61

  if (computedStyle) {
    val = computedStyle.getPropertyValue(name) || computedStyle[name];
  }

  return val;
}

var _RE_NUM_NO_PX = new RegExp("^(".concat(RE_NUM, ")(?!px)[a-z%]+$"), 'i');

var RE_POS = /^(top|right|bottom|left)$/;
var CURRENT_STYLE = 'currentStyle';
var RUNTIME_STYLE = 'runtimeStyle';
var LEFT = 'left';
var PX = 'px';

function _getComputedStyleIE(elem, name) {
  // currentStyle maybe null
  // http://msdn.microsoft.com/en-us/library/ms535231.aspx
  var ret = elem[CURRENT_STYLE] && elem[CURRENT_STYLE][name]; // 当 width/height 设置为百分比时，通过 pixelLeft 方式转换的 width/height 值
  // 一开始就处理了! CUSTOM_STYLE.height,CUSTOM_STYLE.width ,cssHook 解决@2011-08-19
  // 在 ie 下不对，需要直接用 offset 方式
  // borderWidth 等值也有问题，但考虑到 borderWidth 设为百分比的概率很小，这里就不考虑了
  // From the awesome hack by Dean Edwards
  // http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291
  // If we're not dealing with a regular pixel number
  // but a number that has a weird ending, we need to convert it to pixels
  // exclude left right for relativity

  if (_RE_NUM_NO_PX.test(ret) && !RE_POS.test(name)) {
    // Remember the original values
    var style = elem.style;
    var left = style[LEFT];
    var rsLeft = elem[RUNTIME_STYLE][LEFT]; // prevent flashing of content

    elem[RUNTIME_STYLE][LEFT] = elem[CURRENT_STYLE][LEFT]; // Put in the new values to get a computed value out

    style[LEFT] = name === 'fontSize' ? '1em' : ret || 0;
    ret = style.pixelLeft + PX; // Revert the changed values

    style[LEFT] = left;
    elem[RUNTIME_STYLE][LEFT] = rsLeft;
  }

  return ret === '' ? 'auto' : ret;
}

var getComputedStyleX;

if (typeof window !== 'undefined') {
  getComputedStyleX = window.getComputedStyle ? _getComputedStyle : _getComputedStyleIE;
}

function each(arr, fn) {
  for (var i = 0; i < arr.length; i++) {
    fn(arr[i]);
  }
}

function isBorderBoxFn(elem) {
  return getComputedStyleX(elem, 'boxSizing') === 'border-box';
}

var BOX_MODELS = ['margin', 'border', 'padding'];
var CONTENT_INDEX = -1;
var PADDING_INDEX = 2;
var BORDER_INDEX = 1;
var MARGIN_INDEX = 0;

function swap(elem, options, callback) {
  var old = {};
  var style = elem.style;
  var name; // Remember the old values, and insert the new ones

  for (name in options) {
    if (options.hasOwnProperty(name)) {
      old[name] = style[name];
      style[name] = options[name];
    }
  }

  callback.call(elem); // Revert the old values

  for (name in options) {
    if (options.hasOwnProperty(name)) {
      style[name] = old[name];
    }
  }
}

function getPBMWidth(elem, props, which) {
  var value = 0;
  var prop;
  var j;
  var i;

  for (j = 0; j < props.length; j++) {
    prop = props[j];

    if (prop) {
      for (i = 0; i < which.length; i++) {
        var cssProp = void 0;

        if (prop === 'border') {
          cssProp = "".concat(prop + which[i], "Width");
        } else {
          cssProp = prop + which[i];
        }

        value += parseFloat(getComputedStyleX(elem, cssProp)) || 0;
      }
    }
  }

  return value;
}
/**
 * A crude way of determining if an object is a window
 * @member util
 */


function isWindow(obj) {
  // must use == for ie8

  /* eslint eqeqeq:0 */
  return obj != null && obj == obj.window;
}

var domUtils = {};
each(['Width', 'Height'], function (name) {
  domUtils["doc".concat(name)] = function (refWin) {
    var d = refWin.document;
    return Math.max( // firefox chrome documentElement.scrollHeight< body.scrollHeight
    // ie standard mode : documentElement.scrollHeight> body.scrollHeight
    d.documentElement["scroll".concat(name)], // quirks : documentElement.scrollHeight 最大等于可视窗口多一点？
    d.body["scroll".concat(name)], domUtils["viewport".concat(name)](d));
  };

  domUtils["viewport".concat(name)] = function (win) {
    // pc browser includes scrollbar in window.innerWidth
    var prop = "client".concat(name);
    var doc = win.document;
    var body = doc.body;
    var documentElement = doc.documentElement;
    var documentElementProp = documentElement[prop]; // 标准模式取 documentElement
    // backcompat 取 body

    return doc.compatMode === 'CSS1Compat' && documentElementProp || body && body[prop] || documentElementProp;
  };
});
/*
 得到元素的大小信息
 @param elem
 @param name
 @param {String} [extra]  'padding' : (css width) + padding
 'border' : (css width) + padding + border
 'margin' : (css width) + padding + border + margin
 */

function getWH(elem, name, extra) {
  if (isWindow(elem)) {
    return name === 'width' ? domUtils.viewportWidth(elem) : domUtils.viewportHeight(elem);
  } else if (elem.nodeType === 9) {
    return name === 'width' ? domUtils.docWidth(elem) : domUtils.docHeight(elem);
  }

  var which = name === 'width' ? ['Left', 'Right'] : ['Top', 'Bottom'];
  var borderBoxValue = name === 'width' ? elem.offsetWidth : elem.offsetHeight;
  var computedStyle = getComputedStyleX(elem);
  var isBorderBox = isBorderBoxFn(elem);
  var cssBoxValue = 0;

  if (borderBoxValue == null || borderBoxValue <= 0) {
    borderBoxValue = undefined; // Fall back to computed then un computed css if necessary

    cssBoxValue = getComputedStyleX(elem, name);

    if (cssBoxValue == null || Number(cssBoxValue) < 0) {
      cssBoxValue = elem.style[name] || 0;
    } // Normalize '', auto, and prepare for extra


    cssBoxValue = parseFloat(cssBoxValue) || 0;
  }

  if (extra === undefined) {
    extra = isBorderBox ? BORDER_INDEX : CONTENT_INDEX;
  }

  var borderBoxValueOrIsBorderBox = borderBoxValue !== undefined || isBorderBox;
  var val = borderBoxValue || cssBoxValue;

  if (extra === CONTENT_INDEX) {
    if (borderBoxValueOrIsBorderBox) {
      return val - getPBMWidth(elem, ['border', 'padding'], which);
    }

    return cssBoxValue;
  }

  if (borderBoxValueOrIsBorderBox) {
    var padding = extra === PADDING_INDEX ? -getPBMWidth(elem, ['border'], which) : getPBMWidth(elem, ['margin'], which);
    return val + (extra === BORDER_INDEX ? 0 : padding);
  }

  return cssBoxValue + getPBMWidth(elem, BOX_MODELS.slice(extra), which);
}

var cssShow = {
  position: 'absolute',
  visibility: 'hidden',
  display: 'block'
}; // fix #119 : https://github.com/kissyteam/kissy/issues/119

function getWHIgnoreDisplay(elem) {
  var val;
  var args = arguments; // in case elem is window
  // elem.offsetWidth === undefined

  if (elem.offsetWidth !== 0) {
    val = getWH.apply(undefined, args);
  } else {
    swap(elem, cssShow, function () {
      val = getWH.apply(undefined, args);
    });
  }

  return val;
}

function css(el, name, v) {
  var value = v;

  if (_typeof(name) === 'object') {
    for (var i in name) {
      if (name.hasOwnProperty(i)) {
        css(el, i, name[i]);
      }
    }

    return undefined;
  }

  if (typeof value !== 'undefined') {
    if (typeof value === 'number') {
      value += 'px';
    }

    el.style[name] = value;
    return undefined;
  }

  return getComputedStyleX(el, name);
}

each(['width', 'height'], function (name) {
  var first = name.charAt(0).toUpperCase() + name.slice(1);

  domUtils["outer".concat(first)] = function (el, includeMargin) {
    return el && getWHIgnoreDisplay(el, name, includeMargin ? MARGIN_INDEX : BORDER_INDEX);
  };

  var which = name === 'width' ? ['Left', 'Right'] : ['Top', 'Bottom'];

  domUtils[name] = function (elem, val) {
    if (val !== undefined) {
      if (elem) {
        var computedStyle = getComputedStyleX(elem);
        var isBorderBox = isBorderBoxFn(elem);

        if (isBorderBox) {
          val += getPBMWidth(elem, ['padding', 'border'], which);
        }

        return css(elem, name, val);
      }

      return undefined;
    }

    return elem && getWHIgnoreDisplay(elem, name, CONTENT_INDEX);
  };
}); // 设置 elem 相对 elem.ownerDocument 的坐标

function setOffset(elem, offset) {
  // set position first, in-case top/left are set even on static elem
  if (css(elem, 'position') === 'static') {
    elem.style.position = 'relative';
  }

  var old = getOffset(elem);
  var ret = {};
  var current;
  var key;

  for (key in offset) {
    if (offset.hasOwnProperty(key)) {
      current = parseFloat(css(elem, key)) || 0;
      ret[key] = current + offset[key] - old[key];
    }
  }

  css(elem, ret);
}

var util = _objectSpread2({
  getWindow: function getWindow(node) {
    var doc = node.ownerDocument || node;
    return doc.defaultView || doc.parentWindow;
  },
  offset: function offset(el, value) {
    if (typeof value !== 'undefined') {
      setOffset(el, value);
    } else {
      return getOffset(el);
    }
  },
  isWindow: isWindow,
  each: each,
  css: css,
  clone: function clone(obj) {
    var ret = {};

    for (var i in obj) {
      if (obj.hasOwnProperty(i)) {
        ret[i] = obj[i];
      }
    }

    var overflow = obj.overflow;

    if (overflow) {
      for (var _i in obj) {
        if (obj.hasOwnProperty(_i)) {
          ret.overflow[_i] = obj.overflow[_i];
        }
      }
    }

    return ret;
  },
  scrollLeft: function scrollLeft(w, v) {
    if (isWindow(w)) {
      if (v === undefined) {
        return getScrollLeft(w);
      }

      window.scrollTo(v, getScrollTop(w));
    } else {
      if (v === undefined) {
        return w.scrollLeft;
      }

      w.scrollLeft = v;
    }
  },
  scrollTop: function scrollTop(w, v) {
    if (isWindow(w)) {
      if (v === undefined) {
        return getScrollTop(w);
      }

      window.scrollTo(getScrollLeft(w), v);
    } else {
      if (v === undefined) {
        return w.scrollTop;
      }

      w.scrollTop = v;
    }
  },
  viewportWidth: 0,
  viewportHeight: 0
}, domUtils);

function scrollIntoView(elem, container, config) {
  config = config || {}; // document 归一化到 window

  if (container.nodeType === 9) {
    container = util.getWindow(container);
  }

  var allowHorizontalScroll = config.allowHorizontalScroll;
  var onlyScrollIfNeeded = config.onlyScrollIfNeeded;
  var alignWithTop = config.alignWithTop;
  var alignWithLeft = config.alignWithLeft;
  var offsetTop = config.offsetTop || 0;
  var offsetLeft = config.offsetLeft || 0;
  var offsetBottom = config.offsetBottom || 0;
  var offsetRight = config.offsetRight || 0;
  allowHorizontalScroll = allowHorizontalScroll === undefined ? true : allowHorizontalScroll;
  var isWin = util.isWindow(container);
  var elemOffset = util.offset(elem);
  var eh = util.outerHeight(elem);
  var ew = util.outerWidth(elem);
  var containerOffset;
  var ch;
  var cw;
  var containerScroll;
  var diffTop;
  var diffBottom;
  var win;
  var winScroll;
  var ww;
  var wh;

  if (isWin) {
    win = container;
    wh = util.height(win);
    ww = util.width(win);
    winScroll = {
      left: util.scrollLeft(win),
      top: util.scrollTop(win)
    }; // elem 相对 container 可视视窗的距离

    diffTop = {
      left: elemOffset.left - winScroll.left - offsetLeft,
      top: elemOffset.top - winScroll.top - offsetTop
    };
    diffBottom = {
      left: elemOffset.left + ew - (winScroll.left + ww) + offsetRight,
      top: elemOffset.top + eh - (winScroll.top + wh) + offsetBottom
    };
    containerScroll = winScroll;
  } else {
    containerOffset = util.offset(container);
    ch = container.clientHeight;
    cw = container.clientWidth;
    containerScroll = {
      left: container.scrollLeft,
      top: container.scrollTop
    }; // elem 相对 container 可视视窗的距离
    // 注意边框, offset 是边框到根节点

    diffTop = {
      left: elemOffset.left - (containerOffset.left + (parseFloat(util.css(container, 'borderLeftWidth')) || 0)) - offsetLeft,
      top: elemOffset.top - (containerOffset.top + (parseFloat(util.css(container, 'borderTopWidth')) || 0)) - offsetTop
    };
    diffBottom = {
      left: elemOffset.left + ew - (containerOffset.left + cw + (parseFloat(util.css(container, 'borderRightWidth')) || 0)) + offsetRight,
      top: elemOffset.top + eh - (containerOffset.top + ch + (parseFloat(util.css(container, 'borderBottomWidth')) || 0)) + offsetBottom
    };
  }

  if (diffTop.top < 0 || diffBottom.top > 0) {
    // 强制向上
    if (alignWithTop === true) {
      util.scrollTop(container, containerScroll.top + diffTop.top);
    } else if (alignWithTop === false) {
      util.scrollTop(container, containerScroll.top + diffBottom.top);
    } else {
      // 自动调整
      if (diffTop.top < 0) {
        util.scrollTop(container, containerScroll.top + diffTop.top);
      } else {
        util.scrollTop(container, containerScroll.top + diffBottom.top);
      }
    }
  } else {
    if (!onlyScrollIfNeeded) {
      alignWithTop = alignWithTop === undefined ? true : !!alignWithTop;

      if (alignWithTop) {
        util.scrollTop(container, containerScroll.top + diffTop.top);
      } else {
        util.scrollTop(container, containerScroll.top + diffBottom.top);
      }
    }
  }

  if (allowHorizontalScroll) {
    if (diffTop.left < 0 || diffBottom.left > 0) {
      // 强制向上
      if (alignWithLeft === true) {
        util.scrollLeft(container, containerScroll.left + diffTop.left);
      } else if (alignWithLeft === false) {
        util.scrollLeft(container, containerScroll.left + diffBottom.left);
      } else {
        // 自动调整
        if (diffTop.left < 0) {
          util.scrollLeft(container, containerScroll.left + diffTop.left);
        } else {
          util.scrollLeft(container, containerScroll.left + diffBottom.left);
        }
      }
    } else {
      if (!onlyScrollIfNeeded) {
        alignWithLeft = alignWithLeft === undefined ? true : !!alignWithLeft;

        if (alignWithLeft) {
          util.scrollLeft(container, containerScroll.left + diffTop.left);
        } else {
          util.scrollLeft(container, containerScroll.left + diffBottom.left);
        }
      }
    }
  }
}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (scrollIntoView);
//# sourceMappingURL=index.js.map


/***/ }),

/***/ "./src/editor.scss":
/*!*************************!*\
  !*** ./src/editor.scss ***!
  \*************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/style.scss":
/*!************************!*\
  !*** ./src/style.scss ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = window["React"];

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/***/ ((module) => {

"use strict";
module.exports = window["lodash"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["compose"];

/***/ }),

/***/ "@wordpress/core-data":
/*!**********************************!*\
  !*** external ["wp","coreData"] ***!
  \**********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["coreData"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/date":
/*!******************************!*\
  !*** external ["wp","date"] ***!
  \******************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["date"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/is-shallow-equal":
/*!****************************************!*\
  !*** external ["wp","isShallowEqual"] ***!
  \****************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["isShallowEqual"];

/***/ }),

/***/ "@wordpress/keycodes":
/*!**********************************!*\
  !*** external ["wp","keycodes"] ***!
  \**********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["keycodes"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/***/ ((module, exports) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = '';

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (arg) {
				classes = appendClass(classes, parseValue(arg));
			}
		}

		return classes;
	}

	function parseValue (arg) {
		if (typeof arg === 'string' || typeof arg === 'number') {
			return arg;
		}

		if (typeof arg !== 'object') {
			return '';
		}

		if (Array.isArray(arg)) {
			return classNames.apply(null, arg);
		}

		if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
			return arg.toString();
		}

		var classes = '';

		for (var key in arg) {
			if (hasOwn.call(arg, key) && arg[key]) {
				classes = appendClass(classes, key);
			}
		}

		return classes;
	}

	function appendClass (value, newClass) {
		if (!newClass) {
			return value;
		}
	
		if (value) {
			return value + ' ' + newClass;
		}
	
		return value + newClass;
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "./src/block.json":
/*!************************!*\
  !*** ./src/block.json ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"madden-media/kraken-calendar","version":"1.0.0","title":"Kraken Calendar","category":"widgets","icon":"calendar","description":"Calendar and Grid View of Events.","example":{},"supports":{"html":false},"textdomain":"kraken-calendar","editorScript":"file:./index.js","editorStyle":"file:./index.css","style":["file:./style-index.css","file:./style-one.css"],"viewScript":"file:./view.js","render":"file:./render.php","attributes":{"useFilters":{"type":"boolean","default":true},"calendarConfig_eventRenderType":{"type":"string","default":"emptyCircle"},"calendarConfig_headerToolbarStart":{"type":"string","default":"title"},"calendarConfig_headerToolbarCenter":{"type":"string","default":""},"calendarConfig_headerToolbarEnd":{"type":"string","default":"prev,next"},"maxNumberOfGridEventsToShow":{"type":"string","default":6},"listConfig_recurringDateHandler":{"type":"string","default":"showOnce"},"styleSheet":{"type":"string","default":"default"},"contentConfig_useAllCategories":{"type":"boolean","default":false},"contentConfig_categories":{"type":"array","default":[]},"contentConfig_failureMessage":{"type":"string","default":"There are no Events matching that."},"filterConfig_useDates":{"type":"boolean","default":true},"filterConfig_endDateDefault":{"type":"string","default":"one_month"},"filterConfig_fillEndDateInput":{"type":"boolean","default":false},"filterConfig_useKeyword":{"type":"boolean","default":true},"filterConfig_useCategories":{"type":"boolean","default":true},"filterConfig_useFilterReset":{"type":"boolean","default":true},"filterConfig_useFilterSubmit":{"type":"boolean","default":true},"filterConfig_categoryElementType":{"type":"string","default":"select"},"filterLocation":{"type":"string","default":"sidebar"},"listConfig_showTitle":{"type":"boolean","default":true},"listConfig_startDate":{"type":"string","default":""},"listConfig_endDate":{"type":"string","default":""},"listConfig_cardsPerRow":{"type":"string","default":"1"},"cardConfig_showCategories":{"type":"boolean","default":true},"cardConfig_showAddress":{"type":"boolean","default":true},"cardConfig_addressFormat":{"type":"string","default":"street"},"cardConfig_showDate":{"type":"boolean","default":true},"cardConfig_dateStyle":{"type":"string","default":"startDate"},"cardConfig_dateFormat":{"type":"string","default":"m D"},"cardConfig_showPatternString":{"type":"boolean","default":true},"cardConfig_showUpcomingDates":{"type":"boolean","default":true},"cardConfig_upcomingDateFormat":{"type":"string","default":"Y-M-D"},"cardConfig_showTime":{"type":"boolean","default":true},"cardConfig_showThumbnail":{"type":"boolean","default":false},"cardConfig_imageSize":{"type":"string","default":"thumbnail"},"blockConfig_jsOverrideNamespace":{"type":"string","default":""},"blockConfig_developerMode":{"type":"boolean","default":false}}}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"./style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = globalThis["webpackChunkeventastic_calendar"] = globalThis["webpackChunkeventastic_calendar"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["./style-index"], () => (__webpack_require__("./src/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map