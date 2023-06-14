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

// node_modules/trix/dist/trix.js
var require_trix = __commonJS({
  "node_modules/trix/dist/trix.js"(exports, module) {
    (function() {
    }).call(exports), function() {
      var t;
      null == window.Set && (window.Set = t = function() {
        function t2() {
          this.clear();
        }
        return t2.prototype.clear = function() {
          return this.values = [];
        }, t2.prototype.has = function(t3) {
          return -1 !== this.values.indexOf(t3);
        }, t2.prototype.add = function(t3) {
          return this.has(t3) || this.values.push(t3), this;
        }, t2.prototype["delete"] = function(t3) {
          var e;
          return -1 === (e = this.values.indexOf(t3)) ? false : (this.values.splice(e, 1), true);
        }, t2.prototype.forEach = function() {
          var t3;
          return (t3 = this.values).forEach.apply(t3, arguments);
        }, t2;
      }());
    }.call(exports), function(t) {
      function e() {
      }
      function n(t2, e2) {
        return function() {
          t2.apply(e2, arguments);
        };
      }
      function i(t2) {
        if ("object" != typeof this)
          throw new TypeError("Promises must be constructed via new");
        if ("function" != typeof t2)
          throw new TypeError("not a function");
        this._state = 0, this._handled = false, this._value = void 0, this._deferreds = [], c(t2, this);
      }
      function o(t2, e2) {
        for (; 3 === t2._state; )
          t2 = t2._value;
        return 0 === t2._state ? void t2._deferreds.push(e2) : (t2._handled = true, void h(function() {
          var n2 = 1 === t2._state ? e2.onFulfilled : e2.onRejected;
          if (null === n2)
            return void (1 === t2._state ? r : s)(e2.promise, t2._value);
          var i2;
          try {
            i2 = n2(t2._value);
          } catch (o2) {
            return void s(e2.promise, o2);
          }
          r(e2.promise, i2);
        }));
      }
      function r(t2, e2) {
        try {
          if (e2 === t2)
            throw new TypeError("A promise cannot be resolved with itself.");
          if (e2 && ("object" == typeof e2 || "function" == typeof e2)) {
            var o2 = e2.then;
            if (e2 instanceof i)
              return t2._state = 3, t2._value = e2, void a(t2);
            if ("function" == typeof o2)
              return void c(n(o2, e2), t2);
          }
          t2._state = 1, t2._value = e2, a(t2);
        } catch (r2) {
          s(t2, r2);
        }
      }
      function s(t2, e2) {
        t2._state = 2, t2._value = e2, a(t2);
      }
      function a(t2) {
        2 === t2._state && 0 === t2._deferreds.length && setTimeout(function() {
          t2._handled || p(t2._value);
        }, 1);
        for (var e2 = 0, n2 = t2._deferreds.length; n2 > e2; e2++)
          o(t2, t2._deferreds[e2]);
        t2._deferreds = null;
      }
      function u(t2, e2, n2) {
        this.onFulfilled = "function" == typeof t2 ? t2 : null, this.onRejected = "function" == typeof e2 ? e2 : null, this.promise = n2;
      }
      function c(t2, e2) {
        var n2 = false;
        try {
          t2(function(t3) {
            n2 || (n2 = true, r(e2, t3));
          }, function(t3) {
            n2 || (n2 = true, s(e2, t3));
          });
        } catch (i2) {
          if (n2)
            return;
          n2 = true, s(e2, i2);
        }
      }
      var l = setTimeout, h = "function" == typeof setImmediate && setImmediate || function(t2) {
        l(t2, 1);
      }, p = function(t2) {
        "undefined" != typeof console && console && console.warn("Possible Unhandled Promise Rejection:", t2);
      };
      i.prototype["catch"] = function(t2) {
        return this.then(null, t2);
      }, i.prototype.then = function(t2, n2) {
        var r2 = new i(e);
        return o(this, new u(t2, n2, r2)), r2;
      }, i.all = function(t2) {
        var e2 = Array.prototype.slice.call(t2);
        return new i(function(t3, n2) {
          function i2(r3, s2) {
            try {
              if (s2 && ("object" == typeof s2 || "function" == typeof s2)) {
                var a2 = s2.then;
                if ("function" == typeof a2)
                  return void a2.call(s2, function(t4) {
                    i2(r3, t4);
                  }, n2);
              }
              e2[r3] = s2, 0 === --o2 && t3(e2);
            } catch (u2) {
              n2(u2);
            }
          }
          if (0 === e2.length)
            return t3([]);
          for (var o2 = e2.length, r2 = 0; r2 < e2.length; r2++)
            i2(r2, e2[r2]);
        });
      }, i.resolve = function(t2) {
        return t2 && "object" == typeof t2 && t2.constructor === i ? t2 : new i(function(e2) {
          e2(t2);
        });
      }, i.reject = function(t2) {
        return new i(function(e2, n2) {
          n2(t2);
        });
      }, i.race = function(t2) {
        return new i(function(e2, n2) {
          for (var i2 = 0, o2 = t2.length; o2 > i2; i2++)
            t2[i2].then(e2, n2);
        });
      }, i._setImmediateFn = function(t2) {
        h = t2;
      }, i._setUnhandledRejectionFn = function(t2) {
        p = t2;
      }, "undefined" != typeof module && module.exports ? module.exports = i : t.Promise || (t.Promise = i);
    }(exports), function() {
      var t = "object" == typeof window.customElements, e = "function" == typeof document.registerElement, n = t || e;
      n || /**
      * @license
      * Copyright (c) 2014 The Polymer Project Authors. All rights reserved.
      * This code may only be used under the BSD style license found at http://polymer.github.io/LICENSE.txt
      * The complete set of authors may be found at http://polymer.github.io/AUTHORS.txt
      * The complete set of contributors may be found at http://polymer.github.io/CONTRIBUTORS.txt
      * Code distributed by Google as part of the polymer project is also
      * subject to an additional IP rights grant found at http://polymer.github.io/PATENTS.txt
      */
      ("undefined" == typeof WeakMap && !function() {
        var t2 = Object.defineProperty, e2 = Date.now() % 1e9, n2 = function() {
          this.name = "__st" + (1e9 * Math.random() >>> 0) + (e2++ + "__");
        };
        n2.prototype = { set: function(e3, n3) {
          var i = e3[this.name];
          return i && i[0] === e3 ? i[1] = n3 : t2(e3, this.name, { value: [e3, n3], writable: true }), this;
        }, get: function(t3) {
          var e3;
          return (e3 = t3[this.name]) && e3[0] === t3 ? e3[1] : void 0;
        }, "delete": function(t3) {
          var e3 = t3[this.name];
          return e3 && e3[0] === t3 ? (e3[0] = e3[1] = void 0, true) : false;
        }, has: function(t3) {
          var e3 = t3[this.name];
          return e3 ? e3[0] === t3 : false;
        } }, window.WeakMap = n2;
      }(), function(t2) {
        function e2(t3) {
          A.push(t3), b || (b = true, g(i));
        }
        function n2(t3) {
          return window.ShadowDOMPolyfill && window.ShadowDOMPolyfill.wrapIfNeeded(t3) || t3;
        }
        function i() {
          b = false;
          var t3 = A;
          A = [], t3.sort(function(t4, e4) {
            return t4.uid_ - e4.uid_;
          });
          var e3 = false;
          t3.forEach(function(t4) {
            var n3 = t4.takeRecords();
            o(t4), n3.length && (t4.callback_(n3, t4), e3 = true);
          }), e3 && i();
        }
        function o(t3) {
          t3.nodes_.forEach(function(e3) {
            var n3 = m.get(e3);
            n3 && n3.forEach(function(e4) {
              e4.observer === t3 && e4.removeTransientObservers();
            });
          });
        }
        function r(t3, e3) {
          for (var n3 = t3; n3; n3 = n3.parentNode) {
            var i2 = m.get(n3);
            if (i2)
              for (var o2 = 0; o2 < i2.length; o2++) {
                var r2 = i2[o2], s2 = r2.options;
                if (n3 === t3 || s2.subtree) {
                  var a2 = e3(s2);
                  a2 && r2.enqueue(a2);
                }
              }
          }
        }
        function s(t3) {
          this.callback_ = t3, this.nodes_ = [], this.records_ = [], this.uid_ = ++C;
        }
        function a(t3, e3) {
          this.type = t3, this.target = e3, this.addedNodes = [], this.removedNodes = [], this.previousSibling = null, this.nextSibling = null, this.attributeName = null, this.attributeNamespace = null, this.oldValue = null;
        }
        function u(t3) {
          var e3 = new a(t3.type, t3.target);
          return e3.addedNodes = t3.addedNodes.slice(), e3.removedNodes = t3.removedNodes.slice(), e3.previousSibling = t3.previousSibling, e3.nextSibling = t3.nextSibling, e3.attributeName = t3.attributeName, e3.attributeNamespace = t3.attributeNamespace, e3.oldValue = t3.oldValue, e3;
        }
        function c(t3, e3) {
          return x = new a(t3, e3);
        }
        function l(t3) {
          return w ? w : (w = u(x), w.oldValue = t3, w);
        }
        function h() {
          x = w = void 0;
        }
        function p(t3) {
          return t3 === w || t3 === x;
        }
        function d(t3, e3) {
          return t3 === e3 ? t3 : w && p(t3) ? w : null;
        }
        function f(t3, e3, n3) {
          this.observer = t3, this.target = e3, this.options = n3, this.transientObservedNodes = [];
        }
        if (!t2.JsMutationObserver) {
          var g, m = /* @__PURE__ */ new WeakMap();
          if (/Trident|Edge/.test(navigator.userAgent))
            g = setTimeout;
          else if (window.setImmediate)
            g = window.setImmediate;
          else {
            var v = [], y = String(Math.random());
            window.addEventListener("message", function(t3) {
              if (t3.data === y) {
                var e3 = v;
                v = [], e3.forEach(function(t4) {
                  t4();
                });
              }
            }), g = function(t3) {
              v.push(t3), window.postMessage(y, "*");
            };
          }
          var b = false, A = [], C = 0;
          s.prototype = { observe: function(t3, e3) {
            if (t3 = n2(t3), !e3.childList && !e3.attributes && !e3.characterData || e3.attributeOldValue && !e3.attributes || e3.attributeFilter && e3.attributeFilter.length && !e3.attributes || e3.characterDataOldValue && !e3.characterData)
              throw new SyntaxError();
            var i2 = m.get(t3);
            i2 || m.set(t3, i2 = []);
            for (var o2, r2 = 0; r2 < i2.length; r2++)
              if (i2[r2].observer === this) {
                o2 = i2[r2], o2.removeListeners(), o2.options = e3;
                break;
              }
            o2 || (o2 = new f(this, t3, e3), i2.push(o2), this.nodes_.push(t3)), o2.addListeners();
          }, disconnect: function() {
            this.nodes_.forEach(function(t3) {
              for (var e3 = m.get(t3), n3 = 0; n3 < e3.length; n3++) {
                var i2 = e3[n3];
                if (i2.observer === this) {
                  i2.removeListeners(), e3.splice(n3, 1);
                  break;
                }
              }
            }, this), this.records_ = [];
          }, takeRecords: function() {
            var t3 = this.records_;
            return this.records_ = [], t3;
          } };
          var x, w;
          f.prototype = { enqueue: function(t3) {
            var n3 = this.observer.records_, i2 = n3.length;
            if (n3.length > 0) {
              var o2 = n3[i2 - 1], r2 = d(o2, t3);
              if (r2)
                return void (n3[i2 - 1] = r2);
            } else
              e2(this.observer);
            n3[i2] = t3;
          }, addListeners: function() {
            this.addListeners_(this.target);
          }, addListeners_: function(t3) {
            var e3 = this.options;
            e3.attributes && t3.addEventListener("DOMAttrModified", this, true), e3.characterData && t3.addEventListener("DOMCharacterDataModified", this, true), e3.childList && t3.addEventListener("DOMNodeInserted", this, true), (e3.childList || e3.subtree) && t3.addEventListener("DOMNodeRemoved", this, true);
          }, removeListeners: function() {
            this.removeListeners_(this.target);
          }, removeListeners_: function(t3) {
            var e3 = this.options;
            e3.attributes && t3.removeEventListener("DOMAttrModified", this, true), e3.characterData && t3.removeEventListener("DOMCharacterDataModified", this, true), e3.childList && t3.removeEventListener("DOMNodeInserted", this, true), (e3.childList || e3.subtree) && t3.removeEventListener("DOMNodeRemoved", this, true);
          }, addTransientObserver: function(t3) {
            if (t3 !== this.target) {
              this.addListeners_(t3), this.transientObservedNodes.push(t3);
              var e3 = m.get(t3);
              e3 || m.set(t3, e3 = []), e3.push(this);
            }
          }, removeTransientObservers: function() {
            var t3 = this.transientObservedNodes;
            this.transientObservedNodes = [], t3.forEach(function(t4) {
              this.removeListeners_(t4);
              for (var e3 = m.get(t4), n3 = 0; n3 < e3.length; n3++)
                if (e3[n3] === this) {
                  e3.splice(n3, 1);
                  break;
                }
            }, this);
          }, handleEvent: function(t3) {
            switch (t3.stopImmediatePropagation(), t3.type) {
              case "DOMAttrModified":
                var e3 = t3.attrName, n3 = t3.relatedNode.namespaceURI, i2 = t3.target, o2 = new c("attributes", i2);
                o2.attributeName = e3, o2.attributeNamespace = n3;
                var s2 = t3.attrChange === MutationEvent.ADDITION ? null : t3.prevValue;
                r(i2, function(t4) {
                  return !t4.attributes || t4.attributeFilter && t4.attributeFilter.length && -1 === t4.attributeFilter.indexOf(e3) && -1 === t4.attributeFilter.indexOf(n3) ? void 0 : t4.attributeOldValue ? l(s2) : o2;
                });
                break;
              case "DOMCharacterDataModified":
                var i2 = t3.target, o2 = c("characterData", i2), s2 = t3.prevValue;
                r(i2, function(t4) {
                  return t4.characterData ? t4.characterDataOldValue ? l(s2) : o2 : void 0;
                });
                break;
              case "DOMNodeRemoved":
                this.addTransientObserver(t3.target);
              case "DOMNodeInserted":
                var a2, u2, p2 = t3.target;
                "DOMNodeInserted" === t3.type ? (a2 = [p2], u2 = []) : (a2 = [], u2 = [p2]);
                var d2 = p2.previousSibling, f2 = p2.nextSibling, o2 = c("childList", t3.target.parentNode);
                o2.addedNodes = a2, o2.removedNodes = u2, o2.previousSibling = d2, o2.nextSibling = f2, r(t3.relatedNode, function(t4) {
                  return t4.childList ? o2 : void 0;
                });
            }
            h();
          } }, t2.JsMutationObserver = s, t2.MutationObserver || (t2.MutationObserver = s, s._isPolyfilled = true);
        }
      }(self), function() {
        "use strict";
        if (!window.performance || !window.performance.now) {
          var t2 = Date.now();
          window.performance = { now: function() {
            return Date.now() - t2;
          } };
        }
        window.requestAnimationFrame || (window.requestAnimationFrame = function() {
          var t3 = window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame;
          return t3 ? function(e3) {
            return t3(function() {
              e3(performance.now());
            });
          } : function(t4) {
            return window.setTimeout(t4, 1e3 / 60);
          };
        }()), window.cancelAnimationFrame || (window.cancelAnimationFrame = function() {
          return window.webkitCancelAnimationFrame || window.mozCancelAnimationFrame || function(t3) {
            clearTimeout(t3);
          };
        }());
        var e2 = function() {
          var t3 = document.createEvent("Event");
          return t3.initEvent("foo", true, true), t3.preventDefault(), t3.defaultPrevented;
        }();
        if (!e2) {
          var n2 = Event.prototype.preventDefault;
          Event.prototype.preventDefault = function() {
            this.cancelable && (n2.call(this), Object.defineProperty(this, "defaultPrevented", { get: function() {
              return true;
            }, configurable: true }));
          };
        }
        var i = /Trident/.test(navigator.userAgent);
        if ((!window.CustomEvent || i && "function" != typeof window.CustomEvent) && (window.CustomEvent = function(t3, e3) {
          e3 = e3 || {};
          var n3 = document.createEvent("CustomEvent");
          return n3.initCustomEvent(t3, Boolean(e3.bubbles), Boolean(e3.cancelable), e3.detail), n3;
        }, window.CustomEvent.prototype = window.Event.prototype), !window.Event || i && "function" != typeof window.Event) {
          var o = window.Event;
          window.Event = function(t3, e3) {
            e3 = e3 || {};
            var n3 = document.createEvent("Event");
            return n3.initEvent(t3, Boolean(e3.bubbles), Boolean(e3.cancelable)), n3;
          }, window.Event.prototype = o.prototype;
        }
      }(window.WebComponents), window.CustomElements = window.CustomElements || { flags: {} }, function(t2) {
        var e2 = t2.flags, n2 = [], i = function(t3) {
          n2.push(t3);
        }, o = function() {
          n2.forEach(function(e3) {
            e3(t2);
          });
        };
        t2.addModule = i, t2.initializeModules = o, t2.hasNative = Boolean(document.registerElement), t2.isIE = /Trident/.test(navigator.userAgent), t2.useNative = !e2.register && t2.hasNative && !window.ShadowDOMPolyfill && (!window.HTMLImports || window.HTMLImports.useNative);
      }(window.CustomElements), window.CustomElements.addModule(function(t2) {
        function e2(t3, e3) {
          n2(t3, function(t4) {
            return e3(t4) ? true : void i(t4, e3);
          }), i(t3, e3);
        }
        function n2(t3, e3, i2) {
          var o2 = t3.firstElementChild;
          if (!o2)
            for (o2 = t3.firstChild; o2 && o2.nodeType !== Node.ELEMENT_NODE; )
              o2 = o2.nextSibling;
          for (; o2; )
            e3(o2, i2) !== true && n2(o2, e3, i2), o2 = o2.nextElementSibling;
          return null;
        }
        function i(t3, n3) {
          for (var i2 = t3.shadowRoot; i2; )
            e2(i2, n3), i2 = i2.olderShadowRoot;
        }
        function o(t3, e3) {
          r(t3, e3, []);
        }
        function r(t3, e3, n3) {
          if (t3 = window.wrap(t3), !(n3.indexOf(t3) >= 0)) {
            n3.push(t3);
            for (var i2, o2 = t3.querySelectorAll("link[rel=" + s + "]"), a = 0, u = o2.length; u > a && (i2 = o2[a]); a++)
              i2.import && r(i2.import, e3, n3);
            e3(t3);
          }
        }
        var s = window.HTMLImports ? window.HTMLImports.IMPORT_LINK_TYPE : "none";
        t2.forDocumentTree = o, t2.forSubtree = e2;
      }), window.CustomElements.addModule(function(t2) {
        function e2(t3, e3) {
          return n2(t3, e3) || i(t3, e3);
        }
        function n2(e3, n3) {
          return t2.upgrade(e3, n3) ? true : void (n3 && s(e3));
        }
        function i(t3, e3) {
          b(t3, function(t4) {
            return n2(t4, e3) ? true : void 0;
          });
        }
        function o(t3) {
          w.push(t3), x || (x = true, setTimeout(r));
        }
        function r() {
          x = false;
          for (var t3, e3 = w, n3 = 0, i2 = e3.length; i2 > n3 && (t3 = e3[n3]); n3++)
            t3();
          w = [];
        }
        function s(t3) {
          C ? o(function() {
            a(t3);
          }) : a(t3);
        }
        function a(t3) {
          t3.__upgraded__ && !t3.__attached && (t3.__attached = true, t3.attachedCallback && t3.attachedCallback());
        }
        function u(t3) {
          c(t3), b(t3, function(t4) {
            c(t4);
          });
        }
        function c(t3) {
          C ? o(function() {
            l(t3);
          }) : l(t3);
        }
        function l(t3) {
          t3.__upgraded__ && t3.__attached && (t3.__attached = false, t3.detachedCallback && t3.detachedCallback());
        }
        function h(t3) {
          for (var e3 = t3, n3 = window.wrap(document); e3; ) {
            if (e3 == n3)
              return true;
            e3 = e3.parentNode || e3.nodeType === Node.DOCUMENT_FRAGMENT_NODE && e3.host;
          }
        }
        function p(t3) {
          if (t3.shadowRoot && !t3.shadowRoot.__watched) {
            y.dom && console.log("watching shadow-root for: ", t3.localName);
            for (var e3 = t3.shadowRoot; e3; )
              g(e3), e3 = e3.olderShadowRoot;
          }
        }
        function d(t3, n3) {
          if (y.dom) {
            var i2 = n3[0];
            if (i2 && "childList" === i2.type && i2.addedNodes && i2.addedNodes) {
              for (var o2 = i2.addedNodes[0]; o2 && o2 !== document && !o2.host; )
                o2 = o2.parentNode;
              var r2 = o2 && (o2.URL || o2._URL || o2.host && o2.host.localName) || "";
              r2 = r2.split("/?").shift().split("/").pop();
            }
            console.group("mutations (%d) [%s]", n3.length, r2 || "");
          }
          var s2 = h(t3);
          n3.forEach(function(t4) {
            "childList" === t4.type && (E(t4.addedNodes, function(t5) {
              t5.localName && e2(t5, s2);
            }), E(t4.removedNodes, function(t5) {
              t5.localName && u(t5);
            }));
          }), y.dom && console.groupEnd();
        }
        function f(t3) {
          for (t3 = window.wrap(t3), t3 || (t3 = window.wrap(document)); t3.parentNode; )
            t3 = t3.parentNode;
          var e3 = t3.__observer;
          e3 && (d(t3, e3.takeRecords()), r());
        }
        function g(t3) {
          if (!t3.__observer) {
            var e3 = new MutationObserver(d.bind(this, t3));
            e3.observe(t3, { childList: true, subtree: true }), t3.__observer = e3;
          }
        }
        function m(t3) {
          t3 = window.wrap(t3), y.dom && console.group("upgradeDocument: ", t3.baseURI.split("/").pop());
          var n3 = t3 === window.wrap(document);
          e2(t3, n3), g(t3), y.dom && console.groupEnd();
        }
        function v(t3) {
          A(t3, m);
        }
        var y = t2.flags, b = t2.forSubtree, A = t2.forDocumentTree, C = window.MutationObserver._isPolyfilled && y["throttle-attached"];
        t2.hasPolyfillMutations = C, t2.hasThrottledAttached = C;
        var x = false, w = [], E = Array.prototype.forEach.call.bind(Array.prototype.forEach), S = Element.prototype.createShadowRoot;
        S && (Element.prototype.createShadowRoot = function() {
          var t3 = S.call(this);
          return window.CustomElements.watchShadow(this), t3;
        }), t2.watchShadow = p, t2.upgradeDocumentTree = v, t2.upgradeDocument = m, t2.upgradeSubtree = i, t2.upgradeAll = e2, t2.attached = s, t2.takeRecords = f;
      }), window.CustomElements.addModule(function(t2) {
        function e2(e3, i2) {
          if ("template" === e3.localName && window.HTMLTemplateElement && HTMLTemplateElement.decorate && HTMLTemplateElement.decorate(e3), !e3.__upgraded__ && e3.nodeType === Node.ELEMENT_NODE) {
            var o2 = e3.getAttribute("is"), r2 = t2.getRegisteredDefinition(e3.localName) || t2.getRegisteredDefinition(o2);
            if (r2 && (o2 && r2.tag == e3.localName || !o2 && !r2.extends))
              return n2(e3, r2, i2);
          }
        }
        function n2(e3, n3, o2) {
          return s.upgrade && console.group("upgrade:", e3.localName), n3.is && e3.setAttribute("is", n3.is), i(e3, n3), e3.__upgraded__ = true, r(e3), o2 && t2.attached(e3), t2.upgradeSubtree(e3, o2), s.upgrade && console.groupEnd(), e3;
        }
        function i(t3, e3) {
          Object.__proto__ ? t3.__proto__ = e3.prototype : (o(t3, e3.prototype, e3.native), t3.__proto__ = e3.prototype);
        }
        function o(t3, e3, n3) {
          for (var i2 = {}, o2 = e3; o2 !== n3 && o2 !== HTMLElement.prototype; ) {
            for (var r2, s2 = Object.getOwnPropertyNames(o2), a = 0; r2 = s2[a]; a++)
              i2[r2] || (Object.defineProperty(t3, r2, Object.getOwnPropertyDescriptor(o2, r2)), i2[r2] = 1);
            o2 = Object.getPrototypeOf(o2);
          }
        }
        function r(t3) {
          t3.createdCallback && t3.createdCallback();
        }
        var s = t2.flags;
        t2.upgrade = e2, t2.upgradeWithDefinition = n2, t2.implementPrototype = i;
      }), window.CustomElements.addModule(function(t2) {
        function e2(e3, i2) {
          var u2 = i2 || {};
          if (!e3)
            throw new Error("document.registerElement: first argument `name` must not be empty");
          if (e3.indexOf("-") < 0)
            throw new Error("document.registerElement: first argument ('name') must contain a dash ('-'). Argument provided was '" + String(e3) + "'.");
          if (o(e3))
            throw new Error("Failed to execute 'registerElement' on 'Document': Registration failed for type '" + String(e3) + "'. The type name is invalid.");
          if (c(e3))
            throw new Error("DuplicateDefinitionError: a type with name '" + String(e3) + "' is already registered");
          return u2.prototype || (u2.prototype = Object.create(HTMLElement.prototype)), u2.__name = e3.toLowerCase(), u2.extends && (u2.extends = u2.extends.toLowerCase()), u2.lifecycle = u2.lifecycle || {}, u2.ancestry = r(u2.extends), s(u2), a(u2), n2(u2.prototype), l(u2.__name, u2), u2.ctor = h(u2), u2.ctor.prototype = u2.prototype, u2.prototype.constructor = u2.ctor, t2.ready && m(document), u2.ctor;
        }
        function n2(t3) {
          if (!t3.setAttribute._polyfilled) {
            var e3 = t3.setAttribute;
            t3.setAttribute = function(t4, n4) {
              i.call(this, t4, n4, e3);
            };
            var n3 = t3.removeAttribute;
            t3.removeAttribute = function(t4) {
              i.call(this, t4, null, n3);
            }, t3.setAttribute._polyfilled = true;
          }
        }
        function i(t3, e3, n3) {
          t3 = t3.toLowerCase();
          var i2 = this.getAttribute(t3);
          n3.apply(this, arguments);
          var o2 = this.getAttribute(t3);
          this.attributeChangedCallback && o2 !== i2 && this.attributeChangedCallback(t3, i2, o2);
        }
        function o(t3) {
          for (var e3 = 0; e3 < C.length; e3++)
            if (t3 === C[e3])
              return true;
        }
        function r(t3) {
          var e3 = c(t3);
          return e3 ? r(e3.extends).concat([e3]) : [];
        }
        function s(t3) {
          for (var e3, n3 = t3.extends, i2 = 0; e3 = t3.ancestry[i2]; i2++)
            n3 = e3.is && e3.tag;
          t3.tag = n3 || t3.__name, n3 && (t3.is = t3.__name);
        }
        function a(t3) {
          if (!Object.__proto__) {
            var e3 = HTMLElement.prototype;
            if (t3.is) {
              var n3 = document.createElement(t3.tag);
              e3 = Object.getPrototypeOf(n3);
            }
            for (var i2, o2 = t3.prototype, r2 = false; o2; )
              o2 == e3 && (r2 = true), i2 = Object.getPrototypeOf(o2), i2 && (o2.__proto__ = i2), o2 = i2;
            r2 || console.warn(t3.tag + " prototype not found in prototype chain for " + t3.is), t3.native = e3;
          }
        }
        function u(t3) {
          return y(E(t3.tag), t3);
        }
        function c(t3) {
          return t3 ? x[t3.toLowerCase()] : void 0;
        }
        function l(t3, e3) {
          x[t3] = e3;
        }
        function h(t3) {
          return function() {
            return u(t3);
          };
        }
        function p(t3, e3, n3) {
          return t3 === w ? d(e3, n3) : S(t3, e3);
        }
        function d(t3, e3) {
          t3 && (t3 = t3.toLowerCase()), e3 && (e3 = e3.toLowerCase());
          var n3 = c(e3 || t3);
          if (n3) {
            if (t3 == n3.tag && e3 == n3.is)
              return new n3.ctor();
            if (!e3 && !n3.is)
              return new n3.ctor();
          }
          var i2;
          return e3 ? (i2 = d(t3), i2.setAttribute("is", e3), i2) : (i2 = E(t3), t3.indexOf("-") >= 0 && b(i2, HTMLElement), i2);
        }
        function f(t3, e3) {
          var n3 = t3[e3];
          t3[e3] = function() {
            var t4 = n3.apply(this, arguments);
            return v(t4), t4;
          };
        }
        var g, m = (t2.isIE, t2.upgradeDocumentTree), v = t2.upgradeAll, y = t2.upgradeWithDefinition, b = t2.implementPrototype, A = t2.useNative, C = ["annotation-xml", "color-profile", "font-face", "font-face-src", "font-face-uri", "font-face-format", "font-face-name", "missing-glyph"], x = {}, w = "http://www.w3.org/1999/xhtml", E = document.createElement.bind(document), S = document.createElementNS.bind(document);
        g = Object.__proto__ || A ? function(t3, e3) {
          return t3 instanceof e3;
        } : function(t3, e3) {
          if (t3 instanceof e3)
            return true;
          for (var n3 = t3; n3; ) {
            if (n3 === e3.prototype)
              return true;
            n3 = n3.__proto__;
          }
          return false;
        }, f(Node.prototype, "cloneNode"), f(document, "importNode"), document.registerElement = e2, document.createElement = d, document.createElementNS = p, t2.registry = x, t2.instanceof = g, t2.reservedTagList = C, t2.getRegisteredDefinition = c, document.register = document.registerElement;
      }), function(t2) {
        function e2() {
          r(window.wrap(document)), window.CustomElements.ready = true;
          var t3 = window.requestAnimationFrame || function(t4) {
            setTimeout(t4, 16);
          };
          t3(function() {
            setTimeout(function() {
              window.CustomElements.readyTime = Date.now(), window.HTMLImports && (window.CustomElements.elapsed = window.CustomElements.readyTime - window.HTMLImports.readyTime), document.dispatchEvent(new CustomEvent("WebComponentsReady", { bubbles: true }));
            });
          });
        }
        var n2 = t2.useNative, i = t2.initializeModules;
        if (t2.isIE, n2) {
          var o = function() {
          };
          t2.watchShadow = o, t2.upgrade = o, t2.upgradeAll = o, t2.upgradeDocumentTree = o, t2.upgradeSubtree = o, t2.takeRecords = o, t2.instanceof = function(t3, e3) {
            return t3 instanceof e3;
          };
        } else
          i();
        var r = t2.upgradeDocumentTree, s = t2.upgradeDocument;
        if (window.wrap || (window.ShadowDOMPolyfill ? (window.wrap = window.ShadowDOMPolyfill.wrapIfNeeded, window.unwrap = window.ShadowDOMPolyfill.unwrapIfNeeded) : window.wrap = window.unwrap = function(t3) {
          return t3;
        }), window.HTMLImports && (window.HTMLImports.__importsParsingHook = function(t3) {
          t3.import && s(wrap(t3.import));
        }), "complete" === document.readyState || t2.flags.eager)
          e2();
        else if ("interactive" !== document.readyState || window.attachEvent || window.HTMLImports && !window.HTMLImports.ready) {
          var a = window.HTMLImports && !window.HTMLImports.ready ? "HTMLImportsLoaded" : "DOMContentLoaded";
          window.addEventListener(a, e2);
        } else
          e2();
      }(window.CustomElements));
    }.call(exports), function() {
    }.call(exports), function() {
      var t = this;
      (function() {
        (function() {
          this.Trix = { VERSION: "1.3.1", ZERO_WIDTH_SPACE: "\uFEFF", NON_BREAKING_SPACE: "\xA0", OBJECT_REPLACEMENT_CHARACTER: "\uFFFC", browser: { composesExistingText: /Android.*Chrome/.test(navigator.userAgent), forcesObjectResizing: /Trident.*rv:11/.test(navigator.userAgent), supportsInputEvents: function() {
            var t2, e2, n, i;
            if ("undefined" == typeof InputEvent)
              return false;
            for (i = ["data", "getTargetRanges", "inputType"], t2 = 0, e2 = i.length; e2 > t2; t2++)
              if (n = i[t2], !(n in InputEvent.prototype))
                return false;
            return true;
          }() }, config: {} };
        }).call(this);
      }).call(t);
      var e = t.Trix;
      (function() {
        (function() {
          e.BasicObject = function() {
            function t2() {
            }
            var e2, n, i;
            return t2.proxyMethod = function(t3) {
              var i2, o, r, s, a;
              return r = n(t3), i2 = r.name, s = r.toMethod, a = r.toProperty, o = r.optional, this.prototype[i2] = function() {
                var t4, n2;
                return t4 = null != s ? o ? "function" == typeof this[s] ? this[s]() : void 0 : this[s]() : null != a ? this[a] : void 0, o ? (n2 = null != t4 ? t4[i2] : void 0, null != n2 ? e2.call(n2, t4, arguments) : void 0) : (n2 = t4[i2], e2.call(n2, t4, arguments));
              };
            }, n = function(t3) {
              var e3, n2;
              if (!(n2 = t3.match(i)))
                throw new Error("can't parse @proxyMethod expression: " + t3);
              return e3 = { name: n2[4] }, null != n2[2] ? e3.toMethod = n2[1] : e3.toProperty = n2[1], null != n2[3] && (e3.optional = true), e3;
            }, e2 = Function.prototype.apply, i = /^(.+?)(\(\))?(\?)?\.(.+?)$/, t2;
          }();
        }).call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.Object = function(n2) {
            function i() {
              this.id = ++o;
            }
            var o;
            return t2(i, n2), o = 0, i.fromJSONString = function(t3) {
              return this.fromJSON(JSON.parse(t3));
            }, i.prototype.hasSameConstructorAs = function(t3) {
              return this.constructor === (null != t3 ? t3.constructor : void 0);
            }, i.prototype.isEqualTo = function(t3) {
              return this === t3;
            }, i.prototype.inspect = function() {
              var t3, e2, n3;
              return t3 = function() {
                var t4, i2, o2;
                i2 = null != (t4 = this.contentsForInspection()) ? t4 : {}, o2 = [];
                for (e2 in i2)
                  n3 = i2[e2], o2.push(e2 + "=" + n3);
                return o2;
              }.call(this), "#<" + this.constructor.name + ":" + this.id + (t3.length ? " " + t3.join(", ") : "") + ">";
            }, i.prototype.contentsForInspection = function() {
            }, i.prototype.toJSONString = function() {
              return JSON.stringify(this);
            }, i.prototype.toUTF16String = function() {
              return e.UTF16String.box(this);
            }, i.prototype.getCacheKey = function() {
              return this.id.toString();
            }, i;
          }(e.BasicObject);
        }.call(this), function() {
          e.extend = function(t2) {
            var e2, n;
            for (e2 in t2)
              n = t2[e2], this[e2] = n;
            return this;
          };
        }.call(this), function() {
          e.extend({ defer: function(t2) {
            return setTimeout(t2, 1);
          } });
        }.call(this), function() {
          var t2, n;
          e.extend({ normalizeSpaces: function(t3) {
            return t3.replace(RegExp("" + e.ZERO_WIDTH_SPACE, "g"), "").replace(RegExp("" + e.NON_BREAKING_SPACE, "g"), " ");
          }, normalizeNewlines: function(t3) {
            return t3.replace(/\r\n/g, "\n");
          }, breakableWhitespacePattern: RegExp("[^\\S" + e.NON_BREAKING_SPACE + "]"), squishBreakableWhitespace: function(t3) {
            return t3.replace(RegExp("" + e.breakableWhitespacePattern.source, "g"), " ").replace(/\ {2,}/g, " ");
          }, summarizeStringChange: function(t3, i) {
            var o, r, s, a;
            return t3 = e.UTF16String.box(t3), i = e.UTF16String.box(i), i.length < t3.length ? (r = n(t3, i), a = r[0], o = r[1]) : (s = n(i, t3), o = s[0], a = s[1]), { added: o, removed: a };
          } }), n = function(n2, i) {
            var o, r, s, a, u;
            return n2.isEqualTo(i) ? ["", ""] : (r = t2(n2, i), a = r.utf16String.length, s = a ? (u = r.offset, r, o = n2.codepoints.slice(0, u).concat(n2.codepoints.slice(u + a)), t2(i, e.UTF16String.fromCodepoints(o))) : t2(i, n2), [r.utf16String.toString(), s.utf16String.toString()]);
          }, t2 = function(t3, e2) {
            var n2, i, o;
            for (n2 = 0, i = t3.length, o = e2.length; i > n2 && t3.charAt(n2).isEqualTo(e2.charAt(n2)); )
              n2++;
            for (; i > n2 + 1 && t3.charAt(i - 1).isEqualTo(e2.charAt(o - 1)); )
              i--, o--;
            return { utf16String: t3.slice(n2, i), offset: n2 };
          };
        }.call(this), function() {
          e.extend({ copyObject: function(t2) {
            var e2, n, i;
            null == t2 && (t2 = {}), n = {};
            for (e2 in t2)
              i = t2[e2], n[e2] = i;
            return n;
          }, objectsAreEqual: function(t2, e2) {
            var n, i;
            if (null == t2 && (t2 = {}), null == e2 && (e2 = {}), Object.keys(t2).length !== Object.keys(e2).length)
              return false;
            for (n in t2)
              if (i = t2[n], i !== e2[n])
                return false;
            return true;
          } });
        }.call(this), function() {
          var t2 = [].slice;
          e.extend({ arraysAreEqual: function(t3, e2) {
            var n, i, o, r;
            if (null == t3 && (t3 = []), null == e2 && (e2 = []), t3.length !== e2.length)
              return false;
            for (i = n = 0, o = t3.length; o > n; i = ++n)
              if (r = t3[i], r !== e2[i])
                return false;
            return true;
          }, arrayStartsWith: function(t3, n) {
            return null == t3 && (t3 = []), null == n && (n = []), e.arraysAreEqual(t3.slice(0, n.length), n);
          }, spliceArray: function() {
            var e2, n, i;
            return n = arguments[0], e2 = 2 <= arguments.length ? t2.call(arguments, 1) : [], i = n.slice(0), i.splice.apply(i, e2), i;
          }, summarizeArrayChange: function(t3, e2) {
            var n, i, o, r, s, a, u, c, l, h, p;
            for (null == t3 && (t3 = []), null == e2 && (e2 = []), n = [], h = [], o = /* @__PURE__ */ new Set(), r = 0, u = t3.length; u > r; r++)
              p = t3[r], o.add(p);
            for (i = /* @__PURE__ */ new Set(), s = 0, c = e2.length; c > s; s++)
              p = e2[s], i.add(p), o.has(p) || n.push(p);
            for (a = 0, l = t3.length; l > a; a++)
              p = t3[a], i.has(p) || h.push(p);
            return { added: n, removed: h };
          } });
        }.call(this), function() {
          var t2, n, i, o;
          t2 = null, n = null, o = null, i = null, e.extend({ getAllAttributeNames: function() {
            return null != t2 ? t2 : t2 = e.getTextAttributeNames().concat(e.getBlockAttributeNames());
          }, getBlockConfig: function(t3) {
            return e.config.blockAttributes[t3];
          }, getBlockAttributeNames: function() {
            return null != n ? n : n = Object.keys(e.config.blockAttributes);
          }, getTextConfig: function(t3) {
            return e.config.textAttributes[t3];
          }, getTextAttributeNames: function() {
            return null != o ? o : o = Object.keys(e.config.textAttributes);
          }, getListAttributeNames: function() {
            var t3, n2;
            return null != i ? i : i = function() {
              var i2, o2;
              i2 = e.config.blockAttributes, o2 = [];
              for (t3 in i2)
                n2 = i2[t3].listAttribute, null != n2 && o2.push(n2);
              return o2;
            }();
          } });
        }.call(this), function() {
          var t2, n, i, o, r, s = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          t2 = document.documentElement, n = null != (i = null != (o = null != (r = t2.matchesSelector) ? r : t2.webkitMatchesSelector) ? o : t2.msMatchesSelector) ? i : t2.mozMatchesSelector, e.extend({ handleEvent: function(n2, i2) {
            var o2, r2, s2, a, u, c, l, h, p, d, f, g;
            return h = null != i2 ? i2 : {}, c = h.onElement, u = h.matchingSelector, g = h.withCallback, a = h.inPhase, l = h.preventDefault, d = h.times, r2 = null != c ? c : t2, p = u, o2 = g, f = "capturing" === a, s2 = function(t3) {
              var n3;
              return null != d && 0 === --d && s2.destroy(), n3 = e.findClosestElementFromNode(t3.target, { matchingSelector: p }), null != n3 && (null != g && g.call(n3, t3, n3), l) ? t3.preventDefault() : void 0;
            }, s2.destroy = function() {
              return r2.removeEventListener(n2, s2, f);
            }, r2.addEventListener(n2, s2, f), s2;
          }, handleEventOnce: function(t3, n2) {
            return null == n2 && (n2 = {}), n2.times = 1, e.handleEvent(t3, n2);
          }, triggerEvent: function(n2, i2) {
            var o2, r2, s2, a, u, c, l;
            return l = null != i2 ? i2 : {}, c = l.onElement, r2 = l.bubbles, s2 = l.cancelable, o2 = l.attributes, a = null != c ? c : t2, r2 = r2 !== false, s2 = s2 !== false, u = document.createEvent("Events"), u.initEvent(n2, r2, s2), null != o2 && e.extend.call(u, o2), a.dispatchEvent(u);
          }, elementMatchesSelector: function(t3, e2) {
            return 1 === (null != t3 ? t3.nodeType : void 0) ? n.call(t3, e2) : void 0;
          }, findClosestElementFromNode: function(t3, n2) {
            var i2, o2, r2;
            for (o2 = null != n2 ? n2 : {}, i2 = o2.matchingSelector, r2 = o2.untilNode; null != t3 && t3.nodeType !== Node.ELEMENT_NODE; )
              t3 = t3.parentNode;
            if (null != t3) {
              if (null == i2)
                return t3;
              if (t3.closest && null == r2)
                return t3.closest(i2);
              for (; t3 && t3 !== r2; ) {
                if (e.elementMatchesSelector(t3, i2))
                  return t3;
                t3 = t3.parentNode;
              }
            }
          }, findInnerElement: function(t3) {
            for (; null != t3 ? t3.firstElementChild : void 0; )
              t3 = t3.firstElementChild;
            return t3;
          }, innerElementIsActive: function(t3) {
            return document.activeElement !== t3 && e.elementContainsNode(t3, document.activeElement);
          }, elementContainsNode: function(t3, e2) {
            if (t3 && e2)
              for (; e2; ) {
                if (e2 === t3)
                  return true;
                e2 = e2.parentNode;
              }
          }, findNodeFromContainerAndOffset: function(t3, e2) {
            var n2;
            if (t3)
              return t3.nodeType === Node.TEXT_NODE ? t3 : 0 === e2 ? null != (n2 = t3.firstChild) ? n2 : t3 : t3.childNodes.item(e2 - 1);
          }, findElementFromContainerAndOffset: function(t3, n2) {
            var i2;
            return i2 = e.findNodeFromContainerAndOffset(t3, n2), e.findClosestElementFromNode(i2);
          }, findChildIndexOfNode: function(t3) {
            var e2;
            if (null != t3 ? t3.parentNode : void 0) {
              for (e2 = 0; t3 = t3.previousSibling; )
                e2++;
              return e2;
            }
          }, removeNode: function(t3) {
            var e2;
            return null != t3 && null != (e2 = t3.parentNode) ? e2.removeChild(t3) : void 0;
          }, walkTree: function(t3, e2) {
            var n2, i2, o2, r2, s2;
            return o2 = null != e2 ? e2 : {}, i2 = o2.onlyNodesOfType, r2 = o2.usingFilter, n2 = o2.expandEntityReferences, s2 = function() {
              switch (i2) {
                case "element":
                  return NodeFilter.SHOW_ELEMENT;
                case "text":
                  return NodeFilter.SHOW_TEXT;
                case "comment":
                  return NodeFilter.SHOW_COMMENT;
                default:
                  return NodeFilter.SHOW_ALL;
              }
            }(), document.createTreeWalker(t3, s2, null != r2 ? r2 : null, n2 === true);
          }, tagName: function(t3) {
            var e2;
            return null != t3 && null != (e2 = t3.tagName) ? e2.toLowerCase() : void 0;
          }, makeElement: function(t3, e2) {
            var n2, i2, o2, r2, s2, a, u, c, l, h, p, d, f, g;
            if (null == e2 && (e2 = {}), "object" == typeof t3 ? (e2 = t3, t3 = e2.tagName) : e2 = { attributes: e2 }, o2 = document.createElement(t3), null != e2.editable && (null == e2.attributes && (e2.attributes = {}), e2.attributes.contenteditable = e2.editable), e2.attributes) {
              l = e2.attributes;
              for (a in l)
                g = l[a], o2.setAttribute(a, g);
            }
            if (e2.style) {
              h = e2.style;
              for (a in h)
                g = h[a], o2.style[a] = g;
            }
            if (e2.data) {
              p = e2.data;
              for (a in p)
                g = p[a], o2.dataset[a] = g;
            }
            if (e2.className)
              for (d = e2.className.split(" "), r2 = 0, u = d.length; u > r2; r2++)
                i2 = d[r2], o2.classList.add(i2);
            if (e2.textContent && (o2.textContent = e2.textContent), e2.childNodes)
              for (f = [].concat(e2.childNodes), s2 = 0, c = f.length; c > s2; s2++)
                n2 = f[s2], o2.appendChild(n2);
            return o2;
          }, getBlockTagNames: function() {
            var t3, n2;
            return null != e.blockTagNames ? e.blockTagNames : e.blockTagNames = function() {
              var i2, o2;
              i2 = e.config.blockAttributes, o2 = [];
              for (t3 in i2)
                n2 = i2[t3].tagName, n2 && o2.push(n2);
              return o2;
            }();
          }, nodeIsBlockContainer: function(t3) {
            return e.nodeIsBlockStartComment(null != t3 ? t3.firstChild : void 0);
          }, nodeProbablyIsBlockContainer: function(t3) {
            var n2, i2;
            return n2 = e.tagName(t3), s.call(e.getBlockTagNames(), n2) >= 0 && (i2 = e.tagName(t3.firstChild), s.call(e.getBlockTagNames(), i2) < 0);
          }, nodeIsBlockStart: function(t3, n2) {
            var i2;
            return i2 = (null != n2 ? n2 : { strict: true }).strict, i2 ? e.nodeIsBlockStartComment(t3) : e.nodeIsBlockStartComment(t3) || !e.nodeIsBlockStartComment(t3.firstChild) && e.nodeProbablyIsBlockContainer(t3);
          }, nodeIsBlockStartComment: function(t3) {
            return e.nodeIsCommentNode(t3) && "block" === (null != t3 ? t3.data : void 0);
          }, nodeIsCommentNode: function(t3) {
            return (null != t3 ? t3.nodeType : void 0) === Node.COMMENT_NODE;
          }, nodeIsCursorTarget: function(t3, n2) {
            var i2;
            return i2 = (null != n2 ? n2 : {}).name, t3 ? e.nodeIsTextNode(t3) ? t3.data === e.ZERO_WIDTH_SPACE ? i2 ? t3.parentNode.dataset.trixCursorTarget === i2 : true : void 0 : e.nodeIsCursorTarget(t3.firstChild) : void 0;
          }, nodeIsAttachmentElement: function(t3) {
            return e.elementMatchesSelector(t3, e.AttachmentView.attachmentSelector);
          }, nodeIsEmptyTextNode: function(t3) {
            return e.nodeIsTextNode(t3) && "" === (null != t3 ? t3.data : void 0);
          }, nodeIsTextNode: function(t3) {
            return (null != t3 ? t3.nodeType : void 0) === Node.TEXT_NODE;
          } });
        }.call(this), function() {
          var t2, n, i, o, r;
          t2 = e.copyObject, o = e.objectsAreEqual, e.extend({ normalizeRange: i = function(t3) {
            var e2;
            if (null != t3)
              return Array.isArray(t3) || (t3 = [t3, t3]), [n(t3[0]), n(null != (e2 = t3[1]) ? e2 : t3[0])];
          }, rangeIsCollapsed: function(t3) {
            var e2, n2, o2;
            if (null != t3)
              return n2 = i(t3), o2 = n2[0], e2 = n2[1], r(o2, e2);
          }, rangesAreEqual: function(t3, e2) {
            var n2, o2, s, a, u, c;
            if (null != t3 && null != e2)
              return s = i(t3), o2 = s[0], n2 = s[1], a = i(e2), c = a[0], u = a[1], r(o2, c) && r(n2, u);
          } }), n = function(e2) {
            return "number" == typeof e2 ? e2 : t2(e2);
          }, r = function(t3, e2) {
            return "number" == typeof t3 ? t3 === e2 : o(t3, e2);
          };
        }.call(this), function() {
          var t2, n, i, o, r, s, a;
          e.registerElement = function(t3, e2) {
            var n2, i2;
            return null == e2 && (e2 = {}), t3 = t3.toLowerCase(), e2 = a(e2), i2 = s(e2), (n2 = i2.defaultCSS) && (delete i2.defaultCSS, o(n2, t3)), r(t3, i2);
          }, o = function(t3, e2) {
            var n2;
            return n2 = i(e2), n2.textContent = t3.replace(/%t/g, e2);
          }, i = function(e2) {
            var n2, i2;
            return n2 = document.createElement("style"), n2.setAttribute("type", "text/css"), n2.setAttribute("data-tag-name", e2.toLowerCase()), (i2 = t2()) && n2.setAttribute("nonce", i2), document.head.insertBefore(n2, document.head.firstChild), n2;
          }, t2 = function() {
            var t3;
            return (t3 = n("trix-csp-nonce") || n("csp-nonce")) ? t3.getAttribute("content") : void 0;
          }, n = function(t3) {
            return document.head.querySelector("meta[name=" + t3 + "]");
          }, s = function(t3) {
            var e2, n2, i2;
            n2 = {};
            for (e2 in t3)
              i2 = t3[e2], n2[e2] = "function" == typeof i2 ? { value: i2 } : i2;
            return n2;
          }, a = function() {
            var t3;
            return t3 = function(t4) {
              var e2, n2, i2, o2, r2;
              for (e2 = {}, r2 = ["initialize", "connect", "disconnect"], n2 = 0, o2 = r2.length; o2 > n2; n2++)
                i2 = r2[n2], e2[i2] = t4[i2], delete t4[i2];
              return e2;
            }, window.customElements ? function(e2) {
              var n2, i2, o2, r2, s2;
              return s2 = t3(e2), o2 = s2.initialize, n2 = s2.connect, i2 = s2.disconnect, o2 && (r2 = n2, n2 = function() {
                return this.initialized || (this.initialized = true, o2.call(this)), null != r2 ? r2.call(this) : void 0;
              }), n2 && (e2.connectedCallback = n2), i2 && (e2.disconnectedCallback = i2), e2;
            } : function(e2) {
              var n2, i2, o2, r2;
              return r2 = t3(e2), o2 = r2.initialize, n2 = r2.connect, i2 = r2.disconnect, o2 && (e2.createdCallback = o2), n2 && (e2.attachedCallback = n2), i2 && (e2.detachedCallback = i2), e2;
            };
          }(), r = function() {
            return window.customElements ? function(t3, e2) {
              var n2;
              return n2 = function() {
                return "object" == typeof Reflect ? Reflect.construct(HTMLElement, [], n2) : HTMLElement.apply(this);
              }, Object.setPrototypeOf(n2.prototype, HTMLElement.prototype), Object.setPrototypeOf(n2, HTMLElement), Object.defineProperties(n2.prototype, e2), window.customElements.define(t3, n2), n2;
            } : function(t3, e2) {
              var n2, i2;
              return i2 = Object.create(HTMLElement.prototype, e2), n2 = document.registerElement(t3, { prototype: i2 }), Object.defineProperty(i2, "constructor", { value: n2 }), n2;
            };
          }();
        }.call(this), function() {
          var t2, n;
          e.extend({ getDOMSelection: function() {
            var t3;
            return t3 = window.getSelection(), t3.rangeCount > 0 ? t3 : void 0;
          }, getDOMRange: function() {
            var n2, i;
            return (n2 = null != (i = e.getDOMSelection()) ? i.getRangeAt(0) : void 0) && !t2(n2) ? n2 : void 0;
          }, setDOMRange: function(t3) {
            var n2;
            return n2 = window.getSelection(), n2.removeAllRanges(), n2.addRange(t3), e.selectionChangeObserver.update();
          } }), t2 = function(t3) {
            return n(t3.startContainer) || n(t3.endContainer);
          }, n = function(t3) {
            return !Object.getPrototypeOf(t3);
          };
        }.call(this), function() {
          var t2;
          t2 = { "application/x-trix-feature-detection": "test" }, e.extend({ dataTransferIsPlainText: function(t3) {
            var e2, n, i;
            return i = t3.getData("text/plain"), n = t3.getData("text/html"), i && n ? (e2 = new DOMParser().parseFromString(n, "text/html").body, e2.textContent === i ? !e2.querySelector("*") : void 0) : null != i ? i.length : void 0;
          }, dataTransferIsWritable: function(e2) {
            var n, i;
            if (null != (null != e2 ? e2.setData : void 0)) {
              for (n in t2)
                if (i = t2[n], !function() {
                  try {
                    return e2.setData(n, i), e2.getData(n) === i;
                  } catch (t3) {
                  }
                }())
                  return;
              return true;
            }
          }, keyEventIsKeyboardCommand: function() {
            return /Mac|^iP/.test(navigator.platform) ? function(t3) {
              return t3.metaKey;
            } : function(t3) {
              return t3.ctrlKey;
            };
          }() });
        }.call(this), function() {
          e.extend({ RTL_PATTERN: /[\u05BE\u05C0\u05C3\u05D0-\u05EA\u05F0-\u05F4\u061B\u061F\u0621-\u063A\u0640-\u064A\u066D\u0671-\u06B7\u06BA-\u06BE\u06C0-\u06CE\u06D0-\u06D5\u06E5\u06E6\u200F\u202B\u202E\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE72\uFE74\uFE76-\uFEFC]/, getDirection: function() {
            var t2, n, i, o;
            return n = e.makeElement("input", { dir: "auto", name: "x", dirName: "x.dir" }), t2 = e.makeElement("form"), t2.appendChild(n), i = function() {
              try {
                return new FormData(t2).has(n.dirName);
              } catch (e2) {
              }
            }(), o = function() {
              try {
                return n.matches(":dir(ltr),:dir(rtl)");
              } catch (t3) {
              }
            }(), i ? function(e2) {
              return n.value = e2, new FormData(t2).get(n.dirName);
            } : o ? function(t3) {
              return n.value = t3, n.matches(":dir(rtl)") ? "rtl" : "ltr";
            } : function(t3) {
              var n2;
              return n2 = t3.trim().charAt(0), e.RTL_PATTERN.test(n2) ? "rtl" : "ltr";
            };
          }() });
        }.call(this), function() {
        }.call(this), function() {
          var t2, n = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var o in e2)
              i.call(e2, o) && (t3[o] = e2[o]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, i = {}.hasOwnProperty;
          t2 = e.arraysAreEqual, e.Hash = function(i2) {
            function o(t3) {
              null == t3 && (t3 = {}), this.values = s(t3), o.__super__.constructor.apply(this, arguments);
            }
            var r, s, a, u, c;
            return n(o, i2), o.fromCommonAttributesOfObjects = function(t3) {
              var e2, n2, i3, o2, s2, a2;
              if (null == t3 && (t3 = []), !t3.length)
                return new this();
              for (e2 = r(t3[0]), i3 = e2.getKeys(), a2 = t3.slice(1), n2 = 0, o2 = a2.length; o2 > n2; n2++)
                s2 = a2[n2], i3 = e2.getKeysCommonToHash(r(s2)), e2 = e2.slice(i3);
              return e2;
            }, o.box = function(t3) {
              return r(t3);
            }, o.prototype.add = function(t3, e2) {
              return this.merge(u(t3, e2));
            }, o.prototype.remove = function(t3) {
              return new e.Hash(s(this.values, t3));
            }, o.prototype.get = function(t3) {
              return this.values[t3];
            }, o.prototype.has = function(t3) {
              return t3 in this.values;
            }, o.prototype.merge = function(t3) {
              return new e.Hash(a(this.values, c(t3)));
            }, o.prototype.slice = function(t3) {
              var n2, i3, o2, r2;
              for (r2 = {}, n2 = 0, o2 = t3.length; o2 > n2; n2++)
                i3 = t3[n2], this.has(i3) && (r2[i3] = this.values[i3]);
              return new e.Hash(r2);
            }, o.prototype.getKeys = function() {
              return Object.keys(this.values);
            }, o.prototype.getKeysCommonToHash = function(t3) {
              var e2, n2, i3, o2, s2;
              for (t3 = r(t3), o2 = this.getKeys(), s2 = [], e2 = 0, i3 = o2.length; i3 > e2; e2++)
                n2 = o2[e2], this.values[n2] === t3.values[n2] && s2.push(n2);
              return s2;
            }, o.prototype.isEqualTo = function(e2) {
              return t2(this.toArray(), r(e2).toArray());
            }, o.prototype.isEmpty = function() {
              return 0 === this.getKeys().length;
            }, o.prototype.toArray = function() {
              var t3, e2, n2;
              return (null != this.array ? this.array : this.array = function() {
                var i3;
                e2 = [], i3 = this.values;
                for (t3 in i3)
                  n2 = i3[t3], e2.push(t3, n2);
                return e2;
              }.call(this)).slice(0);
            }, o.prototype.toObject = function() {
              return s(this.values);
            }, o.prototype.toJSON = function() {
              return this.toObject();
            }, o.prototype.contentsForInspection = function() {
              return { values: JSON.stringify(this.values) };
            }, u = function(t3, e2) {
              var n2;
              return n2 = {}, n2[t3] = e2, n2;
            }, a = function(t3, e2) {
              var n2, i3, o2;
              i3 = s(t3);
              for (n2 in e2)
                o2 = e2[n2], i3[n2] = o2;
              return i3;
            }, s = function(t3, e2) {
              var n2, i3, o2, r2, s2;
              for (r2 = {}, s2 = Object.keys(t3).sort(), n2 = 0, o2 = s2.length; o2 > n2; n2++)
                i3 = s2[n2], i3 !== e2 && (r2[i3] = t3[i3]);
              return r2;
            }, r = function(t3) {
              return t3 instanceof e.Hash ? t3 : new e.Hash(t3);
            }, c = function(t3) {
              return t3 instanceof e.Hash ? t3.values : t3;
            }, o;
          }(e.Object);
        }.call(this), function() {
          e.ObjectGroup = function() {
            function t2(t3, e2) {
              var n, i;
              this.objects = null != t3 ? t3 : [], i = e2.depth, n = e2.asTree, n && (this.depth = i, this.objects = this.constructor.groupObjects(this.objects, { asTree: n, depth: this.depth + 1 }));
            }
            return t2.groupObjects = function(t3, e2) {
              var n, i, o, r, s, a, u, c, l;
              for (null == t3 && (t3 = []), l = null != e2 ? e2 : {}, o = l.depth, n = l.asTree, n && null == o && (o = 0), c = [], s = 0, a = t3.length; a > s; s++) {
                if (u = t3[s], r) {
                  if (("function" == typeof u.canBeGrouped ? u.canBeGrouped(o) : void 0) && ("function" == typeof (i = r[r.length - 1]).canBeGroupedWith ? i.canBeGroupedWith(u, o) : void 0)) {
                    r.push(u);
                    continue;
                  }
                  c.push(new this(r, { depth: o, asTree: n })), r = null;
                }
                ("function" == typeof u.canBeGrouped ? u.canBeGrouped(o) : void 0) ? r = [u] : c.push(u);
              }
              return r && c.push(new this(r, { depth: o, asTree: n })), c;
            }, t2.prototype.getObjects = function() {
              return this.objects;
            }, t2.prototype.getDepth = function() {
              return this.depth;
            }, t2.prototype.getCacheKey = function() {
              var t3, e2, n, i, o;
              for (e2 = ["objectGroup"], o = this.getObjects(), t3 = 0, n = o.length; n > t3; t3++)
                i = o[t3], e2.push(i.getCacheKey());
              return e2.join("/");
            }, t2;
          }();
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.ObjectMap = function(e2) {
            function n2(t3) {
              var e3, n3, i, o, r;
              for (null == t3 && (t3 = []), this.objects = {}, i = 0, o = t3.length; o > i; i++)
                r = t3[i], n3 = JSON.stringify(r), null == (e3 = this.objects)[n3] && (e3[n3] = r);
            }
            return t2(n2, e2), n2.prototype.find = function(t3) {
              var e3;
              return e3 = JSON.stringify(t3), this.objects[e3];
            }, n2;
          }(e.BasicObject);
        }.call(this), function() {
          e.ElementStore = function() {
            function t2(t3) {
              this.reset(t3);
            }
            var e2;
            return t2.prototype.add = function(t3) {
              var n;
              return n = e2(t3), this.elements[n] = t3;
            }, t2.prototype.remove = function(t3) {
              var n, i;
              return n = e2(t3), (i = this.elements[n]) ? (delete this.elements[n], i) : void 0;
            }, t2.prototype.reset = function(t3) {
              var e3, n, i;
              for (null == t3 && (t3 = []), this.elements = {}, n = 0, i = t3.length; i > n; n++)
                e3 = t3[n], this.add(e3);
              return t3;
            }, e2 = function(t3) {
              return t3.dataset.trixStoreKey;
            }, t2;
          }();
        }.call(this), function() {
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.Operation = function(e2) {
            function n2() {
              return n2.__super__.constructor.apply(this, arguments);
            }
            return t2(n2, e2), n2.prototype.isPerforming = function() {
              return this.performing === true;
            }, n2.prototype.hasPerformed = function() {
              return this.performed === true;
            }, n2.prototype.hasSucceeded = function() {
              return this.performed && this.succeeded;
            }, n2.prototype.hasFailed = function() {
              return this.performed && !this.succeeded;
            }, n2.prototype.getPromise = function() {
              return null != this.promise ? this.promise : this.promise = new Promise(function(t3) {
                return function(e3, n3) {
                  return t3.performing = true, t3.perform(function(i, o) {
                    return t3.succeeded = i, t3.performing = false, t3.performed = true, t3.succeeded ? e3(o) : n3(o);
                  });
                };
              }(this));
            }, n2.prototype.perform = function(t3) {
              return t3(false);
            }, n2.prototype.release = function() {
              var t3;
              return null != (t3 = this.promise) && "function" == typeof t3.cancel && t3.cancel(), this.promise = null, this.performing = null, this.performed = null, this.succeeded = null;
            }, n2.proxyMethod("getPromise().then"), n2.proxyMethod("getPromise().catch"), n2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o, r, s = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              a.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, a = {}.hasOwnProperty;
          e.UTF16String = function(t3) {
            function e2(t4, e3) {
              this.ucs2String = t4, this.codepoints = e3, this.length = this.codepoints.length, this.ucs2Length = this.ucs2String.length;
            }
            return s(e2, t3), e2.box = function(t4) {
              return null == t4 && (t4 = ""), t4 instanceof this ? t4 : this.fromUCS2String(null != t4 ? t4.toString() : void 0);
            }, e2.fromUCS2String = function(t4) {
              return new this(t4, o(t4));
            }, e2.fromCodepoints = function(t4) {
              return new this(r(t4), t4);
            }, e2.prototype.offsetToUCS2Offset = function(t4) {
              return r(this.codepoints.slice(0, Math.max(0, t4))).length;
            }, e2.prototype.offsetFromUCS2Offset = function(t4) {
              return o(this.ucs2String.slice(0, Math.max(0, t4))).length;
            }, e2.prototype.slice = function() {
              var t4;
              return this.constructor.fromCodepoints((t4 = this.codepoints).slice.apply(t4, arguments));
            }, e2.prototype.charAt = function(t4) {
              return this.slice(t4, t4 + 1);
            }, e2.prototype.isEqualTo = function(t4) {
              return this.constructor.box(t4).ucs2String === this.ucs2String;
            }, e2.prototype.toJSON = function() {
              return this.ucs2String;
            }, e2.prototype.getCacheKey = function() {
              return this.ucs2String;
            }, e2.prototype.toString = function() {
              return this.ucs2String;
            }, e2;
          }(e.BasicObject), t2 = 1 === ("function" == typeof Array.from ? Array.from("\u{1F47C}").length : void 0), n = null != ("function" == typeof " ".codePointAt ? " ".codePointAt(0) : void 0), i = " \u{1F47C}" === ("function" == typeof String.fromCodePoint ? String.fromCodePoint(32, 128124) : void 0), o = t2 && n ? function(t3) {
            return Array.from(t3).map(function(t4) {
              return t4.codePointAt(0);
            });
          } : function(t3) {
            var e2, n2, i2, o2, r2;
            for (o2 = [], e2 = 0, i2 = t3.length; i2 > e2; )
              r2 = t3.charCodeAt(e2++), r2 >= 55296 && 56319 >= r2 && i2 > e2 && (n2 = t3.charCodeAt(e2++), 56320 === (64512 & n2) ? r2 = ((1023 & r2) << 10) + (1023 & n2) + 65536 : e2--), o2.push(r2);
            return o2;
          }, r = i ? function(t3) {
            return String.fromCodePoint.apply(String, t3);
          } : function(t3) {
            var e2, n2, i2;
            return e2 = function() {
              var e3, o2, r2;
              for (r2 = [], e3 = 0, o2 = t3.length; o2 > e3; e3++)
                i2 = t3[e3], n2 = "", i2 > 65535 && (i2 -= 65536, n2 += String.fromCharCode(i2 >>> 10 & 1023 | 55296), i2 = 56320 | 1023 & i2), r2.push(n2 + String.fromCharCode(i2));
              return r2;
            }(), e2.join("");
          };
        }.call(this), function() {
        }.call(this), function() {
        }.call(this), function() {
          e.config.lang = { attachFiles: "Attach Files", bold: "Bold", bullets: "Bullets", "byte": "Byte", bytes: "Bytes", captionPlaceholder: "Add a caption\u2026", code: "Code", heading1: "Heading", indent: "Increase Level", italic: "Italic", link: "Link", numbers: "Numbers", outdent: "Decrease Level", quote: "Quote", redo: "Redo", remove: "Remove", strike: "Strikethrough", undo: "Undo", unlink: "Unlink", url: "URL", urlPlaceholder: "Enter a URL\u2026", GB: "GB", KB: "KB", MB: "MB", PB: "PB", TB: "TB" };
        }.call(this), function() {
          e.config.css = { attachment: "attachment", attachmentCaption: "attachment__caption", attachmentCaptionEditor: "attachment__caption-editor", attachmentMetadata: "attachment__metadata", attachmentMetadataContainer: "attachment__metadata-container", attachmentName: "attachment__name", attachmentProgress: "attachment__progress", attachmentSize: "attachment__size", attachmentToolbar: "attachment__toolbar", attachmentGallery: "attachment-gallery" };
        }.call(this), function() {
          var t2;
          e.config.blockAttributes = t2 = { "default": { tagName: "div", parse: false }, quote: { tagName: "blockquote", nestable: true }, heading1: { tagName: "h1", terminal: true, breakOnReturn: true, group: false }, code: { tagName: "pre", terminal: true, text: { plaintext: true } }, bulletList: { tagName: "ul", parse: false }, bullet: { tagName: "li", listAttribute: "bulletList", group: false, nestable: true, test: function(n) {
            return e.tagName(n.parentNode) === t2[this.listAttribute].tagName;
          } }, numberList: { tagName: "ol", parse: false }, number: { tagName: "li", listAttribute: "numberList", group: false, nestable: true, test: function(n) {
            return e.tagName(n.parentNode) === t2[this.listAttribute].tagName;
          } }, attachmentGallery: { tagName: "div", exclusive: true, terminal: true, parse: false, group: false } };
        }.call(this), function() {
          var t2, n;
          t2 = e.config.lang, n = [t2.bytes, t2.KB, t2.MB, t2.GB, t2.TB, t2.PB], e.config.fileSize = { prefix: "IEC", precision: 2, formatter: function(e2) {
            var i, o, r, s, a;
            switch (e2) {
              case 0:
                return "0 " + t2.bytes;
              case 1:
                return "1 " + t2.byte;
              default:
                return i = function() {
                  switch (this.prefix) {
                    case "SI":
                      return 1e3;
                    case "IEC":
                      return 1024;
                  }
                }.call(this), o = Math.floor(Math.log(e2) / Math.log(i)), r = e2 / Math.pow(i, o), s = r.toFixed(this.precision), a = s.replace(/0*$/, "").replace(/\.$/, ""), a + " " + n[o];
            }
          } };
        }.call(this), function() {
          e.config.textAttributes = { bold: { tagName: "strong", inheritable: true, parser: function(t2) {
            var e2;
            return e2 = window.getComputedStyle(t2), "bold" === e2.fontWeight || e2.fontWeight >= 600;
          } }, italic: { tagName: "em", inheritable: true, parser: function(t2) {
            var e2;
            return e2 = window.getComputedStyle(t2), "italic" === e2.fontStyle;
          } }, href: { groupTagName: "a", parser: function(t2) {
            var n, i, o;
            return n = e.AttachmentView.attachmentSelector, o = "a:not(" + n + ")", (i = e.findClosestElementFromNode(t2, { matchingSelector: o })) ? i.getAttribute("href") : void 0;
          } }, strike: { tagName: "del", inheritable: true }, frozen: { style: { backgroundColor: "highlight" } } };
        }.call(this), function() {
          var t2, n, i, o, r;
          r = "[data-trix-serialize=false]", o = ["contenteditable", "data-trix-id", "data-trix-store-key", "data-trix-mutable", "data-trix-placeholder", "tabindex"], n = "data-trix-serialized-attributes", i = "[" + n + "]", t2 = new RegExp("<!--block-->", "g"), e.extend({ serializers: { "application/json": function(t3) {
            var n2;
            if (t3 instanceof e.Document)
              n2 = t3;
            else {
              if (!(t3 instanceof HTMLElement))
                throw new Error("unserializable object");
              n2 = e.Document.fromHTML(t3.innerHTML);
            }
            return n2.toSerializableDocument().toJSONString();
          }, "text/html": function(s) {
            var a, u, c, l, h, p, d, f, g, m, v, y, b, A, C, x, w;
            if (s instanceof e.Document)
              l = e.DocumentView.render(s);
            else {
              if (!(s instanceof HTMLElement))
                throw new Error("unserializable object");
              l = s.cloneNode(true);
            }
            for (A = l.querySelectorAll(r), h = 0, g = A.length; g > h; h++)
              c = A[h], e.removeNode(c);
            for (p = 0, m = o.length; m > p; p++)
              for (a = o[p], C = l.querySelectorAll("[" + a + "]"), d = 0, v = C.length; v > d; d++)
                c = C[d], c.removeAttribute(a);
            for (x = l.querySelectorAll(i), f = 0, y = x.length; y > f; f++) {
              c = x[f];
              try {
                u = JSON.parse(c.getAttribute(n)), c.removeAttribute(n);
                for (b in u)
                  w = u[b], c.setAttribute(b, w);
              } catch (E) {
              }
            }
            return l.innerHTML.replace(t2, "");
          } }, deserializers: { "application/json": function(t3) {
            return e.Document.fromJSONString(t3);
          }, "text/html": function(t3) {
            return e.Document.fromHTML(t3);
          } }, serializeToContentType: function(t3, n2) {
            var i2;
            if (i2 = e.serializers[n2])
              return i2(t3);
            throw new Error("unknown content type: " + n2);
          }, deserializeFromContentType: function(t3, n2) {
            var i2;
            if (i2 = e.deserializers[n2])
              return i2(t3);
            throw new Error("unknown content type: " + n2);
          } });
        }.call(this), function() {
          var t2;
          t2 = e.config.lang, e.config.toolbar = { getDefaultHTML: function() {
            return '<div class="trix-button-row">\n  <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="' + t2.bold + '" tabindex="-1">' + t2.bold + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="' + t2.italic + '" tabindex="-1">' + t2.italic + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="' + t2.strike + '" tabindex="-1">' + t2.strike + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" title="' + t2.link + '" tabindex="-1">' + t2.link + '</button>\n  </span>\n\n  <span class="trix-button-group trix-button-group--block-tools" data-trix-button-group="block-tools">\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1" data-trix-attribute="heading1" title="' + t2.heading1 + '" tabindex="-1">' + t2.heading1 + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-quote" data-trix-attribute="quote" title="' + t2.quote + '" tabindex="-1">' + t2.quote + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-code" data-trix-attribute="code" title="' + t2.code + '" tabindex="-1">' + t2.code + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="' + t2.bullets + '" tabindex="-1">' + t2.bullets + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="' + t2.numbers + '" tabindex="-1">' + t2.numbers + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-decrease-nesting-level" data-trix-action="decreaseNestingLevel" title="' + t2.outdent + '" tabindex="-1">' + t2.outdent + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-increase-nesting-level" data-trix-action="increaseNestingLevel" title="' + t2.indent + '" tabindex="-1">' + t2.indent + '</button>\n  </span>\n\n  <span class="trix-button-group trix-button-group--file-tools" data-trix-button-group="file-tools">\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-attach" data-trix-action="attachFiles" title="' + t2.attachFiles + '" tabindex="-1">' + t2.attachFiles + '</button>\n  </span>\n\n  <span class="trix-button-group-spacer"></span>\n\n  <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="' + t2.undo + '" tabindex="-1">' + t2.undo + '</button>\n    <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="' + t2.redo + '" tabindex="-1">' + t2.redo + '</button>\n  </span>\n</div>\n\n<div class="trix-dialogs" data-trix-dialogs>\n  <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">\n    <div class="trix-dialog__link-fields">\n      <input type="url" name="href" class="trix-input trix-input--dialog" placeholder="' + t2.urlPlaceholder + '" aria-label="' + t2.url + '" required data-trix-input>\n      <div class="trix-button-group">\n        <input type="button" class="trix-button trix-button--dialog" value="' + t2.link + '" data-trix-method="setAttribute">\n        <input type="button" class="trix-button trix-button--dialog" value="' + t2.unlink + '" data-trix-method="removeAttribute">\n      </div>\n    </div>\n  </div>\n</div>';
          } };
        }.call(this), function() {
          e.config.undoInterval = 5e3;
        }.call(this), function() {
          e.config.attachments = { preview: { presentation: "gallery", caption: { name: true, size: true } }, file: { caption: { size: true } } };
        }.call(this), function() {
          e.config.keyNames = { 8: "backspace", 9: "tab", 13: "return", 27: "escape", 37: "left", 39: "right", 46: "delete", 68: "d", 72: "h", 79: "o" };
        }.call(this), function() {
          e.config.input = { level2Enabled: true, getLevel: function() {
            return this.level2Enabled && e.browser.supportsInputEvents ? 2 : 0;
          }, pickFiles: function(t2) {
            var n;
            return n = e.makeElement("input", { type: "file", multiple: true, hidden: true, id: this.fileInputId }), n.addEventListener("change", function() {
              return t2(n.files), e.removeNode(n);
            }), e.removeNode(document.getElementById(this.fileInputId)), document.body.appendChild(n), n.click();
          }, fileInputId: "trix-file-input-" + Date.now().toString(16) };
        }.call(this), function() {
        }.call(this), function() {
          e.registerElement("trix-toolbar", { defaultCSS: "%t {\n  display: block;\n}\n\n%t {\n  white-space: nowrap;\n}\n\n%t [data-trix-dialog] {\n  display: none;\n}\n\n%t [data-trix-dialog][data-trix-active] {\n  display: block;\n}\n\n%t [data-trix-dialog] [data-trix-validate]:invalid {\n  background-color: #ffdddd;\n}", initialize: function() {
            return "" === this.innerHTML ? this.innerHTML = e.config.toolbar.getDefaultHTML() : void 0;
          } });
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i2() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i2.prototype = e2.prototype, t3.prototype = new i2(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty, i = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          e.ObjectView = function(n2) {
            function o(t3, e2) {
              this.object = t3, this.options = null != e2 ? e2 : {}, this.childViews = [], this.rootView = this;
            }
            return t2(o, n2), o.prototype.getNodes = function() {
              var t3, e2, n3, i2, o2;
              for (null == this.nodes && (this.nodes = this.createNodes()), i2 = this.nodes, o2 = [], t3 = 0, e2 = i2.length; e2 > t3; t3++)
                n3 = i2[t3], o2.push(n3.cloneNode(true));
              return o2;
            }, o.prototype.invalidate = function() {
              var t3;
              return this.nodes = null, this.childViews = [], null != (t3 = this.parentView) ? t3.invalidate() : void 0;
            }, o.prototype.invalidateViewForObject = function(t3) {
              var e2;
              return null != (e2 = this.findViewForObject(t3)) ? e2.invalidate() : void 0;
            }, o.prototype.findOrCreateCachedChildView = function(t3, e2) {
              var n3;
              return (n3 = this.getCachedViewForObject(e2)) ? this.recordChildView(n3) : (n3 = this.createChildView.apply(this, arguments), this.cacheViewForObject(n3, e2)), n3;
            }, o.prototype.createChildView = function(t3, n3, i2) {
              var o2;
              return null == i2 && (i2 = {}), n3 instanceof e.ObjectGroup && (i2.viewClass = t3, t3 = e.ObjectGroupView), o2 = new t3(n3, i2), this.recordChildView(o2);
            }, o.prototype.recordChildView = function(t3) {
              return t3.parentView = this, t3.rootView = this.rootView, this.childViews.push(t3), t3;
            }, o.prototype.getAllChildViews = function() {
              var t3, e2, n3, i2, o2;
              for (o2 = [], i2 = this.childViews, e2 = 0, n3 = i2.length; n3 > e2; e2++)
                t3 = i2[e2], o2.push(t3), o2 = o2.concat(t3.getAllChildViews());
              return o2;
            }, o.prototype.findElement = function() {
              return this.findElementForObject(this.object);
            }, o.prototype.findElementForObject = function(t3) {
              var e2;
              return (e2 = null != t3 ? t3.id : void 0) ? this.rootView.element.querySelector("[data-trix-id='" + e2 + "']") : void 0;
            }, o.prototype.findViewForObject = function(t3) {
              var e2, n3, i2, o2;
              for (i2 = this.getAllChildViews(), e2 = 0, n3 = i2.length; n3 > e2; e2++)
                if (o2 = i2[e2], o2.object === t3)
                  return o2;
            }, o.prototype.getViewCache = function() {
              return this.rootView !== this ? this.rootView.getViewCache() : this.isViewCachingEnabled() ? null != this.viewCache ? this.viewCache : this.viewCache = {} : void 0;
            }, o.prototype.isViewCachingEnabled = function() {
              return this.shouldCacheViews !== false;
            }, o.prototype.enableViewCaching = function() {
              return this.shouldCacheViews = true;
            }, o.prototype.disableViewCaching = function() {
              return this.shouldCacheViews = false;
            }, o.prototype.getCachedViewForObject = function(t3) {
              var e2;
              return null != (e2 = this.getViewCache()) ? e2[t3.getCacheKey()] : void 0;
            }, o.prototype.cacheViewForObject = function(t3, e2) {
              var n3;
              return null != (n3 = this.getViewCache()) ? n3[e2.getCacheKey()] = t3 : void 0;
            }, o.prototype.garbageCollectCachedViews = function() {
              var t3, e2, n3, o2, r, s;
              if (t3 = this.getViewCache()) {
                s = this.getAllChildViews().concat(this), n3 = function() {
                  var t4, e3, n4;
                  for (n4 = [], t4 = 0, e3 = s.length; e3 > t4; t4++)
                    r = s[t4], n4.push(r.object.getCacheKey());
                  return n4;
                }(), o2 = [];
                for (e2 in t3)
                  i.call(n3, e2) < 0 && o2.push(delete t3[e2]);
                return o2;
              }
            }, o;
          }(e.BasicObject);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.ObjectGroupView = function(e2) {
            function n2() {
              n2.__super__.constructor.apply(this, arguments), this.objectGroup = this.object, this.viewClass = this.options.viewClass, delete this.options.viewClass;
            }
            return t2(n2, e2), n2.prototype.getChildViews = function() {
              var t3, e3, n3, i;
              if (!this.childViews.length)
                for (i = this.objectGroup.getObjects(), t3 = 0, e3 = i.length; e3 > t3; t3++)
                  n3 = i[t3], this.findOrCreateCachedChildView(this.viewClass, n3, this.options);
              return this.childViews;
            }, n2.prototype.createNodes = function() {
              var t3, e3, n3, i, o, r, s, a, u;
              for (t3 = this.createContainerElement(), s = this.getChildViews(), e3 = 0, i = s.length; i > e3; e3++)
                for (u = s[e3], a = u.getNodes(), n3 = 0, o = a.length; o > n3; n3++)
                  r = a[n3], t3.appendChild(r);
              return [t3];
            }, n2.prototype.createContainerElement = function(t3) {
              return null == t3 && (t3 = this.objectGroup.getDepth()), this.getChildViews()[0].createContainerElement(t3);
            }, n2;
          }(e.ObjectView);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.Controller = function(e2) {
            function n2() {
              return n2.__super__.constructor.apply(this, arguments);
            }
            return t2(n2, e2), n2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o, r, s, a = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, u = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              c.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, c = {}.hasOwnProperty, l = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          t2 = e.findClosestElementFromNode, i = e.nodeIsEmptyTextNode, n = e.nodeIsBlockStartComment, o = e.normalizeSpaces, r = e.summarizeStringChange, s = e.tagName, e.MutationObserver = function(e2) {
            function c2(t3) {
              this.element = t3, this.didMutate = a(this.didMutate, this), this.observer = new window.MutationObserver(this.didMutate), this.start();
            }
            var h, p, d, f;
            return u(c2, e2), p = "data-trix-mutable", d = "[" + p + "]", f = { attributes: true, childList: true, characterData: true, characterDataOldValue: true, subtree: true }, c2.prototype.start = function() {
              return this.reset(), this.observer.observe(this.element, f);
            }, c2.prototype.stop = function() {
              return this.observer.disconnect();
            }, c2.prototype.didMutate = function(t3) {
              var e3, n2;
              return (e3 = this.mutations).push.apply(e3, this.findSignificantMutations(t3)), this.mutations.length ? (null != (n2 = this.delegate) && "function" == typeof n2.elementDidMutate && n2.elementDidMutate(this.getMutationSummary()), this.reset()) : void 0;
            }, c2.prototype.reset = function() {
              return this.mutations = [];
            }, c2.prototype.findSignificantMutations = function(t3) {
              var e3, n2, i2, o2;
              for (o2 = [], e3 = 0, n2 = t3.length; n2 > e3; e3++)
                i2 = t3[e3], this.mutationIsSignificant(i2) && o2.push(i2);
              return o2;
            }, c2.prototype.mutationIsSignificant = function(t3) {
              var e3, n2, i2, o2;
              if (this.nodeIsMutable(t3.target))
                return false;
              for (o2 = this.nodesModifiedByMutation(t3), e3 = 0, n2 = o2.length; n2 > e3; e3++)
                if (i2 = o2[e3], this.nodeIsSignificant(i2))
                  return true;
              return false;
            }, c2.prototype.nodeIsSignificant = function(t3) {
              return t3 !== this.element && !this.nodeIsMutable(t3) && !i(t3);
            }, c2.prototype.nodeIsMutable = function(e3) {
              return t2(e3, { matchingSelector: d });
            }, c2.prototype.nodesModifiedByMutation = function(t3) {
              var e3;
              switch (e3 = [], t3.type) {
                case "attributes":
                  t3.attributeName !== p && e3.push(t3.target);
                  break;
                case "characterData":
                  e3.push(t3.target.parentNode), e3.push(t3.target);
                  break;
                case "childList":
                  e3.push.apply(e3, t3.addedNodes), e3.push.apply(e3, t3.removedNodes);
              }
              return e3;
            }, c2.prototype.getMutationSummary = function() {
              return this.getTextMutationSummary();
            }, c2.prototype.getTextMutationSummary = function() {
              var t3, e3, n2, i2, o2, r2, s2, a2, u2, c3, h2;
              for (a2 = this.getTextChangesFromCharacterData(), n2 = a2.additions, o2 = a2.deletions, h2 = this.getTextChangesFromChildList(), u2 = h2.additions, r2 = 0, s2 = u2.length; s2 > r2; r2++)
                e3 = u2[r2], l.call(n2, e3) < 0 && n2.push(e3);
              return o2.push.apply(o2, h2.deletions), c3 = {}, (t3 = n2.join("")) && (c3.textAdded = t3), (i2 = o2.join("")) && (c3.textDeleted = i2), c3;
            }, c2.prototype.getMutationsByType = function(t3) {
              var e3, n2, i2, o2, r2;
              for (o2 = this.mutations, r2 = [], e3 = 0, n2 = o2.length; n2 > e3; e3++)
                i2 = o2[e3], i2.type === t3 && r2.push(i2);
              return r2;
            }, c2.prototype.getTextChangesFromChildList = function() {
              var t3, e3, i2, r2, s2, a2, u2, c3, l2, p2, d2;
              for (t3 = [], u2 = [], a2 = this.getMutationsByType("childList"), e3 = 0, r2 = a2.length; r2 > e3; e3++)
                s2 = a2[e3], t3.push.apply(t3, s2.addedNodes), u2.push.apply(u2, s2.removedNodes);
              return c3 = 0 === t3.length && 1 === u2.length && n(u2[0]), c3 ? (p2 = [], d2 = ["\n"]) : (p2 = h(t3), d2 = h(u2)), { additions: function() {
                var t4, e4, n2;
                for (n2 = [], i2 = t4 = 0, e4 = p2.length; e4 > t4; i2 = ++t4)
                  l2 = p2[i2], l2 !== d2[i2] && n2.push(o(l2));
                return n2;
              }(), deletions: function() {
                var t4, e4, n2;
                for (n2 = [], i2 = t4 = 0, e4 = d2.length; e4 > t4; i2 = ++t4)
                  l2 = d2[i2], l2 !== p2[i2] && n2.push(o(l2));
                return n2;
              }() };
            }, c2.prototype.getTextChangesFromCharacterData = function() {
              var t3, e3, n2, i2, s2, a2, u2, c3;
              return e3 = this.getMutationsByType("characterData"), e3.length && (c3 = e3[0], n2 = e3[e3.length - 1], s2 = o(c3.oldValue), i2 = o(n2.target.data), a2 = r(s2, i2), t3 = a2.added, u2 = a2.removed), { additions: t3 ? [t3] : [], deletions: u2 ? [u2] : [] };
            }, h = function(t3) {
              var e3, n2, i2, o2;
              for (null == t3 && (t3 = []), o2 = [], e3 = 0, n2 = t3.length; n2 > e3; e3++)
                switch (i2 = t3[e3], i2.nodeType) {
                  case Node.TEXT_NODE:
                    o2.push(i2.data);
                    break;
                  case Node.ELEMENT_NODE:
                    "br" === s(i2) ? o2.push("\n") : o2.push.apply(o2, h(i2.childNodes));
                }
              return o2;
            }, c2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.FileVerificationOperation = function(e2) {
            function n2(t3) {
              this.file = t3;
            }
            return t2(n2, e2), n2.prototype.perform = function(t3) {
              var e3;
              return e3 = new FileReader(), e3.onerror = function() {
                return t3(false);
              }, e3.onload = function(n3) {
                return function() {
                  e3.onerror = null;
                  try {
                    e3.abort();
                  } catch (i) {
                  }
                  return t3(true, n3.file);
                };
              }(this), e3.readAsArrayBuffer(this.file);
            }, n2;
          }(e.Operation);
        }.call(this), function() {
          var t2, n, i = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              o.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, o = {}.hasOwnProperty;
          t2 = e.handleEvent, n = e.innerElementIsActive, e.InputController = function(o2) {
            function r(n2) {
              var i2;
              this.element = n2, this.mutationObserver = new e.MutationObserver(this.element), this.mutationObserver.delegate = this;
              for (i2 in this.events)
                t2(i2, { onElement: this.element, withCallback: this.handlerFor(i2) });
            }
            return i(r, o2), r.prototype.events = {}, r.prototype.elementDidMutate = function() {
            }, r.prototype.editorWillSyncDocumentView = function() {
              return this.mutationObserver.stop();
            }, r.prototype.editorDidSyncDocumentView = function() {
              return this.mutationObserver.start();
            }, r.prototype.requestRender = function() {
              var t3;
              return null != (t3 = this.delegate) && "function" == typeof t3.inputControllerDidRequestRender ? t3.inputControllerDidRequestRender() : void 0;
            }, r.prototype.requestReparse = function() {
              var t3;
              return null != (t3 = this.delegate) && "function" == typeof t3.inputControllerDidRequestReparse && t3.inputControllerDidRequestReparse(), this.requestRender();
            }, r.prototype.attachFiles = function(t3) {
              var n2, i2;
              return i2 = function() {
                var i3, o3, r2;
                for (r2 = [], i3 = 0, o3 = t3.length; o3 > i3; i3++)
                  n2 = t3[i3], r2.push(new e.FileVerificationOperation(n2));
                return r2;
              }(), Promise.all(i2).then(function(t4) {
                return function(e2) {
                  return t4.handleInput(function() {
                    var t5, n3;
                    return null != (t5 = this.delegate) && t5.inputControllerWillAttachFiles(), null != (n3 = this.responder) && n3.insertFiles(e2), this.requestRender();
                  });
                };
              }(this));
            }, r.prototype.handlerFor = function(t3) {
              return function(e2) {
                return function(i2) {
                  return i2.defaultPrevented ? void 0 : e2.handleInput(function() {
                    return n(this.element) ? void 0 : (this.eventName = t3, this.events[t3].call(this, i2));
                  });
                };
              }(this);
            }, r.prototype.handleInput = function(t3) {
              var e2, n2;
              try {
                return null != (e2 = this.delegate) && e2.inputControllerWillHandleInput(), t3.call(this);
              } finally {
                null != (n2 = this.delegate) && n2.inputControllerDidHandleInput();
              }
            }, r.prototype.createLinkHTML = function(t3, e2) {
              var n2;
              return n2 = document.createElement("a"), n2.href = t3, n2.textContent = null != e2 ? e2 : t3, n2.outerHTML;
            }, r;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o, r, s, a, u, c, l, h, p, d, f = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              g.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, g = {}.hasOwnProperty, m = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          c = e.makeElement, l = e.objectsAreEqual, d = e.tagName, n = e.browser, a = e.keyEventIsKeyboardCommand, o = e.dataTransferIsWritable, i = e.dataTransferIsPlainText, u = e.config.keyNames, e.Level0InputController = function(n2) {
            function s2() {
              s2.__super__.constructor.apply(this, arguments), this.resetInputSummary();
            }
            var d2;
            return f(s2, n2), d2 = 0, s2.prototype.setInputSummary = function(t3) {
              var e2, n3;
              null == t3 && (t3 = {}), this.inputSummary.eventName = this.eventName;
              for (e2 in t3)
                n3 = t3[e2], this.inputSummary[e2] = n3;
              return this.inputSummary;
            }, s2.prototype.resetInputSummary = function() {
              return this.inputSummary = {};
            }, s2.prototype.reset = function() {
              return this.resetInputSummary(), e.selectionChangeObserver.reset();
            }, s2.prototype.elementDidMutate = function(t3) {
              var e2;
              return this.isComposing() ? null != (e2 = this.delegate) && "function" == typeof e2.inputControllerDidAllowUnhandledInput ? e2.inputControllerDidAllowUnhandledInput() : void 0 : this.handleInput(function() {
                return this.mutationIsSignificant(t3) && (this.mutationIsExpected(t3) ? this.requestRender() : this.requestReparse()), this.reset();
              });
            }, s2.prototype.mutationIsExpected = function(t3) {
              var e2, n3, i2, o2, r2, s3, a2, u2, c2, l2;
              return a2 = t3.textAdded, u2 = t3.textDeleted, this.inputSummary.preferDocument ? true : (e2 = null != a2 ? a2 === this.inputSummary.textAdded : !this.inputSummary.textAdded, n3 = null != u2 ? this.inputSummary.didDelete : !this.inputSummary.didDelete, c2 = ("\n" === a2 || " \n" === a2) && !e2, l2 = "\n" === u2 && !n3, s3 = c2 && !l2 || l2 && !c2, s3 && (o2 = this.getSelectedRange()) && (i2 = c2 ? a2.replace(/\n$/, "").length || -1 : (null != a2 ? a2.length : void 0) || 1, null != (r2 = this.responder) ? r2.positionIsBlockBreak(o2[1] + i2) : void 0) ? true : e2 && n3);
            }, s2.prototype.mutationIsSignificant = function(t3) {
              var e2, n3, i2;
              return i2 = Object.keys(t3).length > 0, e2 = "" === (null != (n3 = this.compositionInput) ? n3.getEndData() : void 0), i2 || !e2;
            }, s2.prototype.events = { keydown: function(t3) {
              var n3, i2, o2, r2, s3, c2, l2, h2, p2;
              if (this.isComposing() || this.resetInputSummary(), this.inputSummary.didInput = true, r2 = u[t3.keyCode]) {
                for (i2 = this.keys, h2 = ["ctrl", "alt", "shift", "meta"], o2 = 0, c2 = h2.length; c2 > o2; o2++)
                  l2 = h2[o2], t3[l2 + "Key"] && ("ctrl" === l2 && (l2 = "control"), i2 = null != i2 ? i2[l2] : void 0);
                null != (null != i2 ? i2[r2] : void 0) && (this.setInputSummary({ keyName: r2 }), e.selectionChangeObserver.reset(), i2[r2].call(this, t3));
              }
              return a(t3) && (n3 = String.fromCharCode(t3.keyCode).toLowerCase()) && (s3 = function() {
                var e2, n4, i3, o3;
                for (i3 = ["alt", "shift"], o3 = [], e2 = 0, n4 = i3.length; n4 > e2; e2++)
                  l2 = i3[e2], t3[l2 + "Key"] && o3.push(l2);
                return o3;
              }(), s3.push(n3), null != (p2 = this.delegate) ? p2.inputControllerDidReceiveKeyboardCommand(s3) : void 0) ? t3.preventDefault() : void 0;
            }, keypress: function(t3) {
              var e2, n3, i2;
              if (null == this.inputSummary.eventName && !t3.metaKey && (!t3.ctrlKey || t3.altKey))
                return (i2 = p(t3)) ? (null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), null != (n3 = this.responder) && n3.insertString(i2), this.setInputSummary({ textAdded: i2, didDelete: this.selectionIsExpanded() })) : void 0;
            }, textInput: function(t3) {
              var e2, n3, i2, o2;
              return e2 = t3.data, o2 = this.inputSummary.textAdded, o2 && o2 !== e2 && o2.toUpperCase() === e2 ? (n3 = this.getSelectedRange(), this.setSelectedRange([n3[0], n3[1] + o2.length]), null != (i2 = this.responder) && i2.insertString(e2), this.setInputSummary({ textAdded: e2 }), this.setSelectedRange(n3)) : void 0;
            }, dragenter: function(t3) {
              return t3.preventDefault();
            }, dragstart: function(t3) {
              var e2, n3;
              return n3 = t3.target, this.serializeSelectionToDataTransfer(t3.dataTransfer), this.draggedRange = this.getSelectedRange(), null != (e2 = this.delegate) && "function" == typeof e2.inputControllerDidStartDrag ? e2.inputControllerDidStartDrag() : void 0;
            }, dragover: function(t3) {
              var e2, n3;
              return !this.draggedRange && !this.canAcceptDataTransfer(t3.dataTransfer) || (t3.preventDefault(), e2 = { x: t3.clientX, y: t3.clientY }, l(e2, this.draggingPoint)) ? void 0 : (this.draggingPoint = e2, null != (n3 = this.delegate) && "function" == typeof n3.inputControllerDidReceiveDragOverPoint ? n3.inputControllerDidReceiveDragOverPoint(this.draggingPoint) : void 0);
            }, dragend: function() {
              var t3;
              return null != (t3 = this.delegate) && "function" == typeof t3.inputControllerDidCancelDrag && t3.inputControllerDidCancelDrag(), this.draggedRange = null, this.draggingPoint = null;
            }, drop: function(t3) {
              var n3, i2, o2, r2, s3, a2, u2, c2, l2;
              return t3.preventDefault(), o2 = null != (s3 = t3.dataTransfer) ? s3.files : void 0, r2 = { x: t3.clientX, y: t3.clientY }, null != (a2 = this.responder) && a2.setLocationRangeFromPointRange(r2), (null != o2 ? o2.length : void 0) ? this.attachFiles(o2) : this.draggedRange ? (null != (u2 = this.delegate) && u2.inputControllerWillMoveText(), null != (c2 = this.responder) && c2.moveTextFromRange(this.draggedRange), this.draggedRange = null, this.requestRender()) : (i2 = t3.dataTransfer.getData("application/x-trix-document")) && (n3 = e.Document.fromJSONString(i2), null != (l2 = this.responder) && l2.insertDocument(n3), this.requestRender()), this.draggedRange = null, this.draggingPoint = null;
            }, cut: function(t3) {
              var e2, n3;
              return (null != (e2 = this.responder) ? e2.selectionIsExpanded() : void 0) && (this.serializeSelectionToDataTransfer(t3.clipboardData) && t3.preventDefault(), null != (n3 = this.delegate) && n3.inputControllerWillCutText(), this.deleteInDirection("backward"), t3.defaultPrevented) ? this.requestRender() : void 0;
            }, copy: function(t3) {
              var e2;
              return (null != (e2 = this.responder) ? e2.selectionIsExpanded() : void 0) && this.serializeSelectionToDataTransfer(t3.clipboardData) ? t3.preventDefault() : void 0;
            }, paste: function(t3) {
              var n3, o2, s3, a2, u2, c2, l2, p2, f2, g2, v, y, b, A, C, x, w, E, S, R, k, D, L;
              return n3 = null != (p2 = t3.clipboardData) ? p2 : t3.testClipboardData, l2 = { clipboard: n3 }, null == n3 || h(t3) ? void this.getPastedHTMLUsingHiddenElement(function(t4) {
                return function(e2) {
                  var n4, i2, o3;
                  return l2.type = "text/html", l2.html = e2, null != (n4 = t4.delegate) && n4.inputControllerWillPaste(l2), null != (i2 = t4.responder) && i2.insertHTML(l2.html), t4.requestRender(), null != (o3 = t4.delegate) ? o3.inputControllerDidPaste(l2) : void 0;
                };
              }(this)) : ((a2 = n3.getData("URL")) ? (l2.type = "text/html", L = (c2 = n3.getData("public.url-name")) ? e.squishBreakableWhitespace(c2).trim() : a2, l2.html = this.createLinkHTML(a2, L), null != (f2 = this.delegate) && f2.inputControllerWillPaste(l2), this.setInputSummary({ textAdded: L, didDelete: this.selectionIsExpanded() }), null != (C = this.responder) && C.insertHTML(l2.html), this.requestRender(), null != (x = this.delegate) && x.inputControllerDidPaste(l2)) : i(n3) ? (l2.type = "text/plain", l2.string = n3.getData("text/plain"), null != (w = this.delegate) && w.inputControllerWillPaste(l2), this.setInputSummary({ textAdded: l2.string, didDelete: this.selectionIsExpanded() }), null != (E = this.responder) && E.insertString(l2.string), this.requestRender(), null != (S = this.delegate) && S.inputControllerDidPaste(l2)) : (u2 = n3.getData("text/html")) ? (l2.type = "text/html", l2.html = u2, null != (R = this.delegate) && R.inputControllerWillPaste(l2), null != (k = this.responder) && k.insertHTML(l2.html), this.requestRender(), null != (D = this.delegate) && D.inputControllerDidPaste(l2)) : m.call(n3.types, "Files") >= 0 && (s3 = null != (g2 = n3.items) && null != (v = g2[0]) && "function" == typeof v.getAsFile ? v.getAsFile() : void 0) && (!s3.name && (o2 = r(s3)) && (s3.name = "pasted-file-" + ++d2 + "." + o2), l2.type = "File", l2.file = s3, null != (y = this.delegate) && y.inputControllerWillAttachFiles(), null != (b = this.responder) && b.insertFile(l2.file), this.requestRender(), null != (A = this.delegate) && A.inputControllerDidPaste(l2)), t3.preventDefault());
            }, compositionstart: function(t3) {
              return this.getCompositionInput().start(t3.data);
            }, compositionupdate: function(t3) {
              return this.getCompositionInput().update(t3.data);
            }, compositionend: function(t3) {
              return this.getCompositionInput().end(t3.data);
            }, beforeinput: function() {
              return this.inputSummary.didInput = true;
            }, input: function(t3) {
              return this.inputSummary.didInput = true, t3.stopPropagation();
            } }, s2.prototype.keys = { backspace: function(t3) {
              var e2;
              return null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), this.deleteInDirection("backward", t3);
            }, "delete": function(t3) {
              var e2;
              return null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), this.deleteInDirection("forward", t3);
            }, "return": function() {
              var t3, e2;
              return this.setInputSummary({ preferDocument: true }), null != (t3 = this.delegate) && t3.inputControllerWillPerformTyping(), null != (e2 = this.responder) ? e2.insertLineBreak() : void 0;
            }, tab: function(t3) {
              var e2, n3;
              return (null != (e2 = this.responder) ? e2.canIncreaseNestingLevel() : void 0) ? (null != (n3 = this.responder) && n3.increaseNestingLevel(), this.requestRender(), t3.preventDefault()) : void 0;
            }, left: function(t3) {
              var e2;
              return this.selectionIsInCursorTarget() ? (t3.preventDefault(), null != (e2 = this.responder) ? e2.moveCursorInDirection("backward") : void 0) : void 0;
            }, right: function(t3) {
              var e2;
              return this.selectionIsInCursorTarget() ? (t3.preventDefault(), null != (e2 = this.responder) ? e2.moveCursorInDirection("forward") : void 0) : void 0;
            }, control: { d: function(t3) {
              var e2;
              return null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), this.deleteInDirection("forward", t3);
            }, h: function(t3) {
              var e2;
              return null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), this.deleteInDirection("backward", t3);
            }, o: function(t3) {
              var e2, n3;
              return t3.preventDefault(), null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), null != (n3 = this.responder) && n3.insertString("\n", { updatePosition: false }), this.requestRender();
            } }, shift: { "return": function(t3) {
              var e2, n3;
              return null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), null != (n3 = this.responder) && n3.insertString("\n"), this.requestRender(), t3.preventDefault();
            }, tab: function(t3) {
              var e2, n3;
              return (null != (e2 = this.responder) ? e2.canDecreaseNestingLevel() : void 0) ? (null != (n3 = this.responder) && n3.decreaseNestingLevel(), this.requestRender(), t3.preventDefault()) : void 0;
            }, left: function(t3) {
              return this.selectionIsInCursorTarget() ? (t3.preventDefault(), this.expandSelectionInDirection("backward")) : void 0;
            }, right: function(t3) {
              return this.selectionIsInCursorTarget() ? (t3.preventDefault(), this.expandSelectionInDirection("forward")) : void 0;
            } }, alt: { backspace: function() {
              var t3;
              return this.setInputSummary({ preferDocument: false }), null != (t3 = this.delegate) ? t3.inputControllerWillPerformTyping() : void 0;
            } }, meta: { backspace: function() {
              var t3;
              return this.setInputSummary({ preferDocument: false }), null != (t3 = this.delegate) ? t3.inputControllerWillPerformTyping() : void 0;
            } } }, s2.prototype.getCompositionInput = function() {
              return this.isComposing() ? this.compositionInput : this.compositionInput = new t2(this);
            }, s2.prototype.isComposing = function() {
              return null != this.compositionInput && !this.compositionInput.isEnded();
            }, s2.prototype.deleteInDirection = function(t3, e2) {
              var n3;
              return (null != (n3 = this.responder) ? n3.deleteInDirection(t3) : void 0) !== false ? this.setInputSummary({ didDelete: true }) : e2 ? (e2.preventDefault(), this.requestRender()) : void 0;
            }, s2.prototype.serializeSelectionToDataTransfer = function(t3) {
              var n3, i2;
              if (o(t3))
                return n3 = null != (i2 = this.responder) ? i2.getSelectedDocument().toSerializableDocument() : void 0, t3.setData("application/x-trix-document", JSON.stringify(n3)), t3.setData("text/html", e.DocumentView.render(n3).innerHTML), t3.setData("text/plain", n3.toString().replace(/\n$/, "")), true;
            }, s2.prototype.canAcceptDataTransfer = function(t3) {
              var e2, n3, i2, o2, r2, s3;
              for (s3 = {}, o2 = null != (i2 = null != t3 ? t3.types : void 0) ? i2 : [], e2 = 0, n3 = o2.length; n3 > e2; e2++)
                r2 = o2[e2], s3[r2] = true;
              return s3.Files || s3["application/x-trix-document"] || s3["text/html"] || s3["text/plain"];
            }, s2.prototype.getPastedHTMLUsingHiddenElement = function(t3) {
              var n3, i2, o2;
              return i2 = this.getSelectedRange(), o2 = { position: "absolute", left: window.pageXOffset + "px", top: window.pageYOffset + "px", opacity: 0 }, n3 = c({ style: o2, tagName: "div", editable: true }), document.body.appendChild(n3), n3.focus(), requestAnimationFrame(function(o3) {
                return function() {
                  var r2;
                  return r2 = n3.innerHTML, e.removeNode(n3), o3.setSelectedRange(i2), t3(r2);
                };
              }(this));
            }, s2.proxyMethod("responder?.getSelectedRange"), s2.proxyMethod("responder?.setSelectedRange"), s2.proxyMethod("responder?.expandSelectionInDirection"), s2.proxyMethod("responder?.selectionIsInCursorTarget"), s2.proxyMethod("responder?.selectionIsExpanded"), s2;
          }(e.InputController), r = function(t3) {
            var e2, n2;
            return null != (e2 = t3.type) && null != (n2 = e2.match(/\/(\w+)$/)) ? n2[1] : void 0;
          }, s = null != ("function" == typeof " ".codePointAt ? " ".codePointAt(0) : void 0), p = function(t3) {
            var n2;
            return t3.key && s && t3.key.codePointAt(0) === t3.keyCode ? t3.key : (null === t3.which ? n2 = t3.keyCode : 0 !== t3.which && 0 !== t3.charCode && (n2 = t3.charCode), null != n2 && "escape" !== u[n2] ? e.UTF16String.fromCodepoints([n2]).toString() : void 0);
          }, h = function(t3) {
            var e2, n2, i2, o2, r2, s2, a2, u2, c2, l2;
            if (u2 = t3.clipboardData) {
              if (m.call(u2.types, "text/html") >= 0) {
                for (c2 = u2.types, i2 = 0, s2 = c2.length; s2 > i2; i2++)
                  if (l2 = c2[i2], e2 = /^CorePasteboardFlavorType/.test(l2), n2 = /^dyn\./.test(l2) && u2.getData(l2), a2 = e2 || n2)
                    return true;
                return false;
              }
              return o2 = m.call(u2.types, "com.apple.webarchive") >= 0, r2 = m.call(u2.types, "com.apple.flat-rtfd") >= 0, o2 || r2;
            }
          }, t2 = function(t3) {
            function e2(t4) {
              var e3;
              this.inputController = t4, e3 = this.inputController, this.responder = e3.responder, this.delegate = e3.delegate, this.inputSummary = e3.inputSummary, this.data = {};
            }
            return f(e2, t3), e2.prototype.start = function(t4) {
              var e3, n2;
              return this.data.start = t4, this.isSignificant() ? ("keypress" === this.inputSummary.eventName && this.inputSummary.textAdded && null != (e3 = this.responder) && e3.deleteInDirection("left"), this.selectionIsExpanded() || (this.insertPlaceholder(), this.requestRender()), this.range = null != (n2 = this.responder) ? n2.getSelectedRange() : void 0) : void 0;
            }, e2.prototype.update = function(t4) {
              var e3;
              return this.data.update = t4, this.isSignificant() && (e3 = this.selectPlaceholder()) ? (this.forgetPlaceholder(), this.range = e3) : void 0;
            }, e2.prototype.end = function(t4) {
              var e3, n2, i2, o2;
              return this.data.end = t4, this.isSignificant() ? (this.forgetPlaceholder(), this.canApplyToDocument() ? (this.setInputSummary({ preferDocument: true, didInput: false }), null != (e3 = this.delegate) && e3.inputControllerWillPerformTyping(), null != (n2 = this.responder) && n2.setSelectedRange(this.range), null != (i2 = this.responder) && i2.insertString(this.data.end), null != (o2 = this.responder) ? o2.setSelectedRange(this.range[0] + this.data.end.length) : void 0) : null != this.data.start || null != this.data.update ? (this.requestReparse(), this.inputController.reset()) : void 0) : this.inputController.reset();
            }, e2.prototype.getEndData = function() {
              return this.data.end;
            }, e2.prototype.isEnded = function() {
              return null != this.getEndData();
            }, e2.prototype.isSignificant = function() {
              return n.composesExistingText ? this.inputSummary.didInput : true;
            }, e2.prototype.canApplyToDocument = function() {
              var t4, e3;
              return 0 === (null != (t4 = this.data.start) ? t4.length : void 0) && (null != (e3 = this.data.end) ? e3.length : void 0) > 0 && null != this.range;
            }, e2.proxyMethod("inputController.setInputSummary"), e2.proxyMethod("inputController.requestRender"), e2.proxyMethod("inputController.requestReparse"), e2.proxyMethod("responder?.selectionIsExpanded"), e2.proxyMethod("responder?.insertPlaceholder"), e2.proxyMethod("responder?.selectPlaceholder"), e2.proxyMethod("responder?.forgetPlaceholder"), e2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, r = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              s.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, s = {}.hasOwnProperty, a = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          t2 = e.dataTransferIsPlainText, n = e.keyEventIsKeyboardCommand, i = e.objectsAreEqual, e.Level2InputController = function(s2) {
            function u() {
              return this.render = o(this.render, this), u.__super__.constructor.apply(this, arguments);
            }
            var c, l, h, p, d, f;
            return r(u, s2), u.prototype.elementDidMutate = function() {
              var t3;
              return this.scheduledRender ? this.composing && null != (t3 = this.delegate) && "function" == typeof t3.inputControllerDidAllowUnhandledInput ? t3.inputControllerDidAllowUnhandledInput() : void 0 : this.reparse();
            }, u.prototype.scheduleRender = function() {
              return null != this.scheduledRender ? this.scheduledRender : this.scheduledRender = requestAnimationFrame(this.render);
            }, u.prototype.render = function() {
              var t3;
              return cancelAnimationFrame(this.scheduledRender), this.scheduledRender = null, this.composing || null != (t3 = this.delegate) && t3.render(), "function" == typeof this.afterRender && this.afterRender(), this.afterRender = null;
            }, u.prototype.reparse = function() {
              var t3;
              return null != (t3 = this.delegate) ? t3.reparse() : void 0;
            }, u.prototype.events = { keydown: function(t3) {
              var e2, i2, o2, r2;
              if (n(t3)) {
                if (e2 = l(t3), null != (r2 = this.delegate) ? r2.inputControllerDidReceiveKeyboardCommand(e2) : void 0)
                  return t3.preventDefault();
              } else if (o2 = t3.key, t3.altKey && (o2 += "+Alt"), t3.shiftKey && (o2 += "+Shift"), i2 = this.keys[o2])
                return this.withEvent(t3, i2);
            }, paste: function(t3) {
              var e2, n2, i2, o2, r2, s3, a2, u2, c2;
              return h(t3) ? (t3.preventDefault(), this.attachFiles(t3.clipboardData.files)) : p(t3) ? (t3.preventDefault(), n2 = { type: "text/plain", string: t3.clipboardData.getData("text/plain") }, null != (i2 = this.delegate) && i2.inputControllerWillPaste(n2), null != (o2 = this.responder) && o2.insertString(n2.string), this.render(), null != (r2 = this.delegate) ? r2.inputControllerDidPaste(n2) : void 0) : (e2 = null != (s3 = t3.clipboardData) ? s3.getData("URL") : void 0) ? (t3.preventDefault(), n2 = { type: "text/html", html: this.createLinkHTML(e2) }, null != (a2 = this.delegate) && a2.inputControllerWillPaste(n2), null != (u2 = this.responder) && u2.insertHTML(n2.html), this.render(), null != (c2 = this.delegate) ? c2.inputControllerDidPaste(n2) : void 0) : void 0;
            }, beforeinput: function(t3) {
              var e2;
              return (e2 = this.inputTypes[t3.inputType]) ? (this.withEvent(t3, e2), this.scheduleRender()) : void 0;
            }, input: function() {
              return e.selectionChangeObserver.reset();
            }, dragstart: function(t3) {
              var e2, n2;
              return (null != (e2 = this.responder) ? e2.selectionContainsAttachments() : void 0) ? (t3.dataTransfer.setData("application/x-trix-dragging", true), this.dragging = { range: null != (n2 = this.responder) ? n2.getSelectedRange() : void 0, point: d(t3) }) : void 0;
            }, dragenter: function(t3) {
              return c(t3) ? t3.preventDefault() : void 0;
            }, dragover: function(t3) {
              var e2, n2;
              if (this.dragging) {
                if (t3.preventDefault(), e2 = d(t3), !i(e2, this.dragging.point))
                  return this.dragging.point = e2, null != (n2 = this.responder) ? n2.setLocationRangeFromPointRange(e2) : void 0;
              } else if (c(t3))
                return t3.preventDefault();
            }, drop: function(t3) {
              var e2, n2, i2, o2;
              return this.dragging ? (t3.preventDefault(), null != (n2 = this.delegate) && n2.inputControllerWillMoveText(), null != (i2 = this.responder) && i2.moveTextFromRange(this.dragging.range), this.dragging = null, this.scheduleRender()) : c(t3) ? (t3.preventDefault(), e2 = d(t3), null != (o2 = this.responder) && o2.setLocationRangeFromPointRange(e2), this.attachFiles(t3.dataTransfer.files)) : void 0;
            }, dragend: function() {
              var t3;
              return this.dragging ? (null != (t3 = this.responder) && t3.setSelectedRange(this.dragging.range), this.dragging = null) : void 0;
            }, compositionend: function() {
              return this.composing ? (this.composing = false, this.scheduleRender()) : void 0;
            } }, u.prototype.keys = { ArrowLeft: function() {
              var t3, e2;
              return (null != (t3 = this.responder) ? t3.shouldManageMovingCursorInDirection("backward") : void 0) ? (this.event.preventDefault(), null != (e2 = this.responder) ? e2.moveCursorInDirection("backward") : void 0) : void 0;
            }, ArrowRight: function() {
              var t3, e2;
              return (null != (t3 = this.responder) ? t3.shouldManageMovingCursorInDirection("forward") : void 0) ? (this.event.preventDefault(), null != (e2 = this.responder) ? e2.moveCursorInDirection("forward") : void 0) : void 0;
            }, Backspace: function() {
              var t3, e2, n2;
              return (null != (t3 = this.responder) ? t3.shouldManageDeletingInDirection("backward") : void 0) ? (this.event.preventDefault(), null != (e2 = this.delegate) && e2.inputControllerWillPerformTyping(), null != (n2 = this.responder) && n2.deleteInDirection("backward"), this.render()) : void 0;
            }, Tab: function() {
              var t3, e2;
              return (null != (t3 = this.responder) ? t3.canIncreaseNestingLevel() : void 0) ? (this.event.preventDefault(), null != (e2 = this.responder) && e2.increaseNestingLevel(), this.render()) : void 0;
            }, "Tab+Shift": function() {
              var t3, e2;
              return (null != (t3 = this.responder) ? t3.canDecreaseNestingLevel() : void 0) ? (this.event.preventDefault(), null != (e2 = this.responder) && e2.decreaseNestingLevel(), this.render()) : void 0;
            } }, u.prototype.inputTypes = { deleteByComposition: function() {
              return this.deleteInDirection("backward", { recordUndoEntry: false });
            }, deleteByCut: function() {
              return this.deleteInDirection("backward");
            }, deleteByDrag: function() {
              return this.event.preventDefault(), this.withTargetDOMRange(function() {
                var t3;
                return this.deleteByDragRange = null != (t3 = this.responder) ? t3.getSelectedRange() : void 0;
              });
            }, deleteCompositionText: function() {
              return this.deleteInDirection("backward", { recordUndoEntry: false });
            }, deleteContent: function() {
              return this.deleteInDirection("backward");
            }, deleteContentBackward: function() {
              return this.deleteInDirection("backward");
            }, deleteContentForward: function() {
              return this.deleteInDirection("forward");
            }, deleteEntireSoftLine: function() {
              return this.deleteInDirection("forward");
            }, deleteHardLineBackward: function() {
              return this.deleteInDirection("backward");
            }, deleteHardLineForward: function() {
              return this.deleteInDirection("forward");
            }, deleteSoftLineBackward: function() {
              return this.deleteInDirection("backward");
            }, deleteSoftLineForward: function() {
              return this.deleteInDirection("forward");
            }, deleteWordBackward: function() {
              return this.deleteInDirection("backward");
            }, deleteWordForward: function() {
              return this.deleteInDirection("forward");
            }, formatBackColor: function() {
              return this.activateAttributeIfSupported("backgroundColor", this.event.data);
            }, formatBold: function() {
              return this.toggleAttributeIfSupported("bold");
            }, formatFontColor: function() {
              return this.activateAttributeIfSupported("color", this.event.data);
            }, formatFontName: function() {
              return this.activateAttributeIfSupported("font", this.event.data);
            }, formatIndent: function() {
              var t3;
              return (null != (t3 = this.responder) ? t3.canIncreaseNestingLevel() : void 0) ? this.withTargetDOMRange(function() {
                var t4;
                return null != (t4 = this.responder) ? t4.increaseNestingLevel() : void 0;
              }) : void 0;
            }, formatItalic: function() {
              return this.toggleAttributeIfSupported("italic");
            }, formatJustifyCenter: function() {
              return this.toggleAttributeIfSupported("justifyCenter");
            }, formatJustifyFull: function() {
              return this.toggleAttributeIfSupported("justifyFull");
            }, formatJustifyLeft: function() {
              return this.toggleAttributeIfSupported("justifyLeft");
            }, formatJustifyRight: function() {
              return this.toggleAttributeIfSupported("justifyRight");
            }, formatOutdent: function() {
              var t3;
              return (null != (t3 = this.responder) ? t3.canDecreaseNestingLevel() : void 0) ? this.withTargetDOMRange(function() {
                var t4;
                return null != (t4 = this.responder) ? t4.decreaseNestingLevel() : void 0;
              }) : void 0;
            }, formatRemove: function() {
              return this.withTargetDOMRange(function() {
                var t3, e2, n2, i2;
                i2 = [];
                for (t3 in null != (e2 = this.responder) ? e2.getCurrentAttributes() : void 0)
                  i2.push(null != (n2 = this.responder) ? n2.removeCurrentAttribute(t3) : void 0);
                return i2;
              });
            }, formatSetBlockTextDirection: function() {
              return this.activateAttributeIfSupported("blockDir", this.event.data);
            }, formatSetInlineTextDirection: function() {
              return this.activateAttributeIfSupported("textDir", this.event.data);
            }, formatStrikeThrough: function() {
              return this.toggleAttributeIfSupported("strike");
            }, formatSubscript: function() {
              return this.toggleAttributeIfSupported("sub");
            }, formatSuperscript: function() {
              return this.toggleAttributeIfSupported("sup");
            }, formatUnderline: function() {
              return this.toggleAttributeIfSupported("underline");
            }, historyRedo: function() {
              var t3;
              return null != (t3 = this.delegate) ? t3.inputControllerWillPerformRedo() : void 0;
            }, historyUndo: function() {
              var t3;
              return null != (t3 = this.delegate) ? t3.inputControllerWillPerformUndo() : void 0;
            }, insertCompositionText: function() {
              return this.composing = true, this.insertString(this.event.data);
            }, insertFromComposition: function() {
              return this.composing = false, this.insertString(this.event.data);
            }, insertFromDrop: function() {
              var t3, e2;
              return (t3 = this.deleteByDragRange) ? (this.deleteByDragRange = null, null != (e2 = this.delegate) && e2.inputControllerWillMoveText(), this.withTargetDOMRange(function() {
                var e3;
                return null != (e3 = this.responder) ? e3.moveTextFromRange(t3) : void 0;
              })) : void 0;
            }, insertFromPaste: function() {
              var n2, i2, o2, r2, s3, a2, u2, c2, l2, h2, p2;
              return n2 = this.event.dataTransfer, s3 = { dataTransfer: n2 }, (i2 = n2.getData("URL")) ? (this.event.preventDefault(), s3.type = "text/html", p2 = (r2 = n2.getData("public.url-name")) ? e.squishBreakableWhitespace(r2).trim() : i2, s3.html = this.createLinkHTML(i2, p2), null != (a2 = this.delegate) && a2.inputControllerWillPaste(s3), this.withTargetDOMRange(function() {
                var t3;
                return null != (t3 = this.responder) ? t3.insertHTML(s3.html) : void 0;
              }), this.afterRender = function(t3) {
                return function() {
                  var e2;
                  return null != (e2 = t3.delegate) ? e2.inputControllerDidPaste(s3) : void 0;
                };
              }(this)) : t2(n2) ? (s3.type = "text/plain", s3.string = n2.getData("text/plain"), null != (u2 = this.delegate) && u2.inputControllerWillPaste(s3), this.withTargetDOMRange(function() {
                var t3;
                return null != (t3 = this.responder) ? t3.insertString(s3.string) : void 0;
              }), this.afterRender = function(t3) {
                return function() {
                  var e2;
                  return null != (e2 = t3.delegate) ? e2.inputControllerDidPaste(s3) : void 0;
                };
              }(this)) : (o2 = n2.getData("text/html")) ? (this.event.preventDefault(), s3.type = "text/html", s3.html = o2, null != (c2 = this.delegate) && c2.inputControllerWillPaste(s3), this.withTargetDOMRange(function() {
                var t3;
                return null != (t3 = this.responder) ? t3.insertHTML(s3.html) : void 0;
              }), this.afterRender = function(t3) {
                return function() {
                  var e2;
                  return null != (e2 = t3.delegate) ? e2.inputControllerDidPaste(s3) : void 0;
                };
              }(this)) : (null != (l2 = n2.files) ? l2.length : void 0) ? (s3.type = "File", s3.file = n2.files[0], null != (h2 = this.delegate) && h2.inputControllerWillPaste(s3), this.withTargetDOMRange(function() {
                var t3;
                return null != (t3 = this.responder) ? t3.insertFile(s3.file) : void 0;
              }), this.afterRender = function(t3) {
                return function() {
                  var e2;
                  return null != (e2 = t3.delegate) ? e2.inputControllerDidPaste(s3) : void 0;
                };
              }(this)) : void 0;
            }, insertFromYank: function() {
              return this.insertString(this.event.data);
            }, insertLineBreak: function() {
              return this.insertString("\n");
            }, insertLink: function() {
              return this.activateAttributeIfSupported("href", this.event.data);
            }, insertOrderedList: function() {
              return this.toggleAttributeIfSupported("number");
            }, insertParagraph: function() {
              var t3;
              return null != (t3 = this.delegate) && t3.inputControllerWillPerformTyping(), this.withTargetDOMRange(function() {
                var t4;
                return null != (t4 = this.responder) ? t4.insertLineBreak() : void 0;
              });
            }, insertReplacementText: function() {
              return this.insertString(this.event.dataTransfer.getData("text/plain"), { updatePosition: false });
            }, insertText: function() {
              var t3, e2;
              return this.insertString(null != (t3 = this.event.data) ? t3 : null != (e2 = this.event.dataTransfer) ? e2.getData("text/plain") : void 0);
            }, insertTranspose: function() {
              return this.insertString(this.event.data);
            }, insertUnorderedList: function() {
              return this.toggleAttributeIfSupported("bullet");
            } }, u.prototype.insertString = function(t3, e2) {
              var n2;
              return null == t3 && (t3 = ""), null != (n2 = this.delegate) && n2.inputControllerWillPerformTyping(), this.withTargetDOMRange(function() {
                var n3;
                return null != (n3 = this.responder) ? n3.insertString(t3, e2) : void 0;
              });
            }, u.prototype.toggleAttributeIfSupported = function(t3) {
              var n2;
              return a.call(e.getAllAttributeNames(), t3) >= 0 ? (null != (n2 = this.delegate) && n2.inputControllerWillPerformFormatting(t3), this.withTargetDOMRange(function() {
                var e2;
                return null != (e2 = this.responder) ? e2.toggleCurrentAttribute(t3) : void 0;
              })) : void 0;
            }, u.prototype.activateAttributeIfSupported = function(t3, n2) {
              var i2;
              return a.call(e.getAllAttributeNames(), t3) >= 0 ? (null != (i2 = this.delegate) && i2.inputControllerWillPerformFormatting(t3), this.withTargetDOMRange(function() {
                var e2;
                return null != (e2 = this.responder) ? e2.setCurrentAttribute(t3, n2) : void 0;
              })) : void 0;
            }, u.prototype.deleteInDirection = function(t3, e2) {
              var n2, i2, o2, r2;
              return o2 = (null != e2 ? e2 : { recordUndoEntry: true }).recordUndoEntry, o2 && null != (r2 = this.delegate) && r2.inputControllerWillPerformTyping(), i2 = function(e3) {
                return function() {
                  var n3;
                  return null != (n3 = e3.responder) ? n3.deleteInDirection(t3) : void 0;
                };
              }(this), (n2 = this.getTargetDOMRange({ minLength: 2 })) ? this.withTargetDOMRange(n2, i2) : i2();
            }, u.prototype.withTargetDOMRange = function(t3, n2) {
              var i2;
              return "function" == typeof t3 && (n2 = t3, t3 = this.getTargetDOMRange()), t3 ? null != (i2 = this.responder) ? i2.withTargetDOMRange(t3, n2.bind(this)) : void 0 : (e.selectionChangeObserver.reset(), n2.call(this));
            }, u.prototype.getTargetDOMRange = function(t3) {
              var e2, n2, i2, o2;
              return i2 = (null != t3 ? t3 : { minLength: 0 }).minLength, (o2 = "function" == typeof (e2 = this.event).getTargetRanges ? e2.getTargetRanges() : void 0) && o2.length && (n2 = f(o2[0]), 0 === i2 || n2.toString().length >= i2) ? n2 : void 0;
            }, f = function(t3) {
              var e2;
              return e2 = document.createRange(), e2.setStart(t3.startContainer, t3.startOffset), e2.setEnd(t3.endContainer, t3.endOffset), e2;
            }, u.prototype.withEvent = function(t3, e2) {
              var n2;
              this.event = t3;
              try {
                n2 = e2.call(this);
              } finally {
                this.event = null;
              }
              return n2;
            }, c = function(t3) {
              var e2, n2;
              return a.call(null != (e2 = null != (n2 = t3.dataTransfer) ? n2.types : void 0) ? e2 : [], "Files") >= 0;
            }, h = function(t3) {
              var e2;
              return (e2 = t3.clipboardData) ? a.call(e2.types, "Files") >= 0 && 1 === e2.types.length && e2.files.length >= 1 : void 0;
            }, p = function(t3) {
              var e2;
              return (e2 = t3.clipboardData) ? a.call(e2.types, "text/plain") >= 0 && 1 === e2.types.length : void 0;
            }, l = function(t3) {
              var e2;
              return e2 = [], t3.altKey && e2.push("alt"), t3.shiftKey && e2.push("shift"), e2.push(t3.key), e2;
            }, d = function(t3) {
              return { x: t3.clientX, y: t3.clientY };
            }, u;
          }(e.InputController);
        }.call(this), function() {
          var t2, n, i, o, r, s, a, u, c = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, l = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              h.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, h = {}.hasOwnProperty;
          n = e.defer, i = e.handleEvent, s = e.makeElement, u = e.tagName, a = e.config, r = a.lang, t2 = a.css, o = a.keyNames, e.AttachmentEditorController = function(a2) {
            function h2(t3, e2, n2, i2) {
              this.attachmentPiece = t3, this.element = e2, this.container = n2, this.options = null != i2 ? i2 : {}, this.didBlurCaption = c(this.didBlurCaption, this), this.didChangeCaption = c(this.didChangeCaption, this), this.didInputCaption = c(this.didInputCaption, this), this.didKeyDownCaption = c(this.didKeyDownCaption, this), this.didClickActionButton = c(this.didClickActionButton, this), this.didClickToolbar = c(this.didClickToolbar, this), this.attachment = this.attachmentPiece.attachment, "a" === u(this.element) && (this.element = this.element.firstChild), this.install();
            }
            var p;
            return l(h2, a2), p = function(t3) {
              return function() {
                var e2;
                return e2 = t3.apply(this, arguments), e2["do"](), null == this.undos && (this.undos = []), this.undos.push(e2.undo);
              };
            }, h2.prototype.install = function() {
              return this.makeElementMutable(), this.addToolbar(), this.attachment.isPreviewable() ? this.installCaptionEditor() : void 0;
            }, h2.prototype.uninstall = function() {
              var t3, e2;
              for (this.savePendingCaption(); e2 = this.undos.pop(); )
                e2();
              return null != (t3 = this.delegate) ? t3.didUninstallAttachmentEditor(this) : void 0;
            }, h2.prototype.savePendingCaption = function() {
              var t3, e2, n2;
              return null != this.pendingCaption ? (t3 = this.pendingCaption, this.pendingCaption = null, t3 ? null != (e2 = this.delegate) && "function" == typeof e2.attachmentEditorDidRequestUpdatingAttributesForAttachment ? e2.attachmentEditorDidRequestUpdatingAttributesForAttachment({ caption: t3 }, this.attachment) : void 0 : null != (n2 = this.delegate) && "function" == typeof n2.attachmentEditorDidRequestRemovingAttributeForAttachment ? n2.attachmentEditorDidRequestRemovingAttributeForAttachment("caption", this.attachment) : void 0) : void 0;
            }, h2.prototype.makeElementMutable = p(function() {
              return { "do": function(t3) {
                return function() {
                  return t3.element.dataset.trixMutable = true;
                };
              }(this), undo: function(t3) {
                return function() {
                  return delete t3.element.dataset.trixMutable;
                };
              }(this) };
            }), h2.prototype.addToolbar = p(function() {
              var n2;
              return n2 = s({ tagName: "div", className: t2.attachmentToolbar, data: { trixMutable: true }, childNodes: s({ tagName: "div", className: "trix-button-row", childNodes: s({ tagName: "span", className: "trix-button-group trix-button-group--actions", childNodes: s({ tagName: "button", className: "trix-button trix-button--remove", textContent: r.remove, attributes: { title: r.remove }, data: { trixAction: "remove" } }) }) }) }), this.attachment.isPreviewable() && n2.appendChild(s({ tagName: "div", className: t2.attachmentMetadataContainer, childNodes: s({ tagName: "span", className: t2.attachmentMetadata, childNodes: [s({ tagName: "span", className: t2.attachmentName, textContent: this.attachment.getFilename(), attributes: { title: this.attachment.getFilename() } }), s({ tagName: "span", className: t2.attachmentSize, textContent: this.attachment.getFormattedFilesize() })] }) })), i("click", { onElement: n2, withCallback: this.didClickToolbar }), i("click", { onElement: n2, matchingSelector: "[data-trix-action]", withCallback: this.didClickActionButton }), { "do": function(t3) {
                return function() {
                  return t3.element.appendChild(n2);
                };
              }(this), undo: function() {
                return function() {
                  return e.removeNode(n2);
                };
              }(this) };
            }), h2.prototype.installCaptionEditor = p(function() {
              var o2, a3, u2, c2, l2;
              return c2 = s({ tagName: "textarea", className: t2.attachmentCaptionEditor, attributes: { placeholder: r.captionPlaceholder }, data: { trixMutable: true } }), c2.value = this.attachmentPiece.getCaption(), l2 = c2.cloneNode(), l2.classList.add("trix-autoresize-clone"), l2.tabIndex = -1, o2 = function() {
                return l2.value = c2.value, c2.style.height = l2.scrollHeight + "px";
              }, i("input", { onElement: c2, withCallback: o2 }), i("input", { onElement: c2, withCallback: this.didInputCaption }), i("keydown", { onElement: c2, withCallback: this.didKeyDownCaption }), i("change", { onElement: c2, withCallback: this.didChangeCaption }), i("blur", { onElement: c2, withCallback: this.didBlurCaption }), u2 = this.element.querySelector("figcaption"), a3 = u2.cloneNode(), { "do": function(e2) {
                return function() {
                  return u2.style.display = "none", a3.appendChild(c2), a3.appendChild(l2), a3.classList.add(t2.attachmentCaption + "--editing"), u2.parentElement.insertBefore(a3, u2), o2(), e2.options.editCaption ? n(function() {
                    return c2.focus();
                  }) : void 0;
                };
              }(this), undo: function() {
                return e.removeNode(a3), u2.style.display = null;
              } };
            }), h2.prototype.didClickToolbar = function(t3) {
              return t3.preventDefault(), t3.stopPropagation();
            }, h2.prototype.didClickActionButton = function(t3) {
              var e2, n2;
              switch (e2 = t3.target.getAttribute("data-trix-action")) {
                case "remove":
                  return null != (n2 = this.delegate) ? n2.attachmentEditorDidRequestRemovalOfAttachment(this.attachment) : void 0;
              }
            }, h2.prototype.didKeyDownCaption = function(t3) {
              var e2;
              return "return" === o[t3.keyCode] ? (t3.preventDefault(), this.savePendingCaption(), null != (e2 = this.delegate) && "function" == typeof e2.attachmentEditorDidRequestDeselectingAttachment ? e2.attachmentEditorDidRequestDeselectingAttachment(this.attachment) : void 0) : void 0;
            }, h2.prototype.didInputCaption = function(t3) {
              return this.pendingCaption = t3.target.value.replace(/\s/g, " ").trim();
            }, h2.prototype.didChangeCaption = function() {
              return this.savePendingCaption();
            }, h2.prototype.didBlurCaption = function() {
              return this.savePendingCaption();
            }, h2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              r.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, r = {}.hasOwnProperty;
          i = e.makeElement, t2 = e.config.css, e.AttachmentView = function(r2) {
            function s() {
              s.__super__.constructor.apply(this, arguments), this.attachment = this.object, this.attachment.uploadProgressDelegate = this, this.attachmentPiece = this.options.piece;
            }
            var a;
            return o(s, r2), s.attachmentSelector = "[data-trix-attachment]", s.prototype.createContentNodes = function() {
              return [];
            }, s.prototype.createNodes = function() {
              var e2, n2, o2, r3, s2, u, c;
              if (e2 = r3 = i({ tagName: "figure", className: this.getClassName(), data: this.getData(), editable: false }), (n2 = this.getHref()) && (r3 = i({ tagName: "a", editable: false, attributes: { href: n2, tabindex: -1 } }), e2.appendChild(r3)), this.attachment.hasContent())
                r3.innerHTML = this.attachment.getContent();
              else
                for (c = this.createContentNodes(), o2 = 0, s2 = c.length; s2 > o2; o2++)
                  u = c[o2], r3.appendChild(u);
              return r3.appendChild(this.createCaptionElement()), this.attachment.isPending() && (this.progressElement = i({ tagName: "progress", attributes: { "class": t2.attachmentProgress, value: this.attachment.getUploadProgress(), max: 100 }, data: { trixMutable: true, trixStoreKey: ["progressElement", this.attachment.id].join("/") } }), e2.appendChild(this.progressElement)), [a("left"), e2, a("right")];
            }, s.prototype.createCaptionElement = function() {
              var e2, n2, o2, r3, s2, a2, u;
              return o2 = i({ tagName: "figcaption", className: t2.attachmentCaption }), (e2 = this.attachmentPiece.getCaption()) ? (o2.classList.add(t2.attachmentCaption + "--edited"), o2.textContent = e2) : (n2 = this.getCaptionConfig(), n2.name && (r3 = this.attachment.getFilename()), n2.size && (a2 = this.attachment.getFormattedFilesize()), r3 && (s2 = i({ tagName: "span", className: t2.attachmentName, textContent: r3 }), o2.appendChild(s2)), a2 && (r3 && o2.appendChild(document.createTextNode(" ")), u = i({ tagName: "span", className: t2.attachmentSize, textContent: a2 }), o2.appendChild(u))), o2;
            }, s.prototype.getClassName = function() {
              var e2, n2;
              return n2 = [t2.attachment, t2.attachment + "--" + this.attachment.getType()], (e2 = this.attachment.getExtension()) && n2.push(t2.attachment + "--" + e2), n2.join(" ");
            }, s.prototype.getData = function() {
              var t3, e2;
              return e2 = { trixAttachment: JSON.stringify(this.attachment), trixContentType: this.attachment.getContentType(), trixId: this.attachment.id }, t3 = this.attachmentPiece.attributes, t3.isEmpty() || (e2.trixAttributes = JSON.stringify(t3)), this.attachment.isPending() && (e2.trixSerialize = false), e2;
            }, s.prototype.getHref = function() {
              return n(this.attachment.getContent(), "a") ? void 0 : this.attachment.getHref();
            }, s.prototype.getCaptionConfig = function() {
              var t3, n2, i2;
              return i2 = this.attachment.getType(), t3 = e.copyObject(null != (n2 = e.config.attachments[i2]) ? n2.caption : void 0), "file" === i2 && (t3.name = true), t3;
            }, s.prototype.findProgressElement = function() {
              var t3;
              return null != (t3 = this.findElement()) ? t3.querySelector("progress") : void 0;
            }, a = function(t3) {
              return i({ tagName: "span", textContent: e.ZERO_WIDTH_SPACE, data: { trixCursorTarget: t3, trixSerialize: false } });
            }, s.prototype.attachmentDidChangeUploadProgress = function() {
              var t3, e2;
              return e2 = this.attachment.getUploadProgress(), null != (t3 = this.findProgressElement()) ? t3.value = e2 : void 0;
            }, s;
          }(e.ObjectView), n = function(t3, e2) {
            var n2;
            return n2 = i("div"), n2.innerHTML = null != t3 ? t3 : "", n2.querySelector(e2);
          };
        }.call(this), function() {
          var t2, n = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var o in e2)
              i.call(e2, o) && (t3[o] = e2[o]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, i = {}.hasOwnProperty;
          t2 = e.makeElement, e.PreviewableAttachmentView = function(i2) {
            function o() {
              o.__super__.constructor.apply(this, arguments), this.attachment.previewDelegate = this;
            }
            return n(o, i2), o.prototype.createContentNodes = function() {
              return this.image = t2({ tagName: "img", attributes: { src: "" }, data: { trixMutable: true } }), this.refresh(this.image), [this.image];
            }, o.prototype.createCaptionElement = function() {
              var t3;
              return t3 = o.__super__.createCaptionElement.apply(this, arguments), t3.textContent || t3.setAttribute("data-trix-placeholder", e.config.lang.captionPlaceholder), t3;
            }, o.prototype.refresh = function(t3) {
              var e2;
              return null == t3 && (t3 = null != (e2 = this.findElement()) ? e2.querySelector("img") : void 0), t3 ? this.updateAttributesForImage(t3) : void 0;
            }, o.prototype.updateAttributesForImage = function(t3) {
              var e2, n2, i3, o2, r, s;
              return r = this.attachment.getURL(), n2 = this.attachment.getPreviewURL(), t3.src = n2 || r, n2 === r ? t3.removeAttribute("data-trix-serialized-attributes") : (i3 = JSON.stringify({ src: r }), t3.setAttribute("data-trix-serialized-attributes", i3)), s = this.attachment.getWidth(), e2 = this.attachment.getHeight(), null != s && (t3.width = s), null != e2 && (t3.height = e2), o2 = ["imageElement", this.attachment.id, t3.src, t3.width, t3.height].join("/"), t3.dataset.trixStoreKey = o2;
            }, o.prototype.attachmentDidChangeAttributes = function() {
              return this.refresh(this.image), this.refresh();
            }, o;
          }(e.AttachmentView);
        }.call(this), function() {
          var t2, n, i, o = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              r.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, r = {}.hasOwnProperty;
          i = e.makeElement, t2 = e.findInnerElement, n = e.getTextConfig, e.PieceView = function(r2) {
            function s() {
              var t3;
              s.__super__.constructor.apply(this, arguments), this.piece = this.object, this.attributes = this.piece.getAttributes(), t3 = this.options, this.textConfig = t3.textConfig, this.context = t3.context, this.piece.attachment ? this.attachment = this.piece.attachment : this.string = this.piece.toString();
            }
            var a;
            return o(s, r2), s.prototype.createNodes = function() {
              var e2, n2, i2, o2, r3, s2;
              if (s2 = this.attachment ? this.createAttachmentNodes() : this.createStringNodes(), e2 = this.createElement()) {
                for (i2 = t2(e2), n2 = 0, o2 = s2.length; o2 > n2; n2++)
                  r3 = s2[n2], i2.appendChild(r3);
                s2 = [e2];
              }
              return s2;
            }, s.prototype.createAttachmentNodes = function() {
              var t3, n2;
              return t3 = this.attachment.isPreviewable() ? e.PreviewableAttachmentView : e.AttachmentView, n2 = this.createChildView(t3, this.piece.attachment, { piece: this.piece }), n2.getNodes();
            }, s.prototype.createStringNodes = function() {
              var t3, e2, n2, o2, r3, s2, a2, u, c, l;
              if (null != (u = this.textConfig) ? u.plaintext : void 0)
                return [document.createTextNode(this.string)];
              for (a2 = [], c = this.string.split("\n"), n2 = e2 = 0, o2 = c.length; o2 > e2; n2 = ++e2)
                l = c[n2], n2 > 0 && (t3 = i("br"), a2.push(t3)), (r3 = l.length) && (s2 = document.createTextNode(this.preserveSpaces(l)), a2.push(s2));
              return a2;
            }, s.prototype.createElement = function() {
              var t3, e2, o2, r3, s2, a2, u, c, l;
              c = {}, a2 = this.attributes;
              for (r3 in a2)
                if (l = a2[r3], (t3 = n(r3)) && (t3.tagName && (s2 = i(t3.tagName), o2 ? (o2.appendChild(s2), o2 = s2) : e2 = o2 = s2), t3.styleProperty && (c[t3.styleProperty] = l), t3.style)) {
                  u = t3.style;
                  for (r3 in u)
                    l = u[r3], c[r3] = l;
                }
              if (Object.keys(c).length) {
                null == e2 && (e2 = i("span"));
                for (r3 in c)
                  l = c[r3], e2.style[r3] = l;
              }
              return e2;
            }, s.prototype.createContainerElement = function() {
              var t3, e2, o2, r3, s2;
              r3 = this.attributes;
              for (o2 in r3)
                if (s2 = r3[o2], (e2 = n(o2)) && e2.groupTagName)
                  return t3 = {}, t3[o2] = s2, i(e2.groupTagName, t3);
            }, a = e.NON_BREAKING_SPACE, s.prototype.preserveSpaces = function(t3) {
              return this.context.isLast && (t3 = t3.replace(/\ $/, a)), t3 = t3.replace(/(\S)\ {3}(\S)/g, "$1 " + a + " $2").replace(/\ {2}/g, a + " ").replace(/\ {2}/g, " " + a), (this.context.isFirst || this.context.followsWhitespace) && (t3 = t3.replace(/^\ /, a)), t3;
            }, s;
          }(e.ObjectView);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.TextView = function(n2) {
            function i() {
              i.__super__.constructor.apply(this, arguments), this.text = this.object, this.textConfig = this.options.textConfig;
            }
            var o;
            return t2(i, n2), i.prototype.createNodes = function() {
              var t3, n3, i2, r, s, a, u, c, l, h;
              for (a = [], c = e.ObjectGroup.groupObjects(this.getPieces()), r = c.length - 1, i2 = n3 = 0, s = c.length; s > n3; i2 = ++n3)
                u = c[i2], t3 = {}, 0 === i2 && (t3.isFirst = true), i2 === r && (t3.isLast = true), o(l) && (t3.followsWhitespace = true), h = this.findOrCreateCachedChildView(e.PieceView, u, { textConfig: this.textConfig, context: t3 }), a.push.apply(a, h.getNodes()), l = u;
              return a;
            }, i.prototype.getPieces = function() {
              var t3, e2, n3, i2, o2;
              for (i2 = this.text.getPieces(), o2 = [], t3 = 0, e2 = i2.length; e2 > t3; t3++)
                n3 = i2[t3], n3.hasAttribute("blockBreak") || o2.push(n3);
              return o2;
            }, o = function(t3) {
              return /\s$/.test(null != t3 ? t3.toString() : void 0);
            }, i;
          }(e.ObjectView);
        }.call(this), function() {
          var t2, n, i, o = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              r.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, r = {}.hasOwnProperty;
          i = e.makeElement, n = e.getBlockConfig, t2 = e.config.css, e.BlockView = function(r2) {
            function s() {
              s.__super__.constructor.apply(this, arguments), this.block = this.object, this.attributes = this.block.getAttributes();
            }
            return o(s, r2), s.prototype.createNodes = function() {
              var t3, o2, r3, s2, a, u, c, l, h, p, d;
              if (o2 = document.createComment("block"), c = [o2], this.block.isEmpty() ? c.push(i("br")) : (p = null != (l = n(this.block.getLastAttribute())) ? l.text : void 0, d = this.findOrCreateCachedChildView(e.TextView, this.block.text, { textConfig: p }), c.push.apply(c, d.getNodes()), this.shouldAddExtraNewlineElement() && c.push(i("br"))), this.attributes.length)
                return c;
              for (h = e.config.blockAttributes["default"].tagName, this.block.isRTL() && (t3 = { dir: "rtl" }), r3 = i({ tagName: h, attributes: t3 }), s2 = 0, a = c.length; a > s2; s2++)
                u = c[s2], r3.appendChild(u);
              return [r3];
            }, s.prototype.createContainerElement = function(e2) {
              var o2, r3, s2, a, u;
              return o2 = this.attributes[e2], u = n(o2).tagName, 0 === e2 && this.block.isRTL() && (r3 = { dir: "rtl" }), "attachmentGallery" === o2 && (a = this.block.getBlockBreakPosition(), s2 = t2.attachmentGallery + " " + t2.attachmentGallery + "--" + a), i({ tagName: u, className: s2, attributes: r3 });
            }, s.prototype.shouldAddExtraNewlineElement = function() {
              return /\n\n$/.test(this.block.toString());
            }, s;
          }(e.ObjectView);
        }.call(this), function() {
          var t2, n, i = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              o.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, o = {}.hasOwnProperty;
          t2 = e.defer, n = e.makeElement, e.DocumentView = function(o2) {
            function r() {
              r.__super__.constructor.apply(this, arguments), this.element = this.options.element, this.elementStore = new e.ElementStore(), this.setDocument(this.object);
            }
            var s, a, u;
            return i(r, o2), r.render = function(t3) {
              var e2, i2;
              return e2 = n("div"), i2 = new this(t3, { element: e2 }), i2.render(), i2.sync(), e2;
            }, r.prototype.setDocument = function(t3) {
              return t3.isEqualTo(this.document) ? void 0 : this.document = this.object = t3;
            }, r.prototype.render = function() {
              var t3, i2, o3, r2, s2, a2, u2;
              if (this.childViews = [], this.shadowElement = n("div"), !this.document.isEmpty()) {
                for (s2 = e.ObjectGroup.groupObjects(this.document.getBlocks(), { asTree: true }), a2 = [], t3 = 0, i2 = s2.length; i2 > t3; t3++)
                  r2 = s2[t3], u2 = this.findOrCreateCachedChildView(e.BlockView, r2), a2.push(function() {
                    var t4, e2, n2, i3;
                    for (n2 = u2.getNodes(), i3 = [], t4 = 0, e2 = n2.length; e2 > t4; t4++)
                      o3 = n2[t4], i3.push(this.shadowElement.appendChild(o3));
                    return i3;
                  }.call(this));
                return a2;
              }
            }, r.prototype.isSynced = function() {
              return s(this.shadowElement, this.element);
            }, r.prototype.sync = function() {
              var t3;
              for (t3 = this.createDocumentFragmentForSync(); this.element.lastChild; )
                this.element.removeChild(this.element.lastChild);
              return this.element.appendChild(t3), this.didSync();
            }, r.prototype.didSync = function() {
              return this.elementStore.reset(a(this.element)), t2(function(t3) {
                return function() {
                  return t3.garbageCollectCachedViews();
                };
              }(this));
            }, r.prototype.createDocumentFragmentForSync = function() {
              var t3, e2, n2, i2, o3, r2, s2, u2, c, l;
              for (e2 = document.createDocumentFragment(), u2 = this.shadowElement.childNodes, n2 = 0, o3 = u2.length; o3 > n2; n2++)
                s2 = u2[n2], e2.appendChild(s2.cloneNode(true));
              for (c = a(e2), i2 = 0, r2 = c.length; r2 > i2; i2++)
                t3 = c[i2], (l = this.elementStore.remove(t3)) && t3.parentNode.replaceChild(l, t3);
              return e2;
            }, a = function(t3) {
              return t3.querySelectorAll("[data-trix-store-key]");
            }, s = function(t3, e2) {
              return u(t3.innerHTML) === u(e2.innerHTML);
            }, u = function(t3) {
              return t3.replace(/&nbsp;/g, " ");
            }, r;
          }(e.ObjectView);
        }.call(this), function() {
          var t2, n, i, o, r, s = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, a = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              u.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, u = {}.hasOwnProperty;
          i = e.findClosestElementFromNode, o = e.handleEvent, r = e.innerElementIsActive, n = e.defer, t2 = e.AttachmentView.attachmentSelector, e.CompositionController = function(u2) {
            function c(n2, i2) {
              this.element = n2, this.composition = i2, this.didClickAttachment = s(this.didClickAttachment, this), this.didBlur = s(this.didBlur, this), this.didFocus = s(this.didFocus, this), this.documentView = new e.DocumentView(this.composition.document, { element: this.element }), o("focus", { onElement: this.element, withCallback: this.didFocus }), o("blur", { onElement: this.element, withCallback: this.didBlur }), o("click", { onElement: this.element, matchingSelector: "a[contenteditable=false]", preventDefault: true }), o("mousedown", { onElement: this.element, matchingSelector: t2, withCallback: this.didClickAttachment }), o("click", { onElement: this.element, matchingSelector: "a" + t2, preventDefault: true });
            }
            return a(c, u2), c.prototype.didFocus = function() {
              var t3, e2, n2;
              return t3 = function(t4) {
                return function() {
                  var e3;
                  return t4.focused ? void 0 : (t4.focused = true, null != (e3 = t4.delegate) && "function" == typeof e3.compositionControllerDidFocus ? e3.compositionControllerDidFocus() : void 0);
                };
              }(this), null != (e2 = null != (n2 = this.blurPromise) ? n2.then(t3) : void 0) ? e2 : t3();
            }, c.prototype.didBlur = function() {
              return this.blurPromise = new Promise(function(t3) {
                return function(e2) {
                  return n(function() {
                    var n2;
                    return r(t3.element) || (t3.focused = null, null != (n2 = t3.delegate) && "function" == typeof n2.compositionControllerDidBlur && n2.compositionControllerDidBlur()), t3.blurPromise = null, e2();
                  });
                };
              }(this));
            }, c.prototype.didClickAttachment = function(t3, e2) {
              var n2, o2, r2;
              return n2 = this.findAttachmentForElement(e2), o2 = null != i(t3.target, { matchingSelector: "figcaption" }), null != (r2 = this.delegate) && "function" == typeof r2.compositionControllerDidSelectAttachment ? r2.compositionControllerDidSelectAttachment(n2, { editCaption: o2 }) : void 0;
            }, c.prototype.getSerializableElement = function() {
              return this.isEditingAttachment() ? this.documentView.shadowElement : this.element;
            }, c.prototype.render = function() {
              var t3, e2, n2;
              return this.revision !== this.composition.revision && (this.documentView.setDocument(this.composition.document), this.documentView.render(), this.revision = this.composition.revision), this.canSyncDocumentView() && !this.documentView.isSynced() && (null != (t3 = this.delegate) && "function" == typeof t3.compositionControllerWillSyncDocumentView && t3.compositionControllerWillSyncDocumentView(), this.documentView.sync(), null != (e2 = this.delegate) && "function" == typeof e2.compositionControllerDidSyncDocumentView && e2.compositionControllerDidSyncDocumentView()), null != (n2 = this.delegate) && "function" == typeof n2.compositionControllerDidRender ? n2.compositionControllerDidRender() : void 0;
            }, c.prototype.rerenderViewForObject = function(t3) {
              return this.invalidateViewForObject(t3), this.render();
            }, c.prototype.invalidateViewForObject = function(t3) {
              return this.documentView.invalidateViewForObject(t3);
            }, c.prototype.isViewCachingEnabled = function() {
              return this.documentView.isViewCachingEnabled();
            }, c.prototype.enableViewCaching = function() {
              return this.documentView.enableViewCaching();
            }, c.prototype.disableViewCaching = function() {
              return this.documentView.disableViewCaching();
            }, c.prototype.refreshViewCache = function() {
              return this.documentView.garbageCollectCachedViews();
            }, c.prototype.isEditingAttachment = function() {
              return null != this.attachmentEditor;
            }, c.prototype.installAttachmentEditorForAttachment = function(t3, n2) {
              var i2, o2, r2;
              if ((null != (r2 = this.attachmentEditor) ? r2.attachment : void 0) !== t3 && (o2 = this.documentView.findElementForObject(t3)))
                return this.uninstallAttachmentEditor(), i2 = this.composition.document.getAttachmentPieceForAttachment(t3), this.attachmentEditor = new e.AttachmentEditorController(i2, o2, this.element, n2), this.attachmentEditor.delegate = this;
            }, c.prototype.uninstallAttachmentEditor = function() {
              var t3;
              return null != (t3 = this.attachmentEditor) ? t3.uninstall() : void 0;
            }, c.prototype.didUninstallAttachmentEditor = function() {
              return this.attachmentEditor = null, this.render();
            }, c.prototype.attachmentEditorDidRequestUpdatingAttributesForAttachment = function(t3, e2) {
              var n2;
              return null != (n2 = this.delegate) && "function" == typeof n2.compositionControllerWillUpdateAttachment && n2.compositionControllerWillUpdateAttachment(e2), this.composition.updateAttributesForAttachment(t3, e2);
            }, c.prototype.attachmentEditorDidRequestRemovingAttributeForAttachment = function(t3, e2) {
              var n2;
              return null != (n2 = this.delegate) && "function" == typeof n2.compositionControllerWillUpdateAttachment && n2.compositionControllerWillUpdateAttachment(e2), this.composition.removeAttributeForAttachment(t3, e2);
            }, c.prototype.attachmentEditorDidRequestRemovalOfAttachment = function(t3) {
              var e2;
              return null != (e2 = this.delegate) && "function" == typeof e2.compositionControllerDidRequestRemovalOfAttachment ? e2.compositionControllerDidRequestRemovalOfAttachment(t3) : void 0;
            }, c.prototype.attachmentEditorDidRequestDeselectingAttachment = function(t3) {
              var e2;
              return null != (e2 = this.delegate) && "function" == typeof e2.compositionControllerDidRequestDeselectingAttachment ? e2.compositionControllerDidRequestDeselectingAttachment(t3) : void 0;
            }, c.prototype.canSyncDocumentView = function() {
              return !this.isEditingAttachment();
            }, c.prototype.findAttachmentForElement = function(t3) {
              return this.composition.document.getAttachmentById(parseInt(t3.dataset.trixId, 10));
            }, c;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, r = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              s.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, s = {}.hasOwnProperty;
          n = e.handleEvent, i = e.triggerEvent, t2 = e.findClosestElementFromNode, e.ToolbarController = function(e2) {
            function s2(t3) {
              this.element = t3, this.didKeyDownDialogInput = o(this.didKeyDownDialogInput, this), this.didClickDialogButton = o(this.didClickDialogButton, this), this.didClickAttributeButton = o(this.didClickAttributeButton, this), this.didClickActionButton = o(this.didClickActionButton, this), this.attributes = {}, this.actions = {}, this.resetDialogInputs(), n("mousedown", { onElement: this.element, matchingSelector: a, withCallback: this.didClickActionButton }), n("mousedown", { onElement: this.element, matchingSelector: c, withCallback: this.didClickAttributeButton }), n("click", { onElement: this.element, matchingSelector: v, preventDefault: true }), n("click", { onElement: this.element, matchingSelector: l, withCallback: this.didClickDialogButton }), n("keydown", { onElement: this.element, matchingSelector: h, withCallback: this.didKeyDownDialogInput });
            }
            var a, u, c, l, h, p, d, f, g, m, v;
            return r(s2, e2), c = "[data-trix-attribute]", a = "[data-trix-action]", v = c + ", " + a, p = "[data-trix-dialog]", u = p + "[data-trix-active]", l = p + " [data-trix-method]", h = p + " [data-trix-input]", s2.prototype.didClickActionButton = function(t3, e3) {
              var n2, i2, o2;
              return null != (i2 = this.delegate) && i2.toolbarDidClickButton(), t3.preventDefault(), n2 = d(e3), this.getDialog(n2) ? this.toggleDialog(n2) : null != (o2 = this.delegate) ? o2.toolbarDidInvokeAction(n2) : void 0;
            }, s2.prototype.didClickAttributeButton = function(t3, e3) {
              var n2, i2, o2;
              return null != (i2 = this.delegate) && i2.toolbarDidClickButton(), t3.preventDefault(), n2 = f(e3), this.getDialog(n2) ? this.toggleDialog(n2) : null != (o2 = this.delegate) && o2.toolbarDidToggleAttribute(n2), this.refreshAttributeButtons();
            }, s2.prototype.didClickDialogButton = function(e3, n2) {
              var i2, o2;
              return i2 = t2(n2, { matchingSelector: p }), o2 = n2.getAttribute("data-trix-method"), this[o2].call(this, i2);
            }, s2.prototype.didKeyDownDialogInput = function(t3, e3) {
              var n2, i2;
              return 13 === t3.keyCode && (t3.preventDefault(), n2 = e3.getAttribute("name"), i2 = this.getDialog(n2), this.setAttribute(i2)), 27 === t3.keyCode ? (t3.preventDefault(), this.hideDialog()) : void 0;
            }, s2.prototype.updateActions = function(t3) {
              return this.actions = t3, this.refreshActionButtons();
            }, s2.prototype.refreshActionButtons = function() {
              return this.eachActionButton(function(t3) {
                return function(e3, n2) {
                  return e3.disabled = t3.actions[n2] === false;
                };
              }(this));
            }, s2.prototype.eachActionButton = function(t3) {
              var e3, n2, i2, o2, r2;
              for (o2 = this.element.querySelectorAll(a), r2 = [], n2 = 0, i2 = o2.length; i2 > n2; n2++)
                e3 = o2[n2], r2.push(t3(e3, d(e3)));
              return r2;
            }, s2.prototype.updateAttributes = function(t3) {
              return this.attributes = t3, this.refreshAttributeButtons();
            }, s2.prototype.refreshAttributeButtons = function() {
              return this.eachAttributeButton(function(t3) {
                return function(e3, n2) {
                  return e3.disabled = t3.attributes[n2] === false, t3.attributes[n2] || t3.dialogIsVisible(n2) ? (e3.setAttribute("data-trix-active", ""), e3.classList.add("trix-active")) : (e3.removeAttribute("data-trix-active"), e3.classList.remove("trix-active"));
                };
              }(this));
            }, s2.prototype.eachAttributeButton = function(t3) {
              var e3, n2, i2, o2, r2;
              for (o2 = this.element.querySelectorAll(c), r2 = [], n2 = 0, i2 = o2.length; i2 > n2; n2++)
                e3 = o2[n2], r2.push(t3(e3, f(e3)));
              return r2;
            }, s2.prototype.applyKeyboardCommand = function(t3) {
              var e3, n2, o2, r2, s3, a2, u2;
              for (s3 = JSON.stringify(t3.sort()), u2 = this.element.querySelectorAll("[data-trix-key]"), r2 = 0, a2 = u2.length; a2 > r2; r2++)
                if (e3 = u2[r2], o2 = e3.getAttribute("data-trix-key").split("+"), n2 = JSON.stringify(o2.sort()), n2 === s3)
                  return i("mousedown", { onElement: e3 }), true;
              return false;
            }, s2.prototype.dialogIsVisible = function(t3) {
              var e3;
              return (e3 = this.getDialog(t3)) ? e3.hasAttribute("data-trix-active") : void 0;
            }, s2.prototype.toggleDialog = function(t3) {
              return this.dialogIsVisible(t3) ? this.hideDialog() : this.showDialog(t3);
            }, s2.prototype.showDialog = function(t3) {
              var e3, n2, i2, o2, r2, s3, a2, u2, c2, l2;
              for (this.hideDialog(), null != (a2 = this.delegate) && a2.toolbarWillShowDialog(), i2 = this.getDialog(t3), i2.setAttribute("data-trix-active", ""), i2.classList.add("trix-active"), u2 = i2.querySelectorAll("input[disabled]"), o2 = 0, s3 = u2.length; s3 > o2; o2++)
                n2 = u2[o2], n2.removeAttribute("disabled");
              return (e3 = f(i2)) && (r2 = m(i2, t3)) && (r2.value = null != (c2 = this.attributes[e3]) ? c2 : "", r2.select()), null != (l2 = this.delegate) ? l2.toolbarDidShowDialog(t3) : void 0;
            }, s2.prototype.setAttribute = function(t3) {
              var e3, n2, i2;
              return e3 = f(t3), n2 = m(t3, e3), n2.willValidate && !n2.checkValidity() ? (n2.setAttribute("data-trix-validate", ""), n2.classList.add("trix-validate"), n2.focus()) : (null != (i2 = this.delegate) && i2.toolbarDidUpdateAttribute(e3, n2.value), this.hideDialog());
            }, s2.prototype.removeAttribute = function(t3) {
              var e3, n2;
              return e3 = f(t3), null != (n2 = this.delegate) && n2.toolbarDidRemoveAttribute(e3), this.hideDialog();
            }, s2.prototype.hideDialog = function() {
              var t3, e3;
              return (t3 = this.element.querySelector(u)) ? (t3.removeAttribute("data-trix-active"), t3.classList.remove("trix-active"), this.resetDialogInputs(), null != (e3 = this.delegate) ? e3.toolbarDidHideDialog(g(t3)) : void 0) : void 0;
            }, s2.prototype.resetDialogInputs = function() {
              var t3, e3, n2, i2, o2;
              for (i2 = this.element.querySelectorAll(h), o2 = [], t3 = 0, n2 = i2.length; n2 > t3; t3++)
                e3 = i2[t3], e3.setAttribute("disabled", "disabled"), e3.removeAttribute("data-trix-validate"), o2.push(e3.classList.remove("trix-validate"));
              return o2;
            }, s2.prototype.getDialog = function(t3) {
              return this.element.querySelector("[data-trix-dialog=" + t3 + "]");
            }, m = function(t3, e3) {
              return null == e3 && (e3 = f(t3)), t3.querySelector("[data-trix-input][name='" + e3 + "']");
            }, d = function(t3) {
              return t3.getAttribute("data-trix-action");
            }, f = function(t3) {
              var e3;
              return null != (e3 = t3.getAttribute("data-trix-attribute")) ? e3 : t3.getAttribute("data-trix-dialog-attribute");
            }, g = function(t3) {
              return t3.getAttribute("data-trix-dialog");
            }, s2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.ImagePreloadOperation = function(e2) {
            function n2(t3) {
              this.url = t3;
            }
            return t2(n2, e2), n2.prototype.perform = function(t3) {
              var e3;
              return e3 = new Image(), e3.onload = function(n3) {
                return function() {
                  return e3.width = n3.width = e3.naturalWidth, e3.height = n3.height = e3.naturalHeight, t3(true, e3);
                };
              }(this), e3.onerror = function() {
                return t3(false);
              }, e3.src = this.url;
            }, n2;
          }(e.Operation);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, n = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var o in e2)
              i.call(e2, o) && (t3[o] = e2[o]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, i = {}.hasOwnProperty;
          e.Attachment = function(i2) {
            function o(n2) {
              null == n2 && (n2 = {}), this.releaseFile = t2(this.releaseFile, this), o.__super__.constructor.apply(this, arguments), this.attributes = e.Hash.box(n2), this.didChangeAttributes();
            }
            return n(o, i2), o.previewablePattern = /^image(\/(gif|png|jpe?g)|$)/, o.attachmentForFile = function(t3) {
              var e2, n2;
              return n2 = this.attributesForFile(t3), e2 = new this(n2), e2.setFile(t3), e2;
            }, o.attributesForFile = function(t3) {
              return new e.Hash({ filename: t3.name, filesize: t3.size, contentType: t3.type });
            }, o.fromJSON = function(t3) {
              return new this(t3);
            }, o.prototype.getAttribute = function(t3) {
              return this.attributes.get(t3);
            }, o.prototype.hasAttribute = function(t3) {
              return this.attributes.has(t3);
            }, o.prototype.getAttributes = function() {
              return this.attributes.toObject();
            }, o.prototype.setAttributes = function(t3) {
              var e2, n2, i3;
              return null == t3 && (t3 = {}), e2 = this.attributes.merge(t3), this.attributes.isEqualTo(e2) ? void 0 : (this.attributes = e2, this.didChangeAttributes(), null != (n2 = this.previewDelegate) && "function" == typeof n2.attachmentDidChangeAttributes && n2.attachmentDidChangeAttributes(this), null != (i3 = this.delegate) && "function" == typeof i3.attachmentDidChangeAttributes ? i3.attachmentDidChangeAttributes(this) : void 0);
            }, o.prototype.didChangeAttributes = function() {
              return this.isPreviewable() ? this.preloadURL() : void 0;
            }, o.prototype.isPending = function() {
              return null != this.file && !(this.getURL() || this.getHref());
            }, o.prototype.isPreviewable = function() {
              return this.attributes.has("previewable") ? this.attributes.get("previewable") : this.constructor.previewablePattern.test(this.getContentType());
            }, o.prototype.getType = function() {
              return this.hasContent() ? "content" : this.isPreviewable() ? "preview" : "file";
            }, o.prototype.getURL = function() {
              return this.attributes.get("url");
            }, o.prototype.getHref = function() {
              return this.attributes.get("href");
            }, o.prototype.getFilename = function() {
              var t3;
              return null != (t3 = this.attributes.get("filename")) ? t3 : "";
            }, o.prototype.getFilesize = function() {
              return this.attributes.get("filesize");
            }, o.prototype.getFormattedFilesize = function() {
              var t3;
              return t3 = this.attributes.get("filesize"), "number" == typeof t3 ? e.config.fileSize.formatter(t3) : "";
            }, o.prototype.getExtension = function() {
              var t3;
              return null != (t3 = this.getFilename().match(/\.(\w+)$/)) ? t3[1].toLowerCase() : void 0;
            }, o.prototype.getContentType = function() {
              return this.attributes.get("contentType");
            }, o.prototype.hasContent = function() {
              return this.attributes.has("content");
            }, o.prototype.getContent = function() {
              return this.attributes.get("content");
            }, o.prototype.getWidth = function() {
              return this.attributes.get("width");
            }, o.prototype.getHeight = function() {
              return this.attributes.get("height");
            }, o.prototype.getFile = function() {
              return this.file;
            }, o.prototype.setFile = function(t3) {
              return this.file = t3, this.isPreviewable() ? this.preloadFile() : void 0;
            }, o.prototype.releaseFile = function() {
              return this.releasePreloadedFile(), this.file = null;
            }, o.prototype.getUploadProgress = function() {
              var t3;
              return null != (t3 = this.uploadProgress) ? t3 : 0;
            }, o.prototype.setUploadProgress = function(t3) {
              var e2;
              return this.uploadProgress !== t3 ? (this.uploadProgress = t3, null != (e2 = this.uploadProgressDelegate) && "function" == typeof e2.attachmentDidChangeUploadProgress ? e2.attachmentDidChangeUploadProgress(this) : void 0) : void 0;
            }, o.prototype.toJSON = function() {
              return this.getAttributes();
            }, o.prototype.getCacheKey = function() {
              return [o.__super__.getCacheKey.apply(this, arguments), this.attributes.getCacheKey(), this.getPreviewURL()].join("/");
            }, o.prototype.getPreviewURL = function() {
              return this.previewURL || this.preloadingURL;
            }, o.prototype.setPreviewURL = function(t3) {
              var e2, n2;
              return t3 !== this.getPreviewURL() ? (this.previewURL = t3, null != (e2 = this.previewDelegate) && "function" == typeof e2.attachmentDidChangeAttributes && e2.attachmentDidChangeAttributes(this), null != (n2 = this.delegate) && "function" == typeof n2.attachmentDidChangePreviewURL ? n2.attachmentDidChangePreviewURL(this) : void 0) : void 0;
            }, o.prototype.preloadURL = function() {
              return this.preload(this.getURL(), this.releaseFile);
            }, o.prototype.preloadFile = function() {
              return this.file ? (this.fileObjectURL = URL.createObjectURL(this.file), this.preload(this.fileObjectURL)) : void 0;
            }, o.prototype.releasePreloadedFile = function() {
              return this.fileObjectURL ? (URL.revokeObjectURL(this.fileObjectURL), this.fileObjectURL = null) : void 0;
            }, o.prototype.preload = function(t3, n2) {
              var i3;
              return t3 && t3 !== this.getPreviewURL() ? (this.preloadingURL = t3, i3 = new e.ImagePreloadOperation(t3), i3.then(function(e2) {
                return function(i4) {
                  var o2, r;
                  return r = i4.width, o2 = i4.height, e2.getWidth() && e2.getHeight() || e2.setAttributes({ width: r, height: o2 }), e2.preloadingURL = null, e2.setPreviewURL(t3), "function" == typeof n2 ? n2() : void 0;
                };
              }(this))["catch"](function(t4) {
                return function() {
                  return t4.preloadingURL = null, "function" == typeof n2 ? n2() : void 0;
                };
              }(this))) : void 0;
            }, o;
          }(e.Object);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.Piece = function(n2) {
            function i(t3, n3) {
              null == n3 && (n3 = {}), i.__super__.constructor.apply(this, arguments), this.attributes = e.Hash.box(n3);
            }
            return t2(i, n2), i.types = {}, i.registerType = function(t3, e2) {
              return e2.type = t3, this.types[t3] = e2;
            }, i.fromJSON = function(t3) {
              var e2;
              return (e2 = this.types[t3.type]) ? e2.fromJSON(t3) : void 0;
            }, i.prototype.copyWithAttributes = function(t3) {
              return new this.constructor(this.getValue(), t3);
            }, i.prototype.copyWithAdditionalAttributes = function(t3) {
              return this.copyWithAttributes(this.attributes.merge(t3));
            }, i.prototype.copyWithoutAttribute = function(t3) {
              return this.copyWithAttributes(this.attributes.remove(t3));
            }, i.prototype.copy = function() {
              return this.copyWithAttributes(this.attributes);
            }, i.prototype.getAttribute = function(t3) {
              return this.attributes.get(t3);
            }, i.prototype.getAttributesHash = function() {
              return this.attributes;
            }, i.prototype.getAttributes = function() {
              return this.attributes.toObject();
            }, i.prototype.getCommonAttributes = function() {
              var t3, e2, n3;
              return (n3 = pieceList.getPieceAtIndex(0)) ? (t3 = n3.attributes, e2 = t3.getKeys(), pieceList.eachPiece(function(n4) {
                return e2 = t3.getKeysCommonToHash(n4.attributes), t3 = t3.slice(e2);
              }), t3.toObject()) : {};
            }, i.prototype.hasAttribute = function(t3) {
              return this.attributes.has(t3);
            }, i.prototype.hasSameStringValueAsPiece = function(t3) {
              return null != t3 && this.toString() === t3.toString();
            }, i.prototype.hasSameAttributesAsPiece = function(t3) {
              return null != t3 && (this.attributes === t3.attributes || this.attributes.isEqualTo(t3.attributes));
            }, i.prototype.isBlockBreak = function() {
              return false;
            }, i.prototype.isEqualTo = function(t3) {
              return i.__super__.isEqualTo.apply(this, arguments) || this.hasSameConstructorAs(t3) && this.hasSameStringValueAsPiece(t3) && this.hasSameAttributesAsPiece(t3);
            }, i.prototype.isEmpty = function() {
              return 0 === this.length;
            }, i.prototype.isSerializable = function() {
              return true;
            }, i.prototype.toJSON = function() {
              return { type: this.constructor.type, attributes: this.getAttributes() };
            }, i.prototype.contentsForInspection = function() {
              return { type: this.constructor.type, attributes: this.attributes.inspect() };
            }, i.prototype.canBeGrouped = function() {
              return this.hasAttribute("href");
            }, i.prototype.canBeGroupedWith = function(t3) {
              return this.getAttribute("href") === t3.getAttribute("href");
            }, i.prototype.getLength = function() {
              return this.length;
            }, i.prototype.canBeConsolidatedWith = function() {
              return false;
            }, i;
          }(e.Object);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.Piece.registerType("attachment", e.AttachmentPiece = function(n2) {
            function i(t3) {
              this.attachment = t3, i.__super__.constructor.apply(this, arguments), this.length = 1, this.ensureAttachmentExclusivelyHasAttribute("href"), this.attachment.hasContent() || this.removeProhibitedAttributes();
            }
            return t2(i, n2), i.fromJSON = function(t3) {
              return new this(e.Attachment.fromJSON(t3.attachment), t3.attributes);
            }, i.permittedAttributes = ["caption", "presentation"], i.prototype.ensureAttachmentExclusivelyHasAttribute = function(t3) {
              return this.hasAttribute(t3) ? (this.attachment.hasAttribute(t3) || this.attachment.setAttributes(this.attributes.slice(t3)), this.attributes = this.attributes.remove(t3)) : void 0;
            }, i.prototype.removeProhibitedAttributes = function() {
              var t3;
              return t3 = this.attributes.slice(this.constructor.permittedAttributes), t3.isEqualTo(this.attributes) ? void 0 : this.attributes = t3;
            }, i.prototype.getValue = function() {
              return this.attachment;
            }, i.prototype.isSerializable = function() {
              return !this.attachment.isPending();
            }, i.prototype.getCaption = function() {
              var t3;
              return null != (t3 = this.attributes.get("caption")) ? t3 : "";
            }, i.prototype.isEqualTo = function(t3) {
              var e2;
              return i.__super__.isEqualTo.apply(this, arguments) && this.attachment.id === (null != t3 && null != (e2 = t3.attachment) ? e2.id : void 0);
            }, i.prototype.toString = function() {
              return e.OBJECT_REPLACEMENT_CHARACTER;
            }, i.prototype.toJSON = function() {
              var t3;
              return t3 = i.__super__.toJSON.apply(this, arguments), t3.attachment = this.attachment, t3;
            }, i.prototype.getCacheKey = function() {
              return [i.__super__.getCacheKey.apply(this, arguments), this.attachment.getCacheKey()].join("/");
            }, i.prototype.toConsole = function() {
              return JSON.stringify(this.toString());
            }, i;
          }(e.Piece));
        }.call(this), function() {
          var t2, n = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var o in e2)
              i.call(e2, o) && (t3[o] = e2[o]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, i = {}.hasOwnProperty;
          t2 = e.normalizeNewlines, e.Piece.registerType("string", e.StringPiece = function(e2) {
            function i2(e3) {
              i2.__super__.constructor.apply(this, arguments), this.string = t2(e3), this.length = this.string.length;
            }
            return n(i2, e2), i2.fromJSON = function(t3) {
              return new this(t3.string, t3.attributes);
            }, i2.prototype.getValue = function() {
              return this.string;
            }, i2.prototype.toString = function() {
              return this.string.toString();
            }, i2.prototype.isBlockBreak = function() {
              return "\n" === this.toString() && this.getAttribute("blockBreak") === true;
            }, i2.prototype.toJSON = function() {
              var t3;
              return t3 = i2.__super__.toJSON.apply(this, arguments), t3.string = this.string, t3;
            }, i2.prototype.canBeConsolidatedWith = function(t3) {
              return null != t3 && this.hasSameConstructorAs(t3) && this.hasSameAttributesAsPiece(t3);
            }, i2.prototype.consolidateWith = function(t3) {
              return new this.constructor(this.toString() + t3.toString(), this.attributes);
            }, i2.prototype.splitAtOffset = function(t3) {
              var e3, n2;
              return 0 === t3 ? (e3 = null, n2 = this) : t3 === this.length ? (e3 = this, n2 = null) : (e3 = new this.constructor(this.string.slice(0, t3), this.attributes), n2 = new this.constructor(this.string.slice(t3), this.attributes)), [e3, n2];
            }, i2.prototype.toConsole = function() {
              var t3;
              return t3 = this.string, t3.length > 15 && (t3 = t3.slice(0, 14) + "\u2026"), JSON.stringify(t3.toString());
            }, i2;
          }(e.Piece));
        }.call(this), function() {
          var t2, n = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var o2 in e2)
              i.call(e2, o2) && (t3[o2] = e2[o2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, i = {}.hasOwnProperty, o = [].slice;
          t2 = e.spliceArray, e.SplittableList = function(e2) {
            function i2(t3) {
              null == t3 && (t3 = []), i2.__super__.constructor.apply(this, arguments), this.objects = t3.slice(0), this.length = this.objects.length;
            }
            var r, s, a;
            return n(i2, e2), i2.box = function(t3) {
              return t3 instanceof this ? t3 : new this(t3);
            }, i2.prototype.indexOf = function(t3) {
              return this.objects.indexOf(t3);
            }, i2.prototype.splice = function() {
              var e3;
              return e3 = 1 <= arguments.length ? o.call(arguments, 0) : [], new this.constructor(t2.apply(null, [this.objects].concat(o.call(e3))));
            }, i2.prototype.eachObject = function(t3) {
              var e3, n2, i3, o2, r2, s2;
              for (r2 = this.objects, s2 = [], n2 = e3 = 0, i3 = r2.length; i3 > e3; n2 = ++e3)
                o2 = r2[n2], s2.push(t3(o2, n2));
              return s2;
            }, i2.prototype.insertObjectAtIndex = function(t3, e3) {
              return this.splice(e3, 0, t3);
            }, i2.prototype.insertSplittableListAtIndex = function(t3, e3) {
              return this.splice.apply(this, [e3, 0].concat(o.call(t3.objects)));
            }, i2.prototype.insertSplittableListAtPosition = function(t3, e3) {
              var n2, i3, o2;
              return o2 = this.splitObjectAtPosition(e3), i3 = o2[0], n2 = o2[1], new this.constructor(i3).insertSplittableListAtIndex(t3, n2);
            }, i2.prototype.editObjectAtIndex = function(t3, e3) {
              return this.replaceObjectAtIndex(e3(this.objects[t3]), t3);
            }, i2.prototype.replaceObjectAtIndex = function(t3, e3) {
              return this.splice(e3, 1, t3);
            }, i2.prototype.removeObjectAtIndex = function(t3) {
              return this.splice(t3, 1);
            }, i2.prototype.getObjectAtIndex = function(t3) {
              return this.objects[t3];
            }, i2.prototype.getSplittableListInRange = function(t3) {
              var e3, n2, i3, o2;
              return i3 = this.splitObjectsAtRange(t3), n2 = i3[0], e3 = i3[1], o2 = i3[2], new this.constructor(n2.slice(e3, o2 + 1));
            }, i2.prototype.selectSplittableList = function(t3) {
              var e3, n2;
              return n2 = function() {
                var n3, i3, o2, r2;
                for (o2 = this.objects, r2 = [], n3 = 0, i3 = o2.length; i3 > n3; n3++)
                  e3 = o2[n3], t3(e3) && r2.push(e3);
                return r2;
              }.call(this), new this.constructor(n2);
            }, i2.prototype.removeObjectsInRange = function(t3) {
              var e3, n2, i3, o2;
              return i3 = this.splitObjectsAtRange(t3), n2 = i3[0], e3 = i3[1], o2 = i3[2], new this.constructor(n2).splice(e3, o2 - e3 + 1);
            }, i2.prototype.transformObjectsInRange = function(t3, e3) {
              var n2, i3, o2, r2, s2, a2, u;
              return s2 = this.splitObjectsAtRange(t3), r2 = s2[0], i3 = s2[1], a2 = s2[2], u = function() {
                var t4, s3, u2;
                for (u2 = [], n2 = t4 = 0, s3 = r2.length; s3 > t4; n2 = ++t4)
                  o2 = r2[n2], u2.push(n2 >= i3 && a2 >= n2 ? e3(o2) : o2);
                return u2;
              }(), new this.constructor(u);
            }, i2.prototype.splitObjectsAtRange = function(t3) {
              var e3, n2, i3, o2, s2, u;
              return o2 = this.splitObjectAtPosition(a(t3)), n2 = o2[0], e3 = o2[1], i3 = o2[2], s2 = new this.constructor(n2).splitObjectAtPosition(r(t3) + i3), n2 = s2[0], u = s2[1], [n2, e3, u - 1];
            }, i2.prototype.getObjectAtPosition = function(t3) {
              var e3, n2, i3;
              return i3 = this.findIndexAndOffsetAtPosition(t3), e3 = i3.index, n2 = i3.offset, this.objects[e3];
            }, i2.prototype.splitObjectAtPosition = function(t3) {
              var e3, n2, i3, o2, r2, s2, a2, u, c, l;
              return s2 = this.findIndexAndOffsetAtPosition(t3), e3 = s2.index, r2 = s2.offset, o2 = this.objects.slice(0), null != e3 ? 0 === r2 ? (c = e3, l = 0) : (i3 = this.getObjectAtIndex(e3), a2 = i3.splitAtOffset(r2), n2 = a2[0], u = a2[1], o2.splice(e3, 1, n2, u), c = e3 + 1, l = n2.getLength() - r2) : (c = o2.length, l = 0), [o2, c, l];
            }, i2.prototype.consolidate = function() {
              var t3, e3, n2, i3, o2, r2;
              for (i3 = [], o2 = this.objects[0], r2 = this.objects.slice(1), t3 = 0, e3 = r2.length; e3 > t3; t3++)
                n2 = r2[t3], ("function" == typeof o2.canBeConsolidatedWith ? o2.canBeConsolidatedWith(n2) : void 0) ? o2 = o2.consolidateWith(n2) : (i3.push(o2), o2 = n2);
              return null != o2 && i3.push(o2), new this.constructor(i3);
            }, i2.prototype.consolidateFromIndexToIndex = function(t3, e3) {
              var n2, i3, r2;
              return i3 = this.objects.slice(0), r2 = i3.slice(t3, e3 + 1), n2 = new this.constructor(r2).consolidate().toArray(), this.splice.apply(this, [t3, r2.length].concat(o.call(n2)));
            }, i2.prototype.findIndexAndOffsetAtPosition = function(t3) {
              var e3, n2, i3, o2, r2, s2, a2;
              for (e3 = 0, a2 = this.objects, i3 = n2 = 0, o2 = a2.length; o2 > n2; i3 = ++n2) {
                if (s2 = a2[i3], r2 = e3 + s2.getLength(), t3 >= e3 && r2 > t3)
                  return { index: i3, offset: t3 - e3 };
                e3 = r2;
              }
              return { index: null, offset: null };
            }, i2.prototype.findPositionAtIndexAndOffset = function(t3, e3) {
              var n2, i3, o2, r2, s2, a2;
              for (s2 = 0, a2 = this.objects, n2 = i3 = 0, o2 = a2.length; o2 > i3; n2 = ++i3)
                if (r2 = a2[n2], t3 > n2)
                  s2 += r2.getLength();
                else if (n2 === t3) {
                  s2 += e3;
                  break;
                }
              return s2;
            }, i2.prototype.getEndPosition = function() {
              var t3, e3;
              return null != this.endPosition ? this.endPosition : this.endPosition = function() {
                var n2, i3, o2;
                for (e3 = 0, o2 = this.objects, n2 = 0, i3 = o2.length; i3 > n2; n2++)
                  t3 = o2[n2], e3 += t3.getLength();
                return e3;
              }.call(this);
            }, i2.prototype.toString = function() {
              return this.objects.join("");
            }, i2.prototype.toArray = function() {
              return this.objects.slice(0);
            }, i2.prototype.toJSON = function() {
              return this.toArray();
            }, i2.prototype.isEqualTo = function(t3) {
              return i2.__super__.isEqualTo.apply(this, arguments) || s(this.objects, null != t3 ? t3.objects : void 0);
            }, s = function(t3, e3) {
              var n2, i3, o2, r2, s2;
              if (null == e3 && (e3 = []), t3.length !== e3.length)
                return false;
              for (s2 = true, i3 = n2 = 0, o2 = t3.length; o2 > n2; i3 = ++n2)
                r2 = t3[i3], s2 && !r2.isEqualTo(e3[i3]) && (s2 = false);
              return s2;
            }, i2.prototype.contentsForInspection = function() {
              var t3;
              return { objects: "[" + function() {
                var e3, n2, i3, o2;
                for (i3 = this.objects, o2 = [], e3 = 0, n2 = i3.length; n2 > e3; e3++)
                  t3 = i3[e3], o2.push(t3.inspect());
                return o2;
              }.call(this).join(", ") + "]" };
            }, a = function(t3) {
              return t3[0];
            }, r = function(t3) {
              return t3[1];
            }, i2;
          }(e.Object);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.Text = function(n2) {
            function i(t3) {
              var n3;
              null == t3 && (t3 = []), i.__super__.constructor.apply(this, arguments), this.pieceList = new e.SplittableList(function() {
                var e2, i2, o;
                for (o = [], e2 = 0, i2 = t3.length; i2 > e2; e2++)
                  n3 = t3[e2], n3.isEmpty() || o.push(n3);
                return o;
              }());
            }
            return t2(i, n2), i.textForAttachmentWithAttributes = function(t3, n3) {
              var i2;
              return i2 = new e.AttachmentPiece(t3, n3), new this([i2]);
            }, i.textForStringWithAttributes = function(t3, n3) {
              var i2;
              return i2 = new e.StringPiece(t3, n3), new this([i2]);
            }, i.fromJSON = function(t3) {
              var n3, i2;
              return i2 = function() {
                var i3, o, r;
                for (r = [], i3 = 0, o = t3.length; o > i3; i3++)
                  n3 = t3[i3], r.push(e.Piece.fromJSON(n3));
                return r;
              }(), new this(i2);
            }, i.prototype.copy = function() {
              return this.copyWithPieceList(this.pieceList);
            }, i.prototype.copyWithPieceList = function(t3) {
              return new this.constructor(t3.consolidate().toArray());
            }, i.prototype.copyUsingObjectMap = function(t3) {
              var e2, n3;
              return n3 = function() {
                var n4, i2, o, r, s;
                for (o = this.getPieces(), s = [], n4 = 0, i2 = o.length; i2 > n4; n4++)
                  e2 = o[n4], s.push(null != (r = t3.find(e2)) ? r : e2);
                return s;
              }.call(this), new this.constructor(n3);
            }, i.prototype.appendText = function(t3) {
              return this.insertTextAtPosition(t3, this.getLength());
            }, i.prototype.insertTextAtPosition = function(t3, e2) {
              return this.copyWithPieceList(this.pieceList.insertSplittableListAtPosition(t3.pieceList, e2));
            }, i.prototype.removeTextAtRange = function(t3) {
              return this.copyWithPieceList(this.pieceList.removeObjectsInRange(t3));
            }, i.prototype.replaceTextAtRange = function(t3, e2) {
              return this.removeTextAtRange(e2).insertTextAtPosition(t3, e2[0]);
            }, i.prototype.moveTextFromRangeToPosition = function(t3, e2) {
              var n3, i2;
              if (!(t3[0] <= e2 && e2 <= t3[1]))
                return i2 = this.getTextAtRange(t3), n3 = i2.getLength(), t3[0] < e2 && (e2 -= n3), this.removeTextAtRange(t3).insertTextAtPosition(i2, e2);
            }, i.prototype.addAttributeAtRange = function(t3, e2, n3) {
              var i2;
              return i2 = {}, i2[t3] = e2, this.addAttributesAtRange(i2, n3);
            }, i.prototype.addAttributesAtRange = function(t3, e2) {
              return this.copyWithPieceList(this.pieceList.transformObjectsInRange(e2, function(e3) {
                return e3.copyWithAdditionalAttributes(t3);
              }));
            }, i.prototype.removeAttributeAtRange = function(t3, e2) {
              return this.copyWithPieceList(this.pieceList.transformObjectsInRange(e2, function(e3) {
                return e3.copyWithoutAttribute(t3);
              }));
            }, i.prototype.setAttributesAtRange = function(t3, e2) {
              return this.copyWithPieceList(this.pieceList.transformObjectsInRange(e2, function(e3) {
                return e3.copyWithAttributes(t3);
              }));
            }, i.prototype.getAttributesAtPosition = function(t3) {
              var e2, n3;
              return null != (e2 = null != (n3 = this.pieceList.getObjectAtPosition(t3)) ? n3.getAttributes() : void 0) ? e2 : {};
            }, i.prototype.getCommonAttributes = function() {
              var t3, n3;
              return t3 = function() {
                var t4, e2, i2, o;
                for (i2 = this.pieceList.toArray(), o = [], t4 = 0, e2 = i2.length; e2 > t4; t4++)
                  n3 = i2[t4], o.push(n3.getAttributes());
                return o;
              }.call(this), e.Hash.fromCommonAttributesOfObjects(t3).toObject();
            }, i.prototype.getCommonAttributesAtRange = function(t3) {
              var e2;
              return null != (e2 = this.getTextAtRange(t3).getCommonAttributes()) ? e2 : {};
            }, i.prototype.getExpandedRangeForAttributeAtOffset = function(t3, e2) {
              var n3, i2, o;
              for (n3 = o = e2, i2 = this.getLength(); n3 > 0 && this.getCommonAttributesAtRange([n3 - 1, o])[t3]; )
                n3--;
              for (; i2 > o && this.getCommonAttributesAtRange([e2, o + 1])[t3]; )
                o++;
              return [n3, o];
            }, i.prototype.getTextAtRange = function(t3) {
              return this.copyWithPieceList(this.pieceList.getSplittableListInRange(t3));
            }, i.prototype.getStringAtRange = function(t3) {
              return this.pieceList.getSplittableListInRange(t3).toString();
            }, i.prototype.getStringAtPosition = function(t3) {
              return this.getStringAtRange([t3, t3 + 1]);
            }, i.prototype.startsWithString = function(t3) {
              return this.getStringAtRange([0, t3.length]) === t3;
            }, i.prototype.endsWithString = function(t3) {
              var e2;
              return e2 = this.getLength(), this.getStringAtRange([e2 - t3.length, e2]) === t3;
            }, i.prototype.getAttachmentPieces = function() {
              var t3, e2, n3, i2, o;
              for (i2 = this.pieceList.toArray(), o = [], t3 = 0, e2 = i2.length; e2 > t3; t3++)
                n3 = i2[t3], null != n3.attachment && o.push(n3);
              return o;
            }, i.prototype.getAttachments = function() {
              var t3, e2, n3, i2, o;
              for (i2 = this.getAttachmentPieces(), o = [], t3 = 0, e2 = i2.length; e2 > t3; t3++)
                n3 = i2[t3], o.push(n3.attachment);
              return o;
            }, i.prototype.getAttachmentAndPositionById = function(t3) {
              var e2, n3, i2, o, r, s;
              for (o = 0, r = this.pieceList.toArray(), e2 = 0, n3 = r.length; n3 > e2; e2++) {
                if (i2 = r[e2], (null != (s = i2.attachment) ? s.id : void 0) === t3)
                  return { attachment: i2.attachment, position: o };
                o += i2.length;
              }
              return { attachment: null, position: null };
            }, i.prototype.getAttachmentById = function(t3) {
              var e2, n3, i2;
              return i2 = this.getAttachmentAndPositionById(t3), e2 = i2.attachment, n3 = i2.position, e2;
            }, i.prototype.getRangeOfAttachment = function(t3) {
              var e2, n3;
              return n3 = this.getAttachmentAndPositionById(t3.id), t3 = n3.attachment, e2 = n3.position, null != t3 ? [e2, e2 + 1] : void 0;
            }, i.prototype.updateAttributesForAttachment = function(t3, e2) {
              var n3;
              return (n3 = this.getRangeOfAttachment(e2)) ? this.addAttributesAtRange(t3, n3) : this;
            }, i.prototype.getLength = function() {
              return this.pieceList.getEndPosition();
            }, i.prototype.isEmpty = function() {
              return 0 === this.getLength();
            }, i.prototype.isEqualTo = function(t3) {
              var e2;
              return i.__super__.isEqualTo.apply(this, arguments) || (null != t3 && null != (e2 = t3.pieceList) ? e2.isEqualTo(this.pieceList) : void 0);
            }, i.prototype.isBlockBreak = function() {
              return 1 === this.getLength() && this.pieceList.getObjectAtIndex(0).isBlockBreak();
            }, i.prototype.eachPiece = function(t3) {
              return this.pieceList.eachObject(t3);
            }, i.prototype.getPieces = function() {
              return this.pieceList.toArray();
            }, i.prototype.getPieceAtPosition = function(t3) {
              return this.pieceList.getObjectAtPosition(t3);
            }, i.prototype.contentsForInspection = function() {
              return { pieceList: this.pieceList.inspect() };
            }, i.prototype.toSerializableText = function() {
              var t3;
              return t3 = this.pieceList.selectSplittableList(function(t4) {
                return t4.isSerializable();
              }), this.copyWithPieceList(t3);
            }, i.prototype.toString = function() {
              return this.pieceList.toString();
            }, i.prototype.toJSON = function() {
              return this.pieceList.toJSON();
            }, i.prototype.toConsole = function() {
              var t3;
              return JSON.stringify(function() {
                var e2, n3, i2, o;
                for (i2 = this.pieceList.toArray(), o = [], e2 = 0, n3 = i2.length; n3 > e2; e2++)
                  t3 = i2[e2], o.push(JSON.parse(t3.toConsole()));
                return o;
              }.call(this));
            }, i.prototype.getDirection = function() {
              return e.getDirection(this.toString());
            }, i.prototype.isRTL = function() {
              return "rtl" === this.getDirection();
            }, i;
          }(e.Object);
        }.call(this), function() {
          var t2, n, i, o, r, s = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              a.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, a = {}.hasOwnProperty, u = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          }, c = [].slice;
          t2 = e.arraysAreEqual, r = e.spliceArray, i = e.getBlockConfig, n = e.getBlockAttributeNames, o = e.getListAttributeNames, e.Block = function(n2) {
            function a2(t3, n3) {
              null == t3 && (t3 = new e.Text()), null == n3 && (n3 = []), a2.__super__.constructor.apply(this, arguments), this.text = h(t3), this.attributes = n3;
            }
            var l, h, p, d, f, g, m, v, y;
            return s(a2, n2), a2.fromJSON = function(t3) {
              var n3;
              return n3 = e.Text.fromJSON(t3.text), new this(n3, t3.attributes);
            }, a2.prototype.isEmpty = function() {
              return this.text.isBlockBreak();
            }, a2.prototype.isEqualTo = function(e2) {
              return a2.__super__.isEqualTo.apply(this, arguments) || this.text.isEqualTo(null != e2 ? e2.text : void 0) && t2(this.attributes, null != e2 ? e2.attributes : void 0);
            }, a2.prototype.copyWithText = function(t3) {
              return new this.constructor(t3, this.attributes);
            }, a2.prototype.copyWithoutText = function() {
              return this.copyWithText(null);
            }, a2.prototype.copyWithAttributes = function(t3) {
              return new this.constructor(this.text, t3);
            }, a2.prototype.copyWithoutAttributes = function() {
              return this.copyWithAttributes(null);
            }, a2.prototype.copyUsingObjectMap = function(t3) {
              var e2;
              return this.copyWithText((e2 = t3.find(this.text)) ? e2 : this.text.copyUsingObjectMap(t3));
            }, a2.prototype.addAttribute = function(t3) {
              var e2;
              return e2 = this.attributes.concat(d(t3)), this.copyWithAttributes(e2);
            }, a2.prototype.removeAttribute = function(t3) {
              var e2, n3;
              return n3 = i(t3).listAttribute, e2 = g(g(this.attributes, t3), n3), this.copyWithAttributes(e2);
            }, a2.prototype.removeLastAttribute = function() {
              return this.removeAttribute(this.getLastAttribute());
            }, a2.prototype.getLastAttribute = function() {
              return f(this.attributes);
            }, a2.prototype.getAttributes = function() {
              return this.attributes.slice(0);
            }, a2.prototype.getAttributeLevel = function() {
              return this.attributes.length;
            }, a2.prototype.getAttributeAtLevel = function(t3) {
              return this.attributes[t3 - 1];
            }, a2.prototype.hasAttribute = function(t3) {
              return u.call(this.attributes, t3) >= 0;
            }, a2.prototype.hasAttributes = function() {
              return this.getAttributeLevel() > 0;
            }, a2.prototype.getLastNestableAttribute = function() {
              return f(this.getNestableAttributes());
            }, a2.prototype.getNestableAttributes = function() {
              var t3, e2, n3, o2, r2;
              for (o2 = this.attributes, r2 = [], e2 = 0, n3 = o2.length; n3 > e2; e2++)
                t3 = o2[e2], i(t3).nestable && r2.push(t3);
              return r2;
            }, a2.prototype.getNestingLevel = function() {
              return this.getNestableAttributes().length;
            }, a2.prototype.decreaseNestingLevel = function() {
              var t3;
              return (t3 = this.getLastNestableAttribute()) ? this.removeAttribute(t3) : this;
            }, a2.prototype.increaseNestingLevel = function() {
              var t3, e2, n3;
              return (t3 = this.getLastNestableAttribute()) ? (n3 = this.attributes.lastIndexOf(t3), e2 = r.apply(null, [this.attributes, n3 + 1, 0].concat(c.call(d(t3)))), this.copyWithAttributes(e2)) : this;
            }, a2.prototype.getListItemAttributes = function() {
              var t3, e2, n3, o2, r2;
              for (o2 = this.attributes, r2 = [], e2 = 0, n3 = o2.length; n3 > e2; e2++)
                t3 = o2[e2], i(t3).listAttribute && r2.push(t3);
              return r2;
            }, a2.prototype.isListItem = function() {
              var t3;
              return null != (t3 = i(this.getLastAttribute())) ? t3.listAttribute : void 0;
            }, a2.prototype.isTerminalBlock = function() {
              var t3;
              return null != (t3 = i(this.getLastAttribute())) ? t3.terminal : void 0;
            }, a2.prototype.breaksOnReturn = function() {
              var t3;
              return null != (t3 = i(this.getLastAttribute())) ? t3.breakOnReturn : void 0;
            }, a2.prototype.findLineBreakInDirectionFromPosition = function(t3, e2) {
              var n3, i2;
              return i2 = this.toString(), n3 = function() {
                switch (t3) {
                  case "forward":
                    return i2.indexOf("\n", e2);
                  case "backward":
                    return i2.slice(0, e2).lastIndexOf("\n");
                }
              }(), -1 !== n3 ? n3 : void 0;
            }, a2.prototype.contentsForInspection = function() {
              return { text: this.text.inspect(), attributes: this.attributes };
            }, a2.prototype.toString = function() {
              return this.text.toString();
            }, a2.prototype.toJSON = function() {
              return { text: this.text, attributes: this.attributes };
            }, a2.prototype.getDirection = function() {
              return this.text.getDirection();
            }, a2.prototype.isRTL = function() {
              return this.text.isRTL();
            }, a2.prototype.getLength = function() {
              return this.text.getLength();
            }, a2.prototype.canBeConsolidatedWith = function(t3) {
              return !this.hasAttributes() && !t3.hasAttributes() && this.getDirection() === t3.getDirection();
            }, a2.prototype.consolidateWith = function(t3) {
              var n3, i2;
              return n3 = e.Text.textForStringWithAttributes("\n"), i2 = this.getTextWithoutBlockBreak().appendText(n3), this.copyWithText(i2.appendText(t3.text));
            }, a2.prototype.splitAtOffset = function(t3) {
              var e2, n3;
              return 0 === t3 ? (e2 = null, n3 = this) : t3 === this.getLength() ? (e2 = this, n3 = null) : (e2 = this.copyWithText(this.text.getTextAtRange([0, t3])), n3 = this.copyWithText(this.text.getTextAtRange([t3, this.getLength()]))), [e2, n3];
            }, a2.prototype.getBlockBreakPosition = function() {
              return this.text.getLength() - 1;
            }, a2.prototype.getTextWithoutBlockBreak = function() {
              return m(this.text) ? this.text.getTextAtRange([0, this.getBlockBreakPosition()]) : this.text.copy();
            }, a2.prototype.canBeGrouped = function(t3) {
              return this.attributes[t3];
            }, a2.prototype.canBeGroupedWith = function(t3, e2) {
              var n3, r2, s2, a3;
              return s2 = t3.getAttributes(), r2 = s2[e2], n3 = this.attributes[e2], !(n3 !== r2 || i(n3).group === false && (a3 = s2[e2 + 1], u.call(o(), a3) < 0) || this.getDirection() !== t3.getDirection() && !t3.isEmpty());
            }, h = function(t3) {
              return t3 = y(t3), t3 = l(t3);
            }, y = function(t3) {
              var n3, i2, o2, r2, s2, a3;
              return r2 = false, a3 = t3.getPieces(), i2 = 2 <= a3.length ? c.call(a3, 0, n3 = a3.length - 1) : (n3 = 0, []), o2 = a3[n3++], null == o2 ? t3 : (i2 = function() {
                var t4, e2, n4;
                for (n4 = [], t4 = 0, e2 = i2.length; e2 > t4; t4++)
                  s2 = i2[t4], s2.isBlockBreak() ? (r2 = true, n4.push(v(s2))) : n4.push(s2);
                return n4;
              }(), r2 ? new e.Text(c.call(i2).concat([o2])) : t3);
            }, p = e.Text.textForStringWithAttributes("\n", { blockBreak: true }), l = function(t3) {
              return m(t3) ? t3 : t3.appendText(p);
            }, m = function(t3) {
              var e2, n3;
              return n3 = t3.getLength(), 0 === n3 ? false : (e2 = t3.getTextAtRange([n3 - 1, n3]), e2.isBlockBreak());
            }, v = function(t3) {
              return t3.copyWithoutAttribute("blockBreak");
            }, d = function(t3) {
              var e2;
              return e2 = i(t3).listAttribute, null != e2 ? [e2, t3] : [t3];
            }, f = function(t3) {
              return t3.slice(-1)[0];
            }, g = function(t3, e2) {
              var n3;
              return n3 = t3.lastIndexOf(e2), -1 === n3 ? t3 : r(t3, n3, 1);
            }, a2;
          }(e.Object);
        }.call(this), function() {
          var t2, n, i, o = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              r.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, r = {}.hasOwnProperty, s = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          }, a = [].slice;
          n = e.tagName, i = e.walkTree, t2 = e.nodeIsAttachmentElement, e.HTMLSanitizer = function(r2) {
            function u(t3, e2) {
              var n2;
              n2 = null != e2 ? e2 : {}, this.allowedAttributes = n2.allowedAttributes, this.forbiddenProtocols = n2.forbiddenProtocols, this.forbiddenElements = n2.forbiddenElements, null == this.allowedAttributes && (this.allowedAttributes = c), null == this.forbiddenProtocols && (this.forbiddenProtocols = h), null == this.forbiddenElements && (this.forbiddenElements = l), this.body = p(t3);
            }
            var c, l, h, p;
            return o(u, r2), c = "style href src width height class".split(" "), h = "javascript:".split(" "), l = "script iframe".split(" "), u.sanitize = function(t3, e2) {
              var n2;
              return n2 = new this(t3, e2), n2.sanitize(), n2;
            }, u.prototype.sanitize = function() {
              return this.sanitizeElements(), this.normalizeListElementNesting();
            }, u.prototype.getHTML = function() {
              return this.body.innerHTML;
            }, u.prototype.getBody = function() {
              return this.body;
            }, u.prototype.sanitizeElements = function() {
              var t3, n2, o2, r3, s2;
              for (s2 = i(this.body), r3 = []; s2.nextNode(); )
                switch (o2 = s2.currentNode, o2.nodeType) {
                  case Node.ELEMENT_NODE:
                    this.elementIsRemovable(o2) ? r3.push(o2) : this.sanitizeElement(o2);
                    break;
                  case Node.COMMENT_NODE:
                    r3.push(o2);
                }
              for (t3 = 0, n2 = r3.length; n2 > t3; t3++)
                o2 = r3[t3], e.removeNode(o2);
              return this.body;
            }, u.prototype.sanitizeElement = function(t3) {
              var e2, n2, i2, o2, r3;
              for (t3.hasAttribute("href") && (o2 = t3.protocol, s.call(this.forbiddenProtocols, o2) >= 0 && t3.removeAttribute("href")), r3 = a.call(t3.attributes), e2 = 0, n2 = r3.length; n2 > e2; e2++)
                i2 = r3[e2].name, s.call(this.allowedAttributes, i2) >= 0 || 0 === i2.indexOf("data-trix") || t3.removeAttribute(i2);
              return t3;
            }, u.prototype.normalizeListElementNesting = function() {
              var t3, e2, i2, o2, r3;
              for (r3 = a.call(this.body.querySelectorAll("ul,ol")), t3 = 0, e2 = r3.length; e2 > t3; t3++)
                i2 = r3[t3], (o2 = i2.previousElementSibling) && "li" === n(o2) && o2.appendChild(i2);
              return this.body;
            }, u.prototype.elementIsRemovable = function(t3) {
              return (null != t3 ? t3.nodeType : void 0) === Node.ELEMENT_NODE ? this.elementIsForbidden(t3) || this.elementIsntSerializable(t3) : void 0;
            }, u.prototype.elementIsForbidden = function(t3) {
              var e2;
              return e2 = n(t3), s.call(this.forbiddenElements, e2) >= 0;
            }, u.prototype.elementIsntSerializable = function(e2) {
              return "false" === e2.getAttribute("data-trix-serialize") && !t2(e2);
            }, p = function(t3) {
              var e2, n2, i2, o2, r3;
              for (null == t3 && (t3 = ""), t3 = t3.replace(/<\/html[^>]*>[^]*$/i, "</html>"), e2 = document.implementation.createHTMLDocument(""), e2.documentElement.innerHTML = t3, r3 = e2.head.querySelectorAll("style"), i2 = 0, o2 = r3.length; o2 > i2; i2++)
                n2 = r3[i2], e2.body.appendChild(n2);
              return e2.body;
            }, u;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o, r, s, a, u, c, l, h, p = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              d.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, d = {}.hasOwnProperty, f = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          t2 = e.arraysAreEqual, s = e.makeElement, l = e.tagName, r = e.getBlockTagNames, h = e.walkTree, o = e.findClosestElementFromNode, i = e.elementContainsNode, a = e.nodeIsAttachmentElement, u = e.normalizeSpaces, n = e.breakableWhitespacePattern, c = e.squishBreakableWhitespace, e.HTMLParser = function(d2) {
            function g(t3, e2) {
              this.html = t3, this.referenceElement = (null != e2 ? e2 : {}).referenceElement, this.blocks = [], this.blockElements = [], this.processedElements = [];
            }
            var m, v, y, b, A, C, x, w, E, S, R, k;
            return p(g, d2), g.parse = function(t3, e2) {
              var n2;
              return n2 = new this(t3, e2), n2.parse(), n2;
            }, g.prototype.getDocument = function() {
              return e.Document.fromJSON(this.blocks);
            }, g.prototype.parse = function() {
              var t3, n2;
              try {
                for (this.createHiddenContainer(), t3 = e.HTMLSanitizer.sanitize(this.html).getHTML(), this.containerElement.innerHTML = t3, n2 = h(this.containerElement, { usingFilter: x }); n2.nextNode(); )
                  this.processNode(n2.currentNode);
                return this.translateBlockElementMarginsToNewlines();
              } finally {
                this.removeHiddenContainer();
              }
            }, g.prototype.createHiddenContainer = function() {
              return this.referenceElement ? (this.containerElement = this.referenceElement.cloneNode(false), this.containerElement.removeAttribute("id"), this.containerElement.setAttribute("data-trix-internal", ""), this.containerElement.style.display = "none", this.referenceElement.parentNode.insertBefore(this.containerElement, this.referenceElement.nextSibling)) : (this.containerElement = s({ tagName: "div", style: { display: "none" } }), document.body.appendChild(this.containerElement));
            }, g.prototype.removeHiddenContainer = function() {
              return e.removeNode(this.containerElement);
            }, x = function(t3) {
              return "style" === l(t3) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
            }, g.prototype.processNode = function(t3) {
              switch (t3.nodeType) {
                case Node.TEXT_NODE:
                  if (!this.isInsignificantTextNode(t3))
                    return this.appendBlockForTextNode(t3), this.processTextNode(t3);
                  break;
                case Node.ELEMENT_NODE:
                  return this.appendBlockForElement(t3), this.processElement(t3);
              }
            }, g.prototype.appendBlockForTextNode = function(e2) {
              var n2, i2, o2;
              return i2 = e2.parentNode, i2 === this.currentBlockElement && this.isBlockElement(e2.previousSibling) ? this.appendStringWithAttributes("\n") : i2 !== this.containerElement && !this.isBlockElement(i2) || (n2 = this.getBlockAttributes(i2), t2(n2, null != (o2 = this.currentBlock) ? o2.attributes : void 0)) ? void 0 : (this.currentBlock = this.appendBlockForAttributesWithElement(n2, i2), this.currentBlockElement = i2);
            }, g.prototype.appendBlockForElement = function(e2) {
              var n2, o2, r2, s2;
              if (r2 = this.isBlockElement(e2), o2 = i(this.currentBlockElement, e2), r2 && !this.isBlockElement(e2.firstChild)) {
                if ((!this.isInsignificantTextNode(e2.firstChild) || !this.isBlockElement(e2.firstElementChild)) && (n2 = this.getBlockAttributes(e2), e2.firstChild))
                  return o2 && t2(n2, this.currentBlock.attributes) ? this.appendStringWithAttributes("\n") : (this.currentBlock = this.appendBlockForAttributesWithElement(n2, e2), this.currentBlockElement = e2);
              } else if (this.currentBlockElement && !o2 && !r2)
                return (s2 = this.findParentBlockElement(e2)) ? this.appendBlockForElement(s2) : (this.currentBlock = this.appendEmptyBlock(), this.currentBlockElement = null);
            }, g.prototype.findParentBlockElement = function(t3) {
              var e2;
              for (e2 = t3.parentElement; e2 && e2 !== this.containerElement; ) {
                if (this.isBlockElement(e2) && f.call(this.blockElements, e2) >= 0)
                  return e2;
                e2 = e2.parentElement;
              }
              return null;
            }, g.prototype.processTextNode = function(t3) {
              var e2, n2;
              return n2 = t3.data, v(t3.parentNode) || (n2 = c(n2), R(null != (e2 = t3.previousSibling) ? e2.textContent : void 0) && (n2 = A(n2))), this.appendStringWithAttributes(n2, this.getTextAttributes(t3.parentNode));
            }, g.prototype.processElement = function(t3) {
              var e2, n2, i2, o2, r2;
              if (a(t3))
                return e2 = w(t3, "attachment"), Object.keys(e2).length && (o2 = this.getTextAttributes(t3), this.appendAttachmentWithAttributes(e2, o2), t3.innerHTML = ""), this.processedElements.push(t3);
              switch (l(t3)) {
                case "br":
                  return this.isExtraBR(t3) || this.isBlockElement(t3.nextSibling) || this.appendStringWithAttributes("\n", this.getTextAttributes(t3)), this.processedElements.push(t3);
                case "img":
                  e2 = { url: t3.getAttribute("src"), contentType: "image" }, i2 = b(t3);
                  for (n2 in i2)
                    r2 = i2[n2], e2[n2] = r2;
                  return this.appendAttachmentWithAttributes(e2, this.getTextAttributes(t3)), this.processedElements.push(t3);
                case "tr":
                  if (t3.parentNode.firstChild !== t3)
                    return this.appendStringWithAttributes("\n");
                  break;
                case "td":
                  if (t3.parentNode.firstChild !== t3)
                    return this.appendStringWithAttributes(" | ");
              }
            }, g.prototype.appendBlockForAttributesWithElement = function(t3, e2) {
              var n2;
              return this.blockElements.push(e2), n2 = m(t3), this.blocks.push(n2), n2;
            }, g.prototype.appendEmptyBlock = function() {
              return this.appendBlockForAttributesWithElement([], null);
            }, g.prototype.appendStringWithAttributes = function(t3, e2) {
              return this.appendPiece(S(t3, e2));
            }, g.prototype.appendAttachmentWithAttributes = function(t3, e2) {
              return this.appendPiece(E(t3, e2));
            }, g.prototype.appendPiece = function(t3) {
              return 0 === this.blocks.length && this.appendEmptyBlock(), this.blocks[this.blocks.length - 1].text.push(t3);
            }, g.prototype.appendStringToTextAtIndex = function(t3, e2) {
              var n2, i2;
              return i2 = this.blocks[e2].text, n2 = i2[i2.length - 1], "string" === (null != n2 ? n2.type : void 0) ? n2.string += t3 : i2.push(S(t3));
            }, g.prototype.prependStringToTextAtIndex = function(t3, e2) {
              var n2, i2;
              return i2 = this.blocks[e2].text, n2 = i2[0], "string" === (null != n2 ? n2.type : void 0) ? n2.string = t3 + n2.string : i2.unshift(S(t3));
            }, S = function(t3, e2) {
              var n2;
              return null == e2 && (e2 = {}), n2 = "string", t3 = u(t3), { string: t3, attributes: e2, type: n2 };
            }, E = function(t3, e2) {
              var n2;
              return null == e2 && (e2 = {}), n2 = "attachment", { attachment: t3, attributes: e2, type: n2 };
            }, m = function(t3) {
              var e2;
              return null == t3 && (t3 = {}), e2 = [], { text: e2, attributes: t3 };
            }, g.prototype.getTextAttributes = function(t3) {
              var n2, i2, r2, s2, u2, c2, l2, h2, p2, d3, f2, g2;
              r2 = {}, p2 = e.config.textAttributes;
              for (n2 in p2)
                if (u2 = p2[n2], u2.tagName && o(t3, { matchingSelector: u2.tagName, untilNode: this.containerElement }))
                  r2[n2] = true;
                else if (u2.parser) {
                  if (g2 = u2.parser(t3)) {
                    for (i2 = false, d3 = this.findBlockElementAncestors(t3), c2 = 0, h2 = d3.length; h2 > c2; c2++)
                      if (s2 = d3[c2], u2.parser(s2) === g2) {
                        i2 = true;
                        break;
                      }
                    i2 || (r2[n2] = g2);
                  }
                } else
                  u2.styleProperty && (g2 = t3.style[u2.styleProperty]) && (r2[n2] = g2);
              if (a(t3)) {
                f2 = w(t3, "attributes");
                for (l2 in f2)
                  g2 = f2[l2], r2[l2] = g2;
              }
              return r2;
            }, g.prototype.getBlockAttributes = function(t3) {
              var n2, i2, o2, r2;
              for (i2 = []; t3 && t3 !== this.containerElement; ) {
                r2 = e.config.blockAttributes;
                for (n2 in r2)
                  o2 = r2[n2], o2.parse !== false && l(t3) === o2.tagName && (("function" == typeof o2.test ? o2.test(t3) : void 0) || !o2.test) && (i2.push(n2), o2.listAttribute && i2.push(o2.listAttribute));
                t3 = t3.parentNode;
              }
              return i2.reverse();
            }, g.prototype.findBlockElementAncestors = function(t3) {
              var e2, n2;
              for (e2 = []; t3 && t3 !== this.containerElement; )
                n2 = l(t3), f.call(r(), n2) >= 0 && e2.push(t3), t3 = t3.parentNode;
              return e2;
            }, w = function(t3, e2) {
              try {
                return JSON.parse(t3.getAttribute("data-trix-" + e2));
              } catch (n2) {
                return {};
              }
            }, b = function(t3) {
              var e2, n2, i2;
              return i2 = t3.getAttribute("width"), n2 = t3.getAttribute("height"), e2 = {}, i2 && (e2.width = parseInt(i2, 10)), n2 && (e2.height = parseInt(n2, 10)), e2;
            }, g.prototype.isBlockElement = function(t3) {
              var e2;
              if ((null != t3 ? t3.nodeType : void 0) === Node.ELEMENT_NODE && !a(t3) && !o(t3, { matchingSelector: "td", untilNode: this.containerElement }))
                return e2 = l(t3), f.call(r(), e2) >= 0 || "block" === window.getComputedStyle(t3).display;
            }, g.prototype.isInsignificantTextNode = function(t3) {
              var e2, n2, i2;
              if ((null != t3 ? t3.nodeType : void 0) === Node.TEXT_NODE && k(t3.data) && (n2 = t3.parentNode, i2 = t3.previousSibling, e2 = t3.nextSibling, (!C(n2.previousSibling) || this.isBlockElement(n2.previousSibling)) && !v(n2)))
                return !i2 || this.isBlockElement(i2) || !e2 || this.isBlockElement(e2);
            }, g.prototype.isExtraBR = function(t3) {
              return "br" === l(t3) && this.isBlockElement(t3.parentNode) && t3.parentNode.lastChild === t3;
            }, v = function(t3) {
              var e2;
              return e2 = window.getComputedStyle(t3).whiteSpace, "pre" === e2 || "pre-wrap" === e2 || "pre-line" === e2;
            }, C = function(t3) {
              return t3 && !R(t3.textContent);
            }, g.prototype.translateBlockElementMarginsToNewlines = function() {
              var t3, e2, n2, i2, o2, r2, s2, a2;
              for (e2 = this.getMarginOfDefaultBlockElement(), s2 = this.blocks, a2 = [], i2 = n2 = 0, o2 = s2.length; o2 > n2; i2 = ++n2)
                t3 = s2[i2], (r2 = this.getMarginOfBlockElementAtIndex(i2)) && (r2.top > 2 * e2.top && this.prependStringToTextAtIndex("\n", i2), a2.push(r2.bottom > 2 * e2.bottom ? this.appendStringToTextAtIndex("\n", i2) : void 0));
              return a2;
            }, g.prototype.getMarginOfBlockElementAtIndex = function(t3) {
              var e2, n2;
              return !(e2 = this.blockElements[t3]) || !e2.textContent || (n2 = l(e2), f.call(r(), n2) >= 0 || f.call(this.processedElements, e2) >= 0) ? void 0 : y(e2);
            }, g.prototype.getMarginOfDefaultBlockElement = function() {
              var t3;
              return t3 = s(e.config.blockAttributes["default"].tagName), this.containerElement.appendChild(t3), y(t3);
            }, y = function(t3) {
              var e2;
              return e2 = window.getComputedStyle(t3), "block" === e2.display ? { top: parseInt(e2.marginTop), bottom: parseInt(e2.marginBottom) } : void 0;
            }, A = function(t3) {
              return t3.replace(RegExp("^" + n.source + "+"), "");
            }, k = function(t3) {
              return RegExp("^" + n.source + "*$").test(t3);
            }, R = function(t3) {
              return /\s$/.test(t3);
            }, g;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o, r = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              s.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, s = {}.hasOwnProperty, a = [].slice, u = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          t2 = e.arraysAreEqual, i = e.normalizeRange, o = e.rangeIsCollapsed, n = e.getBlockConfig, e.Document = function(s2) {
            function c(t3) {
              null == t3 && (t3 = []), c.__super__.constructor.apply(this, arguments), 0 === t3.length && (t3 = [new e.Block()]), this.blockList = e.SplittableList.box(t3);
            }
            var l;
            return r(c, s2), c.fromJSON = function(t3) {
              var n2, i2;
              return i2 = function() {
                var i3, o2, r2;
                for (r2 = [], i3 = 0, o2 = t3.length; o2 > i3; i3++)
                  n2 = t3[i3], r2.push(e.Block.fromJSON(n2));
                return r2;
              }(), new this(i2);
            }, c.fromHTML = function(t3, n2) {
              return e.HTMLParser.parse(t3, n2).getDocument();
            }, c.fromString = function(t3, n2) {
              var i2;
              return i2 = e.Text.textForStringWithAttributes(t3, n2), new this([new e.Block(i2)]);
            }, c.prototype.isEmpty = function() {
              var t3;
              return 1 === this.blockList.length && (t3 = this.getBlockAtIndex(0), t3.isEmpty() && !t3.hasAttributes());
            }, c.prototype.copy = function(t3) {
              var e2;
              return null == t3 && (t3 = {}), e2 = t3.consolidateBlocks ? this.blockList.consolidate().toArray() : this.blockList.toArray(), new this.constructor(e2);
            }, c.prototype.copyUsingObjectsFromDocument = function(t3) {
              var n2;
              return n2 = new e.ObjectMap(t3.getObjects()), this.copyUsingObjectMap(n2);
            }, c.prototype.copyUsingObjectMap = function(t3) {
              var e2, n2, i2;
              return n2 = function() {
                var n3, o2, r2, s3;
                for (r2 = this.getBlocks(), s3 = [], n3 = 0, o2 = r2.length; o2 > n3; n3++)
                  e2 = r2[n3], s3.push((i2 = t3.find(e2)) ? i2 : e2.copyUsingObjectMap(t3));
                return s3;
              }.call(this), new this.constructor(n2);
            }, c.prototype.copyWithBaseBlockAttributes = function(t3) {
              var e2, n2, i2;
              return null == t3 && (t3 = []), i2 = function() {
                var i3, o2, r2, s3;
                for (r2 = this.getBlocks(), s3 = [], i3 = 0, o2 = r2.length; o2 > i3; i3++)
                  n2 = r2[i3], e2 = t3.concat(n2.getAttributes()), s3.push(n2.copyWithAttributes(e2));
                return s3;
              }.call(this), new this.constructor(i2);
            }, c.prototype.replaceBlock = function(t3, e2) {
              var n2;
              return n2 = this.blockList.indexOf(t3), -1 === n2 ? this : new this.constructor(this.blockList.replaceObjectAtIndex(e2, n2));
            }, c.prototype.insertDocumentAtRange = function(t3, e2) {
              var n2, r2, s3, a2, u2, c2, l2;
              return r2 = t3.blockList, u2 = (e2 = i(e2))[0], c2 = this.locationFromPosition(u2), s3 = c2.index, a2 = c2.offset, l2 = this, n2 = this.getBlockAtPosition(u2), o(e2) && n2.isEmpty() && !n2.hasAttributes() ? l2 = new this.constructor(l2.blockList.removeObjectAtIndex(s3)) : n2.getBlockBreakPosition() === a2 && u2++, l2 = l2.removeTextAtRange(e2), new this.constructor(l2.blockList.insertSplittableListAtPosition(r2, u2));
            }, c.prototype.mergeDocumentAtRange = function(e2, n2) {
              var o2, r2, s3, a2, u2, c2, l2, h, p, d, f, g;
              return f = (n2 = i(n2))[0], d = this.locationFromPosition(f), r2 = this.getBlockAtIndex(d.index).getAttributes(), o2 = e2.getBaseBlockAttributes(), g = r2.slice(-o2.length), t2(o2, g) ? (l2 = r2.slice(0, -o2.length), c2 = e2.copyWithBaseBlockAttributes(l2)) : c2 = e2.copy({ consolidateBlocks: true }).copyWithBaseBlockAttributes(r2), s3 = c2.getBlockCount(), a2 = c2.getBlockAtIndex(0), t2(r2, a2.getAttributes()) ? (u2 = a2.getTextWithoutBlockBreak(), p = this.insertTextAtRange(u2, n2), s3 > 1 && (c2 = new this.constructor(c2.getBlocks().slice(1)), h = f + u2.getLength(), p = p.insertDocumentAtRange(c2, h))) : p = this.insertDocumentAtRange(c2, n2), p;
            }, c.prototype.insertTextAtRange = function(t3, e2) {
              var n2, o2, r2, s3, a2;
              return a2 = (e2 = i(e2))[0], s3 = this.locationFromPosition(a2), o2 = s3.index, r2 = s3.offset, n2 = this.removeTextAtRange(e2), new this.constructor(n2.blockList.editObjectAtIndex(o2, function(e3) {
                return e3.copyWithText(e3.text.insertTextAtPosition(t3, r2));
              }));
            }, c.prototype.removeTextAtRange = function(t3) {
              var e2, n2, r2, s3, a2, u2, c2, l2, h, p, d, f, g, m, v, y, b, A, C, x, w;
              return p = t3 = i(t3), l2 = p[0], A = p[1], o(t3) ? this : (d = this.locationRangeFromRange(t3), u2 = d[0], y = d[1], a2 = u2.index, c2 = u2.offset, s3 = this.getBlockAtIndex(a2), v = y.index, b = y.offset, m = this.getBlockAtIndex(v), f = A - l2 === 1 && s3.getBlockBreakPosition() === c2 && m.getBlockBreakPosition() !== b && "\n" === m.text.getStringAtPosition(b), f ? r2 = this.blockList.editObjectAtIndex(v, function(t4) {
                return t4.copyWithText(t4.text.removeTextAtRange([b, b + 1]));
              }) : (h = s3.text.getTextAtRange([0, c2]), C = m.text.getTextAtRange([b, m.getLength()]), x = h.appendText(C), g = a2 !== v && 0 === c2, w = g && s3.getAttributeLevel() >= m.getAttributeLevel(), n2 = w ? m.copyWithText(x) : s3.copyWithText(x), e2 = v + 1 - a2, r2 = this.blockList.splice(a2, e2, n2)), new this.constructor(r2));
            }, c.prototype.moveTextFromRangeToPosition = function(t3, e2) {
              var n2, o2, r2, s3, u2, c2, l2, h, p, d;
              return c2 = t3 = i(t3), p = c2[0], r2 = c2[1], e2 >= p && r2 >= e2 ? this : (o2 = this.getDocumentAtRange(t3), h = this.removeTextAtRange(t3), u2 = e2 > p, u2 && (e2 -= o2.getLength()), l2 = o2.getBlocks(), s3 = l2[0], n2 = 2 <= l2.length ? a.call(l2, 1) : [], 0 === n2.length ? (d = s3.getTextWithoutBlockBreak(), u2 && (e2 += 1)) : d = s3.text, h = h.insertTextAtRange(d, e2), 0 === n2.length ? h : (o2 = new this.constructor(n2), e2 += d.getLength(), h.insertDocumentAtRange(o2, e2)));
            }, c.prototype.addAttributeAtRange = function(t3, e2, i2) {
              var o2;
              return o2 = this.blockList, this.eachBlockAtRange(i2, function(i3, r2, s3) {
                return o2 = o2.editObjectAtIndex(s3, function() {
                  return n(t3) ? i3.addAttribute(t3, e2) : r2[0] === r2[1] ? i3 : i3.copyWithText(i3.text.addAttributeAtRange(t3, e2, r2));
                });
              }), new this.constructor(o2);
            }, c.prototype.addAttribute = function(t3, e2) {
              var n2;
              return n2 = this.blockList, this.eachBlock(function(i2, o2) {
                return n2 = n2.editObjectAtIndex(o2, function() {
                  return i2.addAttribute(t3, e2);
                });
              }), new this.constructor(n2);
            }, c.prototype.removeAttributeAtRange = function(t3, e2) {
              var i2;
              return i2 = this.blockList, this.eachBlockAtRange(e2, function(e3, o2, r2) {
                return n(t3) ? i2 = i2.editObjectAtIndex(r2, function() {
                  return e3.removeAttribute(t3);
                }) : o2[0] !== o2[1] ? i2 = i2.editObjectAtIndex(r2, function() {
                  return e3.copyWithText(e3.text.removeAttributeAtRange(t3, o2));
                }) : void 0;
              }), new this.constructor(i2);
            }, c.prototype.updateAttributesForAttachment = function(t3, e2) {
              var n2, i2, o2, r2;
              return o2 = (i2 = this.getRangeOfAttachment(e2))[0], n2 = this.locationFromPosition(o2).index, r2 = this.getTextAtIndex(n2), new this.constructor(this.blockList.editObjectAtIndex(n2, function(n3) {
                return n3.copyWithText(r2.updateAttributesForAttachment(t3, e2));
              }));
            }, c.prototype.removeAttributeForAttachment = function(t3, e2) {
              var n2;
              return n2 = this.getRangeOfAttachment(e2), this.removeAttributeAtRange(t3, n2);
            }, c.prototype.insertBlockBreakAtRange = function(t3) {
              var n2, o2, r2, s3;
              return s3 = (t3 = i(t3))[0], r2 = this.locationFromPosition(s3).offset, o2 = this.removeTextAtRange(t3), 0 === r2 && (n2 = [new e.Block()]), new this.constructor(o2.blockList.insertSplittableListAtPosition(new e.SplittableList(n2), s3));
            }, c.prototype.applyBlockAttributeAtRange = function(t3, e2, i2) {
              var o2, r2, s3, a2;
              return s3 = this.expandRangeToLineBreaksAndSplitBlocks(i2), r2 = s3.document, i2 = s3.range, o2 = n(t3), o2.listAttribute ? (r2 = r2.removeLastListAttributeAtRange(i2, { exceptAttributeName: t3 }), a2 = r2.convertLineBreaksToBlockBreaksInRange(i2), r2 = a2.document, i2 = a2.range) : r2 = o2.exclusive ? r2.removeBlockAttributesAtRange(i2) : o2.terminal ? r2.removeLastTerminalAttributeAtRange(i2) : r2.consolidateBlocksAtRange(i2), r2.addAttributeAtRange(t3, e2, i2);
            }, c.prototype.removeLastListAttributeAtRange = function(t3, e2) {
              var i2;
              return null == e2 && (e2 = {}), i2 = this.blockList, this.eachBlockAtRange(t3, function(t4, o2, r2) {
                var s3;
                if ((s3 = t4.getLastAttribute()) && n(s3).listAttribute && s3 !== e2.exceptAttributeName)
                  return i2 = i2.editObjectAtIndex(r2, function() {
                    return t4.removeAttribute(s3);
                  });
              }), new this.constructor(i2);
            }, c.prototype.removeLastTerminalAttributeAtRange = function(t3) {
              var e2;
              return e2 = this.blockList, this.eachBlockAtRange(t3, function(t4, i2, o2) {
                var r2;
                if ((r2 = t4.getLastAttribute()) && n(r2).terminal)
                  return e2 = e2.editObjectAtIndex(o2, function() {
                    return t4.removeAttribute(r2);
                  });
              }), new this.constructor(e2);
            }, c.prototype.removeBlockAttributesAtRange = function(t3) {
              var e2;
              return e2 = this.blockList, this.eachBlockAtRange(t3, function(t4, n2, i2) {
                return t4.hasAttributes() ? e2 = e2.editObjectAtIndex(i2, function() {
                  return t4.copyWithoutAttributes();
                }) : void 0;
              }), new this.constructor(e2);
            }, c.prototype.expandRangeToLineBreaksAndSplitBlocks = function(t3) {
              var e2, n2, o2, r2, s3, a2, u2, c2, l2;
              return a2 = t3 = i(t3), l2 = a2[0], r2 = a2[1], c2 = this.locationFromPosition(l2), o2 = this.locationFromPosition(r2), e2 = this, u2 = e2.getBlockAtIndex(c2.index), null != (c2.offset = u2.findLineBreakInDirectionFromPosition("backward", c2.offset)) && (s3 = e2.positionFromLocation(c2), e2 = e2.insertBlockBreakAtRange([s3, s3 + 1]), o2.index += 1, o2.offset -= e2.getBlockAtIndex(c2.index).getLength(), c2.index += 1), c2.offset = 0, 0 === o2.offset && o2.index > c2.index ? (o2.index -= 1, o2.offset = e2.getBlockAtIndex(o2.index).getBlockBreakPosition()) : (n2 = e2.getBlockAtIndex(o2.index), "\n" === n2.text.getStringAtRange([o2.offset - 1, o2.offset]) ? o2.offset -= 1 : o2.offset = n2.findLineBreakInDirectionFromPosition("forward", o2.offset), o2.offset !== n2.getBlockBreakPosition() && (s3 = e2.positionFromLocation(o2), e2 = e2.insertBlockBreakAtRange([s3, s3 + 1]))), l2 = e2.positionFromLocation(c2), r2 = e2.positionFromLocation(o2), t3 = i([l2, r2]), { document: e2, range: t3 };
            }, c.prototype.convertLineBreaksToBlockBreaksInRange = function(t3) {
              var e2, n2, o2;
              return n2 = (t3 = i(t3))[0], o2 = this.getStringAtRange(t3).slice(0, -1), e2 = this, o2.replace(/.*?\n/g, function(t4) {
                return n2 += t4.length, e2 = e2.insertBlockBreakAtRange([n2 - 1, n2]);
              }), { document: e2, range: t3 };
            }, c.prototype.consolidateBlocksAtRange = function(t3) {
              var e2, n2, o2, r2, s3;
              return o2 = t3 = i(t3), s3 = o2[0], n2 = o2[1], r2 = this.locationFromPosition(s3).index, e2 = this.locationFromPosition(n2).index, new this.constructor(this.blockList.consolidateFromIndexToIndex(r2, e2));
            }, c.prototype.getDocumentAtRange = function(t3) {
              var e2;
              return t3 = i(t3), e2 = this.blockList.getSplittableListInRange(t3).toArray(), new this.constructor(e2);
            }, c.prototype.getStringAtRange = function(t3) {
              var e2, n2, o2;
              return o2 = t3 = i(t3), n2 = o2[o2.length - 1], n2 !== this.getLength() && (e2 = -1), this.getDocumentAtRange(t3).toString().slice(0, e2);
            }, c.prototype.getBlockAtIndex = function(t3) {
              return this.blockList.getObjectAtIndex(t3);
            }, c.prototype.getBlockAtPosition = function(t3) {
              var e2;
              return e2 = this.locationFromPosition(t3).index, this.getBlockAtIndex(e2);
            }, c.prototype.getTextAtIndex = function(t3) {
              var e2;
              return null != (e2 = this.getBlockAtIndex(t3)) ? e2.text : void 0;
            }, c.prototype.getTextAtPosition = function(t3) {
              var e2;
              return e2 = this.locationFromPosition(t3).index, this.getTextAtIndex(e2);
            }, c.prototype.getPieceAtPosition = function(t3) {
              var e2, n2, i2;
              return i2 = this.locationFromPosition(t3), e2 = i2.index, n2 = i2.offset, this.getTextAtIndex(e2).getPieceAtPosition(n2);
            }, c.prototype.getCharacterAtPosition = function(t3) {
              var e2, n2, i2;
              return i2 = this.locationFromPosition(t3), e2 = i2.index, n2 = i2.offset, this.getTextAtIndex(e2).getStringAtRange([n2, n2 + 1]);
            }, c.prototype.getLength = function() {
              return this.blockList.getEndPosition();
            }, c.prototype.getBlocks = function() {
              return this.blockList.toArray();
            }, c.prototype.getBlockCount = function() {
              return this.blockList.length;
            }, c.prototype.getEditCount = function() {
              return this.editCount;
            }, c.prototype.eachBlock = function(t3) {
              return this.blockList.eachObject(t3);
            }, c.prototype.eachBlockAtRange = function(t3, e2) {
              var n2, o2, r2, s3, a2, u2, c2, l2, h, p, d, f;
              if (u2 = t3 = i(t3), d = u2[0], r2 = u2[1], p = this.locationFromPosition(d), o2 = this.locationFromPosition(r2), p.index === o2.index)
                return n2 = this.getBlockAtIndex(p.index), f = [p.offset, o2.offset], e2(n2, f, p.index);
              for (h = [], a2 = s3 = c2 = p.index, l2 = o2.index; l2 >= c2 ? l2 >= s3 : s3 >= l2; a2 = l2 >= c2 ? ++s3 : --s3)
                (n2 = this.getBlockAtIndex(a2)) ? (f = function() {
                  switch (a2) {
                    case p.index:
                      return [p.offset, n2.text.getLength()];
                    case o2.index:
                      return [0, o2.offset];
                    default:
                      return [0, n2.text.getLength()];
                  }
                }(), h.push(e2(n2, f, a2))) : h.push(void 0);
              return h;
            }, c.prototype.getCommonAttributesAtRange = function(t3) {
              var n2, r2, s3;
              return r2 = (t3 = i(t3))[0], o(t3) ? this.getCommonAttributesAtPosition(r2) : (s3 = [], n2 = [], this.eachBlockAtRange(t3, function(t4, e2) {
                return e2[0] !== e2[1] ? (s3.push(t4.text.getCommonAttributesAtRange(e2)), n2.push(l(t4))) : void 0;
              }), e.Hash.fromCommonAttributesOfObjects(s3).merge(e.Hash.fromCommonAttributesOfObjects(n2)).toObject());
            }, c.prototype.getCommonAttributesAtPosition = function(t3) {
              var n2, i2, o2, r2, s3, a2, c2, h, p, d;
              if (p = this.locationFromPosition(t3), s3 = p.index, h = p.offset, o2 = this.getBlockAtIndex(s3), !o2)
                return {};
              r2 = l(o2), n2 = o2.text.getAttributesAtPosition(h), i2 = o2.text.getAttributesAtPosition(h - 1), a2 = function() {
                var t4, n3;
                t4 = e.config.textAttributes, n3 = [];
                for (c2 in t4)
                  d = t4[c2], d.inheritable && n3.push(c2);
                return n3;
              }();
              for (c2 in i2)
                d = i2[c2], (d === n2[c2] || u.call(a2, c2) >= 0) && (r2[c2] = d);
              return r2;
            }, c.prototype.getRangeOfCommonAttributeAtPosition = function(t3, e2) {
              var n2, o2, r2, s3, a2, u2, c2, l2, h;
              return a2 = this.locationFromPosition(e2), r2 = a2.index, s3 = a2.offset, h = this.getTextAtIndex(r2), u2 = h.getExpandedRangeForAttributeAtOffset(t3, s3), l2 = u2[0], o2 = u2[1], c2 = this.positionFromLocation({ index: r2, offset: l2 }), n2 = this.positionFromLocation({ index: r2, offset: o2 }), i([c2, n2]);
            }, c.prototype.getBaseBlockAttributes = function() {
              var t3, e2, n2, i2, o2, r2, s3;
              for (t3 = this.getBlockAtIndex(0).getAttributes(), n2 = i2 = 1, s3 = this.getBlockCount(); s3 >= 1 ? s3 > i2 : i2 > s3; n2 = s3 >= 1 ? ++i2 : --i2)
                e2 = this.getBlockAtIndex(n2).getAttributes(), r2 = Math.min(t3.length, e2.length), t3 = function() {
                  var n3, i3, s4;
                  for (s4 = [], o2 = n3 = 0, i3 = r2; (i3 >= 0 ? i3 > n3 : n3 > i3) && e2[o2] === t3[o2]; o2 = i3 >= 0 ? ++n3 : --n3)
                    s4.push(e2[o2]);
                  return s4;
                }();
              return t3;
            }, l = function(t3) {
              var e2, n2;
              return n2 = {}, (e2 = t3.getLastAttribute()) && (n2[e2] = true), n2;
            }, c.prototype.getAttachmentById = function(t3) {
              var e2, n2, i2, o2;
              for (o2 = this.getAttachments(), n2 = 0, i2 = o2.length; i2 > n2; n2++)
                if (e2 = o2[n2], e2.id === t3)
                  return e2;
            }, c.prototype.getAttachmentPieces = function() {
              var t3;
              return t3 = [], this.blockList.eachObject(function(e2) {
                var n2;
                return n2 = e2.text, t3 = t3.concat(n2.getAttachmentPieces());
              }), t3;
            }, c.prototype.getAttachments = function() {
              var t3, e2, n2, i2, o2;
              for (i2 = this.getAttachmentPieces(), o2 = [], t3 = 0, e2 = i2.length; e2 > t3; t3++)
                n2 = i2[t3], o2.push(n2.attachment);
              return o2;
            }, c.prototype.getRangeOfAttachment = function(t3) {
              var e2, n2, o2, r2, s3, a2, u2;
              for (r2 = 0, s3 = this.blockList.toArray(), n2 = e2 = 0, o2 = s3.length; o2 > e2; n2 = ++e2) {
                if (a2 = s3[n2].text, u2 = a2.getRangeOfAttachment(t3))
                  return i([r2 + u2[0], r2 + u2[1]]);
                r2 += a2.getLength();
              }
            }, c.prototype.getLocationRangeOfAttachment = function(t3) {
              var e2;
              return e2 = this.getRangeOfAttachment(t3), this.locationRangeFromRange(e2);
            }, c.prototype.getAttachmentPieceForAttachment = function(t3) {
              var e2, n2, i2, o2;
              for (o2 = this.getAttachmentPieces(), e2 = 0, n2 = o2.length; n2 > e2; e2++)
                if (i2 = o2[e2], i2.attachment === t3)
                  return i2;
            }, c.prototype.findRangesForBlockAttribute = function(t3) {
              var e2, n2, i2, o2, r2, s3, a2;
              for (r2 = 0, s3 = [], a2 = this.getBlocks(), n2 = 0, i2 = a2.length; i2 > n2; n2++)
                e2 = a2[n2], o2 = e2.getLength(), e2.hasAttribute(t3) && s3.push([r2, r2 + o2]), r2 += o2;
              return s3;
            }, c.prototype.findRangesForTextAttribute = function(t3, e2) {
              var n2, i2, o2, r2, s3, a2, u2, c2, l2, h;
              for (h = (null != e2 ? e2 : {}).withValue, a2 = 0, u2 = [], c2 = [], r2 = function(e3) {
                return null != h ? e3.getAttribute(t3) === h : e3.hasAttribute(t3);
              }, l2 = this.getPieces(), n2 = 0, i2 = l2.length; i2 > n2; n2++)
                s3 = l2[n2], o2 = s3.getLength(), r2(s3) && (u2[1] === a2 ? u2[1] = a2 + o2 : c2.push(u2 = [a2, a2 + o2])), a2 += o2;
              return c2;
            }, c.prototype.locationFromPosition = function(t3) {
              var e2, n2;
              return n2 = this.blockList.findIndexAndOffsetAtPosition(Math.max(0, t3)), null != n2.index ? n2 : (e2 = this.getBlocks(), { index: e2.length - 1, offset: e2[e2.length - 1].getLength() });
            }, c.prototype.positionFromLocation = function(t3) {
              return this.blockList.findPositionAtIndexAndOffset(t3.index, t3.offset);
            }, c.prototype.locationRangeFromPosition = function(t3) {
              return i(this.locationFromPosition(t3));
            }, c.prototype.locationRangeFromRange = function(t3) {
              var e2, n2, o2, r2;
              if (t3 = i(t3))
                return r2 = t3[0], n2 = t3[1], o2 = this.locationFromPosition(r2), e2 = this.locationFromPosition(n2), i([o2, e2]);
            }, c.prototype.rangeFromLocationRange = function(t3) {
              var e2, n2;
              return t3 = i(t3), e2 = this.positionFromLocation(t3[0]), o(t3) || (n2 = this.positionFromLocation(t3[1])), i([e2, n2]);
            }, c.prototype.isEqualTo = function(t3) {
              return this.blockList.isEqualTo(null != t3 ? t3.blockList : void 0);
            }, c.prototype.getTexts = function() {
              var t3, e2, n2, i2, o2;
              for (i2 = this.getBlocks(), o2 = [], e2 = 0, n2 = i2.length; n2 > e2; e2++)
                t3 = i2[e2], o2.push(t3.text);
              return o2;
            }, c.prototype.getPieces = function() {
              var t3, e2, n2, i2, o2;
              for (n2 = [], i2 = this.getTexts(), t3 = 0, e2 = i2.length; e2 > t3; t3++)
                o2 = i2[t3], n2.push.apply(n2, o2.getPieces());
              return n2;
            }, c.prototype.getObjects = function() {
              return this.getBlocks().concat(this.getTexts()).concat(this.getPieces());
            }, c.prototype.toSerializableDocument = function() {
              var t3;
              return t3 = [], this.blockList.eachObject(function(e2) {
                return t3.push(e2.copyWithText(e2.text.toSerializableText()));
              }), new this.constructor(t3);
            }, c.prototype.toString = function() {
              return this.blockList.toString();
            }, c.prototype.toJSON = function() {
              return this.blockList.toJSON();
            }, c.prototype.toConsole = function() {
              var t3;
              return JSON.stringify(function() {
                var e2, n2, i2, o2;
                for (i2 = this.blockList.toArray(), o2 = [], e2 = 0, n2 = i2.length; n2 > e2; e2++)
                  t3 = i2[e2], o2.push(JSON.parse(t3.text.toConsole()));
                return o2;
              }.call(this));
            }, c;
          }(e.Object);
        }.call(this), function() {
          e.LineBreakInsertion = function() {
            function t2(t3) {
              var e2;
              this.composition = t3, this.document = this.composition.document, e2 = this.composition.getSelectedRange(), this.startPosition = e2[0], this.endPosition = e2[1], this.startLocation = this.document.locationFromPosition(this.startPosition), this.endLocation = this.document.locationFromPosition(this.endPosition), this.block = this.document.getBlockAtIndex(this.endLocation.index), this.breaksOnReturn = this.block.breaksOnReturn(), this.previousCharacter = this.block.text.getStringAtPosition(this.endLocation.offset - 1), this.nextCharacter = this.block.text.getStringAtPosition(this.endLocation.offset);
            }
            return t2.prototype.shouldInsertBlockBreak = function() {
              return this.block.hasAttributes() && this.block.isListItem() && !this.block.isEmpty() ? 0 !== this.startLocation.offset : this.breaksOnReturn && "\n" !== this.nextCharacter;
            }, t2.prototype.shouldBreakFormattedBlock = function() {
              return this.block.hasAttributes() && !this.block.isListItem() && (this.breaksOnReturn && "\n" === this.nextCharacter || "\n" === this.previousCharacter);
            }, t2.prototype.shouldDecreaseListLevel = function() {
              return this.block.hasAttributes() && this.block.isListItem() && this.block.isEmpty();
            }, t2.prototype.shouldPrependListItem = function() {
              return this.block.isListItem() && 0 === this.startLocation.offset && !this.block.isEmpty();
            }, t2.prototype.shouldRemoveLastBlockAttribute = function() {
              return this.block.hasAttributes() && !this.block.isListItem() && this.block.isEmpty();
            }, t2;
          }();
        }.call(this), function() {
          var t2, n, i, o, r, s, a, u, c, l, h = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              p.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, p = {}.hasOwnProperty;
          s = e.normalizeRange, c = e.rangesAreEqual, u = e.rangeIsCollapsed, a = e.objectsAreEqual, t2 = e.arrayStartsWith, l = e.summarizeArrayChange, i = e.getAllAttributeNames, o = e.getBlockConfig, r = e.getTextConfig, n = e.extend, e.Composition = function(p2) {
            function d() {
              this.document = new e.Document(), this.attachments = [], this.currentAttributes = {}, this.revision = 0;
            }
            var f;
            return h(d, p2), d.prototype.setDocument = function(t3) {
              var e2;
              return t3.isEqualTo(this.document) ? void 0 : (this.document = t3, this.refreshAttachments(), this.revision++, null != (e2 = this.delegate) && "function" == typeof e2.compositionDidChangeDocument ? e2.compositionDidChangeDocument(t3) : void 0);
            }, d.prototype.getSnapshot = function() {
              return { document: this.document, selectedRange: this.getSelectedRange() };
            }, d.prototype.loadSnapshot = function(t3) {
              var n2, i2, o2, r2;
              return n2 = t3.document, r2 = t3.selectedRange, null != (i2 = this.delegate) && "function" == typeof i2.compositionWillLoadSnapshot && i2.compositionWillLoadSnapshot(), this.setDocument(null != n2 ? n2 : new e.Document()), this.setSelection(null != r2 ? r2 : [0, 0]), null != (o2 = this.delegate) && "function" == typeof o2.compositionDidLoadSnapshot ? o2.compositionDidLoadSnapshot() : void 0;
            }, d.prototype.insertText = function(t3, e2) {
              var n2, i2, o2, r2;
              return r2 = (null != e2 ? e2 : { updatePosition: true }).updatePosition, i2 = this.getSelectedRange(), this.setDocument(this.document.insertTextAtRange(t3, i2)), o2 = i2[0], n2 = o2 + t3.getLength(), r2 && this.setSelection(n2), this.notifyDelegateOfInsertionAtRange([o2, n2]);
            }, d.prototype.insertBlock = function(t3) {
              var n2;
              return null == t3 && (t3 = new e.Block()), n2 = new e.Document([t3]), this.insertDocument(n2);
            }, d.prototype.insertDocument = function(t3) {
              var n2, i2, o2;
              return null == t3 && (t3 = new e.Document()), i2 = this.getSelectedRange(), this.setDocument(this.document.insertDocumentAtRange(t3, i2)), o2 = i2[0], n2 = o2 + t3.getLength(), this.setSelection(n2), this.notifyDelegateOfInsertionAtRange([o2, n2]);
            }, d.prototype.insertString = function(t3, n2) {
              var i2, o2;
              return i2 = this.getCurrentTextAttributes(), o2 = e.Text.textForStringWithAttributes(t3, i2), this.insertText(o2, n2);
            }, d.prototype.insertBlockBreak = function() {
              var t3, e2, n2;
              return e2 = this.getSelectedRange(), this.setDocument(this.document.insertBlockBreakAtRange(e2)), n2 = e2[0], t3 = n2 + 1, this.setSelection(t3), this.notifyDelegateOfInsertionAtRange([n2, t3]);
            }, d.prototype.insertLineBreak = function() {
              var t3, n2;
              return n2 = new e.LineBreakInsertion(this), n2.shouldDecreaseListLevel() ? (this.decreaseListLevel(), this.setSelection(n2.startPosition)) : n2.shouldPrependListItem() ? (t3 = new e.Document([n2.block.copyWithoutText()]), this.insertDocument(t3)) : n2.shouldInsertBlockBreak() ? this.insertBlockBreak() : n2.shouldRemoveLastBlockAttribute() ? this.removeLastBlockAttribute() : n2.shouldBreakFormattedBlock() ? this.breakFormattedBlock(n2) : this.insertString("\n");
            }, d.prototype.insertHTML = function(t3) {
              var n2, i2, o2, r2;
              return n2 = e.Document.fromHTML(t3), o2 = this.getSelectedRange(), this.setDocument(this.document.mergeDocumentAtRange(n2, o2)), r2 = o2[0], i2 = r2 + n2.getLength() - 1, this.setSelection(i2), this.notifyDelegateOfInsertionAtRange([r2, i2]);
            }, d.prototype.replaceHTML = function(t3) {
              var n2, i2, o2;
              return n2 = e.Document.fromHTML(t3).copyUsingObjectsFromDocument(this.document), i2 = this.getLocationRange({ strict: false }), o2 = this.document.rangeFromLocationRange(i2), this.setDocument(n2), this.setSelection(o2);
            }, d.prototype.insertFile = function(t3) {
              return this.insertFiles([t3]);
            }, d.prototype.insertFiles = function(t3) {
              var n2, i2, o2, r2, s2, a2;
              for (i2 = [], r2 = 0, s2 = t3.length; s2 > r2; r2++)
                o2 = t3[r2], (null != (a2 = this.delegate) ? a2.compositionShouldAcceptFile(o2) : void 0) && (n2 = e.Attachment.attachmentForFile(o2), i2.push(n2));
              return this.insertAttachments(i2);
            }, d.prototype.insertAttachment = function(t3) {
              return this.insertAttachments([t3]);
            }, d.prototype.insertAttachments = function(t3) {
              var n2, i2, o2, r2, s2, a2, u2, c2, l2;
              for (c2 = new e.Text(), r2 = 0, s2 = t3.length; s2 > r2; r2++)
                n2 = t3[r2], l2 = n2.getType(), a2 = null != (u2 = e.config.attachments[l2]) ? u2.presentation : void 0, o2 = this.getCurrentTextAttributes(), a2 && (o2.presentation = a2), i2 = e.Text.textForAttachmentWithAttributes(n2, o2), c2 = c2.appendText(i2);
              return this.insertText(c2);
            }, d.prototype.shouldManageDeletingInDirection = function(t3) {
              var e2;
              if (e2 = this.getLocationRange(), u(e2)) {
                if ("backward" === t3 && 0 === e2[0].offset)
                  return true;
                if (this.shouldManageMovingCursorInDirection(t3))
                  return true;
              } else if (e2[0].index !== e2[1].index)
                return true;
              return false;
            }, d.prototype.deleteInDirection = function(t3, e2) {
              var n2, i2, o2, r2, s2, a2, c2, l2;
              return r2 = (null != e2 ? e2 : {}).length, s2 = this.getLocationRange(), a2 = this.getSelectedRange(), c2 = u(a2), c2 ? o2 = "backward" === t3 && 0 === s2[0].offset : l2 = s2[0].index !== s2[1].index, o2 && this.canDecreaseBlockAttributeLevel() && (i2 = this.getBlock(), i2.isListItem() ? this.decreaseListLevel() : this.decreaseBlockAttributeLevel(), this.setSelection(a2[0]), i2.isEmpty()) ? false : (c2 && (a2 = this.getExpandedRangeInDirection(t3, { length: r2 }), "backward" === t3 && (n2 = this.getAttachmentAtRange(a2))), n2 ? (this.editAttachment(n2), false) : (this.setDocument(this.document.removeTextAtRange(a2)), this.setSelection(a2[0]), o2 || l2 ? false : void 0));
            }, d.prototype.moveTextFromRange = function(t3) {
              var e2;
              return e2 = this.getSelectedRange()[0], this.setDocument(this.document.moveTextFromRangeToPosition(t3, e2)), this.setSelection(e2);
            }, d.prototype.removeAttachment = function(t3) {
              var e2;
              return (e2 = this.document.getRangeOfAttachment(t3)) ? (this.stopEditingAttachment(), this.setDocument(this.document.removeTextAtRange(e2)), this.setSelection(e2[0])) : void 0;
            }, d.prototype.removeLastBlockAttribute = function() {
              var t3, e2, n2, i2;
              return n2 = this.getSelectedRange(), i2 = n2[0], e2 = n2[1], t3 = this.document.getBlockAtPosition(e2), this.removeCurrentAttribute(t3.getLastAttribute()), this.setSelection(i2);
            }, f = " ", d.prototype.insertPlaceholder = function() {
              return this.placeholderPosition = this.getPosition(), this.insertString(f);
            }, d.prototype.selectPlaceholder = function() {
              return null != this.placeholderPosition ? (this.setSelectedRange([this.placeholderPosition, this.placeholderPosition + f.length]), this.getSelectedRange()) : void 0;
            }, d.prototype.forgetPlaceholder = function() {
              return this.placeholderPosition = null;
            }, d.prototype.hasCurrentAttribute = function(t3) {
              var e2;
              return e2 = this.currentAttributes[t3], null != e2 && e2 !== false;
            }, d.prototype.toggleCurrentAttribute = function(t3) {
              var e2;
              return (e2 = !this.currentAttributes[t3]) ? this.setCurrentAttribute(t3, e2) : this.removeCurrentAttribute(t3);
            }, d.prototype.canSetCurrentAttribute = function(t3) {
              return o(t3) ? this.canSetCurrentBlockAttribute(t3) : this.canSetCurrentTextAttribute(t3);
            }, d.prototype.canSetCurrentTextAttribute = function() {
              var t3, e2, n2, i2, o2;
              if (e2 = this.getSelectedDocument()) {
                for (o2 = e2.getAttachments(), n2 = 0, i2 = o2.length; i2 > n2; n2++)
                  if (t3 = o2[n2], !t3.hasContent())
                    return false;
                return true;
              }
            }, d.prototype.canSetCurrentBlockAttribute = function() {
              var t3;
              if (t3 = this.getBlock())
                return !t3.isTerminalBlock();
            }, d.prototype.setCurrentAttribute = function(t3, e2) {
              return o(t3) ? this.setBlockAttribute(t3, e2) : (this.setTextAttribute(t3, e2), this.currentAttributes[t3] = e2, this.notifyDelegateOfCurrentAttributesChange());
            }, d.prototype.setTextAttribute = function(t3, n2) {
              var i2, o2, r2, s2;
              if (o2 = this.getSelectedRange())
                return r2 = o2[0], i2 = o2[1], r2 !== i2 ? this.setDocument(this.document.addAttributeAtRange(t3, n2, o2)) : "href" === t3 ? (s2 = e.Text.textForStringWithAttributes(n2, { href: n2 }), this.insertText(s2)) : void 0;
            }, d.prototype.setBlockAttribute = function(t3, e2) {
              var n2, i2;
              if (i2 = this.getSelectedRange())
                return this.canSetCurrentAttribute(t3) ? (n2 = this.getBlock(), this.setDocument(this.document.applyBlockAttributeAtRange(t3, e2, i2)), this.setSelection(i2)) : void 0;
            }, d.prototype.removeCurrentAttribute = function(t3) {
              return o(t3) ? (this.removeBlockAttribute(t3), this.updateCurrentAttributes()) : (this.removeTextAttribute(t3), delete this.currentAttributes[t3], this.notifyDelegateOfCurrentAttributesChange());
            }, d.prototype.removeTextAttribute = function(t3) {
              var e2;
              if (e2 = this.getSelectedRange())
                return this.setDocument(this.document.removeAttributeAtRange(t3, e2));
            }, d.prototype.removeBlockAttribute = function(t3) {
              var e2;
              if (e2 = this.getSelectedRange())
                return this.setDocument(this.document.removeAttributeAtRange(t3, e2));
            }, d.prototype.canDecreaseNestingLevel = function() {
              var t3;
              return (null != (t3 = this.getBlock()) ? t3.getNestingLevel() : void 0) > 0;
            }, d.prototype.canIncreaseNestingLevel = function() {
              var e2, n2, i2;
              if (e2 = this.getBlock())
                return (null != (i2 = o(e2.getLastNestableAttribute())) ? i2.listAttribute : 0) ? (n2 = this.getPreviousBlock()) ? t2(n2.getListItemAttributes(), e2.getListItemAttributes()) : void 0 : e2.getNestingLevel() > 0;
            }, d.prototype.decreaseNestingLevel = function() {
              var t3;
              if (t3 = this.getBlock())
                return this.setDocument(this.document.replaceBlock(t3, t3.decreaseNestingLevel()));
            }, d.prototype.increaseNestingLevel = function() {
              var t3;
              if (t3 = this.getBlock())
                return this.setDocument(this.document.replaceBlock(t3, t3.increaseNestingLevel()));
            }, d.prototype.canDecreaseBlockAttributeLevel = function() {
              var t3;
              return (null != (t3 = this.getBlock()) ? t3.getAttributeLevel() : void 0) > 0;
            }, d.prototype.decreaseBlockAttributeLevel = function() {
              var t3, e2;
              return (t3 = null != (e2 = this.getBlock()) ? e2.getLastAttribute() : void 0) ? this.removeCurrentAttribute(t3) : void 0;
            }, d.prototype.decreaseListLevel = function() {
              var t3, e2, n2, i2, o2, r2;
              for (r2 = this.getSelectedRange()[0], o2 = this.document.locationFromPosition(r2).index, n2 = o2, t3 = this.getBlock().getAttributeLevel(); (e2 = this.document.getBlockAtIndex(n2 + 1)) && e2.isListItem() && e2.getAttributeLevel() > t3; )
                n2++;
              return r2 = this.document.positionFromLocation({ index: o2, offset: 0 }), i2 = this.document.positionFromLocation({ index: n2, offset: 0 }), this.setDocument(this.document.removeLastListAttributeAtRange([r2, i2]));
            }, d.prototype.updateCurrentAttributes = function() {
              var t3, e2, n2, o2, r2, s2;
              if (s2 = this.getSelectedRange({ ignoreLock: true })) {
                for (e2 = this.document.getCommonAttributesAtRange(s2), r2 = i(), n2 = 0, o2 = r2.length; o2 > n2; n2++)
                  t3 = r2[n2], e2[t3] || this.canSetCurrentAttribute(t3) || (e2[t3] = false);
                if (!a(e2, this.currentAttributes))
                  return this.currentAttributes = e2, this.notifyDelegateOfCurrentAttributesChange();
              }
            }, d.prototype.getCurrentAttributes = function() {
              return n.call({}, this.currentAttributes);
            }, d.prototype.getCurrentTextAttributes = function() {
              var t3, e2, n2, i2;
              t3 = {}, n2 = this.currentAttributes;
              for (e2 in n2)
                i2 = n2[e2], i2 !== false && r(e2) && (t3[e2] = i2);
              return t3;
            }, d.prototype.freezeSelection = function() {
              return this.setCurrentAttribute("frozen", true);
            }, d.prototype.thawSelection = function() {
              return this.removeCurrentAttribute("frozen");
            }, d.prototype.hasFrozenSelection = function() {
              return this.hasCurrentAttribute("frozen");
            }, d.proxyMethod("getSelectionManager().getPointRange"), d.proxyMethod("getSelectionManager().setLocationRangeFromPointRange"), d.proxyMethod("getSelectionManager().createLocationRangeFromDOMRange"), d.proxyMethod("getSelectionManager().locationIsCursorTarget"), d.proxyMethod("getSelectionManager().selectionIsExpanded"), d.proxyMethod("delegate?.getSelectionManager"), d.prototype.setSelection = function(t3) {
              var e2, n2;
              return e2 = this.document.locationRangeFromRange(t3), null != (n2 = this.delegate) ? n2.compositionDidRequestChangingSelectionToLocationRange(e2) : void 0;
            }, d.prototype.getSelectedRange = function() {
              var t3;
              return (t3 = this.getLocationRange()) ? this.document.rangeFromLocationRange(t3) : void 0;
            }, d.prototype.setSelectedRange = function(t3) {
              var e2;
              return e2 = this.document.locationRangeFromRange(t3), this.getSelectionManager().setLocationRange(e2);
            }, d.prototype.getPosition = function() {
              var t3;
              return (t3 = this.getLocationRange()) ? this.document.positionFromLocation(t3[0]) : void 0;
            }, d.prototype.getLocationRange = function(t3) {
              var e2, n2;
              return null != (e2 = null != (n2 = this.targetLocationRange) ? n2 : this.getSelectionManager().getLocationRange(t3)) ? e2 : s({ index: 0, offset: 0 });
            }, d.prototype.withTargetLocationRange = function(t3, e2) {
              var n2;
              this.targetLocationRange = t3;
              try {
                n2 = e2();
              } finally {
                this.targetLocationRange = null;
              }
              return n2;
            }, d.prototype.withTargetRange = function(t3, e2) {
              var n2;
              return n2 = this.document.locationRangeFromRange(t3), this.withTargetLocationRange(n2, e2);
            }, d.prototype.withTargetDOMRange = function(t3, e2) {
              var n2;
              return n2 = this.createLocationRangeFromDOMRange(t3, { strict: false }), this.withTargetLocationRange(n2, e2);
            }, d.prototype.getExpandedRangeInDirection = function(t3, e2) {
              var n2, i2, o2, r2;
              return i2 = (null != e2 ? e2 : {}).length, o2 = this.getSelectedRange(), r2 = o2[0], n2 = o2[1], "backward" === t3 ? i2 ? r2 -= i2 : r2 = this.translateUTF16PositionFromOffset(r2, -1) : i2 ? n2 += i2 : n2 = this.translateUTF16PositionFromOffset(n2, 1), s([r2, n2]);
            }, d.prototype.shouldManageMovingCursorInDirection = function(t3) {
              var e2;
              return this.editingAttachment ? true : (e2 = this.getExpandedRangeInDirection(t3), null != this.getAttachmentAtRange(e2));
            }, d.prototype.moveCursorInDirection = function(t3) {
              var e2, n2, i2, o2;
              return this.editingAttachment ? i2 = this.document.getRangeOfAttachment(this.editingAttachment) : (o2 = this.getSelectedRange(), i2 = this.getExpandedRangeInDirection(t3), n2 = !c(o2, i2)), this.setSelectedRange("backward" === t3 ? i2[0] : i2[1]), n2 && (e2 = this.getAttachmentAtRange(i2)) ? this.editAttachment(e2) : void 0;
            }, d.prototype.expandSelectionInDirection = function(t3, e2) {
              var n2, i2;
              return n2 = (null != e2 ? e2 : {}).length, i2 = this.getExpandedRangeInDirection(t3, { length: n2 }), this.setSelectedRange(i2);
            }, d.prototype.expandSelectionForEditing = function() {
              return this.hasCurrentAttribute("href") ? this.expandSelectionAroundCommonAttribute("href") : void 0;
            }, d.prototype.expandSelectionAroundCommonAttribute = function(t3) {
              var e2, n2;
              return e2 = this.getPosition(), n2 = this.document.getRangeOfCommonAttributeAtPosition(t3, e2), this.setSelectedRange(n2);
            }, d.prototype.selectionContainsAttachments = function() {
              var t3;
              return (null != (t3 = this.getSelectedAttachments()) ? t3.length : void 0) > 0;
            }, d.prototype.selectionIsInCursorTarget = function() {
              return this.editingAttachment || this.positionIsCursorTarget(this.getPosition());
            }, d.prototype.positionIsCursorTarget = function(t3) {
              var e2;
              return (e2 = this.document.locationFromPosition(t3)) ? this.locationIsCursorTarget(e2) : void 0;
            }, d.prototype.positionIsBlockBreak = function(t3) {
              var e2;
              return null != (e2 = this.document.getPieceAtPosition(t3)) ? e2.isBlockBreak() : void 0;
            }, d.prototype.getSelectedDocument = function() {
              var t3;
              return (t3 = this.getSelectedRange()) ? this.document.getDocumentAtRange(t3) : void 0;
            }, d.prototype.getSelectedAttachments = function() {
              var t3;
              return null != (t3 = this.getSelectedDocument()) ? t3.getAttachments() : void 0;
            }, d.prototype.getAttachments = function() {
              return this.attachments.slice(0);
            }, d.prototype.refreshAttachments = function() {
              var t3, e2, n2, i2, o2, r2, s2, a2, u2, c2, h2, p3;
              for (n2 = this.document.getAttachments(), a2 = l(this.attachments, n2), t3 = a2.added, h2 = a2.removed, this.attachments = n2, i2 = 0, r2 = h2.length; r2 > i2; i2++)
                e2 = h2[i2], e2.delegate = null, null != (u2 = this.delegate) && "function" == typeof u2.compositionDidRemoveAttachment && u2.compositionDidRemoveAttachment(e2);
              for (p3 = [], o2 = 0, s2 = t3.length; s2 > o2; o2++)
                e2 = t3[o2], e2.delegate = this, p3.push(null != (c2 = this.delegate) && "function" == typeof c2.compositionDidAddAttachment ? c2.compositionDidAddAttachment(e2) : void 0);
              return p3;
            }, d.prototype.attachmentDidChangeAttributes = function(t3) {
              var e2;
              return this.revision++, null != (e2 = this.delegate) && "function" == typeof e2.compositionDidEditAttachment ? e2.compositionDidEditAttachment(t3) : void 0;
            }, d.prototype.attachmentDidChangePreviewURL = function(t3) {
              var e2;
              return this.revision++, null != (e2 = this.delegate) && "function" == typeof e2.compositionDidChangeAttachmentPreviewURL ? e2.compositionDidChangeAttachmentPreviewURL(t3) : void 0;
            }, d.prototype.editAttachment = function(t3, e2) {
              var n2;
              if (t3 !== this.editingAttachment)
                return this.stopEditingAttachment(), this.editingAttachment = t3, null != (n2 = this.delegate) && "function" == typeof n2.compositionDidStartEditingAttachment ? n2.compositionDidStartEditingAttachment(this.editingAttachment, e2) : void 0;
            }, d.prototype.stopEditingAttachment = function() {
              var t3;
              if (this.editingAttachment)
                return null != (t3 = this.delegate) && "function" == typeof t3.compositionDidStopEditingAttachment && t3.compositionDidStopEditingAttachment(this.editingAttachment), this.editingAttachment = null;
            }, d.prototype.updateAttributesForAttachment = function(t3, e2) {
              return this.setDocument(this.document.updateAttributesForAttachment(t3, e2));
            }, d.prototype.removeAttributeForAttachment = function(t3, e2) {
              return this.setDocument(this.document.removeAttributeForAttachment(t3, e2));
            }, d.prototype.breakFormattedBlock = function(t3) {
              var n2, i2, o2, r2, s2;
              return i2 = t3.document, n2 = t3.block, r2 = t3.startPosition, s2 = [r2 - 1, r2], n2.getBlockBreakPosition() === t3.startLocation.offset ? (n2.breaksOnReturn() && "\n" === t3.nextCharacter ? r2 += 1 : i2 = i2.removeTextAtRange(s2), s2 = [r2, r2]) : "\n" === t3.nextCharacter ? "\n" === t3.previousCharacter ? s2 = [r2 - 1, r2 + 1] : (s2 = [r2, r2 + 1], r2 += 1) : t3.startLocation.offset - 1 !== 0 && (r2 += 1), o2 = new e.Document([n2.removeLastAttribute().copyWithoutText()]), this.setDocument(i2.insertDocumentAtRange(o2, s2)), this.setSelection(r2);
            }, d.prototype.getPreviousBlock = function() {
              var t3, e2;
              return (e2 = this.getLocationRange()) && (t3 = e2[0].index, t3 > 0) ? this.document.getBlockAtIndex(t3 - 1) : void 0;
            }, d.prototype.getBlock = function() {
              var t3;
              return (t3 = this.getLocationRange()) ? this.document.getBlockAtIndex(t3[0].index) : void 0;
            }, d.prototype.getAttachmentAtRange = function(t3) {
              var n2;
              return n2 = this.document.getDocumentAtRange(t3), n2.toString() === e.OBJECT_REPLACEMENT_CHARACTER + "\n" ? n2.getAttachments()[0] : void 0;
            }, d.prototype.notifyDelegateOfCurrentAttributesChange = function() {
              var t3;
              return null != (t3 = this.delegate) && "function" == typeof t3.compositionDidChangeCurrentAttributes ? t3.compositionDidChangeCurrentAttributes(this.currentAttributes) : void 0;
            }, d.prototype.notifyDelegateOfInsertionAtRange = function(t3) {
              var e2;
              return null != (e2 = this.delegate) && "function" == typeof e2.compositionDidPerformInsertionAtRange ? e2.compositionDidPerformInsertionAtRange(t3) : void 0;
            }, d.prototype.translateUTF16PositionFromOffset = function(t3, e2) {
              var n2, i2;
              return i2 = this.document.toUTF16String(), n2 = i2.offsetFromUCS2Offset(t3), i2.offsetToUCS2Offset(n2 + e2);
            }, d;
          }(e.BasicObject);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.UndoManager = function(e2) {
            function n2(t3) {
              this.composition = t3, this.undoEntries = [], this.redoEntries = [];
            }
            var i;
            return t2(n2, e2), n2.prototype.recordUndoEntry = function(t3, e3) {
              var n3, o, r, s, a;
              return s = null != e3 ? e3 : {}, o = s.context, n3 = s.consolidatable, r = this.undoEntries.slice(-1)[0], n3 && i(r, t3, o) ? void 0 : (a = this.createEntry({ description: t3, context: o }), this.undoEntries.push(a), this.redoEntries = []);
            }, n2.prototype.undo = function() {
              var t3, e3;
              return (e3 = this.undoEntries.pop()) ? (t3 = this.createEntry(e3), this.redoEntries.push(t3), this.composition.loadSnapshot(e3.snapshot)) : void 0;
            }, n2.prototype.redo = function() {
              var t3, e3;
              return (t3 = this.redoEntries.pop()) ? (e3 = this.createEntry(t3), this.undoEntries.push(e3), this.composition.loadSnapshot(t3.snapshot)) : void 0;
            }, n2.prototype.canUndo = function() {
              return this.undoEntries.length > 0;
            }, n2.prototype.canRedo = function() {
              return this.redoEntries.length > 0;
            }, n2.prototype.createEntry = function(t3) {
              var e3, n3, i2;
              return i2 = null != t3 ? t3 : {}, n3 = i2.description, e3 = i2.context, { description: null != n3 ? n3.toString() : void 0, context: JSON.stringify(e3), snapshot: this.composition.getSnapshot() };
            }, i = function(t3, e3, n3) {
              return (null != t3 ? t3.description : void 0) === (null != e3 ? e3.toString() : void 0) && (null != t3 ? t3.context : void 0) === JSON.stringify(n3);
            }, n2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2;
          e.attachmentGalleryFilter = function(e2) {
            var n;
            return n = new t2(e2), n.perform(), n.getSnapshot();
          }, t2 = function() {
            function t3(t4) {
              this.document = t4.document, this.selectedRange = t4.selectedRange;
            }
            var e2, n, i;
            return e2 = "attachmentGallery", n = "presentation", i = "gallery", t3.prototype.perform = function() {
              return this.removeBlockAttribute(), this.applyBlockAttribute();
            }, t3.prototype.getSnapshot = function() {
              return { document: this.document, selectedRange: this.selectedRange };
            }, t3.prototype.removeBlockAttribute = function() {
              var t4, n2, i2, o, r;
              for (o = this.findRangesOfBlocks(), r = [], t4 = 0, n2 = o.length; n2 > t4; t4++)
                i2 = o[t4], r.push(this.document = this.document.removeAttributeAtRange(e2, i2));
              return r;
            }, t3.prototype.applyBlockAttribute = function() {
              var t4, n2, i2, o, r, s;
              for (i2 = 0, r = this.findRangesOfPieces(), s = [], t4 = 0, n2 = r.length; n2 > t4; t4++)
                o = r[t4], o[1] - o[0] > 1 && (o[0] += i2, o[1] += i2, "\n" !== this.document.getCharacterAtPosition(o[1]) && (this.document = this.document.insertBlockBreakAtRange(o[1]), o[1] < this.selectedRange[1] && this.moveSelectedRangeForward(), o[1]++, i2++), 0 !== o[0] && "\n" !== this.document.getCharacterAtPosition(o[0] - 1) && (this.document = this.document.insertBlockBreakAtRange(o[0]), o[0] < this.selectedRange[0] && this.moveSelectedRangeForward(), o[0]++, i2++), s.push(this.document = this.document.applyBlockAttributeAtRange(e2, true, o)));
              return s;
            }, t3.prototype.findRangesOfBlocks = function() {
              return this.document.findRangesForBlockAttribute(e2);
            }, t3.prototype.findRangesOfPieces = function() {
              return this.document.findRangesForTextAttribute(n, { withValue: i });
            }, t3.prototype.moveSelectedRangeForward = function() {
              return this.selectedRange[0] += 1, this.selectedRange[1] += 1;
            }, t3;
          }();
        }.call(this), function() {
          var t2 = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          };
          e.Editor = function() {
            function n(n2, o, r) {
              this.composition = n2, this.selectionManager = o, this.element = r, this.insertFiles = t2(this.insertFiles, this), this.undoManager = new e.UndoManager(this.composition), this.filters = i.slice(0);
            }
            var i;
            return i = [e.attachmentGalleryFilter], n.prototype.loadDocument = function(t3) {
              return this.loadSnapshot({ document: t3, selectedRange: [0, 0] });
            }, n.prototype.loadHTML = function(t3) {
              return null == t3 && (t3 = ""), this.loadDocument(e.Document.fromHTML(t3, { referenceElement: this.element }));
            }, n.prototype.loadJSON = function(t3) {
              var n2, i2;
              return n2 = t3.document, i2 = t3.selectedRange, n2 = e.Document.fromJSON(n2), this.loadSnapshot({ document: n2, selectedRange: i2 });
            }, n.prototype.loadSnapshot = function(t3) {
              return this.undoManager = new e.UndoManager(this.composition), this.composition.loadSnapshot(t3);
            }, n.prototype.getDocument = function() {
              return this.composition.document;
            }, n.prototype.getSelectedDocument = function() {
              return this.composition.getSelectedDocument();
            }, n.prototype.getSnapshot = function() {
              return this.composition.getSnapshot();
            }, n.prototype.toJSON = function() {
              return this.getSnapshot();
            }, n.prototype.deleteInDirection = function(t3) {
              return this.composition.deleteInDirection(t3);
            }, n.prototype.insertAttachment = function(t3) {
              return this.composition.insertAttachment(t3);
            }, n.prototype.insertAttachments = function(t3) {
              return this.composition.insertAttachments(t3);
            }, n.prototype.insertDocument = function(t3) {
              return this.composition.insertDocument(t3);
            }, n.prototype.insertFile = function(t3) {
              return this.composition.insertFile(t3);
            }, n.prototype.insertFiles = function(t3) {
              return this.composition.insertFiles(t3);
            }, n.prototype.insertHTML = function(t3) {
              return this.composition.insertHTML(t3);
            }, n.prototype.insertString = function(t3) {
              return this.composition.insertString(t3);
            }, n.prototype.insertText = function(t3) {
              return this.composition.insertText(t3);
            }, n.prototype.insertLineBreak = function() {
              return this.composition.insertLineBreak();
            }, n.prototype.getSelectedRange = function() {
              return this.composition.getSelectedRange();
            }, n.prototype.getPosition = function() {
              return this.composition.getPosition();
            }, n.prototype.getClientRectAtPosition = function(t3) {
              var e2;
              return e2 = this.getDocument().locationRangeFromRange([t3, t3 + 1]), this.selectionManager.getClientRectAtLocationRange(e2);
            }, n.prototype.expandSelectionInDirection = function(t3) {
              return this.composition.expandSelectionInDirection(t3);
            }, n.prototype.moveCursorInDirection = function(t3) {
              return this.composition.moveCursorInDirection(t3);
            }, n.prototype.setSelectedRange = function(t3) {
              return this.composition.setSelectedRange(t3);
            }, n.prototype.activateAttribute = function(t3, e2) {
              return null == e2 && (e2 = true), this.composition.setCurrentAttribute(t3, e2);
            }, n.prototype.attributeIsActive = function(t3) {
              return this.composition.hasCurrentAttribute(t3);
            }, n.prototype.canActivateAttribute = function(t3) {
              return this.composition.canSetCurrentAttribute(t3);
            }, n.prototype.deactivateAttribute = function(t3) {
              return this.composition.removeCurrentAttribute(t3);
            }, n.prototype.canDecreaseNestingLevel = function() {
              return this.composition.canDecreaseNestingLevel();
            }, n.prototype.canIncreaseNestingLevel = function() {
              return this.composition.canIncreaseNestingLevel();
            }, n.prototype.decreaseNestingLevel = function() {
              return this.canDecreaseNestingLevel() ? this.composition.decreaseNestingLevel() : void 0;
            }, n.prototype.increaseNestingLevel = function() {
              return this.canIncreaseNestingLevel() ? this.composition.increaseNestingLevel() : void 0;
            }, n.prototype.canRedo = function() {
              return this.undoManager.canRedo();
            }, n.prototype.canUndo = function() {
              return this.undoManager.canUndo();
            }, n.prototype.recordUndoEntry = function(t3, e2) {
              var n2, i2, o;
              return o = null != e2 ? e2 : {}, i2 = o.context, n2 = o.consolidatable, this.undoManager.recordUndoEntry(t3, { context: i2, consolidatable: n2 });
            }, n.prototype.redo = function() {
              return this.canRedo() ? this.undoManager.redo() : void 0;
            }, n.prototype.undo = function() {
              return this.canUndo() ? this.undoManager.undo() : void 0;
            }, n;
          }();
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.ManagedAttachment = function(e2) {
            function n2(t3, e3) {
              var n3;
              this.attachmentManager = t3, this.attachment = e3, n3 = this.attachment, this.id = n3.id, this.file = n3.file;
            }
            return t2(n2, e2), n2.prototype.remove = function() {
              return this.attachmentManager.requestRemovalOfAttachment(this.attachment);
            }, n2.proxyMethod("attachment.getAttribute"), n2.proxyMethod("attachment.hasAttribute"), n2.proxyMethod("attachment.setAttribute"), n2.proxyMethod("attachment.getAttributes"), n2.proxyMethod("attachment.setAttributes"), n2.proxyMethod("attachment.isPending"), n2.proxyMethod("attachment.isPreviewable"), n2.proxyMethod("attachment.getURL"), n2.proxyMethod("attachment.getHref"), n2.proxyMethod("attachment.getFilename"), n2.proxyMethod("attachment.getFilesize"), n2.proxyMethod("attachment.getFormattedFilesize"), n2.proxyMethod("attachment.getExtension"), n2.proxyMethod("attachment.getContentType"), n2.proxyMethod("attachment.getFile"), n2.proxyMethod("attachment.setFile"), n2.proxyMethod("attachment.releaseFile"), n2.proxyMethod("attachment.getUploadProgress"), n2.proxyMethod("attachment.setUploadProgress"), n2;
          }(e.BasicObject);
        }.call(this), function() {
          var t2 = function(t3, e2) {
            function i() {
              this.constructor = t3;
            }
            for (var o in e2)
              n.call(e2, o) && (t3[o] = e2[o]);
            return i.prototype = e2.prototype, t3.prototype = new i(), t3.__super__ = e2.prototype, t3;
          }, n = {}.hasOwnProperty;
          e.AttachmentManager = function(n2) {
            function i(t3) {
              var e2, n3, i2;
              for (null == t3 && (t3 = []), this.managedAttachments = {}, n3 = 0, i2 = t3.length; i2 > n3; n3++)
                e2 = t3[n3], this.manageAttachment(e2);
            }
            return t2(i, n2), i.prototype.getAttachments = function() {
              var t3, e2, n3, i2;
              n3 = this.managedAttachments, i2 = [];
              for (e2 in n3)
                t3 = n3[e2], i2.push(t3);
              return i2;
            }, i.prototype.manageAttachment = function(t3) {
              var n3, i2;
              return null != (n3 = this.managedAttachments)[i2 = t3.id] ? n3[i2] : n3[i2] = new e.ManagedAttachment(this, t3);
            }, i.prototype.attachmentIsManaged = function(t3) {
              return t3.id in this.managedAttachments;
            }, i.prototype.requestRemovalOfAttachment = function(t3) {
              var e2;
              return this.attachmentIsManaged(t3) && null != (e2 = this.delegate) && "function" == typeof e2.attachmentManagerDidRequestRemovalOfAttachment ? e2.attachmentManagerDidRequestRemovalOfAttachment(t3) : void 0;
            }, i.prototype.unmanageAttachment = function(t3) {
              var e2;
              return e2 = this.managedAttachments[t3.id], delete this.managedAttachments[t3.id], e2;
            }, i;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o, r, s, a, u, c, l, h;
          t2 = e.elementContainsNode, n = e.findChildIndexOfNode, r = e.nodeIsBlockStart, s = e.nodeIsBlockStartComment, o = e.nodeIsBlockContainer, a = e.nodeIsCursorTarget, u = e.nodeIsEmptyTextNode, c = e.nodeIsTextNode, i = e.nodeIsAttachmentElement, l = e.tagName, h = e.walkTree, e.LocationMapper = function() {
            function e2(t3) {
              this.element = t3;
            }
            var p, d, f, g;
            return e2.prototype.findLocationFromContainerAndOffset = function(e3, i2, o2) {
              var s2, u2, l2, p2, g2, m, v;
              for (m = (null != o2 ? o2 : { strict: true }).strict, u2 = 0, l2 = false, p2 = { index: 0, offset: 0 }, (s2 = this.findAttachmentElementParentForNode(e3)) && (e3 = s2.parentNode, i2 = n(s2)), v = h(this.element, { usingFilter: f }); v.nextNode(); ) {
                if (g2 = v.currentNode, g2 === e3 && c(e3)) {
                  a(g2) || (p2.offset += i2);
                  break;
                }
                if (g2.parentNode === e3) {
                  if (u2++ === i2)
                    break;
                } else if (!t2(e3, g2) && u2 > 0)
                  break;
                r(g2, { strict: m }) ? (l2 && p2.index++, p2.offset = 0, l2 = true) : p2.offset += d(g2);
              }
              return p2;
            }, e2.prototype.findContainerAndOffsetFromLocation = function(t3) {
              var e3, i2, s2, u2, l2;
              if (0 === t3.index && 0 === t3.offset) {
                for (e3 = this.element, u2 = 0; e3.firstChild; )
                  if (e3 = e3.firstChild, o(e3)) {
                    u2 = 1;
                    break;
                  }
                return [e3, u2];
              }
              if (l2 = this.findNodeAndOffsetFromLocation(t3), i2 = l2[0], s2 = l2[1], i2) {
                if (c(i2))
                  0 === d(i2) ? (e3 = i2.parentNode.parentNode, u2 = n(i2.parentNode), a(i2, { name: "right" }) && u2++) : (e3 = i2, u2 = t3.offset - s2);
                else {
                  if (e3 = i2.parentNode, !r(i2.previousSibling) && !o(e3))
                    for (; i2 === e3.lastChild && (i2 = e3, e3 = e3.parentNode, !o(e3)); )
                      ;
                  u2 = n(i2), 0 !== t3.offset && u2++;
                }
                return [e3, u2];
              }
            }, e2.prototype.findNodeAndOffsetFromLocation = function(t3) {
              var e3, n2, i2, o2, r2, s2, u2, l2;
              for (u2 = 0, l2 = this.getSignificantNodesForIndex(t3.index), n2 = 0, i2 = l2.length; i2 > n2; n2++) {
                if (e3 = l2[n2], o2 = d(e3), t3.offset <= u2 + o2)
                  if (c(e3)) {
                    if (r2 = e3, s2 = u2, t3.offset === s2 && a(r2))
                      break;
                  } else
                    r2 || (r2 = e3, s2 = u2);
                if (u2 += o2, u2 > t3.offset)
                  break;
              }
              return [r2, s2];
            }, e2.prototype.findAttachmentElementParentForNode = function(t3) {
              for (; t3 && t3 !== this.element; ) {
                if (i(t3))
                  return t3;
                t3 = t3.parentNode;
              }
            }, e2.prototype.getSignificantNodesForIndex = function(t3) {
              var e3, n2, i2, o2, r2;
              for (i2 = [], r2 = h(this.element, { usingFilter: p }), o2 = false; r2.nextNode(); )
                if (n2 = r2.currentNode, s(n2)) {
                  if ("undefined" != typeof e3 && null !== e3 ? e3++ : e3 = 0, e3 === t3)
                    o2 = true;
                  else if (o2)
                    break;
                } else
                  o2 && i2.push(n2);
              return i2;
            }, d = function(t3) {
              var e3;
              return t3.nodeType === Node.TEXT_NODE ? a(t3) ? 0 : (e3 = t3.textContent, e3.length) : "br" === l(t3) || i(t3) ? 1 : 0;
            }, p = function(t3) {
              return g(t3) === NodeFilter.FILTER_ACCEPT ? f(t3) : NodeFilter.FILTER_REJECT;
            }, g = function(t3) {
              return u(t3) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
            }, f = function(t3) {
              return i(t3.parentNode) ? NodeFilter.FILTER_REJECT : NodeFilter.FILTER_ACCEPT;
            }, e2;
          }();
        }.call(this), function() {
          var t2, n, i = [].slice;
          t2 = e.getDOMRange, n = e.setDOMRange, e.PointMapper = function() {
            function e2() {
            }
            return e2.prototype.createDOMRangeFromPoint = function(e3) {
              var i2, o, r, s, a, u, c, l;
              if (c = e3.x, l = e3.y, document.caretPositionFromPoint)
                return a = document.caretPositionFromPoint(c, l), r = a.offsetNode, o = a.offset, i2 = document.createRange(), i2.setStart(r, o), i2;
              if (document.caretRangeFromPoint)
                return document.caretRangeFromPoint(c, l);
              if (document.body.createTextRange) {
                s = t2();
                try {
                  u = document.body.createTextRange(), u.moveToPoint(c, l), u.select();
                } catch (h) {
                }
                return i2 = t2(), n(s), i2;
              }
            }, e2.prototype.getClientRectsForDOMRange = function(t3) {
              var e3, n2, o;
              return n2 = i.call(t3.getClientRects()), o = n2[0], e3 = n2[n2.length - 1], [o, e3];
            }, e2;
          }();
        }.call(this), function() {
          var t2, n = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, i = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              o.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, o = {}.hasOwnProperty, r = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          t2 = e.getDOMRange, e.SelectionChangeObserver = function(e2) {
            function o2() {
              this.run = n(this.run, this), this.update = n(this.update, this), this.selectionManagers = [];
            }
            var s;
            return i(o2, e2), o2.prototype.start = function() {
              return this.started ? void 0 : (this.started = true, "onselectionchange" in document ? document.addEventListener("selectionchange", this.update, true) : this.run());
            }, o2.prototype.stop = function() {
              return this.started ? (this.started = false, document.removeEventListener("selectionchange", this.update, true)) : void 0;
            }, o2.prototype.registerSelectionManager = function(t3) {
              return r.call(this.selectionManagers, t3) < 0 ? (this.selectionManagers.push(t3), this.start()) : void 0;
            }, o2.prototype.unregisterSelectionManager = function(t3) {
              var e3;
              return this.selectionManagers = function() {
                var n2, i2, o3, r2;
                for (o3 = this.selectionManagers, r2 = [], n2 = 0, i2 = o3.length; i2 > n2; n2++)
                  e3 = o3[n2], e3 !== t3 && r2.push(e3);
                return r2;
              }.call(this), 0 === this.selectionManagers.length ? this.stop() : void 0;
            }, o2.prototype.notifySelectionManagersOfSelectionChange = function() {
              var t3, e3, n2, i2, o3;
              for (n2 = this.selectionManagers, i2 = [], t3 = 0, e3 = n2.length; e3 > t3; t3++)
                o3 = n2[t3], i2.push(o3.selectionDidChange());
              return i2;
            }, o2.prototype.update = function() {
              var e3;
              return e3 = t2(), s(e3, this.domRange) ? void 0 : (this.domRange = e3, this.notifySelectionManagersOfSelectionChange());
            }, o2.prototype.reset = function() {
              return this.domRange = null, this.update();
            }, o2.prototype.run = function() {
              return this.started ? (this.update(), requestAnimationFrame(this.run)) : void 0;
            }, s = function(t3, e3) {
              return (null != t3 ? t3.startContainer : void 0) === (null != e3 ? e3.startContainer : void 0) && (null != t3 ? t3.startOffset : void 0) === (null != e3 ? e3.startOffset : void 0) && (null != t3 ? t3.endContainer : void 0) === (null != e3 ? e3.endContainer : void 0) && (null != t3 ? t3.endOffset : void 0) === (null != e3 ? e3.endOffset : void 0);
            }, o2;
          }(e.BasicObject), null == e.selectionChangeObserver && (e.selectionChangeObserver = new e.SelectionChangeObserver());
        }.call(this), function() {
          var t2, n, i, o, r, s, a, u, c, l, h = function(t3, e2) {
            return function() {
              return t3.apply(e2, arguments);
            };
          }, p = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              d.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, d = {}.hasOwnProperty;
          i = e.getDOMSelection, n = e.getDOMRange, l = e.setDOMRange, t2 = e.elementContainsNode, s = e.nodeIsCursorTarget, r = e.innerElementIsActive, o = e.handleEvent, a = e.normalizeRange, u = e.rangeIsCollapsed, c = e.rangesAreEqual, e.SelectionManager = function(d2) {
            function f(t3) {
              this.element = t3, this.selectionDidChange = h(this.selectionDidChange, this), this.didMouseDown = h(this.didMouseDown, this), this.locationMapper = new e.LocationMapper(this.element), this.pointMapper = new e.PointMapper(), this.lockCount = 0, o("mousedown", { onElement: this.element, withCallback: this.didMouseDown });
            }
            return p(f, d2), f.prototype.getLocationRange = function(t3) {
              var e2, i2;
              return null == t3 && (t3 = {}), e2 = t3.strict === false ? this.createLocationRangeFromDOMRange(n(), { strict: false }) : t3.ignoreLock ? this.currentLocationRange : null != (i2 = this.lockedLocationRange) ? i2 : this.currentLocationRange;
            }, f.prototype.setLocationRange = function(t3) {
              var e2;
              if (!this.lockedLocationRange)
                return t3 = a(t3), (e2 = this.createDOMRangeFromLocationRange(t3)) ? (l(e2), this.updateCurrentLocationRange(t3)) : void 0;
            }, f.prototype.setLocationRangeFromPointRange = function(t3) {
              var e2, n2;
              return t3 = a(t3), n2 = this.getLocationAtPoint(t3[0]), e2 = this.getLocationAtPoint(t3[1]), this.setLocationRange([n2, e2]);
            }, f.prototype.getClientRectAtLocationRange = function(t3) {
              var e2;
              return (e2 = this.createDOMRangeFromLocationRange(t3)) ? this.getClientRectsForDOMRange(e2)[1] : void 0;
            }, f.prototype.locationIsCursorTarget = function(t3) {
              var e2, n2, i2;
              return i2 = this.findNodeAndOffsetFromLocation(t3), e2 = i2[0], n2 = i2[1], s(e2);
            }, f.prototype.lock = function() {
              return 0 === this.lockCount++ ? (this.updateCurrentLocationRange(), this.lockedLocationRange = this.getLocationRange()) : void 0;
            }, f.prototype.unlock = function() {
              var t3;
              return 0 === --this.lockCount && (t3 = this.lockedLocationRange, this.lockedLocationRange = null, null != t3) ? this.setLocationRange(t3) : void 0;
            }, f.prototype.clearSelection = function() {
              var t3;
              return null != (t3 = i()) ? t3.removeAllRanges() : void 0;
            }, f.prototype.selectionIsCollapsed = function() {
              var t3;
              return (null != (t3 = n()) ? t3.collapsed : void 0) === true;
            }, f.prototype.selectionIsExpanded = function() {
              return !this.selectionIsCollapsed();
            }, f.prototype.createLocationRangeFromDOMRange = function(t3, e2) {
              var n2, i2;
              if (null != t3 && this.domRangeWithinElement(t3) && (i2 = this.findLocationFromContainerAndOffset(t3.startContainer, t3.startOffset, e2)))
                return t3.collapsed || (n2 = this.findLocationFromContainerAndOffset(t3.endContainer, t3.endOffset, e2)), a([i2, n2]);
            }, f.proxyMethod("locationMapper.findLocationFromContainerAndOffset"), f.proxyMethod("locationMapper.findContainerAndOffsetFromLocation"), f.proxyMethod("locationMapper.findNodeAndOffsetFromLocation"), f.proxyMethod("pointMapper.createDOMRangeFromPoint"), f.proxyMethod("pointMapper.getClientRectsForDOMRange"), f.prototype.didMouseDown = function() {
              return this.pauseTemporarily();
            }, f.prototype.pauseTemporarily = function() {
              var e2, n2, i2, r2;
              return this.paused = true, n2 = function(e3) {
                return function() {
                  var n3, o2, s2;
                  for (e3.paused = false, clearTimeout(r2), o2 = 0, s2 = i2.length; s2 > o2; o2++)
                    n3 = i2[o2], n3.destroy();
                  return t2(document, e3.element) ? e3.selectionDidChange() : void 0;
                };
              }(this), r2 = setTimeout(n2, 200), i2 = function() {
                var t3, i3, r3, s2;
                for (r3 = ["mousemove", "keydown"], s2 = [], t3 = 0, i3 = r3.length; i3 > t3; t3++)
                  e2 = r3[t3], s2.push(o(e2, { onElement: document, withCallback: n2 }));
                return s2;
              }();
            }, f.prototype.selectionDidChange = function() {
              return this.paused || r(this.element) ? void 0 : this.updateCurrentLocationRange();
            }, f.prototype.updateCurrentLocationRange = function(t3) {
              var e2;
              return (null != t3 ? t3 : t3 = this.createLocationRangeFromDOMRange(n())) && !c(t3, this.currentLocationRange) ? (this.currentLocationRange = t3, null != (e2 = this.delegate) && "function" == typeof e2.locationRangeDidChange ? e2.locationRangeDidChange(this.currentLocationRange.slice(0)) : void 0) : void 0;
            }, f.prototype.createDOMRangeFromLocationRange = function(t3) {
              var e2, n2, i2, o2;
              return i2 = this.findContainerAndOffsetFromLocation(t3[0]), n2 = u(t3) ? i2 : null != (o2 = this.findContainerAndOffsetFromLocation(t3[1])) ? o2 : i2, null != i2 && null != n2 ? (e2 = document.createRange(), e2.setStart.apply(e2, i2), e2.setEnd.apply(e2, n2), e2) : void 0;
            }, f.prototype.getLocationAtPoint = function(t3) {
              var e2, n2;
              return (e2 = this.createDOMRangeFromPoint(t3)) && null != (n2 = this.createLocationRangeFromDOMRange(e2)) ? n2[0] : void 0;
            }, f.prototype.domRangeWithinElement = function(e2) {
              return e2.collapsed ? t2(this.element, e2.startContainer) : t2(this.element, e2.startContainer) && t2(this.element, e2.endContainer);
            }, f;
          }(e.BasicObject);
        }.call(this), function() {
          var t2, n, i, o, r = function(t3, e2) {
            function n2() {
              this.constructor = t3;
            }
            for (var i2 in e2)
              s.call(e2, i2) && (t3[i2] = e2[i2]);
            return n2.prototype = e2.prototype, t3.prototype = new n2(), t3.__super__ = e2.prototype, t3;
          }, s = {}.hasOwnProperty, a = [].slice;
          i = e.rangeIsCollapsed, o = e.rangesAreEqual, n = e.objectsAreEqual, t2 = e.getBlockConfig, e.EditorController = function(s2) {
            function u(t3) {
              var n2, i2;
              this.editorElement = t3.editorElement, n2 = t3.document, i2 = t3.html, this.selectionManager = new e.SelectionManager(this.editorElement), this.selectionManager.delegate = this, this.composition = new e.Composition(), this.composition.delegate = this, this.attachmentManager = new e.AttachmentManager(this.composition.getAttachments()), this.attachmentManager.delegate = this, this.inputController = new e["Level" + e.config.input.getLevel() + "InputController"](this.editorElement), this.inputController.delegate = this, this.inputController.responder = this.composition, this.compositionController = new e.CompositionController(this.editorElement, this.composition), this.compositionController.delegate = this, this.toolbarController = new e.ToolbarController(this.editorElement.toolbarElement), this.toolbarController.delegate = this, this.editor = new e.Editor(this.composition, this.selectionManager, this.editorElement), null != n2 ? this.editor.loadDocument(n2) : this.editor.loadHTML(i2);
            }
            var c;
            return r(u, s2), u.prototype.registerSelectionManager = function() {
              return e.selectionChangeObserver.registerSelectionManager(this.selectionManager);
            }, u.prototype.unregisterSelectionManager = function() {
              return e.selectionChangeObserver.unregisterSelectionManager(this.selectionManager);
            }, u.prototype.render = function() {
              return this.compositionController.render();
            }, u.prototype.reparse = function() {
              return this.composition.replaceHTML(this.editorElement.innerHTML);
            }, u.prototype.compositionDidChangeDocument = function() {
              return this.notifyEditorElement("document-change"), this.handlingInput ? void 0 : this.render();
            }, u.prototype.compositionDidChangeCurrentAttributes = function(t3) {
              return this.currentAttributes = t3, this.toolbarController.updateAttributes(this.currentAttributes), this.updateCurrentActions(), this.notifyEditorElement("attributes-change", { attributes: this.currentAttributes });
            }, u.prototype.compositionDidPerformInsertionAtRange = function(t3) {
              return this.pasting ? this.pastedRange = t3 : void 0;
            }, u.prototype.compositionShouldAcceptFile = function(t3) {
              return this.notifyEditorElement("file-accept", { file: t3 });
            }, u.prototype.compositionDidAddAttachment = function(t3) {
              var e2;
              return e2 = this.attachmentManager.manageAttachment(t3), this.notifyEditorElement("attachment-add", { attachment: e2 });
            }, u.prototype.compositionDidEditAttachment = function(t3) {
              var e2;
              return this.compositionController.rerenderViewForObject(t3), e2 = this.attachmentManager.manageAttachment(t3), this.notifyEditorElement("attachment-edit", { attachment: e2 }), this.notifyEditorElement("change");
            }, u.prototype.compositionDidChangeAttachmentPreviewURL = function(t3) {
              return this.compositionController.invalidateViewForObject(t3), this.notifyEditorElement("change");
            }, u.prototype.compositionDidRemoveAttachment = function(t3) {
              var e2;
              return e2 = this.attachmentManager.unmanageAttachment(t3), this.notifyEditorElement("attachment-remove", { attachment: e2 });
            }, u.prototype.compositionDidStartEditingAttachment = function(t3, e2) {
              return this.attachmentLocationRange = this.composition.document.getLocationRangeOfAttachment(t3), this.compositionController.installAttachmentEditorForAttachment(t3, e2), this.selectionManager.setLocationRange(this.attachmentLocationRange);
            }, u.prototype.compositionDidStopEditingAttachment = function() {
              return this.compositionController.uninstallAttachmentEditor(), this.attachmentLocationRange = null;
            }, u.prototype.compositionDidRequestChangingSelectionToLocationRange = function(t3) {
              return !this.loadingSnapshot || this.isFocused() ? (this.requestedLocationRange = t3, this.compositionRevisionWhenLocationRangeRequested = this.composition.revision, this.handlingInput ? void 0 : this.render()) : void 0;
            }, u.prototype.compositionWillLoadSnapshot = function() {
              return this.loadingSnapshot = true;
            }, u.prototype.compositionDidLoadSnapshot = function() {
              return this.compositionController.refreshViewCache(), this.render(), this.loadingSnapshot = false;
            }, u.prototype.getSelectionManager = function() {
              return this.selectionManager;
            }, u.proxyMethod("getSelectionManager().setLocationRange"), u.proxyMethod("getSelectionManager().getLocationRange"), u.prototype.attachmentManagerDidRequestRemovalOfAttachment = function(t3) {
              return this.removeAttachment(t3);
            }, u.prototype.compositionControllerWillSyncDocumentView = function() {
              return this.inputController.editorWillSyncDocumentView(), this.selectionManager.lock(), this.selectionManager.clearSelection();
            }, u.prototype.compositionControllerDidSyncDocumentView = function() {
              return this.inputController.editorDidSyncDocumentView(), this.selectionManager.unlock(), this.updateCurrentActions(), this.notifyEditorElement("sync");
            }, u.prototype.compositionControllerDidRender = function() {
              return null != this.requestedLocationRange && (this.compositionRevisionWhenLocationRangeRequested === this.composition.revision && this.selectionManager.setLocationRange(this.requestedLocationRange), this.requestedLocationRange = null, this.compositionRevisionWhenLocationRangeRequested = null), this.renderedCompositionRevision !== this.composition.revision && (this.runEditorFilters(), this.composition.updateCurrentAttributes(), this.notifyEditorElement("render")), this.renderedCompositionRevision = this.composition.revision;
            }, u.prototype.compositionControllerDidFocus = function() {
              return this.isFocusedInvisibly() && this.setLocationRange({ index: 0, offset: 0 }), this.toolbarController.hideDialog(), this.notifyEditorElement("focus");
            }, u.prototype.compositionControllerDidBlur = function() {
              return this.notifyEditorElement("blur");
            }, u.prototype.compositionControllerDidSelectAttachment = function(t3, e2) {
              return this.toolbarController.hideDialog(), this.composition.editAttachment(t3, e2);
            }, u.prototype.compositionControllerDidRequestDeselectingAttachment = function(t3) {
              var e2, n2;
              return e2 = null != (n2 = this.attachmentLocationRange) ? n2 : this.composition.document.getLocationRangeOfAttachment(t3), this.selectionManager.setLocationRange(e2[1]);
            }, u.prototype.compositionControllerWillUpdateAttachment = function(t3) {
              return this.editor.recordUndoEntry("Edit Attachment", { context: t3.id, consolidatable: true });
            }, u.prototype.compositionControllerDidRequestRemovalOfAttachment = function(t3) {
              return this.removeAttachment(t3);
            }, u.prototype.inputControllerWillHandleInput = function() {
              return this.handlingInput = true, this.requestedRender = false;
            }, u.prototype.inputControllerDidRequestRender = function() {
              return this.requestedRender = true;
            }, u.prototype.inputControllerDidHandleInput = function() {
              return this.handlingInput = false, this.requestedRender ? (this.requestedRender = false, this.render()) : void 0;
            }, u.prototype.inputControllerDidAllowUnhandledInput = function() {
              return this.notifyEditorElement("change");
            }, u.prototype.inputControllerDidRequestReparse = function() {
              return this.reparse();
            }, u.prototype.inputControllerWillPerformTyping = function() {
              return this.recordTypingUndoEntry();
            }, u.prototype.inputControllerWillPerformFormatting = function(t3) {
              return this.recordFormattingUndoEntry(t3);
            }, u.prototype.inputControllerWillCutText = function() {
              return this.editor.recordUndoEntry("Cut");
            }, u.prototype.inputControllerWillPaste = function(t3) {
              return this.editor.recordUndoEntry("Paste"), this.pasting = true, this.notifyEditorElement("before-paste", { paste: t3 });
            }, u.prototype.inputControllerDidPaste = function(t3) {
              return t3.range = this.pastedRange, this.pastedRange = null, this.pasting = null, this.notifyEditorElement("paste", { paste: t3 });
            }, u.prototype.inputControllerWillMoveText = function() {
              return this.editor.recordUndoEntry("Move");
            }, u.prototype.inputControllerWillAttachFiles = function() {
              return this.editor.recordUndoEntry("Drop Files");
            }, u.prototype.inputControllerWillPerformUndo = function() {
              return this.editor.undo();
            }, u.prototype.inputControllerWillPerformRedo = function() {
              return this.editor.redo();
            }, u.prototype.inputControllerDidReceiveKeyboardCommand = function(t3) {
              return this.toolbarController.applyKeyboardCommand(t3);
            }, u.prototype.inputControllerDidStartDrag = function() {
              return this.locationRangeBeforeDrag = this.selectionManager.getLocationRange();
            }, u.prototype.inputControllerDidReceiveDragOverPoint = function(t3) {
              return this.selectionManager.setLocationRangeFromPointRange(t3);
            }, u.prototype.inputControllerDidCancelDrag = function() {
              return this.selectionManager.setLocationRange(this.locationRangeBeforeDrag), this.locationRangeBeforeDrag = null;
            }, u.prototype.locationRangeDidChange = function(t3) {
              return this.composition.updateCurrentAttributes(), this.updateCurrentActions(), this.attachmentLocationRange && !o(this.attachmentLocationRange, t3) && this.composition.stopEditingAttachment(), this.notifyEditorElement("selection-change");
            }, u.prototype.toolbarDidClickButton = function() {
              return this.getLocationRange() ? void 0 : this.setLocationRange({ index: 0, offset: 0 });
            }, u.prototype.toolbarDidInvokeAction = function(t3) {
              return this.invokeAction(t3);
            }, u.prototype.toolbarDidToggleAttribute = function(t3) {
              return this.recordFormattingUndoEntry(t3), this.composition.toggleCurrentAttribute(t3), this.render(), this.selectionFrozen ? void 0 : this.editorElement.focus();
            }, u.prototype.toolbarDidUpdateAttribute = function(t3, e2) {
              return this.recordFormattingUndoEntry(t3), this.composition.setCurrentAttribute(t3, e2), this.render(), this.selectionFrozen ? void 0 : this.editorElement.focus();
            }, u.prototype.toolbarDidRemoveAttribute = function(t3) {
              return this.recordFormattingUndoEntry(t3), this.composition.removeCurrentAttribute(t3), this.render(), this.selectionFrozen ? void 0 : this.editorElement.focus();
            }, u.prototype.toolbarWillShowDialog = function() {
              return this.composition.expandSelectionForEditing(), this.freezeSelection();
            }, u.prototype.toolbarDidShowDialog = function(t3) {
              return this.notifyEditorElement("toolbar-dialog-show", { dialogName: t3 });
            }, u.prototype.toolbarDidHideDialog = function(t3) {
              return this.thawSelection(), this.editorElement.focus(), this.notifyEditorElement("toolbar-dialog-hide", { dialogName: t3 });
            }, u.prototype.freezeSelection = function() {
              return this.selectionFrozen ? void 0 : (this.selectionManager.lock(), this.composition.freezeSelection(), this.selectionFrozen = true, this.render());
            }, u.prototype.thawSelection = function() {
              return this.selectionFrozen ? (this.composition.thawSelection(), this.selectionManager.unlock(), this.selectionFrozen = false, this.render()) : void 0;
            }, u.prototype.actions = { undo: { test: function() {
              return this.editor.canUndo();
            }, perform: function() {
              return this.editor.undo();
            } }, redo: { test: function() {
              return this.editor.canRedo();
            }, perform: function() {
              return this.editor.redo();
            } }, link: { test: function() {
              return this.editor.canActivateAttribute("href");
            } }, increaseNestingLevel: { test: function() {
              return this.editor.canIncreaseNestingLevel();
            }, perform: function() {
              return this.editor.increaseNestingLevel() && this.render();
            } }, decreaseNestingLevel: { test: function() {
              return this.editor.canDecreaseNestingLevel();
            }, perform: function() {
              return this.editor.decreaseNestingLevel() && this.render();
            } }, attachFiles: { test: function() {
              return true;
            }, perform: function() {
              return e.config.input.pickFiles(this.editor.insertFiles);
            } } }, u.prototype.canInvokeAction = function(t3) {
              var e2, n2;
              return this.actionIsExternal(t3) ? true : !!(null != (e2 = this.actions[t3]) && null != (n2 = e2.test) ? n2.call(this) : void 0);
            }, u.prototype.invokeAction = function(t3) {
              var e2, n2;
              return this.actionIsExternal(t3) ? this.notifyEditorElement("action-invoke", { actionName: t3 }) : null != (e2 = this.actions[t3]) && null != (n2 = e2.perform) ? n2.call(this) : void 0;
            }, u.prototype.actionIsExternal = function(t3) {
              return /^x-./.test(t3);
            }, u.prototype.getCurrentActions = function() {
              var t3, e2;
              e2 = {};
              for (t3 in this.actions)
                e2[t3] = this.canInvokeAction(t3);
              return e2;
            }, u.prototype.updateCurrentActions = function() {
              var t3;
              return t3 = this.getCurrentActions(), n(t3, this.currentActions) ? void 0 : (this.currentActions = t3, this.toolbarController.updateActions(this.currentActions), this.notifyEditorElement("actions-change", { actions: this.currentActions }));
            }, u.prototype.runEditorFilters = function() {
              var t3, e2, n2, i2, o2, r2, s3, a2;
              for (a2 = this.composition.getSnapshot(), o2 = this.editor.filters, n2 = 0, i2 = o2.length; i2 > n2; n2++)
                e2 = o2[n2], t3 = a2.document, s3 = a2.selectedRange, a2 = null != (r2 = e2.call(this.editor, a2)) ? r2 : {}, null == a2.document && (a2.document = t3), null == a2.selectedRange && (a2.selectedRange = s3);
              return c(a2, this.composition.getSnapshot()) ? void 0 : this.composition.loadSnapshot(a2);
            }, c = function(t3, e2) {
              return o(t3.selectedRange, e2.selectedRange) && t3.document.isEqualTo(e2.document);
            }, u.prototype.updateInputElement = function() {
              var t3, n2;
              return t3 = this.compositionController.getSerializableElement(), n2 = e.serializeToContentType(t3, "text/html"), this.editorElement.setInputElementValue(n2);
            }, u.prototype.notifyEditorElement = function(t3, e2) {
              switch (t3) {
                case "document-change":
                  this.documentChangedSinceLastRender = true;
                  break;
                case "render":
                  this.documentChangedSinceLastRender && (this.documentChangedSinceLastRender = false, this.notifyEditorElement("change"));
                  break;
                case "change":
                case "attachment-add":
                case "attachment-edit":
                case "attachment-remove":
                  this.updateInputElement();
              }
              return this.editorElement.notify(t3, e2);
            }, u.prototype.removeAttachment = function(t3) {
              return this.editor.recordUndoEntry("Delete Attachment"), this.composition.removeAttachment(t3), this.render();
            }, u.prototype.recordFormattingUndoEntry = function(e2) {
              var n2, o2;
              return n2 = t2(e2), o2 = this.selectionManager.getLocationRange(), n2 || !i(o2) ? this.editor.recordUndoEntry("Formatting", { context: this.getUndoContext(), consolidatable: true }) : void 0;
            }, u.prototype.recordTypingUndoEntry = function() {
              return this.editor.recordUndoEntry("Typing", { context: this.getUndoContext(this.currentAttributes), consolidatable: true });
            }, u.prototype.getUndoContext = function() {
              var t3;
              return t3 = 1 <= arguments.length ? a.call(arguments, 0) : [], [this.getLocationContext(), this.getTimeContext()].concat(a.call(t3));
            }, u.prototype.getLocationContext = function() {
              var t3;
              return t3 = this.selectionManager.getLocationRange(), i(t3) ? t3[0].index : t3;
            }, u.prototype.getTimeContext = function() {
              return e.config.undoInterval > 0 ? Math.floor((/* @__PURE__ */ new Date()).getTime() / e.config.undoInterval) : 0;
            }, u.prototype.isFocused = function() {
              var t3;
              return this.editorElement === (null != (t3 = this.editorElement.ownerDocument) ? t3.activeElement : void 0);
            }, u.prototype.isFocusedInvisibly = function() {
              return this.isFocused() && !this.getLocationRange();
            }, u;
          }(e.Controller);
        }.call(this), function() {
          var t2, n, i, o, r, s, a, u = [].indexOf || function(t3) {
            for (var e2 = 0, n2 = this.length; n2 > e2; e2++)
              if (e2 in this && this[e2] === t3)
                return e2;
            return -1;
          };
          n = e.browser, s = e.makeElement, a = e.triggerEvent, o = e.handleEvent, r = e.handleEventOnce, i = e.findClosestElementFromNode, t2 = e.AttachmentView.attachmentSelector, e.registerElement("trix-editor", function() {
            var c, l, h, p, d, f, g, m, v;
            return g = 0, l = function(t3) {
              return !document.querySelector(":focus") && t3.hasAttribute("autofocus") && document.querySelector("[autofocus]") === t3 ? t3.focus() : void 0;
            }, m = function(t3) {
              return t3.hasAttribute("contenteditable") ? void 0 : (t3.setAttribute("contenteditable", ""), r("focus", { onElement: t3, withCallback: function() {
                return h(t3);
              } }));
            }, h = function(t3) {
              return d(t3), v(t3);
            }, d = function(t3) {
              return ("function" == typeof document.queryCommandSupported ? document.queryCommandSupported("enableObjectResizing") : void 0) ? (document.execCommand("enableObjectResizing", false, false), o("mscontrolselect", { onElement: t3, preventDefault: true })) : void 0;
            }, v = function() {
              var t3;
              return ("function" == typeof document.queryCommandSupported ? document.queryCommandSupported("DefaultParagraphSeparator") : void 0) && (t3 = e.config.blockAttributes["default"].tagName, "div" === t3 || "p" === t3) ? document.execCommand("DefaultParagraphSeparator", false, t3) : void 0;
            }, c = function(t3) {
              return t3.hasAttribute("role") ? void 0 : t3.setAttribute("role", "textbox");
            }, f = function(t3) {
              var e2;
              if (!t3.hasAttribute("aria-label") && !t3.hasAttribute("aria-labelledby"))
                return (e2 = function() {
                  var e3, n2, i2;
                  return i2 = function() {
                    var n3, i3, o2, r2;
                    for (o2 = t3.labels, r2 = [], n3 = 0, i3 = o2.length; i3 > n3; n3++)
                      e3 = o2[n3], e3.contains(t3) || r2.push(e3.textContent);
                    return r2;
                  }(), (n2 = i2.join(" ")) ? t3.setAttribute("aria-label", n2) : t3.removeAttribute("aria-label");
                })(), o("focus", { onElement: t3, withCallback: e2 });
            }, p = function() {
              return n.forcesObjectResizing ? { display: "inline", width: "auto" } : { display: "inline-block", width: "1px" };
            }(), { defaultCSS: "%t {\n  display: block;\n}\n\n%t:empty:not(:focus)::before {\n  content: attr(placeholder);\n  color: graytext;\n  cursor: text;\n  pointer-events: none;\n}\n\n%t a[contenteditable=false] {\n  cursor: text;\n}\n\n%t img {\n  max-width: 100%;\n  height: auto;\n}\n\n%t " + t2 + " figcaption textarea {\n  resize: none;\n}\n\n%t " + t2 + " figcaption textarea.trix-autoresize-clone {\n  position: absolute;\n  left: -9999px;\n  max-height: 0px;\n}\n\n%t " + t2 + " figcaption[data-trix-placeholder]:empty::before {\n  content: attr(data-trix-placeholder);\n  color: graytext;\n}\n\n%t [data-trix-cursor-target] {\n  display: " + p.display + " !important;\n  width: " + p.width + " !important;\n  padding: 0 !important;\n  margin: 0 !important;\n  border: none !important;\n}\n\n%t [data-trix-cursor-target=left] {\n  vertical-align: top !important;\n  margin-left: -1px !important;\n}\n\n%t [data-trix-cursor-target=right] {\n  vertical-align: bottom !important;\n  margin-right: -1px !important;\n}", trixId: { get: function() {
              return this.hasAttribute("trix-id") ? this.getAttribute("trix-id") : (this.setAttribute("trix-id", ++g), this.trixId);
            } }, labels: { get: function() {
              var t3, e2, n2;
              return e2 = [], this.id && this.ownerDocument && e2.push.apply(e2, this.ownerDocument.querySelectorAll("label[for='" + this.id + "']")), (t3 = i(this, { matchingSelector: "label" })) && ((n2 = t3.control) === this || null === n2) && e2.push(t3), e2;
            } }, toolbarElement: { get: function() {
              var t3, e2, n2;
              return this.hasAttribute("toolbar") ? null != (e2 = this.ownerDocument) ? e2.getElementById(this.getAttribute("toolbar")) : void 0 : this.parentNode ? (n2 = "trix-toolbar-" + this.trixId, this.setAttribute("toolbar", n2), t3 = s("trix-toolbar", { id: n2 }), this.parentNode.insertBefore(t3, this), t3) : void 0;
            } }, inputElement: { get: function() {
              var t3, e2, n2;
              return this.hasAttribute("input") ? null != (n2 = this.ownerDocument) ? n2.getElementById(this.getAttribute("input")) : void 0 : this.parentNode ? (e2 = "trix-input-" + this.trixId, this.setAttribute("input", e2), t3 = s("input", { type: "hidden", id: e2 }), this.parentNode.insertBefore(t3, this.nextElementSibling), t3) : void 0;
            } }, editor: { get: function() {
              var t3;
              return null != (t3 = this.editorController) ? t3.editor : void 0;
            } }, name: { get: function() {
              var t3;
              return null != (t3 = this.inputElement) ? t3.name : void 0;
            } }, value: { get: function() {
              var t3;
              return null != (t3 = this.inputElement) ? t3.value : void 0;
            }, set: function(t3) {
              var e2;
              return this.defaultValue = t3, null != (e2 = this.editor) ? e2.loadHTML(this.defaultValue) : void 0;
            } }, notify: function(t3, e2) {
              return this.editorController ? a("trix-" + t3, { onElement: this, attributes: e2 }) : void 0;
            }, setInputElementValue: function(t3) {
              var e2;
              return null != (e2 = this.inputElement) ? e2.value = t3 : void 0;
            }, initialize: function() {
              return this.hasAttribute("data-trix-internal") ? void 0 : (m(this), c(this), f(this));
            }, connect: function() {
              return this.hasAttribute("data-trix-internal") ? void 0 : (this.editorController || (a("trix-before-initialize", { onElement: this }), this.editorController = new e.EditorController({ editorElement: this, html: this.defaultValue = this.value }), requestAnimationFrame(function(t3) {
                return function() {
                  return a("trix-initialize", { onElement: t3 });
                };
              }(this))), this.editorController.registerSelectionManager(), this.registerResetListener(), this.registerClickListener(), l(this));
            }, disconnect: function() {
              var t3;
              return null != (t3 = this.editorController) && t3.unregisterSelectionManager(), this.unregisterResetListener(), this.unregisterClickListener();
            }, registerResetListener: function() {
              return this.resetListener = this.resetBubbled.bind(this), window.addEventListener("reset", this.resetListener, false);
            }, unregisterResetListener: function() {
              return window.removeEventListener("reset", this.resetListener, false);
            }, registerClickListener: function() {
              return this.clickListener = this.clickBubbled.bind(this), window.addEventListener("click", this.clickListener, false);
            }, unregisterClickListener: function() {
              return window.removeEventListener("click", this.clickListener, false);
            }, resetBubbled: function(t3) {
              var e2;
              if (!t3.defaultPrevented && t3.target === (null != (e2 = this.inputElement) ? e2.form : void 0))
                return this.reset();
            }, clickBubbled: function(t3) {
              var e2;
              if (!(t3.defaultPrevented || this.contains(t3.target) || !(e2 = i(t3.target, { matchingSelector: "label" })) || u.call(this.labels, e2) < 0))
                return this.focus();
            }, reset: function() {
              return this.value = this.defaultValue;
            } };
          }());
        }.call(this), function() {
        }.call(this);
      }).call(this), "object" == typeof module && module.exports ? module.exports = e : "function" == typeof define && define.amd && define(e);
    }.call(exports);
  }
});

// packages/forms/resources/js/components/rich-editor.js
var import_trix = __toESM(require_trix(), 1);
import_trix.default.config.blockAttributes.default.tagName = "p";
import_trix.default.config.blockAttributes.default.breakOnReturn = true;
import_trix.default.config.blockAttributes.heading = {
  tagName: "h2",
  terminal: true,
  breakOnReturn: true,
  group: false
};
import_trix.default.config.blockAttributes.subHeading = {
  tagName: "h3",
  terminal: true,
  breakOnReturn: true,
  group: false
};
import_trix.default.config.textAttributes.underline = {
  style: { textDecoration: "underline" },
  inheritable: true,
  parser: (element) => {
    const style = window.getComputedStyle(element);
    return style.textDecoration.includes("underline");
  }
};
import_trix.default.Block.prototype.breaksOnReturn = function() {
  const lastAttribute = this.getLastAttribute();
  const blockConfig = import_trix.default.getBlockConfig(
    lastAttribute ? lastAttribute : "default"
  );
  return blockConfig?.breakOnReturn ?? false;
};
import_trix.default.LineBreakInsertion.prototype.shouldInsertBlockBreak = function() {
  if (this.block.hasAttributes() && this.block.isListItem() && !this.block.isEmpty()) {
    return this.startLocation.offset > 0;
  } else {
    return !this.shouldBreakFormattedBlock() ? this.breaksOnReturn : false;
  }
};
function richEditorFormComponent({ state }) {
  return {
    state,
    init: function() {
      this.$refs.trix?.editor?.loadHTML(this.state);
      this.$watch("state", () => {
        if (document.activeElement === this.$refs.trix) {
          return;
        }
        this.$refs.trix?.editor?.loadHTML(this.state);
      });
    }
  };
}
export {
  richEditorFormComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3RyaXgvZGlzdC90cml4LmpzIiwgIi4uLy4uL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL3JpY2gtZWRpdG9yLmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvKlxuVHJpeCAxLjMuMVxuQ29weXJpZ2h0IFx1MDBBOSAyMDIwIEJhc2VjYW1wLCBMTENcbmh0dHA6Ly90cml4LWVkaXRvci5vcmcvXG4gKi9cbihmdW5jdGlvbigpe30pLmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdDtudWxsPT13aW5kb3cuU2V0JiYod2luZG93LlNldD10PWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCgpe3RoaXMuY2xlYXIoKX1yZXR1cm4gdC5wcm90b3R5cGUuY2xlYXI9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy52YWx1ZXM9W119LHQucHJvdG90eXBlLmhhcz1mdW5jdGlvbih0KXtyZXR1cm4tMSE9PXRoaXMudmFsdWVzLmluZGV4T2YodCl9LHQucHJvdG90eXBlLmFkZD1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5oYXModCl8fHRoaXMudmFsdWVzLnB1c2godCksdGhpc30sdC5wcm90b3R5cGVbXCJkZWxldGVcIl09ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuLTE9PT0oZT10aGlzLnZhbHVlcy5pbmRleE9mKHQpKT8hMToodGhpcy52YWx1ZXMuc3BsaWNlKGUsMSksITApfSx0LnByb3RvdHlwZS5mb3JFYWNoPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKHQ9dGhpcy52YWx1ZXMpLmZvckVhY2guYXBwbHkodCxhcmd1bWVudHMpfSx0fSgpKX0uY2FsbCh0aGlzKSxmdW5jdGlvbih0KXtmdW5jdGlvbiBlKCl7fWZ1bmN0aW9uIG4odCxlKXtyZXR1cm4gZnVuY3Rpb24oKXt0LmFwcGx5KGUsYXJndW1lbnRzKX19ZnVuY3Rpb24gaSh0KXtpZihcIm9iamVjdFwiIT10eXBlb2YgdGhpcyl0aHJvdyBuZXcgVHlwZUVycm9yKFwiUHJvbWlzZXMgbXVzdCBiZSBjb25zdHJ1Y3RlZCB2aWEgbmV3XCIpO2lmKFwiZnVuY3Rpb25cIiE9dHlwZW9mIHQpdGhyb3cgbmV3IFR5cGVFcnJvcihcIm5vdCBhIGZ1bmN0aW9uXCIpO3RoaXMuX3N0YXRlPTAsdGhpcy5faGFuZGxlZD0hMSx0aGlzLl92YWx1ZT12b2lkIDAsdGhpcy5fZGVmZXJyZWRzPVtdLGModCx0aGlzKX1mdW5jdGlvbiBvKHQsZSl7Zm9yKDszPT09dC5fc3RhdGU7KXQ9dC5fdmFsdWU7cmV0dXJuIDA9PT10Ll9zdGF0ZT92b2lkIHQuX2RlZmVycmVkcy5wdXNoKGUpOih0Ll9oYW5kbGVkPSEwLHZvaWQgaChmdW5jdGlvbigpe3ZhciBuPTE9PT10Ll9zdGF0ZT9lLm9uRnVsZmlsbGVkOmUub25SZWplY3RlZDtpZihudWxsPT09bilyZXR1cm4gdm9pZCgxPT09dC5fc3RhdGU/cjpzKShlLnByb21pc2UsdC5fdmFsdWUpO3ZhciBpO3RyeXtpPW4odC5fdmFsdWUpfWNhdGNoKG8pe3JldHVybiB2b2lkIHMoZS5wcm9taXNlLG8pfXIoZS5wcm9taXNlLGkpfSkpfWZ1bmN0aW9uIHIodCxlKXt0cnl7aWYoZT09PXQpdGhyb3cgbmV3IFR5cGVFcnJvcihcIkEgcHJvbWlzZSBjYW5ub3QgYmUgcmVzb2x2ZWQgd2l0aCBpdHNlbGYuXCIpO2lmKGUmJihcIm9iamVjdFwiPT10eXBlb2YgZXx8XCJmdW5jdGlvblwiPT10eXBlb2YgZSkpe3ZhciBvPWUudGhlbjtpZihlIGluc3RhbmNlb2YgaSlyZXR1cm4gdC5fc3RhdGU9Myx0Ll92YWx1ZT1lLHZvaWQgYSh0KTtpZihcImZ1bmN0aW9uXCI9PXR5cGVvZiBvKXJldHVybiB2b2lkIGMobihvLGUpLHQpfXQuX3N0YXRlPTEsdC5fdmFsdWU9ZSxhKHQpfWNhdGNoKHIpe3ModCxyKX19ZnVuY3Rpb24gcyh0LGUpe3QuX3N0YXRlPTIsdC5fdmFsdWU9ZSxhKHQpfWZ1bmN0aW9uIGEodCl7Mj09PXQuX3N0YXRlJiYwPT09dC5fZGVmZXJyZWRzLmxlbmd0aCYmc2V0VGltZW91dChmdW5jdGlvbigpe3QuX2hhbmRsZWR8fHAodC5fdmFsdWUpfSwxKTtmb3IodmFyIGU9MCxuPXQuX2RlZmVycmVkcy5sZW5ndGg7bj5lO2UrKylvKHQsdC5fZGVmZXJyZWRzW2VdKTt0Ll9kZWZlcnJlZHM9bnVsbH1mdW5jdGlvbiB1KHQsZSxuKXt0aGlzLm9uRnVsZmlsbGVkPVwiZnVuY3Rpb25cIj09dHlwZW9mIHQ/dDpudWxsLHRoaXMub25SZWplY3RlZD1cImZ1bmN0aW9uXCI9PXR5cGVvZiBlP2U6bnVsbCx0aGlzLnByb21pc2U9bn1mdW5jdGlvbiBjKHQsZSl7dmFyIG49ITE7dHJ5e3QoZnVuY3Rpb24odCl7bnx8KG49ITAscihlLHQpKX0sZnVuY3Rpb24odCl7bnx8KG49ITAscyhlLHQpKX0pfWNhdGNoKGkpe2lmKG4pcmV0dXJuO249ITAscyhlLGkpfX12YXIgbD1zZXRUaW1lb3V0LGg9XCJmdW5jdGlvblwiPT10eXBlb2Ygc2V0SW1tZWRpYXRlJiZzZXRJbW1lZGlhdGV8fGZ1bmN0aW9uKHQpe2wodCwxKX0scD1mdW5jdGlvbih0KXtcInVuZGVmaW5lZFwiIT10eXBlb2YgY29uc29sZSYmY29uc29sZSYmY29uc29sZS53YXJuKFwiUG9zc2libGUgVW5oYW5kbGVkIFByb21pc2UgUmVqZWN0aW9uOlwiLHQpfTtpLnByb3RvdHlwZVtcImNhdGNoXCJdPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnRoZW4obnVsbCx0KX0saS5wcm90b3R5cGUudGhlbj1mdW5jdGlvbih0LG4pe3ZhciByPW5ldyBpKGUpO3JldHVybiBvKHRoaXMsbmV3IHUodCxuLHIpKSxyfSxpLmFsbD1mdW5jdGlvbih0KXt2YXIgZT1BcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbCh0KTtyZXR1cm4gbmV3IGkoZnVuY3Rpb24odCxuKXtmdW5jdGlvbiBpKHIscyl7dHJ5e2lmKHMmJihcIm9iamVjdFwiPT10eXBlb2Ygc3x8XCJmdW5jdGlvblwiPT10eXBlb2Ygcykpe3ZhciBhPXMudGhlbjtpZihcImZ1bmN0aW9uXCI9PXR5cGVvZiBhKXJldHVybiB2b2lkIGEuY2FsbChzLGZ1bmN0aW9uKHQpe2kocix0KX0sbil9ZVtyXT1zLDA9PT0tLW8mJnQoZSl9Y2F0Y2godSl7bih1KX19aWYoMD09PWUubGVuZ3RoKXJldHVybiB0KFtdKTtmb3IodmFyIG89ZS5sZW5ndGgscj0wO3I8ZS5sZW5ndGg7cisrKWkocixlW3JdKX0pfSxpLnJlc29sdmU9ZnVuY3Rpb24odCl7cmV0dXJuIHQmJlwib2JqZWN0XCI9PXR5cGVvZiB0JiZ0LmNvbnN0cnVjdG9yPT09aT90Om5ldyBpKGZ1bmN0aW9uKGUpe2UodCl9KX0saS5yZWplY3Q9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyBpKGZ1bmN0aW9uKGUsbil7bih0KX0pfSxpLnJhY2U9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyBpKGZ1bmN0aW9uKGUsbil7Zm9yKHZhciBpPTAsbz10Lmxlbmd0aDtvPmk7aSsrKXRbaV0udGhlbihlLG4pfSl9LGkuX3NldEltbWVkaWF0ZUZuPWZ1bmN0aW9uKHQpe2g9dH0saS5fc2V0VW5oYW5kbGVkUmVqZWN0aW9uRm49ZnVuY3Rpb24odCl7cD10fSxcInVuZGVmaW5lZFwiIT10eXBlb2YgbW9kdWxlJiZtb2R1bGUuZXhwb3J0cz9tb2R1bGUuZXhwb3J0cz1pOnQuUHJvbWlzZXx8KHQuUHJvbWlzZT1pKX0odGhpcyksZnVuY3Rpb24oKXt2YXIgdD1cIm9iamVjdFwiPT10eXBlb2Ygd2luZG93LmN1c3RvbUVsZW1lbnRzLGU9XCJmdW5jdGlvblwiPT10eXBlb2YgZG9jdW1lbnQucmVnaXN0ZXJFbGVtZW50LG49dHx8ZTtufHwoLyoqXG4gKiBAbGljZW5zZVxuICogQ29weXJpZ2h0IChjKSAyMDE0IFRoZSBQb2x5bWVyIFByb2plY3QgQXV0aG9ycy4gQWxsIHJpZ2h0cyByZXNlcnZlZC5cbiAqIFRoaXMgY29kZSBtYXkgb25seSBiZSB1c2VkIHVuZGVyIHRoZSBCU0Qgc3R5bGUgbGljZW5zZSBmb3VuZCBhdCBodHRwOi8vcG9seW1lci5naXRodWIuaW8vTElDRU5TRS50eHRcbiAqIFRoZSBjb21wbGV0ZSBzZXQgb2YgYXV0aG9ycyBtYXkgYmUgZm91bmQgYXQgaHR0cDovL3BvbHltZXIuZ2l0aHViLmlvL0FVVEhPUlMudHh0XG4gKiBUaGUgY29tcGxldGUgc2V0IG9mIGNvbnRyaWJ1dG9ycyBtYXkgYmUgZm91bmQgYXQgaHR0cDovL3BvbHltZXIuZ2l0aHViLmlvL0NPTlRSSUJVVE9SUy50eHRcbiAqIENvZGUgZGlzdHJpYnV0ZWQgYnkgR29vZ2xlIGFzIHBhcnQgb2YgdGhlIHBvbHltZXIgcHJvamVjdCBpcyBhbHNvXG4gKiBzdWJqZWN0IHRvIGFuIGFkZGl0aW9uYWwgSVAgcmlnaHRzIGdyYW50IGZvdW5kIGF0IGh0dHA6Ly9wb2x5bWVyLmdpdGh1Yi5pby9QQVRFTlRTLnR4dFxuICovXG5cInVuZGVmaW5lZFwiPT10eXBlb2YgV2Vha01hcCYmIWZ1bmN0aW9uKCl7dmFyIHQ9T2JqZWN0LmRlZmluZVByb3BlcnR5LGU9RGF0ZS5ub3coKSUxZTksbj1mdW5jdGlvbigpe3RoaXMubmFtZT1cIl9fc3RcIisoMWU5Kk1hdGgucmFuZG9tKCk+Pj4wKSsoZSsrICtcIl9fXCIpfTtuLnByb3RvdHlwZT17c2V0OmZ1bmN0aW9uKGUsbil7dmFyIGk9ZVt0aGlzLm5hbWVdO3JldHVybiBpJiZpWzBdPT09ZT9pWzFdPW46dChlLHRoaXMubmFtZSx7dmFsdWU6W2Usbl0sd3JpdGFibGU6ITB9KSx0aGlzfSxnZXQ6ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuKGU9dFt0aGlzLm5hbWVdKSYmZVswXT09PXQ/ZVsxXTp2b2lkIDB9LFwiZGVsZXRlXCI6ZnVuY3Rpb24odCl7dmFyIGU9dFt0aGlzLm5hbWVdO3JldHVybiBlJiZlWzBdPT09dD8oZVswXT1lWzFdPXZvaWQgMCwhMCk6ITF9LGhhczpmdW5jdGlvbih0KXt2YXIgZT10W3RoaXMubmFtZV07cmV0dXJuIGU/ZVswXT09PXQ6ITF9fSx3aW5kb3cuV2Vha01hcD1ufSgpLGZ1bmN0aW9uKHQpe2Z1bmN0aW9uIGUodCl7QS5wdXNoKHQpLGJ8fChiPSEwLGcoaSkpfWZ1bmN0aW9uIG4odCl7cmV0dXJuIHdpbmRvdy5TaGFkb3dET01Qb2x5ZmlsbCYmd2luZG93LlNoYWRvd0RPTVBvbHlmaWxsLndyYXBJZk5lZWRlZCh0KXx8dH1mdW5jdGlvbiBpKCl7Yj0hMTt2YXIgdD1BO0E9W10sdC5zb3J0KGZ1bmN0aW9uKHQsZSl7cmV0dXJuIHQudWlkXy1lLnVpZF99KTt2YXIgZT0hMTt0LmZvckVhY2goZnVuY3Rpb24odCl7dmFyIG49dC50YWtlUmVjb3JkcygpO28odCksbi5sZW5ndGgmJih0LmNhbGxiYWNrXyhuLHQpLGU9ITApfSksZSYmaSgpfWZ1bmN0aW9uIG8odCl7dC5ub2Rlc18uZm9yRWFjaChmdW5jdGlvbihlKXt2YXIgbj1tLmdldChlKTtuJiZuLmZvckVhY2goZnVuY3Rpb24oZSl7ZS5vYnNlcnZlcj09PXQmJmUucmVtb3ZlVHJhbnNpZW50T2JzZXJ2ZXJzKCl9KX0pfWZ1bmN0aW9uIHIodCxlKXtmb3IodmFyIG49dDtuO249bi5wYXJlbnROb2RlKXt2YXIgaT1tLmdldChuKTtpZihpKWZvcih2YXIgbz0wO288aS5sZW5ndGg7bysrKXt2YXIgcj1pW29dLHM9ci5vcHRpb25zO2lmKG49PT10fHxzLnN1YnRyZWUpe3ZhciBhPWUocyk7YSYmci5lbnF1ZXVlKGEpfX19fWZ1bmN0aW9uIHModCl7dGhpcy5jYWxsYmFja189dCx0aGlzLm5vZGVzXz1bXSx0aGlzLnJlY29yZHNfPVtdLHRoaXMudWlkXz0rK0N9ZnVuY3Rpb24gYSh0LGUpe3RoaXMudHlwZT10LHRoaXMudGFyZ2V0PWUsdGhpcy5hZGRlZE5vZGVzPVtdLHRoaXMucmVtb3ZlZE5vZGVzPVtdLHRoaXMucHJldmlvdXNTaWJsaW5nPW51bGwsdGhpcy5uZXh0U2libGluZz1udWxsLHRoaXMuYXR0cmlidXRlTmFtZT1udWxsLHRoaXMuYXR0cmlidXRlTmFtZXNwYWNlPW51bGwsdGhpcy5vbGRWYWx1ZT1udWxsfWZ1bmN0aW9uIHUodCl7dmFyIGU9bmV3IGEodC50eXBlLHQudGFyZ2V0KTtyZXR1cm4gZS5hZGRlZE5vZGVzPXQuYWRkZWROb2Rlcy5zbGljZSgpLGUucmVtb3ZlZE5vZGVzPXQucmVtb3ZlZE5vZGVzLnNsaWNlKCksZS5wcmV2aW91c1NpYmxpbmc9dC5wcmV2aW91c1NpYmxpbmcsZS5uZXh0U2libGluZz10Lm5leHRTaWJsaW5nLGUuYXR0cmlidXRlTmFtZT10LmF0dHJpYnV0ZU5hbWUsZS5hdHRyaWJ1dGVOYW1lc3BhY2U9dC5hdHRyaWJ1dGVOYW1lc3BhY2UsZS5vbGRWYWx1ZT10Lm9sZFZhbHVlLGV9ZnVuY3Rpb24gYyh0LGUpe3JldHVybiB4PW5ldyBhKHQsZSl9ZnVuY3Rpb24gbCh0KXtyZXR1cm4gdz93Oih3PXUoeCksdy5vbGRWYWx1ZT10LHcpfWZ1bmN0aW9uIGgoKXt4PXc9dm9pZCAwfWZ1bmN0aW9uIHAodCl7cmV0dXJuIHQ9PT13fHx0PT09eH1mdW5jdGlvbiBkKHQsZSl7cmV0dXJuIHQ9PT1lP3Q6dyYmcCh0KT93Om51bGx9ZnVuY3Rpb24gZih0LGUsbil7dGhpcy5vYnNlcnZlcj10LHRoaXMudGFyZ2V0PWUsdGhpcy5vcHRpb25zPW4sdGhpcy50cmFuc2llbnRPYnNlcnZlZE5vZGVzPVtdfWlmKCF0LkpzTXV0YXRpb25PYnNlcnZlcil7dmFyIGcsbT1uZXcgV2Vha01hcDtpZigvVHJpZGVudHxFZGdlLy50ZXN0KG5hdmlnYXRvci51c2VyQWdlbnQpKWc9c2V0VGltZW91dDtlbHNlIGlmKHdpbmRvdy5zZXRJbW1lZGlhdGUpZz13aW5kb3cuc2V0SW1tZWRpYXRlO2Vsc2V7dmFyIHY9W10seT1TdHJpbmcoTWF0aC5yYW5kb20oKSk7d2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXCJtZXNzYWdlXCIsZnVuY3Rpb24odCl7aWYodC5kYXRhPT09eSl7dmFyIGU9djt2PVtdLGUuZm9yRWFjaChmdW5jdGlvbih0KXt0KCl9KX19KSxnPWZ1bmN0aW9uKHQpe3YucHVzaCh0KSx3aW5kb3cucG9zdE1lc3NhZ2UoeSxcIipcIil9fXZhciBiPSExLEE9W10sQz0wO3MucHJvdG90eXBlPXtvYnNlcnZlOmZ1bmN0aW9uKHQsZSl7aWYodD1uKHQpLCFlLmNoaWxkTGlzdCYmIWUuYXR0cmlidXRlcyYmIWUuY2hhcmFjdGVyRGF0YXx8ZS5hdHRyaWJ1dGVPbGRWYWx1ZSYmIWUuYXR0cmlidXRlc3x8ZS5hdHRyaWJ1dGVGaWx0ZXImJmUuYXR0cmlidXRlRmlsdGVyLmxlbmd0aCYmIWUuYXR0cmlidXRlc3x8ZS5jaGFyYWN0ZXJEYXRhT2xkVmFsdWUmJiFlLmNoYXJhY3RlckRhdGEpdGhyb3cgbmV3IFN5bnRheEVycm9yO3ZhciBpPW0uZ2V0KHQpO2l8fG0uc2V0KHQsaT1bXSk7Zm9yKHZhciBvLHI9MDtyPGkubGVuZ3RoO3IrKylpZihpW3JdLm9ic2VydmVyPT09dGhpcyl7bz1pW3JdLG8ucmVtb3ZlTGlzdGVuZXJzKCksby5vcHRpb25zPWU7YnJlYWt9b3x8KG89bmV3IGYodGhpcyx0LGUpLGkucHVzaChvKSx0aGlzLm5vZGVzXy5wdXNoKHQpKSxvLmFkZExpc3RlbmVycygpfSxkaXNjb25uZWN0OmZ1bmN0aW9uKCl7dGhpcy5ub2Rlc18uZm9yRWFjaChmdW5jdGlvbih0KXtmb3IodmFyIGU9bS5nZXQodCksbj0wO248ZS5sZW5ndGg7bisrKXt2YXIgaT1lW25dO2lmKGkub2JzZXJ2ZXI9PT10aGlzKXtpLnJlbW92ZUxpc3RlbmVycygpLGUuc3BsaWNlKG4sMSk7YnJlYWt9fX0sdGhpcyksdGhpcy5yZWNvcmRzXz1bXX0sdGFrZVJlY29yZHM6ZnVuY3Rpb24oKXt2YXIgdD10aGlzLnJlY29yZHNfO3JldHVybiB0aGlzLnJlY29yZHNfPVtdLHR9fTt2YXIgeCx3O2YucHJvdG90eXBlPXtlbnF1ZXVlOmZ1bmN0aW9uKHQpe3ZhciBuPXRoaXMub2JzZXJ2ZXIucmVjb3Jkc18saT1uLmxlbmd0aDtpZihuLmxlbmd0aD4wKXt2YXIgbz1uW2ktMV0scj1kKG8sdCk7aWYocilyZXR1cm4gdm9pZChuW2ktMV09cil9ZWxzZSBlKHRoaXMub2JzZXJ2ZXIpO25baV09dH0sYWRkTGlzdGVuZXJzOmZ1bmN0aW9uKCl7dGhpcy5hZGRMaXN0ZW5lcnNfKHRoaXMudGFyZ2V0KX0sYWRkTGlzdGVuZXJzXzpmdW5jdGlvbih0KXt2YXIgZT10aGlzLm9wdGlvbnM7ZS5hdHRyaWJ1dGVzJiZ0LmFkZEV2ZW50TGlzdGVuZXIoXCJET01BdHRyTW9kaWZpZWRcIix0aGlzLCEwKSxlLmNoYXJhY3RlckRhdGEmJnQuYWRkRXZlbnRMaXN0ZW5lcihcIkRPTUNoYXJhY3RlckRhdGFNb2RpZmllZFwiLHRoaXMsITApLGUuY2hpbGRMaXN0JiZ0LmFkZEV2ZW50TGlzdGVuZXIoXCJET01Ob2RlSW5zZXJ0ZWRcIix0aGlzLCEwKSwoZS5jaGlsZExpc3R8fGUuc3VidHJlZSkmJnQuYWRkRXZlbnRMaXN0ZW5lcihcIkRPTU5vZGVSZW1vdmVkXCIsdGhpcywhMCl9LHJlbW92ZUxpc3RlbmVyczpmdW5jdGlvbigpe3RoaXMucmVtb3ZlTGlzdGVuZXJzXyh0aGlzLnRhcmdldCl9LHJlbW92ZUxpc3RlbmVyc186ZnVuY3Rpb24odCl7dmFyIGU9dGhpcy5vcHRpb25zO2UuYXR0cmlidXRlcyYmdC5yZW1vdmVFdmVudExpc3RlbmVyKFwiRE9NQXR0ck1vZGlmaWVkXCIsdGhpcywhMCksZS5jaGFyYWN0ZXJEYXRhJiZ0LnJlbW92ZUV2ZW50TGlzdGVuZXIoXCJET01DaGFyYWN0ZXJEYXRhTW9kaWZpZWRcIix0aGlzLCEwKSxlLmNoaWxkTGlzdCYmdC5yZW1vdmVFdmVudExpc3RlbmVyKFwiRE9NTm9kZUluc2VydGVkXCIsdGhpcywhMCksKGUuY2hpbGRMaXN0fHxlLnN1YnRyZWUpJiZ0LnJlbW92ZUV2ZW50TGlzdGVuZXIoXCJET01Ob2RlUmVtb3ZlZFwiLHRoaXMsITApfSxhZGRUcmFuc2llbnRPYnNlcnZlcjpmdW5jdGlvbih0KXtpZih0IT09dGhpcy50YXJnZXQpe3RoaXMuYWRkTGlzdGVuZXJzXyh0KSx0aGlzLnRyYW5zaWVudE9ic2VydmVkTm9kZXMucHVzaCh0KTt2YXIgZT1tLmdldCh0KTtlfHxtLnNldCh0LGU9W10pLGUucHVzaCh0aGlzKX19LHJlbW92ZVRyYW5zaWVudE9ic2VydmVyczpmdW5jdGlvbigpe3ZhciB0PXRoaXMudHJhbnNpZW50T2JzZXJ2ZWROb2Rlczt0aGlzLnRyYW5zaWVudE9ic2VydmVkTm9kZXM9W10sdC5mb3JFYWNoKGZ1bmN0aW9uKHQpe3RoaXMucmVtb3ZlTGlzdGVuZXJzXyh0KTtmb3IodmFyIGU9bS5nZXQodCksbj0wO248ZS5sZW5ndGg7bisrKWlmKGVbbl09PT10aGlzKXtlLnNwbGljZShuLDEpO2JyZWFrfX0sdGhpcyl9LGhhbmRsZUV2ZW50OmZ1bmN0aW9uKHQpe3N3aXRjaCh0LnN0b3BJbW1lZGlhdGVQcm9wYWdhdGlvbigpLHQudHlwZSl7Y2FzZVwiRE9NQXR0ck1vZGlmaWVkXCI6dmFyIGU9dC5hdHRyTmFtZSxuPXQucmVsYXRlZE5vZGUubmFtZXNwYWNlVVJJLGk9dC50YXJnZXQsbz1uZXcgYyhcImF0dHJpYnV0ZXNcIixpKTtvLmF0dHJpYnV0ZU5hbWU9ZSxvLmF0dHJpYnV0ZU5hbWVzcGFjZT1uO3ZhciBzPXQuYXR0ckNoYW5nZT09PU11dGF0aW9uRXZlbnQuQURESVRJT04/bnVsbDp0LnByZXZWYWx1ZTtyKGksZnVuY3Rpb24odCl7cmV0dXJuIXQuYXR0cmlidXRlc3x8dC5hdHRyaWJ1dGVGaWx0ZXImJnQuYXR0cmlidXRlRmlsdGVyLmxlbmd0aCYmLTE9PT10LmF0dHJpYnV0ZUZpbHRlci5pbmRleE9mKGUpJiYtMT09PXQuYXR0cmlidXRlRmlsdGVyLmluZGV4T2Yobik/dm9pZCAwOnQuYXR0cmlidXRlT2xkVmFsdWU/bChzKTpvfSk7YnJlYWs7Y2FzZVwiRE9NQ2hhcmFjdGVyRGF0YU1vZGlmaWVkXCI6dmFyIGk9dC50YXJnZXQsbz1jKFwiY2hhcmFjdGVyRGF0YVwiLGkpLHM9dC5wcmV2VmFsdWU7cihpLGZ1bmN0aW9uKHQpe3JldHVybiB0LmNoYXJhY3RlckRhdGE/dC5jaGFyYWN0ZXJEYXRhT2xkVmFsdWU/bChzKTpvOnZvaWQgMH0pO2JyZWFrO2Nhc2VcIkRPTU5vZGVSZW1vdmVkXCI6dGhpcy5hZGRUcmFuc2llbnRPYnNlcnZlcih0LnRhcmdldCk7Y2FzZVwiRE9NTm9kZUluc2VydGVkXCI6dmFyIGEsdSxwPXQudGFyZ2V0O1wiRE9NTm9kZUluc2VydGVkXCI9PT10LnR5cGU/KGE9W3BdLHU9W10pOihhPVtdLHU9W3BdKTt2YXIgZD1wLnByZXZpb3VzU2libGluZyxmPXAubmV4dFNpYmxpbmcsbz1jKFwiY2hpbGRMaXN0XCIsdC50YXJnZXQucGFyZW50Tm9kZSk7by5hZGRlZE5vZGVzPWEsby5yZW1vdmVkTm9kZXM9dSxvLnByZXZpb3VzU2libGluZz1kLG8ubmV4dFNpYmxpbmc9ZixyKHQucmVsYXRlZE5vZGUsZnVuY3Rpb24odCl7cmV0dXJuIHQuY2hpbGRMaXN0P286dm9pZCAwfSl9aCgpfX0sdC5Kc011dGF0aW9uT2JzZXJ2ZXI9cyx0Lk11dGF0aW9uT2JzZXJ2ZXJ8fCh0Lk11dGF0aW9uT2JzZXJ2ZXI9cyxzLl9pc1BvbHlmaWxsZWQ9ITApfX0oc2VsZiksZnVuY3Rpb24oKXtcInVzZSBzdHJpY3RcIjtpZighd2luZG93LnBlcmZvcm1hbmNlfHwhd2luZG93LnBlcmZvcm1hbmNlLm5vdyl7dmFyIHQ9RGF0ZS5ub3coKTt3aW5kb3cucGVyZm9ybWFuY2U9e25vdzpmdW5jdGlvbigpe3JldHVybiBEYXRlLm5vdygpLXR9fX13aW5kb3cucmVxdWVzdEFuaW1hdGlvbkZyYW1lfHwod2luZG93LnJlcXVlc3RBbmltYXRpb25GcmFtZT1mdW5jdGlvbigpe3ZhciB0PXdpbmRvdy53ZWJraXRSZXF1ZXN0QW5pbWF0aW9uRnJhbWV8fHdpbmRvdy5tb3pSZXF1ZXN0QW5pbWF0aW9uRnJhbWU7cmV0dXJuIHQ/ZnVuY3Rpb24oZSl7cmV0dXJuIHQoZnVuY3Rpb24oKXtlKHBlcmZvcm1hbmNlLm5vdygpKX0pfTpmdW5jdGlvbih0KXtyZXR1cm4gd2luZG93LnNldFRpbWVvdXQodCwxZTMvNjApfX0oKSksd2luZG93LmNhbmNlbEFuaW1hdGlvbkZyYW1lfHwod2luZG93LmNhbmNlbEFuaW1hdGlvbkZyYW1lPWZ1bmN0aW9uKCl7cmV0dXJuIHdpbmRvdy53ZWJraXRDYW5jZWxBbmltYXRpb25GcmFtZXx8d2luZG93Lm1vekNhbmNlbEFuaW1hdGlvbkZyYW1lfHxmdW5jdGlvbih0KXtjbGVhclRpbWVvdXQodCl9fSgpKTt2YXIgZT1mdW5jdGlvbigpe3ZhciB0PWRvY3VtZW50LmNyZWF0ZUV2ZW50KFwiRXZlbnRcIik7cmV0dXJuIHQuaW5pdEV2ZW50KFwiZm9vXCIsITAsITApLHQucHJldmVudERlZmF1bHQoKSx0LmRlZmF1bHRQcmV2ZW50ZWR9KCk7aWYoIWUpe3ZhciBuPUV2ZW50LnByb3RvdHlwZS5wcmV2ZW50RGVmYXVsdDtFdmVudC5wcm90b3R5cGUucHJldmVudERlZmF1bHQ9ZnVuY3Rpb24oKXt0aGlzLmNhbmNlbGFibGUmJihuLmNhbGwodGhpcyksT2JqZWN0LmRlZmluZVByb3BlcnR5KHRoaXMsXCJkZWZhdWx0UHJldmVudGVkXCIse2dldDpmdW5jdGlvbigpe3JldHVybiEwfSxjb25maWd1cmFibGU6ITB9KSl9fXZhciBpPS9UcmlkZW50Ly50ZXN0KG5hdmlnYXRvci51c2VyQWdlbnQpO2lmKCghd2luZG93LkN1c3RvbUV2ZW50fHxpJiZcImZ1bmN0aW9uXCIhPXR5cGVvZiB3aW5kb3cuQ3VzdG9tRXZlbnQpJiYod2luZG93LkN1c3RvbUV2ZW50PWZ1bmN0aW9uKHQsZSl7ZT1lfHx7fTt2YXIgbj1kb2N1bWVudC5jcmVhdGVFdmVudChcIkN1c3RvbUV2ZW50XCIpO3JldHVybiBuLmluaXRDdXN0b21FdmVudCh0LEJvb2xlYW4oZS5idWJibGVzKSxCb29sZWFuKGUuY2FuY2VsYWJsZSksZS5kZXRhaWwpLG59LHdpbmRvdy5DdXN0b21FdmVudC5wcm90b3R5cGU9d2luZG93LkV2ZW50LnByb3RvdHlwZSksIXdpbmRvdy5FdmVudHx8aSYmXCJmdW5jdGlvblwiIT10eXBlb2Ygd2luZG93LkV2ZW50KXt2YXIgbz13aW5kb3cuRXZlbnQ7d2luZG93LkV2ZW50PWZ1bmN0aW9uKHQsZSl7ZT1lfHx7fTt2YXIgbj1kb2N1bWVudC5jcmVhdGVFdmVudChcIkV2ZW50XCIpO3JldHVybiBuLmluaXRFdmVudCh0LEJvb2xlYW4oZS5idWJibGVzKSxCb29sZWFuKGUuY2FuY2VsYWJsZSkpLG59LHdpbmRvdy5FdmVudC5wcm90b3R5cGU9by5wcm90b3R5cGV9fSh3aW5kb3cuV2ViQ29tcG9uZW50cyksd2luZG93LkN1c3RvbUVsZW1lbnRzPXdpbmRvdy5DdXN0b21FbGVtZW50c3x8e2ZsYWdzOnt9fSxmdW5jdGlvbih0KXt2YXIgZT10LmZsYWdzLG49W10saT1mdW5jdGlvbih0KXtuLnB1c2godCl9LG89ZnVuY3Rpb24oKXtuLmZvckVhY2goZnVuY3Rpb24oZSl7ZSh0KX0pfTt0LmFkZE1vZHVsZT1pLHQuaW5pdGlhbGl6ZU1vZHVsZXM9byx0Lmhhc05hdGl2ZT1Cb29sZWFuKGRvY3VtZW50LnJlZ2lzdGVyRWxlbWVudCksdC5pc0lFPS9UcmlkZW50Ly50ZXN0KG5hdmlnYXRvci51c2VyQWdlbnQpLHQudXNlTmF0aXZlPSFlLnJlZ2lzdGVyJiZ0Lmhhc05hdGl2ZSYmIXdpbmRvdy5TaGFkb3dET01Qb2x5ZmlsbCYmKCF3aW5kb3cuSFRNTEltcG9ydHN8fHdpbmRvdy5IVE1MSW1wb3J0cy51c2VOYXRpdmUpfSh3aW5kb3cuQ3VzdG9tRWxlbWVudHMpLHdpbmRvdy5DdXN0b21FbGVtZW50cy5hZGRNb2R1bGUoZnVuY3Rpb24odCl7ZnVuY3Rpb24gZSh0LGUpe24odCxmdW5jdGlvbih0KXtyZXR1cm4gZSh0KT8hMDp2b2lkIGkodCxlKX0pLGkodCxlKX1mdW5jdGlvbiBuKHQsZSxpKXt2YXIgbz10LmZpcnN0RWxlbWVudENoaWxkO2lmKCFvKWZvcihvPXQuZmlyc3RDaGlsZDtvJiZvLm5vZGVUeXBlIT09Tm9kZS5FTEVNRU5UX05PREU7KW89by5uZXh0U2libGluZztmb3IoO287KWUobyxpKSE9PSEwJiZuKG8sZSxpKSxvPW8ubmV4dEVsZW1lbnRTaWJsaW5nO3JldHVybiBudWxsfWZ1bmN0aW9uIGkodCxuKXtmb3IodmFyIGk9dC5zaGFkb3dSb290O2k7KWUoaSxuKSxpPWkub2xkZXJTaGFkb3dSb290fWZ1bmN0aW9uIG8odCxlKXtyKHQsZSxbXSl9ZnVuY3Rpb24gcih0LGUsbil7aWYodD13aW5kb3cud3JhcCh0KSwhKG4uaW5kZXhPZih0KT49MCkpe24ucHVzaCh0KTtmb3IodmFyIGksbz10LnF1ZXJ5U2VsZWN0b3JBbGwoXCJsaW5rW3JlbD1cIitzK1wiXVwiKSxhPTAsdT1vLmxlbmd0aDt1PmEmJihpPW9bYV0pO2ErKylpLmltcG9ydCYmcihpLmltcG9ydCxlLG4pO2UodCl9fXZhciBzPXdpbmRvdy5IVE1MSW1wb3J0cz93aW5kb3cuSFRNTEltcG9ydHMuSU1QT1JUX0xJTktfVFlQRTpcIm5vbmVcIjt0LmZvckRvY3VtZW50VHJlZT1vLHQuZm9yU3VidHJlZT1lfSksd2luZG93LkN1c3RvbUVsZW1lbnRzLmFkZE1vZHVsZShmdW5jdGlvbih0KXtmdW5jdGlvbiBlKHQsZSl7cmV0dXJuIG4odCxlKXx8aSh0LGUpfWZ1bmN0aW9uIG4oZSxuKXtyZXR1cm4gdC51cGdyYWRlKGUsbik/ITA6dm9pZChuJiZzKGUpKX1mdW5jdGlvbiBpKHQsZSl7Yih0LGZ1bmN0aW9uKHQpe3JldHVybiBuKHQsZSk/ITA6dm9pZCAwfSl9ZnVuY3Rpb24gbyh0KXt3LnB1c2godCkseHx8KHg9ITAsc2V0VGltZW91dChyKSl9ZnVuY3Rpb24gcigpe3g9ITE7Zm9yKHZhciB0LGU9dyxuPTAsaT1lLmxlbmd0aDtpPm4mJih0PWVbbl0pO24rKyl0KCk7dz1bXX1mdW5jdGlvbiBzKHQpe0M/byhmdW5jdGlvbigpe2EodCl9KTphKHQpfWZ1bmN0aW9uIGEodCl7dC5fX3VwZ3JhZGVkX18mJiF0Ll9fYXR0YWNoZWQmJih0Ll9fYXR0YWNoZWQ9ITAsdC5hdHRhY2hlZENhbGxiYWNrJiZ0LmF0dGFjaGVkQ2FsbGJhY2soKSl9ZnVuY3Rpb24gdSh0KXtjKHQpLGIodCxmdW5jdGlvbih0KXtjKHQpfSl9ZnVuY3Rpb24gYyh0KXtDP28oZnVuY3Rpb24oKXtsKHQpfSk6bCh0KX1mdW5jdGlvbiBsKHQpe3QuX191cGdyYWRlZF9fJiZ0Ll9fYXR0YWNoZWQmJih0Ll9fYXR0YWNoZWQ9ITEsdC5kZXRhY2hlZENhbGxiYWNrJiZ0LmRldGFjaGVkQ2FsbGJhY2soKSl9ZnVuY3Rpb24gaCh0KXtmb3IodmFyIGU9dCxuPXdpbmRvdy53cmFwKGRvY3VtZW50KTtlOyl7aWYoZT09bilyZXR1cm4hMDtlPWUucGFyZW50Tm9kZXx8ZS5ub2RlVHlwZT09PU5vZGUuRE9DVU1FTlRfRlJBR01FTlRfTk9ERSYmZS5ob3N0fX1mdW5jdGlvbiBwKHQpe2lmKHQuc2hhZG93Um9vdCYmIXQuc2hhZG93Um9vdC5fX3dhdGNoZWQpe3kuZG9tJiZjb25zb2xlLmxvZyhcIndhdGNoaW5nIHNoYWRvdy1yb290IGZvcjogXCIsdC5sb2NhbE5hbWUpO2Zvcih2YXIgZT10LnNoYWRvd1Jvb3Q7ZTspZyhlKSxlPWUub2xkZXJTaGFkb3dSb290fX1mdW5jdGlvbiBkKHQsbil7aWYoeS5kb20pe3ZhciBpPW5bMF07aWYoaSYmXCJjaGlsZExpc3RcIj09PWkudHlwZSYmaS5hZGRlZE5vZGVzJiZpLmFkZGVkTm9kZXMpe2Zvcih2YXIgbz1pLmFkZGVkTm9kZXNbMF07byYmbyE9PWRvY3VtZW50JiYhby5ob3N0OylvPW8ucGFyZW50Tm9kZTt2YXIgcj1vJiYoby5VUkx8fG8uX1VSTHx8by5ob3N0JiZvLmhvc3QubG9jYWxOYW1lKXx8XCJcIjtyPXIuc3BsaXQoXCIvP1wiKS5zaGlmdCgpLnNwbGl0KFwiL1wiKS5wb3AoKX1jb25zb2xlLmdyb3VwKFwibXV0YXRpb25zICglZCkgWyVzXVwiLG4ubGVuZ3RoLHJ8fFwiXCIpfXZhciBzPWgodCk7bi5mb3JFYWNoKGZ1bmN0aW9uKHQpe1wiY2hpbGRMaXN0XCI9PT10LnR5cGUmJihFKHQuYWRkZWROb2RlcyxmdW5jdGlvbih0KXt0LmxvY2FsTmFtZSYmZSh0LHMpfSksRSh0LnJlbW92ZWROb2RlcyxmdW5jdGlvbih0KXt0LmxvY2FsTmFtZSYmdSh0KX0pKX0pLHkuZG9tJiZjb25zb2xlLmdyb3VwRW5kKCl9ZnVuY3Rpb24gZih0KXtmb3IodD13aW5kb3cud3JhcCh0KSx0fHwodD13aW5kb3cud3JhcChkb2N1bWVudCkpO3QucGFyZW50Tm9kZTspdD10LnBhcmVudE5vZGU7dmFyIGU9dC5fX29ic2VydmVyO2UmJihkKHQsZS50YWtlUmVjb3JkcygpKSxyKCkpfWZ1bmN0aW9uIGcodCl7aWYoIXQuX19vYnNlcnZlcil7dmFyIGU9bmV3IE11dGF0aW9uT2JzZXJ2ZXIoZC5iaW5kKHRoaXMsdCkpO2Uub2JzZXJ2ZSh0LHtjaGlsZExpc3Q6ITAsc3VidHJlZTohMH0pLHQuX19vYnNlcnZlcj1lfX1mdW5jdGlvbiBtKHQpe3Q9d2luZG93LndyYXAodCkseS5kb20mJmNvbnNvbGUuZ3JvdXAoXCJ1cGdyYWRlRG9jdW1lbnQ6IFwiLHQuYmFzZVVSSS5zcGxpdChcIi9cIikucG9wKCkpO3ZhciBuPXQ9PT13aW5kb3cud3JhcChkb2N1bWVudCk7ZSh0LG4pLGcodCkseS5kb20mJmNvbnNvbGUuZ3JvdXBFbmQoKX1mdW5jdGlvbiB2KHQpe0EodCxtKX12YXIgeT10LmZsYWdzLGI9dC5mb3JTdWJ0cmVlLEE9dC5mb3JEb2N1bWVudFRyZWUsQz13aW5kb3cuTXV0YXRpb25PYnNlcnZlci5faXNQb2x5ZmlsbGVkJiZ5W1widGhyb3R0bGUtYXR0YWNoZWRcIl07dC5oYXNQb2x5ZmlsbE11dGF0aW9ucz1DLHQuaGFzVGhyb3R0bGVkQXR0YWNoZWQ9Qzt2YXIgeD0hMSx3PVtdLEU9QXJyYXkucHJvdG90eXBlLmZvckVhY2guY2FsbC5iaW5kKEFycmF5LnByb3RvdHlwZS5mb3JFYWNoKSxTPUVsZW1lbnQucHJvdG90eXBlLmNyZWF0ZVNoYWRvd1Jvb3Q7UyYmKEVsZW1lbnQucHJvdG90eXBlLmNyZWF0ZVNoYWRvd1Jvb3Q9ZnVuY3Rpb24oKXt2YXIgdD1TLmNhbGwodGhpcyk7cmV0dXJuIHdpbmRvdy5DdXN0b21FbGVtZW50cy53YXRjaFNoYWRvdyh0aGlzKSx0fSksdC53YXRjaFNoYWRvdz1wLHQudXBncmFkZURvY3VtZW50VHJlZT12LHQudXBncmFkZURvY3VtZW50PW0sdC51cGdyYWRlU3VidHJlZT1pLHQudXBncmFkZUFsbD1lLHQuYXR0YWNoZWQ9cyx0LnRha2VSZWNvcmRzPWZ9KSx3aW5kb3cuQ3VzdG9tRWxlbWVudHMuYWRkTW9kdWxlKGZ1bmN0aW9uKHQpe2Z1bmN0aW9uIGUoZSxpKXtpZihcInRlbXBsYXRlXCI9PT1lLmxvY2FsTmFtZSYmd2luZG93LkhUTUxUZW1wbGF0ZUVsZW1lbnQmJkhUTUxUZW1wbGF0ZUVsZW1lbnQuZGVjb3JhdGUmJkhUTUxUZW1wbGF0ZUVsZW1lbnQuZGVjb3JhdGUoZSksIWUuX191cGdyYWRlZF9fJiZlLm5vZGVUeXBlPT09Tm9kZS5FTEVNRU5UX05PREUpe3ZhciBvPWUuZ2V0QXR0cmlidXRlKFwiaXNcIikscj10LmdldFJlZ2lzdGVyZWREZWZpbml0aW9uKGUubG9jYWxOYW1lKXx8dC5nZXRSZWdpc3RlcmVkRGVmaW5pdGlvbihvKTtpZihyJiYobyYmci50YWc9PWUubG9jYWxOYW1lfHwhbyYmIXIuZXh0ZW5kcykpcmV0dXJuIG4oZSxyLGkpfX1mdW5jdGlvbiBuKGUsbixvKXtyZXR1cm4gcy51cGdyYWRlJiZjb25zb2xlLmdyb3VwKFwidXBncmFkZTpcIixlLmxvY2FsTmFtZSksbi5pcyYmZS5zZXRBdHRyaWJ1dGUoXCJpc1wiLG4uaXMpLGkoZSxuKSxlLl9fdXBncmFkZWRfXz0hMCxyKGUpLG8mJnQuYXR0YWNoZWQoZSksdC51cGdyYWRlU3VidHJlZShlLG8pLHMudXBncmFkZSYmY29uc29sZS5ncm91cEVuZCgpLGV9ZnVuY3Rpb24gaSh0LGUpe09iamVjdC5fX3Byb3RvX18/dC5fX3Byb3RvX189ZS5wcm90b3R5cGU6KG8odCxlLnByb3RvdHlwZSxlLm5hdGl2ZSksdC5fX3Byb3RvX189ZS5wcm90b3R5cGUpfWZ1bmN0aW9uIG8odCxlLG4pe2Zvcih2YXIgaT17fSxvPWU7byE9PW4mJm8hPT1IVE1MRWxlbWVudC5wcm90b3R5cGU7KXtmb3IodmFyIHIscz1PYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyhvKSxhPTA7cj1zW2FdO2ErKylpW3JdfHwoT2JqZWN0LmRlZmluZVByb3BlcnR5KHQscixPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yKG8scikpLGlbcl09MSk7bz1PYmplY3QuZ2V0UHJvdG90eXBlT2Yobyl9fWZ1bmN0aW9uIHIodCl7dC5jcmVhdGVkQ2FsbGJhY2smJnQuY3JlYXRlZENhbGxiYWNrKCl9dmFyIHM9dC5mbGFnczt0LnVwZ3JhZGU9ZSx0LnVwZ3JhZGVXaXRoRGVmaW5pdGlvbj1uLHQuaW1wbGVtZW50UHJvdG90eXBlPWl9KSx3aW5kb3cuQ3VzdG9tRWxlbWVudHMuYWRkTW9kdWxlKGZ1bmN0aW9uKHQpe2Z1bmN0aW9uIGUoZSxpKXt2YXIgdT1pfHx7fTtpZighZSl0aHJvdyBuZXcgRXJyb3IoXCJkb2N1bWVudC5yZWdpc3RlckVsZW1lbnQ6IGZpcnN0IGFyZ3VtZW50IGBuYW1lYCBtdXN0IG5vdCBiZSBlbXB0eVwiKTtpZihlLmluZGV4T2YoXCItXCIpPDApdGhyb3cgbmV3IEVycm9yKFwiZG9jdW1lbnQucmVnaXN0ZXJFbGVtZW50OiBmaXJzdCBhcmd1bWVudCAoJ25hbWUnKSBtdXN0IGNvbnRhaW4gYSBkYXNoICgnLScpLiBBcmd1bWVudCBwcm92aWRlZCB3YXMgJ1wiK1N0cmluZyhlKStcIicuXCIpO2lmKG8oZSkpdGhyb3cgbmV3IEVycm9yKFwiRmFpbGVkIHRvIGV4ZWN1dGUgJ3JlZ2lzdGVyRWxlbWVudCcgb24gJ0RvY3VtZW50JzogUmVnaXN0cmF0aW9uIGZhaWxlZCBmb3IgdHlwZSAnXCIrU3RyaW5nKGUpK1wiJy4gVGhlIHR5cGUgbmFtZSBpcyBpbnZhbGlkLlwiKTtpZihjKGUpKXRocm93IG5ldyBFcnJvcihcIkR1cGxpY2F0ZURlZmluaXRpb25FcnJvcjogYSB0eXBlIHdpdGggbmFtZSAnXCIrU3RyaW5nKGUpK1wiJyBpcyBhbHJlYWR5IHJlZ2lzdGVyZWRcIik7cmV0dXJuIHUucHJvdG90eXBlfHwodS5wcm90b3R5cGU9T2JqZWN0LmNyZWF0ZShIVE1MRWxlbWVudC5wcm90b3R5cGUpKSx1Ll9fbmFtZT1lLnRvTG93ZXJDYXNlKCksdS5leHRlbmRzJiYodS5leHRlbmRzPXUuZXh0ZW5kcy50b0xvd2VyQ2FzZSgpKSx1LmxpZmVjeWNsZT11LmxpZmVjeWNsZXx8e30sdS5hbmNlc3RyeT1yKHUuZXh0ZW5kcykscyh1KSxhKHUpLG4odS5wcm90b3R5cGUpLGwodS5fX25hbWUsdSksdS5jdG9yPWgodSksdS5jdG9yLnByb3RvdHlwZT11LnByb3RvdHlwZSx1LnByb3RvdHlwZS5jb25zdHJ1Y3Rvcj11LmN0b3IsdC5yZWFkeSYmbShkb2N1bWVudCksdS5jdG9yfWZ1bmN0aW9uIG4odCl7aWYoIXQuc2V0QXR0cmlidXRlLl9wb2x5ZmlsbGVkKXt2YXIgZT10LnNldEF0dHJpYnV0ZTt0LnNldEF0dHJpYnV0ZT1mdW5jdGlvbih0LG4pe2kuY2FsbCh0aGlzLHQsbixlKX07dmFyIG49dC5yZW1vdmVBdHRyaWJ1dGU7dC5yZW1vdmVBdHRyaWJ1dGU9ZnVuY3Rpb24odCl7aS5jYWxsKHRoaXMsdCxudWxsLG4pfSx0LnNldEF0dHJpYnV0ZS5fcG9seWZpbGxlZD0hMH19ZnVuY3Rpb24gaSh0LGUsbil7dD10LnRvTG93ZXJDYXNlKCk7dmFyIGk9dGhpcy5nZXRBdHRyaWJ1dGUodCk7bi5hcHBseSh0aGlzLGFyZ3VtZW50cyk7dmFyIG89dGhpcy5nZXRBdHRyaWJ1dGUodCk7dGhpcy5hdHRyaWJ1dGVDaGFuZ2VkQ2FsbGJhY2smJm8hPT1pJiZ0aGlzLmF0dHJpYnV0ZUNoYW5nZWRDYWxsYmFjayh0LGksbyl9ZnVuY3Rpb24gbyh0KXtmb3IodmFyIGU9MDtlPEMubGVuZ3RoO2UrKylpZih0PT09Q1tlXSlyZXR1cm4hMH1mdW5jdGlvbiByKHQpe3ZhciBlPWModCk7cmV0dXJuIGU/cihlLmV4dGVuZHMpLmNvbmNhdChbZV0pOltdfWZ1bmN0aW9uIHModCl7Zm9yKHZhciBlLG49dC5leHRlbmRzLGk9MDtlPXQuYW5jZXN0cnlbaV07aSsrKW49ZS5pcyYmZS50YWc7dC50YWc9bnx8dC5fX25hbWUsbiYmKHQuaXM9dC5fX25hbWUpfWZ1bmN0aW9uIGEodCl7aWYoIU9iamVjdC5fX3Byb3RvX18pe3ZhciBlPUhUTUxFbGVtZW50LnByb3RvdHlwZTtpZih0LmlzKXt2YXIgbj1kb2N1bWVudC5jcmVhdGVFbGVtZW50KHQudGFnKTtlPU9iamVjdC5nZXRQcm90b3R5cGVPZihuKX1mb3IodmFyIGksbz10LnByb3RvdHlwZSxyPSExO287KW89PWUmJihyPSEwKSxpPU9iamVjdC5nZXRQcm90b3R5cGVPZihvKSxpJiYoby5fX3Byb3RvX189aSksbz1pO3J8fGNvbnNvbGUud2Fybih0LnRhZytcIiBwcm90b3R5cGUgbm90IGZvdW5kIGluIHByb3RvdHlwZSBjaGFpbiBmb3IgXCIrdC5pcyksdC5uYXRpdmU9ZX19ZnVuY3Rpb24gdSh0KXtyZXR1cm4geShFKHQudGFnKSx0KX1mdW5jdGlvbiBjKHQpe3JldHVybiB0P3hbdC50b0xvd2VyQ2FzZSgpXTp2b2lkIDB9ZnVuY3Rpb24gbCh0LGUpe3hbdF09ZX1mdW5jdGlvbiBoKHQpe3JldHVybiBmdW5jdGlvbigpe3JldHVybiB1KHQpfX1mdW5jdGlvbiBwKHQsZSxuKXtyZXR1cm4gdD09PXc/ZChlLG4pOlModCxlKX1mdW5jdGlvbiBkKHQsZSl7dCYmKHQ9dC50b0xvd2VyQ2FzZSgpKSxlJiYoZT1lLnRvTG93ZXJDYXNlKCkpO3ZhciBuPWMoZXx8dCk7aWYobil7aWYodD09bi50YWcmJmU9PW4uaXMpcmV0dXJuIG5ldyBuLmN0b3I7aWYoIWUmJiFuLmlzKXJldHVybiBuZXcgbi5jdG9yfXZhciBpO3JldHVybiBlPyhpPWQodCksaS5zZXRBdHRyaWJ1dGUoXCJpc1wiLGUpLGkpOihpPUUodCksdC5pbmRleE9mKFwiLVwiKT49MCYmYihpLEhUTUxFbGVtZW50KSxpKX1mdW5jdGlvbiBmKHQsZSl7dmFyIG49dFtlXTt0W2VdPWZ1bmN0aW9uKCl7dmFyIHQ9bi5hcHBseSh0aGlzLGFyZ3VtZW50cyk7cmV0dXJuIHYodCksdH19dmFyIGcsbT0odC5pc0lFLHQudXBncmFkZURvY3VtZW50VHJlZSksdj10LnVwZ3JhZGVBbGwseT10LnVwZ3JhZGVXaXRoRGVmaW5pdGlvbixiPXQuaW1wbGVtZW50UHJvdG90eXBlLEE9dC51c2VOYXRpdmUsQz1bXCJhbm5vdGF0aW9uLXhtbFwiLFwiY29sb3ItcHJvZmlsZVwiLFwiZm9udC1mYWNlXCIsXCJmb250LWZhY2Utc3JjXCIsXCJmb250LWZhY2UtdXJpXCIsXCJmb250LWZhY2UtZm9ybWF0XCIsXCJmb250LWZhY2UtbmFtZVwiLFwibWlzc2luZy1nbHlwaFwiXSx4PXt9LHc9XCJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hodG1sXCIsRT1kb2N1bWVudC5jcmVhdGVFbGVtZW50LmJpbmQoZG9jdW1lbnQpLFM9ZG9jdW1lbnQuY3JlYXRlRWxlbWVudE5TLmJpbmQoZG9jdW1lbnQpO2c9T2JqZWN0Ll9fcHJvdG9fX3x8QT9mdW5jdGlvbih0LGUpe3JldHVybiB0IGluc3RhbmNlb2YgZX06ZnVuY3Rpb24odCxlKXtpZih0IGluc3RhbmNlb2YgZSlyZXR1cm4hMDtmb3IodmFyIG49dDtuOyl7aWYobj09PWUucHJvdG90eXBlKXJldHVybiEwO249bi5fX3Byb3RvX199cmV0dXJuITF9LGYoTm9kZS5wcm90b3R5cGUsXCJjbG9uZU5vZGVcIiksZihkb2N1bWVudCxcImltcG9ydE5vZGVcIiksZG9jdW1lbnQucmVnaXN0ZXJFbGVtZW50PWUsZG9jdW1lbnQuY3JlYXRlRWxlbWVudD1kLGRvY3VtZW50LmNyZWF0ZUVsZW1lbnROUz1wLHQucmVnaXN0cnk9eCx0Lmluc3RhbmNlb2Y9Zyx0LnJlc2VydmVkVGFnTGlzdD1DLHQuZ2V0UmVnaXN0ZXJlZERlZmluaXRpb249Yyxkb2N1bWVudC5yZWdpc3Rlcj1kb2N1bWVudC5yZWdpc3RlckVsZW1lbnR9KSxmdW5jdGlvbih0KXtmdW5jdGlvbiBlKCl7cih3aW5kb3cud3JhcChkb2N1bWVudCkpLHdpbmRvdy5DdXN0b21FbGVtZW50cy5yZWFkeT0hMDt2YXIgdD13aW5kb3cucmVxdWVzdEFuaW1hdGlvbkZyYW1lfHxmdW5jdGlvbih0KXtzZXRUaW1lb3V0KHQsMTYpfTt0KGZ1bmN0aW9uKCl7c2V0VGltZW91dChmdW5jdGlvbigpe3dpbmRvdy5DdXN0b21FbGVtZW50cy5yZWFkeVRpbWU9RGF0ZS5ub3coKSx3aW5kb3cuSFRNTEltcG9ydHMmJih3aW5kb3cuQ3VzdG9tRWxlbWVudHMuZWxhcHNlZD13aW5kb3cuQ3VzdG9tRWxlbWVudHMucmVhZHlUaW1lLXdpbmRvdy5IVE1MSW1wb3J0cy5yZWFkeVRpbWUpLGRvY3VtZW50LmRpc3BhdGNoRXZlbnQobmV3IEN1c3RvbUV2ZW50KFwiV2ViQ29tcG9uZW50c1JlYWR5XCIse2J1YmJsZXM6ITB9KSl9KX0pfXZhciBuPXQudXNlTmF0aXZlLGk9dC5pbml0aWFsaXplTW9kdWxlcztpZih0LmlzSUUsbil7dmFyIG89ZnVuY3Rpb24oKXt9O3Qud2F0Y2hTaGFkb3c9byx0LnVwZ3JhZGU9byx0LnVwZ3JhZGVBbGw9byx0LnVwZ3JhZGVEb2N1bWVudFRyZWU9byx0LnVwZ3JhZGVTdWJ0cmVlPW8sdC50YWtlUmVjb3Jkcz1vLHQuaW5zdGFuY2VvZj1mdW5jdGlvbih0LGUpe3JldHVybiB0IGluc3RhbmNlb2YgZX19ZWxzZSBpKCk7dmFyIHI9dC51cGdyYWRlRG9jdW1lbnRUcmVlLHM9dC51cGdyYWRlRG9jdW1lbnQ7aWYod2luZG93LndyYXB8fCh3aW5kb3cuU2hhZG93RE9NUG9seWZpbGw/KHdpbmRvdy53cmFwPXdpbmRvdy5TaGFkb3dET01Qb2x5ZmlsbC53cmFwSWZOZWVkZWQsd2luZG93LnVud3JhcD13aW5kb3cuU2hhZG93RE9NUG9seWZpbGwudW53cmFwSWZOZWVkZWQpOndpbmRvdy53cmFwPXdpbmRvdy51bndyYXA9ZnVuY3Rpb24odCl7cmV0dXJuIHR9KSx3aW5kb3cuSFRNTEltcG9ydHMmJih3aW5kb3cuSFRNTEltcG9ydHMuX19pbXBvcnRzUGFyc2luZ0hvb2s9ZnVuY3Rpb24odCl7dC5pbXBvcnQmJnMod3JhcCh0LmltcG9ydCkpfSksXCJjb21wbGV0ZVwiPT09ZG9jdW1lbnQucmVhZHlTdGF0ZXx8dC5mbGFncy5lYWdlcillKCk7ZWxzZSBpZihcImludGVyYWN0aXZlXCIhPT1kb2N1bWVudC5yZWFkeVN0YXRlfHx3aW5kb3cuYXR0YWNoRXZlbnR8fHdpbmRvdy5IVE1MSW1wb3J0cyYmIXdpbmRvdy5IVE1MSW1wb3J0cy5yZWFkeSl7dmFyIGE9d2luZG93LkhUTUxJbXBvcnRzJiYhd2luZG93LkhUTUxJbXBvcnRzLnJlYWR5P1wiSFRNTEltcG9ydHNMb2FkZWRcIjpcIkRPTUNvbnRlbnRMb2FkZWRcIjt3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihhLGUpfWVsc2UgZSgpfSh3aW5kb3cuQ3VzdG9tRWxlbWVudHMpKX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe30uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PXRoaXM7KGZ1bmN0aW9uKCl7KGZ1bmN0aW9uKCl7dGhpcy5Ucml4PXtWRVJTSU9OOlwiMS4zLjFcIixaRVJPX1dJRFRIX1NQQUNFOlwiXFx1ZmVmZlwiLE5PTl9CUkVBS0lOR19TUEFDRTpcIlxceGEwXCIsT0JKRUNUX1JFUExBQ0VNRU5UX0NIQVJBQ1RFUjpcIlxcdWZmZmNcIixicm93c2VyOntjb21wb3Nlc0V4aXN0aW5nVGV4dDovQW5kcm9pZC4qQ2hyb21lLy50ZXN0KG5hdmlnYXRvci51c2VyQWdlbnQpLGZvcmNlc09iamVjdFJlc2l6aW5nOi9UcmlkZW50LipydjoxMS8udGVzdChuYXZpZ2F0b3IudXNlckFnZW50KSxzdXBwb3J0c0lucHV0RXZlbnRzOmZ1bmN0aW9uKCl7dmFyIHQsZSxuLGk7aWYoXCJ1bmRlZmluZWRcIj09dHlwZW9mIElucHV0RXZlbnQpcmV0dXJuITE7Zm9yKGk9W1wiZGF0YVwiLFwiZ2V0VGFyZ2V0UmFuZ2VzXCIsXCJpbnB1dFR5cGVcIl0sdD0wLGU9aS5sZW5ndGg7ZT50O3QrKylpZihuPWlbdF0sIShuIGluIElucHV0RXZlbnQucHJvdG90eXBlKSlyZXR1cm4hMTtyZXR1cm4hMH0oKX0sY29uZmlnOnt9fX0pLmNhbGwodGhpcyl9KS5jYWxsKHQpO3ZhciBlPXQuVHJpeDsoZnVuY3Rpb24oKXsoZnVuY3Rpb24oKXtlLkJhc2ljT2JqZWN0PWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCgpe312YXIgZSxuLGk7cmV0dXJuIHQucHJveHlNZXRob2Q9ZnVuY3Rpb24odCl7dmFyIGksbyxyLHMsYTtyZXR1cm4gcj1uKHQpLGk9ci5uYW1lLHM9ci50b01ldGhvZCxhPXIudG9Qcm9wZXJ0eSxvPXIub3B0aW9uYWwsdGhpcy5wcm90b3R5cGVbaV09ZnVuY3Rpb24oKXt2YXIgdCxuO3JldHVybiB0PW51bGwhPXM/bz9cImZ1bmN0aW9uXCI9PXR5cGVvZiB0aGlzW3NdP3RoaXNbc10oKTp2b2lkIDA6dGhpc1tzXSgpOm51bGwhPWE/dGhpc1thXTp2b2lkIDAsbz8obj1udWxsIT10P3RbaV06dm9pZCAwLG51bGwhPW4/ZS5jYWxsKG4sdCxhcmd1bWVudHMpOnZvaWQgMCk6KG49dFtpXSxlLmNhbGwobix0LGFyZ3VtZW50cykpfX0sbj1mdW5jdGlvbih0KXt2YXIgZSxuO2lmKCEobj10Lm1hdGNoKGkpKSl0aHJvdyBuZXcgRXJyb3IoXCJjYW4ndCBwYXJzZSBAcHJveHlNZXRob2QgZXhwcmVzc2lvbjogXCIrdCk7cmV0dXJuIGU9e25hbWU6bls0XX0sbnVsbCE9blsyXT9lLnRvTWV0aG9kPW5bMV06ZS50b1Byb3BlcnR5PW5bMV0sbnVsbCE9blszXSYmKGUub3B0aW9uYWw9ITApLGV9LGU9RnVuY3Rpb24ucHJvdG90eXBlLmFwcGx5LGk9L14oLis/KShcXChcXCkpPyhcXD8pP1xcLiguKz8pJC8sdH0oKX0pLmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdD1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIGkoKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBvIGluIGUpbi5jYWxsKGUsbykmJih0W29dPWVbb10pO3JldHVybiBpLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgaSx0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxuPXt9Lmhhc093blByb3BlcnR5O2UuT2JqZWN0PWZ1bmN0aW9uKG4pe2Z1bmN0aW9uIGkoKXt0aGlzLmlkPSsrb312YXIgbztyZXR1cm4gdChpLG4pLG89MCxpLmZyb21KU09OU3RyaW5nPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmZyb21KU09OKEpTT04ucGFyc2UodCkpfSxpLnByb3RvdHlwZS5oYXNTYW1lQ29uc3RydWN0b3JBcz1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5jb25zdHJ1Y3Rvcj09PShudWxsIT10P3QuY29uc3RydWN0b3I6dm9pZCAwKX0saS5wcm90b3R5cGUuaXNFcXVhbFRvPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzPT09dH0saS5wcm90b3R5cGUuaW5zcGVjdD1mdW5jdGlvbigpe3ZhciB0LGUsbjtyZXR1cm4gdD1mdW5jdGlvbigpe3ZhciB0LGksbztpPW51bGwhPSh0PXRoaXMuY29udGVudHNGb3JJbnNwZWN0aW9uKCkpP3Q6e30sbz1bXTtmb3IoZSBpbiBpKW49aVtlXSxvLnB1c2goZStcIj1cIituKTtyZXR1cm4gb30uY2FsbCh0aGlzKSxcIiM8XCIrdGhpcy5jb25zdHJ1Y3Rvci5uYW1lK1wiOlwiK3RoaXMuaWQrKHQubGVuZ3RoP1wiIFwiK3Quam9pbihcIiwgXCIpOlwiXCIpK1wiPlwifSxpLnByb3RvdHlwZS5jb250ZW50c0Zvckluc3BlY3Rpb249ZnVuY3Rpb24oKXt9LGkucHJvdG90eXBlLnRvSlNPTlN0cmluZz1mdW5jdGlvbigpe3JldHVybiBKU09OLnN0cmluZ2lmeSh0aGlzKX0saS5wcm90b3R5cGUudG9VVEYxNlN0cmluZz1mdW5jdGlvbigpe3JldHVybiBlLlVURjE2U3RyaW5nLmJveCh0aGlzKX0saS5wcm90b3R5cGUuZ2V0Q2FjaGVLZXk9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pZC50b1N0cmluZygpfSxpfShlLkJhc2ljT2JqZWN0KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuZXh0ZW5kPWZ1bmN0aW9uKHQpe3ZhciBlLG47Zm9yKGUgaW4gdCluPXRbZV0sdGhpc1tlXT1uO3JldHVybiB0aGlzfX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuZXh0ZW5kKHtkZWZlcjpmdW5jdGlvbih0KXtyZXR1cm4gc2V0VGltZW91dCh0LDEpfX0pfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbjtlLmV4dGVuZCh7bm9ybWFsaXplU3BhY2VzOmZ1bmN0aW9uKHQpe3JldHVybiB0LnJlcGxhY2UoUmVnRXhwKFwiXCIrZS5aRVJPX1dJRFRIX1NQQUNFLFwiZ1wiKSxcIlwiKS5yZXBsYWNlKFJlZ0V4cChcIlwiK2UuTk9OX0JSRUFLSU5HX1NQQUNFLFwiZ1wiKSxcIiBcIil9LG5vcm1hbGl6ZU5ld2xpbmVzOmZ1bmN0aW9uKHQpe3JldHVybiB0LnJlcGxhY2UoL1xcclxcbi9nLFwiXFxuXCIpfSxicmVha2FibGVXaGl0ZXNwYWNlUGF0dGVybjpSZWdFeHAoXCJbXlxcXFxTXCIrZS5OT05fQlJFQUtJTkdfU1BBQ0UrXCJdXCIpLHNxdWlzaEJyZWFrYWJsZVdoaXRlc3BhY2U6ZnVuY3Rpb24odCl7cmV0dXJuIHQucmVwbGFjZShSZWdFeHAoXCJcIitlLmJyZWFrYWJsZVdoaXRlc3BhY2VQYXR0ZXJuLnNvdXJjZSxcImdcIiksXCIgXCIpLnJlcGxhY2UoL1xcIHsyLH0vZyxcIiBcIil9LHN1bW1hcml6ZVN0cmluZ0NoYW5nZTpmdW5jdGlvbih0LGkpe3ZhciBvLHIscyxhO3JldHVybiB0PWUuVVRGMTZTdHJpbmcuYm94KHQpLGk9ZS5VVEYxNlN0cmluZy5ib3goaSksaS5sZW5ndGg8dC5sZW5ndGg/KHI9bih0LGkpLGE9clswXSxvPXJbMV0pOihzPW4oaSx0KSxvPXNbMF0sYT1zWzFdKSx7YWRkZWQ6byxyZW1vdmVkOmF9fX0pLG49ZnVuY3Rpb24obixpKXt2YXIgbyxyLHMsYSx1O3JldHVybiBuLmlzRXF1YWxUbyhpKT9bXCJcIixcIlwiXToocj10KG4saSksYT1yLnV0ZjE2U3RyaW5nLmxlbmd0aCxzPWE/KHU9ci5vZmZzZXQscixvPW4uY29kZXBvaW50cy5zbGljZSgwLHUpLmNvbmNhdChuLmNvZGVwb2ludHMuc2xpY2UodSthKSksdChpLGUuVVRGMTZTdHJpbmcuZnJvbUNvZGVwb2ludHMobykpKTp0KGksbiksW3IudXRmMTZTdHJpbmcudG9TdHJpbmcoKSxzLnV0ZjE2U3RyaW5nLnRvU3RyaW5nKCldKX0sdD1mdW5jdGlvbih0LGUpe3ZhciBuLGksbztmb3Iobj0wLGk9dC5sZW5ndGgsbz1lLmxlbmd0aDtpPm4mJnQuY2hhckF0KG4pLmlzRXF1YWxUbyhlLmNoYXJBdChuKSk7KW4rKztmb3IoO2k+bisxJiZ0LmNoYXJBdChpLTEpLmlzRXF1YWxUbyhlLmNoYXJBdChvLTEpKTspaS0tLG8tLTtyZXR1cm57dXRmMTZTdHJpbmc6dC5zbGljZShuLGkpLG9mZnNldDpufX19LmNhbGwodGhpcyksZnVuY3Rpb24oKXtlLmV4dGVuZCh7Y29weU9iamVjdDpmdW5jdGlvbih0KXt2YXIgZSxuLGk7bnVsbD09dCYmKHQ9e30pLG49e307Zm9yKGUgaW4gdClpPXRbZV0sbltlXT1pO3JldHVybiBufSxvYmplY3RzQXJlRXF1YWw6ZnVuY3Rpb24odCxlKXt2YXIgbixpO2lmKG51bGw9PXQmJih0PXt9KSxudWxsPT1lJiYoZT17fSksT2JqZWN0LmtleXModCkubGVuZ3RoIT09T2JqZWN0LmtleXMoZSkubGVuZ3RoKXJldHVybiExO2ZvcihuIGluIHQpaWYoaT10W25dLGkhPT1lW25dKXJldHVybiExO3JldHVybiEwfX0pfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ9W10uc2xpY2U7ZS5leHRlbmQoe2FycmF5c0FyZUVxdWFsOmZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvLHI7aWYobnVsbD09dCYmKHQ9W10pLG51bGw9PWUmJihlPVtdKSx0Lmxlbmd0aCE9PWUubGVuZ3RoKXJldHVybiExO2ZvcihpPW49MCxvPXQubGVuZ3RoO28+bjtpPSsrbilpZihyPXRbaV0sciE9PWVbaV0pcmV0dXJuITE7cmV0dXJuITB9LGFycmF5U3RhcnRzV2l0aDpmdW5jdGlvbih0LG4pe3JldHVybiBudWxsPT10JiYodD1bXSksbnVsbD09biYmKG49W10pLGUuYXJyYXlzQXJlRXF1YWwodC5zbGljZSgwLG4ubGVuZ3RoKSxuKX0sc3BsaWNlQXJyYXk6ZnVuY3Rpb24oKXt2YXIgZSxuLGk7cmV0dXJuIG49YXJndW1lbnRzWzBdLGU9Mjw9YXJndW1lbnRzLmxlbmd0aD90LmNhbGwoYXJndW1lbnRzLDEpOltdLGk9bi5zbGljZSgwKSxpLnNwbGljZS5hcHBseShpLGUpLGl9LHN1bW1hcml6ZUFycmF5Q2hhbmdlOmZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvLHIscyxhLHUsYyxsLGgscDtmb3IobnVsbD09dCYmKHQ9W10pLG51bGw9PWUmJihlPVtdKSxuPVtdLGg9W10sbz1uZXcgU2V0LHI9MCx1PXQubGVuZ3RoO3U+cjtyKyspcD10W3JdLG8uYWRkKHApO2ZvcihpPW5ldyBTZXQscz0wLGM9ZS5sZW5ndGg7Yz5zO3MrKylwPWVbc10saS5hZGQocCksby5oYXMocCl8fG4ucHVzaChwKTtmb3IoYT0wLGw9dC5sZW5ndGg7bD5hO2ErKylwPXRbYV0saS5oYXMocCl8fGgucHVzaChwKTtyZXR1cm57YWRkZWQ6bixyZW1vdmVkOmh9fX0pfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG87dD1udWxsLG49bnVsbCxvPW51bGwsaT1udWxsLGUuZXh0ZW5kKHtnZXRBbGxBdHRyaWJ1dGVOYW1lczpmdW5jdGlvbigpe3JldHVybiBudWxsIT10P3Q6dD1lLmdldFRleHRBdHRyaWJ1dGVOYW1lcygpLmNvbmNhdChlLmdldEJsb2NrQXR0cmlidXRlTmFtZXMoKSl9LGdldEJsb2NrQ29uZmlnOmZ1bmN0aW9uKHQpe3JldHVybiBlLmNvbmZpZy5ibG9ja0F0dHJpYnV0ZXNbdF19LGdldEJsb2NrQXR0cmlidXRlTmFtZXM6ZnVuY3Rpb24oKXtyZXR1cm4gbnVsbCE9bj9uOm49T2JqZWN0LmtleXMoZS5jb25maWcuYmxvY2tBdHRyaWJ1dGVzKX0sZ2V0VGV4dENvbmZpZzpmdW5jdGlvbih0KXtyZXR1cm4gZS5jb25maWcudGV4dEF0dHJpYnV0ZXNbdF19LGdldFRleHRBdHRyaWJ1dGVOYW1lczpmdW5jdGlvbigpe3JldHVybiBudWxsIT1vP286bz1PYmplY3Qua2V5cyhlLmNvbmZpZy50ZXh0QXR0cmlidXRlcyl9LGdldExpc3RBdHRyaWJ1dGVOYW1lczpmdW5jdGlvbigpe3ZhciB0LG47cmV0dXJuIG51bGwhPWk/aTppPWZ1bmN0aW9uKCl7dmFyIGksbztpPWUuY29uZmlnLmJsb2NrQXR0cmlidXRlcyxvPVtdO2Zvcih0IGluIGkpbj1pW3RdLmxpc3RBdHRyaWJ1dGUsbnVsbCE9biYmby5wdXNoKG4pO3JldHVybiBvfSgpfX0pfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scixzPVtdLmluZGV4T2Z8fGZ1bmN0aW9uKHQpe2Zvcih2YXIgZT0wLG49dGhpcy5sZW5ndGg7bj5lO2UrKylpZihlIGluIHRoaXMmJnRoaXNbZV09PT10KXJldHVybiBlO3JldHVybi0xfTt0PWRvY3VtZW50LmRvY3VtZW50RWxlbWVudCxuPW51bGwhPShpPW51bGwhPShvPW51bGwhPShyPXQubWF0Y2hlc1NlbGVjdG9yKT9yOnQud2Via2l0TWF0Y2hlc1NlbGVjdG9yKT9vOnQubXNNYXRjaGVzU2VsZWN0b3IpP2k6dC5tb3pNYXRjaGVzU2VsZWN0b3IsZS5leHRlbmQoe2hhbmRsZUV2ZW50OmZ1bmN0aW9uKG4saSl7dmFyIG8scixzLGEsdSxjLGwsaCxwLGQsZixnO3JldHVybiBoPW51bGwhPWk/aTp7fSxjPWgub25FbGVtZW50LHU9aC5tYXRjaGluZ1NlbGVjdG9yLGc9aC53aXRoQ2FsbGJhY2ssYT1oLmluUGhhc2UsbD1oLnByZXZlbnREZWZhdWx0LGQ9aC50aW1lcyxyPW51bGwhPWM/Yzp0LHA9dSxvPWcsZj1cImNhcHR1cmluZ1wiPT09YSxzPWZ1bmN0aW9uKHQpe3ZhciBuO3JldHVybiBudWxsIT1kJiYwPT09LS1kJiZzLmRlc3Ryb3koKSxuPWUuZmluZENsb3Nlc3RFbGVtZW50RnJvbU5vZGUodC50YXJnZXQse21hdGNoaW5nU2VsZWN0b3I6cH0pLG51bGwhPW4mJihudWxsIT1nJiZnLmNhbGwobix0LG4pLGwpP3QucHJldmVudERlZmF1bHQoKTp2b2lkIDB9LHMuZGVzdHJveT1mdW5jdGlvbigpe3JldHVybiByLnJlbW92ZUV2ZW50TGlzdGVuZXIobixzLGYpfSxyLmFkZEV2ZW50TGlzdGVuZXIobixzLGYpLHN9LGhhbmRsZUV2ZW50T25jZTpmdW5jdGlvbih0LG4pe3JldHVybiBudWxsPT1uJiYobj17fSksbi50aW1lcz0xLGUuaGFuZGxlRXZlbnQodCxuKX0sdHJpZ2dlckV2ZW50OmZ1bmN0aW9uKG4saSl7dmFyIG8scixzLGEsdSxjLGw7cmV0dXJuIGw9bnVsbCE9aT9pOnt9LGM9bC5vbkVsZW1lbnQscj1sLmJ1YmJsZXMscz1sLmNhbmNlbGFibGUsbz1sLmF0dHJpYnV0ZXMsYT1udWxsIT1jP2M6dCxyPXIhPT0hMSxzPXMhPT0hMSx1PWRvY3VtZW50LmNyZWF0ZUV2ZW50KFwiRXZlbnRzXCIpLHUuaW5pdEV2ZW50KG4scixzKSxudWxsIT1vJiZlLmV4dGVuZC5jYWxsKHUsbyksYS5kaXNwYXRjaEV2ZW50KHUpfSxlbGVtZW50TWF0Y2hlc1NlbGVjdG9yOmZ1bmN0aW9uKHQsZSl7cmV0dXJuIDE9PT0obnVsbCE9dD90Lm5vZGVUeXBlOnZvaWQgMCk/bi5jYWxsKHQsZSk6dm9pZCAwfSxmaW5kQ2xvc2VzdEVsZW1lbnRGcm9tTm9kZTpmdW5jdGlvbih0LG4pe3ZhciBpLG8scjtmb3Iobz1udWxsIT1uP246e30saT1vLm1hdGNoaW5nU2VsZWN0b3Iscj1vLnVudGlsTm9kZTtudWxsIT10JiZ0Lm5vZGVUeXBlIT09Tm9kZS5FTEVNRU5UX05PREU7KXQ9dC5wYXJlbnROb2RlO2lmKG51bGwhPXQpe2lmKG51bGw9PWkpcmV0dXJuIHQ7aWYodC5jbG9zZXN0JiZudWxsPT1yKXJldHVybiB0LmNsb3Nlc3QoaSk7Zm9yKDt0JiZ0IT09cjspe2lmKGUuZWxlbWVudE1hdGNoZXNTZWxlY3Rvcih0LGkpKXJldHVybiB0O3Q9dC5wYXJlbnROb2RlfX19LGZpbmRJbm5lckVsZW1lbnQ6ZnVuY3Rpb24odCl7Zm9yKDtudWxsIT10P3QuZmlyc3RFbGVtZW50Q2hpbGQ6dm9pZCAwOyl0PXQuZmlyc3RFbGVtZW50Q2hpbGQ7cmV0dXJuIHR9LGlubmVyRWxlbWVudElzQWN0aXZlOmZ1bmN0aW9uKHQpe3JldHVybiBkb2N1bWVudC5hY3RpdmVFbGVtZW50IT09dCYmZS5lbGVtZW50Q29udGFpbnNOb2RlKHQsZG9jdW1lbnQuYWN0aXZlRWxlbWVudCl9LGVsZW1lbnRDb250YWluc05vZGU6ZnVuY3Rpb24odCxlKXtpZih0JiZlKWZvcig7ZTspe2lmKGU9PT10KXJldHVybiEwO2U9ZS5wYXJlbnROb2RlfX0sZmluZE5vZGVGcm9tQ29udGFpbmVyQW5kT2Zmc2V0OmZ1bmN0aW9uKHQsZSl7dmFyIG47aWYodClyZXR1cm4gdC5ub2RlVHlwZT09PU5vZGUuVEVYVF9OT0RFP3Q6MD09PWU/bnVsbCE9KG49dC5maXJzdENoaWxkKT9uOnQ6dC5jaGlsZE5vZGVzLml0ZW0oZS0xKX0sZmluZEVsZW1lbnRGcm9tQ29udGFpbmVyQW5kT2Zmc2V0OmZ1bmN0aW9uKHQsbil7dmFyIGk7cmV0dXJuIGk9ZS5maW5kTm9kZUZyb21Db250YWluZXJBbmRPZmZzZXQodCxuKSxlLmZpbmRDbG9zZXN0RWxlbWVudEZyb21Ob2RlKGkpfSxmaW5kQ2hpbGRJbmRleE9mTm9kZTpmdW5jdGlvbih0KXt2YXIgZTtpZihudWxsIT10P3QucGFyZW50Tm9kZTp2b2lkIDApe2ZvcihlPTA7dD10LnByZXZpb3VzU2libGluZzspZSsrO3JldHVybiBlfX0scmVtb3ZlTm9kZTpmdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gbnVsbCE9dCYmbnVsbCE9KGU9dC5wYXJlbnROb2RlKT9lLnJlbW92ZUNoaWxkKHQpOnZvaWQgMH0sd2Fsa1RyZWU6ZnVuY3Rpb24odCxlKXt2YXIgbixpLG8scixzO3JldHVybiBvPW51bGwhPWU/ZTp7fSxpPW8ub25seU5vZGVzT2ZUeXBlLHI9by51c2luZ0ZpbHRlcixuPW8uZXhwYW5kRW50aXR5UmVmZXJlbmNlcyxzPWZ1bmN0aW9uKCl7c3dpdGNoKGkpe2Nhc2VcImVsZW1lbnRcIjpyZXR1cm4gTm9kZUZpbHRlci5TSE9XX0VMRU1FTlQ7Y2FzZVwidGV4dFwiOnJldHVybiBOb2RlRmlsdGVyLlNIT1dfVEVYVDtjYXNlXCJjb21tZW50XCI6cmV0dXJuIE5vZGVGaWx0ZXIuU0hPV19DT01NRU5UO2RlZmF1bHQ6cmV0dXJuIE5vZGVGaWx0ZXIuU0hPV19BTEx9fSgpLGRvY3VtZW50LmNyZWF0ZVRyZWVXYWxrZXIodCxzLG51bGwhPXI/cjpudWxsLG49PT0hMCl9LHRhZ05hbWU6ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIG51bGwhPXQmJm51bGwhPShlPXQudGFnTmFtZSk/ZS50b0xvd2VyQ2FzZSgpOnZvaWQgMH0sbWFrZUVsZW1lbnQ6ZnVuY3Rpb24odCxlKXt2YXIgbixpLG8scixzLGEsdSxjLGwsaCxwLGQsZixnO2lmKG51bGw9PWUmJihlPXt9KSxcIm9iamVjdFwiPT10eXBlb2YgdD8oZT10LHQ9ZS50YWdOYW1lKTplPXthdHRyaWJ1dGVzOmV9LG89ZG9jdW1lbnQuY3JlYXRlRWxlbWVudCh0KSxudWxsIT1lLmVkaXRhYmxlJiYobnVsbD09ZS5hdHRyaWJ1dGVzJiYoZS5hdHRyaWJ1dGVzPXt9KSxlLmF0dHJpYnV0ZXMuY29udGVudGVkaXRhYmxlPWUuZWRpdGFibGUpLGUuYXR0cmlidXRlcyl7bD1lLmF0dHJpYnV0ZXM7Zm9yKGEgaW4gbClnPWxbYV0sby5zZXRBdHRyaWJ1dGUoYSxnKX1pZihlLnN0eWxlKXtoPWUuc3R5bGU7Zm9yKGEgaW4gaClnPWhbYV0sby5zdHlsZVthXT1nfWlmKGUuZGF0YSl7cD1lLmRhdGE7Zm9yKGEgaW4gcClnPXBbYV0sby5kYXRhc2V0W2FdPWd9aWYoZS5jbGFzc05hbWUpZm9yKGQ9ZS5jbGFzc05hbWUuc3BsaXQoXCIgXCIpLHI9MCx1PWQubGVuZ3RoO3U+cjtyKyspaT1kW3JdLG8uY2xhc3NMaXN0LmFkZChpKTtpZihlLnRleHRDb250ZW50JiYoby50ZXh0Q29udGVudD1lLnRleHRDb250ZW50KSxlLmNoaWxkTm9kZXMpZm9yKGY9W10uY29uY2F0KGUuY2hpbGROb2Rlcykscz0wLGM9Zi5sZW5ndGg7Yz5zO3MrKyluPWZbc10sby5hcHBlbmRDaGlsZChuKTtyZXR1cm4gb30sZ2V0QmxvY2tUYWdOYW1lczpmdW5jdGlvbigpe3ZhciB0LG47cmV0dXJuIG51bGwhPWUuYmxvY2tUYWdOYW1lcz9lLmJsb2NrVGFnTmFtZXM6ZS5ibG9ja1RhZ05hbWVzPWZ1bmN0aW9uKCl7dmFyIGksbztpPWUuY29uZmlnLmJsb2NrQXR0cmlidXRlcyxvPVtdO2Zvcih0IGluIGkpbj1pW3RdLnRhZ05hbWUsbiYmby5wdXNoKG4pO3JldHVybiBvfSgpfSxub2RlSXNCbG9ja0NvbnRhaW5lcjpmdW5jdGlvbih0KXtyZXR1cm4gZS5ub2RlSXNCbG9ja1N0YXJ0Q29tbWVudChudWxsIT10P3QuZmlyc3RDaGlsZDp2b2lkIDApfSxub2RlUHJvYmFibHlJc0Jsb2NrQ29udGFpbmVyOmZ1bmN0aW9uKHQpe3ZhciBuLGk7cmV0dXJuIG49ZS50YWdOYW1lKHQpLHMuY2FsbChlLmdldEJsb2NrVGFnTmFtZXMoKSxuKT49MCYmKGk9ZS50YWdOYW1lKHQuZmlyc3RDaGlsZCkscy5jYWxsKGUuZ2V0QmxvY2tUYWdOYW1lcygpLGkpPDApfSxub2RlSXNCbG9ja1N0YXJ0OmZ1bmN0aW9uKHQsbil7dmFyIGk7cmV0dXJuIGk9KG51bGwhPW4/bjp7c3RyaWN0OiEwfSkuc3RyaWN0LGk/ZS5ub2RlSXNCbG9ja1N0YXJ0Q29tbWVudCh0KTplLm5vZGVJc0Jsb2NrU3RhcnRDb21tZW50KHQpfHwhZS5ub2RlSXNCbG9ja1N0YXJ0Q29tbWVudCh0LmZpcnN0Q2hpbGQpJiZlLm5vZGVQcm9iYWJseUlzQmxvY2tDb250YWluZXIodCl9LG5vZGVJc0Jsb2NrU3RhcnRDb21tZW50OmZ1bmN0aW9uKHQpe3JldHVybiBlLm5vZGVJc0NvbW1lbnROb2RlKHQpJiZcImJsb2NrXCI9PT0obnVsbCE9dD90LmRhdGE6dm9pZCAwKX0sbm9kZUlzQ29tbWVudE5vZGU6ZnVuY3Rpb24odCl7cmV0dXJuKG51bGwhPXQ/dC5ub2RlVHlwZTp2b2lkIDApPT09Tm9kZS5DT01NRU5UX05PREV9LG5vZGVJc0N1cnNvclRhcmdldDpmdW5jdGlvbih0LG4pe3ZhciBpO3JldHVybiBpPShudWxsIT1uP246e30pLm5hbWUsdD9lLm5vZGVJc1RleHROb2RlKHQpP3QuZGF0YT09PWUuWkVST19XSURUSF9TUEFDRT9pP3QucGFyZW50Tm9kZS5kYXRhc2V0LnRyaXhDdXJzb3JUYXJnZXQ9PT1pOiEwOnZvaWQgMDplLm5vZGVJc0N1cnNvclRhcmdldCh0LmZpcnN0Q2hpbGQpOnZvaWQgMH0sbm9kZUlzQXR0YWNobWVudEVsZW1lbnQ6ZnVuY3Rpb24odCl7cmV0dXJuIGUuZWxlbWVudE1hdGNoZXNTZWxlY3Rvcih0LGUuQXR0YWNobWVudFZpZXcuYXR0YWNobWVudFNlbGVjdG9yKX0sbm9kZUlzRW1wdHlUZXh0Tm9kZTpmdW5jdGlvbih0KXtyZXR1cm4gZS5ub2RlSXNUZXh0Tm9kZSh0KSYmXCJcIj09PShudWxsIT10P3QuZGF0YTp2b2lkIDApfSxub2RlSXNUZXh0Tm9kZTpmdW5jdGlvbih0KXtyZXR1cm4obnVsbCE9dD90Lm5vZGVUeXBlOnZvaWQgMCk9PT1Ob2RlLlRFWFRfTk9ERX19KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0LG4saSxvLHI7dD1lLmNvcHlPYmplY3Qsbz1lLm9iamVjdHNBcmVFcXVhbCxlLmV4dGVuZCh7bm9ybWFsaXplUmFuZ2U6aT1mdW5jdGlvbih0KXt2YXIgZTtpZihudWxsIT10KXJldHVybiBBcnJheS5pc0FycmF5KHQpfHwodD1bdCx0XSksW24odFswXSksbihudWxsIT0oZT10WzFdKT9lOnRbMF0pXX0scmFuZ2VJc0NvbGxhcHNlZDpmdW5jdGlvbih0KXt2YXIgZSxuLG87aWYobnVsbCE9dClyZXR1cm4gbj1pKHQpLG89blswXSxlPW5bMV0scihvLGUpfSxyYW5nZXNBcmVFcXVhbDpmdW5jdGlvbih0LGUpe3ZhciBuLG8scyxhLHUsYztpZihudWxsIT10JiZudWxsIT1lKXJldHVybiBzPWkodCksbz1zWzBdLG49c1sxXSxhPWkoZSksYz1hWzBdLHU9YVsxXSxyKG8sYykmJnIobix1KX19KSxuPWZ1bmN0aW9uKGUpe3JldHVyblwibnVtYmVyXCI9PXR5cGVvZiBlP2U6dChlKX0scj1mdW5jdGlvbih0LGUpe3JldHVyblwibnVtYmVyXCI9PXR5cGVvZiB0P3Q9PT1lOm8odCxlKX19LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbyxyLHMsYTtlLnJlZ2lzdGVyRWxlbWVudD1mdW5jdGlvbih0LGUpe3ZhciBuLGk7cmV0dXJuIG51bGw9PWUmJihlPXt9KSx0PXQudG9Mb3dlckNhc2UoKSxlPWEoZSksaT1zKGUpLChuPWkuZGVmYXVsdENTUykmJihkZWxldGUgaS5kZWZhdWx0Q1NTLG8obix0KSkscih0LGkpfSxvPWZ1bmN0aW9uKHQsZSl7dmFyIG47cmV0dXJuIG49aShlKSxuLnRleHRDb250ZW50PXQucmVwbGFjZSgvJXQvZyxlKX0saT1mdW5jdGlvbihlKXt2YXIgbixpO3JldHVybiBuPWRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzdHlsZVwiKSxuLnNldEF0dHJpYnV0ZShcInR5cGVcIixcInRleHQvY3NzXCIpLG4uc2V0QXR0cmlidXRlKFwiZGF0YS10YWctbmFtZVwiLGUudG9Mb3dlckNhc2UoKSksKGk9dCgpKSYmbi5zZXRBdHRyaWJ1dGUoXCJub25jZVwiLGkpLGRvY3VtZW50LmhlYWQuaW5zZXJ0QmVmb3JlKG4sZG9jdW1lbnQuaGVhZC5maXJzdENoaWxkKSxufSx0PWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKHQ9bihcInRyaXgtY3NwLW5vbmNlXCIpfHxuKFwiY3NwLW5vbmNlXCIpKT90LmdldEF0dHJpYnV0ZShcImNvbnRlbnRcIik6dm9pZCAwfSxuPWZ1bmN0aW9uKHQpe3JldHVybiBkb2N1bWVudC5oZWFkLnF1ZXJ5U2VsZWN0b3IoXCJtZXRhW25hbWU9XCIrdCtcIl1cIil9LHM9ZnVuY3Rpb24odCl7dmFyIGUsbixpO249e307Zm9yKGUgaW4gdClpPXRbZV0sbltlXT1cImZ1bmN0aW9uXCI9PXR5cGVvZiBpP3t2YWx1ZTppfTppO3JldHVybiBufSxhPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIHQ9ZnVuY3Rpb24odCl7dmFyIGUsbixpLG8scjtmb3IoZT17fSxyPVtcImluaXRpYWxpemVcIixcImNvbm5lY3RcIixcImRpc2Nvbm5lY3RcIl0sbj0wLG89ci5sZW5ndGg7bz5uO24rKylpPXJbbl0sZVtpXT10W2ldLGRlbGV0ZSB0W2ldO3JldHVybiBlfSx3aW5kb3cuY3VzdG9tRWxlbWVudHM/ZnVuY3Rpb24oZSl7dmFyIG4saSxvLHIscztyZXR1cm4gcz10KGUpLG89cy5pbml0aWFsaXplLG49cy5jb25uZWN0LGk9cy5kaXNjb25uZWN0LG8mJihyPW4sbj1mdW5jdGlvbigpe3JldHVybiB0aGlzLmluaXRpYWxpemVkfHwodGhpcy5pbml0aWFsaXplZD0hMCxvLmNhbGwodGhpcykpLG51bGwhPXI/ci5jYWxsKHRoaXMpOnZvaWQgMH0pLG4mJihlLmNvbm5lY3RlZENhbGxiYWNrPW4pLGkmJihlLmRpc2Nvbm5lY3RlZENhbGxiYWNrPWkpLGV9OmZ1bmN0aW9uKGUpe3ZhciBuLGksbyxyO3JldHVybiByPXQoZSksbz1yLmluaXRpYWxpemUsbj1yLmNvbm5lY3QsaT1yLmRpc2Nvbm5lY3QsbyYmKGUuY3JlYXRlZENhbGxiYWNrPW8pLG4mJihlLmF0dGFjaGVkQ2FsbGJhY2s9biksaSYmKGUuZGV0YWNoZWRDYWxsYmFjaz1pKSxlfX0oKSxyPWZ1bmN0aW9uKCl7cmV0dXJuIHdpbmRvdy5jdXN0b21FbGVtZW50cz9mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybiBuPWZ1bmN0aW9uKCl7cmV0dXJuXCJvYmplY3RcIj09dHlwZW9mIFJlZmxlY3Q/UmVmbGVjdC5jb25zdHJ1Y3QoSFRNTEVsZW1lbnQsW10sbik6SFRNTEVsZW1lbnQuYXBwbHkodGhpcyl9LE9iamVjdC5zZXRQcm90b3R5cGVPZihuLnByb3RvdHlwZSxIVE1MRWxlbWVudC5wcm90b3R5cGUpLE9iamVjdC5zZXRQcm90b3R5cGVPZihuLEhUTUxFbGVtZW50KSxPYmplY3QuZGVmaW5lUHJvcGVydGllcyhuLnByb3RvdHlwZSxlKSx3aW5kb3cuY3VzdG9tRWxlbWVudHMuZGVmaW5lKHQsbiksbn06ZnVuY3Rpb24odCxlKXt2YXIgbixpO3JldHVybiBpPU9iamVjdC5jcmVhdGUoSFRNTEVsZW1lbnQucHJvdG90eXBlLGUpLG49ZG9jdW1lbnQucmVnaXN0ZXJFbGVtZW50KHQse3Byb3RvdHlwZTppfSksT2JqZWN0LmRlZmluZVByb3BlcnR5KGksXCJjb25zdHJ1Y3RvclwiLHt2YWx1ZTpufSksbn19KCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuO2UuZXh0ZW5kKHtnZXRET01TZWxlY3Rpb246ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gdD13aW5kb3cuZ2V0U2VsZWN0aW9uKCksdC5yYW5nZUNvdW50PjA/dDp2b2lkIDB9LGdldERPTVJhbmdlOmZ1bmN0aW9uKCl7dmFyIG4saTtyZXR1cm4obj1udWxsIT0oaT1lLmdldERPTVNlbGVjdGlvbigpKT9pLmdldFJhbmdlQXQoMCk6dm9pZCAwKSYmIXQobik/bjp2b2lkIDB9LHNldERPTVJhbmdlOmZ1bmN0aW9uKHQpe3ZhciBuO3JldHVybiBuPXdpbmRvdy5nZXRTZWxlY3Rpb24oKSxuLnJlbW92ZUFsbFJhbmdlcygpLG4uYWRkUmFuZ2UodCksZS5zZWxlY3Rpb25DaGFuZ2VPYnNlcnZlci51cGRhdGUoKX19KSx0PWZ1bmN0aW9uKHQpe3JldHVybiBuKHQuc3RhcnRDb250YWluZXIpfHxuKHQuZW5kQ29udGFpbmVyKX0sbj1mdW5jdGlvbih0KXtyZXR1cm4hT2JqZWN0LmdldFByb3RvdHlwZU9mKHQpfX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0O3Q9e1wiYXBwbGljYXRpb24veC10cml4LWZlYXR1cmUtZGV0ZWN0aW9uXCI6XCJ0ZXN0XCJ9LGUuZXh0ZW5kKHtkYXRhVHJhbnNmZXJJc1BsYWluVGV4dDpmdW5jdGlvbih0KXt2YXIgZSxuLGk7cmV0dXJuIGk9dC5nZXREYXRhKFwidGV4dC9wbGFpblwiKSxuPXQuZ2V0RGF0YShcInRleHQvaHRtbFwiKSxpJiZuPyhlPShuZXcgRE9NUGFyc2VyKS5wYXJzZUZyb21TdHJpbmcobixcInRleHQvaHRtbFwiKS5ib2R5LGUudGV4dENvbnRlbnQ9PT1pPyFlLnF1ZXJ5U2VsZWN0b3IoXCIqXCIpOnZvaWQgMCk6bnVsbCE9aT9pLmxlbmd0aDp2b2lkIDB9LGRhdGFUcmFuc2ZlcklzV3JpdGFibGU6ZnVuY3Rpb24oZSl7dmFyIG4saTtpZihudWxsIT0obnVsbCE9ZT9lLnNldERhdGE6dm9pZCAwKSl7Zm9yKG4gaW4gdClpZihpPXRbbl0sIWZ1bmN0aW9uKCl7dHJ5e3JldHVybiBlLnNldERhdGEobixpKSxlLmdldERhdGEobik9PT1pfWNhdGNoKHQpe319KCkpcmV0dXJuO3JldHVybiEwfX0sa2V5RXZlbnRJc0tleWJvYXJkQ29tbWFuZDpmdW5jdGlvbigpe3JldHVybi9NYWN8XmlQLy50ZXN0KG5hdmlnYXRvci5wbGF0Zm9ybSk/ZnVuY3Rpb24odCl7cmV0dXJuIHQubWV0YUtleX06ZnVuY3Rpb24odCl7cmV0dXJuIHQuY3RybEtleX19KCl9KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuZXh0ZW5kKHtSVExfUEFUVEVSTjovW1xcdTA1QkVcXHUwNUMwXFx1MDVDM1xcdTA1RDAtXFx1MDVFQVxcdTA1RjAtXFx1MDVGNFxcdTA2MUJcXHUwNjFGXFx1MDYyMS1cXHUwNjNBXFx1MDY0MC1cXHUwNjRBXFx1MDY2RFxcdTA2NzEtXFx1MDZCN1xcdTA2QkEtXFx1MDZCRVxcdTA2QzAtXFx1MDZDRVxcdTA2RDAtXFx1MDZENVxcdTA2RTVcXHUwNkU2XFx1MjAwRlxcdTIwMkJcXHUyMDJFXFx1RkIxRi1cXHVGQjI4XFx1RkIyQS1cXHVGQjM2XFx1RkIzOC1cXHVGQjNDXFx1RkIzRVxcdUZCNDBcXHVGQjQxXFx1RkI0M1xcdUZCNDRcXHVGQjQ2LVxcdUZCQjFcXHVGQkQzLVxcdUZEM0RcXHVGRDUwLVxcdUZEOEZcXHVGRDkyLVxcdUZEQzdcXHVGREYwLVxcdUZERkJcXHVGRTcwLVxcdUZFNzJcXHVGRTc0XFx1RkU3Ni1cXHVGRUZDXS8sZ2V0RGlyZWN0aW9uOmZ1bmN0aW9uKCl7dmFyIHQsbixpLG87cmV0dXJuIG49ZS5tYWtlRWxlbWVudChcImlucHV0XCIse2RpcjpcImF1dG9cIixuYW1lOlwieFwiLGRpck5hbWU6XCJ4LmRpclwifSksdD1lLm1ha2VFbGVtZW50KFwiZm9ybVwiKSx0LmFwcGVuZENoaWxkKG4pLGk9ZnVuY3Rpb24oKXt0cnl7cmV0dXJuIG5ldyBGb3JtRGF0YSh0KS5oYXMobi5kaXJOYW1lKX1jYXRjaChlKXt9fSgpLG89ZnVuY3Rpb24oKXt0cnl7cmV0dXJuIG4ubWF0Y2hlcyhcIjpkaXIobHRyKSw6ZGlyKHJ0bClcIil9Y2F0Y2godCl7fX0oKSxpP2Z1bmN0aW9uKGUpe3JldHVybiBuLnZhbHVlPWUsbmV3IEZvcm1EYXRhKHQpLmdldChuLmRpck5hbWUpfTpvP2Z1bmN0aW9uKHQpe3JldHVybiBuLnZhbHVlPXQsbi5tYXRjaGVzKFwiOmRpcihydGwpXCIpP1wicnRsXCI6XCJsdHJcIn06ZnVuY3Rpb24odCl7dmFyIG47cmV0dXJuIG49dC50cmltKCkuY2hhckF0KDApLGUuUlRMX1BBVFRFUk4udGVzdChuKT9cInJ0bFwiOlwibHRyXCJ9fSgpfSl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSlpLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LGk9e30uaGFzT3duUHJvcGVydHk7dD1lLmFycmF5c0FyZUVxdWFsLGUuSGFzaD1mdW5jdGlvbihpKXtmdW5jdGlvbiBvKHQpe251bGw9PXQmJih0PXt9KSx0aGlzLnZhbHVlcz1zKHQpLG8uX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKX12YXIgcixzLGEsdSxjO3JldHVybiBuKG8saSksby5mcm9tQ29tbW9uQXR0cmlidXRlc09mT2JqZWN0cz1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxzLGE7aWYobnVsbD09dCYmKHQ9W10pLCF0Lmxlbmd0aClyZXR1cm4gbmV3IHRoaXM7Zm9yKGU9cih0WzBdKSxpPWUuZ2V0S2V5cygpLGE9dC5zbGljZSgxKSxuPTAsbz1hLmxlbmd0aDtvPm47bisrKXM9YVtuXSxpPWUuZ2V0S2V5c0NvbW1vblRvSGFzaChyKHMpKSxlPWUuc2xpY2UoaSk7cmV0dXJuIGV9LG8uYm94PWZ1bmN0aW9uKHQpe3JldHVybiByKHQpfSxvLnByb3RvdHlwZS5hZGQ9ZnVuY3Rpb24odCxlKXtyZXR1cm4gdGhpcy5tZXJnZSh1KHQsZSkpfSxvLnByb3RvdHlwZS5yZW1vdmU9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyBlLkhhc2gocyh0aGlzLnZhbHVlcyx0KSl9LG8ucHJvdG90eXBlLmdldD1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy52YWx1ZXNbdF19LG8ucHJvdG90eXBlLmhhcz1mdW5jdGlvbih0KXtyZXR1cm4gdCBpbiB0aGlzLnZhbHVlc30sby5wcm90b3R5cGUubWVyZ2U9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyBlLkhhc2goYSh0aGlzLnZhbHVlcyxjKHQpKSl9LG8ucHJvdG90eXBlLnNsaWNlPWZ1bmN0aW9uKHQpe3ZhciBuLGksbyxyO2ZvcihyPXt9LG49MCxvPXQubGVuZ3RoO28+bjtuKyspaT10W25dLHRoaXMuaGFzKGkpJiYocltpXT10aGlzLnZhbHVlc1tpXSk7cmV0dXJuIG5ldyBlLkhhc2gocil9LG8ucHJvdG90eXBlLmdldEtleXM9ZnVuY3Rpb24oKXtyZXR1cm4gT2JqZWN0LmtleXModGhpcy52YWx1ZXMpfSxvLnByb3RvdHlwZS5nZXRLZXlzQ29tbW9uVG9IYXNoPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHM7Zm9yKHQ9cih0KSxvPXRoaXMuZ2V0S2V5cygpLHM9W10sZT0wLGk9by5sZW5ndGg7aT5lO2UrKyluPW9bZV0sdGhpcy52YWx1ZXNbbl09PT10LnZhbHVlc1tuXSYmcy5wdXNoKG4pO3JldHVybiBzfSxvLnByb3RvdHlwZS5pc0VxdWFsVG89ZnVuY3Rpb24oZSl7cmV0dXJuIHQodGhpcy50b0FycmF5KCkscihlKS50b0FycmF5KCkpfSxvLnByb3RvdHlwZS5pc0VtcHR5PWZ1bmN0aW9uKCl7cmV0dXJuIDA9PT10aGlzLmdldEtleXMoKS5sZW5ndGh9LG8ucHJvdG90eXBlLnRvQXJyYXk9ZnVuY3Rpb24oKXt2YXIgdCxlLG47cmV0dXJuKG51bGwhPXRoaXMuYXJyYXk/dGhpcy5hcnJheTp0aGlzLmFycmF5PWZ1bmN0aW9uKCl7dmFyIGk7ZT1bXSxpPXRoaXMudmFsdWVzO2Zvcih0IGluIGkpbj1pW3RdLGUucHVzaCh0LG4pO3JldHVybiBlfS5jYWxsKHRoaXMpKS5zbGljZSgwKX0sby5wcm90b3R5cGUudG9PYmplY3Q9ZnVuY3Rpb24oKXtyZXR1cm4gcyh0aGlzLnZhbHVlcyl9LG8ucHJvdG90eXBlLnRvSlNPTj1mdW5jdGlvbigpe3JldHVybiB0aGlzLnRvT2JqZWN0KCl9LG8ucHJvdG90eXBlLmNvbnRlbnRzRm9ySW5zcGVjdGlvbj1mdW5jdGlvbigpe3JldHVybnt2YWx1ZXM6SlNPTi5zdHJpbmdpZnkodGhpcy52YWx1ZXMpfX0sdT1mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybiBuPXt9LG5bdF09ZSxufSxhPWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvO2k9cyh0KTtmb3IobiBpbiBlKW89ZVtuXSxpW25dPW87cmV0dXJuIGl9LHM9ZnVuY3Rpb24odCxlKXt2YXIgbixpLG8scixzO2ZvcihyPXt9LHM9T2JqZWN0LmtleXModCkuc29ydCgpLG49MCxvPXMubGVuZ3RoO28+bjtuKyspaT1zW25dLGkhPT1lJiYocltpXT10W2ldKTtyZXR1cm4gcn0scj1mdW5jdGlvbih0KXtyZXR1cm4gdCBpbnN0YW5jZW9mIGUuSGFzaD90Om5ldyBlLkhhc2godCl9LGM9ZnVuY3Rpb24odCl7cmV0dXJuIHQgaW5zdGFuY2VvZiBlLkhhc2g/dC52YWx1ZXM6dFxufSxvfShlLk9iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXtlLk9iamVjdEdyb3VwPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0LGUpe3ZhciBuLGk7dGhpcy5vYmplY3RzPW51bGwhPXQ/dDpbXSxpPWUuZGVwdGgsbj1lLmFzVHJlZSxuJiYodGhpcy5kZXB0aD1pLHRoaXMub2JqZWN0cz10aGlzLmNvbnN0cnVjdG9yLmdyb3VwT2JqZWN0cyh0aGlzLm9iamVjdHMse2FzVHJlZTpuLGRlcHRoOnRoaXMuZGVwdGgrMX0pKX1yZXR1cm4gdC5ncm91cE9iamVjdHM9ZnVuY3Rpb24odCxlKXt2YXIgbixpLG8scixzLGEsdSxjLGw7Zm9yKG51bGw9PXQmJih0PVtdKSxsPW51bGwhPWU/ZTp7fSxvPWwuZGVwdGgsbj1sLmFzVHJlZSxuJiZudWxsPT1vJiYobz0wKSxjPVtdLHM9MCxhPXQubGVuZ3RoO2E+cztzKyspe2lmKHU9dFtzXSxyKXtpZigoXCJmdW5jdGlvblwiPT10eXBlb2YgdS5jYW5CZUdyb3VwZWQ/dS5jYW5CZUdyb3VwZWQobyk6dm9pZCAwKSYmKFwiZnVuY3Rpb25cIj09dHlwZW9mKGk9cltyLmxlbmd0aC0xXSkuY2FuQmVHcm91cGVkV2l0aD9pLmNhbkJlR3JvdXBlZFdpdGgodSxvKTp2b2lkIDApKXtyLnB1c2godSk7Y29udGludWV9Yy5wdXNoKG5ldyB0aGlzKHIse2RlcHRoOm8sYXNUcmVlOm59KSkscj1udWxsfShcImZ1bmN0aW9uXCI9PXR5cGVvZiB1LmNhbkJlR3JvdXBlZD91LmNhbkJlR3JvdXBlZChvKTp2b2lkIDApP3I9W3VdOmMucHVzaCh1KX1yZXR1cm4gciYmYy5wdXNoKG5ldyB0aGlzKHIse2RlcHRoOm8sYXNUcmVlOm59KSksY30sdC5wcm90b3R5cGUuZ2V0T2JqZWN0cz1mdW5jdGlvbigpe3JldHVybiB0aGlzLm9iamVjdHN9LHQucHJvdG90eXBlLmdldERlcHRoPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZGVwdGh9LHQucHJvdG90eXBlLmdldENhY2hlS2V5PWZ1bmN0aW9uKCl7dmFyIHQsZSxuLGksbztmb3IoZT1bXCJvYmplY3RHcm91cFwiXSxvPXRoaXMuZ2V0T2JqZWN0cygpLHQ9MCxuPW8ubGVuZ3RoO24+dDt0KyspaT1vW3RdLGUucHVzaChpLmdldENhY2hlS2V5KCkpO3JldHVybiBlLmpvaW4oXCIvXCIpfSx0fSgpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBpKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgbyBpbiBlKW4uY2FsbChlLG8pJiYodFtvXT1lW29dKTtyZXR1cm4gaS5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IGksdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sbj17fS5oYXNPd25Qcm9wZXJ0eTtlLk9iamVjdE1hcD1mdW5jdGlvbihlKXtmdW5jdGlvbiBuKHQpe3ZhciBlLG4saSxvLHI7Zm9yKG51bGw9PXQmJih0PVtdKSx0aGlzLm9iamVjdHM9e30saT0wLG89dC5sZW5ndGg7bz5pO2krKylyPXRbaV0sbj1KU09OLnN0cmluZ2lmeShyKSxudWxsPT0oZT10aGlzLm9iamVjdHMpW25dJiYoZVtuXT1yKX1yZXR1cm4gdChuLGUpLG4ucHJvdG90eXBlLmZpbmQ9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGU9SlNPTi5zdHJpbmdpZnkodCksdGhpcy5vYmplY3RzW2VdfSxufShlLkJhc2ljT2JqZWN0KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuRWxlbWVudFN0b3JlPWZ1bmN0aW9uKCl7ZnVuY3Rpb24gdCh0KXt0aGlzLnJlc2V0KHQpfXZhciBlO3JldHVybiB0LnByb3RvdHlwZS5hZGQ9ZnVuY3Rpb24odCl7dmFyIG47cmV0dXJuIG49ZSh0KSx0aGlzLmVsZW1lbnRzW25dPXR9LHQucHJvdG90eXBlLnJlbW92ZT1mdW5jdGlvbih0KXt2YXIgbixpO3JldHVybiBuPWUodCksKGk9dGhpcy5lbGVtZW50c1tuXSk/KGRlbGV0ZSB0aGlzLmVsZW1lbnRzW25dLGkpOnZvaWQgMH0sdC5wcm90b3R5cGUucmVzZXQ9ZnVuY3Rpb24odCl7dmFyIGUsbixpO2ZvcihudWxsPT10JiYodD1bXSksdGhpcy5lbGVtZW50cz17fSxuPTAsaT10Lmxlbmd0aDtpPm47bisrKWU9dFtuXSx0aGlzLmFkZChlKTtyZXR1cm4gdH0sZT1mdW5jdGlvbih0KXtyZXR1cm4gdC5kYXRhc2V0LnRyaXhTdG9yZUtleX0sdH0oKX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe30uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gaSgpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSluLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIGkucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBpLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG49e30uaGFzT3duUHJvcGVydHk7ZS5PcGVyYXRpb249ZnVuY3Rpb24oZSl7ZnVuY3Rpb24gbigpe3JldHVybiBuLl9fc3VwZXJfXy5jb25zdHJ1Y3Rvci5hcHBseSh0aGlzLGFyZ3VtZW50cyl9cmV0dXJuIHQobixlKSxuLnByb3RvdHlwZS5pc1BlcmZvcm1pbmc9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5wZXJmb3JtaW5nPT09ITB9LG4ucHJvdG90eXBlLmhhc1BlcmZvcm1lZD1mdW5jdGlvbigpe3JldHVybiB0aGlzLnBlcmZvcm1lZD09PSEwfSxuLnByb3RvdHlwZS5oYXNTdWNjZWVkZWQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5wZXJmb3JtZWQmJnRoaXMuc3VjY2VlZGVkfSxuLnByb3RvdHlwZS5oYXNGYWlsZWQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5wZXJmb3JtZWQmJiF0aGlzLnN1Y2NlZWRlZH0sbi5wcm90b3R5cGUuZ2V0UHJvbWlzZT1mdW5jdGlvbigpe3JldHVybiBudWxsIT10aGlzLnByb21pc2U/dGhpcy5wcm9taXNlOnRoaXMucHJvbWlzZT1uZXcgUHJvbWlzZShmdW5jdGlvbih0KXtyZXR1cm4gZnVuY3Rpb24oZSxuKXtyZXR1cm4gdC5wZXJmb3JtaW5nPSEwLHQucGVyZm9ybShmdW5jdGlvbihpLG8pe3JldHVybiB0LnN1Y2NlZWRlZD1pLHQucGVyZm9ybWluZz0hMSx0LnBlcmZvcm1lZD0hMCx0LnN1Y2NlZWRlZD9lKG8pOm4obyl9KX19KHRoaXMpKX0sbi5wcm90b3R5cGUucGVyZm9ybT1mdW5jdGlvbih0KXtyZXR1cm4gdCghMSl9LG4ucHJvdG90eXBlLnJlbGVhc2U9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5wcm9taXNlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgdC5jYW5jZWwmJnQuY2FuY2VsKCksdGhpcy5wcm9taXNlPW51bGwsdGhpcy5wZXJmb3JtaW5nPW51bGwsdGhpcy5wZXJmb3JtZWQ9bnVsbCx0aGlzLnN1Y2NlZWRlZD1udWxsfSxuLnByb3h5TWV0aG9kKFwiZ2V0UHJvbWlzZSgpLnRoZW5cIiksbi5wcm94eU1ldGhvZChcImdldFByb21pc2UoKS5jYXRjaFwiKSxufShlLkJhc2ljT2JqZWN0KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0LG4saSxvLHIscz1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpYS5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxhPXt9Lmhhc093blByb3BlcnR5O2UuVVRGMTZTdHJpbmc9ZnVuY3Rpb24odCl7ZnVuY3Rpb24gZSh0LGUpe3RoaXMudWNzMlN0cmluZz10LHRoaXMuY29kZXBvaW50cz1lLHRoaXMubGVuZ3RoPXRoaXMuY29kZXBvaW50cy5sZW5ndGgsdGhpcy51Y3MyTGVuZ3RoPXRoaXMudWNzMlN0cmluZy5sZW5ndGh9cmV0dXJuIHMoZSx0KSxlLmJveD1mdW5jdGlvbih0KXtyZXR1cm4gbnVsbD09dCYmKHQ9XCJcIiksdCBpbnN0YW5jZW9mIHRoaXM/dDp0aGlzLmZyb21VQ1MyU3RyaW5nKG51bGwhPXQ/dC50b1N0cmluZygpOnZvaWQgMCl9LGUuZnJvbVVDUzJTdHJpbmc9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyB0aGlzKHQsbyh0KSl9LGUuZnJvbUNvZGVwb2ludHM9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyB0aGlzKHIodCksdCl9LGUucHJvdG90eXBlLm9mZnNldFRvVUNTMk9mZnNldD1mdW5jdGlvbih0KXtyZXR1cm4gcih0aGlzLmNvZGVwb2ludHMuc2xpY2UoMCxNYXRoLm1heCgwLHQpKSkubGVuZ3RofSxlLnByb3RvdHlwZS5vZmZzZXRGcm9tVUNTMk9mZnNldD1mdW5jdGlvbih0KXtyZXR1cm4gbyh0aGlzLnVjczJTdHJpbmcuc2xpY2UoMCxNYXRoLm1heCgwLHQpKSkubGVuZ3RofSxlLnByb3RvdHlwZS5zbGljZT1mdW5jdGlvbigpe3ZhciB0O3JldHVybiB0aGlzLmNvbnN0cnVjdG9yLmZyb21Db2RlcG9pbnRzKCh0PXRoaXMuY29kZXBvaW50cykuc2xpY2UuYXBwbHkodCxhcmd1bWVudHMpKX0sZS5wcm90b3R5cGUuY2hhckF0PWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnNsaWNlKHQsdCsxKX0sZS5wcm90b3R5cGUuaXNFcXVhbFRvPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbnN0cnVjdG9yLmJveCh0KS51Y3MyU3RyaW5nPT09dGhpcy51Y3MyU3RyaW5nfSxlLnByb3RvdHlwZS50b0pTT049ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy51Y3MyU3RyaW5nfSxlLnByb3RvdHlwZS5nZXRDYWNoZUtleT1mdW5jdGlvbigpe3JldHVybiB0aGlzLnVjczJTdHJpbmd9LGUucHJvdG90eXBlLnRvU3RyaW5nPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudWNzMlN0cmluZ30sZX0oZS5CYXNpY09iamVjdCksdD0xPT09KFwiZnVuY3Rpb25cIj09dHlwZW9mIEFycmF5LmZyb20/QXJyYXkuZnJvbShcIlxcdWQ4M2RcXHVkYzdjXCIpLmxlbmd0aDp2b2lkIDApLG49bnVsbCE9KFwiZnVuY3Rpb25cIj09dHlwZW9mXCIgXCIuY29kZVBvaW50QXQ/XCIgXCIuY29kZVBvaW50QXQoMCk6dm9pZCAwKSxpPVwiIFxcdWQ4M2RcXHVkYzdjXCI9PT0oXCJmdW5jdGlvblwiPT10eXBlb2YgU3RyaW5nLmZyb21Db2RlUG9pbnQ/U3RyaW5nLmZyb21Db2RlUG9pbnQoMzIsMTI4MTI0KTp2b2lkIDApLG89dCYmbj9mdW5jdGlvbih0KXtyZXR1cm4gQXJyYXkuZnJvbSh0KS5tYXAoZnVuY3Rpb24odCl7cmV0dXJuIHQuY29kZVBvaW50QXQoMCl9KX06ZnVuY3Rpb24odCl7dmFyIGUsbixpLG8scjtmb3Iobz1bXSxlPTAsaT10Lmxlbmd0aDtpPmU7KXI9dC5jaGFyQ29kZUF0KGUrKykscj49NTUyOTYmJjU2MzE5Pj1yJiZpPmUmJihuPXQuY2hhckNvZGVBdChlKyspLDU2MzIwPT09KDY0NTEyJm4pP3I9KCgxMDIzJnIpPDwxMCkrKDEwMjMmbikrNjU1MzY6ZS0tKSxvLnB1c2gocik7cmV0dXJuIG99LHI9aT9mdW5jdGlvbih0KXtyZXR1cm4gU3RyaW5nLmZyb21Db2RlUG9pbnQuYXBwbHkoU3RyaW5nLHQpfTpmdW5jdGlvbih0KXt2YXIgZSxuLGk7cmV0dXJuIGU9ZnVuY3Rpb24oKXt2YXIgZSxvLHI7Zm9yKHI9W10sZT0wLG89dC5sZW5ndGg7bz5lO2UrKylpPXRbZV0sbj1cIlwiLGk+NjU1MzUmJihpLT02NTUzNixuKz1TdHJpbmcuZnJvbUNoYXJDb2RlKGk+Pj4xMCYxMDIzfDU1Mjk2KSxpPTU2MzIwfDEwMjMmaSksci5wdXNoKG4rU3RyaW5nLmZyb21DaGFyQ29kZShpKSk7cmV0dXJuIHJ9KCksZS5qb2luKFwiXCIpfX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe30uY2FsbCh0aGlzKSxmdW5jdGlvbigpe30uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuY29uZmlnLmxhbmc9e2F0dGFjaEZpbGVzOlwiQXR0YWNoIEZpbGVzXCIsYm9sZDpcIkJvbGRcIixidWxsZXRzOlwiQnVsbGV0c1wiLFwiYnl0ZVwiOlwiQnl0ZVwiLGJ5dGVzOlwiQnl0ZXNcIixjYXB0aW9uUGxhY2Vob2xkZXI6XCJBZGQgYSBjYXB0aW9uXFx1MjAyNlwiLGNvZGU6XCJDb2RlXCIsaGVhZGluZzE6XCJIZWFkaW5nXCIsaW5kZW50OlwiSW5jcmVhc2UgTGV2ZWxcIixpdGFsaWM6XCJJdGFsaWNcIixsaW5rOlwiTGlua1wiLG51bWJlcnM6XCJOdW1iZXJzXCIsb3V0ZGVudDpcIkRlY3JlYXNlIExldmVsXCIscXVvdGU6XCJRdW90ZVwiLHJlZG86XCJSZWRvXCIscmVtb3ZlOlwiUmVtb3ZlXCIsc3RyaWtlOlwiU3RyaWtldGhyb3VnaFwiLHVuZG86XCJVbmRvXCIsdW5saW5rOlwiVW5saW5rXCIsdXJsOlwiVVJMXCIsdXJsUGxhY2Vob2xkZXI6XCJFbnRlciBhIFVSTFxcdTIwMjZcIixHQjpcIkdCXCIsS0I6XCJLQlwiLE1COlwiTUJcIixQQjpcIlBCXCIsVEI6XCJUQlwifX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuY29uZmlnLmNzcz17YXR0YWNobWVudDpcImF0dGFjaG1lbnRcIixhdHRhY2htZW50Q2FwdGlvbjpcImF0dGFjaG1lbnRfX2NhcHRpb25cIixhdHRhY2htZW50Q2FwdGlvbkVkaXRvcjpcImF0dGFjaG1lbnRfX2NhcHRpb24tZWRpdG9yXCIsYXR0YWNobWVudE1ldGFkYXRhOlwiYXR0YWNobWVudF9fbWV0YWRhdGFcIixhdHRhY2htZW50TWV0YWRhdGFDb250YWluZXI6XCJhdHRhY2htZW50X19tZXRhZGF0YS1jb250YWluZXJcIixhdHRhY2htZW50TmFtZTpcImF0dGFjaG1lbnRfX25hbWVcIixhdHRhY2htZW50UHJvZ3Jlc3M6XCJhdHRhY2htZW50X19wcm9ncmVzc1wiLGF0dGFjaG1lbnRTaXplOlwiYXR0YWNobWVudF9fc2l6ZVwiLGF0dGFjaG1lbnRUb29sYmFyOlwiYXR0YWNobWVudF9fdG9vbGJhclwiLGF0dGFjaG1lbnRHYWxsZXJ5OlwiYXR0YWNobWVudC1nYWxsZXJ5XCJ9fS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ7ZS5jb25maWcuYmxvY2tBdHRyaWJ1dGVzPXQ9e1wiZGVmYXVsdFwiOnt0YWdOYW1lOlwiZGl2XCIscGFyc2U6ITF9LHF1b3RlOnt0YWdOYW1lOlwiYmxvY2txdW90ZVwiLG5lc3RhYmxlOiEwfSxoZWFkaW5nMTp7dGFnTmFtZTpcImgxXCIsdGVybWluYWw6ITAsYnJlYWtPblJldHVybjohMCxncm91cDohMX0sY29kZTp7dGFnTmFtZTpcInByZVwiLHRlcm1pbmFsOiEwLHRleHQ6e3BsYWludGV4dDohMH19LGJ1bGxldExpc3Q6e3RhZ05hbWU6XCJ1bFwiLHBhcnNlOiExfSxidWxsZXQ6e3RhZ05hbWU6XCJsaVwiLGxpc3RBdHRyaWJ1dGU6XCJidWxsZXRMaXN0XCIsZ3JvdXA6ITEsbmVzdGFibGU6ITAsdGVzdDpmdW5jdGlvbihuKXtyZXR1cm4gZS50YWdOYW1lKG4ucGFyZW50Tm9kZSk9PT10W3RoaXMubGlzdEF0dHJpYnV0ZV0udGFnTmFtZX19LG51bWJlckxpc3Q6e3RhZ05hbWU6XCJvbFwiLHBhcnNlOiExfSxudW1iZXI6e3RhZ05hbWU6XCJsaVwiLGxpc3RBdHRyaWJ1dGU6XCJudW1iZXJMaXN0XCIsZ3JvdXA6ITEsbmVzdGFibGU6ITAsdGVzdDpmdW5jdGlvbihuKXtyZXR1cm4gZS50YWdOYW1lKG4ucGFyZW50Tm9kZSk9PT10W3RoaXMubGlzdEF0dHJpYnV0ZV0udGFnTmFtZX19LGF0dGFjaG1lbnRHYWxsZXJ5Ont0YWdOYW1lOlwiZGl2XCIsZXhjbHVzaXZlOiEwLHRlcm1pbmFsOiEwLHBhcnNlOiExLGdyb3VwOiExfX19LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuO3Q9ZS5jb25maWcubGFuZyxuPVt0LmJ5dGVzLHQuS0IsdC5NQix0LkdCLHQuVEIsdC5QQl0sZS5jb25maWcuZmlsZVNpemU9e3ByZWZpeDpcIklFQ1wiLHByZWNpc2lvbjoyLGZvcm1hdHRlcjpmdW5jdGlvbihlKXt2YXIgaSxvLHIscyxhO3N3aXRjaChlKXtjYXNlIDA6cmV0dXJuXCIwIFwiK3QuYnl0ZXM7Y2FzZSAxOnJldHVyblwiMSBcIit0LmJ5dGU7ZGVmYXVsdDpyZXR1cm4gaT1mdW5jdGlvbigpe3N3aXRjaCh0aGlzLnByZWZpeCl7Y2FzZVwiU0lcIjpyZXR1cm4gMWUzO2Nhc2VcIklFQ1wiOnJldHVybiAxMDI0fX0uY2FsbCh0aGlzKSxvPU1hdGguZmxvb3IoTWF0aC5sb2coZSkvTWF0aC5sb2coaSkpLHI9ZS9NYXRoLnBvdyhpLG8pLHM9ci50b0ZpeGVkKHRoaXMucHJlY2lzaW9uKSxhPXMucmVwbGFjZSgvMCokLyxcIlwiKS5yZXBsYWNlKC9cXC4kLyxcIlwiKSxhK1wiIFwiK25bb119fX19LmNhbGwodGhpcyksZnVuY3Rpb24oKXtlLmNvbmZpZy50ZXh0QXR0cmlidXRlcz17Ym9sZDp7dGFnTmFtZTpcInN0cm9uZ1wiLGluaGVyaXRhYmxlOiEwLHBhcnNlcjpmdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT13aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZSh0KSxcImJvbGRcIj09PWUuZm9udFdlaWdodHx8ZS5mb250V2VpZ2h0Pj02MDB9fSxpdGFsaWM6e3RhZ05hbWU6XCJlbVwiLGluaGVyaXRhYmxlOiEwLHBhcnNlcjpmdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT13aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZSh0KSxcIml0YWxpY1wiPT09ZS5mb250U3R5bGV9fSxocmVmOntncm91cFRhZ05hbWU6XCJhXCIscGFyc2VyOmZ1bmN0aW9uKHQpe3ZhciBuLGksbztyZXR1cm4gbj1lLkF0dGFjaG1lbnRWaWV3LmF0dGFjaG1lbnRTZWxlY3RvcixvPVwiYTpub3QoXCIrbitcIilcIiwoaT1lLmZpbmRDbG9zZXN0RWxlbWVudEZyb21Ob2RlKHQse21hdGNoaW5nU2VsZWN0b3I6b30pKT9pLmdldEF0dHJpYnV0ZShcImhyZWZcIik6dm9pZCAwfX0sc3RyaWtlOnt0YWdOYW1lOlwiZGVsXCIsaW5oZXJpdGFibGU6ITB9LGZyb3plbjp7c3R5bGU6e2JhY2tncm91bmRDb2xvcjpcImhpZ2hsaWdodFwifX19fS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scjtyPVwiW2RhdGEtdHJpeC1zZXJpYWxpemU9ZmFsc2VdXCIsbz1bXCJjb250ZW50ZWRpdGFibGVcIixcImRhdGEtdHJpeC1pZFwiLFwiZGF0YS10cml4LXN0b3JlLWtleVwiLFwiZGF0YS10cml4LW11dGFibGVcIixcImRhdGEtdHJpeC1wbGFjZWhvbGRlclwiLFwidGFiaW5kZXhcIl0sbj1cImRhdGEtdHJpeC1zZXJpYWxpemVkLWF0dHJpYnV0ZXNcIixpPVwiW1wiK24rXCJdXCIsdD1uZXcgUmVnRXhwKFwiPCEtLWJsb2NrLS0+XCIsXCJnXCIpLGUuZXh0ZW5kKHtzZXJpYWxpemVyczp7XCJhcHBsaWNhdGlvbi9qc29uXCI6ZnVuY3Rpb24odCl7dmFyIG47aWYodCBpbnN0YW5jZW9mIGUuRG9jdW1lbnQpbj10O2Vsc2V7aWYoISh0IGluc3RhbmNlb2YgSFRNTEVsZW1lbnQpKXRocm93IG5ldyBFcnJvcihcInVuc2VyaWFsaXphYmxlIG9iamVjdFwiKTtuPWUuRG9jdW1lbnQuZnJvbUhUTUwodC5pbm5lckhUTUwpfXJldHVybiBuLnRvU2VyaWFsaXphYmxlRG9jdW1lbnQoKS50b0pTT05TdHJpbmcoKX0sXCJ0ZXh0L2h0bWxcIjpmdW5jdGlvbihzKXt2YXIgYSx1LGMsbCxoLHAsZCxmLGcsbSx2LHksYixBLEMseCx3O2lmKHMgaW5zdGFuY2VvZiBlLkRvY3VtZW50KWw9ZS5Eb2N1bWVudFZpZXcucmVuZGVyKHMpO2Vsc2V7aWYoIShzIGluc3RhbmNlb2YgSFRNTEVsZW1lbnQpKXRocm93IG5ldyBFcnJvcihcInVuc2VyaWFsaXphYmxlIG9iamVjdFwiKTtsPXMuY2xvbmVOb2RlKCEwKX1mb3IoQT1sLnF1ZXJ5U2VsZWN0b3JBbGwociksaD0wLGc9QS5sZW5ndGg7Zz5oO2grKyljPUFbaF0sZS5yZW1vdmVOb2RlKGMpO2ZvcihwPTAsbT1vLmxlbmd0aDttPnA7cCsrKWZvcihhPW9bcF0sQz1sLnF1ZXJ5U2VsZWN0b3JBbGwoXCJbXCIrYStcIl1cIiksZD0wLHY9Qy5sZW5ndGg7dj5kO2QrKyljPUNbZF0sYy5yZW1vdmVBdHRyaWJ1dGUoYSk7Zm9yKHg9bC5xdWVyeVNlbGVjdG9yQWxsKGkpLGY9MCx5PXgubGVuZ3RoO3k+ZjtmKyspe2M9eFtmXTt0cnl7dT1KU09OLnBhcnNlKGMuZ2V0QXR0cmlidXRlKG4pKSxjLnJlbW92ZUF0dHJpYnV0ZShuKTtmb3IoYiBpbiB1KXc9dVtiXSxjLnNldEF0dHJpYnV0ZShiLHcpfWNhdGNoKEUpe319cmV0dXJuIGwuaW5uZXJIVE1MLnJlcGxhY2UodCxcIlwiKX19LGRlc2VyaWFsaXplcnM6e1wiYXBwbGljYXRpb24vanNvblwiOmZ1bmN0aW9uKHQpe3JldHVybiBlLkRvY3VtZW50LmZyb21KU09OU3RyaW5nKHQpfSxcInRleHQvaHRtbFwiOmZ1bmN0aW9uKHQpe3JldHVybiBlLkRvY3VtZW50LmZyb21IVE1MKHQpfX0sc2VyaWFsaXplVG9Db250ZW50VHlwZTpmdW5jdGlvbih0LG4pe3ZhciBpO2lmKGk9ZS5zZXJpYWxpemVyc1tuXSlyZXR1cm4gaSh0KTt0aHJvdyBuZXcgRXJyb3IoXCJ1bmtub3duIGNvbnRlbnQgdHlwZTogXCIrbil9LGRlc2VyaWFsaXplRnJvbUNvbnRlbnRUeXBlOmZ1bmN0aW9uKHQsbil7dmFyIGk7aWYoaT1lLmRlc2VyaWFsaXplcnNbbl0pcmV0dXJuIGkodCk7dGhyb3cgbmV3IEVycm9yKFwidW5rbm93biBjb250ZW50IHR5cGU6IFwiK24pfX0pfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ7dD1lLmNvbmZpZy5sYW5nLGUuY29uZmlnLnRvb2xiYXI9e2dldERlZmF1bHRIVE1MOmZ1bmN0aW9uKCl7cmV0dXJuJzxkaXYgY2xhc3M9XCJ0cml4LWJ1dHRvbi1yb3dcIj5cXG4gIDxzcGFuIGNsYXNzPVwidHJpeC1idXR0b24tZ3JvdXAgdHJpeC1idXR0b24tZ3JvdXAtLXRleHQtdG9vbHNcIiBkYXRhLXRyaXgtYnV0dG9uLWdyb3VwPVwidGV4dC10b29sc1wiPlxcbiAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cInRyaXgtYnV0dG9uIHRyaXgtYnV0dG9uLS1pY29uIHRyaXgtYnV0dG9uLS1pY29uLWJvbGRcIiBkYXRhLXRyaXgtYXR0cmlidXRlPVwiYm9sZFwiIGRhdGEtdHJpeC1rZXk9XCJiXCIgdGl0bGU9XCInK3QuYm9sZCsnXCIgdGFiaW5kZXg9XCItMVwiPicrdC5ib2xkKyc8L2J1dHRvbj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1pdGFsaWNcIiBkYXRhLXRyaXgtYXR0cmlidXRlPVwiaXRhbGljXCIgZGF0YS10cml4LWtleT1cImlcIiB0aXRsZT1cIicrdC5pdGFsaWMrJ1wiIHRhYmluZGV4PVwiLTFcIj4nK3QuaXRhbGljKyc8L2J1dHRvbj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1zdHJpa2VcIiBkYXRhLXRyaXgtYXR0cmlidXRlPVwic3RyaWtlXCIgdGl0bGU9XCInK3Quc3RyaWtlKydcIiB0YWJpbmRleD1cIi0xXCI+Jyt0LnN0cmlrZSsnPC9idXR0b24+XFxuICAgIDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwidHJpeC1idXR0b24gdHJpeC1idXR0b24tLWljb24gdHJpeC1idXR0b24tLWljb24tbGlua1wiIGRhdGEtdHJpeC1hdHRyaWJ1dGU9XCJocmVmXCIgZGF0YS10cml4LWFjdGlvbj1cImxpbmtcIiBkYXRhLXRyaXgta2V5PVwia1wiIHRpdGxlPVwiJyt0LmxpbmsrJ1wiIHRhYmluZGV4PVwiLTFcIj4nK3QubGluaysnPC9idXR0b24+XFxuICA8L3NwYW4+XFxuXFxuICA8c3BhbiBjbGFzcz1cInRyaXgtYnV0dG9uLWdyb3VwIHRyaXgtYnV0dG9uLWdyb3VwLS1ibG9jay10b29sc1wiIGRhdGEtdHJpeC1idXR0b24tZ3JvdXA9XCJibG9jay10b29sc1wiPlxcbiAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cInRyaXgtYnV0dG9uIHRyaXgtYnV0dG9uLS1pY29uIHRyaXgtYnV0dG9uLS1pY29uLWhlYWRpbmctMVwiIGRhdGEtdHJpeC1hdHRyaWJ1dGU9XCJoZWFkaW5nMVwiIHRpdGxlPVwiJyt0LmhlYWRpbmcxKydcIiB0YWJpbmRleD1cIi0xXCI+Jyt0LmhlYWRpbmcxKyc8L2J1dHRvbj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1xdW90ZVwiIGRhdGEtdHJpeC1hdHRyaWJ1dGU9XCJxdW90ZVwiIHRpdGxlPVwiJyt0LnF1b3RlKydcIiB0YWJpbmRleD1cIi0xXCI+Jyt0LnF1b3RlKyc8L2J1dHRvbj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1jb2RlXCIgZGF0YS10cml4LWF0dHJpYnV0ZT1cImNvZGVcIiB0aXRsZT1cIicrdC5jb2RlKydcIiB0YWJpbmRleD1cIi0xXCI+Jyt0LmNvZGUrJzwvYnV0dG9uPlxcbiAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cInRyaXgtYnV0dG9uIHRyaXgtYnV0dG9uLS1pY29uIHRyaXgtYnV0dG9uLS1pY29uLWJ1bGxldC1saXN0XCIgZGF0YS10cml4LWF0dHJpYnV0ZT1cImJ1bGxldFwiIHRpdGxlPVwiJyt0LmJ1bGxldHMrJ1wiIHRhYmluZGV4PVwiLTFcIj4nK3QuYnVsbGV0cysnPC9idXR0b24+XFxuICAgIDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwidHJpeC1idXR0b24gdHJpeC1idXR0b24tLWljb24gdHJpeC1idXR0b24tLWljb24tbnVtYmVyLWxpc3RcIiBkYXRhLXRyaXgtYXR0cmlidXRlPVwibnVtYmVyXCIgdGl0bGU9XCInK3QubnVtYmVycysnXCIgdGFiaW5kZXg9XCItMVwiPicrdC5udW1iZXJzKyc8L2J1dHRvbj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1kZWNyZWFzZS1uZXN0aW5nLWxldmVsXCIgZGF0YS10cml4LWFjdGlvbj1cImRlY3JlYXNlTmVzdGluZ0xldmVsXCIgdGl0bGU9XCInK3Qub3V0ZGVudCsnXCIgdGFiaW5kZXg9XCItMVwiPicrdC5vdXRkZW50Kyc8L2J1dHRvbj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1pbmNyZWFzZS1uZXN0aW5nLWxldmVsXCIgZGF0YS10cml4LWFjdGlvbj1cImluY3JlYXNlTmVzdGluZ0xldmVsXCIgdGl0bGU9XCInK3QuaW5kZW50KydcIiB0YWJpbmRleD1cIi0xXCI+Jyt0LmluZGVudCsnPC9idXR0b24+XFxuICA8L3NwYW4+XFxuXFxuICA8c3BhbiBjbGFzcz1cInRyaXgtYnV0dG9uLWdyb3VwIHRyaXgtYnV0dG9uLWdyb3VwLS1maWxlLXRvb2xzXCIgZGF0YS10cml4LWJ1dHRvbi1ncm91cD1cImZpbGUtdG9vbHNcIj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1hdHRhY2hcIiBkYXRhLXRyaXgtYWN0aW9uPVwiYXR0YWNoRmlsZXNcIiB0aXRsZT1cIicrdC5hdHRhY2hGaWxlcysnXCIgdGFiaW5kZXg9XCItMVwiPicrdC5hdHRhY2hGaWxlcysnPC9idXR0b24+XFxuICA8L3NwYW4+XFxuXFxuICA8c3BhbiBjbGFzcz1cInRyaXgtYnV0dG9uLWdyb3VwLXNwYWNlclwiPjwvc3Bhbj5cXG5cXG4gIDxzcGFuIGNsYXNzPVwidHJpeC1idXR0b24tZ3JvdXAgdHJpeC1idXR0b24tZ3JvdXAtLWhpc3RvcnktdG9vbHNcIiBkYXRhLXRyaXgtYnV0dG9uLWdyb3VwPVwiaGlzdG9yeS10b29sc1wiPlxcbiAgICA8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cInRyaXgtYnV0dG9uIHRyaXgtYnV0dG9uLS1pY29uIHRyaXgtYnV0dG9uLS1pY29uLXVuZG9cIiBkYXRhLXRyaXgtYWN0aW9uPVwidW5kb1wiIGRhdGEtdHJpeC1rZXk9XCJ6XCIgdGl0bGU9XCInK3QudW5kbysnXCIgdGFiaW5kZXg9XCItMVwiPicrdC51bmRvKyc8L2J1dHRvbj5cXG4gICAgPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0taWNvbiB0cml4LWJ1dHRvbi0taWNvbi1yZWRvXCIgZGF0YS10cml4LWFjdGlvbj1cInJlZG9cIiBkYXRhLXRyaXgta2V5PVwic2hpZnQrelwiIHRpdGxlPVwiJyt0LnJlZG8rJ1wiIHRhYmluZGV4PVwiLTFcIj4nK3QucmVkbysnPC9idXR0b24+XFxuICA8L3NwYW4+XFxuPC9kaXY+XFxuXFxuPGRpdiBjbGFzcz1cInRyaXgtZGlhbG9nc1wiIGRhdGEtdHJpeC1kaWFsb2dzPlxcbiAgPGRpdiBjbGFzcz1cInRyaXgtZGlhbG9nIHRyaXgtZGlhbG9nLS1saW5rXCIgZGF0YS10cml4LWRpYWxvZz1cImhyZWZcIiBkYXRhLXRyaXgtZGlhbG9nLWF0dHJpYnV0ZT1cImhyZWZcIj5cXG4gICAgPGRpdiBjbGFzcz1cInRyaXgtZGlhbG9nX19saW5rLWZpZWxkc1wiPlxcbiAgICAgIDxpbnB1dCB0eXBlPVwidXJsXCIgbmFtZT1cImhyZWZcIiBjbGFzcz1cInRyaXgtaW5wdXQgdHJpeC1pbnB1dC0tZGlhbG9nXCIgcGxhY2Vob2xkZXI9XCInK3QudXJsUGxhY2Vob2xkZXIrJ1wiIGFyaWEtbGFiZWw9XCInK3QudXJsKydcIiByZXF1aXJlZCBkYXRhLXRyaXgtaW5wdXQ+XFxuICAgICAgPGRpdiBjbGFzcz1cInRyaXgtYnV0dG9uLWdyb3VwXCI+XFxuICAgICAgICA8aW5wdXQgdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwidHJpeC1idXR0b24gdHJpeC1idXR0b24tLWRpYWxvZ1wiIHZhbHVlPVwiJyt0LmxpbmsrJ1wiIGRhdGEtdHJpeC1tZXRob2Q9XCJzZXRBdHRyaWJ1dGVcIj5cXG4gICAgICAgIDxpbnB1dCB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJ0cml4LWJ1dHRvbiB0cml4LWJ1dHRvbi0tZGlhbG9nXCIgdmFsdWU9XCInK3QudW5saW5rKydcIiBkYXRhLXRyaXgtbWV0aG9kPVwicmVtb3ZlQXR0cmlidXRlXCI+XFxuICAgICAgPC9kaXY+XFxuICAgIDwvZGl2PlxcbiAgPC9kaXY+XFxuPC9kaXY+J319fS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7ZS5jb25maWcudW5kb0ludGVydmFsPTVlM30uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuY29uZmlnLmF0dGFjaG1lbnRzPXtwcmV2aWV3OntwcmVzZW50YXRpb246XCJnYWxsZXJ5XCIsY2FwdGlvbjp7bmFtZTohMCxzaXplOiEwfX0sZmlsZTp7Y2FwdGlvbjp7c2l6ZTohMH19fX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe2UuY29uZmlnLmtleU5hbWVzPXs4OlwiYmFja3NwYWNlXCIsOTpcInRhYlwiLDEzOlwicmV0dXJuXCIsMjc6XCJlc2NhcGVcIiwzNzpcImxlZnRcIiwzOTpcInJpZ2h0XCIsNDY6XCJkZWxldGVcIiw2ODpcImRcIiw3MjpcImhcIiw3OTpcIm9cIn19LmNhbGwodGhpcyksZnVuY3Rpb24oKXtlLmNvbmZpZy5pbnB1dD17bGV2ZWwyRW5hYmxlZDohMCxnZXRMZXZlbDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmxldmVsMkVuYWJsZWQmJmUuYnJvd3Nlci5zdXBwb3J0c0lucHV0RXZlbnRzPzI6MH0scGlja0ZpbGVzOmZ1bmN0aW9uKHQpe3ZhciBuO3JldHVybiBuPWUubWFrZUVsZW1lbnQoXCJpbnB1dFwiLHt0eXBlOlwiZmlsZVwiLG11bHRpcGxlOiEwLGhpZGRlbjohMCxpZDp0aGlzLmZpbGVJbnB1dElkfSksbi5hZGRFdmVudExpc3RlbmVyKFwiY2hhbmdlXCIsZnVuY3Rpb24oKXtyZXR1cm4gdChuLmZpbGVzKSxlLnJlbW92ZU5vZGUobil9KSxlLnJlbW92ZU5vZGUoZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQodGhpcy5maWxlSW5wdXRJZCkpLGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQobiksbi5jbGljaygpfSxmaWxlSW5wdXRJZDpcInRyaXgtZmlsZS1pbnB1dC1cIitEYXRlLm5vdygpLnRvU3RyaW5nKDE2KX19LmNhbGwodGhpcyksZnVuY3Rpb24oKXt9LmNhbGwodGhpcyksZnVuY3Rpb24oKXtlLnJlZ2lzdGVyRWxlbWVudChcInRyaXgtdG9vbGJhclwiLHtkZWZhdWx0Q1NTOlwiJXQge1xcbiAgZGlzcGxheTogYmxvY2s7XFxufVxcblxcbiV0IHtcXG4gIHdoaXRlLXNwYWNlOiBub3dyYXA7XFxufVxcblxcbiV0IFtkYXRhLXRyaXgtZGlhbG9nXSB7XFxuICBkaXNwbGF5OiBub25lO1xcbn1cXG5cXG4ldCBbZGF0YS10cml4LWRpYWxvZ11bZGF0YS10cml4LWFjdGl2ZV0ge1xcbiAgZGlzcGxheTogYmxvY2s7XFxufVxcblxcbiV0IFtkYXRhLXRyaXgtZGlhbG9nXSBbZGF0YS10cml4LXZhbGlkYXRlXTppbnZhbGlkIHtcXG4gIGJhY2tncm91bmQtY29sb3I6ICNmZmRkZGQ7XFxufVwiLGluaXRpYWxpemU6ZnVuY3Rpb24oKXtyZXR1cm5cIlwiPT09dGhpcy5pbm5lckhUTUw/dGhpcy5pbm5lckhUTUw9ZS5jb25maWcudG9vbGJhci5nZXREZWZhdWx0SFRNTCgpOnZvaWQgMH19KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gaSgpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSluLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIGkucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBpLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG49e30uaGFzT3duUHJvcGVydHksaT1bXS5pbmRleE9mfHxmdW5jdGlvbih0KXtmb3IodmFyIGU9MCxuPXRoaXMubGVuZ3RoO24+ZTtlKyspaWYoZSBpbiB0aGlzJiZ0aGlzW2VdPT09dClyZXR1cm4gZTtyZXR1cm4tMX07ZS5PYmplY3RWaWV3PWZ1bmN0aW9uKG4pe2Z1bmN0aW9uIG8odCxlKXt0aGlzLm9iamVjdD10LHRoaXMub3B0aW9ucz1udWxsIT1lP2U6e30sdGhpcy5jaGlsZFZpZXdzPVtdLHRoaXMucm9vdFZpZXc9dGhpc31yZXR1cm4gdChvLG4pLG8ucHJvdG90eXBlLmdldE5vZGVzPWZ1bmN0aW9uKCl7dmFyIHQsZSxuLGksbztmb3IobnVsbD09dGhpcy5ub2RlcyYmKHRoaXMubm9kZXM9dGhpcy5jcmVhdGVOb2RlcygpKSxpPXRoaXMubm9kZXMsbz1bXSx0PTAsZT1pLmxlbmd0aDtlPnQ7dCsrKW49aVt0XSxvLnB1c2gobi5jbG9uZU5vZGUoITApKTtyZXR1cm4gb30sby5wcm90b3R5cGUuaW52YWxpZGF0ZT1mdW5jdGlvbigpe3ZhciB0O3JldHVybiB0aGlzLm5vZGVzPW51bGwsdGhpcy5jaGlsZFZpZXdzPVtdLG51bGwhPSh0PXRoaXMucGFyZW50Vmlldyk/dC5pbnZhbGlkYXRlKCk6dm9pZCAwfSxvLnByb3RvdHlwZS5pbnZhbGlkYXRlVmlld0Zvck9iamVjdD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gbnVsbCE9KGU9dGhpcy5maW5kVmlld0Zvck9iamVjdCh0KSk/ZS5pbnZhbGlkYXRlKCk6dm9pZCAwfSxvLnByb3RvdHlwZS5maW5kT3JDcmVhdGVDYWNoZWRDaGlsZFZpZXc9ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4obj10aGlzLmdldENhY2hlZFZpZXdGb3JPYmplY3QoZSkpP3RoaXMucmVjb3JkQ2hpbGRWaWV3KG4pOihuPXRoaXMuY3JlYXRlQ2hpbGRWaWV3LmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLmNhY2hlVmlld0Zvck9iamVjdChuLGUpKSxufSxvLnByb3RvdHlwZS5jcmVhdGVDaGlsZFZpZXc9ZnVuY3Rpb24odCxuLGkpe3ZhciBvO3JldHVybiBudWxsPT1pJiYoaT17fSksbiBpbnN0YW5jZW9mIGUuT2JqZWN0R3JvdXAmJihpLnZpZXdDbGFzcz10LHQ9ZS5PYmplY3RHcm91cFZpZXcpLG89bmV3IHQobixpKSx0aGlzLnJlY29yZENoaWxkVmlldyhvKX0sby5wcm90b3R5cGUucmVjb3JkQ2hpbGRWaWV3PWZ1bmN0aW9uKHQpe3JldHVybiB0LnBhcmVudFZpZXc9dGhpcyx0LnJvb3RWaWV3PXRoaXMucm9vdFZpZXcsdGhpcy5jaGlsZFZpZXdzLnB1c2godCksdH0sby5wcm90b3R5cGUuZ2V0QWxsQ2hpbGRWaWV3cz1mdW5jdGlvbigpe3ZhciB0LGUsbixpLG87Zm9yKG89W10saT10aGlzLmNoaWxkVmlld3MsZT0wLG49aS5sZW5ndGg7bj5lO2UrKyl0PWlbZV0sby5wdXNoKHQpLG89by5jb25jYXQodC5nZXRBbGxDaGlsZFZpZXdzKCkpO3JldHVybiBvfSxvLnByb3RvdHlwZS5maW5kRWxlbWVudD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmZpbmRFbGVtZW50Rm9yT2JqZWN0KHRoaXMub2JqZWN0KX0sby5wcm90b3R5cGUuZmluZEVsZW1lbnRGb3JPYmplY3Q9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuKGU9bnVsbCE9dD90LmlkOnZvaWQgMCk/dGhpcy5yb290Vmlldy5lbGVtZW50LnF1ZXJ5U2VsZWN0b3IoXCJbZGF0YS10cml4LWlkPSdcIitlK1wiJ11cIik6dm9pZCAwfSxvLnByb3RvdHlwZS5maW5kVmlld0Zvck9iamVjdD1mdW5jdGlvbih0KXt2YXIgZSxuLGksbztmb3IoaT10aGlzLmdldEFsbENoaWxkVmlld3MoKSxlPTAsbj1pLmxlbmd0aDtuPmU7ZSsrKWlmKG89aVtlXSxvLm9iamVjdD09PXQpcmV0dXJuIG99LG8ucHJvdG90eXBlLmdldFZpZXdDYWNoZT1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJvb3RWaWV3IT09dGhpcz90aGlzLnJvb3RWaWV3LmdldFZpZXdDYWNoZSgpOnRoaXMuaXNWaWV3Q2FjaGluZ0VuYWJsZWQoKT9udWxsIT10aGlzLnZpZXdDYWNoZT90aGlzLnZpZXdDYWNoZTp0aGlzLnZpZXdDYWNoZT17fTp2b2lkIDB9LG8ucHJvdG90eXBlLmlzVmlld0NhY2hpbmdFbmFibGVkPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuc2hvdWxkQ2FjaGVWaWV3cyE9PSExfSxvLnByb3RvdHlwZS5lbmFibGVWaWV3Q2FjaGluZz1mdW5jdGlvbigpe3JldHVybiB0aGlzLnNob3VsZENhY2hlVmlld3M9ITB9LG8ucHJvdG90eXBlLmRpc2FibGVWaWV3Q2FjaGluZz1mdW5jdGlvbigpe3JldHVybiB0aGlzLnNob3VsZENhY2hlVmlld3M9ITF9LG8ucHJvdG90eXBlLmdldENhY2hlZFZpZXdGb3JPYmplY3Q9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMuZ2V0Vmlld0NhY2hlKCkpP2VbdC5nZXRDYWNoZUtleSgpXTp2b2lkIDB9LG8ucHJvdG90eXBlLmNhY2hlVmlld0Zvck9iamVjdD1mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybiBudWxsIT0obj10aGlzLmdldFZpZXdDYWNoZSgpKT9uW2UuZ2V0Q2FjaGVLZXkoKV09dDp2b2lkIDB9LG8ucHJvdG90eXBlLmdhcmJhZ2VDb2xsZWN0Q2FjaGVkVmlld3M9ZnVuY3Rpb24oKXt2YXIgdCxlLG4sbyxyLHM7aWYodD10aGlzLmdldFZpZXdDYWNoZSgpKXtzPXRoaXMuZ2V0QWxsQ2hpbGRWaWV3cygpLmNvbmNhdCh0aGlzKSxuPWZ1bmN0aW9uKCl7dmFyIHQsZSxuO2ZvcihuPVtdLHQ9MCxlPXMubGVuZ3RoO2U+dDt0Kyspcj1zW3RdLG4ucHVzaChyLm9iamVjdC5nZXRDYWNoZUtleSgpKTtyZXR1cm4gbn0oKSxvPVtdO2ZvcihlIGluIHQpaS5jYWxsKG4sZSk8MCYmby5wdXNoKGRlbGV0ZSB0W2VdKTtyZXR1cm4gb319LG99KGUuQmFzaWNPYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBpKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgbyBpbiBlKW4uY2FsbChlLG8pJiYodFtvXT1lW29dKTtyZXR1cm4gaS5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IGksdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sbj17fS5oYXNPd25Qcm9wZXJ0eTtlLk9iamVjdEdyb3VwVmlldz1mdW5jdGlvbihlKXtmdW5jdGlvbiBuKCl7bi5fX3N1cGVyX18uY29uc3RydWN0b3IuYXBwbHkodGhpcyxhcmd1bWVudHMpLHRoaXMub2JqZWN0R3JvdXA9dGhpcy5vYmplY3QsdGhpcy52aWV3Q2xhc3M9dGhpcy5vcHRpb25zLnZpZXdDbGFzcyxkZWxldGUgdGhpcy5vcHRpb25zLnZpZXdDbGFzc31yZXR1cm4gdChuLGUpLG4ucHJvdG90eXBlLmdldENoaWxkVmlld3M9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saTtpZighdGhpcy5jaGlsZFZpZXdzLmxlbmd0aClmb3IoaT10aGlzLm9iamVjdEdyb3VwLmdldE9iamVjdHMoKSx0PTAsZT1pLmxlbmd0aDtlPnQ7dCsrKW49aVt0XSx0aGlzLmZpbmRPckNyZWF0ZUNhY2hlZENoaWxkVmlldyh0aGlzLnZpZXdDbGFzcyxuLHRoaXMub3B0aW9ucyk7cmV0dXJuIHRoaXMuY2hpbGRWaWV3c30sbi5wcm90b3R5cGUuY3JlYXRlTm9kZXM9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvLHIscyxhLHU7Zm9yKHQ9dGhpcy5jcmVhdGVDb250YWluZXJFbGVtZW50KCkscz10aGlzLmdldENoaWxkVmlld3MoKSxlPTAsaT1zLmxlbmd0aDtpPmU7ZSsrKWZvcih1PXNbZV0sYT11LmdldE5vZGVzKCksbj0wLG89YS5sZW5ndGg7bz5uO24rKylyPWFbbl0sdC5hcHBlbmRDaGlsZChyKTtyZXR1cm5bdF19LG4ucHJvdG90eXBlLmNyZWF0ZUNvbnRhaW5lckVsZW1lbnQ9ZnVuY3Rpb24odCl7cmV0dXJuIG51bGw9PXQmJih0PXRoaXMub2JqZWN0R3JvdXAuZ2V0RGVwdGgoKSksdGhpcy5nZXRDaGlsZFZpZXdzKClbMF0uY3JlYXRlQ29udGFpbmVyRWxlbWVudCh0KX0sbn0oZS5PYmplY3RWaWV3KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gaSgpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSluLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIGkucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBpLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG49e30uaGFzT3duUHJvcGVydHk7ZS5Db250cm9sbGVyPWZ1bmN0aW9uKGUpe2Z1bmN0aW9uIG4oKXtyZXR1cm4gbi5fX3N1cGVyX18uY29uc3RydWN0b3IuYXBwbHkodGhpcyxhcmd1bWVudHMpfXJldHVybiB0KG4sZSksbn0oZS5CYXNpY09iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbyxyLHMsYT1mdW5jdGlvbih0LGUpe3JldHVybiBmdW5jdGlvbigpe3JldHVybiB0LmFwcGx5KGUsYXJndW1lbnRzKX19LHU9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBuKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgaSBpbiBlKWMuY2FsbChlLGkpJiYodFtpXT1lW2ldKTtyZXR1cm4gbi5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IG4sdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sYz17fS5oYXNPd25Qcm9wZXJ0eSxsPVtdLmluZGV4T2Z8fGZ1bmN0aW9uKHQpe2Zvcih2YXIgZT0wLG49dGhpcy5sZW5ndGg7bj5lO2UrKylpZihlIGluIHRoaXMmJnRoaXNbZV09PT10KXJldHVybiBlO3JldHVybi0xfTt0PWUuZmluZENsb3Nlc3RFbGVtZW50RnJvbU5vZGUsaT1lLm5vZGVJc0VtcHR5VGV4dE5vZGUsbj1lLm5vZGVJc0Jsb2NrU3RhcnRDb21tZW50LG89ZS5ub3JtYWxpemVTcGFjZXMscj1lLnN1bW1hcml6ZVN0cmluZ0NoYW5nZSxzPWUudGFnTmFtZSxlLk11dGF0aW9uT2JzZXJ2ZXI9ZnVuY3Rpb24oZSl7ZnVuY3Rpb24gYyh0KXt0aGlzLmVsZW1lbnQ9dCx0aGlzLmRpZE11dGF0ZT1hKHRoaXMuZGlkTXV0YXRlLHRoaXMpLHRoaXMub2JzZXJ2ZXI9bmV3IHdpbmRvdy5NdXRhdGlvbk9ic2VydmVyKHRoaXMuZGlkTXV0YXRlKSx0aGlzLnN0YXJ0KCl9dmFyIGgscCxkLGY7cmV0dXJuIHUoYyxlKSxwPVwiZGF0YS10cml4LW11dGFibGVcIixkPVwiW1wiK3ArXCJdXCIsZj17YXR0cmlidXRlczohMCxjaGlsZExpc3Q6ITAsY2hhcmFjdGVyRGF0YTohMCxjaGFyYWN0ZXJEYXRhT2xkVmFsdWU6ITAsc3VidHJlZTohMH0sYy5wcm90b3R5cGUuc3RhcnQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5yZXNldCgpLHRoaXMub2JzZXJ2ZXIub2JzZXJ2ZSh0aGlzLmVsZW1lbnQsZil9LGMucHJvdG90eXBlLnN0b3A9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5vYnNlcnZlci5kaXNjb25uZWN0KCl9LGMucHJvdG90eXBlLmRpZE11dGF0ZT1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybihlPXRoaXMubXV0YXRpb25zKS5wdXNoLmFwcGx5KGUsdGhpcy5maW5kU2lnbmlmaWNhbnRNdXRhdGlvbnModCkpLHRoaXMubXV0YXRpb25zLmxlbmd0aD8obnVsbCE9KG49dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIG4uZWxlbWVudERpZE11dGF0ZSYmbi5lbGVtZW50RGlkTXV0YXRlKHRoaXMuZ2V0TXV0YXRpb25TdW1tYXJ5KCkpLHRoaXMucmVzZXQoKSk6dm9pZCAwfSxjLnByb3RvdHlwZS5yZXNldD1mdW5jdGlvbigpe3JldHVybiB0aGlzLm11dGF0aW9ucz1bXX0sYy5wcm90b3R5cGUuZmluZFNpZ25pZmljYW50TXV0YXRpb25zPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvO2ZvcihvPVtdLGU9MCxuPXQubGVuZ3RoO24+ZTtlKyspaT10W2VdLHRoaXMubXV0YXRpb25Jc1NpZ25pZmljYW50KGkpJiZvLnB1c2goaSk7cmV0dXJuIG99LGMucHJvdG90eXBlLm11dGF0aW9uSXNTaWduaWZpY2FudD1mdW5jdGlvbih0KXt2YXIgZSxuLGksbztpZih0aGlzLm5vZGVJc011dGFibGUodC50YXJnZXQpKXJldHVybiExO2ZvcihvPXRoaXMubm9kZXNNb2RpZmllZEJ5TXV0YXRpb24odCksZT0wLG49by5sZW5ndGg7bj5lO2UrKylpZihpPW9bZV0sdGhpcy5ub2RlSXNTaWduaWZpY2FudChpKSlyZXR1cm4hMDtyZXR1cm4hMX0sYy5wcm90b3R5cGUubm9kZUlzU2lnbmlmaWNhbnQ9ZnVuY3Rpb24odCl7cmV0dXJuIHQhPT10aGlzLmVsZW1lbnQmJiF0aGlzLm5vZGVJc011dGFibGUodCkmJiFpKHQpfSxjLnByb3RvdHlwZS5ub2RlSXNNdXRhYmxlPWZ1bmN0aW9uKGUpe3JldHVybiB0KGUse21hdGNoaW5nU2VsZWN0b3I6ZH0pfSxjLnByb3RvdHlwZS5ub2Rlc01vZGlmaWVkQnlNdXRhdGlvbj1mdW5jdGlvbih0KXt2YXIgZTtzd2l0Y2goZT1bXSx0LnR5cGUpe2Nhc2VcImF0dHJpYnV0ZXNcIjp0LmF0dHJpYnV0ZU5hbWUhPT1wJiZlLnB1c2godC50YXJnZXQpO2JyZWFrO2Nhc2VcImNoYXJhY3RlckRhdGFcIjplLnB1c2godC50YXJnZXQucGFyZW50Tm9kZSksZS5wdXNoKHQudGFyZ2V0KTticmVhaztjYXNlXCJjaGlsZExpc3RcIjplLnB1c2guYXBwbHkoZSx0LmFkZGVkTm9kZXMpLGUucHVzaC5hcHBseShlLHQucmVtb3ZlZE5vZGVzKX1yZXR1cm4gZX0sYy5wcm90b3R5cGUuZ2V0TXV0YXRpb25TdW1tYXJ5PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZ2V0VGV4dE11dGF0aW9uU3VtbWFyeSgpfSxjLnByb3RvdHlwZS5nZXRUZXh0TXV0YXRpb25TdW1tYXJ5PWZ1bmN0aW9uKCl7dmFyIHQsZSxuLGksbyxyLHMsYSx1LGMsaDtmb3IoYT10aGlzLmdldFRleHRDaGFuZ2VzRnJvbUNoYXJhY3RlckRhdGEoKSxuPWEuYWRkaXRpb25zLG89YS5kZWxldGlvbnMsaD10aGlzLmdldFRleHRDaGFuZ2VzRnJvbUNoaWxkTGlzdCgpLHU9aC5hZGRpdGlvbnMscj0wLHM9dS5sZW5ndGg7cz5yO3IrKyllPXVbcl0sbC5jYWxsKG4sZSk8MCYmbi5wdXNoKGUpO3JldHVybiBvLnB1c2guYXBwbHkobyxoLmRlbGV0aW9ucyksYz17fSwodD1uLmpvaW4oXCJcIikpJiYoYy50ZXh0QWRkZWQ9dCksKGk9by5qb2luKFwiXCIpKSYmKGMudGV4dERlbGV0ZWQ9aSksY30sYy5wcm90b3R5cGUuZ2V0TXV0YXRpb25zQnlUeXBlPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHI7Zm9yKG89dGhpcy5tdXRhdGlvbnMscj1bXSxlPTAsbj1vLmxlbmd0aDtuPmU7ZSsrKWk9b1tlXSxpLnR5cGU9PT10JiZyLnB1c2goaSk7cmV0dXJuIHJ9LGMucHJvdG90eXBlLmdldFRleHRDaGFuZ2VzRnJvbUNoaWxkTGlzdD1mdW5jdGlvbigpe3ZhciB0LGUsaSxyLHMsYSx1LGMsbCxwLGQ7Zm9yKHQ9W10sdT1bXSxhPXRoaXMuZ2V0TXV0YXRpb25zQnlUeXBlKFwiY2hpbGRMaXN0XCIpLGU9MCxyPWEubGVuZ3RoO3I+ZTtlKyspcz1hW2VdLHQucHVzaC5hcHBseSh0LHMuYWRkZWROb2RlcyksdS5wdXNoLmFwcGx5KHUscy5yZW1vdmVkTm9kZXMpO3JldHVybiBjPTA9PT10Lmxlbmd0aCYmMT09PXUubGVuZ3RoJiZuKHVbMF0pLGM/KHA9W10sZD1bXCJcXG5cIl0pOihwPWgodCksZD1oKHUpKSx7YWRkaXRpb25zOmZ1bmN0aW9uKCl7dmFyIHQsZSxuO2ZvcihuPVtdLGk9dD0wLGU9cC5sZW5ndGg7ZT50O2k9Kyt0KWw9cFtpXSxsIT09ZFtpXSYmbi5wdXNoKG8obCkpO3JldHVybiBufSgpLGRlbGV0aW9uczpmdW5jdGlvbigpe3ZhciB0LGUsbjtmb3Iobj1bXSxpPXQ9MCxlPWQubGVuZ3RoO2U+dDtpPSsrdClsPWRbaV0sbCE9PXBbaV0mJm4ucHVzaChvKGwpKTtyZXR1cm4gbn0oKX19LGMucHJvdG90eXBlLmdldFRleHRDaGFuZ2VzRnJvbUNoYXJhY3RlckRhdGE9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxzLGEsdSxjO3JldHVybiBlPXRoaXMuZ2V0TXV0YXRpb25zQnlUeXBlKFwiY2hhcmFjdGVyRGF0YVwiKSxlLmxlbmd0aCYmKGM9ZVswXSxuPWVbZS5sZW5ndGgtMV0scz1vKGMub2xkVmFsdWUpLGk9byhuLnRhcmdldC5kYXRhKSxhPXIocyxpKSx0PWEuYWRkZWQsdT1hLnJlbW92ZWQpLHthZGRpdGlvbnM6dD9bdF06W10sZGVsZXRpb25zOnU/W3VdOltdfX0saD1mdW5jdGlvbih0KXt2YXIgZSxuLGksbztmb3IobnVsbD09dCYmKHQ9W10pLG89W10sZT0wLG49dC5sZW5ndGg7bj5lO2UrKylzd2l0Y2goaT10W2VdLGkubm9kZVR5cGUpe2Nhc2UgTm9kZS5URVhUX05PREU6by5wdXNoKGkuZGF0YSk7YnJlYWs7Y2FzZSBOb2RlLkVMRU1FTlRfTk9ERTpcImJyXCI9PT1zKGkpP28ucHVzaChcIlxcblwiKTpvLnB1c2guYXBwbHkobyxoKGkuY2hpbGROb2RlcykpfXJldHVybiBvfSxjfShlLkJhc2ljT2JqZWN0KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gaSgpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSluLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIGkucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBpLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG49e30uaGFzT3duUHJvcGVydHk7ZS5GaWxlVmVyaWZpY2F0aW9uT3BlcmF0aW9uPWZ1bmN0aW9uKGUpe2Z1bmN0aW9uIG4odCl7dGhpcy5maWxlPXR9cmV0dXJuIHQobixlKSxuLnByb3RvdHlwZS5wZXJmb3JtPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBlPW5ldyBGaWxlUmVhZGVyLGUub25lcnJvcj1mdW5jdGlvbigpe3JldHVybiB0KCExKX0sZS5vbmxvYWQ9ZnVuY3Rpb24obil7cmV0dXJuIGZ1bmN0aW9uKCl7ZS5vbmVycm9yPW51bGw7dHJ5e2UuYWJvcnQoKX1jYXRjaChpKXt9cmV0dXJuIHQoITAsbi5maWxlKX19KHRoaXMpLGUucmVhZEFzQXJyYXlCdWZmZXIodGhpcy5maWxlKX0sbn0oZS5PcGVyYXRpb24pfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIGkgaW4gZSlvLmNhbGwoZSxpKSYmKHRbaV09ZVtpXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG89e30uaGFzT3duUHJvcGVydHk7dD1lLmhhbmRsZUV2ZW50LG49ZS5pbm5lckVsZW1lbnRJc0FjdGl2ZSxlLklucHV0Q29udHJvbGxlcj1mdW5jdGlvbihvKXtmdW5jdGlvbiByKG4pe3ZhciBpO3RoaXMuZWxlbWVudD1uLHRoaXMubXV0YXRpb25PYnNlcnZlcj1uZXcgZS5NdXRhdGlvbk9ic2VydmVyKHRoaXMuZWxlbWVudCksdGhpcy5tdXRhdGlvbk9ic2VydmVyLmRlbGVnYXRlPXRoaXM7Zm9yKGkgaW4gdGhpcy5ldmVudHMpdChpLHtvbkVsZW1lbnQ6dGhpcy5lbGVtZW50LHdpdGhDYWxsYmFjazp0aGlzLmhhbmRsZXJGb3IoaSl9KX1yZXR1cm4gaShyLG8pLHIucHJvdG90eXBlLmV2ZW50cz17fSxyLnByb3RvdHlwZS5lbGVtZW50RGlkTXV0YXRlPWZ1bmN0aW9uKCl7fSxyLnByb3RvdHlwZS5lZGl0b3JXaWxsU3luY0RvY3VtZW50Vmlldz1mdW5jdGlvbigpe3JldHVybiB0aGlzLm11dGF0aW9uT2JzZXJ2ZXIuc3RvcCgpfSxyLnByb3RvdHlwZS5lZGl0b3JEaWRTeW5jRG9jdW1lbnRWaWV3PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMubXV0YXRpb25PYnNlcnZlci5zdGFydCgpfSxyLnByb3RvdHlwZS5yZXF1ZXN0UmVuZGVyPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiB0LmlucHV0Q29udHJvbGxlckRpZFJlcXVlc3RSZW5kZXI/dC5pbnB1dENvbnRyb2xsZXJEaWRSZXF1ZXN0UmVuZGVyKCk6dm9pZCAwfSxyLnByb3RvdHlwZS5yZXF1ZXN0UmVwYXJzZT1mdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgdC5pbnB1dENvbnRyb2xsZXJEaWRSZXF1ZXN0UmVwYXJzZSYmdC5pbnB1dENvbnRyb2xsZXJEaWRSZXF1ZXN0UmVwYXJzZSgpLHRoaXMucmVxdWVzdFJlbmRlcigpfSxyLnByb3RvdHlwZS5hdHRhY2hGaWxlcz1mdW5jdGlvbih0KXt2YXIgbixpO3JldHVybiBpPWZ1bmN0aW9uKCl7dmFyIGksbyxyO2ZvcihyPVtdLGk9MCxvPXQubGVuZ3RoO28+aTtpKyspbj10W2ldLHIucHVzaChuZXcgZS5GaWxlVmVyaWZpY2F0aW9uT3BlcmF0aW9uKG4pKTtyZXR1cm4gcn0oKSxQcm9taXNlLmFsbChpKS50aGVuKGZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbihlKXtyZXR1cm4gdC5oYW5kbGVJbnB1dChmdW5jdGlvbigpe3ZhciB0LG47cmV0dXJuIG51bGwhPSh0PXRoaXMuZGVsZWdhdGUpJiZ0LmlucHV0Q29udHJvbGxlcldpbGxBdHRhY2hGaWxlcygpLG51bGwhPShuPXRoaXMucmVzcG9uZGVyKSYmbi5pbnNlcnRGaWxlcyhlKSx0aGlzLnJlcXVlc3RSZW5kZXIoKX0pfX0odGhpcykpfSxyLnByb3RvdHlwZS5oYW5kbGVyRm9yPWZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbihlKXtyZXR1cm4gZnVuY3Rpb24oaSl7cmV0dXJuIGkuZGVmYXVsdFByZXZlbnRlZD92b2lkIDA6ZS5oYW5kbGVJbnB1dChmdW5jdGlvbigpe3JldHVybiBuKHRoaXMuZWxlbWVudCk/dm9pZCAwOih0aGlzLmV2ZW50TmFtZT10LHRoaXMuZXZlbnRzW3RdLmNhbGwodGhpcyxpKSl9KX19KHRoaXMpfSxyLnByb3RvdHlwZS5oYW5kbGVJbnB1dD1mdW5jdGlvbih0KXt2YXIgZSxuO3RyeXtyZXR1cm4gbnVsbCE9KGU9dGhpcy5kZWxlZ2F0ZSkmJmUuaW5wdXRDb250cm9sbGVyV2lsbEhhbmRsZUlucHV0KCksdC5jYWxsKHRoaXMpfWZpbmFsbHl7bnVsbCE9KG49dGhpcy5kZWxlZ2F0ZSkmJm4uaW5wdXRDb250cm9sbGVyRGlkSGFuZGxlSW5wdXQoKX19LHIucHJvdG90eXBlLmNyZWF0ZUxpbmtIVE1MPWZ1bmN0aW9uKHQsZSl7dmFyIG47cmV0dXJuIG49ZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcImFcIiksbi5ocmVmPXQsbi50ZXh0Q29udGVudD1udWxsIT1lP2U6dCxuLm91dGVySFRNTH0scn0oZS5CYXNpY09iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbyxyLHMsYSx1LGMsbCxoLHAsZCxmPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIGkgaW4gZSlnLmNhbGwoZSxpKSYmKHRbaV09ZVtpXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LGc9e30uaGFzT3duUHJvcGVydHksbT1bXS5pbmRleE9mfHxmdW5jdGlvbih0KXtmb3IodmFyIGU9MCxuPXRoaXMubGVuZ3RoO24+ZTtlKyspaWYoZSBpbiB0aGlzJiZ0aGlzW2VdPT09dClyZXR1cm4gZTtyZXR1cm4tMX07Yz1lLm1ha2VFbGVtZW50LGw9ZS5vYmplY3RzQXJlRXF1YWwsZD1lLnRhZ05hbWUsbj1lLmJyb3dzZXIsYT1lLmtleUV2ZW50SXNLZXlib2FyZENvbW1hbmQsbz1lLmRhdGFUcmFuc2ZlcklzV3JpdGFibGUsaT1lLmRhdGFUcmFuc2ZlcklzUGxhaW5UZXh0LHU9ZS5jb25maWcua2V5TmFtZXMsZS5MZXZlbDBJbnB1dENvbnRyb2xsZXI9ZnVuY3Rpb24obil7ZnVuY3Rpb24gcygpe3MuX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLnJlc2V0SW5wdXRTdW1tYXJ5KCl9dmFyIGQ7cmV0dXJuIGYocyxuKSxkPTAscy5wcm90b3R5cGUuc2V0SW5wdXRTdW1tYXJ5PWZ1bmN0aW9uKHQpe3ZhciBlLG47bnVsbD09dCYmKHQ9e30pLHRoaXMuaW5wdXRTdW1tYXJ5LmV2ZW50TmFtZT10aGlzLmV2ZW50TmFtZTtmb3IoZSBpbiB0KW49dFtlXSx0aGlzLmlucHV0U3VtbWFyeVtlXT1uO3JldHVybiB0aGlzLmlucHV0U3VtbWFyeX0scy5wcm90b3R5cGUucmVzZXRJbnB1dFN1bW1hcnk9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pbnB1dFN1bW1hcnk9e319LHMucHJvdG90eXBlLnJlc2V0PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMucmVzZXRJbnB1dFN1bW1hcnkoKSxlLnNlbGVjdGlvbkNoYW5nZU9ic2VydmVyLnJlc2V0KCl9LHMucHJvdG90eXBlLmVsZW1lbnREaWRNdXRhdGU9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHRoaXMuaXNDb21wb3NpbmcoKT9udWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgZS5pbnB1dENvbnRyb2xsZXJEaWRBbGxvd1VuaGFuZGxlZElucHV0P2UuaW5wdXRDb250cm9sbGVyRGlkQWxsb3dVbmhhbmRsZWRJbnB1dCgpOnZvaWQgMDp0aGlzLmhhbmRsZUlucHV0KGZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMubXV0YXRpb25Jc1NpZ25pZmljYW50KHQpJiYodGhpcy5tdXRhdGlvbklzRXhwZWN0ZWQodCk/dGhpcy5yZXF1ZXN0UmVuZGVyKCk6dGhpcy5yZXF1ZXN0UmVwYXJzZSgpKSx0aGlzLnJlc2V0KCl9KX0scy5wcm90b3R5cGUubXV0YXRpb25Jc0V4cGVjdGVkPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHIscyxhLHUsYyxsO3JldHVybiBhPXQudGV4dEFkZGVkLHU9dC50ZXh0RGVsZXRlZCx0aGlzLmlucHV0U3VtbWFyeS5wcmVmZXJEb2N1bWVudD8hMDooZT1udWxsIT1hP2E9PT10aGlzLmlucHV0U3VtbWFyeS50ZXh0QWRkZWQ6IXRoaXMuaW5wdXRTdW1tYXJ5LnRleHRBZGRlZCxuPW51bGwhPXU/dGhpcy5pbnB1dFN1bW1hcnkuZGlkRGVsZXRlOiF0aGlzLmlucHV0U3VtbWFyeS5kaWREZWxldGUsYz0oXCJcXG5cIj09PWF8fFwiIFxcblwiPT09YSkmJiFlLGw9XCJcXG5cIj09PXUmJiFuLHM9YyYmIWx8fGwmJiFjLHMmJihvPXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpKSYmKGk9Yz9hLnJlcGxhY2UoL1xcbiQvLFwiXCIpLmxlbmd0aHx8LTE6KG51bGwhPWE/YS5sZW5ndGg6dm9pZCAwKXx8MSxudWxsIT0ocj10aGlzLnJlc3BvbmRlcik/ci5wb3NpdGlvbklzQmxvY2tCcmVhayhvWzFdK2kpOnZvaWQgMCk/ITA6ZSYmbil9LHMucHJvdG90eXBlLm11dGF0aW9uSXNTaWduaWZpY2FudD1mdW5jdGlvbih0KXt2YXIgZSxuLGk7cmV0dXJuIGk9T2JqZWN0LmtleXModCkubGVuZ3RoPjAsZT1cIlwiPT09KG51bGwhPShuPXRoaXMuY29tcG9zaXRpb25JbnB1dCk/bi5nZXRFbmREYXRhKCk6dm9pZCAwKSxpfHwhZX0scy5wcm90b3R5cGUuZXZlbnRzPXtrZXlkb3duOmZ1bmN0aW9uKHQpe3ZhciBuLGksbyxyLHMsYyxsLGgscDtpZih0aGlzLmlzQ29tcG9zaW5nKCl8fHRoaXMucmVzZXRJbnB1dFN1bW1hcnkoKSx0aGlzLmlucHV0U3VtbWFyeS5kaWRJbnB1dD0hMCxyPXVbdC5rZXlDb2RlXSl7Zm9yKGk9dGhpcy5rZXlzLGg9W1wiY3RybFwiLFwiYWx0XCIsXCJzaGlmdFwiLFwibWV0YVwiXSxvPTAsYz1oLmxlbmd0aDtjPm87bysrKWw9aFtvXSx0W2wrXCJLZXlcIl0mJihcImN0cmxcIj09PWwmJihsPVwiY29udHJvbFwiKSxpPW51bGwhPWk/aVtsXTp2b2lkIDApO251bGwhPShudWxsIT1pP2lbcl06dm9pZCAwKSYmKHRoaXMuc2V0SW5wdXRTdW1tYXJ5KHtrZXlOYW1lOnJ9KSxlLnNlbGVjdGlvbkNoYW5nZU9ic2VydmVyLnJlc2V0KCksaVtyXS5jYWxsKHRoaXMsdCkpfXJldHVybiBhKHQpJiYobj1TdHJpbmcuZnJvbUNoYXJDb2RlKHQua2V5Q29kZSkudG9Mb3dlckNhc2UoKSkmJihzPWZ1bmN0aW9uKCl7dmFyIGUsbixpLG87Zm9yKGk9W1wiYWx0XCIsXCJzaGlmdFwiXSxvPVtdLGU9MCxuPWkubGVuZ3RoO24+ZTtlKyspbD1pW2VdLHRbbCtcIktleVwiXSYmby5wdXNoKGwpO3JldHVybiBvfSgpLHMucHVzaChuKSxudWxsIT0ocD10aGlzLmRlbGVnYXRlKT9wLmlucHV0Q29udHJvbGxlckRpZFJlY2VpdmVLZXlib2FyZENvbW1hbmQocyk6dm9pZCAwKT90LnByZXZlbnREZWZhdWx0KCk6dm9pZCAwfSxrZXlwcmVzczpmdW5jdGlvbih0KXt2YXIgZSxuLGk7aWYobnVsbD09dGhpcy5pbnB1dFN1bW1hcnkuZXZlbnROYW1lJiYhdC5tZXRhS2V5JiYoIXQuY3RybEtleXx8dC5hbHRLZXkpKXJldHVybihpPXAodCkpPyhudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmZS5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpLG51bGwhPShuPXRoaXMucmVzcG9uZGVyKSYmbi5pbnNlcnRTdHJpbmcoaSksdGhpcy5zZXRJbnB1dFN1bW1hcnkoe3RleHRBZGRlZDppLGRpZERlbGV0ZTp0aGlzLnNlbGVjdGlvbklzRXhwYW5kZWQoKX0pKTp2b2lkIDB9LHRleHRJbnB1dDpmdW5jdGlvbih0KXt2YXIgZSxuLGksbztyZXR1cm4gZT10LmRhdGEsbz10aGlzLmlucHV0U3VtbWFyeS50ZXh0QWRkZWQsbyYmbyE9PWUmJm8udG9VcHBlckNhc2UoKT09PWU/KG49dGhpcy5nZXRTZWxlY3RlZFJhbmdlKCksdGhpcy5zZXRTZWxlY3RlZFJhbmdlKFtuWzBdLG5bMV0rby5sZW5ndGhdKSxudWxsIT0oaT10aGlzLnJlc3BvbmRlcikmJmkuaW5zZXJ0U3RyaW5nKGUpLHRoaXMuc2V0SW5wdXRTdW1tYXJ5KHt0ZXh0QWRkZWQ6ZX0pLHRoaXMuc2V0U2VsZWN0ZWRSYW5nZShuKSk6dm9pZCAwfSxkcmFnZW50ZXI6ZnVuY3Rpb24odCl7cmV0dXJuIHQucHJldmVudERlZmF1bHQoKX0sZHJhZ3N0YXJ0OmZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG49dC50YXJnZXQsdGhpcy5zZXJpYWxpemVTZWxlY3Rpb25Ub0RhdGFUcmFuc2Zlcih0LmRhdGFUcmFuc2ZlciksdGhpcy5kcmFnZ2VkUmFuZ2U9dGhpcy5nZXRTZWxlY3RlZFJhbmdlKCksbnVsbCE9KGU9dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIGUuaW5wdXRDb250cm9sbGVyRGlkU3RhcnREcmFnP2UuaW5wdXRDb250cm9sbGVyRGlkU3RhcnREcmFnKCk6dm9pZCAwfSxkcmFnb3ZlcjpmdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiF0aGlzLmRyYWdnZWRSYW5nZSYmIXRoaXMuY2FuQWNjZXB0RGF0YVRyYW5zZmVyKHQuZGF0YVRyYW5zZmVyKXx8KHQucHJldmVudERlZmF1bHQoKSxlPXt4OnQuY2xpZW50WCx5OnQuY2xpZW50WX0sbChlLHRoaXMuZHJhZ2dpbmdQb2ludCkpP3ZvaWQgMDoodGhpcy5kcmFnZ2luZ1BvaW50PWUsbnVsbCE9KG49dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIG4uaW5wdXRDb250cm9sbGVyRGlkUmVjZWl2ZURyYWdPdmVyUG9pbnQ/bi5pbnB1dENvbnRyb2xsZXJEaWRSZWNlaXZlRHJhZ092ZXJQb2ludCh0aGlzLmRyYWdnaW5nUG9pbnQpOnZvaWQgMCl9LGRyYWdlbmQ6ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIHQuaW5wdXRDb250cm9sbGVyRGlkQ2FuY2VsRHJhZyYmdC5pbnB1dENvbnRyb2xsZXJEaWRDYW5jZWxEcmFnKCksdGhpcy5kcmFnZ2VkUmFuZ2U9bnVsbCx0aGlzLmRyYWdnaW5nUG9pbnQ9bnVsbH0sZHJvcDpmdW5jdGlvbih0KXt2YXIgbixpLG8scixzLGEsdSxjLGw7cmV0dXJuIHQucHJldmVudERlZmF1bHQoKSxvPW51bGwhPShzPXQuZGF0YVRyYW5zZmVyKT9zLmZpbGVzOnZvaWQgMCxyPXt4OnQuY2xpZW50WCx5OnQuY2xpZW50WX0sbnVsbCE9KGE9dGhpcy5yZXNwb25kZXIpJiZhLnNldExvY2F0aW9uUmFuZ2VGcm9tUG9pbnRSYW5nZShyKSwobnVsbCE9bz9vLmxlbmd0aDp2b2lkIDApP3RoaXMuYXR0YWNoRmlsZXMobyk6dGhpcy5kcmFnZ2VkUmFuZ2U/KG51bGwhPSh1PXRoaXMuZGVsZWdhdGUpJiZ1LmlucHV0Q29udHJvbGxlcldpbGxNb3ZlVGV4dCgpLG51bGwhPShjPXRoaXMucmVzcG9uZGVyKSYmYy5tb3ZlVGV4dEZyb21SYW5nZSh0aGlzLmRyYWdnZWRSYW5nZSksdGhpcy5kcmFnZ2VkUmFuZ2U9bnVsbCx0aGlzLnJlcXVlc3RSZW5kZXIoKSk6KGk9dC5kYXRhVHJhbnNmZXIuZ2V0RGF0YShcImFwcGxpY2F0aW9uL3gtdHJpeC1kb2N1bWVudFwiKSkmJihuPWUuRG9jdW1lbnQuZnJvbUpTT05TdHJpbmcoaSksbnVsbCE9KGw9dGhpcy5yZXNwb25kZXIpJiZsLmluc2VydERvY3VtZW50KG4pLHRoaXMucmVxdWVzdFJlbmRlcigpKSx0aGlzLmRyYWdnZWRSYW5nZT1udWxsLHRoaXMuZHJhZ2dpbmdQb2ludD1udWxsfSxjdXQ6ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4obnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2Uuc2VsZWN0aW9uSXNFeHBhbmRlZCgpOnZvaWQgMCkmJih0aGlzLnNlcmlhbGl6ZVNlbGVjdGlvblRvRGF0YVRyYW5zZmVyKHQuY2xpcGJvYXJkRGF0YSkmJnQucHJldmVudERlZmF1bHQoKSxudWxsIT0obj10aGlzLmRlbGVnYXRlKSYmbi5pbnB1dENvbnRyb2xsZXJXaWxsQ3V0VGV4dCgpLHRoaXMuZGVsZXRlSW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiKSx0LmRlZmF1bHRQcmV2ZW50ZWQpP3RoaXMucmVxdWVzdFJlbmRlcigpOnZvaWQgMH0sY29weTpmdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4obnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2Uuc2VsZWN0aW9uSXNFeHBhbmRlZCgpOnZvaWQgMCkmJnRoaXMuc2VyaWFsaXplU2VsZWN0aW9uVG9EYXRhVHJhbnNmZXIodC5jbGlwYm9hcmREYXRhKT90LnByZXZlbnREZWZhdWx0KCk6dm9pZCAwfSxwYXN0ZTpmdW5jdGlvbih0KXt2YXIgbixvLHMsYSx1LGMsbCxwLGYsZyx2LHksYixBLEMseCx3LEUsUyxSLGssRCxMO3JldHVybiBuPW51bGwhPShwPXQuY2xpcGJvYXJkRGF0YSk/cDp0LnRlc3RDbGlwYm9hcmREYXRhLGw9e2NsaXBib2FyZDpufSxudWxsPT1ufHxoKHQpP3ZvaWQgdGhpcy5nZXRQYXN0ZWRIVE1MVXNpbmdIaWRkZW5FbGVtZW50KGZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbihlKXt2YXIgbixpLG87cmV0dXJuIGwudHlwZT1cInRleHQvaHRtbFwiLGwuaHRtbD1lLG51bGwhPShuPXQuZGVsZWdhdGUpJiZuLmlucHV0Q29udHJvbGxlcldpbGxQYXN0ZShsKSxudWxsIT0oaT10LnJlc3BvbmRlcikmJmkuaW5zZXJ0SFRNTChsLmh0bWwpLHQucmVxdWVzdFJlbmRlcigpLG51bGwhPShvPXQuZGVsZWdhdGUpP28uaW5wdXRDb250cm9sbGVyRGlkUGFzdGUobCk6dm9pZCAwfX0odGhpcykpOigoYT1uLmdldERhdGEoXCJVUkxcIikpPyhsLnR5cGU9XCJ0ZXh0L2h0bWxcIixMPShjPW4uZ2V0RGF0YShcInB1YmxpYy51cmwtbmFtZVwiKSk/ZS5zcXVpc2hCcmVha2FibGVXaGl0ZXNwYWNlKGMpLnRyaW0oKTphLGwuaHRtbD10aGlzLmNyZWF0ZUxpbmtIVE1MKGEsTCksbnVsbCE9KGY9dGhpcy5kZWxlZ2F0ZSkmJmYuaW5wdXRDb250cm9sbGVyV2lsbFBhc3RlKGwpLHRoaXMuc2V0SW5wdXRTdW1tYXJ5KHt0ZXh0QWRkZWQ6TCxkaWREZWxldGU6dGhpcy5zZWxlY3Rpb25Jc0V4cGFuZGVkKCl9KSxudWxsIT0oQz10aGlzLnJlc3BvbmRlcikmJkMuaW5zZXJ0SFRNTChsLmh0bWwpLHRoaXMucmVxdWVzdFJlbmRlcigpLG51bGwhPSh4PXRoaXMuZGVsZWdhdGUpJiZ4LmlucHV0Q29udHJvbGxlckRpZFBhc3RlKGwpKTppKG4pPyhsLnR5cGU9XCJ0ZXh0L3BsYWluXCIsbC5zdHJpbmc9bi5nZXREYXRhKFwidGV4dC9wbGFpblwiKSxudWxsIT0odz10aGlzLmRlbGVnYXRlKSYmdy5pbnB1dENvbnRyb2xsZXJXaWxsUGFzdGUobCksdGhpcy5zZXRJbnB1dFN1bW1hcnkoe3RleHRBZGRlZDpsLnN0cmluZyxkaWREZWxldGU6dGhpcy5zZWxlY3Rpb25Jc0V4cGFuZGVkKCl9KSxudWxsIT0oRT10aGlzLnJlc3BvbmRlcikmJkUuaW5zZXJ0U3RyaW5nKGwuc3RyaW5nKSx0aGlzLnJlcXVlc3RSZW5kZXIoKSxudWxsIT0oUz10aGlzLmRlbGVnYXRlKSYmUy5pbnB1dENvbnRyb2xsZXJEaWRQYXN0ZShsKSk6KHU9bi5nZXREYXRhKFwidGV4dC9odG1sXCIpKT8obC50eXBlPVwidGV4dC9odG1sXCIsbC5odG1sPXUsbnVsbCE9KFI9dGhpcy5kZWxlZ2F0ZSkmJlIuaW5wdXRDb250cm9sbGVyV2lsbFBhc3RlKGwpLG51bGwhPShrPXRoaXMucmVzcG9uZGVyKSYmay5pbnNlcnRIVE1MKGwuaHRtbCksdGhpcy5yZXF1ZXN0UmVuZGVyKCksbnVsbCE9KEQ9dGhpcy5kZWxlZ2F0ZSkmJkQuaW5wdXRDb250cm9sbGVyRGlkUGFzdGUobCkpOm0uY2FsbChuLnR5cGVzLFwiRmlsZXNcIik+PTAmJihzPW51bGwhPShnPW4uaXRlbXMpJiZudWxsIT0odj1nWzBdKSYmXCJmdW5jdGlvblwiPT10eXBlb2Ygdi5nZXRBc0ZpbGU/di5nZXRBc0ZpbGUoKTp2b2lkIDApJiYoIXMubmFtZSYmKG89cihzKSkmJihzLm5hbWU9XCJwYXN0ZWQtZmlsZS1cIisgKytkK1wiLlwiK28pLGwudHlwZT1cIkZpbGVcIixsLmZpbGU9cyxudWxsIT0oeT10aGlzLmRlbGVnYXRlKSYmeS5pbnB1dENvbnRyb2xsZXJXaWxsQXR0YWNoRmlsZXMoKSxudWxsIT0oYj10aGlzLnJlc3BvbmRlcikmJmIuaW5zZXJ0RmlsZShsLmZpbGUpLHRoaXMucmVxdWVzdFJlbmRlcigpLG51bGwhPShBPXRoaXMuZGVsZWdhdGUpJiZBLmlucHV0Q29udHJvbGxlckRpZFBhc3RlKGwpKSx0LnByZXZlbnREZWZhdWx0KCkpfSxjb21wb3NpdGlvbnN0YXJ0OmZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmdldENvbXBvc2l0aW9uSW5wdXQoKS5zdGFydCh0LmRhdGEpfSxjb21wb3NpdGlvbnVwZGF0ZTpmdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5nZXRDb21wb3NpdGlvbklucHV0KCkudXBkYXRlKHQuZGF0YSl9LGNvbXBvc2l0aW9uZW5kOmZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmdldENvbXBvc2l0aW9uSW5wdXQoKS5lbmQodC5kYXRhKX0sYmVmb3JlaW5wdXQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pbnB1dFN1bW1hcnkuZGlkSW5wdXQ9ITBcbn0saW5wdXQ6ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuaW5wdXRTdW1tYXJ5LmRpZElucHV0PSEwLHQuc3RvcFByb3BhZ2F0aW9uKCl9fSxzLnByb3RvdHlwZS5rZXlzPXtiYWNrc3BhY2U6ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZlLmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtVHlwaW5nKCksdGhpcy5kZWxldGVJbkRpcmVjdGlvbihcImJhY2t3YXJkXCIsdCl9LFwiZGVsZXRlXCI6ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZlLmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtVHlwaW5nKCksdGhpcy5kZWxldGVJbkRpcmVjdGlvbihcImZvcndhcmRcIix0KX0sXCJyZXR1cm5cIjpmdW5jdGlvbigpe3ZhciB0LGU7cmV0dXJuIHRoaXMuc2V0SW5wdXRTdW1tYXJ5KHtwcmVmZXJEb2N1bWVudDohMH0pLG51bGwhPSh0PXRoaXMuZGVsZWdhdGUpJiZ0LmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtVHlwaW5nKCksbnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2UuaW5zZXJ0TGluZUJyZWFrKCk6dm9pZCAwfSx0YWI6ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4obnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2UuY2FuSW5jcmVhc2VOZXN0aW5nTGV2ZWwoKTp2b2lkIDApPyhudWxsIT0obj10aGlzLnJlc3BvbmRlcikmJm4uaW5jcmVhc2VOZXN0aW5nTGV2ZWwoKSx0aGlzLnJlcXVlc3RSZW5kZXIoKSx0LnByZXZlbnREZWZhdWx0KCkpOnZvaWQgMH0sbGVmdDpmdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gdGhpcy5zZWxlY3Rpb25Jc0luQ3Vyc29yVGFyZ2V0KCk/KHQucHJldmVudERlZmF1bHQoKSxudWxsIT0oZT10aGlzLnJlc3BvbmRlcik/ZS5tb3ZlQ3Vyc29ySW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiKTp2b2lkIDApOnZvaWQgMH0scmlnaHQ6ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHRoaXMuc2VsZWN0aW9uSXNJbkN1cnNvclRhcmdldCgpPyh0LnByZXZlbnREZWZhdWx0KCksbnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2UubW92ZUN1cnNvckluRGlyZWN0aW9uKFwiZm9yd2FyZFwiKTp2b2lkIDApOnZvaWQgMH0sY29udHJvbDp7ZDpmdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gbnVsbCE9KGU9dGhpcy5kZWxlZ2F0ZSkmJmUuaW5wdXRDb250cm9sbGVyV2lsbFBlcmZvcm1UeXBpbmcoKSx0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiZm9yd2FyZFwiLHQpfSxoOmZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmZS5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpLHRoaXMuZGVsZXRlSW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiLHQpfSxvOmZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIHQucHJldmVudERlZmF1bHQoKSxudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmZS5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpLG51bGwhPShuPXRoaXMucmVzcG9uZGVyKSYmbi5pbnNlcnRTdHJpbmcoXCJcXG5cIix7dXBkYXRlUG9zaXRpb246ITF9KSx0aGlzLnJlcXVlc3RSZW5kZXIoKX19LHNoaWZ0OntcInJldHVyblwiOmZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZlLmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtVHlwaW5nKCksbnVsbCE9KG49dGhpcy5yZXNwb25kZXIpJiZuLmluc2VydFN0cmluZyhcIlxcblwiKSx0aGlzLnJlcXVlc3RSZW5kZXIoKSx0LnByZXZlbnREZWZhdWx0KCl9LHRhYjpmdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybihudWxsIT0oZT10aGlzLnJlc3BvbmRlcik/ZS5jYW5EZWNyZWFzZU5lc3RpbmdMZXZlbCgpOnZvaWQgMCk/KG51bGwhPShuPXRoaXMucmVzcG9uZGVyKSYmbi5kZWNyZWFzZU5lc3RpbmdMZXZlbCgpLHRoaXMucmVxdWVzdFJlbmRlcigpLHQucHJldmVudERlZmF1bHQoKSk6dm9pZCAwfSxsZWZ0OmZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnNlbGVjdGlvbklzSW5DdXJzb3JUYXJnZXQoKT8odC5wcmV2ZW50RGVmYXVsdCgpLHRoaXMuZXhwYW5kU2VsZWN0aW9uSW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiKSk6dm9pZCAwfSxyaWdodDpmdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5zZWxlY3Rpb25Jc0luQ3Vyc29yVGFyZ2V0KCk/KHQucHJldmVudERlZmF1bHQoKSx0aGlzLmV4cGFuZFNlbGVjdGlvbkluRGlyZWN0aW9uKFwiZm9yd2FyZFwiKSk6dm9pZCAwfX0sYWx0OntiYWNrc3BhY2U6ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gdGhpcy5zZXRJbnB1dFN1bW1hcnkoe3ByZWZlckRvY3VtZW50OiExfSksbnVsbCE9KHQ9dGhpcy5kZWxlZ2F0ZSk/dC5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpOnZvaWQgMH19LG1ldGE6e2JhY2tzcGFjZTpmdW5jdGlvbigpe3ZhciB0O3JldHVybiB0aGlzLnNldElucHV0U3VtbWFyeSh7cHJlZmVyRG9jdW1lbnQ6ITF9KSxudWxsIT0odD10aGlzLmRlbGVnYXRlKT90LmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtVHlwaW5nKCk6dm9pZCAwfX19LHMucHJvdG90eXBlLmdldENvbXBvc2l0aW9uSW5wdXQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pc0NvbXBvc2luZygpP3RoaXMuY29tcG9zaXRpb25JbnB1dDp0aGlzLmNvbXBvc2l0aW9uSW5wdXQ9bmV3IHQodGhpcyl9LHMucHJvdG90eXBlLmlzQ29tcG9zaW5nPWZ1bmN0aW9uKCl7cmV0dXJuIG51bGwhPXRoaXMuY29tcG9zaXRpb25JbnB1dCYmIXRoaXMuY29tcG9zaXRpb25JbnB1dC5pc0VuZGVkKCl9LHMucHJvdG90eXBlLmRlbGV0ZUluRGlyZWN0aW9uPWZ1bmN0aW9uKHQsZSl7dmFyIG47cmV0dXJuKG51bGwhPShuPXRoaXMucmVzcG9uZGVyKT9uLmRlbGV0ZUluRGlyZWN0aW9uKHQpOnZvaWQgMCkhPT0hMT90aGlzLnNldElucHV0U3VtbWFyeSh7ZGlkRGVsZXRlOiEwfSk6ZT8oZS5wcmV2ZW50RGVmYXVsdCgpLHRoaXMucmVxdWVzdFJlbmRlcigpKTp2b2lkIDB9LHMucHJvdG90eXBlLnNlcmlhbGl6ZVNlbGVjdGlvblRvRGF0YVRyYW5zZmVyPWZ1bmN0aW9uKHQpe3ZhciBuLGk7aWYobyh0KSlyZXR1cm4gbj1udWxsIT0oaT10aGlzLnJlc3BvbmRlcik/aS5nZXRTZWxlY3RlZERvY3VtZW50KCkudG9TZXJpYWxpemFibGVEb2N1bWVudCgpOnZvaWQgMCx0LnNldERhdGEoXCJhcHBsaWNhdGlvbi94LXRyaXgtZG9jdW1lbnRcIixKU09OLnN0cmluZ2lmeShuKSksdC5zZXREYXRhKFwidGV4dC9odG1sXCIsZS5Eb2N1bWVudFZpZXcucmVuZGVyKG4pLmlubmVySFRNTCksdC5zZXREYXRhKFwidGV4dC9wbGFpblwiLG4udG9TdHJpbmcoKS5yZXBsYWNlKC9cXG4kLyxcIlwiKSksITB9LHMucHJvdG90eXBlLmNhbkFjY2VwdERhdGFUcmFuc2Zlcj1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxyLHM7Zm9yKHM9e30sbz1udWxsIT0oaT1udWxsIT10P3QudHlwZXM6dm9pZCAwKT9pOltdLGU9MCxuPW8ubGVuZ3RoO24+ZTtlKyspcj1vW2VdLHNbcl09ITA7cmV0dXJuIHMuRmlsZXN8fHNbXCJhcHBsaWNhdGlvbi94LXRyaXgtZG9jdW1lbnRcIl18fHNbXCJ0ZXh0L2h0bWxcIl18fHNbXCJ0ZXh0L3BsYWluXCJdfSxzLnByb3RvdHlwZS5nZXRQYXN0ZWRIVE1MVXNpbmdIaWRkZW5FbGVtZW50PWZ1bmN0aW9uKHQpe3ZhciBuLGksbztyZXR1cm4gaT10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSxvPXtwb3NpdGlvbjpcImFic29sdXRlXCIsbGVmdDp3aW5kb3cucGFnZVhPZmZzZXQrXCJweFwiLHRvcDp3aW5kb3cucGFnZVlPZmZzZXQrXCJweFwiLG9wYWNpdHk6MH0sbj1jKHtzdHlsZTpvLHRhZ05hbWU6XCJkaXZcIixlZGl0YWJsZTohMH0pLGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQobiksbi5mb2N1cygpLHJlcXVlc3RBbmltYXRpb25GcmFtZShmdW5jdGlvbihvKXtyZXR1cm4gZnVuY3Rpb24oKXt2YXIgcjtyZXR1cm4gcj1uLmlubmVySFRNTCxlLnJlbW92ZU5vZGUobiksby5zZXRTZWxlY3RlZFJhbmdlKGkpLHQocil9fSh0aGlzKSl9LHMucHJveHlNZXRob2QoXCJyZXNwb25kZXI/LmdldFNlbGVjdGVkUmFuZ2VcIikscy5wcm94eU1ldGhvZChcInJlc3BvbmRlcj8uc2V0U2VsZWN0ZWRSYW5nZVwiKSxzLnByb3h5TWV0aG9kKFwicmVzcG9uZGVyPy5leHBhbmRTZWxlY3Rpb25JbkRpcmVjdGlvblwiKSxzLnByb3h5TWV0aG9kKFwicmVzcG9uZGVyPy5zZWxlY3Rpb25Jc0luQ3Vyc29yVGFyZ2V0XCIpLHMucHJveHlNZXRob2QoXCJyZXNwb25kZXI/LnNlbGVjdGlvbklzRXhwYW5kZWRcIiksc30oZS5JbnB1dENvbnRyb2xsZXIpLHI9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gbnVsbCE9KGU9dC50eXBlKSYmbnVsbCE9KG49ZS5tYXRjaCgvXFwvKFxcdyspJC8pKT9uWzFdOnZvaWQgMH0scz1udWxsIT0oXCJmdW5jdGlvblwiPT10eXBlb2ZcIiBcIi5jb2RlUG9pbnRBdD9cIiBcIi5jb2RlUG9pbnRBdCgwKTp2b2lkIDApLHA9ZnVuY3Rpb24odCl7dmFyIG47cmV0dXJuIHQua2V5JiZzJiZ0LmtleS5jb2RlUG9pbnRBdCgwKT09PXQua2V5Q29kZT90LmtleToobnVsbD09PXQud2hpY2g/bj10LmtleUNvZGU6MCE9PXQud2hpY2gmJjAhPT10LmNoYXJDb2RlJiYobj10LmNoYXJDb2RlKSxudWxsIT1uJiZcImVzY2FwZVwiIT09dVtuXT9lLlVURjE2U3RyaW5nLmZyb21Db2RlcG9pbnRzKFtuXSkudG9TdHJpbmcoKTp2b2lkIDApfSxoPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHIscyxhLHUsYyxsO2lmKHU9dC5jbGlwYm9hcmREYXRhKXtpZihtLmNhbGwodS50eXBlcyxcInRleHQvaHRtbFwiKT49MCl7Zm9yKGM9dS50eXBlcyxpPTAscz1jLmxlbmd0aDtzPmk7aSsrKWlmKGw9Y1tpXSxlPS9eQ29yZVBhc3RlYm9hcmRGbGF2b3JUeXBlLy50ZXN0KGwpLG49L15keW5cXC4vLnRlc3QobCkmJnUuZ2V0RGF0YShsKSxhPWV8fG4pcmV0dXJuITA7cmV0dXJuITF9cmV0dXJuIG89bS5jYWxsKHUudHlwZXMsXCJjb20uYXBwbGUud2ViYXJjaGl2ZVwiKT49MCxyPW0uY2FsbCh1LnR5cGVzLFwiY29tLmFwcGxlLmZsYXQtcnRmZFwiKT49MCxvfHxyfX0sdD1mdW5jdGlvbih0KXtmdW5jdGlvbiBlKHQpe3ZhciBlO3RoaXMuaW5wdXRDb250cm9sbGVyPXQsZT10aGlzLmlucHV0Q29udHJvbGxlcix0aGlzLnJlc3BvbmRlcj1lLnJlc3BvbmRlcix0aGlzLmRlbGVnYXRlPWUuZGVsZWdhdGUsdGhpcy5pbnB1dFN1bW1hcnk9ZS5pbnB1dFN1bW1hcnksdGhpcy5kYXRhPXt9fXJldHVybiBmKGUsdCksZS5wcm90b3R5cGUuc3RhcnQ9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gdGhpcy5kYXRhLnN0YXJ0PXQsdGhpcy5pc1NpZ25pZmljYW50KCk/KFwia2V5cHJlc3NcIj09PXRoaXMuaW5wdXRTdW1tYXJ5LmV2ZW50TmFtZSYmdGhpcy5pbnB1dFN1bW1hcnkudGV4dEFkZGVkJiZudWxsIT0oZT10aGlzLnJlc3BvbmRlcikmJmUuZGVsZXRlSW5EaXJlY3Rpb24oXCJsZWZ0XCIpLHRoaXMuc2VsZWN0aW9uSXNFeHBhbmRlZCgpfHwodGhpcy5pbnNlcnRQbGFjZWhvbGRlcigpLHRoaXMucmVxdWVzdFJlbmRlcigpKSx0aGlzLnJhbmdlPW51bGwhPShuPXRoaXMucmVzcG9uZGVyKT9uLmdldFNlbGVjdGVkUmFuZ2UoKTp2b2lkIDApOnZvaWQgMH0sZS5wcm90b3R5cGUudXBkYXRlPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiB0aGlzLmRhdGEudXBkYXRlPXQsdGhpcy5pc1NpZ25pZmljYW50KCkmJihlPXRoaXMuc2VsZWN0UGxhY2Vob2xkZXIoKSk/KHRoaXMuZm9yZ2V0UGxhY2Vob2xkZXIoKSx0aGlzLnJhbmdlPWUpOnZvaWQgMH0sZS5wcm90b3R5cGUuZW5kPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvO3JldHVybiB0aGlzLmRhdGEuZW5kPXQsdGhpcy5pc1NpZ25pZmljYW50KCk/KHRoaXMuZm9yZ2V0UGxhY2Vob2xkZXIoKSx0aGlzLmNhbkFwcGx5VG9Eb2N1bWVudCgpPyh0aGlzLnNldElucHV0U3VtbWFyeSh7cHJlZmVyRG9jdW1lbnQ6ITAsZGlkSW5wdXQ6ITF9KSxudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmZS5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpLG51bGwhPShuPXRoaXMucmVzcG9uZGVyKSYmbi5zZXRTZWxlY3RlZFJhbmdlKHRoaXMucmFuZ2UpLG51bGwhPShpPXRoaXMucmVzcG9uZGVyKSYmaS5pbnNlcnRTdHJpbmcodGhpcy5kYXRhLmVuZCksbnVsbCE9KG89dGhpcy5yZXNwb25kZXIpP28uc2V0U2VsZWN0ZWRSYW5nZSh0aGlzLnJhbmdlWzBdK3RoaXMuZGF0YS5lbmQubGVuZ3RoKTp2b2lkIDApOm51bGwhPXRoaXMuZGF0YS5zdGFydHx8bnVsbCE9dGhpcy5kYXRhLnVwZGF0ZT8odGhpcy5yZXF1ZXN0UmVwYXJzZSgpLHRoaXMuaW5wdXRDb250cm9sbGVyLnJlc2V0KCkpOnZvaWQgMCk6dGhpcy5pbnB1dENvbnRyb2xsZXIucmVzZXQoKX0sZS5wcm90b3R5cGUuZ2V0RW5kRGF0YT1mdW5jdGlvbigpe3JldHVybiB0aGlzLmRhdGEuZW5kfSxlLnByb3RvdHlwZS5pc0VuZGVkPWZ1bmN0aW9uKCl7cmV0dXJuIG51bGwhPXRoaXMuZ2V0RW5kRGF0YSgpfSxlLnByb3RvdHlwZS5pc1NpZ25pZmljYW50PWZ1bmN0aW9uKCl7cmV0dXJuIG4uY29tcG9zZXNFeGlzdGluZ1RleHQ/dGhpcy5pbnB1dFN1bW1hcnkuZGlkSW5wdXQ6ITB9LGUucHJvdG90eXBlLmNhbkFwcGx5VG9Eb2N1bWVudD1mdW5jdGlvbigpe3ZhciB0LGU7cmV0dXJuIDA9PT0obnVsbCE9KHQ9dGhpcy5kYXRhLnN0YXJ0KT90Lmxlbmd0aDp2b2lkIDApJiYobnVsbCE9KGU9dGhpcy5kYXRhLmVuZCk/ZS5sZW5ndGg6dm9pZCAwKT4wJiZudWxsIT10aGlzLnJhbmdlfSxlLnByb3h5TWV0aG9kKFwiaW5wdXRDb250cm9sbGVyLnNldElucHV0U3VtbWFyeVwiKSxlLnByb3h5TWV0aG9kKFwiaW5wdXRDb250cm9sbGVyLnJlcXVlc3RSZW5kZXJcIiksZS5wcm94eU1ldGhvZChcImlucHV0Q29udHJvbGxlci5yZXF1ZXN0UmVwYXJzZVwiKSxlLnByb3h5TWV0aG9kKFwicmVzcG9uZGVyPy5zZWxlY3Rpb25Jc0V4cGFuZGVkXCIpLGUucHJveHlNZXRob2QoXCJyZXNwb25kZXI/Lmluc2VydFBsYWNlaG9sZGVyXCIpLGUucHJveHlNZXRob2QoXCJyZXNwb25kZXI/LnNlbGVjdFBsYWNlaG9sZGVyXCIpLGUucHJveHlNZXRob2QoXCJyZXNwb25kZXI/LmZvcmdldFBsYWNlaG9sZGVyXCIpLGV9KGUuQmFzaWNPYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG89ZnVuY3Rpb24odCxlKXtyZXR1cm4gZnVuY3Rpb24oKXtyZXR1cm4gdC5hcHBseShlLGFyZ3VtZW50cyl9fSxyPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIGkgaW4gZSlzLmNhbGwoZSxpKSYmKHRbaV09ZVtpXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LHM9e30uaGFzT3duUHJvcGVydHksYT1bXS5pbmRleE9mfHxmdW5jdGlvbih0KXtmb3IodmFyIGU9MCxuPXRoaXMubGVuZ3RoO24+ZTtlKyspaWYoZSBpbiB0aGlzJiZ0aGlzW2VdPT09dClyZXR1cm4gZTtyZXR1cm4tMX07dD1lLmRhdGFUcmFuc2ZlcklzUGxhaW5UZXh0LG49ZS5rZXlFdmVudElzS2V5Ym9hcmRDb21tYW5kLGk9ZS5vYmplY3RzQXJlRXF1YWwsZS5MZXZlbDJJbnB1dENvbnRyb2xsZXI9ZnVuY3Rpb24ocyl7ZnVuY3Rpb24gdSgpe3JldHVybiB0aGlzLnJlbmRlcj1vKHRoaXMucmVuZGVyLHRoaXMpLHUuX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKX12YXIgYyxsLGgscCxkLGY7cmV0dXJuIHIodSxzKSx1LnByb3RvdHlwZS5lbGVtZW50RGlkTXV0YXRlPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIHRoaXMuc2NoZWR1bGVkUmVuZGVyP3RoaXMuY29tcG9zaW5nJiZudWxsIT0odD10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgdC5pbnB1dENvbnRyb2xsZXJEaWRBbGxvd1VuaGFuZGxlZElucHV0P3QuaW5wdXRDb250cm9sbGVyRGlkQWxsb3dVbmhhbmRsZWRJbnB1dCgpOnZvaWQgMDp0aGlzLnJlcGFyc2UoKX0sdS5wcm90b3R5cGUuc2NoZWR1bGVSZW5kZXI9ZnVuY3Rpb24oKXtyZXR1cm4gbnVsbCE9dGhpcy5zY2hlZHVsZWRSZW5kZXI/dGhpcy5zY2hlZHVsZWRSZW5kZXI6dGhpcy5zY2hlZHVsZWRSZW5kZXI9cmVxdWVzdEFuaW1hdGlvbkZyYW1lKHRoaXMucmVuZGVyKX0sdS5wcm90b3R5cGUucmVuZGVyPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIGNhbmNlbEFuaW1hdGlvbkZyYW1lKHRoaXMuc2NoZWR1bGVkUmVuZGVyKSx0aGlzLnNjaGVkdWxlZFJlbmRlcj1udWxsLHRoaXMuY29tcG9zaW5nfHxudWxsIT0odD10aGlzLmRlbGVnYXRlKSYmdC5yZW5kZXIoKSxcImZ1bmN0aW9uXCI9PXR5cGVvZiB0aGlzLmFmdGVyUmVuZGVyJiZ0aGlzLmFmdGVyUmVuZGVyKCksdGhpcy5hZnRlclJlbmRlcj1udWxsfSx1LnByb3RvdHlwZS5yZXBhcnNlPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMuZGVsZWdhdGUpP3QucmVwYXJzZSgpOnZvaWQgMH0sdS5wcm90b3R5cGUuZXZlbnRzPXtrZXlkb3duOmZ1bmN0aW9uKHQpe3ZhciBlLGksbyxyO2lmKG4odCkpe2lmKGU9bCh0KSxudWxsIT0ocj10aGlzLmRlbGVnYXRlKT9yLmlucHV0Q29udHJvbGxlckRpZFJlY2VpdmVLZXlib2FyZENvbW1hbmQoZSk6dm9pZCAwKXJldHVybiB0LnByZXZlbnREZWZhdWx0KCl9ZWxzZSBpZihvPXQua2V5LHQuYWx0S2V5JiYobys9XCIrQWx0XCIpLHQuc2hpZnRLZXkmJihvKz1cIitTaGlmdFwiKSxpPXRoaXMua2V5c1tvXSlyZXR1cm4gdGhpcy53aXRoRXZlbnQodCxpKX0scGFzdGU6ZnVuY3Rpb24odCl7dmFyIGUsbixpLG8scixzLGEsdSxjO3JldHVybiBoKHQpPyh0LnByZXZlbnREZWZhdWx0KCksdGhpcy5hdHRhY2hGaWxlcyh0LmNsaXBib2FyZERhdGEuZmlsZXMpKTpwKHQpPyh0LnByZXZlbnREZWZhdWx0KCksbj17dHlwZTpcInRleHQvcGxhaW5cIixzdHJpbmc6dC5jbGlwYm9hcmREYXRhLmdldERhdGEoXCJ0ZXh0L3BsYWluXCIpfSxudWxsIT0oaT10aGlzLmRlbGVnYXRlKSYmaS5pbnB1dENvbnRyb2xsZXJXaWxsUGFzdGUobiksbnVsbCE9KG89dGhpcy5yZXNwb25kZXIpJiZvLmluc2VydFN0cmluZyhuLnN0cmluZyksdGhpcy5yZW5kZXIoKSxudWxsIT0ocj10aGlzLmRlbGVnYXRlKT9yLmlucHV0Q29udHJvbGxlckRpZFBhc3RlKG4pOnZvaWQgMCk6KGU9bnVsbCE9KHM9dC5jbGlwYm9hcmREYXRhKT9zLmdldERhdGEoXCJVUkxcIik6dm9pZCAwKT8odC5wcmV2ZW50RGVmYXVsdCgpLG49e3R5cGU6XCJ0ZXh0L2h0bWxcIixodG1sOnRoaXMuY3JlYXRlTGlua0hUTUwoZSl9LG51bGwhPShhPXRoaXMuZGVsZWdhdGUpJiZhLmlucHV0Q29udHJvbGxlcldpbGxQYXN0ZShuKSxudWxsIT0odT10aGlzLnJlc3BvbmRlcikmJnUuaW5zZXJ0SFRNTChuLmh0bWwpLHRoaXMucmVuZGVyKCksbnVsbCE9KGM9dGhpcy5kZWxlZ2F0ZSk/Yy5pbnB1dENvbnRyb2xsZXJEaWRQYXN0ZShuKTp2b2lkIDApOnZvaWQgMH0sYmVmb3JlaW5wdXQ6ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuKGU9dGhpcy5pbnB1dFR5cGVzW3QuaW5wdXRUeXBlXSk/KHRoaXMud2l0aEV2ZW50KHQsZSksdGhpcy5zY2hlZHVsZVJlbmRlcigpKTp2b2lkIDB9LGlucHV0OmZ1bmN0aW9uKCl7cmV0dXJuIGUuc2VsZWN0aW9uQ2hhbmdlT2JzZXJ2ZXIucmVzZXQoKX0sZHJhZ3N0YXJ0OmZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuKG51bGwhPShlPXRoaXMucmVzcG9uZGVyKT9lLnNlbGVjdGlvbkNvbnRhaW5zQXR0YWNobWVudHMoKTp2b2lkIDApPyh0LmRhdGFUcmFuc2Zlci5zZXREYXRhKFwiYXBwbGljYXRpb24veC10cml4LWRyYWdnaW5nXCIsITApLHRoaXMuZHJhZ2dpbmc9e3JhbmdlOm51bGwhPShuPXRoaXMucmVzcG9uZGVyKT9uLmdldFNlbGVjdGVkUmFuZ2UoKTp2b2lkIDAscG9pbnQ6ZCh0KX0pOnZvaWQgMH0sZHJhZ2VudGVyOmZ1bmN0aW9uKHQpe3JldHVybiBjKHQpP3QucHJldmVudERlZmF1bHQoKTp2b2lkIDB9LGRyYWdvdmVyOmZ1bmN0aW9uKHQpe3ZhciBlLG47aWYodGhpcy5kcmFnZ2luZyl7aWYodC5wcmV2ZW50RGVmYXVsdCgpLGU9ZCh0KSwhaShlLHRoaXMuZHJhZ2dpbmcucG9pbnQpKXJldHVybiB0aGlzLmRyYWdnaW5nLnBvaW50PWUsbnVsbCE9KG49dGhpcy5yZXNwb25kZXIpP24uc2V0TG9jYXRpb25SYW5nZUZyb21Qb2ludFJhbmdlKGUpOnZvaWQgMH1lbHNlIGlmKGModCkpcmV0dXJuIHQucHJldmVudERlZmF1bHQoKX0sZHJvcDpmdW5jdGlvbih0KXt2YXIgZSxuLGksbztyZXR1cm4gdGhpcy5kcmFnZ2luZz8odC5wcmV2ZW50RGVmYXVsdCgpLG51bGwhPShuPXRoaXMuZGVsZWdhdGUpJiZuLmlucHV0Q29udHJvbGxlcldpbGxNb3ZlVGV4dCgpLG51bGwhPShpPXRoaXMucmVzcG9uZGVyKSYmaS5tb3ZlVGV4dEZyb21SYW5nZSh0aGlzLmRyYWdnaW5nLnJhbmdlKSx0aGlzLmRyYWdnaW5nPW51bGwsdGhpcy5zY2hlZHVsZVJlbmRlcigpKTpjKHQpPyh0LnByZXZlbnREZWZhdWx0KCksZT1kKHQpLG51bGwhPShvPXRoaXMucmVzcG9uZGVyKSYmby5zZXRMb2NhdGlvblJhbmdlRnJvbVBvaW50UmFuZ2UoZSksdGhpcy5hdHRhY2hGaWxlcyh0LmRhdGFUcmFuc2Zlci5maWxlcykpOnZvaWQgMH0sZHJhZ2VuZDpmdW5jdGlvbigpe3ZhciB0O3JldHVybiB0aGlzLmRyYWdnaW5nPyhudWxsIT0odD10aGlzLnJlc3BvbmRlcikmJnQuc2V0U2VsZWN0ZWRSYW5nZSh0aGlzLmRyYWdnaW5nLnJhbmdlKSx0aGlzLmRyYWdnaW5nPW51bGwpOnZvaWQgMH0sY29tcG9zaXRpb25lbmQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb21wb3Npbmc/KHRoaXMuY29tcG9zaW5nPSExLHRoaXMuc2NoZWR1bGVSZW5kZXIoKSk6dm9pZCAwfX0sdS5wcm90b3R5cGUua2V5cz17QXJyb3dMZWZ0OmZ1bmN0aW9uKCl7dmFyIHQsZTtyZXR1cm4obnVsbCE9KHQ9dGhpcy5yZXNwb25kZXIpP3Quc2hvdWxkTWFuYWdlTW92aW5nQ3Vyc29ySW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiKTp2b2lkIDApPyh0aGlzLmV2ZW50LnByZXZlbnREZWZhdWx0KCksbnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2UubW92ZUN1cnNvckluRGlyZWN0aW9uKFwiYmFja3dhcmRcIik6dm9pZCAwKTp2b2lkIDB9LEFycm93UmlnaHQ6ZnVuY3Rpb24oKXt2YXIgdCxlO3JldHVybihudWxsIT0odD10aGlzLnJlc3BvbmRlcik/dC5zaG91bGRNYW5hZ2VNb3ZpbmdDdXJzb3JJbkRpcmVjdGlvbihcImZvcndhcmRcIik6dm9pZCAwKT8odGhpcy5ldmVudC5wcmV2ZW50RGVmYXVsdCgpLG51bGwhPShlPXRoaXMucmVzcG9uZGVyKT9lLm1vdmVDdXJzb3JJbkRpcmVjdGlvbihcImZvcndhcmRcIik6dm9pZCAwKTp2b2lkIDB9LEJhY2tzcGFjZTpmdW5jdGlvbigpe3ZhciB0LGUsbjtyZXR1cm4obnVsbCE9KHQ9dGhpcy5yZXNwb25kZXIpP3Quc2hvdWxkTWFuYWdlRGVsZXRpbmdJbkRpcmVjdGlvbihcImJhY2t3YXJkXCIpOnZvaWQgMCk/KHRoaXMuZXZlbnQucHJldmVudERlZmF1bHQoKSxudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmZS5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpLG51bGwhPShuPXRoaXMucmVzcG9uZGVyKSYmbi5kZWxldGVJbkRpcmVjdGlvbihcImJhY2t3YXJkXCIpLHRoaXMucmVuZGVyKCkpOnZvaWQgMH0sVGFiOmZ1bmN0aW9uKCl7dmFyIHQsZTtyZXR1cm4obnVsbCE9KHQ9dGhpcy5yZXNwb25kZXIpP3QuY2FuSW5jcmVhc2VOZXN0aW5nTGV2ZWwoKTp2b2lkIDApPyh0aGlzLmV2ZW50LnByZXZlbnREZWZhdWx0KCksbnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpJiZlLmluY3JlYXNlTmVzdGluZ0xldmVsKCksdGhpcy5yZW5kZXIoKSk6dm9pZCAwfSxcIlRhYitTaGlmdFwiOmZ1bmN0aW9uKCl7dmFyIHQsZTtyZXR1cm4obnVsbCE9KHQ9dGhpcy5yZXNwb25kZXIpP3QuY2FuRGVjcmVhc2VOZXN0aW5nTGV2ZWwoKTp2b2lkIDApPyh0aGlzLmV2ZW50LnByZXZlbnREZWZhdWx0KCksbnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpJiZlLmRlY3JlYXNlTmVzdGluZ0xldmVsKCksdGhpcy5yZW5kZXIoKSk6dm9pZCAwfX0sdS5wcm90b3R5cGUuaW5wdXRUeXBlcz17ZGVsZXRlQnlDb21wb3NpdGlvbjpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiYmFja3dhcmRcIix7cmVjb3JkVW5kb0VudHJ5OiExfSl9LGRlbGV0ZUJ5Q3V0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZGVsZXRlSW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiKX0sZGVsZXRlQnlEcmFnOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZXZlbnQucHJldmVudERlZmF1bHQoKSx0aGlzLndpdGhUYXJnZXRET01SYW5nZShmdW5jdGlvbigpe3ZhciB0O3JldHVybiB0aGlzLmRlbGV0ZUJ5RHJhZ1JhbmdlPW51bGwhPSh0PXRoaXMucmVzcG9uZGVyKT90LmdldFNlbGVjdGVkUmFuZ2UoKTp2b2lkIDB9KX0sZGVsZXRlQ29tcG9zaXRpb25UZXh0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZGVsZXRlSW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiLHtyZWNvcmRVbmRvRW50cnk6ITF9KX0sZGVsZXRlQ29udGVudDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiYmFja3dhcmRcIil9LGRlbGV0ZUNvbnRlbnRCYWNrd2FyZDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiYmFja3dhcmRcIil9LGRlbGV0ZUNvbnRlbnRGb3J3YXJkOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZGVsZXRlSW5EaXJlY3Rpb24oXCJmb3J3YXJkXCIpfSxkZWxldGVFbnRpcmVTb2Z0TGluZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiZm9yd2FyZFwiKX0sZGVsZXRlSGFyZExpbmVCYWNrd2FyZDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiYmFja3dhcmRcIil9LGRlbGV0ZUhhcmRMaW5lRm9yd2FyZDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiZm9yd2FyZFwiKX0sZGVsZXRlU29mdExpbmVCYWNrd2FyZDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiYmFja3dhcmRcIil9LGRlbGV0ZVNvZnRMaW5lRm9yd2FyZDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmRlbGV0ZUluRGlyZWN0aW9uKFwiZm9yd2FyZFwiKX0sZGVsZXRlV29yZEJhY2t3YXJkOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZGVsZXRlSW5EaXJlY3Rpb24oXCJiYWNrd2FyZFwiKX0sZGVsZXRlV29yZEZvcndhcmQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5kZWxldGVJbkRpcmVjdGlvbihcImZvcndhcmRcIil9LGZvcm1hdEJhY2tDb2xvcjpmdW5jdGlvbigpe3JldHVybiB0aGlzLmFjdGl2YXRlQXR0cmlidXRlSWZTdXBwb3J0ZWQoXCJiYWNrZ3JvdW5kQ29sb3JcIix0aGlzLmV2ZW50LmRhdGEpfSxmb3JtYXRCb2xkOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudG9nZ2xlQXR0cmlidXRlSWZTdXBwb3J0ZWQoXCJib2xkXCIpfSxmb3JtYXRGb250Q29sb3I6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hY3RpdmF0ZUF0dHJpYnV0ZUlmU3VwcG9ydGVkKFwiY29sb3JcIix0aGlzLmV2ZW50LmRhdGEpfSxmb3JtYXRGb250TmFtZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmFjdGl2YXRlQXR0cmlidXRlSWZTdXBwb3J0ZWQoXCJmb250XCIsdGhpcy5ldmVudC5kYXRhKX0sZm9ybWF0SW5kZW50OmZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKG51bGwhPSh0PXRoaXMucmVzcG9uZGVyKT90LmNhbkluY3JlYXNlTmVzdGluZ0xldmVsKCk6dm9pZCAwKT90aGlzLndpdGhUYXJnZXRET01SYW5nZShmdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLnJlc3BvbmRlcik/dC5pbmNyZWFzZU5lc3RpbmdMZXZlbCgpOnZvaWQgMH0pOnZvaWQgMH0sZm9ybWF0SXRhbGljOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudG9nZ2xlQXR0cmlidXRlSWZTdXBwb3J0ZWQoXCJpdGFsaWNcIil9LGZvcm1hdEp1c3RpZnlDZW50ZXI6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy50b2dnbGVBdHRyaWJ1dGVJZlN1cHBvcnRlZChcImp1c3RpZnlDZW50ZXJcIil9LGZvcm1hdEp1c3RpZnlGdWxsOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudG9nZ2xlQXR0cmlidXRlSWZTdXBwb3J0ZWQoXCJqdXN0aWZ5RnVsbFwiKX0sZm9ybWF0SnVzdGlmeUxlZnQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy50b2dnbGVBdHRyaWJ1dGVJZlN1cHBvcnRlZChcImp1c3RpZnlMZWZ0XCIpfSxmb3JtYXRKdXN0aWZ5UmlnaHQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy50b2dnbGVBdHRyaWJ1dGVJZlN1cHBvcnRlZChcImp1c3RpZnlSaWdodFwiKX0sZm9ybWF0T3V0ZGVudDpmdW5jdGlvbigpe3ZhciB0O3JldHVybihudWxsIT0odD10aGlzLnJlc3BvbmRlcik/dC5jYW5EZWNyZWFzZU5lc3RpbmdMZXZlbCgpOnZvaWQgMCk/dGhpcy53aXRoVGFyZ2V0RE9NUmFuZ2UoZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5yZXNwb25kZXIpP3QuZGVjcmVhc2VOZXN0aW5nTGV2ZWwoKTp2b2lkIDB9KTp2b2lkIDB9LGZvcm1hdFJlbW92ZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLndpdGhUYXJnZXRET01SYW5nZShmdW5jdGlvbigpe3ZhciB0LGUsbixpO2k9W107Zm9yKHQgaW4gbnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2UuZ2V0Q3VycmVudEF0dHJpYnV0ZXMoKTp2b2lkIDApaS5wdXNoKG51bGwhPShuPXRoaXMucmVzcG9uZGVyKT9uLnJlbW92ZUN1cnJlbnRBdHRyaWJ1dGUodCk6dm9pZCAwKTtyZXR1cm4gaX0pfSxmb3JtYXRTZXRCbG9ja1RleHREaXJlY3Rpb246ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hY3RpdmF0ZUF0dHJpYnV0ZUlmU3VwcG9ydGVkKFwiYmxvY2tEaXJcIix0aGlzLmV2ZW50LmRhdGEpfSxmb3JtYXRTZXRJbmxpbmVUZXh0RGlyZWN0aW9uOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYWN0aXZhdGVBdHRyaWJ1dGVJZlN1cHBvcnRlZChcInRleHREaXJcIix0aGlzLmV2ZW50LmRhdGEpfSxmb3JtYXRTdHJpa2VUaHJvdWdoOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudG9nZ2xlQXR0cmlidXRlSWZTdXBwb3J0ZWQoXCJzdHJpa2VcIil9LGZvcm1hdFN1YnNjcmlwdDpmdW5jdGlvbigpe3JldHVybiB0aGlzLnRvZ2dsZUF0dHJpYnV0ZUlmU3VwcG9ydGVkKFwic3ViXCIpfSxmb3JtYXRTdXBlcnNjcmlwdDpmdW5jdGlvbigpe3JldHVybiB0aGlzLnRvZ2dsZUF0dHJpYnV0ZUlmU3VwcG9ydGVkKFwic3VwXCIpfSxmb3JtYXRVbmRlcmxpbmU6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy50b2dnbGVBdHRyaWJ1dGVJZlN1cHBvcnRlZChcInVuZGVybGluZVwiKX0saGlzdG9yeVJlZG86ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5kZWxlZ2F0ZSk/dC5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVJlZG8oKTp2b2lkIDB9LGhpc3RvcnlVbmRvOmZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMuZGVsZWdhdGUpP3QuaW5wdXRDb250cm9sbGVyV2lsbFBlcmZvcm1VbmRvKCk6dm9pZCAwfSxpbnNlcnRDb21wb3NpdGlvblRleHQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb21wb3Npbmc9ITAsdGhpcy5pbnNlcnRTdHJpbmcodGhpcy5ldmVudC5kYXRhKX0saW5zZXJ0RnJvbUNvbXBvc2l0aW9uOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29tcG9zaW5nPSExLHRoaXMuaW5zZXJ0U3RyaW5nKHRoaXMuZXZlbnQuZGF0YSl9LGluc2VydEZyb21Ecm9wOmZ1bmN0aW9uKCl7dmFyIHQsZTtyZXR1cm4odD10aGlzLmRlbGV0ZUJ5RHJhZ1JhbmdlKT8odGhpcy5kZWxldGVCeURyYWdSYW5nZT1udWxsLG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZlLmlucHV0Q29udHJvbGxlcldpbGxNb3ZlVGV4dCgpLHRoaXMud2l0aFRhcmdldERPTVJhbmdlKGZ1bmN0aW9uKCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMucmVzcG9uZGVyKT9lLm1vdmVUZXh0RnJvbVJhbmdlKHQpOnZvaWQgMH0pKTp2b2lkIDB9LGluc2VydEZyb21QYXN0ZTpmdW5jdGlvbigpe3ZhciBuLGksbyxyLHMsYSx1LGMsbCxoLHA7cmV0dXJuIG49dGhpcy5ldmVudC5kYXRhVHJhbnNmZXIscz17ZGF0YVRyYW5zZmVyOm59LChpPW4uZ2V0RGF0YShcIlVSTFwiKSk/KHRoaXMuZXZlbnQucHJldmVudERlZmF1bHQoKSxzLnR5cGU9XCJ0ZXh0L2h0bWxcIixwPShyPW4uZ2V0RGF0YShcInB1YmxpYy51cmwtbmFtZVwiKSk/ZS5zcXVpc2hCcmVha2FibGVXaGl0ZXNwYWNlKHIpLnRyaW0oKTppLHMuaHRtbD10aGlzLmNyZWF0ZUxpbmtIVE1MKGkscCksbnVsbCE9KGE9dGhpcy5kZWxlZ2F0ZSkmJmEuaW5wdXRDb250cm9sbGVyV2lsbFBhc3RlKHMpLHRoaXMud2l0aFRhcmdldERPTVJhbmdlKGZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMucmVzcG9uZGVyKT90Lmluc2VydEhUTUwocy5odG1sKTp2b2lkIDB9KSx0aGlzLmFmdGVyUmVuZGVyPWZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbigpe3ZhciBlO3JldHVybiBudWxsIT0oZT10LmRlbGVnYXRlKT9lLmlucHV0Q29udHJvbGxlckRpZFBhc3RlKHMpOnZvaWQgMH19KHRoaXMpKTp0KG4pPyhzLnR5cGU9XCJ0ZXh0L3BsYWluXCIscy5zdHJpbmc9bi5nZXREYXRhKFwidGV4dC9wbGFpblwiKSxudWxsIT0odT10aGlzLmRlbGVnYXRlKSYmdS5pbnB1dENvbnRyb2xsZXJXaWxsUGFzdGUocyksdGhpcy53aXRoVGFyZ2V0RE9NUmFuZ2UoZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5yZXNwb25kZXIpP3QuaW5zZXJ0U3RyaW5nKHMuc3RyaW5nKTp2b2lkIDB9KSx0aGlzLmFmdGVyUmVuZGVyPWZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbigpe3ZhciBlO3JldHVybiBudWxsIT0oZT10LmRlbGVnYXRlKT9lLmlucHV0Q29udHJvbGxlckRpZFBhc3RlKHMpOnZvaWQgMH19KHRoaXMpKToobz1uLmdldERhdGEoXCJ0ZXh0L2h0bWxcIikpPyh0aGlzLmV2ZW50LnByZXZlbnREZWZhdWx0KCkscy50eXBlPVwidGV4dC9odG1sXCIscy5odG1sPW8sbnVsbCE9KGM9dGhpcy5kZWxlZ2F0ZSkmJmMuaW5wdXRDb250cm9sbGVyV2lsbFBhc3RlKHMpLHRoaXMud2l0aFRhcmdldERPTVJhbmdlKGZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMucmVzcG9uZGVyKT90Lmluc2VydEhUTUwocy5odG1sKTp2b2lkIDB9KSx0aGlzLmFmdGVyUmVuZGVyPWZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbigpe3ZhciBlO3JldHVybiBudWxsIT0oZT10LmRlbGVnYXRlKT9lLmlucHV0Q29udHJvbGxlckRpZFBhc3RlKHMpOnZvaWQgMH19KHRoaXMpKToobnVsbCE9KGw9bi5maWxlcyk/bC5sZW5ndGg6dm9pZCAwKT8ocy50eXBlPVwiRmlsZVwiLHMuZmlsZT1uLmZpbGVzWzBdLG51bGwhPShoPXRoaXMuZGVsZWdhdGUpJiZoLmlucHV0Q29udHJvbGxlcldpbGxQYXN0ZShzKSx0aGlzLndpdGhUYXJnZXRET01SYW5nZShmdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLnJlc3BvbmRlcik/dC5pbnNlcnRGaWxlKHMuZmlsZSk6dm9pZCAwfSksdGhpcy5hZnRlclJlbmRlcj1mdW5jdGlvbih0KXtyZXR1cm4gZnVuY3Rpb24oKXt2YXIgZTtyZXR1cm4gbnVsbCE9KGU9dC5kZWxlZ2F0ZSk/ZS5pbnB1dENvbnRyb2xsZXJEaWRQYXN0ZShzKTp2b2lkIDB9fSh0aGlzKSk6dm9pZCAwfSxpbnNlcnRGcm9tWWFuazpmdW5jdGlvbigpe3JldHVybiB0aGlzLmluc2VydFN0cmluZyh0aGlzLmV2ZW50LmRhdGEpfSxpbnNlcnRMaW5lQnJlYWs6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pbnNlcnRTdHJpbmcoXCJcXG5cIil9LGluc2VydExpbms6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hY3RpdmF0ZUF0dHJpYnV0ZUlmU3VwcG9ydGVkKFwiaHJlZlwiLHRoaXMuZXZlbnQuZGF0YSl9LGluc2VydE9yZGVyZWRMaXN0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudG9nZ2xlQXR0cmlidXRlSWZTdXBwb3J0ZWQoXCJudW1iZXJcIil9LGluc2VydFBhcmFncmFwaDpmdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLmRlbGVnYXRlKSYmdC5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpLHRoaXMud2l0aFRhcmdldERPTVJhbmdlKGZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMucmVzcG9uZGVyKT90Lmluc2VydExpbmVCcmVhaygpOnZvaWQgMH0pfSxpbnNlcnRSZXBsYWNlbWVudFRleHQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pbnNlcnRTdHJpbmcodGhpcy5ldmVudC5kYXRhVHJhbnNmZXIuZ2V0RGF0YShcInRleHQvcGxhaW5cIikse3VwZGF0ZVBvc2l0aW9uOiExfSl9LGluc2VydFRleHQ6ZnVuY3Rpb24oKXt2YXIgdCxlO3JldHVybiB0aGlzLmluc2VydFN0cmluZyhudWxsIT0odD10aGlzLmV2ZW50LmRhdGEpP3Q6bnVsbCE9KGU9dGhpcy5ldmVudC5kYXRhVHJhbnNmZXIpP2UuZ2V0RGF0YShcInRleHQvcGxhaW5cIik6dm9pZCAwKX0saW5zZXJ0VHJhbnNwb3NlOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuaW5zZXJ0U3RyaW5nKHRoaXMuZXZlbnQuZGF0YSl9LGluc2VydFVub3JkZXJlZExpc3Q6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy50b2dnbGVBdHRyaWJ1dGVJZlN1cHBvcnRlZChcImJ1bGxldFwiKX19LHUucHJvdG90eXBlLmluc2VydFN0cmluZz1mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybiBudWxsPT10JiYodD1cIlwiKSxudWxsIT0obj10aGlzLmRlbGVnYXRlKSYmbi5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybVR5cGluZygpLHRoaXMud2l0aFRhcmdldERPTVJhbmdlKGZ1bmN0aW9uKCl7dmFyIG47cmV0dXJuIG51bGwhPShuPXRoaXMucmVzcG9uZGVyKT9uLmluc2VydFN0cmluZyh0LGUpOnZvaWQgMH0pfSx1LnByb3RvdHlwZS50b2dnbGVBdHRyaWJ1dGVJZlN1cHBvcnRlZD1mdW5jdGlvbih0KXt2YXIgbjtyZXR1cm4gYS5jYWxsKGUuZ2V0QWxsQXR0cmlidXRlTmFtZXMoKSx0KT49MD8obnVsbCE9KG49dGhpcy5kZWxlZ2F0ZSkmJm4uaW5wdXRDb250cm9sbGVyV2lsbFBlcmZvcm1Gb3JtYXR0aW5nKHQpLHRoaXMud2l0aFRhcmdldERPTVJhbmdlKGZ1bmN0aW9uKCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMucmVzcG9uZGVyKT9lLnRvZ2dsZUN1cnJlbnRBdHRyaWJ1dGUodCk6dm9pZCAwfSkpOnZvaWQgMH0sdS5wcm90b3R5cGUuYWN0aXZhdGVBdHRyaWJ1dGVJZlN1cHBvcnRlZD1mdW5jdGlvbih0LG4pe3ZhciBpO3JldHVybiBhLmNhbGwoZS5nZXRBbGxBdHRyaWJ1dGVOYW1lcygpLHQpPj0wPyhudWxsIT0oaT10aGlzLmRlbGVnYXRlKSYmaS5pbnB1dENvbnRyb2xsZXJXaWxsUGVyZm9ybUZvcm1hdHRpbmcodCksdGhpcy53aXRoVGFyZ2V0RE9NUmFuZ2UoZnVuY3Rpb24oKXt2YXIgZTtyZXR1cm4gbnVsbCE9KGU9dGhpcy5yZXNwb25kZXIpP2Uuc2V0Q3VycmVudEF0dHJpYnV0ZSh0LG4pOnZvaWQgMH0pKTp2b2lkIDB9LHUucHJvdG90eXBlLmRlbGV0ZUluRGlyZWN0aW9uPWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvLHI7cmV0dXJuIG89KG51bGwhPWU/ZTp7cmVjb3JkVW5kb0VudHJ5OiEwfSkucmVjb3JkVW5kb0VudHJ5LG8mJm51bGwhPShyPXRoaXMuZGVsZWdhdGUpJiZyLmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtVHlwaW5nKCksaT1mdW5jdGlvbihlKXtyZXR1cm4gZnVuY3Rpb24oKXt2YXIgbjtyZXR1cm4gbnVsbCE9KG49ZS5yZXNwb25kZXIpP24uZGVsZXRlSW5EaXJlY3Rpb24odCk6dm9pZCAwfX0odGhpcyksKG49dGhpcy5nZXRUYXJnZXRET01SYW5nZSh7bWluTGVuZ3RoOjJ9KSk/dGhpcy53aXRoVGFyZ2V0RE9NUmFuZ2UobixpKTppKCl9LHUucHJvdG90eXBlLndpdGhUYXJnZXRET01SYW5nZT1mdW5jdGlvbih0LG4pe3ZhciBpO3JldHVyblwiZnVuY3Rpb25cIj09dHlwZW9mIHQmJihuPXQsdD10aGlzLmdldFRhcmdldERPTVJhbmdlKCkpLHQ/bnVsbCE9KGk9dGhpcy5yZXNwb25kZXIpP2kud2l0aFRhcmdldERPTVJhbmdlKHQsbi5iaW5kKHRoaXMpKTp2b2lkIDA6KGUuc2VsZWN0aW9uQ2hhbmdlT2JzZXJ2ZXIucmVzZXQoKSxuLmNhbGwodGhpcykpfSx1LnByb3RvdHlwZS5nZXRUYXJnZXRET01SYW5nZT1mdW5jdGlvbih0KXt2YXIgZSxuLGksbztyZXR1cm4gaT0obnVsbCE9dD90OnttaW5MZW5ndGg6MH0pLm1pbkxlbmd0aCwobz1cImZ1bmN0aW9uXCI9PXR5cGVvZihlPXRoaXMuZXZlbnQpLmdldFRhcmdldFJhbmdlcz9lLmdldFRhcmdldFJhbmdlcygpOnZvaWQgMCkmJm8ubGVuZ3RoJiYobj1mKG9bMF0pLDA9PT1pfHxuLnRvU3RyaW5nKCkubGVuZ3RoPj1pKT9uOnZvaWQgMH0sZj1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT1kb2N1bWVudC5jcmVhdGVSYW5nZSgpLGUuc2V0U3RhcnQodC5zdGFydENvbnRhaW5lcix0LnN0YXJ0T2Zmc2V0KSxlLnNldEVuZCh0LmVuZENvbnRhaW5lcix0LmVuZE9mZnNldCksZX0sdS5wcm90b3R5cGUud2l0aEV2ZW50PWZ1bmN0aW9uKHQsZSl7dmFyIG47dGhpcy5ldmVudD10O3RyeXtuPWUuY2FsbCh0aGlzKX1maW5hbGx5e3RoaXMuZXZlbnQ9bnVsbH1yZXR1cm4gbn0sYz1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiBhLmNhbGwobnVsbCE9KGU9bnVsbCE9KG49dC5kYXRhVHJhbnNmZXIpP24udHlwZXM6dm9pZCAwKT9lOltdLFwiRmlsZXNcIik+PTB9LGg9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuKGU9dC5jbGlwYm9hcmREYXRhKT9hLmNhbGwoZS50eXBlcyxcIkZpbGVzXCIpPj0wJiYxPT09ZS50eXBlcy5sZW5ndGgmJmUuZmlsZXMubGVuZ3RoPj0xOnZvaWQgMH0scD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4oZT10LmNsaXBib2FyZERhdGEpP2EuY2FsbChlLnR5cGVzLFwidGV4dC9wbGFpblwiKT49MCYmMT09PWUudHlwZXMubGVuZ3RoOnZvaWQgMH0sbD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT1bXSx0LmFsdEtleSYmZS5wdXNoKFwiYWx0XCIpLHQuc2hpZnRLZXkmJmUucHVzaChcInNoaWZ0XCIpLGUucHVzaCh0LmtleSksZX0sZD1mdW5jdGlvbih0KXtyZXR1cm57eDp0LmNsaWVudFgseTp0LmNsaWVudFl9fSx1fShlLklucHV0Q29udHJvbGxlcil9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbyxyLHMsYSx1LGM9ZnVuY3Rpb24odCxlKXtyZXR1cm4gZnVuY3Rpb24oKXtyZXR1cm4gdC5hcHBseShlLGFyZ3VtZW50cyl9fSxsPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIGkgaW4gZSloLmNhbGwoZSxpKSYmKHRbaV09ZVtpXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LGg9e30uaGFzT3duUHJvcGVydHk7bj1lLmRlZmVyLGk9ZS5oYW5kbGVFdmVudCxzPWUubWFrZUVsZW1lbnQsdT1lLnRhZ05hbWUsYT1lLmNvbmZpZyxyPWEubGFuZyx0PWEuY3NzLG89YS5rZXlOYW1lcyxlLkF0dGFjaG1lbnRFZGl0b3JDb250cm9sbGVyPWZ1bmN0aW9uKGEpe2Z1bmN0aW9uIGgodCxlLG4saSl7dGhpcy5hdHRhY2htZW50UGllY2U9dCx0aGlzLmVsZW1lbnQ9ZSx0aGlzLmNvbnRhaW5lcj1uLHRoaXMub3B0aW9ucz1udWxsIT1pP2k6e30sdGhpcy5kaWRCbHVyQ2FwdGlvbj1jKHRoaXMuZGlkQmx1ckNhcHRpb24sdGhpcyksdGhpcy5kaWRDaGFuZ2VDYXB0aW9uPWModGhpcy5kaWRDaGFuZ2VDYXB0aW9uLHRoaXMpLHRoaXMuZGlkSW5wdXRDYXB0aW9uPWModGhpcy5kaWRJbnB1dENhcHRpb24sdGhpcyksdGhpcy5kaWRLZXlEb3duQ2FwdGlvbj1jKHRoaXMuZGlkS2V5RG93bkNhcHRpb24sdGhpcyksdGhpcy5kaWRDbGlja0FjdGlvbkJ1dHRvbj1jKHRoaXMuZGlkQ2xpY2tBY3Rpb25CdXR0b24sdGhpcyksdGhpcy5kaWRDbGlja1Rvb2xiYXI9Yyh0aGlzLmRpZENsaWNrVG9vbGJhcix0aGlzKSx0aGlzLmF0dGFjaG1lbnQ9dGhpcy5hdHRhY2htZW50UGllY2UuYXR0YWNobWVudCxcImFcIj09PXUodGhpcy5lbGVtZW50KSYmKHRoaXMuZWxlbWVudD10aGlzLmVsZW1lbnQuZmlyc3RDaGlsZCksdGhpcy5pbnN0YWxsKCl9dmFyIHA7cmV0dXJuIGwoaCxhKSxwPWZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbigpe3ZhciBlO3JldHVybiBlPXQuYXBwbHkodGhpcyxhcmd1bWVudHMpLGVbXCJkb1wiXSgpLG51bGw9PXRoaXMudW5kb3MmJih0aGlzLnVuZG9zPVtdKSx0aGlzLnVuZG9zLnB1c2goZS51bmRvKX19LGgucHJvdG90eXBlLmluc3RhbGw9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5tYWtlRWxlbWVudE11dGFibGUoKSx0aGlzLmFkZFRvb2xiYXIoKSx0aGlzLmF0dGFjaG1lbnQuaXNQcmV2aWV3YWJsZSgpP3RoaXMuaW5zdGFsbENhcHRpb25FZGl0b3IoKTp2b2lkIDB9LGgucHJvdG90eXBlLnVuaW5zdGFsbD1mdW5jdGlvbigpe3ZhciB0LGU7Zm9yKHRoaXMuc2F2ZVBlbmRpbmdDYXB0aW9uKCk7ZT10aGlzLnVuZG9zLnBvcCgpOyllKCk7cmV0dXJuIG51bGwhPSh0PXRoaXMuZGVsZWdhdGUpP3QuZGlkVW5pbnN0YWxsQXR0YWNobWVudEVkaXRvcih0aGlzKTp2b2lkIDB9LGgucHJvdG90eXBlLnNhdmVQZW5kaW5nQ2FwdGlvbj1mdW5jdGlvbigpe3ZhciB0LGUsbjtyZXR1cm4gbnVsbCE9dGhpcy5wZW5kaW5nQ2FwdGlvbj8odD10aGlzLnBlbmRpbmdDYXB0aW9uLHRoaXMucGVuZGluZ0NhcHRpb249bnVsbCx0P251bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBlLmF0dGFjaG1lbnRFZGl0b3JEaWRSZXF1ZXN0VXBkYXRpbmdBdHRyaWJ1dGVzRm9yQXR0YWNobWVudD9lLmF0dGFjaG1lbnRFZGl0b3JEaWRSZXF1ZXN0VXBkYXRpbmdBdHRyaWJ1dGVzRm9yQXR0YWNobWVudCh7Y2FwdGlvbjp0fSx0aGlzLmF0dGFjaG1lbnQpOnZvaWQgMDpudWxsIT0obj10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2Ygbi5hdHRhY2htZW50RWRpdG9yRGlkUmVxdWVzdFJlbW92aW5nQXR0cmlidXRlRm9yQXR0YWNobWVudD9uLmF0dGFjaG1lbnRFZGl0b3JEaWRSZXF1ZXN0UmVtb3ZpbmdBdHRyaWJ1dGVGb3JBdHRhY2htZW50KFwiY2FwdGlvblwiLHRoaXMuYXR0YWNobWVudCk6dm9pZCAwKTp2b2lkIDB9LGgucHJvdG90eXBlLm1ha2VFbGVtZW50TXV0YWJsZT1wKGZ1bmN0aW9uKCl7cmV0dXJue1wiZG9cIjpmdW5jdGlvbih0KXtyZXR1cm4gZnVuY3Rpb24oKXtyZXR1cm4gdC5lbGVtZW50LmRhdGFzZXQudHJpeE11dGFibGU9ITB9fSh0aGlzKSx1bmRvOmZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbigpe3JldHVybiBkZWxldGUgdC5lbGVtZW50LmRhdGFzZXQudHJpeE11dGFibGV9fSh0aGlzKX19KSxoLnByb3RvdHlwZS5hZGRUb29sYmFyPXAoZnVuY3Rpb24oKXt2YXIgbjtyZXR1cm4gbj1zKHt0YWdOYW1lOlwiZGl2XCIsY2xhc3NOYW1lOnQuYXR0YWNobWVudFRvb2xiYXIsZGF0YTp7dHJpeE11dGFibGU6ITB9LGNoaWxkTm9kZXM6cyh7dGFnTmFtZTpcImRpdlwiLGNsYXNzTmFtZTpcInRyaXgtYnV0dG9uLXJvd1wiLGNoaWxkTm9kZXM6cyh7dGFnTmFtZTpcInNwYW5cIixjbGFzc05hbWU6XCJ0cml4LWJ1dHRvbi1ncm91cCB0cml4LWJ1dHRvbi1ncm91cC0tYWN0aW9uc1wiLGNoaWxkTm9kZXM6cyh7dGFnTmFtZTpcImJ1dHRvblwiLGNsYXNzTmFtZTpcInRyaXgtYnV0dG9uIHRyaXgtYnV0dG9uLS1yZW1vdmVcIix0ZXh0Q29udGVudDpyLnJlbW92ZSxhdHRyaWJ1dGVzOnt0aXRsZTpyLnJlbW92ZX0sZGF0YTp7dHJpeEFjdGlvbjpcInJlbW92ZVwifX0pfSl9KX0pLHRoaXMuYXR0YWNobWVudC5pc1ByZXZpZXdhYmxlKCkmJm4uYXBwZW5kQ2hpbGQocyh7dGFnTmFtZTpcImRpdlwiLGNsYXNzTmFtZTp0LmF0dGFjaG1lbnRNZXRhZGF0YUNvbnRhaW5lcixjaGlsZE5vZGVzOnMoe3RhZ05hbWU6XCJzcGFuXCIsY2xhc3NOYW1lOnQuYXR0YWNobWVudE1ldGFkYXRhLGNoaWxkTm9kZXM6W3Moe3RhZ05hbWU6XCJzcGFuXCIsY2xhc3NOYW1lOnQuYXR0YWNobWVudE5hbWUsdGV4dENvbnRlbnQ6dGhpcy5hdHRhY2htZW50LmdldEZpbGVuYW1lKCksYXR0cmlidXRlczp7dGl0bGU6dGhpcy5hdHRhY2htZW50LmdldEZpbGVuYW1lKCl9fSkscyh7dGFnTmFtZTpcInNwYW5cIixjbGFzc05hbWU6dC5hdHRhY2htZW50U2l6ZSx0ZXh0Q29udGVudDp0aGlzLmF0dGFjaG1lbnQuZ2V0Rm9ybWF0dGVkRmlsZXNpemUoKX0pXX0pfSkpLGkoXCJjbGlja1wiLHtvbkVsZW1lbnQ6bix3aXRoQ2FsbGJhY2s6dGhpcy5kaWRDbGlja1Rvb2xiYXJ9KSxpKFwiY2xpY2tcIix7b25FbGVtZW50Om4sbWF0Y2hpbmdTZWxlY3RvcjpcIltkYXRhLXRyaXgtYWN0aW9uXVwiLHdpdGhDYWxsYmFjazp0aGlzLmRpZENsaWNrQWN0aW9uQnV0dG9ufSkse1wiZG9cIjpmdW5jdGlvbih0KXtyZXR1cm4gZnVuY3Rpb24oKXtyZXR1cm4gdC5lbGVtZW50LmFwcGVuZENoaWxkKG4pfX0odGhpcyksdW5kbzpmdW5jdGlvbigpe3JldHVybiBmdW5jdGlvbigpe3JldHVybiBlLnJlbW92ZU5vZGUobil9fSh0aGlzKX19KSxoLnByb3RvdHlwZS5pbnN0YWxsQ2FwdGlvbkVkaXRvcj1wKGZ1bmN0aW9uKCl7dmFyIG8sYSx1LGMsbDtyZXR1cm4gYz1zKHt0YWdOYW1lOlwidGV4dGFyZWFcIixjbGFzc05hbWU6dC5hdHRhY2htZW50Q2FwdGlvbkVkaXRvcixhdHRyaWJ1dGVzOntwbGFjZWhvbGRlcjpyLmNhcHRpb25QbGFjZWhvbGRlcn0sZGF0YTp7dHJpeE11dGFibGU6ITB9fSksYy52YWx1ZT10aGlzLmF0dGFjaG1lbnRQaWVjZS5nZXRDYXB0aW9uKCksbD1jLmNsb25lTm9kZSgpLGwuY2xhc3NMaXN0LmFkZChcInRyaXgtYXV0b3Jlc2l6ZS1jbG9uZVwiKSxsLnRhYkluZGV4PS0xLG89ZnVuY3Rpb24oKXtyZXR1cm4gbC52YWx1ZT1jLnZhbHVlLGMuc3R5bGUuaGVpZ2h0PWwuc2Nyb2xsSGVpZ2h0K1wicHhcIn0saShcImlucHV0XCIse29uRWxlbWVudDpjLHdpdGhDYWxsYmFjazpvfSksaShcImlucHV0XCIse29uRWxlbWVudDpjLHdpdGhDYWxsYmFjazp0aGlzLmRpZElucHV0Q2FwdGlvbn0pLGkoXCJrZXlkb3duXCIse29uRWxlbWVudDpjLHdpdGhDYWxsYmFjazp0aGlzLmRpZEtleURvd25DYXB0aW9ufSksaShcImNoYW5nZVwiLHtvbkVsZW1lbnQ6Yyx3aXRoQ2FsbGJhY2s6dGhpcy5kaWRDaGFuZ2VDYXB0aW9ufSksaShcImJsdXJcIix7b25FbGVtZW50OmMsd2l0aENhbGxiYWNrOnRoaXMuZGlkQmx1ckNhcHRpb259KSx1PXRoaXMuZWxlbWVudC5xdWVyeVNlbGVjdG9yKFwiZmlnY2FwdGlvblwiKSxhPXUuY2xvbmVOb2RlKCkse1wiZG9cIjpmdW5jdGlvbihlKXtyZXR1cm4gZnVuY3Rpb24oKXtyZXR1cm4gdS5zdHlsZS5kaXNwbGF5PVwibm9uZVwiLGEuYXBwZW5kQ2hpbGQoYyksYS5hcHBlbmRDaGlsZChsKSxhLmNsYXNzTGlzdC5hZGQodC5hdHRhY2htZW50Q2FwdGlvbitcIi0tZWRpdGluZ1wiKSx1LnBhcmVudEVsZW1lbnQuaW5zZXJ0QmVmb3JlKGEsdSksbygpLGUub3B0aW9ucy5lZGl0Q2FwdGlvbj9uKGZ1bmN0aW9uKCl7cmV0dXJuIGMuZm9jdXMoKX0pOnZvaWQgMH19KHRoaXMpLHVuZG86ZnVuY3Rpb24oKXtyZXR1cm4gZS5yZW1vdmVOb2RlKGEpLHUuc3R5bGUuZGlzcGxheT1udWxsfX19KSxoLnByb3RvdHlwZS5kaWRDbGlja1Rvb2xiYXI9ZnVuY3Rpb24odCl7cmV0dXJuIHQucHJldmVudERlZmF1bHQoKSx0LnN0b3BQcm9wYWdhdGlvbigpfSxoLnByb3RvdHlwZS5kaWRDbGlja0FjdGlvbkJ1dHRvbj1mdW5jdGlvbih0KXt2YXIgZSxuO3N3aXRjaChlPXQudGFyZ2V0LmdldEF0dHJpYnV0ZShcImRhdGEtdHJpeC1hY3Rpb25cIikpe2Nhc2VcInJlbW92ZVwiOnJldHVybiBudWxsIT0obj10aGlzLmRlbGVnYXRlKT9uLmF0dGFjaG1lbnRFZGl0b3JEaWRSZXF1ZXN0UmVtb3ZhbE9mQXR0YWNobWVudCh0aGlzLmF0dGFjaG1lbnQpOnZvaWQgMH19LGgucHJvdG90eXBlLmRpZEtleURvd25DYXB0aW9uPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVyblwicmV0dXJuXCI9PT1vW3Qua2V5Q29kZV0/KHQucHJldmVudERlZmF1bHQoKSx0aGlzLnNhdmVQZW5kaW5nQ2FwdGlvbigpLG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBlLmF0dGFjaG1lbnRFZGl0b3JEaWRSZXF1ZXN0RGVzZWxlY3RpbmdBdHRhY2htZW50P2UuYXR0YWNobWVudEVkaXRvckRpZFJlcXVlc3REZXNlbGVjdGluZ0F0dGFjaG1lbnQodGhpcy5hdHRhY2htZW50KTp2b2lkIDApOnZvaWQgMH0saC5wcm90b3R5cGUuZGlkSW5wdXRDYXB0aW9uPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnBlbmRpbmdDYXB0aW9uPXQudGFyZ2V0LnZhbHVlLnJlcGxhY2UoL1xccy9nLFwiIFwiKS50cmltKCl9LGgucHJvdG90eXBlLmRpZENoYW5nZUNhcHRpb249ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zYXZlUGVuZGluZ0NhcHRpb24oKX0saC5wcm90b3R5cGUuZGlkQmx1ckNhcHRpb249ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zYXZlUGVuZGluZ0NhcHRpb24oKX0saH0oZS5CYXNpY09iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbz1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpci5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxyPXt9Lmhhc093blByb3BlcnR5O2k9ZS5tYWtlRWxlbWVudCx0PWUuY29uZmlnLmNzcyxlLkF0dGFjaG1lbnRWaWV3PWZ1bmN0aW9uKHIpe2Z1bmN0aW9uIHMoKXtzLl9fc3VwZXJfXy5jb25zdHJ1Y3Rvci5hcHBseSh0aGlzLGFyZ3VtZW50cyksdGhpcy5hdHRhY2htZW50PXRoaXMub2JqZWN0LHRoaXMuYXR0YWNobWVudC51cGxvYWRQcm9ncmVzc0RlbGVnYXRlPXRoaXMsdGhpcy5hdHRhY2htZW50UGllY2U9dGhpcy5vcHRpb25zLnBpZWNlfXZhciBhO3JldHVybiBvKHMscikscy5hdHRhY2htZW50U2VsZWN0b3I9XCJbZGF0YS10cml4LWF0dGFjaG1lbnRdXCIscy5wcm90b3R5cGUuY3JlYXRlQ29udGVudE5vZGVzPWZ1bmN0aW9uKCl7cmV0dXJuW119LHMucHJvdG90eXBlLmNyZWF0ZU5vZGVzPWZ1bmN0aW9uKCl7dmFyIGUsbixvLHIscyx1LGM7aWYoZT1yPWkoe3RhZ05hbWU6XCJmaWd1cmVcIixjbGFzc05hbWU6dGhpcy5nZXRDbGFzc05hbWUoKSxkYXRhOnRoaXMuZ2V0RGF0YSgpLGVkaXRhYmxlOiExfSksKG49dGhpcy5nZXRIcmVmKCkpJiYocj1pKHt0YWdOYW1lOlwiYVwiLGVkaXRhYmxlOiExLGF0dHJpYnV0ZXM6e2hyZWY6bix0YWJpbmRleDotMX19KSxlLmFwcGVuZENoaWxkKHIpKSx0aGlzLmF0dGFjaG1lbnQuaGFzQ29udGVudCgpKXIuaW5uZXJIVE1MPXRoaXMuYXR0YWNobWVudC5nZXRDb250ZW50KCk7ZWxzZSBmb3IoYz10aGlzLmNyZWF0ZUNvbnRlbnROb2RlcygpLG89MCxzPWMubGVuZ3RoO3M+bztvKyspdT1jW29dLHIuYXBwZW5kQ2hpbGQodSk7cmV0dXJuIHIuYXBwZW5kQ2hpbGQodGhpcy5jcmVhdGVDYXB0aW9uRWxlbWVudCgpKSx0aGlzLmF0dGFjaG1lbnQuaXNQZW5kaW5nKCkmJih0aGlzLnByb2dyZXNzRWxlbWVudD1pKHt0YWdOYW1lOlwicHJvZ3Jlc3NcIixhdHRyaWJ1dGVzOntcImNsYXNzXCI6dC5hdHRhY2htZW50UHJvZ3Jlc3MsdmFsdWU6dGhpcy5hdHRhY2htZW50LmdldFVwbG9hZFByb2dyZXNzKCksbWF4OjEwMH0sZGF0YTp7dHJpeE11dGFibGU6ITAsdHJpeFN0b3JlS2V5OltcInByb2dyZXNzRWxlbWVudFwiLHRoaXMuYXR0YWNobWVudC5pZF0uam9pbihcIi9cIil9fSksZS5hcHBlbmRDaGlsZCh0aGlzLnByb2dyZXNzRWxlbWVudCkpLFthKFwibGVmdFwiKSxlLGEoXCJyaWdodFwiKV19LHMucHJvdG90eXBlLmNyZWF0ZUNhcHRpb25FbGVtZW50PWZ1bmN0aW9uKCl7dmFyIGUsbixvLHIscyxhLHU7cmV0dXJuIG89aSh7dGFnTmFtZTpcImZpZ2NhcHRpb25cIixjbGFzc05hbWU6dC5hdHRhY2htZW50Q2FwdGlvbn0pLChlPXRoaXMuYXR0YWNobWVudFBpZWNlLmdldENhcHRpb24oKSk/KG8uY2xhc3NMaXN0LmFkZCh0LmF0dGFjaG1lbnRDYXB0aW9uK1wiLS1lZGl0ZWRcIiksby50ZXh0Q29udGVudD1lKToobj10aGlzLmdldENhcHRpb25Db25maWcoKSxuLm5hbWUmJihyPXRoaXMuYXR0YWNobWVudC5nZXRGaWxlbmFtZSgpKSxuLnNpemUmJihhPXRoaXMuYXR0YWNobWVudC5nZXRGb3JtYXR0ZWRGaWxlc2l6ZSgpKSxyJiYocz1pKHt0YWdOYW1lOlwic3BhblwiLGNsYXNzTmFtZTp0LmF0dGFjaG1lbnROYW1lLHRleHRDb250ZW50OnJ9KSxvLmFwcGVuZENoaWxkKHMpKSxhJiYociYmby5hcHBlbmRDaGlsZChkb2N1bWVudC5jcmVhdGVUZXh0Tm9kZShcIiBcIikpLHU9aSh7dGFnTmFtZTpcInNwYW5cIixjbGFzc05hbWU6dC5hdHRhY2htZW50U2l6ZSx0ZXh0Q29udGVudDphfSksby5hcHBlbmRDaGlsZCh1KSkpLG99LHMucHJvdG90eXBlLmdldENsYXNzTmFtZT1mdW5jdGlvbigpe3ZhciBlLG47cmV0dXJuIG49W3QuYXR0YWNobWVudCx0LmF0dGFjaG1lbnQrXCItLVwiK3RoaXMuYXR0YWNobWVudC5nZXRUeXBlKCldLChlPXRoaXMuYXR0YWNobWVudC5nZXRFeHRlbnNpb24oKSkmJm4ucHVzaCh0LmF0dGFjaG1lbnQrXCItLVwiK2UpLG4uam9pbihcIiBcIil9LHMucHJvdG90eXBlLmdldERhdGE9ZnVuY3Rpb24oKXt2YXIgdCxlO3JldHVybiBlPXt0cml4QXR0YWNobWVudDpKU09OLnN0cmluZ2lmeSh0aGlzLmF0dGFjaG1lbnQpLHRyaXhDb250ZW50VHlwZTp0aGlzLmF0dGFjaG1lbnQuZ2V0Q29udGVudFR5cGUoKSx0cml4SWQ6dGhpcy5hdHRhY2htZW50LmlkfSx0PXRoaXMuYXR0YWNobWVudFBpZWNlLmF0dHJpYnV0ZXMsdC5pc0VtcHR5KCl8fChlLnRyaXhBdHRyaWJ1dGVzPUpTT04uc3RyaW5naWZ5KHQpKSx0aGlzLmF0dGFjaG1lbnQuaXNQZW5kaW5nKCkmJihlLnRyaXhTZXJpYWxpemU9ITEpLGV9LHMucHJvdG90eXBlLmdldEhyZWY9ZnVuY3Rpb24oKXtyZXR1cm4gbih0aGlzLmF0dGFjaG1lbnQuZ2V0Q29udGVudCgpLFwiYVwiKT92b2lkIDA6dGhpcy5hdHRhY2htZW50LmdldEhyZWYoKX0scy5wcm90b3R5cGUuZ2V0Q2FwdGlvbkNvbmZpZz1mdW5jdGlvbigpe3ZhciB0LG4saTtyZXR1cm4gaT10aGlzLmF0dGFjaG1lbnQuZ2V0VHlwZSgpLHQ9ZS5jb3B5T2JqZWN0KG51bGwhPShuPWUuY29uZmlnLmF0dGFjaG1lbnRzW2ldKT9uLmNhcHRpb246dm9pZCAwKSxcImZpbGVcIj09PWkmJih0Lm5hbWU9ITApLHR9LHMucHJvdG90eXBlLmZpbmRQcm9ncmVzc0VsZW1lbnQ9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5maW5kRWxlbWVudCgpKT90LnF1ZXJ5U2VsZWN0b3IoXCJwcm9ncmVzc1wiKTp2b2lkIDB9LGE9ZnVuY3Rpb24odCl7cmV0dXJuIGkoe3RhZ05hbWU6XCJzcGFuXCIsdGV4dENvbnRlbnQ6ZS5aRVJPX1dJRFRIX1NQQUNFLGRhdGE6e3RyaXhDdXJzb3JUYXJnZXQ6dCx0cml4U2VyaWFsaXplOiExfX0pfSxzLnByb3RvdHlwZS5hdHRhY2htZW50RGlkQ2hhbmdlVXBsb2FkUHJvZ3Jlc3M9ZnVuY3Rpb24oKXt2YXIgdCxlO3JldHVybiBlPXRoaXMuYXR0YWNobWVudC5nZXRVcGxvYWRQcm9ncmVzcygpLG51bGwhPSh0PXRoaXMuZmluZFByb2dyZXNzRWxlbWVudCgpKT90LnZhbHVlPWU6dm9pZCAwfSxzfShlLk9iamVjdFZpZXcpLG49ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4gbj1pKFwiZGl2XCIpLG4uaW5uZXJIVE1MPW51bGwhPXQ/dDpcIlwiLG4ucXVlcnlTZWxlY3RvcihlKX19LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSlpLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LGk9e30uaGFzT3duUHJvcGVydHk7dD1lLm1ha2VFbGVtZW50LGUuUHJldmlld2FibGVBdHRhY2htZW50Vmlldz1mdW5jdGlvbihpKXtmdW5jdGlvbiBvKCl7by5fX3N1cGVyX18uY29uc3RydWN0b3IuYXBwbHkodGhpcyxhcmd1bWVudHMpLHRoaXMuYXR0YWNobWVudC5wcmV2aWV3RGVsZWdhdGU9dGhpc31yZXR1cm4gbihvLGkpLG8ucHJvdG90eXBlLmNyZWF0ZUNvbnRlbnROb2Rlcz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmltYWdlPXQoe3RhZ05hbWU6XCJpbWdcIixhdHRyaWJ1dGVzOntzcmM6XCJcIn0sZGF0YTp7dHJpeE11dGFibGU6ITB9fSksdGhpcy5yZWZyZXNoKHRoaXMuaW1hZ2UpLFt0aGlzLmltYWdlXX0sby5wcm90b3R5cGUuY3JlYXRlQ2FwdGlvbkVsZW1lbnQ9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gdD1vLl9fc3VwZXJfXy5jcmVhdGVDYXB0aW9uRWxlbWVudC5hcHBseSh0aGlzLGFyZ3VtZW50cyksdC50ZXh0Q29udGVudHx8dC5zZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtcGxhY2Vob2xkZXJcIixlLmNvbmZpZy5sYW5nLmNhcHRpb25QbGFjZWhvbGRlciksdH0sby5wcm90b3R5cGUucmVmcmVzaD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gbnVsbD09dCYmKHQ9bnVsbCE9KGU9dGhpcy5maW5kRWxlbWVudCgpKT9lLnF1ZXJ5U2VsZWN0b3IoXCJpbWdcIik6dm9pZCAwKSx0P3RoaXMudXBkYXRlQXR0cmlidXRlc0ZvckltYWdlKHQpOnZvaWQgMH0sby5wcm90b3R5cGUudXBkYXRlQXR0cmlidXRlc0ZvckltYWdlPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHIscztyZXR1cm4gcj10aGlzLmF0dGFjaG1lbnQuZ2V0VVJMKCksbj10aGlzLmF0dGFjaG1lbnQuZ2V0UHJldmlld1VSTCgpLHQuc3JjPW58fHIsbj09PXI/dC5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtc2VyaWFsaXplZC1hdHRyaWJ1dGVzXCIpOihpPUpTT04uc3RyaW5naWZ5KHtzcmM6cn0pLHQuc2V0QXR0cmlidXRlKFwiZGF0YS10cml4LXNlcmlhbGl6ZWQtYXR0cmlidXRlc1wiLGkpKSxzPXRoaXMuYXR0YWNobWVudC5nZXRXaWR0aCgpLGU9dGhpcy5hdHRhY2htZW50LmdldEhlaWdodCgpLG51bGwhPXMmJih0LndpZHRoPXMpLG51bGwhPWUmJih0LmhlaWdodD1lKSxvPVtcImltYWdlRWxlbWVudFwiLHRoaXMuYXR0YWNobWVudC5pZCx0LnNyYyx0LndpZHRoLHQuaGVpZ2h0XS5qb2luKFwiL1wiKSx0LmRhdGFzZXQudHJpeFN0b3JlS2V5PW99LG8ucHJvdG90eXBlLmF0dGFjaG1lbnREaWRDaGFuZ2VBdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMucmVmcmVzaCh0aGlzLmltYWdlKSx0aGlzLnJlZnJlc2goKX0sb30oZS5BdHRhY2htZW50Vmlldyl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbz1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpci5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxyPXt9Lmhhc093blByb3BlcnR5O2k9ZS5tYWtlRWxlbWVudCx0PWUuZmluZElubmVyRWxlbWVudCxuPWUuZ2V0VGV4dENvbmZpZyxlLlBpZWNlVmlldz1mdW5jdGlvbihyKXtmdW5jdGlvbiBzKCl7dmFyIHQ7cy5fX3N1cGVyX18uY29uc3RydWN0b3IuYXBwbHkodGhpcyxhcmd1bWVudHMpLHRoaXMucGllY2U9dGhpcy5vYmplY3QsdGhpcy5hdHRyaWJ1dGVzPXRoaXMucGllY2UuZ2V0QXR0cmlidXRlcygpLHQ9dGhpcy5vcHRpb25zLHRoaXMudGV4dENvbmZpZz10LnRleHRDb25maWcsdGhpcy5jb250ZXh0PXQuY29udGV4dCx0aGlzLnBpZWNlLmF0dGFjaG1lbnQ/dGhpcy5hdHRhY2htZW50PXRoaXMucGllY2UuYXR0YWNobWVudDp0aGlzLnN0cmluZz10aGlzLnBpZWNlLnRvU3RyaW5nKCl9dmFyIGE7cmV0dXJuIG8ocyxyKSxzLnByb3RvdHlwZS5jcmVhdGVOb2Rlcz1mdW5jdGlvbigpe3ZhciBlLG4saSxvLHIscztpZihzPXRoaXMuYXR0YWNobWVudD90aGlzLmNyZWF0ZUF0dGFjaG1lbnROb2RlcygpOnRoaXMuY3JlYXRlU3RyaW5nTm9kZXMoKSxlPXRoaXMuY3JlYXRlRWxlbWVudCgpKXtmb3IoaT10KGUpLG49MCxvPXMubGVuZ3RoO28+bjtuKyspcj1zW25dLGkuYXBwZW5kQ2hpbGQocik7cz1bZV19cmV0dXJuIHN9LHMucHJvdG90eXBlLmNyZWF0ZUF0dGFjaG1lbnROb2Rlcz1mdW5jdGlvbigpe3ZhciB0LG47cmV0dXJuIHQ9dGhpcy5hdHRhY2htZW50LmlzUHJldmlld2FibGUoKT9lLlByZXZpZXdhYmxlQXR0YWNobWVudFZpZXc6ZS5BdHRhY2htZW50VmlldyxuPXRoaXMuY3JlYXRlQ2hpbGRWaWV3KHQsdGhpcy5waWVjZS5hdHRhY2htZW50LHtwaWVjZTp0aGlzLnBpZWNlfSksbi5nZXROb2RlcygpfSxzLnByb3RvdHlwZS5jcmVhdGVTdHJpbmdOb2Rlcz1mdW5jdGlvbigpe3ZhciB0LGUsbixvLHIscyxhLHUsYyxsO2lmKG51bGwhPSh1PXRoaXMudGV4dENvbmZpZyk/dS5wbGFpbnRleHQ6dm9pZCAwKXJldHVybltkb2N1bWVudC5jcmVhdGVUZXh0Tm9kZSh0aGlzLnN0cmluZyldO2ZvcihhPVtdLGM9dGhpcy5zdHJpbmcuc3BsaXQoXCJcXG5cIiksbj1lPTAsbz1jLmxlbmd0aDtvPmU7bj0rK2UpbD1jW25dLG4+MCYmKHQ9aShcImJyXCIpLGEucHVzaCh0KSksKHI9bC5sZW5ndGgpJiYocz1kb2N1bWVudC5jcmVhdGVUZXh0Tm9kZSh0aGlzLnByZXNlcnZlU3BhY2VzKGwpKSxhLnB1c2gocykpO3JldHVybiBhfSxzLnByb3RvdHlwZS5jcmVhdGVFbGVtZW50PWZ1bmN0aW9uKCl7dmFyIHQsZSxvLHIscyxhLHUsYyxsO2M9e30sYT10aGlzLmF0dHJpYnV0ZXM7Zm9yKHIgaW4gYSlpZihsPWFbcl0sKHQ9bihyKSkmJih0LnRhZ05hbWUmJihzPWkodC50YWdOYW1lKSxvPyhvLmFwcGVuZENoaWxkKHMpLG89cyk6ZT1vPXMpLHQuc3R5bGVQcm9wZXJ0eSYmKGNbdC5zdHlsZVByb3BlcnR5XT1sKSx0LnN0eWxlKSl7dT10LnN0eWxlO2ZvcihyIGluIHUpbD11W3JdLGNbcl09bH1pZihPYmplY3Qua2V5cyhjKS5sZW5ndGgpe251bGw9PWUmJihlPWkoXCJzcGFuXCIpKTtmb3IociBpbiBjKWw9Y1tyXSxlLnN0eWxlW3JdPWx9cmV0dXJuIGV9LHMucHJvdG90eXBlLmNyZWF0ZUNvbnRhaW5lckVsZW1lbnQ9ZnVuY3Rpb24oKXt2YXIgdCxlLG8scixzO3I9dGhpcy5hdHRyaWJ1dGVzO2ZvcihvIGluIHIpaWYocz1yW29dLChlPW4obykpJiZlLmdyb3VwVGFnTmFtZSlyZXR1cm4gdD17fSx0W29dPXMsaShlLmdyb3VwVGFnTmFtZSx0KX0sYT1lLk5PTl9CUkVBS0lOR19TUEFDRSxzLnByb3RvdHlwZS5wcmVzZXJ2ZVNwYWNlcz1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5jb250ZXh0LmlzTGFzdCYmKHQ9dC5yZXBsYWNlKC9cXCAkLyxhKSksdD10LnJlcGxhY2UoLyhcXFMpXFwgezN9KFxcUykvZyxcIiQxIFwiK2ErXCIgJDJcIikucmVwbGFjZSgvXFwgezJ9L2csYStcIiBcIikucmVwbGFjZSgvXFwgezJ9L2csXCIgXCIrYSksKHRoaXMuY29udGV4dC5pc0ZpcnN0fHx0aGlzLmNvbnRleHQuZm9sbG93c1doaXRlc3BhY2UpJiYodD10LnJlcGxhY2UoL15cXCAvLGEpKSx0fSxzfShlLk9iamVjdFZpZXcpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBpKCl7dGhpcy5jb25zdHJ1Y3Rvcj10XG59Zm9yKHZhciBvIGluIGUpbi5jYWxsKGUsbykmJih0W29dPWVbb10pO3JldHVybiBpLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgaSx0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxuPXt9Lmhhc093blByb3BlcnR5O2UuVGV4dFZpZXc9ZnVuY3Rpb24obil7ZnVuY3Rpb24gaSgpe2kuX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLnRleHQ9dGhpcy5vYmplY3QsdGhpcy50ZXh0Q29uZmlnPXRoaXMub3B0aW9ucy50ZXh0Q29uZmlnfXZhciBvO3JldHVybiB0KGksbiksaS5wcm90b3R5cGUuY3JlYXRlTm9kZXM9ZnVuY3Rpb24oKXt2YXIgdCxuLGkscixzLGEsdSxjLGwsaDtmb3IoYT1bXSxjPWUuT2JqZWN0R3JvdXAuZ3JvdXBPYmplY3RzKHRoaXMuZ2V0UGllY2VzKCkpLHI9Yy5sZW5ndGgtMSxpPW49MCxzPWMubGVuZ3RoO3M+bjtpPSsrbil1PWNbaV0sdD17fSwwPT09aSYmKHQuaXNGaXJzdD0hMCksaT09PXImJih0LmlzTGFzdD0hMCksbyhsKSYmKHQuZm9sbG93c1doaXRlc3BhY2U9ITApLGg9dGhpcy5maW5kT3JDcmVhdGVDYWNoZWRDaGlsZFZpZXcoZS5QaWVjZVZpZXcsdSx7dGV4dENvbmZpZzp0aGlzLnRleHRDb25maWcsY29udGV4dDp0fSksYS5wdXNoLmFwcGx5KGEsaC5nZXROb2RlcygpKSxsPXU7cmV0dXJuIGF9LGkucHJvdG90eXBlLmdldFBpZWNlcz1mdW5jdGlvbigpe3ZhciB0LGUsbixpLG87Zm9yKGk9dGhpcy50ZXh0LmdldFBpZWNlcygpLG89W10sdD0wLGU9aS5sZW5ndGg7ZT50O3QrKyluPWlbdF0sbi5oYXNBdHRyaWJ1dGUoXCJibG9ja0JyZWFrXCIpfHxvLnB1c2gobik7cmV0dXJuIG99LG89ZnVuY3Rpb24odCl7cmV0dXJuL1xccyQvLnRlc3QobnVsbCE9dD90LnRvU3RyaW5nKCk6dm9pZCAwKX0saX0oZS5PYmplY3RWaWV3KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0LG4saSxvPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIGkgaW4gZSlyLmNhbGwoZSxpKSYmKHRbaV09ZVtpXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LHI9e30uaGFzT3duUHJvcGVydHk7aT1lLm1ha2VFbGVtZW50LG49ZS5nZXRCbG9ja0NvbmZpZyx0PWUuY29uZmlnLmNzcyxlLkJsb2NrVmlldz1mdW5jdGlvbihyKXtmdW5jdGlvbiBzKCl7cy5fX3N1cGVyX18uY29uc3RydWN0b3IuYXBwbHkodGhpcyxhcmd1bWVudHMpLHRoaXMuYmxvY2s9dGhpcy5vYmplY3QsdGhpcy5hdHRyaWJ1dGVzPXRoaXMuYmxvY2suZ2V0QXR0cmlidXRlcygpfXJldHVybiBvKHMscikscy5wcm90b3R5cGUuY3JlYXRlTm9kZXM9ZnVuY3Rpb24oKXt2YXIgdCxvLHIscyxhLHUsYyxsLGgscCxkO2lmKG89ZG9jdW1lbnQuY3JlYXRlQ29tbWVudChcImJsb2NrXCIpLGM9W29dLHRoaXMuYmxvY2suaXNFbXB0eSgpP2MucHVzaChpKFwiYnJcIikpOihwPW51bGwhPShsPW4odGhpcy5ibG9jay5nZXRMYXN0QXR0cmlidXRlKCkpKT9sLnRleHQ6dm9pZCAwLGQ9dGhpcy5maW5kT3JDcmVhdGVDYWNoZWRDaGlsZFZpZXcoZS5UZXh0Vmlldyx0aGlzLmJsb2NrLnRleHQse3RleHRDb25maWc6cH0pLGMucHVzaC5hcHBseShjLGQuZ2V0Tm9kZXMoKSksdGhpcy5zaG91bGRBZGRFeHRyYU5ld2xpbmVFbGVtZW50KCkmJmMucHVzaChpKFwiYnJcIikpKSx0aGlzLmF0dHJpYnV0ZXMubGVuZ3RoKXJldHVybiBjO2ZvcihoPWUuY29uZmlnLmJsb2NrQXR0cmlidXRlc1tcImRlZmF1bHRcIl0udGFnTmFtZSx0aGlzLmJsb2NrLmlzUlRMKCkmJih0PXtkaXI6XCJydGxcIn0pLHI9aSh7dGFnTmFtZTpoLGF0dHJpYnV0ZXM6dH0pLHM9MCxhPWMubGVuZ3RoO2E+cztzKyspdT1jW3NdLHIuYXBwZW5kQ2hpbGQodSk7cmV0dXJuW3JdfSxzLnByb3RvdHlwZS5jcmVhdGVDb250YWluZXJFbGVtZW50PWZ1bmN0aW9uKGUpe3ZhciBvLHIscyxhLHU7cmV0dXJuIG89dGhpcy5hdHRyaWJ1dGVzW2VdLHU9bihvKS50YWdOYW1lLDA9PT1lJiZ0aGlzLmJsb2NrLmlzUlRMKCkmJihyPXtkaXI6XCJydGxcIn0pLFwiYXR0YWNobWVudEdhbGxlcnlcIj09PW8mJihhPXRoaXMuYmxvY2suZ2V0QmxvY2tCcmVha1Bvc2l0aW9uKCkscz10LmF0dGFjaG1lbnRHYWxsZXJ5K1wiIFwiK3QuYXR0YWNobWVudEdhbGxlcnkrXCItLVwiK2EpLGkoe3RhZ05hbWU6dSxjbGFzc05hbWU6cyxhdHRyaWJ1dGVzOnJ9KX0scy5wcm90b3R5cGUuc2hvdWxkQWRkRXh0cmFOZXdsaW5lRWxlbWVudD1mdW5jdGlvbigpe3JldHVybi9cXG5cXG4kLy50ZXN0KHRoaXMuYmxvY2sudG9TdHJpbmcoKSl9LHN9KGUuT2JqZWN0Vmlldyl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGk9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBuKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgaSBpbiBlKW8uY2FsbChlLGkpJiYodFtpXT1lW2ldKTtyZXR1cm4gbi5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IG4sdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sbz17fS5oYXNPd25Qcm9wZXJ0eTt0PWUuZGVmZXIsbj1lLm1ha2VFbGVtZW50LGUuRG9jdW1lbnRWaWV3PWZ1bmN0aW9uKG8pe2Z1bmN0aW9uIHIoKXtyLl9fc3VwZXJfXy5jb25zdHJ1Y3Rvci5hcHBseSh0aGlzLGFyZ3VtZW50cyksdGhpcy5lbGVtZW50PXRoaXMub3B0aW9ucy5lbGVtZW50LHRoaXMuZWxlbWVudFN0b3JlPW5ldyBlLkVsZW1lbnRTdG9yZSx0aGlzLnNldERvY3VtZW50KHRoaXMub2JqZWN0KX12YXIgcyxhLHU7cmV0dXJuIGkocixvKSxyLnJlbmRlcj1mdW5jdGlvbih0KXt2YXIgZSxpO3JldHVybiBlPW4oXCJkaXZcIiksaT1uZXcgdGhpcyh0LHtlbGVtZW50OmV9KSxpLnJlbmRlcigpLGkuc3luYygpLGV9LHIucHJvdG90eXBlLnNldERvY3VtZW50PWZ1bmN0aW9uKHQpe3JldHVybiB0LmlzRXF1YWxUbyh0aGlzLmRvY3VtZW50KT92b2lkIDA6dGhpcy5kb2N1bWVudD10aGlzLm9iamVjdD10fSxyLnByb3RvdHlwZS5yZW5kZXI9ZnVuY3Rpb24oKXt2YXIgdCxpLG8scixzLGEsdTtpZih0aGlzLmNoaWxkVmlld3M9W10sdGhpcy5zaGFkb3dFbGVtZW50PW4oXCJkaXZcIiksIXRoaXMuZG9jdW1lbnQuaXNFbXB0eSgpKXtmb3Iocz1lLk9iamVjdEdyb3VwLmdyb3VwT2JqZWN0cyh0aGlzLmRvY3VtZW50LmdldEJsb2NrcygpLHthc1RyZWU6ITB9KSxhPVtdLHQ9MCxpPXMubGVuZ3RoO2k+dDt0Kyspcj1zW3RdLHU9dGhpcy5maW5kT3JDcmVhdGVDYWNoZWRDaGlsZFZpZXcoZS5CbG9ja1ZpZXcsciksYS5wdXNoKGZ1bmN0aW9uKCl7dmFyIHQsZSxuLGk7Zm9yKG49dS5nZXROb2RlcygpLGk9W10sdD0wLGU9bi5sZW5ndGg7ZT50O3QrKylvPW5bdF0saS5wdXNoKHRoaXMuc2hhZG93RWxlbWVudC5hcHBlbmRDaGlsZChvKSk7cmV0dXJuIGl9LmNhbGwodGhpcykpO3JldHVybiBhfX0sci5wcm90b3R5cGUuaXNTeW5jZWQ9ZnVuY3Rpb24oKXtyZXR1cm4gcyh0aGlzLnNoYWRvd0VsZW1lbnQsdGhpcy5lbGVtZW50KX0sci5wcm90b3R5cGUuc3luYz1mdW5jdGlvbigpe3ZhciB0O2Zvcih0PXRoaXMuY3JlYXRlRG9jdW1lbnRGcmFnbWVudEZvclN5bmMoKTt0aGlzLmVsZW1lbnQubGFzdENoaWxkOyl0aGlzLmVsZW1lbnQucmVtb3ZlQ2hpbGQodGhpcy5lbGVtZW50Lmxhc3RDaGlsZCk7cmV0dXJuIHRoaXMuZWxlbWVudC5hcHBlbmRDaGlsZCh0KSx0aGlzLmRpZFN5bmMoKX0sci5wcm90b3R5cGUuZGlkU3luYz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmVsZW1lbnRTdG9yZS5yZXNldChhKHRoaXMuZWxlbWVudCkpLHQoZnVuY3Rpb24odCl7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIHQuZ2FyYmFnZUNvbGxlY3RDYWNoZWRWaWV3cygpfX0odGhpcykpfSxyLnByb3RvdHlwZS5jcmVhdGVEb2N1bWVudEZyYWdtZW50Rm9yU3luYz1mdW5jdGlvbigpe3ZhciB0LGUsbixpLG8scixzLHUsYyxsO2ZvcihlPWRvY3VtZW50LmNyZWF0ZURvY3VtZW50RnJhZ21lbnQoKSx1PXRoaXMuc2hhZG93RWxlbWVudC5jaGlsZE5vZGVzLG49MCxvPXUubGVuZ3RoO28+bjtuKyspcz11W25dLGUuYXBwZW5kQ2hpbGQocy5jbG9uZU5vZGUoITApKTtmb3IoYz1hKGUpLGk9MCxyPWMubGVuZ3RoO3I+aTtpKyspdD1jW2ldLChsPXRoaXMuZWxlbWVudFN0b3JlLnJlbW92ZSh0KSkmJnQucGFyZW50Tm9kZS5yZXBsYWNlQ2hpbGQobCx0KTtyZXR1cm4gZX0sYT1mdW5jdGlvbih0KXtyZXR1cm4gdC5xdWVyeVNlbGVjdG9yQWxsKFwiW2RhdGEtdHJpeC1zdG9yZS1rZXldXCIpfSxzPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIHUodC5pbm5lckhUTUwpPT09dShlLmlubmVySFRNTCl9LHU9ZnVuY3Rpb24odCl7cmV0dXJuIHQucmVwbGFjZSgvJm5ic3A7L2csXCIgXCIpfSxyfShlLk9iamVjdFZpZXcpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scixzPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIHQuYXBwbHkoZSxhcmd1bWVudHMpfX0sYT1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpdS5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSx1PXt9Lmhhc093blByb3BlcnR5O2k9ZS5maW5kQ2xvc2VzdEVsZW1lbnRGcm9tTm9kZSxvPWUuaGFuZGxlRXZlbnQscj1lLmlubmVyRWxlbWVudElzQWN0aXZlLG49ZS5kZWZlcix0PWUuQXR0YWNobWVudFZpZXcuYXR0YWNobWVudFNlbGVjdG9yLGUuQ29tcG9zaXRpb25Db250cm9sbGVyPWZ1bmN0aW9uKHUpe2Z1bmN0aW9uIGMobixpKXt0aGlzLmVsZW1lbnQ9bix0aGlzLmNvbXBvc2l0aW9uPWksdGhpcy5kaWRDbGlja0F0dGFjaG1lbnQ9cyh0aGlzLmRpZENsaWNrQXR0YWNobWVudCx0aGlzKSx0aGlzLmRpZEJsdXI9cyh0aGlzLmRpZEJsdXIsdGhpcyksdGhpcy5kaWRGb2N1cz1zKHRoaXMuZGlkRm9jdXMsdGhpcyksdGhpcy5kb2N1bWVudFZpZXc9bmV3IGUuRG9jdW1lbnRWaWV3KHRoaXMuY29tcG9zaXRpb24uZG9jdW1lbnQse2VsZW1lbnQ6dGhpcy5lbGVtZW50fSksbyhcImZvY3VzXCIse29uRWxlbWVudDp0aGlzLmVsZW1lbnQsd2l0aENhbGxiYWNrOnRoaXMuZGlkRm9jdXN9KSxvKFwiYmx1clwiLHtvbkVsZW1lbnQ6dGhpcy5lbGVtZW50LHdpdGhDYWxsYmFjazp0aGlzLmRpZEJsdXJ9KSxvKFwiY2xpY2tcIix7b25FbGVtZW50OnRoaXMuZWxlbWVudCxtYXRjaGluZ1NlbGVjdG9yOlwiYVtjb250ZW50ZWRpdGFibGU9ZmFsc2VdXCIscHJldmVudERlZmF1bHQ6ITB9KSxvKFwibW91c2Vkb3duXCIse29uRWxlbWVudDp0aGlzLmVsZW1lbnQsbWF0Y2hpbmdTZWxlY3Rvcjp0LHdpdGhDYWxsYmFjazp0aGlzLmRpZENsaWNrQXR0YWNobWVudH0pLG8oXCJjbGlja1wiLHtvbkVsZW1lbnQ6dGhpcy5lbGVtZW50LG1hdGNoaW5nU2VsZWN0b3I6XCJhXCIrdCxwcmV2ZW50RGVmYXVsdDohMH0pfXJldHVybiBhKGMsdSksYy5wcm90b3R5cGUuZGlkRm9jdXM9ZnVuY3Rpb24oKXt2YXIgdCxlLG47cmV0dXJuIHQ9ZnVuY3Rpb24odCl7cmV0dXJuIGZ1bmN0aW9uKCl7dmFyIGU7cmV0dXJuIHQuZm9jdXNlZD92b2lkIDA6KHQuZm9jdXNlZD0hMCxudWxsIT0oZT10LmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgZS5jb21wb3NpdGlvbkNvbnRyb2xsZXJEaWRGb2N1cz9lLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZEZvY3VzKCk6dm9pZCAwKX19KHRoaXMpLG51bGwhPShlPW51bGwhPShuPXRoaXMuYmx1clByb21pc2UpP24udGhlbih0KTp2b2lkIDApP2U6dCgpfSxjLnByb3RvdHlwZS5kaWRCbHVyPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYmx1clByb21pc2U9bmV3IFByb21pc2UoZnVuY3Rpb24odCl7cmV0dXJuIGZ1bmN0aW9uKGUpe3JldHVybiBuKGZ1bmN0aW9uKCl7dmFyIG47cmV0dXJuIHIodC5lbGVtZW50KXx8KHQuZm9jdXNlZD1udWxsLG51bGwhPShuPXQuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBuLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZEJsdXImJm4uY29tcG9zaXRpb25Db250cm9sbGVyRGlkQmx1cigpKSx0LmJsdXJQcm9taXNlPW51bGwsZSgpfSl9fSh0aGlzKSl9LGMucHJvdG90eXBlLmRpZENsaWNrQXR0YWNobWVudD1mdW5jdGlvbih0LGUpe3ZhciBuLG8scjtyZXR1cm4gbj10aGlzLmZpbmRBdHRhY2htZW50Rm9yRWxlbWVudChlKSxvPW51bGwhPWkodC50YXJnZXQse21hdGNoaW5nU2VsZWN0b3I6XCJmaWdjYXB0aW9uXCJ9KSxudWxsIT0ocj10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2Ygci5jb21wb3NpdGlvbkNvbnRyb2xsZXJEaWRTZWxlY3RBdHRhY2htZW50P3IuY29tcG9zaXRpb25Db250cm9sbGVyRGlkU2VsZWN0QXR0YWNobWVudChuLHtlZGl0Q2FwdGlvbjpvfSk6dm9pZCAwfSxjLnByb3RvdHlwZS5nZXRTZXJpYWxpemFibGVFbGVtZW50PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuaXNFZGl0aW5nQXR0YWNobWVudCgpP3RoaXMuZG9jdW1lbnRWaWV3LnNoYWRvd0VsZW1lbnQ6dGhpcy5lbGVtZW50fSxjLnByb3RvdHlwZS5yZW5kZXI9ZnVuY3Rpb24oKXt2YXIgdCxlLG47cmV0dXJuIHRoaXMucmV2aXNpb24hPT10aGlzLmNvbXBvc2l0aW9uLnJldmlzaW9uJiYodGhpcy5kb2N1bWVudFZpZXcuc2V0RG9jdW1lbnQodGhpcy5jb21wb3NpdGlvbi5kb2N1bWVudCksdGhpcy5kb2N1bWVudFZpZXcucmVuZGVyKCksdGhpcy5yZXZpc2lvbj10aGlzLmNvbXBvc2l0aW9uLnJldmlzaW9uKSx0aGlzLmNhblN5bmNEb2N1bWVudFZpZXcoKSYmIXRoaXMuZG9jdW1lbnRWaWV3LmlzU3luY2VkKCkmJihudWxsIT0odD10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgdC5jb21wb3NpdGlvbkNvbnRyb2xsZXJXaWxsU3luY0RvY3VtZW50VmlldyYmdC5jb21wb3NpdGlvbkNvbnRyb2xsZXJXaWxsU3luY0RvY3VtZW50VmlldygpLHRoaXMuZG9jdW1lbnRWaWV3LnN5bmMoKSxudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgZS5jb21wb3NpdGlvbkNvbnRyb2xsZXJEaWRTeW5jRG9jdW1lbnRWaWV3JiZlLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZFN5bmNEb2N1bWVudFZpZXcoKSksbnVsbCE9KG49dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIG4uY29tcG9zaXRpb25Db250cm9sbGVyRGlkUmVuZGVyP24uY29tcG9zaXRpb25Db250cm9sbGVyRGlkUmVuZGVyKCk6dm9pZCAwfSxjLnByb3RvdHlwZS5yZXJlbmRlclZpZXdGb3JPYmplY3Q9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuaW52YWxpZGF0ZVZpZXdGb3JPYmplY3QodCksdGhpcy5yZW5kZXIoKX0sYy5wcm90b3R5cGUuaW52YWxpZGF0ZVZpZXdGb3JPYmplY3Q9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuZG9jdW1lbnRWaWV3LmludmFsaWRhdGVWaWV3Rm9yT2JqZWN0KHQpfSxjLnByb3RvdHlwZS5pc1ZpZXdDYWNoaW5nRW5hYmxlZD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmRvY3VtZW50Vmlldy5pc1ZpZXdDYWNoaW5nRW5hYmxlZCgpfSxjLnByb3RvdHlwZS5lbmFibGVWaWV3Q2FjaGluZz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmRvY3VtZW50Vmlldy5lbmFibGVWaWV3Q2FjaGluZygpfSxjLnByb3RvdHlwZS5kaXNhYmxlVmlld0NhY2hpbmc9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5kb2N1bWVudFZpZXcuZGlzYWJsZVZpZXdDYWNoaW5nKCl9LGMucHJvdG90eXBlLnJlZnJlc2hWaWV3Q2FjaGU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5kb2N1bWVudFZpZXcuZ2FyYmFnZUNvbGxlY3RDYWNoZWRWaWV3cygpfSxjLnByb3RvdHlwZS5pc0VkaXRpbmdBdHRhY2htZW50PWZ1bmN0aW9uKCl7cmV0dXJuIG51bGwhPXRoaXMuYXR0YWNobWVudEVkaXRvcn0sYy5wcm90b3R5cGUuaW5zdGFsbEF0dGFjaG1lbnRFZGl0b3JGb3JBdHRhY2htZW50PWZ1bmN0aW9uKHQsbil7dmFyIGksbyxyO2lmKChudWxsIT0ocj10aGlzLmF0dGFjaG1lbnRFZGl0b3IpP3IuYXR0YWNobWVudDp2b2lkIDApIT09dCYmKG89dGhpcy5kb2N1bWVudFZpZXcuZmluZEVsZW1lbnRGb3JPYmplY3QodCkpKXJldHVybiB0aGlzLnVuaW5zdGFsbEF0dGFjaG1lbnRFZGl0b3IoKSxpPXRoaXMuY29tcG9zaXRpb24uZG9jdW1lbnQuZ2V0QXR0YWNobWVudFBpZWNlRm9yQXR0YWNobWVudCh0KSx0aGlzLmF0dGFjaG1lbnRFZGl0b3I9bmV3IGUuQXR0YWNobWVudEVkaXRvckNvbnRyb2xsZXIoaSxvLHRoaXMuZWxlbWVudCxuKSx0aGlzLmF0dGFjaG1lbnRFZGl0b3IuZGVsZWdhdGU9dGhpc30sYy5wcm90b3R5cGUudW5pbnN0YWxsQXR0YWNobWVudEVkaXRvcj1mdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLmF0dGFjaG1lbnRFZGl0b3IpP3QudW5pbnN0YWxsKCk6dm9pZCAwfSxjLnByb3RvdHlwZS5kaWRVbmluc3RhbGxBdHRhY2htZW50RWRpdG9yPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0YWNobWVudEVkaXRvcj1udWxsLHRoaXMucmVuZGVyKCl9LGMucHJvdG90eXBlLmF0dGFjaG1lbnRFZGl0b3JEaWRSZXF1ZXN0VXBkYXRpbmdBdHRyaWJ1dGVzRm9yQXR0YWNobWVudD1mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybiBudWxsIT0obj10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2Ygbi5jb21wb3NpdGlvbkNvbnRyb2xsZXJXaWxsVXBkYXRlQXR0YWNobWVudCYmbi5jb21wb3NpdGlvbkNvbnRyb2xsZXJXaWxsVXBkYXRlQXR0YWNobWVudChlKSx0aGlzLmNvbXBvc2l0aW9uLnVwZGF0ZUF0dHJpYnV0ZXNGb3JBdHRhY2htZW50KHQsZSl9LGMucHJvdG90eXBlLmF0dGFjaG1lbnRFZGl0b3JEaWRSZXF1ZXN0UmVtb3ZpbmdBdHRyaWJ1dGVGb3JBdHRhY2htZW50PWZ1bmN0aW9uKHQsZSl7dmFyIG47cmV0dXJuIG51bGwhPShuPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBuLmNvbXBvc2l0aW9uQ29udHJvbGxlcldpbGxVcGRhdGVBdHRhY2htZW50JiZuLmNvbXBvc2l0aW9uQ29udHJvbGxlcldpbGxVcGRhdGVBdHRhY2htZW50KGUpLHRoaXMuY29tcG9zaXRpb24ucmVtb3ZlQXR0cmlidXRlRm9yQXR0YWNobWVudCh0LGUpfSxjLnByb3RvdHlwZS5hdHRhY2htZW50RWRpdG9yRGlkUmVxdWVzdFJlbW92YWxPZkF0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBlLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZFJlcXVlc3RSZW1vdmFsT2ZBdHRhY2htZW50P2UuY29tcG9zaXRpb25Db250cm9sbGVyRGlkUmVxdWVzdFJlbW92YWxPZkF0dGFjaG1lbnQodCk6dm9pZCAwfSxjLnByb3RvdHlwZS5hdHRhY2htZW50RWRpdG9yRGlkUmVxdWVzdERlc2VsZWN0aW5nQXR0YWNobWVudD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gbnVsbCE9KGU9dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIGUuY29tcG9zaXRpb25Db250cm9sbGVyRGlkUmVxdWVzdERlc2VsZWN0aW5nQXR0YWNobWVudD9lLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZFJlcXVlc3REZXNlbGVjdGluZ0F0dGFjaG1lbnQodCk6dm9pZCAwfSxjLnByb3RvdHlwZS5jYW5TeW5jRG9jdW1lbnRWaWV3PWZ1bmN0aW9uKCl7cmV0dXJuIXRoaXMuaXNFZGl0aW5nQXR0YWNobWVudCgpfSxjLnByb3RvdHlwZS5maW5kQXR0YWNobWVudEZvckVsZW1lbnQ9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uZG9jdW1lbnQuZ2V0QXR0YWNobWVudEJ5SWQocGFyc2VJbnQodC5kYXRhc2V0LnRyaXhJZCwxMCkpfSxjfShlLkJhc2ljT2JqZWN0KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0LG4saSxvPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIHQuYXBwbHkoZSxhcmd1bWVudHMpfX0scj1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpcy5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxzPXt9Lmhhc093blByb3BlcnR5O249ZS5oYW5kbGVFdmVudCxpPWUudHJpZ2dlckV2ZW50LHQ9ZS5maW5kQ2xvc2VzdEVsZW1lbnRGcm9tTm9kZSxlLlRvb2xiYXJDb250cm9sbGVyPWZ1bmN0aW9uKGUpe2Z1bmN0aW9uIHModCl7dGhpcy5lbGVtZW50PXQsdGhpcy5kaWRLZXlEb3duRGlhbG9nSW5wdXQ9byh0aGlzLmRpZEtleURvd25EaWFsb2dJbnB1dCx0aGlzKSx0aGlzLmRpZENsaWNrRGlhbG9nQnV0dG9uPW8odGhpcy5kaWRDbGlja0RpYWxvZ0J1dHRvbix0aGlzKSx0aGlzLmRpZENsaWNrQXR0cmlidXRlQnV0dG9uPW8odGhpcy5kaWRDbGlja0F0dHJpYnV0ZUJ1dHRvbix0aGlzKSx0aGlzLmRpZENsaWNrQWN0aW9uQnV0dG9uPW8odGhpcy5kaWRDbGlja0FjdGlvbkJ1dHRvbix0aGlzKSx0aGlzLmF0dHJpYnV0ZXM9e30sdGhpcy5hY3Rpb25zPXt9LHRoaXMucmVzZXREaWFsb2dJbnB1dHMoKSxuKFwibW91c2Vkb3duXCIse29uRWxlbWVudDp0aGlzLmVsZW1lbnQsbWF0Y2hpbmdTZWxlY3RvcjphLHdpdGhDYWxsYmFjazp0aGlzLmRpZENsaWNrQWN0aW9uQnV0dG9ufSksbihcIm1vdXNlZG93blwiLHtvbkVsZW1lbnQ6dGhpcy5lbGVtZW50LG1hdGNoaW5nU2VsZWN0b3I6Yyx3aXRoQ2FsbGJhY2s6dGhpcy5kaWRDbGlja0F0dHJpYnV0ZUJ1dHRvbn0pLG4oXCJjbGlja1wiLHtvbkVsZW1lbnQ6dGhpcy5lbGVtZW50LG1hdGNoaW5nU2VsZWN0b3I6dixwcmV2ZW50RGVmYXVsdDohMH0pLG4oXCJjbGlja1wiLHtvbkVsZW1lbnQ6dGhpcy5lbGVtZW50LG1hdGNoaW5nU2VsZWN0b3I6bCx3aXRoQ2FsbGJhY2s6dGhpcy5kaWRDbGlja0RpYWxvZ0J1dHRvbn0pLG4oXCJrZXlkb3duXCIse29uRWxlbWVudDp0aGlzLmVsZW1lbnQsbWF0Y2hpbmdTZWxlY3RvcjpoLHdpdGhDYWxsYmFjazp0aGlzLmRpZEtleURvd25EaWFsb2dJbnB1dH0pfXZhciBhLHUsYyxsLGgscCxkLGYsZyxtLHY7cmV0dXJuIHIocyxlKSxjPVwiW2RhdGEtdHJpeC1hdHRyaWJ1dGVdXCIsYT1cIltkYXRhLXRyaXgtYWN0aW9uXVwiLHY9YytcIiwgXCIrYSxwPVwiW2RhdGEtdHJpeC1kaWFsb2ddXCIsdT1wK1wiW2RhdGEtdHJpeC1hY3RpdmVdXCIsbD1wK1wiIFtkYXRhLXRyaXgtbWV0aG9kXVwiLGg9cCtcIiBbZGF0YS10cml4LWlucHV0XVwiLHMucHJvdG90eXBlLmRpZENsaWNrQWN0aW9uQnV0dG9uPWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvO3JldHVybiBudWxsIT0oaT10aGlzLmRlbGVnYXRlKSYmaS50b29sYmFyRGlkQ2xpY2tCdXR0b24oKSx0LnByZXZlbnREZWZhdWx0KCksbj1kKGUpLHRoaXMuZ2V0RGlhbG9nKG4pP3RoaXMudG9nZ2xlRGlhbG9nKG4pOm51bGwhPShvPXRoaXMuZGVsZWdhdGUpP28udG9vbGJhckRpZEludm9rZUFjdGlvbihuKTp2b2lkIDB9LHMucHJvdG90eXBlLmRpZENsaWNrQXR0cmlidXRlQnV0dG9uPWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvO3JldHVybiBudWxsIT0oaT10aGlzLmRlbGVnYXRlKSYmaS50b29sYmFyRGlkQ2xpY2tCdXR0b24oKSx0LnByZXZlbnREZWZhdWx0KCksbj1mKGUpLHRoaXMuZ2V0RGlhbG9nKG4pP3RoaXMudG9nZ2xlRGlhbG9nKG4pOm51bGwhPShvPXRoaXMuZGVsZWdhdGUpJiZvLnRvb2xiYXJEaWRUb2dnbGVBdHRyaWJ1dGUobiksdGhpcy5yZWZyZXNoQXR0cmlidXRlQnV0dG9ucygpfSxzLnByb3RvdHlwZS5kaWRDbGlja0RpYWxvZ0J1dHRvbj1mdW5jdGlvbihlLG4pe3ZhciBpLG87cmV0dXJuIGk9dChuLHttYXRjaGluZ1NlbGVjdG9yOnB9KSxvPW4uZ2V0QXR0cmlidXRlKFwiZGF0YS10cml4LW1ldGhvZFwiKSx0aGlzW29dLmNhbGwodGhpcyxpKX0scy5wcm90b3R5cGUuZGlkS2V5RG93bkRpYWxvZ0lucHV0PWZ1bmN0aW9uKHQsZSl7dmFyIG4saTtyZXR1cm4gMTM9PT10LmtleUNvZGUmJih0LnByZXZlbnREZWZhdWx0KCksbj1lLmdldEF0dHJpYnV0ZShcIm5hbWVcIiksaT10aGlzLmdldERpYWxvZyhuKSx0aGlzLnNldEF0dHJpYnV0ZShpKSksMjc9PT10LmtleUNvZGU/KHQucHJldmVudERlZmF1bHQoKSx0aGlzLmhpZGVEaWFsb2coKSk6dm9pZCAwfSxzLnByb3RvdHlwZS51cGRhdGVBY3Rpb25zPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmFjdGlvbnM9dCx0aGlzLnJlZnJlc2hBY3Rpb25CdXR0b25zKCl9LHMucHJvdG90eXBlLnJlZnJlc2hBY3Rpb25CdXR0b25zPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWFjaEFjdGlvbkJ1dHRvbihmdW5jdGlvbih0KXtyZXR1cm4gZnVuY3Rpb24oZSxuKXtyZXR1cm4gZS5kaXNhYmxlZD10LmFjdGlvbnNbbl09PT0hMX19KHRoaXMpKX0scy5wcm90b3R5cGUuZWFjaEFjdGlvbkJ1dHRvbj1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxyO2ZvcihvPXRoaXMuZWxlbWVudC5xdWVyeVNlbGVjdG9yQWxsKGEpLHI9W10sbj0wLGk9by5sZW5ndGg7aT5uO24rKyllPW9bbl0sci5wdXNoKHQoZSxkKGUpKSk7cmV0dXJuIHJ9LHMucHJvdG90eXBlLnVwZGF0ZUF0dHJpYnV0ZXM9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuYXR0cmlidXRlcz10LHRoaXMucmVmcmVzaEF0dHJpYnV0ZUJ1dHRvbnMoKX0scy5wcm90b3R5cGUucmVmcmVzaEF0dHJpYnV0ZUJ1dHRvbnM9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5lYWNoQXR0cmlidXRlQnV0dG9uKGZ1bmN0aW9uKHQpe3JldHVybiBmdW5jdGlvbihlLG4pe3JldHVybiBlLmRpc2FibGVkPXQuYXR0cmlidXRlc1tuXT09PSExLHQuYXR0cmlidXRlc1tuXXx8dC5kaWFsb2dJc1Zpc2libGUobik/KGUuc2V0QXR0cmlidXRlKFwiZGF0YS10cml4LWFjdGl2ZVwiLFwiXCIpLGUuY2xhc3NMaXN0LmFkZChcInRyaXgtYWN0aXZlXCIpKTooZS5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtYWN0aXZlXCIpLGUuY2xhc3NMaXN0LnJlbW92ZShcInRyaXgtYWN0aXZlXCIpKX19KHRoaXMpKX0scy5wcm90b3R5cGUuZWFjaEF0dHJpYnV0ZUJ1dHRvbj1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxyO2ZvcihvPXRoaXMuZWxlbWVudC5xdWVyeVNlbGVjdG9yQWxsKGMpLHI9W10sbj0wLGk9by5sZW5ndGg7aT5uO24rKyllPW9bbl0sci5wdXNoKHQoZSxmKGUpKSk7cmV0dXJuIHJ9LHMucHJvdG90eXBlLmFwcGx5S2V5Ym9hcmRDb21tYW5kPWZ1bmN0aW9uKHQpe3ZhciBlLG4sbyxyLHMsYSx1O2ZvcihzPUpTT04uc3RyaW5naWZ5KHQuc29ydCgpKSx1PXRoaXMuZWxlbWVudC5xdWVyeVNlbGVjdG9yQWxsKFwiW2RhdGEtdHJpeC1rZXldXCIpLHI9MCxhPXUubGVuZ3RoO2E+cjtyKyspaWYoZT11W3JdLG89ZS5nZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgta2V5XCIpLnNwbGl0KFwiK1wiKSxuPUpTT04uc3RyaW5naWZ5KG8uc29ydCgpKSxuPT09cylyZXR1cm4gaShcIm1vdXNlZG93blwiLHtvbkVsZW1lbnQ6ZX0pLCEwO3JldHVybiExfSxzLnByb3RvdHlwZS5kaWFsb2dJc1Zpc2libGU9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuKGU9dGhpcy5nZXREaWFsb2codCkpP2UuaGFzQXR0cmlidXRlKFwiZGF0YS10cml4LWFjdGl2ZVwiKTp2b2lkIDB9LHMucHJvdG90eXBlLnRvZ2dsZURpYWxvZz1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5kaWFsb2dJc1Zpc2libGUodCk/dGhpcy5oaWRlRGlhbG9nKCk6dGhpcy5zaG93RGlhbG9nKHQpfSxzLnByb3RvdHlwZS5zaG93RGlhbG9nPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHIscyxhLHUsYyxsO2Zvcih0aGlzLmhpZGVEaWFsb2coKSxudWxsIT0oYT10aGlzLmRlbGVnYXRlKSYmYS50b29sYmFyV2lsbFNob3dEaWFsb2coKSxpPXRoaXMuZ2V0RGlhbG9nKHQpLGkuc2V0QXR0cmlidXRlKFwiZGF0YS10cml4LWFjdGl2ZVwiLFwiXCIpLGkuY2xhc3NMaXN0LmFkZChcInRyaXgtYWN0aXZlXCIpLHU9aS5xdWVyeVNlbGVjdG9yQWxsKFwiaW5wdXRbZGlzYWJsZWRdXCIpLG89MCxzPXUubGVuZ3RoO3M+bztvKyspbj11W29dLG4ucmVtb3ZlQXR0cmlidXRlKFwiZGlzYWJsZWRcIik7cmV0dXJuKGU9ZihpKSkmJihyPW0oaSx0KSkmJihyLnZhbHVlPW51bGwhPShjPXRoaXMuYXR0cmlidXRlc1tlXSk/YzpcIlwiLHIuc2VsZWN0KCkpLG51bGwhPShsPXRoaXMuZGVsZWdhdGUpP2wudG9vbGJhckRpZFNob3dEaWFsb2codCk6dm9pZCAwfSxzLnByb3RvdHlwZS5zZXRBdHRyaWJ1dGU9ZnVuY3Rpb24odCl7dmFyIGUsbixpO3JldHVybiBlPWYodCksbj1tKHQsZSksbi53aWxsVmFsaWRhdGUmJiFuLmNoZWNrVmFsaWRpdHkoKT8obi5zZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtdmFsaWRhdGVcIixcIlwiKSxuLmNsYXNzTGlzdC5hZGQoXCJ0cml4LXZhbGlkYXRlXCIpLG4uZm9jdXMoKSk6KG51bGwhPShpPXRoaXMuZGVsZWdhdGUpJiZpLnRvb2xiYXJEaWRVcGRhdGVBdHRyaWJ1dGUoZSxuLnZhbHVlKSx0aGlzLmhpZGVEaWFsb2coKSl9LHMucHJvdG90eXBlLnJlbW92ZUF0dHJpYnV0ZT1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiBlPWYodCksbnVsbCE9KG49dGhpcy5kZWxlZ2F0ZSkmJm4udG9vbGJhckRpZFJlbW92ZUF0dHJpYnV0ZShlKSx0aGlzLmhpZGVEaWFsb2coKX0scy5wcm90b3R5cGUuaGlkZURpYWxvZz1mdW5jdGlvbigpe3ZhciB0LGU7cmV0dXJuKHQ9dGhpcy5lbGVtZW50LnF1ZXJ5U2VsZWN0b3IodSkpPyh0LnJlbW92ZUF0dHJpYnV0ZShcImRhdGEtdHJpeC1hY3RpdmVcIiksdC5jbGFzc0xpc3QucmVtb3ZlKFwidHJpeC1hY3RpdmVcIiksdGhpcy5yZXNldERpYWxvZ0lucHV0cygpLG51bGwhPShlPXRoaXMuZGVsZWdhdGUpP2UudG9vbGJhckRpZEhpZGVEaWFsb2coZyh0KSk6dm9pZCAwKTp2b2lkIDB9LHMucHJvdG90eXBlLnJlc2V0RGlhbG9nSW5wdXRzPWZ1bmN0aW9uKCl7dmFyIHQsZSxuLGksbztmb3IoaT10aGlzLmVsZW1lbnQucXVlcnlTZWxlY3RvckFsbChoKSxvPVtdLHQ9MCxuPWkubGVuZ3RoO24+dDt0KyspZT1pW3RdLGUuc2V0QXR0cmlidXRlKFwiZGlzYWJsZWRcIixcImRpc2FibGVkXCIpLGUucmVtb3ZlQXR0cmlidXRlKFwiZGF0YS10cml4LXZhbGlkYXRlXCIpLG8ucHVzaChlLmNsYXNzTGlzdC5yZW1vdmUoXCJ0cml4LXZhbGlkYXRlXCIpKTtyZXR1cm4gb30scy5wcm90b3R5cGUuZ2V0RGlhbG9nPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmVsZW1lbnQucXVlcnlTZWxlY3RvcihcIltkYXRhLXRyaXgtZGlhbG9nPVwiK3QrXCJdXCIpfSxtPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIG51bGw9PWUmJihlPWYodCkpLHQucXVlcnlTZWxlY3RvcihcIltkYXRhLXRyaXgtaW5wdXRdW25hbWU9J1wiK2UrXCInXVwiKX0sZD1mdW5jdGlvbih0KXtyZXR1cm4gdC5nZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtYWN0aW9uXCIpfSxmPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBudWxsIT0oZT10LmdldEF0dHJpYnV0ZShcImRhdGEtdHJpeC1hdHRyaWJ1dGVcIikpP2U6dC5nZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtZGlhbG9nLWF0dHJpYnV0ZVwiKX0sZz1mdW5jdGlvbih0KXtyZXR1cm4gdC5nZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtZGlhbG9nXCIpfSxzfShlLkJhc2ljT2JqZWN0KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gaSgpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSluLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIGkucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBpLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG49e30uaGFzT3duUHJvcGVydHk7ZS5JbWFnZVByZWxvYWRPcGVyYXRpb249ZnVuY3Rpb24oZSl7ZnVuY3Rpb24gbih0KXt0aGlzLnVybD10fXJldHVybiB0KG4sZSksbi5wcm90b3R5cGUucGVyZm9ybT1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT1uZXcgSW1hZ2UsZS5vbmxvYWQ9ZnVuY3Rpb24obil7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIGUud2lkdGg9bi53aWR0aD1lLm5hdHVyYWxXaWR0aCxlLmhlaWdodD1uLmhlaWdodD1lLm5hdHVyYWxIZWlnaHQsdCghMCxlKX19KHRoaXMpLGUub25lcnJvcj1mdW5jdGlvbigpe3JldHVybiB0KCExKX0sZS5zcmM9dGhpcy51cmx9LG59KGUuT3BlcmF0aW9uKX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIHQuYXBwbHkoZSxhcmd1bWVudHMpfX0sbj1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBvIGluIGUpaS5jYWxsKGUsbykmJih0W29dPWVbb10pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxpPXt9Lmhhc093blByb3BlcnR5O2UuQXR0YWNobWVudD1mdW5jdGlvbihpKXtmdW5jdGlvbiBvKG4pe251bGw9PW4mJihuPXt9KSx0aGlzLnJlbGVhc2VGaWxlPXQodGhpcy5yZWxlYXNlRmlsZSx0aGlzKSxvLl9fc3VwZXJfXy5jb25zdHJ1Y3Rvci5hcHBseSh0aGlzLGFyZ3VtZW50cyksdGhpcy5hdHRyaWJ1dGVzPWUuSGFzaC5ib3gobiksdGhpcy5kaWRDaGFuZ2VBdHRyaWJ1dGVzKCl9cmV0dXJuIG4obyxpKSxvLnByZXZpZXdhYmxlUGF0dGVybj0vXmltYWdlKFxcLyhnaWZ8cG5nfGpwZT9nKXwkKS8sby5hdHRhY2htZW50Rm9yRmlsZT1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiBuPXRoaXMuYXR0cmlidXRlc0ZvckZpbGUodCksZT1uZXcgdGhpcyhuKSxlLnNldEZpbGUodCksZX0sby5hdHRyaWJ1dGVzRm9yRmlsZT1mdW5jdGlvbih0KXtyZXR1cm4gbmV3IGUuSGFzaCh7ZmlsZW5hbWU6dC5uYW1lLGZpbGVzaXplOnQuc2l6ZSxjb250ZW50VHlwZTp0LnR5cGV9KX0sby5mcm9tSlNPTj1mdW5jdGlvbih0KXtyZXR1cm4gbmV3IHRoaXModCl9LG8ucHJvdG90eXBlLmdldEF0dHJpYnV0ZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzLmdldCh0KX0sby5wcm90b3R5cGUuaGFzQXR0cmlidXRlPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmF0dHJpYnV0ZXMuaGFzKHQpfSxvLnByb3RvdHlwZS5nZXRBdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0cmlidXRlcy50b09iamVjdCgpfSxvLnByb3RvdHlwZS5zZXRBdHRyaWJ1dGVzPWZ1bmN0aW9uKHQpe3ZhciBlLG4saTtyZXR1cm4gbnVsbD09dCYmKHQ9e30pLGU9dGhpcy5hdHRyaWJ1dGVzLm1lcmdlKHQpLHRoaXMuYXR0cmlidXRlcy5pc0VxdWFsVG8oZSk/dm9pZCAwOih0aGlzLmF0dHJpYnV0ZXM9ZSx0aGlzLmRpZENoYW5nZUF0dHJpYnV0ZXMoKSxudWxsIT0obj10aGlzLnByZXZpZXdEZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIG4uYXR0YWNobWVudERpZENoYW5nZUF0dHJpYnV0ZXMmJm4uYXR0YWNobWVudERpZENoYW5nZUF0dHJpYnV0ZXModGhpcyksbnVsbCE9KGk9dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIGkuYXR0YWNobWVudERpZENoYW5nZUF0dHJpYnV0ZXM/aS5hdHRhY2htZW50RGlkQ2hhbmdlQXR0cmlidXRlcyh0aGlzKTp2b2lkIDApfSxvLnByb3RvdHlwZS5kaWRDaGFuZ2VBdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuaXNQcmV2aWV3YWJsZSgpP3RoaXMucHJlbG9hZFVSTCgpOnZvaWQgMH0sby5wcm90b3R5cGUuaXNQZW5kaW5nPWZ1bmN0aW9uKCl7cmV0dXJuIG51bGwhPXRoaXMuZmlsZSYmISh0aGlzLmdldFVSTCgpfHx0aGlzLmdldEhyZWYoKSl9LG8ucHJvdG90eXBlLmlzUHJldmlld2FibGU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzLmhhcyhcInByZXZpZXdhYmxlXCIpP3RoaXMuYXR0cmlidXRlcy5nZXQoXCJwcmV2aWV3YWJsZVwiKTp0aGlzLmNvbnN0cnVjdG9yLnByZXZpZXdhYmxlUGF0dGVybi50ZXN0KHRoaXMuZ2V0Q29udGVudFR5cGUoKSl9LG8ucHJvdG90eXBlLmdldFR5cGU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5oYXNDb250ZW50KCk/XCJjb250ZW50XCI6dGhpcy5pc1ByZXZpZXdhYmxlKCk/XCJwcmV2aWV3XCI6XCJmaWxlXCJ9LG8ucHJvdG90eXBlLmdldFVSTD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmF0dHJpYnV0ZXMuZ2V0KFwidXJsXCIpfSxvLnByb3RvdHlwZS5nZXRIcmVmPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0cmlidXRlcy5nZXQoXCJocmVmXCIpfSxvLnByb3RvdHlwZS5nZXRGaWxlbmFtZT1mdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLmF0dHJpYnV0ZXMuZ2V0KFwiZmlsZW5hbWVcIikpP3Q6XCJcIn0sby5wcm90b3R5cGUuZ2V0RmlsZXNpemU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzLmdldChcImZpbGVzaXplXCIpfSxvLnByb3RvdHlwZS5nZXRGb3JtYXR0ZWRGaWxlc2l6ZT1mdW5jdGlvbigpe3ZhciB0O3JldHVybiB0PXRoaXMuYXR0cmlidXRlcy5nZXQoXCJmaWxlc2l6ZVwiKSxcIm51bWJlclwiPT10eXBlb2YgdD9lLmNvbmZpZy5maWxlU2l6ZS5mb3JtYXR0ZXIodCk6XCJcIn0sby5wcm90b3R5cGUuZ2V0RXh0ZW5zaW9uPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMuZ2V0RmlsZW5hbWUoKS5tYXRjaCgvXFwuKFxcdyspJC8pKT90WzFdLnRvTG93ZXJDYXNlKCk6dm9pZCAwfSxvLnByb3RvdHlwZS5nZXRDb250ZW50VHlwZT1mdW5jdGlvbigpe3JldHVybiB0aGlzLmF0dHJpYnV0ZXMuZ2V0KFwiY29udGVudFR5cGVcIil9LG8ucHJvdG90eXBlLmhhc0NvbnRlbnQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzLmhhcyhcImNvbnRlbnRcIil9LG8ucHJvdG90eXBlLmdldENvbnRlbnQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzLmdldChcImNvbnRlbnRcIil9LG8ucHJvdG90eXBlLmdldFdpZHRoPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0cmlidXRlcy5nZXQoXCJ3aWR0aFwiKX0sby5wcm90b3R5cGUuZ2V0SGVpZ2h0PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0cmlidXRlcy5nZXQoXCJoZWlnaHRcIil9LG8ucHJvdG90eXBlLmdldEZpbGU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5maWxlfSxvLnByb3RvdHlwZS5zZXRGaWxlPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmZpbGU9dCx0aGlzLmlzUHJldmlld2FibGUoKT90aGlzLnByZWxvYWRGaWxlKCk6dm9pZCAwfSxvLnByb3RvdHlwZS5yZWxlYXNlRmlsZT1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlbGVhc2VQcmVsb2FkZWRGaWxlKCksdGhpcy5maWxlPW51bGx9LG8ucHJvdG90eXBlLmdldFVwbG9hZFByb2dyZXNzPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMudXBsb2FkUHJvZ3Jlc3MpP3Q6MH0sby5wcm90b3R5cGUuc2V0VXBsb2FkUHJvZ3Jlc3M9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHRoaXMudXBsb2FkUHJvZ3Jlc3MhPT10Pyh0aGlzLnVwbG9hZFByb2dyZXNzPXQsbnVsbCE9KGU9dGhpcy51cGxvYWRQcm9ncmVzc0RlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgZS5hdHRhY2htZW50RGlkQ2hhbmdlVXBsb2FkUHJvZ3Jlc3M/ZS5hdHRhY2htZW50RGlkQ2hhbmdlVXBsb2FkUHJvZ3Jlc3ModGhpcyk6dm9pZCAwKTp2b2lkIDB9LG8ucHJvdG90eXBlLnRvSlNPTj1mdW5jdGlvbigpe3JldHVybiB0aGlzLmdldEF0dHJpYnV0ZXMoKX0sby5wcm90b3R5cGUuZ2V0Q2FjaGVLZXk9ZnVuY3Rpb24oKXtyZXR1cm5bby5fX3N1cGVyX18uZ2V0Q2FjaGVLZXkuYXBwbHkodGhpcyxhcmd1bWVudHMpLHRoaXMuYXR0cmlidXRlcy5nZXRDYWNoZUtleSgpLHRoaXMuZ2V0UHJldmlld1VSTCgpXS5qb2luKFwiL1wiKX0sby5wcm90b3R5cGUuZ2V0UHJldmlld1VSTD1mdW5jdGlvbigpe3JldHVybiB0aGlzLnByZXZpZXdVUkx8fHRoaXMucHJlbG9hZGluZ1VSTH0sby5wcm90b3R5cGUuc2V0UHJldmlld1VSTD1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiB0IT09dGhpcy5nZXRQcmV2aWV3VVJMKCk/KHRoaXMucHJldmlld1VSTD10LG51bGwhPShlPXRoaXMucHJldmlld0RlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgZS5hdHRhY2htZW50RGlkQ2hhbmdlQXR0cmlidXRlcyYmZS5hdHRhY2htZW50RGlkQ2hhbmdlQXR0cmlidXRlcyh0aGlzKSxudWxsIT0obj10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2Ygbi5hdHRhY2htZW50RGlkQ2hhbmdlUHJldmlld1VSTD9uLmF0dGFjaG1lbnREaWRDaGFuZ2VQcmV2aWV3VVJMKHRoaXMpOnZvaWQgMCk6dm9pZCAwfSxvLnByb3RvdHlwZS5wcmVsb2FkVVJMPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMucHJlbG9hZCh0aGlzLmdldFVSTCgpLHRoaXMucmVsZWFzZUZpbGUpfSxvLnByb3RvdHlwZS5wcmVsb2FkRmlsZT1mdW5jdGlvbigpe3JldHVybiB0aGlzLmZpbGU/KHRoaXMuZmlsZU9iamVjdFVSTD1VUkwuY3JlYXRlT2JqZWN0VVJMKHRoaXMuZmlsZSksdGhpcy5wcmVsb2FkKHRoaXMuZmlsZU9iamVjdFVSTCkpOnZvaWQgMH0sby5wcm90b3R5cGUucmVsZWFzZVByZWxvYWRlZEZpbGU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5maWxlT2JqZWN0VVJMPyhVUkwucmV2b2tlT2JqZWN0VVJMKHRoaXMuZmlsZU9iamVjdFVSTCksdGhpcy5maWxlT2JqZWN0VVJMPW51bGwpOnZvaWQgMH0sby5wcm90b3R5cGUucHJlbG9hZD1mdW5jdGlvbih0LG4pe3ZhciBpO3JldHVybiB0JiZ0IT09dGhpcy5nZXRQcmV2aWV3VVJMKCk/KHRoaXMucHJlbG9hZGluZ1VSTD10LGk9bmV3IGUuSW1hZ2VQcmVsb2FkT3BlcmF0aW9uKHQpLGkudGhlbihmdW5jdGlvbihlKXtyZXR1cm4gZnVuY3Rpb24oaSl7dmFyIG8scjtyZXR1cm4gcj1pLndpZHRoLG89aS5oZWlnaHQsZS5nZXRXaWR0aCgpJiZlLmdldEhlaWdodCgpfHxlLnNldEF0dHJpYnV0ZXMoe3dpZHRoOnIsaGVpZ2h0Om99KSxlLnByZWxvYWRpbmdVUkw9bnVsbCxlLnNldFByZXZpZXdVUkwodCksXCJmdW5jdGlvblwiPT10eXBlb2Ygbj9uKCk6dm9pZCAwfX0odGhpcykpW1wiY2F0Y2hcIl0oZnVuY3Rpb24odCl7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIHQucHJlbG9hZGluZ1VSTD1udWxsLFwiZnVuY3Rpb25cIj09dHlwZW9mIG4/bigpOnZvaWQgMH19KHRoaXMpKSk6dm9pZCAwfSxvfShlLk9iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdD1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIGkoKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBvIGluIGUpbi5jYWxsKGUsbykmJih0W29dPWVbb10pO3JldHVybiBpLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgaSx0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxuPXt9Lmhhc093blByb3BlcnR5O2UuUGllY2U9ZnVuY3Rpb24obil7ZnVuY3Rpb24gaSh0LG4pe251bGw9PW4mJihuPXt9KSxpLl9fc3VwZXJfXy5jb25zdHJ1Y3Rvci5hcHBseSh0aGlzLGFyZ3VtZW50cyksdGhpcy5hdHRyaWJ1dGVzPWUuSGFzaC5ib3gobil9cmV0dXJuIHQoaSxuKSxpLnR5cGVzPXt9LGkucmVnaXN0ZXJUeXBlPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIGUudHlwZT10LHRoaXMudHlwZXNbdF09ZX0saS5mcm9tSlNPTj1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4oZT10aGlzLnR5cGVzW3QudHlwZV0pP2UuZnJvbUpTT04odCk6dm9pZCAwfSxpLnByb3RvdHlwZS5jb3B5V2l0aEF0dHJpYnV0ZXM9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyB0aGlzLmNvbnN0cnVjdG9yKHRoaXMuZ2V0VmFsdWUoKSx0KX0saS5wcm90b3R5cGUuY29weVdpdGhBZGRpdGlvbmFsQXR0cmlidXRlcz1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5jb3B5V2l0aEF0dHJpYnV0ZXModGhpcy5hdHRyaWJ1dGVzLm1lcmdlKHQpKX0saS5wcm90b3R5cGUuY29weVdpdGhvdXRBdHRyaWJ1dGU9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29weVdpdGhBdHRyaWJ1dGVzKHRoaXMuYXR0cmlidXRlcy5yZW1vdmUodCkpfSxpLnByb3RvdHlwZS5jb3B5PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29weVdpdGhBdHRyaWJ1dGVzKHRoaXMuYXR0cmlidXRlcyl9LGkucHJvdG90eXBlLmdldEF0dHJpYnV0ZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzLmdldCh0KX0saS5wcm90b3R5cGUuZ2V0QXR0cmlidXRlc0hhc2g9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzfSxpLnByb3RvdHlwZS5nZXRBdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0cmlidXRlcy50b09iamVjdCgpfSxpLnByb3RvdHlwZS5nZXRDb21tb25BdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7dmFyIHQsZSxuO3JldHVybihuPXBpZWNlTGlzdC5nZXRQaWVjZUF0SW5kZXgoMCkpPyh0PW4uYXR0cmlidXRlcyxlPXQuZ2V0S2V5cygpLHBpZWNlTGlzdC5lYWNoUGllY2UoZnVuY3Rpb24obil7cmV0dXJuIGU9dC5nZXRLZXlzQ29tbW9uVG9IYXNoKG4uYXR0cmlidXRlcyksdD10LnNsaWNlKGUpfSksdC50b09iamVjdCgpKTp7fX0saS5wcm90b3R5cGUuaGFzQXR0cmlidXRlPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmF0dHJpYnV0ZXMuaGFzKHQpfSxpLnByb3RvdHlwZS5oYXNTYW1lU3RyaW5nVmFsdWVBc1BpZWNlPWZ1bmN0aW9uKHQpe3JldHVybiBudWxsIT10JiZ0aGlzLnRvU3RyaW5nKCk9PT10LnRvU3RyaW5nKCl9LGkucHJvdG90eXBlLmhhc1NhbWVBdHRyaWJ1dGVzQXNQaWVjZT1mdW5jdGlvbih0KXtyZXR1cm4gbnVsbCE9dCYmKHRoaXMuYXR0cmlidXRlcz09PXQuYXR0cmlidXRlc3x8dGhpcy5hdHRyaWJ1dGVzLmlzRXF1YWxUbyh0LmF0dHJpYnV0ZXMpKX0saS5wcm90b3R5cGUuaXNCbG9ja0JyZWFrPWZ1bmN0aW9uKCl7cmV0dXJuITF9LGkucHJvdG90eXBlLmlzRXF1YWxUbz1mdW5jdGlvbih0KXtyZXR1cm4gaS5fX3N1cGVyX18uaXNFcXVhbFRvLmFwcGx5KHRoaXMsYXJndW1lbnRzKXx8dGhpcy5oYXNTYW1lQ29uc3RydWN0b3JBcyh0KSYmdGhpcy5oYXNTYW1lU3RyaW5nVmFsdWVBc1BpZWNlKHQpJiZ0aGlzLmhhc1NhbWVBdHRyaWJ1dGVzQXNQaWVjZSh0KX0saS5wcm90b3R5cGUuaXNFbXB0eT1mdW5jdGlvbigpe3JldHVybiAwPT09dGhpcy5sZW5ndGh9LGkucHJvdG90eXBlLmlzU2VyaWFsaXphYmxlPWZ1bmN0aW9uKCl7cmV0dXJuITB9LGkucHJvdG90eXBlLnRvSlNPTj1mdW5jdGlvbigpe3JldHVybnt0eXBlOnRoaXMuY29uc3RydWN0b3IudHlwZSxhdHRyaWJ1dGVzOnRoaXMuZ2V0QXR0cmlidXRlcygpfX0saS5wcm90b3R5cGUuY29udGVudHNGb3JJbnNwZWN0aW9uPWZ1bmN0aW9uKCl7cmV0dXJue3R5cGU6dGhpcy5jb25zdHJ1Y3Rvci50eXBlLGF0dHJpYnV0ZXM6dGhpcy5hdHRyaWJ1dGVzLmluc3BlY3QoKX19LGkucHJvdG90eXBlLmNhbkJlR3JvdXBlZD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmhhc0F0dHJpYnV0ZShcImhyZWZcIil9LGkucHJvdG90eXBlLmNhbkJlR3JvdXBlZFdpdGg9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuZ2V0QXR0cmlidXRlKFwiaHJlZlwiKT09PXQuZ2V0QXR0cmlidXRlKFwiaHJlZlwiKX0saS5wcm90b3R5cGUuZ2V0TGVuZ3RoPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMubGVuZ3RofSxpLnByb3RvdHlwZS5jYW5CZUNvbnNvbGlkYXRlZFdpdGg9ZnVuY3Rpb24oKXtyZXR1cm4hMX0saX0oZS5PYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBpKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgbyBpbiBlKW4uY2FsbChlLG8pJiYodFtvXT1lW29dKTtyZXR1cm4gaS5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IGksdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sbj17fS5oYXNPd25Qcm9wZXJ0eTtlLlBpZWNlLnJlZ2lzdGVyVHlwZShcImF0dGFjaG1lbnRcIixlLkF0dGFjaG1lbnRQaWVjZT1mdW5jdGlvbihuKXtmdW5jdGlvbiBpKHQpe3RoaXMuYXR0YWNobWVudD10LGkuX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLmxlbmd0aD0xLHRoaXMuZW5zdXJlQXR0YWNobWVudEV4Y2x1c2l2ZWx5SGFzQXR0cmlidXRlKFwiaHJlZlwiKSx0aGlzLmF0dGFjaG1lbnQuaGFzQ29udGVudCgpfHx0aGlzLnJlbW92ZVByb2hpYml0ZWRBdHRyaWJ1dGVzKCl9cmV0dXJuIHQoaSxuKSxpLmZyb21KU09OPWZ1bmN0aW9uKHQpe3JldHVybiBuZXcgdGhpcyhlLkF0dGFjaG1lbnQuZnJvbUpTT04odC5hdHRhY2htZW50KSx0LmF0dHJpYnV0ZXMpfSxpLnBlcm1pdHRlZEF0dHJpYnV0ZXM9W1wiY2FwdGlvblwiLFwicHJlc2VudGF0aW9uXCJdLGkucHJvdG90eXBlLmVuc3VyZUF0dGFjaG1lbnRFeGNsdXNpdmVseUhhc0F0dHJpYnV0ZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5oYXNBdHRyaWJ1dGUodCk/KHRoaXMuYXR0YWNobWVudC5oYXNBdHRyaWJ1dGUodCl8fHRoaXMuYXR0YWNobWVudC5zZXRBdHRyaWJ1dGVzKHRoaXMuYXR0cmlidXRlcy5zbGljZSh0KSksdGhpcy5hdHRyaWJ1dGVzPXRoaXMuYXR0cmlidXRlcy5yZW1vdmUodCkpOnZvaWQgMH0saS5wcm90b3R5cGUucmVtb3ZlUHJvaGliaXRlZEF0dHJpYnV0ZXM9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gdD10aGlzLmF0dHJpYnV0ZXMuc2xpY2UodGhpcy5jb25zdHJ1Y3Rvci5wZXJtaXR0ZWRBdHRyaWJ1dGVzKSx0LmlzRXF1YWxUbyh0aGlzLmF0dHJpYnV0ZXMpP3ZvaWQgMDp0aGlzLmF0dHJpYnV0ZXM9dH0saS5wcm90b3R5cGUuZ2V0VmFsdWU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hdHRhY2htZW50fSxpLnByb3RvdHlwZS5pc1NlcmlhbGl6YWJsZT1mdW5jdGlvbigpe3JldHVybiF0aGlzLmF0dGFjaG1lbnQuaXNQZW5kaW5nKCl9LGkucHJvdG90eXBlLmdldENhcHRpb249ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5hdHRyaWJ1dGVzLmdldChcImNhcHRpb25cIikpP3Q6XCJcIn0saS5wcm90b3R5cGUuaXNFcXVhbFRvPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBpLl9fc3VwZXJfXy5pc0VxdWFsVG8uYXBwbHkodGhpcyxhcmd1bWVudHMpJiZ0aGlzLmF0dGFjaG1lbnQuaWQ9PT0obnVsbCE9dCYmbnVsbCE9KGU9dC5hdHRhY2htZW50KT9lLmlkOnZvaWQgMCl9LGkucHJvdG90eXBlLnRvU3RyaW5nPWZ1bmN0aW9uKCl7cmV0dXJuIGUuT0JKRUNUX1JFUExBQ0VNRU5UX0NIQVJBQ1RFUn0saS5wcm90b3R5cGUudG9KU09OPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIHQ9aS5fX3N1cGVyX18udG9KU09OLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0LmF0dGFjaG1lbnQ9dGhpcy5hdHRhY2htZW50LHR9LGkucHJvdG90eXBlLmdldENhY2hlS2V5PWZ1bmN0aW9uKCl7cmV0dXJuW2kuX19zdXBlcl9fLmdldENhY2hlS2V5LmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLmF0dGFjaG1lbnQuZ2V0Q2FjaGVLZXkoKV0uam9pbihcIi9cIil9LGkucHJvdG90eXBlLnRvQ29uc29sZT1mdW5jdGlvbigpe3JldHVybiBKU09OLnN0cmluZ2lmeSh0aGlzLnRvU3RyaW5nKCkpfSxpfShlLlBpZWNlKSl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSlpLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LGk9e30uaGFzT3duUHJvcGVydHk7dD1lLm5vcm1hbGl6ZU5ld2xpbmVzLGUuUGllY2UucmVnaXN0ZXJUeXBlKFwic3RyaW5nXCIsZS5TdHJpbmdQaWVjZT1mdW5jdGlvbihlKXtmdW5jdGlvbiBpKGUpe2kuX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLnN0cmluZz10KGUpLHRoaXMubGVuZ3RoPXRoaXMuc3RyaW5nLmxlbmd0aH1yZXR1cm4gbihpLGUpLGkuZnJvbUpTT049ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyB0aGlzKHQuc3RyaW5nLHQuYXR0cmlidXRlcyl9LGkucHJvdG90eXBlLmdldFZhbHVlPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuc3RyaW5nfSxpLnByb3RvdHlwZS50b1N0cmluZz1mdW5jdGlvbigpe3JldHVybiB0aGlzLnN0cmluZy50b1N0cmluZygpfSxpLnByb3RvdHlwZS5pc0Jsb2NrQnJlYWs9ZnVuY3Rpb24oKXtyZXR1cm5cIlxcblwiPT09dGhpcy50b1N0cmluZygpJiZ0aGlzLmdldEF0dHJpYnV0ZShcImJsb2NrQnJlYWtcIik9PT0hMH0saS5wcm90b3R5cGUudG9KU09OPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIHQ9aS5fX3N1cGVyX18udG9KU09OLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0LnN0cmluZz10aGlzLnN0cmluZyx0fSxpLnByb3RvdHlwZS5jYW5CZUNvbnNvbGlkYXRlZFdpdGg9ZnVuY3Rpb24odCl7cmV0dXJuIG51bGwhPXQmJnRoaXMuaGFzU2FtZUNvbnN0cnVjdG9yQXModCkmJnRoaXMuaGFzU2FtZUF0dHJpYnV0ZXNBc1BpZWNlKHQpfSxpLnByb3RvdHlwZS5jb25zb2xpZGF0ZVdpdGg9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyB0aGlzLmNvbnN0cnVjdG9yKHRoaXMudG9TdHJpbmcoKSt0LnRvU3RyaW5nKCksdGhpcy5hdHRyaWJ1dGVzKX0saS5wcm90b3R5cGUuc3BsaXRBdE9mZnNldD1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiAwPT09dD8oZT1udWxsLG49dGhpcyk6dD09PXRoaXMubGVuZ3RoPyhlPXRoaXMsbj1udWxsKTooZT1uZXcgdGhpcy5jb25zdHJ1Y3Rvcih0aGlzLnN0cmluZy5zbGljZSgwLHQpLHRoaXMuYXR0cmlidXRlcyksbj1uZXcgdGhpcy5jb25zdHJ1Y3Rvcih0aGlzLnN0cmluZy5zbGljZSh0KSx0aGlzLmF0dHJpYnV0ZXMpKSxbZSxuXX0saS5wcm90b3R5cGUudG9Db25zb2xlPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIHQ9dGhpcy5zdHJpbmcsdC5sZW5ndGg+MTUmJih0PXQuc2xpY2UoMCwxNCkrXCJcXHUyMDI2XCIpLEpTT04uc3RyaW5naWZ5KHQudG9TdHJpbmcoKSl9LGl9KGUuUGllY2UpKX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0LG49ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBuKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgbyBpbiBlKWkuY2FsbChlLG8pJiYodFtvXT1lW29dKTtyZXR1cm4gbi5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IG4sdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0saT17fS5oYXNPd25Qcm9wZXJ0eSxvPVtdLnNsaWNlO3Q9ZS5zcGxpY2VBcnJheSxlLlNwbGl0dGFibGVMaXN0PWZ1bmN0aW9uKGUpe2Z1bmN0aW9uIGkodCl7bnVsbD09dCYmKHQ9W10pLGkuX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLm9iamVjdHM9dC5zbGljZSgwKSx0aGlzLmxlbmd0aD10aGlzLm9iamVjdHMubGVuZ3RofXZhciByLHMsYTtyZXR1cm4gbihpLGUpLGkuYm94PWZ1bmN0aW9uKHQpe3JldHVybiB0IGluc3RhbmNlb2YgdGhpcz90Om5ldyB0aGlzKHQpfSxpLnByb3RvdHlwZS5pbmRleE9mPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLm9iamVjdHMuaW5kZXhPZih0KX0saS5wcm90b3R5cGUuc3BsaWNlPWZ1bmN0aW9uKCl7dmFyIGU7cmV0dXJuIGU9MTw9YXJndW1lbnRzLmxlbmd0aD9vLmNhbGwoYXJndW1lbnRzLDApOltdLG5ldyB0aGlzLmNvbnN0cnVjdG9yKHQuYXBwbHkobnVsbCxbdGhpcy5vYmplY3RzXS5jb25jYXQoby5jYWxsKGUpKSkpfSxpLnByb3RvdHlwZS5lYWNoT2JqZWN0PWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHIscztmb3Iocj10aGlzLm9iamVjdHMscz1bXSxuPWU9MCxpPXIubGVuZ3RoO2k+ZTtuPSsrZSlvPXJbbl0scy5wdXNoKHQobyxuKSk7cmV0dXJuIHN9LGkucHJvdG90eXBlLmluc2VydE9iamVjdEF0SW5kZXg9ZnVuY3Rpb24odCxlKXtyZXR1cm4gdGhpcy5zcGxpY2UoZSwwLHQpfSxpLnByb3RvdHlwZS5pbnNlcnRTcGxpdHRhYmxlTGlzdEF0SW5kZXg9ZnVuY3Rpb24odCxlKXtyZXR1cm4gdGhpcy5zcGxpY2UuYXBwbHkodGhpcyxbZSwwXS5jb25jYXQoby5jYWxsKHQub2JqZWN0cykpKX0saS5wcm90b3R5cGUuaW5zZXJ0U3BsaXR0YWJsZUxpc3RBdFBvc2l0aW9uPWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvO3JldHVybiBvPXRoaXMuc3BsaXRPYmplY3RBdFBvc2l0aW9uKGUpLGk9b1swXSxuPW9bMV0sbmV3IHRoaXMuY29uc3RydWN0b3IoaSkuaW5zZXJ0U3BsaXR0YWJsZUxpc3RBdEluZGV4KHQsbil9LGkucHJvdG90eXBlLmVkaXRPYmplY3RBdEluZGV4PWZ1bmN0aW9uKHQsZSl7cmV0dXJuIHRoaXMucmVwbGFjZU9iamVjdEF0SW5kZXgoZSh0aGlzLm9iamVjdHNbdF0pLHQpfSxpLnByb3RvdHlwZS5yZXBsYWNlT2JqZWN0QXRJbmRleD1mdW5jdGlvbih0LGUpe3JldHVybiB0aGlzLnNwbGljZShlLDEsdCl9LGkucHJvdG90eXBlLnJlbW92ZU9iamVjdEF0SW5kZXg9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuc3BsaWNlKHQsMSl9LGkucHJvdG90eXBlLmdldE9iamVjdEF0SW5kZXg9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMub2JqZWN0c1t0XX0saS5wcm90b3R5cGUuZ2V0U3BsaXR0YWJsZUxpc3RJblJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvO3JldHVybiBpPXRoaXMuc3BsaXRPYmplY3RzQXRSYW5nZSh0KSxuPWlbMF0sZT1pWzFdLG89aVsyXSxuZXcgdGhpcy5jb25zdHJ1Y3RvcihuLnNsaWNlKGUsbysxKSl9LGkucHJvdG90eXBlLnNlbGVjdFNwbGl0dGFibGVMaXN0PWZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG49ZnVuY3Rpb24oKXt2YXIgbixpLG8scjtmb3Iobz10aGlzLm9iamVjdHMscj1bXSxuPTAsaT1vLmxlbmd0aDtpPm47bisrKWU9b1tuXSx0KGUpJiZyLnB1c2goZSk7cmV0dXJuIHJ9LmNhbGwodGhpcyksbmV3IHRoaXMuY29uc3RydWN0b3Iobil9LGkucHJvdG90eXBlLnJlbW92ZU9iamVjdHNJblJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvO3JldHVybiBpPXRoaXMuc3BsaXRPYmplY3RzQXRSYW5nZSh0KSxuPWlbMF0sZT1pWzFdLG89aVsyXSxuZXcgdGhpcy5jb25zdHJ1Y3RvcihuKS5zcGxpY2UoZSxvLWUrMSl9LGkucHJvdG90eXBlLnRyYW5zZm9ybU9iamVjdHNJblJhbmdlPWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvLHIscyxhLHU7cmV0dXJuIHM9dGhpcy5zcGxpdE9iamVjdHNBdFJhbmdlKHQpLHI9c1swXSxpPXNbMV0sYT1zWzJdLHU9ZnVuY3Rpb24oKXt2YXIgdCxzLHU7Zm9yKHU9W10sbj10PTAscz1yLmxlbmd0aDtzPnQ7bj0rK3Qpbz1yW25dLHUucHVzaChuPj1pJiZhPj1uP2Uobyk6byk7cmV0dXJuIHV9KCksbmV3IHRoaXMuY29uc3RydWN0b3IodSl9LGkucHJvdG90eXBlLnNwbGl0T2JqZWN0c0F0UmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGUsbixpLG8scyx1O3JldHVybiBvPXRoaXMuc3BsaXRPYmplY3RBdFBvc2l0aW9uKGEodCkpLG49b1swXSxlPW9bMV0saT1vWzJdLHM9bmV3IHRoaXMuY29uc3RydWN0b3Iobikuc3BsaXRPYmplY3RBdFBvc2l0aW9uKHIodCkraSksbj1zWzBdLHU9c1sxXSxbbixlLHUtMV19LGkucHJvdG90eXBlLmdldE9iamVjdEF0UG9zaXRpb249ZnVuY3Rpb24odCl7dmFyIGUsbixpO3JldHVybiBpPXRoaXMuZmluZEluZGV4QW5kT2Zmc2V0QXRQb3NpdGlvbih0KSxlPWkuaW5kZXgsbj1pLm9mZnNldCx0aGlzLm9iamVjdHNbZV19LGkucHJvdG90eXBlLnNwbGl0T2JqZWN0QXRQb3NpdGlvbj1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxyLHMsYSx1LGMsbDtyZXR1cm4gcz10aGlzLmZpbmRJbmRleEFuZE9mZnNldEF0UG9zaXRpb24odCksZT1zLmluZGV4LHI9cy5vZmZzZXQsbz10aGlzLm9iamVjdHMuc2xpY2UoMCksbnVsbCE9ZT8wPT09cj8oYz1lLGw9MCk6KGk9dGhpcy5nZXRPYmplY3RBdEluZGV4KGUpLGE9aS5zcGxpdEF0T2Zmc2V0KHIpLG49YVswXSx1PWFbMV0sby5zcGxpY2UoZSwxLG4sdSksYz1lKzEsbD1uLmdldExlbmd0aCgpLXIpOihjPW8ubGVuZ3RoLGw9MCksW28sYyxsXX0saS5wcm90b3R5cGUuY29uc29saWRhdGU9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvLHI7Zm9yKGk9W10sbz10aGlzLm9iamVjdHNbMF0scj10aGlzLm9iamVjdHMuc2xpY2UoMSksdD0wLGU9ci5sZW5ndGg7ZT50O3QrKyluPXJbdF0sKFwiZnVuY3Rpb25cIj09dHlwZW9mIG8uY2FuQmVDb25zb2xpZGF0ZWRXaXRoP28uY2FuQmVDb25zb2xpZGF0ZWRXaXRoKG4pOnZvaWQgMCk/bz1vLmNvbnNvbGlkYXRlV2l0aChuKTooaS5wdXNoKG8pLG89bik7cmV0dXJuIG51bGwhPW8mJmkucHVzaChvKSxuZXcgdGhpcy5jb25zdHJ1Y3RvcihpKX0saS5wcm90b3R5cGUuY29uc29saWRhdGVGcm9tSW5kZXhUb0luZGV4PWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxyO3JldHVybiBpPXRoaXMub2JqZWN0cy5zbGljZSgwKSxyPWkuc2xpY2UodCxlKzEpLG49bmV3IHRoaXMuY29uc3RydWN0b3IocikuY29uc29saWRhdGUoKS50b0FycmF5KCksdGhpcy5zcGxpY2UuYXBwbHkodGhpcyxbdCxyLmxlbmd0aF0uY29uY2F0KG8uY2FsbChuKSkpfSxpLnByb3RvdHlwZS5maW5kSW5kZXhBbmRPZmZzZXRBdFBvc2l0aW9uPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHIscyxhO2ZvcihlPTAsYT10aGlzLm9iamVjdHMsaT1uPTAsbz1hLmxlbmd0aDtvPm47aT0rK24pe2lmKHM9YVtpXSxyPWUrcy5nZXRMZW5ndGgoKSx0Pj1lJiZyPnQpcmV0dXJue2luZGV4Omksb2Zmc2V0OnQtZX07ZT1yfXJldHVybntpbmRleDpudWxsLG9mZnNldDpudWxsfX0saS5wcm90b3R5cGUuZmluZFBvc2l0aW9uQXRJbmRleEFuZE9mZnNldD1mdW5jdGlvbih0LGUpe3ZhciBuLGksbyxyLHMsYTtmb3Iocz0wLGE9dGhpcy5vYmplY3RzLG49aT0wLG89YS5sZW5ndGg7bz5pO249KytpKWlmKHI9YVtuXSx0Pm4pcys9ci5nZXRMZW5ndGgoKTtlbHNlIGlmKG49PT10KXtzKz1lO2JyZWFrfXJldHVybiBzfSxpLnByb3RvdHlwZS5nZXRFbmRQb3NpdGlvbj1mdW5jdGlvbigpe3ZhciB0LGU7cmV0dXJuIG51bGwhPXRoaXMuZW5kUG9zaXRpb24/dGhpcy5lbmRQb3NpdGlvbjp0aGlzLmVuZFBvc2l0aW9uPWZ1bmN0aW9uKCl7dmFyIG4saSxvO2ZvcihlPTAsbz10aGlzLm9iamVjdHMsbj0wLGk9by5sZW5ndGg7aT5uO24rKyl0PW9bbl0sZSs9dC5nZXRMZW5ndGgoKTtyZXR1cm4gZX0uY2FsbCh0aGlzKX0saS5wcm90b3R5cGUudG9TdHJpbmc9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5vYmplY3RzLmpvaW4oXCJcIil9LGkucHJvdG90eXBlLnRvQXJyYXk9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5vYmplY3RzLnNsaWNlKDApfSxpLnByb3RvdHlwZS50b0pTT049ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy50b0FycmF5KCl9LGkucHJvdG90eXBlLmlzRXF1YWxUbz1mdW5jdGlvbih0KXtyZXR1cm4gaS5fX3N1cGVyX18uaXNFcXVhbFRvLmFwcGx5KHRoaXMsYXJndW1lbnRzKXx8cyh0aGlzLm9iamVjdHMsbnVsbCE9dD90Lm9iamVjdHM6dm9pZCAwKX0scz1mdW5jdGlvbih0LGUpe3ZhciBuLGksbyxyLHM7aWYobnVsbD09ZSYmKGU9W10pLHQubGVuZ3RoIT09ZS5sZW5ndGgpcmV0dXJuITE7Zm9yKHM9ITAsaT1uPTAsbz10Lmxlbmd0aDtvPm47aT0rK24pcj10W2ldLHMmJiFyLmlzRXF1YWxUbyhlW2ldKSYmKHM9ITEpO3JldHVybiBzfSxpLnByb3RvdHlwZS5jb250ZW50c0Zvckluc3BlY3Rpb249ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm57b2JqZWN0czpcIltcIitmdW5jdGlvbigpe3ZhciBlLG4saSxvO2ZvcihpPXRoaXMub2JqZWN0cyxvPVtdLGU9MCxuPWkubGVuZ3RoO24+ZTtlKyspdD1pW2VdLG8ucHVzaCh0Lmluc3BlY3QoKSk7cmV0dXJuIG99LmNhbGwodGhpcykuam9pbihcIiwgXCIpK1wiXVwifX0sYT1mdW5jdGlvbih0KXtyZXR1cm4gdFswXX0scj1mdW5jdGlvbih0KXtyZXR1cm4gdFsxXX0saX0oZS5PYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBpKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgbyBpbiBlKW4uY2FsbChlLG8pJiYodFtvXT1lW29dKTtyZXR1cm4gaS5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IGksdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sbj17fS5oYXNPd25Qcm9wZXJ0eTtlLlRleHQ9ZnVuY3Rpb24obil7ZnVuY3Rpb24gaSh0KXt2YXIgbjtudWxsPT10JiYodD1bXSksaS5fX3N1cGVyX18uY29uc3RydWN0b3IuYXBwbHkodGhpcyxhcmd1bWVudHMpLHRoaXMucGllY2VMaXN0PW5ldyBlLlNwbGl0dGFibGVMaXN0KGZ1bmN0aW9uKCl7dmFyIGUsaSxvO2ZvcihvPVtdLGU9MCxpPXQubGVuZ3RoO2k+ZTtlKyspbj10W2VdLG4uaXNFbXB0eSgpfHxvLnB1c2gobik7cmV0dXJuIG99KCkpfXJldHVybiB0KGksbiksaS50ZXh0Rm9yQXR0YWNobWVudFdpdGhBdHRyaWJ1dGVzPWZ1bmN0aW9uKHQsbil7dmFyIGk7cmV0dXJuIGk9bmV3IGUuQXR0YWNobWVudFBpZWNlKHQsbiksbmV3IHRoaXMoW2ldKX0saS50ZXh0Rm9yU3RyaW5nV2l0aEF0dHJpYnV0ZXM9ZnVuY3Rpb24odCxuKXt2YXIgaTtyZXR1cm4gaT1uZXcgZS5TdHJpbmdQaWVjZSh0LG4pLG5ldyB0aGlzKFtpXSl9LGkuZnJvbUpTT049ZnVuY3Rpb24odCl7dmFyIG4saTtyZXR1cm4gaT1mdW5jdGlvbigpe3ZhciBpLG8scjtmb3Iocj1bXSxpPTAsbz10Lmxlbmd0aDtvPmk7aSsrKW49dFtpXSxyLnB1c2goZS5QaWVjZS5mcm9tSlNPTihuKSk7cmV0dXJuIHJ9KCksbmV3IHRoaXMoaSl9LGkucHJvdG90eXBlLmNvcHk9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb3B5V2l0aFBpZWNlTGlzdCh0aGlzLnBpZWNlTGlzdCl9LGkucHJvdG90eXBlLmNvcHlXaXRoUGllY2VMaXN0PWZ1bmN0aW9uKHQpe3JldHVybiBuZXcgdGhpcy5jb25zdHJ1Y3Rvcih0LmNvbnNvbGlkYXRlKCkudG9BcnJheSgpKX0saS5wcm90b3R5cGUuY29weVVzaW5nT2JqZWN0TWFwPWZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG49ZnVuY3Rpb24oKXt2YXIgbixpLG8scixzO2ZvcihvPXRoaXMuZ2V0UGllY2VzKCkscz1bXSxuPTAsaT1vLmxlbmd0aDtpPm47bisrKWU9b1tuXSxzLnB1c2gobnVsbCE9KHI9dC5maW5kKGUpKT9yOmUpO3JldHVybiBzfS5jYWxsKHRoaXMpLG5ldyB0aGlzLmNvbnN0cnVjdG9yKG4pfSxpLnByb3RvdHlwZS5hcHBlbmRUZXh0PWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmluc2VydFRleHRBdFBvc2l0aW9uKHQsdGhpcy5nZXRMZW5ndGgoKSl9LGkucHJvdG90eXBlLmluc2VydFRleHRBdFBvc2l0aW9uPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIHRoaXMuY29weVdpdGhQaWVjZUxpc3QodGhpcy5waWVjZUxpc3QuaW5zZXJ0U3BsaXR0YWJsZUxpc3RBdFBvc2l0aW9uKHQucGllY2VMaXN0LGUpKVxufSxpLnByb3RvdHlwZS5yZW1vdmVUZXh0QXRSYW5nZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5jb3B5V2l0aFBpZWNlTGlzdCh0aGlzLnBpZWNlTGlzdC5yZW1vdmVPYmplY3RzSW5SYW5nZSh0KSl9LGkucHJvdG90eXBlLnJlcGxhY2VUZXh0QXRSYW5nZT1mdW5jdGlvbih0LGUpe3JldHVybiB0aGlzLnJlbW92ZVRleHRBdFJhbmdlKGUpLmluc2VydFRleHRBdFBvc2l0aW9uKHQsZVswXSl9LGkucHJvdG90eXBlLm1vdmVUZXh0RnJvbVJhbmdlVG9Qb3NpdGlvbj1mdW5jdGlvbih0LGUpe3ZhciBuLGk7aWYoISh0WzBdPD1lJiZlPD10WzFdKSlyZXR1cm4gaT10aGlzLmdldFRleHRBdFJhbmdlKHQpLG49aS5nZXRMZW5ndGgoKSx0WzBdPGUmJihlLT1uKSx0aGlzLnJlbW92ZVRleHRBdFJhbmdlKHQpLmluc2VydFRleHRBdFBvc2l0aW9uKGksZSl9LGkucHJvdG90eXBlLmFkZEF0dHJpYnV0ZUF0UmFuZ2U9ZnVuY3Rpb24odCxlLG4pe3ZhciBpO3JldHVybiBpPXt9LGlbdF09ZSx0aGlzLmFkZEF0dHJpYnV0ZXNBdFJhbmdlKGksbil9LGkucHJvdG90eXBlLmFkZEF0dHJpYnV0ZXNBdFJhbmdlPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIHRoaXMuY29weVdpdGhQaWVjZUxpc3QodGhpcy5waWVjZUxpc3QudHJhbnNmb3JtT2JqZWN0c0luUmFuZ2UoZSxmdW5jdGlvbihlKXtyZXR1cm4gZS5jb3B5V2l0aEFkZGl0aW9uYWxBdHRyaWJ1dGVzKHQpfSkpfSxpLnByb3RvdHlwZS5yZW1vdmVBdHRyaWJ1dGVBdFJhbmdlPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIHRoaXMuY29weVdpdGhQaWVjZUxpc3QodGhpcy5waWVjZUxpc3QudHJhbnNmb3JtT2JqZWN0c0luUmFuZ2UoZSxmdW5jdGlvbihlKXtyZXR1cm4gZS5jb3B5V2l0aG91dEF0dHJpYnV0ZSh0KX0pKX0saS5wcm90b3R5cGUuc2V0QXR0cmlidXRlc0F0UmFuZ2U9ZnVuY3Rpb24odCxlKXtyZXR1cm4gdGhpcy5jb3B5V2l0aFBpZWNlTGlzdCh0aGlzLnBpZWNlTGlzdC50cmFuc2Zvcm1PYmplY3RzSW5SYW5nZShlLGZ1bmN0aW9uKGUpe3JldHVybiBlLmNvcHlXaXRoQXR0cmlidXRlcyh0KX0pKX0saS5wcm90b3R5cGUuZ2V0QXR0cmlidXRlc0F0UG9zaXRpb249ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gbnVsbCE9KGU9bnVsbCE9KG49dGhpcy5waWVjZUxpc3QuZ2V0T2JqZWN0QXRQb3NpdGlvbih0KSk/bi5nZXRBdHRyaWJ1dGVzKCk6dm9pZCAwKT9lOnt9fSxpLnByb3RvdHlwZS5nZXRDb21tb25BdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7dmFyIHQsbjtyZXR1cm4gdD1mdW5jdGlvbigpe3ZhciB0LGUsaSxvO2ZvcihpPXRoaXMucGllY2VMaXN0LnRvQXJyYXkoKSxvPVtdLHQ9MCxlPWkubGVuZ3RoO2U+dDt0Kyspbj1pW3RdLG8ucHVzaChuLmdldEF0dHJpYnV0ZXMoKSk7cmV0dXJuIG99LmNhbGwodGhpcyksZS5IYXNoLmZyb21Db21tb25BdHRyaWJ1dGVzT2ZPYmplY3RzKHQpLnRvT2JqZWN0KCl9LGkucHJvdG90eXBlLmdldENvbW1vbkF0dHJpYnV0ZXNBdFJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBudWxsIT0oZT10aGlzLmdldFRleHRBdFJhbmdlKHQpLmdldENvbW1vbkF0dHJpYnV0ZXMoKSk/ZTp7fX0saS5wcm90b3R5cGUuZ2V0RXhwYW5kZWRSYW5nZUZvckF0dHJpYnV0ZUF0T2Zmc2V0PWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvO2ZvcihuPW89ZSxpPXRoaXMuZ2V0TGVuZ3RoKCk7bj4wJiZ0aGlzLmdldENvbW1vbkF0dHJpYnV0ZXNBdFJhbmdlKFtuLTEsb10pW3RdOyluLS07Zm9yKDtpPm8mJnRoaXMuZ2V0Q29tbW9uQXR0cmlidXRlc0F0UmFuZ2UoW2UsbysxXSlbdF07KW8rKztyZXR1cm5bbixvXX0saS5wcm90b3R5cGUuZ2V0VGV4dEF0UmFuZ2U9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29weVdpdGhQaWVjZUxpc3QodGhpcy5waWVjZUxpc3QuZ2V0U3BsaXR0YWJsZUxpc3RJblJhbmdlKHQpKX0saS5wcm90b3R5cGUuZ2V0U3RyaW5nQXRSYW5nZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5waWVjZUxpc3QuZ2V0U3BsaXR0YWJsZUxpc3RJblJhbmdlKHQpLnRvU3RyaW5nKCl9LGkucHJvdG90eXBlLmdldFN0cmluZ0F0UG9zaXRpb249ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuZ2V0U3RyaW5nQXRSYW5nZShbdCx0KzFdKX0saS5wcm90b3R5cGUuc3RhcnRzV2l0aFN0cmluZz1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5nZXRTdHJpbmdBdFJhbmdlKFswLHQubGVuZ3RoXSk9PT10fSxpLnByb3RvdHlwZS5lbmRzV2l0aFN0cmluZz1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT10aGlzLmdldExlbmd0aCgpLHRoaXMuZ2V0U3RyaW5nQXRSYW5nZShbZS10Lmxlbmd0aCxlXSk9PT10fSxpLnByb3RvdHlwZS5nZXRBdHRhY2htZW50UGllY2VzPWZ1bmN0aW9uKCl7dmFyIHQsZSxuLGksbztmb3IoaT10aGlzLnBpZWNlTGlzdC50b0FycmF5KCksbz1bXSx0PTAsZT1pLmxlbmd0aDtlPnQ7dCsrKW49aVt0XSxudWxsIT1uLmF0dGFjaG1lbnQmJm8ucHVzaChuKTtyZXR1cm4gb30saS5wcm90b3R5cGUuZ2V0QXR0YWNobWVudHM9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvO2ZvcihpPXRoaXMuZ2V0QXR0YWNobWVudFBpZWNlcygpLG89W10sdD0wLGU9aS5sZW5ndGg7ZT50O3QrKyluPWlbdF0sby5wdXNoKG4uYXR0YWNobWVudCk7cmV0dXJuIG99LGkucHJvdG90eXBlLmdldEF0dGFjaG1lbnRBbmRQb3NpdGlvbkJ5SWQ9ZnVuY3Rpb24odCl7dmFyIGUsbixpLG8scixzO2ZvcihvPTAscj10aGlzLnBpZWNlTGlzdC50b0FycmF5KCksZT0wLG49ci5sZW5ndGg7bj5lO2UrKyl7aWYoaT1yW2VdLChudWxsIT0ocz1pLmF0dGFjaG1lbnQpP3MuaWQ6dm9pZCAwKT09PXQpcmV0dXJue2F0dGFjaG1lbnQ6aS5hdHRhY2htZW50LHBvc2l0aW9uOm99O28rPWkubGVuZ3RofXJldHVybnthdHRhY2htZW50Om51bGwscG9zaXRpb246bnVsbH19LGkucHJvdG90eXBlLmdldEF0dGFjaG1lbnRCeUlkPWZ1bmN0aW9uKHQpe3ZhciBlLG4saTtyZXR1cm4gaT10aGlzLmdldEF0dGFjaG1lbnRBbmRQb3NpdGlvbkJ5SWQodCksZT1pLmF0dGFjaG1lbnQsbj1pLnBvc2l0aW9uLGV9LGkucHJvdG90eXBlLmdldFJhbmdlT2ZBdHRhY2htZW50PWZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG49dGhpcy5nZXRBdHRhY2htZW50QW5kUG9zaXRpb25CeUlkKHQuaWQpLHQ9bi5hdHRhY2htZW50LGU9bi5wb3NpdGlvbixudWxsIT10P1tlLGUrMV06dm9pZCAwfSxpLnByb3RvdHlwZS51cGRhdGVBdHRyaWJ1dGVzRm9yQXR0YWNobWVudD1mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybihuPXRoaXMuZ2V0UmFuZ2VPZkF0dGFjaG1lbnQoZSkpP3RoaXMuYWRkQXR0cmlidXRlc0F0UmFuZ2UodCxuKTp0aGlzfSxpLnByb3RvdHlwZS5nZXRMZW5ndGg9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5waWVjZUxpc3QuZ2V0RW5kUG9zaXRpb24oKX0saS5wcm90b3R5cGUuaXNFbXB0eT1mdW5jdGlvbigpe3JldHVybiAwPT09dGhpcy5nZXRMZW5ndGgoKX0saS5wcm90b3R5cGUuaXNFcXVhbFRvPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBpLl9fc3VwZXJfXy5pc0VxdWFsVG8uYXBwbHkodGhpcyxhcmd1bWVudHMpfHwobnVsbCE9dCYmbnVsbCE9KGU9dC5waWVjZUxpc3QpP2UuaXNFcXVhbFRvKHRoaXMucGllY2VMaXN0KTp2b2lkIDApfSxpLnByb3RvdHlwZS5pc0Jsb2NrQnJlYWs9ZnVuY3Rpb24oKXtyZXR1cm4gMT09PXRoaXMuZ2V0TGVuZ3RoKCkmJnRoaXMucGllY2VMaXN0LmdldE9iamVjdEF0SW5kZXgoMCkuaXNCbG9ja0JyZWFrKCl9LGkucHJvdG90eXBlLmVhY2hQaWVjZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5waWVjZUxpc3QuZWFjaE9iamVjdCh0KX0saS5wcm90b3R5cGUuZ2V0UGllY2VzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMucGllY2VMaXN0LnRvQXJyYXkoKX0saS5wcm90b3R5cGUuZ2V0UGllY2VBdFBvc2l0aW9uPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnBpZWNlTGlzdC5nZXRPYmplY3RBdFBvc2l0aW9uKHQpfSxpLnByb3RvdHlwZS5jb250ZW50c0Zvckluc3BlY3Rpb249ZnVuY3Rpb24oKXtyZXR1cm57cGllY2VMaXN0OnRoaXMucGllY2VMaXN0Lmluc3BlY3QoKX19LGkucHJvdG90eXBlLnRvU2VyaWFsaXphYmxlVGV4dD1mdW5jdGlvbigpe3ZhciB0O3JldHVybiB0PXRoaXMucGllY2VMaXN0LnNlbGVjdFNwbGl0dGFibGVMaXN0KGZ1bmN0aW9uKHQpe3JldHVybiB0LmlzU2VyaWFsaXphYmxlKCl9KSx0aGlzLmNvcHlXaXRoUGllY2VMaXN0KHQpfSxpLnByb3RvdHlwZS50b1N0cmluZz1mdW5jdGlvbigpe3JldHVybiB0aGlzLnBpZWNlTGlzdC50b1N0cmluZygpfSxpLnByb3RvdHlwZS50b0pTT049ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5waWVjZUxpc3QudG9KU09OKCl9LGkucHJvdG90eXBlLnRvQ29uc29sZT1mdW5jdGlvbigpe3ZhciB0O3JldHVybiBKU09OLnN0cmluZ2lmeShmdW5jdGlvbigpe3ZhciBlLG4saSxvO2ZvcihpPXRoaXMucGllY2VMaXN0LnRvQXJyYXkoKSxvPVtdLGU9MCxuPWkubGVuZ3RoO24+ZTtlKyspdD1pW2VdLG8ucHVzaChKU09OLnBhcnNlKHQudG9Db25zb2xlKCkpKTtyZXR1cm4gb30uY2FsbCh0aGlzKSl9LGkucHJvdG90eXBlLmdldERpcmVjdGlvbj1mdW5jdGlvbigpe3JldHVybiBlLmdldERpcmVjdGlvbih0aGlzLnRvU3RyaW5nKCkpfSxpLnByb3RvdHlwZS5pc1JUTD1mdW5jdGlvbigpe3JldHVyblwicnRsXCI9PT10aGlzLmdldERpcmVjdGlvbigpfSxpfShlLk9iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbyxyLHM9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBuKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgaSBpbiBlKWEuY2FsbChlLGkpJiYodFtpXT1lW2ldKTtyZXR1cm4gbi5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IG4sdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sYT17fS5oYXNPd25Qcm9wZXJ0eSx1PVtdLmluZGV4T2Z8fGZ1bmN0aW9uKHQpe2Zvcih2YXIgZT0wLG49dGhpcy5sZW5ndGg7bj5lO2UrKylpZihlIGluIHRoaXMmJnRoaXNbZV09PT10KXJldHVybiBlO3JldHVybi0xfSxjPVtdLnNsaWNlO3Q9ZS5hcnJheXNBcmVFcXVhbCxyPWUuc3BsaWNlQXJyYXksaT1lLmdldEJsb2NrQ29uZmlnLG49ZS5nZXRCbG9ja0F0dHJpYnV0ZU5hbWVzLG89ZS5nZXRMaXN0QXR0cmlidXRlTmFtZXMsZS5CbG9jaz1mdW5jdGlvbihuKXtmdW5jdGlvbiBhKHQsbil7bnVsbD09dCYmKHQ9bmV3IGUuVGV4dCksbnVsbD09biYmKG49W10pLGEuX19zdXBlcl9fLmNvbnN0cnVjdG9yLmFwcGx5KHRoaXMsYXJndW1lbnRzKSx0aGlzLnRleHQ9aCh0KSx0aGlzLmF0dHJpYnV0ZXM9bn12YXIgbCxoLHAsZCxmLGcsbSx2LHk7cmV0dXJuIHMoYSxuKSxhLmZyb21KU09OPWZ1bmN0aW9uKHQpe3ZhciBuO3JldHVybiBuPWUuVGV4dC5mcm9tSlNPTih0LnRleHQpLG5ldyB0aGlzKG4sdC5hdHRyaWJ1dGVzKX0sYS5wcm90b3R5cGUuaXNFbXB0eT1mdW5jdGlvbigpe3JldHVybiB0aGlzLnRleHQuaXNCbG9ja0JyZWFrKCl9LGEucHJvdG90eXBlLmlzRXF1YWxUbz1mdW5jdGlvbihlKXtyZXR1cm4gYS5fX3N1cGVyX18uaXNFcXVhbFRvLmFwcGx5KHRoaXMsYXJndW1lbnRzKXx8dGhpcy50ZXh0LmlzRXF1YWxUbyhudWxsIT1lP2UudGV4dDp2b2lkIDApJiZ0KHRoaXMuYXR0cmlidXRlcyxudWxsIT1lP2UuYXR0cmlidXRlczp2b2lkIDApfSxhLnByb3RvdHlwZS5jb3B5V2l0aFRleHQ9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyB0aGlzLmNvbnN0cnVjdG9yKHQsdGhpcy5hdHRyaWJ1dGVzKX0sYS5wcm90b3R5cGUuY29weVdpdGhvdXRUZXh0PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29weVdpdGhUZXh0KG51bGwpfSxhLnByb3RvdHlwZS5jb3B5V2l0aEF0dHJpYnV0ZXM9ZnVuY3Rpb24odCl7cmV0dXJuIG5ldyB0aGlzLmNvbnN0cnVjdG9yKHRoaXMudGV4dCx0KX0sYS5wcm90b3R5cGUuY29weVdpdGhvdXRBdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29weVdpdGhBdHRyaWJ1dGVzKG51bGwpfSxhLnByb3RvdHlwZS5jb3B5VXNpbmdPYmplY3RNYXA9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHRoaXMuY29weVdpdGhUZXh0KChlPXQuZmluZCh0aGlzLnRleHQpKT9lOnRoaXMudGV4dC5jb3B5VXNpbmdPYmplY3RNYXAodCkpfSxhLnByb3RvdHlwZS5hZGRBdHRyaWJ1dGU9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGU9dGhpcy5hdHRyaWJ1dGVzLmNvbmNhdChkKHQpKSx0aGlzLmNvcHlXaXRoQXR0cmlidXRlcyhlKX0sYS5wcm90b3R5cGUucmVtb3ZlQXR0cmlidXRlPWZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG49aSh0KS5saXN0QXR0cmlidXRlLGU9ZyhnKHRoaXMuYXR0cmlidXRlcyx0KSxuKSx0aGlzLmNvcHlXaXRoQXR0cmlidXRlcyhlKX0sYS5wcm90b3R5cGUucmVtb3ZlTGFzdEF0dHJpYnV0ZT1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlbW92ZUF0dHJpYnV0ZSh0aGlzLmdldExhc3RBdHRyaWJ1dGUoKSl9LGEucHJvdG90eXBlLmdldExhc3RBdHRyaWJ1dGU9ZnVuY3Rpb24oKXtyZXR1cm4gZih0aGlzLmF0dHJpYnV0ZXMpfSxhLnByb3RvdHlwZS5nZXRBdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0cmlidXRlcy5zbGljZSgwKX0sYS5wcm90b3R5cGUuZ2V0QXR0cmlidXRlTGV2ZWw9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzLmxlbmd0aH0sYS5wcm90b3R5cGUuZ2V0QXR0cmlidXRlQXRMZXZlbD1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5hdHRyaWJ1dGVzW3QtMV19LGEucHJvdG90eXBlLmhhc0F0dHJpYnV0ZT1mdW5jdGlvbih0KXtyZXR1cm4gdS5jYWxsKHRoaXMuYXR0cmlidXRlcyx0KT49MH0sYS5wcm90b3R5cGUuaGFzQXR0cmlidXRlcz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmdldEF0dHJpYnV0ZUxldmVsKCk+MH0sYS5wcm90b3R5cGUuZ2V0TGFzdE5lc3RhYmxlQXR0cmlidXRlPWZ1bmN0aW9uKCl7cmV0dXJuIGYodGhpcy5nZXROZXN0YWJsZUF0dHJpYnV0ZXMoKSl9LGEucHJvdG90eXBlLmdldE5lc3RhYmxlQXR0cmlidXRlcz1mdW5jdGlvbigpe3ZhciB0LGUsbixvLHI7Zm9yKG89dGhpcy5hdHRyaWJ1dGVzLHI9W10sZT0wLG49by5sZW5ndGg7bj5lO2UrKyl0PW9bZV0saSh0KS5uZXN0YWJsZSYmci5wdXNoKHQpO3JldHVybiByfSxhLnByb3RvdHlwZS5nZXROZXN0aW5nTGV2ZWw9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5nZXROZXN0YWJsZUF0dHJpYnV0ZXMoKS5sZW5ndGh9LGEucHJvdG90eXBlLmRlY3JlYXNlTmVzdGluZ0xldmVsPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKHQ9dGhpcy5nZXRMYXN0TmVzdGFibGVBdHRyaWJ1dGUoKSk/dGhpcy5yZW1vdmVBdHRyaWJ1dGUodCk6dGhpc30sYS5wcm90b3R5cGUuaW5jcmVhc2VOZXN0aW5nTGV2ZWw9ZnVuY3Rpb24oKXt2YXIgdCxlLG47cmV0dXJuKHQ9dGhpcy5nZXRMYXN0TmVzdGFibGVBdHRyaWJ1dGUoKSk/KG49dGhpcy5hdHRyaWJ1dGVzLmxhc3RJbmRleE9mKHQpLGU9ci5hcHBseShudWxsLFt0aGlzLmF0dHJpYnV0ZXMsbisxLDBdLmNvbmNhdChjLmNhbGwoZCh0KSkpKSx0aGlzLmNvcHlXaXRoQXR0cmlidXRlcyhlKSk6dGhpc30sYS5wcm90b3R5cGUuZ2V0TGlzdEl0ZW1BdHRyaWJ1dGVzPWZ1bmN0aW9uKCl7dmFyIHQsZSxuLG8scjtmb3Iobz10aGlzLmF0dHJpYnV0ZXMscj1bXSxlPTAsbj1vLmxlbmd0aDtuPmU7ZSsrKXQ9b1tlXSxpKHQpLmxpc3RBdHRyaWJ1dGUmJnIucHVzaCh0KTtyZXR1cm4gcn0sYS5wcm90b3R5cGUuaXNMaXN0SXRlbT1mdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD1pKHRoaXMuZ2V0TGFzdEF0dHJpYnV0ZSgpKSk/dC5saXN0QXR0cmlidXRlOnZvaWQgMH0sYS5wcm90b3R5cGUuaXNUZXJtaW5hbEJsb2NrPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PWkodGhpcy5nZXRMYXN0QXR0cmlidXRlKCkpKT90LnRlcm1pbmFsOnZvaWQgMH0sYS5wcm90b3R5cGUuYnJlYWtzT25SZXR1cm49ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9aSh0aGlzLmdldExhc3RBdHRyaWJ1dGUoKSkpP3QuYnJlYWtPblJldHVybjp2b2lkIDB9LGEucHJvdG90eXBlLmZpbmRMaW5lQnJlYWtJbkRpcmVjdGlvbkZyb21Qb3NpdGlvbj1mdW5jdGlvbih0LGUpe3ZhciBuLGk7cmV0dXJuIGk9dGhpcy50b1N0cmluZygpLG49ZnVuY3Rpb24oKXtzd2l0Y2godCl7Y2FzZVwiZm9yd2FyZFwiOnJldHVybiBpLmluZGV4T2YoXCJcXG5cIixlKTtjYXNlXCJiYWNrd2FyZFwiOnJldHVybiBpLnNsaWNlKDAsZSkubGFzdEluZGV4T2YoXCJcXG5cIil9fSgpLC0xIT09bj9uOnZvaWQgMH0sYS5wcm90b3R5cGUuY29udGVudHNGb3JJbnNwZWN0aW9uPWZ1bmN0aW9uKCl7cmV0dXJue3RleHQ6dGhpcy50ZXh0Lmluc3BlY3QoKSxhdHRyaWJ1dGVzOnRoaXMuYXR0cmlidXRlc319LGEucHJvdG90eXBlLnRvU3RyaW5nPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudGV4dC50b1N0cmluZygpfSxhLnByb3RvdHlwZS50b0pTT049ZnVuY3Rpb24oKXtyZXR1cm57dGV4dDp0aGlzLnRleHQsYXR0cmlidXRlczp0aGlzLmF0dHJpYnV0ZXN9fSxhLnByb3RvdHlwZS5nZXREaXJlY3Rpb249ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy50ZXh0LmdldERpcmVjdGlvbigpfSxhLnByb3RvdHlwZS5pc1JUTD1mdW5jdGlvbigpe3JldHVybiB0aGlzLnRleHQuaXNSVEwoKX0sYS5wcm90b3R5cGUuZ2V0TGVuZ3RoPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudGV4dC5nZXRMZW5ndGgoKX0sYS5wcm90b3R5cGUuY2FuQmVDb25zb2xpZGF0ZWRXaXRoPWZ1bmN0aW9uKHQpe3JldHVybiF0aGlzLmhhc0F0dHJpYnV0ZXMoKSYmIXQuaGFzQXR0cmlidXRlcygpJiZ0aGlzLmdldERpcmVjdGlvbigpPT09dC5nZXREaXJlY3Rpb24oKX0sYS5wcm90b3R5cGUuY29uc29saWRhdGVXaXRoPWZ1bmN0aW9uKHQpe3ZhciBuLGk7cmV0dXJuIG49ZS5UZXh0LnRleHRGb3JTdHJpbmdXaXRoQXR0cmlidXRlcyhcIlxcblwiKSxpPXRoaXMuZ2V0VGV4dFdpdGhvdXRCbG9ja0JyZWFrKCkuYXBwZW5kVGV4dChuKSx0aGlzLmNvcHlXaXRoVGV4dChpLmFwcGVuZFRleHQodC50ZXh0KSl9LGEucHJvdG90eXBlLnNwbGl0QXRPZmZzZXQ9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gMD09PXQ/KGU9bnVsbCxuPXRoaXMpOnQ9PT10aGlzLmdldExlbmd0aCgpPyhlPXRoaXMsbj1udWxsKTooZT10aGlzLmNvcHlXaXRoVGV4dCh0aGlzLnRleHQuZ2V0VGV4dEF0UmFuZ2UoWzAsdF0pKSxuPXRoaXMuY29weVdpdGhUZXh0KHRoaXMudGV4dC5nZXRUZXh0QXRSYW5nZShbdCx0aGlzLmdldExlbmd0aCgpXSkpKSxbZSxuXX0sYS5wcm90b3R5cGUuZ2V0QmxvY2tCcmVha1Bvc2l0aW9uPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudGV4dC5nZXRMZW5ndGgoKS0xfSxhLnByb3RvdHlwZS5nZXRUZXh0V2l0aG91dEJsb2NrQnJlYWs9ZnVuY3Rpb24oKXtyZXR1cm4gbSh0aGlzLnRleHQpP3RoaXMudGV4dC5nZXRUZXh0QXRSYW5nZShbMCx0aGlzLmdldEJsb2NrQnJlYWtQb3NpdGlvbigpXSk6dGhpcy50ZXh0LmNvcHkoKX0sYS5wcm90b3R5cGUuY2FuQmVHcm91cGVkPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmF0dHJpYnV0ZXNbdF19LGEucHJvdG90eXBlLmNhbkJlR3JvdXBlZFdpdGg9ZnVuY3Rpb24odCxlKXt2YXIgbixyLHMsYTtyZXR1cm4gcz10LmdldEF0dHJpYnV0ZXMoKSxyPXNbZV0sbj10aGlzLmF0dHJpYnV0ZXNbZV0sIShuIT09cnx8aShuKS5ncm91cD09PSExJiYoYT1zW2UrMV0sdS5jYWxsKG8oKSxhKTwwKXx8dGhpcy5nZXREaXJlY3Rpb24oKSE9PXQuZ2V0RGlyZWN0aW9uKCkmJiF0LmlzRW1wdHkoKSl9LGg9ZnVuY3Rpb24odCl7cmV0dXJuIHQ9eSh0KSx0PWwodCl9LHk9ZnVuY3Rpb24odCl7dmFyIG4saSxvLHIscyxhO3JldHVybiByPSExLGE9dC5nZXRQaWVjZXMoKSxpPTI8PWEubGVuZ3RoP2MuY2FsbChhLDAsbj1hLmxlbmd0aC0xKToobj0wLFtdKSxvPWFbbisrXSxudWxsPT1vP3Q6KGk9ZnVuY3Rpb24oKXt2YXIgdCxlLG47Zm9yKG49W10sdD0wLGU9aS5sZW5ndGg7ZT50O3QrKylzPWlbdF0scy5pc0Jsb2NrQnJlYWsoKT8ocj0hMCxuLnB1c2godihzKSkpOm4ucHVzaChzKTtyZXR1cm4gbn0oKSxyP25ldyBlLlRleHQoYy5jYWxsKGkpLmNvbmNhdChbb10pKTp0KX0scD1lLlRleHQudGV4dEZvclN0cmluZ1dpdGhBdHRyaWJ1dGVzKFwiXFxuXCIse2Jsb2NrQnJlYWs6ITB9KSxsPWZ1bmN0aW9uKHQpe3JldHVybiBtKHQpP3Q6dC5hcHBlbmRUZXh0KHApfSxtPWZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG49dC5nZXRMZW5ndGgoKSwwPT09bj8hMTooZT10LmdldFRleHRBdFJhbmdlKFtuLTEsbl0pLGUuaXNCbG9ja0JyZWFrKCkpfSx2PWZ1bmN0aW9uKHQpe3JldHVybiB0LmNvcHlXaXRob3V0QXR0cmlidXRlKFwiYmxvY2tCcmVha1wiKX0sZD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT1pKHQpLmxpc3RBdHRyaWJ1dGUsbnVsbCE9ZT9bZSx0XTpbdF19LGY9ZnVuY3Rpb24odCl7cmV0dXJuIHQuc2xpY2UoLTEpWzBdfSxnPWZ1bmN0aW9uKHQsZSl7dmFyIG47cmV0dXJuIG49dC5sYXN0SW5kZXhPZihlKSwtMT09PW4/dDpyKHQsbiwxKX0sYX0oZS5PYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG89ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBuKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgaSBpbiBlKXIuY2FsbChlLGkpJiYodFtpXT1lW2ldKTtyZXR1cm4gbi5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IG4sdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0scj17fS5oYXNPd25Qcm9wZXJ0eSxzPVtdLmluZGV4T2Z8fGZ1bmN0aW9uKHQpe2Zvcih2YXIgZT0wLG49dGhpcy5sZW5ndGg7bj5lO2UrKylpZihlIGluIHRoaXMmJnRoaXNbZV09PT10KXJldHVybiBlO3JldHVybi0xfSxhPVtdLnNsaWNlO249ZS50YWdOYW1lLGk9ZS53YWxrVHJlZSx0PWUubm9kZUlzQXR0YWNobWVudEVsZW1lbnQsZS5IVE1MU2FuaXRpemVyPWZ1bmN0aW9uKHIpe2Z1bmN0aW9uIHUodCxlKXt2YXIgbjtuPW51bGwhPWU/ZTp7fSx0aGlzLmFsbG93ZWRBdHRyaWJ1dGVzPW4uYWxsb3dlZEF0dHJpYnV0ZXMsdGhpcy5mb3JiaWRkZW5Qcm90b2NvbHM9bi5mb3JiaWRkZW5Qcm90b2NvbHMsdGhpcy5mb3JiaWRkZW5FbGVtZW50cz1uLmZvcmJpZGRlbkVsZW1lbnRzLG51bGw9PXRoaXMuYWxsb3dlZEF0dHJpYnV0ZXMmJih0aGlzLmFsbG93ZWRBdHRyaWJ1dGVzPWMpLG51bGw9PXRoaXMuZm9yYmlkZGVuUHJvdG9jb2xzJiYodGhpcy5mb3JiaWRkZW5Qcm90b2NvbHM9aCksbnVsbD09dGhpcy5mb3JiaWRkZW5FbGVtZW50cyYmKHRoaXMuZm9yYmlkZGVuRWxlbWVudHM9bCksdGhpcy5ib2R5PXAodCl9dmFyIGMsbCxoLHA7cmV0dXJuIG8odSxyKSxjPVwic3R5bGUgaHJlZiBzcmMgd2lkdGggaGVpZ2h0IGNsYXNzXCIuc3BsaXQoXCIgXCIpLGg9XCJqYXZhc2NyaXB0OlwiLnNwbGl0KFwiIFwiKSxsPVwic2NyaXB0IGlmcmFtZVwiLnNwbGl0KFwiIFwiKSx1LnNhbml0aXplPWZ1bmN0aW9uKHQsZSl7dmFyIG47cmV0dXJuIG49bmV3IHRoaXModCxlKSxuLnNhbml0aXplKCksbn0sdS5wcm90b3R5cGUuc2FuaXRpemU9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zYW5pdGl6ZUVsZW1lbnRzKCksdGhpcy5ub3JtYWxpemVMaXN0RWxlbWVudE5lc3RpbmcoKX0sdS5wcm90b3R5cGUuZ2V0SFRNTD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmJvZHkuaW5uZXJIVE1MfSx1LnByb3RvdHlwZS5nZXRCb2R5PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYm9keX0sdS5wcm90b3R5cGUuc2FuaXRpemVFbGVtZW50cz1mdW5jdGlvbigpe3ZhciB0LG4sbyxyLHM7Zm9yKHM9aSh0aGlzLmJvZHkpLHI9W107cy5uZXh0Tm9kZSgpOylzd2l0Y2gobz1zLmN1cnJlbnROb2RlLG8ubm9kZVR5cGUpe2Nhc2UgTm9kZS5FTEVNRU5UX05PREU6dGhpcy5lbGVtZW50SXNSZW1vdmFibGUobyk/ci5wdXNoKG8pOnRoaXMuc2FuaXRpemVFbGVtZW50KG8pO2JyZWFrO2Nhc2UgTm9kZS5DT01NRU5UX05PREU6ci5wdXNoKG8pfWZvcih0PTAsbj1yLmxlbmd0aDtuPnQ7dCsrKW89clt0XSxlLnJlbW92ZU5vZGUobyk7cmV0dXJuIHRoaXMuYm9keX0sdS5wcm90b3R5cGUuc2FuaXRpemVFbGVtZW50PWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHI7Zm9yKHQuaGFzQXR0cmlidXRlKFwiaHJlZlwiKSYmKG89dC5wcm90b2NvbCxzLmNhbGwodGhpcy5mb3JiaWRkZW5Qcm90b2NvbHMsbyk+PTAmJnQucmVtb3ZlQXR0cmlidXRlKFwiaHJlZlwiKSkscj1hLmNhbGwodC5hdHRyaWJ1dGVzKSxlPTAsbj1yLmxlbmd0aDtuPmU7ZSsrKWk9cltlXS5uYW1lLHMuY2FsbCh0aGlzLmFsbG93ZWRBdHRyaWJ1dGVzLGkpPj0wfHwwPT09aS5pbmRleE9mKFwiZGF0YS10cml4XCIpfHx0LnJlbW92ZUF0dHJpYnV0ZShpKTtyZXR1cm4gdH0sdS5wcm90b3R5cGUubm9ybWFsaXplTGlzdEVsZW1lbnROZXN0aW5nPWZ1bmN0aW9uKCl7dmFyIHQsZSxpLG8scjtmb3Iocj1hLmNhbGwodGhpcy5ib2R5LnF1ZXJ5U2VsZWN0b3JBbGwoXCJ1bCxvbFwiKSksdD0wLGU9ci5sZW5ndGg7ZT50O3QrKylpPXJbdF0sKG89aS5wcmV2aW91c0VsZW1lbnRTaWJsaW5nKSYmXCJsaVwiPT09bihvKSYmby5hcHBlbmRDaGlsZChpKTtyZXR1cm4gdGhpcy5ib2R5fSx1LnByb3RvdHlwZS5lbGVtZW50SXNSZW1vdmFibGU9ZnVuY3Rpb24odCl7cmV0dXJuKG51bGwhPXQ/dC5ub2RlVHlwZTp2b2lkIDApPT09Tm9kZS5FTEVNRU5UX05PREU/dGhpcy5lbGVtZW50SXNGb3JiaWRkZW4odCl8fHRoaXMuZWxlbWVudElzbnRTZXJpYWxpemFibGUodCk6dm9pZCAwfSx1LnByb3RvdHlwZS5lbGVtZW50SXNGb3JiaWRkZW49ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGU9bih0KSxzLmNhbGwodGhpcy5mb3JiaWRkZW5FbGVtZW50cyxlKT49MH0sdS5wcm90b3R5cGUuZWxlbWVudElzbnRTZXJpYWxpemFibGU9ZnVuY3Rpb24oZSl7cmV0dXJuXCJmYWxzZVwiPT09ZS5nZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtc2VyaWFsaXplXCIpJiYhdChlKX0scD1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxyO2ZvcihudWxsPT10JiYodD1cIlwiKSx0PXQucmVwbGFjZSgvPFxcL2h0bWxbXj5dKj5bXl0qJC9pLFwiPC9odG1sPlwiKSxlPWRvY3VtZW50LmltcGxlbWVudGF0aW9uLmNyZWF0ZUhUTUxEb2N1bWVudChcIlwiKSxlLmRvY3VtZW50RWxlbWVudC5pbm5lckhUTUw9dCxyPWUuaGVhZC5xdWVyeVNlbGVjdG9yQWxsKFwic3R5bGVcIiksaT0wLG89ci5sZW5ndGg7bz5pO2krKyluPXJbaV0sZS5ib2R5LmFwcGVuZENoaWxkKG4pO3JldHVybiBlLmJvZHl9LHV9KGUuQmFzaWNPYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scixzLGEsdSxjLGwsaCxwPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIGkgaW4gZSlkLmNhbGwoZSxpKSYmKHRbaV09ZVtpXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LGQ9e30uaGFzT3duUHJvcGVydHksZj1bXS5pbmRleE9mfHxmdW5jdGlvbih0KXtmb3IodmFyIGU9MCxuPXRoaXMubGVuZ3RoO24+ZTtlKyspaWYoZSBpbiB0aGlzJiZ0aGlzW2VdPT09dClyZXR1cm4gZTtyZXR1cm4tMX07dD1lLmFycmF5c0FyZUVxdWFsLHM9ZS5tYWtlRWxlbWVudCxsPWUudGFnTmFtZSxyPWUuZ2V0QmxvY2tUYWdOYW1lcyxoPWUud2Fsa1RyZWUsbz1lLmZpbmRDbG9zZXN0RWxlbWVudEZyb21Ob2RlLGk9ZS5lbGVtZW50Q29udGFpbnNOb2RlLGE9ZS5ub2RlSXNBdHRhY2htZW50RWxlbWVudCx1PWUubm9ybWFsaXplU3BhY2VzLG49ZS5icmVha2FibGVXaGl0ZXNwYWNlUGF0dGVybixjPWUuc3F1aXNoQnJlYWthYmxlV2hpdGVzcGFjZSxlLkhUTUxQYXJzZXI9ZnVuY3Rpb24oZCl7ZnVuY3Rpb24gZyh0LGUpe3RoaXMuaHRtbD10LHRoaXMucmVmZXJlbmNlRWxlbWVudD0obnVsbCE9ZT9lOnt9KS5yZWZlcmVuY2VFbGVtZW50LHRoaXMuYmxvY2tzPVtdLHRoaXMuYmxvY2tFbGVtZW50cz1bXSx0aGlzLnByb2Nlc3NlZEVsZW1lbnRzPVtdfXZhciBtLHYseSxiLEEsQyx4LHcsRSxTLFIsaztyZXR1cm4gcChnLGQpLGcucGFyc2U9ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4gbj1uZXcgdGhpcyh0LGUpLG4ucGFyc2UoKSxufSxnLnByb3RvdHlwZS5nZXREb2N1bWVudD1mdW5jdGlvbigpe3JldHVybiBlLkRvY3VtZW50LmZyb21KU09OKHRoaXMuYmxvY2tzKX0sZy5wcm90b3R5cGUucGFyc2U9ZnVuY3Rpb24oKXt2YXIgdCxuO3RyeXtmb3IodGhpcy5jcmVhdGVIaWRkZW5Db250YWluZXIoKSx0PWUuSFRNTFNhbml0aXplci5zYW5pdGl6ZSh0aGlzLmh0bWwpLmdldEhUTUwoKSx0aGlzLmNvbnRhaW5lckVsZW1lbnQuaW5uZXJIVE1MPXQsbj1oKHRoaXMuY29udGFpbmVyRWxlbWVudCx7dXNpbmdGaWx0ZXI6eH0pO24ubmV4dE5vZGUoKTspdGhpcy5wcm9jZXNzTm9kZShuLmN1cnJlbnROb2RlKTtyZXR1cm4gdGhpcy50cmFuc2xhdGVCbG9ja0VsZW1lbnRNYXJnaW5zVG9OZXdsaW5lcygpfWZpbmFsbHl7dGhpcy5yZW1vdmVIaWRkZW5Db250YWluZXIoKX19LGcucHJvdG90eXBlLmNyZWF0ZUhpZGRlbkNvbnRhaW5lcj1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlZmVyZW5jZUVsZW1lbnQ/KHRoaXMuY29udGFpbmVyRWxlbWVudD10aGlzLnJlZmVyZW5jZUVsZW1lbnQuY2xvbmVOb2RlKCExKSx0aGlzLmNvbnRhaW5lckVsZW1lbnQucmVtb3ZlQXR0cmlidXRlKFwiaWRcIiksdGhpcy5jb250YWluZXJFbGVtZW50LnNldEF0dHJpYnV0ZShcImRhdGEtdHJpeC1pbnRlcm5hbFwiLFwiXCIpLHRoaXMuY29udGFpbmVyRWxlbWVudC5zdHlsZS5kaXNwbGF5PVwibm9uZVwiLHRoaXMucmVmZXJlbmNlRWxlbWVudC5wYXJlbnROb2RlLmluc2VydEJlZm9yZSh0aGlzLmNvbnRhaW5lckVsZW1lbnQsdGhpcy5yZWZlcmVuY2VFbGVtZW50Lm5leHRTaWJsaW5nKSk6KHRoaXMuY29udGFpbmVyRWxlbWVudD1zKHt0YWdOYW1lOlwiZGl2XCIsc3R5bGU6e2Rpc3BsYXk6XCJub25lXCJ9fSksZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZCh0aGlzLmNvbnRhaW5lckVsZW1lbnQpKX0sZy5wcm90b3R5cGUucmVtb3ZlSGlkZGVuQ29udGFpbmVyPWZ1bmN0aW9uKCl7cmV0dXJuIGUucmVtb3ZlTm9kZSh0aGlzLmNvbnRhaW5lckVsZW1lbnQpfSx4PWZ1bmN0aW9uKHQpe3JldHVyblwic3R5bGVcIj09PWwodCk/Tm9kZUZpbHRlci5GSUxURVJfUkVKRUNUOk5vZGVGaWx0ZXIuRklMVEVSX0FDQ0VQVH0sZy5wcm90b3R5cGUucHJvY2Vzc05vZGU9ZnVuY3Rpb24odCl7c3dpdGNoKHQubm9kZVR5cGUpe2Nhc2UgTm9kZS5URVhUX05PREU6aWYoIXRoaXMuaXNJbnNpZ25pZmljYW50VGV4dE5vZGUodCkpcmV0dXJuIHRoaXMuYXBwZW5kQmxvY2tGb3JUZXh0Tm9kZSh0KSx0aGlzLnByb2Nlc3NUZXh0Tm9kZSh0KTticmVhaztjYXNlIE5vZGUuRUxFTUVOVF9OT0RFOnJldHVybiB0aGlzLmFwcGVuZEJsb2NrRm9yRWxlbWVudCh0KSx0aGlzLnByb2Nlc3NFbGVtZW50KHQpfX0sZy5wcm90b3R5cGUuYXBwZW5kQmxvY2tGb3JUZXh0Tm9kZT1mdW5jdGlvbihlKXt2YXIgbixpLG87cmV0dXJuIGk9ZS5wYXJlbnROb2RlLGk9PT10aGlzLmN1cnJlbnRCbG9ja0VsZW1lbnQmJnRoaXMuaXNCbG9ja0VsZW1lbnQoZS5wcmV2aW91c1NpYmxpbmcpP3RoaXMuYXBwZW5kU3RyaW5nV2l0aEF0dHJpYnV0ZXMoXCJcXG5cIik6aSE9PXRoaXMuY29udGFpbmVyRWxlbWVudCYmIXRoaXMuaXNCbG9ja0VsZW1lbnQoaSl8fChuPXRoaXMuZ2V0QmxvY2tBdHRyaWJ1dGVzKGkpLHQobixudWxsIT0obz10aGlzLmN1cnJlbnRCbG9jayk/by5hdHRyaWJ1dGVzOnZvaWQgMCkpP3ZvaWQgMDoodGhpcy5jdXJyZW50QmxvY2s9dGhpcy5hcHBlbmRCbG9ja0ZvckF0dHJpYnV0ZXNXaXRoRWxlbWVudChuLGkpLHRoaXMuY3VycmVudEJsb2NrRWxlbWVudD1pKX0sZy5wcm90b3R5cGUuYXBwZW5kQmxvY2tGb3JFbGVtZW50PWZ1bmN0aW9uKGUpe3ZhciBuLG8scixzO2lmKHI9dGhpcy5pc0Jsb2NrRWxlbWVudChlKSxvPWkodGhpcy5jdXJyZW50QmxvY2tFbGVtZW50LGUpLHImJiF0aGlzLmlzQmxvY2tFbGVtZW50KGUuZmlyc3RDaGlsZCkpe2lmKCghdGhpcy5pc0luc2lnbmlmaWNhbnRUZXh0Tm9kZShlLmZpcnN0Q2hpbGQpfHwhdGhpcy5pc0Jsb2NrRWxlbWVudChlLmZpcnN0RWxlbWVudENoaWxkKSkmJihuPXRoaXMuZ2V0QmxvY2tBdHRyaWJ1dGVzKGUpLGUuZmlyc3RDaGlsZCkpcmV0dXJuIG8mJnQobix0aGlzLmN1cnJlbnRCbG9jay5hdHRyaWJ1dGVzKT90aGlzLmFwcGVuZFN0cmluZ1dpdGhBdHRyaWJ1dGVzKFwiXFxuXCIpOih0aGlzLmN1cnJlbnRCbG9jaz10aGlzLmFwcGVuZEJsb2NrRm9yQXR0cmlidXRlc1dpdGhFbGVtZW50KG4sZSksdGhpcy5jdXJyZW50QmxvY2tFbGVtZW50PWUpfWVsc2UgaWYodGhpcy5jdXJyZW50QmxvY2tFbGVtZW50JiYhbyYmIXIpcmV0dXJuKHM9dGhpcy5maW5kUGFyZW50QmxvY2tFbGVtZW50KGUpKT90aGlzLmFwcGVuZEJsb2NrRm9yRWxlbWVudChzKToodGhpcy5jdXJyZW50QmxvY2s9dGhpcy5hcHBlbmRFbXB0eUJsb2NrKCksdGhpcy5jdXJyZW50QmxvY2tFbGVtZW50PW51bGwpfSxnLnByb3RvdHlwZS5maW5kUGFyZW50QmxvY2tFbGVtZW50PWZ1bmN0aW9uKHQpe3ZhciBlO2ZvcihlPXQucGFyZW50RWxlbWVudDtlJiZlIT09dGhpcy5jb250YWluZXJFbGVtZW50Oyl7aWYodGhpcy5pc0Jsb2NrRWxlbWVudChlKSYmZi5jYWxsKHRoaXMuYmxvY2tFbGVtZW50cyxlKT49MClyZXR1cm4gZTtlPWUucGFyZW50RWxlbWVudH1yZXR1cm4gbnVsbH0sZy5wcm90b3R5cGUucHJvY2Vzc1RleHROb2RlPWZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIG49dC5kYXRhLHYodC5wYXJlbnROb2RlKXx8KG49YyhuKSxSKG51bGwhPShlPXQucHJldmlvdXNTaWJsaW5nKT9lLnRleHRDb250ZW50OnZvaWQgMCkmJihuPUEobikpKSx0aGlzLmFwcGVuZFN0cmluZ1dpdGhBdHRyaWJ1dGVzKG4sdGhpcy5nZXRUZXh0QXR0cmlidXRlcyh0LnBhcmVudE5vZGUpKX0sZy5wcm90b3R5cGUucHJvY2Vzc0VsZW1lbnQ9ZnVuY3Rpb24odCl7dmFyIGUsbixpLG8scjtpZihhKHQpKXJldHVybiBlPXcodCxcImF0dGFjaG1lbnRcIiksT2JqZWN0LmtleXMoZSkubGVuZ3RoJiYobz10aGlzLmdldFRleHRBdHRyaWJ1dGVzKHQpLHRoaXMuYXBwZW5kQXR0YWNobWVudFdpdGhBdHRyaWJ1dGVzKGUsbyksdC5pbm5lckhUTUw9XCJcIiksdGhpcy5wcm9jZXNzZWRFbGVtZW50cy5wdXNoKHQpO3N3aXRjaChsKHQpKXtjYXNlXCJiclwiOnJldHVybiB0aGlzLmlzRXh0cmFCUih0KXx8dGhpcy5pc0Jsb2NrRWxlbWVudCh0Lm5leHRTaWJsaW5nKXx8dGhpcy5hcHBlbmRTdHJpbmdXaXRoQXR0cmlidXRlcyhcIlxcblwiLHRoaXMuZ2V0VGV4dEF0dHJpYnV0ZXModCkpLHRoaXMucHJvY2Vzc2VkRWxlbWVudHMucHVzaCh0KTtjYXNlXCJpbWdcIjplPXt1cmw6dC5nZXRBdHRyaWJ1dGUoXCJzcmNcIiksY29udGVudFR5cGU6XCJpbWFnZVwifSxpPWIodCk7Zm9yKG4gaW4gaSlyPWlbbl0sZVtuXT1yO3JldHVybiB0aGlzLmFwcGVuZEF0dGFjaG1lbnRXaXRoQXR0cmlidXRlcyhlLHRoaXMuZ2V0VGV4dEF0dHJpYnV0ZXModCkpLHRoaXMucHJvY2Vzc2VkRWxlbWVudHMucHVzaCh0KTtjYXNlXCJ0clwiOmlmKHQucGFyZW50Tm9kZS5maXJzdENoaWxkIT09dClyZXR1cm4gdGhpcy5hcHBlbmRTdHJpbmdXaXRoQXR0cmlidXRlcyhcIlxcblwiKTticmVhaztjYXNlXCJ0ZFwiOmlmKHQucGFyZW50Tm9kZS5maXJzdENoaWxkIT09dClyZXR1cm4gdGhpcy5hcHBlbmRTdHJpbmdXaXRoQXR0cmlidXRlcyhcIiB8IFwiKX19LGcucHJvdG90eXBlLmFwcGVuZEJsb2NrRm9yQXR0cmlidXRlc1dpdGhFbGVtZW50PWZ1bmN0aW9uKHQsZSl7dmFyIG47cmV0dXJuIHRoaXMuYmxvY2tFbGVtZW50cy5wdXNoKGUpLG49bSh0KSx0aGlzLmJsb2Nrcy5wdXNoKG4pLG59LGcucHJvdG90eXBlLmFwcGVuZEVtcHR5QmxvY2s9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5hcHBlbmRCbG9ja0ZvckF0dHJpYnV0ZXNXaXRoRWxlbWVudChbXSxudWxsKX0sZy5wcm90b3R5cGUuYXBwZW5kU3RyaW5nV2l0aEF0dHJpYnV0ZXM9ZnVuY3Rpb24odCxlKXtyZXR1cm4gdGhpcy5hcHBlbmRQaWVjZShTKHQsZSkpfSxnLnByb3RvdHlwZS5hcHBlbmRBdHRhY2htZW50V2l0aEF0dHJpYnV0ZXM9ZnVuY3Rpb24odCxlKXtyZXR1cm4gdGhpcy5hcHBlbmRQaWVjZShFKHQsZSkpfSxnLnByb3RvdHlwZS5hcHBlbmRQaWVjZT1mdW5jdGlvbih0KXtyZXR1cm4gMD09PXRoaXMuYmxvY2tzLmxlbmd0aCYmdGhpcy5hcHBlbmRFbXB0eUJsb2NrKCksdGhpcy5ibG9ja3NbdGhpcy5ibG9ja3MubGVuZ3RoLTFdLnRleHQucHVzaCh0KX0sZy5wcm90b3R5cGUuYXBwZW5kU3RyaW5nVG9UZXh0QXRJbmRleD1mdW5jdGlvbih0LGUpe3ZhciBuLGk7cmV0dXJuIGk9dGhpcy5ibG9ja3NbZV0udGV4dCxuPWlbaS5sZW5ndGgtMV0sXCJzdHJpbmdcIj09PShudWxsIT1uP24udHlwZTp2b2lkIDApP24uc3RyaW5nKz10OmkucHVzaChTKHQpKX0sZy5wcm90b3R5cGUucHJlcGVuZFN0cmluZ1RvVGV4dEF0SW5kZXg9ZnVuY3Rpb24odCxlKXt2YXIgbixpO3JldHVybiBpPXRoaXMuYmxvY2tzW2VdLnRleHQsbj1pWzBdLFwic3RyaW5nXCI9PT0obnVsbCE9bj9uLnR5cGU6dm9pZCAwKT9uLnN0cmluZz10K24uc3RyaW5nOmkudW5zaGlmdChTKHQpKX0sUz1mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybiBudWxsPT1lJiYoZT17fSksbj1cInN0cmluZ1wiLHQ9dSh0KSx7c3RyaW5nOnQsYXR0cmlidXRlczplLHR5cGU6bn19LEU9ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4gbnVsbD09ZSYmKGU9e30pLG49XCJhdHRhY2htZW50XCIse2F0dGFjaG1lbnQ6dCxhdHRyaWJ1dGVzOmUsdHlwZTpufX0sbT1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gbnVsbD09dCYmKHQ9e30pLGU9W10se3RleHQ6ZSxhdHRyaWJ1dGVzOnR9fSxnLnByb3RvdHlwZS5nZXRUZXh0QXR0cmlidXRlcz1mdW5jdGlvbih0KXt2YXIgbixpLHIscyx1LGMsbCxoLHAsZCxmLGc7cj17fSxwPWUuY29uZmlnLnRleHRBdHRyaWJ1dGVzO2ZvcihuIGluIHApaWYodT1wW25dLHUudGFnTmFtZSYmbyh0LHttYXRjaGluZ1NlbGVjdG9yOnUudGFnTmFtZSx1bnRpbE5vZGU6dGhpcy5jb250YWluZXJFbGVtZW50fSkpcltuXT0hMDtlbHNlIGlmKHUucGFyc2VyKXtpZihnPXUucGFyc2VyKHQpKXtmb3IoaT0hMSxkPXRoaXMuZmluZEJsb2NrRWxlbWVudEFuY2VzdG9ycyh0KSxjPTAsaD1kLmxlbmd0aDtoPmM7YysrKWlmKHM9ZFtjXSx1LnBhcnNlcihzKT09PWcpe2k9ITA7YnJlYWt9aXx8KHJbbl09Zyl9fWVsc2UgdS5zdHlsZVByb3BlcnR5JiYoZz10LnN0eWxlW3Uuc3R5bGVQcm9wZXJ0eV0pJiYocltuXT1nKTtpZihhKHQpKXtmPXcodCxcImF0dHJpYnV0ZXNcIik7Zm9yKGwgaW4gZilnPWZbbF0scltsXT1nfXJldHVybiByfSxnLnByb3RvdHlwZS5nZXRCbG9ja0F0dHJpYnV0ZXM9ZnVuY3Rpb24odCl7dmFyIG4saSxvLHI7Zm9yKGk9W107dCYmdCE9PXRoaXMuY29udGFpbmVyRWxlbWVudDspe3I9ZS5jb25maWcuYmxvY2tBdHRyaWJ1dGVzO2ZvcihuIGluIHIpbz1yW25dLG8ucGFyc2UhPT0hMSYmbCh0KT09PW8udGFnTmFtZSYmKChcImZ1bmN0aW9uXCI9PXR5cGVvZiBvLnRlc3Q/by50ZXN0KHQpOnZvaWQgMCl8fCFvLnRlc3QpJiYoaS5wdXNoKG4pLG8ubGlzdEF0dHJpYnV0ZSYmaS5wdXNoKG8ubGlzdEF0dHJpYnV0ZSkpO3Q9dC5wYXJlbnROb2RlfXJldHVybiBpLnJldmVyc2UoKX0sZy5wcm90b3R5cGUuZmluZEJsb2NrRWxlbWVudEFuY2VzdG9ycz1mdW5jdGlvbih0KXt2YXIgZSxuO2ZvcihlPVtdO3QmJnQhPT10aGlzLmNvbnRhaW5lckVsZW1lbnQ7KW49bCh0KSxmLmNhbGwocigpLG4pPj0wJiZlLnB1c2godCksdD10LnBhcmVudE5vZGU7cmV0dXJuIGV9LHc9ZnVuY3Rpb24odCxlKXt0cnl7cmV0dXJuIEpTT04ucGFyc2UodC5nZXRBdHRyaWJ1dGUoXCJkYXRhLXRyaXgtXCIrZSkpfWNhdGNoKG4pe3JldHVybnt9fX0sYj1mdW5jdGlvbih0KXt2YXIgZSxuLGk7cmV0dXJuIGk9dC5nZXRBdHRyaWJ1dGUoXCJ3aWR0aFwiKSxuPXQuZ2V0QXR0cmlidXRlKFwiaGVpZ2h0XCIpLGU9e30saSYmKGUud2lkdGg9cGFyc2VJbnQoaSwxMCkpLG4mJihlLmhlaWdodD1wYXJzZUludChuLDEwKSksZX0sZy5wcm90b3R5cGUuaXNCbG9ja0VsZW1lbnQ9ZnVuY3Rpb24odCl7dmFyIGU7aWYoKG51bGwhPXQ/dC5ub2RlVHlwZTp2b2lkIDApPT09Tm9kZS5FTEVNRU5UX05PREUmJiFhKHQpJiYhbyh0LHttYXRjaGluZ1NlbGVjdG9yOlwidGRcIix1bnRpbE5vZGU6dGhpcy5jb250YWluZXJFbGVtZW50fSkpcmV0dXJuIGU9bCh0KSxmLmNhbGwocigpLGUpPj0wfHxcImJsb2NrXCI9PT13aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZSh0KS5kaXNwbGF5fSxnLnByb3RvdHlwZS5pc0luc2lnbmlmaWNhbnRUZXh0Tm9kZT1mdW5jdGlvbih0KXt2YXIgZSxuLGk7aWYoKG51bGwhPXQ/dC5ub2RlVHlwZTp2b2lkIDApPT09Tm9kZS5URVhUX05PREUmJmsodC5kYXRhKSYmKG49dC5wYXJlbnROb2RlLGk9dC5wcmV2aW91c1NpYmxpbmcsZT10Lm5leHRTaWJsaW5nLCghQyhuLnByZXZpb3VzU2libGluZyl8fHRoaXMuaXNCbG9ja0VsZW1lbnQobi5wcmV2aW91c1NpYmxpbmcpKSYmIXYobikpKXJldHVybiFpfHx0aGlzLmlzQmxvY2tFbGVtZW50KGkpfHwhZXx8dGhpcy5pc0Jsb2NrRWxlbWVudChlKX0sZy5wcm90b3R5cGUuaXNFeHRyYUJSPWZ1bmN0aW9uKHQpe3JldHVyblwiYnJcIj09PWwodCkmJnRoaXMuaXNCbG9ja0VsZW1lbnQodC5wYXJlbnROb2RlKSYmdC5wYXJlbnROb2RlLmxhc3RDaGlsZD09PXR9LHY9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGU9d2luZG93LmdldENvbXB1dGVkU3R5bGUodCkud2hpdGVTcGFjZSxcInByZVwiPT09ZXx8XCJwcmUtd3JhcFwiPT09ZXx8XCJwcmUtbGluZVwiPT09ZX0sQz1mdW5jdGlvbih0KXtyZXR1cm4gdCYmIVIodC50ZXh0Q29udGVudCl9LGcucHJvdG90eXBlLnRyYW5zbGF0ZUJsb2NrRWxlbWVudE1hcmdpbnNUb05ld2xpbmVzPWZ1bmN0aW9uKCl7dmFyIHQsZSxuLGksbyxyLHMsYTtmb3IoZT10aGlzLmdldE1hcmdpbk9mRGVmYXVsdEJsb2NrRWxlbWVudCgpLHM9dGhpcy5ibG9ja3MsYT1bXSxpPW49MCxvPXMubGVuZ3RoO28+bjtpPSsrbil0PXNbaV0sKHI9dGhpcy5nZXRNYXJnaW5PZkJsb2NrRWxlbWVudEF0SW5kZXgoaSkpJiYoci50b3A+MiplLnRvcCYmdGhpcy5wcmVwZW5kU3RyaW5nVG9UZXh0QXRJbmRleChcIlxcblwiLGkpLGEucHVzaChyLmJvdHRvbT4yKmUuYm90dG9tP3RoaXMuYXBwZW5kU3RyaW5nVG9UZXh0QXRJbmRleChcIlxcblwiLGkpOnZvaWQgMCkpO3JldHVybiBhfSxnLnByb3RvdHlwZS5nZXRNYXJnaW5PZkJsb2NrRWxlbWVudEF0SW5kZXg9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4hKGU9dGhpcy5ibG9ja0VsZW1lbnRzW3RdKXx8IWUudGV4dENvbnRlbnR8fChuPWwoZSksZi5jYWxsKHIoKSxuKT49MHx8Zi5jYWxsKHRoaXMucHJvY2Vzc2VkRWxlbWVudHMsZSk+PTApP3ZvaWQgMDp5KGUpfSxnLnByb3RvdHlwZS5nZXRNYXJnaW5PZkRlZmF1bHRCbG9ja0VsZW1lbnQ9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gdD1zKGUuY29uZmlnLmJsb2NrQXR0cmlidXRlc1tcImRlZmF1bHRcIl0udGFnTmFtZSksdGhpcy5jb250YWluZXJFbGVtZW50LmFwcGVuZENoaWxkKHQpLHkodCl9LHk9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGU9d2luZG93LmdldENvbXB1dGVkU3R5bGUodCksXCJibG9ja1wiPT09ZS5kaXNwbGF5P3t0b3A6cGFyc2VJbnQoZS5tYXJnaW5Ub3ApLGJvdHRvbTpwYXJzZUludChlLm1hcmdpbkJvdHRvbSl9OnZvaWQgMH0sQT1mdW5jdGlvbih0KXtyZXR1cm4gdC5yZXBsYWNlKFJlZ0V4cChcIl5cIituLnNvdXJjZStcIitcIiksXCJcIil9LGs9ZnVuY3Rpb24odCl7cmV0dXJuIFJlZ0V4cChcIl5cIituLnNvdXJjZStcIiokXCIpLnRlc3QodCl9LFI9ZnVuY3Rpb24odCl7cmV0dXJuL1xccyQvLnRlc3QodCl9LGd9KGUuQmFzaWNPYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scj1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpcy5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxzPXt9Lmhhc093blByb3BlcnR5LGE9W10uc2xpY2UsdT1bXS5pbmRleE9mfHxmdW5jdGlvbih0KXtmb3IodmFyIGU9MCxuPXRoaXMubGVuZ3RoO24+ZTtlKyspaWYoZSBpbiB0aGlzJiZ0aGlzW2VdPT09dClyZXR1cm4gZTtyZXR1cm4tMX07dD1lLmFycmF5c0FyZUVxdWFsLGk9ZS5ub3JtYWxpemVSYW5nZSxvPWUucmFuZ2VJc0NvbGxhcHNlZCxuPWUuZ2V0QmxvY2tDb25maWcsZS5Eb2N1bWVudD1mdW5jdGlvbihzKXtmdW5jdGlvbiBjKHQpe251bGw9PXQmJih0PVtdKSxjLl9fc3VwZXJfXy5jb25zdHJ1Y3Rvci5hcHBseSh0aGlzLGFyZ3VtZW50cyksMD09PXQubGVuZ3RoJiYodD1bbmV3IGUuQmxvY2tdKSx0aGlzLmJsb2NrTGlzdD1lLlNwbGl0dGFibGVMaXN0LmJveCh0KX12YXIgbDtyZXR1cm4gcihjLHMpLGMuZnJvbUpTT049ZnVuY3Rpb24odCl7dmFyIG4saTtyZXR1cm4gaT1mdW5jdGlvbigpe3ZhciBpLG8scjtmb3Iocj1bXSxpPTAsbz10Lmxlbmd0aDtvPmk7aSsrKW49dFtpXSxyLnB1c2goZS5CbG9jay5mcm9tSlNPTihuKSk7cmV0dXJuIHJ9KCksbmV3IHRoaXMoaSl9LGMuZnJvbUhUTUw9ZnVuY3Rpb24odCxuKXtyZXR1cm4gZS5IVE1MUGFyc2VyLnBhcnNlKHQsbikuZ2V0RG9jdW1lbnQoKX0sYy5mcm9tU3RyaW5nPWZ1bmN0aW9uKHQsbil7dmFyIGk7cmV0dXJuIGk9ZS5UZXh0LnRleHRGb3JTdHJpbmdXaXRoQXR0cmlidXRlcyh0LG4pLG5ldyB0aGlzKFtuZXcgZS5CbG9jayhpKV0pfSxjLnByb3RvdHlwZS5pc0VtcHR5PWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIDE9PT10aGlzLmJsb2NrTGlzdC5sZW5ndGgmJih0PXRoaXMuZ2V0QmxvY2tBdEluZGV4KDApLHQuaXNFbXB0eSgpJiYhdC5oYXNBdHRyaWJ1dGVzKCkpfSxjLnByb3RvdHlwZS5jb3B5PWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBudWxsPT10JiYodD17fSksZT10LmNvbnNvbGlkYXRlQmxvY2tzP3RoaXMuYmxvY2tMaXN0LmNvbnNvbGlkYXRlKCkudG9BcnJheSgpOnRoaXMuYmxvY2tMaXN0LnRvQXJyYXkoKSxuZXcgdGhpcy5jb25zdHJ1Y3RvcihlKX0sYy5wcm90b3R5cGUuY29weVVzaW5nT2JqZWN0c0Zyb21Eb2N1bWVudD1mdW5jdGlvbih0KXt2YXIgbjtyZXR1cm4gbj1uZXcgZS5PYmplY3RNYXAodC5nZXRPYmplY3RzKCkpLHRoaXMuY29weVVzaW5nT2JqZWN0TWFwKG4pfSxjLnByb3RvdHlwZS5jb3B5VXNpbmdPYmplY3RNYXA9ZnVuY3Rpb24odCl7dmFyIGUsbixpO3JldHVybiBuPWZ1bmN0aW9uKCl7dmFyIG4sbyxyLHM7Zm9yKHI9dGhpcy5nZXRCbG9ja3MoKSxzPVtdLG49MCxvPXIubGVuZ3RoO28+bjtuKyspZT1yW25dLHMucHVzaCgoaT10LmZpbmQoZSkpP2k6ZS5jb3B5VXNpbmdPYmplY3RNYXAodCkpO3JldHVybiBzfS5jYWxsKHRoaXMpLG5ldyB0aGlzLmNvbnN0cnVjdG9yKG4pfSxjLnByb3RvdHlwZS5jb3B5V2l0aEJhc2VCbG9ja0F0dHJpYnV0ZXM9ZnVuY3Rpb24odCl7dmFyIGUsbixpO3JldHVybiBudWxsPT10JiYodD1bXSksaT1mdW5jdGlvbigpe3ZhciBpLG8scixzO2ZvcihyPXRoaXMuZ2V0QmxvY2tzKCkscz1bXSxpPTAsbz1yLmxlbmd0aDtvPmk7aSsrKW49cltpXSxlPXQuY29uY2F0KG4uZ2V0QXR0cmlidXRlcygpKSxzLnB1c2gobi5jb3B5V2l0aEF0dHJpYnV0ZXMoZSkpO3JldHVybiBzfS5jYWxsKHRoaXMpLG5ldyB0aGlzLmNvbnN0cnVjdG9yKGkpfSxjLnByb3RvdHlwZS5yZXBsYWNlQmxvY2s9ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4gbj10aGlzLmJsb2NrTGlzdC5pbmRleE9mKHQpLC0xPT09bj90aGlzOm5ldyB0aGlzLmNvbnN0cnVjdG9yKHRoaXMuYmxvY2tMaXN0LnJlcGxhY2VPYmplY3RBdEluZGV4KGUsbikpfSxjLnByb3RvdHlwZS5pbnNlcnREb2N1bWVudEF0UmFuZ2U9ZnVuY3Rpb24odCxlKXt2YXIgbixyLHMsYSx1LGMsbDtyZXR1cm4gcj10LmJsb2NrTGlzdCx1PShlPWkoZSkpWzBdLGM9dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbih1KSxzPWMuaW5kZXgsYT1jLm9mZnNldCxsPXRoaXMsbj10aGlzLmdldEJsb2NrQXRQb3NpdGlvbih1KSxvKGUpJiZuLmlzRW1wdHkoKSYmIW4uaGFzQXR0cmlidXRlcygpP2w9bmV3IHRoaXMuY29uc3RydWN0b3IobC5ibG9ja0xpc3QucmVtb3ZlT2JqZWN0QXRJbmRleChzKSk6bi5nZXRCbG9ja0JyZWFrUG9zaXRpb24oKT09PWEmJnUrKyxsPWwucmVtb3ZlVGV4dEF0UmFuZ2UoZSksbmV3IHRoaXMuY29uc3RydWN0b3IobC5ibG9ja0xpc3QuaW5zZXJ0U3BsaXR0YWJsZUxpc3RBdFBvc2l0aW9uKHIsdSkpfSxjLnByb3RvdHlwZS5tZXJnZURvY3VtZW50QXRSYW5nZT1mdW5jdGlvbihlLG4pe3ZhciBvLHIscyxhLHUsYyxsLGgscCxkLGYsZztyZXR1cm4gZj0obj1pKG4pKVswXSxkPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24oZikscj10aGlzLmdldEJsb2NrQXRJbmRleChkLmluZGV4KS5nZXRBdHRyaWJ1dGVzKCksbz1lLmdldEJhc2VCbG9ja0F0dHJpYnV0ZXMoKSxnPXIuc2xpY2UoLW8ubGVuZ3RoKSx0KG8sZyk/KGw9ci5zbGljZSgwLC1vLmxlbmd0aCksYz1lLmNvcHlXaXRoQmFzZUJsb2NrQXR0cmlidXRlcyhsKSk6Yz1lLmNvcHkoe2NvbnNvbGlkYXRlQmxvY2tzOiEwfSkuY29weVdpdGhCYXNlQmxvY2tBdHRyaWJ1dGVzKHIpLHM9Yy5nZXRCbG9ja0NvdW50KCksYT1jLmdldEJsb2NrQXRJbmRleCgwKSx0KHIsYS5nZXRBdHRyaWJ1dGVzKCkpPyh1PWEuZ2V0VGV4dFdpdGhvdXRCbG9ja0JyZWFrKCkscD10aGlzLmluc2VydFRleHRBdFJhbmdlKHUsbikscz4xJiYoYz1uZXcgdGhpcy5jb25zdHJ1Y3RvcihjLmdldEJsb2NrcygpLnNsaWNlKDEpKSxoPWYrdS5nZXRMZW5ndGgoKSxwPXAuaW5zZXJ0RG9jdW1lbnRBdFJhbmdlKGMsaCkpKTpwPXRoaXMuaW5zZXJ0RG9jdW1lbnRBdFJhbmdlKGMsbikscH0sYy5wcm90b3R5cGUuaW5zZXJ0VGV4dEF0UmFuZ2U9ZnVuY3Rpb24odCxlKXt2YXIgbixvLHIscyxhO3JldHVybiBhPShlPWkoZSkpWzBdLHM9dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbihhKSxvPXMuaW5kZXgscj1zLm9mZnNldCxuPXRoaXMucmVtb3ZlVGV4dEF0UmFuZ2UoZSksbmV3IHRoaXMuY29uc3RydWN0b3Iobi5ibG9ja0xpc3QuZWRpdE9iamVjdEF0SW5kZXgobyxmdW5jdGlvbihlKXtyZXR1cm4gZS5jb3B5V2l0aFRleHQoZS50ZXh0Lmluc2VydFRleHRBdFBvc2l0aW9uKHQscikpfSkpfSxjLnByb3RvdHlwZS5yZW1vdmVUZXh0QXRSYW5nZT1mdW5jdGlvbih0KXt2YXIgZSxuLHIscyxhLHUsYyxsLGgscCxkLGYsZyxtLHYseSxiLEEsQyx4LHc7cmV0dXJuIHA9dD1pKHQpLGw9cFswXSxBPXBbMV0sbyh0KT90aGlzOihkPXRoaXMubG9jYXRpb25SYW5nZUZyb21SYW5nZSh0KSx1PWRbMF0seT1kWzFdLGE9dS5pbmRleCxjPXUub2Zmc2V0LHM9dGhpcy5nZXRCbG9ja0F0SW5kZXgoYSksdj15LmluZGV4LGI9eS5vZmZzZXQsbT10aGlzLmdldEJsb2NrQXRJbmRleCh2KSxmPUEtbD09PTEmJnMuZ2V0QmxvY2tCcmVha1Bvc2l0aW9uKCk9PT1jJiZtLmdldEJsb2NrQnJlYWtQb3NpdGlvbigpIT09YiYmXCJcXG5cIj09PW0udGV4dC5nZXRTdHJpbmdBdFBvc2l0aW9uKGIpLGY/cj10aGlzLmJsb2NrTGlzdC5lZGl0T2JqZWN0QXRJbmRleCh2LGZ1bmN0aW9uKHQpe3JldHVybiB0LmNvcHlXaXRoVGV4dCh0LnRleHQucmVtb3ZlVGV4dEF0UmFuZ2UoW2IsYisxXSkpfSk6KGg9cy50ZXh0LmdldFRleHRBdFJhbmdlKFswLGNdKSxDPW0udGV4dC5nZXRUZXh0QXRSYW5nZShbYixtLmdldExlbmd0aCgpXSkseD1oLmFwcGVuZFRleHQoQyksZz1hIT09diYmMD09PWMsdz1nJiZzLmdldEF0dHJpYnV0ZUxldmVsKCk+PW0uZ2V0QXR0cmlidXRlTGV2ZWwoKSxuPXc/bS5jb3B5V2l0aFRleHQoeCk6cy5jb3B5V2l0aFRleHQoeCksZT12KzEtYSxyPXRoaXMuYmxvY2tMaXN0LnNwbGljZShhLGUsbikpLG5ldyB0aGlzLmNvbnN0cnVjdG9yKHIpKX0sYy5wcm90b3R5cGUubW92ZVRleHRGcm9tUmFuZ2VUb1Bvc2l0aW9uPWZ1bmN0aW9uKHQsZSl7dmFyIG4sbyxyLHMsdSxjLGwsaCxwLGQ7cmV0dXJuIGM9dD1pKHQpLHA9Y1swXSxyPWNbMV0sZT49cCYmcj49ZT90aGlzOihvPXRoaXMuZ2V0RG9jdW1lbnRBdFJhbmdlKHQpLGg9dGhpcy5yZW1vdmVUZXh0QXRSYW5nZSh0KSx1PWU+cCx1JiYoZS09by5nZXRMZW5ndGgoKSksbD1vLmdldEJsb2NrcygpLHM9bFswXSxuPTI8PWwubGVuZ3RoP2EuY2FsbChsLDEpOltdLDA9PT1uLmxlbmd0aD8oZD1zLmdldFRleHRXaXRob3V0QmxvY2tCcmVhaygpLHUmJihlKz0xKSk6ZD1zLnRleHQsaD1oLmluc2VydFRleHRBdFJhbmdlKGQsZSksMD09PW4ubGVuZ3RoP2g6KG89bmV3IHRoaXMuY29uc3RydWN0b3IobiksZSs9ZC5nZXRMZW5ndGgoKSxoLmluc2VydERvY3VtZW50QXRSYW5nZShvLGUpKSl9LGMucHJvdG90eXBlLmFkZEF0dHJpYnV0ZUF0UmFuZ2U9ZnVuY3Rpb24odCxlLGkpe3ZhciBvO3JldHVybiBvPXRoaXMuYmxvY2tMaXN0LHRoaXMuZWFjaEJsb2NrQXRSYW5nZShpLGZ1bmN0aW9uKGkscixzKXtyZXR1cm4gbz1vLmVkaXRPYmplY3RBdEluZGV4KHMsZnVuY3Rpb24oKXtyZXR1cm4gbih0KT9pLmFkZEF0dHJpYnV0ZSh0LGUpOnJbMF09PT1yWzFdP2k6aS5jb3B5V2l0aFRleHQoaS50ZXh0LmFkZEF0dHJpYnV0ZUF0UmFuZ2UodCxlLHIpKX0pfSksbmV3IHRoaXMuY29uc3RydWN0b3Iobyl9LGMucHJvdG90eXBlLmFkZEF0dHJpYnV0ZT1mdW5jdGlvbih0LGUpe3ZhciBuO3JldHVybiBuPXRoaXMuYmxvY2tMaXN0LHRoaXMuZWFjaEJsb2NrKGZ1bmN0aW9uKGksbyl7cmV0dXJuIG49bi5lZGl0T2JqZWN0QXRJbmRleChvLGZ1bmN0aW9uKCl7cmV0dXJuIGkuYWRkQXR0cmlidXRlKHQsZSl9KX0pLG5ldyB0aGlzLmNvbnN0cnVjdG9yKG4pfSxjLnByb3RvdHlwZS5yZW1vdmVBdHRyaWJ1dGVBdFJhbmdlPWZ1bmN0aW9uKHQsZSl7dmFyIGk7cmV0dXJuIGk9dGhpcy5ibG9ja0xpc3QsdGhpcy5lYWNoQmxvY2tBdFJhbmdlKGUsZnVuY3Rpb24oZSxvLHIpe3JldHVybiBuKHQpP2k9aS5lZGl0T2JqZWN0QXRJbmRleChyLGZ1bmN0aW9uKCl7cmV0dXJuIGUucmVtb3ZlQXR0cmlidXRlKHQpfSk6b1swXSE9PW9bMV0/aT1pLmVkaXRPYmplY3RBdEluZGV4KHIsZnVuY3Rpb24oKXtyZXR1cm4gZS5jb3B5V2l0aFRleHQoZS50ZXh0LnJlbW92ZUF0dHJpYnV0ZUF0UmFuZ2UodCxvKSl9KTp2b2lkIDB9KSxuZXcgdGhpcy5jb25zdHJ1Y3RvcihpKX0sYy5wcm90b3R5cGUudXBkYXRlQXR0cmlidXRlc0ZvckF0dGFjaG1lbnQ9ZnVuY3Rpb24odCxlKXt2YXIgbixpLG8scjtyZXR1cm4gbz0oaT10aGlzLmdldFJhbmdlT2ZBdHRhY2htZW50KGUpKVswXSxuPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24obykuaW5kZXgscj10aGlzLmdldFRleHRBdEluZGV4KG4pLG5ldyB0aGlzLmNvbnN0cnVjdG9yKHRoaXMuYmxvY2tMaXN0LmVkaXRPYmplY3RBdEluZGV4KG4sZnVuY3Rpb24obil7cmV0dXJuIG4uY29weVdpdGhUZXh0KHIudXBkYXRlQXR0cmlidXRlc0ZvckF0dGFjaG1lbnQodCxlKSl9KSl9LGMucHJvdG90eXBlLnJlbW92ZUF0dHJpYnV0ZUZvckF0dGFjaG1lbnQ9ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4gbj10aGlzLmdldFJhbmdlT2ZBdHRhY2htZW50KGUpLHRoaXMucmVtb3ZlQXR0cmlidXRlQXRSYW5nZSh0LG4pfSxjLnByb3RvdHlwZS5pbnNlcnRCbG9ja0JyZWFrQXRSYW5nZT1mdW5jdGlvbih0KXt2YXIgbixvLHIscztyZXR1cm4gcz0odD1pKHQpKVswXSxyPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24ocykub2Zmc2V0LG89dGhpcy5yZW1vdmVUZXh0QXRSYW5nZSh0KSwwPT09ciYmKG49W25ldyBlLkJsb2NrXSksbmV3IHRoaXMuY29uc3RydWN0b3Ioby5ibG9ja0xpc3QuaW5zZXJ0U3BsaXR0YWJsZUxpc3RBdFBvc2l0aW9uKG5ldyBlLlNwbGl0dGFibGVMaXN0KG4pLHMpKX0sYy5wcm90b3R5cGUuYXBwbHlCbG9ja0F0dHJpYnV0ZUF0UmFuZ2U9ZnVuY3Rpb24odCxlLGkpe3ZhciBvLHIscyxhO3JldHVybiBzPXRoaXMuZXhwYW5kUmFuZ2VUb0xpbmVCcmVha3NBbmRTcGxpdEJsb2NrcyhpKSxyPXMuZG9jdW1lbnQsaT1zLnJhbmdlLG89bih0KSxvLmxpc3RBdHRyaWJ1dGU/KHI9ci5yZW1vdmVMYXN0TGlzdEF0dHJpYnV0ZUF0UmFuZ2UoaSx7ZXhjZXB0QXR0cmlidXRlTmFtZTp0fSksYT1yLmNvbnZlcnRMaW5lQnJlYWtzVG9CbG9ja0JyZWFrc0luUmFuZ2UoaSkscj1hLmRvY3VtZW50LGk9YS5yYW5nZSk6cj1vLmV4Y2x1c2l2ZT9yLnJlbW92ZUJsb2NrQXR0cmlidXRlc0F0UmFuZ2UoaSk6by50ZXJtaW5hbD9yLnJlbW92ZUxhc3RUZXJtaW5hbEF0dHJpYnV0ZUF0UmFuZ2UoaSk6ci5jb25zb2xpZGF0ZUJsb2Nrc0F0UmFuZ2UoaSksci5hZGRBdHRyaWJ1dGVBdFJhbmdlKHQsZSxpKX0sYy5wcm90b3R5cGUucmVtb3ZlTGFzdExpc3RBdHRyaWJ1dGVBdFJhbmdlPWZ1bmN0aW9uKHQsZSl7dmFyIGk7cmV0dXJuIG51bGw9PWUmJihlPXt9KSxpPXRoaXMuYmxvY2tMaXN0LHRoaXMuZWFjaEJsb2NrQXRSYW5nZSh0LGZ1bmN0aW9uKHQsbyxyKXt2YXIgcztpZigocz10LmdldExhc3RBdHRyaWJ1dGUoKSkmJm4ocykubGlzdEF0dHJpYnV0ZSYmcyE9PWUuZXhjZXB0QXR0cmlidXRlTmFtZSlyZXR1cm4gaT1pLmVkaXRPYmplY3RBdEluZGV4KHIsZnVuY3Rpb24oKXtyZXR1cm4gdC5yZW1vdmVBdHRyaWJ1dGUocyl9KX0pLG5ldyB0aGlzLmNvbnN0cnVjdG9yKGkpfSxjLnByb3RvdHlwZS5yZW1vdmVMYXN0VGVybWluYWxBdHRyaWJ1dGVBdFJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBlPXRoaXMuYmxvY2tMaXN0LHRoaXMuZWFjaEJsb2NrQXRSYW5nZSh0LGZ1bmN0aW9uKHQsaSxvKXt2YXIgcjtpZigocj10LmdldExhc3RBdHRyaWJ1dGUoKSkmJm4ocikudGVybWluYWwpcmV0dXJuIGU9ZS5lZGl0T2JqZWN0QXRJbmRleChvLGZ1bmN0aW9uKCl7cmV0dXJuIHQucmVtb3ZlQXR0cmlidXRlKHIpfSl9KSxuZXcgdGhpcy5jb25zdHJ1Y3RvcihlKX0sYy5wcm90b3R5cGUucmVtb3ZlQmxvY2tBdHRyaWJ1dGVzQXRSYW5nZT1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT10aGlzLmJsb2NrTGlzdCx0aGlzLmVhY2hCbG9ja0F0UmFuZ2UodCxmdW5jdGlvbih0LG4saSl7cmV0dXJuIHQuaGFzQXR0cmlidXRlcygpP2U9ZS5lZGl0T2JqZWN0QXRJbmRleChpLGZ1bmN0aW9uKCl7cmV0dXJuIHQuY29weVdpdGhvdXRBdHRyaWJ1dGVzKCl9KTp2b2lkIDB9KSxuZXcgdGhpcy5jb25zdHJ1Y3RvcihlKX0sYy5wcm90b3R5cGUuZXhwYW5kUmFuZ2VUb0xpbmVCcmVha3NBbmRTcGxpdEJsb2Nrcz1mdW5jdGlvbih0KXt2YXIgZSxuLG8scixzLGEsdSxjLGw7cmV0dXJuIGE9dD1pKHQpLGw9YVswXSxyPWFbMV0sYz10aGlzLmxvY2F0aW9uRnJvbVBvc2l0aW9uKGwpLG89dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbihyKSxlPXRoaXMsdT1lLmdldEJsb2NrQXRJbmRleChjLmluZGV4KSxudWxsIT0oYy5vZmZzZXQ9dS5maW5kTGluZUJyZWFrSW5EaXJlY3Rpb25Gcm9tUG9zaXRpb24oXCJiYWNrd2FyZFwiLGMub2Zmc2V0KSkmJihzPWUucG9zaXRpb25Gcm9tTG9jYXRpb24oYyksZT1lLmluc2VydEJsb2NrQnJlYWtBdFJhbmdlKFtzLHMrMV0pLG8uaW5kZXgrPTEsby5vZmZzZXQtPWUuZ2V0QmxvY2tBdEluZGV4KGMuaW5kZXgpLmdldExlbmd0aCgpLGMuaW5kZXgrPTEpLGMub2Zmc2V0PTAsMD09PW8ub2Zmc2V0JiZvLmluZGV4PmMuaW5kZXg/KG8uaW5kZXgtPTEsby5vZmZzZXQ9ZS5nZXRCbG9ja0F0SW5kZXgoby5pbmRleCkuZ2V0QmxvY2tCcmVha1Bvc2l0aW9uKCkpOihuPWUuZ2V0QmxvY2tBdEluZGV4KG8uaW5kZXgpLFwiXFxuXCI9PT1uLnRleHQuZ2V0U3RyaW5nQXRSYW5nZShbby5vZmZzZXQtMSxvLm9mZnNldF0pP28ub2Zmc2V0LT0xOm8ub2Zmc2V0PW4uZmluZExpbmVCcmVha0luRGlyZWN0aW9uRnJvbVBvc2l0aW9uKFwiZm9yd2FyZFwiLG8ub2Zmc2V0KSxvLm9mZnNldCE9PW4uZ2V0QmxvY2tCcmVha1Bvc2l0aW9uKCkmJihzPWUucG9zaXRpb25Gcm9tTG9jYXRpb24obyksZT1lLmluc2VydEJsb2NrQnJlYWtBdFJhbmdlKFtzLHMrMV0pKSksbD1lLnBvc2l0aW9uRnJvbUxvY2F0aW9uKGMpLHI9ZS5wb3NpdGlvbkZyb21Mb2NhdGlvbihvKSx0PWkoW2wscl0pLHtkb2N1bWVudDplLHJhbmdlOnR9fSxjLnByb3RvdHlwZS5jb252ZXJ0TGluZUJyZWFrc1RvQmxvY2tCcmVha3NJblJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlLG4sbztyZXR1cm4gbj0odD1pKHQpKVswXSxvPXRoaXMuZ2V0U3RyaW5nQXRSYW5nZSh0KS5zbGljZSgwLC0xKSxlPXRoaXMsby5yZXBsYWNlKC8uKj9cXG4vZyxmdW5jdGlvbih0KXtyZXR1cm4gbis9dC5sZW5ndGgsZT1lLmluc2VydEJsb2NrQnJlYWtBdFJhbmdlKFtuLTEsbl0pfSkse2RvY3VtZW50OmUscmFuZ2U6dH19LGMucHJvdG90eXBlLmNvbnNvbGlkYXRlQmxvY2tzQXRSYW5nZT1mdW5jdGlvbih0KXt2YXIgZSxuLG8scixzO3JldHVybiBvPXQ9aSh0KSxzPW9bMF0sbj1vWzFdLHI9dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbihzKS5pbmRleCxlPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24obikuaW5kZXgsbmV3IHRoaXMuY29uc3RydWN0b3IodGhpcy5ibG9ja0xpc3QuY29uc29saWRhdGVGcm9tSW5kZXhUb0luZGV4KHIsZSkpfSxjLnByb3RvdHlwZS5nZXREb2N1bWVudEF0UmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHQ9aSh0KSxlPXRoaXMuYmxvY2tMaXN0LmdldFNwbGl0dGFibGVMaXN0SW5SYW5nZSh0KS50b0FycmF5KCksbmV3IHRoaXMuY29uc3RydWN0b3IoZSl9LGMucHJvdG90eXBlLmdldFN0cmluZ0F0UmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGUsbixvO3JldHVybiBvPXQ9aSh0KSxuPW9bby5sZW5ndGgtMV0sbiE9PXRoaXMuZ2V0TGVuZ3RoKCkmJihlPS0xKSx0aGlzLmdldERvY3VtZW50QXRSYW5nZSh0KS50b1N0cmluZygpLnNsaWNlKDAsZSl9LGMucHJvdG90eXBlLmdldEJsb2NrQXRJbmRleD1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5ibG9ja0xpc3QuZ2V0T2JqZWN0QXRJbmRleCh0KX0sYy5wcm90b3R5cGUuZ2V0QmxvY2tBdFBvc2l0aW9uPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBlPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24odCkuaW5kZXgsdGhpcy5nZXRCbG9ja0F0SW5kZXgoZSl9LGMucHJvdG90eXBlLmdldFRleHRBdEluZGV4PWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBudWxsIT0oZT10aGlzLmdldEJsb2NrQXRJbmRleCh0KSk/ZS50ZXh0OnZvaWQgMH0sYy5wcm90b3R5cGUuZ2V0VGV4dEF0UG9zaXRpb249ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGU9dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbih0KS5pbmRleCx0aGlzLmdldFRleHRBdEluZGV4KGUpfSxjLnByb3RvdHlwZS5nZXRQaWVjZUF0UG9zaXRpb249ZnVuY3Rpb24odCl7dmFyIGUsbixpO3JldHVybiBpPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24odCksZT1pLmluZGV4LG49aS5vZmZzZXQsdGhpcy5nZXRUZXh0QXRJbmRleChlKS5nZXRQaWVjZUF0UG9zaXRpb24obil9LGMucHJvdG90eXBlLmdldENoYXJhY3RlckF0UG9zaXRpb249ZnVuY3Rpb24odCl7dmFyIGUsbixpO3JldHVybiBpPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24odCksZT1pLmluZGV4LG49aS5vZmZzZXQsdGhpcy5nZXRUZXh0QXRJbmRleChlKS5nZXRTdHJpbmdBdFJhbmdlKFtuLG4rMV0pfSxjLnByb3RvdHlwZS5nZXRMZW5ndGg9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5ibG9ja0xpc3QuZ2V0RW5kUG9zaXRpb24oKX0sYy5wcm90b3R5cGUuZ2V0QmxvY2tzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYmxvY2tMaXN0LnRvQXJyYXkoKX0sYy5wcm90b3R5cGUuZ2V0QmxvY2tDb3VudD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmJsb2NrTGlzdC5sZW5ndGh9LGMucHJvdG90eXBlLmdldEVkaXRDb3VudD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmVkaXRDb3VudH0sYy5wcm90b3R5cGUuZWFjaEJsb2NrPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmJsb2NrTGlzdC5lYWNoT2JqZWN0KHQpfSxjLnByb3RvdHlwZS5lYWNoQmxvY2tBdFJhbmdlPWZ1bmN0aW9uKHQsZSl7dmFyIG4sbyxyLHMsYSx1LGMsbCxoLHAsZCxmO2lmKHU9dD1pKHQpLGQ9dVswXSxyPXVbMV0scD10aGlzLmxvY2F0aW9uRnJvbVBvc2l0aW9uKGQpLG89dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbihyKSxwLmluZGV4PT09by5pbmRleClyZXR1cm4gbj10aGlzLmdldEJsb2NrQXRJbmRleChwLmluZGV4KSxmPVtwLm9mZnNldCxvLm9mZnNldF0sZShuLGYscC5pbmRleCk7Zm9yKGg9W10sYT1zPWM9cC5pbmRleCxsPW8uaW5kZXg7bD49Yz9sPj1zOnM+PWw7YT1sPj1jPysrczotLXMpKG49dGhpcy5nZXRCbG9ja0F0SW5kZXgoYSkpPyhmPWZ1bmN0aW9uKCl7c3dpdGNoKGEpe2Nhc2UgcC5pbmRleDpyZXR1cm5bcC5vZmZzZXQsbi50ZXh0LmdldExlbmd0aCgpXTtjYXNlIG8uaW5kZXg6cmV0dXJuWzAsby5vZmZzZXRdO2RlZmF1bHQ6cmV0dXJuWzAsbi50ZXh0LmdldExlbmd0aCgpXX19KCksaC5wdXNoKGUobixmLGEpKSk6aC5wdXNoKHZvaWQgMCk7cmV0dXJuIGh9LGMucHJvdG90eXBlLmdldENvbW1vbkF0dHJpYnV0ZXNBdFJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBuLHIscztyZXR1cm4gcj0odD1pKHQpKVswXSxvKHQpP3RoaXMuZ2V0Q29tbW9uQXR0cmlidXRlc0F0UG9zaXRpb24ocik6KHM9W10sbj1bXSx0aGlzLmVhY2hCbG9ja0F0UmFuZ2UodCxmdW5jdGlvbih0LGUpe3JldHVybiBlWzBdIT09ZVsxXT8ocy5wdXNoKHQudGV4dC5nZXRDb21tb25BdHRyaWJ1dGVzQXRSYW5nZShlKSksbi5wdXNoKGwodCkpKTp2b2lkIDBcbn0pLGUuSGFzaC5mcm9tQ29tbW9uQXR0cmlidXRlc09mT2JqZWN0cyhzKS5tZXJnZShlLkhhc2guZnJvbUNvbW1vbkF0dHJpYnV0ZXNPZk9iamVjdHMobikpLnRvT2JqZWN0KCkpfSxjLnByb3RvdHlwZS5nZXRDb21tb25BdHRyaWJ1dGVzQXRQb3NpdGlvbj1mdW5jdGlvbih0KXt2YXIgbixpLG8scixzLGEsYyxoLHAsZDtpZihwPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24odCkscz1wLmluZGV4LGg9cC5vZmZzZXQsbz10aGlzLmdldEJsb2NrQXRJbmRleChzKSwhbylyZXR1cm57fTtyPWwobyksbj1vLnRleHQuZ2V0QXR0cmlidXRlc0F0UG9zaXRpb24oaCksaT1vLnRleHQuZ2V0QXR0cmlidXRlc0F0UG9zaXRpb24oaC0xKSxhPWZ1bmN0aW9uKCl7dmFyIHQsbjt0PWUuY29uZmlnLnRleHRBdHRyaWJ1dGVzLG49W107Zm9yKGMgaW4gdClkPXRbY10sZC5pbmhlcml0YWJsZSYmbi5wdXNoKGMpO3JldHVybiBufSgpO2ZvcihjIGluIGkpZD1pW2NdLChkPT09bltjXXx8dS5jYWxsKGEsYyk+PTApJiYocltjXT1kKTtyZXR1cm4gcn0sYy5wcm90b3R5cGUuZ2V0UmFuZ2VPZkNvbW1vbkF0dHJpYnV0ZUF0UG9zaXRpb249ZnVuY3Rpb24odCxlKXt2YXIgbixvLHIscyxhLHUsYyxsLGg7cmV0dXJuIGE9dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbihlKSxyPWEuaW5kZXgscz1hLm9mZnNldCxoPXRoaXMuZ2V0VGV4dEF0SW5kZXgociksdT1oLmdldEV4cGFuZGVkUmFuZ2VGb3JBdHRyaWJ1dGVBdE9mZnNldCh0LHMpLGw9dVswXSxvPXVbMV0sYz10aGlzLnBvc2l0aW9uRnJvbUxvY2F0aW9uKHtpbmRleDpyLG9mZnNldDpsfSksbj10aGlzLnBvc2l0aW9uRnJvbUxvY2F0aW9uKHtpbmRleDpyLG9mZnNldDpvfSksaShbYyxuXSl9LGMucHJvdG90eXBlLmdldEJhc2VCbG9ja0F0dHJpYnV0ZXM9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvLHIscztmb3IodD10aGlzLmdldEJsb2NrQXRJbmRleCgwKS5nZXRBdHRyaWJ1dGVzKCksbj1pPTEscz10aGlzLmdldEJsb2NrQ291bnQoKTtzPj0xP3M+aTppPnM7bj1zPj0xPysraTotLWkpZT10aGlzLmdldEJsb2NrQXRJbmRleChuKS5nZXRBdHRyaWJ1dGVzKCkscj1NYXRoLm1pbih0Lmxlbmd0aCxlLmxlbmd0aCksdD1mdW5jdGlvbigpe3ZhciBuLGkscztmb3Iocz1bXSxvPW49MCxpPXI7KGk+PTA/aT5uOm4+aSkmJmVbb109PT10W29dO289aT49MD8rK246LS1uKXMucHVzaChlW29dKTtyZXR1cm4gc30oKTtyZXR1cm4gdH0sbD1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiBuPXt9LChlPXQuZ2V0TGFzdEF0dHJpYnV0ZSgpKSYmKG5bZV09ITApLG59LGMucHJvdG90eXBlLmdldEF0dGFjaG1lbnRCeUlkPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvO2ZvcihvPXRoaXMuZ2V0QXR0YWNobWVudHMoKSxuPTAsaT1vLmxlbmd0aDtpPm47bisrKWlmKGU9b1tuXSxlLmlkPT09dClyZXR1cm4gZX0sYy5wcm90b3R5cGUuZ2V0QXR0YWNobWVudFBpZWNlcz1mdW5jdGlvbigpe3ZhciB0O3JldHVybiB0PVtdLHRoaXMuYmxvY2tMaXN0LmVhY2hPYmplY3QoZnVuY3Rpb24oZSl7dmFyIG47cmV0dXJuIG49ZS50ZXh0LHQ9dC5jb25jYXQobi5nZXRBdHRhY2htZW50UGllY2VzKCkpfSksdH0sYy5wcm90b3R5cGUuZ2V0QXR0YWNobWVudHM9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvO2ZvcihpPXRoaXMuZ2V0QXR0YWNobWVudFBpZWNlcygpLG89W10sdD0wLGU9aS5sZW5ndGg7ZT50O3QrKyluPWlbdF0sby5wdXNoKG4uYXR0YWNobWVudCk7cmV0dXJuIG99LGMucHJvdG90eXBlLmdldFJhbmdlT2ZBdHRhY2htZW50PWZ1bmN0aW9uKHQpe3ZhciBlLG4sbyxyLHMsYSx1O2ZvcihyPTAscz10aGlzLmJsb2NrTGlzdC50b0FycmF5KCksbj1lPTAsbz1zLmxlbmd0aDtvPmU7bj0rK2Upe2lmKGE9c1tuXS50ZXh0LHU9YS5nZXRSYW5nZU9mQXR0YWNobWVudCh0KSlyZXR1cm4gaShbcit1WzBdLHIrdVsxXV0pO3IrPWEuZ2V0TGVuZ3RoKCl9fSxjLnByb3RvdHlwZS5nZXRMb2NhdGlvblJhbmdlT2ZBdHRhY2htZW50PWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBlPXRoaXMuZ2V0UmFuZ2VPZkF0dGFjaG1lbnQodCksdGhpcy5sb2NhdGlvblJhbmdlRnJvbVJhbmdlKGUpfSxjLnByb3RvdHlwZS5nZXRBdHRhY2htZW50UGllY2VGb3JBdHRhY2htZW50PWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvO2ZvcihvPXRoaXMuZ2V0QXR0YWNobWVudFBpZWNlcygpLGU9MCxuPW8ubGVuZ3RoO24+ZTtlKyspaWYoaT1vW2VdLGkuYXR0YWNobWVudD09PXQpcmV0dXJuIGl9LGMucHJvdG90eXBlLmZpbmRSYW5nZXNGb3JCbG9ja0F0dHJpYnV0ZT1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxyLHMsYTtmb3Iocj0wLHM9W10sYT10aGlzLmdldEJsb2NrcygpLG49MCxpPWEubGVuZ3RoO2k+bjtuKyspZT1hW25dLG89ZS5nZXRMZW5ndGgoKSxlLmhhc0F0dHJpYnV0ZSh0KSYmcy5wdXNoKFtyLHIrb10pLHIrPW87cmV0dXJuIHN9LGMucHJvdG90eXBlLmZpbmRSYW5nZXNGb3JUZXh0QXR0cmlidXRlPWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvLHIscyxhLHUsYyxsLGg7Zm9yKGg9KG51bGwhPWU/ZTp7fSkud2l0aFZhbHVlLGE9MCx1PVtdLGM9W10scj1mdW5jdGlvbihlKXtyZXR1cm4gbnVsbCE9aD9lLmdldEF0dHJpYnV0ZSh0KT09PWg6ZS5oYXNBdHRyaWJ1dGUodCl9LGw9dGhpcy5nZXRQaWVjZXMoKSxuPTAsaT1sLmxlbmd0aDtpPm47bisrKXM9bFtuXSxvPXMuZ2V0TGVuZ3RoKCkscihzKSYmKHVbMV09PT1hP3VbMV09YStvOmMucHVzaCh1PVthLGErb10pKSxhKz1vO3JldHVybiBjfSxjLnByb3RvdHlwZS5sb2NhdGlvbkZyb21Qb3NpdGlvbj1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiBuPXRoaXMuYmxvY2tMaXN0LmZpbmRJbmRleEFuZE9mZnNldEF0UG9zaXRpb24oTWF0aC5tYXgoMCx0KSksbnVsbCE9bi5pbmRleD9uOihlPXRoaXMuZ2V0QmxvY2tzKCkse2luZGV4OmUubGVuZ3RoLTEsb2Zmc2V0OmVbZS5sZW5ndGgtMV0uZ2V0TGVuZ3RoKCl9KX0sYy5wcm90b3R5cGUucG9zaXRpb25Gcm9tTG9jYXRpb249ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuYmxvY2tMaXN0LmZpbmRQb3NpdGlvbkF0SW5kZXhBbmRPZmZzZXQodC5pbmRleCx0Lm9mZnNldCl9LGMucHJvdG90eXBlLmxvY2F0aW9uUmFuZ2VGcm9tUG9zaXRpb249ZnVuY3Rpb24odCl7cmV0dXJuIGkodGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbih0KSl9LGMucHJvdG90eXBlLmxvY2F0aW9uUmFuZ2VGcm9tUmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGUsbixvLHI7aWYodD1pKHQpKXJldHVybiByPXRbMF0sbj10WzFdLG89dGhpcy5sb2NhdGlvbkZyb21Qb3NpdGlvbihyKSxlPXRoaXMubG9jYXRpb25Gcm9tUG9zaXRpb24obiksaShbbyxlXSl9LGMucHJvdG90eXBlLnJhbmdlRnJvbUxvY2F0aW9uUmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gdD1pKHQpLGU9dGhpcy5wb3NpdGlvbkZyb21Mb2NhdGlvbih0WzBdKSxvKHQpfHwobj10aGlzLnBvc2l0aW9uRnJvbUxvY2F0aW9uKHRbMV0pKSxpKFtlLG5dKX0sYy5wcm90b3R5cGUuaXNFcXVhbFRvPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmJsb2NrTGlzdC5pc0VxdWFsVG8obnVsbCE9dD90LmJsb2NrTGlzdDp2b2lkIDApfSxjLnByb3RvdHlwZS5nZXRUZXh0cz1mdW5jdGlvbigpe3ZhciB0LGUsbixpLG87Zm9yKGk9dGhpcy5nZXRCbG9ja3MoKSxvPVtdLGU9MCxuPWkubGVuZ3RoO24+ZTtlKyspdD1pW2VdLG8ucHVzaCh0LnRleHQpO3JldHVybiBvfSxjLnByb3RvdHlwZS5nZXRQaWVjZXM9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvO2ZvcihuPVtdLGk9dGhpcy5nZXRUZXh0cygpLHQ9MCxlPWkubGVuZ3RoO2U+dDt0Kyspbz1pW3RdLG4ucHVzaC5hcHBseShuLG8uZ2V0UGllY2VzKCkpO3JldHVybiBufSxjLnByb3RvdHlwZS5nZXRPYmplY3RzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZ2V0QmxvY2tzKCkuY29uY2F0KHRoaXMuZ2V0VGV4dHMoKSkuY29uY2F0KHRoaXMuZ2V0UGllY2VzKCkpfSxjLnByb3RvdHlwZS50b1NlcmlhbGl6YWJsZURvY3VtZW50PWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIHQ9W10sdGhpcy5ibG9ja0xpc3QuZWFjaE9iamVjdChmdW5jdGlvbihlKXtyZXR1cm4gdC5wdXNoKGUuY29weVdpdGhUZXh0KGUudGV4dC50b1NlcmlhbGl6YWJsZVRleHQoKSkpfSksbmV3IHRoaXMuY29uc3RydWN0b3IodCl9LGMucHJvdG90eXBlLnRvU3RyaW5nPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYmxvY2tMaXN0LnRvU3RyaW5nKCl9LGMucHJvdG90eXBlLnRvSlNPTj1mdW5jdGlvbigpe3JldHVybiB0aGlzLmJsb2NrTGlzdC50b0pTT04oKX0sYy5wcm90b3R5cGUudG9Db25zb2xlPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIEpTT04uc3RyaW5naWZ5KGZ1bmN0aW9uKCl7dmFyIGUsbixpLG87Zm9yKGk9dGhpcy5ibG9ja0xpc3QudG9BcnJheSgpLG89W10sZT0wLG49aS5sZW5ndGg7bj5lO2UrKyl0PWlbZV0sby5wdXNoKEpTT04ucGFyc2UodC50ZXh0LnRvQ29uc29sZSgpKSk7cmV0dXJuIG99LmNhbGwodGhpcykpfSxjfShlLk9iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXtlLkxpbmVCcmVha0luc2VydGlvbj1mdW5jdGlvbigpe2Z1bmN0aW9uIHQodCl7dmFyIGU7dGhpcy5jb21wb3NpdGlvbj10LHRoaXMuZG9jdW1lbnQ9dGhpcy5jb21wb3NpdGlvbi5kb2N1bWVudCxlPXRoaXMuY29tcG9zaXRpb24uZ2V0U2VsZWN0ZWRSYW5nZSgpLHRoaXMuc3RhcnRQb3NpdGlvbj1lWzBdLHRoaXMuZW5kUG9zaXRpb249ZVsxXSx0aGlzLnN0YXJ0TG9jYXRpb249dGhpcy5kb2N1bWVudC5sb2NhdGlvbkZyb21Qb3NpdGlvbih0aGlzLnN0YXJ0UG9zaXRpb24pLHRoaXMuZW5kTG9jYXRpb249dGhpcy5kb2N1bWVudC5sb2NhdGlvbkZyb21Qb3NpdGlvbih0aGlzLmVuZFBvc2l0aW9uKSx0aGlzLmJsb2NrPXRoaXMuZG9jdW1lbnQuZ2V0QmxvY2tBdEluZGV4KHRoaXMuZW5kTG9jYXRpb24uaW5kZXgpLHRoaXMuYnJlYWtzT25SZXR1cm49dGhpcy5ibG9jay5icmVha3NPblJldHVybigpLHRoaXMucHJldmlvdXNDaGFyYWN0ZXI9dGhpcy5ibG9jay50ZXh0LmdldFN0cmluZ0F0UG9zaXRpb24odGhpcy5lbmRMb2NhdGlvbi5vZmZzZXQtMSksdGhpcy5uZXh0Q2hhcmFjdGVyPXRoaXMuYmxvY2sudGV4dC5nZXRTdHJpbmdBdFBvc2l0aW9uKHRoaXMuZW5kTG9jYXRpb24ub2Zmc2V0KX1yZXR1cm4gdC5wcm90b3R5cGUuc2hvdWxkSW5zZXJ0QmxvY2tCcmVhaz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmJsb2NrLmhhc0F0dHJpYnV0ZXMoKSYmdGhpcy5ibG9jay5pc0xpc3RJdGVtKCkmJiF0aGlzLmJsb2NrLmlzRW1wdHkoKT8wIT09dGhpcy5zdGFydExvY2F0aW9uLm9mZnNldDp0aGlzLmJyZWFrc09uUmV0dXJuJiZcIlxcblwiIT09dGhpcy5uZXh0Q2hhcmFjdGVyfSx0LnByb3RvdHlwZS5zaG91bGRCcmVha0Zvcm1hdHRlZEJsb2NrPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYmxvY2suaGFzQXR0cmlidXRlcygpJiYhdGhpcy5ibG9jay5pc0xpc3RJdGVtKCkmJih0aGlzLmJyZWFrc09uUmV0dXJuJiZcIlxcblwiPT09dGhpcy5uZXh0Q2hhcmFjdGVyfHxcIlxcblwiPT09dGhpcy5wcmV2aW91c0NoYXJhY3Rlcil9LHQucHJvdG90eXBlLnNob3VsZERlY3JlYXNlTGlzdExldmVsPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYmxvY2suaGFzQXR0cmlidXRlcygpJiZ0aGlzLmJsb2NrLmlzTGlzdEl0ZW0oKSYmdGhpcy5ibG9jay5pc0VtcHR5KCl9LHQucHJvdG90eXBlLnNob3VsZFByZXBlbmRMaXN0SXRlbT1mdW5jdGlvbigpe3JldHVybiB0aGlzLmJsb2NrLmlzTGlzdEl0ZW0oKSYmMD09PXRoaXMuc3RhcnRMb2NhdGlvbi5vZmZzZXQmJiF0aGlzLmJsb2NrLmlzRW1wdHkoKX0sdC5wcm90b3R5cGUuc2hvdWxkUmVtb3ZlTGFzdEJsb2NrQXR0cmlidXRlPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYmxvY2suaGFzQXR0cmlidXRlcygpJiYhdGhpcy5ibG9jay5pc0xpc3RJdGVtKCkmJnRoaXMuYmxvY2suaXNFbXB0eSgpfSx0fSgpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scixzLGEsdSxjLGwsaD1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpcC5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxwPXt9Lmhhc093blByb3BlcnR5O3M9ZS5ub3JtYWxpemVSYW5nZSxjPWUucmFuZ2VzQXJlRXF1YWwsdT1lLnJhbmdlSXNDb2xsYXBzZWQsYT1lLm9iamVjdHNBcmVFcXVhbCx0PWUuYXJyYXlTdGFydHNXaXRoLGw9ZS5zdW1tYXJpemVBcnJheUNoYW5nZSxpPWUuZ2V0QWxsQXR0cmlidXRlTmFtZXMsbz1lLmdldEJsb2NrQ29uZmlnLHI9ZS5nZXRUZXh0Q29uZmlnLG49ZS5leHRlbmQsZS5Db21wb3NpdGlvbj1mdW5jdGlvbihwKXtmdW5jdGlvbiBkKCl7dGhpcy5kb2N1bWVudD1uZXcgZS5Eb2N1bWVudCx0aGlzLmF0dGFjaG1lbnRzPVtdLHRoaXMuY3VycmVudEF0dHJpYnV0ZXM9e30sdGhpcy5yZXZpc2lvbj0wfXZhciBmO3JldHVybiBoKGQscCksZC5wcm90b3R5cGUuc2V0RG9jdW1lbnQ9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHQuaXNFcXVhbFRvKHRoaXMuZG9jdW1lbnQpP3ZvaWQgMDoodGhpcy5kb2N1bWVudD10LHRoaXMucmVmcmVzaEF0dGFjaG1lbnRzKCksdGhpcy5yZXZpc2lvbisrLG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBlLmNvbXBvc2l0aW9uRGlkQ2hhbmdlRG9jdW1lbnQ/ZS5jb21wb3NpdGlvbkRpZENoYW5nZURvY3VtZW50KHQpOnZvaWQgMCl9LGQucHJvdG90eXBlLmdldFNuYXBzaG90PWZ1bmN0aW9uKCl7cmV0dXJue2RvY3VtZW50OnRoaXMuZG9jdW1lbnQsc2VsZWN0ZWRSYW5nZTp0aGlzLmdldFNlbGVjdGVkUmFuZ2UoKX19LGQucHJvdG90eXBlLmxvYWRTbmFwc2hvdD1mdW5jdGlvbih0KXt2YXIgbixpLG8scjtyZXR1cm4gbj10LmRvY3VtZW50LHI9dC5zZWxlY3RlZFJhbmdlLG51bGwhPShpPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBpLmNvbXBvc2l0aW9uV2lsbExvYWRTbmFwc2hvdCYmaS5jb21wb3NpdGlvbldpbGxMb2FkU25hcHNob3QoKSx0aGlzLnNldERvY3VtZW50KG51bGwhPW4/bjpuZXcgZS5Eb2N1bWVudCksdGhpcy5zZXRTZWxlY3Rpb24obnVsbCE9cj9yOlswLDBdKSxudWxsIT0obz10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2Ygby5jb21wb3NpdGlvbkRpZExvYWRTbmFwc2hvdD9vLmNvbXBvc2l0aW9uRGlkTG9hZFNuYXBzaG90KCk6dm9pZCAwfSxkLnByb3RvdHlwZS5pbnNlcnRUZXh0PWZ1bmN0aW9uKHQsZSl7dmFyIG4saSxvLHI7cmV0dXJuIHI9KG51bGwhPWU/ZTp7dXBkYXRlUG9zaXRpb246ITB9KS51cGRhdGVQb3NpdGlvbixpPXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpLHRoaXMuc2V0RG9jdW1lbnQodGhpcy5kb2N1bWVudC5pbnNlcnRUZXh0QXRSYW5nZSh0LGkpKSxvPWlbMF0sbj1vK3QuZ2V0TGVuZ3RoKCksciYmdGhpcy5zZXRTZWxlY3Rpb24obiksdGhpcy5ub3RpZnlEZWxlZ2F0ZU9mSW5zZXJ0aW9uQXRSYW5nZShbbyxuXSl9LGQucHJvdG90eXBlLmluc2VydEJsb2NrPWZ1bmN0aW9uKHQpe3ZhciBuO3JldHVybiBudWxsPT10JiYodD1uZXcgZS5CbG9jayksbj1uZXcgZS5Eb2N1bWVudChbdF0pLHRoaXMuaW5zZXJ0RG9jdW1lbnQobil9LGQucHJvdG90eXBlLmluc2VydERvY3VtZW50PWZ1bmN0aW9uKHQpe3ZhciBuLGksbztyZXR1cm4gbnVsbD09dCYmKHQ9bmV3IGUuRG9jdW1lbnQpLGk9dGhpcy5nZXRTZWxlY3RlZFJhbmdlKCksdGhpcy5zZXREb2N1bWVudCh0aGlzLmRvY3VtZW50Lmluc2VydERvY3VtZW50QXRSYW5nZSh0LGkpKSxvPWlbMF0sbj1vK3QuZ2V0TGVuZ3RoKCksdGhpcy5zZXRTZWxlY3Rpb24obiksdGhpcy5ub3RpZnlEZWxlZ2F0ZU9mSW5zZXJ0aW9uQXRSYW5nZShbbyxuXSl9LGQucHJvdG90eXBlLmluc2VydFN0cmluZz1mdW5jdGlvbih0LG4pe3ZhciBpLG87cmV0dXJuIGk9dGhpcy5nZXRDdXJyZW50VGV4dEF0dHJpYnV0ZXMoKSxvPWUuVGV4dC50ZXh0Rm9yU3RyaW5nV2l0aEF0dHJpYnV0ZXModCxpKSx0aGlzLmluc2VydFRleHQobyxuKX0sZC5wcm90b3R5cGUuaW5zZXJ0QmxvY2tCcmVhaz1mdW5jdGlvbigpe3ZhciB0LGUsbjtyZXR1cm4gZT10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSx0aGlzLnNldERvY3VtZW50KHRoaXMuZG9jdW1lbnQuaW5zZXJ0QmxvY2tCcmVha0F0UmFuZ2UoZSkpLG49ZVswXSx0PW4rMSx0aGlzLnNldFNlbGVjdGlvbih0KSx0aGlzLm5vdGlmeURlbGVnYXRlT2ZJbnNlcnRpb25BdFJhbmdlKFtuLHRdKX0sZC5wcm90b3R5cGUuaW5zZXJ0TGluZUJyZWFrPWZ1bmN0aW9uKCl7dmFyIHQsbjtyZXR1cm4gbj1uZXcgZS5MaW5lQnJlYWtJbnNlcnRpb24odGhpcyksbi5zaG91bGREZWNyZWFzZUxpc3RMZXZlbCgpPyh0aGlzLmRlY3JlYXNlTGlzdExldmVsKCksdGhpcy5zZXRTZWxlY3Rpb24obi5zdGFydFBvc2l0aW9uKSk6bi5zaG91bGRQcmVwZW5kTGlzdEl0ZW0oKT8odD1uZXcgZS5Eb2N1bWVudChbbi5ibG9jay5jb3B5V2l0aG91dFRleHQoKV0pLHRoaXMuaW5zZXJ0RG9jdW1lbnQodCkpOm4uc2hvdWxkSW5zZXJ0QmxvY2tCcmVhaygpP3RoaXMuaW5zZXJ0QmxvY2tCcmVhaygpOm4uc2hvdWxkUmVtb3ZlTGFzdEJsb2NrQXR0cmlidXRlKCk/dGhpcy5yZW1vdmVMYXN0QmxvY2tBdHRyaWJ1dGUoKTpuLnNob3VsZEJyZWFrRm9ybWF0dGVkQmxvY2soKT90aGlzLmJyZWFrRm9ybWF0dGVkQmxvY2sobik6dGhpcy5pbnNlcnRTdHJpbmcoXCJcXG5cIil9LGQucHJvdG90eXBlLmluc2VydEhUTUw9ZnVuY3Rpb24odCl7dmFyIG4saSxvLHI7cmV0dXJuIG49ZS5Eb2N1bWVudC5mcm9tSFRNTCh0KSxvPXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpLHRoaXMuc2V0RG9jdW1lbnQodGhpcy5kb2N1bWVudC5tZXJnZURvY3VtZW50QXRSYW5nZShuLG8pKSxyPW9bMF0saT1yK24uZ2V0TGVuZ3RoKCktMSx0aGlzLnNldFNlbGVjdGlvbihpKSx0aGlzLm5vdGlmeURlbGVnYXRlT2ZJbnNlcnRpb25BdFJhbmdlKFtyLGldKX0sZC5wcm90b3R5cGUucmVwbGFjZUhUTUw9ZnVuY3Rpb24odCl7dmFyIG4saSxvO3JldHVybiBuPWUuRG9jdW1lbnQuZnJvbUhUTUwodCkuY29weVVzaW5nT2JqZWN0c0Zyb21Eb2N1bWVudCh0aGlzLmRvY3VtZW50KSxpPXRoaXMuZ2V0TG9jYXRpb25SYW5nZSh7c3RyaWN0OiExfSksbz10aGlzLmRvY3VtZW50LnJhbmdlRnJvbUxvY2F0aW9uUmFuZ2UoaSksdGhpcy5zZXREb2N1bWVudChuKSx0aGlzLnNldFNlbGVjdGlvbihvKX0sZC5wcm90b3R5cGUuaW5zZXJ0RmlsZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5pbnNlcnRGaWxlcyhbdF0pfSxkLnByb3RvdHlwZS5pbnNlcnRGaWxlcz1mdW5jdGlvbih0KXt2YXIgbixpLG8scixzLGE7Zm9yKGk9W10scj0wLHM9dC5sZW5ndGg7cz5yO3IrKylvPXRbcl0sKG51bGwhPShhPXRoaXMuZGVsZWdhdGUpP2EuY29tcG9zaXRpb25TaG91bGRBY2NlcHRGaWxlKG8pOnZvaWQgMCkmJihuPWUuQXR0YWNobWVudC5hdHRhY2htZW50Rm9yRmlsZShvKSxpLnB1c2gobikpO3JldHVybiB0aGlzLmluc2VydEF0dGFjaG1lbnRzKGkpfSxkLnByb3RvdHlwZS5pbnNlcnRBdHRhY2htZW50PWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmluc2VydEF0dGFjaG1lbnRzKFt0XSl9LGQucHJvdG90eXBlLmluc2VydEF0dGFjaG1lbnRzPWZ1bmN0aW9uKHQpe3ZhciBuLGksbyxyLHMsYSx1LGMsbDtmb3IoYz1uZXcgZS5UZXh0LHI9MCxzPXQubGVuZ3RoO3M+cjtyKyspbj10W3JdLGw9bi5nZXRUeXBlKCksYT1udWxsIT0odT1lLmNvbmZpZy5hdHRhY2htZW50c1tsXSk/dS5wcmVzZW50YXRpb246dm9pZCAwLG89dGhpcy5nZXRDdXJyZW50VGV4dEF0dHJpYnV0ZXMoKSxhJiYoby5wcmVzZW50YXRpb249YSksaT1lLlRleHQudGV4dEZvckF0dGFjaG1lbnRXaXRoQXR0cmlidXRlcyhuLG8pLGM9Yy5hcHBlbmRUZXh0KGkpO3JldHVybiB0aGlzLmluc2VydFRleHQoYyl9LGQucHJvdG90eXBlLnNob3VsZE1hbmFnZURlbGV0aW5nSW5EaXJlY3Rpb249ZnVuY3Rpb24odCl7dmFyIGU7aWYoZT10aGlzLmdldExvY2F0aW9uUmFuZ2UoKSx1KGUpKXtpZihcImJhY2t3YXJkXCI9PT10JiYwPT09ZVswXS5vZmZzZXQpcmV0dXJuITA7aWYodGhpcy5zaG91bGRNYW5hZ2VNb3ZpbmdDdXJzb3JJbkRpcmVjdGlvbih0KSlyZXR1cm4hMH1lbHNlIGlmKGVbMF0uaW5kZXghPT1lWzFdLmluZGV4KXJldHVybiEwO3JldHVybiExfSxkLnByb3RvdHlwZS5kZWxldGVJbkRpcmVjdGlvbj1mdW5jdGlvbih0LGUpe3ZhciBuLGksbyxyLHMsYSxjLGw7cmV0dXJuIHI9KG51bGwhPWU/ZTp7fSkubGVuZ3RoLHM9dGhpcy5nZXRMb2NhdGlvblJhbmdlKCksYT10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSxjPXUoYSksYz9vPVwiYmFja3dhcmRcIj09PXQmJjA9PT1zWzBdLm9mZnNldDpsPXNbMF0uaW5kZXghPT1zWzFdLmluZGV4LG8mJnRoaXMuY2FuRGVjcmVhc2VCbG9ja0F0dHJpYnV0ZUxldmVsKCkmJihpPXRoaXMuZ2V0QmxvY2soKSxpLmlzTGlzdEl0ZW0oKT90aGlzLmRlY3JlYXNlTGlzdExldmVsKCk6dGhpcy5kZWNyZWFzZUJsb2NrQXR0cmlidXRlTGV2ZWwoKSx0aGlzLnNldFNlbGVjdGlvbihhWzBdKSxpLmlzRW1wdHkoKSk/ITE6KGMmJihhPXRoaXMuZ2V0RXhwYW5kZWRSYW5nZUluRGlyZWN0aW9uKHQse2xlbmd0aDpyfSksXCJiYWNrd2FyZFwiPT09dCYmKG49dGhpcy5nZXRBdHRhY2htZW50QXRSYW5nZShhKSkpLG4/KHRoaXMuZWRpdEF0dGFjaG1lbnQobiksITEpOih0aGlzLnNldERvY3VtZW50KHRoaXMuZG9jdW1lbnQucmVtb3ZlVGV4dEF0UmFuZ2UoYSkpLHRoaXMuc2V0U2VsZWN0aW9uKGFbMF0pLG98fGw/ITE6dm9pZCAwKSl9LGQucHJvdG90eXBlLm1vdmVUZXh0RnJvbVJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBlPXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpWzBdLHRoaXMuc2V0RG9jdW1lbnQodGhpcy5kb2N1bWVudC5tb3ZlVGV4dEZyb21SYW5nZVRvUG9zaXRpb24odCxlKSksdGhpcy5zZXRTZWxlY3Rpb24oZSl9LGQucHJvdG90eXBlLnJlbW92ZUF0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuKGU9dGhpcy5kb2N1bWVudC5nZXRSYW5nZU9mQXR0YWNobWVudCh0KSk/KHRoaXMuc3RvcEVkaXRpbmdBdHRhY2htZW50KCksdGhpcy5zZXREb2N1bWVudCh0aGlzLmRvY3VtZW50LnJlbW92ZVRleHRBdFJhbmdlKGUpKSx0aGlzLnNldFNlbGVjdGlvbihlWzBdKSk6dm9pZCAwfSxkLnByb3RvdHlwZS5yZW1vdmVMYXN0QmxvY2tBdHRyaWJ1dGU9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saTtyZXR1cm4gbj10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSxpPW5bMF0sZT1uWzFdLHQ9dGhpcy5kb2N1bWVudC5nZXRCbG9ja0F0UG9zaXRpb24oZSksdGhpcy5yZW1vdmVDdXJyZW50QXR0cmlidXRlKHQuZ2V0TGFzdEF0dHJpYnV0ZSgpKSx0aGlzLnNldFNlbGVjdGlvbihpKX0sZj1cIiBcIixkLnByb3RvdHlwZS5pbnNlcnRQbGFjZWhvbGRlcj1mdW5jdGlvbigpe3JldHVybiB0aGlzLnBsYWNlaG9sZGVyUG9zaXRpb249dGhpcy5nZXRQb3NpdGlvbigpLHRoaXMuaW5zZXJ0U3RyaW5nKGYpfSxkLnByb3RvdHlwZS5zZWxlY3RQbGFjZWhvbGRlcj1mdW5jdGlvbigpe3JldHVybiBudWxsIT10aGlzLnBsYWNlaG9sZGVyUG9zaXRpb24/KHRoaXMuc2V0U2VsZWN0ZWRSYW5nZShbdGhpcy5wbGFjZWhvbGRlclBvc2l0aW9uLHRoaXMucGxhY2Vob2xkZXJQb3NpdGlvbitmLmxlbmd0aF0pLHRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpKTp2b2lkIDB9LGQucHJvdG90eXBlLmZvcmdldFBsYWNlaG9sZGVyPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMucGxhY2Vob2xkZXJQb3NpdGlvbj1udWxsfSxkLnByb3RvdHlwZS5oYXNDdXJyZW50QXR0cmlidXRlPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBlPXRoaXMuY3VycmVudEF0dHJpYnV0ZXNbdF0sbnVsbCE9ZSYmZSE9PSExfSxkLnByb3RvdHlwZS50b2dnbGVDdXJyZW50QXR0cmlidXRlPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybihlPSF0aGlzLmN1cnJlbnRBdHRyaWJ1dGVzW3RdKT90aGlzLnNldEN1cnJlbnRBdHRyaWJ1dGUodCxlKTp0aGlzLnJlbW92ZUN1cnJlbnRBdHRyaWJ1dGUodCl9LGQucHJvdG90eXBlLmNhblNldEN1cnJlbnRBdHRyaWJ1dGU9ZnVuY3Rpb24odCl7cmV0dXJuIG8odCk/dGhpcy5jYW5TZXRDdXJyZW50QmxvY2tBdHRyaWJ1dGUodCk6dGhpcy5jYW5TZXRDdXJyZW50VGV4dEF0dHJpYnV0ZSh0KX0sZC5wcm90b3R5cGUuY2FuU2V0Q3VycmVudFRleHRBdHRyaWJ1dGU9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvO2lmKGU9dGhpcy5nZXRTZWxlY3RlZERvY3VtZW50KCkpe2ZvcihvPWUuZ2V0QXR0YWNobWVudHMoKSxuPTAsaT1vLmxlbmd0aDtpPm47bisrKWlmKHQ9b1tuXSwhdC5oYXNDb250ZW50KCkpcmV0dXJuITE7cmV0dXJuITB9fSxkLnByb3RvdHlwZS5jYW5TZXRDdXJyZW50QmxvY2tBdHRyaWJ1dGU9ZnVuY3Rpb24oKXt2YXIgdDtpZih0PXRoaXMuZ2V0QmxvY2soKSlyZXR1cm4hdC5pc1Rlcm1pbmFsQmxvY2soKX0sZC5wcm90b3R5cGUuc2V0Q3VycmVudEF0dHJpYnV0ZT1mdW5jdGlvbih0LGUpe3JldHVybiBvKHQpP3RoaXMuc2V0QmxvY2tBdHRyaWJ1dGUodCxlKToodGhpcy5zZXRUZXh0QXR0cmlidXRlKHQsZSksdGhpcy5jdXJyZW50QXR0cmlidXRlc1t0XT1lLHRoaXMubm90aWZ5RGVsZWdhdGVPZkN1cnJlbnRBdHRyaWJ1dGVzQ2hhbmdlKCkpfSxkLnByb3RvdHlwZS5zZXRUZXh0QXR0cmlidXRlPWZ1bmN0aW9uKHQsbil7dmFyIGksbyxyLHM7aWYobz10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSlyZXR1cm4gcj1vWzBdLGk9b1sxXSxyIT09aT90aGlzLnNldERvY3VtZW50KHRoaXMuZG9jdW1lbnQuYWRkQXR0cmlidXRlQXRSYW5nZSh0LG4sbykpOlwiaHJlZlwiPT09dD8ocz1lLlRleHQudGV4dEZvclN0cmluZ1dpdGhBdHRyaWJ1dGVzKG4se2hyZWY6bn0pLHRoaXMuaW5zZXJ0VGV4dChzKSk6dm9pZCAwfSxkLnByb3RvdHlwZS5zZXRCbG9ja0F0dHJpYnV0ZT1mdW5jdGlvbih0LGUpe3ZhciBuLGk7aWYoaT10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSlyZXR1cm4gdGhpcy5jYW5TZXRDdXJyZW50QXR0cmlidXRlKHQpPyhuPXRoaXMuZ2V0QmxvY2soKSx0aGlzLnNldERvY3VtZW50KHRoaXMuZG9jdW1lbnQuYXBwbHlCbG9ja0F0dHJpYnV0ZUF0UmFuZ2UodCxlLGkpKSx0aGlzLnNldFNlbGVjdGlvbihpKSk6dm9pZCAwfSxkLnByb3RvdHlwZS5yZW1vdmVDdXJyZW50QXR0cmlidXRlPWZ1bmN0aW9uKHQpe3JldHVybiBvKHQpPyh0aGlzLnJlbW92ZUJsb2NrQXR0cmlidXRlKHQpLHRoaXMudXBkYXRlQ3VycmVudEF0dHJpYnV0ZXMoKSk6KHRoaXMucmVtb3ZlVGV4dEF0dHJpYnV0ZSh0KSxkZWxldGUgdGhpcy5jdXJyZW50QXR0cmlidXRlc1t0XSx0aGlzLm5vdGlmeURlbGVnYXRlT2ZDdXJyZW50QXR0cmlidXRlc0NoYW5nZSgpKX0sZC5wcm90b3R5cGUucmVtb3ZlVGV4dEF0dHJpYnV0ZT1mdW5jdGlvbih0KXt2YXIgZTtpZihlPXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpKXJldHVybiB0aGlzLnNldERvY3VtZW50KHRoaXMuZG9jdW1lbnQucmVtb3ZlQXR0cmlidXRlQXRSYW5nZSh0LGUpKX0sZC5wcm90b3R5cGUucmVtb3ZlQmxvY2tBdHRyaWJ1dGU9ZnVuY3Rpb24odCl7dmFyIGU7aWYoZT10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSlyZXR1cm4gdGhpcy5zZXREb2N1bWVudCh0aGlzLmRvY3VtZW50LnJlbW92ZUF0dHJpYnV0ZUF0UmFuZ2UodCxlKSl9LGQucHJvdG90eXBlLmNhbkRlY3JlYXNlTmVzdGluZ0xldmVsPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKG51bGwhPSh0PXRoaXMuZ2V0QmxvY2soKSk/dC5nZXROZXN0aW5nTGV2ZWwoKTp2b2lkIDApPjB9LGQucHJvdG90eXBlLmNhbkluY3JlYXNlTmVzdGluZ0xldmVsPWZ1bmN0aW9uKCl7dmFyIGUsbixpO2lmKGU9dGhpcy5nZXRCbG9jaygpKXJldHVybihudWxsIT0oaT1vKGUuZ2V0TGFzdE5lc3RhYmxlQXR0cmlidXRlKCkpKT9pLmxpc3RBdHRyaWJ1dGU6MCk/KG49dGhpcy5nZXRQcmV2aW91c0Jsb2NrKCkpP3Qobi5nZXRMaXN0SXRlbUF0dHJpYnV0ZXMoKSxlLmdldExpc3RJdGVtQXR0cmlidXRlcygpKTp2b2lkIDA6ZS5nZXROZXN0aW5nTGV2ZWwoKT4wfSxkLnByb3RvdHlwZS5kZWNyZWFzZU5lc3RpbmdMZXZlbD1mdW5jdGlvbigpe3ZhciB0O2lmKHQ9dGhpcy5nZXRCbG9jaygpKXJldHVybiB0aGlzLnNldERvY3VtZW50KHRoaXMuZG9jdW1lbnQucmVwbGFjZUJsb2NrKHQsdC5kZWNyZWFzZU5lc3RpbmdMZXZlbCgpKSl9LGQucHJvdG90eXBlLmluY3JlYXNlTmVzdGluZ0xldmVsPWZ1bmN0aW9uKCl7dmFyIHQ7aWYodD10aGlzLmdldEJsb2NrKCkpcmV0dXJuIHRoaXMuc2V0RG9jdW1lbnQodGhpcy5kb2N1bWVudC5yZXBsYWNlQmxvY2sodCx0LmluY3JlYXNlTmVzdGluZ0xldmVsKCkpKX0sZC5wcm90b3R5cGUuY2FuRGVjcmVhc2VCbG9ja0F0dHJpYnV0ZUxldmVsPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKG51bGwhPSh0PXRoaXMuZ2V0QmxvY2soKSk/dC5nZXRBdHRyaWJ1dGVMZXZlbCgpOnZvaWQgMCk+MH0sZC5wcm90b3R5cGUuZGVjcmVhc2VCbG9ja0F0dHJpYnV0ZUxldmVsPWZ1bmN0aW9uKCl7dmFyIHQsZTtyZXR1cm4odD1udWxsIT0oZT10aGlzLmdldEJsb2NrKCkpP2UuZ2V0TGFzdEF0dHJpYnV0ZSgpOnZvaWQgMCk/dGhpcy5yZW1vdmVDdXJyZW50QXR0cmlidXRlKHQpOnZvaWQgMH0sZC5wcm90b3R5cGUuZGVjcmVhc2VMaXN0TGV2ZWw9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saSxvLHI7Zm9yKHI9dGhpcy5nZXRTZWxlY3RlZFJhbmdlKClbMF0sbz10aGlzLmRvY3VtZW50LmxvY2F0aW9uRnJvbVBvc2l0aW9uKHIpLmluZGV4LG49byx0PXRoaXMuZ2V0QmxvY2soKS5nZXRBdHRyaWJ1dGVMZXZlbCgpOyhlPXRoaXMuZG9jdW1lbnQuZ2V0QmxvY2tBdEluZGV4KG4rMSkpJiZlLmlzTGlzdEl0ZW0oKSYmZS5nZXRBdHRyaWJ1dGVMZXZlbCgpPnQ7KW4rKztyZXR1cm4gcj10aGlzLmRvY3VtZW50LnBvc2l0aW9uRnJvbUxvY2F0aW9uKHtpbmRleDpvLG9mZnNldDowfSksaT10aGlzLmRvY3VtZW50LnBvc2l0aW9uRnJvbUxvY2F0aW9uKHtpbmRleDpuLG9mZnNldDowfSksdGhpcy5zZXREb2N1bWVudCh0aGlzLmRvY3VtZW50LnJlbW92ZUxhc3RMaXN0QXR0cmlidXRlQXRSYW5nZShbcixpXSkpfSxkLnByb3RvdHlwZS51cGRhdGVDdXJyZW50QXR0cmlidXRlcz1mdW5jdGlvbigpe3ZhciB0LGUsbixvLHIscztpZihzPXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSh7aWdub3JlTG9jazohMH0pKXtmb3IoZT10aGlzLmRvY3VtZW50LmdldENvbW1vbkF0dHJpYnV0ZXNBdFJhbmdlKHMpLHI9aSgpLG49MCxvPXIubGVuZ3RoO28+bjtuKyspdD1yW25dLGVbdF18fHRoaXMuY2FuU2V0Q3VycmVudEF0dHJpYnV0ZSh0KXx8KGVbdF09ITEpO2lmKCFhKGUsdGhpcy5jdXJyZW50QXR0cmlidXRlcykpcmV0dXJuIHRoaXMuY3VycmVudEF0dHJpYnV0ZXM9ZSx0aGlzLm5vdGlmeURlbGVnYXRlT2ZDdXJyZW50QXR0cmlidXRlc0NoYW5nZSgpfX0sZC5wcm90b3R5cGUuZ2V0Q3VycmVudEF0dHJpYnV0ZXM9ZnVuY3Rpb24oKXtyZXR1cm4gbi5jYWxsKHt9LHRoaXMuY3VycmVudEF0dHJpYnV0ZXMpfSxkLnByb3RvdHlwZS5nZXRDdXJyZW50VGV4dEF0dHJpYnV0ZXM9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saTt0PXt9LG49dGhpcy5jdXJyZW50QXR0cmlidXRlcztmb3IoZSBpbiBuKWk9bltlXSxpIT09ITEmJnIoZSkmJih0W2VdPWkpO3JldHVybiB0fSxkLnByb3RvdHlwZS5mcmVlemVTZWxlY3Rpb249ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zZXRDdXJyZW50QXR0cmlidXRlKFwiZnJvemVuXCIsITApfSxkLnByb3RvdHlwZS50aGF3U2VsZWN0aW9uPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMucmVtb3ZlQ3VycmVudEF0dHJpYnV0ZShcImZyb3plblwiKX0sZC5wcm90b3R5cGUuaGFzRnJvemVuU2VsZWN0aW9uPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuaGFzQ3VycmVudEF0dHJpYnV0ZShcImZyb3plblwiKX0sZC5wcm94eU1ldGhvZChcImdldFNlbGVjdGlvbk1hbmFnZXIoKS5nZXRQb2ludFJhbmdlXCIpLGQucHJveHlNZXRob2QoXCJnZXRTZWxlY3Rpb25NYW5hZ2VyKCkuc2V0TG9jYXRpb25SYW5nZUZyb21Qb2ludFJhbmdlXCIpLGQucHJveHlNZXRob2QoXCJnZXRTZWxlY3Rpb25NYW5hZ2VyKCkuY3JlYXRlTG9jYXRpb25SYW5nZUZyb21ET01SYW5nZVwiKSxkLnByb3h5TWV0aG9kKFwiZ2V0U2VsZWN0aW9uTWFuYWdlcigpLmxvY2F0aW9uSXNDdXJzb3JUYXJnZXRcIiksZC5wcm94eU1ldGhvZChcImdldFNlbGVjdGlvbk1hbmFnZXIoKS5zZWxlY3Rpb25Jc0V4cGFuZGVkXCIpLGQucHJveHlNZXRob2QoXCJkZWxlZ2F0ZT8uZ2V0U2VsZWN0aW9uTWFuYWdlclwiKSxkLnByb3RvdHlwZS5zZXRTZWxlY3Rpb249ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gZT10aGlzLmRvY3VtZW50LmxvY2F0aW9uUmFuZ2VGcm9tUmFuZ2UodCksbnVsbCE9KG49dGhpcy5kZWxlZ2F0ZSk/bi5jb21wb3NpdGlvbkRpZFJlcXVlc3RDaGFuZ2luZ1NlbGVjdGlvblRvTG9jYXRpb25SYW5nZShlKTp2b2lkIDB9LGQucHJvdG90eXBlLmdldFNlbGVjdGVkUmFuZ2U9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4odD10aGlzLmdldExvY2F0aW9uUmFuZ2UoKSk/dGhpcy5kb2N1bWVudC5yYW5nZUZyb21Mb2NhdGlvblJhbmdlKHQpOnZvaWQgMH0sZC5wcm90b3R5cGUuc2V0U2VsZWN0ZWRSYW5nZT1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT10aGlzLmRvY3VtZW50LmxvY2F0aW9uUmFuZ2VGcm9tUmFuZ2UodCksdGhpcy5nZXRTZWxlY3Rpb25NYW5hZ2VyKCkuc2V0TG9jYXRpb25SYW5nZShlKX0sZC5wcm90b3R5cGUuZ2V0UG9zaXRpb249ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4odD10aGlzLmdldExvY2F0aW9uUmFuZ2UoKSk/dGhpcy5kb2N1bWVudC5wb3NpdGlvbkZyb21Mb2NhdGlvbih0WzBdKTp2b2lkIDB9LGQucHJvdG90eXBlLmdldExvY2F0aW9uUmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gbnVsbCE9KGU9bnVsbCE9KG49dGhpcy50YXJnZXRMb2NhdGlvblJhbmdlKT9uOnRoaXMuZ2V0U2VsZWN0aW9uTWFuYWdlcigpLmdldExvY2F0aW9uUmFuZ2UodCkpP2U6cyh7aW5kZXg6MCxvZmZzZXQ6MH0pfSxkLnByb3RvdHlwZS53aXRoVGFyZ2V0TG9jYXRpb25SYW5nZT1mdW5jdGlvbih0LGUpe3ZhciBuO3RoaXMudGFyZ2V0TG9jYXRpb25SYW5nZT10O3RyeXtuPWUoKX1maW5hbGx5e3RoaXMudGFyZ2V0TG9jYXRpb25SYW5nZT1udWxsfXJldHVybiBufSxkLnByb3RvdHlwZS53aXRoVGFyZ2V0UmFuZ2U9ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4gbj10aGlzLmRvY3VtZW50LmxvY2F0aW9uUmFuZ2VGcm9tUmFuZ2UodCksdGhpcy53aXRoVGFyZ2V0TG9jYXRpb25SYW5nZShuLGUpfSxkLnByb3RvdHlwZS53aXRoVGFyZ2V0RE9NUmFuZ2U9ZnVuY3Rpb24odCxlKXt2YXIgbjtyZXR1cm4gbj10aGlzLmNyZWF0ZUxvY2F0aW9uUmFuZ2VGcm9tRE9NUmFuZ2UodCx7c3RyaWN0OiExfSksdGhpcy53aXRoVGFyZ2V0TG9jYXRpb25SYW5nZShuLGUpfSxkLnByb3RvdHlwZS5nZXRFeHBhbmRlZFJhbmdlSW5EaXJlY3Rpb249ZnVuY3Rpb24odCxlKXt2YXIgbixpLG8scjtyZXR1cm4gaT0obnVsbCE9ZT9lOnt9KS5sZW5ndGgsbz10aGlzLmdldFNlbGVjdGVkUmFuZ2UoKSxyPW9bMF0sbj1vWzFdLFwiYmFja3dhcmRcIj09PXQ/aT9yLT1pOnI9dGhpcy50cmFuc2xhdGVVVEYxNlBvc2l0aW9uRnJvbU9mZnNldChyLC0xKTppP24rPWk6bj10aGlzLnRyYW5zbGF0ZVVURjE2UG9zaXRpb25Gcm9tT2Zmc2V0KG4sMSkscyhbcixuXSl9LGQucHJvdG90eXBlLnNob3VsZE1hbmFnZU1vdmluZ0N1cnNvckluRGlyZWN0aW9uPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiB0aGlzLmVkaXRpbmdBdHRhY2htZW50PyEwOihlPXRoaXMuZ2V0RXhwYW5kZWRSYW5nZUluRGlyZWN0aW9uKHQpLG51bGwhPXRoaXMuZ2V0QXR0YWNobWVudEF0UmFuZ2UoZSkpfSxkLnByb3RvdHlwZS5tb3ZlQ3Vyc29ySW5EaXJlY3Rpb249ZnVuY3Rpb24odCl7dmFyIGUsbixpLG87cmV0dXJuIHRoaXMuZWRpdGluZ0F0dGFjaG1lbnQ/aT10aGlzLmRvY3VtZW50LmdldFJhbmdlT2ZBdHRhY2htZW50KHRoaXMuZWRpdGluZ0F0dGFjaG1lbnQpOihvPXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpLGk9dGhpcy5nZXRFeHBhbmRlZFJhbmdlSW5EaXJlY3Rpb24odCksbj0hYyhvLGkpKSx0aGlzLnNldFNlbGVjdGVkUmFuZ2UoXCJiYWNrd2FyZFwiPT09dD9pWzBdOmlbMV0pLG4mJihlPXRoaXMuZ2V0QXR0YWNobWVudEF0UmFuZ2UoaSkpP3RoaXMuZWRpdEF0dGFjaG1lbnQoZSk6dm9pZCAwfSxkLnByb3RvdHlwZS5leHBhbmRTZWxlY3Rpb25JbkRpcmVjdGlvbj1mdW5jdGlvbih0LGUpe3ZhciBuLGk7cmV0dXJuIG49KG51bGwhPWU/ZTp7fSkubGVuZ3RoLGk9dGhpcy5nZXRFeHBhbmRlZFJhbmdlSW5EaXJlY3Rpb24odCx7bGVuZ3RoOm59KSx0aGlzLnNldFNlbGVjdGVkUmFuZ2UoaSl9LGQucHJvdG90eXBlLmV4cGFuZFNlbGVjdGlvbkZvckVkaXRpbmc9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5oYXNDdXJyZW50QXR0cmlidXRlKFwiaHJlZlwiKT90aGlzLmV4cGFuZFNlbGVjdGlvbkFyb3VuZENvbW1vbkF0dHJpYnV0ZShcImhyZWZcIik6dm9pZCAwfSxkLnByb3RvdHlwZS5leHBhbmRTZWxlY3Rpb25Bcm91bmRDb21tb25BdHRyaWJ1dGU9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gZT10aGlzLmdldFBvc2l0aW9uKCksbj10aGlzLmRvY3VtZW50LmdldFJhbmdlT2ZDb21tb25BdHRyaWJ1dGVBdFBvc2l0aW9uKHQsZSksdGhpcy5zZXRTZWxlY3RlZFJhbmdlKG4pfSxkLnByb3RvdHlwZS5zZWxlY3Rpb25Db250YWluc0F0dGFjaG1lbnRzPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKG51bGwhPSh0PXRoaXMuZ2V0U2VsZWN0ZWRBdHRhY2htZW50cygpKT90Lmxlbmd0aDp2b2lkIDApPjB9LGQucHJvdG90eXBlLnNlbGVjdGlvbklzSW5DdXJzb3JUYXJnZXQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5lZGl0aW5nQXR0YWNobWVudHx8dGhpcy5wb3NpdGlvbklzQ3Vyc29yVGFyZ2V0KHRoaXMuZ2V0UG9zaXRpb24oKSl9LGQucHJvdG90eXBlLnBvc2l0aW9uSXNDdXJzb3JUYXJnZXQ9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuKGU9dGhpcy5kb2N1bWVudC5sb2NhdGlvbkZyb21Qb3NpdGlvbih0KSk/dGhpcy5sb2NhdGlvbklzQ3Vyc29yVGFyZ2V0KGUpOnZvaWQgMH0sZC5wcm90b3R5cGUucG9zaXRpb25Jc0Jsb2NrQnJlYWs9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMuZG9jdW1lbnQuZ2V0UGllY2VBdFBvc2l0aW9uKHQpKT9lLmlzQmxvY2tCcmVhaygpOnZvaWQgMH0sZC5wcm90b3R5cGUuZ2V0U2VsZWN0ZWREb2N1bWVudD1mdW5jdGlvbigpe3ZhciB0O3JldHVybih0PXRoaXMuZ2V0U2VsZWN0ZWRSYW5nZSgpKT90aGlzLmRvY3VtZW50LmdldERvY3VtZW50QXRSYW5nZSh0KTp2b2lkIDB9LGQucHJvdG90eXBlLmdldFNlbGVjdGVkQXR0YWNobWVudHM9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gbnVsbCE9KHQ9dGhpcy5nZXRTZWxlY3RlZERvY3VtZW50KCkpP3QuZ2V0QXR0YWNobWVudHMoKTp2b2lkIDB9LGQucHJvdG90eXBlLmdldEF0dGFjaG1lbnRzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuYXR0YWNobWVudHMuc2xpY2UoMCl9LGQucHJvdG90eXBlLnJlZnJlc2hBdHRhY2htZW50cz1mdW5jdGlvbigpe3ZhciB0LGUsbixpLG8scixzLGEsdSxjLGgscDtmb3Iobj10aGlzLmRvY3VtZW50LmdldEF0dGFjaG1lbnRzKCksYT1sKHRoaXMuYXR0YWNobWVudHMsbiksdD1hLmFkZGVkLGg9YS5yZW1vdmVkLHRoaXMuYXR0YWNobWVudHM9bixpPTAscj1oLmxlbmd0aDtyPmk7aSsrKWU9aFtpXSxlLmRlbGVnYXRlPW51bGwsbnVsbCE9KHU9dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIHUuY29tcG9zaXRpb25EaWRSZW1vdmVBdHRhY2htZW50JiZ1LmNvbXBvc2l0aW9uRGlkUmVtb3ZlQXR0YWNobWVudChlKTtmb3IocD1bXSxvPTAscz10Lmxlbmd0aDtzPm87bysrKWU9dFtvXSxlLmRlbGVnYXRlPXRoaXMscC5wdXNoKG51bGwhPShjPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBjLmNvbXBvc2l0aW9uRGlkQWRkQXR0YWNobWVudD9jLmNvbXBvc2l0aW9uRGlkQWRkQXR0YWNobWVudChlKTp2b2lkIDApO3JldHVybiBwfSxkLnByb3RvdHlwZS5hdHRhY2htZW50RGlkQ2hhbmdlQXR0cmlidXRlcz1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gdGhpcy5yZXZpc2lvbisrLG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBlLmNvbXBvc2l0aW9uRGlkRWRpdEF0dGFjaG1lbnQ/ZS5jb21wb3NpdGlvbkRpZEVkaXRBdHRhY2htZW50KHQpOnZvaWQgMH0sZC5wcm90b3R5cGUuYXR0YWNobWVudERpZENoYW5nZVByZXZpZXdVUkw9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHRoaXMucmV2aXNpb24rKyxudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgZS5jb21wb3NpdGlvbkRpZENoYW5nZUF0dGFjaG1lbnRQcmV2aWV3VVJMP2UuY29tcG9zaXRpb25EaWRDaGFuZ2VBdHRhY2htZW50UHJldmlld1VSTCh0KTp2b2lkIDB9LGQucHJvdG90eXBlLmVkaXRBdHRhY2htZW50PWZ1bmN0aW9uKHQsZSl7dmFyIG47aWYodCE9PXRoaXMuZWRpdGluZ0F0dGFjaG1lbnQpcmV0dXJuIHRoaXMuc3RvcEVkaXRpbmdBdHRhY2htZW50KCksdGhpcy5lZGl0aW5nQXR0YWNobWVudD10LG51bGwhPShuPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBuLmNvbXBvc2l0aW9uRGlkU3RhcnRFZGl0aW5nQXR0YWNobWVudD9uLmNvbXBvc2l0aW9uRGlkU3RhcnRFZGl0aW5nQXR0YWNobWVudCh0aGlzLmVkaXRpbmdBdHRhY2htZW50LGUpOnZvaWQgMH0sZC5wcm90b3R5cGUuc3RvcEVkaXRpbmdBdHRhY2htZW50PWZ1bmN0aW9uKCl7dmFyIHQ7aWYodGhpcy5lZGl0aW5nQXR0YWNobWVudClyZXR1cm4gbnVsbCE9KHQ9dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIHQuY29tcG9zaXRpb25EaWRTdG9wRWRpdGluZ0F0dGFjaG1lbnQmJnQuY29tcG9zaXRpb25EaWRTdG9wRWRpdGluZ0F0dGFjaG1lbnQodGhpcy5lZGl0aW5nQXR0YWNobWVudCksdGhpcy5lZGl0aW5nQXR0YWNobWVudD1udWxsfSxkLnByb3RvdHlwZS51cGRhdGVBdHRyaWJ1dGVzRm9yQXR0YWNobWVudD1mdW5jdGlvbih0LGUpe3JldHVybiB0aGlzLnNldERvY3VtZW50KHRoaXMuZG9jdW1lbnQudXBkYXRlQXR0cmlidXRlc0ZvckF0dGFjaG1lbnQodCxlKSl9LGQucHJvdG90eXBlLnJlbW92ZUF0dHJpYnV0ZUZvckF0dGFjaG1lbnQ9ZnVuY3Rpb24odCxlKXtyZXR1cm4gdGhpcy5zZXREb2N1bWVudCh0aGlzLmRvY3VtZW50LnJlbW92ZUF0dHJpYnV0ZUZvckF0dGFjaG1lbnQodCxlKSl9LGQucHJvdG90eXBlLmJyZWFrRm9ybWF0dGVkQmxvY2s9ZnVuY3Rpb24odCl7dmFyIG4saSxvLHIscztyZXR1cm4gaT10LmRvY3VtZW50LG49dC5ibG9jayxyPXQuc3RhcnRQb3NpdGlvbixzPVtyLTEscl0sbi5nZXRCbG9ja0JyZWFrUG9zaXRpb24oKT09PXQuc3RhcnRMb2NhdGlvbi5vZmZzZXQ/KG4uYnJlYWtzT25SZXR1cm4oKSYmXCJcXG5cIj09PXQubmV4dENoYXJhY3Rlcj9yKz0xOmk9aS5yZW1vdmVUZXh0QXRSYW5nZShzKSxzPVtyLHJdKTpcIlxcblwiPT09dC5uZXh0Q2hhcmFjdGVyP1wiXFxuXCI9PT10LnByZXZpb3VzQ2hhcmFjdGVyP3M9W3ItMSxyKzFdOihzPVtyLHIrMV0scis9MSk6dC5zdGFydExvY2F0aW9uLm9mZnNldC0xIT09MCYmKHIrPTEpLG89bmV3IGUuRG9jdW1lbnQoW24ucmVtb3ZlTGFzdEF0dHJpYnV0ZSgpLmNvcHlXaXRob3V0VGV4dCgpXSksdGhpcy5zZXREb2N1bWVudChpLmluc2VydERvY3VtZW50QXRSYW5nZShvLHMpKSx0aGlzLnNldFNlbGVjdGlvbihyKX0sZC5wcm90b3R5cGUuZ2V0UHJldmlvdXNCbG9jaz1mdW5jdGlvbigpe3ZhciB0LGU7cmV0dXJuKGU9dGhpcy5nZXRMb2NhdGlvblJhbmdlKCkpJiYodD1lWzBdLmluZGV4LHQ+MCk/dGhpcy5kb2N1bWVudC5nZXRCbG9ja0F0SW5kZXgodC0xKTp2b2lkIDB9LGQucHJvdG90eXBlLmdldEJsb2NrPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKHQ9dGhpcy5nZXRMb2NhdGlvblJhbmdlKCkpP3RoaXMuZG9jdW1lbnQuZ2V0QmxvY2tBdEluZGV4KHRbMF0uaW5kZXgpOnZvaWQgMH0sZC5wcm90b3R5cGUuZ2V0QXR0YWNobWVudEF0UmFuZ2U9ZnVuY3Rpb24odCl7dmFyIG47cmV0dXJuIG49dGhpcy5kb2N1bWVudC5nZXREb2N1bWVudEF0UmFuZ2UodCksbi50b1N0cmluZygpPT09ZS5PQkpFQ1RfUkVQTEFDRU1FTlRfQ0hBUkFDVEVSK1wiXFxuXCI/bi5nZXRBdHRhY2htZW50cygpWzBdOnZvaWQgMH0sZC5wcm90b3R5cGUubm90aWZ5RGVsZWdhdGVPZkN1cnJlbnRBdHRyaWJ1dGVzQ2hhbmdlPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiB0LmNvbXBvc2l0aW9uRGlkQ2hhbmdlQ3VycmVudEF0dHJpYnV0ZXM/dC5jb21wb3NpdGlvbkRpZENoYW5nZUN1cnJlbnRBdHRyaWJ1dGVzKHRoaXMuY3VycmVudEF0dHJpYnV0ZXMpOnZvaWQgMH0sZC5wcm90b3R5cGUubm90aWZ5RGVsZWdhdGVPZkluc2VydGlvbkF0UmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIG51bGwhPShlPXRoaXMuZGVsZWdhdGUpJiZcImZ1bmN0aW9uXCI9PXR5cGVvZiBlLmNvbXBvc2l0aW9uRGlkUGVyZm9ybUluc2VydGlvbkF0UmFuZ2U/ZS5jb21wb3NpdGlvbkRpZFBlcmZvcm1JbnNlcnRpb25BdFJhbmdlKHQpOnZvaWQgMH0sZC5wcm90b3R5cGUudHJhbnNsYXRlVVRGMTZQb3NpdGlvbkZyb21PZmZzZXQ9ZnVuY3Rpb24odCxlKXt2YXIgbixpO3JldHVybiBpPXRoaXMuZG9jdW1lbnQudG9VVEYxNlN0cmluZygpLG49aS5vZmZzZXRGcm9tVUNTMk9mZnNldCh0KSxpLm9mZnNldFRvVUNTMk9mZnNldChuK2UpfSxkfShlLkJhc2ljT2JqZWN0KX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gaSgpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIG8gaW4gZSluLmNhbGwoZSxvKSYmKHRbb109ZVtvXSk7cmV0dXJuIGkucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBpLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG49e30uaGFzT3duUHJvcGVydHk7ZS5VbmRvTWFuYWdlcj1mdW5jdGlvbihlKXtmdW5jdGlvbiBuKHQpe3RoaXMuY29tcG9zaXRpb249dCx0aGlzLnVuZG9FbnRyaWVzPVtdLHRoaXMucmVkb0VudHJpZXM9W119dmFyIGk7cmV0dXJuIHQobixlKSxuLnByb3RvdHlwZS5yZWNvcmRVbmRvRW50cnk9ZnVuY3Rpb24odCxlKXt2YXIgbixvLHIscyxhO3JldHVybiBzPW51bGwhPWU/ZTp7fSxvPXMuY29udGV4dCxuPXMuY29uc29saWRhdGFibGUscj10aGlzLnVuZG9FbnRyaWVzLnNsaWNlKC0xKVswXSxuJiZpKHIsdCxvKT92b2lkIDA6KGE9dGhpcy5jcmVhdGVFbnRyeSh7ZGVzY3JpcHRpb246dCxjb250ZXh0Om99KSx0aGlzLnVuZG9FbnRyaWVzLnB1c2goYSksdGhpcy5yZWRvRW50cmllcz1bXSl9LG4ucHJvdG90eXBlLnVuZG89ZnVuY3Rpb24oKXt2YXIgdCxlO3JldHVybihlPXRoaXMudW5kb0VudHJpZXMucG9wKCkpPyh0PXRoaXMuY3JlYXRlRW50cnkoZSksdGhpcy5yZWRvRW50cmllcy5wdXNoKHQpLHRoaXMuY29tcG9zaXRpb24ubG9hZFNuYXBzaG90KGUuc25hcHNob3QpKTp2b2lkIDB9LG4ucHJvdG90eXBlLnJlZG89ZnVuY3Rpb24oKXt2YXIgdCxlO3JldHVybih0PXRoaXMucmVkb0VudHJpZXMucG9wKCkpPyhlPXRoaXMuY3JlYXRlRW50cnkodCksdGhpcy51bmRvRW50cmllcy5wdXNoKGUpLHRoaXMuY29tcG9zaXRpb24ubG9hZFNuYXBzaG90KHQuc25hcHNob3QpKTp2b2lkIDB9LG4ucHJvdG90eXBlLmNhblVuZG89ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy51bmRvRW50cmllcy5sZW5ndGg+MH0sbi5wcm90b3R5cGUuY2FuUmVkbz1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlZG9FbnRyaWVzLmxlbmd0aD4wfSxuLnByb3RvdHlwZS5jcmVhdGVFbnRyeT1mdW5jdGlvbih0KXt2YXIgZSxuLGk7cmV0dXJuIGk9bnVsbCE9dD90Ont9LG49aS5kZXNjcmlwdGlvbixlPWkuY29udGV4dCx7ZGVzY3JpcHRpb246bnVsbCE9bj9uLnRvU3RyaW5nKCk6dm9pZCAwLGNvbnRleHQ6SlNPTi5zdHJpbmdpZnkoZSksc25hcHNob3Q6dGhpcy5jb21wb3NpdGlvbi5nZXRTbmFwc2hvdCgpfX0saT1mdW5jdGlvbih0LGUsbil7cmV0dXJuKG51bGwhPXQ/dC5kZXNjcmlwdGlvbjp2b2lkIDApPT09KG51bGwhPWU/ZS50b1N0cmluZygpOnZvaWQgMCkmJihudWxsIT10P3QuY29udGV4dDp2b2lkIDApPT09SlNPTi5zdHJpbmdpZnkobil9LG59KGUuQmFzaWNPYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ7ZS5hdHRhY2htZW50R2FsbGVyeUZpbHRlcj1mdW5jdGlvbihlKXt2YXIgbjtyZXR1cm4gbj1uZXcgdChlKSxuLnBlcmZvcm0oKSxuLmdldFNuYXBzaG90KCl9LHQ9ZnVuY3Rpb24oKXtmdW5jdGlvbiB0KHQpe3RoaXMuZG9jdW1lbnQ9dC5kb2N1bWVudCx0aGlzLnNlbGVjdGVkUmFuZ2U9dC5zZWxlY3RlZFJhbmdlfXZhciBlLG4saTtyZXR1cm4gZT1cImF0dGFjaG1lbnRHYWxsZXJ5XCIsbj1cInByZXNlbnRhdGlvblwiLGk9XCJnYWxsZXJ5XCIsdC5wcm90b3R5cGUucGVyZm9ybT1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlbW92ZUJsb2NrQXR0cmlidXRlKCksdGhpcy5hcHBseUJsb2NrQXR0cmlidXRlKCl9LHQucHJvdG90eXBlLmdldFNuYXBzaG90PWZ1bmN0aW9uKCl7cmV0dXJue2RvY3VtZW50OnRoaXMuZG9jdW1lbnQsc2VsZWN0ZWRSYW5nZTp0aGlzLnNlbGVjdGVkUmFuZ2V9fSx0LnByb3RvdHlwZS5yZW1vdmVCbG9ja0F0dHJpYnV0ZT1mdW5jdGlvbigpe3ZhciB0LG4saSxvLHI7Zm9yKG89dGhpcy5maW5kUmFuZ2VzT2ZCbG9ja3MoKSxyPVtdLHQ9MCxuPW8ubGVuZ3RoO24+dDt0KyspaT1vW3RdLHIucHVzaCh0aGlzLmRvY3VtZW50PXRoaXMuZG9jdW1lbnQucmVtb3ZlQXR0cmlidXRlQXRSYW5nZShlLGkpKTtyZXR1cm4gcn0sdC5wcm90b3R5cGUuYXBwbHlCbG9ja0F0dHJpYnV0ZT1mdW5jdGlvbigpe3ZhciB0LG4saSxvLHIscztmb3IoaT0wLHI9dGhpcy5maW5kUmFuZ2VzT2ZQaWVjZXMoKSxzPVtdLHQ9MCxuPXIubGVuZ3RoO24+dDt0Kyspbz1yW3RdLG9bMV0tb1swXT4xJiYob1swXSs9aSxvWzFdKz1pLFwiXFxuXCIhPT10aGlzLmRvY3VtZW50LmdldENoYXJhY3RlckF0UG9zaXRpb24ob1sxXSkmJih0aGlzLmRvY3VtZW50PXRoaXMuZG9jdW1lbnQuaW5zZXJ0QmxvY2tCcmVha0F0UmFuZ2Uob1sxXSksb1sxXTx0aGlzLnNlbGVjdGVkUmFuZ2VbMV0mJnRoaXMubW92ZVNlbGVjdGVkUmFuZ2VGb3J3YXJkKCksb1sxXSsrLGkrKyksMCE9PW9bMF0mJlwiXFxuXCIhPT10aGlzLmRvY3VtZW50LmdldENoYXJhY3RlckF0UG9zaXRpb24ob1swXS0xKSYmKHRoaXMuZG9jdW1lbnQ9dGhpcy5kb2N1bWVudC5pbnNlcnRCbG9ja0JyZWFrQXRSYW5nZShvWzBdKSxvWzBdPHRoaXMuc2VsZWN0ZWRSYW5nZVswXSYmdGhpcy5tb3ZlU2VsZWN0ZWRSYW5nZUZvcndhcmQoKSxvWzBdKyssaSsrKSxzLnB1c2godGhpcy5kb2N1bWVudD10aGlzLmRvY3VtZW50LmFwcGx5QmxvY2tBdHRyaWJ1dGVBdFJhbmdlKGUsITAsbykpKTtyZXR1cm4gc30sdC5wcm90b3R5cGUuZmluZFJhbmdlc09mQmxvY2tzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZG9jdW1lbnQuZmluZFJhbmdlc0ZvckJsb2NrQXR0cmlidXRlKGUpfSx0LnByb3RvdHlwZS5maW5kUmFuZ2VzT2ZQaWVjZXM9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5kb2N1bWVudC5maW5kUmFuZ2VzRm9yVGV4dEF0dHJpYnV0ZShuLHt3aXRoVmFsdWU6aX0pfSx0LnByb3RvdHlwZS5tb3ZlU2VsZWN0ZWRSYW5nZUZvcndhcmQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zZWxlY3RlZFJhbmdlWzBdKz0xLHRoaXMuc2VsZWN0ZWRSYW5nZVsxXSs9MX0sdH0oKX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0PWZ1bmN0aW9uKHQsZSl7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIHQuYXBwbHkoZSxhcmd1bWVudHMpfX07ZS5FZGl0b3I9ZnVuY3Rpb24oKXtmdW5jdGlvbiBuKG4sbyxyKXt0aGlzLmNvbXBvc2l0aW9uPW4sdGhpcy5zZWxlY3Rpb25NYW5hZ2VyPW8sdGhpcy5lbGVtZW50PXIsdGhpcy5pbnNlcnRGaWxlcz10KHRoaXMuaW5zZXJ0RmlsZXMsdGhpcyksdGhpcy51bmRvTWFuYWdlcj1uZXcgZS5VbmRvTWFuYWdlcih0aGlzLmNvbXBvc2l0aW9uKSx0aGlzLmZpbHRlcnM9aS5zbGljZSgwKX12YXIgaTtyZXR1cm4gaT1bZS5hdHRhY2htZW50R2FsbGVyeUZpbHRlcl0sbi5wcm90b3R5cGUubG9hZERvY3VtZW50PWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmxvYWRTbmFwc2hvdCh7ZG9jdW1lbnQ6dCxzZWxlY3RlZFJhbmdlOlswLDBdfSl9LG4ucHJvdG90eXBlLmxvYWRIVE1MPWZ1bmN0aW9uKHQpe3JldHVybiBudWxsPT10JiYodD1cIlwiKSx0aGlzLmxvYWREb2N1bWVudChlLkRvY3VtZW50LmZyb21IVE1MKHQse3JlZmVyZW5jZUVsZW1lbnQ6dGhpcy5lbGVtZW50fSkpfSxuLnByb3RvdHlwZS5sb2FkSlNPTj1mdW5jdGlvbih0KXt2YXIgbixpO3JldHVybiBuPXQuZG9jdW1lbnQsaT10LnNlbGVjdGVkUmFuZ2Usbj1lLkRvY3VtZW50LmZyb21KU09OKG4pLHRoaXMubG9hZFNuYXBzaG90KHtkb2N1bWVudDpuLHNlbGVjdGVkUmFuZ2U6aX0pfSxuLnByb3RvdHlwZS5sb2FkU25hcHNob3Q9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMudW5kb01hbmFnZXI9bmV3IGUuVW5kb01hbmFnZXIodGhpcy5jb21wb3NpdGlvbiksdGhpcy5jb21wb3NpdGlvbi5sb2FkU25hcHNob3QodCl9LG4ucHJvdG90eXBlLmdldERvY3VtZW50PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uZG9jdW1lbnR9LG4ucHJvdG90eXBlLmdldFNlbGVjdGVkRG9jdW1lbnQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbi5nZXRTZWxlY3RlZERvY3VtZW50KCl9LG4ucHJvdG90eXBlLmdldFNuYXBzaG90PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uZ2V0U25hcHNob3QoKX0sbi5wcm90b3R5cGUudG9KU09OPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZ2V0U25hcHNob3QoKX0sbi5wcm90b3R5cGUuZGVsZXRlSW5EaXJlY3Rpb249ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uZGVsZXRlSW5EaXJlY3Rpb24odCl9LG4ucHJvdG90eXBlLmluc2VydEF0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uaW5zZXJ0QXR0YWNobWVudCh0KX0sbi5wcm90b3R5cGUuaW5zZXJ0QXR0YWNobWVudHM9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uaW5zZXJ0QXR0YWNobWVudHModCl9LG4ucHJvdG90eXBlLmluc2VydERvY3VtZW50PWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uLmluc2VydERvY3VtZW50KHQpfSxuLnByb3RvdHlwZS5pbnNlcnRGaWxlPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uLmluc2VydEZpbGUodCl9LG4ucHJvdG90eXBlLmluc2VydEZpbGVzPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uLmluc2VydEZpbGVzKHQpfSxuLnByb3RvdHlwZS5pbnNlcnRIVE1MPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uLmluc2VydEhUTUwodCl9LG4ucHJvdG90eXBlLmluc2VydFN0cmluZz1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbi5pbnNlcnRTdHJpbmcodCl9LG4ucHJvdG90eXBlLmluc2VydFRleHQ9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uaW5zZXJ0VGV4dCh0KX0sbi5wcm90b3R5cGUuaW5zZXJ0TGluZUJyZWFrPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uaW5zZXJ0TGluZUJyZWFrKCl9LG4ucHJvdG90eXBlLmdldFNlbGVjdGVkUmFuZ2U9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbi5nZXRTZWxlY3RlZFJhbmdlKCl9LG4ucHJvdG90eXBlLmdldFBvc2l0aW9uPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uZ2V0UG9zaXRpb24oKX0sbi5wcm90b3R5cGUuZ2V0Q2xpZW50UmVjdEF0UG9zaXRpb249ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIGU9dGhpcy5nZXREb2N1bWVudCgpLmxvY2F0aW9uUmFuZ2VGcm9tUmFuZ2UoW3QsdCsxXSksdGhpcy5zZWxlY3Rpb25NYW5hZ2VyLmdldENsaWVudFJlY3RBdExvY2F0aW9uUmFuZ2UoZSl9LG4ucHJvdG90eXBlLmV4cGFuZFNlbGVjdGlvbkluRGlyZWN0aW9uPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uLmV4cGFuZFNlbGVjdGlvbkluRGlyZWN0aW9uKHQpfSxuLnByb3RvdHlwZS5tb3ZlQ3Vyc29ySW5EaXJlY3Rpb249ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24ubW92ZUN1cnNvckluRGlyZWN0aW9uKHQpfSxuLnByb3RvdHlwZS5zZXRTZWxlY3RlZFJhbmdlPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uLnNldFNlbGVjdGVkUmFuZ2UodCl9LG4ucHJvdG90eXBlLmFjdGl2YXRlQXR0cmlidXRlPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIG51bGw9PWUmJihlPSEwKSx0aGlzLmNvbXBvc2l0aW9uLnNldEN1cnJlbnRBdHRyaWJ1dGUodCxlKX0sbi5wcm90b3R5cGUuYXR0cmlidXRlSXNBY3RpdmU9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uaGFzQ3VycmVudEF0dHJpYnV0ZSh0KX0sbi5wcm90b3R5cGUuY2FuQWN0aXZhdGVBdHRyaWJ1dGU9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uY2FuU2V0Q3VycmVudEF0dHJpYnV0ZSh0KX0sbi5wcm90b3R5cGUuZGVhY3RpdmF0ZUF0dHJpYnV0ZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbi5yZW1vdmVDdXJyZW50QXR0cmlidXRlKHQpfSxuLnByb3RvdHlwZS5jYW5EZWNyZWFzZU5lc3RpbmdMZXZlbD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uLmNhbkRlY3JlYXNlTmVzdGluZ0xldmVsKCl9LG4ucHJvdG90eXBlLmNhbkluY3JlYXNlTmVzdGluZ0xldmVsPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uY2FuSW5jcmVhc2VOZXN0aW5nTGV2ZWwoKX0sbi5wcm90b3R5cGUuZGVjcmVhc2VOZXN0aW5nTGV2ZWw9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jYW5EZWNyZWFzZU5lc3RpbmdMZXZlbCgpP3RoaXMuY29tcG9zaXRpb24uZGVjcmVhc2VOZXN0aW5nTGV2ZWwoKTp2b2lkIDB9LG4ucHJvdG90eXBlLmluY3JlYXNlTmVzdGluZ0xldmVsPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY2FuSW5jcmVhc2VOZXN0aW5nTGV2ZWwoKT90aGlzLmNvbXBvc2l0aW9uLmluY3JlYXNlTmVzdGluZ0xldmVsKCk6dm9pZCAwfSxuLnByb3RvdHlwZS5jYW5SZWRvPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudW5kb01hbmFnZXIuY2FuUmVkbygpfSxuLnByb3RvdHlwZS5jYW5VbmRvPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMudW5kb01hbmFnZXIuY2FuVW5kbygpfSxuLnByb3RvdHlwZS5yZWNvcmRVbmRvRW50cnk9ZnVuY3Rpb24odCxlKXt2YXIgbixpLG87cmV0dXJuIG89bnVsbCE9ZT9lOnt9LGk9by5jb250ZXh0LG49by5jb25zb2xpZGF0YWJsZSx0aGlzLnVuZG9NYW5hZ2VyLnJlY29yZFVuZG9FbnRyeSh0LHtjb250ZXh0OmksY29uc29saWRhdGFibGU6bn0pfSxuLnByb3RvdHlwZS5yZWRvPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY2FuUmVkbygpP3RoaXMudW5kb01hbmFnZXIucmVkbygpOnZvaWQgMH0sbi5wcm90b3R5cGUudW5kbz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmNhblVuZG8oKT90aGlzLnVuZG9NYW5hZ2VyLnVuZG8oKTp2b2lkIDB9LG59KCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdD1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIGkoKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBvIGluIGUpbi5jYWxsKGUsbykmJih0W29dPWVbb10pO3JldHVybiBpLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgaSx0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxuPXt9Lmhhc093blByb3BlcnR5O2UuTWFuYWdlZEF0dGFjaG1lbnQ9ZnVuY3Rpb24oZSl7ZnVuY3Rpb24gbih0LGUpe3ZhciBuO3RoaXMuYXR0YWNobWVudE1hbmFnZXI9dCx0aGlzLmF0dGFjaG1lbnQ9ZSxuPXRoaXMuYXR0YWNobWVudCx0aGlzLmlkPW4uaWQsdGhpcy5maWxlPW4uZmlsZX1yZXR1cm4gdChuLGUpLG4ucHJvdG90eXBlLnJlbW92ZT1mdW5jdGlvbigpe3JldHVybiB0aGlzLmF0dGFjaG1lbnRNYW5hZ2VyLnJlcXVlc3RSZW1vdmFsT2ZBdHRhY2htZW50KHRoaXMuYXR0YWNobWVudCl9LG4ucHJveHlNZXRob2QoXCJhdHRhY2htZW50LmdldEF0dHJpYnV0ZVwiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5oYXNBdHRyaWJ1dGVcIiksbi5wcm94eU1ldGhvZChcImF0dGFjaG1lbnQuc2V0QXR0cmlidXRlXCIpLG4ucHJveHlNZXRob2QoXCJhdHRhY2htZW50LmdldEF0dHJpYnV0ZXNcIiksbi5wcm94eU1ldGhvZChcImF0dGFjaG1lbnQuc2V0QXR0cmlidXRlc1wiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5pc1BlbmRpbmdcIiksbi5wcm94eU1ldGhvZChcImF0dGFjaG1lbnQuaXNQcmV2aWV3YWJsZVwiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5nZXRVUkxcIiksbi5wcm94eU1ldGhvZChcImF0dGFjaG1lbnQuZ2V0SHJlZlwiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5nZXRGaWxlbmFtZVwiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5nZXRGaWxlc2l6ZVwiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5nZXRGb3JtYXR0ZWRGaWxlc2l6ZVwiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5nZXRFeHRlbnNpb25cIiksbi5wcm94eU1ldGhvZChcImF0dGFjaG1lbnQuZ2V0Q29udGVudFR5cGVcIiksbi5wcm94eU1ldGhvZChcImF0dGFjaG1lbnQuZ2V0RmlsZVwiKSxuLnByb3h5TWV0aG9kKFwiYXR0YWNobWVudC5zZXRGaWxlXCIpLG4ucHJveHlNZXRob2QoXCJhdHRhY2htZW50LnJlbGVhc2VGaWxlXCIpLG4ucHJveHlNZXRob2QoXCJhdHRhY2htZW50LmdldFVwbG9hZFByb2dyZXNzXCIpLG4ucHJveHlNZXRob2QoXCJhdHRhY2htZW50LnNldFVwbG9hZFByb2dyZXNzXCIpLG59KGUuQmFzaWNPYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQ9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBpKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgbyBpbiBlKW4uY2FsbChlLG8pJiYodFtvXT1lW29dKTtyZXR1cm4gaS5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IGksdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sbj17fS5oYXNPd25Qcm9wZXJ0eTtlLkF0dGFjaG1lbnRNYW5hZ2VyPWZ1bmN0aW9uKG4pe2Z1bmN0aW9uIGkodCl7dmFyIGUsbixpO2ZvcihudWxsPT10JiYodD1bXSksdGhpcy5tYW5hZ2VkQXR0YWNobWVudHM9e30sbj0wLGk9dC5sZW5ndGg7aT5uO24rKyllPXRbbl0sdGhpcy5tYW5hZ2VBdHRhY2htZW50KGUpfXJldHVybiB0KGksbiksaS5wcm90b3R5cGUuZ2V0QXR0YWNobWVudHM9ZnVuY3Rpb24oKXt2YXIgdCxlLG4saTtuPXRoaXMubWFuYWdlZEF0dGFjaG1lbnRzLGk9W107Zm9yKGUgaW4gbil0PW5bZV0saS5wdXNoKHQpO3JldHVybiBpfSxpLnByb3RvdHlwZS5tYW5hZ2VBdHRhY2htZW50PWZ1bmN0aW9uKHQpe3ZhciBuLGk7cmV0dXJuIG51bGwhPShuPXRoaXMubWFuYWdlZEF0dGFjaG1lbnRzKVtpPXQuaWRdP25baV06bltpXT1uZXcgZS5NYW5hZ2VkQXR0YWNobWVudCh0aGlzLHQpfSxpLnByb3RvdHlwZS5hdHRhY2htZW50SXNNYW5hZ2VkPWZ1bmN0aW9uKHQpe3JldHVybiB0LmlkIGluIHRoaXMubWFuYWdlZEF0dGFjaG1lbnRzfSxpLnByb3RvdHlwZS5yZXF1ZXN0UmVtb3ZhbE9mQXR0YWNobWVudD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gdGhpcy5hdHRhY2htZW50SXNNYW5hZ2VkKHQpJiZudWxsIT0oZT10aGlzLmRlbGVnYXRlKSYmXCJmdW5jdGlvblwiPT10eXBlb2YgZS5hdHRhY2htZW50TWFuYWdlckRpZFJlcXVlc3RSZW1vdmFsT2ZBdHRhY2htZW50P2UuYXR0YWNobWVudE1hbmFnZXJEaWRSZXF1ZXN0UmVtb3ZhbE9mQXR0YWNobWVudCh0KTp2b2lkIDB9LGkucHJvdG90eXBlLnVubWFuYWdlQXR0YWNobWVudD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT10aGlzLm1hbmFnZWRBdHRhY2htZW50c1t0LmlkXSxkZWxldGUgdGhpcy5tYW5hZ2VkQXR0YWNobWVudHNbdC5pZF0sZX0saX0oZS5CYXNpY09iamVjdCl9LmNhbGwodGhpcyksZnVuY3Rpb24oKXt2YXIgdCxuLGksbyxyLHMsYSx1LGMsbCxoO3Q9ZS5lbGVtZW50Q29udGFpbnNOb2RlLG49ZS5maW5kQ2hpbGRJbmRleE9mTm9kZSxyPWUubm9kZUlzQmxvY2tTdGFydCxzPWUubm9kZUlzQmxvY2tTdGFydENvbW1lbnQsbz1lLm5vZGVJc0Jsb2NrQ29udGFpbmVyLGE9ZS5ub2RlSXNDdXJzb3JUYXJnZXQsdT1lLm5vZGVJc0VtcHR5VGV4dE5vZGUsYz1lLm5vZGVJc1RleHROb2RlLGk9ZS5ub2RlSXNBdHRhY2htZW50RWxlbWVudCxsPWUudGFnTmFtZSxoPWUud2Fsa1RyZWUsZS5Mb2NhdGlvbk1hcHBlcj1mdW5jdGlvbigpe2Z1bmN0aW9uIGUodCl7dGhpcy5lbGVtZW50PXR9dmFyIHAsZCxmLGc7cmV0dXJuIGUucHJvdG90eXBlLmZpbmRMb2NhdGlvbkZyb21Db250YWluZXJBbmRPZmZzZXQ9ZnVuY3Rpb24oZSxpLG8pe3ZhciBzLHUsbCxwLGcsbSx2O2ZvcihtPShudWxsIT1vP286e3N0cmljdDohMH0pLnN0cmljdCx1PTAsbD0hMSxwPXtpbmRleDowLG9mZnNldDowfSwocz10aGlzLmZpbmRBdHRhY2htZW50RWxlbWVudFBhcmVudEZvck5vZGUoZSkpJiYoZT1zLnBhcmVudE5vZGUsaT1uKHMpKSx2PWgodGhpcy5lbGVtZW50LHt1c2luZ0ZpbHRlcjpmfSk7di5uZXh0Tm9kZSgpOyl7aWYoZz12LmN1cnJlbnROb2RlLGc9PT1lJiZjKGUpKXthKGcpfHwocC5vZmZzZXQrPWkpO1xuYnJlYWt9aWYoZy5wYXJlbnROb2RlPT09ZSl7aWYodSsrPT09aSlicmVha31lbHNlIGlmKCF0KGUsZykmJnU+MClicmVhaztyKGcse3N0cmljdDptfSk/KGwmJnAuaW5kZXgrKyxwLm9mZnNldD0wLGw9ITApOnAub2Zmc2V0Kz1kKGcpfXJldHVybiBwfSxlLnByb3RvdHlwZS5maW5kQ29udGFpbmVyQW5kT2Zmc2V0RnJvbUxvY2F0aW9uPWZ1bmN0aW9uKHQpe3ZhciBlLGkscyx1LGw7aWYoMD09PXQuaW5kZXgmJjA9PT10Lm9mZnNldCl7Zm9yKGU9dGhpcy5lbGVtZW50LHU9MDtlLmZpcnN0Q2hpbGQ7KWlmKGU9ZS5maXJzdENoaWxkLG8oZSkpe3U9MTticmVha31yZXR1cm5bZSx1XX1pZihsPXRoaXMuZmluZE5vZGVBbmRPZmZzZXRGcm9tTG9jYXRpb24odCksaT1sWzBdLHM9bFsxXSxpKXtpZihjKGkpKTA9PT1kKGkpPyhlPWkucGFyZW50Tm9kZS5wYXJlbnROb2RlLHU9bihpLnBhcmVudE5vZGUpLGEoaSx7bmFtZTpcInJpZ2h0XCJ9KSYmdSsrKTooZT1pLHU9dC5vZmZzZXQtcyk7ZWxzZXtpZihlPWkucGFyZW50Tm9kZSwhcihpLnByZXZpb3VzU2libGluZykmJiFvKGUpKWZvcig7aT09PWUubGFzdENoaWxkJiYoaT1lLGU9ZS5wYXJlbnROb2RlLCFvKGUpKTspO3U9bihpKSwwIT09dC5vZmZzZXQmJnUrK31yZXR1cm5bZSx1XX19LGUucHJvdG90eXBlLmZpbmROb2RlQW5kT2Zmc2V0RnJvbUxvY2F0aW9uPWZ1bmN0aW9uKHQpe3ZhciBlLG4saSxvLHIscyx1LGw7Zm9yKHU9MCxsPXRoaXMuZ2V0U2lnbmlmaWNhbnROb2Rlc0ZvckluZGV4KHQuaW5kZXgpLG49MCxpPWwubGVuZ3RoO2k+bjtuKyspe2lmKGU9bFtuXSxvPWQoZSksdC5vZmZzZXQ8PXUrbylpZihjKGUpKXtpZihyPWUscz11LHQub2Zmc2V0PT09cyYmYShyKSlicmVha31lbHNlIHJ8fChyPWUscz11KTtpZih1Kz1vLHU+dC5vZmZzZXQpYnJlYWt9cmV0dXJuW3Isc119LGUucHJvdG90eXBlLmZpbmRBdHRhY2htZW50RWxlbWVudFBhcmVudEZvck5vZGU9ZnVuY3Rpb24odCl7Zm9yKDt0JiZ0IT09dGhpcy5lbGVtZW50Oyl7aWYoaSh0KSlyZXR1cm4gdDt0PXQucGFyZW50Tm9kZX19LGUucHJvdG90eXBlLmdldFNpZ25pZmljYW50Tm9kZXNGb3JJbmRleD1mdW5jdGlvbih0KXt2YXIgZSxuLGksbyxyO2ZvcihpPVtdLHI9aCh0aGlzLmVsZW1lbnQse3VzaW5nRmlsdGVyOnB9KSxvPSExO3IubmV4dE5vZGUoKTspaWYobj1yLmN1cnJlbnROb2RlLHMobikpe2lmKFwidW5kZWZpbmVkXCIhPXR5cGVvZiBlJiZudWxsIT09ZT9lKys6ZT0wLGU9PT10KW89ITA7ZWxzZSBpZihvKWJyZWFrfWVsc2UgbyYmaS5wdXNoKG4pO3JldHVybiBpfSxkPWZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiB0Lm5vZGVUeXBlPT09Tm9kZS5URVhUX05PREU/YSh0KT8wOihlPXQudGV4dENvbnRlbnQsZS5sZW5ndGgpOlwiYnJcIj09PWwodCl8fGkodCk/MTowfSxwPWZ1bmN0aW9uKHQpe3JldHVybiBnKHQpPT09Tm9kZUZpbHRlci5GSUxURVJfQUNDRVBUP2YodCk6Tm9kZUZpbHRlci5GSUxURVJfUkVKRUNUfSxnPWZ1bmN0aW9uKHQpe3JldHVybiB1KHQpP05vZGVGaWx0ZXIuRklMVEVSX1JFSkVDVDpOb2RlRmlsdGVyLkZJTFRFUl9BQ0NFUFR9LGY9ZnVuY3Rpb24odCl7cmV0dXJuIGkodC5wYXJlbnROb2RlKT9Ob2RlRmlsdGVyLkZJTFRFUl9SRUpFQ1Q6Tm9kZUZpbHRlci5GSUxURVJfQUNDRVBUfSxlfSgpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpPVtdLnNsaWNlO3Q9ZS5nZXRET01SYW5nZSxuPWUuc2V0RE9NUmFuZ2UsZS5Qb2ludE1hcHBlcj1mdW5jdGlvbigpe2Z1bmN0aW9uIGUoKXt9cmV0dXJuIGUucHJvdG90eXBlLmNyZWF0ZURPTVJhbmdlRnJvbVBvaW50PWZ1bmN0aW9uKGUpe3ZhciBpLG8scixzLGEsdSxjLGw7aWYoYz1lLngsbD1lLnksZG9jdW1lbnQuY2FyZXRQb3NpdGlvbkZyb21Qb2ludClyZXR1cm4gYT1kb2N1bWVudC5jYXJldFBvc2l0aW9uRnJvbVBvaW50KGMsbCkscj1hLm9mZnNldE5vZGUsbz1hLm9mZnNldCxpPWRvY3VtZW50LmNyZWF0ZVJhbmdlKCksaS5zZXRTdGFydChyLG8pLGk7aWYoZG9jdW1lbnQuY2FyZXRSYW5nZUZyb21Qb2ludClyZXR1cm4gZG9jdW1lbnQuY2FyZXRSYW5nZUZyb21Qb2ludChjLGwpO2lmKGRvY3VtZW50LmJvZHkuY3JlYXRlVGV4dFJhbmdlKXtzPXQoKTt0cnl7dT1kb2N1bWVudC5ib2R5LmNyZWF0ZVRleHRSYW5nZSgpLHUubW92ZVRvUG9pbnQoYyxsKSx1LnNlbGVjdCgpfWNhdGNoKGgpe31yZXR1cm4gaT10KCksbihzKSxpfX0sZS5wcm90b3R5cGUuZ2V0Q2xpZW50UmVjdHNGb3JET01SYW5nZT1mdW5jdGlvbih0KXt2YXIgZSxuLG87cmV0dXJuIG49aS5jYWxsKHQuZ2V0Q2xpZW50UmVjdHMoKSksbz1uWzBdLGU9bltuLmxlbmd0aC0xXSxbbyxlXX0sZX0oKX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0LG49ZnVuY3Rpb24odCxlKXtyZXR1cm4gZnVuY3Rpb24oKXtyZXR1cm4gdC5hcHBseShlLGFyZ3VtZW50cyl9fSxpPWZ1bmN0aW9uKHQsZSl7ZnVuY3Rpb24gbigpe3RoaXMuY29uc3RydWN0b3I9dH1mb3IodmFyIGkgaW4gZSlvLmNhbGwoZSxpKSYmKHRbaV09ZVtpXSk7cmV0dXJuIG4ucHJvdG90eXBlPWUucHJvdG90eXBlLHQucHJvdG90eXBlPW5ldyBuLHQuX19zdXBlcl9fPWUucHJvdG90eXBlLHR9LG89e30uaGFzT3duUHJvcGVydHkscj1bXS5pbmRleE9mfHxmdW5jdGlvbih0KXtmb3IodmFyIGU9MCxuPXRoaXMubGVuZ3RoO24+ZTtlKyspaWYoZSBpbiB0aGlzJiZ0aGlzW2VdPT09dClyZXR1cm4gZTtyZXR1cm4tMX07dD1lLmdldERPTVJhbmdlLGUuU2VsZWN0aW9uQ2hhbmdlT2JzZXJ2ZXI9ZnVuY3Rpb24oZSl7ZnVuY3Rpb24gbygpe3RoaXMucnVuPW4odGhpcy5ydW4sdGhpcyksdGhpcy51cGRhdGU9bih0aGlzLnVwZGF0ZSx0aGlzKSx0aGlzLnNlbGVjdGlvbk1hbmFnZXJzPVtdfXZhciBzO3JldHVybiBpKG8sZSksby5wcm90b3R5cGUuc3RhcnQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zdGFydGVkP3ZvaWQgMDoodGhpcy5zdGFydGVkPSEwLFwib25zZWxlY3Rpb25jaGFuZ2VcImluIGRvY3VtZW50P2RvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJzZWxlY3Rpb25jaGFuZ2VcIix0aGlzLnVwZGF0ZSwhMCk6dGhpcy5ydW4oKSl9LG8ucHJvdG90eXBlLnN0b3A9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zdGFydGVkPyh0aGlzLnN0YXJ0ZWQ9ITEsZG9jdW1lbnQucmVtb3ZlRXZlbnRMaXN0ZW5lcihcInNlbGVjdGlvbmNoYW5nZVwiLHRoaXMudXBkYXRlLCEwKSk6dm9pZCAwfSxvLnByb3RvdHlwZS5yZWdpc3RlclNlbGVjdGlvbk1hbmFnZXI9ZnVuY3Rpb24odCl7cmV0dXJuIHIuY2FsbCh0aGlzLnNlbGVjdGlvbk1hbmFnZXJzLHQpPDA/KHRoaXMuc2VsZWN0aW9uTWFuYWdlcnMucHVzaCh0KSx0aGlzLnN0YXJ0KCkpOnZvaWQgMH0sby5wcm90b3R5cGUudW5yZWdpc3RlclNlbGVjdGlvbk1hbmFnZXI9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHRoaXMuc2VsZWN0aW9uTWFuYWdlcnM9ZnVuY3Rpb24oKXt2YXIgbixpLG8scjtmb3Iobz10aGlzLnNlbGVjdGlvbk1hbmFnZXJzLHI9W10sbj0wLGk9by5sZW5ndGg7aT5uO24rKyllPW9bbl0sZSE9PXQmJnIucHVzaChlKTtyZXR1cm4gcn0uY2FsbCh0aGlzKSwwPT09dGhpcy5zZWxlY3Rpb25NYW5hZ2Vycy5sZW5ndGg/dGhpcy5zdG9wKCk6dm9pZCAwfSxvLnByb3RvdHlwZS5ub3RpZnlTZWxlY3Rpb25NYW5hZ2Vyc09mU2VsZWN0aW9uQ2hhbmdlPWZ1bmN0aW9uKCl7dmFyIHQsZSxuLGksbztmb3Iobj10aGlzLnNlbGVjdGlvbk1hbmFnZXJzLGk9W10sdD0wLGU9bi5sZW5ndGg7ZT50O3QrKylvPW5bdF0saS5wdXNoKG8uc2VsZWN0aW9uRGlkQ2hhbmdlKCkpO3JldHVybiBpfSxvLnByb3RvdHlwZS51cGRhdGU9ZnVuY3Rpb24oKXt2YXIgZTtyZXR1cm4gZT10KCkscyhlLHRoaXMuZG9tUmFuZ2UpP3ZvaWQgMDoodGhpcy5kb21SYW5nZT1lLHRoaXMubm90aWZ5U2VsZWN0aW9uTWFuYWdlcnNPZlNlbGVjdGlvbkNoYW5nZSgpKX0sby5wcm90b3R5cGUucmVzZXQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5kb21SYW5nZT1udWxsLHRoaXMudXBkYXRlKCl9LG8ucHJvdG90eXBlLnJ1bj1mdW5jdGlvbigpe3JldHVybiB0aGlzLnN0YXJ0ZWQ/KHRoaXMudXBkYXRlKCkscmVxdWVzdEFuaW1hdGlvbkZyYW1lKHRoaXMucnVuKSk6dm9pZCAwfSxzPWZ1bmN0aW9uKHQsZSl7cmV0dXJuKG51bGwhPXQ/dC5zdGFydENvbnRhaW5lcjp2b2lkIDApPT09KG51bGwhPWU/ZS5zdGFydENvbnRhaW5lcjp2b2lkIDApJiYobnVsbCE9dD90LnN0YXJ0T2Zmc2V0OnZvaWQgMCk9PT0obnVsbCE9ZT9lLnN0YXJ0T2Zmc2V0OnZvaWQgMCkmJihudWxsIT10P3QuZW5kQ29udGFpbmVyOnZvaWQgMCk9PT0obnVsbCE9ZT9lLmVuZENvbnRhaW5lcjp2b2lkIDApJiYobnVsbCE9dD90LmVuZE9mZnNldDp2b2lkIDApPT09KG51bGwhPWU/ZS5lbmRPZmZzZXQ6dm9pZCAwKX0sb30oZS5CYXNpY09iamVjdCksbnVsbD09ZS5zZWxlY3Rpb25DaGFuZ2VPYnNlcnZlciYmKGUuc2VsZWN0aW9uQ2hhbmdlT2JzZXJ2ZXI9bmV3IGUuU2VsZWN0aW9uQ2hhbmdlT2JzZXJ2ZXIpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scixzLGEsdSxjLGwsaD1mdW5jdGlvbih0LGUpe3JldHVybiBmdW5jdGlvbigpe3JldHVybiB0LmFwcGx5KGUsYXJndW1lbnRzKX19LHA9ZnVuY3Rpb24odCxlKXtmdW5jdGlvbiBuKCl7dGhpcy5jb25zdHJ1Y3Rvcj10fWZvcih2YXIgaSBpbiBlKWQuY2FsbChlLGkpJiYodFtpXT1lW2ldKTtyZXR1cm4gbi5wcm90b3R5cGU9ZS5wcm90b3R5cGUsdC5wcm90b3R5cGU9bmV3IG4sdC5fX3N1cGVyX189ZS5wcm90b3R5cGUsdH0sZD17fS5oYXNPd25Qcm9wZXJ0eTtpPWUuZ2V0RE9NU2VsZWN0aW9uLG49ZS5nZXRET01SYW5nZSxsPWUuc2V0RE9NUmFuZ2UsdD1lLmVsZW1lbnRDb250YWluc05vZGUscz1lLm5vZGVJc0N1cnNvclRhcmdldCxyPWUuaW5uZXJFbGVtZW50SXNBY3RpdmUsbz1lLmhhbmRsZUV2ZW50LGE9ZS5ub3JtYWxpemVSYW5nZSx1PWUucmFuZ2VJc0NvbGxhcHNlZCxjPWUucmFuZ2VzQXJlRXF1YWwsZS5TZWxlY3Rpb25NYW5hZ2VyPWZ1bmN0aW9uKGQpe2Z1bmN0aW9uIGYodCl7dGhpcy5lbGVtZW50PXQsdGhpcy5zZWxlY3Rpb25EaWRDaGFuZ2U9aCh0aGlzLnNlbGVjdGlvbkRpZENoYW5nZSx0aGlzKSx0aGlzLmRpZE1vdXNlRG93bj1oKHRoaXMuZGlkTW91c2VEb3duLHRoaXMpLHRoaXMubG9jYXRpb25NYXBwZXI9bmV3IGUuTG9jYXRpb25NYXBwZXIodGhpcy5lbGVtZW50KSx0aGlzLnBvaW50TWFwcGVyPW5ldyBlLlBvaW50TWFwcGVyLHRoaXMubG9ja0NvdW50PTAsbyhcIm1vdXNlZG93blwiLHtvbkVsZW1lbnQ6dGhpcy5lbGVtZW50LHdpdGhDYWxsYmFjazp0aGlzLmRpZE1vdXNlRG93bn0pfXJldHVybiBwKGYsZCksZi5wcm90b3R5cGUuZ2V0TG9jYXRpb25SYW5nZT1mdW5jdGlvbih0KXt2YXIgZSxpO3JldHVybiBudWxsPT10JiYodD17fSksZT10LnN0cmljdD09PSExP3RoaXMuY3JlYXRlTG9jYXRpb25SYW5nZUZyb21ET01SYW5nZShuKCkse3N0cmljdDohMX0pOnQuaWdub3JlTG9jaz90aGlzLmN1cnJlbnRMb2NhdGlvblJhbmdlOm51bGwhPShpPXRoaXMubG9ja2VkTG9jYXRpb25SYW5nZSk/aTp0aGlzLmN1cnJlbnRMb2NhdGlvblJhbmdlfSxmLnByb3RvdHlwZS5zZXRMb2NhdGlvblJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlO2lmKCF0aGlzLmxvY2tlZExvY2F0aW9uUmFuZ2UpcmV0dXJuIHQ9YSh0KSwoZT10aGlzLmNyZWF0ZURPTVJhbmdlRnJvbUxvY2F0aW9uUmFuZ2UodCkpPyhsKGUpLHRoaXMudXBkYXRlQ3VycmVudExvY2F0aW9uUmFuZ2UodCkpOnZvaWQgMH0sZi5wcm90b3R5cGUuc2V0TG9jYXRpb25SYW5nZUZyb21Qb2ludFJhbmdlPWZ1bmN0aW9uKHQpe3ZhciBlLG47cmV0dXJuIHQ9YSh0KSxuPXRoaXMuZ2V0TG9jYXRpb25BdFBvaW50KHRbMF0pLGU9dGhpcy5nZXRMb2NhdGlvbkF0UG9pbnQodFsxXSksdGhpcy5zZXRMb2NhdGlvblJhbmdlKFtuLGVdKX0sZi5wcm90b3R5cGUuZ2V0Q2xpZW50UmVjdEF0TG9jYXRpb25SYW5nZT1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4oZT10aGlzLmNyZWF0ZURPTVJhbmdlRnJvbUxvY2F0aW9uUmFuZ2UodCkpP3RoaXMuZ2V0Q2xpZW50UmVjdHNGb3JET01SYW5nZShlKVsxXTp2b2lkIDB9LGYucHJvdG90eXBlLmxvY2F0aW9uSXNDdXJzb3JUYXJnZXQ9ZnVuY3Rpb24odCl7dmFyIGUsbixpO3JldHVybiBpPXRoaXMuZmluZE5vZGVBbmRPZmZzZXRGcm9tTG9jYXRpb24odCksZT1pWzBdLG49aVsxXSxzKGUpfSxmLnByb3RvdHlwZS5sb2NrPWZ1bmN0aW9uKCl7cmV0dXJuIDA9PT10aGlzLmxvY2tDb3VudCsrPyh0aGlzLnVwZGF0ZUN1cnJlbnRMb2NhdGlvblJhbmdlKCksdGhpcy5sb2NrZWRMb2NhdGlvblJhbmdlPXRoaXMuZ2V0TG9jYXRpb25SYW5nZSgpKTp2b2lkIDB9LGYucHJvdG90eXBlLnVubG9jaz1mdW5jdGlvbigpe3ZhciB0O3JldHVybiAwPT09LS10aGlzLmxvY2tDb3VudCYmKHQ9dGhpcy5sb2NrZWRMb2NhdGlvblJhbmdlLHRoaXMubG9ja2VkTG9jYXRpb25SYW5nZT1udWxsLG51bGwhPXQpP3RoaXMuc2V0TG9jYXRpb25SYW5nZSh0KTp2b2lkIDB9LGYucHJvdG90eXBlLmNsZWFyU2VsZWN0aW9uPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PWkoKSk/dC5yZW1vdmVBbGxSYW5nZXMoKTp2b2lkIDB9LGYucHJvdG90eXBlLnNlbGVjdGlvbklzQ29sbGFwc2VkPWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKG51bGwhPSh0PW4oKSk/dC5jb2xsYXBzZWQ6dm9pZCAwKT09PSEwfSxmLnByb3RvdHlwZS5zZWxlY3Rpb25Jc0V4cGFuZGVkPWZ1bmN0aW9uKCl7cmV0dXJuIXRoaXMuc2VsZWN0aW9uSXNDb2xsYXBzZWQoKX0sZi5wcm90b3R5cGUuY3JlYXRlTG9jYXRpb25SYW5nZUZyb21ET01SYW5nZT1mdW5jdGlvbih0LGUpe3ZhciBuLGk7aWYobnVsbCE9dCYmdGhpcy5kb21SYW5nZVdpdGhpbkVsZW1lbnQodCkmJihpPXRoaXMuZmluZExvY2F0aW9uRnJvbUNvbnRhaW5lckFuZE9mZnNldCh0LnN0YXJ0Q29udGFpbmVyLHQuc3RhcnRPZmZzZXQsZSkpKXJldHVybiB0LmNvbGxhcHNlZHx8KG49dGhpcy5maW5kTG9jYXRpb25Gcm9tQ29udGFpbmVyQW5kT2Zmc2V0KHQuZW5kQ29udGFpbmVyLHQuZW5kT2Zmc2V0LGUpKSxhKFtpLG5dKX0sZi5wcm94eU1ldGhvZChcImxvY2F0aW9uTWFwcGVyLmZpbmRMb2NhdGlvbkZyb21Db250YWluZXJBbmRPZmZzZXRcIiksZi5wcm94eU1ldGhvZChcImxvY2F0aW9uTWFwcGVyLmZpbmRDb250YWluZXJBbmRPZmZzZXRGcm9tTG9jYXRpb25cIiksZi5wcm94eU1ldGhvZChcImxvY2F0aW9uTWFwcGVyLmZpbmROb2RlQW5kT2Zmc2V0RnJvbUxvY2F0aW9uXCIpLGYucHJveHlNZXRob2QoXCJwb2ludE1hcHBlci5jcmVhdGVET01SYW5nZUZyb21Qb2ludFwiKSxmLnByb3h5TWV0aG9kKFwicG9pbnRNYXBwZXIuZ2V0Q2xpZW50UmVjdHNGb3JET01SYW5nZVwiKSxmLnByb3RvdHlwZS5kaWRNb3VzZURvd249ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5wYXVzZVRlbXBvcmFyaWx5KCl9LGYucHJvdG90eXBlLnBhdXNlVGVtcG9yYXJpbHk9ZnVuY3Rpb24oKXt2YXIgZSxuLGkscjtyZXR1cm4gdGhpcy5wYXVzZWQ9ITAsbj1mdW5jdGlvbihlKXtyZXR1cm4gZnVuY3Rpb24oKXt2YXIgbixvLHM7Zm9yKGUucGF1c2VkPSExLGNsZWFyVGltZW91dChyKSxvPTAscz1pLmxlbmd0aDtzPm87bysrKW49aVtvXSxuLmRlc3Ryb3koKTtyZXR1cm4gdChkb2N1bWVudCxlLmVsZW1lbnQpP2Uuc2VsZWN0aW9uRGlkQ2hhbmdlKCk6dm9pZCAwfX0odGhpcykscj1zZXRUaW1lb3V0KG4sMjAwKSxpPWZ1bmN0aW9uKCl7dmFyIHQsaSxyLHM7Zm9yKHI9W1wibW91c2Vtb3ZlXCIsXCJrZXlkb3duXCJdLHM9W10sdD0wLGk9ci5sZW5ndGg7aT50O3QrKyllPXJbdF0scy5wdXNoKG8oZSx7b25FbGVtZW50OmRvY3VtZW50LHdpdGhDYWxsYmFjazpufSkpO3JldHVybiBzfSgpfSxmLnByb3RvdHlwZS5zZWxlY3Rpb25EaWRDaGFuZ2U9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5wYXVzZWR8fHIodGhpcy5lbGVtZW50KT92b2lkIDA6dGhpcy51cGRhdGVDdXJyZW50TG9jYXRpb25SYW5nZSgpfSxmLnByb3RvdHlwZS51cGRhdGVDdXJyZW50TG9jYXRpb25SYW5nZT1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4obnVsbCE9dD90OnQ9dGhpcy5jcmVhdGVMb2NhdGlvblJhbmdlRnJvbURPTVJhbmdlKG4oKSkpJiYhYyh0LHRoaXMuY3VycmVudExvY2F0aW9uUmFuZ2UpPyh0aGlzLmN1cnJlbnRMb2NhdGlvblJhbmdlPXQsbnVsbCE9KGU9dGhpcy5kZWxlZ2F0ZSkmJlwiZnVuY3Rpb25cIj09dHlwZW9mIGUubG9jYXRpb25SYW5nZURpZENoYW5nZT9lLmxvY2F0aW9uUmFuZ2VEaWRDaGFuZ2UodGhpcy5jdXJyZW50TG9jYXRpb25SYW5nZS5zbGljZSgwKSk6dm9pZCAwKTp2b2lkIDB9LGYucHJvdG90eXBlLmNyZWF0ZURPTVJhbmdlRnJvbUxvY2F0aW9uUmFuZ2U9ZnVuY3Rpb24odCl7dmFyIGUsbixpLG87cmV0dXJuIGk9dGhpcy5maW5kQ29udGFpbmVyQW5kT2Zmc2V0RnJvbUxvY2F0aW9uKHRbMF0pLG49dSh0KT9pOm51bGwhPShvPXRoaXMuZmluZENvbnRhaW5lckFuZE9mZnNldEZyb21Mb2NhdGlvbih0WzFdKSk/bzppLG51bGwhPWkmJm51bGwhPW4/KGU9ZG9jdW1lbnQuY3JlYXRlUmFuZ2UoKSxlLnNldFN0YXJ0LmFwcGx5KGUsaSksZS5zZXRFbmQuYXBwbHkoZSxuKSxlKTp2b2lkIDB9LGYucHJvdG90eXBlLmdldExvY2F0aW9uQXRQb2ludD1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybihlPXRoaXMuY3JlYXRlRE9NUmFuZ2VGcm9tUG9pbnQodCkpJiZudWxsIT0obj10aGlzLmNyZWF0ZUxvY2F0aW9uUmFuZ2VGcm9tRE9NUmFuZ2UoZSkpP25bMF06dm9pZCAwfSxmLnByb3RvdHlwZS5kb21SYW5nZVdpdGhpbkVsZW1lbnQ9ZnVuY3Rpb24oZSl7cmV0dXJuIGUuY29sbGFwc2VkP3QodGhpcy5lbGVtZW50LGUuc3RhcnRDb250YWluZXIpOnQodGhpcy5lbGVtZW50LGUuc3RhcnRDb250YWluZXIpJiZ0KHRoaXMuZWxlbWVudCxlLmVuZENvbnRhaW5lcil9LGZ9KGUuQmFzaWNPYmplY3QpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7dmFyIHQsbixpLG8scj1mdW5jdGlvbih0LGUpe2Z1bmN0aW9uIG4oKXt0aGlzLmNvbnN0cnVjdG9yPXR9Zm9yKHZhciBpIGluIGUpcy5jYWxsKGUsaSkmJih0W2ldPWVbaV0pO3JldHVybiBuLnByb3RvdHlwZT1lLnByb3RvdHlwZSx0LnByb3RvdHlwZT1uZXcgbix0Ll9fc3VwZXJfXz1lLnByb3RvdHlwZSx0fSxzPXt9Lmhhc093blByb3BlcnR5LGE9W10uc2xpY2U7aT1lLnJhbmdlSXNDb2xsYXBzZWQsbz1lLnJhbmdlc0FyZUVxdWFsLG49ZS5vYmplY3RzQXJlRXF1YWwsdD1lLmdldEJsb2NrQ29uZmlnLGUuRWRpdG9yQ29udHJvbGxlcj1mdW5jdGlvbihzKXtmdW5jdGlvbiB1KHQpe3ZhciBuLGk7dGhpcy5lZGl0b3JFbGVtZW50PXQuZWRpdG9yRWxlbWVudCxuPXQuZG9jdW1lbnQsaT10Lmh0bWwsdGhpcy5zZWxlY3Rpb25NYW5hZ2VyPW5ldyBlLlNlbGVjdGlvbk1hbmFnZXIodGhpcy5lZGl0b3JFbGVtZW50KSx0aGlzLnNlbGVjdGlvbk1hbmFnZXIuZGVsZWdhdGU9dGhpcyx0aGlzLmNvbXBvc2l0aW9uPW5ldyBlLkNvbXBvc2l0aW9uLHRoaXMuY29tcG9zaXRpb24uZGVsZWdhdGU9dGhpcyx0aGlzLmF0dGFjaG1lbnRNYW5hZ2VyPW5ldyBlLkF0dGFjaG1lbnRNYW5hZ2VyKHRoaXMuY29tcG9zaXRpb24uZ2V0QXR0YWNobWVudHMoKSksdGhpcy5hdHRhY2htZW50TWFuYWdlci5kZWxlZ2F0ZT10aGlzLHRoaXMuaW5wdXRDb250cm9sbGVyPW5ldyhlW1wiTGV2ZWxcIitlLmNvbmZpZy5pbnB1dC5nZXRMZXZlbCgpK1wiSW5wdXRDb250cm9sbGVyXCJdKSh0aGlzLmVkaXRvckVsZW1lbnQpLHRoaXMuaW5wdXRDb250cm9sbGVyLmRlbGVnYXRlPXRoaXMsdGhpcy5pbnB1dENvbnRyb2xsZXIucmVzcG9uZGVyPXRoaXMuY29tcG9zaXRpb24sdGhpcy5jb21wb3NpdGlvbkNvbnRyb2xsZXI9bmV3IGUuQ29tcG9zaXRpb25Db250cm9sbGVyKHRoaXMuZWRpdG9yRWxlbWVudCx0aGlzLmNvbXBvc2l0aW9uKSx0aGlzLmNvbXBvc2l0aW9uQ29udHJvbGxlci5kZWxlZ2F0ZT10aGlzLHRoaXMudG9vbGJhckNvbnRyb2xsZXI9bmV3IGUuVG9vbGJhckNvbnRyb2xsZXIodGhpcy5lZGl0b3JFbGVtZW50LnRvb2xiYXJFbGVtZW50KSx0aGlzLnRvb2xiYXJDb250cm9sbGVyLmRlbGVnYXRlPXRoaXMsdGhpcy5lZGl0b3I9bmV3IGUuRWRpdG9yKHRoaXMuY29tcG9zaXRpb24sdGhpcy5zZWxlY3Rpb25NYW5hZ2VyLHRoaXMuZWRpdG9yRWxlbWVudCksbnVsbCE9bj90aGlzLmVkaXRvci5sb2FkRG9jdW1lbnQobik6dGhpcy5lZGl0b3IubG9hZEhUTUwoaSl9dmFyIGM7cmV0dXJuIHIodSxzKSx1LnByb3RvdHlwZS5yZWdpc3RlclNlbGVjdGlvbk1hbmFnZXI9ZnVuY3Rpb24oKXtyZXR1cm4gZS5zZWxlY3Rpb25DaGFuZ2VPYnNlcnZlci5yZWdpc3RlclNlbGVjdGlvbk1hbmFnZXIodGhpcy5zZWxlY3Rpb25NYW5hZ2VyKX0sdS5wcm90b3R5cGUudW5yZWdpc3RlclNlbGVjdGlvbk1hbmFnZXI9ZnVuY3Rpb24oKXtyZXR1cm4gZS5zZWxlY3Rpb25DaGFuZ2VPYnNlcnZlci51bnJlZ2lzdGVyU2VsZWN0aW9uTWFuYWdlcih0aGlzLnNlbGVjdGlvbk1hbmFnZXIpfSx1LnByb3RvdHlwZS5yZW5kZXI9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbkNvbnRyb2xsZXIucmVuZGVyKCl9LHUucHJvdG90eXBlLnJlcGFyc2U9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbi5yZXBsYWNlSFRNTCh0aGlzLmVkaXRvckVsZW1lbnQuaW5uZXJIVE1MKX0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25EaWRDaGFuZ2VEb2N1bWVudD1mdW5jdGlvbigpe3JldHVybiB0aGlzLm5vdGlmeUVkaXRvckVsZW1lbnQoXCJkb2N1bWVudC1jaGFuZ2VcIiksdGhpcy5oYW5kbGluZ0lucHV0P3ZvaWQgMDp0aGlzLnJlbmRlcigpfSx1LnByb3RvdHlwZS5jb21wb3NpdGlvbkRpZENoYW5nZUN1cnJlbnRBdHRyaWJ1dGVzPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmN1cnJlbnRBdHRyaWJ1dGVzPXQsdGhpcy50b29sYmFyQ29udHJvbGxlci51cGRhdGVBdHRyaWJ1dGVzKHRoaXMuY3VycmVudEF0dHJpYnV0ZXMpLHRoaXMudXBkYXRlQ3VycmVudEFjdGlvbnMoKSx0aGlzLm5vdGlmeUVkaXRvckVsZW1lbnQoXCJhdHRyaWJ1dGVzLWNoYW5nZVwiLHthdHRyaWJ1dGVzOnRoaXMuY3VycmVudEF0dHJpYnV0ZXN9KX0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25EaWRQZXJmb3JtSW5zZXJ0aW9uQXRSYW5nZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5wYXN0aW5nP3RoaXMucGFzdGVkUmFuZ2U9dDp2b2lkIDB9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uU2hvdWxkQWNjZXB0RmlsZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiZmlsZS1hY2NlcHRcIix7ZmlsZTp0fSl9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uRGlkQWRkQXR0YWNobWVudD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT10aGlzLmF0dGFjaG1lbnRNYW5hZ2VyLm1hbmFnZUF0dGFjaG1lbnQodCksdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiYXR0YWNobWVudC1hZGRcIix7YXR0YWNobWVudDplfSl9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uRGlkRWRpdEF0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7dmFyIGU7cmV0dXJuIHRoaXMuY29tcG9zaXRpb25Db250cm9sbGVyLnJlcmVuZGVyVmlld0Zvck9iamVjdCh0KSxlPXRoaXMuYXR0YWNobWVudE1hbmFnZXIubWFuYWdlQXR0YWNobWVudCh0KSx0aGlzLm5vdGlmeUVkaXRvckVsZW1lbnQoXCJhdHRhY2htZW50LWVkaXRcIix7YXR0YWNobWVudDplfSksdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiY2hhbmdlXCIpfSx1LnByb3RvdHlwZS5jb21wb3NpdGlvbkRpZENoYW5nZUF0dGFjaG1lbnRQcmV2aWV3VVJMPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uQ29udHJvbGxlci5pbnZhbGlkYXRlVmlld0Zvck9iamVjdCh0KSx0aGlzLm5vdGlmeUVkaXRvckVsZW1lbnQoXCJjaGFuZ2VcIil9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uRGlkUmVtb3ZlQXR0YWNobWVudD1mdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gZT10aGlzLmF0dGFjaG1lbnRNYW5hZ2VyLnVubWFuYWdlQXR0YWNobWVudCh0KSx0aGlzLm5vdGlmeUVkaXRvckVsZW1lbnQoXCJhdHRhY2htZW50LXJlbW92ZVwiLHthdHRhY2htZW50OmV9KX0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25EaWRTdGFydEVkaXRpbmdBdHRhY2htZW50PWZ1bmN0aW9uKHQsZSl7cmV0dXJuIHRoaXMuYXR0YWNobWVudExvY2F0aW9uUmFuZ2U9dGhpcy5jb21wb3NpdGlvbi5kb2N1bWVudC5nZXRMb2NhdGlvblJhbmdlT2ZBdHRhY2htZW50KHQpLHRoaXMuY29tcG9zaXRpb25Db250cm9sbGVyLmluc3RhbGxBdHRhY2htZW50RWRpdG9yRm9yQXR0YWNobWVudCh0LGUpLHRoaXMuc2VsZWN0aW9uTWFuYWdlci5zZXRMb2NhdGlvblJhbmdlKHRoaXMuYXR0YWNobWVudExvY2F0aW9uUmFuZ2UpfSx1LnByb3RvdHlwZS5jb21wb3NpdGlvbkRpZFN0b3BFZGl0aW5nQXR0YWNobWVudD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmNvbXBvc2l0aW9uQ29udHJvbGxlci51bmluc3RhbGxBdHRhY2htZW50RWRpdG9yKCksdGhpcy5hdHRhY2htZW50TG9jYXRpb25SYW5nZT1udWxsfSx1LnByb3RvdHlwZS5jb21wb3NpdGlvbkRpZFJlcXVlc3RDaGFuZ2luZ1NlbGVjdGlvblRvTG9jYXRpb25SYW5nZT1mdW5jdGlvbih0KXtyZXR1cm4hdGhpcy5sb2FkaW5nU25hcHNob3R8fHRoaXMuaXNGb2N1c2VkKCk/KHRoaXMucmVxdWVzdGVkTG9jYXRpb25SYW5nZT10LHRoaXMuY29tcG9zaXRpb25SZXZpc2lvbldoZW5Mb2NhdGlvblJhbmdlUmVxdWVzdGVkPXRoaXMuY29tcG9zaXRpb24ucmV2aXNpb24sdGhpcy5oYW5kbGluZ0lucHV0P3ZvaWQgMDp0aGlzLnJlbmRlcigpKTp2b2lkIDB9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uV2lsbExvYWRTbmFwc2hvdD1mdW5jdGlvbigpe3JldHVybiB0aGlzLmxvYWRpbmdTbmFwc2hvdD0hMH0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25EaWRMb2FkU25hcHNob3Q9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbkNvbnRyb2xsZXIucmVmcmVzaFZpZXdDYWNoZSgpLHRoaXMucmVuZGVyKCksdGhpcy5sb2FkaW5nU25hcHNob3Q9ITF9LHUucHJvdG90eXBlLmdldFNlbGVjdGlvbk1hbmFnZXI9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5zZWxlY3Rpb25NYW5hZ2VyfSx1LnByb3h5TWV0aG9kKFwiZ2V0U2VsZWN0aW9uTWFuYWdlcigpLnNldExvY2F0aW9uUmFuZ2VcIiksdS5wcm94eU1ldGhvZChcImdldFNlbGVjdGlvbk1hbmFnZXIoKS5nZXRMb2NhdGlvblJhbmdlXCIpLHUucHJvdG90eXBlLmF0dGFjaG1lbnRNYW5hZ2VyRGlkUmVxdWVzdFJlbW92YWxPZkF0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMucmVtb3ZlQXR0YWNobWVudCh0KX0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25Db250cm9sbGVyV2lsbFN5bmNEb2N1bWVudFZpZXc9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pbnB1dENvbnRyb2xsZXIuZWRpdG9yV2lsbFN5bmNEb2N1bWVudFZpZXcoKSx0aGlzLnNlbGVjdGlvbk1hbmFnZXIubG9jaygpLHRoaXMuc2VsZWN0aW9uTWFuYWdlci5jbGVhclNlbGVjdGlvbigpfSx1LnByb3RvdHlwZS5jb21wb3NpdGlvbkNvbnRyb2xsZXJEaWRTeW5jRG9jdW1lbnRWaWV3PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuaW5wdXRDb250cm9sbGVyLmVkaXRvckRpZFN5bmNEb2N1bWVudFZpZXcoKSx0aGlzLnNlbGVjdGlvbk1hbmFnZXIudW5sb2NrKCksdGhpcy51cGRhdGVDdXJyZW50QWN0aW9ucygpLHRoaXMubm90aWZ5RWRpdG9yRWxlbWVudChcInN5bmNcIil9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZFJlbmRlcj1mdW5jdGlvbigpe3JldHVybiBudWxsIT10aGlzLnJlcXVlc3RlZExvY2F0aW9uUmFuZ2UmJih0aGlzLmNvbXBvc2l0aW9uUmV2aXNpb25XaGVuTG9jYXRpb25SYW5nZVJlcXVlc3RlZD09PXRoaXMuY29tcG9zaXRpb24ucmV2aXNpb24mJnRoaXMuc2VsZWN0aW9uTWFuYWdlci5zZXRMb2NhdGlvblJhbmdlKHRoaXMucmVxdWVzdGVkTG9jYXRpb25SYW5nZSksdGhpcy5yZXF1ZXN0ZWRMb2NhdGlvblJhbmdlPW51bGwsdGhpcy5jb21wb3NpdGlvblJldmlzaW9uV2hlbkxvY2F0aW9uUmFuZ2VSZXF1ZXN0ZWQ9bnVsbCksdGhpcy5yZW5kZXJlZENvbXBvc2l0aW9uUmV2aXNpb24hPT10aGlzLmNvbXBvc2l0aW9uLnJldmlzaW9uJiYodGhpcy5ydW5FZGl0b3JGaWx0ZXJzKCksdGhpcy5jb21wb3NpdGlvbi51cGRhdGVDdXJyZW50QXR0cmlidXRlcygpLHRoaXMubm90aWZ5RWRpdG9yRWxlbWVudChcInJlbmRlclwiKSksdGhpcy5yZW5kZXJlZENvbXBvc2l0aW9uUmV2aXNpb249dGhpcy5jb21wb3NpdGlvbi5yZXZpc2lvbn0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25Db250cm9sbGVyRGlkRm9jdXM9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5pc0ZvY3VzZWRJbnZpc2libHkoKSYmdGhpcy5zZXRMb2NhdGlvblJhbmdlKHtpbmRleDowLG9mZnNldDowfSksdGhpcy50b29sYmFyQ29udHJvbGxlci5oaWRlRGlhbG9nKCksdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiZm9jdXNcIil9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZEJsdXI9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiYmx1clwiKX0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25Db250cm9sbGVyRGlkU2VsZWN0QXR0YWNobWVudD1mdW5jdGlvbih0LGUpe3JldHVybiB0aGlzLnRvb2xiYXJDb250cm9sbGVyLmhpZGVEaWFsb2coKSx0aGlzLmNvbXBvc2l0aW9uLmVkaXRBdHRhY2htZW50KHQsZSl9LHUucHJvdG90eXBlLmNvbXBvc2l0aW9uQ29udHJvbGxlckRpZFJlcXVlc3REZXNlbGVjdGluZ0F0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gZT1udWxsIT0obj10aGlzLmF0dGFjaG1lbnRMb2NhdGlvblJhbmdlKT9uOnRoaXMuY29tcG9zaXRpb24uZG9jdW1lbnQuZ2V0TG9jYXRpb25SYW5nZU9mQXR0YWNobWVudCh0KSx0aGlzLnNlbGVjdGlvbk1hbmFnZXIuc2V0TG9jYXRpb25SYW5nZShlWzFdKX0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25Db250cm9sbGVyV2lsbFVwZGF0ZUF0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuZWRpdG9yLnJlY29yZFVuZG9FbnRyeShcIkVkaXQgQXR0YWNobWVudFwiLHtjb250ZXh0OnQuaWQsY29uc29saWRhdGFibGU6ITB9KX0sdS5wcm90b3R5cGUuY29tcG9zaXRpb25Db250cm9sbGVyRGlkUmVxdWVzdFJlbW92YWxPZkF0dGFjaG1lbnQ9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMucmVtb3ZlQXR0YWNobWVudCh0KX0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyV2lsbEhhbmRsZUlucHV0PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuaGFuZGxpbmdJbnB1dD0hMCx0aGlzLnJlcXVlc3RlZFJlbmRlcj0hMX0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyRGlkUmVxdWVzdFJlbmRlcj1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlcXVlc3RlZFJlbmRlcj0hMH0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyRGlkSGFuZGxlSW5wdXQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5oYW5kbGluZ0lucHV0PSExLHRoaXMucmVxdWVzdGVkUmVuZGVyPyh0aGlzLnJlcXVlc3RlZFJlbmRlcj0hMSx0aGlzLnJlbmRlcigpKTp2b2lkIDB9LHUucHJvdG90eXBlLmlucHV0Q29udHJvbGxlckRpZEFsbG93VW5oYW5kbGVkSW5wdXQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiY2hhbmdlXCIpfSx1LnByb3RvdHlwZS5pbnB1dENvbnRyb2xsZXJEaWRSZXF1ZXN0UmVwYXJzZT1mdW5jdGlvbigpe3JldHVybiB0aGlzLnJlcGFyc2UoKX0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyV2lsbFBlcmZvcm1UeXBpbmc9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5yZWNvcmRUeXBpbmdVbmRvRW50cnkoKX0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyV2lsbFBlcmZvcm1Gb3JtYXR0aW5nPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnJlY29yZEZvcm1hdHRpbmdVbmRvRW50cnkodCl9LHUucHJvdG90eXBlLmlucHV0Q29udHJvbGxlcldpbGxDdXRUZXh0PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWRpdG9yLnJlY29yZFVuZG9FbnRyeShcIkN1dFwiKX0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyV2lsbFBhc3RlPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmVkaXRvci5yZWNvcmRVbmRvRW50cnkoXCJQYXN0ZVwiKSx0aGlzLnBhc3Rpbmc9ITAsdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiYmVmb3JlLXBhc3RlXCIse3Bhc3RlOnR9KX0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyRGlkUGFzdGU9ZnVuY3Rpb24odCl7cmV0dXJuIHQucmFuZ2U9dGhpcy5wYXN0ZWRSYW5nZSx0aGlzLnBhc3RlZFJhbmdlPW51bGwsdGhpcy5wYXN0aW5nPW51bGwsdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwicGFzdGVcIix7cGFzdGU6dH0pfSx1LnByb3RvdHlwZS5pbnB1dENvbnRyb2xsZXJXaWxsTW92ZVRleHQ9ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5lZGl0b3IucmVjb3JkVW5kb0VudHJ5KFwiTW92ZVwiKX0sdS5wcm90b3R5cGUuaW5wdXRDb250cm9sbGVyV2lsbEF0dGFjaEZpbGVzPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWRpdG9yLnJlY29yZFVuZG9FbnRyeShcIkRyb3AgRmlsZXNcIil9LHUucHJvdG90eXBlLmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtVW5kbz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmVkaXRvci51bmRvKCl9LHUucHJvdG90eXBlLmlucHV0Q29udHJvbGxlcldpbGxQZXJmb3JtUmVkbz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmVkaXRvci5yZWRvKCl9LHUucHJvdG90eXBlLmlucHV0Q29udHJvbGxlckRpZFJlY2VpdmVLZXlib2FyZENvbW1hbmQ9ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMudG9vbGJhckNvbnRyb2xsZXIuYXBwbHlLZXlib2FyZENvbW1hbmQodCl9LHUucHJvdG90eXBlLmlucHV0Q29udHJvbGxlckRpZFN0YXJ0RHJhZz1mdW5jdGlvbigpe3JldHVybiB0aGlzLmxvY2F0aW9uUmFuZ2VCZWZvcmVEcmFnPXRoaXMuc2VsZWN0aW9uTWFuYWdlci5nZXRMb2NhdGlvblJhbmdlKCl9LHUucHJvdG90eXBlLmlucHV0Q29udHJvbGxlckRpZFJlY2VpdmVEcmFnT3ZlclBvaW50PWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnNlbGVjdGlvbk1hbmFnZXIuc2V0TG9jYXRpb25SYW5nZUZyb21Qb2ludFJhbmdlKHQpfSx1LnByb3RvdHlwZS5pbnB1dENvbnRyb2xsZXJEaWRDYW5jZWxEcmFnPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuc2VsZWN0aW9uTWFuYWdlci5zZXRMb2NhdGlvblJhbmdlKHRoaXMubG9jYXRpb25SYW5nZUJlZm9yZURyYWcpLHRoaXMubG9jYXRpb25SYW5nZUJlZm9yZURyYWc9bnVsbH0sdS5wcm90b3R5cGUubG9jYXRpb25SYW5nZURpZENoYW5nZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5jb21wb3NpdGlvbi51cGRhdGVDdXJyZW50QXR0cmlidXRlcygpLHRoaXMudXBkYXRlQ3VycmVudEFjdGlvbnMoKSx0aGlzLmF0dGFjaG1lbnRMb2NhdGlvblJhbmdlJiYhbyh0aGlzLmF0dGFjaG1lbnRMb2NhdGlvblJhbmdlLHQpJiZ0aGlzLmNvbXBvc2l0aW9uLnN0b3BFZGl0aW5nQXR0YWNobWVudCgpLHRoaXMubm90aWZ5RWRpdG9yRWxlbWVudChcInNlbGVjdGlvbi1jaGFuZ2VcIil9LHUucHJvdG90eXBlLnRvb2xiYXJEaWRDbGlja0J1dHRvbj1mdW5jdGlvbigpe3JldHVybiB0aGlzLmdldExvY2F0aW9uUmFuZ2UoKT92b2lkIDA6dGhpcy5zZXRMb2NhdGlvblJhbmdlKHtpbmRleDowLG9mZnNldDowfSl9LHUucHJvdG90eXBlLnRvb2xiYXJEaWRJbnZva2VBY3Rpb249ZnVuY3Rpb24odCl7cmV0dXJuIHRoaXMuaW52b2tlQWN0aW9uKHQpfSx1LnByb3RvdHlwZS50b29sYmFyRGlkVG9nZ2xlQXR0cmlidXRlPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLnJlY29yZEZvcm1hdHRpbmdVbmRvRW50cnkodCksdGhpcy5jb21wb3NpdGlvbi50b2dnbGVDdXJyZW50QXR0cmlidXRlKHQpLHRoaXMucmVuZGVyKCksdGhpcy5zZWxlY3Rpb25Gcm96ZW4/dm9pZCAwOnRoaXMuZWRpdG9yRWxlbWVudC5mb2N1cygpfSx1LnByb3RvdHlwZS50b29sYmFyRGlkVXBkYXRlQXR0cmlidXRlPWZ1bmN0aW9uKHQsZSl7cmV0dXJuIHRoaXMucmVjb3JkRm9ybWF0dGluZ1VuZG9FbnRyeSh0KSx0aGlzLmNvbXBvc2l0aW9uLnNldEN1cnJlbnRBdHRyaWJ1dGUodCxlKSx0aGlzLnJlbmRlcigpLHRoaXMuc2VsZWN0aW9uRnJvemVuP3ZvaWQgMDp0aGlzLmVkaXRvckVsZW1lbnQuZm9jdXMoKX0sdS5wcm90b3R5cGUudG9vbGJhckRpZFJlbW92ZUF0dHJpYnV0ZT1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy5yZWNvcmRGb3JtYXR0aW5nVW5kb0VudHJ5KHQpLHRoaXMuY29tcG9zaXRpb24ucmVtb3ZlQ3VycmVudEF0dHJpYnV0ZSh0KSx0aGlzLnJlbmRlcigpLHRoaXMuc2VsZWN0aW9uRnJvemVuP3ZvaWQgMDp0aGlzLmVkaXRvckVsZW1lbnQuZm9jdXMoKX0sdS5wcm90b3R5cGUudG9vbGJhcldpbGxTaG93RGlhbG9nPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY29tcG9zaXRpb24uZXhwYW5kU2VsZWN0aW9uRm9yRWRpdGluZygpLHRoaXMuZnJlZXplU2VsZWN0aW9uKCl9LHUucHJvdG90eXBlLnRvb2xiYXJEaWRTaG93RGlhbG9nPWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLm5vdGlmeUVkaXRvckVsZW1lbnQoXCJ0b29sYmFyLWRpYWxvZy1zaG93XCIse2RpYWxvZ05hbWU6dH0pfSx1LnByb3RvdHlwZS50b29sYmFyRGlkSGlkZURpYWxvZz1mdW5jdGlvbih0KXtyZXR1cm4gdGhpcy50aGF3U2VsZWN0aW9uKCksdGhpcy5lZGl0b3JFbGVtZW50LmZvY3VzKCksdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwidG9vbGJhci1kaWFsb2ctaGlkZVwiLHtkaWFsb2dOYW1lOnR9KX0sdS5wcm90b3R5cGUuZnJlZXplU2VsZWN0aW9uPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuc2VsZWN0aW9uRnJvemVuP3ZvaWQgMDoodGhpcy5zZWxlY3Rpb25NYW5hZ2VyLmxvY2soKSx0aGlzLmNvbXBvc2l0aW9uLmZyZWV6ZVNlbGVjdGlvbigpLHRoaXMuc2VsZWN0aW9uRnJvemVuPSEwLHRoaXMucmVuZGVyKCkpfSx1LnByb3RvdHlwZS50aGF3U2VsZWN0aW9uPWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuc2VsZWN0aW9uRnJvemVuPyh0aGlzLmNvbXBvc2l0aW9uLnRoYXdTZWxlY3Rpb24oKSx0aGlzLnNlbGVjdGlvbk1hbmFnZXIudW5sb2NrKCksdGhpcy5zZWxlY3Rpb25Gcm96ZW49ITEsdGhpcy5yZW5kZXIoKSk6dm9pZCAwfSx1LnByb3RvdHlwZS5hY3Rpb25zPXt1bmRvOnt0ZXN0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWRpdG9yLmNhblVuZG8oKX0scGVyZm9ybTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmVkaXRvci51bmRvKCl9fSxyZWRvOnt0ZXN0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWRpdG9yLmNhblJlZG8oKX0scGVyZm9ybTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmVkaXRvci5yZWRvKCl9fSxsaW5rOnt0ZXN0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWRpdG9yLmNhbkFjdGl2YXRlQXR0cmlidXRlKFwiaHJlZlwiKX19LGluY3JlYXNlTmVzdGluZ0xldmVsOnt0ZXN0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWRpdG9yLmNhbkluY3JlYXNlTmVzdGluZ0xldmVsKCl9LHBlcmZvcm06ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5lZGl0b3IuaW5jcmVhc2VOZXN0aW5nTGV2ZWwoKSYmdGhpcy5yZW5kZXIoKX19LGRlY3JlYXNlTmVzdGluZ0xldmVsOnt0ZXN0OmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuZWRpdG9yLmNhbkRlY3JlYXNlTmVzdGluZ0xldmVsKCl9LHBlcmZvcm06ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5lZGl0b3IuZGVjcmVhc2VOZXN0aW5nTGV2ZWwoKSYmdGhpcy5yZW5kZXIoKX19LGF0dGFjaEZpbGVzOnt0ZXN0OmZ1bmN0aW9uKCl7cmV0dXJuITB9LHBlcmZvcm06ZnVuY3Rpb24oKXtyZXR1cm4gZS5jb25maWcuaW5wdXQucGlja0ZpbGVzKHRoaXMuZWRpdG9yLmluc2VydEZpbGVzKX19fSx1LnByb3RvdHlwZS5jYW5JbnZva2VBY3Rpb249ZnVuY3Rpb24odCl7dmFyIGUsbjtyZXR1cm4gdGhpcy5hY3Rpb25Jc0V4dGVybmFsKHQpPyEwOiEhKG51bGwhPShlPXRoaXMuYWN0aW9uc1t0XSkmJm51bGwhPShuPWUudGVzdCk/bi5jYWxsKHRoaXMpOnZvaWQgMCl9LHUucHJvdG90eXBlLmludm9rZUFjdGlvbj1mdW5jdGlvbih0KXt2YXIgZSxuO3JldHVybiB0aGlzLmFjdGlvbklzRXh0ZXJuYWwodCk/dGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiYWN0aW9uLWludm9rZVwiLHthY3Rpb25OYW1lOnR9KTpudWxsIT0oZT10aGlzLmFjdGlvbnNbdF0pJiZudWxsIT0obj1lLnBlcmZvcm0pP24uY2FsbCh0aGlzKTp2b2lkIDB9LHUucHJvdG90eXBlLmFjdGlvbklzRXh0ZXJuYWw9ZnVuY3Rpb24odCl7cmV0dXJuL154LS4vLnRlc3QodCl9LHUucHJvdG90eXBlLmdldEN1cnJlbnRBY3Rpb25zPWZ1bmN0aW9uKCl7dmFyIHQsZTtlPXt9O2Zvcih0IGluIHRoaXMuYWN0aW9ucyllW3RdPXRoaXMuY2FuSW52b2tlQWN0aW9uKHQpO3JldHVybiBlfSx1LnByb3RvdHlwZS51cGRhdGVDdXJyZW50QWN0aW9ucz1mdW5jdGlvbigpe3ZhciB0O3JldHVybiB0PXRoaXMuZ2V0Q3VycmVudEFjdGlvbnMoKSxuKHQsdGhpcy5jdXJyZW50QWN0aW9ucyk/dm9pZCAwOih0aGlzLmN1cnJlbnRBY3Rpb25zPXQsdGhpcy50b29sYmFyQ29udHJvbGxlci51cGRhdGVBY3Rpb25zKHRoaXMuY3VycmVudEFjdGlvbnMpLHRoaXMubm90aWZ5RWRpdG9yRWxlbWVudChcImFjdGlvbnMtY2hhbmdlXCIse2FjdGlvbnM6dGhpcy5jdXJyZW50QWN0aW9uc30pKX0sdS5wcm90b3R5cGUucnVuRWRpdG9yRmlsdGVycz1mdW5jdGlvbigpe3ZhciB0LGUsbixpLG8scixzLGE7Zm9yKGE9dGhpcy5jb21wb3NpdGlvbi5nZXRTbmFwc2hvdCgpLG89dGhpcy5lZGl0b3IuZmlsdGVycyxuPTAsaT1vLmxlbmd0aDtpPm47bisrKWU9b1tuXSx0PWEuZG9jdW1lbnQscz1hLnNlbGVjdGVkUmFuZ2UsYT1udWxsIT0ocj1lLmNhbGwodGhpcy5lZGl0b3IsYSkpP3I6e30sbnVsbD09YS5kb2N1bWVudCYmKGEuZG9jdW1lbnQ9dCksbnVsbD09YS5zZWxlY3RlZFJhbmdlJiYoYS5zZWxlY3RlZFJhbmdlPXMpO3JldHVybiBjKGEsdGhpcy5jb21wb3NpdGlvbi5nZXRTbmFwc2hvdCgpKT92b2lkIDA6dGhpcy5jb21wb3NpdGlvbi5sb2FkU25hcHNob3QoYSl9LGM9ZnVuY3Rpb24odCxlKXtyZXR1cm4gbyh0LnNlbGVjdGVkUmFuZ2UsZS5zZWxlY3RlZFJhbmdlKSYmdC5kb2N1bWVudC5pc0VxdWFsVG8oZS5kb2N1bWVudCl9LHUucHJvdG90eXBlLnVwZGF0ZUlucHV0RWxlbWVudD1mdW5jdGlvbigpe3ZhciB0LG47cmV0dXJuIHQ9dGhpcy5jb21wb3NpdGlvbkNvbnRyb2xsZXIuZ2V0U2VyaWFsaXphYmxlRWxlbWVudCgpLG49ZS5zZXJpYWxpemVUb0NvbnRlbnRUeXBlKHQsXCJ0ZXh0L2h0bWxcIiksdGhpcy5lZGl0b3JFbGVtZW50LnNldElucHV0RWxlbWVudFZhbHVlKG4pfSx1LnByb3RvdHlwZS5ub3RpZnlFZGl0b3JFbGVtZW50PWZ1bmN0aW9uKHQsZSl7c3dpdGNoKHQpe2Nhc2VcImRvY3VtZW50LWNoYW5nZVwiOnRoaXMuZG9jdW1lbnRDaGFuZ2VkU2luY2VMYXN0UmVuZGVyPSEwO2JyZWFrO2Nhc2VcInJlbmRlclwiOnRoaXMuZG9jdW1lbnRDaGFuZ2VkU2luY2VMYXN0UmVuZGVyJiYodGhpcy5kb2N1bWVudENoYW5nZWRTaW5jZUxhc3RSZW5kZXI9ITEsdGhpcy5ub3RpZnlFZGl0b3JFbGVtZW50KFwiY2hhbmdlXCIpKTticmVhaztjYXNlXCJjaGFuZ2VcIjpjYXNlXCJhdHRhY2htZW50LWFkZFwiOmNhc2VcImF0dGFjaG1lbnQtZWRpdFwiOmNhc2VcImF0dGFjaG1lbnQtcmVtb3ZlXCI6dGhpcy51cGRhdGVJbnB1dEVsZW1lbnQoKX1yZXR1cm4gdGhpcy5lZGl0b3JFbGVtZW50Lm5vdGlmeSh0LGUpfSx1LnByb3RvdHlwZS5yZW1vdmVBdHRhY2htZW50PWZ1bmN0aW9uKHQpe3JldHVybiB0aGlzLmVkaXRvci5yZWNvcmRVbmRvRW50cnkoXCJEZWxldGUgQXR0YWNobWVudFwiKSx0aGlzLmNvbXBvc2l0aW9uLnJlbW92ZUF0dGFjaG1lbnQodCksdGhpcy5yZW5kZXIoKX0sdS5wcm90b3R5cGUucmVjb3JkRm9ybWF0dGluZ1VuZG9FbnRyeT1mdW5jdGlvbihlKXt2YXIgbixvO3JldHVybiBuPXQoZSksbz10aGlzLnNlbGVjdGlvbk1hbmFnZXIuZ2V0TG9jYXRpb25SYW5nZSgpLG58fCFpKG8pP3RoaXMuZWRpdG9yLnJlY29yZFVuZG9FbnRyeShcIkZvcm1hdHRpbmdcIix7Y29udGV4dDp0aGlzLmdldFVuZG9Db250ZXh0KCksY29uc29saWRhdGFibGU6ITB9KTp2b2lkIDB9LHUucHJvdG90eXBlLnJlY29yZFR5cGluZ1VuZG9FbnRyeT1mdW5jdGlvbigpe3JldHVybiB0aGlzLmVkaXRvci5yZWNvcmRVbmRvRW50cnkoXCJUeXBpbmdcIix7Y29udGV4dDp0aGlzLmdldFVuZG9Db250ZXh0KHRoaXMuY3VycmVudEF0dHJpYnV0ZXMpLGNvbnNvbGlkYXRhYmxlOiEwfSl9LHUucHJvdG90eXBlLmdldFVuZG9Db250ZXh0PWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIHQ9MTw9YXJndW1lbnRzLmxlbmd0aD9hLmNhbGwoYXJndW1lbnRzLDApOltdLFt0aGlzLmdldExvY2F0aW9uQ29udGV4dCgpLHRoaXMuZ2V0VGltZUNvbnRleHQoKV0uY29uY2F0KGEuY2FsbCh0KSl9LHUucHJvdG90eXBlLmdldExvY2F0aW9uQ29udGV4dD1mdW5jdGlvbigpe3ZhciB0O3JldHVybiB0PXRoaXMuc2VsZWN0aW9uTWFuYWdlci5nZXRMb2NhdGlvblJhbmdlKCksaSh0KT90WzBdLmluZGV4OnR9LHUucHJvdG90eXBlLmdldFRpbWVDb250ZXh0PWZ1bmN0aW9uKCl7cmV0dXJuIGUuY29uZmlnLnVuZG9JbnRlcnZhbD4wP01hdGguZmxvb3IoKG5ldyBEYXRlKS5nZXRUaW1lKCkvZS5jb25maWcudW5kb0ludGVydmFsKTowfSx1LnByb3RvdHlwZS5pc0ZvY3VzZWQ9ZnVuY3Rpb24oKXt2YXIgdDtyZXR1cm4gdGhpcy5lZGl0b3JFbGVtZW50PT09KG51bGwhPSh0PXRoaXMuZWRpdG9yRWxlbWVudC5vd25lckRvY3VtZW50KT90LmFjdGl2ZUVsZW1lbnQ6dm9pZCAwKX0sdS5wcm90b3R5cGUuaXNGb2N1c2VkSW52aXNpYmx5PWZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuaXNGb2N1c2VkKCkmJiF0aGlzLmdldExvY2F0aW9uUmFuZ2UoKX0sdX0oZS5Db250cm9sbGVyKX0uY2FsbCh0aGlzKSxmdW5jdGlvbigpe3ZhciB0LG4saSxvLHIscyxhLHU9W10uaW5kZXhPZnx8ZnVuY3Rpb24odCl7Zm9yKHZhciBlPTAsbj10aGlzLmxlbmd0aDtuPmU7ZSsrKWlmKGUgaW4gdGhpcyYmdGhpc1tlXT09PXQpcmV0dXJuIGU7cmV0dXJuLTF9O249ZS5icm93c2VyLHM9ZS5tYWtlRWxlbWVudCxhPWUudHJpZ2dlckV2ZW50LG89ZS5oYW5kbGVFdmVudCxyPWUuaGFuZGxlRXZlbnRPbmNlLGk9ZS5maW5kQ2xvc2VzdEVsZW1lbnRGcm9tTm9kZSx0PWUuQXR0YWNobWVudFZpZXcuYXR0YWNobWVudFNlbGVjdG9yLGUucmVnaXN0ZXJFbGVtZW50KFwidHJpeC1lZGl0b3JcIixmdW5jdGlvbigpe3ZhciBjLGwsaCxwLGQsZixnLG0sdjtyZXR1cm4gZz0wLGw9ZnVuY3Rpb24odCl7cmV0dXJuIWRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCI6Zm9jdXNcIikmJnQuaGFzQXR0cmlidXRlKFwiYXV0b2ZvY3VzXCIpJiZkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiW2F1dG9mb2N1c11cIik9PT10P3QuZm9jdXMoKTp2b2lkIDB9LG09ZnVuY3Rpb24odCl7cmV0dXJuIHQuaGFzQXR0cmlidXRlKFwiY29udGVudGVkaXRhYmxlXCIpP3ZvaWQgMDoodC5zZXRBdHRyaWJ1dGUoXCJjb250ZW50ZWRpdGFibGVcIixcIlwiKSxyKFwiZm9jdXNcIix7b25FbGVtZW50OnQsd2l0aENhbGxiYWNrOmZ1bmN0aW9uKCl7cmV0dXJuIGgodCl9fSkpfSxoPWZ1bmN0aW9uKHQpe3JldHVybiBkKHQpLHYodCl9LGQ9ZnVuY3Rpb24odCl7cmV0dXJuKFwiZnVuY3Rpb25cIj09dHlwZW9mIGRvY3VtZW50LnF1ZXJ5Q29tbWFuZFN1cHBvcnRlZD9kb2N1bWVudC5xdWVyeUNvbW1hbmRTdXBwb3J0ZWQoXCJlbmFibGVPYmplY3RSZXNpemluZ1wiKTp2b2lkIDApPyhkb2N1bWVudC5leGVjQ29tbWFuZChcImVuYWJsZU9iamVjdFJlc2l6aW5nXCIsITEsITEpLG8oXCJtc2NvbnRyb2xzZWxlY3RcIix7b25FbGVtZW50OnQscHJldmVudERlZmF1bHQ6ITB9KSk6dm9pZCAwfSx2PWZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuKFwiZnVuY3Rpb25cIj09dHlwZW9mIGRvY3VtZW50LnF1ZXJ5Q29tbWFuZFN1cHBvcnRlZD9kb2N1bWVudC5xdWVyeUNvbW1hbmRTdXBwb3J0ZWQoXCJEZWZhdWx0UGFyYWdyYXBoU2VwYXJhdG9yXCIpOnZvaWQgMCkmJih0PWUuY29uZmlnLmJsb2NrQXR0cmlidXRlc1tcImRlZmF1bHRcIl0udGFnTmFtZSxcImRpdlwiPT09dHx8XCJwXCI9PT10KT9kb2N1bWVudC5leGVjQ29tbWFuZChcIkRlZmF1bHRQYXJhZ3JhcGhTZXBhcmF0b3JcIiwhMSx0KTp2b2lkIDB9LGM9ZnVuY3Rpb24odCl7cmV0dXJuIHQuaGFzQXR0cmlidXRlKFwicm9sZVwiKT92b2lkIDA6dC5zZXRBdHRyaWJ1dGUoXCJyb2xlXCIsXCJ0ZXh0Ym94XCIpfSxmPWZ1bmN0aW9uKHQpe3ZhciBlO2lmKCF0Lmhhc0F0dHJpYnV0ZShcImFyaWEtbGFiZWxcIikmJiF0Lmhhc0F0dHJpYnV0ZShcImFyaWEtbGFiZWxsZWRieVwiKSlyZXR1cm4oZT1mdW5jdGlvbigpe3ZhciBlLG4saTtyZXR1cm4gaT1mdW5jdGlvbigpe3ZhciBuLGksbyxyO2ZvcihvPXQubGFiZWxzLHI9W10sbj0wLGk9by5sZW5ndGg7aT5uO24rKyllPW9bbl0sZS5jb250YWlucyh0KXx8ci5wdXNoKGUudGV4dENvbnRlbnQpO3JldHVybiByfSgpLChuPWkuam9pbihcIiBcIikpP3Quc2V0QXR0cmlidXRlKFwiYXJpYS1sYWJlbFwiLG4pOnQucmVtb3ZlQXR0cmlidXRlKFwiYXJpYS1sYWJlbFwiKX0pKCksbyhcImZvY3VzXCIse29uRWxlbWVudDp0LHdpdGhDYWxsYmFjazplfSl9LHA9ZnVuY3Rpb24oKXtyZXR1cm4gbi5mb3JjZXNPYmplY3RSZXNpemluZz97ZGlzcGxheTpcImlubGluZVwiLHdpZHRoOlwiYXV0b1wifTp7ZGlzcGxheTpcImlubGluZS1ibG9ja1wiLHdpZHRoOlwiMXB4XCJ9fSgpLHtkZWZhdWx0Q1NTOlwiJXQge1xcbiAgZGlzcGxheTogYmxvY2s7XFxufVxcblxcbiV0OmVtcHR5Om5vdCg6Zm9jdXMpOjpiZWZvcmUge1xcbiAgY29udGVudDogYXR0cihwbGFjZWhvbGRlcik7XFxuICBjb2xvcjogZ3JheXRleHQ7XFxuICBjdXJzb3I6IHRleHQ7XFxuICBwb2ludGVyLWV2ZW50czogbm9uZTtcXG59XFxuXFxuJXQgYVtjb250ZW50ZWRpdGFibGU9ZmFsc2VdIHtcXG4gIGN1cnNvcjogdGV4dDtcXG59XFxuXFxuJXQgaW1nIHtcXG4gIG1heC13aWR0aDogMTAwJTtcXG4gIGhlaWdodDogYXV0bztcXG59XFxuXFxuJXQgXCIrdCtcIiBmaWdjYXB0aW9uIHRleHRhcmVhIHtcXG4gIHJlc2l6ZTogbm9uZTtcXG59XFxuXFxuJXQgXCIrdCtcIiBmaWdjYXB0aW9uIHRleHRhcmVhLnRyaXgtYXV0b3Jlc2l6ZS1jbG9uZSB7XFxuICBwb3NpdGlvbjogYWJzb2x1dGU7XFxuICBsZWZ0OiAtOTk5OXB4O1xcbiAgbWF4LWhlaWdodDogMHB4O1xcbn1cXG5cXG4ldCBcIit0K1wiIGZpZ2NhcHRpb25bZGF0YS10cml4LXBsYWNlaG9sZGVyXTplbXB0eTo6YmVmb3JlIHtcXG4gIGNvbnRlbnQ6IGF0dHIoZGF0YS10cml4LXBsYWNlaG9sZGVyKTtcXG4gIGNvbG9yOiBncmF5dGV4dDtcXG59XFxuXFxuJXQgW2RhdGEtdHJpeC1jdXJzb3ItdGFyZ2V0XSB7XFxuICBkaXNwbGF5OiBcIitwLmRpc3BsYXkrXCIgIWltcG9ydGFudDtcXG4gIHdpZHRoOiBcIitwLndpZHRoK1wiICFpbXBvcnRhbnQ7XFxuICBwYWRkaW5nOiAwICFpbXBvcnRhbnQ7XFxuICBtYXJnaW46IDAgIWltcG9ydGFudDtcXG4gIGJvcmRlcjogbm9uZSAhaW1wb3J0YW50O1xcbn1cXG5cXG4ldCBbZGF0YS10cml4LWN1cnNvci10YXJnZXQ9bGVmdF0ge1xcbiAgdmVydGljYWwtYWxpZ246IHRvcCAhaW1wb3J0YW50O1xcbiAgbWFyZ2luLWxlZnQ6IC0xcHggIWltcG9ydGFudDtcXG59XFxuXFxuJXQgW2RhdGEtdHJpeC1jdXJzb3ItdGFyZ2V0PXJpZ2h0XSB7XFxuICB2ZXJ0aWNhbC1hbGlnbjogYm90dG9tICFpbXBvcnRhbnQ7XFxuICBtYXJnaW4tcmlnaHQ6IC0xcHggIWltcG9ydGFudDtcXG59XCIsdHJpeElkOntnZXQ6ZnVuY3Rpb24oKXtyZXR1cm4gdGhpcy5oYXNBdHRyaWJ1dGUoXCJ0cml4LWlkXCIpP3RoaXMuZ2V0QXR0cmlidXRlKFwidHJpeC1pZFwiKToodGhpcy5zZXRBdHRyaWJ1dGUoXCJ0cml4LWlkXCIsKytnKSx0aGlzLnRyaXhJZCl9fSxsYWJlbHM6e2dldDpmdW5jdGlvbigpe3ZhciB0LGUsbjtyZXR1cm4gZT1bXSx0aGlzLmlkJiZ0aGlzLm93bmVyRG9jdW1lbnQmJmUucHVzaC5hcHBseShlLHRoaXMub3duZXJEb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKFwibGFiZWxbZm9yPSdcIit0aGlzLmlkK1wiJ11cIikpLCh0PWkodGhpcyx7bWF0Y2hpbmdTZWxlY3RvcjpcImxhYmVsXCJ9KSkmJigobj10LmNvbnRyb2wpPT09dGhpc3x8bnVsbD09PW4pJiZlLnB1c2godCksZX19LHRvb2xiYXJFbGVtZW50OntnZXQ6ZnVuY3Rpb24oKXt2YXIgdCxlLG47cmV0dXJuIHRoaXMuaGFzQXR0cmlidXRlKFwidG9vbGJhclwiKT9udWxsIT0oZT10aGlzLm93bmVyRG9jdW1lbnQpP2UuZ2V0RWxlbWVudEJ5SWQodGhpcy5nZXRBdHRyaWJ1dGUoXCJ0b29sYmFyXCIpKTp2b2lkIDA6dGhpcy5wYXJlbnROb2RlPyhuPVwidHJpeC10b29sYmFyLVwiK3RoaXMudHJpeElkLHRoaXMuc2V0QXR0cmlidXRlKFwidG9vbGJhclwiLG4pLHQ9cyhcInRyaXgtdG9vbGJhclwiLHtpZDpufSksdGhpcy5wYXJlbnROb2RlLmluc2VydEJlZm9yZSh0LHRoaXMpLHQpOnZvaWQgMH19LGlucHV0RWxlbWVudDp7Z2V0OmZ1bmN0aW9uKCl7dmFyIHQsZSxuO3JldHVybiB0aGlzLmhhc0F0dHJpYnV0ZShcImlucHV0XCIpP251bGwhPShuPXRoaXMub3duZXJEb2N1bWVudCk/bi5nZXRFbGVtZW50QnlJZCh0aGlzLmdldEF0dHJpYnV0ZShcImlucHV0XCIpKTp2b2lkIDA6dGhpcy5wYXJlbnROb2RlPyhlPVwidHJpeC1pbnB1dC1cIit0aGlzLnRyaXhJZCx0aGlzLnNldEF0dHJpYnV0ZShcImlucHV0XCIsZSksdD1zKFwiaW5wdXRcIix7dHlwZTpcImhpZGRlblwiLGlkOmV9KSx0aGlzLnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKHQsdGhpcy5uZXh0RWxlbWVudFNpYmxpbmcpLHQpOnZvaWQgMH19LGVkaXRvcjp7Z2V0OmZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMuZWRpdG9yQ29udHJvbGxlcik/dC5lZGl0b3I6dm9pZCAwfX0sbmFtZTp7Z2V0OmZ1bmN0aW9uKCl7dmFyIHQ7cmV0dXJuIG51bGwhPSh0PXRoaXMuaW5wdXRFbGVtZW50KT90Lm5hbWU6dm9pZCAwfX0sdmFsdWU6e2dldDpmdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLmlucHV0RWxlbWVudCk/dC52YWx1ZTp2b2lkIDB9LHNldDpmdW5jdGlvbih0KXt2YXIgZTtyZXR1cm4gdGhpcy5kZWZhdWx0VmFsdWU9dCxudWxsIT0oZT10aGlzLmVkaXRvcik/ZS5sb2FkSFRNTCh0aGlzLmRlZmF1bHRWYWx1ZSk6dm9pZCAwfX0sbm90aWZ5OmZ1bmN0aW9uKHQsZSl7cmV0dXJuIHRoaXMuZWRpdG9yQ29udHJvbGxlcj9hKFwidHJpeC1cIit0LHtvbkVsZW1lbnQ6dGhpcyxhdHRyaWJ1dGVzOmV9KTp2b2lkIDB9LHNldElucHV0RWxlbWVudFZhbHVlOmZ1bmN0aW9uKHQpe3ZhciBlO3JldHVybiBudWxsIT0oZT10aGlzLmlucHV0RWxlbWVudCk/ZS52YWx1ZT10OnZvaWQgMH0saW5pdGlhbGl6ZTpmdW5jdGlvbigpe3JldHVybiB0aGlzLmhhc0F0dHJpYnV0ZShcImRhdGEtdHJpeC1pbnRlcm5hbFwiKT92b2lkIDA6KG0odGhpcyksYyh0aGlzKSxmKHRoaXMpKX0sY29ubmVjdDpmdW5jdGlvbigpe3JldHVybiB0aGlzLmhhc0F0dHJpYnV0ZShcImRhdGEtdHJpeC1pbnRlcm5hbFwiKT92b2lkIDA6KHRoaXMuZWRpdG9yQ29udHJvbGxlcnx8KGEoXCJ0cml4LWJlZm9yZS1pbml0aWFsaXplXCIse29uRWxlbWVudDp0aGlzfSksdGhpcy5lZGl0b3JDb250cm9sbGVyPW5ldyBlLkVkaXRvckNvbnRyb2xsZXIoe2VkaXRvckVsZW1lbnQ6dGhpcyxodG1sOnRoaXMuZGVmYXVsdFZhbHVlPXRoaXMudmFsdWV9KSxyZXF1ZXN0QW5pbWF0aW9uRnJhbWUoZnVuY3Rpb24odCl7cmV0dXJuIGZ1bmN0aW9uKCl7cmV0dXJuIGEoXCJ0cml4LWluaXRpYWxpemVcIix7b25FbGVtZW50OnR9KX19KHRoaXMpKSksdGhpcy5lZGl0b3JDb250cm9sbGVyLnJlZ2lzdGVyU2VsZWN0aW9uTWFuYWdlcigpLHRoaXMucmVnaXN0ZXJSZXNldExpc3RlbmVyKCksdGhpcy5yZWdpc3RlckNsaWNrTGlzdGVuZXIoKSxsKHRoaXMpKX0sZGlzY29ubmVjdDpmdW5jdGlvbigpe3ZhciB0O3JldHVybiBudWxsIT0odD10aGlzLmVkaXRvckNvbnRyb2xsZXIpJiZ0LnVucmVnaXN0ZXJTZWxlY3Rpb25NYW5hZ2VyKCksdGhpcy51bnJlZ2lzdGVyUmVzZXRMaXN0ZW5lcigpLHRoaXMudW5yZWdpc3RlckNsaWNrTGlzdGVuZXIoKX0scmVnaXN0ZXJSZXNldExpc3RlbmVyOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMucmVzZXRMaXN0ZW5lcj10aGlzLnJlc2V0QnViYmxlZC5iaW5kKHRoaXMpLHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwicmVzZXRcIix0aGlzLnJlc2V0TGlzdGVuZXIsITEpfSx1bnJlZ2lzdGVyUmVzZXRMaXN0ZW5lcjpmdW5jdGlvbigpe3JldHVybiB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcihcInJlc2V0XCIsdGhpcy5yZXNldExpc3RlbmVyLCExKX0scmVnaXN0ZXJDbGlja0xpc3RlbmVyOmZ1bmN0aW9uKCl7cmV0dXJuIHRoaXMuY2xpY2tMaXN0ZW5lcj10aGlzLmNsaWNrQnViYmxlZC5iaW5kKHRoaXMpLHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwiY2xpY2tcIix0aGlzLmNsaWNrTGlzdGVuZXIsITEpfSx1bnJlZ2lzdGVyQ2xpY2tMaXN0ZW5lcjpmdW5jdGlvbigpe3JldHVybiB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsdGhpcy5jbGlja0xpc3RlbmVyLCExKX0scmVzZXRCdWJibGVkOmZ1bmN0aW9uKHQpe3ZhciBlO2lmKCF0LmRlZmF1bHRQcmV2ZW50ZWQmJnQudGFyZ2V0PT09KG51bGwhPShlPXRoaXMuaW5wdXRFbGVtZW50KT9lLmZvcm06dm9pZCAwKSlyZXR1cm4gdGhpcy5yZXNldCgpfSxjbGlja0J1YmJsZWQ6ZnVuY3Rpb24odCl7dmFyIGU7aWYoISh0LmRlZmF1bHRQcmV2ZW50ZWR8fHRoaXMuY29udGFpbnModC50YXJnZXQpfHwhKGU9aSh0LnRhcmdldCx7bWF0Y2hpbmdTZWxlY3RvcjpcImxhYmVsXCJ9KSl8fHUuY2FsbCh0aGlzLmxhYmVscyxlKTwwKSlyZXR1cm4gdGhpcy5mb2N1cygpfSxyZXNldDpmdW5jdGlvbigpe3JldHVybiB0aGlzLnZhbHVlPXRoaXMuZGVmYXVsdFZhbHVlfX19KCkpfS5jYWxsKHRoaXMpLGZ1bmN0aW9uKCl7fS5jYWxsKHRoaXMpfSkuY2FsbCh0aGlzKSxcIm9iamVjdFwiPT10eXBlb2YgbW9kdWxlJiZtb2R1bGUuZXhwb3J0cz9tb2R1bGUuZXhwb3J0cz1lOlwiZnVuY3Rpb25cIj09dHlwZW9mIGRlZmluZSYmZGVmaW5lLmFtZCYmZGVmaW5lKGUpfS5jYWxsKHRoaXMpOyIsICJpbXBvcnQgVHJpeCBmcm9tICd0cml4L2Rpc3QvdHJpeCdcblxuVHJpeC5jb25maWcuYmxvY2tBdHRyaWJ1dGVzLmRlZmF1bHQudGFnTmFtZSA9ICdwJ1xuXG5Ucml4LmNvbmZpZy5ibG9ja0F0dHJpYnV0ZXMuZGVmYXVsdC5icmVha09uUmV0dXJuID0gdHJ1ZVxuXG5Ucml4LmNvbmZpZy5ibG9ja0F0dHJpYnV0ZXMuaGVhZGluZyA9IHtcbiAgICB0YWdOYW1lOiAnaDInLFxuICAgIHRlcm1pbmFsOiB0cnVlLFxuICAgIGJyZWFrT25SZXR1cm46IHRydWUsXG4gICAgZ3JvdXA6IGZhbHNlLFxufVxuXG5Ucml4LmNvbmZpZy5ibG9ja0F0dHJpYnV0ZXMuc3ViSGVhZGluZyA9IHtcbiAgICB0YWdOYW1lOiAnaDMnLFxuICAgIHRlcm1pbmFsOiB0cnVlLFxuICAgIGJyZWFrT25SZXR1cm46IHRydWUsXG4gICAgZ3JvdXA6IGZhbHNlLFxufVxuXG5Ucml4LmNvbmZpZy50ZXh0QXR0cmlidXRlcy51bmRlcmxpbmUgPSB7XG4gICAgc3R5bGU6IHsgdGV4dERlY29yYXRpb246ICd1bmRlcmxpbmUnIH0sXG4gICAgaW5oZXJpdGFibGU6IHRydWUsXG4gICAgcGFyc2VyOiAoZWxlbWVudCkgPT4ge1xuICAgICAgICBjb25zdCBzdHlsZSA9IHdpbmRvdy5nZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpXG5cbiAgICAgICAgcmV0dXJuIHN0eWxlLnRleHREZWNvcmF0aW9uLmluY2x1ZGVzKCd1bmRlcmxpbmUnKVxuICAgIH0sXG59XG5cblRyaXguQmxvY2sucHJvdG90eXBlLmJyZWFrc09uUmV0dXJuID0gZnVuY3Rpb24gKCkge1xuICAgIGNvbnN0IGxhc3RBdHRyaWJ1dGUgPSB0aGlzLmdldExhc3RBdHRyaWJ1dGUoKVxuICAgIGNvbnN0IGJsb2NrQ29uZmlnID0gVHJpeC5nZXRCbG9ja0NvbmZpZyhcbiAgICAgICAgbGFzdEF0dHJpYnV0ZSA/IGxhc3RBdHRyaWJ1dGUgOiAnZGVmYXVsdCcsXG4gICAgKVxuXG4gICAgcmV0dXJuIGJsb2NrQ29uZmlnPy5icmVha09uUmV0dXJuID8/IGZhbHNlXG59XG5cblRyaXguTGluZUJyZWFrSW5zZXJ0aW9uLnByb3RvdHlwZS5zaG91bGRJbnNlcnRCbG9ja0JyZWFrID0gZnVuY3Rpb24gKCkge1xuICAgIGlmIChcbiAgICAgICAgdGhpcy5ibG9jay5oYXNBdHRyaWJ1dGVzKCkgJiZcbiAgICAgICAgdGhpcy5ibG9jay5pc0xpc3RJdGVtKCkgJiZcbiAgICAgICAgIXRoaXMuYmxvY2suaXNFbXB0eSgpXG4gICAgKSB7XG4gICAgICAgIHJldHVybiB0aGlzLnN0YXJ0TG9jYXRpb24ub2Zmc2V0ID4gMFxuICAgIH0gZWxzZSB7XG4gICAgICAgIHJldHVybiAhdGhpcy5zaG91bGRCcmVha0Zvcm1hdHRlZEJsb2NrKCkgPyB0aGlzLmJyZWFrc09uUmV0dXJuIDogZmFsc2VcbiAgICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHJpY2hFZGl0b3JGb3JtQ29tcG9uZW50KHsgc3RhdGUgfSkge1xuICAgIHJldHVybiB7XG4gICAgICAgIHN0YXRlLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRoaXMuJHJlZnMudHJpeD8uZWRpdG9yPy5sb2FkSFRNTCh0aGlzLnN0YXRlKVxuXG4gICAgICAgICAgICB0aGlzLiR3YXRjaCgnc3RhdGUnLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKGRvY3VtZW50LmFjdGl2ZUVsZW1lbnQgPT09IHRoaXMuJHJlZnMudHJpeCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLiRyZWZzLnRyaXg/LmVkaXRvcj8ubG9hZEhUTUwodGhpcy5zdGF0ZSlcbiAgICAgICAgICAgIH0pXG4gICAgICAgIH0sXG4gICAgfVxufVxuIl0sCiAgIm1hcHBpbmdzIjogIjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFBQTtBQUtBLEtBQUMsV0FBVTtBQUFBLElBQUMsR0FBRyxLQUFLLE9BQUksR0FBRSxXQUFVO0FBQUMsVUFBSTtBQUFFLGNBQU0sT0FBTyxRQUFNLE9BQU8sTUFBSSxJQUFFLFdBQVU7QUFBQyxpQkFBU0EsS0FBRztBQUFDLGVBQUssTUFBTTtBQUFBLFFBQUM7QUFBQyxlQUFPQSxHQUFFLFVBQVUsUUFBTSxXQUFVO0FBQUMsaUJBQU8sS0FBSyxTQUFPLENBQUM7QUFBQSxRQUFDLEdBQUVBLEdBQUUsVUFBVSxNQUFJLFNBQVNBLElBQUU7QUFBQyxpQkFBTSxPQUFLLEtBQUssT0FBTyxRQUFRQSxFQUFDO0FBQUEsUUFBQyxHQUFFQSxHQUFFLFVBQVUsTUFBSSxTQUFTQSxJQUFFO0FBQUMsaUJBQU8sS0FBSyxJQUFJQSxFQUFDLEtBQUcsS0FBSyxPQUFPLEtBQUtBLEVBQUMsR0FBRTtBQUFBLFFBQUksR0FBRUEsR0FBRSxVQUFVLFFBQVEsSUFBRSxTQUFTQSxJQUFFO0FBQUMsY0FBSTtBQUFFLGlCQUFNLFFBQU0sSUFBRSxLQUFLLE9BQU8sUUFBUUEsRUFBQyxLQUFHLFNBQUksS0FBSyxPQUFPLE9BQU8sR0FBRSxDQUFDLEdBQUU7QUFBQSxRQUFHLEdBQUVBLEdBQUUsVUFBVSxVQUFRLFdBQVU7QUFBQyxjQUFJQTtBQUFFLGtCQUFPQSxLQUFFLEtBQUssUUFBUSxRQUFRLE1BQU1BLElBQUUsU0FBUztBQUFBLFFBQUMsR0FBRUE7QUFBQSxNQUFDLEVBQUU7QUFBQSxJQUFFLEVBQUUsS0FBSyxPQUFJLEdBQUUsU0FBUyxHQUFFO0FBQUMsZUFBUyxJQUFHO0FBQUEs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QUFFLEVBQUU7QUFBRSxrQkFBRyxJQUFFQSxHQUFFLENBQUMsR0FBRSxNQUFJQyxHQUFFLENBQUM7QUFBRSx1QkFBTTtBQUFHLG1CQUFNO0FBQUEsVUFBRSxHQUFFLGlCQUFnQixTQUFTRCxJQUFFLEdBQUU7QUFBQyxtQkFBTyxRQUFNQSxPQUFJQSxLQUFFLENBQUMsSUFBRyxRQUFNLE1BQUksSUFBRSxDQUFDLElBQUcsRUFBRSxlQUFlQSxHQUFFLE1BQU0sR0FBRSxFQUFFLE1BQU0sR0FBRSxDQUFDO0FBQUEsVUFBQyxHQUFFLGFBQVksV0FBVTtBQUFDLGdCQUFJQyxJQUFFLEdBQUU7QUFBRSxtQkFBTyxJQUFFLFVBQVUsQ0FBQyxHQUFFQSxLQUFFLEtBQUcsVUFBVSxTQUFPRCxHQUFFLEtBQUssV0FBVSxDQUFDLElBQUUsQ0FBQyxHQUFFLElBQUUsRUFBRSxNQUFNLENBQUMsR0FBRSxFQUFFLE9BQU8sTUFBTSxHQUFFQyxFQUFDLEdBQUU7QUFBQSxVQUFDLEdBQUUsc0JBQXFCLFNBQVNELElBQUVDLElBQUU7QUFBQyxnQkFBSSxHQUFFLEdBQUUsR0FBRSxHQUFFLEdBQUUsR0FBRSxHQUFFLEdBQUUsR0FBRSxHQUFFO0FBQUUsaUJBQUksUUFBTUQsT0FBSUEsS0FBRSxDQUFDLElBQUcsUUFBTUMsT0FBSUEsS0FBRSxDQUFDLElBQUcsSUFBRSxDQUFDLEdBQUUsSUFBRSxDQUFDLEdBQUUsSUFBRSxvQkFBSSxPQUFJLElBQUUsR0FBRSxJQUFFRCxHQUFFLFFBQU8sSUFBRSxHQUFFO0FBQUksa0JBQUVBLEdBQUUsQ0FBQyxHQUFFLEVBQUUsSUFBSSxDQUFDO0FBQUUsaUJBQUksSUFBRSxvQkFBSSxPQUFJLElBQUUsR0FBRSxJQUFFQyxHQUFFLFFBQU8sSUFBRSxHQUFFO0FBQUksa0JBQUVBLEdBQUUsQ0FBQyxHQUFFLEVBQUUsSUFBSSxDQUFDLEdBQUUsRUFBRSxJQUFJLENBQUMsS0FBRyxFQUFFLEtBQUssQ0FBQztBQUFFLGlCQUFJLElBQUUsR0FBRSxJQUFFRCxHQUFFLFFBQU8sSUFBRSxHQUFFO0FB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QUFFLFlBQVksb0JBQW9CLEdBQUVBO0FBQUEsVUFBQyxFQUFFLEVBQUUsV0FBVztBQUFBLFFBQUMsRUFBRSxLQUFLLElBQUksR0FBRSxXQUFVO0FBQUMsY0FBSUYsSUFBRSxHQUFFLEdBQUUsR0FBRSxHQUFFLElBQUUsU0FBU0EsSUFBRUMsSUFBRTtBQUFDLHFCQUFTQyxLQUFHO0FBQUMsbUJBQUssY0FBWUY7QUFBQSxZQUFDO0FBQUMscUJBQVFHLE1BQUtGO0FBQUUsZ0JBQUUsS0FBS0EsSUFBRUUsRUFBQyxNQUFJSCxHQUFFRyxFQUFDLElBQUVGLEdBQUVFLEVBQUM7QUFBRyxtQkFBT0QsR0FBRSxZQUFVRCxHQUFFLFdBQVVELEdBQUUsWUFBVSxJQUFJRSxNQUFFRixHQUFFLFlBQVVDLEdBQUUsV0FBVUQ7QUFBQSxVQUFDLEdBQUUsSUFBRSxDQUFDLEVBQUU7QUFBZSxZQUFFLGNBQVksU0FBU0EsSUFBRTtBQUFDLHFCQUFTQyxHQUFFRCxJQUFFQyxJQUFFO0FBQUMsbUJBQUssYUFBV0QsSUFBRSxLQUFLLGFBQVdDLElBQUUsS0FBSyxTQUFPLEtBQUssV0FBVyxRQUFPLEtBQUssYUFBVyxLQUFLLFdBQVc7QUFBQSxZQUFNO0FBQUMsbUJBQU8sRUFBRUEsSUFBRUQsRUFBQyxHQUFFQyxHQUFFLE1BQUksU0FBU0QsSUFBRTtBQUFDLHFCQUFPLFFBQU1BLE9BQUlBLEtBQUUsS0FBSUEsY0FBYSxPQUFLQSxLQUFFLEtBQUssZUFBZSxRQUFNQSxLQUFFQSxHQUFFLFNBQVMsSUFBRSxNQUFNO0FBQUEsWUFBQyxHQUFFQyxHQUFFLGlCQUFlLFNBQVNELElBQUU7QUFBQyxxQkFBTyxJQUFJLEtBQUtBLElBQUUsRUFBRUEsRUFBQyxDQUFDO0FBQUEsWUFBQyxHQUFFQyxHQUFFLGlCQUFlLFNBQVNELElBQUU7QUFBQyxxQkFBTyxJQUFJLEtBQUssRUFBRUEsRUFBQyxHQUFFQSxFQUFDO0FBQUEsWUFBQyxHQUFFQyxHQUFFLFVBQVUscUJBQW1CLFNBQVNELElBQUU7QUFBQyxxQkFBTyxFQUFFLEtBQUssV0FBVyxN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LFlBQUFBLFFBQUssT0FBTyxnQkFBZ0IsUUFBUSxnQkFBZ0I7QUFFcEQsWUFBQUEsUUFBSyxPQUFPLGdCQUFnQixVQUFVO0FBQUEsRUFDbEMsU0FBUztBQUFBLEVBQ1QsVUFBVTtBQUFBLEVBQ1YsZUFBZTtBQUFBLEVBQ2YsT0FBTztBQUNYO0FBRUEsWUFBQUEsUUFBSyxPQUFPLGdCQUFnQixhQUFhO0FBQUEsRUFDckMsU0FBUztBQUFBLEVBQ1QsVUFBVTtBQUFBLEVBQ1YsZUFBZTtBQUFBLEVBQ2YsT0FBTztBQUNYO0FBRUEsWUFBQUEsUUFBSyxPQUFPLGVBQWUsWUFBWTtBQUFBLEVBQ25DLE9BQU8sRUFBRSxnQkFBZ0IsWUFBWTtBQUFBLEVBQ3JDLGFBQWE7QUFBQSxFQUNiLFFBQVEsQ0FBQyxZQUFZO0FBQ2pCLFVBQU0sUUFBUSxPQUFPLGlCQUFpQixPQUFPO0FBRTdDLFdBQU8sTUFBTSxlQUFlLFNBQVMsV0FBVztBQUFBLEVBQ3BEO0FBQ0o7QUFFQSxZQUFBQSxRQUFLLE1BQU0sVUFBVSxpQkFBaUIsV0FBWTtBQUM5QyxRQUFNLGdCQUFnQixLQUFLLGlCQUFpQjtBQUM1QyxRQUFNLGNBQWMsWUFBQUEsUUFBSztBQUFBLElBQ3JCLGdCQUFnQixnQkFBZ0I7QUFBQSxFQUNwQztBQUVBLFNBQU8sYUFBYSxpQkFBaUI7QUFDekM7QUFFQSxZQUFBQSxRQUFLLG1CQUFtQixVQUFVLHlCQUF5QixXQUFZO0FBQ25FLE1BQ0ksS0FBSyxNQUFNLGNBQWMsS0FDekIsS0FBSyxNQUFNLFdBQVcsS0FDdEIsQ0FBQyxLQUFLLE1BQU0sUUFBUSxHQUN0QjtBQUNFLFdBQU8sS0FBSyxjQUFjLFNBQVM7QUFBQSxFQUN2QyxPQUFPO0FBQ0gsV0FBTyxDQUFDLEtBQUssMEJBQTBCLElBQUksS0FBSyxpQkFBaUI7QUFBQSxFQUNyRTtBQUNKO0FBRWUsU0FBUix3QkFBeUMsRUFBRSxNQUFNLEdBQUc7QUFDdkQsU0FBTztBQUFBLElBQ0g7QUFBQSxJQUVBLE1BQU0sV0FBWTtBQUNkLFdBQUssTUFBTSxNQUFNLFFBQVEsU0FBUyxLQUFLLEtBQUs7QUFFNUMsV0FBSyxPQUFPLFNBQVMsTUFBTTtBQUN2QixZQUFJLFNBQVMsa0JBQWtCLEtBQUssTUFBTSxNQUFNO0FBQzVDO0FBQUEsUUFDSjtBQUVBLGFBQUssTUFBTSxNQUFNLFFBQVEsU0FBUyxLQUFLLEtBQUs7QUFBQSxNQUNoRCxDQUFDO0FBQUEsSUFDTDtBQUFBLEVBQ0o7QUFDSjsiLAogICJuYW1lcyI6IFsidCIsICJlIiwgIm4iLCAiaSIsICJvIiwgInIiLCAicyIsICJhIiwgInUiLCAicCIsICJkIiwgImYiLCAiYyIsICJoIiwgImwiLCAiZyIsICJUcml4Il0KfQo=
