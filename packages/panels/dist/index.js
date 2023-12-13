(() => {
  // node_modules/@danharrin/alpine-mousetrap/dist/module.esm.js
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __markAsModule = (target) => __defProp(target, "__esModule", { value: true });
  var __commonJS = (callback, module) => () => {
    if (!module) {
      module = { exports: {} };
      callback(module.exports, module);
    }
    return module.exports;
  };
  var __exportStar = (target, module, desc) => {
    if (module && typeof module === "object" || typeof module === "function") {
      for (let key of __getOwnPropNames(module))
        if (!__hasOwnProp.call(target, key) && key !== "default")
          __defProp(target, key, { get: () => module[key], enumerable: !(desc = __getOwnPropDesc(module, key)) || desc.enumerable });
    }
    return target;
  };
  var __toModule = (module) => {
    return __exportStar(__markAsModule(__defProp(module != null ? __create(__getProtoOf(module)) : {}, "default", module && module.__esModule && "default" in module ? { get: () => module.default, enumerable: true } : { value: module, enumerable: true })), module);
  };
  var require_mousetrap = __commonJS((exports, module) => {
    (function(window2, document2, undefined2) {
      if (!window2) {
        return;
      }
      var _MAP = {
        8: "backspace",
        9: "tab",
        13: "enter",
        16: "shift",
        17: "ctrl",
        18: "alt",
        20: "capslock",
        27: "esc",
        32: "space",
        33: "pageup",
        34: "pagedown",
        35: "end",
        36: "home",
        37: "left",
        38: "up",
        39: "right",
        40: "down",
        45: "ins",
        46: "del",
        91: "meta",
        93: "meta",
        224: "meta"
      };
      var _KEYCODE_MAP = {
        106: "*",
        107: "+",
        109: "-",
        110: ".",
        111: "/",
        186: ";",
        187: "=",
        188: ",",
        189: "-",
        190: ".",
        191: "/",
        192: "`",
        219: "[",
        220: "\\",
        221: "]",
        222: "'"
      };
      var _SHIFT_MAP = {
        "~": "`",
        "!": "1",
        "@": "2",
        "#": "3",
        $: "4",
        "%": "5",
        "^": "6",
        "&": "7",
        "*": "8",
        "(": "9",
        ")": "0",
        _: "-",
        "+": "=",
        ":": ";",
        '"': "'",
        "<": ",",
        ">": ".",
        "?": "/",
        "|": "\\"
      };
      var _SPECIAL_ALIASES = {
        option: "alt",
        command: "meta",
        return: "enter",
        escape: "esc",
        plus: "+",
        mod: /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? "meta" : "ctrl"
      };
      var _REVERSE_MAP;
      for (var i = 1; i < 20; ++i) {
        _MAP[111 + i] = "f" + i;
      }
      for (i = 0; i <= 9; ++i) {
        _MAP[i + 96] = i.toString();
      }
      function _addEvent(object, type, callback) {
        if (object.addEventListener) {
          object.addEventListener(type, callback, false);
          return;
        }
        object.attachEvent("on" + type, callback);
      }
      function _characterFromEvent(e) {
        if (e.type == "keypress") {
          var character = String.fromCharCode(e.which);
          if (!e.shiftKey) {
            character = character.toLowerCase();
          }
          return character;
        }
        if (_MAP[e.which]) {
          return _MAP[e.which];
        }
        if (_KEYCODE_MAP[e.which]) {
          return _KEYCODE_MAP[e.which];
        }
        return String.fromCharCode(e.which).toLowerCase();
      }
      function _modifiersMatch(modifiers1, modifiers2) {
        return modifiers1.sort().join(",") === modifiers2.sort().join(",");
      }
      function _eventModifiers(e) {
        var modifiers = [];
        if (e.shiftKey) {
          modifiers.push("shift");
        }
        if (e.altKey) {
          modifiers.push("alt");
        }
        if (e.ctrlKey) {
          modifiers.push("ctrl");
        }
        if (e.metaKey) {
          modifiers.push("meta");
        }
        return modifiers;
      }
      function _preventDefault(e) {
        if (e.preventDefault) {
          e.preventDefault();
          return;
        }
        e.returnValue = false;
      }
      function _stopPropagation(e) {
        if (e.stopPropagation) {
          e.stopPropagation();
          return;
        }
        e.cancelBubble = true;
      }
      function _isModifier(key) {
        return key == "shift" || key == "ctrl" || key == "alt" || key == "meta";
      }
      function _getReverseMap() {
        if (!_REVERSE_MAP) {
          _REVERSE_MAP = {};
          for (var key in _MAP) {
            if (key > 95 && key < 112) {
              continue;
            }
            if (_MAP.hasOwnProperty(key)) {
              _REVERSE_MAP[_MAP[key]] = key;
            }
          }
        }
        return _REVERSE_MAP;
      }
      function _pickBestAction(key, modifiers, action) {
        if (!action) {
          action = _getReverseMap()[key] ? "keydown" : "keypress";
        }
        if (action == "keypress" && modifiers.length) {
          action = "keydown";
        }
        return action;
      }
      function _keysFromString(combination) {
        if (combination === "+") {
          return ["+"];
        }
        combination = combination.replace(/\+{2}/g, "+plus");
        return combination.split("+");
      }
      function _getKeyInfo(combination, action) {
        var keys;
        var key;
        var i2;
        var modifiers = [];
        keys = _keysFromString(combination);
        for (i2 = 0; i2 < keys.length; ++i2) {
          key = keys[i2];
          if (_SPECIAL_ALIASES[key]) {
            key = _SPECIAL_ALIASES[key];
          }
          if (action && action != "keypress" && _SHIFT_MAP[key]) {
            key = _SHIFT_MAP[key];
            modifiers.push("shift");
          }
          if (_isModifier(key)) {
            modifiers.push(key);
          }
        }
        action = _pickBestAction(key, modifiers, action);
        return {
          key,
          modifiers,
          action
        };
      }
      function _belongsTo(element, ancestor) {
        if (element === null || element === document2) {
          return false;
        }
        if (element === ancestor) {
          return true;
        }
        return _belongsTo(element.parentNode, ancestor);
      }
      function Mousetrap3(targetElement) {
        var self = this;
        targetElement = targetElement || document2;
        if (!(self instanceof Mousetrap3)) {
          return new Mousetrap3(targetElement);
        }
        self.target = targetElement;
        self._callbacks = {};
        self._directMap = {};
        var _sequenceLevels = {};
        var _resetTimer;
        var _ignoreNextKeyup = false;
        var _ignoreNextKeypress = false;
        var _nextExpectedAction = false;
        function _resetSequences(doNotReset) {
          doNotReset = doNotReset || {};
          var activeSequences = false, key;
          for (key in _sequenceLevels) {
            if (doNotReset[key]) {
              activeSequences = true;
              continue;
            }
            _sequenceLevels[key] = 0;
          }
          if (!activeSequences) {
            _nextExpectedAction = false;
          }
        }
        function _getMatches(character, modifiers, e, sequenceName, combination, level) {
          var i2;
          var callback;
          var matches = [];
          var action = e.type;
          if (!self._callbacks[character]) {
            return [];
          }
          if (action == "keyup" && _isModifier(character)) {
            modifiers = [character];
          }
          for (i2 = 0; i2 < self._callbacks[character].length; ++i2) {
            callback = self._callbacks[character][i2];
            if (!sequenceName && callback.seq && _sequenceLevels[callback.seq] != callback.level) {
              continue;
            }
            if (action != callback.action) {
              continue;
            }
            if (action == "keypress" && !e.metaKey && !e.ctrlKey || _modifiersMatch(modifiers, callback.modifiers)) {
              var deleteCombo = !sequenceName && callback.combo == combination;
              var deleteSequence = sequenceName && callback.seq == sequenceName && callback.level == level;
              if (deleteCombo || deleteSequence) {
                self._callbacks[character].splice(i2, 1);
              }
              matches.push(callback);
            }
          }
          return matches;
        }
        function _fireCallback(callback, e, combo, sequence) {
          if (self.stopCallback(e, e.target || e.srcElement, combo, sequence)) {
            return;
          }
          if (callback(e, combo) === false) {
            _preventDefault(e);
            _stopPropagation(e);
          }
        }
        self._handleKey = function(character, modifiers, e) {
          var callbacks = _getMatches(character, modifiers, e);
          var i2;
          var doNotReset = {};
          var maxLevel = 0;
          var processedSequenceCallback = false;
          for (i2 = 0; i2 < callbacks.length; ++i2) {
            if (callbacks[i2].seq) {
              maxLevel = Math.max(maxLevel, callbacks[i2].level);
            }
          }
          for (i2 = 0; i2 < callbacks.length; ++i2) {
            if (callbacks[i2].seq) {
              if (callbacks[i2].level != maxLevel) {
                continue;
              }
              processedSequenceCallback = true;
              doNotReset[callbacks[i2].seq] = 1;
              _fireCallback(callbacks[i2].callback, e, callbacks[i2].combo, callbacks[i2].seq);
              continue;
            }
            if (!processedSequenceCallback) {
              _fireCallback(callbacks[i2].callback, e, callbacks[i2].combo);
            }
          }
          var ignoreThisKeypress = e.type == "keypress" && _ignoreNextKeypress;
          if (e.type == _nextExpectedAction && !_isModifier(character) && !ignoreThisKeypress) {
            _resetSequences(doNotReset);
          }
          _ignoreNextKeypress = processedSequenceCallback && e.type == "keydown";
        };
        function _handleKeyEvent(e) {
          if (typeof e.which !== "number") {
            e.which = e.keyCode;
          }
          var character = _characterFromEvent(e);
          if (!character) {
            return;
          }
          if (e.type == "keyup" && _ignoreNextKeyup === character) {
            _ignoreNextKeyup = false;
            return;
          }
          self.handleKey(character, _eventModifiers(e), e);
        }
        function _resetSequenceTimer() {
          clearTimeout(_resetTimer);
          _resetTimer = setTimeout(_resetSequences, 1e3);
        }
        function _bindSequence(combo, keys, callback, action) {
          _sequenceLevels[combo] = 0;
          function _increaseSequence(nextAction) {
            return function() {
              _nextExpectedAction = nextAction;
              ++_sequenceLevels[combo];
              _resetSequenceTimer();
            };
          }
          function _callbackAndReset(e) {
            _fireCallback(callback, e, combo);
            if (action !== "keyup") {
              _ignoreNextKeyup = _characterFromEvent(e);
            }
            setTimeout(_resetSequences, 10);
          }
          for (var i2 = 0; i2 < keys.length; ++i2) {
            var isFinal = i2 + 1 === keys.length;
            var wrappedCallback = isFinal ? _callbackAndReset : _increaseSequence(action || _getKeyInfo(keys[i2 + 1]).action);
            _bindSingle(keys[i2], wrappedCallback, action, combo, i2);
          }
        }
        function _bindSingle(combination, callback, action, sequenceName, level) {
          self._directMap[combination + ":" + action] = callback;
          combination = combination.replace(/\s+/g, " ");
          var sequence = combination.split(" ");
          var info;
          if (sequence.length > 1) {
            _bindSequence(combination, sequence, callback, action);
            return;
          }
          info = _getKeyInfo(combination, action);
          self._callbacks[info.key] = self._callbacks[info.key] || [];
          _getMatches(info.key, info.modifiers, { type: info.action }, sequenceName, combination, level);
          self._callbacks[info.key][sequenceName ? "unshift" : "push"]({
            callback,
            modifiers: info.modifiers,
            action: info.action,
            seq: sequenceName,
            level,
            combo: combination
          });
        }
        self._bindMultiple = function(combinations, callback, action) {
          for (var i2 = 0; i2 < combinations.length; ++i2) {
            _bindSingle(combinations[i2], callback, action);
          }
        };
        _addEvent(targetElement, "keypress", _handleKeyEvent);
        _addEvent(targetElement, "keydown", _handleKeyEvent);
        _addEvent(targetElement, "keyup", _handleKeyEvent);
      }
      Mousetrap3.prototype.bind = function(keys, callback, action) {
        var self = this;
        keys = keys instanceof Array ? keys : [keys];
        self._bindMultiple.call(self, keys, callback, action);
        return self;
      };
      Mousetrap3.prototype.unbind = function(keys, action) {
        var self = this;
        return self.bind.call(self, keys, function() {
        }, action);
      };
      Mousetrap3.prototype.trigger = function(keys, action) {
        var self = this;
        if (self._directMap[keys + ":" + action]) {
          self._directMap[keys + ":" + action]({}, keys);
        }
        return self;
      };
      Mousetrap3.prototype.reset = function() {
        var self = this;
        self._callbacks = {};
        self._directMap = {};
        return self;
      };
      Mousetrap3.prototype.stopCallback = function(e, element) {
        var self = this;
        if ((" " + element.className + " ").indexOf(" mousetrap ") > -1) {
          return false;
        }
        if (_belongsTo(element, self.target)) {
          return false;
        }
        if ("composedPath" in e && typeof e.composedPath === "function") {
          var initialEventTarget = e.composedPath()[0];
          if (initialEventTarget !== e.target) {
            element = initialEventTarget;
          }
        }
        return element.tagName == "INPUT" || element.tagName == "SELECT" || element.tagName == "TEXTAREA" || element.isContentEditable;
      };
      Mousetrap3.prototype.handleKey = function() {
        var self = this;
        return self._handleKey.apply(self, arguments);
      };
      Mousetrap3.addKeycodes = function(object) {
        for (var key in object) {
          if (object.hasOwnProperty(key)) {
            _MAP[key] = object[key];
          }
        }
        _REVERSE_MAP = null;
      };
      Mousetrap3.init = function() {
        var documentMousetrap = Mousetrap3(document2);
        for (var method in documentMousetrap) {
          if (method.charAt(0) !== "_") {
            Mousetrap3[method] = function(method2) {
              return function() {
                return documentMousetrap[method2].apply(documentMousetrap, arguments);
              };
            }(method);
          }
        }
      };
      Mousetrap3.init();
      window2.Mousetrap = Mousetrap3;
      if (typeof module !== "undefined" && module.exports) {
        module.exports = Mousetrap3;
      }
      if (typeof define === "function" && define.amd) {
        define(function() {
          return Mousetrap3;
        });
      }
    })(typeof window !== "undefined" ? window : null, typeof window !== "undefined" ? document : null);
  });
  var import_mousetrap = __toModule(require_mousetrap());
  (function(Mousetrap3) {
    if (!Mousetrap3) {
      return;
    }
    var _globalCallbacks = {};
    var _originalStopCallback = Mousetrap3.prototype.stopCallback;
    Mousetrap3.prototype.stopCallback = function(e, element, combo, sequence) {
      var self = this;
      if (self.paused) {
        return true;
      }
      if (_globalCallbacks[combo] || _globalCallbacks[sequence]) {
        return false;
      }
      return _originalStopCallback.call(self, e, element, combo);
    };
    Mousetrap3.prototype.bindGlobal = function(keys, callback, action) {
      var self = this;
      self.bind(keys, callback, action);
      if (keys instanceof Array) {
        for (var i = 0; i < keys.length; i++) {
          _globalCallbacks[keys[i]] = true;
        }
        return;
      }
      _globalCallbacks[keys] = true;
    };
    Mousetrap3.init();
  })(typeof Mousetrap !== "undefined" ? Mousetrap : void 0);
  var src_default = (Alpine) => {
    Alpine.directive("mousetrap", (el, { modifiers, expression }, { evaluate }) => {
      const action = () => expression ? evaluate(expression) : el.click();
      modifiers = modifiers.map((modifier) => modifier.replace("-", "+"));
      if (modifiers.includes("global")) {
        modifiers = modifiers.filter((modifier) => modifier !== "global");
        import_mousetrap.default.bindGlobal(modifiers, ($event) => {
          $event.preventDefault();
          action();
        });
      }
      import_mousetrap.default.bind(modifiers, ($event) => {
        $event.preventDefault();
        action();
      });
    });
  };
  var module_default = src_default;

  // packages/panels/resources/js/index.js
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(module_default);
    window.Alpine.store("sidebar", {
      isOpen: window.Alpine.$persist(true).as("isOpen"),
      collapsedGroups: window.Alpine.$persist(null).as("collapsedGroups"),
      groupIsCollapsed: function(group) {
        return this.collapsedGroups.includes(group);
      },
      collapseGroup: function(group) {
        if (this.collapsedGroups.includes(group)) {
          return;
        }
        this.collapsedGroups = this.collapsedGroups.concat(group);
      },
      toggleCollapsedGroup: function(group) {
        this.collapsedGroups = this.collapsedGroups.includes(group) ? this.collapsedGroups.filter(
          (collapsedGroup) => collapsedGroup !== group
        ) : this.collapsedGroups.concat(group);
      },
      close: function() {
        this.isOpen = false;
      },
      open: function() {
        this.isOpen = true;
      }
    });
    const theme = localStorage.getItem("theme") ?? "system";
    window.Alpine.store(
      "theme",
      theme === "dark" || theme === "system" && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
    );
    window.addEventListener("theme-changed", (event) => {
      let theme2 = event.detail;
      localStorage.setItem("theme", theme2);
      if (theme2 === "system") {
        theme2 = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
      }
      window.Alpine.store("theme", theme2);
    });
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", (event) => {
      if (localStorage.getItem("theme") === "system") {
        window.Alpine.store("theme", event.matches ? "dark" : "light");
      }
    });
    window.Alpine.effect(() => {
      const theme2 = window.Alpine.store("theme");
      theme2 === "dark" ? document.documentElement.classList.add("dark") : document.documentElement.classList.remove("dark");
    });
  });
})();
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0BkYW5oYXJyaW4vYWxwaW5lLW1vdXNldHJhcC9kaXN0L21vZHVsZS5lc20uanMiLCAiLi4vcmVzb3VyY2VzL2pzL2luZGV4LmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyJ2YXIgX19jcmVhdGUgPSBPYmplY3QuY3JlYXRlO1xudmFyIF9fZGVmUHJvcCA9IE9iamVjdC5kZWZpbmVQcm9wZXJ0eTtcbnZhciBfX2dldFByb3RvT2YgPSBPYmplY3QuZ2V0UHJvdG90eXBlT2Y7XG52YXIgX19oYXNPd25Qcm9wID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eTtcbnZhciBfX2dldE93blByb3BOYW1lcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzO1xudmFyIF9fZ2V0T3duUHJvcERlc2MgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yO1xudmFyIF9fbWFya0FzTW9kdWxlID0gKHRhcmdldCkgPT4gX19kZWZQcm9wKHRhcmdldCwgXCJfX2VzTW9kdWxlXCIsIHt2YWx1ZTogdHJ1ZX0pO1xudmFyIF9fY29tbW9uSlMgPSAoY2FsbGJhY2ssIG1vZHVsZSkgPT4gKCkgPT4ge1xuICBpZiAoIW1vZHVsZSkge1xuICAgIG1vZHVsZSA9IHtleHBvcnRzOiB7fX07XG4gICAgY2FsbGJhY2sobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSk7XG4gIH1cbiAgcmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufTtcbnZhciBfX2V4cG9ydFN0YXIgPSAodGFyZ2V0LCBtb2R1bGUsIGRlc2MpID0+IHtcbiAgaWYgKG1vZHVsZSAmJiB0eXBlb2YgbW9kdWxlID09PSBcIm9iamVjdFwiIHx8IHR5cGVvZiBtb2R1bGUgPT09IFwiZnVuY3Rpb25cIikge1xuICAgIGZvciAobGV0IGtleSBvZiBfX2dldE93blByb3BOYW1lcyhtb2R1bGUpKVxuICAgICAgaWYgKCFfX2hhc093blByb3AuY2FsbCh0YXJnZXQsIGtleSkgJiYga2V5ICE9PSBcImRlZmF1bHRcIilcbiAgICAgICAgX19kZWZQcm9wKHRhcmdldCwga2V5LCB7Z2V0OiAoKSA9PiBtb2R1bGVba2V5XSwgZW51bWVyYWJsZTogIShkZXNjID0gX19nZXRPd25Qcm9wRGVzYyhtb2R1bGUsIGtleSkpIHx8IGRlc2MuZW51bWVyYWJsZX0pO1xuICB9XG4gIHJldHVybiB0YXJnZXQ7XG59O1xudmFyIF9fdG9Nb2R1bGUgPSAobW9kdWxlKSA9PiB7XG4gIHJldHVybiBfX2V4cG9ydFN0YXIoX19tYXJrQXNNb2R1bGUoX19kZWZQcm9wKG1vZHVsZSAhPSBudWxsID8gX19jcmVhdGUoX19nZXRQcm90b09mKG1vZHVsZSkpIDoge30sIFwiZGVmYXVsdFwiLCBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgJiYgXCJkZWZhdWx0XCIgaW4gbW9kdWxlID8ge2dldDogKCkgPT4gbW9kdWxlLmRlZmF1bHQsIGVudW1lcmFibGU6IHRydWV9IDoge3ZhbHVlOiBtb2R1bGUsIGVudW1lcmFibGU6IHRydWV9KSksIG1vZHVsZSk7XG59O1xuXG4vLyBub2RlX21vZHVsZXMvbW91c2V0cmFwL21vdXNldHJhcC5qc1xudmFyIHJlcXVpcmVfbW91c2V0cmFwID0gX19jb21tb25KUygoZXhwb3J0cywgbW9kdWxlKSA9PiB7XG4gIChmdW5jdGlvbih3aW5kb3cyLCBkb2N1bWVudDIsIHVuZGVmaW5lZDIpIHtcbiAgICBpZiAoIXdpbmRvdzIpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdmFyIF9NQVAgPSB7XG4gICAgICA4OiBcImJhY2tzcGFjZVwiLFxuICAgICAgOTogXCJ0YWJcIixcbiAgICAgIDEzOiBcImVudGVyXCIsXG4gICAgICAxNjogXCJzaGlmdFwiLFxuICAgICAgMTc6IFwiY3RybFwiLFxuICAgICAgMTg6IFwiYWx0XCIsXG4gICAgICAyMDogXCJjYXBzbG9ja1wiLFxuICAgICAgMjc6IFwiZXNjXCIsXG4gICAgICAzMjogXCJzcGFjZVwiLFxuICAgICAgMzM6IFwicGFnZXVwXCIsXG4gICAgICAzNDogXCJwYWdlZG93blwiLFxuICAgICAgMzU6IFwiZW5kXCIsXG4gICAgICAzNjogXCJob21lXCIsXG4gICAgICAzNzogXCJsZWZ0XCIsXG4gICAgICAzODogXCJ1cFwiLFxuICAgICAgMzk6IFwicmlnaHRcIixcbiAgICAgIDQwOiBcImRvd25cIixcbiAgICAgIDQ1OiBcImluc1wiLFxuICAgICAgNDY6IFwiZGVsXCIsXG4gICAgICA5MTogXCJtZXRhXCIsXG4gICAgICA5MzogXCJtZXRhXCIsXG4gICAgICAyMjQ6IFwibWV0YVwiXG4gICAgfTtcbiAgICB2YXIgX0tFWUNPREVfTUFQID0ge1xuICAgICAgMTA2OiBcIipcIixcbiAgICAgIDEwNzogXCIrXCIsXG4gICAgICAxMDk6IFwiLVwiLFxuICAgICAgMTEwOiBcIi5cIixcbiAgICAgIDExMTogXCIvXCIsXG4gICAgICAxODY6IFwiO1wiLFxuICAgICAgMTg3OiBcIj1cIixcbiAgICAgIDE4ODogXCIsXCIsXG4gICAgICAxODk6IFwiLVwiLFxuICAgICAgMTkwOiBcIi5cIixcbiAgICAgIDE5MTogXCIvXCIsXG4gICAgICAxOTI6IFwiYFwiLFxuICAgICAgMjE5OiBcIltcIixcbiAgICAgIDIyMDogXCJcXFxcXCIsXG4gICAgICAyMjE6IFwiXVwiLFxuICAgICAgMjIyOiBcIidcIlxuICAgIH07XG4gICAgdmFyIF9TSElGVF9NQVAgPSB7XG4gICAgICBcIn5cIjogXCJgXCIsXG4gICAgICBcIiFcIjogXCIxXCIsXG4gICAgICBcIkBcIjogXCIyXCIsXG4gICAgICBcIiNcIjogXCIzXCIsXG4gICAgICAkOiBcIjRcIixcbiAgICAgIFwiJVwiOiBcIjVcIixcbiAgICAgIFwiXlwiOiBcIjZcIixcbiAgICAgIFwiJlwiOiBcIjdcIixcbiAgICAgIFwiKlwiOiBcIjhcIixcbiAgICAgIFwiKFwiOiBcIjlcIixcbiAgICAgIFwiKVwiOiBcIjBcIixcbiAgICAgIF86IFwiLVwiLFxuICAgICAgXCIrXCI6IFwiPVwiLFxuICAgICAgXCI6XCI6IFwiO1wiLFxuICAgICAgJ1wiJzogXCInXCIsXG4gICAgICBcIjxcIjogXCIsXCIsXG4gICAgICBcIj5cIjogXCIuXCIsXG4gICAgICBcIj9cIjogXCIvXCIsXG4gICAgICBcInxcIjogXCJcXFxcXCJcbiAgICB9O1xuICAgIHZhciBfU1BFQ0lBTF9BTElBU0VTID0ge1xuICAgICAgb3B0aW9uOiBcImFsdFwiLFxuICAgICAgY29tbWFuZDogXCJtZXRhXCIsXG4gICAgICByZXR1cm46IFwiZW50ZXJcIixcbiAgICAgIGVzY2FwZTogXCJlc2NcIixcbiAgICAgIHBsdXM6IFwiK1wiLFxuICAgICAgbW9kOiAvTWFjfGlQb2R8aVBob25lfGlQYWQvLnRlc3QobmF2aWdhdG9yLnBsYXRmb3JtKSA/IFwibWV0YVwiIDogXCJjdHJsXCJcbiAgICB9O1xuICAgIHZhciBfUkVWRVJTRV9NQVA7XG4gICAgZm9yICh2YXIgaSA9IDE7IGkgPCAyMDsgKytpKSB7XG4gICAgICBfTUFQWzExMSArIGldID0gXCJmXCIgKyBpO1xuICAgIH1cbiAgICBmb3IgKGkgPSAwOyBpIDw9IDk7ICsraSkge1xuICAgICAgX01BUFtpICsgOTZdID0gaS50b1N0cmluZygpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfYWRkRXZlbnQob2JqZWN0LCB0eXBlLCBjYWxsYmFjaykge1xuICAgICAgaWYgKG9iamVjdC5hZGRFdmVudExpc3RlbmVyKSB7XG4gICAgICAgIG9iamVjdC5hZGRFdmVudExpc3RlbmVyKHR5cGUsIGNhbGxiYWNrLCBmYWxzZSk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIG9iamVjdC5hdHRhY2hFdmVudChcIm9uXCIgKyB0eXBlLCBjYWxsYmFjayk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9jaGFyYWN0ZXJGcm9tRXZlbnQoZSkge1xuICAgICAgaWYgKGUudHlwZSA9PSBcImtleXByZXNzXCIpIHtcbiAgICAgICAgdmFyIGNoYXJhY3RlciA9IFN0cmluZy5mcm9tQ2hhckNvZGUoZS53aGljaCk7XG4gICAgICAgIGlmICghZS5zaGlmdEtleSkge1xuICAgICAgICAgIGNoYXJhY3RlciA9IGNoYXJhY3Rlci50b0xvd2VyQ2FzZSgpO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBjaGFyYWN0ZXI7XG4gICAgICB9XG4gICAgICBpZiAoX01BUFtlLndoaWNoXSkge1xuICAgICAgICByZXR1cm4gX01BUFtlLndoaWNoXTtcbiAgICAgIH1cbiAgICAgIGlmIChfS0VZQ09ERV9NQVBbZS53aGljaF0pIHtcbiAgICAgICAgcmV0dXJuIF9LRVlDT0RFX01BUFtlLndoaWNoXTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBTdHJpbmcuZnJvbUNoYXJDb2RlKGUud2hpY2gpLnRvTG93ZXJDYXNlKCk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9tb2RpZmllcnNNYXRjaChtb2RpZmllcnMxLCBtb2RpZmllcnMyKSB7XG4gICAgICByZXR1cm4gbW9kaWZpZXJzMS5zb3J0KCkuam9pbihcIixcIikgPT09IG1vZGlmaWVyczIuc29ydCgpLmpvaW4oXCIsXCIpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfZXZlbnRNb2RpZmllcnMoZSkge1xuICAgICAgdmFyIG1vZGlmaWVycyA9IFtdO1xuICAgICAgaWYgKGUuc2hpZnRLZXkpIHtcbiAgICAgICAgbW9kaWZpZXJzLnB1c2goXCJzaGlmdFwiKTtcbiAgICAgIH1cbiAgICAgIGlmIChlLmFsdEtleSkge1xuICAgICAgICBtb2RpZmllcnMucHVzaChcImFsdFwiKTtcbiAgICAgIH1cbiAgICAgIGlmIChlLmN0cmxLZXkpIHtcbiAgICAgICAgbW9kaWZpZXJzLnB1c2goXCJjdHJsXCIpO1xuICAgICAgfVxuICAgICAgaWYgKGUubWV0YUtleSkge1xuICAgICAgICBtb2RpZmllcnMucHVzaChcIm1ldGFcIik7XG4gICAgICB9XG4gICAgICByZXR1cm4gbW9kaWZpZXJzO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfcHJldmVudERlZmF1bHQoZSkge1xuICAgICAgaWYgKGUucHJldmVudERlZmF1bHQpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBlLnJldHVyblZhbHVlID0gZmFsc2U7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9zdG9wUHJvcGFnYXRpb24oZSkge1xuICAgICAgaWYgKGUuc3RvcFByb3BhZ2F0aW9uKSB7XG4gICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGUuY2FuY2VsQnViYmxlID0gdHJ1ZTtcbiAgICB9XG4gICAgZnVuY3Rpb24gX2lzTW9kaWZpZXIoa2V5KSB7XG4gICAgICByZXR1cm4ga2V5ID09IFwic2hpZnRcIiB8fCBrZXkgPT0gXCJjdHJsXCIgfHwga2V5ID09IFwiYWx0XCIgfHwga2V5ID09IFwibWV0YVwiO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfZ2V0UmV2ZXJzZU1hcCgpIHtcbiAgICAgIGlmICghX1JFVkVSU0VfTUFQKSB7XG4gICAgICAgIF9SRVZFUlNFX01BUCA9IHt9O1xuICAgICAgICBmb3IgKHZhciBrZXkgaW4gX01BUCkge1xuICAgICAgICAgIGlmIChrZXkgPiA5NSAmJiBrZXkgPCAxMTIpIHtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAoX01BUC5oYXNPd25Qcm9wZXJ0eShrZXkpKSB7XG4gICAgICAgICAgICBfUkVWRVJTRV9NQVBbX01BUFtrZXldXSA9IGtleTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiBfUkVWRVJTRV9NQVA7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9waWNrQmVzdEFjdGlvbihrZXksIG1vZGlmaWVycywgYWN0aW9uKSB7XG4gICAgICBpZiAoIWFjdGlvbikge1xuICAgICAgICBhY3Rpb24gPSBfZ2V0UmV2ZXJzZU1hcCgpW2tleV0gPyBcImtleWRvd25cIiA6IFwia2V5cHJlc3NcIjtcbiAgICAgIH1cbiAgICAgIGlmIChhY3Rpb24gPT0gXCJrZXlwcmVzc1wiICYmIG1vZGlmaWVycy5sZW5ndGgpIHtcbiAgICAgICAgYWN0aW9uID0gXCJrZXlkb3duXCI7XG4gICAgICB9XG4gICAgICByZXR1cm4gYWN0aW9uO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfa2V5c0Zyb21TdHJpbmcoY29tYmluYXRpb24pIHtcbiAgICAgIGlmIChjb21iaW5hdGlvbiA9PT0gXCIrXCIpIHtcbiAgICAgICAgcmV0dXJuIFtcIitcIl07XG4gICAgICB9XG4gICAgICBjb21iaW5hdGlvbiA9IGNvbWJpbmF0aW9uLnJlcGxhY2UoL1xcK3syfS9nLCBcIitwbHVzXCIpO1xuICAgICAgcmV0dXJuIGNvbWJpbmF0aW9uLnNwbGl0KFwiK1wiKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gX2dldEtleUluZm8oY29tYmluYXRpb24sIGFjdGlvbikge1xuICAgICAgdmFyIGtleXM7XG4gICAgICB2YXIga2V5O1xuICAgICAgdmFyIGkyO1xuICAgICAgdmFyIG1vZGlmaWVycyA9IFtdO1xuICAgICAga2V5cyA9IF9rZXlzRnJvbVN0cmluZyhjb21iaW5hdGlvbik7XG4gICAgICBmb3IgKGkyID0gMDsgaTIgPCBrZXlzLmxlbmd0aDsgKytpMikge1xuICAgICAgICBrZXkgPSBrZXlzW2kyXTtcbiAgICAgICAgaWYgKF9TUEVDSUFMX0FMSUFTRVNba2V5XSkge1xuICAgICAgICAgIGtleSA9IF9TUEVDSUFMX0FMSUFTRVNba2V5XTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoYWN0aW9uICYmIGFjdGlvbiAhPSBcImtleXByZXNzXCIgJiYgX1NISUZUX01BUFtrZXldKSB7XG4gICAgICAgICAga2V5ID0gX1NISUZUX01BUFtrZXldO1xuICAgICAgICAgIG1vZGlmaWVycy5wdXNoKFwic2hpZnRcIik7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKF9pc01vZGlmaWVyKGtleSkpIHtcbiAgICAgICAgICBtb2RpZmllcnMucHVzaChrZXkpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBhY3Rpb24gPSBfcGlja0Jlc3RBY3Rpb24oa2V5LCBtb2RpZmllcnMsIGFjdGlvbik7XG4gICAgICByZXR1cm4ge1xuICAgICAgICBrZXksXG4gICAgICAgIG1vZGlmaWVycyxcbiAgICAgICAgYWN0aW9uXG4gICAgICB9O1xuICAgIH1cbiAgICBmdW5jdGlvbiBfYmVsb25nc1RvKGVsZW1lbnQsIGFuY2VzdG9yKSB7XG4gICAgICBpZiAoZWxlbWVudCA9PT0gbnVsbCB8fCBlbGVtZW50ID09PSBkb2N1bWVudDIpIHtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuICAgICAgaWYgKGVsZW1lbnQgPT09IGFuY2VzdG9yKSB7XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfVxuICAgICAgcmV0dXJuIF9iZWxvbmdzVG8oZWxlbWVudC5wYXJlbnROb2RlLCBhbmNlc3Rvcik7XG4gICAgfVxuICAgIGZ1bmN0aW9uIE1vdXNldHJhcDModGFyZ2V0RWxlbWVudCkge1xuICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgICAgdGFyZ2V0RWxlbWVudCA9IHRhcmdldEVsZW1lbnQgfHwgZG9jdW1lbnQyO1xuICAgICAgaWYgKCEoc2VsZiBpbnN0YW5jZW9mIE1vdXNldHJhcDMpKSB7XG4gICAgICAgIHJldHVybiBuZXcgTW91c2V0cmFwMyh0YXJnZXRFbGVtZW50KTtcbiAgICAgIH1cbiAgICAgIHNlbGYudGFyZ2V0ID0gdGFyZ2V0RWxlbWVudDtcbiAgICAgIHNlbGYuX2NhbGxiYWNrcyA9IHt9O1xuICAgICAgc2VsZi5fZGlyZWN0TWFwID0ge307XG4gICAgICB2YXIgX3NlcXVlbmNlTGV2ZWxzID0ge307XG4gICAgICB2YXIgX3Jlc2V0VGltZXI7XG4gICAgICB2YXIgX2lnbm9yZU5leHRLZXl1cCA9IGZhbHNlO1xuICAgICAgdmFyIF9pZ25vcmVOZXh0S2V5cHJlc3MgPSBmYWxzZTtcbiAgICAgIHZhciBfbmV4dEV4cGVjdGVkQWN0aW9uID0gZmFsc2U7XG4gICAgICBmdW5jdGlvbiBfcmVzZXRTZXF1ZW5jZXMoZG9Ob3RSZXNldCkge1xuICAgICAgICBkb05vdFJlc2V0ID0gZG9Ob3RSZXNldCB8fCB7fTtcbiAgICAgICAgdmFyIGFjdGl2ZVNlcXVlbmNlcyA9IGZhbHNlLCBrZXk7XG4gICAgICAgIGZvciAoa2V5IGluIF9zZXF1ZW5jZUxldmVscykge1xuICAgICAgICAgIGlmIChkb05vdFJlc2V0W2tleV0pIHtcbiAgICAgICAgICAgIGFjdGl2ZVNlcXVlbmNlcyA9IHRydWU7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICB9XG4gICAgICAgICAgX3NlcXVlbmNlTGV2ZWxzW2tleV0gPSAwO1xuICAgICAgICB9XG4gICAgICAgIGlmICghYWN0aXZlU2VxdWVuY2VzKSB7XG4gICAgICAgICAgX25leHRFeHBlY3RlZEFjdGlvbiA9IGZhbHNlO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBfZ2V0TWF0Y2hlcyhjaGFyYWN0ZXIsIG1vZGlmaWVycywgZSwgc2VxdWVuY2VOYW1lLCBjb21iaW5hdGlvbiwgbGV2ZWwpIHtcbiAgICAgICAgdmFyIGkyO1xuICAgICAgICB2YXIgY2FsbGJhY2s7XG4gICAgICAgIHZhciBtYXRjaGVzID0gW107XG4gICAgICAgIHZhciBhY3Rpb24gPSBlLnR5cGU7XG4gICAgICAgIGlmICghc2VsZi5fY2FsbGJhY2tzW2NoYXJhY3Rlcl0pIHtcbiAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGFjdGlvbiA9PSBcImtleXVwXCIgJiYgX2lzTW9kaWZpZXIoY2hhcmFjdGVyKSkge1xuICAgICAgICAgIG1vZGlmaWVycyA9IFtjaGFyYWN0ZXJdO1xuICAgICAgICB9XG4gICAgICAgIGZvciAoaTIgPSAwOyBpMiA8IHNlbGYuX2NhbGxiYWNrc1tjaGFyYWN0ZXJdLmxlbmd0aDsgKytpMikge1xuICAgICAgICAgIGNhbGxiYWNrID0gc2VsZi5fY2FsbGJhY2tzW2NoYXJhY3Rlcl1baTJdO1xuICAgICAgICAgIGlmICghc2VxdWVuY2VOYW1lICYmIGNhbGxiYWNrLnNlcSAmJiBfc2VxdWVuY2VMZXZlbHNbY2FsbGJhY2suc2VxXSAhPSBjYWxsYmFjay5sZXZlbCkge1xuICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmIChhY3Rpb24gIT0gY2FsbGJhY2suYWN0aW9uKSB7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKGFjdGlvbiA9PSBcImtleXByZXNzXCIgJiYgIWUubWV0YUtleSAmJiAhZS5jdHJsS2V5IHx8IF9tb2RpZmllcnNNYXRjaChtb2RpZmllcnMsIGNhbGxiYWNrLm1vZGlmaWVycykpIHtcbiAgICAgICAgICAgIHZhciBkZWxldGVDb21ibyA9ICFzZXF1ZW5jZU5hbWUgJiYgY2FsbGJhY2suY29tYm8gPT0gY29tYmluYXRpb247XG4gICAgICAgICAgICB2YXIgZGVsZXRlU2VxdWVuY2UgPSBzZXF1ZW5jZU5hbWUgJiYgY2FsbGJhY2suc2VxID09IHNlcXVlbmNlTmFtZSAmJiBjYWxsYmFjay5sZXZlbCA9PSBsZXZlbDtcbiAgICAgICAgICAgIGlmIChkZWxldGVDb21ibyB8fCBkZWxldGVTZXF1ZW5jZSkge1xuICAgICAgICAgICAgICBzZWxmLl9jYWxsYmFja3NbY2hhcmFjdGVyXS5zcGxpY2UoaTIsIDEpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgbWF0Y2hlcy5wdXNoKGNhbGxiYWNrKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIG1hdGNoZXM7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBfZmlyZUNhbGxiYWNrKGNhbGxiYWNrLCBlLCBjb21ibywgc2VxdWVuY2UpIHtcbiAgICAgICAgaWYgKHNlbGYuc3RvcENhbGxiYWNrKGUsIGUudGFyZ2V0IHx8IGUuc3JjRWxlbWVudCwgY29tYm8sIHNlcXVlbmNlKSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBpZiAoY2FsbGJhY2soZSwgY29tYm8pID09PSBmYWxzZSkge1xuICAgICAgICAgIF9wcmV2ZW50RGVmYXVsdChlKTtcbiAgICAgICAgICBfc3RvcFByb3BhZ2F0aW9uKGUpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBzZWxmLl9oYW5kbGVLZXkgPSBmdW5jdGlvbihjaGFyYWN0ZXIsIG1vZGlmaWVycywgZSkge1xuICAgICAgICB2YXIgY2FsbGJhY2tzID0gX2dldE1hdGNoZXMoY2hhcmFjdGVyLCBtb2RpZmllcnMsIGUpO1xuICAgICAgICB2YXIgaTI7XG4gICAgICAgIHZhciBkb05vdFJlc2V0ID0ge307XG4gICAgICAgIHZhciBtYXhMZXZlbCA9IDA7XG4gICAgICAgIHZhciBwcm9jZXNzZWRTZXF1ZW5jZUNhbGxiYWNrID0gZmFsc2U7XG4gICAgICAgIGZvciAoaTIgPSAwOyBpMiA8IGNhbGxiYWNrcy5sZW5ndGg7ICsraTIpIHtcbiAgICAgICAgICBpZiAoY2FsbGJhY2tzW2kyXS5zZXEpIHtcbiAgICAgICAgICAgIG1heExldmVsID0gTWF0aC5tYXgobWF4TGV2ZWwsIGNhbGxiYWNrc1tpMl0ubGV2ZWwpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICBmb3IgKGkyID0gMDsgaTIgPCBjYWxsYmFja3MubGVuZ3RoOyArK2kyKSB7XG4gICAgICAgICAgaWYgKGNhbGxiYWNrc1tpMl0uc2VxKSB7XG4gICAgICAgICAgICBpZiAoY2FsbGJhY2tzW2kyXS5sZXZlbCAhPSBtYXhMZXZlbCkge1xuICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHByb2Nlc3NlZFNlcXVlbmNlQ2FsbGJhY2sgPSB0cnVlO1xuICAgICAgICAgICAgZG9Ob3RSZXNldFtjYWxsYmFja3NbaTJdLnNlcV0gPSAxO1xuICAgICAgICAgICAgX2ZpcmVDYWxsYmFjayhjYWxsYmFja3NbaTJdLmNhbGxiYWNrLCBlLCBjYWxsYmFja3NbaTJdLmNvbWJvLCBjYWxsYmFja3NbaTJdLnNlcSk7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKCFwcm9jZXNzZWRTZXF1ZW5jZUNhbGxiYWNrKSB7XG4gICAgICAgICAgICBfZmlyZUNhbGxiYWNrKGNhbGxiYWNrc1tpMl0uY2FsbGJhY2ssIGUsIGNhbGxiYWNrc1tpMl0uY29tYm8pO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICB2YXIgaWdub3JlVGhpc0tleXByZXNzID0gZS50eXBlID09IFwia2V5cHJlc3NcIiAmJiBfaWdub3JlTmV4dEtleXByZXNzO1xuICAgICAgICBpZiAoZS50eXBlID09IF9uZXh0RXhwZWN0ZWRBY3Rpb24gJiYgIV9pc01vZGlmaWVyKGNoYXJhY3RlcikgJiYgIWlnbm9yZVRoaXNLZXlwcmVzcykge1xuICAgICAgICAgIF9yZXNldFNlcXVlbmNlcyhkb05vdFJlc2V0KTtcbiAgICAgICAgfVxuICAgICAgICBfaWdub3JlTmV4dEtleXByZXNzID0gcHJvY2Vzc2VkU2VxdWVuY2VDYWxsYmFjayAmJiBlLnR5cGUgPT0gXCJrZXlkb3duXCI7XG4gICAgICB9O1xuICAgICAgZnVuY3Rpb24gX2hhbmRsZUtleUV2ZW50KGUpIHtcbiAgICAgICAgaWYgKHR5cGVvZiBlLndoaWNoICE9PSBcIm51bWJlclwiKSB7XG4gICAgICAgICAgZS53aGljaCA9IGUua2V5Q29kZTtcbiAgICAgICAgfVxuICAgICAgICB2YXIgY2hhcmFjdGVyID0gX2NoYXJhY3RlckZyb21FdmVudChlKTtcbiAgICAgICAgaWYgKCFjaGFyYWN0ZXIpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGUudHlwZSA9PSBcImtleXVwXCIgJiYgX2lnbm9yZU5leHRLZXl1cCA9PT0gY2hhcmFjdGVyKSB7XG4gICAgICAgICAgX2lnbm9yZU5leHRLZXl1cCA9IGZhbHNlO1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBzZWxmLmhhbmRsZUtleShjaGFyYWN0ZXIsIF9ldmVudE1vZGlmaWVycyhlKSwgZSk7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBfcmVzZXRTZXF1ZW5jZVRpbWVyKCkge1xuICAgICAgICBjbGVhclRpbWVvdXQoX3Jlc2V0VGltZXIpO1xuICAgICAgICBfcmVzZXRUaW1lciA9IHNldFRpbWVvdXQoX3Jlc2V0U2VxdWVuY2VzLCAxZTMpO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gX2JpbmRTZXF1ZW5jZShjb21ibywga2V5cywgY2FsbGJhY2ssIGFjdGlvbikge1xuICAgICAgICBfc2VxdWVuY2VMZXZlbHNbY29tYm9dID0gMDtcbiAgICAgICAgZnVuY3Rpb24gX2luY3JlYXNlU2VxdWVuY2UobmV4dEFjdGlvbikge1xuICAgICAgICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIF9uZXh0RXhwZWN0ZWRBY3Rpb24gPSBuZXh0QWN0aW9uO1xuICAgICAgICAgICAgKytfc2VxdWVuY2VMZXZlbHNbY29tYm9dO1xuICAgICAgICAgICAgX3Jlc2V0U2VxdWVuY2VUaW1lcigpO1xuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgZnVuY3Rpb24gX2NhbGxiYWNrQW5kUmVzZXQoZSkge1xuICAgICAgICAgIF9maXJlQ2FsbGJhY2soY2FsbGJhY2ssIGUsIGNvbWJvKTtcbiAgICAgICAgICBpZiAoYWN0aW9uICE9PSBcImtleXVwXCIpIHtcbiAgICAgICAgICAgIF9pZ25vcmVOZXh0S2V5dXAgPSBfY2hhcmFjdGVyRnJvbUV2ZW50KGUpO1xuICAgICAgICAgIH1cbiAgICAgICAgICBzZXRUaW1lb3V0KF9yZXNldFNlcXVlbmNlcywgMTApO1xuICAgICAgICB9XG4gICAgICAgIGZvciAodmFyIGkyID0gMDsgaTIgPCBrZXlzLmxlbmd0aDsgKytpMikge1xuICAgICAgICAgIHZhciBpc0ZpbmFsID0gaTIgKyAxID09PSBrZXlzLmxlbmd0aDtcbiAgICAgICAgICB2YXIgd3JhcHBlZENhbGxiYWNrID0gaXNGaW5hbCA/IF9jYWxsYmFja0FuZFJlc2V0IDogX2luY3JlYXNlU2VxdWVuY2UoYWN0aW9uIHx8IF9nZXRLZXlJbmZvKGtleXNbaTIgKyAxXSkuYWN0aW9uKTtcbiAgICAgICAgICBfYmluZFNpbmdsZShrZXlzW2kyXSwgd3JhcHBlZENhbGxiYWNrLCBhY3Rpb24sIGNvbWJvLCBpMik7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIF9iaW5kU2luZ2xlKGNvbWJpbmF0aW9uLCBjYWxsYmFjaywgYWN0aW9uLCBzZXF1ZW5jZU5hbWUsIGxldmVsKSB7XG4gICAgICAgIHNlbGYuX2RpcmVjdE1hcFtjb21iaW5hdGlvbiArIFwiOlwiICsgYWN0aW9uXSA9IGNhbGxiYWNrO1xuICAgICAgICBjb21iaW5hdGlvbiA9IGNvbWJpbmF0aW9uLnJlcGxhY2UoL1xccysvZywgXCIgXCIpO1xuICAgICAgICB2YXIgc2VxdWVuY2UgPSBjb21iaW5hdGlvbi5zcGxpdChcIiBcIik7XG4gICAgICAgIHZhciBpbmZvO1xuICAgICAgICBpZiAoc2VxdWVuY2UubGVuZ3RoID4gMSkge1xuICAgICAgICAgIF9iaW5kU2VxdWVuY2UoY29tYmluYXRpb24sIHNlcXVlbmNlLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgaW5mbyA9IF9nZXRLZXlJbmZvKGNvbWJpbmF0aW9uLCBhY3Rpb24pO1xuICAgICAgICBzZWxmLl9jYWxsYmFja3NbaW5mby5rZXldID0gc2VsZi5fY2FsbGJhY2tzW2luZm8ua2V5XSB8fCBbXTtcbiAgICAgICAgX2dldE1hdGNoZXMoaW5mby5rZXksIGluZm8ubW9kaWZpZXJzLCB7dHlwZTogaW5mby5hY3Rpb259LCBzZXF1ZW5jZU5hbWUsIGNvbWJpbmF0aW9uLCBsZXZlbCk7XG4gICAgICAgIHNlbGYuX2NhbGxiYWNrc1tpbmZvLmtleV1bc2VxdWVuY2VOYW1lID8gXCJ1bnNoaWZ0XCIgOiBcInB1c2hcIl0oe1xuICAgICAgICAgIGNhbGxiYWNrLFxuICAgICAgICAgIG1vZGlmaWVyczogaW5mby5tb2RpZmllcnMsXG4gICAgICAgICAgYWN0aW9uOiBpbmZvLmFjdGlvbixcbiAgICAgICAgICBzZXE6IHNlcXVlbmNlTmFtZSxcbiAgICAgICAgICBsZXZlbCxcbiAgICAgICAgICBjb21ibzogY29tYmluYXRpb25cbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBzZWxmLl9iaW5kTXVsdGlwbGUgPSBmdW5jdGlvbihjb21iaW5hdGlvbnMsIGNhbGxiYWNrLCBhY3Rpb24pIHtcbiAgICAgICAgZm9yICh2YXIgaTIgPSAwOyBpMiA8IGNvbWJpbmF0aW9ucy5sZW5ndGg7ICsraTIpIHtcbiAgICAgICAgICBfYmluZFNpbmdsZShjb21iaW5hdGlvbnNbaTJdLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICAgIF9hZGRFdmVudCh0YXJnZXRFbGVtZW50LCBcImtleXByZXNzXCIsIF9oYW5kbGVLZXlFdmVudCk7XG4gICAgICBfYWRkRXZlbnQodGFyZ2V0RWxlbWVudCwgXCJrZXlkb3duXCIsIF9oYW5kbGVLZXlFdmVudCk7XG4gICAgICBfYWRkRXZlbnQodGFyZ2V0RWxlbWVudCwgXCJrZXl1cFwiLCBfaGFuZGxlS2V5RXZlbnQpO1xuICAgIH1cbiAgICBNb3VzZXRyYXAzLnByb3RvdHlwZS5iaW5kID0gZnVuY3Rpb24oa2V5cywgY2FsbGJhY2ssIGFjdGlvbikge1xuICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgICAga2V5cyA9IGtleXMgaW5zdGFuY2VvZiBBcnJheSA/IGtleXMgOiBba2V5c107XG4gICAgICBzZWxmLl9iaW5kTXVsdGlwbGUuY2FsbChzZWxmLCBrZXlzLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICAgIHJldHVybiBzZWxmO1xuICAgIH07XG4gICAgTW91c2V0cmFwMy5wcm90b3R5cGUudW5iaW5kID0gZnVuY3Rpb24oa2V5cywgYWN0aW9uKSB7XG4gICAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgICByZXR1cm4gc2VsZi5iaW5kLmNhbGwoc2VsZiwga2V5cywgZnVuY3Rpb24oKSB7XG4gICAgICB9LCBhY3Rpb24pO1xuICAgIH07XG4gICAgTW91c2V0cmFwMy5wcm90b3R5cGUudHJpZ2dlciA9IGZ1bmN0aW9uKGtleXMsIGFjdGlvbikge1xuICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgICAgaWYgKHNlbGYuX2RpcmVjdE1hcFtrZXlzICsgXCI6XCIgKyBhY3Rpb25dKSB7XG4gICAgICAgIHNlbGYuX2RpcmVjdE1hcFtrZXlzICsgXCI6XCIgKyBhY3Rpb25dKHt9LCBrZXlzKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBzZWxmO1xuICAgIH07XG4gICAgTW91c2V0cmFwMy5wcm90b3R5cGUucmVzZXQgPSBmdW5jdGlvbigpIHtcbiAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgIHNlbGYuX2NhbGxiYWNrcyA9IHt9O1xuICAgICAgc2VsZi5fZGlyZWN0TWFwID0ge307XG4gICAgICByZXR1cm4gc2VsZjtcbiAgICB9O1xuICAgIE1vdXNldHJhcDMucHJvdG90eXBlLnN0b3BDYWxsYmFjayA9IGZ1bmN0aW9uKGUsIGVsZW1lbnQpIHtcbiAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgIGlmICgoXCIgXCIgKyBlbGVtZW50LmNsYXNzTmFtZSArIFwiIFwiKS5pbmRleE9mKFwiIG1vdXNldHJhcCBcIikgPiAtMSkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG4gICAgICBpZiAoX2JlbG9uZ3NUbyhlbGVtZW50LCBzZWxmLnRhcmdldCkpIHtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuICAgICAgaWYgKFwiY29tcG9zZWRQYXRoXCIgaW4gZSAmJiB0eXBlb2YgZS5jb21wb3NlZFBhdGggPT09IFwiZnVuY3Rpb25cIikge1xuICAgICAgICB2YXIgaW5pdGlhbEV2ZW50VGFyZ2V0ID0gZS5jb21wb3NlZFBhdGgoKVswXTtcbiAgICAgICAgaWYgKGluaXRpYWxFdmVudFRhcmdldCAhPT0gZS50YXJnZXQpIHtcbiAgICAgICAgICBlbGVtZW50ID0gaW5pdGlhbEV2ZW50VGFyZ2V0O1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gZWxlbWVudC50YWdOYW1lID09IFwiSU5QVVRcIiB8fCBlbGVtZW50LnRhZ05hbWUgPT0gXCJTRUxFQ1RcIiB8fCBlbGVtZW50LnRhZ05hbWUgPT0gXCJURVhUQVJFQVwiIHx8IGVsZW1lbnQuaXNDb250ZW50RWRpdGFibGU7XG4gICAgfTtcbiAgICBNb3VzZXRyYXAzLnByb3RvdHlwZS5oYW5kbGVLZXkgPSBmdW5jdGlvbigpIHtcbiAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgIHJldHVybiBzZWxmLl9oYW5kbGVLZXkuYXBwbHkoc2VsZiwgYXJndW1lbnRzKTtcbiAgICB9O1xuICAgIE1vdXNldHJhcDMuYWRkS2V5Y29kZXMgPSBmdW5jdGlvbihvYmplY3QpIHtcbiAgICAgIGZvciAodmFyIGtleSBpbiBvYmplY3QpIHtcbiAgICAgICAgaWYgKG9iamVjdC5oYXNPd25Qcm9wZXJ0eShrZXkpKSB7XG4gICAgICAgICAgX01BUFtrZXldID0gb2JqZWN0W2tleV07XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIF9SRVZFUlNFX01BUCA9IG51bGw7XG4gICAgfTtcbiAgICBNb3VzZXRyYXAzLmluaXQgPSBmdW5jdGlvbigpIHtcbiAgICAgIHZhciBkb2N1bWVudE1vdXNldHJhcCA9IE1vdXNldHJhcDMoZG9jdW1lbnQyKTtcbiAgICAgIGZvciAodmFyIG1ldGhvZCBpbiBkb2N1bWVudE1vdXNldHJhcCkge1xuICAgICAgICBpZiAobWV0aG9kLmNoYXJBdCgwKSAhPT0gXCJfXCIpIHtcbiAgICAgICAgICBNb3VzZXRyYXAzW21ldGhvZF0gPSBmdW5jdGlvbihtZXRob2QyKSB7XG4gICAgICAgICAgICByZXR1cm4gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgIHJldHVybiBkb2N1bWVudE1vdXNldHJhcFttZXRob2QyXS5hcHBseShkb2N1bWVudE1vdXNldHJhcCwgYXJndW1lbnRzKTtcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgfShtZXRob2QpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfTtcbiAgICBNb3VzZXRyYXAzLmluaXQoKTtcbiAgICB3aW5kb3cyLk1vdXNldHJhcCA9IE1vdXNldHJhcDM7XG4gICAgaWYgKHR5cGVvZiBtb2R1bGUgIT09IFwidW5kZWZpbmVkXCIgJiYgbW9kdWxlLmV4cG9ydHMpIHtcbiAgICAgIG1vZHVsZS5leHBvcnRzID0gTW91c2V0cmFwMztcbiAgICB9XG4gICAgaWYgKHR5cGVvZiBkZWZpbmUgPT09IFwiZnVuY3Rpb25cIiAmJiBkZWZpbmUuYW1kKSB7XG4gICAgICBkZWZpbmUoZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiBNb3VzZXRyYXAzO1xuICAgICAgfSk7XG4gICAgfVxuICB9KSh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93IDogbnVsbCwgdHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IGRvY3VtZW50IDogbnVsbCk7XG59KTtcblxuLy8gc3JjL2luZGV4LmpzXG52YXIgaW1wb3J0X21vdXNldHJhcCA9IF9fdG9Nb2R1bGUocmVxdWlyZV9tb3VzZXRyYXAoKSk7XG5cbi8vIG5vZGVfbW9kdWxlcy9tb3VzZXRyYXAvcGx1Z2lucy9nbG9iYWwtYmluZC9tb3VzZXRyYXAtZ2xvYmFsLWJpbmQuanNcbihmdW5jdGlvbihNb3VzZXRyYXAzKSB7XG4gIGlmICghTW91c2V0cmFwMykge1xuICAgIHJldHVybjtcbiAgfVxuICB2YXIgX2dsb2JhbENhbGxiYWNrcyA9IHt9O1xuICB2YXIgX29yaWdpbmFsU3RvcENhbGxiYWNrID0gTW91c2V0cmFwMy5wcm90b3R5cGUuc3RvcENhbGxiYWNrO1xuICBNb3VzZXRyYXAzLnByb3RvdHlwZS5zdG9wQ2FsbGJhY2sgPSBmdW5jdGlvbihlLCBlbGVtZW50LCBjb21ibywgc2VxdWVuY2UpIHtcbiAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgaWYgKHNlbGYucGF1c2VkKSB7XG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9XG4gICAgaWYgKF9nbG9iYWxDYWxsYmFja3NbY29tYm9dIHx8IF9nbG9iYWxDYWxsYmFja3Nbc2VxdWVuY2VdKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICAgIHJldHVybiBfb3JpZ2luYWxTdG9wQ2FsbGJhY2suY2FsbChzZWxmLCBlLCBlbGVtZW50LCBjb21ibyk7XG4gIH07XG4gIE1vdXNldHJhcDMucHJvdG90eXBlLmJpbmRHbG9iYWwgPSBmdW5jdGlvbihrZXlzLCBjYWxsYmFjaywgYWN0aW9uKSB7XG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgIHNlbGYuYmluZChrZXlzLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICBpZiAoa2V5cyBpbnN0YW5jZW9mIEFycmF5KSB7XG4gICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGtleXMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgX2dsb2JhbENhbGxiYWNrc1trZXlzW2ldXSA9IHRydWU7XG4gICAgICB9XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIF9nbG9iYWxDYWxsYmFja3Nba2V5c10gPSB0cnVlO1xuICB9O1xuICBNb3VzZXRyYXAzLmluaXQoKTtcbn0pKHR5cGVvZiBNb3VzZXRyYXAgIT09IFwidW5kZWZpbmVkXCIgPyBNb3VzZXRyYXAgOiB2b2lkIDApO1xuXG4vLyBzcmMvaW5kZXguanNcbnZhciBzcmNfZGVmYXVsdCA9IChBbHBpbmUpID0+IHtcbiAgQWxwaW5lLmRpcmVjdGl2ZShcIm1vdXNldHJhcFwiLCAoZWwsIHttb2RpZmllcnMsIGV4cHJlc3Npb259LCB7ZXZhbHVhdGV9KSA9PiB7XG4gICAgY29uc3QgYWN0aW9uID0gKCkgPT4gZXhwcmVzc2lvbiA/IGV2YWx1YXRlKGV4cHJlc3Npb24pIDogZWwuY2xpY2soKTtcbiAgICBtb2RpZmllcnMgPSBtb2RpZmllcnMubWFwKChtb2RpZmllcikgPT4gbW9kaWZpZXIucmVwbGFjZShcIi1cIiwgXCIrXCIpKTtcbiAgICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiZ2xvYmFsXCIpKSB7XG4gICAgICBtb2RpZmllcnMgPSBtb2RpZmllcnMuZmlsdGVyKChtb2RpZmllcikgPT4gbW9kaWZpZXIgIT09IFwiZ2xvYmFsXCIpO1xuICAgICAgaW1wb3J0X21vdXNldHJhcC5kZWZhdWx0LmJpbmRHbG9iYWwobW9kaWZpZXJzLCAoJGV2ZW50KSA9PiB7XG4gICAgICAgICRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBhY3Rpb24oKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICBpbXBvcnRfbW91c2V0cmFwLmRlZmF1bHQuYmluZChtb2RpZmllcnMsICgkZXZlbnQpID0+IHtcbiAgICAgICRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgYWN0aW9uKCk7XG4gICAgfSk7XG4gIH0pO1xufTtcblxuLy8gYnVpbGRzL21vZHVsZS5qc1xudmFyIG1vZHVsZV9kZWZhdWx0ID0gc3JjX2RlZmF1bHQ7XG5leHBvcnQge1xuICBtb2R1bGVfZGVmYXVsdCBhcyBkZWZhdWx0XG59O1xuIiwgImltcG9ydCBNb3VzZXRyYXAgZnJvbSAnQGRhbmhhcnJpbi9hbHBpbmUtbW91c2V0cmFwJ1xuXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdhbHBpbmU6aW5pdCcsICgpID0+IHtcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihNb3VzZXRyYXApXG5cbiAgICB3aW5kb3cuQWxwaW5lLnN0b3JlKCdzaWRlYmFyJywge1xuICAgICAgICBpc09wZW46IHdpbmRvdy5BbHBpbmUuJHBlcnNpc3QodHJ1ZSkuYXMoJ2lzT3BlbicpLFxuXG4gICAgICAgIGNvbGxhcHNlZEdyb3Vwczogd2luZG93LkFscGluZS4kcGVyc2lzdChudWxsKS5hcygnY29sbGFwc2VkR3JvdXBzJyksXG5cbiAgICAgICAgZ3JvdXBJc0NvbGxhcHNlZDogZnVuY3Rpb24gKGdyb3VwKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5jb2xsYXBzZWRHcm91cHMuaW5jbHVkZXMoZ3JvdXApXG4gICAgICAgIH0sXG5cbiAgICAgICAgY29sbGFwc2VHcm91cDogZnVuY3Rpb24gKGdyb3VwKSB7XG4gICAgICAgICAgICBpZiAodGhpcy5jb2xsYXBzZWRHcm91cHMuaW5jbHVkZXMoZ3JvdXApKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuY29sbGFwc2VkR3JvdXBzID0gdGhpcy5jb2xsYXBzZWRHcm91cHMuY29uY2F0KGdyb3VwKVxuICAgICAgICB9LFxuXG4gICAgICAgIHRvZ2dsZUNvbGxhcHNlZEdyb3VwOiBmdW5jdGlvbiAoZ3JvdXApIHtcbiAgICAgICAgICAgIHRoaXMuY29sbGFwc2VkR3JvdXBzID0gdGhpcy5jb2xsYXBzZWRHcm91cHMuaW5jbHVkZXMoZ3JvdXApXG4gICAgICAgICAgICAgICAgPyB0aGlzLmNvbGxhcHNlZEdyb3Vwcy5maWx0ZXIoXG4gICAgICAgICAgICAgICAgICAgICAgKGNvbGxhcHNlZEdyb3VwKSA9PiBjb2xsYXBzZWRHcm91cCAhPT0gZ3JvdXAsXG4gICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgOiB0aGlzLmNvbGxhcHNlZEdyb3Vwcy5jb25jYXQoZ3JvdXApXG4gICAgICAgIH0sXG5cbiAgICAgICAgY2xvc2U6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRoaXMuaXNPcGVuID0gZmFsc2VcbiAgICAgICAgfSxcblxuICAgICAgICBvcGVuOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLmlzT3BlbiA9IHRydWVcbiAgICAgICAgfSxcbiAgICB9KVxuXG4gICAgY29uc3QgdGhlbWUgPSBsb2NhbFN0b3JhZ2UuZ2V0SXRlbSgndGhlbWUnKSA/PyAnc3lzdGVtJ1xuXG4gICAgd2luZG93LkFscGluZS5zdG9yZShcbiAgICAgICAgJ3RoZW1lJyxcbiAgICAgICAgdGhlbWUgPT09ICdkYXJrJyB8fFxuICAgICAgICAgICAgKHRoZW1lID09PSAnc3lzdGVtJyAmJlxuICAgICAgICAgICAgICAgIHdpbmRvdy5tYXRjaE1lZGlhKCcocHJlZmVycy1jb2xvci1zY2hlbWU6IGRhcmspJykubWF0Y2hlcylcbiAgICAgICAgICAgID8gJ2RhcmsnXG4gICAgICAgICAgICA6ICdsaWdodCcsXG4gICAgKVxuXG4gICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ3RoZW1lLWNoYW5nZWQnLCAoZXZlbnQpID0+IHtcbiAgICAgICAgbGV0IHRoZW1lID0gZXZlbnQuZGV0YWlsXG5cbiAgICAgICAgbG9jYWxTdG9yYWdlLnNldEl0ZW0oJ3RoZW1lJywgdGhlbWUpXG5cbiAgICAgICAgaWYgKHRoZW1lID09PSAnc3lzdGVtJykge1xuICAgICAgICAgICAgdGhlbWUgPSB3aW5kb3cubWF0Y2hNZWRpYSgnKHByZWZlcnMtY29sb3Itc2NoZW1lOiBkYXJrKScpLm1hdGNoZXNcbiAgICAgICAgICAgICAgICA/ICdkYXJrJ1xuICAgICAgICAgICAgICAgIDogJ2xpZ2h0J1xuICAgICAgICB9XG5cbiAgICAgICAgd2luZG93LkFscGluZS5zdG9yZSgndGhlbWUnLCB0aGVtZSlcbiAgICB9KVxuXG4gICAgd2luZG93XG4gICAgICAgIC5tYXRjaE1lZGlhKCcocHJlZmVycy1jb2xvci1zY2hlbWU6IGRhcmspJylcbiAgICAgICAgLmFkZEV2ZW50TGlzdGVuZXIoJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgICAgICAgaWYgKGxvY2FsU3RvcmFnZS5nZXRJdGVtKCd0aGVtZScpID09PSAnc3lzdGVtJykge1xuICAgICAgICAgICAgICAgIHdpbmRvdy5BbHBpbmUuc3RvcmUoJ3RoZW1lJywgZXZlbnQubWF0Y2hlcyA/ICdkYXJrJyA6ICdsaWdodCcpXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pXG5cbiAgICB3aW5kb3cuQWxwaW5lLmVmZmVjdCgoKSA9PiB7XG4gICAgICAgIGNvbnN0IHRoZW1lID0gd2luZG93LkFscGluZS5zdG9yZSgndGhlbWUnKVxuXG4gICAgICAgIHRoZW1lID09PSAnZGFyaydcbiAgICAgICAgICAgID8gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmNsYXNzTGlzdC5hZGQoJ2RhcmsnKVxuICAgICAgICAgICAgOiBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuY2xhc3NMaXN0LnJlbW92ZSgnZGFyaycpXG4gICAgfSlcbn0pXG4iXSwKICAibWFwcGluZ3MiOiAiOztBQUFBLE1BQUksV0FBVyxPQUFPO0FBQ3RCLE1BQUksWUFBWSxPQUFPO0FBQ3ZCLE1BQUksZUFBZSxPQUFPO0FBQzFCLE1BQUksZUFBZSxPQUFPLFVBQVU7QUFDcEMsTUFBSSxvQkFBb0IsT0FBTztBQUMvQixNQUFJLG1CQUFtQixPQUFPO0FBQzlCLE1BQUksaUJBQWlCLENBQUMsV0FBVyxVQUFVLFFBQVEsY0FBYyxFQUFDLE9BQU8sS0FBSSxDQUFDO0FBQzlFLE1BQUksYUFBYSxDQUFDLFVBQVUsV0FBVyxNQUFNO0FBQzNDLFFBQUksQ0FBQyxRQUFRO0FBQ1gsZUFBUyxFQUFDLFNBQVMsQ0FBQyxFQUFDO0FBQ3JCLGVBQVMsT0FBTyxTQUFTLE1BQU07QUFBQSxJQUNqQztBQUNBLFdBQU8sT0FBTztBQUFBLEVBQ2hCO0FBQ0EsTUFBSSxlQUFlLENBQUMsUUFBUSxRQUFRLFNBQVM7QUFDM0MsUUFBSSxVQUFVLE9BQU8sV0FBVyxZQUFZLE9BQU8sV0FBVyxZQUFZO0FBQ3hFLGVBQVMsT0FBTyxrQkFBa0IsTUFBTTtBQUN0QyxZQUFJLENBQUMsYUFBYSxLQUFLLFFBQVEsR0FBRyxLQUFLLFFBQVE7QUFDN0Msb0JBQVUsUUFBUSxLQUFLLEVBQUMsS0FBSyxNQUFNLE9BQU8sR0FBRyxHQUFHLFlBQVksRUFBRSxPQUFPLGlCQUFpQixRQUFRLEdBQUcs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
