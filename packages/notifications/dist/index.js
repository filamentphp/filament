(() => {
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

  // node_modules/uuid-browser/lib/rng-browser.js
  var require_rng_browser = __commonJS({
    "node_modules/uuid-browser/lib/rng-browser.js"(exports, module) {
      var rng;
      var crypto = typeof global !== "undefined" && (global.crypto || global.msCrypto);
      if (crypto && crypto.getRandomValues) {
        rnds8 = new Uint8Array(16);
        rng = function whatwgRNG() {
          crypto.getRandomValues(rnds8);
          return rnds8;
        };
      }
      var rnds8;
      if (!rng) {
        rnds = new Array(16);
        rng = function() {
          for (var i = 0, r; i < 16; i++) {
            if ((i & 3) === 0)
              r = Math.random() * 4294967296;
            rnds[i] = r >>> ((i & 3) << 3) & 255;
          }
          return rnds;
        };
      }
      var rnds;
      module.exports = rng;
    }
  });

  // node_modules/uuid-browser/lib/bytesToUuid.js
  var require_bytesToUuid = __commonJS({
    "node_modules/uuid-browser/lib/bytesToUuid.js"(exports, module) {
      var byteToHex = [];
      for (i = 0; i < 256; ++i) {
        byteToHex[i] = (i + 256).toString(16).substr(1);
      }
      var i;
      function bytesToUuid(buf, offset) {
        var i2 = offset || 0;
        var bth = byteToHex;
        return bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]];
      }
      module.exports = bytesToUuid;
    }
  });

  // node_modules/uuid-browser/v1.js
  var require_v1 = __commonJS({
    "node_modules/uuid-browser/v1.js"(exports, module) {
      var rng = require_rng_browser();
      var bytesToUuid = require_bytesToUuid();
      var _seedBytes = rng();
      var _nodeId = [
        _seedBytes[0] | 1,
        _seedBytes[1],
        _seedBytes[2],
        _seedBytes[3],
        _seedBytes[4],
        _seedBytes[5]
      ];
      var _clockseq = (_seedBytes[6] << 8 | _seedBytes[7]) & 16383;
      var _lastMSecs = 0;
      var _lastNSecs = 0;
      function v1(options, buf, offset) {
        var i = buf && offset || 0;
        var b = buf || [];
        options = options || {};
        var clockseq = options.clockseq !== void 0 ? options.clockseq : _clockseq;
        var msecs = options.msecs !== void 0 ? options.msecs : (/* @__PURE__ */ new Date()).getTime();
        var nsecs = options.nsecs !== void 0 ? options.nsecs : _lastNSecs + 1;
        var dt = msecs - _lastMSecs + (nsecs - _lastNSecs) / 1e4;
        if (dt < 0 && options.clockseq === void 0) {
          clockseq = clockseq + 1 & 16383;
        }
        if ((dt < 0 || msecs > _lastMSecs) && options.nsecs === void 0) {
          nsecs = 0;
        }
        if (nsecs >= 1e4) {
          throw new Error("uuid.v1(): Can't create more than 10M uuids/sec");
        }
        _lastMSecs = msecs;
        _lastNSecs = nsecs;
        _clockseq = clockseq;
        msecs += 122192928e5;
        var tl = ((msecs & 268435455) * 1e4 + nsecs) % 4294967296;
        b[i++] = tl >>> 24 & 255;
        b[i++] = tl >>> 16 & 255;
        b[i++] = tl >>> 8 & 255;
        b[i++] = tl & 255;
        var tmh = msecs / 4294967296 * 1e4 & 268435455;
        b[i++] = tmh >>> 8 & 255;
        b[i++] = tmh & 255;
        b[i++] = tmh >>> 24 & 15 | 16;
        b[i++] = tmh >>> 16 & 255;
        b[i++] = clockseq >>> 8 | 128;
        b[i++] = clockseq & 255;
        var node = options.node || _nodeId;
        for (var n = 0; n < 6; ++n) {
          b[i + n] = node[n];
        }
        return buf ? buf : bytesToUuid(b);
      }
      module.exports = v1;
    }
  });

  // node_modules/uuid-browser/v4.js
  var require_v4 = __commonJS({
    "node_modules/uuid-browser/v4.js"(exports, module) {
      var rng = require_rng_browser();
      var bytesToUuid = require_bytesToUuid();
      function v4(options, buf, offset) {
        var i = buf && offset || 0;
        if (typeof options == "string") {
          buf = options == "binary" ? new Array(16) : null;
          options = null;
        }
        options = options || {};
        var rnds = options.random || (options.rng || rng)();
        rnds[6] = rnds[6] & 15 | 64;
        rnds[8] = rnds[8] & 63 | 128;
        if (buf) {
          for (var ii = 0; ii < 16; ++ii) {
            buf[i + ii] = rnds[ii];
          }
        }
        return buf || bytesToUuid(rnds);
      }
      module.exports = v4;
    }
  });

  // node_modules/uuid-browser/index.js
  var require_uuid_browser = __commonJS({
    "node_modules/uuid-browser/index.js"(exports, module) {
      var v1 = require_v1();
      var v4 = require_v4();
      var uuid2 = v4;
      uuid2.v1 = v1;
      uuid2.v4 = v4;
      module.exports = uuid2;
    }
  });

  // node_modules/alpinejs/src/mutation.js
  var onAttributeAddeds = [];
  var onElRemoveds = [];
  var onElAddeds = [];
  function cleanupAttributes(el, names) {
    if (!el._x_attributeCleanups)
      return;
    Object.entries(el._x_attributeCleanups).forEach(([name, value]) => {
      if (names === void 0 || names.includes(name)) {
        value.forEach((i) => i());
        delete el._x_attributeCleanups[name];
      }
    });
  }
  var observer = new MutationObserver(onMutate);
  var currentlyObserving = false;
  function startObservingMutations() {
    observer.observe(document, { subtree: true, childList: true, attributes: true, attributeOldValue: true });
    currentlyObserving = true;
  }
  function stopObservingMutations() {
    flushObserver();
    observer.disconnect();
    currentlyObserving = false;
  }
  var recordQueue = [];
  var willProcessRecordQueue = false;
  function flushObserver() {
    recordQueue = recordQueue.concat(observer.takeRecords());
    if (recordQueue.length && !willProcessRecordQueue) {
      willProcessRecordQueue = true;
      queueMicrotask(() => {
        processRecordQueue();
        willProcessRecordQueue = false;
      });
    }
  }
  function processRecordQueue() {
    onMutate(recordQueue);
    recordQueue.length = 0;
  }
  function mutateDom(callback) {
    if (!currentlyObserving)
      return callback();
    stopObservingMutations();
    let result = callback();
    startObservingMutations();
    return result;
  }
  var isCollecting = false;
  var deferredMutations = [];
  function onMutate(mutations) {
    if (isCollecting) {
      deferredMutations = deferredMutations.concat(mutations);
      return;
    }
    let addedNodes = [];
    let removedNodes = [];
    let addedAttributes = /* @__PURE__ */ new Map();
    let removedAttributes = /* @__PURE__ */ new Map();
    for (let i = 0; i < mutations.length; i++) {
      if (mutations[i].target._x_ignoreMutationObserver)
        continue;
      if (mutations[i].type === "childList") {
        mutations[i].addedNodes.forEach((node) => node.nodeType === 1 && addedNodes.push(node));
        mutations[i].removedNodes.forEach((node) => node.nodeType === 1 && removedNodes.push(node));
      }
      if (mutations[i].type === "attributes") {
        let el = mutations[i].target;
        let name = mutations[i].attributeName;
        let oldValue = mutations[i].oldValue;
        let add = () => {
          if (!addedAttributes.has(el))
            addedAttributes.set(el, []);
          addedAttributes.get(el).push({ name, value: el.getAttribute(name) });
        };
        let remove = () => {
          if (!removedAttributes.has(el))
            removedAttributes.set(el, []);
          removedAttributes.get(el).push(name);
        };
        if (el.hasAttribute(name) && oldValue === null) {
          add();
        } else if (el.hasAttribute(name)) {
          remove();
          add();
        } else {
          remove();
        }
      }
    }
    removedAttributes.forEach((attrs, el) => {
      cleanupAttributes(el, attrs);
    });
    addedAttributes.forEach((attrs, el) => {
      onAttributeAddeds.forEach((i) => i(el, attrs));
    });
    for (let node of removedNodes) {
      if (addedNodes.includes(node))
        continue;
      onElRemoveds.forEach((i) => i(node));
      if (node._x_cleanups) {
        while (node._x_cleanups.length)
          node._x_cleanups.pop()();
      }
    }
    addedNodes.forEach((node) => {
      node._x_ignoreSelf = true;
      node._x_ignore = true;
    });
    for (let node of addedNodes) {
      if (removedNodes.includes(node))
        continue;
      if (!node.isConnected)
        continue;
      delete node._x_ignoreSelf;
      delete node._x_ignore;
      onElAddeds.forEach((i) => i(node));
      node._x_ignore = true;
      node._x_ignoreSelf = true;
    }
    addedNodes.forEach((node) => {
      delete node._x_ignoreSelf;
      delete node._x_ignore;
    });
    addedNodes = null;
    removedNodes = null;
    addedAttributes = null;
    removedAttributes = null;
  }

  // node_modules/alpinejs/src/utils/once.js
  function once(callback, fallback = () => {
  }) {
    let called = false;
    return function() {
      if (!called) {
        called = true;
        callback.apply(this, arguments);
      } else {
        fallback.apply(this, arguments);
      }
    };
  }

  // packages/notifications/resources/js/components/notification.js
  var notification_default = (Alpine) => {
    Alpine.data("notificationComponent", ({ notification }) => ({
      isShown: false,
      computedStyle: null,
      init: function() {
        this.computedStyle = window.getComputedStyle(this.$el);
        this.configureTransitions();
        this.configureAnimations();
        if (notification.duration && notification.duration !== "persistent") {
          setTimeout(() => this.close(), notification.duration);
        }
        this.isShown = true;
      },
      configureTransitions: function() {
        const display = this.computedStyle.display;
        const show = () => {
          mutateDom(() => {
            this.$el.style.setProperty("display", display);
            this.$el.style.setProperty("visibility", "visible");
          });
          this.$el._x_isShown = true;
        };
        const hide = () => {
          mutateDom(() => {
            this.$el._x_isShown ? this.$el.style.setProperty("visibility", "hidden") : this.$el.style.setProperty("display", "none");
          });
        };
        const toggle = once(
          (value) => value ? show() : hide(),
          (value) => {
            this.$el._x_toggleAndCascadeWithTransitions(
              this.$el,
              value,
              show,
              hide
            );
          }
        );
        Alpine.effect(() => toggle(this.isShown));
      },
      configureAnimations: function() {
        let animation;
        Livewire.hook("message.received", (_, component) => {
          if (!component.serverMemo.data.isFilamentNotificationsComponent) {
            return;
          }
          const getTop = () => this.$el.getBoundingClientRect().top;
          const oldTop = getTop();
          animation = () => {
            this.$el.animate(
              [
                { transform: `translateY(${oldTop - getTop()}px)` },
                { transform: "translateY(0px)" }
              ],
              {
                duration: this.getTransitionDuration(),
                easing: this.computedStyle.transitionTimingFunction
              }
            );
          };
          this.$el.getAnimations().forEach((animation2) => animation2.finish());
        });
        Livewire.hook("message.processed", (_, component) => {
          if (!component.serverMemo.data.isFilamentNotificationsComponent) {
            return;
          }
          if (!this.isShown) {
            return;
          }
          animation();
        });
      },
      close: function() {
        this.isShown = false;
        setTimeout(
          () => Livewire.emit("notificationClosed", notification.id),
          this.getTransitionDuration()
        );
      },
      markAsRead: function() {
        Livewire.emit("markedNotificationAsRead", notification.id);
      },
      markAsUnread: function() {
        Livewire.emit("markedNotificationAsUnread", notification.id);
      },
      getTransitionDuration: function() {
        return parseFloat(this.computedStyle.transitionDuration) * 1e3;
      }
    }));
  };

  // packages/notifications/resources/js/Notification.js
  var import_uuid_browser = __toESM(require_uuid_browser(), 1);
  var Notification = class {
    constructor() {
      this.id((0, import_uuid_browser.v4)());
      return this;
    }
    id(id) {
      this.id = id;
      return this;
    }
    title(title) {
      this.title = title;
      return this;
    }
    body(body) {
      this.body = body;
      return this;
    }
    actions(actions) {
      this.actions = actions;
      return this;
    }
    status(status) {
      switch (status) {
        case "danger":
          this.danger();
          break;
        case "info":
          this.info();
          break;
        case "success":
          this.success();
          break;
        case "warning":
          this.warning();
          break;
      }
      return this;
    }
    color(color) {
      this.color = color;
      return this;
    }
    icon(icon) {
      this.icon = icon;
      return this;
    }
    iconColor(color) {
      this.iconColor = color;
      return this;
    }
    duration(duration) {
      this.duration = duration;
      return this;
    }
    seconds(seconds) {
      this.duration(seconds * 1e3);
      return this;
    }
    persistent() {
      this.duration("persistent");
      return this;
    }
    danger() {
      this.icon("heroicon-o-x-circle");
      this.iconColor("danger");
      return this;
    }
    info() {
      this.icon("heroicon-o-information-circle");
      this.iconColor("info");
      return this;
    }
    success() {
      this.icon("heroicon-o-check-circle");
      this.iconColor("success");
      return this;
    }
    warning() {
      this.icon("heroicon-o-exclamation-circle");
      this.iconColor("warning");
      return this;
    }
    view(view) {
      this.view = view;
      return this;
    }
    viewData(viewData) {
      this.viewData = viewData;
      return this;
    }
    send() {
      Livewire.emit("notificationSent", this);
      return this;
    }
  };
  var Action = class {
    constructor(name) {
      this.name(name);
      return this;
    }
    name(name) {
      this.name = name;
      return this;
    }
    color(color) {
      this.color = color;
      return this;
    }
    emit(event, data) {
      this.event(event);
      this.eventData(data);
      return this;
    }
    emitSelf(event, data) {
      this.emit(event, data);
      this.emitDirection = "self";
      return this;
    }
    emitTo(component, event, data) {
      this.emit(event, data);
      this.emitDirection = "to";
      this.emitToComponent = component;
      return this;
    }
    emitUp(event, data) {
      this.emit(event, data);
      this.emitDirection = "up";
      return this;
    }
    emitDirection(emitDirection) {
      this.emitDirection = emitDirection;
      return this;
    }
    emitToComponent(component) {
      this.emitToComponent = component;
      return this;
    }
    event(event) {
      this.event = event;
      return this;
    }
    eventData(data) {
      this.eventData = data;
      return this;
    }
    extraAttributes(attributes) {
      this.extraAttributes = attributes;
      return this;
    }
    icon(icon) {
      this.icon = icon;
      return this;
    }
    iconPosition(position) {
      this.iconPosition = position;
      return this;
    }
    outlined(condition = true) {
      this.isOutlined = condition;
      return this;
    }
    disabled(condition = true) {
      this.isDisabled = condition;
      return this;
    }
    label(label) {
      this.label = label;
      return this;
    }
    close(condition = true) {
      this.shouldClose = condition;
      return this;
    }
    openUrlInNewTab(condition = true) {
      this.shouldOpenUrlInNewTab = condition;
      return this;
    }
    size(size) {
      this.size = size;
      return this;
    }
    url(url) {
      this.url = url;
      return this;
    }
    view(view) {
      this.view = view;
      return this;
    }
    button() {
      this.view("filament-notifications::actions.button-action");
      return this;
    }
    grouped() {
      this.view("filament-notifications::actions.grouped-action");
      return this;
    }
    link() {
      this.view("filament-notifications::actions.link-action");
      return this;
    }
  };
  var ActionGroup = class {
    constructor(actions) {
      this.actions(actions);
      return this;
    }
    actions(actions) {
      this.actions = actions.map((action) => action.grouped());
      return this;
    }
    color(color) {
      this.color = color;
      return this;
    }
    icon(icon) {
      this.icon = icon;
      return this;
    }
    iconPosition(position) {
      this.iconPosition = position;
      return this;
    }
    label(label) {
      this.label = label;
      return this;
    }
    tooltip(tooltip) {
      this.tooltip = tooltip;
      return this;
    }
  };

  // packages/notifications/resources/js/index.js
  window.NotificationAction = Action;
  window.NotificationActionGroup = ActionGroup;
  window.Notification = Notification;
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(notification_default);
  });
})();
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci9saWIvcm5nLWJyb3dzZXIuanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci9saWIvYnl0ZXNUb1V1aWQuanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci92MS5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvdXVpZC1icm93c2VyL3Y0LmpzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy91dWlkLWJyb3dzZXIvaW5kZXguanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2FscGluZWpzL3NyYy9tdXRhdGlvbi5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvYWxwaW5lanMvc3JjL3V0aWxzL29uY2UuanMiLCAiLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvbm90aWZpY2F0aW9uLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9Ob3RpZmljYXRpb24uanMiLCAiLi4vcmVzb3VyY2VzL2pzL2luZGV4LmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvLyBVbmlxdWUgSUQgY3JlYXRpb24gcmVxdWlyZXMgYSBoaWdoIHF1YWxpdHkgcmFuZG9tICMgZ2VuZXJhdG9yLiAgSW4gdGhlXG4vLyBicm93c2VyIHRoaXMgaXMgYSBsaXR0bGUgY29tcGxpY2F0ZWQgZHVlIHRvIHVua25vd24gcXVhbGl0eSBvZiBNYXRoLnJhbmRvbSgpXG4vLyBhbmQgaW5jb25zaXN0ZW50IHN1cHBvcnQgZm9yIHRoZSBgY3J5cHRvYCBBUEkuICBXZSBkbyB0aGUgYmVzdCB3ZSBjYW4gdmlhXG4vLyBmZWF0dXJlLWRldGVjdGlvblxudmFyIHJuZztcblxudmFyIGNyeXB0byA9IHR5cGVvZiBnbG9iYWwgIT09ICd1bmRlZmluZWQnICYmIChnbG9iYWwuY3J5cHRvIHx8IGdsb2JhbC5tc0NyeXB0byk7IC8vIGZvciBJRSAxMVxuaWYgKGNyeXB0byAmJiBjcnlwdG8uZ2V0UmFuZG9tVmFsdWVzKSB7XG4gIC8vIFdIQVRXRyBjcnlwdG8gUk5HIC0gaHR0cDovL3dpa2kud2hhdHdnLm9yZy93aWtpL0NyeXB0b1xuICB2YXIgcm5kczggPSBuZXcgVWludDhBcnJheSgxNik7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcbiAgcm5nID0gZnVuY3Rpb24gd2hhdHdnUk5HKCkge1xuICAgIGNyeXB0by5nZXRSYW5kb21WYWx1ZXMocm5kczgpO1xuICAgIHJldHVybiBybmRzODtcbiAgfTtcbn1cblxuaWYgKCFybmcpIHtcbiAgLy8gTWF0aC5yYW5kb20oKS1iYXNlZCAoUk5HKVxuICAvL1xuICAvLyBJZiBhbGwgZWxzZSBmYWlscywgdXNlIE1hdGgucmFuZG9tKCkuICBJdCdzIGZhc3QsIGJ1dCBpcyBvZiB1bnNwZWNpZmllZFxuICAvLyBxdWFsaXR5LlxuICB2YXIgcm5kcyA9IG5ldyBBcnJheSgxNik7XG4gIHJuZyA9IGZ1bmN0aW9uKCkge1xuICAgIGZvciAodmFyIGkgPSAwLCByOyBpIDwgMTY7IGkrKykge1xuICAgICAgaWYgKChpICYgMHgwMykgPT09IDApIHIgPSBNYXRoLnJhbmRvbSgpICogMHgxMDAwMDAwMDA7XG4gICAgICBybmRzW2ldID0gciA+Pj4gKChpICYgMHgwMykgPDwgMykgJiAweGZmO1xuICAgIH1cblxuICAgIHJldHVybiBybmRzO1xuICB9O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IHJuZztcbiIsICIvKipcbiAqIENvbnZlcnQgYXJyYXkgb2YgMTYgYnl0ZSB2YWx1ZXMgdG8gVVVJRCBzdHJpbmcgZm9ybWF0IG9mIHRoZSBmb3JtOlxuICogWFhYWFhYWFgtWFhYWC1YWFhYLVhYWFgtWFhYWFhYWFhYWFhYXG4gKi9cbnZhciBieXRlVG9IZXggPSBbXTtcbmZvciAodmFyIGkgPSAwOyBpIDwgMjU2OyArK2kpIHtcbiAgYnl0ZVRvSGV4W2ldID0gKGkgKyAweDEwMCkudG9TdHJpbmcoMTYpLnN1YnN0cigxKTtcbn1cblxuZnVuY3Rpb24gYnl0ZXNUb1V1aWQoYnVmLCBvZmZzZXQpIHtcbiAgdmFyIGkgPSBvZmZzZXQgfHwgMDtcbiAgdmFyIGJ0aCA9IGJ5dGVUb0hleDtcbiAgcmV0dXJuIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXSArICctJyArXG4gICAgICAgICAgYnRoW2J1ZltpKytdXSArIGJ0aFtidWZbaSsrXV0gKyAnLScgK1xuICAgICAgICAgIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICsgJy0nICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXSArICctJyArXG4gICAgICAgICAgYnRoW2J1ZltpKytdXSArIGJ0aFtidWZbaSsrXV0gK1xuICAgICAgICAgIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBieXRlc1RvVXVpZDtcbiIsICJ2YXIgcm5nID0gcmVxdWlyZSgnLi9saWIvcm5nLWJyb3dzZXInKTtcbnZhciBieXRlc1RvVXVpZCA9IHJlcXVpcmUoJy4vbGliL2J5dGVzVG9VdWlkJyk7XG5cbi8vICoqYHYxKClgIC0gR2VuZXJhdGUgdGltZS1iYXNlZCBVVUlEKipcbi8vXG4vLyBJbnNwaXJlZCBieSBodHRwczovL2dpdGh1Yi5jb20vTGlvc0svVVVJRC5qc1xuLy8gYW5kIGh0dHA6Ly9kb2NzLnB5dGhvbi5vcmcvbGlicmFyeS91dWlkLmh0bWxcblxuLy8gcmFuZG9tICMncyB3ZSBuZWVkIHRvIGluaXQgbm9kZSBhbmQgY2xvY2tzZXFcbnZhciBfc2VlZEJ5dGVzID0gcm5nKCk7XG5cbi8vIFBlciA0LjUsIGNyZWF0ZSBhbmQgNDgtYml0IG5vZGUgaWQsICg0NyByYW5kb20gYml0cyArIG11bHRpY2FzdCBiaXQgPSAxKVxudmFyIF9ub2RlSWQgPSBbXG4gIF9zZWVkQnl0ZXNbMF0gfCAweDAxLFxuICBfc2VlZEJ5dGVzWzFdLCBfc2VlZEJ5dGVzWzJdLCBfc2VlZEJ5dGVzWzNdLCBfc2VlZEJ5dGVzWzRdLCBfc2VlZEJ5dGVzWzVdXG5dO1xuXG4vLyBQZXIgNC4yLjIsIHJhbmRvbWl6ZSAoMTQgYml0KSBjbG9ja3NlcVxudmFyIF9jbG9ja3NlcSA9IChfc2VlZEJ5dGVzWzZdIDw8IDggfCBfc2VlZEJ5dGVzWzddKSAmIDB4M2ZmZjtcblxuLy8gUHJldmlvdXMgdXVpZCBjcmVhdGlvbiB0aW1lXG52YXIgX2xhc3RNU2VjcyA9IDAsIF9sYXN0TlNlY3MgPSAwO1xuXG4vLyBTZWUgaHR0cHM6Ly9naXRodWIuY29tL2Jyb29mYS9ub2RlLXV1aWQgZm9yIEFQSSBkZXRhaWxzXG5mdW5jdGlvbiB2MShvcHRpb25zLCBidWYsIG9mZnNldCkge1xuICB2YXIgaSA9IGJ1ZiAmJiBvZmZzZXQgfHwgMDtcbiAgdmFyIGIgPSBidWYgfHwgW107XG5cbiAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cbiAgdmFyIGNsb2Nrc2VxID0gb3B0aW9ucy5jbG9ja3NlcSAhPT0gdW5kZWZpbmVkID8gb3B0aW9ucy5jbG9ja3NlcSA6IF9jbG9ja3NlcTtcblxuICAvLyBVVUlEIHRpbWVzdGFtcHMgYXJlIDEwMCBuYW5vLXNlY29uZCB1bml0cyBzaW5jZSB0aGUgR3JlZ29yaWFuIGVwb2NoLFxuICAvLyAoMTU4Mi0xMC0xNSAwMDowMCkuICBKU051bWJlcnMgYXJlbid0IHByZWNpc2UgZW5vdWdoIGZvciB0aGlzLCBzb1xuICAvLyB0aW1lIGlzIGhhbmRsZWQgaW50ZXJuYWxseSBhcyAnbXNlY3MnIChpbnRlZ2VyIG1pbGxpc2Vjb25kcykgYW5kICduc2VjcydcbiAgLy8gKDEwMC1uYW5vc2Vjb25kcyBvZmZzZXQgZnJvbSBtc2Vjcykgc2luY2UgdW5peCBlcG9jaCwgMTk3MC0wMS0wMSAwMDowMC5cbiAgdmFyIG1zZWNzID0gb3B0aW9ucy5tc2VjcyAhPT0gdW5kZWZpbmVkID8gb3B0aW9ucy5tc2VjcyA6IG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xuXG4gIC8vIFBlciA0LjIuMS4yLCB1c2UgY291bnQgb2YgdXVpZCdzIGdlbmVyYXRlZCBkdXJpbmcgdGhlIGN1cnJlbnQgY2xvY2tcbiAgLy8gY3ljbGUgdG8gc2ltdWxhdGUgaGlnaGVyIHJlc29sdXRpb24gY2xvY2tcbiAgdmFyIG5zZWNzID0gb3B0aW9ucy5uc2VjcyAhPT0gdW5kZWZpbmVkID8gb3B0aW9ucy5uc2VjcyA6IF9sYXN0TlNlY3MgKyAxO1xuXG4gIC8vIFRpbWUgc2luY2UgbGFzdCB1dWlkIGNyZWF0aW9uIChpbiBtc2VjcylcbiAgdmFyIGR0ID0gKG1zZWNzIC0gX2xhc3RNU2VjcykgKyAobnNlY3MgLSBfbGFzdE5TZWNzKS8xMDAwMDtcblxuICAvLyBQZXIgNC4yLjEuMiwgQnVtcCBjbG9ja3NlcSBvbiBjbG9jayByZWdyZXNzaW9uXG4gIGlmIChkdCA8IDAgJiYgb3B0aW9ucy5jbG9ja3NlcSA9PT0gdW5kZWZpbmVkKSB7XG4gICAgY2xvY2tzZXEgPSBjbG9ja3NlcSArIDEgJiAweDNmZmY7XG4gIH1cblxuICAvLyBSZXNldCBuc2VjcyBpZiBjbG9jayByZWdyZXNzZXMgKG5ldyBjbG9ja3NlcSkgb3Igd2UndmUgbW92ZWQgb250byBhIG5ld1xuICAvLyB0aW1lIGludGVydmFsXG4gIGlmICgoZHQgPCAwIHx8IG1zZWNzID4gX2xhc3RNU2VjcykgJiYgb3B0aW9ucy5uc2VjcyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgbnNlY3MgPSAwO1xuICB9XG5cbiAgLy8gUGVyIDQuMi4xLjIgVGhyb3cgZXJyb3IgaWYgdG9vIG1hbnkgdXVpZHMgYXJlIHJlcXVlc3RlZFxuICBpZiAobnNlY3MgPj0gMTAwMDApIHtcbiAgICB0aHJvdyBuZXcgRXJyb3IoJ3V1aWQudjEoKTogQ2FuXFwndCBjcmVhdGUgbW9yZSB0aGFuIDEwTSB1dWlkcy9zZWMnKTtcbiAgfVxuXG4gIF9sYXN0TVNlY3MgPSBtc2VjcztcbiAgX2xhc3ROU2VjcyA9IG5zZWNzO1xuICBfY2xvY2tzZXEgPSBjbG9ja3NlcTtcblxuICAvLyBQZXIgNC4xLjQgLSBDb252ZXJ0IGZyb20gdW5peCBlcG9jaCB0byBHcmVnb3JpYW4gZXBvY2hcbiAgbXNlY3MgKz0gMTIyMTkyOTI4MDAwMDA7XG5cbiAgLy8gYHRpbWVfbG93YFxuICB2YXIgdGwgPSAoKG1zZWNzICYgMHhmZmZmZmZmKSAqIDEwMDAwICsgbnNlY3MpICUgMHgxMDAwMDAwMDA7XG4gIGJbaSsrXSA9IHRsID4+PiAyNCAmIDB4ZmY7XG4gIGJbaSsrXSA9IHRsID4+PiAxNiAmIDB4ZmY7XG4gIGJbaSsrXSA9IHRsID4+PiA4ICYgMHhmZjtcbiAgYltpKytdID0gdGwgJiAweGZmO1xuXG4gIC8vIGB0aW1lX21pZGBcbiAgdmFyIHRtaCA9IChtc2VjcyAvIDB4MTAwMDAwMDAwICogMTAwMDApICYgMHhmZmZmZmZmO1xuICBiW2krK10gPSB0bWggPj4+IDggJiAweGZmO1xuICBiW2krK10gPSB0bWggJiAweGZmO1xuXG4gIC8vIGB0aW1lX2hpZ2hfYW5kX3ZlcnNpb25gXG4gIGJbaSsrXSA9IHRtaCA+Pj4gMjQgJiAweGYgfCAweDEwOyAvLyBpbmNsdWRlIHZlcnNpb25cbiAgYltpKytdID0gdG1oID4+PiAxNiAmIDB4ZmY7XG5cbiAgLy8gYGNsb2NrX3NlcV9oaV9hbmRfcmVzZXJ2ZWRgIChQZXIgNC4yLjIgLSBpbmNsdWRlIHZhcmlhbnQpXG4gIGJbaSsrXSA9IGNsb2Nrc2VxID4+PiA4IHwgMHg4MDtcblxuICAvLyBgY2xvY2tfc2VxX2xvd2BcbiAgYltpKytdID0gY2xvY2tzZXEgJiAweGZmO1xuXG4gIC8vIGBub2RlYFxuICB2YXIgbm9kZSA9IG9wdGlvbnMubm9kZSB8fCBfbm9kZUlkO1xuICBmb3IgKHZhciBuID0gMDsgbiA8IDY7ICsrbikge1xuICAgIGJbaSArIG5dID0gbm9kZVtuXTtcbiAgfVxuXG4gIHJldHVybiBidWYgPyBidWYgOiBieXRlc1RvVXVpZChiKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSB2MTtcbiIsICJ2YXIgcm5nID0gcmVxdWlyZSgnLi9saWIvcm5nLWJyb3dzZXInKTtcbnZhciBieXRlc1RvVXVpZCA9IHJlcXVpcmUoJy4vbGliL2J5dGVzVG9VdWlkJyk7XG5cbmZ1bmN0aW9uIHY0KG9wdGlvbnMsIGJ1Ziwgb2Zmc2V0KSB7XG4gIHZhciBpID0gYnVmICYmIG9mZnNldCB8fCAwO1xuXG4gIGlmICh0eXBlb2Yob3B0aW9ucykgPT0gJ3N0cmluZycpIHtcbiAgICBidWYgPSBvcHRpb25zID09ICdiaW5hcnknID8gbmV3IEFycmF5KDE2KSA6IG51bGw7XG4gICAgb3B0aW9ucyA9IG51bGw7XG4gIH1cbiAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cbiAgdmFyIHJuZHMgPSBvcHRpb25zLnJhbmRvbSB8fCAob3B0aW9ucy5ybmcgfHwgcm5nKSgpO1xuXG4gIC8vIFBlciA0LjQsIHNldCBiaXRzIGZvciB2ZXJzaW9uIGFuZCBgY2xvY2tfc2VxX2hpX2FuZF9yZXNlcnZlZGBcbiAgcm5kc1s2XSA9IChybmRzWzZdICYgMHgwZikgfCAweDQwO1xuICBybmRzWzhdID0gKHJuZHNbOF0gJiAweDNmKSB8IDB4ODA7XG5cbiAgLy8gQ29weSBieXRlcyB0byBidWZmZXIsIGlmIHByb3ZpZGVkXG4gIGlmIChidWYpIHtcbiAgICBmb3IgKHZhciBpaSA9IDA7IGlpIDwgMTY7ICsraWkpIHtcbiAgICAgIGJ1ZltpICsgaWldID0gcm5kc1tpaV07XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGJ1ZiB8fCBieXRlc1RvVXVpZChybmRzKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSB2NDtcbiIsICJ2YXIgdjEgPSByZXF1aXJlKCcuL3YxJyk7XG52YXIgdjQgPSByZXF1aXJlKCcuL3Y0Jyk7XG5cbnZhciB1dWlkID0gdjQ7XG51dWlkLnYxID0gdjE7XG51dWlkLnY0ID0gdjQ7XG5cbm1vZHVsZS5leHBvcnRzID0gdXVpZDtcbiIsICJsZXQgb25BdHRyaWJ1dGVBZGRlZHMgPSBbXVxubGV0IG9uRWxSZW1vdmVkcyA9IFtdXG5sZXQgb25FbEFkZGVkcyA9IFtdXG5cbmV4cG9ydCBmdW5jdGlvbiBvbkVsQWRkZWQoY2FsbGJhY2spIHtcbiAgICBvbkVsQWRkZWRzLnB1c2goY2FsbGJhY2spXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvbkVsUmVtb3ZlZChlbCwgY2FsbGJhY2spIHtcbiAgICBpZiAodHlwZW9mIGNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIGlmICghIGVsLl94X2NsZWFudXBzKSBlbC5feF9jbGVhbnVwcyA9IFtdXG4gICAgICAgIGVsLl94X2NsZWFudXBzLnB1c2goY2FsbGJhY2spXG4gICAgfSBlbHNlIHtcbiAgICAgICAgY2FsbGJhY2sgPSBlbFxuICAgICAgICBvbkVsUmVtb3ZlZHMucHVzaChjYWxsYmFjaylcbiAgICB9XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvbkF0dHJpYnV0ZXNBZGRlZChjYWxsYmFjaykge1xuICAgIG9uQXR0cmlidXRlQWRkZWRzLnB1c2goY2FsbGJhY2spXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvbkF0dHJpYnV0ZVJlbW92ZWQoZWwsIG5hbWUsIGNhbGxiYWNrKSB7XG4gICAgaWYgKCEgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHMpIGVsLl94X2F0dHJpYnV0ZUNsZWFudXBzID0ge31cbiAgICBpZiAoISBlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXSkgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHNbbmFtZV0gPSBbXVxuXG4gICAgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHNbbmFtZV0ucHVzaChjYWxsYmFjaylcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGNsZWFudXBBdHRyaWJ1dGVzKGVsLCBuYW1lcykge1xuICAgIGlmICghIGVsLl94X2F0dHJpYnV0ZUNsZWFudXBzKSByZXR1cm5cblxuICAgIE9iamVjdC5lbnRyaWVzKGVsLl94X2F0dHJpYnV0ZUNsZWFudXBzKS5mb3JFYWNoKChbbmFtZSwgdmFsdWVdKSA9PiB7XG4gICAgICAgIGlmIChuYW1lcyA9PT0gdW5kZWZpbmVkIHx8IG5hbWVzLmluY2x1ZGVzKG5hbWUpKSB7XG4gICAgICAgICAgICB2YWx1ZS5mb3JFYWNoKGkgPT4gaSgpKVxuXG4gICAgICAgICAgICBkZWxldGUgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHNbbmFtZV1cbiAgICAgICAgfVxuICAgIH0pXG59XG5cbmxldCBvYnNlcnZlciA9IG5ldyBNdXRhdGlvbk9ic2VydmVyKG9uTXV0YXRlKVxuXG5sZXQgY3VycmVudGx5T2JzZXJ2aW5nID0gZmFsc2VcblxuZXhwb3J0IGZ1bmN0aW9uIHN0YXJ0T2JzZXJ2aW5nTXV0YXRpb25zKCkge1xuICAgIG9ic2VydmVyLm9ic2VydmUoZG9jdW1lbnQsIHsgc3VidHJlZTogdHJ1ZSwgY2hpbGRMaXN0OiB0cnVlLCBhdHRyaWJ1dGVzOiB0cnVlLCBhdHRyaWJ1dGVPbGRWYWx1ZTogdHJ1ZSB9KVxuXG4gICAgY3VycmVudGx5T2JzZXJ2aW5nID0gdHJ1ZVxufVxuXG5leHBvcnQgZnVuY3Rpb24gc3RvcE9ic2VydmluZ011dGF0aW9ucygpIHtcbiAgICBmbHVzaE9ic2VydmVyKClcblxuICAgIG9ic2VydmVyLmRpc2Nvbm5lY3QoKVxuXG4gICAgY3VycmVudGx5T2JzZXJ2aW5nID0gZmFsc2Vcbn1cblxubGV0IHJlY29yZFF1ZXVlID0gW11cbmxldCB3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlID0gZmFsc2VcblxuZXhwb3J0IGZ1bmN0aW9uIGZsdXNoT2JzZXJ2ZXIoKSB7XG4gICAgcmVjb3JkUXVldWUgPSByZWNvcmRRdWV1ZS5jb25jYXQob2JzZXJ2ZXIudGFrZVJlY29yZHMoKSlcblxuICAgIGlmIChyZWNvcmRRdWV1ZS5sZW5ndGggJiYgISB3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlKSB7XG4gICAgICAgIHdpbGxQcm9jZXNzUmVjb3JkUXVldWUgPSB0cnVlXG5cbiAgICAgICAgcXVldWVNaWNyb3Rhc2soKCkgPT4ge1xuICAgICAgICAgICAgcHJvY2Vzc1JlY29yZFF1ZXVlKClcblxuICAgICAgICAgICAgd2lsbFByb2Nlc3NSZWNvcmRRdWV1ZSA9IGZhbHNlXG4gICAgICAgIH0pXG4gICAgfVxufVxuXG5mdW5jdGlvbiBwcm9jZXNzUmVjb3JkUXVldWUoKSB7XG4gICAgIG9uTXV0YXRlKHJlY29yZFF1ZXVlKVxuXG4gICAgIHJlY29yZFF1ZXVlLmxlbmd0aCA9IDBcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIG11dGF0ZURvbShjYWxsYmFjaykge1xuICAgIGlmICghIGN1cnJlbnRseU9ic2VydmluZykgcmV0dXJuIGNhbGxiYWNrKClcblxuICAgIHN0b3BPYnNlcnZpbmdNdXRhdGlvbnMoKVxuXG4gICAgbGV0IHJlc3VsdCA9IGNhbGxiYWNrKClcblxuICAgIHN0YXJ0T2JzZXJ2aW5nTXV0YXRpb25zKClcblxuICAgIHJldHVybiByZXN1bHRcbn1cblxubGV0IGlzQ29sbGVjdGluZyA9IGZhbHNlXG5sZXQgZGVmZXJyZWRNdXRhdGlvbnMgPSBbXVxuXG5leHBvcnQgZnVuY3Rpb24gZGVmZXJNdXRhdGlvbnMoKSB7XG4gICAgaXNDb2xsZWN0aW5nID0gdHJ1ZVxufVxuXG5leHBvcnQgZnVuY3Rpb24gZmx1c2hBbmRTdG9wRGVmZXJyaW5nTXV0YXRpb25zKCkge1xuICAgIGlzQ29sbGVjdGluZyA9IGZhbHNlXG5cbiAgICBvbk11dGF0ZShkZWZlcnJlZE11dGF0aW9ucylcblxuICAgIGRlZmVycmVkTXV0YXRpb25zID0gW11cbn1cblxuZnVuY3Rpb24gb25NdXRhdGUobXV0YXRpb25zKSB7XG4gICAgaWYgKGlzQ29sbGVjdGluZykge1xuICAgICAgICBkZWZlcnJlZE11dGF0aW9ucyA9IGRlZmVycmVkTXV0YXRpb25zLmNvbmNhdChtdXRhdGlvbnMpXG5cbiAgICAgICAgcmV0dXJuXG4gICAgfVxuXG4gICAgbGV0IGFkZGVkTm9kZXMgPSBbXVxuICAgIGxldCByZW1vdmVkTm9kZXMgPSBbXVxuICAgIGxldCBhZGRlZEF0dHJpYnV0ZXMgPSBuZXcgTWFwXG4gICAgbGV0IHJlbW92ZWRBdHRyaWJ1dGVzID0gbmV3IE1hcFxuXG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBtdXRhdGlvbnMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgaWYgKG11dGF0aW9uc1tpXS50YXJnZXQuX3hfaWdub3JlTXV0YXRpb25PYnNlcnZlcikgY29udGludWVcblxuICAgICAgICBpZiAobXV0YXRpb25zW2ldLnR5cGUgPT09ICdjaGlsZExpc3QnKSB7XG4gICAgICAgICAgICBtdXRhdGlvbnNbaV0uYWRkZWROb2Rlcy5mb3JFYWNoKG5vZGUgPT4gbm9kZS5ub2RlVHlwZSA9PT0gMSAmJiBhZGRlZE5vZGVzLnB1c2gobm9kZSkpXG4gICAgICAgICAgICBtdXRhdGlvbnNbaV0ucmVtb3ZlZE5vZGVzLmZvckVhY2gobm9kZSA9PiBub2RlLm5vZGVUeXBlID09PSAxICYmIHJlbW92ZWROb2Rlcy5wdXNoKG5vZGUpKVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKG11dGF0aW9uc1tpXS50eXBlID09PSAnYXR0cmlidXRlcycpIHtcbiAgICAgICAgICAgIGxldCBlbCA9IG11dGF0aW9uc1tpXS50YXJnZXRcbiAgICAgICAgICAgIGxldCBuYW1lID0gbXV0YXRpb25zW2ldLmF0dHJpYnV0ZU5hbWVcbiAgICAgICAgICAgIGxldCBvbGRWYWx1ZSA9IG11dGF0aW9uc1tpXS5vbGRWYWx1ZVxuXG4gICAgICAgICAgICBsZXQgYWRkID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgIGlmICghIGFkZGVkQXR0cmlidXRlcy5oYXMoZWwpKSBhZGRlZEF0dHJpYnV0ZXMuc2V0KGVsLCBbXSlcblxuICAgICAgICAgICAgICAgIGFkZGVkQXR0cmlidXRlcy5nZXQoZWwpLnB1c2goeyBuYW1lLCAgdmFsdWU6IGVsLmdldEF0dHJpYnV0ZShuYW1lKSB9KVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBsZXQgcmVtb3ZlID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgIGlmICghIHJlbW92ZWRBdHRyaWJ1dGVzLmhhcyhlbCkpIHJlbW92ZWRBdHRyaWJ1dGVzLnNldChlbCwgW10pXG5cbiAgICAgICAgICAgICAgICByZW1vdmVkQXR0cmlidXRlcy5nZXQoZWwpLnB1c2gobmFtZSlcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gTmV3IGF0dHJpYnV0ZS5cbiAgICAgICAgICAgIGlmIChlbC5oYXNBdHRyaWJ1dGUobmFtZSkgJiYgb2xkVmFsdWUgPT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICBhZGQoKVxuICAgICAgICAgICAgLy8gQ2hhbmdlZCBhdHR0cmlidXRlLlxuICAgICAgICAgICAgfSBlbHNlIGlmIChlbC5oYXNBdHRyaWJ1dGUobmFtZSkpIHtcbiAgICAgICAgICAgICAgICByZW1vdmUoKVxuICAgICAgICAgICAgICAgIGFkZCgpXG4gICAgICAgICAgICAvLyBSZW1vdmVkIGF0dHRyaWJ1dGUuXG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHJlbW92ZSgpXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICByZW1vdmVkQXR0cmlidXRlcy5mb3JFYWNoKChhdHRycywgZWwpID0+IHtcbiAgICAgICAgY2xlYW51cEF0dHJpYnV0ZXMoZWwsIGF0dHJzKVxuICAgIH0pXG5cbiAgICBhZGRlZEF0dHJpYnV0ZXMuZm9yRWFjaCgoYXR0cnMsIGVsKSA9PiB7XG4gICAgICAgIG9uQXR0cmlidXRlQWRkZWRzLmZvckVhY2goaSA9PiBpKGVsLCBhdHRycykpXG4gICAgfSlcblxuICAgIGZvciAobGV0IG5vZGUgb2YgcmVtb3ZlZE5vZGVzKSB7XG4gICAgICAgIC8vIElmIGFuIGVsZW1lbnQgZ2V0cyBtb3ZlZCBvbiBhIHBhZ2UsIGl0J3MgcmVnaXN0ZXJlZFxuICAgICAgICAvLyBhcyBib3RoIGFuIFwiYWRkXCIgYW5kIFwicmVtb3ZlXCIsIHNvIHdlIHdhbnQgdG8gc2tpcCB0aG9zZS5cbiAgICAgICAgaWYgKGFkZGVkTm9kZXMuaW5jbHVkZXMobm9kZSkpIGNvbnRpbnVlXG5cbiAgICAgICAgb25FbFJlbW92ZWRzLmZvckVhY2goaSA9PiBpKG5vZGUpKVxuXG4gICAgICAgIGlmIChub2RlLl94X2NsZWFudXBzKSB7XG4gICAgICAgICAgICB3aGlsZSAobm9kZS5feF9jbGVhbnVwcy5sZW5ndGgpIG5vZGUuX3hfY2xlYW51cHMucG9wKCkoKVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgLy8gTXV0YXRpb25zIGFyZSBidW5kbGVkIHRvZ2V0aGVyIGJ5IHRoZSBicm93c2VyIGJ1dCBzb21ldGltZXNcbiAgICAvLyBmb3IgY29tcGxleCBjYXNlcywgdGhlcmUgbWF5IGJlIGphdmFzY3JpcHQgY29kZSBhZGRpbmcgYSB3cmFwcGVyXG4gICAgLy8gYW5kIHRoZW4gYW4gYWxwaW5lIGNvbXBvbmVudCBhcyBhIGNoaWxkIG9mIHRoYXQgd3JhcHBlciBpbiB0aGUgc2FtZVxuICAgIC8vIGZ1bmN0aW9uIGFuZCB0aGUgbXV0YXRpb24gb2JzZXJ2ZXIgd2lsbCByZWNlaXZlIDIgZGlmZmVyZW50IG11dGF0aW9ucy5cbiAgICAvLyB3aGVuIGl0IGNvbWVzIHRpbWUgdG8gcnVuIHRoZW0sIHRoZSBkb20gY29udGFpbnMgYm90aCBjaGFuZ2VzIHNvIHRoZSBjaGlsZFxuICAgIC8vIGVsZW1lbnQgd291bGQgYmUgcHJvY2Vzc2VkIHR3aWNlIGFzIEFscGluZSBjYWxscyBpbml0VHJlZSBvblxuICAgIC8vIGJvdGggbXV0YXRpb25zLiBXZSBtYXJrIGFsbCBub2RlcyBhcyBfeF9pZ25vcmVkIGFuZCBvbmx5IHJlbW92ZSB0aGUgZmxhZ1xuICAgIC8vIHdoZW4gcHJvY2Vzc2luZyB0aGUgbm9kZSB0byBhdm9pZCB0aG9zZSBkdXBsaWNhdGVzLlxuICAgIGFkZGVkTm9kZXMuZm9yRWFjaCgobm9kZSkgPT4ge1xuICAgICAgICBub2RlLl94X2lnbm9yZVNlbGYgPSB0cnVlXG4gICAgICAgIG5vZGUuX3hfaWdub3JlID0gdHJ1ZVxuICAgIH0pXG4gICAgZm9yIChsZXQgbm9kZSBvZiBhZGRlZE5vZGVzKSB7XG4gICAgICAgIC8vIElmIGFuIGVsZW1lbnQgZ2V0cyBtb3ZlZCBvbiBhIHBhZ2UsIGl0J3MgcmVnaXN0ZXJlZFxuICAgICAgICAvLyBhcyBib3RoIGFuIFwiYWRkXCIgYW5kIFwicmVtb3ZlXCIsIHNvIHdlIHdhbnQgdG8gc2tpcCB0aG9zZS5cbiAgICAgICAgaWYgKHJlbW92ZWROb2Rlcy5pbmNsdWRlcyhub2RlKSkgY29udGludWVcblxuICAgICAgICAvLyBJZiB0aGUgbm9kZSB3YXMgZXZlbnR1YWxseSByZW1vdmVkIGFzIHBhcnQgb2Ygb25lIG9mIGhpc1xuICAgICAgICAvLyBwYXJlbnQgbXV0YXRpb25zLCBza2lwIGl0XG4gICAgICAgIGlmICghIG5vZGUuaXNDb25uZWN0ZWQpIGNvbnRpbnVlXG5cbiAgICAgICAgZGVsZXRlIG5vZGUuX3hfaWdub3JlU2VsZlxuICAgICAgICBkZWxldGUgbm9kZS5feF9pZ25vcmVcbiAgICAgICAgb25FbEFkZGVkcy5mb3JFYWNoKGkgPT4gaShub2RlKSlcbiAgICAgICAgbm9kZS5feF9pZ25vcmUgPSB0cnVlXG4gICAgICAgIG5vZGUuX3hfaWdub3JlU2VsZiA9IHRydWVcbiAgICB9XG4gICAgYWRkZWROb2Rlcy5mb3JFYWNoKChub2RlKSA9PiB7XG4gICAgICAgIGRlbGV0ZSBub2RlLl94X2lnbm9yZVNlbGZcbiAgICAgICAgZGVsZXRlIG5vZGUuX3hfaWdub3JlXG4gICAgfSlcblxuICAgIGFkZGVkTm9kZXMgPSBudWxsXG4gICAgcmVtb3ZlZE5vZGVzID0gbnVsbFxuICAgIGFkZGVkQXR0cmlidXRlcyA9IG51bGxcbiAgICByZW1vdmVkQXR0cmlidXRlcyA9IG51bGxcbn1cbiIsICJcbmV4cG9ydCBmdW5jdGlvbiBvbmNlKGNhbGxiYWNrLCBmYWxsYmFjayA9ICgpID0+IHt9KSB7XG4gICAgbGV0IGNhbGxlZCA9IGZhbHNlXG5cbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgICBpZiAoISBjYWxsZWQpIHtcbiAgICAgICAgICAgIGNhbGxlZCA9IHRydWVcblxuICAgICAgICAgICAgY2FsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgZmFsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKVxuICAgICAgICB9XG4gICAgfVxufVxuIiwgImltcG9ydCB7IG11dGF0ZURvbSB9IGZyb20gJ2FscGluZWpzL3NyYy9tdXRhdGlvbidcbmltcG9ydCB7IG9uY2UgfSBmcm9tICdhbHBpbmVqcy9zcmMvdXRpbHMvb25jZSdcblxuZXhwb3J0IGRlZmF1bHQgKEFscGluZSkgPT4ge1xuICAgIEFscGluZS5kYXRhKCdub3RpZmljYXRpb25Db21wb25lbnQnLCAoeyBub3RpZmljYXRpb24gfSkgPT4gKHtcbiAgICAgICAgaXNTaG93bjogZmFsc2UsXG5cbiAgICAgICAgY29tcHV0ZWRTdHlsZTogbnVsbCxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLmNvbXB1dGVkU3R5bGUgPSB3aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZSh0aGlzLiRlbClcblxuICAgICAgICAgICAgdGhpcy5jb25maWd1cmVUcmFuc2l0aW9ucygpXG4gICAgICAgICAgICB0aGlzLmNvbmZpZ3VyZUFuaW1hdGlvbnMoKVxuXG4gICAgICAgICAgICBpZiAoXG4gICAgICAgICAgICAgICAgbm90aWZpY2F0aW9uLmR1cmF0aW9uICYmXG4gICAgICAgICAgICAgICAgbm90aWZpY2F0aW9uLmR1cmF0aW9uICE9PSAncGVyc2lzdGVudCdcbiAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4gdGhpcy5jbG9zZSgpLCBub3RpZmljYXRpb24uZHVyYXRpb24pXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuaXNTaG93biA9IHRydWVcbiAgICAgICAgfSxcblxuICAgICAgICBjb25maWd1cmVUcmFuc2l0aW9uczogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgY29uc3QgZGlzcGxheSA9IHRoaXMuY29tcHV0ZWRTdHlsZS5kaXNwbGF5XG5cbiAgICAgICAgICAgIGNvbnN0IHNob3cgPSAoKSA9PiB7XG4gICAgICAgICAgICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuc3R5bGUuc2V0UHJvcGVydHkoJ2Rpc3BsYXknLCBkaXNwbGF5KVxuICAgICAgICAgICAgICAgICAgICB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgndmlzaWJpbGl0eScsICd2aXNpYmxlJylcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIHRoaXMuJGVsLl94X2lzU2hvd24gPSB0cnVlXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IGhpZGUgPSAoKSA9PiB7XG4gICAgICAgICAgICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuX3hfaXNTaG93blxuICAgICAgICAgICAgICAgICAgICAgICAgPyB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgndmlzaWJpbGl0eScsICdoaWRkZW4nKVxuICAgICAgICAgICAgICAgICAgICAgICAgOiB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgnZGlzcGxheScsICdub25lJylcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCB0b2dnbGUgPSBvbmNlKFxuICAgICAgICAgICAgICAgICh2YWx1ZSkgPT4gKHZhbHVlID8gc2hvdygpIDogaGlkZSgpKSxcbiAgICAgICAgICAgICAgICAodmFsdWUpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyhcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuJGVsLFxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWUsXG4gICAgICAgICAgICAgICAgICAgICAgICBzaG93LFxuICAgICAgICAgICAgICAgICAgICAgICAgaGlkZSxcbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICApXG5cbiAgICAgICAgICAgIEFscGluZS5lZmZlY3QoKCkgPT4gdG9nZ2xlKHRoaXMuaXNTaG93bikpXG4gICAgICAgIH0sXG5cbiAgICAgICAgY29uZmlndXJlQW5pbWF0aW9uczogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0IGFuaW1hdGlvblxuXG4gICAgICAgICAgICBMaXZld2lyZS5ob29rKCdtZXNzYWdlLnJlY2VpdmVkJywgKF8sIGNvbXBvbmVudCkgPT4ge1xuICAgICAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICAgICAgIWNvbXBvbmVudC5zZXJ2ZXJNZW1vLmRhdGEuaXNGaWxhbWVudE5vdGlmaWNhdGlvbnNDb21wb25lbnRcbiAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgY29uc3QgZ2V0VG9wID0gKCkgPT4gdGhpcy4kZWwuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkudG9wXG4gICAgICAgICAgICAgICAgY29uc3Qgb2xkVG9wID0gZ2V0VG9wKClcblxuICAgICAgICAgICAgICAgIGFuaW1hdGlvbiA9ICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuYW5pbWF0ZShcbiAgICAgICAgICAgICAgICAgICAgICAgIFtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB7IHRyYW5zZm9ybTogYHRyYW5zbGF0ZVkoJHtvbGRUb3AgLSBnZXRUb3AoKX1weClgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgeyB0cmFuc2Zvcm06ICd0cmFuc2xhdGVZKDBweCknIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICBdLFxuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGR1cmF0aW9uOiB0aGlzLmdldFRyYW5zaXRpb25EdXJhdGlvbigpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVhc2luZzogdGhpcy5jb21wdXRlZFN0eWxlLnRyYW5zaXRpb25UaW1pbmdGdW5jdGlvbixcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLiRlbFxuICAgICAgICAgICAgICAgICAgICAuZ2V0QW5pbWF0aW9ucygpXG4gICAgICAgICAgICAgICAgICAgIC5mb3JFYWNoKChhbmltYXRpb24pID0+IGFuaW1hdGlvbi5maW5pc2goKSlcbiAgICAgICAgICAgIH0pXG5cbiAgICAgICAgICAgIExpdmV3aXJlLmhvb2soJ21lc3NhZ2UucHJvY2Vzc2VkJywgKF8sIGNvbXBvbmVudCkgPT4ge1xuICAgICAgICAgICAgICAgIGlmIChcbiAgICAgICAgICAgICAgICAgICAgIWNvbXBvbmVudC5zZXJ2ZXJNZW1vLmRhdGEuaXNGaWxhbWVudE5vdGlmaWNhdGlvbnNDb21wb25lbnRcbiAgICAgICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKCF0aGlzLmlzU2hvd24pIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgYW5pbWF0aW9uKClcbiAgICAgICAgICAgIH0pXG4gICAgICAgIH0sXG5cbiAgICAgICAgY2xvc2U6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRoaXMuaXNTaG93biA9IGZhbHNlXG5cbiAgICAgICAgICAgIHNldFRpbWVvdXQoXG4gICAgICAgICAgICAgICAgKCkgPT4gTGl2ZXdpcmUuZW1pdCgnbm90aWZpY2F0aW9uQ2xvc2VkJywgbm90aWZpY2F0aW9uLmlkKSxcbiAgICAgICAgICAgICAgICB0aGlzLmdldFRyYW5zaXRpb25EdXJhdGlvbigpLFxuICAgICAgICAgICAgKVxuICAgICAgICB9LFxuXG4gICAgICAgIG1hcmtBc1JlYWQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIExpdmV3aXJlLmVtaXQoJ21hcmtlZE5vdGlmaWNhdGlvbkFzUmVhZCcsIG5vdGlmaWNhdGlvbi5pZClcbiAgICAgICAgfSxcblxuICAgICAgICBtYXJrQXNVbnJlYWQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIExpdmV3aXJlLmVtaXQoJ21hcmtlZE5vdGlmaWNhdGlvbkFzVW5yZWFkJywgbm90aWZpY2F0aW9uLmlkKVxuICAgICAgICB9LFxuXG4gICAgICAgIGdldFRyYW5zaXRpb25EdXJhdGlvbjogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgcmV0dXJuIHBhcnNlRmxvYXQodGhpcy5jb21wdXRlZFN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbikgKiAxMDAwXG4gICAgICAgIH0sXG4gICAgfSkpXG59XG4iLCAiaW1wb3J0IHsgdjQgYXMgdXVpZCB9IGZyb20gJ3V1aWQtYnJvd3NlcidcblxuY2xhc3MgTm90aWZpY2F0aW9uIHtcbiAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgdGhpcy5pZCh1dWlkKCkpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpZChpZCkge1xuICAgICAgICB0aGlzLmlkID0gaWRcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHRpdGxlKHRpdGxlKSB7XG4gICAgICAgIHRoaXMudGl0bGUgPSB0aXRsZVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgYm9keShib2R5KSB7XG4gICAgICAgIHRoaXMuYm9keSA9IGJvZHlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGFjdGlvbnMoYWN0aW9ucykge1xuICAgICAgICB0aGlzLmFjdGlvbnMgPSBhY3Rpb25zXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBzdGF0dXMoc3RhdHVzKSB7XG4gICAgICAgIHN3aXRjaCAoc3RhdHVzKSB7XG4gICAgICAgICAgICBjYXNlICdkYW5nZXInOlxuICAgICAgICAgICAgICAgIHRoaXMuZGFuZ2VyKClcblxuICAgICAgICAgICAgICAgIGJyZWFrXG5cbiAgICAgICAgICAgIGNhc2UgJ2luZm8nOlxuICAgICAgICAgICAgICAgIHRoaXMuaW5mbygpXG5cbiAgICAgICAgICAgICAgICBicmVha1xuXG4gICAgICAgICAgICBjYXNlICdzdWNjZXNzJzpcbiAgICAgICAgICAgICAgICB0aGlzLnN1Y2Nlc3MoKVxuXG4gICAgICAgICAgICAgICAgYnJlYWtcblxuICAgICAgICAgICAgY2FzZSAnd2FybmluZyc6XG4gICAgICAgICAgICAgICAgdGhpcy53YXJuaW5nKClcblxuICAgICAgICAgICAgICAgIGJyZWFrXG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGNvbG9yKGNvbG9yKSB7XG4gICAgICAgIHRoaXMuY29sb3IgPSBjb2xvclxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgaWNvbihpY29uKSB7XG4gICAgICAgIHRoaXMuaWNvbiA9IGljb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGljb25Db2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmljb25Db2xvciA9IGNvbG9yXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkdXJhdGlvbihkdXJhdGlvbikge1xuICAgICAgICB0aGlzLmR1cmF0aW9uID0gZHVyYXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHNlY29uZHMoc2Vjb25kcykge1xuICAgICAgICB0aGlzLmR1cmF0aW9uKHNlY29uZHMgKiAxMDAwKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgcGVyc2lzdGVudCgpIHtcbiAgICAgICAgdGhpcy5kdXJhdGlvbigncGVyc2lzdGVudCcpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkYW5nZXIoKSB7XG4gICAgICAgIHRoaXMuaWNvbignaGVyb2ljb24tby14LWNpcmNsZScpXG4gICAgICAgIHRoaXMuaWNvbkNvbG9yKCdkYW5nZXInKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgaW5mbygpIHtcbiAgICAgICAgdGhpcy5pY29uKCdoZXJvaWNvbi1vLWluZm9ybWF0aW9uLWNpcmNsZScpXG4gICAgICAgIHRoaXMuaWNvbkNvbG9yKCdpbmZvJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHN1Y2Nlc3MoKSB7XG4gICAgICAgIHRoaXMuaWNvbignaGVyb2ljb24tby1jaGVjay1jaXJjbGUnKVxuICAgICAgICB0aGlzLmljb25Db2xvcignc3VjY2VzcycpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICB3YXJuaW5nKCkge1xuICAgICAgICB0aGlzLmljb24oJ2hlcm9pY29uLW8tZXhjbGFtYXRpb24tY2lyY2xlJylcbiAgICAgICAgdGhpcy5pY29uQ29sb3IoJ3dhcm5pbmcnKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgdmlldyh2aWV3KSB7XG4gICAgICAgIHRoaXMudmlldyA9IHZpZXdcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHZpZXdEYXRhKHZpZXdEYXRhKSB7XG4gICAgICAgIHRoaXMudmlld0RhdGEgPSB2aWV3RGF0YVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgc2VuZCgpIHtcbiAgICAgICAgTGl2ZXdpcmUuZW1pdCgnbm90aWZpY2F0aW9uU2VudCcsIHRoaXMpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG59XG5cbmNsYXNzIEFjdGlvbiB7XG4gICAgY29uc3RydWN0b3IobmFtZSkge1xuICAgICAgICB0aGlzLm5hbWUobmFtZSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIG5hbWUobmFtZSkge1xuICAgICAgICB0aGlzLm5hbWUgPSBuYW1lXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBjb2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmNvbG9yID0gY29sb3JcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGVtaXQoZXZlbnQsIGRhdGEpIHtcbiAgICAgICAgdGhpcy5ldmVudChldmVudClcbiAgICAgICAgdGhpcy5ldmVudERhdGEoZGF0YSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGVtaXRTZWxmKGV2ZW50LCBkYXRhKSB7XG4gICAgICAgIHRoaXMuZW1pdChldmVudCwgZGF0YSlcbiAgICAgICAgdGhpcy5lbWl0RGlyZWN0aW9uID0gJ3NlbGYnXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBlbWl0VG8oY29tcG9uZW50LCBldmVudCwgZGF0YSkge1xuICAgICAgICB0aGlzLmVtaXQoZXZlbnQsIGRhdGEpXG4gICAgICAgIHRoaXMuZW1pdERpcmVjdGlvbiA9ICd0bydcbiAgICAgICAgdGhpcy5lbWl0VG9Db21wb25lbnQgPSBjb21wb25lbnRcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGVtaXRVcChldmVudCwgZGF0YSkge1xuICAgICAgICB0aGlzLmVtaXQoZXZlbnQsIGRhdGEpXG4gICAgICAgIHRoaXMuZW1pdERpcmVjdGlvbiA9ICd1cCdcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGVtaXREaXJlY3Rpb24oZW1pdERpcmVjdGlvbikge1xuICAgICAgICB0aGlzLmVtaXREaXJlY3Rpb24gPSBlbWl0RGlyZWN0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBlbWl0VG9Db21wb25lbnQoY29tcG9uZW50KSB7XG4gICAgICAgIHRoaXMuZW1pdFRvQ29tcG9uZW50ID0gY29tcG9uZW50XG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBldmVudChldmVudCkge1xuICAgICAgICB0aGlzLmV2ZW50ID0gZXZlbnRcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGV2ZW50RGF0YShkYXRhKSB7XG4gICAgICAgIHRoaXMuZXZlbnREYXRhID0gZGF0YVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZXh0cmFBdHRyaWJ1dGVzKGF0dHJpYnV0ZXMpIHtcbiAgICAgICAgdGhpcy5leHRyYUF0dHJpYnV0ZXMgPSBhdHRyaWJ1dGVzXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpY29uKGljb24pIHtcbiAgICAgICAgdGhpcy5pY29uID0gaWNvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgaWNvblBvc2l0aW9uKHBvc2l0aW9uKSB7XG4gICAgICAgIHRoaXMuaWNvblBvc2l0aW9uID0gcG9zaXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIG91dGxpbmVkKGNvbmRpdGlvbiA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5pc091dGxpbmVkID0gY29uZGl0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkaXNhYmxlZChjb25kaXRpb24gPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuaXNEaXNhYmxlZCA9IGNvbmRpdGlvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgbGFiZWwobGFiZWwpIHtcbiAgICAgICAgdGhpcy5sYWJlbCA9IGxhYmVsXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBjbG9zZShjb25kaXRpb24gPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2hvdWxkQ2xvc2UgPSBjb25kaXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIG9wZW5VcmxJbk5ld1RhYihjb25kaXRpb24gPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2hvdWxkT3BlblVybEluTmV3VGFiID0gY29uZGl0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBzaXplKHNpemUpIHtcbiAgICAgICAgdGhpcy5zaXplID0gc2l6ZVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgdXJsKHVybCkge1xuICAgICAgICB0aGlzLnVybCA9IHVybFxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgdmlldyh2aWV3KSB7XG4gICAgICAgIHRoaXMudmlldyA9IHZpZXdcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGJ1dHRvbigpIHtcbiAgICAgICAgdGhpcy52aWV3KCdmaWxhbWVudC1ub3RpZmljYXRpb25zOjphY3Rpb25zLmJ1dHRvbi1hY3Rpb24nKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZ3JvdXBlZCgpIHtcbiAgICAgICAgdGhpcy52aWV3KCdmaWxhbWVudC1ub3RpZmljYXRpb25zOjphY3Rpb25zLmdyb3VwZWQtYWN0aW9uJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGxpbmsoKSB7XG4gICAgICAgIHRoaXMudmlldygnZmlsYW1lbnQtbm90aWZpY2F0aW9uczo6YWN0aW9ucy5saW5rLWFjdGlvbicpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG59XG5cbmNsYXNzIEFjdGlvbkdyb3VwIHtcbiAgICBjb25zdHJ1Y3RvcihhY3Rpb25zKSB7XG4gICAgICAgIHRoaXMuYWN0aW9ucyhhY3Rpb25zKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgYWN0aW9ucyhhY3Rpb25zKSB7XG4gICAgICAgIHRoaXMuYWN0aW9ucyA9IGFjdGlvbnMubWFwKChhY3Rpb24pID0+IGFjdGlvbi5ncm91cGVkKCkpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBjb2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmNvbG9yID0gY29sb3JcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGljb24oaWNvbikge1xuICAgICAgICB0aGlzLmljb24gPSBpY29uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpY29uUG9zaXRpb24ocG9zaXRpb24pIHtcbiAgICAgICAgdGhpcy5pY29uUG9zaXRpb24gPSBwb3NpdGlvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgbGFiZWwobGFiZWwpIHtcbiAgICAgICAgdGhpcy5sYWJlbCA9IGxhYmVsXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICB0b29sdGlwKHRvb2x0aXApIHtcbiAgICAgICAgdGhpcy50b29sdGlwID0gdG9vbHRpcFxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxufVxuXG5leHBvcnQgeyBBY3Rpb24sIEFjdGlvbkdyb3VwLCBOb3RpZmljYXRpb24gfVxuIiwgImltcG9ydCBOb3RpZmljYXRpb25Db21wb25lbnRBbHBpbmVQbHVnaW4gZnJvbSAnLi9jb21wb25lbnRzL25vdGlmaWNhdGlvbidcbmltcG9ydCB7XG4gICAgQWN0aW9uIGFzIE5vdGlmaWNhdGlvbkFjdGlvbixcbiAgICBBY3Rpb25Hcm91cCBhcyBOb3RpZmljYXRpb25BY3Rpb25Hcm91cCxcbiAgICBOb3RpZmljYXRpb24sXG59IGZyb20gJy4vTm90aWZpY2F0aW9uJ1xuXG53aW5kb3cuTm90aWZpY2F0aW9uQWN0aW9uID0gTm90aWZpY2F0aW9uQWN0aW9uXG53aW5kb3cuTm90aWZpY2F0aW9uQWN0aW9uR3JvdXAgPSBOb3RpZmljYXRpb25BY3Rpb25Hcm91cFxud2luZG93Lk5vdGlmaWNhdGlvbiA9IE5vdGlmaWNhdGlvblxuXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdhbHBpbmU6aW5pdCcsICgpID0+IHtcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihOb3RpZmljYXRpb25Db21wb25lbnRBbHBpbmVQbHVnaW4pXG59KVxuIl0sCiAgIm1hcHBpbmdzIjogIjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQUE7QUFJQSxVQUFJO0FBRUosVUFBSSxTQUFTLE9BQU8sV0FBVyxnQkFBZ0IsT0FBTyxVQUFVLE9BQU87QUFDdkUsVUFBSSxVQUFVLE9BQU8saUJBQWlCO0FBRWhDLGdCQUFRLElBQUksV0FBVyxFQUFFO0FBQzdCLGNBQU0sU0FBUyxZQUFZO0FBQ3pCLGlCQUFPLGdCQUFnQixLQUFLO0FBQzVCLGlCQUFPO0FBQUEsUUFDVDtBQUFBLE1BQ0Y7QUFMTTtBQU9OLFVBQUksQ0FBQyxLQUFLO0FBS0osZUFBTyxJQUFJLE1BQU0sRUFBRTtBQUN2QixjQUFNLFdBQVc7QUFDZixtQkFBUyxJQUFJLEdBQUcsR0FBRyxJQUFJLElBQUksS0FBSztBQUM5QixpQkFBSyxJQUFJLE9BQVU7QUFBRyxrQkFBSSxLQUFLLE9BQU8sSUFBSTtBQUMxQyxpQkFBSyxDQUFDLElBQUksUUFBUSxJQUFJLE1BQVMsS0FBSztBQUFBLFVBQ3RDO0FBRUEsaUJBQU87QUFBQSxRQUNUO0FBQUEsTUFDRjtBQVRNO0FBV04sYUFBTyxVQUFVO0FBQUE7QUFBQTs7O0FDaENqQjtBQUFBO0FBSUEsVUFBSSxZQUFZLENBQUM7QUFDakIsV0FBUyxJQUFJLEdBQUcsSUFBSSxLQUFLLEVBQUUsR0FBRztBQUM1QixrQkFBVSxDQUFDLEtBQUssSUFBSSxLQUFPLFNBQVMsRUFBRSxFQUFFLE9BQU8sQ0FBQztBQUFBLE1BQ2xEO0FBRlM7QUFJVCxlQUFTLFlBQVksS0FBSyxRQUFRO0FBQ2hDLFlBQUlBLEtBQUksVUFBVTtBQUNsQixZQUFJLE1BQU07QUFDVixlQUFPLElBQUksSUFBSUEs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QixhQUFPO0FBQUEsSUFDWDtBQUFBLElBRUEsZ0JBQWdCLFlBQVksTUFBTTtBQUM5QixXQUFLLHdCQUF3QjtBQUU3QixhQUFPO0FBQUEsSUFDWDtBQUFBLElBRUEsS0FBSyxNQUFNO0FBQ1AsV0FBSyxPQUFPO0FBRVosYUFBTztBQUFBLElBQ1g7QUFBQSxJQUVBLElBQUksS0FBSztBQUNMLFdBQUssTUFBTTtBQUVYLGFBQU87QUFBQSxJQUNYO0FBQUEsSUFFQSxLQUFLLE1BQU07QUFDUCxXQUFLLE9BQU87QUFFWixhQUFPO0FBQUEsSUFDWDtBQUFBLElBRUEsU0FBUztBQUNMLFdBQUssS0FBSywrQ0FBK0M7QUFFekQsYUFBTztBQUFBLElBQ1g7QUFBQSxJQUVBLFVBQVU7QUFDTixXQUFLLEtBQUssZ0RBQWdEO0FBRTFELGFBQU87QUFBQSxJQUNYO0FBQUEsSUFFQSxPQUFPO0FBQ0gsV0FBSyxLQUFLLDZDQUE2QztBQUV2RCxhQUFPO0FBQUEsSUFDWDtBQUFBLEVBQ0o7QUFFQSxNQUFNLGNBQU4sTUFBa0I7QUFBQSxJQUNkLFlBQVksU0FBUztBQUNqQixXQUFLLFFBQVEsT0FBTztBQUVwQixhQUFPO0FBQUEsSUFDWDtBQUFBLElBRUEsUUFBUSxTQUFTO0FBQ2IsV0FBSyxVQUFVLFFBQVEsSUFBSSxDQUFDLFdBQVcsT0FBTyxRQUFRLENBQUM7QUFFdkQsYUFBTztBQUFBLElBQ1g7QUFBQSxJQUVBLE1BQU0sT0FBTztBQUNULFdBQUssUUFBUTtBQUViLGFBQU87QUFBQSxJQUNYO0FBQUEsSUFFQSxLQUFLLE1BQU07QUFDUCxXQUFLLE9BQU87QUFFWixhQUFPO0FBQUEsSUFDWDtBQUFBLElBRUEsYUFBYSxVQUFVO0FBQ25CLFdBQUssZUFBZTtBQUVwQixhQUFPO0FBQUEsSUFDWDtBQUFBLElBRUEsTUFBTSxPQUFPO0FBQ1QsV0FBSyxRQUFRO0FBRWIsYUFBTztBQUFBLElBQ1g7QUFBQSxJQUVBLFFBQVEsU0FBUztBQUNiLFdBQUssVUFBVTtBQUVmLGFBQU87QUFBQSxJQUNYO0FBQUEsRUFDSjs7O0FDOVVBLFNBQU8scUJBQXFCO0FBQzVCLFNBQU8sMEJBQTBCO0FBQ2pDLFNBQU8sZUFBZTtBQUV0QixXQUFTLGlCQUFpQixlQUFlLE1BQU07QUFDM0MsV0FBTyxPQUFPLE9BQU8sb0JBQWlDO0FBQUEsRUFDMUQsQ0FBQzsiLAogICJuYW1lcyI6IFsiaSIsICJ1dWlkIiwgImFuaW1hdGlvbiIsICJ1dWlkIl0KfQo=
