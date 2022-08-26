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
      if (notification.duration !== null) {
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
        if (component.fingerprint.name !== "notifications") {
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
        if (component.fingerprint.name !== "notifications") {
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

// packages/notifications/resources/js/index.js
var js_default = (Alpine) => {
  Alpine.plugin(notification_default);
};
export {
  notification_default as NotificationComponentAlpinePlugin,
  js_default as default
};
