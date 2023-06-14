(() => {
  // node_modules/alpinejs/dist/module.esm.js
  var flushPending = false;
  var flushing = false;
  var queue = [];
  var lastFlushedIndex = -1;
  function scheduler(callback) {
    queueJob(callback);
  }
  function queueJob(job) {
    if (!queue.includes(job))
      queue.push(job);
    queueFlush();
  }
  function dequeueJob(job) {
    let index = queue.indexOf(job);
    if (index !== -1 && index > lastFlushedIndex)
      queue.splice(index, 1);
  }
  function queueFlush() {
    if (!flushing && !flushPending) {
      flushPending = true;
      queueMicrotask(flushJobs);
    }
  }
  function flushJobs() {
    flushPending = false;
    flushing = true;
    for (let i = 0; i < queue.length; i++) {
      queue[i]();
      lastFlushedIndex = i;
    }
    queue.length = 0;
    lastFlushedIndex = -1;
    flushing = false;
  }
  var reactive;
  var effect;
  var release;
  var raw;
  var shouldSchedule = true;
  function disableEffectScheduling(callback) {
    shouldSchedule = false;
    callback();
    shouldSchedule = true;
  }
  function setReactivityEngine(engine) {
    reactive = engine.reactive;
    release = engine.release;
    effect = (callback) => engine.effect(callback, { scheduler: (task) => {
      if (shouldSchedule) {
        scheduler(task);
      } else {
        task();
      }
    } });
    raw = engine.raw;
  }
  function overrideEffect(override) {
    effect = override;
  }
  function elementBoundEffect(el) {
    let cleanup2 = () => {
    };
    let wrappedEffect = (callback) => {
      let effectReference = effect(callback);
      if (!el._x_effects) {
        el._x_effects = /* @__PURE__ */ new Set();
        el._x_runEffects = () => {
          el._x_effects.forEach((i) => i());
        };
      }
      el._x_effects.add(effectReference);
      cleanup2 = () => {
        if (effectReference === void 0)
          return;
        el._x_effects.delete(effectReference);
        release(effectReference);
      };
      return effectReference;
    };
    return [wrappedEffect, () => {
      cleanup2();
    }];
  }
  var onAttributeAddeds = [];
  var onElRemoveds = [];
  var onElAddeds = [];
  function onElAdded(callback) {
    onElAddeds.push(callback);
  }
  function onElRemoved(el, callback) {
    if (typeof callback === "function") {
      if (!el._x_cleanups)
        el._x_cleanups = [];
      el._x_cleanups.push(callback);
    } else {
      callback = el;
      onElRemoveds.push(callback);
    }
  }
  function onAttributesAdded(callback) {
    onAttributeAddeds.push(callback);
  }
  function onAttributeRemoved(el, name, callback) {
    if (!el._x_attributeCleanups)
      el._x_attributeCleanups = {};
    if (!el._x_attributeCleanups[name])
      el._x_attributeCleanups[name] = [];
    el._x_attributeCleanups[name].push(callback);
  }
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
  function deferMutations() {
    isCollecting = true;
  }
  function flushAndStopDeferringMutations() {
    isCollecting = false;
    onMutate(deferredMutations);
    deferredMutations = [];
  }
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
        let add2 = () => {
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
          add2();
        } else if (el.hasAttribute(name)) {
          remove();
          add2();
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
  function scope(node) {
    return mergeProxies(closestDataStack(node));
  }
  function addScopeToNode(node, data2, referenceNode) {
    node._x_dataStack = [data2, ...closestDataStack(referenceNode || node)];
    return () => {
      node._x_dataStack = node._x_dataStack.filter((i) => i !== data2);
    };
  }
  function closestDataStack(node) {
    if (node._x_dataStack)
      return node._x_dataStack;
    if (typeof ShadowRoot === "function" && node instanceof ShadowRoot) {
      return closestDataStack(node.host);
    }
    if (!node.parentNode) {
      return [];
    }
    return closestDataStack(node.parentNode);
  }
  function mergeProxies(objects) {
    let thisProxy = new Proxy({}, {
      ownKeys: () => {
        return Array.from(new Set(objects.flatMap((i) => Object.keys(i))));
      },
      has: (target, name) => {
        return objects.some((obj) => obj.hasOwnProperty(name));
      },
      get: (target, name) => {
        return (objects.find((obj) => {
          if (obj.hasOwnProperty(name)) {
            let descriptor = Object.getOwnPropertyDescriptor(obj, name);
            if (descriptor.get && descriptor.get._x_alreadyBound || descriptor.set && descriptor.set._x_alreadyBound) {
              return true;
            }
            if ((descriptor.get || descriptor.set) && descriptor.enumerable) {
              let getter = descriptor.get;
              let setter = descriptor.set;
              let property = descriptor;
              getter = getter && getter.bind(thisProxy);
              setter = setter && setter.bind(thisProxy);
              if (getter)
                getter._x_alreadyBound = true;
              if (setter)
                setter._x_alreadyBound = true;
              Object.defineProperty(obj, name, {
                ...property,
                get: getter,
                set: setter
              });
            }
            return true;
          }
          return false;
        }) || {})[name];
      },
      set: (target, name, value) => {
        let closestObjectWithKey = objects.find((obj) => obj.hasOwnProperty(name));
        if (closestObjectWithKey) {
          closestObjectWithKey[name] = value;
        } else {
          objects[objects.length - 1][name] = value;
        }
        return true;
      }
    });
    return thisProxy;
  }
  function initInterceptors(data2) {
    let isObject2 = (val) => typeof val === "object" && !Array.isArray(val) && val !== null;
    let recurse = (obj, basePath = "") => {
      Object.entries(Object.getOwnPropertyDescriptors(obj)).forEach(([key, { value, enumerable }]) => {
        if (enumerable === false || value === void 0)
          return;
        let path = basePath === "" ? key : `${basePath}.${key}`;
        if (typeof value === "object" && value !== null && value._x_interceptor) {
          obj[key] = value.initialize(data2, path, key);
        } else {
          if (isObject2(value) && value !== obj && !(value instanceof Element)) {
            recurse(value, path);
          }
        }
      });
    };
    return recurse(data2);
  }
  function interceptor(callback, mutateObj = () => {
  }) {
    let obj = {
      initialValue: void 0,
      _x_interceptor: true,
      initialize(data2, path, key) {
        return callback(this.initialValue, () => get(data2, path), (value) => set(data2, path, value), path, key);
      }
    };
    mutateObj(obj);
    return (initialValue) => {
      if (typeof initialValue === "object" && initialValue !== null && initialValue._x_interceptor) {
        let initialize = obj.initialize.bind(obj);
        obj.initialize = (data2, path, key) => {
          let innerValue = initialValue.initialize(data2, path, key);
          obj.initialValue = innerValue;
          return initialize(data2, path, key);
        };
      } else {
        obj.initialValue = initialValue;
      }
      return obj;
    };
  }
  function get(obj, path) {
    return path.split(".").reduce((carry, segment) => carry[segment], obj);
  }
  function set(obj, path, value) {
    if (typeof path === "string")
      path = path.split(".");
    if (path.length === 1)
      obj[path[0]] = value;
    else if (path.length === 0)
      throw error;
    else {
      if (obj[path[0]])
        return set(obj[path[0]], path.slice(1), value);
      else {
        obj[path[0]] = {};
        return set(obj[path[0]], path.slice(1), value);
      }
    }
  }
  var magics = {};
  function magic(name, callback) {
    magics[name] = callback;
  }
  function injectMagics(obj, el) {
    Object.entries(magics).forEach(([name, callback]) => {
      let memoizedUtilities = null;
      function getUtilities() {
        if (memoizedUtilities) {
          return memoizedUtilities;
        } else {
          let [utilities, cleanup2] = getElementBoundUtilities(el);
          memoizedUtilities = { interceptor, ...utilities };
          onElRemoved(el, cleanup2);
          return memoizedUtilities;
        }
      }
      Object.defineProperty(obj, `$${name}`, {
        get() {
          return callback(el, getUtilities());
        },
        enumerable: false
      });
    });
    return obj;
  }
  function tryCatch(el, expression, callback, ...args) {
    try {
      return callback(...args);
    } catch (e) {
      handleError(e, el, expression);
    }
  }
  function handleError(error2, el, expression = void 0) {
    Object.assign(error2, { el, expression });
    console.warn(`Alpine Expression Error: ${error2.message}

${expression ? 'Expression: "' + expression + '"\n\n' : ""}`, el);
    setTimeout(() => {
      throw error2;
    }, 0);
  }
  var shouldAutoEvaluateFunctions = true;
  function dontAutoEvaluateFunctions(callback) {
    let cache = shouldAutoEvaluateFunctions;
    shouldAutoEvaluateFunctions = false;
    callback();
    shouldAutoEvaluateFunctions = cache;
  }
  function evaluate(el, expression, extras = {}) {
    let result;
    evaluateLater(el, expression)((value) => result = value, extras);
    return result;
  }
  function evaluateLater(...args) {
    return theEvaluatorFunction(...args);
  }
  var theEvaluatorFunction = normalEvaluator;
  function setEvaluator(newEvaluator) {
    theEvaluatorFunction = newEvaluator;
  }
  function normalEvaluator(el, expression) {
    let overriddenMagics = {};
    injectMagics(overriddenMagics, el);
    let dataStack = [overriddenMagics, ...closestDataStack(el)];
    let evaluator = typeof expression === "function" ? generateEvaluatorFromFunction(dataStack, expression) : generateEvaluatorFromString(dataStack, expression, el);
    return tryCatch.bind(null, el, expression, evaluator);
  }
  function generateEvaluatorFromFunction(dataStack, func) {
    return (receiver = () => {
    }, { scope: scope2 = {}, params = [] } = {}) => {
      let result = func.apply(mergeProxies([scope2, ...dataStack]), params);
      runIfTypeOfFunction(receiver, result);
    };
  }
  var evaluatorMemo = {};
  function generateFunctionFromString(expression, el) {
    if (evaluatorMemo[expression]) {
      return evaluatorMemo[expression];
    }
    let AsyncFunction = Object.getPrototypeOf(async function() {
    }).constructor;
    let rightSideSafeExpression = /^[\n\s]*if.*\(.*\)/.test(expression) || /^(let|const)\s/.test(expression) ? `(async()=>{ ${expression} })()` : expression;
    const safeAsyncFunction = () => {
      try {
        return new AsyncFunction(["__self", "scope"], `with (scope) { __self.result = ${rightSideSafeExpression} }; __self.finished = true; return __self.result;`);
      } catch (error2) {
        handleError(error2, el, expression);
        return Promise.resolve();
      }
    };
    let func = safeAsyncFunction();
    evaluatorMemo[expression] = func;
    return func;
  }
  function generateEvaluatorFromString(dataStack, expression, el) {
    let func = generateFunctionFromString(expression, el);
    return (receiver = () => {
    }, { scope: scope2 = {}, params = [] } = {}) => {
      func.result = void 0;
      func.finished = false;
      let completeScope = mergeProxies([scope2, ...dataStack]);
      if (typeof func === "function") {
        let promise = func(func, completeScope).catch((error2) => handleError(error2, el, expression));
        if (func.finished) {
          runIfTypeOfFunction(receiver, func.result, completeScope, params, el);
          func.result = void 0;
        } else {
          promise.then((result) => {
            runIfTypeOfFunction(receiver, result, completeScope, params, el);
          }).catch((error2) => handleError(error2, el, expression)).finally(() => func.result = void 0);
        }
      }
    };
  }
  function runIfTypeOfFunction(receiver, value, scope2, params, el) {
    if (shouldAutoEvaluateFunctions && typeof value === "function") {
      let result = value.apply(scope2, params);
      if (result instanceof Promise) {
        result.then((i) => runIfTypeOfFunction(receiver, i, scope2, params)).catch((error2) => handleError(error2, el, value));
      } else {
        receiver(result);
      }
    } else if (typeof value === "object" && value instanceof Promise) {
      value.then((i) => receiver(i));
    } else {
      receiver(value);
    }
  }
  var prefixAsString = "x-";
  function prefix(subject = "") {
    return prefixAsString + subject;
  }
  function setPrefix(newPrefix) {
    prefixAsString = newPrefix;
  }
  var directiveHandlers = {};
  function directive(name, callback) {
    directiveHandlers[name] = callback;
    return {
      before(directive2) {
        if (!directiveHandlers[directive2]) {
          console.warn("Cannot find directive `${directive}`. `${name}` will use the default order of execution");
          return;
        }
        const pos = directiveOrder.indexOf(directive2);
        directiveOrder.splice(pos >= 0 ? pos : directiveOrder.indexOf("DEFAULT"), 0, name);
      }
    };
  }
  function directives(el, attributes, originalAttributeOverride) {
    attributes = Array.from(attributes);
    if (el._x_virtualDirectives) {
      let vAttributes = Object.entries(el._x_virtualDirectives).map(([name, value]) => ({ name, value }));
      let staticAttributes = attributesOnly(vAttributes);
      vAttributes = vAttributes.map((attribute) => {
        if (staticAttributes.find((attr) => attr.name === attribute.name)) {
          return {
            name: `x-bind:${attribute.name}`,
            value: `"${attribute.value}"`
          };
        }
        return attribute;
      });
      attributes = attributes.concat(vAttributes);
    }
    let transformedAttributeMap = {};
    let directives2 = attributes.map(toTransformedAttributes((newName, oldName) => transformedAttributeMap[newName] = oldName)).filter(outNonAlpineAttributes).map(toParsedDirectives(transformedAttributeMap, originalAttributeOverride)).sort(byPriority);
    return directives2.map((directive2) => {
      return getDirectiveHandler(el, directive2);
    });
  }
  function attributesOnly(attributes) {
    return Array.from(attributes).map(toTransformedAttributes()).filter((attr) => !outNonAlpineAttributes(attr));
  }
  var isDeferringHandlers = false;
  var directiveHandlerStacks = /* @__PURE__ */ new Map();
  var currentHandlerStackKey = Symbol();
  function deferHandlingDirectives(callback) {
    isDeferringHandlers = true;
    let key = Symbol();
    currentHandlerStackKey = key;
    directiveHandlerStacks.set(key, []);
    let flushHandlers = () => {
      while (directiveHandlerStacks.get(key).length)
        directiveHandlerStacks.get(key).shift()();
      directiveHandlerStacks.delete(key);
    };
    let stopDeferring = () => {
      isDeferringHandlers = false;
      flushHandlers();
    };
    callback(flushHandlers);
    stopDeferring();
  }
  function getElementBoundUtilities(el) {
    let cleanups = [];
    let cleanup2 = (callback) => cleanups.push(callback);
    let [effect3, cleanupEffect] = elementBoundEffect(el);
    cleanups.push(cleanupEffect);
    let utilities = {
      Alpine: alpine_default,
      effect: effect3,
      cleanup: cleanup2,
      evaluateLater: evaluateLater.bind(evaluateLater, el),
      evaluate: evaluate.bind(evaluate, el)
    };
    let doCleanup = () => cleanups.forEach((i) => i());
    return [utilities, doCleanup];
  }
  function getDirectiveHandler(el, directive2) {
    let noop = () => {
    };
    let handler3 = directiveHandlers[directive2.type] || noop;
    let [utilities, cleanup2] = getElementBoundUtilities(el);
    onAttributeRemoved(el, directive2.original, cleanup2);
    let fullHandler = () => {
      if (el._x_ignore || el._x_ignoreSelf)
        return;
      handler3.inline && handler3.inline(el, directive2, utilities);
      handler3 = handler3.bind(handler3, el, directive2, utilities);
      isDeferringHandlers ? directiveHandlerStacks.get(currentHandlerStackKey).push(handler3) : handler3();
    };
    fullHandler.runCleanups = cleanup2;
    return fullHandler;
  }
  var startingWith = (subject, replacement) => ({ name, value }) => {
    if (name.startsWith(subject))
      name = name.replace(subject, replacement);
    return { name, value };
  };
  var into = (i) => i;
  function toTransformedAttributes(callback = () => {
  }) {
    return ({ name, value }) => {
      let { name: newName, value: newValue } = attributeTransformers.reduce((carry, transform) => {
        return transform(carry);
      }, { name, value });
      if (newName !== name)
        callback(newName, name);
      return { name: newName, value: newValue };
    };
  }
  var attributeTransformers = [];
  function mapAttributes(callback) {
    attributeTransformers.push(callback);
  }
  function outNonAlpineAttributes({ name }) {
    return alpineAttributeRegex().test(name);
  }
  var alpineAttributeRegex = () => new RegExp(`^${prefixAsString}([^:^.]+)\\b`);
  function toParsedDirectives(transformedAttributeMap, originalAttributeOverride) {
    return ({ name, value }) => {
      let typeMatch = name.match(alpineAttributeRegex());
      let valueMatch = name.match(/:([a-zA-Z0-9\-:]+)/);
      let modifiers = name.match(/\.[^.\]]+(?=[^\]]*$)/g) || [];
      let original = originalAttributeOverride || transformedAttributeMap[name] || name;
      return {
        type: typeMatch ? typeMatch[1] : null,
        value: valueMatch ? valueMatch[1] : null,
        modifiers: modifiers.map((i) => i.replace(".", "")),
        expression: value,
        original
      };
    };
  }
  var DEFAULT = "DEFAULT";
  var directiveOrder = [
    "ignore",
    "ref",
    "data",
    "id",
    "bind",
    "init",
    "for",
    "model",
    "modelable",
    "transition",
    "show",
    "if",
    DEFAULT,
    "teleport"
  ];
  function byPriority(a, b) {
    let typeA = directiveOrder.indexOf(a.type) === -1 ? DEFAULT : a.type;
    let typeB = directiveOrder.indexOf(b.type) === -1 ? DEFAULT : b.type;
    return directiveOrder.indexOf(typeA) - directiveOrder.indexOf(typeB);
  }
  function dispatch(el, name, detail = {}) {
    el.dispatchEvent(new CustomEvent(name, {
      detail,
      bubbles: true,
      composed: true,
      cancelable: true
    }));
  }
  function walk(el, callback) {
    if (typeof ShadowRoot === "function" && el instanceof ShadowRoot) {
      Array.from(el.children).forEach((el2) => walk(el2, callback));
      return;
    }
    let skip = false;
    callback(el, () => skip = true);
    if (skip)
      return;
    let node = el.firstElementChild;
    while (node) {
      walk(node, callback, false);
      node = node.nextElementSibling;
    }
  }
  function warn(message, ...args) {
    console.warn(`Alpine Warning: ${message}`, ...args);
  }
  var started = false;
  function start() {
    if (started)
      warn("Alpine has already been initialized on this page. Calling Alpine.start() more than once can cause problems.");
    started = true;
    if (!document.body)
      warn("Unable to initialize. Trying to load Alpine before `<body>` is available. Did you forget to add `defer` in Alpine's `<script>` tag?");
    dispatch(document, "alpine:init");
    dispatch(document, "alpine:initializing");
    startObservingMutations();
    onElAdded((el) => initTree(el, walk));
    onElRemoved((el) => destroyTree(el));
    onAttributesAdded((el, attrs) => {
      directives(el, attrs).forEach((handle) => handle());
    });
    let outNestedComponents = (el) => !closestRoot(el.parentElement, true);
    Array.from(document.querySelectorAll(allSelectors())).filter(outNestedComponents).forEach((el) => {
      initTree(el);
    });
    dispatch(document, "alpine:initialized");
  }
  var rootSelectorCallbacks = [];
  var initSelectorCallbacks = [];
  function rootSelectors() {
    return rootSelectorCallbacks.map((fn) => fn());
  }
  function allSelectors() {
    return rootSelectorCallbacks.concat(initSelectorCallbacks).map((fn) => fn());
  }
  function addRootSelector(selectorCallback) {
    rootSelectorCallbacks.push(selectorCallback);
  }
  function addInitSelector(selectorCallback) {
    initSelectorCallbacks.push(selectorCallback);
  }
  function closestRoot(el, includeInitSelectors = false) {
    return findClosest(el, (element) => {
      const selectors = includeInitSelectors ? allSelectors() : rootSelectors();
      if (selectors.some((selector) => element.matches(selector)))
        return true;
    });
  }
  function findClosest(el, callback) {
    if (!el)
      return;
    if (callback(el))
      return el;
    if (el._x_teleportBack)
      el = el._x_teleportBack;
    if (!el.parentElement)
      return;
    return findClosest(el.parentElement, callback);
  }
  function isRoot(el) {
    return rootSelectors().some((selector) => el.matches(selector));
  }
  var initInterceptors2 = [];
  function interceptInit(callback) {
    initInterceptors2.push(callback);
  }
  function initTree(el, walker = walk, intercept = () => {
  }) {
    deferHandlingDirectives(() => {
      walker(el, (el2, skip) => {
        intercept(el2, skip);
        initInterceptors2.forEach((i) => i(el2, skip));
        directives(el2, el2.attributes).forEach((handle) => handle());
        el2._x_ignore && skip();
      });
    });
  }
  function destroyTree(root) {
    walk(root, (el) => cleanupAttributes(el));
  }
  var tickStack = [];
  var isHolding = false;
  function nextTick(callback = () => {
  }) {
    queueMicrotask(() => {
      isHolding || setTimeout(() => {
        releaseNextTicks();
      });
    });
    return new Promise((res) => {
      tickStack.push(() => {
        callback();
        res();
      });
    });
  }
  function releaseNextTicks() {
    isHolding = false;
    while (tickStack.length)
      tickStack.shift()();
  }
  function holdNextTicks() {
    isHolding = true;
  }
  function setClasses(el, value) {
    if (Array.isArray(value)) {
      return setClassesFromString(el, value.join(" "));
    } else if (typeof value === "object" && value !== null) {
      return setClassesFromObject(el, value);
    } else if (typeof value === "function") {
      return setClasses(el, value());
    }
    return setClassesFromString(el, value);
  }
  function setClassesFromString(el, classString) {
    let split = (classString2) => classString2.split(" ").filter(Boolean);
    let missingClasses = (classString2) => classString2.split(" ").filter((i) => !el.classList.contains(i)).filter(Boolean);
    let addClassesAndReturnUndo = (classes) => {
      el.classList.add(...classes);
      return () => {
        el.classList.remove(...classes);
      };
    };
    classString = classString === true ? classString = "" : classString || "";
    return addClassesAndReturnUndo(missingClasses(classString));
  }
  function setClassesFromObject(el, classObject) {
    let split = (classString) => classString.split(" ").filter(Boolean);
    let forAdd = Object.entries(classObject).flatMap(([classString, bool]) => bool ? split(classString) : false).filter(Boolean);
    let forRemove = Object.entries(classObject).flatMap(([classString, bool]) => !bool ? split(classString) : false).filter(Boolean);
    let added = [];
    let removed = [];
    forRemove.forEach((i) => {
      if (el.classList.contains(i)) {
        el.classList.remove(i);
        removed.push(i);
      }
    });
    forAdd.forEach((i) => {
      if (!el.classList.contains(i)) {
        el.classList.add(i);
        added.push(i);
      }
    });
    return () => {
      removed.forEach((i) => el.classList.add(i));
      added.forEach((i) => el.classList.remove(i));
    };
  }
  function setStyles(el, value) {
    if (typeof value === "object" && value !== null) {
      return setStylesFromObject(el, value);
    }
    return setStylesFromString(el, value);
  }
  function setStylesFromObject(el, value) {
    let previousStyles = {};
    Object.entries(value).forEach(([key, value2]) => {
      previousStyles[key] = el.style[key];
      if (!key.startsWith("--")) {
        key = kebabCase(key);
      }
      el.style.setProperty(key, value2);
    });
    setTimeout(() => {
      if (el.style.length === 0) {
        el.removeAttribute("style");
      }
    });
    return () => {
      setStyles(el, previousStyles);
    };
  }
  function setStylesFromString(el, value) {
    let cache = el.getAttribute("style", value);
    el.setAttribute("style", value);
    return () => {
      el.setAttribute("style", cache || "");
    };
  }
  function kebabCase(subject) {
    return subject.replace(/([a-z])([A-Z])/g, "$1-$2").toLowerCase();
  }
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
  directive("transition", (el, { value, modifiers, expression }, { evaluate: evaluate2 }) => {
    if (typeof expression === "function")
      expression = evaluate2(expression);
    if (expression === false)
      return;
    if (!expression || typeof expression === "boolean") {
      registerTransitionsFromHelper(el, modifiers, value);
    } else {
      registerTransitionsFromClassString(el, expression, value);
    }
  });
  function registerTransitionsFromClassString(el, classString, stage) {
    registerTransitionObject(el, setClasses, "");
    let directiveStorageMap = {
      enter: (classes) => {
        el._x_transition.enter.during = classes;
      },
      "enter-start": (classes) => {
        el._x_transition.enter.start = classes;
      },
      "enter-end": (classes) => {
        el._x_transition.enter.end = classes;
      },
      leave: (classes) => {
        el._x_transition.leave.during = classes;
      },
      "leave-start": (classes) => {
        el._x_transition.leave.start = classes;
      },
      "leave-end": (classes) => {
        el._x_transition.leave.end = classes;
      }
    };
    directiveStorageMap[stage](classString);
  }
  function registerTransitionsFromHelper(el, modifiers, stage) {
    registerTransitionObject(el, setStyles);
    let doesntSpecify = !modifiers.includes("in") && !modifiers.includes("out") && !stage;
    let transitioningIn = doesntSpecify || modifiers.includes("in") || ["enter"].includes(stage);
    let transitioningOut = doesntSpecify || modifiers.includes("out") || ["leave"].includes(stage);
    if (modifiers.includes("in") && !doesntSpecify) {
      modifiers = modifiers.filter((i, index) => index < modifiers.indexOf("out"));
    }
    if (modifiers.includes("out") && !doesntSpecify) {
      modifiers = modifiers.filter((i, index) => index > modifiers.indexOf("out"));
    }
    let wantsAll = !modifiers.includes("opacity") && !modifiers.includes("scale");
    let wantsOpacity = wantsAll || modifiers.includes("opacity");
    let wantsScale = wantsAll || modifiers.includes("scale");
    let opacityValue = wantsOpacity ? 0 : 1;
    let scaleValue = wantsScale ? modifierValue(modifiers, "scale", 95) / 100 : 1;
    let delay = modifierValue(modifiers, "delay", 0) / 1e3;
    let origin = modifierValue(modifiers, "origin", "center");
    let property = "opacity, transform";
    let durationIn = modifierValue(modifiers, "duration", 150) / 1e3;
    let durationOut = modifierValue(modifiers, "duration", 75) / 1e3;
    let easing = `cubic-bezier(0.4, 0.0, 0.2, 1)`;
    if (transitioningIn) {
      el._x_transition.enter.during = {
        transformOrigin: origin,
        transitionDelay: `${delay}s`,
        transitionProperty: property,
        transitionDuration: `${durationIn}s`,
        transitionTimingFunction: easing
      };
      el._x_transition.enter.start = {
        opacity: opacityValue,
        transform: `scale(${scaleValue})`
      };
      el._x_transition.enter.end = {
        opacity: 1,
        transform: `scale(1)`
      };
    }
    if (transitioningOut) {
      el._x_transition.leave.during = {
        transformOrigin: origin,
        transitionDelay: `${delay}s`,
        transitionProperty: property,
        transitionDuration: `${durationOut}s`,
        transitionTimingFunction: easing
      };
      el._x_transition.leave.start = {
        opacity: 1,
        transform: `scale(1)`
      };
      el._x_transition.leave.end = {
        opacity: opacityValue,
        transform: `scale(${scaleValue})`
      };
    }
  }
  function registerTransitionObject(el, setFunction, defaultValue = {}) {
    if (!el._x_transition)
      el._x_transition = {
        enter: { during: defaultValue, start: defaultValue, end: defaultValue },
        leave: { during: defaultValue, start: defaultValue, end: defaultValue },
        in(before = () => {
        }, after = () => {
        }) {
          transition(el, setFunction, {
            during: this.enter.during,
            start: this.enter.start,
            end: this.enter.end
          }, before, after);
        },
        out(before = () => {
        }, after = () => {
        }) {
          transition(el, setFunction, {
            during: this.leave.during,
            start: this.leave.start,
            end: this.leave.end
          }, before, after);
        }
      };
  }
  window.Element.prototype._x_toggleAndCascadeWithTransitions = function(el, value, show, hide) {
    const nextTick2 = document.visibilityState === "visible" ? requestAnimationFrame : setTimeout;
    let clickAwayCompatibleShow = () => nextTick2(show);
    if (value) {
      if (el._x_transition && (el._x_transition.enter || el._x_transition.leave)) {
        el._x_transition.enter && (Object.entries(el._x_transition.enter.during).length || Object.entries(el._x_transition.enter.start).length || Object.entries(el._x_transition.enter.end).length) ? el._x_transition.in(show) : clickAwayCompatibleShow();
      } else {
        el._x_transition ? el._x_transition.in(show) : clickAwayCompatibleShow();
      }
      return;
    }
    el._x_hidePromise = el._x_transition ? new Promise((resolve, reject) => {
      el._x_transition.out(() => {
      }, () => resolve(hide));
      el._x_transitioning.beforeCancel(() => reject({ isFromCancelledTransition: true }));
    }) : Promise.resolve(hide);
    queueMicrotask(() => {
      let closest = closestHide(el);
      if (closest) {
        if (!closest._x_hideChildren)
          closest._x_hideChildren = [];
        closest._x_hideChildren.push(el);
      } else {
        nextTick2(() => {
          let hideAfterChildren = (el2) => {
            let carry = Promise.all([
              el2._x_hidePromise,
              ...(el2._x_hideChildren || []).map(hideAfterChildren)
            ]).then(([i]) => i());
            delete el2._x_hidePromise;
            delete el2._x_hideChildren;
            return carry;
          };
          hideAfterChildren(el).catch((e) => {
            if (!e.isFromCancelledTransition)
              throw e;
          });
        });
      }
    });
  };
  function closestHide(el) {
    let parent = el.parentNode;
    if (!parent)
      return;
    return parent._x_hidePromise ? parent : closestHide(parent);
  }
  function transition(el, setFunction, { during, start: start2, end } = {}, before = () => {
  }, after = () => {
  }) {
    if (el._x_transitioning)
      el._x_transitioning.cancel();
    if (Object.keys(during).length === 0 && Object.keys(start2).length === 0 && Object.keys(end).length === 0) {
      before();
      after();
      return;
    }
    let undoStart, undoDuring, undoEnd;
    performTransition(el, {
      start() {
        undoStart = setFunction(el, start2);
      },
      during() {
        undoDuring = setFunction(el, during);
      },
      before,
      end() {
        undoStart();
        undoEnd = setFunction(el, end);
      },
      after,
      cleanup() {
        undoDuring();
        undoEnd();
      }
    });
  }
  function performTransition(el, stages) {
    let interrupted, reachedBefore, reachedEnd;
    let finish = once(() => {
      mutateDom(() => {
        interrupted = true;
        if (!reachedBefore)
          stages.before();
        if (!reachedEnd) {
          stages.end();
          releaseNextTicks();
        }
        stages.after();
        if (el.isConnected)
          stages.cleanup();
        delete el._x_transitioning;
      });
    });
    el._x_transitioning = {
      beforeCancels: [],
      beforeCancel(callback) {
        this.beforeCancels.push(callback);
      },
      cancel: once(function() {
        while (this.beforeCancels.length) {
          this.beforeCancels.shift()();
        }
        ;
        finish();
      }),
      finish
    };
    mutateDom(() => {
      stages.start();
      stages.during();
    });
    holdNextTicks();
    requestAnimationFrame(() => {
      if (interrupted)
        return;
      let duration = Number(getComputedStyle(el).transitionDuration.replace(/,.*/, "").replace("s", "")) * 1e3;
      let delay = Number(getComputedStyle(el).transitionDelay.replace(/,.*/, "").replace("s", "")) * 1e3;
      if (duration === 0)
        duration = Number(getComputedStyle(el).animationDuration.replace("s", "")) * 1e3;
      mutateDom(() => {
        stages.before();
      });
      reachedBefore = true;
      requestAnimationFrame(() => {
        if (interrupted)
          return;
        mutateDom(() => {
          stages.end();
        });
        releaseNextTicks();
        setTimeout(el._x_transitioning.finish, duration + delay);
        reachedEnd = true;
      });
    });
  }
  function modifierValue(modifiers, key, fallback) {
    if (modifiers.indexOf(key) === -1)
      return fallback;
    const rawValue = modifiers[modifiers.indexOf(key) + 1];
    if (!rawValue)
      return fallback;
    if (key === "scale") {
      if (isNaN(rawValue))
        return fallback;
    }
    if (key === "duration" || key === "delay") {
      let match = rawValue.match(/([0-9]+)ms/);
      if (match)
        return match[1];
    }
    if (key === "origin") {
      if (["top", "right", "left", "center", "bottom"].includes(modifiers[modifiers.indexOf(key) + 2])) {
        return [rawValue, modifiers[modifiers.indexOf(key) + 2]].join(" ");
      }
    }
    return rawValue;
  }
  var isCloning = false;
  function skipDuringClone(callback, fallback = () => {
  }) {
    return (...args) => isCloning ? fallback(...args) : callback(...args);
  }
  function onlyDuringClone(callback) {
    return (...args) => isCloning && callback(...args);
  }
  function clone(oldEl, newEl) {
    if (!newEl._x_dataStack)
      newEl._x_dataStack = oldEl._x_dataStack;
    isCloning = true;
    dontRegisterReactiveSideEffects(() => {
      cloneTree(newEl);
    });
    isCloning = false;
  }
  function cloneTree(el) {
    let hasRunThroughFirstEl = false;
    let shallowWalker = (el2, callback) => {
      walk(el2, (el3, skip) => {
        if (hasRunThroughFirstEl && isRoot(el3))
          return skip();
        hasRunThroughFirstEl = true;
        callback(el3, skip);
      });
    };
    initTree(el, shallowWalker);
  }
  function dontRegisterReactiveSideEffects(callback) {
    let cache = effect;
    overrideEffect((callback2, el) => {
      let storedEffect = cache(callback2);
      release(storedEffect);
      return () => {
      };
    });
    callback();
    overrideEffect(cache);
  }
  function bind(el, name, value, modifiers = []) {
    if (!el._x_bindings)
      el._x_bindings = reactive({});
    el._x_bindings[name] = value;
    name = modifiers.includes("camel") ? camelCase(name) : name;
    switch (name) {
      case "value":
        bindInputValue(el, value);
        break;
      case "style":
        bindStyles(el, value);
        break;
      case "class":
        bindClasses(el, value);
        break;
      case "selected":
      case "checked":
        bindAttributeAndProperty(el, name, value);
        break;
      default:
        bindAttribute(el, name, value);
        break;
    }
  }
  function bindInputValue(el, value) {
    if (el.type === "radio") {
      if (el.attributes.value === void 0) {
        el.value = value;
      }
      if (window.fromModel) {
        el.checked = checkedAttrLooseCompare(el.value, value);
      }
    } else if (el.type === "checkbox") {
      if (Number.isInteger(value)) {
        el.value = value;
      } else if (!Number.isInteger(value) && !Array.isArray(value) && typeof value !== "boolean" && ![null, void 0].includes(value)) {
        el.value = String(value);
      } else {
        if (Array.isArray(value)) {
          el.checked = value.some((val) => checkedAttrLooseCompare(val, el.value));
        } else {
          el.checked = !!value;
        }
      }
    } else if (el.tagName === "SELECT") {
      updateSelect(el, value);
    } else {
      if (el.value === value)
        return;
      el.value = value;
    }
  }
  function bindClasses(el, value) {
    if (el._x_undoAddedClasses)
      el._x_undoAddedClasses();
    el._x_undoAddedClasses = setClasses(el, value);
  }
  function bindStyles(el, value) {
    if (el._x_undoAddedStyles)
      el._x_undoAddedStyles();
    el._x_undoAddedStyles = setStyles(el, value);
  }
  function bindAttributeAndProperty(el, name, value) {
    bindAttribute(el, name, value);
    setPropertyIfChanged(el, name, value);
  }
  function bindAttribute(el, name, value) {
    if ([null, void 0, false].includes(value) && attributeShouldntBePreservedIfFalsy(name)) {
      el.removeAttribute(name);
    } else {
      if (isBooleanAttr(name))
        value = name;
      setIfChanged(el, name, value);
    }
  }
  function setIfChanged(el, attrName, value) {
    if (el.getAttribute(attrName) != value) {
      el.setAttribute(attrName, value);
    }
  }
  function setPropertyIfChanged(el, propName, value) {
    if (el[propName] !== value) {
      el[propName] = value;
    }
  }
  function updateSelect(el, value) {
    const arrayWrappedValue = [].concat(value).map((value2) => {
      return value2 + "";
    });
    Array.from(el.options).forEach((option) => {
      option.selected = arrayWrappedValue.includes(option.value);
    });
  }
  function camelCase(subject) {
    return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase());
  }
  function checkedAttrLooseCompare(valueA, valueB) {
    return valueA == valueB;
  }
  function isBooleanAttr(attrName) {
    const booleanAttributes = [
      "disabled",
      "checked",
      "required",
      "readonly",
      "hidden",
      "open",
      "selected",
      "autofocus",
      "itemscope",
      "multiple",
      "novalidate",
      "allowfullscreen",
      "allowpaymentrequest",
      "formnovalidate",
      "autoplay",
      "controls",
      "loop",
      "muted",
      "playsinline",
      "default",
      "ismap",
      "reversed",
      "async",
      "defer",
      "nomodule"
    ];
    return booleanAttributes.includes(attrName);
  }
  function attributeShouldntBePreservedIfFalsy(name) {
    return !["aria-pressed", "aria-checked", "aria-expanded", "aria-selected"].includes(name);
  }
  function getBinding(el, name, fallback) {
    if (el._x_bindings && el._x_bindings[name] !== void 0)
      return el._x_bindings[name];
    let attr = el.getAttribute(name);
    if (attr === null)
      return typeof fallback === "function" ? fallback() : fallback;
    if (attr === "")
      return true;
    if (isBooleanAttr(name)) {
      return !![name, "true"].includes(attr);
    }
    return attr;
  }
  function debounce(func, wait) {
    var timeout;
    return function() {
      var context = this, args = arguments;
      var later = function() {
        timeout = null;
        func.apply(context, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
  function throttle(func, limit) {
    let inThrottle;
    return function() {
      let context = this, args = arguments;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }
  function plugin(callback) {
    let callbacks = Array.isArray(callback) ? callback : [callback];
    callbacks.forEach((i) => i(alpine_default));
  }
  var stores = {};
  var isReactive = false;
  function store(name, value) {
    if (!isReactive) {
      stores = reactive(stores);
      isReactive = true;
    }
    if (value === void 0) {
      return stores[name];
    }
    stores[name] = value;
    if (typeof value === "object" && value !== null && value.hasOwnProperty("init") && typeof value.init === "function") {
      stores[name].init();
    }
    initInterceptors(stores[name]);
  }
  function getStores() {
    return stores;
  }
  var binds = {};
  function bind2(name, bindings) {
    let getBindings = typeof bindings !== "function" ? () => bindings : bindings;
    if (name instanceof Element) {
      applyBindingsObject(name, getBindings());
    } else {
      binds[name] = getBindings;
    }
  }
  function injectBindingProviders(obj) {
    Object.entries(binds).forEach(([name, callback]) => {
      Object.defineProperty(obj, name, {
        get() {
          return (...args) => {
            return callback(...args);
          };
        }
      });
    });
    return obj;
  }
  function applyBindingsObject(el, obj, original) {
    let cleanupRunners = [];
    while (cleanupRunners.length)
      cleanupRunners.pop()();
    let attributes = Object.entries(obj).map(([name, value]) => ({ name, value }));
    let staticAttributes = attributesOnly(attributes);
    attributes = attributes.map((attribute) => {
      if (staticAttributes.find((attr) => attr.name === attribute.name)) {
        return {
          name: `x-bind:${attribute.name}`,
          value: `"${attribute.value}"`
        };
      }
      return attribute;
    });
    directives(el, attributes, original).map((handle) => {
      cleanupRunners.push(handle.runCleanups);
      handle();
    });
  }
  var datas = {};
  function data(name, callback) {
    datas[name] = callback;
  }
  function injectDataProviders(obj, context) {
    Object.entries(datas).forEach(([name, callback]) => {
      Object.defineProperty(obj, name, {
        get() {
          return (...args) => {
            return callback.bind(context)(...args);
          };
        },
        enumerable: false
      });
    });
    return obj;
  }
  var Alpine = {
    get reactive() {
      return reactive;
    },
    get release() {
      return release;
    },
    get effect() {
      return effect;
    },
    get raw() {
      return raw;
    },
    version: "3.12.2",
    flushAndStopDeferringMutations,
    dontAutoEvaluateFunctions,
    disableEffectScheduling,
    startObservingMutations,
    stopObservingMutations,
    setReactivityEngine,
    closestDataStack,
    skipDuringClone,
    onlyDuringClone,
    addRootSelector,
    addInitSelector,
    addScopeToNode,
    deferMutations,
    mapAttributes,
    evaluateLater,
    interceptInit,
    setEvaluator,
    mergeProxies,
    findClosest,
    closestRoot,
    destroyTree,
    interceptor,
    transition,
    setStyles,
    mutateDom,
    directive,
    throttle,
    debounce,
    evaluate,
    initTree,
    nextTick,
    prefixed: prefix,
    prefix: setPrefix,
    plugin,
    magic,
    store,
    start,
    clone,
    bound: getBinding,
    $data: scope,
    walk,
    data,
    bind: bind2
  };
  var alpine_default = Alpine;
  function makeMap(str, expectsLowerCase) {
    const map = /* @__PURE__ */ Object.create(null);
    const list = str.split(",");
    for (let i = 0; i < list.length; i++) {
      map[list[i]] = true;
    }
    return expectsLowerCase ? (val) => !!map[val.toLowerCase()] : (val) => !!map[val];
  }
  var specialBooleanAttrs = `itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly`;
  var isBooleanAttr2 = /* @__PURE__ */ makeMap(specialBooleanAttrs + `,async,autofocus,autoplay,controls,default,defer,disabled,hidden,loop,open,required,reversed,scoped,seamless,checked,muted,multiple,selected`);
  var EMPTY_OBJ = true ? Object.freeze({}) : {};
  var EMPTY_ARR = true ? Object.freeze([]) : [];
  var extend = Object.assign;
  var hasOwnProperty = Object.prototype.hasOwnProperty;
  var hasOwn = (val, key) => hasOwnProperty.call(val, key);
  var isArray = Array.isArray;
  var isMap = (val) => toTypeString(val) === "[object Map]";
  var isString = (val) => typeof val === "string";
  var isSymbol = (val) => typeof val === "symbol";
  var isObject = (val) => val !== null && typeof val === "object";
  var objectToString = Object.prototype.toString;
  var toTypeString = (value) => objectToString.call(value);
  var toRawType = (value) => {
    return toTypeString(value).slice(8, -1);
  };
  var isIntegerKey = (key) => isString(key) && key !== "NaN" && key[0] !== "-" && "" + parseInt(key, 10) === key;
  var cacheStringFunction = (fn) => {
    const cache = /* @__PURE__ */ Object.create(null);
    return (str) => {
      const hit = cache[str];
      return hit || (cache[str] = fn(str));
    };
  };
  var camelizeRE = /-(\w)/g;
  var camelize = cacheStringFunction((str) => {
    return str.replace(camelizeRE, (_, c) => c ? c.toUpperCase() : "");
  });
  var hyphenateRE = /\B([A-Z])/g;
  var hyphenate = cacheStringFunction((str) => str.replace(hyphenateRE, "-$1").toLowerCase());
  var capitalize = cacheStringFunction((str) => str.charAt(0).toUpperCase() + str.slice(1));
  var toHandlerKey = cacheStringFunction((str) => str ? `on${capitalize(str)}` : ``);
  var hasChanged = (value, oldValue) => value !== oldValue && (value === value || oldValue === oldValue);
  var targetMap = /* @__PURE__ */ new WeakMap();
  var effectStack = [];
  var activeEffect;
  var ITERATE_KEY = Symbol(true ? "iterate" : "");
  var MAP_KEY_ITERATE_KEY = Symbol(true ? "Map key iterate" : "");
  function isEffect(fn) {
    return fn && fn._isEffect === true;
  }
  function effect2(fn, options = EMPTY_OBJ) {
    if (isEffect(fn)) {
      fn = fn.raw;
    }
    const effect3 = createReactiveEffect(fn, options);
    if (!options.lazy) {
      effect3();
    }
    return effect3;
  }
  function stop(effect3) {
    if (effect3.active) {
      cleanup(effect3);
      if (effect3.options.onStop) {
        effect3.options.onStop();
      }
      effect3.active = false;
    }
  }
  var uid = 0;
  function createReactiveEffect(fn, options) {
    const effect3 = function reactiveEffect() {
      if (!effect3.active) {
        return fn();
      }
      if (!effectStack.includes(effect3)) {
        cleanup(effect3);
        try {
          enableTracking();
          effectStack.push(effect3);
          activeEffect = effect3;
          return fn();
        } finally {
          effectStack.pop();
          resetTracking();
          activeEffect = effectStack[effectStack.length - 1];
        }
      }
    };
    effect3.id = uid++;
    effect3.allowRecurse = !!options.allowRecurse;
    effect3._isEffect = true;
    effect3.active = true;
    effect3.raw = fn;
    effect3.deps = [];
    effect3.options = options;
    return effect3;
  }
  function cleanup(effect3) {
    const { deps } = effect3;
    if (deps.length) {
      for (let i = 0; i < deps.length; i++) {
        deps[i].delete(effect3);
      }
      deps.length = 0;
    }
  }
  var shouldTrack = true;
  var trackStack = [];
  function pauseTracking() {
    trackStack.push(shouldTrack);
    shouldTrack = false;
  }
  function enableTracking() {
    trackStack.push(shouldTrack);
    shouldTrack = true;
  }
  function resetTracking() {
    const last = trackStack.pop();
    shouldTrack = last === void 0 ? true : last;
  }
  function track(target, type, key) {
    if (!shouldTrack || activeEffect === void 0) {
      return;
    }
    let depsMap = targetMap.get(target);
    if (!depsMap) {
      targetMap.set(target, depsMap = /* @__PURE__ */ new Map());
    }
    let dep = depsMap.get(key);
    if (!dep) {
      depsMap.set(key, dep = /* @__PURE__ */ new Set());
    }
    if (!dep.has(activeEffect)) {
      dep.add(activeEffect);
      activeEffect.deps.push(dep);
      if (activeEffect.options.onTrack) {
        activeEffect.options.onTrack({
          effect: activeEffect,
          target,
          type,
          key
        });
      }
    }
  }
  function trigger(target, type, key, newValue, oldValue, oldTarget) {
    const depsMap = targetMap.get(target);
    if (!depsMap) {
      return;
    }
    const effects = /* @__PURE__ */ new Set();
    const add2 = (effectsToAdd) => {
      if (effectsToAdd) {
        effectsToAdd.forEach((effect3) => {
          if (effect3 !== activeEffect || effect3.allowRecurse) {
            effects.add(effect3);
          }
        });
      }
    };
    if (type === "clear") {
      depsMap.forEach(add2);
    } else if (key === "length" && isArray(target)) {
      depsMap.forEach((dep, key2) => {
        if (key2 === "length" || key2 >= newValue) {
          add2(dep);
        }
      });
    } else {
      if (key !== void 0) {
        add2(depsMap.get(key));
      }
      switch (type) {
        case "add":
          if (!isArray(target)) {
            add2(depsMap.get(ITERATE_KEY));
            if (isMap(target)) {
              add2(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          } else if (isIntegerKey(key)) {
            add2(depsMap.get("length"));
          }
          break;
        case "delete":
          if (!isArray(target)) {
            add2(depsMap.get(ITERATE_KEY));
            if (isMap(target)) {
              add2(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          }
          break;
        case "set":
          if (isMap(target)) {
            add2(depsMap.get(ITERATE_KEY));
          }
          break;
      }
    }
    const run = (effect3) => {
      if (effect3.options.onTrigger) {
        effect3.options.onTrigger({
          effect: effect3,
          target,
          key,
          type,
          newValue,
          oldValue,
          oldTarget
        });
      }
      if (effect3.options.scheduler) {
        effect3.options.scheduler(effect3);
      } else {
        effect3();
      }
    };
    effects.forEach(run);
  }
  var isNonTrackableKeys = /* @__PURE__ */ makeMap(`__proto__,__v_isRef,__isVue`);
  var builtInSymbols = new Set(Object.getOwnPropertyNames(Symbol).map((key) => Symbol[key]).filter(isSymbol));
  var get2 = /* @__PURE__ */ createGetter();
  var shallowGet = /* @__PURE__ */ createGetter(false, true);
  var readonlyGet = /* @__PURE__ */ createGetter(true);
  var shallowReadonlyGet = /* @__PURE__ */ createGetter(true, true);
  var arrayInstrumentations = {};
  ["includes", "indexOf", "lastIndexOf"].forEach((key) => {
    const method = Array.prototype[key];
    arrayInstrumentations[key] = function(...args) {
      const arr = toRaw(this);
      for (let i = 0, l = this.length; i < l; i++) {
        track(arr, "get", i + "");
      }
      const res = method.apply(arr, args);
      if (res === -1 || res === false) {
        return method.apply(arr, args.map(toRaw));
      } else {
        return res;
      }
    };
  });
  ["push", "pop", "shift", "unshift", "splice"].forEach((key) => {
    const method = Array.prototype[key];
    arrayInstrumentations[key] = function(...args) {
      pauseTracking();
      const res = method.apply(this, args);
      resetTracking();
      return res;
    };
  });
  function createGetter(isReadonly = false, shallow = false) {
    return function get3(target, key, receiver) {
      if (key === "__v_isReactive") {
        return !isReadonly;
      } else if (key === "__v_isReadonly") {
        return isReadonly;
      } else if (key === "__v_raw" && receiver === (isReadonly ? shallow ? shallowReadonlyMap : readonlyMap : shallow ? shallowReactiveMap : reactiveMap).get(target)) {
        return target;
      }
      const targetIsArray = isArray(target);
      if (!isReadonly && targetIsArray && hasOwn(arrayInstrumentations, key)) {
        return Reflect.get(arrayInstrumentations, key, receiver);
      }
      const res = Reflect.get(target, key, receiver);
      if (isSymbol(key) ? builtInSymbols.has(key) : isNonTrackableKeys(key)) {
        return res;
      }
      if (!isReadonly) {
        track(target, "get", key);
      }
      if (shallow) {
        return res;
      }
      if (isRef(res)) {
        const shouldUnwrap = !targetIsArray || !isIntegerKey(key);
        return shouldUnwrap ? res.value : res;
      }
      if (isObject(res)) {
        return isReadonly ? readonly(res) : reactive2(res);
      }
      return res;
    };
  }
  var set2 = /* @__PURE__ */ createSetter();
  var shallowSet = /* @__PURE__ */ createSetter(true);
  function createSetter(shallow = false) {
    return function set3(target, key, value, receiver) {
      let oldValue = target[key];
      if (!shallow) {
        value = toRaw(value);
        oldValue = toRaw(oldValue);
        if (!isArray(target) && isRef(oldValue) && !isRef(value)) {
          oldValue.value = value;
          return true;
        }
      }
      const hadKey = isArray(target) && isIntegerKey(key) ? Number(key) < target.length : hasOwn(target, key);
      const result = Reflect.set(target, key, value, receiver);
      if (target === toRaw(receiver)) {
        if (!hadKey) {
          trigger(target, "add", key, value);
        } else if (hasChanged(value, oldValue)) {
          trigger(target, "set", key, value, oldValue);
        }
      }
      return result;
    };
  }
  function deleteProperty(target, key) {
    const hadKey = hasOwn(target, key);
    const oldValue = target[key];
    const result = Reflect.deleteProperty(target, key);
    if (result && hadKey) {
      trigger(target, "delete", key, void 0, oldValue);
    }
    return result;
  }
  function has(target, key) {
    const result = Reflect.has(target, key);
    if (!isSymbol(key) || !builtInSymbols.has(key)) {
      track(target, "has", key);
    }
    return result;
  }
  function ownKeys(target) {
    track(target, "iterate", isArray(target) ? "length" : ITERATE_KEY);
    return Reflect.ownKeys(target);
  }
  var mutableHandlers = {
    get: get2,
    set: set2,
    deleteProperty,
    has,
    ownKeys
  };
  var readonlyHandlers = {
    get: readonlyGet,
    set(target, key) {
      if (true) {
        console.warn(`Set operation on key "${String(key)}" failed: target is readonly.`, target);
      }
      return true;
    },
    deleteProperty(target, key) {
      if (true) {
        console.warn(`Delete operation on key "${String(key)}" failed: target is readonly.`, target);
      }
      return true;
    }
  };
  var shallowReactiveHandlers = extend({}, mutableHandlers, {
    get: shallowGet,
    set: shallowSet
  });
  var shallowReadonlyHandlers = extend({}, readonlyHandlers, {
    get: shallowReadonlyGet
  });
  var toReactive = (value) => isObject(value) ? reactive2(value) : value;
  var toReadonly = (value) => isObject(value) ? readonly(value) : value;
  var toShallow = (value) => value;
  var getProto = (v) => Reflect.getPrototypeOf(v);
  function get$1(target, key, isReadonly = false, isShallow = false) {
    target = target["__v_raw"];
    const rawTarget = toRaw(target);
    const rawKey = toRaw(key);
    if (key !== rawKey) {
      !isReadonly && track(rawTarget, "get", key);
    }
    !isReadonly && track(rawTarget, "get", rawKey);
    const { has: has2 } = getProto(rawTarget);
    const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
    if (has2.call(rawTarget, key)) {
      return wrap(target.get(key));
    } else if (has2.call(rawTarget, rawKey)) {
      return wrap(target.get(rawKey));
    } else if (target !== rawTarget) {
      target.get(key);
    }
  }
  function has$1(key, isReadonly = false) {
    const target = this["__v_raw"];
    const rawTarget = toRaw(target);
    const rawKey = toRaw(key);
    if (key !== rawKey) {
      !isReadonly && track(rawTarget, "has", key);
    }
    !isReadonly && track(rawTarget, "has", rawKey);
    return key === rawKey ? target.has(key) : target.has(key) || target.has(rawKey);
  }
  function size(target, isReadonly = false) {
    target = target["__v_raw"];
    !isReadonly && track(toRaw(target), "iterate", ITERATE_KEY);
    return Reflect.get(target, "size", target);
  }
  function add(value) {
    value = toRaw(value);
    const target = toRaw(this);
    const proto = getProto(target);
    const hadKey = proto.has.call(target, value);
    if (!hadKey) {
      target.add(value);
      trigger(target, "add", value, value);
    }
    return this;
  }
  function set$1(key, value) {
    value = toRaw(value);
    const target = toRaw(this);
    const { has: has2, get: get3 } = getProto(target);
    let hadKey = has2.call(target, key);
    if (!hadKey) {
      key = toRaw(key);
      hadKey = has2.call(target, key);
    } else if (true) {
      checkIdentityKeys(target, has2, key);
    }
    const oldValue = get3.call(target, key);
    target.set(key, value);
    if (!hadKey) {
      trigger(target, "add", key, value);
    } else if (hasChanged(value, oldValue)) {
      trigger(target, "set", key, value, oldValue);
    }
    return this;
  }
  function deleteEntry(key) {
    const target = toRaw(this);
    const { has: has2, get: get3 } = getProto(target);
    let hadKey = has2.call(target, key);
    if (!hadKey) {
      key = toRaw(key);
      hadKey = has2.call(target, key);
    } else if (true) {
      checkIdentityKeys(target, has2, key);
    }
    const oldValue = get3 ? get3.call(target, key) : void 0;
    const result = target.delete(key);
    if (hadKey) {
      trigger(target, "delete", key, void 0, oldValue);
    }
    return result;
  }
  function clear() {
    const target = toRaw(this);
    const hadItems = target.size !== 0;
    const oldTarget = true ? isMap(target) ? new Map(target) : new Set(target) : void 0;
    const result = target.clear();
    if (hadItems) {
      trigger(target, "clear", void 0, void 0, oldTarget);
    }
    return result;
  }
  function createForEach(isReadonly, isShallow) {
    return function forEach(callback, thisArg) {
      const observed = this;
      const target = observed["__v_raw"];
      const rawTarget = toRaw(target);
      const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
      !isReadonly && track(rawTarget, "iterate", ITERATE_KEY);
      return target.forEach((value, key) => {
        return callback.call(thisArg, wrap(value), wrap(key), observed);
      });
    };
  }
  function createIterableMethod(method, isReadonly, isShallow) {
    return function(...args) {
      const target = this["__v_raw"];
      const rawTarget = toRaw(target);
      const targetIsMap = isMap(rawTarget);
      const isPair = method === "entries" || method === Symbol.iterator && targetIsMap;
      const isKeyOnly = method === "keys" && targetIsMap;
      const innerIterator = target[method](...args);
      const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
      !isReadonly && track(rawTarget, "iterate", isKeyOnly ? MAP_KEY_ITERATE_KEY : ITERATE_KEY);
      return {
        next() {
          const { value, done } = innerIterator.next();
          return done ? { value, done } : {
            value: isPair ? [wrap(value[0]), wrap(value[1])] : wrap(value),
            done
          };
        },
        [Symbol.iterator]() {
          return this;
        }
      };
    };
  }
  function createReadonlyMethod(type) {
    return function(...args) {
      if (true) {
        const key = args[0] ? `on key "${args[0]}" ` : ``;
        console.warn(`${capitalize(type)} operation ${key}failed: target is readonly.`, toRaw(this));
      }
      return type === "delete" ? false : this;
    };
  }
  var mutableInstrumentations = {
    get(key) {
      return get$1(this, key);
    },
    get size() {
      return size(this);
    },
    has: has$1,
    add,
    set: set$1,
    delete: deleteEntry,
    clear,
    forEach: createForEach(false, false)
  };
  var shallowInstrumentations = {
    get(key) {
      return get$1(this, key, false, true);
    },
    get size() {
      return size(this);
    },
    has: has$1,
    add,
    set: set$1,
    delete: deleteEntry,
    clear,
    forEach: createForEach(false, true)
  };
  var readonlyInstrumentations = {
    get(key) {
      return get$1(this, key, true);
    },
    get size() {
      return size(this, true);
    },
    has(key) {
      return has$1.call(this, key, true);
    },
    add: createReadonlyMethod("add"),
    set: createReadonlyMethod("set"),
    delete: createReadonlyMethod("delete"),
    clear: createReadonlyMethod("clear"),
    forEach: createForEach(true, false)
  };
  var shallowReadonlyInstrumentations = {
    get(key) {
      return get$1(this, key, true, true);
    },
    get size() {
      return size(this, true);
    },
    has(key) {
      return has$1.call(this, key, true);
    },
    add: createReadonlyMethod("add"),
    set: createReadonlyMethod("set"),
    delete: createReadonlyMethod("delete"),
    clear: createReadonlyMethod("clear"),
    forEach: createForEach(true, true)
  };
  var iteratorMethods = ["keys", "values", "entries", Symbol.iterator];
  iteratorMethods.forEach((method) => {
    mutableInstrumentations[method] = createIterableMethod(method, false, false);
    readonlyInstrumentations[method] = createIterableMethod(method, true, false);
    shallowInstrumentations[method] = createIterableMethod(method, false, true);
    shallowReadonlyInstrumentations[method] = createIterableMethod(method, true, true);
  });
  function createInstrumentationGetter(isReadonly, shallow) {
    const instrumentations = shallow ? isReadonly ? shallowReadonlyInstrumentations : shallowInstrumentations : isReadonly ? readonlyInstrumentations : mutableInstrumentations;
    return (target, key, receiver) => {
      if (key === "__v_isReactive") {
        return !isReadonly;
      } else if (key === "__v_isReadonly") {
        return isReadonly;
      } else if (key === "__v_raw") {
        return target;
      }
      return Reflect.get(hasOwn(instrumentations, key) && key in target ? instrumentations : target, key, receiver);
    };
  }
  var mutableCollectionHandlers = {
    get: createInstrumentationGetter(false, false)
  };
  var shallowCollectionHandlers = {
    get: createInstrumentationGetter(false, true)
  };
  var readonlyCollectionHandlers = {
    get: createInstrumentationGetter(true, false)
  };
  var shallowReadonlyCollectionHandlers = {
    get: createInstrumentationGetter(true, true)
  };
  function checkIdentityKeys(target, has2, key) {
    const rawKey = toRaw(key);
    if (rawKey !== key && has2.call(target, rawKey)) {
      const type = toRawType(target);
      console.warn(`Reactive ${type} contains both the raw and reactive versions of the same object${type === `Map` ? ` as keys` : ``}, which can lead to inconsistencies. Avoid differentiating between the raw and reactive versions of an object and only use the reactive version if possible.`);
    }
  }
  var reactiveMap = /* @__PURE__ */ new WeakMap();
  var shallowReactiveMap = /* @__PURE__ */ new WeakMap();
  var readonlyMap = /* @__PURE__ */ new WeakMap();
  var shallowReadonlyMap = /* @__PURE__ */ new WeakMap();
  function targetTypeMap(rawType) {
    switch (rawType) {
      case "Object":
      case "Array":
        return 1;
      case "Map":
      case "Set":
      case "WeakMap":
      case "WeakSet":
        return 2;
      default:
        return 0;
    }
  }
  function getTargetType(value) {
    return value["__v_skip"] || !Object.isExtensible(value) ? 0 : targetTypeMap(toRawType(value));
  }
  function reactive2(target) {
    if (target && target["__v_isReadonly"]) {
      return target;
    }
    return createReactiveObject(target, false, mutableHandlers, mutableCollectionHandlers, reactiveMap);
  }
  function readonly(target) {
    return createReactiveObject(target, true, readonlyHandlers, readonlyCollectionHandlers, readonlyMap);
  }
  function createReactiveObject(target, isReadonly, baseHandlers, collectionHandlers, proxyMap) {
    if (!isObject(target)) {
      if (true) {
        console.warn(`value cannot be made reactive: ${String(target)}`);
      }
      return target;
    }
    if (target["__v_raw"] && !(isReadonly && target["__v_isReactive"])) {
      return target;
    }
    const existingProxy = proxyMap.get(target);
    if (existingProxy) {
      return existingProxy;
    }
    const targetType = getTargetType(target);
    if (targetType === 0) {
      return target;
    }
    const proxy = new Proxy(target, targetType === 2 ? collectionHandlers : baseHandlers);
    proxyMap.set(target, proxy);
    return proxy;
  }
  function toRaw(observed) {
    return observed && toRaw(observed["__v_raw"]) || observed;
  }
  function isRef(r) {
    return Boolean(r && r.__v_isRef === true);
  }
  magic("nextTick", () => nextTick);
  magic("dispatch", (el) => dispatch.bind(dispatch, el));
  magic("watch", (el, { evaluateLater: evaluateLater2, effect: effect3 }) => (key, callback) => {
    let evaluate2 = evaluateLater2(key);
    let firstTime = true;
    let oldValue;
    let effectReference = effect3(() => evaluate2((value) => {
      JSON.stringify(value);
      if (!firstTime) {
        queueMicrotask(() => {
          callback(value, oldValue);
          oldValue = value;
        });
      } else {
        oldValue = value;
      }
      firstTime = false;
    }));
    el._x_effects.delete(effectReference);
  });
  magic("store", getStores);
  magic("data", (el) => scope(el));
  magic("root", (el) => closestRoot(el));
  magic("refs", (el) => {
    if (el._x_refs_proxy)
      return el._x_refs_proxy;
    el._x_refs_proxy = mergeProxies(getArrayOfRefObject(el));
    return el._x_refs_proxy;
  });
  function getArrayOfRefObject(el) {
    let refObjects = [];
    let currentEl = el;
    while (currentEl) {
      if (currentEl._x_refs)
        refObjects.push(currentEl._x_refs);
      currentEl = currentEl.parentNode;
    }
    return refObjects;
  }
  var globalIdMemo = {};
  function findAndIncrementId(name) {
    if (!globalIdMemo[name])
      globalIdMemo[name] = 0;
    return ++globalIdMemo[name];
  }
  function closestIdRoot(el, name) {
    return findClosest(el, (element) => {
      if (element._x_ids && element._x_ids[name])
        return true;
    });
  }
  function setIdRoot(el, name) {
    if (!el._x_ids)
      el._x_ids = {};
    if (!el._x_ids[name])
      el._x_ids[name] = findAndIncrementId(name);
  }
  magic("id", (el) => (name, key = null) => {
    let root = closestIdRoot(el, name);
    let id = root ? root._x_ids[name] : findAndIncrementId(name);
    return key ? `${name}-${id}-${key}` : `${name}-${id}`;
  });
  magic("el", (el) => el);
  warnMissingPluginMagic("Focus", "focus", "focus");
  warnMissingPluginMagic("Persist", "persist", "persist");
  function warnMissingPluginMagic(name, magicName, slug) {
    magic(magicName, (el) => warn(`You can't use [$${directiveName}] without first installing the "${name}" plugin here: https://alpinejs.dev/plugins/${slug}`, el));
  }
  function entangle({ get: outerGet, set: outerSet }, { get: innerGet, set: innerSet }) {
    let firstRun = true;
    let outerHash, innerHash, outerHashLatest, innerHashLatest;
    let reference = effect(() => {
      let outer, inner;
      if (firstRun) {
        outer = outerGet();
        innerSet(outer);
        inner = innerGet();
        firstRun = false;
      } else {
        outer = outerGet();
        inner = innerGet();
        outerHashLatest = JSON.stringify(outer);
        innerHashLatest = JSON.stringify(inner);
        if (outerHashLatest !== outerHash) {
          inner = innerGet();
          innerSet(outer);
          inner = outer;
        } else {
          outerSet(inner);
          outer = inner;
        }
      }
      outerHash = JSON.stringify(outer);
      innerHash = JSON.stringify(inner);
    });
    return () => {
      release(reference);
    };
  }
  directive("modelable", (el, { expression }, { effect: effect3, evaluateLater: evaluateLater2, cleanup: cleanup2 }) => {
    let func = evaluateLater2(expression);
    let innerGet = () => {
      let result;
      func((i) => result = i);
      return result;
    };
    let evaluateInnerSet = evaluateLater2(`${expression} = __placeholder`);
    let innerSet = (val) => evaluateInnerSet(() => {
    }, { scope: { __placeholder: val } });
    let initialValue = innerGet();
    innerSet(initialValue);
    queueMicrotask(() => {
      if (!el._x_model)
        return;
      el._x_removeModelListeners["default"]();
      let outerGet = el._x_model.get;
      let outerSet = el._x_model.set;
      let releaseEntanglement = entangle({
        get() {
          return outerGet();
        },
        set(value) {
          outerSet(value);
        }
      }, {
        get() {
          return innerGet();
        },
        set(value) {
          innerSet(value);
        }
      });
      cleanup2(releaseEntanglement);
    });
  });
  var teleportContainerDuringClone = document.createElement("div");
  directive("teleport", (el, { modifiers, expression }, { cleanup: cleanup2 }) => {
    if (el.tagName.toLowerCase() !== "template")
      warn("x-teleport can only be used on a <template> tag", el);
    let target = skipDuringClone(() => {
      return document.querySelector(expression);
    }, () => {
      return teleportContainerDuringClone;
    })();
    if (!target)
      warn(`Cannot find x-teleport element for selector: "${expression}"`);
    let clone2 = el.content.cloneNode(true).firstElementChild;
    el._x_teleport = clone2;
    clone2._x_teleportBack = el;
    if (el._x_forwardEvents) {
      el._x_forwardEvents.forEach((eventName) => {
        clone2.addEventListener(eventName, (e) => {
          e.stopPropagation();
          el.dispatchEvent(new e.constructor(e.type, e));
        });
      });
    }
    addScopeToNode(clone2, {}, el);
    mutateDom(() => {
      if (modifiers.includes("prepend")) {
        target.parentNode.insertBefore(clone2, target);
      } else if (modifiers.includes("append")) {
        target.parentNode.insertBefore(clone2, target.nextSibling);
      } else {
        target.appendChild(clone2);
      }
      initTree(clone2);
      clone2._x_ignore = true;
    });
    cleanup2(() => clone2.remove());
  });
  var handler = () => {
  };
  handler.inline = (el, { modifiers }, { cleanup: cleanup2 }) => {
    modifiers.includes("self") ? el._x_ignoreSelf = true : el._x_ignore = true;
    cleanup2(() => {
      modifiers.includes("self") ? delete el._x_ignoreSelf : delete el._x_ignore;
    });
  };
  directive("ignore", handler);
  directive("effect", (el, { expression }, { effect: effect3 }) => effect3(evaluateLater(el, expression)));
  function on(el, event, modifiers, callback) {
    let listenerTarget = el;
    let handler3 = (e) => callback(e);
    let options = {};
    let wrapHandler = (callback2, wrapper) => (e) => wrapper(callback2, e);
    if (modifiers.includes("dot"))
      event = dotSyntax(event);
    if (modifiers.includes("camel"))
      event = camelCase2(event);
    if (modifiers.includes("passive"))
      options.passive = true;
    if (modifiers.includes("capture"))
      options.capture = true;
    if (modifiers.includes("window"))
      listenerTarget = window;
    if (modifiers.includes("document"))
      listenerTarget = document;
    if (modifiers.includes("debounce")) {
      let nextModifier = modifiers[modifiers.indexOf("debounce") + 1] || "invalid-wait";
      let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
      handler3 = debounce(handler3, wait);
    }
    if (modifiers.includes("throttle")) {
      let nextModifier = modifiers[modifiers.indexOf("throttle") + 1] || "invalid-wait";
      let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
      handler3 = throttle(handler3, wait);
    }
    if (modifiers.includes("prevent"))
      handler3 = wrapHandler(handler3, (next, e) => {
        e.preventDefault();
        next(e);
      });
    if (modifiers.includes("stop"))
      handler3 = wrapHandler(handler3, (next, e) => {
        e.stopPropagation();
        next(e);
      });
    if (modifiers.includes("self"))
      handler3 = wrapHandler(handler3, (next, e) => {
        e.target === el && next(e);
      });
    if (modifiers.includes("away") || modifiers.includes("outside")) {
      listenerTarget = document;
      handler3 = wrapHandler(handler3, (next, e) => {
        if (el.contains(e.target))
          return;
        if (e.target.isConnected === false)
          return;
        if (el.offsetWidth < 1 && el.offsetHeight < 1)
          return;
        if (el._x_isShown === false)
          return;
        next(e);
      });
    }
    if (modifiers.includes("once")) {
      handler3 = wrapHandler(handler3, (next, e) => {
        next(e);
        listenerTarget.removeEventListener(event, handler3, options);
      });
    }
    handler3 = wrapHandler(handler3, (next, e) => {
      if (isKeyEvent(event)) {
        if (isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers)) {
          return;
        }
      }
      next(e);
    });
    listenerTarget.addEventListener(event, handler3, options);
    return () => {
      listenerTarget.removeEventListener(event, handler3, options);
    };
  }
  function dotSyntax(subject) {
    return subject.replace(/-/g, ".");
  }
  function camelCase2(subject) {
    return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase());
  }
  function isNumeric(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
  }
  function kebabCase2(subject) {
    if ([" ", "_"].includes(subject))
      return subject;
    return subject.replace(/([a-z])([A-Z])/g, "$1-$2").replace(/[_\s]/, "-").toLowerCase();
  }
  function isKeyEvent(event) {
    return ["keydown", "keyup"].includes(event);
  }
  function isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers) {
    let keyModifiers = modifiers.filter((i) => {
      return !["window", "document", "prevent", "stop", "once", "capture"].includes(i);
    });
    if (keyModifiers.includes("debounce")) {
      let debounceIndex = keyModifiers.indexOf("debounce");
      keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || "invalid-wait").split("ms")[0]) ? 2 : 1);
    }
    if (keyModifiers.includes("throttle")) {
      let debounceIndex = keyModifiers.indexOf("throttle");
      keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || "invalid-wait").split("ms")[0]) ? 2 : 1);
    }
    if (keyModifiers.length === 0)
      return false;
    if (keyModifiers.length === 1 && keyToModifiers(e.key).includes(keyModifiers[0]))
      return false;
    const systemKeyModifiers = ["ctrl", "shift", "alt", "meta", "cmd", "super"];
    const selectedSystemKeyModifiers = systemKeyModifiers.filter((modifier) => keyModifiers.includes(modifier));
    keyModifiers = keyModifiers.filter((i) => !selectedSystemKeyModifiers.includes(i));
    if (selectedSystemKeyModifiers.length > 0) {
      const activelyPressedKeyModifiers = selectedSystemKeyModifiers.filter((modifier) => {
        if (modifier === "cmd" || modifier === "super")
          modifier = "meta";
        return e[`${modifier}Key`];
      });
      if (activelyPressedKeyModifiers.length === selectedSystemKeyModifiers.length) {
        if (keyToModifiers(e.key).includes(keyModifiers[0]))
          return false;
      }
    }
    return true;
  }
  function keyToModifiers(key) {
    if (!key)
      return [];
    key = kebabCase2(key);
    let modifierToKeyMap = {
      ctrl: "control",
      slash: "/",
      space: " ",
      spacebar: " ",
      cmd: "meta",
      esc: "escape",
      up: "arrow-up",
      down: "arrow-down",
      left: "arrow-left",
      right: "arrow-right",
      period: ".",
      equal: "=",
      minus: "-",
      underscore: "_"
    };
    modifierToKeyMap[key] = key;
    return Object.keys(modifierToKeyMap).map((modifier) => {
      if (modifierToKeyMap[modifier] === key)
        return modifier;
    }).filter((modifier) => modifier);
  }
  directive("model", (el, { modifiers, expression }, { effect: effect3, cleanup: cleanup2 }) => {
    let scopeTarget = el;
    if (modifiers.includes("parent")) {
      scopeTarget = el.parentNode;
    }
    let evaluateGet = evaluateLater(scopeTarget, expression);
    let evaluateSet;
    if (typeof expression === "string") {
      evaluateSet = evaluateLater(scopeTarget, `${expression} = __placeholder`);
    } else if (typeof expression === "function" && typeof expression() === "string") {
      evaluateSet = evaluateLater(scopeTarget, `${expression()} = __placeholder`);
    } else {
      evaluateSet = () => {
      };
    }
    let getValue = () => {
      let result;
      evaluateGet((value) => result = value);
      return isGetterSetter(result) ? result.get() : result;
    };
    let setValue = (value) => {
      let result;
      evaluateGet((value2) => result = value2);
      if (isGetterSetter(result)) {
        result.set(value);
      } else {
        evaluateSet(() => {
        }, {
          scope: { __placeholder: value }
        });
      }
    };
    if (typeof expression === "string" && el.type === "radio") {
      mutateDom(() => {
        if (!el.hasAttribute("name"))
          el.setAttribute("name", expression);
      });
    }
    var event = el.tagName.toLowerCase() === "select" || ["checkbox", "radio"].includes(el.type) || modifiers.includes("lazy") ? "change" : "input";
    let removeListener = isCloning ? () => {
    } : on(el, event, modifiers, (e) => {
      setValue(getInputValue(el, modifiers, e, getValue()));
    });
    if (modifiers.includes("fill") && [null, ""].includes(getValue())) {
      el.dispatchEvent(new Event(event, {}));
    }
    if (!el._x_removeModelListeners)
      el._x_removeModelListeners = {};
    el._x_removeModelListeners["default"] = removeListener;
    cleanup2(() => el._x_removeModelListeners["default"]());
    if (el.form) {
      let removeResetListener = on(el.form, "reset", [], (e) => {
        nextTick(() => el._x_model && el._x_model.set(el.value));
      });
      cleanup2(() => removeResetListener());
    }
    el._x_model = {
      get() {
        return getValue();
      },
      set(value) {
        setValue(value);
      }
    };
    el._x_forceModelUpdate = (value) => {
      value = value === void 0 ? getValue() : value;
      if (value === void 0 && typeof expression === "string" && expression.match(/\./))
        value = "";
      window.fromModel = true;
      mutateDom(() => bind(el, "value", value));
      delete window.fromModel;
    };
    effect3(() => {
      let value = getValue();
      if (modifiers.includes("unintrusive") && document.activeElement.isSameNode(el))
        return;
      el._x_forceModelUpdate(value);
    });
  });
  function getInputValue(el, modifiers, event, currentValue) {
    return mutateDom(() => {
      if (event instanceof CustomEvent && event.detail !== void 0)
        return event.detail ?? event.target.value;
      else if (el.type === "checkbox") {
        if (Array.isArray(currentValue)) {
          let newValue = modifiers.includes("number") ? safeParseNumber(event.target.value) : event.target.value;
          return event.target.checked ? currentValue.concat([newValue]) : currentValue.filter((el2) => !checkedAttrLooseCompare2(el2, newValue));
        } else {
          return event.target.checked;
        }
      } else if (el.tagName.toLowerCase() === "select" && el.multiple) {
        return modifiers.includes("number") ? Array.from(event.target.selectedOptions).map((option) => {
          let rawValue = option.value || option.text;
          return safeParseNumber(rawValue);
        }) : Array.from(event.target.selectedOptions).map((option) => {
          return option.value || option.text;
        });
      } else {
        let rawValue = event.target.value;
        return modifiers.includes("number") ? safeParseNumber(rawValue) : modifiers.includes("trim") ? rawValue.trim() : rawValue;
      }
    });
  }
  function safeParseNumber(rawValue) {
    let number = rawValue ? parseFloat(rawValue) : null;
    return isNumeric2(number) ? number : rawValue;
  }
  function checkedAttrLooseCompare2(valueA, valueB) {
    return valueA == valueB;
  }
  function isNumeric2(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
  }
  function isGetterSetter(value) {
    return value !== null && typeof value === "object" && typeof value.get === "function" && typeof value.set === "function";
  }
  directive("cloak", (el) => queueMicrotask(() => mutateDom(() => el.removeAttribute(prefix("cloak")))));
  addInitSelector(() => `[${prefix("init")}]`);
  directive("init", skipDuringClone((el, { expression }, { evaluate: evaluate2 }) => {
    if (typeof expression === "string") {
      return !!expression.trim() && evaluate2(expression, {}, false);
    }
    return evaluate2(expression, {}, false);
  }));
  directive("text", (el, { expression }, { effect: effect3, evaluateLater: evaluateLater2 }) => {
    let evaluate2 = evaluateLater2(expression);
    effect3(() => {
      evaluate2((value) => {
        mutateDom(() => {
          el.textContent = value;
        });
      });
    });
  });
  directive("html", (el, { expression }, { effect: effect3, evaluateLater: evaluateLater2 }) => {
    let evaluate2 = evaluateLater2(expression);
    effect3(() => {
      evaluate2((value) => {
        mutateDom(() => {
          el.innerHTML = value;
          el._x_ignoreSelf = true;
          initTree(el);
          delete el._x_ignoreSelf;
        });
      });
    });
  });
  mapAttributes(startingWith(":", into(prefix("bind:"))));
  directive("bind", (el, { value, modifiers, expression, original }, { effect: effect3 }) => {
    if (!value) {
      let bindingProviders = {};
      injectBindingProviders(bindingProviders);
      let getBindings = evaluateLater(el, expression);
      getBindings((bindings) => {
        applyBindingsObject(el, bindings, original);
      }, { scope: bindingProviders });
      return;
    }
    if (value === "key")
      return storeKeyForXFor(el, expression);
    let evaluate2 = evaluateLater(el, expression);
    effect3(() => evaluate2((result) => {
      if (result === void 0 && typeof expression === "string" && expression.match(/\./)) {
        result = "";
      }
      mutateDom(() => bind(el, value, result, modifiers));
    }));
  });
  function storeKeyForXFor(el, expression) {
    el._x_keyExpression = expression;
  }
  addRootSelector(() => `[${prefix("data")}]`);
  directive("data", skipDuringClone((el, { expression }, { cleanup: cleanup2 }) => {
    expression = expression === "" ? "{}" : expression;
    let magicContext = {};
    injectMagics(magicContext, el);
    let dataProviderContext = {};
    injectDataProviders(dataProviderContext, magicContext);
    let data2 = evaluate(el, expression, { scope: dataProviderContext });
    if (data2 === void 0 || data2 === true)
      data2 = {};
    injectMagics(data2, el);
    let reactiveData = reactive(data2);
    initInterceptors(reactiveData);
    let undo = addScopeToNode(el, reactiveData);
    reactiveData["init"] && evaluate(el, reactiveData["init"]);
    cleanup2(() => {
      reactiveData["destroy"] && evaluate(el, reactiveData["destroy"]);
      undo();
    });
  }));
  directive("show", (el, { modifiers, expression }, { effect: effect3 }) => {
    let evaluate2 = evaluateLater(el, expression);
    if (!el._x_doHide)
      el._x_doHide = () => {
        mutateDom(() => {
          el.style.setProperty("display", "none", modifiers.includes("important") ? "important" : void 0);
        });
      };
    if (!el._x_doShow)
      el._x_doShow = () => {
        mutateDom(() => {
          if (el.style.length === 1 && el.style.display === "none") {
            el.removeAttribute("style");
          } else {
            el.style.removeProperty("display");
          }
        });
      };
    let hide = () => {
      el._x_doHide();
      el._x_isShown = false;
    };
    let show = () => {
      el._x_doShow();
      el._x_isShown = true;
    };
    let clickAwayCompatibleShow = () => setTimeout(show);
    let toggle = once((value) => value ? show() : hide(), (value) => {
      if (typeof el._x_toggleAndCascadeWithTransitions === "function") {
        el._x_toggleAndCascadeWithTransitions(el, value, show, hide);
      } else {
        value ? clickAwayCompatibleShow() : hide();
      }
    });
    let oldValue;
    let firstTime = true;
    effect3(() => evaluate2((value) => {
      if (!firstTime && value === oldValue)
        return;
      if (modifiers.includes("immediate"))
        value ? clickAwayCompatibleShow() : hide();
      toggle(value);
      oldValue = value;
      firstTime = false;
    }));
  });
  directive("for", (el, { expression }, { effect: effect3, cleanup: cleanup2 }) => {
    let iteratorNames = parseForExpression(expression);
    let evaluateItems = evaluateLater(el, iteratorNames.items);
    let evaluateKey = evaluateLater(el, el._x_keyExpression || "index");
    el._x_prevKeys = [];
    el._x_lookup = {};
    effect3(() => loop(el, iteratorNames, evaluateItems, evaluateKey));
    cleanup2(() => {
      Object.values(el._x_lookup).forEach((el2) => el2.remove());
      delete el._x_prevKeys;
      delete el._x_lookup;
    });
  });
  function loop(el, iteratorNames, evaluateItems, evaluateKey) {
    let isObject2 = (i) => typeof i === "object" && !Array.isArray(i);
    let templateEl = el;
    evaluateItems((items) => {
      if (isNumeric3(items) && items >= 0) {
        items = Array.from(Array(items).keys(), (i) => i + 1);
      }
      if (items === void 0)
        items = [];
      let lookup = el._x_lookup;
      let prevKeys = el._x_prevKeys;
      let scopes = [];
      let keys = [];
      if (isObject2(items)) {
        items = Object.entries(items).map(([key, value]) => {
          let scope2 = getIterationScopeVariables(iteratorNames, value, key, items);
          evaluateKey((value2) => keys.push(value2), { scope: { index: key, ...scope2 } });
          scopes.push(scope2);
        });
      } else {
        for (let i = 0; i < items.length; i++) {
          let scope2 = getIterationScopeVariables(iteratorNames, items[i], i, items);
          evaluateKey((value) => keys.push(value), { scope: { index: i, ...scope2 } });
          scopes.push(scope2);
        }
      }
      let adds = [];
      let moves = [];
      let removes = [];
      let sames = [];
      for (let i = 0; i < prevKeys.length; i++) {
        let key = prevKeys[i];
        if (keys.indexOf(key) === -1)
          removes.push(key);
      }
      prevKeys = prevKeys.filter((key) => !removes.includes(key));
      let lastKey = "template";
      for (let i = 0; i < keys.length; i++) {
        let key = keys[i];
        let prevIndex = prevKeys.indexOf(key);
        if (prevIndex === -1) {
          prevKeys.splice(i, 0, key);
          adds.push([lastKey, i]);
        } else if (prevIndex !== i) {
          let keyInSpot = prevKeys.splice(i, 1)[0];
          let keyForSpot = prevKeys.splice(prevIndex - 1, 1)[0];
          prevKeys.splice(i, 0, keyForSpot);
          prevKeys.splice(prevIndex, 0, keyInSpot);
          moves.push([keyInSpot, keyForSpot]);
        } else {
          sames.push(key);
        }
        lastKey = key;
      }
      for (let i = 0; i < removes.length; i++) {
        let key = removes[i];
        if (!!lookup[key]._x_effects) {
          lookup[key]._x_effects.forEach(dequeueJob);
        }
        lookup[key].remove();
        lookup[key] = null;
        delete lookup[key];
      }
      for (let i = 0; i < moves.length; i++) {
        let [keyInSpot, keyForSpot] = moves[i];
        let elInSpot = lookup[keyInSpot];
        let elForSpot = lookup[keyForSpot];
        let marker = document.createElement("div");
        mutateDom(() => {
          if (!elForSpot)
            warn(`x-for ":key" is undefined or invalid`, templateEl);
          elForSpot.after(marker);
          elInSpot.after(elForSpot);
          elForSpot._x_currentIfEl && elForSpot.after(elForSpot._x_currentIfEl);
          marker.before(elInSpot);
          elInSpot._x_currentIfEl && elInSpot.after(elInSpot._x_currentIfEl);
          marker.remove();
        });
        elForSpot._x_refreshXForScope(scopes[keys.indexOf(keyForSpot)]);
      }
      for (let i = 0; i < adds.length; i++) {
        let [lastKey2, index] = adds[i];
        let lastEl = lastKey2 === "template" ? templateEl : lookup[lastKey2];
        if (lastEl._x_currentIfEl)
          lastEl = lastEl._x_currentIfEl;
        let scope2 = scopes[index];
        let key = keys[index];
        let clone2 = document.importNode(templateEl.content, true).firstElementChild;
        let reactiveScope = reactive(scope2);
        addScopeToNode(clone2, reactiveScope, templateEl);
        clone2._x_refreshXForScope = (newScope) => {
          Object.entries(newScope).forEach(([key2, value]) => {
            reactiveScope[key2] = value;
          });
        };
        mutateDom(() => {
          lastEl.after(clone2);
          initTree(clone2);
        });
        if (typeof key === "object") {
          warn("x-for key cannot be an object, it must be a string or an integer", templateEl);
        }
        lookup[key] = clone2;
      }
      for (let i = 0; i < sames.length; i++) {
        lookup[sames[i]]._x_refreshXForScope(scopes[keys.indexOf(sames[i])]);
      }
      templateEl._x_prevKeys = keys;
    });
  }
  function parseForExpression(expression) {
    let forIteratorRE = /,([^,\}\]]*)(?:,([^,\}\]]*))?$/;
    let stripParensRE = /^\s*\(|\)\s*$/g;
    let forAliasRE = /([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/;
    let inMatch = expression.match(forAliasRE);
    if (!inMatch)
      return;
    let res = {};
    res.items = inMatch[2].trim();
    let item = inMatch[1].replace(stripParensRE, "").trim();
    let iteratorMatch = item.match(forIteratorRE);
    if (iteratorMatch) {
      res.item = item.replace(forIteratorRE, "").trim();
      res.index = iteratorMatch[1].trim();
      if (iteratorMatch[2]) {
        res.collection = iteratorMatch[2].trim();
      }
    } else {
      res.item = item;
    }
    return res;
  }
  function getIterationScopeVariables(iteratorNames, item, index, items) {
    let scopeVariables = {};
    if (/^\[.*\]$/.test(iteratorNames.item) && Array.isArray(item)) {
      let names = iteratorNames.item.replace("[", "").replace("]", "").split(",").map((i) => i.trim());
      names.forEach((name, i) => {
        scopeVariables[name] = item[i];
      });
    } else if (/^\{.*\}$/.test(iteratorNames.item) && !Array.isArray(item) && typeof item === "object") {
      let names = iteratorNames.item.replace("{", "").replace("}", "").split(",").map((i) => i.trim());
      names.forEach((name) => {
        scopeVariables[name] = item[name];
      });
    } else {
      scopeVariables[iteratorNames.item] = item;
    }
    if (iteratorNames.index)
      scopeVariables[iteratorNames.index] = index;
    if (iteratorNames.collection)
      scopeVariables[iteratorNames.collection] = items;
    return scopeVariables;
  }
  function isNumeric3(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
  }
  function handler2() {
  }
  handler2.inline = (el, { expression }, { cleanup: cleanup2 }) => {
    let root = closestRoot(el);
    if (!root._x_refs)
      root._x_refs = {};
    root._x_refs[expression] = el;
    cleanup2(() => delete root._x_refs[expression]);
  };
  directive("ref", handler2);
  directive("if", (el, { expression }, { effect: effect3, cleanup: cleanup2 }) => {
    let evaluate2 = evaluateLater(el, expression);
    let show = () => {
      if (el._x_currentIfEl)
        return el._x_currentIfEl;
      let clone2 = el.content.cloneNode(true).firstElementChild;
      addScopeToNode(clone2, {}, el);
      mutateDom(() => {
        el.after(clone2);
        initTree(clone2);
      });
      el._x_currentIfEl = clone2;
      el._x_undoIf = () => {
        walk(clone2, (node) => {
          if (!!node._x_effects) {
            node._x_effects.forEach(dequeueJob);
          }
        });
        clone2.remove();
        delete el._x_currentIfEl;
      };
      return clone2;
    };
    let hide = () => {
      if (!el._x_undoIf)
        return;
      el._x_undoIf();
      delete el._x_undoIf;
    };
    effect3(() => evaluate2((value) => {
      value ? show() : hide();
    }));
    cleanup2(() => el._x_undoIf && el._x_undoIf());
  });
  directive("id", (el, { expression }, { evaluate: evaluate2 }) => {
    let names = evaluate2(expression);
    names.forEach((name) => setIdRoot(el, name));
  });
  mapAttributes(startingWith("@", into(prefix("on:"))));
  directive("on", skipDuringClone((el, { value, modifiers, expression }, { cleanup: cleanup2 }) => {
    let evaluate2 = expression ? evaluateLater(el, expression) : () => {
    };
    if (el.tagName.toLowerCase() === "template") {
      if (!el._x_forwardEvents)
        el._x_forwardEvents = [];
      if (!el._x_forwardEvents.includes(value))
        el._x_forwardEvents.push(value);
    }
    let removeListener = on(el, value, modifiers, (e) => {
      evaluate2(() => {
      }, { scope: { $event: e }, params: [e] });
    });
    cleanup2(() => removeListener());
  }));
  warnMissingPluginDirective("Collapse", "collapse", "collapse");
  warnMissingPluginDirective("Intersect", "intersect", "intersect");
  warnMissingPluginDirective("Focus", "trap", "focus");
  warnMissingPluginDirective("Mask", "mask", "mask");
  function warnMissingPluginDirective(name, directiveName2, slug) {
    directive(directiveName2, (el) => warn(`You can't use [x-${directiveName2}] without first installing the "${name}" plugin here: https://alpinejs.dev/plugins/${slug}`, el));
  }
  alpine_default.setEvaluator(normalEvaluator);
  alpine_default.setReactivityEngine({ reactive: reactive2, effect: effect2, release: stop, raw: toRaw });
  var src_default = alpine_default;
  var module_default = src_default;

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
  var src_default2 = (Alpine2) => {
    Alpine2.directive("mousetrap", (el, { modifiers, expression }, { evaluate: evaluate2 }) => {
      const action = () => expression ? evaluate2(expression) : el.click();
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
  var module_default2 = src_default2;

  // node_modules/@alpinejs/persist/dist/module.esm.js
  function src_default3(Alpine2) {
    let persist = () => {
      let alias;
      let storage = localStorage;
      return Alpine2.interceptor((initialValue, getter, setter, path, key) => {
        let lookup = alias || `_x_${path}`;
        let initial = storageHas(lookup, storage) ? storageGet(lookup, storage) : initialValue;
        setter(initial);
        Alpine2.effect(() => {
          let value = getter();
          storageSet(lookup, value, storage);
          setter(value);
        });
        return initial;
      }, (func) => {
        func.as = (key) => {
          alias = key;
          return func;
        }, func.using = (target) => {
          storage = target;
          return func;
        };
      });
    };
    Object.defineProperty(Alpine2, "$persist", { get: () => persist() });
    Alpine2.magic("persist", persist);
    Alpine2.persist = (key, { get: get3, set: set3 }, storage = localStorage) => {
      let initial = storageHas(key, storage) ? storageGet(key, storage) : get3();
      set3(initial);
      Alpine2.effect(() => {
        let value = get3();
        storageSet(key, value, storage);
        set3(value);
      });
    };
  }
  function storageHas(key, storage) {
    return storage.getItem(key) !== null;
  }
  function storageGet(key, storage) {
    return JSON.parse(storage.getItem(key, storage));
  }
  function storageSet(key, value, storage) {
    storage.setItem(key, JSON.stringify(value));
  }
  var module_default3 = src_default3;

  // node_modules/@ryangjchandler/alpine-tooltip/dist/module.esm.js
  var __create2 = Object.create;
  var __defProp2 = Object.defineProperty;
  var __getProtoOf2 = Object.getPrototypeOf;
  var __hasOwnProp2 = Object.prototype.hasOwnProperty;
  var __getOwnPropNames2 = Object.getOwnPropertyNames;
  var __getOwnPropDesc2 = Object.getOwnPropertyDescriptor;
  var __markAsModule2 = (target) => __defProp2(target, "__esModule", { value: true });
  var __commonJS2 = (callback, module) => () => {
    if (!module) {
      module = { exports: {} };
      callback(module.exports, module);
    }
    return module.exports;
  };
  var __exportStar2 = (target, module, desc) => {
    if (module && typeof module === "object" || typeof module === "function") {
      for (let key of __getOwnPropNames2(module))
        if (!__hasOwnProp2.call(target, key) && key !== "default")
          __defProp2(target, key, { get: () => module[key], enumerable: !(desc = __getOwnPropDesc2(module, key)) || desc.enumerable });
    }
    return target;
  };
  var __toModule2 = (module) => {
    return __exportStar2(__markAsModule2(__defProp2(module != null ? __create2(__getProtoOf2(module)) : {}, "default", module && module.__esModule && "default" in module ? { get: () => module.default, enumerable: true } : { value: module, enumerable: true })), module);
  };
  var require_popper = __commonJS2((exports) => {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    function getBoundingClientRect(element) {
      var rect = element.getBoundingClientRect();
      return {
        width: rect.width,
        height: rect.height,
        top: rect.top,
        right: rect.right,
        bottom: rect.bottom,
        left: rect.left,
        x: rect.left,
        y: rect.top
      };
    }
    function getWindow(node) {
      if (node == null) {
        return window;
      }
      if (node.toString() !== "[object Window]") {
        var ownerDocument = node.ownerDocument;
        return ownerDocument ? ownerDocument.defaultView || window : window;
      }
      return node;
    }
    function getWindowScroll(node) {
      var win = getWindow(node);
      var scrollLeft = win.pageXOffset;
      var scrollTop = win.pageYOffset;
      return {
        scrollLeft,
        scrollTop
      };
    }
    function isElement(node) {
      var OwnElement = getWindow(node).Element;
      return node instanceof OwnElement || node instanceof Element;
    }
    function isHTMLElement(node) {
      var OwnElement = getWindow(node).HTMLElement;
      return node instanceof OwnElement || node instanceof HTMLElement;
    }
    function isShadowRoot(node) {
      if (typeof ShadowRoot === "undefined") {
        return false;
      }
      var OwnElement = getWindow(node).ShadowRoot;
      return node instanceof OwnElement || node instanceof ShadowRoot;
    }
    function getHTMLElementScroll(element) {
      return {
        scrollLeft: element.scrollLeft,
        scrollTop: element.scrollTop
      };
    }
    function getNodeScroll(node) {
      if (node === getWindow(node) || !isHTMLElement(node)) {
        return getWindowScroll(node);
      } else {
        return getHTMLElementScroll(node);
      }
    }
    function getNodeName(element) {
      return element ? (element.nodeName || "").toLowerCase() : null;
    }
    function getDocumentElement(element) {
      return ((isElement(element) ? element.ownerDocument : element.document) || window.document).documentElement;
    }
    function getWindowScrollBarX(element) {
      return getBoundingClientRect(getDocumentElement(element)).left + getWindowScroll(element).scrollLeft;
    }
    function getComputedStyle2(element) {
      return getWindow(element).getComputedStyle(element);
    }
    function isScrollParent(element) {
      var _getComputedStyle = getComputedStyle2(element), overflow = _getComputedStyle.overflow, overflowX = _getComputedStyle.overflowX, overflowY = _getComputedStyle.overflowY;
      return /auto|scroll|overlay|hidden/.test(overflow + overflowY + overflowX);
    }
    function getCompositeRect(elementOrVirtualElement, offsetParent, isFixed) {
      if (isFixed === void 0) {
        isFixed = false;
      }
      var documentElement = getDocumentElement(offsetParent);
      var rect = getBoundingClientRect(elementOrVirtualElement);
      var isOffsetParentAnElement = isHTMLElement(offsetParent);
      var scroll = {
        scrollLeft: 0,
        scrollTop: 0
      };
      var offsets = {
        x: 0,
        y: 0
      };
      if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
        if (getNodeName(offsetParent) !== "body" || isScrollParent(documentElement)) {
          scroll = getNodeScroll(offsetParent);
        }
        if (isHTMLElement(offsetParent)) {
          offsets = getBoundingClientRect(offsetParent);
          offsets.x += offsetParent.clientLeft;
          offsets.y += offsetParent.clientTop;
        } else if (documentElement) {
          offsets.x = getWindowScrollBarX(documentElement);
        }
      }
      return {
        x: rect.left + scroll.scrollLeft - offsets.x,
        y: rect.top + scroll.scrollTop - offsets.y,
        width: rect.width,
        height: rect.height
      };
    }
    function getLayoutRect(element) {
      var clientRect = getBoundingClientRect(element);
      var width = element.offsetWidth;
      var height = element.offsetHeight;
      if (Math.abs(clientRect.width - width) <= 1) {
        width = clientRect.width;
      }
      if (Math.abs(clientRect.height - height) <= 1) {
        height = clientRect.height;
      }
      return {
        x: element.offsetLeft,
        y: element.offsetTop,
        width,
        height
      };
    }
    function getParentNode(element) {
      if (getNodeName(element) === "html") {
        return element;
      }
      return element.assignedSlot || element.parentNode || (isShadowRoot(element) ? element.host : null) || getDocumentElement(element);
    }
    function getScrollParent(node) {
      if (["html", "body", "#document"].indexOf(getNodeName(node)) >= 0) {
        return node.ownerDocument.body;
      }
      if (isHTMLElement(node) && isScrollParent(node)) {
        return node;
      }
      return getScrollParent(getParentNode(node));
    }
    function listScrollParents(element, list) {
      var _element$ownerDocumen;
      if (list === void 0) {
        list = [];
      }
      var scrollParent = getScrollParent(element);
      var isBody = scrollParent === ((_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body);
      var win = getWindow(scrollParent);
      var target = isBody ? [win].concat(win.visualViewport || [], isScrollParent(scrollParent) ? scrollParent : []) : scrollParent;
      var updatedList = list.concat(target);
      return isBody ? updatedList : updatedList.concat(listScrollParents(getParentNode(target)));
    }
    function isTableElement(element) {
      return ["table", "td", "th"].indexOf(getNodeName(element)) >= 0;
    }
    function getTrueOffsetParent(element) {
      if (!isHTMLElement(element) || getComputedStyle2(element).position === "fixed") {
        return null;
      }
      return element.offsetParent;
    }
    function getContainingBlock(element) {
      var isFirefox = navigator.userAgent.toLowerCase().indexOf("firefox") !== -1;
      var isIE = navigator.userAgent.indexOf("Trident") !== -1;
      if (isIE && isHTMLElement(element)) {
        var elementCss = getComputedStyle2(element);
        if (elementCss.position === "fixed") {
          return null;
        }
      }
      var currentNode = getParentNode(element);
      while (isHTMLElement(currentNode) && ["html", "body"].indexOf(getNodeName(currentNode)) < 0) {
        var css = getComputedStyle2(currentNode);
        if (css.transform !== "none" || css.perspective !== "none" || css.contain === "paint" || ["transform", "perspective"].indexOf(css.willChange) !== -1 || isFirefox && css.willChange === "filter" || isFirefox && css.filter && css.filter !== "none") {
          return currentNode;
        } else {
          currentNode = currentNode.parentNode;
        }
      }
      return null;
    }
    function getOffsetParent(element) {
      var window2 = getWindow(element);
      var offsetParent = getTrueOffsetParent(element);
      while (offsetParent && isTableElement(offsetParent) && getComputedStyle2(offsetParent).position === "static") {
        offsetParent = getTrueOffsetParent(offsetParent);
      }
      if (offsetParent && (getNodeName(offsetParent) === "html" || getNodeName(offsetParent) === "body" && getComputedStyle2(offsetParent).position === "static")) {
        return window2;
      }
      return offsetParent || getContainingBlock(element) || window2;
    }
    var top = "top";
    var bottom = "bottom";
    var right = "right";
    var left = "left";
    var auto = "auto";
    var basePlacements = [top, bottom, right, left];
    var start2 = "start";
    var end = "end";
    var clippingParents = "clippingParents";
    var viewport = "viewport";
    var popper = "popper";
    var reference = "reference";
    var variationPlacements = /* @__PURE__ */ basePlacements.reduce(function(acc, placement) {
      return acc.concat([placement + "-" + start2, placement + "-" + end]);
    }, []);
    var placements = /* @__PURE__ */ [].concat(basePlacements, [auto]).reduce(function(acc, placement) {
      return acc.concat([placement, placement + "-" + start2, placement + "-" + end]);
    }, []);
    var beforeRead = "beforeRead";
    var read = "read";
    var afterRead = "afterRead";
    var beforeMain = "beforeMain";
    var main = "main";
    var afterMain = "afterMain";
    var beforeWrite = "beforeWrite";
    var write = "write";
    var afterWrite = "afterWrite";
    var modifierPhases = [beforeRead, read, afterRead, beforeMain, main, afterMain, beforeWrite, write, afterWrite];
    function order(modifiers) {
      var map = /* @__PURE__ */ new Map();
      var visited = /* @__PURE__ */ new Set();
      var result = [];
      modifiers.forEach(function(modifier) {
        map.set(modifier.name, modifier);
      });
      function sort(modifier) {
        visited.add(modifier.name);
        var requires = [].concat(modifier.requires || [], modifier.requiresIfExists || []);
        requires.forEach(function(dep) {
          if (!visited.has(dep)) {
            var depModifier = map.get(dep);
            if (depModifier) {
              sort(depModifier);
            }
          }
        });
        result.push(modifier);
      }
      modifiers.forEach(function(modifier) {
        if (!visited.has(modifier.name)) {
          sort(modifier);
        }
      });
      return result;
    }
    function orderModifiers(modifiers) {
      var orderedModifiers = order(modifiers);
      return modifierPhases.reduce(function(acc, phase) {
        return acc.concat(orderedModifiers.filter(function(modifier) {
          return modifier.phase === phase;
        }));
      }, []);
    }
    function debounce2(fn) {
      var pending;
      return function() {
        if (!pending) {
          pending = new Promise(function(resolve) {
            Promise.resolve().then(function() {
              pending = void 0;
              resolve(fn());
            });
          });
        }
        return pending;
      };
    }
    function format(str) {
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }
      return [].concat(args).reduce(function(p, c) {
        return p.replace(/%s/, c);
      }, str);
    }
    var INVALID_MODIFIER_ERROR = 'Popper: modifier "%s" provided an invalid %s property, expected %s but got %s';
    var MISSING_DEPENDENCY_ERROR = 'Popper: modifier "%s" requires "%s", but "%s" modifier is not available';
    var VALID_PROPERTIES = ["name", "enabled", "phase", "fn", "effect", "requires", "options"];
    function validateModifiers(modifiers) {
      modifiers.forEach(function(modifier) {
        Object.keys(modifier).forEach(function(key) {
          switch (key) {
            case "name":
              if (typeof modifier.name !== "string") {
                console.error(format(INVALID_MODIFIER_ERROR, String(modifier.name), '"name"', '"string"', '"' + String(modifier.name) + '"'));
              }
              break;
            case "enabled":
              if (typeof modifier.enabled !== "boolean") {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"enabled"', '"boolean"', '"' + String(modifier.enabled) + '"'));
              }
            case "phase":
              if (modifierPhases.indexOf(modifier.phase) < 0) {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"phase"', "either " + modifierPhases.join(", "), '"' + String(modifier.phase) + '"'));
              }
              break;
            case "fn":
              if (typeof modifier.fn !== "function") {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"fn"', '"function"', '"' + String(modifier.fn) + '"'));
              }
              break;
            case "effect":
              if (typeof modifier.effect !== "function") {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"effect"', '"function"', '"' + String(modifier.fn) + '"'));
              }
              break;
            case "requires":
              if (!Array.isArray(modifier.requires)) {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"requires"', '"array"', '"' + String(modifier.requires) + '"'));
              }
              break;
            case "requiresIfExists":
              if (!Array.isArray(modifier.requiresIfExists)) {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"requiresIfExists"', '"array"', '"' + String(modifier.requiresIfExists) + '"'));
              }
              break;
            case "options":
            case "data":
              break;
            default:
              console.error('PopperJS: an invalid property has been provided to the "' + modifier.name + '" modifier, valid properties are ' + VALID_PROPERTIES.map(function(s) {
                return '"' + s + '"';
              }).join(", ") + '; but "' + key + '" was provided.');
          }
          modifier.requires && modifier.requires.forEach(function(requirement) {
            if (modifiers.find(function(mod) {
              return mod.name === requirement;
            }) == null) {
              console.error(format(MISSING_DEPENDENCY_ERROR, String(modifier.name), requirement, requirement));
            }
          });
        });
      });
    }
    function uniqueBy(arr, fn) {
      var identifiers = /* @__PURE__ */ new Set();
      return arr.filter(function(item) {
        var identifier = fn(item);
        if (!identifiers.has(identifier)) {
          identifiers.add(identifier);
          return true;
        }
      });
    }
    function getBasePlacement(placement) {
      return placement.split("-")[0];
    }
    function mergeByName(modifiers) {
      var merged = modifiers.reduce(function(merged2, current) {
        var existing = merged2[current.name];
        merged2[current.name] = existing ? Object.assign({}, existing, current, {
          options: Object.assign({}, existing.options, current.options),
          data: Object.assign({}, existing.data, current.data)
        }) : current;
        return merged2;
      }, {});
      return Object.keys(merged).map(function(key) {
        return merged[key];
      });
    }
    function getViewportRect(element) {
      var win = getWindow(element);
      var html = getDocumentElement(element);
      var visualViewport = win.visualViewport;
      var width = html.clientWidth;
      var height = html.clientHeight;
      var x = 0;
      var y = 0;
      if (visualViewport) {
        width = visualViewport.width;
        height = visualViewport.height;
        if (!/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
          x = visualViewport.offsetLeft;
          y = visualViewport.offsetTop;
        }
      }
      return {
        width,
        height,
        x: x + getWindowScrollBarX(element),
        y
      };
    }
    var max = Math.max;
    var min = Math.min;
    var round = Math.round;
    function getDocumentRect(element) {
      var _element$ownerDocumen;
      var html = getDocumentElement(element);
      var winScroll = getWindowScroll(element);
      var body = (_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body;
      var width = max(html.scrollWidth, html.clientWidth, body ? body.scrollWidth : 0, body ? body.clientWidth : 0);
      var height = max(html.scrollHeight, html.clientHeight, body ? body.scrollHeight : 0, body ? body.clientHeight : 0);
      var x = -winScroll.scrollLeft + getWindowScrollBarX(element);
      var y = -winScroll.scrollTop;
      if (getComputedStyle2(body || html).direction === "rtl") {
        x += max(html.clientWidth, body ? body.clientWidth : 0) - width;
      }
      return {
        width,
        height,
        x,
        y
      };
    }
    function contains(parent, child) {
      var rootNode = child.getRootNode && child.getRootNode();
      if (parent.contains(child)) {
        return true;
      } else if (rootNode && isShadowRoot(rootNode)) {
        var next = child;
        do {
          if (next && parent.isSameNode(next)) {
            return true;
          }
          next = next.parentNode || next.host;
        } while (next);
      }
      return false;
    }
    function rectToClientRect(rect) {
      return Object.assign({}, rect, {
        left: rect.x,
        top: rect.y,
        right: rect.x + rect.width,
        bottom: rect.y + rect.height
      });
    }
    function getInnerBoundingClientRect(element) {
      var rect = getBoundingClientRect(element);
      rect.top = rect.top + element.clientTop;
      rect.left = rect.left + element.clientLeft;
      rect.bottom = rect.top + element.clientHeight;
      rect.right = rect.left + element.clientWidth;
      rect.width = element.clientWidth;
      rect.height = element.clientHeight;
      rect.x = rect.left;
      rect.y = rect.top;
      return rect;
    }
    function getClientRectFromMixedType(element, clippingParent) {
      return clippingParent === viewport ? rectToClientRect(getViewportRect(element)) : isHTMLElement(clippingParent) ? getInnerBoundingClientRect(clippingParent) : rectToClientRect(getDocumentRect(getDocumentElement(element)));
    }
    function getClippingParents(element) {
      var clippingParents2 = listScrollParents(getParentNode(element));
      var canEscapeClipping = ["absolute", "fixed"].indexOf(getComputedStyle2(element).position) >= 0;
      var clipperElement = canEscapeClipping && isHTMLElement(element) ? getOffsetParent(element) : element;
      if (!isElement(clipperElement)) {
        return [];
      }
      return clippingParents2.filter(function(clippingParent) {
        return isElement(clippingParent) && contains(clippingParent, clipperElement) && getNodeName(clippingParent) !== "body";
      });
    }
    function getClippingRect(element, boundary, rootBoundary) {
      var mainClippingParents = boundary === "clippingParents" ? getClippingParents(element) : [].concat(boundary);
      var clippingParents2 = [].concat(mainClippingParents, [rootBoundary]);
      var firstClippingParent = clippingParents2[0];
      var clippingRect = clippingParents2.reduce(function(accRect, clippingParent) {
        var rect = getClientRectFromMixedType(element, clippingParent);
        accRect.top = max(rect.top, accRect.top);
        accRect.right = min(rect.right, accRect.right);
        accRect.bottom = min(rect.bottom, accRect.bottom);
        accRect.left = max(rect.left, accRect.left);
        return accRect;
      }, getClientRectFromMixedType(element, firstClippingParent));
      clippingRect.width = clippingRect.right - clippingRect.left;
      clippingRect.height = clippingRect.bottom - clippingRect.top;
      clippingRect.x = clippingRect.left;
      clippingRect.y = clippingRect.top;
      return clippingRect;
    }
    function getVariation(placement) {
      return placement.split("-")[1];
    }
    function getMainAxisFromPlacement(placement) {
      return ["top", "bottom"].indexOf(placement) >= 0 ? "x" : "y";
    }
    function computeOffsets(_ref) {
      var reference2 = _ref.reference, element = _ref.element, placement = _ref.placement;
      var basePlacement = placement ? getBasePlacement(placement) : null;
      var variation = placement ? getVariation(placement) : null;
      var commonX = reference2.x + reference2.width / 2 - element.width / 2;
      var commonY = reference2.y + reference2.height / 2 - element.height / 2;
      var offsets;
      switch (basePlacement) {
        case top:
          offsets = {
            x: commonX,
            y: reference2.y - element.height
          };
          break;
        case bottom:
          offsets = {
            x: commonX,
            y: reference2.y + reference2.height
          };
          break;
        case right:
          offsets = {
            x: reference2.x + reference2.width,
            y: commonY
          };
          break;
        case left:
          offsets = {
            x: reference2.x - element.width,
            y: commonY
          };
          break;
        default:
          offsets = {
            x: reference2.x,
            y: reference2.y
          };
      }
      var mainAxis = basePlacement ? getMainAxisFromPlacement(basePlacement) : null;
      if (mainAxis != null) {
        var len = mainAxis === "y" ? "height" : "width";
        switch (variation) {
          case start2:
            offsets[mainAxis] = offsets[mainAxis] - (reference2[len] / 2 - element[len] / 2);
            break;
          case end:
            offsets[mainAxis] = offsets[mainAxis] + (reference2[len] / 2 - element[len] / 2);
            break;
        }
      }
      return offsets;
    }
    function getFreshSideObject() {
      return {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
      };
    }
    function mergePaddingObject(paddingObject) {
      return Object.assign({}, getFreshSideObject(), paddingObject);
    }
    function expandToHashMap(value, keys) {
      return keys.reduce(function(hashMap, key) {
        hashMap[key] = value;
        return hashMap;
      }, {});
    }
    function detectOverflow(state, options) {
      if (options === void 0) {
        options = {};
      }
      var _options = options, _options$placement = _options.placement, placement = _options$placement === void 0 ? state.placement : _options$placement, _options$boundary = _options.boundary, boundary = _options$boundary === void 0 ? clippingParents : _options$boundary, _options$rootBoundary = _options.rootBoundary, rootBoundary = _options$rootBoundary === void 0 ? viewport : _options$rootBoundary, _options$elementConte = _options.elementContext, elementContext = _options$elementConte === void 0 ? popper : _options$elementConte, _options$altBoundary = _options.altBoundary, altBoundary = _options$altBoundary === void 0 ? false : _options$altBoundary, _options$padding = _options.padding, padding = _options$padding === void 0 ? 0 : _options$padding;
      var paddingObject = mergePaddingObject(typeof padding !== "number" ? padding : expandToHashMap(padding, basePlacements));
      var altContext = elementContext === popper ? reference : popper;
      var referenceElement = state.elements.reference;
      var popperRect = state.rects.popper;
      var element = state.elements[altBoundary ? altContext : elementContext];
      var clippingClientRect = getClippingRect(isElement(element) ? element : element.contextElement || getDocumentElement(state.elements.popper), boundary, rootBoundary);
      var referenceClientRect = getBoundingClientRect(referenceElement);
      var popperOffsets2 = computeOffsets({
        reference: referenceClientRect,
        element: popperRect,
        strategy: "absolute",
        placement
      });
      var popperClientRect = rectToClientRect(Object.assign({}, popperRect, popperOffsets2));
      var elementClientRect = elementContext === popper ? popperClientRect : referenceClientRect;
      var overflowOffsets = {
        top: clippingClientRect.top - elementClientRect.top + paddingObject.top,
        bottom: elementClientRect.bottom - clippingClientRect.bottom + paddingObject.bottom,
        left: clippingClientRect.left - elementClientRect.left + paddingObject.left,
        right: elementClientRect.right - clippingClientRect.right + paddingObject.right
      };
      var offsetData = state.modifiersData.offset;
      if (elementContext === popper && offsetData) {
        var offset2 = offsetData[placement];
        Object.keys(overflowOffsets).forEach(function(key) {
          var multiply = [right, bottom].indexOf(key) >= 0 ? 1 : -1;
          var axis = [top, bottom].indexOf(key) >= 0 ? "y" : "x";
          overflowOffsets[key] += offset2[axis] * multiply;
        });
      }
      return overflowOffsets;
    }
    var INVALID_ELEMENT_ERROR = "Popper: Invalid reference or popper argument provided. They must be either a DOM element or virtual element.";
    var INFINITE_LOOP_ERROR = "Popper: An infinite loop in the modifiers cycle has been detected! The cycle has been interrupted to prevent a browser crash.";
    var DEFAULT_OPTIONS = {
      placement: "bottom",
      modifiers: [],
      strategy: "absolute"
    };
    function areValidElements() {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      return !args.some(function(element) {
        return !(element && typeof element.getBoundingClientRect === "function");
      });
    }
    function popperGenerator(generatorOptions) {
      if (generatorOptions === void 0) {
        generatorOptions = {};
      }
      var _generatorOptions = generatorOptions, _generatorOptions$def = _generatorOptions.defaultModifiers, defaultModifiers2 = _generatorOptions$def === void 0 ? [] : _generatorOptions$def, _generatorOptions$def2 = _generatorOptions.defaultOptions, defaultOptions = _generatorOptions$def2 === void 0 ? DEFAULT_OPTIONS : _generatorOptions$def2;
      return function createPopper2(reference2, popper2, options) {
        if (options === void 0) {
          options = defaultOptions;
        }
        var state = {
          placement: "bottom",
          orderedModifiers: [],
          options: Object.assign({}, DEFAULT_OPTIONS, defaultOptions),
          modifiersData: {},
          elements: {
            reference: reference2,
            popper: popper2
          },
          attributes: {},
          styles: {}
        };
        var effectCleanupFns = [];
        var isDestroyed = false;
        var instance = {
          state,
          setOptions: function setOptions(options2) {
            cleanupModifierEffects();
            state.options = Object.assign({}, defaultOptions, state.options, options2);
            state.scrollParents = {
              reference: isElement(reference2) ? listScrollParents(reference2) : reference2.contextElement ? listScrollParents(reference2.contextElement) : [],
              popper: listScrollParents(popper2)
            };
            var orderedModifiers = orderModifiers(mergeByName([].concat(defaultModifiers2, state.options.modifiers)));
            state.orderedModifiers = orderedModifiers.filter(function(m) {
              return m.enabled;
            });
            if (true) {
              var modifiers = uniqueBy([].concat(orderedModifiers, state.options.modifiers), function(_ref) {
                var name = _ref.name;
                return name;
              });
              validateModifiers(modifiers);
              if (getBasePlacement(state.options.placement) === auto) {
                var flipModifier = state.orderedModifiers.find(function(_ref2) {
                  var name = _ref2.name;
                  return name === "flip";
                });
                if (!flipModifier) {
                  console.error(['Popper: "auto" placements require the "flip" modifier be', "present and enabled to work."].join(" "));
                }
              }
              var _getComputedStyle = getComputedStyle2(popper2), marginTop = _getComputedStyle.marginTop, marginRight = _getComputedStyle.marginRight, marginBottom = _getComputedStyle.marginBottom, marginLeft = _getComputedStyle.marginLeft;
              if ([marginTop, marginRight, marginBottom, marginLeft].some(function(margin) {
                return parseFloat(margin);
              })) {
                console.warn(['Popper: CSS "margin" styles cannot be used to apply padding', "between the popper and its reference element or boundary.", "To replicate margin, use the `offset` modifier, as well as", "the `padding` option in the `preventOverflow` and `flip`", "modifiers."].join(" "));
              }
            }
            runModifierEffects();
            return instance.update();
          },
          forceUpdate: function forceUpdate() {
            if (isDestroyed) {
              return;
            }
            var _state$elements = state.elements, reference3 = _state$elements.reference, popper3 = _state$elements.popper;
            if (!areValidElements(reference3, popper3)) {
              if (true) {
                console.error(INVALID_ELEMENT_ERROR);
              }
              return;
            }
            state.rects = {
              reference: getCompositeRect(reference3, getOffsetParent(popper3), state.options.strategy === "fixed"),
              popper: getLayoutRect(popper3)
            };
            state.reset = false;
            state.placement = state.options.placement;
            state.orderedModifiers.forEach(function(modifier) {
              return state.modifiersData[modifier.name] = Object.assign({}, modifier.data);
            });
            var __debug_loops__ = 0;
            for (var index = 0; index < state.orderedModifiers.length; index++) {
              if (true) {
                __debug_loops__ += 1;
                if (__debug_loops__ > 100) {
                  console.error(INFINITE_LOOP_ERROR);
                  break;
                }
              }
              if (state.reset === true) {
                state.reset = false;
                index = -1;
                continue;
              }
              var _state$orderedModifie = state.orderedModifiers[index], fn = _state$orderedModifie.fn, _state$orderedModifie2 = _state$orderedModifie.options, _options = _state$orderedModifie2 === void 0 ? {} : _state$orderedModifie2, name = _state$orderedModifie.name;
              if (typeof fn === "function") {
                state = fn({
                  state,
                  options: _options,
                  name,
                  instance
                }) || state;
              }
            }
          },
          update: debounce2(function() {
            return new Promise(function(resolve) {
              instance.forceUpdate();
              resolve(state);
            });
          }),
          destroy: function destroy() {
            cleanupModifierEffects();
            isDestroyed = true;
          }
        };
        if (!areValidElements(reference2, popper2)) {
          if (true) {
            console.error(INVALID_ELEMENT_ERROR);
          }
          return instance;
        }
        instance.setOptions(options).then(function(state2) {
          if (!isDestroyed && options.onFirstUpdate) {
            options.onFirstUpdate(state2);
          }
        });
        function runModifierEffects() {
          state.orderedModifiers.forEach(function(_ref3) {
            var name = _ref3.name, _ref3$options = _ref3.options, options2 = _ref3$options === void 0 ? {} : _ref3$options, effect22 = _ref3.effect;
            if (typeof effect22 === "function") {
              var cleanupFn = effect22({
                state,
                name,
                instance,
                options: options2
              });
              var noopFn = function noopFn2() {
              };
              effectCleanupFns.push(cleanupFn || noopFn);
            }
          });
        }
        function cleanupModifierEffects() {
          effectCleanupFns.forEach(function(fn) {
            return fn();
          });
          effectCleanupFns = [];
        }
        return instance;
      };
    }
    var passive = {
      passive: true
    };
    function effect$2(_ref) {
      var state = _ref.state, instance = _ref.instance, options = _ref.options;
      var _options$scroll = options.scroll, scroll = _options$scroll === void 0 ? true : _options$scroll, _options$resize = options.resize, resize = _options$resize === void 0 ? true : _options$resize;
      var window2 = getWindow(state.elements.popper);
      var scrollParents = [].concat(state.scrollParents.reference, state.scrollParents.popper);
      if (scroll) {
        scrollParents.forEach(function(scrollParent) {
          scrollParent.addEventListener("scroll", instance.update, passive);
        });
      }
      if (resize) {
        window2.addEventListener("resize", instance.update, passive);
      }
      return function() {
        if (scroll) {
          scrollParents.forEach(function(scrollParent) {
            scrollParent.removeEventListener("scroll", instance.update, passive);
          });
        }
        if (resize) {
          window2.removeEventListener("resize", instance.update, passive);
        }
      };
    }
    var eventListeners = {
      name: "eventListeners",
      enabled: true,
      phase: "write",
      fn: function fn() {
      },
      effect: effect$2,
      data: {}
    };
    function popperOffsets(_ref) {
      var state = _ref.state, name = _ref.name;
      state.modifiersData[name] = computeOffsets({
        reference: state.rects.reference,
        element: state.rects.popper,
        strategy: "absolute",
        placement: state.placement
      });
    }
    var popperOffsets$1 = {
      name: "popperOffsets",
      enabled: true,
      phase: "read",
      fn: popperOffsets,
      data: {}
    };
    var unsetSides = {
      top: "auto",
      right: "auto",
      bottom: "auto",
      left: "auto"
    };
    function roundOffsetsByDPR(_ref) {
      var x = _ref.x, y = _ref.y;
      var win = window;
      var dpr = win.devicePixelRatio || 1;
      return {
        x: round(round(x * dpr) / dpr) || 0,
        y: round(round(y * dpr) / dpr) || 0
      };
    }
    function mapToStyles(_ref2) {
      var _Object$assign2;
      var popper2 = _ref2.popper, popperRect = _ref2.popperRect, placement = _ref2.placement, offsets = _ref2.offsets, position = _ref2.position, gpuAcceleration = _ref2.gpuAcceleration, adaptive = _ref2.adaptive, roundOffsets = _ref2.roundOffsets;
      var _ref3 = roundOffsets === true ? roundOffsetsByDPR(offsets) : typeof roundOffsets === "function" ? roundOffsets(offsets) : offsets, _ref3$x = _ref3.x, x = _ref3$x === void 0 ? 0 : _ref3$x, _ref3$y = _ref3.y, y = _ref3$y === void 0 ? 0 : _ref3$y;
      var hasX = offsets.hasOwnProperty("x");
      var hasY = offsets.hasOwnProperty("y");
      var sideX = left;
      var sideY = top;
      var win = window;
      if (adaptive) {
        var offsetParent = getOffsetParent(popper2);
        var heightProp = "clientHeight";
        var widthProp = "clientWidth";
        if (offsetParent === getWindow(popper2)) {
          offsetParent = getDocumentElement(popper2);
          if (getComputedStyle2(offsetParent).position !== "static") {
            heightProp = "scrollHeight";
            widthProp = "scrollWidth";
          }
        }
        offsetParent = offsetParent;
        if (placement === top) {
          sideY = bottom;
          y -= offsetParent[heightProp] - popperRect.height;
          y *= gpuAcceleration ? 1 : -1;
        }
        if (placement === left) {
          sideX = right;
          x -= offsetParent[widthProp] - popperRect.width;
          x *= gpuAcceleration ? 1 : -1;
        }
      }
      var commonStyles = Object.assign({
        position
      }, adaptive && unsetSides);
      if (gpuAcceleration) {
        var _Object$assign;
        return Object.assign({}, commonStyles, (_Object$assign = {}, _Object$assign[sideY] = hasY ? "0" : "", _Object$assign[sideX] = hasX ? "0" : "", _Object$assign.transform = (win.devicePixelRatio || 1) < 2 ? "translate(" + x + "px, " + y + "px)" : "translate3d(" + x + "px, " + y + "px, 0)", _Object$assign));
      }
      return Object.assign({}, commonStyles, (_Object$assign2 = {}, _Object$assign2[sideY] = hasY ? y + "px" : "", _Object$assign2[sideX] = hasX ? x + "px" : "", _Object$assign2.transform = "", _Object$assign2));
    }
    function computeStyles(_ref4) {
      var state = _ref4.state, options = _ref4.options;
      var _options$gpuAccelerat = options.gpuAcceleration, gpuAcceleration = _options$gpuAccelerat === void 0 ? true : _options$gpuAccelerat, _options$adaptive = options.adaptive, adaptive = _options$adaptive === void 0 ? true : _options$adaptive, _options$roundOffsets = options.roundOffsets, roundOffsets = _options$roundOffsets === void 0 ? true : _options$roundOffsets;
      if (true) {
        var transitionProperty = getComputedStyle2(state.elements.popper).transitionProperty || "";
        if (adaptive && ["transform", "top", "right", "bottom", "left"].some(function(property) {
          return transitionProperty.indexOf(property) >= 0;
        })) {
          console.warn(["Popper: Detected CSS transitions on at least one of the following", 'CSS properties: "transform", "top", "right", "bottom", "left".', "\n\n", 'Disable the "computeStyles" modifier\'s `adaptive` option to allow', "for smooth transitions, or remove these properties from the CSS", "transition declaration on the popper element if only transitioning", "opacity or background-color for example.", "\n\n", "We recommend using the popper element as a wrapper around an inner", "element that can have any CSS property transitioned for animations."].join(" "));
        }
      }
      var commonStyles = {
        placement: getBasePlacement(state.placement),
        popper: state.elements.popper,
        popperRect: state.rects.popper,
        gpuAcceleration
      };
      if (state.modifiersData.popperOffsets != null) {
        state.styles.popper = Object.assign({}, state.styles.popper, mapToStyles(Object.assign({}, commonStyles, {
          offsets: state.modifiersData.popperOffsets,
          position: state.options.strategy,
          adaptive,
          roundOffsets
        })));
      }
      if (state.modifiersData.arrow != null) {
        state.styles.arrow = Object.assign({}, state.styles.arrow, mapToStyles(Object.assign({}, commonStyles, {
          offsets: state.modifiersData.arrow,
          position: "absolute",
          adaptive: false,
          roundOffsets
        })));
      }
      state.attributes.popper = Object.assign({}, state.attributes.popper, {
        "data-popper-placement": state.placement
      });
    }
    var computeStyles$1 = {
      name: "computeStyles",
      enabled: true,
      phase: "beforeWrite",
      fn: computeStyles,
      data: {}
    };
    function applyStyles(_ref) {
      var state = _ref.state;
      Object.keys(state.elements).forEach(function(name) {
        var style = state.styles[name] || {};
        var attributes = state.attributes[name] || {};
        var element = state.elements[name];
        if (!isHTMLElement(element) || !getNodeName(element)) {
          return;
        }
        Object.assign(element.style, style);
        Object.keys(attributes).forEach(function(name2) {
          var value = attributes[name2];
          if (value === false) {
            element.removeAttribute(name2);
          } else {
            element.setAttribute(name2, value === true ? "" : value);
          }
        });
      });
    }
    function effect$1(_ref2) {
      var state = _ref2.state;
      var initialStyles = {
        popper: {
          position: state.options.strategy,
          left: "0",
          top: "0",
          margin: "0"
        },
        arrow: {
          position: "absolute"
        },
        reference: {}
      };
      Object.assign(state.elements.popper.style, initialStyles.popper);
      state.styles = initialStyles;
      if (state.elements.arrow) {
        Object.assign(state.elements.arrow.style, initialStyles.arrow);
      }
      return function() {
        Object.keys(state.elements).forEach(function(name) {
          var element = state.elements[name];
          var attributes = state.attributes[name] || {};
          var styleProperties = Object.keys(state.styles.hasOwnProperty(name) ? state.styles[name] : initialStyles[name]);
          var style = styleProperties.reduce(function(style2, property) {
            style2[property] = "";
            return style2;
          }, {});
          if (!isHTMLElement(element) || !getNodeName(element)) {
            return;
          }
          Object.assign(element.style, style);
          Object.keys(attributes).forEach(function(attribute) {
            element.removeAttribute(attribute);
          });
        });
      };
    }
    var applyStyles$1 = {
      name: "applyStyles",
      enabled: true,
      phase: "write",
      fn: applyStyles,
      effect: effect$1,
      requires: ["computeStyles"]
    };
    function distanceAndSkiddingToXY(placement, rects, offset2) {
      var basePlacement = getBasePlacement(placement);
      var invertDistance = [left, top].indexOf(basePlacement) >= 0 ? -1 : 1;
      var _ref = typeof offset2 === "function" ? offset2(Object.assign({}, rects, {
        placement
      })) : offset2, skidding = _ref[0], distance = _ref[1];
      skidding = skidding || 0;
      distance = (distance || 0) * invertDistance;
      return [left, right].indexOf(basePlacement) >= 0 ? {
        x: distance,
        y: skidding
      } : {
        x: skidding,
        y: distance
      };
    }
    function offset(_ref2) {
      var state = _ref2.state, options = _ref2.options, name = _ref2.name;
      var _options$offset = options.offset, offset2 = _options$offset === void 0 ? [0, 0] : _options$offset;
      var data2 = placements.reduce(function(acc, placement) {
        acc[placement] = distanceAndSkiddingToXY(placement, state.rects, offset2);
        return acc;
      }, {});
      var _data$state$placement = data2[state.placement], x = _data$state$placement.x, y = _data$state$placement.y;
      if (state.modifiersData.popperOffsets != null) {
        state.modifiersData.popperOffsets.x += x;
        state.modifiersData.popperOffsets.y += y;
      }
      state.modifiersData[name] = data2;
    }
    var offset$1 = {
      name: "offset",
      enabled: true,
      phase: "main",
      requires: ["popperOffsets"],
      fn: offset
    };
    var hash$1 = {
      left: "right",
      right: "left",
      bottom: "top",
      top: "bottom"
    };
    function getOppositePlacement(placement) {
      return placement.replace(/left|right|bottom|top/g, function(matched) {
        return hash$1[matched];
      });
    }
    var hash = {
      start: "end",
      end: "start"
    };
    function getOppositeVariationPlacement(placement) {
      return placement.replace(/start|end/g, function(matched) {
        return hash[matched];
      });
    }
    function computeAutoPlacement(state, options) {
      if (options === void 0) {
        options = {};
      }
      var _options = options, placement = _options.placement, boundary = _options.boundary, rootBoundary = _options.rootBoundary, padding = _options.padding, flipVariations = _options.flipVariations, _options$allowedAutoP = _options.allowedAutoPlacements, allowedAutoPlacements = _options$allowedAutoP === void 0 ? placements : _options$allowedAutoP;
      var variation = getVariation(placement);
      var placements$1 = variation ? flipVariations ? variationPlacements : variationPlacements.filter(function(placement2) {
        return getVariation(placement2) === variation;
      }) : basePlacements;
      var allowedPlacements = placements$1.filter(function(placement2) {
        return allowedAutoPlacements.indexOf(placement2) >= 0;
      });
      if (allowedPlacements.length === 0) {
        allowedPlacements = placements$1;
        if (true) {
          console.error(["Popper: The `allowedAutoPlacements` option did not allow any", "placements. Ensure the `placement` option matches the variation", "of the allowed placements.", 'For example, "auto" cannot be used to allow "bottom-start".', 'Use "auto-start" instead.'].join(" "));
        }
      }
      var overflows = allowedPlacements.reduce(function(acc, placement2) {
        acc[placement2] = detectOverflow(state, {
          placement: placement2,
          boundary,
          rootBoundary,
          padding
        })[getBasePlacement(placement2)];
        return acc;
      }, {});
      return Object.keys(overflows).sort(function(a, b) {
        return overflows[a] - overflows[b];
      });
    }
    function getExpandedFallbackPlacements(placement) {
      if (getBasePlacement(placement) === auto) {
        return [];
      }
      var oppositePlacement = getOppositePlacement(placement);
      return [getOppositeVariationPlacement(placement), oppositePlacement, getOppositeVariationPlacement(oppositePlacement)];
    }
    function flip(_ref) {
      var state = _ref.state, options = _ref.options, name = _ref.name;
      if (state.modifiersData[name]._skip) {
        return;
      }
      var _options$mainAxis = options.mainAxis, checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis, _options$altAxis = options.altAxis, checkAltAxis = _options$altAxis === void 0 ? true : _options$altAxis, specifiedFallbackPlacements = options.fallbackPlacements, padding = options.padding, boundary = options.boundary, rootBoundary = options.rootBoundary, altBoundary = options.altBoundary, _options$flipVariatio = options.flipVariations, flipVariations = _options$flipVariatio === void 0 ? true : _options$flipVariatio, allowedAutoPlacements = options.allowedAutoPlacements;
      var preferredPlacement = state.options.placement;
      var basePlacement = getBasePlacement(preferredPlacement);
      var isBasePlacement = basePlacement === preferredPlacement;
      var fallbackPlacements = specifiedFallbackPlacements || (isBasePlacement || !flipVariations ? [getOppositePlacement(preferredPlacement)] : getExpandedFallbackPlacements(preferredPlacement));
      var placements2 = [preferredPlacement].concat(fallbackPlacements).reduce(function(acc, placement2) {
        return acc.concat(getBasePlacement(placement2) === auto ? computeAutoPlacement(state, {
          placement: placement2,
          boundary,
          rootBoundary,
          padding,
          flipVariations,
          allowedAutoPlacements
        }) : placement2);
      }, []);
      var referenceRect = state.rects.reference;
      var popperRect = state.rects.popper;
      var checksMap = /* @__PURE__ */ new Map();
      var makeFallbackChecks = true;
      var firstFittingPlacement = placements2[0];
      for (var i = 0; i < placements2.length; i++) {
        var placement = placements2[i];
        var _basePlacement = getBasePlacement(placement);
        var isStartVariation = getVariation(placement) === start2;
        var isVertical = [top, bottom].indexOf(_basePlacement) >= 0;
        var len = isVertical ? "width" : "height";
        var overflow = detectOverflow(state, {
          placement,
          boundary,
          rootBoundary,
          altBoundary,
          padding
        });
        var mainVariationSide = isVertical ? isStartVariation ? right : left : isStartVariation ? bottom : top;
        if (referenceRect[len] > popperRect[len]) {
          mainVariationSide = getOppositePlacement(mainVariationSide);
        }
        var altVariationSide = getOppositePlacement(mainVariationSide);
        var checks = [];
        if (checkMainAxis) {
          checks.push(overflow[_basePlacement] <= 0);
        }
        if (checkAltAxis) {
          checks.push(overflow[mainVariationSide] <= 0, overflow[altVariationSide] <= 0);
        }
        if (checks.every(function(check) {
          return check;
        })) {
          firstFittingPlacement = placement;
          makeFallbackChecks = false;
          break;
        }
        checksMap.set(placement, checks);
      }
      if (makeFallbackChecks) {
        var numberOfChecks = flipVariations ? 3 : 1;
        var _loop = function _loop2(_i2) {
          var fittingPlacement = placements2.find(function(placement2) {
            var checks2 = checksMap.get(placement2);
            if (checks2) {
              return checks2.slice(0, _i2).every(function(check) {
                return check;
              });
            }
          });
          if (fittingPlacement) {
            firstFittingPlacement = fittingPlacement;
            return "break";
          }
        };
        for (var _i = numberOfChecks; _i > 0; _i--) {
          var _ret = _loop(_i);
          if (_ret === "break")
            break;
        }
      }
      if (state.placement !== firstFittingPlacement) {
        state.modifiersData[name]._skip = true;
        state.placement = firstFittingPlacement;
        state.reset = true;
      }
    }
    var flip$1 = {
      name: "flip",
      enabled: true,
      phase: "main",
      fn: flip,
      requiresIfExists: ["offset"],
      data: {
        _skip: false
      }
    };
    function getAltAxis(axis) {
      return axis === "x" ? "y" : "x";
    }
    function within(min$1, value, max$1) {
      return max(min$1, min(value, max$1));
    }
    function preventOverflow(_ref) {
      var state = _ref.state, options = _ref.options, name = _ref.name;
      var _options$mainAxis = options.mainAxis, checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis, _options$altAxis = options.altAxis, checkAltAxis = _options$altAxis === void 0 ? false : _options$altAxis, boundary = options.boundary, rootBoundary = options.rootBoundary, altBoundary = options.altBoundary, padding = options.padding, _options$tether = options.tether, tether = _options$tether === void 0 ? true : _options$tether, _options$tetherOffset = options.tetherOffset, tetherOffset = _options$tetherOffset === void 0 ? 0 : _options$tetherOffset;
      var overflow = detectOverflow(state, {
        boundary,
        rootBoundary,
        padding,
        altBoundary
      });
      var basePlacement = getBasePlacement(state.placement);
      var variation = getVariation(state.placement);
      var isBasePlacement = !variation;
      var mainAxis = getMainAxisFromPlacement(basePlacement);
      var altAxis = getAltAxis(mainAxis);
      var popperOffsets2 = state.modifiersData.popperOffsets;
      var referenceRect = state.rects.reference;
      var popperRect = state.rects.popper;
      var tetherOffsetValue = typeof tetherOffset === "function" ? tetherOffset(Object.assign({}, state.rects, {
        placement: state.placement
      })) : tetherOffset;
      var data2 = {
        x: 0,
        y: 0
      };
      if (!popperOffsets2) {
        return;
      }
      if (checkMainAxis || checkAltAxis) {
        var mainSide = mainAxis === "y" ? top : left;
        var altSide = mainAxis === "y" ? bottom : right;
        var len = mainAxis === "y" ? "height" : "width";
        var offset2 = popperOffsets2[mainAxis];
        var min$1 = popperOffsets2[mainAxis] + overflow[mainSide];
        var max$1 = popperOffsets2[mainAxis] - overflow[altSide];
        var additive = tether ? -popperRect[len] / 2 : 0;
        var minLen = variation === start2 ? referenceRect[len] : popperRect[len];
        var maxLen = variation === start2 ? -popperRect[len] : -referenceRect[len];
        var arrowElement = state.elements.arrow;
        var arrowRect = tether && arrowElement ? getLayoutRect(arrowElement) : {
          width: 0,
          height: 0
        };
        var arrowPaddingObject = state.modifiersData["arrow#persistent"] ? state.modifiersData["arrow#persistent"].padding : getFreshSideObject();
        var arrowPaddingMin = arrowPaddingObject[mainSide];
        var arrowPaddingMax = arrowPaddingObject[altSide];
        var arrowLen = within(0, referenceRect[len], arrowRect[len]);
        var minOffset = isBasePlacement ? referenceRect[len] / 2 - additive - arrowLen - arrowPaddingMin - tetherOffsetValue : minLen - arrowLen - arrowPaddingMin - tetherOffsetValue;
        var maxOffset = isBasePlacement ? -referenceRect[len] / 2 + additive + arrowLen + arrowPaddingMax + tetherOffsetValue : maxLen + arrowLen + arrowPaddingMax + tetherOffsetValue;
        var arrowOffsetParent = state.elements.arrow && getOffsetParent(state.elements.arrow);
        var clientOffset = arrowOffsetParent ? mainAxis === "y" ? arrowOffsetParent.clientTop || 0 : arrowOffsetParent.clientLeft || 0 : 0;
        var offsetModifierValue = state.modifiersData.offset ? state.modifiersData.offset[state.placement][mainAxis] : 0;
        var tetherMin = popperOffsets2[mainAxis] + minOffset - offsetModifierValue - clientOffset;
        var tetherMax = popperOffsets2[mainAxis] + maxOffset - offsetModifierValue;
        if (checkMainAxis) {
          var preventedOffset = within(tether ? min(min$1, tetherMin) : min$1, offset2, tether ? max(max$1, tetherMax) : max$1);
          popperOffsets2[mainAxis] = preventedOffset;
          data2[mainAxis] = preventedOffset - offset2;
        }
        if (checkAltAxis) {
          var _mainSide = mainAxis === "x" ? top : left;
          var _altSide = mainAxis === "x" ? bottom : right;
          var _offset = popperOffsets2[altAxis];
          var _min = _offset + overflow[_mainSide];
          var _max = _offset - overflow[_altSide];
          var _preventedOffset = within(tether ? min(_min, tetherMin) : _min, _offset, tether ? max(_max, tetherMax) : _max);
          popperOffsets2[altAxis] = _preventedOffset;
          data2[altAxis] = _preventedOffset - _offset;
        }
      }
      state.modifiersData[name] = data2;
    }
    var preventOverflow$1 = {
      name: "preventOverflow",
      enabled: true,
      phase: "main",
      fn: preventOverflow,
      requiresIfExists: ["offset"]
    };
    var toPaddingObject = function toPaddingObject2(padding, state) {
      padding = typeof padding === "function" ? padding(Object.assign({}, state.rects, {
        placement: state.placement
      })) : padding;
      return mergePaddingObject(typeof padding !== "number" ? padding : expandToHashMap(padding, basePlacements));
    };
    function arrow(_ref) {
      var _state$modifiersData$;
      var state = _ref.state, name = _ref.name, options = _ref.options;
      var arrowElement = state.elements.arrow;
      var popperOffsets2 = state.modifiersData.popperOffsets;
      var basePlacement = getBasePlacement(state.placement);
      var axis = getMainAxisFromPlacement(basePlacement);
      var isVertical = [left, right].indexOf(basePlacement) >= 0;
      var len = isVertical ? "height" : "width";
      if (!arrowElement || !popperOffsets2) {
        return;
      }
      var paddingObject = toPaddingObject(options.padding, state);
      var arrowRect = getLayoutRect(arrowElement);
      var minProp = axis === "y" ? top : left;
      var maxProp = axis === "y" ? bottom : right;
      var endDiff = state.rects.reference[len] + state.rects.reference[axis] - popperOffsets2[axis] - state.rects.popper[len];
      var startDiff = popperOffsets2[axis] - state.rects.reference[axis];
      var arrowOffsetParent = getOffsetParent(arrowElement);
      var clientSize = arrowOffsetParent ? axis === "y" ? arrowOffsetParent.clientHeight || 0 : arrowOffsetParent.clientWidth || 0 : 0;
      var centerToReference = endDiff / 2 - startDiff / 2;
      var min2 = paddingObject[minProp];
      var max2 = clientSize - arrowRect[len] - paddingObject[maxProp];
      var center = clientSize / 2 - arrowRect[len] / 2 + centerToReference;
      var offset2 = within(min2, center, max2);
      var axisProp = axis;
      state.modifiersData[name] = (_state$modifiersData$ = {}, _state$modifiersData$[axisProp] = offset2, _state$modifiersData$.centerOffset = offset2 - center, _state$modifiersData$);
    }
    function effect3(_ref2) {
      var state = _ref2.state, options = _ref2.options;
      var _options$element = options.element, arrowElement = _options$element === void 0 ? "[data-popper-arrow]" : _options$element;
      if (arrowElement == null) {
        return;
      }
      if (typeof arrowElement === "string") {
        arrowElement = state.elements.popper.querySelector(arrowElement);
        if (!arrowElement) {
          return;
        }
      }
      if (true) {
        if (!isHTMLElement(arrowElement)) {
          console.error(['Popper: "arrow" element must be an HTMLElement (not an SVGElement).', "To use an SVG arrow, wrap it in an HTMLElement that will be used as", "the arrow."].join(" "));
        }
      }
      if (!contains(state.elements.popper, arrowElement)) {
        if (true) {
          console.error(['Popper: "arrow" modifier\'s `element` must be a child of the popper', "element."].join(" "));
        }
        return;
      }
      state.elements.arrow = arrowElement;
    }
    var arrow$1 = {
      name: "arrow",
      enabled: true,
      phase: "main",
      fn: arrow,
      effect: effect3,
      requires: ["popperOffsets"],
      requiresIfExists: ["preventOverflow"]
    };
    function getSideOffsets(overflow, rect, preventedOffsets) {
      if (preventedOffsets === void 0) {
        preventedOffsets = {
          x: 0,
          y: 0
        };
      }
      return {
        top: overflow.top - rect.height - preventedOffsets.y,
        right: overflow.right - rect.width + preventedOffsets.x,
        bottom: overflow.bottom - rect.height + preventedOffsets.y,
        left: overflow.left - rect.width - preventedOffsets.x
      };
    }
    function isAnySideFullyClipped(overflow) {
      return [top, right, bottom, left].some(function(side) {
        return overflow[side] >= 0;
      });
    }
    function hide(_ref) {
      var state = _ref.state, name = _ref.name;
      var referenceRect = state.rects.reference;
      var popperRect = state.rects.popper;
      var preventedOffsets = state.modifiersData.preventOverflow;
      var referenceOverflow = detectOverflow(state, {
        elementContext: "reference"
      });
      var popperAltOverflow = detectOverflow(state, {
        altBoundary: true
      });
      var referenceClippingOffsets = getSideOffsets(referenceOverflow, referenceRect);
      var popperEscapeOffsets = getSideOffsets(popperAltOverflow, popperRect, preventedOffsets);
      var isReferenceHidden = isAnySideFullyClipped(referenceClippingOffsets);
      var hasPopperEscaped = isAnySideFullyClipped(popperEscapeOffsets);
      state.modifiersData[name] = {
        referenceClippingOffsets,
        popperEscapeOffsets,
        isReferenceHidden,
        hasPopperEscaped
      };
      state.attributes.popper = Object.assign({}, state.attributes.popper, {
        "data-popper-reference-hidden": isReferenceHidden,
        "data-popper-escaped": hasPopperEscaped
      });
    }
    var hide$1 = {
      name: "hide",
      enabled: true,
      phase: "main",
      requiresIfExists: ["preventOverflow"],
      fn: hide
    };
    var defaultModifiers$1 = [eventListeners, popperOffsets$1, computeStyles$1, applyStyles$1];
    var createPopper$1 = /* @__PURE__ */ popperGenerator({
      defaultModifiers: defaultModifiers$1
    });
    var defaultModifiers = [eventListeners, popperOffsets$1, computeStyles$1, applyStyles$1, offset$1, flip$1, preventOverflow$1, arrow$1, hide$1];
    var createPopper = /* @__PURE__ */ popperGenerator({
      defaultModifiers
    });
    exports.applyStyles = applyStyles$1;
    exports.arrow = arrow$1;
    exports.computeStyles = computeStyles$1;
    exports.createPopper = createPopper;
    exports.createPopperLite = createPopper$1;
    exports.defaultModifiers = defaultModifiers;
    exports.detectOverflow = detectOverflow;
    exports.eventListeners = eventListeners;
    exports.flip = flip$1;
    exports.hide = hide$1;
    exports.offset = offset$1;
    exports.popperGenerator = popperGenerator;
    exports.popperOffsets = popperOffsets$1;
    exports.preventOverflow = preventOverflow$1;
  });
  var require_tippy_cjs = __commonJS2((exports) => {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    var core = require_popper();
    var ROUND_ARROW = '<svg width="16" height="6" xmlns="http://www.w3.org/2000/svg"><path d="M0 6s1.796-.013 4.67-3.615C5.851.9 6.93.006 8 0c1.07-.006 2.148.887 3.343 2.385C14.233 6.005 16 6 16 6H0z"></svg>';
    var BOX_CLASS = "tippy-box";
    var CONTENT_CLASS = "tippy-content";
    var BACKDROP_CLASS = "tippy-backdrop";
    var ARROW_CLASS = "tippy-arrow";
    var SVG_ARROW_CLASS = "tippy-svg-arrow";
    var TOUCH_OPTIONS = {
      passive: true,
      capture: true
    };
    function hasOwnProperty2(obj, key) {
      return {}.hasOwnProperty.call(obj, key);
    }
    function getValueAtIndexOrReturn(value, index, defaultValue) {
      if (Array.isArray(value)) {
        var v = value[index];
        return v == null ? Array.isArray(defaultValue) ? defaultValue[index] : defaultValue : v;
      }
      return value;
    }
    function isType(value, type) {
      var str = {}.toString.call(value);
      return str.indexOf("[object") === 0 && str.indexOf(type + "]") > -1;
    }
    function invokeWithArgsOrReturn(value, args) {
      return typeof value === "function" ? value.apply(void 0, args) : value;
    }
    function debounce2(fn, ms) {
      if (ms === 0) {
        return fn;
      }
      var timeout;
      return function(arg) {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
          fn(arg);
        }, ms);
      };
    }
    function removeProperties(obj, keys) {
      var clone2 = Object.assign({}, obj);
      keys.forEach(function(key) {
        delete clone2[key];
      });
      return clone2;
    }
    function splitBySpaces(value) {
      return value.split(/\s+/).filter(Boolean);
    }
    function normalizeToArray(value) {
      return [].concat(value);
    }
    function pushIfUnique(arr, value) {
      if (arr.indexOf(value) === -1) {
        arr.push(value);
      }
    }
    function unique(arr) {
      return arr.filter(function(item, index) {
        return arr.indexOf(item) === index;
      });
    }
    function getBasePlacement(placement) {
      return placement.split("-")[0];
    }
    function arrayFrom(value) {
      return [].slice.call(value);
    }
    function removeUndefinedProps(obj) {
      return Object.keys(obj).reduce(function(acc, key) {
        if (obj[key] !== void 0) {
          acc[key] = obj[key];
        }
        return acc;
      }, {});
    }
    function div() {
      return document.createElement("div");
    }
    function isElement(value) {
      return ["Element", "Fragment"].some(function(type) {
        return isType(value, type);
      });
    }
    function isNodeList(value) {
      return isType(value, "NodeList");
    }
    function isMouseEvent(value) {
      return isType(value, "MouseEvent");
    }
    function isReferenceElement(value) {
      return !!(value && value._tippy && value._tippy.reference === value);
    }
    function getArrayOfElements(value) {
      if (isElement(value)) {
        return [value];
      }
      if (isNodeList(value)) {
        return arrayFrom(value);
      }
      if (Array.isArray(value)) {
        return value;
      }
      return arrayFrom(document.querySelectorAll(value));
    }
    function setTransitionDuration(els, value) {
      els.forEach(function(el) {
        if (el) {
          el.style.transitionDuration = value + "ms";
        }
      });
    }
    function setVisibilityState(els, state) {
      els.forEach(function(el) {
        if (el) {
          el.setAttribute("data-state", state);
        }
      });
    }
    function getOwnerDocument(elementOrElements) {
      var _element$ownerDocumen;
      var _normalizeToArray = normalizeToArray(elementOrElements), element = _normalizeToArray[0];
      return (element == null ? void 0 : (_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body) ? element.ownerDocument : document;
    }
    function isCursorOutsideInteractiveBorder(popperTreeData, event) {
      var clientX = event.clientX, clientY = event.clientY;
      return popperTreeData.every(function(_ref) {
        var popperRect = _ref.popperRect, popperState = _ref.popperState, props = _ref.props;
        var interactiveBorder = props.interactiveBorder;
        var basePlacement = getBasePlacement(popperState.placement);
        var offsetData = popperState.modifiersData.offset;
        if (!offsetData) {
          return true;
        }
        var topDistance = basePlacement === "bottom" ? offsetData.top.y : 0;
        var bottomDistance = basePlacement === "top" ? offsetData.bottom.y : 0;
        var leftDistance = basePlacement === "right" ? offsetData.left.x : 0;
        var rightDistance = basePlacement === "left" ? offsetData.right.x : 0;
        var exceedsTop = popperRect.top - clientY + topDistance > interactiveBorder;
        var exceedsBottom = clientY - popperRect.bottom - bottomDistance > interactiveBorder;
        var exceedsLeft = popperRect.left - clientX + leftDistance > interactiveBorder;
        var exceedsRight = clientX - popperRect.right - rightDistance > interactiveBorder;
        return exceedsTop || exceedsBottom || exceedsLeft || exceedsRight;
      });
    }
    function updateTransitionEndListener(box, action, listener) {
      var method = action + "EventListener";
      ["transitionend", "webkitTransitionEnd"].forEach(function(event) {
        box[method](event, listener);
      });
    }
    var currentInput = {
      isTouch: false
    };
    var lastMouseMoveTime = 0;
    function onDocumentTouchStart() {
      if (currentInput.isTouch) {
        return;
      }
      currentInput.isTouch = true;
      if (window.performance) {
        document.addEventListener("mousemove", onDocumentMouseMove);
      }
    }
    function onDocumentMouseMove() {
      var now = performance.now();
      if (now - lastMouseMoveTime < 20) {
        currentInput.isTouch = false;
        document.removeEventListener("mousemove", onDocumentMouseMove);
      }
      lastMouseMoveTime = now;
    }
    function onWindowBlur() {
      var activeElement = document.activeElement;
      if (isReferenceElement(activeElement)) {
        var instance = activeElement._tippy;
        if (activeElement.blur && !instance.state.isVisible) {
          activeElement.blur();
        }
      }
    }
    function bindGlobalEventListeners() {
      document.addEventListener("touchstart", onDocumentTouchStart, TOUCH_OPTIONS);
      window.addEventListener("blur", onWindowBlur);
    }
    var isBrowser = typeof window !== "undefined" && typeof document !== "undefined";
    var ua = isBrowser ? navigator.userAgent : "";
    var isIE = /MSIE |Trident\//.test(ua);
    function createMemoryLeakWarning(method) {
      var txt = method === "destroy" ? "n already-" : " ";
      return [method + "() was called on a" + txt + "destroyed instance. This is a no-op but", "indicates a potential memory leak."].join(" ");
    }
    function clean(value) {
      var spacesAndTabs = /[ \t]{2,}/g;
      var lineStartWithSpaces = /^[ \t]*/gm;
      return value.replace(spacesAndTabs, " ").replace(lineStartWithSpaces, "").trim();
    }
    function getDevMessage(message) {
      return clean("\n  %ctippy.js\n\n  %c" + clean(message) + "\n\n  %c\u{1F477}\u200D This is a development-only message. It will be removed in production.\n  ");
    }
    function getFormattedMessage(message) {
      return [
        getDevMessage(message),
        "color: #00C584; font-size: 1.3em; font-weight: bold;",
        "line-height: 1.5",
        "color: #a6a095;"
      ];
    }
    var visitedMessages;
    if (true) {
      resetVisitedMessages();
    }
    function resetVisitedMessages() {
      visitedMessages = /* @__PURE__ */ new Set();
    }
    function warnWhen(condition, message) {
      if (condition && !visitedMessages.has(message)) {
        var _console;
        visitedMessages.add(message);
        (_console = console).warn.apply(_console, getFormattedMessage(message));
      }
    }
    function errorWhen(condition, message) {
      if (condition && !visitedMessages.has(message)) {
        var _console2;
        visitedMessages.add(message);
        (_console2 = console).error.apply(_console2, getFormattedMessage(message));
      }
    }
    function validateTargets(targets) {
      var didPassFalsyValue = !targets;
      var didPassPlainObject = Object.prototype.toString.call(targets) === "[object Object]" && !targets.addEventListener;
      errorWhen(didPassFalsyValue, ["tippy() was passed", "`" + String(targets) + "`", "as its targets (first) argument. Valid types are: String, Element,", "Element[], or NodeList."].join(" "));
      errorWhen(didPassPlainObject, ["tippy() was passed a plain object which is not supported as an argument", "for virtual positioning. Use props.getReferenceClientRect instead."].join(" "));
    }
    var pluginProps = {
      animateFill: false,
      followCursor: false,
      inlinePositioning: false,
      sticky: false
    };
    var renderProps = {
      allowHTML: false,
      animation: "fade",
      arrow: true,
      content: "",
      inertia: false,
      maxWidth: 350,
      role: "tooltip",
      theme: "",
      zIndex: 9999
    };
    var defaultProps = Object.assign({
      appendTo: function appendTo() {
        return document.body;
      },
      aria: {
        content: "auto",
        expanded: "auto"
      },
      delay: 0,
      duration: [300, 250],
      getReferenceClientRect: null,
      hideOnClick: true,
      ignoreAttributes: false,
      interactive: false,
      interactiveBorder: 2,
      interactiveDebounce: 0,
      moveTransition: "",
      offset: [0, 10],
      onAfterUpdate: function onAfterUpdate() {
      },
      onBeforeUpdate: function onBeforeUpdate() {
      },
      onCreate: function onCreate() {
      },
      onDestroy: function onDestroy() {
      },
      onHidden: function onHidden() {
      },
      onHide: function onHide() {
      },
      onMount: function onMount() {
      },
      onShow: function onShow() {
      },
      onShown: function onShown() {
      },
      onTrigger: function onTrigger() {
      },
      onUntrigger: function onUntrigger() {
      },
      onClickOutside: function onClickOutside() {
      },
      placement: "top",
      plugins: [],
      popperOptions: {},
      render: null,
      showOnCreate: false,
      touch: true,
      trigger: "mouseenter focus",
      triggerTarget: null
    }, pluginProps, {}, renderProps);
    var defaultKeys = Object.keys(defaultProps);
    var setDefaultProps = function setDefaultProps2(partialProps) {
      if (true) {
        validateProps(partialProps, []);
      }
      var keys = Object.keys(partialProps);
      keys.forEach(function(key) {
        defaultProps[key] = partialProps[key];
      });
    };
    function getExtendedPassedProps(passedProps) {
      var plugins = passedProps.plugins || [];
      var pluginProps2 = plugins.reduce(function(acc, plugin2) {
        var name = plugin2.name, defaultValue = plugin2.defaultValue;
        if (name) {
          acc[name] = passedProps[name] !== void 0 ? passedProps[name] : defaultValue;
        }
        return acc;
      }, {});
      return Object.assign({}, passedProps, {}, pluginProps2);
    }
    function getDataAttributeProps(reference, plugins) {
      var propKeys = plugins ? Object.keys(getExtendedPassedProps(Object.assign({}, defaultProps, {
        plugins
      }))) : defaultKeys;
      var props = propKeys.reduce(function(acc, key) {
        var valueAsString = (reference.getAttribute("data-tippy-" + key) || "").trim();
        if (!valueAsString) {
          return acc;
        }
        if (key === "content") {
          acc[key] = valueAsString;
        } else {
          try {
            acc[key] = JSON.parse(valueAsString);
          } catch (e) {
            acc[key] = valueAsString;
          }
        }
        return acc;
      }, {});
      return props;
    }
    function evaluateProps(reference, props) {
      var out = Object.assign({}, props, {
        content: invokeWithArgsOrReturn(props.content, [reference])
      }, props.ignoreAttributes ? {} : getDataAttributeProps(reference, props.plugins));
      out.aria = Object.assign({}, defaultProps.aria, {}, out.aria);
      out.aria = {
        expanded: out.aria.expanded === "auto" ? props.interactive : out.aria.expanded,
        content: out.aria.content === "auto" ? props.interactive ? null : "describedby" : out.aria.content
      };
      return out;
    }
    function validateProps(partialProps, plugins) {
      if (partialProps === void 0) {
        partialProps = {};
      }
      if (plugins === void 0) {
        plugins = [];
      }
      var keys = Object.keys(partialProps);
      keys.forEach(function(prop) {
        var nonPluginProps = removeProperties(defaultProps, Object.keys(pluginProps));
        var didPassUnknownProp = !hasOwnProperty2(nonPluginProps, prop);
        if (didPassUnknownProp) {
          didPassUnknownProp = plugins.filter(function(plugin2) {
            return plugin2.name === prop;
          }).length === 0;
        }
        warnWhen(didPassUnknownProp, ["`" + prop + "`", "is not a valid prop. You may have spelled it incorrectly, or if it's", "a plugin, forgot to pass it in an array as props.plugins.", "\n\n", "All props: https://atomiks.github.io/tippyjs/v6/all-props/\n", "Plugins: https://atomiks.github.io/tippyjs/v6/plugins/"].join(" "));
      });
    }
    var innerHTML = function innerHTML2() {
      return "innerHTML";
    };
    function dangerouslySetInnerHTML(element, html) {
      element[innerHTML()] = html;
    }
    function createArrowElement(value) {
      var arrow = div();
      if (value === true) {
        arrow.className = ARROW_CLASS;
      } else {
        arrow.className = SVG_ARROW_CLASS;
        if (isElement(value)) {
          arrow.appendChild(value);
        } else {
          dangerouslySetInnerHTML(arrow, value);
        }
      }
      return arrow;
    }
    function setContent(content, props) {
      if (isElement(props.content)) {
        dangerouslySetInnerHTML(content, "");
        content.appendChild(props.content);
      } else if (typeof props.content !== "function") {
        if (props.allowHTML) {
          dangerouslySetInnerHTML(content, props.content);
        } else {
          content.textContent = props.content;
        }
      }
    }
    function getChildren(popper) {
      var box = popper.firstElementChild;
      var boxChildren = arrayFrom(box.children);
      return {
        box,
        content: boxChildren.find(function(node) {
          return node.classList.contains(CONTENT_CLASS);
        }),
        arrow: boxChildren.find(function(node) {
          return node.classList.contains(ARROW_CLASS) || node.classList.contains(SVG_ARROW_CLASS);
        }),
        backdrop: boxChildren.find(function(node) {
          return node.classList.contains(BACKDROP_CLASS);
        })
      };
    }
    function render(instance) {
      var popper = div();
      var box = div();
      box.className = BOX_CLASS;
      box.setAttribute("data-state", "hidden");
      box.setAttribute("tabindex", "-1");
      var content = div();
      content.className = CONTENT_CLASS;
      content.setAttribute("data-state", "hidden");
      setContent(content, instance.props);
      popper.appendChild(box);
      box.appendChild(content);
      onUpdate(instance.props, instance.props);
      function onUpdate(prevProps, nextProps) {
        var _getChildren = getChildren(popper), box2 = _getChildren.box, content2 = _getChildren.content, arrow = _getChildren.arrow;
        if (nextProps.theme) {
          box2.setAttribute("data-theme", nextProps.theme);
        } else {
          box2.removeAttribute("data-theme");
        }
        if (typeof nextProps.animation === "string") {
          box2.setAttribute("data-animation", nextProps.animation);
        } else {
          box2.removeAttribute("data-animation");
        }
        if (nextProps.inertia) {
          box2.setAttribute("data-inertia", "");
        } else {
          box2.removeAttribute("data-inertia");
        }
        box2.style.maxWidth = typeof nextProps.maxWidth === "number" ? nextProps.maxWidth + "px" : nextProps.maxWidth;
        if (nextProps.role) {
          box2.setAttribute("role", nextProps.role);
        } else {
          box2.removeAttribute("role");
        }
        if (prevProps.content !== nextProps.content || prevProps.allowHTML !== nextProps.allowHTML) {
          setContent(content2, instance.props);
        }
        if (nextProps.arrow) {
          if (!arrow) {
            box2.appendChild(createArrowElement(nextProps.arrow));
          } else if (prevProps.arrow !== nextProps.arrow) {
            box2.removeChild(arrow);
            box2.appendChild(createArrowElement(nextProps.arrow));
          }
        } else if (arrow) {
          box2.removeChild(arrow);
        }
      }
      return {
        popper,
        onUpdate
      };
    }
    render.$$tippy = true;
    var idCounter = 1;
    var mouseMoveListeners = [];
    var mountedInstances = [];
    function createTippy(reference, passedProps) {
      var props = evaluateProps(reference, Object.assign({}, defaultProps, {}, getExtendedPassedProps(removeUndefinedProps(passedProps))));
      var showTimeout;
      var hideTimeout;
      var scheduleHideAnimationFrame;
      var isVisibleFromClick = false;
      var didHideDueToDocumentMouseDown = false;
      var didTouchMove = false;
      var ignoreOnFirstUpdate = false;
      var lastTriggerEvent;
      var currentTransitionEndListener;
      var onFirstUpdate;
      var listeners = [];
      var debouncedOnMouseMove = debounce2(onMouseMove, props.interactiveDebounce);
      var currentTarget;
      var id = idCounter++;
      var popperInstance = null;
      var plugins = unique(props.plugins);
      var state = {
        isEnabled: true,
        isVisible: false,
        isDestroyed: false,
        isMounted: false,
        isShown: false
      };
      var instance = {
        id,
        reference,
        popper: div(),
        popperInstance,
        props,
        state,
        plugins,
        clearDelayTimeouts,
        setProps,
        setContent: setContent2,
        show,
        hide,
        hideWithInteractivity,
        enable,
        disable,
        unmount,
        destroy
      };
      if (!props.render) {
        if (true) {
          errorWhen(true, "render() function has not been supplied.");
        }
        return instance;
      }
      var _props$render = props.render(instance), popper = _props$render.popper, onUpdate = _props$render.onUpdate;
      popper.setAttribute("data-tippy-root", "");
      popper.id = "tippy-" + instance.id;
      instance.popper = popper;
      reference._tippy = instance;
      popper._tippy = instance;
      var pluginsHooks = plugins.map(function(plugin2) {
        return plugin2.fn(instance);
      });
      var hasAriaExpanded = reference.hasAttribute("aria-expanded");
      addListeners();
      handleAriaExpandedAttribute();
      handleStyles();
      invokeHook("onCreate", [instance]);
      if (props.showOnCreate) {
        scheduleShow();
      }
      popper.addEventListener("mouseenter", function() {
        if (instance.props.interactive && instance.state.isVisible) {
          instance.clearDelayTimeouts();
        }
      });
      popper.addEventListener("mouseleave", function(event) {
        if (instance.props.interactive && instance.props.trigger.indexOf("mouseenter") >= 0) {
          getDocument().addEventListener("mousemove", debouncedOnMouseMove);
          debouncedOnMouseMove(event);
        }
      });
      return instance;
      function getNormalizedTouchSettings() {
        var touch = instance.props.touch;
        return Array.isArray(touch) ? touch : [touch, 0];
      }
      function getIsCustomTouchBehavior() {
        return getNormalizedTouchSettings()[0] === "hold";
      }
      function getIsDefaultRenderFn() {
        var _instance$props$rende;
        return !!((_instance$props$rende = instance.props.render) == null ? void 0 : _instance$props$rende.$$tippy);
      }
      function getCurrentTarget() {
        return currentTarget || reference;
      }
      function getDocument() {
        var parent = getCurrentTarget().parentNode;
        return parent ? getOwnerDocument(parent) : document;
      }
      function getDefaultTemplateChildren() {
        return getChildren(popper);
      }
      function getDelay(isShow) {
        if (instance.state.isMounted && !instance.state.isVisible || currentInput.isTouch || lastTriggerEvent && lastTriggerEvent.type === "focus") {
          return 0;
        }
        return getValueAtIndexOrReturn(instance.props.delay, isShow ? 0 : 1, defaultProps.delay);
      }
      function handleStyles() {
        popper.style.pointerEvents = instance.props.interactive && instance.state.isVisible ? "" : "none";
        popper.style.zIndex = "" + instance.props.zIndex;
      }
      function invokeHook(hook, args, shouldInvokePropsHook) {
        if (shouldInvokePropsHook === void 0) {
          shouldInvokePropsHook = true;
        }
        pluginsHooks.forEach(function(pluginHooks) {
          if (pluginHooks[hook]) {
            pluginHooks[hook].apply(void 0, args);
          }
        });
        if (shouldInvokePropsHook) {
          var _instance$props;
          (_instance$props = instance.props)[hook].apply(_instance$props, args);
        }
      }
      function handleAriaContentAttribute() {
        var aria = instance.props.aria;
        if (!aria.content) {
          return;
        }
        var attr = "aria-" + aria.content;
        var id2 = popper.id;
        var nodes = normalizeToArray(instance.props.triggerTarget || reference);
        nodes.forEach(function(node) {
          var currentValue = node.getAttribute(attr);
          if (instance.state.isVisible) {
            node.setAttribute(attr, currentValue ? currentValue + " " + id2 : id2);
          } else {
            var nextValue = currentValue && currentValue.replace(id2, "").trim();
            if (nextValue) {
              node.setAttribute(attr, nextValue);
            } else {
              node.removeAttribute(attr);
            }
          }
        });
      }
      function handleAriaExpandedAttribute() {
        if (hasAriaExpanded || !instance.props.aria.expanded) {
          return;
        }
        var nodes = normalizeToArray(instance.props.triggerTarget || reference);
        nodes.forEach(function(node) {
          if (instance.props.interactive) {
            node.setAttribute("aria-expanded", instance.state.isVisible && node === getCurrentTarget() ? "true" : "false");
          } else {
            node.removeAttribute("aria-expanded");
          }
        });
      }
      function cleanupInteractiveMouseListeners() {
        getDocument().removeEventListener("mousemove", debouncedOnMouseMove);
        mouseMoveListeners = mouseMoveListeners.filter(function(listener) {
          return listener !== debouncedOnMouseMove;
        });
      }
      function onDocumentPress(event) {
        if (currentInput.isTouch) {
          if (didTouchMove || event.type === "mousedown") {
            return;
          }
        }
        if (instance.props.interactive && popper.contains(event.target)) {
          return;
        }
        if (getCurrentTarget().contains(event.target)) {
          if (currentInput.isTouch) {
            return;
          }
          if (instance.state.isVisible && instance.props.trigger.indexOf("click") >= 0) {
            return;
          }
        } else {
          invokeHook("onClickOutside", [instance, event]);
        }
        if (instance.props.hideOnClick === true) {
          instance.clearDelayTimeouts();
          instance.hide();
          didHideDueToDocumentMouseDown = true;
          setTimeout(function() {
            didHideDueToDocumentMouseDown = false;
          });
          if (!instance.state.isMounted) {
            removeDocumentPress();
          }
        }
      }
      function onTouchMove() {
        didTouchMove = true;
      }
      function onTouchStart() {
        didTouchMove = false;
      }
      function addDocumentPress() {
        var doc = getDocument();
        doc.addEventListener("mousedown", onDocumentPress, true);
        doc.addEventListener("touchend", onDocumentPress, TOUCH_OPTIONS);
        doc.addEventListener("touchstart", onTouchStart, TOUCH_OPTIONS);
        doc.addEventListener("touchmove", onTouchMove, TOUCH_OPTIONS);
      }
      function removeDocumentPress() {
        var doc = getDocument();
        doc.removeEventListener("mousedown", onDocumentPress, true);
        doc.removeEventListener("touchend", onDocumentPress, TOUCH_OPTIONS);
        doc.removeEventListener("touchstart", onTouchStart, TOUCH_OPTIONS);
        doc.removeEventListener("touchmove", onTouchMove, TOUCH_OPTIONS);
      }
      function onTransitionedOut(duration, callback) {
        onTransitionEnd(duration, function() {
          if (!instance.state.isVisible && popper.parentNode && popper.parentNode.contains(popper)) {
            callback();
          }
        });
      }
      function onTransitionedIn(duration, callback) {
        onTransitionEnd(duration, callback);
      }
      function onTransitionEnd(duration, callback) {
        var box = getDefaultTemplateChildren().box;
        function listener(event) {
          if (event.target === box) {
            updateTransitionEndListener(box, "remove", listener);
            callback();
          }
        }
        if (duration === 0) {
          return callback();
        }
        updateTransitionEndListener(box, "remove", currentTransitionEndListener);
        updateTransitionEndListener(box, "add", listener);
        currentTransitionEndListener = listener;
      }
      function on2(eventType, handler3, options) {
        if (options === void 0) {
          options = false;
        }
        var nodes = normalizeToArray(instance.props.triggerTarget || reference);
        nodes.forEach(function(node) {
          node.addEventListener(eventType, handler3, options);
          listeners.push({
            node,
            eventType,
            handler: handler3,
            options
          });
        });
      }
      function addListeners() {
        if (getIsCustomTouchBehavior()) {
          on2("touchstart", onTrigger, {
            passive: true
          });
          on2("touchend", onMouseLeave, {
            passive: true
          });
        }
        splitBySpaces(instance.props.trigger).forEach(function(eventType) {
          if (eventType === "manual") {
            return;
          }
          on2(eventType, onTrigger);
          switch (eventType) {
            case "mouseenter":
              on2("mouseleave", onMouseLeave);
              break;
            case "focus":
              on2(isIE ? "focusout" : "blur", onBlurOrFocusOut);
              break;
            case "focusin":
              on2("focusout", onBlurOrFocusOut);
              break;
          }
        });
      }
      function removeListeners() {
        listeners.forEach(function(_ref) {
          var node = _ref.node, eventType = _ref.eventType, handler3 = _ref.handler, options = _ref.options;
          node.removeEventListener(eventType, handler3, options);
        });
        listeners = [];
      }
      function onTrigger(event) {
        var _lastTriggerEvent;
        var shouldScheduleClickHide = false;
        if (!instance.state.isEnabled || isEventListenerStopped(event) || didHideDueToDocumentMouseDown) {
          return;
        }
        var wasFocused = ((_lastTriggerEvent = lastTriggerEvent) == null ? void 0 : _lastTriggerEvent.type) === "focus";
        lastTriggerEvent = event;
        currentTarget = event.currentTarget;
        handleAriaExpandedAttribute();
        if (!instance.state.isVisible && isMouseEvent(event)) {
          mouseMoveListeners.forEach(function(listener) {
            return listener(event);
          });
        }
        if (event.type === "click" && (instance.props.trigger.indexOf("mouseenter") < 0 || isVisibleFromClick) && instance.props.hideOnClick !== false && instance.state.isVisible) {
          shouldScheduleClickHide = true;
        } else {
          scheduleShow(event);
        }
        if (event.type === "click") {
          isVisibleFromClick = !shouldScheduleClickHide;
        }
        if (shouldScheduleClickHide && !wasFocused) {
          scheduleHide(event);
        }
      }
      function onMouseMove(event) {
        var target = event.target;
        var isCursorOverReferenceOrPopper = getCurrentTarget().contains(target) || popper.contains(target);
        if (event.type === "mousemove" && isCursorOverReferenceOrPopper) {
          return;
        }
        var popperTreeData = getNestedPopperTree().concat(popper).map(function(popper2) {
          var _instance$popperInsta;
          var instance2 = popper2._tippy;
          var state2 = (_instance$popperInsta = instance2.popperInstance) == null ? void 0 : _instance$popperInsta.state;
          if (state2) {
            return {
              popperRect: popper2.getBoundingClientRect(),
              popperState: state2,
              props
            };
          }
          return null;
        }).filter(Boolean);
        if (isCursorOutsideInteractiveBorder(popperTreeData, event)) {
          cleanupInteractiveMouseListeners();
          scheduleHide(event);
        }
      }
      function onMouseLeave(event) {
        var shouldBail = isEventListenerStopped(event) || instance.props.trigger.indexOf("click") >= 0 && isVisibleFromClick;
        if (shouldBail) {
          return;
        }
        if (instance.props.interactive) {
          instance.hideWithInteractivity(event);
          return;
        }
        scheduleHide(event);
      }
      function onBlurOrFocusOut(event) {
        if (instance.props.trigger.indexOf("focusin") < 0 && event.target !== getCurrentTarget()) {
          return;
        }
        if (instance.props.interactive && event.relatedTarget && popper.contains(event.relatedTarget)) {
          return;
        }
        scheduleHide(event);
      }
      function isEventListenerStopped(event) {
        return currentInput.isTouch ? getIsCustomTouchBehavior() !== event.type.indexOf("touch") >= 0 : false;
      }
      function createPopperInstance() {
        destroyPopperInstance();
        var _instance$props2 = instance.props, popperOptions = _instance$props2.popperOptions, placement = _instance$props2.placement, offset = _instance$props2.offset, getReferenceClientRect = _instance$props2.getReferenceClientRect, moveTransition = _instance$props2.moveTransition;
        var arrow = getIsDefaultRenderFn() ? getChildren(popper).arrow : null;
        var computedReference = getReferenceClientRect ? {
          getBoundingClientRect: getReferenceClientRect,
          contextElement: getReferenceClientRect.contextElement || getCurrentTarget()
        } : reference;
        var tippyModifier = {
          name: "$$tippy",
          enabled: true,
          phase: "beforeWrite",
          requires: ["computeStyles"],
          fn: function fn(_ref2) {
            var state2 = _ref2.state;
            if (getIsDefaultRenderFn()) {
              var _getDefaultTemplateCh = getDefaultTemplateChildren(), box = _getDefaultTemplateCh.box;
              ["placement", "reference-hidden", "escaped"].forEach(function(attr) {
                if (attr === "placement") {
                  box.setAttribute("data-placement", state2.placement);
                } else {
                  if (state2.attributes.popper["data-popper-" + attr]) {
                    box.setAttribute("data-" + attr, "");
                  } else {
                    box.removeAttribute("data-" + attr);
                  }
                }
              });
              state2.attributes.popper = {};
            }
          }
        };
        var modifiers = [{
          name: "offset",
          options: {
            offset
          }
        }, {
          name: "preventOverflow",
          options: {
            padding: {
              top: 2,
              bottom: 2,
              left: 5,
              right: 5
            }
          }
        }, {
          name: "flip",
          options: {
            padding: 5
          }
        }, {
          name: "computeStyles",
          options: {
            adaptive: !moveTransition
          }
        }, tippyModifier];
        if (getIsDefaultRenderFn() && arrow) {
          modifiers.push({
            name: "arrow",
            options: {
              element: arrow,
              padding: 3
            }
          });
        }
        modifiers.push.apply(modifiers, (popperOptions == null ? void 0 : popperOptions.modifiers) || []);
        instance.popperInstance = core.createPopper(computedReference, popper, Object.assign({}, popperOptions, {
          placement,
          onFirstUpdate,
          modifiers
        }));
      }
      function destroyPopperInstance() {
        if (instance.popperInstance) {
          instance.popperInstance.destroy();
          instance.popperInstance = null;
        }
      }
      function mount() {
        var appendTo = instance.props.appendTo;
        var parentNode;
        var node = getCurrentTarget();
        if (instance.props.interactive && appendTo === defaultProps.appendTo || appendTo === "parent") {
          parentNode = node.parentNode;
        } else {
          parentNode = invokeWithArgsOrReturn(appendTo, [node]);
        }
        if (!parentNode.contains(popper)) {
          parentNode.appendChild(popper);
        }
        createPopperInstance();
        if (true) {
          warnWhen(instance.props.interactive && appendTo === defaultProps.appendTo && node.nextElementSibling !== popper, ["Interactive tippy element may not be accessible via keyboard", "navigation because it is not directly after the reference element", "in the DOM source order.", "\n\n", "Using a wrapper <div> or <span> tag around the reference element", "solves this by creating a new parentNode context.", "\n\n", "Specifying `appendTo: document.body` silences this warning, but it", "assumes you are using a focus management solution to handle", "keyboard navigation.", "\n\n", "See: https://atomiks.github.io/tippyjs/v6/accessibility/#interactivity"].join(" "));
        }
      }
      function getNestedPopperTree() {
        return arrayFrom(popper.querySelectorAll("[data-tippy-root]"));
      }
      function scheduleShow(event) {
        instance.clearDelayTimeouts();
        if (event) {
          invokeHook("onTrigger", [instance, event]);
        }
        addDocumentPress();
        var delay = getDelay(true);
        var _getNormalizedTouchSe = getNormalizedTouchSettings(), touchValue = _getNormalizedTouchSe[0], touchDelay = _getNormalizedTouchSe[1];
        if (currentInput.isTouch && touchValue === "hold" && touchDelay) {
          delay = touchDelay;
        }
        if (delay) {
          showTimeout = setTimeout(function() {
            instance.show();
          }, delay);
        } else {
          instance.show();
        }
      }
      function scheduleHide(event) {
        instance.clearDelayTimeouts();
        invokeHook("onUntrigger", [instance, event]);
        if (!instance.state.isVisible) {
          removeDocumentPress();
          return;
        }
        if (instance.props.trigger.indexOf("mouseenter") >= 0 && instance.props.trigger.indexOf("click") >= 0 && ["mouseleave", "mousemove"].indexOf(event.type) >= 0 && isVisibleFromClick) {
          return;
        }
        var delay = getDelay(false);
        if (delay) {
          hideTimeout = setTimeout(function() {
            if (instance.state.isVisible) {
              instance.hide();
            }
          }, delay);
        } else {
          scheduleHideAnimationFrame = requestAnimationFrame(function() {
            instance.hide();
          });
        }
      }
      function enable() {
        instance.state.isEnabled = true;
      }
      function disable() {
        instance.hide();
        instance.state.isEnabled = false;
      }
      function clearDelayTimeouts() {
        clearTimeout(showTimeout);
        clearTimeout(hideTimeout);
        cancelAnimationFrame(scheduleHideAnimationFrame);
      }
      function setProps(partialProps) {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("setProps"));
        }
        if (instance.state.isDestroyed) {
          return;
        }
        invokeHook("onBeforeUpdate", [instance, partialProps]);
        removeListeners();
        var prevProps = instance.props;
        var nextProps = evaluateProps(reference, Object.assign({}, instance.props, {}, partialProps, {
          ignoreAttributes: true
        }));
        instance.props = nextProps;
        addListeners();
        if (prevProps.interactiveDebounce !== nextProps.interactiveDebounce) {
          cleanupInteractiveMouseListeners();
          debouncedOnMouseMove = debounce2(onMouseMove, nextProps.interactiveDebounce);
        }
        if (prevProps.triggerTarget && !nextProps.triggerTarget) {
          normalizeToArray(prevProps.triggerTarget).forEach(function(node) {
            node.removeAttribute("aria-expanded");
          });
        } else if (nextProps.triggerTarget) {
          reference.removeAttribute("aria-expanded");
        }
        handleAriaExpandedAttribute();
        handleStyles();
        if (onUpdate) {
          onUpdate(prevProps, nextProps);
        }
        if (instance.popperInstance) {
          createPopperInstance();
          getNestedPopperTree().forEach(function(nestedPopper) {
            requestAnimationFrame(nestedPopper._tippy.popperInstance.forceUpdate);
          });
        }
        invokeHook("onAfterUpdate", [instance, partialProps]);
      }
      function setContent2(content) {
        instance.setProps({
          content
        });
      }
      function show() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("show"));
        }
        var isAlreadyVisible = instance.state.isVisible;
        var isDestroyed = instance.state.isDestroyed;
        var isDisabled = !instance.state.isEnabled;
        var isTouchAndTouchDisabled = currentInput.isTouch && !instance.props.touch;
        var duration = getValueAtIndexOrReturn(instance.props.duration, 0, defaultProps.duration);
        if (isAlreadyVisible || isDestroyed || isDisabled || isTouchAndTouchDisabled) {
          return;
        }
        if (getCurrentTarget().hasAttribute("disabled")) {
          return;
        }
        invokeHook("onShow", [instance], false);
        if (instance.props.onShow(instance) === false) {
          return;
        }
        instance.state.isVisible = true;
        if (getIsDefaultRenderFn()) {
          popper.style.visibility = "visible";
        }
        handleStyles();
        addDocumentPress();
        if (!instance.state.isMounted) {
          popper.style.transition = "none";
        }
        if (getIsDefaultRenderFn()) {
          var _getDefaultTemplateCh2 = getDefaultTemplateChildren(), box = _getDefaultTemplateCh2.box, content = _getDefaultTemplateCh2.content;
          setTransitionDuration([box, content], 0);
        }
        onFirstUpdate = function onFirstUpdate2() {
          var _instance$popperInsta2;
          if (!instance.state.isVisible || ignoreOnFirstUpdate) {
            return;
          }
          ignoreOnFirstUpdate = true;
          void popper.offsetHeight;
          popper.style.transition = instance.props.moveTransition;
          if (getIsDefaultRenderFn() && instance.props.animation) {
            var _getDefaultTemplateCh3 = getDefaultTemplateChildren(), _box = _getDefaultTemplateCh3.box, _content = _getDefaultTemplateCh3.content;
            setTransitionDuration([_box, _content], duration);
            setVisibilityState([_box, _content], "visible");
          }
          handleAriaContentAttribute();
          handleAriaExpandedAttribute();
          pushIfUnique(mountedInstances, instance);
          (_instance$popperInsta2 = instance.popperInstance) == null ? void 0 : _instance$popperInsta2.forceUpdate();
          instance.state.isMounted = true;
          invokeHook("onMount", [instance]);
          if (instance.props.animation && getIsDefaultRenderFn()) {
            onTransitionedIn(duration, function() {
              instance.state.isShown = true;
              invokeHook("onShown", [instance]);
            });
          }
        };
        mount();
      }
      function hide() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("hide"));
        }
        var isAlreadyHidden = !instance.state.isVisible;
        var isDestroyed = instance.state.isDestroyed;
        var isDisabled = !instance.state.isEnabled;
        var duration = getValueAtIndexOrReturn(instance.props.duration, 1, defaultProps.duration);
        if (isAlreadyHidden || isDestroyed || isDisabled) {
          return;
        }
        invokeHook("onHide", [instance], false);
        if (instance.props.onHide(instance) === false) {
          return;
        }
        instance.state.isVisible = false;
        instance.state.isShown = false;
        ignoreOnFirstUpdate = false;
        isVisibleFromClick = false;
        if (getIsDefaultRenderFn()) {
          popper.style.visibility = "hidden";
        }
        cleanupInteractiveMouseListeners();
        removeDocumentPress();
        handleStyles();
        if (getIsDefaultRenderFn()) {
          var _getDefaultTemplateCh4 = getDefaultTemplateChildren(), box = _getDefaultTemplateCh4.box, content = _getDefaultTemplateCh4.content;
          if (instance.props.animation) {
            setTransitionDuration([box, content], duration);
            setVisibilityState([box, content], "hidden");
          }
        }
        handleAriaContentAttribute();
        handleAriaExpandedAttribute();
        if (instance.props.animation) {
          if (getIsDefaultRenderFn()) {
            onTransitionedOut(duration, instance.unmount);
          }
        } else {
          instance.unmount();
        }
      }
      function hideWithInteractivity(event) {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("hideWithInteractivity"));
        }
        getDocument().addEventListener("mousemove", debouncedOnMouseMove);
        pushIfUnique(mouseMoveListeners, debouncedOnMouseMove);
        debouncedOnMouseMove(event);
      }
      function unmount() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("unmount"));
        }
        if (instance.state.isVisible) {
          instance.hide();
        }
        if (!instance.state.isMounted) {
          return;
        }
        destroyPopperInstance();
        getNestedPopperTree().forEach(function(nestedPopper) {
          nestedPopper._tippy.unmount();
        });
        if (popper.parentNode) {
          popper.parentNode.removeChild(popper);
        }
        mountedInstances = mountedInstances.filter(function(i) {
          return i !== instance;
        });
        instance.state.isMounted = false;
        invokeHook("onHidden", [instance]);
      }
      function destroy() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("destroy"));
        }
        if (instance.state.isDestroyed) {
          return;
        }
        instance.clearDelayTimeouts();
        instance.unmount();
        removeListeners();
        delete reference._tippy;
        instance.state.isDestroyed = true;
        invokeHook("onDestroy", [instance]);
      }
    }
    function tippy2(targets, optionalProps) {
      if (optionalProps === void 0) {
        optionalProps = {};
      }
      var plugins = defaultProps.plugins.concat(optionalProps.plugins || []);
      if (true) {
        validateTargets(targets);
        validateProps(optionalProps, plugins);
      }
      bindGlobalEventListeners();
      var passedProps = Object.assign({}, optionalProps, {
        plugins
      });
      var elements = getArrayOfElements(targets);
      if (true) {
        var isSingleContentElement = isElement(passedProps.content);
        var isMoreThanOneReferenceElement = elements.length > 1;
        warnWhen(isSingleContentElement && isMoreThanOneReferenceElement, ["tippy() was passed an Element as the `content` prop, but more than", "one tippy instance was created by this invocation. This means the", "content element will only be appended to the last tippy instance.", "\n\n", "Instead, pass the .innerHTML of the element, or use a function that", "returns a cloned version of the element instead.", "\n\n", "1) content: element.innerHTML\n", "2) content: () => element.cloneNode(true)"].join(" "));
      }
      var instances = elements.reduce(function(acc, reference) {
        var instance = reference && createTippy(reference, passedProps);
        if (instance) {
          acc.push(instance);
        }
        return acc;
      }, []);
      return isElement(targets) ? instances[0] : instances;
    }
    tippy2.defaultProps = defaultProps;
    tippy2.setDefaultProps = setDefaultProps;
    tippy2.currentInput = currentInput;
    var hideAll = function hideAll2(_temp) {
      var _ref = _temp === void 0 ? {} : _temp, excludedReferenceOrInstance = _ref.exclude, duration = _ref.duration;
      mountedInstances.forEach(function(instance) {
        var isExcluded = false;
        if (excludedReferenceOrInstance) {
          isExcluded = isReferenceElement(excludedReferenceOrInstance) ? instance.reference === excludedReferenceOrInstance : instance.popper === excludedReferenceOrInstance.popper;
        }
        if (!isExcluded) {
          var originalDuration = instance.props.duration;
          instance.setProps({
            duration
          });
          instance.hide();
          if (!instance.state.isDestroyed) {
            instance.setProps({
              duration: originalDuration
            });
          }
        }
      });
    };
    var applyStylesModifier = Object.assign({}, core.applyStyles, {
      effect: function effect3(_ref) {
        var state = _ref.state;
        var initialStyles = {
          popper: {
            position: state.options.strategy,
            left: "0",
            top: "0",
            margin: "0"
          },
          arrow: {
            position: "absolute"
          },
          reference: {}
        };
        Object.assign(state.elements.popper.style, initialStyles.popper);
        state.styles = initialStyles;
        if (state.elements.arrow) {
          Object.assign(state.elements.arrow.style, initialStyles.arrow);
        }
      }
    });
    var createSingleton = function createSingleton2(tippyInstances, optionalProps) {
      var _optionalProps$popper;
      if (optionalProps === void 0) {
        optionalProps = {};
      }
      if (true) {
        errorWhen(!Array.isArray(tippyInstances), ["The first argument passed to createSingleton() must be an array of", "tippy instances. The passed value was", String(tippyInstances)].join(" "));
      }
      var individualInstances = tippyInstances;
      var references = [];
      var currentTarget;
      var overrides = optionalProps.overrides;
      var interceptSetPropsCleanups = [];
      var shownOnCreate = false;
      function setReferences() {
        references = individualInstances.map(function(instance) {
          return instance.reference;
        });
      }
      function enableInstances(isEnabled) {
        individualInstances.forEach(function(instance) {
          if (isEnabled) {
            instance.enable();
          } else {
            instance.disable();
          }
        });
      }
      function interceptSetProps(singleton2) {
        return individualInstances.map(function(instance) {
          var originalSetProps2 = instance.setProps;
          instance.setProps = function(props) {
            originalSetProps2(props);
            if (instance.reference === currentTarget) {
              singleton2.setProps(props);
            }
          };
          return function() {
            instance.setProps = originalSetProps2;
          };
        });
      }
      function prepareInstance(singleton2, target) {
        var index = references.indexOf(target);
        if (target === currentTarget) {
          return;
        }
        currentTarget = target;
        var overrideProps = (overrides || []).concat("content").reduce(function(acc, prop) {
          acc[prop] = individualInstances[index].props[prop];
          return acc;
        }, {});
        singleton2.setProps(Object.assign({}, overrideProps, {
          getReferenceClientRect: typeof overrideProps.getReferenceClientRect === "function" ? overrideProps.getReferenceClientRect : function() {
            return target.getBoundingClientRect();
          }
        }));
      }
      enableInstances(false);
      setReferences();
      var plugin2 = {
        fn: function fn() {
          return {
            onDestroy: function onDestroy() {
              enableInstances(true);
            },
            onHidden: function onHidden() {
              currentTarget = null;
            },
            onClickOutside: function onClickOutside(instance) {
              if (instance.props.showOnCreate && !shownOnCreate) {
                shownOnCreate = true;
                currentTarget = null;
              }
            },
            onShow: function onShow(instance) {
              if (instance.props.showOnCreate && !shownOnCreate) {
                shownOnCreate = true;
                prepareInstance(instance, references[0]);
              }
            },
            onTrigger: function onTrigger(instance, event) {
              prepareInstance(instance, event.currentTarget);
            }
          };
        }
      };
      var singleton = tippy2(div(), Object.assign({}, removeProperties(optionalProps, ["overrides"]), {
        plugins: [plugin2].concat(optionalProps.plugins || []),
        triggerTarget: references,
        popperOptions: Object.assign({}, optionalProps.popperOptions, {
          modifiers: [].concat(((_optionalProps$popper = optionalProps.popperOptions) == null ? void 0 : _optionalProps$popper.modifiers) || [], [applyStylesModifier])
        })
      }));
      var originalShow = singleton.show;
      singleton.show = function(target) {
        originalShow();
        if (!currentTarget && target == null) {
          return prepareInstance(singleton, references[0]);
        }
        if (currentTarget && target == null) {
          return;
        }
        if (typeof target === "number") {
          return references[target] && prepareInstance(singleton, references[target]);
        }
        if (individualInstances.includes(target)) {
          var ref = target.reference;
          return prepareInstance(singleton, ref);
        }
        if (references.includes(target)) {
          return prepareInstance(singleton, target);
        }
      };
      singleton.showNext = function() {
        var first = references[0];
        if (!currentTarget) {
          return singleton.show(0);
        }
        var index = references.indexOf(currentTarget);
        singleton.show(references[index + 1] || first);
      };
      singleton.showPrevious = function() {
        var last = references[references.length - 1];
        if (!currentTarget) {
          return singleton.show(last);
        }
        var index = references.indexOf(currentTarget);
        var target = references[index - 1] || last;
        singleton.show(target);
      };
      var originalSetProps = singleton.setProps;
      singleton.setProps = function(props) {
        overrides = props.overrides || overrides;
        originalSetProps(props);
      };
      singleton.setInstances = function(nextInstances) {
        enableInstances(true);
        interceptSetPropsCleanups.forEach(function(fn) {
          return fn();
        });
        individualInstances = nextInstances;
        enableInstances(false);
        setReferences();
        interceptSetProps(singleton);
        singleton.setProps({
          triggerTarget: references
        });
      };
      interceptSetPropsCleanups = interceptSetProps(singleton);
      return singleton;
    };
    var BUBBLING_EVENTS_MAP = {
      mouseover: "mouseenter",
      focusin: "focus",
      click: "click"
    };
    function delegate(targets, props) {
      if (true) {
        errorWhen(!(props && props.target), ["You must specity a `target` prop indicating a CSS selector string matching", "the target elements that should receive a tippy."].join(" "));
      }
      var listeners = [];
      var childTippyInstances = [];
      var disabled = false;
      var target = props.target;
      var nativeProps = removeProperties(props, ["target"]);
      var parentProps = Object.assign({}, nativeProps, {
        trigger: "manual",
        touch: false
      });
      var childProps = Object.assign({}, nativeProps, {
        showOnCreate: true
      });
      var returnValue = tippy2(targets, parentProps);
      var normalizedReturnValue = normalizeToArray(returnValue);
      function onTrigger(event) {
        if (!event.target || disabled) {
          return;
        }
        var targetNode = event.target.closest(target);
        if (!targetNode) {
          return;
        }
        var trigger2 = targetNode.getAttribute("data-tippy-trigger") || props.trigger || defaultProps.trigger;
        if (targetNode._tippy) {
          return;
        }
        if (event.type === "touchstart" && typeof childProps.touch === "boolean") {
          return;
        }
        if (event.type !== "touchstart" && trigger2.indexOf(BUBBLING_EVENTS_MAP[event.type]) < 0) {
          return;
        }
        var instance = tippy2(targetNode, childProps);
        if (instance) {
          childTippyInstances = childTippyInstances.concat(instance);
        }
      }
      function on2(node, eventType, handler3, options) {
        if (options === void 0) {
          options = false;
        }
        node.addEventListener(eventType, handler3, options);
        listeners.push({
          node,
          eventType,
          handler: handler3,
          options
        });
      }
      function addEventListeners(instance) {
        var reference = instance.reference;
        on2(reference, "touchstart", onTrigger, TOUCH_OPTIONS);
        on2(reference, "mouseover", onTrigger);
        on2(reference, "focusin", onTrigger);
        on2(reference, "click", onTrigger);
      }
      function removeEventListeners() {
        listeners.forEach(function(_ref) {
          var node = _ref.node, eventType = _ref.eventType, handler3 = _ref.handler, options = _ref.options;
          node.removeEventListener(eventType, handler3, options);
        });
        listeners = [];
      }
      function applyMutations(instance) {
        var originalDestroy = instance.destroy;
        var originalEnable = instance.enable;
        var originalDisable = instance.disable;
        instance.destroy = function(shouldDestroyChildInstances) {
          if (shouldDestroyChildInstances === void 0) {
            shouldDestroyChildInstances = true;
          }
          if (shouldDestroyChildInstances) {
            childTippyInstances.forEach(function(instance2) {
              instance2.destroy();
            });
          }
          childTippyInstances = [];
          removeEventListeners();
          originalDestroy();
        };
        instance.enable = function() {
          originalEnable();
          childTippyInstances.forEach(function(instance2) {
            return instance2.enable();
          });
          disabled = false;
        };
        instance.disable = function() {
          originalDisable();
          childTippyInstances.forEach(function(instance2) {
            return instance2.disable();
          });
          disabled = true;
        };
        addEventListeners(instance);
      }
      normalizedReturnValue.forEach(applyMutations);
      return returnValue;
    }
    var animateFill = {
      name: "animateFill",
      defaultValue: false,
      fn: function fn(instance) {
        var _instance$props$rende;
        if (!((_instance$props$rende = instance.props.render) == null ? void 0 : _instance$props$rende.$$tippy)) {
          if (true) {
            errorWhen(instance.props.animateFill, "The `animateFill` plugin requires the default render function.");
          }
          return {};
        }
        var _getChildren = getChildren(instance.popper), box = _getChildren.box, content = _getChildren.content;
        var backdrop = instance.props.animateFill ? createBackdropElement() : null;
        return {
          onCreate: function onCreate() {
            if (backdrop) {
              box.insertBefore(backdrop, box.firstElementChild);
              box.setAttribute("data-animatefill", "");
              box.style.overflow = "hidden";
              instance.setProps({
                arrow: false,
                animation: "shift-away"
              });
            }
          },
          onMount: function onMount() {
            if (backdrop) {
              var transitionDuration = box.style.transitionDuration;
              var duration = Number(transitionDuration.replace("ms", ""));
              content.style.transitionDelay = Math.round(duration / 10) + "ms";
              backdrop.style.transitionDuration = transitionDuration;
              setVisibilityState([backdrop], "visible");
            }
          },
          onShow: function onShow() {
            if (backdrop) {
              backdrop.style.transitionDuration = "0ms";
            }
          },
          onHide: function onHide() {
            if (backdrop) {
              setVisibilityState([backdrop], "hidden");
            }
          }
        };
      }
    };
    function createBackdropElement() {
      var backdrop = div();
      backdrop.className = BACKDROP_CLASS;
      setVisibilityState([backdrop], "hidden");
      return backdrop;
    }
    var mouseCoords = {
      clientX: 0,
      clientY: 0
    };
    var activeInstances = [];
    function storeMouseCoords(_ref) {
      var clientX = _ref.clientX, clientY = _ref.clientY;
      mouseCoords = {
        clientX,
        clientY
      };
    }
    function addMouseCoordsListener(doc) {
      doc.addEventListener("mousemove", storeMouseCoords);
    }
    function removeMouseCoordsListener(doc) {
      doc.removeEventListener("mousemove", storeMouseCoords);
    }
    var followCursor2 = {
      name: "followCursor",
      defaultValue: false,
      fn: function fn(instance) {
        var reference = instance.reference;
        var doc = getOwnerDocument(instance.props.triggerTarget || reference);
        var isInternalUpdate = false;
        var wasFocusEvent = false;
        var isUnmounted = true;
        var prevProps = instance.props;
        function getIsInitialBehavior() {
          return instance.props.followCursor === "initial" && instance.state.isVisible;
        }
        function addListener() {
          doc.addEventListener("mousemove", onMouseMove);
        }
        function removeListener() {
          doc.removeEventListener("mousemove", onMouseMove);
        }
        function unsetGetReferenceClientRect() {
          isInternalUpdate = true;
          instance.setProps({
            getReferenceClientRect: null
          });
          isInternalUpdate = false;
        }
        function onMouseMove(event) {
          var isCursorOverReference = event.target ? reference.contains(event.target) : true;
          var followCursor3 = instance.props.followCursor;
          var clientX = event.clientX, clientY = event.clientY;
          var rect = reference.getBoundingClientRect();
          var relativeX = clientX - rect.left;
          var relativeY = clientY - rect.top;
          if (isCursorOverReference || !instance.props.interactive) {
            instance.setProps({
              getReferenceClientRect: function getReferenceClientRect() {
                var rect2 = reference.getBoundingClientRect();
                var x = clientX;
                var y = clientY;
                if (followCursor3 === "initial") {
                  x = rect2.left + relativeX;
                  y = rect2.top + relativeY;
                }
                var top = followCursor3 === "horizontal" ? rect2.top : y;
                var right = followCursor3 === "vertical" ? rect2.right : x;
                var bottom = followCursor3 === "horizontal" ? rect2.bottom : y;
                var left = followCursor3 === "vertical" ? rect2.left : x;
                return {
                  width: right - left,
                  height: bottom - top,
                  top,
                  right,
                  bottom,
                  left
                };
              }
            });
          }
        }
        function create() {
          if (instance.props.followCursor) {
            activeInstances.push({
              instance,
              doc
            });
            addMouseCoordsListener(doc);
          }
        }
        function destroy() {
          activeInstances = activeInstances.filter(function(data2) {
            return data2.instance !== instance;
          });
          if (activeInstances.filter(function(data2) {
            return data2.doc === doc;
          }).length === 0) {
            removeMouseCoordsListener(doc);
          }
        }
        return {
          onCreate: create,
          onDestroy: destroy,
          onBeforeUpdate: function onBeforeUpdate() {
            prevProps = instance.props;
          },
          onAfterUpdate: function onAfterUpdate(_, _ref2) {
            var followCursor3 = _ref2.followCursor;
            if (isInternalUpdate) {
              return;
            }
            if (followCursor3 !== void 0 && prevProps.followCursor !== followCursor3) {
              destroy();
              if (followCursor3) {
                create();
                if (instance.state.isMounted && !wasFocusEvent && !getIsInitialBehavior()) {
                  addListener();
                }
              } else {
                removeListener();
                unsetGetReferenceClientRect();
              }
            }
          },
          onMount: function onMount() {
            if (instance.props.followCursor && !wasFocusEvent) {
              if (isUnmounted) {
                onMouseMove(mouseCoords);
                isUnmounted = false;
              }
              if (!getIsInitialBehavior()) {
                addListener();
              }
            }
          },
          onTrigger: function onTrigger(_, event) {
            if (isMouseEvent(event)) {
              mouseCoords = {
                clientX: event.clientX,
                clientY: event.clientY
              };
            }
            wasFocusEvent = event.type === "focus";
          },
          onHidden: function onHidden() {
            if (instance.props.followCursor) {
              unsetGetReferenceClientRect();
              removeListener();
              isUnmounted = true;
            }
          }
        };
      }
    };
    function getProps(props, modifier) {
      var _props$popperOptions;
      return {
        popperOptions: Object.assign({}, props.popperOptions, {
          modifiers: [].concat((((_props$popperOptions = props.popperOptions) == null ? void 0 : _props$popperOptions.modifiers) || []).filter(function(_ref) {
            var name = _ref.name;
            return name !== modifier.name;
          }), [modifier])
        })
      };
    }
    var inlinePositioning = {
      name: "inlinePositioning",
      defaultValue: false,
      fn: function fn(instance) {
        var reference = instance.reference;
        function isEnabled() {
          return !!instance.props.inlinePositioning;
        }
        var placement;
        var cursorRectIndex = -1;
        var isInternalUpdate = false;
        var modifier = {
          name: "tippyInlinePositioning",
          enabled: true,
          phase: "afterWrite",
          fn: function fn2(_ref2) {
            var state = _ref2.state;
            if (isEnabled()) {
              if (placement !== state.placement) {
                instance.setProps({
                  getReferenceClientRect: function getReferenceClientRect() {
                    return _getReferenceClientRect(state.placement);
                  }
                });
              }
              placement = state.placement;
            }
          }
        };
        function _getReferenceClientRect(placement2) {
          return getInlineBoundingClientRect(getBasePlacement(placement2), reference.getBoundingClientRect(), arrayFrom(reference.getClientRects()), cursorRectIndex);
        }
        function setInternalProps(partialProps) {
          isInternalUpdate = true;
          instance.setProps(partialProps);
          isInternalUpdate = false;
        }
        function addModifier() {
          if (!isInternalUpdate) {
            setInternalProps(getProps(instance.props, modifier));
          }
        }
        return {
          onCreate: addModifier,
          onAfterUpdate: addModifier,
          onTrigger: function onTrigger(_, event) {
            if (isMouseEvent(event)) {
              var rects = arrayFrom(instance.reference.getClientRects());
              var cursorRect = rects.find(function(rect) {
                return rect.left - 2 <= event.clientX && rect.right + 2 >= event.clientX && rect.top - 2 <= event.clientY && rect.bottom + 2 >= event.clientY;
              });
              cursorRectIndex = rects.indexOf(cursorRect);
            }
          },
          onUntrigger: function onUntrigger() {
            cursorRectIndex = -1;
          }
        };
      }
    };
    function getInlineBoundingClientRect(currentBasePlacement, boundingRect, clientRects, cursorRectIndex) {
      if (clientRects.length < 2 || currentBasePlacement === null) {
        return boundingRect;
      }
      if (clientRects.length === 2 && cursorRectIndex >= 0 && clientRects[0].left > clientRects[1].right) {
        return clientRects[cursorRectIndex] || boundingRect;
      }
      switch (currentBasePlacement) {
        case "top":
        case "bottom": {
          var firstRect = clientRects[0];
          var lastRect = clientRects[clientRects.length - 1];
          var isTop = currentBasePlacement === "top";
          var top = firstRect.top;
          var bottom = lastRect.bottom;
          var left = isTop ? firstRect.left : lastRect.left;
          var right = isTop ? firstRect.right : lastRect.right;
          var width = right - left;
          var height = bottom - top;
          return {
            top,
            bottom,
            left,
            right,
            width,
            height
          };
        }
        case "left":
        case "right": {
          var minLeft = Math.min.apply(Math, clientRects.map(function(rects) {
            return rects.left;
          }));
          var maxRight = Math.max.apply(Math, clientRects.map(function(rects) {
            return rects.right;
          }));
          var measureRects = clientRects.filter(function(rect) {
            return currentBasePlacement === "left" ? rect.left === minLeft : rect.right === maxRight;
          });
          var _top = measureRects[0].top;
          var _bottom = measureRects[measureRects.length - 1].bottom;
          var _left = minLeft;
          var _right = maxRight;
          var _width = _right - _left;
          var _height = _bottom - _top;
          return {
            top: _top,
            bottom: _bottom,
            left: _left,
            right: _right,
            width: _width,
            height: _height
          };
        }
        default: {
          return boundingRect;
        }
      }
    }
    var sticky = {
      name: "sticky",
      defaultValue: false,
      fn: function fn(instance) {
        var reference = instance.reference, popper = instance.popper;
        function getReference() {
          return instance.popperInstance ? instance.popperInstance.state.elements.reference : reference;
        }
        function shouldCheck(value) {
          return instance.props.sticky === true || instance.props.sticky === value;
        }
        var prevRefRect = null;
        var prevPopRect = null;
        function updatePosition() {
          var currentRefRect = shouldCheck("reference") ? getReference().getBoundingClientRect() : null;
          var currentPopRect = shouldCheck("popper") ? popper.getBoundingClientRect() : null;
          if (currentRefRect && areRectsDifferent(prevRefRect, currentRefRect) || currentPopRect && areRectsDifferent(prevPopRect, currentPopRect)) {
            if (instance.popperInstance) {
              instance.popperInstance.update();
            }
          }
          prevRefRect = currentRefRect;
          prevPopRect = currentPopRect;
          if (instance.state.isMounted) {
            requestAnimationFrame(updatePosition);
          }
        }
        return {
          onMount: function onMount() {
            if (instance.props.sticky) {
              updatePosition();
            }
          }
        };
      }
    };
    function areRectsDifferent(rectA, rectB) {
      if (rectA && rectB) {
        return rectA.top !== rectB.top || rectA.right !== rectB.right || rectA.bottom !== rectB.bottom || rectA.left !== rectB.left;
      }
      return true;
    }
    tippy2.setDefaultProps({
      render
    });
    exports.animateFill = animateFill;
    exports.createSingleton = createSingleton;
    exports.default = tippy2;
    exports.delegate = delegate;
    exports.followCursor = followCursor2;
    exports.hideAll = hideAll;
    exports.inlinePositioning = inlinePositioning;
    exports.roundArrow = ROUND_ARROW;
    exports.sticky = sticky;
  });
  var import_tippy2 = __toModule2(require_tippy_cjs());
  var import_tippy = __toModule2(require_tippy_cjs());
  var buildConfigFromModifiers = (modifiers) => {
    const config = {
      plugins: []
    };
    const getModifierArgument = (modifier) => {
      return modifiers[modifiers.indexOf(modifier) + 1];
    };
    if (modifiers.includes("animation")) {
      config.animation = getModifierArgument("animation");
    }
    if (modifiers.includes("duration")) {
      config.duration = parseInt(getModifierArgument("duration"));
    }
    if (modifiers.includes("delay")) {
      const delay = getModifierArgument("delay");
      config.delay = delay.includes("-") ? delay.split("-").map((n) => parseInt(n)) : parseInt(delay);
    }
    if (modifiers.includes("cursor")) {
      config.plugins.push(import_tippy.followCursor);
      const next = getModifierArgument("cursor");
      if (["x", "initial"].includes(next)) {
        config.followCursor = next === "x" ? "horizontal" : "initial";
      } else {
        config.followCursor = true;
      }
    }
    if (modifiers.includes("on")) {
      config.trigger = getModifierArgument("on");
    }
    if (modifiers.includes("arrowless")) {
      config.arrow = false;
    }
    if (modifiers.includes("html")) {
      config.allowHTML = true;
    }
    if (modifiers.includes("interactive")) {
      config.interactive = true;
    }
    if (modifiers.includes("border") && config.interactive) {
      config.interactiveBorder = parseInt(getModifierArgument("border"));
    }
    if (modifiers.includes("debounce") && config.interactive) {
      config.interactiveDebounce = parseInt(getModifierArgument("debounce"));
    }
    if (modifiers.includes("max-width")) {
      config.maxWidth = parseInt(getModifierArgument("max-width"));
    }
    if (modifiers.includes("theme")) {
      config.theme = getModifierArgument("theme");
    }
    if (modifiers.includes("placement")) {
      config.placement = getModifierArgument("placement");
    }
    return config;
  };
  function src_default4(Alpine2) {
    Alpine2.magic("tooltip", (el) => {
      return (content, config = {}) => {
        const instance = (0, import_tippy2.default)(el, {
          content,
          trigger: "manual",
          ...config
        });
        instance.show();
        setTimeout(() => {
          instance.hide();
          setTimeout(() => instance.destroy(), config.duration || 300);
        }, config.timeout || 2e3);
      };
    });
    Alpine2.directive("tooltip", (el, { modifiers, expression }, { evaluateLater: evaluateLater2, effect: effect3 }) => {
      const config = modifiers.length > 0 ? buildConfigFromModifiers(modifiers) : {};
      if (!el.__x_tippy) {
        el.__x_tippy = (0, import_tippy2.default)(el, config);
      }
      const enableTooltip = () => el.__x_tippy.enable();
      const disableTooltip = () => el.__x_tippy.disable();
      const setupTooltip = (content) => {
        if (!content) {
          disableTooltip();
        } else {
          enableTooltip();
          el.__x_tippy.setContent(content);
        }
      };
      if (modifiers.includes("raw")) {
        setupTooltip(expression);
      } else {
        const getContent = evaluateLater2(expression);
        effect3(() => {
          getContent((content) => {
            if (typeof content === "object") {
              el.__x_tippy.setProps(content);
              enableTooltip();
            } else {
              setupTooltip(content);
            }
          });
        });
      }
    });
  }
  var module_default4 = src_default4;

  // packages/panels/resources/js/index.js
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(module_default2);
    window.Alpine.plugin(module_default3);
    window.Alpine.plugin(module_default4);
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
    window.Alpine.store(
      "theme",
      window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"
    );
    window.addEventListener("theme-changed", (event) => {
      window.Alpine.store("theme", event.detail);
    });
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", (event) => {
      window.Alpine.store("theme", event.matches ? "dark" : "light");
    });
  });
  window.Alpine = module_default;
  module_default.start();
})();
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2FscGluZWpzL2Rpc3QvbW9kdWxlLmVzbS5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvQGRhbmhhcnJpbi9hbHBpbmUtbW91c2V0cmFwL2Rpc3QvbW9kdWxlLmVzbS5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvQGFscGluZWpzL3BlcnNpc3QvZGlzdC9tb2R1bGUuZXNtLmpzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9AcnlhbmdqY2hhbmRsZXIvYWxwaW5lLXRvb2x0aXAvZGlzdC9tb2R1bGUuZXNtLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9pbmRleC5qcyJdLAogICJzb3VyY2VzQ29udGVudCI6IFsiLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL3NjaGVkdWxlci5qc1xudmFyIGZsdXNoUGVuZGluZyA9IGZhbHNlO1xudmFyIGZsdXNoaW5nID0gZmFsc2U7XG52YXIgcXVldWUgPSBbXTtcbnZhciBsYXN0Rmx1c2hlZEluZGV4ID0gLTE7XG5mdW5jdGlvbiBzY2hlZHVsZXIoY2FsbGJhY2spIHtcbiAgcXVldWVKb2IoY2FsbGJhY2spO1xufVxuZnVuY3Rpb24gcXVldWVKb2Ioam9iKSB7XG4gIGlmICghcXVldWUuaW5jbHVkZXMoam9iKSlcbiAgICBxdWV1ZS5wdXNoKGpvYik7XG4gIHF1ZXVlRmx1c2goKTtcbn1cbmZ1bmN0aW9uIGRlcXVldWVKb2Ioam9iKSB7XG4gIGxldCBpbmRleCA9IHF1ZXVlLmluZGV4T2Yoam9iKTtcbiAgaWYgKGluZGV4ICE9PSAtMSAmJiBpbmRleCA+IGxhc3RGbHVzaGVkSW5kZXgpXG4gICAgcXVldWUuc3BsaWNlKGluZGV4LCAxKTtcbn1cbmZ1bmN0aW9uIHF1ZXVlRmx1c2goKSB7XG4gIGlmICghZmx1c2hpbmcgJiYgIWZsdXNoUGVuZGluZykge1xuICAgIGZsdXNoUGVuZGluZyA9IHRydWU7XG4gICAgcXVldWVNaWNyb3Rhc2soZmx1c2hKb2JzKTtcbiAgfVxufVxuZnVuY3Rpb24gZmx1c2hKb2JzKCkge1xuICBmbHVzaFBlbmRpbmcgPSBmYWxzZTtcbiAgZmx1c2hpbmcgPSB0cnVlO1xuICBmb3IgKGxldCBpID0gMDsgaSA8IHF1ZXVlLmxlbmd0aDsgaSsrKSB7XG4gICAgcXVldWVbaV0oKTtcbiAgICBsYXN0Rmx1c2hlZEluZGV4ID0gaTtcbiAgfVxuICBxdWV1ZS5sZW5ndGggPSAwO1xuICBsYXN0Rmx1c2hlZEluZGV4ID0gLTE7XG4gIGZsdXNoaW5nID0gZmFsc2U7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9yZWFjdGl2aXR5LmpzXG52YXIgcmVhY3RpdmU7XG52YXIgZWZmZWN0O1xudmFyIHJlbGVhc2U7XG52YXIgcmF3O1xudmFyIHNob3VsZFNjaGVkdWxlID0gdHJ1ZTtcbmZ1bmN0aW9uIGRpc2FibGVFZmZlY3RTY2hlZHVsaW5nKGNhbGxiYWNrKSB7XG4gIHNob3VsZFNjaGVkdWxlID0gZmFsc2U7XG4gIGNhbGxiYWNrKCk7XG4gIHNob3VsZFNjaGVkdWxlID0gdHJ1ZTtcbn1cbmZ1bmN0aW9uIHNldFJlYWN0aXZpdHlFbmdpbmUoZW5naW5lKSB7XG4gIHJlYWN0aXZlID0gZW5naW5lLnJlYWN0aXZlO1xuICByZWxlYXNlID0gZW5naW5lLnJlbGVhc2U7XG4gIGVmZmVjdCA9IChjYWxsYmFjaykgPT4gZW5naW5lLmVmZmVjdChjYWxsYmFjaywge3NjaGVkdWxlcjogKHRhc2spID0+IHtcbiAgICBpZiAoc2hvdWxkU2NoZWR1bGUpIHtcbiAgICAgIHNjaGVkdWxlcih0YXNrKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGFzaygpO1xuICAgIH1cbiAgfX0pO1xuICByYXcgPSBlbmdpbmUucmF3O1xufVxuZnVuY3Rpb24gb3ZlcnJpZGVFZmZlY3Qob3ZlcnJpZGUpIHtcbiAgZWZmZWN0ID0gb3ZlcnJpZGU7XG59XG5mdW5jdGlvbiBlbGVtZW50Qm91bmRFZmZlY3QoZWwpIHtcbiAgbGV0IGNsZWFudXAyID0gKCkgPT4ge1xuICB9O1xuICBsZXQgd3JhcHBlZEVmZmVjdCA9IChjYWxsYmFjaykgPT4ge1xuICAgIGxldCBlZmZlY3RSZWZlcmVuY2UgPSBlZmZlY3QoY2FsbGJhY2spO1xuICAgIGlmICghZWwuX3hfZWZmZWN0cykge1xuICAgICAgZWwuX3hfZWZmZWN0cyA9IG5ldyBTZXQoKTtcbiAgICAgIGVsLl94X3J1bkVmZmVjdHMgPSAoKSA9PiB7XG4gICAgICAgIGVsLl94X2VmZmVjdHMuZm9yRWFjaCgoaSkgPT4gaSgpKTtcbiAgICAgIH07XG4gICAgfVxuICAgIGVsLl94X2VmZmVjdHMuYWRkKGVmZmVjdFJlZmVyZW5jZSk7XG4gICAgY2xlYW51cDIgPSAoKSA9PiB7XG4gICAgICBpZiAoZWZmZWN0UmVmZXJlbmNlID09PSB2b2lkIDApXG4gICAgICAgIHJldHVybjtcbiAgICAgIGVsLl94X2VmZmVjdHMuZGVsZXRlKGVmZmVjdFJlZmVyZW5jZSk7XG4gICAgICByZWxlYXNlKGVmZmVjdFJlZmVyZW5jZSk7XG4gICAgfTtcbiAgICByZXR1cm4gZWZmZWN0UmVmZXJlbmNlO1xuICB9O1xuICByZXR1cm4gW3dyYXBwZWRFZmZlY3QsICgpID0+IHtcbiAgICBjbGVhbnVwMigpO1xuICB9XTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL211dGF0aW9uLmpzXG52YXIgb25BdHRyaWJ1dGVBZGRlZHMgPSBbXTtcbnZhciBvbkVsUmVtb3ZlZHMgPSBbXTtcbnZhciBvbkVsQWRkZWRzID0gW107XG5mdW5jdGlvbiBvbkVsQWRkZWQoY2FsbGJhY2spIHtcbiAgb25FbEFkZGVkcy5wdXNoKGNhbGxiYWNrKTtcbn1cbmZ1bmN0aW9uIG9uRWxSZW1vdmVkKGVsLCBjYWxsYmFjaykge1xuICBpZiAodHlwZW9mIGNhbGxiYWNrID09PSBcImZ1bmN0aW9uXCIpIHtcbiAgICBpZiAoIWVsLl94X2NsZWFudXBzKVxuICAgICAgZWwuX3hfY2xlYW51cHMgPSBbXTtcbiAgICBlbC5feF9jbGVhbnVwcy5wdXNoKGNhbGxiYWNrKTtcbiAgfSBlbHNlIHtcbiAgICBjYWxsYmFjayA9IGVsO1xuICAgIG9uRWxSZW1vdmVkcy5wdXNoKGNhbGxiYWNrKTtcbiAgfVxufVxuZnVuY3Rpb24gb25BdHRyaWJ1dGVzQWRkZWQoY2FsbGJhY2spIHtcbiAgb25BdHRyaWJ1dGVBZGRlZHMucHVzaChjYWxsYmFjayk7XG59XG5mdW5jdGlvbiBvbkF0dHJpYnV0ZVJlbW92ZWQoZWwsIG5hbWUsIGNhbGxiYWNrKSB7XG4gIGlmICghZWwuX3hfYXR0cmlidXRlQ2xlYW51cHMpXG4gICAgZWwuX3hfYXR0cmlidXRlQ2xlYW51cHMgPSB7fTtcbiAgaWYgKCFlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXSlcbiAgICBlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXSA9IFtdO1xuICBlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXS5wdXNoKGNhbGxiYWNrKTtcbn1cbmZ1bmN0aW9uIGNsZWFudXBBdHRyaWJ1dGVzKGVsLCBuYW1lcykge1xuICBpZiAoIWVsLl94X2F0dHJpYnV0ZUNsZWFudXBzKVxuICAgIHJldHVybjtcbiAgT2JqZWN0LmVudHJpZXMoZWwuX3hfYXR0cmlidXRlQ2xlYW51cHMpLmZvckVhY2goKFtuYW1lLCB2YWx1ZV0pID0+IHtcbiAgICBpZiAobmFtZXMgPT09IHZvaWQgMCB8fCBuYW1lcy5pbmNsdWRlcyhuYW1lKSkge1xuICAgICAgdmFsdWUuZm9yRWFjaCgoaSkgPT4gaSgpKTtcbiAgICAgIGRlbGV0ZSBlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXTtcbiAgICB9XG4gIH0pO1xufVxudmFyIG9ic2VydmVyID0gbmV3IE11dGF0aW9uT2JzZXJ2ZXIob25NdXRhdGUpO1xudmFyIGN1cnJlbnRseU9ic2VydmluZyA9IGZhbHNlO1xuZnVuY3Rpb24gc3RhcnRPYnNlcnZpbmdNdXRhdGlvbnMoKSB7XG4gIG9ic2VydmVyLm9ic2VydmUoZG9jdW1lbnQsIHtzdWJ0cmVlOiB0cnVlLCBjaGlsZExpc3Q6IHRydWUsIGF0dHJpYnV0ZXM6IHRydWUsIGF0dHJpYnV0ZU9sZFZhbHVlOiB0cnVlfSk7XG4gIGN1cnJlbnRseU9ic2VydmluZyA9IHRydWU7XG59XG5mdW5jdGlvbiBzdG9wT2JzZXJ2aW5nTXV0YXRpb25zKCkge1xuICBmbHVzaE9ic2VydmVyKCk7XG4gIG9ic2VydmVyLmRpc2Nvbm5lY3QoKTtcbiAgY3VycmVudGx5T2JzZXJ2aW5nID0gZmFsc2U7XG59XG52YXIgcmVjb3JkUXVldWUgPSBbXTtcbnZhciB3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlID0gZmFsc2U7XG5mdW5jdGlvbiBmbHVzaE9ic2VydmVyKCkge1xuICByZWNvcmRRdWV1ZSA9IHJlY29yZFF1ZXVlLmNvbmNhdChvYnNlcnZlci50YWtlUmVjb3JkcygpKTtcbiAgaWYgKHJlY29yZFF1ZXVlLmxlbmd0aCAmJiAhd2lsbFByb2Nlc3NSZWNvcmRRdWV1ZSkge1xuICAgIHdpbGxQcm9jZXNzUmVjb3JkUXVldWUgPSB0cnVlO1xuICAgIHF1ZXVlTWljcm90YXNrKCgpID0+IHtcbiAgICAgIHByb2Nlc3NSZWNvcmRRdWV1ZSgpO1xuICAgICAgd2lsbFByb2Nlc3NSZWNvcmRRdWV1ZSA9IGZhbHNlO1xuICAgIH0pO1xuICB9XG59XG5mdW5jdGlvbiBwcm9jZXNzUmVjb3JkUXVldWUoKSB7XG4gIG9uTXV0YXRlKHJlY29yZFF1ZXVlKTtcbiAgcmVjb3JkUXVldWUubGVuZ3RoID0gMDtcbn1cbmZ1bmN0aW9uIG11dGF0ZURvbShjYWxsYmFjaykge1xuICBpZiAoIWN1cnJlbnRseU9ic2VydmluZylcbiAgICByZXR1cm4gY2FsbGJhY2soKTtcbiAgc3RvcE9ic2VydmluZ011dGF0aW9ucygpO1xuICBsZXQgcmVzdWx0ID0gY2FsbGJhY2soKTtcbiAgc3RhcnRPYnNlcnZpbmdNdXRhdGlvbnMoKTtcbiAgcmV0dXJuIHJlc3VsdDtcbn1cbnZhciBpc0NvbGxlY3RpbmcgPSBmYWxzZTtcbnZhciBkZWZlcnJlZE11dGF0aW9ucyA9IFtdO1xuZnVuY3Rpb24gZGVmZXJNdXRhdGlvbnMoKSB7XG4gIGlzQ29sbGVjdGluZyA9IHRydWU7XG59XG5mdW5jdGlvbiBmbHVzaEFuZFN0b3BEZWZlcnJpbmdNdXRhdGlvbnMoKSB7XG4gIGlzQ29sbGVjdGluZyA9IGZhbHNlO1xuICBvbk11dGF0ZShkZWZlcnJlZE11dGF0aW9ucyk7XG4gIGRlZmVycmVkTXV0YXRpb25zID0gW107XG59XG5mdW5jdGlvbiBvbk11dGF0ZShtdXRhdGlvbnMpIHtcbiAgaWYgKGlzQ29sbGVjdGluZykge1xuICAgIGRlZmVycmVkTXV0YXRpb25zID0gZGVmZXJyZWRNdXRhdGlvbnMuY29uY2F0KG11dGF0aW9ucyk7XG4gICAgcmV0dXJuO1xuICB9XG4gIGxldCBhZGRlZE5vZGVzID0gW107XG4gIGxldCByZW1vdmVkTm9kZXMgPSBbXTtcbiAgbGV0IGFkZGVkQXR0cmlidXRlcyA9IG5ldyBNYXAoKTtcbiAgbGV0IHJlbW92ZWRBdHRyaWJ1dGVzID0gbmV3IE1hcCgpO1xuICBmb3IgKGxldCBpID0gMDsgaSA8IG11dGF0aW9ucy5sZW5ndGg7IGkrKykge1xuICAgIGlmIChtdXRhdGlvbnNbaV0udGFyZ2V0Ll94X2lnbm9yZU11dGF0aW9uT2JzZXJ2ZXIpXG4gICAgICBjb250aW51ZTtcbiAgICBpZiAobXV0YXRpb25zW2ldLnR5cGUgPT09IFwiY2hpbGRMaXN0XCIpIHtcbiAgICAgIG11dGF0aW9uc1tpXS5hZGRlZE5vZGVzLmZvckVhY2goKG5vZGUpID0+IG5vZGUubm9kZVR5cGUgPT09IDEgJiYgYWRkZWROb2Rlcy5wdXNoKG5vZGUpKTtcbiAgICAgIG11dGF0aW9uc1tpXS5yZW1vdmVkTm9kZXMuZm9yRWFjaCgobm9kZSkgPT4gbm9kZS5ub2RlVHlwZSA9PT0gMSAmJiByZW1vdmVkTm9kZXMucHVzaChub2RlKSk7XG4gICAgfVxuICAgIGlmIChtdXRhdGlvbnNbaV0udHlwZSA9PT0gXCJhdHRyaWJ1dGVzXCIpIHtcbiAgICAgIGxldCBlbCA9IG11dGF0aW9uc1tpXS50YXJnZXQ7XG4gICAgICBsZXQgbmFtZSA9IG11dGF0aW9uc1tpXS5hdHRyaWJ1dGVOYW1lO1xuICAgICAgbGV0IG9sZFZhbHVlID0gbXV0YXRpb25zW2ldLm9sZFZhbHVlO1xuICAgICAgbGV0IGFkZDIgPSAoKSA9PiB7XG4gICAgICAgIGlmICghYWRkZWRBdHRyaWJ1dGVzLmhhcyhlbCkpXG4gICAgICAgICAgYWRkZWRBdHRyaWJ1dGVzLnNldChlbCwgW10pO1xuICAgICAgICBhZGRlZEF0dHJpYnV0ZXMuZ2V0KGVsKS5wdXNoKHtuYW1lLCB2YWx1ZTogZWwuZ2V0QXR0cmlidXRlKG5hbWUpfSk7XG4gICAgICB9O1xuICAgICAgbGV0IHJlbW92ZSA9ICgpID0+IHtcbiAgICAgICAgaWYgKCFyZW1vdmVkQXR0cmlidXRlcy5oYXMoZWwpKVxuICAgICAgICAgIHJlbW92ZWRBdHRyaWJ1dGVzLnNldChlbCwgW10pO1xuICAgICAgICByZW1vdmVkQXR0cmlidXRlcy5nZXQoZWwpLnB1c2gobmFtZSk7XG4gICAgICB9O1xuICAgICAgaWYgKGVsLmhhc0F0dHJpYnV0ZShuYW1lKSAmJiBvbGRWYWx1ZSA9PT0gbnVsbCkge1xuICAgICAgICBhZGQyKCk7XG4gICAgICB9IGVsc2UgaWYgKGVsLmhhc0F0dHJpYnV0ZShuYW1lKSkge1xuICAgICAgICByZW1vdmUoKTtcbiAgICAgICAgYWRkMigpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgcmVtb3ZlKCk7XG4gICAgICB9XG4gICAgfVxuICB9XG4gIHJlbW92ZWRBdHRyaWJ1dGVzLmZvckVhY2goKGF0dHJzLCBlbCkgPT4ge1xuICAgIGNsZWFudXBBdHRyaWJ1dGVzKGVsLCBhdHRycyk7XG4gIH0pO1xuICBhZGRlZEF0dHJpYnV0ZXMuZm9yRWFjaCgoYXR0cnMsIGVsKSA9PiB7XG4gICAgb25BdHRyaWJ1dGVBZGRlZHMuZm9yRWFjaCgoaSkgPT4gaShlbCwgYXR0cnMpKTtcbiAgfSk7XG4gIGZvciAobGV0IG5vZGUgb2YgcmVtb3ZlZE5vZGVzKSB7XG4gICAgaWYgKGFkZGVkTm9kZXMuaW5jbHVkZXMobm9kZSkpXG4gICAgICBjb250aW51ZTtcbiAgICBvbkVsUmVtb3ZlZHMuZm9yRWFjaCgoaSkgPT4gaShub2RlKSk7XG4gICAgaWYgKG5vZGUuX3hfY2xlYW51cHMpIHtcbiAgICAgIHdoaWxlIChub2RlLl94X2NsZWFudXBzLmxlbmd0aClcbiAgICAgICAgbm9kZS5feF9jbGVhbnVwcy5wb3AoKSgpO1xuICAgIH1cbiAgfVxuICBhZGRlZE5vZGVzLmZvckVhY2goKG5vZGUpID0+IHtcbiAgICBub2RlLl94X2lnbm9yZVNlbGYgPSB0cnVlO1xuICAgIG5vZGUuX3hfaWdub3JlID0gdHJ1ZTtcbiAgfSk7XG4gIGZvciAobGV0IG5vZGUgb2YgYWRkZWROb2Rlcykge1xuICAgIGlmIChyZW1vdmVkTm9kZXMuaW5jbHVkZXMobm9kZSkpXG4gICAgICBjb250aW51ZTtcbiAgICBpZiAoIW5vZGUuaXNDb25uZWN0ZWQpXG4gICAgICBjb250aW51ZTtcbiAgICBkZWxldGUgbm9kZS5feF9pZ25vcmVTZWxmO1xuICAgIGRlbGV0ZSBub2RlLl94X2lnbm9yZTtcbiAgICBvbkVsQWRkZWRzLmZvckVhY2goKGkpID0+IGkobm9kZSkpO1xuICAgIG5vZGUuX3hfaWdub3JlID0gdHJ1ZTtcbiAgICBub2RlLl94X2lnbm9yZVNlbGYgPSB0cnVlO1xuICB9XG4gIGFkZGVkTm9kZXMuZm9yRWFjaCgobm9kZSkgPT4ge1xuICAgIGRlbGV0ZSBub2RlLl94X2lnbm9yZVNlbGY7XG4gICAgZGVsZXRlIG5vZGUuX3hfaWdub3JlO1xuICB9KTtcbiAgYWRkZWROb2RlcyA9IG51bGw7XG4gIHJlbW92ZWROb2RlcyA9IG51bGw7XG4gIGFkZGVkQXR0cmlidXRlcyA9IG51bGw7XG4gIHJlbW92ZWRBdHRyaWJ1dGVzID0gbnVsbDtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL3Njb3BlLmpzXG5mdW5jdGlvbiBzY29wZShub2RlKSB7XG4gIHJldHVybiBtZXJnZVByb3hpZXMoY2xvc2VzdERhdGFTdGFjayhub2RlKSk7XG59XG5mdW5jdGlvbiBhZGRTY29wZVRvTm9kZShub2RlLCBkYXRhMiwgcmVmZXJlbmNlTm9kZSkge1xuICBub2RlLl94X2RhdGFTdGFjayA9IFtkYXRhMiwgLi4uY2xvc2VzdERhdGFTdGFjayhyZWZlcmVuY2VOb2RlIHx8IG5vZGUpXTtcbiAgcmV0dXJuICgpID0+IHtcbiAgICBub2RlLl94X2RhdGFTdGFjayA9IG5vZGUuX3hfZGF0YVN0YWNrLmZpbHRlcigoaSkgPT4gaSAhPT0gZGF0YTIpO1xuICB9O1xufVxuZnVuY3Rpb24gY2xvc2VzdERhdGFTdGFjayhub2RlKSB7XG4gIGlmIChub2RlLl94X2RhdGFTdGFjaylcbiAgICByZXR1cm4gbm9kZS5feF9kYXRhU3RhY2s7XG4gIGlmICh0eXBlb2YgU2hhZG93Um9vdCA9PT0gXCJmdW5jdGlvblwiICYmIG5vZGUgaW5zdGFuY2VvZiBTaGFkb3dSb290KSB7XG4gICAgcmV0dXJuIGNsb3Nlc3REYXRhU3RhY2sobm9kZS5ob3N0KTtcbiAgfVxuICBpZiAoIW5vZGUucGFyZW50Tm9kZSkge1xuICAgIHJldHVybiBbXTtcbiAgfVxuICByZXR1cm4gY2xvc2VzdERhdGFTdGFjayhub2RlLnBhcmVudE5vZGUpO1xufVxuZnVuY3Rpb24gbWVyZ2VQcm94aWVzKG9iamVjdHMpIHtcbiAgbGV0IHRoaXNQcm94eSA9IG5ldyBQcm94eSh7fSwge1xuICAgIG93bktleXM6ICgpID0+IHtcbiAgICAgIHJldHVybiBBcnJheS5mcm9tKG5ldyBTZXQob2JqZWN0cy5mbGF0TWFwKChpKSA9PiBPYmplY3Qua2V5cyhpKSkpKTtcbiAgICB9LFxuICAgIGhhczogKHRhcmdldCwgbmFtZSkgPT4ge1xuICAgICAgcmV0dXJuIG9iamVjdHMuc29tZSgob2JqKSA9PiBvYmouaGFzT3duUHJvcGVydHkobmFtZSkpO1xuICAgIH0sXG4gICAgZ2V0OiAodGFyZ2V0LCBuYW1lKSA9PiB7XG4gICAgICByZXR1cm4gKG9iamVjdHMuZmluZCgob2JqKSA9PiB7XG4gICAgICAgIGlmIChvYmouaGFzT3duUHJvcGVydHkobmFtZSkpIHtcbiAgICAgICAgICBsZXQgZGVzY3JpcHRvciA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3Iob2JqLCBuYW1lKTtcbiAgICAgICAgICBpZiAoZGVzY3JpcHRvci5nZXQgJiYgZGVzY3JpcHRvci5nZXQuX3hfYWxyZWFkeUJvdW5kIHx8IGRlc2NyaXB0b3Iuc2V0ICYmIGRlc2NyaXB0b3Iuc2V0Ll94X2FscmVhZHlCb3VuZCkge1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmICgoZGVzY3JpcHRvci5nZXQgfHwgZGVzY3JpcHRvci5zZXQpICYmIGRlc2NyaXB0b3IuZW51bWVyYWJsZSkge1xuICAgICAgICAgICAgbGV0IGdldHRlciA9IGRlc2NyaXB0b3IuZ2V0O1xuICAgICAgICAgICAgbGV0IHNldHRlciA9IGRlc2NyaXB0b3Iuc2V0O1xuICAgICAgICAgICAgbGV0IHByb3BlcnR5ID0gZGVzY3JpcHRvcjtcbiAgICAgICAgICAgIGdldHRlciA9IGdldHRlciAmJiBnZXR0ZXIuYmluZCh0aGlzUHJveHkpO1xuICAgICAgICAgICAgc2V0dGVyID0gc2V0dGVyICYmIHNldHRlci5iaW5kKHRoaXNQcm94eSk7XG4gICAgICAgICAgICBpZiAoZ2V0dGVyKVxuICAgICAgICAgICAgICBnZXR0ZXIuX3hfYWxyZWFkeUJvdW5kID0gdHJ1ZTtcbiAgICAgICAgICAgIGlmIChzZXR0ZXIpXG4gICAgICAgICAgICAgIHNldHRlci5feF9hbHJlYWR5Qm91bmQgPSB0cnVlO1xuICAgICAgICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KG9iaiwgbmFtZSwge1xuICAgICAgICAgICAgICAuLi5wcm9wZXJ0eSxcbiAgICAgICAgICAgICAgZ2V0OiBnZXR0ZXIsXG4gICAgICAgICAgICAgIHNldDogc2V0dGVyXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG4gICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfSkgfHwge30pW25hbWVdO1xuICAgIH0sXG4gICAgc2V0OiAodGFyZ2V0LCBuYW1lLCB2YWx1ZSkgPT4ge1xuICAgICAgbGV0IGNsb3Nlc3RPYmplY3RXaXRoS2V5ID0gb2JqZWN0cy5maW5kKChvYmopID0+IG9iai5oYXNPd25Qcm9wZXJ0eShuYW1lKSk7XG4gICAgICBpZiAoY2xvc2VzdE9iamVjdFdpdGhLZXkpIHtcbiAgICAgICAgY2xvc2VzdE9iamVjdFdpdGhLZXlbbmFtZV0gPSB2YWx1ZTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIG9iamVjdHNbb2JqZWN0cy5sZW5ndGggLSAxXVtuYW1lXSA9IHZhbHVlO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuICB9KTtcbiAgcmV0dXJuIHRoaXNQcm94eTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2ludGVyY2VwdG9yLmpzXG5mdW5jdGlvbiBpbml0SW50ZXJjZXB0b3JzKGRhdGEyKSB7XG4gIGxldCBpc09iamVjdDIgPSAodmFsKSA9PiB0eXBlb2YgdmFsID09PSBcIm9iamVjdFwiICYmICFBcnJheS5pc0FycmF5KHZhbCkgJiYgdmFsICE9PSBudWxsO1xuICBsZXQgcmVjdXJzZSA9IChvYmosIGJhc2VQYXRoID0gXCJcIikgPT4ge1xuICAgIE9iamVjdC5lbnRyaWVzKE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3JzKG9iaikpLmZvckVhY2goKFtrZXksIHt2YWx1ZSwgZW51bWVyYWJsZX1dKSA9PiB7XG4gICAgICBpZiAoZW51bWVyYWJsZSA9PT0gZmFsc2UgfHwgdmFsdWUgPT09IHZvaWQgMClcbiAgICAgICAgcmV0dXJuO1xuICAgICAgbGV0IHBhdGggPSBiYXNlUGF0aCA9PT0gXCJcIiA/IGtleSA6IGAke2Jhc2VQYXRofS4ke2tleX1gO1xuICAgICAgaWYgKHR5cGVvZiB2YWx1ZSA9PT0gXCJvYmplY3RcIiAmJiB2YWx1ZSAhPT0gbnVsbCAmJiB2YWx1ZS5feF9pbnRlcmNlcHRvcikge1xuICAgICAgICBvYmpba2V5XSA9IHZhbHVlLmluaXRpYWxpemUoZGF0YTIsIHBhdGgsIGtleSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBpZiAoaXNPYmplY3QyKHZhbHVlKSAmJiB2YWx1ZSAhPT0gb2JqICYmICEodmFsdWUgaW5zdGFuY2VvZiBFbGVtZW50KSkge1xuICAgICAgICAgIHJlY3Vyc2UodmFsdWUsIHBhdGgpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSk7XG4gIH07XG4gIHJldHVybiByZWN1cnNlKGRhdGEyKTtcbn1cbmZ1bmN0aW9uIGludGVyY2VwdG9yKGNhbGxiYWNrLCBtdXRhdGVPYmogPSAoKSA9PiB7XG59KSB7XG4gIGxldCBvYmogPSB7XG4gICAgaW5pdGlhbFZhbHVlOiB2b2lkIDAsXG4gICAgX3hfaW50ZXJjZXB0b3I6IHRydWUsXG4gICAgaW5pdGlhbGl6ZShkYXRhMiwgcGF0aCwga2V5KSB7XG4gICAgICByZXR1cm4gY2FsbGJhY2sodGhpcy5pbml0aWFsVmFsdWUsICgpID0+IGdldChkYXRhMiwgcGF0aCksICh2YWx1ZSkgPT4gc2V0KGRhdGEyLCBwYXRoLCB2YWx1ZSksIHBhdGgsIGtleSk7XG4gICAgfVxuICB9O1xuICBtdXRhdGVPYmoob2JqKTtcbiAgcmV0dXJuIChpbml0aWFsVmFsdWUpID0+IHtcbiAgICBpZiAodHlwZW9mIGluaXRpYWxWYWx1ZSA9PT0gXCJvYmplY3RcIiAmJiBpbml0aWFsVmFsdWUgIT09IG51bGwgJiYgaW5pdGlhbFZhbHVlLl94X2ludGVyY2VwdG9yKSB7XG4gICAgICBsZXQgaW5pdGlhbGl6ZSA9IG9iai5pbml0aWFsaXplLmJpbmQob2JqKTtcbiAgICAgIG9iai5pbml0aWFsaXplID0gKGRhdGEyLCBwYXRoLCBrZXkpID0+IHtcbiAgICAgICAgbGV0IGlubmVyVmFsdWUgPSBpbml0aWFsVmFsdWUuaW5pdGlhbGl6ZShkYXRhMiwgcGF0aCwga2V5KTtcbiAgICAgICAgb2JqLmluaXRpYWxWYWx1ZSA9IGlubmVyVmFsdWU7XG4gICAgICAgIHJldHVybiBpbml0aWFsaXplKGRhdGEyLCBwYXRoLCBrZXkpO1xuICAgICAgfTtcbiAgICB9IGVsc2Uge1xuICAgICAgb2JqLmluaXRpYWxWYWx1ZSA9IGluaXRpYWxWYWx1ZTtcbiAgICB9XG4gICAgcmV0dXJuIG9iajtcbiAgfTtcbn1cbmZ1bmN0aW9uIGdldChvYmosIHBhdGgpIHtcbiAgcmV0dXJuIHBhdGguc3BsaXQoXCIuXCIpLnJlZHVjZSgoY2FycnksIHNlZ21lbnQpID0+IGNhcnJ5W3NlZ21lbnRdLCBvYmopO1xufVxuZnVuY3Rpb24gc2V0KG9iaiwgcGF0aCwgdmFsdWUpIHtcbiAgaWYgKHR5cGVvZiBwYXRoID09PSBcInN0cmluZ1wiKVxuICAgIHBhdGggPSBwYXRoLnNwbGl0KFwiLlwiKTtcbiAgaWYgKHBhdGgubGVuZ3RoID09PSAxKVxuICAgIG9ialtwYXRoWzBdXSA9IHZhbHVlO1xuICBlbHNlIGlmIChwYXRoLmxlbmd0aCA9PT0gMClcbiAgICB0aHJvdyBlcnJvcjtcbiAgZWxzZSB7XG4gICAgaWYgKG9ialtwYXRoWzBdXSlcbiAgICAgIHJldHVybiBzZXQob2JqW3BhdGhbMF1dLCBwYXRoLnNsaWNlKDEpLCB2YWx1ZSk7XG4gICAgZWxzZSB7XG4gICAgICBvYmpbcGF0aFswXV0gPSB7fTtcbiAgICAgIHJldHVybiBzZXQob2JqW3BhdGhbMF1dLCBwYXRoLnNsaWNlKDEpLCB2YWx1ZSk7XG4gICAgfVxuICB9XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9tYWdpY3MuanNcbnZhciBtYWdpY3MgPSB7fTtcbmZ1bmN0aW9uIG1hZ2ljKG5hbWUsIGNhbGxiYWNrKSB7XG4gIG1hZ2ljc1tuYW1lXSA9IGNhbGxiYWNrO1xufVxuZnVuY3Rpb24gaW5qZWN0TWFnaWNzKG9iaiwgZWwpIHtcbiAgT2JqZWN0LmVudHJpZXMobWFnaWNzKS5mb3JFYWNoKChbbmFtZSwgY2FsbGJhY2tdKSA9PiB7XG4gICAgbGV0IG1lbW9pemVkVXRpbGl0aWVzID0gbnVsbDtcbiAgICBmdW5jdGlvbiBnZXRVdGlsaXRpZXMoKSB7XG4gICAgICBpZiAobWVtb2l6ZWRVdGlsaXRpZXMpIHtcbiAgICAgICAgcmV0dXJuIG1lbW9pemVkVXRpbGl0aWVzO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgbGV0IFt1dGlsaXRpZXMsIGNsZWFudXAyXSA9IGdldEVsZW1lbnRCb3VuZFV0aWxpdGllcyhlbCk7XG4gICAgICAgIG1lbW9pemVkVXRpbGl0aWVzID0ge2ludGVyY2VwdG9yLCAuLi51dGlsaXRpZXN9O1xuICAgICAgICBvbkVsUmVtb3ZlZChlbCwgY2xlYW51cDIpO1xuICAgICAgICByZXR1cm4gbWVtb2l6ZWRVdGlsaXRpZXM7XG4gICAgICB9XG4gICAgfVxuICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShvYmosIGAkJHtuYW1lfWAsIHtcbiAgICAgIGdldCgpIHtcbiAgICAgICAgcmV0dXJuIGNhbGxiYWNrKGVsLCBnZXRVdGlsaXRpZXMoKSk7XG4gICAgICB9LFxuICAgICAgZW51bWVyYWJsZTogZmFsc2VcbiAgICB9KTtcbiAgfSk7XG4gIHJldHVybiBvYmo7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy91dGlscy9lcnJvci5qc1xuZnVuY3Rpb24gdHJ5Q2F0Y2goZWwsIGV4cHJlc3Npb24sIGNhbGxiYWNrLCAuLi5hcmdzKSB7XG4gIHRyeSB7XG4gICAgcmV0dXJuIGNhbGxiYWNrKC4uLmFyZ3MpO1xuICB9IGNhdGNoIChlKSB7XG4gICAgaGFuZGxlRXJyb3IoZSwgZWwsIGV4cHJlc3Npb24pO1xuICB9XG59XG5mdW5jdGlvbiBoYW5kbGVFcnJvcihlcnJvcjIsIGVsLCBleHByZXNzaW9uID0gdm9pZCAwKSB7XG4gIE9iamVjdC5hc3NpZ24oZXJyb3IyLCB7ZWwsIGV4cHJlc3Npb259KTtcbiAgY29uc29sZS53YXJuKGBBbHBpbmUgRXhwcmVzc2lvbiBFcnJvcjogJHtlcnJvcjIubWVzc2FnZX1cblxuJHtleHByZXNzaW9uID8gJ0V4cHJlc3Npb246IFwiJyArIGV4cHJlc3Npb24gKyAnXCJcXG5cXG4nIDogXCJcIn1gLCBlbCk7XG4gIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgIHRocm93IGVycm9yMjtcbiAgfSwgMCk7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9ldmFsdWF0b3IuanNcbnZhciBzaG91bGRBdXRvRXZhbHVhdGVGdW5jdGlvbnMgPSB0cnVlO1xuZnVuY3Rpb24gZG9udEF1dG9FdmFsdWF0ZUZ1bmN0aW9ucyhjYWxsYmFjaykge1xuICBsZXQgY2FjaGUgPSBzaG91bGRBdXRvRXZhbHVhdGVGdW5jdGlvbnM7XG4gIHNob3VsZEF1dG9FdmFsdWF0ZUZ1bmN0aW9ucyA9IGZhbHNlO1xuICBjYWxsYmFjaygpO1xuICBzaG91bGRBdXRvRXZhbHVhdGVGdW5jdGlvbnMgPSBjYWNoZTtcbn1cbmZ1bmN0aW9uIGV2YWx1YXRlKGVsLCBleHByZXNzaW9uLCBleHRyYXMgPSB7fSkge1xuICBsZXQgcmVzdWx0O1xuICBldmFsdWF0ZUxhdGVyKGVsLCBleHByZXNzaW9uKSgodmFsdWUpID0+IHJlc3VsdCA9IHZhbHVlLCBleHRyYXMpO1xuICByZXR1cm4gcmVzdWx0O1xufVxuZnVuY3Rpb24gZXZhbHVhdGVMYXRlciguLi5hcmdzKSB7XG4gIHJldHVybiB0aGVFdmFsdWF0b3JGdW5jdGlvbiguLi5hcmdzKTtcbn1cbnZhciB0aGVFdmFsdWF0b3JGdW5jdGlvbiA9IG5vcm1hbEV2YWx1YXRvcjtcbmZ1bmN0aW9uIHNldEV2YWx1YXRvcihuZXdFdmFsdWF0b3IpIHtcbiAgdGhlRXZhbHVhdG9yRnVuY3Rpb24gPSBuZXdFdmFsdWF0b3I7XG59XG5mdW5jdGlvbiBub3JtYWxFdmFsdWF0b3IoZWwsIGV4cHJlc3Npb24pIHtcbiAgbGV0IG92ZXJyaWRkZW5NYWdpY3MgPSB7fTtcbiAgaW5qZWN0TWFnaWNzKG92ZXJyaWRkZW5NYWdpY3MsIGVsKTtcbiAgbGV0IGRhdGFTdGFjayA9IFtvdmVycmlkZGVuTWFnaWNzLCAuLi5jbG9zZXN0RGF0YVN0YWNrKGVsKV07XG4gIGxldCBldmFsdWF0b3IgPSB0eXBlb2YgZXhwcmVzc2lvbiA9PT0gXCJmdW5jdGlvblwiID8gZ2VuZXJhdGVFdmFsdWF0b3JGcm9tRnVuY3Rpb24oZGF0YVN0YWNrLCBleHByZXNzaW9uKSA6IGdlbmVyYXRlRXZhbHVhdG9yRnJvbVN0cmluZyhkYXRhU3RhY2ssIGV4cHJlc3Npb24sIGVsKTtcbiAgcmV0dXJuIHRyeUNhdGNoLmJpbmQobnVsbCwgZWwsIGV4cHJlc3Npb24sIGV2YWx1YXRvcik7XG59XG5mdW5jdGlvbiBnZW5lcmF0ZUV2YWx1YXRvckZyb21GdW5jdGlvbihkYXRhU3RhY2ssIGZ1bmMpIHtcbiAgcmV0dXJuIChyZWNlaXZlciA9ICgpID0+IHtcbiAgfSwge3Njb3BlOiBzY29wZTIgPSB7fSwgcGFyYW1zID0gW119ID0ge30pID0+IHtcbiAgICBsZXQgcmVzdWx0ID0gZnVuYy5hcHBseShtZXJnZVByb3hpZXMoW3Njb3BlMiwgLi4uZGF0YVN0YWNrXSksIHBhcmFtcyk7XG4gICAgcnVuSWZUeXBlT2ZGdW5jdGlvbihyZWNlaXZlciwgcmVzdWx0KTtcbiAgfTtcbn1cbnZhciBldmFsdWF0b3JNZW1vID0ge307XG5mdW5jdGlvbiBnZW5lcmF0ZUZ1bmN0aW9uRnJvbVN0cmluZyhleHByZXNzaW9uLCBlbCkge1xuICBpZiAoZXZhbHVhdG9yTWVtb1tleHByZXNzaW9uXSkge1xuICAgIHJldHVybiBldmFsdWF0b3JNZW1vW2V4cHJlc3Npb25dO1xuICB9XG4gIGxldCBBc3luY0Z1bmN0aW9uID0gT2JqZWN0LmdldFByb3RvdHlwZU9mKGFzeW5jIGZ1bmN0aW9uKCkge1xuICB9KS5jb25zdHJ1Y3RvcjtcbiAgbGV0IHJpZ2h0U2lkZVNhZmVFeHByZXNzaW9uID0gL15bXFxuXFxzXSppZi4qXFwoLipcXCkvLnRlc3QoZXhwcmVzc2lvbikgfHwgL14obGV0fGNvbnN0KVxccy8udGVzdChleHByZXNzaW9uKSA/IGAoYXN5bmMoKT0+eyAke2V4cHJlc3Npb259IH0pKClgIDogZXhwcmVzc2lvbjtcbiAgY29uc3Qgc2FmZUFzeW5jRnVuY3Rpb24gPSAoKSA9PiB7XG4gICAgdHJ5IHtcbiAgICAgIHJldHVybiBuZXcgQXN5bmNGdW5jdGlvbihbXCJfX3NlbGZcIiwgXCJzY29wZVwiXSwgYHdpdGggKHNjb3BlKSB7IF9fc2VsZi5yZXN1bHQgPSAke3JpZ2h0U2lkZVNhZmVFeHByZXNzaW9ufSB9OyBfX3NlbGYuZmluaXNoZWQgPSB0cnVlOyByZXR1cm4gX19zZWxmLnJlc3VsdDtgKTtcbiAgICB9IGNhdGNoIChlcnJvcjIpIHtcbiAgICAgIGhhbmRsZUVycm9yKGVycm9yMiwgZWwsIGV4cHJlc3Npb24pO1xuICAgICAgcmV0dXJuIFByb21pc2UucmVzb2x2ZSgpO1xuICAgIH1cbiAgfTtcbiAgbGV0IGZ1bmMgPSBzYWZlQXN5bmNGdW5jdGlvbigpO1xuICBldmFsdWF0b3JNZW1vW2V4cHJlc3Npb25dID0gZnVuYztcbiAgcmV0dXJuIGZ1bmM7XG59XG5mdW5jdGlvbiBnZW5lcmF0ZUV2YWx1YXRvckZyb21TdHJpbmcoZGF0YVN0YWNrLCBleHByZXNzaW9uLCBlbCkge1xuICBsZXQgZnVuYyA9IGdlbmVyYXRlRnVuY3Rpb25Gcm9tU3RyaW5nKGV4cHJlc3Npb24sIGVsKTtcbiAgcmV0dXJuIChyZWNlaXZlciA9ICgpID0+IHtcbiAgfSwge3Njb3BlOiBzY29wZTIgPSB7fSwgcGFyYW1zID0gW119ID0ge30pID0+IHtcbiAgICBmdW5jLnJlc3VsdCA9IHZvaWQgMDtcbiAgICBmdW5jLmZpbmlzaGVkID0gZmFsc2U7XG4gICAgbGV0IGNvbXBsZXRlU2NvcGUgPSBtZXJnZVByb3hpZXMoW3Njb3BlMiwgLi4uZGF0YVN0YWNrXSk7XG4gICAgaWYgKHR5cGVvZiBmdW5jID09PSBcImZ1bmN0aW9uXCIpIHtcbiAgICAgIGxldCBwcm9taXNlID0gZnVuYyhmdW5jLCBjb21wbGV0ZVNjb3BlKS5jYXRjaCgoZXJyb3IyKSA9PiBoYW5kbGVFcnJvcihlcnJvcjIsIGVsLCBleHByZXNzaW9uKSk7XG4gICAgICBpZiAoZnVuYy5maW5pc2hlZCkge1xuICAgICAgICBydW5JZlR5cGVPZkZ1bmN0aW9uKHJlY2VpdmVyLCBmdW5jLnJlc3VsdCwgY29tcGxldGVTY29wZSwgcGFyYW1zLCBlbCk7XG4gICAgICAgIGZ1bmMucmVzdWx0ID0gdm9pZCAwO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgcHJvbWlzZS50aGVuKChyZXN1bHQpID0+IHtcbiAgICAgICAgICBydW5JZlR5cGVPZkZ1bmN0aW9uKHJlY2VpdmVyLCByZXN1bHQsIGNvbXBsZXRlU2NvcGUsIHBhcmFtcywgZWwpO1xuICAgICAgICB9KS5jYXRjaCgoZXJyb3IyKSA9PiBoYW5kbGVFcnJvcihlcnJvcjIsIGVsLCBleHByZXNzaW9uKSkuZmluYWxseSgoKSA9PiBmdW5jLnJlc3VsdCA9IHZvaWQgMCk7XG4gICAgICB9XG4gICAgfVxuICB9O1xufVxuZnVuY3Rpb24gcnVuSWZUeXBlT2ZGdW5jdGlvbihyZWNlaXZlciwgdmFsdWUsIHNjb3BlMiwgcGFyYW1zLCBlbCkge1xuICBpZiAoc2hvdWxkQXV0b0V2YWx1YXRlRnVuY3Rpb25zICYmIHR5cGVvZiB2YWx1ZSA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgbGV0IHJlc3VsdCA9IHZhbHVlLmFwcGx5KHNjb3BlMiwgcGFyYW1zKTtcbiAgICBpZiAocmVzdWx0IGluc3RhbmNlb2YgUHJvbWlzZSkge1xuICAgICAgcmVzdWx0LnRoZW4oKGkpID0+IHJ1bklmVHlwZU9mRnVuY3Rpb24ocmVjZWl2ZXIsIGksIHNjb3BlMiwgcGFyYW1zKSkuY2F0Y2goKGVycm9yMikgPT4gaGFuZGxlRXJyb3IoZXJyb3IyLCBlbCwgdmFsdWUpKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmVjZWl2ZXIocmVzdWx0KTtcbiAgICB9XG4gIH0gZWxzZSBpZiAodHlwZW9mIHZhbHVlID09PSBcIm9iamVjdFwiICYmIHZhbHVlIGluc3RhbmNlb2YgUHJvbWlzZSkge1xuICAgIHZhbHVlLnRoZW4oKGkpID0+IHJlY2VpdmVyKGkpKTtcbiAgfSBlbHNlIHtcbiAgICByZWNlaXZlcih2YWx1ZSk7XG4gIH1cbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMuanNcbnZhciBwcmVmaXhBc1N0cmluZyA9IFwieC1cIjtcbmZ1bmN0aW9uIHByZWZpeChzdWJqZWN0ID0gXCJcIikge1xuICByZXR1cm4gcHJlZml4QXNTdHJpbmcgKyBzdWJqZWN0O1xufVxuZnVuY3Rpb24gc2V0UHJlZml4KG5ld1ByZWZpeCkge1xuICBwcmVmaXhBc1N0cmluZyA9IG5ld1ByZWZpeDtcbn1cbnZhciBkaXJlY3RpdmVIYW5kbGVycyA9IHt9O1xuZnVuY3Rpb24gZGlyZWN0aXZlKG5hbWUsIGNhbGxiYWNrKSB7XG4gIGRpcmVjdGl2ZUhhbmRsZXJzW25hbWVdID0gY2FsbGJhY2s7XG4gIHJldHVybiB7XG4gICAgYmVmb3JlKGRpcmVjdGl2ZTIpIHtcbiAgICAgIGlmICghZGlyZWN0aXZlSGFuZGxlcnNbZGlyZWN0aXZlMl0pIHtcbiAgICAgICAgY29uc29sZS53YXJuKFwiQ2Fubm90IGZpbmQgZGlyZWN0aXZlIGAke2RpcmVjdGl2ZX1gLiBgJHtuYW1lfWAgd2lsbCB1c2UgdGhlIGRlZmF1bHQgb3JkZXIgb2YgZXhlY3V0aW9uXCIpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBjb25zdCBwb3MgPSBkaXJlY3RpdmVPcmRlci5pbmRleE9mKGRpcmVjdGl2ZTIpO1xuICAgICAgZGlyZWN0aXZlT3JkZXIuc3BsaWNlKHBvcyA+PSAwID8gcG9zIDogZGlyZWN0aXZlT3JkZXIuaW5kZXhPZihcIkRFRkFVTFRcIiksIDAsIG5hbWUpO1xuICAgIH1cbiAgfTtcbn1cbmZ1bmN0aW9uIGRpcmVjdGl2ZXMoZWwsIGF0dHJpYnV0ZXMsIG9yaWdpbmFsQXR0cmlidXRlT3ZlcnJpZGUpIHtcbiAgYXR0cmlidXRlcyA9IEFycmF5LmZyb20oYXR0cmlidXRlcyk7XG4gIGlmIChlbC5feF92aXJ0dWFsRGlyZWN0aXZlcykge1xuICAgIGxldCB2QXR0cmlidXRlcyA9IE9iamVjdC5lbnRyaWVzKGVsLl94X3ZpcnR1YWxEaXJlY3RpdmVzKS5tYXAoKFtuYW1lLCB2YWx1ZV0pID0+ICh7bmFtZSwgdmFsdWV9KSk7XG4gICAgbGV0IHN0YXRpY0F0dHJpYnV0ZXMgPSBhdHRyaWJ1dGVzT25seSh2QXR0cmlidXRlcyk7XG4gICAgdkF0dHJpYnV0ZXMgPSB2QXR0cmlidXRlcy5tYXAoKGF0dHJpYnV0ZSkgPT4ge1xuICAgICAgaWYgKHN0YXRpY0F0dHJpYnV0ZXMuZmluZCgoYXR0cikgPT4gYXR0ci5uYW1lID09PSBhdHRyaWJ1dGUubmFtZSkpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICBuYW1lOiBgeC1iaW5kOiR7YXR0cmlidXRlLm5hbWV9YCxcbiAgICAgICAgICB2YWx1ZTogYFwiJHthdHRyaWJ1dGUudmFsdWV9XCJgXG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICByZXR1cm4gYXR0cmlidXRlO1xuICAgIH0pO1xuICAgIGF0dHJpYnV0ZXMgPSBhdHRyaWJ1dGVzLmNvbmNhdCh2QXR0cmlidXRlcyk7XG4gIH1cbiAgbGV0IHRyYW5zZm9ybWVkQXR0cmlidXRlTWFwID0ge307XG4gIGxldCBkaXJlY3RpdmVzMiA9IGF0dHJpYnV0ZXMubWFwKHRvVHJhbnNmb3JtZWRBdHRyaWJ1dGVzKChuZXdOYW1lLCBvbGROYW1lKSA9PiB0cmFuc2Zvcm1lZEF0dHJpYnV0ZU1hcFtuZXdOYW1lXSA9IG9sZE5hbWUpKS5maWx0ZXIob3V0Tm9uQWxwaW5lQXR0cmlidXRlcykubWFwKHRvUGFyc2VkRGlyZWN0aXZlcyh0cmFuc2Zvcm1lZEF0dHJpYnV0ZU1hcCwgb3JpZ2luYWxBdHRyaWJ1dGVPdmVycmlkZSkpLnNvcnQoYnlQcmlvcml0eSk7XG4gIHJldHVybiBkaXJlY3RpdmVzMi5tYXAoKGRpcmVjdGl2ZTIpID0+IHtcbiAgICByZXR1cm4gZ2V0RGlyZWN0aXZlSGFuZGxlcihlbCwgZGlyZWN0aXZlMik7XG4gIH0pO1xufVxuZnVuY3Rpb24gYXR0cmlidXRlc09ubHkoYXR0cmlidXRlcykge1xuICByZXR1cm4gQXJyYXkuZnJvbShhdHRyaWJ1dGVzKS5tYXAodG9UcmFuc2Zvcm1lZEF0dHJpYnV0ZXMoKSkuZmlsdGVyKChhdHRyKSA9PiAhb3V0Tm9uQWxwaW5lQXR0cmlidXRlcyhhdHRyKSk7XG59XG52YXIgaXNEZWZlcnJpbmdIYW5kbGVycyA9IGZhbHNlO1xudmFyIGRpcmVjdGl2ZUhhbmRsZXJTdGFja3MgPSBuZXcgTWFwKCk7XG52YXIgY3VycmVudEhhbmRsZXJTdGFja0tleSA9IFN5bWJvbCgpO1xuZnVuY3Rpb24gZGVmZXJIYW5kbGluZ0RpcmVjdGl2ZXMoY2FsbGJhY2spIHtcbiAgaXNEZWZlcnJpbmdIYW5kbGVycyA9IHRydWU7XG4gIGxldCBrZXkgPSBTeW1ib2woKTtcbiAgY3VycmVudEhhbmRsZXJTdGFja0tleSA9IGtleTtcbiAgZGlyZWN0aXZlSGFuZGxlclN0YWNrcy5zZXQoa2V5LCBbXSk7XG4gIGxldCBmbHVzaEhhbmRsZXJzID0gKCkgPT4ge1xuICAgIHdoaWxlIChkaXJlY3RpdmVIYW5kbGVyU3RhY2tzLmdldChrZXkpLmxlbmd0aClcbiAgICAgIGRpcmVjdGl2ZUhhbmRsZXJTdGFja3MuZ2V0KGtleSkuc2hpZnQoKSgpO1xuICAgIGRpcmVjdGl2ZUhhbmRsZXJTdGFja3MuZGVsZXRlKGtleSk7XG4gIH07XG4gIGxldCBzdG9wRGVmZXJyaW5nID0gKCkgPT4ge1xuICAgIGlzRGVmZXJyaW5nSGFuZGxlcnMgPSBmYWxzZTtcbiAgICBmbHVzaEhhbmRsZXJzKCk7XG4gIH07XG4gIGNhbGxiYWNrKGZsdXNoSGFuZGxlcnMpO1xuICBzdG9wRGVmZXJyaW5nKCk7XG59XG5mdW5jdGlvbiBnZXRFbGVtZW50Qm91bmRVdGlsaXRpZXMoZWwpIHtcbiAgbGV0IGNsZWFudXBzID0gW107XG4gIGxldCBjbGVhbnVwMiA9IChjYWxsYmFjaykgPT4gY2xlYW51cHMucHVzaChjYWxsYmFjayk7XG4gIGxldCBbZWZmZWN0MywgY2xlYW51cEVmZmVjdF0gPSBlbGVtZW50Qm91bmRFZmZlY3QoZWwpO1xuICBjbGVhbnVwcy5wdXNoKGNsZWFudXBFZmZlY3QpO1xuICBsZXQgdXRpbGl0aWVzID0ge1xuICAgIEFscGluZTogYWxwaW5lX2RlZmF1bHQsXG4gICAgZWZmZWN0OiBlZmZlY3QzLFxuICAgIGNsZWFudXA6IGNsZWFudXAyLFxuICAgIGV2YWx1YXRlTGF0ZXI6IGV2YWx1YXRlTGF0ZXIuYmluZChldmFsdWF0ZUxhdGVyLCBlbCksXG4gICAgZXZhbHVhdGU6IGV2YWx1YXRlLmJpbmQoZXZhbHVhdGUsIGVsKVxuICB9O1xuICBsZXQgZG9DbGVhbnVwID0gKCkgPT4gY2xlYW51cHMuZm9yRWFjaCgoaSkgPT4gaSgpKTtcbiAgcmV0dXJuIFt1dGlsaXRpZXMsIGRvQ2xlYW51cF07XG59XG5mdW5jdGlvbiBnZXREaXJlY3RpdmVIYW5kbGVyKGVsLCBkaXJlY3RpdmUyKSB7XG4gIGxldCBub29wID0gKCkgPT4ge1xuICB9O1xuICBsZXQgaGFuZGxlcjMgPSBkaXJlY3RpdmVIYW5kbGVyc1tkaXJlY3RpdmUyLnR5cGVdIHx8IG5vb3A7XG4gIGxldCBbdXRpbGl0aWVzLCBjbGVhbnVwMl0gPSBnZXRFbGVtZW50Qm91bmRVdGlsaXRpZXMoZWwpO1xuICBvbkF0dHJpYnV0ZVJlbW92ZWQoZWwsIGRpcmVjdGl2ZTIub3JpZ2luYWwsIGNsZWFudXAyKTtcbiAgbGV0IGZ1bGxIYW5kbGVyID0gKCkgPT4ge1xuICAgIGlmIChlbC5feF9pZ25vcmUgfHwgZWwuX3hfaWdub3JlU2VsZilcbiAgICAgIHJldHVybjtcbiAgICBoYW5kbGVyMy5pbmxpbmUgJiYgaGFuZGxlcjMuaW5saW5lKGVsLCBkaXJlY3RpdmUyLCB1dGlsaXRpZXMpO1xuICAgIGhhbmRsZXIzID0gaGFuZGxlcjMuYmluZChoYW5kbGVyMywgZWwsIGRpcmVjdGl2ZTIsIHV0aWxpdGllcyk7XG4gICAgaXNEZWZlcnJpbmdIYW5kbGVycyA/IGRpcmVjdGl2ZUhhbmRsZXJTdGFja3MuZ2V0KGN1cnJlbnRIYW5kbGVyU3RhY2tLZXkpLnB1c2goaGFuZGxlcjMpIDogaGFuZGxlcjMoKTtcbiAgfTtcbiAgZnVsbEhhbmRsZXIucnVuQ2xlYW51cHMgPSBjbGVhbnVwMjtcbiAgcmV0dXJuIGZ1bGxIYW5kbGVyO1xufVxudmFyIHN0YXJ0aW5nV2l0aCA9IChzdWJqZWN0LCByZXBsYWNlbWVudCkgPT4gKHtuYW1lLCB2YWx1ZX0pID0+IHtcbiAgaWYgKG5hbWUuc3RhcnRzV2l0aChzdWJqZWN0KSlcbiAgICBuYW1lID0gbmFtZS5yZXBsYWNlKHN1YmplY3QsIHJlcGxhY2VtZW50KTtcbiAgcmV0dXJuIHtuYW1lLCB2YWx1ZX07XG59O1xudmFyIGludG8gPSAoaSkgPT4gaTtcbmZ1bmN0aW9uIHRvVHJhbnNmb3JtZWRBdHRyaWJ1dGVzKGNhbGxiYWNrID0gKCkgPT4ge1xufSkge1xuICByZXR1cm4gKHtuYW1lLCB2YWx1ZX0pID0+IHtcbiAgICBsZXQge25hbWU6IG5ld05hbWUsIHZhbHVlOiBuZXdWYWx1ZX0gPSBhdHRyaWJ1dGVUcmFuc2Zvcm1lcnMucmVkdWNlKChjYXJyeSwgdHJhbnNmb3JtKSA9PiB7XG4gICAgICByZXR1cm4gdHJhbnNmb3JtKGNhcnJ5KTtcbiAgICB9LCB7bmFtZSwgdmFsdWV9KTtcbiAgICBpZiAobmV3TmFtZSAhPT0gbmFtZSlcbiAgICAgIGNhbGxiYWNrKG5ld05hbWUsIG5hbWUpO1xuICAgIHJldHVybiB7bmFtZTogbmV3TmFtZSwgdmFsdWU6IG5ld1ZhbHVlfTtcbiAgfTtcbn1cbnZhciBhdHRyaWJ1dGVUcmFuc2Zvcm1lcnMgPSBbXTtcbmZ1bmN0aW9uIG1hcEF0dHJpYnV0ZXMoY2FsbGJhY2spIHtcbiAgYXR0cmlidXRlVHJhbnNmb3JtZXJzLnB1c2goY2FsbGJhY2spO1xufVxuZnVuY3Rpb24gb3V0Tm9uQWxwaW5lQXR0cmlidXRlcyh7bmFtZX0pIHtcbiAgcmV0dXJuIGFscGluZUF0dHJpYnV0ZVJlZ2V4KCkudGVzdChuYW1lKTtcbn1cbnZhciBhbHBpbmVBdHRyaWJ1dGVSZWdleCA9ICgpID0+IG5ldyBSZWdFeHAoYF4ke3ByZWZpeEFzU3RyaW5nfShbXjpeLl0rKVxcXFxiYCk7XG5mdW5jdGlvbiB0b1BhcnNlZERpcmVjdGl2ZXModHJhbnNmb3JtZWRBdHRyaWJ1dGVNYXAsIG9yaWdpbmFsQXR0cmlidXRlT3ZlcnJpZGUpIHtcbiAgcmV0dXJuICh7bmFtZSwgdmFsdWV9KSA9PiB7XG4gICAgbGV0IHR5cGVNYXRjaCA9IG5hbWUubWF0Y2goYWxwaW5lQXR0cmlidXRlUmVnZXgoKSk7XG4gICAgbGV0IHZhbHVlTWF0Y2ggPSBuYW1lLm1hdGNoKC86KFthLXpBLVowLTlcXC06XSspLyk7XG4gICAgbGV0IG1vZGlmaWVycyA9IG5hbWUubWF0Y2goL1xcLlteLlxcXV0rKD89W15cXF1dKiQpL2cpIHx8IFtdO1xuICAgIGxldCBvcmlnaW5hbCA9IG9yaWdpbmFsQXR0cmlidXRlT3ZlcnJpZGUgfHwgdHJhbnNmb3JtZWRBdHRyaWJ1dGVNYXBbbmFtZV0gfHwgbmFtZTtcbiAgICByZXR1cm4ge1xuICAgICAgdHlwZTogdHlwZU1hdGNoID8gdHlwZU1hdGNoWzFdIDogbnVsbCxcbiAgICAgIHZhbHVlOiB2YWx1ZU1hdGNoID8gdmFsdWVNYXRjaFsxXSA6IG51bGwsXG4gICAgICBtb2RpZmllcnM6IG1vZGlmaWVycy5tYXAoKGkpID0+IGkucmVwbGFjZShcIi5cIiwgXCJcIikpLFxuICAgICAgZXhwcmVzc2lvbjogdmFsdWUsXG4gICAgICBvcmlnaW5hbFxuICAgIH07XG4gIH07XG59XG52YXIgREVGQVVMVCA9IFwiREVGQVVMVFwiO1xudmFyIGRpcmVjdGl2ZU9yZGVyID0gW1xuICBcImlnbm9yZVwiLFxuICBcInJlZlwiLFxuICBcImRhdGFcIixcbiAgXCJpZFwiLFxuICBcImJpbmRcIixcbiAgXCJpbml0XCIsXG4gIFwiZm9yXCIsXG4gIFwibW9kZWxcIixcbiAgXCJtb2RlbGFibGVcIixcbiAgXCJ0cmFuc2l0aW9uXCIsXG4gIFwic2hvd1wiLFxuICBcImlmXCIsXG4gIERFRkFVTFQsXG4gIFwidGVsZXBvcnRcIlxuXTtcbmZ1bmN0aW9uIGJ5UHJpb3JpdHkoYSwgYikge1xuICBsZXQgdHlwZUEgPSBkaXJlY3RpdmVPcmRlci5pbmRleE9mKGEudHlwZSkgPT09IC0xID8gREVGQVVMVCA6IGEudHlwZTtcbiAgbGV0IHR5cGVCID0gZGlyZWN0aXZlT3JkZXIuaW5kZXhPZihiLnR5cGUpID09PSAtMSA/IERFRkFVTFQgOiBiLnR5cGU7XG4gIHJldHVybiBkaXJlY3RpdmVPcmRlci5pbmRleE9mKHR5cGVBKSAtIGRpcmVjdGl2ZU9yZGVyLmluZGV4T2YodHlwZUIpO1xufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvdXRpbHMvZGlzcGF0Y2guanNcbmZ1bmN0aW9uIGRpc3BhdGNoKGVsLCBuYW1lLCBkZXRhaWwgPSB7fSkge1xuICBlbC5kaXNwYXRjaEV2ZW50KG5ldyBDdXN0b21FdmVudChuYW1lLCB7XG4gICAgZGV0YWlsLFxuICAgIGJ1YmJsZXM6IHRydWUsXG4gICAgY29tcG9zZWQ6IHRydWUsXG4gICAgY2FuY2VsYWJsZTogdHJ1ZVxuICB9KSk7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy91dGlscy93YWxrLmpzXG5mdW5jdGlvbiB3YWxrKGVsLCBjYWxsYmFjaykge1xuICBpZiAodHlwZW9mIFNoYWRvd1Jvb3QgPT09IFwiZnVuY3Rpb25cIiAmJiBlbCBpbnN0YW5jZW9mIFNoYWRvd1Jvb3QpIHtcbiAgICBBcnJheS5mcm9tKGVsLmNoaWxkcmVuKS5mb3JFYWNoKChlbDIpID0+IHdhbGsoZWwyLCBjYWxsYmFjaykpO1xuICAgIHJldHVybjtcbiAgfVxuICBsZXQgc2tpcCA9IGZhbHNlO1xuICBjYWxsYmFjayhlbCwgKCkgPT4gc2tpcCA9IHRydWUpO1xuICBpZiAoc2tpcClcbiAgICByZXR1cm47XG4gIGxldCBub2RlID0gZWwuZmlyc3RFbGVtZW50Q2hpbGQ7XG4gIHdoaWxlIChub2RlKSB7XG4gICAgd2Fsayhub2RlLCBjYWxsYmFjaywgZmFsc2UpO1xuICAgIG5vZGUgPSBub2RlLm5leHRFbGVtZW50U2libGluZztcbiAgfVxufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvdXRpbHMvd2Fybi5qc1xuZnVuY3Rpb24gd2FybihtZXNzYWdlLCAuLi5hcmdzKSB7XG4gIGNvbnNvbGUud2FybihgQWxwaW5lIFdhcm5pbmc6ICR7bWVzc2FnZX1gLCAuLi5hcmdzKTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2xpZmVjeWNsZS5qc1xudmFyIHN0YXJ0ZWQgPSBmYWxzZTtcbmZ1bmN0aW9uIHN0YXJ0KCkge1xuICBpZiAoc3RhcnRlZClcbiAgICB3YXJuKFwiQWxwaW5lIGhhcyBhbHJlYWR5IGJlZW4gaW5pdGlhbGl6ZWQgb24gdGhpcyBwYWdlLiBDYWxsaW5nIEFscGluZS5zdGFydCgpIG1vcmUgdGhhbiBvbmNlIGNhbiBjYXVzZSBwcm9ibGVtcy5cIik7XG4gIHN0YXJ0ZWQgPSB0cnVlO1xuICBpZiAoIWRvY3VtZW50LmJvZHkpXG4gICAgd2FybihcIlVuYWJsZSB0byBpbml0aWFsaXplLiBUcnlpbmcgdG8gbG9hZCBBbHBpbmUgYmVmb3JlIGA8Ym9keT5gIGlzIGF2YWlsYWJsZS4gRGlkIHlvdSBmb3JnZXQgdG8gYWRkIGBkZWZlcmAgaW4gQWxwaW5lJ3MgYDxzY3JpcHQ+YCB0YWc/XCIpO1xuICBkaXNwYXRjaChkb2N1bWVudCwgXCJhbHBpbmU6aW5pdFwiKTtcbiAgZGlzcGF0Y2goZG9jdW1lbnQsIFwiYWxwaW5lOmluaXRpYWxpemluZ1wiKTtcbiAgc3RhcnRPYnNlcnZpbmdNdXRhdGlvbnMoKTtcbiAgb25FbEFkZGVkKChlbCkgPT4gaW5pdFRyZWUoZWwsIHdhbGspKTtcbiAgb25FbFJlbW92ZWQoKGVsKSA9PiBkZXN0cm95VHJlZShlbCkpO1xuICBvbkF0dHJpYnV0ZXNBZGRlZCgoZWwsIGF0dHJzKSA9PiB7XG4gICAgZGlyZWN0aXZlcyhlbCwgYXR0cnMpLmZvckVhY2goKGhhbmRsZSkgPT4gaGFuZGxlKCkpO1xuICB9KTtcbiAgbGV0IG91dE5lc3RlZENvbXBvbmVudHMgPSAoZWwpID0+ICFjbG9zZXN0Um9vdChlbC5wYXJlbnRFbGVtZW50LCB0cnVlKTtcbiAgQXJyYXkuZnJvbShkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKGFsbFNlbGVjdG9ycygpKSkuZmlsdGVyKG91dE5lc3RlZENvbXBvbmVudHMpLmZvckVhY2goKGVsKSA9PiB7XG4gICAgaW5pdFRyZWUoZWwpO1xuICB9KTtcbiAgZGlzcGF0Y2goZG9jdW1lbnQsIFwiYWxwaW5lOmluaXRpYWxpemVkXCIpO1xufVxudmFyIHJvb3RTZWxlY3RvckNhbGxiYWNrcyA9IFtdO1xudmFyIGluaXRTZWxlY3RvckNhbGxiYWNrcyA9IFtdO1xuZnVuY3Rpb24gcm9vdFNlbGVjdG9ycygpIHtcbiAgcmV0dXJuIHJvb3RTZWxlY3RvckNhbGxiYWNrcy5tYXAoKGZuKSA9PiBmbigpKTtcbn1cbmZ1bmN0aW9uIGFsbFNlbGVjdG9ycygpIHtcbiAgcmV0dXJuIHJvb3RTZWxlY3RvckNhbGxiYWNrcy5jb25jYXQoaW5pdFNlbGVjdG9yQ2FsbGJhY2tzKS5tYXAoKGZuKSA9PiBmbigpKTtcbn1cbmZ1bmN0aW9uIGFkZFJvb3RTZWxlY3RvcihzZWxlY3RvckNhbGxiYWNrKSB7XG4gIHJvb3RTZWxlY3RvckNhbGxiYWNrcy5wdXNoKHNlbGVjdG9yQ2FsbGJhY2spO1xufVxuZnVuY3Rpb24gYWRkSW5pdFNlbGVjdG9yKHNlbGVjdG9yQ2FsbGJhY2spIHtcbiAgaW5pdFNlbGVjdG9yQ2FsbGJhY2tzLnB1c2goc2VsZWN0b3JDYWxsYmFjayk7XG59XG5mdW5jdGlvbiBjbG9zZXN0Um9vdChlbCwgaW5jbHVkZUluaXRTZWxlY3RvcnMgPSBmYWxzZSkge1xuICByZXR1cm4gZmluZENsb3Nlc3QoZWwsIChlbGVtZW50KSA9PiB7XG4gICAgY29uc3Qgc2VsZWN0b3JzID0gaW5jbHVkZUluaXRTZWxlY3RvcnMgPyBhbGxTZWxlY3RvcnMoKSA6IHJvb3RTZWxlY3RvcnMoKTtcbiAgICBpZiAoc2VsZWN0b3JzLnNvbWUoKHNlbGVjdG9yKSA9PiBlbGVtZW50Lm1hdGNoZXMoc2VsZWN0b3IpKSlcbiAgICAgIHJldHVybiB0cnVlO1xuICB9KTtcbn1cbmZ1bmN0aW9uIGZpbmRDbG9zZXN0KGVsLCBjYWxsYmFjaykge1xuICBpZiAoIWVsKVxuICAgIHJldHVybjtcbiAgaWYgKGNhbGxiYWNrKGVsKSlcbiAgICByZXR1cm4gZWw7XG4gIGlmIChlbC5feF90ZWxlcG9ydEJhY2spXG4gICAgZWwgPSBlbC5feF90ZWxlcG9ydEJhY2s7XG4gIGlmICghZWwucGFyZW50RWxlbWVudClcbiAgICByZXR1cm47XG4gIHJldHVybiBmaW5kQ2xvc2VzdChlbC5wYXJlbnRFbGVtZW50LCBjYWxsYmFjayk7XG59XG5mdW5jdGlvbiBpc1Jvb3QoZWwpIHtcbiAgcmV0dXJuIHJvb3RTZWxlY3RvcnMoKS5zb21lKChzZWxlY3RvcikgPT4gZWwubWF0Y2hlcyhzZWxlY3RvcikpO1xufVxudmFyIGluaXRJbnRlcmNlcHRvcnMyID0gW107XG5mdW5jdGlvbiBpbnRlcmNlcHRJbml0KGNhbGxiYWNrKSB7XG4gIGluaXRJbnRlcmNlcHRvcnMyLnB1c2goY2FsbGJhY2spO1xufVxuZnVuY3Rpb24gaW5pdFRyZWUoZWwsIHdhbGtlciA9IHdhbGssIGludGVyY2VwdCA9ICgpID0+IHtcbn0pIHtcbiAgZGVmZXJIYW5kbGluZ0RpcmVjdGl2ZXMoKCkgPT4ge1xuICAgIHdhbGtlcihlbCwgKGVsMiwgc2tpcCkgPT4ge1xuICAgICAgaW50ZXJjZXB0KGVsMiwgc2tpcCk7XG4gICAgICBpbml0SW50ZXJjZXB0b3JzMi5mb3JFYWNoKChpKSA9PiBpKGVsMiwgc2tpcCkpO1xuICAgICAgZGlyZWN0aXZlcyhlbDIsIGVsMi5hdHRyaWJ1dGVzKS5mb3JFYWNoKChoYW5kbGUpID0+IGhhbmRsZSgpKTtcbiAgICAgIGVsMi5feF9pZ25vcmUgJiYgc2tpcCgpO1xuICAgIH0pO1xuICB9KTtcbn1cbmZ1bmN0aW9uIGRlc3Ryb3lUcmVlKHJvb3QpIHtcbiAgd2Fsayhyb290LCAoZWwpID0+IGNsZWFudXBBdHRyaWJ1dGVzKGVsKSk7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9uZXh0VGljay5qc1xudmFyIHRpY2tTdGFjayA9IFtdO1xudmFyIGlzSG9sZGluZyA9IGZhbHNlO1xuZnVuY3Rpb24gbmV4dFRpY2soY2FsbGJhY2sgPSAoKSA9PiB7XG59KSB7XG4gIHF1ZXVlTWljcm90YXNrKCgpID0+IHtcbiAgICBpc0hvbGRpbmcgfHwgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICByZWxlYXNlTmV4dFRpY2tzKCk7XG4gICAgfSk7XG4gIH0pO1xuICByZXR1cm4gbmV3IFByb21pc2UoKHJlcykgPT4ge1xuICAgIHRpY2tTdGFjay5wdXNoKCgpID0+IHtcbiAgICAgIGNhbGxiYWNrKCk7XG4gICAgICByZXMoKTtcbiAgICB9KTtcbiAgfSk7XG59XG5mdW5jdGlvbiByZWxlYXNlTmV4dFRpY2tzKCkge1xuICBpc0hvbGRpbmcgPSBmYWxzZTtcbiAgd2hpbGUgKHRpY2tTdGFjay5sZW5ndGgpXG4gICAgdGlja1N0YWNrLnNoaWZ0KCkoKTtcbn1cbmZ1bmN0aW9uIGhvbGROZXh0VGlja3MoKSB7XG4gIGlzSG9sZGluZyA9IHRydWU7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy91dGlscy9jbGFzc2VzLmpzXG5mdW5jdGlvbiBzZXRDbGFzc2VzKGVsLCB2YWx1ZSkge1xuICBpZiAoQXJyYXkuaXNBcnJheSh2YWx1ZSkpIHtcbiAgICByZXR1cm4gc2V0Q2xhc3Nlc0Zyb21TdHJpbmcoZWwsIHZhbHVlLmpvaW4oXCIgXCIpKTtcbiAgfSBlbHNlIGlmICh0eXBlb2YgdmFsdWUgPT09IFwib2JqZWN0XCIgJiYgdmFsdWUgIT09IG51bGwpIHtcbiAgICByZXR1cm4gc2V0Q2xhc3Nlc0Zyb21PYmplY3QoZWwsIHZhbHVlKTtcbiAgfSBlbHNlIGlmICh0eXBlb2YgdmFsdWUgPT09IFwiZnVuY3Rpb25cIikge1xuICAgIHJldHVybiBzZXRDbGFzc2VzKGVsLCB2YWx1ZSgpKTtcbiAgfVxuICByZXR1cm4gc2V0Q2xhc3Nlc0Zyb21TdHJpbmcoZWwsIHZhbHVlKTtcbn1cbmZ1bmN0aW9uIHNldENsYXNzZXNGcm9tU3RyaW5nKGVsLCBjbGFzc1N0cmluZykge1xuICBsZXQgc3BsaXQgPSAoY2xhc3NTdHJpbmcyKSA9PiBjbGFzc1N0cmluZzIuc3BsaXQoXCIgXCIpLmZpbHRlcihCb29sZWFuKTtcbiAgbGV0IG1pc3NpbmdDbGFzc2VzID0gKGNsYXNzU3RyaW5nMikgPT4gY2xhc3NTdHJpbmcyLnNwbGl0KFwiIFwiKS5maWx0ZXIoKGkpID0+ICFlbC5jbGFzc0xpc3QuY29udGFpbnMoaSkpLmZpbHRlcihCb29sZWFuKTtcbiAgbGV0IGFkZENsYXNzZXNBbmRSZXR1cm5VbmRvID0gKGNsYXNzZXMpID0+IHtcbiAgICBlbC5jbGFzc0xpc3QuYWRkKC4uLmNsYXNzZXMpO1xuICAgIHJldHVybiAoKSA9PiB7XG4gICAgICBlbC5jbGFzc0xpc3QucmVtb3ZlKC4uLmNsYXNzZXMpO1xuICAgIH07XG4gIH07XG4gIGNsYXNzU3RyaW5nID0gY2xhc3NTdHJpbmcgPT09IHRydWUgPyBjbGFzc1N0cmluZyA9IFwiXCIgOiBjbGFzc1N0cmluZyB8fCBcIlwiO1xuICByZXR1cm4gYWRkQ2xhc3Nlc0FuZFJldHVyblVuZG8obWlzc2luZ0NsYXNzZXMoY2xhc3NTdHJpbmcpKTtcbn1cbmZ1bmN0aW9uIHNldENsYXNzZXNGcm9tT2JqZWN0KGVsLCBjbGFzc09iamVjdCkge1xuICBsZXQgc3BsaXQgPSAoY2xhc3NTdHJpbmcpID0+IGNsYXNzU3RyaW5nLnNwbGl0KFwiIFwiKS5maWx0ZXIoQm9vbGVhbik7XG4gIGxldCBmb3JBZGQgPSBPYmplY3QuZW50cmllcyhjbGFzc09iamVjdCkuZmxhdE1hcCgoW2NsYXNzU3RyaW5nLCBib29sXSkgPT4gYm9vbCA/IHNwbGl0KGNsYXNzU3RyaW5nKSA6IGZhbHNlKS5maWx0ZXIoQm9vbGVhbik7XG4gIGxldCBmb3JSZW1vdmUgPSBPYmplY3QuZW50cmllcyhjbGFzc09iamVjdCkuZmxhdE1hcCgoW2NsYXNzU3RyaW5nLCBib29sXSkgPT4gIWJvb2wgPyBzcGxpdChjbGFzc1N0cmluZykgOiBmYWxzZSkuZmlsdGVyKEJvb2xlYW4pO1xuICBsZXQgYWRkZWQgPSBbXTtcbiAgbGV0IHJlbW92ZWQgPSBbXTtcbiAgZm9yUmVtb3ZlLmZvckVhY2goKGkpID0+IHtcbiAgICBpZiAoZWwuY2xhc3NMaXN0LmNvbnRhaW5zKGkpKSB7XG4gICAgICBlbC5jbGFzc0xpc3QucmVtb3ZlKGkpO1xuICAgICAgcmVtb3ZlZC5wdXNoKGkpO1xuICAgIH1cbiAgfSk7XG4gIGZvckFkZC5mb3JFYWNoKChpKSA9PiB7XG4gICAgaWYgKCFlbC5jbGFzc0xpc3QuY29udGFpbnMoaSkpIHtcbiAgICAgIGVsLmNsYXNzTGlzdC5hZGQoaSk7XG4gICAgICBhZGRlZC5wdXNoKGkpO1xuICAgIH1cbiAgfSk7XG4gIHJldHVybiAoKSA9PiB7XG4gICAgcmVtb3ZlZC5mb3JFYWNoKChpKSA9PiBlbC5jbGFzc0xpc3QuYWRkKGkpKTtcbiAgICBhZGRlZC5mb3JFYWNoKChpKSA9PiBlbC5jbGFzc0xpc3QucmVtb3ZlKGkpKTtcbiAgfTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL3V0aWxzL3N0eWxlcy5qc1xuZnVuY3Rpb24gc2V0U3R5bGVzKGVsLCB2YWx1ZSkge1xuICBpZiAodHlwZW9mIHZhbHVlID09PSBcIm9iamVjdFwiICYmIHZhbHVlICE9PSBudWxsKSB7XG4gICAgcmV0dXJuIHNldFN0eWxlc0Zyb21PYmplY3QoZWwsIHZhbHVlKTtcbiAgfVxuICByZXR1cm4gc2V0U3R5bGVzRnJvbVN0cmluZyhlbCwgdmFsdWUpO1xufVxuZnVuY3Rpb24gc2V0U3R5bGVzRnJvbU9iamVjdChlbCwgdmFsdWUpIHtcbiAgbGV0IHByZXZpb3VzU3R5bGVzID0ge307XG4gIE9iamVjdC5lbnRyaWVzKHZhbHVlKS5mb3JFYWNoKChba2V5LCB2YWx1ZTJdKSA9PiB7XG4gICAgcHJldmlvdXNTdHlsZXNba2V5XSA9IGVsLnN0eWxlW2tleV07XG4gICAgaWYgKCFrZXkuc3RhcnRzV2l0aChcIi0tXCIpKSB7XG4gICAgICBrZXkgPSBrZWJhYkNhc2Uoa2V5KTtcbiAgICB9XG4gICAgZWwuc3R5bGUuc2V0UHJvcGVydHkoa2V5LCB2YWx1ZTIpO1xuICB9KTtcbiAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgaWYgKGVsLnN0eWxlLmxlbmd0aCA9PT0gMCkge1xuICAgICAgZWwucmVtb3ZlQXR0cmlidXRlKFwic3R5bGVcIik7XG4gICAgfVxuICB9KTtcbiAgcmV0dXJuICgpID0+IHtcbiAgICBzZXRTdHlsZXMoZWwsIHByZXZpb3VzU3R5bGVzKTtcbiAgfTtcbn1cbmZ1bmN0aW9uIHNldFN0eWxlc0Zyb21TdHJpbmcoZWwsIHZhbHVlKSB7XG4gIGxldCBjYWNoZSA9IGVsLmdldEF0dHJpYnV0ZShcInN0eWxlXCIsIHZhbHVlKTtcbiAgZWwuc2V0QXR0cmlidXRlKFwic3R5bGVcIiwgdmFsdWUpO1xuICByZXR1cm4gKCkgPT4ge1xuICAgIGVsLnNldEF0dHJpYnV0ZShcInN0eWxlXCIsIGNhY2hlIHx8IFwiXCIpO1xuICB9O1xufVxuZnVuY3Rpb24ga2ViYWJDYXNlKHN1YmplY3QpIHtcbiAgcmV0dXJuIHN1YmplY3QucmVwbGFjZSgvKFthLXpdKShbQS1aXSkvZywgXCIkMS0kMlwiKS50b0xvd2VyQ2FzZSgpO1xufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvdXRpbHMvb25jZS5qc1xuZnVuY3Rpb24gb25jZShjYWxsYmFjaywgZmFsbGJhY2sgPSAoKSA9PiB7XG59KSB7XG4gIGxldCBjYWxsZWQgPSBmYWxzZTtcbiAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgIGlmICghY2FsbGVkKSB7XG4gICAgICBjYWxsZWQgPSB0cnVlO1xuICAgICAgY2FsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZmFsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG4gIH07XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9kaXJlY3RpdmVzL3gtdHJhbnNpdGlvbi5qc1xuZGlyZWN0aXZlKFwidHJhbnNpdGlvblwiLCAoZWwsIHt2YWx1ZSwgbW9kaWZpZXJzLCBleHByZXNzaW9ufSwge2V2YWx1YXRlOiBldmFsdWF0ZTJ9KSA9PiB7XG4gIGlmICh0eXBlb2YgZXhwcmVzc2lvbiA9PT0gXCJmdW5jdGlvblwiKVxuICAgIGV4cHJlc3Npb24gPSBldmFsdWF0ZTIoZXhwcmVzc2lvbik7XG4gIGlmIChleHByZXNzaW9uID09PSBmYWxzZSlcbiAgICByZXR1cm47XG4gIGlmICghZXhwcmVzc2lvbiB8fCB0eXBlb2YgZXhwcmVzc2lvbiA9PT0gXCJib29sZWFuXCIpIHtcbiAgICByZWdpc3RlclRyYW5zaXRpb25zRnJvbUhlbHBlcihlbCwgbW9kaWZpZXJzLCB2YWx1ZSk7XG4gIH0gZWxzZSB7XG4gICAgcmVnaXN0ZXJUcmFuc2l0aW9uc0Zyb21DbGFzc1N0cmluZyhlbCwgZXhwcmVzc2lvbiwgdmFsdWUpO1xuICB9XG59KTtcbmZ1bmN0aW9uIHJlZ2lzdGVyVHJhbnNpdGlvbnNGcm9tQ2xhc3NTdHJpbmcoZWwsIGNsYXNzU3RyaW5nLCBzdGFnZSkge1xuICByZWdpc3RlclRyYW5zaXRpb25PYmplY3QoZWwsIHNldENsYXNzZXMsIFwiXCIpO1xuICBsZXQgZGlyZWN0aXZlU3RvcmFnZU1hcCA9IHtcbiAgICBlbnRlcjogKGNsYXNzZXMpID0+IHtcbiAgICAgIGVsLl94X3RyYW5zaXRpb24uZW50ZXIuZHVyaW5nID0gY2xhc3NlcztcbiAgICB9LFxuICAgIFwiZW50ZXItc3RhcnRcIjogKGNsYXNzZXMpID0+IHtcbiAgICAgIGVsLl94X3RyYW5zaXRpb24uZW50ZXIuc3RhcnQgPSBjbGFzc2VzO1xuICAgIH0sXG4gICAgXCJlbnRlci1lbmRcIjogKGNsYXNzZXMpID0+IHtcbiAgICAgIGVsLl94X3RyYW5zaXRpb24uZW50ZXIuZW5kID0gY2xhc3NlcztcbiAgICB9LFxuICAgIGxlYXZlOiAoY2xhc3NlcykgPT4ge1xuICAgICAgZWwuX3hfdHJhbnNpdGlvbi5sZWF2ZS5kdXJpbmcgPSBjbGFzc2VzO1xuICAgIH0sXG4gICAgXCJsZWF2ZS1zdGFydFwiOiAoY2xhc3NlcykgPT4ge1xuICAgICAgZWwuX3hfdHJhbnNpdGlvbi5sZWF2ZS5zdGFydCA9IGNsYXNzZXM7XG4gICAgfSxcbiAgICBcImxlYXZlLWVuZFwiOiAoY2xhc3NlcykgPT4ge1xuICAgICAgZWwuX3hfdHJhbnNpdGlvbi5sZWF2ZS5lbmQgPSBjbGFzc2VzO1xuICAgIH1cbiAgfTtcbiAgZGlyZWN0aXZlU3RvcmFnZU1hcFtzdGFnZV0oY2xhc3NTdHJpbmcpO1xufVxuZnVuY3Rpb24gcmVnaXN0ZXJUcmFuc2l0aW9uc0Zyb21IZWxwZXIoZWwsIG1vZGlmaWVycywgc3RhZ2UpIHtcbiAgcmVnaXN0ZXJUcmFuc2l0aW9uT2JqZWN0KGVsLCBzZXRTdHlsZXMpO1xuICBsZXQgZG9lc250U3BlY2lmeSA9ICFtb2RpZmllcnMuaW5jbHVkZXMoXCJpblwiKSAmJiAhbW9kaWZpZXJzLmluY2x1ZGVzKFwib3V0XCIpICYmICFzdGFnZTtcbiAgbGV0IHRyYW5zaXRpb25pbmdJbiA9IGRvZXNudFNwZWNpZnkgfHwgbW9kaWZpZXJzLmluY2x1ZGVzKFwiaW5cIikgfHwgW1wiZW50ZXJcIl0uaW5jbHVkZXMoc3RhZ2UpO1xuICBsZXQgdHJhbnNpdGlvbmluZ091dCA9IGRvZXNudFNwZWNpZnkgfHwgbW9kaWZpZXJzLmluY2x1ZGVzKFwib3V0XCIpIHx8IFtcImxlYXZlXCJdLmluY2x1ZGVzKHN0YWdlKTtcbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImluXCIpICYmICFkb2VzbnRTcGVjaWZ5KSB7XG4gICAgbW9kaWZpZXJzID0gbW9kaWZpZXJzLmZpbHRlcigoaSwgaW5kZXgpID0+IGluZGV4IDwgbW9kaWZpZXJzLmluZGV4T2YoXCJvdXRcIikpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJvdXRcIikgJiYgIWRvZXNudFNwZWNpZnkpIHtcbiAgICBtb2RpZmllcnMgPSBtb2RpZmllcnMuZmlsdGVyKChpLCBpbmRleCkgPT4gaW5kZXggPiBtb2RpZmllcnMuaW5kZXhPZihcIm91dFwiKSk7XG4gIH1cbiAgbGV0IHdhbnRzQWxsID0gIW1vZGlmaWVycy5pbmNsdWRlcyhcIm9wYWNpdHlcIikgJiYgIW1vZGlmaWVycy5pbmNsdWRlcyhcInNjYWxlXCIpO1xuICBsZXQgd2FudHNPcGFjaXR5ID0gd2FudHNBbGwgfHwgbW9kaWZpZXJzLmluY2x1ZGVzKFwib3BhY2l0eVwiKTtcbiAgbGV0IHdhbnRzU2NhbGUgPSB3YW50c0FsbCB8fCBtb2RpZmllcnMuaW5jbHVkZXMoXCJzY2FsZVwiKTtcbiAgbGV0IG9wYWNpdHlWYWx1ZSA9IHdhbnRzT3BhY2l0eSA/IDAgOiAxO1xuICBsZXQgc2NhbGVWYWx1ZSA9IHdhbnRzU2NhbGUgPyBtb2RpZmllclZhbHVlKG1vZGlmaWVycywgXCJzY2FsZVwiLCA5NSkgLyAxMDAgOiAxO1xuICBsZXQgZGVsYXkgPSBtb2RpZmllclZhbHVlKG1vZGlmaWVycywgXCJkZWxheVwiLCAwKSAvIDFlMztcbiAgbGV0IG9yaWdpbiA9IG1vZGlmaWVyVmFsdWUobW9kaWZpZXJzLCBcIm9yaWdpblwiLCBcImNlbnRlclwiKTtcbiAgbGV0IHByb3BlcnR5ID0gXCJvcGFjaXR5LCB0cmFuc2Zvcm1cIjtcbiAgbGV0IGR1cmF0aW9uSW4gPSBtb2RpZmllclZhbHVlKG1vZGlmaWVycywgXCJkdXJhdGlvblwiLCAxNTApIC8gMWUzO1xuICBsZXQgZHVyYXRpb25PdXQgPSBtb2RpZmllclZhbHVlKG1vZGlmaWVycywgXCJkdXJhdGlvblwiLCA3NSkgLyAxZTM7XG4gIGxldCBlYXNpbmcgPSBgY3ViaWMtYmV6aWVyKDAuNCwgMC4wLCAwLjIsIDEpYDtcbiAgaWYgKHRyYW5zaXRpb25pbmdJbikge1xuICAgIGVsLl94X3RyYW5zaXRpb24uZW50ZXIuZHVyaW5nID0ge1xuICAgICAgdHJhbnNmb3JtT3JpZ2luOiBvcmlnaW4sXG4gICAgICB0cmFuc2l0aW9uRGVsYXk6IGAke2RlbGF5fXNgLFxuICAgICAgdHJhbnNpdGlvblByb3BlcnR5OiBwcm9wZXJ0eSxcbiAgICAgIHRyYW5zaXRpb25EdXJhdGlvbjogYCR7ZHVyYXRpb25Jbn1zYCxcbiAgICAgIHRyYW5zaXRpb25UaW1pbmdGdW5jdGlvbjogZWFzaW5nXG4gICAgfTtcbiAgICBlbC5feF90cmFuc2l0aW9uLmVudGVyLnN0YXJ0ID0ge1xuICAgICAgb3BhY2l0eTogb3BhY2l0eVZhbHVlLFxuICAgICAgdHJhbnNmb3JtOiBgc2NhbGUoJHtzY2FsZVZhbHVlfSlgXG4gICAgfTtcbiAgICBlbC5feF90cmFuc2l0aW9uLmVudGVyLmVuZCA9IHtcbiAgICAgIG9wYWNpdHk6IDEsXG4gICAgICB0cmFuc2Zvcm06IGBzY2FsZSgxKWBcbiAgICB9O1xuICB9XG4gIGlmICh0cmFuc2l0aW9uaW5nT3V0KSB7XG4gICAgZWwuX3hfdHJhbnNpdGlvbi5sZWF2ZS5kdXJpbmcgPSB7XG4gICAgICB0cmFuc2Zvcm1PcmlnaW46IG9yaWdpbixcbiAgICAgIHRyYW5zaXRpb25EZWxheTogYCR7ZGVsYXl9c2AsXG4gICAgICB0cmFuc2l0aW9uUHJvcGVydHk6IHByb3BlcnR5LFxuICAgICAgdHJhbnNpdGlvbkR1cmF0aW9uOiBgJHtkdXJhdGlvbk91dH1zYCxcbiAgICAgIHRyYW5zaXRpb25UaW1pbmdGdW5jdGlvbjogZWFzaW5nXG4gICAgfTtcbiAgICBlbC5feF90cmFuc2l0aW9uLmxlYXZlLnN0YXJ0ID0ge1xuICAgICAgb3BhY2l0eTogMSxcbiAgICAgIHRyYW5zZm9ybTogYHNjYWxlKDEpYFxuICAgIH07XG4gICAgZWwuX3hfdHJhbnNpdGlvbi5sZWF2ZS5lbmQgPSB7XG4gICAgICBvcGFjaXR5OiBvcGFjaXR5VmFsdWUsXG4gICAgICB0cmFuc2Zvcm06IGBzY2FsZSgke3NjYWxlVmFsdWV9KWBcbiAgICB9O1xuICB9XG59XG5mdW5jdGlvbiByZWdpc3RlclRyYW5zaXRpb25PYmplY3QoZWwsIHNldEZ1bmN0aW9uLCBkZWZhdWx0VmFsdWUgPSB7fSkge1xuICBpZiAoIWVsLl94X3RyYW5zaXRpb24pXG4gICAgZWwuX3hfdHJhbnNpdGlvbiA9IHtcbiAgICAgIGVudGVyOiB7ZHVyaW5nOiBkZWZhdWx0VmFsdWUsIHN0YXJ0OiBkZWZhdWx0VmFsdWUsIGVuZDogZGVmYXVsdFZhbHVlfSxcbiAgICAgIGxlYXZlOiB7ZHVyaW5nOiBkZWZhdWx0VmFsdWUsIHN0YXJ0OiBkZWZhdWx0VmFsdWUsIGVuZDogZGVmYXVsdFZhbHVlfSxcbiAgICAgIGluKGJlZm9yZSA9ICgpID0+IHtcbiAgICAgIH0sIGFmdGVyID0gKCkgPT4ge1xuICAgICAgfSkge1xuICAgICAgICB0cmFuc2l0aW9uKGVsLCBzZXRGdW5jdGlvbiwge1xuICAgICAgICAgIGR1cmluZzogdGhpcy5lbnRlci5kdXJpbmcsXG4gICAgICAgICAgc3RhcnQ6IHRoaXMuZW50ZXIuc3RhcnQsXG4gICAgICAgICAgZW5kOiB0aGlzLmVudGVyLmVuZFxuICAgICAgICB9LCBiZWZvcmUsIGFmdGVyKTtcbiAgICAgIH0sXG4gICAgICBvdXQoYmVmb3JlID0gKCkgPT4ge1xuICAgICAgfSwgYWZ0ZXIgPSAoKSA9PiB7XG4gICAgICB9KSB7XG4gICAgICAgIHRyYW5zaXRpb24oZWwsIHNldEZ1bmN0aW9uLCB7XG4gICAgICAgICAgZHVyaW5nOiB0aGlzLmxlYXZlLmR1cmluZyxcbiAgICAgICAgICBzdGFydDogdGhpcy5sZWF2ZS5zdGFydCxcbiAgICAgICAgICBlbmQ6IHRoaXMubGVhdmUuZW5kXG4gICAgICAgIH0sIGJlZm9yZSwgYWZ0ZXIpO1xuICAgICAgfVxuICAgIH07XG59XG53aW5kb3cuRWxlbWVudC5wcm90b3R5cGUuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyA9IGZ1bmN0aW9uKGVsLCB2YWx1ZSwgc2hvdywgaGlkZSkge1xuICBjb25zdCBuZXh0VGljazIgPSBkb2N1bWVudC52aXNpYmlsaXR5U3RhdGUgPT09IFwidmlzaWJsZVwiID8gcmVxdWVzdEFuaW1hdGlvbkZyYW1lIDogc2V0VGltZW91dDtcbiAgbGV0IGNsaWNrQXdheUNvbXBhdGlibGVTaG93ID0gKCkgPT4gbmV4dFRpY2syKHNob3cpO1xuICBpZiAodmFsdWUpIHtcbiAgICBpZiAoZWwuX3hfdHJhbnNpdGlvbiAmJiAoZWwuX3hfdHJhbnNpdGlvbi5lbnRlciB8fCBlbC5feF90cmFuc2l0aW9uLmxlYXZlKSkge1xuICAgICAgZWwuX3hfdHJhbnNpdGlvbi5lbnRlciAmJiAoT2JqZWN0LmVudHJpZXMoZWwuX3hfdHJhbnNpdGlvbi5lbnRlci5kdXJpbmcpLmxlbmd0aCB8fCBPYmplY3QuZW50cmllcyhlbC5feF90cmFuc2l0aW9uLmVudGVyLnN0YXJ0KS5sZW5ndGggfHwgT2JqZWN0LmVudHJpZXMoZWwuX3hfdHJhbnNpdGlvbi5lbnRlci5lbmQpLmxlbmd0aCkgPyBlbC5feF90cmFuc2l0aW9uLmluKHNob3cpIDogY2xpY2tBd2F5Q29tcGF0aWJsZVNob3coKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZWwuX3hfdHJhbnNpdGlvbiA/IGVsLl94X3RyYW5zaXRpb24uaW4oc2hvdykgOiBjbGlja0F3YXlDb21wYXRpYmxlU2hvdygpO1xuICAgIH1cbiAgICByZXR1cm47XG4gIH1cbiAgZWwuX3hfaGlkZVByb21pc2UgPSBlbC5feF90cmFuc2l0aW9uID8gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgIGVsLl94X3RyYW5zaXRpb24ub3V0KCgpID0+IHtcbiAgICB9LCAoKSA9PiByZXNvbHZlKGhpZGUpKTtcbiAgICBlbC5feF90cmFuc2l0aW9uaW5nLmJlZm9yZUNhbmNlbCgoKSA9PiByZWplY3Qoe2lzRnJvbUNhbmNlbGxlZFRyYW5zaXRpb246IHRydWV9KSk7XG4gIH0pIDogUHJvbWlzZS5yZXNvbHZlKGhpZGUpO1xuICBxdWV1ZU1pY3JvdGFzaygoKSA9PiB7XG4gICAgbGV0IGNsb3Nlc3QgPSBjbG9zZXN0SGlkZShlbCk7XG4gICAgaWYgKGNsb3Nlc3QpIHtcbiAgICAgIGlmICghY2xvc2VzdC5feF9oaWRlQ2hpbGRyZW4pXG4gICAgICAgIGNsb3Nlc3QuX3hfaGlkZUNoaWxkcmVuID0gW107XG4gICAgICBjbG9zZXN0Ll94X2hpZGVDaGlsZHJlbi5wdXNoKGVsKTtcbiAgICB9IGVsc2Uge1xuICAgICAgbmV4dFRpY2syKCgpID0+IHtcbiAgICAgICAgbGV0IGhpZGVBZnRlckNoaWxkcmVuID0gKGVsMikgPT4ge1xuICAgICAgICAgIGxldCBjYXJyeSA9IFByb21pc2UuYWxsKFtcbiAgICAgICAgICAgIGVsMi5feF9oaWRlUHJvbWlzZSxcbiAgICAgICAgICAgIC4uLihlbDIuX3hfaGlkZUNoaWxkcmVuIHx8IFtdKS5tYXAoaGlkZUFmdGVyQ2hpbGRyZW4pXG4gICAgICAgICAgXSkudGhlbigoW2ldKSA9PiBpKCkpO1xuICAgICAgICAgIGRlbGV0ZSBlbDIuX3hfaGlkZVByb21pc2U7XG4gICAgICAgICAgZGVsZXRlIGVsMi5feF9oaWRlQ2hpbGRyZW47XG4gICAgICAgICAgcmV0dXJuIGNhcnJ5O1xuICAgICAgICB9O1xuICAgICAgICBoaWRlQWZ0ZXJDaGlsZHJlbihlbCkuY2F0Y2goKGUpID0+IHtcbiAgICAgICAgICBpZiAoIWUuaXNGcm9tQ2FuY2VsbGVkVHJhbnNpdGlvbilcbiAgICAgICAgICAgIHRocm93IGU7XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgfVxuICB9KTtcbn07XG5mdW5jdGlvbiBjbG9zZXN0SGlkZShlbCkge1xuICBsZXQgcGFyZW50ID0gZWwucGFyZW50Tm9kZTtcbiAgaWYgKCFwYXJlbnQpXG4gICAgcmV0dXJuO1xuICByZXR1cm4gcGFyZW50Ll94X2hpZGVQcm9taXNlID8gcGFyZW50IDogY2xvc2VzdEhpZGUocGFyZW50KTtcbn1cbmZ1bmN0aW9uIHRyYW5zaXRpb24oZWwsIHNldEZ1bmN0aW9uLCB7ZHVyaW5nLCBzdGFydDogc3RhcnQyLCBlbmR9ID0ge30sIGJlZm9yZSA9ICgpID0+IHtcbn0sIGFmdGVyID0gKCkgPT4ge1xufSkge1xuICBpZiAoZWwuX3hfdHJhbnNpdGlvbmluZylcbiAgICBlbC5feF90cmFuc2l0aW9uaW5nLmNhbmNlbCgpO1xuICBpZiAoT2JqZWN0LmtleXMoZHVyaW5nKS5sZW5ndGggPT09IDAgJiYgT2JqZWN0LmtleXMoc3RhcnQyKS5sZW5ndGggPT09IDAgJiYgT2JqZWN0LmtleXMoZW5kKS5sZW5ndGggPT09IDApIHtcbiAgICBiZWZvcmUoKTtcbiAgICBhZnRlcigpO1xuICAgIHJldHVybjtcbiAgfVxuICBsZXQgdW5kb1N0YXJ0LCB1bmRvRHVyaW5nLCB1bmRvRW5kO1xuICBwZXJmb3JtVHJhbnNpdGlvbihlbCwge1xuICAgIHN0YXJ0KCkge1xuICAgICAgdW5kb1N0YXJ0ID0gc2V0RnVuY3Rpb24oZWwsIHN0YXJ0Mik7XG4gICAgfSxcbiAgICBkdXJpbmcoKSB7XG4gICAgICB1bmRvRHVyaW5nID0gc2V0RnVuY3Rpb24oZWwsIGR1cmluZyk7XG4gICAgfSxcbiAgICBiZWZvcmUsXG4gICAgZW5kKCkge1xuICAgICAgdW5kb1N0YXJ0KCk7XG4gICAgICB1bmRvRW5kID0gc2V0RnVuY3Rpb24oZWwsIGVuZCk7XG4gICAgfSxcbiAgICBhZnRlcixcbiAgICBjbGVhbnVwKCkge1xuICAgICAgdW5kb0R1cmluZygpO1xuICAgICAgdW5kb0VuZCgpO1xuICAgIH1cbiAgfSk7XG59XG5mdW5jdGlvbiBwZXJmb3JtVHJhbnNpdGlvbihlbCwgc3RhZ2VzKSB7XG4gIGxldCBpbnRlcnJ1cHRlZCwgcmVhY2hlZEJlZm9yZSwgcmVhY2hlZEVuZDtcbiAgbGV0IGZpbmlzaCA9IG9uY2UoKCkgPT4ge1xuICAgIG11dGF0ZURvbSgoKSA9PiB7XG4gICAgICBpbnRlcnJ1cHRlZCA9IHRydWU7XG4gICAgICBpZiAoIXJlYWNoZWRCZWZvcmUpXG4gICAgICAgIHN0YWdlcy5iZWZvcmUoKTtcbiAgICAgIGlmICghcmVhY2hlZEVuZCkge1xuICAgICAgICBzdGFnZXMuZW5kKCk7XG4gICAgICAgIHJlbGVhc2VOZXh0VGlja3MoKTtcbiAgICAgIH1cbiAgICAgIHN0YWdlcy5hZnRlcigpO1xuICAgICAgaWYgKGVsLmlzQ29ubmVjdGVkKVxuICAgICAgICBzdGFnZXMuY2xlYW51cCgpO1xuICAgICAgZGVsZXRlIGVsLl94X3RyYW5zaXRpb25pbmc7XG4gICAgfSk7XG4gIH0pO1xuICBlbC5feF90cmFuc2l0aW9uaW5nID0ge1xuICAgIGJlZm9yZUNhbmNlbHM6IFtdLFxuICAgIGJlZm9yZUNhbmNlbChjYWxsYmFjaykge1xuICAgICAgdGhpcy5iZWZvcmVDYW5jZWxzLnB1c2goY2FsbGJhY2spO1xuICAgIH0sXG4gICAgY2FuY2VsOiBvbmNlKGZ1bmN0aW9uKCkge1xuICAgICAgd2hpbGUgKHRoaXMuYmVmb3JlQ2FuY2Vscy5sZW5ndGgpIHtcbiAgICAgICAgdGhpcy5iZWZvcmVDYW5jZWxzLnNoaWZ0KCkoKTtcbiAgICAgIH1cbiAgICAgIDtcbiAgICAgIGZpbmlzaCgpO1xuICAgIH0pLFxuICAgIGZpbmlzaFxuICB9O1xuICBtdXRhdGVEb20oKCkgPT4ge1xuICAgIHN0YWdlcy5zdGFydCgpO1xuICAgIHN0YWdlcy5kdXJpbmcoKTtcbiAgfSk7XG4gIGhvbGROZXh0VGlja3MoKTtcbiAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IHtcbiAgICBpZiAoaW50ZXJydXB0ZWQpXG4gICAgICByZXR1cm47XG4gICAgbGV0IGR1cmF0aW9uID0gTnVtYmVyKGdldENvbXB1dGVkU3R5bGUoZWwpLnRyYW5zaXRpb25EdXJhdGlvbi5yZXBsYWNlKC8sLiovLCBcIlwiKS5yZXBsYWNlKFwic1wiLCBcIlwiKSkgKiAxZTM7XG4gICAgbGV0IGRlbGF5ID0gTnVtYmVyKGdldENvbXB1dGVkU3R5bGUoZWwpLnRyYW5zaXRpb25EZWxheS5yZXBsYWNlKC8sLiovLCBcIlwiKS5yZXBsYWNlKFwic1wiLCBcIlwiKSkgKiAxZTM7XG4gICAgaWYgKGR1cmF0aW9uID09PSAwKVxuICAgICAgZHVyYXRpb24gPSBOdW1iZXIoZ2V0Q29tcHV0ZWRTdHlsZShlbCkuYW5pbWF0aW9uRHVyYXRpb24ucmVwbGFjZShcInNcIiwgXCJcIikpICogMWUzO1xuICAgIG11dGF0ZURvbSgoKSA9PiB7XG4gICAgICBzdGFnZXMuYmVmb3JlKCk7XG4gICAgfSk7XG4gICAgcmVhY2hlZEJlZm9yZSA9IHRydWU7XG4gICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKCgpID0+IHtcbiAgICAgIGlmIChpbnRlcnJ1cHRlZClcbiAgICAgICAgcmV0dXJuO1xuICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgc3RhZ2VzLmVuZCgpO1xuICAgICAgfSk7XG4gICAgICByZWxlYXNlTmV4dFRpY2tzKCk7XG4gICAgICBzZXRUaW1lb3V0KGVsLl94X3RyYW5zaXRpb25pbmcuZmluaXNoLCBkdXJhdGlvbiArIGRlbGF5KTtcbiAgICAgIHJlYWNoZWRFbmQgPSB0cnVlO1xuICAgIH0pO1xuICB9KTtcbn1cbmZ1bmN0aW9uIG1vZGlmaWVyVmFsdWUobW9kaWZpZXJzLCBrZXksIGZhbGxiYWNrKSB7XG4gIGlmIChtb2RpZmllcnMuaW5kZXhPZihrZXkpID09PSAtMSlcbiAgICByZXR1cm4gZmFsbGJhY2s7XG4gIGNvbnN0IHJhd1ZhbHVlID0gbW9kaWZpZXJzW21vZGlmaWVycy5pbmRleE9mKGtleSkgKyAxXTtcbiAgaWYgKCFyYXdWYWx1ZSlcbiAgICByZXR1cm4gZmFsbGJhY2s7XG4gIGlmIChrZXkgPT09IFwic2NhbGVcIikge1xuICAgIGlmIChpc05hTihyYXdWYWx1ZSkpXG4gICAgICByZXR1cm4gZmFsbGJhY2s7XG4gIH1cbiAgaWYgKGtleSA9PT0gXCJkdXJhdGlvblwiIHx8IGtleSA9PT0gXCJkZWxheVwiKSB7XG4gICAgbGV0IG1hdGNoID0gcmF3VmFsdWUubWF0Y2goLyhbMC05XSspbXMvKTtcbiAgICBpZiAobWF0Y2gpXG4gICAgICByZXR1cm4gbWF0Y2hbMV07XG4gIH1cbiAgaWYgKGtleSA9PT0gXCJvcmlnaW5cIikge1xuICAgIGlmIChbXCJ0b3BcIiwgXCJyaWdodFwiLCBcImxlZnRcIiwgXCJjZW50ZXJcIiwgXCJib3R0b21cIl0uaW5jbHVkZXMobW9kaWZpZXJzW21vZGlmaWVycy5pbmRleE9mKGtleSkgKyAyXSkpIHtcbiAgICAgIHJldHVybiBbcmF3VmFsdWUsIG1vZGlmaWVyc1ttb2RpZmllcnMuaW5kZXhPZihrZXkpICsgMl1dLmpvaW4oXCIgXCIpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmF3VmFsdWU7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9jbG9uZS5qc1xudmFyIGlzQ2xvbmluZyA9IGZhbHNlO1xuZnVuY3Rpb24gc2tpcER1cmluZ0Nsb25lKGNhbGxiYWNrLCBmYWxsYmFjayA9ICgpID0+IHtcbn0pIHtcbiAgcmV0dXJuICguLi5hcmdzKSA9PiBpc0Nsb25pbmcgPyBmYWxsYmFjayguLi5hcmdzKSA6IGNhbGxiYWNrKC4uLmFyZ3MpO1xufVxuZnVuY3Rpb24gb25seUR1cmluZ0Nsb25lKGNhbGxiYWNrKSB7XG4gIHJldHVybiAoLi4uYXJncykgPT4gaXNDbG9uaW5nICYmIGNhbGxiYWNrKC4uLmFyZ3MpO1xufVxuZnVuY3Rpb24gY2xvbmUob2xkRWwsIG5ld0VsKSB7XG4gIGlmICghbmV3RWwuX3hfZGF0YVN0YWNrKVxuICAgIG5ld0VsLl94X2RhdGFTdGFjayA9IG9sZEVsLl94X2RhdGFTdGFjaztcbiAgaXNDbG9uaW5nID0gdHJ1ZTtcbiAgZG9udFJlZ2lzdGVyUmVhY3RpdmVTaWRlRWZmZWN0cygoKSA9PiB7XG4gICAgY2xvbmVUcmVlKG5ld0VsKTtcbiAgfSk7XG4gIGlzQ2xvbmluZyA9IGZhbHNlO1xufVxuZnVuY3Rpb24gY2xvbmVUcmVlKGVsKSB7XG4gIGxldCBoYXNSdW5UaHJvdWdoRmlyc3RFbCA9IGZhbHNlO1xuICBsZXQgc2hhbGxvd1dhbGtlciA9IChlbDIsIGNhbGxiYWNrKSA9PiB7XG4gICAgd2FsayhlbDIsIChlbDMsIHNraXApID0+IHtcbiAgICAgIGlmIChoYXNSdW5UaHJvdWdoRmlyc3RFbCAmJiBpc1Jvb3QoZWwzKSlcbiAgICAgICAgcmV0dXJuIHNraXAoKTtcbiAgICAgIGhhc1J1blRocm91Z2hGaXJzdEVsID0gdHJ1ZTtcbiAgICAgIGNhbGxiYWNrKGVsMywgc2tpcCk7XG4gICAgfSk7XG4gIH07XG4gIGluaXRUcmVlKGVsLCBzaGFsbG93V2Fsa2VyKTtcbn1cbmZ1bmN0aW9uIGRvbnRSZWdpc3RlclJlYWN0aXZlU2lkZUVmZmVjdHMoY2FsbGJhY2spIHtcbiAgbGV0IGNhY2hlID0gZWZmZWN0O1xuICBvdmVycmlkZUVmZmVjdCgoY2FsbGJhY2syLCBlbCkgPT4ge1xuICAgIGxldCBzdG9yZWRFZmZlY3QgPSBjYWNoZShjYWxsYmFjazIpO1xuICAgIHJlbGVhc2Uoc3RvcmVkRWZmZWN0KTtcbiAgICByZXR1cm4gKCkgPT4ge1xuICAgIH07XG4gIH0pO1xuICBjYWxsYmFjaygpO1xuICBvdmVycmlkZUVmZmVjdChjYWNoZSk7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy91dGlscy9iaW5kLmpzXG5mdW5jdGlvbiBiaW5kKGVsLCBuYW1lLCB2YWx1ZSwgbW9kaWZpZXJzID0gW10pIHtcbiAgaWYgKCFlbC5feF9iaW5kaW5ncylcbiAgICBlbC5feF9iaW5kaW5ncyA9IHJlYWN0aXZlKHt9KTtcbiAgZWwuX3hfYmluZGluZ3NbbmFtZV0gPSB2YWx1ZTtcbiAgbmFtZSA9IG1vZGlmaWVycy5pbmNsdWRlcyhcImNhbWVsXCIpID8gY2FtZWxDYXNlKG5hbWUpIDogbmFtZTtcbiAgc3dpdGNoIChuYW1lKSB7XG4gICAgY2FzZSBcInZhbHVlXCI6XG4gICAgICBiaW5kSW5wdXRWYWx1ZShlbCwgdmFsdWUpO1xuICAgICAgYnJlYWs7XG4gICAgY2FzZSBcInN0eWxlXCI6XG4gICAgICBiaW5kU3R5bGVzKGVsLCB2YWx1ZSk7XG4gICAgICBicmVhaztcbiAgICBjYXNlIFwiY2xhc3NcIjpcbiAgICAgIGJpbmRDbGFzc2VzKGVsLCB2YWx1ZSk7XG4gICAgICBicmVhaztcbiAgICBjYXNlIFwic2VsZWN0ZWRcIjpcbiAgICBjYXNlIFwiY2hlY2tlZFwiOlxuICAgICAgYmluZEF0dHJpYnV0ZUFuZFByb3BlcnR5KGVsLCBuYW1lLCB2YWx1ZSk7XG4gICAgICBicmVhaztcbiAgICBkZWZhdWx0OlxuICAgICAgYmluZEF0dHJpYnV0ZShlbCwgbmFtZSwgdmFsdWUpO1xuICAgICAgYnJlYWs7XG4gIH1cbn1cbmZ1bmN0aW9uIGJpbmRJbnB1dFZhbHVlKGVsLCB2YWx1ZSkge1xuICBpZiAoZWwudHlwZSA9PT0gXCJyYWRpb1wiKSB7XG4gICAgaWYgKGVsLmF0dHJpYnV0ZXMudmFsdWUgPT09IHZvaWQgMCkge1xuICAgICAgZWwudmFsdWUgPSB2YWx1ZTtcbiAgICB9XG4gICAgaWYgKHdpbmRvdy5mcm9tTW9kZWwpIHtcbiAgICAgIGVsLmNoZWNrZWQgPSBjaGVja2VkQXR0ckxvb3NlQ29tcGFyZShlbC52YWx1ZSwgdmFsdWUpO1xuICAgIH1cbiAgfSBlbHNlIGlmIChlbC50eXBlID09PSBcImNoZWNrYm94XCIpIHtcbiAgICBpZiAoTnVtYmVyLmlzSW50ZWdlcih2YWx1ZSkpIHtcbiAgICAgIGVsLnZhbHVlID0gdmFsdWU7XG4gICAgfSBlbHNlIGlmICghTnVtYmVyLmlzSW50ZWdlcih2YWx1ZSkgJiYgIUFycmF5LmlzQXJyYXkodmFsdWUpICYmIHR5cGVvZiB2YWx1ZSAhPT0gXCJib29sZWFuXCIgJiYgIVtudWxsLCB2b2lkIDBdLmluY2x1ZGVzKHZhbHVlKSkge1xuICAgICAgZWwudmFsdWUgPSBTdHJpbmcodmFsdWUpO1xuICAgIH0gZWxzZSB7XG4gICAgICBpZiAoQXJyYXkuaXNBcnJheSh2YWx1ZSkpIHtcbiAgICAgICAgZWwuY2hlY2tlZCA9IHZhbHVlLnNvbWUoKHZhbCkgPT4gY2hlY2tlZEF0dHJMb29zZUNvbXBhcmUodmFsLCBlbC52YWx1ZSkpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgZWwuY2hlY2tlZCA9ICEhdmFsdWU7XG4gICAgICB9XG4gICAgfVxuICB9IGVsc2UgaWYgKGVsLnRhZ05hbWUgPT09IFwiU0VMRUNUXCIpIHtcbiAgICB1cGRhdGVTZWxlY3QoZWwsIHZhbHVlKTtcbiAgfSBlbHNlIHtcbiAgICBpZiAoZWwudmFsdWUgPT09IHZhbHVlKVxuICAgICAgcmV0dXJuO1xuICAgIGVsLnZhbHVlID0gdmFsdWU7XG4gIH1cbn1cbmZ1bmN0aW9uIGJpbmRDbGFzc2VzKGVsLCB2YWx1ZSkge1xuICBpZiAoZWwuX3hfdW5kb0FkZGVkQ2xhc3NlcylcbiAgICBlbC5feF91bmRvQWRkZWRDbGFzc2VzKCk7XG4gIGVsLl94X3VuZG9BZGRlZENsYXNzZXMgPSBzZXRDbGFzc2VzKGVsLCB2YWx1ZSk7XG59XG5mdW5jdGlvbiBiaW5kU3R5bGVzKGVsLCB2YWx1ZSkge1xuICBpZiAoZWwuX3hfdW5kb0FkZGVkU3R5bGVzKVxuICAgIGVsLl94X3VuZG9BZGRlZFN0eWxlcygpO1xuICBlbC5feF91bmRvQWRkZWRTdHlsZXMgPSBzZXRTdHlsZXMoZWwsIHZhbHVlKTtcbn1cbmZ1bmN0aW9uIGJpbmRBdHRyaWJ1dGVBbmRQcm9wZXJ0eShlbCwgbmFtZSwgdmFsdWUpIHtcbiAgYmluZEF0dHJpYnV0ZShlbCwgbmFtZSwgdmFsdWUpO1xuICBzZXRQcm9wZXJ0eUlmQ2hhbmdlZChlbCwgbmFtZSwgdmFsdWUpO1xufVxuZnVuY3Rpb24gYmluZEF0dHJpYnV0ZShlbCwgbmFtZSwgdmFsdWUpIHtcbiAgaWYgKFtudWxsLCB2b2lkIDAsIGZhbHNlXS5pbmNsdWRlcyh2YWx1ZSkgJiYgYXR0cmlidXRlU2hvdWxkbnRCZVByZXNlcnZlZElmRmFsc3kobmFtZSkpIHtcbiAgICBlbC5yZW1vdmVBdHRyaWJ1dGUobmFtZSk7XG4gIH0gZWxzZSB7XG4gICAgaWYgKGlzQm9vbGVhbkF0dHIobmFtZSkpXG4gICAgICB2YWx1ZSA9IG5hbWU7XG4gICAgc2V0SWZDaGFuZ2VkKGVsLCBuYW1lLCB2YWx1ZSk7XG4gIH1cbn1cbmZ1bmN0aW9uIHNldElmQ2hhbmdlZChlbCwgYXR0ck5hbWUsIHZhbHVlKSB7XG4gIGlmIChlbC5nZXRBdHRyaWJ1dGUoYXR0ck5hbWUpICE9IHZhbHVlKSB7XG4gICAgZWwuc2V0QXR0cmlidXRlKGF0dHJOYW1lLCB2YWx1ZSk7XG4gIH1cbn1cbmZ1bmN0aW9uIHNldFByb3BlcnR5SWZDaGFuZ2VkKGVsLCBwcm9wTmFtZSwgdmFsdWUpIHtcbiAgaWYgKGVsW3Byb3BOYW1lXSAhPT0gdmFsdWUpIHtcbiAgICBlbFtwcm9wTmFtZV0gPSB2YWx1ZTtcbiAgfVxufVxuZnVuY3Rpb24gdXBkYXRlU2VsZWN0KGVsLCB2YWx1ZSkge1xuICBjb25zdCBhcnJheVdyYXBwZWRWYWx1ZSA9IFtdLmNvbmNhdCh2YWx1ZSkubWFwKCh2YWx1ZTIpID0+IHtcbiAgICByZXR1cm4gdmFsdWUyICsgXCJcIjtcbiAgfSk7XG4gIEFycmF5LmZyb20oZWwub3B0aW9ucykuZm9yRWFjaCgob3B0aW9uKSA9PiB7XG4gICAgb3B0aW9uLnNlbGVjdGVkID0gYXJyYXlXcmFwcGVkVmFsdWUuaW5jbHVkZXMob3B0aW9uLnZhbHVlKTtcbiAgfSk7XG59XG5mdW5jdGlvbiBjYW1lbENhc2Uoc3ViamVjdCkge1xuICByZXR1cm4gc3ViamVjdC50b0xvd2VyQ2FzZSgpLnJlcGxhY2UoLy0oXFx3KS9nLCAobWF0Y2gsIGNoYXIpID0+IGNoYXIudG9VcHBlckNhc2UoKSk7XG59XG5mdW5jdGlvbiBjaGVja2VkQXR0ckxvb3NlQ29tcGFyZSh2YWx1ZUEsIHZhbHVlQikge1xuICByZXR1cm4gdmFsdWVBID09IHZhbHVlQjtcbn1cbmZ1bmN0aW9uIGlzQm9vbGVhbkF0dHIoYXR0ck5hbWUpIHtcbiAgY29uc3QgYm9vbGVhbkF0dHJpYnV0ZXMgPSBbXG4gICAgXCJkaXNhYmxlZFwiLFxuICAgIFwiY2hlY2tlZFwiLFxuICAgIFwicmVxdWlyZWRcIixcbiAgICBcInJlYWRvbmx5XCIsXG4gICAgXCJoaWRkZW5cIixcbiAgICBcIm9wZW5cIixcbiAgICBcInNlbGVjdGVkXCIsXG4gICAgXCJhdXRvZm9jdXNcIixcbiAgICBcIml0ZW1zY29wZVwiLFxuICAgIFwibXVsdGlwbGVcIixcbiAgICBcIm5vdmFsaWRhdGVcIixcbiAgICBcImFsbG93ZnVsbHNjcmVlblwiLFxuICAgIFwiYWxsb3dwYXltZW50cmVxdWVzdFwiLFxuICAgIFwiZm9ybW5vdmFsaWRhdGVcIixcbiAgICBcImF1dG9wbGF5XCIsXG4gICAgXCJjb250cm9sc1wiLFxuICAgIFwibG9vcFwiLFxuICAgIFwibXV0ZWRcIixcbiAgICBcInBsYXlzaW5saW5lXCIsXG4gICAgXCJkZWZhdWx0XCIsXG4gICAgXCJpc21hcFwiLFxuICAgIFwicmV2ZXJzZWRcIixcbiAgICBcImFzeW5jXCIsXG4gICAgXCJkZWZlclwiLFxuICAgIFwibm9tb2R1bGVcIlxuICBdO1xuICByZXR1cm4gYm9vbGVhbkF0dHJpYnV0ZXMuaW5jbHVkZXMoYXR0ck5hbWUpO1xufVxuZnVuY3Rpb24gYXR0cmlidXRlU2hvdWxkbnRCZVByZXNlcnZlZElmRmFsc3kobmFtZSkge1xuICByZXR1cm4gIVtcImFyaWEtcHJlc3NlZFwiLCBcImFyaWEtY2hlY2tlZFwiLCBcImFyaWEtZXhwYW5kZWRcIiwgXCJhcmlhLXNlbGVjdGVkXCJdLmluY2x1ZGVzKG5hbWUpO1xufVxuZnVuY3Rpb24gZ2V0QmluZGluZyhlbCwgbmFtZSwgZmFsbGJhY2spIHtcbiAgaWYgKGVsLl94X2JpbmRpbmdzICYmIGVsLl94X2JpbmRpbmdzW25hbWVdICE9PSB2b2lkIDApXG4gICAgcmV0dXJuIGVsLl94X2JpbmRpbmdzW25hbWVdO1xuICBsZXQgYXR0ciA9IGVsLmdldEF0dHJpYnV0ZShuYW1lKTtcbiAgaWYgKGF0dHIgPT09IG51bGwpXG4gICAgcmV0dXJuIHR5cGVvZiBmYWxsYmFjayA9PT0gXCJmdW5jdGlvblwiID8gZmFsbGJhY2soKSA6IGZhbGxiYWNrO1xuICBpZiAoYXR0ciA9PT0gXCJcIilcbiAgICByZXR1cm4gdHJ1ZTtcbiAgaWYgKGlzQm9vbGVhbkF0dHIobmFtZSkpIHtcbiAgICByZXR1cm4gISFbbmFtZSwgXCJ0cnVlXCJdLmluY2x1ZGVzKGF0dHIpO1xuICB9XG4gIHJldHVybiBhdHRyO1xufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvdXRpbHMvZGVib3VuY2UuanNcbmZ1bmN0aW9uIGRlYm91bmNlKGZ1bmMsIHdhaXQpIHtcbiAgdmFyIHRpbWVvdXQ7XG4gIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICB2YXIgY29udGV4dCA9IHRoaXMsIGFyZ3MgPSBhcmd1bWVudHM7XG4gICAgdmFyIGxhdGVyID0gZnVuY3Rpb24oKSB7XG4gICAgICB0aW1lb3V0ID0gbnVsbDtcbiAgICAgIGZ1bmMuYXBwbHkoY29udGV4dCwgYXJncyk7XG4gICAgfTtcbiAgICBjbGVhclRpbWVvdXQodGltZW91dCk7XG4gICAgdGltZW91dCA9IHNldFRpbWVvdXQobGF0ZXIsIHdhaXQpO1xuICB9O1xufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvdXRpbHMvdGhyb3R0bGUuanNcbmZ1bmN0aW9uIHRocm90dGxlKGZ1bmMsIGxpbWl0KSB7XG4gIGxldCBpblRocm90dGxlO1xuICByZXR1cm4gZnVuY3Rpb24oKSB7XG4gICAgbGV0IGNvbnRleHQgPSB0aGlzLCBhcmdzID0gYXJndW1lbnRzO1xuICAgIGlmICghaW5UaHJvdHRsZSkge1xuICAgICAgZnVuYy5hcHBseShjb250ZXh0LCBhcmdzKTtcbiAgICAgIGluVGhyb3R0bGUgPSB0cnVlO1xuICAgICAgc2V0VGltZW91dCgoKSA9PiBpblRocm90dGxlID0gZmFsc2UsIGxpbWl0KTtcbiAgICB9XG4gIH07XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9wbHVnaW4uanNcbmZ1bmN0aW9uIHBsdWdpbihjYWxsYmFjaykge1xuICBsZXQgY2FsbGJhY2tzID0gQXJyYXkuaXNBcnJheShjYWxsYmFjaykgPyBjYWxsYmFjayA6IFtjYWxsYmFja107XG4gIGNhbGxiYWNrcy5mb3JFYWNoKChpKSA9PiBpKGFscGluZV9kZWZhdWx0KSk7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9zdG9yZS5qc1xudmFyIHN0b3JlcyA9IHt9O1xudmFyIGlzUmVhY3RpdmUgPSBmYWxzZTtcbmZ1bmN0aW9uIHN0b3JlKG5hbWUsIHZhbHVlKSB7XG4gIGlmICghaXNSZWFjdGl2ZSkge1xuICAgIHN0b3JlcyA9IHJlYWN0aXZlKHN0b3Jlcyk7XG4gICAgaXNSZWFjdGl2ZSA9IHRydWU7XG4gIH1cbiAgaWYgKHZhbHVlID09PSB2b2lkIDApIHtcbiAgICByZXR1cm4gc3RvcmVzW25hbWVdO1xuICB9XG4gIHN0b3Jlc1tuYW1lXSA9IHZhbHVlO1xuICBpZiAodHlwZW9mIHZhbHVlID09PSBcIm9iamVjdFwiICYmIHZhbHVlICE9PSBudWxsICYmIHZhbHVlLmhhc093blByb3BlcnR5KFwiaW5pdFwiKSAmJiB0eXBlb2YgdmFsdWUuaW5pdCA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgc3RvcmVzW25hbWVdLmluaXQoKTtcbiAgfVxuICBpbml0SW50ZXJjZXB0b3JzKHN0b3Jlc1tuYW1lXSk7XG59XG5mdW5jdGlvbiBnZXRTdG9yZXMoKSB7XG4gIHJldHVybiBzdG9yZXM7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9iaW5kcy5qc1xudmFyIGJpbmRzID0ge307XG5mdW5jdGlvbiBiaW5kMihuYW1lLCBiaW5kaW5ncykge1xuICBsZXQgZ2V0QmluZGluZ3MgPSB0eXBlb2YgYmluZGluZ3MgIT09IFwiZnVuY3Rpb25cIiA/ICgpID0+IGJpbmRpbmdzIDogYmluZGluZ3M7XG4gIGlmIChuYW1lIGluc3RhbmNlb2YgRWxlbWVudCkge1xuICAgIGFwcGx5QmluZGluZ3NPYmplY3QobmFtZSwgZ2V0QmluZGluZ3MoKSk7XG4gIH0gZWxzZSB7XG4gICAgYmluZHNbbmFtZV0gPSBnZXRCaW5kaW5ncztcbiAgfVxufVxuZnVuY3Rpb24gaW5qZWN0QmluZGluZ1Byb3ZpZGVycyhvYmopIHtcbiAgT2JqZWN0LmVudHJpZXMoYmluZHMpLmZvckVhY2goKFtuYW1lLCBjYWxsYmFja10pID0+IHtcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkob2JqLCBuYW1lLCB7XG4gICAgICBnZXQoKSB7XG4gICAgICAgIHJldHVybiAoLi4uYXJncykgPT4ge1xuICAgICAgICAgIHJldHVybiBjYWxsYmFjayguLi5hcmdzKTtcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfSk7XG4gIHJldHVybiBvYmo7XG59XG5mdW5jdGlvbiBhcHBseUJpbmRpbmdzT2JqZWN0KGVsLCBvYmosIG9yaWdpbmFsKSB7XG4gIGxldCBjbGVhbnVwUnVubmVycyA9IFtdO1xuICB3aGlsZSAoY2xlYW51cFJ1bm5lcnMubGVuZ3RoKVxuICAgIGNsZWFudXBSdW5uZXJzLnBvcCgpKCk7XG4gIGxldCBhdHRyaWJ1dGVzID0gT2JqZWN0LmVudHJpZXMob2JqKS5tYXAoKFtuYW1lLCB2YWx1ZV0pID0+ICh7bmFtZSwgdmFsdWV9KSk7XG4gIGxldCBzdGF0aWNBdHRyaWJ1dGVzID0gYXR0cmlidXRlc09ubHkoYXR0cmlidXRlcyk7XG4gIGF0dHJpYnV0ZXMgPSBhdHRyaWJ1dGVzLm1hcCgoYXR0cmlidXRlKSA9PiB7XG4gICAgaWYgKHN0YXRpY0F0dHJpYnV0ZXMuZmluZCgoYXR0cikgPT4gYXR0ci5uYW1lID09PSBhdHRyaWJ1dGUubmFtZSkpIHtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIG5hbWU6IGB4LWJpbmQ6JHthdHRyaWJ1dGUubmFtZX1gLFxuICAgICAgICB2YWx1ZTogYFwiJHthdHRyaWJ1dGUudmFsdWV9XCJgXG4gICAgICB9O1xuICAgIH1cbiAgICByZXR1cm4gYXR0cmlidXRlO1xuICB9KTtcbiAgZGlyZWN0aXZlcyhlbCwgYXR0cmlidXRlcywgb3JpZ2luYWwpLm1hcCgoaGFuZGxlKSA9PiB7XG4gICAgY2xlYW51cFJ1bm5lcnMucHVzaChoYW5kbGUucnVuQ2xlYW51cHMpO1xuICAgIGhhbmRsZSgpO1xuICB9KTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RhdGFzLmpzXG52YXIgZGF0YXMgPSB7fTtcbmZ1bmN0aW9uIGRhdGEobmFtZSwgY2FsbGJhY2spIHtcbiAgZGF0YXNbbmFtZV0gPSBjYWxsYmFjaztcbn1cbmZ1bmN0aW9uIGluamVjdERhdGFQcm92aWRlcnMob2JqLCBjb250ZXh0KSB7XG4gIE9iamVjdC5lbnRyaWVzKGRhdGFzKS5mb3JFYWNoKChbbmFtZSwgY2FsbGJhY2tdKSA9PiB7XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KG9iaiwgbmFtZSwge1xuICAgICAgZ2V0KCkge1xuICAgICAgICByZXR1cm4gKC4uLmFyZ3MpID0+IHtcbiAgICAgICAgICByZXR1cm4gY2FsbGJhY2suYmluZChjb250ZXh0KSguLi5hcmdzKTtcbiAgICAgICAgfTtcbiAgICAgIH0sXG4gICAgICBlbnVtZXJhYmxlOiBmYWxzZVxuICAgIH0pO1xuICB9KTtcbiAgcmV0dXJuIG9iajtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2FscGluZS5qc1xudmFyIEFscGluZSA9IHtcbiAgZ2V0IHJlYWN0aXZlKCkge1xuICAgIHJldHVybiByZWFjdGl2ZTtcbiAgfSxcbiAgZ2V0IHJlbGVhc2UoKSB7XG4gICAgcmV0dXJuIHJlbGVhc2U7XG4gIH0sXG4gIGdldCBlZmZlY3QoKSB7XG4gICAgcmV0dXJuIGVmZmVjdDtcbiAgfSxcbiAgZ2V0IHJhdygpIHtcbiAgICByZXR1cm4gcmF3O1xuICB9LFxuICB2ZXJzaW9uOiBcIjMuMTIuMlwiLFxuICBmbHVzaEFuZFN0b3BEZWZlcnJpbmdNdXRhdGlvbnMsXG4gIGRvbnRBdXRvRXZhbHVhdGVGdW5jdGlvbnMsXG4gIGRpc2FibGVFZmZlY3RTY2hlZHVsaW5nLFxuICBzdGFydE9ic2VydmluZ011dGF0aW9ucyxcbiAgc3RvcE9ic2VydmluZ011dGF0aW9ucyxcbiAgc2V0UmVhY3Rpdml0eUVuZ2luZSxcbiAgY2xvc2VzdERhdGFTdGFjayxcbiAgc2tpcER1cmluZ0Nsb25lLFxuICBvbmx5RHVyaW5nQ2xvbmUsXG4gIGFkZFJvb3RTZWxlY3RvcixcbiAgYWRkSW5pdFNlbGVjdG9yLFxuICBhZGRTY29wZVRvTm9kZSxcbiAgZGVmZXJNdXRhdGlvbnMsXG4gIG1hcEF0dHJpYnV0ZXMsXG4gIGV2YWx1YXRlTGF0ZXIsXG4gIGludGVyY2VwdEluaXQsXG4gIHNldEV2YWx1YXRvcixcbiAgbWVyZ2VQcm94aWVzLFxuICBmaW5kQ2xvc2VzdCxcbiAgY2xvc2VzdFJvb3QsXG4gIGRlc3Ryb3lUcmVlLFxuICBpbnRlcmNlcHRvcixcbiAgdHJhbnNpdGlvbixcbiAgc2V0U3R5bGVzLFxuICBtdXRhdGVEb20sXG4gIGRpcmVjdGl2ZSxcbiAgdGhyb3R0bGUsXG4gIGRlYm91bmNlLFxuICBldmFsdWF0ZSxcbiAgaW5pdFRyZWUsXG4gIG5leHRUaWNrLFxuICBwcmVmaXhlZDogcHJlZml4LFxuICBwcmVmaXg6IHNldFByZWZpeCxcbiAgcGx1Z2luLFxuICBtYWdpYyxcbiAgc3RvcmUsXG4gIHN0YXJ0LFxuICBjbG9uZSxcbiAgYm91bmQ6IGdldEJpbmRpbmcsXG4gICRkYXRhOiBzY29wZSxcbiAgd2FsayxcbiAgZGF0YSxcbiAgYmluZDogYmluZDJcbn07XG52YXIgYWxwaW5lX2RlZmF1bHQgPSBBbHBpbmU7XG5cbi8vIG5vZGVfbW9kdWxlcy9AdnVlL3NoYXJlZC9kaXN0L3NoYXJlZC5lc20tYnVuZGxlci5qc1xuZnVuY3Rpb24gbWFrZU1hcChzdHIsIGV4cGVjdHNMb3dlckNhc2UpIHtcbiAgY29uc3QgbWFwID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgY29uc3QgbGlzdCA9IHN0ci5zcGxpdChcIixcIik7XG4gIGZvciAobGV0IGkgPSAwOyBpIDwgbGlzdC5sZW5ndGg7IGkrKykge1xuICAgIG1hcFtsaXN0W2ldXSA9IHRydWU7XG4gIH1cbiAgcmV0dXJuIGV4cGVjdHNMb3dlckNhc2UgPyAodmFsKSA9PiAhIW1hcFt2YWwudG9Mb3dlckNhc2UoKV0gOiAodmFsKSA9PiAhIW1hcFt2YWxdO1xufVxudmFyIFBhdGNoRmxhZ05hbWVzID0ge1xuICBbMV06IGBURVhUYCxcbiAgWzJdOiBgQ0xBU1NgLFxuICBbNF06IGBTVFlMRWAsXG4gIFs4XTogYFBST1BTYCxcbiAgWzE2XTogYEZVTExfUFJPUFNgLFxuICBbMzJdOiBgSFlEUkFURV9FVkVOVFNgLFxuICBbNjRdOiBgU1RBQkxFX0ZSQUdNRU5UYCxcbiAgWzEyOF06IGBLRVlFRF9GUkFHTUVOVGAsXG4gIFsyNTZdOiBgVU5LRVlFRF9GUkFHTUVOVGAsXG4gIFs1MTJdOiBgTkVFRF9QQVRDSGAsXG4gIFsxMDI0XTogYERZTkFNSUNfU0xPVFNgLFxuICBbMjA0OF06IGBERVZfUk9PVF9GUkFHTUVOVGAsXG4gIFstMV06IGBIT0lTVEVEYCxcbiAgWy0yXTogYEJBSUxgXG59O1xudmFyIHNsb3RGbGFnc1RleHQgPSB7XG4gIFsxXTogXCJTVEFCTEVcIixcbiAgWzJdOiBcIkRZTkFNSUNcIixcbiAgWzNdOiBcIkZPUldBUkRFRFwiXG59O1xudmFyIHNwZWNpYWxCb29sZWFuQXR0cnMgPSBgaXRlbXNjb3BlLGFsbG93ZnVsbHNjcmVlbixmb3Jtbm92YWxpZGF0ZSxpc21hcCxub21vZHVsZSxub3ZhbGlkYXRlLHJlYWRvbmx5YDtcbnZhciBpc0Jvb2xlYW5BdHRyMiA9IC8qIEBfX1BVUkVfXyAqLyBtYWtlTWFwKHNwZWNpYWxCb29sZWFuQXR0cnMgKyBgLGFzeW5jLGF1dG9mb2N1cyxhdXRvcGxheSxjb250cm9scyxkZWZhdWx0LGRlZmVyLGRpc2FibGVkLGhpZGRlbixsb29wLG9wZW4scmVxdWlyZWQscmV2ZXJzZWQsc2NvcGVkLHNlYW1sZXNzLGNoZWNrZWQsbXV0ZWQsbXVsdGlwbGUsc2VsZWN0ZWRgKTtcbnZhciBFTVBUWV9PQkogPSB0cnVlID8gT2JqZWN0LmZyZWV6ZSh7fSkgOiB7fTtcbnZhciBFTVBUWV9BUlIgPSB0cnVlID8gT2JqZWN0LmZyZWV6ZShbXSkgOiBbXTtcbnZhciBleHRlbmQgPSBPYmplY3QuYXNzaWduO1xudmFyIGhhc093blByb3BlcnR5ID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eTtcbnZhciBoYXNPd24gPSAodmFsLCBrZXkpID0+IGhhc093blByb3BlcnR5LmNhbGwodmFsLCBrZXkpO1xudmFyIGlzQXJyYXkgPSBBcnJheS5pc0FycmF5O1xudmFyIGlzTWFwID0gKHZhbCkgPT4gdG9UeXBlU3RyaW5nKHZhbCkgPT09IFwiW29iamVjdCBNYXBdXCI7XG52YXIgaXNTdHJpbmcgPSAodmFsKSA9PiB0eXBlb2YgdmFsID09PSBcInN0cmluZ1wiO1xudmFyIGlzU3ltYm9sID0gKHZhbCkgPT4gdHlwZW9mIHZhbCA9PT0gXCJzeW1ib2xcIjtcbnZhciBpc09iamVjdCA9ICh2YWwpID0+IHZhbCAhPT0gbnVsbCAmJiB0eXBlb2YgdmFsID09PSBcIm9iamVjdFwiO1xudmFyIG9iamVjdFRvU3RyaW5nID0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZztcbnZhciB0b1R5cGVTdHJpbmcgPSAodmFsdWUpID0+IG9iamVjdFRvU3RyaW5nLmNhbGwodmFsdWUpO1xudmFyIHRvUmF3VHlwZSA9ICh2YWx1ZSkgPT4ge1xuICByZXR1cm4gdG9UeXBlU3RyaW5nKHZhbHVlKS5zbGljZSg4LCAtMSk7XG59O1xudmFyIGlzSW50ZWdlcktleSA9IChrZXkpID0+IGlzU3RyaW5nKGtleSkgJiYga2V5ICE9PSBcIk5hTlwiICYmIGtleVswXSAhPT0gXCItXCIgJiYgXCJcIiArIHBhcnNlSW50KGtleSwgMTApID09PSBrZXk7XG52YXIgY2FjaGVTdHJpbmdGdW5jdGlvbiA9IChmbikgPT4ge1xuICBjb25zdCBjYWNoZSA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gIHJldHVybiAoc3RyKSA9PiB7XG4gICAgY29uc3QgaGl0ID0gY2FjaGVbc3RyXTtcbiAgICByZXR1cm4gaGl0IHx8IChjYWNoZVtzdHJdID0gZm4oc3RyKSk7XG4gIH07XG59O1xudmFyIGNhbWVsaXplUkUgPSAvLShcXHcpL2c7XG52YXIgY2FtZWxpemUgPSBjYWNoZVN0cmluZ0Z1bmN0aW9uKChzdHIpID0+IHtcbiAgcmV0dXJuIHN0ci5yZXBsYWNlKGNhbWVsaXplUkUsIChfLCBjKSA9PiBjID8gYy50b1VwcGVyQ2FzZSgpIDogXCJcIik7XG59KTtcbnZhciBoeXBoZW5hdGVSRSA9IC9cXEIoW0EtWl0pL2c7XG52YXIgaHlwaGVuYXRlID0gY2FjaGVTdHJpbmdGdW5jdGlvbigoc3RyKSA9PiBzdHIucmVwbGFjZShoeXBoZW5hdGVSRSwgXCItJDFcIikudG9Mb3dlckNhc2UoKSk7XG52YXIgY2FwaXRhbGl6ZSA9IGNhY2hlU3RyaW5nRnVuY3Rpb24oKHN0cikgPT4gc3RyLmNoYXJBdCgwKS50b1VwcGVyQ2FzZSgpICsgc3RyLnNsaWNlKDEpKTtcbnZhciB0b0hhbmRsZXJLZXkgPSBjYWNoZVN0cmluZ0Z1bmN0aW9uKChzdHIpID0+IHN0ciA/IGBvbiR7Y2FwaXRhbGl6ZShzdHIpfWAgOiBgYCk7XG52YXIgaGFzQ2hhbmdlZCA9ICh2YWx1ZSwgb2xkVmFsdWUpID0+IHZhbHVlICE9PSBvbGRWYWx1ZSAmJiAodmFsdWUgPT09IHZhbHVlIHx8IG9sZFZhbHVlID09PSBvbGRWYWx1ZSk7XG5cbi8vIG5vZGVfbW9kdWxlcy9AdnVlL3JlYWN0aXZpdHkvZGlzdC9yZWFjdGl2aXR5LmVzbS1idW5kbGVyLmpzXG52YXIgdGFyZ2V0TWFwID0gbmV3IFdlYWtNYXAoKTtcbnZhciBlZmZlY3RTdGFjayA9IFtdO1xudmFyIGFjdGl2ZUVmZmVjdDtcbnZhciBJVEVSQVRFX0tFWSA9IFN5bWJvbCh0cnVlID8gXCJpdGVyYXRlXCIgOiBcIlwiKTtcbnZhciBNQVBfS0VZX0lURVJBVEVfS0VZID0gU3ltYm9sKHRydWUgPyBcIk1hcCBrZXkgaXRlcmF0ZVwiIDogXCJcIik7XG5mdW5jdGlvbiBpc0VmZmVjdChmbikge1xuICByZXR1cm4gZm4gJiYgZm4uX2lzRWZmZWN0ID09PSB0cnVlO1xufVxuZnVuY3Rpb24gZWZmZWN0Mihmbiwgb3B0aW9ucyA9IEVNUFRZX09CSikge1xuICBpZiAoaXNFZmZlY3QoZm4pKSB7XG4gICAgZm4gPSBmbi5yYXc7XG4gIH1cbiAgY29uc3QgZWZmZWN0MyA9IGNyZWF0ZVJlYWN0aXZlRWZmZWN0KGZuLCBvcHRpb25zKTtcbiAgaWYgKCFvcHRpb25zLmxhenkpIHtcbiAgICBlZmZlY3QzKCk7XG4gIH1cbiAgcmV0dXJuIGVmZmVjdDM7XG59XG5mdW5jdGlvbiBzdG9wKGVmZmVjdDMpIHtcbiAgaWYgKGVmZmVjdDMuYWN0aXZlKSB7XG4gICAgY2xlYW51cChlZmZlY3QzKTtcbiAgICBpZiAoZWZmZWN0My5vcHRpb25zLm9uU3RvcCkge1xuICAgICAgZWZmZWN0My5vcHRpb25zLm9uU3RvcCgpO1xuICAgIH1cbiAgICBlZmZlY3QzLmFjdGl2ZSA9IGZhbHNlO1xuICB9XG59XG52YXIgdWlkID0gMDtcbmZ1bmN0aW9uIGNyZWF0ZVJlYWN0aXZlRWZmZWN0KGZuLCBvcHRpb25zKSB7XG4gIGNvbnN0IGVmZmVjdDMgPSBmdW5jdGlvbiByZWFjdGl2ZUVmZmVjdCgpIHtcbiAgICBpZiAoIWVmZmVjdDMuYWN0aXZlKSB7XG4gICAgICByZXR1cm4gZm4oKTtcbiAgICB9XG4gICAgaWYgKCFlZmZlY3RTdGFjay5pbmNsdWRlcyhlZmZlY3QzKSkge1xuICAgICAgY2xlYW51cChlZmZlY3QzKTtcbiAgICAgIHRyeSB7XG4gICAgICAgIGVuYWJsZVRyYWNraW5nKCk7XG4gICAgICAgIGVmZmVjdFN0YWNrLnB1c2goZWZmZWN0Myk7XG4gICAgICAgIGFjdGl2ZUVmZmVjdCA9IGVmZmVjdDM7XG4gICAgICAgIHJldHVybiBmbigpO1xuICAgICAgfSBmaW5hbGx5IHtcbiAgICAgICAgZWZmZWN0U3RhY2sucG9wKCk7XG4gICAgICAgIHJlc2V0VHJhY2tpbmcoKTtcbiAgICAgICAgYWN0aXZlRWZmZWN0ID0gZWZmZWN0U3RhY2tbZWZmZWN0U3RhY2subGVuZ3RoIC0gMV07XG4gICAgICB9XG4gICAgfVxuICB9O1xuICBlZmZlY3QzLmlkID0gdWlkKys7XG4gIGVmZmVjdDMuYWxsb3dSZWN1cnNlID0gISFvcHRpb25zLmFsbG93UmVjdXJzZTtcbiAgZWZmZWN0My5faXNFZmZlY3QgPSB0cnVlO1xuICBlZmZlY3QzLmFjdGl2ZSA9IHRydWU7XG4gIGVmZmVjdDMucmF3ID0gZm47XG4gIGVmZmVjdDMuZGVwcyA9IFtdO1xuICBlZmZlY3QzLm9wdGlvbnMgPSBvcHRpb25zO1xuICByZXR1cm4gZWZmZWN0Mztcbn1cbmZ1bmN0aW9uIGNsZWFudXAoZWZmZWN0Mykge1xuICBjb25zdCB7ZGVwc30gPSBlZmZlY3QzO1xuICBpZiAoZGVwcy5sZW5ndGgpIHtcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IGRlcHMubGVuZ3RoOyBpKyspIHtcbiAgICAgIGRlcHNbaV0uZGVsZXRlKGVmZmVjdDMpO1xuICAgIH1cbiAgICBkZXBzLmxlbmd0aCA9IDA7XG4gIH1cbn1cbnZhciBzaG91bGRUcmFjayA9IHRydWU7XG52YXIgdHJhY2tTdGFjayA9IFtdO1xuZnVuY3Rpb24gcGF1c2VUcmFja2luZygpIHtcbiAgdHJhY2tTdGFjay5wdXNoKHNob3VsZFRyYWNrKTtcbiAgc2hvdWxkVHJhY2sgPSBmYWxzZTtcbn1cbmZ1bmN0aW9uIGVuYWJsZVRyYWNraW5nKCkge1xuICB0cmFja1N0YWNrLnB1c2goc2hvdWxkVHJhY2spO1xuICBzaG91bGRUcmFjayA9IHRydWU7XG59XG5mdW5jdGlvbiByZXNldFRyYWNraW5nKCkge1xuICBjb25zdCBsYXN0ID0gdHJhY2tTdGFjay5wb3AoKTtcbiAgc2hvdWxkVHJhY2sgPSBsYXN0ID09PSB2b2lkIDAgPyB0cnVlIDogbGFzdDtcbn1cbmZ1bmN0aW9uIHRyYWNrKHRhcmdldCwgdHlwZSwga2V5KSB7XG4gIGlmICghc2hvdWxkVHJhY2sgfHwgYWN0aXZlRWZmZWN0ID09PSB2b2lkIDApIHtcbiAgICByZXR1cm47XG4gIH1cbiAgbGV0IGRlcHNNYXAgPSB0YXJnZXRNYXAuZ2V0KHRhcmdldCk7XG4gIGlmICghZGVwc01hcCkge1xuICAgIHRhcmdldE1hcC5zZXQodGFyZ2V0LCBkZXBzTWFwID0gbmV3IE1hcCgpKTtcbiAgfVxuICBsZXQgZGVwID0gZGVwc01hcC5nZXQoa2V5KTtcbiAgaWYgKCFkZXApIHtcbiAgICBkZXBzTWFwLnNldChrZXksIGRlcCA9IG5ldyBTZXQoKSk7XG4gIH1cbiAgaWYgKCFkZXAuaGFzKGFjdGl2ZUVmZmVjdCkpIHtcbiAgICBkZXAuYWRkKGFjdGl2ZUVmZmVjdCk7XG4gICAgYWN0aXZlRWZmZWN0LmRlcHMucHVzaChkZXApO1xuICAgIGlmIChhY3RpdmVFZmZlY3Qub3B0aW9ucy5vblRyYWNrKSB7XG4gICAgICBhY3RpdmVFZmZlY3Qub3B0aW9ucy5vblRyYWNrKHtcbiAgICAgICAgZWZmZWN0OiBhY3RpdmVFZmZlY3QsXG4gICAgICAgIHRhcmdldCxcbiAgICAgICAgdHlwZSxcbiAgICAgICAga2V5XG4gICAgICB9KTtcbiAgICB9XG4gIH1cbn1cbmZ1bmN0aW9uIHRyaWdnZXIodGFyZ2V0LCB0eXBlLCBrZXksIG5ld1ZhbHVlLCBvbGRWYWx1ZSwgb2xkVGFyZ2V0KSB7XG4gIGNvbnN0IGRlcHNNYXAgPSB0YXJnZXRNYXAuZ2V0KHRhcmdldCk7XG4gIGlmICghZGVwc01hcCkge1xuICAgIHJldHVybjtcbiAgfVxuICBjb25zdCBlZmZlY3RzID0gbmV3IFNldCgpO1xuICBjb25zdCBhZGQyID0gKGVmZmVjdHNUb0FkZCkgPT4ge1xuICAgIGlmIChlZmZlY3RzVG9BZGQpIHtcbiAgICAgIGVmZmVjdHNUb0FkZC5mb3JFYWNoKChlZmZlY3QzKSA9PiB7XG4gICAgICAgIGlmIChlZmZlY3QzICE9PSBhY3RpdmVFZmZlY3QgfHwgZWZmZWN0My5hbGxvd1JlY3Vyc2UpIHtcbiAgICAgICAgICBlZmZlY3RzLmFkZChlZmZlY3QzKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfVxuICB9O1xuICBpZiAodHlwZSA9PT0gXCJjbGVhclwiKSB7XG4gICAgZGVwc01hcC5mb3JFYWNoKGFkZDIpO1xuICB9IGVsc2UgaWYgKGtleSA9PT0gXCJsZW5ndGhcIiAmJiBpc0FycmF5KHRhcmdldCkpIHtcbiAgICBkZXBzTWFwLmZvckVhY2goKGRlcCwga2V5MikgPT4ge1xuICAgICAgaWYgKGtleTIgPT09IFwibGVuZ3RoXCIgfHwga2V5MiA+PSBuZXdWYWx1ZSkge1xuICAgICAgICBhZGQyKGRlcCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH0gZWxzZSB7XG4gICAgaWYgKGtleSAhPT0gdm9pZCAwKSB7XG4gICAgICBhZGQyKGRlcHNNYXAuZ2V0KGtleSkpO1xuICAgIH1cbiAgICBzd2l0Y2ggKHR5cGUpIHtcbiAgICAgIGNhc2UgXCJhZGRcIjpcbiAgICAgICAgaWYgKCFpc0FycmF5KHRhcmdldCkpIHtcbiAgICAgICAgICBhZGQyKGRlcHNNYXAuZ2V0KElURVJBVEVfS0VZKSk7XG4gICAgICAgICAgaWYgKGlzTWFwKHRhcmdldCkpIHtcbiAgICAgICAgICAgIGFkZDIoZGVwc01hcC5nZXQoTUFQX0tFWV9JVEVSQVRFX0tFWSkpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIGlmIChpc0ludGVnZXJLZXkoa2V5KSkge1xuICAgICAgICAgIGFkZDIoZGVwc01hcC5nZXQoXCJsZW5ndGhcIikpO1xuICAgICAgICB9XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSBcImRlbGV0ZVwiOlxuICAgICAgICBpZiAoIWlzQXJyYXkodGFyZ2V0KSkge1xuICAgICAgICAgIGFkZDIoZGVwc01hcC5nZXQoSVRFUkFURV9LRVkpKTtcbiAgICAgICAgICBpZiAoaXNNYXAodGFyZ2V0KSkge1xuICAgICAgICAgICAgYWRkMihkZXBzTWFwLmdldChNQVBfS0VZX0lURVJBVEVfS0VZKSk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSBcInNldFwiOlxuICAgICAgICBpZiAoaXNNYXAodGFyZ2V0KSkge1xuICAgICAgICAgIGFkZDIoZGVwc01hcC5nZXQoSVRFUkFURV9LRVkpKTtcbiAgICAgICAgfVxuICAgICAgICBicmVhaztcbiAgICB9XG4gIH1cbiAgY29uc3QgcnVuID0gKGVmZmVjdDMpID0+IHtcbiAgICBpZiAoZWZmZWN0My5vcHRpb25zLm9uVHJpZ2dlcikge1xuICAgICAgZWZmZWN0My5vcHRpb25zLm9uVHJpZ2dlcih7XG4gICAgICAgIGVmZmVjdDogZWZmZWN0MyxcbiAgICAgICAgdGFyZ2V0LFxuICAgICAgICBrZXksXG4gICAgICAgIHR5cGUsXG4gICAgICAgIG5ld1ZhbHVlLFxuICAgICAgICBvbGRWYWx1ZSxcbiAgICAgICAgb2xkVGFyZ2V0XG4gICAgICB9KTtcbiAgICB9XG4gICAgaWYgKGVmZmVjdDMub3B0aW9ucy5zY2hlZHVsZXIpIHtcbiAgICAgIGVmZmVjdDMub3B0aW9ucy5zY2hlZHVsZXIoZWZmZWN0Myk7XG4gICAgfSBlbHNlIHtcbiAgICAgIGVmZmVjdDMoKTtcbiAgICB9XG4gIH07XG4gIGVmZmVjdHMuZm9yRWFjaChydW4pO1xufVxudmFyIGlzTm9uVHJhY2thYmxlS2V5cyA9IC8qIEBfX1BVUkVfXyAqLyBtYWtlTWFwKGBfX3Byb3RvX18sX192X2lzUmVmLF9faXNWdWVgKTtcbnZhciBidWlsdEluU3ltYm9scyA9IG5ldyBTZXQoT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoU3ltYm9sKS5tYXAoKGtleSkgPT4gU3ltYm9sW2tleV0pLmZpbHRlcihpc1N5bWJvbCkpO1xudmFyIGdldDIgPSAvKiBAX19QVVJFX18gKi8gY3JlYXRlR2V0dGVyKCk7XG52YXIgc2hhbGxvd0dldCA9IC8qIEBfX1BVUkVfXyAqLyBjcmVhdGVHZXR0ZXIoZmFsc2UsIHRydWUpO1xudmFyIHJlYWRvbmx5R2V0ID0gLyogQF9fUFVSRV9fICovIGNyZWF0ZUdldHRlcih0cnVlKTtcbnZhciBzaGFsbG93UmVhZG9ubHlHZXQgPSAvKiBAX19QVVJFX18gKi8gY3JlYXRlR2V0dGVyKHRydWUsIHRydWUpO1xudmFyIGFycmF5SW5zdHJ1bWVudGF0aW9ucyA9IHt9O1xuW1wiaW5jbHVkZXNcIiwgXCJpbmRleE9mXCIsIFwibGFzdEluZGV4T2ZcIl0uZm9yRWFjaCgoa2V5KSA9PiB7XG4gIGNvbnN0IG1ldGhvZCA9IEFycmF5LnByb3RvdHlwZVtrZXldO1xuICBhcnJheUluc3RydW1lbnRhdGlvbnNba2V5XSA9IGZ1bmN0aW9uKC4uLmFyZ3MpIHtcbiAgICBjb25zdCBhcnIgPSB0b1Jhdyh0aGlzKTtcbiAgICBmb3IgKGxldCBpID0gMCwgbCA9IHRoaXMubGVuZ3RoOyBpIDwgbDsgaSsrKSB7XG4gICAgICB0cmFjayhhcnIsIFwiZ2V0XCIsIGkgKyBcIlwiKTtcbiAgICB9XG4gICAgY29uc3QgcmVzID0gbWV0aG9kLmFwcGx5KGFyciwgYXJncyk7XG4gICAgaWYgKHJlcyA9PT0gLTEgfHwgcmVzID09PSBmYWxzZSkge1xuICAgICAgcmV0dXJuIG1ldGhvZC5hcHBseShhcnIsIGFyZ3MubWFwKHRvUmF3KSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiByZXM7XG4gICAgfVxuICB9O1xufSk7XG5bXCJwdXNoXCIsIFwicG9wXCIsIFwic2hpZnRcIiwgXCJ1bnNoaWZ0XCIsIFwic3BsaWNlXCJdLmZvckVhY2goKGtleSkgPT4ge1xuICBjb25zdCBtZXRob2QgPSBBcnJheS5wcm90b3R5cGVba2V5XTtcbiAgYXJyYXlJbnN0cnVtZW50YXRpb25zW2tleV0gPSBmdW5jdGlvbiguLi5hcmdzKSB7XG4gICAgcGF1c2VUcmFja2luZygpO1xuICAgIGNvbnN0IHJlcyA9IG1ldGhvZC5hcHBseSh0aGlzLCBhcmdzKTtcbiAgICByZXNldFRyYWNraW5nKCk7XG4gICAgcmV0dXJuIHJlcztcbiAgfTtcbn0pO1xuZnVuY3Rpb24gY3JlYXRlR2V0dGVyKGlzUmVhZG9ubHkgPSBmYWxzZSwgc2hhbGxvdyA9IGZhbHNlKSB7XG4gIHJldHVybiBmdW5jdGlvbiBnZXQzKHRhcmdldCwga2V5LCByZWNlaXZlcikge1xuICAgIGlmIChrZXkgPT09IFwiX192X2lzUmVhY3RpdmVcIikge1xuICAgICAgcmV0dXJuICFpc1JlYWRvbmx5O1xuICAgIH0gZWxzZSBpZiAoa2V5ID09PSBcIl9fdl9pc1JlYWRvbmx5XCIpIHtcbiAgICAgIHJldHVybiBpc1JlYWRvbmx5O1xuICAgIH0gZWxzZSBpZiAoa2V5ID09PSBcIl9fdl9yYXdcIiAmJiByZWNlaXZlciA9PT0gKGlzUmVhZG9ubHkgPyBzaGFsbG93ID8gc2hhbGxvd1JlYWRvbmx5TWFwIDogcmVhZG9ubHlNYXAgOiBzaGFsbG93ID8gc2hhbGxvd1JlYWN0aXZlTWFwIDogcmVhY3RpdmVNYXApLmdldCh0YXJnZXQpKSB7XG4gICAgICByZXR1cm4gdGFyZ2V0O1xuICAgIH1cbiAgICBjb25zdCB0YXJnZXRJc0FycmF5ID0gaXNBcnJheSh0YXJnZXQpO1xuICAgIGlmICghaXNSZWFkb25seSAmJiB0YXJnZXRJc0FycmF5ICYmIGhhc093bihhcnJheUluc3RydW1lbnRhdGlvbnMsIGtleSkpIHtcbiAgICAgIHJldHVybiBSZWZsZWN0LmdldChhcnJheUluc3RydW1lbnRhdGlvbnMsIGtleSwgcmVjZWl2ZXIpO1xuICAgIH1cbiAgICBjb25zdCByZXMgPSBSZWZsZWN0LmdldCh0YXJnZXQsIGtleSwgcmVjZWl2ZXIpO1xuICAgIGlmIChpc1N5bWJvbChrZXkpID8gYnVpbHRJblN5bWJvbHMuaGFzKGtleSkgOiBpc05vblRyYWNrYWJsZUtleXMoa2V5KSkge1xuICAgICAgcmV0dXJuIHJlcztcbiAgICB9XG4gICAgaWYgKCFpc1JlYWRvbmx5KSB7XG4gICAgICB0cmFjayh0YXJnZXQsIFwiZ2V0XCIsIGtleSk7XG4gICAgfVxuICAgIGlmIChzaGFsbG93KSB7XG4gICAgICByZXR1cm4gcmVzO1xuICAgIH1cbiAgICBpZiAoaXNSZWYocmVzKSkge1xuICAgICAgY29uc3Qgc2hvdWxkVW53cmFwID0gIXRhcmdldElzQXJyYXkgfHwgIWlzSW50ZWdlcktleShrZXkpO1xuICAgICAgcmV0dXJuIHNob3VsZFVud3JhcCA/IHJlcy52YWx1ZSA6IHJlcztcbiAgICB9XG4gICAgaWYgKGlzT2JqZWN0KHJlcykpIHtcbiAgICAgIHJldHVybiBpc1JlYWRvbmx5ID8gcmVhZG9ubHkocmVzKSA6IHJlYWN0aXZlMihyZXMpO1xuICAgIH1cbiAgICByZXR1cm4gcmVzO1xuICB9O1xufVxudmFyIHNldDIgPSAvKiBAX19QVVJFX18gKi8gY3JlYXRlU2V0dGVyKCk7XG52YXIgc2hhbGxvd1NldCA9IC8qIEBfX1BVUkVfXyAqLyBjcmVhdGVTZXR0ZXIodHJ1ZSk7XG5mdW5jdGlvbiBjcmVhdGVTZXR0ZXIoc2hhbGxvdyA9IGZhbHNlKSB7XG4gIHJldHVybiBmdW5jdGlvbiBzZXQzKHRhcmdldCwga2V5LCB2YWx1ZSwgcmVjZWl2ZXIpIHtcbiAgICBsZXQgb2xkVmFsdWUgPSB0YXJnZXRba2V5XTtcbiAgICBpZiAoIXNoYWxsb3cpIHtcbiAgICAgIHZhbHVlID0gdG9SYXcodmFsdWUpO1xuICAgICAgb2xkVmFsdWUgPSB0b1JhdyhvbGRWYWx1ZSk7XG4gICAgICBpZiAoIWlzQXJyYXkodGFyZ2V0KSAmJiBpc1JlZihvbGRWYWx1ZSkgJiYgIWlzUmVmKHZhbHVlKSkge1xuICAgICAgICBvbGRWYWx1ZS52YWx1ZSA9IHZhbHVlO1xuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgIH1cbiAgICB9XG4gICAgY29uc3QgaGFkS2V5ID0gaXNBcnJheSh0YXJnZXQpICYmIGlzSW50ZWdlcktleShrZXkpID8gTnVtYmVyKGtleSkgPCB0YXJnZXQubGVuZ3RoIDogaGFzT3duKHRhcmdldCwga2V5KTtcbiAgICBjb25zdCByZXN1bHQgPSBSZWZsZWN0LnNldCh0YXJnZXQsIGtleSwgdmFsdWUsIHJlY2VpdmVyKTtcbiAgICBpZiAodGFyZ2V0ID09PSB0b1JhdyhyZWNlaXZlcikpIHtcbiAgICAgIGlmICghaGFkS2V5KSB7XG4gICAgICAgIHRyaWdnZXIodGFyZ2V0LCBcImFkZFwiLCBrZXksIHZhbHVlKTtcbiAgICAgIH0gZWxzZSBpZiAoaGFzQ2hhbmdlZCh2YWx1ZSwgb2xkVmFsdWUpKSB7XG4gICAgICAgIHRyaWdnZXIodGFyZ2V0LCBcInNldFwiLCBrZXksIHZhbHVlLCBvbGRWYWx1ZSk7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiByZXN1bHQ7XG4gIH07XG59XG5mdW5jdGlvbiBkZWxldGVQcm9wZXJ0eSh0YXJnZXQsIGtleSkge1xuICBjb25zdCBoYWRLZXkgPSBoYXNPd24odGFyZ2V0LCBrZXkpO1xuICBjb25zdCBvbGRWYWx1ZSA9IHRhcmdldFtrZXldO1xuICBjb25zdCByZXN1bHQgPSBSZWZsZWN0LmRlbGV0ZVByb3BlcnR5KHRhcmdldCwga2V5KTtcbiAgaWYgKHJlc3VsdCAmJiBoYWRLZXkpIHtcbiAgICB0cmlnZ2VyKHRhcmdldCwgXCJkZWxldGVcIiwga2V5LCB2b2lkIDAsIG9sZFZhbHVlKTtcbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuZnVuY3Rpb24gaGFzKHRhcmdldCwga2V5KSB7XG4gIGNvbnN0IHJlc3VsdCA9IFJlZmxlY3QuaGFzKHRhcmdldCwga2V5KTtcbiAgaWYgKCFpc1N5bWJvbChrZXkpIHx8ICFidWlsdEluU3ltYm9scy5oYXMoa2V5KSkge1xuICAgIHRyYWNrKHRhcmdldCwgXCJoYXNcIiwga2V5KTtcbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuZnVuY3Rpb24gb3duS2V5cyh0YXJnZXQpIHtcbiAgdHJhY2sodGFyZ2V0LCBcIml0ZXJhdGVcIiwgaXNBcnJheSh0YXJnZXQpID8gXCJsZW5ndGhcIiA6IElURVJBVEVfS0VZKTtcbiAgcmV0dXJuIFJlZmxlY3Qub3duS2V5cyh0YXJnZXQpO1xufVxudmFyIG11dGFibGVIYW5kbGVycyA9IHtcbiAgZ2V0OiBnZXQyLFxuICBzZXQ6IHNldDIsXG4gIGRlbGV0ZVByb3BlcnR5LFxuICBoYXMsXG4gIG93bktleXNcbn07XG52YXIgcmVhZG9ubHlIYW5kbGVycyA9IHtcbiAgZ2V0OiByZWFkb25seUdldCxcbiAgc2V0KHRhcmdldCwga2V5KSB7XG4gICAgaWYgKHRydWUpIHtcbiAgICAgIGNvbnNvbGUud2FybihgU2V0IG9wZXJhdGlvbiBvbiBrZXkgXCIke1N0cmluZyhrZXkpfVwiIGZhaWxlZDogdGFyZ2V0IGlzIHJlYWRvbmx5LmAsIHRhcmdldCk7XG4gICAgfVxuICAgIHJldHVybiB0cnVlO1xuICB9LFxuICBkZWxldGVQcm9wZXJ0eSh0YXJnZXQsIGtleSkge1xuICAgIGlmICh0cnVlKSB7XG4gICAgICBjb25zb2xlLndhcm4oYERlbGV0ZSBvcGVyYXRpb24gb24ga2V5IFwiJHtTdHJpbmcoa2V5KX1cIiBmYWlsZWQ6IHRhcmdldCBpcyByZWFkb25seS5gLCB0YXJnZXQpO1xuICAgIH1cbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxufTtcbnZhciBzaGFsbG93UmVhY3RpdmVIYW5kbGVycyA9IGV4dGVuZCh7fSwgbXV0YWJsZUhhbmRsZXJzLCB7XG4gIGdldDogc2hhbGxvd0dldCxcbiAgc2V0OiBzaGFsbG93U2V0XG59KTtcbnZhciBzaGFsbG93UmVhZG9ubHlIYW5kbGVycyA9IGV4dGVuZCh7fSwgcmVhZG9ubHlIYW5kbGVycywge1xuICBnZXQ6IHNoYWxsb3dSZWFkb25seUdldFxufSk7XG52YXIgdG9SZWFjdGl2ZSA9ICh2YWx1ZSkgPT4gaXNPYmplY3QodmFsdWUpID8gcmVhY3RpdmUyKHZhbHVlKSA6IHZhbHVlO1xudmFyIHRvUmVhZG9ubHkgPSAodmFsdWUpID0+IGlzT2JqZWN0KHZhbHVlKSA/IHJlYWRvbmx5KHZhbHVlKSA6IHZhbHVlO1xudmFyIHRvU2hhbGxvdyA9ICh2YWx1ZSkgPT4gdmFsdWU7XG52YXIgZ2V0UHJvdG8gPSAodikgPT4gUmVmbGVjdC5nZXRQcm90b3R5cGVPZih2KTtcbmZ1bmN0aW9uIGdldCQxKHRhcmdldCwga2V5LCBpc1JlYWRvbmx5ID0gZmFsc2UsIGlzU2hhbGxvdyA9IGZhbHNlKSB7XG4gIHRhcmdldCA9IHRhcmdldFtcIl9fdl9yYXdcIl07XG4gIGNvbnN0IHJhd1RhcmdldCA9IHRvUmF3KHRhcmdldCk7XG4gIGNvbnN0IHJhd0tleSA9IHRvUmF3KGtleSk7XG4gIGlmIChrZXkgIT09IHJhd0tleSkge1xuICAgICFpc1JlYWRvbmx5ICYmIHRyYWNrKHJhd1RhcmdldCwgXCJnZXRcIiwga2V5KTtcbiAgfVxuICAhaXNSZWFkb25seSAmJiB0cmFjayhyYXdUYXJnZXQsIFwiZ2V0XCIsIHJhd0tleSk7XG4gIGNvbnN0IHtoYXM6IGhhczJ9ID0gZ2V0UHJvdG8ocmF3VGFyZ2V0KTtcbiAgY29uc3Qgd3JhcCA9IGlzU2hhbGxvdyA/IHRvU2hhbGxvdyA6IGlzUmVhZG9ubHkgPyB0b1JlYWRvbmx5IDogdG9SZWFjdGl2ZTtcbiAgaWYgKGhhczIuY2FsbChyYXdUYXJnZXQsIGtleSkpIHtcbiAgICByZXR1cm4gd3JhcCh0YXJnZXQuZ2V0KGtleSkpO1xuICB9IGVsc2UgaWYgKGhhczIuY2FsbChyYXdUYXJnZXQsIHJhd0tleSkpIHtcbiAgICByZXR1cm4gd3JhcCh0YXJnZXQuZ2V0KHJhd0tleSkpO1xuICB9IGVsc2UgaWYgKHRhcmdldCAhPT0gcmF3VGFyZ2V0KSB7XG4gICAgdGFyZ2V0LmdldChrZXkpO1xuICB9XG59XG5mdW5jdGlvbiBoYXMkMShrZXksIGlzUmVhZG9ubHkgPSBmYWxzZSkge1xuICBjb25zdCB0YXJnZXQgPSB0aGlzW1wiX192X3Jhd1wiXTtcbiAgY29uc3QgcmF3VGFyZ2V0ID0gdG9SYXcodGFyZ2V0KTtcbiAgY29uc3QgcmF3S2V5ID0gdG9SYXcoa2V5KTtcbiAgaWYgKGtleSAhPT0gcmF3S2V5KSB7XG4gICAgIWlzUmVhZG9ubHkgJiYgdHJhY2socmF3VGFyZ2V0LCBcImhhc1wiLCBrZXkpO1xuICB9XG4gICFpc1JlYWRvbmx5ICYmIHRyYWNrKHJhd1RhcmdldCwgXCJoYXNcIiwgcmF3S2V5KTtcbiAgcmV0dXJuIGtleSA9PT0gcmF3S2V5ID8gdGFyZ2V0LmhhcyhrZXkpIDogdGFyZ2V0LmhhcyhrZXkpIHx8IHRhcmdldC5oYXMocmF3S2V5KTtcbn1cbmZ1bmN0aW9uIHNpemUodGFyZ2V0LCBpc1JlYWRvbmx5ID0gZmFsc2UpIHtcbiAgdGFyZ2V0ID0gdGFyZ2V0W1wiX192X3Jhd1wiXTtcbiAgIWlzUmVhZG9ubHkgJiYgdHJhY2sodG9SYXcodGFyZ2V0KSwgXCJpdGVyYXRlXCIsIElURVJBVEVfS0VZKTtcbiAgcmV0dXJuIFJlZmxlY3QuZ2V0KHRhcmdldCwgXCJzaXplXCIsIHRhcmdldCk7XG59XG5mdW5jdGlvbiBhZGQodmFsdWUpIHtcbiAgdmFsdWUgPSB0b1Jhdyh2YWx1ZSk7XG4gIGNvbnN0IHRhcmdldCA9IHRvUmF3KHRoaXMpO1xuICBjb25zdCBwcm90byA9IGdldFByb3RvKHRhcmdldCk7XG4gIGNvbnN0IGhhZEtleSA9IHByb3RvLmhhcy5jYWxsKHRhcmdldCwgdmFsdWUpO1xuICBpZiAoIWhhZEtleSkge1xuICAgIHRhcmdldC5hZGQodmFsdWUpO1xuICAgIHRyaWdnZXIodGFyZ2V0LCBcImFkZFwiLCB2YWx1ZSwgdmFsdWUpO1xuICB9XG4gIHJldHVybiB0aGlzO1xufVxuZnVuY3Rpb24gc2V0JDEoa2V5LCB2YWx1ZSkge1xuICB2YWx1ZSA9IHRvUmF3KHZhbHVlKTtcbiAgY29uc3QgdGFyZ2V0ID0gdG9SYXcodGhpcyk7XG4gIGNvbnN0IHtoYXM6IGhhczIsIGdldDogZ2V0M30gPSBnZXRQcm90byh0YXJnZXQpO1xuICBsZXQgaGFkS2V5ID0gaGFzMi5jYWxsKHRhcmdldCwga2V5KTtcbiAgaWYgKCFoYWRLZXkpIHtcbiAgICBrZXkgPSB0b1JhdyhrZXkpO1xuICAgIGhhZEtleSA9IGhhczIuY2FsbCh0YXJnZXQsIGtleSk7XG4gIH0gZWxzZSBpZiAodHJ1ZSkge1xuICAgIGNoZWNrSWRlbnRpdHlLZXlzKHRhcmdldCwgaGFzMiwga2V5KTtcbiAgfVxuICBjb25zdCBvbGRWYWx1ZSA9IGdldDMuY2FsbCh0YXJnZXQsIGtleSk7XG4gIHRhcmdldC5zZXQoa2V5LCB2YWx1ZSk7XG4gIGlmICghaGFkS2V5KSB7XG4gICAgdHJpZ2dlcih0YXJnZXQsIFwiYWRkXCIsIGtleSwgdmFsdWUpO1xuICB9IGVsc2UgaWYgKGhhc0NoYW5nZWQodmFsdWUsIG9sZFZhbHVlKSkge1xuICAgIHRyaWdnZXIodGFyZ2V0LCBcInNldFwiLCBrZXksIHZhbHVlLCBvbGRWYWx1ZSk7XG4gIH1cbiAgcmV0dXJuIHRoaXM7XG59XG5mdW5jdGlvbiBkZWxldGVFbnRyeShrZXkpIHtcbiAgY29uc3QgdGFyZ2V0ID0gdG9SYXcodGhpcyk7XG4gIGNvbnN0IHtoYXM6IGhhczIsIGdldDogZ2V0M30gPSBnZXRQcm90byh0YXJnZXQpO1xuICBsZXQgaGFkS2V5ID0gaGFzMi5jYWxsKHRhcmdldCwga2V5KTtcbiAgaWYgKCFoYWRLZXkpIHtcbiAgICBrZXkgPSB0b1JhdyhrZXkpO1xuICAgIGhhZEtleSA9IGhhczIuY2FsbCh0YXJnZXQsIGtleSk7XG4gIH0gZWxzZSBpZiAodHJ1ZSkge1xuICAgIGNoZWNrSWRlbnRpdHlLZXlzKHRhcmdldCwgaGFzMiwga2V5KTtcbiAgfVxuICBjb25zdCBvbGRWYWx1ZSA9IGdldDMgPyBnZXQzLmNhbGwodGFyZ2V0LCBrZXkpIDogdm9pZCAwO1xuICBjb25zdCByZXN1bHQgPSB0YXJnZXQuZGVsZXRlKGtleSk7XG4gIGlmIChoYWRLZXkpIHtcbiAgICB0cmlnZ2VyKHRhcmdldCwgXCJkZWxldGVcIiwga2V5LCB2b2lkIDAsIG9sZFZhbHVlKTtcbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuZnVuY3Rpb24gY2xlYXIoKSB7XG4gIGNvbnN0IHRhcmdldCA9IHRvUmF3KHRoaXMpO1xuICBjb25zdCBoYWRJdGVtcyA9IHRhcmdldC5zaXplICE9PSAwO1xuICBjb25zdCBvbGRUYXJnZXQgPSB0cnVlID8gaXNNYXAodGFyZ2V0KSA/IG5ldyBNYXAodGFyZ2V0KSA6IG5ldyBTZXQodGFyZ2V0KSA6IHZvaWQgMDtcbiAgY29uc3QgcmVzdWx0ID0gdGFyZ2V0LmNsZWFyKCk7XG4gIGlmIChoYWRJdGVtcykge1xuICAgIHRyaWdnZXIodGFyZ2V0LCBcImNsZWFyXCIsIHZvaWQgMCwgdm9pZCAwLCBvbGRUYXJnZXQpO1xuICB9XG4gIHJldHVybiByZXN1bHQ7XG59XG5mdW5jdGlvbiBjcmVhdGVGb3JFYWNoKGlzUmVhZG9ubHksIGlzU2hhbGxvdykge1xuICByZXR1cm4gZnVuY3Rpb24gZm9yRWFjaChjYWxsYmFjaywgdGhpc0FyZykge1xuICAgIGNvbnN0IG9ic2VydmVkID0gdGhpcztcbiAgICBjb25zdCB0YXJnZXQgPSBvYnNlcnZlZFtcIl9fdl9yYXdcIl07XG4gICAgY29uc3QgcmF3VGFyZ2V0ID0gdG9SYXcodGFyZ2V0KTtcbiAgICBjb25zdCB3cmFwID0gaXNTaGFsbG93ID8gdG9TaGFsbG93IDogaXNSZWFkb25seSA/IHRvUmVhZG9ubHkgOiB0b1JlYWN0aXZlO1xuICAgICFpc1JlYWRvbmx5ICYmIHRyYWNrKHJhd1RhcmdldCwgXCJpdGVyYXRlXCIsIElURVJBVEVfS0VZKTtcbiAgICByZXR1cm4gdGFyZ2V0LmZvckVhY2goKHZhbHVlLCBrZXkpID0+IHtcbiAgICAgIHJldHVybiBjYWxsYmFjay5jYWxsKHRoaXNBcmcsIHdyYXAodmFsdWUpLCB3cmFwKGtleSksIG9ic2VydmVkKTtcbiAgICB9KTtcbiAgfTtcbn1cbmZ1bmN0aW9uIGNyZWF0ZUl0ZXJhYmxlTWV0aG9kKG1ldGhvZCwgaXNSZWFkb25seSwgaXNTaGFsbG93KSB7XG4gIHJldHVybiBmdW5jdGlvbiguLi5hcmdzKSB7XG4gICAgY29uc3QgdGFyZ2V0ID0gdGhpc1tcIl9fdl9yYXdcIl07XG4gICAgY29uc3QgcmF3VGFyZ2V0ID0gdG9SYXcodGFyZ2V0KTtcbiAgICBjb25zdCB0YXJnZXRJc01hcCA9IGlzTWFwKHJhd1RhcmdldCk7XG4gICAgY29uc3QgaXNQYWlyID0gbWV0aG9kID09PSBcImVudHJpZXNcIiB8fCBtZXRob2QgPT09IFN5bWJvbC5pdGVyYXRvciAmJiB0YXJnZXRJc01hcDtcbiAgICBjb25zdCBpc0tleU9ubHkgPSBtZXRob2QgPT09IFwia2V5c1wiICYmIHRhcmdldElzTWFwO1xuICAgIGNvbnN0IGlubmVySXRlcmF0b3IgPSB0YXJnZXRbbWV0aG9kXSguLi5hcmdzKTtcbiAgICBjb25zdCB3cmFwID0gaXNTaGFsbG93ID8gdG9TaGFsbG93IDogaXNSZWFkb25seSA/IHRvUmVhZG9ubHkgOiB0b1JlYWN0aXZlO1xuICAgICFpc1JlYWRvbmx5ICYmIHRyYWNrKHJhd1RhcmdldCwgXCJpdGVyYXRlXCIsIGlzS2V5T25seSA/IE1BUF9LRVlfSVRFUkFURV9LRVkgOiBJVEVSQVRFX0tFWSk7XG4gICAgcmV0dXJuIHtcbiAgICAgIG5leHQoKSB7XG4gICAgICAgIGNvbnN0IHt2YWx1ZSwgZG9uZX0gPSBpbm5lckl0ZXJhdG9yLm5leHQoKTtcbiAgICAgICAgcmV0dXJuIGRvbmUgPyB7dmFsdWUsIGRvbmV9IDoge1xuICAgICAgICAgIHZhbHVlOiBpc1BhaXIgPyBbd3JhcCh2YWx1ZVswXSksIHdyYXAodmFsdWVbMV0pXSA6IHdyYXAodmFsdWUpLFxuICAgICAgICAgIGRvbmVcbiAgICAgICAgfTtcbiAgICAgIH0sXG4gICAgICBbU3ltYm9sLml0ZXJhdG9yXSgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG4gICAgfTtcbiAgfTtcbn1cbmZ1bmN0aW9uIGNyZWF0ZVJlYWRvbmx5TWV0aG9kKHR5cGUpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uKC4uLmFyZ3MpIHtcbiAgICBpZiAodHJ1ZSkge1xuICAgICAgY29uc3Qga2V5ID0gYXJnc1swXSA/IGBvbiBrZXkgXCIke2FyZ3NbMF19XCIgYCA6IGBgO1xuICAgICAgY29uc29sZS53YXJuKGAke2NhcGl0YWxpemUodHlwZSl9IG9wZXJhdGlvbiAke2tleX1mYWlsZWQ6IHRhcmdldCBpcyByZWFkb25seS5gLCB0b1Jhdyh0aGlzKSk7XG4gICAgfVxuICAgIHJldHVybiB0eXBlID09PSBcImRlbGV0ZVwiID8gZmFsc2UgOiB0aGlzO1xuICB9O1xufVxudmFyIG11dGFibGVJbnN0cnVtZW50YXRpb25zID0ge1xuICBnZXQoa2V5KSB7XG4gICAgcmV0dXJuIGdldCQxKHRoaXMsIGtleSk7XG4gIH0sXG4gIGdldCBzaXplKCkge1xuICAgIHJldHVybiBzaXplKHRoaXMpO1xuICB9LFxuICBoYXM6IGhhcyQxLFxuICBhZGQsXG4gIHNldDogc2V0JDEsXG4gIGRlbGV0ZTogZGVsZXRlRW50cnksXG4gIGNsZWFyLFxuICBmb3JFYWNoOiBjcmVhdGVGb3JFYWNoKGZhbHNlLCBmYWxzZSlcbn07XG52YXIgc2hhbGxvd0luc3RydW1lbnRhdGlvbnMgPSB7XG4gIGdldChrZXkpIHtcbiAgICByZXR1cm4gZ2V0JDEodGhpcywga2V5LCBmYWxzZSwgdHJ1ZSk7XG4gIH0sXG4gIGdldCBzaXplKCkge1xuICAgIHJldHVybiBzaXplKHRoaXMpO1xuICB9LFxuICBoYXM6IGhhcyQxLFxuICBhZGQsXG4gIHNldDogc2V0JDEsXG4gIGRlbGV0ZTogZGVsZXRlRW50cnksXG4gIGNsZWFyLFxuICBmb3JFYWNoOiBjcmVhdGVGb3JFYWNoKGZhbHNlLCB0cnVlKVxufTtcbnZhciByZWFkb25seUluc3RydW1lbnRhdGlvbnMgPSB7XG4gIGdldChrZXkpIHtcbiAgICByZXR1cm4gZ2V0JDEodGhpcywga2V5LCB0cnVlKTtcbiAgfSxcbiAgZ2V0IHNpemUoKSB7XG4gICAgcmV0dXJuIHNpemUodGhpcywgdHJ1ZSk7XG4gIH0sXG4gIGhhcyhrZXkpIHtcbiAgICByZXR1cm4gaGFzJDEuY2FsbCh0aGlzLCBrZXksIHRydWUpO1xuICB9LFxuICBhZGQ6IGNyZWF0ZVJlYWRvbmx5TWV0aG9kKFwiYWRkXCIpLFxuICBzZXQ6IGNyZWF0ZVJlYWRvbmx5TWV0aG9kKFwic2V0XCIpLFxuICBkZWxldGU6IGNyZWF0ZVJlYWRvbmx5TWV0aG9kKFwiZGVsZXRlXCIpLFxuICBjbGVhcjogY3JlYXRlUmVhZG9ubHlNZXRob2QoXCJjbGVhclwiKSxcbiAgZm9yRWFjaDogY3JlYXRlRm9yRWFjaCh0cnVlLCBmYWxzZSlcbn07XG52YXIgc2hhbGxvd1JlYWRvbmx5SW5zdHJ1bWVudGF0aW9ucyA9IHtcbiAgZ2V0KGtleSkge1xuICAgIHJldHVybiBnZXQkMSh0aGlzLCBrZXksIHRydWUsIHRydWUpO1xuICB9LFxuICBnZXQgc2l6ZSgpIHtcbiAgICByZXR1cm4gc2l6ZSh0aGlzLCB0cnVlKTtcbiAgfSxcbiAgaGFzKGtleSkge1xuICAgIHJldHVybiBoYXMkMS5jYWxsKHRoaXMsIGtleSwgdHJ1ZSk7XG4gIH0sXG4gIGFkZDogY3JlYXRlUmVhZG9ubHlNZXRob2QoXCJhZGRcIiksXG4gIHNldDogY3JlYXRlUmVhZG9ubHlNZXRob2QoXCJzZXRcIiksXG4gIGRlbGV0ZTogY3JlYXRlUmVhZG9ubHlNZXRob2QoXCJkZWxldGVcIiksXG4gIGNsZWFyOiBjcmVhdGVSZWFkb25seU1ldGhvZChcImNsZWFyXCIpLFxuICBmb3JFYWNoOiBjcmVhdGVGb3JFYWNoKHRydWUsIHRydWUpXG59O1xudmFyIGl0ZXJhdG9yTWV0aG9kcyA9IFtcImtleXNcIiwgXCJ2YWx1ZXNcIiwgXCJlbnRyaWVzXCIsIFN5bWJvbC5pdGVyYXRvcl07XG5pdGVyYXRvck1ldGhvZHMuZm9yRWFjaCgobWV0aG9kKSA9PiB7XG4gIG11dGFibGVJbnN0cnVtZW50YXRpb25zW21ldGhvZF0gPSBjcmVhdGVJdGVyYWJsZU1ldGhvZChtZXRob2QsIGZhbHNlLCBmYWxzZSk7XG4gIHJlYWRvbmx5SW5zdHJ1bWVudGF0aW9uc1ttZXRob2RdID0gY3JlYXRlSXRlcmFibGVNZXRob2QobWV0aG9kLCB0cnVlLCBmYWxzZSk7XG4gIHNoYWxsb3dJbnN0cnVtZW50YXRpb25zW21ldGhvZF0gPSBjcmVhdGVJdGVyYWJsZU1ldGhvZChtZXRob2QsIGZhbHNlLCB0cnVlKTtcbiAgc2hhbGxvd1JlYWRvbmx5SW5zdHJ1bWVudGF0aW9uc1ttZXRob2RdID0gY3JlYXRlSXRlcmFibGVNZXRob2QobWV0aG9kLCB0cnVlLCB0cnVlKTtcbn0pO1xuZnVuY3Rpb24gY3JlYXRlSW5zdHJ1bWVudGF0aW9uR2V0dGVyKGlzUmVhZG9ubHksIHNoYWxsb3cpIHtcbiAgY29uc3QgaW5zdHJ1bWVudGF0aW9ucyA9IHNoYWxsb3cgPyBpc1JlYWRvbmx5ID8gc2hhbGxvd1JlYWRvbmx5SW5zdHJ1bWVudGF0aW9ucyA6IHNoYWxsb3dJbnN0cnVtZW50YXRpb25zIDogaXNSZWFkb25seSA/IHJlYWRvbmx5SW5zdHJ1bWVudGF0aW9ucyA6IG11dGFibGVJbnN0cnVtZW50YXRpb25zO1xuICByZXR1cm4gKHRhcmdldCwga2V5LCByZWNlaXZlcikgPT4ge1xuICAgIGlmIChrZXkgPT09IFwiX192X2lzUmVhY3RpdmVcIikge1xuICAgICAgcmV0dXJuICFpc1JlYWRvbmx5O1xuICAgIH0gZWxzZSBpZiAoa2V5ID09PSBcIl9fdl9pc1JlYWRvbmx5XCIpIHtcbiAgICAgIHJldHVybiBpc1JlYWRvbmx5O1xuICAgIH0gZWxzZSBpZiAoa2V5ID09PSBcIl9fdl9yYXdcIikge1xuICAgICAgcmV0dXJuIHRhcmdldDtcbiAgICB9XG4gICAgcmV0dXJuIFJlZmxlY3QuZ2V0KGhhc093bihpbnN0cnVtZW50YXRpb25zLCBrZXkpICYmIGtleSBpbiB0YXJnZXQgPyBpbnN0cnVtZW50YXRpb25zIDogdGFyZ2V0LCBrZXksIHJlY2VpdmVyKTtcbiAgfTtcbn1cbnZhciBtdXRhYmxlQ29sbGVjdGlvbkhhbmRsZXJzID0ge1xuICBnZXQ6IGNyZWF0ZUluc3RydW1lbnRhdGlvbkdldHRlcihmYWxzZSwgZmFsc2UpXG59O1xudmFyIHNoYWxsb3dDb2xsZWN0aW9uSGFuZGxlcnMgPSB7XG4gIGdldDogY3JlYXRlSW5zdHJ1bWVudGF0aW9uR2V0dGVyKGZhbHNlLCB0cnVlKVxufTtcbnZhciByZWFkb25seUNvbGxlY3Rpb25IYW5kbGVycyA9IHtcbiAgZ2V0OiBjcmVhdGVJbnN0cnVtZW50YXRpb25HZXR0ZXIodHJ1ZSwgZmFsc2UpXG59O1xudmFyIHNoYWxsb3dSZWFkb25seUNvbGxlY3Rpb25IYW5kbGVycyA9IHtcbiAgZ2V0OiBjcmVhdGVJbnN0cnVtZW50YXRpb25HZXR0ZXIodHJ1ZSwgdHJ1ZSlcbn07XG5mdW5jdGlvbiBjaGVja0lkZW50aXR5S2V5cyh0YXJnZXQsIGhhczIsIGtleSkge1xuICBjb25zdCByYXdLZXkgPSB0b1JhdyhrZXkpO1xuICBpZiAocmF3S2V5ICE9PSBrZXkgJiYgaGFzMi5jYWxsKHRhcmdldCwgcmF3S2V5KSkge1xuICAgIGNvbnN0IHR5cGUgPSB0b1Jhd1R5cGUodGFyZ2V0KTtcbiAgICBjb25zb2xlLndhcm4oYFJlYWN0aXZlICR7dHlwZX0gY29udGFpbnMgYm90aCB0aGUgcmF3IGFuZCByZWFjdGl2ZSB2ZXJzaW9ucyBvZiB0aGUgc2FtZSBvYmplY3Qke3R5cGUgPT09IGBNYXBgID8gYCBhcyBrZXlzYCA6IGBgfSwgd2hpY2ggY2FuIGxlYWQgdG8gaW5jb25zaXN0ZW5jaWVzLiBBdm9pZCBkaWZmZXJlbnRpYXRpbmcgYmV0d2VlbiB0aGUgcmF3IGFuZCByZWFjdGl2ZSB2ZXJzaW9ucyBvZiBhbiBvYmplY3QgYW5kIG9ubHkgdXNlIHRoZSByZWFjdGl2ZSB2ZXJzaW9uIGlmIHBvc3NpYmxlLmApO1xuICB9XG59XG52YXIgcmVhY3RpdmVNYXAgPSBuZXcgV2Vha01hcCgpO1xudmFyIHNoYWxsb3dSZWFjdGl2ZU1hcCA9IG5ldyBXZWFrTWFwKCk7XG52YXIgcmVhZG9ubHlNYXAgPSBuZXcgV2Vha01hcCgpO1xudmFyIHNoYWxsb3dSZWFkb25seU1hcCA9IG5ldyBXZWFrTWFwKCk7XG5mdW5jdGlvbiB0YXJnZXRUeXBlTWFwKHJhd1R5cGUpIHtcbiAgc3dpdGNoIChyYXdUeXBlKSB7XG4gICAgY2FzZSBcIk9iamVjdFwiOlxuICAgIGNhc2UgXCJBcnJheVwiOlxuICAgICAgcmV0dXJuIDE7XG4gICAgY2FzZSBcIk1hcFwiOlxuICAgIGNhc2UgXCJTZXRcIjpcbiAgICBjYXNlIFwiV2Vha01hcFwiOlxuICAgIGNhc2UgXCJXZWFrU2V0XCI6XG4gICAgICByZXR1cm4gMjtcbiAgICBkZWZhdWx0OlxuICAgICAgcmV0dXJuIDA7XG4gIH1cbn1cbmZ1bmN0aW9uIGdldFRhcmdldFR5cGUodmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlW1wiX192X3NraXBcIl0gfHwgIU9iamVjdC5pc0V4dGVuc2libGUodmFsdWUpID8gMCA6IHRhcmdldFR5cGVNYXAodG9SYXdUeXBlKHZhbHVlKSk7XG59XG5mdW5jdGlvbiByZWFjdGl2ZTIodGFyZ2V0KSB7XG4gIGlmICh0YXJnZXQgJiYgdGFyZ2V0W1wiX192X2lzUmVhZG9ubHlcIl0pIHtcbiAgICByZXR1cm4gdGFyZ2V0O1xuICB9XG4gIHJldHVybiBjcmVhdGVSZWFjdGl2ZU9iamVjdCh0YXJnZXQsIGZhbHNlLCBtdXRhYmxlSGFuZGxlcnMsIG11dGFibGVDb2xsZWN0aW9uSGFuZGxlcnMsIHJlYWN0aXZlTWFwKTtcbn1cbmZ1bmN0aW9uIHJlYWRvbmx5KHRhcmdldCkge1xuICByZXR1cm4gY3JlYXRlUmVhY3RpdmVPYmplY3QodGFyZ2V0LCB0cnVlLCByZWFkb25seUhhbmRsZXJzLCByZWFkb25seUNvbGxlY3Rpb25IYW5kbGVycywgcmVhZG9ubHlNYXApO1xufVxuZnVuY3Rpb24gY3JlYXRlUmVhY3RpdmVPYmplY3QodGFyZ2V0LCBpc1JlYWRvbmx5LCBiYXNlSGFuZGxlcnMsIGNvbGxlY3Rpb25IYW5kbGVycywgcHJveHlNYXApIHtcbiAgaWYgKCFpc09iamVjdCh0YXJnZXQpKSB7XG4gICAgaWYgKHRydWUpIHtcbiAgICAgIGNvbnNvbGUud2FybihgdmFsdWUgY2Fubm90IGJlIG1hZGUgcmVhY3RpdmU6ICR7U3RyaW5nKHRhcmdldCl9YCk7XG4gICAgfVxuICAgIHJldHVybiB0YXJnZXQ7XG4gIH1cbiAgaWYgKHRhcmdldFtcIl9fdl9yYXdcIl0gJiYgIShpc1JlYWRvbmx5ICYmIHRhcmdldFtcIl9fdl9pc1JlYWN0aXZlXCJdKSkge1xuICAgIHJldHVybiB0YXJnZXQ7XG4gIH1cbiAgY29uc3QgZXhpc3RpbmdQcm94eSA9IHByb3h5TWFwLmdldCh0YXJnZXQpO1xuICBpZiAoZXhpc3RpbmdQcm94eSkge1xuICAgIHJldHVybiBleGlzdGluZ1Byb3h5O1xuICB9XG4gIGNvbnN0IHRhcmdldFR5cGUgPSBnZXRUYXJnZXRUeXBlKHRhcmdldCk7XG4gIGlmICh0YXJnZXRUeXBlID09PSAwKSB7XG4gICAgcmV0dXJuIHRhcmdldDtcbiAgfVxuICBjb25zdCBwcm94eSA9IG5ldyBQcm94eSh0YXJnZXQsIHRhcmdldFR5cGUgPT09IDIgPyBjb2xsZWN0aW9uSGFuZGxlcnMgOiBiYXNlSGFuZGxlcnMpO1xuICBwcm94eU1hcC5zZXQodGFyZ2V0LCBwcm94eSk7XG4gIHJldHVybiBwcm94eTtcbn1cbmZ1bmN0aW9uIHRvUmF3KG9ic2VydmVkKSB7XG4gIHJldHVybiBvYnNlcnZlZCAmJiB0b1JhdyhvYnNlcnZlZFtcIl9fdl9yYXdcIl0pIHx8IG9ic2VydmVkO1xufVxuZnVuY3Rpb24gaXNSZWYocikge1xuICByZXR1cm4gQm9vbGVhbihyICYmIHIuX192X2lzUmVmID09PSB0cnVlKTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL21hZ2ljcy8kbmV4dFRpY2suanNcbm1hZ2ljKFwibmV4dFRpY2tcIiwgKCkgPT4gbmV4dFRpY2spO1xuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvbWFnaWNzLyRkaXNwYXRjaC5qc1xubWFnaWMoXCJkaXNwYXRjaFwiLCAoZWwpID0+IGRpc3BhdGNoLmJpbmQoZGlzcGF0Y2gsIGVsKSk7XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9tYWdpY3MvJHdhdGNoLmpzXG5tYWdpYyhcIndhdGNoXCIsIChlbCwge2V2YWx1YXRlTGF0ZXI6IGV2YWx1YXRlTGF0ZXIyLCBlZmZlY3Q6IGVmZmVjdDN9KSA9PiAoa2V5LCBjYWxsYmFjaykgPT4ge1xuICBsZXQgZXZhbHVhdGUyID0gZXZhbHVhdGVMYXRlcjIoa2V5KTtcbiAgbGV0IGZpcnN0VGltZSA9IHRydWU7XG4gIGxldCBvbGRWYWx1ZTtcbiAgbGV0IGVmZmVjdFJlZmVyZW5jZSA9IGVmZmVjdDMoKCkgPT4gZXZhbHVhdGUyKCh2YWx1ZSkgPT4ge1xuICAgIEpTT04uc3RyaW5naWZ5KHZhbHVlKTtcbiAgICBpZiAoIWZpcnN0VGltZSkge1xuICAgICAgcXVldWVNaWNyb3Rhc2soKCkgPT4ge1xuICAgICAgICBjYWxsYmFjayh2YWx1ZSwgb2xkVmFsdWUpO1xuICAgICAgICBvbGRWYWx1ZSA9IHZhbHVlO1xuICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIG9sZFZhbHVlID0gdmFsdWU7XG4gICAgfVxuICAgIGZpcnN0VGltZSA9IGZhbHNlO1xuICB9KSk7XG4gIGVsLl94X2VmZmVjdHMuZGVsZXRlKGVmZmVjdFJlZmVyZW5jZSk7XG59KTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL21hZ2ljcy8kc3RvcmUuanNcbm1hZ2ljKFwic3RvcmVcIiwgZ2V0U3RvcmVzKTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL21hZ2ljcy8kZGF0YS5qc1xubWFnaWMoXCJkYXRhXCIsIChlbCkgPT4gc2NvcGUoZWwpKTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL21hZ2ljcy8kcm9vdC5qc1xubWFnaWMoXCJyb290XCIsIChlbCkgPT4gY2xvc2VzdFJvb3QoZWwpKTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL21hZ2ljcy8kcmVmcy5qc1xubWFnaWMoXCJyZWZzXCIsIChlbCkgPT4ge1xuICBpZiAoZWwuX3hfcmVmc19wcm94eSlcbiAgICByZXR1cm4gZWwuX3hfcmVmc19wcm94eTtcbiAgZWwuX3hfcmVmc19wcm94eSA9IG1lcmdlUHJveGllcyhnZXRBcnJheU9mUmVmT2JqZWN0KGVsKSk7XG4gIHJldHVybiBlbC5feF9yZWZzX3Byb3h5O1xufSk7XG5mdW5jdGlvbiBnZXRBcnJheU9mUmVmT2JqZWN0KGVsKSB7XG4gIGxldCByZWZPYmplY3RzID0gW107XG4gIGxldCBjdXJyZW50RWwgPSBlbDtcbiAgd2hpbGUgKGN1cnJlbnRFbCkge1xuICAgIGlmIChjdXJyZW50RWwuX3hfcmVmcylcbiAgICAgIHJlZk9iamVjdHMucHVzaChjdXJyZW50RWwuX3hfcmVmcyk7XG4gICAgY3VycmVudEVsID0gY3VycmVudEVsLnBhcmVudE5vZGU7XG4gIH1cbiAgcmV0dXJuIHJlZk9iamVjdHM7XG59XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9pZHMuanNcbnZhciBnbG9iYWxJZE1lbW8gPSB7fTtcbmZ1bmN0aW9uIGZpbmRBbmRJbmNyZW1lbnRJZChuYW1lKSB7XG4gIGlmICghZ2xvYmFsSWRNZW1vW25hbWVdKVxuICAgIGdsb2JhbElkTWVtb1tuYW1lXSA9IDA7XG4gIHJldHVybiArK2dsb2JhbElkTWVtb1tuYW1lXTtcbn1cbmZ1bmN0aW9uIGNsb3Nlc3RJZFJvb3QoZWwsIG5hbWUpIHtcbiAgcmV0dXJuIGZpbmRDbG9zZXN0KGVsLCAoZWxlbWVudCkgPT4ge1xuICAgIGlmIChlbGVtZW50Ll94X2lkcyAmJiBlbGVtZW50Ll94X2lkc1tuYW1lXSlcbiAgICAgIHJldHVybiB0cnVlO1xuICB9KTtcbn1cbmZ1bmN0aW9uIHNldElkUm9vdChlbCwgbmFtZSkge1xuICBpZiAoIWVsLl94X2lkcylcbiAgICBlbC5feF9pZHMgPSB7fTtcbiAgaWYgKCFlbC5feF9pZHNbbmFtZV0pXG4gICAgZWwuX3hfaWRzW25hbWVdID0gZmluZEFuZEluY3JlbWVudElkKG5hbWUpO1xufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvbWFnaWNzLyRpZC5qc1xubWFnaWMoXCJpZFwiLCAoZWwpID0+IChuYW1lLCBrZXkgPSBudWxsKSA9PiB7XG4gIGxldCByb290ID0gY2xvc2VzdElkUm9vdChlbCwgbmFtZSk7XG4gIGxldCBpZCA9IHJvb3QgPyByb290Ll94X2lkc1tuYW1lXSA6IGZpbmRBbmRJbmNyZW1lbnRJZChuYW1lKTtcbiAgcmV0dXJuIGtleSA/IGAke25hbWV9LSR7aWR9LSR7a2V5fWAgOiBgJHtuYW1lfS0ke2lkfWA7XG59KTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL21hZ2ljcy8kZWwuanNcbm1hZ2ljKFwiZWxcIiwgKGVsKSA9PiBlbCk7XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9tYWdpY3MvaW5kZXguanNcbndhcm5NaXNzaW5nUGx1Z2luTWFnaWMoXCJGb2N1c1wiLCBcImZvY3VzXCIsIFwiZm9jdXNcIik7XG53YXJuTWlzc2luZ1BsdWdpbk1hZ2ljKFwiUGVyc2lzdFwiLCBcInBlcnNpc3RcIiwgXCJwZXJzaXN0XCIpO1xuZnVuY3Rpb24gd2Fybk1pc3NpbmdQbHVnaW5NYWdpYyhuYW1lLCBtYWdpY05hbWUsIHNsdWcpIHtcbiAgbWFnaWMobWFnaWNOYW1lLCAoZWwpID0+IHdhcm4oYFlvdSBjYW4ndCB1c2UgWyQke2RpcmVjdGl2ZU5hbWV9XSB3aXRob3V0IGZpcnN0IGluc3RhbGxpbmcgdGhlIFwiJHtuYW1lfVwiIHBsdWdpbiBoZXJlOiBodHRwczovL2FscGluZWpzLmRldi9wbHVnaW5zLyR7c2x1Z31gLCBlbCkpO1xufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvZW50YW5nbGUuanNcbmZ1bmN0aW9uIGVudGFuZ2xlKHtnZXQ6IG91dGVyR2V0LCBzZXQ6IG91dGVyU2V0fSwge2dldDogaW5uZXJHZXQsIHNldDogaW5uZXJTZXR9KSB7XG4gIGxldCBmaXJzdFJ1biA9IHRydWU7XG4gIGxldCBvdXRlckhhc2gsIGlubmVySGFzaCwgb3V0ZXJIYXNoTGF0ZXN0LCBpbm5lckhhc2hMYXRlc3Q7XG4gIGxldCByZWZlcmVuY2UgPSBlZmZlY3QoKCkgPT4ge1xuICAgIGxldCBvdXRlciwgaW5uZXI7XG4gICAgaWYgKGZpcnN0UnVuKSB7XG4gICAgICBvdXRlciA9IG91dGVyR2V0KCk7XG4gICAgICBpbm5lclNldChvdXRlcik7XG4gICAgICBpbm5lciA9IGlubmVyR2V0KCk7XG4gICAgICBmaXJzdFJ1biA9IGZhbHNlO1xuICAgIH0gZWxzZSB7XG4gICAgICBvdXRlciA9IG91dGVyR2V0KCk7XG4gICAgICBpbm5lciA9IGlubmVyR2V0KCk7XG4gICAgICBvdXRlckhhc2hMYXRlc3QgPSBKU09OLnN0cmluZ2lmeShvdXRlcik7XG4gICAgICBpbm5lckhhc2hMYXRlc3QgPSBKU09OLnN0cmluZ2lmeShpbm5lcik7XG4gICAgICBpZiAob3V0ZXJIYXNoTGF0ZXN0ICE9PSBvdXRlckhhc2gpIHtcbiAgICAgICAgaW5uZXIgPSBpbm5lckdldCgpO1xuICAgICAgICBpbm5lclNldChvdXRlcik7XG4gICAgICAgIGlubmVyID0gb3V0ZXI7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBvdXRlclNldChpbm5lcik7XG4gICAgICAgIG91dGVyID0gaW5uZXI7XG4gICAgICB9XG4gICAgfVxuICAgIG91dGVySGFzaCA9IEpTT04uc3RyaW5naWZ5KG91dGVyKTtcbiAgICBpbm5lckhhc2ggPSBKU09OLnN0cmluZ2lmeShpbm5lcik7XG4gIH0pO1xuICByZXR1cm4gKCkgPT4ge1xuICAgIHJlbGVhc2UocmVmZXJlbmNlKTtcbiAgfTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1tb2RlbGFibGUuanNcbmRpcmVjdGl2ZShcIm1vZGVsYWJsZVwiLCAoZWwsIHtleHByZXNzaW9ufSwge2VmZmVjdDogZWZmZWN0MywgZXZhbHVhdGVMYXRlcjogZXZhbHVhdGVMYXRlcjIsIGNsZWFudXA6IGNsZWFudXAyfSkgPT4ge1xuICBsZXQgZnVuYyA9IGV2YWx1YXRlTGF0ZXIyKGV4cHJlc3Npb24pO1xuICBsZXQgaW5uZXJHZXQgPSAoKSA9PiB7XG4gICAgbGV0IHJlc3VsdDtcbiAgICBmdW5jKChpKSA9PiByZXN1bHQgPSBpKTtcbiAgICByZXR1cm4gcmVzdWx0O1xuICB9O1xuICBsZXQgZXZhbHVhdGVJbm5lclNldCA9IGV2YWx1YXRlTGF0ZXIyKGAke2V4cHJlc3Npb259ID0gX19wbGFjZWhvbGRlcmApO1xuICBsZXQgaW5uZXJTZXQgPSAodmFsKSA9PiBldmFsdWF0ZUlubmVyU2V0KCgpID0+IHtcbiAgfSwge3Njb3BlOiB7X19wbGFjZWhvbGRlcjogdmFsfX0pO1xuICBsZXQgaW5pdGlhbFZhbHVlID0gaW5uZXJHZXQoKTtcbiAgaW5uZXJTZXQoaW5pdGlhbFZhbHVlKTtcbiAgcXVldWVNaWNyb3Rhc2soKCkgPT4ge1xuICAgIGlmICghZWwuX3hfbW9kZWwpXG4gICAgICByZXR1cm47XG4gICAgZWwuX3hfcmVtb3ZlTW9kZWxMaXN0ZW5lcnNbXCJkZWZhdWx0XCJdKCk7XG4gICAgbGV0IG91dGVyR2V0ID0gZWwuX3hfbW9kZWwuZ2V0O1xuICAgIGxldCBvdXRlclNldCA9IGVsLl94X21vZGVsLnNldDtcbiAgICBsZXQgcmVsZWFzZUVudGFuZ2xlbWVudCA9IGVudGFuZ2xlKHtcbiAgICAgIGdldCgpIHtcbiAgICAgICAgcmV0dXJuIG91dGVyR2V0KCk7XG4gICAgICB9LFxuICAgICAgc2V0KHZhbHVlKSB7XG4gICAgICAgIG91dGVyU2V0KHZhbHVlKTtcbiAgICAgIH1cbiAgICB9LCB7XG4gICAgICBnZXQoKSB7XG4gICAgICAgIHJldHVybiBpbm5lckdldCgpO1xuICAgICAgfSxcbiAgICAgIHNldCh2YWx1ZSkge1xuICAgICAgICBpbm5lclNldCh2YWx1ZSk7XG4gICAgICB9XG4gICAgfSk7XG4gICAgY2xlYW51cDIocmVsZWFzZUVudGFuZ2xlbWVudCk7XG4gIH0pO1xufSk7XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9kaXJlY3RpdmVzL3gtdGVsZXBvcnQuanNcbnZhciB0ZWxlcG9ydENvbnRhaW5lckR1cmluZ0Nsb25lID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcImRpdlwiKTtcbmRpcmVjdGl2ZShcInRlbGVwb3J0XCIsIChlbCwge21vZGlmaWVycywgZXhwcmVzc2lvbn0sIHtjbGVhbnVwOiBjbGVhbnVwMn0pID0+IHtcbiAgaWYgKGVsLnRhZ05hbWUudG9Mb3dlckNhc2UoKSAhPT0gXCJ0ZW1wbGF0ZVwiKVxuICAgIHdhcm4oXCJ4LXRlbGVwb3J0IGNhbiBvbmx5IGJlIHVzZWQgb24gYSA8dGVtcGxhdGU+IHRhZ1wiLCBlbCk7XG4gIGxldCB0YXJnZXQgPSBza2lwRHVyaW5nQ2xvbmUoKCkgPT4ge1xuICAgIHJldHVybiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGV4cHJlc3Npb24pO1xuICB9LCAoKSA9PiB7XG4gICAgcmV0dXJuIHRlbGVwb3J0Q29udGFpbmVyRHVyaW5nQ2xvbmU7XG4gIH0pKCk7XG4gIGlmICghdGFyZ2V0KVxuICAgIHdhcm4oYENhbm5vdCBmaW5kIHgtdGVsZXBvcnQgZWxlbWVudCBmb3Igc2VsZWN0b3I6IFwiJHtleHByZXNzaW9ufVwiYCk7XG4gIGxldCBjbG9uZTIgPSBlbC5jb250ZW50LmNsb25lTm9kZSh0cnVlKS5maXJzdEVsZW1lbnRDaGlsZDtcbiAgZWwuX3hfdGVsZXBvcnQgPSBjbG9uZTI7XG4gIGNsb25lMi5feF90ZWxlcG9ydEJhY2sgPSBlbDtcbiAgaWYgKGVsLl94X2ZvcndhcmRFdmVudHMpIHtcbiAgICBlbC5feF9mb3J3YXJkRXZlbnRzLmZvckVhY2goKGV2ZW50TmFtZSkgPT4ge1xuICAgICAgY2xvbmUyLmFkZEV2ZW50TGlzdGVuZXIoZXZlbnROYW1lLCAoZSkgPT4ge1xuICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICBlbC5kaXNwYXRjaEV2ZW50KG5ldyBlLmNvbnN0cnVjdG9yKGUudHlwZSwgZSkpO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbiAgYWRkU2NvcGVUb05vZGUoY2xvbmUyLCB7fSwgZWwpO1xuICBtdXRhdGVEb20oKCkgPT4ge1xuICAgIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJwcmVwZW5kXCIpKSB7XG4gICAgICB0YXJnZXQucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoY2xvbmUyLCB0YXJnZXQpO1xuICAgIH0gZWxzZSBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiYXBwZW5kXCIpKSB7XG4gICAgICB0YXJnZXQucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoY2xvbmUyLCB0YXJnZXQubmV4dFNpYmxpbmcpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0YXJnZXQuYXBwZW5kQ2hpbGQoY2xvbmUyKTtcbiAgICB9XG4gICAgaW5pdFRyZWUoY2xvbmUyKTtcbiAgICBjbG9uZTIuX3hfaWdub3JlID0gdHJ1ZTtcbiAgfSk7XG4gIGNsZWFudXAyKCgpID0+IGNsb25lMi5yZW1vdmUoKSk7XG59KTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1pZ25vcmUuanNcbnZhciBoYW5kbGVyID0gKCkgPT4ge1xufTtcbmhhbmRsZXIuaW5saW5lID0gKGVsLCB7bW9kaWZpZXJzfSwge2NsZWFudXA6IGNsZWFudXAyfSkgPT4ge1xuICBtb2RpZmllcnMuaW5jbHVkZXMoXCJzZWxmXCIpID8gZWwuX3hfaWdub3JlU2VsZiA9IHRydWUgOiBlbC5feF9pZ25vcmUgPSB0cnVlO1xuICBjbGVhbnVwMigoKSA9PiB7XG4gICAgbW9kaWZpZXJzLmluY2x1ZGVzKFwic2VsZlwiKSA/IGRlbGV0ZSBlbC5feF9pZ25vcmVTZWxmIDogZGVsZXRlIGVsLl94X2lnbm9yZTtcbiAgfSk7XG59O1xuZGlyZWN0aXZlKFwiaWdub3JlXCIsIGhhbmRsZXIpO1xuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvZGlyZWN0aXZlcy94LWVmZmVjdC5qc1xuZGlyZWN0aXZlKFwiZWZmZWN0XCIsIChlbCwge2V4cHJlc3Npb259LCB7ZWZmZWN0OiBlZmZlY3QzfSkgPT4gZWZmZWN0MyhldmFsdWF0ZUxhdGVyKGVsLCBleHByZXNzaW9uKSkpO1xuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvdXRpbHMvb24uanNcbmZ1bmN0aW9uIG9uKGVsLCBldmVudCwgbW9kaWZpZXJzLCBjYWxsYmFjaykge1xuICBsZXQgbGlzdGVuZXJUYXJnZXQgPSBlbDtcbiAgbGV0IGhhbmRsZXIzID0gKGUpID0+IGNhbGxiYWNrKGUpO1xuICBsZXQgb3B0aW9ucyA9IHt9O1xuICBsZXQgd3JhcEhhbmRsZXIgPSAoY2FsbGJhY2syLCB3cmFwcGVyKSA9PiAoZSkgPT4gd3JhcHBlcihjYWxsYmFjazIsIGUpO1xuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiZG90XCIpKVxuICAgIGV2ZW50ID0gZG90U3ludGF4KGV2ZW50KTtcbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImNhbWVsXCIpKVxuICAgIGV2ZW50ID0gY2FtZWxDYXNlMihldmVudCk7XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJwYXNzaXZlXCIpKVxuICAgIG9wdGlvbnMucGFzc2l2ZSA9IHRydWU7XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJjYXB0dXJlXCIpKVxuICAgIG9wdGlvbnMuY2FwdHVyZSA9IHRydWU7XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJ3aW5kb3dcIikpXG4gICAgbGlzdGVuZXJUYXJnZXQgPSB3aW5kb3c7XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJkb2N1bWVudFwiKSlcbiAgICBsaXN0ZW5lclRhcmdldCA9IGRvY3VtZW50O1xuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiZGVib3VuY2VcIikpIHtcbiAgICBsZXQgbmV4dE1vZGlmaWVyID0gbW9kaWZpZXJzW21vZGlmaWVycy5pbmRleE9mKFwiZGVib3VuY2VcIikgKyAxXSB8fCBcImludmFsaWQtd2FpdFwiO1xuICAgIGxldCB3YWl0ID0gaXNOdW1lcmljKG5leHRNb2RpZmllci5zcGxpdChcIm1zXCIpWzBdKSA/IE51bWJlcihuZXh0TW9kaWZpZXIuc3BsaXQoXCJtc1wiKVswXSkgOiAyNTA7XG4gICAgaGFuZGxlcjMgPSBkZWJvdW5jZShoYW5kbGVyMywgd2FpdCk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInRocm90dGxlXCIpKSB7XG4gICAgbGV0IG5leHRNb2RpZmllciA9IG1vZGlmaWVyc1ttb2RpZmllcnMuaW5kZXhPZihcInRocm90dGxlXCIpICsgMV0gfHwgXCJpbnZhbGlkLXdhaXRcIjtcbiAgICBsZXQgd2FpdCA9IGlzTnVtZXJpYyhuZXh0TW9kaWZpZXIuc3BsaXQoXCJtc1wiKVswXSkgPyBOdW1iZXIobmV4dE1vZGlmaWVyLnNwbGl0KFwibXNcIilbMF0pIDogMjUwO1xuICAgIGhhbmRsZXIzID0gdGhyb3R0bGUoaGFuZGxlcjMsIHdhaXQpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJwcmV2ZW50XCIpKVxuICAgIGhhbmRsZXIzID0gd3JhcEhhbmRsZXIoaGFuZGxlcjMsIChuZXh0LCBlKSA9PiB7XG4gICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBuZXh0KGUpO1xuICAgIH0pO1xuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwic3RvcFwiKSlcbiAgICBoYW5kbGVyMyA9IHdyYXBIYW5kbGVyKGhhbmRsZXIzLCAobmV4dCwgZSkgPT4ge1xuICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIG5leHQoZSk7XG4gICAgfSk7XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJzZWxmXCIpKVxuICAgIGhhbmRsZXIzID0gd3JhcEhhbmRsZXIoaGFuZGxlcjMsIChuZXh0LCBlKSA9PiB7XG4gICAgICBlLnRhcmdldCA9PT0gZWwgJiYgbmV4dChlKTtcbiAgICB9KTtcbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImF3YXlcIikgfHwgbW9kaWZpZXJzLmluY2x1ZGVzKFwib3V0c2lkZVwiKSkge1xuICAgIGxpc3RlbmVyVGFyZ2V0ID0gZG9jdW1lbnQ7XG4gICAgaGFuZGxlcjMgPSB3cmFwSGFuZGxlcihoYW5kbGVyMywgKG5leHQsIGUpID0+IHtcbiAgICAgIGlmIChlbC5jb250YWlucyhlLnRhcmdldCkpXG4gICAgICAgIHJldHVybjtcbiAgICAgIGlmIChlLnRhcmdldC5pc0Nvbm5lY3RlZCA9PT0gZmFsc2UpXG4gICAgICAgIHJldHVybjtcbiAgICAgIGlmIChlbC5vZmZzZXRXaWR0aCA8IDEgJiYgZWwub2Zmc2V0SGVpZ2h0IDwgMSlcbiAgICAgICAgcmV0dXJuO1xuICAgICAgaWYgKGVsLl94X2lzU2hvd24gPT09IGZhbHNlKVxuICAgICAgICByZXR1cm47XG4gICAgICBuZXh0KGUpO1xuICAgIH0pO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJvbmNlXCIpKSB7XG4gICAgaGFuZGxlcjMgPSB3cmFwSGFuZGxlcihoYW5kbGVyMywgKG5leHQsIGUpID0+IHtcbiAgICAgIG5leHQoZSk7XG4gICAgICBsaXN0ZW5lclRhcmdldC5yZW1vdmVFdmVudExpc3RlbmVyKGV2ZW50LCBoYW5kbGVyMywgb3B0aW9ucyk7XG4gICAgfSk7XG4gIH1cbiAgaGFuZGxlcjMgPSB3cmFwSGFuZGxlcihoYW5kbGVyMywgKG5leHQsIGUpID0+IHtcbiAgICBpZiAoaXNLZXlFdmVudChldmVudCkpIHtcbiAgICAgIGlmIChpc0xpc3RlbmluZ0ZvckFTcGVjaWZpY0tleVRoYXRIYXNudEJlZW5QcmVzc2VkKGUsIG1vZGlmaWVycykpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgIH1cbiAgICBuZXh0KGUpO1xuICB9KTtcbiAgbGlzdGVuZXJUYXJnZXQuYWRkRXZlbnRMaXN0ZW5lcihldmVudCwgaGFuZGxlcjMsIG9wdGlvbnMpO1xuICByZXR1cm4gKCkgPT4ge1xuICAgIGxpc3RlbmVyVGFyZ2V0LnJlbW92ZUV2ZW50TGlzdGVuZXIoZXZlbnQsIGhhbmRsZXIzLCBvcHRpb25zKTtcbiAgfTtcbn1cbmZ1bmN0aW9uIGRvdFN5bnRheChzdWJqZWN0KSB7XG4gIHJldHVybiBzdWJqZWN0LnJlcGxhY2UoLy0vZywgXCIuXCIpO1xufVxuZnVuY3Rpb24gY2FtZWxDYXNlMihzdWJqZWN0KSB7XG4gIHJldHVybiBzdWJqZWN0LnRvTG93ZXJDYXNlKCkucmVwbGFjZSgvLShcXHcpL2csIChtYXRjaCwgY2hhcikgPT4gY2hhci50b1VwcGVyQ2FzZSgpKTtcbn1cbmZ1bmN0aW9uIGlzTnVtZXJpYyhzdWJqZWN0KSB7XG4gIHJldHVybiAhQXJyYXkuaXNBcnJheShzdWJqZWN0KSAmJiAhaXNOYU4oc3ViamVjdCk7XG59XG5mdW5jdGlvbiBrZWJhYkNhc2UyKHN1YmplY3QpIHtcbiAgaWYgKFtcIiBcIiwgXCJfXCJdLmluY2x1ZGVzKHN1YmplY3QpKVxuICAgIHJldHVybiBzdWJqZWN0O1xuICByZXR1cm4gc3ViamVjdC5yZXBsYWNlKC8oW2Etel0pKFtBLVpdKS9nLCBcIiQxLSQyXCIpLnJlcGxhY2UoL1tfXFxzXS8sIFwiLVwiKS50b0xvd2VyQ2FzZSgpO1xufVxuZnVuY3Rpb24gaXNLZXlFdmVudChldmVudCkge1xuICByZXR1cm4gW1wia2V5ZG93blwiLCBcImtleXVwXCJdLmluY2x1ZGVzKGV2ZW50KTtcbn1cbmZ1bmN0aW9uIGlzTGlzdGVuaW5nRm9yQVNwZWNpZmljS2V5VGhhdEhhc250QmVlblByZXNzZWQoZSwgbW9kaWZpZXJzKSB7XG4gIGxldCBrZXlNb2RpZmllcnMgPSBtb2RpZmllcnMuZmlsdGVyKChpKSA9PiB7XG4gICAgcmV0dXJuICFbXCJ3aW5kb3dcIiwgXCJkb2N1bWVudFwiLCBcInByZXZlbnRcIiwgXCJzdG9wXCIsIFwib25jZVwiLCBcImNhcHR1cmVcIl0uaW5jbHVkZXMoaSk7XG4gIH0pO1xuICBpZiAoa2V5TW9kaWZpZXJzLmluY2x1ZGVzKFwiZGVib3VuY2VcIikpIHtcbiAgICBsZXQgZGVib3VuY2VJbmRleCA9IGtleU1vZGlmaWVycy5pbmRleE9mKFwiZGVib3VuY2VcIik7XG4gICAga2V5TW9kaWZpZXJzLnNwbGljZShkZWJvdW5jZUluZGV4LCBpc051bWVyaWMoKGtleU1vZGlmaWVyc1tkZWJvdW5jZUluZGV4ICsgMV0gfHwgXCJpbnZhbGlkLXdhaXRcIikuc3BsaXQoXCJtc1wiKVswXSkgPyAyIDogMSk7XG4gIH1cbiAgaWYgKGtleU1vZGlmaWVycy5pbmNsdWRlcyhcInRocm90dGxlXCIpKSB7XG4gICAgbGV0IGRlYm91bmNlSW5kZXggPSBrZXlNb2RpZmllcnMuaW5kZXhPZihcInRocm90dGxlXCIpO1xuICAgIGtleU1vZGlmaWVycy5zcGxpY2UoZGVib3VuY2VJbmRleCwgaXNOdW1lcmljKChrZXlNb2RpZmllcnNbZGVib3VuY2VJbmRleCArIDFdIHx8IFwiaW52YWxpZC13YWl0XCIpLnNwbGl0KFwibXNcIilbMF0pID8gMiA6IDEpO1xuICB9XG4gIGlmIChrZXlNb2RpZmllcnMubGVuZ3RoID09PSAwKVxuICAgIHJldHVybiBmYWxzZTtcbiAgaWYgKGtleU1vZGlmaWVycy5sZW5ndGggPT09IDEgJiYga2V5VG9Nb2RpZmllcnMoZS5rZXkpLmluY2x1ZGVzKGtleU1vZGlmaWVyc1swXSkpXG4gICAgcmV0dXJuIGZhbHNlO1xuICBjb25zdCBzeXN0ZW1LZXlNb2RpZmllcnMgPSBbXCJjdHJsXCIsIFwic2hpZnRcIiwgXCJhbHRcIiwgXCJtZXRhXCIsIFwiY21kXCIsIFwic3VwZXJcIl07XG4gIGNvbnN0IHNlbGVjdGVkU3lzdGVtS2V5TW9kaWZpZXJzID0gc3lzdGVtS2V5TW9kaWZpZXJzLmZpbHRlcigobW9kaWZpZXIpID0+IGtleU1vZGlmaWVycy5pbmNsdWRlcyhtb2RpZmllcikpO1xuICBrZXlNb2RpZmllcnMgPSBrZXlNb2RpZmllcnMuZmlsdGVyKChpKSA9PiAhc2VsZWN0ZWRTeXN0ZW1LZXlNb2RpZmllcnMuaW5jbHVkZXMoaSkpO1xuICBpZiAoc2VsZWN0ZWRTeXN0ZW1LZXlNb2RpZmllcnMubGVuZ3RoID4gMCkge1xuICAgIGNvbnN0IGFjdGl2ZWx5UHJlc3NlZEtleU1vZGlmaWVycyA9IHNlbGVjdGVkU3lzdGVtS2V5TW9kaWZpZXJzLmZpbHRlcigobW9kaWZpZXIpID0+IHtcbiAgICAgIGlmIChtb2RpZmllciA9PT0gXCJjbWRcIiB8fCBtb2RpZmllciA9PT0gXCJzdXBlclwiKVxuICAgICAgICBtb2RpZmllciA9IFwibWV0YVwiO1xuICAgICAgcmV0dXJuIGVbYCR7bW9kaWZpZXJ9S2V5YF07XG4gICAgfSk7XG4gICAgaWYgKGFjdGl2ZWx5UHJlc3NlZEtleU1vZGlmaWVycy5sZW5ndGggPT09IHNlbGVjdGVkU3lzdGVtS2V5TW9kaWZpZXJzLmxlbmd0aCkge1xuICAgICAgaWYgKGtleVRvTW9kaWZpZXJzKGUua2V5KS5pbmNsdWRlcyhrZXlNb2RpZmllcnNbMF0pKVxuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG4gIHJldHVybiB0cnVlO1xufVxuZnVuY3Rpb24ga2V5VG9Nb2RpZmllcnMoa2V5KSB7XG4gIGlmICgha2V5KVxuICAgIHJldHVybiBbXTtcbiAga2V5ID0ga2ViYWJDYXNlMihrZXkpO1xuICBsZXQgbW9kaWZpZXJUb0tleU1hcCA9IHtcbiAgICBjdHJsOiBcImNvbnRyb2xcIixcbiAgICBzbGFzaDogXCIvXCIsXG4gICAgc3BhY2U6IFwiIFwiLFxuICAgIHNwYWNlYmFyOiBcIiBcIixcbiAgICBjbWQ6IFwibWV0YVwiLFxuICAgIGVzYzogXCJlc2NhcGVcIixcbiAgICB1cDogXCJhcnJvdy11cFwiLFxuICAgIGRvd246IFwiYXJyb3ctZG93blwiLFxuICAgIGxlZnQ6IFwiYXJyb3ctbGVmdFwiLFxuICAgIHJpZ2h0OiBcImFycm93LXJpZ2h0XCIsXG4gICAgcGVyaW9kOiBcIi5cIixcbiAgICBlcXVhbDogXCI9XCIsXG4gICAgbWludXM6IFwiLVwiLFxuICAgIHVuZGVyc2NvcmU6IFwiX1wiXG4gIH07XG4gIG1vZGlmaWVyVG9LZXlNYXBba2V5XSA9IGtleTtcbiAgcmV0dXJuIE9iamVjdC5rZXlzKG1vZGlmaWVyVG9LZXlNYXApLm1hcCgobW9kaWZpZXIpID0+IHtcbiAgICBpZiAobW9kaWZpZXJUb0tleU1hcFttb2RpZmllcl0gPT09IGtleSlcbiAgICAgIHJldHVybiBtb2RpZmllcjtcbiAgfSkuZmlsdGVyKChtb2RpZmllcikgPT4gbW9kaWZpZXIpO1xufVxuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvZGlyZWN0aXZlcy94LW1vZGVsLmpzXG5kaXJlY3RpdmUoXCJtb2RlbFwiLCAoZWwsIHttb2RpZmllcnMsIGV4cHJlc3Npb259LCB7ZWZmZWN0OiBlZmZlY3QzLCBjbGVhbnVwOiBjbGVhbnVwMn0pID0+IHtcbiAgbGV0IHNjb3BlVGFyZ2V0ID0gZWw7XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJwYXJlbnRcIikpIHtcbiAgICBzY29wZVRhcmdldCA9IGVsLnBhcmVudE5vZGU7XG4gIH1cbiAgbGV0IGV2YWx1YXRlR2V0ID0gZXZhbHVhdGVMYXRlcihzY29wZVRhcmdldCwgZXhwcmVzc2lvbik7XG4gIGxldCBldmFsdWF0ZVNldDtcbiAgaWYgKHR5cGVvZiBleHByZXNzaW9uID09PSBcInN0cmluZ1wiKSB7XG4gICAgZXZhbHVhdGVTZXQgPSBldmFsdWF0ZUxhdGVyKHNjb3BlVGFyZ2V0LCBgJHtleHByZXNzaW9ufSA9IF9fcGxhY2Vob2xkZXJgKTtcbiAgfSBlbHNlIGlmICh0eXBlb2YgZXhwcmVzc2lvbiA9PT0gXCJmdW5jdGlvblwiICYmIHR5cGVvZiBleHByZXNzaW9uKCkgPT09IFwic3RyaW5nXCIpIHtcbiAgICBldmFsdWF0ZVNldCA9IGV2YWx1YXRlTGF0ZXIoc2NvcGVUYXJnZXQsIGAke2V4cHJlc3Npb24oKX0gPSBfX3BsYWNlaG9sZGVyYCk7XG4gIH0gZWxzZSB7XG4gICAgZXZhbHVhdGVTZXQgPSAoKSA9PiB7XG4gICAgfTtcbiAgfVxuICBsZXQgZ2V0VmFsdWUgPSAoKSA9PiB7XG4gICAgbGV0IHJlc3VsdDtcbiAgICBldmFsdWF0ZUdldCgodmFsdWUpID0+IHJlc3VsdCA9IHZhbHVlKTtcbiAgICByZXR1cm4gaXNHZXR0ZXJTZXR0ZXIocmVzdWx0KSA/IHJlc3VsdC5nZXQoKSA6IHJlc3VsdDtcbiAgfTtcbiAgbGV0IHNldFZhbHVlID0gKHZhbHVlKSA9PiB7XG4gICAgbGV0IHJlc3VsdDtcbiAgICBldmFsdWF0ZUdldCgodmFsdWUyKSA9PiByZXN1bHQgPSB2YWx1ZTIpO1xuICAgIGlmIChpc0dldHRlclNldHRlcihyZXN1bHQpKSB7XG4gICAgICByZXN1bHQuc2V0KHZhbHVlKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZXZhbHVhdGVTZXQoKCkgPT4ge1xuICAgICAgfSwge1xuICAgICAgICBzY29wZToge19fcGxhY2Vob2xkZXI6IHZhbHVlfVxuICAgICAgfSk7XG4gICAgfVxuICB9O1xuICBpZiAodHlwZW9mIGV4cHJlc3Npb24gPT09IFwic3RyaW5nXCIgJiYgZWwudHlwZSA9PT0gXCJyYWRpb1wiKSB7XG4gICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgIGlmICghZWwuaGFzQXR0cmlidXRlKFwibmFtZVwiKSlcbiAgICAgICAgZWwuc2V0QXR0cmlidXRlKFwibmFtZVwiLCBleHByZXNzaW9uKTtcbiAgICB9KTtcbiAgfVxuICB2YXIgZXZlbnQgPSBlbC50YWdOYW1lLnRvTG93ZXJDYXNlKCkgPT09IFwic2VsZWN0XCIgfHwgW1wiY2hlY2tib3hcIiwgXCJyYWRpb1wiXS5pbmNsdWRlcyhlbC50eXBlKSB8fCBtb2RpZmllcnMuaW5jbHVkZXMoXCJsYXp5XCIpID8gXCJjaGFuZ2VcIiA6IFwiaW5wdXRcIjtcbiAgbGV0IHJlbW92ZUxpc3RlbmVyID0gaXNDbG9uaW5nID8gKCkgPT4ge1xuICB9IDogb24oZWwsIGV2ZW50LCBtb2RpZmllcnMsIChlKSA9PiB7XG4gICAgc2V0VmFsdWUoZ2V0SW5wdXRWYWx1ZShlbCwgbW9kaWZpZXJzLCBlLCBnZXRWYWx1ZSgpKSk7XG4gIH0pO1xuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiZmlsbFwiKSAmJiBbbnVsbCwgXCJcIl0uaW5jbHVkZXMoZ2V0VmFsdWUoKSkpIHtcbiAgICBlbC5kaXNwYXRjaEV2ZW50KG5ldyBFdmVudChldmVudCwge30pKTtcbiAgfVxuICBpZiAoIWVsLl94X3JlbW92ZU1vZGVsTGlzdGVuZXJzKVxuICAgIGVsLl94X3JlbW92ZU1vZGVsTGlzdGVuZXJzID0ge307XG4gIGVsLl94X3JlbW92ZU1vZGVsTGlzdGVuZXJzW1wiZGVmYXVsdFwiXSA9IHJlbW92ZUxpc3RlbmVyO1xuICBjbGVhbnVwMigoKSA9PiBlbC5feF9yZW1vdmVNb2RlbExpc3RlbmVyc1tcImRlZmF1bHRcIl0oKSk7XG4gIGlmIChlbC5mb3JtKSB7XG4gICAgbGV0IHJlbW92ZVJlc2V0TGlzdGVuZXIgPSBvbihlbC5mb3JtLCBcInJlc2V0XCIsIFtdLCAoZSkgPT4ge1xuICAgICAgbmV4dFRpY2soKCkgPT4gZWwuX3hfbW9kZWwgJiYgZWwuX3hfbW9kZWwuc2V0KGVsLnZhbHVlKSk7XG4gICAgfSk7XG4gICAgY2xlYW51cDIoKCkgPT4gcmVtb3ZlUmVzZXRMaXN0ZW5lcigpKTtcbiAgfVxuICBlbC5feF9tb2RlbCA9IHtcbiAgICBnZXQoKSB7XG4gICAgICByZXR1cm4gZ2V0VmFsdWUoKTtcbiAgICB9LFxuICAgIHNldCh2YWx1ZSkge1xuICAgICAgc2V0VmFsdWUodmFsdWUpO1xuICAgIH1cbiAgfTtcbiAgZWwuX3hfZm9yY2VNb2RlbFVwZGF0ZSA9ICh2YWx1ZSkgPT4ge1xuICAgIHZhbHVlID0gdmFsdWUgPT09IHZvaWQgMCA/IGdldFZhbHVlKCkgOiB2YWx1ZTtcbiAgICBpZiAodmFsdWUgPT09IHZvaWQgMCAmJiB0eXBlb2YgZXhwcmVzc2lvbiA9PT0gXCJzdHJpbmdcIiAmJiBleHByZXNzaW9uLm1hdGNoKC9cXC4vKSlcbiAgICAgIHZhbHVlID0gXCJcIjtcbiAgICB3aW5kb3cuZnJvbU1vZGVsID0gdHJ1ZTtcbiAgICBtdXRhdGVEb20oKCkgPT4gYmluZChlbCwgXCJ2YWx1ZVwiLCB2YWx1ZSkpO1xuICAgIGRlbGV0ZSB3aW5kb3cuZnJvbU1vZGVsO1xuICB9O1xuICBlZmZlY3QzKCgpID0+IHtcbiAgICBsZXQgdmFsdWUgPSBnZXRWYWx1ZSgpO1xuICAgIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJ1bmludHJ1c2l2ZVwiKSAmJiBkb2N1bWVudC5hY3RpdmVFbGVtZW50LmlzU2FtZU5vZGUoZWwpKVxuICAgICAgcmV0dXJuO1xuICAgIGVsLl94X2ZvcmNlTW9kZWxVcGRhdGUodmFsdWUpO1xuICB9KTtcbn0pO1xuZnVuY3Rpb24gZ2V0SW5wdXRWYWx1ZShlbCwgbW9kaWZpZXJzLCBldmVudCwgY3VycmVudFZhbHVlKSB7XG4gIHJldHVybiBtdXRhdGVEb20oKCkgPT4ge1xuICAgIGlmIChldmVudCBpbnN0YW5jZW9mIEN1c3RvbUV2ZW50ICYmIGV2ZW50LmRldGFpbCAhPT0gdm9pZCAwKVxuICAgICAgcmV0dXJuIGV2ZW50LmRldGFpbCA/PyBldmVudC50YXJnZXQudmFsdWU7XG4gICAgZWxzZSBpZiAoZWwudHlwZSA9PT0gXCJjaGVja2JveFwiKSB7XG4gICAgICBpZiAoQXJyYXkuaXNBcnJheShjdXJyZW50VmFsdWUpKSB7XG4gICAgICAgIGxldCBuZXdWYWx1ZSA9IG1vZGlmaWVycy5pbmNsdWRlcyhcIm51bWJlclwiKSA/IHNhZmVQYXJzZU51bWJlcihldmVudC50YXJnZXQudmFsdWUpIDogZXZlbnQudGFyZ2V0LnZhbHVlO1xuICAgICAgICByZXR1cm4gZXZlbnQudGFyZ2V0LmNoZWNrZWQgPyBjdXJyZW50VmFsdWUuY29uY2F0KFtuZXdWYWx1ZV0pIDogY3VycmVudFZhbHVlLmZpbHRlcigoZWwyKSA9PiAhY2hlY2tlZEF0dHJMb29zZUNvbXBhcmUyKGVsMiwgbmV3VmFsdWUpKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHJldHVybiBldmVudC50YXJnZXQuY2hlY2tlZDtcbiAgICAgIH1cbiAgICB9IGVsc2UgaWYgKGVsLnRhZ05hbWUudG9Mb3dlckNhc2UoKSA9PT0gXCJzZWxlY3RcIiAmJiBlbC5tdWx0aXBsZSkge1xuICAgICAgcmV0dXJuIG1vZGlmaWVycy5pbmNsdWRlcyhcIm51bWJlclwiKSA/IEFycmF5LmZyb20oZXZlbnQudGFyZ2V0LnNlbGVjdGVkT3B0aW9ucykubWFwKChvcHRpb24pID0+IHtcbiAgICAgICAgbGV0IHJhd1ZhbHVlID0gb3B0aW9uLnZhbHVlIHx8IG9wdGlvbi50ZXh0O1xuICAgICAgICByZXR1cm4gc2FmZVBhcnNlTnVtYmVyKHJhd1ZhbHVlKTtcbiAgICAgIH0pIDogQXJyYXkuZnJvbShldmVudC50YXJnZXQuc2VsZWN0ZWRPcHRpb25zKS5tYXAoKG9wdGlvbikgPT4ge1xuICAgICAgICByZXR1cm4gb3B0aW9uLnZhbHVlIHx8IG9wdGlvbi50ZXh0O1xuICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIGxldCByYXdWYWx1ZSA9IGV2ZW50LnRhcmdldC52YWx1ZTtcbiAgICAgIHJldHVybiBtb2RpZmllcnMuaW5jbHVkZXMoXCJudW1iZXJcIikgPyBzYWZlUGFyc2VOdW1iZXIocmF3VmFsdWUpIDogbW9kaWZpZXJzLmluY2x1ZGVzKFwidHJpbVwiKSA/IHJhd1ZhbHVlLnRyaW0oKSA6IHJhd1ZhbHVlO1xuICAgIH1cbiAgfSk7XG59XG5mdW5jdGlvbiBzYWZlUGFyc2VOdW1iZXIocmF3VmFsdWUpIHtcbiAgbGV0IG51bWJlciA9IHJhd1ZhbHVlID8gcGFyc2VGbG9hdChyYXdWYWx1ZSkgOiBudWxsO1xuICByZXR1cm4gaXNOdW1lcmljMihudW1iZXIpID8gbnVtYmVyIDogcmF3VmFsdWU7XG59XG5mdW5jdGlvbiBjaGVja2VkQXR0ckxvb3NlQ29tcGFyZTIodmFsdWVBLCB2YWx1ZUIpIHtcbiAgcmV0dXJuIHZhbHVlQSA9PSB2YWx1ZUI7XG59XG5mdW5jdGlvbiBpc051bWVyaWMyKHN1YmplY3QpIHtcbiAgcmV0dXJuICFBcnJheS5pc0FycmF5KHN1YmplY3QpICYmICFpc05hTihzdWJqZWN0KTtcbn1cbmZ1bmN0aW9uIGlzR2V0dGVyU2V0dGVyKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPT0gbnVsbCAmJiB0eXBlb2YgdmFsdWUgPT09IFwib2JqZWN0XCIgJiYgdHlwZW9mIHZhbHVlLmdldCA9PT0gXCJmdW5jdGlvblwiICYmIHR5cGVvZiB2YWx1ZS5zZXQgPT09IFwiZnVuY3Rpb25cIjtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1jbG9hay5qc1xuZGlyZWN0aXZlKFwiY2xvYWtcIiwgKGVsKSA9PiBxdWV1ZU1pY3JvdGFzaygoKSA9PiBtdXRhdGVEb20oKCkgPT4gZWwucmVtb3ZlQXR0cmlidXRlKHByZWZpeChcImNsb2FrXCIpKSkpKTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1pbml0LmpzXG5hZGRJbml0U2VsZWN0b3IoKCkgPT4gYFske3ByZWZpeChcImluaXRcIil9XWApO1xuZGlyZWN0aXZlKFwiaW5pdFwiLCBza2lwRHVyaW5nQ2xvbmUoKGVsLCB7ZXhwcmVzc2lvbn0sIHtldmFsdWF0ZTogZXZhbHVhdGUyfSkgPT4ge1xuICBpZiAodHlwZW9mIGV4cHJlc3Npb24gPT09IFwic3RyaW5nXCIpIHtcbiAgICByZXR1cm4gISFleHByZXNzaW9uLnRyaW0oKSAmJiBldmFsdWF0ZTIoZXhwcmVzc2lvbiwge30sIGZhbHNlKTtcbiAgfVxuICByZXR1cm4gZXZhbHVhdGUyKGV4cHJlc3Npb24sIHt9LCBmYWxzZSk7XG59KSk7XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL3NyYy9kaXJlY3RpdmVzL3gtdGV4dC5qc1xuZGlyZWN0aXZlKFwidGV4dFwiLCAoZWwsIHtleHByZXNzaW9ufSwge2VmZmVjdDogZWZmZWN0MywgZXZhbHVhdGVMYXRlcjogZXZhbHVhdGVMYXRlcjJ9KSA9PiB7XG4gIGxldCBldmFsdWF0ZTIgPSBldmFsdWF0ZUxhdGVyMihleHByZXNzaW9uKTtcbiAgZWZmZWN0MygoKSA9PiB7XG4gICAgZXZhbHVhdGUyKCh2YWx1ZSkgPT4ge1xuICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgZWwudGV4dENvbnRlbnQgPSB2YWx1ZTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9KTtcbn0pO1xuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvZGlyZWN0aXZlcy94LWh0bWwuanNcbmRpcmVjdGl2ZShcImh0bWxcIiwgKGVsLCB7ZXhwcmVzc2lvbn0sIHtlZmZlY3Q6IGVmZmVjdDMsIGV2YWx1YXRlTGF0ZXI6IGV2YWx1YXRlTGF0ZXIyfSkgPT4ge1xuICBsZXQgZXZhbHVhdGUyID0gZXZhbHVhdGVMYXRlcjIoZXhwcmVzc2lvbik7XG4gIGVmZmVjdDMoKCkgPT4ge1xuICAgIGV2YWx1YXRlMigodmFsdWUpID0+IHtcbiAgICAgIG11dGF0ZURvbSgoKSA9PiB7XG4gICAgICAgIGVsLmlubmVySFRNTCA9IHZhbHVlO1xuICAgICAgICBlbC5feF9pZ25vcmVTZWxmID0gdHJ1ZTtcbiAgICAgICAgaW5pdFRyZWUoZWwpO1xuICAgICAgICBkZWxldGUgZWwuX3hfaWdub3JlU2VsZjtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9KTtcbn0pO1xuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvZGlyZWN0aXZlcy94LWJpbmQuanNcbm1hcEF0dHJpYnV0ZXMoc3RhcnRpbmdXaXRoKFwiOlwiLCBpbnRvKHByZWZpeChcImJpbmQ6XCIpKSkpO1xuZGlyZWN0aXZlKFwiYmluZFwiLCAoZWwsIHt2YWx1ZSwgbW9kaWZpZXJzLCBleHByZXNzaW9uLCBvcmlnaW5hbH0sIHtlZmZlY3Q6IGVmZmVjdDN9KSA9PiB7XG4gIGlmICghdmFsdWUpIHtcbiAgICBsZXQgYmluZGluZ1Byb3ZpZGVycyA9IHt9O1xuICAgIGluamVjdEJpbmRpbmdQcm92aWRlcnMoYmluZGluZ1Byb3ZpZGVycyk7XG4gICAgbGV0IGdldEJpbmRpbmdzID0gZXZhbHVhdGVMYXRlcihlbCwgZXhwcmVzc2lvbik7XG4gICAgZ2V0QmluZGluZ3MoKGJpbmRpbmdzKSA9PiB7XG4gICAgICBhcHBseUJpbmRpbmdzT2JqZWN0KGVsLCBiaW5kaW5ncywgb3JpZ2luYWwpO1xuICAgIH0sIHtzY29wZTogYmluZGluZ1Byb3ZpZGVyc30pO1xuICAgIHJldHVybjtcbiAgfVxuICBpZiAodmFsdWUgPT09IFwia2V5XCIpXG4gICAgcmV0dXJuIHN0b3JlS2V5Rm9yWEZvcihlbCwgZXhwcmVzc2lvbik7XG4gIGxldCBldmFsdWF0ZTIgPSBldmFsdWF0ZUxhdGVyKGVsLCBleHByZXNzaW9uKTtcbiAgZWZmZWN0MygoKSA9PiBldmFsdWF0ZTIoKHJlc3VsdCkgPT4ge1xuICAgIGlmIChyZXN1bHQgPT09IHZvaWQgMCAmJiB0eXBlb2YgZXhwcmVzc2lvbiA9PT0gXCJzdHJpbmdcIiAmJiBleHByZXNzaW9uLm1hdGNoKC9cXC4vKSkge1xuICAgICAgcmVzdWx0ID0gXCJcIjtcbiAgICB9XG4gICAgbXV0YXRlRG9tKCgpID0+IGJpbmQoZWwsIHZhbHVlLCByZXN1bHQsIG1vZGlmaWVycykpO1xuICB9KSk7XG59KTtcbmZ1bmN0aW9uIHN0b3JlS2V5Rm9yWEZvcihlbCwgZXhwcmVzc2lvbikge1xuICBlbC5feF9rZXlFeHByZXNzaW9uID0gZXhwcmVzc2lvbjtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1kYXRhLmpzXG5hZGRSb290U2VsZWN0b3IoKCkgPT4gYFske3ByZWZpeChcImRhdGFcIil9XWApO1xuZGlyZWN0aXZlKFwiZGF0YVwiLCBza2lwRHVyaW5nQ2xvbmUoKGVsLCB7ZXhwcmVzc2lvbn0sIHtjbGVhbnVwOiBjbGVhbnVwMn0pID0+IHtcbiAgZXhwcmVzc2lvbiA9IGV4cHJlc3Npb24gPT09IFwiXCIgPyBcInt9XCIgOiBleHByZXNzaW9uO1xuICBsZXQgbWFnaWNDb250ZXh0ID0ge307XG4gIGluamVjdE1hZ2ljcyhtYWdpY0NvbnRleHQsIGVsKTtcbiAgbGV0IGRhdGFQcm92aWRlckNvbnRleHQgPSB7fTtcbiAgaW5qZWN0RGF0YVByb3ZpZGVycyhkYXRhUHJvdmlkZXJDb250ZXh0LCBtYWdpY0NvbnRleHQpO1xuICBsZXQgZGF0YTIgPSBldmFsdWF0ZShlbCwgZXhwcmVzc2lvbiwge3Njb3BlOiBkYXRhUHJvdmlkZXJDb250ZXh0fSk7XG4gIGlmIChkYXRhMiA9PT0gdm9pZCAwIHx8IGRhdGEyID09PSB0cnVlKVxuICAgIGRhdGEyID0ge307XG4gIGluamVjdE1hZ2ljcyhkYXRhMiwgZWwpO1xuICBsZXQgcmVhY3RpdmVEYXRhID0gcmVhY3RpdmUoZGF0YTIpO1xuICBpbml0SW50ZXJjZXB0b3JzKHJlYWN0aXZlRGF0YSk7XG4gIGxldCB1bmRvID0gYWRkU2NvcGVUb05vZGUoZWwsIHJlYWN0aXZlRGF0YSk7XG4gIHJlYWN0aXZlRGF0YVtcImluaXRcIl0gJiYgZXZhbHVhdGUoZWwsIHJlYWN0aXZlRGF0YVtcImluaXRcIl0pO1xuICBjbGVhbnVwMigoKSA9PiB7XG4gICAgcmVhY3RpdmVEYXRhW1wiZGVzdHJveVwiXSAmJiBldmFsdWF0ZShlbCwgcmVhY3RpdmVEYXRhW1wiZGVzdHJveVwiXSk7XG4gICAgdW5kbygpO1xuICB9KTtcbn0pKTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1zaG93LmpzXG5kaXJlY3RpdmUoXCJzaG93XCIsIChlbCwge21vZGlmaWVycywgZXhwcmVzc2lvbn0sIHtlZmZlY3Q6IGVmZmVjdDN9KSA9PiB7XG4gIGxldCBldmFsdWF0ZTIgPSBldmFsdWF0ZUxhdGVyKGVsLCBleHByZXNzaW9uKTtcbiAgaWYgKCFlbC5feF9kb0hpZGUpXG4gICAgZWwuX3hfZG9IaWRlID0gKCkgPT4ge1xuICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgZWwuc3R5bGUuc2V0UHJvcGVydHkoXCJkaXNwbGF5XCIsIFwibm9uZVwiLCBtb2RpZmllcnMuaW5jbHVkZXMoXCJpbXBvcnRhbnRcIikgPyBcImltcG9ydGFudFwiIDogdm9pZCAwKTtcbiAgICAgIH0pO1xuICAgIH07XG4gIGlmICghZWwuX3hfZG9TaG93KVxuICAgIGVsLl94X2RvU2hvdyA9ICgpID0+IHtcbiAgICAgIG11dGF0ZURvbSgoKSA9PiB7XG4gICAgICAgIGlmIChlbC5zdHlsZS5sZW5ndGggPT09IDEgJiYgZWwuc3R5bGUuZGlzcGxheSA9PT0gXCJub25lXCIpIHtcbiAgICAgICAgICBlbC5yZW1vdmVBdHRyaWJ1dGUoXCJzdHlsZVwiKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBlbC5zdHlsZS5yZW1vdmVQcm9wZXJ0eShcImRpc3BsYXlcIik7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH07XG4gIGxldCBoaWRlID0gKCkgPT4ge1xuICAgIGVsLl94X2RvSGlkZSgpO1xuICAgIGVsLl94X2lzU2hvd24gPSBmYWxzZTtcbiAgfTtcbiAgbGV0IHNob3cgPSAoKSA9PiB7XG4gICAgZWwuX3hfZG9TaG93KCk7XG4gICAgZWwuX3hfaXNTaG93biA9IHRydWU7XG4gIH07XG4gIGxldCBjbGlja0F3YXlDb21wYXRpYmxlU2hvdyA9ICgpID0+IHNldFRpbWVvdXQoc2hvdyk7XG4gIGxldCB0b2dnbGUgPSBvbmNlKCh2YWx1ZSkgPT4gdmFsdWUgPyBzaG93KCkgOiBoaWRlKCksICh2YWx1ZSkgPT4ge1xuICAgIGlmICh0eXBlb2YgZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgICBlbC5feF90b2dnbGVBbmRDYXNjYWRlV2l0aFRyYW5zaXRpb25zKGVsLCB2YWx1ZSwgc2hvdywgaGlkZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhbHVlID8gY2xpY2tBd2F5Q29tcGF0aWJsZVNob3coKSA6IGhpZGUoKTtcbiAgICB9XG4gIH0pO1xuICBsZXQgb2xkVmFsdWU7XG4gIGxldCBmaXJzdFRpbWUgPSB0cnVlO1xuICBlZmZlY3QzKCgpID0+IGV2YWx1YXRlMigodmFsdWUpID0+IHtcbiAgICBpZiAoIWZpcnN0VGltZSAmJiB2YWx1ZSA9PT0gb2xkVmFsdWUpXG4gICAgICByZXR1cm47XG4gICAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImltbWVkaWF0ZVwiKSlcbiAgICAgIHZhbHVlID8gY2xpY2tBd2F5Q29tcGF0aWJsZVNob3coKSA6IGhpZGUoKTtcbiAgICB0b2dnbGUodmFsdWUpO1xuICAgIG9sZFZhbHVlID0gdmFsdWU7XG4gICAgZmlyc3RUaW1lID0gZmFsc2U7XG4gIH0pKTtcbn0pO1xuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvZGlyZWN0aXZlcy94LWZvci5qc1xuZGlyZWN0aXZlKFwiZm9yXCIsIChlbCwge2V4cHJlc3Npb259LCB7ZWZmZWN0OiBlZmZlY3QzLCBjbGVhbnVwOiBjbGVhbnVwMn0pID0+IHtcbiAgbGV0IGl0ZXJhdG9yTmFtZXMgPSBwYXJzZUZvckV4cHJlc3Npb24oZXhwcmVzc2lvbik7XG4gIGxldCBldmFsdWF0ZUl0ZW1zID0gZXZhbHVhdGVMYXRlcihlbCwgaXRlcmF0b3JOYW1lcy5pdGVtcyk7XG4gIGxldCBldmFsdWF0ZUtleSA9IGV2YWx1YXRlTGF0ZXIoZWwsIGVsLl94X2tleUV4cHJlc3Npb24gfHwgXCJpbmRleFwiKTtcbiAgZWwuX3hfcHJldktleXMgPSBbXTtcbiAgZWwuX3hfbG9va3VwID0ge307XG4gIGVmZmVjdDMoKCkgPT4gbG9vcChlbCwgaXRlcmF0b3JOYW1lcywgZXZhbHVhdGVJdGVtcywgZXZhbHVhdGVLZXkpKTtcbiAgY2xlYW51cDIoKCkgPT4ge1xuICAgIE9iamVjdC52YWx1ZXMoZWwuX3hfbG9va3VwKS5mb3JFYWNoKChlbDIpID0+IGVsMi5yZW1vdmUoKSk7XG4gICAgZGVsZXRlIGVsLl94X3ByZXZLZXlzO1xuICAgIGRlbGV0ZSBlbC5feF9sb29rdXA7XG4gIH0pO1xufSk7XG5mdW5jdGlvbiBsb29wKGVsLCBpdGVyYXRvck5hbWVzLCBldmFsdWF0ZUl0ZW1zLCBldmFsdWF0ZUtleSkge1xuICBsZXQgaXNPYmplY3QyID0gKGkpID0+IHR5cGVvZiBpID09PSBcIm9iamVjdFwiICYmICFBcnJheS5pc0FycmF5KGkpO1xuICBsZXQgdGVtcGxhdGVFbCA9IGVsO1xuICBldmFsdWF0ZUl0ZW1zKChpdGVtcykgPT4ge1xuICAgIGlmIChpc051bWVyaWMzKGl0ZW1zKSAmJiBpdGVtcyA+PSAwKSB7XG4gICAgICBpdGVtcyA9IEFycmF5LmZyb20oQXJyYXkoaXRlbXMpLmtleXMoKSwgKGkpID0+IGkgKyAxKTtcbiAgICB9XG4gICAgaWYgKGl0ZW1zID09PSB2b2lkIDApXG4gICAgICBpdGVtcyA9IFtdO1xuICAgIGxldCBsb29rdXAgPSBlbC5feF9sb29rdXA7XG4gICAgbGV0IHByZXZLZXlzID0gZWwuX3hfcHJldktleXM7XG4gICAgbGV0IHNjb3BlcyA9IFtdO1xuICAgIGxldCBrZXlzID0gW107XG4gICAgaWYgKGlzT2JqZWN0MihpdGVtcykpIHtcbiAgICAgIGl0ZW1zID0gT2JqZWN0LmVudHJpZXMoaXRlbXMpLm1hcCgoW2tleSwgdmFsdWVdKSA9PiB7XG4gICAgICAgIGxldCBzY29wZTIgPSBnZXRJdGVyYXRpb25TY29wZVZhcmlhYmxlcyhpdGVyYXRvck5hbWVzLCB2YWx1ZSwga2V5LCBpdGVtcyk7XG4gICAgICAgIGV2YWx1YXRlS2V5KCh2YWx1ZTIpID0+IGtleXMucHVzaCh2YWx1ZTIpLCB7c2NvcGU6IHtpbmRleDoga2V5LCAuLi5zY29wZTJ9fSk7XG4gICAgICAgIHNjb3Blcy5wdXNoKHNjb3BlMik7XG4gICAgICB9KTtcbiAgICB9IGVsc2Uge1xuICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBpdGVtcy5sZW5ndGg7IGkrKykge1xuICAgICAgICBsZXQgc2NvcGUyID0gZ2V0SXRlcmF0aW9uU2NvcGVWYXJpYWJsZXMoaXRlcmF0b3JOYW1lcywgaXRlbXNbaV0sIGksIGl0ZW1zKTtcbiAgICAgICAgZXZhbHVhdGVLZXkoKHZhbHVlKSA9PiBrZXlzLnB1c2godmFsdWUpLCB7c2NvcGU6IHtpbmRleDogaSwgLi4uc2NvcGUyfX0pO1xuICAgICAgICBzY29wZXMucHVzaChzY29wZTIpO1xuICAgICAgfVxuICAgIH1cbiAgICBsZXQgYWRkcyA9IFtdO1xuICAgIGxldCBtb3ZlcyA9IFtdO1xuICAgIGxldCByZW1vdmVzID0gW107XG4gICAgbGV0IHNhbWVzID0gW107XG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBwcmV2S2V5cy5sZW5ndGg7IGkrKykge1xuICAgICAgbGV0IGtleSA9IHByZXZLZXlzW2ldO1xuICAgICAgaWYgKGtleXMuaW5kZXhPZihrZXkpID09PSAtMSlcbiAgICAgICAgcmVtb3Zlcy5wdXNoKGtleSk7XG4gICAgfVxuICAgIHByZXZLZXlzID0gcHJldktleXMuZmlsdGVyKChrZXkpID0+ICFyZW1vdmVzLmluY2x1ZGVzKGtleSkpO1xuICAgIGxldCBsYXN0S2V5ID0gXCJ0ZW1wbGF0ZVwiO1xuICAgIGZvciAobGV0IGkgPSAwOyBpIDwga2V5cy5sZW5ndGg7IGkrKykge1xuICAgICAgbGV0IGtleSA9IGtleXNbaV07XG4gICAgICBsZXQgcHJldkluZGV4ID0gcHJldktleXMuaW5kZXhPZihrZXkpO1xuICAgICAgaWYgKHByZXZJbmRleCA9PT0gLTEpIHtcbiAgICAgICAgcHJldktleXMuc3BsaWNlKGksIDAsIGtleSk7XG4gICAgICAgIGFkZHMucHVzaChbbGFzdEtleSwgaV0pO1xuICAgICAgfSBlbHNlIGlmIChwcmV2SW5kZXggIT09IGkpIHtcbiAgICAgICAgbGV0IGtleUluU3BvdCA9IHByZXZLZXlzLnNwbGljZShpLCAxKVswXTtcbiAgICAgICAgbGV0IGtleUZvclNwb3QgPSBwcmV2S2V5cy5zcGxpY2UocHJldkluZGV4IC0gMSwgMSlbMF07XG4gICAgICAgIHByZXZLZXlzLnNwbGljZShpLCAwLCBrZXlGb3JTcG90KTtcbiAgICAgICAgcHJldktleXMuc3BsaWNlKHByZXZJbmRleCwgMCwga2V5SW5TcG90KTtcbiAgICAgICAgbW92ZXMucHVzaChba2V5SW5TcG90LCBrZXlGb3JTcG90XSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBzYW1lcy5wdXNoKGtleSk7XG4gICAgICB9XG4gICAgICBsYXN0S2V5ID0ga2V5O1xuICAgIH1cbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IHJlbW92ZXMubGVuZ3RoOyBpKyspIHtcbiAgICAgIGxldCBrZXkgPSByZW1vdmVzW2ldO1xuICAgICAgaWYgKCEhbG9va3VwW2tleV0uX3hfZWZmZWN0cykge1xuICAgICAgICBsb29rdXBba2V5XS5feF9lZmZlY3RzLmZvckVhY2goZGVxdWV1ZUpvYik7XG4gICAgICB9XG4gICAgICBsb29rdXBba2V5XS5yZW1vdmUoKTtcbiAgICAgIGxvb2t1cFtrZXldID0gbnVsbDtcbiAgICAgIGRlbGV0ZSBsb29rdXBba2V5XTtcbiAgICB9XG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBtb3Zlcy5sZW5ndGg7IGkrKykge1xuICAgICAgbGV0IFtrZXlJblNwb3QsIGtleUZvclNwb3RdID0gbW92ZXNbaV07XG4gICAgICBsZXQgZWxJblNwb3QgPSBsb29rdXBba2V5SW5TcG90XTtcbiAgICAgIGxldCBlbEZvclNwb3QgPSBsb29rdXBba2V5Rm9yU3BvdF07XG4gICAgICBsZXQgbWFya2VyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcImRpdlwiKTtcbiAgICAgIG11dGF0ZURvbSgoKSA9PiB7XG4gICAgICAgIGlmICghZWxGb3JTcG90KVxuICAgICAgICAgIHdhcm4oYHgtZm9yIFwiOmtleVwiIGlzIHVuZGVmaW5lZCBvciBpbnZhbGlkYCwgdGVtcGxhdGVFbCk7XG4gICAgICAgIGVsRm9yU3BvdC5hZnRlcihtYXJrZXIpO1xuICAgICAgICBlbEluU3BvdC5hZnRlcihlbEZvclNwb3QpO1xuICAgICAgICBlbEZvclNwb3QuX3hfY3VycmVudElmRWwgJiYgZWxGb3JTcG90LmFmdGVyKGVsRm9yU3BvdC5feF9jdXJyZW50SWZFbCk7XG4gICAgICAgIG1hcmtlci5iZWZvcmUoZWxJblNwb3QpO1xuICAgICAgICBlbEluU3BvdC5feF9jdXJyZW50SWZFbCAmJiBlbEluU3BvdC5hZnRlcihlbEluU3BvdC5feF9jdXJyZW50SWZFbCk7XG4gICAgICAgIG1hcmtlci5yZW1vdmUoKTtcbiAgICAgIH0pO1xuICAgICAgZWxGb3JTcG90Ll94X3JlZnJlc2hYRm9yU2NvcGUoc2NvcGVzW2tleXMuaW5kZXhPZihrZXlGb3JTcG90KV0pO1xuICAgIH1cbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IGFkZHMubGVuZ3RoOyBpKyspIHtcbiAgICAgIGxldCBbbGFzdEtleTIsIGluZGV4XSA9IGFkZHNbaV07XG4gICAgICBsZXQgbGFzdEVsID0gbGFzdEtleTIgPT09IFwidGVtcGxhdGVcIiA/IHRlbXBsYXRlRWwgOiBsb29rdXBbbGFzdEtleTJdO1xuICAgICAgaWYgKGxhc3RFbC5feF9jdXJyZW50SWZFbClcbiAgICAgICAgbGFzdEVsID0gbGFzdEVsLl94X2N1cnJlbnRJZkVsO1xuICAgICAgbGV0IHNjb3BlMiA9IHNjb3Blc1tpbmRleF07XG4gICAgICBsZXQga2V5ID0ga2V5c1tpbmRleF07XG4gICAgICBsZXQgY2xvbmUyID0gZG9jdW1lbnQuaW1wb3J0Tm9kZSh0ZW1wbGF0ZUVsLmNvbnRlbnQsIHRydWUpLmZpcnN0RWxlbWVudENoaWxkO1xuICAgICAgbGV0IHJlYWN0aXZlU2NvcGUgPSByZWFjdGl2ZShzY29wZTIpO1xuICAgICAgYWRkU2NvcGVUb05vZGUoY2xvbmUyLCByZWFjdGl2ZVNjb3BlLCB0ZW1wbGF0ZUVsKTtcbiAgICAgIGNsb25lMi5feF9yZWZyZXNoWEZvclNjb3BlID0gKG5ld1Njb3BlKSA9PiB7XG4gICAgICAgIE9iamVjdC5lbnRyaWVzKG5ld1Njb3BlKS5mb3JFYWNoKChba2V5MiwgdmFsdWVdKSA9PiB7XG4gICAgICAgICAgcmVhY3RpdmVTY29wZVtrZXkyXSA9IHZhbHVlO1xuICAgICAgICB9KTtcbiAgICAgIH07XG4gICAgICBtdXRhdGVEb20oKCkgPT4ge1xuICAgICAgICBsYXN0RWwuYWZ0ZXIoY2xvbmUyKTtcbiAgICAgICAgaW5pdFRyZWUoY2xvbmUyKTtcbiAgICAgIH0pO1xuICAgICAgaWYgKHR5cGVvZiBrZXkgPT09IFwib2JqZWN0XCIpIHtcbiAgICAgICAgd2FybihcIngtZm9yIGtleSBjYW5ub3QgYmUgYW4gb2JqZWN0LCBpdCBtdXN0IGJlIGEgc3RyaW5nIG9yIGFuIGludGVnZXJcIiwgdGVtcGxhdGVFbCk7XG4gICAgICB9XG4gICAgICBsb29rdXBba2V5XSA9IGNsb25lMjtcbiAgICB9XG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBzYW1lcy5sZW5ndGg7IGkrKykge1xuICAgICAgbG9va3VwW3NhbWVzW2ldXS5feF9yZWZyZXNoWEZvclNjb3BlKHNjb3Blc1trZXlzLmluZGV4T2Yoc2FtZXNbaV0pXSk7XG4gICAgfVxuICAgIHRlbXBsYXRlRWwuX3hfcHJldktleXMgPSBrZXlzO1xuICB9KTtcbn1cbmZ1bmN0aW9uIHBhcnNlRm9yRXhwcmVzc2lvbihleHByZXNzaW9uKSB7XG4gIGxldCBmb3JJdGVyYXRvclJFID0gLywoW14sXFx9XFxdXSopKD86LChbXixcXH1cXF1dKikpPyQvO1xuICBsZXQgc3RyaXBQYXJlbnNSRSA9IC9eXFxzKlxcKHxcXClcXHMqJC9nO1xuICBsZXQgZm9yQWxpYXNSRSA9IC8oW1xcc1xcU10qPylcXHMrKD86aW58b2YpXFxzKyhbXFxzXFxTXSopLztcbiAgbGV0IGluTWF0Y2ggPSBleHByZXNzaW9uLm1hdGNoKGZvckFsaWFzUkUpO1xuICBpZiAoIWluTWF0Y2gpXG4gICAgcmV0dXJuO1xuICBsZXQgcmVzID0ge307XG4gIHJlcy5pdGVtcyA9IGluTWF0Y2hbMl0udHJpbSgpO1xuICBsZXQgaXRlbSA9IGluTWF0Y2hbMV0ucmVwbGFjZShzdHJpcFBhcmVuc1JFLCBcIlwiKS50cmltKCk7XG4gIGxldCBpdGVyYXRvck1hdGNoID0gaXRlbS5tYXRjaChmb3JJdGVyYXRvclJFKTtcbiAgaWYgKGl0ZXJhdG9yTWF0Y2gpIHtcbiAgICByZXMuaXRlbSA9IGl0ZW0ucmVwbGFjZShmb3JJdGVyYXRvclJFLCBcIlwiKS50cmltKCk7XG4gICAgcmVzLmluZGV4ID0gaXRlcmF0b3JNYXRjaFsxXS50cmltKCk7XG4gICAgaWYgKGl0ZXJhdG9yTWF0Y2hbMl0pIHtcbiAgICAgIHJlcy5jb2xsZWN0aW9uID0gaXRlcmF0b3JNYXRjaFsyXS50cmltKCk7XG4gICAgfVxuICB9IGVsc2Uge1xuICAgIHJlcy5pdGVtID0gaXRlbTtcbiAgfVxuICByZXR1cm4gcmVzO1xufVxuZnVuY3Rpb24gZ2V0SXRlcmF0aW9uU2NvcGVWYXJpYWJsZXMoaXRlcmF0b3JOYW1lcywgaXRlbSwgaW5kZXgsIGl0ZW1zKSB7XG4gIGxldCBzY29wZVZhcmlhYmxlcyA9IHt9O1xuICBpZiAoL15cXFsuKlxcXSQvLnRlc3QoaXRlcmF0b3JOYW1lcy5pdGVtKSAmJiBBcnJheS5pc0FycmF5KGl0ZW0pKSB7XG4gICAgbGV0IG5hbWVzID0gaXRlcmF0b3JOYW1lcy5pdGVtLnJlcGxhY2UoXCJbXCIsIFwiXCIpLnJlcGxhY2UoXCJdXCIsIFwiXCIpLnNwbGl0KFwiLFwiKS5tYXAoKGkpID0+IGkudHJpbSgpKTtcbiAgICBuYW1lcy5mb3JFYWNoKChuYW1lLCBpKSA9PiB7XG4gICAgICBzY29wZVZhcmlhYmxlc1tuYW1lXSA9IGl0ZW1baV07XG4gICAgfSk7XG4gIH0gZWxzZSBpZiAoL15cXHsuKlxcfSQvLnRlc3QoaXRlcmF0b3JOYW1lcy5pdGVtKSAmJiAhQXJyYXkuaXNBcnJheShpdGVtKSAmJiB0eXBlb2YgaXRlbSA9PT0gXCJvYmplY3RcIikge1xuICAgIGxldCBuYW1lcyA9IGl0ZXJhdG9yTmFtZXMuaXRlbS5yZXBsYWNlKFwie1wiLCBcIlwiKS5yZXBsYWNlKFwifVwiLCBcIlwiKS5zcGxpdChcIixcIikubWFwKChpKSA9PiBpLnRyaW0oKSk7XG4gICAgbmFtZXMuZm9yRWFjaCgobmFtZSkgPT4ge1xuICAgICAgc2NvcGVWYXJpYWJsZXNbbmFtZV0gPSBpdGVtW25hbWVdO1xuICAgIH0pO1xuICB9IGVsc2Uge1xuICAgIHNjb3BlVmFyaWFibGVzW2l0ZXJhdG9yTmFtZXMuaXRlbV0gPSBpdGVtO1xuICB9XG4gIGlmIChpdGVyYXRvck5hbWVzLmluZGV4KVxuICAgIHNjb3BlVmFyaWFibGVzW2l0ZXJhdG9yTmFtZXMuaW5kZXhdID0gaW5kZXg7XG4gIGlmIChpdGVyYXRvck5hbWVzLmNvbGxlY3Rpb24pXG4gICAgc2NvcGVWYXJpYWJsZXNbaXRlcmF0b3JOYW1lcy5jb2xsZWN0aW9uXSA9IGl0ZW1zO1xuICByZXR1cm4gc2NvcGVWYXJpYWJsZXM7XG59XG5mdW5jdGlvbiBpc051bWVyaWMzKHN1YmplY3QpIHtcbiAgcmV0dXJuICFBcnJheS5pc0FycmF5KHN1YmplY3QpICYmICFpc05hTihzdWJqZWN0KTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1yZWYuanNcbmZ1bmN0aW9uIGhhbmRsZXIyKCkge1xufVxuaGFuZGxlcjIuaW5saW5lID0gKGVsLCB7ZXhwcmVzc2lvbn0sIHtjbGVhbnVwOiBjbGVhbnVwMn0pID0+IHtcbiAgbGV0IHJvb3QgPSBjbG9zZXN0Um9vdChlbCk7XG4gIGlmICghcm9vdC5feF9yZWZzKVxuICAgIHJvb3QuX3hfcmVmcyA9IHt9O1xuICByb290Ll94X3JlZnNbZXhwcmVzc2lvbl0gPSBlbDtcbiAgY2xlYW51cDIoKCkgPT4gZGVsZXRlIHJvb3QuX3hfcmVmc1tleHByZXNzaW9uXSk7XG59O1xuZGlyZWN0aXZlKFwicmVmXCIsIGhhbmRsZXIyKTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1pZi5qc1xuZGlyZWN0aXZlKFwiaWZcIiwgKGVsLCB7ZXhwcmVzc2lvbn0sIHtlZmZlY3Q6IGVmZmVjdDMsIGNsZWFudXA6IGNsZWFudXAyfSkgPT4ge1xuICBsZXQgZXZhbHVhdGUyID0gZXZhbHVhdGVMYXRlcihlbCwgZXhwcmVzc2lvbik7XG4gIGxldCBzaG93ID0gKCkgPT4ge1xuICAgIGlmIChlbC5feF9jdXJyZW50SWZFbClcbiAgICAgIHJldHVybiBlbC5feF9jdXJyZW50SWZFbDtcbiAgICBsZXQgY2xvbmUyID0gZWwuY29udGVudC5jbG9uZU5vZGUodHJ1ZSkuZmlyc3RFbGVtZW50Q2hpbGQ7XG4gICAgYWRkU2NvcGVUb05vZGUoY2xvbmUyLCB7fSwgZWwpO1xuICAgIG11dGF0ZURvbSgoKSA9PiB7XG4gICAgICBlbC5hZnRlcihjbG9uZTIpO1xuICAgICAgaW5pdFRyZWUoY2xvbmUyKTtcbiAgICB9KTtcbiAgICBlbC5feF9jdXJyZW50SWZFbCA9IGNsb25lMjtcbiAgICBlbC5feF91bmRvSWYgPSAoKSA9PiB7XG4gICAgICB3YWxrKGNsb25lMiwgKG5vZGUpID0+IHtcbiAgICAgICAgaWYgKCEhbm9kZS5feF9lZmZlY3RzKSB7XG4gICAgICAgICAgbm9kZS5feF9lZmZlY3RzLmZvckVhY2goZGVxdWV1ZUpvYik7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgICAgY2xvbmUyLnJlbW92ZSgpO1xuICAgICAgZGVsZXRlIGVsLl94X2N1cnJlbnRJZkVsO1xuICAgIH07XG4gICAgcmV0dXJuIGNsb25lMjtcbiAgfTtcbiAgbGV0IGhpZGUgPSAoKSA9PiB7XG4gICAgaWYgKCFlbC5feF91bmRvSWYpXG4gICAgICByZXR1cm47XG4gICAgZWwuX3hfdW5kb0lmKCk7XG4gICAgZGVsZXRlIGVsLl94X3VuZG9JZjtcbiAgfTtcbiAgZWZmZWN0MygoKSA9PiBldmFsdWF0ZTIoKHZhbHVlKSA9PiB7XG4gICAgdmFsdWUgPyBzaG93KCkgOiBoaWRlKCk7XG4gIH0pKTtcbiAgY2xlYW51cDIoKCkgPT4gZWwuX3hfdW5kb0lmICYmIGVsLl94X3VuZG9JZigpKTtcbn0pO1xuXG4vLyBwYWNrYWdlcy9hbHBpbmVqcy9zcmMvZGlyZWN0aXZlcy94LWlkLmpzXG5kaXJlY3RpdmUoXCJpZFwiLCAoZWwsIHtleHByZXNzaW9ufSwge2V2YWx1YXRlOiBldmFsdWF0ZTJ9KSA9PiB7XG4gIGxldCBuYW1lcyA9IGV2YWx1YXRlMihleHByZXNzaW9uKTtcbiAgbmFtZXMuZm9yRWFjaCgobmFtZSkgPT4gc2V0SWRSb290KGVsLCBuYW1lKSk7XG59KTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMveC1vbi5qc1xubWFwQXR0cmlidXRlcyhzdGFydGluZ1dpdGgoXCJAXCIsIGludG8ocHJlZml4KFwib246XCIpKSkpO1xuZGlyZWN0aXZlKFwib25cIiwgc2tpcER1cmluZ0Nsb25lKChlbCwge3ZhbHVlLCBtb2RpZmllcnMsIGV4cHJlc3Npb259LCB7Y2xlYW51cDogY2xlYW51cDJ9KSA9PiB7XG4gIGxldCBldmFsdWF0ZTIgPSBleHByZXNzaW9uID8gZXZhbHVhdGVMYXRlcihlbCwgZXhwcmVzc2lvbikgOiAoKSA9PiB7XG4gIH07XG4gIGlmIChlbC50YWdOYW1lLnRvTG93ZXJDYXNlKCkgPT09IFwidGVtcGxhdGVcIikge1xuICAgIGlmICghZWwuX3hfZm9yd2FyZEV2ZW50cylcbiAgICAgIGVsLl94X2ZvcndhcmRFdmVudHMgPSBbXTtcbiAgICBpZiAoIWVsLl94X2ZvcndhcmRFdmVudHMuaW5jbHVkZXModmFsdWUpKVxuICAgICAgZWwuX3hfZm9yd2FyZEV2ZW50cy5wdXNoKHZhbHVlKTtcbiAgfVxuICBsZXQgcmVtb3ZlTGlzdGVuZXIgPSBvbihlbCwgdmFsdWUsIG1vZGlmaWVycywgKGUpID0+IHtcbiAgICBldmFsdWF0ZTIoKCkgPT4ge1xuICAgIH0sIHtzY29wZTogeyRldmVudDogZX0sIHBhcmFtczogW2VdfSk7XG4gIH0pO1xuICBjbGVhbnVwMigoKSA9PiByZW1vdmVMaXN0ZW5lcigpKTtcbn0pKTtcblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2RpcmVjdGl2ZXMvaW5kZXguanNcbndhcm5NaXNzaW5nUGx1Z2luRGlyZWN0aXZlKFwiQ29sbGFwc2VcIiwgXCJjb2xsYXBzZVwiLCBcImNvbGxhcHNlXCIpO1xud2Fybk1pc3NpbmdQbHVnaW5EaXJlY3RpdmUoXCJJbnRlcnNlY3RcIiwgXCJpbnRlcnNlY3RcIiwgXCJpbnRlcnNlY3RcIik7XG53YXJuTWlzc2luZ1BsdWdpbkRpcmVjdGl2ZShcIkZvY3VzXCIsIFwidHJhcFwiLCBcImZvY3VzXCIpO1xud2Fybk1pc3NpbmdQbHVnaW5EaXJlY3RpdmUoXCJNYXNrXCIsIFwibWFza1wiLCBcIm1hc2tcIik7XG5mdW5jdGlvbiB3YXJuTWlzc2luZ1BsdWdpbkRpcmVjdGl2ZShuYW1lLCBkaXJlY3RpdmVOYW1lMiwgc2x1Zykge1xuICBkaXJlY3RpdmUoZGlyZWN0aXZlTmFtZTIsIChlbCkgPT4gd2FybihgWW91IGNhbid0IHVzZSBbeC0ke2RpcmVjdGl2ZU5hbWUyfV0gd2l0aG91dCBmaXJzdCBpbnN0YWxsaW5nIHRoZSBcIiR7bmFtZX1cIiBwbHVnaW4gaGVyZTogaHR0cHM6Ly9hbHBpbmVqcy5kZXYvcGx1Z2lucy8ke3NsdWd9YCwgZWwpKTtcbn1cblxuLy8gcGFja2FnZXMvYWxwaW5lanMvc3JjL2luZGV4LmpzXG5hbHBpbmVfZGVmYXVsdC5zZXRFdmFsdWF0b3Iobm9ybWFsRXZhbHVhdG9yKTtcbmFscGluZV9kZWZhdWx0LnNldFJlYWN0aXZpdHlFbmdpbmUoe3JlYWN0aXZlOiByZWFjdGl2ZTIsIGVmZmVjdDogZWZmZWN0MiwgcmVsZWFzZTogc3RvcCwgcmF3OiB0b1Jhd30pO1xudmFyIHNyY19kZWZhdWx0ID0gYWxwaW5lX2RlZmF1bHQ7XG5cbi8vIHBhY2thZ2VzL2FscGluZWpzL2J1aWxkcy9tb2R1bGUuanNcbnZhciBtb2R1bGVfZGVmYXVsdCA9IHNyY19kZWZhdWx0O1xuZXhwb3J0IHtcbiAgbW9kdWxlX2RlZmF1bHQgYXMgZGVmYXVsdFxufTtcbiIsICJ2YXIgX19jcmVhdGUgPSBPYmplY3QuY3JlYXRlO1xudmFyIF9fZGVmUHJvcCA9IE9iamVjdC5kZWZpbmVQcm9wZXJ0eTtcbnZhciBfX2dldFByb3RvT2YgPSBPYmplY3QuZ2V0UHJvdG90eXBlT2Y7XG52YXIgX19oYXNPd25Qcm9wID0gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eTtcbnZhciBfX2dldE93blByb3BOYW1lcyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzO1xudmFyIF9fZ2V0T3duUHJvcERlc2MgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yO1xudmFyIF9fbWFya0FzTW9kdWxlID0gKHRhcmdldCkgPT4gX19kZWZQcm9wKHRhcmdldCwgXCJfX2VzTW9kdWxlXCIsIHt2YWx1ZTogdHJ1ZX0pO1xudmFyIF9fY29tbW9uSlMgPSAoY2FsbGJhY2ssIG1vZHVsZSkgPT4gKCkgPT4ge1xuICBpZiAoIW1vZHVsZSkge1xuICAgIG1vZHVsZSA9IHtleHBvcnRzOiB7fX07XG4gICAgY2FsbGJhY2sobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSk7XG4gIH1cbiAgcmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufTtcbnZhciBfX2V4cG9ydFN0YXIgPSAodGFyZ2V0LCBtb2R1bGUsIGRlc2MpID0+IHtcbiAgaWYgKG1vZHVsZSAmJiB0eXBlb2YgbW9kdWxlID09PSBcIm9iamVjdFwiIHx8IHR5cGVvZiBtb2R1bGUgPT09IFwiZnVuY3Rpb25cIikge1xuICAgIGZvciAobGV0IGtleSBvZiBfX2dldE93blByb3BOYW1lcyhtb2R1bGUpKVxuICAgICAgaWYgKCFfX2hhc093blByb3AuY2FsbCh0YXJnZXQsIGtleSkgJiYga2V5ICE9PSBcImRlZmF1bHRcIilcbiAgICAgICAgX19kZWZQcm9wKHRhcmdldCwga2V5LCB7Z2V0OiAoKSA9PiBtb2R1bGVba2V5XSwgZW51bWVyYWJsZTogIShkZXNjID0gX19nZXRPd25Qcm9wRGVzYyhtb2R1bGUsIGtleSkpIHx8IGRlc2MuZW51bWVyYWJsZX0pO1xuICB9XG4gIHJldHVybiB0YXJnZXQ7XG59O1xudmFyIF9fdG9Nb2R1bGUgPSAobW9kdWxlKSA9PiB7XG4gIHJldHVybiBfX2V4cG9ydFN0YXIoX19tYXJrQXNNb2R1bGUoX19kZWZQcm9wKG1vZHVsZSAhPSBudWxsID8gX19jcmVhdGUoX19nZXRQcm90b09mKG1vZHVsZSkpIDoge30sIFwiZGVmYXVsdFwiLCBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgJiYgXCJkZWZhdWx0XCIgaW4gbW9kdWxlID8ge2dldDogKCkgPT4gbW9kdWxlLmRlZmF1bHQsIGVudW1lcmFibGU6IHRydWV9IDoge3ZhbHVlOiBtb2R1bGUsIGVudW1lcmFibGU6IHRydWV9KSksIG1vZHVsZSk7XG59O1xuXG4vLyBub2RlX21vZHVsZXMvbW91c2V0cmFwL21vdXNldHJhcC5qc1xudmFyIHJlcXVpcmVfbW91c2V0cmFwID0gX19jb21tb25KUygoZXhwb3J0cywgbW9kdWxlKSA9PiB7XG4gIChmdW5jdGlvbih3aW5kb3cyLCBkb2N1bWVudDIsIHVuZGVmaW5lZDIpIHtcbiAgICBpZiAoIXdpbmRvdzIpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdmFyIF9NQVAgPSB7XG4gICAgICA4OiBcImJhY2tzcGFjZVwiLFxuICAgICAgOTogXCJ0YWJcIixcbiAgICAgIDEzOiBcImVudGVyXCIsXG4gICAgICAxNjogXCJzaGlmdFwiLFxuICAgICAgMTc6IFwiY3RybFwiLFxuICAgICAgMTg6IFwiYWx0XCIsXG4gICAgICAyMDogXCJjYXBzbG9ja1wiLFxuICAgICAgMjc6IFwiZXNjXCIsXG4gICAgICAzMjogXCJzcGFjZVwiLFxuICAgICAgMzM6IFwicGFnZXVwXCIsXG4gICAgICAzNDogXCJwYWdlZG93blwiLFxuICAgICAgMzU6IFwiZW5kXCIsXG4gICAgICAzNjogXCJob21lXCIsXG4gICAgICAzNzogXCJsZWZ0XCIsXG4gICAgICAzODogXCJ1cFwiLFxuICAgICAgMzk6IFwicmlnaHRcIixcbiAgICAgIDQwOiBcImRvd25cIixcbiAgICAgIDQ1OiBcImluc1wiLFxuICAgICAgNDY6IFwiZGVsXCIsXG4gICAgICA5MTogXCJtZXRhXCIsXG4gICAgICA5MzogXCJtZXRhXCIsXG4gICAgICAyMjQ6IFwibWV0YVwiXG4gICAgfTtcbiAgICB2YXIgX0tFWUNPREVfTUFQID0ge1xuICAgICAgMTA2OiBcIipcIixcbiAgICAgIDEwNzogXCIrXCIsXG4gICAgICAxMDk6IFwiLVwiLFxuICAgICAgMTEwOiBcIi5cIixcbiAgICAgIDExMTogXCIvXCIsXG4gICAgICAxODY6IFwiO1wiLFxuICAgICAgMTg3OiBcIj1cIixcbiAgICAgIDE4ODogXCIsXCIsXG4gICAgICAxODk6IFwiLVwiLFxuICAgICAgMTkwOiBcIi5cIixcbiAgICAgIDE5MTogXCIvXCIsXG4gICAgICAxOTI6IFwiYFwiLFxuICAgICAgMjE5OiBcIltcIixcbiAgICAgIDIyMDogXCJcXFxcXCIsXG4gICAgICAyMjE6IFwiXVwiLFxuICAgICAgMjIyOiBcIidcIlxuICAgIH07XG4gICAgdmFyIF9TSElGVF9NQVAgPSB7XG4gICAgICBcIn5cIjogXCJgXCIsXG4gICAgICBcIiFcIjogXCIxXCIsXG4gICAgICBcIkBcIjogXCIyXCIsXG4gICAgICBcIiNcIjogXCIzXCIsXG4gICAgICAkOiBcIjRcIixcbiAgICAgIFwiJVwiOiBcIjVcIixcbiAgICAgIFwiXlwiOiBcIjZcIixcbiAgICAgIFwiJlwiOiBcIjdcIixcbiAgICAgIFwiKlwiOiBcIjhcIixcbiAgICAgIFwiKFwiOiBcIjlcIixcbiAgICAgIFwiKVwiOiBcIjBcIixcbiAgICAgIF86IFwiLVwiLFxuICAgICAgXCIrXCI6IFwiPVwiLFxuICAgICAgXCI6XCI6IFwiO1wiLFxuICAgICAgJ1wiJzogXCInXCIsXG4gICAgICBcIjxcIjogXCIsXCIsXG4gICAgICBcIj5cIjogXCIuXCIsXG4gICAgICBcIj9cIjogXCIvXCIsXG4gICAgICBcInxcIjogXCJcXFxcXCJcbiAgICB9O1xuICAgIHZhciBfU1BFQ0lBTF9BTElBU0VTID0ge1xuICAgICAgb3B0aW9uOiBcImFsdFwiLFxuICAgICAgY29tbWFuZDogXCJtZXRhXCIsXG4gICAgICByZXR1cm46IFwiZW50ZXJcIixcbiAgICAgIGVzY2FwZTogXCJlc2NcIixcbiAgICAgIHBsdXM6IFwiK1wiLFxuICAgICAgbW9kOiAvTWFjfGlQb2R8aVBob25lfGlQYWQvLnRlc3QobmF2aWdhdG9yLnBsYXRmb3JtKSA/IFwibWV0YVwiIDogXCJjdHJsXCJcbiAgICB9O1xuICAgIHZhciBfUkVWRVJTRV9NQVA7XG4gICAgZm9yICh2YXIgaSA9IDE7IGkgPCAyMDsgKytpKSB7XG4gICAgICBfTUFQWzExMSArIGldID0gXCJmXCIgKyBpO1xuICAgIH1cbiAgICBmb3IgKGkgPSAwOyBpIDw9IDk7ICsraSkge1xuICAgICAgX01BUFtpICsgOTZdID0gaS50b1N0cmluZygpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfYWRkRXZlbnQob2JqZWN0LCB0eXBlLCBjYWxsYmFjaykge1xuICAgICAgaWYgKG9iamVjdC5hZGRFdmVudExpc3RlbmVyKSB7XG4gICAgICAgIG9iamVjdC5hZGRFdmVudExpc3RlbmVyKHR5cGUsIGNhbGxiYWNrLCBmYWxzZSk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIG9iamVjdC5hdHRhY2hFdmVudChcIm9uXCIgKyB0eXBlLCBjYWxsYmFjayk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9jaGFyYWN0ZXJGcm9tRXZlbnQoZSkge1xuICAgICAgaWYgKGUudHlwZSA9PSBcImtleXByZXNzXCIpIHtcbiAgICAgICAgdmFyIGNoYXJhY3RlciA9IFN0cmluZy5mcm9tQ2hhckNvZGUoZS53aGljaCk7XG4gICAgICAgIGlmICghZS5zaGlmdEtleSkge1xuICAgICAgICAgIGNoYXJhY3RlciA9IGNoYXJhY3Rlci50b0xvd2VyQ2FzZSgpO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBjaGFyYWN0ZXI7XG4gICAgICB9XG4gICAgICBpZiAoX01BUFtlLndoaWNoXSkge1xuICAgICAgICByZXR1cm4gX01BUFtlLndoaWNoXTtcbiAgICAgIH1cbiAgICAgIGlmIChfS0VZQ09ERV9NQVBbZS53aGljaF0pIHtcbiAgICAgICAgcmV0dXJuIF9LRVlDT0RFX01BUFtlLndoaWNoXTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBTdHJpbmcuZnJvbUNoYXJDb2RlKGUud2hpY2gpLnRvTG93ZXJDYXNlKCk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9tb2RpZmllcnNNYXRjaChtb2RpZmllcnMxLCBtb2RpZmllcnMyKSB7XG4gICAgICByZXR1cm4gbW9kaWZpZXJzMS5zb3J0KCkuam9pbihcIixcIikgPT09IG1vZGlmaWVyczIuc29ydCgpLmpvaW4oXCIsXCIpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfZXZlbnRNb2RpZmllcnMoZSkge1xuICAgICAgdmFyIG1vZGlmaWVycyA9IFtdO1xuICAgICAgaWYgKGUuc2hpZnRLZXkpIHtcbiAgICAgICAgbW9kaWZpZXJzLnB1c2goXCJzaGlmdFwiKTtcbiAgICAgIH1cbiAgICAgIGlmIChlLmFsdEtleSkge1xuICAgICAgICBtb2RpZmllcnMucHVzaChcImFsdFwiKTtcbiAgICAgIH1cbiAgICAgIGlmIChlLmN0cmxLZXkpIHtcbiAgICAgICAgbW9kaWZpZXJzLnB1c2goXCJjdHJsXCIpO1xuICAgICAgfVxuICAgICAgaWYgKGUubWV0YUtleSkge1xuICAgICAgICBtb2RpZmllcnMucHVzaChcIm1ldGFcIik7XG4gICAgICB9XG4gICAgICByZXR1cm4gbW9kaWZpZXJzO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfcHJldmVudERlZmF1bHQoZSkge1xuICAgICAgaWYgKGUucHJldmVudERlZmF1bHQpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBlLnJldHVyblZhbHVlID0gZmFsc2U7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9zdG9wUHJvcGFnYXRpb24oZSkge1xuICAgICAgaWYgKGUuc3RvcFByb3BhZ2F0aW9uKSB7XG4gICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGUuY2FuY2VsQnViYmxlID0gdHJ1ZTtcbiAgICB9XG4gICAgZnVuY3Rpb24gX2lzTW9kaWZpZXIoa2V5KSB7XG4gICAgICByZXR1cm4ga2V5ID09IFwic2hpZnRcIiB8fCBrZXkgPT0gXCJjdHJsXCIgfHwga2V5ID09IFwiYWx0XCIgfHwga2V5ID09IFwibWV0YVwiO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfZ2V0UmV2ZXJzZU1hcCgpIHtcbiAgICAgIGlmICghX1JFVkVSU0VfTUFQKSB7XG4gICAgICAgIF9SRVZFUlNFX01BUCA9IHt9O1xuICAgICAgICBmb3IgKHZhciBrZXkgaW4gX01BUCkge1xuICAgICAgICAgIGlmIChrZXkgPiA5NSAmJiBrZXkgPCAxMTIpIHtcbiAgICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAoX01BUC5oYXNPd25Qcm9wZXJ0eShrZXkpKSB7XG4gICAgICAgICAgICBfUkVWRVJTRV9NQVBbX01BUFtrZXldXSA9IGtleTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiBfUkVWRVJTRV9NQVA7XG4gICAgfVxuICAgIGZ1bmN0aW9uIF9waWNrQmVzdEFjdGlvbihrZXksIG1vZGlmaWVycywgYWN0aW9uKSB7XG4gICAgICBpZiAoIWFjdGlvbikge1xuICAgICAgICBhY3Rpb24gPSBfZ2V0UmV2ZXJzZU1hcCgpW2tleV0gPyBcImtleWRvd25cIiA6IFwia2V5cHJlc3NcIjtcbiAgICAgIH1cbiAgICAgIGlmIChhY3Rpb24gPT0gXCJrZXlwcmVzc1wiICYmIG1vZGlmaWVycy5sZW5ndGgpIHtcbiAgICAgICAgYWN0aW9uID0gXCJrZXlkb3duXCI7XG4gICAgICB9XG4gICAgICByZXR1cm4gYWN0aW9uO1xuICAgIH1cbiAgICBmdW5jdGlvbiBfa2V5c0Zyb21TdHJpbmcoY29tYmluYXRpb24pIHtcbiAgICAgIGlmIChjb21iaW5hdGlvbiA9PT0gXCIrXCIpIHtcbiAgICAgICAgcmV0dXJuIFtcIitcIl07XG4gICAgICB9XG4gICAgICBjb21iaW5hdGlvbiA9IGNvbWJpbmF0aW9uLnJlcGxhY2UoL1xcK3syfS9nLCBcIitwbHVzXCIpO1xuICAgICAgcmV0dXJuIGNvbWJpbmF0aW9uLnNwbGl0KFwiK1wiKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gX2dldEtleUluZm8oY29tYmluYXRpb24sIGFjdGlvbikge1xuICAgICAgdmFyIGtleXM7XG4gICAgICB2YXIga2V5O1xuICAgICAgdmFyIGkyO1xuICAgICAgdmFyIG1vZGlmaWVycyA9IFtdO1xuICAgICAga2V5cyA9IF9rZXlzRnJvbVN0cmluZyhjb21iaW5hdGlvbik7XG4gICAgICBmb3IgKGkyID0gMDsgaTIgPCBrZXlzLmxlbmd0aDsgKytpMikge1xuICAgICAgICBrZXkgPSBrZXlzW2kyXTtcbiAgICAgICAgaWYgKF9TUEVDSUFMX0FMSUFTRVNba2V5XSkge1xuICAgICAgICAgIGtleSA9IF9TUEVDSUFMX0FMSUFTRVNba2V5XTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoYWN0aW9uICYmIGFjdGlvbiAhPSBcImtleXByZXNzXCIgJiYgX1NISUZUX01BUFtrZXldKSB7XG4gICAgICAgICAga2V5ID0gX1NISUZUX01BUFtrZXldO1xuICAgICAgICAgIG1vZGlmaWVycy5wdXNoKFwic2hpZnRcIik7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKF9pc01vZGlmaWVyKGtleSkpIHtcbiAgICAgICAgICBtb2RpZmllcnMucHVzaChrZXkpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBhY3Rpb24gPSBfcGlja0Jlc3RBY3Rpb24oa2V5LCBtb2RpZmllcnMsIGFjdGlvbik7XG4gICAgICByZXR1cm4ge1xuICAgICAgICBrZXksXG4gICAgICAgIG1vZGlmaWVycyxcbiAgICAgICAgYWN0aW9uXG4gICAgICB9O1xuICAgIH1cbiAgICBmdW5jdGlvbiBfYmVsb25nc1RvKGVsZW1lbnQsIGFuY2VzdG9yKSB7XG4gICAgICBpZiAoZWxlbWVudCA9PT0gbnVsbCB8fCBlbGVtZW50ID09PSBkb2N1bWVudDIpIHtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuICAgICAgaWYgKGVsZW1lbnQgPT09IGFuY2VzdG9yKSB7XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfVxuICAgICAgcmV0dXJuIF9iZWxvbmdzVG8oZWxlbWVudC5wYXJlbnROb2RlLCBhbmNlc3Rvcik7XG4gICAgfVxuICAgIGZ1bmN0aW9uIE1vdXNldHJhcDModGFyZ2V0RWxlbWVudCkge1xuICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgICAgdGFyZ2V0RWxlbWVudCA9IHRhcmdldEVsZW1lbnQgfHwgZG9jdW1lbnQyO1xuICAgICAgaWYgKCEoc2VsZiBpbnN0YW5jZW9mIE1vdXNldHJhcDMpKSB7XG4gICAgICAgIHJldHVybiBuZXcgTW91c2V0cmFwMyh0YXJnZXRFbGVtZW50KTtcbiAgICAgIH1cbiAgICAgIHNlbGYudGFyZ2V0ID0gdGFyZ2V0RWxlbWVudDtcbiAgICAgIHNlbGYuX2NhbGxiYWNrcyA9IHt9O1xuICAgICAgc2VsZi5fZGlyZWN0TWFwID0ge307XG4gICAgICB2YXIgX3NlcXVlbmNlTGV2ZWxzID0ge307XG4gICAgICB2YXIgX3Jlc2V0VGltZXI7XG4gICAgICB2YXIgX2lnbm9yZU5leHRLZXl1cCA9IGZhbHNlO1xuICAgICAgdmFyIF9pZ25vcmVOZXh0S2V5cHJlc3MgPSBmYWxzZTtcbiAgICAgIHZhciBfbmV4dEV4cGVjdGVkQWN0aW9uID0gZmFsc2U7XG4gICAgICBmdW5jdGlvbiBfcmVzZXRTZXF1ZW5jZXMoZG9Ob3RSZXNldCkge1xuICAgICAgICBkb05vdFJlc2V0ID0gZG9Ob3RSZXNldCB8fCB7fTtcbiAgICAgICAgdmFyIGFjdGl2ZVNlcXVlbmNlcyA9IGZhbHNlLCBrZXk7XG4gICAgICAgIGZvciAoa2V5IGluIF9zZXF1ZW5jZUxldmVscykge1xuICAgICAgICAgIGlmIChkb05vdFJlc2V0W2tleV0pIHtcbiAgICAgICAgICAgIGFjdGl2ZVNlcXVlbmNlcyA9IHRydWU7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICB9XG4gICAgICAgICAgX3NlcXVlbmNlTGV2ZWxzW2tleV0gPSAwO1xuICAgICAgICB9XG4gICAgICAgIGlmICghYWN0aXZlU2VxdWVuY2VzKSB7XG4gICAgICAgICAgX25leHRFeHBlY3RlZEFjdGlvbiA9IGZhbHNlO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBfZ2V0TWF0Y2hlcyhjaGFyYWN0ZXIsIG1vZGlmaWVycywgZSwgc2VxdWVuY2VOYW1lLCBjb21iaW5hdGlvbiwgbGV2ZWwpIHtcbiAgICAgICAgdmFyIGkyO1xuICAgICAgICB2YXIgY2FsbGJhY2s7XG4gICAgICAgIHZhciBtYXRjaGVzID0gW107XG4gICAgICAgIHZhciBhY3Rpb24gPSBlLnR5cGU7XG4gICAgICAgIGlmICghc2VsZi5fY2FsbGJhY2tzW2NoYXJhY3Rlcl0pIHtcbiAgICAgICAgICByZXR1cm4gW107XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGFjdGlvbiA9PSBcImtleXVwXCIgJiYgX2lzTW9kaWZpZXIoY2hhcmFjdGVyKSkge1xuICAgICAgICAgIG1vZGlmaWVycyA9IFtjaGFyYWN0ZXJdO1xuICAgICAgICB9XG4gICAgICAgIGZvciAoaTIgPSAwOyBpMiA8IHNlbGYuX2NhbGxiYWNrc1tjaGFyYWN0ZXJdLmxlbmd0aDsgKytpMikge1xuICAgICAgICAgIGNhbGxiYWNrID0gc2VsZi5fY2FsbGJhY2tzW2NoYXJhY3Rlcl1baTJdO1xuICAgICAgICAgIGlmICghc2VxdWVuY2VOYW1lICYmIGNhbGxiYWNrLnNlcSAmJiBfc2VxdWVuY2VMZXZlbHNbY2FsbGJhY2suc2VxXSAhPSBjYWxsYmFjay5sZXZlbCkge1xuICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmIChhY3Rpb24gIT0gY2FsbGJhY2suYWN0aW9uKSB7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKGFjdGlvbiA9PSBcImtleXByZXNzXCIgJiYgIWUubWV0YUtleSAmJiAhZS5jdHJsS2V5IHx8IF9tb2RpZmllcnNNYXRjaChtb2RpZmllcnMsIGNhbGxiYWNrLm1vZGlmaWVycykpIHtcbiAgICAgICAgICAgIHZhciBkZWxldGVDb21ibyA9ICFzZXF1ZW5jZU5hbWUgJiYgY2FsbGJhY2suY29tYm8gPT0gY29tYmluYXRpb247XG4gICAgICAgICAgICB2YXIgZGVsZXRlU2VxdWVuY2UgPSBzZXF1ZW5jZU5hbWUgJiYgY2FsbGJhY2suc2VxID09IHNlcXVlbmNlTmFtZSAmJiBjYWxsYmFjay5sZXZlbCA9PSBsZXZlbDtcbiAgICAgICAgICAgIGlmIChkZWxldGVDb21ibyB8fCBkZWxldGVTZXF1ZW5jZSkge1xuICAgICAgICAgICAgICBzZWxmLl9jYWxsYmFja3NbY2hhcmFjdGVyXS5zcGxpY2UoaTIsIDEpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgbWF0Y2hlcy5wdXNoKGNhbGxiYWNrKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIG1hdGNoZXM7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBfZmlyZUNhbGxiYWNrKGNhbGxiYWNrLCBlLCBjb21ibywgc2VxdWVuY2UpIHtcbiAgICAgICAgaWYgKHNlbGYuc3RvcENhbGxiYWNrKGUsIGUudGFyZ2V0IHx8IGUuc3JjRWxlbWVudCwgY29tYm8sIHNlcXVlbmNlKSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBpZiAoY2FsbGJhY2soZSwgY29tYm8pID09PSBmYWxzZSkge1xuICAgICAgICAgIF9wcmV2ZW50RGVmYXVsdChlKTtcbiAgICAgICAgICBfc3RvcFByb3BhZ2F0aW9uKGUpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBzZWxmLl9oYW5kbGVLZXkgPSBmdW5jdGlvbihjaGFyYWN0ZXIsIG1vZGlmaWVycywgZSkge1xuICAgICAgICB2YXIgY2FsbGJhY2tzID0gX2dldE1hdGNoZXMoY2hhcmFjdGVyLCBtb2RpZmllcnMsIGUpO1xuICAgICAgICB2YXIgaTI7XG4gICAgICAgIHZhciBkb05vdFJlc2V0ID0ge307XG4gICAgICAgIHZhciBtYXhMZXZlbCA9IDA7XG4gICAgICAgIHZhciBwcm9jZXNzZWRTZXF1ZW5jZUNhbGxiYWNrID0gZmFsc2U7XG4gICAgICAgIGZvciAoaTIgPSAwOyBpMiA8IGNhbGxiYWNrcy5sZW5ndGg7ICsraTIpIHtcbiAgICAgICAgICBpZiAoY2FsbGJhY2tzW2kyXS5zZXEpIHtcbiAgICAgICAgICAgIG1heExldmVsID0gTWF0aC5tYXgobWF4TGV2ZWwsIGNhbGxiYWNrc1tpMl0ubGV2ZWwpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICBmb3IgKGkyID0gMDsgaTIgPCBjYWxsYmFja3MubGVuZ3RoOyArK2kyKSB7XG4gICAgICAgICAgaWYgKGNhbGxiYWNrc1tpMl0uc2VxKSB7XG4gICAgICAgICAgICBpZiAoY2FsbGJhY2tzW2kyXS5sZXZlbCAhPSBtYXhMZXZlbCkge1xuICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHByb2Nlc3NlZFNlcXVlbmNlQ2FsbGJhY2sgPSB0cnVlO1xuICAgICAgICAgICAgZG9Ob3RSZXNldFtjYWxsYmFja3NbaTJdLnNlcV0gPSAxO1xuICAgICAgICAgICAgX2ZpcmVDYWxsYmFjayhjYWxsYmFja3NbaTJdLmNhbGxiYWNrLCBlLCBjYWxsYmFja3NbaTJdLmNvbWJvLCBjYWxsYmFja3NbaTJdLnNlcSk7XG4gICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKCFwcm9jZXNzZWRTZXF1ZW5jZUNhbGxiYWNrKSB7XG4gICAgICAgICAgICBfZmlyZUNhbGxiYWNrKGNhbGxiYWNrc1tpMl0uY2FsbGJhY2ssIGUsIGNhbGxiYWNrc1tpMl0uY29tYm8pO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICB2YXIgaWdub3JlVGhpc0tleXByZXNzID0gZS50eXBlID09IFwia2V5cHJlc3NcIiAmJiBfaWdub3JlTmV4dEtleXByZXNzO1xuICAgICAgICBpZiAoZS50eXBlID09IF9uZXh0RXhwZWN0ZWRBY3Rpb24gJiYgIV9pc01vZGlmaWVyKGNoYXJhY3RlcikgJiYgIWlnbm9yZVRoaXNLZXlwcmVzcykge1xuICAgICAgICAgIF9yZXNldFNlcXVlbmNlcyhkb05vdFJlc2V0KTtcbiAgICAgICAgfVxuICAgICAgICBfaWdub3JlTmV4dEtleXByZXNzID0gcHJvY2Vzc2VkU2VxdWVuY2VDYWxsYmFjayAmJiBlLnR5cGUgPT0gXCJrZXlkb3duXCI7XG4gICAgICB9O1xuICAgICAgZnVuY3Rpb24gX2hhbmRsZUtleUV2ZW50KGUpIHtcbiAgICAgICAgaWYgKHR5cGVvZiBlLndoaWNoICE9PSBcIm51bWJlclwiKSB7XG4gICAgICAgICAgZS53aGljaCA9IGUua2V5Q29kZTtcbiAgICAgICAgfVxuICAgICAgICB2YXIgY2hhcmFjdGVyID0gX2NoYXJhY3RlckZyb21FdmVudChlKTtcbiAgICAgICAgaWYgKCFjaGFyYWN0ZXIpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGUudHlwZSA9PSBcImtleXVwXCIgJiYgX2lnbm9yZU5leHRLZXl1cCA9PT0gY2hhcmFjdGVyKSB7XG4gICAgICAgICAgX2lnbm9yZU5leHRLZXl1cCA9IGZhbHNlO1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBzZWxmLmhhbmRsZUtleShjaGFyYWN0ZXIsIF9ldmVudE1vZGlmaWVycyhlKSwgZSk7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBfcmVzZXRTZXF1ZW5jZVRpbWVyKCkge1xuICAgICAgICBjbGVhclRpbWVvdXQoX3Jlc2V0VGltZXIpO1xuICAgICAgICBfcmVzZXRUaW1lciA9IHNldFRpbWVvdXQoX3Jlc2V0U2VxdWVuY2VzLCAxZTMpO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gX2JpbmRTZXF1ZW5jZShjb21ibywga2V5cywgY2FsbGJhY2ssIGFjdGlvbikge1xuICAgICAgICBfc2VxdWVuY2VMZXZlbHNbY29tYm9dID0gMDtcbiAgICAgICAgZnVuY3Rpb24gX2luY3JlYXNlU2VxdWVuY2UobmV4dEFjdGlvbikge1xuICAgICAgICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIF9uZXh0RXhwZWN0ZWRBY3Rpb24gPSBuZXh0QWN0aW9uO1xuICAgICAgICAgICAgKytfc2VxdWVuY2VMZXZlbHNbY29tYm9dO1xuICAgICAgICAgICAgX3Jlc2V0U2VxdWVuY2VUaW1lcigpO1xuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgZnVuY3Rpb24gX2NhbGxiYWNrQW5kUmVzZXQoZSkge1xuICAgICAgICAgIF9maXJlQ2FsbGJhY2soY2FsbGJhY2ssIGUsIGNvbWJvKTtcbiAgICAgICAgICBpZiAoYWN0aW9uICE9PSBcImtleXVwXCIpIHtcbiAgICAgICAgICAgIF9pZ25vcmVOZXh0S2V5dXAgPSBfY2hhcmFjdGVyRnJvbUV2ZW50KGUpO1xuICAgICAgICAgIH1cbiAgICAgICAgICBzZXRUaW1lb3V0KF9yZXNldFNlcXVlbmNlcywgMTApO1xuICAgICAgICB9XG4gICAgICAgIGZvciAodmFyIGkyID0gMDsgaTIgPCBrZXlzLmxlbmd0aDsgKytpMikge1xuICAgICAgICAgIHZhciBpc0ZpbmFsID0gaTIgKyAxID09PSBrZXlzLmxlbmd0aDtcbiAgICAgICAgICB2YXIgd3JhcHBlZENhbGxiYWNrID0gaXNGaW5hbCA/IF9jYWxsYmFja0FuZFJlc2V0IDogX2luY3JlYXNlU2VxdWVuY2UoYWN0aW9uIHx8IF9nZXRLZXlJbmZvKGtleXNbaTIgKyAxXSkuYWN0aW9uKTtcbiAgICAgICAgICBfYmluZFNpbmdsZShrZXlzW2kyXSwgd3JhcHBlZENhbGxiYWNrLCBhY3Rpb24sIGNvbWJvLCBpMik7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIF9iaW5kU2luZ2xlKGNvbWJpbmF0aW9uLCBjYWxsYmFjaywgYWN0aW9uLCBzZXF1ZW5jZU5hbWUsIGxldmVsKSB7XG4gICAgICAgIHNlbGYuX2RpcmVjdE1hcFtjb21iaW5hdGlvbiArIFwiOlwiICsgYWN0aW9uXSA9IGNhbGxiYWNrO1xuICAgICAgICBjb21iaW5hdGlvbiA9IGNvbWJpbmF0aW9uLnJlcGxhY2UoL1xccysvZywgXCIgXCIpO1xuICAgICAgICB2YXIgc2VxdWVuY2UgPSBjb21iaW5hdGlvbi5zcGxpdChcIiBcIik7XG4gICAgICAgIHZhciBpbmZvO1xuICAgICAgICBpZiAoc2VxdWVuY2UubGVuZ3RoID4gMSkge1xuICAgICAgICAgIF9iaW5kU2VxdWVuY2UoY29tYmluYXRpb24sIHNlcXVlbmNlLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgaW5mbyA9IF9nZXRLZXlJbmZvKGNvbWJpbmF0aW9uLCBhY3Rpb24pO1xuICAgICAgICBzZWxmLl9jYWxsYmFja3NbaW5mby5rZXldID0gc2VsZi5fY2FsbGJhY2tzW2luZm8ua2V5XSB8fCBbXTtcbiAgICAgICAgX2dldE1hdGNoZXMoaW5mby5rZXksIGluZm8ubW9kaWZpZXJzLCB7dHlwZTogaW5mby5hY3Rpb259LCBzZXF1ZW5jZU5hbWUsIGNvbWJpbmF0aW9uLCBsZXZlbCk7XG4gICAgICAgIHNlbGYuX2NhbGxiYWNrc1tpbmZvLmtleV1bc2VxdWVuY2VOYW1lID8gXCJ1bnNoaWZ0XCIgOiBcInB1c2hcIl0oe1xuICAgICAgICAgIGNhbGxiYWNrLFxuICAgICAgICAgIG1vZGlmaWVyczogaW5mby5tb2RpZmllcnMsXG4gICAgICAgICAgYWN0aW9uOiBpbmZvLmFjdGlvbixcbiAgICAgICAgICBzZXE6IHNlcXVlbmNlTmFtZSxcbiAgICAgICAgICBsZXZlbCxcbiAgICAgICAgICBjb21ibzogY29tYmluYXRpb25cbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBzZWxmLl9iaW5kTXVsdGlwbGUgPSBmdW5jdGlvbihjb21iaW5hdGlvbnMsIGNhbGxiYWNrLCBhY3Rpb24pIHtcbiAgICAgICAgZm9yICh2YXIgaTIgPSAwOyBpMiA8IGNvbWJpbmF0aW9ucy5sZW5ndGg7ICsraTIpIHtcbiAgICAgICAgICBfYmluZFNpbmdsZShjb21iaW5hdGlvbnNbaTJdLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICAgIF9hZGRFdmVudCh0YXJnZXRFbGVtZW50LCBcImtleXByZXNzXCIsIF9oYW5kbGVLZXlFdmVudCk7XG4gICAgICBfYWRkRXZlbnQodGFyZ2V0RWxlbWVudCwgXCJrZXlkb3duXCIsIF9oYW5kbGVLZXlFdmVudCk7XG4gICAgICBfYWRkRXZlbnQodGFyZ2V0RWxlbWVudCwgXCJrZXl1cFwiLCBfaGFuZGxlS2V5RXZlbnQpO1xuICAgIH1cbiAgICBNb3VzZXRyYXAzLnByb3RvdHlwZS5iaW5kID0gZnVuY3Rpb24oa2V5cywgY2FsbGJhY2ssIGFjdGlvbikge1xuICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgICAga2V5cyA9IGtleXMgaW5zdGFuY2VvZiBBcnJheSA/IGtleXMgOiBba2V5c107XG4gICAgICBzZWxmLl9iaW5kTXVsdGlwbGUuY2FsbChzZWxmLCBrZXlzLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICAgIHJldHVybiBzZWxmO1xuICAgIH07XG4gICAgTW91c2V0cmFwMy5wcm90b3R5cGUudW5iaW5kID0gZnVuY3Rpb24oa2V5cywgYWN0aW9uKSB7XG4gICAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgICByZXR1cm4gc2VsZi5iaW5kLmNhbGwoc2VsZiwga2V5cywgZnVuY3Rpb24oKSB7XG4gICAgICB9LCBhY3Rpb24pO1xuICAgIH07XG4gICAgTW91c2V0cmFwMy5wcm90b3R5cGUudHJpZ2dlciA9IGZ1bmN0aW9uKGtleXMsIGFjdGlvbikge1xuICAgICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgICAgaWYgKHNlbGYuX2RpcmVjdE1hcFtrZXlzICsgXCI6XCIgKyBhY3Rpb25dKSB7XG4gICAgICAgIHNlbGYuX2RpcmVjdE1hcFtrZXlzICsgXCI6XCIgKyBhY3Rpb25dKHt9LCBrZXlzKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBzZWxmO1xuICAgIH07XG4gICAgTW91c2V0cmFwMy5wcm90b3R5cGUucmVzZXQgPSBmdW5jdGlvbigpIHtcbiAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgIHNlbGYuX2NhbGxiYWNrcyA9IHt9O1xuICAgICAgc2VsZi5fZGlyZWN0TWFwID0ge307XG4gICAgICByZXR1cm4gc2VsZjtcbiAgICB9O1xuICAgIE1vdXNldHJhcDMucHJvdG90eXBlLnN0b3BDYWxsYmFjayA9IGZ1bmN0aW9uKGUsIGVsZW1lbnQpIHtcbiAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgIGlmICgoXCIgXCIgKyBlbGVtZW50LmNsYXNzTmFtZSArIFwiIFwiKS5pbmRleE9mKFwiIG1vdXNldHJhcCBcIikgPiAtMSkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG4gICAgICBpZiAoX2JlbG9uZ3NUbyhlbGVtZW50LCBzZWxmLnRhcmdldCkpIHtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuICAgICAgaWYgKFwiY29tcG9zZWRQYXRoXCIgaW4gZSAmJiB0eXBlb2YgZS5jb21wb3NlZFBhdGggPT09IFwiZnVuY3Rpb25cIikge1xuICAgICAgICB2YXIgaW5pdGlhbEV2ZW50VGFyZ2V0ID0gZS5jb21wb3NlZFBhdGgoKVswXTtcbiAgICAgICAgaWYgKGluaXRpYWxFdmVudFRhcmdldCAhPT0gZS50YXJnZXQpIHtcbiAgICAgICAgICBlbGVtZW50ID0gaW5pdGlhbEV2ZW50VGFyZ2V0O1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gZWxlbWVudC50YWdOYW1lID09IFwiSU5QVVRcIiB8fCBlbGVtZW50LnRhZ05hbWUgPT0gXCJTRUxFQ1RcIiB8fCBlbGVtZW50LnRhZ05hbWUgPT0gXCJURVhUQVJFQVwiIHx8IGVsZW1lbnQuaXNDb250ZW50RWRpdGFibGU7XG4gICAgfTtcbiAgICBNb3VzZXRyYXAzLnByb3RvdHlwZS5oYW5kbGVLZXkgPSBmdW5jdGlvbigpIHtcbiAgICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAgIHJldHVybiBzZWxmLl9oYW5kbGVLZXkuYXBwbHkoc2VsZiwgYXJndW1lbnRzKTtcbiAgICB9O1xuICAgIE1vdXNldHJhcDMuYWRkS2V5Y29kZXMgPSBmdW5jdGlvbihvYmplY3QpIHtcbiAgICAgIGZvciAodmFyIGtleSBpbiBvYmplY3QpIHtcbiAgICAgICAgaWYgKG9iamVjdC5oYXNPd25Qcm9wZXJ0eShrZXkpKSB7XG4gICAgICAgICAgX01BUFtrZXldID0gb2JqZWN0W2tleV07XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIF9SRVZFUlNFX01BUCA9IG51bGw7XG4gICAgfTtcbiAgICBNb3VzZXRyYXAzLmluaXQgPSBmdW5jdGlvbigpIHtcbiAgICAgIHZhciBkb2N1bWVudE1vdXNldHJhcCA9IE1vdXNldHJhcDMoZG9jdW1lbnQyKTtcbiAgICAgIGZvciAodmFyIG1ldGhvZCBpbiBkb2N1bWVudE1vdXNldHJhcCkge1xuICAgICAgICBpZiAobWV0aG9kLmNoYXJBdCgwKSAhPT0gXCJfXCIpIHtcbiAgICAgICAgICBNb3VzZXRyYXAzW21ldGhvZF0gPSBmdW5jdGlvbihtZXRob2QyKSB7XG4gICAgICAgICAgICByZXR1cm4gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgIHJldHVybiBkb2N1bWVudE1vdXNldHJhcFttZXRob2QyXS5hcHBseShkb2N1bWVudE1vdXNldHJhcCwgYXJndW1lbnRzKTtcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgfShtZXRob2QpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfTtcbiAgICBNb3VzZXRyYXAzLmluaXQoKTtcbiAgICB3aW5kb3cyLk1vdXNldHJhcCA9IE1vdXNldHJhcDM7XG4gICAgaWYgKHR5cGVvZiBtb2R1bGUgIT09IFwidW5kZWZpbmVkXCIgJiYgbW9kdWxlLmV4cG9ydHMpIHtcbiAgICAgIG1vZHVsZS5leHBvcnRzID0gTW91c2V0cmFwMztcbiAgICB9XG4gICAgaWYgKHR5cGVvZiBkZWZpbmUgPT09IFwiZnVuY3Rpb25cIiAmJiBkZWZpbmUuYW1kKSB7XG4gICAgICBkZWZpbmUoZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiBNb3VzZXRyYXAzO1xuICAgICAgfSk7XG4gICAgfVxuICB9KSh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93IDogbnVsbCwgdHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IGRvY3VtZW50IDogbnVsbCk7XG59KTtcblxuLy8gc3JjL2luZGV4LmpzXG52YXIgaW1wb3J0X21vdXNldHJhcCA9IF9fdG9Nb2R1bGUocmVxdWlyZV9tb3VzZXRyYXAoKSk7XG5cbi8vIG5vZGVfbW9kdWxlcy9tb3VzZXRyYXAvcGx1Z2lucy9nbG9iYWwtYmluZC9tb3VzZXRyYXAtZ2xvYmFsLWJpbmQuanNcbihmdW5jdGlvbihNb3VzZXRyYXAzKSB7XG4gIGlmICghTW91c2V0cmFwMykge1xuICAgIHJldHVybjtcbiAgfVxuICB2YXIgX2dsb2JhbENhbGxiYWNrcyA9IHt9O1xuICB2YXIgX29yaWdpbmFsU3RvcENhbGxiYWNrID0gTW91c2V0cmFwMy5wcm90b3R5cGUuc3RvcENhbGxiYWNrO1xuICBNb3VzZXRyYXAzLnByb3RvdHlwZS5zdG9wQ2FsbGJhY2sgPSBmdW5jdGlvbihlLCBlbGVtZW50LCBjb21ibywgc2VxdWVuY2UpIHtcbiAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgaWYgKHNlbGYucGF1c2VkKSB7XG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9XG4gICAgaWYgKF9nbG9iYWxDYWxsYmFja3NbY29tYm9dIHx8IF9nbG9iYWxDYWxsYmFja3Nbc2VxdWVuY2VdKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICAgIHJldHVybiBfb3JpZ2luYWxTdG9wQ2FsbGJhY2suY2FsbChzZWxmLCBlLCBlbGVtZW50LCBjb21ibyk7XG4gIH07XG4gIE1vdXNldHJhcDMucHJvdG90eXBlLmJpbmRHbG9iYWwgPSBmdW5jdGlvbihrZXlzLCBjYWxsYmFjaywgYWN0aW9uKSB7XG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgIHNlbGYuYmluZChrZXlzLCBjYWxsYmFjaywgYWN0aW9uKTtcbiAgICBpZiAoa2V5cyBpbnN0YW5jZW9mIEFycmF5KSB7XG4gICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGtleXMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgX2dsb2JhbENhbGxiYWNrc1trZXlzW2ldXSA9IHRydWU7XG4gICAgICB9XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIF9nbG9iYWxDYWxsYmFja3Nba2V5c10gPSB0cnVlO1xuICB9O1xuICBNb3VzZXRyYXAzLmluaXQoKTtcbn0pKHR5cGVvZiBNb3VzZXRyYXAgIT09IFwidW5kZWZpbmVkXCIgPyBNb3VzZXRyYXAgOiB2b2lkIDApO1xuXG4vLyBzcmMvaW5kZXguanNcbnZhciBzcmNfZGVmYXVsdCA9IChBbHBpbmUpID0+IHtcbiAgQWxwaW5lLmRpcmVjdGl2ZShcIm1vdXNldHJhcFwiLCAoZWwsIHttb2RpZmllcnMsIGV4cHJlc3Npb259LCB7ZXZhbHVhdGV9KSA9PiB7XG4gICAgY29uc3QgYWN0aW9uID0gKCkgPT4gZXhwcmVzc2lvbiA/IGV2YWx1YXRlKGV4cHJlc3Npb24pIDogZWwuY2xpY2soKTtcbiAgICBtb2RpZmllcnMgPSBtb2RpZmllcnMubWFwKChtb2RpZmllcikgPT4gbW9kaWZpZXIucmVwbGFjZShcIi1cIiwgXCIrXCIpKTtcbiAgICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiZ2xvYmFsXCIpKSB7XG4gICAgICBtb2RpZmllcnMgPSBtb2RpZmllcnMuZmlsdGVyKChtb2RpZmllcikgPT4gbW9kaWZpZXIgIT09IFwiZ2xvYmFsXCIpO1xuICAgICAgaW1wb3J0X21vdXNldHJhcC5kZWZhdWx0LmJpbmRHbG9iYWwobW9kaWZpZXJzLCAoJGV2ZW50KSA9PiB7XG4gICAgICAgICRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBhY3Rpb24oKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICBpbXBvcnRfbW91c2V0cmFwLmRlZmF1bHQuYmluZChtb2RpZmllcnMsICgkZXZlbnQpID0+IHtcbiAgICAgICRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgYWN0aW9uKCk7XG4gICAgfSk7XG4gIH0pO1xufTtcblxuLy8gYnVpbGRzL21vZHVsZS5qc1xudmFyIG1vZHVsZV9kZWZhdWx0ID0gc3JjX2RlZmF1bHQ7XG5leHBvcnQge1xuICBtb2R1bGVfZGVmYXVsdCBhcyBkZWZhdWx0XG59O1xuIiwgIi8vIHBhY2thZ2VzL3BlcnNpc3Qvc3JjL2luZGV4LmpzXG5mdW5jdGlvbiBzcmNfZGVmYXVsdChBbHBpbmUpIHtcbiAgbGV0IHBlcnNpc3QgPSAoKSA9PiB7XG4gICAgbGV0IGFsaWFzO1xuICAgIGxldCBzdG9yYWdlID0gbG9jYWxTdG9yYWdlO1xuICAgIHJldHVybiBBbHBpbmUuaW50ZXJjZXB0b3IoKGluaXRpYWxWYWx1ZSwgZ2V0dGVyLCBzZXR0ZXIsIHBhdGgsIGtleSkgPT4ge1xuICAgICAgbGV0IGxvb2t1cCA9IGFsaWFzIHx8IGBfeF8ke3BhdGh9YDtcbiAgICAgIGxldCBpbml0aWFsID0gc3RvcmFnZUhhcyhsb29rdXAsIHN0b3JhZ2UpID8gc3RvcmFnZUdldChsb29rdXAsIHN0b3JhZ2UpIDogaW5pdGlhbFZhbHVlO1xuICAgICAgc2V0dGVyKGluaXRpYWwpO1xuICAgICAgQWxwaW5lLmVmZmVjdCgoKSA9PiB7XG4gICAgICAgIGxldCB2YWx1ZSA9IGdldHRlcigpO1xuICAgICAgICBzdG9yYWdlU2V0KGxvb2t1cCwgdmFsdWUsIHN0b3JhZ2UpO1xuICAgICAgICBzZXR0ZXIodmFsdWUpO1xuICAgICAgfSk7XG4gICAgICByZXR1cm4gaW5pdGlhbDtcbiAgICB9LCAoZnVuYykgPT4ge1xuICAgICAgZnVuYy5hcyA9IChrZXkpID0+IHtcbiAgICAgICAgYWxpYXMgPSBrZXk7XG4gICAgICAgIHJldHVybiBmdW5jO1xuICAgICAgfSwgZnVuYy51c2luZyA9ICh0YXJnZXQpID0+IHtcbiAgICAgICAgc3RvcmFnZSA9IHRhcmdldDtcbiAgICAgICAgcmV0dXJuIGZ1bmM7XG4gICAgICB9O1xuICAgIH0pO1xuICB9O1xuICBPYmplY3QuZGVmaW5lUHJvcGVydHkoQWxwaW5lLCBcIiRwZXJzaXN0XCIsIHtnZXQ6ICgpID0+IHBlcnNpc3QoKX0pO1xuICBBbHBpbmUubWFnaWMoXCJwZXJzaXN0XCIsIHBlcnNpc3QpO1xuICBBbHBpbmUucGVyc2lzdCA9IChrZXksIHtnZXQsIHNldH0sIHN0b3JhZ2UgPSBsb2NhbFN0b3JhZ2UpID0+IHtcbiAgICBsZXQgaW5pdGlhbCA9IHN0b3JhZ2VIYXMoa2V5LCBzdG9yYWdlKSA/IHN0b3JhZ2VHZXQoa2V5LCBzdG9yYWdlKSA6IGdldCgpO1xuICAgIHNldChpbml0aWFsKTtcbiAgICBBbHBpbmUuZWZmZWN0KCgpID0+IHtcbiAgICAgIGxldCB2YWx1ZSA9IGdldCgpO1xuICAgICAgc3RvcmFnZVNldChrZXksIHZhbHVlLCBzdG9yYWdlKTtcbiAgICAgIHNldCh2YWx1ZSk7XG4gICAgfSk7XG4gIH07XG59XG5mdW5jdGlvbiBzdG9yYWdlSGFzKGtleSwgc3RvcmFnZSkge1xuICByZXR1cm4gc3RvcmFnZS5nZXRJdGVtKGtleSkgIT09IG51bGw7XG59XG5mdW5jdGlvbiBzdG9yYWdlR2V0KGtleSwgc3RvcmFnZSkge1xuICByZXR1cm4gSlNPTi5wYXJzZShzdG9yYWdlLmdldEl0ZW0oa2V5LCBzdG9yYWdlKSk7XG59XG5mdW5jdGlvbiBzdG9yYWdlU2V0KGtleSwgdmFsdWUsIHN0b3JhZ2UpIHtcbiAgc3RvcmFnZS5zZXRJdGVtKGtleSwgSlNPTi5zdHJpbmdpZnkodmFsdWUpKTtcbn1cblxuLy8gcGFja2FnZXMvcGVyc2lzdC9idWlsZHMvbW9kdWxlLmpzXG52YXIgbW9kdWxlX2RlZmF1bHQgPSBzcmNfZGVmYXVsdDtcbmV4cG9ydCB7XG4gIG1vZHVsZV9kZWZhdWx0IGFzIGRlZmF1bHRcbn07XG4iLCAidmFyIF9fY3JlYXRlID0gT2JqZWN0LmNyZWF0ZTtcbnZhciBfX2RlZlByb3AgPSBPYmplY3QuZGVmaW5lUHJvcGVydHk7XG52YXIgX19nZXRQcm90b09mID0gT2JqZWN0LmdldFByb3RvdHlwZU9mO1xudmFyIF9faGFzT3duUHJvcCA9IE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHk7XG52YXIgX19nZXRPd25Qcm9wTmFtZXMgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcztcbnZhciBfX2dldE93blByb3BEZXNjID0gT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcjtcbnZhciBfX21hcmtBc01vZHVsZSA9ICh0YXJnZXQpID0+IF9fZGVmUHJvcCh0YXJnZXQsIFwiX19lc01vZHVsZVwiLCB7dmFsdWU6IHRydWV9KTtcbnZhciBfX2NvbW1vbkpTID0gKGNhbGxiYWNrLCBtb2R1bGUpID0+ICgpID0+IHtcbiAgaWYgKCFtb2R1bGUpIHtcbiAgICBtb2R1bGUgPSB7ZXhwb3J0czoge319O1xuICAgIGNhbGxiYWNrKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUpO1xuICB9XG4gIHJldHVybiBtb2R1bGUuZXhwb3J0cztcbn07XG52YXIgX19leHBvcnRTdGFyID0gKHRhcmdldCwgbW9kdWxlLCBkZXNjKSA9PiB7XG4gIGlmIChtb2R1bGUgJiYgdHlwZW9mIG1vZHVsZSA9PT0gXCJvYmplY3RcIiB8fCB0eXBlb2YgbW9kdWxlID09PSBcImZ1bmN0aW9uXCIpIHtcbiAgICBmb3IgKGxldCBrZXkgb2YgX19nZXRPd25Qcm9wTmFtZXMobW9kdWxlKSlcbiAgICAgIGlmICghX19oYXNPd25Qcm9wLmNhbGwodGFyZ2V0LCBrZXkpICYmIGtleSAhPT0gXCJkZWZhdWx0XCIpXG4gICAgICAgIF9fZGVmUHJvcCh0YXJnZXQsIGtleSwge2dldDogKCkgPT4gbW9kdWxlW2tleV0sIGVudW1lcmFibGU6ICEoZGVzYyA9IF9fZ2V0T3duUHJvcERlc2MobW9kdWxlLCBrZXkpKSB8fCBkZXNjLmVudW1lcmFibGV9KTtcbiAgfVxuICByZXR1cm4gdGFyZ2V0O1xufTtcbnZhciBfX3RvTW9kdWxlID0gKG1vZHVsZSkgPT4ge1xuICByZXR1cm4gX19leHBvcnRTdGFyKF9fbWFya0FzTW9kdWxlKF9fZGVmUHJvcChtb2R1bGUgIT0gbnVsbCA/IF9fY3JlYXRlKF9fZ2V0UHJvdG9PZihtb2R1bGUpKSA6IHt9LCBcImRlZmF1bHRcIiwgbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlICYmIFwiZGVmYXVsdFwiIGluIG1vZHVsZSA/IHtnZXQ6ICgpID0+IG1vZHVsZS5kZWZhdWx0LCBlbnVtZXJhYmxlOiB0cnVlfSA6IHt2YWx1ZTogbW9kdWxlLCBlbnVtZXJhYmxlOiB0cnVlfSkpLCBtb2R1bGUpO1xufTtcblxuLy8gbm9kZV9tb2R1bGVzL0Bwb3BwZXJqcy9jb3JlL2Rpc3QvY2pzL3BvcHBlci5qc1xudmFyIHJlcXVpcmVfcG9wcGVyID0gX19jb21tb25KUygoZXhwb3J0cykgPT4ge1xuICBcInVzZSBzdHJpY3RcIjtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCB7dmFsdWU6IHRydWV9KTtcbiAgZnVuY3Rpb24gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGVsZW1lbnQpIHtcbiAgICB2YXIgcmVjdCA9IGVsZW1lbnQuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7XG4gICAgcmV0dXJuIHtcbiAgICAgIHdpZHRoOiByZWN0LndpZHRoLFxuICAgICAgaGVpZ2h0OiByZWN0LmhlaWdodCxcbiAgICAgIHRvcDogcmVjdC50b3AsXG4gICAgICByaWdodDogcmVjdC5yaWdodCxcbiAgICAgIGJvdHRvbTogcmVjdC5ib3R0b20sXG4gICAgICBsZWZ0OiByZWN0LmxlZnQsXG4gICAgICB4OiByZWN0LmxlZnQsXG4gICAgICB5OiByZWN0LnRvcFxuICAgIH07XG4gIH1cbiAgZnVuY3Rpb24gZ2V0V2luZG93KG5vZGUpIHtcbiAgICBpZiAobm9kZSA9PSBudWxsKSB7XG4gICAgICByZXR1cm4gd2luZG93O1xuICAgIH1cbiAgICBpZiAobm9kZS50b1N0cmluZygpICE9PSBcIltvYmplY3QgV2luZG93XVwiKSB7XG4gICAgICB2YXIgb3duZXJEb2N1bWVudCA9IG5vZGUub3duZXJEb2N1bWVudDtcbiAgICAgIHJldHVybiBvd25lckRvY3VtZW50ID8gb3duZXJEb2N1bWVudC5kZWZhdWx0VmlldyB8fCB3aW5kb3cgOiB3aW5kb3c7XG4gICAgfVxuICAgIHJldHVybiBub2RlO1xuICB9XG4gIGZ1bmN0aW9uIGdldFdpbmRvd1Njcm9sbChub2RlKSB7XG4gICAgdmFyIHdpbiA9IGdldFdpbmRvdyhub2RlKTtcbiAgICB2YXIgc2Nyb2xsTGVmdCA9IHdpbi5wYWdlWE9mZnNldDtcbiAgICB2YXIgc2Nyb2xsVG9wID0gd2luLnBhZ2VZT2Zmc2V0O1xuICAgIHJldHVybiB7XG4gICAgICBzY3JvbGxMZWZ0LFxuICAgICAgc2Nyb2xsVG9wXG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBpc0VsZW1lbnQobm9kZSkge1xuICAgIHZhciBPd25FbGVtZW50ID0gZ2V0V2luZG93KG5vZGUpLkVsZW1lbnQ7XG4gICAgcmV0dXJuIG5vZGUgaW5zdGFuY2VvZiBPd25FbGVtZW50IHx8IG5vZGUgaW5zdGFuY2VvZiBFbGVtZW50O1xuICB9XG4gIGZ1bmN0aW9uIGlzSFRNTEVsZW1lbnQobm9kZSkge1xuICAgIHZhciBPd25FbGVtZW50ID0gZ2V0V2luZG93KG5vZGUpLkhUTUxFbGVtZW50O1xuICAgIHJldHVybiBub2RlIGluc3RhbmNlb2YgT3duRWxlbWVudCB8fCBub2RlIGluc3RhbmNlb2YgSFRNTEVsZW1lbnQ7XG4gIH1cbiAgZnVuY3Rpb24gaXNTaGFkb3dSb290KG5vZGUpIHtcbiAgICBpZiAodHlwZW9mIFNoYWRvd1Jvb3QgPT09IFwidW5kZWZpbmVkXCIpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gICAgdmFyIE93bkVsZW1lbnQgPSBnZXRXaW5kb3cobm9kZSkuU2hhZG93Um9vdDtcbiAgICByZXR1cm4gbm9kZSBpbnN0YW5jZW9mIE93bkVsZW1lbnQgfHwgbm9kZSBpbnN0YW5jZW9mIFNoYWRvd1Jvb3Q7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0SFRNTEVsZW1lbnRTY3JvbGwoZWxlbWVudCkge1xuICAgIHJldHVybiB7XG4gICAgICBzY3JvbGxMZWZ0OiBlbGVtZW50LnNjcm9sbExlZnQsXG4gICAgICBzY3JvbGxUb3A6IGVsZW1lbnQuc2Nyb2xsVG9wXG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBnZXROb2RlU2Nyb2xsKG5vZGUpIHtcbiAgICBpZiAobm9kZSA9PT0gZ2V0V2luZG93KG5vZGUpIHx8ICFpc0hUTUxFbGVtZW50KG5vZGUpKSB7XG4gICAgICByZXR1cm4gZ2V0V2luZG93U2Nyb2xsKG5vZGUpO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gZ2V0SFRNTEVsZW1lbnRTY3JvbGwobm9kZSk7XG4gICAgfVxuICB9XG4gIGZ1bmN0aW9uIGdldE5vZGVOYW1lKGVsZW1lbnQpIHtcbiAgICByZXR1cm4gZWxlbWVudCA/IChlbGVtZW50Lm5vZGVOYW1lIHx8IFwiXCIpLnRvTG93ZXJDYXNlKCkgOiBudWxsO1xuICB9XG4gIGZ1bmN0aW9uIGdldERvY3VtZW50RWxlbWVudChlbGVtZW50KSB7XG4gICAgcmV0dXJuICgoaXNFbGVtZW50KGVsZW1lbnQpID8gZWxlbWVudC5vd25lckRvY3VtZW50IDogZWxlbWVudC5kb2N1bWVudCkgfHwgd2luZG93LmRvY3VtZW50KS5kb2N1bWVudEVsZW1lbnQ7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0V2luZG93U2Nyb2xsQmFyWChlbGVtZW50KSB7XG4gICAgcmV0dXJuIGdldEJvdW5kaW5nQ2xpZW50UmVjdChnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCkpLmxlZnQgKyBnZXRXaW5kb3dTY3JvbGwoZWxlbWVudCkuc2Nyb2xsTGVmdDtcbiAgfVxuICBmdW5jdGlvbiBnZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpIHtcbiAgICByZXR1cm4gZ2V0V2luZG93KGVsZW1lbnQpLmdldENvbXB1dGVkU3R5bGUoZWxlbWVudCk7XG4gIH1cbiAgZnVuY3Rpb24gaXNTY3JvbGxQYXJlbnQoZWxlbWVudCkge1xuICAgIHZhciBfZ2V0Q29tcHV0ZWRTdHlsZSA9IGdldENvbXB1dGVkU3R5bGUoZWxlbWVudCksIG92ZXJmbG93ID0gX2dldENvbXB1dGVkU3R5bGUub3ZlcmZsb3csIG92ZXJmbG93WCA9IF9nZXRDb21wdXRlZFN0eWxlLm92ZXJmbG93WCwgb3ZlcmZsb3dZID0gX2dldENvbXB1dGVkU3R5bGUub3ZlcmZsb3dZO1xuICAgIHJldHVybiAvYXV0b3xzY3JvbGx8b3ZlcmxheXxoaWRkZW4vLnRlc3Qob3ZlcmZsb3cgKyBvdmVyZmxvd1kgKyBvdmVyZmxvd1gpO1xuICB9XG4gIGZ1bmN0aW9uIGdldENvbXBvc2l0ZVJlY3QoZWxlbWVudE9yVmlydHVhbEVsZW1lbnQsIG9mZnNldFBhcmVudCwgaXNGaXhlZCkge1xuICAgIGlmIChpc0ZpeGVkID09PSB2b2lkIDApIHtcbiAgICAgIGlzRml4ZWQgPSBmYWxzZTtcbiAgICB9XG4gICAgdmFyIGRvY3VtZW50RWxlbWVudCA9IGdldERvY3VtZW50RWxlbWVudChvZmZzZXRQYXJlbnQpO1xuICAgIHZhciByZWN0ID0gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGVsZW1lbnRPclZpcnR1YWxFbGVtZW50KTtcbiAgICB2YXIgaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgPSBpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCk7XG4gICAgdmFyIHNjcm9sbCA9IHtcbiAgICAgIHNjcm9sbExlZnQ6IDAsXG4gICAgICBzY3JvbGxUb3A6IDBcbiAgICB9O1xuICAgIHZhciBvZmZzZXRzID0ge1xuICAgICAgeDogMCxcbiAgICAgIHk6IDBcbiAgICB9O1xuICAgIGlmIChpc09mZnNldFBhcmVudEFuRWxlbWVudCB8fCAhaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgJiYgIWlzRml4ZWQpIHtcbiAgICAgIGlmIChnZXROb2RlTmFtZShvZmZzZXRQYXJlbnQpICE9PSBcImJvZHlcIiB8fCBpc1Njcm9sbFBhcmVudChkb2N1bWVudEVsZW1lbnQpKSB7XG4gICAgICAgIHNjcm9sbCA9IGdldE5vZGVTY3JvbGwob2Zmc2V0UGFyZW50KTtcbiAgICAgIH1cbiAgICAgIGlmIChpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCkpIHtcbiAgICAgICAgb2Zmc2V0cyA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChvZmZzZXRQYXJlbnQpO1xuICAgICAgICBvZmZzZXRzLnggKz0gb2Zmc2V0UGFyZW50LmNsaWVudExlZnQ7XG4gICAgICAgIG9mZnNldHMueSArPSBvZmZzZXRQYXJlbnQuY2xpZW50VG9wO1xuICAgICAgfSBlbHNlIGlmIChkb2N1bWVudEVsZW1lbnQpIHtcbiAgICAgICAgb2Zmc2V0cy54ID0gZ2V0V2luZG93U2Nyb2xsQmFyWChkb2N1bWVudEVsZW1lbnQpO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4ge1xuICAgICAgeDogcmVjdC5sZWZ0ICsgc2Nyb2xsLnNjcm9sbExlZnQgLSBvZmZzZXRzLngsXG4gICAgICB5OiByZWN0LnRvcCArIHNjcm9sbC5zY3JvbGxUb3AgLSBvZmZzZXRzLnksXG4gICAgICB3aWR0aDogcmVjdC53aWR0aCxcbiAgICAgIGhlaWdodDogcmVjdC5oZWlnaHRcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIGdldExheW91dFJlY3QoZWxlbWVudCkge1xuICAgIHZhciBjbGllbnRSZWN0ID0gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGVsZW1lbnQpO1xuICAgIHZhciB3aWR0aCA9IGVsZW1lbnQub2Zmc2V0V2lkdGg7XG4gICAgdmFyIGhlaWdodCA9IGVsZW1lbnQub2Zmc2V0SGVpZ2h0O1xuICAgIGlmIChNYXRoLmFicyhjbGllbnRSZWN0LndpZHRoIC0gd2lkdGgpIDw9IDEpIHtcbiAgICAgIHdpZHRoID0gY2xpZW50UmVjdC53aWR0aDtcbiAgICB9XG4gICAgaWYgKE1hdGguYWJzKGNsaWVudFJlY3QuaGVpZ2h0IC0gaGVpZ2h0KSA8PSAxKSB7XG4gICAgICBoZWlnaHQgPSBjbGllbnRSZWN0LmhlaWdodDtcbiAgICB9XG4gICAgcmV0dXJuIHtcbiAgICAgIHg6IGVsZW1lbnQub2Zmc2V0TGVmdCxcbiAgICAgIHk6IGVsZW1lbnQub2Zmc2V0VG9wLFxuICAgICAgd2lkdGgsXG4gICAgICBoZWlnaHRcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIGdldFBhcmVudE5vZGUoZWxlbWVudCkge1xuICAgIGlmIChnZXROb2RlTmFtZShlbGVtZW50KSA9PT0gXCJodG1sXCIpIHtcbiAgICAgIHJldHVybiBlbGVtZW50O1xuICAgIH1cbiAgICByZXR1cm4gZWxlbWVudC5hc3NpZ25lZFNsb3QgfHwgZWxlbWVudC5wYXJlbnROb2RlIHx8IChpc1NoYWRvd1Jvb3QoZWxlbWVudCkgPyBlbGVtZW50Lmhvc3QgOiBudWxsKSB8fCBnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCk7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0U2Nyb2xsUGFyZW50KG5vZGUpIHtcbiAgICBpZiAoW1wiaHRtbFwiLCBcImJvZHlcIiwgXCIjZG9jdW1lbnRcIl0uaW5kZXhPZihnZXROb2RlTmFtZShub2RlKSkgPj0gMCkge1xuICAgICAgcmV0dXJuIG5vZGUub3duZXJEb2N1bWVudC5ib2R5O1xuICAgIH1cbiAgICBpZiAoaXNIVE1MRWxlbWVudChub2RlKSAmJiBpc1Njcm9sbFBhcmVudChub2RlKSkge1xuICAgICAgcmV0dXJuIG5vZGU7XG4gICAgfVxuICAgIHJldHVybiBnZXRTY3JvbGxQYXJlbnQoZ2V0UGFyZW50Tm9kZShub2RlKSk7XG4gIH1cbiAgZnVuY3Rpb24gbGlzdFNjcm9sbFBhcmVudHMoZWxlbWVudCwgbGlzdCkge1xuICAgIHZhciBfZWxlbWVudCRvd25lckRvY3VtZW47XG4gICAgaWYgKGxpc3QgPT09IHZvaWQgMCkge1xuICAgICAgbGlzdCA9IFtdO1xuICAgIH1cbiAgICB2YXIgc2Nyb2xsUGFyZW50ID0gZ2V0U2Nyb2xsUGFyZW50KGVsZW1lbnQpO1xuICAgIHZhciBpc0JvZHkgPSBzY3JvbGxQYXJlbnQgPT09ICgoX2VsZW1lbnQkb3duZXJEb2N1bWVuID0gZWxlbWVudC5vd25lckRvY3VtZW50KSA9PSBudWxsID8gdm9pZCAwIDogX2VsZW1lbnQkb3duZXJEb2N1bWVuLmJvZHkpO1xuICAgIHZhciB3aW4gPSBnZXRXaW5kb3coc2Nyb2xsUGFyZW50KTtcbiAgICB2YXIgdGFyZ2V0ID0gaXNCb2R5ID8gW3dpbl0uY29uY2F0KHdpbi52aXN1YWxWaWV3cG9ydCB8fCBbXSwgaXNTY3JvbGxQYXJlbnQoc2Nyb2xsUGFyZW50KSA/IHNjcm9sbFBhcmVudCA6IFtdKSA6IHNjcm9sbFBhcmVudDtcbiAgICB2YXIgdXBkYXRlZExpc3QgPSBsaXN0LmNvbmNhdCh0YXJnZXQpO1xuICAgIHJldHVybiBpc0JvZHkgPyB1cGRhdGVkTGlzdCA6IHVwZGF0ZWRMaXN0LmNvbmNhdChsaXN0U2Nyb2xsUGFyZW50cyhnZXRQYXJlbnROb2RlKHRhcmdldCkpKTtcbiAgfVxuICBmdW5jdGlvbiBpc1RhYmxlRWxlbWVudChlbGVtZW50KSB7XG4gICAgcmV0dXJuIFtcInRhYmxlXCIsIFwidGRcIiwgXCJ0aFwiXS5pbmRleE9mKGdldE5vZGVOYW1lKGVsZW1lbnQpKSA+PSAwO1xuICB9XG4gIGZ1bmN0aW9uIGdldFRydWVPZmZzZXRQYXJlbnQoZWxlbWVudCkge1xuICAgIGlmICghaXNIVE1MRWxlbWVudChlbGVtZW50KSB8fCBnZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpLnBvc2l0aW9uID09PSBcImZpeGVkXCIpIHtcbiAgICAgIHJldHVybiBudWxsO1xuICAgIH1cbiAgICByZXR1cm4gZWxlbWVudC5vZmZzZXRQYXJlbnQ7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0Q29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHtcbiAgICB2YXIgaXNGaXJlZm94ID0gbmF2aWdhdG9yLnVzZXJBZ2VudC50b0xvd2VyQ2FzZSgpLmluZGV4T2YoXCJmaXJlZm94XCIpICE9PSAtMTtcbiAgICB2YXIgaXNJRSA9IG5hdmlnYXRvci51c2VyQWdlbnQuaW5kZXhPZihcIlRyaWRlbnRcIikgIT09IC0xO1xuICAgIGlmIChpc0lFICYmIGlzSFRNTEVsZW1lbnQoZWxlbWVudCkpIHtcbiAgICAgIHZhciBlbGVtZW50Q3NzID0gZ2V0Q29tcHV0ZWRTdHlsZShlbGVtZW50KTtcbiAgICAgIGlmIChlbGVtZW50Q3NzLnBvc2l0aW9uID09PSBcImZpeGVkXCIpIHtcbiAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgICB9XG4gICAgfVxuICAgIHZhciBjdXJyZW50Tm9kZSA9IGdldFBhcmVudE5vZGUoZWxlbWVudCk7XG4gICAgd2hpbGUgKGlzSFRNTEVsZW1lbnQoY3VycmVudE5vZGUpICYmIFtcImh0bWxcIiwgXCJib2R5XCJdLmluZGV4T2YoZ2V0Tm9kZU5hbWUoY3VycmVudE5vZGUpKSA8IDApIHtcbiAgICAgIHZhciBjc3MgPSBnZXRDb21wdXRlZFN0eWxlKGN1cnJlbnROb2RlKTtcbiAgICAgIGlmIChjc3MudHJhbnNmb3JtICE9PSBcIm5vbmVcIiB8fCBjc3MucGVyc3BlY3RpdmUgIT09IFwibm9uZVwiIHx8IGNzcy5jb250YWluID09PSBcInBhaW50XCIgfHwgW1widHJhbnNmb3JtXCIsIFwicGVyc3BlY3RpdmVcIl0uaW5kZXhPZihjc3Mud2lsbENoYW5nZSkgIT09IC0xIHx8IGlzRmlyZWZveCAmJiBjc3Mud2lsbENoYW5nZSA9PT0gXCJmaWx0ZXJcIiB8fCBpc0ZpcmVmb3ggJiYgY3NzLmZpbHRlciAmJiBjc3MuZmlsdGVyICE9PSBcIm5vbmVcIikge1xuICAgICAgICByZXR1cm4gY3VycmVudE5vZGU7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBjdXJyZW50Tm9kZSA9IGN1cnJlbnROb2RlLnBhcmVudE5vZGU7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBudWxsO1xuICB9XG4gIGZ1bmN0aW9uIGdldE9mZnNldFBhcmVudChlbGVtZW50KSB7XG4gICAgdmFyIHdpbmRvdzIgPSBnZXRXaW5kb3coZWxlbWVudCk7XG4gICAgdmFyIG9mZnNldFBhcmVudCA9IGdldFRydWVPZmZzZXRQYXJlbnQoZWxlbWVudCk7XG4gICAgd2hpbGUgKG9mZnNldFBhcmVudCAmJiBpc1RhYmxlRWxlbWVudChvZmZzZXRQYXJlbnQpICYmIGdldENvbXB1dGVkU3R5bGUob2Zmc2V0UGFyZW50KS5wb3NpdGlvbiA9PT0gXCJzdGF0aWNcIikge1xuICAgICAgb2Zmc2V0UGFyZW50ID0gZ2V0VHJ1ZU9mZnNldFBhcmVudChvZmZzZXRQYXJlbnQpO1xuICAgIH1cbiAgICBpZiAob2Zmc2V0UGFyZW50ICYmIChnZXROb2RlTmFtZShvZmZzZXRQYXJlbnQpID09PSBcImh0bWxcIiB8fCBnZXROb2RlTmFtZShvZmZzZXRQYXJlbnQpID09PSBcImJvZHlcIiAmJiBnZXRDb21wdXRlZFN0eWxlKG9mZnNldFBhcmVudCkucG9zaXRpb24gPT09IFwic3RhdGljXCIpKSB7XG4gICAgICByZXR1cm4gd2luZG93MjtcbiAgICB9XG4gICAgcmV0dXJuIG9mZnNldFBhcmVudCB8fCBnZXRDb250YWluaW5nQmxvY2soZWxlbWVudCkgfHwgd2luZG93MjtcbiAgfVxuICB2YXIgdG9wID0gXCJ0b3BcIjtcbiAgdmFyIGJvdHRvbSA9IFwiYm90dG9tXCI7XG4gIHZhciByaWdodCA9IFwicmlnaHRcIjtcbiAgdmFyIGxlZnQgPSBcImxlZnRcIjtcbiAgdmFyIGF1dG8gPSBcImF1dG9cIjtcbiAgdmFyIGJhc2VQbGFjZW1lbnRzID0gW3RvcCwgYm90dG9tLCByaWdodCwgbGVmdF07XG4gIHZhciBzdGFydCA9IFwic3RhcnRcIjtcbiAgdmFyIGVuZCA9IFwiZW5kXCI7XG4gIHZhciBjbGlwcGluZ1BhcmVudHMgPSBcImNsaXBwaW5nUGFyZW50c1wiO1xuICB2YXIgdmlld3BvcnQgPSBcInZpZXdwb3J0XCI7XG4gIHZhciBwb3BwZXIgPSBcInBvcHBlclwiO1xuICB2YXIgcmVmZXJlbmNlID0gXCJyZWZlcmVuY2VcIjtcbiAgdmFyIHZhcmlhdGlvblBsYWNlbWVudHMgPSAvKiBAX19QVVJFX18gKi8gYmFzZVBsYWNlbWVudHMucmVkdWNlKGZ1bmN0aW9uKGFjYywgcGxhY2VtZW50KSB7XG4gICAgcmV0dXJuIGFjYy5jb25jYXQoW3BsYWNlbWVudCArIFwiLVwiICsgc3RhcnQsIHBsYWNlbWVudCArIFwiLVwiICsgZW5kXSk7XG4gIH0sIFtdKTtcbiAgdmFyIHBsYWNlbWVudHMgPSAvKiBAX19QVVJFX18gKi8gW10uY29uY2F0KGJhc2VQbGFjZW1lbnRzLCBbYXV0b10pLnJlZHVjZShmdW5jdGlvbihhY2MsIHBsYWNlbWVudCkge1xuICAgIHJldHVybiBhY2MuY29uY2F0KFtwbGFjZW1lbnQsIHBsYWNlbWVudCArIFwiLVwiICsgc3RhcnQsIHBsYWNlbWVudCArIFwiLVwiICsgZW5kXSk7XG4gIH0sIFtdKTtcbiAgdmFyIGJlZm9yZVJlYWQgPSBcImJlZm9yZVJlYWRcIjtcbiAgdmFyIHJlYWQgPSBcInJlYWRcIjtcbiAgdmFyIGFmdGVyUmVhZCA9IFwiYWZ0ZXJSZWFkXCI7XG4gIHZhciBiZWZvcmVNYWluID0gXCJiZWZvcmVNYWluXCI7XG4gIHZhciBtYWluID0gXCJtYWluXCI7XG4gIHZhciBhZnRlck1haW4gPSBcImFmdGVyTWFpblwiO1xuICB2YXIgYmVmb3JlV3JpdGUgPSBcImJlZm9yZVdyaXRlXCI7XG4gIHZhciB3cml0ZSA9IFwid3JpdGVcIjtcbiAgdmFyIGFmdGVyV3JpdGUgPSBcImFmdGVyV3JpdGVcIjtcbiAgdmFyIG1vZGlmaWVyUGhhc2VzID0gW2JlZm9yZVJlYWQsIHJlYWQsIGFmdGVyUmVhZCwgYmVmb3JlTWFpbiwgbWFpbiwgYWZ0ZXJNYWluLCBiZWZvcmVXcml0ZSwgd3JpdGUsIGFmdGVyV3JpdGVdO1xuICBmdW5jdGlvbiBvcmRlcihtb2RpZmllcnMpIHtcbiAgICB2YXIgbWFwID0gbmV3IE1hcCgpO1xuICAgIHZhciB2aXNpdGVkID0gbmV3IFNldCgpO1xuICAgIHZhciByZXN1bHQgPSBbXTtcbiAgICBtb2RpZmllcnMuZm9yRWFjaChmdW5jdGlvbihtb2RpZmllcikge1xuICAgICAgbWFwLnNldChtb2RpZmllci5uYW1lLCBtb2RpZmllcik7XG4gICAgfSk7XG4gICAgZnVuY3Rpb24gc29ydChtb2RpZmllcikge1xuICAgICAgdmlzaXRlZC5hZGQobW9kaWZpZXIubmFtZSk7XG4gICAgICB2YXIgcmVxdWlyZXMgPSBbXS5jb25jYXQobW9kaWZpZXIucmVxdWlyZXMgfHwgW10sIG1vZGlmaWVyLnJlcXVpcmVzSWZFeGlzdHMgfHwgW10pO1xuICAgICAgcmVxdWlyZXMuZm9yRWFjaChmdW5jdGlvbihkZXApIHtcbiAgICAgICAgaWYgKCF2aXNpdGVkLmhhcyhkZXApKSB7XG4gICAgICAgICAgdmFyIGRlcE1vZGlmaWVyID0gbWFwLmdldChkZXApO1xuICAgICAgICAgIGlmIChkZXBNb2RpZmllcikge1xuICAgICAgICAgICAgc29ydChkZXBNb2RpZmllcik7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9KTtcbiAgICAgIHJlc3VsdC5wdXNoKG1vZGlmaWVyKTtcbiAgICB9XG4gICAgbW9kaWZpZXJzLmZvckVhY2goZnVuY3Rpb24obW9kaWZpZXIpIHtcbiAgICAgIGlmICghdmlzaXRlZC5oYXMobW9kaWZpZXIubmFtZSkpIHtcbiAgICAgICAgc29ydChtb2RpZmllcik7XG4gICAgICB9XG4gICAgfSk7XG4gICAgcmV0dXJuIHJlc3VsdDtcbiAgfVxuICBmdW5jdGlvbiBvcmRlck1vZGlmaWVycyhtb2RpZmllcnMpIHtcbiAgICB2YXIgb3JkZXJlZE1vZGlmaWVycyA9IG9yZGVyKG1vZGlmaWVycyk7XG4gICAgcmV0dXJuIG1vZGlmaWVyUGhhc2VzLnJlZHVjZShmdW5jdGlvbihhY2MsIHBoYXNlKSB7XG4gICAgICByZXR1cm4gYWNjLmNvbmNhdChvcmRlcmVkTW9kaWZpZXJzLmZpbHRlcihmdW5jdGlvbihtb2RpZmllcikge1xuICAgICAgICByZXR1cm4gbW9kaWZpZXIucGhhc2UgPT09IHBoYXNlO1xuICAgICAgfSkpO1xuICAgIH0sIFtdKTtcbiAgfVxuICBmdW5jdGlvbiBkZWJvdW5jZShmbikge1xuICAgIHZhciBwZW5kaW5nO1xuICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgIGlmICghcGVuZGluZykge1xuICAgICAgICBwZW5kaW5nID0gbmV3IFByb21pc2UoZnVuY3Rpb24ocmVzb2x2ZSkge1xuICAgICAgICAgIFByb21pc2UucmVzb2x2ZSgpLnRoZW4oZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBwZW5kaW5nID0gdm9pZCAwO1xuICAgICAgICAgICAgcmVzb2x2ZShmbigpKTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gcGVuZGluZztcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIGZvcm1hdChzdHIpIHtcbiAgICBmb3IgKHZhciBfbGVuID0gYXJndW1lbnRzLmxlbmd0aCwgYXJncyA9IG5ldyBBcnJheShfbGVuID4gMSA/IF9sZW4gLSAxIDogMCksIF9rZXkgPSAxOyBfa2V5IDwgX2xlbjsgX2tleSsrKSB7XG4gICAgICBhcmdzW19rZXkgLSAxXSA9IGFyZ3VtZW50c1tfa2V5XTtcbiAgICB9XG4gICAgcmV0dXJuIFtdLmNvbmNhdChhcmdzKS5yZWR1Y2UoZnVuY3Rpb24ocCwgYykge1xuICAgICAgcmV0dXJuIHAucmVwbGFjZSgvJXMvLCBjKTtcbiAgICB9LCBzdHIpO1xuICB9XG4gIHZhciBJTlZBTElEX01PRElGSUVSX0VSUk9SID0gJ1BvcHBlcjogbW9kaWZpZXIgXCIlc1wiIHByb3ZpZGVkIGFuIGludmFsaWQgJXMgcHJvcGVydHksIGV4cGVjdGVkICVzIGJ1dCBnb3QgJXMnO1xuICB2YXIgTUlTU0lOR19ERVBFTkRFTkNZX0VSUk9SID0gJ1BvcHBlcjogbW9kaWZpZXIgXCIlc1wiIHJlcXVpcmVzIFwiJXNcIiwgYnV0IFwiJXNcIiBtb2RpZmllciBpcyBub3QgYXZhaWxhYmxlJztcbiAgdmFyIFZBTElEX1BST1BFUlRJRVMgPSBbXCJuYW1lXCIsIFwiZW5hYmxlZFwiLCBcInBoYXNlXCIsIFwiZm5cIiwgXCJlZmZlY3RcIiwgXCJyZXF1aXJlc1wiLCBcIm9wdGlvbnNcIl07XG4gIGZ1bmN0aW9uIHZhbGlkYXRlTW9kaWZpZXJzKG1vZGlmaWVycykge1xuICAgIG1vZGlmaWVycy5mb3JFYWNoKGZ1bmN0aW9uKG1vZGlmaWVyKSB7XG4gICAgICBPYmplY3Qua2V5cyhtb2RpZmllcikuZm9yRWFjaChmdW5jdGlvbihrZXkpIHtcbiAgICAgICAgc3dpdGNoIChrZXkpIHtcbiAgICAgICAgICBjYXNlIFwibmFtZVwiOlxuICAgICAgICAgICAgaWYgKHR5cGVvZiBtb2RpZmllci5uYW1lICE9PSBcInN0cmluZ1wiKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KElOVkFMSURfTU9ESUZJRVJfRVJST1IsIFN0cmluZyhtb2RpZmllci5uYW1lKSwgJ1wibmFtZVwiJywgJ1wic3RyaW5nXCInLCAnXCInICsgU3RyaW5nKG1vZGlmaWVyLm5hbWUpICsgJ1wiJykpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgY2FzZSBcImVuYWJsZWRcIjpcbiAgICAgICAgICAgIGlmICh0eXBlb2YgbW9kaWZpZXIuZW5hYmxlZCAhPT0gXCJib29sZWFuXCIpIHtcbiAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihmb3JtYXQoSU5WQUxJRF9NT0RJRklFUl9FUlJPUiwgbW9kaWZpZXIubmFtZSwgJ1wiZW5hYmxlZFwiJywgJ1wiYm9vbGVhblwiJywgJ1wiJyArIFN0cmluZyhtb2RpZmllci5lbmFibGVkKSArICdcIicpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICBjYXNlIFwicGhhc2VcIjpcbiAgICAgICAgICAgIGlmIChtb2RpZmllclBoYXNlcy5pbmRleE9mKG1vZGlmaWVyLnBoYXNlKSA8IDApIHtcbiAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihmb3JtYXQoSU5WQUxJRF9NT0RJRklFUl9FUlJPUiwgbW9kaWZpZXIubmFtZSwgJ1wicGhhc2VcIicsIFwiZWl0aGVyIFwiICsgbW9kaWZpZXJQaGFzZXMuam9pbihcIiwgXCIpLCAnXCInICsgU3RyaW5nKG1vZGlmaWVyLnBoYXNlKSArICdcIicpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgXCJmblwiOlxuICAgICAgICAgICAgaWYgKHR5cGVvZiBtb2RpZmllci5mbiAhPT0gXCJmdW5jdGlvblwiKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KElOVkFMSURfTU9ESUZJRVJfRVJST1IsIG1vZGlmaWVyLm5hbWUsICdcImZuXCInLCAnXCJmdW5jdGlvblwiJywgJ1wiJyArIFN0cmluZyhtb2RpZmllci5mbikgKyAnXCInKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICBjYXNlIFwiZWZmZWN0XCI6XG4gICAgICAgICAgICBpZiAodHlwZW9mIG1vZGlmaWVyLmVmZmVjdCAhPT0gXCJmdW5jdGlvblwiKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KElOVkFMSURfTU9ESUZJRVJfRVJST1IsIG1vZGlmaWVyLm5hbWUsICdcImVmZmVjdFwiJywgJ1wiZnVuY3Rpb25cIicsICdcIicgKyBTdHJpbmcobW9kaWZpZXIuZm4pICsgJ1wiJykpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgY2FzZSBcInJlcXVpcmVzXCI6XG4gICAgICAgICAgICBpZiAoIUFycmF5LmlzQXJyYXkobW9kaWZpZXIucmVxdWlyZXMpKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KElOVkFMSURfTU9ESUZJRVJfRVJST1IsIG1vZGlmaWVyLm5hbWUsICdcInJlcXVpcmVzXCInLCAnXCJhcnJheVwiJywgJ1wiJyArIFN0cmluZyhtb2RpZmllci5yZXF1aXJlcykgKyAnXCInKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICBjYXNlIFwicmVxdWlyZXNJZkV4aXN0c1wiOlxuICAgICAgICAgICAgaWYgKCFBcnJheS5pc0FycmF5KG1vZGlmaWVyLnJlcXVpcmVzSWZFeGlzdHMpKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KElOVkFMSURfTU9ESUZJRVJfRVJST1IsIG1vZGlmaWVyLm5hbWUsICdcInJlcXVpcmVzSWZFeGlzdHNcIicsICdcImFycmF5XCInLCAnXCInICsgU3RyaW5nKG1vZGlmaWVyLnJlcXVpcmVzSWZFeGlzdHMpICsgJ1wiJykpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgY2FzZSBcIm9wdGlvbnNcIjpcbiAgICAgICAgICBjYXNlIFwiZGF0YVwiOlxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoJ1BvcHBlckpTOiBhbiBpbnZhbGlkIHByb3BlcnR5IGhhcyBiZWVuIHByb3ZpZGVkIHRvIHRoZSBcIicgKyBtb2RpZmllci5uYW1lICsgJ1wiIG1vZGlmaWVyLCB2YWxpZCBwcm9wZXJ0aWVzIGFyZSAnICsgVkFMSURfUFJPUEVSVElFUy5tYXAoZnVuY3Rpb24ocykge1xuICAgICAgICAgICAgICByZXR1cm4gJ1wiJyArIHMgKyAnXCInO1xuICAgICAgICAgICAgfSkuam9pbihcIiwgXCIpICsgJzsgYnV0IFwiJyArIGtleSArICdcIiB3YXMgcHJvdmlkZWQuJyk7XG4gICAgICAgIH1cbiAgICAgICAgbW9kaWZpZXIucmVxdWlyZXMgJiYgbW9kaWZpZXIucmVxdWlyZXMuZm9yRWFjaChmdW5jdGlvbihyZXF1aXJlbWVudCkge1xuICAgICAgICAgIGlmIChtb2RpZmllcnMuZmluZChmdW5jdGlvbihtb2QpIHtcbiAgICAgICAgICAgIHJldHVybiBtb2QubmFtZSA9PT0gcmVxdWlyZW1lbnQ7XG4gICAgICAgICAgfSkgPT0gbnVsbCkge1xuICAgICAgICAgICAgY29uc29sZS5lcnJvcihmb3JtYXQoTUlTU0lOR19ERVBFTkRFTkNZX0VSUk9SLCBTdHJpbmcobW9kaWZpZXIubmFtZSksIHJlcXVpcmVtZW50LCByZXF1aXJlbWVudCkpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiB1bmlxdWVCeShhcnIsIGZuKSB7XG4gICAgdmFyIGlkZW50aWZpZXJzID0gbmV3IFNldCgpO1xuICAgIHJldHVybiBhcnIuZmlsdGVyKGZ1bmN0aW9uKGl0ZW0pIHtcbiAgICAgIHZhciBpZGVudGlmaWVyID0gZm4oaXRlbSk7XG4gICAgICBpZiAoIWlkZW50aWZpZXJzLmhhcyhpZGVudGlmaWVyKSkge1xuICAgICAgICBpZGVudGlmaWVycy5hZGQoaWRlbnRpZmllcik7XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGdldEJhc2VQbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gICAgcmV0dXJuIHBsYWNlbWVudC5zcGxpdChcIi1cIilbMF07XG4gIH1cbiAgZnVuY3Rpb24gbWVyZ2VCeU5hbWUobW9kaWZpZXJzKSB7XG4gICAgdmFyIG1lcmdlZCA9IG1vZGlmaWVycy5yZWR1Y2UoZnVuY3Rpb24obWVyZ2VkMiwgY3VycmVudCkge1xuICAgICAgdmFyIGV4aXN0aW5nID0gbWVyZ2VkMltjdXJyZW50Lm5hbWVdO1xuICAgICAgbWVyZ2VkMltjdXJyZW50Lm5hbWVdID0gZXhpc3RpbmcgPyBPYmplY3QuYXNzaWduKHt9LCBleGlzdGluZywgY3VycmVudCwge1xuICAgICAgICBvcHRpb25zOiBPYmplY3QuYXNzaWduKHt9LCBleGlzdGluZy5vcHRpb25zLCBjdXJyZW50Lm9wdGlvbnMpLFxuICAgICAgICBkYXRhOiBPYmplY3QuYXNzaWduKHt9LCBleGlzdGluZy5kYXRhLCBjdXJyZW50LmRhdGEpXG4gICAgICB9KSA6IGN1cnJlbnQ7XG4gICAgICByZXR1cm4gbWVyZ2VkMjtcbiAgICB9LCB7fSk7XG4gICAgcmV0dXJuIE9iamVjdC5rZXlzKG1lcmdlZCkubWFwKGZ1bmN0aW9uKGtleSkge1xuICAgICAgcmV0dXJuIG1lcmdlZFtrZXldO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGdldFZpZXdwb3J0UmVjdChlbGVtZW50KSB7XG4gICAgdmFyIHdpbiA9IGdldFdpbmRvdyhlbGVtZW50KTtcbiAgICB2YXIgaHRtbCA9IGdldERvY3VtZW50RWxlbWVudChlbGVtZW50KTtcbiAgICB2YXIgdmlzdWFsVmlld3BvcnQgPSB3aW4udmlzdWFsVmlld3BvcnQ7XG4gICAgdmFyIHdpZHRoID0gaHRtbC5jbGllbnRXaWR0aDtcbiAgICB2YXIgaGVpZ2h0ID0gaHRtbC5jbGllbnRIZWlnaHQ7XG4gICAgdmFyIHggPSAwO1xuICAgIHZhciB5ID0gMDtcbiAgICBpZiAodmlzdWFsVmlld3BvcnQpIHtcbiAgICAgIHdpZHRoID0gdmlzdWFsVmlld3BvcnQud2lkdGg7XG4gICAgICBoZWlnaHQgPSB2aXN1YWxWaWV3cG9ydC5oZWlnaHQ7XG4gICAgICBpZiAoIS9eKCg/IWNocm9tZXxhbmRyb2lkKS4pKnNhZmFyaS9pLnRlc3QobmF2aWdhdG9yLnVzZXJBZ2VudCkpIHtcbiAgICAgICAgeCA9IHZpc3VhbFZpZXdwb3J0Lm9mZnNldExlZnQ7XG4gICAgICAgIHkgPSB2aXN1YWxWaWV3cG9ydC5vZmZzZXRUb3A7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiB7XG4gICAgICB3aWR0aCxcbiAgICAgIGhlaWdodCxcbiAgICAgIHg6IHggKyBnZXRXaW5kb3dTY3JvbGxCYXJYKGVsZW1lbnQpLFxuICAgICAgeVxuICAgIH07XG4gIH1cbiAgdmFyIG1heCA9IE1hdGgubWF4O1xuICB2YXIgbWluID0gTWF0aC5taW47XG4gIHZhciByb3VuZCA9IE1hdGgucm91bmQ7XG4gIGZ1bmN0aW9uIGdldERvY3VtZW50UmVjdChlbGVtZW50KSB7XG4gICAgdmFyIF9lbGVtZW50JG93bmVyRG9jdW1lbjtcbiAgICB2YXIgaHRtbCA9IGdldERvY3VtZW50RWxlbWVudChlbGVtZW50KTtcbiAgICB2YXIgd2luU2Nyb2xsID0gZ2V0V2luZG93U2Nyb2xsKGVsZW1lbnQpO1xuICAgIHZhciBib2R5ID0gKF9lbGVtZW50JG93bmVyRG9jdW1lbiA9IGVsZW1lbnQub3duZXJEb2N1bWVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9lbGVtZW50JG93bmVyRG9jdW1lbi5ib2R5O1xuICAgIHZhciB3aWR0aCA9IG1heChodG1sLnNjcm9sbFdpZHRoLCBodG1sLmNsaWVudFdpZHRoLCBib2R5ID8gYm9keS5zY3JvbGxXaWR0aCA6IDAsIGJvZHkgPyBib2R5LmNsaWVudFdpZHRoIDogMCk7XG4gICAgdmFyIGhlaWdodCA9IG1heChodG1sLnNjcm9sbEhlaWdodCwgaHRtbC5jbGllbnRIZWlnaHQsIGJvZHkgPyBib2R5LnNjcm9sbEhlaWdodCA6IDAsIGJvZHkgPyBib2R5LmNsaWVudEhlaWdodCA6IDApO1xuICAgIHZhciB4ID0gLXdpblNjcm9sbC5zY3JvbGxMZWZ0ICsgZ2V0V2luZG93U2Nyb2xsQmFyWChlbGVtZW50KTtcbiAgICB2YXIgeSA9IC13aW5TY3JvbGwuc2Nyb2xsVG9wO1xuICAgIGlmIChnZXRDb21wdXRlZFN0eWxlKGJvZHkgfHwgaHRtbCkuZGlyZWN0aW9uID09PSBcInJ0bFwiKSB7XG4gICAgICB4ICs9IG1heChodG1sLmNsaWVudFdpZHRoLCBib2R5ID8gYm9keS5jbGllbnRXaWR0aCA6IDApIC0gd2lkdGg7XG4gICAgfVxuICAgIHJldHVybiB7XG4gICAgICB3aWR0aCxcbiAgICAgIGhlaWdodCxcbiAgICAgIHgsXG4gICAgICB5XG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBjb250YWlucyhwYXJlbnQsIGNoaWxkKSB7XG4gICAgdmFyIHJvb3ROb2RlID0gY2hpbGQuZ2V0Um9vdE5vZGUgJiYgY2hpbGQuZ2V0Um9vdE5vZGUoKTtcbiAgICBpZiAocGFyZW50LmNvbnRhaW5zKGNoaWxkKSkge1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfSBlbHNlIGlmIChyb290Tm9kZSAmJiBpc1NoYWRvd1Jvb3Qocm9vdE5vZGUpKSB7XG4gICAgICB2YXIgbmV4dCA9IGNoaWxkO1xuICAgICAgZG8ge1xuICAgICAgICBpZiAobmV4dCAmJiBwYXJlbnQuaXNTYW1lTm9kZShuZXh0KSkge1xuICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIG5leHQgPSBuZXh0LnBhcmVudE5vZGUgfHwgbmV4dC5ob3N0O1xuICAgICAgfSB3aGlsZSAobmV4dCk7XG4gICAgfVxuICAgIHJldHVybiBmYWxzZTtcbiAgfVxuICBmdW5jdGlvbiByZWN0VG9DbGllbnRSZWN0KHJlY3QpIHtcbiAgICByZXR1cm4gT2JqZWN0LmFzc2lnbih7fSwgcmVjdCwge1xuICAgICAgbGVmdDogcmVjdC54LFxuICAgICAgdG9wOiByZWN0LnksXG4gICAgICByaWdodDogcmVjdC54ICsgcmVjdC53aWR0aCxcbiAgICAgIGJvdHRvbTogcmVjdC55ICsgcmVjdC5oZWlnaHRcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBnZXRJbm5lckJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50KSB7XG4gICAgdmFyIHJlY3QgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3QoZWxlbWVudCk7XG4gICAgcmVjdC50b3AgPSByZWN0LnRvcCArIGVsZW1lbnQuY2xpZW50VG9wO1xuICAgIHJlY3QubGVmdCA9IHJlY3QubGVmdCArIGVsZW1lbnQuY2xpZW50TGVmdDtcbiAgICByZWN0LmJvdHRvbSA9IHJlY3QudG9wICsgZWxlbWVudC5jbGllbnRIZWlnaHQ7XG4gICAgcmVjdC5yaWdodCA9IHJlY3QubGVmdCArIGVsZW1lbnQuY2xpZW50V2lkdGg7XG4gICAgcmVjdC53aWR0aCA9IGVsZW1lbnQuY2xpZW50V2lkdGg7XG4gICAgcmVjdC5oZWlnaHQgPSBlbGVtZW50LmNsaWVudEhlaWdodDtcbiAgICByZWN0LnggPSByZWN0LmxlZnQ7XG4gICAgcmVjdC55ID0gcmVjdC50b3A7XG4gICAgcmV0dXJuIHJlY3Q7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0Q2xpZW50UmVjdEZyb21NaXhlZFR5cGUoZWxlbWVudCwgY2xpcHBpbmdQYXJlbnQpIHtcbiAgICByZXR1cm4gY2xpcHBpbmdQYXJlbnQgPT09IHZpZXdwb3J0ID8gcmVjdFRvQ2xpZW50UmVjdChnZXRWaWV3cG9ydFJlY3QoZWxlbWVudCkpIDogaXNIVE1MRWxlbWVudChjbGlwcGluZ1BhcmVudCkgPyBnZXRJbm5lckJvdW5kaW5nQ2xpZW50UmVjdChjbGlwcGluZ1BhcmVudCkgOiByZWN0VG9DbGllbnRSZWN0KGdldERvY3VtZW50UmVjdChnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCkpKTtcbiAgfVxuICBmdW5jdGlvbiBnZXRDbGlwcGluZ1BhcmVudHMoZWxlbWVudCkge1xuICAgIHZhciBjbGlwcGluZ1BhcmVudHMyID0gbGlzdFNjcm9sbFBhcmVudHMoZ2V0UGFyZW50Tm9kZShlbGVtZW50KSk7XG4gICAgdmFyIGNhbkVzY2FwZUNsaXBwaW5nID0gW1wiYWJzb2x1dGVcIiwgXCJmaXhlZFwiXS5pbmRleE9mKGdldENvbXB1dGVkU3R5bGUoZWxlbWVudCkucG9zaXRpb24pID49IDA7XG4gICAgdmFyIGNsaXBwZXJFbGVtZW50ID0gY2FuRXNjYXBlQ2xpcHBpbmcgJiYgaXNIVE1MRWxlbWVudChlbGVtZW50KSA/IGdldE9mZnNldFBhcmVudChlbGVtZW50KSA6IGVsZW1lbnQ7XG4gICAgaWYgKCFpc0VsZW1lbnQoY2xpcHBlckVsZW1lbnQpKSB7XG4gICAgICByZXR1cm4gW107XG4gICAgfVxuICAgIHJldHVybiBjbGlwcGluZ1BhcmVudHMyLmZpbHRlcihmdW5jdGlvbihjbGlwcGluZ1BhcmVudCkge1xuICAgICAgcmV0dXJuIGlzRWxlbWVudChjbGlwcGluZ1BhcmVudCkgJiYgY29udGFpbnMoY2xpcHBpbmdQYXJlbnQsIGNsaXBwZXJFbGVtZW50KSAmJiBnZXROb2RlTmFtZShjbGlwcGluZ1BhcmVudCkgIT09IFwiYm9keVwiO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGdldENsaXBwaW5nUmVjdChlbGVtZW50LCBib3VuZGFyeSwgcm9vdEJvdW5kYXJ5KSB7XG4gICAgdmFyIG1haW5DbGlwcGluZ1BhcmVudHMgPSBib3VuZGFyeSA9PT0gXCJjbGlwcGluZ1BhcmVudHNcIiA/IGdldENsaXBwaW5nUGFyZW50cyhlbGVtZW50KSA6IFtdLmNvbmNhdChib3VuZGFyeSk7XG4gICAgdmFyIGNsaXBwaW5nUGFyZW50czIgPSBbXS5jb25jYXQobWFpbkNsaXBwaW5nUGFyZW50cywgW3Jvb3RCb3VuZGFyeV0pO1xuICAgIHZhciBmaXJzdENsaXBwaW5nUGFyZW50ID0gY2xpcHBpbmdQYXJlbnRzMlswXTtcbiAgICB2YXIgY2xpcHBpbmdSZWN0ID0gY2xpcHBpbmdQYXJlbnRzMi5yZWR1Y2UoZnVuY3Rpb24oYWNjUmVjdCwgY2xpcHBpbmdQYXJlbnQpIHtcbiAgICAgIHZhciByZWN0ID0gZ2V0Q2xpZW50UmVjdEZyb21NaXhlZFR5cGUoZWxlbWVudCwgY2xpcHBpbmdQYXJlbnQpO1xuICAgICAgYWNjUmVjdC50b3AgPSBtYXgocmVjdC50b3AsIGFjY1JlY3QudG9wKTtcbiAgICAgIGFjY1JlY3QucmlnaHQgPSBtaW4ocmVjdC5yaWdodCwgYWNjUmVjdC5yaWdodCk7XG4gICAgICBhY2NSZWN0LmJvdHRvbSA9IG1pbihyZWN0LmJvdHRvbSwgYWNjUmVjdC5ib3R0b20pO1xuICAgICAgYWNjUmVjdC5sZWZ0ID0gbWF4KHJlY3QubGVmdCwgYWNjUmVjdC5sZWZ0KTtcbiAgICAgIHJldHVybiBhY2NSZWN0O1xuICAgIH0sIGdldENsaWVudFJlY3RGcm9tTWl4ZWRUeXBlKGVsZW1lbnQsIGZpcnN0Q2xpcHBpbmdQYXJlbnQpKTtcbiAgICBjbGlwcGluZ1JlY3Qud2lkdGggPSBjbGlwcGluZ1JlY3QucmlnaHQgLSBjbGlwcGluZ1JlY3QubGVmdDtcbiAgICBjbGlwcGluZ1JlY3QuaGVpZ2h0ID0gY2xpcHBpbmdSZWN0LmJvdHRvbSAtIGNsaXBwaW5nUmVjdC50b3A7XG4gICAgY2xpcHBpbmdSZWN0LnggPSBjbGlwcGluZ1JlY3QubGVmdDtcbiAgICBjbGlwcGluZ1JlY3QueSA9IGNsaXBwaW5nUmVjdC50b3A7XG4gICAgcmV0dXJuIGNsaXBwaW5nUmVjdDtcbiAgfVxuICBmdW5jdGlvbiBnZXRWYXJpYXRpb24ocGxhY2VtZW50KSB7XG4gICAgcmV0dXJuIHBsYWNlbWVudC5zcGxpdChcIi1cIilbMV07XG4gIH1cbiAgZnVuY3Rpb24gZ2V0TWFpbkF4aXNGcm9tUGxhY2VtZW50KHBsYWNlbWVudCkge1xuICAgIHJldHVybiBbXCJ0b3BcIiwgXCJib3R0b21cIl0uaW5kZXhPZihwbGFjZW1lbnQpID49IDAgPyBcInhcIiA6IFwieVwiO1xuICB9XG4gIGZ1bmN0aW9uIGNvbXB1dGVPZmZzZXRzKF9yZWYpIHtcbiAgICB2YXIgcmVmZXJlbmNlMiA9IF9yZWYucmVmZXJlbmNlLCBlbGVtZW50ID0gX3JlZi5lbGVtZW50LCBwbGFjZW1lbnQgPSBfcmVmLnBsYWNlbWVudDtcbiAgICB2YXIgYmFzZVBsYWNlbWVudCA9IHBsYWNlbWVudCA/IGdldEJhc2VQbGFjZW1lbnQocGxhY2VtZW50KSA6IG51bGw7XG4gICAgdmFyIHZhcmlhdGlvbiA9IHBsYWNlbWVudCA/IGdldFZhcmlhdGlvbihwbGFjZW1lbnQpIDogbnVsbDtcbiAgICB2YXIgY29tbW9uWCA9IHJlZmVyZW5jZTIueCArIHJlZmVyZW5jZTIud2lkdGggLyAyIC0gZWxlbWVudC53aWR0aCAvIDI7XG4gICAgdmFyIGNvbW1vblkgPSByZWZlcmVuY2UyLnkgKyByZWZlcmVuY2UyLmhlaWdodCAvIDIgLSBlbGVtZW50LmhlaWdodCAvIDI7XG4gICAgdmFyIG9mZnNldHM7XG4gICAgc3dpdGNoIChiYXNlUGxhY2VtZW50KSB7XG4gICAgICBjYXNlIHRvcDpcbiAgICAgICAgb2Zmc2V0cyA9IHtcbiAgICAgICAgICB4OiBjb21tb25YLFxuICAgICAgICAgIHk6IHJlZmVyZW5jZTIueSAtIGVsZW1lbnQuaGVpZ2h0XG4gICAgICAgIH07XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSBib3R0b206XG4gICAgICAgIG9mZnNldHMgPSB7XG4gICAgICAgICAgeDogY29tbW9uWCxcbiAgICAgICAgICB5OiByZWZlcmVuY2UyLnkgKyByZWZlcmVuY2UyLmhlaWdodFxuICAgICAgICB9O1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgcmlnaHQ6XG4gICAgICAgIG9mZnNldHMgPSB7XG4gICAgICAgICAgeDogcmVmZXJlbmNlMi54ICsgcmVmZXJlbmNlMi53aWR0aCxcbiAgICAgICAgICB5OiBjb21tb25ZXG4gICAgICAgIH07XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSBsZWZ0OlxuICAgICAgICBvZmZzZXRzID0ge1xuICAgICAgICAgIHg6IHJlZmVyZW5jZTIueCAtIGVsZW1lbnQud2lkdGgsXG4gICAgICAgICAgeTogY29tbW9uWVxuICAgICAgICB9O1xuICAgICAgICBicmVhaztcbiAgICAgIGRlZmF1bHQ6XG4gICAgICAgIG9mZnNldHMgPSB7XG4gICAgICAgICAgeDogcmVmZXJlbmNlMi54LFxuICAgICAgICAgIHk6IHJlZmVyZW5jZTIueVxuICAgICAgICB9O1xuICAgIH1cbiAgICB2YXIgbWFpbkF4aXMgPSBiYXNlUGxhY2VtZW50ID8gZ2V0TWFpbkF4aXNGcm9tUGxhY2VtZW50KGJhc2VQbGFjZW1lbnQpIDogbnVsbDtcbiAgICBpZiAobWFpbkF4aXMgIT0gbnVsbCkge1xuICAgICAgdmFyIGxlbiA9IG1haW5BeGlzID09PSBcInlcIiA/IFwiaGVpZ2h0XCIgOiBcIndpZHRoXCI7XG4gICAgICBzd2l0Y2ggKHZhcmlhdGlvbikge1xuICAgICAgICBjYXNlIHN0YXJ0OlxuICAgICAgICAgIG9mZnNldHNbbWFpbkF4aXNdID0gb2Zmc2V0c1ttYWluQXhpc10gLSAocmVmZXJlbmNlMltsZW5dIC8gMiAtIGVsZW1lbnRbbGVuXSAvIDIpO1xuICAgICAgICAgIGJyZWFrO1xuICAgICAgICBjYXNlIGVuZDpcbiAgICAgICAgICBvZmZzZXRzW21haW5BeGlzXSA9IG9mZnNldHNbbWFpbkF4aXNdICsgKHJlZmVyZW5jZTJbbGVuXSAvIDIgLSBlbGVtZW50W2xlbl0gLyAyKTtcbiAgICAgICAgICBicmVhaztcbiAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIG9mZnNldHM7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0RnJlc2hTaWRlT2JqZWN0KCkge1xuICAgIHJldHVybiB7XG4gICAgICB0b3A6IDAsXG4gICAgICByaWdodDogMCxcbiAgICAgIGJvdHRvbTogMCxcbiAgICAgIGxlZnQ6IDBcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIG1lcmdlUGFkZGluZ09iamVjdChwYWRkaW5nT2JqZWN0KSB7XG4gICAgcmV0dXJuIE9iamVjdC5hc3NpZ24oe30sIGdldEZyZXNoU2lkZU9iamVjdCgpLCBwYWRkaW5nT2JqZWN0KTtcbiAgfVxuICBmdW5jdGlvbiBleHBhbmRUb0hhc2hNYXAodmFsdWUsIGtleXMpIHtcbiAgICByZXR1cm4ga2V5cy5yZWR1Y2UoZnVuY3Rpb24oaGFzaE1hcCwga2V5KSB7XG4gICAgICBoYXNoTWFwW2tleV0gPSB2YWx1ZTtcbiAgICAgIHJldHVybiBoYXNoTWFwO1xuICAgIH0sIHt9KTtcbiAgfVxuICBmdW5jdGlvbiBkZXRlY3RPdmVyZmxvdyhzdGF0ZSwgb3B0aW9ucykge1xuICAgIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICAgIG9wdGlvbnMgPSB7fTtcbiAgICB9XG4gICAgdmFyIF9vcHRpb25zID0gb3B0aW9ucywgX29wdGlvbnMkcGxhY2VtZW50ID0gX29wdGlvbnMucGxhY2VtZW50LCBwbGFjZW1lbnQgPSBfb3B0aW9ucyRwbGFjZW1lbnQgPT09IHZvaWQgMCA/IHN0YXRlLnBsYWNlbWVudCA6IF9vcHRpb25zJHBsYWNlbWVudCwgX29wdGlvbnMkYm91bmRhcnkgPSBfb3B0aW9ucy5ib3VuZGFyeSwgYm91bmRhcnkgPSBfb3B0aW9ucyRib3VuZGFyeSA9PT0gdm9pZCAwID8gY2xpcHBpbmdQYXJlbnRzIDogX29wdGlvbnMkYm91bmRhcnksIF9vcHRpb25zJHJvb3RCb3VuZGFyeSA9IF9vcHRpb25zLnJvb3RCb3VuZGFyeSwgcm9vdEJvdW5kYXJ5ID0gX29wdGlvbnMkcm9vdEJvdW5kYXJ5ID09PSB2b2lkIDAgPyB2aWV3cG9ydCA6IF9vcHRpb25zJHJvb3RCb3VuZGFyeSwgX29wdGlvbnMkZWxlbWVudENvbnRlID0gX29wdGlvbnMuZWxlbWVudENvbnRleHQsIGVsZW1lbnRDb250ZXh0ID0gX29wdGlvbnMkZWxlbWVudENvbnRlID09PSB2b2lkIDAgPyBwb3BwZXIgOiBfb3B0aW9ucyRlbGVtZW50Q29udGUsIF9vcHRpb25zJGFsdEJvdW5kYXJ5ID0gX29wdGlvbnMuYWx0Qm91bmRhcnksIGFsdEJvdW5kYXJ5ID0gX29wdGlvbnMkYWx0Qm91bmRhcnkgPT09IHZvaWQgMCA/IGZhbHNlIDogX29wdGlvbnMkYWx0Qm91bmRhcnksIF9vcHRpb25zJHBhZGRpbmcgPSBfb3B0aW9ucy5wYWRkaW5nLCBwYWRkaW5nID0gX29wdGlvbnMkcGFkZGluZyA9PT0gdm9pZCAwID8gMCA6IF9vcHRpb25zJHBhZGRpbmc7XG4gICAgdmFyIHBhZGRpbmdPYmplY3QgPSBtZXJnZVBhZGRpbmdPYmplY3QodHlwZW9mIHBhZGRpbmcgIT09IFwibnVtYmVyXCIgPyBwYWRkaW5nIDogZXhwYW5kVG9IYXNoTWFwKHBhZGRpbmcsIGJhc2VQbGFjZW1lbnRzKSk7XG4gICAgdmFyIGFsdENvbnRleHQgPSBlbGVtZW50Q29udGV4dCA9PT0gcG9wcGVyID8gcmVmZXJlbmNlIDogcG9wcGVyO1xuICAgIHZhciByZWZlcmVuY2VFbGVtZW50ID0gc3RhdGUuZWxlbWVudHMucmVmZXJlbmNlO1xuICAgIHZhciBwb3BwZXJSZWN0ID0gc3RhdGUucmVjdHMucG9wcGVyO1xuICAgIHZhciBlbGVtZW50ID0gc3RhdGUuZWxlbWVudHNbYWx0Qm91bmRhcnkgPyBhbHRDb250ZXh0IDogZWxlbWVudENvbnRleHRdO1xuICAgIHZhciBjbGlwcGluZ0NsaWVudFJlY3QgPSBnZXRDbGlwcGluZ1JlY3QoaXNFbGVtZW50KGVsZW1lbnQpID8gZWxlbWVudCA6IGVsZW1lbnQuY29udGV4dEVsZW1lbnQgfHwgZ2V0RG9jdW1lbnRFbGVtZW50KHN0YXRlLmVsZW1lbnRzLnBvcHBlciksIGJvdW5kYXJ5LCByb290Qm91bmRhcnkpO1xuICAgIHZhciByZWZlcmVuY2VDbGllbnRSZWN0ID0gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KHJlZmVyZW5jZUVsZW1lbnQpO1xuICAgIHZhciBwb3BwZXJPZmZzZXRzMiA9IGNvbXB1dGVPZmZzZXRzKHtcbiAgICAgIHJlZmVyZW5jZTogcmVmZXJlbmNlQ2xpZW50UmVjdCxcbiAgICAgIGVsZW1lbnQ6IHBvcHBlclJlY3QsXG4gICAgICBzdHJhdGVneTogXCJhYnNvbHV0ZVwiLFxuICAgICAgcGxhY2VtZW50XG4gICAgfSk7XG4gICAgdmFyIHBvcHBlckNsaWVudFJlY3QgPSByZWN0VG9DbGllbnRSZWN0KE9iamVjdC5hc3NpZ24oe30sIHBvcHBlclJlY3QsIHBvcHBlck9mZnNldHMyKSk7XG4gICAgdmFyIGVsZW1lbnRDbGllbnRSZWN0ID0gZWxlbWVudENvbnRleHQgPT09IHBvcHBlciA/IHBvcHBlckNsaWVudFJlY3QgOiByZWZlcmVuY2VDbGllbnRSZWN0O1xuICAgIHZhciBvdmVyZmxvd09mZnNldHMgPSB7XG4gICAgICB0b3A6IGNsaXBwaW5nQ2xpZW50UmVjdC50b3AgLSBlbGVtZW50Q2xpZW50UmVjdC50b3AgKyBwYWRkaW5nT2JqZWN0LnRvcCxcbiAgICAgIGJvdHRvbTogZWxlbWVudENsaWVudFJlY3QuYm90dG9tIC0gY2xpcHBpbmdDbGllbnRSZWN0LmJvdHRvbSArIHBhZGRpbmdPYmplY3QuYm90dG9tLFxuICAgICAgbGVmdDogY2xpcHBpbmdDbGllbnRSZWN0LmxlZnQgLSBlbGVtZW50Q2xpZW50UmVjdC5sZWZ0ICsgcGFkZGluZ09iamVjdC5sZWZ0LFxuICAgICAgcmlnaHQ6IGVsZW1lbnRDbGllbnRSZWN0LnJpZ2h0IC0gY2xpcHBpbmdDbGllbnRSZWN0LnJpZ2h0ICsgcGFkZGluZ09iamVjdC5yaWdodFxuICAgIH07XG4gICAgdmFyIG9mZnNldERhdGEgPSBzdGF0ZS5tb2RpZmllcnNEYXRhLm9mZnNldDtcbiAgICBpZiAoZWxlbWVudENvbnRleHQgPT09IHBvcHBlciAmJiBvZmZzZXREYXRhKSB7XG4gICAgICB2YXIgb2Zmc2V0MiA9IG9mZnNldERhdGFbcGxhY2VtZW50XTtcbiAgICAgIE9iamVjdC5rZXlzKG92ZXJmbG93T2Zmc2V0cykuZm9yRWFjaChmdW5jdGlvbihrZXkpIHtcbiAgICAgICAgdmFyIG11bHRpcGx5ID0gW3JpZ2h0LCBib3R0b21dLmluZGV4T2Yoa2V5KSA+PSAwID8gMSA6IC0xO1xuICAgICAgICB2YXIgYXhpcyA9IFt0b3AsIGJvdHRvbV0uaW5kZXhPZihrZXkpID49IDAgPyBcInlcIiA6IFwieFwiO1xuICAgICAgICBvdmVyZmxvd09mZnNldHNba2V5XSArPSBvZmZzZXQyW2F4aXNdICogbXVsdGlwbHk7XG4gICAgICB9KTtcbiAgICB9XG4gICAgcmV0dXJuIG92ZXJmbG93T2Zmc2V0cztcbiAgfVxuICB2YXIgSU5WQUxJRF9FTEVNRU5UX0VSUk9SID0gXCJQb3BwZXI6IEludmFsaWQgcmVmZXJlbmNlIG9yIHBvcHBlciBhcmd1bWVudCBwcm92aWRlZC4gVGhleSBtdXN0IGJlIGVpdGhlciBhIERPTSBlbGVtZW50IG9yIHZpcnR1YWwgZWxlbWVudC5cIjtcbiAgdmFyIElORklOSVRFX0xPT1BfRVJST1IgPSBcIlBvcHBlcjogQW4gaW5maW5pdGUgbG9vcCBpbiB0aGUgbW9kaWZpZXJzIGN5Y2xlIGhhcyBiZWVuIGRldGVjdGVkISBUaGUgY3ljbGUgaGFzIGJlZW4gaW50ZXJydXB0ZWQgdG8gcHJldmVudCBhIGJyb3dzZXIgY3Jhc2guXCI7XG4gIHZhciBERUZBVUxUX09QVElPTlMgPSB7XG4gICAgcGxhY2VtZW50OiBcImJvdHRvbVwiLFxuICAgIG1vZGlmaWVyczogW10sXG4gICAgc3RyYXRlZ3k6IFwiYWJzb2x1dGVcIlxuICB9O1xuICBmdW5jdGlvbiBhcmVWYWxpZEVsZW1lbnRzKCkge1xuICAgIGZvciAodmFyIF9sZW4gPSBhcmd1bWVudHMubGVuZ3RoLCBhcmdzID0gbmV3IEFycmF5KF9sZW4pLCBfa2V5ID0gMDsgX2tleSA8IF9sZW47IF9rZXkrKykge1xuICAgICAgYXJnc1tfa2V5XSA9IGFyZ3VtZW50c1tfa2V5XTtcbiAgICB9XG4gICAgcmV0dXJuICFhcmdzLnNvbWUoZnVuY3Rpb24oZWxlbWVudCkge1xuICAgICAgcmV0dXJuICEoZWxlbWVudCAmJiB0eXBlb2YgZWxlbWVudC5nZXRCb3VuZGluZ0NsaWVudFJlY3QgPT09IFwiZnVuY3Rpb25cIik7XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gcG9wcGVyR2VuZXJhdG9yKGdlbmVyYXRvck9wdGlvbnMpIHtcbiAgICBpZiAoZ2VuZXJhdG9yT3B0aW9ucyA9PT0gdm9pZCAwKSB7XG4gICAgICBnZW5lcmF0b3JPcHRpb25zID0ge307XG4gICAgfVxuICAgIHZhciBfZ2VuZXJhdG9yT3B0aW9ucyA9IGdlbmVyYXRvck9wdGlvbnMsIF9nZW5lcmF0b3JPcHRpb25zJGRlZiA9IF9nZW5lcmF0b3JPcHRpb25zLmRlZmF1bHRNb2RpZmllcnMsIGRlZmF1bHRNb2RpZmllcnMyID0gX2dlbmVyYXRvck9wdGlvbnMkZGVmID09PSB2b2lkIDAgPyBbXSA6IF9nZW5lcmF0b3JPcHRpb25zJGRlZiwgX2dlbmVyYXRvck9wdGlvbnMkZGVmMiA9IF9nZW5lcmF0b3JPcHRpb25zLmRlZmF1bHRPcHRpb25zLCBkZWZhdWx0T3B0aW9ucyA9IF9nZW5lcmF0b3JPcHRpb25zJGRlZjIgPT09IHZvaWQgMCA/IERFRkFVTFRfT1BUSU9OUyA6IF9nZW5lcmF0b3JPcHRpb25zJGRlZjI7XG4gICAgcmV0dXJuIGZ1bmN0aW9uIGNyZWF0ZVBvcHBlcjIocmVmZXJlbmNlMiwgcG9wcGVyMiwgb3B0aW9ucykge1xuICAgICAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgICAgICBvcHRpb25zID0gZGVmYXVsdE9wdGlvbnM7XG4gICAgICB9XG4gICAgICB2YXIgc3RhdGUgPSB7XG4gICAgICAgIHBsYWNlbWVudDogXCJib3R0b21cIixcbiAgICAgICAgb3JkZXJlZE1vZGlmaWVyczogW10sXG4gICAgICAgIG9wdGlvbnM6IE9iamVjdC5hc3NpZ24oe30sIERFRkFVTFRfT1BUSU9OUywgZGVmYXVsdE9wdGlvbnMpLFxuICAgICAgICBtb2RpZmllcnNEYXRhOiB7fSxcbiAgICAgICAgZWxlbWVudHM6IHtcbiAgICAgICAgICByZWZlcmVuY2U6IHJlZmVyZW5jZTIsXG4gICAgICAgICAgcG9wcGVyOiBwb3BwZXIyXG4gICAgICAgIH0sXG4gICAgICAgIGF0dHJpYnV0ZXM6IHt9LFxuICAgICAgICBzdHlsZXM6IHt9XG4gICAgICB9O1xuICAgICAgdmFyIGVmZmVjdENsZWFudXBGbnMgPSBbXTtcbiAgICAgIHZhciBpc0Rlc3Ryb3llZCA9IGZhbHNlO1xuICAgICAgdmFyIGluc3RhbmNlID0ge1xuICAgICAgICBzdGF0ZSxcbiAgICAgICAgc2V0T3B0aW9uczogZnVuY3Rpb24gc2V0T3B0aW9ucyhvcHRpb25zMikge1xuICAgICAgICAgIGNsZWFudXBNb2RpZmllckVmZmVjdHMoKTtcbiAgICAgICAgICBzdGF0ZS5vcHRpb25zID0gT2JqZWN0LmFzc2lnbih7fSwgZGVmYXVsdE9wdGlvbnMsIHN0YXRlLm9wdGlvbnMsIG9wdGlvbnMyKTtcbiAgICAgICAgICBzdGF0ZS5zY3JvbGxQYXJlbnRzID0ge1xuICAgICAgICAgICAgcmVmZXJlbmNlOiBpc0VsZW1lbnQocmVmZXJlbmNlMikgPyBsaXN0U2Nyb2xsUGFyZW50cyhyZWZlcmVuY2UyKSA6IHJlZmVyZW5jZTIuY29udGV4dEVsZW1lbnQgPyBsaXN0U2Nyb2xsUGFyZW50cyhyZWZlcmVuY2UyLmNvbnRleHRFbGVtZW50KSA6IFtdLFxuICAgICAgICAgICAgcG9wcGVyOiBsaXN0U2Nyb2xsUGFyZW50cyhwb3BwZXIyKVxuICAgICAgICAgIH07XG4gICAgICAgICAgdmFyIG9yZGVyZWRNb2RpZmllcnMgPSBvcmRlck1vZGlmaWVycyhtZXJnZUJ5TmFtZShbXS5jb25jYXQoZGVmYXVsdE1vZGlmaWVyczIsIHN0YXRlLm9wdGlvbnMubW9kaWZpZXJzKSkpO1xuICAgICAgICAgIHN0YXRlLm9yZGVyZWRNb2RpZmllcnMgPSBvcmRlcmVkTW9kaWZpZXJzLmZpbHRlcihmdW5jdGlvbihtKSB7XG4gICAgICAgICAgICByZXR1cm4gbS5lbmFibGVkO1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgICAgICB2YXIgbW9kaWZpZXJzID0gdW5pcXVlQnkoW10uY29uY2F0KG9yZGVyZWRNb2RpZmllcnMsIHN0YXRlLm9wdGlvbnMubW9kaWZpZXJzKSwgZnVuY3Rpb24oX3JlZikge1xuICAgICAgICAgICAgICB2YXIgbmFtZSA9IF9yZWYubmFtZTtcbiAgICAgICAgICAgICAgcmV0dXJuIG5hbWU7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIHZhbGlkYXRlTW9kaWZpZXJzKG1vZGlmaWVycyk7XG4gICAgICAgICAgICBpZiAoZ2V0QmFzZVBsYWNlbWVudChzdGF0ZS5vcHRpb25zLnBsYWNlbWVudCkgPT09IGF1dG8pIHtcbiAgICAgICAgICAgICAgdmFyIGZsaXBNb2RpZmllciA9IHN0YXRlLm9yZGVyZWRNb2RpZmllcnMuZmluZChmdW5jdGlvbihfcmVmMikge1xuICAgICAgICAgICAgICAgIHZhciBuYW1lID0gX3JlZjIubmFtZTtcbiAgICAgICAgICAgICAgICByZXR1cm4gbmFtZSA9PT0gXCJmbGlwXCI7XG4gICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICBpZiAoIWZsaXBNb2RpZmllcikge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoWydQb3BwZXI6IFwiYXV0b1wiIHBsYWNlbWVudHMgcmVxdWlyZSB0aGUgXCJmbGlwXCIgbW9kaWZpZXIgYmUnLCBcInByZXNlbnQgYW5kIGVuYWJsZWQgdG8gd29yay5cIl0uam9pbihcIiBcIikpO1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB2YXIgX2dldENvbXB1dGVkU3R5bGUgPSBnZXRDb21wdXRlZFN0eWxlKHBvcHBlcjIpLCBtYXJnaW5Ub3AgPSBfZ2V0Q29tcHV0ZWRTdHlsZS5tYXJnaW5Ub3AsIG1hcmdpblJpZ2h0ID0gX2dldENvbXB1dGVkU3R5bGUubWFyZ2luUmlnaHQsIG1hcmdpbkJvdHRvbSA9IF9nZXRDb21wdXRlZFN0eWxlLm1hcmdpbkJvdHRvbSwgbWFyZ2luTGVmdCA9IF9nZXRDb21wdXRlZFN0eWxlLm1hcmdpbkxlZnQ7XG4gICAgICAgICAgICBpZiAoW21hcmdpblRvcCwgbWFyZ2luUmlnaHQsIG1hcmdpbkJvdHRvbSwgbWFyZ2luTGVmdF0uc29tZShmdW5jdGlvbihtYXJnaW4pIHtcbiAgICAgICAgICAgICAgcmV0dXJuIHBhcnNlRmxvYXQobWFyZ2luKTtcbiAgICAgICAgICAgIH0pKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUud2FybihbJ1BvcHBlcjogQ1NTIFwibWFyZ2luXCIgc3R5bGVzIGNhbm5vdCBiZSB1c2VkIHRvIGFwcGx5IHBhZGRpbmcnLCBcImJldHdlZW4gdGhlIHBvcHBlciBhbmQgaXRzIHJlZmVyZW5jZSBlbGVtZW50IG9yIGJvdW5kYXJ5LlwiLCBcIlRvIHJlcGxpY2F0ZSBtYXJnaW4sIHVzZSB0aGUgYG9mZnNldGAgbW9kaWZpZXIsIGFzIHdlbGwgYXNcIiwgXCJ0aGUgYHBhZGRpbmdgIG9wdGlvbiBpbiB0aGUgYHByZXZlbnRPdmVyZmxvd2AgYW5kIGBmbGlwYFwiLCBcIm1vZGlmaWVycy5cIl0uam9pbihcIiBcIikpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgICBydW5Nb2RpZmllckVmZmVjdHMoKTtcbiAgICAgICAgICByZXR1cm4gaW5zdGFuY2UudXBkYXRlKCk7XG4gICAgICAgIH0sXG4gICAgICAgIGZvcmNlVXBkYXRlOiBmdW5jdGlvbiBmb3JjZVVwZGF0ZSgpIHtcbiAgICAgICAgICBpZiAoaXNEZXN0cm95ZWQpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICB9XG4gICAgICAgICAgdmFyIF9zdGF0ZSRlbGVtZW50cyA9IHN0YXRlLmVsZW1lbnRzLCByZWZlcmVuY2UzID0gX3N0YXRlJGVsZW1lbnRzLnJlZmVyZW5jZSwgcG9wcGVyMyA9IF9zdGF0ZSRlbGVtZW50cy5wb3BwZXI7XG4gICAgICAgICAgaWYgKCFhcmVWYWxpZEVsZW1lbnRzKHJlZmVyZW5jZTMsIHBvcHBlcjMpKSB7XG4gICAgICAgICAgICBpZiAodHJ1ZSkge1xuICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKElOVkFMSURfRUxFTUVOVF9FUlJPUik7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgfVxuICAgICAgICAgIHN0YXRlLnJlY3RzID0ge1xuICAgICAgICAgICAgcmVmZXJlbmNlOiBnZXRDb21wb3NpdGVSZWN0KHJlZmVyZW5jZTMsIGdldE9mZnNldFBhcmVudChwb3BwZXIzKSwgc3RhdGUub3B0aW9ucy5zdHJhdGVneSA9PT0gXCJmaXhlZFwiKSxcbiAgICAgICAgICAgIHBvcHBlcjogZ2V0TGF5b3V0UmVjdChwb3BwZXIzKVxuICAgICAgICAgIH07XG4gICAgICAgICAgc3RhdGUucmVzZXQgPSBmYWxzZTtcbiAgICAgICAgICBzdGF0ZS5wbGFjZW1lbnQgPSBzdGF0ZS5vcHRpb25zLnBsYWNlbWVudDtcbiAgICAgICAgICBzdGF0ZS5vcmRlcmVkTW9kaWZpZXJzLmZvckVhY2goZnVuY3Rpb24obW9kaWZpZXIpIHtcbiAgICAgICAgICAgIHJldHVybiBzdGF0ZS5tb2RpZmllcnNEYXRhW21vZGlmaWVyLm5hbWVdID0gT2JqZWN0LmFzc2lnbih7fSwgbW9kaWZpZXIuZGF0YSk7XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgdmFyIF9fZGVidWdfbG9vcHNfXyA9IDA7XG4gICAgICAgICAgZm9yICh2YXIgaW5kZXggPSAwOyBpbmRleCA8IHN0YXRlLm9yZGVyZWRNb2RpZmllcnMubGVuZ3RoOyBpbmRleCsrKSB7XG4gICAgICAgICAgICBpZiAodHJ1ZSkge1xuICAgICAgICAgICAgICBfX2RlYnVnX2xvb3BzX18gKz0gMTtcbiAgICAgICAgICAgICAgaWYgKF9fZGVidWdfbG9vcHNfXyA+IDEwMCkge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoSU5GSU5JVEVfTE9PUF9FUlJPUik7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChzdGF0ZS5yZXNldCA9PT0gdHJ1ZSkge1xuICAgICAgICAgICAgICBzdGF0ZS5yZXNldCA9IGZhbHNlO1xuICAgICAgICAgICAgICBpbmRleCA9IC0xO1xuICAgICAgICAgICAgICBjb250aW51ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHZhciBfc3RhdGUkb3JkZXJlZE1vZGlmaWUgPSBzdGF0ZS5vcmRlcmVkTW9kaWZpZXJzW2luZGV4XSwgZm4gPSBfc3RhdGUkb3JkZXJlZE1vZGlmaWUuZm4sIF9zdGF0ZSRvcmRlcmVkTW9kaWZpZTIgPSBfc3RhdGUkb3JkZXJlZE1vZGlmaWUub3B0aW9ucywgX29wdGlvbnMgPSBfc3RhdGUkb3JkZXJlZE1vZGlmaWUyID09PSB2b2lkIDAgPyB7fSA6IF9zdGF0ZSRvcmRlcmVkTW9kaWZpZTIsIG5hbWUgPSBfc3RhdGUkb3JkZXJlZE1vZGlmaWUubmFtZTtcbiAgICAgICAgICAgIGlmICh0eXBlb2YgZm4gPT09IFwiZnVuY3Rpb25cIikge1xuICAgICAgICAgICAgICBzdGF0ZSA9IGZuKHtcbiAgICAgICAgICAgICAgICBzdGF0ZSxcbiAgICAgICAgICAgICAgICBvcHRpb25zOiBfb3B0aW9ucyxcbiAgICAgICAgICAgICAgICBuYW1lLFxuICAgICAgICAgICAgICAgIGluc3RhbmNlXG4gICAgICAgICAgICAgIH0pIHx8IHN0YXRlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgdXBkYXRlOiBkZWJvdW5jZShmdW5jdGlvbigpIHtcbiAgICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoZnVuY3Rpb24ocmVzb2x2ZSkge1xuICAgICAgICAgICAgaW5zdGFuY2UuZm9yY2VVcGRhdGUoKTtcbiAgICAgICAgICAgIHJlc29sdmUoc3RhdGUpO1xuICAgICAgICAgIH0pO1xuICAgICAgICB9KSxcbiAgICAgICAgZGVzdHJveTogZnVuY3Rpb24gZGVzdHJveSgpIHtcbiAgICAgICAgICBjbGVhbnVwTW9kaWZpZXJFZmZlY3RzKCk7XG4gICAgICAgICAgaXNEZXN0cm95ZWQgPSB0cnVlO1xuICAgICAgICB9XG4gICAgICB9O1xuICAgICAgaWYgKCFhcmVWYWxpZEVsZW1lbnRzKHJlZmVyZW5jZTIsIHBvcHBlcjIpKSB7XG4gICAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgICAgY29uc29sZS5lcnJvcihJTlZBTElEX0VMRU1FTlRfRVJST1IpO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBpbnN0YW5jZTtcbiAgICAgIH1cbiAgICAgIGluc3RhbmNlLnNldE9wdGlvbnMob3B0aW9ucykudGhlbihmdW5jdGlvbihzdGF0ZTIpIHtcbiAgICAgICAgaWYgKCFpc0Rlc3Ryb3llZCAmJiBvcHRpb25zLm9uRmlyc3RVcGRhdGUpIHtcbiAgICAgICAgICBvcHRpb25zLm9uRmlyc3RVcGRhdGUoc3RhdGUyKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgICBmdW5jdGlvbiBydW5Nb2RpZmllckVmZmVjdHMoKSB7XG4gICAgICAgIHN0YXRlLm9yZGVyZWRNb2RpZmllcnMuZm9yRWFjaChmdW5jdGlvbihfcmVmMykge1xuICAgICAgICAgIHZhciBuYW1lID0gX3JlZjMubmFtZSwgX3JlZjMkb3B0aW9ucyA9IF9yZWYzLm9wdGlvbnMsIG9wdGlvbnMyID0gX3JlZjMkb3B0aW9ucyA9PT0gdm9pZCAwID8ge30gOiBfcmVmMyRvcHRpb25zLCBlZmZlY3QyID0gX3JlZjMuZWZmZWN0O1xuICAgICAgICAgIGlmICh0eXBlb2YgZWZmZWN0MiA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgICAgICAgICB2YXIgY2xlYW51cEZuID0gZWZmZWN0Mih7XG4gICAgICAgICAgICAgIHN0YXRlLFxuICAgICAgICAgICAgICBuYW1lLFxuICAgICAgICAgICAgICBpbnN0YW5jZSxcbiAgICAgICAgICAgICAgb3B0aW9uczogb3B0aW9uczJcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgdmFyIG5vb3BGbiA9IGZ1bmN0aW9uIG5vb3BGbjIoKSB7XG4gICAgICAgICAgICB9O1xuICAgICAgICAgICAgZWZmZWN0Q2xlYW51cEZucy5wdXNoKGNsZWFudXBGbiB8fCBub29wRm4pO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBjbGVhbnVwTW9kaWZpZXJFZmZlY3RzKCkge1xuICAgICAgICBlZmZlY3RDbGVhbnVwRm5zLmZvckVhY2goZnVuY3Rpb24oZm4pIHtcbiAgICAgICAgICByZXR1cm4gZm4oKTtcbiAgICAgICAgfSk7XG4gICAgICAgIGVmZmVjdENsZWFudXBGbnMgPSBbXTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBpbnN0YW5jZTtcbiAgICB9O1xuICB9XG4gIHZhciBwYXNzaXZlID0ge1xuICAgIHBhc3NpdmU6IHRydWVcbiAgfTtcbiAgZnVuY3Rpb24gZWZmZWN0JDIoX3JlZikge1xuICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGUsIGluc3RhbmNlID0gX3JlZi5pbnN0YW5jZSwgb3B0aW9ucyA9IF9yZWYub3B0aW9ucztcbiAgICB2YXIgX29wdGlvbnMkc2Nyb2xsID0gb3B0aW9ucy5zY3JvbGwsIHNjcm9sbCA9IF9vcHRpb25zJHNjcm9sbCA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJHNjcm9sbCwgX29wdGlvbnMkcmVzaXplID0gb3B0aW9ucy5yZXNpemUsIHJlc2l6ZSA9IF9vcHRpb25zJHJlc2l6ZSA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJHJlc2l6ZTtcbiAgICB2YXIgd2luZG93MiA9IGdldFdpbmRvdyhzdGF0ZS5lbGVtZW50cy5wb3BwZXIpO1xuICAgIHZhciBzY3JvbGxQYXJlbnRzID0gW10uY29uY2F0KHN0YXRlLnNjcm9sbFBhcmVudHMucmVmZXJlbmNlLCBzdGF0ZS5zY3JvbGxQYXJlbnRzLnBvcHBlcik7XG4gICAgaWYgKHNjcm9sbCkge1xuICAgICAgc2Nyb2xsUGFyZW50cy5mb3JFYWNoKGZ1bmN0aW9uKHNjcm9sbFBhcmVudCkge1xuICAgICAgICBzY3JvbGxQYXJlbnQuYWRkRXZlbnRMaXN0ZW5lcihcInNjcm9sbFwiLCBpbnN0YW5jZS51cGRhdGUsIHBhc3NpdmUpO1xuICAgICAgfSk7XG4gICAgfVxuICAgIGlmIChyZXNpemUpIHtcbiAgICAgIHdpbmRvdzIuYWRkRXZlbnRMaXN0ZW5lcihcInJlc2l6ZVwiLCBpbnN0YW5jZS51cGRhdGUsIHBhc3NpdmUpO1xuICAgIH1cbiAgICByZXR1cm4gZnVuY3Rpb24oKSB7XG4gICAgICBpZiAoc2Nyb2xsKSB7XG4gICAgICAgIHNjcm9sbFBhcmVudHMuZm9yRWFjaChmdW5jdGlvbihzY3JvbGxQYXJlbnQpIHtcbiAgICAgICAgICBzY3JvbGxQYXJlbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcihcInNjcm9sbFwiLCBpbnN0YW5jZS51cGRhdGUsIHBhc3NpdmUpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmIChyZXNpemUpIHtcbiAgICAgICAgd2luZG93Mi5yZW1vdmVFdmVudExpc3RlbmVyKFwicmVzaXplXCIsIGluc3RhbmNlLnVwZGF0ZSwgcGFzc2l2ZSk7XG4gICAgICB9XG4gICAgfTtcbiAgfVxuICB2YXIgZXZlbnRMaXN0ZW5lcnMgPSB7XG4gICAgbmFtZTogXCJldmVudExpc3RlbmVyc1wiLFxuICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgcGhhc2U6IFwid3JpdGVcIixcbiAgICBmbjogZnVuY3Rpb24gZm4oKSB7XG4gICAgfSxcbiAgICBlZmZlY3Q6IGVmZmVjdCQyLFxuICAgIGRhdGE6IHt9XG4gIH07XG4gIGZ1bmN0aW9uIHBvcHBlck9mZnNldHMoX3JlZikge1xuICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGUsIG5hbWUgPSBfcmVmLm5hbWU7XG4gICAgc3RhdGUubW9kaWZpZXJzRGF0YVtuYW1lXSA9IGNvbXB1dGVPZmZzZXRzKHtcbiAgICAgIHJlZmVyZW5jZTogc3RhdGUucmVjdHMucmVmZXJlbmNlLFxuICAgICAgZWxlbWVudDogc3RhdGUucmVjdHMucG9wcGVyLFxuICAgICAgc3RyYXRlZ3k6IFwiYWJzb2x1dGVcIixcbiAgICAgIHBsYWNlbWVudDogc3RhdGUucGxhY2VtZW50XG4gICAgfSk7XG4gIH1cbiAgdmFyIHBvcHBlck9mZnNldHMkMSA9IHtcbiAgICBuYW1lOiBcInBvcHBlck9mZnNldHNcIixcbiAgICBlbmFibGVkOiB0cnVlLFxuICAgIHBoYXNlOiBcInJlYWRcIixcbiAgICBmbjogcG9wcGVyT2Zmc2V0cyxcbiAgICBkYXRhOiB7fVxuICB9O1xuICB2YXIgdW5zZXRTaWRlcyA9IHtcbiAgICB0b3A6IFwiYXV0b1wiLFxuICAgIHJpZ2h0OiBcImF1dG9cIixcbiAgICBib3R0b206IFwiYXV0b1wiLFxuICAgIGxlZnQ6IFwiYXV0b1wiXG4gIH07XG4gIGZ1bmN0aW9uIHJvdW5kT2Zmc2V0c0J5RFBSKF9yZWYpIHtcbiAgICB2YXIgeCA9IF9yZWYueCwgeSA9IF9yZWYueTtcbiAgICB2YXIgd2luID0gd2luZG93O1xuICAgIHZhciBkcHIgPSB3aW4uZGV2aWNlUGl4ZWxSYXRpbyB8fCAxO1xuICAgIHJldHVybiB7XG4gICAgICB4OiByb3VuZChyb3VuZCh4ICogZHByKSAvIGRwcikgfHwgMCxcbiAgICAgIHk6IHJvdW5kKHJvdW5kKHkgKiBkcHIpIC8gZHByKSB8fCAwXG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBtYXBUb1N0eWxlcyhfcmVmMikge1xuICAgIHZhciBfT2JqZWN0JGFzc2lnbjI7XG4gICAgdmFyIHBvcHBlcjIgPSBfcmVmMi5wb3BwZXIsIHBvcHBlclJlY3QgPSBfcmVmMi5wb3BwZXJSZWN0LCBwbGFjZW1lbnQgPSBfcmVmMi5wbGFjZW1lbnQsIG9mZnNldHMgPSBfcmVmMi5vZmZzZXRzLCBwb3NpdGlvbiA9IF9yZWYyLnBvc2l0aW9uLCBncHVBY2NlbGVyYXRpb24gPSBfcmVmMi5ncHVBY2NlbGVyYXRpb24sIGFkYXB0aXZlID0gX3JlZjIuYWRhcHRpdmUsIHJvdW5kT2Zmc2V0cyA9IF9yZWYyLnJvdW5kT2Zmc2V0cztcbiAgICB2YXIgX3JlZjMgPSByb3VuZE9mZnNldHMgPT09IHRydWUgPyByb3VuZE9mZnNldHNCeURQUihvZmZzZXRzKSA6IHR5cGVvZiByb3VuZE9mZnNldHMgPT09IFwiZnVuY3Rpb25cIiA/IHJvdW5kT2Zmc2V0cyhvZmZzZXRzKSA6IG9mZnNldHMsIF9yZWYzJHggPSBfcmVmMy54LCB4ID0gX3JlZjMkeCA9PT0gdm9pZCAwID8gMCA6IF9yZWYzJHgsIF9yZWYzJHkgPSBfcmVmMy55LCB5ID0gX3JlZjMkeSA9PT0gdm9pZCAwID8gMCA6IF9yZWYzJHk7XG4gICAgdmFyIGhhc1ggPSBvZmZzZXRzLmhhc093blByb3BlcnR5KFwieFwiKTtcbiAgICB2YXIgaGFzWSA9IG9mZnNldHMuaGFzT3duUHJvcGVydHkoXCJ5XCIpO1xuICAgIHZhciBzaWRlWCA9IGxlZnQ7XG4gICAgdmFyIHNpZGVZID0gdG9wO1xuICAgIHZhciB3aW4gPSB3aW5kb3c7XG4gICAgaWYgKGFkYXB0aXZlKSB7XG4gICAgICB2YXIgb2Zmc2V0UGFyZW50ID0gZ2V0T2Zmc2V0UGFyZW50KHBvcHBlcjIpO1xuICAgICAgdmFyIGhlaWdodFByb3AgPSBcImNsaWVudEhlaWdodFwiO1xuICAgICAgdmFyIHdpZHRoUHJvcCA9IFwiY2xpZW50V2lkdGhcIjtcbiAgICAgIGlmIChvZmZzZXRQYXJlbnQgPT09IGdldFdpbmRvdyhwb3BwZXIyKSkge1xuICAgICAgICBvZmZzZXRQYXJlbnQgPSBnZXREb2N1bWVudEVsZW1lbnQocG9wcGVyMik7XG4gICAgICAgIGlmIChnZXRDb21wdXRlZFN0eWxlKG9mZnNldFBhcmVudCkucG9zaXRpb24gIT09IFwic3RhdGljXCIpIHtcbiAgICAgICAgICBoZWlnaHRQcm9wID0gXCJzY3JvbGxIZWlnaHRcIjtcbiAgICAgICAgICB3aWR0aFByb3AgPSBcInNjcm9sbFdpZHRoXCI7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIG9mZnNldFBhcmVudCA9IG9mZnNldFBhcmVudDtcbiAgICAgIGlmIChwbGFjZW1lbnQgPT09IHRvcCkge1xuICAgICAgICBzaWRlWSA9IGJvdHRvbTtcbiAgICAgICAgeSAtPSBvZmZzZXRQYXJlbnRbaGVpZ2h0UHJvcF0gLSBwb3BwZXJSZWN0LmhlaWdodDtcbiAgICAgICAgeSAqPSBncHVBY2NlbGVyYXRpb24gPyAxIDogLTE7XG4gICAgICB9XG4gICAgICBpZiAocGxhY2VtZW50ID09PSBsZWZ0KSB7XG4gICAgICAgIHNpZGVYID0gcmlnaHQ7XG4gICAgICAgIHggLT0gb2Zmc2V0UGFyZW50W3dpZHRoUHJvcF0gLSBwb3BwZXJSZWN0LndpZHRoO1xuICAgICAgICB4ICo9IGdwdUFjY2VsZXJhdGlvbiA/IDEgOiAtMTtcbiAgICAgIH1cbiAgICB9XG4gICAgdmFyIGNvbW1vblN0eWxlcyA9IE9iamVjdC5hc3NpZ24oe1xuICAgICAgcG9zaXRpb25cbiAgICB9LCBhZGFwdGl2ZSAmJiB1bnNldFNpZGVzKTtcbiAgICBpZiAoZ3B1QWNjZWxlcmF0aW9uKSB7XG4gICAgICB2YXIgX09iamVjdCRhc3NpZ247XG4gICAgICByZXR1cm4gT2JqZWN0LmFzc2lnbih7fSwgY29tbW9uU3R5bGVzLCAoX09iamVjdCRhc3NpZ24gPSB7fSwgX09iamVjdCRhc3NpZ25bc2lkZVldID0gaGFzWSA/IFwiMFwiIDogXCJcIiwgX09iamVjdCRhc3NpZ25bc2lkZVhdID0gaGFzWCA/IFwiMFwiIDogXCJcIiwgX09iamVjdCRhc3NpZ24udHJhbnNmb3JtID0gKHdpbi5kZXZpY2VQaXhlbFJhdGlvIHx8IDEpIDwgMiA/IFwidHJhbnNsYXRlKFwiICsgeCArIFwicHgsIFwiICsgeSArIFwicHgpXCIgOiBcInRyYW5zbGF0ZTNkKFwiICsgeCArIFwicHgsIFwiICsgeSArIFwicHgsIDApXCIsIF9PYmplY3QkYXNzaWduKSk7XG4gICAgfVxuICAgIHJldHVybiBPYmplY3QuYXNzaWduKHt9LCBjb21tb25TdHlsZXMsIChfT2JqZWN0JGFzc2lnbjIgPSB7fSwgX09iamVjdCRhc3NpZ24yW3NpZGVZXSA9IGhhc1kgPyB5ICsgXCJweFwiIDogXCJcIiwgX09iamVjdCRhc3NpZ24yW3NpZGVYXSA9IGhhc1ggPyB4ICsgXCJweFwiIDogXCJcIiwgX09iamVjdCRhc3NpZ24yLnRyYW5zZm9ybSA9IFwiXCIsIF9PYmplY3QkYXNzaWduMikpO1xuICB9XG4gIGZ1bmN0aW9uIGNvbXB1dGVTdHlsZXMoX3JlZjQpIHtcbiAgICB2YXIgc3RhdGUgPSBfcmVmNC5zdGF0ZSwgb3B0aW9ucyA9IF9yZWY0Lm9wdGlvbnM7XG4gICAgdmFyIF9vcHRpb25zJGdwdUFjY2VsZXJhdCA9IG9wdGlvbnMuZ3B1QWNjZWxlcmF0aW9uLCBncHVBY2NlbGVyYXRpb24gPSBfb3B0aW9ucyRncHVBY2NlbGVyYXQgPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyRncHVBY2NlbGVyYXQsIF9vcHRpb25zJGFkYXB0aXZlID0gb3B0aW9ucy5hZGFwdGl2ZSwgYWRhcHRpdmUgPSBfb3B0aW9ucyRhZGFwdGl2ZSA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJGFkYXB0aXZlLCBfb3B0aW9ucyRyb3VuZE9mZnNldHMgPSBvcHRpb25zLnJvdW5kT2Zmc2V0cywgcm91bmRPZmZzZXRzID0gX29wdGlvbnMkcm91bmRPZmZzZXRzID09PSB2b2lkIDAgPyB0cnVlIDogX29wdGlvbnMkcm91bmRPZmZzZXRzO1xuICAgIGlmICh0cnVlKSB7XG4gICAgICB2YXIgdHJhbnNpdGlvblByb3BlcnR5ID0gZ2V0Q29tcHV0ZWRTdHlsZShzdGF0ZS5lbGVtZW50cy5wb3BwZXIpLnRyYW5zaXRpb25Qcm9wZXJ0eSB8fCBcIlwiO1xuICAgICAgaWYgKGFkYXB0aXZlICYmIFtcInRyYW5zZm9ybVwiLCBcInRvcFwiLCBcInJpZ2h0XCIsIFwiYm90dG9tXCIsIFwibGVmdFwiXS5zb21lKGZ1bmN0aW9uKHByb3BlcnR5KSB7XG4gICAgICAgIHJldHVybiB0cmFuc2l0aW9uUHJvcGVydHkuaW5kZXhPZihwcm9wZXJ0eSkgPj0gMDtcbiAgICAgIH0pKSB7XG4gICAgICAgIGNvbnNvbGUud2FybihbXCJQb3BwZXI6IERldGVjdGVkIENTUyB0cmFuc2l0aW9ucyBvbiBhdCBsZWFzdCBvbmUgb2YgdGhlIGZvbGxvd2luZ1wiLCAnQ1NTIHByb3BlcnRpZXM6IFwidHJhbnNmb3JtXCIsIFwidG9wXCIsIFwicmlnaHRcIiwgXCJib3R0b21cIiwgXCJsZWZ0XCIuJywgXCJcXG5cXG5cIiwgJ0Rpc2FibGUgdGhlIFwiY29tcHV0ZVN0eWxlc1wiIG1vZGlmaWVyXFwncyBgYWRhcHRpdmVgIG9wdGlvbiB0byBhbGxvdycsIFwiZm9yIHNtb290aCB0cmFuc2l0aW9ucywgb3IgcmVtb3ZlIHRoZXNlIHByb3BlcnRpZXMgZnJvbSB0aGUgQ1NTXCIsIFwidHJhbnNpdGlvbiBkZWNsYXJhdGlvbiBvbiB0aGUgcG9wcGVyIGVsZW1lbnQgaWYgb25seSB0cmFuc2l0aW9uaW5nXCIsIFwib3BhY2l0eSBvciBiYWNrZ3JvdW5kLWNvbG9yIGZvciBleGFtcGxlLlwiLCBcIlxcblxcblwiLCBcIldlIHJlY29tbWVuZCB1c2luZyB0aGUgcG9wcGVyIGVsZW1lbnQgYXMgYSB3cmFwcGVyIGFyb3VuZCBhbiBpbm5lclwiLCBcImVsZW1lbnQgdGhhdCBjYW4gaGF2ZSBhbnkgQ1NTIHByb3BlcnR5IHRyYW5zaXRpb25lZCBmb3IgYW5pbWF0aW9ucy5cIl0uam9pbihcIiBcIikpO1xuICAgICAgfVxuICAgIH1cbiAgICB2YXIgY29tbW9uU3R5bGVzID0ge1xuICAgICAgcGxhY2VtZW50OiBnZXRCYXNlUGxhY2VtZW50KHN0YXRlLnBsYWNlbWVudCksXG4gICAgICBwb3BwZXI6IHN0YXRlLmVsZW1lbnRzLnBvcHBlcixcbiAgICAgIHBvcHBlclJlY3Q6IHN0YXRlLnJlY3RzLnBvcHBlcixcbiAgICAgIGdwdUFjY2VsZXJhdGlvblxuICAgIH07XG4gICAgaWYgKHN0YXRlLm1vZGlmaWVyc0RhdGEucG9wcGVyT2Zmc2V0cyAhPSBudWxsKSB7XG4gICAgICBzdGF0ZS5zdHlsZXMucG9wcGVyID0gT2JqZWN0LmFzc2lnbih7fSwgc3RhdGUuc3R5bGVzLnBvcHBlciwgbWFwVG9TdHlsZXMoT2JqZWN0LmFzc2lnbih7fSwgY29tbW9uU3R5bGVzLCB7XG4gICAgICAgIG9mZnNldHM6IHN0YXRlLm1vZGlmaWVyc0RhdGEucG9wcGVyT2Zmc2V0cyxcbiAgICAgICAgcG9zaXRpb246IHN0YXRlLm9wdGlvbnMuc3RyYXRlZ3ksXG4gICAgICAgIGFkYXB0aXZlLFxuICAgICAgICByb3VuZE9mZnNldHNcbiAgICAgIH0pKSk7XG4gICAgfVxuICAgIGlmIChzdGF0ZS5tb2RpZmllcnNEYXRhLmFycm93ICE9IG51bGwpIHtcbiAgICAgIHN0YXRlLnN0eWxlcy5hcnJvdyA9IE9iamVjdC5hc3NpZ24oe30sIHN0YXRlLnN0eWxlcy5hcnJvdywgbWFwVG9TdHlsZXMoT2JqZWN0LmFzc2lnbih7fSwgY29tbW9uU3R5bGVzLCB7XG4gICAgICAgIG9mZnNldHM6IHN0YXRlLm1vZGlmaWVyc0RhdGEuYXJyb3csXG4gICAgICAgIHBvc2l0aW9uOiBcImFic29sdXRlXCIsXG4gICAgICAgIGFkYXB0aXZlOiBmYWxzZSxcbiAgICAgICAgcm91bmRPZmZzZXRzXG4gICAgICB9KSkpO1xuICAgIH1cbiAgICBzdGF0ZS5hdHRyaWJ1dGVzLnBvcHBlciA9IE9iamVjdC5hc3NpZ24oe30sIHN0YXRlLmF0dHJpYnV0ZXMucG9wcGVyLCB7XG4gICAgICBcImRhdGEtcG9wcGVyLXBsYWNlbWVudFwiOiBzdGF0ZS5wbGFjZW1lbnRcbiAgICB9KTtcbiAgfVxuICB2YXIgY29tcHV0ZVN0eWxlcyQxID0ge1xuICAgIG5hbWU6IFwiY29tcHV0ZVN0eWxlc1wiLFxuICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgcGhhc2U6IFwiYmVmb3JlV3JpdGVcIixcbiAgICBmbjogY29tcHV0ZVN0eWxlcyxcbiAgICBkYXRhOiB7fVxuICB9O1xuICBmdW5jdGlvbiBhcHBseVN0eWxlcyhfcmVmKSB7XG4gICAgdmFyIHN0YXRlID0gX3JlZi5zdGF0ZTtcbiAgICBPYmplY3Qua2V5cyhzdGF0ZS5lbGVtZW50cykuZm9yRWFjaChmdW5jdGlvbihuYW1lKSB7XG4gICAgICB2YXIgc3R5bGUgPSBzdGF0ZS5zdHlsZXNbbmFtZV0gfHwge307XG4gICAgICB2YXIgYXR0cmlidXRlcyA9IHN0YXRlLmF0dHJpYnV0ZXNbbmFtZV0gfHwge307XG4gICAgICB2YXIgZWxlbWVudCA9IHN0YXRlLmVsZW1lbnRzW25hbWVdO1xuICAgICAgaWYgKCFpc0hUTUxFbGVtZW50KGVsZW1lbnQpIHx8ICFnZXROb2RlTmFtZShlbGVtZW50KSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBPYmplY3QuYXNzaWduKGVsZW1lbnQuc3R5bGUsIHN0eWxlKTtcbiAgICAgIE9iamVjdC5rZXlzKGF0dHJpYnV0ZXMpLmZvckVhY2goZnVuY3Rpb24obmFtZTIpIHtcbiAgICAgICAgdmFyIHZhbHVlID0gYXR0cmlidXRlc1tuYW1lMl07XG4gICAgICAgIGlmICh2YWx1ZSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICBlbGVtZW50LnJlbW92ZUF0dHJpYnV0ZShuYW1lMik7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgZWxlbWVudC5zZXRBdHRyaWJ1dGUobmFtZTIsIHZhbHVlID09PSB0cnVlID8gXCJcIiA6IHZhbHVlKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gZWZmZWN0JDEoX3JlZjIpIHtcbiAgICB2YXIgc3RhdGUgPSBfcmVmMi5zdGF0ZTtcbiAgICB2YXIgaW5pdGlhbFN0eWxlcyA9IHtcbiAgICAgIHBvcHBlcjoge1xuICAgICAgICBwb3NpdGlvbjogc3RhdGUub3B0aW9ucy5zdHJhdGVneSxcbiAgICAgICAgbGVmdDogXCIwXCIsXG4gICAgICAgIHRvcDogXCIwXCIsXG4gICAgICAgIG1hcmdpbjogXCIwXCJcbiAgICAgIH0sXG4gICAgICBhcnJvdzoge1xuICAgICAgICBwb3NpdGlvbjogXCJhYnNvbHV0ZVwiXG4gICAgICB9LFxuICAgICAgcmVmZXJlbmNlOiB7fVxuICAgIH07XG4gICAgT2JqZWN0LmFzc2lnbihzdGF0ZS5lbGVtZW50cy5wb3BwZXIuc3R5bGUsIGluaXRpYWxTdHlsZXMucG9wcGVyKTtcbiAgICBzdGF0ZS5zdHlsZXMgPSBpbml0aWFsU3R5bGVzO1xuICAgIGlmIChzdGF0ZS5lbGVtZW50cy5hcnJvdykge1xuICAgICAgT2JqZWN0LmFzc2lnbihzdGF0ZS5lbGVtZW50cy5hcnJvdy5zdHlsZSwgaW5pdGlhbFN0eWxlcy5hcnJvdyk7XG4gICAgfVxuICAgIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICAgIE9iamVjdC5rZXlzKHN0YXRlLmVsZW1lbnRzKS5mb3JFYWNoKGZ1bmN0aW9uKG5hbWUpIHtcbiAgICAgICAgdmFyIGVsZW1lbnQgPSBzdGF0ZS5lbGVtZW50c1tuYW1lXTtcbiAgICAgICAgdmFyIGF0dHJpYnV0ZXMgPSBzdGF0ZS5hdHRyaWJ1dGVzW25hbWVdIHx8IHt9O1xuICAgICAgICB2YXIgc3R5bGVQcm9wZXJ0aWVzID0gT2JqZWN0LmtleXMoc3RhdGUuc3R5bGVzLmhhc093blByb3BlcnR5KG5hbWUpID8gc3RhdGUuc3R5bGVzW25hbWVdIDogaW5pdGlhbFN0eWxlc1tuYW1lXSk7XG4gICAgICAgIHZhciBzdHlsZSA9IHN0eWxlUHJvcGVydGllcy5yZWR1Y2UoZnVuY3Rpb24oc3R5bGUyLCBwcm9wZXJ0eSkge1xuICAgICAgICAgIHN0eWxlMltwcm9wZXJ0eV0gPSBcIlwiO1xuICAgICAgICAgIHJldHVybiBzdHlsZTI7XG4gICAgICAgIH0sIHt9KTtcbiAgICAgICAgaWYgKCFpc0hUTUxFbGVtZW50KGVsZW1lbnQpIHx8ICFnZXROb2RlTmFtZShlbGVtZW50KSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBPYmplY3QuYXNzaWduKGVsZW1lbnQuc3R5bGUsIHN0eWxlKTtcbiAgICAgICAgT2JqZWN0LmtleXMoYXR0cmlidXRlcykuZm9yRWFjaChmdW5jdGlvbihhdHRyaWJ1dGUpIHtcbiAgICAgICAgICBlbGVtZW50LnJlbW92ZUF0dHJpYnV0ZShhdHRyaWJ1dGUpO1xuICAgICAgICB9KTtcbiAgICAgIH0pO1xuICAgIH07XG4gIH1cbiAgdmFyIGFwcGx5U3R5bGVzJDEgPSB7XG4gICAgbmFtZTogXCJhcHBseVN0eWxlc1wiLFxuICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgcGhhc2U6IFwid3JpdGVcIixcbiAgICBmbjogYXBwbHlTdHlsZXMsXG4gICAgZWZmZWN0OiBlZmZlY3QkMSxcbiAgICByZXF1aXJlczogW1wiY29tcHV0ZVN0eWxlc1wiXVxuICB9O1xuICBmdW5jdGlvbiBkaXN0YW5jZUFuZFNraWRkaW5nVG9YWShwbGFjZW1lbnQsIHJlY3RzLCBvZmZzZXQyKSB7XG4gICAgdmFyIGJhc2VQbGFjZW1lbnQgPSBnZXRCYXNlUGxhY2VtZW50KHBsYWNlbWVudCk7XG4gICAgdmFyIGludmVydERpc3RhbmNlID0gW2xlZnQsIHRvcF0uaW5kZXhPZihiYXNlUGxhY2VtZW50KSA+PSAwID8gLTEgOiAxO1xuICAgIHZhciBfcmVmID0gdHlwZW9mIG9mZnNldDIgPT09IFwiZnVuY3Rpb25cIiA/IG9mZnNldDIoT2JqZWN0LmFzc2lnbih7fSwgcmVjdHMsIHtcbiAgICAgIHBsYWNlbWVudFxuICAgIH0pKSA6IG9mZnNldDIsIHNraWRkaW5nID0gX3JlZlswXSwgZGlzdGFuY2UgPSBfcmVmWzFdO1xuICAgIHNraWRkaW5nID0gc2tpZGRpbmcgfHwgMDtcbiAgICBkaXN0YW5jZSA9IChkaXN0YW5jZSB8fCAwKSAqIGludmVydERpc3RhbmNlO1xuICAgIHJldHVybiBbbGVmdCwgcmlnaHRdLmluZGV4T2YoYmFzZVBsYWNlbWVudCkgPj0gMCA/IHtcbiAgICAgIHg6IGRpc3RhbmNlLFxuICAgICAgeTogc2tpZGRpbmdcbiAgICB9IDoge1xuICAgICAgeDogc2tpZGRpbmcsXG4gICAgICB5OiBkaXN0YW5jZVxuICAgIH07XG4gIH1cbiAgZnVuY3Rpb24gb2Zmc2V0KF9yZWYyKSB7XG4gICAgdmFyIHN0YXRlID0gX3JlZjIuc3RhdGUsIG9wdGlvbnMgPSBfcmVmMi5vcHRpb25zLCBuYW1lID0gX3JlZjIubmFtZTtcbiAgICB2YXIgX29wdGlvbnMkb2Zmc2V0ID0gb3B0aW9ucy5vZmZzZXQsIG9mZnNldDIgPSBfb3B0aW9ucyRvZmZzZXQgPT09IHZvaWQgMCA/IFswLCAwXSA6IF9vcHRpb25zJG9mZnNldDtcbiAgICB2YXIgZGF0YSA9IHBsYWNlbWVudHMucmVkdWNlKGZ1bmN0aW9uKGFjYywgcGxhY2VtZW50KSB7XG4gICAgICBhY2NbcGxhY2VtZW50XSA9IGRpc3RhbmNlQW5kU2tpZGRpbmdUb1hZKHBsYWNlbWVudCwgc3RhdGUucmVjdHMsIG9mZnNldDIpO1xuICAgICAgcmV0dXJuIGFjYztcbiAgICB9LCB7fSk7XG4gICAgdmFyIF9kYXRhJHN0YXRlJHBsYWNlbWVudCA9IGRhdGFbc3RhdGUucGxhY2VtZW50XSwgeCA9IF9kYXRhJHN0YXRlJHBsYWNlbWVudC54LCB5ID0gX2RhdGEkc3RhdGUkcGxhY2VtZW50Lnk7XG4gICAgaWYgKHN0YXRlLm1vZGlmaWVyc0RhdGEucG9wcGVyT2Zmc2V0cyAhPSBudWxsKSB7XG4gICAgICBzdGF0ZS5tb2RpZmllcnNEYXRhLnBvcHBlck9mZnNldHMueCArPSB4O1xuICAgICAgc3RhdGUubW9kaWZpZXJzRGF0YS5wb3BwZXJPZmZzZXRzLnkgKz0geTtcbiAgICB9XG4gICAgc3RhdGUubW9kaWZpZXJzRGF0YVtuYW1lXSA9IGRhdGE7XG4gIH1cbiAgdmFyIG9mZnNldCQxID0ge1xuICAgIG5hbWU6IFwib2Zmc2V0XCIsXG4gICAgZW5hYmxlZDogdHJ1ZSxcbiAgICBwaGFzZTogXCJtYWluXCIsXG4gICAgcmVxdWlyZXM6IFtcInBvcHBlck9mZnNldHNcIl0sXG4gICAgZm46IG9mZnNldFxuICB9O1xuICB2YXIgaGFzaCQxID0ge1xuICAgIGxlZnQ6IFwicmlnaHRcIixcbiAgICByaWdodDogXCJsZWZ0XCIsXG4gICAgYm90dG9tOiBcInRvcFwiLFxuICAgIHRvcDogXCJib3R0b21cIlxuICB9O1xuICBmdW5jdGlvbiBnZXRPcHBvc2l0ZVBsYWNlbWVudChwbGFjZW1lbnQpIHtcbiAgICByZXR1cm4gcGxhY2VtZW50LnJlcGxhY2UoL2xlZnR8cmlnaHR8Ym90dG9tfHRvcC9nLCBmdW5jdGlvbihtYXRjaGVkKSB7XG4gICAgICByZXR1cm4gaGFzaCQxW21hdGNoZWRdO1xuICAgIH0pO1xuICB9XG4gIHZhciBoYXNoID0ge1xuICAgIHN0YXJ0OiBcImVuZFwiLFxuICAgIGVuZDogXCJzdGFydFwiXG4gIH07XG4gIGZ1bmN0aW9uIGdldE9wcG9zaXRlVmFyaWF0aW9uUGxhY2VtZW50KHBsYWNlbWVudCkge1xuICAgIHJldHVybiBwbGFjZW1lbnQucmVwbGFjZSgvc3RhcnR8ZW5kL2csIGZ1bmN0aW9uKG1hdGNoZWQpIHtcbiAgICAgIHJldHVybiBoYXNoW21hdGNoZWRdO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGNvbXB1dGVBdXRvUGxhY2VtZW50KHN0YXRlLCBvcHRpb25zKSB7XG4gICAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgICAgb3B0aW9ucyA9IHt9O1xuICAgIH1cbiAgICB2YXIgX29wdGlvbnMgPSBvcHRpb25zLCBwbGFjZW1lbnQgPSBfb3B0aW9ucy5wbGFjZW1lbnQsIGJvdW5kYXJ5ID0gX29wdGlvbnMuYm91bmRhcnksIHJvb3RCb3VuZGFyeSA9IF9vcHRpb25zLnJvb3RCb3VuZGFyeSwgcGFkZGluZyA9IF9vcHRpb25zLnBhZGRpbmcsIGZsaXBWYXJpYXRpb25zID0gX29wdGlvbnMuZmxpcFZhcmlhdGlvbnMsIF9vcHRpb25zJGFsbG93ZWRBdXRvUCA9IF9vcHRpb25zLmFsbG93ZWRBdXRvUGxhY2VtZW50cywgYWxsb3dlZEF1dG9QbGFjZW1lbnRzID0gX29wdGlvbnMkYWxsb3dlZEF1dG9QID09PSB2b2lkIDAgPyBwbGFjZW1lbnRzIDogX29wdGlvbnMkYWxsb3dlZEF1dG9QO1xuICAgIHZhciB2YXJpYXRpb24gPSBnZXRWYXJpYXRpb24ocGxhY2VtZW50KTtcbiAgICB2YXIgcGxhY2VtZW50cyQxID0gdmFyaWF0aW9uID8gZmxpcFZhcmlhdGlvbnMgPyB2YXJpYXRpb25QbGFjZW1lbnRzIDogdmFyaWF0aW9uUGxhY2VtZW50cy5maWx0ZXIoZnVuY3Rpb24ocGxhY2VtZW50Mikge1xuICAgICAgcmV0dXJuIGdldFZhcmlhdGlvbihwbGFjZW1lbnQyKSA9PT0gdmFyaWF0aW9uO1xuICAgIH0pIDogYmFzZVBsYWNlbWVudHM7XG4gICAgdmFyIGFsbG93ZWRQbGFjZW1lbnRzID0gcGxhY2VtZW50cyQxLmZpbHRlcihmdW5jdGlvbihwbGFjZW1lbnQyKSB7XG4gICAgICByZXR1cm4gYWxsb3dlZEF1dG9QbGFjZW1lbnRzLmluZGV4T2YocGxhY2VtZW50MikgPj0gMDtcbiAgICB9KTtcbiAgICBpZiAoYWxsb3dlZFBsYWNlbWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICBhbGxvd2VkUGxhY2VtZW50cyA9IHBsYWNlbWVudHMkMTtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIGNvbnNvbGUuZXJyb3IoW1wiUG9wcGVyOiBUaGUgYGFsbG93ZWRBdXRvUGxhY2VtZW50c2Agb3B0aW9uIGRpZCBub3QgYWxsb3cgYW55XCIsIFwicGxhY2VtZW50cy4gRW5zdXJlIHRoZSBgcGxhY2VtZW50YCBvcHRpb24gbWF0Y2hlcyB0aGUgdmFyaWF0aW9uXCIsIFwib2YgdGhlIGFsbG93ZWQgcGxhY2VtZW50cy5cIiwgJ0ZvciBleGFtcGxlLCBcImF1dG9cIiBjYW5ub3QgYmUgdXNlZCB0byBhbGxvdyBcImJvdHRvbS1zdGFydFwiLicsICdVc2UgXCJhdXRvLXN0YXJ0XCIgaW5zdGVhZC4nXS5qb2luKFwiIFwiKSk7XG4gICAgICB9XG4gICAgfVxuICAgIHZhciBvdmVyZmxvd3MgPSBhbGxvd2VkUGxhY2VtZW50cy5yZWR1Y2UoZnVuY3Rpb24oYWNjLCBwbGFjZW1lbnQyKSB7XG4gICAgICBhY2NbcGxhY2VtZW50Ml0gPSBkZXRlY3RPdmVyZmxvdyhzdGF0ZSwge1xuICAgICAgICBwbGFjZW1lbnQ6IHBsYWNlbWVudDIsXG4gICAgICAgIGJvdW5kYXJ5LFxuICAgICAgICByb290Qm91bmRhcnksXG4gICAgICAgIHBhZGRpbmdcbiAgICAgIH0pW2dldEJhc2VQbGFjZW1lbnQocGxhY2VtZW50MildO1xuICAgICAgcmV0dXJuIGFjYztcbiAgICB9LCB7fSk7XG4gICAgcmV0dXJuIE9iamVjdC5rZXlzKG92ZXJmbG93cykuc29ydChmdW5jdGlvbihhLCBiKSB7XG4gICAgICByZXR1cm4gb3ZlcmZsb3dzW2FdIC0gb3ZlcmZsb3dzW2JdO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGdldEV4cGFuZGVkRmFsbGJhY2tQbGFjZW1lbnRzKHBsYWNlbWVudCkge1xuICAgIGlmIChnZXRCYXNlUGxhY2VtZW50KHBsYWNlbWVudCkgPT09IGF1dG8pIHtcbiAgICAgIHJldHVybiBbXTtcbiAgICB9XG4gICAgdmFyIG9wcG9zaXRlUGxhY2VtZW50ID0gZ2V0T3Bwb3NpdGVQbGFjZW1lbnQocGxhY2VtZW50KTtcbiAgICByZXR1cm4gW2dldE9wcG9zaXRlVmFyaWF0aW9uUGxhY2VtZW50KHBsYWNlbWVudCksIG9wcG9zaXRlUGxhY2VtZW50LCBnZXRPcHBvc2l0ZVZhcmlhdGlvblBsYWNlbWVudChvcHBvc2l0ZVBsYWNlbWVudCldO1xuICB9XG4gIGZ1bmN0aW9uIGZsaXAoX3JlZikge1xuICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGUsIG9wdGlvbnMgPSBfcmVmLm9wdGlvbnMsIG5hbWUgPSBfcmVmLm5hbWU7XG4gICAgaWYgKHN0YXRlLm1vZGlmaWVyc0RhdGFbbmFtZV0uX3NraXApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdmFyIF9vcHRpb25zJG1haW5BeGlzID0gb3B0aW9ucy5tYWluQXhpcywgY2hlY2tNYWluQXhpcyA9IF9vcHRpb25zJG1haW5BeGlzID09PSB2b2lkIDAgPyB0cnVlIDogX29wdGlvbnMkbWFpbkF4aXMsIF9vcHRpb25zJGFsdEF4aXMgPSBvcHRpb25zLmFsdEF4aXMsIGNoZWNrQWx0QXhpcyA9IF9vcHRpb25zJGFsdEF4aXMgPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyRhbHRBeGlzLCBzcGVjaWZpZWRGYWxsYmFja1BsYWNlbWVudHMgPSBvcHRpb25zLmZhbGxiYWNrUGxhY2VtZW50cywgcGFkZGluZyA9IG9wdGlvbnMucGFkZGluZywgYm91bmRhcnkgPSBvcHRpb25zLmJvdW5kYXJ5LCByb290Qm91bmRhcnkgPSBvcHRpb25zLnJvb3RCb3VuZGFyeSwgYWx0Qm91bmRhcnkgPSBvcHRpb25zLmFsdEJvdW5kYXJ5LCBfb3B0aW9ucyRmbGlwVmFyaWF0aW8gPSBvcHRpb25zLmZsaXBWYXJpYXRpb25zLCBmbGlwVmFyaWF0aW9ucyA9IF9vcHRpb25zJGZsaXBWYXJpYXRpbyA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJGZsaXBWYXJpYXRpbywgYWxsb3dlZEF1dG9QbGFjZW1lbnRzID0gb3B0aW9ucy5hbGxvd2VkQXV0b1BsYWNlbWVudHM7XG4gICAgdmFyIHByZWZlcnJlZFBsYWNlbWVudCA9IHN0YXRlLm9wdGlvbnMucGxhY2VtZW50O1xuICAgIHZhciBiYXNlUGxhY2VtZW50ID0gZ2V0QmFzZVBsYWNlbWVudChwcmVmZXJyZWRQbGFjZW1lbnQpO1xuICAgIHZhciBpc0Jhc2VQbGFjZW1lbnQgPSBiYXNlUGxhY2VtZW50ID09PSBwcmVmZXJyZWRQbGFjZW1lbnQ7XG4gICAgdmFyIGZhbGxiYWNrUGxhY2VtZW50cyA9IHNwZWNpZmllZEZhbGxiYWNrUGxhY2VtZW50cyB8fCAoaXNCYXNlUGxhY2VtZW50IHx8ICFmbGlwVmFyaWF0aW9ucyA/IFtnZXRPcHBvc2l0ZVBsYWNlbWVudChwcmVmZXJyZWRQbGFjZW1lbnQpXSA6IGdldEV4cGFuZGVkRmFsbGJhY2tQbGFjZW1lbnRzKHByZWZlcnJlZFBsYWNlbWVudCkpO1xuICAgIHZhciBwbGFjZW1lbnRzMiA9IFtwcmVmZXJyZWRQbGFjZW1lbnRdLmNvbmNhdChmYWxsYmFja1BsYWNlbWVudHMpLnJlZHVjZShmdW5jdGlvbihhY2MsIHBsYWNlbWVudDIpIHtcbiAgICAgIHJldHVybiBhY2MuY29uY2F0KGdldEJhc2VQbGFjZW1lbnQocGxhY2VtZW50MikgPT09IGF1dG8gPyBjb21wdXRlQXV0b1BsYWNlbWVudChzdGF0ZSwge1xuICAgICAgICBwbGFjZW1lbnQ6IHBsYWNlbWVudDIsXG4gICAgICAgIGJvdW5kYXJ5LFxuICAgICAgICByb290Qm91bmRhcnksXG4gICAgICAgIHBhZGRpbmcsXG4gICAgICAgIGZsaXBWYXJpYXRpb25zLFxuICAgICAgICBhbGxvd2VkQXV0b1BsYWNlbWVudHNcbiAgICAgIH0pIDogcGxhY2VtZW50Mik7XG4gICAgfSwgW10pO1xuICAgIHZhciByZWZlcmVuY2VSZWN0ID0gc3RhdGUucmVjdHMucmVmZXJlbmNlO1xuICAgIHZhciBwb3BwZXJSZWN0ID0gc3RhdGUucmVjdHMucG9wcGVyO1xuICAgIHZhciBjaGVja3NNYXAgPSBuZXcgTWFwKCk7XG4gICAgdmFyIG1ha2VGYWxsYmFja0NoZWNrcyA9IHRydWU7XG4gICAgdmFyIGZpcnN0Rml0dGluZ1BsYWNlbWVudCA9IHBsYWNlbWVudHMyWzBdO1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgcGxhY2VtZW50czIubGVuZ3RoOyBpKyspIHtcbiAgICAgIHZhciBwbGFjZW1lbnQgPSBwbGFjZW1lbnRzMltpXTtcbiAgICAgIHZhciBfYmFzZVBsYWNlbWVudCA9IGdldEJhc2VQbGFjZW1lbnQocGxhY2VtZW50KTtcbiAgICAgIHZhciBpc1N0YXJ0VmFyaWF0aW9uID0gZ2V0VmFyaWF0aW9uKHBsYWNlbWVudCkgPT09IHN0YXJ0O1xuICAgICAgdmFyIGlzVmVydGljYWwgPSBbdG9wLCBib3R0b21dLmluZGV4T2YoX2Jhc2VQbGFjZW1lbnQpID49IDA7XG4gICAgICB2YXIgbGVuID0gaXNWZXJ0aWNhbCA/IFwid2lkdGhcIiA6IFwiaGVpZ2h0XCI7XG4gICAgICB2YXIgb3ZlcmZsb3cgPSBkZXRlY3RPdmVyZmxvdyhzdGF0ZSwge1xuICAgICAgICBwbGFjZW1lbnQsXG4gICAgICAgIGJvdW5kYXJ5LFxuICAgICAgICByb290Qm91bmRhcnksXG4gICAgICAgIGFsdEJvdW5kYXJ5LFxuICAgICAgICBwYWRkaW5nXG4gICAgICB9KTtcbiAgICAgIHZhciBtYWluVmFyaWF0aW9uU2lkZSA9IGlzVmVydGljYWwgPyBpc1N0YXJ0VmFyaWF0aW9uID8gcmlnaHQgOiBsZWZ0IDogaXNTdGFydFZhcmlhdGlvbiA/IGJvdHRvbSA6IHRvcDtcbiAgICAgIGlmIChyZWZlcmVuY2VSZWN0W2xlbl0gPiBwb3BwZXJSZWN0W2xlbl0pIHtcbiAgICAgICAgbWFpblZhcmlhdGlvblNpZGUgPSBnZXRPcHBvc2l0ZVBsYWNlbWVudChtYWluVmFyaWF0aW9uU2lkZSk7XG4gICAgICB9XG4gICAgICB2YXIgYWx0VmFyaWF0aW9uU2lkZSA9IGdldE9wcG9zaXRlUGxhY2VtZW50KG1haW5WYXJpYXRpb25TaWRlKTtcbiAgICAgIHZhciBjaGVja3MgPSBbXTtcbiAgICAgIGlmIChjaGVja01haW5BeGlzKSB7XG4gICAgICAgIGNoZWNrcy5wdXNoKG92ZXJmbG93W19iYXNlUGxhY2VtZW50XSA8PSAwKTtcbiAgICAgIH1cbiAgICAgIGlmIChjaGVja0FsdEF4aXMpIHtcbiAgICAgICAgY2hlY2tzLnB1c2gob3ZlcmZsb3dbbWFpblZhcmlhdGlvblNpZGVdIDw9IDAsIG92ZXJmbG93W2FsdFZhcmlhdGlvblNpZGVdIDw9IDApO1xuICAgICAgfVxuICAgICAgaWYgKGNoZWNrcy5ldmVyeShmdW5jdGlvbihjaGVjaykge1xuICAgICAgICByZXR1cm4gY2hlY2s7XG4gICAgICB9KSkge1xuICAgICAgICBmaXJzdEZpdHRpbmdQbGFjZW1lbnQgPSBwbGFjZW1lbnQ7XG4gICAgICAgIG1ha2VGYWxsYmFja0NoZWNrcyA9IGZhbHNlO1xuICAgICAgICBicmVhaztcbiAgICAgIH1cbiAgICAgIGNoZWNrc01hcC5zZXQocGxhY2VtZW50LCBjaGVja3MpO1xuICAgIH1cbiAgICBpZiAobWFrZUZhbGxiYWNrQ2hlY2tzKSB7XG4gICAgICB2YXIgbnVtYmVyT2ZDaGVja3MgPSBmbGlwVmFyaWF0aW9ucyA/IDMgOiAxO1xuICAgICAgdmFyIF9sb29wID0gZnVuY3Rpb24gX2xvb3AyKF9pMikge1xuICAgICAgICB2YXIgZml0dGluZ1BsYWNlbWVudCA9IHBsYWNlbWVudHMyLmZpbmQoZnVuY3Rpb24ocGxhY2VtZW50Mikge1xuICAgICAgICAgIHZhciBjaGVja3MyID0gY2hlY2tzTWFwLmdldChwbGFjZW1lbnQyKTtcbiAgICAgICAgICBpZiAoY2hlY2tzMikge1xuICAgICAgICAgICAgcmV0dXJuIGNoZWNrczIuc2xpY2UoMCwgX2kyKS5ldmVyeShmdW5jdGlvbihjaGVjaykge1xuICAgICAgICAgICAgICByZXR1cm4gY2hlY2s7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgICBpZiAoZml0dGluZ1BsYWNlbWVudCkge1xuICAgICAgICAgIGZpcnN0Rml0dGluZ1BsYWNlbWVudCA9IGZpdHRpbmdQbGFjZW1lbnQ7XG4gICAgICAgICAgcmV0dXJuIFwiYnJlYWtcIjtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICAgIGZvciAodmFyIF9pID0gbnVtYmVyT2ZDaGVja3M7IF9pID4gMDsgX2ktLSkge1xuICAgICAgICB2YXIgX3JldCA9IF9sb29wKF9pKTtcbiAgICAgICAgaWYgKF9yZXQgPT09IFwiYnJlYWtcIilcbiAgICAgICAgICBicmVhaztcbiAgICAgIH1cbiAgICB9XG4gICAgaWYgKHN0YXRlLnBsYWNlbWVudCAhPT0gZmlyc3RGaXR0aW5nUGxhY2VtZW50KSB7XG4gICAgICBzdGF0ZS5tb2RpZmllcnNEYXRhW25hbWVdLl9za2lwID0gdHJ1ZTtcbiAgICAgIHN0YXRlLnBsYWNlbWVudCA9IGZpcnN0Rml0dGluZ1BsYWNlbWVudDtcbiAgICAgIHN0YXRlLnJlc2V0ID0gdHJ1ZTtcbiAgICB9XG4gIH1cbiAgdmFyIGZsaXAkMSA9IHtcbiAgICBuYW1lOiBcImZsaXBcIixcbiAgICBlbmFibGVkOiB0cnVlLFxuICAgIHBoYXNlOiBcIm1haW5cIixcbiAgICBmbjogZmxpcCxcbiAgICByZXF1aXJlc0lmRXhpc3RzOiBbXCJvZmZzZXRcIl0sXG4gICAgZGF0YToge1xuICAgICAgX3NraXA6IGZhbHNlXG4gICAgfVxuICB9O1xuICBmdW5jdGlvbiBnZXRBbHRBeGlzKGF4aXMpIHtcbiAgICByZXR1cm4gYXhpcyA9PT0gXCJ4XCIgPyBcInlcIiA6IFwieFwiO1xuICB9XG4gIGZ1bmN0aW9uIHdpdGhpbihtaW4kMSwgdmFsdWUsIG1heCQxKSB7XG4gICAgcmV0dXJuIG1heChtaW4kMSwgbWluKHZhbHVlLCBtYXgkMSkpO1xuICB9XG4gIGZ1bmN0aW9uIHByZXZlbnRPdmVyZmxvdyhfcmVmKSB7XG4gICAgdmFyIHN0YXRlID0gX3JlZi5zdGF0ZSwgb3B0aW9ucyA9IF9yZWYub3B0aW9ucywgbmFtZSA9IF9yZWYubmFtZTtcbiAgICB2YXIgX29wdGlvbnMkbWFpbkF4aXMgPSBvcHRpb25zLm1haW5BeGlzLCBjaGVja01haW5BeGlzID0gX29wdGlvbnMkbWFpbkF4aXMgPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyRtYWluQXhpcywgX29wdGlvbnMkYWx0QXhpcyA9IG9wdGlvbnMuYWx0QXhpcywgY2hlY2tBbHRBeGlzID0gX29wdGlvbnMkYWx0QXhpcyA9PT0gdm9pZCAwID8gZmFsc2UgOiBfb3B0aW9ucyRhbHRBeGlzLCBib3VuZGFyeSA9IG9wdGlvbnMuYm91bmRhcnksIHJvb3RCb3VuZGFyeSA9IG9wdGlvbnMucm9vdEJvdW5kYXJ5LCBhbHRCb3VuZGFyeSA9IG9wdGlvbnMuYWx0Qm91bmRhcnksIHBhZGRpbmcgPSBvcHRpb25zLnBhZGRpbmcsIF9vcHRpb25zJHRldGhlciA9IG9wdGlvbnMudGV0aGVyLCB0ZXRoZXIgPSBfb3B0aW9ucyR0ZXRoZXIgPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyR0ZXRoZXIsIF9vcHRpb25zJHRldGhlck9mZnNldCA9IG9wdGlvbnMudGV0aGVyT2Zmc2V0LCB0ZXRoZXJPZmZzZXQgPSBfb3B0aW9ucyR0ZXRoZXJPZmZzZXQgPT09IHZvaWQgMCA/IDAgOiBfb3B0aW9ucyR0ZXRoZXJPZmZzZXQ7XG4gICAgdmFyIG92ZXJmbG93ID0gZGV0ZWN0T3ZlcmZsb3coc3RhdGUsIHtcbiAgICAgIGJvdW5kYXJ5LFxuICAgICAgcm9vdEJvdW5kYXJ5LFxuICAgICAgcGFkZGluZyxcbiAgICAgIGFsdEJvdW5kYXJ5XG4gICAgfSk7XG4gICAgdmFyIGJhc2VQbGFjZW1lbnQgPSBnZXRCYXNlUGxhY2VtZW50KHN0YXRlLnBsYWNlbWVudCk7XG4gICAgdmFyIHZhcmlhdGlvbiA9IGdldFZhcmlhdGlvbihzdGF0ZS5wbGFjZW1lbnQpO1xuICAgIHZhciBpc0Jhc2VQbGFjZW1lbnQgPSAhdmFyaWF0aW9uO1xuICAgIHZhciBtYWluQXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChiYXNlUGxhY2VtZW50KTtcbiAgICB2YXIgYWx0QXhpcyA9IGdldEFsdEF4aXMobWFpbkF4aXMpO1xuICAgIHZhciBwb3BwZXJPZmZzZXRzMiA9IHN0YXRlLm1vZGlmaWVyc0RhdGEucG9wcGVyT2Zmc2V0cztcbiAgICB2YXIgcmVmZXJlbmNlUmVjdCA9IHN0YXRlLnJlY3RzLnJlZmVyZW5jZTtcbiAgICB2YXIgcG9wcGVyUmVjdCA9IHN0YXRlLnJlY3RzLnBvcHBlcjtcbiAgICB2YXIgdGV0aGVyT2Zmc2V0VmFsdWUgPSB0eXBlb2YgdGV0aGVyT2Zmc2V0ID09PSBcImZ1bmN0aW9uXCIgPyB0ZXRoZXJPZmZzZXQoT2JqZWN0LmFzc2lnbih7fSwgc3RhdGUucmVjdHMsIHtcbiAgICAgIHBsYWNlbWVudDogc3RhdGUucGxhY2VtZW50XG4gICAgfSkpIDogdGV0aGVyT2Zmc2V0O1xuICAgIHZhciBkYXRhID0ge1xuICAgICAgeDogMCxcbiAgICAgIHk6IDBcbiAgICB9O1xuICAgIGlmICghcG9wcGVyT2Zmc2V0czIpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKGNoZWNrTWFpbkF4aXMgfHwgY2hlY2tBbHRBeGlzKSB7XG4gICAgICB2YXIgbWFpblNpZGUgPSBtYWluQXhpcyA9PT0gXCJ5XCIgPyB0b3AgOiBsZWZ0O1xuICAgICAgdmFyIGFsdFNpZGUgPSBtYWluQXhpcyA9PT0gXCJ5XCIgPyBib3R0b20gOiByaWdodDtcbiAgICAgIHZhciBsZW4gPSBtYWluQXhpcyA9PT0gXCJ5XCIgPyBcImhlaWdodFwiIDogXCJ3aWR0aFwiO1xuICAgICAgdmFyIG9mZnNldDIgPSBwb3BwZXJPZmZzZXRzMlttYWluQXhpc107XG4gICAgICB2YXIgbWluJDEgPSBwb3BwZXJPZmZzZXRzMlttYWluQXhpc10gKyBvdmVyZmxvd1ttYWluU2lkZV07XG4gICAgICB2YXIgbWF4JDEgPSBwb3BwZXJPZmZzZXRzMlttYWluQXhpc10gLSBvdmVyZmxvd1thbHRTaWRlXTtcbiAgICAgIHZhciBhZGRpdGl2ZSA9IHRldGhlciA/IC1wb3BwZXJSZWN0W2xlbl0gLyAyIDogMDtcbiAgICAgIHZhciBtaW5MZW4gPSB2YXJpYXRpb24gPT09IHN0YXJ0ID8gcmVmZXJlbmNlUmVjdFtsZW5dIDogcG9wcGVyUmVjdFtsZW5dO1xuICAgICAgdmFyIG1heExlbiA9IHZhcmlhdGlvbiA9PT0gc3RhcnQgPyAtcG9wcGVyUmVjdFtsZW5dIDogLXJlZmVyZW5jZVJlY3RbbGVuXTtcbiAgICAgIHZhciBhcnJvd0VsZW1lbnQgPSBzdGF0ZS5lbGVtZW50cy5hcnJvdztcbiAgICAgIHZhciBhcnJvd1JlY3QgPSB0ZXRoZXIgJiYgYXJyb3dFbGVtZW50ID8gZ2V0TGF5b3V0UmVjdChhcnJvd0VsZW1lbnQpIDoge1xuICAgICAgICB3aWR0aDogMCxcbiAgICAgICAgaGVpZ2h0OiAwXG4gICAgICB9O1xuICAgICAgdmFyIGFycm93UGFkZGluZ09iamVjdCA9IHN0YXRlLm1vZGlmaWVyc0RhdGFbXCJhcnJvdyNwZXJzaXN0ZW50XCJdID8gc3RhdGUubW9kaWZpZXJzRGF0YVtcImFycm93I3BlcnNpc3RlbnRcIl0ucGFkZGluZyA6IGdldEZyZXNoU2lkZU9iamVjdCgpO1xuICAgICAgdmFyIGFycm93UGFkZGluZ01pbiA9IGFycm93UGFkZGluZ09iamVjdFttYWluU2lkZV07XG4gICAgICB2YXIgYXJyb3dQYWRkaW5nTWF4ID0gYXJyb3dQYWRkaW5nT2JqZWN0W2FsdFNpZGVdO1xuICAgICAgdmFyIGFycm93TGVuID0gd2l0aGluKDAsIHJlZmVyZW5jZVJlY3RbbGVuXSwgYXJyb3dSZWN0W2xlbl0pO1xuICAgICAgdmFyIG1pbk9mZnNldCA9IGlzQmFzZVBsYWNlbWVudCA/IHJlZmVyZW5jZVJlY3RbbGVuXSAvIDIgLSBhZGRpdGl2ZSAtIGFycm93TGVuIC0gYXJyb3dQYWRkaW5nTWluIC0gdGV0aGVyT2Zmc2V0VmFsdWUgOiBtaW5MZW4gLSBhcnJvd0xlbiAtIGFycm93UGFkZGluZ01pbiAtIHRldGhlck9mZnNldFZhbHVlO1xuICAgICAgdmFyIG1heE9mZnNldCA9IGlzQmFzZVBsYWNlbWVudCA/IC1yZWZlcmVuY2VSZWN0W2xlbl0gLyAyICsgYWRkaXRpdmUgKyBhcnJvd0xlbiArIGFycm93UGFkZGluZ01heCArIHRldGhlck9mZnNldFZhbHVlIDogbWF4TGVuICsgYXJyb3dMZW4gKyBhcnJvd1BhZGRpbmdNYXggKyB0ZXRoZXJPZmZzZXRWYWx1ZTtcbiAgICAgIHZhciBhcnJvd09mZnNldFBhcmVudCA9IHN0YXRlLmVsZW1lbnRzLmFycm93ICYmIGdldE9mZnNldFBhcmVudChzdGF0ZS5lbGVtZW50cy5hcnJvdyk7XG4gICAgICB2YXIgY2xpZW50T2Zmc2V0ID0gYXJyb3dPZmZzZXRQYXJlbnQgPyBtYWluQXhpcyA9PT0gXCJ5XCIgPyBhcnJvd09mZnNldFBhcmVudC5jbGllbnRUb3AgfHwgMCA6IGFycm93T2Zmc2V0UGFyZW50LmNsaWVudExlZnQgfHwgMCA6IDA7XG4gICAgICB2YXIgb2Zmc2V0TW9kaWZpZXJWYWx1ZSA9IHN0YXRlLm1vZGlmaWVyc0RhdGEub2Zmc2V0ID8gc3RhdGUubW9kaWZpZXJzRGF0YS5vZmZzZXRbc3RhdGUucGxhY2VtZW50XVttYWluQXhpc10gOiAwO1xuICAgICAgdmFyIHRldGhlck1pbiA9IHBvcHBlck9mZnNldHMyW21haW5BeGlzXSArIG1pbk9mZnNldCAtIG9mZnNldE1vZGlmaWVyVmFsdWUgLSBjbGllbnRPZmZzZXQ7XG4gICAgICB2YXIgdGV0aGVyTWF4ID0gcG9wcGVyT2Zmc2V0czJbbWFpbkF4aXNdICsgbWF4T2Zmc2V0IC0gb2Zmc2V0TW9kaWZpZXJWYWx1ZTtcbiAgICAgIGlmIChjaGVja01haW5BeGlzKSB7XG4gICAgICAgIHZhciBwcmV2ZW50ZWRPZmZzZXQgPSB3aXRoaW4odGV0aGVyID8gbWluKG1pbiQxLCB0ZXRoZXJNaW4pIDogbWluJDEsIG9mZnNldDIsIHRldGhlciA/IG1heChtYXgkMSwgdGV0aGVyTWF4KSA6IG1heCQxKTtcbiAgICAgICAgcG9wcGVyT2Zmc2V0czJbbWFpbkF4aXNdID0gcHJldmVudGVkT2Zmc2V0O1xuICAgICAgICBkYXRhW21haW5BeGlzXSA9IHByZXZlbnRlZE9mZnNldCAtIG9mZnNldDI7XG4gICAgICB9XG4gICAgICBpZiAoY2hlY2tBbHRBeGlzKSB7XG4gICAgICAgIHZhciBfbWFpblNpZGUgPSBtYWluQXhpcyA9PT0gXCJ4XCIgPyB0b3AgOiBsZWZ0O1xuICAgICAgICB2YXIgX2FsdFNpZGUgPSBtYWluQXhpcyA9PT0gXCJ4XCIgPyBib3R0b20gOiByaWdodDtcbiAgICAgICAgdmFyIF9vZmZzZXQgPSBwb3BwZXJPZmZzZXRzMlthbHRBeGlzXTtcbiAgICAgICAgdmFyIF9taW4gPSBfb2Zmc2V0ICsgb3ZlcmZsb3dbX21haW5TaWRlXTtcbiAgICAgICAgdmFyIF9tYXggPSBfb2Zmc2V0IC0gb3ZlcmZsb3dbX2FsdFNpZGVdO1xuICAgICAgICB2YXIgX3ByZXZlbnRlZE9mZnNldCA9IHdpdGhpbih0ZXRoZXIgPyBtaW4oX21pbiwgdGV0aGVyTWluKSA6IF9taW4sIF9vZmZzZXQsIHRldGhlciA/IG1heChfbWF4LCB0ZXRoZXJNYXgpIDogX21heCk7XG4gICAgICAgIHBvcHBlck9mZnNldHMyW2FsdEF4aXNdID0gX3ByZXZlbnRlZE9mZnNldDtcbiAgICAgICAgZGF0YVthbHRBeGlzXSA9IF9wcmV2ZW50ZWRPZmZzZXQgLSBfb2Zmc2V0O1xuICAgICAgfVxuICAgIH1cbiAgICBzdGF0ZS5tb2RpZmllcnNEYXRhW25hbWVdID0gZGF0YTtcbiAgfVxuICB2YXIgcHJldmVudE92ZXJmbG93JDEgPSB7XG4gICAgbmFtZTogXCJwcmV2ZW50T3ZlcmZsb3dcIixcbiAgICBlbmFibGVkOiB0cnVlLFxuICAgIHBoYXNlOiBcIm1haW5cIixcbiAgICBmbjogcHJldmVudE92ZXJmbG93LFxuICAgIHJlcXVpcmVzSWZFeGlzdHM6IFtcIm9mZnNldFwiXVxuICB9O1xuICB2YXIgdG9QYWRkaW5nT2JqZWN0ID0gZnVuY3Rpb24gdG9QYWRkaW5nT2JqZWN0MihwYWRkaW5nLCBzdGF0ZSkge1xuICAgIHBhZGRpbmcgPSB0eXBlb2YgcGFkZGluZyA9PT0gXCJmdW5jdGlvblwiID8gcGFkZGluZyhPYmplY3QuYXNzaWduKHt9LCBzdGF0ZS5yZWN0cywge1xuICAgICAgcGxhY2VtZW50OiBzdGF0ZS5wbGFjZW1lbnRcbiAgICB9KSkgOiBwYWRkaW5nO1xuICAgIHJldHVybiBtZXJnZVBhZGRpbmdPYmplY3QodHlwZW9mIHBhZGRpbmcgIT09IFwibnVtYmVyXCIgPyBwYWRkaW5nIDogZXhwYW5kVG9IYXNoTWFwKHBhZGRpbmcsIGJhc2VQbGFjZW1lbnRzKSk7XG4gIH07XG4gIGZ1bmN0aW9uIGFycm93KF9yZWYpIHtcbiAgICB2YXIgX3N0YXRlJG1vZGlmaWVyc0RhdGEkO1xuICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGUsIG5hbWUgPSBfcmVmLm5hbWUsIG9wdGlvbnMgPSBfcmVmLm9wdGlvbnM7XG4gICAgdmFyIGFycm93RWxlbWVudCA9IHN0YXRlLmVsZW1lbnRzLmFycm93O1xuICAgIHZhciBwb3BwZXJPZmZzZXRzMiA9IHN0YXRlLm1vZGlmaWVyc0RhdGEucG9wcGVyT2Zmc2V0cztcbiAgICB2YXIgYmFzZVBsYWNlbWVudCA9IGdldEJhc2VQbGFjZW1lbnQoc3RhdGUucGxhY2VtZW50KTtcbiAgICB2YXIgYXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChiYXNlUGxhY2VtZW50KTtcbiAgICB2YXIgaXNWZXJ0aWNhbCA9IFtsZWZ0LCByaWdodF0uaW5kZXhPZihiYXNlUGxhY2VtZW50KSA+PSAwO1xuICAgIHZhciBsZW4gPSBpc1ZlcnRpY2FsID8gXCJoZWlnaHRcIiA6IFwid2lkdGhcIjtcbiAgICBpZiAoIWFycm93RWxlbWVudCB8fCAhcG9wcGVyT2Zmc2V0czIpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdmFyIHBhZGRpbmdPYmplY3QgPSB0b1BhZGRpbmdPYmplY3Qob3B0aW9ucy5wYWRkaW5nLCBzdGF0ZSk7XG4gICAgdmFyIGFycm93UmVjdCA9IGdldExheW91dFJlY3QoYXJyb3dFbGVtZW50KTtcbiAgICB2YXIgbWluUHJvcCA9IGF4aXMgPT09IFwieVwiID8gdG9wIDogbGVmdDtcbiAgICB2YXIgbWF4UHJvcCA9IGF4aXMgPT09IFwieVwiID8gYm90dG9tIDogcmlnaHQ7XG4gICAgdmFyIGVuZERpZmYgPSBzdGF0ZS5yZWN0cy5yZWZlcmVuY2VbbGVuXSArIHN0YXRlLnJlY3RzLnJlZmVyZW5jZVtheGlzXSAtIHBvcHBlck9mZnNldHMyW2F4aXNdIC0gc3RhdGUucmVjdHMucG9wcGVyW2xlbl07XG4gICAgdmFyIHN0YXJ0RGlmZiA9IHBvcHBlck9mZnNldHMyW2F4aXNdIC0gc3RhdGUucmVjdHMucmVmZXJlbmNlW2F4aXNdO1xuICAgIHZhciBhcnJvd09mZnNldFBhcmVudCA9IGdldE9mZnNldFBhcmVudChhcnJvd0VsZW1lbnQpO1xuICAgIHZhciBjbGllbnRTaXplID0gYXJyb3dPZmZzZXRQYXJlbnQgPyBheGlzID09PSBcInlcIiA/IGFycm93T2Zmc2V0UGFyZW50LmNsaWVudEhlaWdodCB8fCAwIDogYXJyb3dPZmZzZXRQYXJlbnQuY2xpZW50V2lkdGggfHwgMCA6IDA7XG4gICAgdmFyIGNlbnRlclRvUmVmZXJlbmNlID0gZW5kRGlmZiAvIDIgLSBzdGFydERpZmYgLyAyO1xuICAgIHZhciBtaW4yID0gcGFkZGluZ09iamVjdFttaW5Qcm9wXTtcbiAgICB2YXIgbWF4MiA9IGNsaWVudFNpemUgLSBhcnJvd1JlY3RbbGVuXSAtIHBhZGRpbmdPYmplY3RbbWF4UHJvcF07XG4gICAgdmFyIGNlbnRlciA9IGNsaWVudFNpemUgLyAyIC0gYXJyb3dSZWN0W2xlbl0gLyAyICsgY2VudGVyVG9SZWZlcmVuY2U7XG4gICAgdmFyIG9mZnNldDIgPSB3aXRoaW4obWluMiwgY2VudGVyLCBtYXgyKTtcbiAgICB2YXIgYXhpc1Byb3AgPSBheGlzO1xuICAgIHN0YXRlLm1vZGlmaWVyc0RhdGFbbmFtZV0gPSAoX3N0YXRlJG1vZGlmaWVyc0RhdGEkID0ge30sIF9zdGF0ZSRtb2RpZmllcnNEYXRhJFtheGlzUHJvcF0gPSBvZmZzZXQyLCBfc3RhdGUkbW9kaWZpZXJzRGF0YSQuY2VudGVyT2Zmc2V0ID0gb2Zmc2V0MiAtIGNlbnRlciwgX3N0YXRlJG1vZGlmaWVyc0RhdGEkKTtcbiAgfVxuICBmdW5jdGlvbiBlZmZlY3QoX3JlZjIpIHtcbiAgICB2YXIgc3RhdGUgPSBfcmVmMi5zdGF0ZSwgb3B0aW9ucyA9IF9yZWYyLm9wdGlvbnM7XG4gICAgdmFyIF9vcHRpb25zJGVsZW1lbnQgPSBvcHRpb25zLmVsZW1lbnQsIGFycm93RWxlbWVudCA9IF9vcHRpb25zJGVsZW1lbnQgPT09IHZvaWQgMCA/IFwiW2RhdGEtcG9wcGVyLWFycm93XVwiIDogX29wdGlvbnMkZWxlbWVudDtcbiAgICBpZiAoYXJyb3dFbGVtZW50ID09IG51bGwpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKHR5cGVvZiBhcnJvd0VsZW1lbnQgPT09IFwic3RyaW5nXCIpIHtcbiAgICAgIGFycm93RWxlbWVudCA9IHN0YXRlLmVsZW1lbnRzLnBvcHBlci5xdWVyeVNlbGVjdG9yKGFycm93RWxlbWVudCk7XG4gICAgICBpZiAoIWFycm93RWxlbWVudCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgfVxuICAgIGlmICh0cnVlKSB7XG4gICAgICBpZiAoIWlzSFRNTEVsZW1lbnQoYXJyb3dFbGVtZW50KSkge1xuICAgICAgICBjb25zb2xlLmVycm9yKFsnUG9wcGVyOiBcImFycm93XCIgZWxlbWVudCBtdXN0IGJlIGFuIEhUTUxFbGVtZW50IChub3QgYW4gU1ZHRWxlbWVudCkuJywgXCJUbyB1c2UgYW4gU1ZHIGFycm93LCB3cmFwIGl0IGluIGFuIEhUTUxFbGVtZW50IHRoYXQgd2lsbCBiZSB1c2VkIGFzXCIsIFwidGhlIGFycm93LlwiXS5qb2luKFwiIFwiKSk7XG4gICAgICB9XG4gICAgfVxuICAgIGlmICghY29udGFpbnMoc3RhdGUuZWxlbWVudHMucG9wcGVyLCBhcnJvd0VsZW1lbnQpKSB7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICBjb25zb2xlLmVycm9yKFsnUG9wcGVyOiBcImFycm93XCIgbW9kaWZpZXJcXCdzIGBlbGVtZW50YCBtdXN0IGJlIGEgY2hpbGQgb2YgdGhlIHBvcHBlcicsIFwiZWxlbWVudC5cIl0uam9pbihcIiBcIikpO1xuICAgICAgfVxuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBzdGF0ZS5lbGVtZW50cy5hcnJvdyA9IGFycm93RWxlbWVudDtcbiAgfVxuICB2YXIgYXJyb3ckMSA9IHtcbiAgICBuYW1lOiBcImFycm93XCIsXG4gICAgZW5hYmxlZDogdHJ1ZSxcbiAgICBwaGFzZTogXCJtYWluXCIsXG4gICAgZm46IGFycm93LFxuICAgIGVmZmVjdCxcbiAgICByZXF1aXJlczogW1wicG9wcGVyT2Zmc2V0c1wiXSxcbiAgICByZXF1aXJlc0lmRXhpc3RzOiBbXCJwcmV2ZW50T3ZlcmZsb3dcIl1cbiAgfTtcbiAgZnVuY3Rpb24gZ2V0U2lkZU9mZnNldHMob3ZlcmZsb3csIHJlY3QsIHByZXZlbnRlZE9mZnNldHMpIHtcbiAgICBpZiAocHJldmVudGVkT2Zmc2V0cyA9PT0gdm9pZCAwKSB7XG4gICAgICBwcmV2ZW50ZWRPZmZzZXRzID0ge1xuICAgICAgICB4OiAwLFxuICAgICAgICB5OiAwXG4gICAgICB9O1xuICAgIH1cbiAgICByZXR1cm4ge1xuICAgICAgdG9wOiBvdmVyZmxvdy50b3AgLSByZWN0LmhlaWdodCAtIHByZXZlbnRlZE9mZnNldHMueSxcbiAgICAgIHJpZ2h0OiBvdmVyZmxvdy5yaWdodCAtIHJlY3Qud2lkdGggKyBwcmV2ZW50ZWRPZmZzZXRzLngsXG4gICAgICBib3R0b206IG92ZXJmbG93LmJvdHRvbSAtIHJlY3QuaGVpZ2h0ICsgcHJldmVudGVkT2Zmc2V0cy55LFxuICAgICAgbGVmdDogb3ZlcmZsb3cubGVmdCAtIHJlY3Qud2lkdGggLSBwcmV2ZW50ZWRPZmZzZXRzLnhcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIGlzQW55U2lkZUZ1bGx5Q2xpcHBlZChvdmVyZmxvdykge1xuICAgIHJldHVybiBbdG9wLCByaWdodCwgYm90dG9tLCBsZWZ0XS5zb21lKGZ1bmN0aW9uKHNpZGUpIHtcbiAgICAgIHJldHVybiBvdmVyZmxvd1tzaWRlXSA+PSAwO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGhpZGUoX3JlZikge1xuICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGUsIG5hbWUgPSBfcmVmLm5hbWU7XG4gICAgdmFyIHJlZmVyZW5jZVJlY3QgPSBzdGF0ZS5yZWN0cy5yZWZlcmVuY2U7XG4gICAgdmFyIHBvcHBlclJlY3QgPSBzdGF0ZS5yZWN0cy5wb3BwZXI7XG4gICAgdmFyIHByZXZlbnRlZE9mZnNldHMgPSBzdGF0ZS5tb2RpZmllcnNEYXRhLnByZXZlbnRPdmVyZmxvdztcbiAgICB2YXIgcmVmZXJlbmNlT3ZlcmZsb3cgPSBkZXRlY3RPdmVyZmxvdyhzdGF0ZSwge1xuICAgICAgZWxlbWVudENvbnRleHQ6IFwicmVmZXJlbmNlXCJcbiAgICB9KTtcbiAgICB2YXIgcG9wcGVyQWx0T3ZlcmZsb3cgPSBkZXRlY3RPdmVyZmxvdyhzdGF0ZSwge1xuICAgICAgYWx0Qm91bmRhcnk6IHRydWVcbiAgICB9KTtcbiAgICB2YXIgcmVmZXJlbmNlQ2xpcHBpbmdPZmZzZXRzID0gZ2V0U2lkZU9mZnNldHMocmVmZXJlbmNlT3ZlcmZsb3csIHJlZmVyZW5jZVJlY3QpO1xuICAgIHZhciBwb3BwZXJFc2NhcGVPZmZzZXRzID0gZ2V0U2lkZU9mZnNldHMocG9wcGVyQWx0T3ZlcmZsb3csIHBvcHBlclJlY3QsIHByZXZlbnRlZE9mZnNldHMpO1xuICAgIHZhciBpc1JlZmVyZW5jZUhpZGRlbiA9IGlzQW55U2lkZUZ1bGx5Q2xpcHBlZChyZWZlcmVuY2VDbGlwcGluZ09mZnNldHMpO1xuICAgIHZhciBoYXNQb3BwZXJFc2NhcGVkID0gaXNBbnlTaWRlRnVsbHlDbGlwcGVkKHBvcHBlckVzY2FwZU9mZnNldHMpO1xuICAgIHN0YXRlLm1vZGlmaWVyc0RhdGFbbmFtZV0gPSB7XG4gICAgICByZWZlcmVuY2VDbGlwcGluZ09mZnNldHMsXG4gICAgICBwb3BwZXJFc2NhcGVPZmZzZXRzLFxuICAgICAgaXNSZWZlcmVuY2VIaWRkZW4sXG4gICAgICBoYXNQb3BwZXJFc2NhcGVkXG4gICAgfTtcbiAgICBzdGF0ZS5hdHRyaWJ1dGVzLnBvcHBlciA9IE9iamVjdC5hc3NpZ24oe30sIHN0YXRlLmF0dHJpYnV0ZXMucG9wcGVyLCB7XG4gICAgICBcImRhdGEtcG9wcGVyLXJlZmVyZW5jZS1oaWRkZW5cIjogaXNSZWZlcmVuY2VIaWRkZW4sXG4gICAgICBcImRhdGEtcG9wcGVyLWVzY2FwZWRcIjogaGFzUG9wcGVyRXNjYXBlZFxuICAgIH0pO1xuICB9XG4gIHZhciBoaWRlJDEgPSB7XG4gICAgbmFtZTogXCJoaWRlXCIsXG4gICAgZW5hYmxlZDogdHJ1ZSxcbiAgICBwaGFzZTogXCJtYWluXCIsXG4gICAgcmVxdWlyZXNJZkV4aXN0czogW1wicHJldmVudE92ZXJmbG93XCJdLFxuICAgIGZuOiBoaWRlXG4gIH07XG4gIHZhciBkZWZhdWx0TW9kaWZpZXJzJDEgPSBbZXZlbnRMaXN0ZW5lcnMsIHBvcHBlck9mZnNldHMkMSwgY29tcHV0ZVN0eWxlcyQxLCBhcHBseVN0eWxlcyQxXTtcbiAgdmFyIGNyZWF0ZVBvcHBlciQxID0gLyogQF9fUFVSRV9fICovIHBvcHBlckdlbmVyYXRvcih7XG4gICAgZGVmYXVsdE1vZGlmaWVyczogZGVmYXVsdE1vZGlmaWVycyQxXG4gIH0pO1xuICB2YXIgZGVmYXVsdE1vZGlmaWVycyA9IFtldmVudExpc3RlbmVycywgcG9wcGVyT2Zmc2V0cyQxLCBjb21wdXRlU3R5bGVzJDEsIGFwcGx5U3R5bGVzJDEsIG9mZnNldCQxLCBmbGlwJDEsIHByZXZlbnRPdmVyZmxvdyQxLCBhcnJvdyQxLCBoaWRlJDFdO1xuICB2YXIgY3JlYXRlUG9wcGVyID0gLyogQF9fUFVSRV9fICovIHBvcHBlckdlbmVyYXRvcih7XG4gICAgZGVmYXVsdE1vZGlmaWVyc1xuICB9KTtcbiAgZXhwb3J0cy5hcHBseVN0eWxlcyA9IGFwcGx5U3R5bGVzJDE7XG4gIGV4cG9ydHMuYXJyb3cgPSBhcnJvdyQxO1xuICBleHBvcnRzLmNvbXB1dGVTdHlsZXMgPSBjb21wdXRlU3R5bGVzJDE7XG4gIGV4cG9ydHMuY3JlYXRlUG9wcGVyID0gY3JlYXRlUG9wcGVyO1xuICBleHBvcnRzLmNyZWF0ZVBvcHBlckxpdGUgPSBjcmVhdGVQb3BwZXIkMTtcbiAgZXhwb3J0cy5kZWZhdWx0TW9kaWZpZXJzID0gZGVmYXVsdE1vZGlmaWVycztcbiAgZXhwb3J0cy5kZXRlY3RPdmVyZmxvdyA9IGRldGVjdE92ZXJmbG93O1xuICBleHBvcnRzLmV2ZW50TGlzdGVuZXJzID0gZXZlbnRMaXN0ZW5lcnM7XG4gIGV4cG9ydHMuZmxpcCA9IGZsaXAkMTtcbiAgZXhwb3J0cy5oaWRlID0gaGlkZSQxO1xuICBleHBvcnRzLm9mZnNldCA9IG9mZnNldCQxO1xuICBleHBvcnRzLnBvcHBlckdlbmVyYXRvciA9IHBvcHBlckdlbmVyYXRvcjtcbiAgZXhwb3J0cy5wb3BwZXJPZmZzZXRzID0gcG9wcGVyT2Zmc2V0cyQxO1xuICBleHBvcnRzLnByZXZlbnRPdmVyZmxvdyA9IHByZXZlbnRPdmVyZmxvdyQxO1xufSk7XG5cbi8vIG5vZGVfbW9kdWxlcy90aXBweS5qcy9kaXN0L3RpcHB5LmNqcy5qc1xudmFyIHJlcXVpcmVfdGlwcHlfY2pzID0gX19jb21tb25KUygoZXhwb3J0cykgPT4ge1xuICBcInVzZSBzdHJpY3RcIjtcbiAgT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCB7dmFsdWU6IHRydWV9KTtcbiAgdmFyIGNvcmUgPSByZXF1aXJlX3BvcHBlcigpO1xuICB2YXIgUk9VTkRfQVJST1cgPSAnPHN2ZyB3aWR0aD1cIjE2XCIgaGVpZ2h0PVwiNlwiIHhtbG5zPVwiaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmdcIj48cGF0aCBkPVwiTTAgNnMxLjc5Ni0uMDEzIDQuNjctMy42MTVDNS44NTEuOSA2LjkzLjAwNiA4IDBjMS4wNy0uMDA2IDIuMTQ4Ljg4NyAzLjM0MyAyLjM4NUMxNC4yMzMgNi4wMDUgMTYgNiAxNiA2SDB6XCI+PC9zdmc+JztcbiAgdmFyIEJPWF9DTEFTUyA9IFwidGlwcHktYm94XCI7XG4gIHZhciBDT05URU5UX0NMQVNTID0gXCJ0aXBweS1jb250ZW50XCI7XG4gIHZhciBCQUNLRFJPUF9DTEFTUyA9IFwidGlwcHktYmFja2Ryb3BcIjtcbiAgdmFyIEFSUk9XX0NMQVNTID0gXCJ0aXBweS1hcnJvd1wiO1xuICB2YXIgU1ZHX0FSUk9XX0NMQVNTID0gXCJ0aXBweS1zdmctYXJyb3dcIjtcbiAgdmFyIFRPVUNIX09QVElPTlMgPSB7XG4gICAgcGFzc2l2ZTogdHJ1ZSxcbiAgICBjYXB0dXJlOiB0cnVlXG4gIH07XG4gIGZ1bmN0aW9uIGhhc093blByb3BlcnR5KG9iaiwga2V5KSB7XG4gICAgcmV0dXJuIHt9Lmhhc093blByb3BlcnR5LmNhbGwob2JqLCBrZXkpO1xuICB9XG4gIGZ1bmN0aW9uIGdldFZhbHVlQXRJbmRleE9yUmV0dXJuKHZhbHVlLCBpbmRleCwgZGVmYXVsdFZhbHVlKSB7XG4gICAgaWYgKEFycmF5LmlzQXJyYXkodmFsdWUpKSB7XG4gICAgICB2YXIgdiA9IHZhbHVlW2luZGV4XTtcbiAgICAgIHJldHVybiB2ID09IG51bGwgPyBBcnJheS5pc0FycmF5KGRlZmF1bHRWYWx1ZSkgPyBkZWZhdWx0VmFsdWVbaW5kZXhdIDogZGVmYXVsdFZhbHVlIDogdjtcbiAgICB9XG4gICAgcmV0dXJuIHZhbHVlO1xuICB9XG4gIGZ1bmN0aW9uIGlzVHlwZSh2YWx1ZSwgdHlwZSkge1xuICAgIHZhciBzdHIgPSB7fS50b1N0cmluZy5jYWxsKHZhbHVlKTtcbiAgICByZXR1cm4gc3RyLmluZGV4T2YoXCJbb2JqZWN0XCIpID09PSAwICYmIHN0ci5pbmRleE9mKHR5cGUgKyBcIl1cIikgPiAtMTtcbiAgfVxuICBmdW5jdGlvbiBpbnZva2VXaXRoQXJnc09yUmV0dXJuKHZhbHVlLCBhcmdzKSB7XG4gICAgcmV0dXJuIHR5cGVvZiB2YWx1ZSA9PT0gXCJmdW5jdGlvblwiID8gdmFsdWUuYXBwbHkodm9pZCAwLCBhcmdzKSA6IHZhbHVlO1xuICB9XG4gIGZ1bmN0aW9uIGRlYm91bmNlKGZuLCBtcykge1xuICAgIGlmIChtcyA9PT0gMCkge1xuICAgICAgcmV0dXJuIGZuO1xuICAgIH1cbiAgICB2YXIgdGltZW91dDtcbiAgICByZXR1cm4gZnVuY3Rpb24oYXJnKSB7XG4gICAgICBjbGVhclRpbWVvdXQodGltZW91dCk7XG4gICAgICB0aW1lb3V0ID0gc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgZm4oYXJnKTtcbiAgICAgIH0sIG1zKTtcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIHJlbW92ZVByb3BlcnRpZXMob2JqLCBrZXlzKSB7XG4gICAgdmFyIGNsb25lID0gT2JqZWN0LmFzc2lnbih7fSwgb2JqKTtcbiAgICBrZXlzLmZvckVhY2goZnVuY3Rpb24oa2V5KSB7XG4gICAgICBkZWxldGUgY2xvbmVba2V5XTtcbiAgICB9KTtcbiAgICByZXR1cm4gY2xvbmU7XG4gIH1cbiAgZnVuY3Rpb24gc3BsaXRCeVNwYWNlcyh2YWx1ZSkge1xuICAgIHJldHVybiB2YWx1ZS5zcGxpdCgvXFxzKy8pLmZpbHRlcihCb29sZWFuKTtcbiAgfVxuICBmdW5jdGlvbiBub3JtYWxpemVUb0FycmF5KHZhbHVlKSB7XG4gICAgcmV0dXJuIFtdLmNvbmNhdCh2YWx1ZSk7XG4gIH1cbiAgZnVuY3Rpb24gcHVzaElmVW5pcXVlKGFyciwgdmFsdWUpIHtcbiAgICBpZiAoYXJyLmluZGV4T2YodmFsdWUpID09PSAtMSkge1xuICAgICAgYXJyLnB1c2godmFsdWUpO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiB1bmlxdWUoYXJyKSB7XG4gICAgcmV0dXJuIGFyci5maWx0ZXIoZnVuY3Rpb24oaXRlbSwgaW5kZXgpIHtcbiAgICAgIHJldHVybiBhcnIuaW5kZXhPZihpdGVtKSA9PT0gaW5kZXg7XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0QmFzZVBsYWNlbWVudChwbGFjZW1lbnQpIHtcbiAgICByZXR1cm4gcGxhY2VtZW50LnNwbGl0KFwiLVwiKVswXTtcbiAgfVxuICBmdW5jdGlvbiBhcnJheUZyb20odmFsdWUpIHtcbiAgICByZXR1cm4gW10uc2xpY2UuY2FsbCh2YWx1ZSk7XG4gIH1cbiAgZnVuY3Rpb24gcmVtb3ZlVW5kZWZpbmVkUHJvcHMob2JqKSB7XG4gICAgcmV0dXJuIE9iamVjdC5rZXlzKG9iaikucmVkdWNlKGZ1bmN0aW9uKGFjYywga2V5KSB7XG4gICAgICBpZiAob2JqW2tleV0gIT09IHZvaWQgMCkge1xuICAgICAgICBhY2Nba2V5XSA9IG9ialtrZXldO1xuICAgICAgfVxuICAgICAgcmV0dXJuIGFjYztcbiAgICB9LCB7fSk7XG4gIH1cbiAgZnVuY3Rpb24gZGl2KCkge1xuICAgIHJldHVybiBkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwiZGl2XCIpO1xuICB9XG4gIGZ1bmN0aW9uIGlzRWxlbWVudCh2YWx1ZSkge1xuICAgIHJldHVybiBbXCJFbGVtZW50XCIsIFwiRnJhZ21lbnRcIl0uc29tZShmdW5jdGlvbih0eXBlKSB7XG4gICAgICByZXR1cm4gaXNUeXBlKHZhbHVlLCB0eXBlKTtcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBpc05vZGVMaXN0KHZhbHVlKSB7XG4gICAgcmV0dXJuIGlzVHlwZSh2YWx1ZSwgXCJOb2RlTGlzdFwiKTtcbiAgfVxuICBmdW5jdGlvbiBpc01vdXNlRXZlbnQodmFsdWUpIHtcbiAgICByZXR1cm4gaXNUeXBlKHZhbHVlLCBcIk1vdXNlRXZlbnRcIik7XG4gIH1cbiAgZnVuY3Rpb24gaXNSZWZlcmVuY2VFbGVtZW50KHZhbHVlKSB7XG4gICAgcmV0dXJuICEhKHZhbHVlICYmIHZhbHVlLl90aXBweSAmJiB2YWx1ZS5fdGlwcHkucmVmZXJlbmNlID09PSB2YWx1ZSk7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0QXJyYXlPZkVsZW1lbnRzKHZhbHVlKSB7XG4gICAgaWYgKGlzRWxlbWVudCh2YWx1ZSkpIHtcbiAgICAgIHJldHVybiBbdmFsdWVdO1xuICAgIH1cbiAgICBpZiAoaXNOb2RlTGlzdCh2YWx1ZSkpIHtcbiAgICAgIHJldHVybiBhcnJheUZyb20odmFsdWUpO1xuICAgIH1cbiAgICBpZiAoQXJyYXkuaXNBcnJheSh2YWx1ZSkpIHtcbiAgICAgIHJldHVybiB2YWx1ZTtcbiAgICB9XG4gICAgcmV0dXJuIGFycmF5RnJvbShkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKHZhbHVlKSk7XG4gIH1cbiAgZnVuY3Rpb24gc2V0VHJhbnNpdGlvbkR1cmF0aW9uKGVscywgdmFsdWUpIHtcbiAgICBlbHMuZm9yRWFjaChmdW5jdGlvbihlbCkge1xuICAgICAgaWYgKGVsKSB7XG4gICAgICAgIGVsLnN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbiA9IHZhbHVlICsgXCJtc1wiO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIHNldFZpc2liaWxpdHlTdGF0ZShlbHMsIHN0YXRlKSB7XG4gICAgZWxzLmZvckVhY2goZnVuY3Rpb24oZWwpIHtcbiAgICAgIGlmIChlbCkge1xuICAgICAgICBlbC5zZXRBdHRyaWJ1dGUoXCJkYXRhLXN0YXRlXCIsIHN0YXRlKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBnZXRPd25lckRvY3VtZW50KGVsZW1lbnRPckVsZW1lbnRzKSB7XG4gICAgdmFyIF9lbGVtZW50JG93bmVyRG9jdW1lbjtcbiAgICB2YXIgX25vcm1hbGl6ZVRvQXJyYXkgPSBub3JtYWxpemVUb0FycmF5KGVsZW1lbnRPckVsZW1lbnRzKSwgZWxlbWVudCA9IF9ub3JtYWxpemVUb0FycmF5WzBdO1xuICAgIHJldHVybiAoZWxlbWVudCA9PSBudWxsID8gdm9pZCAwIDogKF9lbGVtZW50JG93bmVyRG9jdW1lbiA9IGVsZW1lbnQub3duZXJEb2N1bWVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9lbGVtZW50JG93bmVyRG9jdW1lbi5ib2R5KSA/IGVsZW1lbnQub3duZXJEb2N1bWVudCA6IGRvY3VtZW50O1xuICB9XG4gIGZ1bmN0aW9uIGlzQ3Vyc29yT3V0c2lkZUludGVyYWN0aXZlQm9yZGVyKHBvcHBlclRyZWVEYXRhLCBldmVudCkge1xuICAgIHZhciBjbGllbnRYID0gZXZlbnQuY2xpZW50WCwgY2xpZW50WSA9IGV2ZW50LmNsaWVudFk7XG4gICAgcmV0dXJuIHBvcHBlclRyZWVEYXRhLmV2ZXJ5KGZ1bmN0aW9uKF9yZWYpIHtcbiAgICAgIHZhciBwb3BwZXJSZWN0ID0gX3JlZi5wb3BwZXJSZWN0LCBwb3BwZXJTdGF0ZSA9IF9yZWYucG9wcGVyU3RhdGUsIHByb3BzID0gX3JlZi5wcm9wcztcbiAgICAgIHZhciBpbnRlcmFjdGl2ZUJvcmRlciA9IHByb3BzLmludGVyYWN0aXZlQm9yZGVyO1xuICAgICAgdmFyIGJhc2VQbGFjZW1lbnQgPSBnZXRCYXNlUGxhY2VtZW50KHBvcHBlclN0YXRlLnBsYWNlbWVudCk7XG4gICAgICB2YXIgb2Zmc2V0RGF0YSA9IHBvcHBlclN0YXRlLm1vZGlmaWVyc0RhdGEub2Zmc2V0O1xuICAgICAgaWYgKCFvZmZzZXREYXRhKSB7XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfVxuICAgICAgdmFyIHRvcERpc3RhbmNlID0gYmFzZVBsYWNlbWVudCA9PT0gXCJib3R0b21cIiA/IG9mZnNldERhdGEudG9wLnkgOiAwO1xuICAgICAgdmFyIGJvdHRvbURpc3RhbmNlID0gYmFzZVBsYWNlbWVudCA9PT0gXCJ0b3BcIiA/IG9mZnNldERhdGEuYm90dG9tLnkgOiAwO1xuICAgICAgdmFyIGxlZnREaXN0YW5jZSA9IGJhc2VQbGFjZW1lbnQgPT09IFwicmlnaHRcIiA/IG9mZnNldERhdGEubGVmdC54IDogMDtcbiAgICAgIHZhciByaWdodERpc3RhbmNlID0gYmFzZVBsYWNlbWVudCA9PT0gXCJsZWZ0XCIgPyBvZmZzZXREYXRhLnJpZ2h0LnggOiAwO1xuICAgICAgdmFyIGV4Y2VlZHNUb3AgPSBwb3BwZXJSZWN0LnRvcCAtIGNsaWVudFkgKyB0b3BEaXN0YW5jZSA+IGludGVyYWN0aXZlQm9yZGVyO1xuICAgICAgdmFyIGV4Y2VlZHNCb3R0b20gPSBjbGllbnRZIC0gcG9wcGVyUmVjdC5ib3R0b20gLSBib3R0b21EaXN0YW5jZSA+IGludGVyYWN0aXZlQm9yZGVyO1xuICAgICAgdmFyIGV4Y2VlZHNMZWZ0ID0gcG9wcGVyUmVjdC5sZWZ0IC0gY2xpZW50WCArIGxlZnREaXN0YW5jZSA+IGludGVyYWN0aXZlQm9yZGVyO1xuICAgICAgdmFyIGV4Y2VlZHNSaWdodCA9IGNsaWVudFggLSBwb3BwZXJSZWN0LnJpZ2h0IC0gcmlnaHREaXN0YW5jZSA+IGludGVyYWN0aXZlQm9yZGVyO1xuICAgICAgcmV0dXJuIGV4Y2VlZHNUb3AgfHwgZXhjZWVkc0JvdHRvbSB8fCBleGNlZWRzTGVmdCB8fCBleGNlZWRzUmlnaHQ7XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gdXBkYXRlVHJhbnNpdGlvbkVuZExpc3RlbmVyKGJveCwgYWN0aW9uLCBsaXN0ZW5lcikge1xuICAgIHZhciBtZXRob2QgPSBhY3Rpb24gKyBcIkV2ZW50TGlzdGVuZXJcIjtcbiAgICBbXCJ0cmFuc2l0aW9uZW5kXCIsIFwid2Via2l0VHJhbnNpdGlvbkVuZFwiXS5mb3JFYWNoKGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICBib3hbbWV0aG9kXShldmVudCwgbGlzdGVuZXIpO1xuICAgIH0pO1xuICB9XG4gIHZhciBjdXJyZW50SW5wdXQgPSB7XG4gICAgaXNUb3VjaDogZmFsc2VcbiAgfTtcbiAgdmFyIGxhc3RNb3VzZU1vdmVUaW1lID0gMDtcbiAgZnVuY3Rpb24gb25Eb2N1bWVudFRvdWNoU3RhcnQoKSB7XG4gICAgaWYgKGN1cnJlbnRJbnB1dC5pc1RvdWNoKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGN1cnJlbnRJbnB1dC5pc1RvdWNoID0gdHJ1ZTtcbiAgICBpZiAod2luZG93LnBlcmZvcm1hbmNlKSB7XG4gICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKFwibW91c2Vtb3ZlXCIsIG9uRG9jdW1lbnRNb3VzZU1vdmUpO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiBvbkRvY3VtZW50TW91c2VNb3ZlKCkge1xuICAgIHZhciBub3cgPSBwZXJmb3JtYW5jZS5ub3coKTtcbiAgICBpZiAobm93IC0gbGFzdE1vdXNlTW92ZVRpbWUgPCAyMCkge1xuICAgICAgY3VycmVudElucHV0LmlzVG91Y2ggPSBmYWxzZTtcbiAgICAgIGRvY3VtZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoXCJtb3VzZW1vdmVcIiwgb25Eb2N1bWVudE1vdXNlTW92ZSk7XG4gICAgfVxuICAgIGxhc3RNb3VzZU1vdmVUaW1lID0gbm93O1xuICB9XG4gIGZ1bmN0aW9uIG9uV2luZG93Qmx1cigpIHtcbiAgICB2YXIgYWN0aXZlRWxlbWVudCA9IGRvY3VtZW50LmFjdGl2ZUVsZW1lbnQ7XG4gICAgaWYgKGlzUmVmZXJlbmNlRWxlbWVudChhY3RpdmVFbGVtZW50KSkge1xuICAgICAgdmFyIGluc3RhbmNlID0gYWN0aXZlRWxlbWVudC5fdGlwcHk7XG4gICAgICBpZiAoYWN0aXZlRWxlbWVudC5ibHVyICYmICFpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUpIHtcbiAgICAgICAgYWN0aXZlRWxlbWVudC5ibHVyKCk7XG4gICAgICB9XG4gICAgfVxuICB9XG4gIGZ1bmN0aW9uIGJpbmRHbG9iYWxFdmVudExpc3RlbmVycygpIHtcbiAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKFwidG91Y2hzdGFydFwiLCBvbkRvY3VtZW50VG91Y2hTdGFydCwgVE9VQ0hfT1BUSU9OUyk7XG4gICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXCJibHVyXCIsIG9uV2luZG93Qmx1cik7XG4gIH1cbiAgdmFyIGlzQnJvd3NlciA9IHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgJiYgdHlwZW9mIGRvY3VtZW50ICE9PSBcInVuZGVmaW5lZFwiO1xuICB2YXIgdWEgPSBpc0Jyb3dzZXIgPyBuYXZpZ2F0b3IudXNlckFnZW50IDogXCJcIjtcbiAgdmFyIGlzSUUgPSAvTVNJRSB8VHJpZGVudFxcLy8udGVzdCh1YSk7XG4gIGZ1bmN0aW9uIGNyZWF0ZU1lbW9yeUxlYWtXYXJuaW5nKG1ldGhvZCkge1xuICAgIHZhciB0eHQgPSBtZXRob2QgPT09IFwiZGVzdHJveVwiID8gXCJuIGFscmVhZHktXCIgOiBcIiBcIjtcbiAgICByZXR1cm4gW21ldGhvZCArIFwiKCkgd2FzIGNhbGxlZCBvbiBhXCIgKyB0eHQgKyBcImRlc3Ryb3llZCBpbnN0YW5jZS4gVGhpcyBpcyBhIG5vLW9wIGJ1dFwiLCBcImluZGljYXRlcyBhIHBvdGVudGlhbCBtZW1vcnkgbGVhay5cIl0uam9pbihcIiBcIik7XG4gIH1cbiAgZnVuY3Rpb24gY2xlYW4odmFsdWUpIHtcbiAgICB2YXIgc3BhY2VzQW5kVGFicyA9IC9bIFxcdF17Mix9L2c7XG4gICAgdmFyIGxpbmVTdGFydFdpdGhTcGFjZXMgPSAvXlsgXFx0XSovZ207XG4gICAgcmV0dXJuIHZhbHVlLnJlcGxhY2Uoc3BhY2VzQW5kVGFicywgXCIgXCIpLnJlcGxhY2UobGluZVN0YXJ0V2l0aFNwYWNlcywgXCJcIikudHJpbSgpO1xuICB9XG4gIGZ1bmN0aW9uIGdldERldk1lc3NhZ2UobWVzc2FnZSkge1xuICAgIHJldHVybiBjbGVhbihcIlxcbiAgJWN0aXBweS5qc1xcblxcbiAgJWNcIiArIGNsZWFuKG1lc3NhZ2UpICsgXCJcXG5cXG4gICVjXFx1ezFGNDc3fVxcdTIwMEQgVGhpcyBpcyBhIGRldmVsb3BtZW50LW9ubHkgbWVzc2FnZS4gSXQgd2lsbCBiZSByZW1vdmVkIGluIHByb2R1Y3Rpb24uXFxuICBcIik7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0Rm9ybWF0dGVkTWVzc2FnZShtZXNzYWdlKSB7XG4gICAgcmV0dXJuIFtcbiAgICAgIGdldERldk1lc3NhZ2UobWVzc2FnZSksXG4gICAgICBcImNvbG9yOiAjMDBDNTg0OyBmb250LXNpemU6IDEuM2VtOyBmb250LXdlaWdodDogYm9sZDtcIixcbiAgICAgIFwibGluZS1oZWlnaHQ6IDEuNVwiLFxuICAgICAgXCJjb2xvcjogI2E2YTA5NTtcIlxuICAgIF07XG4gIH1cbiAgdmFyIHZpc2l0ZWRNZXNzYWdlcztcbiAgaWYgKHRydWUpIHtcbiAgICByZXNldFZpc2l0ZWRNZXNzYWdlcygpO1xuICB9XG4gIGZ1bmN0aW9uIHJlc2V0VmlzaXRlZE1lc3NhZ2VzKCkge1xuICAgIHZpc2l0ZWRNZXNzYWdlcyA9IG5ldyBTZXQoKTtcbiAgfVxuICBmdW5jdGlvbiB3YXJuV2hlbihjb25kaXRpb24sIG1lc3NhZ2UpIHtcbiAgICBpZiAoY29uZGl0aW9uICYmICF2aXNpdGVkTWVzc2FnZXMuaGFzKG1lc3NhZ2UpKSB7XG4gICAgICB2YXIgX2NvbnNvbGU7XG4gICAgICB2aXNpdGVkTWVzc2FnZXMuYWRkKG1lc3NhZ2UpO1xuICAgICAgKF9jb25zb2xlID0gY29uc29sZSkud2Fybi5hcHBseShfY29uc29sZSwgZ2V0Rm9ybWF0dGVkTWVzc2FnZShtZXNzYWdlKSk7XG4gICAgfVxuICB9XG4gIGZ1bmN0aW9uIGVycm9yV2hlbihjb25kaXRpb24sIG1lc3NhZ2UpIHtcbiAgICBpZiAoY29uZGl0aW9uICYmICF2aXNpdGVkTWVzc2FnZXMuaGFzKG1lc3NhZ2UpKSB7XG4gICAgICB2YXIgX2NvbnNvbGUyO1xuICAgICAgdmlzaXRlZE1lc3NhZ2VzLmFkZChtZXNzYWdlKTtcbiAgICAgIChfY29uc29sZTIgPSBjb25zb2xlKS5lcnJvci5hcHBseShfY29uc29sZTIsIGdldEZvcm1hdHRlZE1lc3NhZ2UobWVzc2FnZSkpO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiB2YWxpZGF0ZVRhcmdldHModGFyZ2V0cykge1xuICAgIHZhciBkaWRQYXNzRmFsc3lWYWx1ZSA9ICF0YXJnZXRzO1xuICAgIHZhciBkaWRQYXNzUGxhaW5PYmplY3QgPSBPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nLmNhbGwodGFyZ2V0cykgPT09IFwiW29iamVjdCBPYmplY3RdXCIgJiYgIXRhcmdldHMuYWRkRXZlbnRMaXN0ZW5lcjtcbiAgICBlcnJvcldoZW4oZGlkUGFzc0ZhbHN5VmFsdWUsIFtcInRpcHB5KCkgd2FzIHBhc3NlZFwiLCBcImBcIiArIFN0cmluZyh0YXJnZXRzKSArIFwiYFwiLCBcImFzIGl0cyB0YXJnZXRzIChmaXJzdCkgYXJndW1lbnQuIFZhbGlkIHR5cGVzIGFyZTogU3RyaW5nLCBFbGVtZW50LFwiLCBcIkVsZW1lbnRbXSwgb3IgTm9kZUxpc3QuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICBlcnJvcldoZW4oZGlkUGFzc1BsYWluT2JqZWN0LCBbXCJ0aXBweSgpIHdhcyBwYXNzZWQgYSBwbGFpbiBvYmplY3Qgd2hpY2ggaXMgbm90IHN1cHBvcnRlZCBhcyBhbiBhcmd1bWVudFwiLCBcImZvciB2aXJ0dWFsIHBvc2l0aW9uaW5nLiBVc2UgcHJvcHMuZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdCBpbnN0ZWFkLlwiXS5qb2luKFwiIFwiKSk7XG4gIH1cbiAgdmFyIHBsdWdpblByb3BzID0ge1xuICAgIGFuaW1hdGVGaWxsOiBmYWxzZSxcbiAgICBmb2xsb3dDdXJzb3I6IGZhbHNlLFxuICAgIGlubGluZVBvc2l0aW9uaW5nOiBmYWxzZSxcbiAgICBzdGlja3k6IGZhbHNlXG4gIH07XG4gIHZhciByZW5kZXJQcm9wcyA9IHtcbiAgICBhbGxvd0hUTUw6IGZhbHNlLFxuICAgIGFuaW1hdGlvbjogXCJmYWRlXCIsXG4gICAgYXJyb3c6IHRydWUsXG4gICAgY29udGVudDogXCJcIixcbiAgICBpbmVydGlhOiBmYWxzZSxcbiAgICBtYXhXaWR0aDogMzUwLFxuICAgIHJvbGU6IFwidG9vbHRpcFwiLFxuICAgIHRoZW1lOiBcIlwiLFxuICAgIHpJbmRleDogOTk5OVxuICB9O1xuICB2YXIgZGVmYXVsdFByb3BzID0gT2JqZWN0LmFzc2lnbih7XG4gICAgYXBwZW5kVG86IGZ1bmN0aW9uIGFwcGVuZFRvKCkge1xuICAgICAgcmV0dXJuIGRvY3VtZW50LmJvZHk7XG4gICAgfSxcbiAgICBhcmlhOiB7XG4gICAgICBjb250ZW50OiBcImF1dG9cIixcbiAgICAgIGV4cGFuZGVkOiBcImF1dG9cIlxuICAgIH0sXG4gICAgZGVsYXk6IDAsXG4gICAgZHVyYXRpb246IFszMDAsIDI1MF0sXG4gICAgZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdDogbnVsbCxcbiAgICBoaWRlT25DbGljazogdHJ1ZSxcbiAgICBpZ25vcmVBdHRyaWJ1dGVzOiBmYWxzZSxcbiAgICBpbnRlcmFjdGl2ZTogZmFsc2UsXG4gICAgaW50ZXJhY3RpdmVCb3JkZXI6IDIsXG4gICAgaW50ZXJhY3RpdmVEZWJvdW5jZTogMCxcbiAgICBtb3ZlVHJhbnNpdGlvbjogXCJcIixcbiAgICBvZmZzZXQ6IFswLCAxMF0sXG4gICAgb25BZnRlclVwZGF0ZTogZnVuY3Rpb24gb25BZnRlclVwZGF0ZSgpIHtcbiAgICB9LFxuICAgIG9uQmVmb3JlVXBkYXRlOiBmdW5jdGlvbiBvbkJlZm9yZVVwZGF0ZSgpIHtcbiAgICB9LFxuICAgIG9uQ3JlYXRlOiBmdW5jdGlvbiBvbkNyZWF0ZSgpIHtcbiAgICB9LFxuICAgIG9uRGVzdHJveTogZnVuY3Rpb24gb25EZXN0cm95KCkge1xuICAgIH0sXG4gICAgb25IaWRkZW46IGZ1bmN0aW9uIG9uSGlkZGVuKCkge1xuICAgIH0sXG4gICAgb25IaWRlOiBmdW5jdGlvbiBvbkhpZGUoKSB7XG4gICAgfSxcbiAgICBvbk1vdW50OiBmdW5jdGlvbiBvbk1vdW50KCkge1xuICAgIH0sXG4gICAgb25TaG93OiBmdW5jdGlvbiBvblNob3coKSB7XG4gICAgfSxcbiAgICBvblNob3duOiBmdW5jdGlvbiBvblNob3duKCkge1xuICAgIH0sXG4gICAgb25UcmlnZ2VyOiBmdW5jdGlvbiBvblRyaWdnZXIoKSB7XG4gICAgfSxcbiAgICBvblVudHJpZ2dlcjogZnVuY3Rpb24gb25VbnRyaWdnZXIoKSB7XG4gICAgfSxcbiAgICBvbkNsaWNrT3V0c2lkZTogZnVuY3Rpb24gb25DbGlja091dHNpZGUoKSB7XG4gICAgfSxcbiAgICBwbGFjZW1lbnQ6IFwidG9wXCIsXG4gICAgcGx1Z2luczogW10sXG4gICAgcG9wcGVyT3B0aW9uczoge30sXG4gICAgcmVuZGVyOiBudWxsLFxuICAgIHNob3dPbkNyZWF0ZTogZmFsc2UsXG4gICAgdG91Y2g6IHRydWUsXG4gICAgdHJpZ2dlcjogXCJtb3VzZWVudGVyIGZvY3VzXCIsXG4gICAgdHJpZ2dlclRhcmdldDogbnVsbFxuICB9LCBwbHVnaW5Qcm9wcywge30sIHJlbmRlclByb3BzKTtcbiAgdmFyIGRlZmF1bHRLZXlzID0gT2JqZWN0LmtleXMoZGVmYXVsdFByb3BzKTtcbiAgdmFyIHNldERlZmF1bHRQcm9wcyA9IGZ1bmN0aW9uIHNldERlZmF1bHRQcm9wczIocGFydGlhbFByb3BzKSB7XG4gICAgaWYgKHRydWUpIHtcbiAgICAgIHZhbGlkYXRlUHJvcHMocGFydGlhbFByb3BzLCBbXSk7XG4gICAgfVxuICAgIHZhciBrZXlzID0gT2JqZWN0LmtleXMocGFydGlhbFByb3BzKTtcbiAgICBrZXlzLmZvckVhY2goZnVuY3Rpb24oa2V5KSB7XG4gICAgICBkZWZhdWx0UHJvcHNba2V5XSA9IHBhcnRpYWxQcm9wc1trZXldO1xuICAgIH0pO1xuICB9O1xuICBmdW5jdGlvbiBnZXRFeHRlbmRlZFBhc3NlZFByb3BzKHBhc3NlZFByb3BzKSB7XG4gICAgdmFyIHBsdWdpbnMgPSBwYXNzZWRQcm9wcy5wbHVnaW5zIHx8IFtdO1xuICAgIHZhciBwbHVnaW5Qcm9wczIgPSBwbHVnaW5zLnJlZHVjZShmdW5jdGlvbihhY2MsIHBsdWdpbikge1xuICAgICAgdmFyIG5hbWUgPSBwbHVnaW4ubmFtZSwgZGVmYXVsdFZhbHVlID0gcGx1Z2luLmRlZmF1bHRWYWx1ZTtcbiAgICAgIGlmIChuYW1lKSB7XG4gICAgICAgIGFjY1tuYW1lXSA9IHBhc3NlZFByb3BzW25hbWVdICE9PSB2b2lkIDAgPyBwYXNzZWRQcm9wc1tuYW1lXSA6IGRlZmF1bHRWYWx1ZTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBhY2M7XG4gICAgfSwge30pO1xuICAgIHJldHVybiBPYmplY3QuYXNzaWduKHt9LCBwYXNzZWRQcm9wcywge30sIHBsdWdpblByb3BzMik7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0RGF0YUF0dHJpYnV0ZVByb3BzKHJlZmVyZW5jZSwgcGx1Z2lucykge1xuICAgIHZhciBwcm9wS2V5cyA9IHBsdWdpbnMgPyBPYmplY3Qua2V5cyhnZXRFeHRlbmRlZFBhc3NlZFByb3BzKE9iamVjdC5hc3NpZ24oe30sIGRlZmF1bHRQcm9wcywge1xuICAgICAgcGx1Z2luc1xuICAgIH0pKSkgOiBkZWZhdWx0S2V5cztcbiAgICB2YXIgcHJvcHMgPSBwcm9wS2V5cy5yZWR1Y2UoZnVuY3Rpb24oYWNjLCBrZXkpIHtcbiAgICAgIHZhciB2YWx1ZUFzU3RyaW5nID0gKHJlZmVyZW5jZS5nZXRBdHRyaWJ1dGUoXCJkYXRhLXRpcHB5LVwiICsga2V5KSB8fCBcIlwiKS50cmltKCk7XG4gICAgICBpZiAoIXZhbHVlQXNTdHJpbmcpIHtcbiAgICAgICAgcmV0dXJuIGFjYztcbiAgICAgIH1cbiAgICAgIGlmIChrZXkgPT09IFwiY29udGVudFwiKSB7XG4gICAgICAgIGFjY1trZXldID0gdmFsdWVBc1N0cmluZztcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgYWNjW2tleV0gPSBKU09OLnBhcnNlKHZhbHVlQXNTdHJpbmcpO1xuICAgICAgICB9IGNhdGNoIChlKSB7XG4gICAgICAgICAgYWNjW2tleV0gPSB2YWx1ZUFzU3RyaW5nO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4gYWNjO1xuICAgIH0sIHt9KTtcbiAgICByZXR1cm4gcHJvcHM7XG4gIH1cbiAgZnVuY3Rpb24gZXZhbHVhdGVQcm9wcyhyZWZlcmVuY2UsIHByb3BzKSB7XG4gICAgdmFyIG91dCA9IE9iamVjdC5hc3NpZ24oe30sIHByb3BzLCB7XG4gICAgICBjb250ZW50OiBpbnZva2VXaXRoQXJnc09yUmV0dXJuKHByb3BzLmNvbnRlbnQsIFtyZWZlcmVuY2VdKVxuICAgIH0sIHByb3BzLmlnbm9yZUF0dHJpYnV0ZXMgPyB7fSA6IGdldERhdGFBdHRyaWJ1dGVQcm9wcyhyZWZlcmVuY2UsIHByb3BzLnBsdWdpbnMpKTtcbiAgICBvdXQuYXJpYSA9IE9iamVjdC5hc3NpZ24oe30sIGRlZmF1bHRQcm9wcy5hcmlhLCB7fSwgb3V0LmFyaWEpO1xuICAgIG91dC5hcmlhID0ge1xuICAgICAgZXhwYW5kZWQ6IG91dC5hcmlhLmV4cGFuZGVkID09PSBcImF1dG9cIiA/IHByb3BzLmludGVyYWN0aXZlIDogb3V0LmFyaWEuZXhwYW5kZWQsXG4gICAgICBjb250ZW50OiBvdXQuYXJpYS5jb250ZW50ID09PSBcImF1dG9cIiA/IHByb3BzLmludGVyYWN0aXZlID8gbnVsbCA6IFwiZGVzY3JpYmVkYnlcIiA6IG91dC5hcmlhLmNvbnRlbnRcbiAgICB9O1xuICAgIHJldHVybiBvdXQ7XG4gIH1cbiAgZnVuY3Rpb24gdmFsaWRhdGVQcm9wcyhwYXJ0aWFsUHJvcHMsIHBsdWdpbnMpIHtcbiAgICBpZiAocGFydGlhbFByb3BzID09PSB2b2lkIDApIHtcbiAgICAgIHBhcnRpYWxQcm9wcyA9IHt9O1xuICAgIH1cbiAgICBpZiAocGx1Z2lucyA9PT0gdm9pZCAwKSB7XG4gICAgICBwbHVnaW5zID0gW107XG4gICAgfVxuICAgIHZhciBrZXlzID0gT2JqZWN0LmtleXMocGFydGlhbFByb3BzKTtcbiAgICBrZXlzLmZvckVhY2goZnVuY3Rpb24ocHJvcCkge1xuICAgICAgdmFyIG5vblBsdWdpblByb3BzID0gcmVtb3ZlUHJvcGVydGllcyhkZWZhdWx0UHJvcHMsIE9iamVjdC5rZXlzKHBsdWdpblByb3BzKSk7XG4gICAgICB2YXIgZGlkUGFzc1Vua25vd25Qcm9wID0gIWhhc093blByb3BlcnR5KG5vblBsdWdpblByb3BzLCBwcm9wKTtcbiAgICAgIGlmIChkaWRQYXNzVW5rbm93blByb3ApIHtcbiAgICAgICAgZGlkUGFzc1Vua25vd25Qcm9wID0gcGx1Z2lucy5maWx0ZXIoZnVuY3Rpb24ocGx1Z2luKSB7XG4gICAgICAgICAgcmV0dXJuIHBsdWdpbi5uYW1lID09PSBwcm9wO1xuICAgICAgICB9KS5sZW5ndGggPT09IDA7XG4gICAgICB9XG4gICAgICB3YXJuV2hlbihkaWRQYXNzVW5rbm93blByb3AsIFtcImBcIiArIHByb3AgKyBcImBcIiwgXCJpcyBub3QgYSB2YWxpZCBwcm9wLiBZb3UgbWF5IGhhdmUgc3BlbGxlZCBpdCBpbmNvcnJlY3RseSwgb3IgaWYgaXQnc1wiLCBcImEgcGx1Z2luLCBmb3Jnb3QgdG8gcGFzcyBpdCBpbiBhbiBhcnJheSBhcyBwcm9wcy5wbHVnaW5zLlwiLCBcIlxcblxcblwiLCBcIkFsbCBwcm9wczogaHR0cHM6Ly9hdG9taWtzLmdpdGh1Yi5pby90aXBweWpzL3Y2L2FsbC1wcm9wcy9cXG5cIiwgXCJQbHVnaW5zOiBodHRwczovL2F0b21pa3MuZ2l0aHViLmlvL3RpcHB5anMvdjYvcGx1Z2lucy9cIl0uam9pbihcIiBcIikpO1xuICAgIH0pO1xuICB9XG4gIHZhciBpbm5lckhUTUwgPSBmdW5jdGlvbiBpbm5lckhUTUwyKCkge1xuICAgIHJldHVybiBcImlubmVySFRNTFwiO1xuICB9O1xuICBmdW5jdGlvbiBkYW5nZXJvdXNseVNldElubmVySFRNTChlbGVtZW50LCBodG1sKSB7XG4gICAgZWxlbWVudFtpbm5lckhUTUwoKV0gPSBodG1sO1xuICB9XG4gIGZ1bmN0aW9uIGNyZWF0ZUFycm93RWxlbWVudCh2YWx1ZSkge1xuICAgIHZhciBhcnJvdyA9IGRpdigpO1xuICAgIGlmICh2YWx1ZSA9PT0gdHJ1ZSkge1xuICAgICAgYXJyb3cuY2xhc3NOYW1lID0gQVJST1dfQ0xBU1M7XG4gICAgfSBlbHNlIHtcbiAgICAgIGFycm93LmNsYXNzTmFtZSA9IFNWR19BUlJPV19DTEFTUztcbiAgICAgIGlmIChpc0VsZW1lbnQodmFsdWUpKSB7XG4gICAgICAgIGFycm93LmFwcGVuZENoaWxkKHZhbHVlKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGRhbmdlcm91c2x5U2V0SW5uZXJIVE1MKGFycm93LCB2YWx1ZSk7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBhcnJvdztcbiAgfVxuICBmdW5jdGlvbiBzZXRDb250ZW50KGNvbnRlbnQsIHByb3BzKSB7XG4gICAgaWYgKGlzRWxlbWVudChwcm9wcy5jb250ZW50KSkge1xuICAgICAgZGFuZ2Vyb3VzbHlTZXRJbm5lckhUTUwoY29udGVudCwgXCJcIik7XG4gICAgICBjb250ZW50LmFwcGVuZENoaWxkKHByb3BzLmNvbnRlbnQpO1xuICAgIH0gZWxzZSBpZiAodHlwZW9mIHByb3BzLmNvbnRlbnQgIT09IFwiZnVuY3Rpb25cIikge1xuICAgICAgaWYgKHByb3BzLmFsbG93SFRNTCkge1xuICAgICAgICBkYW5nZXJvdXNseVNldElubmVySFRNTChjb250ZW50LCBwcm9wcy5jb250ZW50KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNvbnRlbnQudGV4dENvbnRlbnQgPSBwcm9wcy5jb250ZW50O1xuICAgICAgfVxuICAgIH1cbiAgfVxuICBmdW5jdGlvbiBnZXRDaGlsZHJlbihwb3BwZXIpIHtcbiAgICB2YXIgYm94ID0gcG9wcGVyLmZpcnN0RWxlbWVudENoaWxkO1xuICAgIHZhciBib3hDaGlsZHJlbiA9IGFycmF5RnJvbShib3guY2hpbGRyZW4pO1xuICAgIHJldHVybiB7XG4gICAgICBib3gsXG4gICAgICBjb250ZW50OiBib3hDaGlsZHJlbi5maW5kKGZ1bmN0aW9uKG5vZGUpIHtcbiAgICAgICAgcmV0dXJuIG5vZGUuY2xhc3NMaXN0LmNvbnRhaW5zKENPTlRFTlRfQ0xBU1MpO1xuICAgICAgfSksXG4gICAgICBhcnJvdzogYm94Q2hpbGRyZW4uZmluZChmdW5jdGlvbihub2RlKSB7XG4gICAgICAgIHJldHVybiBub2RlLmNsYXNzTGlzdC5jb250YWlucyhBUlJPV19DTEFTUykgfHwgbm9kZS5jbGFzc0xpc3QuY29udGFpbnMoU1ZHX0FSUk9XX0NMQVNTKTtcbiAgICAgIH0pLFxuICAgICAgYmFja2Ryb3A6IGJveENoaWxkcmVuLmZpbmQoZnVuY3Rpb24obm9kZSkge1xuICAgICAgICByZXR1cm4gbm9kZS5jbGFzc0xpc3QuY29udGFpbnMoQkFDS0RST1BfQ0xBU1MpO1xuICAgICAgfSlcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIHJlbmRlcihpbnN0YW5jZSkge1xuICAgIHZhciBwb3BwZXIgPSBkaXYoKTtcbiAgICB2YXIgYm94ID0gZGl2KCk7XG4gICAgYm94LmNsYXNzTmFtZSA9IEJPWF9DTEFTUztcbiAgICBib3guc2V0QXR0cmlidXRlKFwiZGF0YS1zdGF0ZVwiLCBcImhpZGRlblwiKTtcbiAgICBib3guc2V0QXR0cmlidXRlKFwidGFiaW5kZXhcIiwgXCItMVwiKTtcbiAgICB2YXIgY29udGVudCA9IGRpdigpO1xuICAgIGNvbnRlbnQuY2xhc3NOYW1lID0gQ09OVEVOVF9DTEFTUztcbiAgICBjb250ZW50LnNldEF0dHJpYnV0ZShcImRhdGEtc3RhdGVcIiwgXCJoaWRkZW5cIik7XG4gICAgc2V0Q29udGVudChjb250ZW50LCBpbnN0YW5jZS5wcm9wcyk7XG4gICAgcG9wcGVyLmFwcGVuZENoaWxkKGJveCk7XG4gICAgYm94LmFwcGVuZENoaWxkKGNvbnRlbnQpO1xuICAgIG9uVXBkYXRlKGluc3RhbmNlLnByb3BzLCBpbnN0YW5jZS5wcm9wcyk7XG4gICAgZnVuY3Rpb24gb25VcGRhdGUocHJldlByb3BzLCBuZXh0UHJvcHMpIHtcbiAgICAgIHZhciBfZ2V0Q2hpbGRyZW4gPSBnZXRDaGlsZHJlbihwb3BwZXIpLCBib3gyID0gX2dldENoaWxkcmVuLmJveCwgY29udGVudDIgPSBfZ2V0Q2hpbGRyZW4uY29udGVudCwgYXJyb3cgPSBfZ2V0Q2hpbGRyZW4uYXJyb3c7XG4gICAgICBpZiAobmV4dFByb3BzLnRoZW1lKSB7XG4gICAgICAgIGJveDIuc2V0QXR0cmlidXRlKFwiZGF0YS10aGVtZVwiLCBuZXh0UHJvcHMudGhlbWUpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYm94Mi5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLXRoZW1lXCIpO1xuICAgICAgfVxuICAgICAgaWYgKHR5cGVvZiBuZXh0UHJvcHMuYW5pbWF0aW9uID09PSBcInN0cmluZ1wiKSB7XG4gICAgICAgIGJveDIuc2V0QXR0cmlidXRlKFwiZGF0YS1hbmltYXRpb25cIiwgbmV4dFByb3BzLmFuaW1hdGlvbik7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBib3gyLnJlbW92ZUF0dHJpYnV0ZShcImRhdGEtYW5pbWF0aW9uXCIpO1xuICAgICAgfVxuICAgICAgaWYgKG5leHRQcm9wcy5pbmVydGlhKSB7XG4gICAgICAgIGJveDIuc2V0QXR0cmlidXRlKFwiZGF0YS1pbmVydGlhXCIsIFwiXCIpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYm94Mi5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLWluZXJ0aWFcIik7XG4gICAgICB9XG4gICAgICBib3gyLnN0eWxlLm1heFdpZHRoID0gdHlwZW9mIG5leHRQcm9wcy5tYXhXaWR0aCA9PT0gXCJudW1iZXJcIiA/IG5leHRQcm9wcy5tYXhXaWR0aCArIFwicHhcIiA6IG5leHRQcm9wcy5tYXhXaWR0aDtcbiAgICAgIGlmIChuZXh0UHJvcHMucm9sZSkge1xuICAgICAgICBib3gyLnNldEF0dHJpYnV0ZShcInJvbGVcIiwgbmV4dFByb3BzLnJvbGUpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYm94Mi5yZW1vdmVBdHRyaWJ1dGUoXCJyb2xlXCIpO1xuICAgICAgfVxuICAgICAgaWYgKHByZXZQcm9wcy5jb250ZW50ICE9PSBuZXh0UHJvcHMuY29udGVudCB8fCBwcmV2UHJvcHMuYWxsb3dIVE1MICE9PSBuZXh0UHJvcHMuYWxsb3dIVE1MKSB7XG4gICAgICAgIHNldENvbnRlbnQoY29udGVudDIsIGluc3RhbmNlLnByb3BzKTtcbiAgICAgIH1cbiAgICAgIGlmIChuZXh0UHJvcHMuYXJyb3cpIHtcbiAgICAgICAgaWYgKCFhcnJvdykge1xuICAgICAgICAgIGJveDIuYXBwZW5kQ2hpbGQoY3JlYXRlQXJyb3dFbGVtZW50KG5leHRQcm9wcy5hcnJvdykpO1xuICAgICAgICB9IGVsc2UgaWYgKHByZXZQcm9wcy5hcnJvdyAhPT0gbmV4dFByb3BzLmFycm93KSB7XG4gICAgICAgICAgYm94Mi5yZW1vdmVDaGlsZChhcnJvdyk7XG4gICAgICAgICAgYm94Mi5hcHBlbmRDaGlsZChjcmVhdGVBcnJvd0VsZW1lbnQobmV4dFByb3BzLmFycm93KSk7XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSBpZiAoYXJyb3cpIHtcbiAgICAgICAgYm94Mi5yZW1vdmVDaGlsZChhcnJvdyk7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiB7XG4gICAgICBwb3BwZXIsXG4gICAgICBvblVwZGF0ZVxuICAgIH07XG4gIH1cbiAgcmVuZGVyLiQkdGlwcHkgPSB0cnVlO1xuICB2YXIgaWRDb3VudGVyID0gMTtcbiAgdmFyIG1vdXNlTW92ZUxpc3RlbmVycyA9IFtdO1xuICB2YXIgbW91bnRlZEluc3RhbmNlcyA9IFtdO1xuICBmdW5jdGlvbiBjcmVhdGVUaXBweShyZWZlcmVuY2UsIHBhc3NlZFByb3BzKSB7XG4gICAgdmFyIHByb3BzID0gZXZhbHVhdGVQcm9wcyhyZWZlcmVuY2UsIE9iamVjdC5hc3NpZ24oe30sIGRlZmF1bHRQcm9wcywge30sIGdldEV4dGVuZGVkUGFzc2VkUHJvcHMocmVtb3ZlVW5kZWZpbmVkUHJvcHMocGFzc2VkUHJvcHMpKSkpO1xuICAgIHZhciBzaG93VGltZW91dDtcbiAgICB2YXIgaGlkZVRpbWVvdXQ7XG4gICAgdmFyIHNjaGVkdWxlSGlkZUFuaW1hdGlvbkZyYW1lO1xuICAgIHZhciBpc1Zpc2libGVGcm9tQ2xpY2sgPSBmYWxzZTtcbiAgICB2YXIgZGlkSGlkZUR1ZVRvRG9jdW1lbnRNb3VzZURvd24gPSBmYWxzZTtcbiAgICB2YXIgZGlkVG91Y2hNb3ZlID0gZmFsc2U7XG4gICAgdmFyIGlnbm9yZU9uRmlyc3RVcGRhdGUgPSBmYWxzZTtcbiAgICB2YXIgbGFzdFRyaWdnZXJFdmVudDtcbiAgICB2YXIgY3VycmVudFRyYW5zaXRpb25FbmRMaXN0ZW5lcjtcbiAgICB2YXIgb25GaXJzdFVwZGF0ZTtcbiAgICB2YXIgbGlzdGVuZXJzID0gW107XG4gICAgdmFyIGRlYm91bmNlZE9uTW91c2VNb3ZlID0gZGVib3VuY2Uob25Nb3VzZU1vdmUsIHByb3BzLmludGVyYWN0aXZlRGVib3VuY2UpO1xuICAgIHZhciBjdXJyZW50VGFyZ2V0O1xuICAgIHZhciBpZCA9IGlkQ291bnRlcisrO1xuICAgIHZhciBwb3BwZXJJbnN0YW5jZSA9IG51bGw7XG4gICAgdmFyIHBsdWdpbnMgPSB1bmlxdWUocHJvcHMucGx1Z2lucyk7XG4gICAgdmFyIHN0YXRlID0ge1xuICAgICAgaXNFbmFibGVkOiB0cnVlLFxuICAgICAgaXNWaXNpYmxlOiBmYWxzZSxcbiAgICAgIGlzRGVzdHJveWVkOiBmYWxzZSxcbiAgICAgIGlzTW91bnRlZDogZmFsc2UsXG4gICAgICBpc1Nob3duOiBmYWxzZVxuICAgIH07XG4gICAgdmFyIGluc3RhbmNlID0ge1xuICAgICAgaWQsXG4gICAgICByZWZlcmVuY2UsXG4gICAgICBwb3BwZXI6IGRpdigpLFxuICAgICAgcG9wcGVySW5zdGFuY2UsXG4gICAgICBwcm9wcyxcbiAgICAgIHN0YXRlLFxuICAgICAgcGx1Z2lucyxcbiAgICAgIGNsZWFyRGVsYXlUaW1lb3V0cyxcbiAgICAgIHNldFByb3BzLFxuICAgICAgc2V0Q29udGVudDogc2V0Q29udGVudDIsXG4gICAgICBzaG93LFxuICAgICAgaGlkZSxcbiAgICAgIGhpZGVXaXRoSW50ZXJhY3Rpdml0eSxcbiAgICAgIGVuYWJsZSxcbiAgICAgIGRpc2FibGUsXG4gICAgICB1bm1vdW50LFxuICAgICAgZGVzdHJveVxuICAgIH07XG4gICAgaWYgKCFwcm9wcy5yZW5kZXIpIHtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIGVycm9yV2hlbih0cnVlLCBcInJlbmRlcigpIGZ1bmN0aW9uIGhhcyBub3QgYmVlbiBzdXBwbGllZC5cIik7XG4gICAgICB9XG4gICAgICByZXR1cm4gaW5zdGFuY2U7XG4gICAgfVxuICAgIHZhciBfcHJvcHMkcmVuZGVyID0gcHJvcHMucmVuZGVyKGluc3RhbmNlKSwgcG9wcGVyID0gX3Byb3BzJHJlbmRlci5wb3BwZXIsIG9uVXBkYXRlID0gX3Byb3BzJHJlbmRlci5vblVwZGF0ZTtcbiAgICBwb3BwZXIuc2V0QXR0cmlidXRlKFwiZGF0YS10aXBweS1yb290XCIsIFwiXCIpO1xuICAgIHBvcHBlci5pZCA9IFwidGlwcHktXCIgKyBpbnN0YW5jZS5pZDtcbiAgICBpbnN0YW5jZS5wb3BwZXIgPSBwb3BwZXI7XG4gICAgcmVmZXJlbmNlLl90aXBweSA9IGluc3RhbmNlO1xuICAgIHBvcHBlci5fdGlwcHkgPSBpbnN0YW5jZTtcbiAgICB2YXIgcGx1Z2luc0hvb2tzID0gcGx1Z2lucy5tYXAoZnVuY3Rpb24ocGx1Z2luKSB7XG4gICAgICByZXR1cm4gcGx1Z2luLmZuKGluc3RhbmNlKTtcbiAgICB9KTtcbiAgICB2YXIgaGFzQXJpYUV4cGFuZGVkID0gcmVmZXJlbmNlLmhhc0F0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIik7XG4gICAgYWRkTGlzdGVuZXJzKCk7XG4gICAgaGFuZGxlQXJpYUV4cGFuZGVkQXR0cmlidXRlKCk7XG4gICAgaGFuZGxlU3R5bGVzKCk7XG4gICAgaW52b2tlSG9vayhcIm9uQ3JlYXRlXCIsIFtpbnN0YW5jZV0pO1xuICAgIGlmIChwcm9wcy5zaG93T25DcmVhdGUpIHtcbiAgICAgIHNjaGVkdWxlU2hvdygpO1xuICAgIH1cbiAgICBwb3BwZXIuYWRkRXZlbnRMaXN0ZW5lcihcIm1vdXNlZW50ZXJcIiwgZnVuY3Rpb24oKSB7XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUgJiYgaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlKSB7XG4gICAgICAgIGluc3RhbmNlLmNsZWFyRGVsYXlUaW1lb3V0cygpO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHBvcHBlci5hZGRFdmVudExpc3RlbmVyKFwibW91c2VsZWF2ZVwiLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLmludGVyYWN0aXZlICYmIGluc3RhbmNlLnByb3BzLnRyaWdnZXIuaW5kZXhPZihcIm1vdXNlZW50ZXJcIikgPj0gMCkge1xuICAgICAgICBnZXREb2N1bWVudCgpLmFkZEV2ZW50TGlzdGVuZXIoXCJtb3VzZW1vdmVcIiwgZGVib3VuY2VkT25Nb3VzZU1vdmUpO1xuICAgICAgICBkZWJvdW5jZWRPbk1vdXNlTW92ZShldmVudCk7XG4gICAgICB9XG4gICAgfSk7XG4gICAgcmV0dXJuIGluc3RhbmNlO1xuICAgIGZ1bmN0aW9uIGdldE5vcm1hbGl6ZWRUb3VjaFNldHRpbmdzKCkge1xuICAgICAgdmFyIHRvdWNoID0gaW5zdGFuY2UucHJvcHMudG91Y2g7XG4gICAgICByZXR1cm4gQXJyYXkuaXNBcnJheSh0b3VjaCkgPyB0b3VjaCA6IFt0b3VjaCwgMF07XG4gICAgfVxuICAgIGZ1bmN0aW9uIGdldElzQ3VzdG9tVG91Y2hCZWhhdmlvcigpIHtcbiAgICAgIHJldHVybiBnZXROb3JtYWxpemVkVG91Y2hTZXR0aW5ncygpWzBdID09PSBcImhvbGRcIjtcbiAgICB9XG4gICAgZnVuY3Rpb24gZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSB7XG4gICAgICB2YXIgX2luc3RhbmNlJHByb3BzJHJlbmRlO1xuICAgICAgcmV0dXJuICEhKChfaW5zdGFuY2UkcHJvcHMkcmVuZGUgPSBpbnN0YW5jZS5wcm9wcy5yZW5kZXIpID09IG51bGwgPyB2b2lkIDAgOiBfaW5zdGFuY2UkcHJvcHMkcmVuZGUuJCR0aXBweSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGdldEN1cnJlbnRUYXJnZXQoKSB7XG4gICAgICByZXR1cm4gY3VycmVudFRhcmdldCB8fCByZWZlcmVuY2U7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGdldERvY3VtZW50KCkge1xuICAgICAgdmFyIHBhcmVudCA9IGdldEN1cnJlbnRUYXJnZXQoKS5wYXJlbnROb2RlO1xuICAgICAgcmV0dXJuIHBhcmVudCA/IGdldE93bmVyRG9jdW1lbnQocGFyZW50KSA6IGRvY3VtZW50O1xuICAgIH1cbiAgICBmdW5jdGlvbiBnZXREZWZhdWx0VGVtcGxhdGVDaGlsZHJlbigpIHtcbiAgICAgIHJldHVybiBnZXRDaGlsZHJlbihwb3BwZXIpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBnZXREZWxheShpc1Nob3cpIHtcbiAgICAgIGlmIChpbnN0YW5jZS5zdGF0ZS5pc01vdW50ZWQgJiYgIWluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSB8fCBjdXJyZW50SW5wdXQuaXNUb3VjaCB8fCBsYXN0VHJpZ2dlckV2ZW50ICYmIGxhc3RUcmlnZ2VyRXZlbnQudHlwZSA9PT0gXCJmb2N1c1wiKSB7XG4gICAgICAgIHJldHVybiAwO1xuICAgICAgfVxuICAgICAgcmV0dXJuIGdldFZhbHVlQXRJbmRleE9yUmV0dXJuKGluc3RhbmNlLnByb3BzLmRlbGF5LCBpc1Nob3cgPyAwIDogMSwgZGVmYXVsdFByb3BzLmRlbGF5KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gaGFuZGxlU3R5bGVzKCkge1xuICAgICAgcG9wcGVyLnN0eWxlLnBvaW50ZXJFdmVudHMgPSBpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSAmJiBpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUgPyBcIlwiIDogXCJub25lXCI7XG4gICAgICBwb3BwZXIuc3R5bGUuekluZGV4ID0gXCJcIiArIGluc3RhbmNlLnByb3BzLnpJbmRleDtcbiAgICB9XG4gICAgZnVuY3Rpb24gaW52b2tlSG9vayhob29rLCBhcmdzLCBzaG91bGRJbnZva2VQcm9wc0hvb2spIHtcbiAgICAgIGlmIChzaG91bGRJbnZva2VQcm9wc0hvb2sgPT09IHZvaWQgMCkge1xuICAgICAgICBzaG91bGRJbnZva2VQcm9wc0hvb2sgPSB0cnVlO1xuICAgICAgfVxuICAgICAgcGx1Z2luc0hvb2tzLmZvckVhY2goZnVuY3Rpb24ocGx1Z2luSG9va3MpIHtcbiAgICAgICAgaWYgKHBsdWdpbkhvb2tzW2hvb2tdKSB7XG4gICAgICAgICAgcGx1Z2luSG9va3NbaG9va10uYXBwbHkodm9pZCAwLCBhcmdzKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgICBpZiAoc2hvdWxkSW52b2tlUHJvcHNIb29rKSB7XG4gICAgICAgIHZhciBfaW5zdGFuY2UkcHJvcHM7XG4gICAgICAgIChfaW5zdGFuY2UkcHJvcHMgPSBpbnN0YW5jZS5wcm9wcylbaG9va10uYXBwbHkoX2luc3RhbmNlJHByb3BzLCBhcmdzKTtcbiAgICAgIH1cbiAgICB9XG4gICAgZnVuY3Rpb24gaGFuZGxlQXJpYUNvbnRlbnRBdHRyaWJ1dGUoKSB7XG4gICAgICB2YXIgYXJpYSA9IGluc3RhbmNlLnByb3BzLmFyaWE7XG4gICAgICBpZiAoIWFyaWEuY29udGVudCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgYXR0ciA9IFwiYXJpYS1cIiArIGFyaWEuY29udGVudDtcbiAgICAgIHZhciBpZDIgPSBwb3BwZXIuaWQ7XG4gICAgICB2YXIgbm9kZXMgPSBub3JtYWxpemVUb0FycmF5KGluc3RhbmNlLnByb3BzLnRyaWdnZXJUYXJnZXQgfHwgcmVmZXJlbmNlKTtcbiAgICAgIG5vZGVzLmZvckVhY2goZnVuY3Rpb24obm9kZSkge1xuICAgICAgICB2YXIgY3VycmVudFZhbHVlID0gbm9kZS5nZXRBdHRyaWJ1dGUoYXR0cik7XG4gICAgICAgIGlmIChpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUpIHtcbiAgICAgICAgICBub2RlLnNldEF0dHJpYnV0ZShhdHRyLCBjdXJyZW50VmFsdWUgPyBjdXJyZW50VmFsdWUgKyBcIiBcIiArIGlkMiA6IGlkMik7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgdmFyIG5leHRWYWx1ZSA9IGN1cnJlbnRWYWx1ZSAmJiBjdXJyZW50VmFsdWUucmVwbGFjZShpZDIsIFwiXCIpLnRyaW0oKTtcbiAgICAgICAgICBpZiAobmV4dFZhbHVlKSB7XG4gICAgICAgICAgICBub2RlLnNldEF0dHJpYnV0ZShhdHRyLCBuZXh0VmFsdWUpO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBub2RlLnJlbW92ZUF0dHJpYnV0ZShhdHRyKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBoYW5kbGVBcmlhRXhwYW5kZWRBdHRyaWJ1dGUoKSB7XG4gICAgICBpZiAoaGFzQXJpYUV4cGFuZGVkIHx8ICFpbnN0YW5jZS5wcm9wcy5hcmlhLmV4cGFuZGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHZhciBub2RlcyA9IG5vcm1hbGl6ZVRvQXJyYXkoaW5zdGFuY2UucHJvcHMudHJpZ2dlclRhcmdldCB8fCByZWZlcmVuY2UpO1xuICAgICAgbm9kZXMuZm9yRWFjaChmdW5jdGlvbihub2RlKSB7XG4gICAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSkge1xuICAgICAgICAgIG5vZGUuc2V0QXR0cmlidXRlKFwiYXJpYS1leHBhbmRlZFwiLCBpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUgJiYgbm9kZSA9PT0gZ2V0Q3VycmVudFRhcmdldCgpID8gXCJ0cnVlXCIgOiBcImZhbHNlXCIpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIG5vZGUucmVtb3ZlQXR0cmlidXRlKFwiYXJpYS1leHBhbmRlZFwiKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGNsZWFudXBJbnRlcmFjdGl2ZU1vdXNlTGlzdGVuZXJzKCkge1xuICAgICAgZ2V0RG9jdW1lbnQoKS5yZW1vdmVFdmVudExpc3RlbmVyKFwibW91c2Vtb3ZlXCIsIGRlYm91bmNlZE9uTW91c2VNb3ZlKTtcbiAgICAgIG1vdXNlTW92ZUxpc3RlbmVycyA9IG1vdXNlTW92ZUxpc3RlbmVycy5maWx0ZXIoZnVuY3Rpb24obGlzdGVuZXIpIHtcbiAgICAgICAgcmV0dXJuIGxpc3RlbmVyICE9PSBkZWJvdW5jZWRPbk1vdXNlTW92ZTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBvbkRvY3VtZW50UHJlc3MoZXZlbnQpIHtcbiAgICAgIGlmIChjdXJyZW50SW5wdXQuaXNUb3VjaCkge1xuICAgICAgICBpZiAoZGlkVG91Y2hNb3ZlIHx8IGV2ZW50LnR5cGUgPT09IFwibW91c2Vkb3duXCIpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSAmJiBwb3BwZXIuY29udGFpbnMoZXZlbnQudGFyZ2V0KSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpZiAoZ2V0Q3VycmVudFRhcmdldCgpLmNvbnRhaW5zKGV2ZW50LnRhcmdldCkpIHtcbiAgICAgICAgaWYgKGN1cnJlbnRJbnB1dC5pc1RvdWNoKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUgJiYgaW5zdGFuY2UucHJvcHMudHJpZ2dlci5pbmRleE9mKFwiY2xpY2tcIikgPj0gMCkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgaW52b2tlSG9vayhcIm9uQ2xpY2tPdXRzaWRlXCIsIFtpbnN0YW5jZSwgZXZlbnRdKTtcbiAgICAgIH1cbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5oaWRlT25DbGljayA9PT0gdHJ1ZSkge1xuICAgICAgICBpbnN0YW5jZS5jbGVhckRlbGF5VGltZW91dHMoKTtcbiAgICAgICAgaW5zdGFuY2UuaGlkZSgpO1xuICAgICAgICBkaWRIaWRlRHVlVG9Eb2N1bWVudE1vdXNlRG93biA9IHRydWU7XG4gICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgZGlkSGlkZUR1ZVRvRG9jdW1lbnRNb3VzZURvd24gPSBmYWxzZTtcbiAgICAgICAgfSk7XG4gICAgICAgIGlmICghaW5zdGFuY2Uuc3RhdGUuaXNNb3VudGVkKSB7XG4gICAgICAgICAgcmVtb3ZlRG9jdW1lbnRQcmVzcygpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uVG91Y2hNb3ZlKCkge1xuICAgICAgZGlkVG91Y2hNb3ZlID0gdHJ1ZTtcbiAgICB9XG4gICAgZnVuY3Rpb24gb25Ub3VjaFN0YXJ0KCkge1xuICAgICAgZGlkVG91Y2hNb3ZlID0gZmFsc2U7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGFkZERvY3VtZW50UHJlc3MoKSB7XG4gICAgICB2YXIgZG9jID0gZ2V0RG9jdW1lbnQoKTtcbiAgICAgIGRvYy5hZGRFdmVudExpc3RlbmVyKFwibW91c2Vkb3duXCIsIG9uRG9jdW1lbnRQcmVzcywgdHJ1ZSk7XG4gICAgICBkb2MuYWRkRXZlbnRMaXN0ZW5lcihcInRvdWNoZW5kXCIsIG9uRG9jdW1lbnRQcmVzcywgVE9VQ0hfT1BUSU9OUyk7XG4gICAgICBkb2MuYWRkRXZlbnRMaXN0ZW5lcihcInRvdWNoc3RhcnRcIiwgb25Ub3VjaFN0YXJ0LCBUT1VDSF9PUFRJT05TKTtcbiAgICAgIGRvYy5hZGRFdmVudExpc3RlbmVyKFwidG91Y2htb3ZlXCIsIG9uVG91Y2hNb3ZlLCBUT1VDSF9PUFRJT05TKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gcmVtb3ZlRG9jdW1lbnRQcmVzcygpIHtcbiAgICAgIHZhciBkb2MgPSBnZXREb2N1bWVudCgpO1xuICAgICAgZG9jLnJlbW92ZUV2ZW50TGlzdGVuZXIoXCJtb3VzZWRvd25cIiwgb25Eb2N1bWVudFByZXNzLCB0cnVlKTtcbiAgICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwidG91Y2hlbmRcIiwgb25Eb2N1bWVudFByZXNzLCBUT1VDSF9PUFRJT05TKTtcbiAgICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwidG91Y2hzdGFydFwiLCBvblRvdWNoU3RhcnQsIFRPVUNIX09QVElPTlMpO1xuICAgICAgZG9jLnJlbW92ZUV2ZW50TGlzdGVuZXIoXCJ0b3VjaG1vdmVcIiwgb25Ub3VjaE1vdmUsIFRPVUNIX09QVElPTlMpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBvblRyYW5zaXRpb25lZE91dChkdXJhdGlvbiwgY2FsbGJhY2spIHtcbiAgICAgIG9uVHJhbnNpdGlvbkVuZChkdXJhdGlvbiwgZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmICghaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlICYmIHBvcHBlci5wYXJlbnROb2RlICYmIHBvcHBlci5wYXJlbnROb2RlLmNvbnRhaW5zKHBvcHBlcikpIHtcbiAgICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gb25UcmFuc2l0aW9uZWRJbihkdXJhdGlvbiwgY2FsbGJhY2spIHtcbiAgICAgIG9uVHJhbnNpdGlvbkVuZChkdXJhdGlvbiwgY2FsbGJhY2spO1xuICAgIH1cbiAgICBmdW5jdGlvbiBvblRyYW5zaXRpb25FbmQoZHVyYXRpb24sIGNhbGxiYWNrKSB7XG4gICAgICB2YXIgYm94ID0gZ2V0RGVmYXVsdFRlbXBsYXRlQ2hpbGRyZW4oKS5ib3g7XG4gICAgICBmdW5jdGlvbiBsaXN0ZW5lcihldmVudCkge1xuICAgICAgICBpZiAoZXZlbnQudGFyZ2V0ID09PSBib3gpIHtcbiAgICAgICAgICB1cGRhdGVUcmFuc2l0aW9uRW5kTGlzdGVuZXIoYm94LCBcInJlbW92ZVwiLCBsaXN0ZW5lcik7XG4gICAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgaWYgKGR1cmF0aW9uID09PSAwKSB7XG4gICAgICAgIHJldHVybiBjYWxsYmFjaygpO1xuICAgICAgfVxuICAgICAgdXBkYXRlVHJhbnNpdGlvbkVuZExpc3RlbmVyKGJveCwgXCJyZW1vdmVcIiwgY3VycmVudFRyYW5zaXRpb25FbmRMaXN0ZW5lcik7XG4gICAgICB1cGRhdGVUcmFuc2l0aW9uRW5kTGlzdGVuZXIoYm94LCBcImFkZFwiLCBsaXN0ZW5lcik7XG4gICAgICBjdXJyZW50VHJhbnNpdGlvbkVuZExpc3RlbmVyID0gbGlzdGVuZXI7XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uKGV2ZW50VHlwZSwgaGFuZGxlciwgb3B0aW9ucykge1xuICAgICAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgICAgICBvcHRpb25zID0gZmFsc2U7XG4gICAgICB9XG4gICAgICB2YXIgbm9kZXMgPSBub3JtYWxpemVUb0FycmF5KGluc3RhbmNlLnByb3BzLnRyaWdnZXJUYXJnZXQgfHwgcmVmZXJlbmNlKTtcbiAgICAgIG5vZGVzLmZvckVhY2goZnVuY3Rpb24obm9kZSkge1xuICAgICAgICBub2RlLmFkZEV2ZW50TGlzdGVuZXIoZXZlbnRUeXBlLCBoYW5kbGVyLCBvcHRpb25zKTtcbiAgICAgICAgbGlzdGVuZXJzLnB1c2goe1xuICAgICAgICAgIG5vZGUsXG4gICAgICAgICAgZXZlbnRUeXBlLFxuICAgICAgICAgIGhhbmRsZXIsXG4gICAgICAgICAgb3B0aW9uc1xuICAgICAgICB9KTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBhZGRMaXN0ZW5lcnMoKSB7XG4gICAgICBpZiAoZ2V0SXNDdXN0b21Ub3VjaEJlaGF2aW9yKCkpIHtcbiAgICAgICAgb24oXCJ0b3VjaHN0YXJ0XCIsIG9uVHJpZ2dlciwge1xuICAgICAgICAgIHBhc3NpdmU6IHRydWVcbiAgICAgICAgfSk7XG4gICAgICAgIG9uKFwidG91Y2hlbmRcIiwgb25Nb3VzZUxlYXZlLCB7XG4gICAgICAgICAgcGFzc2l2ZTogdHJ1ZVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIHNwbGl0QnlTcGFjZXMoaW5zdGFuY2UucHJvcHMudHJpZ2dlcikuZm9yRWFjaChmdW5jdGlvbihldmVudFR5cGUpIHtcbiAgICAgICAgaWYgKGV2ZW50VHlwZSA9PT0gXCJtYW51YWxcIikge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBvbihldmVudFR5cGUsIG9uVHJpZ2dlcik7XG4gICAgICAgIHN3aXRjaCAoZXZlbnRUeXBlKSB7XG4gICAgICAgICAgY2FzZSBcIm1vdXNlZW50ZXJcIjpcbiAgICAgICAgICAgIG9uKFwibW91c2VsZWF2ZVwiLCBvbk1vdXNlTGVhdmUpO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgY2FzZSBcImZvY3VzXCI6XG4gICAgICAgICAgICBvbihpc0lFID8gXCJmb2N1c291dFwiIDogXCJibHVyXCIsIG9uQmx1ck9yRm9jdXNPdXQpO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgY2FzZSBcImZvY3VzaW5cIjpcbiAgICAgICAgICAgIG9uKFwiZm9jdXNvdXRcIiwgb25CbHVyT3JGb2N1c091dCk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIHJlbW92ZUxpc3RlbmVycygpIHtcbiAgICAgIGxpc3RlbmVycy5mb3JFYWNoKGZ1bmN0aW9uKF9yZWYpIHtcbiAgICAgICAgdmFyIG5vZGUgPSBfcmVmLm5vZGUsIGV2ZW50VHlwZSA9IF9yZWYuZXZlbnRUeXBlLCBoYW5kbGVyID0gX3JlZi5oYW5kbGVyLCBvcHRpb25zID0gX3JlZi5vcHRpb25zO1xuICAgICAgICBub2RlLnJlbW92ZUV2ZW50TGlzdGVuZXIoZXZlbnRUeXBlLCBoYW5kbGVyLCBvcHRpb25zKTtcbiAgICAgIH0pO1xuICAgICAgbGlzdGVuZXJzID0gW107XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uVHJpZ2dlcihldmVudCkge1xuICAgICAgdmFyIF9sYXN0VHJpZ2dlckV2ZW50O1xuICAgICAgdmFyIHNob3VsZFNjaGVkdWxlQ2xpY2tIaWRlID0gZmFsc2U7XG4gICAgICBpZiAoIWluc3RhbmNlLnN0YXRlLmlzRW5hYmxlZCB8fCBpc0V2ZW50TGlzdGVuZXJTdG9wcGVkKGV2ZW50KSB8fCBkaWRIaWRlRHVlVG9Eb2N1bWVudE1vdXNlRG93bikge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgd2FzRm9jdXNlZCA9ICgoX2xhc3RUcmlnZ2VyRXZlbnQgPSBsYXN0VHJpZ2dlckV2ZW50KSA9PSBudWxsID8gdm9pZCAwIDogX2xhc3RUcmlnZ2VyRXZlbnQudHlwZSkgPT09IFwiZm9jdXNcIjtcbiAgICAgIGxhc3RUcmlnZ2VyRXZlbnQgPSBldmVudDtcbiAgICAgIGN1cnJlbnRUYXJnZXQgPSBldmVudC5jdXJyZW50VGFyZ2V0O1xuICAgICAgaGFuZGxlQXJpYUV4cGFuZGVkQXR0cmlidXRlKCk7XG4gICAgICBpZiAoIWluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSAmJiBpc01vdXNlRXZlbnQoZXZlbnQpKSB7XG4gICAgICAgIG1vdXNlTW92ZUxpc3RlbmVycy5mb3JFYWNoKGZ1bmN0aW9uKGxpc3RlbmVyKSB7XG4gICAgICAgICAgcmV0dXJuIGxpc3RlbmVyKGV2ZW50KTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBpZiAoZXZlbnQudHlwZSA9PT0gXCJjbGlja1wiICYmIChpbnN0YW5jZS5wcm9wcy50cmlnZ2VyLmluZGV4T2YoXCJtb3VzZWVudGVyXCIpIDwgMCB8fCBpc1Zpc2libGVGcm9tQ2xpY2spICYmIGluc3RhbmNlLnByb3BzLmhpZGVPbkNsaWNrICE9PSBmYWxzZSAmJiBpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUpIHtcbiAgICAgICAgc2hvdWxkU2NoZWR1bGVDbGlja0hpZGUgPSB0cnVlO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgc2NoZWR1bGVTaG93KGV2ZW50KTtcbiAgICAgIH1cbiAgICAgIGlmIChldmVudC50eXBlID09PSBcImNsaWNrXCIpIHtcbiAgICAgICAgaXNWaXNpYmxlRnJvbUNsaWNrID0gIXNob3VsZFNjaGVkdWxlQ2xpY2tIaWRlO1xuICAgICAgfVxuICAgICAgaWYgKHNob3VsZFNjaGVkdWxlQ2xpY2tIaWRlICYmICF3YXNGb2N1c2VkKSB7XG4gICAgICAgIHNjaGVkdWxlSGlkZShldmVudCk7XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uTW91c2VNb3ZlKGV2ZW50KSB7XG4gICAgICB2YXIgdGFyZ2V0ID0gZXZlbnQudGFyZ2V0O1xuICAgICAgdmFyIGlzQ3Vyc29yT3ZlclJlZmVyZW5jZU9yUG9wcGVyID0gZ2V0Q3VycmVudFRhcmdldCgpLmNvbnRhaW5zKHRhcmdldCkgfHwgcG9wcGVyLmNvbnRhaW5zKHRhcmdldCk7XG4gICAgICBpZiAoZXZlbnQudHlwZSA9PT0gXCJtb3VzZW1vdmVcIiAmJiBpc0N1cnNvck92ZXJSZWZlcmVuY2VPclBvcHBlcikge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgcG9wcGVyVHJlZURhdGEgPSBnZXROZXN0ZWRQb3BwZXJUcmVlKCkuY29uY2F0KHBvcHBlcikubWFwKGZ1bmN0aW9uKHBvcHBlcjIpIHtcbiAgICAgICAgdmFyIF9pbnN0YW5jZSRwb3BwZXJJbnN0YTtcbiAgICAgICAgdmFyIGluc3RhbmNlMiA9IHBvcHBlcjIuX3RpcHB5O1xuICAgICAgICB2YXIgc3RhdGUyID0gKF9pbnN0YW5jZSRwb3BwZXJJbnN0YSA9IGluc3RhbmNlMi5wb3BwZXJJbnN0YW5jZSkgPT0gbnVsbCA/IHZvaWQgMCA6IF9pbnN0YW5jZSRwb3BwZXJJbnN0YS5zdGF0ZTtcbiAgICAgICAgaWYgKHN0YXRlMikge1xuICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICBwb3BwZXJSZWN0OiBwb3BwZXIyLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLFxuICAgICAgICAgICAgcG9wcGVyU3RhdGU6IHN0YXRlMixcbiAgICAgICAgICAgIHByb3BzXG4gICAgICAgICAgfTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgIH0pLmZpbHRlcihCb29sZWFuKTtcbiAgICAgIGlmIChpc0N1cnNvck91dHNpZGVJbnRlcmFjdGl2ZUJvcmRlcihwb3BwZXJUcmVlRGF0YSwgZXZlbnQpKSB7XG4gICAgICAgIGNsZWFudXBJbnRlcmFjdGl2ZU1vdXNlTGlzdGVuZXJzKCk7XG4gICAgICAgIHNjaGVkdWxlSGlkZShldmVudCk7XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uTW91c2VMZWF2ZShldmVudCkge1xuICAgICAgdmFyIHNob3VsZEJhaWwgPSBpc0V2ZW50TGlzdGVuZXJTdG9wcGVkKGV2ZW50KSB8fCBpbnN0YW5jZS5wcm9wcy50cmlnZ2VyLmluZGV4T2YoXCJjbGlja1wiKSA+PSAwICYmIGlzVmlzaWJsZUZyb21DbGljaztcbiAgICAgIGlmIChzaG91bGRCYWlsKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSkge1xuICAgICAgICBpbnN0YW5jZS5oaWRlV2l0aEludGVyYWN0aXZpdHkoZXZlbnQpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBzY2hlZHVsZUhpZGUoZXZlbnQpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBvbkJsdXJPckZvY3VzT3V0KGV2ZW50KSB7XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMudHJpZ2dlci5pbmRleE9mKFwiZm9jdXNpblwiKSA8IDAgJiYgZXZlbnQudGFyZ2V0ICE9PSBnZXRDdXJyZW50VGFyZ2V0KCkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLmludGVyYWN0aXZlICYmIGV2ZW50LnJlbGF0ZWRUYXJnZXQgJiYgcG9wcGVyLmNvbnRhaW5zKGV2ZW50LnJlbGF0ZWRUYXJnZXQpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHNjaGVkdWxlSGlkZShldmVudCk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGlzRXZlbnRMaXN0ZW5lclN0b3BwZWQoZXZlbnQpIHtcbiAgICAgIHJldHVybiBjdXJyZW50SW5wdXQuaXNUb3VjaCA/IGdldElzQ3VzdG9tVG91Y2hCZWhhdmlvcigpICE9PSBldmVudC50eXBlLmluZGV4T2YoXCJ0b3VjaFwiKSA+PSAwIDogZmFsc2U7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGNyZWF0ZVBvcHBlckluc3RhbmNlKCkge1xuICAgICAgZGVzdHJveVBvcHBlckluc3RhbmNlKCk7XG4gICAgICB2YXIgX2luc3RhbmNlJHByb3BzMiA9IGluc3RhbmNlLnByb3BzLCBwb3BwZXJPcHRpb25zID0gX2luc3RhbmNlJHByb3BzMi5wb3BwZXJPcHRpb25zLCBwbGFjZW1lbnQgPSBfaW5zdGFuY2UkcHJvcHMyLnBsYWNlbWVudCwgb2Zmc2V0ID0gX2luc3RhbmNlJHByb3BzMi5vZmZzZXQsIGdldFJlZmVyZW5jZUNsaWVudFJlY3QgPSBfaW5zdGFuY2UkcHJvcHMyLmdldFJlZmVyZW5jZUNsaWVudFJlY3QsIG1vdmVUcmFuc2l0aW9uID0gX2luc3RhbmNlJHByb3BzMi5tb3ZlVHJhbnNpdGlvbjtcbiAgICAgIHZhciBhcnJvdyA9IGdldElzRGVmYXVsdFJlbmRlckZuKCkgPyBnZXRDaGlsZHJlbihwb3BwZXIpLmFycm93IDogbnVsbDtcbiAgICAgIHZhciBjb21wdXRlZFJlZmVyZW5jZSA9IGdldFJlZmVyZW5jZUNsaWVudFJlY3QgPyB7XG4gICAgICAgIGdldEJvdW5kaW5nQ2xpZW50UmVjdDogZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdCxcbiAgICAgICAgY29udGV4dEVsZW1lbnQ6IGdldFJlZmVyZW5jZUNsaWVudFJlY3QuY29udGV4dEVsZW1lbnQgfHwgZ2V0Q3VycmVudFRhcmdldCgpXG4gICAgICB9IDogcmVmZXJlbmNlO1xuICAgICAgdmFyIHRpcHB5TW9kaWZpZXIgPSB7XG4gICAgICAgIG5hbWU6IFwiJCR0aXBweVwiLFxuICAgICAgICBlbmFibGVkOiB0cnVlLFxuICAgICAgICBwaGFzZTogXCJiZWZvcmVXcml0ZVwiLFxuICAgICAgICByZXF1aXJlczogW1wiY29tcHV0ZVN0eWxlc1wiXSxcbiAgICAgICAgZm46IGZ1bmN0aW9uIGZuKF9yZWYyKSB7XG4gICAgICAgICAgdmFyIHN0YXRlMiA9IF9yZWYyLnN0YXRlO1xuICAgICAgICAgIGlmIChnZXRJc0RlZmF1bHRSZW5kZXJGbigpKSB7XG4gICAgICAgICAgICB2YXIgX2dldERlZmF1bHRUZW1wbGF0ZUNoID0gZ2V0RGVmYXVsdFRlbXBsYXRlQ2hpbGRyZW4oKSwgYm94ID0gX2dldERlZmF1bHRUZW1wbGF0ZUNoLmJveDtcbiAgICAgICAgICAgIFtcInBsYWNlbWVudFwiLCBcInJlZmVyZW5jZS1oaWRkZW5cIiwgXCJlc2NhcGVkXCJdLmZvckVhY2goZnVuY3Rpb24oYXR0cikge1xuICAgICAgICAgICAgICBpZiAoYXR0ciA9PT0gXCJwbGFjZW1lbnRcIikge1xuICAgICAgICAgICAgICAgIGJveC5zZXRBdHRyaWJ1dGUoXCJkYXRhLXBsYWNlbWVudFwiLCBzdGF0ZTIucGxhY2VtZW50KTtcbiAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBpZiAoc3RhdGUyLmF0dHJpYnV0ZXMucG9wcGVyW1wiZGF0YS1wb3BwZXItXCIgKyBhdHRyXSkge1xuICAgICAgICAgICAgICAgICAgYm94LnNldEF0dHJpYnV0ZShcImRhdGEtXCIgKyBhdHRyLCBcIlwiKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgYm94LnJlbW92ZUF0dHJpYnV0ZShcImRhdGEtXCIgKyBhdHRyKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgc3RhdGUyLmF0dHJpYnV0ZXMucG9wcGVyID0ge307XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9O1xuICAgICAgdmFyIG1vZGlmaWVycyA9IFt7XG4gICAgICAgIG5hbWU6IFwib2Zmc2V0XCIsXG4gICAgICAgIG9wdGlvbnM6IHtcbiAgICAgICAgICBvZmZzZXRcbiAgICAgICAgfVxuICAgICAgfSwge1xuICAgICAgICBuYW1lOiBcInByZXZlbnRPdmVyZmxvd1wiLFxuICAgICAgICBvcHRpb25zOiB7XG4gICAgICAgICAgcGFkZGluZzoge1xuICAgICAgICAgICAgdG9wOiAyLFxuICAgICAgICAgICAgYm90dG9tOiAyLFxuICAgICAgICAgICAgbGVmdDogNSxcbiAgICAgICAgICAgIHJpZ2h0OiA1XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9LCB7XG4gICAgICAgIG5hbWU6IFwiZmxpcFwiLFxuICAgICAgICBvcHRpb25zOiB7XG4gICAgICAgICAgcGFkZGluZzogNVxuICAgICAgICB9XG4gICAgICB9LCB7XG4gICAgICAgIG5hbWU6IFwiY29tcHV0ZVN0eWxlc1wiLFxuICAgICAgICBvcHRpb25zOiB7XG4gICAgICAgICAgYWRhcHRpdmU6ICFtb3ZlVHJhbnNpdGlvblxuICAgICAgICB9XG4gICAgICB9LCB0aXBweU1vZGlmaWVyXTtcbiAgICAgIGlmIChnZXRJc0RlZmF1bHRSZW5kZXJGbigpICYmIGFycm93KSB7XG4gICAgICAgIG1vZGlmaWVycy5wdXNoKHtcbiAgICAgICAgICBuYW1lOiBcImFycm93XCIsXG4gICAgICAgICAgb3B0aW9uczoge1xuICAgICAgICAgICAgZWxlbWVudDogYXJyb3csXG4gICAgICAgICAgICBwYWRkaW5nOiAzXG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIG1vZGlmaWVycy5wdXNoLmFwcGx5KG1vZGlmaWVycywgKHBvcHBlck9wdGlvbnMgPT0gbnVsbCA/IHZvaWQgMCA6IHBvcHBlck9wdGlvbnMubW9kaWZpZXJzKSB8fCBbXSk7XG4gICAgICBpbnN0YW5jZS5wb3BwZXJJbnN0YW5jZSA9IGNvcmUuY3JlYXRlUG9wcGVyKGNvbXB1dGVkUmVmZXJlbmNlLCBwb3BwZXIsIE9iamVjdC5hc3NpZ24oe30sIHBvcHBlck9wdGlvbnMsIHtcbiAgICAgICAgcGxhY2VtZW50LFxuICAgICAgICBvbkZpcnN0VXBkYXRlLFxuICAgICAgICBtb2RpZmllcnNcbiAgICAgIH0pKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gZGVzdHJveVBvcHBlckluc3RhbmNlKCkge1xuICAgICAgaWYgKGluc3RhbmNlLnBvcHBlckluc3RhbmNlKSB7XG4gICAgICAgIGluc3RhbmNlLnBvcHBlckluc3RhbmNlLmRlc3Ryb3koKTtcbiAgICAgICAgaW5zdGFuY2UucG9wcGVySW5zdGFuY2UgPSBudWxsO1xuICAgICAgfVxuICAgIH1cbiAgICBmdW5jdGlvbiBtb3VudCgpIHtcbiAgICAgIHZhciBhcHBlbmRUbyA9IGluc3RhbmNlLnByb3BzLmFwcGVuZFRvO1xuICAgICAgdmFyIHBhcmVudE5vZGU7XG4gICAgICB2YXIgbm9kZSA9IGdldEN1cnJlbnRUYXJnZXQoKTtcbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSAmJiBhcHBlbmRUbyA9PT0gZGVmYXVsdFByb3BzLmFwcGVuZFRvIHx8IGFwcGVuZFRvID09PSBcInBhcmVudFwiKSB7XG4gICAgICAgIHBhcmVudE5vZGUgPSBub2RlLnBhcmVudE5vZGU7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBwYXJlbnROb2RlID0gaW52b2tlV2l0aEFyZ3NPclJldHVybihhcHBlbmRUbywgW25vZGVdKTtcbiAgICAgIH1cbiAgICAgIGlmICghcGFyZW50Tm9kZS5jb250YWlucyhwb3BwZXIpKSB7XG4gICAgICAgIHBhcmVudE5vZGUuYXBwZW5kQ2hpbGQocG9wcGVyKTtcbiAgICAgIH1cbiAgICAgIGNyZWF0ZVBvcHBlckluc3RhbmNlKCk7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICB3YXJuV2hlbihpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSAmJiBhcHBlbmRUbyA9PT0gZGVmYXVsdFByb3BzLmFwcGVuZFRvICYmIG5vZGUubmV4dEVsZW1lbnRTaWJsaW5nICE9PSBwb3BwZXIsIFtcIkludGVyYWN0aXZlIHRpcHB5IGVsZW1lbnQgbWF5IG5vdCBiZSBhY2Nlc3NpYmxlIHZpYSBrZXlib2FyZFwiLCBcIm5hdmlnYXRpb24gYmVjYXVzZSBpdCBpcyBub3QgZGlyZWN0bHkgYWZ0ZXIgdGhlIHJlZmVyZW5jZSBlbGVtZW50XCIsIFwiaW4gdGhlIERPTSBzb3VyY2Ugb3JkZXIuXCIsIFwiXFxuXFxuXCIsIFwiVXNpbmcgYSB3cmFwcGVyIDxkaXY+IG9yIDxzcGFuPiB0YWcgYXJvdW5kIHRoZSByZWZlcmVuY2UgZWxlbWVudFwiLCBcInNvbHZlcyB0aGlzIGJ5IGNyZWF0aW5nIGEgbmV3IHBhcmVudE5vZGUgY29udGV4dC5cIiwgXCJcXG5cXG5cIiwgXCJTcGVjaWZ5aW5nIGBhcHBlbmRUbzogZG9jdW1lbnQuYm9keWAgc2lsZW5jZXMgdGhpcyB3YXJuaW5nLCBidXQgaXRcIiwgXCJhc3N1bWVzIHlvdSBhcmUgdXNpbmcgYSBmb2N1cyBtYW5hZ2VtZW50IHNvbHV0aW9uIHRvIGhhbmRsZVwiLCBcImtleWJvYXJkIG5hdmlnYXRpb24uXCIsIFwiXFxuXFxuXCIsIFwiU2VlOiBodHRwczovL2F0b21pa3MuZ2l0aHViLmlvL3RpcHB5anMvdjYvYWNjZXNzaWJpbGl0eS8jaW50ZXJhY3Rpdml0eVwiXS5qb2luKFwiIFwiKSk7XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIGdldE5lc3RlZFBvcHBlclRyZWUoKSB7XG4gICAgICByZXR1cm4gYXJyYXlGcm9tKHBvcHBlci5xdWVyeVNlbGVjdG9yQWxsKFwiW2RhdGEtdGlwcHktcm9vdF1cIikpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBzY2hlZHVsZVNob3coZXZlbnQpIHtcbiAgICAgIGluc3RhbmNlLmNsZWFyRGVsYXlUaW1lb3V0cygpO1xuICAgICAgaWYgKGV2ZW50KSB7XG4gICAgICAgIGludm9rZUhvb2soXCJvblRyaWdnZXJcIiwgW2luc3RhbmNlLCBldmVudF0pO1xuICAgICAgfVxuICAgICAgYWRkRG9jdW1lbnRQcmVzcygpO1xuICAgICAgdmFyIGRlbGF5ID0gZ2V0RGVsYXkodHJ1ZSk7XG4gICAgICB2YXIgX2dldE5vcm1hbGl6ZWRUb3VjaFNlID0gZ2V0Tm9ybWFsaXplZFRvdWNoU2V0dGluZ3MoKSwgdG91Y2hWYWx1ZSA9IF9nZXROb3JtYWxpemVkVG91Y2hTZVswXSwgdG91Y2hEZWxheSA9IF9nZXROb3JtYWxpemVkVG91Y2hTZVsxXTtcbiAgICAgIGlmIChjdXJyZW50SW5wdXQuaXNUb3VjaCAmJiB0b3VjaFZhbHVlID09PSBcImhvbGRcIiAmJiB0b3VjaERlbGF5KSB7XG4gICAgICAgIGRlbGF5ID0gdG91Y2hEZWxheTtcbiAgICAgIH1cbiAgICAgIGlmIChkZWxheSkge1xuICAgICAgICBzaG93VGltZW91dCA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgaW5zdGFuY2Uuc2hvdygpO1xuICAgICAgICB9LCBkZWxheSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBpbnN0YW5jZS5zaG93KCk7XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIHNjaGVkdWxlSGlkZShldmVudCkge1xuICAgICAgaW5zdGFuY2UuY2xlYXJEZWxheVRpbWVvdXRzKCk7XG4gICAgICBpbnZva2VIb29rKFwib25VbnRyaWdnZXJcIiwgW2luc3RhbmNlLCBldmVudF0pO1xuICAgICAgaWYgKCFpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUpIHtcbiAgICAgICAgcmVtb3ZlRG9jdW1lbnRQcmVzcygpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMudHJpZ2dlci5pbmRleE9mKFwibW91c2VlbnRlclwiKSA+PSAwICYmIGluc3RhbmNlLnByb3BzLnRyaWdnZXIuaW5kZXhPZihcImNsaWNrXCIpID49IDAgJiYgW1wibW91c2VsZWF2ZVwiLCBcIm1vdXNlbW92ZVwiXS5pbmRleE9mKGV2ZW50LnR5cGUpID49IDAgJiYgaXNWaXNpYmxlRnJvbUNsaWNrKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHZhciBkZWxheSA9IGdldERlbGF5KGZhbHNlKTtcbiAgICAgIGlmIChkZWxheSkge1xuICAgICAgICBoaWRlVGltZW91dCA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgaWYgKGluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSkge1xuICAgICAgICAgICAgaW5zdGFuY2UuaGlkZSgpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSwgZGVsYXkpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgc2NoZWR1bGVIaWRlQW5pbWF0aW9uRnJhbWUgPSByZXF1ZXN0QW5pbWF0aW9uRnJhbWUoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgaW5zdGFuY2UuaGlkZSgpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9XG4gICAgZnVuY3Rpb24gZW5hYmxlKCkge1xuICAgICAgaW5zdGFuY2Uuc3RhdGUuaXNFbmFibGVkID0gdHJ1ZTtcbiAgICB9XG4gICAgZnVuY3Rpb24gZGlzYWJsZSgpIHtcbiAgICAgIGluc3RhbmNlLmhpZGUoKTtcbiAgICAgIGluc3RhbmNlLnN0YXRlLmlzRW5hYmxlZCA9IGZhbHNlO1xuICAgIH1cbiAgICBmdW5jdGlvbiBjbGVhckRlbGF5VGltZW91dHMoKSB7XG4gICAgICBjbGVhclRpbWVvdXQoc2hvd1RpbWVvdXQpO1xuICAgICAgY2xlYXJUaW1lb3V0KGhpZGVUaW1lb3V0KTtcbiAgICAgIGNhbmNlbEFuaW1hdGlvbkZyYW1lKHNjaGVkdWxlSGlkZUFuaW1hdGlvbkZyYW1lKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gc2V0UHJvcHMocGFydGlhbFByb3BzKSB7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICB3YXJuV2hlbihpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZCwgY3JlYXRlTWVtb3J5TGVha1dhcm5pbmcoXCJzZXRQcm9wc1wiKSk7XG4gICAgICB9XG4gICAgICBpZiAoaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaW52b2tlSG9vayhcIm9uQmVmb3JlVXBkYXRlXCIsIFtpbnN0YW5jZSwgcGFydGlhbFByb3BzXSk7XG4gICAgICByZW1vdmVMaXN0ZW5lcnMoKTtcbiAgICAgIHZhciBwcmV2UHJvcHMgPSBpbnN0YW5jZS5wcm9wcztcbiAgICAgIHZhciBuZXh0UHJvcHMgPSBldmFsdWF0ZVByb3BzKHJlZmVyZW5jZSwgT2JqZWN0LmFzc2lnbih7fSwgaW5zdGFuY2UucHJvcHMsIHt9LCBwYXJ0aWFsUHJvcHMsIHtcbiAgICAgICAgaWdub3JlQXR0cmlidXRlczogdHJ1ZVxuICAgICAgfSkpO1xuICAgICAgaW5zdGFuY2UucHJvcHMgPSBuZXh0UHJvcHM7XG4gICAgICBhZGRMaXN0ZW5lcnMoKTtcbiAgICAgIGlmIChwcmV2UHJvcHMuaW50ZXJhY3RpdmVEZWJvdW5jZSAhPT0gbmV4dFByb3BzLmludGVyYWN0aXZlRGVib3VuY2UpIHtcbiAgICAgICAgY2xlYW51cEludGVyYWN0aXZlTW91c2VMaXN0ZW5lcnMoKTtcbiAgICAgICAgZGVib3VuY2VkT25Nb3VzZU1vdmUgPSBkZWJvdW5jZShvbk1vdXNlTW92ZSwgbmV4dFByb3BzLmludGVyYWN0aXZlRGVib3VuY2UpO1xuICAgICAgfVxuICAgICAgaWYgKHByZXZQcm9wcy50cmlnZ2VyVGFyZ2V0ICYmICFuZXh0UHJvcHMudHJpZ2dlclRhcmdldCkge1xuICAgICAgICBub3JtYWxpemVUb0FycmF5KHByZXZQcm9wcy50cmlnZ2VyVGFyZ2V0KS5mb3JFYWNoKGZ1bmN0aW9uKG5vZGUpIHtcbiAgICAgICAgICBub2RlLnJlbW92ZUF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIik7XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIGlmIChuZXh0UHJvcHMudHJpZ2dlclRhcmdldCkge1xuICAgICAgICByZWZlcmVuY2UucmVtb3ZlQXR0cmlidXRlKFwiYXJpYS1leHBhbmRlZFwiKTtcbiAgICAgIH1cbiAgICAgIGhhbmRsZUFyaWFFeHBhbmRlZEF0dHJpYnV0ZSgpO1xuICAgICAgaGFuZGxlU3R5bGVzKCk7XG4gICAgICBpZiAob25VcGRhdGUpIHtcbiAgICAgICAgb25VcGRhdGUocHJldlByb3BzLCBuZXh0UHJvcHMpO1xuICAgICAgfVxuICAgICAgaWYgKGluc3RhbmNlLnBvcHBlckluc3RhbmNlKSB7XG4gICAgICAgIGNyZWF0ZVBvcHBlckluc3RhbmNlKCk7XG4gICAgICAgIGdldE5lc3RlZFBvcHBlclRyZWUoKS5mb3JFYWNoKGZ1bmN0aW9uKG5lc3RlZFBvcHBlcikge1xuICAgICAgICAgIHJlcXVlc3RBbmltYXRpb25GcmFtZShuZXN0ZWRQb3BwZXIuX3RpcHB5LnBvcHBlckluc3RhbmNlLmZvcmNlVXBkYXRlKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBpbnZva2VIb29rKFwib25BZnRlclVwZGF0ZVwiLCBbaW5zdGFuY2UsIHBhcnRpYWxQcm9wc10pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBzZXRDb250ZW50Mihjb250ZW50KSB7XG4gICAgICBpbnN0YW5jZS5zZXRQcm9wcyh7XG4gICAgICAgIGNvbnRlbnRcbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBzaG93KCkge1xuICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgd2FybldoZW4oaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQsIGNyZWF0ZU1lbW9yeUxlYWtXYXJuaW5nKFwic2hvd1wiKSk7XG4gICAgICB9XG4gICAgICB2YXIgaXNBbHJlYWR5VmlzaWJsZSA9IGluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZTtcbiAgICAgIHZhciBpc0Rlc3Ryb3llZCA9IGluc3RhbmNlLnN0YXRlLmlzRGVzdHJveWVkO1xuICAgICAgdmFyIGlzRGlzYWJsZWQgPSAhaW5zdGFuY2Uuc3RhdGUuaXNFbmFibGVkO1xuICAgICAgdmFyIGlzVG91Y2hBbmRUb3VjaERpc2FibGVkID0gY3VycmVudElucHV0LmlzVG91Y2ggJiYgIWluc3RhbmNlLnByb3BzLnRvdWNoO1xuICAgICAgdmFyIGR1cmF0aW9uID0gZ2V0VmFsdWVBdEluZGV4T3JSZXR1cm4oaW5zdGFuY2UucHJvcHMuZHVyYXRpb24sIDAsIGRlZmF1bHRQcm9wcy5kdXJhdGlvbik7XG4gICAgICBpZiAoaXNBbHJlYWR5VmlzaWJsZSB8fCBpc0Rlc3Ryb3llZCB8fCBpc0Rpc2FibGVkIHx8IGlzVG91Y2hBbmRUb3VjaERpc2FibGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGlmIChnZXRDdXJyZW50VGFyZ2V0KCkuaGFzQXR0cmlidXRlKFwiZGlzYWJsZWRcIikpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaW52b2tlSG9vayhcIm9uU2hvd1wiLCBbaW5zdGFuY2VdLCBmYWxzZSk7XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMub25TaG93KGluc3RhbmNlKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlID0gdHJ1ZTtcbiAgICAgIGlmIChnZXRJc0RlZmF1bHRSZW5kZXJGbigpKSB7XG4gICAgICAgIHBvcHBlci5zdHlsZS52aXNpYmlsaXR5ID0gXCJ2aXNpYmxlXCI7XG4gICAgICB9XG4gICAgICBoYW5kbGVTdHlsZXMoKTtcbiAgICAgIGFkZERvY3VtZW50UHJlc3MoKTtcbiAgICAgIGlmICghaW5zdGFuY2Uuc3RhdGUuaXNNb3VudGVkKSB7XG4gICAgICAgIHBvcHBlci5zdHlsZS50cmFuc2l0aW9uID0gXCJub25lXCI7XG4gICAgICB9XG4gICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSkge1xuICAgICAgICB2YXIgX2dldERlZmF1bHRUZW1wbGF0ZUNoMiA9IGdldERlZmF1bHRUZW1wbGF0ZUNoaWxkcmVuKCksIGJveCA9IF9nZXREZWZhdWx0VGVtcGxhdGVDaDIuYm94LCBjb250ZW50ID0gX2dldERlZmF1bHRUZW1wbGF0ZUNoMi5jb250ZW50O1xuICAgICAgICBzZXRUcmFuc2l0aW9uRHVyYXRpb24oW2JveCwgY29udGVudF0sIDApO1xuICAgICAgfVxuICAgICAgb25GaXJzdFVwZGF0ZSA9IGZ1bmN0aW9uIG9uRmlyc3RVcGRhdGUyKCkge1xuICAgICAgICB2YXIgX2luc3RhbmNlJHBvcHBlckluc3RhMjtcbiAgICAgICAgaWYgKCFpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUgfHwgaWdub3JlT25GaXJzdFVwZGF0ZSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBpZ25vcmVPbkZpcnN0VXBkYXRlID0gdHJ1ZTtcbiAgICAgICAgdm9pZCBwb3BwZXIub2Zmc2V0SGVpZ2h0O1xuICAgICAgICBwb3BwZXIuc3R5bGUudHJhbnNpdGlvbiA9IGluc3RhbmNlLnByb3BzLm1vdmVUcmFuc2l0aW9uO1xuICAgICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSAmJiBpbnN0YW5jZS5wcm9wcy5hbmltYXRpb24pIHtcbiAgICAgICAgICB2YXIgX2dldERlZmF1bHRUZW1wbGF0ZUNoMyA9IGdldERlZmF1bHRUZW1wbGF0ZUNoaWxkcmVuKCksIF9ib3ggPSBfZ2V0RGVmYXVsdFRlbXBsYXRlQ2gzLmJveCwgX2NvbnRlbnQgPSBfZ2V0RGVmYXVsdFRlbXBsYXRlQ2gzLmNvbnRlbnQ7XG4gICAgICAgICAgc2V0VHJhbnNpdGlvbkR1cmF0aW9uKFtfYm94LCBfY29udGVudF0sIGR1cmF0aW9uKTtcbiAgICAgICAgICBzZXRWaXNpYmlsaXR5U3RhdGUoW19ib3gsIF9jb250ZW50XSwgXCJ2aXNpYmxlXCIpO1xuICAgICAgICB9XG4gICAgICAgIGhhbmRsZUFyaWFDb250ZW50QXR0cmlidXRlKCk7XG4gICAgICAgIGhhbmRsZUFyaWFFeHBhbmRlZEF0dHJpYnV0ZSgpO1xuICAgICAgICBwdXNoSWZVbmlxdWUobW91bnRlZEluc3RhbmNlcywgaW5zdGFuY2UpO1xuICAgICAgICAoX2luc3RhbmNlJHBvcHBlckluc3RhMiA9IGluc3RhbmNlLnBvcHBlckluc3RhbmNlKSA9PSBudWxsID8gdm9pZCAwIDogX2luc3RhbmNlJHBvcHBlckluc3RhMi5mb3JjZVVwZGF0ZSgpO1xuICAgICAgICBpbnN0YW5jZS5zdGF0ZS5pc01vdW50ZWQgPSB0cnVlO1xuICAgICAgICBpbnZva2VIb29rKFwib25Nb3VudFwiLCBbaW5zdGFuY2VdKTtcbiAgICAgICAgaWYgKGluc3RhbmNlLnByb3BzLmFuaW1hdGlvbiAmJiBnZXRJc0RlZmF1bHRSZW5kZXJGbigpKSB7XG4gICAgICAgICAgb25UcmFuc2l0aW9uZWRJbihkdXJhdGlvbiwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBpbnN0YW5jZS5zdGF0ZS5pc1Nob3duID0gdHJ1ZTtcbiAgICAgICAgICAgIGludm9rZUhvb2soXCJvblNob3duXCIsIFtpbnN0YW5jZV0pO1xuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9O1xuICAgICAgbW91bnQoKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gaGlkZSgpIHtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIHdhcm5XaGVuKGluc3RhbmNlLnN0YXRlLmlzRGVzdHJveWVkLCBjcmVhdGVNZW1vcnlMZWFrV2FybmluZyhcImhpZGVcIikpO1xuICAgICAgfVxuICAgICAgdmFyIGlzQWxyZWFkeUhpZGRlbiA9ICFpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGU7XG4gICAgICB2YXIgaXNEZXN0cm95ZWQgPSBpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZDtcbiAgICAgIHZhciBpc0Rpc2FibGVkID0gIWluc3RhbmNlLnN0YXRlLmlzRW5hYmxlZDtcbiAgICAgIHZhciBkdXJhdGlvbiA9IGdldFZhbHVlQXRJbmRleE9yUmV0dXJuKGluc3RhbmNlLnByb3BzLmR1cmF0aW9uLCAxLCBkZWZhdWx0UHJvcHMuZHVyYXRpb24pO1xuICAgICAgaWYgKGlzQWxyZWFkeUhpZGRlbiB8fCBpc0Rlc3Ryb3llZCB8fCBpc0Rpc2FibGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGludm9rZUhvb2soXCJvbkhpZGVcIiwgW2luc3RhbmNlXSwgZmFsc2UpO1xuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLm9uSGlkZShpbnN0YW5jZSkgPT09IGZhbHNlKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSA9IGZhbHNlO1xuICAgICAgaW5zdGFuY2Uuc3RhdGUuaXNTaG93biA9IGZhbHNlO1xuICAgICAgaWdub3JlT25GaXJzdFVwZGF0ZSA9IGZhbHNlO1xuICAgICAgaXNWaXNpYmxlRnJvbUNsaWNrID0gZmFsc2U7XG4gICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSkge1xuICAgICAgICBwb3BwZXIuc3R5bGUudmlzaWJpbGl0eSA9IFwiaGlkZGVuXCI7XG4gICAgICB9XG4gICAgICBjbGVhbnVwSW50ZXJhY3RpdmVNb3VzZUxpc3RlbmVycygpO1xuICAgICAgcmVtb3ZlRG9jdW1lbnRQcmVzcygpO1xuICAgICAgaGFuZGxlU3R5bGVzKCk7XG4gICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSkge1xuICAgICAgICB2YXIgX2dldERlZmF1bHRUZW1wbGF0ZUNoNCA9IGdldERlZmF1bHRUZW1wbGF0ZUNoaWxkcmVuKCksIGJveCA9IF9nZXREZWZhdWx0VGVtcGxhdGVDaDQuYm94LCBjb250ZW50ID0gX2dldERlZmF1bHRUZW1wbGF0ZUNoNC5jb250ZW50O1xuICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuYW5pbWF0aW9uKSB7XG4gICAgICAgICAgc2V0VHJhbnNpdGlvbkR1cmF0aW9uKFtib3gsIGNvbnRlbnRdLCBkdXJhdGlvbik7XG4gICAgICAgICAgc2V0VmlzaWJpbGl0eVN0YXRlKFtib3gsIGNvbnRlbnRdLCBcImhpZGRlblwiKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgaGFuZGxlQXJpYUNvbnRlbnRBdHRyaWJ1dGUoKTtcbiAgICAgIGhhbmRsZUFyaWFFeHBhbmRlZEF0dHJpYnV0ZSgpO1xuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLmFuaW1hdGlvbikge1xuICAgICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSkge1xuICAgICAgICAgIG9uVHJhbnNpdGlvbmVkT3V0KGR1cmF0aW9uLCBpbnN0YW5jZS51bm1vdW50KTtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgaW5zdGFuY2UudW5tb3VudCgpO1xuICAgICAgfVxuICAgIH1cbiAgICBmdW5jdGlvbiBoaWRlV2l0aEludGVyYWN0aXZpdHkoZXZlbnQpIHtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIHdhcm5XaGVuKGluc3RhbmNlLnN0YXRlLmlzRGVzdHJveWVkLCBjcmVhdGVNZW1vcnlMZWFrV2FybmluZyhcImhpZGVXaXRoSW50ZXJhY3Rpdml0eVwiKSk7XG4gICAgICB9XG4gICAgICBnZXREb2N1bWVudCgpLmFkZEV2ZW50TGlzdGVuZXIoXCJtb3VzZW1vdmVcIiwgZGVib3VuY2VkT25Nb3VzZU1vdmUpO1xuICAgICAgcHVzaElmVW5pcXVlKG1vdXNlTW92ZUxpc3RlbmVycywgZGVib3VuY2VkT25Nb3VzZU1vdmUpO1xuICAgICAgZGVib3VuY2VkT25Nb3VzZU1vdmUoZXZlbnQpO1xuICAgIH1cbiAgICBmdW5jdGlvbiB1bm1vdW50KCkge1xuICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgd2FybldoZW4oaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQsIGNyZWF0ZU1lbW9yeUxlYWtXYXJuaW5nKFwidW5tb3VudFwiKSk7XG4gICAgICB9XG4gICAgICBpZiAoaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlKSB7XG4gICAgICAgIGluc3RhbmNlLmhpZGUoKTtcbiAgICAgIH1cbiAgICAgIGlmICghaW5zdGFuY2Uuc3RhdGUuaXNNb3VudGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGRlc3Ryb3lQb3BwZXJJbnN0YW5jZSgpO1xuICAgICAgZ2V0TmVzdGVkUG9wcGVyVHJlZSgpLmZvckVhY2goZnVuY3Rpb24obmVzdGVkUG9wcGVyKSB7XG4gICAgICAgIG5lc3RlZFBvcHBlci5fdGlwcHkudW5tb3VudCgpO1xuICAgICAgfSk7XG4gICAgICBpZiAocG9wcGVyLnBhcmVudE5vZGUpIHtcbiAgICAgICAgcG9wcGVyLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQocG9wcGVyKTtcbiAgICAgIH1cbiAgICAgIG1vdW50ZWRJbnN0YW5jZXMgPSBtb3VudGVkSW5zdGFuY2VzLmZpbHRlcihmdW5jdGlvbihpKSB7XG4gICAgICAgIHJldHVybiBpICE9PSBpbnN0YW5jZTtcbiAgICAgIH0pO1xuICAgICAgaW5zdGFuY2Uuc3RhdGUuaXNNb3VudGVkID0gZmFsc2U7XG4gICAgICBpbnZva2VIb29rKFwib25IaWRkZW5cIiwgW2luc3RhbmNlXSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGRlc3Ryb3koKSB7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICB3YXJuV2hlbihpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZCwgY3JlYXRlTWVtb3J5TGVha1dhcm5pbmcoXCJkZXN0cm95XCIpKTtcbiAgICAgIH1cbiAgICAgIGlmIChpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpbnN0YW5jZS5jbGVhckRlbGF5VGltZW91dHMoKTtcbiAgICAgIGluc3RhbmNlLnVubW91bnQoKTtcbiAgICAgIHJlbW92ZUxpc3RlbmVycygpO1xuICAgICAgZGVsZXRlIHJlZmVyZW5jZS5fdGlwcHk7XG4gICAgICBpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZCA9IHRydWU7XG4gICAgICBpbnZva2VIb29rKFwib25EZXN0cm95XCIsIFtpbnN0YW5jZV0pO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiB0aXBweTIodGFyZ2V0cywgb3B0aW9uYWxQcm9wcykge1xuICAgIGlmIChvcHRpb25hbFByb3BzID09PSB2b2lkIDApIHtcbiAgICAgIG9wdGlvbmFsUHJvcHMgPSB7fTtcbiAgICB9XG4gICAgdmFyIHBsdWdpbnMgPSBkZWZhdWx0UHJvcHMucGx1Z2lucy5jb25jYXQob3B0aW9uYWxQcm9wcy5wbHVnaW5zIHx8IFtdKTtcbiAgICBpZiAodHJ1ZSkge1xuICAgICAgdmFsaWRhdGVUYXJnZXRzKHRhcmdldHMpO1xuICAgICAgdmFsaWRhdGVQcm9wcyhvcHRpb25hbFByb3BzLCBwbHVnaW5zKTtcbiAgICB9XG4gICAgYmluZEdsb2JhbEV2ZW50TGlzdGVuZXJzKCk7XG4gICAgdmFyIHBhc3NlZFByb3BzID0gT2JqZWN0LmFzc2lnbih7fSwgb3B0aW9uYWxQcm9wcywge1xuICAgICAgcGx1Z2luc1xuICAgIH0pO1xuICAgIHZhciBlbGVtZW50cyA9IGdldEFycmF5T2ZFbGVtZW50cyh0YXJnZXRzKTtcbiAgICBpZiAodHJ1ZSkge1xuICAgICAgdmFyIGlzU2luZ2xlQ29udGVudEVsZW1lbnQgPSBpc0VsZW1lbnQocGFzc2VkUHJvcHMuY29udGVudCk7XG4gICAgICB2YXIgaXNNb3JlVGhhbk9uZVJlZmVyZW5jZUVsZW1lbnQgPSBlbGVtZW50cy5sZW5ndGggPiAxO1xuICAgICAgd2FybldoZW4oaXNTaW5nbGVDb250ZW50RWxlbWVudCAmJiBpc01vcmVUaGFuT25lUmVmZXJlbmNlRWxlbWVudCwgW1widGlwcHkoKSB3YXMgcGFzc2VkIGFuIEVsZW1lbnQgYXMgdGhlIGBjb250ZW50YCBwcm9wLCBidXQgbW9yZSB0aGFuXCIsIFwib25lIHRpcHB5IGluc3RhbmNlIHdhcyBjcmVhdGVkIGJ5IHRoaXMgaW52b2NhdGlvbi4gVGhpcyBtZWFucyB0aGVcIiwgXCJjb250ZW50IGVsZW1lbnQgd2lsbCBvbmx5IGJlIGFwcGVuZGVkIHRvIHRoZSBsYXN0IHRpcHB5IGluc3RhbmNlLlwiLCBcIlxcblxcblwiLCBcIkluc3RlYWQsIHBhc3MgdGhlIC5pbm5lckhUTUwgb2YgdGhlIGVsZW1lbnQsIG9yIHVzZSBhIGZ1bmN0aW9uIHRoYXRcIiwgXCJyZXR1cm5zIGEgY2xvbmVkIHZlcnNpb24gb2YgdGhlIGVsZW1lbnQgaW5zdGVhZC5cIiwgXCJcXG5cXG5cIiwgXCIxKSBjb250ZW50OiBlbGVtZW50LmlubmVySFRNTFxcblwiLCBcIjIpIGNvbnRlbnQ6ICgpID0+IGVsZW1lbnQuY2xvbmVOb2RlKHRydWUpXCJdLmpvaW4oXCIgXCIpKTtcbiAgICB9XG4gICAgdmFyIGluc3RhbmNlcyA9IGVsZW1lbnRzLnJlZHVjZShmdW5jdGlvbihhY2MsIHJlZmVyZW5jZSkge1xuICAgICAgdmFyIGluc3RhbmNlID0gcmVmZXJlbmNlICYmIGNyZWF0ZVRpcHB5KHJlZmVyZW5jZSwgcGFzc2VkUHJvcHMpO1xuICAgICAgaWYgKGluc3RhbmNlKSB7XG4gICAgICAgIGFjYy5wdXNoKGluc3RhbmNlKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBhY2M7XG4gICAgfSwgW10pO1xuICAgIHJldHVybiBpc0VsZW1lbnQodGFyZ2V0cykgPyBpbnN0YW5jZXNbMF0gOiBpbnN0YW5jZXM7XG4gIH1cbiAgdGlwcHkyLmRlZmF1bHRQcm9wcyA9IGRlZmF1bHRQcm9wcztcbiAgdGlwcHkyLnNldERlZmF1bHRQcm9wcyA9IHNldERlZmF1bHRQcm9wcztcbiAgdGlwcHkyLmN1cnJlbnRJbnB1dCA9IGN1cnJlbnRJbnB1dDtcbiAgdmFyIGhpZGVBbGwgPSBmdW5jdGlvbiBoaWRlQWxsMihfdGVtcCkge1xuICAgIHZhciBfcmVmID0gX3RlbXAgPT09IHZvaWQgMCA/IHt9IDogX3RlbXAsIGV4Y2x1ZGVkUmVmZXJlbmNlT3JJbnN0YW5jZSA9IF9yZWYuZXhjbHVkZSwgZHVyYXRpb24gPSBfcmVmLmR1cmF0aW9uO1xuICAgIG1vdW50ZWRJbnN0YW5jZXMuZm9yRWFjaChmdW5jdGlvbihpbnN0YW5jZSkge1xuICAgICAgdmFyIGlzRXhjbHVkZWQgPSBmYWxzZTtcbiAgICAgIGlmIChleGNsdWRlZFJlZmVyZW5jZU9ySW5zdGFuY2UpIHtcbiAgICAgICAgaXNFeGNsdWRlZCA9IGlzUmVmZXJlbmNlRWxlbWVudChleGNsdWRlZFJlZmVyZW5jZU9ySW5zdGFuY2UpID8gaW5zdGFuY2UucmVmZXJlbmNlID09PSBleGNsdWRlZFJlZmVyZW5jZU9ySW5zdGFuY2UgOiBpbnN0YW5jZS5wb3BwZXIgPT09IGV4Y2x1ZGVkUmVmZXJlbmNlT3JJbnN0YW5jZS5wb3BwZXI7XG4gICAgICB9XG4gICAgICBpZiAoIWlzRXhjbHVkZWQpIHtcbiAgICAgICAgdmFyIG9yaWdpbmFsRHVyYXRpb24gPSBpbnN0YW5jZS5wcm9wcy5kdXJhdGlvbjtcbiAgICAgICAgaW5zdGFuY2Uuc2V0UHJvcHMoe1xuICAgICAgICAgIGR1cmF0aW9uXG4gICAgICAgIH0pO1xuICAgICAgICBpbnN0YW5jZS5oaWRlKCk7XG4gICAgICAgIGlmICghaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQpIHtcbiAgICAgICAgICBpbnN0YW5jZS5zZXRQcm9wcyh7XG4gICAgICAgICAgICBkdXJhdGlvbjogb3JpZ2luYWxEdXJhdGlvblxuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSk7XG4gIH07XG4gIHZhciBhcHBseVN0eWxlc01vZGlmaWVyID0gT2JqZWN0LmFzc2lnbih7fSwgY29yZS5hcHBseVN0eWxlcywge1xuICAgIGVmZmVjdDogZnVuY3Rpb24gZWZmZWN0KF9yZWYpIHtcbiAgICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGU7XG4gICAgICB2YXIgaW5pdGlhbFN0eWxlcyA9IHtcbiAgICAgICAgcG9wcGVyOiB7XG4gICAgICAgICAgcG9zaXRpb246IHN0YXRlLm9wdGlvbnMuc3RyYXRlZ3ksXG4gICAgICAgICAgbGVmdDogXCIwXCIsXG4gICAgICAgICAgdG9wOiBcIjBcIixcbiAgICAgICAgICBtYXJnaW46IFwiMFwiXG4gICAgICAgIH0sXG4gICAgICAgIGFycm93OiB7XG4gICAgICAgICAgcG9zaXRpb246IFwiYWJzb2x1dGVcIlxuICAgICAgICB9LFxuICAgICAgICByZWZlcmVuY2U6IHt9XG4gICAgICB9O1xuICAgICAgT2JqZWN0LmFzc2lnbihzdGF0ZS5lbGVtZW50cy5wb3BwZXIuc3R5bGUsIGluaXRpYWxTdHlsZXMucG9wcGVyKTtcbiAgICAgIHN0YXRlLnN0eWxlcyA9IGluaXRpYWxTdHlsZXM7XG4gICAgICBpZiAoc3RhdGUuZWxlbWVudHMuYXJyb3cpIHtcbiAgICAgICAgT2JqZWN0LmFzc2lnbihzdGF0ZS5lbGVtZW50cy5hcnJvdy5zdHlsZSwgaW5pdGlhbFN0eWxlcy5hcnJvdyk7XG4gICAgICB9XG4gICAgfVxuICB9KTtcbiAgdmFyIGNyZWF0ZVNpbmdsZXRvbiA9IGZ1bmN0aW9uIGNyZWF0ZVNpbmdsZXRvbjIodGlwcHlJbnN0YW5jZXMsIG9wdGlvbmFsUHJvcHMpIHtcbiAgICB2YXIgX29wdGlvbmFsUHJvcHMkcG9wcGVyO1xuICAgIGlmIChvcHRpb25hbFByb3BzID09PSB2b2lkIDApIHtcbiAgICAgIG9wdGlvbmFsUHJvcHMgPSB7fTtcbiAgICB9XG4gICAgaWYgKHRydWUpIHtcbiAgICAgIGVycm9yV2hlbighQXJyYXkuaXNBcnJheSh0aXBweUluc3RhbmNlcyksIFtcIlRoZSBmaXJzdCBhcmd1bWVudCBwYXNzZWQgdG8gY3JlYXRlU2luZ2xldG9uKCkgbXVzdCBiZSBhbiBhcnJheSBvZlwiLCBcInRpcHB5IGluc3RhbmNlcy4gVGhlIHBhc3NlZCB2YWx1ZSB3YXNcIiwgU3RyaW5nKHRpcHB5SW5zdGFuY2VzKV0uam9pbihcIiBcIikpO1xuICAgIH1cbiAgICB2YXIgaW5kaXZpZHVhbEluc3RhbmNlcyA9IHRpcHB5SW5zdGFuY2VzO1xuICAgIHZhciByZWZlcmVuY2VzID0gW107XG4gICAgdmFyIGN1cnJlbnRUYXJnZXQ7XG4gICAgdmFyIG92ZXJyaWRlcyA9IG9wdGlvbmFsUHJvcHMub3ZlcnJpZGVzO1xuICAgIHZhciBpbnRlcmNlcHRTZXRQcm9wc0NsZWFudXBzID0gW107XG4gICAgdmFyIHNob3duT25DcmVhdGUgPSBmYWxzZTtcbiAgICBmdW5jdGlvbiBzZXRSZWZlcmVuY2VzKCkge1xuICAgICAgcmVmZXJlbmNlcyA9IGluZGl2aWR1YWxJbnN0YW5jZXMubWFwKGZ1bmN0aW9uKGluc3RhbmNlKSB7XG4gICAgICAgIHJldHVybiBpbnN0YW5jZS5yZWZlcmVuY2U7XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gZW5hYmxlSW5zdGFuY2VzKGlzRW5hYmxlZCkge1xuICAgICAgaW5kaXZpZHVhbEluc3RhbmNlcy5mb3JFYWNoKGZ1bmN0aW9uKGluc3RhbmNlKSB7XG4gICAgICAgIGlmIChpc0VuYWJsZWQpIHtcbiAgICAgICAgICBpbnN0YW5jZS5lbmFibGUoKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBpbnN0YW5jZS5kaXNhYmxlKCk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBpbnRlcmNlcHRTZXRQcm9wcyhzaW5nbGV0b24yKSB7XG4gICAgICByZXR1cm4gaW5kaXZpZHVhbEluc3RhbmNlcy5tYXAoZnVuY3Rpb24oaW5zdGFuY2UpIHtcbiAgICAgICAgdmFyIG9yaWdpbmFsU2V0UHJvcHMyID0gaW5zdGFuY2Uuc2V0UHJvcHM7XG4gICAgICAgIGluc3RhbmNlLnNldFByb3BzID0gZnVuY3Rpb24ocHJvcHMpIHtcbiAgICAgICAgICBvcmlnaW5hbFNldFByb3BzMihwcm9wcyk7XG4gICAgICAgICAgaWYgKGluc3RhbmNlLnJlZmVyZW5jZSA9PT0gY3VycmVudFRhcmdldCkge1xuICAgICAgICAgICAgc2luZ2xldG9uMi5zZXRQcm9wcyhwcm9wcyk7XG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgICByZXR1cm4gZnVuY3Rpb24oKSB7XG4gICAgICAgICAgaW5zdGFuY2Uuc2V0UHJvcHMgPSBvcmlnaW5hbFNldFByb3BzMjtcbiAgICAgICAgfTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBwcmVwYXJlSW5zdGFuY2Uoc2luZ2xldG9uMiwgdGFyZ2V0KSB7XG4gICAgICB2YXIgaW5kZXggPSByZWZlcmVuY2VzLmluZGV4T2YodGFyZ2V0KTtcbiAgICAgIGlmICh0YXJnZXQgPT09IGN1cnJlbnRUYXJnZXQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgY3VycmVudFRhcmdldCA9IHRhcmdldDtcbiAgICAgIHZhciBvdmVycmlkZVByb3BzID0gKG92ZXJyaWRlcyB8fCBbXSkuY29uY2F0KFwiY29udGVudFwiKS5yZWR1Y2UoZnVuY3Rpb24oYWNjLCBwcm9wKSB7XG4gICAgICAgIGFjY1twcm9wXSA9IGluZGl2aWR1YWxJbnN0YW5jZXNbaW5kZXhdLnByb3BzW3Byb3BdO1xuICAgICAgICByZXR1cm4gYWNjO1xuICAgICAgfSwge30pO1xuICAgICAgc2luZ2xldG9uMi5zZXRQcm9wcyhPYmplY3QuYXNzaWduKHt9LCBvdmVycmlkZVByb3BzLCB7XG4gICAgICAgIGdldFJlZmVyZW5jZUNsaWVudFJlY3Q6IHR5cGVvZiBvdmVycmlkZVByb3BzLmdldFJlZmVyZW5jZUNsaWVudFJlY3QgPT09IFwiZnVuY3Rpb25cIiA/IG92ZXJyaWRlUHJvcHMuZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdCA6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgIHJldHVybiB0YXJnZXQuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7XG4gICAgICAgIH1cbiAgICAgIH0pKTtcbiAgICB9XG4gICAgZW5hYmxlSW5zdGFuY2VzKGZhbHNlKTtcbiAgICBzZXRSZWZlcmVuY2VzKCk7XG4gICAgdmFyIHBsdWdpbiA9IHtcbiAgICAgIGZuOiBmdW5jdGlvbiBmbigpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICBvbkRlc3Ryb3k6IGZ1bmN0aW9uIG9uRGVzdHJveSgpIHtcbiAgICAgICAgICAgIGVuYWJsZUluc3RhbmNlcyh0cnVlKTtcbiAgICAgICAgICB9LFxuICAgICAgICAgIG9uSGlkZGVuOiBmdW5jdGlvbiBvbkhpZGRlbigpIHtcbiAgICAgICAgICAgIGN1cnJlbnRUYXJnZXQgPSBudWxsO1xuICAgICAgICAgIH0sXG4gICAgICAgICAgb25DbGlja091dHNpZGU6IGZ1bmN0aW9uIG9uQ2xpY2tPdXRzaWRlKGluc3RhbmNlKSB7XG4gICAgICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuc2hvd09uQ3JlYXRlICYmICFzaG93bk9uQ3JlYXRlKSB7XG4gICAgICAgICAgICAgIHNob3duT25DcmVhdGUgPSB0cnVlO1xuICAgICAgICAgICAgICBjdXJyZW50VGFyZ2V0ID0gbnVsbDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9LFxuICAgICAgICAgIG9uU2hvdzogZnVuY3Rpb24gb25TaG93KGluc3RhbmNlKSB7XG4gICAgICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuc2hvd09uQ3JlYXRlICYmICFzaG93bk9uQ3JlYXRlKSB7XG4gICAgICAgICAgICAgIHNob3duT25DcmVhdGUgPSB0cnVlO1xuICAgICAgICAgICAgICBwcmVwYXJlSW5zdGFuY2UoaW5zdGFuY2UsIHJlZmVyZW5jZXNbMF0pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0sXG4gICAgICAgICAgb25UcmlnZ2VyOiBmdW5jdGlvbiBvblRyaWdnZXIoaW5zdGFuY2UsIGV2ZW50KSB7XG4gICAgICAgICAgICBwcmVwYXJlSW5zdGFuY2UoaW5zdGFuY2UsIGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgICAgIH1cbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICB9O1xuICAgIHZhciBzaW5nbGV0b24gPSB0aXBweTIoZGl2KCksIE9iamVjdC5hc3NpZ24oe30sIHJlbW92ZVByb3BlcnRpZXMob3B0aW9uYWxQcm9wcywgW1wib3ZlcnJpZGVzXCJdKSwge1xuICAgICAgcGx1Z2luczogW3BsdWdpbl0uY29uY2F0KG9wdGlvbmFsUHJvcHMucGx1Z2lucyB8fCBbXSksXG4gICAgICB0cmlnZ2VyVGFyZ2V0OiByZWZlcmVuY2VzLFxuICAgICAgcG9wcGVyT3B0aW9uczogT2JqZWN0LmFzc2lnbih7fSwgb3B0aW9uYWxQcm9wcy5wb3BwZXJPcHRpb25zLCB7XG4gICAgICAgIG1vZGlmaWVyczogW10uY29uY2F0KCgoX29wdGlvbmFsUHJvcHMkcG9wcGVyID0gb3B0aW9uYWxQcm9wcy5wb3BwZXJPcHRpb25zKSA9PSBudWxsID8gdm9pZCAwIDogX29wdGlvbmFsUHJvcHMkcG9wcGVyLm1vZGlmaWVycykgfHwgW10sIFthcHBseVN0eWxlc01vZGlmaWVyXSlcbiAgICAgIH0pXG4gICAgfSkpO1xuICAgIHZhciBvcmlnaW5hbFNob3cgPSBzaW5nbGV0b24uc2hvdztcbiAgICBzaW5nbGV0b24uc2hvdyA9IGZ1bmN0aW9uKHRhcmdldCkge1xuICAgICAgb3JpZ2luYWxTaG93KCk7XG4gICAgICBpZiAoIWN1cnJlbnRUYXJnZXQgJiYgdGFyZ2V0ID09IG51bGwpIHtcbiAgICAgICAgcmV0dXJuIHByZXBhcmVJbnN0YW5jZShzaW5nbGV0b24sIHJlZmVyZW5jZXNbMF0pO1xuICAgICAgfVxuICAgICAgaWYgKGN1cnJlbnRUYXJnZXQgJiYgdGFyZ2V0ID09IG51bGwpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaWYgKHR5cGVvZiB0YXJnZXQgPT09IFwibnVtYmVyXCIpIHtcbiAgICAgICAgcmV0dXJuIHJlZmVyZW5jZXNbdGFyZ2V0XSAmJiBwcmVwYXJlSW5zdGFuY2Uoc2luZ2xldG9uLCByZWZlcmVuY2VzW3RhcmdldF0pO1xuICAgICAgfVxuICAgICAgaWYgKGluZGl2aWR1YWxJbnN0YW5jZXMuaW5jbHVkZXModGFyZ2V0KSkge1xuICAgICAgICB2YXIgcmVmID0gdGFyZ2V0LnJlZmVyZW5jZTtcbiAgICAgICAgcmV0dXJuIHByZXBhcmVJbnN0YW5jZShzaW5nbGV0b24sIHJlZik7XG4gICAgICB9XG4gICAgICBpZiAocmVmZXJlbmNlcy5pbmNsdWRlcyh0YXJnZXQpKSB7XG4gICAgICAgIHJldHVybiBwcmVwYXJlSW5zdGFuY2Uoc2luZ2xldG9uLCB0YXJnZXQpO1xuICAgICAgfVxuICAgIH07XG4gICAgc2luZ2xldG9uLnNob3dOZXh0ID0gZnVuY3Rpb24oKSB7XG4gICAgICB2YXIgZmlyc3QgPSByZWZlcmVuY2VzWzBdO1xuICAgICAgaWYgKCFjdXJyZW50VGFyZ2V0KSB7XG4gICAgICAgIHJldHVybiBzaW5nbGV0b24uc2hvdygwKTtcbiAgICAgIH1cbiAgICAgIHZhciBpbmRleCA9IHJlZmVyZW5jZXMuaW5kZXhPZihjdXJyZW50VGFyZ2V0KTtcbiAgICAgIHNpbmdsZXRvbi5zaG93KHJlZmVyZW5jZXNbaW5kZXggKyAxXSB8fCBmaXJzdCk7XG4gICAgfTtcbiAgICBzaW5nbGV0b24uc2hvd1ByZXZpb3VzID0gZnVuY3Rpb24oKSB7XG4gICAgICB2YXIgbGFzdCA9IHJlZmVyZW5jZXNbcmVmZXJlbmNlcy5sZW5ndGggLSAxXTtcbiAgICAgIGlmICghY3VycmVudFRhcmdldCkge1xuICAgICAgICByZXR1cm4gc2luZ2xldG9uLnNob3cobGFzdCk7XG4gICAgICB9XG4gICAgICB2YXIgaW5kZXggPSByZWZlcmVuY2VzLmluZGV4T2YoY3VycmVudFRhcmdldCk7XG4gICAgICB2YXIgdGFyZ2V0ID0gcmVmZXJlbmNlc1tpbmRleCAtIDFdIHx8IGxhc3Q7XG4gICAgICBzaW5nbGV0b24uc2hvdyh0YXJnZXQpO1xuICAgIH07XG4gICAgdmFyIG9yaWdpbmFsU2V0UHJvcHMgPSBzaW5nbGV0b24uc2V0UHJvcHM7XG4gICAgc2luZ2xldG9uLnNldFByb3BzID0gZnVuY3Rpb24ocHJvcHMpIHtcbiAgICAgIG92ZXJyaWRlcyA9IHByb3BzLm92ZXJyaWRlcyB8fCBvdmVycmlkZXM7XG4gICAgICBvcmlnaW5hbFNldFByb3BzKHByb3BzKTtcbiAgICB9O1xuICAgIHNpbmdsZXRvbi5zZXRJbnN0YW5jZXMgPSBmdW5jdGlvbihuZXh0SW5zdGFuY2VzKSB7XG4gICAgICBlbmFibGVJbnN0YW5jZXModHJ1ZSk7XG4gICAgICBpbnRlcmNlcHRTZXRQcm9wc0NsZWFudXBzLmZvckVhY2goZnVuY3Rpb24oZm4pIHtcbiAgICAgICAgcmV0dXJuIGZuKCk7XG4gICAgICB9KTtcbiAgICAgIGluZGl2aWR1YWxJbnN0YW5jZXMgPSBuZXh0SW5zdGFuY2VzO1xuICAgICAgZW5hYmxlSW5zdGFuY2VzKGZhbHNlKTtcbiAgICAgIHNldFJlZmVyZW5jZXMoKTtcbiAgICAgIGludGVyY2VwdFNldFByb3BzKHNpbmdsZXRvbik7XG4gICAgICBzaW5nbGV0b24uc2V0UHJvcHMoe1xuICAgICAgICB0cmlnZ2VyVGFyZ2V0OiByZWZlcmVuY2VzXG4gICAgICB9KTtcbiAgICB9O1xuICAgIGludGVyY2VwdFNldFByb3BzQ2xlYW51cHMgPSBpbnRlcmNlcHRTZXRQcm9wcyhzaW5nbGV0b24pO1xuICAgIHJldHVybiBzaW5nbGV0b247XG4gIH07XG4gIHZhciBCVUJCTElOR19FVkVOVFNfTUFQID0ge1xuICAgIG1vdXNlb3ZlcjogXCJtb3VzZWVudGVyXCIsXG4gICAgZm9jdXNpbjogXCJmb2N1c1wiLFxuICAgIGNsaWNrOiBcImNsaWNrXCJcbiAgfTtcbiAgZnVuY3Rpb24gZGVsZWdhdGUodGFyZ2V0cywgcHJvcHMpIHtcbiAgICBpZiAodHJ1ZSkge1xuICAgICAgZXJyb3JXaGVuKCEocHJvcHMgJiYgcHJvcHMudGFyZ2V0KSwgW1wiWW91IG11c3Qgc3BlY2l0eSBhIGB0YXJnZXRgIHByb3AgaW5kaWNhdGluZyBhIENTUyBzZWxlY3RvciBzdHJpbmcgbWF0Y2hpbmdcIiwgXCJ0aGUgdGFyZ2V0IGVsZW1lbnRzIHRoYXQgc2hvdWxkIHJlY2VpdmUgYSB0aXBweS5cIl0uam9pbihcIiBcIikpO1xuICAgIH1cbiAgICB2YXIgbGlzdGVuZXJzID0gW107XG4gICAgdmFyIGNoaWxkVGlwcHlJbnN0YW5jZXMgPSBbXTtcbiAgICB2YXIgZGlzYWJsZWQgPSBmYWxzZTtcbiAgICB2YXIgdGFyZ2V0ID0gcHJvcHMudGFyZ2V0O1xuICAgIHZhciBuYXRpdmVQcm9wcyA9IHJlbW92ZVByb3BlcnRpZXMocHJvcHMsIFtcInRhcmdldFwiXSk7XG4gICAgdmFyIHBhcmVudFByb3BzID0gT2JqZWN0LmFzc2lnbih7fSwgbmF0aXZlUHJvcHMsIHtcbiAgICAgIHRyaWdnZXI6IFwibWFudWFsXCIsXG4gICAgICB0b3VjaDogZmFsc2VcbiAgICB9KTtcbiAgICB2YXIgY2hpbGRQcm9wcyA9IE9iamVjdC5hc3NpZ24oe30sIG5hdGl2ZVByb3BzLCB7XG4gICAgICBzaG93T25DcmVhdGU6IHRydWVcbiAgICB9KTtcbiAgICB2YXIgcmV0dXJuVmFsdWUgPSB0aXBweTIodGFyZ2V0cywgcGFyZW50UHJvcHMpO1xuICAgIHZhciBub3JtYWxpemVkUmV0dXJuVmFsdWUgPSBub3JtYWxpemVUb0FycmF5KHJldHVyblZhbHVlKTtcbiAgICBmdW5jdGlvbiBvblRyaWdnZXIoZXZlbnQpIHtcbiAgICAgIGlmICghZXZlbnQudGFyZ2V0IHx8IGRpc2FibGVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHZhciB0YXJnZXROb2RlID0gZXZlbnQudGFyZ2V0LmNsb3Nlc3QodGFyZ2V0KTtcbiAgICAgIGlmICghdGFyZ2V0Tm9kZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgdHJpZ2dlciA9IHRhcmdldE5vZGUuZ2V0QXR0cmlidXRlKFwiZGF0YS10aXBweS10cmlnZ2VyXCIpIHx8IHByb3BzLnRyaWdnZXIgfHwgZGVmYXVsdFByb3BzLnRyaWdnZXI7XG4gICAgICBpZiAodGFyZ2V0Tm9kZS5fdGlwcHkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaWYgKGV2ZW50LnR5cGUgPT09IFwidG91Y2hzdGFydFwiICYmIHR5cGVvZiBjaGlsZFByb3BzLnRvdWNoID09PSBcImJvb2xlYW5cIikge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpZiAoZXZlbnQudHlwZSAhPT0gXCJ0b3VjaHN0YXJ0XCIgJiYgdHJpZ2dlci5pbmRleE9mKEJVQkJMSU5HX0VWRU5UU19NQVBbZXZlbnQudHlwZV0pIDwgMCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgaW5zdGFuY2UgPSB0aXBweTIodGFyZ2V0Tm9kZSwgY2hpbGRQcm9wcyk7XG4gICAgICBpZiAoaW5zdGFuY2UpIHtcbiAgICAgICAgY2hpbGRUaXBweUluc3RhbmNlcyA9IGNoaWxkVGlwcHlJbnN0YW5jZXMuY29uY2F0KGluc3RhbmNlKTtcbiAgICAgIH1cbiAgICB9XG4gICAgZnVuY3Rpb24gb24obm9kZSwgZXZlbnRUeXBlLCBoYW5kbGVyLCBvcHRpb25zKSB7XG4gICAgICBpZiAob3B0aW9ucyA9PT0gdm9pZCAwKSB7XG4gICAgICAgIG9wdGlvbnMgPSBmYWxzZTtcbiAgICAgIH1cbiAgICAgIG5vZGUuYWRkRXZlbnRMaXN0ZW5lcihldmVudFR5cGUsIGhhbmRsZXIsIG9wdGlvbnMpO1xuICAgICAgbGlzdGVuZXJzLnB1c2goe1xuICAgICAgICBub2RlLFxuICAgICAgICBldmVudFR5cGUsXG4gICAgICAgIGhhbmRsZXIsXG4gICAgICAgIG9wdGlvbnNcbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBhZGRFdmVudExpc3RlbmVycyhpbnN0YW5jZSkge1xuICAgICAgdmFyIHJlZmVyZW5jZSA9IGluc3RhbmNlLnJlZmVyZW5jZTtcbiAgICAgIG9uKHJlZmVyZW5jZSwgXCJ0b3VjaHN0YXJ0XCIsIG9uVHJpZ2dlciwgVE9VQ0hfT1BUSU9OUyk7XG4gICAgICBvbihyZWZlcmVuY2UsIFwibW91c2VvdmVyXCIsIG9uVHJpZ2dlcik7XG4gICAgICBvbihyZWZlcmVuY2UsIFwiZm9jdXNpblwiLCBvblRyaWdnZXIpO1xuICAgICAgb24ocmVmZXJlbmNlLCBcImNsaWNrXCIsIG9uVHJpZ2dlcik7XG4gICAgfVxuICAgIGZ1bmN0aW9uIHJlbW92ZUV2ZW50TGlzdGVuZXJzKCkge1xuICAgICAgbGlzdGVuZXJzLmZvckVhY2goZnVuY3Rpb24oX3JlZikge1xuICAgICAgICB2YXIgbm9kZSA9IF9yZWYubm9kZSwgZXZlbnRUeXBlID0gX3JlZi5ldmVudFR5cGUsIGhhbmRsZXIgPSBfcmVmLmhhbmRsZXIsIG9wdGlvbnMgPSBfcmVmLm9wdGlvbnM7XG4gICAgICAgIG5vZGUucmVtb3ZlRXZlbnRMaXN0ZW5lcihldmVudFR5cGUsIGhhbmRsZXIsIG9wdGlvbnMpO1xuICAgICAgfSk7XG4gICAgICBsaXN0ZW5lcnMgPSBbXTtcbiAgICB9XG4gICAgZnVuY3Rpb24gYXBwbHlNdXRhdGlvbnMoaW5zdGFuY2UpIHtcbiAgICAgIHZhciBvcmlnaW5hbERlc3Ryb3kgPSBpbnN0YW5jZS5kZXN0cm95O1xuICAgICAgdmFyIG9yaWdpbmFsRW5hYmxlID0gaW5zdGFuY2UuZW5hYmxlO1xuICAgICAgdmFyIG9yaWdpbmFsRGlzYWJsZSA9IGluc3RhbmNlLmRpc2FibGU7XG4gICAgICBpbnN0YW5jZS5kZXN0cm95ID0gZnVuY3Rpb24oc2hvdWxkRGVzdHJveUNoaWxkSW5zdGFuY2VzKSB7XG4gICAgICAgIGlmIChzaG91bGREZXN0cm95Q2hpbGRJbnN0YW5jZXMgPT09IHZvaWQgMCkge1xuICAgICAgICAgIHNob3VsZERlc3Ryb3lDaGlsZEluc3RhbmNlcyA9IHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKHNob3VsZERlc3Ryb3lDaGlsZEluc3RhbmNlcykge1xuICAgICAgICAgIGNoaWxkVGlwcHlJbnN0YW5jZXMuZm9yRWFjaChmdW5jdGlvbihpbnN0YW5jZTIpIHtcbiAgICAgICAgICAgIGluc3RhbmNlMi5kZXN0cm95KCk7XG4gICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgY2hpbGRUaXBweUluc3RhbmNlcyA9IFtdO1xuICAgICAgICByZW1vdmVFdmVudExpc3RlbmVycygpO1xuICAgICAgICBvcmlnaW5hbERlc3Ryb3koKTtcbiAgICAgIH07XG4gICAgICBpbnN0YW5jZS5lbmFibGUgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgb3JpZ2luYWxFbmFibGUoKTtcbiAgICAgICAgY2hpbGRUaXBweUluc3RhbmNlcy5mb3JFYWNoKGZ1bmN0aW9uKGluc3RhbmNlMikge1xuICAgICAgICAgIHJldHVybiBpbnN0YW5jZTIuZW5hYmxlKCk7XG4gICAgICAgIH0pO1xuICAgICAgICBkaXNhYmxlZCA9IGZhbHNlO1xuICAgICAgfTtcbiAgICAgIGluc3RhbmNlLmRpc2FibGUgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgb3JpZ2luYWxEaXNhYmxlKCk7XG4gICAgICAgIGNoaWxkVGlwcHlJbnN0YW5jZXMuZm9yRWFjaChmdW5jdGlvbihpbnN0YW5jZTIpIHtcbiAgICAgICAgICByZXR1cm4gaW5zdGFuY2UyLmRpc2FibGUoKTtcbiAgICAgICAgfSk7XG4gICAgICAgIGRpc2FibGVkID0gdHJ1ZTtcbiAgICAgIH07XG4gICAgICBhZGRFdmVudExpc3RlbmVycyhpbnN0YW5jZSk7XG4gICAgfVxuICAgIG5vcm1hbGl6ZWRSZXR1cm5WYWx1ZS5mb3JFYWNoKGFwcGx5TXV0YXRpb25zKTtcbiAgICByZXR1cm4gcmV0dXJuVmFsdWU7XG4gIH1cbiAgdmFyIGFuaW1hdGVGaWxsID0ge1xuICAgIG5hbWU6IFwiYW5pbWF0ZUZpbGxcIixcbiAgICBkZWZhdWx0VmFsdWU6IGZhbHNlLFxuICAgIGZuOiBmdW5jdGlvbiBmbihpbnN0YW5jZSkge1xuICAgICAgdmFyIF9pbnN0YW5jZSRwcm9wcyRyZW5kZTtcbiAgICAgIGlmICghKChfaW5zdGFuY2UkcHJvcHMkcmVuZGUgPSBpbnN0YW5jZS5wcm9wcy5yZW5kZXIpID09IG51bGwgPyB2b2lkIDAgOiBfaW5zdGFuY2UkcHJvcHMkcmVuZGUuJCR0aXBweSkpIHtcbiAgICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgICBlcnJvcldoZW4oaW5zdGFuY2UucHJvcHMuYW5pbWF0ZUZpbGwsIFwiVGhlIGBhbmltYXRlRmlsbGAgcGx1Z2luIHJlcXVpcmVzIHRoZSBkZWZhdWx0IHJlbmRlciBmdW5jdGlvbi5cIik7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHt9O1xuICAgICAgfVxuICAgICAgdmFyIF9nZXRDaGlsZHJlbiA9IGdldENoaWxkcmVuKGluc3RhbmNlLnBvcHBlciksIGJveCA9IF9nZXRDaGlsZHJlbi5ib3gsIGNvbnRlbnQgPSBfZ2V0Q2hpbGRyZW4uY29udGVudDtcbiAgICAgIHZhciBiYWNrZHJvcCA9IGluc3RhbmNlLnByb3BzLmFuaW1hdGVGaWxsID8gY3JlYXRlQmFja2Ryb3BFbGVtZW50KCkgOiBudWxsO1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgb25DcmVhdGU6IGZ1bmN0aW9uIG9uQ3JlYXRlKCkge1xuICAgICAgICAgIGlmIChiYWNrZHJvcCkge1xuICAgICAgICAgICAgYm94Lmluc2VydEJlZm9yZShiYWNrZHJvcCwgYm94LmZpcnN0RWxlbWVudENoaWxkKTtcbiAgICAgICAgICAgIGJveC5zZXRBdHRyaWJ1dGUoXCJkYXRhLWFuaW1hdGVmaWxsXCIsIFwiXCIpO1xuICAgICAgICAgICAgYm94LnN0eWxlLm92ZXJmbG93ID0gXCJoaWRkZW5cIjtcbiAgICAgICAgICAgIGluc3RhbmNlLnNldFByb3BzKHtcbiAgICAgICAgICAgICAgYXJyb3c6IGZhbHNlLFxuICAgICAgICAgICAgICBhbmltYXRpb246IFwic2hpZnQtYXdheVwiXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIG9uTW91bnQ6IGZ1bmN0aW9uIG9uTW91bnQoKSB7XG4gICAgICAgICAgaWYgKGJhY2tkcm9wKSB7XG4gICAgICAgICAgICB2YXIgdHJhbnNpdGlvbkR1cmF0aW9uID0gYm94LnN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbjtcbiAgICAgICAgICAgIHZhciBkdXJhdGlvbiA9IE51bWJlcih0cmFuc2l0aW9uRHVyYXRpb24ucmVwbGFjZShcIm1zXCIsIFwiXCIpKTtcbiAgICAgICAgICAgIGNvbnRlbnQuc3R5bGUudHJhbnNpdGlvbkRlbGF5ID0gTWF0aC5yb3VuZChkdXJhdGlvbiAvIDEwKSArIFwibXNcIjtcbiAgICAgICAgICAgIGJhY2tkcm9wLnN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbiA9IHRyYW5zaXRpb25EdXJhdGlvbjtcbiAgICAgICAgICAgIHNldFZpc2liaWxpdHlTdGF0ZShbYmFja2Ryb3BdLCBcInZpc2libGVcIik7XG4gICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBvblNob3c6IGZ1bmN0aW9uIG9uU2hvdygpIHtcbiAgICAgICAgICBpZiAoYmFja2Ryb3ApIHtcbiAgICAgICAgICAgIGJhY2tkcm9wLnN0eWxlLnRyYW5zaXRpb25EdXJhdGlvbiA9IFwiMG1zXCI7XG4gICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBvbkhpZGU6IGZ1bmN0aW9uIG9uSGlkZSgpIHtcbiAgICAgICAgICBpZiAoYmFja2Ryb3ApIHtcbiAgICAgICAgICAgIHNldFZpc2liaWxpdHlTdGF0ZShbYmFja2Ryb3BdLCBcImhpZGRlblwiKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH07XG4gICAgfVxuICB9O1xuICBmdW5jdGlvbiBjcmVhdGVCYWNrZHJvcEVsZW1lbnQoKSB7XG4gICAgdmFyIGJhY2tkcm9wID0gZGl2KCk7XG4gICAgYmFja2Ryb3AuY2xhc3NOYW1lID0gQkFDS0RST1BfQ0xBU1M7XG4gICAgc2V0VmlzaWJpbGl0eVN0YXRlKFtiYWNrZHJvcF0sIFwiaGlkZGVuXCIpO1xuICAgIHJldHVybiBiYWNrZHJvcDtcbiAgfVxuICB2YXIgbW91c2VDb29yZHMgPSB7XG4gICAgY2xpZW50WDogMCxcbiAgICBjbGllbnRZOiAwXG4gIH07XG4gIHZhciBhY3RpdmVJbnN0YW5jZXMgPSBbXTtcbiAgZnVuY3Rpb24gc3RvcmVNb3VzZUNvb3JkcyhfcmVmKSB7XG4gICAgdmFyIGNsaWVudFggPSBfcmVmLmNsaWVudFgsIGNsaWVudFkgPSBfcmVmLmNsaWVudFk7XG4gICAgbW91c2VDb29yZHMgPSB7XG4gICAgICBjbGllbnRYLFxuICAgICAgY2xpZW50WVxuICAgIH07XG4gIH1cbiAgZnVuY3Rpb24gYWRkTW91c2VDb29yZHNMaXN0ZW5lcihkb2MpIHtcbiAgICBkb2MuYWRkRXZlbnRMaXN0ZW5lcihcIm1vdXNlbW92ZVwiLCBzdG9yZU1vdXNlQ29vcmRzKTtcbiAgfVxuICBmdW5jdGlvbiByZW1vdmVNb3VzZUNvb3Jkc0xpc3RlbmVyKGRvYykge1xuICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwibW91c2Vtb3ZlXCIsIHN0b3JlTW91c2VDb29yZHMpO1xuICB9XG4gIHZhciBmb2xsb3dDdXJzb3IyID0ge1xuICAgIG5hbWU6IFwiZm9sbG93Q3Vyc29yXCIsXG4gICAgZGVmYXVsdFZhbHVlOiBmYWxzZSxcbiAgICBmbjogZnVuY3Rpb24gZm4oaW5zdGFuY2UpIHtcbiAgICAgIHZhciByZWZlcmVuY2UgPSBpbnN0YW5jZS5yZWZlcmVuY2U7XG4gICAgICB2YXIgZG9jID0gZ2V0T3duZXJEb2N1bWVudChpbnN0YW5jZS5wcm9wcy50cmlnZ2VyVGFyZ2V0IHx8IHJlZmVyZW5jZSk7XG4gICAgICB2YXIgaXNJbnRlcm5hbFVwZGF0ZSA9IGZhbHNlO1xuICAgICAgdmFyIHdhc0ZvY3VzRXZlbnQgPSBmYWxzZTtcbiAgICAgIHZhciBpc1VubW91bnRlZCA9IHRydWU7XG4gICAgICB2YXIgcHJldlByb3BzID0gaW5zdGFuY2UucHJvcHM7XG4gICAgICBmdW5jdGlvbiBnZXRJc0luaXRpYWxCZWhhdmlvcigpIHtcbiAgICAgICAgcmV0dXJuIGluc3RhbmNlLnByb3BzLmZvbGxvd0N1cnNvciA9PT0gXCJpbml0aWFsXCIgJiYgaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gYWRkTGlzdGVuZXIoKSB7XG4gICAgICAgIGRvYy5hZGRFdmVudExpc3RlbmVyKFwibW91c2Vtb3ZlXCIsIG9uTW91c2VNb3ZlKTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIHJlbW92ZUxpc3RlbmVyKCkge1xuICAgICAgICBkb2MucmVtb3ZlRXZlbnRMaXN0ZW5lcihcIm1vdXNlbW92ZVwiLCBvbk1vdXNlTW92ZSk7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiB1bnNldEdldFJlZmVyZW5jZUNsaWVudFJlY3QoKSB7XG4gICAgICAgIGlzSW50ZXJuYWxVcGRhdGUgPSB0cnVlO1xuICAgICAgICBpbnN0YW5jZS5zZXRQcm9wcyh7XG4gICAgICAgICAgZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdDogbnVsbFxuICAgICAgICB9KTtcbiAgICAgICAgaXNJbnRlcm5hbFVwZGF0ZSA9IGZhbHNlO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gb25Nb3VzZU1vdmUoZXZlbnQpIHtcbiAgICAgICAgdmFyIGlzQ3Vyc29yT3ZlclJlZmVyZW5jZSA9IGV2ZW50LnRhcmdldCA/IHJlZmVyZW5jZS5jb250YWlucyhldmVudC50YXJnZXQpIDogdHJ1ZTtcbiAgICAgICAgdmFyIGZvbGxvd0N1cnNvcjMgPSBpbnN0YW5jZS5wcm9wcy5mb2xsb3dDdXJzb3I7XG4gICAgICAgIHZhciBjbGllbnRYID0gZXZlbnQuY2xpZW50WCwgY2xpZW50WSA9IGV2ZW50LmNsaWVudFk7XG4gICAgICAgIHZhciByZWN0ID0gcmVmZXJlbmNlLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgICAgICB2YXIgcmVsYXRpdmVYID0gY2xpZW50WCAtIHJlY3QubGVmdDtcbiAgICAgICAgdmFyIHJlbGF0aXZlWSA9IGNsaWVudFkgLSByZWN0LnRvcDtcbiAgICAgICAgaWYgKGlzQ3Vyc29yT3ZlclJlZmVyZW5jZSB8fCAhaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUpIHtcbiAgICAgICAgICBpbnN0YW5jZS5zZXRQcm9wcyh7XG4gICAgICAgICAgICBnZXRSZWZlcmVuY2VDbGllbnRSZWN0OiBmdW5jdGlvbiBnZXRSZWZlcmVuY2VDbGllbnRSZWN0KCkge1xuICAgICAgICAgICAgICB2YXIgcmVjdDIgPSByZWZlcmVuY2UuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7XG4gICAgICAgICAgICAgIHZhciB4ID0gY2xpZW50WDtcbiAgICAgICAgICAgICAgdmFyIHkgPSBjbGllbnRZO1xuICAgICAgICAgICAgICBpZiAoZm9sbG93Q3Vyc29yMyA9PT0gXCJpbml0aWFsXCIpIHtcbiAgICAgICAgICAgICAgICB4ID0gcmVjdDIubGVmdCArIHJlbGF0aXZlWDtcbiAgICAgICAgICAgICAgICB5ID0gcmVjdDIudG9wICsgcmVsYXRpdmVZO1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgIHZhciB0b3AgPSBmb2xsb3dDdXJzb3IzID09PSBcImhvcml6b250YWxcIiA/IHJlY3QyLnRvcCA6IHk7XG4gICAgICAgICAgICAgIHZhciByaWdodCA9IGZvbGxvd0N1cnNvcjMgPT09IFwidmVydGljYWxcIiA/IHJlY3QyLnJpZ2h0IDogeDtcbiAgICAgICAgICAgICAgdmFyIGJvdHRvbSA9IGZvbGxvd0N1cnNvcjMgPT09IFwiaG9yaXpvbnRhbFwiID8gcmVjdDIuYm90dG9tIDogeTtcbiAgICAgICAgICAgICAgdmFyIGxlZnQgPSBmb2xsb3dDdXJzb3IzID09PSBcInZlcnRpY2FsXCIgPyByZWN0Mi5sZWZ0IDogeDtcbiAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICB3aWR0aDogcmlnaHQgLSBsZWZ0LFxuICAgICAgICAgICAgICAgIGhlaWdodDogYm90dG9tIC0gdG9wLFxuICAgICAgICAgICAgICAgIHRvcCxcbiAgICAgICAgICAgICAgICByaWdodCxcbiAgICAgICAgICAgICAgICBib3R0b20sXG4gICAgICAgICAgICAgICAgbGVmdFxuICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBjcmVhdGUoKSB7XG4gICAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5mb2xsb3dDdXJzb3IpIHtcbiAgICAgICAgICBhY3RpdmVJbnN0YW5jZXMucHVzaCh7XG4gICAgICAgICAgICBpbnN0YW5jZSxcbiAgICAgICAgICAgIGRvY1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIGFkZE1vdXNlQ29vcmRzTGlzdGVuZXIoZG9jKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgZnVuY3Rpb24gZGVzdHJveSgpIHtcbiAgICAgICAgYWN0aXZlSW5zdGFuY2VzID0gYWN0aXZlSW5zdGFuY2VzLmZpbHRlcihmdW5jdGlvbihkYXRhKSB7XG4gICAgICAgICAgcmV0dXJuIGRhdGEuaW5zdGFuY2UgIT09IGluc3RhbmNlO1xuICAgICAgICB9KTtcbiAgICAgICAgaWYgKGFjdGl2ZUluc3RhbmNlcy5maWx0ZXIoZnVuY3Rpb24oZGF0YSkge1xuICAgICAgICAgIHJldHVybiBkYXRhLmRvYyA9PT0gZG9jO1xuICAgICAgICB9KS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICByZW1vdmVNb3VzZUNvb3Jkc0xpc3RlbmVyKGRvYyk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIHJldHVybiB7XG4gICAgICAgIG9uQ3JlYXRlOiBjcmVhdGUsXG4gICAgICAgIG9uRGVzdHJveTogZGVzdHJveSxcbiAgICAgICAgb25CZWZvcmVVcGRhdGU6IGZ1bmN0aW9uIG9uQmVmb3JlVXBkYXRlKCkge1xuICAgICAgICAgIHByZXZQcm9wcyA9IGluc3RhbmNlLnByb3BzO1xuICAgICAgICB9LFxuICAgICAgICBvbkFmdGVyVXBkYXRlOiBmdW5jdGlvbiBvbkFmdGVyVXBkYXRlKF8sIF9yZWYyKSB7XG4gICAgICAgICAgdmFyIGZvbGxvd0N1cnNvcjMgPSBfcmVmMi5mb2xsb3dDdXJzb3I7XG4gICAgICAgICAgaWYgKGlzSW50ZXJuYWxVcGRhdGUpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKGZvbGxvd0N1cnNvcjMgIT09IHZvaWQgMCAmJiBwcmV2UHJvcHMuZm9sbG93Q3Vyc29yICE9PSBmb2xsb3dDdXJzb3IzKSB7XG4gICAgICAgICAgICBkZXN0cm95KCk7XG4gICAgICAgICAgICBpZiAoZm9sbG93Q3Vyc29yMykge1xuICAgICAgICAgICAgICBjcmVhdGUoKTtcbiAgICAgICAgICAgICAgaWYgKGluc3RhbmNlLnN0YXRlLmlzTW91bnRlZCAmJiAhd2FzRm9jdXNFdmVudCAmJiAhZ2V0SXNJbml0aWFsQmVoYXZpb3IoKSkge1xuICAgICAgICAgICAgICAgIGFkZExpc3RlbmVyKCk7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgIHJlbW92ZUxpc3RlbmVyKCk7XG4gICAgICAgICAgICAgIHVuc2V0R2V0UmVmZXJlbmNlQ2xpZW50UmVjdCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgb25Nb3VudDogZnVuY3Rpb24gb25Nb3VudCgpIHtcbiAgICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuZm9sbG93Q3Vyc29yICYmICF3YXNGb2N1c0V2ZW50KSB7XG4gICAgICAgICAgICBpZiAoaXNVbm1vdW50ZWQpIHtcbiAgICAgICAgICAgICAgb25Nb3VzZU1vdmUobW91c2VDb29yZHMpO1xuICAgICAgICAgICAgICBpc1VubW91bnRlZCA9IGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKCFnZXRJc0luaXRpYWxCZWhhdmlvcigpKSB7XG4gICAgICAgICAgICAgIGFkZExpc3RlbmVyKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBvblRyaWdnZXI6IGZ1bmN0aW9uIG9uVHJpZ2dlcihfLCBldmVudCkge1xuICAgICAgICAgIGlmIChpc01vdXNlRXZlbnQoZXZlbnQpKSB7XG4gICAgICAgICAgICBtb3VzZUNvb3JkcyA9IHtcbiAgICAgICAgICAgICAgY2xpZW50WDogZXZlbnQuY2xpZW50WCxcbiAgICAgICAgICAgICAgY2xpZW50WTogZXZlbnQuY2xpZW50WVxuICAgICAgICAgICAgfTtcbiAgICAgICAgICB9XG4gICAgICAgICAgd2FzRm9jdXNFdmVudCA9IGV2ZW50LnR5cGUgPT09IFwiZm9jdXNcIjtcbiAgICAgICAgfSxcbiAgICAgICAgb25IaWRkZW46IGZ1bmN0aW9uIG9uSGlkZGVuKCkge1xuICAgICAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5mb2xsb3dDdXJzb3IpIHtcbiAgICAgICAgICAgIHVuc2V0R2V0UmVmZXJlbmNlQ2xpZW50UmVjdCgpO1xuICAgICAgICAgICAgcmVtb3ZlTGlzdGVuZXIoKTtcbiAgICAgICAgICAgIGlzVW5tb3VudGVkID0gdHJ1ZTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH07XG4gICAgfVxuICB9O1xuICBmdW5jdGlvbiBnZXRQcm9wcyhwcm9wcywgbW9kaWZpZXIpIHtcbiAgICB2YXIgX3Byb3BzJHBvcHBlck9wdGlvbnM7XG4gICAgcmV0dXJuIHtcbiAgICAgIHBvcHBlck9wdGlvbnM6IE9iamVjdC5hc3NpZ24oe30sIHByb3BzLnBvcHBlck9wdGlvbnMsIHtcbiAgICAgICAgbW9kaWZpZXJzOiBbXS5jb25jYXQoKCgoX3Byb3BzJHBvcHBlck9wdGlvbnMgPSBwcm9wcy5wb3BwZXJPcHRpb25zKSA9PSBudWxsID8gdm9pZCAwIDogX3Byb3BzJHBvcHBlck9wdGlvbnMubW9kaWZpZXJzKSB8fCBbXSkuZmlsdGVyKGZ1bmN0aW9uKF9yZWYpIHtcbiAgICAgICAgICB2YXIgbmFtZSA9IF9yZWYubmFtZTtcbiAgICAgICAgICByZXR1cm4gbmFtZSAhPT0gbW9kaWZpZXIubmFtZTtcbiAgICAgICAgfSksIFttb2RpZmllcl0pXG4gICAgICB9KVxuICAgIH07XG4gIH1cbiAgdmFyIGlubGluZVBvc2l0aW9uaW5nID0ge1xuICAgIG5hbWU6IFwiaW5saW5lUG9zaXRpb25pbmdcIixcbiAgICBkZWZhdWx0VmFsdWU6IGZhbHNlLFxuICAgIGZuOiBmdW5jdGlvbiBmbihpbnN0YW5jZSkge1xuICAgICAgdmFyIHJlZmVyZW5jZSA9IGluc3RhbmNlLnJlZmVyZW5jZTtcbiAgICAgIGZ1bmN0aW9uIGlzRW5hYmxlZCgpIHtcbiAgICAgICAgcmV0dXJuICEhaW5zdGFuY2UucHJvcHMuaW5saW5lUG9zaXRpb25pbmc7XG4gICAgICB9XG4gICAgICB2YXIgcGxhY2VtZW50O1xuICAgICAgdmFyIGN1cnNvclJlY3RJbmRleCA9IC0xO1xuICAgICAgdmFyIGlzSW50ZXJuYWxVcGRhdGUgPSBmYWxzZTtcbiAgICAgIHZhciBtb2RpZmllciA9IHtcbiAgICAgICAgbmFtZTogXCJ0aXBweUlubGluZVBvc2l0aW9uaW5nXCIsXG4gICAgICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgICAgIHBoYXNlOiBcImFmdGVyV3JpdGVcIixcbiAgICAgICAgZm46IGZ1bmN0aW9uIGZuMihfcmVmMikge1xuICAgICAgICAgIHZhciBzdGF0ZSA9IF9yZWYyLnN0YXRlO1xuICAgICAgICAgIGlmIChpc0VuYWJsZWQoKSkge1xuICAgICAgICAgICAgaWYgKHBsYWNlbWVudCAhPT0gc3RhdGUucGxhY2VtZW50KSB7XG4gICAgICAgICAgICAgIGluc3RhbmNlLnNldFByb3BzKHtcbiAgICAgICAgICAgICAgICBnZXRSZWZlcmVuY2VDbGllbnRSZWN0OiBmdW5jdGlvbiBnZXRSZWZlcmVuY2VDbGllbnRSZWN0KCkge1xuICAgICAgICAgICAgICAgICAgcmV0dXJuIF9nZXRSZWZlcmVuY2VDbGllbnRSZWN0KHN0YXRlLnBsYWNlbWVudCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHBsYWNlbWVudCA9IHN0YXRlLnBsYWNlbWVudDtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH07XG4gICAgICBmdW5jdGlvbiBfZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdChwbGFjZW1lbnQyKSB7XG4gICAgICAgIHJldHVybiBnZXRJbmxpbmVCb3VuZGluZ0NsaWVudFJlY3QoZ2V0QmFzZVBsYWNlbWVudChwbGFjZW1lbnQyKSwgcmVmZXJlbmNlLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLCBhcnJheUZyb20ocmVmZXJlbmNlLmdldENsaWVudFJlY3RzKCkpLCBjdXJzb3JSZWN0SW5kZXgpO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gc2V0SW50ZXJuYWxQcm9wcyhwYXJ0aWFsUHJvcHMpIHtcbiAgICAgICAgaXNJbnRlcm5hbFVwZGF0ZSA9IHRydWU7XG4gICAgICAgIGluc3RhbmNlLnNldFByb3BzKHBhcnRpYWxQcm9wcyk7XG4gICAgICAgIGlzSW50ZXJuYWxVcGRhdGUgPSBmYWxzZTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIGFkZE1vZGlmaWVyKCkge1xuICAgICAgICBpZiAoIWlzSW50ZXJuYWxVcGRhdGUpIHtcbiAgICAgICAgICBzZXRJbnRlcm5hbFByb3BzKGdldFByb3BzKGluc3RhbmNlLnByb3BzLCBtb2RpZmllcikpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4ge1xuICAgICAgICBvbkNyZWF0ZTogYWRkTW9kaWZpZXIsXG4gICAgICAgIG9uQWZ0ZXJVcGRhdGU6IGFkZE1vZGlmaWVyLFxuICAgICAgICBvblRyaWdnZXI6IGZ1bmN0aW9uIG9uVHJpZ2dlcihfLCBldmVudCkge1xuICAgICAgICAgIGlmIChpc01vdXNlRXZlbnQoZXZlbnQpKSB7XG4gICAgICAgICAgICB2YXIgcmVjdHMgPSBhcnJheUZyb20oaW5zdGFuY2UucmVmZXJlbmNlLmdldENsaWVudFJlY3RzKCkpO1xuICAgICAgICAgICAgdmFyIGN1cnNvclJlY3QgPSByZWN0cy5maW5kKGZ1bmN0aW9uKHJlY3QpIHtcbiAgICAgICAgICAgICAgcmV0dXJuIHJlY3QubGVmdCAtIDIgPD0gZXZlbnQuY2xpZW50WCAmJiByZWN0LnJpZ2h0ICsgMiA+PSBldmVudC5jbGllbnRYICYmIHJlY3QudG9wIC0gMiA8PSBldmVudC5jbGllbnRZICYmIHJlY3QuYm90dG9tICsgMiA+PSBldmVudC5jbGllbnRZO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBjdXJzb3JSZWN0SW5kZXggPSByZWN0cy5pbmRleE9mKGN1cnNvclJlY3QpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgb25VbnRyaWdnZXI6IGZ1bmN0aW9uIG9uVW50cmlnZ2VyKCkge1xuICAgICAgICAgIGN1cnNvclJlY3RJbmRleCA9IC0xO1xuICAgICAgICB9XG4gICAgICB9O1xuICAgIH1cbiAgfTtcbiAgZnVuY3Rpb24gZ2V0SW5saW5lQm91bmRpbmdDbGllbnRSZWN0KGN1cnJlbnRCYXNlUGxhY2VtZW50LCBib3VuZGluZ1JlY3QsIGNsaWVudFJlY3RzLCBjdXJzb3JSZWN0SW5kZXgpIHtcbiAgICBpZiAoY2xpZW50UmVjdHMubGVuZ3RoIDwgMiB8fCBjdXJyZW50QmFzZVBsYWNlbWVudCA9PT0gbnVsbCkge1xuICAgICAgcmV0dXJuIGJvdW5kaW5nUmVjdDtcbiAgICB9XG4gICAgaWYgKGNsaWVudFJlY3RzLmxlbmd0aCA9PT0gMiAmJiBjdXJzb3JSZWN0SW5kZXggPj0gMCAmJiBjbGllbnRSZWN0c1swXS5sZWZ0ID4gY2xpZW50UmVjdHNbMV0ucmlnaHQpIHtcbiAgICAgIHJldHVybiBjbGllbnRSZWN0c1tjdXJzb3JSZWN0SW5kZXhdIHx8IGJvdW5kaW5nUmVjdDtcbiAgICB9XG4gICAgc3dpdGNoIChjdXJyZW50QmFzZVBsYWNlbWVudCkge1xuICAgICAgY2FzZSBcInRvcFwiOlxuICAgICAgY2FzZSBcImJvdHRvbVwiOiB7XG4gICAgICAgIHZhciBmaXJzdFJlY3QgPSBjbGllbnRSZWN0c1swXTtcbiAgICAgICAgdmFyIGxhc3RSZWN0ID0gY2xpZW50UmVjdHNbY2xpZW50UmVjdHMubGVuZ3RoIC0gMV07XG4gICAgICAgIHZhciBpc1RvcCA9IGN1cnJlbnRCYXNlUGxhY2VtZW50ID09PSBcInRvcFwiO1xuICAgICAgICB2YXIgdG9wID0gZmlyc3RSZWN0LnRvcDtcbiAgICAgICAgdmFyIGJvdHRvbSA9IGxhc3RSZWN0LmJvdHRvbTtcbiAgICAgICAgdmFyIGxlZnQgPSBpc1RvcCA/IGZpcnN0UmVjdC5sZWZ0IDogbGFzdFJlY3QubGVmdDtcbiAgICAgICAgdmFyIHJpZ2h0ID0gaXNUb3AgPyBmaXJzdFJlY3QucmlnaHQgOiBsYXN0UmVjdC5yaWdodDtcbiAgICAgICAgdmFyIHdpZHRoID0gcmlnaHQgLSBsZWZ0O1xuICAgICAgICB2YXIgaGVpZ2h0ID0gYm90dG9tIC0gdG9wO1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIHRvcCxcbiAgICAgICAgICBib3R0b20sXG4gICAgICAgICAgbGVmdCxcbiAgICAgICAgICByaWdodCxcbiAgICAgICAgICB3aWR0aCxcbiAgICAgICAgICBoZWlnaHRcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIGNhc2UgXCJsZWZ0XCI6XG4gICAgICBjYXNlIFwicmlnaHRcIjoge1xuICAgICAgICB2YXIgbWluTGVmdCA9IE1hdGgubWluLmFwcGx5KE1hdGgsIGNsaWVudFJlY3RzLm1hcChmdW5jdGlvbihyZWN0cykge1xuICAgICAgICAgIHJldHVybiByZWN0cy5sZWZ0O1xuICAgICAgICB9KSk7XG4gICAgICAgIHZhciBtYXhSaWdodCA9IE1hdGgubWF4LmFwcGx5KE1hdGgsIGNsaWVudFJlY3RzLm1hcChmdW5jdGlvbihyZWN0cykge1xuICAgICAgICAgIHJldHVybiByZWN0cy5yaWdodDtcbiAgICAgICAgfSkpO1xuICAgICAgICB2YXIgbWVhc3VyZVJlY3RzID0gY2xpZW50UmVjdHMuZmlsdGVyKGZ1bmN0aW9uKHJlY3QpIHtcbiAgICAgICAgICByZXR1cm4gY3VycmVudEJhc2VQbGFjZW1lbnQgPT09IFwibGVmdFwiID8gcmVjdC5sZWZ0ID09PSBtaW5MZWZ0IDogcmVjdC5yaWdodCA9PT0gbWF4UmlnaHQ7XG4gICAgICAgIH0pO1xuICAgICAgICB2YXIgX3RvcCA9IG1lYXN1cmVSZWN0c1swXS50b3A7XG4gICAgICAgIHZhciBfYm90dG9tID0gbWVhc3VyZVJlY3RzW21lYXN1cmVSZWN0cy5sZW5ndGggLSAxXS5ib3R0b207XG4gICAgICAgIHZhciBfbGVmdCA9IG1pbkxlZnQ7XG4gICAgICAgIHZhciBfcmlnaHQgPSBtYXhSaWdodDtcbiAgICAgICAgdmFyIF93aWR0aCA9IF9yaWdodCAtIF9sZWZ0O1xuICAgICAgICB2YXIgX2hlaWdodCA9IF9ib3R0b20gLSBfdG9wO1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIHRvcDogX3RvcCxcbiAgICAgICAgICBib3R0b206IF9ib3R0b20sXG4gICAgICAgICAgbGVmdDogX2xlZnQsXG4gICAgICAgICAgcmlnaHQ6IF9yaWdodCxcbiAgICAgICAgICB3aWR0aDogX3dpZHRoLFxuICAgICAgICAgIGhlaWdodDogX2hlaWdodFxuICAgICAgICB9O1xuICAgICAgfVxuICAgICAgZGVmYXVsdDoge1xuICAgICAgICByZXR1cm4gYm91bmRpbmdSZWN0O1xuICAgICAgfVxuICAgIH1cbiAgfVxuICB2YXIgc3RpY2t5ID0ge1xuICAgIG5hbWU6IFwic3RpY2t5XCIsXG4gICAgZGVmYXVsdFZhbHVlOiBmYWxzZSxcbiAgICBmbjogZnVuY3Rpb24gZm4oaW5zdGFuY2UpIHtcbiAgICAgIHZhciByZWZlcmVuY2UgPSBpbnN0YW5jZS5yZWZlcmVuY2UsIHBvcHBlciA9IGluc3RhbmNlLnBvcHBlcjtcbiAgICAgIGZ1bmN0aW9uIGdldFJlZmVyZW5jZSgpIHtcbiAgICAgICAgcmV0dXJuIGluc3RhbmNlLnBvcHBlckluc3RhbmNlID8gaW5zdGFuY2UucG9wcGVySW5zdGFuY2Uuc3RhdGUuZWxlbWVudHMucmVmZXJlbmNlIDogcmVmZXJlbmNlO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gc2hvdWxkQ2hlY2sodmFsdWUpIHtcbiAgICAgICAgcmV0dXJuIGluc3RhbmNlLnByb3BzLnN0aWNreSA9PT0gdHJ1ZSB8fCBpbnN0YW5jZS5wcm9wcy5zdGlja3kgPT09IHZhbHVlO1xuICAgICAgfVxuICAgICAgdmFyIHByZXZSZWZSZWN0ID0gbnVsbDtcbiAgICAgIHZhciBwcmV2UG9wUmVjdCA9IG51bGw7XG4gICAgICBmdW5jdGlvbiB1cGRhdGVQb3NpdGlvbigpIHtcbiAgICAgICAgdmFyIGN1cnJlbnRSZWZSZWN0ID0gc2hvdWxkQ2hlY2soXCJyZWZlcmVuY2VcIikgPyBnZXRSZWZlcmVuY2UoKS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKSA6IG51bGw7XG4gICAgICAgIHZhciBjdXJyZW50UG9wUmVjdCA9IHNob3VsZENoZWNrKFwicG9wcGVyXCIpID8gcG9wcGVyLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpIDogbnVsbDtcbiAgICAgICAgaWYgKGN1cnJlbnRSZWZSZWN0ICYmIGFyZVJlY3RzRGlmZmVyZW50KHByZXZSZWZSZWN0LCBjdXJyZW50UmVmUmVjdCkgfHwgY3VycmVudFBvcFJlY3QgJiYgYXJlUmVjdHNEaWZmZXJlbnQocHJldlBvcFJlY3QsIGN1cnJlbnRQb3BSZWN0KSkge1xuICAgICAgICAgIGlmIChpbnN0YW5jZS5wb3BwZXJJbnN0YW5jZSkge1xuICAgICAgICAgICAgaW5zdGFuY2UucG9wcGVySW5zdGFuY2UudXBkYXRlKCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIHByZXZSZWZSZWN0ID0gY3VycmVudFJlZlJlY3Q7XG4gICAgICAgIHByZXZQb3BSZWN0ID0gY3VycmVudFBvcFJlY3Q7XG4gICAgICAgIGlmIChpbnN0YW5jZS5zdGF0ZS5pc01vdW50ZWQpIHtcbiAgICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUodXBkYXRlUG9zaXRpb24pO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4ge1xuICAgICAgICBvbk1vdW50OiBmdW5jdGlvbiBvbk1vdW50KCkge1xuICAgICAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5zdGlja3kpIHtcbiAgICAgICAgICAgIHVwZGF0ZVBvc2l0aW9uKCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9O1xuICAgIH1cbiAgfTtcbiAgZnVuY3Rpb24gYXJlUmVjdHNEaWZmZXJlbnQocmVjdEEsIHJlY3RCKSB7XG4gICAgaWYgKHJlY3RBICYmIHJlY3RCKSB7XG4gICAgICByZXR1cm4gcmVjdEEudG9wICE9PSByZWN0Qi50b3AgfHwgcmVjdEEucmlnaHQgIT09IHJlY3RCLnJpZ2h0IHx8IHJlY3RBLmJvdHRvbSAhPT0gcmVjdEIuYm90dG9tIHx8IHJlY3RBLmxlZnQgIT09IHJlY3RCLmxlZnQ7XG4gICAgfVxuICAgIHJldHVybiB0cnVlO1xuICB9XG4gIHRpcHB5Mi5zZXREZWZhdWx0UHJvcHMoe1xuICAgIHJlbmRlclxuICB9KTtcbiAgZXhwb3J0cy5hbmltYXRlRmlsbCA9IGFuaW1hdGVGaWxsO1xuICBleHBvcnRzLmNyZWF0ZVNpbmdsZXRvbiA9IGNyZWF0ZVNpbmdsZXRvbjtcbiAgZXhwb3J0cy5kZWZhdWx0ID0gdGlwcHkyO1xuICBleHBvcnRzLmRlbGVnYXRlID0gZGVsZWdhdGU7XG4gIGV4cG9ydHMuZm9sbG93Q3Vyc29yID0gZm9sbG93Q3Vyc29yMjtcbiAgZXhwb3J0cy5oaWRlQWxsID0gaGlkZUFsbDtcbiAgZXhwb3J0cy5pbmxpbmVQb3NpdGlvbmluZyA9IGlubGluZVBvc2l0aW9uaW5nO1xuICBleHBvcnRzLnJvdW5kQXJyb3cgPSBST1VORF9BUlJPVztcbiAgZXhwb3J0cy5zdGlja3kgPSBzdGlja3k7XG59KTtcblxuLy8gc3JjL2luZGV4LmpzXG52YXIgaW1wb3J0X3RpcHB5MiA9IF9fdG9Nb2R1bGUocmVxdWlyZV90aXBweV9janMoKSk7XG5cbi8vIHNyYy9idWlsZENvbmZpZ0Zyb21Nb2RpZmllcnMuanNcbnZhciBpbXBvcnRfdGlwcHkgPSBfX3RvTW9kdWxlKHJlcXVpcmVfdGlwcHlfY2pzKCkpO1xudmFyIGJ1aWxkQ29uZmlnRnJvbU1vZGlmaWVycyA9IChtb2RpZmllcnMpID0+IHtcbiAgY29uc3QgY29uZmlnID0ge1xuICAgIHBsdWdpbnM6IFtdXG4gIH07XG4gIGNvbnN0IGdldE1vZGlmaWVyQXJndW1lbnQgPSAobW9kaWZpZXIpID0+IHtcbiAgICByZXR1cm4gbW9kaWZpZXJzW21vZGlmaWVycy5pbmRleE9mKG1vZGlmaWVyKSArIDFdO1xuICB9O1xuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiYW5pbWF0aW9uXCIpKSB7XG4gICAgY29uZmlnLmFuaW1hdGlvbiA9IGdldE1vZGlmaWVyQXJndW1lbnQoXCJhbmltYXRpb25cIik7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImR1cmF0aW9uXCIpKSB7XG4gICAgY29uZmlnLmR1cmF0aW9uID0gcGFyc2VJbnQoZ2V0TW9kaWZpZXJBcmd1bWVudChcImR1cmF0aW9uXCIpKTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiZGVsYXlcIikpIHtcbiAgICBjb25zdCBkZWxheSA9IGdldE1vZGlmaWVyQXJndW1lbnQoXCJkZWxheVwiKTtcbiAgICBjb25maWcuZGVsYXkgPSBkZWxheS5pbmNsdWRlcyhcIi1cIikgPyBkZWxheS5zcGxpdChcIi1cIikubWFwKChuKSA9PiBwYXJzZUludChuKSkgOiBwYXJzZUludChkZWxheSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImN1cnNvclwiKSkge1xuICAgIGNvbmZpZy5wbHVnaW5zLnB1c2goaW1wb3J0X3RpcHB5LmZvbGxvd0N1cnNvcik7XG4gICAgY29uc3QgbmV4dCA9IGdldE1vZGlmaWVyQXJndW1lbnQoXCJjdXJzb3JcIik7XG4gICAgaWYgKFtcInhcIiwgXCJpbml0aWFsXCJdLmluY2x1ZGVzKG5leHQpKSB7XG4gICAgICBjb25maWcuZm9sbG93Q3Vyc29yID0gbmV4dCA9PT0gXCJ4XCIgPyBcImhvcml6b250YWxcIiA6IFwiaW5pdGlhbFwiO1xuICAgIH0gZWxzZSB7XG4gICAgICBjb25maWcuZm9sbG93Q3Vyc29yID0gdHJ1ZTtcbiAgICB9XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcIm9uXCIpKSB7XG4gICAgY29uZmlnLnRyaWdnZXIgPSBnZXRNb2RpZmllckFyZ3VtZW50KFwib25cIik7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImFycm93bGVzc1wiKSkge1xuICAgIGNvbmZpZy5hcnJvdyA9IGZhbHNlO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJodG1sXCIpKSB7XG4gICAgY29uZmlnLmFsbG93SFRNTCA9IHRydWU7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImludGVyYWN0aXZlXCIpKSB7XG4gICAgY29uZmlnLmludGVyYWN0aXZlID0gdHJ1ZTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiYm9yZGVyXCIpICYmIGNvbmZpZy5pbnRlcmFjdGl2ZSkge1xuICAgIGNvbmZpZy5pbnRlcmFjdGl2ZUJvcmRlciA9IHBhcnNlSW50KGdldE1vZGlmaWVyQXJndW1lbnQoXCJib3JkZXJcIikpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJkZWJvdW5jZVwiKSAmJiBjb25maWcuaW50ZXJhY3RpdmUpIHtcbiAgICBjb25maWcuaW50ZXJhY3RpdmVEZWJvdW5jZSA9IHBhcnNlSW50KGdldE1vZGlmaWVyQXJndW1lbnQoXCJkZWJvdW5jZVwiKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcIm1heC13aWR0aFwiKSkge1xuICAgIGNvbmZpZy5tYXhXaWR0aCA9IHBhcnNlSW50KGdldE1vZGlmaWVyQXJndW1lbnQoXCJtYXgtd2lkdGhcIikpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJ0aGVtZVwiKSkge1xuICAgIGNvbmZpZy50aGVtZSA9IGdldE1vZGlmaWVyQXJndW1lbnQoXCJ0aGVtZVwiKTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwicGxhY2VtZW50XCIpKSB7XG4gICAgY29uZmlnLnBsYWNlbWVudCA9IGdldE1vZGlmaWVyQXJndW1lbnQoXCJwbGFjZW1lbnRcIik7XG4gIH1cbiAgcmV0dXJuIGNvbmZpZztcbn07XG5cbi8vIHNyYy9pbmRleC5qc1xuZnVuY3Rpb24gc3JjX2RlZmF1bHQoQWxwaW5lKSB7XG4gIEFscGluZS5tYWdpYyhcInRvb2x0aXBcIiwgKGVsKSA9PiB7XG4gICAgcmV0dXJuIChjb250ZW50LCBjb25maWcgPSB7fSkgPT4ge1xuICAgICAgY29uc3QgaW5zdGFuY2UgPSAoMCwgaW1wb3J0X3RpcHB5Mi5kZWZhdWx0KShlbCwge1xuICAgICAgICBjb250ZW50LFxuICAgICAgICB0cmlnZ2VyOiBcIm1hbnVhbFwiLFxuICAgICAgICAuLi5jb25maWdcbiAgICAgIH0pO1xuICAgICAgaW5zdGFuY2Uuc2hvdygpO1xuICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgIGluc3RhbmNlLmhpZGUoKTtcbiAgICAgICAgc2V0VGltZW91dCgoKSA9PiBpbnN0YW5jZS5kZXN0cm95KCksIGNvbmZpZy5kdXJhdGlvbiB8fCAzMDApO1xuICAgICAgfSwgY29uZmlnLnRpbWVvdXQgfHwgMmUzKTtcbiAgICB9O1xuICB9KTtcbiAgQWxwaW5lLmRpcmVjdGl2ZShcInRvb2x0aXBcIiwgKGVsLCB7bW9kaWZpZXJzLCBleHByZXNzaW9ufSwge2V2YWx1YXRlTGF0ZXIsIGVmZmVjdH0pID0+IHtcbiAgICBjb25zdCBjb25maWcgPSBtb2RpZmllcnMubGVuZ3RoID4gMCA/IGJ1aWxkQ29uZmlnRnJvbU1vZGlmaWVycyhtb2RpZmllcnMpIDoge307XG4gICAgaWYgKCFlbC5fX3hfdGlwcHkpIHtcbiAgICAgIGVsLl9feF90aXBweSA9ICgwLCBpbXBvcnRfdGlwcHkyLmRlZmF1bHQpKGVsLCBjb25maWcpO1xuICAgIH1cbiAgICBjb25zdCBlbmFibGVUb29sdGlwID0gKCkgPT4gZWwuX194X3RpcHB5LmVuYWJsZSgpO1xuICAgIGNvbnN0IGRpc2FibGVUb29sdGlwID0gKCkgPT4gZWwuX194X3RpcHB5LmRpc2FibGUoKTtcbiAgICBjb25zdCBzZXR1cFRvb2x0aXAgPSAoY29udGVudCkgPT4ge1xuICAgICAgaWYgKCFjb250ZW50KSB7XG4gICAgICAgIGRpc2FibGVUb29sdGlwKCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBlbmFibGVUb29sdGlwKCk7XG4gICAgICAgIGVsLl9feF90aXBweS5zZXRDb250ZW50KGNvbnRlbnQpO1xuICAgICAgfVxuICAgIH07XG4gICAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInJhd1wiKSkge1xuICAgICAgc2V0dXBUb29sdGlwKGV4cHJlc3Npb24pO1xuICAgIH0gZWxzZSB7XG4gICAgICBjb25zdCBnZXRDb250ZW50ID0gZXZhbHVhdGVMYXRlcihleHByZXNzaW9uKTtcbiAgICAgIGVmZmVjdCgoKSA9PiB7XG4gICAgICAgIGdldENvbnRlbnQoKGNvbnRlbnQpID0+IHtcbiAgICAgICAgICBpZiAodHlwZW9mIGNvbnRlbnQgPT09IFwib2JqZWN0XCIpIHtcbiAgICAgICAgICAgIGVsLl9feF90aXBweS5zZXRQcm9wcyhjb250ZW50KTtcbiAgICAgICAgICAgIGVuYWJsZVRvb2x0aXAoKTtcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgc2V0dXBUb29sdGlwKGNvbnRlbnQpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICB9XG4gIH0pO1xufVxuXG4vLyBidWlsZHMvbW9kdWxlLmpzXG52YXIgbW9kdWxlX2RlZmF1bHQgPSBzcmNfZGVmYXVsdDtcbmV4cG9ydCB7XG4gIG1vZHVsZV9kZWZhdWx0IGFzIGRlZmF1bHRcbn07XG4iLCAiaW1wb3J0IEFscGluZSBmcm9tICdhbHBpbmVqcydcbmltcG9ydCBNb3VzZXRyYXAgZnJvbSAnQGRhbmhhcnJpbi9hbHBpbmUtbW91c2V0cmFwJ1xuaW1wb3J0IFBlcnNpc3QgZnJvbSAnQGFscGluZWpzL3BlcnNpc3QnXG5pbXBvcnQgVG9vbHRpcCBmcm9tICdAcnlhbmdqY2hhbmRsZXIvYWxwaW5lLXRvb2x0aXAnXG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2FscGluZTppbml0JywgKCkgPT4ge1xuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKE1vdXNldHJhcClcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihQZXJzaXN0KVxuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKFRvb2x0aXApXG5cbiAgICB3aW5kb3cuQWxwaW5lLnN0b3JlKCdzaWRlYmFyJywge1xuICAgICAgICBpc09wZW46IHdpbmRvdy5BbHBpbmUuJHBlcnNpc3QodHJ1ZSkuYXMoJ2lzT3BlbicpLFxuXG4gICAgICAgIGNvbGxhcHNlZEdyb3Vwczogd2luZG93LkFscGluZS4kcGVyc2lzdChudWxsKS5hcygnY29sbGFwc2VkR3JvdXBzJyksXG5cbiAgICAgICAgZ3JvdXBJc0NvbGxhcHNlZDogZnVuY3Rpb24gKGdyb3VwKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5jb2xsYXBzZWRHcm91cHMuaW5jbHVkZXMoZ3JvdXApXG4gICAgICAgIH0sXG5cbiAgICAgICAgY29sbGFwc2VHcm91cDogZnVuY3Rpb24gKGdyb3VwKSB7XG4gICAgICAgICAgICBpZiAodGhpcy5jb2xsYXBzZWRHcm91cHMuaW5jbHVkZXMoZ3JvdXApKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuY29sbGFwc2VkR3JvdXBzID0gdGhpcy5jb2xsYXBzZWRHcm91cHMuY29uY2F0KGdyb3VwKVxuICAgICAgICB9LFxuXG4gICAgICAgIHRvZ2dsZUNvbGxhcHNlZEdyb3VwOiBmdW5jdGlvbiAoZ3JvdXApIHtcbiAgICAgICAgICAgIHRoaXMuY29sbGFwc2VkR3JvdXBzID0gdGhpcy5jb2xsYXBzZWRHcm91cHMuaW5jbHVkZXMoZ3JvdXApXG4gICAgICAgICAgICAgICAgPyB0aGlzLmNvbGxhcHNlZEdyb3Vwcy5maWx0ZXIoXG4gICAgICAgICAgICAgICAgICAgICAgKGNvbGxhcHNlZEdyb3VwKSA9PiBjb2xsYXBzZWRHcm91cCAhPT0gZ3JvdXAsXG4gICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgOiB0aGlzLmNvbGxhcHNlZEdyb3Vwcy5jb25jYXQoZ3JvdXApXG4gICAgICAgIH0sXG5cbiAgICAgICAgY2xvc2U6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRoaXMuaXNPcGVuID0gZmFsc2VcbiAgICAgICAgfSxcblxuICAgICAgICBvcGVuOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLmlzT3BlbiA9IHRydWVcbiAgICAgICAgfSxcbiAgICB9KVxuXG4gICAgd2luZG93LkFscGluZS5zdG9yZShcbiAgICAgICAgJ3RoZW1lJyxcbiAgICAgICAgd2luZG93Lm1hdGNoTWVkaWEoJyhwcmVmZXJzLWNvbG9yLXNjaGVtZTogZGFyayknKS5tYXRjaGVzXG4gICAgICAgICAgICA/ICdkYXJrJ1xuICAgICAgICAgICAgOiAnbGlnaHQnLFxuICAgIClcblxuICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCd0aGVtZS1jaGFuZ2VkJywgKGV2ZW50KSA9PiB7XG4gICAgICAgIHdpbmRvdy5BbHBpbmUuc3RvcmUoJ3RoZW1lJywgZXZlbnQuZGV0YWlsKVxuICAgIH0pXG5cbiAgICB3aW5kb3dcbiAgICAgICAgLm1hdGNoTWVkaWEoJyhwcmVmZXJzLWNvbG9yLXNjaGVtZTogZGFyayknKVxuICAgICAgICAuYWRkRXZlbnRMaXN0ZW5lcignY2hhbmdlJywgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICB3aW5kb3cuQWxwaW5lLnN0b3JlKCd0aGVtZScsIGV2ZW50Lm1hdGNoZXMgPyAnZGFyaycgOiAnbGlnaHQnKVxuICAgICAgICB9KVxufSlcblxud2luZG93LkFscGluZSA9IEFscGluZVxuQWxwaW5lLnN0YXJ0KClcbiJdLAogICJtYXBwaW5ncyI6ICI7O0FBQ0EsTUFBSSxlQUFlO0FBQ25CLE1BQUksV0FBVztBQUNmLE1BQUksUUFBUSxDQUFDO0FBQ2IsTUFBSSxtQkFBbUI7QUFDdkIsV0FBUyxVQUFVLFVBQVU7QUFDM0IsYUFBUyxRQUFRO0FBQUEsRUFDbkI7QUFDQSxXQUFTLFNBQVMsS0FBSztBQUNyQixRQUFJLENBQUMsTUFBTSxTQUFTLEdBQUc7QUFDckIsWUFBTSxLQUFLLEdBQUc7QUFDaEIsZUFBVztBQUFBLEVBQ2I7QUFDQSxXQUFTLFdBQVcsS0FBSztBQUN2QixRQUFJLFFBQVEsTUFBTSxRQUFRLEdBQUc7QUFDN0IsUUFBSSxVQUFVLE1BQU0sUUFBUTtBQUMxQixZQUFNLE9BQU8sT0FBTyxDQUFDO0FBQUEsRUFDekI7QUFDQSxXQUFTLGFBQWE7QUFDcEIsUUFBSSxDQUFDLFlBQVksQ0FBQyxjQUFjO0FBQzlCLHFCQUFlO0FBQ2YscUJBQWUsU0FBUztBQUFBLElBQzFCO0FBQUEsRUFDRjtBQUNBLFdBQVMsWUFBWTtBQUNuQixtQkFBZTtBQUNmLGVBQVc7QUFDWCxhQUFTLElBQUksR0FB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QUFDZixhQUFLLFNBQVM7QUFBQSxNQUNsQjtBQUFBLE1BRUEsTUFBTSxXQUFZO0FBQ2QsYUFBSyxTQUFTO0FBQUEsTUFDbEI7QUFBQSxJQUNKLENBQUM7QUFFRCxXQUFPLE9BQU87QUFBQSxNQUNWO0FBQUEsTUFDQSxPQUFPLFdBQVcsOEJBQThCLEVBQUUsVUFDNUMsU0FDQTtBQUFBLElBQ1Y7QUFFQSxXQUFPLGlCQUFpQixpQkFBaUIsQ0FBQyxVQUFVO0FBQ2hELGFBQU8sT0FBTyxNQUFNLFNBQVMsTUFBTSxNQUFNO0FBQUEsSUFDN0MsQ0FBQztBQUVELFdBQ0ssV0FBVyw4QkFBOEIsRUFDekMsaUJBQWlCLFVBQVUsQ0FBQyxVQUFVO0FBQ25DLGFBQU8sT0FBTyxNQUFNLFNBQVMsTUFBTSxVQUFVLFNBQVMsT0FBTztBQUFBLElBQ2pFLENBQUM7QUFBQSxFQUNULENBQUM7QUFFRCxTQUFPLFNBQVM7QUFDaEIsaUJBQU8sTUFBTTsiLAogICJuYW1lcyI6IFsic3JjX2RlZmF1bHQiLCAiQWxwaW5lIiwgImV2YWx1YXRlIiwgIm1vZHVsZV9kZWZhdWx0IiwgInNyY19kZWZhdWx0IiwgIkFscGluZSIsICJnZXQiLCAic2V0IiwgIm1vZHVsZV9kZWZhdWx0IiwgIl9fY3JlYXRlIiwgIl9fZGVmUHJvcCIsICJfX2dldFByb3RvT2YiLCAiX19oYXNPd25Qcm9wIiwgIl9fZ2V0T3duUHJvcE5hbWVzIiwgIl9fZ2V0T3duUHJvcERlc2MiLCAiX19tYXJrQXNNb2R1bGUiLCAiX19jb21tb25KUyIsICJfX2V4cG9ydFN0YXIiLCAiX190b01vZHVsZSIsICJnZXRDb21wdXRlZFN0eWxlIiwgInN0YXJ0IiwgImRlYm91bmNlIiwgImVmZmVjdDIiLCAiZGF0YSIsICJlZmZlY3QiLCAiaGFzT3duUHJvcGVydHkiLCAiY2xvbmUiLCAicGx1Z2luIiwgIm9uIiwgImhhbmRsZXIiLCAidHJpZ2dlciIsICJzcmNfZGVmYXVsdCIsICJBbHBpbmUiLCAiZXZhbHVhdGVMYXRlciIsICJtb2R1bGVfZGVmYXVsdCIsICJtb2R1bGVfZGVmYXVsdCJdCn0K
