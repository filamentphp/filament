(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global = global || self, global.Alpine = factory());
  }(this, (function () { 'use strict';

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
          ownKeys(Object(source), true).forEach(function (key) {
            _defineProperty(target, key, source[key]);
          });
        } else if (Object.getOwnPropertyDescriptors) {
          Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
        } else {
          ownKeys(Object(source)).forEach(function (key) {
            Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
          });
        }
      }

      return target;
    }

    // Thanks @stimulus:
    // https://github.com/stimulusjs/stimulus/blob/master/packages/%40stimulus/core/src/application.ts
    function domReady() {
      return new Promise(resolve => {
        if (document.readyState == "loading") {
          document.addEventListener("DOMContentLoaded", resolve);
        } else {
          resolve();
        }
      });
    }
    function arrayUnique(array) {
      return Array.from(new Set(array));
    }
    function isTesting() {
      return navigator.userAgent.includes("Node.js") || navigator.userAgent.includes("jsdom");
    }
    function checkedAttrLooseCompare(valueA, valueB) {
      return valueA == valueB;
    }
    function warnIfMalformedTemplate(el, directive) {
      if (el.tagName.toLowerCase() !== 'template') {
        console.warn(`Alpine: [${directive}] directive should only be added to <template> tags. See https://github.com/alpinejs/alpine#${directive}`);
      } else if (el.content.childElementCount !== 1) {
        console.warn(`Alpine: <template> tag with [${directive}] encountered with an unexpected number of root elements. Make sure <template> has a single root element. `);
      }
    }
    function kebabCase(subject) {
      return subject.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/[_\s]/, '-').toLowerCase();
    }
    function camelCase(subject) {
      return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase());
    }
    function walk(el, callback) {
      if (callback(el) === false) return;
      let node = el.firstElementChild;

      while (node) {
        walk(node, callback);
        node = node.nextElementSibling;
      }
    }
    function debounce(func, wait) {
      var timeout;
      return function () {
        var context = this,
            args = arguments;

        var later = function later() {
          timeout = null;
          func.apply(context, args);
        };

        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    const handleError = (el, expression, error) => {
      console.warn(`Alpine Error: "${error}"\n\nExpression: "${expression}"\nElement:`, el);

      if (!isTesting()) {
        Object.assign(error, {
          el,
          expression
        });
        throw error;
      }
    };

    function tryCatch(cb, {
      el,
      expression
    }) {
      try {
        const value = cb();
        return value instanceof Promise ? value.catch(e => handleError(el, expression, e)) : value;
      } catch (e) {
        handleError(el, expression, e);
      }
    }

    function saferEval(el, expression, dataContext, additionalHelperVariables = {}) {
      return tryCatch(() => {
        if (typeof expression === 'function') {
          return expression.call(dataContext);
        }

        return new Function(['$data', ...Object.keys(additionalHelperVariables)], `var __alpine_result; with($data) { __alpine_result = ${expression} }; return __alpine_result`)(dataContext, ...Object.values(additionalHelperVariables));
      }, {
        el,
        expression
      });
    }
    function saferEvalNoReturn(el, expression, dataContext, additionalHelperVariables = {}) {
      return tryCatch(() => {
        if (typeof expression === 'function') {
          return Promise.resolve(expression.call(dataContext, additionalHelperVariables['$event']));
        }

        let AsyncFunction = Function;
        /* MODERN-ONLY:START */

        AsyncFunction = Object.getPrototypeOf(async function () {}).constructor;
        /* MODERN-ONLY:END */
        // For the cases when users pass only a function reference to the caller: `x-on:click="foo"`
        // Where "foo" is a function. Also, we'll pass the function the event instance when we call it.

        if (Object.keys(dataContext).includes(expression)) {
          let methodReference = new Function(['dataContext', ...Object.keys(additionalHelperVariables)], `with(dataContext) { return ${expression} }`)(dataContext, ...Object.values(additionalHelperVariables));

          if (typeof methodReference === 'function') {
            return Promise.resolve(methodReference.call(dataContext, additionalHelperVariables['$event']));
          } else {
            return Promise.resolve();
          }
        }

        return Promise.resolve(new AsyncFunction(['dataContext', ...Object.keys(additionalHelperVariables)], `with(dataContext) { ${expression} }`)(dataContext, ...Object.values(additionalHelperVariables)));
      }, {
        el,
        expression
      });
    }
    const xAttrRE = /^x-(on|bind|data|text|html|model|if|for|show|cloak|transition|ref|spread)\b/;
    function isXAttr(attr) {
      const name = replaceAtAndColonWithStandardSyntax(attr.name);
      return xAttrRE.test(name);
    }
    function getXAttrs(el, component, type) {
      let directives = Array.from(el.attributes).filter(isXAttr).map(parseHtmlAttribute); // Get an object of directives from x-spread.

      let spreadDirective = directives.filter(directive => directive.type === 'spread')[0];

      if (spreadDirective) {
        let spreadObject = saferEval(el, spreadDirective.expression, component.$data); // Add x-spread directives to the pile of existing directives.

        directives = directives.concat(Object.entries(spreadObject).map(([name, value]) => parseHtmlAttribute({
          name,
          value
        })));
      }

      if (type) return directives.filter(i => i.type === type);
      return sortDirectives(directives);
    }

    function sortDirectives(directives) {
      let directiveOrder = ['bind', 'model', 'show', 'catch-all'];
      return directives.sort((a, b) => {
        let typeA = directiveOrder.indexOf(a.type) === -1 ? 'catch-all' : a.type;
        let typeB = directiveOrder.indexOf(b.type) === -1 ? 'catch-all' : b.type;
        return directiveOrder.indexOf(typeA) - directiveOrder.indexOf(typeB);
      });
    }

    function parseHtmlAttribute({
      name,
      value
    }) {
      const normalizedName = replaceAtAndColonWithStandardSyntax(name);
      const typeMatch = normalizedName.match(xAttrRE);
      const valueMatch = normalizedName.match(/:([a-zA-Z0-9\-:]+)/);
      const modifiers = normalizedName.match(/\.[^.\]]+(?=[^\]]*$)/g) || [];
      return {
        type: typeMatch ? typeMatch[1] : null,
        value: valueMatch ? valueMatch[1] : null,
        modifiers: modifiers.map(i => i.replace('.', '')),
        expression: value
      };
    }
    function isBooleanAttr(attrName) {
      // As per HTML spec table https://html.spec.whatwg.org/multipage/indices.html#attributes-3:boolean-attribute
      // Array roughly ordered by estimated usage
      const booleanAttributes = ['disabled', 'checked', 'required', 'readonly', 'hidden', 'open', 'selected', 'autofocus', 'itemscope', 'multiple', 'novalidate', 'allowfullscreen', 'allowpaymentrequest', 'formnovalidate', 'autoplay', 'controls', 'loop', 'muted', 'playsinline', 'default', 'ismap', 'reversed', 'async', 'defer', 'nomodule'];
      return booleanAttributes.includes(attrName);
    }
    function replaceAtAndColonWithStandardSyntax(name) {
      if (name.startsWith('@')) {
        return name.replace('@', 'x-on:');
      } else if (name.startsWith(':')) {
        return name.replace(':', 'x-bind:');
      }

      return name;
    }
    function convertClassStringToArray(classList, filterFn = Boolean) {
      return classList.split(' ').filter(filterFn);
    }
    const TRANSITION_TYPE_IN = 'in';
    const TRANSITION_TYPE_OUT = 'out';
    const TRANSITION_CANCELLED = 'cancelled';
    function transitionIn(el, show, reject, component, forceSkip = false) {
      // We don't want to transition on the initial page load.
      if (forceSkip) return show();

      if (el.__x_transition && el.__x_transition.type === TRANSITION_TYPE_IN) {
        // there is already a similar transition going on, this was probably triggered by
        // a change in a different property, let's just leave the previous one doing its job
        return;
      }

      const attrs = getXAttrs(el, component, 'transition');
      const showAttr = getXAttrs(el, component, 'show')[0]; // If this is triggered by a x-show.transition.

      if (showAttr && showAttr.modifiers.includes('transition')) {
        let modifiers = showAttr.modifiers; // If x-show.transition.out, we'll skip the "in" transition.

        if (modifiers.includes('out') && !modifiers.includes('in')) return show();
        const settingBothSidesOfTransition = modifiers.includes('in') && modifiers.includes('out'); // If x-show.transition.in...out... only use "in" related modifiers for this transition.

        modifiers = settingBothSidesOfTransition ? modifiers.filter((i, index) => index < modifiers.indexOf('out')) : modifiers;
        transitionHelperIn(el, modifiers, show, reject); // Otherwise, we can assume x-transition:enter.
      } else if (attrs.some(attr => ['enter', 'enter-start', 'enter-end'].includes(attr.value))) {
        transitionClassesIn(el, component, attrs, show, reject);
      } else {
        // If neither, just show that damn thing.
        show();
      }
    }
    function transitionOut(el, hide, reject, component, forceSkip = false) {
      // We don't want to transition on the initial page load.
      if (forceSkip) return hide();

      if (el.__x_transition && el.__x_transition.type === TRANSITION_TYPE_OUT) {
        // there is already a similar transition going on, this was probably triggered by
        // a change in a different property, let's just leave the previous one doing its job
        return;
      }

      const attrs = getXAttrs(el, component, 'transition');
      const showAttr = getXAttrs(el, component, 'show')[0];

      if (showAttr && showAttr.modifiers.includes('transition')) {
        let modifiers = showAttr.modifiers;
        if (modifiers.includes('in') && !modifiers.includes('out')) return hide();
        const settingBothSidesOfTransition = modifiers.includes('in') && modifiers.includes('out');
        modifiers = settingBothSidesOfTransition ? modifiers.filter((i, index) => index > modifiers.indexOf('out')) : modifiers;
        transitionHelperOut(el, modifiers, settingBothSidesOfTransition, hide, reject);
      } else if (attrs.some(attr => ['leave', 'leave-start', 'leave-end'].includes(attr.value))) {
        transitionClassesOut(el, component, attrs, hide, reject);
      } else {
        hide();
      }
    }
    function transitionHelperIn(el, modifiers, showCallback, reject) {
      // Default values inspired by: https://material.io/design/motion/speed.html#duration
      const styleValues = {
        duration: modifierValue(modifiers, 'duration', 150),
        origin: modifierValue(modifiers, 'origin', 'center'),
        first: {
          opacity: 0,
          scale: modifierValue(modifiers, 'scale', 95)
        },
        second: {
          opacity: 1,
          scale: 100
        }
      };
      transitionHelper(el, modifiers, showCallback, () => {}, reject, styleValues, TRANSITION_TYPE_IN);
    }
    function transitionHelperOut(el, modifiers, settingBothSidesOfTransition, hideCallback, reject) {
      // Make the "out" transition .5x slower than the "in". (Visually better)
      // HOWEVER, if they explicitly set a duration for the "out" transition,
      // use that.
      const duration = settingBothSidesOfTransition ? modifierValue(modifiers, 'duration', 150) : modifierValue(modifiers, 'duration', 150) / 2;
      const styleValues = {
        duration: duration,
        origin: modifierValue(modifiers, 'origin', 'center'),
        first: {
          opacity: 1,
          scale: 100
        },
        second: {
          opacity: 0,
          scale: modifierValue(modifiers, 'scale', 95)
        }
      };
      transitionHelper(el, modifiers, () => {}, hideCallback, reject, styleValues, TRANSITION_TYPE_OUT);
    }

    function modifierValue(modifiers, key, fallback) {
      // If the modifier isn't present, use the default.
      if (modifiers.indexOf(key) === -1) return fallback; // If it IS present, grab the value after it: x-show.transition.duration.500ms

      const rawValue = modifiers[modifiers.indexOf(key) + 1];
      if (!rawValue) return fallback;

      if (key === 'scale') {
        // Check if the very next value is NOT a number and return the fallback.
        // If x-show.transition.scale, we'll use the default scale value.
        // That is how a user opts out of the opacity transition.
        if (!isNumeric(rawValue)) return fallback;
      }

      if (key === 'duration') {
        // Support x-show.transition.duration.500ms && duration.500
        let match = rawValue.match(/([0-9]+)ms/);
        if (match) return match[1];
      }

      if (key === 'origin') {
        // Support chaining origin directions: x-show.transition.top.right
        if (['top', 'right', 'left', 'center', 'bottom'].includes(modifiers[modifiers.indexOf(key) + 2])) {
          return [rawValue, modifiers[modifiers.indexOf(key) + 2]].join(' ');
        }
      }

      return rawValue;
    }

    function transitionHelper(el, modifiers, hook1, hook2, reject, styleValues, type) {
      // clear the previous transition if exists to avoid caching the wrong styles
      if (el.__x_transition) {
        el.__x_transition.cancel && el.__x_transition.cancel();
      } // If the user set these style values, we'll put them back when we're done with them.


      const opacityCache = el.style.opacity;
      const transformCache = el.style.transform;
      const transformOriginCache = el.style.transformOrigin; // If no modifiers are present: x-show.transition, we'll default to both opacity and scale.

      const noModifiers = !modifiers.includes('opacity') && !modifiers.includes('scale');
      const transitionOpacity = noModifiers || modifiers.includes('opacity');
      const transitionScale = noModifiers || modifiers.includes('scale'); // These are the explicit stages of a transition (same stages for in and for out).
      // This way you can get a birds eye view of the hooks, and the differences
      // between them.

      const stages = {
        start() {
          if (transitionOpacity) el.style.opacity = styleValues.first.opacity;
          if (transitionScale) el.style.transform = `scale(${styleValues.first.scale / 100})`;
        },

        during() {
          if (transitionScale) el.style.transformOrigin = styleValues.origin;
          el.style.transitionProperty = [transitionOpacity ? `opacity` : ``, transitionScale ? `transform` : ``].join(' ').trim();
          el.style.transitionDuration = `${styleValues.duration / 1000}s`;
          el.style.transitionTimingFunction = `cubic-bezier(0.4, 0.0, 0.2, 1)`;
        },

        show() {
          hook1();
        },

        end() {
          if (transitionOpacity) el.style.opacity = styleValues.second.opacity;
          if (transitionScale) el.style.transform = `scale(${styleValues.second.scale / 100})`;
        },

        hide() {
          hook2();
        },

        cleanup() {
          if (transitionOpacity) el.style.opacity = opacityCache;
          if (transitionScale) el.style.transform = transformCache;
          if (transitionScale) el.style.transformOrigin = transformOriginCache;
          el.style.transitionProperty = null;
          el.style.transitionDuration = null;
          el.style.transitionTimingFunction = null;
        }

      };
      transition(el, stages, type, reject);
    }

    const ensureStringExpression = (expression, el, component) => {
      return typeof expression === 'function' ? component.evaluateReturnExpression(el, expression) : expression;
    };

    function transitionClassesIn(el, component, directives, showCallback, reject) {
      const enter = convertClassStringToArray(ensureStringExpression((directives.find(i => i.value === 'enter') || {
        expression: ''
      }).expression, el, component));
      const enterStart = convertClassStringToArray(ensureStringExpression((directives.find(i => i.value === 'enter-start') || {
        expression: ''
      }).expression, el, component));
      const enterEnd = convertClassStringToArray(ensureStringExpression((directives.find(i => i.value === 'enter-end') || {
        expression: ''
      }).expression, el, component));
      transitionClasses(el, enter, enterStart, enterEnd, showCallback, () => {}, TRANSITION_TYPE_IN, reject);
    }
    function transitionClassesOut(el, component, directives, hideCallback, reject) {
      const leave = convertClassStringToArray(ensureStringExpression((directives.find(i => i.value === 'leave') || {
        expression: ''
      }).expression, el, component));
      const leaveStart = convertClassStringToArray(ensureStringExpression((directives.find(i => i.value === 'leave-start') || {
        expression: ''
      }).expression, el, component));
      const leaveEnd = convertClassStringToArray(ensureStringExpression((directives.find(i => i.value === 'leave-end') || {
        expression: ''
      }).expression, el, component));
      transitionClasses(el, leave, leaveStart, leaveEnd, () => {}, hideCallback, TRANSITION_TYPE_OUT, reject);
    }
    function transitionClasses(el, classesDuring, classesStart, classesEnd, hook1, hook2, type, reject) {
      // clear the previous transition if exists to avoid caching the wrong classes
      if (el.__x_transition) {
        el.__x_transition.cancel && el.__x_transition.cancel();
      }

      const originalClasses = el.__x_original_classes || [];
      const stages = {
        start() {
          el.classList.add(...classesStart);
        },

        during() {
          el.classList.add(...classesDuring);
        },

        show() {
          hook1();
        },

        end() {
          // Don't remove classes that were in the original class attribute.
          el.classList.remove(...classesStart.filter(i => !originalClasses.includes(i)));
          el.classList.add(...classesEnd);
        },

        hide() {
          hook2();
        },

        cleanup() {
          el.classList.remove(...classesDuring.filter(i => !originalClasses.includes(i)));
          el.classList.remove(...classesEnd.filter(i => !originalClasses.includes(i)));
        }

      };
      transition(el, stages, type, reject);
    }
    function transition(el, stages, type, reject) {
      const finish = once(() => {
        stages.hide(); // Adding an "isConnected" check, in case the callback
        // removed the element from the DOM.

        if (el.isConnected) {
          stages.cleanup();
        }

        delete el.__x_transition;
      });
      el.__x_transition = {
        // Set transition type so we can avoid clearing transition if the direction is the same
        type: type,
        // create a callback for the last stages of the transition so we can call it
        // from different point and early terminate it. Once will ensure that function
        // is only called one time.
        cancel: once(() => {
          reject(TRANSITION_CANCELLED);
          finish();
        }),
        finish,
        // This store the next animation frame so we can cancel it
        nextFrame: null
      };
      stages.start();
      stages.during();
      el.__x_transition.nextFrame = requestAnimationFrame(() => {
        // Note: Safari's transitionDuration property will list out comma separated transition durations
        // for every single transition property. Let's grab the first one and call it a day.
        let duration = Number(getComputedStyle(el).transitionDuration.replace(/,.*/, '').replace('s', '')) * 1000;

        if (duration === 0) {
          duration = Number(getComputedStyle(el).animationDuration.replace('s', '')) * 1000;
        }

        stages.show();
        el.__x_transition.nextFrame = requestAnimationFrame(() => {
          stages.end();
          setTimeout(el.__x_transition.finish, duration);
        });
      });
    }
    function isNumeric(subject) {
      return !Array.isArray(subject) && !isNaN(subject);
    } // Thanks @vuejs
    // https://github.com/vuejs/vue/blob/4de4649d9637262a9b007720b59f80ac72a5620c/src/shared/util.js

    function once(callback) {
      let called = false;
      return function () {
        if (!called) {
          called = true;
          callback.apply(this, arguments);
        }
      };
    }

    function handleForDirective(component, templateEl, expression, initialUpdate, extraVars) {
      warnIfMalformedTemplate(templateEl, 'x-for');
      let iteratorNames = typeof expression === 'function' ? parseForExpression(component.evaluateReturnExpression(templateEl, expression)) : parseForExpression(expression);
      let items = evaluateItemsAndReturnEmptyIfXIfIsPresentAndFalseOnElement(component, templateEl, iteratorNames, extraVars); // As we walk the array, we'll also walk the DOM (updating/creating as we go).

      let currentEl = templateEl;
      items.forEach((item, index) => {
        let iterationScopeVariables = getIterationScopeVariables(iteratorNames, item, index, items, extraVars());
        let currentKey = generateKeyForIteration(component, templateEl, index, iterationScopeVariables);
        let nextEl = lookAheadForMatchingKeyedElementAndMoveItIfFound(currentEl.nextElementSibling, currentKey); // If we haven't found a matching key, insert the element at the current position.

        if (!nextEl) {
          nextEl = addElementInLoopAfterCurrentEl(templateEl, currentEl); // And transition it in if it's not the first page load.

          transitionIn(nextEl, () => {}, () => {}, component, initialUpdate);
          nextEl.__x_for = iterationScopeVariables;
          component.initializeElements(nextEl, () => nextEl.__x_for); // Otherwise update the element we found.
        } else {
          // Temporarily remove the key indicator to allow the normal "updateElements" to work.
          delete nextEl.__x_for_key;
          nextEl.__x_for = iterationScopeVariables;
          component.updateElements(nextEl, () => nextEl.__x_for);
        }

        currentEl = nextEl;
        currentEl.__x_for_key = currentKey;
      });
      removeAnyLeftOverElementsFromPreviousUpdate(currentEl, component);
    } // This was taken from VueJS 2.* core. Thanks Vue!

    function parseForExpression(expression) {
      let forIteratorRE = /,([^,\}\]]*)(?:,([^,\}\]]*))?$/;
      let stripParensRE = /^\(|\)$/g;
      let forAliasRE = /([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/;
      let inMatch = String(expression).match(forAliasRE);
      if (!inMatch) return;
      let res = {};
      res.items = inMatch[2].trim();
      let item = inMatch[1].trim().replace(stripParensRE, '');
      let iteratorMatch = item.match(forIteratorRE);

      if (iteratorMatch) {
        res.item = item.replace(forIteratorRE, '').trim();
        res.index = iteratorMatch[1].trim();

        if (iteratorMatch[2]) {
          res.collection = iteratorMatch[2].trim();
        }
      } else {
        res.item = item;
      }

      return res;
    }

    function getIterationScopeVariables(iteratorNames, item, index, items, extraVars) {
      // We must create a new object, so each iteration has a new scope
      let scopeVariables = extraVars ? _objectSpread2({}, extraVars) : {};
      scopeVariables[iteratorNames.item] = item;
      if (iteratorNames.index) scopeVariables[iteratorNames.index] = index;
      if (iteratorNames.collection) scopeVariables[iteratorNames.collection] = items;
      return scopeVariables;
    }

    function generateKeyForIteration(component, el, index, iterationScopeVariables) {
      let bindKeyAttribute = getXAttrs(el, component, 'bind').filter(attr => attr.value === 'key')[0]; // If the dev hasn't specified a key, just return the index of the iteration.

      if (!bindKeyAttribute) return index;
      return component.evaluateReturnExpression(el, bindKeyAttribute.expression, () => iterationScopeVariables);
    }

    function evaluateItemsAndReturnEmptyIfXIfIsPresentAndFalseOnElement(component, el, iteratorNames, extraVars) {
      let ifAttribute = getXAttrs(el, component, 'if')[0];

      if (ifAttribute && !component.evaluateReturnExpression(el, ifAttribute.expression)) {
        return [];
      }

      let items = component.evaluateReturnExpression(el, iteratorNames.items, extraVars); // This adds support for the `i in n` syntax.

      if (isNumeric(items) && items >= 0) {
        items = Array.from(Array(items).keys(), i => i + 1);
      }

      return items;
    }

    function addElementInLoopAfterCurrentEl(templateEl, currentEl) {
      let clone = document.importNode(templateEl.content, true);
      currentEl.parentElement.insertBefore(clone, currentEl.nextElementSibling);
      return currentEl.nextElementSibling;
    }

    function lookAheadForMatchingKeyedElementAndMoveItIfFound(nextEl, currentKey) {
      if (!nextEl) return; // If we are already past the x-for generated elements, we don't need to look ahead.

      if (nextEl.__x_for_key === undefined) return; // If the the key's DO match, no need to look ahead.

      if (nextEl.__x_for_key === currentKey) return nextEl; // If they don't, we'll look ahead for a match.
      // If we find it, we'll move it to the current position in the loop.

      let tmpNextEl = nextEl;

      while (tmpNextEl) {
        if (tmpNextEl.__x_for_key === currentKey) {
          return tmpNextEl.parentElement.insertBefore(tmpNextEl, nextEl);
        }

        tmpNextEl = tmpNextEl.nextElementSibling && tmpNextEl.nextElementSibling.__x_for_key !== undefined ? tmpNextEl.nextElementSibling : false;
      }
    }

    function removeAnyLeftOverElementsFromPreviousUpdate(currentEl, component) {
      var nextElementFromOldLoop = currentEl.nextElementSibling && currentEl.nextElementSibling.__x_for_key !== undefined ? currentEl.nextElementSibling : false;

      while (nextElementFromOldLoop) {
        let nextElementFromOldLoopImmutable = nextElementFromOldLoop;
        let nextSibling = nextElementFromOldLoop.nextElementSibling;
        transitionOut(nextElementFromOldLoop, () => {
          nextElementFromOldLoopImmutable.remove();
        }, () => {}, component);
        nextElementFromOldLoop = nextSibling && nextSibling.__x_for_key !== undefined ? nextSibling : false;
      }
    }

    function handleAttributeBindingDirective(component, el, attrName, expression, extraVars, attrType, modifiers) {
      var value = component.evaluateReturnExpression(el, expression, extraVars);

      if (attrName === 'value') {
        if (Alpine.ignoreFocusedForValueBinding && document.activeElement.isSameNode(el)) return; // If nested model key is undefined, set the default value to empty string.

        if (value === undefined && String(expression).match(/\./)) {
          value = '';
        }

        if (el.type === 'radio') {
          // Set radio value from x-bind:value, if no "value" attribute exists.
          // If there are any initial state values, radio will have a correct
          // "checked" value since x-bind:value is processed before x-model.
          if (el.attributes.value === undefined && attrType === 'bind') {
            el.value = value;
          } else if (attrType !== 'bind') {
            el.checked = checkedAttrLooseCompare(el.value, value);
          }
        } else if (el.type === 'checkbox') {
          // If we are explicitly binding a string to the :value, set the string,
          // If the value is a boolean, leave it alone, it will be set to "on"
          // automatically.
          if (typeof value !== 'boolean' && ![null, undefined].includes(value) && attrType === 'bind') {
            el.value = String(value);
          } else if (attrType !== 'bind') {
            if (Array.isArray(value)) {
              // I'm purposely not using Array.includes here because it's
              // strict, and because of Numeric/String mis-casting, I
              // want the "includes" to be "fuzzy".
              el.checked = value.some(val => checkedAttrLooseCompare(val, el.value));
            } else {
              el.checked = !!value;
            }
          }
        } else if (el.tagName === 'SELECT') {
          updateSelect(el, value);
        } else {
          if (el.value === value) return;
          el.value = value;
        }
      } else if (attrName === 'class') {
        if (Array.isArray(value)) {
          const originalClasses = el.__x_original_classes || [];
          el.setAttribute('class', arrayUnique(originalClasses.concat(value)).join(' '));
        } else if (typeof value === 'object') {
          // Sorting the keys / class names by their boolean value will ensure that
          // anything that evaluates to `false` and needs to remove classes is run first.
          const keysSortedByBooleanValue = Object.keys(value).sort((a, b) => value[a] - value[b]);
          keysSortedByBooleanValue.forEach(classNames => {
            if (value[classNames]) {
              convertClassStringToArray(classNames).forEach(className => el.classList.add(className));
            } else {
              convertClassStringToArray(classNames).forEach(className => el.classList.remove(className));
            }
          });
        } else {
          const originalClasses = el.__x_original_classes || [];
          const newClasses = value ? convertClassStringToArray(value) : [];
          el.setAttribute('class', arrayUnique(originalClasses.concat(newClasses)).join(' '));
        }
      } else {
        attrName = modifiers.includes('camel') ? camelCase(attrName) : attrName; // If an attribute's bound value is null, undefined or false, remove the attribute

        if ([null, undefined, false].includes(value)) {
          el.removeAttribute(attrName);
        } else {
          isBooleanAttr(attrName) ? setIfChanged(el, attrName, attrName) : setIfChanged(el, attrName, value);
        }
      }
    }

    function setIfChanged(el, attrName, value) {
      if (el.getAttribute(attrName) != value) {
        el.setAttribute(attrName, value);
      }
    }

    function updateSelect(el, value) {
      const arrayWrappedValue = [].concat(value).map(value => {
        return value + '';
      });
      Array.from(el.options).forEach(option => {
        option.selected = arrayWrappedValue.includes(option.value || option.text);
      });
    }

    function handleTextDirective(el, output, expression) {
      // If nested model key is undefined, set the default value to empty string.
      if (output === undefined && String(expression).match(/\./)) {
        output = '';
      }

      el.textContent = output;
    }

    function handleHtmlDirective(component, el, expression, extraVars) {
      el.innerHTML = component.evaluateReturnExpression(el, expression, extraVars);
    }

    function handleShowDirective(component, el, value, modifiers, initialUpdate = false) {
      const hide = () => {
        el.style.display = 'none';
        el.__x_is_shown = false;
      };

      const show = () => {
        if (el.style.length === 1 && el.style.display === 'none') {
          el.removeAttribute('style');
        } else {
          el.style.removeProperty('display');
        }

        el.__x_is_shown = true;
      };

      if (initialUpdate === true) {
        if (value) {
          show();
        } else {
          hide();
        }

        return;
      }

      const handle = (resolve, reject) => {
        if (value) {
          if (el.style.display === 'none' || el.__x_transition) {
            transitionIn(el, () => {
              show();
            }, reject, component);
          }

          resolve(() => {});
        } else {
          if (el.style.display !== 'none') {
            transitionOut(el, () => {
              resolve(() => {
                hide();
              });
            }, reject, component);
          } else {
            resolve(() => {});
          }
        }
      }; // The working of x-show is a bit complex because we need to
      // wait for any child transitions to finish before hiding
      // some element. Also, this has to be done recursively.
      // If x-show.immediate, foregoe the waiting.


      if (modifiers.includes('immediate')) {
        handle(finish => finish(), () => {});
        return;
      } // x-show is encountered during a DOM tree walk. If an element
      // we encounter is NOT a child of another x-show element we
      // can execute the previous x-show stack (if one exists).


      if (component.showDirectiveLastElement && !component.showDirectiveLastElement.contains(el)) {
        component.executeAndClearRemainingShowDirectiveStack();
      }

      component.showDirectiveStack.push(handle);
      component.showDirectiveLastElement = el;
    }

    function handleIfDirective(component, el, expressionResult, initialUpdate, extraVars) {
      warnIfMalformedTemplate(el, 'x-if');
      const elementHasAlreadyBeenAdded = el.nextElementSibling && el.nextElementSibling.__x_inserted_me === true;

      if (expressionResult && (!elementHasAlreadyBeenAdded || el.__x_transition)) {
        const clone = document.importNode(el.content, true);
        el.parentElement.insertBefore(clone, el.nextElementSibling);
        transitionIn(el.nextElementSibling, () => {}, () => {}, component, initialUpdate);
        component.initializeElements(el.nextElementSibling, extraVars);
        el.nextElementSibling.__x_inserted_me = true;
      } else if (!expressionResult && elementHasAlreadyBeenAdded) {
        transitionOut(el.nextElementSibling, () => {
          el.nextElementSibling.remove();
        }, () => {}, component, initialUpdate);
      }
    }

    function registerListener(component, el, event, modifiers, expression, extraVars = {}) {
      const options = {
        passive: modifiers.includes('passive')
      };

      if (modifiers.includes('camel')) {
        event = camelCase(event);
      }

      let handler, listenerTarget;

      if (modifiers.includes('away')) {
        listenerTarget = document;

        handler = e => {
          // Don't do anything if the click came from the element or within it.
          if (el.contains(e.target)) return; // Don't do anything if this element isn't currently visible.

          if (el.offsetWidth < 1 && el.offsetHeight < 1) return; // Now that we are sure the element is visible, AND the click
          // is from outside it, let's run the expression.

          runListenerHandler(component, expression, e, extraVars);

          if (modifiers.includes('once')) {
            document.removeEventListener(event, handler, options);
          }
        };
      } else {
        listenerTarget = modifiers.includes('window') ? window : modifiers.includes('document') ? document : el;

        handler = e => {
          // Remove this global event handler if the element that declared it
          // has been removed. It's now stale.
          if (listenerTarget === window || listenerTarget === document) {
            if (!document.body.contains(el)) {
              listenerTarget.removeEventListener(event, handler, options);
              return;
            }
          }

          if (isKeyEvent(event)) {
            if (isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers)) {
              return;
            }
          }

          if (modifiers.includes('prevent')) e.preventDefault();
          if (modifiers.includes('stop')) e.stopPropagation(); // If the .self modifier isn't present, or if it is present and
          // the target element matches the element we are registering the
          // event on, run the handler

          if (!modifiers.includes('self') || e.target === el) {
            const returnValue = runListenerHandler(component, expression, e, extraVars);
            returnValue.then(value => {
              if (value === false) {
                e.preventDefault();
              } else {
                if (modifiers.includes('once')) {
                  listenerTarget.removeEventListener(event, handler, options);
                }
              }
            });
          }
        };
      }

      if (modifiers.includes('debounce')) {
        let nextModifier = modifiers[modifiers.indexOf('debounce') + 1] || 'invalid-wait';
        let wait = isNumeric(nextModifier.split('ms')[0]) ? Number(nextModifier.split('ms')[0]) : 250;
        handler = debounce(handler, wait);
      }

      listenerTarget.addEventListener(event, handler, options);
    }

    function runListenerHandler(component, expression, e, extraVars) {
      return component.evaluateCommandExpression(e.target, expression, () => {
        return _objectSpread2(_objectSpread2({}, extraVars()), {}, {
          '$event': e
        });
      });
    }

    function isKeyEvent(event) {
      return ['keydown', 'keyup'].includes(event);
    }

    function isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers) {
      let keyModifiers = modifiers.filter(i => {
        return !['window', 'document', 'prevent', 'stop'].includes(i);
      });

      if (keyModifiers.includes('debounce')) {
        let debounceIndex = keyModifiers.indexOf('debounce');
        keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || 'invalid-wait').split('ms')[0]) ? 2 : 1);
      } // If no modifier is specified, we'll call it a press.


      if (keyModifiers.length === 0) return false; // If one is passed, AND it matches the key pressed, we'll call it a press.

      if (keyModifiers.length === 1 && keyModifiers[0] === keyToModifier(e.key)) return false; // The user is listening for key combinations.

      const systemKeyModifiers = ['ctrl', 'shift', 'alt', 'meta', 'cmd', 'super'];
      const selectedSystemKeyModifiers = systemKeyModifiers.filter(modifier => keyModifiers.includes(modifier));
      keyModifiers = keyModifiers.filter(i => !selectedSystemKeyModifiers.includes(i));

      if (selectedSystemKeyModifiers.length > 0) {
        const activelyPressedKeyModifiers = selectedSystemKeyModifiers.filter(modifier => {
          // Alias "cmd" and "super" to "meta"
          if (modifier === 'cmd' || modifier === 'super') modifier = 'meta';
          return e[`${modifier}Key`];
        }); // If all the modifiers selected are pressed, ...

        if (activelyPressedKeyModifiers.length === selectedSystemKeyModifiers.length) {
          // AND the remaining key is pressed as well. It's a press.
          if (keyModifiers[0] === keyToModifier(e.key)) return false;
        }
      } // We'll call it NOT a valid keypress.


      return true;
    }

    function keyToModifier(key) {
      switch (key) {
        case '/':
          return 'slash';

        case ' ':
        case 'Spacebar':
          return 'space';

        default:
          return key && kebabCase(key);
      }
    }

    function registerModelListener(component, el, modifiers, expression, extraVars) {
      // If the element we are binding to is a select, a radio, or checkbox
      // we'll listen for the change event instead of the "input" event.
      var event = el.tagName.toLowerCase() === 'select' || ['checkbox', 'radio'].includes(el.type) || modifiers.includes('lazy') ? 'change' : 'input';
      const listenerExpression = `${expression} = rightSideOfExpression($event, ${expression})`;
      registerListener(component, el, event, modifiers, listenerExpression, () => {
        return _objectSpread2(_objectSpread2({}, extraVars()), {}, {
          rightSideOfExpression: generateModelAssignmentFunction(el, modifiers, expression)
        });
      });
    }

    function generateModelAssignmentFunction(el, modifiers, expression) {
      if (el.type === 'radio') {
        // Radio buttons only work properly when they share a name attribute.
        // People might assume we take care of that for them, because
        // they already set a shared "x-model" attribute.
        if (!el.hasAttribute('name')) el.setAttribute('name', expression);
      }

      return (event, currentValue) => {
        // Check for event.detail due to an issue where IE11 handles other events as a CustomEvent.
        if (event instanceof CustomEvent && event.detail) {
          return event.detail;
        } else if (el.type === 'checkbox') {
          // If the data we are binding to is an array, toggle its value inside the array.
          if (Array.isArray(currentValue)) {
            const newValue = modifiers.includes('number') ? safeParseNumber(event.target.value) : event.target.value;
            return event.target.checked ? currentValue.concat([newValue]) : currentValue.filter(el => !checkedAttrLooseCompare(el, newValue));
          } else {
            return event.target.checked;
          }
        } else if (el.tagName.toLowerCase() === 'select' && el.multiple) {
          return modifiers.includes('number') ? Array.from(event.target.selectedOptions).map(option => {
            const rawValue = option.value || option.text;
            return safeParseNumber(rawValue);
          }) : Array.from(event.target.selectedOptions).map(option => {
            return option.value || option.text;
          });
        } else {
          const rawValue = event.target.value;
          return modifiers.includes('number') ? safeParseNumber(rawValue) : modifiers.includes('trim') ? rawValue.trim() : rawValue;
        }
      };
    }

    function safeParseNumber(rawValue) {
      const number = rawValue ? parseFloat(rawValue) : null;
      return isNumeric(number) ? number : rawValue;
    }

    /**
     * Copyright (C) 2017 salesforce.com, inc.
     */
    const { isArray } = Array;
    const { getPrototypeOf, create: ObjectCreate, defineProperty: ObjectDefineProperty, defineProperties: ObjectDefineProperties, isExtensible, getOwnPropertyDescriptor, getOwnPropertyNames, getOwnPropertySymbols, preventExtensions, hasOwnProperty, } = Object;
    const { push: ArrayPush, concat: ArrayConcat, map: ArrayMap, } = Array.prototype;
    function isUndefined(obj) {
        return obj === undefined;
    }
    function isFunction(obj) {
        return typeof obj === 'function';
    }
    function isObject(obj) {
        return typeof obj === 'object';
    }
    const proxyToValueMap = new WeakMap();
    function registerProxy(proxy, value) {
        proxyToValueMap.set(proxy, value);
    }
    const unwrap = (replicaOrAny) => proxyToValueMap.get(replicaOrAny) || replicaOrAny;

    function wrapValue(membrane, value) {
        return membrane.valueIsObservable(value) ? membrane.getProxy(value) : value;
    }
    /**
     * Unwrap property descriptors will set value on original descriptor
     * We only need to unwrap if value is specified
     * @param descriptor external descrpitor provided to define new property on original value
     */
    function unwrapDescriptor(descriptor) {
        if (hasOwnProperty.call(descriptor, 'value')) {
            descriptor.value = unwrap(descriptor.value);
        }
        return descriptor;
    }
    function lockShadowTarget(membrane, shadowTarget, originalTarget) {
        const targetKeys = ArrayConcat.call(getOwnPropertyNames(originalTarget), getOwnPropertySymbols(originalTarget));
        targetKeys.forEach((key) => {
            let descriptor = getOwnPropertyDescriptor(originalTarget, key);
            // We do not need to wrap the descriptor if configurable
            // Because we can deal with wrapping it when user goes through
            // Get own property descriptor. There is also a chance that this descriptor
            // could change sometime in the future, so we can defer wrapping
            // until we need to
            if (!descriptor.configurable) {
                descriptor = wrapDescriptor(membrane, descriptor, wrapValue);
            }
            ObjectDefineProperty(shadowTarget, key, descriptor);
        });
        preventExtensions(shadowTarget);
    }
    class ReactiveProxyHandler {
        constructor(membrane, value) {
            this.originalTarget = value;
            this.membrane = membrane;
        }
        get(shadowTarget, key) {
            const { originalTarget, membrane } = this;
            const value = originalTarget[key];
            const { valueObserved } = membrane;
            valueObserved(originalTarget, key);
            return membrane.getProxy(value);
        }
        set(shadowTarget, key, value) {
            const { originalTarget, membrane: { valueMutated } } = this;
            const oldValue = originalTarget[key];
            if (oldValue !== value) {
                originalTarget[key] = value;
                valueMutated(originalTarget, key);
            }
            else if (key === 'length' && isArray(originalTarget)) {
                // fix for issue #236: push will add the new index, and by the time length
                // is updated, the internal length is already equal to the new length value
                // therefore, the oldValue is equal to the value. This is the forking logic
                // to support this use case.
                valueMutated(originalTarget, key);
            }
            return true;
        }
        deleteProperty(shadowTarget, key) {
            const { originalTarget, membrane: { valueMutated } } = this;
            delete originalTarget[key];
            valueMutated(originalTarget, key);
            return true;
        }
        apply(shadowTarget, thisArg, argArray) {
            /* No op */
        }
        construct(target, argArray, newTarget) {
            /* No op */
        }
        has(shadowTarget, key) {
            const { originalTarget, membrane: { valueObserved } } = this;
            valueObserved(originalTarget, key);
            return key in originalTarget;
        }
        ownKeys(shadowTarget) {
            const { originalTarget } = this;
            return ArrayConcat.call(getOwnPropertyNames(originalTarget), getOwnPropertySymbols(originalTarget));
        }
        isExtensible(shadowTarget) {
            const shadowIsExtensible = isExtensible(shadowTarget);
            if (!shadowIsExtensible) {
                return shadowIsExtensible;
            }
            const { originalTarget, membrane } = this;
            const targetIsExtensible = isExtensible(originalTarget);
            if (!targetIsExtensible) {
                lockShadowTarget(membrane, shadowTarget, originalTarget);
            }
            return targetIsExtensible;
        }
        setPrototypeOf(shadowTarget, prototype) {
        }
        getPrototypeOf(shadowTarget) {
            const { originalTarget } = this;
            return getPrototypeOf(originalTarget);
        }
        getOwnPropertyDescriptor(shadowTarget, key) {
            const { originalTarget, membrane } = this;
            const { valueObserved } = this.membrane;
            // keys looked up via hasOwnProperty need to be reactive
            valueObserved(originalTarget, key);
            let desc = getOwnPropertyDescriptor(originalTarget, key);
            if (isUndefined(desc)) {
                return desc;
            }
            const shadowDescriptor = getOwnPropertyDescriptor(shadowTarget, key);
            if (!isUndefined(shadowDescriptor)) {
                return shadowDescriptor;
            }
            // Note: by accessing the descriptor, the key is marked as observed
            // but access to the value, setter or getter (if available) cannot observe
            // mutations, just like regular methods, in which case we just do nothing.
            desc = wrapDescriptor(membrane, desc, wrapValue);
            if (!desc.configurable) {
                // If descriptor from original target is not configurable,
                // We must copy the wrapped descriptor over to the shadow target.
                // Otherwise, proxy will throw an invariant error.
                // This is our last chance to lock the value.
                // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy/handler/getOwnPropertyDescriptor#Invariants
                ObjectDefineProperty(shadowTarget, key, desc);
            }
            return desc;
        }
        preventExtensions(shadowTarget) {
            const { originalTarget, membrane } = this;
            lockShadowTarget(membrane, shadowTarget, originalTarget);
            preventExtensions(originalTarget);
            return true;
        }
        defineProperty(shadowTarget, key, descriptor) {
            const { originalTarget, membrane } = this;
            const { valueMutated } = membrane;
            const { configurable } = descriptor;
            // We have to check for value in descriptor
            // because Object.freeze(proxy) calls this method
            // with only { configurable: false, writeable: false }
            // Additionally, method will only be called with writeable:false
            // if the descriptor has a value, as opposed to getter/setter
            // So we can just check if writable is present and then see if
            // value is present. This eliminates getter and setter descriptors
            if (hasOwnProperty.call(descriptor, 'writable') && !hasOwnProperty.call(descriptor, 'value')) {
                const originalDescriptor = getOwnPropertyDescriptor(originalTarget, key);
                descriptor.value = originalDescriptor.value;
            }
            ObjectDefineProperty(originalTarget, key, unwrapDescriptor(descriptor));
            if (configurable === false) {
                ObjectDefineProperty(shadowTarget, key, wrapDescriptor(membrane, descriptor, wrapValue));
            }
            valueMutated(originalTarget, key);
            return true;
        }
    }

    function wrapReadOnlyValue(membrane, value) {
        return membrane.valueIsObservable(value) ? membrane.getReadOnlyProxy(value) : value;
    }
    class ReadOnlyHandler {
        constructor(membrane, value) {
            this.originalTarget = value;
            this.membrane = membrane;
        }
        get(shadowTarget, key) {
            const { membrane, originalTarget } = this;
            const value = originalTarget[key];
            const { valueObserved } = membrane;
            valueObserved(originalTarget, key);
            return membrane.getReadOnlyProxy(value);
        }
        set(shadowTarget, key, value) {
            return false;
        }
        deleteProperty(shadowTarget, key) {
            return false;
        }
        apply(shadowTarget, thisArg, argArray) {
            /* No op */
        }
        construct(target, argArray, newTarget) {
            /* No op */
        }
        has(shadowTarget, key) {
            const { originalTarget, membrane: { valueObserved } } = this;
            valueObserved(originalTarget, key);
            return key in originalTarget;
        }
        ownKeys(shadowTarget) {
            const { originalTarget } = this;
            return ArrayConcat.call(getOwnPropertyNames(originalTarget), getOwnPropertySymbols(originalTarget));
        }
        setPrototypeOf(shadowTarget, prototype) {
        }
        getOwnPropertyDescriptor(shadowTarget, key) {
            const { originalTarget, membrane } = this;
            const { valueObserved } = membrane;
            // keys looked up via hasOwnProperty need to be reactive
            valueObserved(originalTarget, key);
            let desc = getOwnPropertyDescriptor(originalTarget, key);
            if (isUndefined(desc)) {
                return desc;
            }
            const shadowDescriptor = getOwnPropertyDescriptor(shadowTarget, key);
            if (!isUndefined(shadowDescriptor)) {
                return shadowDescriptor;
            }
            // Note: by accessing the descriptor, the key is marked as observed
            // but access to the value or getter (if available) cannot be observed,
            // just like regular methods, in which case we just do nothing.
            desc = wrapDescriptor(membrane, desc, wrapReadOnlyValue);
            if (hasOwnProperty.call(desc, 'set')) {
                desc.set = undefined; // readOnly membrane does not allow setters
            }
            if (!desc.configurable) {
                // If descriptor from original target is not configurable,
                // We must copy the wrapped descriptor over to the shadow target.
                // Otherwise, proxy will throw an invariant error.
                // This is our last chance to lock the value.
                // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy/handler/getOwnPropertyDescriptor#Invariants
                ObjectDefineProperty(shadowTarget, key, desc);
            }
            return desc;
        }
        preventExtensions(shadowTarget) {
            return false;
        }
        defineProperty(shadowTarget, key, descriptor) {
            return false;
        }
    }
    function createShadowTarget(value) {
        let shadowTarget = undefined;
        if (isArray(value)) {
            shadowTarget = [];
        }
        else if (isObject(value)) {
            shadowTarget = {};
        }
        return shadowTarget;
    }
    const ObjectDotPrototype = Object.prototype;
    function defaultValueIsObservable(value) {
        // intentionally checking for null
        if (value === null) {
            return false;
        }
        // treat all non-object types, including undefined, as non-observable values
        if (typeof value !== 'object') {
            return false;
        }
        if (isArray(value)) {
            return true;
        }
        const proto = getPrototypeOf(value);
        return (proto === ObjectDotPrototype || proto === null || getPrototypeOf(proto) === null);
    }
    const defaultValueObserved = (obj, key) => {
        /* do nothing */
    };
    const defaultValueMutated = (obj, key) => {
        /* do nothing */
    };
    const defaultValueDistortion = (value) => value;
    function wrapDescriptor(membrane, descriptor, getValue) {
        const { set, get } = descriptor;
        if (hasOwnProperty.call(descriptor, 'value')) {
            descriptor.value = getValue(membrane, descriptor.value);
        }
        else {
            if (!isUndefined(get)) {
                descriptor.get = function () {
                    // invoking the original getter with the original target
                    return getValue(membrane, get.call(unwrap(this)));
                };
            }
            if (!isUndefined(set)) {
                descriptor.set = function (value) {
                    // At this point we don't have a clear indication of whether
                    // or not a valid mutation will occur, we don't have the key,
                    // and we are not sure why and how they are invoking this setter.
                    // Nevertheless we preserve the original semantics by invoking the
                    // original setter with the original target and the unwrapped value
                    set.call(unwrap(this), membrane.unwrapProxy(value));
                };
            }
        }
        return descriptor;
    }
    class ReactiveMembrane {
        constructor(options) {
            this.valueDistortion = defaultValueDistortion;
            this.valueMutated = defaultValueMutated;
            this.valueObserved = defaultValueObserved;
            this.valueIsObservable = defaultValueIsObservable;
            this.objectGraph = new WeakMap();
            if (!isUndefined(options)) {
                const { valueDistortion, valueMutated, valueObserved, valueIsObservable } = options;
                this.valueDistortion = isFunction(valueDistortion) ? valueDistortion : defaultValueDistortion;
                this.valueMutated = isFunction(valueMutated) ? valueMutated : defaultValueMutated;
                this.valueObserved = isFunction(valueObserved) ? valueObserved : defaultValueObserved;
                this.valueIsObservable = isFunction(valueIsObservable) ? valueIsObservable : defaultValueIsObservable;
            }
        }
        getProxy(value) {
            const unwrappedValue = unwrap(value);
            const distorted = this.valueDistortion(unwrappedValue);
            if (this.valueIsObservable(distorted)) {
                const o = this.getReactiveState(unwrappedValue, distorted);
                // when trying to extract the writable version of a readonly
                // we return the readonly.
                return o.readOnly === value ? value : o.reactive;
            }
            return distorted;
        }
        getReadOnlyProxy(value) {
            value = unwrap(value);
            const distorted = this.valueDistortion(value);
            if (this.valueIsObservable(distorted)) {
                return this.getReactiveState(value, distorted).readOnly;
            }
            return distorted;
        }
        unwrapProxy(p) {
            return unwrap(p);
        }
        getReactiveState(value, distortedValue) {
            const { objectGraph, } = this;
            let reactiveState = objectGraph.get(distortedValue);
            if (reactiveState) {
                return reactiveState;
            }
            const membrane = this;
            reactiveState = {
                get reactive() {
                    const reactiveHandler = new ReactiveProxyHandler(membrane, distortedValue);
                    // caching the reactive proxy after the first time it is accessed
                    const proxy = new Proxy(createShadowTarget(distortedValue), reactiveHandler);
                    registerProxy(proxy, value);
                    ObjectDefineProperty(this, 'reactive', { value: proxy });
                    return proxy;
                },
                get readOnly() {
                    const readOnlyHandler = new ReadOnlyHandler(membrane, distortedValue);
                    // caching the readOnly proxy after the first time it is accessed
                    const proxy = new Proxy(createShadowTarget(distortedValue), readOnlyHandler);
                    registerProxy(proxy, value);
                    ObjectDefineProperty(this, 'readOnly', { value: proxy });
                    return proxy;
                }
            };
            objectGraph.set(distortedValue, reactiveState);
            return reactiveState;
        }
    }
    /** version: 0.26.0 */

    function wrap(data, mutationCallback) {

      let membrane = new ReactiveMembrane({
        valueMutated(target, key) {
          mutationCallback(target, key);
        }

      });
      return {
        data: membrane.getProxy(data),
        membrane: membrane
      };
    }
    function unwrap$1(membrane, observable) {
      let unwrappedData = membrane.unwrapProxy(observable);
      let copy = {};
      Object.keys(unwrappedData).forEach(key => {
        if (['$el', '$refs', '$nextTick', '$watch'].includes(key)) return;
        copy[key] = unwrappedData[key];
      });
      return copy;
    }

    class Component {
      constructor(el, componentForClone = null) {
        this.$el = el;
        const dataAttr = this.$el.getAttribute('x-data');
        const dataExpression = dataAttr === '' ? '{}' : dataAttr;
        const initExpression = this.$el.getAttribute('x-init');
        let dataExtras = {
          $el: this.$el
        };
        let canonicalComponentElementReference = componentForClone ? componentForClone.$el : this.$el;
        Object.entries(Alpine.magicProperties).forEach(([name, callback]) => {
          Object.defineProperty(dataExtras, `$${name}`, {
            get: function get() {
              return callback(canonicalComponentElementReference);
            }
          });
        });
        this.unobservedData = componentForClone ? componentForClone.getUnobservedData() : saferEval(el, dataExpression, dataExtras);
        // Construct a Proxy-based observable. This will be used to handle reactivity.

        let {
          membrane,
          data
        } = this.wrapDataInObservable(this.unobservedData);
        this.$data = data;
        this.membrane = membrane; // After making user-supplied data methods reactive, we can now add
        // our magic properties to the original data for access.

        this.unobservedData.$el = this.$el;
        this.unobservedData.$refs = this.getRefsProxy();
        this.nextTickStack = [];

        this.unobservedData.$nextTick = callback => {
          this.nextTickStack.push(callback);
        };

        this.watchers = {};

        this.unobservedData.$watch = (property, callback) => {
          if (!this.watchers[property]) this.watchers[property] = [];
          this.watchers[property].push(callback);
        };
        /* MODERN-ONLY:START */
        // We remove this piece of code from the legacy build.
        // In IE11, we have already defined our helpers at this point.
        // Register custom magic properties.


        Object.entries(Alpine.magicProperties).forEach(([name, callback]) => {
          Object.defineProperty(this.unobservedData, `$${name}`, {
            get: function get() {
              return callback(canonicalComponentElementReference, this.$el);
            }
          });
        });
        /* MODERN-ONLY:END */

        this.showDirectiveStack = [];
        this.showDirectiveLastElement;
        componentForClone || Alpine.onBeforeComponentInitializeds.forEach(callback => callback(this));
        var initReturnedCallback; // If x-init is present AND we aren't cloning (skip x-init on clone)

        if (initExpression && !componentForClone) {
          // We want to allow data manipulation, but not trigger DOM updates just yet.
          // We haven't even initialized the elements with their Alpine bindings. I mean c'mon.
          this.pauseReactivity = true;
          initReturnedCallback = this.evaluateReturnExpression(this.$el, initExpression);
          this.pauseReactivity = false;
        } // Register all our listeners and set all our attribute bindings.
        // If we're cloning a component, the third parameter ensures no duplicate
        // event listeners are registered (the mutation observer will take care of them)


        this.initializeElements(this.$el, () => {}, componentForClone); // Use mutation observer to detect new elements being added within this component at run-time.
        // Alpine's just so darn flexible amirite?

        this.listenForNewElementsToInitialize();

        if (typeof initReturnedCallback === 'function') {
          // Run the callback returned from the "x-init" hook to allow the user to do stuff after
          // Alpine's got it's grubby little paws all over everything.
          initReturnedCallback.call(this.$data);
        }

        componentForClone || setTimeout(() => {
          Alpine.onComponentInitializeds.forEach(callback => callback(this));
        }, 0);
      }

      getUnobservedData() {
        return unwrap$1(this.membrane, this.$data);
      }

      wrapDataInObservable(data) {
        var self = this;
        let updateDom = debounce(function () {
          self.updateElements(self.$el);
        }, 0);
        return wrap(data, (target, key) => {
          if (self.watchers[key]) {
            // If there's a watcher for this specific key, run it.
            self.watchers[key].forEach(callback => callback(target[key]));
          } else if (Array.isArray(target)) {
            // Arrays are special cases, if any of the items change, we consider the array as mutated.
            Object.keys(self.watchers).forEach(fullDotNotationKey => {
              let dotNotationParts = fullDotNotationKey.split('.'); // Ignore length mutations since they would result in duplicate calls.
              // For example, when calling push, we would get a mutation for the item's key
              // and a second mutation for the length property.

              if (key === 'length') return;
              dotNotationParts.reduce((comparisonData, part) => {
                if (Object.is(target, comparisonData[part])) {
                  self.watchers[fullDotNotationKey].forEach(callback => callback(target));
                }

                return comparisonData[part];
              }, self.unobservedData);
            });
          } else {
            // Let's walk through the watchers with "dot-notation" (foo.bar) and see
            // if this mutation fits any of them.
            Object.keys(self.watchers).filter(i => i.includes('.')).forEach(fullDotNotationKey => {
              let dotNotationParts = fullDotNotationKey.split('.'); // If this dot-notation watcher's last "part" doesn't match the current
              // key, then skip it early for performance reasons.

              if (key !== dotNotationParts[dotNotationParts.length - 1]) return; // Now, walk through the dot-notation "parts" recursively to find
              // a match, and call the watcher if one's found.

              dotNotationParts.reduce((comparisonData, part) => {
                if (Object.is(target, comparisonData)) {
                  // Run the watchers.
                  self.watchers[fullDotNotationKey].forEach(callback => callback(target[key]));
                }

                return comparisonData[part];
              }, self.unobservedData);
            });
          } // Don't react to data changes for cases like the `x-created` hook.


          if (self.pauseReactivity) return;
          updateDom();
        });
      }

      walkAndSkipNestedComponents(el, callback, initializeComponentCallback = () => {}) {
        walk(el, el => {
          // We've hit a component.
          if (el.hasAttribute('x-data')) {
            // If it's not the current one.
            if (!el.isSameNode(this.$el)) {
              // Initialize it if it's not.
              if (!el.__x) initializeComponentCallback(el); // Now we'll let that sub-component deal with itself.

              return false;
            }
          }

          return callback(el);
        });
      }

      initializeElements(rootEl, extraVars = () => {}, componentForClone = false) {
        this.walkAndSkipNestedComponents(rootEl, el => {
          // Don't touch spawns from for loop
          if (el.__x_for_key !== undefined) return false; // Don't touch spawns from if directives

          if (el.__x_inserted_me !== undefined) return false;
          this.initializeElement(el, extraVars, componentForClone ? false : true);
        }, el => {
          if (!componentForClone) el.__x = new Component(el);
        });
        this.executeAndClearRemainingShowDirectiveStack();
        this.executeAndClearNextTickStack(rootEl);
      }

      initializeElement(el, extraVars, shouldRegisterListeners = true) {
        // To support class attribute merging, we have to know what the element's
        // original class attribute looked like for reference.
        if (el.hasAttribute('class') && getXAttrs(el, this).length > 0) {
          el.__x_original_classes = convertClassStringToArray(el.getAttribute('class'));
        }

        shouldRegisterListeners && this.registerListeners(el, extraVars);
        this.resolveBoundAttributes(el, true, extraVars);
      }

      updateElements(rootEl, extraVars = () => {}) {
        this.walkAndSkipNestedComponents(rootEl, el => {
          // Don't touch spawns from for loop (and check if the root is actually a for loop in a parent, don't skip it.)
          if (el.__x_for_key !== undefined && !el.isSameNode(this.$el)) return false;
          this.updateElement(el, extraVars);
        }, el => {
          el.__x = new Component(el);
        });
        this.executeAndClearRemainingShowDirectiveStack();
        this.executeAndClearNextTickStack(rootEl);
      }

      executeAndClearNextTickStack(el) {
        // Skip spawns from alpine directives
        if (el === this.$el && this.nextTickStack.length > 0) {
          // We run the tick stack after the next frame to allow any
          // running transitions to pass the initial show stage.
          requestAnimationFrame(() => {
            while (this.nextTickStack.length > 0) {
              this.nextTickStack.shift()();
            }
          });
        }
      }

      executeAndClearRemainingShowDirectiveStack() {
        // The goal here is to start all the x-show transitions
        // and build a nested promise chain so that elements
        // only hide when the children are finished hiding.
        this.showDirectiveStack.reverse().map(handler => {
          return new Promise((resolve, reject) => {
            handler(resolve, reject);
          });
        }).reduce((promiseChain, promise) => {
          return promiseChain.then(() => {
            return promise.then(finishElement => {
              finishElement();
            });
          });
        }, Promise.resolve(() => {})).catch(e => {
          if (e !== TRANSITION_CANCELLED) throw e;
        }); // We've processed the handler stack. let's clear it.

        this.showDirectiveStack = [];
        this.showDirectiveLastElement = undefined;
      }

      updateElement(el, extraVars) {
        this.resolveBoundAttributes(el, false, extraVars);
      }

      registerListeners(el, extraVars) {
        getXAttrs(el, this).forEach(({
          type,
          value,
          modifiers,
          expression
        }) => {
          switch (type) {
            case 'on':
              registerListener(this, el, value, modifiers, expression, extraVars);
              break;

            case 'model':
              registerModelListener(this, el, modifiers, expression, extraVars);
              break;
          }
        });
      }

      resolveBoundAttributes(el, initialUpdate = false, extraVars) {
        let attrs = getXAttrs(el, this);
        attrs.forEach(({
          type,
          value,
          modifiers,
          expression
        }) => {
          switch (type) {
            case 'model':
              handleAttributeBindingDirective(this, el, 'value', expression, extraVars, type, modifiers);
              break;

            case 'bind':
              // The :key binding on an x-for is special, ignore it.
              if (el.tagName.toLowerCase() === 'template' && value === 'key') return;
              handleAttributeBindingDirective(this, el, value, expression, extraVars, type, modifiers);
              break;

            case 'text':
              var output = this.evaluateReturnExpression(el, expression, extraVars);
              handleTextDirective(el, output, expression);
              break;

            case 'html':
              handleHtmlDirective(this, el, expression, extraVars);
              break;

            case 'show':
              var output = this.evaluateReturnExpression(el, expression, extraVars);
              handleShowDirective(this, el, output, modifiers, initialUpdate);
              break;

            case 'if':
              // If this element also has x-for on it, don't process x-if.
              // We will let the "x-for" directive handle the "if"ing.
              if (attrs.some(i => i.type === 'for')) return;
              var output = this.evaluateReturnExpression(el, expression, extraVars);
              handleIfDirective(this, el, output, initialUpdate, extraVars);
              break;

            case 'for':
              handleForDirective(this, el, expression, initialUpdate, extraVars);
              break;

            case 'cloak':
              el.removeAttribute('x-cloak');
              break;
          }
        });
      }

      evaluateReturnExpression(el, expression, extraVars = () => {}) {
        return saferEval(el, expression, this.$data, _objectSpread2(_objectSpread2({}, extraVars()), {}, {
          $dispatch: this.getDispatchFunction(el)
        }));
      }

      evaluateCommandExpression(el, expression, extraVars = () => {}) {
        return saferEvalNoReturn(el, expression, this.$data, _objectSpread2(_objectSpread2({}, extraVars()), {}, {
          $dispatch: this.getDispatchFunction(el)
        }));
      }

      getDispatchFunction(el) {
        return (event, detail = {}) => {
          el.dispatchEvent(new CustomEvent(event, {
            detail,
            bubbles: true
          }));
        };
      }

      listenForNewElementsToInitialize() {
        const targetNode = this.$el;
        const observerOptions = {
          childList: true,
          attributes: true,
          subtree: true
        };
        const observer = new MutationObserver(mutations => {
          for (let i = 0; i < mutations.length; i++) {
            // Filter out mutations triggered from child components.
            const closestParentComponent = mutations[i].target.closest('[x-data]');
            if (!(closestParentComponent && closestParentComponent.isSameNode(this.$el))) continue;

            if (mutations[i].type === 'attributes' && mutations[i].attributeName === 'x-data') {
              const xAttr = mutations[i].target.getAttribute('x-data') || '{}';
              const rawData = saferEval(this.$el, xAttr, {
                $el: this.$el
              });
              Object.keys(rawData).forEach(key => {
                if (this.$data[key] !== rawData[key]) {
                  this.$data[key] = rawData[key];
                }
              });
            }

            if (mutations[i].addedNodes.length > 0) {
              mutations[i].addedNodes.forEach(node => {
                if (node.nodeType !== 1 || node.__x_inserted_me) return;

                if (node.matches('[x-data]') && !node.__x) {
                  node.__x = new Component(node);
                  return;
                }

                this.initializeElements(node);
              });
            }
          }
        });
        observer.observe(targetNode, observerOptions);
      }

      getRefsProxy() {
        var self = this;
        var refObj = {};
        // One of the goals of this is to not hold elements in memory, but rather re-evaluate
        // the DOM when the system needs something from it. This way, the framework is flexible and
        // friendly to outside DOM changes from libraries like Vue/Livewire.
        // For this reason, I'm using an "on-demand" proxy to fake a "$refs" object.

        return new Proxy(refObj, {
          get(object, property) {
            if (property === '$isAlpineProxy') return true;
            var ref; // We can't just query the DOM because it's hard to filter out refs in
            // nested components.

            self.walkAndSkipNestedComponents(self.$el, el => {
              if (el.hasAttribute('x-ref') && el.getAttribute('x-ref') === property) {
                ref = el;
              }
            });
            return ref;
          }

        });
      }

    }

    const Alpine = {
      version: "2.8.1",
      pauseMutationObserver: false,
      magicProperties: {},
      onComponentInitializeds: [],
      onBeforeComponentInitializeds: [],
      ignoreFocusedForValueBinding: false,
      start: async function start() {
        if (!isTesting()) {
          await domReady();
        }

        this.discoverComponents(el => {
          this.initializeComponent(el);
        }); // It's easier and more performant to just support Turbolinks than listen
        // to MutationObserver mutations at the document level.

        document.addEventListener("turbolinks:load", () => {
          this.discoverUninitializedComponents(el => {
            this.initializeComponent(el);
          });
        });
        this.listenForNewUninitializedComponentsAtRunTime();
      },
      discoverComponents: function discoverComponents(callback) {
        const rootEls = document.querySelectorAll('[x-data]');
        rootEls.forEach(rootEl => {
          callback(rootEl);
        });
      },
      discoverUninitializedComponents: function discoverUninitializedComponents(callback, el = null) {
        const rootEls = (el || document).querySelectorAll('[x-data]');
        Array.from(rootEls).filter(el => el.__x === undefined).forEach(rootEl => {
          callback(rootEl);
        });
      },
      listenForNewUninitializedComponentsAtRunTime: function listenForNewUninitializedComponentsAtRunTime() {
        const targetNode = document.querySelector('body');
        const observerOptions = {
          childList: true,
          attributes: true,
          subtree: true
        };
        const observer = new MutationObserver(mutations => {
          if (this.pauseMutationObserver) return;

          for (let i = 0; i < mutations.length; i++) {
            if (mutations[i].addedNodes.length > 0) {
              mutations[i].addedNodes.forEach(node => {
                // Discard non-element nodes (like line-breaks)
                if (node.nodeType !== 1) return; // Discard any changes happening within an existing component.
                // They will take care of themselves.

                if (node.parentElement && node.parentElement.closest('[x-data]')) return;
                this.discoverUninitializedComponents(el => {
                  this.initializeComponent(el);
                }, node.parentElement);
              });
            }
          }
        });
        observer.observe(targetNode, observerOptions);
      },
      initializeComponent: function initializeComponent(el) {
        if (!el.__x) {
          // Wrap in a try/catch so that we don't prevent other components
          // from initializing when one component contains an error.
          try {
            el.__x = new Component(el);
          } catch (error) {
            setTimeout(() => {
              throw error;
            }, 0);
          }
        }
      },
      clone: function clone(component, newEl) {
        if (!newEl.__x) {
          newEl.__x = new Component(newEl, component);
        }
      },
      addMagicProperty: function addMagicProperty(name, callback) {
        this.magicProperties[name] = callback;
      },
      onComponentInitialized: function onComponentInitialized(callback) {
        this.onComponentInitializeds.push(callback);
      },
      onBeforeComponentInitialized: function onBeforeComponentInitialized(callback) {
        this.onBeforeComponentInitializeds.push(callback);
      }
    };

    if (!isTesting()) {
      window.Alpine = Alpine;

      if (window.deferLoadingAlpine) {
        window.deferLoadingAlpine(function () {
          window.Alpine.start();
        });
      } else {
        window.Alpine.start();
      }
    }

    return Alpine;

  })));
