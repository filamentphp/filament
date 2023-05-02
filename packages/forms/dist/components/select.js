var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
};
var __copyProps = (to, from, except, desc) => {
  if (from && typeof from === "object" || typeof from === "function") {
    for (let key of __getOwnPropNames(from))
      if (!__hasOwnProp.call(to, key) && key !== except)
        __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
  }
  return to;
};
var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
  // If the importer is in node compatibility mode or this is not an ESM
  // file that has been converted to a CommonJS file using a Babel-
  // compatible transform (i.e. "__esModule" has not been set), then set
  // "default" to the CommonJS "module.exports" for node compatibility.
  isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
  mod
));

// node_modules/choices.js/public/assets/scripts/choices.js
var require_choices = __commonJS({
  "node_modules/choices.js/public/assets/scripts/choices.js"(exports, module) {
    (function webpackUniversalModuleDefinition(root, factory) {
      if (typeof exports === "object" && typeof module === "object")
        module.exports = factory();
      else if (typeof define === "function" && define.amd)
        define([], factory);
      else if (typeof exports === "object")
        exports["Choices"] = factory();
      else
        root["Choices"] = factory();
    })(window, function() {
      return (
        /******/
        function() {
          "use strict";
          var __webpack_modules__ = {
            /***/
            282: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.clearChoices = exports2.activateChoices = exports2.filterChoices = exports2.addChoice = void 0;
                var constants_1 = __webpack_require__2(883);
                var addChoice = function(_a) {
                  var value = _a.value, label = _a.label, id = _a.id, groupId = _a.groupId, disabled = _a.disabled, elementId = _a.elementId, customProperties = _a.customProperties, placeholder = _a.placeholder, keyCode = _a.keyCode;
                  return {
                    type: constants_1.ACTION_TYPES.ADD_CHOICE,
                    value,
                    label,
                    id,
                    groupId,
                    disabled,
                    elementId,
                    customProperties,
                    placeholder,
                    keyCode
                  };
                };
                exports2.addChoice = addChoice;
                var filterChoices = function(results) {
                  return {
                    type: constants_1.ACTION_TYPES.FILTER_CHOICES,
                    results
                  };
                };
                exports2.filterChoices = filterChoices;
                var activateChoices = function(active) {
                  if (active === void 0) {
                    active = true;
                  }
                  return {
                    type: constants_1.ACTION_TYPES.ACTIVATE_CHOICES,
                    active
                  };
                };
                exports2.activateChoices = activateChoices;
                var clearChoices = function() {
                  return {
                    type: constants_1.ACTION_TYPES.CLEAR_CHOICES
                  };
                };
                exports2.clearChoices = clearChoices;
              }
            ),
            /***/
            783: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.addGroup = void 0;
                var constants_1 = __webpack_require__2(883);
                var addGroup = function(_a) {
                  var value = _a.value, id = _a.id, active = _a.active, disabled = _a.disabled;
                  return {
                    type: constants_1.ACTION_TYPES.ADD_GROUP,
                    value,
                    id,
                    active,
                    disabled
                  };
                };
                exports2.addGroup = addGroup;
              }
            ),
            /***/
            464: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.highlightItem = exports2.removeItem = exports2.addItem = void 0;
                var constants_1 = __webpack_require__2(883);
                var addItem = function(_a) {
                  var value = _a.value, label = _a.label, id = _a.id, choiceId = _a.choiceId, groupId = _a.groupId, customProperties = _a.customProperties, placeholder = _a.placeholder, keyCode = _a.keyCode;
                  return {
                    type: constants_1.ACTION_TYPES.ADD_ITEM,
                    value,
                    label,
                    id,
                    choiceId,
                    groupId,
                    customProperties,
                    placeholder,
                    keyCode
                  };
                };
                exports2.addItem = addItem;
                var removeItem = function(id, choiceId) {
                  return {
                    type: constants_1.ACTION_TYPES.REMOVE_ITEM,
                    id,
                    choiceId
                  };
                };
                exports2.removeItem = removeItem;
                var highlightItem = function(id, highlighted) {
                  return {
                    type: constants_1.ACTION_TYPES.HIGHLIGHT_ITEM,
                    id,
                    highlighted
                  };
                };
                exports2.highlightItem = highlightItem;
              }
            ),
            /***/
            137: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.setIsLoading = exports2.resetTo = exports2.clearAll = void 0;
                var constants_1 = __webpack_require__2(883);
                var clearAll = function() {
                  return {
                    type: constants_1.ACTION_TYPES.CLEAR_ALL
                  };
                };
                exports2.clearAll = clearAll;
                var resetTo = function(state) {
                  return {
                    type: constants_1.ACTION_TYPES.RESET_TO,
                    state
                  };
                };
                exports2.resetTo = resetTo;
                var setIsLoading = function(isLoading) {
                  return {
                    type: constants_1.ACTION_TYPES.SET_IS_LOADING,
                    isLoading
                  };
                };
                exports2.setIsLoading = setIsLoading;
              }
            ),
            /***/
            373: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                var __spreadArray = this && this.__spreadArray || function(to, from, pack) {
                  if (pack || arguments.length === 2)
                    for (var i = 0, l = from.length, ar; i < l; i++) {
                      if (ar || !(i in from)) {
                        if (!ar)
                          ar = Array.prototype.slice.call(from, 0, i);
                        ar[i] = from[i];
                      }
                    }
                  return to.concat(ar || Array.prototype.slice.call(from));
                };
                var __importDefault = this && this.__importDefault || function(mod) {
                  return mod && mod.__esModule ? mod : {
                    "default": mod
                  };
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var deepmerge_1 = __importDefault(__webpack_require__2(996));
                var fuse_js_1 = __importDefault(__webpack_require__2(221));
                var choices_1 = __webpack_require__2(282);
                var groups_1 = __webpack_require__2(783);
                var items_1 = __webpack_require__2(464);
                var misc_1 = __webpack_require__2(137);
                var components_1 = __webpack_require__2(520);
                var constants_1 = __webpack_require__2(883);
                var defaults_1 = __webpack_require__2(789);
                var utils_1 = __webpack_require__2(799);
                var reducers_1 = __webpack_require__2(655);
                var store_1 = __importDefault(__webpack_require__2(744));
                var templates_1 = __importDefault(__webpack_require__2(686));
                var IS_IE11 = "-ms-scroll-limit" in document.documentElement.style && "-ms-ime-align" in document.documentElement.style;
                var USER_DEFAULTS = {};
                var Choices2 = (
                  /** @class */
                  function() {
                    function Choices3(element, userConfig) {
                      if (element === void 0) {
                        element = "[data-choice]";
                      }
                      if (userConfig === void 0) {
                        userConfig = {};
                      }
                      var _this = this;
                      if (userConfig.allowHTML === void 0) {
                        console.warn("Deprecation warning: allowHTML will default to false in a future release. To render HTML in Choices, you will need to set it to true. Setting allowHTML will suppress this message.");
                      }
                      this.config = deepmerge_1.default.all(
                        [defaults_1.DEFAULT_CONFIG, Choices3.defaults.options, userConfig],
                        // When merging array configs, replace with a copy of the userConfig array,
                        // instead of concatenating with the default array
                        {
                          arrayMerge: function(_, sourceArray) {
                            return __spreadArray([], sourceArray, true);
                          }
                        }
                      );
                      var invalidConfigOptions = (0, utils_1.diff)(this.config, defaults_1.DEFAULT_CONFIG);
                      if (invalidConfigOptions.length) {
                        console.warn("Unknown config option(s) passed", invalidConfigOptions.join(", "));
                      }
                      var passedElement = typeof element === "string" ? document.querySelector(element) : element;
                      if (!(passedElement instanceof HTMLInputElement || passedElement instanceof HTMLSelectElement)) {
                        throw TypeError("Expected one of the following types text|select-one|select-multiple");
                      }
                      this._isTextElement = passedElement.type === constants_1.TEXT_TYPE;
                      this._isSelectOneElement = passedElement.type === constants_1.SELECT_ONE_TYPE;
                      this._isSelectMultipleElement = passedElement.type === constants_1.SELECT_MULTIPLE_TYPE;
                      this._isSelectElement = this._isSelectOneElement || this._isSelectMultipleElement;
                      this.config.searchEnabled = this._isSelectMultipleElement || this.config.searchEnabled;
                      if (!["auto", "always"].includes("".concat(this.config.renderSelectedChoices))) {
                        this.config.renderSelectedChoices = "auto";
                      }
                      if (userConfig.addItemFilter && typeof userConfig.addItemFilter !== "function") {
                        var re = userConfig.addItemFilter instanceof RegExp ? userConfig.addItemFilter : new RegExp(userConfig.addItemFilter);
                        this.config.addItemFilter = re.test.bind(re);
                      }
                      if (this._isTextElement) {
                        this.passedElement = new components_1.WrappedInput({
                          element: passedElement,
                          classNames: this.config.classNames,
                          delimiter: this.config.delimiter
                        });
                      } else {
                        this.passedElement = new components_1.WrappedSelect({
                          element: passedElement,
                          classNames: this.config.classNames,
                          template: function(data) {
                            return _this._templates.option(data);
                          }
                        });
                      }
                      this.initialised = false;
                      this._store = new store_1.default();
                      this._initialState = reducers_1.defaultState;
                      this._currentState = reducers_1.defaultState;
                      this._prevState = reducers_1.defaultState;
                      this._currentValue = "";
                      this._canSearch = !!this.config.searchEnabled;
                      this._isScrollingOnIe = false;
                      this._highlightPosition = 0;
                      this._wasTap = true;
                      this._placeholderValue = this._generatePlaceholderValue();
                      this._baseId = (0, utils_1.generateId)(this.passedElement.element, "choices-");
                      this._direction = this.passedElement.dir;
                      if (!this._direction) {
                        var elementDirection = window.getComputedStyle(this.passedElement.element).direction;
                        var documentDirection = window.getComputedStyle(document.documentElement).direction;
                        if (elementDirection !== documentDirection) {
                          this._direction = elementDirection;
                        }
                      }
                      this._idNames = {
                        itemChoice: "item-choice"
                      };
                      if (this._isSelectElement) {
                        this._presetGroups = this.passedElement.optionGroups;
                        this._presetOptions = this.passedElement.options;
                      }
                      this._presetChoices = this.config.choices;
                      this._presetItems = this.config.items;
                      if (this.passedElement.value && this._isTextElement) {
                        var splitValues = this.passedElement.value.split(this.config.delimiter);
                        this._presetItems = this._presetItems.concat(splitValues);
                      }
                      if (this.passedElement.options) {
                        this.passedElement.options.forEach(function(option) {
                          _this._presetChoices.push({
                            value: option.value,
                            label: option.innerHTML,
                            selected: !!option.selected,
                            disabled: option.disabled || option.parentNode.disabled,
                            placeholder: option.value === "" || option.hasAttribute("placeholder"),
                            customProperties: (0, utils_1.parseCustomProperties)(option.dataset.customProperties)
                          });
                        });
                      }
                      this._render = this._render.bind(this);
                      this._onFocus = this._onFocus.bind(this);
                      this._onBlur = this._onBlur.bind(this);
                      this._onKeyUp = this._onKeyUp.bind(this);
                      this._onKeyDown = this._onKeyDown.bind(this);
                      this._onClick = this._onClick.bind(this);
                      this._onTouchMove = this._onTouchMove.bind(this);
                      this._onTouchEnd = this._onTouchEnd.bind(this);
                      this._onMouseDown = this._onMouseDown.bind(this);
                      this._onMouseOver = this._onMouseOver.bind(this);
                      this._onFormReset = this._onFormReset.bind(this);
                      this._onSelectKey = this._onSelectKey.bind(this);
                      this._onEnterKey = this._onEnterKey.bind(this);
                      this._onEscapeKey = this._onEscapeKey.bind(this);
                      this._onDirectionKey = this._onDirectionKey.bind(this);
                      this._onDeleteKey = this._onDeleteKey.bind(this);
                      if (this.passedElement.isActive) {
                        if (!this.config.silent) {
                          console.warn("Trying to initialise Choices on element already initialised", {
                            element
                          });
                        }
                        this.initialised = true;
                        return;
                      }
                      this.init();
                    }
                    Object.defineProperty(Choices3, "defaults", {
                      get: function() {
                        return Object.preventExtensions({
                          get options() {
                            return USER_DEFAULTS;
                          },
                          get templates() {
                            return templates_1.default;
                          }
                        });
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Choices3.prototype.init = function() {
                      if (this.initialised) {
                        return;
                      }
                      this._createTemplates();
                      this._createElements();
                      this._createStructure();
                      this._store.subscribe(this._render);
                      this._render();
                      this._addEventListeners();
                      var shouldDisable = !this.config.addItems || this.passedElement.element.hasAttribute("disabled");
                      if (shouldDisable) {
                        this.disable();
                      }
                      this.initialised = true;
                      var callbackOnInit = this.config.callbackOnInit;
                      if (callbackOnInit && typeof callbackOnInit === "function") {
                        callbackOnInit.call(this);
                      }
                    };
                    Choices3.prototype.destroy = function() {
                      if (!this.initialised) {
                        return;
                      }
                      this._removeEventListeners();
                      this.passedElement.reveal();
                      this.containerOuter.unwrap(this.passedElement.element);
                      this.clearStore();
                      if (this._isSelectElement) {
                        this.passedElement.options = this._presetOptions;
                      }
                      this._templates = templates_1.default;
                      this.initialised = false;
                    };
                    Choices3.prototype.enable = function() {
                      if (this.passedElement.isDisabled) {
                        this.passedElement.enable();
                      }
                      if (this.containerOuter.isDisabled) {
                        this._addEventListeners();
                        this.input.enable();
                        this.containerOuter.enable();
                      }
                      return this;
                    };
                    Choices3.prototype.disable = function() {
                      if (!this.passedElement.isDisabled) {
                        this.passedElement.disable();
                      }
                      if (!this.containerOuter.isDisabled) {
                        this._removeEventListeners();
                        this.input.disable();
                        this.containerOuter.disable();
                      }
                      return this;
                    };
                    Choices3.prototype.highlightItem = function(item, runEvent) {
                      if (runEvent === void 0) {
                        runEvent = true;
                      }
                      if (!item || !item.id) {
                        return this;
                      }
                      var id = item.id, _a = item.groupId, groupId = _a === void 0 ? -1 : _a, _b = item.value, value = _b === void 0 ? "" : _b, _c = item.label, label = _c === void 0 ? "" : _c;
                      var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;
                      this._store.dispatch((0, items_1.highlightItem)(id, true));
                      if (runEvent) {
                        this.passedElement.triggerEvent(constants_1.EVENTS.highlightItem, {
                          id,
                          value,
                          label,
                          groupValue: group && group.value ? group.value : null
                        });
                      }
                      return this;
                    };
                    Choices3.prototype.unhighlightItem = function(item) {
                      if (!item || !item.id) {
                        return this;
                      }
                      var id = item.id, _a = item.groupId, groupId = _a === void 0 ? -1 : _a, _b = item.value, value = _b === void 0 ? "" : _b, _c = item.label, label = _c === void 0 ? "" : _c;
                      var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;
                      this._store.dispatch((0, items_1.highlightItem)(id, false));
                      this.passedElement.triggerEvent(constants_1.EVENTS.highlightItem, {
                        id,
                        value,
                        label,
                        groupValue: group && group.value ? group.value : null
                      });
                      return this;
                    };
                    Choices3.prototype.highlightAll = function() {
                      var _this = this;
                      this._store.items.forEach(function(item) {
                        return _this.highlightItem(item);
                      });
                      return this;
                    };
                    Choices3.prototype.unhighlightAll = function() {
                      var _this = this;
                      this._store.items.forEach(function(item) {
                        return _this.unhighlightItem(item);
                      });
                      return this;
                    };
                    Choices3.prototype.removeActiveItemsByValue = function(value) {
                      var _this = this;
                      this._store.activeItems.filter(function(item) {
                        return item.value === value;
                      }).forEach(function(item) {
                        return _this._removeItem(item);
                      });
                      return this;
                    };
                    Choices3.prototype.removeActiveItems = function(excludedId) {
                      var _this = this;
                      this._store.activeItems.filter(function(_a) {
                        var id = _a.id;
                        return id !== excludedId;
                      }).forEach(function(item) {
                        return _this._removeItem(item);
                      });
                      return this;
                    };
                    Choices3.prototype.removeHighlightedItems = function(runEvent) {
                      var _this = this;
                      if (runEvent === void 0) {
                        runEvent = false;
                      }
                      this._store.highlightedActiveItems.forEach(function(item) {
                        _this._removeItem(item);
                        if (runEvent) {
                          _this._triggerChange(item.value);
                        }
                      });
                      return this;
                    };
                    Choices3.prototype.showDropdown = function(preventInputFocus) {
                      var _this = this;
                      if (this.dropdown.isActive) {
                        return this;
                      }
                      requestAnimationFrame(function() {
                        _this.dropdown.show();
                        _this.containerOuter.open(_this.dropdown.distanceFromTopWindow);
                        if (!preventInputFocus && _this._canSearch) {
                          _this.input.focus();
                        }
                        _this.passedElement.triggerEvent(constants_1.EVENTS.showDropdown, {});
                      });
                      return this;
                    };
                    Choices3.prototype.hideDropdown = function(preventInputBlur) {
                      var _this = this;
                      if (!this.dropdown.isActive) {
                        return this;
                      }
                      requestAnimationFrame(function() {
                        _this.dropdown.hide();
                        _this.containerOuter.close();
                        if (!preventInputBlur && _this._canSearch) {
                          _this.input.removeActiveDescendant();
                          _this.input.blur();
                        }
                        _this.passedElement.triggerEvent(constants_1.EVENTS.hideDropdown, {});
                      });
                      return this;
                    };
                    Choices3.prototype.getValue = function(valueOnly) {
                      if (valueOnly === void 0) {
                        valueOnly = false;
                      }
                      var values = this._store.activeItems.reduce(function(selectedItems, item) {
                        var itemValue = valueOnly ? item.value : item;
                        selectedItems.push(itemValue);
                        return selectedItems;
                      }, []);
                      return this._isSelectOneElement ? values[0] : values;
                    };
                    Choices3.prototype.setValue = function(items) {
                      var _this = this;
                      if (!this.initialised) {
                        return this;
                      }
                      items.forEach(function(value) {
                        return _this._setChoiceOrItem(value);
                      });
                      return this;
                    };
                    Choices3.prototype.setChoiceByValue = function(value) {
                      var _this = this;
                      if (!this.initialised || this._isTextElement) {
                        return this;
                      }
                      var choiceValue = Array.isArray(value) ? value : [value];
                      choiceValue.forEach(function(val) {
                        return _this._findAndSelectChoiceByValue(val);
                      });
                      return this;
                    };
                    Choices3.prototype.setChoices = function(choicesArrayOrFetcher, value, label, replaceChoices) {
                      var _this = this;
                      if (choicesArrayOrFetcher === void 0) {
                        choicesArrayOrFetcher = [];
                      }
                      if (value === void 0) {
                        value = "value";
                      }
                      if (label === void 0) {
                        label = "label";
                      }
                      if (replaceChoices === void 0) {
                        replaceChoices = false;
                      }
                      if (!this.initialised) {
                        throw new ReferenceError("setChoices was called on a non-initialized instance of Choices");
                      }
                      if (!this._isSelectElement) {
                        throw new TypeError("setChoices can't be used with INPUT based Choices");
                      }
                      if (typeof value !== "string" || !value) {
                        throw new TypeError("value parameter must be a name of 'value' field in passed objects");
                      }
                      if (replaceChoices) {
                        this.clearChoices();
                      }
                      if (typeof choicesArrayOrFetcher === "function") {
                        var fetcher_1 = choicesArrayOrFetcher(this);
                        if (typeof Promise === "function" && fetcher_1 instanceof Promise) {
                          return new Promise(function(resolve) {
                            return requestAnimationFrame(resolve);
                          }).then(function() {
                            return _this._handleLoadingState(true);
                          }).then(function() {
                            return fetcher_1;
                          }).then(function(data) {
                            return _this.setChoices(data, value, label, replaceChoices);
                          }).catch(function(err) {
                            if (!_this.config.silent) {
                              console.error(err);
                            }
                          }).then(function() {
                            return _this._handleLoadingState(false);
                          }).then(function() {
                            return _this;
                          });
                        }
                        if (!Array.isArray(fetcher_1)) {
                          throw new TypeError(".setChoices first argument function must return either array of choices or Promise, got: ".concat(typeof fetcher_1));
                        }
                        return this.setChoices(fetcher_1, value, label, false);
                      }
                      if (!Array.isArray(choicesArrayOrFetcher)) {
                        throw new TypeError(".setChoices must be called either with array of choices with a function resulting into Promise of array of choices");
                      }
                      this.containerOuter.removeLoadingState();
                      this._startLoading();
                      choicesArrayOrFetcher.forEach(function(groupOrChoice) {
                        if (groupOrChoice.choices) {
                          _this._addGroup({
                            id: groupOrChoice.id ? parseInt("".concat(groupOrChoice.id), 10) : null,
                            group: groupOrChoice,
                            valueKey: value,
                            labelKey: label
                          });
                        } else {
                          var choice = groupOrChoice;
                          _this._addChoice({
                            value: choice[value],
                            label: choice[label],
                            isSelected: !!choice.selected,
                            isDisabled: !!choice.disabled,
                            placeholder: !!choice.placeholder,
                            customProperties: choice.customProperties
                          });
                        }
                      });
                      this._stopLoading();
                      return this;
                    };
                    Choices3.prototype.clearChoices = function() {
                      this._store.dispatch((0, choices_1.clearChoices)());
                      return this;
                    };
                    Choices3.prototype.clearStore = function() {
                      this._store.dispatch((0, misc_1.clearAll)());
                      return this;
                    };
                    Choices3.prototype.clearInput = function() {
                      var shouldSetInputWidth = !this._isSelectOneElement;
                      this.input.clear(shouldSetInputWidth);
                      if (!this._isTextElement && this._canSearch) {
                        this._isSearching = false;
                        this._store.dispatch((0, choices_1.activateChoices)(true));
                      }
                      return this;
                    };
                    Choices3.prototype._render = function() {
                      if (this._store.isLoading()) {
                        return;
                      }
                      this._currentState = this._store.state;
                      var stateChanged = this._currentState.choices !== this._prevState.choices || this._currentState.groups !== this._prevState.groups || this._currentState.items !== this._prevState.items;
                      var shouldRenderChoices = this._isSelectElement;
                      var shouldRenderItems = this._currentState.items !== this._prevState.items;
                      if (!stateChanged) {
                        return;
                      }
                      if (shouldRenderChoices) {
                        this._renderChoices();
                      }
                      if (shouldRenderItems) {
                        this._renderItems();
                      }
                      this._prevState = this._currentState;
                    };
                    Choices3.prototype._renderChoices = function() {
                      var _this = this;
                      var _a = this._store, activeGroups = _a.activeGroups, activeChoices = _a.activeChoices;
                      var choiceListFragment = document.createDocumentFragment();
                      this.choiceList.clear();
                      if (this.config.resetScrollPosition) {
                        requestAnimationFrame(function() {
                          return _this.choiceList.scrollToTop();
                        });
                      }
                      if (activeGroups.length >= 1 && !this._isSearching) {
                        var activePlaceholders = activeChoices.filter(function(activeChoice) {
                          return activeChoice.placeholder === true && activeChoice.groupId === -1;
                        });
                        if (activePlaceholders.length >= 1) {
                          choiceListFragment = this._createChoicesFragment(activePlaceholders, choiceListFragment);
                        }
                        choiceListFragment = this._createGroupsFragment(activeGroups, activeChoices, choiceListFragment);
                      } else if (activeChoices.length >= 1) {
                        choiceListFragment = this._createChoicesFragment(activeChoices, choiceListFragment);
                      }
                      if (choiceListFragment.childNodes && choiceListFragment.childNodes.length > 0) {
                        var activeItems = this._store.activeItems;
                        var canAddItem = this._canAddItem(activeItems, this.input.value);
                        if (canAddItem.response) {
                          this.choiceList.append(choiceListFragment);
                          this._highlightChoice();
                        } else {
                          var notice = this._getTemplate("notice", canAddItem.notice);
                          this.choiceList.append(notice);
                        }
                      } else {
                        var dropdownItem = void 0;
                        var notice = void 0;
                        if (this._isSearching) {
                          notice = typeof this.config.noResultsText === "function" ? this.config.noResultsText() : this.config.noResultsText;
                          dropdownItem = this._getTemplate("notice", notice, "no-results");
                        } else {
                          notice = typeof this.config.noChoicesText === "function" ? this.config.noChoicesText() : this.config.noChoicesText;
                          dropdownItem = this._getTemplate("notice", notice, "no-choices");
                        }
                        this.choiceList.append(dropdownItem);
                      }
                    };
                    Choices3.prototype._renderItems = function() {
                      var activeItems = this._store.activeItems || [];
                      this.itemList.clear();
                      var itemListFragment = this._createItemsFragment(activeItems);
                      if (itemListFragment.childNodes) {
                        this.itemList.append(itemListFragment);
                      }
                    };
                    Choices3.prototype._createGroupsFragment = function(groups, choices, fragment) {
                      var _this = this;
                      if (fragment === void 0) {
                        fragment = document.createDocumentFragment();
                      }
                      var getGroupChoices = function(group) {
                        return choices.filter(function(choice) {
                          if (_this._isSelectOneElement) {
                            return choice.groupId === group.id;
                          }
                          return choice.groupId === group.id && (_this.config.renderSelectedChoices === "always" || !choice.selected);
                        });
                      };
                      if (this.config.shouldSort) {
                        groups.sort(this.config.sorter);
                      }
                      groups.forEach(function(group) {
                        var groupChoices = getGroupChoices(group);
                        if (groupChoices.length >= 1) {
                          var dropdownGroup = _this._getTemplate("choiceGroup", group);
                          fragment.appendChild(dropdownGroup);
                          _this._createChoicesFragment(groupChoices, fragment, true);
                        }
                      });
                      return fragment;
                    };
                    Choices3.prototype._createChoicesFragment = function(choices, fragment, withinGroup) {
                      var _this = this;
                      if (fragment === void 0) {
                        fragment = document.createDocumentFragment();
                      }
                      if (withinGroup === void 0) {
                        withinGroup = false;
                      }
                      var _a = this.config, renderSelectedChoices = _a.renderSelectedChoices, searchResultLimit = _a.searchResultLimit, renderChoiceLimit = _a.renderChoiceLimit;
                      var filter = this._isSearching ? utils_1.sortByScore : this.config.sorter;
                      var appendChoice = function(choice) {
                        var shouldRender = renderSelectedChoices === "auto" ? _this._isSelectOneElement || !choice.selected : true;
                        if (shouldRender) {
                          var dropdownItem = _this._getTemplate("choice", choice, _this.config.itemSelectText);
                          fragment.appendChild(dropdownItem);
                        }
                      };
                      var rendererableChoices = choices;
                      if (renderSelectedChoices === "auto" && !this._isSelectOneElement) {
                        rendererableChoices = choices.filter(function(choice) {
                          return !choice.selected;
                        });
                      }
                      var _b = rendererableChoices.reduce(function(acc, choice) {
                        if (choice.placeholder) {
                          acc.placeholderChoices.push(choice);
                        } else {
                          acc.normalChoices.push(choice);
                        }
                        return acc;
                      }, {
                        placeholderChoices: [],
                        normalChoices: []
                      }), placeholderChoices = _b.placeholderChoices, normalChoices = _b.normalChoices;
                      if (this.config.shouldSort || this._isSearching) {
                        normalChoices.sort(filter);
                      }
                      var choiceLimit = rendererableChoices.length;
                      var sortedChoices = this._isSelectOneElement ? __spreadArray(__spreadArray([], placeholderChoices, true), normalChoices, true) : normalChoices;
                      if (this._isSearching) {
                        choiceLimit = searchResultLimit;
                      } else if (renderChoiceLimit && renderChoiceLimit > 0 && !withinGroup) {
                        choiceLimit = renderChoiceLimit;
                      }
                      for (var i = 0; i < choiceLimit; i += 1) {
                        if (sortedChoices[i]) {
                          appendChoice(sortedChoices[i]);
                        }
                      }
                      return fragment;
                    };
                    Choices3.prototype._createItemsFragment = function(items, fragment) {
                      var _this = this;
                      if (fragment === void 0) {
                        fragment = document.createDocumentFragment();
                      }
                      var _a = this.config, shouldSortItems = _a.shouldSortItems, sorter = _a.sorter, removeItemButton = _a.removeItemButton;
                      if (shouldSortItems && !this._isSelectOneElement) {
                        items.sort(sorter);
                      }
                      if (this._isTextElement) {
                        this.passedElement.value = items.map(function(_a2) {
                          var value = _a2.value;
                          return value;
                        }).join(this.config.delimiter);
                      } else {
                        this.passedElement.options = items;
                      }
                      var addItemToFragment = function(item) {
                        var listItem = _this._getTemplate("item", item, removeItemButton);
                        fragment.appendChild(listItem);
                      };
                      items.forEach(addItemToFragment);
                      return fragment;
                    };
                    Choices3.prototype._triggerChange = function(value) {
                      if (value === void 0 || value === null) {
                        return;
                      }
                      this.passedElement.triggerEvent(constants_1.EVENTS.change, {
                        value
                      });
                    };
                    Choices3.prototype._selectPlaceholderChoice = function(placeholderChoice) {
                      this._addItem({
                        value: placeholderChoice.value,
                        label: placeholderChoice.label,
                        choiceId: placeholderChoice.id,
                        groupId: placeholderChoice.groupId,
                        placeholder: placeholderChoice.placeholder
                      });
                      this._triggerChange(placeholderChoice.value);
                    };
                    Choices3.prototype._handleButtonAction = function(activeItems, element) {
                      if (!activeItems || !element || !this.config.removeItems || !this.config.removeItemButton) {
                        return;
                      }
                      var itemId = element.parentNode && element.parentNode.dataset.id;
                      var itemToRemove = itemId && activeItems.find(function(item) {
                        return item.id === parseInt(itemId, 10);
                      });
                      if (!itemToRemove) {
                        return;
                      }
                      this._removeItem(itemToRemove);
                      this._triggerChange(itemToRemove.value);
                      if (this._isSelectOneElement && this._store.placeholderChoice) {
                        this._selectPlaceholderChoice(this._store.placeholderChoice);
                      }
                    };
                    Choices3.prototype._handleItemAction = function(activeItems, element, hasShiftKey) {
                      var _this = this;
                      if (hasShiftKey === void 0) {
                        hasShiftKey = false;
                      }
                      if (!activeItems || !element || !this.config.removeItems || this._isSelectOneElement) {
                        return;
                      }
                      var passedId = element.dataset.id;
                      activeItems.forEach(function(item) {
                        if (item.id === parseInt("".concat(passedId), 10) && !item.highlighted) {
                          _this.highlightItem(item);
                        } else if (!hasShiftKey && item.highlighted) {
                          _this.unhighlightItem(item);
                        }
                      });
                      this.input.focus();
                    };
                    Choices3.prototype._handleChoiceAction = function(activeItems, element) {
                      if (!activeItems || !element) {
                        return;
                      }
                      var id = element.dataset.id;
                      var choice = id && this._store.getChoiceById(id);
                      if (!choice) {
                        return;
                      }
                      var passedKeyCode = activeItems[0] && activeItems[0].keyCode ? activeItems[0].keyCode : void 0;
                      var hasActiveDropdown = this.dropdown.isActive;
                      choice.keyCode = passedKeyCode;
                      this.passedElement.triggerEvent(constants_1.EVENTS.choice, {
                        choice
                      });
                      if (!choice.selected && !choice.disabled) {
                        var canAddItem = this._canAddItem(activeItems, choice.value);
                        if (canAddItem.response) {
                          this._addItem({
                            value: choice.value,
                            label: choice.label,
                            choiceId: choice.id,
                            groupId: choice.groupId,
                            customProperties: choice.customProperties,
                            placeholder: choice.placeholder,
                            keyCode: choice.keyCode
                          });
                          this._triggerChange(choice.value);
                        }
                      }
                      this.clearInput();
                      if (hasActiveDropdown && this._isSelectOneElement) {
                        this.hideDropdown(true);
                        this.containerOuter.focus();
                      }
                    };
                    Choices3.prototype._handleBackspace = function(activeItems) {
                      if (!this.config.removeItems || !activeItems) {
                        return;
                      }
                      var lastItem = activeItems[activeItems.length - 1];
                      var hasHighlightedItems = activeItems.some(function(item) {
                        return item.highlighted;
                      });
                      if (this.config.editItems && !hasHighlightedItems && lastItem) {
                        this.input.value = lastItem.value;
                        this.input.setWidth();
                        this._removeItem(lastItem);
                        this._triggerChange(lastItem.value);
                      } else {
                        if (!hasHighlightedItems) {
                          this.highlightItem(lastItem, false);
                        }
                        this.removeHighlightedItems(true);
                      }
                    };
                    Choices3.prototype._startLoading = function() {
                      this._store.dispatch((0, misc_1.setIsLoading)(true));
                    };
                    Choices3.prototype._stopLoading = function() {
                      this._store.dispatch((0, misc_1.setIsLoading)(false));
                    };
                    Choices3.prototype._handleLoadingState = function(setLoading) {
                      if (setLoading === void 0) {
                        setLoading = true;
                      }
                      var placeholderItem = this.itemList.getChild(".".concat(this.config.classNames.placeholder));
                      if (setLoading) {
                        this.disable();
                        this.containerOuter.addLoadingState();
                        if (this._isSelectOneElement) {
                          if (!placeholderItem) {
                            placeholderItem = this._getTemplate("placeholder", this.config.loadingText);
                            if (placeholderItem) {
                              this.itemList.append(placeholderItem);
                            }
                          } else {
                            placeholderItem.innerHTML = this.config.loadingText;
                          }
                        } else {
                          this.input.placeholder = this.config.loadingText;
                        }
                      } else {
                        this.enable();
                        this.containerOuter.removeLoadingState();
                        if (this._isSelectOneElement) {
                          if (placeholderItem) {
                            placeholderItem.innerHTML = this._placeholderValue || "";
                          }
                        } else {
                          this.input.placeholder = this._placeholderValue || "";
                        }
                      }
                    };
                    Choices3.prototype._handleSearch = function(value) {
                      if (!this.input.isFocussed) {
                        return;
                      }
                      var choices = this._store.choices;
                      var _a = this.config, searchFloor = _a.searchFloor, searchChoices = _a.searchChoices;
                      var hasUnactiveChoices = choices.some(function(option) {
                        return !option.active;
                      });
                      if (value !== null && typeof value !== "undefined" && value.length >= searchFloor) {
                        var resultCount = searchChoices ? this._searchChoices(value) : 0;
                        this.passedElement.triggerEvent(constants_1.EVENTS.search, {
                          value,
                          resultCount
                        });
                      } else if (hasUnactiveChoices) {
                        this._isSearching = false;
                        this._store.dispatch((0, choices_1.activateChoices)(true));
                      }
                    };
                    Choices3.prototype._canAddItem = function(activeItems, value) {
                      var canAddItem = true;
                      var notice = typeof this.config.addItemText === "function" ? this.config.addItemText(value) : this.config.addItemText;
                      if (!this._isSelectOneElement) {
                        var isDuplicateValue = (0, utils_1.existsInArray)(activeItems, value);
                        if (this.config.maxItemCount > 0 && this.config.maxItemCount <= activeItems.length) {
                          canAddItem = false;
                          notice = typeof this.config.maxItemText === "function" ? this.config.maxItemText(this.config.maxItemCount) : this.config.maxItemText;
                        }
                        if (!this.config.duplicateItemsAllowed && isDuplicateValue && canAddItem) {
                          canAddItem = false;
                          notice = typeof this.config.uniqueItemText === "function" ? this.config.uniqueItemText(value) : this.config.uniqueItemText;
                        }
                        if (this._isTextElement && this.config.addItems && canAddItem && typeof this.config.addItemFilter === "function" && !this.config.addItemFilter(value)) {
                          canAddItem = false;
                          notice = typeof this.config.customAddItemText === "function" ? this.config.customAddItemText(value) : this.config.customAddItemText;
                        }
                      }
                      return {
                        response: canAddItem,
                        notice
                      };
                    };
                    Choices3.prototype._searchChoices = function(value) {
                      var newValue = typeof value === "string" ? value.trim() : value;
                      var currentValue = typeof this._currentValue === "string" ? this._currentValue.trim() : this._currentValue;
                      if (newValue.length < 1 && newValue === "".concat(currentValue, " ")) {
                        return 0;
                      }
                      var haystack = this._store.searchableChoices;
                      var needle = newValue;
                      var options = Object.assign(this.config.fuseOptions, {
                        keys: __spreadArray([], this.config.searchFields, true),
                        includeMatches: true
                      });
                      var fuse = new fuse_js_1.default(haystack, options);
                      var results = fuse.search(needle);
                      this._currentValue = newValue;
                      this._highlightPosition = 0;
                      this._isSearching = true;
                      this._store.dispatch((0, choices_1.filterChoices)(results));
                      return results.length;
                    };
                    Choices3.prototype._addEventListeners = function() {
                      var documentElement = document.documentElement;
                      documentElement.addEventListener("touchend", this._onTouchEnd, true);
                      this.containerOuter.element.addEventListener("keydown", this._onKeyDown, true);
                      this.containerOuter.element.addEventListener("mousedown", this._onMouseDown, true);
                      documentElement.addEventListener("click", this._onClick, {
                        passive: true
                      });
                      documentElement.addEventListener("touchmove", this._onTouchMove, {
                        passive: true
                      });
                      this.dropdown.element.addEventListener("mouseover", this._onMouseOver, {
                        passive: true
                      });
                      if (this._isSelectOneElement) {
                        this.containerOuter.element.addEventListener("focus", this._onFocus, {
                          passive: true
                        });
                        this.containerOuter.element.addEventListener("blur", this._onBlur, {
                          passive: true
                        });
                      }
                      this.input.element.addEventListener("keyup", this._onKeyUp, {
                        passive: true
                      });
                      this.input.element.addEventListener("focus", this._onFocus, {
                        passive: true
                      });
                      this.input.element.addEventListener("blur", this._onBlur, {
                        passive: true
                      });
                      if (this.input.element.form) {
                        this.input.element.form.addEventListener("reset", this._onFormReset, {
                          passive: true
                        });
                      }
                      this.input.addEventListeners();
                    };
                    Choices3.prototype._removeEventListeners = function() {
                      var documentElement = document.documentElement;
                      documentElement.removeEventListener("touchend", this._onTouchEnd, true);
                      this.containerOuter.element.removeEventListener("keydown", this._onKeyDown, true);
                      this.containerOuter.element.removeEventListener("mousedown", this._onMouseDown, true);
                      documentElement.removeEventListener("click", this._onClick);
                      documentElement.removeEventListener("touchmove", this._onTouchMove);
                      this.dropdown.element.removeEventListener("mouseover", this._onMouseOver);
                      if (this._isSelectOneElement) {
                        this.containerOuter.element.removeEventListener("focus", this._onFocus);
                        this.containerOuter.element.removeEventListener("blur", this._onBlur);
                      }
                      this.input.element.removeEventListener("keyup", this._onKeyUp);
                      this.input.element.removeEventListener("focus", this._onFocus);
                      this.input.element.removeEventListener("blur", this._onBlur);
                      if (this.input.element.form) {
                        this.input.element.form.removeEventListener("reset", this._onFormReset);
                      }
                      this.input.removeEventListeners();
                    };
                    Choices3.prototype._onKeyDown = function(event) {
                      var keyCode = event.keyCode;
                      var activeItems = this._store.activeItems;
                      var hasFocusedInput = this.input.isFocussed;
                      var hasActiveDropdown = this.dropdown.isActive;
                      var hasItems = this.itemList.hasChildren();
                      var keyString = String.fromCharCode(keyCode);
                      var wasPrintableChar = /[^\x00-\x1F]/.test(keyString);
                      var BACK_KEY = constants_1.KEY_CODES.BACK_KEY, DELETE_KEY = constants_1.KEY_CODES.DELETE_KEY, ENTER_KEY = constants_1.KEY_CODES.ENTER_KEY, A_KEY = constants_1.KEY_CODES.A_KEY, ESC_KEY = constants_1.KEY_CODES.ESC_KEY, UP_KEY = constants_1.KEY_CODES.UP_KEY, DOWN_KEY = constants_1.KEY_CODES.DOWN_KEY, PAGE_UP_KEY = constants_1.KEY_CODES.PAGE_UP_KEY, PAGE_DOWN_KEY = constants_1.KEY_CODES.PAGE_DOWN_KEY;
                      if (!this._isTextElement && !hasActiveDropdown && wasPrintableChar) {
                        this.showDropdown();
                        if (!this.input.isFocussed) {
                          this.input.value += event.key.toLowerCase();
                        }
                      }
                      switch (keyCode) {
                        case A_KEY:
                          return this._onSelectKey(event, hasItems);
                        case ENTER_KEY:
                          return this._onEnterKey(event, activeItems, hasActiveDropdown);
                        case ESC_KEY:
                          return this._onEscapeKey(hasActiveDropdown);
                        case UP_KEY:
                        case PAGE_UP_KEY:
                        case DOWN_KEY:
                        case PAGE_DOWN_KEY:
                          return this._onDirectionKey(event, hasActiveDropdown);
                        case DELETE_KEY:
                        case BACK_KEY:
                          return this._onDeleteKey(event, activeItems, hasFocusedInput);
                        default:
                      }
                    };
                    Choices3.prototype._onKeyUp = function(_a) {
                      var target = _a.target, keyCode = _a.keyCode;
                      var value = this.input.value;
                      var activeItems = this._store.activeItems;
                      var canAddItem = this._canAddItem(activeItems, value);
                      var backKey = constants_1.KEY_CODES.BACK_KEY, deleteKey = constants_1.KEY_CODES.DELETE_KEY;
                      if (this._isTextElement) {
                        var canShowDropdownNotice = canAddItem.notice && value;
                        if (canShowDropdownNotice) {
                          var dropdownItem = this._getTemplate("notice", canAddItem.notice);
                          this.dropdown.element.innerHTML = dropdownItem.outerHTML;
                          this.showDropdown(true);
                        } else {
                          this.hideDropdown(true);
                        }
                      } else {
                        var wasRemovalKeyCode = keyCode === backKey || keyCode === deleteKey;
                        var userHasRemovedValue = wasRemovalKeyCode && target && !target.value;
                        var canReactivateChoices = !this._isTextElement && this._isSearching;
                        var canSearch = this._canSearch && canAddItem.response;
                        if (userHasRemovedValue && canReactivateChoices) {
                          this._isSearching = false;
                          this._store.dispatch((0, choices_1.activateChoices)(true));
                        } else if (canSearch) {
                          this._handleSearch(this.input.rawValue);
                        }
                      }
                      this._canSearch = this.config.searchEnabled;
                    };
                    Choices3.prototype._onSelectKey = function(event, hasItems) {
                      var ctrlKey = event.ctrlKey, metaKey = event.metaKey;
                      var hasCtrlDownKeyPressed = ctrlKey || metaKey;
                      if (hasCtrlDownKeyPressed && hasItems) {
                        this._canSearch = false;
                        var shouldHightlightAll = this.config.removeItems && !this.input.value && this.input.element === document.activeElement;
                        if (shouldHightlightAll) {
                          this.highlightAll();
                        }
                      }
                    };
                    Choices3.prototype._onEnterKey = function(event, activeItems, hasActiveDropdown) {
                      var target = event.target;
                      var enterKey = constants_1.KEY_CODES.ENTER_KEY;
                      var targetWasButton = target && target.hasAttribute("data-button");
                      if (this._isTextElement && target && target.value) {
                        var value = this.input.value;
                        var canAddItem = this._canAddItem(activeItems, value);
                        if (canAddItem.response) {
                          this.hideDropdown(true);
                          this._addItem({
                            value
                          });
                          this._triggerChange(value);
                          this.clearInput();
                        }
                      }
                      if (targetWasButton) {
                        this._handleButtonAction(activeItems, target);
                        event.preventDefault();
                      }
                      if (hasActiveDropdown) {
                        var highlightedChoice = this.dropdown.getChild(".".concat(this.config.classNames.highlightedState));
                        if (highlightedChoice) {
                          if (activeItems[0]) {
                            activeItems[0].keyCode = enterKey;
                          }
                          this._handleChoiceAction(activeItems, highlightedChoice);
                        }
                        event.preventDefault();
                      } else if (this._isSelectOneElement) {
                        this.showDropdown();
                        event.preventDefault();
                      }
                    };
                    Choices3.prototype._onEscapeKey = function(hasActiveDropdown) {
                      if (hasActiveDropdown) {
                        this.hideDropdown(true);
                        this.containerOuter.focus();
                      }
                    };
                    Choices3.prototype._onDirectionKey = function(event, hasActiveDropdown) {
                      var keyCode = event.keyCode, metaKey = event.metaKey;
                      var downKey = constants_1.KEY_CODES.DOWN_KEY, pageUpKey = constants_1.KEY_CODES.PAGE_UP_KEY, pageDownKey = constants_1.KEY_CODES.PAGE_DOWN_KEY;
                      if (hasActiveDropdown || this._isSelectOneElement) {
                        this.showDropdown();
                        this._canSearch = false;
                        var directionInt = keyCode === downKey || keyCode === pageDownKey ? 1 : -1;
                        var skipKey = metaKey || keyCode === pageDownKey || keyCode === pageUpKey;
                        var selectableChoiceIdentifier = "[data-choice-selectable]";
                        var nextEl = void 0;
                        if (skipKey) {
                          if (directionInt > 0) {
                            nextEl = this.dropdown.element.querySelector("".concat(selectableChoiceIdentifier, ":last-of-type"));
                          } else {
                            nextEl = this.dropdown.element.querySelector(selectableChoiceIdentifier);
                          }
                        } else {
                          var currentEl = this.dropdown.element.querySelector(".".concat(this.config.classNames.highlightedState));
                          if (currentEl) {
                            nextEl = (0, utils_1.getAdjacentEl)(currentEl, selectableChoiceIdentifier, directionInt);
                          } else {
                            nextEl = this.dropdown.element.querySelector(selectableChoiceIdentifier);
                          }
                        }
                        if (nextEl) {
                          if (!(0, utils_1.isScrolledIntoView)(nextEl, this.choiceList.element, directionInt)) {
                            this.choiceList.scrollToChildElement(nextEl, directionInt);
                          }
                          this._highlightChoice(nextEl);
                        }
                        event.preventDefault();
                      }
                    };
                    Choices3.prototype._onDeleteKey = function(event, activeItems, hasFocusedInput) {
                      var target = event.target;
                      if (!this._isSelectOneElement && !target.value && hasFocusedInput) {
                        this._handleBackspace(activeItems);
                        event.preventDefault();
                      }
                    };
                    Choices3.prototype._onTouchMove = function() {
                      if (this._wasTap) {
                        this._wasTap = false;
                      }
                    };
                    Choices3.prototype._onTouchEnd = function(event) {
                      var target = (event || event.touches[0]).target;
                      var touchWasWithinContainer = this._wasTap && this.containerOuter.element.contains(target);
                      if (touchWasWithinContainer) {
                        var containerWasExactTarget = target === this.containerOuter.element || target === this.containerInner.element;
                        if (containerWasExactTarget) {
                          if (this._isTextElement) {
                            this.input.focus();
                          } else if (this._isSelectMultipleElement) {
                            this.showDropdown();
                          }
                        }
                        event.stopPropagation();
                      }
                      this._wasTap = true;
                    };
                    Choices3.prototype._onMouseDown = function(event) {
                      var target = event.target;
                      if (!(target instanceof HTMLElement)) {
                        return;
                      }
                      if (IS_IE11 && this.choiceList.element.contains(target)) {
                        var firstChoice = this.choiceList.element.firstElementChild;
                        var isOnScrollbar = this._direction === "ltr" ? event.offsetX >= firstChoice.offsetWidth : event.offsetX < firstChoice.offsetLeft;
                        this._isScrollingOnIe = isOnScrollbar;
                      }
                      if (target === this.input.element) {
                        return;
                      }
                      var item = target.closest("[data-button],[data-item],[data-choice]");
                      if (item instanceof HTMLElement) {
                        var hasShiftKey = event.shiftKey;
                        var activeItems = this._store.activeItems;
                        var dataset = item.dataset;
                        if ("button" in dataset) {
                          this._handleButtonAction(activeItems, item);
                        } else if ("item" in dataset) {
                          this._handleItemAction(activeItems, item, hasShiftKey);
                        } else if ("choice" in dataset) {
                          this._handleChoiceAction(activeItems, item);
                        }
                      }
                      event.preventDefault();
                    };
                    Choices3.prototype._onMouseOver = function(_a) {
                      var target = _a.target;
                      if (target instanceof HTMLElement && "choice" in target.dataset) {
                        this._highlightChoice(target);
                      }
                    };
                    Choices3.prototype._onClick = function(_a) {
                      var target = _a.target;
                      var clickWasWithinContainer = this.containerOuter.element.contains(target);
                      if (clickWasWithinContainer) {
                        if (!this.dropdown.isActive && !this.containerOuter.isDisabled) {
                          if (this._isTextElement) {
                            if (document.activeElement !== this.input.element) {
                              this.input.focus();
                            }
                          } else {
                            this.showDropdown();
                            this.containerOuter.focus();
                          }
                        } else if (this._isSelectOneElement && target !== this.input.element && !this.dropdown.element.contains(target)) {
                          this.hideDropdown();
                        }
                      } else {
                        var hasHighlightedItems = this._store.highlightedActiveItems.length > 0;
                        if (hasHighlightedItems) {
                          this.unhighlightAll();
                        }
                        this.containerOuter.removeFocusState();
                        this.hideDropdown(true);
                      }
                    };
                    Choices3.prototype._onFocus = function(_a) {
                      var _b;
                      var _this = this;
                      var target = _a.target;
                      var focusWasWithinContainer = target && this.containerOuter.element.contains(target);
                      if (!focusWasWithinContainer) {
                        return;
                      }
                      var focusActions = (_b = {}, _b[constants_1.TEXT_TYPE] = function() {
                        if (target === _this.input.element) {
                          _this.containerOuter.addFocusState();
                        }
                      }, _b[constants_1.SELECT_ONE_TYPE] = function() {
                        _this.containerOuter.addFocusState();
                        if (target === _this.input.element) {
                          _this.showDropdown(true);
                        }
                      }, _b[constants_1.SELECT_MULTIPLE_TYPE] = function() {
                        if (target === _this.input.element) {
                          _this.showDropdown(true);
                          _this.containerOuter.addFocusState();
                        }
                      }, _b);
                      focusActions[this.passedElement.element.type]();
                    };
                    Choices3.prototype._onBlur = function(_a) {
                      var _b;
                      var _this = this;
                      var target = _a.target;
                      var blurWasWithinContainer = target && this.containerOuter.element.contains(target);
                      if (blurWasWithinContainer && !this._isScrollingOnIe) {
                        var activeItems = this._store.activeItems;
                        var hasHighlightedItems_1 = activeItems.some(function(item) {
                          return item.highlighted;
                        });
                        var blurActions = (_b = {}, _b[constants_1.TEXT_TYPE] = function() {
                          if (target === _this.input.element) {
                            _this.containerOuter.removeFocusState();
                            if (hasHighlightedItems_1) {
                              _this.unhighlightAll();
                            }
                            _this.hideDropdown(true);
                          }
                        }, _b[constants_1.SELECT_ONE_TYPE] = function() {
                          _this.containerOuter.removeFocusState();
                          if (target === _this.input.element || target === _this.containerOuter.element && !_this._canSearch) {
                            _this.hideDropdown(true);
                          }
                        }, _b[constants_1.SELECT_MULTIPLE_TYPE] = function() {
                          if (target === _this.input.element) {
                            _this.containerOuter.removeFocusState();
                            _this.hideDropdown(true);
                            if (hasHighlightedItems_1) {
                              _this.unhighlightAll();
                            }
                          }
                        }, _b);
                        blurActions[this.passedElement.element.type]();
                      } else {
                        this._isScrollingOnIe = false;
                        this.input.element.focus();
                      }
                    };
                    Choices3.prototype._onFormReset = function() {
                      this._store.dispatch((0, misc_1.resetTo)(this._initialState));
                    };
                    Choices3.prototype._highlightChoice = function(el) {
                      var _this = this;
                      if (el === void 0) {
                        el = null;
                      }
                      var choices = Array.from(this.dropdown.element.querySelectorAll("[data-choice-selectable]"));
                      if (!choices.length) {
                        return;
                      }
                      var passedEl = el;
                      var highlightedChoices = Array.from(this.dropdown.element.querySelectorAll(".".concat(this.config.classNames.highlightedState)));
                      highlightedChoices.forEach(function(choice) {
                        choice.classList.remove(_this.config.classNames.highlightedState);
                        choice.setAttribute("aria-selected", "false");
                      });
                      if (passedEl) {
                        this._highlightPosition = choices.indexOf(passedEl);
                      } else {
                        if (choices.length > this._highlightPosition) {
                          passedEl = choices[this._highlightPosition];
                        } else {
                          passedEl = choices[choices.length - 1];
                        }
                        if (!passedEl) {
                          passedEl = choices[0];
                        }
                      }
                      passedEl.classList.add(this.config.classNames.highlightedState);
                      passedEl.setAttribute("aria-selected", "true");
                      this.passedElement.triggerEvent(constants_1.EVENTS.highlightChoice, {
                        el: passedEl
                      });
                      if (this.dropdown.isActive) {
                        this.input.setActiveDescendant(passedEl.id);
                        this.containerOuter.setActiveDescendant(passedEl.id);
                      }
                    };
                    Choices3.prototype._addItem = function(_a) {
                      var value = _a.value, _b = _a.label, label = _b === void 0 ? null : _b, _c = _a.choiceId, choiceId = _c === void 0 ? -1 : _c, _d = _a.groupId, groupId = _d === void 0 ? -1 : _d, _e = _a.customProperties, customProperties = _e === void 0 ? {} : _e, _f = _a.placeholder, placeholder = _f === void 0 ? false : _f, _g = _a.keyCode, keyCode = _g === void 0 ? -1 : _g;
                      var passedValue = typeof value === "string" ? value.trim() : value;
                      var items = this._store.items;
                      var passedLabel = label || passedValue;
                      var passedOptionId = choiceId || -1;
                      var group = groupId >= 0 ? this._store.getGroupById(groupId) : null;
                      var id = items ? items.length + 1 : 1;
                      if (this.config.prependValue) {
                        passedValue = this.config.prependValue + passedValue.toString();
                      }
                      if (this.config.appendValue) {
                        passedValue += this.config.appendValue.toString();
                      }
                      this._store.dispatch((0, items_1.addItem)({
                        value: passedValue,
                        label: passedLabel,
                        id,
                        choiceId: passedOptionId,
                        groupId,
                        customProperties,
                        placeholder,
                        keyCode
                      }));
                      if (this._isSelectOneElement) {
                        this.removeActiveItems(id);
                      }
                      this.passedElement.triggerEvent(constants_1.EVENTS.addItem, {
                        id,
                        value: passedValue,
                        label: passedLabel,
                        customProperties,
                        groupValue: group && group.value ? group.value : null,
                        keyCode
                      });
                    };
                    Choices3.prototype._removeItem = function(item) {
                      var id = item.id, value = item.value, label = item.label, customProperties = item.customProperties, choiceId = item.choiceId, groupId = item.groupId;
                      var group = groupId && groupId >= 0 ? this._store.getGroupById(groupId) : null;
                      if (!id || !choiceId) {
                        return;
                      }
                      this._store.dispatch((0, items_1.removeItem)(id, choiceId));
                      this.passedElement.triggerEvent(constants_1.EVENTS.removeItem, {
                        id,
                        value,
                        label,
                        customProperties,
                        groupValue: group && group.value ? group.value : null
                      });
                    };
                    Choices3.prototype._addChoice = function(_a) {
                      var value = _a.value, _b = _a.label, label = _b === void 0 ? null : _b, _c = _a.isSelected, isSelected = _c === void 0 ? false : _c, _d = _a.isDisabled, isDisabled = _d === void 0 ? false : _d, _e = _a.groupId, groupId = _e === void 0 ? -1 : _e, _f = _a.customProperties, customProperties = _f === void 0 ? {} : _f, _g = _a.placeholder, placeholder = _g === void 0 ? false : _g, _h = _a.keyCode, keyCode = _h === void 0 ? -1 : _h;
                      if (typeof value === "undefined" || value === null) {
                        return;
                      }
                      var choices = this._store.choices;
                      var choiceLabel = label || value;
                      var choiceId = choices ? choices.length + 1 : 1;
                      var choiceElementId = "".concat(this._baseId, "-").concat(this._idNames.itemChoice, "-").concat(choiceId);
                      this._store.dispatch((0, choices_1.addChoice)({
                        id: choiceId,
                        groupId,
                        elementId: choiceElementId,
                        value,
                        label: choiceLabel,
                        disabled: isDisabled,
                        customProperties,
                        placeholder,
                        keyCode
                      }));
                      if (isSelected) {
                        this._addItem({
                          value,
                          label: choiceLabel,
                          choiceId,
                          customProperties,
                          placeholder,
                          keyCode
                        });
                      }
                    };
                    Choices3.prototype._addGroup = function(_a) {
                      var _this = this;
                      var group = _a.group, id = _a.id, _b = _a.valueKey, valueKey = _b === void 0 ? "value" : _b, _c = _a.labelKey, labelKey = _c === void 0 ? "label" : _c;
                      var groupChoices = (0, utils_1.isType)("Object", group) ? group.choices : Array.from(group.getElementsByTagName("OPTION"));
                      var groupId = id || Math.floor((/* @__PURE__ */ new Date()).valueOf() * Math.random());
                      var isDisabled = group.disabled ? group.disabled : false;
                      if (groupChoices) {
                        this._store.dispatch((0, groups_1.addGroup)({
                          value: group.label,
                          id: groupId,
                          active: true,
                          disabled: isDisabled
                        }));
                        var addGroupChoices = function(choice) {
                          var isOptDisabled = choice.disabled || choice.parentNode && choice.parentNode.disabled;
                          _this._addChoice({
                            value: choice[valueKey],
                            label: (0, utils_1.isType)("Object", choice) ? choice[labelKey] : choice.innerHTML,
                            isSelected: choice.selected,
                            isDisabled: isOptDisabled,
                            groupId,
                            customProperties: choice.customProperties,
                            placeholder: choice.placeholder
                          });
                        };
                        groupChoices.forEach(addGroupChoices);
                      } else {
                        this._store.dispatch((0, groups_1.addGroup)({
                          value: group.label,
                          id: group.id,
                          active: false,
                          disabled: group.disabled
                        }));
                      }
                    };
                    Choices3.prototype._getTemplate = function(template) {
                      var _a;
                      var args = [];
                      for (var _i = 1; _i < arguments.length; _i++) {
                        args[_i - 1] = arguments[_i];
                      }
                      return (_a = this._templates[template]).call.apply(_a, __spreadArray([this, this.config], args, false));
                    };
                    Choices3.prototype._createTemplates = function() {
                      var callbackOnCreateTemplates = this.config.callbackOnCreateTemplates;
                      var userTemplates = {};
                      if (callbackOnCreateTemplates && typeof callbackOnCreateTemplates === "function") {
                        userTemplates = callbackOnCreateTemplates.call(this, utils_1.strToEl);
                      }
                      this._templates = (0, deepmerge_1.default)(templates_1.default, userTemplates);
                    };
                    Choices3.prototype._createElements = function() {
                      this.containerOuter = new components_1.Container({
                        element: this._getTemplate("containerOuter", this._direction, this._isSelectElement, this._isSelectOneElement, this.config.searchEnabled, this.passedElement.element.type, this.config.labelId),
                        classNames: this.config.classNames,
                        type: this.passedElement.element.type,
                        position: this.config.position
                      });
                      this.containerInner = new components_1.Container({
                        element: this._getTemplate("containerInner"),
                        classNames: this.config.classNames,
                        type: this.passedElement.element.type,
                        position: this.config.position
                      });
                      this.input = new components_1.Input({
                        element: this._getTemplate("input", this._placeholderValue),
                        classNames: this.config.classNames,
                        type: this.passedElement.element.type,
                        preventPaste: !this.config.paste
                      });
                      this.choiceList = new components_1.List({
                        element: this._getTemplate("choiceList", this._isSelectOneElement)
                      });
                      this.itemList = new components_1.List({
                        element: this._getTemplate("itemList", this._isSelectOneElement)
                      });
                      this.dropdown = new components_1.Dropdown({
                        element: this._getTemplate("dropdown"),
                        classNames: this.config.classNames,
                        type: this.passedElement.element.type
                      });
                    };
                    Choices3.prototype._createStructure = function() {
                      this.passedElement.conceal();
                      this.containerInner.wrap(this.passedElement.element);
                      this.containerOuter.wrap(this.containerInner.element);
                      if (this._isSelectOneElement) {
                        this.input.placeholder = this.config.searchPlaceholderValue || "";
                      } else if (this._placeholderValue) {
                        this.input.placeholder = this._placeholderValue;
                        this.input.setWidth();
                      }
                      this.containerOuter.element.appendChild(this.containerInner.element);
                      this.containerOuter.element.appendChild(this.dropdown.element);
                      this.containerInner.element.appendChild(this.itemList.element);
                      if (!this._isTextElement) {
                        this.dropdown.element.appendChild(this.choiceList.element);
                      }
                      if (!this._isSelectOneElement) {
                        this.containerInner.element.appendChild(this.input.element);
                      } else if (this.config.searchEnabled) {
                        this.dropdown.element.insertBefore(this.input.element, this.dropdown.element.firstChild);
                      }
                      if (this._isSelectElement) {
                        this._highlightPosition = 0;
                        this._isSearching = false;
                        this._startLoading();
                        if (this._presetGroups.length) {
                          this._addPredefinedGroups(this._presetGroups);
                        } else {
                          this._addPredefinedChoices(this._presetChoices);
                        }
                        this._stopLoading();
                      }
                      if (this._isTextElement) {
                        this._addPredefinedItems(this._presetItems);
                      }
                    };
                    Choices3.prototype._addPredefinedGroups = function(groups) {
                      var _this = this;
                      var placeholderChoice = this.passedElement.placeholderOption;
                      if (placeholderChoice && placeholderChoice.parentNode && placeholderChoice.parentNode.tagName === "SELECT") {
                        this._addChoice({
                          value: placeholderChoice.value,
                          label: placeholderChoice.innerHTML,
                          isSelected: placeholderChoice.selected,
                          isDisabled: placeholderChoice.disabled,
                          placeholder: true
                        });
                      }
                      groups.forEach(function(group) {
                        return _this._addGroup({
                          group,
                          id: group.id || null
                        });
                      });
                    };
                    Choices3.prototype._addPredefinedChoices = function(choices) {
                      var _this = this;
                      if (this.config.shouldSort) {
                        choices.sort(this.config.sorter);
                      }
                      var hasSelectedChoice = choices.some(function(choice) {
                        return choice.selected;
                      });
                      var firstEnabledChoiceIndex = choices.findIndex(function(choice) {
                        return choice.disabled === void 0 || !choice.disabled;
                      });
                      choices.forEach(function(choice, index) {
                        var _a = choice.value, value = _a === void 0 ? "" : _a, label = choice.label, customProperties = choice.customProperties, placeholder = choice.placeholder;
                        if (_this._isSelectElement) {
                          if (choice.choices) {
                            _this._addGroup({
                              group: choice,
                              id: choice.id || null
                            });
                          } else {
                            var shouldPreselect = _this._isSelectOneElement && !hasSelectedChoice && index === firstEnabledChoiceIndex;
                            var isSelected = shouldPreselect ? true : choice.selected;
                            var isDisabled = choice.disabled;
                            _this._addChoice({
                              value,
                              label,
                              isSelected: !!isSelected,
                              isDisabled: !!isDisabled,
                              placeholder: !!placeholder,
                              customProperties
                            });
                          }
                        } else {
                          _this._addChoice({
                            value,
                            label,
                            isSelected: !!choice.selected,
                            isDisabled: !!choice.disabled,
                            placeholder: !!choice.placeholder,
                            customProperties
                          });
                        }
                      });
                    };
                    Choices3.prototype._addPredefinedItems = function(items) {
                      var _this = this;
                      items.forEach(function(item) {
                        if (typeof item === "object" && item.value) {
                          _this._addItem({
                            value: item.value,
                            label: item.label,
                            choiceId: item.id,
                            customProperties: item.customProperties,
                            placeholder: item.placeholder
                          });
                        }
                        if (typeof item === "string") {
                          _this._addItem({
                            value: item
                          });
                        }
                      });
                    };
                    Choices3.prototype._setChoiceOrItem = function(item) {
                      var _this = this;
                      var itemType = (0, utils_1.getType)(item).toLowerCase();
                      var handleType = {
                        object: function() {
                          if (!item.value) {
                            return;
                          }
                          if (!_this._isTextElement) {
                            _this._addChoice({
                              value: item.value,
                              label: item.label,
                              isSelected: true,
                              isDisabled: false,
                              customProperties: item.customProperties,
                              placeholder: item.placeholder
                            });
                          } else {
                            _this._addItem({
                              value: item.value,
                              label: item.label,
                              choiceId: item.id,
                              customProperties: item.customProperties,
                              placeholder: item.placeholder
                            });
                          }
                        },
                        string: function() {
                          if (!_this._isTextElement) {
                            _this._addChoice({
                              value: item,
                              label: item,
                              isSelected: true,
                              isDisabled: false
                            });
                          } else {
                            _this._addItem({
                              value: item
                            });
                          }
                        }
                      };
                      handleType[itemType]();
                    };
                    Choices3.prototype._findAndSelectChoiceByValue = function(value) {
                      var _this = this;
                      var choices = this._store.choices;
                      var foundChoice = choices.find(function(choice) {
                        return _this.config.valueComparer(choice.value, value);
                      });
                      if (foundChoice && !foundChoice.selected) {
                        this._addItem({
                          value: foundChoice.value,
                          label: foundChoice.label,
                          choiceId: foundChoice.id,
                          groupId: foundChoice.groupId,
                          customProperties: foundChoice.customProperties,
                          placeholder: foundChoice.placeholder,
                          keyCode: foundChoice.keyCode
                        });
                      }
                    };
                    Choices3.prototype._generatePlaceholderValue = function() {
                      if (this._isSelectElement && this.passedElement.placeholderOption) {
                        var placeholderOption = this.passedElement.placeholderOption;
                        return placeholderOption ? placeholderOption.text : null;
                      }
                      var _a = this.config, placeholder = _a.placeholder, placeholderValue = _a.placeholderValue;
                      var dataset = this.passedElement.element.dataset;
                      if (placeholder) {
                        if (placeholderValue) {
                          return placeholderValue;
                        }
                        if (dataset.placeholder) {
                          return dataset.placeholder;
                        }
                      }
                      return null;
                    };
                    return Choices3;
                  }()
                );
                exports2["default"] = Choices2;
              }
            ),
            /***/
            613: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var utils_1 = __webpack_require__2(799);
                var constants_1 = __webpack_require__2(883);
                var Container = (
                  /** @class */
                  function() {
                    function Container2(_a) {
                      var element = _a.element, type = _a.type, classNames = _a.classNames, position = _a.position;
                      this.element = element;
                      this.classNames = classNames;
                      this.type = type;
                      this.position = position;
                      this.isOpen = false;
                      this.isFlipped = false;
                      this.isFocussed = false;
                      this.isDisabled = false;
                      this.isLoading = false;
                      this._onFocus = this._onFocus.bind(this);
                      this._onBlur = this._onBlur.bind(this);
                    }
                    Container2.prototype.addEventListeners = function() {
                      this.element.addEventListener("focus", this._onFocus);
                      this.element.addEventListener("blur", this._onBlur);
                    };
                    Container2.prototype.removeEventListeners = function() {
                      this.element.removeEventListener("focus", this._onFocus);
                      this.element.removeEventListener("blur", this._onBlur);
                    };
                    Container2.prototype.shouldFlip = function(dropdownPos) {
                      if (typeof dropdownPos !== "number") {
                        return false;
                      }
                      var shouldFlip = false;
                      if (this.position === "auto") {
                        shouldFlip = !window.matchMedia("(min-height: ".concat(dropdownPos + 1, "px)")).matches;
                      } else if (this.position === "top") {
                        shouldFlip = true;
                      }
                      return shouldFlip;
                    };
                    Container2.prototype.setActiveDescendant = function(activeDescendantID) {
                      this.element.setAttribute("aria-activedescendant", activeDescendantID);
                    };
                    Container2.prototype.removeActiveDescendant = function() {
                      this.element.removeAttribute("aria-activedescendant");
                    };
                    Container2.prototype.open = function(dropdownPos) {
                      this.element.classList.add(this.classNames.openState);
                      this.element.setAttribute("aria-expanded", "true");
                      this.isOpen = true;
                      if (this.shouldFlip(dropdownPos)) {
                        this.element.classList.add(this.classNames.flippedState);
                        this.isFlipped = true;
                      }
                    };
                    Container2.prototype.close = function() {
                      this.element.classList.remove(this.classNames.openState);
                      this.element.setAttribute("aria-expanded", "false");
                      this.removeActiveDescendant();
                      this.isOpen = false;
                      if (this.isFlipped) {
                        this.element.classList.remove(this.classNames.flippedState);
                        this.isFlipped = false;
                      }
                    };
                    Container2.prototype.focus = function() {
                      if (!this.isFocussed) {
                        this.element.focus();
                      }
                    };
                    Container2.prototype.addFocusState = function() {
                      this.element.classList.add(this.classNames.focusState);
                    };
                    Container2.prototype.removeFocusState = function() {
                      this.element.classList.remove(this.classNames.focusState);
                    };
                    Container2.prototype.enable = function() {
                      this.element.classList.remove(this.classNames.disabledState);
                      this.element.removeAttribute("aria-disabled");
                      if (this.type === constants_1.SELECT_ONE_TYPE) {
                        this.element.setAttribute("tabindex", "0");
                      }
                      this.isDisabled = false;
                    };
                    Container2.prototype.disable = function() {
                      this.element.classList.add(this.classNames.disabledState);
                      this.element.setAttribute("aria-disabled", "true");
                      if (this.type === constants_1.SELECT_ONE_TYPE) {
                        this.element.setAttribute("tabindex", "-1");
                      }
                      this.isDisabled = true;
                    };
                    Container2.prototype.wrap = function(element) {
                      (0, utils_1.wrap)(element, this.element);
                    };
                    Container2.prototype.unwrap = function(element) {
                      if (this.element.parentNode) {
                        this.element.parentNode.insertBefore(element, this.element);
                        this.element.parentNode.removeChild(this.element);
                      }
                    };
                    Container2.prototype.addLoadingState = function() {
                      this.element.classList.add(this.classNames.loadingState);
                      this.element.setAttribute("aria-busy", "true");
                      this.isLoading = true;
                    };
                    Container2.prototype.removeLoadingState = function() {
                      this.element.classList.remove(this.classNames.loadingState);
                      this.element.removeAttribute("aria-busy");
                      this.isLoading = false;
                    };
                    Container2.prototype._onFocus = function() {
                      this.isFocussed = true;
                    };
                    Container2.prototype._onBlur = function() {
                      this.isFocussed = false;
                    };
                    return Container2;
                  }()
                );
                exports2["default"] = Container;
              }
            ),
            /***/
            217: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var Dropdown = (
                  /** @class */
                  function() {
                    function Dropdown2(_a) {
                      var element = _a.element, type = _a.type, classNames = _a.classNames;
                      this.element = element;
                      this.classNames = classNames;
                      this.type = type;
                      this.isActive = false;
                    }
                    Object.defineProperty(Dropdown2.prototype, "distanceFromTopWindow", {
                      /**
                       * Bottom position of dropdown in viewport coordinates
                       */
                      get: function() {
                        return this.element.getBoundingClientRect().bottom;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Dropdown2.prototype.getChild = function(selector) {
                      return this.element.querySelector(selector);
                    };
                    Dropdown2.prototype.show = function() {
                      this.element.classList.add(this.classNames.activeState);
                      this.element.setAttribute("aria-expanded", "true");
                      this.isActive = true;
                      return this;
                    };
                    Dropdown2.prototype.hide = function() {
                      this.element.classList.remove(this.classNames.activeState);
                      this.element.setAttribute("aria-expanded", "false");
                      this.isActive = false;
                      return this;
                    };
                    return Dropdown2;
                  }()
                );
                exports2["default"] = Dropdown;
              }
            ),
            /***/
            520: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                var __importDefault = this && this.__importDefault || function(mod) {
                  return mod && mod.__esModule ? mod : {
                    "default": mod
                  };
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.WrappedSelect = exports2.WrappedInput = exports2.List = exports2.Input = exports2.Container = exports2.Dropdown = void 0;
                var dropdown_1 = __importDefault(__webpack_require__2(217));
                exports2.Dropdown = dropdown_1.default;
                var container_1 = __importDefault(__webpack_require__2(613));
                exports2.Container = container_1.default;
                var input_1 = __importDefault(__webpack_require__2(11));
                exports2.Input = input_1.default;
                var list_1 = __importDefault(__webpack_require__2(624));
                exports2.List = list_1.default;
                var wrapped_input_1 = __importDefault(__webpack_require__2(541));
                exports2.WrappedInput = wrapped_input_1.default;
                var wrapped_select_1 = __importDefault(__webpack_require__2(982));
                exports2.WrappedSelect = wrapped_select_1.default;
              }
            ),
            /***/
            11: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var utils_1 = __webpack_require__2(799);
                var constants_1 = __webpack_require__2(883);
                var Input = (
                  /** @class */
                  function() {
                    function Input2(_a) {
                      var element = _a.element, type = _a.type, classNames = _a.classNames, preventPaste = _a.preventPaste;
                      this.element = element;
                      this.type = type;
                      this.classNames = classNames;
                      this.preventPaste = preventPaste;
                      this.isFocussed = this.element.isEqualNode(document.activeElement);
                      this.isDisabled = element.disabled;
                      this._onPaste = this._onPaste.bind(this);
                      this._onInput = this._onInput.bind(this);
                      this._onFocus = this._onFocus.bind(this);
                      this._onBlur = this._onBlur.bind(this);
                    }
                    Object.defineProperty(Input2.prototype, "placeholder", {
                      set: function(placeholder) {
                        this.element.placeholder = placeholder;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Input2.prototype, "value", {
                      get: function() {
                        return (0, utils_1.sanitise)(this.element.value);
                      },
                      set: function(value) {
                        this.element.value = value;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Input2.prototype, "rawValue", {
                      get: function() {
                        return this.element.value;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Input2.prototype.addEventListeners = function() {
                      this.element.addEventListener("paste", this._onPaste);
                      this.element.addEventListener("input", this._onInput, {
                        passive: true
                      });
                      this.element.addEventListener("focus", this._onFocus, {
                        passive: true
                      });
                      this.element.addEventListener("blur", this._onBlur, {
                        passive: true
                      });
                    };
                    Input2.prototype.removeEventListeners = function() {
                      this.element.removeEventListener("input", this._onInput);
                      this.element.removeEventListener("paste", this._onPaste);
                      this.element.removeEventListener("focus", this._onFocus);
                      this.element.removeEventListener("blur", this._onBlur);
                    };
                    Input2.prototype.enable = function() {
                      this.element.removeAttribute("disabled");
                      this.isDisabled = false;
                    };
                    Input2.prototype.disable = function() {
                      this.element.setAttribute("disabled", "");
                      this.isDisabled = true;
                    };
                    Input2.prototype.focus = function() {
                      if (!this.isFocussed) {
                        this.element.focus();
                      }
                    };
                    Input2.prototype.blur = function() {
                      if (this.isFocussed) {
                        this.element.blur();
                      }
                    };
                    Input2.prototype.clear = function(setWidth) {
                      if (setWidth === void 0) {
                        setWidth = true;
                      }
                      if (this.element.value) {
                        this.element.value = "";
                      }
                      if (setWidth) {
                        this.setWidth();
                      }
                      return this;
                    };
                    Input2.prototype.setWidth = function() {
                      var _a = this.element, style = _a.style, value = _a.value, placeholder = _a.placeholder;
                      style.minWidth = "".concat(placeholder.length + 1, "ch");
                      style.width = "".concat(value.length + 1, "ch");
                    };
                    Input2.prototype.setActiveDescendant = function(activeDescendantID) {
                      this.element.setAttribute("aria-activedescendant", activeDescendantID);
                    };
                    Input2.prototype.removeActiveDescendant = function() {
                      this.element.removeAttribute("aria-activedescendant");
                    };
                    Input2.prototype._onInput = function() {
                      if (this.type !== constants_1.SELECT_ONE_TYPE) {
                        this.setWidth();
                      }
                    };
                    Input2.prototype._onPaste = function(event) {
                      if (this.preventPaste) {
                        event.preventDefault();
                      }
                    };
                    Input2.prototype._onFocus = function() {
                      this.isFocussed = true;
                    };
                    Input2.prototype._onBlur = function() {
                      this.isFocussed = false;
                    };
                    return Input2;
                  }()
                );
                exports2["default"] = Input;
              }
            ),
            /***/
            624: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var constants_1 = __webpack_require__2(883);
                var List = (
                  /** @class */
                  function() {
                    function List2(_a) {
                      var element = _a.element;
                      this.element = element;
                      this.scrollPos = this.element.scrollTop;
                      this.height = this.element.offsetHeight;
                    }
                    List2.prototype.clear = function() {
                      this.element.innerHTML = "";
                    };
                    List2.prototype.append = function(node) {
                      this.element.appendChild(node);
                    };
                    List2.prototype.getChild = function(selector) {
                      return this.element.querySelector(selector);
                    };
                    List2.prototype.hasChildren = function() {
                      return this.element.hasChildNodes();
                    };
                    List2.prototype.scrollToTop = function() {
                      this.element.scrollTop = 0;
                    };
                    List2.prototype.scrollToChildElement = function(element, direction) {
                      var _this = this;
                      if (!element) {
                        return;
                      }
                      var listHeight = this.element.offsetHeight;
                      var listScrollPosition = this.element.scrollTop + listHeight;
                      var elementHeight = element.offsetHeight;
                      var elementPos = element.offsetTop + elementHeight;
                      var destination = direction > 0 ? this.element.scrollTop + elementPos - listScrollPosition : element.offsetTop;
                      requestAnimationFrame(function() {
                        _this._animateScroll(destination, direction);
                      });
                    };
                    List2.prototype._scrollDown = function(scrollPos, strength, destination) {
                      var easing = (destination - scrollPos) / strength;
                      var distance = easing > 1 ? easing : 1;
                      this.element.scrollTop = scrollPos + distance;
                    };
                    List2.prototype._scrollUp = function(scrollPos, strength, destination) {
                      var easing = (scrollPos - destination) / strength;
                      var distance = easing > 1 ? easing : 1;
                      this.element.scrollTop = scrollPos - distance;
                    };
                    List2.prototype._animateScroll = function(destination, direction) {
                      var _this = this;
                      var strength = constants_1.SCROLLING_SPEED;
                      var choiceListScrollTop = this.element.scrollTop;
                      var continueAnimation = false;
                      if (direction > 0) {
                        this._scrollDown(choiceListScrollTop, strength, destination);
                        if (choiceListScrollTop < destination) {
                          continueAnimation = true;
                        }
                      } else {
                        this._scrollUp(choiceListScrollTop, strength, destination);
                        if (choiceListScrollTop > destination) {
                          continueAnimation = true;
                        }
                      }
                      if (continueAnimation) {
                        requestAnimationFrame(function() {
                          _this._animateScroll(destination, direction);
                        });
                      }
                    };
                    return List2;
                  }()
                );
                exports2["default"] = List;
              }
            ),
            /***/
            730: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var utils_1 = __webpack_require__2(799);
                var WrappedElement = (
                  /** @class */
                  function() {
                    function WrappedElement2(_a) {
                      var element = _a.element, classNames = _a.classNames;
                      this.element = element;
                      this.classNames = classNames;
                      if (!(element instanceof HTMLInputElement) && !(element instanceof HTMLSelectElement)) {
                        throw new TypeError("Invalid element passed");
                      }
                      this.isDisabled = false;
                    }
                    Object.defineProperty(WrappedElement2.prototype, "isActive", {
                      get: function() {
                        return this.element.dataset.choice === "active";
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(WrappedElement2.prototype, "dir", {
                      get: function() {
                        return this.element.dir;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(WrappedElement2.prototype, "value", {
                      get: function() {
                        return this.element.value;
                      },
                      set: function(value) {
                        this.element.value = value;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    WrappedElement2.prototype.conceal = function() {
                      this.element.classList.add(this.classNames.input);
                      this.element.hidden = true;
                      this.element.tabIndex = -1;
                      var origStyle = this.element.getAttribute("style");
                      if (origStyle) {
                        this.element.setAttribute("data-choice-orig-style", origStyle);
                      }
                      this.element.setAttribute("data-choice", "active");
                    };
                    WrappedElement2.prototype.reveal = function() {
                      this.element.classList.remove(this.classNames.input);
                      this.element.hidden = false;
                      this.element.removeAttribute("tabindex");
                      var origStyle = this.element.getAttribute("data-choice-orig-style");
                      if (origStyle) {
                        this.element.removeAttribute("data-choice-orig-style");
                        this.element.setAttribute("style", origStyle);
                      } else {
                        this.element.removeAttribute("style");
                      }
                      this.element.removeAttribute("data-choice");
                      this.element.value = this.element.value;
                    };
                    WrappedElement2.prototype.enable = function() {
                      this.element.removeAttribute("disabled");
                      this.element.disabled = false;
                      this.isDisabled = false;
                    };
                    WrappedElement2.prototype.disable = function() {
                      this.element.setAttribute("disabled", "");
                      this.element.disabled = true;
                      this.isDisabled = true;
                    };
                    WrappedElement2.prototype.triggerEvent = function(eventType, data) {
                      (0, utils_1.dispatchEvent)(this.element, eventType, data);
                    };
                    return WrappedElement2;
                  }()
                );
                exports2["default"] = WrappedElement;
              }
            ),
            /***/
            541: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                var __extends = this && this.__extends || function() {
                  var extendStatics = function(d, b) {
                    extendStatics = Object.setPrototypeOf || {
                      __proto__: []
                    } instanceof Array && function(d2, b2) {
                      d2.__proto__ = b2;
                    } || function(d2, b2) {
                      for (var p in b2)
                        if (Object.prototype.hasOwnProperty.call(b2, p))
                          d2[p] = b2[p];
                    };
                    return extendStatics(d, b);
                  };
                  return function(d, b) {
                    if (typeof b !== "function" && b !== null)
                      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
                    extendStatics(d, b);
                    function __() {
                      this.constructor = d;
                    }
                    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                  };
                }();
                var __importDefault = this && this.__importDefault || function(mod) {
                  return mod && mod.__esModule ? mod : {
                    "default": mod
                  };
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var wrapped_element_1 = __importDefault(__webpack_require__2(730));
                var WrappedInput = (
                  /** @class */
                  function(_super) {
                    __extends(WrappedInput2, _super);
                    function WrappedInput2(_a) {
                      var element = _a.element, classNames = _a.classNames, delimiter = _a.delimiter;
                      var _this = _super.call(this, {
                        element,
                        classNames
                      }) || this;
                      _this.delimiter = delimiter;
                      return _this;
                    }
                    Object.defineProperty(WrappedInput2.prototype, "value", {
                      get: function() {
                        return this.element.value;
                      },
                      set: function(value) {
                        this.element.setAttribute("value", value);
                        this.element.value = value;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    return WrappedInput2;
                  }(wrapped_element_1.default)
                );
                exports2["default"] = WrappedInput;
              }
            ),
            /***/
            982: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                var __extends = this && this.__extends || function() {
                  var extendStatics = function(d, b) {
                    extendStatics = Object.setPrototypeOf || {
                      __proto__: []
                    } instanceof Array && function(d2, b2) {
                      d2.__proto__ = b2;
                    } || function(d2, b2) {
                      for (var p in b2)
                        if (Object.prototype.hasOwnProperty.call(b2, p))
                          d2[p] = b2[p];
                    };
                    return extendStatics(d, b);
                  };
                  return function(d, b) {
                    if (typeof b !== "function" && b !== null)
                      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
                    extendStatics(d, b);
                    function __() {
                      this.constructor = d;
                    }
                    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                  };
                }();
                var __importDefault = this && this.__importDefault || function(mod) {
                  return mod && mod.__esModule ? mod : {
                    "default": mod
                  };
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var wrapped_element_1 = __importDefault(__webpack_require__2(730));
                var WrappedSelect = (
                  /** @class */
                  function(_super) {
                    __extends(WrappedSelect2, _super);
                    function WrappedSelect2(_a) {
                      var element = _a.element, classNames = _a.classNames, template = _a.template;
                      var _this = _super.call(this, {
                        element,
                        classNames
                      }) || this;
                      _this.template = template;
                      return _this;
                    }
                    Object.defineProperty(WrappedSelect2.prototype, "placeholderOption", {
                      get: function() {
                        return this.element.querySelector('option[value=""]') || // Backward compatibility layer for the non-standard placeholder attribute supported in older versions.
                        this.element.querySelector("option[placeholder]");
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(WrappedSelect2.prototype, "optionGroups", {
                      get: function() {
                        return Array.from(this.element.getElementsByTagName("OPTGROUP"));
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(WrappedSelect2.prototype, "options", {
                      get: function() {
                        return Array.from(this.element.options);
                      },
                      set: function(options) {
                        var _this = this;
                        var fragment = document.createDocumentFragment();
                        var addOptionToFragment = function(data) {
                          var option = _this.template(data);
                          fragment.appendChild(option);
                        };
                        options.forEach(function(optionData) {
                          return addOptionToFragment(optionData);
                        });
                        this.appendDocFragment(fragment);
                      },
                      enumerable: false,
                      configurable: true
                    });
                    WrappedSelect2.prototype.appendDocFragment = function(fragment) {
                      this.element.innerHTML = "";
                      this.element.appendChild(fragment);
                    };
                    return WrappedSelect2;
                  }(wrapped_element_1.default)
                );
                exports2["default"] = WrappedSelect;
              }
            ),
            /***/
            883: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.SCROLLING_SPEED = exports2.SELECT_MULTIPLE_TYPE = exports2.SELECT_ONE_TYPE = exports2.TEXT_TYPE = exports2.KEY_CODES = exports2.ACTION_TYPES = exports2.EVENTS = void 0;
                exports2.EVENTS = {
                  showDropdown: "showDropdown",
                  hideDropdown: "hideDropdown",
                  change: "change",
                  choice: "choice",
                  search: "search",
                  addItem: "addItem",
                  removeItem: "removeItem",
                  highlightItem: "highlightItem",
                  highlightChoice: "highlightChoice",
                  unhighlightItem: "unhighlightItem"
                };
                exports2.ACTION_TYPES = {
                  ADD_CHOICE: "ADD_CHOICE",
                  FILTER_CHOICES: "FILTER_CHOICES",
                  ACTIVATE_CHOICES: "ACTIVATE_CHOICES",
                  CLEAR_CHOICES: "CLEAR_CHOICES",
                  ADD_GROUP: "ADD_GROUP",
                  ADD_ITEM: "ADD_ITEM",
                  REMOVE_ITEM: "REMOVE_ITEM",
                  HIGHLIGHT_ITEM: "HIGHLIGHT_ITEM",
                  CLEAR_ALL: "CLEAR_ALL",
                  RESET_TO: "RESET_TO",
                  SET_IS_LOADING: "SET_IS_LOADING"
                };
                exports2.KEY_CODES = {
                  BACK_KEY: 46,
                  DELETE_KEY: 8,
                  ENTER_KEY: 13,
                  A_KEY: 65,
                  ESC_KEY: 27,
                  UP_KEY: 38,
                  DOWN_KEY: 40,
                  PAGE_UP_KEY: 33,
                  PAGE_DOWN_KEY: 34
                };
                exports2.TEXT_TYPE = "text";
                exports2.SELECT_ONE_TYPE = "select-one";
                exports2.SELECT_MULTIPLE_TYPE = "select-multiple";
                exports2.SCROLLING_SPEED = 4;
              }
            ),
            /***/
            789: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.DEFAULT_CONFIG = exports2.DEFAULT_CLASSNAMES = void 0;
                var utils_1 = __webpack_require__2(799);
                exports2.DEFAULT_CLASSNAMES = {
                  containerOuter: "choices",
                  containerInner: "choices__inner",
                  input: "choices__input",
                  inputCloned: "choices__input--cloned",
                  list: "choices__list",
                  listItems: "choices__list--multiple",
                  listSingle: "choices__list--single",
                  listDropdown: "choices__list--dropdown",
                  item: "choices__item",
                  itemSelectable: "choices__item--selectable",
                  itemDisabled: "choices__item--disabled",
                  itemChoice: "choices__item--choice",
                  placeholder: "choices__placeholder",
                  group: "choices__group",
                  groupHeading: "choices__heading",
                  button: "choices__button",
                  activeState: "is-active",
                  focusState: "is-focused",
                  openState: "is-open",
                  disabledState: "is-disabled",
                  highlightedState: "is-highlighted",
                  selectedState: "is-selected",
                  flippedState: "is-flipped",
                  loadingState: "is-loading",
                  noResults: "has-no-results",
                  noChoices: "has-no-choices"
                };
                exports2.DEFAULT_CONFIG = {
                  items: [],
                  choices: [],
                  silent: false,
                  renderChoiceLimit: -1,
                  maxItemCount: -1,
                  addItems: true,
                  addItemFilter: null,
                  removeItems: true,
                  removeItemButton: false,
                  editItems: false,
                  allowHTML: true,
                  duplicateItemsAllowed: true,
                  delimiter: ",",
                  paste: true,
                  searchEnabled: true,
                  searchChoices: true,
                  searchFloor: 1,
                  searchResultLimit: 4,
                  searchFields: ["label", "value"],
                  position: "auto",
                  resetScrollPosition: true,
                  shouldSort: true,
                  shouldSortItems: false,
                  sorter: utils_1.sortByAlpha,
                  placeholder: true,
                  placeholderValue: null,
                  searchPlaceholderValue: null,
                  prependValue: null,
                  appendValue: null,
                  renderSelectedChoices: "auto",
                  loadingText: "Loading...",
                  noResultsText: "No results found",
                  noChoicesText: "No choices to choose from",
                  itemSelectText: "Press to select",
                  uniqueItemText: "Only unique values can be added",
                  customAddItemText: "Only values matching specific conditions can be added",
                  addItemText: function(value) {
                    return 'Press Enter to add <b>"'.concat((0, utils_1.sanitise)(value), '"</b>');
                  },
                  maxItemText: function(maxItemCount) {
                    return "Only ".concat(maxItemCount, " values can be added");
                  },
                  valueComparer: function(value1, value2) {
                    return value1 === value2;
                  },
                  fuseOptions: {
                    includeScore: true
                  },
                  labelId: "",
                  callbackOnInit: null,
                  callbackOnCreateTemplates: null,
                  classNames: exports2.DEFAULT_CLASSNAMES
                };
              }
            ),
            /***/
            18: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            978: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            948: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            359: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            285: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            533: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            187: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                var __createBinding = this && this.__createBinding || (Object.create ? function(o, m, k, k2) {
                  if (k2 === void 0)
                    k2 = k;
                  var desc = Object.getOwnPropertyDescriptor(m, k);
                  if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
                    desc = {
                      enumerable: true,
                      get: function() {
                        return m[k];
                      }
                    };
                  }
                  Object.defineProperty(o, k2, desc);
                } : function(o, m, k, k2) {
                  if (k2 === void 0)
                    k2 = k;
                  o[k2] = m[k];
                });
                var __exportStar = this && this.__exportStar || function(m, exports3) {
                  for (var p in m)
                    if (p !== "default" && !Object.prototype.hasOwnProperty.call(exports3, p))
                      __createBinding(exports3, m, p);
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                __exportStar(__webpack_require__2(18), exports2);
                __exportStar(__webpack_require__2(978), exports2);
                __exportStar(__webpack_require__2(948), exports2);
                __exportStar(__webpack_require__2(359), exports2);
                __exportStar(__webpack_require__2(285), exports2);
                __exportStar(__webpack_require__2(533), exports2);
                __exportStar(__webpack_require__2(287), exports2);
                __exportStar(__webpack_require__2(132), exports2);
                __exportStar(__webpack_require__2(837), exports2);
                __exportStar(__webpack_require__2(598), exports2);
                __exportStar(__webpack_require__2(369), exports2);
                __exportStar(__webpack_require__2(37), exports2);
                __exportStar(__webpack_require__2(47), exports2);
                __exportStar(__webpack_require__2(923), exports2);
                __exportStar(__webpack_require__2(876), exports2);
              }
            ),
            /***/
            287: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            132: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            837: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            598: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            37: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            369: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            47: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            923: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            876: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
              }
            ),
            /***/
            799: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.parseCustomProperties = exports2.diff = exports2.cloneObject = exports2.existsInArray = exports2.dispatchEvent = exports2.sortByScore = exports2.sortByAlpha = exports2.strToEl = exports2.sanitise = exports2.isScrolledIntoView = exports2.getAdjacentEl = exports2.wrap = exports2.isType = exports2.getType = exports2.generateId = exports2.generateChars = exports2.getRandomNumber = void 0;
                var getRandomNumber = function(min, max) {
                  return Math.floor(Math.random() * (max - min) + min);
                };
                exports2.getRandomNumber = getRandomNumber;
                var generateChars = function(length) {
                  return Array.from({
                    length
                  }, function() {
                    return (0, exports2.getRandomNumber)(0, 36).toString(36);
                  }).join("");
                };
                exports2.generateChars = generateChars;
                var generateId = function(element, prefix) {
                  var id = element.id || element.name && "".concat(element.name, "-").concat((0, exports2.generateChars)(2)) || (0, exports2.generateChars)(4);
                  id = id.replace(/(:|\.|\[|\]|,)/g, "");
                  id = "".concat(prefix, "-").concat(id);
                  return id;
                };
                exports2.generateId = generateId;
                var getType = function(obj) {
                  return Object.prototype.toString.call(obj).slice(8, -1);
                };
                exports2.getType = getType;
                var isType = function(type, obj) {
                  return obj !== void 0 && obj !== null && (0, exports2.getType)(obj) === type;
                };
                exports2.isType = isType;
                var wrap = function(element, wrapper) {
                  if (wrapper === void 0) {
                    wrapper = document.createElement("div");
                  }
                  if (element.parentNode) {
                    if (element.nextSibling) {
                      element.parentNode.insertBefore(wrapper, element.nextSibling);
                    } else {
                      element.parentNode.appendChild(wrapper);
                    }
                  }
                  return wrapper.appendChild(element);
                };
                exports2.wrap = wrap;
                var getAdjacentEl = function(startEl, selector, direction) {
                  if (direction === void 0) {
                    direction = 1;
                  }
                  var prop = "".concat(direction > 0 ? "next" : "previous", "ElementSibling");
                  var sibling = startEl[prop];
                  while (sibling) {
                    if (sibling.matches(selector)) {
                      return sibling;
                    }
                    sibling = sibling[prop];
                  }
                  return sibling;
                };
                exports2.getAdjacentEl = getAdjacentEl;
                var isScrolledIntoView = function(element, parent, direction) {
                  if (direction === void 0) {
                    direction = 1;
                  }
                  if (!element) {
                    return false;
                  }
                  var isVisible;
                  if (direction > 0) {
                    isVisible = parent.scrollTop + parent.offsetHeight >= element.offsetTop + element.offsetHeight;
                  } else {
                    isVisible = element.offsetTop >= parent.scrollTop;
                  }
                  return isVisible;
                };
                exports2.isScrolledIntoView = isScrolledIntoView;
                var sanitise = function(value) {
                  if (typeof value !== "string") {
                    return value;
                  }
                  return value.replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
                };
                exports2.sanitise = sanitise;
                exports2.strToEl = function() {
                  var tmpEl = document.createElement("div");
                  return function(str) {
                    var cleanedInput = str.trim();
                    tmpEl.innerHTML = cleanedInput;
                    var firldChild = tmpEl.children[0];
                    while (tmpEl.firstChild) {
                      tmpEl.removeChild(tmpEl.firstChild);
                    }
                    return firldChild;
                  };
                }();
                var sortByAlpha = function(_a, _b) {
                  var value = _a.value, _c = _a.label, label = _c === void 0 ? value : _c;
                  var value2 = _b.value, _d = _b.label, label2 = _d === void 0 ? value2 : _d;
                  return label.localeCompare(label2, [], {
                    sensitivity: "base",
                    ignorePunctuation: true,
                    numeric: true
                  });
                };
                exports2.sortByAlpha = sortByAlpha;
                var sortByScore = function(a, b) {
                  var _a = a.score, scoreA = _a === void 0 ? 0 : _a;
                  var _b = b.score, scoreB = _b === void 0 ? 0 : _b;
                  return scoreA - scoreB;
                };
                exports2.sortByScore = sortByScore;
                var dispatchEvent = function(element, type, customArgs) {
                  if (customArgs === void 0) {
                    customArgs = null;
                  }
                  var event = new CustomEvent(type, {
                    detail: customArgs,
                    bubbles: true,
                    cancelable: true
                  });
                  return element.dispatchEvent(event);
                };
                exports2.dispatchEvent = dispatchEvent;
                var existsInArray = function(array, value, key) {
                  if (key === void 0) {
                    key = "value";
                  }
                  return array.some(function(item) {
                    if (typeof value === "string") {
                      return item[key] === value.trim();
                    }
                    return item[key] === value;
                  });
                };
                exports2.existsInArray = existsInArray;
                var cloneObject = function(obj) {
                  return JSON.parse(JSON.stringify(obj));
                };
                exports2.cloneObject = cloneObject;
                var diff = function(a, b) {
                  var aKeys = Object.keys(a).sort();
                  var bKeys = Object.keys(b).sort();
                  return aKeys.filter(function(i) {
                    return bKeys.indexOf(i) < 0;
                  });
                };
                exports2.diff = diff;
                var parseCustomProperties = function(customProperties) {
                  if (typeof customProperties !== "undefined") {
                    try {
                      return JSON.parse(customProperties);
                    } catch (e) {
                      return customProperties;
                    }
                  }
                  return {};
                };
                exports2.parseCustomProperties = parseCustomProperties;
              }
            ),
            /***/
            273: (
              /***/
              function(__unused_webpack_module, exports2) {
                var __spreadArray = this && this.__spreadArray || function(to, from, pack) {
                  if (pack || arguments.length === 2)
                    for (var i = 0, l = from.length, ar; i < l; i++) {
                      if (ar || !(i in from)) {
                        if (!ar)
                          ar = Array.prototype.slice.call(from, 0, i);
                        ar[i] = from[i];
                      }
                    }
                  return to.concat(ar || Array.prototype.slice.call(from));
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.defaultState = void 0;
                exports2.defaultState = [];
                function choices(state, action) {
                  if (state === void 0) {
                    state = exports2.defaultState;
                  }
                  if (action === void 0) {
                    action = {};
                  }
                  switch (action.type) {
                    case "ADD_CHOICE": {
                      var addChoiceAction = action;
                      var choice = {
                        id: addChoiceAction.id,
                        elementId: addChoiceAction.elementId,
                        groupId: addChoiceAction.groupId,
                        value: addChoiceAction.value,
                        label: addChoiceAction.label || addChoiceAction.value,
                        disabled: addChoiceAction.disabled || false,
                        selected: false,
                        active: true,
                        score: 9999,
                        customProperties: addChoiceAction.customProperties,
                        placeholder: addChoiceAction.placeholder || false
                      };
                      return __spreadArray(__spreadArray([], state, true), [choice], false);
                    }
                    case "ADD_ITEM": {
                      var addItemAction_1 = action;
                      if (addItemAction_1.choiceId > -1) {
                        return state.map(function(obj) {
                          var choice2 = obj;
                          if (choice2.id === parseInt("".concat(addItemAction_1.choiceId), 10)) {
                            choice2.selected = true;
                          }
                          return choice2;
                        });
                      }
                      return state;
                    }
                    case "REMOVE_ITEM": {
                      var removeItemAction_1 = action;
                      if (removeItemAction_1.choiceId && removeItemAction_1.choiceId > -1) {
                        return state.map(function(obj) {
                          var choice2 = obj;
                          if (choice2.id === parseInt("".concat(removeItemAction_1.choiceId), 10)) {
                            choice2.selected = false;
                          }
                          return choice2;
                        });
                      }
                      return state;
                    }
                    case "FILTER_CHOICES": {
                      var filterChoicesAction_1 = action;
                      return state.map(function(obj) {
                        var choice2 = obj;
                        choice2.active = filterChoicesAction_1.results.some(function(_a) {
                          var item = _a.item, score = _a.score;
                          if (item.id === choice2.id) {
                            choice2.score = score;
                            return true;
                          }
                          return false;
                        });
                        return choice2;
                      });
                    }
                    case "ACTIVATE_CHOICES": {
                      var activateChoicesAction_1 = action;
                      return state.map(function(obj) {
                        var choice2 = obj;
                        choice2.active = activateChoicesAction_1.active;
                        return choice2;
                      });
                    }
                    case "CLEAR_CHOICES": {
                      return exports2.defaultState;
                    }
                    default: {
                      return state;
                    }
                  }
                }
                exports2["default"] = choices;
              }
            ),
            /***/
            871: (
              /***/
              function(__unused_webpack_module, exports2) {
                var __spreadArray = this && this.__spreadArray || function(to, from, pack) {
                  if (pack || arguments.length === 2)
                    for (var i = 0, l = from.length, ar; i < l; i++) {
                      if (ar || !(i in from)) {
                        if (!ar)
                          ar = Array.prototype.slice.call(from, 0, i);
                        ar[i] = from[i];
                      }
                    }
                  return to.concat(ar || Array.prototype.slice.call(from));
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.defaultState = void 0;
                exports2.defaultState = [];
                function groups(state, action) {
                  if (state === void 0) {
                    state = exports2.defaultState;
                  }
                  if (action === void 0) {
                    action = {};
                  }
                  switch (action.type) {
                    case "ADD_GROUP": {
                      var addGroupAction = action;
                      return __spreadArray(__spreadArray([], state, true), [{
                        id: addGroupAction.id,
                        value: addGroupAction.value,
                        active: addGroupAction.active,
                        disabled: addGroupAction.disabled
                      }], false);
                    }
                    case "CLEAR_CHOICES": {
                      return [];
                    }
                    default: {
                      return state;
                    }
                  }
                }
                exports2["default"] = groups;
              }
            ),
            /***/
            655: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                var __importDefault = this && this.__importDefault || function(mod) {
                  return mod && mod.__esModule ? mod : {
                    "default": mod
                  };
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.defaultState = void 0;
                var redux_1 = __webpack_require__2(791);
                var items_1 = __importDefault(__webpack_require__2(52));
                var groups_1 = __importDefault(__webpack_require__2(871));
                var choices_1 = __importDefault(__webpack_require__2(273));
                var loading_1 = __importDefault(__webpack_require__2(502));
                var utils_1 = __webpack_require__2(799);
                exports2.defaultState = {
                  groups: [],
                  items: [],
                  choices: [],
                  loading: false
                };
                var appReducer = (0, redux_1.combineReducers)({
                  items: items_1.default,
                  groups: groups_1.default,
                  choices: choices_1.default,
                  loading: loading_1.default
                });
                var rootReducer = function(passedState, action) {
                  var state = passedState;
                  if (action.type === "CLEAR_ALL") {
                    state = exports2.defaultState;
                  } else if (action.type === "RESET_TO") {
                    return (0, utils_1.cloneObject)(action.state);
                  }
                  return appReducer(state, action);
                };
                exports2["default"] = rootReducer;
              }
            ),
            /***/
            52: (
              /***/
              function(__unused_webpack_module, exports2) {
                var __spreadArray = this && this.__spreadArray || function(to, from, pack) {
                  if (pack || arguments.length === 2)
                    for (var i = 0, l = from.length, ar; i < l; i++) {
                      if (ar || !(i in from)) {
                        if (!ar)
                          ar = Array.prototype.slice.call(from, 0, i);
                        ar[i] = from[i];
                      }
                    }
                  return to.concat(ar || Array.prototype.slice.call(from));
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.defaultState = void 0;
                exports2.defaultState = [];
                function items(state, action) {
                  if (state === void 0) {
                    state = exports2.defaultState;
                  }
                  if (action === void 0) {
                    action = {};
                  }
                  switch (action.type) {
                    case "ADD_ITEM": {
                      var addItemAction = action;
                      var newState = __spreadArray(__spreadArray([], state, true), [{
                        id: addItemAction.id,
                        choiceId: addItemAction.choiceId,
                        groupId: addItemAction.groupId,
                        value: addItemAction.value,
                        label: addItemAction.label,
                        active: true,
                        highlighted: false,
                        customProperties: addItemAction.customProperties,
                        placeholder: addItemAction.placeholder || false,
                        keyCode: null
                      }], false);
                      return newState.map(function(obj) {
                        var item = obj;
                        item.highlighted = false;
                        return item;
                      });
                    }
                    case "REMOVE_ITEM": {
                      return state.map(function(obj) {
                        var item = obj;
                        if (item.id === action.id) {
                          item.active = false;
                        }
                        return item;
                      });
                    }
                    case "HIGHLIGHT_ITEM": {
                      var highlightItemAction_1 = action;
                      return state.map(function(obj) {
                        var item = obj;
                        if (item.id === highlightItemAction_1.id) {
                          item.highlighted = highlightItemAction_1.highlighted;
                        }
                        return item;
                      });
                    }
                    default: {
                      return state;
                    }
                  }
                }
                exports2["default"] = items;
              }
            ),
            /***/
            502: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                exports2.defaultState = void 0;
                exports2.defaultState = false;
                var general = function(state, action) {
                  if (state === void 0) {
                    state = exports2.defaultState;
                  }
                  if (action === void 0) {
                    action = {};
                  }
                  switch (action.type) {
                    case "SET_IS_LOADING": {
                      return action.isLoading;
                    }
                    default: {
                      return state;
                    }
                  }
                };
                exports2["default"] = general;
              }
            ),
            /***/
            744: (
              /***/
              function(__unused_webpack_module, exports2, __webpack_require__2) {
                var __spreadArray = this && this.__spreadArray || function(to, from, pack) {
                  if (pack || arguments.length === 2)
                    for (var i = 0, l = from.length, ar; i < l; i++) {
                      if (ar || !(i in from)) {
                        if (!ar)
                          ar = Array.prototype.slice.call(from, 0, i);
                        ar[i] = from[i];
                      }
                    }
                  return to.concat(ar || Array.prototype.slice.call(from));
                };
                var __importDefault = this && this.__importDefault || function(mod) {
                  return mod && mod.__esModule ? mod : {
                    "default": mod
                  };
                };
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var redux_1 = __webpack_require__2(791);
                var index_1 = __importDefault(__webpack_require__2(655));
                var Store = (
                  /** @class */
                  function() {
                    function Store2() {
                      this._store = (0, redux_1.createStore)(index_1.default, window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__());
                    }
                    Store2.prototype.subscribe = function(onChange) {
                      this._store.subscribe(onChange);
                    };
                    Store2.prototype.dispatch = function(action) {
                      this._store.dispatch(action);
                    };
                    Object.defineProperty(Store2.prototype, "state", {
                      /**
                       * Get store object (wrapping Redux method)
                       */
                      get: function() {
                        return this._store.getState();
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "items", {
                      /**
                       * Get items from store
                       */
                      get: function() {
                        return this.state.items;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "activeItems", {
                      /**
                       * Get active items from store
                       */
                      get: function() {
                        return this.items.filter(function(item) {
                          return item.active === true;
                        });
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "highlightedActiveItems", {
                      /**
                       * Get highlighted items from store
                       */
                      get: function() {
                        return this.items.filter(function(item) {
                          return item.active && item.highlighted;
                        });
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "choices", {
                      /**
                       * Get choices from store
                       */
                      get: function() {
                        return this.state.choices;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "activeChoices", {
                      /**
                       * Get active choices from store
                       */
                      get: function() {
                        return this.choices.filter(function(choice) {
                          return choice.active === true;
                        });
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "selectableChoices", {
                      /**
                       * Get selectable choices from store
                       */
                      get: function() {
                        return this.choices.filter(function(choice) {
                          return choice.disabled !== true;
                        });
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "searchableChoices", {
                      /**
                       * Get choices that can be searched (excluding placeholders)
                       */
                      get: function() {
                        return this.selectableChoices.filter(function(choice) {
                          return choice.placeholder !== true;
                        });
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "placeholderChoice", {
                      /**
                       * Get placeholder choice from store
                       */
                      get: function() {
                        return __spreadArray([], this.choices, true).reverse().find(function(choice) {
                          return choice.placeholder === true;
                        });
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "groups", {
                      /**
                       * Get groups from store
                       */
                      get: function() {
                        return this.state.groups;
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Object.defineProperty(Store2.prototype, "activeGroups", {
                      /**
                       * Get active groups from store
                       */
                      get: function() {
                        var _a = this, groups = _a.groups, choices = _a.choices;
                        return groups.filter(function(group) {
                          var isActive = group.active === true && group.disabled === false;
                          var hasActiveOptions = choices.some(function(choice) {
                            return choice.active === true && choice.disabled === false;
                          });
                          return isActive && hasActiveOptions;
                        }, []);
                      },
                      enumerable: false,
                      configurable: true
                    });
                    Store2.prototype.isLoading = function() {
                      return this.state.loading;
                    };
                    Store2.prototype.getChoiceById = function(id) {
                      return this.activeChoices.find(function(choice) {
                        return choice.id === parseInt(id, 10);
                      });
                    };
                    Store2.prototype.getGroupById = function(id) {
                      return this.groups.find(function(group) {
                        return group.id === id;
                      });
                    };
                    return Store2;
                  }()
                );
                exports2["default"] = Store;
              }
            ),
            /***/
            686: (
              /***/
              function(__unused_webpack_module, exports2) {
                Object.defineProperty(exports2, "__esModule", {
                  value: true
                });
                var templates = {
                  containerOuter: function(_a, dir, isSelectElement, isSelectOneElement, searchEnabled, passedElementType, labelId) {
                    var containerOuter = _a.classNames.containerOuter;
                    var div = Object.assign(document.createElement("div"), {
                      className: containerOuter
                    });
                    div.dataset.type = passedElementType;
                    if (dir) {
                      div.dir = dir;
                    }
                    if (isSelectOneElement) {
                      div.tabIndex = 0;
                    }
                    if (isSelectElement) {
                      div.setAttribute("role", searchEnabled ? "combobox" : "listbox");
                      if (searchEnabled) {
                        div.setAttribute("aria-autocomplete", "list");
                      }
                    }
                    div.setAttribute("aria-haspopup", "true");
                    div.setAttribute("aria-expanded", "false");
                    if (labelId) {
                      div.setAttribute("aria-labelledby", labelId);
                    }
                    return div;
                  },
                  containerInner: function(_a) {
                    var containerInner = _a.classNames.containerInner;
                    return Object.assign(document.createElement("div"), {
                      className: containerInner
                    });
                  },
                  itemList: function(_a, isSelectOneElement) {
                    var _b = _a.classNames, list = _b.list, listSingle = _b.listSingle, listItems = _b.listItems;
                    return Object.assign(document.createElement("div"), {
                      className: "".concat(list, " ").concat(isSelectOneElement ? listSingle : listItems)
                    });
                  },
                  placeholder: function(_a, value) {
                    var _b;
                    var allowHTML = _a.allowHTML, placeholder = _a.classNames.placeholder;
                    return Object.assign(document.createElement("div"), (_b = {
                      className: placeholder
                    }, _b[allowHTML ? "innerHTML" : "innerText"] = value, _b));
                  },
                  item: function(_a, _b, removeItemButton) {
                    var _c, _d;
                    var allowHTML = _a.allowHTML, _e = _a.classNames, item = _e.item, button = _e.button, highlightedState = _e.highlightedState, itemSelectable = _e.itemSelectable, placeholder = _e.placeholder;
                    var id = _b.id, value = _b.value, label = _b.label, customProperties = _b.customProperties, active = _b.active, disabled = _b.disabled, highlighted = _b.highlighted, isPlaceholder = _b.placeholder;
                    var div = Object.assign(document.createElement("div"), (_c = {
                      className: item
                    }, _c[allowHTML ? "innerHTML" : "innerText"] = label, _c));
                    Object.assign(div.dataset, {
                      item: "",
                      id,
                      value,
                      customProperties
                    });
                    if (active) {
                      div.setAttribute("aria-selected", "true");
                    }
                    if (disabled) {
                      div.setAttribute("aria-disabled", "true");
                    }
                    if (isPlaceholder) {
                      div.classList.add(placeholder);
                    }
                    div.classList.add(highlighted ? highlightedState : itemSelectable);
                    if (removeItemButton) {
                      if (disabled) {
                        div.classList.remove(itemSelectable);
                      }
                      div.dataset.deletable = "";
                      var REMOVE_ITEM_TEXT = "Remove item";
                      var removeButton = Object.assign(document.createElement("button"), (_d = {
                        type: "button",
                        className: button
                      }, _d[allowHTML ? "innerHTML" : "innerText"] = REMOVE_ITEM_TEXT, _d));
                      removeButton.setAttribute("aria-label", "".concat(REMOVE_ITEM_TEXT, ": '").concat(value, "'"));
                      removeButton.dataset.button = "";
                      div.appendChild(removeButton);
                    }
                    return div;
                  },
                  choiceList: function(_a, isSelectOneElement) {
                    var list = _a.classNames.list;
                    var div = Object.assign(document.createElement("div"), {
                      className: list
                    });
                    if (!isSelectOneElement) {
                      div.setAttribute("aria-multiselectable", "true");
                    }
                    div.setAttribute("role", "listbox");
                    return div;
                  },
                  choiceGroup: function(_a, _b) {
                    var _c;
                    var allowHTML = _a.allowHTML, _d = _a.classNames, group = _d.group, groupHeading = _d.groupHeading, itemDisabled = _d.itemDisabled;
                    var id = _b.id, value = _b.value, disabled = _b.disabled;
                    var div = Object.assign(document.createElement("div"), {
                      className: "".concat(group, " ").concat(disabled ? itemDisabled : "")
                    });
                    div.setAttribute("role", "group");
                    Object.assign(div.dataset, {
                      group: "",
                      id,
                      value
                    });
                    if (disabled) {
                      div.setAttribute("aria-disabled", "true");
                    }
                    div.appendChild(Object.assign(document.createElement("div"), (_c = {
                      className: groupHeading
                    }, _c[allowHTML ? "innerHTML" : "innerText"] = value, _c)));
                    return div;
                  },
                  choice: function(_a, _b, selectText) {
                    var _c;
                    var allowHTML = _a.allowHTML, _d = _a.classNames, item = _d.item, itemChoice = _d.itemChoice, itemSelectable = _d.itemSelectable, selectedState = _d.selectedState, itemDisabled = _d.itemDisabled, placeholder = _d.placeholder;
                    var id = _b.id, value = _b.value, label = _b.label, groupId = _b.groupId, elementId = _b.elementId, isDisabled = _b.disabled, isSelected = _b.selected, isPlaceholder = _b.placeholder;
                    var div = Object.assign(document.createElement("div"), (_c = {
                      id: elementId
                    }, _c[allowHTML ? "innerHTML" : "innerText"] = label, _c.className = "".concat(item, " ").concat(itemChoice), _c));
                    if (isSelected) {
                      div.classList.add(selectedState);
                    }
                    if (isPlaceholder) {
                      div.classList.add(placeholder);
                    }
                    div.setAttribute("role", groupId && groupId > 0 ? "treeitem" : "option");
                    Object.assign(div.dataset, {
                      choice: "",
                      id,
                      value,
                      selectText
                    });
                    if (isDisabled) {
                      div.classList.add(itemDisabled);
                      div.dataset.choiceDisabled = "";
                      div.setAttribute("aria-disabled", "true");
                    } else {
                      div.classList.add(itemSelectable);
                      div.dataset.choiceSelectable = "";
                    }
                    return div;
                  },
                  input: function(_a, placeholderValue) {
                    var _b = _a.classNames, input = _b.input, inputCloned = _b.inputCloned;
                    var inp = Object.assign(document.createElement("input"), {
                      type: "search",
                      name: "search_terms",
                      className: "".concat(input, " ").concat(inputCloned),
                      autocomplete: "off",
                      autocapitalize: "off",
                      spellcheck: false
                    });
                    inp.setAttribute("role", "textbox");
                    inp.setAttribute("aria-autocomplete", "list");
                    inp.setAttribute("aria-label", placeholderValue);
                    return inp;
                  },
                  dropdown: function(_a) {
                    var _b = _a.classNames, list = _b.list, listDropdown = _b.listDropdown;
                    var div = document.createElement("div");
                    div.classList.add(list, listDropdown);
                    div.setAttribute("aria-expanded", "false");
                    return div;
                  },
                  notice: function(_a, innerText, type) {
                    var _b;
                    var allowHTML = _a.allowHTML, _c = _a.classNames, item = _c.item, itemChoice = _c.itemChoice, noResults = _c.noResults, noChoices = _c.noChoices;
                    if (type === void 0) {
                      type = "";
                    }
                    var classes = [item, itemChoice];
                    if (type === "no-choices") {
                      classes.push(noChoices);
                    } else if (type === "no-results") {
                      classes.push(noResults);
                    }
                    return Object.assign(document.createElement("div"), (_b = {}, _b[allowHTML ? "innerHTML" : "innerText"] = innerText, _b.className = classes.join(" "), _b));
                  },
                  option: function(_a) {
                    var label = _a.label, value = _a.value, customProperties = _a.customProperties, active = _a.active, disabled = _a.disabled;
                    var opt = new Option(label, value, false, active);
                    if (customProperties) {
                      opt.dataset.customProperties = "".concat(customProperties);
                    }
                    opt.disabled = !!disabled;
                    return opt;
                  }
                };
                exports2["default"] = templates;
              }
            ),
            /***/
            996: (
              /***/
              function(module2) {
                var isMergeableObject = function isMergeableObject2(value) {
                  return isNonNullObject(value) && !isSpecial(value);
                };
                function isNonNullObject(value) {
                  return !!value && typeof value === "object";
                }
                function isSpecial(value) {
                  var stringValue = Object.prototype.toString.call(value);
                  return stringValue === "[object RegExp]" || stringValue === "[object Date]" || isReactElement(value);
                }
                var canUseSymbol = typeof Symbol === "function" && Symbol.for;
                var REACT_ELEMENT_TYPE = canUseSymbol ? Symbol.for("react.element") : 60103;
                function isReactElement(value) {
                  return value.$$typeof === REACT_ELEMENT_TYPE;
                }
                function emptyTarget(val) {
                  return Array.isArray(val) ? [] : {};
                }
                function cloneUnlessOtherwiseSpecified(value, options) {
                  return options.clone !== false && options.isMergeableObject(value) ? deepmerge(emptyTarget(value), value, options) : value;
                }
                function defaultArrayMerge(target, source, options) {
                  return target.concat(source).map(function(element) {
                    return cloneUnlessOtherwiseSpecified(element, options);
                  });
                }
                function getMergeFunction(key, options) {
                  if (!options.customMerge) {
                    return deepmerge;
                  }
                  var customMerge = options.customMerge(key);
                  return typeof customMerge === "function" ? customMerge : deepmerge;
                }
                function getEnumerableOwnPropertySymbols(target) {
                  return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(target).filter(function(symbol) {
                    return target.propertyIsEnumerable(symbol);
                  }) : [];
                }
                function getKeys(target) {
                  return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target));
                }
                function propertyIsOnObject(object, property) {
                  try {
                    return property in object;
                  } catch (_) {
                    return false;
                  }
                }
                function propertyIsUnsafe(target, key) {
                  return propertyIsOnObject(target, key) && !(Object.hasOwnProperty.call(target, key) && Object.propertyIsEnumerable.call(target, key));
                }
                function mergeObject(target, source, options) {
                  var destination = {};
                  if (options.isMergeableObject(target)) {
                    getKeys(target).forEach(function(key) {
                      destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
                    });
                  }
                  getKeys(source).forEach(function(key) {
                    if (propertyIsUnsafe(target, key)) {
                      return;
                    }
                    if (propertyIsOnObject(target, key) && options.isMergeableObject(source[key])) {
                      destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
                    } else {
                      destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
                    }
                  });
                  return destination;
                }
                function deepmerge(target, source, options) {
                  options = options || {};
                  options.arrayMerge = options.arrayMerge || defaultArrayMerge;
                  options.isMergeableObject = options.isMergeableObject || isMergeableObject;
                  options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;
                  var sourceIsArray = Array.isArray(source);
                  var targetIsArray = Array.isArray(target);
                  var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;
                  if (!sourceAndTargetTypesMatch) {
                    return cloneUnlessOtherwiseSpecified(source, options);
                  } else if (sourceIsArray) {
                    return options.arrayMerge(target, source, options);
                  } else {
                    return mergeObject(target, source, options);
                  }
                }
                deepmerge.all = function deepmergeAll(array, options) {
                  if (!Array.isArray(array)) {
                    throw new Error("first argument should be an array");
                  }
                  return array.reduce(function(prev, next) {
                    return deepmerge(prev, next, options);
                  }, {});
                };
                var deepmerge_1 = deepmerge;
                module2.exports = deepmerge_1;
              }
            ),
            /***/
            221: (
              /***/
              function(__unused_webpack_module, __webpack_exports__2, __webpack_require__2) {
                __webpack_require__2.r(__webpack_exports__2);
                __webpack_require__2.d(__webpack_exports__2, {
                  /* harmony export */
                  "default": function() {
                    return (
                      /* binding */
                      Fuse
                    );
                  }
                  /* harmony export */
                });
                function isArray(value) {
                  return !Array.isArray ? getTag(value) === "[object Array]" : Array.isArray(value);
                }
                const INFINITY = 1 / 0;
                function baseToString(value) {
                  if (typeof value == "string") {
                    return value;
                  }
                  let result = value + "";
                  return result == "0" && 1 / value == -INFINITY ? "-0" : result;
                }
                function toString(value) {
                  return value == null ? "" : baseToString(value);
                }
                function isString(value) {
                  return typeof value === "string";
                }
                function isNumber(value) {
                  return typeof value === "number";
                }
                function isBoolean(value) {
                  return value === true || value === false || isObjectLike(value) && getTag(value) == "[object Boolean]";
                }
                function isObject(value) {
                  return typeof value === "object";
                }
                function isObjectLike(value) {
                  return isObject(value) && value !== null;
                }
                function isDefined(value) {
                  return value !== void 0 && value !== null;
                }
                function isBlank(value) {
                  return !value.trim().length;
                }
                function getTag(value) {
                  return value == null ? value === void 0 ? "[object Undefined]" : "[object Null]" : Object.prototype.toString.call(value);
                }
                const EXTENDED_SEARCH_UNAVAILABLE = "Extended search is not available";
                const INCORRECT_INDEX_TYPE = "Incorrect 'index' type";
                const LOGICAL_SEARCH_INVALID_QUERY_FOR_KEY = (key) => `Invalid value for key ${key}`;
                const PATTERN_LENGTH_TOO_LARGE = (max) => `Pattern length exceeds max of ${max}.`;
                const MISSING_KEY_PROPERTY = (name) => `Missing ${name} property in key`;
                const INVALID_KEY_WEIGHT_VALUE = (key) => `Property 'weight' in key '${key}' must be a positive integer`;
                const hasOwn = Object.prototype.hasOwnProperty;
                class KeyStore {
                  constructor(keys) {
                    this._keys = [];
                    this._keyMap = {};
                    let totalWeight = 0;
                    keys.forEach((key) => {
                      let obj = createKey(key);
                      totalWeight += obj.weight;
                      this._keys.push(obj);
                      this._keyMap[obj.id] = obj;
                      totalWeight += obj.weight;
                    });
                    this._keys.forEach((key) => {
                      key.weight /= totalWeight;
                    });
                  }
                  get(keyId) {
                    return this._keyMap[keyId];
                  }
                  keys() {
                    return this._keys;
                  }
                  toJSON() {
                    return JSON.stringify(this._keys);
                  }
                }
                function createKey(key) {
                  let path = null;
                  let id = null;
                  let src = null;
                  let weight = 1;
                  let getFn = null;
                  if (isString(key) || isArray(key)) {
                    src = key;
                    path = createKeyPath(key);
                    id = createKeyId(key);
                  } else {
                    if (!hasOwn.call(key, "name")) {
                      throw new Error(MISSING_KEY_PROPERTY("name"));
                    }
                    const name = key.name;
                    src = name;
                    if (hasOwn.call(key, "weight")) {
                      weight = key.weight;
                      if (weight <= 0) {
                        throw new Error(INVALID_KEY_WEIGHT_VALUE(name));
                      }
                    }
                    path = createKeyPath(name);
                    id = createKeyId(name);
                    getFn = key.getFn;
                  }
                  return { path, id, weight, src, getFn };
                }
                function createKeyPath(key) {
                  return isArray(key) ? key : key.split(".");
                }
                function createKeyId(key) {
                  return isArray(key) ? key.join(".") : key;
                }
                function get(obj, path) {
                  let list = [];
                  let arr = false;
                  const deepGet = (obj2, path2, index) => {
                    if (!isDefined(obj2)) {
                      return;
                    }
                    if (!path2[index]) {
                      list.push(obj2);
                    } else {
                      let key = path2[index];
                      const value = obj2[key];
                      if (!isDefined(value)) {
                        return;
                      }
                      if (index === path2.length - 1 && (isString(value) || isNumber(value) || isBoolean(value))) {
                        list.push(toString(value));
                      } else if (isArray(value)) {
                        arr = true;
                        for (let i = 0, len = value.length; i < len; i += 1) {
                          deepGet(value[i], path2, index + 1);
                        }
                      } else if (path2.length) {
                        deepGet(value, path2, index + 1);
                      }
                    }
                  };
                  deepGet(obj, isString(path) ? path.split(".") : path, 0);
                  return arr ? list : list[0];
                }
                const MatchOptions = {
                  // Whether the matches should be included in the result set. When `true`, each record in the result
                  // set will include the indices of the matched characters.
                  // These can consequently be used for highlighting purposes.
                  includeMatches: false,
                  // When `true`, the matching function will continue to the end of a search pattern even if
                  // a perfect match has already been located in the string.
                  findAllMatches: false,
                  // Minimum number of characters that must be matched before a result is considered a match
                  minMatchCharLength: 1
                };
                const BasicOptions = {
                  // When `true`, the algorithm continues searching to the end of the input even if a perfect
                  // match is found before the end of the same input.
                  isCaseSensitive: false,
                  // When true, the matching function will continue to the end of a search pattern even if
                  includeScore: false,
                  // List of properties that will be searched. This also supports nested properties.
                  keys: [],
                  // Whether to sort the result list, by score
                  shouldSort: true,
                  // Default sort function: sort by ascending score, ascending index
                  sortFn: (a, b) => a.score === b.score ? a.idx < b.idx ? -1 : 1 : a.score < b.score ? -1 : 1
                };
                const FuzzyOptions = {
                  // Approximately where in the text is the pattern expected to be found?
                  location: 0,
                  // At what point does the match algorithm give up. A threshold of '0.0' requires a perfect match
                  // (of both letters and location), a threshold of '1.0' would match anything.
                  threshold: 0.6,
                  // Determines how close the match must be to the fuzzy location (specified above).
                  // An exact letter match which is 'distance' characters away from the fuzzy location
                  // would score as a complete mismatch. A distance of '0' requires the match be at
                  // the exact location specified, a threshold of '1000' would require a perfect match
                  // to be within 800 characters of the fuzzy location to be found using a 0.8 threshold.
                  distance: 100
                };
                const AdvancedOptions = {
                  // When `true`, it enables the use of unix-like search commands
                  useExtendedSearch: false,
                  // The get function to use when fetching an object's properties.
                  // The default will search nested paths *ie foo.bar.baz*
                  getFn: get,
                  // When `true`, search will ignore `location` and `distance`, so it won't matter
                  // where in the string the pattern appears.
                  // More info: https://fusejs.io/concepts/scoring-theory.html#fuzziness-score
                  ignoreLocation: false,
                  // When `true`, the calculation for the relevance score (used for sorting) will
                  // ignore the field-length norm.
                  // More info: https://fusejs.io/concepts/scoring-theory.html#field-length-norm
                  ignoreFieldNorm: false,
                  // The weight to determine how much field length norm effects scoring.
                  fieldNormWeight: 1
                };
                var Config = {
                  ...BasicOptions,
                  ...MatchOptions,
                  ...FuzzyOptions,
                  ...AdvancedOptions
                };
                const SPACE = /[^ ]+/g;
                function norm(weight = 1, mantissa = 3) {
                  const cache = /* @__PURE__ */ new Map();
                  const m = Math.pow(10, mantissa);
                  return {
                    get(value) {
                      const numTokens = value.match(SPACE).length;
                      if (cache.has(numTokens)) {
                        return cache.get(numTokens);
                      }
                      const norm2 = 1 / Math.pow(numTokens, 0.5 * weight);
                      const n = parseFloat(Math.round(norm2 * m) / m);
                      cache.set(numTokens, n);
                      return n;
                    },
                    clear() {
                      cache.clear();
                    }
                  };
                }
                class FuseIndex {
                  constructor({
                    getFn = Config.getFn,
                    fieldNormWeight = Config.fieldNormWeight
                  } = {}) {
                    this.norm = norm(fieldNormWeight, 3);
                    this.getFn = getFn;
                    this.isCreated = false;
                    this.setIndexRecords();
                  }
                  setSources(docs = []) {
                    this.docs = docs;
                  }
                  setIndexRecords(records = []) {
                    this.records = records;
                  }
                  setKeys(keys = []) {
                    this.keys = keys;
                    this._keysMap = {};
                    keys.forEach((key, idx) => {
                      this._keysMap[key.id] = idx;
                    });
                  }
                  create() {
                    if (this.isCreated || !this.docs.length) {
                      return;
                    }
                    this.isCreated = true;
                    if (isString(this.docs[0])) {
                      this.docs.forEach((doc, docIndex) => {
                        this._addString(doc, docIndex);
                      });
                    } else {
                      this.docs.forEach((doc, docIndex) => {
                        this._addObject(doc, docIndex);
                      });
                    }
                    this.norm.clear();
                  }
                  // Adds a doc to the end of the index
                  add(doc) {
                    const idx = this.size();
                    if (isString(doc)) {
                      this._addString(doc, idx);
                    } else {
                      this._addObject(doc, idx);
                    }
                  }
                  // Removes the doc at the specified index of the index
                  removeAt(idx) {
                    this.records.splice(idx, 1);
                    for (let i = idx, len = this.size(); i < len; i += 1) {
                      this.records[i].i -= 1;
                    }
                  }
                  getValueForItemAtKeyId(item, keyId) {
                    return item[this._keysMap[keyId]];
                  }
                  size() {
                    return this.records.length;
                  }
                  _addString(doc, docIndex) {
                    if (!isDefined(doc) || isBlank(doc)) {
                      return;
                    }
                    let record = {
                      v: doc,
                      i: docIndex,
                      n: this.norm.get(doc)
                    };
                    this.records.push(record);
                  }
                  _addObject(doc, docIndex) {
                    let record = { i: docIndex, $: {} };
                    this.keys.forEach((key, keyIndex) => {
                      let value = key.getFn ? key.getFn(doc) : this.getFn(doc, key.path);
                      if (!isDefined(value)) {
                        return;
                      }
                      if (isArray(value)) {
                        let subRecords = [];
                        const stack = [{ nestedArrIndex: -1, value }];
                        while (stack.length) {
                          const { nestedArrIndex, value: value2 } = stack.pop();
                          if (!isDefined(value2)) {
                            continue;
                          }
                          if (isString(value2) && !isBlank(value2)) {
                            let subRecord = {
                              v: value2,
                              i: nestedArrIndex,
                              n: this.norm.get(value2)
                            };
                            subRecords.push(subRecord);
                          } else if (isArray(value2)) {
                            value2.forEach((item, k) => {
                              stack.push({
                                nestedArrIndex: k,
                                value: item
                              });
                            });
                          } else
                            ;
                        }
                        record.$[keyIndex] = subRecords;
                      } else if (isString(value) && !isBlank(value)) {
                        let subRecord = {
                          v: value,
                          n: this.norm.get(value)
                        };
                        record.$[keyIndex] = subRecord;
                      }
                    });
                    this.records.push(record);
                  }
                  toJSON() {
                    return {
                      keys: this.keys,
                      records: this.records
                    };
                  }
                }
                function createIndex(keys, docs, { getFn = Config.getFn, fieldNormWeight = Config.fieldNormWeight } = {}) {
                  const myIndex = new FuseIndex({ getFn, fieldNormWeight });
                  myIndex.setKeys(keys.map(createKey));
                  myIndex.setSources(docs);
                  myIndex.create();
                  return myIndex;
                }
                function parseIndex(data, { getFn = Config.getFn, fieldNormWeight = Config.fieldNormWeight } = {}) {
                  const { keys, records } = data;
                  const myIndex = new FuseIndex({ getFn, fieldNormWeight });
                  myIndex.setKeys(keys);
                  myIndex.setIndexRecords(records);
                  return myIndex;
                }
                function computeScore$1(pattern, {
                  errors = 0,
                  currentLocation = 0,
                  expectedLocation = 0,
                  distance = Config.distance,
                  ignoreLocation = Config.ignoreLocation
                } = {}) {
                  const accuracy = errors / pattern.length;
                  if (ignoreLocation) {
                    return accuracy;
                  }
                  const proximity = Math.abs(expectedLocation - currentLocation);
                  if (!distance) {
                    return proximity ? 1 : accuracy;
                  }
                  return accuracy + proximity / distance;
                }
                function convertMaskToIndices(matchmask = [], minMatchCharLength = Config.minMatchCharLength) {
                  let indices = [];
                  let start = -1;
                  let end = -1;
                  let i = 0;
                  for (let len = matchmask.length; i < len; i += 1) {
                    let match = matchmask[i];
                    if (match && start === -1) {
                      start = i;
                    } else if (!match && start !== -1) {
                      end = i - 1;
                      if (end - start + 1 >= minMatchCharLength) {
                        indices.push([start, end]);
                      }
                      start = -1;
                    }
                  }
                  if (matchmask[i - 1] && i - start >= minMatchCharLength) {
                    indices.push([start, i - 1]);
                  }
                  return indices;
                }
                const MAX_BITS = 32;
                function search(text, pattern, patternAlphabet, {
                  location = Config.location,
                  distance = Config.distance,
                  threshold = Config.threshold,
                  findAllMatches = Config.findAllMatches,
                  minMatchCharLength = Config.minMatchCharLength,
                  includeMatches = Config.includeMatches,
                  ignoreLocation = Config.ignoreLocation
                } = {}) {
                  if (pattern.length > MAX_BITS) {
                    throw new Error(PATTERN_LENGTH_TOO_LARGE(MAX_BITS));
                  }
                  const patternLen = pattern.length;
                  const textLen = text.length;
                  const expectedLocation = Math.max(0, Math.min(location, textLen));
                  let currentThreshold = threshold;
                  let bestLocation = expectedLocation;
                  const computeMatches = minMatchCharLength > 1 || includeMatches;
                  const matchMask = computeMatches ? Array(textLen) : [];
                  let index;
                  while ((index = text.indexOf(pattern, bestLocation)) > -1) {
                    let score = computeScore$1(pattern, {
                      currentLocation: index,
                      expectedLocation,
                      distance,
                      ignoreLocation
                    });
                    currentThreshold = Math.min(score, currentThreshold);
                    bestLocation = index + patternLen;
                    if (computeMatches) {
                      let i = 0;
                      while (i < patternLen) {
                        matchMask[index + i] = 1;
                        i += 1;
                      }
                    }
                  }
                  bestLocation = -1;
                  let lastBitArr = [];
                  let finalScore = 1;
                  let binMax = patternLen + textLen;
                  const mask = 1 << patternLen - 1;
                  for (let i = 0; i < patternLen; i += 1) {
                    let binMin = 0;
                    let binMid = binMax;
                    while (binMin < binMid) {
                      const score2 = computeScore$1(pattern, {
                        errors: i,
                        currentLocation: expectedLocation + binMid,
                        expectedLocation,
                        distance,
                        ignoreLocation
                      });
                      if (score2 <= currentThreshold) {
                        binMin = binMid;
                      } else {
                        binMax = binMid;
                      }
                      binMid = Math.floor((binMax - binMin) / 2 + binMin);
                    }
                    binMax = binMid;
                    let start = Math.max(1, expectedLocation - binMid + 1);
                    let finish = findAllMatches ? textLen : Math.min(expectedLocation + binMid, textLen) + patternLen;
                    let bitArr = Array(finish + 2);
                    bitArr[finish + 1] = (1 << i) - 1;
                    for (let j = finish; j >= start; j -= 1) {
                      let currentLocation = j - 1;
                      let charMatch = patternAlphabet[text.charAt(currentLocation)];
                      if (computeMatches) {
                        matchMask[currentLocation] = +!!charMatch;
                      }
                      bitArr[j] = (bitArr[j + 1] << 1 | 1) & charMatch;
                      if (i) {
                        bitArr[j] |= (lastBitArr[j + 1] | lastBitArr[j]) << 1 | 1 | lastBitArr[j + 1];
                      }
                      if (bitArr[j] & mask) {
                        finalScore = computeScore$1(pattern, {
                          errors: i,
                          currentLocation,
                          expectedLocation,
                          distance,
                          ignoreLocation
                        });
                        if (finalScore <= currentThreshold) {
                          currentThreshold = finalScore;
                          bestLocation = currentLocation;
                          if (bestLocation <= expectedLocation) {
                            break;
                          }
                          start = Math.max(1, 2 * expectedLocation - bestLocation);
                        }
                      }
                    }
                    const score = computeScore$1(pattern, {
                      errors: i + 1,
                      currentLocation: expectedLocation,
                      expectedLocation,
                      distance,
                      ignoreLocation
                    });
                    if (score > currentThreshold) {
                      break;
                    }
                    lastBitArr = bitArr;
                  }
                  const result = {
                    isMatch: bestLocation >= 0,
                    // Count exact matches (those with a score of 0) to be "almost" exact
                    score: Math.max(1e-3, finalScore)
                  };
                  if (computeMatches) {
                    const indices = convertMaskToIndices(matchMask, minMatchCharLength);
                    if (!indices.length) {
                      result.isMatch = false;
                    } else if (includeMatches) {
                      result.indices = indices;
                    }
                  }
                  return result;
                }
                function createPatternAlphabet(pattern) {
                  let mask = {};
                  for (let i = 0, len = pattern.length; i < len; i += 1) {
                    const char = pattern.charAt(i);
                    mask[char] = (mask[char] || 0) | 1 << len - i - 1;
                  }
                  return mask;
                }
                class BitapSearch {
                  constructor(pattern, {
                    location = Config.location,
                    threshold = Config.threshold,
                    distance = Config.distance,
                    includeMatches = Config.includeMatches,
                    findAllMatches = Config.findAllMatches,
                    minMatchCharLength = Config.minMatchCharLength,
                    isCaseSensitive = Config.isCaseSensitive,
                    ignoreLocation = Config.ignoreLocation
                  } = {}) {
                    this.options = {
                      location,
                      threshold,
                      distance,
                      includeMatches,
                      findAllMatches,
                      minMatchCharLength,
                      isCaseSensitive,
                      ignoreLocation
                    };
                    this.pattern = isCaseSensitive ? pattern : pattern.toLowerCase();
                    this.chunks = [];
                    if (!this.pattern.length) {
                      return;
                    }
                    const addChunk = (pattern2, startIndex) => {
                      this.chunks.push({
                        pattern: pattern2,
                        alphabet: createPatternAlphabet(pattern2),
                        startIndex
                      });
                    };
                    const len = this.pattern.length;
                    if (len > MAX_BITS) {
                      let i = 0;
                      const remainder = len % MAX_BITS;
                      const end = len - remainder;
                      while (i < end) {
                        addChunk(this.pattern.substr(i, MAX_BITS), i);
                        i += MAX_BITS;
                      }
                      if (remainder) {
                        const startIndex = len - MAX_BITS;
                        addChunk(this.pattern.substr(startIndex), startIndex);
                      }
                    } else {
                      addChunk(this.pattern, 0);
                    }
                  }
                  searchIn(text) {
                    const { isCaseSensitive, includeMatches } = this.options;
                    if (!isCaseSensitive) {
                      text = text.toLowerCase();
                    }
                    if (this.pattern === text) {
                      let result2 = {
                        isMatch: true,
                        score: 0
                      };
                      if (includeMatches) {
                        result2.indices = [[0, text.length - 1]];
                      }
                      return result2;
                    }
                    const {
                      location,
                      distance,
                      threshold,
                      findAllMatches,
                      minMatchCharLength,
                      ignoreLocation
                    } = this.options;
                    let allIndices = [];
                    let totalScore = 0;
                    let hasMatches = false;
                    this.chunks.forEach(({ pattern, alphabet, startIndex }) => {
                      const { isMatch, score, indices } = search(text, pattern, alphabet, {
                        location: location + startIndex,
                        distance,
                        threshold,
                        findAllMatches,
                        minMatchCharLength,
                        includeMatches,
                        ignoreLocation
                      });
                      if (isMatch) {
                        hasMatches = true;
                      }
                      totalScore += score;
                      if (isMatch && indices) {
                        allIndices = [...allIndices, ...indices];
                      }
                    });
                    let result = {
                      isMatch: hasMatches,
                      score: hasMatches ? totalScore / this.chunks.length : 1
                    };
                    if (hasMatches && includeMatches) {
                      result.indices = allIndices;
                    }
                    return result;
                  }
                }
                class BaseMatch {
                  constructor(pattern) {
                    this.pattern = pattern;
                  }
                  static isMultiMatch(pattern) {
                    return getMatch(pattern, this.multiRegex);
                  }
                  static isSingleMatch(pattern) {
                    return getMatch(pattern, this.singleRegex);
                  }
                  search() {
                  }
                }
                function getMatch(pattern, exp) {
                  const matches = pattern.match(exp);
                  return matches ? matches[1] : null;
                }
                class ExactMatch extends BaseMatch {
                  constructor(pattern) {
                    super(pattern);
                  }
                  static get type() {
                    return "exact";
                  }
                  static get multiRegex() {
                    return /^="(.*)"$/;
                  }
                  static get singleRegex() {
                    return /^=(.*)$/;
                  }
                  search(text) {
                    const isMatch = text === this.pattern;
                    return {
                      isMatch,
                      score: isMatch ? 0 : 1,
                      indices: [0, this.pattern.length - 1]
                    };
                  }
                }
                class InverseExactMatch extends BaseMatch {
                  constructor(pattern) {
                    super(pattern);
                  }
                  static get type() {
                    return "inverse-exact";
                  }
                  static get multiRegex() {
                    return /^!"(.*)"$/;
                  }
                  static get singleRegex() {
                    return /^!(.*)$/;
                  }
                  search(text) {
                    const index = text.indexOf(this.pattern);
                    const isMatch = index === -1;
                    return {
                      isMatch,
                      score: isMatch ? 0 : 1,
                      indices: [0, text.length - 1]
                    };
                  }
                }
                class PrefixExactMatch extends BaseMatch {
                  constructor(pattern) {
                    super(pattern);
                  }
                  static get type() {
                    return "prefix-exact";
                  }
                  static get multiRegex() {
                    return /^\^"(.*)"$/;
                  }
                  static get singleRegex() {
                    return /^\^(.*)$/;
                  }
                  search(text) {
                    const isMatch = text.startsWith(this.pattern);
                    return {
                      isMatch,
                      score: isMatch ? 0 : 1,
                      indices: [0, this.pattern.length - 1]
                    };
                  }
                }
                class InversePrefixExactMatch extends BaseMatch {
                  constructor(pattern) {
                    super(pattern);
                  }
                  static get type() {
                    return "inverse-prefix-exact";
                  }
                  static get multiRegex() {
                    return /^!\^"(.*)"$/;
                  }
                  static get singleRegex() {
                    return /^!\^(.*)$/;
                  }
                  search(text) {
                    const isMatch = !text.startsWith(this.pattern);
                    return {
                      isMatch,
                      score: isMatch ? 0 : 1,
                      indices: [0, text.length - 1]
                    };
                  }
                }
                class SuffixExactMatch extends BaseMatch {
                  constructor(pattern) {
                    super(pattern);
                  }
                  static get type() {
                    return "suffix-exact";
                  }
                  static get multiRegex() {
                    return /^"(.*)"\$$/;
                  }
                  static get singleRegex() {
                    return /^(.*)\$$/;
                  }
                  search(text) {
                    const isMatch = text.endsWith(this.pattern);
                    return {
                      isMatch,
                      score: isMatch ? 0 : 1,
                      indices: [text.length - this.pattern.length, text.length - 1]
                    };
                  }
                }
                class InverseSuffixExactMatch extends BaseMatch {
                  constructor(pattern) {
                    super(pattern);
                  }
                  static get type() {
                    return "inverse-suffix-exact";
                  }
                  static get multiRegex() {
                    return /^!"(.*)"\$$/;
                  }
                  static get singleRegex() {
                    return /^!(.*)\$$/;
                  }
                  search(text) {
                    const isMatch = !text.endsWith(this.pattern);
                    return {
                      isMatch,
                      score: isMatch ? 0 : 1,
                      indices: [0, text.length - 1]
                    };
                  }
                }
                class FuzzyMatch extends BaseMatch {
                  constructor(pattern, {
                    location = Config.location,
                    threshold = Config.threshold,
                    distance = Config.distance,
                    includeMatches = Config.includeMatches,
                    findAllMatches = Config.findAllMatches,
                    minMatchCharLength = Config.minMatchCharLength,
                    isCaseSensitive = Config.isCaseSensitive,
                    ignoreLocation = Config.ignoreLocation
                  } = {}) {
                    super(pattern);
                    this._bitapSearch = new BitapSearch(pattern, {
                      location,
                      threshold,
                      distance,
                      includeMatches,
                      findAllMatches,
                      minMatchCharLength,
                      isCaseSensitive,
                      ignoreLocation
                    });
                  }
                  static get type() {
                    return "fuzzy";
                  }
                  static get multiRegex() {
                    return /^"(.*)"$/;
                  }
                  static get singleRegex() {
                    return /^(.*)$/;
                  }
                  search(text) {
                    return this._bitapSearch.searchIn(text);
                  }
                }
                class IncludeMatch extends BaseMatch {
                  constructor(pattern) {
                    super(pattern);
                  }
                  static get type() {
                    return "include";
                  }
                  static get multiRegex() {
                    return /^'"(.*)"$/;
                  }
                  static get singleRegex() {
                    return /^'(.*)$/;
                  }
                  search(text) {
                    let location = 0;
                    let index;
                    const indices = [];
                    const patternLen = this.pattern.length;
                    while ((index = text.indexOf(this.pattern, location)) > -1) {
                      location = index + patternLen;
                      indices.push([index, location - 1]);
                    }
                    const isMatch = !!indices.length;
                    return {
                      isMatch,
                      score: isMatch ? 0 : 1,
                      indices
                    };
                  }
                }
                const searchers = [
                  ExactMatch,
                  IncludeMatch,
                  PrefixExactMatch,
                  InversePrefixExactMatch,
                  InverseSuffixExactMatch,
                  SuffixExactMatch,
                  InverseExactMatch,
                  FuzzyMatch
                ];
                const searchersLen = searchers.length;
                const SPACE_RE = / +(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)/;
                const OR_TOKEN = "|";
                function parseQuery(pattern, options = {}) {
                  return pattern.split(OR_TOKEN).map((item) => {
                    let query = item.trim().split(SPACE_RE).filter((item2) => item2 && !!item2.trim());
                    let results = [];
                    for (let i = 0, len = query.length; i < len; i += 1) {
                      const queryItem = query[i];
                      let found = false;
                      let idx = -1;
                      while (!found && ++idx < searchersLen) {
                        const searcher = searchers[idx];
                        let token = searcher.isMultiMatch(queryItem);
                        if (token) {
                          results.push(new searcher(token, options));
                          found = true;
                        }
                      }
                      if (found) {
                        continue;
                      }
                      idx = -1;
                      while (++idx < searchersLen) {
                        const searcher = searchers[idx];
                        let token = searcher.isSingleMatch(queryItem);
                        if (token) {
                          results.push(new searcher(token, options));
                          break;
                        }
                      }
                    }
                    return results;
                  });
                }
                const MultiMatchSet = /* @__PURE__ */ new Set([FuzzyMatch.type, IncludeMatch.type]);
                class ExtendedSearch {
                  constructor(pattern, {
                    isCaseSensitive = Config.isCaseSensitive,
                    includeMatches = Config.includeMatches,
                    minMatchCharLength = Config.minMatchCharLength,
                    ignoreLocation = Config.ignoreLocation,
                    findAllMatches = Config.findAllMatches,
                    location = Config.location,
                    threshold = Config.threshold,
                    distance = Config.distance
                  } = {}) {
                    this.query = null;
                    this.options = {
                      isCaseSensitive,
                      includeMatches,
                      minMatchCharLength,
                      findAllMatches,
                      ignoreLocation,
                      location,
                      threshold,
                      distance
                    };
                    this.pattern = isCaseSensitive ? pattern : pattern.toLowerCase();
                    this.query = parseQuery(this.pattern, this.options);
                  }
                  static condition(_, options) {
                    return options.useExtendedSearch;
                  }
                  searchIn(text) {
                    const query = this.query;
                    if (!query) {
                      return {
                        isMatch: false,
                        score: 1
                      };
                    }
                    const { includeMatches, isCaseSensitive } = this.options;
                    text = isCaseSensitive ? text : text.toLowerCase();
                    let numMatches = 0;
                    let allIndices = [];
                    let totalScore = 0;
                    for (let i = 0, qLen = query.length; i < qLen; i += 1) {
                      const searchers2 = query[i];
                      allIndices.length = 0;
                      numMatches = 0;
                      for (let j = 0, pLen = searchers2.length; j < pLen; j += 1) {
                        const searcher = searchers2[j];
                        const { isMatch, indices, score } = searcher.search(text);
                        if (isMatch) {
                          numMatches += 1;
                          totalScore += score;
                          if (includeMatches) {
                            const type = searcher.constructor.type;
                            if (MultiMatchSet.has(type)) {
                              allIndices = [...allIndices, ...indices];
                            } else {
                              allIndices.push(indices);
                            }
                          }
                        } else {
                          totalScore = 0;
                          numMatches = 0;
                          allIndices.length = 0;
                          break;
                        }
                      }
                      if (numMatches) {
                        let result = {
                          isMatch: true,
                          score: totalScore / numMatches
                        };
                        if (includeMatches) {
                          result.indices = allIndices;
                        }
                        return result;
                      }
                    }
                    return {
                      isMatch: false,
                      score: 1
                    };
                  }
                }
                const registeredSearchers = [];
                function register(...args) {
                  registeredSearchers.push(...args);
                }
                function createSearcher(pattern, options) {
                  for (let i = 0, len = registeredSearchers.length; i < len; i += 1) {
                    let searcherClass = registeredSearchers[i];
                    if (searcherClass.condition(pattern, options)) {
                      return new searcherClass(pattern, options);
                    }
                  }
                  return new BitapSearch(pattern, options);
                }
                const LogicalOperator = {
                  AND: "$and",
                  OR: "$or"
                };
                const KeyType = {
                  PATH: "$path",
                  PATTERN: "$val"
                };
                const isExpression = (query) => !!(query[LogicalOperator.AND] || query[LogicalOperator.OR]);
                const isPath = (query) => !!query[KeyType.PATH];
                const isLeaf = (query) => !isArray(query) && isObject(query) && !isExpression(query);
                const convertToExplicit = (query) => ({
                  [LogicalOperator.AND]: Object.keys(query).map((key) => ({
                    [key]: query[key]
                  }))
                });
                function parse(query, options, { auto = true } = {}) {
                  const next = (query2) => {
                    let keys = Object.keys(query2);
                    const isQueryPath = isPath(query2);
                    if (!isQueryPath && keys.length > 1 && !isExpression(query2)) {
                      return next(convertToExplicit(query2));
                    }
                    if (isLeaf(query2)) {
                      const key = isQueryPath ? query2[KeyType.PATH] : keys[0];
                      const pattern = isQueryPath ? query2[KeyType.PATTERN] : query2[key];
                      if (!isString(pattern)) {
                        throw new Error(LOGICAL_SEARCH_INVALID_QUERY_FOR_KEY(key));
                      }
                      const obj = {
                        keyId: createKeyId(key),
                        pattern
                      };
                      if (auto) {
                        obj.searcher = createSearcher(pattern, options);
                      }
                      return obj;
                    }
                    let node = {
                      children: [],
                      operator: keys[0]
                    };
                    keys.forEach((key) => {
                      const value = query2[key];
                      if (isArray(value)) {
                        value.forEach((item) => {
                          node.children.push(next(item));
                        });
                      }
                    });
                    return node;
                  };
                  if (!isExpression(query)) {
                    query = convertToExplicit(query);
                  }
                  return next(query);
                }
                function computeScore(results, { ignoreFieldNorm = Config.ignoreFieldNorm }) {
                  results.forEach((result) => {
                    let totalScore = 1;
                    result.matches.forEach(({ key, norm: norm2, score }) => {
                      const weight = key ? key.weight : null;
                      totalScore *= Math.pow(
                        score === 0 && weight ? Number.EPSILON : score,
                        (weight || 1) * (ignoreFieldNorm ? 1 : norm2)
                      );
                    });
                    result.score = totalScore;
                  });
                }
                function transformMatches(result, data) {
                  const matches = result.matches;
                  data.matches = [];
                  if (!isDefined(matches)) {
                    return;
                  }
                  matches.forEach((match) => {
                    if (!isDefined(match.indices) || !match.indices.length) {
                      return;
                    }
                    const { indices, value } = match;
                    let obj = {
                      indices,
                      value
                    };
                    if (match.key) {
                      obj.key = match.key.src;
                    }
                    if (match.idx > -1) {
                      obj.refIndex = match.idx;
                    }
                    data.matches.push(obj);
                  });
                }
                function transformScore(result, data) {
                  data.score = result.score;
                }
                function format(results, docs, {
                  includeMatches = Config.includeMatches,
                  includeScore = Config.includeScore
                } = {}) {
                  const transformers = [];
                  if (includeMatches)
                    transformers.push(transformMatches);
                  if (includeScore)
                    transformers.push(transformScore);
                  return results.map((result) => {
                    const { idx } = result;
                    const data = {
                      item: docs[idx],
                      refIndex: idx
                    };
                    if (transformers.length) {
                      transformers.forEach((transformer) => {
                        transformer(result, data);
                      });
                    }
                    return data;
                  });
                }
                class Fuse {
                  constructor(docs, options = {}, index) {
                    this.options = { ...Config, ...options };
                    if (this.options.useExtendedSearch && false) {
                    }
                    this._keyStore = new KeyStore(this.options.keys);
                    this.setCollection(docs, index);
                  }
                  setCollection(docs, index) {
                    this._docs = docs;
                    if (index && !(index instanceof FuseIndex)) {
                      throw new Error(INCORRECT_INDEX_TYPE);
                    }
                    this._myIndex = index || createIndex(this.options.keys, this._docs, {
                      getFn: this.options.getFn,
                      fieldNormWeight: this.options.fieldNormWeight
                    });
                  }
                  add(doc) {
                    if (!isDefined(doc)) {
                      return;
                    }
                    this._docs.push(doc);
                    this._myIndex.add(doc);
                  }
                  remove(predicate = () => false) {
                    const results = [];
                    for (let i = 0, len = this._docs.length; i < len; i += 1) {
                      const doc = this._docs[i];
                      if (predicate(doc, i)) {
                        this.removeAt(i);
                        i -= 1;
                        len -= 1;
                        results.push(doc);
                      }
                    }
                    return results;
                  }
                  removeAt(idx) {
                    this._docs.splice(idx, 1);
                    this._myIndex.removeAt(idx);
                  }
                  getIndex() {
                    return this._myIndex;
                  }
                  search(query, { limit = -1 } = {}) {
                    const {
                      includeMatches,
                      includeScore,
                      shouldSort,
                      sortFn,
                      ignoreFieldNorm
                    } = this.options;
                    let results = isString(query) ? isString(this._docs[0]) ? this._searchStringList(query) : this._searchObjectList(query) : this._searchLogical(query);
                    computeScore(results, { ignoreFieldNorm });
                    if (shouldSort) {
                      results.sort(sortFn);
                    }
                    if (isNumber(limit) && limit > -1) {
                      results = results.slice(0, limit);
                    }
                    return format(results, this._docs, {
                      includeMatches,
                      includeScore
                    });
                  }
                  _searchStringList(query) {
                    const searcher = createSearcher(query, this.options);
                    const { records } = this._myIndex;
                    const results = [];
                    records.forEach(({ v: text, i: idx, n: norm2 }) => {
                      if (!isDefined(text)) {
                        return;
                      }
                      const { isMatch, score, indices } = searcher.searchIn(text);
                      if (isMatch) {
                        results.push({
                          item: text,
                          idx,
                          matches: [{ score, value: text, norm: norm2, indices }]
                        });
                      }
                    });
                    return results;
                  }
                  _searchLogical(query) {
                    const expression = parse(query, this.options);
                    const evaluate = (node, item, idx) => {
                      if (!node.children) {
                        const { keyId, searcher } = node;
                        const matches = this._findMatches({
                          key: this._keyStore.get(keyId),
                          value: this._myIndex.getValueForItemAtKeyId(item, keyId),
                          searcher
                        });
                        if (matches && matches.length) {
                          return [
                            {
                              idx,
                              item,
                              matches
                            }
                          ];
                        }
                        return [];
                      }
                      const res = [];
                      for (let i = 0, len = node.children.length; i < len; i += 1) {
                        const child = node.children[i];
                        const result = evaluate(child, item, idx);
                        if (result.length) {
                          res.push(...result);
                        } else if (node.operator === LogicalOperator.AND) {
                          return [];
                        }
                      }
                      return res;
                    };
                    const records = this._myIndex.records;
                    const resultMap = {};
                    const results = [];
                    records.forEach(({ $: item, i: idx }) => {
                      if (isDefined(item)) {
                        let expResults = evaluate(expression, item, idx);
                        if (expResults.length) {
                          if (!resultMap[idx]) {
                            resultMap[idx] = { idx, item, matches: [] };
                            results.push(resultMap[idx]);
                          }
                          expResults.forEach(({ matches }) => {
                            resultMap[idx].matches.push(...matches);
                          });
                        }
                      }
                    });
                    return results;
                  }
                  _searchObjectList(query) {
                    const searcher = createSearcher(query, this.options);
                    const { keys, records } = this._myIndex;
                    const results = [];
                    records.forEach(({ $: item, i: idx }) => {
                      if (!isDefined(item)) {
                        return;
                      }
                      let matches = [];
                      keys.forEach((key, keyIndex) => {
                        matches.push(
                          ...this._findMatches({
                            key,
                            value: item[keyIndex],
                            searcher
                          })
                        );
                      });
                      if (matches.length) {
                        results.push({
                          idx,
                          item,
                          matches
                        });
                      }
                    });
                    return results;
                  }
                  _findMatches({ key, value, searcher }) {
                    if (!isDefined(value)) {
                      return [];
                    }
                    let matches = [];
                    if (isArray(value)) {
                      value.forEach(({ v: text, i: idx, n: norm2 }) => {
                        if (!isDefined(text)) {
                          return;
                        }
                        const { isMatch, score, indices } = searcher.searchIn(text);
                        if (isMatch) {
                          matches.push({
                            score,
                            key,
                            value: text,
                            idx,
                            norm: norm2,
                            indices
                          });
                        }
                      });
                    } else {
                      const { v: text, n: norm2 } = value;
                      const { isMatch, score, indices } = searcher.searchIn(text);
                      if (isMatch) {
                        matches.push({ score, key, value: text, norm: norm2, indices });
                      }
                    }
                    return matches;
                  }
                }
                Fuse.version = "6.6.2";
                Fuse.createIndex = createIndex;
                Fuse.parseIndex = parseIndex;
                Fuse.config = Config;
                {
                  Fuse.parseQuery = parse;
                }
                {
                  register(ExtendedSearch);
                }
              }
            ),
            /***/
            791: (
              /***/
              function(__unused_webpack_module, __webpack_exports__2, __webpack_require__2) {
                __webpack_require__2.r(__webpack_exports__2);
                __webpack_require__2.d(__webpack_exports__2, {
                  "__DO_NOT_USE__ActionTypes": function() {
                    return (
                      /* binding */
                      ActionTypes
                    );
                  },
                  "applyMiddleware": function() {
                    return (
                      /* binding */
                      applyMiddleware
                    );
                  },
                  "bindActionCreators": function() {
                    return (
                      /* binding */
                      bindActionCreators
                    );
                  },
                  "combineReducers": function() {
                    return (
                      /* binding */
                      combineReducers
                    );
                  },
                  "compose": function() {
                    return (
                      /* binding */
                      compose
                    );
                  },
                  "createStore": function() {
                    return (
                      /* binding */
                      createStore
                    );
                  },
                  "legacy_createStore": function() {
                    return (
                      /* binding */
                      legacy_createStore
                    );
                  }
                });
                ;
                function _typeof(obj) {
                  "@babel/helpers - typeof";
                  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj2) {
                    return typeof obj2;
                  } : function(obj2) {
                    return obj2 && "function" == typeof Symbol && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
                  }, _typeof(obj);
                }
                ;
                function _toPrimitive(input, hint) {
                  if (_typeof(input) !== "object" || input === null)
                    return input;
                  var prim = input[Symbol.toPrimitive];
                  if (prim !== void 0) {
                    var res = prim.call(input, hint || "default");
                    if (_typeof(res) !== "object")
                      return res;
                    throw new TypeError("@@toPrimitive must return a primitive value.");
                  }
                  return (hint === "string" ? String : Number)(input);
                }
                ;
                function _toPropertyKey(arg) {
                  var key = _toPrimitive(arg, "string");
                  return _typeof(key) === "symbol" ? key : String(key);
                }
                ;
                function _defineProperty(obj, key, value) {
                  key = _toPropertyKey(key);
                  if (key in obj) {
                    Object.defineProperty(obj, key, {
                      value,
                      enumerable: true,
                      configurable: true,
                      writable: true
                    });
                  } else {
                    obj[key] = value;
                  }
                  return obj;
                }
                ;
                function ownKeys(object, enumerableOnly) {
                  var keys = Object.keys(object);
                  if (Object.getOwnPropertySymbols) {
                    var symbols = Object.getOwnPropertySymbols(object);
                    enumerableOnly && (symbols = symbols.filter(function(sym) {
                      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
                    })), keys.push.apply(keys, symbols);
                  }
                  return keys;
                }
                function _objectSpread2(target) {
                  for (var i = 1; i < arguments.length; i++) {
                    var source = null != arguments[i] ? arguments[i] : {};
                    i % 2 ? ownKeys(Object(source), true).forEach(function(key) {
                      _defineProperty(target, key, source[key]);
                    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function(key) {
                      Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
                    });
                  }
                  return target;
                }
                ;
                function formatProdErrorMessage(code) {
                  return "Minified Redux error #" + code + "; visit https://redux.js.org/Errors?code=" + code + " for the full message or use the non-minified dev environment for full errors. ";
                }
                var $$observable = function() {
                  return typeof Symbol === "function" && Symbol.observable || "@@observable";
                }();
                var randomString = function randomString2() {
                  return Math.random().toString(36).substring(7).split("").join(".");
                };
                var ActionTypes = {
                  INIT: "@@redux/INIT" + randomString(),
                  REPLACE: "@@redux/REPLACE" + randomString(),
                  PROBE_UNKNOWN_ACTION: function PROBE_UNKNOWN_ACTION() {
                    return "@@redux/PROBE_UNKNOWN_ACTION" + randomString();
                  }
                };
                function isPlainObject(obj) {
                  if (typeof obj !== "object" || obj === null)
                    return false;
                  var proto = obj;
                  while (Object.getPrototypeOf(proto) !== null) {
                    proto = Object.getPrototypeOf(proto);
                  }
                  return Object.getPrototypeOf(obj) === proto;
                }
                function miniKindOf(val) {
                  if (val === void 0)
                    return "undefined";
                  if (val === null)
                    return "null";
                  var type = typeof val;
                  switch (type) {
                    case "boolean":
                    case "string":
                    case "number":
                    case "symbol":
                    case "function": {
                      return type;
                    }
                  }
                  if (Array.isArray(val))
                    return "array";
                  if (isDate(val))
                    return "date";
                  if (isError(val))
                    return "error";
                  var constructorName = ctorName(val);
                  switch (constructorName) {
                    case "Symbol":
                    case "Promise":
                    case "WeakMap":
                    case "WeakSet":
                    case "Map":
                    case "Set":
                      return constructorName;
                  }
                  return type.slice(8, -1).toLowerCase().replace(/\s/g, "");
                }
                function ctorName(val) {
                  return typeof val.constructor === "function" ? val.constructor.name : null;
                }
                function isError(val) {
                  return val instanceof Error || typeof val.message === "string" && val.constructor && typeof val.constructor.stackTraceLimit === "number";
                }
                function isDate(val) {
                  if (val instanceof Date)
                    return true;
                  return typeof val.toDateString === "function" && typeof val.getDate === "function" && typeof val.setDate === "function";
                }
                function kindOf(val) {
                  var typeOfVal = typeof val;
                  if (false) {
                  }
                  return typeOfVal;
                }
                function createStore(reducer, preloadedState, enhancer) {
                  var _ref2;
                  if (typeof preloadedState === "function" && typeof enhancer === "function" || typeof enhancer === "function" && typeof arguments[3] === "function") {
                    throw new Error(true ? formatProdErrorMessage(0) : 0);
                  }
                  if (typeof preloadedState === "function" && typeof enhancer === "undefined") {
                    enhancer = preloadedState;
                    preloadedState = void 0;
                  }
                  if (typeof enhancer !== "undefined") {
                    if (typeof enhancer !== "function") {
                      throw new Error(true ? formatProdErrorMessage(1) : 0);
                    }
                    return enhancer(createStore)(reducer, preloadedState);
                  }
                  if (typeof reducer !== "function") {
                    throw new Error(true ? formatProdErrorMessage(2) : 0);
                  }
                  var currentReducer = reducer;
                  var currentState = preloadedState;
                  var currentListeners = [];
                  var nextListeners = currentListeners;
                  var isDispatching = false;
                  function ensureCanMutateNextListeners() {
                    if (nextListeners === currentListeners) {
                      nextListeners = currentListeners.slice();
                    }
                  }
                  function getState() {
                    if (isDispatching) {
                      throw new Error(true ? formatProdErrorMessage(3) : 0);
                    }
                    return currentState;
                  }
                  function subscribe(listener) {
                    if (typeof listener !== "function") {
                      throw new Error(true ? formatProdErrorMessage(4) : 0);
                    }
                    if (isDispatching) {
                      throw new Error(true ? formatProdErrorMessage(5) : 0);
                    }
                    var isSubscribed = true;
                    ensureCanMutateNextListeners();
                    nextListeners.push(listener);
                    return function unsubscribe() {
                      if (!isSubscribed) {
                        return;
                      }
                      if (isDispatching) {
                        throw new Error(true ? formatProdErrorMessage(6) : 0);
                      }
                      isSubscribed = false;
                      ensureCanMutateNextListeners();
                      var index = nextListeners.indexOf(listener);
                      nextListeners.splice(index, 1);
                      currentListeners = null;
                    };
                  }
                  function dispatch(action) {
                    if (!isPlainObject(action)) {
                      throw new Error(true ? formatProdErrorMessage(7) : 0);
                    }
                    if (typeof action.type === "undefined") {
                      throw new Error(true ? formatProdErrorMessage(8) : 0);
                    }
                    if (isDispatching) {
                      throw new Error(true ? formatProdErrorMessage(9) : 0);
                    }
                    try {
                      isDispatching = true;
                      currentState = currentReducer(currentState, action);
                    } finally {
                      isDispatching = false;
                    }
                    var listeners = currentListeners = nextListeners;
                    for (var i = 0; i < listeners.length; i++) {
                      var listener = listeners[i];
                      listener();
                    }
                    return action;
                  }
                  function replaceReducer(nextReducer) {
                    if (typeof nextReducer !== "function") {
                      throw new Error(true ? formatProdErrorMessage(10) : 0);
                    }
                    currentReducer = nextReducer;
                    dispatch({
                      type: ActionTypes.REPLACE
                    });
                  }
                  function observable() {
                    var _ref;
                    var outerSubscribe = subscribe;
                    return _ref = {
                      /**
                       * The minimal observable subscription method.
                       * @param {Object} observer Any object that can be used as an observer.
                       * The observer object should have a `next` method.
                       * @returns {subscription} An object with an `unsubscribe` method that can
                       * be used to unsubscribe the observable from the store, and prevent further
                       * emission of values from the observable.
                       */
                      subscribe: function subscribe2(observer) {
                        if (typeof observer !== "object" || observer === null) {
                          throw new Error(true ? formatProdErrorMessage(11) : 0);
                        }
                        function observeState() {
                          if (observer.next) {
                            observer.next(getState());
                          }
                        }
                        observeState();
                        var unsubscribe = outerSubscribe(observeState);
                        return {
                          unsubscribe
                        };
                      }
                    }, _ref[$$observable] = function() {
                      return this;
                    }, _ref;
                  }
                  dispatch({
                    type: ActionTypes.INIT
                  });
                  return _ref2 = {
                    dispatch,
                    subscribe,
                    getState,
                    replaceReducer
                  }, _ref2[$$observable] = observable, _ref2;
                }
                var legacy_createStore = createStore;
                function warning(message) {
                  if (typeof console !== "undefined" && typeof console.error === "function") {
                    console.error(message);
                  }
                  try {
                    throw new Error(message);
                  } catch (e) {
                  }
                }
                function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
                  var reducerKeys = Object.keys(reducers);
                  var argumentName = action && action.type === ActionTypes.INIT ? "preloadedState argument passed to createStore" : "previous state received by the reducer";
                  if (reducerKeys.length === 0) {
                    return "Store does not have a valid reducer. Make sure the argument passed to combineReducers is an object whose values are reducers.";
                  }
                  if (!isPlainObject(inputState)) {
                    return "The " + argumentName + ' has unexpected type of "' + kindOf(inputState) + '". Expected argument to be an object with the following ' + ('keys: "' + reducerKeys.join('", "') + '"');
                  }
                  var unexpectedKeys = Object.keys(inputState).filter(function(key) {
                    return !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key];
                  });
                  unexpectedKeys.forEach(function(key) {
                    unexpectedKeyCache[key] = true;
                  });
                  if (action && action.type === ActionTypes.REPLACE)
                    return;
                  if (unexpectedKeys.length > 0) {
                    return "Unexpected " + (unexpectedKeys.length > 1 ? "keys" : "key") + " " + ('"' + unexpectedKeys.join('", "') + '" found in ' + argumentName + ". ") + "Expected to find one of the known reducer keys instead: " + ('"' + reducerKeys.join('", "') + '". Unexpected keys will be ignored.');
                  }
                }
                function assertReducerShape(reducers) {
                  Object.keys(reducers).forEach(function(key) {
                    var reducer = reducers[key];
                    var initialState = reducer(void 0, {
                      type: ActionTypes.INIT
                    });
                    if (typeof initialState === "undefined") {
                      throw new Error(true ? formatProdErrorMessage(12) : 0);
                    }
                    if (typeof reducer(void 0, {
                      type: ActionTypes.PROBE_UNKNOWN_ACTION()
                    }) === "undefined") {
                      throw new Error(true ? formatProdErrorMessage(13) : 0);
                    }
                  });
                }
                function combineReducers(reducers) {
                  var reducerKeys = Object.keys(reducers);
                  var finalReducers = {};
                  for (var i = 0; i < reducerKeys.length; i++) {
                    var key = reducerKeys[i];
                    if (false) {
                    }
                    if (typeof reducers[key] === "function") {
                      finalReducers[key] = reducers[key];
                    }
                  }
                  var finalReducerKeys = Object.keys(finalReducers);
                  var unexpectedKeyCache;
                  if (false) {
                  }
                  var shapeAssertionError;
                  try {
                    assertReducerShape(finalReducers);
                  } catch (e) {
                    shapeAssertionError = e;
                  }
                  return function combination(state, action) {
                    if (state === void 0) {
                      state = {};
                    }
                    if (shapeAssertionError) {
                      throw shapeAssertionError;
                    }
                    if (false) {
                      var warningMessage;
                    }
                    var hasChanged = false;
                    var nextState = {};
                    for (var _i = 0; _i < finalReducerKeys.length; _i++) {
                      var _key = finalReducerKeys[_i];
                      var reducer = finalReducers[_key];
                      var previousStateForKey = state[_key];
                      var nextStateForKey = reducer(previousStateForKey, action);
                      if (typeof nextStateForKey === "undefined") {
                        var actionType = action && action.type;
                        throw new Error(true ? formatProdErrorMessage(14) : 0);
                      }
                      nextState[_key] = nextStateForKey;
                      hasChanged = hasChanged || nextStateForKey !== previousStateForKey;
                    }
                    hasChanged = hasChanged || finalReducerKeys.length !== Object.keys(state).length;
                    return hasChanged ? nextState : state;
                  };
                }
                function bindActionCreator(actionCreator, dispatch) {
                  return function() {
                    return dispatch(actionCreator.apply(this, arguments));
                  };
                }
                function bindActionCreators(actionCreators, dispatch) {
                  if (typeof actionCreators === "function") {
                    return bindActionCreator(actionCreators, dispatch);
                  }
                  if (typeof actionCreators !== "object" || actionCreators === null) {
                    throw new Error(true ? formatProdErrorMessage(16) : 0);
                  }
                  var boundActionCreators = {};
                  for (var key in actionCreators) {
                    var actionCreator = actionCreators[key];
                    if (typeof actionCreator === "function") {
                      boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
                    }
                  }
                  return boundActionCreators;
                }
                function compose() {
                  for (var _len = arguments.length, funcs = new Array(_len), _key = 0; _key < _len; _key++) {
                    funcs[_key] = arguments[_key];
                  }
                  if (funcs.length === 0) {
                    return function(arg) {
                      return arg;
                    };
                  }
                  if (funcs.length === 1) {
                    return funcs[0];
                  }
                  return funcs.reduce(function(a, b) {
                    return function() {
                      return a(b.apply(void 0, arguments));
                    };
                  });
                }
                function applyMiddleware() {
                  for (var _len = arguments.length, middlewares = new Array(_len), _key = 0; _key < _len; _key++) {
                    middlewares[_key] = arguments[_key];
                  }
                  return function(createStore2) {
                    return function() {
                      var store = createStore2.apply(void 0, arguments);
                      var _dispatch = function dispatch() {
                        throw new Error(true ? formatProdErrorMessage(15) : 0);
                      };
                      var middlewareAPI = {
                        getState: store.getState,
                        dispatch: function dispatch() {
                          return _dispatch.apply(void 0, arguments);
                        }
                      };
                      var chain = middlewares.map(function(middleware) {
                        return middleware(middlewareAPI);
                      });
                      _dispatch = compose.apply(void 0, chain)(store.dispatch);
                      return _objectSpread2(_objectSpread2({}, store), {}, {
                        dispatch: _dispatch
                      });
                    };
                  };
                }
                function isCrushed() {
                }
                if (false) {
                }
              }
            )
            /******/
          };
          var __webpack_module_cache__ = {};
          function __webpack_require__(moduleId) {
            var cachedModule = __webpack_module_cache__[moduleId];
            if (cachedModule !== void 0) {
              return cachedModule.exports;
            }
            var module2 = __webpack_module_cache__[moduleId] = {
              /******/
              // no module.id needed
              /******/
              // no module.loaded needed
              /******/
              exports: {}
              /******/
            };
            __webpack_modules__[moduleId].call(module2.exports, module2, module2.exports, __webpack_require__);
            return module2.exports;
          }
          !function() {
            __webpack_require__.n = function(module2) {
              var getter = module2 && module2.__esModule ? (
                /******/
                function() {
                  return module2["default"];
                }
              ) : (
                /******/
                function() {
                  return module2;
                }
              );
              __webpack_require__.d(getter, { a: getter });
              return getter;
            };
          }();
          !function() {
            __webpack_require__.d = function(exports2, definition) {
              for (var key in definition) {
                if (__webpack_require__.o(definition, key) && !__webpack_require__.o(exports2, key)) {
                  Object.defineProperty(exports2, key, { enumerable: true, get: definition[key] });
                }
              }
            };
          }();
          !function() {
            __webpack_require__.o = function(obj, prop) {
              return Object.prototype.hasOwnProperty.call(obj, prop);
            };
          }();
          !function() {
            __webpack_require__.r = function(exports2) {
              if (typeof Symbol !== "undefined" && Symbol.toStringTag) {
                Object.defineProperty(exports2, Symbol.toStringTag, { value: "Module" });
              }
              Object.defineProperty(exports2, "__esModule", { value: true });
            };
          }();
          var __webpack_exports__ = {};
          !function() {
            var _scripts_choices__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(373);
            var _scripts_choices__WEBPACK_IMPORTED_MODULE_0___default = /* @__PURE__ */ __webpack_require__.n(_scripts_choices__WEBPACK_IMPORTED_MODULE_0__);
            var _scripts_interfaces__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(187);
            var _scripts_interfaces__WEBPACK_IMPORTED_MODULE_1___default = /* @__PURE__ */ __webpack_require__.n(_scripts_interfaces__WEBPACK_IMPORTED_MODULE_1__);
            var _scripts_constants__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(883);
            var _scripts_defaults__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(789);
            var _scripts_templates__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(686);
            __webpack_exports__["default"] = _scripts_choices__WEBPACK_IMPORTED_MODULE_0___default();
          }();
          __webpack_exports__ = __webpack_exports__["default"];
          return __webpack_exports__;
        }()
      );
    });
  }
});

// packages/forms/resources/js/components/select.js
var import_choices = __toESM(require_choices());
function selectFormComponent({
  isHtmlAllowed,
  getOptionLabelUsing,
  getOptionLabelsUsing,
  getOptionsUsing,
  getSearchResultsUsing,
  isAutofocused,
  isDisabled,
  isMultiple,
  hasDynamicOptions,
  hasDynamicSearchResults,
  livewireId,
  loadingMessage,
  maxItems,
  maxItemsMessage,
  noSearchResultsMessage,
  options,
  optionsLimit,
  placeholder,
  position,
  searchDebounce,
  searchingMessage,
  searchPrompt,
  state,
  statePath
}) {
  return {
    isSearching: false,
    select: null,
    selectedOptions: [],
    isStateBeingUpdated: false,
    state,
    init: async function() {
      this.select = new import_choices.default(this.$refs.input, {
        allowHTML: isHtmlAllowed,
        duplicateItemsAllowed: false,
        itemSelectText: "",
        loadingText: loadingMessage,
        maxItemCount: maxItems ?? -1,
        maxItemText: (maxItemCount) => window.pluralize(maxItemsMessage, maxItemCount, {
          count: maxItemCount
        }),
        noChoicesText: searchPrompt,
        noResultsText: noSearchResultsMessage,
        placeholderValue: placeholder,
        position: position ?? "auto",
        removeItemButton: true,
        renderChoiceLimit: optionsLimit,
        searchFields: ["label"],
        searchPlaceholderValue: searchPrompt,
        searchResultLimit: optionsLimit,
        shouldSort: false
      });
      await this.refreshChoices({ withInitialOptions: true });
      if (![null, void 0, ""].includes(this.state)) {
        this.select.setChoiceByValue(this.formatState(this.state));
      }
      this.refreshPlaceholder();
      if (isAutofocused) {
        this.select.showDropdown();
      }
      this.$refs.input.addEventListener("change", () => {
        this.refreshPlaceholder();
        if (this.isStateBeingUpdated) {
          return;
        }
        this.isStateBeingUpdated = true;
        this.state = this.select.getValue(true) ?? null;
        this.$nextTick(() => this.isStateBeingUpdated = false);
      });
      if (hasDynamicOptions) {
        this.$refs.input.addEventListener("showDropdown", async () => {
          this.select.clearChoices();
          await this.select.setChoices([
            {
              label: loadingMessage,
              value: "",
              disabled: true
            }
          ]);
          await this.refreshChoices();
        });
      }
      if (hasDynamicSearchResults) {
        this.$refs.input.addEventListener("search", async (event) => {
          let search = event.detail.value?.trim();
          if ([null, void 0, ""].includes(search)) {
            return;
          }
          this.isSearching = true;
          this.select.clearChoices();
          await this.select.setChoices([
            {
              label: searchingMessage,
              value: "",
              disabled: true
            }
          ]);
        });
        this.$refs.input.addEventListener(
          "search",
          Alpine.debounce(async (event) => {
            await this.refreshChoices({
              search: event.detail.value?.trim()
            });
            this.isSearching = false;
          }, searchDebounce)
        );
      }
      if (!isMultiple) {
        window.addEventListener(
          "filament-forms::select/refreshSelectedOptionLabel",
          async (event) => {
            if (event.detail.livewireId !== livewireId) {
              return;
            }
            if (event.detail.statePath !== statePath) {
              return;
            }
            await this.refreshSelectedOption();
          }
        );
      }
      this.$watch("state", async () => {
        if (!this.select) {
          return;
        }
        this.refreshPlaceholder();
        if (this.isStateBeingUpdated) {
          return;
        }
        await this.refreshSelectedOption();
      });
    },
    destroy: function() {
      this.select.destroy();
      this.select = null;
    },
    refreshChoices: async function(config = {}) {
      this.setChoices(await this.getChoices(config));
    },
    refreshSelectedOption: async function() {
      const choices = await this.getChoices({
        withInitialOptions: !hasDynamicOptions
      });
      this.select.clearStore();
      this.setChoices(choices);
      if (![null, void 0, ""].includes(this.state)) {
        this.select.setChoiceByValue(this.formatState(this.state));
      }
    },
    setChoices: function(choices) {
      this.select.setChoices(choices, "value", "label", true);
    },
    getChoices: async function(config = {}) {
      const existingOptions = await this.getOptions(config);
      return existingOptions.concat(
        await this.getMissingOptions(existingOptions)
      );
    },
    getOptions: async function({ search, withInitialOptions }) {
      if (withInitialOptions) {
        return options;
      }
      if (search !== "" && search !== null && search !== void 0) {
        return await getSearchResultsUsing(search);
      }
      return await getOptionsUsing();
    },
    refreshPlaceholder: function() {
      if (isDisabled) {
        return;
      }
      if (isMultiple) {
        return;
      }
      this.select._renderItems();
      if (![null, void 0, ""].includes(this.state)) {
        return;
      }
      this.$el.querySelector(
        ".choices__list--single"
      ).innerHTML = `<div class="choices__placeholder choices__item">${placeholder}</div>`;
    },
    formatState: function(state2) {
      if (isMultiple) {
        return (state2 ?? []).map((item) => item?.toString());
      }
      return state2?.toString();
    },
    getMissingOptions: async function(existingOptions) {
      let state2 = this.formatState(this.state);
      if ([null, void 0, "", [], {}].includes(state2)) {
        return {};
      }
      const existingOptionValues = new Set(
        existingOptions.length ? existingOptions.map((option) => option.value) : []
      );
      if (isMultiple) {
        if (state2.every((value) => existingOptionValues.has(value))) {
          return {};
        }
        return await getOptionLabelsUsing();
      }
      if (existingOptionValues.has(state2)) {
        return existingOptionValues;
      }
      return [
        {
          label: await getOptionLabelUsing(),
          value: state2
        }
      ];
    }
  };
}
export {
  selectFormComponent as default
};
/*! Bundled license information:

choices.js/public/assets/scripts/choices.js:
  (*! choices.js v10.2.0 | © 2022 Josh Johnson | https://github.com/jshjohnson/Choices#readme *)
*/
