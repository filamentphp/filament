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
          Alpine.mutateDom(() => {
            this.$el.style.setProperty("display", display);
            this.$el.style.setProperty("visibility", "visible");
          });
          this.$el._x_isShown = true;
        };
        const hide = () => {
          Alpine.mutateDom(() => {
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
      this.status = status;
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
      this.status("danger");
      return this;
    }
    info() {
      this.status("info");
      return this;
    }
    success() {
      this.status("success");
      return this;
    }
    warning() {
      this.status("warning");
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci9saWIvcm5nLWJyb3dzZXIuanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci9saWIvYnl0ZXNUb1V1aWQuanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3V1aWQtYnJvd3Nlci92MS5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvdXVpZC1icm93c2VyL3Y0LmpzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy91dWlkLWJyb3dzZXIvaW5kZXguanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2FscGluZWpzL3NyYy91dGlscy9vbmNlLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL25vdGlmaWNhdGlvbi5qcyIsICIuLi9yZXNvdXJjZXMvanMvTm90aWZpY2F0aW9uLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9pbmRleC5qcyJdLAogICJzb3VyY2VzQ29udGVudCI6IFsiLy8gVW5pcXVlIElEIGNyZWF0aW9uIHJlcXVpcmVzIGEgaGlnaCBxdWFsaXR5IHJhbmRvbSAjIGdlbmVyYXRvci4gIEluIHRoZVxuLy8gYnJvd3NlciB0aGlzIGlzIGEgbGl0dGxlIGNvbXBsaWNhdGVkIGR1ZSB0byB1bmtub3duIHF1YWxpdHkgb2YgTWF0aC5yYW5kb20oKVxuLy8gYW5kIGluY29uc2lzdGVudCBzdXBwb3J0IGZvciB0aGUgYGNyeXB0b2AgQVBJLiAgV2UgZG8gdGhlIGJlc3Qgd2UgY2FuIHZpYVxuLy8gZmVhdHVyZS1kZXRlY3Rpb25cbnZhciBybmc7XG5cbnZhciBjcnlwdG8gPSB0eXBlb2YgZ2xvYmFsICE9PSAndW5kZWZpbmVkJyAmJiAoZ2xvYmFsLmNyeXB0byB8fCBnbG9iYWwubXNDcnlwdG8pOyAvLyBmb3IgSUUgMTFcbmlmIChjcnlwdG8gJiYgY3J5cHRvLmdldFJhbmRvbVZhbHVlcykge1xuICAvLyBXSEFUV0cgY3J5cHRvIFJORyAtIGh0dHA6Ly93aWtpLndoYXR3Zy5vcmcvd2lraS9DcnlwdG9cbiAgdmFyIHJuZHM4ID0gbmV3IFVpbnQ4QXJyYXkoMTYpOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVuZGVmXG4gIHJuZyA9IGZ1bmN0aW9uIHdoYXR3Z1JORygpIHtcbiAgICBjcnlwdG8uZ2V0UmFuZG9tVmFsdWVzKHJuZHM4KTtcbiAgICByZXR1cm4gcm5kczg7XG4gIH07XG59XG5cbmlmICghcm5nKSB7XG4gIC8vIE1hdGgucmFuZG9tKCktYmFzZWQgKFJORylcbiAgLy9cbiAgLy8gSWYgYWxsIGVsc2UgZmFpbHMsIHVzZSBNYXRoLnJhbmRvbSgpLiAgSXQncyBmYXN0LCBidXQgaXMgb2YgdW5zcGVjaWZpZWRcbiAgLy8gcXVhbGl0eS5cbiAgdmFyIHJuZHMgPSBuZXcgQXJyYXkoMTYpO1xuICBybmcgPSBmdW5jdGlvbigpIHtcbiAgICBmb3IgKHZhciBpID0gMCwgcjsgaSA8IDE2OyBpKyspIHtcbiAgICAgIGlmICgoaSAmIDB4MDMpID09PSAwKSByID0gTWF0aC5yYW5kb20oKSAqIDB4MTAwMDAwMDAwO1xuICAgICAgcm5kc1tpXSA9IHIgPj4+ICgoaSAmIDB4MDMpIDw8IDMpICYgMHhmZjtcbiAgICB9XG5cbiAgICByZXR1cm4gcm5kcztcbiAgfTtcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBybmc7XG4iLCAiLyoqXG4gKiBDb252ZXJ0IGFycmF5IG9mIDE2IGJ5dGUgdmFsdWVzIHRvIFVVSUQgc3RyaW5nIGZvcm1hdCBvZiB0aGUgZm9ybTpcbiAqIFhYWFhYWFhYLVhYWFgtWFhYWC1YWFhYLVhYWFhYWFhYWFhYWFxuICovXG52YXIgYnl0ZVRvSGV4ID0gW107XG5mb3IgKHZhciBpID0gMDsgaSA8IDI1NjsgKytpKSB7XG4gIGJ5dGVUb0hleFtpXSA9IChpICsgMHgxMDApLnRvU3RyaW5nKDE2KS5zdWJzdHIoMSk7XG59XG5cbmZ1bmN0aW9uIGJ5dGVzVG9VdWlkKGJ1Ziwgb2Zmc2V0KSB7XG4gIHZhciBpID0gb2Zmc2V0IHx8IDA7XG4gIHZhciBidGggPSBieXRlVG9IZXg7XG4gIHJldHVybiBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXSArXG4gICAgICAgICAgYnRoW2J1ZltpKytdXSArIGJ0aFtidWZbaSsrXV0gKyAnLScgK1xuICAgICAgICAgIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICsgJy0nICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXSArICctJyArXG4gICAgICAgICAgYnRoW2J1ZltpKytdXSArIGJ0aFtidWZbaSsrXV0gKyAnLScgK1xuICAgICAgICAgIGJ0aFtidWZbaSsrXV0gKyBidGhbYnVmW2krK11dICtcbiAgICAgICAgICBidGhbYnVmW2krK11dICsgYnRoW2J1ZltpKytdXSArXG4gICAgICAgICAgYnRoW2J1ZltpKytdXSArIGJ0aFtidWZbaSsrXV07XG59XG5cbm1vZHVsZS5leHBvcnRzID0gYnl0ZXNUb1V1aWQ7XG4iLCAidmFyIHJuZyA9IHJlcXVpcmUoJy4vbGliL3JuZy1icm93c2VyJyk7XG52YXIgYnl0ZXNUb1V1aWQgPSByZXF1aXJlKCcuL2xpYi9ieXRlc1RvVXVpZCcpO1xuXG4vLyAqKmB2MSgpYCAtIEdlbmVyYXRlIHRpbWUtYmFzZWQgVVVJRCoqXG4vL1xuLy8gSW5zcGlyZWQgYnkgaHR0cHM6Ly9naXRodWIuY29tL0xpb3NLL1VVSUQuanNcbi8vIGFuZCBodHRwOi8vZG9jcy5weXRob24ub3JnL2xpYnJhcnkvdXVpZC5odG1sXG5cbi8vIHJhbmRvbSAjJ3Mgd2UgbmVlZCB0byBpbml0IG5vZGUgYW5kIGNsb2Nrc2VxXG52YXIgX3NlZWRCeXRlcyA9IHJuZygpO1xuXG4vLyBQZXIgNC41LCBjcmVhdGUgYW5kIDQ4LWJpdCBub2RlIGlkLCAoNDcgcmFuZG9tIGJpdHMgKyBtdWx0aWNhc3QgYml0ID0gMSlcbnZhciBfbm9kZUlkID0gW1xuICBfc2VlZEJ5dGVzWzBdIHwgMHgwMSxcbiAgX3NlZWRCeXRlc1sxXSwgX3NlZWRCeXRlc1syXSwgX3NlZWRCeXRlc1szXSwgX3NlZWRCeXRlc1s0XSwgX3NlZWRCeXRlc1s1XVxuXTtcblxuLy8gUGVyIDQuMi4yLCByYW5kb21pemUgKDE0IGJpdCkgY2xvY2tzZXFcbnZhciBfY2xvY2tzZXEgPSAoX3NlZWRCeXRlc1s2XSA8PCA4IHwgX3NlZWRCeXRlc1s3XSkgJiAweDNmZmY7XG5cbi8vIFByZXZpb3VzIHV1aWQgY3JlYXRpb24gdGltZVxudmFyIF9sYXN0TVNlY3MgPSAwLCBfbGFzdE5TZWNzID0gMDtcblxuLy8gU2VlIGh0dHBzOi8vZ2l0aHViLmNvbS9icm9vZmEvbm9kZS11dWlkIGZvciBBUEkgZGV0YWlsc1xuZnVuY3Rpb24gdjEob3B0aW9ucywgYnVmLCBvZmZzZXQpIHtcbiAgdmFyIGkgPSBidWYgJiYgb2Zmc2V0IHx8IDA7XG4gIHZhciBiID0gYnVmIHx8IFtdO1xuXG4gIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuXG4gIHZhciBjbG9ja3NlcSA9IG9wdGlvbnMuY2xvY2tzZXEgIT09IHVuZGVmaW5lZCA/IG9wdGlvbnMuY2xvY2tzZXEgOiBfY2xvY2tzZXE7XG5cbiAgLy8gVVVJRCB0aW1lc3RhbXBzIGFyZSAxMDAgbmFuby1zZWNvbmQgdW5pdHMgc2luY2UgdGhlIEdyZWdvcmlhbiBlcG9jaCxcbiAgLy8gKDE1ODItMTAtMTUgMDA6MDApLiAgSlNOdW1iZXJzIGFyZW4ndCBwcmVjaXNlIGVub3VnaCBmb3IgdGhpcywgc29cbiAgLy8gdGltZSBpcyBoYW5kbGVkIGludGVybmFsbHkgYXMgJ21zZWNzJyAoaW50ZWdlciBtaWxsaXNlY29uZHMpIGFuZCAnbnNlY3MnXG4gIC8vICgxMDAtbmFub3NlY29uZHMgb2Zmc2V0IGZyb20gbXNlY3MpIHNpbmNlIHVuaXggZXBvY2gsIDE5NzAtMDEtMDEgMDA6MDAuXG4gIHZhciBtc2VjcyA9IG9wdGlvbnMubXNlY3MgIT09IHVuZGVmaW5lZCA/IG9wdGlvbnMubXNlY3MgOiBuZXcgRGF0ZSgpLmdldFRpbWUoKTtcblxuICAvLyBQZXIgNC4yLjEuMiwgdXNlIGNvdW50IG9mIHV1aWQncyBnZW5lcmF0ZWQgZHVyaW5nIHRoZSBjdXJyZW50IGNsb2NrXG4gIC8vIGN5Y2xlIHRvIHNpbXVsYXRlIGhpZ2hlciByZXNvbHV0aW9uIGNsb2NrXG4gIHZhciBuc2VjcyA9IG9wdGlvbnMubnNlY3MgIT09IHVuZGVmaW5lZCA/IG9wdGlvbnMubnNlY3MgOiBfbGFzdE5TZWNzICsgMTtcblxuICAvLyBUaW1lIHNpbmNlIGxhc3QgdXVpZCBjcmVhdGlvbiAoaW4gbXNlY3MpXG4gIHZhciBkdCA9IChtc2VjcyAtIF9sYXN0TVNlY3MpICsgKG5zZWNzIC0gX2xhc3ROU2VjcykvMTAwMDA7XG5cbiAgLy8gUGVyIDQuMi4xLjIsIEJ1bXAgY2xvY2tzZXEgb24gY2xvY2sgcmVncmVzc2lvblxuICBpZiAoZHQgPCAwICYmIG9wdGlvbnMuY2xvY2tzZXEgPT09IHVuZGVmaW5lZCkge1xuICAgIGNsb2Nrc2VxID0gY2xvY2tzZXEgKyAxICYgMHgzZmZmO1xuICB9XG5cbiAgLy8gUmVzZXQgbnNlY3MgaWYgY2xvY2sgcmVncmVzc2VzIChuZXcgY2xvY2tzZXEpIG9yIHdlJ3ZlIG1vdmVkIG9udG8gYSBuZXdcbiAgLy8gdGltZSBpbnRlcnZhbFxuICBpZiAoKGR0IDwgMCB8fCBtc2VjcyA+IF9sYXN0TVNlY3MpICYmIG9wdGlvbnMubnNlY3MgPT09IHVuZGVmaW5lZCkge1xuICAgIG5zZWNzID0gMDtcbiAgfVxuXG4gIC8vIFBlciA0LjIuMS4yIFRocm93IGVycm9yIGlmIHRvbyBtYW55IHV1aWRzIGFyZSByZXF1ZXN0ZWRcbiAgaWYgKG5zZWNzID49IDEwMDAwKSB7XG4gICAgdGhyb3cgbmV3IEVycm9yKCd1dWlkLnYxKCk6IENhblxcJ3QgY3JlYXRlIG1vcmUgdGhhbiAxME0gdXVpZHMvc2VjJyk7XG4gIH1cblxuICBfbGFzdE1TZWNzID0gbXNlY3M7XG4gIF9sYXN0TlNlY3MgPSBuc2VjcztcbiAgX2Nsb2Nrc2VxID0gY2xvY2tzZXE7XG5cbiAgLy8gUGVyIDQuMS40IC0gQ29udmVydCBmcm9tIHVuaXggZXBvY2ggdG8gR3JlZ29yaWFuIGVwb2NoXG4gIG1zZWNzICs9IDEyMjE5MjkyODAwMDAwO1xuXG4gIC8vIGB0aW1lX2xvd2BcbiAgdmFyIHRsID0gKChtc2VjcyAmIDB4ZmZmZmZmZikgKiAxMDAwMCArIG5zZWNzKSAlIDB4MTAwMDAwMDAwO1xuICBiW2krK10gPSB0bCA+Pj4gMjQgJiAweGZmO1xuICBiW2krK10gPSB0bCA+Pj4gMTYgJiAweGZmO1xuICBiW2krK10gPSB0bCA+Pj4gOCAmIDB4ZmY7XG4gIGJbaSsrXSA9IHRsICYgMHhmZjtcblxuICAvLyBgdGltZV9taWRgXG4gIHZhciB0bWggPSAobXNlY3MgLyAweDEwMDAwMDAwMCAqIDEwMDAwKSAmIDB4ZmZmZmZmZjtcbiAgYltpKytdID0gdG1oID4+PiA4ICYgMHhmZjtcbiAgYltpKytdID0gdG1oICYgMHhmZjtcblxuICAvLyBgdGltZV9oaWdoX2FuZF92ZXJzaW9uYFxuICBiW2krK10gPSB0bWggPj4+IDI0ICYgMHhmIHwgMHgxMDsgLy8gaW5jbHVkZSB2ZXJzaW9uXG4gIGJbaSsrXSA9IHRtaCA+Pj4gMTYgJiAweGZmO1xuXG4gIC8vIGBjbG9ja19zZXFfaGlfYW5kX3Jlc2VydmVkYCAoUGVyIDQuMi4yIC0gaW5jbHVkZSB2YXJpYW50KVxuICBiW2krK10gPSBjbG9ja3NlcSA+Pj4gOCB8IDB4ODA7XG5cbiAgLy8gYGNsb2NrX3NlcV9sb3dgXG4gIGJbaSsrXSA9IGNsb2Nrc2VxICYgMHhmZjtcblxuICAvLyBgbm9kZWBcbiAgdmFyIG5vZGUgPSBvcHRpb25zLm5vZGUgfHwgX25vZGVJZDtcbiAgZm9yICh2YXIgbiA9IDA7IG4gPCA2OyArK24pIHtcbiAgICBiW2kgKyBuXSA9IG5vZGVbbl07XG4gIH1cblxuICByZXR1cm4gYnVmID8gYnVmIDogYnl0ZXNUb1V1aWQoYik7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gdjE7XG4iLCAidmFyIHJuZyA9IHJlcXVpcmUoJy4vbGliL3JuZy1icm93c2VyJyk7XG52YXIgYnl0ZXNUb1V1aWQgPSByZXF1aXJlKCcuL2xpYi9ieXRlc1RvVXVpZCcpO1xuXG5mdW5jdGlvbiB2NChvcHRpb25zLCBidWYsIG9mZnNldCkge1xuICB2YXIgaSA9IGJ1ZiAmJiBvZmZzZXQgfHwgMDtcblxuICBpZiAodHlwZW9mKG9wdGlvbnMpID09ICdzdHJpbmcnKSB7XG4gICAgYnVmID0gb3B0aW9ucyA9PSAnYmluYXJ5JyA/IG5ldyBBcnJheSgxNikgOiBudWxsO1xuICAgIG9wdGlvbnMgPSBudWxsO1xuICB9XG4gIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuXG4gIHZhciBybmRzID0gb3B0aW9ucy5yYW5kb20gfHwgKG9wdGlvbnMucm5nIHx8IHJuZykoKTtcblxuICAvLyBQZXIgNC40LCBzZXQgYml0cyBmb3IgdmVyc2lvbiBhbmQgYGNsb2NrX3NlcV9oaV9hbmRfcmVzZXJ2ZWRgXG4gIHJuZHNbNl0gPSAocm5kc1s2XSAmIDB4MGYpIHwgMHg0MDtcbiAgcm5kc1s4XSA9IChybmRzWzhdICYgMHgzZikgfCAweDgwO1xuXG4gIC8vIENvcHkgYnl0ZXMgdG8gYnVmZmVyLCBpZiBwcm92aWRlZFxuICBpZiAoYnVmKSB7XG4gICAgZm9yICh2YXIgaWkgPSAwOyBpaSA8IDE2OyArK2lpKSB7XG4gICAgICBidWZbaSArIGlpXSA9IHJuZHNbaWldO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBidWYgfHwgYnl0ZXNUb1V1aWQocm5kcyk7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gdjQ7XG4iLCAidmFyIHYxID0gcmVxdWlyZSgnLi92MScpO1xudmFyIHY0ID0gcmVxdWlyZSgnLi92NCcpO1xuXG52YXIgdXVpZCA9IHY0O1xudXVpZC52MSA9IHYxO1xudXVpZC52NCA9IHY0O1xuXG5tb2R1bGUuZXhwb3J0cyA9IHV1aWQ7XG4iLCAiXG5leHBvcnQgZnVuY3Rpb24gb25jZShjYWxsYmFjaywgZmFsbGJhY2sgPSAoKSA9PiB7fSkge1xuICAgIGxldCBjYWxsZWQgPSBmYWxzZVxuXG4gICAgcmV0dXJuIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgaWYgKCEgY2FsbGVkKSB7XG4gICAgICAgICAgICBjYWxsZWQgPSB0cnVlXG5cbiAgICAgICAgICAgIGNhbGxiYWNrLmFwcGx5KHRoaXMsIGFyZ3VtZW50cylcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGZhbGxiYWNrLmFwcGx5KHRoaXMsIGFyZ3VtZW50cylcbiAgICAgICAgfVxuICAgIH1cbn1cbiIsICJpbXBvcnQgeyBvbmNlIH0gZnJvbSAnYWxwaW5lanMvc3JjL3V0aWxzL29uY2UnXG5cbmV4cG9ydCBkZWZhdWx0IChBbHBpbmUpID0+IHtcbiAgICBBbHBpbmUuZGF0YSgnbm90aWZpY2F0aW9uQ29tcG9uZW50JywgKHsgbm90aWZpY2F0aW9uIH0pID0+ICh7XG4gICAgICAgIGlzU2hvd246IGZhbHNlLFxuXG4gICAgICAgIGNvbXB1dGVkU3R5bGU6IG51bGwsXG5cbiAgICAgICAgdHJhbnNpdGlvbkR1cmF0aW9uOiBudWxsLFxuXG4gICAgICAgIHRyYW5zaXRpb25FYXNpbmc6IG51bGwsXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdGhpcy5jb21wdXRlZFN0eWxlID0gd2luZG93LmdldENvbXB1dGVkU3R5bGUodGhpcy4kZWwpXG5cbiAgICAgICAgICAgIHRoaXMudHJhbnNpdGlvbkR1cmF0aW9uID1cbiAgICAgICAgICAgICAgICBwYXJzZUZsb2F0KHRoaXMuY29tcHV0ZWRTdHlsZS50cmFuc2l0aW9uRHVyYXRpb24pICogMTAwMFxuXG4gICAgICAgICAgICB0aGlzLnRyYW5zaXRpb25FYXNpbmcgPSB0aGlzLmNvbXB1dGVkU3R5bGUudHJhbnNpdGlvblRpbWluZ0Z1bmN0aW9uXG5cbiAgICAgICAgICAgIHRoaXMuY29uZmlndXJlVHJhbnNpdGlvbnMoKVxuICAgICAgICAgICAgdGhpcy5jb25maWd1cmVBbmltYXRpb25zKClcblxuICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgIG5vdGlmaWNhdGlvbi5kdXJhdGlvbiAmJlxuICAgICAgICAgICAgICAgIG5vdGlmaWNhdGlvbi5kdXJhdGlvbiAhPT0gJ3BlcnNpc3RlbnQnXG4gICAgICAgICAgICApIHtcbiAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHRoaXMuY2xvc2UoKSwgbm90aWZpY2F0aW9uLmR1cmF0aW9uKVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLmlzU2hvd24gPSB0cnVlXG4gICAgICAgIH0sXG5cbiAgICAgICAgY29uZmlndXJlVHJhbnNpdGlvbnM6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGNvbnN0IGRpc3BsYXkgPSB0aGlzLmNvbXB1dGVkU3R5bGUuZGlzcGxheVxuXG4gICAgICAgICAgICBjb25zdCBzaG93ID0gKCkgPT4ge1xuICAgICAgICAgICAgICAgIEFscGluZS5tdXRhdGVEb20oKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgnZGlzcGxheScsIGRpc3BsYXkpXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuJGVsLnN0eWxlLnNldFByb3BlcnR5KCd2aXNpYmlsaXR5JywgJ3Zpc2libGUnKVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgdGhpcy4kZWwuX3hfaXNTaG93biA9IHRydWVcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY29uc3QgaGlkZSA9ICgpID0+IHtcbiAgICAgICAgICAgICAgICBBbHBpbmUubXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuX3hfaXNTaG93blxuICAgICAgICAgICAgICAgICAgICAgICAgPyB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgndmlzaWJpbGl0eScsICdoaWRkZW4nKVxuICAgICAgICAgICAgICAgICAgICAgICAgOiB0aGlzLiRlbC5zdHlsZS5zZXRQcm9wZXJ0eSgnZGlzcGxheScsICdub25lJylcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zdCB0b2dnbGUgPSBvbmNlKFxuICAgICAgICAgICAgICAgICh2YWx1ZSkgPT4gKHZhbHVlID8gc2hvdygpIDogaGlkZSgpKSxcbiAgICAgICAgICAgICAgICAodmFsdWUpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy4kZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyhcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuJGVsLFxuICAgICAgICAgICAgICAgICAgICAgICAgdmFsdWUsXG4gICAgICAgICAgICAgICAgICAgICAgICBzaG93LFxuICAgICAgICAgICAgICAgICAgICAgICAgaGlkZSxcbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICApXG5cbiAgICAgICAgICAgIEFscGluZS5lZmZlY3QoKCkgPT4gdG9nZ2xlKHRoaXMuaXNTaG93bikpXG4gICAgICAgIH0sXG5cbiAgICAgICAgY29uZmlndXJlQW5pbWF0aW9uczogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0IGFuaW1hdGlvblxuXG4gICAgICAgICAgICBMaXZld2lyZS5ob29rKFxuICAgICAgICAgICAgICAgICdjb21taXQnLFxuICAgICAgICAgICAgICAgICh7IGNvbXBvbmVudCwgY29tbWl0LCBzdWNjZWVkLCBmYWlsLCByZXNwb25kIH0pID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKFxuICAgICAgICAgICAgICAgICAgICAgICAgIWNvbXBvbmVudC5zbmFwc2hvdC5kYXRhXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmlzRmlsYW1lbnROb3RpZmljYXRpb25zQ29tcG9uZW50XG4gICAgICAgICAgICAgICAgICAgICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBjb25zdCBnZXRUb3AgPSAoKSA9PiB0aGlzLiRlbC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKS50b3BcbiAgICAgICAgICAgICAgICAgICAgY29uc3Qgb2xkVG9wID0gZ2V0VG9wKClcblxuICAgICAgICAgICAgICAgICAgICByZXNwb25kKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFuaW1hdGlvbiA9ICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoIXRoaXMuaXNTaG93bikge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLiRlbC5hbmltYXRlKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdHJhbnNmb3JtOiBgdHJhbnNsYXRlWSgke1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBvbGRUb3AgLSBnZXRUb3AoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1weClgLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHsgdHJhbnNmb3JtOiAndHJhbnNsYXRlWSgwcHgpJyB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkdXJhdGlvbjogdGhpcy50cmFuc2l0aW9uRHVyYXRpb24sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlYXNpbmc6IHRoaXMudHJhbnNpdGlvbkVhc2luZyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuJGVsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmdldEFuaW1hdGlvbnMoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5mb3JFYWNoKChhbmltYXRpb24pID0+IGFuaW1hdGlvbi5maW5pc2goKSlcbiAgICAgICAgICAgICAgICAgICAgfSlcblxuICAgICAgICAgICAgICAgICAgICBzdWNjZWVkKCh7IHNuYXBzaG90LCBlZmZlY3QgfSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgYW5pbWF0aW9uKClcbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgKVxuICAgICAgICB9LFxuXG4gICAgICAgIGNsb3NlOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLmlzU2hvd24gPSBmYWxzZVxuXG4gICAgICAgICAgICBzZXRUaW1lb3V0KFxuICAgICAgICAgICAgICAgICgpID0+XG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5kaXNwYXRjaEV2ZW50KFxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3IEN1c3RvbUV2ZW50KCdub3RpZmljYXRpb25DbG9zZWQnLCB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGV0YWlsOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlkOiBub3RpZmljYXRpb24uaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pLFxuICAgICAgICAgICAgICAgICAgICApLFxuICAgICAgICAgICAgICAgIHRoaXMudHJhbnNpdGlvbkR1cmF0aW9uLFxuICAgICAgICAgICAgKVxuICAgICAgICB9LFxuXG4gICAgICAgIG1hcmtBc1JlYWQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHdpbmRvdy5kaXNwYXRjaEV2ZW50KFxuICAgICAgICAgICAgICAgIG5ldyBDdXN0b21FdmVudCgnbWFya2VkTm90aWZpY2F0aW9uQXNSZWFkJywge1xuICAgICAgICAgICAgICAgICAgICBkZXRhaWw6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkOiBub3RpZmljYXRpb24uaWQsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgfSksXG4gICAgICAgICAgICApXG4gICAgICAgIH0sXG5cbiAgICAgICAgbWFya0FzVW5yZWFkOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB3aW5kb3cuZGlzcGF0Y2hFdmVudChcbiAgICAgICAgICAgICAgICBuZXcgQ3VzdG9tRXZlbnQoJ21hcmtlZE5vdGlmaWNhdGlvbkFzVW5yZWFkJywge1xuICAgICAgICAgICAgICAgICAgICBkZXRhaWw6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlkOiBub3RpZmljYXRpb24uaWQsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgfSksXG4gICAgICAgICAgICApXG4gICAgICAgIH0sXG4gICAgfSkpXG59XG4iLCAiaW1wb3J0IHsgdjQgYXMgdXVpZCB9IGZyb20gJ3V1aWQtYnJvd3NlcidcblxuY2xhc3MgTm90aWZpY2F0aW9uIHtcbiAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgdGhpcy5pZCh1dWlkKCkpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpZChpZCkge1xuICAgICAgICB0aGlzLmlkID0gaWRcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHRpdGxlKHRpdGxlKSB7XG4gICAgICAgIHRoaXMudGl0bGUgPSB0aXRsZVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgYm9keShib2R5KSB7XG4gICAgICAgIHRoaXMuYm9keSA9IGJvZHlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGFjdGlvbnMoYWN0aW9ucykge1xuICAgICAgICB0aGlzLmFjdGlvbnMgPSBhY3Rpb25zXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBzdGF0dXMoc3RhdHVzKSB7XG4gICAgICAgIHRoaXMuc3RhdHVzID0gc3RhdHVzXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBjb2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmNvbG9yID0gY29sb3JcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGljb24oaWNvbikge1xuICAgICAgICB0aGlzLmljb24gPSBpY29uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpY29uQ29sb3IoY29sb3IpIHtcbiAgICAgICAgdGhpcy5pY29uQ29sb3IgPSBjb2xvclxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZHVyYXRpb24oZHVyYXRpb24pIHtcbiAgICAgICAgdGhpcy5kdXJhdGlvbiA9IGR1cmF0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBzZWNvbmRzKHNlY29uZHMpIHtcbiAgICAgICAgdGhpcy5kdXJhdGlvbihzZWNvbmRzICogMTAwMClcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHBlcnNpc3RlbnQoKSB7XG4gICAgICAgIHRoaXMuZHVyYXRpb24oJ3BlcnNpc3RlbnQnKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZGFuZ2VyKCkge1xuICAgICAgICB0aGlzLnN0YXR1cygnZGFuZ2VyJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGluZm8oKSB7XG4gICAgICAgIHRoaXMuc3RhdHVzKCdpbmZvJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHN1Y2Nlc3MoKSB7XG4gICAgICAgIHRoaXMuc3RhdHVzKCdzdWNjZXNzJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHdhcm5pbmcoKSB7XG4gICAgICAgIHRoaXMuc3RhdHVzKCd3YXJuaW5nJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHZpZXcodmlldykge1xuICAgICAgICB0aGlzLnZpZXcgPSB2aWV3XG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICB2aWV3RGF0YSh2aWV3RGF0YSkge1xuICAgICAgICB0aGlzLnZpZXdEYXRhID0gdmlld0RhdGFcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIHNlbmQoKSB7XG4gICAgICAgIHdpbmRvdy5kaXNwYXRjaEV2ZW50KFxuICAgICAgICAgICAgbmV3IEN1c3RvbUV2ZW50KCdub3RpZmljYXRpb25TZW50Jywge1xuICAgICAgICAgICAgICAgIGRldGFpbDoge1xuICAgICAgICAgICAgICAgICAgICBub3RpZmljYXRpb246IHRoaXMsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIH0pLFxuICAgICAgICApXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG59XG5cbmNsYXNzIEFjdGlvbiB7XG4gICAgY29uc3RydWN0b3IobmFtZSkge1xuICAgICAgICB0aGlzLm5hbWUobmFtZSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIG5hbWUobmFtZSkge1xuICAgICAgICB0aGlzLm5hbWUgPSBuYW1lXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBjb2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmNvbG9yID0gY29sb3JcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGRpc3BhdGNoKGV2ZW50LCBkYXRhKSB7XG4gICAgICAgIHRoaXMuZXZlbnQoZXZlbnQpXG4gICAgICAgIHRoaXMuZXZlbnREYXRhKGRhdGEpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkaXNwYXRjaFNlbGYoZXZlbnQsIGRhdGEpIHtcbiAgICAgICAgdGhpcy5kaXNwYXRjaChldmVudCwgZGF0YSlcbiAgICAgICAgdGhpcy5kaXNwYXRjaERpcmVjdGlvbiA9ICdzZWxmJ1xuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZGlzcGF0Y2hUbyhjb21wb25lbnQsIGV2ZW50LCBkYXRhKSB7XG4gICAgICAgIHRoaXMuZGlzcGF0Y2goZXZlbnQsIGRhdGEpXG4gICAgICAgIHRoaXMuZGlzcGF0Y2hEaXJlY3Rpb24gPSAndG8nXG4gICAgICAgIHRoaXMuZGlzcGF0Y2hUb0NvbXBvbmVudCA9IGNvbXBvbmVudFxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQGRlcHJlY2F0ZWQgVXNlIGBkaXNwYXRjaCgpYCBpbnN0ZWFkLlxuICAgICAqL1xuICAgIGVtaXQoZXZlbnQsIGRhdGEpIHtcbiAgICAgICAgdGhpcy5kaXNwYXRjaChldmVudCwgZGF0YSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEBkZXByZWNhdGVkIFVzZSBgZGlzcGF0Y2hTZWxmKClgIGluc3RlYWQuXG4gICAgICovXG4gICAgZW1pdFNlbGYoZXZlbnQsIGRhdGEpIHtcbiAgICAgICAgdGhpcy5kaXNwYXRjaFNlbGYoZXZlbnQsIGRhdGEpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBAZGVwcmVjYXRlZCBVc2UgYGRpc3BhdGNoVG8oKWAgaW5zdGVhZC5cbiAgICAgKi9cbiAgICBlbWl0VG8oY29tcG9uZW50LCBldmVudCwgZGF0YSkge1xuICAgICAgICB0aGlzLmRpc3BhdGNoVG8oY29tcG9uZW50LCBldmVudCwgZGF0YSlcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGRpc3BhdGNoRGlyZWN0aW9uKGRpc3BhdGNoRGlyZWN0aW9uKSB7XG4gICAgICAgIHRoaXMuZGlzcGF0Y2hEaXJlY3Rpb24gPSBkaXNwYXRjaERpcmVjdGlvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZGlzcGF0Y2hUb0NvbXBvbmVudChjb21wb25lbnQpIHtcbiAgICAgICAgdGhpcy5kaXNwYXRjaFRvQ29tcG9uZW50ID0gY29tcG9uZW50XG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBldmVudChldmVudCkge1xuICAgICAgICB0aGlzLmV2ZW50ID0gZXZlbnRcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGV2ZW50RGF0YShkYXRhKSB7XG4gICAgICAgIHRoaXMuZXZlbnREYXRhID0gZGF0YVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZXh0cmFBdHRyaWJ1dGVzKGF0dHJpYnV0ZXMpIHtcbiAgICAgICAgdGhpcy5leHRyYUF0dHJpYnV0ZXMgPSBhdHRyaWJ1dGVzXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpY29uKGljb24pIHtcbiAgICAgICAgdGhpcy5pY29uID0gaWNvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgaWNvblBvc2l0aW9uKHBvc2l0aW9uKSB7XG4gICAgICAgIHRoaXMuaWNvblBvc2l0aW9uID0gcG9zaXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIG91dGxpbmVkKGNvbmRpdGlvbiA9IHRydWUpIHtcbiAgICAgICAgdGhpcy5pc091dGxpbmVkID0gY29uZGl0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBkaXNhYmxlZChjb25kaXRpb24gPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuaXNEaXNhYmxlZCA9IGNvbmRpdGlvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgbGFiZWwobGFiZWwpIHtcbiAgICAgICAgdGhpcy5sYWJlbCA9IGxhYmVsXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBjbG9zZShjb25kaXRpb24gPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2hvdWxkQ2xvc2UgPSBjb25kaXRpb25cblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIG9wZW5VcmxJbk5ld1RhYihjb25kaXRpb24gPSB0cnVlKSB7XG4gICAgICAgIHRoaXMuc2hvdWxkT3BlblVybEluTmV3VGFiID0gY29uZGl0aW9uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBzaXplKHNpemUpIHtcbiAgICAgICAgdGhpcy5zaXplID0gc2l6ZVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgdXJsKHVybCkge1xuICAgICAgICB0aGlzLnVybCA9IHVybFxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgdmlldyh2aWV3KSB7XG4gICAgICAgIHRoaXMudmlldyA9IHZpZXdcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGJ1dHRvbigpIHtcbiAgICAgICAgdGhpcy52aWV3KCdmaWxhbWVudC1ub3RpZmljYXRpb25zOjphY3Rpb25zLmJ1dHRvbi1hY3Rpb24nKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgZ3JvdXBlZCgpIHtcbiAgICAgICAgdGhpcy52aWV3KCdmaWxhbWVudC1ub3RpZmljYXRpb25zOjphY3Rpb25zLmdyb3VwZWQtYWN0aW9uJylcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGxpbmsoKSB7XG4gICAgICAgIHRoaXMudmlldygnZmlsYW1lbnQtbm90aWZpY2F0aW9uczo6YWN0aW9ucy5saW5rLWFjdGlvbicpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG59XG5cbmNsYXNzIEFjdGlvbkdyb3VwIHtcbiAgICBjb25zdHJ1Y3RvcihhY3Rpb25zKSB7XG4gICAgICAgIHRoaXMuYWN0aW9ucyhhY3Rpb25zKVxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgYWN0aW9ucyhhY3Rpb25zKSB7XG4gICAgICAgIHRoaXMuYWN0aW9ucyA9IGFjdGlvbnMubWFwKChhY3Rpb24pID0+IGFjdGlvbi5ncm91cGVkKCkpXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBjb2xvcihjb2xvcikge1xuICAgICAgICB0aGlzLmNvbG9yID0gY29sb3JcblxuICAgICAgICByZXR1cm4gdGhpc1xuICAgIH1cblxuICAgIGljb24oaWNvbikge1xuICAgICAgICB0aGlzLmljb24gPSBpY29uXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICBpY29uUG9zaXRpb24ocG9zaXRpb24pIHtcbiAgICAgICAgdGhpcy5pY29uUG9zaXRpb24gPSBwb3NpdGlvblxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxuXG4gICAgbGFiZWwobGFiZWwpIHtcbiAgICAgICAgdGhpcy5sYWJlbCA9IGxhYmVsXG5cbiAgICAgICAgcmV0dXJuIHRoaXNcbiAgICB9XG5cbiAgICB0b29sdGlwKHRvb2x0aXApIHtcbiAgICAgICAgdGhpcy50b29sdGlwID0gdG9vbHRpcFxuXG4gICAgICAgIHJldHVybiB0aGlzXG4gICAgfVxufVxuXG5leHBvcnQgeyBBY3Rpb24sIEFjdGlvbkdyb3VwLCBOb3RpZmljYXRpb24gfVxuIiwgImltcG9ydCBOb3RpZmljYXRpb25Db21wb25lbnRBbHBpbmVQbHVnaW4gZnJvbSAnLi9jb21wb25lbnRzL25vdGlmaWNhdGlvbidcbmltcG9ydCB7XG4gICAgQWN0aW9uIGFzIE5vdGlmaWNhdGlvbkFjdGlvbixcbiAgICBBY3Rpb25Hcm91cCBhcyBOb3RpZmljYXRpb25BY3Rpb25Hcm91cCxcbiAgICBOb3RpZmljYXRpb24sXG59IGZyb20gJy4vTm90aWZpY2F0aW9uJ1xuXG53aW5kb3cuRmlsYW1lbnROb3RpZmljYXRpb25BY3Rpb24gPSBOb3RpZmljYXRpb25BY3Rpb25cbndpbmRvdy5GaWxhbWVudE5vdGlmaWNhdGlvbkFjdGlvbkdyb3VwID0gTm90aWZpY2F0aW9uQWN0aW9uR3JvdXBcbndpbmRvdy5GaWxhbWVudE5vdGlmaWNhdGlvbiA9IE5vdGlmaWNhdGlvblxuXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdhbHBpbmU6aW5pdCcsICgpID0+IHtcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihOb3RpZmljYXRpb25Db21wb25lbnRBbHBpbmVQbHVnaW4pXG59KVxuIl0sCiAgIm1hcHBpbmdzIjogIjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQUE7QUFJQSxVQUFJO0FBRUosVUFBSSxTQUFTLE9BQU8sV0FBVyxnQkFBZ0IsT0FBTyxVQUFVLE9BQU87QUFDdkUsVUFBSSxVQUFVLE9BQU8saUJBQWlCO0FBRWhDLGdCQUFRLElBQUksV0FBVyxFQUFFO0FBQzdCLGNBQU0sU0FBUyxZQUFZO0FBQ3pCLGlCQUFPLGdCQUFnQixLQUFLO0FBQzVCLGlCQUFPO0FBQUEsUUFDVDtBQUFBLE1BQ0Y7QUFMTTtBQU9OLFVBQUksQ0FBQyxLQUFLO0FBS0osZUFBTyxJQUFJLE1BQU0sRUFBRTtBQUN2QixjQUFNLFdBQVc7QUFDZixtQkFBUyxJQUFJLEdBQUcsR0FBRyxJQUFJLElBQUksS0FBSztBQUM5QixpQkFBSyxJQUFJLE9BQVU7QUFBRyxrQkFBSSxLQUFLLE9BQU8sSUFBSTtBQUMxQyxpQkFBSyxDQUFDLElBQUksUUFBUSxJQUFJLE1BQVMsS0FBSztBQUFBLFVBQ3RDO0FBRUEsaUJBQU87QUFBQSxRQUNUO0FBQUEsTUFDRjtBQVRNO0FBV04sYUFBTyxVQUFVO0FBQUE7QUFBQTs7O0FDaENqQjtBQUFBO0FBSUEsVUFBSSxZQUFZLENBQUM7QUFDakIsV0FB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
