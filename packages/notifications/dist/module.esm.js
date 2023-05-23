var __create = Object.create;
var __defProp = Object.defineProperty;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __markAsModule = (target) => __defProp(target, "__esModule", {value: true});
var __commonJS = (callback, module) => () => {
  if (!module) {
    module = {exports: {}};
    callback(module.exports, module);
  }
  return module.exports;
};
var __exportStar = (target, module, desc) => {
  if (module && typeof module === "object" || typeof module === "function") {
    for (let key of __getOwnPropNames(module))
      if (!__hasOwnProp.call(target, key) && key !== "default")
        __defProp(target, key, {get: () => module[key], enumerable: !(desc = __getOwnPropDesc(module, key)) || desc.enumerable});
  }
  return target;
};
var __toModule = (module) => {
  return __exportStar(__markAsModule(__defProp(module != null ? __create(__getProtoOf(module)) : {}, "default", module && module.__esModule && "default" in module ? {get: () => module.default, enumerable: true} : {value: module, enumerable: true})), module);
};

// node_modules/uuid-browser/lib/rng-browser.js
var require_rng_browser = __commonJS((exports, module) => {
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
});

// node_modules/uuid-browser/lib/bytesToUuid.js
var require_bytesToUuid = __commonJS((exports, module) => {
  var byteToHex = [];
  for (var i = 0; i < 256; ++i) {
    byteToHex[i] = (i + 256).toString(16).substr(1);
  }
  function bytesToUuid(buf, offset) {
    var i2 = offset || 0;
    var bth = byteToHex;
    return bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + "-" + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]] + bth[buf[i2++]];
  }
  module.exports = bytesToUuid;
});

// node_modules/uuid-browser/v1.js
var require_v1 = __commonJS((exports, module) => {
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
    var msecs = options.msecs !== void 0 ? options.msecs : new Date().getTime();
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
});

// node_modules/uuid-browser/v4.js
var require_v4 = __commonJS((exports, module) => {
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
});

// node_modules/uuid-browser/index.js
var require_uuid_browser = __commonJS((exports, module) => {
  var v1 = require_v1();
  var v4 = require_v4();
  var uuid2 = v4;
  uuid2.v1 = v1;
  uuid2.v4 = v4;
  module.exports = uuid2;
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
  observer.observe(document, {subtree: true, childList: true, attributes: true, attributeOldValue: true});
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
  let addedAttributes = new Map();
  let removedAttributes = new Map();
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
        addedAttributes.get(el).push({name, value: el.getAttribute(name)});
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
  Alpine.data("notificationComponent", ({notification}) => ({
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
      const toggle = once((value) => value ? show() : hide(), (value) => {
        this.$el._x_toggleAndCascadeWithTransitions(this.$el, value, show, hide);
      });
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
          this.$el.animate([
            {transform: `translateY(${oldTop - getTop()}px)`},
            {transform: "translateY(0px)"}
          ], {
            duration: this.getTransitionDuration(),
            easing: this.computedStyle.transitionTimingFunction
          });
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
      setTimeout(() => Livewire.emit("notificationClosed", notification.id), this.getTransitionDuration());
    },
    getTransitionDuration: function() {
      return parseFloat(this.computedStyle.transitionDuration) * 1e3;
    }
  }));
};

// packages/notifications/resources/js/Notification.js
var import_uuid_browser = __toModule(require_uuid_browser());
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
      case "success":
        this.success();
        break;
      case "warning":
        this.warning();
        break;
      case "danger":
        this.danger();
        break;
    }
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
  danger() {
    this.icon("heroicon-o-x-circle");
    this.iconColor("danger");
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
    this.shouldCloseNotification = condition;
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
    this.view("notifications::actions.button-action");
    return this;
  }
  grouped() {
    this.view("notifications::actions.grouped-action");
    return this;
  }
  link() {
    this.view("notifications::actions.link-action");
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
var js_default = (Alpine) => {
  Alpine.plugin(notification_default);
};
export {
  Notification,
  Action as NotificationAction,
  ActionGroup as NotificationActionGroup,
  notification_default as NotificationComponentAlpinePlugin,
  js_default as default
};
