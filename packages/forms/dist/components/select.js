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
var import_choices = __toESM(require_choices(), 1);
function selectFormComponent({
  canSelectPlaceholder,
  isHtmlAllowed,
  getOptionLabelUsing,
  getOptionLabelsUsing,
  getOptionsUsing,
  getSearchResultsUsing,
  isAutofocused,
  isMultiple,
  isSearchable,
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
  searchableOptionFields,
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
        removeItemButton: canSelectPlaceholder,
        renderChoiceLimit: optionsLimit,
        searchEnabled: isSearchable,
        searchFields: searchableOptionFields ?? ["label"],
        searchPlaceholderValue: searchPrompt,
        searchResultLimit: optionsLimit,
        shouldSort: false,
        searchFloor: hasDynamicSearchResults ? 0 : 1
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
          this.isSearching = true;
          this.select.clearChoices();
          await this.select.setChoices([
            {
              label: [null, void 0, ""].includes(search) ? loadingMessage : searchingMessage,
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
          "filament-forms::select.refreshSelectedOptionLabel",
          async (event) => {
            if (event.detail.livewireId !== livewireId) {
              return;
            }
            if (event.detail.statePath !== statePath) {
              return;
            }
            await this.refreshChoices({
              withInitialOptions: !hasDynamicOptions
            });
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
        await this.refreshChoices({
          withInitialOptions: !hasDynamicOptions
        });
      });
    },
    destroy: function() {
      this.select.destroy();
      this.select = null;
    },
    refreshChoices: async function(config = {}) {
      const choices = await this.getChoices(config);
      this.select.clearStore();
      this.refreshPlaceholder();
      this.setChoices(choices);
      if (![null, void 0, ""].includes(this.state)) {
        this.select.setChoiceByValue(this.formatState(this.state));
      }
    },
    setChoices: function(choices) {
      this.select.setChoices(choices, "value", "label", true);
    },
    getChoices: async function(config = {}) {
      const existingOptions = await this.getExistingOptions(config);
      return existingOptions.concat(
        await this.getMissingOptions(existingOptions)
      );
    },
    getExistingOptions: async function({ search, withInitialOptions }) {
      if (withInitialOptions) {
        return options;
      }
      let results = [];
      if (search !== "" && search !== null && search !== void 0) {
        results = await getSearchResultsUsing(search);
      } else {
        results = await getOptionsUsing();
      }
      return results.map(
        (option) => Array.isArray(this.state) && this.state.includes(option.value) ? ((option2) => {
          option2.selected = true;
          return option2;
        })(option) : option
      );
    },
    refreshPlaceholder: function() {
      if (isMultiple) {
        return;
      }
      this.select._renderItems();
      if (![null, void 0, ""].includes(this.state)) {
        return;
      }
      this.$el.querySelector(
        ".choices__list--single"
      ).innerHTML = `<div class="choices__placeholder choices__item">${placeholder ?? ""}</div>`;
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
        return (await getOptionLabelsUsing()).filter((option) => !existingOptionValues.has(option.value)).map((option) => {
          option.selected = true;
          return option;
        });
      }
      if (existingOptionValues.has(state2)) {
        return existingOptionValues;
      }
      return [
        {
          label: await getOptionLabelUsing(),
          value: state2,
          selected: true
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2Nob2ljZXMuanMvcHVibGljL2Fzc2V0cy9zY3JpcHRzL2Nob2ljZXMuanMiLCAiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvc2VsZWN0LmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvKiEgY2hvaWNlcy5qcyB2MTAuMi4wIHwgXHUwMEE5IDIwMjIgSm9zaCBKb2huc29uIHwgaHR0cHM6Ly9naXRodWIuY29tL2pzaGpvaG5zb24vQ2hvaWNlcyNyZWFkbWUgKi9cbihmdW5jdGlvbiB3ZWJwYWNrVW5pdmVyc2FsTW9kdWxlRGVmaW5pdGlvbihyb290LCBmYWN0b3J5KSB7XG5cdGlmKHR5cGVvZiBleHBvcnRzID09PSAnb2JqZWN0JyAmJiB0eXBlb2YgbW9kdWxlID09PSAnb2JqZWN0Jylcblx0XHRtb2R1bGUuZXhwb3J0cyA9IGZhY3RvcnkoKTtcblx0ZWxzZSBpZih0eXBlb2YgZGVmaW5lID09PSAnZnVuY3Rpb24nICYmIGRlZmluZS5hbWQpXG5cdFx0ZGVmaW5lKFtdLCBmYWN0b3J5KTtcblx0ZWxzZSBpZih0eXBlb2YgZXhwb3J0cyA9PT0gJ29iamVjdCcpXG5cdFx0ZXhwb3J0c1tcIkNob2ljZXNcIl0gPSBmYWN0b3J5KCk7XG5cdGVsc2Vcblx0XHRyb290W1wiQ2hvaWNlc1wiXSA9IGZhY3RvcnkoKTtcbn0pKHdpbmRvdywgZnVuY3Rpb24oKSB7XG5yZXR1cm4gLyoqKioqKi8gKGZ1bmN0aW9uKCkgeyAvLyB3ZWJwYWNrQm9vdHN0cmFwXG4vKioqKioqLyBcdFwidXNlIHN0cmljdFwiO1xuLyoqKioqKi8gXHR2YXIgX193ZWJwYWNrX21vZHVsZXNfXyA9ICh7XG5cbi8qKiovIDI4Mjpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuZXhwb3J0cy5jbGVhckNob2ljZXMgPSBleHBvcnRzLmFjdGl2YXRlQ2hvaWNlcyA9IGV4cG9ydHMuZmlsdGVyQ2hvaWNlcyA9IGV4cG9ydHMuYWRkQ2hvaWNlID0gdm9pZCAwO1xudmFyIGNvbnN0YW50c18xID0gX193ZWJwYWNrX3JlcXVpcmVfXyg4ODMpO1xudmFyIGFkZENob2ljZSA9IGZ1bmN0aW9uIChfYSkge1xuICB2YXIgdmFsdWUgPSBfYS52YWx1ZSxcbiAgICBsYWJlbCA9IF9hLmxhYmVsLFxuICAgIGlkID0gX2EuaWQsXG4gICAgZ3JvdXBJZCA9IF9hLmdyb3VwSWQsXG4gICAgZGlzYWJsZWQgPSBfYS5kaXNhYmxlZCxcbiAgICBlbGVtZW50SWQgPSBfYS5lbGVtZW50SWQsXG4gICAgY3VzdG9tUHJvcGVydGllcyA9IF9hLmN1c3RvbVByb3BlcnRpZXMsXG4gICAgcGxhY2Vob2xkZXIgPSBfYS5wbGFjZWhvbGRlcixcbiAgICBrZXlDb2RlID0gX2Eua2V5Q29kZTtcbiAgcmV0dXJuIHtcbiAgICB0eXBlOiBjb25zdGFudHNfMS5BQ1RJT05fVFlQRVMuQUREX0NIT0lDRSxcbiAgICB2YWx1ZTogdmFsdWUsXG4gICAgbGFiZWw6IGxhYmVsLFxuICAgIGlkOiBpZCxcbiAgICBncm91cElkOiBncm91cElkLFxuICAgIGRpc2FibGVkOiBkaXNhYmxlZCxcbiAgICBlbGVtZW50SWQ6IGVsZW1lbnRJZCxcbiAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjdXN0b21Qcm9wZXJ0aWVzLFxuICAgIHBsYWNlaG9sZGVyOiBwbGFjZWhvbGRlcixcbiAgICBrZXlDb2RlOiBrZXlDb2RlXG4gIH07XG59O1xuZXhwb3J0cy5hZGRDaG9pY2UgPSBhZGRDaG9pY2U7XG52YXIgZmlsdGVyQ2hvaWNlcyA9IGZ1bmN0aW9uIChyZXN1bHRzKSB7XG4gIHJldHVybiB7XG4gICAgdHlwZTogY29uc3RhbnRzXzEuQUNUSU9OX1RZUEVTLkZJTFRFUl9DSE9JQ0VTLFxuICAgIHJlc3VsdHM6IHJlc3VsdHNcbiAgfTtcbn07XG5leHBvcnRzLmZpbHRlckNob2ljZXMgPSBmaWx0ZXJDaG9pY2VzO1xudmFyIGFjdGl2YXRlQ2hvaWNlcyA9IGZ1bmN0aW9uIChhY3RpdmUpIHtcbiAgaWYgKGFjdGl2ZSA9PT0gdm9pZCAwKSB7XG4gICAgYWN0aXZlID0gdHJ1ZTtcbiAgfVxuICByZXR1cm4ge1xuICAgIHR5cGU6IGNvbnN0YW50c18xLkFDVElPTl9UWVBFUy5BQ1RJVkFURV9DSE9JQ0VTLFxuICAgIGFjdGl2ZTogYWN0aXZlXG4gIH07XG59O1xuZXhwb3J0cy5hY3RpdmF0ZUNob2ljZXMgPSBhY3RpdmF0ZUNob2ljZXM7XG52YXIgY2xlYXJDaG9pY2VzID0gZnVuY3Rpb24gKCkge1xuICByZXR1cm4ge1xuICAgIHR5cGU6IGNvbnN0YW50c18xLkFDVElPTl9UWVBFUy5DTEVBUl9DSE9JQ0VTXG4gIH07XG59O1xuZXhwb3J0cy5jbGVhckNob2ljZXMgPSBjbGVhckNob2ljZXM7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA3ODM6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbmV4cG9ydHMuYWRkR3JvdXAgPSB2b2lkIDA7XG52YXIgY29uc3RhbnRzXzEgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDg4Myk7XG52YXIgYWRkR3JvdXAgPSBmdW5jdGlvbiAoX2EpIHtcbiAgdmFyIHZhbHVlID0gX2EudmFsdWUsXG4gICAgaWQgPSBfYS5pZCxcbiAgICBhY3RpdmUgPSBfYS5hY3RpdmUsXG4gICAgZGlzYWJsZWQgPSBfYS5kaXNhYmxlZDtcbiAgcmV0dXJuIHtcbiAgICB0eXBlOiBjb25zdGFudHNfMS5BQ1RJT05fVFlQRVMuQUREX0dST1VQLFxuICAgIHZhbHVlOiB2YWx1ZSxcbiAgICBpZDogaWQsXG4gICAgYWN0aXZlOiBhY3RpdmUsXG4gICAgZGlzYWJsZWQ6IGRpc2FibGVkXG4gIH07XG59O1xuZXhwb3J0cy5hZGRHcm91cCA9IGFkZEdyb3VwO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gNDY0OlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5leHBvcnRzLmhpZ2hsaWdodEl0ZW0gPSBleHBvcnRzLnJlbW92ZUl0ZW0gPSBleHBvcnRzLmFkZEl0ZW0gPSB2b2lkIDA7XG52YXIgY29uc3RhbnRzXzEgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDg4Myk7XG52YXIgYWRkSXRlbSA9IGZ1bmN0aW9uIChfYSkge1xuICB2YXIgdmFsdWUgPSBfYS52YWx1ZSxcbiAgICBsYWJlbCA9IF9hLmxhYmVsLFxuICAgIGlkID0gX2EuaWQsXG4gICAgY2hvaWNlSWQgPSBfYS5jaG9pY2VJZCxcbiAgICBncm91cElkID0gX2EuZ3JvdXBJZCxcbiAgICBjdXN0b21Qcm9wZXJ0aWVzID0gX2EuY3VzdG9tUHJvcGVydGllcyxcbiAgICBwbGFjZWhvbGRlciA9IF9hLnBsYWNlaG9sZGVyLFxuICAgIGtleUNvZGUgPSBfYS5rZXlDb2RlO1xuICByZXR1cm4ge1xuICAgIHR5cGU6IGNvbnN0YW50c18xLkFDVElPTl9UWVBFUy5BRERfSVRFTSxcbiAgICB2YWx1ZTogdmFsdWUsXG4gICAgbGFiZWw6IGxhYmVsLFxuICAgIGlkOiBpZCxcbiAgICBjaG9pY2VJZDogY2hvaWNlSWQsXG4gICAgZ3JvdXBJZDogZ3JvdXBJZCxcbiAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjdXN0b21Qcm9wZXJ0aWVzLFxuICAgIHBsYWNlaG9sZGVyOiBwbGFjZWhvbGRlcixcbiAgICBrZXlDb2RlOiBrZXlDb2RlXG4gIH07XG59O1xuZXhwb3J0cy5hZGRJdGVtID0gYWRkSXRlbTtcbnZhciByZW1vdmVJdGVtID0gZnVuY3Rpb24gKGlkLCBjaG9pY2VJZCkge1xuICByZXR1cm4ge1xuICAgIHR5cGU6IGNvbnN0YW50c18xLkFDVElPTl9UWVBFUy5SRU1PVkVfSVRFTSxcbiAgICBpZDogaWQsXG4gICAgY2hvaWNlSWQ6IGNob2ljZUlkXG4gIH07XG59O1xuZXhwb3J0cy5yZW1vdmVJdGVtID0gcmVtb3ZlSXRlbTtcbnZhciBoaWdobGlnaHRJdGVtID0gZnVuY3Rpb24gKGlkLCBoaWdobGlnaHRlZCkge1xuICByZXR1cm4ge1xuICAgIHR5cGU6IGNvbnN0YW50c18xLkFDVElPTl9UWVBFUy5ISUdITElHSFRfSVRFTSxcbiAgICBpZDogaWQsXG4gICAgaGlnaGxpZ2h0ZWQ6IGhpZ2hsaWdodGVkXG4gIH07XG59O1xuZXhwb3J0cy5oaWdobGlnaHRJdGVtID0gaGlnaGxpZ2h0SXRlbTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDEzNzpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuZXhwb3J0cy5zZXRJc0xvYWRpbmcgPSBleHBvcnRzLnJlc2V0VG8gPSBleHBvcnRzLmNsZWFyQWxsID0gdm9pZCAwO1xudmFyIGNvbnN0YW50c18xID0gX193ZWJwYWNrX3JlcXVpcmVfXyg4ODMpO1xudmFyIGNsZWFyQWxsID0gZnVuY3Rpb24gKCkge1xuICByZXR1cm4ge1xuICAgIHR5cGU6IGNvbnN0YW50c18xLkFDVElPTl9UWVBFUy5DTEVBUl9BTExcbiAgfTtcbn07XG5leHBvcnRzLmNsZWFyQWxsID0gY2xlYXJBbGw7XG52YXIgcmVzZXRUbyA9IGZ1bmN0aW9uIChzdGF0ZSkge1xuICByZXR1cm4ge1xuICAgIHR5cGU6IGNvbnN0YW50c18xLkFDVElPTl9UWVBFUy5SRVNFVF9UTyxcbiAgICBzdGF0ZTogc3RhdGVcbiAgfTtcbn07XG5leHBvcnRzLnJlc2V0VG8gPSByZXNldFRvO1xudmFyIHNldElzTG9hZGluZyA9IGZ1bmN0aW9uIChpc0xvYWRpbmcpIHtcbiAgcmV0dXJuIHtcbiAgICB0eXBlOiBjb25zdGFudHNfMS5BQ1RJT05fVFlQRVMuU0VUX0lTX0xPQURJTkcsXG4gICAgaXNMb2FkaW5nOiBpc0xvYWRpbmdcbiAgfTtcbn07XG5leHBvcnRzLnNldElzTG9hZGluZyA9IHNldElzTG9hZGluZztcblxuLyoqKi8gfSksXG5cbi8qKiovIDM3Mzpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXykge1xuXG5cblxudmFyIF9fc3ByZWFkQXJyYXkgPSB0aGlzICYmIHRoaXMuX19zcHJlYWRBcnJheSB8fCBmdW5jdGlvbiAodG8sIGZyb20sIHBhY2spIHtcbiAgaWYgKHBhY2sgfHwgYXJndW1lbnRzLmxlbmd0aCA9PT0gMikgZm9yICh2YXIgaSA9IDAsIGwgPSBmcm9tLmxlbmd0aCwgYXI7IGkgPCBsOyBpKyspIHtcbiAgICBpZiAoYXIgfHwgIShpIGluIGZyb20pKSB7XG4gICAgICBpZiAoIWFyKSBhciA9IEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKGZyb20sIDAsIGkpO1xuICAgICAgYXJbaV0gPSBmcm9tW2ldO1xuICAgIH1cbiAgfVxuICByZXR1cm4gdG8uY29uY2F0KGFyIHx8IEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKGZyb20pKTtcbn07XG52YXIgX19pbXBvcnREZWZhdWx0ID0gdGhpcyAmJiB0aGlzLl9faW1wb3J0RGVmYXVsdCB8fCBmdW5jdGlvbiAobW9kKSB7XG4gIHJldHVybiBtb2QgJiYgbW9kLl9fZXNNb2R1bGUgPyBtb2QgOiB7XG4gICAgXCJkZWZhdWx0XCI6IG1vZFxuICB9O1xufTtcbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbnZhciBkZWVwbWVyZ2VfMSA9IF9faW1wb3J0RGVmYXVsdChfX3dlYnBhY2tfcmVxdWlyZV9fKDk5NikpO1xuLyogZXNsaW50LWRpc2FibGUgQHR5cGVzY3JpcHQtZXNsaW50L25vLWV4cGxpY2l0LWFueSAqL1xudmFyIGZ1c2VfanNfMSA9IF9faW1wb3J0RGVmYXVsdChfX3dlYnBhY2tfcmVxdWlyZV9fKDIyMSkpO1xudmFyIGNob2ljZXNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oMjgyKTtcbnZhciBncm91cHNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oNzgzKTtcbnZhciBpdGVtc18xID0gX193ZWJwYWNrX3JlcXVpcmVfXyg0NjQpO1xudmFyIG1pc2NfMSA9IF9fd2VicGFja19yZXF1aXJlX18oMTM3KTtcbnZhciBjb21wb25lbnRzXzEgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDUyMCk7XG52YXIgY29uc3RhbnRzXzEgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDg4Myk7XG52YXIgZGVmYXVsdHNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oNzg5KTtcbnZhciB1dGlsc18xID0gX193ZWJwYWNrX3JlcXVpcmVfXyg3OTkpO1xudmFyIHJlZHVjZXJzXzEgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDY1NSk7XG52YXIgc3RvcmVfMSA9IF9faW1wb3J0RGVmYXVsdChfX3dlYnBhY2tfcmVxdWlyZV9fKDc0NCkpO1xudmFyIHRlbXBsYXRlc18xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oNjg2KSk7XG4vKiogQHNlZSB7QGxpbmsgaHR0cDovL2Jyb3dzZXJoYWNrcy5jb20vI2hhY2stYWNlYTA3NWQwYWM2OTU0ZjI3NWE3MDAyMzkwNjA1MGN9ICovXG52YXIgSVNfSUUxMSA9ICctbXMtc2Nyb2xsLWxpbWl0JyBpbiBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuc3R5bGUgJiYgJy1tcy1pbWUtYWxpZ24nIGluIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5zdHlsZTtcbnZhciBVU0VSX0RFRkFVTFRTID0ge307XG4vKipcbiAqIENob2ljZXNcbiAqIEBhdXRob3IgSm9zaCBKb2huc29uPGpvc2hAam9zaHVham9obnNvbi5jby51az5cbiAqL1xudmFyIENob2ljZXMgPSAvKiogQGNsYXNzICovZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBDaG9pY2VzKGVsZW1lbnQsIHVzZXJDb25maWcpIHtcbiAgICBpZiAoZWxlbWVudCA9PT0gdm9pZCAwKSB7XG4gICAgICBlbGVtZW50ID0gJ1tkYXRhLWNob2ljZV0nO1xuICAgIH1cbiAgICBpZiAodXNlckNvbmZpZyA9PT0gdm9pZCAwKSB7XG4gICAgICB1c2VyQ29uZmlnID0ge307XG4gICAgfVxuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgaWYgKHVzZXJDb25maWcuYWxsb3dIVE1MID09PSB1bmRlZmluZWQpIHtcbiAgICAgIGNvbnNvbGUud2FybignRGVwcmVjYXRpb24gd2FybmluZzogYWxsb3dIVE1MIHdpbGwgZGVmYXVsdCB0byBmYWxzZSBpbiBhIGZ1dHVyZSByZWxlYXNlLiBUbyByZW5kZXIgSFRNTCBpbiBDaG9pY2VzLCB5b3Ugd2lsbCBuZWVkIHRvIHNldCBpdCB0byB0cnVlLiBTZXR0aW5nIGFsbG93SFRNTCB3aWxsIHN1cHByZXNzIHRoaXMgbWVzc2FnZS4nKTtcbiAgICB9XG4gICAgdGhpcy5jb25maWcgPSBkZWVwbWVyZ2VfMS5kZWZhdWx0LmFsbChbZGVmYXVsdHNfMS5ERUZBVUxUX0NPTkZJRywgQ2hvaWNlcy5kZWZhdWx0cy5vcHRpb25zLCB1c2VyQ29uZmlnXSxcbiAgICAvLyBXaGVuIG1lcmdpbmcgYXJyYXkgY29uZmlncywgcmVwbGFjZSB3aXRoIGEgY29weSBvZiB0aGUgdXNlckNvbmZpZyBhcnJheSxcbiAgICAvLyBpbnN0ZWFkIG9mIGNvbmNhdGVuYXRpbmcgd2l0aCB0aGUgZGVmYXVsdCBhcnJheVxuICAgIHtcbiAgICAgIGFycmF5TWVyZ2U6IGZ1bmN0aW9uIChfLCBzb3VyY2VBcnJheSkge1xuICAgICAgICByZXR1cm4gX19zcHJlYWRBcnJheShbXSwgc291cmNlQXJyYXksIHRydWUpO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHZhciBpbnZhbGlkQ29uZmlnT3B0aW9ucyA9ICgwLCB1dGlsc18xLmRpZmYpKHRoaXMuY29uZmlnLCBkZWZhdWx0c18xLkRFRkFVTFRfQ09ORklHKTtcbiAgICBpZiAoaW52YWxpZENvbmZpZ09wdGlvbnMubGVuZ3RoKSB7XG4gICAgICBjb25zb2xlLndhcm4oJ1Vua25vd24gY29uZmlnIG9wdGlvbihzKSBwYXNzZWQnLCBpbnZhbGlkQ29uZmlnT3B0aW9ucy5qb2luKCcsICcpKTtcbiAgICB9XG4gICAgdmFyIHBhc3NlZEVsZW1lbnQgPSB0eXBlb2YgZWxlbWVudCA9PT0gJ3N0cmluZycgPyBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGVsZW1lbnQpIDogZWxlbWVudDtcbiAgICBpZiAoIShwYXNzZWRFbGVtZW50IGluc3RhbmNlb2YgSFRNTElucHV0RWxlbWVudCB8fCBwYXNzZWRFbGVtZW50IGluc3RhbmNlb2YgSFRNTFNlbGVjdEVsZW1lbnQpKSB7XG4gICAgICB0aHJvdyBUeXBlRXJyb3IoJ0V4cGVjdGVkIG9uZSBvZiB0aGUgZm9sbG93aW5nIHR5cGVzIHRleHR8c2VsZWN0LW9uZXxzZWxlY3QtbXVsdGlwbGUnKTtcbiAgICB9XG4gICAgdGhpcy5faXNUZXh0RWxlbWVudCA9IHBhc3NlZEVsZW1lbnQudHlwZSA9PT0gY29uc3RhbnRzXzEuVEVYVF9UWVBFO1xuICAgIHRoaXMuX2lzU2VsZWN0T25lRWxlbWVudCA9IHBhc3NlZEVsZW1lbnQudHlwZSA9PT0gY29uc3RhbnRzXzEuU0VMRUNUX09ORV9UWVBFO1xuICAgIHRoaXMuX2lzU2VsZWN0TXVsdGlwbGVFbGVtZW50ID0gcGFzc2VkRWxlbWVudC50eXBlID09PSBjb25zdGFudHNfMS5TRUxFQ1RfTVVMVElQTEVfVFlQRTtcbiAgICB0aGlzLl9pc1NlbGVjdEVsZW1lbnQgPSB0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQgfHwgdGhpcy5faXNTZWxlY3RNdWx0aXBsZUVsZW1lbnQ7XG4gICAgdGhpcy5jb25maWcuc2VhcmNoRW5hYmxlZCA9IHRoaXMuX2lzU2VsZWN0TXVsdGlwbGVFbGVtZW50IHx8IHRoaXMuY29uZmlnLnNlYXJjaEVuYWJsZWQ7XG4gICAgaWYgKCFbJ2F1dG8nLCAnYWx3YXlzJ10uaW5jbHVkZXMoXCJcIi5jb25jYXQodGhpcy5jb25maWcucmVuZGVyU2VsZWN0ZWRDaG9pY2VzKSkpIHtcbiAgICAgIHRoaXMuY29uZmlnLnJlbmRlclNlbGVjdGVkQ2hvaWNlcyA9ICdhdXRvJztcbiAgICB9XG4gICAgaWYgKHVzZXJDb25maWcuYWRkSXRlbUZpbHRlciAmJiB0eXBlb2YgdXNlckNvbmZpZy5hZGRJdGVtRmlsdGVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICB2YXIgcmUgPSB1c2VyQ29uZmlnLmFkZEl0ZW1GaWx0ZXIgaW5zdGFuY2VvZiBSZWdFeHAgPyB1c2VyQ29uZmlnLmFkZEl0ZW1GaWx0ZXIgOiBuZXcgUmVnRXhwKHVzZXJDb25maWcuYWRkSXRlbUZpbHRlcik7XG4gICAgICB0aGlzLmNvbmZpZy5hZGRJdGVtRmlsdGVyID0gcmUudGVzdC5iaW5kKHJlKTtcbiAgICB9XG4gICAgaWYgKHRoaXMuX2lzVGV4dEVsZW1lbnQpIHtcbiAgICAgIHRoaXMucGFzc2VkRWxlbWVudCA9IG5ldyBjb21wb25lbnRzXzEuV3JhcHBlZElucHV0KHtcbiAgICAgICAgZWxlbWVudDogcGFzc2VkRWxlbWVudCxcbiAgICAgICAgY2xhc3NOYW1lczogdGhpcy5jb25maWcuY2xhc3NOYW1lcyxcbiAgICAgICAgZGVsaW1pdGVyOiB0aGlzLmNvbmZpZy5kZWxpbWl0ZXJcbiAgICAgIH0pO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLnBhc3NlZEVsZW1lbnQgPSBuZXcgY29tcG9uZW50c18xLldyYXBwZWRTZWxlY3Qoe1xuICAgICAgICBlbGVtZW50OiBwYXNzZWRFbGVtZW50LFxuICAgICAgICBjbGFzc05hbWVzOiB0aGlzLmNvbmZpZy5jbGFzc05hbWVzLFxuICAgICAgICB0ZW1wbGF0ZTogZnVuY3Rpb24gKGRhdGEpIHtcbiAgICAgICAgICByZXR1cm4gX3RoaXMuX3RlbXBsYXRlcy5vcHRpb24oZGF0YSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH1cbiAgICB0aGlzLmluaXRpYWxpc2VkID0gZmFsc2U7XG4gICAgdGhpcy5fc3RvcmUgPSBuZXcgc3RvcmVfMS5kZWZhdWx0KCk7XG4gICAgdGhpcy5faW5pdGlhbFN0YXRlID0gcmVkdWNlcnNfMS5kZWZhdWx0U3RhdGU7XG4gICAgdGhpcy5fY3VycmVudFN0YXRlID0gcmVkdWNlcnNfMS5kZWZhdWx0U3RhdGU7XG4gICAgdGhpcy5fcHJldlN0YXRlID0gcmVkdWNlcnNfMS5kZWZhdWx0U3RhdGU7XG4gICAgdGhpcy5fY3VycmVudFZhbHVlID0gJyc7XG4gICAgdGhpcy5fY2FuU2VhcmNoID0gISF0aGlzLmNvbmZpZy5zZWFyY2hFbmFibGVkO1xuICAgIHRoaXMuX2lzU2Nyb2xsaW5nT25JZSA9IGZhbHNlO1xuICAgIHRoaXMuX2hpZ2hsaWdodFBvc2l0aW9uID0gMDtcbiAgICB0aGlzLl93YXNUYXAgPSB0cnVlO1xuICAgIHRoaXMuX3BsYWNlaG9sZGVyVmFsdWUgPSB0aGlzLl9nZW5lcmF0ZVBsYWNlaG9sZGVyVmFsdWUoKTtcbiAgICB0aGlzLl9iYXNlSWQgPSAoMCwgdXRpbHNfMS5nZW5lcmF0ZUlkKSh0aGlzLnBhc3NlZEVsZW1lbnQuZWxlbWVudCwgJ2Nob2ljZXMtJyk7XG4gICAgLyoqXG4gICAgICogc2V0dGluZyBkaXJlY3Rpb24gaW4gY2FzZXMgd2hlcmUgaXQncyBleHBsaWNpdGx5IHNldCBvbiBwYXNzZWRFbGVtZW50XG4gICAgICogb3Igd2hlbiBjYWxjdWxhdGVkIGRpcmVjdGlvbiBpcyBkaWZmZXJlbnQgZnJvbSB0aGUgZG9jdW1lbnRcbiAgICAgKi9cbiAgICB0aGlzLl9kaXJlY3Rpb24gPSB0aGlzLnBhc3NlZEVsZW1lbnQuZGlyO1xuICAgIGlmICghdGhpcy5fZGlyZWN0aW9uKSB7XG4gICAgICB2YXIgZWxlbWVudERpcmVjdGlvbiA9IHdpbmRvdy5nZXRDb21wdXRlZFN0eWxlKHRoaXMucGFzc2VkRWxlbWVudC5lbGVtZW50KS5kaXJlY3Rpb247XG4gICAgICB2YXIgZG9jdW1lbnREaXJlY3Rpb24gPSB3aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZShkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQpLmRpcmVjdGlvbjtcbiAgICAgIGlmIChlbGVtZW50RGlyZWN0aW9uICE9PSBkb2N1bWVudERpcmVjdGlvbikge1xuICAgICAgICB0aGlzLl9kaXJlY3Rpb24gPSBlbGVtZW50RGlyZWN0aW9uO1xuICAgICAgfVxuICAgIH1cbiAgICB0aGlzLl9pZE5hbWVzID0ge1xuICAgICAgaXRlbUNob2ljZTogJ2l0ZW0tY2hvaWNlJ1xuICAgIH07XG4gICAgaWYgKHRoaXMuX2lzU2VsZWN0RWxlbWVudCkge1xuICAgICAgLy8gQXNzaWduIHByZXNldCBncm91cHMgZnJvbSBwYXNzZWQgZWxlbWVudFxuICAgICAgdGhpcy5fcHJlc2V0R3JvdXBzID0gdGhpcy5wYXNzZWRFbGVtZW50Lm9wdGlvbkdyb3VwcztcbiAgICAgIC8vIEFzc2lnbiBwcmVzZXQgb3B0aW9ucyBmcm9tIHBhc3NlZCBlbGVtZW50XG4gICAgICB0aGlzLl9wcmVzZXRPcHRpb25zID0gdGhpcy5wYXNzZWRFbGVtZW50Lm9wdGlvbnM7XG4gICAgfVxuICAgIC8vIEFzc2lnbiBwcmVzZXQgY2hvaWNlcyBmcm9tIHBhc3NlZCBvYmplY3RcbiAgICB0aGlzLl9wcmVzZXRDaG9pY2VzID0gdGhpcy5jb25maWcuY2hvaWNlcztcbiAgICAvLyBBc3NpZ24gcHJlc2V0IGl0ZW1zIGZyb20gcGFzc2VkIG9iamVjdCBmaXJzdFxuICAgIHRoaXMuX3ByZXNldEl0ZW1zID0gdGhpcy5jb25maWcuaXRlbXM7XG4gICAgLy8gQWRkIGFueSB2YWx1ZXMgcGFzc2VkIGZyb20gYXR0cmlidXRlXG4gICAgaWYgKHRoaXMucGFzc2VkRWxlbWVudC52YWx1ZSAmJiB0aGlzLl9pc1RleHRFbGVtZW50KSB7XG4gICAgICB2YXIgc3BsaXRWYWx1ZXMgPSB0aGlzLnBhc3NlZEVsZW1lbnQudmFsdWUuc3BsaXQodGhpcy5jb25maWcuZGVsaW1pdGVyKTtcbiAgICAgIHRoaXMuX3ByZXNldEl0ZW1zID0gdGhpcy5fcHJlc2V0SXRlbXMuY29uY2F0KHNwbGl0VmFsdWVzKTtcbiAgICB9XG4gICAgLy8gQ3JlYXRlIGFycmF5IG9mIGNob2ljZXMgZnJvbSBvcHRpb24gZWxlbWVudHNcbiAgICBpZiAodGhpcy5wYXNzZWRFbGVtZW50Lm9wdGlvbnMpIHtcbiAgICAgIHRoaXMucGFzc2VkRWxlbWVudC5vcHRpb25zLmZvckVhY2goZnVuY3Rpb24gKG9wdGlvbikge1xuICAgICAgICBfdGhpcy5fcHJlc2V0Q2hvaWNlcy5wdXNoKHtcbiAgICAgICAgICB2YWx1ZTogb3B0aW9uLnZhbHVlLFxuICAgICAgICAgIGxhYmVsOiBvcHRpb24uaW5uZXJIVE1MLFxuICAgICAgICAgIHNlbGVjdGVkOiAhIW9wdGlvbi5zZWxlY3RlZCxcbiAgICAgICAgICBkaXNhYmxlZDogb3B0aW9uLmRpc2FibGVkIHx8IG9wdGlvbi5wYXJlbnROb2RlLmRpc2FibGVkLFxuICAgICAgICAgIHBsYWNlaG9sZGVyOiBvcHRpb24udmFsdWUgPT09ICcnIHx8IG9wdGlvbi5oYXNBdHRyaWJ1dGUoJ3BsYWNlaG9sZGVyJyksXG4gICAgICAgICAgY3VzdG9tUHJvcGVydGllczogKDAsIHV0aWxzXzEucGFyc2VDdXN0b21Qcm9wZXJ0aWVzKShvcHRpb24uZGF0YXNldC5jdXN0b21Qcm9wZXJ0aWVzKVxuICAgICAgICB9KTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICB0aGlzLl9yZW5kZXIgPSB0aGlzLl9yZW5kZXIuYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vbkZvY3VzID0gdGhpcy5fb25Gb2N1cy5iaW5kKHRoaXMpO1xuICAgIHRoaXMuX29uQmx1ciA9IHRoaXMuX29uQmx1ci5iaW5kKHRoaXMpO1xuICAgIHRoaXMuX29uS2V5VXAgPSB0aGlzLl9vbktleVVwLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25LZXlEb3duID0gdGhpcy5fb25LZXlEb3duLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25DbGljayA9IHRoaXMuX29uQ2xpY2suYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vblRvdWNoTW92ZSA9IHRoaXMuX29uVG91Y2hNb3ZlLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25Ub3VjaEVuZCA9IHRoaXMuX29uVG91Y2hFbmQuYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vbk1vdXNlRG93biA9IHRoaXMuX29uTW91c2VEb3duLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25Nb3VzZU92ZXIgPSB0aGlzLl9vbk1vdXNlT3Zlci5iaW5kKHRoaXMpO1xuICAgIHRoaXMuX29uRm9ybVJlc2V0ID0gdGhpcy5fb25Gb3JtUmVzZXQuYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vblNlbGVjdEtleSA9IHRoaXMuX29uU2VsZWN0S2V5LmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25FbnRlcktleSA9IHRoaXMuX29uRW50ZXJLZXkuYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vbkVzY2FwZUtleSA9IHRoaXMuX29uRXNjYXBlS2V5LmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25EaXJlY3Rpb25LZXkgPSB0aGlzLl9vbkRpcmVjdGlvbktleS5iaW5kKHRoaXMpO1xuICAgIHRoaXMuX29uRGVsZXRlS2V5ID0gdGhpcy5fb25EZWxldGVLZXkuYmluZCh0aGlzKTtcbiAgICAvLyBJZiBlbGVtZW50IGhhcyBhbHJlYWR5IGJlZW4gaW5pdGlhbGlzZWQgd2l0aCBDaG9pY2VzLCBmYWlsIHNpbGVudGx5XG4gICAgaWYgKHRoaXMucGFzc2VkRWxlbWVudC5pc0FjdGl2ZSkge1xuICAgICAgaWYgKCF0aGlzLmNvbmZpZy5zaWxlbnQpIHtcbiAgICAgICAgY29uc29sZS53YXJuKCdUcnlpbmcgdG8gaW5pdGlhbGlzZSBDaG9pY2VzIG9uIGVsZW1lbnQgYWxyZWFkeSBpbml0aWFsaXNlZCcsIHtcbiAgICAgICAgICBlbGVtZW50OiBlbGVtZW50XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICAgdGhpcy5pbml0aWFsaXNlZCA9IHRydWU7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIC8vIExldCdzIGdvXG4gICAgdGhpcy5pbml0KCk7XG4gIH1cbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KENob2ljZXMsIFwiZGVmYXVsdHNcIiwge1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIE9iamVjdC5wcmV2ZW50RXh0ZW5zaW9ucyh7XG4gICAgICAgIGdldCBvcHRpb25zKCkge1xuICAgICAgICAgIHJldHVybiBVU0VSX0RFRkFVTFRTO1xuICAgICAgICB9LFxuICAgICAgICBnZXQgdGVtcGxhdGVzKCkge1xuICAgICAgICAgIHJldHVybiB0ZW1wbGF0ZXNfMS5kZWZhdWx0O1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuaW5pdCA9IGZ1bmN0aW9uICgpIHtcbiAgICBpZiAodGhpcy5pbml0aWFsaXNlZCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLl9jcmVhdGVUZW1wbGF0ZXMoKTtcbiAgICB0aGlzLl9jcmVhdGVFbGVtZW50cygpO1xuICAgIHRoaXMuX2NyZWF0ZVN0cnVjdHVyZSgpO1xuICAgIHRoaXMuX3N0b3JlLnN1YnNjcmliZSh0aGlzLl9yZW5kZXIpO1xuICAgIHRoaXMuX3JlbmRlcigpO1xuICAgIHRoaXMuX2FkZEV2ZW50TGlzdGVuZXJzKCk7XG4gICAgdmFyIHNob3VsZERpc2FibGUgPSAhdGhpcy5jb25maWcuYWRkSXRlbXMgfHwgdGhpcy5wYXNzZWRFbGVtZW50LmVsZW1lbnQuaGFzQXR0cmlidXRlKCdkaXNhYmxlZCcpO1xuICAgIGlmIChzaG91bGREaXNhYmxlKSB7XG4gICAgICB0aGlzLmRpc2FibGUoKTtcbiAgICB9XG4gICAgdGhpcy5pbml0aWFsaXNlZCA9IHRydWU7XG4gICAgdmFyIGNhbGxiYWNrT25Jbml0ID0gdGhpcy5jb25maWcuY2FsbGJhY2tPbkluaXQ7XG4gICAgLy8gUnVuIGNhbGxiYWNrIGlmIGl0IGlzIGEgZnVuY3Rpb25cbiAgICBpZiAoY2FsbGJhY2tPbkluaXQgJiYgdHlwZW9mIGNhbGxiYWNrT25Jbml0ID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICBjYWxsYmFja09uSW5pdC5jYWxsKHRoaXMpO1xuICAgIH1cbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuZGVzdHJveSA9IGZ1bmN0aW9uICgpIHtcbiAgICBpZiAoIXRoaXMuaW5pdGlhbGlzZWQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdGhpcy5fcmVtb3ZlRXZlbnRMaXN0ZW5lcnMoKTtcbiAgICB0aGlzLnBhc3NlZEVsZW1lbnQucmV2ZWFsKCk7XG4gICAgdGhpcy5jb250YWluZXJPdXRlci51bndyYXAodGhpcy5wYXNzZWRFbGVtZW50LmVsZW1lbnQpO1xuICAgIHRoaXMuY2xlYXJTdG9yZSgpO1xuICAgIGlmICh0aGlzLl9pc1NlbGVjdEVsZW1lbnQpIHtcbiAgICAgIHRoaXMucGFzc2VkRWxlbWVudC5vcHRpb25zID0gdGhpcy5fcHJlc2V0T3B0aW9ucztcbiAgICB9XG4gICAgdGhpcy5fdGVtcGxhdGVzID0gdGVtcGxhdGVzXzEuZGVmYXVsdDtcbiAgICB0aGlzLmluaXRpYWxpc2VkID0gZmFsc2U7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLmVuYWJsZSA9IGZ1bmN0aW9uICgpIHtcbiAgICBpZiAodGhpcy5wYXNzZWRFbGVtZW50LmlzRGlzYWJsZWQpIHtcbiAgICAgIHRoaXMucGFzc2VkRWxlbWVudC5lbmFibGUoKTtcbiAgICB9XG4gICAgaWYgKHRoaXMuY29udGFpbmVyT3V0ZXIuaXNEaXNhYmxlZCkge1xuICAgICAgdGhpcy5fYWRkRXZlbnRMaXN0ZW5lcnMoKTtcbiAgICAgIHRoaXMuaW5wdXQuZW5hYmxlKCk7XG4gICAgICB0aGlzLmNvbnRhaW5lck91dGVyLmVuYWJsZSgpO1xuICAgIH1cbiAgICByZXR1cm4gdGhpcztcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuZGlzYWJsZSA9IGZ1bmN0aW9uICgpIHtcbiAgICBpZiAoIXRoaXMucGFzc2VkRWxlbWVudC5pc0Rpc2FibGVkKSB7XG4gICAgICB0aGlzLnBhc3NlZEVsZW1lbnQuZGlzYWJsZSgpO1xuICAgIH1cbiAgICBpZiAoIXRoaXMuY29udGFpbmVyT3V0ZXIuaXNEaXNhYmxlZCkge1xuICAgICAgdGhpcy5fcmVtb3ZlRXZlbnRMaXN0ZW5lcnMoKTtcbiAgICAgIHRoaXMuaW5wdXQuZGlzYWJsZSgpO1xuICAgICAgdGhpcy5jb250YWluZXJPdXRlci5kaXNhYmxlKCk7XG4gICAgfVxuICAgIHJldHVybiB0aGlzO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5oaWdobGlnaHRJdGVtID0gZnVuY3Rpb24gKGl0ZW0sIHJ1bkV2ZW50KSB7XG4gICAgaWYgKHJ1bkV2ZW50ID09PSB2b2lkIDApIHtcbiAgICAgIHJ1bkV2ZW50ID0gdHJ1ZTtcbiAgICB9XG4gICAgaWYgKCFpdGVtIHx8ICFpdGVtLmlkKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgdmFyIGlkID0gaXRlbS5pZCxcbiAgICAgIF9hID0gaXRlbS5ncm91cElkLFxuICAgICAgZ3JvdXBJZCA9IF9hID09PSB2b2lkIDAgPyAtMSA6IF9hLFxuICAgICAgX2IgPSBpdGVtLnZhbHVlLFxuICAgICAgdmFsdWUgPSBfYiA9PT0gdm9pZCAwID8gJycgOiBfYixcbiAgICAgIF9jID0gaXRlbS5sYWJlbCxcbiAgICAgIGxhYmVsID0gX2MgPT09IHZvaWQgMCA/ICcnIDogX2M7XG4gICAgdmFyIGdyb3VwID0gZ3JvdXBJZCA+PSAwID8gdGhpcy5fc3RvcmUuZ2V0R3JvdXBCeUlkKGdyb3VwSWQpIDogbnVsbDtcbiAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaCgoMCwgaXRlbXNfMS5oaWdobGlnaHRJdGVtKShpZCwgdHJ1ZSkpO1xuICAgIGlmIChydW5FdmVudCkge1xuICAgICAgdGhpcy5wYXNzZWRFbGVtZW50LnRyaWdnZXJFdmVudChjb25zdGFudHNfMS5FVkVOVFMuaGlnaGxpZ2h0SXRlbSwge1xuICAgICAgICBpZDogaWQsXG4gICAgICAgIHZhbHVlOiB2YWx1ZSxcbiAgICAgICAgbGFiZWw6IGxhYmVsLFxuICAgICAgICBncm91cFZhbHVlOiBncm91cCAmJiBncm91cC52YWx1ZSA/IGdyb3VwLnZhbHVlIDogbnVsbFxuICAgICAgfSk7XG4gICAgfVxuICAgIHJldHVybiB0aGlzO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS51bmhpZ2hsaWdodEl0ZW0gPSBmdW5jdGlvbiAoaXRlbSkge1xuICAgIGlmICghaXRlbSB8fCAhaXRlbS5pZCkge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIHZhciBpZCA9IGl0ZW0uaWQsXG4gICAgICBfYSA9IGl0ZW0uZ3JvdXBJZCxcbiAgICAgIGdyb3VwSWQgPSBfYSA9PT0gdm9pZCAwID8gLTEgOiBfYSxcbiAgICAgIF9iID0gaXRlbS52YWx1ZSxcbiAgICAgIHZhbHVlID0gX2IgPT09IHZvaWQgMCA/ICcnIDogX2IsXG4gICAgICBfYyA9IGl0ZW0ubGFiZWwsXG4gICAgICBsYWJlbCA9IF9jID09PSB2b2lkIDAgPyAnJyA6IF9jO1xuICAgIHZhciBncm91cCA9IGdyb3VwSWQgPj0gMCA/IHRoaXMuX3N0b3JlLmdldEdyb3VwQnlJZChncm91cElkKSA6IG51bGw7XG4gICAgdGhpcy5fc3RvcmUuZGlzcGF0Y2goKDAsIGl0ZW1zXzEuaGlnaGxpZ2h0SXRlbSkoaWQsIGZhbHNlKSk7XG4gICAgdGhpcy5wYXNzZWRFbGVtZW50LnRyaWdnZXJFdmVudChjb25zdGFudHNfMS5FVkVOVFMuaGlnaGxpZ2h0SXRlbSwge1xuICAgICAgaWQ6IGlkLFxuICAgICAgdmFsdWU6IHZhbHVlLFxuICAgICAgbGFiZWw6IGxhYmVsLFxuICAgICAgZ3JvdXBWYWx1ZTogZ3JvdXAgJiYgZ3JvdXAudmFsdWUgPyBncm91cC52YWx1ZSA6IG51bGxcbiAgICB9KTtcbiAgICByZXR1cm4gdGhpcztcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuaGlnaGxpZ2h0QWxsID0gZnVuY3Rpb24gKCkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgdGhpcy5fc3RvcmUuaXRlbXMuZm9yRWFjaChmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgcmV0dXJuIF90aGlzLmhpZ2hsaWdodEl0ZW0oaXRlbSk7XG4gICAgfSk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLnVuaGlnaGxpZ2h0QWxsID0gZnVuY3Rpb24gKCkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgdGhpcy5fc3RvcmUuaXRlbXMuZm9yRWFjaChmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgcmV0dXJuIF90aGlzLnVuaGlnaGxpZ2h0SXRlbShpdGVtKTtcbiAgICB9KTtcbiAgICByZXR1cm4gdGhpcztcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUucmVtb3ZlQWN0aXZlSXRlbXNCeVZhbHVlID0gZnVuY3Rpb24gKHZhbHVlKSB7XG4gICAgdmFyIF90aGlzID0gdGhpcztcbiAgICB0aGlzLl9zdG9yZS5hY3RpdmVJdGVtcy5maWx0ZXIoZnVuY3Rpb24gKGl0ZW0pIHtcbiAgICAgIHJldHVybiBpdGVtLnZhbHVlID09PSB2YWx1ZTtcbiAgICB9KS5mb3JFYWNoKGZ1bmN0aW9uIChpdGVtKSB7XG4gICAgICByZXR1cm4gX3RoaXMuX3JlbW92ZUl0ZW0oaXRlbSk7XG4gICAgfSk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLnJlbW92ZUFjdGl2ZUl0ZW1zID0gZnVuY3Rpb24gKGV4Y2x1ZGVkSWQpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIHRoaXMuX3N0b3JlLmFjdGl2ZUl0ZW1zLmZpbHRlcihmdW5jdGlvbiAoX2EpIHtcbiAgICAgIHZhciBpZCA9IF9hLmlkO1xuICAgICAgcmV0dXJuIGlkICE9PSBleGNsdWRlZElkO1xuICAgIH0pLmZvckVhY2goZnVuY3Rpb24gKGl0ZW0pIHtcbiAgICAgIHJldHVybiBfdGhpcy5fcmVtb3ZlSXRlbShpdGVtKTtcbiAgICB9KTtcbiAgICByZXR1cm4gdGhpcztcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUucmVtb3ZlSGlnaGxpZ2h0ZWRJdGVtcyA9IGZ1bmN0aW9uIChydW5FdmVudCkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgaWYgKHJ1bkV2ZW50ID09PSB2b2lkIDApIHtcbiAgICAgIHJ1bkV2ZW50ID0gZmFsc2U7XG4gICAgfVxuICAgIHRoaXMuX3N0b3JlLmhpZ2hsaWdodGVkQWN0aXZlSXRlbXMuZm9yRWFjaChmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgX3RoaXMuX3JlbW92ZUl0ZW0oaXRlbSk7XG4gICAgICAvLyBJZiB0aGlzIGFjdGlvbiB3YXMgcGVyZm9ybWVkIGJ5IHRoZSB1c2VyXG4gICAgICAvLyB0cmlnZ2VyIHRoZSBldmVudFxuICAgICAgaWYgKHJ1bkV2ZW50KSB7XG4gICAgICAgIF90aGlzLl90cmlnZ2VyQ2hhbmdlKGl0ZW0udmFsdWUpO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHJldHVybiB0aGlzO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5zaG93RHJvcGRvd24gPSBmdW5jdGlvbiAocHJldmVudElucHV0Rm9jdXMpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIGlmICh0aGlzLmRyb3Bkb3duLmlzQWN0aXZlKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZ1bmN0aW9uICgpIHtcbiAgICAgIF90aGlzLmRyb3Bkb3duLnNob3coKTtcbiAgICAgIF90aGlzLmNvbnRhaW5lck91dGVyLm9wZW4oX3RoaXMuZHJvcGRvd24uZGlzdGFuY2VGcm9tVG9wV2luZG93KTtcbiAgICAgIGlmICghcHJldmVudElucHV0Rm9jdXMgJiYgX3RoaXMuX2NhblNlYXJjaCkge1xuICAgICAgICBfdGhpcy5pbnB1dC5mb2N1cygpO1xuICAgICAgfVxuICAgICAgX3RoaXMucGFzc2VkRWxlbWVudC50cmlnZ2VyRXZlbnQoY29uc3RhbnRzXzEuRVZFTlRTLnNob3dEcm9wZG93biwge30pO1xuICAgIH0pO1xuICAgIHJldHVybiB0aGlzO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5oaWRlRHJvcGRvd24gPSBmdW5jdGlvbiAocHJldmVudElucHV0Qmx1cikge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgaWYgKCF0aGlzLmRyb3Bkb3duLmlzQWN0aXZlKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZ1bmN0aW9uICgpIHtcbiAgICAgIF90aGlzLmRyb3Bkb3duLmhpZGUoKTtcbiAgICAgIF90aGlzLmNvbnRhaW5lck91dGVyLmNsb3NlKCk7XG4gICAgICBpZiAoIXByZXZlbnRJbnB1dEJsdXIgJiYgX3RoaXMuX2NhblNlYXJjaCkge1xuICAgICAgICBfdGhpcy5pbnB1dC5yZW1vdmVBY3RpdmVEZXNjZW5kYW50KCk7XG4gICAgICAgIF90aGlzLmlucHV0LmJsdXIoKTtcbiAgICAgIH1cbiAgICAgIF90aGlzLnBhc3NlZEVsZW1lbnQudHJpZ2dlckV2ZW50KGNvbnN0YW50c18xLkVWRU5UUy5oaWRlRHJvcGRvd24sIHt9KTtcbiAgICB9KTtcbiAgICByZXR1cm4gdGhpcztcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuZ2V0VmFsdWUgPSBmdW5jdGlvbiAodmFsdWVPbmx5KSB7XG4gICAgaWYgKHZhbHVlT25seSA9PT0gdm9pZCAwKSB7XG4gICAgICB2YWx1ZU9ubHkgPSBmYWxzZTtcbiAgICB9XG4gICAgdmFyIHZhbHVlcyA9IHRoaXMuX3N0b3JlLmFjdGl2ZUl0ZW1zLnJlZHVjZShmdW5jdGlvbiAoc2VsZWN0ZWRJdGVtcywgaXRlbSkge1xuICAgICAgdmFyIGl0ZW1WYWx1ZSA9IHZhbHVlT25seSA/IGl0ZW0udmFsdWUgOiBpdGVtO1xuICAgICAgc2VsZWN0ZWRJdGVtcy5wdXNoKGl0ZW1WYWx1ZSk7XG4gICAgICByZXR1cm4gc2VsZWN0ZWRJdGVtcztcbiAgICB9LCBbXSk7XG4gICAgcmV0dXJuIHRoaXMuX2lzU2VsZWN0T25lRWxlbWVudCA/IHZhbHVlc1swXSA6IHZhbHVlcztcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuc2V0VmFsdWUgPSBmdW5jdGlvbiAoaXRlbXMpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIGlmICghdGhpcy5pbml0aWFsaXNlZCkge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIGl0ZW1zLmZvckVhY2goZnVuY3Rpb24gKHZhbHVlKSB7XG4gICAgICByZXR1cm4gX3RoaXMuX3NldENob2ljZU9ySXRlbSh2YWx1ZSk7XG4gICAgfSk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLnNldENob2ljZUJ5VmFsdWUgPSBmdW5jdGlvbiAodmFsdWUpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIGlmICghdGhpcy5pbml0aWFsaXNlZCB8fCB0aGlzLl9pc1RleHRFbGVtZW50KSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgLy8gSWYgb25seSBvbmUgdmFsdWUgaGFzIGJlZW4gcGFzc2VkLCBjb252ZXJ0IHRvIGFycmF5XG4gICAgdmFyIGNob2ljZVZhbHVlID0gQXJyYXkuaXNBcnJheSh2YWx1ZSkgPyB2YWx1ZSA6IFt2YWx1ZV07XG4gICAgLy8gTG9vcCB0aHJvdWdoIGVhY2ggdmFsdWUgYW5kXG4gICAgY2hvaWNlVmFsdWUuZm9yRWFjaChmdW5jdGlvbiAodmFsKSB7XG4gICAgICByZXR1cm4gX3RoaXMuX2ZpbmRBbmRTZWxlY3RDaG9pY2VCeVZhbHVlKHZhbCk7XG4gICAgfSk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIC8qKlxuICAgKiBTZXQgY2hvaWNlcyBvZiBzZWxlY3QgaW5wdXQgdmlhIGFuIGFycmF5IG9mIG9iamVjdHMgKG9yIGZ1bmN0aW9uIHRoYXQgcmV0dXJucyBhcnJheSBvZiBvYmplY3Qgb3IgcHJvbWlzZSBvZiBpdCksXG4gICAqIGEgdmFsdWUgZmllbGQgbmFtZSBhbmQgYSBsYWJlbCBmaWVsZCBuYW1lLlxuICAgKiBUaGlzIGJlaGF2ZXMgdGhlIHNhbWUgYXMgcGFzc2luZyBpdGVtcyB2aWEgdGhlIGNob2ljZXMgb3B0aW9uIGJ1dCBjYW4gYmUgY2FsbGVkIGFmdGVyIGluaXRpYWxpc2luZyBDaG9pY2VzLlxuICAgKiBUaGlzIGNhbiBhbHNvIGJlIHVzZWQgdG8gYWRkIGdyb3VwcyBvZiBjaG9pY2VzIChzZWUgZXhhbXBsZSAyKTsgT3B0aW9uYWxseSBwYXNzIGEgdHJ1ZSBgcmVwbGFjZUNob2ljZXNgIHZhbHVlIHRvIHJlbW92ZSBhbnkgZXhpc3RpbmcgY2hvaWNlcy5cbiAgICogT3B0aW9uYWxseSBwYXNzIGEgYGN1c3RvbVByb3BlcnRpZXNgIG9iamVjdCB0byBhZGQgYWRkaXRpb25hbCBkYXRhIHRvIHlvdXIgY2hvaWNlcyAodXNlZnVsIHdoZW4gc2VhcmNoaW5nL2ZpbHRlcmluZyBldGMpLlxuICAgKlxuICAgKiAqKklucHV0IHR5cGVzIGFmZmVjdGVkOioqIHNlbGVjdC1vbmUsIHNlbGVjdC1tdWx0aXBsZVxuICAgKlxuICAgKiBAZXhhbXBsZVxuICAgKiBgYGBqc1xuICAgKiBjb25zdCBleGFtcGxlID0gbmV3IENob2ljZXMoZWxlbWVudCk7XG4gICAqXG4gICAqIGV4YW1wbGUuc2V0Q2hvaWNlcyhbXG4gICAqICAge3ZhbHVlOiAnT25lJywgbGFiZWw6ICdMYWJlbCBPbmUnLCBkaXNhYmxlZDogdHJ1ZX0sXG4gICAqICAge3ZhbHVlOiAnVHdvJywgbGFiZWw6ICdMYWJlbCBUd28nLCBzZWxlY3RlZDogdHJ1ZX0sXG4gICAqICAge3ZhbHVlOiAnVGhyZWUnLCBsYWJlbDogJ0xhYmVsIFRocmVlJ30sXG4gICAqIF0sICd2YWx1ZScsICdsYWJlbCcsIGZhbHNlKTtcbiAgICogYGBgXG4gICAqXG4gICAqIEBleGFtcGxlXG4gICAqIGBgYGpzXG4gICAqIGNvbnN0IGV4YW1wbGUgPSBuZXcgQ2hvaWNlcyhlbGVtZW50KTtcbiAgICpcbiAgICogZXhhbXBsZS5zZXRDaG9pY2VzKGFzeW5jICgpID0+IHtcbiAgICogICB0cnkge1xuICAgKiAgICAgIGNvbnN0IGl0ZW1zID0gYXdhaXQgZmV0Y2goJy9pdGVtcycpO1xuICAgKiAgICAgIHJldHVybiBpdGVtcy5qc29uKClcbiAgICogICB9IGNhdGNoKGVycikge1xuICAgKiAgICAgIGNvbnNvbGUuZXJyb3IoZXJyKVxuICAgKiAgIH1cbiAgICogfSk7XG4gICAqIGBgYFxuICAgKlxuICAgKiBAZXhhbXBsZVxuICAgKiBgYGBqc1xuICAgKiBjb25zdCBleGFtcGxlID0gbmV3IENob2ljZXMoZWxlbWVudCk7XG4gICAqXG4gICAqIGV4YW1wbGUuc2V0Q2hvaWNlcyhbe1xuICAgKiAgIGxhYmVsOiAnR3JvdXAgb25lJyxcbiAgICogICBpZDogMSxcbiAgICogICBkaXNhYmxlZDogZmFsc2UsXG4gICAqICAgY2hvaWNlczogW1xuICAgKiAgICAge3ZhbHVlOiAnQ2hpbGQgT25lJywgbGFiZWw6ICdDaGlsZCBPbmUnLCBzZWxlY3RlZDogdHJ1ZX0sXG4gICAqICAgICB7dmFsdWU6ICdDaGlsZCBUd28nLCBsYWJlbDogJ0NoaWxkIFR3bycsICBkaXNhYmxlZDogdHJ1ZX0sXG4gICAqICAgICB7dmFsdWU6ICdDaGlsZCBUaHJlZScsIGxhYmVsOiAnQ2hpbGQgVGhyZWUnfSxcbiAgICogICBdXG4gICAqIH0sXG4gICAqIHtcbiAgICogICBsYWJlbDogJ0dyb3VwIHR3bycsXG4gICAqICAgaWQ6IDIsXG4gICAqICAgZGlzYWJsZWQ6IGZhbHNlLFxuICAgKiAgIGNob2ljZXM6IFtcbiAgICogICAgIHt2YWx1ZTogJ0NoaWxkIEZvdXInLCBsYWJlbDogJ0NoaWxkIEZvdXInLCBkaXNhYmxlZDogdHJ1ZX0sXG4gICAqICAgICB7dmFsdWU6ICdDaGlsZCBGaXZlJywgbGFiZWw6ICdDaGlsZCBGaXZlJ30sXG4gICAqICAgICB7dmFsdWU6ICdDaGlsZCBTaXgnLCBsYWJlbDogJ0NoaWxkIFNpeCcsIGN1c3RvbVByb3BlcnRpZXM6IHtcbiAgICogICAgICAgZGVzY3JpcHRpb246ICdDdXN0b20gZGVzY3JpcHRpb24gYWJvdXQgY2hpbGQgc2l4JyxcbiAgICogICAgICAgcmFuZG9tOiAnQW5vdGhlciByYW5kb20gY3VzdG9tIHByb3BlcnR5J1xuICAgKiAgICAgfX0sXG4gICAqICAgXVxuICAgKiB9XSwgJ3ZhbHVlJywgJ2xhYmVsJywgZmFsc2UpO1xuICAgKiBgYGBcbiAgICovXG4gIENob2ljZXMucHJvdG90eXBlLnNldENob2ljZXMgPSBmdW5jdGlvbiAoY2hvaWNlc0FycmF5T3JGZXRjaGVyLCB2YWx1ZSwgbGFiZWwsIHJlcGxhY2VDaG9pY2VzKSB7XG4gICAgdmFyIF90aGlzID0gdGhpcztcbiAgICBpZiAoY2hvaWNlc0FycmF5T3JGZXRjaGVyID09PSB2b2lkIDApIHtcbiAgICAgIGNob2ljZXNBcnJheU9yRmV0Y2hlciA9IFtdO1xuICAgIH1cbiAgICBpZiAodmFsdWUgPT09IHZvaWQgMCkge1xuICAgICAgdmFsdWUgPSAndmFsdWUnO1xuICAgIH1cbiAgICBpZiAobGFiZWwgPT09IHZvaWQgMCkge1xuICAgICAgbGFiZWwgPSAnbGFiZWwnO1xuICAgIH1cbiAgICBpZiAocmVwbGFjZUNob2ljZXMgPT09IHZvaWQgMCkge1xuICAgICAgcmVwbGFjZUNob2ljZXMgPSBmYWxzZTtcbiAgICB9XG4gICAgaWYgKCF0aGlzLmluaXRpYWxpc2VkKSB7XG4gICAgICB0aHJvdyBuZXcgUmVmZXJlbmNlRXJyb3IoXCJzZXRDaG9pY2VzIHdhcyBjYWxsZWQgb24gYSBub24taW5pdGlhbGl6ZWQgaW5zdGFuY2Ugb2YgQ2hvaWNlc1wiKTtcbiAgICB9XG4gICAgaWYgKCF0aGlzLl9pc1NlbGVjdEVsZW1lbnQpIHtcbiAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJzZXRDaG9pY2VzIGNhbid0IGJlIHVzZWQgd2l0aCBJTlBVVCBiYXNlZCBDaG9pY2VzXCIpO1xuICAgIH1cbiAgICBpZiAodHlwZW9mIHZhbHVlICE9PSAnc3RyaW5nJyB8fCAhdmFsdWUpIHtcbiAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJ2YWx1ZSBwYXJhbWV0ZXIgbXVzdCBiZSBhIG5hbWUgb2YgJ3ZhbHVlJyBmaWVsZCBpbiBwYXNzZWQgb2JqZWN0c1wiKTtcbiAgICB9XG4gICAgLy8gQ2xlYXIgY2hvaWNlcyBpZiBuZWVkZWRcbiAgICBpZiAocmVwbGFjZUNob2ljZXMpIHtcbiAgICAgIHRoaXMuY2xlYXJDaG9pY2VzKCk7XG4gICAgfVxuICAgIGlmICh0eXBlb2YgY2hvaWNlc0FycmF5T3JGZXRjaGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAvLyBpdCdzIGEgY2hvaWNlcyBmZXRjaGVyIGZ1bmN0aW9uXG4gICAgICB2YXIgZmV0Y2hlcl8xID0gY2hvaWNlc0FycmF5T3JGZXRjaGVyKHRoaXMpO1xuICAgICAgaWYgKHR5cGVvZiBQcm9taXNlID09PSAnZnVuY3Rpb24nICYmIGZldGNoZXJfMSBpbnN0YW5jZW9mIFByb21pc2UpIHtcbiAgICAgICAgLy8gdGhhdCdzIGEgcHJvbWlzZVxuICAgICAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tcHJvbWlzZS1leGVjdXRvci1yZXR1cm5cbiAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKGZ1bmN0aW9uIChyZXNvbHZlKSB7XG4gICAgICAgICAgcmV0dXJuIHJlcXVlc3RBbmltYXRpb25GcmFtZShyZXNvbHZlKTtcbiAgICAgICAgfSkudGhlbihmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgcmV0dXJuIF90aGlzLl9oYW5kbGVMb2FkaW5nU3RhdGUodHJ1ZSk7XG4gICAgICAgIH0pLnRoZW4oZnVuY3Rpb24gKCkge1xuICAgICAgICAgIHJldHVybiBmZXRjaGVyXzE7XG4gICAgICAgIH0pLnRoZW4oZnVuY3Rpb24gKGRhdGEpIHtcbiAgICAgICAgICByZXR1cm4gX3RoaXMuc2V0Q2hvaWNlcyhkYXRhLCB2YWx1ZSwgbGFiZWwsIHJlcGxhY2VDaG9pY2VzKTtcbiAgICAgICAgfSkuY2F0Y2goZnVuY3Rpb24gKGVycikge1xuICAgICAgICAgIGlmICghX3RoaXMuY29uZmlnLnNpbGVudCkge1xuICAgICAgICAgICAgY29uc29sZS5lcnJvcihlcnIpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSkudGhlbihmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgcmV0dXJuIF90aGlzLl9oYW5kbGVMb2FkaW5nU3RhdGUoZmFsc2UpO1xuICAgICAgICB9KS50aGVuKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICByZXR1cm4gX3RoaXM7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICAgLy8gZnVuY3Rpb24gcmV0dXJuZWQgc29tZXRoaW5nIGVsc2UgdGhhbiBwcm9taXNlLCBsZXQncyBjaGVjayBpZiBpdCdzIGFuIGFycmF5IG9mIGNob2ljZXNcbiAgICAgIGlmICghQXJyYXkuaXNBcnJheShmZXRjaGVyXzEpKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCIuc2V0Q2hvaWNlcyBmaXJzdCBhcmd1bWVudCBmdW5jdGlvbiBtdXN0IHJldHVybiBlaXRoZXIgYXJyYXkgb2YgY2hvaWNlcyBvciBQcm9taXNlLCBnb3Q6IFwiLmNvbmNhdCh0eXBlb2YgZmV0Y2hlcl8xKSk7XG4gICAgICB9XG4gICAgICAvLyByZWN1cnNpb24gd2l0aCByZXN1bHRzLCBpdCdzIHN5bmMgYW5kIGNob2ljZXMgd2VyZSBjbGVhcmVkIGFscmVhZHlcbiAgICAgIHJldHVybiB0aGlzLnNldENob2ljZXMoZmV0Y2hlcl8xLCB2YWx1ZSwgbGFiZWwsIGZhbHNlKTtcbiAgICB9XG4gICAgaWYgKCFBcnJheS5pc0FycmF5KGNob2ljZXNBcnJheU9yRmV0Y2hlcikpIHtcbiAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCIuc2V0Q2hvaWNlcyBtdXN0IGJlIGNhbGxlZCBlaXRoZXIgd2l0aCBhcnJheSBvZiBjaG9pY2VzIHdpdGggYSBmdW5jdGlvbiByZXN1bHRpbmcgaW50byBQcm9taXNlIG9mIGFycmF5IG9mIGNob2ljZXNcIik7XG4gICAgfVxuICAgIHRoaXMuY29udGFpbmVyT3V0ZXIucmVtb3ZlTG9hZGluZ1N0YXRlKCk7XG4gICAgdGhpcy5fc3RhcnRMb2FkaW5nKCk7XG4gICAgY2hvaWNlc0FycmF5T3JGZXRjaGVyLmZvckVhY2goZnVuY3Rpb24gKGdyb3VwT3JDaG9pY2UpIHtcbiAgICAgIGlmIChncm91cE9yQ2hvaWNlLmNob2ljZXMpIHtcbiAgICAgICAgX3RoaXMuX2FkZEdyb3VwKHtcbiAgICAgICAgICBpZDogZ3JvdXBPckNob2ljZS5pZCA/IHBhcnNlSW50KFwiXCIuY29uY2F0KGdyb3VwT3JDaG9pY2UuaWQpLCAxMCkgOiBudWxsLFxuICAgICAgICAgIGdyb3VwOiBncm91cE9yQ2hvaWNlLFxuICAgICAgICAgIHZhbHVlS2V5OiB2YWx1ZSxcbiAgICAgICAgICBsYWJlbEtleTogbGFiZWxcbiAgICAgICAgfSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgY2hvaWNlID0gZ3JvdXBPckNob2ljZTtcbiAgICAgICAgX3RoaXMuX2FkZENob2ljZSh7XG4gICAgICAgICAgdmFsdWU6IGNob2ljZVt2YWx1ZV0sXG4gICAgICAgICAgbGFiZWw6IGNob2ljZVtsYWJlbF0sXG4gICAgICAgICAgaXNTZWxlY3RlZDogISFjaG9pY2Uuc2VsZWN0ZWQsXG4gICAgICAgICAgaXNEaXNhYmxlZDogISFjaG9pY2UuZGlzYWJsZWQsXG4gICAgICAgICAgcGxhY2Vob2xkZXI6ICEhY2hvaWNlLnBsYWNlaG9sZGVyLFxuICAgICAgICAgIGN1c3RvbVByb3BlcnRpZXM6IGNob2ljZS5jdXN0b21Qcm9wZXJ0aWVzXG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHRoaXMuX3N0b3BMb2FkaW5nKCk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLmNsZWFyQ2hvaWNlcyA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaCgoMCwgY2hvaWNlc18xLmNsZWFyQ2hvaWNlcykoKSk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLmNsZWFyU3RvcmUgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5fc3RvcmUuZGlzcGF0Y2goKDAsIG1pc2NfMS5jbGVhckFsbCkoKSk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLmNsZWFySW5wdXQgPSBmdW5jdGlvbiAoKSB7XG4gICAgdmFyIHNob3VsZFNldElucHV0V2lkdGggPSAhdGhpcy5faXNTZWxlY3RPbmVFbGVtZW50O1xuICAgIHRoaXMuaW5wdXQuY2xlYXIoc2hvdWxkU2V0SW5wdXRXaWR0aCk7XG4gICAgaWYgKCF0aGlzLl9pc1RleHRFbGVtZW50ICYmIHRoaXMuX2NhblNlYXJjaCkge1xuICAgICAgdGhpcy5faXNTZWFyY2hpbmcgPSBmYWxzZTtcbiAgICAgIHRoaXMuX3N0b3JlLmRpc3BhdGNoKCgwLCBjaG9pY2VzXzEuYWN0aXZhdGVDaG9pY2VzKSh0cnVlKSk7XG4gICAgfVxuICAgIHJldHVybiB0aGlzO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fcmVuZGVyID0gZnVuY3Rpb24gKCkge1xuICAgIGlmICh0aGlzLl9zdG9yZS5pc0xvYWRpbmcoKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLl9jdXJyZW50U3RhdGUgPSB0aGlzLl9zdG9yZS5zdGF0ZTtcbiAgICB2YXIgc3RhdGVDaGFuZ2VkID0gdGhpcy5fY3VycmVudFN0YXRlLmNob2ljZXMgIT09IHRoaXMuX3ByZXZTdGF0ZS5jaG9pY2VzIHx8IHRoaXMuX2N1cnJlbnRTdGF0ZS5ncm91cHMgIT09IHRoaXMuX3ByZXZTdGF0ZS5ncm91cHMgfHwgdGhpcy5fY3VycmVudFN0YXRlLml0ZW1zICE9PSB0aGlzLl9wcmV2U3RhdGUuaXRlbXM7XG4gICAgdmFyIHNob3VsZFJlbmRlckNob2ljZXMgPSB0aGlzLl9pc1NlbGVjdEVsZW1lbnQ7XG4gICAgdmFyIHNob3VsZFJlbmRlckl0ZW1zID0gdGhpcy5fY3VycmVudFN0YXRlLml0ZW1zICE9PSB0aGlzLl9wcmV2U3RhdGUuaXRlbXM7XG4gICAgaWYgKCFzdGF0ZUNoYW5nZWQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKHNob3VsZFJlbmRlckNob2ljZXMpIHtcbiAgICAgIHRoaXMuX3JlbmRlckNob2ljZXMoKTtcbiAgICB9XG4gICAgaWYgKHNob3VsZFJlbmRlckl0ZW1zKSB7XG4gICAgICB0aGlzLl9yZW5kZXJJdGVtcygpO1xuICAgIH1cbiAgICB0aGlzLl9wcmV2U3RhdGUgPSB0aGlzLl9jdXJyZW50U3RhdGU7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9yZW5kZXJDaG9pY2VzID0gZnVuY3Rpb24gKCkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgdmFyIF9hID0gdGhpcy5fc3RvcmUsXG4gICAgICBhY3RpdmVHcm91cHMgPSBfYS5hY3RpdmVHcm91cHMsXG4gICAgICBhY3RpdmVDaG9pY2VzID0gX2EuYWN0aXZlQ2hvaWNlcztcbiAgICB2YXIgY2hvaWNlTGlzdEZyYWdtZW50ID0gZG9jdW1lbnQuY3JlYXRlRG9jdW1lbnRGcmFnbWVudCgpO1xuICAgIHRoaXMuY2hvaWNlTGlzdC5jbGVhcigpO1xuICAgIGlmICh0aGlzLmNvbmZpZy5yZXNldFNjcm9sbFBvc2l0aW9uKSB7XG4gICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUoZnVuY3Rpb24gKCkge1xuICAgICAgICByZXR1cm4gX3RoaXMuY2hvaWNlTGlzdC5zY3JvbGxUb1RvcCgpO1xuICAgICAgfSk7XG4gICAgfVxuICAgIC8vIElmIHdlIGhhdmUgZ3JvdXBlZCBvcHRpb25zXG4gICAgaWYgKGFjdGl2ZUdyb3Vwcy5sZW5ndGggPj0gMSAmJiAhdGhpcy5faXNTZWFyY2hpbmcpIHtcbiAgICAgIC8vIElmIHdlIGhhdmUgYSBwbGFjZWhvbGRlciBjaG9pY2UgYWxvbmcgd2l0aCBncm91cHNcbiAgICAgIHZhciBhY3RpdmVQbGFjZWhvbGRlcnMgPSBhY3RpdmVDaG9pY2VzLmZpbHRlcihmdW5jdGlvbiAoYWN0aXZlQ2hvaWNlKSB7XG4gICAgICAgIHJldHVybiBhY3RpdmVDaG9pY2UucGxhY2Vob2xkZXIgPT09IHRydWUgJiYgYWN0aXZlQ2hvaWNlLmdyb3VwSWQgPT09IC0xO1xuICAgICAgfSk7XG4gICAgICBpZiAoYWN0aXZlUGxhY2Vob2xkZXJzLmxlbmd0aCA+PSAxKSB7XG4gICAgICAgIGNob2ljZUxpc3RGcmFnbWVudCA9IHRoaXMuX2NyZWF0ZUNob2ljZXNGcmFnbWVudChhY3RpdmVQbGFjZWhvbGRlcnMsIGNob2ljZUxpc3RGcmFnbWVudCk7XG4gICAgICB9XG4gICAgICBjaG9pY2VMaXN0RnJhZ21lbnQgPSB0aGlzLl9jcmVhdGVHcm91cHNGcmFnbWVudChhY3RpdmVHcm91cHMsIGFjdGl2ZUNob2ljZXMsIGNob2ljZUxpc3RGcmFnbWVudCk7XG4gICAgfSBlbHNlIGlmIChhY3RpdmVDaG9pY2VzLmxlbmd0aCA+PSAxKSB7XG4gICAgICBjaG9pY2VMaXN0RnJhZ21lbnQgPSB0aGlzLl9jcmVhdGVDaG9pY2VzRnJhZ21lbnQoYWN0aXZlQ2hvaWNlcywgY2hvaWNlTGlzdEZyYWdtZW50KTtcbiAgICB9XG4gICAgLy8gSWYgd2UgaGF2ZSBjaG9pY2VzIHRvIHNob3dcbiAgICBpZiAoY2hvaWNlTGlzdEZyYWdtZW50LmNoaWxkTm9kZXMgJiYgY2hvaWNlTGlzdEZyYWdtZW50LmNoaWxkTm9kZXMubGVuZ3RoID4gMCkge1xuICAgICAgdmFyIGFjdGl2ZUl0ZW1zID0gdGhpcy5fc3RvcmUuYWN0aXZlSXRlbXM7XG4gICAgICB2YXIgY2FuQWRkSXRlbSA9IHRoaXMuX2NhbkFkZEl0ZW0oYWN0aXZlSXRlbXMsIHRoaXMuaW5wdXQudmFsdWUpO1xuICAgICAgLy8gLi4uYW5kIHdlIGNhbiBzZWxlY3QgdGhlbVxuICAgICAgaWYgKGNhbkFkZEl0ZW0ucmVzcG9uc2UpIHtcbiAgICAgICAgLy8gLi4uYXBwZW5kIHRoZW0gYW5kIGhpZ2hsaWdodCB0aGUgZmlyc3QgY2hvaWNlXG4gICAgICAgIHRoaXMuY2hvaWNlTGlzdC5hcHBlbmQoY2hvaWNlTGlzdEZyYWdtZW50KTtcbiAgICAgICAgdGhpcy5faGlnaGxpZ2h0Q2hvaWNlKCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgbm90aWNlID0gdGhpcy5fZ2V0VGVtcGxhdGUoJ25vdGljZScsIGNhbkFkZEl0ZW0ubm90aWNlKTtcbiAgICAgICAgdGhpcy5jaG9pY2VMaXN0LmFwcGVuZChub3RpY2UpO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICAvLyBPdGhlcndpc2Ugc2hvdyBhIG5vdGljZVxuICAgICAgdmFyIGRyb3Bkb3duSXRlbSA9IHZvaWQgMDtcbiAgICAgIHZhciBub3RpY2UgPSB2b2lkIDA7XG4gICAgICBpZiAodGhpcy5faXNTZWFyY2hpbmcpIHtcbiAgICAgICAgbm90aWNlID0gdHlwZW9mIHRoaXMuY29uZmlnLm5vUmVzdWx0c1RleHQgPT09ICdmdW5jdGlvbicgPyB0aGlzLmNvbmZpZy5ub1Jlc3VsdHNUZXh0KCkgOiB0aGlzLmNvbmZpZy5ub1Jlc3VsdHNUZXh0O1xuICAgICAgICBkcm9wZG93bkl0ZW0gPSB0aGlzLl9nZXRUZW1wbGF0ZSgnbm90aWNlJywgbm90aWNlLCAnbm8tcmVzdWx0cycpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgbm90aWNlID0gdHlwZW9mIHRoaXMuY29uZmlnLm5vQ2hvaWNlc1RleHQgPT09ICdmdW5jdGlvbicgPyB0aGlzLmNvbmZpZy5ub0Nob2ljZXNUZXh0KCkgOiB0aGlzLmNvbmZpZy5ub0Nob2ljZXNUZXh0O1xuICAgICAgICBkcm9wZG93bkl0ZW0gPSB0aGlzLl9nZXRUZW1wbGF0ZSgnbm90aWNlJywgbm90aWNlLCAnbm8tY2hvaWNlcycpO1xuICAgICAgfVxuICAgICAgdGhpcy5jaG9pY2VMaXN0LmFwcGVuZChkcm9wZG93bkl0ZW0pO1xuICAgIH1cbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX3JlbmRlckl0ZW1zID0gZnVuY3Rpb24gKCkge1xuICAgIHZhciBhY3RpdmVJdGVtcyA9IHRoaXMuX3N0b3JlLmFjdGl2ZUl0ZW1zIHx8IFtdO1xuICAgIHRoaXMuaXRlbUxpc3QuY2xlYXIoKTtcbiAgICAvLyBDcmVhdGUgYSBmcmFnbWVudCB0byBzdG9yZSBvdXIgbGlzdCBpdGVtc1xuICAgIC8vIChzbyB3ZSBkb24ndCBoYXZlIHRvIHVwZGF0ZSB0aGUgRE9NIGZvciBlYWNoIGl0ZW0pXG4gICAgdmFyIGl0ZW1MaXN0RnJhZ21lbnQgPSB0aGlzLl9jcmVhdGVJdGVtc0ZyYWdtZW50KGFjdGl2ZUl0ZW1zKTtcbiAgICAvLyBJZiB3ZSBoYXZlIGl0ZW1zIHRvIGFkZCwgYXBwZW5kIHRoZW1cbiAgICBpZiAoaXRlbUxpc3RGcmFnbWVudC5jaGlsZE5vZGVzKSB7XG4gICAgICB0aGlzLml0ZW1MaXN0LmFwcGVuZChpdGVtTGlzdEZyYWdtZW50KTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9jcmVhdGVHcm91cHNGcmFnbWVudCA9IGZ1bmN0aW9uIChncm91cHMsIGNob2ljZXMsIGZyYWdtZW50KSB7XG4gICAgdmFyIF90aGlzID0gdGhpcztcbiAgICBpZiAoZnJhZ21lbnQgPT09IHZvaWQgMCkge1xuICAgICAgZnJhZ21lbnQgPSBkb2N1bWVudC5jcmVhdGVEb2N1bWVudEZyYWdtZW50KCk7XG4gICAgfVxuICAgIHZhciBnZXRHcm91cENob2ljZXMgPSBmdW5jdGlvbiAoZ3JvdXApIHtcbiAgICAgIHJldHVybiBjaG9pY2VzLmZpbHRlcihmdW5jdGlvbiAoY2hvaWNlKSB7XG4gICAgICAgIGlmIChfdGhpcy5faXNTZWxlY3RPbmVFbGVtZW50KSB7XG4gICAgICAgICAgcmV0dXJuIGNob2ljZS5ncm91cElkID09PSBncm91cC5pZDtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gY2hvaWNlLmdyb3VwSWQgPT09IGdyb3VwLmlkICYmIChfdGhpcy5jb25maWcucmVuZGVyU2VsZWN0ZWRDaG9pY2VzID09PSAnYWx3YXlzJyB8fCAhY2hvaWNlLnNlbGVjdGVkKTtcbiAgICAgIH0pO1xuICAgIH07XG4gICAgLy8gSWYgc29ydGluZyBpcyBlbmFibGVkLCBmaWx0ZXIgZ3JvdXBzXG4gICAgaWYgKHRoaXMuY29uZmlnLnNob3VsZFNvcnQpIHtcbiAgICAgIGdyb3Vwcy5zb3J0KHRoaXMuY29uZmlnLnNvcnRlcik7XG4gICAgfVxuICAgIGdyb3Vwcy5mb3JFYWNoKGZ1bmN0aW9uIChncm91cCkge1xuICAgICAgdmFyIGdyb3VwQ2hvaWNlcyA9IGdldEdyb3VwQ2hvaWNlcyhncm91cCk7XG4gICAgICBpZiAoZ3JvdXBDaG9pY2VzLmxlbmd0aCA+PSAxKSB7XG4gICAgICAgIHZhciBkcm9wZG93bkdyb3VwID0gX3RoaXMuX2dldFRlbXBsYXRlKCdjaG9pY2VHcm91cCcsIGdyb3VwKTtcbiAgICAgICAgZnJhZ21lbnQuYXBwZW5kQ2hpbGQoZHJvcGRvd25Hcm91cCk7XG4gICAgICAgIF90aGlzLl9jcmVhdGVDaG9pY2VzRnJhZ21lbnQoZ3JvdXBDaG9pY2VzLCBmcmFnbWVudCwgdHJ1ZSk7XG4gICAgICB9XG4gICAgfSk7XG4gICAgcmV0dXJuIGZyYWdtZW50O1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fY3JlYXRlQ2hvaWNlc0ZyYWdtZW50ID0gZnVuY3Rpb24gKGNob2ljZXMsIGZyYWdtZW50LCB3aXRoaW5Hcm91cCkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgaWYgKGZyYWdtZW50ID09PSB2b2lkIDApIHtcbiAgICAgIGZyYWdtZW50ID0gZG9jdW1lbnQuY3JlYXRlRG9jdW1lbnRGcmFnbWVudCgpO1xuICAgIH1cbiAgICBpZiAod2l0aGluR3JvdXAgPT09IHZvaWQgMCkge1xuICAgICAgd2l0aGluR3JvdXAgPSBmYWxzZTtcbiAgICB9XG4gICAgLy8gQ3JlYXRlIGEgZnJhZ21lbnQgdG8gc3RvcmUgb3VyIGxpc3QgaXRlbXMgKHNvIHdlIGRvbid0IGhhdmUgdG8gdXBkYXRlIHRoZSBET00gZm9yIGVhY2ggaXRlbSlcbiAgICB2YXIgX2EgPSB0aGlzLmNvbmZpZyxcbiAgICAgIHJlbmRlclNlbGVjdGVkQ2hvaWNlcyA9IF9hLnJlbmRlclNlbGVjdGVkQ2hvaWNlcyxcbiAgICAgIHNlYXJjaFJlc3VsdExpbWl0ID0gX2Euc2VhcmNoUmVzdWx0TGltaXQsXG4gICAgICByZW5kZXJDaG9pY2VMaW1pdCA9IF9hLnJlbmRlckNob2ljZUxpbWl0O1xuICAgIHZhciBmaWx0ZXIgPSB0aGlzLl9pc1NlYXJjaGluZyA/IHV0aWxzXzEuc29ydEJ5U2NvcmUgOiB0aGlzLmNvbmZpZy5zb3J0ZXI7XG4gICAgdmFyIGFwcGVuZENob2ljZSA9IGZ1bmN0aW9uIChjaG9pY2UpIHtcbiAgICAgIHZhciBzaG91bGRSZW5kZXIgPSByZW5kZXJTZWxlY3RlZENob2ljZXMgPT09ICdhdXRvJyA/IF90aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQgfHwgIWNob2ljZS5zZWxlY3RlZCA6IHRydWU7XG4gICAgICBpZiAoc2hvdWxkUmVuZGVyKSB7XG4gICAgICAgIHZhciBkcm9wZG93bkl0ZW0gPSBfdGhpcy5fZ2V0VGVtcGxhdGUoJ2Nob2ljZScsIGNob2ljZSwgX3RoaXMuY29uZmlnLml0ZW1TZWxlY3RUZXh0KTtcbiAgICAgICAgZnJhZ21lbnQuYXBwZW5kQ2hpbGQoZHJvcGRvd25JdGVtKTtcbiAgICAgIH1cbiAgICB9O1xuICAgIHZhciByZW5kZXJlcmFibGVDaG9pY2VzID0gY2hvaWNlcztcbiAgICBpZiAocmVuZGVyU2VsZWN0ZWRDaG9pY2VzID09PSAnYXV0bycgJiYgIXRoaXMuX2lzU2VsZWN0T25lRWxlbWVudCkge1xuICAgICAgcmVuZGVyZXJhYmxlQ2hvaWNlcyA9IGNob2ljZXMuZmlsdGVyKGZ1bmN0aW9uIChjaG9pY2UpIHtcbiAgICAgICAgcmV0dXJuICFjaG9pY2Uuc2VsZWN0ZWQ7XG4gICAgICB9KTtcbiAgICB9XG4gICAgLy8gU3BsaXQgYXJyYXkgaW50byBwbGFjZWhvbGRlcnMgYW5kIFwibm9ybWFsXCIgY2hvaWNlc1xuICAgIHZhciBfYiA9IHJlbmRlcmVyYWJsZUNob2ljZXMucmVkdWNlKGZ1bmN0aW9uIChhY2MsIGNob2ljZSkge1xuICAgICAgICBpZiAoY2hvaWNlLnBsYWNlaG9sZGVyKSB7XG4gICAgICAgICAgYWNjLnBsYWNlaG9sZGVyQ2hvaWNlcy5wdXNoKGNob2ljZSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgYWNjLm5vcm1hbENob2ljZXMucHVzaChjaG9pY2UpO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBhY2M7XG4gICAgICB9LCB7XG4gICAgICAgIHBsYWNlaG9sZGVyQ2hvaWNlczogW10sXG4gICAgICAgIG5vcm1hbENob2ljZXM6IFtdXG4gICAgICB9KSxcbiAgICAgIHBsYWNlaG9sZGVyQ2hvaWNlcyA9IF9iLnBsYWNlaG9sZGVyQ2hvaWNlcyxcbiAgICAgIG5vcm1hbENob2ljZXMgPSBfYi5ub3JtYWxDaG9pY2VzO1xuICAgIC8vIElmIHNvcnRpbmcgaXMgZW5hYmxlZCBvciB0aGUgdXNlciBpcyBzZWFyY2hpbmcsIGZpbHRlciBjaG9pY2VzXG4gICAgaWYgKHRoaXMuY29uZmlnLnNob3VsZFNvcnQgfHwgdGhpcy5faXNTZWFyY2hpbmcpIHtcbiAgICAgIG5vcm1hbENob2ljZXMuc29ydChmaWx0ZXIpO1xuICAgIH1cbiAgICB2YXIgY2hvaWNlTGltaXQgPSByZW5kZXJlcmFibGVDaG9pY2VzLmxlbmd0aDtcbiAgICAvLyBQcmVwZW5kIHBsYWNlaG9sZWRlclxuICAgIHZhciBzb3J0ZWRDaG9pY2VzID0gdGhpcy5faXNTZWxlY3RPbmVFbGVtZW50ID8gX19zcHJlYWRBcnJheShfX3NwcmVhZEFycmF5KFtdLCBwbGFjZWhvbGRlckNob2ljZXMsIHRydWUpLCBub3JtYWxDaG9pY2VzLCB0cnVlKSA6IG5vcm1hbENob2ljZXM7XG4gICAgaWYgKHRoaXMuX2lzU2VhcmNoaW5nKSB7XG4gICAgICBjaG9pY2VMaW1pdCA9IHNlYXJjaFJlc3VsdExpbWl0O1xuICAgIH0gZWxzZSBpZiAocmVuZGVyQ2hvaWNlTGltaXQgJiYgcmVuZGVyQ2hvaWNlTGltaXQgPiAwICYmICF3aXRoaW5Hcm91cCkge1xuICAgICAgY2hvaWNlTGltaXQgPSByZW5kZXJDaG9pY2VMaW1pdDtcbiAgICB9XG4gICAgLy8gQWRkIGVhY2ggY2hvaWNlIHRvIGRyb3Bkb3duIHdpdGhpbiByYW5nZVxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgY2hvaWNlTGltaXQ7IGkgKz0gMSkge1xuICAgICAgaWYgKHNvcnRlZENob2ljZXNbaV0pIHtcbiAgICAgICAgYXBwZW5kQ2hvaWNlKHNvcnRlZENob2ljZXNbaV0pO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gZnJhZ21lbnQ7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9jcmVhdGVJdGVtc0ZyYWdtZW50ID0gZnVuY3Rpb24gKGl0ZW1zLCBmcmFnbWVudCkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgaWYgKGZyYWdtZW50ID09PSB2b2lkIDApIHtcbiAgICAgIGZyYWdtZW50ID0gZG9jdW1lbnQuY3JlYXRlRG9jdW1lbnRGcmFnbWVudCgpO1xuICAgIH1cbiAgICAvLyBDcmVhdGUgZnJhZ21lbnQgdG8gYWRkIGVsZW1lbnRzIHRvXG4gICAgdmFyIF9hID0gdGhpcy5jb25maWcsXG4gICAgICBzaG91bGRTb3J0SXRlbXMgPSBfYS5zaG91bGRTb3J0SXRlbXMsXG4gICAgICBzb3J0ZXIgPSBfYS5zb3J0ZXIsXG4gICAgICByZW1vdmVJdGVtQnV0dG9uID0gX2EucmVtb3ZlSXRlbUJ1dHRvbjtcbiAgICAvLyBJZiBzb3J0aW5nIGlzIGVuYWJsZWQsIGZpbHRlciBpdGVtc1xuICAgIGlmIChzaG91bGRTb3J0SXRlbXMgJiYgIXRoaXMuX2lzU2VsZWN0T25lRWxlbWVudCkge1xuICAgICAgaXRlbXMuc29ydChzb3J0ZXIpO1xuICAgIH1cbiAgICBpZiAodGhpcy5faXNUZXh0RWxlbWVudCkge1xuICAgICAgLy8gVXBkYXRlIHRoZSB2YWx1ZSBvZiB0aGUgaGlkZGVuIGlucHV0XG4gICAgICB0aGlzLnBhc3NlZEVsZW1lbnQudmFsdWUgPSBpdGVtcy5tYXAoZnVuY3Rpb24gKF9hKSB7XG4gICAgICAgIHZhciB2YWx1ZSA9IF9hLnZhbHVlO1xuICAgICAgICByZXR1cm4gdmFsdWU7XG4gICAgICB9KS5qb2luKHRoaXMuY29uZmlnLmRlbGltaXRlcik7XG4gICAgfSBlbHNlIHtcbiAgICAgIC8vIFVwZGF0ZSB0aGUgb3B0aW9ucyBvZiB0aGUgaGlkZGVuIGlucHV0XG4gICAgICB0aGlzLnBhc3NlZEVsZW1lbnQub3B0aW9ucyA9IGl0ZW1zO1xuICAgIH1cbiAgICB2YXIgYWRkSXRlbVRvRnJhZ21lbnQgPSBmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgLy8gQ3JlYXRlIG5ldyBsaXN0IGVsZW1lbnRcbiAgICAgIHZhciBsaXN0SXRlbSA9IF90aGlzLl9nZXRUZW1wbGF0ZSgnaXRlbScsIGl0ZW0sIHJlbW92ZUl0ZW1CdXR0b24pO1xuICAgICAgLy8gQXBwZW5kIGl0IHRvIGxpc3RcbiAgICAgIGZyYWdtZW50LmFwcGVuZENoaWxkKGxpc3RJdGVtKTtcbiAgICB9O1xuICAgIC8vIEFkZCBlYWNoIGxpc3QgaXRlbSB0byBsaXN0XG4gICAgaXRlbXMuZm9yRWFjaChhZGRJdGVtVG9GcmFnbWVudCk7XG4gICAgcmV0dXJuIGZyYWdtZW50O1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fdHJpZ2dlckNoYW5nZSA9IGZ1bmN0aW9uICh2YWx1ZSkge1xuICAgIGlmICh2YWx1ZSA9PT0gdW5kZWZpbmVkIHx8IHZhbHVlID09PSBudWxsKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHRoaXMucGFzc2VkRWxlbWVudC50cmlnZ2VyRXZlbnQoY29uc3RhbnRzXzEuRVZFTlRTLmNoYW5nZSwge1xuICAgICAgdmFsdWU6IHZhbHVlXG4gICAgfSk7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9zZWxlY3RQbGFjZWhvbGRlckNob2ljZSA9IGZ1bmN0aW9uIChwbGFjZWhvbGRlckNob2ljZSkge1xuICAgIHRoaXMuX2FkZEl0ZW0oe1xuICAgICAgdmFsdWU6IHBsYWNlaG9sZGVyQ2hvaWNlLnZhbHVlLFxuICAgICAgbGFiZWw6IHBsYWNlaG9sZGVyQ2hvaWNlLmxhYmVsLFxuICAgICAgY2hvaWNlSWQ6IHBsYWNlaG9sZGVyQ2hvaWNlLmlkLFxuICAgICAgZ3JvdXBJZDogcGxhY2Vob2xkZXJDaG9pY2UuZ3JvdXBJZCxcbiAgICAgIHBsYWNlaG9sZGVyOiBwbGFjZWhvbGRlckNob2ljZS5wbGFjZWhvbGRlclxuICAgIH0pO1xuICAgIHRoaXMuX3RyaWdnZXJDaGFuZ2UocGxhY2Vob2xkZXJDaG9pY2UudmFsdWUpO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5faGFuZGxlQnV0dG9uQWN0aW9uID0gZnVuY3Rpb24gKGFjdGl2ZUl0ZW1zLCBlbGVtZW50KSB7XG4gICAgaWYgKCFhY3RpdmVJdGVtcyB8fCAhZWxlbWVudCB8fCAhdGhpcy5jb25maWcucmVtb3ZlSXRlbXMgfHwgIXRoaXMuY29uZmlnLnJlbW92ZUl0ZW1CdXR0b24pIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdmFyIGl0ZW1JZCA9IGVsZW1lbnQucGFyZW50Tm9kZSAmJiBlbGVtZW50LnBhcmVudE5vZGUuZGF0YXNldC5pZDtcbiAgICB2YXIgaXRlbVRvUmVtb3ZlID0gaXRlbUlkICYmIGFjdGl2ZUl0ZW1zLmZpbmQoZnVuY3Rpb24gKGl0ZW0pIHtcbiAgICAgIHJldHVybiBpdGVtLmlkID09PSBwYXJzZUludChpdGVtSWQsIDEwKTtcbiAgICB9KTtcbiAgICBpZiAoIWl0ZW1Ub1JlbW92ZSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICAvLyBSZW1vdmUgaXRlbSBhc3NvY2lhdGVkIHdpdGggYnV0dG9uXG4gICAgdGhpcy5fcmVtb3ZlSXRlbShpdGVtVG9SZW1vdmUpO1xuICAgIHRoaXMuX3RyaWdnZXJDaGFuZ2UoaXRlbVRvUmVtb3ZlLnZhbHVlKTtcbiAgICBpZiAodGhpcy5faXNTZWxlY3RPbmVFbGVtZW50ICYmIHRoaXMuX3N0b3JlLnBsYWNlaG9sZGVyQ2hvaWNlKSB7XG4gICAgICB0aGlzLl9zZWxlY3RQbGFjZWhvbGRlckNob2ljZSh0aGlzLl9zdG9yZS5wbGFjZWhvbGRlckNob2ljZSk7XG4gICAgfVxuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5faGFuZGxlSXRlbUFjdGlvbiA9IGZ1bmN0aW9uIChhY3RpdmVJdGVtcywgZWxlbWVudCwgaGFzU2hpZnRLZXkpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIGlmIChoYXNTaGlmdEtleSA9PT0gdm9pZCAwKSB7XG4gICAgICBoYXNTaGlmdEtleSA9IGZhbHNlO1xuICAgIH1cbiAgICBpZiAoIWFjdGl2ZUl0ZW1zIHx8ICFlbGVtZW50IHx8ICF0aGlzLmNvbmZpZy5yZW1vdmVJdGVtcyB8fCB0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdmFyIHBhc3NlZElkID0gZWxlbWVudC5kYXRhc2V0LmlkO1xuICAgIC8vIFdlIG9ubHkgd2FudCB0byBzZWxlY3Qgb25lIGl0ZW0gd2l0aCBhIGNsaWNrXG4gICAgLy8gc28gd2UgZGVzZWxlY3QgYW55IGl0ZW1zIHRoYXQgYXJlbid0IHRoZSB0YXJnZXRcbiAgICAvLyB1bmxlc3Mgc2hpZnQgaXMgYmVpbmcgcHJlc3NlZFxuICAgIGFjdGl2ZUl0ZW1zLmZvckVhY2goZnVuY3Rpb24gKGl0ZW0pIHtcbiAgICAgIGlmIChpdGVtLmlkID09PSBwYXJzZUludChcIlwiLmNvbmNhdChwYXNzZWRJZCksIDEwKSAmJiAhaXRlbS5oaWdobGlnaHRlZCkge1xuICAgICAgICBfdGhpcy5oaWdobGlnaHRJdGVtKGl0ZW0pO1xuICAgICAgfSBlbHNlIGlmICghaGFzU2hpZnRLZXkgJiYgaXRlbS5oaWdobGlnaHRlZCkge1xuICAgICAgICBfdGhpcy51bmhpZ2hsaWdodEl0ZW0oaXRlbSk7XG4gICAgICB9XG4gICAgfSk7XG4gICAgLy8gRm9jdXMgaW5wdXQgYXMgd2l0aG91dCBmb2N1cywgYSB1c2VyIGNhbm5vdCBkbyBhbnl0aGluZyB3aXRoIGFcbiAgICAvLyBoaWdobGlnaHRlZCBpdGVtXG4gICAgdGhpcy5pbnB1dC5mb2N1cygpO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5faGFuZGxlQ2hvaWNlQWN0aW9uID0gZnVuY3Rpb24gKGFjdGl2ZUl0ZW1zLCBlbGVtZW50KSB7XG4gICAgaWYgKCFhY3RpdmVJdGVtcyB8fCAhZWxlbWVudCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICAvLyBJZiB3ZSBhcmUgY2xpY2tpbmcgb24gYW4gb3B0aW9uXG4gICAgdmFyIGlkID0gZWxlbWVudC5kYXRhc2V0LmlkO1xuICAgIHZhciBjaG9pY2UgPSBpZCAmJiB0aGlzLl9zdG9yZS5nZXRDaG9pY2VCeUlkKGlkKTtcbiAgICBpZiAoIWNob2ljZSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB2YXIgcGFzc2VkS2V5Q29kZSA9IGFjdGl2ZUl0ZW1zWzBdICYmIGFjdGl2ZUl0ZW1zWzBdLmtleUNvZGUgPyBhY3RpdmVJdGVtc1swXS5rZXlDb2RlIDogdW5kZWZpbmVkO1xuICAgIHZhciBoYXNBY3RpdmVEcm9wZG93biA9IHRoaXMuZHJvcGRvd24uaXNBY3RpdmU7XG4gICAgLy8gVXBkYXRlIGNob2ljZSBrZXlDb2RlXG4gICAgY2hvaWNlLmtleUNvZGUgPSBwYXNzZWRLZXlDb2RlO1xuICAgIHRoaXMucGFzc2VkRWxlbWVudC50cmlnZ2VyRXZlbnQoY29uc3RhbnRzXzEuRVZFTlRTLmNob2ljZSwge1xuICAgICAgY2hvaWNlOiBjaG9pY2VcbiAgICB9KTtcbiAgICBpZiAoIWNob2ljZS5zZWxlY3RlZCAmJiAhY2hvaWNlLmRpc2FibGVkKSB7XG4gICAgICB2YXIgY2FuQWRkSXRlbSA9IHRoaXMuX2NhbkFkZEl0ZW0oYWN0aXZlSXRlbXMsIGNob2ljZS52YWx1ZSk7XG4gICAgICBpZiAoY2FuQWRkSXRlbS5yZXNwb25zZSkge1xuICAgICAgICB0aGlzLl9hZGRJdGVtKHtcbiAgICAgICAgICB2YWx1ZTogY2hvaWNlLnZhbHVlLFxuICAgICAgICAgIGxhYmVsOiBjaG9pY2UubGFiZWwsXG4gICAgICAgICAgY2hvaWNlSWQ6IGNob2ljZS5pZCxcbiAgICAgICAgICBncm91cElkOiBjaG9pY2UuZ3JvdXBJZCxcbiAgICAgICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjaG9pY2UuY3VzdG9tUHJvcGVydGllcyxcbiAgICAgICAgICBwbGFjZWhvbGRlcjogY2hvaWNlLnBsYWNlaG9sZGVyLFxuICAgICAgICAgIGtleUNvZGU6IGNob2ljZS5rZXlDb2RlXG4gICAgICAgIH0pO1xuICAgICAgICB0aGlzLl90cmlnZ2VyQ2hhbmdlKGNob2ljZS52YWx1ZSk7XG4gICAgICB9XG4gICAgfVxuICAgIHRoaXMuY2xlYXJJbnB1dCgpO1xuICAgIC8vIFdlIHdhbnQgdG8gY2xvc2UgdGhlIGRyb3Bkb3duIGlmIHdlIGFyZSBkZWFsaW5nIHdpdGggYSBzaW5nbGUgc2VsZWN0IGJveFxuICAgIGlmIChoYXNBY3RpdmVEcm9wZG93biAmJiB0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIHRoaXMuaGlkZURyb3Bkb3duKHRydWUpO1xuICAgICAgdGhpcy5jb250YWluZXJPdXRlci5mb2N1cygpO1xuICAgIH1cbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2hhbmRsZUJhY2tzcGFjZSA9IGZ1bmN0aW9uIChhY3RpdmVJdGVtcykge1xuICAgIGlmICghdGhpcy5jb25maWcucmVtb3ZlSXRlbXMgfHwgIWFjdGl2ZUl0ZW1zKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHZhciBsYXN0SXRlbSA9IGFjdGl2ZUl0ZW1zW2FjdGl2ZUl0ZW1zLmxlbmd0aCAtIDFdO1xuICAgIHZhciBoYXNIaWdobGlnaHRlZEl0ZW1zID0gYWN0aXZlSXRlbXMuc29tZShmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgcmV0dXJuIGl0ZW0uaGlnaGxpZ2h0ZWQ7XG4gICAgfSk7XG4gICAgLy8gSWYgZWRpdGluZyB0aGUgbGFzdCBpdGVtIGlzIGFsbG93ZWQgYW5kIHRoZXJlIGFyZSBub3Qgb3RoZXIgc2VsZWN0ZWQgaXRlbXMsXG4gICAgLy8gd2UgY2FuIGVkaXQgdGhlIGl0ZW0gdmFsdWUuIE90aGVyd2lzZSBpZiB3ZSBjYW4gcmVtb3ZlIGl0ZW1zLCByZW1vdmUgYWxsIHNlbGVjdGVkIGl0ZW1zXG4gICAgaWYgKHRoaXMuY29uZmlnLmVkaXRJdGVtcyAmJiAhaGFzSGlnaGxpZ2h0ZWRJdGVtcyAmJiBsYXN0SXRlbSkge1xuICAgICAgdGhpcy5pbnB1dC52YWx1ZSA9IGxhc3RJdGVtLnZhbHVlO1xuICAgICAgdGhpcy5pbnB1dC5zZXRXaWR0aCgpO1xuICAgICAgdGhpcy5fcmVtb3ZlSXRlbShsYXN0SXRlbSk7XG4gICAgICB0aGlzLl90cmlnZ2VyQ2hhbmdlKGxhc3RJdGVtLnZhbHVlKTtcbiAgICB9IGVsc2Uge1xuICAgICAgaWYgKCFoYXNIaWdobGlnaHRlZEl0ZW1zKSB7XG4gICAgICAgIC8vIEhpZ2hsaWdodCBsYXN0IGl0ZW0gaWYgbm9uZSBhbHJlYWR5IGhpZ2hsaWdodGVkXG4gICAgICAgIHRoaXMuaGlnaGxpZ2h0SXRlbShsYXN0SXRlbSwgZmFsc2UpO1xuICAgICAgfVxuICAgICAgdGhpcy5yZW1vdmVIaWdobGlnaHRlZEl0ZW1zKHRydWUpO1xuICAgIH1cbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX3N0YXJ0TG9hZGluZyA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaCgoMCwgbWlzY18xLnNldElzTG9hZGluZykodHJ1ZSkpO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fc3RvcExvYWRpbmcgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5fc3RvcmUuZGlzcGF0Y2goKDAsIG1pc2NfMS5zZXRJc0xvYWRpbmcpKGZhbHNlKSk7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9oYW5kbGVMb2FkaW5nU3RhdGUgPSBmdW5jdGlvbiAoc2V0TG9hZGluZykge1xuICAgIGlmIChzZXRMb2FkaW5nID09PSB2b2lkIDApIHtcbiAgICAgIHNldExvYWRpbmcgPSB0cnVlO1xuICAgIH1cbiAgICB2YXIgcGxhY2Vob2xkZXJJdGVtID0gdGhpcy5pdGVtTGlzdC5nZXRDaGlsZChcIi5cIi5jb25jYXQodGhpcy5jb25maWcuY2xhc3NOYW1lcy5wbGFjZWhvbGRlcikpO1xuICAgIGlmIChzZXRMb2FkaW5nKSB7XG4gICAgICB0aGlzLmRpc2FibGUoKTtcbiAgICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuYWRkTG9hZGluZ1N0YXRlKCk7XG4gICAgICBpZiAodGhpcy5faXNTZWxlY3RPbmVFbGVtZW50KSB7XG4gICAgICAgIGlmICghcGxhY2Vob2xkZXJJdGVtKSB7XG4gICAgICAgICAgcGxhY2Vob2xkZXJJdGVtID0gdGhpcy5fZ2V0VGVtcGxhdGUoJ3BsYWNlaG9sZGVyJywgdGhpcy5jb25maWcubG9hZGluZ1RleHQpO1xuICAgICAgICAgIGlmIChwbGFjZWhvbGRlckl0ZW0pIHtcbiAgICAgICAgICAgIHRoaXMuaXRlbUxpc3QuYXBwZW5kKHBsYWNlaG9sZGVySXRlbSk7XG4gICAgICAgICAgfVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHBsYWNlaG9sZGVySXRlbS5pbm5lckhUTUwgPSB0aGlzLmNvbmZpZy5sb2FkaW5nVGV4dDtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5pbnB1dC5wbGFjZWhvbGRlciA9IHRoaXMuY29uZmlnLmxvYWRpbmdUZXh0O1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLmVuYWJsZSgpO1xuICAgICAgdGhpcy5jb250YWluZXJPdXRlci5yZW1vdmVMb2FkaW5nU3RhdGUoKTtcbiAgICAgIGlmICh0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgICAgaWYgKHBsYWNlaG9sZGVySXRlbSkge1xuICAgICAgICAgIHBsYWNlaG9sZGVySXRlbS5pbm5lckhUTUwgPSB0aGlzLl9wbGFjZWhvbGRlclZhbHVlIHx8ICcnO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLmlucHV0LnBsYWNlaG9sZGVyID0gdGhpcy5fcGxhY2Vob2xkZXJWYWx1ZSB8fCAnJztcbiAgICAgIH1cbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9oYW5kbGVTZWFyY2ggPSBmdW5jdGlvbiAodmFsdWUpIHtcbiAgICBpZiAoIXRoaXMuaW5wdXQuaXNGb2N1c3NlZCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB2YXIgY2hvaWNlcyA9IHRoaXMuX3N0b3JlLmNob2ljZXM7XG4gICAgdmFyIF9hID0gdGhpcy5jb25maWcsXG4gICAgICBzZWFyY2hGbG9vciA9IF9hLnNlYXJjaEZsb29yLFxuICAgICAgc2VhcmNoQ2hvaWNlcyA9IF9hLnNlYXJjaENob2ljZXM7XG4gICAgdmFyIGhhc1VuYWN0aXZlQ2hvaWNlcyA9IGNob2ljZXMuc29tZShmdW5jdGlvbiAob3B0aW9uKSB7XG4gICAgICByZXR1cm4gIW9wdGlvbi5hY3RpdmU7XG4gICAgfSk7XG4gICAgLy8gQ2hlY2sgdGhhdCB3ZSBoYXZlIGEgdmFsdWUgdG8gc2VhcmNoIGFuZCB0aGUgaW5wdXQgd2FzIGFuIGFscGhhbnVtZXJpYyBjaGFyYWN0ZXJcbiAgICBpZiAodmFsdWUgIT09IG51bGwgJiYgdHlwZW9mIHZhbHVlICE9PSAndW5kZWZpbmVkJyAmJiB2YWx1ZS5sZW5ndGggPj0gc2VhcmNoRmxvb3IpIHtcbiAgICAgIHZhciByZXN1bHRDb3VudCA9IHNlYXJjaENob2ljZXMgPyB0aGlzLl9zZWFyY2hDaG9pY2VzKHZhbHVlKSA6IDA7XG4gICAgICAvLyBUcmlnZ2VyIHNlYXJjaCBldmVudFxuICAgICAgdGhpcy5wYXNzZWRFbGVtZW50LnRyaWdnZXJFdmVudChjb25zdGFudHNfMS5FVkVOVFMuc2VhcmNoLCB7XG4gICAgICAgIHZhbHVlOiB2YWx1ZSxcbiAgICAgICAgcmVzdWx0Q291bnQ6IHJlc3VsdENvdW50XG4gICAgICB9KTtcbiAgICB9IGVsc2UgaWYgKGhhc1VuYWN0aXZlQ2hvaWNlcykge1xuICAgICAgLy8gT3RoZXJ3aXNlIHJlc2V0IGNob2ljZXMgdG8gYWN0aXZlXG4gICAgICB0aGlzLl9pc1NlYXJjaGluZyA9IGZhbHNlO1xuICAgICAgdGhpcy5fc3RvcmUuZGlzcGF0Y2goKDAsIGNob2ljZXNfMS5hY3RpdmF0ZUNob2ljZXMpKHRydWUpKTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9jYW5BZGRJdGVtID0gZnVuY3Rpb24gKGFjdGl2ZUl0ZW1zLCB2YWx1ZSkge1xuICAgIHZhciBjYW5BZGRJdGVtID0gdHJ1ZTtcbiAgICB2YXIgbm90aWNlID0gdHlwZW9mIHRoaXMuY29uZmlnLmFkZEl0ZW1UZXh0ID09PSAnZnVuY3Rpb24nID8gdGhpcy5jb25maWcuYWRkSXRlbVRleHQodmFsdWUpIDogdGhpcy5jb25maWcuYWRkSXRlbVRleHQ7XG4gICAgaWYgKCF0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIHZhciBpc0R1cGxpY2F0ZVZhbHVlID0gKDAsIHV0aWxzXzEuZXhpc3RzSW5BcnJheSkoYWN0aXZlSXRlbXMsIHZhbHVlKTtcbiAgICAgIGlmICh0aGlzLmNvbmZpZy5tYXhJdGVtQ291bnQgPiAwICYmIHRoaXMuY29uZmlnLm1heEl0ZW1Db3VudCA8PSBhY3RpdmVJdGVtcy5sZW5ndGgpIHtcbiAgICAgICAgLy8gSWYgdGhlcmUgaXMgYSBtYXggZW50cnkgbGltaXQgYW5kIHdlIGhhdmUgcmVhY2hlZCB0aGF0IGxpbWl0XG4gICAgICAgIC8vIGRvbid0IHVwZGF0ZVxuICAgICAgICBjYW5BZGRJdGVtID0gZmFsc2U7XG4gICAgICAgIG5vdGljZSA9IHR5cGVvZiB0aGlzLmNvbmZpZy5tYXhJdGVtVGV4dCA9PT0gJ2Z1bmN0aW9uJyA/IHRoaXMuY29uZmlnLm1heEl0ZW1UZXh0KHRoaXMuY29uZmlnLm1heEl0ZW1Db3VudCkgOiB0aGlzLmNvbmZpZy5tYXhJdGVtVGV4dDtcbiAgICAgIH1cbiAgICAgIGlmICghdGhpcy5jb25maWcuZHVwbGljYXRlSXRlbXNBbGxvd2VkICYmIGlzRHVwbGljYXRlVmFsdWUgJiYgY2FuQWRkSXRlbSkge1xuICAgICAgICBjYW5BZGRJdGVtID0gZmFsc2U7XG4gICAgICAgIG5vdGljZSA9IHR5cGVvZiB0aGlzLmNvbmZpZy51bmlxdWVJdGVtVGV4dCA9PT0gJ2Z1bmN0aW9uJyA/IHRoaXMuY29uZmlnLnVuaXF1ZUl0ZW1UZXh0KHZhbHVlKSA6IHRoaXMuY29uZmlnLnVuaXF1ZUl0ZW1UZXh0O1xuICAgICAgfVxuICAgICAgaWYgKHRoaXMuX2lzVGV4dEVsZW1lbnQgJiYgdGhpcy5jb25maWcuYWRkSXRlbXMgJiYgY2FuQWRkSXRlbSAmJiB0eXBlb2YgdGhpcy5jb25maWcuYWRkSXRlbUZpbHRlciA9PT0gJ2Z1bmN0aW9uJyAmJiAhdGhpcy5jb25maWcuYWRkSXRlbUZpbHRlcih2YWx1ZSkpIHtcbiAgICAgICAgY2FuQWRkSXRlbSA9IGZhbHNlO1xuICAgICAgICBub3RpY2UgPSB0eXBlb2YgdGhpcy5jb25maWcuY3VzdG9tQWRkSXRlbVRleHQgPT09ICdmdW5jdGlvbicgPyB0aGlzLmNvbmZpZy5jdXN0b21BZGRJdGVtVGV4dCh2YWx1ZSkgOiB0aGlzLmNvbmZpZy5jdXN0b21BZGRJdGVtVGV4dDtcbiAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIHtcbiAgICAgIHJlc3BvbnNlOiBjYW5BZGRJdGVtLFxuICAgICAgbm90aWNlOiBub3RpY2VcbiAgICB9O1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fc2VhcmNoQ2hvaWNlcyA9IGZ1bmN0aW9uICh2YWx1ZSkge1xuICAgIHZhciBuZXdWYWx1ZSA9IHR5cGVvZiB2YWx1ZSA9PT0gJ3N0cmluZycgPyB2YWx1ZS50cmltKCkgOiB2YWx1ZTtcbiAgICB2YXIgY3VycmVudFZhbHVlID0gdHlwZW9mIHRoaXMuX2N1cnJlbnRWYWx1ZSA9PT0gJ3N0cmluZycgPyB0aGlzLl9jdXJyZW50VmFsdWUudHJpbSgpIDogdGhpcy5fY3VycmVudFZhbHVlO1xuICAgIGlmIChuZXdWYWx1ZS5sZW5ndGggPCAxICYmIG5ld1ZhbHVlID09PSBcIlwiLmNvbmNhdChjdXJyZW50VmFsdWUsIFwiIFwiKSkge1xuICAgICAgcmV0dXJuIDA7XG4gICAgfVxuICAgIC8vIElmIG5ldyB2YWx1ZSBtYXRjaGVzIHRoZSBkZXNpcmVkIGxlbmd0aCBhbmQgaXMgbm90IHRoZSBzYW1lIGFzIHRoZSBjdXJyZW50IHZhbHVlIHdpdGggYSBzcGFjZVxuICAgIHZhciBoYXlzdGFjayA9IHRoaXMuX3N0b3JlLnNlYXJjaGFibGVDaG9pY2VzO1xuICAgIHZhciBuZWVkbGUgPSBuZXdWYWx1ZTtcbiAgICB2YXIgb3B0aW9ucyA9IE9iamVjdC5hc3NpZ24odGhpcy5jb25maWcuZnVzZU9wdGlvbnMsIHtcbiAgICAgIGtleXM6IF9fc3ByZWFkQXJyYXkoW10sIHRoaXMuY29uZmlnLnNlYXJjaEZpZWxkcywgdHJ1ZSksXG4gICAgICBpbmNsdWRlTWF0Y2hlczogdHJ1ZVxuICAgIH0pO1xuICAgIHZhciBmdXNlID0gbmV3IGZ1c2VfanNfMS5kZWZhdWx0KGhheXN0YWNrLCBvcHRpb25zKTtcbiAgICB2YXIgcmVzdWx0cyA9IGZ1c2Uuc2VhcmNoKG5lZWRsZSk7IC8vIHNlZSBodHRwczovL2dpdGh1Yi5jb20va3Jpc2svRnVzZS9pc3N1ZXMvMzAzXG4gICAgdGhpcy5fY3VycmVudFZhbHVlID0gbmV3VmFsdWU7XG4gICAgdGhpcy5faGlnaGxpZ2h0UG9zaXRpb24gPSAwO1xuICAgIHRoaXMuX2lzU2VhcmNoaW5nID0gdHJ1ZTtcbiAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaCgoMCwgY2hvaWNlc18xLmZpbHRlckNob2ljZXMpKHJlc3VsdHMpKTtcbiAgICByZXR1cm4gcmVzdWx0cy5sZW5ndGg7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9hZGRFdmVudExpc3RlbmVycyA9IGZ1bmN0aW9uICgpIHtcbiAgICB2YXIgZG9jdW1lbnRFbGVtZW50ID0gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50O1xuICAgIC8vIGNhcHR1cmUgZXZlbnRzIC0gY2FuIGNhbmNlbCBldmVudCBwcm9jZXNzaW5nIG9yIHByb3BhZ2F0aW9uXG4gICAgZG9jdW1lbnRFbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ3RvdWNoZW5kJywgdGhpcy5fb25Ub3VjaEVuZCwgdHJ1ZSk7XG4gICAgdGhpcy5jb250YWluZXJPdXRlci5lbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2tleWRvd24nLCB0aGlzLl9vbktleURvd24sIHRydWUpO1xuICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudC5hZGRFdmVudExpc3RlbmVyKCdtb3VzZWRvd24nLCB0aGlzLl9vbk1vdXNlRG93biwgdHJ1ZSk7XG4gICAgLy8gcGFzc2l2ZSBldmVudHMgLSBkb2Vzbid0IGNhbGwgYHByZXZlbnREZWZhdWx0YCBvciBgc3RvcFByb3BhZ2F0aW9uYFxuICAgIGRvY3VtZW50RWxlbWVudC5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMuX29uQ2xpY2ssIHtcbiAgICAgIHBhc3NpdmU6IHRydWVcbiAgICB9KTtcbiAgICBkb2N1bWVudEVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcigndG91Y2htb3ZlJywgdGhpcy5fb25Ub3VjaE1vdmUsIHtcbiAgICAgIHBhc3NpdmU6IHRydWVcbiAgICB9KTtcbiAgICB0aGlzLmRyb3Bkb3duLmVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignbW91c2VvdmVyJywgdGhpcy5fb25Nb3VzZU92ZXIsIHtcbiAgICAgIHBhc3NpdmU6IHRydWVcbiAgICB9KTtcbiAgICBpZiAodGhpcy5faXNTZWxlY3RPbmVFbGVtZW50KSB7XG4gICAgICB0aGlzLmNvbnRhaW5lck91dGVyLmVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignZm9jdXMnLCB0aGlzLl9vbkZvY3VzLCB7XG4gICAgICAgIHBhc3NpdmU6IHRydWVcbiAgICAgIH0pO1xuICAgICAgdGhpcy5jb250YWluZXJPdXRlci5lbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2JsdXInLCB0aGlzLl9vbkJsdXIsIHtcbiAgICAgICAgcGFzc2l2ZTogdHJ1ZVxuICAgICAgfSk7XG4gICAgfVxuICAgIHRoaXMuaW5wdXQuZWxlbWVudC5hZGRFdmVudExpc3RlbmVyKCdrZXl1cCcsIHRoaXMuX29uS2V5VXAsIHtcbiAgICAgIHBhc3NpdmU6IHRydWVcbiAgICB9KTtcbiAgICB0aGlzLmlucHV0LmVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignZm9jdXMnLCB0aGlzLl9vbkZvY3VzLCB7XG4gICAgICBwYXNzaXZlOiB0cnVlXG4gICAgfSk7XG4gICAgdGhpcy5pbnB1dC5lbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2JsdXInLCB0aGlzLl9vbkJsdXIsIHtcbiAgICAgIHBhc3NpdmU6IHRydWVcbiAgICB9KTtcbiAgICBpZiAodGhpcy5pbnB1dC5lbGVtZW50LmZvcm0pIHtcbiAgICAgIHRoaXMuaW5wdXQuZWxlbWVudC5mb3JtLmFkZEV2ZW50TGlzdGVuZXIoJ3Jlc2V0JywgdGhpcy5fb25Gb3JtUmVzZXQsIHtcbiAgICAgICAgcGFzc2l2ZTogdHJ1ZVxuICAgICAgfSk7XG4gICAgfVxuICAgIHRoaXMuaW5wdXQuYWRkRXZlbnRMaXN0ZW5lcnMoKTtcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX3JlbW92ZUV2ZW50TGlzdGVuZXJzID0gZnVuY3Rpb24gKCkge1xuICAgIHZhciBkb2N1bWVudEVsZW1lbnQgPSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQ7XG4gICAgZG9jdW1lbnRFbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ3RvdWNoZW5kJywgdGhpcy5fb25Ub3VjaEVuZCwgdHJ1ZSk7XG4gICAgdGhpcy5jb250YWluZXJPdXRlci5lbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2tleWRvd24nLCB0aGlzLl9vbktleURvd24sIHRydWUpO1xuICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudC5yZW1vdmVFdmVudExpc3RlbmVyKCdtb3VzZWRvd24nLCB0aGlzLl9vbk1vdXNlRG93biwgdHJ1ZSk7XG4gICAgZG9jdW1lbnRFbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcy5fb25DbGljayk7XG4gICAgZG9jdW1lbnRFbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ3RvdWNobW92ZScsIHRoaXMuX29uVG91Y2hNb3ZlKTtcbiAgICB0aGlzLmRyb3Bkb3duLmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcignbW91c2VvdmVyJywgdGhpcy5fb25Nb3VzZU92ZXIpO1xuICAgIGlmICh0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudC5yZW1vdmVFdmVudExpc3RlbmVyKCdmb2N1cycsIHRoaXMuX29uRm9jdXMpO1xuICAgICAgdGhpcy5jb250YWluZXJPdXRlci5lbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2JsdXInLCB0aGlzLl9vbkJsdXIpO1xuICAgIH1cbiAgICB0aGlzLmlucHV0LmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcigna2V5dXAnLCB0aGlzLl9vbktleVVwKTtcbiAgICB0aGlzLmlucHV0LmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcignZm9jdXMnLCB0aGlzLl9vbkZvY3VzKTtcbiAgICB0aGlzLmlucHV0LmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcignYmx1cicsIHRoaXMuX29uQmx1cik7XG4gICAgaWYgKHRoaXMuaW5wdXQuZWxlbWVudC5mb3JtKSB7XG4gICAgICB0aGlzLmlucHV0LmVsZW1lbnQuZm9ybS5yZW1vdmVFdmVudExpc3RlbmVyKCdyZXNldCcsIHRoaXMuX29uRm9ybVJlc2V0KTtcbiAgICB9XG4gICAgdGhpcy5pbnB1dC5yZW1vdmVFdmVudExpc3RlbmVycygpO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fb25LZXlEb3duID0gZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgdmFyIGtleUNvZGUgPSBldmVudC5rZXlDb2RlO1xuICAgIHZhciBhY3RpdmVJdGVtcyA9IHRoaXMuX3N0b3JlLmFjdGl2ZUl0ZW1zO1xuICAgIHZhciBoYXNGb2N1c2VkSW5wdXQgPSB0aGlzLmlucHV0LmlzRm9jdXNzZWQ7XG4gICAgdmFyIGhhc0FjdGl2ZURyb3Bkb3duID0gdGhpcy5kcm9wZG93bi5pc0FjdGl2ZTtcbiAgICB2YXIgaGFzSXRlbXMgPSB0aGlzLml0ZW1MaXN0Lmhhc0NoaWxkcmVuKCk7XG4gICAgdmFyIGtleVN0cmluZyA9IFN0cmluZy5mcm9tQ2hhckNvZGUoa2V5Q29kZSk7XG4gICAgLy8gZXNsaW50LWRpc2FibGUtbmV4dC1saW5lIG5vLWNvbnRyb2wtcmVnZXhcbiAgICB2YXIgd2FzUHJpbnRhYmxlQ2hhciA9IC9bXlxceDAwLVxceDFGXS8udGVzdChrZXlTdHJpbmcpO1xuICAgIHZhciBCQUNLX0tFWSA9IGNvbnN0YW50c18xLktFWV9DT0RFUy5CQUNLX0tFWSxcbiAgICAgIERFTEVURV9LRVkgPSBjb25zdGFudHNfMS5LRVlfQ09ERVMuREVMRVRFX0tFWSxcbiAgICAgIEVOVEVSX0tFWSA9IGNvbnN0YW50c18xLktFWV9DT0RFUy5FTlRFUl9LRVksXG4gICAgICBBX0tFWSA9IGNvbnN0YW50c18xLktFWV9DT0RFUy5BX0tFWSxcbiAgICAgIEVTQ19LRVkgPSBjb25zdGFudHNfMS5LRVlfQ09ERVMuRVNDX0tFWSxcbiAgICAgIFVQX0tFWSA9IGNvbnN0YW50c18xLktFWV9DT0RFUy5VUF9LRVksXG4gICAgICBET1dOX0tFWSA9IGNvbnN0YW50c18xLktFWV9DT0RFUy5ET1dOX0tFWSxcbiAgICAgIFBBR0VfVVBfS0VZID0gY29uc3RhbnRzXzEuS0VZX0NPREVTLlBBR0VfVVBfS0VZLFxuICAgICAgUEFHRV9ET1dOX0tFWSA9IGNvbnN0YW50c18xLktFWV9DT0RFUy5QQUdFX0RPV05fS0VZO1xuICAgIGlmICghdGhpcy5faXNUZXh0RWxlbWVudCAmJiAhaGFzQWN0aXZlRHJvcGRvd24gJiYgd2FzUHJpbnRhYmxlQ2hhcikge1xuICAgICAgdGhpcy5zaG93RHJvcGRvd24oKTtcbiAgICAgIGlmICghdGhpcy5pbnB1dC5pc0ZvY3Vzc2VkKSB7XG4gICAgICAgIC8qXG4gICAgICAgICAgV2UgdXBkYXRlIHRoZSBpbnB1dCB2YWx1ZSB3aXRoIHRoZSBwcmVzc2VkIGtleSBhc1xuICAgICAgICAgIHRoZSBpbnB1dCB3YXMgbm90IGZvY3Vzc2VkIGF0IHRoZSB0aW1lIG9mIGtleSBwcmVzc1xuICAgICAgICAgIHRoZXJlZm9yZSBkb2VzIG5vdCBoYXZlIHRoZSB2YWx1ZSBvZiB0aGUga2V5LlxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmlucHV0LnZhbHVlICs9IGV2ZW50LmtleS50b0xvd2VyQ2FzZSgpO1xuICAgICAgfVxuICAgIH1cbiAgICBzd2l0Y2ggKGtleUNvZGUpIHtcbiAgICAgIGNhc2UgQV9LRVk6XG4gICAgICAgIHJldHVybiB0aGlzLl9vblNlbGVjdEtleShldmVudCwgaGFzSXRlbXMpO1xuICAgICAgY2FzZSBFTlRFUl9LRVk6XG4gICAgICAgIHJldHVybiB0aGlzLl9vbkVudGVyS2V5KGV2ZW50LCBhY3RpdmVJdGVtcywgaGFzQWN0aXZlRHJvcGRvd24pO1xuICAgICAgY2FzZSBFU0NfS0VZOlxuICAgICAgICByZXR1cm4gdGhpcy5fb25Fc2NhcGVLZXkoaGFzQWN0aXZlRHJvcGRvd24pO1xuICAgICAgY2FzZSBVUF9LRVk6XG4gICAgICBjYXNlIFBBR0VfVVBfS0VZOlxuICAgICAgY2FzZSBET1dOX0tFWTpcbiAgICAgIGNhc2UgUEFHRV9ET1dOX0tFWTpcbiAgICAgICAgcmV0dXJuIHRoaXMuX29uRGlyZWN0aW9uS2V5KGV2ZW50LCBoYXNBY3RpdmVEcm9wZG93bik7XG4gICAgICBjYXNlIERFTEVURV9LRVk6XG4gICAgICBjYXNlIEJBQ0tfS0VZOlxuICAgICAgICByZXR1cm4gdGhpcy5fb25EZWxldGVLZXkoZXZlbnQsIGFjdGl2ZUl0ZW1zLCBoYXNGb2N1c2VkSW5wdXQpO1xuICAgICAgZGVmYXVsdDpcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vbktleVVwID0gZnVuY3Rpb24gKF9hKSB7XG4gICAgdmFyIHRhcmdldCA9IF9hLnRhcmdldCxcbiAgICAgIGtleUNvZGUgPSBfYS5rZXlDb2RlO1xuICAgIHZhciB2YWx1ZSA9IHRoaXMuaW5wdXQudmFsdWU7XG4gICAgdmFyIGFjdGl2ZUl0ZW1zID0gdGhpcy5fc3RvcmUuYWN0aXZlSXRlbXM7XG4gICAgdmFyIGNhbkFkZEl0ZW0gPSB0aGlzLl9jYW5BZGRJdGVtKGFjdGl2ZUl0ZW1zLCB2YWx1ZSk7XG4gICAgdmFyIGJhY2tLZXkgPSBjb25zdGFudHNfMS5LRVlfQ09ERVMuQkFDS19LRVksXG4gICAgICBkZWxldGVLZXkgPSBjb25zdGFudHNfMS5LRVlfQ09ERVMuREVMRVRFX0tFWTtcbiAgICAvLyBXZSBhcmUgdHlwaW5nIGludG8gYSB0ZXh0IGlucHV0IGFuZCBoYXZlIGEgdmFsdWUsIHdlIHdhbnQgdG8gc2hvdyBhIGRyb3Bkb3duXG4gICAgLy8gbm90aWNlLiBPdGhlcndpc2UgaGlkZSB0aGUgZHJvcGRvd25cbiAgICBpZiAodGhpcy5faXNUZXh0RWxlbWVudCkge1xuICAgICAgdmFyIGNhblNob3dEcm9wZG93bk5vdGljZSA9IGNhbkFkZEl0ZW0ubm90aWNlICYmIHZhbHVlO1xuICAgICAgaWYgKGNhblNob3dEcm9wZG93bk5vdGljZSkge1xuICAgICAgICB2YXIgZHJvcGRvd25JdGVtID0gdGhpcy5fZ2V0VGVtcGxhdGUoJ25vdGljZScsIGNhbkFkZEl0ZW0ubm90aWNlKTtcbiAgICAgICAgdGhpcy5kcm9wZG93bi5lbGVtZW50LmlubmVySFRNTCA9IGRyb3Bkb3duSXRlbS5vdXRlckhUTUw7XG4gICAgICAgIHRoaXMuc2hvd0Ryb3Bkb3duKHRydWUpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5oaWRlRHJvcGRvd24odHJ1ZSk7XG4gICAgICB9XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhciB3YXNSZW1vdmFsS2V5Q29kZSA9IGtleUNvZGUgPT09IGJhY2tLZXkgfHwga2V5Q29kZSA9PT0gZGVsZXRlS2V5O1xuICAgICAgdmFyIHVzZXJIYXNSZW1vdmVkVmFsdWUgPSB3YXNSZW1vdmFsS2V5Q29kZSAmJiB0YXJnZXQgJiYgIXRhcmdldC52YWx1ZTtcbiAgICAgIHZhciBjYW5SZWFjdGl2YXRlQ2hvaWNlcyA9ICF0aGlzLl9pc1RleHRFbGVtZW50ICYmIHRoaXMuX2lzU2VhcmNoaW5nO1xuICAgICAgdmFyIGNhblNlYXJjaCA9IHRoaXMuX2NhblNlYXJjaCAmJiBjYW5BZGRJdGVtLnJlc3BvbnNlO1xuICAgICAgaWYgKHVzZXJIYXNSZW1vdmVkVmFsdWUgJiYgY2FuUmVhY3RpdmF0ZUNob2ljZXMpIHtcbiAgICAgICAgdGhpcy5faXNTZWFyY2hpbmcgPSBmYWxzZTtcbiAgICAgICAgdGhpcy5fc3RvcmUuZGlzcGF0Y2goKDAsIGNob2ljZXNfMS5hY3RpdmF0ZUNob2ljZXMpKHRydWUpKTtcbiAgICAgIH0gZWxzZSBpZiAoY2FuU2VhcmNoKSB7XG4gICAgICAgIHRoaXMuX2hhbmRsZVNlYXJjaCh0aGlzLmlucHV0LnJhd1ZhbHVlKTtcbiAgICAgIH1cbiAgICB9XG4gICAgdGhpcy5fY2FuU2VhcmNoID0gdGhpcy5jb25maWcuc2VhcmNoRW5hYmxlZDtcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX29uU2VsZWN0S2V5ID0gZnVuY3Rpb24gKGV2ZW50LCBoYXNJdGVtcykge1xuICAgIHZhciBjdHJsS2V5ID0gZXZlbnQuY3RybEtleSxcbiAgICAgIG1ldGFLZXkgPSBldmVudC5tZXRhS2V5O1xuICAgIHZhciBoYXNDdHJsRG93bktleVByZXNzZWQgPSBjdHJsS2V5IHx8IG1ldGFLZXk7XG4gICAgLy8gSWYgQ1RSTCArIEEgb3IgQ01EICsgQSBoYXZlIGJlZW4gcHJlc3NlZCBhbmQgdGhlcmUgYXJlIGl0ZW1zIHRvIHNlbGVjdFxuICAgIGlmIChoYXNDdHJsRG93bktleVByZXNzZWQgJiYgaGFzSXRlbXMpIHtcbiAgICAgIHRoaXMuX2NhblNlYXJjaCA9IGZhbHNlO1xuICAgICAgdmFyIHNob3VsZEhpZ2h0bGlnaHRBbGwgPSB0aGlzLmNvbmZpZy5yZW1vdmVJdGVtcyAmJiAhdGhpcy5pbnB1dC52YWx1ZSAmJiB0aGlzLmlucHV0LmVsZW1lbnQgPT09IGRvY3VtZW50LmFjdGl2ZUVsZW1lbnQ7XG4gICAgICBpZiAoc2hvdWxkSGlnaHRsaWdodEFsbCkge1xuICAgICAgICB0aGlzLmhpZ2hsaWdodEFsbCgpO1xuICAgICAgfVxuICAgIH1cbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX29uRW50ZXJLZXkgPSBmdW5jdGlvbiAoZXZlbnQsIGFjdGl2ZUl0ZW1zLCBoYXNBY3RpdmVEcm9wZG93bikge1xuICAgIHZhciB0YXJnZXQgPSBldmVudC50YXJnZXQ7XG4gICAgdmFyIGVudGVyS2V5ID0gY29uc3RhbnRzXzEuS0VZX0NPREVTLkVOVEVSX0tFWTtcbiAgICB2YXIgdGFyZ2V0V2FzQnV0dG9uID0gdGFyZ2V0ICYmIHRhcmdldC5oYXNBdHRyaWJ1dGUoJ2RhdGEtYnV0dG9uJyk7XG4gICAgaWYgKHRoaXMuX2lzVGV4dEVsZW1lbnQgJiYgdGFyZ2V0ICYmIHRhcmdldC52YWx1ZSkge1xuICAgICAgdmFyIHZhbHVlID0gdGhpcy5pbnB1dC52YWx1ZTtcbiAgICAgIHZhciBjYW5BZGRJdGVtID0gdGhpcy5fY2FuQWRkSXRlbShhY3RpdmVJdGVtcywgdmFsdWUpO1xuICAgICAgaWYgKGNhbkFkZEl0ZW0ucmVzcG9uc2UpIHtcbiAgICAgICAgdGhpcy5oaWRlRHJvcGRvd24odHJ1ZSk7XG4gICAgICAgIHRoaXMuX2FkZEl0ZW0oe1xuICAgICAgICAgIHZhbHVlOiB2YWx1ZVxuICAgICAgICB9KTtcbiAgICAgICAgdGhpcy5fdHJpZ2dlckNoYW5nZSh2YWx1ZSk7XG4gICAgICAgIHRoaXMuY2xlYXJJbnB1dCgpO1xuICAgICAgfVxuICAgIH1cbiAgICBpZiAodGFyZ2V0V2FzQnV0dG9uKSB7XG4gICAgICB0aGlzLl9oYW5kbGVCdXR0b25BY3Rpb24oYWN0aXZlSXRlbXMsIHRhcmdldCk7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIH1cbiAgICBpZiAoaGFzQWN0aXZlRHJvcGRvd24pIHtcbiAgICAgIHZhciBoaWdobGlnaHRlZENob2ljZSA9IHRoaXMuZHJvcGRvd24uZ2V0Q2hpbGQoXCIuXCIuY29uY2F0KHRoaXMuY29uZmlnLmNsYXNzTmFtZXMuaGlnaGxpZ2h0ZWRTdGF0ZSkpO1xuICAgICAgaWYgKGhpZ2hsaWdodGVkQ2hvaWNlKSB7XG4gICAgICAgIC8vIGFkZCBlbnRlciBrZXlDb2RlIHZhbHVlXG4gICAgICAgIGlmIChhY3RpdmVJdGVtc1swXSkge1xuICAgICAgICAgIGFjdGl2ZUl0ZW1zWzBdLmtleUNvZGUgPSBlbnRlcktleTsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby1wYXJhbS1yZWFzc2lnblxuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5faGFuZGxlQ2hvaWNlQWN0aW9uKGFjdGl2ZUl0ZW1zLCBoaWdobGlnaHRlZENob2ljZSk7XG4gICAgICB9XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIH0gZWxzZSBpZiAodGhpcy5faXNTZWxlY3RPbmVFbGVtZW50KSB7XG4gICAgICB0aGlzLnNob3dEcm9wZG93bigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vbkVzY2FwZUtleSA9IGZ1bmN0aW9uIChoYXNBY3RpdmVEcm9wZG93bikge1xuICAgIGlmIChoYXNBY3RpdmVEcm9wZG93bikge1xuICAgICAgdGhpcy5oaWRlRHJvcGRvd24odHJ1ZSk7XG4gICAgICB0aGlzLmNvbnRhaW5lck91dGVyLmZvY3VzKCk7XG4gICAgfVxuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fb25EaXJlY3Rpb25LZXkgPSBmdW5jdGlvbiAoZXZlbnQsIGhhc0FjdGl2ZURyb3Bkb3duKSB7XG4gICAgdmFyIGtleUNvZGUgPSBldmVudC5rZXlDb2RlLFxuICAgICAgbWV0YUtleSA9IGV2ZW50Lm1ldGFLZXk7XG4gICAgdmFyIGRvd25LZXkgPSBjb25zdGFudHNfMS5LRVlfQ09ERVMuRE9XTl9LRVksXG4gICAgICBwYWdlVXBLZXkgPSBjb25zdGFudHNfMS5LRVlfQ09ERVMuUEFHRV9VUF9LRVksXG4gICAgICBwYWdlRG93bktleSA9IGNvbnN0YW50c18xLktFWV9DT0RFUy5QQUdFX0RPV05fS0VZO1xuICAgIC8vIElmIHVwIG9yIGRvd24ga2V5IGlzIHByZXNzZWQsIHRyYXZlcnNlIHRocm91Z2ggb3B0aW9uc1xuICAgIGlmIChoYXNBY3RpdmVEcm9wZG93biB8fCB0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIHRoaXMuc2hvd0Ryb3Bkb3duKCk7XG4gICAgICB0aGlzLl9jYW5TZWFyY2ggPSBmYWxzZTtcbiAgICAgIHZhciBkaXJlY3Rpb25JbnQgPSBrZXlDb2RlID09PSBkb3duS2V5IHx8IGtleUNvZGUgPT09IHBhZ2VEb3duS2V5ID8gMSA6IC0xO1xuICAgICAgdmFyIHNraXBLZXkgPSBtZXRhS2V5IHx8IGtleUNvZGUgPT09IHBhZ2VEb3duS2V5IHx8IGtleUNvZGUgPT09IHBhZ2VVcEtleTtcbiAgICAgIHZhciBzZWxlY3RhYmxlQ2hvaWNlSWRlbnRpZmllciA9ICdbZGF0YS1jaG9pY2Utc2VsZWN0YWJsZV0nO1xuICAgICAgdmFyIG5leHRFbCA9IHZvaWQgMDtcbiAgICAgIGlmIChza2lwS2V5KSB7XG4gICAgICAgIGlmIChkaXJlY3Rpb25JbnQgPiAwKSB7XG4gICAgICAgICAgbmV4dEVsID0gdGhpcy5kcm9wZG93bi5lbGVtZW50LnF1ZXJ5U2VsZWN0b3IoXCJcIi5jb25jYXQoc2VsZWN0YWJsZUNob2ljZUlkZW50aWZpZXIsIFwiOmxhc3Qtb2YtdHlwZVwiKSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgbmV4dEVsID0gdGhpcy5kcm9wZG93bi5lbGVtZW50LnF1ZXJ5U2VsZWN0b3Ioc2VsZWN0YWJsZUNob2ljZUlkZW50aWZpZXIpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgY3VycmVudEVsID0gdGhpcy5kcm9wZG93bi5lbGVtZW50LnF1ZXJ5U2VsZWN0b3IoXCIuXCIuY29uY2F0KHRoaXMuY29uZmlnLmNsYXNzTmFtZXMuaGlnaGxpZ2h0ZWRTdGF0ZSkpO1xuICAgICAgICBpZiAoY3VycmVudEVsKSB7XG4gICAgICAgICAgbmV4dEVsID0gKDAsIHV0aWxzXzEuZ2V0QWRqYWNlbnRFbCkoY3VycmVudEVsLCBzZWxlY3RhYmxlQ2hvaWNlSWRlbnRpZmllciwgZGlyZWN0aW9uSW50KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBuZXh0RWwgPSB0aGlzLmRyb3Bkb3duLmVsZW1lbnQucXVlcnlTZWxlY3RvcihzZWxlY3RhYmxlQ2hvaWNlSWRlbnRpZmllcik7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGlmIChuZXh0RWwpIHtcbiAgICAgICAgLy8gV2UgcHJldmVudCBkZWZhdWx0IHRvIHN0b3AgdGhlIGN1cnNvciBtb3ZpbmdcbiAgICAgICAgLy8gd2hlbiBwcmVzc2luZyB0aGUgYXJyb3dcbiAgICAgICAgaWYgKCEoMCwgdXRpbHNfMS5pc1Njcm9sbGVkSW50b1ZpZXcpKG5leHRFbCwgdGhpcy5jaG9pY2VMaXN0LmVsZW1lbnQsIGRpcmVjdGlvbkludCkpIHtcbiAgICAgICAgICB0aGlzLmNob2ljZUxpc3Quc2Nyb2xsVG9DaGlsZEVsZW1lbnQobmV4dEVsLCBkaXJlY3Rpb25JbnQpO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuX2hpZ2hsaWdodENob2ljZShuZXh0RWwpO1xuICAgICAgfVxuICAgICAgLy8gUHJldmVudCBkZWZhdWx0IHRvIG1haW50YWluIGN1cnNvciBwb3NpdGlvbiB3aGlsc3RcbiAgICAgIC8vIHRyYXZlcnNpbmcgZHJvcGRvd24gb3B0aW9uc1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vbkRlbGV0ZUtleSA9IGZ1bmN0aW9uIChldmVudCwgYWN0aXZlSXRlbXMsIGhhc0ZvY3VzZWRJbnB1dCkge1xuICAgIHZhciB0YXJnZXQgPSBldmVudC50YXJnZXQ7XG4gICAgLy8gSWYgYmFja3NwYWNlIG9yIGRlbGV0ZSBrZXkgaXMgcHJlc3NlZCBhbmQgdGhlIGlucHV0IGhhcyBubyB2YWx1ZVxuICAgIGlmICghdGhpcy5faXNTZWxlY3RPbmVFbGVtZW50ICYmICF0YXJnZXQudmFsdWUgJiYgaGFzRm9jdXNlZElucHV0KSB7XG4gICAgICB0aGlzLl9oYW5kbGVCYWNrc3BhY2UoYWN0aXZlSXRlbXMpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vblRvdWNoTW92ZSA9IGZ1bmN0aW9uICgpIHtcbiAgICBpZiAodGhpcy5fd2FzVGFwKSB7XG4gICAgICB0aGlzLl93YXNUYXAgPSBmYWxzZTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vblRvdWNoRW5kID0gZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgdmFyIHRhcmdldCA9IChldmVudCB8fCBldmVudC50b3VjaGVzWzBdKS50YXJnZXQ7XG4gICAgdmFyIHRvdWNoV2FzV2l0aGluQ29udGFpbmVyID0gdGhpcy5fd2FzVGFwICYmIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudC5jb250YWlucyh0YXJnZXQpO1xuICAgIGlmICh0b3VjaFdhc1dpdGhpbkNvbnRhaW5lcikge1xuICAgICAgdmFyIGNvbnRhaW5lcldhc0V4YWN0VGFyZ2V0ID0gdGFyZ2V0ID09PSB0aGlzLmNvbnRhaW5lck91dGVyLmVsZW1lbnQgfHwgdGFyZ2V0ID09PSB0aGlzLmNvbnRhaW5lcklubmVyLmVsZW1lbnQ7XG4gICAgICBpZiAoY29udGFpbmVyV2FzRXhhY3RUYXJnZXQpIHtcbiAgICAgICAgaWYgKHRoaXMuX2lzVGV4dEVsZW1lbnQpIHtcbiAgICAgICAgICB0aGlzLmlucHV0LmZvY3VzKCk7XG4gICAgICAgIH0gZWxzZSBpZiAodGhpcy5faXNTZWxlY3RNdWx0aXBsZUVsZW1lbnQpIHtcbiAgICAgICAgICB0aGlzLnNob3dEcm9wZG93bigpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICAvLyBQcmV2ZW50cyBmb2N1cyBldmVudCBmaXJpbmdcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgIH1cbiAgICB0aGlzLl93YXNUYXAgPSB0cnVlO1xuICB9O1xuICAvKipcbiAgICogSGFuZGxlcyBtb3VzZWRvd24gZXZlbnQgaW4gY2FwdHVyZSBtb2RlIGZvciBjb250YWluZXRPdXRlci5lbGVtZW50XG4gICAqL1xuICBDaG9pY2VzLnByb3RvdHlwZS5fb25Nb3VzZURvd24gPSBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICB2YXIgdGFyZ2V0ID0gZXZlbnQudGFyZ2V0O1xuICAgIGlmICghKHRhcmdldCBpbnN0YW5jZW9mIEhUTUxFbGVtZW50KSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICAvLyBJZiB3ZSBoYXZlIG91ciBtb3VzZSBkb3duIG9uIHRoZSBzY3JvbGxiYXIgYW5kIGFyZSBvbiBJRTExLi4uXG4gICAgaWYgKElTX0lFMTEgJiYgdGhpcy5jaG9pY2VMaXN0LmVsZW1lbnQuY29udGFpbnModGFyZ2V0KSkge1xuICAgICAgLy8gY2hlY2sgaWYgY2xpY2sgd2FzIG9uIGEgc2Nyb2xsYmFyIGFyZWFcbiAgICAgIHZhciBmaXJzdENob2ljZSA9IHRoaXMuY2hvaWNlTGlzdC5lbGVtZW50LmZpcnN0RWxlbWVudENoaWxkO1xuICAgICAgdmFyIGlzT25TY3JvbGxiYXIgPSB0aGlzLl9kaXJlY3Rpb24gPT09ICdsdHInID8gZXZlbnQub2Zmc2V0WCA+PSBmaXJzdENob2ljZS5vZmZzZXRXaWR0aCA6IGV2ZW50Lm9mZnNldFggPCBmaXJzdENob2ljZS5vZmZzZXRMZWZ0O1xuICAgICAgdGhpcy5faXNTY3JvbGxpbmdPbkllID0gaXNPblNjcm9sbGJhcjtcbiAgICB9XG4gICAgaWYgKHRhcmdldCA9PT0gdGhpcy5pbnB1dC5lbGVtZW50KSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHZhciBpdGVtID0gdGFyZ2V0LmNsb3Nlc3QoJ1tkYXRhLWJ1dHRvbl0sW2RhdGEtaXRlbV0sW2RhdGEtY2hvaWNlXScpO1xuICAgIGlmIChpdGVtIGluc3RhbmNlb2YgSFRNTEVsZW1lbnQpIHtcbiAgICAgIHZhciBoYXNTaGlmdEtleSA9IGV2ZW50LnNoaWZ0S2V5O1xuICAgICAgdmFyIGFjdGl2ZUl0ZW1zID0gdGhpcy5fc3RvcmUuYWN0aXZlSXRlbXM7XG4gICAgICB2YXIgZGF0YXNldCA9IGl0ZW0uZGF0YXNldDtcbiAgICAgIGlmICgnYnV0dG9uJyBpbiBkYXRhc2V0KSB7XG4gICAgICAgIHRoaXMuX2hhbmRsZUJ1dHRvbkFjdGlvbihhY3RpdmVJdGVtcywgaXRlbSk7XG4gICAgICB9IGVsc2UgaWYgKCdpdGVtJyBpbiBkYXRhc2V0KSB7XG4gICAgICAgIHRoaXMuX2hhbmRsZUl0ZW1BY3Rpb24oYWN0aXZlSXRlbXMsIGl0ZW0sIGhhc1NoaWZ0S2V5KTtcbiAgICAgIH0gZWxzZSBpZiAoJ2Nob2ljZScgaW4gZGF0YXNldCkge1xuICAgICAgICB0aGlzLl9oYW5kbGVDaG9pY2VBY3Rpb24oYWN0aXZlSXRlbXMsIGl0ZW0pO1xuICAgICAgfVxuICAgIH1cbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICB9O1xuICAvKipcbiAgICogSGFuZGxlcyBtb3VzZW92ZXIgZXZlbnQgb3ZlciB0aGlzLmRyb3Bkb3duXG4gICAqIEBwYXJhbSB7TW91c2VFdmVudH0gZXZlbnRcbiAgICovXG4gIENob2ljZXMucHJvdG90eXBlLl9vbk1vdXNlT3ZlciA9IGZ1bmN0aW9uIChfYSkge1xuICAgIHZhciB0YXJnZXQgPSBfYS50YXJnZXQ7XG4gICAgaWYgKHRhcmdldCBpbnN0YW5jZW9mIEhUTUxFbGVtZW50ICYmICdjaG9pY2UnIGluIHRhcmdldC5kYXRhc2V0KSB7XG4gICAgICB0aGlzLl9oaWdobGlnaHRDaG9pY2UodGFyZ2V0KTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vbkNsaWNrID0gZnVuY3Rpb24gKF9hKSB7XG4gICAgdmFyIHRhcmdldCA9IF9hLnRhcmdldDtcbiAgICB2YXIgY2xpY2tXYXNXaXRoaW5Db250YWluZXIgPSB0aGlzLmNvbnRhaW5lck91dGVyLmVsZW1lbnQuY29udGFpbnModGFyZ2V0KTtcbiAgICBpZiAoY2xpY2tXYXNXaXRoaW5Db250YWluZXIpIHtcbiAgICAgIGlmICghdGhpcy5kcm9wZG93bi5pc0FjdGl2ZSAmJiAhdGhpcy5jb250YWluZXJPdXRlci5pc0Rpc2FibGVkKSB7XG4gICAgICAgIGlmICh0aGlzLl9pc1RleHRFbGVtZW50KSB7XG4gICAgICAgICAgaWYgKGRvY3VtZW50LmFjdGl2ZUVsZW1lbnQgIT09IHRoaXMuaW5wdXQuZWxlbWVudCkge1xuICAgICAgICAgICAgdGhpcy5pbnB1dC5mb2N1cygpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICB0aGlzLnNob3dEcm9wZG93bigpO1xuICAgICAgICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuZm9jdXMoKTtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIGlmICh0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQgJiYgdGFyZ2V0ICE9PSB0aGlzLmlucHV0LmVsZW1lbnQgJiYgIXRoaXMuZHJvcGRvd24uZWxlbWVudC5jb250YWlucyh0YXJnZXQpKSB7XG4gICAgICAgIHRoaXMuaGlkZURyb3Bkb3duKCk7XG4gICAgICB9XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhciBoYXNIaWdobGlnaHRlZEl0ZW1zID0gdGhpcy5fc3RvcmUuaGlnaGxpZ2h0ZWRBY3RpdmVJdGVtcy5sZW5ndGggPiAwO1xuICAgICAgaWYgKGhhc0hpZ2hsaWdodGVkSXRlbXMpIHtcbiAgICAgICAgdGhpcy51bmhpZ2hsaWdodEFsbCgpO1xuICAgICAgfVxuICAgICAgdGhpcy5jb250YWluZXJPdXRlci5yZW1vdmVGb2N1c1N0YXRlKCk7XG4gICAgICB0aGlzLmhpZGVEcm9wZG93bih0cnVlKTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vbkZvY3VzID0gZnVuY3Rpb24gKF9hKSB7XG4gICAgdmFyIF9iO1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgdmFyIHRhcmdldCA9IF9hLnRhcmdldDtcbiAgICB2YXIgZm9jdXNXYXNXaXRoaW5Db250YWluZXIgPSB0YXJnZXQgJiYgdGhpcy5jb250YWluZXJPdXRlci5lbGVtZW50LmNvbnRhaW5zKHRhcmdldCk7XG4gICAgaWYgKCFmb2N1c1dhc1dpdGhpbkNvbnRhaW5lcikge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB2YXIgZm9jdXNBY3Rpb25zID0gKF9iID0ge30sIF9iW2NvbnN0YW50c18xLlRFWFRfVFlQRV0gPSBmdW5jdGlvbiAoKSB7XG4gICAgICBpZiAodGFyZ2V0ID09PSBfdGhpcy5pbnB1dC5lbGVtZW50KSB7XG4gICAgICAgIF90aGlzLmNvbnRhaW5lck91dGVyLmFkZEZvY3VzU3RhdGUoKTtcbiAgICAgIH1cbiAgICB9LCBfYltjb25zdGFudHNfMS5TRUxFQ1RfT05FX1RZUEVdID0gZnVuY3Rpb24gKCkge1xuICAgICAgX3RoaXMuY29udGFpbmVyT3V0ZXIuYWRkRm9jdXNTdGF0ZSgpO1xuICAgICAgaWYgKHRhcmdldCA9PT0gX3RoaXMuaW5wdXQuZWxlbWVudCkge1xuICAgICAgICBfdGhpcy5zaG93RHJvcGRvd24odHJ1ZSk7XG4gICAgICB9XG4gICAgfSwgX2JbY29uc3RhbnRzXzEuU0VMRUNUX01VTFRJUExFX1RZUEVdID0gZnVuY3Rpb24gKCkge1xuICAgICAgaWYgKHRhcmdldCA9PT0gX3RoaXMuaW5wdXQuZWxlbWVudCkge1xuICAgICAgICBfdGhpcy5zaG93RHJvcGRvd24odHJ1ZSk7XG4gICAgICAgIC8vIElmIGVsZW1lbnQgaXMgYSBzZWxlY3QgYm94LCB0aGUgZm9jdXNlZCBlbGVtZW50IGlzIHRoZSBjb250YWluZXIgYW5kIHRoZSBkcm9wZG93blxuICAgICAgICAvLyBpc24ndCBhbHJlYWR5IG9wZW4sIGZvY3VzIGFuZCBzaG93IGRyb3Bkb3duXG4gICAgICAgIF90aGlzLmNvbnRhaW5lck91dGVyLmFkZEZvY3VzU3RhdGUoKTtcbiAgICAgIH1cbiAgICB9LCBfYik7XG4gICAgZm9jdXNBY3Rpb25zW3RoaXMucGFzc2VkRWxlbWVudC5lbGVtZW50LnR5cGVdKCk7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9vbkJsdXIgPSBmdW5jdGlvbiAoX2EpIHtcbiAgICB2YXIgX2I7XG4gICAgdmFyIF90aGlzID0gdGhpcztcbiAgICB2YXIgdGFyZ2V0ID0gX2EudGFyZ2V0O1xuICAgIHZhciBibHVyV2FzV2l0aGluQ29udGFpbmVyID0gdGFyZ2V0ICYmIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudC5jb250YWlucyh0YXJnZXQpO1xuICAgIGlmIChibHVyV2FzV2l0aGluQ29udGFpbmVyICYmICF0aGlzLl9pc1Njcm9sbGluZ09uSWUpIHtcbiAgICAgIHZhciBhY3RpdmVJdGVtcyA9IHRoaXMuX3N0b3JlLmFjdGl2ZUl0ZW1zO1xuICAgICAgdmFyIGhhc0hpZ2hsaWdodGVkSXRlbXNfMSA9IGFjdGl2ZUl0ZW1zLnNvbWUoZnVuY3Rpb24gKGl0ZW0pIHtcbiAgICAgICAgcmV0dXJuIGl0ZW0uaGlnaGxpZ2h0ZWQ7XG4gICAgICB9KTtcbiAgICAgIHZhciBibHVyQWN0aW9ucyA9IChfYiA9IHt9LCBfYltjb25zdGFudHNfMS5URVhUX1RZUEVdID0gZnVuY3Rpb24gKCkge1xuICAgICAgICBpZiAodGFyZ2V0ID09PSBfdGhpcy5pbnB1dC5lbGVtZW50KSB7XG4gICAgICAgICAgX3RoaXMuY29udGFpbmVyT3V0ZXIucmVtb3ZlRm9jdXNTdGF0ZSgpO1xuICAgICAgICAgIGlmIChoYXNIaWdobGlnaHRlZEl0ZW1zXzEpIHtcbiAgICAgICAgICAgIF90aGlzLnVuaGlnaGxpZ2h0QWxsKCk7XG4gICAgICAgICAgfVxuICAgICAgICAgIF90aGlzLmhpZGVEcm9wZG93bih0cnVlKTtcbiAgICAgICAgfVxuICAgICAgfSwgX2JbY29uc3RhbnRzXzEuU0VMRUNUX09ORV9UWVBFXSA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgX3RoaXMuY29udGFpbmVyT3V0ZXIucmVtb3ZlRm9jdXNTdGF0ZSgpO1xuICAgICAgICBpZiAodGFyZ2V0ID09PSBfdGhpcy5pbnB1dC5lbGVtZW50IHx8IHRhcmdldCA9PT0gX3RoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudCAmJiAhX3RoaXMuX2NhblNlYXJjaCkge1xuICAgICAgICAgIF90aGlzLmhpZGVEcm9wZG93bih0cnVlKTtcbiAgICAgICAgfVxuICAgICAgfSwgX2JbY29uc3RhbnRzXzEuU0VMRUNUX01VTFRJUExFX1RZUEVdID0gZnVuY3Rpb24gKCkge1xuICAgICAgICBpZiAodGFyZ2V0ID09PSBfdGhpcy5pbnB1dC5lbGVtZW50KSB7XG4gICAgICAgICAgX3RoaXMuY29udGFpbmVyT3V0ZXIucmVtb3ZlRm9jdXNTdGF0ZSgpO1xuICAgICAgICAgIF90aGlzLmhpZGVEcm9wZG93bih0cnVlKTtcbiAgICAgICAgICBpZiAoaGFzSGlnaGxpZ2h0ZWRJdGVtc18xKSB7XG4gICAgICAgICAgICBfdGhpcy51bmhpZ2hsaWdodEFsbCgpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfSwgX2IpO1xuICAgICAgYmx1ckFjdGlvbnNbdGhpcy5wYXNzZWRFbGVtZW50LmVsZW1lbnQudHlwZV0oKTtcbiAgICB9IGVsc2Uge1xuICAgICAgLy8gT24gSUUxMSwgY2xpY2tpbmcgdGhlIHNjb2xsYmFyIGJsdXJzIG91ciBpbnB1dCBhbmQgdGh1c1xuICAgICAgLy8gY2xvc2VzIHRoZSBkcm9wZG93bi4gVG8gc3RvcCB0aGlzLCB3ZSByZWZvY3VzIG91ciBpbnB1dFxuICAgICAgLy8gaWYgd2Uga25vdyB3ZSBhcmUgb24gSUUgKmFuZCogYXJlIHNjcm9sbGluZy5cbiAgICAgIHRoaXMuX2lzU2Nyb2xsaW5nT25JZSA9IGZhbHNlO1xuICAgICAgdGhpcy5pbnB1dC5lbGVtZW50LmZvY3VzKCk7XG4gICAgfVxuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fb25Gb3JtUmVzZXQgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5fc3RvcmUuZGlzcGF0Y2goKDAsIG1pc2NfMS5yZXNldFRvKSh0aGlzLl9pbml0aWFsU3RhdGUpKTtcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2hpZ2hsaWdodENob2ljZSA9IGZ1bmN0aW9uIChlbCkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgaWYgKGVsID09PSB2b2lkIDApIHtcbiAgICAgIGVsID0gbnVsbDtcbiAgICB9XG4gICAgdmFyIGNob2ljZXMgPSBBcnJheS5mcm9tKHRoaXMuZHJvcGRvd24uZWxlbWVudC5xdWVyeVNlbGVjdG9yQWxsKCdbZGF0YS1jaG9pY2Utc2VsZWN0YWJsZV0nKSk7XG4gICAgaWYgKCFjaG9pY2VzLmxlbmd0aCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB2YXIgcGFzc2VkRWwgPSBlbDtcbiAgICB2YXIgaGlnaGxpZ2h0ZWRDaG9pY2VzID0gQXJyYXkuZnJvbSh0aGlzLmRyb3Bkb3duLmVsZW1lbnQucXVlcnlTZWxlY3RvckFsbChcIi5cIi5jb25jYXQodGhpcy5jb25maWcuY2xhc3NOYW1lcy5oaWdobGlnaHRlZFN0YXRlKSkpO1xuICAgIC8vIFJlbW92ZSBhbnkgaGlnaGxpZ2h0ZWQgY2hvaWNlc1xuICAgIGhpZ2hsaWdodGVkQ2hvaWNlcy5mb3JFYWNoKGZ1bmN0aW9uIChjaG9pY2UpIHtcbiAgICAgIGNob2ljZS5jbGFzc0xpc3QucmVtb3ZlKF90aGlzLmNvbmZpZy5jbGFzc05hbWVzLmhpZ2hsaWdodGVkU3RhdGUpO1xuICAgICAgY2hvaWNlLnNldEF0dHJpYnV0ZSgnYXJpYS1zZWxlY3RlZCcsICdmYWxzZScpO1xuICAgIH0pO1xuICAgIGlmIChwYXNzZWRFbCkge1xuICAgICAgdGhpcy5faGlnaGxpZ2h0UG9zaXRpb24gPSBjaG9pY2VzLmluZGV4T2YocGFzc2VkRWwpO1xuICAgIH0gZWxzZSB7XG4gICAgICAvLyBIaWdobGlnaHQgY2hvaWNlIGJhc2VkIG9uIGxhc3Qga25vd24gaGlnaGxpZ2h0IGxvY2F0aW9uXG4gICAgICBpZiAoY2hvaWNlcy5sZW5ndGggPiB0aGlzLl9oaWdobGlnaHRQb3NpdGlvbikge1xuICAgICAgICAvLyBJZiB3ZSBoYXZlIGFuIG9wdGlvbiB0byBoaWdobGlnaHRcbiAgICAgICAgcGFzc2VkRWwgPSBjaG9pY2VzW3RoaXMuX2hpZ2hsaWdodFBvc2l0aW9uXTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIC8vIE90aGVyd2lzZSBoaWdobGlnaHQgdGhlIG9wdGlvbiBiZWZvcmVcbiAgICAgICAgcGFzc2VkRWwgPSBjaG9pY2VzW2Nob2ljZXMubGVuZ3RoIC0gMV07XG4gICAgICB9XG4gICAgICBpZiAoIXBhc3NlZEVsKSB7XG4gICAgICAgIHBhc3NlZEVsID0gY2hvaWNlc1swXTtcbiAgICAgIH1cbiAgICB9XG4gICAgcGFzc2VkRWwuY2xhc3NMaXN0LmFkZCh0aGlzLmNvbmZpZy5jbGFzc05hbWVzLmhpZ2hsaWdodGVkU3RhdGUpO1xuICAgIHBhc3NlZEVsLnNldEF0dHJpYnV0ZSgnYXJpYS1zZWxlY3RlZCcsICd0cnVlJyk7XG4gICAgdGhpcy5wYXNzZWRFbGVtZW50LnRyaWdnZXJFdmVudChjb25zdGFudHNfMS5FVkVOVFMuaGlnaGxpZ2h0Q2hvaWNlLCB7XG4gICAgICBlbDogcGFzc2VkRWxcbiAgICB9KTtcbiAgICBpZiAodGhpcy5kcm9wZG93bi5pc0FjdGl2ZSkge1xuICAgICAgLy8gSUUxMSBpZ25vcmVzIGFyaWEtbGFiZWwgYW5kIGJsb2NrcyB2aXJ0dWFsIGtleWJvYXJkXG4gICAgICAvLyBpZiBhcmlhLWFjdGl2ZWRlc2NlbmRhbnQgaXMgc2V0IHdpdGhvdXQgYSBkcm9wZG93blxuICAgICAgdGhpcy5pbnB1dC5zZXRBY3RpdmVEZXNjZW5kYW50KHBhc3NlZEVsLmlkKTtcbiAgICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuc2V0QWN0aXZlRGVzY2VuZGFudChwYXNzZWRFbC5pZCk7XG4gICAgfVxuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fYWRkSXRlbSA9IGZ1bmN0aW9uIChfYSkge1xuICAgIHZhciB2YWx1ZSA9IF9hLnZhbHVlLFxuICAgICAgX2IgPSBfYS5sYWJlbCxcbiAgICAgIGxhYmVsID0gX2IgPT09IHZvaWQgMCA/IG51bGwgOiBfYixcbiAgICAgIF9jID0gX2EuY2hvaWNlSWQsXG4gICAgICBjaG9pY2VJZCA9IF9jID09PSB2b2lkIDAgPyAtMSA6IF9jLFxuICAgICAgX2QgPSBfYS5ncm91cElkLFxuICAgICAgZ3JvdXBJZCA9IF9kID09PSB2b2lkIDAgPyAtMSA6IF9kLFxuICAgICAgX2UgPSBfYS5jdXN0b21Qcm9wZXJ0aWVzLFxuICAgICAgY3VzdG9tUHJvcGVydGllcyA9IF9lID09PSB2b2lkIDAgPyB7fSA6IF9lLFxuICAgICAgX2YgPSBfYS5wbGFjZWhvbGRlcixcbiAgICAgIHBsYWNlaG9sZGVyID0gX2YgPT09IHZvaWQgMCA/IGZhbHNlIDogX2YsXG4gICAgICBfZyA9IF9hLmtleUNvZGUsXG4gICAgICBrZXlDb2RlID0gX2cgPT09IHZvaWQgMCA/IC0xIDogX2c7XG4gICAgdmFyIHBhc3NlZFZhbHVlID0gdHlwZW9mIHZhbHVlID09PSAnc3RyaW5nJyA/IHZhbHVlLnRyaW0oKSA6IHZhbHVlO1xuICAgIHZhciBpdGVtcyA9IHRoaXMuX3N0b3JlLml0ZW1zO1xuICAgIHZhciBwYXNzZWRMYWJlbCA9IGxhYmVsIHx8IHBhc3NlZFZhbHVlO1xuICAgIHZhciBwYXNzZWRPcHRpb25JZCA9IGNob2ljZUlkIHx8IC0xO1xuICAgIHZhciBncm91cCA9IGdyb3VwSWQgPj0gMCA/IHRoaXMuX3N0b3JlLmdldEdyb3VwQnlJZChncm91cElkKSA6IG51bGw7XG4gICAgdmFyIGlkID0gaXRlbXMgPyBpdGVtcy5sZW5ndGggKyAxIDogMTtcbiAgICAvLyBJZiBhIHByZXBlbmRlZCB2YWx1ZSBoYXMgYmVlbiBwYXNzZWQsIHByZXBlbmQgaXRcbiAgICBpZiAodGhpcy5jb25maWcucHJlcGVuZFZhbHVlKSB7XG4gICAgICBwYXNzZWRWYWx1ZSA9IHRoaXMuY29uZmlnLnByZXBlbmRWYWx1ZSArIHBhc3NlZFZhbHVlLnRvU3RyaW5nKCk7XG4gICAgfVxuICAgIC8vIElmIGFuIGFwcGVuZGVkIHZhbHVlIGhhcyBiZWVuIHBhc3NlZCwgYXBwZW5kIGl0XG4gICAgaWYgKHRoaXMuY29uZmlnLmFwcGVuZFZhbHVlKSB7XG4gICAgICBwYXNzZWRWYWx1ZSArPSB0aGlzLmNvbmZpZy5hcHBlbmRWYWx1ZS50b1N0cmluZygpO1xuICAgIH1cbiAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaCgoMCwgaXRlbXNfMS5hZGRJdGVtKSh7XG4gICAgICB2YWx1ZTogcGFzc2VkVmFsdWUsXG4gICAgICBsYWJlbDogcGFzc2VkTGFiZWwsXG4gICAgICBpZDogaWQsXG4gICAgICBjaG9pY2VJZDogcGFzc2VkT3B0aW9uSWQsXG4gICAgICBncm91cElkOiBncm91cElkLFxuICAgICAgY3VzdG9tUHJvcGVydGllczogY3VzdG9tUHJvcGVydGllcyxcbiAgICAgIHBsYWNlaG9sZGVyOiBwbGFjZWhvbGRlcixcbiAgICAgIGtleUNvZGU6IGtleUNvZGVcbiAgICB9KSk7XG4gICAgaWYgKHRoaXMuX2lzU2VsZWN0T25lRWxlbWVudCkge1xuICAgICAgdGhpcy5yZW1vdmVBY3RpdmVJdGVtcyhpZCk7XG4gICAgfVxuICAgIC8vIFRyaWdnZXIgY2hhbmdlIGV2ZW50XG4gICAgdGhpcy5wYXNzZWRFbGVtZW50LnRyaWdnZXJFdmVudChjb25zdGFudHNfMS5FVkVOVFMuYWRkSXRlbSwge1xuICAgICAgaWQ6IGlkLFxuICAgICAgdmFsdWU6IHBhc3NlZFZhbHVlLFxuICAgICAgbGFiZWw6IHBhc3NlZExhYmVsLFxuICAgICAgY3VzdG9tUHJvcGVydGllczogY3VzdG9tUHJvcGVydGllcyxcbiAgICAgIGdyb3VwVmFsdWU6IGdyb3VwICYmIGdyb3VwLnZhbHVlID8gZ3JvdXAudmFsdWUgOiBudWxsLFxuICAgICAga2V5Q29kZToga2V5Q29kZVxuICAgIH0pO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fcmVtb3ZlSXRlbSA9IGZ1bmN0aW9uIChpdGVtKSB7XG4gICAgdmFyIGlkID0gaXRlbS5pZCxcbiAgICAgIHZhbHVlID0gaXRlbS52YWx1ZSxcbiAgICAgIGxhYmVsID0gaXRlbS5sYWJlbCxcbiAgICAgIGN1c3RvbVByb3BlcnRpZXMgPSBpdGVtLmN1c3RvbVByb3BlcnRpZXMsXG4gICAgICBjaG9pY2VJZCA9IGl0ZW0uY2hvaWNlSWQsXG4gICAgICBncm91cElkID0gaXRlbS5ncm91cElkO1xuICAgIHZhciBncm91cCA9IGdyb3VwSWQgJiYgZ3JvdXBJZCA+PSAwID8gdGhpcy5fc3RvcmUuZ2V0R3JvdXBCeUlkKGdyb3VwSWQpIDogbnVsbDtcbiAgICBpZiAoIWlkIHx8ICFjaG9pY2VJZCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaCgoMCwgaXRlbXNfMS5yZW1vdmVJdGVtKShpZCwgY2hvaWNlSWQpKTtcbiAgICB0aGlzLnBhc3NlZEVsZW1lbnQudHJpZ2dlckV2ZW50KGNvbnN0YW50c18xLkVWRU5UUy5yZW1vdmVJdGVtLCB7XG4gICAgICBpZDogaWQsXG4gICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICBsYWJlbDogbGFiZWwsXG4gICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjdXN0b21Qcm9wZXJ0aWVzLFxuICAgICAgZ3JvdXBWYWx1ZTogZ3JvdXAgJiYgZ3JvdXAudmFsdWUgPyBncm91cC52YWx1ZSA6IG51bGxcbiAgICB9KTtcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2FkZENob2ljZSA9IGZ1bmN0aW9uIChfYSkge1xuICAgIHZhciB2YWx1ZSA9IF9hLnZhbHVlLFxuICAgICAgX2IgPSBfYS5sYWJlbCxcbiAgICAgIGxhYmVsID0gX2IgPT09IHZvaWQgMCA/IG51bGwgOiBfYixcbiAgICAgIF9jID0gX2EuaXNTZWxlY3RlZCxcbiAgICAgIGlzU2VsZWN0ZWQgPSBfYyA9PT0gdm9pZCAwID8gZmFsc2UgOiBfYyxcbiAgICAgIF9kID0gX2EuaXNEaXNhYmxlZCxcbiAgICAgIGlzRGlzYWJsZWQgPSBfZCA9PT0gdm9pZCAwID8gZmFsc2UgOiBfZCxcbiAgICAgIF9lID0gX2EuZ3JvdXBJZCxcbiAgICAgIGdyb3VwSWQgPSBfZSA9PT0gdm9pZCAwID8gLTEgOiBfZSxcbiAgICAgIF9mID0gX2EuY3VzdG9tUHJvcGVydGllcyxcbiAgICAgIGN1c3RvbVByb3BlcnRpZXMgPSBfZiA9PT0gdm9pZCAwID8ge30gOiBfZixcbiAgICAgIF9nID0gX2EucGxhY2Vob2xkZXIsXG4gICAgICBwbGFjZWhvbGRlciA9IF9nID09PSB2b2lkIDAgPyBmYWxzZSA6IF9nLFxuICAgICAgX2ggPSBfYS5rZXlDb2RlLFxuICAgICAga2V5Q29kZSA9IF9oID09PSB2b2lkIDAgPyAtMSA6IF9oO1xuICAgIGlmICh0eXBlb2YgdmFsdWUgPT09ICd1bmRlZmluZWQnIHx8IHZhbHVlID09PSBudWxsKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIC8vIEdlbmVyYXRlIHVuaXF1ZSBpZFxuICAgIHZhciBjaG9pY2VzID0gdGhpcy5fc3RvcmUuY2hvaWNlcztcbiAgICB2YXIgY2hvaWNlTGFiZWwgPSBsYWJlbCB8fCB2YWx1ZTtcbiAgICB2YXIgY2hvaWNlSWQgPSBjaG9pY2VzID8gY2hvaWNlcy5sZW5ndGggKyAxIDogMTtcbiAgICB2YXIgY2hvaWNlRWxlbWVudElkID0gXCJcIi5jb25jYXQodGhpcy5fYmFzZUlkLCBcIi1cIikuY29uY2F0KHRoaXMuX2lkTmFtZXMuaXRlbUNob2ljZSwgXCItXCIpLmNvbmNhdChjaG9pY2VJZCk7XG4gICAgdGhpcy5fc3RvcmUuZGlzcGF0Y2goKDAsIGNob2ljZXNfMS5hZGRDaG9pY2UpKHtcbiAgICAgIGlkOiBjaG9pY2VJZCxcbiAgICAgIGdyb3VwSWQ6IGdyb3VwSWQsXG4gICAgICBlbGVtZW50SWQ6IGNob2ljZUVsZW1lbnRJZCxcbiAgICAgIHZhbHVlOiB2YWx1ZSxcbiAgICAgIGxhYmVsOiBjaG9pY2VMYWJlbCxcbiAgICAgIGRpc2FibGVkOiBpc0Rpc2FibGVkLFxuICAgICAgY3VzdG9tUHJvcGVydGllczogY3VzdG9tUHJvcGVydGllcyxcbiAgICAgIHBsYWNlaG9sZGVyOiBwbGFjZWhvbGRlcixcbiAgICAgIGtleUNvZGU6IGtleUNvZGVcbiAgICB9KSk7XG4gICAgaWYgKGlzU2VsZWN0ZWQpIHtcbiAgICAgIHRoaXMuX2FkZEl0ZW0oe1xuICAgICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICAgIGxhYmVsOiBjaG9pY2VMYWJlbCxcbiAgICAgICAgY2hvaWNlSWQ6IGNob2ljZUlkLFxuICAgICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjdXN0b21Qcm9wZXJ0aWVzLFxuICAgICAgICBwbGFjZWhvbGRlcjogcGxhY2Vob2xkZXIsXG4gICAgICAgIGtleUNvZGU6IGtleUNvZGVcbiAgICAgIH0pO1xuICAgIH1cbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2FkZEdyb3VwID0gZnVuY3Rpb24gKF9hKSB7XG4gICAgdmFyIF90aGlzID0gdGhpcztcbiAgICB2YXIgZ3JvdXAgPSBfYS5ncm91cCxcbiAgICAgIGlkID0gX2EuaWQsXG4gICAgICBfYiA9IF9hLnZhbHVlS2V5LFxuICAgICAgdmFsdWVLZXkgPSBfYiA9PT0gdm9pZCAwID8gJ3ZhbHVlJyA6IF9iLFxuICAgICAgX2MgPSBfYS5sYWJlbEtleSxcbiAgICAgIGxhYmVsS2V5ID0gX2MgPT09IHZvaWQgMCA/ICdsYWJlbCcgOiBfYztcbiAgICB2YXIgZ3JvdXBDaG9pY2VzID0gKDAsIHV0aWxzXzEuaXNUeXBlKSgnT2JqZWN0JywgZ3JvdXApID8gZ3JvdXAuY2hvaWNlcyA6IEFycmF5LmZyb20oZ3JvdXAuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ09QVElPTicpKTtcbiAgICB2YXIgZ3JvdXBJZCA9IGlkIHx8IE1hdGguZmxvb3IobmV3IERhdGUoKS52YWx1ZU9mKCkgKiBNYXRoLnJhbmRvbSgpKTtcbiAgICB2YXIgaXNEaXNhYmxlZCA9IGdyb3VwLmRpc2FibGVkID8gZ3JvdXAuZGlzYWJsZWQgOiBmYWxzZTtcbiAgICBpZiAoZ3JvdXBDaG9pY2VzKSB7XG4gICAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaCgoMCwgZ3JvdXBzXzEuYWRkR3JvdXApKHtcbiAgICAgICAgdmFsdWU6IGdyb3VwLmxhYmVsLFxuICAgICAgICBpZDogZ3JvdXBJZCxcbiAgICAgICAgYWN0aXZlOiB0cnVlLFxuICAgICAgICBkaXNhYmxlZDogaXNEaXNhYmxlZFxuICAgICAgfSkpO1xuICAgICAgdmFyIGFkZEdyb3VwQ2hvaWNlcyA9IGZ1bmN0aW9uIChjaG9pY2UpIHtcbiAgICAgICAgdmFyIGlzT3B0RGlzYWJsZWQgPSBjaG9pY2UuZGlzYWJsZWQgfHwgY2hvaWNlLnBhcmVudE5vZGUgJiYgY2hvaWNlLnBhcmVudE5vZGUuZGlzYWJsZWQ7XG4gICAgICAgIF90aGlzLl9hZGRDaG9pY2Uoe1xuICAgICAgICAgIHZhbHVlOiBjaG9pY2VbdmFsdWVLZXldLFxuICAgICAgICAgIGxhYmVsOiAoMCwgdXRpbHNfMS5pc1R5cGUpKCdPYmplY3QnLCBjaG9pY2UpID8gY2hvaWNlW2xhYmVsS2V5XSA6IGNob2ljZS5pbm5lckhUTUwsXG4gICAgICAgICAgaXNTZWxlY3RlZDogY2hvaWNlLnNlbGVjdGVkLFxuICAgICAgICAgIGlzRGlzYWJsZWQ6IGlzT3B0RGlzYWJsZWQsXG4gICAgICAgICAgZ3JvdXBJZDogZ3JvdXBJZCxcbiAgICAgICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjaG9pY2UuY3VzdG9tUHJvcGVydGllcyxcbiAgICAgICAgICBwbGFjZWhvbGRlcjogY2hvaWNlLnBsYWNlaG9sZGVyXG4gICAgICAgIH0pO1xuICAgICAgfTtcbiAgICAgIGdyb3VwQ2hvaWNlcy5mb3JFYWNoKGFkZEdyb3VwQ2hvaWNlcyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuX3N0b3JlLmRpc3BhdGNoKCgwLCBncm91cHNfMS5hZGRHcm91cCkoe1xuICAgICAgICB2YWx1ZTogZ3JvdXAubGFiZWwsXG4gICAgICAgIGlkOiBncm91cC5pZCxcbiAgICAgICAgYWN0aXZlOiBmYWxzZSxcbiAgICAgICAgZGlzYWJsZWQ6IGdyb3VwLmRpc2FibGVkXG4gICAgICB9KSk7XG4gICAgfVxuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fZ2V0VGVtcGxhdGUgPSBmdW5jdGlvbiAodGVtcGxhdGUpIHtcbiAgICB2YXIgX2E7XG4gICAgdmFyIGFyZ3MgPSBbXTtcbiAgICBmb3IgKHZhciBfaSA9IDE7IF9pIDwgYXJndW1lbnRzLmxlbmd0aDsgX2krKykge1xuICAgICAgYXJnc1tfaSAtIDFdID0gYXJndW1lbnRzW19pXTtcbiAgICB9XG4gICAgcmV0dXJuIChfYSA9IHRoaXMuX3RlbXBsYXRlc1t0ZW1wbGF0ZV0pLmNhbGwuYXBwbHkoX2EsIF9fc3ByZWFkQXJyYXkoW3RoaXMsIHRoaXMuY29uZmlnXSwgYXJncywgZmFsc2UpKTtcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2NyZWF0ZVRlbXBsYXRlcyA9IGZ1bmN0aW9uICgpIHtcbiAgICB2YXIgY2FsbGJhY2tPbkNyZWF0ZVRlbXBsYXRlcyA9IHRoaXMuY29uZmlnLmNhbGxiYWNrT25DcmVhdGVUZW1wbGF0ZXM7XG4gICAgdmFyIHVzZXJUZW1wbGF0ZXMgPSB7fTtcbiAgICBpZiAoY2FsbGJhY2tPbkNyZWF0ZVRlbXBsYXRlcyAmJiB0eXBlb2YgY2FsbGJhY2tPbkNyZWF0ZVRlbXBsYXRlcyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgdXNlclRlbXBsYXRlcyA9IGNhbGxiYWNrT25DcmVhdGVUZW1wbGF0ZXMuY2FsbCh0aGlzLCB1dGlsc18xLnN0clRvRWwpO1xuICAgIH1cbiAgICB0aGlzLl90ZW1wbGF0ZXMgPSAoMCwgZGVlcG1lcmdlXzEuZGVmYXVsdCkodGVtcGxhdGVzXzEuZGVmYXVsdCwgdXNlclRlbXBsYXRlcyk7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9jcmVhdGVFbGVtZW50cyA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLmNvbnRhaW5lck91dGVyID0gbmV3IGNvbXBvbmVudHNfMS5Db250YWluZXIoe1xuICAgICAgZWxlbWVudDogdGhpcy5fZ2V0VGVtcGxhdGUoJ2NvbnRhaW5lck91dGVyJywgdGhpcy5fZGlyZWN0aW9uLCB0aGlzLl9pc1NlbGVjdEVsZW1lbnQsIHRoaXMuX2lzU2VsZWN0T25lRWxlbWVudCwgdGhpcy5jb25maWcuc2VhcmNoRW5hYmxlZCwgdGhpcy5wYXNzZWRFbGVtZW50LmVsZW1lbnQudHlwZSwgdGhpcy5jb25maWcubGFiZWxJZCksXG4gICAgICBjbGFzc05hbWVzOiB0aGlzLmNvbmZpZy5jbGFzc05hbWVzLFxuICAgICAgdHlwZTogdGhpcy5wYXNzZWRFbGVtZW50LmVsZW1lbnQudHlwZSxcbiAgICAgIHBvc2l0aW9uOiB0aGlzLmNvbmZpZy5wb3NpdGlvblxuICAgIH0pO1xuICAgIHRoaXMuY29udGFpbmVySW5uZXIgPSBuZXcgY29tcG9uZW50c18xLkNvbnRhaW5lcih7XG4gICAgICBlbGVtZW50OiB0aGlzLl9nZXRUZW1wbGF0ZSgnY29udGFpbmVySW5uZXInKSxcbiAgICAgIGNsYXNzTmFtZXM6IHRoaXMuY29uZmlnLmNsYXNzTmFtZXMsXG4gICAgICB0eXBlOiB0aGlzLnBhc3NlZEVsZW1lbnQuZWxlbWVudC50eXBlLFxuICAgICAgcG9zaXRpb246IHRoaXMuY29uZmlnLnBvc2l0aW9uXG4gICAgfSk7XG4gICAgdGhpcy5pbnB1dCA9IG5ldyBjb21wb25lbnRzXzEuSW5wdXQoe1xuICAgICAgZWxlbWVudDogdGhpcy5fZ2V0VGVtcGxhdGUoJ2lucHV0JywgdGhpcy5fcGxhY2Vob2xkZXJWYWx1ZSksXG4gICAgICBjbGFzc05hbWVzOiB0aGlzLmNvbmZpZy5jbGFzc05hbWVzLFxuICAgICAgdHlwZTogdGhpcy5wYXNzZWRFbGVtZW50LmVsZW1lbnQudHlwZSxcbiAgICAgIHByZXZlbnRQYXN0ZTogIXRoaXMuY29uZmlnLnBhc3RlXG4gICAgfSk7XG4gICAgdGhpcy5jaG9pY2VMaXN0ID0gbmV3IGNvbXBvbmVudHNfMS5MaXN0KHtcbiAgICAgIGVsZW1lbnQ6IHRoaXMuX2dldFRlbXBsYXRlKCdjaG9pY2VMaXN0JywgdGhpcy5faXNTZWxlY3RPbmVFbGVtZW50KVxuICAgIH0pO1xuICAgIHRoaXMuaXRlbUxpc3QgPSBuZXcgY29tcG9uZW50c18xLkxpc3Qoe1xuICAgICAgZWxlbWVudDogdGhpcy5fZ2V0VGVtcGxhdGUoJ2l0ZW1MaXN0JywgdGhpcy5faXNTZWxlY3RPbmVFbGVtZW50KVxuICAgIH0pO1xuICAgIHRoaXMuZHJvcGRvd24gPSBuZXcgY29tcG9uZW50c18xLkRyb3Bkb3duKHtcbiAgICAgIGVsZW1lbnQ6IHRoaXMuX2dldFRlbXBsYXRlKCdkcm9wZG93bicpLFxuICAgICAgY2xhc3NOYW1lczogdGhpcy5jb25maWcuY2xhc3NOYW1lcyxcbiAgICAgIHR5cGU6IHRoaXMucGFzc2VkRWxlbWVudC5lbGVtZW50LnR5cGVcbiAgICB9KTtcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2NyZWF0ZVN0cnVjdHVyZSA9IGZ1bmN0aW9uICgpIHtcbiAgICAvLyBIaWRlIG9yaWdpbmFsIGVsZW1lbnRcbiAgICB0aGlzLnBhc3NlZEVsZW1lbnQuY29uY2VhbCgpO1xuICAgIC8vIFdyYXAgaW5wdXQgaW4gY29udGFpbmVyIHByZXNlcnZpbmcgRE9NIG9yZGVyaW5nXG4gICAgdGhpcy5jb250YWluZXJJbm5lci53cmFwKHRoaXMucGFzc2VkRWxlbWVudC5lbGVtZW50KTtcbiAgICAvLyBXcmFwcGVyIGlubmVyIGNvbnRhaW5lciB3aXRoIG91dGVyIGNvbnRhaW5lclxuICAgIHRoaXMuY29udGFpbmVyT3V0ZXIud3JhcCh0aGlzLmNvbnRhaW5lcklubmVyLmVsZW1lbnQpO1xuICAgIGlmICh0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIHRoaXMuaW5wdXQucGxhY2Vob2xkZXIgPSB0aGlzLmNvbmZpZy5zZWFyY2hQbGFjZWhvbGRlclZhbHVlIHx8ICcnO1xuICAgIH0gZWxzZSBpZiAodGhpcy5fcGxhY2Vob2xkZXJWYWx1ZSkge1xuICAgICAgdGhpcy5pbnB1dC5wbGFjZWhvbGRlciA9IHRoaXMuX3BsYWNlaG9sZGVyVmFsdWU7XG4gICAgICB0aGlzLmlucHV0LnNldFdpZHRoKCk7XG4gICAgfVxuICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudC5hcHBlbmRDaGlsZCh0aGlzLmNvbnRhaW5lcklubmVyLmVsZW1lbnQpO1xuICAgIHRoaXMuY29udGFpbmVyT3V0ZXIuZWxlbWVudC5hcHBlbmRDaGlsZCh0aGlzLmRyb3Bkb3duLmVsZW1lbnQpO1xuICAgIHRoaXMuY29udGFpbmVySW5uZXIuZWxlbWVudC5hcHBlbmRDaGlsZCh0aGlzLml0ZW1MaXN0LmVsZW1lbnQpO1xuICAgIGlmICghdGhpcy5faXNUZXh0RWxlbWVudCkge1xuICAgICAgdGhpcy5kcm9wZG93bi5lbGVtZW50LmFwcGVuZENoaWxkKHRoaXMuY2hvaWNlTGlzdC5lbGVtZW50KTtcbiAgICB9XG4gICAgaWYgKCF0aGlzLl9pc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIHRoaXMuY29udGFpbmVySW5uZXIuZWxlbWVudC5hcHBlbmRDaGlsZCh0aGlzLmlucHV0LmVsZW1lbnQpO1xuICAgIH0gZWxzZSBpZiAodGhpcy5jb25maWcuc2VhcmNoRW5hYmxlZCkge1xuICAgICAgdGhpcy5kcm9wZG93bi5lbGVtZW50Lmluc2VydEJlZm9yZSh0aGlzLmlucHV0LmVsZW1lbnQsIHRoaXMuZHJvcGRvd24uZWxlbWVudC5maXJzdENoaWxkKTtcbiAgICB9XG4gICAgaWYgKHRoaXMuX2lzU2VsZWN0RWxlbWVudCkge1xuICAgICAgdGhpcy5faGlnaGxpZ2h0UG9zaXRpb24gPSAwO1xuICAgICAgdGhpcy5faXNTZWFyY2hpbmcgPSBmYWxzZTtcbiAgICAgIHRoaXMuX3N0YXJ0TG9hZGluZygpO1xuICAgICAgaWYgKHRoaXMuX3ByZXNldEdyb3Vwcy5sZW5ndGgpIHtcbiAgICAgICAgdGhpcy5fYWRkUHJlZGVmaW5lZEdyb3Vwcyh0aGlzLl9wcmVzZXRHcm91cHMpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fYWRkUHJlZGVmaW5lZENob2ljZXModGhpcy5fcHJlc2V0Q2hvaWNlcyk7XG4gICAgICB9XG4gICAgICB0aGlzLl9zdG9wTG9hZGluZygpO1xuICAgIH1cbiAgICBpZiAodGhpcy5faXNUZXh0RWxlbWVudCkge1xuICAgICAgdGhpcy5fYWRkUHJlZGVmaW5lZEl0ZW1zKHRoaXMuX3ByZXNldEl0ZW1zKTtcbiAgICB9XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9hZGRQcmVkZWZpbmVkR3JvdXBzID0gZnVuY3Rpb24gKGdyb3Vwcykge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgLy8gSWYgd2UgaGF2ZSBhIHBsYWNlaG9sZGVyIG9wdGlvblxuICAgIHZhciBwbGFjZWhvbGRlckNob2ljZSA9IHRoaXMucGFzc2VkRWxlbWVudC5wbGFjZWhvbGRlck9wdGlvbjtcbiAgICBpZiAocGxhY2Vob2xkZXJDaG9pY2UgJiYgcGxhY2Vob2xkZXJDaG9pY2UucGFyZW50Tm9kZSAmJiBwbGFjZWhvbGRlckNob2ljZS5wYXJlbnROb2RlLnRhZ05hbWUgPT09ICdTRUxFQ1QnKSB7XG4gICAgICB0aGlzLl9hZGRDaG9pY2Uoe1xuICAgICAgICB2YWx1ZTogcGxhY2Vob2xkZXJDaG9pY2UudmFsdWUsXG4gICAgICAgIGxhYmVsOiBwbGFjZWhvbGRlckNob2ljZS5pbm5lckhUTUwsXG4gICAgICAgIGlzU2VsZWN0ZWQ6IHBsYWNlaG9sZGVyQ2hvaWNlLnNlbGVjdGVkLFxuICAgICAgICBpc0Rpc2FibGVkOiBwbGFjZWhvbGRlckNob2ljZS5kaXNhYmxlZCxcbiAgICAgICAgcGxhY2Vob2xkZXI6IHRydWVcbiAgICAgIH0pO1xuICAgIH1cbiAgICBncm91cHMuZm9yRWFjaChmdW5jdGlvbiAoZ3JvdXApIHtcbiAgICAgIHJldHVybiBfdGhpcy5fYWRkR3JvdXAoe1xuICAgICAgICBncm91cDogZ3JvdXAsXG4gICAgICAgIGlkOiBncm91cC5pZCB8fCBudWxsXG4gICAgICB9KTtcbiAgICB9KTtcbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2FkZFByZWRlZmluZWRDaG9pY2VzID0gZnVuY3Rpb24gKGNob2ljZXMpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIC8vIElmIHNvcnRpbmcgaXMgZW5hYmxlZCBvciB0aGUgdXNlciBpcyBzZWFyY2hpbmcsIGZpbHRlciBjaG9pY2VzXG4gICAgaWYgKHRoaXMuY29uZmlnLnNob3VsZFNvcnQpIHtcbiAgICAgIGNob2ljZXMuc29ydCh0aGlzLmNvbmZpZy5zb3J0ZXIpO1xuICAgIH1cbiAgICB2YXIgaGFzU2VsZWN0ZWRDaG9pY2UgPSBjaG9pY2VzLnNvbWUoZnVuY3Rpb24gKGNob2ljZSkge1xuICAgICAgcmV0dXJuIGNob2ljZS5zZWxlY3RlZDtcbiAgICB9KTtcbiAgICB2YXIgZmlyc3RFbmFibGVkQ2hvaWNlSW5kZXggPSBjaG9pY2VzLmZpbmRJbmRleChmdW5jdGlvbiAoY2hvaWNlKSB7XG4gICAgICByZXR1cm4gY2hvaWNlLmRpc2FibGVkID09PSB1bmRlZmluZWQgfHwgIWNob2ljZS5kaXNhYmxlZDtcbiAgICB9KTtcbiAgICBjaG9pY2VzLmZvckVhY2goZnVuY3Rpb24gKGNob2ljZSwgaW5kZXgpIHtcbiAgICAgIHZhciBfYSA9IGNob2ljZS52YWx1ZSxcbiAgICAgICAgdmFsdWUgPSBfYSA9PT0gdm9pZCAwID8gJycgOiBfYSxcbiAgICAgICAgbGFiZWwgPSBjaG9pY2UubGFiZWwsXG4gICAgICAgIGN1c3RvbVByb3BlcnRpZXMgPSBjaG9pY2UuY3VzdG9tUHJvcGVydGllcyxcbiAgICAgICAgcGxhY2Vob2xkZXIgPSBjaG9pY2UucGxhY2Vob2xkZXI7XG4gICAgICBpZiAoX3RoaXMuX2lzU2VsZWN0RWxlbWVudCkge1xuICAgICAgICAvLyBJZiB0aGUgY2hvaWNlIGlzIGFjdHVhbGx5IGEgZ3JvdXBcbiAgICAgICAgaWYgKGNob2ljZS5jaG9pY2VzKSB7XG4gICAgICAgICAgX3RoaXMuX2FkZEdyb3VwKHtcbiAgICAgICAgICAgIGdyb3VwOiBjaG9pY2UsXG4gICAgICAgICAgICBpZDogY2hvaWNlLmlkIHx8IG51bGxcbiAgICAgICAgICB9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAvKipcbiAgICAgICAgICAgKiBJZiB0aGVyZSBpcyBhIHNlbGVjdGVkIGNob2ljZSBhbHJlYWR5IG9yIHRoZSBjaG9pY2UgaXMgbm90IHRoZSBmaXJzdCBpblxuICAgICAgICAgICAqIHRoZSBhcnJheSwgYWRkIGVhY2ggY2hvaWNlIG5vcm1hbGx5LlxuICAgICAgICAgICAqXG4gICAgICAgICAgICogT3RoZXJ3aXNlIHdlIHByZS1zZWxlY3QgdGhlIGZpcnN0IGVuYWJsZWQgY2hvaWNlIGluIHRoZSBhcnJheSAoXCJzZWxlY3Qtb25lXCIgb25seSlcbiAgICAgICAgICAgKi9cbiAgICAgICAgICB2YXIgc2hvdWxkUHJlc2VsZWN0ID0gX3RoaXMuX2lzU2VsZWN0T25lRWxlbWVudCAmJiAhaGFzU2VsZWN0ZWRDaG9pY2UgJiYgaW5kZXggPT09IGZpcnN0RW5hYmxlZENob2ljZUluZGV4O1xuICAgICAgICAgIHZhciBpc1NlbGVjdGVkID0gc2hvdWxkUHJlc2VsZWN0ID8gdHJ1ZSA6IGNob2ljZS5zZWxlY3RlZDtcbiAgICAgICAgICB2YXIgaXNEaXNhYmxlZCA9IGNob2ljZS5kaXNhYmxlZDtcbiAgICAgICAgICBfdGhpcy5fYWRkQ2hvaWNlKHtcbiAgICAgICAgICAgIHZhbHVlOiB2YWx1ZSxcbiAgICAgICAgICAgIGxhYmVsOiBsYWJlbCxcbiAgICAgICAgICAgIGlzU2VsZWN0ZWQ6ICEhaXNTZWxlY3RlZCxcbiAgICAgICAgICAgIGlzRGlzYWJsZWQ6ICEhaXNEaXNhYmxlZCxcbiAgICAgICAgICAgIHBsYWNlaG9sZGVyOiAhIXBsYWNlaG9sZGVyLFxuICAgICAgICAgICAgY3VzdG9tUHJvcGVydGllczogY3VzdG9tUHJvcGVydGllc1xuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBfdGhpcy5fYWRkQ2hvaWNlKHtcbiAgICAgICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICAgICAgbGFiZWw6IGxhYmVsLFxuICAgICAgICAgIGlzU2VsZWN0ZWQ6ICEhY2hvaWNlLnNlbGVjdGVkLFxuICAgICAgICAgIGlzRGlzYWJsZWQ6ICEhY2hvaWNlLmRpc2FibGVkLFxuICAgICAgICAgIHBsYWNlaG9sZGVyOiAhIWNob2ljZS5wbGFjZWhvbGRlcixcbiAgICAgICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjdXN0b21Qcm9wZXJ0aWVzXG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fYWRkUHJlZGVmaW5lZEl0ZW1zID0gZnVuY3Rpb24gKGl0ZW1zKSB7XG4gICAgdmFyIF90aGlzID0gdGhpcztcbiAgICBpdGVtcy5mb3JFYWNoKGZ1bmN0aW9uIChpdGVtKSB7XG4gICAgICBpZiAodHlwZW9mIGl0ZW0gPT09ICdvYmplY3QnICYmIGl0ZW0udmFsdWUpIHtcbiAgICAgICAgX3RoaXMuX2FkZEl0ZW0oe1xuICAgICAgICAgIHZhbHVlOiBpdGVtLnZhbHVlLFxuICAgICAgICAgIGxhYmVsOiBpdGVtLmxhYmVsLFxuICAgICAgICAgIGNob2ljZUlkOiBpdGVtLmlkLFxuICAgICAgICAgIGN1c3RvbVByb3BlcnRpZXM6IGl0ZW0uY3VzdG9tUHJvcGVydGllcyxcbiAgICAgICAgICBwbGFjZWhvbGRlcjogaXRlbS5wbGFjZWhvbGRlclxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmICh0eXBlb2YgaXRlbSA9PT0gJ3N0cmluZycpIHtcbiAgICAgICAgX3RoaXMuX2FkZEl0ZW0oe1xuICAgICAgICAgIHZhbHVlOiBpdGVtXG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pO1xuICB9O1xuICBDaG9pY2VzLnByb3RvdHlwZS5fc2V0Q2hvaWNlT3JJdGVtID0gZnVuY3Rpb24gKGl0ZW0pIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIHZhciBpdGVtVHlwZSA9ICgwLCB1dGlsc18xLmdldFR5cGUpKGl0ZW0pLnRvTG93ZXJDYXNlKCk7XG4gICAgdmFyIGhhbmRsZVR5cGUgPSB7XG4gICAgICBvYmplY3Q6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgaWYgKCFpdGVtLnZhbHVlKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIC8vIElmIHdlIGFyZSBkZWFsaW5nIHdpdGggYSBzZWxlY3QgaW5wdXQsIHdlIG5lZWQgdG8gY3JlYXRlIGFuIG9wdGlvbiBmaXJzdFxuICAgICAgICAvLyB0aGF0IGlzIHRoZW4gc2VsZWN0ZWQuIEZvciB0ZXh0IGlucHV0cyB3ZSBjYW4ganVzdCBhZGQgaXRlbXMgbm9ybWFsbHkuXG4gICAgICAgIGlmICghX3RoaXMuX2lzVGV4dEVsZW1lbnQpIHtcbiAgICAgICAgICBfdGhpcy5fYWRkQ2hvaWNlKHtcbiAgICAgICAgICAgIHZhbHVlOiBpdGVtLnZhbHVlLFxuICAgICAgICAgICAgbGFiZWw6IGl0ZW0ubGFiZWwsXG4gICAgICAgICAgICBpc1NlbGVjdGVkOiB0cnVlLFxuICAgICAgICAgICAgaXNEaXNhYmxlZDogZmFsc2UsXG4gICAgICAgICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBpdGVtLmN1c3RvbVByb3BlcnRpZXMsXG4gICAgICAgICAgICBwbGFjZWhvbGRlcjogaXRlbS5wbGFjZWhvbGRlclxuICAgICAgICAgIH0pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIF90aGlzLl9hZGRJdGVtKHtcbiAgICAgICAgICAgIHZhbHVlOiBpdGVtLnZhbHVlLFxuICAgICAgICAgICAgbGFiZWw6IGl0ZW0ubGFiZWwsXG4gICAgICAgICAgICBjaG9pY2VJZDogaXRlbS5pZCxcbiAgICAgICAgICAgIGN1c3RvbVByb3BlcnRpZXM6IGl0ZW0uY3VzdG9tUHJvcGVydGllcyxcbiAgICAgICAgICAgIHBsYWNlaG9sZGVyOiBpdGVtLnBsYWNlaG9sZGVyXG4gICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgIH0sXG4gICAgICBzdHJpbmc6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgaWYgKCFfdGhpcy5faXNUZXh0RWxlbWVudCkge1xuICAgICAgICAgIF90aGlzLl9hZGRDaG9pY2Uoe1xuICAgICAgICAgICAgdmFsdWU6IGl0ZW0sXG4gICAgICAgICAgICBsYWJlbDogaXRlbSxcbiAgICAgICAgICAgIGlzU2VsZWN0ZWQ6IHRydWUsXG4gICAgICAgICAgICBpc0Rpc2FibGVkOiBmYWxzZVxuICAgICAgICAgIH0pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIF90aGlzLl9hZGRJdGVtKHtcbiAgICAgICAgICAgIHZhbHVlOiBpdGVtXG4gICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9O1xuICAgIGhhbmRsZVR5cGVbaXRlbVR5cGVdKCk7XG4gIH07XG4gIENob2ljZXMucHJvdG90eXBlLl9maW5kQW5kU2VsZWN0Q2hvaWNlQnlWYWx1ZSA9IGZ1bmN0aW9uICh2YWx1ZSkge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgdmFyIGNob2ljZXMgPSB0aGlzLl9zdG9yZS5jaG9pY2VzO1xuICAgIC8vIENoZWNrICd2YWx1ZScgcHJvcGVydHkgZXhpc3RzIGFuZCB0aGUgY2hvaWNlIGlzbid0IGFscmVhZHkgc2VsZWN0ZWRcbiAgICB2YXIgZm91bmRDaG9pY2UgPSBjaG9pY2VzLmZpbmQoZnVuY3Rpb24gKGNob2ljZSkge1xuICAgICAgcmV0dXJuIF90aGlzLmNvbmZpZy52YWx1ZUNvbXBhcmVyKGNob2ljZS52YWx1ZSwgdmFsdWUpO1xuICAgIH0pO1xuICAgIGlmIChmb3VuZENob2ljZSAmJiAhZm91bmRDaG9pY2Uuc2VsZWN0ZWQpIHtcbiAgICAgIHRoaXMuX2FkZEl0ZW0oe1xuICAgICAgICB2YWx1ZTogZm91bmRDaG9pY2UudmFsdWUsXG4gICAgICAgIGxhYmVsOiBmb3VuZENob2ljZS5sYWJlbCxcbiAgICAgICAgY2hvaWNlSWQ6IGZvdW5kQ2hvaWNlLmlkLFxuICAgICAgICBncm91cElkOiBmb3VuZENob2ljZS5ncm91cElkLFxuICAgICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBmb3VuZENob2ljZS5jdXN0b21Qcm9wZXJ0aWVzLFxuICAgICAgICBwbGFjZWhvbGRlcjogZm91bmRDaG9pY2UucGxhY2Vob2xkZXIsXG4gICAgICAgIGtleUNvZGU6IGZvdW5kQ2hvaWNlLmtleUNvZGVcbiAgICAgIH0pO1xuICAgIH1cbiAgfTtcbiAgQ2hvaWNlcy5wcm90b3R5cGUuX2dlbmVyYXRlUGxhY2Vob2xkZXJWYWx1ZSA9IGZ1bmN0aW9uICgpIHtcbiAgICBpZiAodGhpcy5faXNTZWxlY3RFbGVtZW50ICYmIHRoaXMucGFzc2VkRWxlbWVudC5wbGFjZWhvbGRlck9wdGlvbikge1xuICAgICAgdmFyIHBsYWNlaG9sZGVyT3B0aW9uID0gdGhpcy5wYXNzZWRFbGVtZW50LnBsYWNlaG9sZGVyT3B0aW9uO1xuICAgICAgcmV0dXJuIHBsYWNlaG9sZGVyT3B0aW9uID8gcGxhY2Vob2xkZXJPcHRpb24udGV4dCA6IG51bGw7XG4gICAgfVxuICAgIHZhciBfYSA9IHRoaXMuY29uZmlnLFxuICAgICAgcGxhY2Vob2xkZXIgPSBfYS5wbGFjZWhvbGRlcixcbiAgICAgIHBsYWNlaG9sZGVyVmFsdWUgPSBfYS5wbGFjZWhvbGRlclZhbHVlO1xuICAgIHZhciBkYXRhc2V0ID0gdGhpcy5wYXNzZWRFbGVtZW50LmVsZW1lbnQuZGF0YXNldDtcbiAgICBpZiAocGxhY2Vob2xkZXIpIHtcbiAgICAgIGlmIChwbGFjZWhvbGRlclZhbHVlKSB7XG4gICAgICAgIHJldHVybiBwbGFjZWhvbGRlclZhbHVlO1xuICAgICAgfVxuICAgICAgaWYgKGRhdGFzZXQucGxhY2Vob2xkZXIpIHtcbiAgICAgICAgcmV0dXJuIGRhdGFzZXQucGxhY2Vob2xkZXI7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBudWxsO1xuICB9O1xuICByZXR1cm4gQ2hvaWNlcztcbn0oKTtcbmV4cG9ydHNbXCJkZWZhdWx0XCJdID0gQ2hvaWNlcztcblxuLyoqKi8gfSksXG5cbi8qKiovIDYxMzpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xudmFyIHV0aWxzXzEgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDc5OSk7XG52YXIgY29uc3RhbnRzXzEgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDg4Myk7XG52YXIgQ29udGFpbmVyID0gLyoqIEBjbGFzcyAqL2Z1bmN0aW9uICgpIHtcbiAgZnVuY3Rpb24gQ29udGFpbmVyKF9hKSB7XG4gICAgdmFyIGVsZW1lbnQgPSBfYS5lbGVtZW50LFxuICAgICAgdHlwZSA9IF9hLnR5cGUsXG4gICAgICBjbGFzc05hbWVzID0gX2EuY2xhc3NOYW1lcyxcbiAgICAgIHBvc2l0aW9uID0gX2EucG9zaXRpb247XG4gICAgdGhpcy5lbGVtZW50ID0gZWxlbWVudDtcbiAgICB0aGlzLmNsYXNzTmFtZXMgPSBjbGFzc05hbWVzO1xuICAgIHRoaXMudHlwZSA9IHR5cGU7XG4gICAgdGhpcy5wb3NpdGlvbiA9IHBvc2l0aW9uO1xuICAgIHRoaXMuaXNPcGVuID0gZmFsc2U7XG4gICAgdGhpcy5pc0ZsaXBwZWQgPSBmYWxzZTtcbiAgICB0aGlzLmlzRm9jdXNzZWQgPSBmYWxzZTtcbiAgICB0aGlzLmlzRGlzYWJsZWQgPSBmYWxzZTtcbiAgICB0aGlzLmlzTG9hZGluZyA9IGZhbHNlO1xuICAgIHRoaXMuX29uRm9jdXMgPSB0aGlzLl9vbkZvY3VzLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25CbHVyID0gdGhpcy5fb25CbHVyLmJpbmQodGhpcyk7XG4gIH1cbiAgQ29udGFpbmVyLnByb3RvdHlwZS5hZGRFdmVudExpc3RlbmVycyA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLmVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignZm9jdXMnLCB0aGlzLl9vbkZvY3VzKTtcbiAgICB0aGlzLmVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignYmx1cicsIHRoaXMuX29uQmx1cik7XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUucmVtb3ZlRXZlbnRMaXN0ZW5lcnMgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2ZvY3VzJywgdGhpcy5fb25Gb2N1cyk7XG4gICAgdGhpcy5lbGVtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoJ2JsdXInLCB0aGlzLl9vbkJsdXIpO1xuICB9O1xuICAvKipcbiAgICogRGV0ZXJtaW5lIHdoZXRoZXIgY29udGFpbmVyIHNob3VsZCBiZSBmbGlwcGVkIGJhc2VkIG9uIHBhc3NlZFxuICAgKiBkcm9wZG93biBwb3NpdGlvblxuICAgKi9cbiAgQ29udGFpbmVyLnByb3RvdHlwZS5zaG91bGRGbGlwID0gZnVuY3Rpb24gKGRyb3Bkb3duUG9zKSB7XG4gICAgaWYgKHR5cGVvZiBkcm9wZG93blBvcyAhPT0gJ251bWJlcicpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gICAgLy8gSWYgZmxpcCBpcyBlbmFibGVkIGFuZCB0aGUgZHJvcGRvd24gYm90dG9tIHBvc2l0aW9uIGlzXG4gICAgLy8gZ3JlYXRlciB0aGFuIHRoZSB3aW5kb3cgaGVpZ2h0IGZsaXAgdGhlIGRyb3Bkb3duLlxuICAgIHZhciBzaG91bGRGbGlwID0gZmFsc2U7XG4gICAgaWYgKHRoaXMucG9zaXRpb24gPT09ICdhdXRvJykge1xuICAgICAgc2hvdWxkRmxpcCA9ICF3aW5kb3cubWF0Y2hNZWRpYShcIihtaW4taGVpZ2h0OiBcIi5jb25jYXQoZHJvcGRvd25Qb3MgKyAxLCBcInB4KVwiKSkubWF0Y2hlcztcbiAgICB9IGVsc2UgaWYgKHRoaXMucG9zaXRpb24gPT09ICd0b3AnKSB7XG4gICAgICBzaG91bGRGbGlwID0gdHJ1ZTtcbiAgICB9XG4gICAgcmV0dXJuIHNob3VsZEZsaXA7XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUuc2V0QWN0aXZlRGVzY2VuZGFudCA9IGZ1bmN0aW9uIChhY3RpdmVEZXNjZW5kYW50SUQpIHtcbiAgICB0aGlzLmVsZW1lbnQuc2V0QXR0cmlidXRlKCdhcmlhLWFjdGl2ZWRlc2NlbmRhbnQnLCBhY3RpdmVEZXNjZW5kYW50SUQpO1xuICB9O1xuICBDb250YWluZXIucHJvdG90eXBlLnJlbW92ZUFjdGl2ZURlc2NlbmRhbnQgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LnJlbW92ZUF0dHJpYnV0ZSgnYXJpYS1hY3RpdmVkZXNjZW5kYW50Jyk7XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUub3BlbiA9IGZ1bmN0aW9uIChkcm9wZG93blBvcykge1xuICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QuYWRkKHRoaXMuY2xhc3NOYW1lcy5vcGVuU3RhdGUpO1xuICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ2FyaWEtZXhwYW5kZWQnLCAndHJ1ZScpO1xuICAgIHRoaXMuaXNPcGVuID0gdHJ1ZTtcbiAgICBpZiAodGhpcy5zaG91bGRGbGlwKGRyb3Bkb3duUG9zKSkge1xuICAgICAgdGhpcy5lbGVtZW50LmNsYXNzTGlzdC5hZGQodGhpcy5jbGFzc05hbWVzLmZsaXBwZWRTdGF0ZSk7XG4gICAgICB0aGlzLmlzRmxpcHBlZCA9IHRydWU7XG4gICAgfVxuICB9O1xuICBDb250YWluZXIucHJvdG90eXBlLmNsb3NlID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKHRoaXMuY2xhc3NOYW1lcy5vcGVuU3RhdGUpO1xuICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ2FyaWEtZXhwYW5kZWQnLCAnZmFsc2UnKTtcbiAgICB0aGlzLnJlbW92ZUFjdGl2ZURlc2NlbmRhbnQoKTtcbiAgICB0aGlzLmlzT3BlbiA9IGZhbHNlO1xuICAgIC8vIEEgZHJvcGRvd24gZmxpcHMgaWYgaXQgZG9lcyBub3QgaGF2ZSBzcGFjZSB3aXRoaW4gdGhlIHBhZ2VcbiAgICBpZiAodGhpcy5pc0ZsaXBwZWQpIHtcbiAgICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKHRoaXMuY2xhc3NOYW1lcy5mbGlwcGVkU3RhdGUpO1xuICAgICAgdGhpcy5pc0ZsaXBwZWQgPSBmYWxzZTtcbiAgICB9XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUuZm9jdXMgPSBmdW5jdGlvbiAoKSB7XG4gICAgaWYgKCF0aGlzLmlzRm9jdXNzZWQpIHtcbiAgICAgIHRoaXMuZWxlbWVudC5mb2N1cygpO1xuICAgIH1cbiAgfTtcbiAgQ29udGFpbmVyLnByb3RvdHlwZS5hZGRGb2N1c1N0YXRlID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QuYWRkKHRoaXMuY2xhc3NOYW1lcy5mb2N1c1N0YXRlKTtcbiAgfTtcbiAgQ29udGFpbmVyLnByb3RvdHlwZS5yZW1vdmVGb2N1c1N0YXRlID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKHRoaXMuY2xhc3NOYW1lcy5mb2N1c1N0YXRlKTtcbiAgfTtcbiAgQ29udGFpbmVyLnByb3RvdHlwZS5lbmFibGUgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LmNsYXNzTGlzdC5yZW1vdmUodGhpcy5jbGFzc05hbWVzLmRpc2FibGVkU3RhdGUpO1xuICAgIHRoaXMuZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUoJ2FyaWEtZGlzYWJsZWQnKTtcbiAgICBpZiAodGhpcy50eXBlID09PSBjb25zdGFudHNfMS5TRUxFQ1RfT05FX1RZUEUpIHtcbiAgICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ3RhYmluZGV4JywgJzAnKTtcbiAgICB9XG4gICAgdGhpcy5pc0Rpc2FibGVkID0gZmFsc2U7XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUuZGlzYWJsZSA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLmVsZW1lbnQuY2xhc3NMaXN0LmFkZCh0aGlzLmNsYXNzTmFtZXMuZGlzYWJsZWRTdGF0ZSk7XG4gICAgdGhpcy5lbGVtZW50LnNldEF0dHJpYnV0ZSgnYXJpYS1kaXNhYmxlZCcsICd0cnVlJyk7XG4gICAgaWYgKHRoaXMudHlwZSA9PT0gY29uc3RhbnRzXzEuU0VMRUNUX09ORV9UWVBFKSB7XG4gICAgICB0aGlzLmVsZW1lbnQuc2V0QXR0cmlidXRlKCd0YWJpbmRleCcsICctMScpO1xuICAgIH1cbiAgICB0aGlzLmlzRGlzYWJsZWQgPSB0cnVlO1xuICB9O1xuICBDb250YWluZXIucHJvdG90eXBlLndyYXAgPSBmdW5jdGlvbiAoZWxlbWVudCkge1xuICAgICgwLCB1dGlsc18xLndyYXApKGVsZW1lbnQsIHRoaXMuZWxlbWVudCk7XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUudW53cmFwID0gZnVuY3Rpb24gKGVsZW1lbnQpIHtcbiAgICBpZiAodGhpcy5lbGVtZW50LnBhcmVudE5vZGUpIHtcbiAgICAgIC8vIE1vdmUgcGFzc2VkIGVsZW1lbnQgb3V0c2lkZSB0aGlzIGVsZW1lbnRcbiAgICAgIHRoaXMuZWxlbWVudC5wYXJlbnROb2RlLmluc2VydEJlZm9yZShlbGVtZW50LCB0aGlzLmVsZW1lbnQpO1xuICAgICAgLy8gUmVtb3ZlIHRoaXMgZWxlbWVudFxuICAgICAgdGhpcy5lbGVtZW50LnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQodGhpcy5lbGVtZW50KTtcbiAgICB9XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUuYWRkTG9hZGluZ1N0YXRlID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QuYWRkKHRoaXMuY2xhc3NOYW1lcy5sb2FkaW5nU3RhdGUpO1xuICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ2FyaWEtYnVzeScsICd0cnVlJyk7XG4gICAgdGhpcy5pc0xvYWRpbmcgPSB0cnVlO1xuICB9O1xuICBDb250YWluZXIucHJvdG90eXBlLnJlbW92ZUxvYWRpbmdTdGF0ZSA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLmVsZW1lbnQuY2xhc3NMaXN0LnJlbW92ZSh0aGlzLmNsYXNzTmFtZXMubG9hZGluZ1N0YXRlKTtcbiAgICB0aGlzLmVsZW1lbnQucmVtb3ZlQXR0cmlidXRlKCdhcmlhLWJ1c3knKTtcbiAgICB0aGlzLmlzTG9hZGluZyA9IGZhbHNlO1xuICB9O1xuICBDb250YWluZXIucHJvdG90eXBlLl9vbkZvY3VzID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuaXNGb2N1c3NlZCA9IHRydWU7XG4gIH07XG4gIENvbnRhaW5lci5wcm90b3R5cGUuX29uQmx1ciA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLmlzRm9jdXNzZWQgPSBmYWxzZTtcbiAgfTtcbiAgcmV0dXJuIENvbnRhaW5lcjtcbn0oKTtcbmV4cG9ydHNbXCJkZWZhdWx0XCJdID0gQ29udGFpbmVyO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gMjE3OlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG52YXIgRHJvcGRvd24gPSAvKiogQGNsYXNzICovZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBEcm9wZG93bihfYSkge1xuICAgIHZhciBlbGVtZW50ID0gX2EuZWxlbWVudCxcbiAgICAgIHR5cGUgPSBfYS50eXBlLFxuICAgICAgY2xhc3NOYW1lcyA9IF9hLmNsYXNzTmFtZXM7XG4gICAgdGhpcy5lbGVtZW50ID0gZWxlbWVudDtcbiAgICB0aGlzLmNsYXNzTmFtZXMgPSBjbGFzc05hbWVzO1xuICAgIHRoaXMudHlwZSA9IHR5cGU7XG4gICAgdGhpcy5pc0FjdGl2ZSA9IGZhbHNlO1xuICB9XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShEcm9wZG93bi5wcm90b3R5cGUsIFwiZGlzdGFuY2VGcm9tVG9wV2luZG93XCIsIHtcbiAgICAvKipcbiAgICAgKiBCb3R0b20gcG9zaXRpb24gb2YgZHJvcGRvd24gaW4gdmlld3BvcnQgY29vcmRpbmF0ZXNcbiAgICAgKi9cbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiB0aGlzLmVsZW1lbnQuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkuYm90dG9tO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBEcm9wZG93bi5wcm90b3R5cGUuZ2V0Q2hpbGQgPSBmdW5jdGlvbiAoc2VsZWN0b3IpIHtcbiAgICByZXR1cm4gdGhpcy5lbGVtZW50LnF1ZXJ5U2VsZWN0b3Ioc2VsZWN0b3IpO1xuICB9O1xuICAvKipcbiAgICogU2hvdyBkcm9wZG93biB0byB1c2VyIGJ5IGFkZGluZyBhY3RpdmUgc3RhdGUgY2xhc3NcbiAgICovXG4gIERyb3Bkb3duLnByb3RvdHlwZS5zaG93ID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QuYWRkKHRoaXMuY2xhc3NOYW1lcy5hY3RpdmVTdGF0ZSk7XG4gICAgdGhpcy5lbGVtZW50LnNldEF0dHJpYnV0ZSgnYXJpYS1leHBhbmRlZCcsICd0cnVlJyk7XG4gICAgdGhpcy5pc0FjdGl2ZSA9IHRydWU7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH07XG4gIC8qKlxuICAgKiBIaWRlIGRyb3Bkb3duIGZyb20gdXNlclxuICAgKi9cbiAgRHJvcGRvd24ucHJvdG90eXBlLmhpZGUgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LmNsYXNzTGlzdC5yZW1vdmUodGhpcy5jbGFzc05hbWVzLmFjdGl2ZVN0YXRlKTtcbiAgICB0aGlzLmVsZW1lbnQuc2V0QXR0cmlidXRlKCdhcmlhLWV4cGFuZGVkJywgJ2ZhbHNlJyk7XG4gICAgdGhpcy5pc0FjdGl2ZSA9IGZhbHNlO1xuICAgIHJldHVybiB0aGlzO1xuICB9O1xuICByZXR1cm4gRHJvcGRvd247XG59KCk7XG5leHBvcnRzW1wiZGVmYXVsdFwiXSA9IERyb3Bkb3duO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gNTIwOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKSB7XG5cblxuXG52YXIgX19pbXBvcnREZWZhdWx0ID0gdGhpcyAmJiB0aGlzLl9faW1wb3J0RGVmYXVsdCB8fCBmdW5jdGlvbiAobW9kKSB7XG4gIHJldHVybiBtb2QgJiYgbW9kLl9fZXNNb2R1bGUgPyBtb2QgOiB7XG4gICAgXCJkZWZhdWx0XCI6IG1vZFxuICB9O1xufTtcbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbmV4cG9ydHMuV3JhcHBlZFNlbGVjdCA9IGV4cG9ydHMuV3JhcHBlZElucHV0ID0gZXhwb3J0cy5MaXN0ID0gZXhwb3J0cy5JbnB1dCA9IGV4cG9ydHMuQ29udGFpbmVyID0gZXhwb3J0cy5Ecm9wZG93biA9IHZvaWQgMDtcbnZhciBkcm9wZG93bl8xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oMjE3KSk7XG5leHBvcnRzLkRyb3Bkb3duID0gZHJvcGRvd25fMS5kZWZhdWx0O1xudmFyIGNvbnRhaW5lcl8xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oNjEzKSk7XG5leHBvcnRzLkNvbnRhaW5lciA9IGNvbnRhaW5lcl8xLmRlZmF1bHQ7XG52YXIgaW5wdXRfMSA9IF9faW1wb3J0RGVmYXVsdChfX3dlYnBhY2tfcmVxdWlyZV9fKDExKSk7XG5leHBvcnRzLklucHV0ID0gaW5wdXRfMS5kZWZhdWx0O1xudmFyIGxpc3RfMSA9IF9faW1wb3J0RGVmYXVsdChfX3dlYnBhY2tfcmVxdWlyZV9fKDYyNCkpO1xuZXhwb3J0cy5MaXN0ID0gbGlzdF8xLmRlZmF1bHQ7XG52YXIgd3JhcHBlZF9pbnB1dF8xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oNTQxKSk7XG5leHBvcnRzLldyYXBwZWRJbnB1dCA9IHdyYXBwZWRfaW5wdXRfMS5kZWZhdWx0O1xudmFyIHdyYXBwZWRfc2VsZWN0XzEgPSBfX2ltcG9ydERlZmF1bHQoX193ZWJwYWNrX3JlcXVpcmVfXyg5ODIpKTtcbmV4cG9ydHMuV3JhcHBlZFNlbGVjdCA9IHdyYXBwZWRfc2VsZWN0XzEuZGVmYXVsdDtcblxuLyoqKi8gfSksXG5cbi8qKiovIDExOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG52YXIgdXRpbHNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oNzk5KTtcbnZhciBjb25zdGFudHNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oODgzKTtcbnZhciBJbnB1dCA9IC8qKiBAY2xhc3MgKi9mdW5jdGlvbiAoKSB7XG4gIGZ1bmN0aW9uIElucHV0KF9hKSB7XG4gICAgdmFyIGVsZW1lbnQgPSBfYS5lbGVtZW50LFxuICAgICAgdHlwZSA9IF9hLnR5cGUsXG4gICAgICBjbGFzc05hbWVzID0gX2EuY2xhc3NOYW1lcyxcbiAgICAgIHByZXZlbnRQYXN0ZSA9IF9hLnByZXZlbnRQYXN0ZTtcbiAgICB0aGlzLmVsZW1lbnQgPSBlbGVtZW50O1xuICAgIHRoaXMudHlwZSA9IHR5cGU7XG4gICAgdGhpcy5jbGFzc05hbWVzID0gY2xhc3NOYW1lcztcbiAgICB0aGlzLnByZXZlbnRQYXN0ZSA9IHByZXZlbnRQYXN0ZTtcbiAgICB0aGlzLmlzRm9jdXNzZWQgPSB0aGlzLmVsZW1lbnQuaXNFcXVhbE5vZGUoZG9jdW1lbnQuYWN0aXZlRWxlbWVudCk7XG4gICAgdGhpcy5pc0Rpc2FibGVkID0gZWxlbWVudC5kaXNhYmxlZDtcbiAgICB0aGlzLl9vblBhc3RlID0gdGhpcy5fb25QYXN0ZS5iaW5kKHRoaXMpO1xuICAgIHRoaXMuX29uSW5wdXQgPSB0aGlzLl9vbklucHV0LmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25Gb2N1cyA9IHRoaXMuX29uRm9jdXMuYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vbkJsdXIgPSB0aGlzLl9vbkJsdXIuYmluZCh0aGlzKTtcbiAgfVxuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoSW5wdXQucHJvdG90eXBlLCBcInBsYWNlaG9sZGVyXCIsIHtcbiAgICBzZXQ6IGZ1bmN0aW9uIChwbGFjZWhvbGRlcikge1xuICAgICAgdGhpcy5lbGVtZW50LnBsYWNlaG9sZGVyID0gcGxhY2Vob2xkZXI7XG4gICAgfSxcbiAgICBlbnVtZXJhYmxlOiBmYWxzZSxcbiAgICBjb25maWd1cmFibGU6IHRydWVcbiAgfSk7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShJbnB1dC5wcm90b3R5cGUsIFwidmFsdWVcIiwge1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuICgwLCB1dGlsc18xLnNhbml0aXNlKSh0aGlzLmVsZW1lbnQudmFsdWUpO1xuICAgIH0sXG4gICAgc2V0OiBmdW5jdGlvbiAodmFsdWUpIHtcbiAgICAgIHRoaXMuZWxlbWVudC52YWx1ZSA9IHZhbHVlO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoSW5wdXQucHJvdG90eXBlLCBcInJhd1ZhbHVlXCIsIHtcbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiB0aGlzLmVsZW1lbnQudmFsdWU7XG4gICAgfSxcbiAgICBlbnVtZXJhYmxlOiBmYWxzZSxcbiAgICBjb25maWd1cmFibGU6IHRydWVcbiAgfSk7XG4gIElucHV0LnByb3RvdHlwZS5hZGRFdmVudExpc3RlbmVycyA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLmVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcigncGFzdGUnLCB0aGlzLl9vblBhc3RlKTtcbiAgICB0aGlzLmVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignaW5wdXQnLCB0aGlzLl9vbklucHV0LCB7XG4gICAgICBwYXNzaXZlOiB0cnVlXG4gICAgfSk7XG4gICAgdGhpcy5lbGVtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2ZvY3VzJywgdGhpcy5fb25Gb2N1cywge1xuICAgICAgcGFzc2l2ZTogdHJ1ZVxuICAgIH0pO1xuICAgIHRoaXMuZWxlbWVudC5hZGRFdmVudExpc3RlbmVyKCdibHVyJywgdGhpcy5fb25CbHVyLCB7XG4gICAgICBwYXNzaXZlOiB0cnVlXG4gICAgfSk7XG4gIH07XG4gIElucHV0LnByb3RvdHlwZS5yZW1vdmVFdmVudExpc3RlbmVycyA9IGZ1bmN0aW9uICgpIHtcbiAgICB0aGlzLmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcignaW5wdXQnLCB0aGlzLl9vbklucHV0KTtcbiAgICB0aGlzLmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcigncGFzdGUnLCB0aGlzLl9vblBhc3RlKTtcbiAgICB0aGlzLmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcignZm9jdXMnLCB0aGlzLl9vbkZvY3VzKTtcbiAgICB0aGlzLmVsZW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcignYmx1cicsIHRoaXMuX29uQmx1cik7XG4gIH07XG4gIElucHV0LnByb3RvdHlwZS5lbmFibGUgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LnJlbW92ZUF0dHJpYnV0ZSgnZGlzYWJsZWQnKTtcbiAgICB0aGlzLmlzRGlzYWJsZWQgPSBmYWxzZTtcbiAgfTtcbiAgSW5wdXQucHJvdG90eXBlLmRpc2FibGUgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LnNldEF0dHJpYnV0ZSgnZGlzYWJsZWQnLCAnJyk7XG4gICAgdGhpcy5pc0Rpc2FibGVkID0gdHJ1ZTtcbiAgfTtcbiAgSW5wdXQucHJvdG90eXBlLmZvY3VzID0gZnVuY3Rpb24gKCkge1xuICAgIGlmICghdGhpcy5pc0ZvY3Vzc2VkKSB7XG4gICAgICB0aGlzLmVsZW1lbnQuZm9jdXMoKTtcbiAgICB9XG4gIH07XG4gIElucHV0LnByb3RvdHlwZS5ibHVyID0gZnVuY3Rpb24gKCkge1xuICAgIGlmICh0aGlzLmlzRm9jdXNzZWQpIHtcbiAgICAgIHRoaXMuZWxlbWVudC5ibHVyKCk7XG4gICAgfVxuICB9O1xuICBJbnB1dC5wcm90b3R5cGUuY2xlYXIgPSBmdW5jdGlvbiAoc2V0V2lkdGgpIHtcbiAgICBpZiAoc2V0V2lkdGggPT09IHZvaWQgMCkge1xuICAgICAgc2V0V2lkdGggPSB0cnVlO1xuICAgIH1cbiAgICBpZiAodGhpcy5lbGVtZW50LnZhbHVlKSB7XG4gICAgICB0aGlzLmVsZW1lbnQudmFsdWUgPSAnJztcbiAgICB9XG4gICAgaWYgKHNldFdpZHRoKSB7XG4gICAgICB0aGlzLnNldFdpZHRoKCk7XG4gICAgfVxuICAgIHJldHVybiB0aGlzO1xuICB9O1xuICAvKipcbiAgICogU2V0IHRoZSBjb3JyZWN0IGlucHV0IHdpZHRoIGJhc2VkIG9uIHBsYWNlaG9sZGVyXG4gICAqIHZhbHVlIG9yIGlucHV0IHZhbHVlXG4gICAqL1xuICBJbnB1dC5wcm90b3R5cGUuc2V0V2lkdGggPSBmdW5jdGlvbiAoKSB7XG4gICAgLy8gUmVzaXplIGlucHV0IHRvIGNvbnRlbnRzIG9yIHBsYWNlaG9sZGVyXG4gICAgdmFyIF9hID0gdGhpcy5lbGVtZW50LFxuICAgICAgc3R5bGUgPSBfYS5zdHlsZSxcbiAgICAgIHZhbHVlID0gX2EudmFsdWUsXG4gICAgICBwbGFjZWhvbGRlciA9IF9hLnBsYWNlaG9sZGVyO1xuICAgIHN0eWxlLm1pbldpZHRoID0gXCJcIi5jb25jYXQocGxhY2Vob2xkZXIubGVuZ3RoICsgMSwgXCJjaFwiKTtcbiAgICBzdHlsZS53aWR0aCA9IFwiXCIuY29uY2F0KHZhbHVlLmxlbmd0aCArIDEsIFwiY2hcIik7XG4gIH07XG4gIElucHV0LnByb3RvdHlwZS5zZXRBY3RpdmVEZXNjZW5kYW50ID0gZnVuY3Rpb24gKGFjdGl2ZURlc2NlbmRhbnRJRCkge1xuICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ2FyaWEtYWN0aXZlZGVzY2VuZGFudCcsIGFjdGl2ZURlc2NlbmRhbnRJRCk7XG4gIH07XG4gIElucHV0LnByb3RvdHlwZS5yZW1vdmVBY3RpdmVEZXNjZW5kYW50ID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUoJ2FyaWEtYWN0aXZlZGVzY2VuZGFudCcpO1xuICB9O1xuICBJbnB1dC5wcm90b3R5cGUuX29uSW5wdXQgPSBmdW5jdGlvbiAoKSB7XG4gICAgaWYgKHRoaXMudHlwZSAhPT0gY29uc3RhbnRzXzEuU0VMRUNUX09ORV9UWVBFKSB7XG4gICAgICB0aGlzLnNldFdpZHRoKCk7XG4gICAgfVxuICB9O1xuICBJbnB1dC5wcm90b3R5cGUuX29uUGFzdGUgPSBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICBpZiAodGhpcy5wcmV2ZW50UGFzdGUpIHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgfVxuICB9O1xuICBJbnB1dC5wcm90b3R5cGUuX29uRm9jdXMgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5pc0ZvY3Vzc2VkID0gdHJ1ZTtcbiAgfTtcbiAgSW5wdXQucHJvdG90eXBlLl9vbkJsdXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5pc0ZvY3Vzc2VkID0gZmFsc2U7XG4gIH07XG4gIHJldHVybiBJbnB1dDtcbn0oKTtcbmV4cG9ydHNbXCJkZWZhdWx0XCJdID0gSW5wdXQ7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA2MjQ6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbnZhciBjb25zdGFudHNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oODgzKTtcbnZhciBMaXN0ID0gLyoqIEBjbGFzcyAqL2Z1bmN0aW9uICgpIHtcbiAgZnVuY3Rpb24gTGlzdChfYSkge1xuICAgIHZhciBlbGVtZW50ID0gX2EuZWxlbWVudDtcbiAgICB0aGlzLmVsZW1lbnQgPSBlbGVtZW50O1xuICAgIHRoaXMuc2Nyb2xsUG9zID0gdGhpcy5lbGVtZW50LnNjcm9sbFRvcDtcbiAgICB0aGlzLmhlaWdodCA9IHRoaXMuZWxlbWVudC5vZmZzZXRIZWlnaHQ7XG4gIH1cbiAgTGlzdC5wcm90b3R5cGUuY2xlYXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LmlubmVySFRNTCA9ICcnO1xuICB9O1xuICBMaXN0LnByb3RvdHlwZS5hcHBlbmQgPSBmdW5jdGlvbiAobm9kZSkge1xuICAgIHRoaXMuZWxlbWVudC5hcHBlbmRDaGlsZChub2RlKTtcbiAgfTtcbiAgTGlzdC5wcm90b3R5cGUuZ2V0Q2hpbGQgPSBmdW5jdGlvbiAoc2VsZWN0b3IpIHtcbiAgICByZXR1cm4gdGhpcy5lbGVtZW50LnF1ZXJ5U2VsZWN0b3Ioc2VsZWN0b3IpO1xuICB9O1xuICBMaXN0LnByb3RvdHlwZS5oYXNDaGlsZHJlbiA9IGZ1bmN0aW9uICgpIHtcbiAgICByZXR1cm4gdGhpcy5lbGVtZW50Lmhhc0NoaWxkTm9kZXMoKTtcbiAgfTtcbiAgTGlzdC5wcm90b3R5cGUuc2Nyb2xsVG9Ub3AgPSBmdW5jdGlvbiAoKSB7XG4gICAgdGhpcy5lbGVtZW50LnNjcm9sbFRvcCA9IDA7XG4gIH07XG4gIExpc3QucHJvdG90eXBlLnNjcm9sbFRvQ2hpbGRFbGVtZW50ID0gZnVuY3Rpb24gKGVsZW1lbnQsIGRpcmVjdGlvbikge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgaWYgKCFlbGVtZW50KSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHZhciBsaXN0SGVpZ2h0ID0gdGhpcy5lbGVtZW50Lm9mZnNldEhlaWdodDtcbiAgICAvLyBTY3JvbGwgcG9zaXRpb24gb2YgZHJvcGRvd25cbiAgICB2YXIgbGlzdFNjcm9sbFBvc2l0aW9uID0gdGhpcy5lbGVtZW50LnNjcm9sbFRvcCArIGxpc3RIZWlnaHQ7XG4gICAgdmFyIGVsZW1lbnRIZWlnaHQgPSBlbGVtZW50Lm9mZnNldEhlaWdodDtcbiAgICAvLyBEaXN0YW5jZSBmcm9tIGJvdHRvbSBvZiBlbGVtZW50IHRvIHRvcCBvZiBwYXJlbnRcbiAgICB2YXIgZWxlbWVudFBvcyA9IGVsZW1lbnQub2Zmc2V0VG9wICsgZWxlbWVudEhlaWdodDtcbiAgICAvLyBEaWZmZXJlbmNlIGJldHdlZW4gdGhlIGVsZW1lbnQgYW5kIHNjcm9sbCBwb3NpdGlvblxuICAgIHZhciBkZXN0aW5hdGlvbiA9IGRpcmVjdGlvbiA+IDAgPyB0aGlzLmVsZW1lbnQuc2Nyb2xsVG9wICsgZWxlbWVudFBvcyAtIGxpc3RTY3JvbGxQb3NpdGlvbiA6IGVsZW1lbnQub2Zmc2V0VG9wO1xuICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZShmdW5jdGlvbiAoKSB7XG4gICAgICBfdGhpcy5fYW5pbWF0ZVNjcm9sbChkZXN0aW5hdGlvbiwgZGlyZWN0aW9uKTtcbiAgICB9KTtcbiAgfTtcbiAgTGlzdC5wcm90b3R5cGUuX3Njcm9sbERvd24gPSBmdW5jdGlvbiAoc2Nyb2xsUG9zLCBzdHJlbmd0aCwgZGVzdGluYXRpb24pIHtcbiAgICB2YXIgZWFzaW5nID0gKGRlc3RpbmF0aW9uIC0gc2Nyb2xsUG9zKSAvIHN0cmVuZ3RoO1xuICAgIHZhciBkaXN0YW5jZSA9IGVhc2luZyA+IDEgPyBlYXNpbmcgOiAxO1xuICAgIHRoaXMuZWxlbWVudC5zY3JvbGxUb3AgPSBzY3JvbGxQb3MgKyBkaXN0YW5jZTtcbiAgfTtcbiAgTGlzdC5wcm90b3R5cGUuX3Njcm9sbFVwID0gZnVuY3Rpb24gKHNjcm9sbFBvcywgc3RyZW5ndGgsIGRlc3RpbmF0aW9uKSB7XG4gICAgdmFyIGVhc2luZyA9IChzY3JvbGxQb3MgLSBkZXN0aW5hdGlvbikgLyBzdHJlbmd0aDtcbiAgICB2YXIgZGlzdGFuY2UgPSBlYXNpbmcgPiAxID8gZWFzaW5nIDogMTtcbiAgICB0aGlzLmVsZW1lbnQuc2Nyb2xsVG9wID0gc2Nyb2xsUG9zIC0gZGlzdGFuY2U7XG4gIH07XG4gIExpc3QucHJvdG90eXBlLl9hbmltYXRlU2Nyb2xsID0gZnVuY3Rpb24gKGRlc3RpbmF0aW9uLCBkaXJlY3Rpb24pIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgIHZhciBzdHJlbmd0aCA9IGNvbnN0YW50c18xLlNDUk9MTElOR19TUEVFRDtcbiAgICB2YXIgY2hvaWNlTGlzdFNjcm9sbFRvcCA9IHRoaXMuZWxlbWVudC5zY3JvbGxUb3A7XG4gICAgdmFyIGNvbnRpbnVlQW5pbWF0aW9uID0gZmFsc2U7XG4gICAgaWYgKGRpcmVjdGlvbiA+IDApIHtcbiAgICAgIHRoaXMuX3Njcm9sbERvd24oY2hvaWNlTGlzdFNjcm9sbFRvcCwgc3RyZW5ndGgsIGRlc3RpbmF0aW9uKTtcbiAgICAgIGlmIChjaG9pY2VMaXN0U2Nyb2xsVG9wIDwgZGVzdGluYXRpb24pIHtcbiAgICAgICAgY29udGludWVBbmltYXRpb24gPSB0cnVlO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLl9zY3JvbGxVcChjaG9pY2VMaXN0U2Nyb2xsVG9wLCBzdHJlbmd0aCwgZGVzdGluYXRpb24pO1xuICAgICAgaWYgKGNob2ljZUxpc3RTY3JvbGxUb3AgPiBkZXN0aW5hdGlvbikge1xuICAgICAgICBjb250aW51ZUFuaW1hdGlvbiA9IHRydWU7XG4gICAgICB9XG4gICAgfVxuICAgIGlmIChjb250aW51ZUFuaW1hdGlvbikge1xuICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgX3RoaXMuX2FuaW1hdGVTY3JvbGwoZGVzdGluYXRpb24sIGRpcmVjdGlvbik7XG4gICAgICB9KTtcbiAgICB9XG4gIH07XG4gIHJldHVybiBMaXN0O1xufSgpO1xuZXhwb3J0c1tcImRlZmF1bHRcIl0gPSBMaXN0O1xuXG4vKioqLyB9KSxcblxuLyoqKi8gNzMwOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG52YXIgdXRpbHNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oNzk5KTtcbnZhciBXcmFwcGVkRWxlbWVudCA9IC8qKiBAY2xhc3MgKi9mdW5jdGlvbiAoKSB7XG4gIGZ1bmN0aW9uIFdyYXBwZWRFbGVtZW50KF9hKSB7XG4gICAgdmFyIGVsZW1lbnQgPSBfYS5lbGVtZW50LFxuICAgICAgY2xhc3NOYW1lcyA9IF9hLmNsYXNzTmFtZXM7XG4gICAgdGhpcy5lbGVtZW50ID0gZWxlbWVudDtcbiAgICB0aGlzLmNsYXNzTmFtZXMgPSBjbGFzc05hbWVzO1xuICAgIGlmICghKGVsZW1lbnQgaW5zdGFuY2VvZiBIVE1MSW5wdXRFbGVtZW50KSAmJiAhKGVsZW1lbnQgaW5zdGFuY2VvZiBIVE1MU2VsZWN0RWxlbWVudCkpIHtcbiAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ0ludmFsaWQgZWxlbWVudCBwYXNzZWQnKTtcbiAgICB9XG4gICAgdGhpcy5pc0Rpc2FibGVkID0gZmFsc2U7XG4gIH1cbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KFdyYXBwZWRFbGVtZW50LnByb3RvdHlwZSwgXCJpc0FjdGl2ZVwiLCB7XG4gICAgZ2V0OiBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gdGhpcy5lbGVtZW50LmRhdGFzZXQuY2hvaWNlID09PSAnYWN0aXZlJztcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KFdyYXBwZWRFbGVtZW50LnByb3RvdHlwZSwgXCJkaXJcIiwge1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHRoaXMuZWxlbWVudC5kaXI7XG4gICAgfSxcbiAgICBlbnVtZXJhYmxlOiBmYWxzZSxcbiAgICBjb25maWd1cmFibGU6IHRydWVcbiAgfSk7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShXcmFwcGVkRWxlbWVudC5wcm90b3R5cGUsIFwidmFsdWVcIiwge1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHRoaXMuZWxlbWVudC52YWx1ZTtcbiAgICB9LFxuICAgIHNldDogZnVuY3Rpb24gKHZhbHVlKSB7XG4gICAgICAvLyB5b3UgbXVzdCBkZWZpbmUgc2V0dGVyIGhlcmUgb3RoZXJ3aXNlIGl0IHdpbGwgYmUgcmVhZG9ubHkgcHJvcGVydHlcbiAgICAgIHRoaXMuZWxlbWVudC52YWx1ZSA9IHZhbHVlO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBXcmFwcGVkRWxlbWVudC5wcm90b3R5cGUuY29uY2VhbCA9IGZ1bmN0aW9uICgpIHtcbiAgICAvLyBIaWRlIHBhc3NlZCBpbnB1dFxuICAgIHRoaXMuZWxlbWVudC5jbGFzc0xpc3QuYWRkKHRoaXMuY2xhc3NOYW1lcy5pbnB1dCk7XG4gICAgdGhpcy5lbGVtZW50LmhpZGRlbiA9IHRydWU7XG4gICAgLy8gUmVtb3ZlIGVsZW1lbnQgZnJvbSB0YWIgaW5kZXhcbiAgICB0aGlzLmVsZW1lbnQudGFiSW5kZXggPSAtMTtcbiAgICAvLyBCYWNrdXAgb3JpZ2luYWwgc3R5bGVzIGlmIGFueVxuICAgIHZhciBvcmlnU3R5bGUgPSB0aGlzLmVsZW1lbnQuZ2V0QXR0cmlidXRlKCdzdHlsZScpO1xuICAgIGlmIChvcmlnU3R5bGUpIHtcbiAgICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ2RhdGEtY2hvaWNlLW9yaWctc3R5bGUnLCBvcmlnU3R5bGUpO1xuICAgIH1cbiAgICB0aGlzLmVsZW1lbnQuc2V0QXR0cmlidXRlKCdkYXRhLWNob2ljZScsICdhY3RpdmUnKTtcbiAgfTtcbiAgV3JhcHBlZEVsZW1lbnQucHJvdG90eXBlLnJldmVhbCA9IGZ1bmN0aW9uICgpIHtcbiAgICAvLyBSZWluc3RhdGUgcGFzc2VkIGVsZW1lbnRcbiAgICB0aGlzLmVsZW1lbnQuY2xhc3NMaXN0LnJlbW92ZSh0aGlzLmNsYXNzTmFtZXMuaW5wdXQpO1xuICAgIHRoaXMuZWxlbWVudC5oaWRkZW4gPSBmYWxzZTtcbiAgICB0aGlzLmVsZW1lbnQucmVtb3ZlQXR0cmlidXRlKCd0YWJpbmRleCcpO1xuICAgIC8vIFJlY292ZXIgb3JpZ2luYWwgc3R5bGVzIGlmIGFueVxuICAgIHZhciBvcmlnU3R5bGUgPSB0aGlzLmVsZW1lbnQuZ2V0QXR0cmlidXRlKCdkYXRhLWNob2ljZS1vcmlnLXN0eWxlJyk7XG4gICAgaWYgKG9yaWdTdHlsZSkge1xuICAgICAgdGhpcy5lbGVtZW50LnJlbW92ZUF0dHJpYnV0ZSgnZGF0YS1jaG9pY2Utb3JpZy1zdHlsZScpO1xuICAgICAgdGhpcy5lbGVtZW50LnNldEF0dHJpYnV0ZSgnc3R5bGUnLCBvcmlnU3R5bGUpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLmVsZW1lbnQucmVtb3ZlQXR0cmlidXRlKCdzdHlsZScpO1xuICAgIH1cbiAgICB0aGlzLmVsZW1lbnQucmVtb3ZlQXR0cmlidXRlKCdkYXRhLWNob2ljZScpO1xuICAgIC8vIFJlLWFzc2lnbiB2YWx1ZXMgLSB0aGlzIGlzIHdlaXJkLCBJIGtub3dcbiAgICAvLyBAdG9kbyBGaWd1cmUgb3V0IHdoeSB3ZSBuZWVkIHRvIGRvIHRoaXNcbiAgICB0aGlzLmVsZW1lbnQudmFsdWUgPSB0aGlzLmVsZW1lbnQudmFsdWU7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tc2VsZi1hc3NpZ25cbiAgfTtcblxuICBXcmFwcGVkRWxlbWVudC5wcm90b3R5cGUuZW5hYmxlID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUoJ2Rpc2FibGVkJyk7XG4gICAgdGhpcy5lbGVtZW50LmRpc2FibGVkID0gZmFsc2U7XG4gICAgdGhpcy5pc0Rpc2FibGVkID0gZmFsc2U7XG4gIH07XG4gIFdyYXBwZWRFbGVtZW50LnByb3RvdHlwZS5kaXNhYmxlID0gZnVuY3Rpb24gKCkge1xuICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ2Rpc2FibGVkJywgJycpO1xuICAgIHRoaXMuZWxlbWVudC5kaXNhYmxlZCA9IHRydWU7XG4gICAgdGhpcy5pc0Rpc2FibGVkID0gdHJ1ZTtcbiAgfTtcbiAgV3JhcHBlZEVsZW1lbnQucHJvdG90eXBlLnRyaWdnZXJFdmVudCA9IGZ1bmN0aW9uIChldmVudFR5cGUsIGRhdGEpIHtcbiAgICAoMCwgdXRpbHNfMS5kaXNwYXRjaEV2ZW50KSh0aGlzLmVsZW1lbnQsIGV2ZW50VHlwZSwgZGF0YSk7XG4gIH07XG4gIHJldHVybiBXcmFwcGVkRWxlbWVudDtcbn0oKTtcbmV4cG9ydHNbXCJkZWZhdWx0XCJdID0gV3JhcHBlZEVsZW1lbnQ7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA1NDE6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuXG5cbnZhciBfX2V4dGVuZHMgPSB0aGlzICYmIHRoaXMuX19leHRlbmRzIHx8IGZ1bmN0aW9uICgpIHtcbiAgdmFyIGV4dGVuZFN0YXRpY3MgPSBmdW5jdGlvbiAoZCwgYikge1xuICAgIGV4dGVuZFN0YXRpY3MgPSBPYmplY3Quc2V0UHJvdG90eXBlT2YgfHwge1xuICAgICAgX19wcm90b19fOiBbXVxuICAgIH0gaW5zdGFuY2VvZiBBcnJheSAmJiBmdW5jdGlvbiAoZCwgYikge1xuICAgICAgZC5fX3Byb3RvX18gPSBiO1xuICAgIH0gfHwgZnVuY3Rpb24gKGQsIGIpIHtcbiAgICAgIGZvciAodmFyIHAgaW4gYikgaWYgKE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChiLCBwKSkgZFtwXSA9IGJbcF07XG4gICAgfTtcbiAgICByZXR1cm4gZXh0ZW5kU3RhdGljcyhkLCBiKTtcbiAgfTtcbiAgcmV0dXJuIGZ1bmN0aW9uIChkLCBiKSB7XG4gICAgaWYgKHR5cGVvZiBiICE9PSBcImZ1bmN0aW9uXCIgJiYgYiAhPT0gbnVsbCkgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNsYXNzIGV4dGVuZHMgdmFsdWUgXCIgKyBTdHJpbmcoYikgKyBcIiBpcyBub3QgYSBjb25zdHJ1Y3RvciBvciBudWxsXCIpO1xuICAgIGV4dGVuZFN0YXRpY3MoZCwgYik7XG4gICAgZnVuY3Rpb24gX18oKSB7XG4gICAgICB0aGlzLmNvbnN0cnVjdG9yID0gZDtcbiAgICB9XG4gICAgZC5wcm90b3R5cGUgPSBiID09PSBudWxsID8gT2JqZWN0LmNyZWF0ZShiKSA6IChfXy5wcm90b3R5cGUgPSBiLnByb3RvdHlwZSwgbmV3IF9fKCkpO1xuICB9O1xufSgpO1xudmFyIF9faW1wb3J0RGVmYXVsdCA9IHRoaXMgJiYgdGhpcy5fX2ltcG9ydERlZmF1bHQgfHwgZnVuY3Rpb24gKG1vZCkge1xuICByZXR1cm4gbW9kICYmIG1vZC5fX2VzTW9kdWxlID8gbW9kIDoge1xuICAgIFwiZGVmYXVsdFwiOiBtb2RcbiAgfTtcbn07XG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG52YXIgd3JhcHBlZF9lbGVtZW50XzEgPSBfX2ltcG9ydERlZmF1bHQoX193ZWJwYWNrX3JlcXVpcmVfXyg3MzApKTtcbnZhciBXcmFwcGVkSW5wdXQgPSAvKiogQGNsYXNzICovZnVuY3Rpb24gKF9zdXBlcikge1xuICBfX2V4dGVuZHMoV3JhcHBlZElucHV0LCBfc3VwZXIpO1xuICBmdW5jdGlvbiBXcmFwcGVkSW5wdXQoX2EpIHtcbiAgICB2YXIgZWxlbWVudCA9IF9hLmVsZW1lbnQsXG4gICAgICBjbGFzc05hbWVzID0gX2EuY2xhc3NOYW1lcyxcbiAgICAgIGRlbGltaXRlciA9IF9hLmRlbGltaXRlcjtcbiAgICB2YXIgX3RoaXMgPSBfc3VwZXIuY2FsbCh0aGlzLCB7XG4gICAgICBlbGVtZW50OiBlbGVtZW50LFxuICAgICAgY2xhc3NOYW1lczogY2xhc3NOYW1lc1xuICAgIH0pIHx8IHRoaXM7XG4gICAgX3RoaXMuZGVsaW1pdGVyID0gZGVsaW1pdGVyO1xuICAgIHJldHVybiBfdGhpcztcbiAgfVxuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoV3JhcHBlZElucHV0LnByb3RvdHlwZSwgXCJ2YWx1ZVwiLCB7XG4gICAgZ2V0OiBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gdGhpcy5lbGVtZW50LnZhbHVlO1xuICAgIH0sXG4gICAgc2V0OiBmdW5jdGlvbiAodmFsdWUpIHtcbiAgICAgIHRoaXMuZWxlbWVudC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgdmFsdWUpO1xuICAgICAgdGhpcy5lbGVtZW50LnZhbHVlID0gdmFsdWU7XG4gICAgfSxcbiAgICBlbnVtZXJhYmxlOiBmYWxzZSxcbiAgICBjb25maWd1cmFibGU6IHRydWVcbiAgfSk7XG4gIHJldHVybiBXcmFwcGVkSW5wdXQ7XG59KHdyYXBwZWRfZWxlbWVudF8xLmRlZmF1bHQpO1xuZXhwb3J0c1tcImRlZmF1bHRcIl0gPSBXcmFwcGVkSW5wdXQ7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA5ODI6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuXG5cbnZhciBfX2V4dGVuZHMgPSB0aGlzICYmIHRoaXMuX19leHRlbmRzIHx8IGZ1bmN0aW9uICgpIHtcbiAgdmFyIGV4dGVuZFN0YXRpY3MgPSBmdW5jdGlvbiAoZCwgYikge1xuICAgIGV4dGVuZFN0YXRpY3MgPSBPYmplY3Quc2V0UHJvdG90eXBlT2YgfHwge1xuICAgICAgX19wcm90b19fOiBbXVxuICAgIH0gaW5zdGFuY2VvZiBBcnJheSAmJiBmdW5jdGlvbiAoZCwgYikge1xuICAgICAgZC5fX3Byb3RvX18gPSBiO1xuICAgIH0gfHwgZnVuY3Rpb24gKGQsIGIpIHtcbiAgICAgIGZvciAodmFyIHAgaW4gYikgaWYgKE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChiLCBwKSkgZFtwXSA9IGJbcF07XG4gICAgfTtcbiAgICByZXR1cm4gZXh0ZW5kU3RhdGljcyhkLCBiKTtcbiAgfTtcbiAgcmV0dXJuIGZ1bmN0aW9uIChkLCBiKSB7XG4gICAgaWYgKHR5cGVvZiBiICE9PSBcImZ1bmN0aW9uXCIgJiYgYiAhPT0gbnVsbCkgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNsYXNzIGV4dGVuZHMgdmFsdWUgXCIgKyBTdHJpbmcoYikgKyBcIiBpcyBub3QgYSBjb25zdHJ1Y3RvciBvciBudWxsXCIpO1xuICAgIGV4dGVuZFN0YXRpY3MoZCwgYik7XG4gICAgZnVuY3Rpb24gX18oKSB7XG4gICAgICB0aGlzLmNvbnN0cnVjdG9yID0gZDtcbiAgICB9XG4gICAgZC5wcm90b3R5cGUgPSBiID09PSBudWxsID8gT2JqZWN0LmNyZWF0ZShiKSA6IChfXy5wcm90b3R5cGUgPSBiLnByb3RvdHlwZSwgbmV3IF9fKCkpO1xuICB9O1xufSgpO1xudmFyIF9faW1wb3J0RGVmYXVsdCA9IHRoaXMgJiYgdGhpcy5fX2ltcG9ydERlZmF1bHQgfHwgZnVuY3Rpb24gKG1vZCkge1xuICByZXR1cm4gbW9kICYmIG1vZC5fX2VzTW9kdWxlID8gbW9kIDoge1xuICAgIFwiZGVmYXVsdFwiOiBtb2RcbiAgfTtcbn07XG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG52YXIgd3JhcHBlZF9lbGVtZW50XzEgPSBfX2ltcG9ydERlZmF1bHQoX193ZWJwYWNrX3JlcXVpcmVfXyg3MzApKTtcbnZhciBXcmFwcGVkU2VsZWN0ID0gLyoqIEBjbGFzcyAqL2Z1bmN0aW9uIChfc3VwZXIpIHtcbiAgX19leHRlbmRzKFdyYXBwZWRTZWxlY3QsIF9zdXBlcik7XG4gIGZ1bmN0aW9uIFdyYXBwZWRTZWxlY3QoX2EpIHtcbiAgICB2YXIgZWxlbWVudCA9IF9hLmVsZW1lbnQsXG4gICAgICBjbGFzc05hbWVzID0gX2EuY2xhc3NOYW1lcyxcbiAgICAgIHRlbXBsYXRlID0gX2EudGVtcGxhdGU7XG4gICAgdmFyIF90aGlzID0gX3N1cGVyLmNhbGwodGhpcywge1xuICAgICAgZWxlbWVudDogZWxlbWVudCxcbiAgICAgIGNsYXNzTmFtZXM6IGNsYXNzTmFtZXNcbiAgICB9KSB8fCB0aGlzO1xuICAgIF90aGlzLnRlbXBsYXRlID0gdGVtcGxhdGU7XG4gICAgcmV0dXJuIF90aGlzO1xuICB9XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShXcmFwcGVkU2VsZWN0LnByb3RvdHlwZSwgXCJwbGFjZWhvbGRlck9wdGlvblwiLCB7XG4gICAgZ2V0OiBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gdGhpcy5lbGVtZW50LnF1ZXJ5U2VsZWN0b3IoJ29wdGlvblt2YWx1ZT1cIlwiXScpIHx8XG4gICAgICAvLyBCYWNrd2FyZCBjb21wYXRpYmlsaXR5IGxheWVyIGZvciB0aGUgbm9uLXN0YW5kYXJkIHBsYWNlaG9sZGVyIGF0dHJpYnV0ZSBzdXBwb3J0ZWQgaW4gb2xkZXIgdmVyc2lvbnMuXG4gICAgICB0aGlzLmVsZW1lbnQucXVlcnlTZWxlY3Rvcignb3B0aW9uW3BsYWNlaG9sZGVyXScpO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoV3JhcHBlZFNlbGVjdC5wcm90b3R5cGUsIFwib3B0aW9uR3JvdXBzXCIsIHtcbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBBcnJheS5mcm9tKHRoaXMuZWxlbWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnT1BUR1JPVVAnKSk7XG4gICAgfSxcbiAgICBlbnVtZXJhYmxlOiBmYWxzZSxcbiAgICBjb25maWd1cmFibGU6IHRydWVcbiAgfSk7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShXcmFwcGVkU2VsZWN0LnByb3RvdHlwZSwgXCJvcHRpb25zXCIsIHtcbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBBcnJheS5mcm9tKHRoaXMuZWxlbWVudC5vcHRpb25zKTtcbiAgICB9LFxuICAgIHNldDogZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICB2YXIgZnJhZ21lbnQgPSBkb2N1bWVudC5jcmVhdGVEb2N1bWVudEZyYWdtZW50KCk7XG4gICAgICB2YXIgYWRkT3B0aW9uVG9GcmFnbWVudCA9IGZ1bmN0aW9uIChkYXRhKSB7XG4gICAgICAgIC8vIENyZWF0ZSBhIHN0YW5kYXJkIHNlbGVjdCBvcHRpb25cbiAgICAgICAgdmFyIG9wdGlvbiA9IF90aGlzLnRlbXBsYXRlKGRhdGEpO1xuICAgICAgICAvLyBBcHBlbmQgaXQgdG8gZnJhZ21lbnRcbiAgICAgICAgZnJhZ21lbnQuYXBwZW5kQ2hpbGQob3B0aW9uKTtcbiAgICAgIH07XG4gICAgICAvLyBBZGQgZWFjaCBsaXN0IGl0ZW0gdG8gbGlzdFxuICAgICAgb3B0aW9ucy5mb3JFYWNoKGZ1bmN0aW9uIChvcHRpb25EYXRhKSB7XG4gICAgICAgIHJldHVybiBhZGRPcHRpb25Ub0ZyYWdtZW50KG9wdGlvbkRhdGEpO1xuICAgICAgfSk7XG4gICAgICB0aGlzLmFwcGVuZERvY0ZyYWdtZW50KGZyYWdtZW50KTtcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgV3JhcHBlZFNlbGVjdC5wcm90b3R5cGUuYXBwZW5kRG9jRnJhZ21lbnQgPSBmdW5jdGlvbiAoZnJhZ21lbnQpIHtcbiAgICB0aGlzLmVsZW1lbnQuaW5uZXJIVE1MID0gJyc7XG4gICAgdGhpcy5lbGVtZW50LmFwcGVuZENoaWxkKGZyYWdtZW50KTtcbiAgfTtcbiAgcmV0dXJuIFdyYXBwZWRTZWxlY3Q7XG59KHdyYXBwZWRfZWxlbWVudF8xLmRlZmF1bHQpO1xuZXhwb3J0c1tcImRlZmF1bHRcIl0gPSBXcmFwcGVkU2VsZWN0O1xuXG4vKioqLyB9KSxcblxuLyoqKi8gODgzOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5leHBvcnRzLlNDUk9MTElOR19TUEVFRCA9IGV4cG9ydHMuU0VMRUNUX01VTFRJUExFX1RZUEUgPSBleHBvcnRzLlNFTEVDVF9PTkVfVFlQRSA9IGV4cG9ydHMuVEVYVF9UWVBFID0gZXhwb3J0cy5LRVlfQ09ERVMgPSBleHBvcnRzLkFDVElPTl9UWVBFUyA9IGV4cG9ydHMuRVZFTlRTID0gdm9pZCAwO1xuZXhwb3J0cy5FVkVOVFMgPSB7XG4gIHNob3dEcm9wZG93bjogJ3Nob3dEcm9wZG93bicsXG4gIGhpZGVEcm9wZG93bjogJ2hpZGVEcm9wZG93bicsXG4gIGNoYW5nZTogJ2NoYW5nZScsXG4gIGNob2ljZTogJ2Nob2ljZScsXG4gIHNlYXJjaDogJ3NlYXJjaCcsXG4gIGFkZEl0ZW06ICdhZGRJdGVtJyxcbiAgcmVtb3ZlSXRlbTogJ3JlbW92ZUl0ZW0nLFxuICBoaWdobGlnaHRJdGVtOiAnaGlnaGxpZ2h0SXRlbScsXG4gIGhpZ2hsaWdodENob2ljZTogJ2hpZ2hsaWdodENob2ljZScsXG4gIHVuaGlnaGxpZ2h0SXRlbTogJ3VuaGlnaGxpZ2h0SXRlbSdcbn07XG5leHBvcnRzLkFDVElPTl9UWVBFUyA9IHtcbiAgQUREX0NIT0lDRTogJ0FERF9DSE9JQ0UnLFxuICBGSUxURVJfQ0hPSUNFUzogJ0ZJTFRFUl9DSE9JQ0VTJyxcbiAgQUNUSVZBVEVfQ0hPSUNFUzogJ0FDVElWQVRFX0NIT0lDRVMnLFxuICBDTEVBUl9DSE9JQ0VTOiAnQ0xFQVJfQ0hPSUNFUycsXG4gIEFERF9HUk9VUDogJ0FERF9HUk9VUCcsXG4gIEFERF9JVEVNOiAnQUREX0lURU0nLFxuICBSRU1PVkVfSVRFTTogJ1JFTU9WRV9JVEVNJyxcbiAgSElHSExJR0hUX0lURU06ICdISUdITElHSFRfSVRFTScsXG4gIENMRUFSX0FMTDogJ0NMRUFSX0FMTCcsXG4gIFJFU0VUX1RPOiAnUkVTRVRfVE8nLFxuICBTRVRfSVNfTE9BRElORzogJ1NFVF9JU19MT0FESU5HJ1xufTtcbmV4cG9ydHMuS0VZX0NPREVTID0ge1xuICBCQUNLX0tFWTogNDYsXG4gIERFTEVURV9LRVk6IDgsXG4gIEVOVEVSX0tFWTogMTMsXG4gIEFfS0VZOiA2NSxcbiAgRVNDX0tFWTogMjcsXG4gIFVQX0tFWTogMzgsXG4gIERPV05fS0VZOiA0MCxcbiAgUEFHRV9VUF9LRVk6IDMzLFxuICBQQUdFX0RPV05fS0VZOiAzNFxufTtcbmV4cG9ydHMuVEVYVF9UWVBFID0gJ3RleHQnO1xuZXhwb3J0cy5TRUxFQ1RfT05FX1RZUEUgPSAnc2VsZWN0LW9uZSc7XG5leHBvcnRzLlNFTEVDVF9NVUxUSVBMRV9UWVBFID0gJ3NlbGVjdC1tdWx0aXBsZSc7XG5leHBvcnRzLlNDUk9MTElOR19TUEVFRCA9IDQ7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA3ODk6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbmV4cG9ydHMuREVGQVVMVF9DT05GSUcgPSBleHBvcnRzLkRFRkFVTFRfQ0xBU1NOQU1FUyA9IHZvaWQgMDtcbnZhciB1dGlsc18xID0gX193ZWJwYWNrX3JlcXVpcmVfXyg3OTkpO1xuZXhwb3J0cy5ERUZBVUxUX0NMQVNTTkFNRVMgPSB7XG4gIGNvbnRhaW5lck91dGVyOiAnY2hvaWNlcycsXG4gIGNvbnRhaW5lcklubmVyOiAnY2hvaWNlc19faW5uZXInLFxuICBpbnB1dDogJ2Nob2ljZXNfX2lucHV0JyxcbiAgaW5wdXRDbG9uZWQ6ICdjaG9pY2VzX19pbnB1dC0tY2xvbmVkJyxcbiAgbGlzdDogJ2Nob2ljZXNfX2xpc3QnLFxuICBsaXN0SXRlbXM6ICdjaG9pY2VzX19saXN0LS1tdWx0aXBsZScsXG4gIGxpc3RTaW5nbGU6ICdjaG9pY2VzX19saXN0LS1zaW5nbGUnLFxuICBsaXN0RHJvcGRvd246ICdjaG9pY2VzX19saXN0LS1kcm9wZG93bicsXG4gIGl0ZW06ICdjaG9pY2VzX19pdGVtJyxcbiAgaXRlbVNlbGVjdGFibGU6ICdjaG9pY2VzX19pdGVtLS1zZWxlY3RhYmxlJyxcbiAgaXRlbURpc2FibGVkOiAnY2hvaWNlc19faXRlbS0tZGlzYWJsZWQnLFxuICBpdGVtQ2hvaWNlOiAnY2hvaWNlc19faXRlbS0tY2hvaWNlJyxcbiAgcGxhY2Vob2xkZXI6ICdjaG9pY2VzX19wbGFjZWhvbGRlcicsXG4gIGdyb3VwOiAnY2hvaWNlc19fZ3JvdXAnLFxuICBncm91cEhlYWRpbmc6ICdjaG9pY2VzX19oZWFkaW5nJyxcbiAgYnV0dG9uOiAnY2hvaWNlc19fYnV0dG9uJyxcbiAgYWN0aXZlU3RhdGU6ICdpcy1hY3RpdmUnLFxuICBmb2N1c1N0YXRlOiAnaXMtZm9jdXNlZCcsXG4gIG9wZW5TdGF0ZTogJ2lzLW9wZW4nLFxuICBkaXNhYmxlZFN0YXRlOiAnaXMtZGlzYWJsZWQnLFxuICBoaWdobGlnaHRlZFN0YXRlOiAnaXMtaGlnaGxpZ2h0ZWQnLFxuICBzZWxlY3RlZFN0YXRlOiAnaXMtc2VsZWN0ZWQnLFxuICBmbGlwcGVkU3RhdGU6ICdpcy1mbGlwcGVkJyxcbiAgbG9hZGluZ1N0YXRlOiAnaXMtbG9hZGluZycsXG4gIG5vUmVzdWx0czogJ2hhcy1uby1yZXN1bHRzJyxcbiAgbm9DaG9pY2VzOiAnaGFzLW5vLWNob2ljZXMnXG59O1xuZXhwb3J0cy5ERUZBVUxUX0NPTkZJRyA9IHtcbiAgaXRlbXM6IFtdLFxuICBjaG9pY2VzOiBbXSxcbiAgc2lsZW50OiBmYWxzZSxcbiAgcmVuZGVyQ2hvaWNlTGltaXQ6IC0xLFxuICBtYXhJdGVtQ291bnQ6IC0xLFxuICBhZGRJdGVtczogdHJ1ZSxcbiAgYWRkSXRlbUZpbHRlcjogbnVsbCxcbiAgcmVtb3ZlSXRlbXM6IHRydWUsXG4gIHJlbW92ZUl0ZW1CdXR0b246IGZhbHNlLFxuICBlZGl0SXRlbXM6IGZhbHNlLFxuICBhbGxvd0hUTUw6IHRydWUsXG4gIGR1cGxpY2F0ZUl0ZW1zQWxsb3dlZDogdHJ1ZSxcbiAgZGVsaW1pdGVyOiAnLCcsXG4gIHBhc3RlOiB0cnVlLFxuICBzZWFyY2hFbmFibGVkOiB0cnVlLFxuICBzZWFyY2hDaG9pY2VzOiB0cnVlLFxuICBzZWFyY2hGbG9vcjogMSxcbiAgc2VhcmNoUmVzdWx0TGltaXQ6IDQsXG4gIHNlYXJjaEZpZWxkczogWydsYWJlbCcsICd2YWx1ZSddLFxuICBwb3NpdGlvbjogJ2F1dG8nLFxuICByZXNldFNjcm9sbFBvc2l0aW9uOiB0cnVlLFxuICBzaG91bGRTb3J0OiB0cnVlLFxuICBzaG91bGRTb3J0SXRlbXM6IGZhbHNlLFxuICBzb3J0ZXI6IHV0aWxzXzEuc29ydEJ5QWxwaGEsXG4gIHBsYWNlaG9sZGVyOiB0cnVlLFxuICBwbGFjZWhvbGRlclZhbHVlOiBudWxsLFxuICBzZWFyY2hQbGFjZWhvbGRlclZhbHVlOiBudWxsLFxuICBwcmVwZW5kVmFsdWU6IG51bGwsXG4gIGFwcGVuZFZhbHVlOiBudWxsLFxuICByZW5kZXJTZWxlY3RlZENob2ljZXM6ICdhdXRvJyxcbiAgbG9hZGluZ1RleHQ6ICdMb2FkaW5nLi4uJyxcbiAgbm9SZXN1bHRzVGV4dDogJ05vIHJlc3VsdHMgZm91bmQnLFxuICBub0Nob2ljZXNUZXh0OiAnTm8gY2hvaWNlcyB0byBjaG9vc2UgZnJvbScsXG4gIGl0ZW1TZWxlY3RUZXh0OiAnUHJlc3MgdG8gc2VsZWN0JyxcbiAgdW5pcXVlSXRlbVRleHQ6ICdPbmx5IHVuaXF1ZSB2YWx1ZXMgY2FuIGJlIGFkZGVkJyxcbiAgY3VzdG9tQWRkSXRlbVRleHQ6ICdPbmx5IHZhbHVlcyBtYXRjaGluZyBzcGVjaWZpYyBjb25kaXRpb25zIGNhbiBiZSBhZGRlZCcsXG4gIGFkZEl0ZW1UZXh0OiBmdW5jdGlvbiAodmFsdWUpIHtcbiAgICByZXR1cm4gXCJQcmVzcyBFbnRlciB0byBhZGQgPGI+XFxcIlwiLmNvbmNhdCgoMCwgdXRpbHNfMS5zYW5pdGlzZSkodmFsdWUpLCBcIlxcXCI8L2I+XCIpO1xuICB9LFxuICBtYXhJdGVtVGV4dDogZnVuY3Rpb24gKG1heEl0ZW1Db3VudCkge1xuICAgIHJldHVybiBcIk9ubHkgXCIuY29uY2F0KG1heEl0ZW1Db3VudCwgXCIgdmFsdWVzIGNhbiBiZSBhZGRlZFwiKTtcbiAgfSxcbiAgdmFsdWVDb21wYXJlcjogZnVuY3Rpb24gKHZhbHVlMSwgdmFsdWUyKSB7XG4gICAgcmV0dXJuIHZhbHVlMSA9PT0gdmFsdWUyO1xuICB9LFxuICBmdXNlT3B0aW9uczoge1xuICAgIGluY2x1ZGVTY29yZTogdHJ1ZVxuICB9LFxuICBsYWJlbElkOiAnJyxcbiAgY2FsbGJhY2tPbkluaXQ6IG51bGwsXG4gIGNhbGxiYWNrT25DcmVhdGVUZW1wbGF0ZXM6IG51bGwsXG4gIGNsYXNzTmFtZXM6IGV4cG9ydHMuREVGQVVMVF9DTEFTU05BTUVTXG59O1xuXG4vKioqLyB9KSxcblxuLyoqKi8gMTg6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDk3ODpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cykge1xuXG5cblxuLyogZXNsaW50LWRpc2FibGUgQHR5cGVzY3JpcHQtZXNsaW50L25vLWV4cGxpY2l0LWFueSAqL1xuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gOTQ4OlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5cbi8qKiovIH0pLFxuXG4vKioqLyAzNTk6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDI4NTpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gNTMzOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG4vKiBlc2xpbnQtZGlzYWJsZSBAdHlwZXNjcmlwdC1lc2xpbnQvbm8tZXhwbGljaXQtYW55ICovXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5cbi8qKiovIH0pLFxuXG4vKioqLyAxODc6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuXG5cbnZhciBfX2NyZWF0ZUJpbmRpbmcgPSB0aGlzICYmIHRoaXMuX19jcmVhdGVCaW5kaW5nIHx8IChPYmplY3QuY3JlYXRlID8gZnVuY3Rpb24gKG8sIG0sIGssIGsyKSB7XG4gIGlmIChrMiA9PT0gdW5kZWZpbmVkKSBrMiA9IGs7XG4gIHZhciBkZXNjID0gT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcihtLCBrKTtcbiAgaWYgKCFkZXNjIHx8IChcImdldFwiIGluIGRlc2MgPyAhbS5fX2VzTW9kdWxlIDogZGVzYy53cml0YWJsZSB8fCBkZXNjLmNvbmZpZ3VyYWJsZSkpIHtcbiAgICBkZXNjID0ge1xuICAgICAgZW51bWVyYWJsZTogdHJ1ZSxcbiAgICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgICByZXR1cm4gbVtrXTtcbiAgICAgIH1cbiAgICB9O1xuICB9XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShvLCBrMiwgZGVzYyk7XG59IDogZnVuY3Rpb24gKG8sIG0sIGssIGsyKSB7XG4gIGlmIChrMiA9PT0gdW5kZWZpbmVkKSBrMiA9IGs7XG4gIG9bazJdID0gbVtrXTtcbn0pO1xudmFyIF9fZXhwb3J0U3RhciA9IHRoaXMgJiYgdGhpcy5fX2V4cG9ydFN0YXIgfHwgZnVuY3Rpb24gKG0sIGV4cG9ydHMpIHtcbiAgZm9yICh2YXIgcCBpbiBtKSBpZiAocCAhPT0gXCJkZWZhdWx0XCIgJiYgIU9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChleHBvcnRzLCBwKSkgX19jcmVhdGVCaW5kaW5nKGV4cG9ydHMsIG0sIHApO1xufTtcbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbl9fZXhwb3J0U3RhcihfX3dlYnBhY2tfcmVxdWlyZV9fKDE4KSwgZXhwb3J0cyk7XG5fX2V4cG9ydFN0YXIoX193ZWJwYWNrX3JlcXVpcmVfXyg5NzgpLCBleHBvcnRzKTtcbl9fZXhwb3J0U3RhcihfX3dlYnBhY2tfcmVxdWlyZV9fKDk0OCksIGV4cG9ydHMpO1xuX19leHBvcnRTdGFyKF9fd2VicGFja19yZXF1aXJlX18oMzU5KSwgZXhwb3J0cyk7XG5fX2V4cG9ydFN0YXIoX193ZWJwYWNrX3JlcXVpcmVfXygyODUpLCBleHBvcnRzKTtcbl9fZXhwb3J0U3RhcihfX3dlYnBhY2tfcmVxdWlyZV9fKDUzMyksIGV4cG9ydHMpO1xuX19leHBvcnRTdGFyKF9fd2VicGFja19yZXF1aXJlX18oMjg3KSwgZXhwb3J0cyk7XG5fX2V4cG9ydFN0YXIoX193ZWJwYWNrX3JlcXVpcmVfXygxMzIpLCBleHBvcnRzKTtcbl9fZXhwb3J0U3RhcihfX3dlYnBhY2tfcmVxdWlyZV9fKDgzNyksIGV4cG9ydHMpO1xuX19leHBvcnRTdGFyKF9fd2VicGFja19yZXF1aXJlX18oNTk4KSwgZXhwb3J0cyk7XG5fX2V4cG9ydFN0YXIoX193ZWJwYWNrX3JlcXVpcmVfXygzNjkpLCBleHBvcnRzKTtcbl9fZXhwb3J0U3RhcihfX3dlYnBhY2tfcmVxdWlyZV9fKDM3KSwgZXhwb3J0cyk7XG5fX2V4cG9ydFN0YXIoX193ZWJwYWNrX3JlcXVpcmVfXyg0NyksIGV4cG9ydHMpO1xuX19leHBvcnRTdGFyKF9fd2VicGFja19yZXF1aXJlX18oOTIzKSwgZXhwb3J0cyk7XG5fX2V4cG9ydFN0YXIoX193ZWJwYWNrX3JlcXVpcmVfXyg4NzYpLCBleHBvcnRzKTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDI4Nzpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gMTMyOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA4Mzc6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDU5ODpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gMzc6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDM2OTpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gNDc6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDkyMzpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cykge1xuXG5cblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gODc2OlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA3OTk6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbi8qIGVzbGludC1kaXNhYmxlIEB0eXBlc2NyaXB0LWVzbGludC9uby1leHBsaWNpdC1hbnkgKi9cbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbmV4cG9ydHMucGFyc2VDdXN0b21Qcm9wZXJ0aWVzID0gZXhwb3J0cy5kaWZmID0gZXhwb3J0cy5jbG9uZU9iamVjdCA9IGV4cG9ydHMuZXhpc3RzSW5BcnJheSA9IGV4cG9ydHMuZGlzcGF0Y2hFdmVudCA9IGV4cG9ydHMuc29ydEJ5U2NvcmUgPSBleHBvcnRzLnNvcnRCeUFscGhhID0gZXhwb3J0cy5zdHJUb0VsID0gZXhwb3J0cy5zYW5pdGlzZSA9IGV4cG9ydHMuaXNTY3JvbGxlZEludG9WaWV3ID0gZXhwb3J0cy5nZXRBZGphY2VudEVsID0gZXhwb3J0cy53cmFwID0gZXhwb3J0cy5pc1R5cGUgPSBleHBvcnRzLmdldFR5cGUgPSBleHBvcnRzLmdlbmVyYXRlSWQgPSBleHBvcnRzLmdlbmVyYXRlQ2hhcnMgPSBleHBvcnRzLmdldFJhbmRvbU51bWJlciA9IHZvaWQgMDtcbnZhciBnZXRSYW5kb21OdW1iZXIgPSBmdW5jdGlvbiAobWluLCBtYXgpIHtcbiAgcmV0dXJuIE1hdGguZmxvb3IoTWF0aC5yYW5kb20oKSAqIChtYXggLSBtaW4pICsgbWluKTtcbn07XG5leHBvcnRzLmdldFJhbmRvbU51bWJlciA9IGdldFJhbmRvbU51bWJlcjtcbnZhciBnZW5lcmF0ZUNoYXJzID0gZnVuY3Rpb24gKGxlbmd0aCkge1xuICByZXR1cm4gQXJyYXkuZnJvbSh7XG4gICAgbGVuZ3RoOiBsZW5ndGhcbiAgfSwgZnVuY3Rpb24gKCkge1xuICAgIHJldHVybiAoMCwgZXhwb3J0cy5nZXRSYW5kb21OdW1iZXIpKDAsIDM2KS50b1N0cmluZygzNik7XG4gIH0pLmpvaW4oJycpO1xufTtcbmV4cG9ydHMuZ2VuZXJhdGVDaGFycyA9IGdlbmVyYXRlQ2hhcnM7XG52YXIgZ2VuZXJhdGVJZCA9IGZ1bmN0aW9uIChlbGVtZW50LCBwcmVmaXgpIHtcbiAgdmFyIGlkID0gZWxlbWVudC5pZCB8fCBlbGVtZW50Lm5hbWUgJiYgXCJcIi5jb25jYXQoZWxlbWVudC5uYW1lLCBcIi1cIikuY29uY2F0KCgwLCBleHBvcnRzLmdlbmVyYXRlQ2hhcnMpKDIpKSB8fCAoMCwgZXhwb3J0cy5nZW5lcmF0ZUNoYXJzKSg0KTtcbiAgaWQgPSBpZC5yZXBsYWNlKC8oOnxcXC58XFxbfFxcXXwsKS9nLCAnJyk7XG4gIGlkID0gXCJcIi5jb25jYXQocHJlZml4LCBcIi1cIikuY29uY2F0KGlkKTtcbiAgcmV0dXJuIGlkO1xufTtcbmV4cG9ydHMuZ2VuZXJhdGVJZCA9IGdlbmVyYXRlSWQ7XG52YXIgZ2V0VHlwZSA9IGZ1bmN0aW9uIChvYmopIHtcbiAgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmcuY2FsbChvYmopLnNsaWNlKDgsIC0xKTtcbn07XG5leHBvcnRzLmdldFR5cGUgPSBnZXRUeXBlO1xudmFyIGlzVHlwZSA9IGZ1bmN0aW9uICh0eXBlLCBvYmopIHtcbiAgcmV0dXJuIG9iaiAhPT0gdW5kZWZpbmVkICYmIG9iaiAhPT0gbnVsbCAmJiAoMCwgZXhwb3J0cy5nZXRUeXBlKShvYmopID09PSB0eXBlO1xufTtcbmV4cG9ydHMuaXNUeXBlID0gaXNUeXBlO1xudmFyIHdyYXAgPSBmdW5jdGlvbiAoZWxlbWVudCwgd3JhcHBlcikge1xuICBpZiAod3JhcHBlciA9PT0gdm9pZCAwKSB7XG4gICAgd3JhcHBlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICB9XG4gIGlmIChlbGVtZW50LnBhcmVudE5vZGUpIHtcbiAgICBpZiAoZWxlbWVudC5uZXh0U2libGluZykge1xuICAgICAgZWxlbWVudC5wYXJlbnROb2RlLmluc2VydEJlZm9yZSh3cmFwcGVyLCBlbGVtZW50Lm5leHRTaWJsaW5nKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZWxlbWVudC5wYXJlbnROb2RlLmFwcGVuZENoaWxkKHdyYXBwZXIpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gd3JhcHBlci5hcHBlbmRDaGlsZChlbGVtZW50KTtcbn07XG5leHBvcnRzLndyYXAgPSB3cmFwO1xudmFyIGdldEFkamFjZW50RWwgPSBmdW5jdGlvbiAoc3RhcnRFbCwgc2VsZWN0b3IsIGRpcmVjdGlvbikge1xuICBpZiAoZGlyZWN0aW9uID09PSB2b2lkIDApIHtcbiAgICBkaXJlY3Rpb24gPSAxO1xuICB9XG4gIHZhciBwcm9wID0gXCJcIi5jb25jYXQoZGlyZWN0aW9uID4gMCA/ICduZXh0JyA6ICdwcmV2aW91cycsIFwiRWxlbWVudFNpYmxpbmdcIik7XG4gIHZhciBzaWJsaW5nID0gc3RhcnRFbFtwcm9wXTtcbiAgd2hpbGUgKHNpYmxpbmcpIHtcbiAgICBpZiAoc2libGluZy5tYXRjaGVzKHNlbGVjdG9yKSkge1xuICAgICAgcmV0dXJuIHNpYmxpbmc7XG4gICAgfVxuICAgIHNpYmxpbmcgPSBzaWJsaW5nW3Byb3BdO1xuICB9XG4gIHJldHVybiBzaWJsaW5nO1xufTtcbmV4cG9ydHMuZ2V0QWRqYWNlbnRFbCA9IGdldEFkamFjZW50RWw7XG52YXIgaXNTY3JvbGxlZEludG9WaWV3ID0gZnVuY3Rpb24gKGVsZW1lbnQsIHBhcmVudCwgZGlyZWN0aW9uKSB7XG4gIGlmIChkaXJlY3Rpb24gPT09IHZvaWQgMCkge1xuICAgIGRpcmVjdGlvbiA9IDE7XG4gIH1cbiAgaWYgKCFlbGVtZW50KSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG4gIHZhciBpc1Zpc2libGU7XG4gIGlmIChkaXJlY3Rpb24gPiAwKSB7XG4gICAgLy8gSW4gdmlldyBmcm9tIGJvdHRvbVxuICAgIGlzVmlzaWJsZSA9IHBhcmVudC5zY3JvbGxUb3AgKyBwYXJlbnQub2Zmc2V0SGVpZ2h0ID49IGVsZW1lbnQub2Zmc2V0VG9wICsgZWxlbWVudC5vZmZzZXRIZWlnaHQ7XG4gIH0gZWxzZSB7XG4gICAgLy8gSW4gdmlldyBmcm9tIHRvcFxuICAgIGlzVmlzaWJsZSA9IGVsZW1lbnQub2Zmc2V0VG9wID49IHBhcmVudC5zY3JvbGxUb3A7XG4gIH1cbiAgcmV0dXJuIGlzVmlzaWJsZTtcbn07XG5leHBvcnRzLmlzU2Nyb2xsZWRJbnRvVmlldyA9IGlzU2Nyb2xsZWRJbnRvVmlldztcbnZhciBzYW5pdGlzZSA9IGZ1bmN0aW9uICh2YWx1ZSkge1xuICBpZiAodHlwZW9mIHZhbHVlICE9PSAnc3RyaW5nJykge1xuICAgIHJldHVybiB2YWx1ZTtcbiAgfVxuICByZXR1cm4gdmFsdWUucmVwbGFjZSgvJi9nLCAnJmFtcDsnKS5yZXBsYWNlKC8+L2csICcmZ3Q7JykucmVwbGFjZSgvPC9nLCAnJmx0OycpLnJlcGxhY2UoL1wiL2csICcmcXVvdDsnKTtcbn07XG5leHBvcnRzLnNhbml0aXNlID0gc2FuaXRpc2U7XG5leHBvcnRzLnN0clRvRWwgPSBmdW5jdGlvbiAoKSB7XG4gIHZhciB0bXBFbCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICByZXR1cm4gZnVuY3Rpb24gKHN0cikge1xuICAgIHZhciBjbGVhbmVkSW5wdXQgPSBzdHIudHJpbSgpO1xuICAgIHRtcEVsLmlubmVySFRNTCA9IGNsZWFuZWRJbnB1dDtcbiAgICB2YXIgZmlybGRDaGlsZCA9IHRtcEVsLmNoaWxkcmVuWzBdO1xuICAgIHdoaWxlICh0bXBFbC5maXJzdENoaWxkKSB7XG4gICAgICB0bXBFbC5yZW1vdmVDaGlsZCh0bXBFbC5maXJzdENoaWxkKTtcbiAgICB9XG4gICAgcmV0dXJuIGZpcmxkQ2hpbGQ7XG4gIH07XG59KCk7XG52YXIgc29ydEJ5QWxwaGEgPSBmdW5jdGlvbiAoX2EsIF9iKSB7XG4gIHZhciB2YWx1ZSA9IF9hLnZhbHVlLFxuICAgIF9jID0gX2EubGFiZWwsXG4gICAgbGFiZWwgPSBfYyA9PT0gdm9pZCAwID8gdmFsdWUgOiBfYztcbiAgdmFyIHZhbHVlMiA9IF9iLnZhbHVlLFxuICAgIF9kID0gX2IubGFiZWwsXG4gICAgbGFiZWwyID0gX2QgPT09IHZvaWQgMCA/IHZhbHVlMiA6IF9kO1xuICByZXR1cm4gbGFiZWwubG9jYWxlQ29tcGFyZShsYWJlbDIsIFtdLCB7XG4gICAgc2Vuc2l0aXZpdHk6ICdiYXNlJyxcbiAgICBpZ25vcmVQdW5jdHVhdGlvbjogdHJ1ZSxcbiAgICBudW1lcmljOiB0cnVlXG4gIH0pO1xufTtcbmV4cG9ydHMuc29ydEJ5QWxwaGEgPSBzb3J0QnlBbHBoYTtcbnZhciBzb3J0QnlTY29yZSA9IGZ1bmN0aW9uIChhLCBiKSB7XG4gIHZhciBfYSA9IGEuc2NvcmUsXG4gICAgc2NvcmVBID0gX2EgPT09IHZvaWQgMCA/IDAgOiBfYTtcbiAgdmFyIF9iID0gYi5zY29yZSxcbiAgICBzY29yZUIgPSBfYiA9PT0gdm9pZCAwID8gMCA6IF9iO1xuICByZXR1cm4gc2NvcmVBIC0gc2NvcmVCO1xufTtcbmV4cG9ydHMuc29ydEJ5U2NvcmUgPSBzb3J0QnlTY29yZTtcbnZhciBkaXNwYXRjaEV2ZW50ID0gZnVuY3Rpb24gKGVsZW1lbnQsIHR5cGUsIGN1c3RvbUFyZ3MpIHtcbiAgaWYgKGN1c3RvbUFyZ3MgPT09IHZvaWQgMCkge1xuICAgIGN1c3RvbUFyZ3MgPSBudWxsO1xuICB9XG4gIHZhciBldmVudCA9IG5ldyBDdXN0b21FdmVudCh0eXBlLCB7XG4gICAgZGV0YWlsOiBjdXN0b21BcmdzLFxuICAgIGJ1YmJsZXM6IHRydWUsXG4gICAgY2FuY2VsYWJsZTogdHJ1ZVxuICB9KTtcbiAgcmV0dXJuIGVsZW1lbnQuZGlzcGF0Y2hFdmVudChldmVudCk7XG59O1xuZXhwb3J0cy5kaXNwYXRjaEV2ZW50ID0gZGlzcGF0Y2hFdmVudDtcbnZhciBleGlzdHNJbkFycmF5ID0gZnVuY3Rpb24gKGFycmF5LCB2YWx1ZSwga2V5KSB7XG4gIGlmIChrZXkgPT09IHZvaWQgMCkge1xuICAgIGtleSA9ICd2YWx1ZSc7XG4gIH1cbiAgcmV0dXJuIGFycmF5LnNvbWUoZnVuY3Rpb24gKGl0ZW0pIHtcbiAgICBpZiAodHlwZW9mIHZhbHVlID09PSAnc3RyaW5nJykge1xuICAgICAgcmV0dXJuIGl0ZW1ba2V5XSA9PT0gdmFsdWUudHJpbSgpO1xuICAgIH1cbiAgICByZXR1cm4gaXRlbVtrZXldID09PSB2YWx1ZTtcbiAgfSk7XG59O1xuZXhwb3J0cy5leGlzdHNJbkFycmF5ID0gZXhpc3RzSW5BcnJheTtcbnZhciBjbG9uZU9iamVjdCA9IGZ1bmN0aW9uIChvYmopIHtcbiAgcmV0dXJuIEpTT04ucGFyc2UoSlNPTi5zdHJpbmdpZnkob2JqKSk7XG59O1xuZXhwb3J0cy5jbG9uZU9iamVjdCA9IGNsb25lT2JqZWN0O1xuLyoqXG4gKiBSZXR1cm5zIGFuIGFycmF5IG9mIGtleXMgcHJlc2VudCBvbiB0aGUgZmlyc3QgYnV0IG1pc3Npbmcgb24gdGhlIHNlY29uZCBvYmplY3RcbiAqL1xudmFyIGRpZmYgPSBmdW5jdGlvbiAoYSwgYikge1xuICB2YXIgYUtleXMgPSBPYmplY3Qua2V5cyhhKS5zb3J0KCk7XG4gIHZhciBiS2V5cyA9IE9iamVjdC5rZXlzKGIpLnNvcnQoKTtcbiAgcmV0dXJuIGFLZXlzLmZpbHRlcihmdW5jdGlvbiAoaSkge1xuICAgIHJldHVybiBiS2V5cy5pbmRleE9mKGkpIDwgMDtcbiAgfSk7XG59O1xuZXhwb3J0cy5kaWZmID0gZGlmZjtcbnZhciBwYXJzZUN1c3RvbVByb3BlcnRpZXMgPSBmdW5jdGlvbiAoY3VzdG9tUHJvcGVydGllcykge1xuICBpZiAodHlwZW9mIGN1c3RvbVByb3BlcnRpZXMgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgdHJ5IHtcbiAgICAgIHJldHVybiBKU09OLnBhcnNlKGN1c3RvbVByb3BlcnRpZXMpO1xuICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgIHJldHVybiBjdXN0b21Qcm9wZXJ0aWVzO1xuICAgIH1cbiAgfVxuICByZXR1cm4ge307XG59O1xuZXhwb3J0cy5wYXJzZUN1c3RvbVByb3BlcnRpZXMgPSBwYXJzZUN1c3RvbVByb3BlcnRpZXM7XG5cbi8qKiovIH0pLFxuXG4vKioqLyAyNzM6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbnZhciBfX3NwcmVhZEFycmF5ID0gdGhpcyAmJiB0aGlzLl9fc3ByZWFkQXJyYXkgfHwgZnVuY3Rpb24gKHRvLCBmcm9tLCBwYWNrKSB7XG4gIGlmIChwYWNrIHx8IGFyZ3VtZW50cy5sZW5ndGggPT09IDIpIGZvciAodmFyIGkgPSAwLCBsID0gZnJvbS5sZW5ndGgsIGFyOyBpIDwgbDsgaSsrKSB7XG4gICAgaWYgKGFyIHx8ICEoaSBpbiBmcm9tKSkge1xuICAgICAgaWYgKCFhcikgYXIgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChmcm9tLCAwLCBpKTtcbiAgICAgIGFyW2ldID0gZnJvbVtpXTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHRvLmNvbmNhdChhciB8fCBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChmcm9tKSk7XG59O1xuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuZXhwb3J0cy5kZWZhdWx0U3RhdGUgPSB2b2lkIDA7XG5leHBvcnRzLmRlZmF1bHRTdGF0ZSA9IFtdO1xuZnVuY3Rpb24gY2hvaWNlcyhzdGF0ZSwgYWN0aW9uKSB7XG4gIGlmIChzdGF0ZSA9PT0gdm9pZCAwKSB7XG4gICAgc3RhdGUgPSBleHBvcnRzLmRlZmF1bHRTdGF0ZTtcbiAgfVxuICBpZiAoYWN0aW9uID09PSB2b2lkIDApIHtcbiAgICBhY3Rpb24gPSB7fTtcbiAgfVxuICBzd2l0Y2ggKGFjdGlvbi50eXBlKSB7XG4gICAgY2FzZSAnQUREX0NIT0lDRSc6XG4gICAgICB7XG4gICAgICAgIHZhciBhZGRDaG9pY2VBY3Rpb24gPSBhY3Rpb247XG4gICAgICAgIHZhciBjaG9pY2UgPSB7XG4gICAgICAgICAgaWQ6IGFkZENob2ljZUFjdGlvbi5pZCxcbiAgICAgICAgICBlbGVtZW50SWQ6IGFkZENob2ljZUFjdGlvbi5lbGVtZW50SWQsXG4gICAgICAgICAgZ3JvdXBJZDogYWRkQ2hvaWNlQWN0aW9uLmdyb3VwSWQsXG4gICAgICAgICAgdmFsdWU6IGFkZENob2ljZUFjdGlvbi52YWx1ZSxcbiAgICAgICAgICBsYWJlbDogYWRkQ2hvaWNlQWN0aW9uLmxhYmVsIHx8IGFkZENob2ljZUFjdGlvbi52YWx1ZSxcbiAgICAgICAgICBkaXNhYmxlZDogYWRkQ2hvaWNlQWN0aW9uLmRpc2FibGVkIHx8IGZhbHNlLFxuICAgICAgICAgIHNlbGVjdGVkOiBmYWxzZSxcbiAgICAgICAgICBhY3RpdmU6IHRydWUsXG4gICAgICAgICAgc2NvcmU6IDk5OTksXG4gICAgICAgICAgY3VzdG9tUHJvcGVydGllczogYWRkQ2hvaWNlQWN0aW9uLmN1c3RvbVByb3BlcnRpZXMsXG4gICAgICAgICAgcGxhY2Vob2xkZXI6IGFkZENob2ljZUFjdGlvbi5wbGFjZWhvbGRlciB8fCBmYWxzZVxuICAgICAgICB9O1xuICAgICAgICAvKlxuICAgICAgICAgIEEgZGlzYWJsZWQgY2hvaWNlIGFwcGVhcnMgaW4gdGhlIGNob2ljZSBkcm9wZG93biBidXQgY2Fubm90IGJlIHNlbGVjdGVkXG4gICAgICAgICAgQSBzZWxlY3RlZCBjaG9pY2UgaGFzIGJlZW4gYWRkZWQgdG8gdGhlIHBhc3NlZCBpbnB1dCdzIHZhbHVlIChhZGRlZCBhcyBhbiBpdGVtKVxuICAgICAgICAgIEFuIGFjdGl2ZSBjaG9pY2UgYXBwZWFycyB3aXRoaW4gdGhlIGNob2ljZSBkcm9wZG93blxuICAgICAgICAqL1xuICAgICAgICByZXR1cm4gX19zcHJlYWRBcnJheShfX3NwcmVhZEFycmF5KFtdLCBzdGF0ZSwgdHJ1ZSksIFtjaG9pY2VdLCBmYWxzZSk7XG4gICAgICB9XG4gICAgY2FzZSAnQUREX0lURU0nOlxuICAgICAge1xuICAgICAgICB2YXIgYWRkSXRlbUFjdGlvbl8xID0gYWN0aW9uO1xuICAgICAgICAvLyBXaGVuIGFuIGl0ZW0gaXMgYWRkZWQgYW5kIGl0IGhhcyBhbiBhc3NvY2lhdGVkIGNob2ljZSxcbiAgICAgICAgLy8gd2Ugd2FudCB0byBkaXNhYmxlIGl0IHNvIGl0IGNhbid0IGJlIGNob3NlbiBhZ2FpblxuICAgICAgICBpZiAoYWRkSXRlbUFjdGlvbl8xLmNob2ljZUlkID4gLTEpIHtcbiAgICAgICAgICByZXR1cm4gc3RhdGUubWFwKGZ1bmN0aW9uIChvYmopIHtcbiAgICAgICAgICAgIHZhciBjaG9pY2UgPSBvYmo7XG4gICAgICAgICAgICBpZiAoY2hvaWNlLmlkID09PSBwYXJzZUludChcIlwiLmNvbmNhdChhZGRJdGVtQWN0aW9uXzEuY2hvaWNlSWQpLCAxMCkpIHtcbiAgICAgICAgICAgICAgY2hvaWNlLnNlbGVjdGVkID0gdHJ1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBjaG9pY2U7XG4gICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHN0YXRlO1xuICAgICAgfVxuICAgIGNhc2UgJ1JFTU9WRV9JVEVNJzpcbiAgICAgIHtcbiAgICAgICAgdmFyIHJlbW92ZUl0ZW1BY3Rpb25fMSA9IGFjdGlvbjtcbiAgICAgICAgLy8gV2hlbiBhbiBpdGVtIGlzIHJlbW92ZWQgYW5kIGl0IGhhcyBhbiBhc3NvY2lhdGVkIGNob2ljZSxcbiAgICAgICAgLy8gd2Ugd2FudCB0byByZS1lbmFibGUgaXQgc28gaXQgY2FuIGJlIGNob3NlbiBhZ2FpblxuICAgICAgICBpZiAocmVtb3ZlSXRlbUFjdGlvbl8xLmNob2ljZUlkICYmIHJlbW92ZUl0ZW1BY3Rpb25fMS5jaG9pY2VJZCA+IC0xKSB7XG4gICAgICAgICAgcmV0dXJuIHN0YXRlLm1hcChmdW5jdGlvbiAob2JqKSB7XG4gICAgICAgICAgICB2YXIgY2hvaWNlID0gb2JqO1xuICAgICAgICAgICAgaWYgKGNob2ljZS5pZCA9PT0gcGFyc2VJbnQoXCJcIi5jb25jYXQocmVtb3ZlSXRlbUFjdGlvbl8xLmNob2ljZUlkKSwgMTApKSB7XG4gICAgICAgICAgICAgIGNob2ljZS5zZWxlY3RlZCA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIGNob2ljZTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gc3RhdGU7XG4gICAgICB9XG4gICAgY2FzZSAnRklMVEVSX0NIT0lDRVMnOlxuICAgICAge1xuICAgICAgICB2YXIgZmlsdGVyQ2hvaWNlc0FjdGlvbl8xID0gYWN0aW9uO1xuICAgICAgICByZXR1cm4gc3RhdGUubWFwKGZ1bmN0aW9uIChvYmopIHtcbiAgICAgICAgICB2YXIgY2hvaWNlID0gb2JqO1xuICAgICAgICAgIC8vIFNldCBhY3RpdmUgc3RhdGUgYmFzZWQgb24gd2hldGhlciBjaG9pY2UgaXNcbiAgICAgICAgICAvLyB3aXRoaW4gZmlsdGVyZWQgcmVzdWx0c1xuICAgICAgICAgIGNob2ljZS5hY3RpdmUgPSBmaWx0ZXJDaG9pY2VzQWN0aW9uXzEucmVzdWx0cy5zb21lKGZ1bmN0aW9uIChfYSkge1xuICAgICAgICAgICAgdmFyIGl0ZW0gPSBfYS5pdGVtLFxuICAgICAgICAgICAgICBzY29yZSA9IF9hLnNjb3JlO1xuICAgICAgICAgICAgaWYgKGl0ZW0uaWQgPT09IGNob2ljZS5pZCkge1xuICAgICAgICAgICAgICBjaG9pY2Uuc2NvcmUgPSBzY29yZTtcbiAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgcmV0dXJuIGNob2ljZTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgY2FzZSAnQUNUSVZBVEVfQ0hPSUNFUyc6XG4gICAgICB7XG4gICAgICAgIHZhciBhY3RpdmF0ZUNob2ljZXNBY3Rpb25fMSA9IGFjdGlvbjtcbiAgICAgICAgcmV0dXJuIHN0YXRlLm1hcChmdW5jdGlvbiAob2JqKSB7XG4gICAgICAgICAgdmFyIGNob2ljZSA9IG9iajtcbiAgICAgICAgICBjaG9pY2UuYWN0aXZlID0gYWN0aXZhdGVDaG9pY2VzQWN0aW9uXzEuYWN0aXZlO1xuICAgICAgICAgIHJldHVybiBjaG9pY2U7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIGNhc2UgJ0NMRUFSX0NIT0lDRVMnOlxuICAgICAge1xuICAgICAgICByZXR1cm4gZXhwb3J0cy5kZWZhdWx0U3RhdGU7XG4gICAgICB9XG4gICAgZGVmYXVsdDpcbiAgICAgIHtcbiAgICAgICAgcmV0dXJuIHN0YXRlO1xuICAgICAgfVxuICB9XG59XG5leHBvcnRzW1wiZGVmYXVsdFwiXSA9IGNob2ljZXM7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA4NzE6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMpIHtcblxuXG5cbnZhciBfX3NwcmVhZEFycmF5ID0gdGhpcyAmJiB0aGlzLl9fc3ByZWFkQXJyYXkgfHwgZnVuY3Rpb24gKHRvLCBmcm9tLCBwYWNrKSB7XG4gIGlmIChwYWNrIHx8IGFyZ3VtZW50cy5sZW5ndGggPT09IDIpIGZvciAodmFyIGkgPSAwLCBsID0gZnJvbS5sZW5ndGgsIGFyOyBpIDwgbDsgaSsrKSB7XG4gICAgaWYgKGFyIHx8ICEoaSBpbiBmcm9tKSkge1xuICAgICAgaWYgKCFhcikgYXIgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChmcm9tLCAwLCBpKTtcbiAgICAgIGFyW2ldID0gZnJvbVtpXTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHRvLmNvbmNhdChhciB8fCBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChmcm9tKSk7XG59O1xuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xuZXhwb3J0cy5kZWZhdWx0U3RhdGUgPSB2b2lkIDA7XG5leHBvcnRzLmRlZmF1bHRTdGF0ZSA9IFtdO1xuZnVuY3Rpb24gZ3JvdXBzKHN0YXRlLCBhY3Rpb24pIHtcbiAgaWYgKHN0YXRlID09PSB2b2lkIDApIHtcbiAgICBzdGF0ZSA9IGV4cG9ydHMuZGVmYXVsdFN0YXRlO1xuICB9XG4gIGlmIChhY3Rpb24gPT09IHZvaWQgMCkge1xuICAgIGFjdGlvbiA9IHt9O1xuICB9XG4gIHN3aXRjaCAoYWN0aW9uLnR5cGUpIHtcbiAgICBjYXNlICdBRERfR1JPVVAnOlxuICAgICAge1xuICAgICAgICB2YXIgYWRkR3JvdXBBY3Rpb24gPSBhY3Rpb247XG4gICAgICAgIHJldHVybiBfX3NwcmVhZEFycmF5KF9fc3ByZWFkQXJyYXkoW10sIHN0YXRlLCB0cnVlKSwgW3tcbiAgICAgICAgICBpZDogYWRkR3JvdXBBY3Rpb24uaWQsXG4gICAgICAgICAgdmFsdWU6IGFkZEdyb3VwQWN0aW9uLnZhbHVlLFxuICAgICAgICAgIGFjdGl2ZTogYWRkR3JvdXBBY3Rpb24uYWN0aXZlLFxuICAgICAgICAgIGRpc2FibGVkOiBhZGRHcm91cEFjdGlvbi5kaXNhYmxlZFxuICAgICAgICB9XSwgZmFsc2UpO1xuICAgICAgfVxuICAgIGNhc2UgJ0NMRUFSX0NIT0lDRVMnOlxuICAgICAge1xuICAgICAgICByZXR1cm4gW107XG4gICAgICB9XG4gICAgZGVmYXVsdDpcbiAgICAgIHtcbiAgICAgICAgcmV0dXJuIHN0YXRlO1xuICAgICAgfVxuICB9XG59XG5leHBvcnRzW1wiZGVmYXVsdFwiXSA9IGdyb3VwcztcblxuLyoqKi8gfSksXG5cbi8qKiovIDY1NTpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXykge1xuXG5cblxudmFyIF9faW1wb3J0RGVmYXVsdCA9IHRoaXMgJiYgdGhpcy5fX2ltcG9ydERlZmF1bHQgfHwgZnVuY3Rpb24gKG1vZCkge1xuICByZXR1cm4gbW9kICYmIG1vZC5fX2VzTW9kdWxlID8gbW9kIDoge1xuICAgIFwiZGVmYXVsdFwiOiBtb2RcbiAgfTtcbn07XG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5leHBvcnRzLmRlZmF1bHRTdGF0ZSA9IHZvaWQgMDtcbnZhciByZWR1eF8xID0gX193ZWJwYWNrX3JlcXVpcmVfXyg3OTEpO1xudmFyIGl0ZW1zXzEgPSBfX2ltcG9ydERlZmF1bHQoX193ZWJwYWNrX3JlcXVpcmVfXyg1MikpO1xudmFyIGdyb3Vwc18xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oODcxKSk7XG52YXIgY2hvaWNlc18xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oMjczKSk7XG52YXIgbG9hZGluZ18xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oNTAyKSk7XG52YXIgdXRpbHNfMSA9IF9fd2VicGFja19yZXF1aXJlX18oNzk5KTtcbmV4cG9ydHMuZGVmYXVsdFN0YXRlID0ge1xuICBncm91cHM6IFtdLFxuICBpdGVtczogW10sXG4gIGNob2ljZXM6IFtdLFxuICBsb2FkaW5nOiBmYWxzZVxufTtcbnZhciBhcHBSZWR1Y2VyID0gKDAsIHJlZHV4XzEuY29tYmluZVJlZHVjZXJzKSh7XG4gIGl0ZW1zOiBpdGVtc18xLmRlZmF1bHQsXG4gIGdyb3VwczogZ3JvdXBzXzEuZGVmYXVsdCxcbiAgY2hvaWNlczogY2hvaWNlc18xLmRlZmF1bHQsXG4gIGxvYWRpbmc6IGxvYWRpbmdfMS5kZWZhdWx0XG59KTtcbnZhciByb290UmVkdWNlciA9IGZ1bmN0aW9uIChwYXNzZWRTdGF0ZSwgYWN0aW9uKSB7XG4gIHZhciBzdGF0ZSA9IHBhc3NlZFN0YXRlO1xuICAvLyBJZiB3ZSBhcmUgY2xlYXJpbmcgYWxsIGl0ZW1zLCBncm91cHMgYW5kIG9wdGlvbnMgd2UgcmVhc3NpZ25cbiAgLy8gc3RhdGUgYW5kIHRoZW4gcGFzcyB0aGF0IHN0YXRlIHRvIG91ciBwcm9wZXIgcmVkdWNlci4gVGhpcyBpc24ndFxuICAvLyBtdXRhdGluZyBvdXIgYWN0dWFsIHN0YXRlXG4gIC8vIFNlZTogaHR0cDovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMzU2NDE5OTJcbiAgaWYgKGFjdGlvbi50eXBlID09PSAnQ0xFQVJfQUxMJykge1xuICAgIHN0YXRlID0gZXhwb3J0cy5kZWZhdWx0U3RhdGU7XG4gIH0gZWxzZSBpZiAoYWN0aW9uLnR5cGUgPT09ICdSRVNFVF9UTycpIHtcbiAgICByZXR1cm4gKDAsIHV0aWxzXzEuY2xvbmVPYmplY3QpKGFjdGlvbi5zdGF0ZSk7XG4gIH1cbiAgcmV0dXJuIGFwcFJlZHVjZXIoc3RhdGUsIGFjdGlvbik7XG59O1xuZXhwb3J0c1tcImRlZmF1bHRcIl0gPSByb290UmVkdWNlcjtcblxuLyoqKi8gfSksXG5cbi8qKiovIDUyOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG52YXIgX19zcHJlYWRBcnJheSA9IHRoaXMgJiYgdGhpcy5fX3NwcmVhZEFycmF5IHx8IGZ1bmN0aW9uICh0bywgZnJvbSwgcGFjaykge1xuICBpZiAocGFjayB8fCBhcmd1bWVudHMubGVuZ3RoID09PSAyKSBmb3IgKHZhciBpID0gMCwgbCA9IGZyb20ubGVuZ3RoLCBhcjsgaSA8IGw7IGkrKykge1xuICAgIGlmIChhciB8fCAhKGkgaW4gZnJvbSkpIHtcbiAgICAgIGlmICghYXIpIGFyID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoZnJvbSwgMCwgaSk7XG4gICAgICBhcltpXSA9IGZyb21baV07XG4gICAgfVxuICB9XG4gIHJldHVybiB0by5jb25jYXQoYXIgfHwgQXJyYXkucHJvdG90eXBlLnNsaWNlLmNhbGwoZnJvbSkpO1xufTtcbk9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgKHtcbiAgdmFsdWU6IHRydWVcbn0pKTtcbmV4cG9ydHMuZGVmYXVsdFN0YXRlID0gdm9pZCAwO1xuZXhwb3J0cy5kZWZhdWx0U3RhdGUgPSBbXTtcbmZ1bmN0aW9uIGl0ZW1zKHN0YXRlLCBhY3Rpb24pIHtcbiAgaWYgKHN0YXRlID09PSB2b2lkIDApIHtcbiAgICBzdGF0ZSA9IGV4cG9ydHMuZGVmYXVsdFN0YXRlO1xuICB9XG4gIGlmIChhY3Rpb24gPT09IHZvaWQgMCkge1xuICAgIGFjdGlvbiA9IHt9O1xuICB9XG4gIHN3aXRjaCAoYWN0aW9uLnR5cGUpIHtcbiAgICBjYXNlICdBRERfSVRFTSc6XG4gICAgICB7XG4gICAgICAgIHZhciBhZGRJdGVtQWN0aW9uID0gYWN0aW9uO1xuICAgICAgICAvLyBBZGQgb2JqZWN0IHRvIGl0ZW1zIGFycmF5XG4gICAgICAgIHZhciBuZXdTdGF0ZSA9IF9fc3ByZWFkQXJyYXkoX19zcHJlYWRBcnJheShbXSwgc3RhdGUsIHRydWUpLCBbe1xuICAgICAgICAgIGlkOiBhZGRJdGVtQWN0aW9uLmlkLFxuICAgICAgICAgIGNob2ljZUlkOiBhZGRJdGVtQWN0aW9uLmNob2ljZUlkLFxuICAgICAgICAgIGdyb3VwSWQ6IGFkZEl0ZW1BY3Rpb24uZ3JvdXBJZCxcbiAgICAgICAgICB2YWx1ZTogYWRkSXRlbUFjdGlvbi52YWx1ZSxcbiAgICAgICAgICBsYWJlbDogYWRkSXRlbUFjdGlvbi5sYWJlbCxcbiAgICAgICAgICBhY3RpdmU6IHRydWUsXG4gICAgICAgICAgaGlnaGxpZ2h0ZWQ6IGZhbHNlLFxuICAgICAgICAgIGN1c3RvbVByb3BlcnRpZXM6IGFkZEl0ZW1BY3Rpb24uY3VzdG9tUHJvcGVydGllcyxcbiAgICAgICAgICBwbGFjZWhvbGRlcjogYWRkSXRlbUFjdGlvbi5wbGFjZWhvbGRlciB8fCBmYWxzZSxcbiAgICAgICAgICBrZXlDb2RlOiBudWxsXG4gICAgICAgIH1dLCBmYWxzZSk7XG4gICAgICAgIHJldHVybiBuZXdTdGF0ZS5tYXAoZnVuY3Rpb24gKG9iaikge1xuICAgICAgICAgIHZhciBpdGVtID0gb2JqO1xuICAgICAgICAgIGl0ZW0uaGlnaGxpZ2h0ZWQgPSBmYWxzZTtcbiAgICAgICAgICByZXR1cm4gaXRlbTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgY2FzZSAnUkVNT1ZFX0lURU0nOlxuICAgICAge1xuICAgICAgICAvLyBTZXQgaXRlbSB0byBpbmFjdGl2ZVxuICAgICAgICByZXR1cm4gc3RhdGUubWFwKGZ1bmN0aW9uIChvYmopIHtcbiAgICAgICAgICB2YXIgaXRlbSA9IG9iajtcbiAgICAgICAgICBpZiAoaXRlbS5pZCA9PT0gYWN0aW9uLmlkKSB7XG4gICAgICAgICAgICBpdGVtLmFjdGl2ZSA9IGZhbHNlO1xuICAgICAgICAgIH1cbiAgICAgICAgICByZXR1cm4gaXRlbTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgY2FzZSAnSElHSExJR0hUX0lURU0nOlxuICAgICAge1xuICAgICAgICB2YXIgaGlnaGxpZ2h0SXRlbUFjdGlvbl8xID0gYWN0aW9uO1xuICAgICAgICByZXR1cm4gc3RhdGUubWFwKGZ1bmN0aW9uIChvYmopIHtcbiAgICAgICAgICB2YXIgaXRlbSA9IG9iajtcbiAgICAgICAgICBpZiAoaXRlbS5pZCA9PT0gaGlnaGxpZ2h0SXRlbUFjdGlvbl8xLmlkKSB7XG4gICAgICAgICAgICBpdGVtLmhpZ2hsaWdodGVkID0gaGlnaGxpZ2h0SXRlbUFjdGlvbl8xLmhpZ2hsaWdodGVkO1xuICAgICAgICAgIH1cbiAgICAgICAgICByZXR1cm4gaXRlbTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgZGVmYXVsdDpcbiAgICAgIHtcbiAgICAgICAgcmV0dXJuIHN0YXRlO1xuICAgICAgfVxuICB9XG59XG5leHBvcnRzW1wiZGVmYXVsdFwiXSA9IGl0ZW1zO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gNTAyOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBleHBvcnRzKSB7XG5cblxuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG5leHBvcnRzLmRlZmF1bHRTdGF0ZSA9IHZvaWQgMDtcbmV4cG9ydHMuZGVmYXVsdFN0YXRlID0gZmFsc2U7XG52YXIgZ2VuZXJhbCA9IGZ1bmN0aW9uIChzdGF0ZSwgYWN0aW9uKSB7XG4gIGlmIChzdGF0ZSA9PT0gdm9pZCAwKSB7XG4gICAgc3RhdGUgPSBleHBvcnRzLmRlZmF1bHRTdGF0ZTtcbiAgfVxuICBpZiAoYWN0aW9uID09PSB2b2lkIDApIHtcbiAgICBhY3Rpb24gPSB7fTtcbiAgfVxuICBzd2l0Y2ggKGFjdGlvbi50eXBlKSB7XG4gICAgY2FzZSAnU0VUX0lTX0xPQURJTkcnOlxuICAgICAge1xuICAgICAgICByZXR1cm4gYWN0aW9uLmlzTG9hZGluZztcbiAgICAgIH1cbiAgICBkZWZhdWx0OlxuICAgICAge1xuICAgICAgICByZXR1cm4gc3RhdGU7XG4gICAgICB9XG4gIH1cbn07XG5leHBvcnRzW1wiZGVmYXVsdFwiXSA9IGdlbmVyYWw7XG5cbi8qKiovIH0pLFxuXG4vKioqLyA3NDQ6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIGV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuXG5cbnZhciBfX3NwcmVhZEFycmF5ID0gdGhpcyAmJiB0aGlzLl9fc3ByZWFkQXJyYXkgfHwgZnVuY3Rpb24gKHRvLCBmcm9tLCBwYWNrKSB7XG4gIGlmIChwYWNrIHx8IGFyZ3VtZW50cy5sZW5ndGggPT09IDIpIGZvciAodmFyIGkgPSAwLCBsID0gZnJvbS5sZW5ndGgsIGFyOyBpIDwgbDsgaSsrKSB7XG4gICAgaWYgKGFyIHx8ICEoaSBpbiBmcm9tKSkge1xuICAgICAgaWYgKCFhcikgYXIgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChmcm9tLCAwLCBpKTtcbiAgICAgIGFyW2ldID0gZnJvbVtpXTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHRvLmNvbmNhdChhciB8fCBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChmcm9tKSk7XG59O1xudmFyIF9faW1wb3J0RGVmYXVsdCA9IHRoaXMgJiYgdGhpcy5fX2ltcG9ydERlZmF1bHQgfHwgZnVuY3Rpb24gKG1vZCkge1xuICByZXR1cm4gbW9kICYmIG1vZC5fX2VzTW9kdWxlID8gbW9kIDoge1xuICAgIFwiZGVmYXVsdFwiOiBtb2RcbiAgfTtcbn07XG5PYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsICh7XG4gIHZhbHVlOiB0cnVlXG59KSk7XG4vKiBlc2xpbnQtZGlzYWJsZSBAdHlwZXNjcmlwdC1lc2xpbnQvbm8tZXhwbGljaXQtYW55ICovXG52YXIgcmVkdXhfMSA9IF9fd2VicGFja19yZXF1aXJlX18oNzkxKTtcbnZhciBpbmRleF8xID0gX19pbXBvcnREZWZhdWx0KF9fd2VicGFja19yZXF1aXJlX18oNjU1KSk7XG52YXIgU3RvcmUgPSAvKiogQGNsYXNzICovZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBTdG9yZSgpIHtcbiAgICB0aGlzLl9zdG9yZSA9ICgwLCByZWR1eF8xLmNyZWF0ZVN0b3JlKShpbmRleF8xLmRlZmF1bHQsIHdpbmRvdy5fX1JFRFVYX0RFVlRPT0xTX0VYVEVOU0lPTl9fICYmIHdpbmRvdy5fX1JFRFVYX0RFVlRPT0xTX0VYVEVOU0lPTl9fKCkpO1xuICB9XG4gIC8qKlxuICAgKiBTdWJzY3JpYmUgc3RvcmUgdG8gZnVuY3Rpb24gY2FsbCAod3JhcHBlZCBSZWR1eCBtZXRob2QpXG4gICAqL1xuICBTdG9yZS5wcm90b3R5cGUuc3Vic2NyaWJlID0gZnVuY3Rpb24gKG9uQ2hhbmdlKSB7XG4gICAgdGhpcy5fc3RvcmUuc3Vic2NyaWJlKG9uQ2hhbmdlKTtcbiAgfTtcbiAgLyoqXG4gICAqIERpc3BhdGNoIGV2ZW50IHRvIHN0b3JlICh3cmFwcGVkIFJlZHV4IG1ldGhvZClcbiAgICovXG4gIFN0b3JlLnByb3RvdHlwZS5kaXNwYXRjaCA9IGZ1bmN0aW9uIChhY3Rpb24pIHtcbiAgICB0aGlzLl9zdG9yZS5kaXNwYXRjaChhY3Rpb24pO1xuICB9O1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoU3RvcmUucHJvdG90eXBlLCBcInN0YXRlXCIsIHtcbiAgICAvKipcbiAgICAgKiBHZXQgc3RvcmUgb2JqZWN0ICh3cmFwcGluZyBSZWR1eCBtZXRob2QpXG4gICAgICovXG4gICAgZ2V0OiBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gdGhpcy5fc3RvcmUuZ2V0U3RhdGUoKTtcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KFN0b3JlLnByb3RvdHlwZSwgXCJpdGVtc1wiLCB7XG4gICAgLyoqXG4gICAgICogR2V0IGl0ZW1zIGZyb20gc3RvcmVcbiAgICAgKi9cbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiB0aGlzLnN0YXRlLml0ZW1zO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoU3RvcmUucHJvdG90eXBlLCBcImFjdGl2ZUl0ZW1zXCIsIHtcbiAgICAvKipcbiAgICAgKiBHZXQgYWN0aXZlIGl0ZW1zIGZyb20gc3RvcmVcbiAgICAgKi9cbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiB0aGlzLml0ZW1zLmZpbHRlcihmdW5jdGlvbiAoaXRlbSkge1xuICAgICAgICByZXR1cm4gaXRlbS5hY3RpdmUgPT09IHRydWU7XG4gICAgICB9KTtcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KFN0b3JlLnByb3RvdHlwZSwgXCJoaWdobGlnaHRlZEFjdGl2ZUl0ZW1zXCIsIHtcbiAgICAvKipcbiAgICAgKiBHZXQgaGlnaGxpZ2h0ZWQgaXRlbXMgZnJvbSBzdG9yZVxuICAgICAqL1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHRoaXMuaXRlbXMuZmlsdGVyKGZ1bmN0aW9uIChpdGVtKSB7XG4gICAgICAgIHJldHVybiBpdGVtLmFjdGl2ZSAmJiBpdGVtLmhpZ2hsaWdodGVkO1xuICAgICAgfSk7XG4gICAgfSxcbiAgICBlbnVtZXJhYmxlOiBmYWxzZSxcbiAgICBjb25maWd1cmFibGU6IHRydWVcbiAgfSk7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShTdG9yZS5wcm90b3R5cGUsIFwiY2hvaWNlc1wiLCB7XG4gICAgLyoqXG4gICAgICogR2V0IGNob2ljZXMgZnJvbSBzdG9yZVxuICAgICAqL1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHRoaXMuc3RhdGUuY2hvaWNlcztcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KFN0b3JlLnByb3RvdHlwZSwgXCJhY3RpdmVDaG9pY2VzXCIsIHtcbiAgICAvKipcbiAgICAgKiBHZXQgYWN0aXZlIGNob2ljZXMgZnJvbSBzdG9yZVxuICAgICAqL1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHRoaXMuY2hvaWNlcy5maWx0ZXIoZnVuY3Rpb24gKGNob2ljZSkge1xuICAgICAgICByZXR1cm4gY2hvaWNlLmFjdGl2ZSA9PT0gdHJ1ZTtcbiAgICAgIH0pO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoU3RvcmUucHJvdG90eXBlLCBcInNlbGVjdGFibGVDaG9pY2VzXCIsIHtcbiAgICAvKipcbiAgICAgKiBHZXQgc2VsZWN0YWJsZSBjaG9pY2VzIGZyb20gc3RvcmVcbiAgICAgKi9cbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiB0aGlzLmNob2ljZXMuZmlsdGVyKGZ1bmN0aW9uIChjaG9pY2UpIHtcbiAgICAgICAgcmV0dXJuIGNob2ljZS5kaXNhYmxlZCAhPT0gdHJ1ZTtcbiAgICAgIH0pO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoU3RvcmUucHJvdG90eXBlLCBcInNlYXJjaGFibGVDaG9pY2VzXCIsIHtcbiAgICAvKipcbiAgICAgKiBHZXQgY2hvaWNlcyB0aGF0IGNhbiBiZSBzZWFyY2hlZCAoZXhjbHVkaW5nIHBsYWNlaG9sZGVycylcbiAgICAgKi9cbiAgICBnZXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiB0aGlzLnNlbGVjdGFibGVDaG9pY2VzLmZpbHRlcihmdW5jdGlvbiAoY2hvaWNlKSB7XG4gICAgICAgIHJldHVybiBjaG9pY2UucGxhY2Vob2xkZXIgIT09IHRydWU7XG4gICAgICB9KTtcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KFN0b3JlLnByb3RvdHlwZSwgXCJwbGFjZWhvbGRlckNob2ljZVwiLCB7XG4gICAgLyoqXG4gICAgICogR2V0IHBsYWNlaG9sZGVyIGNob2ljZSBmcm9tIHN0b3JlXG4gICAgICovXG4gICAgZ2V0OiBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gX19zcHJlYWRBcnJheShbXSwgdGhpcy5jaG9pY2VzLCB0cnVlKS5yZXZlcnNlKCkuZmluZChmdW5jdGlvbiAoY2hvaWNlKSB7XG4gICAgICAgIHJldHVybiBjaG9pY2UucGxhY2Vob2xkZXIgPT09IHRydWU7XG4gICAgICB9KTtcbiAgICB9LFxuICAgIGVudW1lcmFibGU6IGZhbHNlLFxuICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICB9KTtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KFN0b3JlLnByb3RvdHlwZSwgXCJncm91cHNcIiwge1xuICAgIC8qKlxuICAgICAqIEdldCBncm91cHMgZnJvbSBzdG9yZVxuICAgICAqL1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHRoaXMuc3RhdGUuZ3JvdXBzO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoU3RvcmUucHJvdG90eXBlLCBcImFjdGl2ZUdyb3Vwc1wiLCB7XG4gICAgLyoqXG4gICAgICogR2V0IGFjdGl2ZSBncm91cHMgZnJvbSBzdG9yZVxuICAgICAqL1xuICAgIGdldDogZnVuY3Rpb24gKCkge1xuICAgICAgdmFyIF9hID0gdGhpcyxcbiAgICAgICAgZ3JvdXBzID0gX2EuZ3JvdXBzLFxuICAgICAgICBjaG9pY2VzID0gX2EuY2hvaWNlcztcbiAgICAgIHJldHVybiBncm91cHMuZmlsdGVyKGZ1bmN0aW9uIChncm91cCkge1xuICAgICAgICB2YXIgaXNBY3RpdmUgPSBncm91cC5hY3RpdmUgPT09IHRydWUgJiYgZ3JvdXAuZGlzYWJsZWQgPT09IGZhbHNlO1xuICAgICAgICB2YXIgaGFzQWN0aXZlT3B0aW9ucyA9IGNob2ljZXMuc29tZShmdW5jdGlvbiAoY2hvaWNlKSB7XG4gICAgICAgICAgcmV0dXJuIGNob2ljZS5hY3RpdmUgPT09IHRydWUgJiYgY2hvaWNlLmRpc2FibGVkID09PSBmYWxzZTtcbiAgICAgICAgfSk7XG4gICAgICAgIHJldHVybiBpc0FjdGl2ZSAmJiBoYXNBY3RpdmVPcHRpb25zO1xuICAgICAgfSwgW10pO1xuICAgIH0sXG4gICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgY29uZmlndXJhYmxlOiB0cnVlXG4gIH0pO1xuICAvKipcbiAgICogR2V0IGxvYWRpbmcgc3RhdGUgZnJvbSBzdG9yZVxuICAgKi9cbiAgU3RvcmUucHJvdG90eXBlLmlzTG9hZGluZyA9IGZ1bmN0aW9uICgpIHtcbiAgICByZXR1cm4gdGhpcy5zdGF0ZS5sb2FkaW5nO1xuICB9O1xuICAvKipcbiAgICogR2V0IHNpbmdsZSBjaG9pY2UgYnkgaXQncyBJRFxuICAgKi9cbiAgU3RvcmUucHJvdG90eXBlLmdldENob2ljZUJ5SWQgPSBmdW5jdGlvbiAoaWQpIHtcbiAgICByZXR1cm4gdGhpcy5hY3RpdmVDaG9pY2VzLmZpbmQoZnVuY3Rpb24gKGNob2ljZSkge1xuICAgICAgcmV0dXJuIGNob2ljZS5pZCA9PT0gcGFyc2VJbnQoaWQsIDEwKTtcbiAgICB9KTtcbiAgfTtcbiAgLyoqXG4gICAqIEdldCBncm91cCBieSBncm91cCBpZFxuICAgKi9cbiAgU3RvcmUucHJvdG90eXBlLmdldEdyb3VwQnlJZCA9IGZ1bmN0aW9uIChpZCkge1xuICAgIHJldHVybiB0aGlzLmdyb3Vwcy5maW5kKGZ1bmN0aW9uIChncm91cCkge1xuICAgICAgcmV0dXJuIGdyb3VwLmlkID09PSBpZDtcbiAgICB9KTtcbiAgfTtcbiAgcmV0dXJuIFN0b3JlO1xufSgpO1xuZXhwb3J0c1tcImRlZmF1bHRcIl0gPSBTdG9yZTtcblxuLyoqKi8gfSksXG5cbi8qKiovIDY4Njpcbi8qKiovIChmdW5jdGlvbihfX3VudXNlZF93ZWJwYWNrX21vZHVsZSwgZXhwb3J0cykge1xuXG5cblxuLyoqXG4gKiBIZWxwZXJzIHRvIGNyZWF0ZSBIVE1MIGVsZW1lbnRzIHVzZWQgYnkgQ2hvaWNlc1xuICogQ2FuIGJlIG92ZXJyaWRkZW4gYnkgcHJvdmlkaW5nIGBjYWxsYmFja09uQ3JlYXRlVGVtcGxhdGVzYCBvcHRpb25cbiAqL1xuT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCAoe1xuICB2YWx1ZTogdHJ1ZVxufSkpO1xudmFyIHRlbXBsYXRlcyA9IHtcbiAgY29udGFpbmVyT3V0ZXI6IGZ1bmN0aW9uIChfYSwgZGlyLCBpc1NlbGVjdEVsZW1lbnQsIGlzU2VsZWN0T25lRWxlbWVudCwgc2VhcmNoRW5hYmxlZCwgcGFzc2VkRWxlbWVudFR5cGUsIGxhYmVsSWQpIHtcbiAgICB2YXIgY29udGFpbmVyT3V0ZXIgPSBfYS5jbGFzc05hbWVzLmNvbnRhaW5lck91dGVyO1xuICAgIHZhciBkaXYgPSBPYmplY3QuYXNzaWduKGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpLCB7XG4gICAgICBjbGFzc05hbWU6IGNvbnRhaW5lck91dGVyXG4gICAgfSk7XG4gICAgZGl2LmRhdGFzZXQudHlwZSA9IHBhc3NlZEVsZW1lbnRUeXBlO1xuICAgIGlmIChkaXIpIHtcbiAgICAgIGRpdi5kaXIgPSBkaXI7XG4gICAgfVxuICAgIGlmIChpc1NlbGVjdE9uZUVsZW1lbnQpIHtcbiAgICAgIGRpdi50YWJJbmRleCA9IDA7XG4gICAgfVxuICAgIGlmIChpc1NlbGVjdEVsZW1lbnQpIHtcbiAgICAgIGRpdi5zZXRBdHRyaWJ1dGUoJ3JvbGUnLCBzZWFyY2hFbmFibGVkID8gJ2NvbWJvYm94JyA6ICdsaXN0Ym94Jyk7XG4gICAgICBpZiAoc2VhcmNoRW5hYmxlZCkge1xuICAgICAgICBkaXYuc2V0QXR0cmlidXRlKCdhcmlhLWF1dG9jb21wbGV0ZScsICdsaXN0Jyk7XG4gICAgICB9XG4gICAgfVxuICAgIGRpdi5zZXRBdHRyaWJ1dGUoJ2FyaWEtaGFzcG9wdXAnLCAndHJ1ZScpO1xuICAgIGRpdi5zZXRBdHRyaWJ1dGUoJ2FyaWEtZXhwYW5kZWQnLCAnZmFsc2UnKTtcbiAgICBpZiAobGFiZWxJZCkge1xuICAgICAgZGl2LnNldEF0dHJpYnV0ZSgnYXJpYS1sYWJlbGxlZGJ5JywgbGFiZWxJZCk7XG4gICAgfVxuICAgIHJldHVybiBkaXY7XG4gIH0sXG4gIGNvbnRhaW5lcklubmVyOiBmdW5jdGlvbiAoX2EpIHtcbiAgICB2YXIgY29udGFpbmVySW5uZXIgPSBfYS5jbGFzc05hbWVzLmNvbnRhaW5lcklubmVyO1xuICAgIHJldHVybiBPYmplY3QuYXNzaWduKGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpLCB7XG4gICAgICBjbGFzc05hbWU6IGNvbnRhaW5lcklubmVyXG4gICAgfSk7XG4gIH0sXG4gIGl0ZW1MaXN0OiBmdW5jdGlvbiAoX2EsIGlzU2VsZWN0T25lRWxlbWVudCkge1xuICAgIHZhciBfYiA9IF9hLmNsYXNzTmFtZXMsXG4gICAgICBsaXN0ID0gX2IubGlzdCxcbiAgICAgIGxpc3RTaW5nbGUgPSBfYi5saXN0U2luZ2xlLFxuICAgICAgbGlzdEl0ZW1zID0gX2IubGlzdEl0ZW1zO1xuICAgIHJldHVybiBPYmplY3QuYXNzaWduKGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpLCB7XG4gICAgICBjbGFzc05hbWU6IFwiXCIuY29uY2F0KGxpc3QsIFwiIFwiKS5jb25jYXQoaXNTZWxlY3RPbmVFbGVtZW50ID8gbGlzdFNpbmdsZSA6IGxpc3RJdGVtcylcbiAgICB9KTtcbiAgfSxcbiAgcGxhY2Vob2xkZXI6IGZ1bmN0aW9uIChfYSwgdmFsdWUpIHtcbiAgICB2YXIgX2I7XG4gICAgdmFyIGFsbG93SFRNTCA9IF9hLmFsbG93SFRNTCxcbiAgICAgIHBsYWNlaG9sZGVyID0gX2EuY2xhc3NOYW1lcy5wbGFjZWhvbGRlcjtcbiAgICByZXR1cm4gT2JqZWN0LmFzc2lnbihkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKSwgKF9iID0ge1xuICAgICAgY2xhc3NOYW1lOiBwbGFjZWhvbGRlclxuICAgIH0sIF9iW2FsbG93SFRNTCA/ICdpbm5lckhUTUwnIDogJ2lubmVyVGV4dCddID0gdmFsdWUsIF9iKSk7XG4gIH0sXG4gIGl0ZW06IGZ1bmN0aW9uIChfYSwgX2IsIHJlbW92ZUl0ZW1CdXR0b24pIHtcbiAgICB2YXIgX2MsIF9kO1xuICAgIHZhciBhbGxvd0hUTUwgPSBfYS5hbGxvd0hUTUwsXG4gICAgICBfZSA9IF9hLmNsYXNzTmFtZXMsXG4gICAgICBpdGVtID0gX2UuaXRlbSxcbiAgICAgIGJ1dHRvbiA9IF9lLmJ1dHRvbixcbiAgICAgIGhpZ2hsaWdodGVkU3RhdGUgPSBfZS5oaWdobGlnaHRlZFN0YXRlLFxuICAgICAgaXRlbVNlbGVjdGFibGUgPSBfZS5pdGVtU2VsZWN0YWJsZSxcbiAgICAgIHBsYWNlaG9sZGVyID0gX2UucGxhY2Vob2xkZXI7XG4gICAgdmFyIGlkID0gX2IuaWQsXG4gICAgICB2YWx1ZSA9IF9iLnZhbHVlLFxuICAgICAgbGFiZWwgPSBfYi5sYWJlbCxcbiAgICAgIGN1c3RvbVByb3BlcnRpZXMgPSBfYi5jdXN0b21Qcm9wZXJ0aWVzLFxuICAgICAgYWN0aXZlID0gX2IuYWN0aXZlLFxuICAgICAgZGlzYWJsZWQgPSBfYi5kaXNhYmxlZCxcbiAgICAgIGhpZ2hsaWdodGVkID0gX2IuaGlnaGxpZ2h0ZWQsXG4gICAgICBpc1BsYWNlaG9sZGVyID0gX2IucGxhY2Vob2xkZXI7XG4gICAgdmFyIGRpdiA9IE9iamVjdC5hc3NpZ24oZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2JyksIChfYyA9IHtcbiAgICAgIGNsYXNzTmFtZTogaXRlbVxuICAgIH0sIF9jW2FsbG93SFRNTCA/ICdpbm5lckhUTUwnIDogJ2lubmVyVGV4dCddID0gbGFiZWwsIF9jKSk7XG4gICAgT2JqZWN0LmFzc2lnbihkaXYuZGF0YXNldCwge1xuICAgICAgaXRlbTogJycsXG4gICAgICBpZDogaWQsXG4gICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICBjdXN0b21Qcm9wZXJ0aWVzOiBjdXN0b21Qcm9wZXJ0aWVzXG4gICAgfSk7XG4gICAgaWYgKGFjdGl2ZSkge1xuICAgICAgZGl2LnNldEF0dHJpYnV0ZSgnYXJpYS1zZWxlY3RlZCcsICd0cnVlJyk7XG4gICAgfVxuICAgIGlmIChkaXNhYmxlZCkge1xuICAgICAgZGl2LnNldEF0dHJpYnV0ZSgnYXJpYS1kaXNhYmxlZCcsICd0cnVlJyk7XG4gICAgfVxuICAgIGlmIChpc1BsYWNlaG9sZGVyKSB7XG4gICAgICBkaXYuY2xhc3NMaXN0LmFkZChwbGFjZWhvbGRlcik7XG4gICAgfVxuICAgIGRpdi5jbGFzc0xpc3QuYWRkKGhpZ2hsaWdodGVkID8gaGlnaGxpZ2h0ZWRTdGF0ZSA6IGl0ZW1TZWxlY3RhYmxlKTtcbiAgICBpZiAocmVtb3ZlSXRlbUJ1dHRvbikge1xuICAgICAgaWYgKGRpc2FibGVkKSB7XG4gICAgICAgIGRpdi5jbGFzc0xpc3QucmVtb3ZlKGl0ZW1TZWxlY3RhYmxlKTtcbiAgICAgIH1cbiAgICAgIGRpdi5kYXRhc2V0LmRlbGV0YWJsZSA9ICcnO1xuICAgICAgLyoqIEB0b2RvIFRoaXMgTVVTVCBiZSBsb2NhbGl6YWJsZSwgbm90IGhhcmRjb2RlZCEgKi9cbiAgICAgIHZhciBSRU1PVkVfSVRFTV9URVhUID0gJ1JlbW92ZSBpdGVtJztcbiAgICAgIHZhciByZW1vdmVCdXR0b24gPSBPYmplY3QuYXNzaWduKGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpLCAoX2QgPSB7XG4gICAgICAgIHR5cGU6ICdidXR0b24nLFxuICAgICAgICBjbGFzc05hbWU6IGJ1dHRvblxuICAgICAgfSwgX2RbYWxsb3dIVE1MID8gJ2lubmVySFRNTCcgOiAnaW5uZXJUZXh0J10gPSBSRU1PVkVfSVRFTV9URVhULCBfZCkpO1xuICAgICAgcmVtb3ZlQnV0dG9uLnNldEF0dHJpYnV0ZSgnYXJpYS1sYWJlbCcsIFwiXCIuY29uY2F0KFJFTU9WRV9JVEVNX1RFWFQsIFwiOiAnXCIpLmNvbmNhdCh2YWx1ZSwgXCInXCIpKTtcbiAgICAgIHJlbW92ZUJ1dHRvbi5kYXRhc2V0LmJ1dHRvbiA9ICcnO1xuICAgICAgZGl2LmFwcGVuZENoaWxkKHJlbW92ZUJ1dHRvbik7XG4gICAgfVxuICAgIHJldHVybiBkaXY7XG4gIH0sXG4gIGNob2ljZUxpc3Q6IGZ1bmN0aW9uIChfYSwgaXNTZWxlY3RPbmVFbGVtZW50KSB7XG4gICAgdmFyIGxpc3QgPSBfYS5jbGFzc05hbWVzLmxpc3Q7XG4gICAgdmFyIGRpdiA9IE9iamVjdC5hc3NpZ24oZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2JyksIHtcbiAgICAgIGNsYXNzTmFtZTogbGlzdFxuICAgIH0pO1xuICAgIGlmICghaXNTZWxlY3RPbmVFbGVtZW50KSB7XG4gICAgICBkaXYuc2V0QXR0cmlidXRlKCdhcmlhLW11bHRpc2VsZWN0YWJsZScsICd0cnVlJyk7XG4gICAgfVxuICAgIGRpdi5zZXRBdHRyaWJ1dGUoJ3JvbGUnLCAnbGlzdGJveCcpO1xuICAgIHJldHVybiBkaXY7XG4gIH0sXG4gIGNob2ljZUdyb3VwOiBmdW5jdGlvbiAoX2EsIF9iKSB7XG4gICAgdmFyIF9jO1xuICAgIHZhciBhbGxvd0hUTUwgPSBfYS5hbGxvd0hUTUwsXG4gICAgICBfZCA9IF9hLmNsYXNzTmFtZXMsXG4gICAgICBncm91cCA9IF9kLmdyb3VwLFxuICAgICAgZ3JvdXBIZWFkaW5nID0gX2QuZ3JvdXBIZWFkaW5nLFxuICAgICAgaXRlbURpc2FibGVkID0gX2QuaXRlbURpc2FibGVkO1xuICAgIHZhciBpZCA9IF9iLmlkLFxuICAgICAgdmFsdWUgPSBfYi52YWx1ZSxcbiAgICAgIGRpc2FibGVkID0gX2IuZGlzYWJsZWQ7XG4gICAgdmFyIGRpdiA9IE9iamVjdC5hc3NpZ24oZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2JyksIHtcbiAgICAgIGNsYXNzTmFtZTogXCJcIi5jb25jYXQoZ3JvdXAsIFwiIFwiKS5jb25jYXQoZGlzYWJsZWQgPyBpdGVtRGlzYWJsZWQgOiAnJylcbiAgICB9KTtcbiAgICBkaXYuc2V0QXR0cmlidXRlKCdyb2xlJywgJ2dyb3VwJyk7XG4gICAgT2JqZWN0LmFzc2lnbihkaXYuZGF0YXNldCwge1xuICAgICAgZ3JvdXA6ICcnLFxuICAgICAgaWQ6IGlkLFxuICAgICAgdmFsdWU6IHZhbHVlXG4gICAgfSk7XG4gICAgaWYgKGRpc2FibGVkKSB7XG4gICAgICBkaXYuc2V0QXR0cmlidXRlKCdhcmlhLWRpc2FibGVkJywgJ3RydWUnKTtcbiAgICB9XG4gICAgZGl2LmFwcGVuZENoaWxkKE9iamVjdC5hc3NpZ24oZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2JyksIChfYyA9IHtcbiAgICAgIGNsYXNzTmFtZTogZ3JvdXBIZWFkaW5nXG4gICAgfSwgX2NbYWxsb3dIVE1MID8gJ2lubmVySFRNTCcgOiAnaW5uZXJUZXh0J10gPSB2YWx1ZSwgX2MpKSk7XG4gICAgcmV0dXJuIGRpdjtcbiAgfSxcbiAgY2hvaWNlOiBmdW5jdGlvbiAoX2EsIF9iLCBzZWxlY3RUZXh0KSB7XG4gICAgdmFyIF9jO1xuICAgIHZhciBhbGxvd0hUTUwgPSBfYS5hbGxvd0hUTUwsXG4gICAgICBfZCA9IF9hLmNsYXNzTmFtZXMsXG4gICAgICBpdGVtID0gX2QuaXRlbSxcbiAgICAgIGl0ZW1DaG9pY2UgPSBfZC5pdGVtQ2hvaWNlLFxuICAgICAgaXRlbVNlbGVjdGFibGUgPSBfZC5pdGVtU2VsZWN0YWJsZSxcbiAgICAgIHNlbGVjdGVkU3RhdGUgPSBfZC5zZWxlY3RlZFN0YXRlLFxuICAgICAgaXRlbURpc2FibGVkID0gX2QuaXRlbURpc2FibGVkLFxuICAgICAgcGxhY2Vob2xkZXIgPSBfZC5wbGFjZWhvbGRlcjtcbiAgICB2YXIgaWQgPSBfYi5pZCxcbiAgICAgIHZhbHVlID0gX2IudmFsdWUsXG4gICAgICBsYWJlbCA9IF9iLmxhYmVsLFxuICAgICAgZ3JvdXBJZCA9IF9iLmdyb3VwSWQsXG4gICAgICBlbGVtZW50SWQgPSBfYi5lbGVtZW50SWQsXG4gICAgICBpc0Rpc2FibGVkID0gX2IuZGlzYWJsZWQsXG4gICAgICBpc1NlbGVjdGVkID0gX2Iuc2VsZWN0ZWQsXG4gICAgICBpc1BsYWNlaG9sZGVyID0gX2IucGxhY2Vob2xkZXI7XG4gICAgdmFyIGRpdiA9IE9iamVjdC5hc3NpZ24oZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2JyksIChfYyA9IHtcbiAgICAgIGlkOiBlbGVtZW50SWRcbiAgICB9LCBfY1thbGxvd0hUTUwgPyAnaW5uZXJIVE1MJyA6ICdpbm5lclRleHQnXSA9IGxhYmVsLCBfYy5jbGFzc05hbWUgPSBcIlwiLmNvbmNhdChpdGVtLCBcIiBcIikuY29uY2F0KGl0ZW1DaG9pY2UpLCBfYykpO1xuICAgIGlmIChpc1NlbGVjdGVkKSB7XG4gICAgICBkaXYuY2xhc3NMaXN0LmFkZChzZWxlY3RlZFN0YXRlKTtcbiAgICB9XG4gICAgaWYgKGlzUGxhY2Vob2xkZXIpIHtcbiAgICAgIGRpdi5jbGFzc0xpc3QuYWRkKHBsYWNlaG9sZGVyKTtcbiAgICB9XG4gICAgZGl2LnNldEF0dHJpYnV0ZSgncm9sZScsIGdyb3VwSWQgJiYgZ3JvdXBJZCA+IDAgPyAndHJlZWl0ZW0nIDogJ29wdGlvbicpO1xuICAgIE9iamVjdC5hc3NpZ24oZGl2LmRhdGFzZXQsIHtcbiAgICAgIGNob2ljZTogJycsXG4gICAgICBpZDogaWQsXG4gICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICBzZWxlY3RUZXh0OiBzZWxlY3RUZXh0XG4gICAgfSk7XG4gICAgaWYgKGlzRGlzYWJsZWQpIHtcbiAgICAgIGRpdi5jbGFzc0xpc3QuYWRkKGl0ZW1EaXNhYmxlZCk7XG4gICAgICBkaXYuZGF0YXNldC5jaG9pY2VEaXNhYmxlZCA9ICcnO1xuICAgICAgZGl2LnNldEF0dHJpYnV0ZSgnYXJpYS1kaXNhYmxlZCcsICd0cnVlJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIGRpdi5jbGFzc0xpc3QuYWRkKGl0ZW1TZWxlY3RhYmxlKTtcbiAgICAgIGRpdi5kYXRhc2V0LmNob2ljZVNlbGVjdGFibGUgPSAnJztcbiAgICB9XG4gICAgcmV0dXJuIGRpdjtcbiAgfSxcbiAgaW5wdXQ6IGZ1bmN0aW9uIChfYSwgcGxhY2Vob2xkZXJWYWx1ZSkge1xuICAgIHZhciBfYiA9IF9hLmNsYXNzTmFtZXMsXG4gICAgICBpbnB1dCA9IF9iLmlucHV0LFxuICAgICAgaW5wdXRDbG9uZWQgPSBfYi5pbnB1dENsb25lZDtcbiAgICB2YXIgaW5wID0gT2JqZWN0LmFzc2lnbihkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbnB1dCcpLCB7XG4gICAgICB0eXBlOiAnc2VhcmNoJyxcbiAgICAgIG5hbWU6ICdzZWFyY2hfdGVybXMnLFxuICAgICAgY2xhc3NOYW1lOiBcIlwiLmNvbmNhdChpbnB1dCwgXCIgXCIpLmNvbmNhdChpbnB1dENsb25lZCksXG4gICAgICBhdXRvY29tcGxldGU6ICdvZmYnLFxuICAgICAgYXV0b2NhcGl0YWxpemU6ICdvZmYnLFxuICAgICAgc3BlbGxjaGVjazogZmFsc2VcbiAgICB9KTtcbiAgICBpbnAuc2V0QXR0cmlidXRlKCdyb2xlJywgJ3RleHRib3gnKTtcbiAgICBpbnAuc2V0QXR0cmlidXRlKCdhcmlhLWF1dG9jb21wbGV0ZScsICdsaXN0Jyk7XG4gICAgaW5wLnNldEF0dHJpYnV0ZSgnYXJpYS1sYWJlbCcsIHBsYWNlaG9sZGVyVmFsdWUpO1xuICAgIHJldHVybiBpbnA7XG4gIH0sXG4gIGRyb3Bkb3duOiBmdW5jdGlvbiAoX2EpIHtcbiAgICB2YXIgX2IgPSBfYS5jbGFzc05hbWVzLFxuICAgICAgbGlzdCA9IF9iLmxpc3QsXG4gICAgICBsaXN0RHJvcGRvd24gPSBfYi5saXN0RHJvcGRvd247XG4gICAgdmFyIGRpdiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICAgIGRpdi5jbGFzc0xpc3QuYWRkKGxpc3QsIGxpc3REcm9wZG93bik7XG4gICAgZGl2LnNldEF0dHJpYnV0ZSgnYXJpYS1leHBhbmRlZCcsICdmYWxzZScpO1xuICAgIHJldHVybiBkaXY7XG4gIH0sXG4gIG5vdGljZTogZnVuY3Rpb24gKF9hLCBpbm5lclRleHQsIHR5cGUpIHtcbiAgICB2YXIgX2I7XG4gICAgdmFyIGFsbG93SFRNTCA9IF9hLmFsbG93SFRNTCxcbiAgICAgIF9jID0gX2EuY2xhc3NOYW1lcyxcbiAgICAgIGl0ZW0gPSBfYy5pdGVtLFxuICAgICAgaXRlbUNob2ljZSA9IF9jLml0ZW1DaG9pY2UsXG4gICAgICBub1Jlc3VsdHMgPSBfYy5ub1Jlc3VsdHMsXG4gICAgICBub0Nob2ljZXMgPSBfYy5ub0Nob2ljZXM7XG4gICAgaWYgKHR5cGUgPT09IHZvaWQgMCkge1xuICAgICAgdHlwZSA9ICcnO1xuICAgIH1cbiAgICB2YXIgY2xhc3NlcyA9IFtpdGVtLCBpdGVtQ2hvaWNlXTtcbiAgICBpZiAodHlwZSA9PT0gJ25vLWNob2ljZXMnKSB7XG4gICAgICBjbGFzc2VzLnB1c2gobm9DaG9pY2VzKTtcbiAgICB9IGVsc2UgaWYgKHR5cGUgPT09ICduby1yZXN1bHRzJykge1xuICAgICAgY2xhc3Nlcy5wdXNoKG5vUmVzdWx0cyk7XG4gICAgfVxuICAgIHJldHVybiBPYmplY3QuYXNzaWduKGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpLCAoX2IgPSB7fSwgX2JbYWxsb3dIVE1MID8gJ2lubmVySFRNTCcgOiAnaW5uZXJUZXh0J10gPSBpbm5lclRleHQsIF9iLmNsYXNzTmFtZSA9IGNsYXNzZXMuam9pbignICcpLCBfYikpO1xuICB9LFxuICBvcHRpb246IGZ1bmN0aW9uIChfYSkge1xuICAgIHZhciBsYWJlbCA9IF9hLmxhYmVsLFxuICAgICAgdmFsdWUgPSBfYS52YWx1ZSxcbiAgICAgIGN1c3RvbVByb3BlcnRpZXMgPSBfYS5jdXN0b21Qcm9wZXJ0aWVzLFxuICAgICAgYWN0aXZlID0gX2EuYWN0aXZlLFxuICAgICAgZGlzYWJsZWQgPSBfYS5kaXNhYmxlZDtcbiAgICB2YXIgb3B0ID0gbmV3IE9wdGlvbihsYWJlbCwgdmFsdWUsIGZhbHNlLCBhY3RpdmUpO1xuICAgIGlmIChjdXN0b21Qcm9wZXJ0aWVzKSB7XG4gICAgICBvcHQuZGF0YXNldC5jdXN0b21Qcm9wZXJ0aWVzID0gXCJcIi5jb25jYXQoY3VzdG9tUHJvcGVydGllcyk7XG4gICAgfVxuICAgIG9wdC5kaXNhYmxlZCA9ICEhZGlzYWJsZWQ7XG4gICAgcmV0dXJuIG9wdDtcbiAgfVxufTtcbmV4cG9ydHNbXCJkZWZhdWx0XCJdID0gdGVtcGxhdGVzO1xuXG4vKioqLyB9KSxcblxuLyoqKi8gOTk2OlxuLyoqKi8gKGZ1bmN0aW9uKG1vZHVsZSkge1xuXG5cblxudmFyIGlzTWVyZ2VhYmxlT2JqZWN0ID0gZnVuY3Rpb24gaXNNZXJnZWFibGVPYmplY3QodmFsdWUpIHtcblx0cmV0dXJuIGlzTm9uTnVsbE9iamVjdCh2YWx1ZSlcblx0XHQmJiAhaXNTcGVjaWFsKHZhbHVlKVxufTtcblxuZnVuY3Rpb24gaXNOb25OdWxsT2JqZWN0KHZhbHVlKSB7XG5cdHJldHVybiAhIXZhbHVlICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCdcbn1cblxuZnVuY3Rpb24gaXNTcGVjaWFsKHZhbHVlKSB7XG5cdHZhciBzdHJpbmdWYWx1ZSA9IE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG5cblx0cmV0dXJuIHN0cmluZ1ZhbHVlID09PSAnW29iamVjdCBSZWdFeHBdJ1xuXHRcdHx8IHN0cmluZ1ZhbHVlID09PSAnW29iamVjdCBEYXRlXSdcblx0XHR8fCBpc1JlYWN0RWxlbWVudCh2YWx1ZSlcbn1cblxuLy8gc2VlIGh0dHBzOi8vZ2l0aHViLmNvbS9mYWNlYm9vay9yZWFjdC9ibG9iL2I1YWM5NjNmYjc5MWQxMjk4ZTdmMzk2MjM2MzgzYmM5NTVmOTE2YzEvc3JjL2lzb21vcnBoaWMvY2xhc3NpYy9lbGVtZW50L1JlYWN0RWxlbWVudC5qcyNMMjEtTDI1XG52YXIgY2FuVXNlU3ltYm9sID0gdHlwZW9mIFN5bWJvbCA9PT0gJ2Z1bmN0aW9uJyAmJiBTeW1ib2wuZm9yO1xudmFyIFJFQUNUX0VMRU1FTlRfVFlQRSA9IGNhblVzZVN5bWJvbCA/IFN5bWJvbC5mb3IoJ3JlYWN0LmVsZW1lbnQnKSA6IDB4ZWFjNztcblxuZnVuY3Rpb24gaXNSZWFjdEVsZW1lbnQodmFsdWUpIHtcblx0cmV0dXJuIHZhbHVlLiQkdHlwZW9mID09PSBSRUFDVF9FTEVNRU5UX1RZUEVcbn1cblxuZnVuY3Rpb24gZW1wdHlUYXJnZXQodmFsKSB7XG5cdHJldHVybiBBcnJheS5pc0FycmF5KHZhbCkgPyBbXSA6IHt9XG59XG5cbmZ1bmN0aW9uIGNsb25lVW5sZXNzT3RoZXJ3aXNlU3BlY2lmaWVkKHZhbHVlLCBvcHRpb25zKSB7XG5cdHJldHVybiAob3B0aW9ucy5jbG9uZSAhPT0gZmFsc2UgJiYgb3B0aW9ucy5pc01lcmdlYWJsZU9iamVjdCh2YWx1ZSkpXG5cdFx0PyBkZWVwbWVyZ2UoZW1wdHlUYXJnZXQodmFsdWUpLCB2YWx1ZSwgb3B0aW9ucylcblx0XHQ6IHZhbHVlXG59XG5cbmZ1bmN0aW9uIGRlZmF1bHRBcnJheU1lcmdlKHRhcmdldCwgc291cmNlLCBvcHRpb25zKSB7XG5cdHJldHVybiB0YXJnZXQuY29uY2F0KHNvdXJjZSkubWFwKGZ1bmN0aW9uKGVsZW1lbnQpIHtcblx0XHRyZXR1cm4gY2xvbmVVbmxlc3NPdGhlcndpc2VTcGVjaWZpZWQoZWxlbWVudCwgb3B0aW9ucylcblx0fSlcbn1cblxuZnVuY3Rpb24gZ2V0TWVyZ2VGdW5jdGlvbihrZXksIG9wdGlvbnMpIHtcblx0aWYgKCFvcHRpb25zLmN1c3RvbU1lcmdlKSB7XG5cdFx0cmV0dXJuIGRlZXBtZXJnZVxuXHR9XG5cdHZhciBjdXN0b21NZXJnZSA9IG9wdGlvbnMuY3VzdG9tTWVyZ2Uoa2V5KTtcblx0cmV0dXJuIHR5cGVvZiBjdXN0b21NZXJnZSA9PT0gJ2Z1bmN0aW9uJyA/IGN1c3RvbU1lcmdlIDogZGVlcG1lcmdlXG59XG5cbmZ1bmN0aW9uIGdldEVudW1lcmFibGVPd25Qcm9wZXJ0eVN5bWJvbHModGFyZ2V0KSB7XG5cdHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzXG5cdFx0PyBPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKHRhcmdldCkuZmlsdGVyKGZ1bmN0aW9uKHN5bWJvbCkge1xuXHRcdFx0cmV0dXJuIHRhcmdldC5wcm9wZXJ0eUlzRW51bWVyYWJsZShzeW1ib2wpXG5cdFx0fSlcblx0XHQ6IFtdXG59XG5cbmZ1bmN0aW9uIGdldEtleXModGFyZ2V0KSB7XG5cdHJldHVybiBPYmplY3Qua2V5cyh0YXJnZXQpLmNvbmNhdChnZXRFbnVtZXJhYmxlT3duUHJvcGVydHlTeW1ib2xzKHRhcmdldCkpXG59XG5cbmZ1bmN0aW9uIHByb3BlcnR5SXNPbk9iamVjdChvYmplY3QsIHByb3BlcnR5KSB7XG5cdHRyeSB7XG5cdFx0cmV0dXJuIHByb3BlcnR5IGluIG9iamVjdFxuXHR9IGNhdGNoKF8pIHtcblx0XHRyZXR1cm4gZmFsc2Vcblx0fVxufVxuXG4vLyBQcm90ZWN0cyBmcm9tIHByb3RvdHlwZSBwb2lzb25pbmcgYW5kIHVuZXhwZWN0ZWQgbWVyZ2luZyB1cCB0aGUgcHJvdG90eXBlIGNoYWluLlxuZnVuY3Rpb24gcHJvcGVydHlJc1Vuc2FmZSh0YXJnZXQsIGtleSkge1xuXHRyZXR1cm4gcHJvcGVydHlJc09uT2JqZWN0KHRhcmdldCwga2V5KSAvLyBQcm9wZXJ0aWVzIGFyZSBzYWZlIHRvIG1lcmdlIGlmIHRoZXkgZG9uJ3QgZXhpc3QgaW4gdGhlIHRhcmdldCB5ZXQsXG5cdFx0JiYgIShPYmplY3QuaGFzT3duUHJvcGVydHkuY2FsbCh0YXJnZXQsIGtleSkgLy8gdW5zYWZlIGlmIHRoZXkgZXhpc3QgdXAgdGhlIHByb3RvdHlwZSBjaGFpbixcblx0XHRcdCYmIE9iamVjdC5wcm9wZXJ0eUlzRW51bWVyYWJsZS5jYWxsKHRhcmdldCwga2V5KSkgLy8gYW5kIGFsc28gdW5zYWZlIGlmIHRoZXkncmUgbm9uZW51bWVyYWJsZS5cbn1cblxuZnVuY3Rpb24gbWVyZ2VPYmplY3QodGFyZ2V0LCBzb3VyY2UsIG9wdGlvbnMpIHtcblx0dmFyIGRlc3RpbmF0aW9uID0ge307XG5cdGlmIChvcHRpb25zLmlzTWVyZ2VhYmxlT2JqZWN0KHRhcmdldCkpIHtcblx0XHRnZXRLZXlzKHRhcmdldCkuZm9yRWFjaChmdW5jdGlvbihrZXkpIHtcblx0XHRcdGRlc3RpbmF0aW9uW2tleV0gPSBjbG9uZVVubGVzc090aGVyd2lzZVNwZWNpZmllZCh0YXJnZXRba2V5XSwgb3B0aW9ucyk7XG5cdFx0fSk7XG5cdH1cblx0Z2V0S2V5cyhzb3VyY2UpLmZvckVhY2goZnVuY3Rpb24oa2V5KSB7XG5cdFx0aWYgKHByb3BlcnR5SXNVbnNhZmUodGFyZ2V0LCBrZXkpKSB7XG5cdFx0XHRyZXR1cm5cblx0XHR9XG5cblx0XHRpZiAocHJvcGVydHlJc09uT2JqZWN0KHRhcmdldCwga2V5KSAmJiBvcHRpb25zLmlzTWVyZ2VhYmxlT2JqZWN0KHNvdXJjZVtrZXldKSkge1xuXHRcdFx0ZGVzdGluYXRpb25ba2V5XSA9IGdldE1lcmdlRnVuY3Rpb24oa2V5LCBvcHRpb25zKSh0YXJnZXRba2V5XSwgc291cmNlW2tleV0sIG9wdGlvbnMpO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHRkZXN0aW5hdGlvbltrZXldID0gY2xvbmVVbmxlc3NPdGhlcndpc2VTcGVjaWZpZWQoc291cmNlW2tleV0sIG9wdGlvbnMpO1xuXHRcdH1cblx0fSk7XG5cdHJldHVybiBkZXN0aW5hdGlvblxufVxuXG5mdW5jdGlvbiBkZWVwbWVyZ2UodGFyZ2V0LCBzb3VyY2UsIG9wdGlvbnMpIHtcblx0b3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cdG9wdGlvbnMuYXJyYXlNZXJnZSA9IG9wdGlvbnMuYXJyYXlNZXJnZSB8fCBkZWZhdWx0QXJyYXlNZXJnZTtcblx0b3B0aW9ucy5pc01lcmdlYWJsZU9iamVjdCA9IG9wdGlvbnMuaXNNZXJnZWFibGVPYmplY3QgfHwgaXNNZXJnZWFibGVPYmplY3Q7XG5cdC8vIGNsb25lVW5sZXNzT3RoZXJ3aXNlU3BlY2lmaWVkIGlzIGFkZGVkIHRvIGBvcHRpb25zYCBzbyB0aGF0IGN1c3RvbSBhcnJheU1lcmdlKClcblx0Ly8gaW1wbGVtZW50YXRpb25zIGNhbiB1c2UgaXQuIFRoZSBjYWxsZXIgbWF5IG5vdCByZXBsYWNlIGl0LlxuXHRvcHRpb25zLmNsb25lVW5sZXNzT3RoZXJ3aXNlU3BlY2lmaWVkID0gY2xvbmVVbmxlc3NPdGhlcndpc2VTcGVjaWZpZWQ7XG5cblx0dmFyIHNvdXJjZUlzQXJyYXkgPSBBcnJheS5pc0FycmF5KHNvdXJjZSk7XG5cdHZhciB0YXJnZXRJc0FycmF5ID0gQXJyYXkuaXNBcnJheSh0YXJnZXQpO1xuXHR2YXIgc291cmNlQW5kVGFyZ2V0VHlwZXNNYXRjaCA9IHNvdXJjZUlzQXJyYXkgPT09IHRhcmdldElzQXJyYXk7XG5cblx0aWYgKCFzb3VyY2VBbmRUYXJnZXRUeXBlc01hdGNoKSB7XG5cdFx0cmV0dXJuIGNsb25lVW5sZXNzT3RoZXJ3aXNlU3BlY2lmaWVkKHNvdXJjZSwgb3B0aW9ucylcblx0fSBlbHNlIGlmIChzb3VyY2VJc0FycmF5KSB7XG5cdFx0cmV0dXJuIG9wdGlvbnMuYXJyYXlNZXJnZSh0YXJnZXQsIHNvdXJjZSwgb3B0aW9ucylcblx0fSBlbHNlIHtcblx0XHRyZXR1cm4gbWVyZ2VPYmplY3QodGFyZ2V0LCBzb3VyY2UsIG9wdGlvbnMpXG5cdH1cbn1cblxuZGVlcG1lcmdlLmFsbCA9IGZ1bmN0aW9uIGRlZXBtZXJnZUFsbChhcnJheSwgb3B0aW9ucykge1xuXHRpZiAoIUFycmF5LmlzQXJyYXkoYXJyYXkpKSB7XG5cdFx0dGhyb3cgbmV3IEVycm9yKCdmaXJzdCBhcmd1bWVudCBzaG91bGQgYmUgYW4gYXJyYXknKVxuXHR9XG5cblx0cmV0dXJuIGFycmF5LnJlZHVjZShmdW5jdGlvbihwcmV2LCBuZXh0KSB7XG5cdFx0cmV0dXJuIGRlZXBtZXJnZShwcmV2LCBuZXh0LCBvcHRpb25zKVxuXHR9LCB7fSlcbn07XG5cbnZhciBkZWVwbWVyZ2VfMSA9IGRlZXBtZXJnZTtcblxubW9kdWxlLmV4cG9ydHMgPSBkZWVwbWVyZ2VfMTtcblxuXG4vKioqLyB9KSxcblxuLyoqKi8gMjIxOlxuLyoqKi8gKGZ1bmN0aW9uKF9fdW51c2VkX3dlYnBhY2tfbW9kdWxlLCBfX3dlYnBhY2tfZXhwb3J0c19fLCBfX3dlYnBhY2tfcmVxdWlyZV9fKSB7XG5cbl9fd2VicGFja19yZXF1aXJlX18ucihfX3dlYnBhY2tfZXhwb3J0c19fKTtcbi8qIGhhcm1vbnkgZXhwb3J0ICovIF9fd2VicGFja19yZXF1aXJlX18uZChfX3dlYnBhY2tfZXhwb3J0c19fLCB7XG4vKiBoYXJtb255IGV4cG9ydCAqLyAgIFwiZGVmYXVsdFwiOiBmdW5jdGlvbigpIHsgcmV0dXJuIC8qIGJpbmRpbmcgKi8gRnVzZTsgfVxuLyogaGFybW9ueSBleHBvcnQgKi8gfSk7XG4vKipcbiAqIEZ1c2UuanMgdjYuNi4yIC0gTGlnaHR3ZWlnaHQgZnV6enktc2VhcmNoIChodHRwOi8vZnVzZWpzLmlvKVxuICpcbiAqIENvcHlyaWdodCAoYykgMjAyMiBLaXJvIFJpc2sgKGh0dHA6Ly9raXJvLm1lKVxuICogQWxsIFJpZ2h0cyBSZXNlcnZlZC4gQXBhY2hlIFNvZnR3YXJlIExpY2Vuc2UgMi4wXG4gKlxuICogaHR0cDovL3d3dy5hcGFjaGUub3JnL2xpY2Vuc2VzL0xJQ0VOU0UtMi4wXG4gKi9cblxuZnVuY3Rpb24gaXNBcnJheSh2YWx1ZSkge1xuICByZXR1cm4gIUFycmF5LmlzQXJyYXlcbiAgICA/IGdldFRhZyh2YWx1ZSkgPT09ICdbb2JqZWN0IEFycmF5XSdcbiAgICA6IEFycmF5LmlzQXJyYXkodmFsdWUpXG59XG5cbi8vIEFkYXB0ZWQgZnJvbTogaHR0cHM6Ly9naXRodWIuY29tL2xvZGFzaC9sb2Rhc2gvYmxvYi9tYXN0ZXIvLmludGVybmFsL2Jhc2VUb1N0cmluZy5qc1xuY29uc3QgSU5GSU5JVFkgPSAxIC8gMDtcbmZ1bmN0aW9uIGJhc2VUb1N0cmluZyh2YWx1ZSkge1xuICAvLyBFeGl0IGVhcmx5IGZvciBzdHJpbmdzIHRvIGF2b2lkIGEgcGVyZm9ybWFuY2UgaGl0IGluIHNvbWUgZW52aXJvbm1lbnRzLlxuICBpZiAodHlwZW9mIHZhbHVlID09ICdzdHJpbmcnKSB7XG4gICAgcmV0dXJuIHZhbHVlXG4gIH1cbiAgbGV0IHJlc3VsdCA9IHZhbHVlICsgJyc7XG4gIHJldHVybiByZXN1bHQgPT0gJzAnICYmIDEgLyB2YWx1ZSA9PSAtSU5GSU5JVFkgPyAnLTAnIDogcmVzdWx0XG59XG5cbmZ1bmN0aW9uIHRvU3RyaW5nKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSA9PSBudWxsID8gJycgOiBiYXNlVG9TdHJpbmcodmFsdWUpXG59XG5cbmZ1bmN0aW9uIGlzU3RyaW5nKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdzdHJpbmcnXG59XG5cbmZ1bmN0aW9uIGlzTnVtYmVyKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdudW1iZXInXG59XG5cbi8vIEFkYXB0ZWQgZnJvbTogaHR0cHM6Ly9naXRodWIuY29tL2xvZGFzaC9sb2Rhc2gvYmxvYi9tYXN0ZXIvaXNCb29sZWFuLmpzXG5mdW5jdGlvbiBpc0Jvb2xlYW4odmFsdWUpIHtcbiAgcmV0dXJuIChcbiAgICB2YWx1ZSA9PT0gdHJ1ZSB8fFxuICAgIHZhbHVlID09PSBmYWxzZSB8fFxuICAgIChpc09iamVjdExpa2UodmFsdWUpICYmIGdldFRhZyh2YWx1ZSkgPT0gJ1tvYmplY3QgQm9vbGVhbl0nKVxuICApXG59XG5cbmZ1bmN0aW9uIGlzT2JqZWN0KHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnXG59XG5cbi8vIENoZWNrcyBpZiBgdmFsdWVgIGlzIG9iamVjdC1saWtlLlxuZnVuY3Rpb24gaXNPYmplY3RMaWtlKHZhbHVlKSB7XG4gIHJldHVybiBpc09iamVjdCh2YWx1ZSkgJiYgdmFsdWUgIT09IG51bGxcbn1cblxuZnVuY3Rpb24gaXNEZWZpbmVkKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPT0gdW5kZWZpbmVkICYmIHZhbHVlICE9PSBudWxsXG59XG5cbmZ1bmN0aW9uIGlzQmxhbmsodmFsdWUpIHtcbiAgcmV0dXJuICF2YWx1ZS50cmltKCkubGVuZ3RoXG59XG5cbi8vIEdldHMgdGhlIGB0b1N0cmluZ1RhZ2Agb2YgYHZhbHVlYC5cbi8vIEFkYXB0ZWQgZnJvbTogaHR0cHM6Ly9naXRodWIuY29tL2xvZGFzaC9sb2Rhc2gvYmxvYi9tYXN0ZXIvLmludGVybmFsL2dldFRhZy5qc1xuZnVuY3Rpb24gZ2V0VGFnKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSA9PSBudWxsXG4gICAgPyB2YWx1ZSA9PT0gdW5kZWZpbmVkXG4gICAgICA/ICdbb2JqZWN0IFVuZGVmaW5lZF0nXG4gICAgICA6ICdbb2JqZWN0IE51bGxdJ1xuICAgIDogT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKHZhbHVlKVxufVxuXG5jb25zdCBFWFRFTkRFRF9TRUFSQ0hfVU5BVkFJTEFCTEUgPSAnRXh0ZW5kZWQgc2VhcmNoIGlzIG5vdCBhdmFpbGFibGUnO1xuXG5jb25zdCBJTkNPUlJFQ1RfSU5ERVhfVFlQRSA9IFwiSW5jb3JyZWN0ICdpbmRleCcgdHlwZVwiO1xuXG5jb25zdCBMT0dJQ0FMX1NFQVJDSF9JTlZBTElEX1FVRVJZX0ZPUl9LRVkgPSAoa2V5KSA9PlxuICBgSW52YWxpZCB2YWx1ZSBmb3Iga2V5ICR7a2V5fWA7XG5cbmNvbnN0IFBBVFRFUk5fTEVOR1RIX1RPT19MQVJHRSA9IChtYXgpID0+XG4gIGBQYXR0ZXJuIGxlbmd0aCBleGNlZWRzIG1heCBvZiAke21heH0uYDtcblxuY29uc3QgTUlTU0lOR19LRVlfUFJPUEVSVFkgPSAobmFtZSkgPT4gYE1pc3NpbmcgJHtuYW1lfSBwcm9wZXJ0eSBpbiBrZXlgO1xuXG5jb25zdCBJTlZBTElEX0tFWV9XRUlHSFRfVkFMVUUgPSAoa2V5KSA9PlxuICBgUHJvcGVydHkgJ3dlaWdodCcgaW4ga2V5ICcke2tleX0nIG11c3QgYmUgYSBwb3NpdGl2ZSBpbnRlZ2VyYDtcblxuY29uc3QgaGFzT3duID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eTtcblxuY2xhc3MgS2V5U3RvcmUge1xuICBjb25zdHJ1Y3RvcihrZXlzKSB7XG4gICAgdGhpcy5fa2V5cyA9IFtdO1xuICAgIHRoaXMuX2tleU1hcCA9IHt9O1xuXG4gICAgbGV0IHRvdGFsV2VpZ2h0ID0gMDtcblxuICAgIGtleXMuZm9yRWFjaCgoa2V5KSA9PiB7XG4gICAgICBsZXQgb2JqID0gY3JlYXRlS2V5KGtleSk7XG5cbiAgICAgIHRvdGFsV2VpZ2h0ICs9IG9iai53ZWlnaHQ7XG5cbiAgICAgIHRoaXMuX2tleXMucHVzaChvYmopO1xuICAgICAgdGhpcy5fa2V5TWFwW29iai5pZF0gPSBvYmo7XG5cbiAgICAgIHRvdGFsV2VpZ2h0ICs9IG9iai53ZWlnaHQ7XG4gICAgfSk7XG5cbiAgICAvLyBOb3JtYWxpemUgd2VpZ2h0cyBzbyB0aGF0IHRoZWlyIHN1bSBpcyBlcXVhbCB0byAxXG4gICAgdGhpcy5fa2V5cy5mb3JFYWNoKChrZXkpID0+IHtcbiAgICAgIGtleS53ZWlnaHQgLz0gdG90YWxXZWlnaHQ7XG4gICAgfSk7XG4gIH1cbiAgZ2V0KGtleUlkKSB7XG4gICAgcmV0dXJuIHRoaXMuX2tleU1hcFtrZXlJZF1cbiAgfVxuICBrZXlzKCkge1xuICAgIHJldHVybiB0aGlzLl9rZXlzXG4gIH1cbiAgdG9KU09OKCkge1xuICAgIHJldHVybiBKU09OLnN0cmluZ2lmeSh0aGlzLl9rZXlzKVxuICB9XG59XG5cbmZ1bmN0aW9uIGNyZWF0ZUtleShrZXkpIHtcbiAgbGV0IHBhdGggPSBudWxsO1xuICBsZXQgaWQgPSBudWxsO1xuICBsZXQgc3JjID0gbnVsbDtcbiAgbGV0IHdlaWdodCA9IDE7XG4gIGxldCBnZXRGbiA9IG51bGw7XG5cbiAgaWYgKGlzU3RyaW5nKGtleSkgfHwgaXNBcnJheShrZXkpKSB7XG4gICAgc3JjID0ga2V5O1xuICAgIHBhdGggPSBjcmVhdGVLZXlQYXRoKGtleSk7XG4gICAgaWQgPSBjcmVhdGVLZXlJZChrZXkpO1xuICB9IGVsc2Uge1xuICAgIGlmICghaGFzT3duLmNhbGwoa2V5LCAnbmFtZScpKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoTUlTU0lOR19LRVlfUFJPUEVSVFkoJ25hbWUnKSlcbiAgICB9XG5cbiAgICBjb25zdCBuYW1lID0ga2V5Lm5hbWU7XG4gICAgc3JjID0gbmFtZTtcblxuICAgIGlmIChoYXNPd24uY2FsbChrZXksICd3ZWlnaHQnKSkge1xuICAgICAgd2VpZ2h0ID0ga2V5LndlaWdodDtcblxuICAgICAgaWYgKHdlaWdodCA8PSAwKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvcihJTlZBTElEX0tFWV9XRUlHSFRfVkFMVUUobmFtZSkpXG4gICAgICB9XG4gICAgfVxuXG4gICAgcGF0aCA9IGNyZWF0ZUtleVBhdGgobmFtZSk7XG4gICAgaWQgPSBjcmVhdGVLZXlJZChuYW1lKTtcbiAgICBnZXRGbiA9IGtleS5nZXRGbjtcbiAgfVxuXG4gIHJldHVybiB7IHBhdGgsIGlkLCB3ZWlnaHQsIHNyYywgZ2V0Rm4gfVxufVxuXG5mdW5jdGlvbiBjcmVhdGVLZXlQYXRoKGtleSkge1xuICByZXR1cm4gaXNBcnJheShrZXkpID8ga2V5IDoga2V5LnNwbGl0KCcuJylcbn1cblxuZnVuY3Rpb24gY3JlYXRlS2V5SWQoa2V5KSB7XG4gIHJldHVybiBpc0FycmF5KGtleSkgPyBrZXkuam9pbignLicpIDoga2V5XG59XG5cbmZ1bmN0aW9uIGdldChvYmosIHBhdGgpIHtcbiAgbGV0IGxpc3QgPSBbXTtcbiAgbGV0IGFyciA9IGZhbHNlO1xuXG4gIGNvbnN0IGRlZXBHZXQgPSAob2JqLCBwYXRoLCBpbmRleCkgPT4ge1xuICAgIGlmICghaXNEZWZpbmVkKG9iaikpIHtcbiAgICAgIHJldHVyblxuICAgIH1cbiAgICBpZiAoIXBhdGhbaW5kZXhdKSB7XG4gICAgICAvLyBJZiB0aGVyZSdzIG5vIHBhdGggbGVmdCwgd2UndmUgYXJyaXZlZCBhdCB0aGUgb2JqZWN0IHdlIGNhcmUgYWJvdXQuXG4gICAgICBsaXN0LnB1c2gob2JqKTtcbiAgICB9IGVsc2Uge1xuICAgICAgbGV0IGtleSA9IHBhdGhbaW5kZXhdO1xuXG4gICAgICBjb25zdCB2YWx1ZSA9IG9ialtrZXldO1xuXG4gICAgICBpZiAoIWlzRGVmaW5lZCh2YWx1ZSkpIHtcbiAgICAgICAgcmV0dXJuXG4gICAgICB9XG5cbiAgICAgIC8vIElmIHdlJ3JlIGF0IHRoZSBsYXN0IHZhbHVlIGluIHRoZSBwYXRoLCBhbmQgaWYgaXQncyBhIHN0cmluZy9udW1iZXIvYm9vbCxcbiAgICAgIC8vIGFkZCBpdCB0byB0aGUgbGlzdFxuICAgICAgaWYgKFxuICAgICAgICBpbmRleCA9PT0gcGF0aC5sZW5ndGggLSAxICYmXG4gICAgICAgIChpc1N0cmluZyh2YWx1ZSkgfHwgaXNOdW1iZXIodmFsdWUpIHx8IGlzQm9vbGVhbih2YWx1ZSkpXG4gICAgICApIHtcbiAgICAgICAgbGlzdC5wdXNoKHRvU3RyaW5nKHZhbHVlKSk7XG4gICAgICB9IGVsc2UgaWYgKGlzQXJyYXkodmFsdWUpKSB7XG4gICAgICAgIGFyciA9IHRydWU7XG4gICAgICAgIC8vIFNlYXJjaCBlYWNoIGl0ZW0gaW4gdGhlIGFycmF5LlxuICAgICAgICBmb3IgKGxldCBpID0gMCwgbGVuID0gdmFsdWUubGVuZ3RoOyBpIDwgbGVuOyBpICs9IDEpIHtcbiAgICAgICAgICBkZWVwR2V0KHZhbHVlW2ldLCBwYXRoLCBpbmRleCArIDEpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKHBhdGgubGVuZ3RoKSB7XG4gICAgICAgIC8vIEFuIG9iamVjdC4gUmVjdXJzZSBmdXJ0aGVyLlxuICAgICAgICBkZWVwR2V0KHZhbHVlLCBwYXRoLCBpbmRleCArIDEpO1xuICAgICAgfVxuICAgIH1cbiAgfTtcblxuICAvLyBCYWNrd2FyZHMgY29tcGF0aWJpbGl0eSAoc2luY2UgcGF0aCB1c2VkIHRvIGJlIGEgc3RyaW5nKVxuICBkZWVwR2V0KG9iaiwgaXNTdHJpbmcocGF0aCkgPyBwYXRoLnNwbGl0KCcuJykgOiBwYXRoLCAwKTtcblxuICByZXR1cm4gYXJyID8gbGlzdCA6IGxpc3RbMF1cbn1cblxuY29uc3QgTWF0Y2hPcHRpb25zID0ge1xuICAvLyBXaGV0aGVyIHRoZSBtYXRjaGVzIHNob3VsZCBiZSBpbmNsdWRlZCBpbiB0aGUgcmVzdWx0IHNldC4gV2hlbiBgdHJ1ZWAsIGVhY2ggcmVjb3JkIGluIHRoZSByZXN1bHRcbiAgLy8gc2V0IHdpbGwgaW5jbHVkZSB0aGUgaW5kaWNlcyBvZiB0aGUgbWF0Y2hlZCBjaGFyYWN0ZXJzLlxuICAvLyBUaGVzZSBjYW4gY29uc2VxdWVudGx5IGJlIHVzZWQgZm9yIGhpZ2hsaWdodGluZyBwdXJwb3Nlcy5cbiAgaW5jbHVkZU1hdGNoZXM6IGZhbHNlLFxuICAvLyBXaGVuIGB0cnVlYCwgdGhlIG1hdGNoaW5nIGZ1bmN0aW9uIHdpbGwgY29udGludWUgdG8gdGhlIGVuZCBvZiBhIHNlYXJjaCBwYXR0ZXJuIGV2ZW4gaWZcbiAgLy8gYSBwZXJmZWN0IG1hdGNoIGhhcyBhbHJlYWR5IGJlZW4gbG9jYXRlZCBpbiB0aGUgc3RyaW5nLlxuICBmaW5kQWxsTWF0Y2hlczogZmFsc2UsXG4gIC8vIE1pbmltdW0gbnVtYmVyIG9mIGNoYXJhY3RlcnMgdGhhdCBtdXN0IGJlIG1hdGNoZWQgYmVmb3JlIGEgcmVzdWx0IGlzIGNvbnNpZGVyZWQgYSBtYXRjaFxuICBtaW5NYXRjaENoYXJMZW5ndGg6IDFcbn07XG5cbmNvbnN0IEJhc2ljT3B0aW9ucyA9IHtcbiAgLy8gV2hlbiBgdHJ1ZWAsIHRoZSBhbGdvcml0aG0gY29udGludWVzIHNlYXJjaGluZyB0byB0aGUgZW5kIG9mIHRoZSBpbnB1dCBldmVuIGlmIGEgcGVyZmVjdFxuICAvLyBtYXRjaCBpcyBmb3VuZCBiZWZvcmUgdGhlIGVuZCBvZiB0aGUgc2FtZSBpbnB1dC5cbiAgaXNDYXNlU2Vuc2l0aXZlOiBmYWxzZSxcbiAgLy8gV2hlbiB0cnVlLCB0aGUgbWF0Y2hpbmcgZnVuY3Rpb24gd2lsbCBjb250aW51ZSB0byB0aGUgZW5kIG9mIGEgc2VhcmNoIHBhdHRlcm4gZXZlbiBpZlxuICBpbmNsdWRlU2NvcmU6IGZhbHNlLFxuICAvLyBMaXN0IG9mIHByb3BlcnRpZXMgdGhhdCB3aWxsIGJlIHNlYXJjaGVkLiBUaGlzIGFsc28gc3VwcG9ydHMgbmVzdGVkIHByb3BlcnRpZXMuXG4gIGtleXM6IFtdLFxuICAvLyBXaGV0aGVyIHRvIHNvcnQgdGhlIHJlc3VsdCBsaXN0LCBieSBzY29yZVxuICBzaG91bGRTb3J0OiB0cnVlLFxuICAvLyBEZWZhdWx0IHNvcnQgZnVuY3Rpb246IHNvcnQgYnkgYXNjZW5kaW5nIHNjb3JlLCBhc2NlbmRpbmcgaW5kZXhcbiAgc29ydEZuOiAoYSwgYikgPT5cbiAgICBhLnNjb3JlID09PSBiLnNjb3JlID8gKGEuaWR4IDwgYi5pZHggPyAtMSA6IDEpIDogYS5zY29yZSA8IGIuc2NvcmUgPyAtMSA6IDFcbn07XG5cbmNvbnN0IEZ1enp5T3B0aW9ucyA9IHtcbiAgLy8gQXBwcm94aW1hdGVseSB3aGVyZSBpbiB0aGUgdGV4dCBpcyB0aGUgcGF0dGVybiBleHBlY3RlZCB0byBiZSBmb3VuZD9cbiAgbG9jYXRpb246IDAsXG4gIC8vIEF0IHdoYXQgcG9pbnQgZG9lcyB0aGUgbWF0Y2ggYWxnb3JpdGhtIGdpdmUgdXAuIEEgdGhyZXNob2xkIG9mICcwLjAnIHJlcXVpcmVzIGEgcGVyZmVjdCBtYXRjaFxuICAvLyAob2YgYm90aCBsZXR0ZXJzIGFuZCBsb2NhdGlvbiksIGEgdGhyZXNob2xkIG9mICcxLjAnIHdvdWxkIG1hdGNoIGFueXRoaW5nLlxuICB0aHJlc2hvbGQ6IDAuNixcbiAgLy8gRGV0ZXJtaW5lcyBob3cgY2xvc2UgdGhlIG1hdGNoIG11c3QgYmUgdG8gdGhlIGZ1enp5IGxvY2F0aW9uIChzcGVjaWZpZWQgYWJvdmUpLlxuICAvLyBBbiBleGFjdCBsZXR0ZXIgbWF0Y2ggd2hpY2ggaXMgJ2Rpc3RhbmNlJyBjaGFyYWN0ZXJzIGF3YXkgZnJvbSB0aGUgZnV6enkgbG9jYXRpb25cbiAgLy8gd291bGQgc2NvcmUgYXMgYSBjb21wbGV0ZSBtaXNtYXRjaC4gQSBkaXN0YW5jZSBvZiAnMCcgcmVxdWlyZXMgdGhlIG1hdGNoIGJlIGF0XG4gIC8vIHRoZSBleGFjdCBsb2NhdGlvbiBzcGVjaWZpZWQsIGEgdGhyZXNob2xkIG9mICcxMDAwJyB3b3VsZCByZXF1aXJlIGEgcGVyZmVjdCBtYXRjaFxuICAvLyB0byBiZSB3aXRoaW4gODAwIGNoYXJhY3RlcnMgb2YgdGhlIGZ1enp5IGxvY2F0aW9uIHRvIGJlIGZvdW5kIHVzaW5nIGEgMC44IHRocmVzaG9sZC5cbiAgZGlzdGFuY2U6IDEwMFxufTtcblxuY29uc3QgQWR2YW5jZWRPcHRpb25zID0ge1xuICAvLyBXaGVuIGB0cnVlYCwgaXQgZW5hYmxlcyB0aGUgdXNlIG9mIHVuaXgtbGlrZSBzZWFyY2ggY29tbWFuZHNcbiAgdXNlRXh0ZW5kZWRTZWFyY2g6IGZhbHNlLFxuICAvLyBUaGUgZ2V0IGZ1bmN0aW9uIHRvIHVzZSB3aGVuIGZldGNoaW5nIGFuIG9iamVjdCdzIHByb3BlcnRpZXMuXG4gIC8vIFRoZSBkZWZhdWx0IHdpbGwgc2VhcmNoIG5lc3RlZCBwYXRocyAqaWUgZm9vLmJhci5iYXoqXG4gIGdldEZuOiBnZXQsXG4gIC8vIFdoZW4gYHRydWVgLCBzZWFyY2ggd2lsbCBpZ25vcmUgYGxvY2F0aW9uYCBhbmQgYGRpc3RhbmNlYCwgc28gaXQgd29uJ3QgbWF0dGVyXG4gIC8vIHdoZXJlIGluIHRoZSBzdHJpbmcgdGhlIHBhdHRlcm4gYXBwZWFycy5cbiAgLy8gTW9yZSBpbmZvOiBodHRwczovL2Z1c2Vqcy5pby9jb25jZXB0cy9zY29yaW5nLXRoZW9yeS5odG1sI2Z1enppbmVzcy1zY29yZVxuICBpZ25vcmVMb2NhdGlvbjogZmFsc2UsXG4gIC8vIFdoZW4gYHRydWVgLCB0aGUgY2FsY3VsYXRpb24gZm9yIHRoZSByZWxldmFuY2Ugc2NvcmUgKHVzZWQgZm9yIHNvcnRpbmcpIHdpbGxcbiAgLy8gaWdub3JlIHRoZSBmaWVsZC1sZW5ndGggbm9ybS5cbiAgLy8gTW9yZSBpbmZvOiBodHRwczovL2Z1c2Vqcy5pby9jb25jZXB0cy9zY29yaW5nLXRoZW9yeS5odG1sI2ZpZWxkLWxlbmd0aC1ub3JtXG4gIGlnbm9yZUZpZWxkTm9ybTogZmFsc2UsXG4gIC8vIFRoZSB3ZWlnaHQgdG8gZGV0ZXJtaW5lIGhvdyBtdWNoIGZpZWxkIGxlbmd0aCBub3JtIGVmZmVjdHMgc2NvcmluZy5cbiAgZmllbGROb3JtV2VpZ2h0OiAxXG59O1xuXG52YXIgQ29uZmlnID0ge1xuICAuLi5CYXNpY09wdGlvbnMsXG4gIC4uLk1hdGNoT3B0aW9ucyxcbiAgLi4uRnV6enlPcHRpb25zLFxuICAuLi5BZHZhbmNlZE9wdGlvbnNcbn07XG5cbmNvbnN0IFNQQUNFID0gL1teIF0rL2c7XG5cbi8vIEZpZWxkLWxlbmd0aCBub3JtOiB0aGUgc2hvcnRlciB0aGUgZmllbGQsIHRoZSBoaWdoZXIgdGhlIHdlaWdodC5cbi8vIFNldCB0byAzIGRlY2ltYWxzIHRvIHJlZHVjZSBpbmRleCBzaXplLlxuZnVuY3Rpb24gbm9ybSh3ZWlnaHQgPSAxLCBtYW50aXNzYSA9IDMpIHtcbiAgY29uc3QgY2FjaGUgPSBuZXcgTWFwKCk7XG4gIGNvbnN0IG0gPSBNYXRoLnBvdygxMCwgbWFudGlzc2EpO1xuXG4gIHJldHVybiB7XG4gICAgZ2V0KHZhbHVlKSB7XG4gICAgICBjb25zdCBudW1Ub2tlbnMgPSB2YWx1ZS5tYXRjaChTUEFDRSkubGVuZ3RoO1xuXG4gICAgICBpZiAoY2FjaGUuaGFzKG51bVRva2VucykpIHtcbiAgICAgICAgcmV0dXJuIGNhY2hlLmdldChudW1Ub2tlbnMpXG4gICAgICB9XG5cbiAgICAgIC8vIERlZmF1bHQgZnVuY3Rpb24gaXMgMS9zcXJ0KHgpLCB3ZWlnaHQgbWFrZXMgdGhhdCB2YXJpYWJsZVxuICAgICAgY29uc3Qgbm9ybSA9IDEgLyBNYXRoLnBvdyhudW1Ub2tlbnMsIDAuNSAqIHdlaWdodCk7XG5cbiAgICAgIC8vIEluIHBsYWNlIG9mIGB0b0ZpeGVkKG1hbnRpc3NhKWAsIGZvciBmYXN0ZXIgY29tcHV0YXRpb25cbiAgICAgIGNvbnN0IG4gPSBwYXJzZUZsb2F0KE1hdGgucm91bmQobm9ybSAqIG0pIC8gbSk7XG5cbiAgICAgIGNhY2hlLnNldChudW1Ub2tlbnMsIG4pO1xuXG4gICAgICByZXR1cm4gblxuICAgIH0sXG4gICAgY2xlYXIoKSB7XG4gICAgICBjYWNoZS5jbGVhcigpO1xuICAgIH1cbiAgfVxufVxuXG5jbGFzcyBGdXNlSW5kZXgge1xuICBjb25zdHJ1Y3Rvcih7XG4gICAgZ2V0Rm4gPSBDb25maWcuZ2V0Rm4sXG4gICAgZmllbGROb3JtV2VpZ2h0ID0gQ29uZmlnLmZpZWxkTm9ybVdlaWdodFxuICB9ID0ge30pIHtcbiAgICB0aGlzLm5vcm0gPSBub3JtKGZpZWxkTm9ybVdlaWdodCwgMyk7XG4gICAgdGhpcy5nZXRGbiA9IGdldEZuO1xuICAgIHRoaXMuaXNDcmVhdGVkID0gZmFsc2U7XG5cbiAgICB0aGlzLnNldEluZGV4UmVjb3JkcygpO1xuICB9XG4gIHNldFNvdXJjZXMoZG9jcyA9IFtdKSB7XG4gICAgdGhpcy5kb2NzID0gZG9jcztcbiAgfVxuICBzZXRJbmRleFJlY29yZHMocmVjb3JkcyA9IFtdKSB7XG4gICAgdGhpcy5yZWNvcmRzID0gcmVjb3JkcztcbiAgfVxuICBzZXRLZXlzKGtleXMgPSBbXSkge1xuICAgIHRoaXMua2V5cyA9IGtleXM7XG4gICAgdGhpcy5fa2V5c01hcCA9IHt9O1xuICAgIGtleXMuZm9yRWFjaCgoa2V5LCBpZHgpID0+IHtcbiAgICAgIHRoaXMuX2tleXNNYXBba2V5LmlkXSA9IGlkeDtcbiAgICB9KTtcbiAgfVxuICBjcmVhdGUoKSB7XG4gICAgaWYgKHRoaXMuaXNDcmVhdGVkIHx8ICF0aGlzLmRvY3MubGVuZ3RoKSB7XG4gICAgICByZXR1cm5cbiAgICB9XG5cbiAgICB0aGlzLmlzQ3JlYXRlZCA9IHRydWU7XG5cbiAgICAvLyBMaXN0IGlzIEFycmF5PFN0cmluZz5cbiAgICBpZiAoaXNTdHJpbmcodGhpcy5kb2NzWzBdKSkge1xuICAgICAgdGhpcy5kb2NzLmZvckVhY2goKGRvYywgZG9jSW5kZXgpID0+IHtcbiAgICAgICAgdGhpcy5fYWRkU3RyaW5nKGRvYywgZG9jSW5kZXgpO1xuICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIC8vIExpc3QgaXMgQXJyYXk8T2JqZWN0PlxuICAgICAgdGhpcy5kb2NzLmZvckVhY2goKGRvYywgZG9jSW5kZXgpID0+IHtcbiAgICAgICAgdGhpcy5fYWRkT2JqZWN0KGRvYywgZG9jSW5kZXgpO1xuICAgICAgfSk7XG4gICAgfVxuXG4gICAgdGhpcy5ub3JtLmNsZWFyKCk7XG4gIH1cbiAgLy8gQWRkcyBhIGRvYyB0byB0aGUgZW5kIG9mIHRoZSBpbmRleFxuICBhZGQoZG9jKSB7XG4gICAgY29uc3QgaWR4ID0gdGhpcy5zaXplKCk7XG5cbiAgICBpZiAoaXNTdHJpbmcoZG9jKSkge1xuICAgICAgdGhpcy5fYWRkU3RyaW5nKGRvYywgaWR4KTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5fYWRkT2JqZWN0KGRvYywgaWR4KTtcbiAgICB9XG4gIH1cbiAgLy8gUmVtb3ZlcyB0aGUgZG9jIGF0IHRoZSBzcGVjaWZpZWQgaW5kZXggb2YgdGhlIGluZGV4XG4gIHJlbW92ZUF0KGlkeCkge1xuICAgIHRoaXMucmVjb3Jkcy5zcGxpY2UoaWR4LCAxKTtcblxuICAgIC8vIENoYW5nZSByZWYgaW5kZXggb2YgZXZlcnkgc3Vic3F1ZW50IGRvY1xuICAgIGZvciAobGV0IGkgPSBpZHgsIGxlbiA9IHRoaXMuc2l6ZSgpOyBpIDwgbGVuOyBpICs9IDEpIHtcbiAgICAgIHRoaXMucmVjb3Jkc1tpXS5pIC09IDE7XG4gICAgfVxuICB9XG4gIGdldFZhbHVlRm9ySXRlbUF0S2V5SWQoaXRlbSwga2V5SWQpIHtcbiAgICByZXR1cm4gaXRlbVt0aGlzLl9rZXlzTWFwW2tleUlkXV1cbiAgfVxuICBzaXplKCkge1xuICAgIHJldHVybiB0aGlzLnJlY29yZHMubGVuZ3RoXG4gIH1cbiAgX2FkZFN0cmluZyhkb2MsIGRvY0luZGV4KSB7XG4gICAgaWYgKCFpc0RlZmluZWQoZG9jKSB8fCBpc0JsYW5rKGRvYykpIHtcbiAgICAgIHJldHVyblxuICAgIH1cblxuICAgIGxldCByZWNvcmQgPSB7XG4gICAgICB2OiBkb2MsXG4gICAgICBpOiBkb2NJbmRleCxcbiAgICAgIG46IHRoaXMubm9ybS5nZXQoZG9jKVxuICAgIH07XG5cbiAgICB0aGlzLnJlY29yZHMucHVzaChyZWNvcmQpO1xuICB9XG4gIF9hZGRPYmplY3QoZG9jLCBkb2NJbmRleCkge1xuICAgIGxldCByZWNvcmQgPSB7IGk6IGRvY0luZGV4LCAkOiB7fSB9O1xuXG4gICAgLy8gSXRlcmF0ZSBvdmVyIGV2ZXJ5IGtleSAoaS5lLCBwYXRoKSwgYW5kIGZldGNoIHRoZSB2YWx1ZSBhdCB0aGF0IGtleVxuICAgIHRoaXMua2V5cy5mb3JFYWNoKChrZXksIGtleUluZGV4KSA9PiB7XG4gICAgICBsZXQgdmFsdWUgPSBrZXkuZ2V0Rm4gPyBrZXkuZ2V0Rm4oZG9jKSA6IHRoaXMuZ2V0Rm4oZG9jLCBrZXkucGF0aCk7XG5cbiAgICAgIGlmICghaXNEZWZpbmVkKHZhbHVlKSkge1xuICAgICAgICByZXR1cm5cbiAgICAgIH1cblxuICAgICAgaWYgKGlzQXJyYXkodmFsdWUpKSB7XG4gICAgICAgIGxldCBzdWJSZWNvcmRzID0gW107XG4gICAgICAgIGNvbnN0IHN0YWNrID0gW3sgbmVzdGVkQXJySW5kZXg6IC0xLCB2YWx1ZSB9XTtcblxuICAgICAgICB3aGlsZSAoc3RhY2subGVuZ3RoKSB7XG4gICAgICAgICAgY29uc3QgeyBuZXN0ZWRBcnJJbmRleCwgdmFsdWUgfSA9IHN0YWNrLnBvcCgpO1xuXG4gICAgICAgICAgaWYgKCFpc0RlZmluZWQodmFsdWUpKSB7XG4gICAgICAgICAgICBjb250aW51ZVxuICAgICAgICAgIH1cblxuICAgICAgICAgIGlmIChpc1N0cmluZyh2YWx1ZSkgJiYgIWlzQmxhbmsodmFsdWUpKSB7XG4gICAgICAgICAgICBsZXQgc3ViUmVjb3JkID0ge1xuICAgICAgICAgICAgICB2OiB2YWx1ZSxcbiAgICAgICAgICAgICAgaTogbmVzdGVkQXJySW5kZXgsXG4gICAgICAgICAgICAgIG46IHRoaXMubm9ybS5nZXQodmFsdWUpXG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICBzdWJSZWNvcmRzLnB1c2goc3ViUmVjb3JkKTtcbiAgICAgICAgICB9IGVsc2UgaWYgKGlzQXJyYXkodmFsdWUpKSB7XG4gICAgICAgICAgICB2YWx1ZS5mb3JFYWNoKChpdGVtLCBrKSA9PiB7XG4gICAgICAgICAgICAgIHN0YWNrLnB1c2goe1xuICAgICAgICAgICAgICAgIG5lc3RlZEFyckluZGV4OiBrLFxuICAgICAgICAgICAgICAgIHZhbHVlOiBpdGVtXG4gICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfSBlbHNlIDtcbiAgICAgICAgfVxuICAgICAgICByZWNvcmQuJFtrZXlJbmRleF0gPSBzdWJSZWNvcmRzO1xuICAgICAgfSBlbHNlIGlmIChpc1N0cmluZyh2YWx1ZSkgJiYgIWlzQmxhbmsodmFsdWUpKSB7XG4gICAgICAgIGxldCBzdWJSZWNvcmQgPSB7XG4gICAgICAgICAgdjogdmFsdWUsXG4gICAgICAgICAgbjogdGhpcy5ub3JtLmdldCh2YWx1ZSlcbiAgICAgICAgfTtcblxuICAgICAgICByZWNvcmQuJFtrZXlJbmRleF0gPSBzdWJSZWNvcmQ7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICB0aGlzLnJlY29yZHMucHVzaChyZWNvcmQpO1xuICB9XG4gIHRvSlNPTigpIHtcbiAgICByZXR1cm4ge1xuICAgICAga2V5czogdGhpcy5rZXlzLFxuICAgICAgcmVjb3JkczogdGhpcy5yZWNvcmRzXG4gICAgfVxuICB9XG59XG5cbmZ1bmN0aW9uIGNyZWF0ZUluZGV4KFxuICBrZXlzLFxuICBkb2NzLFxuICB7IGdldEZuID0gQ29uZmlnLmdldEZuLCBmaWVsZE5vcm1XZWlnaHQgPSBDb25maWcuZmllbGROb3JtV2VpZ2h0IH0gPSB7fVxuKSB7XG4gIGNvbnN0IG15SW5kZXggPSBuZXcgRnVzZUluZGV4KHsgZ2V0Rm4sIGZpZWxkTm9ybVdlaWdodCB9KTtcbiAgbXlJbmRleC5zZXRLZXlzKGtleXMubWFwKGNyZWF0ZUtleSkpO1xuICBteUluZGV4LnNldFNvdXJjZXMoZG9jcyk7XG4gIG15SW5kZXguY3JlYXRlKCk7XG4gIHJldHVybiBteUluZGV4XG59XG5cbmZ1bmN0aW9uIHBhcnNlSW5kZXgoXG4gIGRhdGEsXG4gIHsgZ2V0Rm4gPSBDb25maWcuZ2V0Rm4sIGZpZWxkTm9ybVdlaWdodCA9IENvbmZpZy5maWVsZE5vcm1XZWlnaHQgfSA9IHt9XG4pIHtcbiAgY29uc3QgeyBrZXlzLCByZWNvcmRzIH0gPSBkYXRhO1xuICBjb25zdCBteUluZGV4ID0gbmV3IEZ1c2VJbmRleCh7IGdldEZuLCBmaWVsZE5vcm1XZWlnaHQgfSk7XG4gIG15SW5kZXguc2V0S2V5cyhrZXlzKTtcbiAgbXlJbmRleC5zZXRJbmRleFJlY29yZHMocmVjb3Jkcyk7XG4gIHJldHVybiBteUluZGV4XG59XG5cbmZ1bmN0aW9uIGNvbXB1dGVTY29yZSQxKFxuICBwYXR0ZXJuLFxuICB7XG4gICAgZXJyb3JzID0gMCxcbiAgICBjdXJyZW50TG9jYXRpb24gPSAwLFxuICAgIGV4cGVjdGVkTG9jYXRpb24gPSAwLFxuICAgIGRpc3RhbmNlID0gQ29uZmlnLmRpc3RhbmNlLFxuICAgIGlnbm9yZUxvY2F0aW9uID0gQ29uZmlnLmlnbm9yZUxvY2F0aW9uXG4gIH0gPSB7fVxuKSB7XG4gIGNvbnN0IGFjY3VyYWN5ID0gZXJyb3JzIC8gcGF0dGVybi5sZW5ndGg7XG5cbiAgaWYgKGlnbm9yZUxvY2F0aW9uKSB7XG4gICAgcmV0dXJuIGFjY3VyYWN5XG4gIH1cblxuICBjb25zdCBwcm94aW1pdHkgPSBNYXRoLmFicyhleHBlY3RlZExvY2F0aW9uIC0gY3VycmVudExvY2F0aW9uKTtcblxuICBpZiAoIWRpc3RhbmNlKSB7XG4gICAgLy8gRG9kZ2UgZGl2aWRlIGJ5IHplcm8gZXJyb3IuXG4gICAgcmV0dXJuIHByb3hpbWl0eSA/IDEuMCA6IGFjY3VyYWN5XG4gIH1cblxuICByZXR1cm4gYWNjdXJhY3kgKyBwcm94aW1pdHkgLyBkaXN0YW5jZVxufVxuXG5mdW5jdGlvbiBjb252ZXJ0TWFza1RvSW5kaWNlcyhcbiAgbWF0Y2htYXNrID0gW10sXG4gIG1pbk1hdGNoQ2hhckxlbmd0aCA9IENvbmZpZy5taW5NYXRjaENoYXJMZW5ndGhcbikge1xuICBsZXQgaW5kaWNlcyA9IFtdO1xuICBsZXQgc3RhcnQgPSAtMTtcbiAgbGV0IGVuZCA9IC0xO1xuICBsZXQgaSA9IDA7XG5cbiAgZm9yIChsZXQgbGVuID0gbWF0Y2htYXNrLmxlbmd0aDsgaSA8IGxlbjsgaSArPSAxKSB7XG4gICAgbGV0IG1hdGNoID0gbWF0Y2htYXNrW2ldO1xuICAgIGlmIChtYXRjaCAmJiBzdGFydCA9PT0gLTEpIHtcbiAgICAgIHN0YXJ0ID0gaTtcbiAgICB9IGVsc2UgaWYgKCFtYXRjaCAmJiBzdGFydCAhPT0gLTEpIHtcbiAgICAgIGVuZCA9IGkgLSAxO1xuICAgICAgaWYgKGVuZCAtIHN0YXJ0ICsgMSA+PSBtaW5NYXRjaENoYXJMZW5ndGgpIHtcbiAgICAgICAgaW5kaWNlcy5wdXNoKFtzdGFydCwgZW5kXSk7XG4gICAgICB9XG4gICAgICBzdGFydCA9IC0xO1xuICAgIH1cbiAgfVxuXG4gIC8vIChpLTEgLSBzdGFydCkgKyAxID0+IGkgLSBzdGFydFxuICBpZiAobWF0Y2htYXNrW2kgLSAxXSAmJiBpIC0gc3RhcnQgPj0gbWluTWF0Y2hDaGFyTGVuZ3RoKSB7XG4gICAgaW5kaWNlcy5wdXNoKFtzdGFydCwgaSAtIDFdKTtcbiAgfVxuXG4gIHJldHVybiBpbmRpY2VzXG59XG5cbi8vIE1hY2hpbmUgd29yZCBzaXplXG5jb25zdCBNQVhfQklUUyA9IDMyO1xuXG5mdW5jdGlvbiBzZWFyY2goXG4gIHRleHQsXG4gIHBhdHRlcm4sXG4gIHBhdHRlcm5BbHBoYWJldCxcbiAge1xuICAgIGxvY2F0aW9uID0gQ29uZmlnLmxvY2F0aW9uLFxuICAgIGRpc3RhbmNlID0gQ29uZmlnLmRpc3RhbmNlLFxuICAgIHRocmVzaG9sZCA9IENvbmZpZy50aHJlc2hvbGQsXG4gICAgZmluZEFsbE1hdGNoZXMgPSBDb25maWcuZmluZEFsbE1hdGNoZXMsXG4gICAgbWluTWF0Y2hDaGFyTGVuZ3RoID0gQ29uZmlnLm1pbk1hdGNoQ2hhckxlbmd0aCxcbiAgICBpbmNsdWRlTWF0Y2hlcyA9IENvbmZpZy5pbmNsdWRlTWF0Y2hlcyxcbiAgICBpZ25vcmVMb2NhdGlvbiA9IENvbmZpZy5pZ25vcmVMb2NhdGlvblxuICB9ID0ge31cbikge1xuICBpZiAocGF0dGVybi5sZW5ndGggPiBNQVhfQklUUykge1xuICAgIHRocm93IG5ldyBFcnJvcihQQVRURVJOX0xFTkdUSF9UT09fTEFSR0UoTUFYX0JJVFMpKVxuICB9XG5cbiAgY29uc3QgcGF0dGVybkxlbiA9IHBhdHRlcm4ubGVuZ3RoO1xuICAvLyBTZXQgc3RhcnRpbmcgbG9jYXRpb24gYXQgYmVnaW5uaW5nIHRleHQgYW5kIGluaXRpYWxpemUgdGhlIGFscGhhYmV0LlxuICBjb25zdCB0ZXh0TGVuID0gdGV4dC5sZW5ndGg7XG4gIC8vIEhhbmRsZSB0aGUgY2FzZSB3aGVuIGxvY2F0aW9uID4gdGV4dC5sZW5ndGhcbiAgY29uc3QgZXhwZWN0ZWRMb2NhdGlvbiA9IE1hdGgubWF4KDAsIE1hdGgubWluKGxvY2F0aW9uLCB0ZXh0TGVuKSk7XG4gIC8vIEhpZ2hlc3Qgc2NvcmUgYmV5b25kIHdoaWNoIHdlIGdpdmUgdXAuXG4gIGxldCBjdXJyZW50VGhyZXNob2xkID0gdGhyZXNob2xkO1xuICAvLyBJcyB0aGVyZSBhIG5lYXJieSBleGFjdCBtYXRjaD8gKHNwZWVkdXApXG4gIGxldCBiZXN0TG9jYXRpb24gPSBleHBlY3RlZExvY2F0aW9uO1xuXG4gIC8vIFBlcmZvcm1hbmNlOiBvbmx5IGNvbXB1dGVyIG1hdGNoZXMgd2hlbiB0aGUgbWluTWF0Y2hDaGFyTGVuZ3RoID4gMVxuICAvLyBPUiBpZiBgaW5jbHVkZU1hdGNoZXNgIGlzIHRydWUuXG4gIGNvbnN0IGNvbXB1dGVNYXRjaGVzID0gbWluTWF0Y2hDaGFyTGVuZ3RoID4gMSB8fCBpbmNsdWRlTWF0Y2hlcztcbiAgLy8gQSBtYXNrIG9mIHRoZSBtYXRjaGVzLCB1c2VkIGZvciBidWlsZGluZyB0aGUgaW5kaWNlc1xuICBjb25zdCBtYXRjaE1hc2sgPSBjb21wdXRlTWF0Y2hlcyA/IEFycmF5KHRleHRMZW4pIDogW107XG5cbiAgbGV0IGluZGV4O1xuXG4gIC8vIEdldCBhbGwgZXhhY3QgbWF0Y2hlcywgaGVyZSBmb3Igc3BlZWQgdXBcbiAgd2hpbGUgKChpbmRleCA9IHRleHQuaW5kZXhPZihwYXR0ZXJuLCBiZXN0TG9jYXRpb24pKSA+IC0xKSB7XG4gICAgbGV0IHNjb3JlID0gY29tcHV0ZVNjb3JlJDEocGF0dGVybiwge1xuICAgICAgY3VycmVudExvY2F0aW9uOiBpbmRleCxcbiAgICAgIGV4cGVjdGVkTG9jYXRpb24sXG4gICAgICBkaXN0YW5jZSxcbiAgICAgIGlnbm9yZUxvY2F0aW9uXG4gICAgfSk7XG5cbiAgICBjdXJyZW50VGhyZXNob2xkID0gTWF0aC5taW4oc2NvcmUsIGN1cnJlbnRUaHJlc2hvbGQpO1xuICAgIGJlc3RMb2NhdGlvbiA9IGluZGV4ICsgcGF0dGVybkxlbjtcblxuICAgIGlmIChjb21wdXRlTWF0Y2hlcykge1xuICAgICAgbGV0IGkgPSAwO1xuICAgICAgd2hpbGUgKGkgPCBwYXR0ZXJuTGVuKSB7XG4gICAgICAgIG1hdGNoTWFza1tpbmRleCArIGldID0gMTtcbiAgICAgICAgaSArPSAxO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIC8vIFJlc2V0IHRoZSBiZXN0IGxvY2F0aW9uXG4gIGJlc3RMb2NhdGlvbiA9IC0xO1xuXG4gIGxldCBsYXN0Qml0QXJyID0gW107XG4gIGxldCBmaW5hbFNjb3JlID0gMTtcbiAgbGV0IGJpbk1heCA9IHBhdHRlcm5MZW4gKyB0ZXh0TGVuO1xuXG4gIGNvbnN0IG1hc2sgPSAxIDw8IChwYXR0ZXJuTGVuIC0gMSk7XG5cbiAgZm9yIChsZXQgaSA9IDA7IGkgPCBwYXR0ZXJuTGVuOyBpICs9IDEpIHtcbiAgICAvLyBTY2FuIGZvciB0aGUgYmVzdCBtYXRjaDsgZWFjaCBpdGVyYXRpb24gYWxsb3dzIGZvciBvbmUgbW9yZSBlcnJvci5cbiAgICAvLyBSdW4gYSBiaW5hcnkgc2VhcmNoIHRvIGRldGVybWluZSBob3cgZmFyIGZyb20gdGhlIG1hdGNoIGxvY2F0aW9uIHdlIGNhbiBzdHJheVxuICAgIC8vIGF0IHRoaXMgZXJyb3IgbGV2ZWwuXG4gICAgbGV0IGJpbk1pbiA9IDA7XG4gICAgbGV0IGJpbk1pZCA9IGJpbk1heDtcblxuICAgIHdoaWxlIChiaW5NaW4gPCBiaW5NaWQpIHtcbiAgICAgIGNvbnN0IHNjb3JlID0gY29tcHV0ZVNjb3JlJDEocGF0dGVybiwge1xuICAgICAgICBlcnJvcnM6IGksXG4gICAgICAgIGN1cnJlbnRMb2NhdGlvbjogZXhwZWN0ZWRMb2NhdGlvbiArIGJpbk1pZCxcbiAgICAgICAgZXhwZWN0ZWRMb2NhdGlvbixcbiAgICAgICAgZGlzdGFuY2UsXG4gICAgICAgIGlnbm9yZUxvY2F0aW9uXG4gICAgICB9KTtcblxuICAgICAgaWYgKHNjb3JlIDw9IGN1cnJlbnRUaHJlc2hvbGQpIHtcbiAgICAgICAgYmluTWluID0gYmluTWlkO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYmluTWF4ID0gYmluTWlkO1xuICAgICAgfVxuXG4gICAgICBiaW5NaWQgPSBNYXRoLmZsb29yKChiaW5NYXggLSBiaW5NaW4pIC8gMiArIGJpbk1pbik7XG4gICAgfVxuXG4gICAgLy8gVXNlIHRoZSByZXN1bHQgZnJvbSB0aGlzIGl0ZXJhdGlvbiBhcyB0aGUgbWF4aW11bSBmb3IgdGhlIG5leHQuXG4gICAgYmluTWF4ID0gYmluTWlkO1xuXG4gICAgbGV0IHN0YXJ0ID0gTWF0aC5tYXgoMSwgZXhwZWN0ZWRMb2NhdGlvbiAtIGJpbk1pZCArIDEpO1xuICAgIGxldCBmaW5pc2ggPSBmaW5kQWxsTWF0Y2hlc1xuICAgICAgPyB0ZXh0TGVuXG4gICAgICA6IE1hdGgubWluKGV4cGVjdGVkTG9jYXRpb24gKyBiaW5NaWQsIHRleHRMZW4pICsgcGF0dGVybkxlbjtcblxuICAgIC8vIEluaXRpYWxpemUgdGhlIGJpdCBhcnJheVxuICAgIGxldCBiaXRBcnIgPSBBcnJheShmaW5pc2ggKyAyKTtcblxuICAgIGJpdEFycltmaW5pc2ggKyAxXSA9ICgxIDw8IGkpIC0gMTtcblxuICAgIGZvciAobGV0IGogPSBmaW5pc2g7IGogPj0gc3RhcnQ7IGogLT0gMSkge1xuICAgICAgbGV0IGN1cnJlbnRMb2NhdGlvbiA9IGogLSAxO1xuICAgICAgbGV0IGNoYXJNYXRjaCA9IHBhdHRlcm5BbHBoYWJldFt0ZXh0LmNoYXJBdChjdXJyZW50TG9jYXRpb24pXTtcblxuICAgICAgaWYgKGNvbXB1dGVNYXRjaGVzKSB7XG4gICAgICAgIC8vIFNwZWVkIHVwOiBxdWljayBib29sIHRvIGludCBjb252ZXJzaW9uIChpLmUsIGBjaGFyTWF0Y2ggPyAxIDogMGApXG4gICAgICAgIG1hdGNoTWFza1tjdXJyZW50TG9jYXRpb25dID0gKyEhY2hhck1hdGNoO1xuICAgICAgfVxuXG4gICAgICAvLyBGaXJzdCBwYXNzOiBleGFjdCBtYXRjaFxuICAgICAgYml0QXJyW2pdID0gKChiaXRBcnJbaiArIDFdIDw8IDEpIHwgMSkgJiBjaGFyTWF0Y2g7XG5cbiAgICAgIC8vIFN1YnNlcXVlbnQgcGFzc2VzOiBmdXp6eSBtYXRjaFxuICAgICAgaWYgKGkpIHtcbiAgICAgICAgYml0QXJyW2pdIHw9XG4gICAgICAgICAgKChsYXN0Qml0QXJyW2ogKyAxXSB8IGxhc3RCaXRBcnJbal0pIDw8IDEpIHwgMSB8IGxhc3RCaXRBcnJbaiArIDFdO1xuICAgICAgfVxuXG4gICAgICBpZiAoYml0QXJyW2pdICYgbWFzaykge1xuICAgICAgICBmaW5hbFNjb3JlID0gY29tcHV0ZVNjb3JlJDEocGF0dGVybiwge1xuICAgICAgICAgIGVycm9yczogaSxcbiAgICAgICAgICBjdXJyZW50TG9jYXRpb24sXG4gICAgICAgICAgZXhwZWN0ZWRMb2NhdGlvbixcbiAgICAgICAgICBkaXN0YW5jZSxcbiAgICAgICAgICBpZ25vcmVMb2NhdGlvblxuICAgICAgICB9KTtcblxuICAgICAgICAvLyBUaGlzIG1hdGNoIHdpbGwgYWxtb3N0IGNlcnRhaW5seSBiZSBiZXR0ZXIgdGhhbiBhbnkgZXhpc3RpbmcgbWF0Y2guXG4gICAgICAgIC8vIEJ1dCBjaGVjayBhbnl3YXkuXG4gICAgICAgIGlmIChmaW5hbFNjb3JlIDw9IGN1cnJlbnRUaHJlc2hvbGQpIHtcbiAgICAgICAgICAvLyBJbmRlZWQgaXQgaXNcbiAgICAgICAgICBjdXJyZW50VGhyZXNob2xkID0gZmluYWxTY29yZTtcbiAgICAgICAgICBiZXN0TG9jYXRpb24gPSBjdXJyZW50TG9jYXRpb247XG5cbiAgICAgICAgICAvLyBBbHJlYWR5IHBhc3NlZCBgbG9jYCwgZG93bmhpbGwgZnJvbSBoZXJlIG9uIGluLlxuICAgICAgICAgIGlmIChiZXN0TG9jYXRpb24gPD0gZXhwZWN0ZWRMb2NhdGlvbikge1xuICAgICAgICAgICAgYnJlYWtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICAvLyBXaGVuIHBhc3NpbmcgYGJlc3RMb2NhdGlvbmAsIGRvbid0IGV4Y2VlZCBvdXIgY3VycmVudCBkaXN0YW5jZSBmcm9tIGBleHBlY3RlZExvY2F0aW9uYC5cbiAgICAgICAgICBzdGFydCA9IE1hdGgubWF4KDEsIDIgKiBleHBlY3RlZExvY2F0aW9uIC0gYmVzdExvY2F0aW9uKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cblxuICAgIC8vIE5vIGhvcGUgZm9yIGEgKGJldHRlcikgbWF0Y2ggYXQgZ3JlYXRlciBlcnJvciBsZXZlbHMuXG4gICAgY29uc3Qgc2NvcmUgPSBjb21wdXRlU2NvcmUkMShwYXR0ZXJuLCB7XG4gICAgICBlcnJvcnM6IGkgKyAxLFxuICAgICAgY3VycmVudExvY2F0aW9uOiBleHBlY3RlZExvY2F0aW9uLFxuICAgICAgZXhwZWN0ZWRMb2NhdGlvbixcbiAgICAgIGRpc3RhbmNlLFxuICAgICAgaWdub3JlTG9jYXRpb25cbiAgICB9KTtcblxuICAgIGlmIChzY29yZSA+IGN1cnJlbnRUaHJlc2hvbGQpIHtcbiAgICAgIGJyZWFrXG4gICAgfVxuXG4gICAgbGFzdEJpdEFyciA9IGJpdEFycjtcbiAgfVxuXG4gIGNvbnN0IHJlc3VsdCA9IHtcbiAgICBpc01hdGNoOiBiZXN0TG9jYXRpb24gPj0gMCxcbiAgICAvLyBDb3VudCBleGFjdCBtYXRjaGVzICh0aG9zZSB3aXRoIGEgc2NvcmUgb2YgMCkgdG8gYmUgXCJhbG1vc3RcIiBleGFjdFxuICAgIHNjb3JlOiBNYXRoLm1heCgwLjAwMSwgZmluYWxTY29yZSlcbiAgfTtcblxuICBpZiAoY29tcHV0ZU1hdGNoZXMpIHtcbiAgICBjb25zdCBpbmRpY2VzID0gY29udmVydE1hc2tUb0luZGljZXMobWF0Y2hNYXNrLCBtaW5NYXRjaENoYXJMZW5ndGgpO1xuICAgIGlmICghaW5kaWNlcy5sZW5ndGgpIHtcbiAgICAgIHJlc3VsdC5pc01hdGNoID0gZmFsc2U7XG4gICAgfSBlbHNlIGlmIChpbmNsdWRlTWF0Y2hlcykge1xuICAgICAgcmVzdWx0LmluZGljZXMgPSBpbmRpY2VzO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiByZXN1bHRcbn1cblxuZnVuY3Rpb24gY3JlYXRlUGF0dGVybkFscGhhYmV0KHBhdHRlcm4pIHtcbiAgbGV0IG1hc2sgPSB7fTtcblxuICBmb3IgKGxldCBpID0gMCwgbGVuID0gcGF0dGVybi5sZW5ndGg7IGkgPCBsZW47IGkgKz0gMSkge1xuICAgIGNvbnN0IGNoYXIgPSBwYXR0ZXJuLmNoYXJBdChpKTtcbiAgICBtYXNrW2NoYXJdID0gKG1hc2tbY2hhcl0gfHwgMCkgfCAoMSA8PCAobGVuIC0gaSAtIDEpKTtcbiAgfVxuXG4gIHJldHVybiBtYXNrXG59XG5cbmNsYXNzIEJpdGFwU2VhcmNoIHtcbiAgY29uc3RydWN0b3IoXG4gICAgcGF0dGVybixcbiAgICB7XG4gICAgICBsb2NhdGlvbiA9IENvbmZpZy5sb2NhdGlvbixcbiAgICAgIHRocmVzaG9sZCA9IENvbmZpZy50aHJlc2hvbGQsXG4gICAgICBkaXN0YW5jZSA9IENvbmZpZy5kaXN0YW5jZSxcbiAgICAgIGluY2x1ZGVNYXRjaGVzID0gQ29uZmlnLmluY2x1ZGVNYXRjaGVzLFxuICAgICAgZmluZEFsbE1hdGNoZXMgPSBDb25maWcuZmluZEFsbE1hdGNoZXMsXG4gICAgICBtaW5NYXRjaENoYXJMZW5ndGggPSBDb25maWcubWluTWF0Y2hDaGFyTGVuZ3RoLFxuICAgICAgaXNDYXNlU2Vuc2l0aXZlID0gQ29uZmlnLmlzQ2FzZVNlbnNpdGl2ZSxcbiAgICAgIGlnbm9yZUxvY2F0aW9uID0gQ29uZmlnLmlnbm9yZUxvY2F0aW9uXG4gICAgfSA9IHt9XG4gICkge1xuICAgIHRoaXMub3B0aW9ucyA9IHtcbiAgICAgIGxvY2F0aW9uLFxuICAgICAgdGhyZXNob2xkLFxuICAgICAgZGlzdGFuY2UsXG4gICAgICBpbmNsdWRlTWF0Y2hlcyxcbiAgICAgIGZpbmRBbGxNYXRjaGVzLFxuICAgICAgbWluTWF0Y2hDaGFyTGVuZ3RoLFxuICAgICAgaXNDYXNlU2Vuc2l0aXZlLFxuICAgICAgaWdub3JlTG9jYXRpb25cbiAgICB9O1xuXG4gICAgdGhpcy5wYXR0ZXJuID0gaXNDYXNlU2Vuc2l0aXZlID8gcGF0dGVybiA6IHBhdHRlcm4udG9Mb3dlckNhc2UoKTtcblxuICAgIHRoaXMuY2h1bmtzID0gW107XG5cbiAgICBpZiAoIXRoaXMucGF0dGVybi5sZW5ndGgpIHtcbiAgICAgIHJldHVyblxuICAgIH1cblxuICAgIGNvbnN0IGFkZENodW5rID0gKHBhdHRlcm4sIHN0YXJ0SW5kZXgpID0+IHtcbiAgICAgIHRoaXMuY2h1bmtzLnB1c2goe1xuICAgICAgICBwYXR0ZXJuLFxuICAgICAgICBhbHBoYWJldDogY3JlYXRlUGF0dGVybkFscGhhYmV0KHBhdHRlcm4pLFxuICAgICAgICBzdGFydEluZGV4XG4gICAgICB9KTtcbiAgICB9O1xuXG4gICAgY29uc3QgbGVuID0gdGhpcy5wYXR0ZXJuLmxlbmd0aDtcblxuICAgIGlmIChsZW4gPiBNQVhfQklUUykge1xuICAgICAgbGV0IGkgPSAwO1xuICAgICAgY29uc3QgcmVtYWluZGVyID0gbGVuICUgTUFYX0JJVFM7XG4gICAgICBjb25zdCBlbmQgPSBsZW4gLSByZW1haW5kZXI7XG5cbiAgICAgIHdoaWxlIChpIDwgZW5kKSB7XG4gICAgICAgIGFkZENodW5rKHRoaXMucGF0dGVybi5zdWJzdHIoaSwgTUFYX0JJVFMpLCBpKTtcbiAgICAgICAgaSArPSBNQVhfQklUUztcbiAgICAgIH1cblxuICAgICAgaWYgKHJlbWFpbmRlcikge1xuICAgICAgICBjb25zdCBzdGFydEluZGV4ID0gbGVuIC0gTUFYX0JJVFM7XG4gICAgICAgIGFkZENodW5rKHRoaXMucGF0dGVybi5zdWJzdHIoc3RhcnRJbmRleCksIHN0YXJ0SW5kZXgpO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICBhZGRDaHVuayh0aGlzLnBhdHRlcm4sIDApO1xuICAgIH1cbiAgfVxuXG4gIHNlYXJjaEluKHRleHQpIHtcbiAgICBjb25zdCB7IGlzQ2FzZVNlbnNpdGl2ZSwgaW5jbHVkZU1hdGNoZXMgfSA9IHRoaXMub3B0aW9ucztcblxuICAgIGlmICghaXNDYXNlU2Vuc2l0aXZlKSB7XG4gICAgICB0ZXh0ID0gdGV4dC50b0xvd2VyQ2FzZSgpO1xuICAgIH1cblxuICAgIC8vIEV4YWN0IG1hdGNoXG4gICAgaWYgKHRoaXMucGF0dGVybiA9PT0gdGV4dCkge1xuICAgICAgbGV0IHJlc3VsdCA9IHtcbiAgICAgICAgaXNNYXRjaDogdHJ1ZSxcbiAgICAgICAgc2NvcmU6IDBcbiAgICAgIH07XG5cbiAgICAgIGlmIChpbmNsdWRlTWF0Y2hlcykge1xuICAgICAgICByZXN1bHQuaW5kaWNlcyA9IFtbMCwgdGV4dC5sZW5ndGggLSAxXV07XG4gICAgICB9XG5cbiAgICAgIHJldHVybiByZXN1bHRcbiAgICB9XG5cbiAgICAvLyBPdGhlcndpc2UsIHVzZSBCaXRhcCBhbGdvcml0aG1cbiAgICBjb25zdCB7XG4gICAgICBsb2NhdGlvbixcbiAgICAgIGRpc3RhbmNlLFxuICAgICAgdGhyZXNob2xkLFxuICAgICAgZmluZEFsbE1hdGNoZXMsXG4gICAgICBtaW5NYXRjaENoYXJMZW5ndGgsXG4gICAgICBpZ25vcmVMb2NhdGlvblxuICAgIH0gPSB0aGlzLm9wdGlvbnM7XG5cbiAgICBsZXQgYWxsSW5kaWNlcyA9IFtdO1xuICAgIGxldCB0b3RhbFNjb3JlID0gMDtcbiAgICBsZXQgaGFzTWF0Y2hlcyA9IGZhbHNlO1xuXG4gICAgdGhpcy5jaHVua3MuZm9yRWFjaCgoeyBwYXR0ZXJuLCBhbHBoYWJldCwgc3RhcnRJbmRleCB9KSA9PiB7XG4gICAgICBjb25zdCB7IGlzTWF0Y2gsIHNjb3JlLCBpbmRpY2VzIH0gPSBzZWFyY2godGV4dCwgcGF0dGVybiwgYWxwaGFiZXQsIHtcbiAgICAgICAgbG9jYXRpb246IGxvY2F0aW9uICsgc3RhcnRJbmRleCxcbiAgICAgICAgZGlzdGFuY2UsXG4gICAgICAgIHRocmVzaG9sZCxcbiAgICAgICAgZmluZEFsbE1hdGNoZXMsXG4gICAgICAgIG1pbk1hdGNoQ2hhckxlbmd0aCxcbiAgICAgICAgaW5jbHVkZU1hdGNoZXMsXG4gICAgICAgIGlnbm9yZUxvY2F0aW9uXG4gICAgICB9KTtcblxuICAgICAgaWYgKGlzTWF0Y2gpIHtcbiAgICAgICAgaGFzTWF0Y2hlcyA9IHRydWU7XG4gICAgICB9XG5cbiAgICAgIHRvdGFsU2NvcmUgKz0gc2NvcmU7XG5cbiAgICAgIGlmIChpc01hdGNoICYmIGluZGljZXMpIHtcbiAgICAgICAgYWxsSW5kaWNlcyA9IFsuLi5hbGxJbmRpY2VzLCAuLi5pbmRpY2VzXTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIGxldCByZXN1bHQgPSB7XG4gICAgICBpc01hdGNoOiBoYXNNYXRjaGVzLFxuICAgICAgc2NvcmU6IGhhc01hdGNoZXMgPyB0b3RhbFNjb3JlIC8gdGhpcy5jaHVua3MubGVuZ3RoIDogMVxuICAgIH07XG5cbiAgICBpZiAoaGFzTWF0Y2hlcyAmJiBpbmNsdWRlTWF0Y2hlcykge1xuICAgICAgcmVzdWx0LmluZGljZXMgPSBhbGxJbmRpY2VzO1xuICAgIH1cblxuICAgIHJldHVybiByZXN1bHRcbiAgfVxufVxuXG5jbGFzcyBCYXNlTWF0Y2gge1xuICBjb25zdHJ1Y3RvcihwYXR0ZXJuKSB7XG4gICAgdGhpcy5wYXR0ZXJuID0gcGF0dGVybjtcbiAgfVxuICBzdGF0aWMgaXNNdWx0aU1hdGNoKHBhdHRlcm4pIHtcbiAgICByZXR1cm4gZ2V0TWF0Y2gocGF0dGVybiwgdGhpcy5tdWx0aVJlZ2V4KVxuICB9XG4gIHN0YXRpYyBpc1NpbmdsZU1hdGNoKHBhdHRlcm4pIHtcbiAgICByZXR1cm4gZ2V0TWF0Y2gocGF0dGVybiwgdGhpcy5zaW5nbGVSZWdleClcbiAgfVxuICBzZWFyY2goLyp0ZXh0Ki8pIHt9XG59XG5cbmZ1bmN0aW9uIGdldE1hdGNoKHBhdHRlcm4sIGV4cCkge1xuICBjb25zdCBtYXRjaGVzID0gcGF0dGVybi5tYXRjaChleHApO1xuICByZXR1cm4gbWF0Y2hlcyA/IG1hdGNoZXNbMV0gOiBudWxsXG59XG5cbi8vIFRva2VuOiAnZmlsZVxuXG5jbGFzcyBFeGFjdE1hdGNoIGV4dGVuZHMgQmFzZU1hdGNoIHtcbiAgY29uc3RydWN0b3IocGF0dGVybikge1xuICAgIHN1cGVyKHBhdHRlcm4pO1xuICB9XG4gIHN0YXRpYyBnZXQgdHlwZSgpIHtcbiAgICByZXR1cm4gJ2V4YWN0J1xuICB9XG4gIHN0YXRpYyBnZXQgbXVsdGlSZWdleCgpIHtcbiAgICByZXR1cm4gL149XCIoLiopXCIkL1xuICB9XG4gIHN0YXRpYyBnZXQgc2luZ2xlUmVnZXgoKSB7XG4gICAgcmV0dXJuIC9ePSguKikkL1xuICB9XG4gIHNlYXJjaCh0ZXh0KSB7XG4gICAgY29uc3QgaXNNYXRjaCA9IHRleHQgPT09IHRoaXMucGF0dGVybjtcblxuICAgIHJldHVybiB7XG4gICAgICBpc01hdGNoLFxuICAgICAgc2NvcmU6IGlzTWF0Y2ggPyAwIDogMSxcbiAgICAgIGluZGljZXM6IFswLCB0aGlzLnBhdHRlcm4ubGVuZ3RoIC0gMV1cbiAgICB9XG4gIH1cbn1cblxuLy8gVG9rZW46ICFmaXJlXG5cbmNsYXNzIEludmVyc2VFeGFjdE1hdGNoIGV4dGVuZHMgQmFzZU1hdGNoIHtcbiAgY29uc3RydWN0b3IocGF0dGVybikge1xuICAgIHN1cGVyKHBhdHRlcm4pO1xuICB9XG4gIHN0YXRpYyBnZXQgdHlwZSgpIHtcbiAgICByZXR1cm4gJ2ludmVyc2UtZXhhY3QnXG4gIH1cbiAgc3RhdGljIGdldCBtdWx0aVJlZ2V4KCkge1xuICAgIHJldHVybiAvXiFcIiguKilcIiQvXG4gIH1cbiAgc3RhdGljIGdldCBzaW5nbGVSZWdleCgpIHtcbiAgICByZXR1cm4gL14hKC4qKSQvXG4gIH1cbiAgc2VhcmNoKHRleHQpIHtcbiAgICBjb25zdCBpbmRleCA9IHRleHQuaW5kZXhPZih0aGlzLnBhdHRlcm4pO1xuICAgIGNvbnN0IGlzTWF0Y2ggPSBpbmRleCA9PT0gLTE7XG5cbiAgICByZXR1cm4ge1xuICAgICAgaXNNYXRjaCxcbiAgICAgIHNjb3JlOiBpc01hdGNoID8gMCA6IDEsXG4gICAgICBpbmRpY2VzOiBbMCwgdGV4dC5sZW5ndGggLSAxXVxuICAgIH1cbiAgfVxufVxuXG4vLyBUb2tlbjogXmZpbGVcblxuY2xhc3MgUHJlZml4RXhhY3RNYXRjaCBleHRlbmRzIEJhc2VNYXRjaCB7XG4gIGNvbnN0cnVjdG9yKHBhdHRlcm4pIHtcbiAgICBzdXBlcihwYXR0ZXJuKTtcbiAgfVxuICBzdGF0aWMgZ2V0IHR5cGUoKSB7XG4gICAgcmV0dXJuICdwcmVmaXgtZXhhY3QnXG4gIH1cbiAgc3RhdGljIGdldCBtdWx0aVJlZ2V4KCkge1xuICAgIHJldHVybiAvXlxcXlwiKC4qKVwiJC9cbiAgfVxuICBzdGF0aWMgZ2V0IHNpbmdsZVJlZ2V4KCkge1xuICAgIHJldHVybiAvXlxcXiguKikkL1xuICB9XG4gIHNlYXJjaCh0ZXh0KSB7XG4gICAgY29uc3QgaXNNYXRjaCA9IHRleHQuc3RhcnRzV2l0aCh0aGlzLnBhdHRlcm4pO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIGlzTWF0Y2gsXG4gICAgICBzY29yZTogaXNNYXRjaCA/IDAgOiAxLFxuICAgICAgaW5kaWNlczogWzAsIHRoaXMucGF0dGVybi5sZW5ndGggLSAxXVxuICAgIH1cbiAgfVxufVxuXG4vLyBUb2tlbjogIV5maXJlXG5cbmNsYXNzIEludmVyc2VQcmVmaXhFeGFjdE1hdGNoIGV4dGVuZHMgQmFzZU1hdGNoIHtcbiAgY29uc3RydWN0b3IocGF0dGVybikge1xuICAgIHN1cGVyKHBhdHRlcm4pO1xuICB9XG4gIHN0YXRpYyBnZXQgdHlwZSgpIHtcbiAgICByZXR1cm4gJ2ludmVyc2UtcHJlZml4LWV4YWN0J1xuICB9XG4gIHN0YXRpYyBnZXQgbXVsdGlSZWdleCgpIHtcbiAgICByZXR1cm4gL14hXFxeXCIoLiopXCIkL1xuICB9XG4gIHN0YXRpYyBnZXQgc2luZ2xlUmVnZXgoKSB7XG4gICAgcmV0dXJuIC9eIVxcXiguKikkL1xuICB9XG4gIHNlYXJjaCh0ZXh0KSB7XG4gICAgY29uc3QgaXNNYXRjaCA9ICF0ZXh0LnN0YXJ0c1dpdGgodGhpcy5wYXR0ZXJuKTtcblxuICAgIHJldHVybiB7XG4gICAgICBpc01hdGNoLFxuICAgICAgc2NvcmU6IGlzTWF0Y2ggPyAwIDogMSxcbiAgICAgIGluZGljZXM6IFswLCB0ZXh0Lmxlbmd0aCAtIDFdXG4gICAgfVxuICB9XG59XG5cbi8vIFRva2VuOiAuZmlsZSRcblxuY2xhc3MgU3VmZml4RXhhY3RNYXRjaCBleHRlbmRzIEJhc2VNYXRjaCB7XG4gIGNvbnN0cnVjdG9yKHBhdHRlcm4pIHtcbiAgICBzdXBlcihwYXR0ZXJuKTtcbiAgfVxuICBzdGF0aWMgZ2V0IHR5cGUoKSB7XG4gICAgcmV0dXJuICdzdWZmaXgtZXhhY3QnXG4gIH1cbiAgc3RhdGljIGdldCBtdWx0aVJlZ2V4KCkge1xuICAgIHJldHVybiAvXlwiKC4qKVwiXFwkJC9cbiAgfVxuICBzdGF0aWMgZ2V0IHNpbmdsZVJlZ2V4KCkge1xuICAgIHJldHVybiAvXiguKilcXCQkL1xuICB9XG4gIHNlYXJjaCh0ZXh0KSB7XG4gICAgY29uc3QgaXNNYXRjaCA9IHRleHQuZW5kc1dpdGgodGhpcy5wYXR0ZXJuKTtcblxuICAgIHJldHVybiB7XG4gICAgICBpc01hdGNoLFxuICAgICAgc2NvcmU6IGlzTWF0Y2ggPyAwIDogMSxcbiAgICAgIGluZGljZXM6IFt0ZXh0Lmxlbmd0aCAtIHRoaXMucGF0dGVybi5sZW5ndGgsIHRleHQubGVuZ3RoIC0gMV1cbiAgICB9XG4gIH1cbn1cblxuLy8gVG9rZW46ICEuZmlsZSRcblxuY2xhc3MgSW52ZXJzZVN1ZmZpeEV4YWN0TWF0Y2ggZXh0ZW5kcyBCYXNlTWF0Y2gge1xuICBjb25zdHJ1Y3RvcihwYXR0ZXJuKSB7XG4gICAgc3VwZXIocGF0dGVybik7XG4gIH1cbiAgc3RhdGljIGdldCB0eXBlKCkge1xuICAgIHJldHVybiAnaW52ZXJzZS1zdWZmaXgtZXhhY3QnXG4gIH1cbiAgc3RhdGljIGdldCBtdWx0aVJlZ2V4KCkge1xuICAgIHJldHVybiAvXiFcIiguKilcIlxcJCQvXG4gIH1cbiAgc3RhdGljIGdldCBzaW5nbGVSZWdleCgpIHtcbiAgICByZXR1cm4gL14hKC4qKVxcJCQvXG4gIH1cbiAgc2VhcmNoKHRleHQpIHtcbiAgICBjb25zdCBpc01hdGNoID0gIXRleHQuZW5kc1dpdGgodGhpcy5wYXR0ZXJuKTtcbiAgICByZXR1cm4ge1xuICAgICAgaXNNYXRjaCxcbiAgICAgIHNjb3JlOiBpc01hdGNoID8gMCA6IDEsXG4gICAgICBpbmRpY2VzOiBbMCwgdGV4dC5sZW5ndGggLSAxXVxuICAgIH1cbiAgfVxufVxuXG5jbGFzcyBGdXp6eU1hdGNoIGV4dGVuZHMgQmFzZU1hdGNoIHtcbiAgY29uc3RydWN0b3IoXG4gICAgcGF0dGVybixcbiAgICB7XG4gICAgICBsb2NhdGlvbiA9IENvbmZpZy5sb2NhdGlvbixcbiAgICAgIHRocmVzaG9sZCA9IENvbmZpZy50aHJlc2hvbGQsXG4gICAgICBkaXN0YW5jZSA9IENvbmZpZy5kaXN0YW5jZSxcbiAgICAgIGluY2x1ZGVNYXRjaGVzID0gQ29uZmlnLmluY2x1ZGVNYXRjaGVzLFxuICAgICAgZmluZEFsbE1hdGNoZXMgPSBDb25maWcuZmluZEFsbE1hdGNoZXMsXG4gICAgICBtaW5NYXRjaENoYXJMZW5ndGggPSBDb25maWcubWluTWF0Y2hDaGFyTGVuZ3RoLFxuICAgICAgaXNDYXNlU2Vuc2l0aXZlID0gQ29uZmlnLmlzQ2FzZVNlbnNpdGl2ZSxcbiAgICAgIGlnbm9yZUxvY2F0aW9uID0gQ29uZmlnLmlnbm9yZUxvY2F0aW9uXG4gICAgfSA9IHt9XG4gICkge1xuICAgIHN1cGVyKHBhdHRlcm4pO1xuICAgIHRoaXMuX2JpdGFwU2VhcmNoID0gbmV3IEJpdGFwU2VhcmNoKHBhdHRlcm4sIHtcbiAgICAgIGxvY2F0aW9uLFxuICAgICAgdGhyZXNob2xkLFxuICAgICAgZGlzdGFuY2UsXG4gICAgICBpbmNsdWRlTWF0Y2hlcyxcbiAgICAgIGZpbmRBbGxNYXRjaGVzLFxuICAgICAgbWluTWF0Y2hDaGFyTGVuZ3RoLFxuICAgICAgaXNDYXNlU2Vuc2l0aXZlLFxuICAgICAgaWdub3JlTG9jYXRpb25cbiAgICB9KTtcbiAgfVxuICBzdGF0aWMgZ2V0IHR5cGUoKSB7XG4gICAgcmV0dXJuICdmdXp6eSdcbiAgfVxuICBzdGF0aWMgZ2V0IG11bHRpUmVnZXgoKSB7XG4gICAgcmV0dXJuIC9eXCIoLiopXCIkL1xuICB9XG4gIHN0YXRpYyBnZXQgc2luZ2xlUmVnZXgoKSB7XG4gICAgcmV0dXJuIC9eKC4qKSQvXG4gIH1cbiAgc2VhcmNoKHRleHQpIHtcbiAgICByZXR1cm4gdGhpcy5fYml0YXBTZWFyY2guc2VhcmNoSW4odGV4dClcbiAgfVxufVxuXG4vLyBUb2tlbjogJ2ZpbGVcblxuY2xhc3MgSW5jbHVkZU1hdGNoIGV4dGVuZHMgQmFzZU1hdGNoIHtcbiAgY29uc3RydWN0b3IocGF0dGVybikge1xuICAgIHN1cGVyKHBhdHRlcm4pO1xuICB9XG4gIHN0YXRpYyBnZXQgdHlwZSgpIHtcbiAgICByZXR1cm4gJ2luY2x1ZGUnXG4gIH1cbiAgc3RhdGljIGdldCBtdWx0aVJlZ2V4KCkge1xuICAgIHJldHVybiAvXidcIiguKilcIiQvXG4gIH1cbiAgc3RhdGljIGdldCBzaW5nbGVSZWdleCgpIHtcbiAgICByZXR1cm4gL14nKC4qKSQvXG4gIH1cbiAgc2VhcmNoKHRleHQpIHtcbiAgICBsZXQgbG9jYXRpb24gPSAwO1xuICAgIGxldCBpbmRleDtcblxuICAgIGNvbnN0IGluZGljZXMgPSBbXTtcbiAgICBjb25zdCBwYXR0ZXJuTGVuID0gdGhpcy5wYXR0ZXJuLmxlbmd0aDtcblxuICAgIC8vIEdldCBhbGwgZXhhY3QgbWF0Y2hlc1xuICAgIHdoaWxlICgoaW5kZXggPSB0ZXh0LmluZGV4T2YodGhpcy5wYXR0ZXJuLCBsb2NhdGlvbikpID4gLTEpIHtcbiAgICAgIGxvY2F0aW9uID0gaW5kZXggKyBwYXR0ZXJuTGVuO1xuICAgICAgaW5kaWNlcy5wdXNoKFtpbmRleCwgbG9jYXRpb24gLSAxXSk7XG4gICAgfVxuXG4gICAgY29uc3QgaXNNYXRjaCA9ICEhaW5kaWNlcy5sZW5ndGg7XG5cbiAgICByZXR1cm4ge1xuICAgICAgaXNNYXRjaCxcbiAgICAgIHNjb3JlOiBpc01hdGNoID8gMCA6IDEsXG4gICAgICBpbmRpY2VzXG4gICAgfVxuICB9XG59XG5cbi8vIFx1Mjc1N09yZGVyIGlzIGltcG9ydGFudC4gRE8gTk9UIENIQU5HRS5cbmNvbnN0IHNlYXJjaGVycyA9IFtcbiAgRXhhY3RNYXRjaCxcbiAgSW5jbHVkZU1hdGNoLFxuICBQcmVmaXhFeGFjdE1hdGNoLFxuICBJbnZlcnNlUHJlZml4RXhhY3RNYXRjaCxcbiAgSW52ZXJzZVN1ZmZpeEV4YWN0TWF0Y2gsXG4gIFN1ZmZpeEV4YWN0TWF0Y2gsXG4gIEludmVyc2VFeGFjdE1hdGNoLFxuICBGdXp6eU1hdGNoXG5dO1xuXG5jb25zdCBzZWFyY2hlcnNMZW4gPSBzZWFyY2hlcnMubGVuZ3RoO1xuXG4vLyBSZWdleCB0byBzcGxpdCBieSBzcGFjZXMsIGJ1dCBrZWVwIGFueXRoaW5nIGluIHF1b3RlcyB0b2dldGhlclxuY29uc3QgU1BBQ0VfUkUgPSAvICsoPz0oPzpbXlxcXCJdKlxcXCJbXlxcXCJdKlxcXCIpKlteXFxcIl0qJCkvO1xuY29uc3QgT1JfVE9LRU4gPSAnfCc7XG5cbi8vIFJldHVybiBhIDJEIGFycmF5IHJlcHJlc2VudGF0aW9uIG9mIHRoZSBxdWVyeSwgZm9yIHNpbXBsZXIgcGFyc2luZy5cbi8vIEV4YW1wbGU6XG4vLyBcIl5jb3JlIGdvJCB8IHJiJCB8IHB5JCB4eSRcIiA9PiBbW1wiXmNvcmVcIiwgXCJnbyRcIl0sIFtcInJiJFwiXSwgW1wicHkkXCIsIFwieHkkXCJdXVxuZnVuY3Rpb24gcGFyc2VRdWVyeShwYXR0ZXJuLCBvcHRpb25zID0ge30pIHtcbiAgcmV0dXJuIHBhdHRlcm4uc3BsaXQoT1JfVE9LRU4pLm1hcCgoaXRlbSkgPT4ge1xuICAgIGxldCBxdWVyeSA9IGl0ZW1cbiAgICAgIC50cmltKClcbiAgICAgIC5zcGxpdChTUEFDRV9SRSlcbiAgICAgIC5maWx0ZXIoKGl0ZW0pID0+IGl0ZW0gJiYgISFpdGVtLnRyaW0oKSk7XG5cbiAgICBsZXQgcmVzdWx0cyA9IFtdO1xuICAgIGZvciAobGV0IGkgPSAwLCBsZW4gPSBxdWVyeS5sZW5ndGg7IGkgPCBsZW47IGkgKz0gMSkge1xuICAgICAgY29uc3QgcXVlcnlJdGVtID0gcXVlcnlbaV07XG5cbiAgICAgIC8vIDEuIEhhbmRsZSBtdWx0aXBsZSBxdWVyeSBtYXRjaCAoaS5lLCBvbmNlIHRoYXQgYXJlIHF1b3RlZCwgbGlrZSBgXCJoZWxsbyB3b3JsZFwiYClcbiAgICAgIGxldCBmb3VuZCA9IGZhbHNlO1xuICAgICAgbGV0IGlkeCA9IC0xO1xuICAgICAgd2hpbGUgKCFmb3VuZCAmJiArK2lkeCA8IHNlYXJjaGVyc0xlbikge1xuICAgICAgICBjb25zdCBzZWFyY2hlciA9IHNlYXJjaGVyc1tpZHhdO1xuICAgICAgICBsZXQgdG9rZW4gPSBzZWFyY2hlci5pc011bHRpTWF0Y2gocXVlcnlJdGVtKTtcbiAgICAgICAgaWYgKHRva2VuKSB7XG4gICAgICAgICAgcmVzdWx0cy5wdXNoKG5ldyBzZWFyY2hlcih0b2tlbiwgb3B0aW9ucykpO1xuICAgICAgICAgIGZvdW5kID0gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBpZiAoZm91bmQpIHtcbiAgICAgICAgY29udGludWVcbiAgICAgIH1cblxuICAgICAgLy8gMi4gSGFuZGxlIHNpbmdsZSBxdWVyeSBtYXRjaGVzIChpLmUsIG9uY2UgdGhhdCBhcmUgKm5vdCogcXVvdGVkKVxuICAgICAgaWR4ID0gLTE7XG4gICAgICB3aGlsZSAoKytpZHggPCBzZWFyY2hlcnNMZW4pIHtcbiAgICAgICAgY29uc3Qgc2VhcmNoZXIgPSBzZWFyY2hlcnNbaWR4XTtcbiAgICAgICAgbGV0IHRva2VuID0gc2VhcmNoZXIuaXNTaW5nbGVNYXRjaChxdWVyeUl0ZW0pO1xuICAgICAgICBpZiAodG9rZW4pIHtcbiAgICAgICAgICByZXN1bHRzLnB1c2gobmV3IHNlYXJjaGVyKHRva2VuLCBvcHRpb25zKSk7XG4gICAgICAgICAgYnJlYWtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiByZXN1bHRzXG4gIH0pXG59XG5cbi8vIFRoZXNlIGV4dGVuZGVkIG1hdGNoZXJzIGNhbiByZXR1cm4gYW4gYXJyYXkgb2YgbWF0Y2hlcywgYXMgb3Bwb3NlZFxuLy8gdG8gYSBzaW5nbCBtYXRjaFxuY29uc3QgTXVsdGlNYXRjaFNldCA9IG5ldyBTZXQoW0Z1enp5TWF0Y2gudHlwZSwgSW5jbHVkZU1hdGNoLnR5cGVdKTtcblxuLyoqXG4gKiBDb21tYW5kLWxpa2Ugc2VhcmNoaW5nXG4gKiA9PT09PT09PT09PT09PT09PT09PT09XG4gKlxuICogR2l2ZW4gbXVsdGlwbGUgc2VhcmNoIHRlcm1zIGRlbGltaXRlZCBieSBzcGFjZXMuZS5nLiBgXmpzY3JpcHQgLnB5dGhvbiQgcnVieSAhamF2YWAsXG4gKiBzZWFyY2ggaW4gYSBnaXZlbiB0ZXh0LlxuICpcbiAqIFNlYXJjaCBzeW50YXg6XG4gKlxuICogfCBUb2tlbiAgICAgICB8IE1hdGNoIHR5cGUgICAgICAgICAgICAgICAgIHwgRGVzY3JpcHRpb24gICAgICAgICAgICAgICAgICAgICAgICAgICAgfFxuICogfCAtLS0tLS0tLS0tLSB8IC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tIHwgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gfFxuICogfCBganNjcmlwdGAgICB8IGZ1enp5LW1hdGNoICAgICAgICAgICAgICAgIHwgSXRlbXMgdGhhdCBmdXp6eSBtYXRjaCBganNjcmlwdGAgICAgICAgfFxuICogfCBgPXNjaGVtZWAgICB8IGV4YWN0LW1hdGNoICAgICAgICAgICAgICAgIHwgSXRlbXMgdGhhdCBhcmUgYHNjaGVtZWAgICAgICAgICAgICAgICAgfFxuICogfCBgJ3B5dGhvbmAgICB8IGluY2x1ZGUtbWF0Y2ggICAgICAgICAgICAgIHwgSXRlbXMgdGhhdCBpbmNsdWRlIGBweXRob25gICAgICAgICAgICAgfFxuICogfCBgIXJ1YnlgICAgICB8IGludmVyc2UtZXhhY3QtbWF0Y2ggICAgICAgIHwgSXRlbXMgdGhhdCBkbyBub3QgaW5jbHVkZSBgcnVieWAgICAgICAgfFxuICogfCBgXmphdmFgICAgICB8IHByZWZpeC1leGFjdC1tYXRjaCAgICAgICAgIHwgSXRlbXMgdGhhdCBzdGFydCB3aXRoIGBqYXZhYCAgICAgICAgICAgfFxuICogfCBgIV5lYXJsYW5nYCB8IGludmVyc2UtcHJlZml4LWV4YWN0LW1hdGNoIHwgSXRlbXMgdGhhdCBkbyBub3Qgc3RhcnQgd2l0aCBgZWFybGFuZ2AgfFxuICogfCBgLmpzJGAgICAgICB8IHN1ZmZpeC1leGFjdC1tYXRjaCAgICAgICAgIHwgSXRlbXMgdGhhdCBlbmQgd2l0aCBgLmpzYCAgICAgICAgICAgICAgfFxuICogfCBgIS5nbyRgICAgICB8IGludmVyc2Utc3VmZml4LWV4YWN0LW1hdGNoIHwgSXRlbXMgdGhhdCBkbyBub3QgZW5kIHdpdGggYC5nb2AgICAgICAgfFxuICpcbiAqIEEgc2luZ2xlIHBpcGUgY2hhcmFjdGVyIGFjdHMgYXMgYW4gT1Igb3BlcmF0b3IuIEZvciBleGFtcGxlLCB0aGUgZm9sbG93aW5nXG4gKiBxdWVyeSBtYXRjaGVzIGVudHJpZXMgdGhhdCBzdGFydCB3aXRoIGBjb3JlYCBhbmQgZW5kIHdpdGggZWl0aGVyYGdvYCwgYHJiYCxcbiAqIG9yYHB5YC5cbiAqXG4gKiBgYGBcbiAqIF5jb3JlIGdvJCB8IHJiJCB8IHB5JFxuICogYGBgXG4gKi9cbmNsYXNzIEV4dGVuZGVkU2VhcmNoIHtcbiAgY29uc3RydWN0b3IoXG4gICAgcGF0dGVybixcbiAgICB7XG4gICAgICBpc0Nhc2VTZW5zaXRpdmUgPSBDb25maWcuaXNDYXNlU2Vuc2l0aXZlLFxuICAgICAgaW5jbHVkZU1hdGNoZXMgPSBDb25maWcuaW5jbHVkZU1hdGNoZXMsXG4gICAgICBtaW5NYXRjaENoYXJMZW5ndGggPSBDb25maWcubWluTWF0Y2hDaGFyTGVuZ3RoLFxuICAgICAgaWdub3JlTG9jYXRpb24gPSBDb25maWcuaWdub3JlTG9jYXRpb24sXG4gICAgICBmaW5kQWxsTWF0Y2hlcyA9IENvbmZpZy5maW5kQWxsTWF0Y2hlcyxcbiAgICAgIGxvY2F0aW9uID0gQ29uZmlnLmxvY2F0aW9uLFxuICAgICAgdGhyZXNob2xkID0gQ29uZmlnLnRocmVzaG9sZCxcbiAgICAgIGRpc3RhbmNlID0gQ29uZmlnLmRpc3RhbmNlXG4gICAgfSA9IHt9XG4gICkge1xuICAgIHRoaXMucXVlcnkgPSBudWxsO1xuICAgIHRoaXMub3B0aW9ucyA9IHtcbiAgICAgIGlzQ2FzZVNlbnNpdGl2ZSxcbiAgICAgIGluY2x1ZGVNYXRjaGVzLFxuICAgICAgbWluTWF0Y2hDaGFyTGVuZ3RoLFxuICAgICAgZmluZEFsbE1hdGNoZXMsXG4gICAgICBpZ25vcmVMb2NhdGlvbixcbiAgICAgIGxvY2F0aW9uLFxuICAgICAgdGhyZXNob2xkLFxuICAgICAgZGlzdGFuY2VcbiAgICB9O1xuXG4gICAgdGhpcy5wYXR0ZXJuID0gaXNDYXNlU2Vuc2l0aXZlID8gcGF0dGVybiA6IHBhdHRlcm4udG9Mb3dlckNhc2UoKTtcbiAgICB0aGlzLnF1ZXJ5ID0gcGFyc2VRdWVyeSh0aGlzLnBhdHRlcm4sIHRoaXMub3B0aW9ucyk7XG4gIH1cblxuICBzdGF0aWMgY29uZGl0aW9uKF8sIG9wdGlvbnMpIHtcbiAgICByZXR1cm4gb3B0aW9ucy51c2VFeHRlbmRlZFNlYXJjaFxuICB9XG5cbiAgc2VhcmNoSW4odGV4dCkge1xuICAgIGNvbnN0IHF1ZXJ5ID0gdGhpcy5xdWVyeTtcblxuICAgIGlmICghcXVlcnkpIHtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIGlzTWF0Y2g6IGZhbHNlLFxuICAgICAgICBzY29yZTogMVxuICAgICAgfVxuICAgIH1cblxuICAgIGNvbnN0IHsgaW5jbHVkZU1hdGNoZXMsIGlzQ2FzZVNlbnNpdGl2ZSB9ID0gdGhpcy5vcHRpb25zO1xuXG4gICAgdGV4dCA9IGlzQ2FzZVNlbnNpdGl2ZSA/IHRleHQgOiB0ZXh0LnRvTG93ZXJDYXNlKCk7XG5cbiAgICBsZXQgbnVtTWF0Y2hlcyA9IDA7XG4gICAgbGV0IGFsbEluZGljZXMgPSBbXTtcbiAgICBsZXQgdG90YWxTY29yZSA9IDA7XG5cbiAgICAvLyBPUnNcbiAgICBmb3IgKGxldCBpID0gMCwgcUxlbiA9IHF1ZXJ5Lmxlbmd0aDsgaSA8IHFMZW47IGkgKz0gMSkge1xuICAgICAgY29uc3Qgc2VhcmNoZXJzID0gcXVlcnlbaV07XG5cbiAgICAgIC8vIFJlc2V0IGluZGljZXNcbiAgICAgIGFsbEluZGljZXMubGVuZ3RoID0gMDtcbiAgICAgIG51bU1hdGNoZXMgPSAwO1xuXG4gICAgICAvLyBBTkRzXG4gICAgICBmb3IgKGxldCBqID0gMCwgcExlbiA9IHNlYXJjaGVycy5sZW5ndGg7IGogPCBwTGVuOyBqICs9IDEpIHtcbiAgICAgICAgY29uc3Qgc2VhcmNoZXIgPSBzZWFyY2hlcnNbal07XG4gICAgICAgIGNvbnN0IHsgaXNNYXRjaCwgaW5kaWNlcywgc2NvcmUgfSA9IHNlYXJjaGVyLnNlYXJjaCh0ZXh0KTtcblxuICAgICAgICBpZiAoaXNNYXRjaCkge1xuICAgICAgICAgIG51bU1hdGNoZXMgKz0gMTtcbiAgICAgICAgICB0b3RhbFNjb3JlICs9IHNjb3JlO1xuICAgICAgICAgIGlmIChpbmNsdWRlTWF0Y2hlcykge1xuICAgICAgICAgICAgY29uc3QgdHlwZSA9IHNlYXJjaGVyLmNvbnN0cnVjdG9yLnR5cGU7XG4gICAgICAgICAgICBpZiAoTXVsdGlNYXRjaFNldC5oYXModHlwZSkpIHtcbiAgICAgICAgICAgICAgYWxsSW5kaWNlcyA9IFsuLi5hbGxJbmRpY2VzLCAuLi5pbmRpY2VzXTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIGFsbEluZGljZXMucHVzaChpbmRpY2VzKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgdG90YWxTY29yZSA9IDA7XG4gICAgICAgICAgbnVtTWF0Y2hlcyA9IDA7XG4gICAgICAgICAgYWxsSW5kaWNlcy5sZW5ndGggPSAwO1xuICAgICAgICAgIGJyZWFrXG4gICAgICAgIH1cbiAgICAgIH1cblxuICAgICAgLy8gT1IgY29uZGl0aW9uLCBzbyBpZiBUUlVFLCByZXR1cm5cbiAgICAgIGlmIChudW1NYXRjaGVzKSB7XG4gICAgICAgIGxldCByZXN1bHQgPSB7XG4gICAgICAgICAgaXNNYXRjaDogdHJ1ZSxcbiAgICAgICAgICBzY29yZTogdG90YWxTY29yZSAvIG51bU1hdGNoZXNcbiAgICAgICAgfTtcblxuICAgICAgICBpZiAoaW5jbHVkZU1hdGNoZXMpIHtcbiAgICAgICAgICByZXN1bHQuaW5kaWNlcyA9IGFsbEluZGljZXM7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gcmVzdWx0XG4gICAgICB9XG4gICAgfVxuXG4gICAgLy8gTm90aGluZyB3YXMgbWF0Y2hlZFxuICAgIHJldHVybiB7XG4gICAgICBpc01hdGNoOiBmYWxzZSxcbiAgICAgIHNjb3JlOiAxXG4gICAgfVxuICB9XG59XG5cbmNvbnN0IHJlZ2lzdGVyZWRTZWFyY2hlcnMgPSBbXTtcblxuZnVuY3Rpb24gcmVnaXN0ZXIoLi4uYXJncykge1xuICByZWdpc3RlcmVkU2VhcmNoZXJzLnB1c2goLi4uYXJncyk7XG59XG5cbmZ1bmN0aW9uIGNyZWF0ZVNlYXJjaGVyKHBhdHRlcm4sIG9wdGlvbnMpIHtcbiAgZm9yIChsZXQgaSA9IDAsIGxlbiA9IHJlZ2lzdGVyZWRTZWFyY2hlcnMubGVuZ3RoOyBpIDwgbGVuOyBpICs9IDEpIHtcbiAgICBsZXQgc2VhcmNoZXJDbGFzcyA9IHJlZ2lzdGVyZWRTZWFyY2hlcnNbaV07XG4gICAgaWYgKHNlYXJjaGVyQ2xhc3MuY29uZGl0aW9uKHBhdHRlcm4sIG9wdGlvbnMpKSB7XG4gICAgICByZXR1cm4gbmV3IHNlYXJjaGVyQ2xhc3MocGF0dGVybiwgb3B0aW9ucylcbiAgICB9XG4gIH1cblxuICByZXR1cm4gbmV3IEJpdGFwU2VhcmNoKHBhdHRlcm4sIG9wdGlvbnMpXG59XG5cbmNvbnN0IExvZ2ljYWxPcGVyYXRvciA9IHtcbiAgQU5EOiAnJGFuZCcsXG4gIE9SOiAnJG9yJ1xufTtcblxuY29uc3QgS2V5VHlwZSA9IHtcbiAgUEFUSDogJyRwYXRoJyxcbiAgUEFUVEVSTjogJyR2YWwnXG59O1xuXG5jb25zdCBpc0V4cHJlc3Npb24gPSAocXVlcnkpID0+XG4gICEhKHF1ZXJ5W0xvZ2ljYWxPcGVyYXRvci5BTkRdIHx8IHF1ZXJ5W0xvZ2ljYWxPcGVyYXRvci5PUl0pO1xuXG5jb25zdCBpc1BhdGggPSAocXVlcnkpID0+ICEhcXVlcnlbS2V5VHlwZS5QQVRIXTtcblxuY29uc3QgaXNMZWFmID0gKHF1ZXJ5KSA9PlxuICAhaXNBcnJheShxdWVyeSkgJiYgaXNPYmplY3QocXVlcnkpICYmICFpc0V4cHJlc3Npb24ocXVlcnkpO1xuXG5jb25zdCBjb252ZXJ0VG9FeHBsaWNpdCA9IChxdWVyeSkgPT4gKHtcbiAgW0xvZ2ljYWxPcGVyYXRvci5BTkRdOiBPYmplY3Qua2V5cyhxdWVyeSkubWFwKChrZXkpID0+ICh7XG4gICAgW2tleV06IHF1ZXJ5W2tleV1cbiAgfSkpXG59KTtcblxuLy8gV2hlbiBgYXV0b2AgaXMgYHRydWVgLCB0aGUgcGFyc2UgZnVuY3Rpb24gd2lsbCBpbmZlciBhbmQgaW5pdGlhbGl6ZSBhbmQgYWRkXG4vLyB0aGUgYXBwcm9wcmlhdGUgYFNlYXJjaGVyYCBpbnN0YW5jZVxuZnVuY3Rpb24gcGFyc2UocXVlcnksIG9wdGlvbnMsIHsgYXV0byA9IHRydWUgfSA9IHt9KSB7XG4gIGNvbnN0IG5leHQgPSAocXVlcnkpID0+IHtcbiAgICBsZXQga2V5cyA9IE9iamVjdC5rZXlzKHF1ZXJ5KTtcblxuICAgIGNvbnN0IGlzUXVlcnlQYXRoID0gaXNQYXRoKHF1ZXJ5KTtcblxuICAgIGlmICghaXNRdWVyeVBhdGggJiYga2V5cy5sZW5ndGggPiAxICYmICFpc0V4cHJlc3Npb24ocXVlcnkpKSB7XG4gICAgICByZXR1cm4gbmV4dChjb252ZXJ0VG9FeHBsaWNpdChxdWVyeSkpXG4gICAgfVxuXG4gICAgaWYgKGlzTGVhZihxdWVyeSkpIHtcbiAgICAgIGNvbnN0IGtleSA9IGlzUXVlcnlQYXRoID8gcXVlcnlbS2V5VHlwZS5QQVRIXSA6IGtleXNbMF07XG5cbiAgICAgIGNvbnN0IHBhdHRlcm4gPSBpc1F1ZXJ5UGF0aCA/IHF1ZXJ5W0tleVR5cGUuUEFUVEVSTl0gOiBxdWVyeVtrZXldO1xuXG4gICAgICBpZiAoIWlzU3RyaW5nKHBhdHRlcm4pKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvcihMT0dJQ0FMX1NFQVJDSF9JTlZBTElEX1FVRVJZX0ZPUl9LRVkoa2V5KSlcbiAgICAgIH1cblxuICAgICAgY29uc3Qgb2JqID0ge1xuICAgICAgICBrZXlJZDogY3JlYXRlS2V5SWQoa2V5KSxcbiAgICAgICAgcGF0dGVyblxuICAgICAgfTtcblxuICAgICAgaWYgKGF1dG8pIHtcbiAgICAgICAgb2JqLnNlYXJjaGVyID0gY3JlYXRlU2VhcmNoZXIocGF0dGVybiwgb3B0aW9ucyk7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiBvYmpcbiAgICB9XG5cbiAgICBsZXQgbm9kZSA9IHtcbiAgICAgIGNoaWxkcmVuOiBbXSxcbiAgICAgIG9wZXJhdG9yOiBrZXlzWzBdXG4gICAgfTtcblxuICAgIGtleXMuZm9yRWFjaCgoa2V5KSA9PiB7XG4gICAgICBjb25zdCB2YWx1ZSA9IHF1ZXJ5W2tleV07XG5cbiAgICAgIGlmIChpc0FycmF5KHZhbHVlKSkge1xuICAgICAgICB2YWx1ZS5mb3JFYWNoKChpdGVtKSA9PiB7XG4gICAgICAgICAgbm9kZS5jaGlsZHJlbi5wdXNoKG5leHQoaXRlbSkpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiBub2RlXG4gIH07XG5cbiAgaWYgKCFpc0V4cHJlc3Npb24ocXVlcnkpKSB7XG4gICAgcXVlcnkgPSBjb252ZXJ0VG9FeHBsaWNpdChxdWVyeSk7XG4gIH1cblxuICByZXR1cm4gbmV4dChxdWVyeSlcbn1cblxuLy8gUHJhY3RpY2FsIHNjb3JpbmcgZnVuY3Rpb25cbmZ1bmN0aW9uIGNvbXB1dGVTY29yZShcbiAgcmVzdWx0cyxcbiAgeyBpZ25vcmVGaWVsZE5vcm0gPSBDb25maWcuaWdub3JlRmllbGROb3JtIH1cbikge1xuICByZXN1bHRzLmZvckVhY2goKHJlc3VsdCkgPT4ge1xuICAgIGxldCB0b3RhbFNjb3JlID0gMTtcblxuICAgIHJlc3VsdC5tYXRjaGVzLmZvckVhY2goKHsga2V5LCBub3JtLCBzY29yZSB9KSA9PiB7XG4gICAgICBjb25zdCB3ZWlnaHQgPSBrZXkgPyBrZXkud2VpZ2h0IDogbnVsbDtcblxuICAgICAgdG90YWxTY29yZSAqPSBNYXRoLnBvdyhcbiAgICAgICAgc2NvcmUgPT09IDAgJiYgd2VpZ2h0ID8gTnVtYmVyLkVQU0lMT04gOiBzY29yZSxcbiAgICAgICAgKHdlaWdodCB8fCAxKSAqIChpZ25vcmVGaWVsZE5vcm0gPyAxIDogbm9ybSlcbiAgICAgICk7XG4gICAgfSk7XG5cbiAgICByZXN1bHQuc2NvcmUgPSB0b3RhbFNjb3JlO1xuICB9KTtcbn1cblxuZnVuY3Rpb24gdHJhbnNmb3JtTWF0Y2hlcyhyZXN1bHQsIGRhdGEpIHtcbiAgY29uc3QgbWF0Y2hlcyA9IHJlc3VsdC5tYXRjaGVzO1xuICBkYXRhLm1hdGNoZXMgPSBbXTtcblxuICBpZiAoIWlzRGVmaW5lZChtYXRjaGVzKSkge1xuICAgIHJldHVyblxuICB9XG5cbiAgbWF0Y2hlcy5mb3JFYWNoKChtYXRjaCkgPT4ge1xuICAgIGlmICghaXNEZWZpbmVkKG1hdGNoLmluZGljZXMpIHx8ICFtYXRjaC5pbmRpY2VzLmxlbmd0aCkge1xuICAgICAgcmV0dXJuXG4gICAgfVxuXG4gICAgY29uc3QgeyBpbmRpY2VzLCB2YWx1ZSB9ID0gbWF0Y2g7XG5cbiAgICBsZXQgb2JqID0ge1xuICAgICAgaW5kaWNlcyxcbiAgICAgIHZhbHVlXG4gICAgfTtcblxuICAgIGlmIChtYXRjaC5rZXkpIHtcbiAgICAgIG9iai5rZXkgPSBtYXRjaC5rZXkuc3JjO1xuICAgIH1cblxuICAgIGlmIChtYXRjaC5pZHggPiAtMSkge1xuICAgICAgb2JqLnJlZkluZGV4ID0gbWF0Y2guaWR4O1xuICAgIH1cblxuICAgIGRhdGEubWF0Y2hlcy5wdXNoKG9iaik7XG4gIH0pO1xufVxuXG5mdW5jdGlvbiB0cmFuc2Zvcm1TY29yZShyZXN1bHQsIGRhdGEpIHtcbiAgZGF0YS5zY29yZSA9IHJlc3VsdC5zY29yZTtcbn1cblxuZnVuY3Rpb24gZm9ybWF0KFxuICByZXN1bHRzLFxuICBkb2NzLFxuICB7XG4gICAgaW5jbHVkZU1hdGNoZXMgPSBDb25maWcuaW5jbHVkZU1hdGNoZXMsXG4gICAgaW5jbHVkZVNjb3JlID0gQ29uZmlnLmluY2x1ZGVTY29yZVxuICB9ID0ge31cbikge1xuICBjb25zdCB0cmFuc2Zvcm1lcnMgPSBbXTtcblxuICBpZiAoaW5jbHVkZU1hdGNoZXMpIHRyYW5zZm9ybWVycy5wdXNoKHRyYW5zZm9ybU1hdGNoZXMpO1xuICBpZiAoaW5jbHVkZVNjb3JlKSB0cmFuc2Zvcm1lcnMucHVzaCh0cmFuc2Zvcm1TY29yZSk7XG5cbiAgcmV0dXJuIHJlc3VsdHMubWFwKChyZXN1bHQpID0+IHtcbiAgICBjb25zdCB7IGlkeCB9ID0gcmVzdWx0O1xuXG4gICAgY29uc3QgZGF0YSA9IHtcbiAgICAgIGl0ZW06IGRvY3NbaWR4XSxcbiAgICAgIHJlZkluZGV4OiBpZHhcbiAgICB9O1xuXG4gICAgaWYgKHRyYW5zZm9ybWVycy5sZW5ndGgpIHtcbiAgICAgIHRyYW5zZm9ybWVycy5mb3JFYWNoKCh0cmFuc2Zvcm1lcikgPT4ge1xuICAgICAgICB0cmFuc2Zvcm1lcihyZXN1bHQsIGRhdGEpO1xuICAgICAgfSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGRhdGFcbiAgfSlcbn1cblxuY2xhc3MgRnVzZSB7XG4gIGNvbnN0cnVjdG9yKGRvY3MsIG9wdGlvbnMgPSB7fSwgaW5kZXgpIHtcbiAgICB0aGlzLm9wdGlvbnMgPSB7IC4uLkNvbmZpZywgLi4ub3B0aW9ucyB9O1xuXG4gICAgaWYgKFxuICAgICAgdGhpcy5vcHRpb25zLnVzZUV4dGVuZGVkU2VhcmNoICYmXG4gICAgICAhdHJ1ZVxuICAgICkge31cblxuICAgIHRoaXMuX2tleVN0b3JlID0gbmV3IEtleVN0b3JlKHRoaXMub3B0aW9ucy5rZXlzKTtcblxuICAgIHRoaXMuc2V0Q29sbGVjdGlvbihkb2NzLCBpbmRleCk7XG4gIH1cblxuICBzZXRDb2xsZWN0aW9uKGRvY3MsIGluZGV4KSB7XG4gICAgdGhpcy5fZG9jcyA9IGRvY3M7XG5cbiAgICBpZiAoaW5kZXggJiYgIShpbmRleCBpbnN0YW5jZW9mIEZ1c2VJbmRleCkpIHtcbiAgICAgIHRocm93IG5ldyBFcnJvcihJTkNPUlJFQ1RfSU5ERVhfVFlQRSlcbiAgICB9XG5cbiAgICB0aGlzLl9teUluZGV4ID1cbiAgICAgIGluZGV4IHx8XG4gICAgICBjcmVhdGVJbmRleCh0aGlzLm9wdGlvbnMua2V5cywgdGhpcy5fZG9jcywge1xuICAgICAgICBnZXRGbjogdGhpcy5vcHRpb25zLmdldEZuLFxuICAgICAgICBmaWVsZE5vcm1XZWlnaHQ6IHRoaXMub3B0aW9ucy5maWVsZE5vcm1XZWlnaHRcbiAgICAgIH0pO1xuICB9XG5cbiAgYWRkKGRvYykge1xuICAgIGlmICghaXNEZWZpbmVkKGRvYykpIHtcbiAgICAgIHJldHVyblxuICAgIH1cblxuICAgIHRoaXMuX2RvY3MucHVzaChkb2MpO1xuICAgIHRoaXMuX215SW5kZXguYWRkKGRvYyk7XG4gIH1cblxuICByZW1vdmUocHJlZGljYXRlID0gKC8qIGRvYywgaWR4ICovKSA9PiBmYWxzZSkge1xuICAgIGNvbnN0IHJlc3VsdHMgPSBbXTtcblxuICAgIGZvciAobGV0IGkgPSAwLCBsZW4gPSB0aGlzLl9kb2NzLmxlbmd0aDsgaSA8IGxlbjsgaSArPSAxKSB7XG4gICAgICBjb25zdCBkb2MgPSB0aGlzLl9kb2NzW2ldO1xuICAgICAgaWYgKHByZWRpY2F0ZShkb2MsIGkpKSB7XG4gICAgICAgIHRoaXMucmVtb3ZlQXQoaSk7XG4gICAgICAgIGkgLT0gMTtcbiAgICAgICAgbGVuIC09IDE7XG5cbiAgICAgICAgcmVzdWx0cy5wdXNoKGRvYyk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIHJlc3VsdHNcbiAgfVxuXG4gIHJlbW92ZUF0KGlkeCkge1xuICAgIHRoaXMuX2RvY3Muc3BsaWNlKGlkeCwgMSk7XG4gICAgdGhpcy5fbXlJbmRleC5yZW1vdmVBdChpZHgpO1xuICB9XG5cbiAgZ2V0SW5kZXgoKSB7XG4gICAgcmV0dXJuIHRoaXMuX215SW5kZXhcbiAgfVxuXG4gIHNlYXJjaChxdWVyeSwgeyBsaW1pdCA9IC0xIH0gPSB7fSkge1xuICAgIGNvbnN0IHtcbiAgICAgIGluY2x1ZGVNYXRjaGVzLFxuICAgICAgaW5jbHVkZVNjb3JlLFxuICAgICAgc2hvdWxkU29ydCxcbiAgICAgIHNvcnRGbixcbiAgICAgIGlnbm9yZUZpZWxkTm9ybVxuICAgIH0gPSB0aGlzLm9wdGlvbnM7XG5cbiAgICBsZXQgcmVzdWx0cyA9IGlzU3RyaW5nKHF1ZXJ5KVxuICAgICAgPyBpc1N0cmluZyh0aGlzLl9kb2NzWzBdKVxuICAgICAgICA/IHRoaXMuX3NlYXJjaFN0cmluZ0xpc3QocXVlcnkpXG4gICAgICAgIDogdGhpcy5fc2VhcmNoT2JqZWN0TGlzdChxdWVyeSlcbiAgICAgIDogdGhpcy5fc2VhcmNoTG9naWNhbChxdWVyeSk7XG5cbiAgICBjb21wdXRlU2NvcmUocmVzdWx0cywgeyBpZ25vcmVGaWVsZE5vcm0gfSk7XG5cbiAgICBpZiAoc2hvdWxkU29ydCkge1xuICAgICAgcmVzdWx0cy5zb3J0KHNvcnRGbik7XG4gICAgfVxuXG4gICAgaWYgKGlzTnVtYmVyKGxpbWl0KSAmJiBsaW1pdCA+IC0xKSB7XG4gICAgICByZXN1bHRzID0gcmVzdWx0cy5zbGljZSgwLCBsaW1pdCk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGZvcm1hdChyZXN1bHRzLCB0aGlzLl9kb2NzLCB7XG4gICAgICBpbmNsdWRlTWF0Y2hlcyxcbiAgICAgIGluY2x1ZGVTY29yZVxuICAgIH0pXG4gIH1cblxuICBfc2VhcmNoU3RyaW5nTGlzdChxdWVyeSkge1xuICAgIGNvbnN0IHNlYXJjaGVyID0gY3JlYXRlU2VhcmNoZXIocXVlcnksIHRoaXMub3B0aW9ucyk7XG4gICAgY29uc3QgeyByZWNvcmRzIH0gPSB0aGlzLl9teUluZGV4O1xuICAgIGNvbnN0IHJlc3VsdHMgPSBbXTtcblxuICAgIC8vIEl0ZXJhdGUgb3ZlciBldmVyeSBzdHJpbmcgaW4gdGhlIGluZGV4XG4gICAgcmVjb3Jkcy5mb3JFYWNoKCh7IHY6IHRleHQsIGk6IGlkeCwgbjogbm9ybSB9KSA9PiB7XG4gICAgICBpZiAoIWlzRGVmaW5lZCh0ZXh0KSkge1xuICAgICAgICByZXR1cm5cbiAgICAgIH1cblxuICAgICAgY29uc3QgeyBpc01hdGNoLCBzY29yZSwgaW5kaWNlcyB9ID0gc2VhcmNoZXIuc2VhcmNoSW4odGV4dCk7XG5cbiAgICAgIGlmIChpc01hdGNoKSB7XG4gICAgICAgIHJlc3VsdHMucHVzaCh7XG4gICAgICAgICAgaXRlbTogdGV4dCxcbiAgICAgICAgICBpZHgsXG4gICAgICAgICAgbWF0Y2hlczogW3sgc2NvcmUsIHZhbHVlOiB0ZXh0LCBub3JtLCBpbmRpY2VzIH1dXG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgcmV0dXJuIHJlc3VsdHNcbiAgfVxuXG4gIF9zZWFyY2hMb2dpY2FsKHF1ZXJ5KSB7XG5cbiAgICBjb25zdCBleHByZXNzaW9uID0gcGFyc2UocXVlcnksIHRoaXMub3B0aW9ucyk7XG5cbiAgICBjb25zdCBldmFsdWF0ZSA9IChub2RlLCBpdGVtLCBpZHgpID0+IHtcbiAgICAgIGlmICghbm9kZS5jaGlsZHJlbikge1xuICAgICAgICBjb25zdCB7IGtleUlkLCBzZWFyY2hlciB9ID0gbm9kZTtcblxuICAgICAgICBjb25zdCBtYXRjaGVzID0gdGhpcy5fZmluZE1hdGNoZXMoe1xuICAgICAgICAgIGtleTogdGhpcy5fa2V5U3RvcmUuZ2V0KGtleUlkKSxcbiAgICAgICAgICB2YWx1ZTogdGhpcy5fbXlJbmRleC5nZXRWYWx1ZUZvckl0ZW1BdEtleUlkKGl0ZW0sIGtleUlkKSxcbiAgICAgICAgICBzZWFyY2hlclxuICAgICAgICB9KTtcblxuICAgICAgICBpZiAobWF0Y2hlcyAmJiBtYXRjaGVzLmxlbmd0aCkge1xuICAgICAgICAgIHJldHVybiBbXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgIGlkeCxcbiAgICAgICAgICAgICAgaXRlbSxcbiAgICAgICAgICAgICAgbWF0Y2hlc1xuICAgICAgICAgICAgfVxuICAgICAgICAgIF1cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBbXVxuICAgICAgfVxuXG4gICAgICBjb25zdCByZXMgPSBbXTtcbiAgICAgIGZvciAobGV0IGkgPSAwLCBsZW4gPSBub2RlLmNoaWxkcmVuLmxlbmd0aDsgaSA8IGxlbjsgaSArPSAxKSB7XG4gICAgICAgIGNvbnN0IGNoaWxkID0gbm9kZS5jaGlsZHJlbltpXTtcbiAgICAgICAgY29uc3QgcmVzdWx0ID0gZXZhbHVhdGUoY2hpbGQsIGl0ZW0sIGlkeCk7XG4gICAgICAgIGlmIChyZXN1bHQubGVuZ3RoKSB7XG4gICAgICAgICAgcmVzLnB1c2goLi4ucmVzdWx0KTtcbiAgICAgICAgfSBlbHNlIGlmIChub2RlLm9wZXJhdG9yID09PSBMb2dpY2FsT3BlcmF0b3IuQU5EKSB7XG4gICAgICAgICAgcmV0dXJuIFtdXG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiByZXNcbiAgICB9O1xuXG4gICAgY29uc3QgcmVjb3JkcyA9IHRoaXMuX215SW5kZXgucmVjb3JkcztcbiAgICBjb25zdCByZXN1bHRNYXAgPSB7fTtcbiAgICBjb25zdCByZXN1bHRzID0gW107XG5cbiAgICByZWNvcmRzLmZvckVhY2goKHsgJDogaXRlbSwgaTogaWR4IH0pID0+IHtcbiAgICAgIGlmIChpc0RlZmluZWQoaXRlbSkpIHtcbiAgICAgICAgbGV0IGV4cFJlc3VsdHMgPSBldmFsdWF0ZShleHByZXNzaW9uLCBpdGVtLCBpZHgpO1xuXG4gICAgICAgIGlmIChleHBSZXN1bHRzLmxlbmd0aCkge1xuICAgICAgICAgIC8vIERlZHVwZSB3aGVuIGFkZGluZ1xuICAgICAgICAgIGlmICghcmVzdWx0TWFwW2lkeF0pIHtcbiAgICAgICAgICAgIHJlc3VsdE1hcFtpZHhdID0geyBpZHgsIGl0ZW0sIG1hdGNoZXM6IFtdIH07XG4gICAgICAgICAgICByZXN1bHRzLnB1c2gocmVzdWx0TWFwW2lkeF0pO1xuICAgICAgICAgIH1cbiAgICAgICAgICBleHBSZXN1bHRzLmZvckVhY2goKHsgbWF0Y2hlcyB9KSA9PiB7XG4gICAgICAgICAgICByZXN1bHRNYXBbaWR4XS5tYXRjaGVzLnB1c2goLi4ubWF0Y2hlcyk7XG4gICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiByZXN1bHRzXG4gIH1cblxuICBfc2VhcmNoT2JqZWN0TGlzdChxdWVyeSkge1xuICAgIGNvbnN0IHNlYXJjaGVyID0gY3JlYXRlU2VhcmNoZXIocXVlcnksIHRoaXMub3B0aW9ucyk7XG4gICAgY29uc3QgeyBrZXlzLCByZWNvcmRzIH0gPSB0aGlzLl9teUluZGV4O1xuICAgIGNvbnN0IHJlc3VsdHMgPSBbXTtcblxuICAgIC8vIExpc3QgaXMgQXJyYXk8T2JqZWN0PlxuICAgIHJlY29yZHMuZm9yRWFjaCgoeyAkOiBpdGVtLCBpOiBpZHggfSkgPT4ge1xuICAgICAgaWYgKCFpc0RlZmluZWQoaXRlbSkpIHtcbiAgICAgICAgcmV0dXJuXG4gICAgICB9XG5cbiAgICAgIGxldCBtYXRjaGVzID0gW107XG5cbiAgICAgIC8vIEl0ZXJhdGUgb3ZlciBldmVyeSBrZXkgKGkuZSwgcGF0aCksIGFuZCBmZXRjaCB0aGUgdmFsdWUgYXQgdGhhdCBrZXlcbiAgICAgIGtleXMuZm9yRWFjaCgoa2V5LCBrZXlJbmRleCkgPT4ge1xuICAgICAgICBtYXRjaGVzLnB1c2goXG4gICAgICAgICAgLi4udGhpcy5fZmluZE1hdGNoZXMoe1xuICAgICAgICAgICAga2V5LFxuICAgICAgICAgICAgdmFsdWU6IGl0ZW1ba2V5SW5kZXhdLFxuICAgICAgICAgICAgc2VhcmNoZXJcbiAgICAgICAgICB9KVxuICAgICAgICApO1xuICAgICAgfSk7XG5cbiAgICAgIGlmIChtYXRjaGVzLmxlbmd0aCkge1xuICAgICAgICByZXN1bHRzLnB1c2goe1xuICAgICAgICAgIGlkeCxcbiAgICAgICAgICBpdGVtLFxuICAgICAgICAgIG1hdGNoZXNcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICByZXR1cm4gcmVzdWx0c1xuICB9XG4gIF9maW5kTWF0Y2hlcyh7IGtleSwgdmFsdWUsIHNlYXJjaGVyIH0pIHtcbiAgICBpZiAoIWlzRGVmaW5lZCh2YWx1ZSkpIHtcbiAgICAgIHJldHVybiBbXVxuICAgIH1cblxuICAgIGxldCBtYXRjaGVzID0gW107XG5cbiAgICBpZiAoaXNBcnJheSh2YWx1ZSkpIHtcbiAgICAgIHZhbHVlLmZvckVhY2goKHsgdjogdGV4dCwgaTogaWR4LCBuOiBub3JtIH0pID0+IHtcbiAgICAgICAgaWYgKCFpc0RlZmluZWQodGV4dCkpIHtcbiAgICAgICAgICByZXR1cm5cbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHsgaXNNYXRjaCwgc2NvcmUsIGluZGljZXMgfSA9IHNlYXJjaGVyLnNlYXJjaEluKHRleHQpO1xuXG4gICAgICAgIGlmIChpc01hdGNoKSB7XG4gICAgICAgICAgbWF0Y2hlcy5wdXNoKHtcbiAgICAgICAgICAgIHNjb3JlLFxuICAgICAgICAgICAga2V5LFxuICAgICAgICAgICAgdmFsdWU6IHRleHQsXG4gICAgICAgICAgICBpZHgsXG4gICAgICAgICAgICBub3JtLFxuICAgICAgICAgICAgaW5kaWNlc1xuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9IGVsc2Uge1xuICAgICAgY29uc3QgeyB2OiB0ZXh0LCBuOiBub3JtIH0gPSB2YWx1ZTtcblxuICAgICAgY29uc3QgeyBpc01hdGNoLCBzY29yZSwgaW5kaWNlcyB9ID0gc2VhcmNoZXIuc2VhcmNoSW4odGV4dCk7XG5cbiAgICAgIGlmIChpc01hdGNoKSB7XG4gICAgICAgIG1hdGNoZXMucHVzaCh7IHNjb3JlLCBrZXksIHZhbHVlOiB0ZXh0LCBub3JtLCBpbmRpY2VzIH0pO1xuICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBtYXRjaGVzXG4gIH1cbn1cblxuRnVzZS52ZXJzaW9uID0gJzYuNi4yJztcbkZ1c2UuY3JlYXRlSW5kZXggPSBjcmVhdGVJbmRleDtcbkZ1c2UucGFyc2VJbmRleCA9IHBhcnNlSW5kZXg7XG5GdXNlLmNvbmZpZyA9IENvbmZpZztcblxue1xuICBGdXNlLnBhcnNlUXVlcnkgPSBwYXJzZTtcbn1cblxue1xuICByZWdpc3RlcihFeHRlbmRlZFNlYXJjaCk7XG59XG5cblxuXG5cbi8qKiovIH0pLFxuXG4vKioqLyA3OTE6XG4vKioqLyAoZnVuY3Rpb24oX191bnVzZWRfd2VicGFja19tb2R1bGUsIF9fd2VicGFja19leHBvcnRzX18sIF9fd2VicGFja19yZXF1aXJlX18pIHtcblxuLy8gRVNNIENPTVBBVCBGTEFHXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIoX193ZWJwYWNrX2V4cG9ydHNfXyk7XG5cbi8vIEVYUE9SVFNcbl9fd2VicGFja19yZXF1aXJlX18uZChfX3dlYnBhY2tfZXhwb3J0c19fLCB7XG4gIFwiX19ET19OT1RfVVNFX19BY3Rpb25UeXBlc1wiOiBmdW5jdGlvbigpIHsgcmV0dXJuIC8qIGJpbmRpbmcgKi8gQWN0aW9uVHlwZXM7IH0sXG4gIFwiYXBwbHlNaWRkbGV3YXJlXCI6IGZ1bmN0aW9uKCkgeyByZXR1cm4gLyogYmluZGluZyAqLyBhcHBseU1pZGRsZXdhcmU7IH0sXG4gIFwiYmluZEFjdGlvbkNyZWF0b3JzXCI6IGZ1bmN0aW9uKCkgeyByZXR1cm4gLyogYmluZGluZyAqLyBiaW5kQWN0aW9uQ3JlYXRvcnM7IH0sXG4gIFwiY29tYmluZVJlZHVjZXJzXCI6IGZ1bmN0aW9uKCkgeyByZXR1cm4gLyogYmluZGluZyAqLyBjb21iaW5lUmVkdWNlcnM7IH0sXG4gIFwiY29tcG9zZVwiOiBmdW5jdGlvbigpIHsgcmV0dXJuIC8qIGJpbmRpbmcgKi8gY29tcG9zZTsgfSxcbiAgXCJjcmVhdGVTdG9yZVwiOiBmdW5jdGlvbigpIHsgcmV0dXJuIC8qIGJpbmRpbmcgKi8gY3JlYXRlU3RvcmU7IH0sXG4gIFwibGVnYWN5X2NyZWF0ZVN0b3JlXCI6IGZ1bmN0aW9uKCkgeyByZXR1cm4gLyogYmluZGluZyAqLyBsZWdhY3lfY3JlYXRlU3RvcmU7IH1cbn0pO1xuXG47Ly8gQ09OQ0FURU5BVEVEIE1PRFVMRTogLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vdHlwZW9mLmpzXG5mdW5jdGlvbiBfdHlwZW9mKG9iaikge1xuICBcIkBiYWJlbC9oZWxwZXJzIC0gdHlwZW9mXCI7XG5cbiAgcmV0dXJuIF90eXBlb2YgPSBcImZ1bmN0aW9uXCIgPT0gdHlwZW9mIFN5bWJvbCAmJiBcInN5bWJvbFwiID09IHR5cGVvZiBTeW1ib2wuaXRlcmF0b3IgPyBmdW5jdGlvbiAob2JqKSB7XG4gICAgcmV0dXJuIHR5cGVvZiBvYmo7XG4gIH0gOiBmdW5jdGlvbiAob2JqKSB7XG4gICAgcmV0dXJuIG9iaiAmJiBcImZ1bmN0aW9uXCIgPT0gdHlwZW9mIFN5bWJvbCAmJiBvYmouY29uc3RydWN0b3IgPT09IFN5bWJvbCAmJiBvYmogIT09IFN5bWJvbC5wcm90b3R5cGUgPyBcInN5bWJvbFwiIDogdHlwZW9mIG9iajtcbiAgfSwgX3R5cGVvZihvYmopO1xufVxuOy8vIENPTkNBVEVOQVRFRCBNT0RVTEU6IC4vbm9kZV9tb2R1bGVzL0BiYWJlbC9ydW50aW1lL2hlbHBlcnMvZXNtL3RvUHJpbWl0aXZlLmpzXG5cbmZ1bmN0aW9uIF90b1ByaW1pdGl2ZShpbnB1dCwgaGludCkge1xuICBpZiAoX3R5cGVvZihpbnB1dCkgIT09IFwib2JqZWN0XCIgfHwgaW5wdXQgPT09IG51bGwpIHJldHVybiBpbnB1dDtcbiAgdmFyIHByaW0gPSBpbnB1dFtTeW1ib2wudG9QcmltaXRpdmVdO1xuICBpZiAocHJpbSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgdmFyIHJlcyA9IHByaW0uY2FsbChpbnB1dCwgaGludCB8fCBcImRlZmF1bHRcIik7XG4gICAgaWYgKF90eXBlb2YocmVzKSAhPT0gXCJvYmplY3RcIikgcmV0dXJuIHJlcztcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiQEB0b1ByaW1pdGl2ZSBtdXN0IHJldHVybiBhIHByaW1pdGl2ZSB2YWx1ZS5cIik7XG4gIH1cbiAgcmV0dXJuIChoaW50ID09PSBcInN0cmluZ1wiID8gU3RyaW5nIDogTnVtYmVyKShpbnB1dCk7XG59XG47Ly8gQ09OQ0FURU5BVEVEIE1PRFVMRTogLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vdG9Qcm9wZXJ0eUtleS5qc1xuXG5cbmZ1bmN0aW9uIF90b1Byb3BlcnR5S2V5KGFyZykge1xuICB2YXIga2V5ID0gX3RvUHJpbWl0aXZlKGFyZywgXCJzdHJpbmdcIik7XG4gIHJldHVybiBfdHlwZW9mKGtleSkgPT09IFwic3ltYm9sXCIgPyBrZXkgOiBTdHJpbmcoa2V5KTtcbn1cbjsvLyBDT05DQVRFTkFURUQgTU9EVUxFOiAuL25vZGVfbW9kdWxlcy9AYmFiZWwvcnVudGltZS9oZWxwZXJzL2VzbS9kZWZpbmVQcm9wZXJ0eS5qc1xuXG5mdW5jdGlvbiBfZGVmaW5lUHJvcGVydHkob2JqLCBrZXksIHZhbHVlKSB7XG4gIGtleSA9IF90b1Byb3BlcnR5S2V5KGtleSk7XG4gIGlmIChrZXkgaW4gb2JqKSB7XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KG9iaiwga2V5LCB7XG4gICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICBlbnVtZXJhYmxlOiB0cnVlLFxuICAgICAgY29uZmlndXJhYmxlOiB0cnVlLFxuICAgICAgd3JpdGFibGU6IHRydWVcbiAgICB9KTtcbiAgfSBlbHNlIHtcbiAgICBvYmpba2V5XSA9IHZhbHVlO1xuICB9XG4gIHJldHVybiBvYmo7XG59XG47Ly8gQ09OQ0FURU5BVEVEIE1PRFVMRTogLi9ub2RlX21vZHVsZXMvQGJhYmVsL3J1bnRpbWUvaGVscGVycy9lc20vb2JqZWN0U3ByZWFkMi5qc1xuXG5mdW5jdGlvbiBvd25LZXlzKG9iamVjdCwgZW51bWVyYWJsZU9ubHkpIHtcbiAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhvYmplY3QpO1xuICBpZiAoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scykge1xuICAgIHZhciBzeW1ib2xzID0gT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scyhvYmplY3QpO1xuICAgIGVudW1lcmFibGVPbmx5ICYmIChzeW1ib2xzID0gc3ltYm9scy5maWx0ZXIoZnVuY3Rpb24gKHN5bSkge1xuICAgICAgcmV0dXJuIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3Iob2JqZWN0LCBzeW0pLmVudW1lcmFibGU7XG4gICAgfSkpLCBrZXlzLnB1c2guYXBwbHkoa2V5cywgc3ltYm9scyk7XG4gIH1cbiAgcmV0dXJuIGtleXM7XG59XG5mdW5jdGlvbiBfb2JqZWN0U3ByZWFkMih0YXJnZXQpIHtcbiAgZm9yICh2YXIgaSA9IDE7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIHtcbiAgICB2YXIgc291cmNlID0gbnVsbCAhPSBhcmd1bWVudHNbaV0gPyBhcmd1bWVudHNbaV0gOiB7fTtcbiAgICBpICUgMiA/IG93bktleXMoT2JqZWN0KHNvdXJjZSksICEwKS5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgIF9kZWZpbmVQcm9wZXJ0eSh0YXJnZXQsIGtleSwgc291cmNlW2tleV0pO1xuICAgIH0pIDogT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcnMgPyBPYmplY3QuZGVmaW5lUHJvcGVydGllcyh0YXJnZXQsIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3JzKHNvdXJjZSkpIDogb3duS2V5cyhPYmplY3Qoc291cmNlKSkuZm9yRWFjaChmdW5jdGlvbiAoa2V5KSB7XG4gICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkodGFyZ2V0LCBrZXksIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3Ioc291cmNlLCBrZXkpKTtcbiAgICB9KTtcbiAgfVxuICByZXR1cm4gdGFyZ2V0O1xufVxuOy8vIENPTkNBVEVOQVRFRCBNT0RVTEU6IC4vbm9kZV9tb2R1bGVzL3JlZHV4L2VzL3JlZHV4LmpzXG5cblxuLyoqXG4gKiBBZGFwdGVkIGZyb20gUmVhY3Q6IGh0dHBzOi8vZ2l0aHViLmNvbS9mYWNlYm9vay9yZWFjdC9ibG9iL21hc3Rlci9wYWNrYWdlcy9zaGFyZWQvZm9ybWF0UHJvZEVycm9yTWVzc2FnZS5qc1xuICpcbiAqIERvIG5vdCByZXF1aXJlIHRoaXMgbW9kdWxlIGRpcmVjdGx5ISBVc2Ugbm9ybWFsIHRocm93IGVycm9yIGNhbGxzLiBUaGVzZSBtZXNzYWdlcyB3aWxsIGJlIHJlcGxhY2VkIHdpdGggZXJyb3IgY29kZXNcbiAqIGR1cmluZyBidWlsZC5cbiAqIEBwYXJhbSB7bnVtYmVyfSBjb2RlXG4gKi9cbmZ1bmN0aW9uIGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoY29kZSkge1xuICByZXR1cm4gXCJNaW5pZmllZCBSZWR1eCBlcnJvciAjXCIgKyBjb2RlICsgXCI7IHZpc2l0IGh0dHBzOi8vcmVkdXguanMub3JnL0Vycm9ycz9jb2RlPVwiICsgY29kZSArIFwiIGZvciB0aGUgZnVsbCBtZXNzYWdlIG9yIFwiICsgJ3VzZSB0aGUgbm9uLW1pbmlmaWVkIGRldiBlbnZpcm9ubWVudCBmb3IgZnVsbCBlcnJvcnMuICc7XG59XG5cbi8vIElubGluZWQgdmVyc2lvbiBvZiB0aGUgYHN5bWJvbC1vYnNlcnZhYmxlYCBwb2x5ZmlsbFxudmFyICQkb2JzZXJ2YWJsZSA9IChmdW5jdGlvbiAoKSB7XG4gIHJldHVybiB0eXBlb2YgU3ltYm9sID09PSAnZnVuY3Rpb24nICYmIFN5bWJvbC5vYnNlcnZhYmxlIHx8ICdAQG9ic2VydmFibGUnO1xufSkoKTtcblxuLyoqXG4gKiBUaGVzZSBhcmUgcHJpdmF0ZSBhY3Rpb24gdHlwZXMgcmVzZXJ2ZWQgYnkgUmVkdXguXG4gKiBGb3IgYW55IHVua25vd24gYWN0aW9ucywgeW91IG11c3QgcmV0dXJuIHRoZSBjdXJyZW50IHN0YXRlLlxuICogSWYgdGhlIGN1cnJlbnQgc3RhdGUgaXMgdW5kZWZpbmVkLCB5b3UgbXVzdCByZXR1cm4gdGhlIGluaXRpYWwgc3RhdGUuXG4gKiBEbyBub3QgcmVmZXJlbmNlIHRoZXNlIGFjdGlvbiB0eXBlcyBkaXJlY3RseSBpbiB5b3VyIGNvZGUuXG4gKi9cbnZhciByYW5kb21TdHJpbmcgPSBmdW5jdGlvbiByYW5kb21TdHJpbmcoKSB7XG4gIHJldHVybiBNYXRoLnJhbmRvbSgpLnRvU3RyaW5nKDM2KS5zdWJzdHJpbmcoNykuc3BsaXQoJycpLmpvaW4oJy4nKTtcbn07XG5cbnZhciBBY3Rpb25UeXBlcyA9IHtcbiAgSU5JVDogXCJAQHJlZHV4L0lOSVRcIiArIHJhbmRvbVN0cmluZygpLFxuICBSRVBMQUNFOiBcIkBAcmVkdXgvUkVQTEFDRVwiICsgcmFuZG9tU3RyaW5nKCksXG4gIFBST0JFX1VOS05PV05fQUNUSU9OOiBmdW5jdGlvbiBQUk9CRV9VTktOT1dOX0FDVElPTigpIHtcbiAgICByZXR1cm4gXCJAQHJlZHV4L1BST0JFX1VOS05PV05fQUNUSU9OXCIgKyByYW5kb21TdHJpbmcoKTtcbiAgfVxufTtcblxuLyoqXG4gKiBAcGFyYW0ge2FueX0gb2JqIFRoZSBvYmplY3QgdG8gaW5zcGVjdC5cbiAqIEByZXR1cm5zIHtib29sZWFufSBUcnVlIGlmIHRoZSBhcmd1bWVudCBhcHBlYXJzIHRvIGJlIGEgcGxhaW4gb2JqZWN0LlxuICovXG5mdW5jdGlvbiBpc1BsYWluT2JqZWN0KG9iaikge1xuICBpZiAodHlwZW9mIG9iaiAhPT0gJ29iamVjdCcgfHwgb2JqID09PSBudWxsKSByZXR1cm4gZmFsc2U7XG4gIHZhciBwcm90byA9IG9iajtcblxuICB3aGlsZSAoT2JqZWN0LmdldFByb3RvdHlwZU9mKHByb3RvKSAhPT0gbnVsbCkge1xuICAgIHByb3RvID0gT2JqZWN0LmdldFByb3RvdHlwZU9mKHByb3RvKTtcbiAgfVxuXG4gIHJldHVybiBPYmplY3QuZ2V0UHJvdG90eXBlT2Yob2JqKSA9PT0gcHJvdG87XG59XG5cbi8vIElubGluZWQgLyBzaG9ydGVuZWQgdmVyc2lvbiBvZiBga2luZE9mYCBmcm9tIGh0dHBzOi8vZ2l0aHViLmNvbS9qb25zY2hsaW5rZXJ0L2tpbmQtb2ZcbmZ1bmN0aW9uIG1pbmlLaW5kT2YodmFsKSB7XG4gIGlmICh2YWwgPT09IHZvaWQgMCkgcmV0dXJuICd1bmRlZmluZWQnO1xuICBpZiAodmFsID09PSBudWxsKSByZXR1cm4gJ251bGwnO1xuICB2YXIgdHlwZSA9IHR5cGVvZiB2YWw7XG5cbiAgc3dpdGNoICh0eXBlKSB7XG4gICAgY2FzZSAnYm9vbGVhbic6XG4gICAgY2FzZSAnc3RyaW5nJzpcbiAgICBjYXNlICdudW1iZXInOlxuICAgIGNhc2UgJ3N5bWJvbCc6XG4gICAgY2FzZSAnZnVuY3Rpb24nOlxuICAgICAge1xuICAgICAgICByZXR1cm4gdHlwZTtcbiAgICAgIH1cbiAgfVxuXG4gIGlmIChBcnJheS5pc0FycmF5KHZhbCkpIHJldHVybiAnYXJyYXknO1xuICBpZiAoaXNEYXRlKHZhbCkpIHJldHVybiAnZGF0ZSc7XG4gIGlmIChpc0Vycm9yKHZhbCkpIHJldHVybiAnZXJyb3InO1xuICB2YXIgY29uc3RydWN0b3JOYW1lID0gY3Rvck5hbWUodmFsKTtcblxuICBzd2l0Y2ggKGNvbnN0cnVjdG9yTmFtZSkge1xuICAgIGNhc2UgJ1N5bWJvbCc6XG4gICAgY2FzZSAnUHJvbWlzZSc6XG4gICAgY2FzZSAnV2Vha01hcCc6XG4gICAgY2FzZSAnV2Vha1NldCc6XG4gICAgY2FzZSAnTWFwJzpcbiAgICBjYXNlICdTZXQnOlxuICAgICAgcmV0dXJuIGNvbnN0cnVjdG9yTmFtZTtcbiAgfSAvLyBvdGhlclxuXG5cbiAgcmV0dXJuIHR5cGUuc2xpY2UoOCwgLTEpLnRvTG93ZXJDYXNlKCkucmVwbGFjZSgvXFxzL2csICcnKTtcbn1cblxuZnVuY3Rpb24gY3Rvck5hbWUodmFsKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsLmNvbnN0cnVjdG9yID09PSAnZnVuY3Rpb24nID8gdmFsLmNvbnN0cnVjdG9yLm5hbWUgOiBudWxsO1xufVxuXG5mdW5jdGlvbiBpc0Vycm9yKHZhbCkge1xuICByZXR1cm4gdmFsIGluc3RhbmNlb2YgRXJyb3IgfHwgdHlwZW9mIHZhbC5tZXNzYWdlID09PSAnc3RyaW5nJyAmJiB2YWwuY29uc3RydWN0b3IgJiYgdHlwZW9mIHZhbC5jb25zdHJ1Y3Rvci5zdGFja1RyYWNlTGltaXQgPT09ICdudW1iZXInO1xufVxuXG5mdW5jdGlvbiBpc0RhdGUodmFsKSB7XG4gIGlmICh2YWwgaW5zdGFuY2VvZiBEYXRlKSByZXR1cm4gdHJ1ZTtcbiAgcmV0dXJuIHR5cGVvZiB2YWwudG9EYXRlU3RyaW5nID09PSAnZnVuY3Rpb24nICYmIHR5cGVvZiB2YWwuZ2V0RGF0ZSA9PT0gJ2Z1bmN0aW9uJyAmJiB0eXBlb2YgdmFsLnNldERhdGUgPT09ICdmdW5jdGlvbic7XG59XG5cbmZ1bmN0aW9uIGtpbmRPZih2YWwpIHtcbiAgdmFyIHR5cGVPZlZhbCA9IHR5cGVvZiB2YWw7XG5cbiAgaWYgKGZhbHNlKSB7fVxuXG4gIHJldHVybiB0eXBlT2ZWYWw7XG59XG5cbi8qKlxuICogQGRlcHJlY2F0ZWRcbiAqXG4gKiAqKldlIHJlY29tbWVuZCB1c2luZyB0aGUgYGNvbmZpZ3VyZVN0b3JlYCBtZXRob2RcbiAqIG9mIHRoZSBgQHJlZHV4anMvdG9vbGtpdGAgcGFja2FnZSoqLCB3aGljaCByZXBsYWNlcyBgY3JlYXRlU3RvcmVgLlxuICpcbiAqIFJlZHV4IFRvb2xraXQgaXMgb3VyIHJlY29tbWVuZGVkIGFwcHJvYWNoIGZvciB3cml0aW5nIFJlZHV4IGxvZ2ljIHRvZGF5LFxuICogaW5jbHVkaW5nIHN0b3JlIHNldHVwLCByZWR1Y2VycywgZGF0YSBmZXRjaGluZywgYW5kIG1vcmUuXG4gKlxuICogKipGb3IgbW9yZSBkZXRhaWxzLCBwbGVhc2UgcmVhZCB0aGlzIFJlZHV4IGRvY3MgcGFnZToqKlxuICogKipodHRwczovL3JlZHV4LmpzLm9yZy9pbnRyb2R1Y3Rpb24vd2h5LXJ0ay1pcy1yZWR1eC10b2RheSoqXG4gKlxuICogYGNvbmZpZ3VyZVN0b3JlYCBmcm9tIFJlZHV4IFRvb2xraXQgaXMgYW4gaW1wcm92ZWQgdmVyc2lvbiBvZiBgY3JlYXRlU3RvcmVgIHRoYXRcbiAqIHNpbXBsaWZpZXMgc2V0dXAgYW5kIGhlbHBzIGF2b2lkIGNvbW1vbiBidWdzLlxuICpcbiAqIFlvdSBzaG91bGQgbm90IGJlIHVzaW5nIHRoZSBgcmVkdXhgIGNvcmUgcGFja2FnZSBieSBpdHNlbGYgdG9kYXksIGV4Y2VwdCBmb3IgbGVhcm5pbmcgcHVycG9zZXMuXG4gKiBUaGUgYGNyZWF0ZVN0b3JlYCBtZXRob2QgZnJvbSB0aGUgY29yZSBgcmVkdXhgIHBhY2thZ2Ugd2lsbCBub3QgYmUgcmVtb3ZlZCwgYnV0IHdlIGVuY291cmFnZVxuICogYWxsIHVzZXJzIHRvIG1pZ3JhdGUgdG8gdXNpbmcgUmVkdXggVG9vbGtpdCBmb3IgYWxsIFJlZHV4IGNvZGUuXG4gKlxuICogSWYgeW91IHdhbnQgdG8gdXNlIGBjcmVhdGVTdG9yZWAgd2l0aG91dCB0aGlzIHZpc3VhbCBkZXByZWNhdGlvbiB3YXJuaW5nLCB1c2VcbiAqIHRoZSBgbGVnYWN5X2NyZWF0ZVN0b3JlYCBpbXBvcnQgaW5zdGVhZDpcbiAqXG4gKiBgaW1wb3J0IHsgbGVnYWN5X2NyZWF0ZVN0b3JlIGFzIGNyZWF0ZVN0b3JlfSBmcm9tICdyZWR1eCdgXG4gKlxuICovXG5cbmZ1bmN0aW9uIGNyZWF0ZVN0b3JlKHJlZHVjZXIsIHByZWxvYWRlZFN0YXRlLCBlbmhhbmNlcikge1xuICB2YXIgX3JlZjI7XG5cbiAgaWYgKHR5cGVvZiBwcmVsb2FkZWRTdGF0ZSA9PT0gJ2Z1bmN0aW9uJyAmJiB0eXBlb2YgZW5oYW5jZXIgPT09ICdmdW5jdGlvbicgfHwgdHlwZW9mIGVuaGFuY2VyID09PSAnZnVuY3Rpb24nICYmIHR5cGVvZiBhcmd1bWVudHNbM10gPT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgRXJyb3IoIHRydWUgPyBmb3JtYXRQcm9kRXJyb3JNZXNzYWdlKDApIDogMCk7XG4gIH1cblxuICBpZiAodHlwZW9mIHByZWxvYWRlZFN0YXRlID09PSAnZnVuY3Rpb24nICYmIHR5cGVvZiBlbmhhbmNlciA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICBlbmhhbmNlciA9IHByZWxvYWRlZFN0YXRlO1xuICAgIHByZWxvYWRlZFN0YXRlID0gdW5kZWZpbmVkO1xuICB9XG5cbiAgaWYgKHR5cGVvZiBlbmhhbmNlciAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICBpZiAodHlwZW9mIGVuaGFuY2VyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoIHRydWUgPyBmb3JtYXRQcm9kRXJyb3JNZXNzYWdlKDEpIDogMCk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGVuaGFuY2VyKGNyZWF0ZVN0b3JlKShyZWR1Y2VyLCBwcmVsb2FkZWRTdGF0ZSk7XG4gIH1cblxuICBpZiAodHlwZW9mIHJlZHVjZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgRXJyb3IoIHRydWUgPyBmb3JtYXRQcm9kRXJyb3JNZXNzYWdlKDIpIDogMCk7XG4gIH1cblxuICB2YXIgY3VycmVudFJlZHVjZXIgPSByZWR1Y2VyO1xuICB2YXIgY3VycmVudFN0YXRlID0gcHJlbG9hZGVkU3RhdGU7XG4gIHZhciBjdXJyZW50TGlzdGVuZXJzID0gW107XG4gIHZhciBuZXh0TGlzdGVuZXJzID0gY3VycmVudExpc3RlbmVycztcbiAgdmFyIGlzRGlzcGF0Y2hpbmcgPSBmYWxzZTtcbiAgLyoqXG4gICAqIFRoaXMgbWFrZXMgYSBzaGFsbG93IGNvcHkgb2YgY3VycmVudExpc3RlbmVycyBzbyB3ZSBjYW4gdXNlXG4gICAqIG5leHRMaXN0ZW5lcnMgYXMgYSB0ZW1wb3JhcnkgbGlzdCB3aGlsZSBkaXNwYXRjaGluZy5cbiAgICpcbiAgICogVGhpcyBwcmV2ZW50cyBhbnkgYnVncyBhcm91bmQgY29uc3VtZXJzIGNhbGxpbmdcbiAgICogc3Vic2NyaWJlL3Vuc3Vic2NyaWJlIGluIHRoZSBtaWRkbGUgb2YgYSBkaXNwYXRjaC5cbiAgICovXG5cbiAgZnVuY3Rpb24gZW5zdXJlQ2FuTXV0YXRlTmV4dExpc3RlbmVycygpIHtcbiAgICBpZiAobmV4dExpc3RlbmVycyA9PT0gY3VycmVudExpc3RlbmVycykge1xuICAgICAgbmV4dExpc3RlbmVycyA9IGN1cnJlbnRMaXN0ZW5lcnMuc2xpY2UoKTtcbiAgICB9XG4gIH1cbiAgLyoqXG4gICAqIFJlYWRzIHRoZSBzdGF0ZSB0cmVlIG1hbmFnZWQgYnkgdGhlIHN0b3JlLlxuICAgKlxuICAgKiBAcmV0dXJucyB7YW55fSBUaGUgY3VycmVudCBzdGF0ZSB0cmVlIG9mIHlvdXIgYXBwbGljYXRpb24uXG4gICAqL1xuXG5cbiAgZnVuY3Rpb24gZ2V0U3RhdGUoKSB7XG4gICAgaWYgKGlzRGlzcGF0Y2hpbmcpIHtcbiAgICAgIHRocm93IG5ldyBFcnJvciggdHJ1ZSA/IGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoMykgOiAwKTtcbiAgICB9XG5cbiAgICByZXR1cm4gY3VycmVudFN0YXRlO1xuICB9XG4gIC8qKlxuICAgKiBBZGRzIGEgY2hhbmdlIGxpc3RlbmVyLiBJdCB3aWxsIGJlIGNhbGxlZCBhbnkgdGltZSBhbiBhY3Rpb24gaXMgZGlzcGF0Y2hlZCxcbiAgICogYW5kIHNvbWUgcGFydCBvZiB0aGUgc3RhdGUgdHJlZSBtYXkgcG90ZW50aWFsbHkgaGF2ZSBjaGFuZ2VkLiBZb3UgbWF5IHRoZW5cbiAgICogY2FsbCBgZ2V0U3RhdGUoKWAgdG8gcmVhZCB0aGUgY3VycmVudCBzdGF0ZSB0cmVlIGluc2lkZSB0aGUgY2FsbGJhY2suXG4gICAqXG4gICAqIFlvdSBtYXkgY2FsbCBgZGlzcGF0Y2goKWAgZnJvbSBhIGNoYW5nZSBsaXN0ZW5lciwgd2l0aCB0aGUgZm9sbG93aW5nXG4gICAqIGNhdmVhdHM6XG4gICAqXG4gICAqIDEuIFRoZSBzdWJzY3JpcHRpb25zIGFyZSBzbmFwc2hvdHRlZCBqdXN0IGJlZm9yZSBldmVyeSBgZGlzcGF0Y2goKWAgY2FsbC5cbiAgICogSWYgeW91IHN1YnNjcmliZSBvciB1bnN1YnNjcmliZSB3aGlsZSB0aGUgbGlzdGVuZXJzIGFyZSBiZWluZyBpbnZva2VkLCB0aGlzXG4gICAqIHdpbGwgbm90IGhhdmUgYW55IGVmZmVjdCBvbiB0aGUgYGRpc3BhdGNoKClgIHRoYXQgaXMgY3VycmVudGx5IGluIHByb2dyZXNzLlxuICAgKiBIb3dldmVyLCB0aGUgbmV4dCBgZGlzcGF0Y2goKWAgY2FsbCwgd2hldGhlciBuZXN0ZWQgb3Igbm90LCB3aWxsIHVzZSBhIG1vcmVcbiAgICogcmVjZW50IHNuYXBzaG90IG9mIHRoZSBzdWJzY3JpcHRpb24gbGlzdC5cbiAgICpcbiAgICogMi4gVGhlIGxpc3RlbmVyIHNob3VsZCBub3QgZXhwZWN0IHRvIHNlZSBhbGwgc3RhdGUgY2hhbmdlcywgYXMgdGhlIHN0YXRlXG4gICAqIG1pZ2h0IGhhdmUgYmVlbiB1cGRhdGVkIG11bHRpcGxlIHRpbWVzIGR1cmluZyBhIG5lc3RlZCBgZGlzcGF0Y2goKWAgYmVmb3JlXG4gICAqIHRoZSBsaXN0ZW5lciBpcyBjYWxsZWQuIEl0IGlzLCBob3dldmVyLCBndWFyYW50ZWVkIHRoYXQgYWxsIHN1YnNjcmliZXJzXG4gICAqIHJlZ2lzdGVyZWQgYmVmb3JlIHRoZSBgZGlzcGF0Y2goKWAgc3RhcnRlZCB3aWxsIGJlIGNhbGxlZCB3aXRoIHRoZSBsYXRlc3RcbiAgICogc3RhdGUgYnkgdGhlIHRpbWUgaXQgZXhpdHMuXG4gICAqXG4gICAqIEBwYXJhbSB7RnVuY3Rpb259IGxpc3RlbmVyIEEgY2FsbGJhY2sgdG8gYmUgaW52b2tlZCBvbiBldmVyeSBkaXNwYXRjaC5cbiAgICogQHJldHVybnMge0Z1bmN0aW9ufSBBIGZ1bmN0aW9uIHRvIHJlbW92ZSB0aGlzIGNoYW5nZSBsaXN0ZW5lci5cbiAgICovXG5cblxuICBmdW5jdGlvbiBzdWJzY3JpYmUobGlzdGVuZXIpIHtcbiAgICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoIHRydWUgPyBmb3JtYXRQcm9kRXJyb3JNZXNzYWdlKDQpIDogMCk7XG4gICAgfVxuXG4gICAgaWYgKGlzRGlzcGF0Y2hpbmcpIHtcbiAgICAgIHRocm93IG5ldyBFcnJvciggdHJ1ZSA/IGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoNSkgOiAwKTtcbiAgICB9XG5cbiAgICB2YXIgaXNTdWJzY3JpYmVkID0gdHJ1ZTtcbiAgICBlbnN1cmVDYW5NdXRhdGVOZXh0TGlzdGVuZXJzKCk7XG4gICAgbmV4dExpc3RlbmVycy5wdXNoKGxpc3RlbmVyKTtcbiAgICByZXR1cm4gZnVuY3Rpb24gdW5zdWJzY3JpYmUoKSB7XG4gICAgICBpZiAoIWlzU3Vic2NyaWJlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGlmIChpc0Rpc3BhdGNoaW5nKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvciggdHJ1ZSA/IGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoNikgOiAwKTtcbiAgICAgIH1cblxuICAgICAgaXNTdWJzY3JpYmVkID0gZmFsc2U7XG4gICAgICBlbnN1cmVDYW5NdXRhdGVOZXh0TGlzdGVuZXJzKCk7XG4gICAgICB2YXIgaW5kZXggPSBuZXh0TGlzdGVuZXJzLmluZGV4T2YobGlzdGVuZXIpO1xuICAgICAgbmV4dExpc3RlbmVycy5zcGxpY2UoaW5kZXgsIDEpO1xuICAgICAgY3VycmVudExpc3RlbmVycyA9IG51bGw7XG4gICAgfTtcbiAgfVxuICAvKipcbiAgICogRGlzcGF0Y2hlcyBhbiBhY3Rpb24uIEl0IGlzIHRoZSBvbmx5IHdheSB0byB0cmlnZ2VyIGEgc3RhdGUgY2hhbmdlLlxuICAgKlxuICAgKiBUaGUgYHJlZHVjZXJgIGZ1bmN0aW9uLCB1c2VkIHRvIGNyZWF0ZSB0aGUgc3RvcmUsIHdpbGwgYmUgY2FsbGVkIHdpdGggdGhlXG4gICAqIGN1cnJlbnQgc3RhdGUgdHJlZSBhbmQgdGhlIGdpdmVuIGBhY3Rpb25gLiBJdHMgcmV0dXJuIHZhbHVlIHdpbGxcbiAgICogYmUgY29uc2lkZXJlZCB0aGUgKipuZXh0Kiogc3RhdGUgb2YgdGhlIHRyZWUsIGFuZCB0aGUgY2hhbmdlIGxpc3RlbmVyc1xuICAgKiB3aWxsIGJlIG5vdGlmaWVkLlxuICAgKlxuICAgKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvbmx5IHN1cHBvcnRzIHBsYWluIG9iamVjdCBhY3Rpb25zLiBJZiB5b3Ugd2FudCB0b1xuICAgKiBkaXNwYXRjaCBhIFByb21pc2UsIGFuIE9ic2VydmFibGUsIGEgdGh1bmssIG9yIHNvbWV0aGluZyBlbHNlLCB5b3UgbmVlZCB0b1xuICAgKiB3cmFwIHlvdXIgc3RvcmUgY3JlYXRpbmcgZnVuY3Rpb24gaW50byB0aGUgY29ycmVzcG9uZGluZyBtaWRkbGV3YXJlLiBGb3JcbiAgICogZXhhbXBsZSwgc2VlIHRoZSBkb2N1bWVudGF0aW9uIGZvciB0aGUgYHJlZHV4LXRodW5rYCBwYWNrYWdlLiBFdmVuIHRoZVxuICAgKiBtaWRkbGV3YXJlIHdpbGwgZXZlbnR1YWxseSBkaXNwYXRjaCBwbGFpbiBvYmplY3QgYWN0aW9ucyB1c2luZyB0aGlzIG1ldGhvZC5cbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IGFjdGlvbiBBIHBsYWluIG9iamVjdCByZXByZXNlbnRpbmcgXHUyMDFDd2hhdCBjaGFuZ2VkXHUyMDFELiBJdCBpc1xuICAgKiBhIGdvb2QgaWRlYSB0byBrZWVwIGFjdGlvbnMgc2VyaWFsaXphYmxlIHNvIHlvdSBjYW4gcmVjb3JkIGFuZCByZXBsYXkgdXNlclxuICAgKiBzZXNzaW9ucywgb3IgdXNlIHRoZSB0aW1lIHRyYXZlbGxpbmcgYHJlZHV4LWRldnRvb2xzYC4gQW4gYWN0aW9uIG11c3QgaGF2ZVxuICAgKiBhIGB0eXBlYCBwcm9wZXJ0eSB3aGljaCBtYXkgbm90IGJlIGB1bmRlZmluZWRgLiBJdCBpcyBhIGdvb2QgaWRlYSB0byB1c2VcbiAgICogc3RyaW5nIGNvbnN0YW50cyBmb3IgYWN0aW9uIHR5cGVzLlxuICAgKlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fSBGb3IgY29udmVuaWVuY2UsIHRoZSBzYW1lIGFjdGlvbiBvYmplY3QgeW91IGRpc3BhdGNoZWQuXG4gICAqXG4gICAqIE5vdGUgdGhhdCwgaWYgeW91IHVzZSBhIGN1c3RvbSBtaWRkbGV3YXJlLCBpdCBtYXkgd3JhcCBgZGlzcGF0Y2goKWAgdG9cbiAgICogcmV0dXJuIHNvbWV0aGluZyBlbHNlIChmb3IgZXhhbXBsZSwgYSBQcm9taXNlIHlvdSBjYW4gYXdhaXQpLlxuICAgKi9cblxuXG4gIGZ1bmN0aW9uIGRpc3BhdGNoKGFjdGlvbikge1xuICAgIGlmICghaXNQbGFpbk9iamVjdChhY3Rpb24pKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoIHRydWUgPyBmb3JtYXRQcm9kRXJyb3JNZXNzYWdlKDcpIDogMCk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiBhY3Rpb24udHlwZSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgIHRocm93IG5ldyBFcnJvciggdHJ1ZSA/IGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoOCkgOiAwKTtcbiAgICB9XG5cbiAgICBpZiAoaXNEaXNwYXRjaGluZykge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKCB0cnVlID8gZm9ybWF0UHJvZEVycm9yTWVzc2FnZSg5KSA6IDApO1xuICAgIH1cblxuICAgIHRyeSB7XG4gICAgICBpc0Rpc3BhdGNoaW5nID0gdHJ1ZTtcbiAgICAgIGN1cnJlbnRTdGF0ZSA9IGN1cnJlbnRSZWR1Y2VyKGN1cnJlbnRTdGF0ZSwgYWN0aW9uKTtcbiAgICB9IGZpbmFsbHkge1xuICAgICAgaXNEaXNwYXRjaGluZyA9IGZhbHNlO1xuICAgIH1cblxuICAgIHZhciBsaXN0ZW5lcnMgPSBjdXJyZW50TGlzdGVuZXJzID0gbmV4dExpc3RlbmVycztcblxuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGlzdGVuZXJzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgbGlzdGVuZXIgPSBsaXN0ZW5lcnNbaV07XG4gICAgICBsaXN0ZW5lcigpO1xuICAgIH1cblxuICAgIHJldHVybiBhY3Rpb247XG4gIH1cbiAgLyoqXG4gICAqIFJlcGxhY2VzIHRoZSByZWR1Y2VyIGN1cnJlbnRseSB1c2VkIGJ5IHRoZSBzdG9yZSB0byBjYWxjdWxhdGUgdGhlIHN0YXRlLlxuICAgKlxuICAgKiBZb3UgbWlnaHQgbmVlZCB0aGlzIGlmIHlvdXIgYXBwIGltcGxlbWVudHMgY29kZSBzcGxpdHRpbmcgYW5kIHlvdSB3YW50IHRvXG4gICAqIGxvYWQgc29tZSBvZiB0aGUgcmVkdWNlcnMgZHluYW1pY2FsbHkuIFlvdSBtaWdodCBhbHNvIG5lZWQgdGhpcyBpZiB5b3VcbiAgICogaW1wbGVtZW50IGEgaG90IHJlbG9hZGluZyBtZWNoYW5pc20gZm9yIFJlZHV4LlxuICAgKlxuICAgKiBAcGFyYW0ge0Z1bmN0aW9ufSBuZXh0UmVkdWNlciBUaGUgcmVkdWNlciBmb3IgdGhlIHN0b3JlIHRvIHVzZSBpbnN0ZWFkLlxuICAgKiBAcmV0dXJucyB7dm9pZH1cbiAgICovXG5cblxuICBmdW5jdGlvbiByZXBsYWNlUmVkdWNlcihuZXh0UmVkdWNlcikge1xuICAgIGlmICh0eXBlb2YgbmV4dFJlZHVjZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgIHRocm93IG5ldyBFcnJvciggdHJ1ZSA/IGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoMTApIDogMCk7XG4gICAgfVxuXG4gICAgY3VycmVudFJlZHVjZXIgPSBuZXh0UmVkdWNlcjsgLy8gVGhpcyBhY3Rpb24gaGFzIGEgc2ltaWxpYXIgZWZmZWN0IHRvIEFjdGlvblR5cGVzLklOSVQuXG4gICAgLy8gQW55IHJlZHVjZXJzIHRoYXQgZXhpc3RlZCBpbiBib3RoIHRoZSBuZXcgYW5kIG9sZCByb290UmVkdWNlclxuICAgIC8vIHdpbGwgcmVjZWl2ZSB0aGUgcHJldmlvdXMgc3RhdGUuIFRoaXMgZWZmZWN0aXZlbHkgcG9wdWxhdGVzXG4gICAgLy8gdGhlIG5ldyBzdGF0ZSB0cmVlIHdpdGggYW55IHJlbGV2YW50IGRhdGEgZnJvbSB0aGUgb2xkIG9uZS5cblxuICAgIGRpc3BhdGNoKHtcbiAgICAgIHR5cGU6IEFjdGlvblR5cGVzLlJFUExBQ0VcbiAgICB9KTtcbiAgfVxuICAvKipcbiAgICogSW50ZXJvcGVyYWJpbGl0eSBwb2ludCBmb3Igb2JzZXJ2YWJsZS9yZWFjdGl2ZSBsaWJyYXJpZXMuXG4gICAqIEByZXR1cm5zIHtvYnNlcnZhYmxlfSBBIG1pbmltYWwgb2JzZXJ2YWJsZSBvZiBzdGF0ZSBjaGFuZ2VzLlxuICAgKiBGb3IgbW9yZSBpbmZvcm1hdGlvbiwgc2VlIHRoZSBvYnNlcnZhYmxlIHByb3Bvc2FsOlxuICAgKiBodHRwczovL2dpdGh1Yi5jb20vdGMzOS9wcm9wb3NhbC1vYnNlcnZhYmxlXG4gICAqL1xuXG5cbiAgZnVuY3Rpb24gb2JzZXJ2YWJsZSgpIHtcbiAgICB2YXIgX3JlZjtcblxuICAgIHZhciBvdXRlclN1YnNjcmliZSA9IHN1YnNjcmliZTtcbiAgICByZXR1cm4gX3JlZiA9IHtcbiAgICAgIC8qKlxuICAgICAgICogVGhlIG1pbmltYWwgb2JzZXJ2YWJsZSBzdWJzY3JpcHRpb24gbWV0aG9kLlxuICAgICAgICogQHBhcmFtIHtPYmplY3R9IG9ic2VydmVyIEFueSBvYmplY3QgdGhhdCBjYW4gYmUgdXNlZCBhcyBhbiBvYnNlcnZlci5cbiAgICAgICAqIFRoZSBvYnNlcnZlciBvYmplY3Qgc2hvdWxkIGhhdmUgYSBgbmV4dGAgbWV0aG9kLlxuICAgICAgICogQHJldHVybnMge3N1YnNjcmlwdGlvbn0gQW4gb2JqZWN0IHdpdGggYW4gYHVuc3Vic2NyaWJlYCBtZXRob2QgdGhhdCBjYW5cbiAgICAgICAqIGJlIHVzZWQgdG8gdW5zdWJzY3JpYmUgdGhlIG9ic2VydmFibGUgZnJvbSB0aGUgc3RvcmUsIGFuZCBwcmV2ZW50IGZ1cnRoZXJcbiAgICAgICAqIGVtaXNzaW9uIG9mIHZhbHVlcyBmcm9tIHRoZSBvYnNlcnZhYmxlLlxuICAgICAgICovXG4gICAgICBzdWJzY3JpYmU6IGZ1bmN0aW9uIHN1YnNjcmliZShvYnNlcnZlcikge1xuICAgICAgICBpZiAodHlwZW9mIG9ic2VydmVyICE9PSAnb2JqZWN0JyB8fCBvYnNlcnZlciA9PT0gbnVsbCkge1xuICAgICAgICAgIHRocm93IG5ldyBFcnJvciggdHJ1ZSA/IGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoMTEpIDogMCk7XG4gICAgICAgIH1cblxuICAgICAgICBmdW5jdGlvbiBvYnNlcnZlU3RhdGUoKSB7XG4gICAgICAgICAgaWYgKG9ic2VydmVyLm5leHQpIHtcbiAgICAgICAgICAgIG9ic2VydmVyLm5leHQoZ2V0U3RhdGUoKSk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgb2JzZXJ2ZVN0YXRlKCk7XG4gICAgICAgIHZhciB1bnN1YnNjcmliZSA9IG91dGVyU3Vic2NyaWJlKG9ic2VydmVTdGF0ZSk7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgdW5zdWJzY3JpYmU6IHVuc3Vic2NyaWJlXG4gICAgICAgIH07XG4gICAgICB9XG4gICAgfSwgX3JlZlskJG9ic2VydmFibGVdID0gZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSwgX3JlZjtcbiAgfSAvLyBXaGVuIGEgc3RvcmUgaXMgY3JlYXRlZCwgYW4gXCJJTklUXCIgYWN0aW9uIGlzIGRpc3BhdGNoZWQgc28gdGhhdCBldmVyeVxuICAvLyByZWR1Y2VyIHJldHVybnMgdGhlaXIgaW5pdGlhbCBzdGF0ZS4gVGhpcyBlZmZlY3RpdmVseSBwb3B1bGF0ZXNcbiAgLy8gdGhlIGluaXRpYWwgc3RhdGUgdHJlZS5cblxuXG4gIGRpc3BhdGNoKHtcbiAgICB0eXBlOiBBY3Rpb25UeXBlcy5JTklUXG4gIH0pO1xuICByZXR1cm4gX3JlZjIgPSB7XG4gICAgZGlzcGF0Y2g6IGRpc3BhdGNoLFxuICAgIHN1YnNjcmliZTogc3Vic2NyaWJlLFxuICAgIGdldFN0YXRlOiBnZXRTdGF0ZSxcbiAgICByZXBsYWNlUmVkdWNlcjogcmVwbGFjZVJlZHVjZXJcbiAgfSwgX3JlZjJbJCRvYnNlcnZhYmxlXSA9IG9ic2VydmFibGUsIF9yZWYyO1xufVxuLyoqXG4gKiBDcmVhdGVzIGEgUmVkdXggc3RvcmUgdGhhdCBob2xkcyB0aGUgc3RhdGUgdHJlZS5cbiAqXG4gKiAqKldlIHJlY29tbWVuZCB1c2luZyBgY29uZmlndXJlU3RvcmVgIGZyb20gdGhlXG4gKiBgQHJlZHV4anMvdG9vbGtpdGAgcGFja2FnZSoqLCB3aGljaCByZXBsYWNlcyBgY3JlYXRlU3RvcmVgOlxuICogKipodHRwczovL3JlZHV4LmpzLm9yZy9pbnRyb2R1Y3Rpb24vd2h5LXJ0ay1pcy1yZWR1eC10b2RheSoqXG4gKlxuICogVGhlIG9ubHkgd2F5IHRvIGNoYW5nZSB0aGUgZGF0YSBpbiB0aGUgc3RvcmUgaXMgdG8gY2FsbCBgZGlzcGF0Y2goKWAgb24gaXQuXG4gKlxuICogVGhlcmUgc2hvdWxkIG9ubHkgYmUgYSBzaW5nbGUgc3RvcmUgaW4geW91ciBhcHAuIFRvIHNwZWNpZnkgaG93IGRpZmZlcmVudFxuICogcGFydHMgb2YgdGhlIHN0YXRlIHRyZWUgcmVzcG9uZCB0byBhY3Rpb25zLCB5b3UgbWF5IGNvbWJpbmUgc2V2ZXJhbCByZWR1Y2Vyc1xuICogaW50byBhIHNpbmdsZSByZWR1Y2VyIGZ1bmN0aW9uIGJ5IHVzaW5nIGBjb21iaW5lUmVkdWNlcnNgLlxuICpcbiAqIEBwYXJhbSB7RnVuY3Rpb259IHJlZHVjZXIgQSBmdW5jdGlvbiB0aGF0IHJldHVybnMgdGhlIG5leHQgc3RhdGUgdHJlZSwgZ2l2ZW5cbiAqIHRoZSBjdXJyZW50IHN0YXRlIHRyZWUgYW5kIHRoZSBhY3Rpb24gdG8gaGFuZGxlLlxuICpcbiAqIEBwYXJhbSB7YW55fSBbcHJlbG9hZGVkU3RhdGVdIFRoZSBpbml0aWFsIHN0YXRlLiBZb3UgbWF5IG9wdGlvbmFsbHkgc3BlY2lmeSBpdFxuICogdG8gaHlkcmF0ZSB0aGUgc3RhdGUgZnJvbSB0aGUgc2VydmVyIGluIHVuaXZlcnNhbCBhcHBzLCBvciB0byByZXN0b3JlIGFcbiAqIHByZXZpb3VzbHkgc2VyaWFsaXplZCB1c2VyIHNlc3Npb24uXG4gKiBJZiB5b3UgdXNlIGBjb21iaW5lUmVkdWNlcnNgIHRvIHByb2R1Y2UgdGhlIHJvb3QgcmVkdWNlciBmdW5jdGlvbiwgdGhpcyBtdXN0IGJlXG4gKiBhbiBvYmplY3Qgd2l0aCB0aGUgc2FtZSBzaGFwZSBhcyBgY29tYmluZVJlZHVjZXJzYCBrZXlzLlxuICpcbiAqIEBwYXJhbSB7RnVuY3Rpb259IFtlbmhhbmNlcl0gVGhlIHN0b3JlIGVuaGFuY2VyLiBZb3UgbWF5IG9wdGlvbmFsbHkgc3BlY2lmeSBpdFxuICogdG8gZW5oYW5jZSB0aGUgc3RvcmUgd2l0aCB0aGlyZC1wYXJ0eSBjYXBhYmlsaXRpZXMgc3VjaCBhcyBtaWRkbGV3YXJlLFxuICogdGltZSB0cmF2ZWwsIHBlcnNpc3RlbmNlLCBldGMuIFRoZSBvbmx5IHN0b3JlIGVuaGFuY2VyIHRoYXQgc2hpcHMgd2l0aCBSZWR1eFxuICogaXMgYGFwcGx5TWlkZGxld2FyZSgpYC5cbiAqXG4gKiBAcmV0dXJucyB7U3RvcmV9IEEgUmVkdXggc3RvcmUgdGhhdCBsZXRzIHlvdSByZWFkIHRoZSBzdGF0ZSwgZGlzcGF0Y2ggYWN0aW9uc1xuICogYW5kIHN1YnNjcmliZSB0byBjaGFuZ2VzLlxuICovXG5cbnZhciBsZWdhY3lfY3JlYXRlU3RvcmUgPSBjcmVhdGVTdG9yZTtcblxuLyoqXG4gKiBQcmludHMgYSB3YXJuaW5nIGluIHRoZSBjb25zb2xlIGlmIGl0IGV4aXN0cy5cbiAqXG4gKiBAcGFyYW0ge1N0cmluZ30gbWVzc2FnZSBUaGUgd2FybmluZyBtZXNzYWdlLlxuICogQHJldHVybnMge3ZvaWR9XG4gKi9cbmZ1bmN0aW9uIHdhcm5pbmcobWVzc2FnZSkge1xuICAvKiBlc2xpbnQtZGlzYWJsZSBuby1jb25zb2xlICovXG4gIGlmICh0eXBlb2YgY29uc29sZSAhPT0gJ3VuZGVmaW5lZCcgJiYgdHlwZW9mIGNvbnNvbGUuZXJyb3IgPT09ICdmdW5jdGlvbicpIHtcbiAgICBjb25zb2xlLmVycm9yKG1lc3NhZ2UpO1xuICB9XG4gIC8qIGVzbGludC1lbmFibGUgbm8tY29uc29sZSAqL1xuXG5cbiAgdHJ5IHtcbiAgICAvLyBUaGlzIGVycm9yIHdhcyB0aHJvd24gYXMgYSBjb252ZW5pZW5jZSBzbyB0aGF0IGlmIHlvdSBlbmFibGVcbiAgICAvLyBcImJyZWFrIG9uIGFsbCBleGNlcHRpb25zXCIgaW4geW91ciBjb25zb2xlLFxuICAgIC8vIGl0IHdvdWxkIHBhdXNlIHRoZSBleGVjdXRpb24gYXQgdGhpcyBsaW5lLlxuICAgIHRocm93IG5ldyBFcnJvcihtZXNzYWdlKTtcbiAgfSBjYXRjaCAoZSkge30gLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby1lbXB0eVxuXG59XG5cbmZ1bmN0aW9uIGdldFVuZXhwZWN0ZWRTdGF0ZVNoYXBlV2FybmluZ01lc3NhZ2UoaW5wdXRTdGF0ZSwgcmVkdWNlcnMsIGFjdGlvbiwgdW5leHBlY3RlZEtleUNhY2hlKSB7XG4gIHZhciByZWR1Y2VyS2V5cyA9IE9iamVjdC5rZXlzKHJlZHVjZXJzKTtcbiAgdmFyIGFyZ3VtZW50TmFtZSA9IGFjdGlvbiAmJiBhY3Rpb24udHlwZSA9PT0gQWN0aW9uVHlwZXMuSU5JVCA/ICdwcmVsb2FkZWRTdGF0ZSBhcmd1bWVudCBwYXNzZWQgdG8gY3JlYXRlU3RvcmUnIDogJ3ByZXZpb3VzIHN0YXRlIHJlY2VpdmVkIGJ5IHRoZSByZWR1Y2VyJztcblxuICBpZiAocmVkdWNlcktleXMubGVuZ3RoID09PSAwKSB7XG4gICAgcmV0dXJuICdTdG9yZSBkb2VzIG5vdCBoYXZlIGEgdmFsaWQgcmVkdWNlci4gTWFrZSBzdXJlIHRoZSBhcmd1bWVudCBwYXNzZWQgJyArICd0byBjb21iaW5lUmVkdWNlcnMgaXMgYW4gb2JqZWN0IHdob3NlIHZhbHVlcyBhcmUgcmVkdWNlcnMuJztcbiAgfVxuXG4gIGlmICghaXNQbGFpbk9iamVjdChpbnB1dFN0YXRlKSkge1xuICAgIHJldHVybiBcIlRoZSBcIiArIGFyZ3VtZW50TmFtZSArIFwiIGhhcyB1bmV4cGVjdGVkIHR5cGUgb2YgXFxcIlwiICsga2luZE9mKGlucHV0U3RhdGUpICsgXCJcXFwiLiBFeHBlY3RlZCBhcmd1bWVudCB0byBiZSBhbiBvYmplY3Qgd2l0aCB0aGUgZm9sbG93aW5nIFwiICsgKFwia2V5czogXFxcIlwiICsgcmVkdWNlcktleXMuam9pbignXCIsIFwiJykgKyBcIlxcXCJcIik7XG4gIH1cblxuICB2YXIgdW5leHBlY3RlZEtleXMgPSBPYmplY3Qua2V5cyhpbnB1dFN0YXRlKS5maWx0ZXIoZnVuY3Rpb24gKGtleSkge1xuICAgIHJldHVybiAhcmVkdWNlcnMuaGFzT3duUHJvcGVydHkoa2V5KSAmJiAhdW5leHBlY3RlZEtleUNhY2hlW2tleV07XG4gIH0pO1xuICB1bmV4cGVjdGVkS2V5cy5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICB1bmV4cGVjdGVkS2V5Q2FjaGVba2V5XSA9IHRydWU7XG4gIH0pO1xuICBpZiAoYWN0aW9uICYmIGFjdGlvbi50eXBlID09PSBBY3Rpb25UeXBlcy5SRVBMQUNFKSByZXR1cm47XG5cbiAgaWYgKHVuZXhwZWN0ZWRLZXlzLmxlbmd0aCA+IDApIHtcbiAgICByZXR1cm4gXCJVbmV4cGVjdGVkIFwiICsgKHVuZXhwZWN0ZWRLZXlzLmxlbmd0aCA+IDEgPyAna2V5cycgOiAna2V5JykgKyBcIiBcIiArIChcIlxcXCJcIiArIHVuZXhwZWN0ZWRLZXlzLmpvaW4oJ1wiLCBcIicpICsgXCJcXFwiIGZvdW5kIGluIFwiICsgYXJndW1lbnROYW1lICsgXCIuIFwiKSArIFwiRXhwZWN0ZWQgdG8gZmluZCBvbmUgb2YgdGhlIGtub3duIHJlZHVjZXIga2V5cyBpbnN0ZWFkOiBcIiArIChcIlxcXCJcIiArIHJlZHVjZXJLZXlzLmpvaW4oJ1wiLCBcIicpICsgXCJcXFwiLiBVbmV4cGVjdGVkIGtleXMgd2lsbCBiZSBpZ25vcmVkLlwiKTtcbiAgfVxufVxuXG5mdW5jdGlvbiBhc3NlcnRSZWR1Y2VyU2hhcGUocmVkdWNlcnMpIHtcbiAgT2JqZWN0LmtleXMocmVkdWNlcnMpLmZvckVhY2goZnVuY3Rpb24gKGtleSkge1xuICAgIHZhciByZWR1Y2VyID0gcmVkdWNlcnNba2V5XTtcbiAgICB2YXIgaW5pdGlhbFN0YXRlID0gcmVkdWNlcih1bmRlZmluZWQsIHtcbiAgICAgIHR5cGU6IEFjdGlvblR5cGVzLklOSVRcbiAgICB9KTtcblxuICAgIGlmICh0eXBlb2YgaW5pdGlhbFN0YXRlID09PSAndW5kZWZpbmVkJykge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKCB0cnVlID8gZm9ybWF0UHJvZEVycm9yTWVzc2FnZSgxMikgOiAwKTtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHJlZHVjZXIodW5kZWZpbmVkLCB7XG4gICAgICB0eXBlOiBBY3Rpb25UeXBlcy5QUk9CRV9VTktOT1dOX0FDVElPTigpXG4gICAgfSkgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoIHRydWUgPyBmb3JtYXRQcm9kRXJyb3JNZXNzYWdlKDEzKSA6IDApO1xuICAgIH1cbiAgfSk7XG59XG4vKipcbiAqIFR1cm5zIGFuIG9iamVjdCB3aG9zZSB2YWx1ZXMgYXJlIGRpZmZlcmVudCByZWR1Y2VyIGZ1bmN0aW9ucywgaW50byBhIHNpbmdsZVxuICogcmVkdWNlciBmdW5jdGlvbi4gSXQgd2lsbCBjYWxsIGV2ZXJ5IGNoaWxkIHJlZHVjZXIsIGFuZCBnYXRoZXIgdGhlaXIgcmVzdWx0c1xuICogaW50byBhIHNpbmdsZSBzdGF0ZSBvYmplY3QsIHdob3NlIGtleXMgY29ycmVzcG9uZCB0byB0aGUga2V5cyBvZiB0aGUgcGFzc2VkXG4gKiByZWR1Y2VyIGZ1bmN0aW9ucy5cbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gcmVkdWNlcnMgQW4gb2JqZWN0IHdob3NlIHZhbHVlcyBjb3JyZXNwb25kIHRvIGRpZmZlcmVudFxuICogcmVkdWNlciBmdW5jdGlvbnMgdGhhdCBuZWVkIHRvIGJlIGNvbWJpbmVkIGludG8gb25lLiBPbmUgaGFuZHkgd2F5IHRvIG9idGFpblxuICogaXQgaXMgdG8gdXNlIEVTNiBgaW1wb3J0ICogYXMgcmVkdWNlcnNgIHN5bnRheC4gVGhlIHJlZHVjZXJzIG1heSBuZXZlciByZXR1cm5cbiAqIHVuZGVmaW5lZCBmb3IgYW55IGFjdGlvbi4gSW5zdGVhZCwgdGhleSBzaG91bGQgcmV0dXJuIHRoZWlyIGluaXRpYWwgc3RhdGVcbiAqIGlmIHRoZSBzdGF0ZSBwYXNzZWQgdG8gdGhlbSB3YXMgdW5kZWZpbmVkLCBhbmQgdGhlIGN1cnJlbnQgc3RhdGUgZm9yIGFueVxuICogdW5yZWNvZ25pemVkIGFjdGlvbi5cbiAqXG4gKiBAcmV0dXJucyB7RnVuY3Rpb259IEEgcmVkdWNlciBmdW5jdGlvbiB0aGF0IGludm9rZXMgZXZlcnkgcmVkdWNlciBpbnNpZGUgdGhlXG4gKiBwYXNzZWQgb2JqZWN0LCBhbmQgYnVpbGRzIGEgc3RhdGUgb2JqZWN0IHdpdGggdGhlIHNhbWUgc2hhcGUuXG4gKi9cblxuXG5mdW5jdGlvbiBjb21iaW5lUmVkdWNlcnMocmVkdWNlcnMpIHtcbiAgdmFyIHJlZHVjZXJLZXlzID0gT2JqZWN0LmtleXMocmVkdWNlcnMpO1xuICB2YXIgZmluYWxSZWR1Y2VycyA9IHt9O1xuXG4gIGZvciAodmFyIGkgPSAwOyBpIDwgcmVkdWNlcktleXMubGVuZ3RoOyBpKyspIHtcbiAgICB2YXIga2V5ID0gcmVkdWNlcktleXNbaV07XG5cbiAgICBpZiAoZmFsc2UpIHt9XG5cbiAgICBpZiAodHlwZW9mIHJlZHVjZXJzW2tleV0gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIGZpbmFsUmVkdWNlcnNba2V5XSA9IHJlZHVjZXJzW2tleV07XG4gICAgfVxuICB9XG5cbiAgdmFyIGZpbmFsUmVkdWNlcktleXMgPSBPYmplY3Qua2V5cyhmaW5hbFJlZHVjZXJzKTsgLy8gVGhpcyBpcyB1c2VkIHRvIG1ha2Ugc3VyZSB3ZSBkb24ndCB3YXJuIGFib3V0IHRoZSBzYW1lXG4gIC8vIGtleXMgbXVsdGlwbGUgdGltZXMuXG5cbiAgdmFyIHVuZXhwZWN0ZWRLZXlDYWNoZTtcblxuICBpZiAoZmFsc2UpIHt9XG5cbiAgdmFyIHNoYXBlQXNzZXJ0aW9uRXJyb3I7XG5cbiAgdHJ5IHtcbiAgICBhc3NlcnRSZWR1Y2VyU2hhcGUoZmluYWxSZWR1Y2Vycyk7XG4gIH0gY2F0Y2ggKGUpIHtcbiAgICBzaGFwZUFzc2VydGlvbkVycm9yID0gZTtcbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiBjb21iaW5hdGlvbihzdGF0ZSwgYWN0aW9uKSB7XG4gICAgaWYgKHN0YXRlID09PSB2b2lkIDApIHtcbiAgICAgIHN0YXRlID0ge307XG4gICAgfVxuXG4gICAgaWYgKHNoYXBlQXNzZXJ0aW9uRXJyb3IpIHtcbiAgICAgIHRocm93IHNoYXBlQXNzZXJ0aW9uRXJyb3I7XG4gICAgfVxuXG4gICAgaWYgKGZhbHNlKSB7IHZhciB3YXJuaW5nTWVzc2FnZTsgfVxuXG4gICAgdmFyIGhhc0NoYW5nZWQgPSBmYWxzZTtcbiAgICB2YXIgbmV4dFN0YXRlID0ge307XG5cbiAgICBmb3IgKHZhciBfaSA9IDA7IF9pIDwgZmluYWxSZWR1Y2VyS2V5cy5sZW5ndGg7IF9pKyspIHtcbiAgICAgIHZhciBfa2V5ID0gZmluYWxSZWR1Y2VyS2V5c1tfaV07XG4gICAgICB2YXIgcmVkdWNlciA9IGZpbmFsUmVkdWNlcnNbX2tleV07XG4gICAgICB2YXIgcHJldmlvdXNTdGF0ZUZvcktleSA9IHN0YXRlW19rZXldO1xuICAgICAgdmFyIG5leHRTdGF0ZUZvcktleSA9IHJlZHVjZXIocHJldmlvdXNTdGF0ZUZvcktleSwgYWN0aW9uKTtcblxuICAgICAgaWYgKHR5cGVvZiBuZXh0U3RhdGVGb3JLZXkgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIHZhciBhY3Rpb25UeXBlID0gYWN0aW9uICYmIGFjdGlvbi50eXBlO1xuICAgICAgICB0aHJvdyBuZXcgRXJyb3IoIHRydWUgPyBmb3JtYXRQcm9kRXJyb3JNZXNzYWdlKDE0KSA6IDApO1xuICAgICAgfVxuXG4gICAgICBuZXh0U3RhdGVbX2tleV0gPSBuZXh0U3RhdGVGb3JLZXk7XG4gICAgICBoYXNDaGFuZ2VkID0gaGFzQ2hhbmdlZCB8fCBuZXh0U3RhdGVGb3JLZXkgIT09IHByZXZpb3VzU3RhdGVGb3JLZXk7XG4gICAgfVxuXG4gICAgaGFzQ2hhbmdlZCA9IGhhc0NoYW5nZWQgfHwgZmluYWxSZWR1Y2VyS2V5cy5sZW5ndGggIT09IE9iamVjdC5rZXlzKHN0YXRlKS5sZW5ndGg7XG4gICAgcmV0dXJuIGhhc0NoYW5nZWQgPyBuZXh0U3RhdGUgOiBzdGF0ZTtcbiAgfTtcbn1cblxuZnVuY3Rpb24gYmluZEFjdGlvbkNyZWF0b3IoYWN0aW9uQ3JlYXRvciwgZGlzcGF0Y2gpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uICgpIHtcbiAgICByZXR1cm4gZGlzcGF0Y2goYWN0aW9uQ3JlYXRvci5hcHBseSh0aGlzLCBhcmd1bWVudHMpKTtcbiAgfTtcbn1cbi8qKlxuICogVHVybnMgYW4gb2JqZWN0IHdob3NlIHZhbHVlcyBhcmUgYWN0aW9uIGNyZWF0b3JzLCBpbnRvIGFuIG9iamVjdCB3aXRoIHRoZVxuICogc2FtZSBrZXlzLCBidXQgd2l0aCBldmVyeSBmdW5jdGlvbiB3cmFwcGVkIGludG8gYSBgZGlzcGF0Y2hgIGNhbGwgc28gdGhleVxuICogbWF5IGJlIGludm9rZWQgZGlyZWN0bHkuIFRoaXMgaXMganVzdCBhIGNvbnZlbmllbmNlIG1ldGhvZCwgYXMgeW91IGNhbiBjYWxsXG4gKiBgc3RvcmUuZGlzcGF0Y2goTXlBY3Rpb25DcmVhdG9ycy5kb1NvbWV0aGluZygpKWAgeW91cnNlbGYganVzdCBmaW5lLlxuICpcbiAqIEZvciBjb252ZW5pZW5jZSwgeW91IGNhbiBhbHNvIHBhc3MgYW4gYWN0aW9uIGNyZWF0b3IgYXMgdGhlIGZpcnN0IGFyZ3VtZW50LFxuICogYW5kIGdldCBhIGRpc3BhdGNoIHdyYXBwZWQgZnVuY3Rpb24gaW4gcmV0dXJuLlxuICpcbiAqIEBwYXJhbSB7RnVuY3Rpb258T2JqZWN0fSBhY3Rpb25DcmVhdG9ycyBBbiBvYmplY3Qgd2hvc2UgdmFsdWVzIGFyZSBhY3Rpb25cbiAqIGNyZWF0b3IgZnVuY3Rpb25zLiBPbmUgaGFuZHkgd2F5IHRvIG9idGFpbiBpdCBpcyB0byB1c2UgRVM2IGBpbXBvcnQgKiBhc2BcbiAqIHN5bnRheC4gWW91IG1heSBhbHNvIHBhc3MgYSBzaW5nbGUgZnVuY3Rpb24uXG4gKlxuICogQHBhcmFtIHtGdW5jdGlvbn0gZGlzcGF0Y2ggVGhlIGBkaXNwYXRjaGAgZnVuY3Rpb24gYXZhaWxhYmxlIG9uIHlvdXIgUmVkdXhcbiAqIHN0b3JlLlxuICpcbiAqIEByZXR1cm5zIHtGdW5jdGlvbnxPYmplY3R9IFRoZSBvYmplY3QgbWltaWNraW5nIHRoZSBvcmlnaW5hbCBvYmplY3QsIGJ1dCB3aXRoXG4gKiBldmVyeSBhY3Rpb24gY3JlYXRvciB3cmFwcGVkIGludG8gdGhlIGBkaXNwYXRjaGAgY2FsbC4gSWYgeW91IHBhc3NlZCBhXG4gKiBmdW5jdGlvbiBhcyBgYWN0aW9uQ3JlYXRvcnNgLCB0aGUgcmV0dXJuIHZhbHVlIHdpbGwgYWxzbyBiZSBhIHNpbmdsZVxuICogZnVuY3Rpb24uXG4gKi9cblxuXG5mdW5jdGlvbiBiaW5kQWN0aW9uQ3JlYXRvcnMoYWN0aW9uQ3JlYXRvcnMsIGRpc3BhdGNoKSB7XG4gIGlmICh0eXBlb2YgYWN0aW9uQ3JlYXRvcnMgPT09ICdmdW5jdGlvbicpIHtcbiAgICByZXR1cm4gYmluZEFjdGlvbkNyZWF0b3IoYWN0aW9uQ3JlYXRvcnMsIGRpc3BhdGNoKTtcbiAgfVxuXG4gIGlmICh0eXBlb2YgYWN0aW9uQ3JlYXRvcnMgIT09ICdvYmplY3QnIHx8IGFjdGlvbkNyZWF0b3JzID09PSBudWxsKSB7XG4gICAgdGhyb3cgbmV3IEVycm9yKCB0cnVlID8gZm9ybWF0UHJvZEVycm9yTWVzc2FnZSgxNikgOiAwKTtcbiAgfVxuXG4gIHZhciBib3VuZEFjdGlvbkNyZWF0b3JzID0ge307XG5cbiAgZm9yICh2YXIga2V5IGluIGFjdGlvbkNyZWF0b3JzKSB7XG4gICAgdmFyIGFjdGlvbkNyZWF0b3IgPSBhY3Rpb25DcmVhdG9yc1trZXldO1xuXG4gICAgaWYgKHR5cGVvZiBhY3Rpb25DcmVhdG9yID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICBib3VuZEFjdGlvbkNyZWF0b3JzW2tleV0gPSBiaW5kQWN0aW9uQ3JlYXRvcihhY3Rpb25DcmVhdG9yLCBkaXNwYXRjaCk7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGJvdW5kQWN0aW9uQ3JlYXRvcnM7XG59XG5cbi8qKlxuICogQ29tcG9zZXMgc2luZ2xlLWFyZ3VtZW50IGZ1bmN0aW9ucyBmcm9tIHJpZ2h0IHRvIGxlZnQuIFRoZSByaWdodG1vc3RcbiAqIGZ1bmN0aW9uIGNhbiB0YWtlIG11bHRpcGxlIGFyZ3VtZW50cyBhcyBpdCBwcm92aWRlcyB0aGUgc2lnbmF0dXJlIGZvclxuICogdGhlIHJlc3VsdGluZyBjb21wb3NpdGUgZnVuY3Rpb24uXG4gKlxuICogQHBhcmFtIHsuLi5GdW5jdGlvbn0gZnVuY3MgVGhlIGZ1bmN0aW9ucyB0byBjb21wb3NlLlxuICogQHJldHVybnMge0Z1bmN0aW9ufSBBIGZ1bmN0aW9uIG9idGFpbmVkIGJ5IGNvbXBvc2luZyB0aGUgYXJndW1lbnQgZnVuY3Rpb25zXG4gKiBmcm9tIHJpZ2h0IHRvIGxlZnQuIEZvciBleGFtcGxlLCBjb21wb3NlKGYsIGcsIGgpIGlzIGlkZW50aWNhbCB0byBkb2luZ1xuICogKC4uLmFyZ3MpID0+IGYoZyhoKC4uLmFyZ3MpKSkuXG4gKi9cbmZ1bmN0aW9uIGNvbXBvc2UoKSB7XG4gIGZvciAodmFyIF9sZW4gPSBhcmd1bWVudHMubGVuZ3RoLCBmdW5jcyA9IG5ldyBBcnJheShfbGVuKSwgX2tleSA9IDA7IF9rZXkgPCBfbGVuOyBfa2V5KyspIHtcbiAgICBmdW5jc1tfa2V5XSA9IGFyZ3VtZW50c1tfa2V5XTtcbiAgfVxuXG4gIGlmIChmdW5jcy5sZW5ndGggPT09IDApIHtcbiAgICByZXR1cm4gZnVuY3Rpb24gKGFyZykge1xuICAgICAgcmV0dXJuIGFyZztcbiAgICB9O1xuICB9XG5cbiAgaWYgKGZ1bmNzLmxlbmd0aCA9PT0gMSkge1xuICAgIHJldHVybiBmdW5jc1swXTtcbiAgfVxuXG4gIHJldHVybiBmdW5jcy5yZWR1Y2UoZnVuY3Rpb24gKGEsIGIpIHtcbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIGEoYi5hcHBseSh2b2lkIDAsIGFyZ3VtZW50cykpO1xuICAgIH07XG4gIH0pO1xufVxuXG4vKipcbiAqIENyZWF0ZXMgYSBzdG9yZSBlbmhhbmNlciB0aGF0IGFwcGxpZXMgbWlkZGxld2FyZSB0byB0aGUgZGlzcGF0Y2ggbWV0aG9kXG4gKiBvZiB0aGUgUmVkdXggc3RvcmUuIFRoaXMgaXMgaGFuZHkgZm9yIGEgdmFyaWV0eSBvZiB0YXNrcywgc3VjaCBhcyBleHByZXNzaW5nXG4gKiBhc3luY2hyb25vdXMgYWN0aW9ucyBpbiBhIGNvbmNpc2UgbWFubmVyLCBvciBsb2dnaW5nIGV2ZXJ5IGFjdGlvbiBwYXlsb2FkLlxuICpcbiAqIFNlZSBgcmVkdXgtdGh1bmtgIHBhY2thZ2UgYXMgYW4gZXhhbXBsZSBvZiB0aGUgUmVkdXggbWlkZGxld2FyZS5cbiAqXG4gKiBCZWNhdXNlIG1pZGRsZXdhcmUgaXMgcG90ZW50aWFsbHkgYXN5bmNocm9ub3VzLCB0aGlzIHNob3VsZCBiZSB0aGUgZmlyc3RcbiAqIHN0b3JlIGVuaGFuY2VyIGluIHRoZSBjb21wb3NpdGlvbiBjaGFpbi5cbiAqXG4gKiBOb3RlIHRoYXQgZWFjaCBtaWRkbGV3YXJlIHdpbGwgYmUgZ2l2ZW4gdGhlIGBkaXNwYXRjaGAgYW5kIGBnZXRTdGF0ZWAgZnVuY3Rpb25zXG4gKiBhcyBuYW1lZCBhcmd1bWVudHMuXG4gKlxuICogQHBhcmFtIHsuLi5GdW5jdGlvbn0gbWlkZGxld2FyZXMgVGhlIG1pZGRsZXdhcmUgY2hhaW4gdG8gYmUgYXBwbGllZC5cbiAqIEByZXR1cm5zIHtGdW5jdGlvbn0gQSBzdG9yZSBlbmhhbmNlciBhcHBseWluZyB0aGUgbWlkZGxld2FyZS5cbiAqL1xuXG5mdW5jdGlvbiBhcHBseU1pZGRsZXdhcmUoKSB7XG4gIGZvciAodmFyIF9sZW4gPSBhcmd1bWVudHMubGVuZ3RoLCBtaWRkbGV3YXJlcyA9IG5ldyBBcnJheShfbGVuKSwgX2tleSA9IDA7IF9rZXkgPCBfbGVuOyBfa2V5KyspIHtcbiAgICBtaWRkbGV3YXJlc1tfa2V5XSA9IGFyZ3VtZW50c1tfa2V5XTtcbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiAoY3JlYXRlU3RvcmUpIHtcbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgdmFyIHN0b3JlID0gY3JlYXRlU3RvcmUuYXBwbHkodm9pZCAwLCBhcmd1bWVudHMpO1xuXG4gICAgICB2YXIgX2Rpc3BhdGNoID0gZnVuY3Rpb24gZGlzcGF0Y2goKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvciggdHJ1ZSA/IGZvcm1hdFByb2RFcnJvck1lc3NhZ2UoMTUpIDogMCk7XG4gICAgICB9O1xuXG4gICAgICB2YXIgbWlkZGxld2FyZUFQSSA9IHtcbiAgICAgICAgZ2V0U3RhdGU6IHN0b3JlLmdldFN0YXRlLFxuICAgICAgICBkaXNwYXRjaDogZnVuY3Rpb24gZGlzcGF0Y2goKSB7XG4gICAgICAgICAgcmV0dXJuIF9kaXNwYXRjaC5hcHBseSh2b2lkIDAsIGFyZ3VtZW50cyk7XG4gICAgICAgIH1cbiAgICAgIH07XG4gICAgICB2YXIgY2hhaW4gPSBtaWRkbGV3YXJlcy5tYXAoZnVuY3Rpb24gKG1pZGRsZXdhcmUpIHtcbiAgICAgICAgcmV0dXJuIG1pZGRsZXdhcmUobWlkZGxld2FyZUFQSSk7XG4gICAgICB9KTtcbiAgICAgIF9kaXNwYXRjaCA9IGNvbXBvc2UuYXBwbHkodm9pZCAwLCBjaGFpbikoc3RvcmUuZGlzcGF0Y2gpO1xuICAgICAgcmV0dXJuIF9vYmplY3RTcHJlYWQyKF9vYmplY3RTcHJlYWQyKHt9LCBzdG9yZSksIHt9LCB7XG4gICAgICAgIGRpc3BhdGNoOiBfZGlzcGF0Y2hcbiAgICAgIH0pO1xuICAgIH07XG4gIH07XG59XG5cbi8qXG4gKiBUaGlzIGlzIGEgZHVtbXkgZnVuY3Rpb24gdG8gY2hlY2sgaWYgdGhlIGZ1bmN0aW9uIG5hbWUgaGFzIGJlZW4gYWx0ZXJlZCBieSBtaW5pZmljYXRpb24uXG4gKiBJZiB0aGUgZnVuY3Rpb24gaGFzIGJlZW4gbWluaWZpZWQgYW5kIE5PREVfRU5WICE9PSAncHJvZHVjdGlvbicsIHdhcm4gdGhlIHVzZXIuXG4gKi9cblxuZnVuY3Rpb24gaXNDcnVzaGVkKCkge31cblxuaWYgKGZhbHNlKSB7fVxuXG5cblxuXG4vKioqLyB9KVxuXG4vKioqKioqLyBcdH0pO1xuLyoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKi9cbi8qKioqKiovIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuLyoqKioqKi8gXHR2YXIgX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fID0ge307XG4vKioqKioqLyBcdFxuLyoqKioqKi8gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuLyoqKioqKi8gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG4vKioqKioqLyBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4vKioqKioqLyBcdFx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG4vKioqKioqLyBcdFx0aWYgKGNhY2hlZE1vZHVsZSAhPT0gdW5kZWZpbmVkKSB7XG4vKioqKioqLyBcdFx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG4vKioqKioqLyBcdFx0fVxuLyoqKioqKi8gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4vKioqKioqLyBcdFx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG4vKioqKioqLyBcdFx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG4vKioqKioqLyBcdFx0XHQvLyBubyBtb2R1bGUubG9hZGVkIG5lZWRlZFxuLyoqKioqKi8gXHRcdFx0ZXhwb3J0czoge31cbi8qKioqKiovIFx0XHR9O1xuLyoqKioqKi8gXHRcbi8qKioqKiovIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbi8qKioqKiovIFx0XHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcbi8qKioqKiovIFx0XG4vKioqKioqLyBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbi8qKioqKiovIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4vKioqKioqLyBcdH1cbi8qKioqKiovIFx0XG4vKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqL1xuLyoqKioqKi8gXHQvKiB3ZWJwYWNrL3J1bnRpbWUvY29tcGF0IGdldCBkZWZhdWx0IGV4cG9ydCAqL1xuLyoqKioqKi8gXHQhZnVuY3Rpb24oKSB7XG4vKioqKioqLyBcdFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbi8qKioqKiovIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbi8qKioqKiovIFx0XHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuLyoqKioqKi8gXHRcdFx0XHRmdW5jdGlvbigpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbi8qKioqKiovIFx0XHRcdFx0ZnVuY3Rpb24oKSB7IHJldHVybiBtb2R1bGU7IH07XG4vKioqKioqLyBcdFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCB7IGE6IGdldHRlciB9KTtcbi8qKioqKiovIFx0XHRcdHJldHVybiBnZXR0ZXI7XG4vKioqKioqLyBcdFx0fTtcbi8qKioqKiovIFx0fSgpO1xuLyoqKioqKi8gXHRcbi8qKioqKiovIFx0Lyogd2VicGFjay9ydW50aW1lL2RlZmluZSBwcm9wZXJ0eSBnZXR0ZXJzICovXG4vKioqKioqLyBcdCFmdW5jdGlvbigpIHtcbi8qKioqKiovIFx0XHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9ucyBmb3IgaGFybW9ueSBleHBvcnRzXG4vKioqKioqLyBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgZGVmaW5pdGlvbikge1xuLyoqKioqKi8gXHRcdFx0Zm9yKHZhciBrZXkgaW4gZGVmaW5pdGlvbikge1xuLyoqKioqKi8gXHRcdFx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcbi8qKioqKiovIFx0XHRcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywga2V5LCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZGVmaW5pdGlvbltrZXldIH0pO1xuLyoqKioqKi8gXHRcdFx0XHR9XG4vKioqKioqLyBcdFx0XHR9XG4vKioqKioqLyBcdFx0fTtcbi8qKioqKiovIFx0fSgpO1xuLyoqKioqKi8gXHRcbi8qKioqKiovIFx0Lyogd2VicGFjay9ydW50aW1lL2hhc093blByb3BlcnR5IHNob3J0aGFuZCAqL1xuLyoqKioqKi8gXHQhZnVuY3Rpb24oKSB7XG4vKioqKioqLyBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqLCBwcm9wKSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKTsgfVxuLyoqKioqKi8gXHR9KCk7XG4vKioqKioqLyBcdFxuLyoqKioqKi8gXHQvKiB3ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0ICovXG4vKioqKioqLyBcdCFmdW5jdGlvbigpIHtcbi8qKioqKiovIFx0XHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4vKioqKioqLyBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuLyoqKioqKi8gXHRcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4vKioqKioqLyBcdFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuLyoqKioqKi8gXHRcdFx0fVxuLyoqKioqKi8gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbi8qKioqKiovIFx0XHR9O1xuLyoqKioqKi8gXHR9KCk7XG4vKioqKioqLyBcdFxuLyoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKi9cbnZhciBfX3dlYnBhY2tfZXhwb3J0c19fID0ge307XG4vLyBUaGlzIGVudHJ5IG5lZWQgdG8gYmUgd3JhcHBlZCBpbiBhbiBJSUZFIGJlY2F1c2UgaXQgbmVlZCB0byBiZSBpc29sYXRlZCBhZ2FpbnN0IG90aGVyIG1vZHVsZXMgaW4gdGhlIGNodW5rLlxuIWZ1bmN0aW9uKCkge1xuLyogaGFybW9ueSBpbXBvcnQgKi8gdmFyIF9zY3JpcHRzX2Nob2ljZXNfX1dFQlBBQ0tfSU1QT1JURURfTU9EVUxFXzBfXyA9IF9fd2VicGFja19yZXF1aXJlX18oMzczKTtcbi8qIGhhcm1vbnkgaW1wb3J0ICovIHZhciBfc2NyaXB0c19jaG9pY2VzX19XRUJQQUNLX0lNUE9SVEVEX01PRFVMRV8wX19fZGVmYXVsdCA9IC8qI19fUFVSRV9fKi9fX3dlYnBhY2tfcmVxdWlyZV9fLm4oX3NjcmlwdHNfY2hvaWNlc19fV0VCUEFDS19JTVBPUlRFRF9NT0RVTEVfMF9fKTtcbi8qIGhhcm1vbnkgaW1wb3J0ICovIHZhciBfc2NyaXB0c19pbnRlcmZhY2VzX19XRUJQQUNLX0lNUE9SVEVEX01PRFVMRV8xX18gPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDE4Nyk7XG4vKiBoYXJtb255IGltcG9ydCAqLyB2YXIgX3NjcmlwdHNfaW50ZXJmYWNlc19fV0VCUEFDS19JTVBPUlRFRF9NT0RVTEVfMV9fX2RlZmF1bHQgPSAvKiNfX1BVUkVfXyovX193ZWJwYWNrX3JlcXVpcmVfXy5uKF9zY3JpcHRzX2ludGVyZmFjZXNfX1dFQlBBQ0tfSU1QT1JURURfTU9EVUxFXzFfXyk7XG4vKiBoYXJtb255IGltcG9ydCAqLyB2YXIgX3NjcmlwdHNfY29uc3RhbnRzX19XRUJQQUNLX0lNUE9SVEVEX01PRFVMRV8yX18gPSBfX3dlYnBhY2tfcmVxdWlyZV9fKDg4Myk7XG4vKiBoYXJtb255IGltcG9ydCAqLyB2YXIgX3NjcmlwdHNfZGVmYXVsdHNfX1dFQlBBQ0tfSU1QT1JURURfTU9EVUxFXzNfXyA9IF9fd2VicGFja19yZXF1aXJlX18oNzg5KTtcbi8qIGhhcm1vbnkgaW1wb3J0ICovIHZhciBfc2NyaXB0c190ZW1wbGF0ZXNfX1dFQlBBQ0tfSU1QT1JURURfTU9EVUxFXzRfXyA9IF9fd2VicGFja19yZXF1aXJlX18oNjg2KTtcblxuXG5cblxuXG5cblxuLyogaGFybW9ueSBkZWZhdWx0IGV4cG9ydCAqLyBfX3dlYnBhY2tfZXhwb3J0c19fW1wiZGVmYXVsdFwiXSA9ICgoX3NjcmlwdHNfY2hvaWNlc19fV0VCUEFDS19JTVBPUlRFRF9NT0RVTEVfMF9fX2RlZmF1bHQoKSkpO1xuXG59KCk7XG5fX3dlYnBhY2tfZXhwb3J0c19fID0gX193ZWJwYWNrX2V4cG9ydHNfX1tcImRlZmF1bHRcIl07XG4vKioqKioqLyBcdHJldHVybiBfX3dlYnBhY2tfZXhwb3J0c19fO1xuLyoqKioqKi8gfSkoKVxuO1xufSk7IiwgImltcG9ydCBDaG9pY2VzIGZyb20gJ2Nob2ljZXMuanMnXG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHNlbGVjdEZvcm1Db21wb25lbnQoe1xuICAgIGNhblNlbGVjdFBsYWNlaG9sZGVyLFxuICAgIGlzSHRtbEFsbG93ZWQsXG4gICAgZ2V0T3B0aW9uTGFiZWxVc2luZyxcbiAgICBnZXRPcHRpb25MYWJlbHNVc2luZyxcbiAgICBnZXRPcHRpb25zVXNpbmcsXG4gICAgZ2V0U2VhcmNoUmVzdWx0c1VzaW5nLFxuICAgIGlzQXV0b2ZvY3VzZWQsXG4gICAgaXNNdWx0aXBsZSxcbiAgICBpc1NlYXJjaGFibGUsXG4gICAgaGFzRHluYW1pY09wdGlvbnMsXG4gICAgaGFzRHluYW1pY1NlYXJjaFJlc3VsdHMsXG4gICAgbGl2ZXdpcmVJZCxcbiAgICBsb2FkaW5nTWVzc2FnZSxcbiAgICBtYXhJdGVtcyxcbiAgICBtYXhJdGVtc01lc3NhZ2UsXG4gICAgbm9TZWFyY2hSZXN1bHRzTWVzc2FnZSxcbiAgICBvcHRpb25zLFxuICAgIG9wdGlvbnNMaW1pdCxcbiAgICBwbGFjZWhvbGRlcixcbiAgICBwb3NpdGlvbixcbiAgICBzZWFyY2hEZWJvdW5jZSxcbiAgICBzZWFyY2hpbmdNZXNzYWdlLFxuICAgIHNlYXJjaFByb21wdCxcbiAgICBzZWFyY2hhYmxlT3B0aW9uRmllbGRzLFxuICAgIHN0YXRlLFxuICAgIHN0YXRlUGF0aCxcbn0pIHtcbiAgICByZXR1cm4ge1xuICAgICAgICBpc1NlYXJjaGluZzogZmFsc2UsXG5cbiAgICAgICAgc2VsZWN0OiBudWxsLFxuXG4gICAgICAgIHNlbGVjdGVkT3B0aW9uczogW10sXG5cbiAgICAgICAgaXNTdGF0ZUJlaW5nVXBkYXRlZDogZmFsc2UsXG5cbiAgICAgICAgc3RhdGUsXG5cbiAgICAgICAgaW5pdDogYXN5bmMgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdGhpcy5zZWxlY3QgPSBuZXcgQ2hvaWNlcyh0aGlzLiRyZWZzLmlucHV0LCB7XG4gICAgICAgICAgICAgICAgYWxsb3dIVE1MOiBpc0h0bWxBbGxvd2VkLFxuICAgICAgICAgICAgICAgIGR1cGxpY2F0ZUl0ZW1zQWxsb3dlZDogZmFsc2UsXG4gICAgICAgICAgICAgICAgaXRlbVNlbGVjdFRleHQ6ICcnLFxuICAgICAgICAgICAgICAgIGxvYWRpbmdUZXh0OiBsb2FkaW5nTWVzc2FnZSxcbiAgICAgICAgICAgICAgICBtYXhJdGVtQ291bnQ6IG1heEl0ZW1zID8/IC0xLFxuICAgICAgICAgICAgICAgIG1heEl0ZW1UZXh0OiAobWF4SXRlbUNvdW50KSA9PlxuICAgICAgICAgICAgICAgICAgICB3aW5kb3cucGx1cmFsaXplKG1heEl0ZW1zTWVzc2FnZSwgbWF4SXRlbUNvdW50LCB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb3VudDogbWF4SXRlbUNvdW50LFxuICAgICAgICAgICAgICAgICAgICB9KSxcbiAgICAgICAgICAgICAgICBub0Nob2ljZXNUZXh0OiBzZWFyY2hQcm9tcHQsXG4gICAgICAgICAgICAgICAgbm9SZXN1bHRzVGV4dDogbm9TZWFyY2hSZXN1bHRzTWVzc2FnZSxcbiAgICAgICAgICAgICAgICBwbGFjZWhvbGRlclZhbHVlOiBwbGFjZWhvbGRlcixcbiAgICAgICAgICAgICAgICBwb3NpdGlvbjogcG9zaXRpb24gPz8gJ2F1dG8nLFxuICAgICAgICAgICAgICAgIHJlbW92ZUl0ZW1CdXR0b246IGNhblNlbGVjdFBsYWNlaG9sZGVyLFxuICAgICAgICAgICAgICAgIHJlbmRlckNob2ljZUxpbWl0OiBvcHRpb25zTGltaXQsXG4gICAgICAgICAgICAgICAgc2VhcmNoRW5hYmxlZDogaXNTZWFyY2hhYmxlLFxuICAgICAgICAgICAgICAgIHNlYXJjaEZpZWxkczogc2VhcmNoYWJsZU9wdGlvbkZpZWxkcyA/PyBbJ2xhYmVsJ10sXG4gICAgICAgICAgICAgICAgc2VhcmNoUGxhY2Vob2xkZXJWYWx1ZTogc2VhcmNoUHJvbXB0LFxuICAgICAgICAgICAgICAgIHNlYXJjaFJlc3VsdExpbWl0OiBvcHRpb25zTGltaXQsXG4gICAgICAgICAgICAgICAgc2hvdWxkU29ydDogZmFsc2UsXG4gICAgICAgICAgICAgICAgc2VhcmNoRmxvb3I6IGhhc0R5bmFtaWNTZWFyY2hSZXN1bHRzID8gMCA6IDEsXG4gICAgICAgICAgICB9KVxuXG4gICAgICAgICAgICBhd2FpdCB0aGlzLnJlZnJlc2hDaG9pY2VzKHsgd2l0aEluaXRpYWxPcHRpb25zOiB0cnVlIH0pXG5cbiAgICAgICAgICAgIGlmICghW251bGwsIHVuZGVmaW5lZCwgJyddLmluY2x1ZGVzKHRoaXMuc3RhdGUpKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3Quc2V0Q2hvaWNlQnlWYWx1ZSh0aGlzLmZvcm1hdFN0YXRlKHRoaXMuc3RhdGUpKVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLnJlZnJlc2hQbGFjZWhvbGRlcigpXG5cbiAgICAgICAgICAgIGlmIChpc0F1dG9mb2N1c2VkKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3Quc2hvd0Ryb3Bkb3duKClcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy4kcmVmcy5pbnB1dC5hZGRFdmVudExpc3RlbmVyKCdjaGFuZ2UnLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5yZWZyZXNoUGxhY2Vob2xkZXIoKVxuXG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaXNTdGF0ZUJlaW5nVXBkYXRlZCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLmlzU3RhdGVCZWluZ1VwZGF0ZWQgPSB0cnVlXG4gICAgICAgICAgICAgICAgdGhpcy5zdGF0ZSA9IHRoaXMuc2VsZWN0LmdldFZhbHVlKHRydWUpID8/IG51bGxcbiAgICAgICAgICAgICAgICB0aGlzLiRuZXh0VGljaygoKSA9PiAodGhpcy5pc1N0YXRlQmVpbmdVcGRhdGVkID0gZmFsc2UpKVxuICAgICAgICAgICAgfSlcblxuICAgICAgICAgICAgaWYgKGhhc0R5bmFtaWNPcHRpb25zKSB7XG4gICAgICAgICAgICAgICAgdGhpcy4kcmVmcy5pbnB1dC5hZGRFdmVudExpc3RlbmVyKCdzaG93RHJvcGRvd24nLCBhc3luYyAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2VsZWN0LmNsZWFyQ2hvaWNlcygpXG4gICAgICAgICAgICAgICAgICAgIGF3YWl0IHRoaXMuc2VsZWN0LnNldENob2ljZXMoW1xuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsOiBsb2FkaW5nTWVzc2FnZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZTogJycsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGlzYWJsZWQ6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBdKVxuXG4gICAgICAgICAgICAgICAgICAgIGF3YWl0IHRoaXMucmVmcmVzaENob2ljZXMoKVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChoYXNEeW5hbWljU2VhcmNoUmVzdWx0cykge1xuICAgICAgICAgICAgICAgIHRoaXMuJHJlZnMuaW5wdXQuYWRkRXZlbnRMaXN0ZW5lcignc2VhcmNoJywgYXN5bmMgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBzZWFyY2ggPSBldmVudC5kZXRhaWwudmFsdWU/LnRyaW0oKVxuXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaXNTZWFyY2hpbmcgPSB0cnVlXG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZWxlY3QuY2xlYXJDaG9pY2VzKClcbiAgICAgICAgICAgICAgICAgICAgYXdhaXQgdGhpcy5zZWxlY3Quc2V0Q2hvaWNlcyhbXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGFiZWw6IFtudWxsLCB1bmRlZmluZWQsICcnXS5pbmNsdWRlcyhzZWFyY2gpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgID8gbG9hZGluZ01lc3NhZ2VcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiBzZWFyY2hpbmdNZXNzYWdlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlOiAnJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkaXNhYmxlZDogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIF0pXG4gICAgICAgICAgICAgICAgfSlcblxuICAgICAgICAgICAgICAgIHRoaXMuJHJlZnMuaW5wdXQuYWRkRXZlbnRMaXN0ZW5lcihcbiAgICAgICAgICAgICAgICAgICAgJ3NlYXJjaCcsXG4gICAgICAgICAgICAgICAgICAgIEFscGluZS5kZWJvdW5jZShhc3luYyAoZXZlbnQpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGF3YWl0IHRoaXMucmVmcmVzaENob2ljZXMoe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlYXJjaDogZXZlbnQuZGV0YWlsLnZhbHVlPy50cmltKCksXG4gICAgICAgICAgICAgICAgICAgICAgICB9KVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmlzU2VhcmNoaW5nID0gZmFsc2VcbiAgICAgICAgICAgICAgICAgICAgfSwgc2VhcmNoRGVib3VuY2UpLFxuICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCFpc011bHRpcGxlKSB7XG4gICAgICAgICAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXG4gICAgICAgICAgICAgICAgICAgICdmaWxhbWVudC1mb3Jtczo6c2VsZWN0LnJlZnJlc2hTZWxlY3RlZE9wdGlvbkxhYmVsJyxcbiAgICAgICAgICAgICAgICAgICAgYXN5bmMgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoZXZlbnQuZGV0YWlsLmxpdmV3aXJlSWQgIT09IGxpdmV3aXJlSWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGV2ZW50LmRldGFpbC5zdGF0ZVBhdGggIT09IHN0YXRlUGF0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBhd2FpdCB0aGlzLnJlZnJlc2hDaG9pY2VzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB3aXRoSW5pdGlhbE9wdGlvbnM6ICFoYXNEeW5hbWljT3B0aW9ucyxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLiR3YXRjaCgnc3RhdGUnLCBhc3luYyAoKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKCF0aGlzLnNlbGVjdCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLnJlZnJlc2hQbGFjZWhvbGRlcigpXG5cbiAgICAgICAgICAgICAgICBpZiAodGhpcy5pc1N0YXRlQmVpbmdVcGRhdGVkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGF3YWl0IHRoaXMucmVmcmVzaENob2ljZXMoe1xuICAgICAgICAgICAgICAgICAgICB3aXRoSW5pdGlhbE9wdGlvbnM6ICFoYXNEeW5hbWljT3B0aW9ucyxcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgfSlcbiAgICAgICAgfSxcblxuICAgICAgICBkZXN0cm95OiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLnNlbGVjdC5kZXN0cm95KClcbiAgICAgICAgICAgIHRoaXMuc2VsZWN0ID0gbnVsbFxuICAgICAgICB9LFxuXG4gICAgICAgIHJlZnJlc2hDaG9pY2VzOiBhc3luYyBmdW5jdGlvbiAoY29uZmlnID0ge30pIHtcbiAgICAgICAgICAgIGNvbnN0IGNob2ljZXMgPSBhd2FpdCB0aGlzLmdldENob2ljZXMoY29uZmlnKVxuXG4gICAgICAgICAgICB0aGlzLnNlbGVjdC5jbGVhclN0b3JlKClcblxuICAgICAgICAgICAgdGhpcy5yZWZyZXNoUGxhY2Vob2xkZXIoKVxuXG4gICAgICAgICAgICB0aGlzLnNldENob2ljZXMoY2hvaWNlcylcblxuICAgICAgICAgICAgaWYgKCFbbnVsbCwgdW5kZWZpbmVkLCAnJ10uaW5jbHVkZXModGhpcy5zdGF0ZSkpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNlbGVjdC5zZXRDaG9pY2VCeVZhbHVlKHRoaXMuZm9ybWF0U3RhdGUodGhpcy5zdGF0ZSkpXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICAgICAgc2V0Q2hvaWNlczogZnVuY3Rpb24gKGNob2ljZXMpIHtcbiAgICAgICAgICAgIHRoaXMuc2VsZWN0LnNldENob2ljZXMoY2hvaWNlcywgJ3ZhbHVlJywgJ2xhYmVsJywgdHJ1ZSlcbiAgICAgICAgfSxcblxuICAgICAgICBnZXRDaG9pY2VzOiBhc3luYyBmdW5jdGlvbiAoY29uZmlnID0ge30pIHtcbiAgICAgICAgICAgIGNvbnN0IGV4aXN0aW5nT3B0aW9ucyA9IGF3YWl0IHRoaXMuZ2V0RXhpc3RpbmdPcHRpb25zKGNvbmZpZylcblxuICAgICAgICAgICAgcmV0dXJuIGV4aXN0aW5nT3B0aW9ucy5jb25jYXQoXG4gICAgICAgICAgICAgICAgYXdhaXQgdGhpcy5nZXRNaXNzaW5nT3B0aW9ucyhleGlzdGluZ09wdGlvbnMpLFxuICAgICAgICAgICAgKVxuICAgICAgICB9LFxuXG4gICAgICAgIGdldEV4aXN0aW5nT3B0aW9uczogYXN5bmMgZnVuY3Rpb24gKHsgc2VhcmNoLCB3aXRoSW5pdGlhbE9wdGlvbnMgfSkge1xuICAgICAgICAgICAgaWYgKHdpdGhJbml0aWFsT3B0aW9ucykge1xuICAgICAgICAgICAgICAgIHJldHVybiBvcHRpb25zXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGxldCByZXN1bHRzID0gW11cblxuICAgICAgICAgICAgaWYgKHNlYXJjaCAhPT0gJycgJiYgc2VhcmNoICE9PSBudWxsICYmIHNlYXJjaCAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgcmVzdWx0cyA9IGF3YWl0IGdldFNlYXJjaFJlc3VsdHNVc2luZyhzZWFyY2gpXG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHJlc3VsdHMgPSBhd2FpdCBnZXRPcHRpb25zVXNpbmcoKVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gcmVzdWx0cy5tYXAoKG9wdGlvbikgPT5cbiAgICAgICAgICAgICAgICBBcnJheS5pc0FycmF5KHRoaXMuc3RhdGUpICYmIHRoaXMuc3RhdGUuaW5jbHVkZXMob3B0aW9uLnZhbHVlKVxuICAgICAgICAgICAgICAgICAgICA/ICgob3B0aW9uKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbi5zZWxlY3RlZCA9IHRydWVcblxuICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gb3B0aW9uXG4gICAgICAgICAgICAgICAgICAgICAgfSkob3B0aW9uKVxuICAgICAgICAgICAgICAgICAgICA6IG9wdGlvbixcbiAgICAgICAgICAgIClcbiAgICAgICAgfSxcblxuICAgICAgICByZWZyZXNoUGxhY2Vob2xkZXI6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmIChpc011bHRpcGxlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuc2VsZWN0Ll9yZW5kZXJJdGVtcygpXG5cbiAgICAgICAgICAgIGlmICghW251bGwsIHVuZGVmaW5lZCwgJyddLmluY2x1ZGVzKHRoaXMuc3RhdGUpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuJGVsLnF1ZXJ5U2VsZWN0b3IoXG4gICAgICAgICAgICAgICAgJy5jaG9pY2VzX19saXN0LS1zaW5nbGUnLFxuICAgICAgICAgICAgKS5pbm5lckhUTUwgPSBgPGRpdiBjbGFzcz1cImNob2ljZXNfX3BsYWNlaG9sZGVyIGNob2ljZXNfX2l0ZW1cIj4ke1xuICAgICAgICAgICAgICAgIHBsYWNlaG9sZGVyID8/ICcnXG4gICAgICAgICAgICB9PC9kaXY+YFxuICAgICAgICB9LFxuXG4gICAgICAgIGZvcm1hdFN0YXRlOiBmdW5jdGlvbiAoc3RhdGUpIHtcbiAgICAgICAgICAgIGlmIChpc011bHRpcGxlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIChzdGF0ZSA/PyBbXSkubWFwKChpdGVtKSA9PiBpdGVtPy50b1N0cmluZygpKVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gc3RhdGU/LnRvU3RyaW5nKClcbiAgICAgICAgfSxcblxuICAgICAgICBnZXRNaXNzaW5nT3B0aW9uczogYXN5bmMgZnVuY3Rpb24gKGV4aXN0aW5nT3B0aW9ucykge1xuICAgICAgICAgICAgbGV0IHN0YXRlID0gdGhpcy5mb3JtYXRTdGF0ZSh0aGlzLnN0YXRlKVxuXG4gICAgICAgICAgICBpZiAoW251bGwsIHVuZGVmaW5lZCwgJycsIFtdLCB7fV0uaW5jbHVkZXMoc3RhdGUpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHt9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IGV4aXN0aW5nT3B0aW9uVmFsdWVzID0gbmV3IFNldChcbiAgICAgICAgICAgICAgICBleGlzdGluZ09wdGlvbnMubGVuZ3RoXG4gICAgICAgICAgICAgICAgICAgID8gZXhpc3RpbmdPcHRpb25zLm1hcCgob3B0aW9uKSA9PiBvcHRpb24udmFsdWUpXG4gICAgICAgICAgICAgICAgICAgIDogW10sXG4gICAgICAgICAgICApXG5cbiAgICAgICAgICAgIGlmIChpc011bHRpcGxlKSB7XG4gICAgICAgICAgICAgICAgaWYgKHN0YXRlLmV2ZXJ5KCh2YWx1ZSkgPT4gZXhpc3RpbmdPcHRpb25WYWx1ZXMuaGFzKHZhbHVlKSkpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHt9XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuIChhd2FpdCBnZXRPcHRpb25MYWJlbHNVc2luZygpKVxuICAgICAgICAgICAgICAgICAgICAuZmlsdGVyKChvcHRpb24pID0+ICFleGlzdGluZ09wdGlvblZhbHVlcy5oYXMob3B0aW9uLnZhbHVlKSlcbiAgICAgICAgICAgICAgICAgICAgLm1hcCgob3B0aW9uKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBvcHRpb24uc2VsZWN0ZWQgPSB0cnVlXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBvcHRpb25cbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGV4aXN0aW5nT3B0aW9uVmFsdWVzLmhhcyhzdGF0ZSkpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZXhpc3RpbmdPcHRpb25WYWx1ZXNcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIFtcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIGxhYmVsOiBhd2FpdCBnZXRPcHRpb25MYWJlbFVzaW5nKCksXG4gICAgICAgICAgICAgICAgICAgIHZhbHVlOiBzdGF0ZSxcbiAgICAgICAgICAgICAgICAgICAgc2VsZWN0ZWQ6IHRydWUsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIF1cbiAgICAgICAgfSxcbiAgICB9XG59XG4iXSwKICAibWFwcGluZ3MiOiAiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFBQTtBQUFBO0FBQ0EsS0FBQyxTQUFTLGlDQUFpQyxNQUFNLFNBQVM7QUFDekQsVUFBRyxPQUFPLFlBQVksWUFBWSxPQUFPLFdBQVc7QUFDbkQsZUFBTyxVQUFVLFFBQVE7QUFBQSxlQUNsQixPQUFPLFdBQVcsY0FBYyxPQUFPO0FBQzlDLGVBQU8sQ0FBQyxHQUFHLE9BQU87QUFBQSxlQUNYLE9BQU8sWUFBWTtBQUMxQixnQkFBUSxTQUFTLElBQUksUUFBUTtBQUFBO0FBRTdCLGFBQUssU0FBUyxJQUFJLFFBQVE7QUFBQSxJQUM1QixHQUFHLFFBQVEsV0FBVztBQUN0QjtBQUFBO0FBQUEsUUFBaUIsV0FBVztBQUNsQjtBQUNBLGNBQUksc0JBQXVCO0FBQUE7QUFBQSxZQUUvQjtBQUFBO0FBQUEsY0FDQyxTQUFTLHlCQUF5QkEsVUFBU0Msc0JBQXFCO0FBSXZFLHVCQUFPLGVBQWVELFVBQVMsY0FBZTtBQUFBLGtCQUM1QyxPQUFPO0FBQUEsZ0JBQ1QsQ0FBRTtBQUNGLGdCQUFBQSxTQUFRLGVBQWVBLFNBQVEsa0JBQWtCQSxTQUFRLGdCQUFnQkEsU0FBUSxZQUFZO0FBQzdGLG9CQUFJLGNBQWNDLHFCQUFvQixHQUFHO0FBQ3pDLG9CQUFJLFlBQVksU0FBVSxJQUFJO0FBQzVCLHNCQUFJLFFBQVEsR0FBRyxPQUNiLFFBQVEsR0FBRyxPQUNYLEtBQUssR0FBRyxJQUNSLFVBQVUsR0FBRyxTQUNiLFdBQVcsR0FBRyxVQUNkLFlBQVksR0FBRyxXQUNmLG1CQUFtQixHQUFHLGtCQUN0QixjQUFjLEdBQUcsYUFDakIsVUFBVSxHQUFHO0FBQ2YseUJBQU87QUFBQSxvQkFDTCxNQUFNLFlBQVksYUFBYTtBQUFBLG9CQUMvQjtBQUFBLG9CQUNBO0FBQUEsb0JBQ0E7QUFBQSxvQkFDQTtBQUFBLG9CQUNBO0FBQUEs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QSxVQUFJLFlBQVk7QUFDWixZQUFJQSxPQUFNLE1BQU0sQ0FBQyxVQUFVLHFCQUFxQixJQUFJLEtBQUssQ0FBQyxHQUFHO0FBQ3pELGlCQUFPLENBQUM7QUFBQSxRQUNaO0FBRUEsZ0JBQVEsTUFBTSxxQkFBcUIsR0FDOUIsT0FBTyxDQUFDLFdBQVcsQ0FBQyxxQkFBcUIsSUFBSSxPQUFPLEtBQUssQ0FBQyxFQUMxRCxJQUFJLENBQUMsV0FBVztBQUNiLGlCQUFPLFdBQVc7QUFFbEIsaUJBQU87QUFBQSxRQUNYLENBQUM7QUFBQSxNQUNUO0FBRUEsVUFBSSxxQkFBcUIsSUFBSUEsTUFBSyxHQUFHO0FBQ2pDLGVBQU87QUFBQSxNQUNYO0FBRUEsYUFBTztBQUFBLFFBQ0g7QUFBQSxVQUNJLE9BQU8sTUFBTSxvQkFBb0I7QUFBQSxVQUNqQyxPQUFPQTtBQUFBLFVBQ1AsVUFBVTtBQUFBLFFBQ2Q7QUFBQSxNQUNKO0FBQUEsSUFDSjtBQUFBLEVBQ0o7QUFDSjsiLAogICJuYW1lcyI6IFsiZXhwb3J0cyIsICJfX3dlYnBhY2tfcmVxdWlyZV9fIiwgIkNob2ljZXMiLCAiX2EiLCAiQ29udGFpbmVyIiwgIkRyb3Bkb3duIiwgIklucHV0IiwgIkxpc3QiLCAiV3JhcHBlZEVsZW1lbnQiLCAiZCIsICJiIiwgIldyYXBwZWRJbnB1dCIsICJXcmFwcGVkU2VsZWN0IiwgImNob2ljZSIsICJTdG9yZSIsICJtb2R1bGUiLCAiaXNNZXJnZWFibGVPYmplY3QiLCAiX193ZWJwYWNrX2V4cG9ydHNfXyIsICJvYmoiLCAicGF0aCIsICJub3JtIiwgInZhbHVlIiwgInNjb3JlIiwgInBhdHRlcm4iLCAicmVzdWx0IiwgIml0ZW0iLCAic2VhcmNoZXJzIiwgInF1ZXJ5IiwgInJhbmRvbVN0cmluZyIsICJzdWJzY3JpYmUiLCAiY3JlYXRlU3RvcmUiLCAiQ2hvaWNlcyIsICJvcHRpb24iLCAic3RhdGUiXQp9Cg==
