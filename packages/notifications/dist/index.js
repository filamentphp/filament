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
      transitionDuration: null,
      transitionEasing: null,
      init: function() {
        this.computedStyle = window.getComputedStyle(this.$el);
        this.transitionDuration = parseFloat(this.computedStyle.transitionDuration) * 1e3;
        this.transitionEasing = this.computedStyle.transitionTimingFunction;
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
        Livewire.hook(
          "commit",
          ({ component, commit, succeed, fail, respond }) => {
            if (!component.snapshot.data.isFilamentNotificationsComponent) {
              return;
            }
            const getTop = () => this.$el.getBoundingClientRect().top;
            const oldTop = getTop();
            respond(() => {
              animation = () => {
                if (!this.isShown) {
                  return;
                }
                this.$el.animate(
                  [
                    {
                      transform: `translateY(${oldTop - getTop()}px)`
                    },
                    { transform: "translateY(0px)" }
                  ],
                  {
                    duration: this.transitionDuration,
                    easing: this.transitionEasing
                  }
                );
              };
              this.$el.getAnimations().forEach((animation2) => animation2.finish());
            });
            succeed(({ snapshot, effect }) => {
              animation();
            });
          }
        );
      },
      close: function() {
        this.isShown = false;
        setTimeout(
          () => window.dispatchEvent(
            new CustomEvent("notificationClosed", {
              detail: {
                id: notification.id
              }
            })
          ),
          this.transitionDuration
        );
      },
      markAsRead: function() {
        window.dispatchEvent(
          new CustomEvent("markedNotificationAsRead", {
            detail: {
              id: notification.id
            }
          })
        );
      },
      markAsUnread: function() {
        window.dispatchEvent(
          new CustomEvent("markedNotificationAsUnread", {
            detail: {
              id: notification.id
            }
          })
        );
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
      window.dispatchEvent(
        new CustomEvent("notificationSent", {
          detail: {
            notification: this
          }
        })
      );
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
    dispatch(event, data) {
      this.event(event);
      this.eventData(data);
      return this;
    }
    dispatchSelf(event, data) {
      this.dispatch(event, data);
      this.dispatchDirection = "self";
      return this;
    }
    dispatchTo(component, event, data) {
      this.dispatch(event, data);
      this.dispatchDirection = "to";
      this.dispatchToComponent = component;
      return this;
    }
    /**
     * @deprecated Use `dispatch()` instead.
     */
    emit(event, data) {
      this.dispatch(event, data);
      return this;
    }
    /**
     * @deprecated Use `dispatchSelf()` instead.
     */
    emitSelf(event, data) {
      this.dispatchSelf(event, data);
      return this;
    }
    /**
     * @deprecated Use `dispatchTo()` instead.
     */
    emitTo(component, event, data) {
      this.dispatchTo(component, event, data);
      return this;
    }
    dispatchDirection(dispatchDirection) {
      this.dispatchDirection = dispatchDirection;
      return this;
    }
    dispatchToComponent(component) {
      this.dispatchToComponent = component;
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
  window.FilamentNotificationAction = Action;
  window.FilamentNotificationActionGroup = ActionGroup;
  window.FilamentNotification = Notification;
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(notification_default);
  });
})();
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci9saWIvcm5nLWJyb3dzZXIuanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci9saWIvYnl0ZXNUb1V1aWQuanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci92MS5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvdXVpZC1icm93c2VyL3Y0LmpzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy91dWlkLWJyb3dzZXIvaW5kZXguanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2FscGluZWpzL3NyYy9tdXRhdGlvbi5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvYWxwaW5lanMvc3JjL3V0aWxzL29uY2UuanMiLCAiLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvbm90aWZpY2F0aW9uLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9Ob3RpZmljYXRpb24uanMiLCAiLi4vcmVzb3VyY2VzL2pzL2luZGV4LmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvLyBVbmlxdWUgSUQgY3JlYXRpb24gcmVxdWlyZXMgYSBoaWdoIHF1YWxpdHkgcmFuZG9tICMgZ2VuZXJhdG9yLiAgSW4gdGhlXG4vLyBicm93c2VyIHRoaXMgaXMgYSBsaXR0bGUgY29tcGxpY2F0ZWQgZHVlIHRvIHVua25vd24gcXVhbGl0eSBvZiBNYXRoLnJhbmRvbSgpXG4vLyBhbmQgaW5jb25zaXN0ZW50IHN1cHBvcnQgZm9yIHRoZSBgY3J5cHRvYCBBUEkuICBXZSBkbyB0aGUgYmVzdCB3ZSBjYW4gdmlhXG4vLyBmZWF0dXJlLWRldGVjdGlvblxudmFyIHJuZztcblxudmFyIGNyeXB0byA9IHR5cGVvZiBnbG9iYWwgIT09ICd1bmRlZmluZWQnICYmIChnbG9iYWwuY3J5cHRvIHx8IGdsb2JhbC5tc0NyeXB0byk7IC8vIGZvciBJRSAxMVxuaWYgKGNyeXB0byAmJiBjcnlwdG8uZ2V0UmFuZG9tVmFsdWVzKSB7XG4gIC8vIFdIQVRXRyBjcnlwdG8gUk5HIC0gaHR0cDovL3dpa2kud2hhdHdnLm9yZy93aWtpL0NyeXB0b1xuICB2YXIgcm5kczggPSBuZXcgVWludDhBcnJheSgxNik7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcbiAgcm5nID0gZnVuY3Rpb24gd2hhdHdnUk5HKCkge1xuICAgIGNyeXB0by5nZXRSYW5kb21WYWx1ZXMocm5kczgpO1xuICAgIHJldHVybiBybmRzODtcbiAgfTtcbn1cblxuaWYgKCFybmcpIHtcbiAgLy8gTWF0aC5yYW5kb20oKS1iYXNlZCAoUk5HKVxuICAvL1xuICAvLyBJZiBhbGwgZWxzZSBmYWlscywgdXNlIE1hdGgucmFuZG9tKCkuICBJdCdzIGZhc3QsIGJ1dCBpcyBvZiB1bnNwZWNpZmllZFxuICAvLyBxdWFsaXR5LlxuICB2YXIgcm5kcyA9IG5ldyBBcnJheSgxNik7XG4gIHJuZyA9IGZ1bmN0aW9uKCkge1xuICAgIGZvciAodmFyIGkgPSAwLCByOyBpIDwgMTY7IGkrKykge1xuICAgICAgaWYgKChpICYgMHgwMykgPT09IDApIHIgPSBNYXRoLnJhbmRvbSgpICogMHgxMDAwMDAwMDA7XG4gICAgICBybmRzW2ldID0gciA+Pj4gKChpICYgMHgwMykgPDwgMykgJiAweGZmO1xuICAgIH1cblxuICAgIHJldHVybiBybmRzO1xuICB9O1xufVxuXG5tb2R1bGUuZXhwb3J0cyA9IHJuZztcbiIsICIvKipcbiAqIENvbnZlcnQgYXJyYXkgb2YgMTYgYnl0ZSB2YWx1ZXMgdG8gVVVJRCBzdHJpbmcgZm9ybWF0IG9mIHRoZSBmb3JtOlxuICogWFhYWFhYWFgtWFhYWC1YWFhYLVhYWFgtWFhYWFhYWFhYWFhYXG4gKi9cbnZhciBieXRlVG9IZXggPSBbXTtcbmZvciAodmFyIGkgPSAwOyBpIDwgMjU2OyArK2kpIHtcbiAgYnl0ZVRvSGV4W2ldID0gKGkgKyAweDEwMCkudG9TdHJpbmcoMTYpLnN1YnN0cigxKTtcbn1cblxuZnVuY3Rpb24gYnl0ZXNUb1V1aWQoYnVmLCBvZmZzZXQpIHtcbiAgdmFyIGkgPSBvZmZzZXQgfHwgMDtcbiAgdmFyIGJ0aCA9IGJ5dGVUb0hleDtcbiAgcmV0dXJuIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXSArICctJyArXG4gICAgICAgICAgYnRoW2J1ZltpKytdXSArIGJ0aFtidWZbaSsrXV0gKyAnLScgK1xuICAgICAgICAgIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICsgJy0nICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXSArICctJyArXG4gICAgICAgICAgYnRoW2J1ZltpKytdXSArIGJ0aFtidWZbaSsrXV0gK1xuICAgICAgICAgIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBieXRlc1RvVXVpZDtcbiIsICJ2YXIgcm5nID0gcmVxdWlyZSgnLi9saWIvcm5nLWJyb3dzZXInKTtcbnZhciBieXRlc1RvVXVpZCA9IHJlcXVpcmUoJy4vbGliL2J5dGVzVG9VdWlkJyk7XG5cbi8vICoqYHYxKClgIC0gR2VuZXJhdGUgdGltZS1iYXNlZCBVVUlEKipcbi8vXG4vLyBJbnNwaXJlZCBieSBodHRwczovL2dpdGh1Yi5jb20vTGlvc0svVVVJRC5qc1xuLy8gYW5kIGh0dHA6Ly9kb2NzLnB5dGhvbi5vcmcvbGlicmFyeS91dWlkLmh0bWxcblxuLy8gcmFuZG9tICMncyB3ZSBuZWVkIHRvIGluaXQgbm9kZSBhbmQgY2xvY2tzZXFcbnZhciBfc2VlZEJ5dGVzID0gcm5nKCk7XG5cbi8vIFBlciA0LjUsIGNyZWF0ZSBhbmQgNDgtYml0IG5vZGUgaWQsICg0NyByYW5kb20gYml0cyArIG11bHRpY2FzdCBiaXQgPSAxKVxudmFyIF9ub2RlSWQgPSBbXG4gIF9zZWVkQnl0ZXNbMF0gfCAweDAxLFxuICBfc2VlZEJ5dGVzWzFdLCBfc2VlZEJ5dGVzWzJdLCBfc2VlZEJ5dGVzWzNdLCBfc2VlZEJ5dGVzWzRdLCBfc2VlZEJ5dGVzWzVdXG5dO1xuXG4vLyBQZXIgNC4yLjIsIHJhbmRvbWl6ZSAoMTQgYml0KSBjbG9ja3NlcVxudmFyIF9jbG9ja3NlcSA9IChfc2VlZEJ5dGVzWzZdIDw8IDggfCBfc2VlZEJ5dGVzWzddKSAmIDB4M2ZmZjtcblxuLy8gUHJldmlvdXMgdXVpZCBjcmVhdGlvbiB0aW1lXG52YXIgX2xhc3RNU2VjcyA9IDAsIF9sYXN0TlNlY3MgPSAwO1xuXG4vLyBTZWUgaHR0cHM6Ly9naXRodWIuY29tL2Jyb29mYS9ub2RlLXV1aWQgZm9yIEFQSSBkZXRhaWxzXG5mdW5jdGlvbiB2MShvcHRpb25zLCBidWYsIG9mZnNldCkge1xuICB2YXIgaSA9IGJ1ZiAmJiBvZmZzZXQgfHwgMDtcbiAgdmFyIGIgPSBidWYgfHwgW107XG5cbiAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cbiAgdmFyIGNsb2Nrc2VxID0gb3B0aW9ucy5jbG9ja3NlcSAhPT0gdW5kZWZpbmVkID8gb3B0aW9ucy5jbG9ja3NlcSA6IF9jbG9ja3NlcTtcblxuICAvLyBVVUlEIHRpbWVzdGFtcHMgYXJlIDEwMCBuYW5vLXNlY29uZCB1bml0cyBzaW5jZSB0aGUgR3JlZ29yaWFuIGVwb2NoLFxuICAvLyAoMTU4Mi0xMC0xNSAwMDowMCkuICBKU051bWJlcnMgYXJlbid0IHByZWNpc2UgZW5vdWdoIGZvciB0aGlzLCBzb1xuICAvLyB0aW1lIGlzIGhhbmRsZWQgaW50ZXJuYWxseSBhcyAnbXNlY3MnIChpbnRlZ2VyIG1pbGxpc2Vjb25kcykgYW5kICduc2VjcydcbiAgLy8gKDEwMC1uYW5vc2Vjb25kcyBvZmZzZXQgZnJvbSBtc2Vjcykgc2luY2UgdW5peCBlcG9jaCwgMTk3MC0wMS0wMSAwMDowMC5cbiAgdmFyIG1zZWNzID0gb3B0aW9ucy5tc2VjcyAhPT0gdW5kZWZpbmVkID8gb3B0aW9ucy5tc2VjcyA6IG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xuXG4gIC8vIFBlciA0LjIuMS4yLCB1c2UgY291bnQgb2YgdXVpZCdzIGdlbmVyYXRlZCBkdXJpbmcgdGhlIGN1cnJlbnQgY2xvY2tcbiAgLy8gY3ljbGUgdG8gc2ltdWxhdGUgaGlnaGVyIHJlc29sdXRpb24gY2xvY2tcbiAgdmFyIG5zZWNzID0gb3B0aW9ucy5uc2VjcyAhPT0gdW5kZWZpbmVkID8gb3B0aW9ucy5uc2VjcyA6IF9sYXN0TlNlY3MgKyAxO1xuXG4gIC8vIFRpbWUgc2luY2UgbGFzdCB1dWlkIGNyZWF0aW9uIChpbiBtc2VjcylcbiAgdmFyIGR0ID0gKG1zZWNzIC0gX2xhc3RNU2VjcykgKyAobnNlY3MgLSBfbGFzdE5TZWNzKS8xMDAwMDtcblxuICAvLyBQZXIgNC4yLjEuMiwgQnVtcCBjbG9ja3NlcSBvbiBjbG9jayByZWdyZXNzaW9uXG4gIGlmIChkdCA8IDAgJiYgb3B0aW9ucy5jbG9ja3NlcSA9PT0gdW5kZWZpbmVkKSB7XG4gICAgY2xvY2tzZXEgPSBjbG9ja3NlcSArIDEgJiAweDNmZmY7XG4gIH1cblxuICAvLyBSZXNldCBuc2VjcyBpZiBjbG9jayByZWdyZXNzZXMgKG5ldyBjbG9ja3NlcSkgb3Igd2UndmUgbW92ZWQgb250byBhIG5ld1xuICAvLyB0aW1lIGludGVydmFsXG4gIGlmICgoZHQgPCAwIHx8IG1zZWNzID4gX2xhc3RNU2VjcykgJiYgb3B0aW9ucy5uc2VjcyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgbnNlY3MgPSAwO1xuICB9XG5cbiAgLy8gUGVyIDQuMi4xLjIgVGhyb3cgZXJyb3IgaWYgdG9vIG1hbnkgdXVpZHMgYXJlIHJlcXVlc3RlZFxuICBpZiAobnNlY3MgPj0gMTAwMDApIHtcbiAgICB0aHJvdyBuZXcgRXJyb3IoJ3V1aWQudjEoKTogQ2FuXFwndCBjcmVhdGUgbW9yZSB0aGFuIDEwTSB1dWlkcy9zZWMnKTtcbiAgfVxuXG4gIF9sYXN0TVNlY3MgPSBtc2VjcztcbiAgX2xhc3ROU2VjcyA9IG5zZWNzO1xuICBfY2xvY2tzZXEgPSBjbG9ja3NlcTtcblxuICAvLyBQZXIgNC4xLjQgLSBDb252ZXJ0IGZyb20gdW5peCBlcG9jaCB0byBHcmVnb3JpYW4gZXBvY2hcbiAgbXNlY3MgKz0gMTIyMTkyOTI4MDAwMDA7XG5cbiAgLy8gYHRpbWVfbG93YFxuICB2YXIgdGwgPSAoKG1zZWNzICYgMHhmZmZmZmZmKSAqIDEwMDAwICsgbnNlY3MpICUgMHgxMDAwMDAwMDA7XG4gIGJbaSsrXSA9IHRsID4+PiAyNCAmIDB4ZmY7XG4gIGJbaSsrXSA9IHRsID4+PiAxNiAmIDB4ZmY7XG4gIGJbaSsrXSA9IHRsID4+PiA4ICYgMHhmZjtcbiAgYltpKytdID0gdGwgJiAweGZmO1xuXG4gIC8vIGB0aW1lX21pZGBcbiAgdmFyIHRtaCA9IChtc2VjcyAvIDB4MTAwMDAwMDAwICogMTAwMDApICYgMHhmZmZmZmZmO1xuICBiW2krK10gPSB0bWggPj4+IDggJiAweGZmO1xuICBiW2krK10gPSB0bWggJiAweGZmO1xuXG4gIC8vIGB0aW1lX2hpZ2hfYW5kX3ZlcnNpb25gXG4gIGJbaSsrXSA9IHRtaCA+Pj4gMjQgJiAweGYgfCAweDEwOyAvLyBpbmNsdWRlIHZlcnNpb25cbiAgYltpKytdID0gdG1oID4+PiAxNiAmIDB4ZmY7XG5cbiAgLy8gYGNsb2NrX3NlcV9oaV9hbmRfcmVzZXJ2ZWRgIChQZXIgNC4yLjIgLSBpbmNsdWRlIHZhcmlhbnQpXG4gIGJbaSsrXSA9IGNsb2Nrc2VxID4+PiA4IHwgMHg4MDtcblxuICAvLyBgY2xvY2tfc2VxX2xvd2BcbiAgYltpKytdID0gY2xvY2tzZXEgJiAweGZmO1xuXG4gIC8vIGBub2RlYFxuICB2YXIgbm9kZSA9IG9wdGlvbnMubm9kZSB8fCBfbm9kZUlkO1xuICBmb3IgKHZhciBuID0gMDsgbiA8IDY7ICsrbikge1xuICAgIGJbaSArIG5dID0gbm9kZVtuXTtcbiAgfVxuXG4gIHJldHVybiBidWYgPyBidWYgOiBieXRlc1RvVXVpZChiKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSB2MTtcbiIsICJ2YXIgcm5nID0gcmVxdWlyZSgnLi9saWIvcm5nLWJyb3dzZXInKTtcbnZhciBieXRlc1RvVXVpZCA9IHJlcXVpcmUoJy4vbGliL2J5dGVzVG9VdWlkJyk7XG5cbmZ1bmN0aW9uIHY0KG9wdGlvbnMsIGJ1Ziwgb2Zmc2V0KSB7XG4gIHZhciBpID0gYnVmICYmIG9mZnNldCB8fCAwO1xuXG4gIGlmICh0eXBlb2Yob3B0aW9ucykgPT0gJ3N0cmluZycpIHtcbiAgICBidWYgPSBvcHRpb25zID09ICdiaW5hcnknID8gbmV3IEFycmF5KDE2KSA6IG51bGw7XG4gICAgb3B0aW9ucyA9IG51bGw7XG4gIH1cbiAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cbiAgdmFyIHJuZHMgPSBvcHRpb25zLnJhbmRvbSB8fCAob3B0aW9ucy5ybmcgfHwgcm5nKSgpO1xuXG4gIC8vIFBlciA0LjQsIHNldCBiaXRzIGZvciB2ZXJzaW9uIGFuZCBgY2xvY2tfc2VxX2hpX2FuZF9yZXNlcnZlZGBcbiAgcm5kc1s2XSA9IChybmRzWzZdICYgMHgwZikgfCAweDQwO1xuICBybmRzWzhdID0gKHJuZHNbOF0gJiAweDNmKSB8IDB4ODA7XG5cbiAgLy8gQ29weSBieXRlcyB0byBidWZmZXIsIGlmIHByb3ZpZGVkXG4gIGlmIChidWYpIHtcbiAgICBmb3IgKHZhciBpaSA9IDA7IGlpIDwgMTY7ICsraWkpIHtcbiAgICAgIGJ1ZltpICsgaWldID0gcm5kc1tpaV07XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGJ1ZiB8fCBieXRlc1RvVXVpZChybmRzKTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSB2NDtcbiIsICJ2YXIgdjEgPSByZXF1aXJlKCcuL3YxJyk7XG52YXIgdjQgPSByZXF1aXJlKCcuL3Y0Jyk7XG5cbnZhciB1dWlkID0gdjQ7XG51dWlkLnYxID0gdjE7XG51dWlkLnY0ID0gdjQ7XG5cbm1vZHVsZS5leHBvcnRzID0gdXVpZDtcbiIsICJsZXQgb25BdHRyaWJ1dGVBZGRlZHMgPSBbXVxubGV0IG9uRWxSZW1vdmVkcyA9IFtdXG5sZXQgb25FbEFkZGVkcyA9IFtdXG5cbmV4cG9ydCBmdW5jdGlvbiBvbkVsQWRkZWQoY2FsbGJhY2spIHtcbiAgICBvbkVsQWRkZWRzLnB1c2goY2FsbGJhY2spXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvbkVsUmVtb3ZlZChlbCwgY2FsbGJhY2spIHtcbiAgICBpZiAodHlwZW9mIGNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIGlmICghIGVsLl94X2NsZWFudXBzKSBlbC5feF9jbGVhbnVwcyA9IFtdXG4gICAgICAgIGVsLl94X2NsZWFudXBzLnB1c2goY2FsbGJhY2spXG4gICAgfSBlbHNlIHtcbiAgICAgICAgY2FsbGJhY2sgPSBlbFxuICAgICAgICBvbkVsUmVtb3ZlZHMucHVzaChjYWxsYmFjaylcbiAgICB9XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvbkF0dHJpYnV0ZXNBZGRlZChjYWxsYmFjaykge1xuICAgIG9uQXR0cmlidXRlQWRkZWRzLnB1c2goY2FsbGJhY2spXG59XG5cbmV4cG9ydCBmdW5jdGlvbiBvbkF0dHJpYnV0ZVJlbW92ZWQoZWwsIG5hbWUsIGNhbGxiYWNrKSB7XG4gICAgaWYgKCEgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHMpIGVsLl94X2F0dHJpYnV0ZUNsZWFudXBzID0ge31cbiAgICBpZiAoISBlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXSkgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHNbbmFtZV0gPSBbXVxuXG4gICAgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHNbbmFtZV0ucHVzaChjYWxsYmFjaylcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGNsZWFudXBBdHRyaWJ1dGVzKGVsLCBuYW1lcykge1xuICAgIGlmICghIGVsLl94X2F0dHJpYnV0ZUNsZWFudXBzKSByZXR1cm5cblxuICAgIE9iamVjdC5lbnRyaWVzKGVsLl94X2F0dHJpYnV0ZUNsZWFudXBzKS5mb3JFYWNoKChbbmFtZSwgdmFsdWVdKSA9PiB7XG4gICAgICAgIGlmIChuYW1lcyA9PT0gdW5kZWZpbmVkIHx8IG5hbWVzLmluY2x1ZGVzKG5hbWUpKSB7XG4gICAgICAgICAgICB2YWx1ZS5mb3JFYWNoKGkgPT4gaSgpKVxuXG4gICAgICAgICAgICBkZWxldGUgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHNbbmFtZV1cbiAgICAgICAgfVxuICAgIH0pXG59XG5cbmxldCBvYnNlcnZlciA9IG5ldyBNdXRhdGlvbk9ic2VydmVyKG9uTXV0YXRlKVxuXG5sZXQgY3VycmVudGx5T2JzZXJ2aW5nID0gZmFsc2VcblxuZXhwb3J0IGZ1bmN0aW9uIHN0YXJ0T2JzZXJ2aW5nTXV0YXRpb25zKCkge1xuICAgIG9ic2VydmVyLm9ic2VydmUoZG9jdW1lbnQsIHsgc3VidHJlZTogdHJ1ZSwgY2hpbGRMaXN0OiB0cnVlLCBhdHRyaWJ1dGVzOiB0cnVlLCBhdHRyaWJ1dGVPbGRWYWx1ZTogdHJ1ZSB9KVxuXG4gICAgY3VycmVudGx5T2JzZXJ2aW5nID0gdHJ1ZVxufVxuXG5leHBvcnQgZnVuY3Rpb24gc3RvcE9ic2VydmluZ011dGF0aW9ucygpIHtcbiAgICBmbHVzaE9ic2VydmVyKClcblxuICAgIG9ic2VydmVyLmRpc2Nvbm5lY3QoKVxuXG4gICAgY3VycmVudGx5T2JzZXJ2aW5nID0gZmFsc2Vcbn1cblxubGV0IHJlY29yZFF1ZXVlID0gW11cbmxldCB3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlID0gZmFsc2VcblxuZXhwb3J0IGZ1bmN0aW9uIGZsdXNoT2JzZXJ2ZXIoKSB7XG4gICAgcmVjb3JkUXVldWUgPSByZWNvcmRRdWV1ZS5jb25jYXQob2JzZXJ2ZXIudGFrZVJlY29yZHMoKSlcblxuICAgIGlmIChyZWNvcmRRdWV1ZS5sZW5ndGggJiYgISB3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlKSB7XG4gICAgICAgIHdpbGxQcm9jZXNzUmVjb3JkUXVldWUgPSB0cnVlXG5cbiAgICAgICAgcXVldWVNaWNyb3Rhc2soKCkgPT4ge1xuICAgICAgICAgICAgcHJvY2Vzc1JlY29yZFF1ZXVlKClcblxuICAgICAgICAgICAgd2lsbFByb2Nlc3NSZWNvcmRRdWV1ZSA9IGZhbHNlXG4gICAgICAgIH0pXG4gICAgfVxufVxuXG5mdW5jdGlvbiBwcm9jZXNzUmVjb3JkUXVldWUoKSB7XG4gICAgIG9uTXV0YXRlKHJlY29yZFF1ZXVlKVxuXG4gICAgIHJlY29yZFF1ZXVlLmxlbmd0aCA9IDBcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIG11dGF0ZURvbShjYWxsYmFjaykge1xuICAgIGlmICghIGN1cnJlbnRseU9ic2VydmluZykgcmV0dXJuIGNhbGxiYWNrKClcblxuICAgIHN0b3BPYnNlcnZpbmdNdXRhdGlvbnMoKVxuXG4gICAgbGV0IHJlc3VsdCA9IGNhbGxiYWNrKClcblxuICAgIHN0YXJ0T2JzZXJ2aW5nTXV0YXRpb25zKClcblxuICAgIHJldHVybiByZXN1bHRcbn1cblxubGV0IGlzQ29sbGVjdGluZyA9IGZhbHNlXG5sZXQgZGVmZXJyZWRNdXRhdGlvbnMgPSBbXVxuXG5leHBvcnQgZnVuY3Rpb24gZGVmZXJNdXRhdGlvbnMoKSB7XG4gICAgaXNDb2xsZWN0aW5nID0gdHJ1ZVxufVxuXG5leHBvcnQgZnVuY3Rpb24gZmx1c2hBbmRTdG9wRGVmZXJyaW5nTXV0YXRpb25zKCkge1xuICAgIGlzQ29sbGVjdGluZyA9IGZhbHNlXG5cbiAgICBvbk11dGF0ZShkZWZlcnJlZE11dGF0aW9ucylcblxuICAgIGRlZmVycmVkTXV0YXRpb25zID0gW11cbn1cblxuZnVuY3Rpb24gb25NdXRhdGUobXV0YXRpb25zKSB7XG4gICAgaWYgKGlzQ29sbGVjdGluZykge1xuICAgICAgICBkZWZlcnJlZE11dGF0aW9ucyA9IGRlZmVycmVkTXV0YXRpb25zLmNvbmNhdChtdXRhdGlvbnMpXG5cbiAgICAgICAgcmV0dXJuXG4gICAgfVxuXG4gICAgbGV0IGFkZGVkTm9kZXMgPSBbXVxuICAgIGxldCByZW1vdmVkTm9kZXMgPSBbXVxuICAgIGxldCBhZGRlZEF0dHJpYnV0ZXMgPSBuZXcgTWFwXG4gICAgbGV0IHJlbW92ZWRBdHRyaWJ1dGVzID0gbmV3IE1hcFxuXG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBtdXRhdGlvbnMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgaWYgKG11dGF0aW9uc1tpXS50YXJnZXQuX3hfaWdub3JlTXV0YXRpb25PYnNlcnZlcikgY29udGludWVcblxuICAgICAgICBpZiAobXV0YXRpb25zW2ldLnR5cGUgPT09ICdjaGlsZExpc3QnKSB7XG4gICAgICAgICAgICBtdXRhdGlvbnNbaV0uYWRkZWROb2Rlcy5mb3JFYWNoKG5vZGUgPT4gbm9kZS5ub2RlVHlwZSA9PT0gMSAmJiBhZGRlZE5vZGVzLnB1c2gobm9kZSkpXG4gICAgICAgICAgICBtdXRhdGlvbnNbaV0ucmVtb3ZlZE5vZGVzLmZvckVhY2gobm9kZSA9PiBub2RlLm5vZGVUeXBlID09PSAxICYmIHJlbW92ZWROb2Rlcy5wdXNoKG5vZGUpKVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKG11dGF0aW9uc1tpXS50eXBlID09PSAnYXR0cmlidXRlcycpIHtcbiAgICAgICAgICAgIGxldCBlbCA9IG11dGF0aW9uc1tpXS50YXJnZXRcbiAgICAgICAgICAgIGxldCBuYW1lID0gbXV0YXRpb25zW2ldLmF0dHJpYnV0ZU5hbWVcbiAgICAgICAgICAgIGxldCBvbGRWYWx1ZSA9IG11dGF0aW9uc1tpXS5vbGRWYWx1ZVxuXG4gICAgICAgICAgICBsZXQgYWRkID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgIGlmICghIGFkZGVkQXR0cmlidXRlcy5oYXMoZWwpKSBhZGRlZEF0dHJpYnV0ZXMuc2V0KGVsLCBbXSlcblxuICAgICAgICAgICAgICAgIGFkZGVkQXR0cmlidXRlcy5nZXQoZWwpLnB1c2goeyBuYW1lLCAgdmFsdWU6IGVsLmdldEF0dHJpYnV0ZShuYW1lKSB9KVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBsZXQgcmVtb3ZlID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgIGlmICghIHJlbW92ZWRBdHRyaWJ1dGVzLmhhcyhlbCkpIHJlbW92ZWRBdHRyaWJ1dGVzLnNldChlbCwgW10pXG5cbiAgICAgICAgICAgICAgICByZW1vdmVkQXR0cmlidXRlcy5nZXQoZWwpLnB1c2gobmFtZSlcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gTmV3IGF0dHJpYnV0ZS5cbiAgICAgICAgICAgIGlmIChlbC5oYXNBdHRyaWJ1dGUobmFtZSkgJiYgb2xkVmFsdWUgPT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICBhZGQoKVxuICAgICAgICAgICAgLy8gQ2hhbmdlZCBhdHR0cmlidXRlLlxuICAgICAgICAgICAgfSBlbHNlIGlmIChlbC5oYXNBdHRyaWJ1dGUobmFtZSkpIHtcbiAgICAgICAgICAgICAgICByZW1vdmUoKVxuICAgICAgICAgICAgICAgIGFkZCgpXG4gICAgICAgICAgICAvLyBSZW1vdmVkIGF0dHRyaWJ1dGUuXG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHJlbW92ZSgpXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICByZW1vdmVkQXR0cmlidXRlcy5mb3JFYWNoKChhdHRycywgZWwpID0+IHtcbiAgICAgICAgY2xlYW51cEF0dHJpYnV0ZXMoZWwsIGF0dHJzKVxuICAgIH0pXG5cbiAgICBhZGRlZEF0dHJpYnV0ZXMuZm9yRWFjaCgoYXR0cnMsIGVsKSA9PiB7XG4gICAgICAgIG9uQXR0cmlidXRlQWRkZWRzLmZvckVhY2goaSA9PiBpKGVsLCBhdHRycykpXG4gICAgfSlcblxuICAgIGZvciAobGV0IG5vZGUgb2YgcmVtb3ZlZE5vZGVzKSB7XG4gICAgICAgIC8vIElmIGFuIGVsZW1lbnQgZ2V0cyBtb3ZlZCBvbiBhIHBhZ2UsIGl0J3MgcmVnaXN0ZXJlZFxuICAgICAgICAvLyBhcyBib3RoIGFuIFwiYWRkXCIgYW5kIFwicmVtb3ZlXCIsIHNvIHdlIHdhbnQgdG8gc2tpcCB0aG9zZS5cbiAgICAgICAgaWYgKGFkZGVkTm9kZXMuaW5jbHVkZXMobm9kZSkpIGNvbnRpbnVlXG5cbiAgICAgICAgb25FbFJlbW92ZWRzLmZvckVhY2goaSA9PiBpKG5vZGUpKVxuXG4gICAgICAgIGlmIChub2RlLl94X2NsZWFudXBzKSB7XG4gICAgICAgICAgICB3aGlsZSAobm9kZS5feF9jbGVhbnVwcy5sZW5ndGgpIG5vZGUuX3hfY2xlYW51cHMucG9wKCkoKVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgLy8gTXV0YXRpb25zIGFyZSBidW5kbGVkIHRvZ2V0aGVyIGJ5IHRoZSBicm93c2VyIGJ1dCBzb21ldGltZXNcbiAgICAvLyBmb3IgY29tcGxleCBjYXNlcywgdGhlcmUgbWF5IGJlIGphdmFzY3JpcHQgY29kZSBhZGRpbmcgYSB3cmFwcGVyXG4gICAgLy8gYW5kIHRoZW4gYW4gYWxwaW5lIGNvbXBvbmVudCBhcyBhIGNoaWxkIG9mIHRoYXQgd3JhcHBlciBpbiB0aGUgc2FtZVxuICAgIC8vIGZ1bmN0aW9uIGFuZCB0aGUgbXV0YXRpb24gb2JzZXJ2ZXIgd2lsbCByZWNlaXZlIDIgZGlmZmVyZW50IG11dGF0aW9ucy5cbiAgICAvLyB3aGVuIGl0IGNvbWVzIHRpbWUgdG8gcnVuIHRoZW0sIHRoZSBkb20gY29udGFpbnMgYm90aCBjaGFuZ2VzIHNvIHRoZSBjaGlsZFxuICAgIC8vIGVsZW1lbnQgd291bGQgYmUgcHJvY2Vzc2VkIHR3aWNlIGFzIEFscGluZSBjYWxscyBpbml0VHJlZSBvblxuICAgIC8vIGJvdGggbXV0YXRpb25zLiBXZSBtYXJrIGFsbCBub2RlcyBhcyBfeF9pZ25vcmVkIGFuZCBvbmx5IHJlbW92ZSB0aGUgZmxhZ1xuICAgIC8vIHdoZW4gcHJvY2Vzc2luZyB0aGUgbm9kZSB0byBhdm9pZCB0aG9zZSBkdXBsaWNhdGVzLlxuICAgIGFkZGVkTm9kZXMuZm9yRWFjaCgobm9kZSkgPT4ge1xuICAgICAgICBub2RlLl94X2lnbm9yZVNlbGYgPSB0cnVlXG4gICAgICAgIG5vZGUuX3hfaWdub3JlID0gdHJ1ZVxuICAgIH0pXG4gICAgZm9yIChsZXQgbm9kZSBvZiBhZGRlZE5vZGVzKSB7XG4gICAgICAgIC8vIElmIGFuIGVsZW1lbnQgZ2V0cyBtb3ZlZCBvbiBhIHBhZ2UsIGl0J3MgcmVnaXN0ZXJlZFxuICAgICAgICAvLyBhcyBib3RoIGFuIFwiYWRkXCIgYW5kIFwicmVtb3ZlXCIsIHNvIHdlIHdhbnQgdG8gc2tpcCB0aG9zZS5cbiAgICAgICAgaWYgKHJlbW92ZWROb2Rlcy5pbmNsdWRlcyhub2RlKSkgY29udGludWVcblxuICAgICAgICAvLyBJZiB0aGUgbm9kZSB3YXMgZXZlbnR1YWxseSByZW1vdmVkIGFzIHBhcnQgb2Ygb25lIG9mIGhpc1xuICAgICAgICAvLyBwYXJlbnQgbXV0YXRpb25zLCBza2lwIGl0XG4gICAgICAgIGlmICghIG5vZGUuaXNDb25uZWN0ZWQpIGNvbnRpbnVlXG5cbiAgICAgICAgZGVsZXRlIG5vZGUuX3hfaWdub3JlU2VsZlxuICAgICAgICBkZWxldGUgbm9kZS5feF9pZ25vcmVcbiAgICAgICAgb25FbEFkZGVkcy5mb3JFYWNoKGkgPT4gaShub2RlKSlcbiAgICAgICAgbm9kZS5feF9pZ25vcmUgPSB0cnVlXG4gICAgICAgIG5vZGUuX3hfaWdub3JlU2VsZiA9IHRydWVcbiAgICB9XG4gICAgYWRkZWROb2Rlcy5mb3JFYWNoKChub2RlKSA9PiB7XG4gICAgICAgIGRlbGV0ZSBub2RlLl94X2lnbm9yZVNlbGZcbiAgICAgICAgZGVsZXRlIG5vZGUuX3hfaWdub3JlXG4gICAgfSlcblxuICAgIGFkZGVkTm9kZXMgPSBudWxsXG4gICAgcmVtb3ZlZE5vZGVzID0gbnVsbFxuICAgIGFkZGVkQXR0cmlidXRlcyA9IG51bGxcbiAgICByZW1vdmVkQXR0cmlidXRlcyA9IG51bGxcbn1cbiIsICJcbmV4cG9ydCBmdW5jdGlvbiBvbmNlKGNhbGxiYWNrLCBmYWxsYmFjayA9ICgpID0+IHt9KSB7XG4gICAgbGV0IGNhbGxlZCA9IGZhbHNlXG5cbiAgICByZXR1cm4gZnVuY3Rpb24gKCkge1xuICAgICAgICBpZiAoISBjYWxsZWQpIHtcbiAgICAgICAgICAgIGNhbGxlZCA9IHRydWVcblxuICAgICAgICAgICAgY2FsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgZmFsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKVxuICAgICAgICB9XG4gICAgfVxufVxuIiwgImltcG9ydCB7IG11dGF0ZURvbSB9IGZyb20gJ2FscGluZWpzL3NyYy9tdXRhdGlvbidcbmltcG9ydCB7IG9uY2UgfSBmcm9tICdhbHBpbmVqcy9zcmMvdXRpbHMvb25jZSdcblxuZXhwb3J0IGRlZmF1bHQgKEFscGluZSkgPT4ge1xuICAgIEFscGluZS5kYXRhKCdub3RpZmljYXRpb25Db21wb25lbnQnLCAoeyBub3RpZmljYXRpb24gfSkgPT4gKHtcbiAgICAgICAgaXNTaG93bjogZmFsc2UsXG5cbiAgICAgICAgY29tcHV0ZWRTdHlsZTogbnVsbCxcblxuICAgICAgICB0cmFuc2l0aW9uRHVyYXRpb246IG51bGwsXG5cbiAgICAgICAgdHJhbnNpdGlvbkVhc2luZzogbnVsbCxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLmNvbXB1dGVkU3R5bGUgPSB3aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZSh0aGlzLiRlbClcblxuICAgICAgICAgICAgdGhpcy50cmFuc2l0aW9uRHVyYXRpb24gPVxuICAgICAgICAgICAgICAgIHBhcnNlRmxvYXQodGhpcy5jb21wdXRlZFN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbikgKiAxMDAwXG5cbiAgICAgICAgICAgIHRoaXMudHJhbnNpdGlvbkVhc2luZyA9IHRoaXMuY29tcHV0ZWRTdHlsZS50cmFuc2l0aW9uVGltaW5nRnVuY3Rpb25cblxuICAgICAgICAgICAgdGhpcy5jb25maWd1cmVUcmFuc2l0aW9ucygpXG4gICAgICAgICAgICB0aGlzLmNvbmZpZ3VyZUFuaW1hdGlvbnMoKVxuXG4gICAgICAgICAgICBpZiAoXG4gICAgICAgICAgICAgICAgbm90aWZpY2F0aW9uLmR1cmF0aW9uICYmXG4gICAgICAgICAgICAgICAgbm90aWZpY2F0aW9uLmR1cmF0aW9uICE9PSAncGVyc2lzdGVudCdcbiAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4gdGhpcy5jbG9zZSgpLCBub3RpZmljYXRpb24uZHVyYXRpb24pXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuaXNTaG93biA9IHRydWVcbiAgICAgICAgfSxcblxuICAgICAgICBjb25maWd1cmVUcmFuc2l0aW9uczogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgY29uc3QgZGlzcGxheSA9IHRoaXMuY29tcHV0ZWRTdHlsZS5kaXNwbGF5XG5cbiAgICAgICAgICAgIGNvbnN0IHNob3cgPSAoKSA9PiB7XG4gICAgICAgICAgICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuc3R5bGUuc2V0UHJvcGVydHkoJ2Rpc3BsYXknLCBkaXNwbGF5KVxuICAgICAgICAgICAgICAgICAgICB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgndmlzaWJpbGl0eScsICd2aXNpYmxlJylcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIHRoaXMuJGVsLl94X2lzU2hvd24gPSB0cnVlXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnN0IGhpZGUgPSAoKSA9PiB7XG4gICAgICAgICAgICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuX3hfaXNTaG93blxuICAgICAgICAgICAgICAgICAgICAgICAgPyB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgndmlzaWJpbGl0eScsICdoaWRkZW4nKVxuICAgICAgICAgICAgICAgICAgICAgICAgOiB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgnZGlzcGxheScsICdub25lJylcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCB0b2dnbGUgPSBvbmNlKFxuICAgICAgICAgICAgICAgICh2YWx1ZSkgPT4gKHZhbHVlID8gc2hvdygpIDogaGlkZSgpKSxcbiAgICAgICAgICAgICAgICAodmFsdWUpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyhcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuJGVsLFxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWUsXG4gICAgICAgICAgICAgICAgICAgICAgICBzaG93LFxuICAgICAgICAgICAgICAgICAgICAgICAgaGlkZSxcbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICApXG5cbiAgICAgICAgICAgIEFscGluZS5lZmZlY3QoKCkgPT4gdG9nZ2xlKHRoaXMuaXNTaG93bikpXG4gICAgICAgIH0sXG5cbiAgICAgICAgY29uZmlndXJlQW5pbWF0aW9uczogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0IGFuaW1hdGlvblxuXG4gICAgICAgICAgICBMaXZld2lyZS5ob29rKFxuICAgICAgICAgICAgICAgICdjb21taXQnLFxuICAgICAgICAgICAgICAgICh7IGNvbXBvbmVudCwgY29tbWl0LCBzdWNjZWVkLCBmYWlsLCByZXNwb25kIH0pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgIWNvbXBvbmVudC5zbmFwc2hvdC5kYXRhXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmlzRmlsYW1lbnROb3RpZmljYXRpb25zQ29tcG9uZW50XG4gICAgICAgICAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBjb25zdCBnZXRUb3AgPSAoKSA9PiB0aGlzLiRlbC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKS50b3BcbiAgICAgICAgICAgICAgICAgICAgY29uc3Qgb2xkVG9wID0gZ2V0VG9wKClcblxuICAgICAgICAgICAgICAgICAgICByZXNwb25kKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFuaW1hdGlvbiA9ICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoIXRoaXMuaXNTaG93bikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLiRlbC5hbmltYXRlKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdHJhbnNmb3JtOiBgdHJhbnNsYXRlWSgke1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBvbGRUb3AgLSBnZXRUb3AoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1weClgLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHsgdHJhbnNmb3JtOiAndHJhbnNsYXRlWSgwcHgpJyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkdXJhdGlvbjogdGhpcy50cmFuc2l0aW9uRHVyYXRpb24sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlYXNpbmc6IHRoaXMudHJhbnNpdGlvbkVhc2luZyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuJGVsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmdldEFuaW1hdGlvbnMoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5mb3JFYWNoKChhbmltYXRpb24pID0+IGFuaW1hdGlvbi5maW5pc2goKSlcbiAgICAgICAgICAgICAgICAgICAgfSlcblxuICAgICAgICAgICAgICAgICAgICBzdWNjZWVkKCh7IHNuYXBzaG90LCBlZmZlY3QgfSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgYW5pbWF0aW9uKClcbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgKVxuICAgICAgICB9LFxuXG4gICAgICAgIGNsb3NlOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLmlzU2hvd24gPSBmYWxzZVxuXG4gICAgICAgICAgICBzZXRUaW1lb3V0KFxuICAgICAgICAgICAgICAgICgpID0+XG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5kaXNwYXRjaEV2ZW50KFxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3IEN1c3RvbUV2ZW50KCdub3RpZmljYXRpb25DbG9zZWQnLCB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGV0YWlsOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlkOiBub3RpZmljYXRpb24uaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pLFxuICAgICAgICAgICAgICAgICAgICApLFxuICAgICAgICAgICAgICAgIHRoaXMudHJhbnNpdGlvbkR1cmF0aW9uLFxuICAgICAgICAgICAgKVxuICAgICAgICB9LFxuXG4gICAgICAgIG1hcmtBc1JlYWQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHdpbmRvdy5kaXNwYXRjaEV2ZW50KFxuICAgICAgICAgICAgICAgIG5ldyBDdXN0b21FdmVudCgnbWFya2VkTm90aWZpY2F0aW9uQXNSZWFkJywge1xuICAgICAgICAgICAgICAgICAgICBkZXRhaWw6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkOiBub3RpZmljYXRpb24uaWQsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgfSksXG4gICAgICAgICAgICApXG4gICAgICAgIH0sXG5cbiAgICAgICAgbWFya0FzVW5yZWFkOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB3aW5kb3cuZGlzcGF0Y2hFdmVudChcbiAgICAgICAgICAgICAgICBuZXcgQ3VzdG9tRXZlbnQoJ21hcmtlZE5vdGlmaWNhdGlvbkFzVW5yZWFkJywge1xuICAgICAgICAgICAgICAgICAgICBkZXRhaWw6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkOiBub3RpZmljYXRpb24uaWQsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgfSksXG4gICAgICAgICAgICApXG4gICAgICAgIH0sXG4gICAgfSkpXG59XG4iLCAiaW1wb3J0IHsgdjQgYXMgdXVpZCB9IGZyb20gJ3V1aWQtYnJvd3NlcidcblxuY2xhc3MgTm90aWZpY2F0aW9uIHtcbiAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgdGhpcy5pZCh1dWlkKCkpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpZChpZCkge1xuICAgICAgICB0aGlzLmlkID0gaWRcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHRpdGxlKHRpdGxlKSB7XG4gICAgICAgIHRoaXMudGl0bGUgPSB0aXRsZVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgYm9keShib2R5KSB7XG4gICAgICAgIHRoaXMuYm9keSA9IGJvZHlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGFjdGlvbnMoYWN0aW9ucykge1xuICAgICAgICB0aGlzLmFjdGlvbnMgPSBhY3Rpb25zXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBzdGF0dXMoc3RhdHVzKSB7XG4gICAgICAgIHN3aXRjaCAoc3RhdHVzKSB7XG4gICAgICAgICAgICBjYXNlICdkYW5nZXInOlxuICAgICAgICAgICAgICAgIHRoaXMuZGFuZ2VyKClcblxuICAgICAgICAgICAgICAgIGJyZWFrXG5cbiAgICAgICAgICAgIGNhc2UgJ2luZm8nOlxuICAgICAgICAgICAgICAgIHRoaXMuaW5mbygpXG5cbiAgICAgICAgICAgICAgICBicmVha1xuXG4gICAgICAgICAgICBjYXNlICdzdWNjZXNzJzpcbiAgICAgICAgICAgICAgICB0aGlzLnN1Y2Nlc3MoKVxuXG4gICAgICAgICAgICAgICAgYnJlYWtcblxuICAgICAgICAgICAgY2FzZSAnd2FybmluZyc6XG4gICAgICAgICAgICAgICAgdGhpcy53YXJuaW5nKClcblxuICAgICAgICAgICAgICAgIGJyZWFrXG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGNvbG9yKGNvbG9yKSB7XG4gICAgICAgIHRoaXMuY29sb3IgPSBjb2xvclxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgaWNvbihpY29uKSB7XG4gICAgICAgIHRoaXMuaWNvbiA9IGljb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGljb25Db2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmljb25Db2xvciA9IGNvbG9yXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkdXJhdGlvbihkdXJhdGlvbikge1xuICAgICAgICB0aGlzLmR1cmF0aW9uID0gZHVyYXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHNlY29uZHMoc2Vjb25kcykge1xuICAgICAgICB0aGlzLmR1cmF0aW9uKHNlY29uZHMgKiAxMDAwKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgcGVyc2lzdGVudCgpIHtcbiAgICAgICAgdGhpcy5kdXJhdGlvbigncGVyc2lzdGVudCcpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkYW5nZXIoKSB7XG4gICAgICAgIHRoaXMuaWNvbignaGVyb2ljb24tby14LWNpcmNsZScpXG4gICAgICAgIHRoaXMuaWNvbkNvbG9yKCdkYW5nZXInKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgaW5mbygpIHtcbiAgICAgICAgdGhpcy5pY29uKCdoZXJvaWNvbi1vLWluZm9ybWF0aW9uLWNpcmNsZScpXG4gICAgICAgIHRoaXMuaWNvbkNvbG9yKCdpbmZvJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHN1Y2Nlc3MoKSB7XG4gICAgICAgIHRoaXMuaWNvbignaGVyb2ljb24tby1jaGVjay1jaXJjbGUnKVxuICAgICAgICB0aGlzLmljb25Db2xvcignc3VjY2VzcycpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICB3YXJuaW5nKCkge1xuICAgICAgICB0aGlzLmljb24oJ2hlcm9pY29uLW8tZXhjbGFtYXRpb24tY2lyY2xlJylcbiAgICAgICAgdGhpcy5pY29uQ29sb3IoJ3dhcm5pbmcnKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgdmlldyh2aWV3KSB7XG4gICAgICAgIHRoaXMudmlldyA9IHZpZXdcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHZpZXdEYXRhKHZpZXdEYXRhKSB7XG4gICAgICAgIHRoaXMudmlld0RhdGEgPSB2aWV3RGF0YVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgc2VuZCgpIHtcbiAgICAgICAgd2luZG93LmRpc3BhdGNoRXZlbnQoXG4gICAgICAgICAgICBuZXcgQ3VzdG9tRXZlbnQoJ25vdGlmaWNhdGlvblNlbnQnLCB7XG4gICAgICAgICAgICAgICAgZGV0YWlsOiB7XG4gICAgICAgICAgICAgICAgICAgIG5vdGlmaWNhdGlvbjogdGhpcyxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSksXG4gICAgICAgIClcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cbn1cblxuY2xhc3MgQWN0aW9uIHtcbiAgICBjb25zdHJ1Y3RvcihuYW1lKSB7XG4gICAgICAgIHRoaXMubmFtZShuYW1lKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgbmFtZShuYW1lKSB7XG4gICAgICAgIHRoaXMubmFtZSA9IG5hbWVcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGNvbG9yKGNvbG9yKSB7XG4gICAgICAgIHRoaXMuY29sb3IgPSBjb2xvclxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZGlzcGF0Y2goZXZlbnQsIGRhdGEpIHtcbiAgICAgICAgdGhpcy5ldmVudChldmVudClcbiAgICAgICAgdGhpcy5ldmVudERhdGEoZGF0YSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGRpc3BhdGNoU2VsZihldmVudCwgZGF0YSkge1xuICAgICAgICB0aGlzLmRpc3BhdGNoKGV2ZW50LCBkYXRhKVxuICAgICAgICB0aGlzLmRpc3BhdGNoRGlyZWN0aW9uID0gJ3NlbGYnXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkaXNwYXRjaFRvKGNvbXBvbmVudCwgZXZlbnQsIGRhdGEpIHtcbiAgICAgICAgdGhpcy5kaXNwYXRjaChldmVudCwgZGF0YSlcbiAgICAgICAgdGhpcy5kaXNwYXRjaERpcmVjdGlvbiA9ICd0bydcbiAgICAgICAgdGhpcy5kaXNwYXRjaFRvQ29tcG9uZW50ID0gY29tcG9uZW50XG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAZGVwcmVjYXRlZCBVc2UgYGRpc3BhdGNoKClgIGluc3RlYWQuXG4gICAgICovXG4gICAgZW1pdChldmVudCwgZGF0YSkge1xuICAgICAgICB0aGlzLmRpc3BhdGNoKGV2ZW50LCBkYXRhKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGRlcHJlY2F0ZWQgVXNlIGBkaXNwYXRjaFNlbGYoKWAgaW5zdGVhZC5cbiAgICAgKi9cbiAgICBlbWl0U2VsZihldmVudCwgZGF0YSkge1xuICAgICAgICB0aGlzLmRpc3BhdGNoU2VsZihldmVudCwgZGF0YSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBkZXByZWNhdGVkIFVzZSBgZGlzcGF0Y2hUbygpYCBpbnN0ZWFkLlxuICAgICAqL1xuICAgIGVtaXRUbyhjb21wb25lbnQsIGV2ZW50LCBkYXRhKSB7XG4gICAgICAgIHRoaXMuZGlzcGF0Y2hUbyhjb21wb25lbnQsIGV2ZW50LCBkYXRhKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZGlzcGF0Y2hEaXJlY3Rpb24oZGlzcGF0Y2hEaXJlY3Rpb24pIHtcbiAgICAgICAgdGhpcy5kaXNwYXRjaERpcmVjdGlvbiA9IGRpc3BhdGNoRGlyZWN0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkaXNwYXRjaFRvQ29tcG9uZW50KGNvbXBvbmVudCkge1xuICAgICAgICB0aGlzLmRpc3BhdGNoVG9Db21wb25lbnQgPSBjb21wb25lbnRcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGV2ZW50KGV2ZW50KSB7XG4gICAgICAgIHRoaXMuZXZlbnQgPSBldmVudFxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZXZlbnREYXRhKGRhdGEpIHtcbiAgICAgICAgdGhpcy5ldmVudERhdGEgPSBkYXRhXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBleHRyYUF0dHJpYnV0ZXMoYXR0cmlidXRlcykge1xuICAgICAgICB0aGlzLmV4dHJhQXR0cmlidXRlcyA9IGF0dHJpYnV0ZXNcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGljb24oaWNvbikge1xuICAgICAgICB0aGlzLmljb24gPSBpY29uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpY29uUG9zaXRpb24ocG9zaXRpb24pIHtcbiAgICAgICAgdGhpcy5pY29uUG9zaXRpb24gPSBwb3NpdGlvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgb3V0bGluZWQoY29uZGl0aW9uID0gdHJ1ZSkge1xuICAgICAgICB0aGlzLmlzT3V0bGluZWQgPSBjb25kaXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGRpc2FibGVkKGNvbmRpdGlvbiA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5pc0Rpc2FibGVkID0gY29uZGl0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBsYWJlbChsYWJlbCkge1xuICAgICAgICB0aGlzLmxhYmVsID0gbGFiZWxcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGNsb3NlKGNvbmRpdGlvbiA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5zaG91bGRDbG9zZSA9IGNvbmRpdGlvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgb3BlblVybEluTmV3VGFiKGNvbmRpdGlvbiA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5zaG91bGRPcGVuVXJsSW5OZXdUYWIgPSBjb25kaXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHNpemUoc2l6ZSkge1xuICAgICAgICB0aGlzLnNpemUgPSBzaXplXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICB1cmwodXJsKSB7XG4gICAgICAgIHRoaXMudXJsID0gdXJsXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICB2aWV3KHZpZXcpIHtcbiAgICAgICAgdGhpcy52aWV3ID0gdmlld1xuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgYnV0dG9uKCkge1xuICAgICAgICB0aGlzLnZpZXcoJ2ZpbGFtZW50LW5vdGlmaWNhdGlvbnM6OmFjdGlvbnMuYnV0dG9uLWFjdGlvbicpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBncm91cGVkKCkge1xuICAgICAgICB0aGlzLnZpZXcoJ2ZpbGFtZW50LW5vdGlmaWNhdGlvbnM6OmFjdGlvbnMuZ3JvdXBlZC1hY3Rpb24nKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgbGluaygpIHtcbiAgICAgICAgdGhpcy52aWV3KCdmaWxhbWVudC1ub3RpZmljYXRpb25zOjphY3Rpb25zLmxpbmstYWN0aW9uJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cbn1cblxuY2xhc3MgQWN0aW9uR3JvdXAge1xuICAgIGNvbnN0cnVjdG9yKGFjdGlvbnMpIHtcbiAgICAgICAgdGhpcy5hY3Rpb25zKGFjdGlvbnMpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBhY3Rpb25zKGFjdGlvbnMpIHtcbiAgICAgICAgdGhpcy5hY3Rpb25zID0gYWN0aW9ucy5tYXAoKGFjdGlvbikgPT4gYWN0aW9uLmdyb3VwZWQoKSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGNvbG9yKGNvbG9yKSB7XG4gICAgICAgIHRoaXMuY29sb3IgPSBjb2xvclxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgaWNvbihpY29uKSB7XG4gICAgICAgIHRoaXMuaWNvbiA9IGljb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGljb25Qb3NpdGlvbihwb3NpdGlvbikge1xuICAgICAgICB0aGlzLmljb25Qb3NpdGlvbiA9IHBvc2l0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBsYWJlbChsYWJlbCkge1xuICAgICAgICB0aGlzLmxhYmVsID0gbGFiZWxcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHRvb2x0aXAodG9vbHRpcCkge1xuICAgICAgICB0aGlzLnRvb2x0aXAgPSB0b29sdGlwXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG59XG5cbmV4cG9ydCB7IEFjdGlvbiwgQWN0aW9uR3JvdXAsIE5vdGlmaWNhdGlvbiB9XG4iLCAiaW1wb3J0IE5vdGlmaWNhdGlvbkNvbXBvbmVudEFscGluZVBsdWdpbiBmcm9tICcuL2NvbXBvbmVudHMvbm90aWZpY2F0aW9uJ1xuaW1wb3J0IHtcbiAgICBBY3Rpb24gYXMgTm90aWZpY2F0aW9uQWN0aW9uLFxuICAgIEFjdGlvbkdyb3VwIGFzIE5vdGlmaWNhdGlvbkFjdGlvbkdyb3VwLFxuICAgIE5vdGlmaWNhdGlvbixcbn0gZnJvbSAnLi9Ob3RpZmljYXRpb24nXG5cbndpbmRvdy5GaWxhbWVudE5vdGlmaWNhdGlvbkFjdGlvbiA9IE5vdGlmaWNhdGlvbkFjdGlvblxud2luZG93LkZpbGFtZW50Tm90aWZpY2F0aW9uQWN0aW9uR3JvdXAgPSBOb3RpZmljYXRpb25BY3Rpb25Hcm91cFxud2luZG93LkZpbGFtZW50Tm90aWZpY2F0aW9uID0gTm90aWZpY2F0aW9uXG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2FscGluZTppbml0JywgKCkgPT4ge1xuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKE5vdGlmaWNhdGlvbkNvbXBvbmVudEFscGluZVBsdWdpbilcbn0pXG4iXSwKICAibWFwcGluZ3MiOiAiOzs7Ozs7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LFdBQUssVUFBVSxRQUFRLElBQUksQ0FBQyxXQUFXLE9BQU8sUUFBUSxDQUFDO0FBRXZELGFBQU87QUFBQSxJQUNYO0FBQUEsSUFFQSxNQUFNLE9BQU87QUFDVCxXQUFLLFFBQVE7QUFFYixhQUFPO0FBQUEsSUFDWDtBQUFBLElBRUEsS0FBSyxNQUFNO0FBQ1AsV0FBSyxPQUFPO0FBRVosYUFBTztBQUFBLElBQ1g7QUFBQSxJQUVBLGFBQWEsVUFBVTtBQUNuQixXQUFLLGVBQWU7QUFFcEIsYUFBTztBQUFBLElBQ1g7QUFBQSxJQUVBLE1BQU0sT0FBTztBQUNULFdBQUssUUFBUTtBQUViLGFBQU87QUFBQSxJQUNYO0FBQUEsSUFFQSxRQUFRLFNBQVM7QUFDYixXQUFLLFVBQVU7QUFFZixhQUFPO0FBQUEsSUFDWDtBQUFBLEVBQ0o7OztBQ3hXQSxTQUFPLDZCQUE2QjtBQUNwQyxTQUFPLGtDQUFrQztBQUN6QyxTQUFPLHVCQUF1QjtBQUU5QixXQUFTLGlCQUFpQixlQUFlLE1BQU07QUFDM0MsV0FBTyxPQUFPLE9BQU8sb0JBQWlDO0FBQUEsRUFDMUQsQ0FBQzsiLAogICJuYW1lcyI6IFsiaSIsICJ1dWlkIiwgImFuaW1hdGlvbiIsICJ1dWlkIl0KfQo=
