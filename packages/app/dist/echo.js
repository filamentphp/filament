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

  // node_modules/pusher-js/dist/web/pusher.js
  var require_pusher = __commonJS({
    "node_modules/pusher-js/dist/web/pusher.js"(exports, module) {
      (function webpackUniversalModuleDefinition(root, factory) {
        if (typeof exports === "object" && typeof module === "object")
          module.exports = factory();
        else if (typeof define === "function" && define.amd)
          define([], factory);
        else if (typeof exports === "object")
          exports["Pusher"] = factory();
        else
          root["Pusher"] = factory();
      })(window, function() {
        return (
          /******/
          function(modules) {
            var installedModules = {};
            function __webpack_require__(moduleId) {
              if (installedModules[moduleId]) {
                return installedModules[moduleId].exports;
              }
              var module2 = installedModules[moduleId] = {
                /******/
                i: moduleId,
                /******/
                l: false,
                /******/
                exports: {}
                /******/
              };
              modules[moduleId].call(module2.exports, module2, module2.exports, __webpack_require__);
              module2.l = true;
              return module2.exports;
            }
            __webpack_require__.m = modules;
            __webpack_require__.c = installedModules;
            __webpack_require__.d = function(exports2, name, getter) {
              if (!__webpack_require__.o(exports2, name)) {
                Object.defineProperty(exports2, name, { enumerable: true, get: getter });
              }
            };
            __webpack_require__.r = function(exports2) {
              if (typeof Symbol !== "undefined" && Symbol.toStringTag) {
                Object.defineProperty(exports2, Symbol.toStringTag, { value: "Module" });
              }
              Object.defineProperty(exports2, "__esModule", { value: true });
            };
            __webpack_require__.t = function(value, mode) {
              if (mode & 1)
                value = __webpack_require__(value);
              if (mode & 8)
                return value;
              if (mode & 4 && typeof value === "object" && value && value.__esModule)
                return value;
              var ns = /* @__PURE__ */ Object.create(null);
              __webpack_require__.r(ns);
              Object.defineProperty(ns, "default", { enumerable: true, value });
              if (mode & 2 && typeof value != "string")
                for (var key in value)
                  __webpack_require__.d(ns, key, function(key2) {
                    return value[key2];
                  }.bind(null, key));
              return ns;
            };
            __webpack_require__.n = function(module2) {
              var getter = module2 && module2.__esModule ? (
                /******/
                function getDefault() {
                  return module2["default"];
                }
              ) : (
                /******/
                function getModuleExports() {
                  return module2;
                }
              );
              __webpack_require__.d(getter, "a", getter);
              return getter;
            };
            __webpack_require__.o = function(object, property) {
              return Object.prototype.hasOwnProperty.call(object, property);
            };
            __webpack_require__.p = "";
            return __webpack_require__(__webpack_require__.s = 2);
          }([
            /* 0 */
            /***/
            function(module2, exports2, __webpack_require__) {
              "use strict";
              var __extends = this && this.__extends || function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              Object.defineProperty(exports2, "__esModule", { value: true });
              var INVALID_BYTE = 256;
              var Coder = (
                /** @class */
                function() {
                  function Coder2(_paddingCharacter) {
                    if (_paddingCharacter === void 0) {
                      _paddingCharacter = "=";
                    }
                    this._paddingCharacter = _paddingCharacter;
                  }
                  Coder2.prototype.encodedLength = function(length) {
                    if (!this._paddingCharacter) {
                      return (length * 8 + 5) / 6 | 0;
                    }
                    return (length + 2) / 3 * 4 | 0;
                  };
                  Coder2.prototype.encode = function(data) {
                    var out = "";
                    var i = 0;
                    for (; i < data.length - 2; i += 3) {
                      var c = data[i] << 16 | data[i + 1] << 8 | data[i + 2];
                      out += this._encodeByte(c >>> 3 * 6 & 63);
                      out += this._encodeByte(c >>> 2 * 6 & 63);
                      out += this._encodeByte(c >>> 1 * 6 & 63);
                      out += this._encodeByte(c >>> 0 * 6 & 63);
                    }
                    var left = data.length - i;
                    if (left > 0) {
                      var c = data[i] << 16 | (left === 2 ? data[i + 1] << 8 : 0);
                      out += this._encodeByte(c >>> 3 * 6 & 63);
                      out += this._encodeByte(c >>> 2 * 6 & 63);
                      if (left === 2) {
                        out += this._encodeByte(c >>> 1 * 6 & 63);
                      } else {
                        out += this._paddingCharacter || "";
                      }
                      out += this._paddingCharacter || "";
                    }
                    return out;
                  };
                  Coder2.prototype.maxDecodedLength = function(length) {
                    if (!this._paddingCharacter) {
                      return (length * 6 + 7) / 8 | 0;
                    }
                    return length / 4 * 3 | 0;
                  };
                  Coder2.prototype.decodedLength = function(s) {
                    return this.maxDecodedLength(s.length - this._getPaddingLength(s));
                  };
                  Coder2.prototype.decode = function(s) {
                    if (s.length === 0) {
                      return new Uint8Array(0);
                    }
                    var paddingLength = this._getPaddingLength(s);
                    var length = s.length - paddingLength;
                    var out = new Uint8Array(this.maxDecodedLength(length));
                    var op = 0;
                    var i = 0;
                    var haveBad = 0;
                    var v0 = 0, v1 = 0, v2 = 0, v3 = 0;
                    for (; i < length - 4; i += 4) {
                      v0 = this._decodeChar(s.charCodeAt(i + 0));
                      v1 = this._decodeChar(s.charCodeAt(i + 1));
                      v2 = this._decodeChar(s.charCodeAt(i + 2));
                      v3 = this._decodeChar(s.charCodeAt(i + 3));
                      out[op++] = v0 << 2 | v1 >>> 4;
                      out[op++] = v1 << 4 | v2 >>> 2;
                      out[op++] = v2 << 6 | v3;
                      haveBad |= v0 & INVALID_BYTE;
                      haveBad |= v1 & INVALID_BYTE;
                      haveBad |= v2 & INVALID_BYTE;
                      haveBad |= v3 & INVALID_BYTE;
                    }
                    if (i < length - 1) {
                      v0 = this._decodeChar(s.charCodeAt(i));
                      v1 = this._decodeChar(s.charCodeAt(i + 1));
                      out[op++] = v0 << 2 | v1 >>> 4;
                      haveBad |= v0 & INVALID_BYTE;
                      haveBad |= v1 & INVALID_BYTE;
                    }
                    if (i < length - 2) {
                      v2 = this._decodeChar(s.charCodeAt(i + 2));
                      out[op++] = v1 << 4 | v2 >>> 2;
                      haveBad |= v2 & INVALID_BYTE;
                    }
                    if (i < length - 3) {
                      v3 = this._decodeChar(s.charCodeAt(i + 3));
                      out[op++] = v2 << 6 | v3;
                      haveBad |= v3 & INVALID_BYTE;
                    }
                    if (haveBad !== 0) {
                      throw new Error("Base64Coder: incorrect characters for decoding");
                    }
                    return out;
                  };
                  Coder2.prototype._encodeByte = function(b) {
                    var result = b;
                    result += 65;
                    result += 25 - b >>> 8 & 0 - 65 - 26 + 97;
                    result += 51 - b >>> 8 & 26 - 97 - 52 + 48;
                    result += 61 - b >>> 8 & 52 - 48 - 62 + 43;
                    result += 62 - b >>> 8 & 62 - 43 - 63 + 47;
                    return String.fromCharCode(result);
                  };
                  Coder2.prototype._decodeChar = function(c) {
                    var result = INVALID_BYTE;
                    result += (42 - c & c - 44) >>> 8 & -INVALID_BYTE + c - 43 + 62;
                    result += (46 - c & c - 48) >>> 8 & -INVALID_BYTE + c - 47 + 63;
                    result += (47 - c & c - 58) >>> 8 & -INVALID_BYTE + c - 48 + 52;
                    result += (64 - c & c - 91) >>> 8 & -INVALID_BYTE + c - 65 + 0;
                    result += (96 - c & c - 123) >>> 8 & -INVALID_BYTE + c - 97 + 26;
                    return result;
                  };
                  Coder2.prototype._getPaddingLength = function(s) {
                    var paddingLength = 0;
                    if (this._paddingCharacter) {
                      for (var i = s.length - 1; i >= 0; i--) {
                        if (s[i] !== this._paddingCharacter) {
                          break;
                        }
                        paddingLength++;
                      }
                      if (s.length < 4 || paddingLength > 2) {
                        throw new Error("Base64Coder: incorrect padding");
                      }
                    }
                    return paddingLength;
                  };
                  return Coder2;
                }()
              );
              exports2.Coder = Coder;
              var stdCoder = new Coder();
              function encode(data) {
                return stdCoder.encode(data);
              }
              exports2.encode = encode;
              function decode(s) {
                return stdCoder.decode(s);
              }
              exports2.decode = decode;
              var URLSafeCoder = (
                /** @class */
                function(_super) {
                  __extends(URLSafeCoder2, _super);
                  function URLSafeCoder2() {
                    return _super !== null && _super.apply(this, arguments) || this;
                  }
                  URLSafeCoder2.prototype._encodeByte = function(b) {
                    var result = b;
                    result += 65;
                    result += 25 - b >>> 8 & 0 - 65 - 26 + 97;
                    result += 51 - b >>> 8 & 26 - 97 - 52 + 48;
                    result += 61 - b >>> 8 & 52 - 48 - 62 + 45;
                    result += 62 - b >>> 8 & 62 - 45 - 63 + 95;
                    return String.fromCharCode(result);
                  };
                  URLSafeCoder2.prototype._decodeChar = function(c) {
                    var result = INVALID_BYTE;
                    result += (44 - c & c - 46) >>> 8 & -INVALID_BYTE + c - 45 + 62;
                    result += (94 - c & c - 96) >>> 8 & -INVALID_BYTE + c - 95 + 63;
                    result += (47 - c & c - 58) >>> 8 & -INVALID_BYTE + c - 48 + 52;
                    result += (64 - c & c - 91) >>> 8 & -INVALID_BYTE + c - 65 + 0;
                    result += (96 - c & c - 123) >>> 8 & -INVALID_BYTE + c - 97 + 26;
                    return result;
                  };
                  return URLSafeCoder2;
                }(Coder)
              );
              exports2.URLSafeCoder = URLSafeCoder;
              var urlSafeCoder = new URLSafeCoder();
              function encodeURLSafe(data) {
                return urlSafeCoder.encode(data);
              }
              exports2.encodeURLSafe = encodeURLSafe;
              function decodeURLSafe(s) {
                return urlSafeCoder.decode(s);
              }
              exports2.decodeURLSafe = decodeURLSafe;
              exports2.encodedLength = function(length) {
                return stdCoder.encodedLength(length);
              };
              exports2.maxDecodedLength = function(length) {
                return stdCoder.maxDecodedLength(length);
              };
              exports2.decodedLength = function(s) {
                return stdCoder.decodedLength(s);
              };
            },
            /* 1 */
            /***/
            function(module2, exports2, __webpack_require__) {
              "use strict";
              Object.defineProperty(exports2, "__esModule", { value: true });
              var INVALID_UTF16 = "utf8: invalid string";
              var INVALID_UTF8 = "utf8: invalid source encoding";
              function encode(s) {
                var arr = new Uint8Array(encodedLength(s));
                var pos = 0;
                for (var i = 0; i < s.length; i++) {
                  var c = s.charCodeAt(i);
                  if (c < 128) {
                    arr[pos++] = c;
                  } else if (c < 2048) {
                    arr[pos++] = 192 | c >> 6;
                    arr[pos++] = 128 | c & 63;
                  } else if (c < 55296) {
                    arr[pos++] = 224 | c >> 12;
                    arr[pos++] = 128 | c >> 6 & 63;
                    arr[pos++] = 128 | c & 63;
                  } else {
                    i++;
                    c = (c & 1023) << 10;
                    c |= s.charCodeAt(i) & 1023;
                    c += 65536;
                    arr[pos++] = 240 | c >> 18;
                    arr[pos++] = 128 | c >> 12 & 63;
                    arr[pos++] = 128 | c >> 6 & 63;
                    arr[pos++] = 128 | c & 63;
                  }
                }
                return arr;
              }
              exports2.encode = encode;
              function encodedLength(s) {
                var result = 0;
                for (var i = 0; i < s.length; i++) {
                  var c = s.charCodeAt(i);
                  if (c < 128) {
                    result += 1;
                  } else if (c < 2048) {
                    result += 2;
                  } else if (c < 55296) {
                    result += 3;
                  } else if (c <= 57343) {
                    if (i >= s.length - 1) {
                      throw new Error(INVALID_UTF16);
                    }
                    i++;
                    result += 4;
                  } else {
                    throw new Error(INVALID_UTF16);
                  }
                }
                return result;
              }
              exports2.encodedLength = encodedLength;
              function decode(arr) {
                var chars = [];
                for (var i = 0; i < arr.length; i++) {
                  var b = arr[i];
                  if (b & 128) {
                    var min = void 0;
                    if (b < 224) {
                      if (i >= arr.length) {
                        throw new Error(INVALID_UTF8);
                      }
                      var n1 = arr[++i];
                      if ((n1 & 192) !== 128) {
                        throw new Error(INVALID_UTF8);
                      }
                      b = (b & 31) << 6 | n1 & 63;
                      min = 128;
                    } else if (b < 240) {
                      if (i >= arr.length - 1) {
                        throw new Error(INVALID_UTF8);
                      }
                      var n1 = arr[++i];
                      var n2 = arr[++i];
                      if ((n1 & 192) !== 128 || (n2 & 192) !== 128) {
                        throw new Error(INVALID_UTF8);
                      }
                      b = (b & 15) << 12 | (n1 & 63) << 6 | n2 & 63;
                      min = 2048;
                    } else if (b < 248) {
                      if (i >= arr.length - 2) {
                        throw new Error(INVALID_UTF8);
                      }
                      var n1 = arr[++i];
                      var n2 = arr[++i];
                      var n3 = arr[++i];
                      if ((n1 & 192) !== 128 || (n2 & 192) !== 128 || (n3 & 192) !== 128) {
                        throw new Error(INVALID_UTF8);
                      }
                      b = (b & 15) << 18 | (n1 & 63) << 12 | (n2 & 63) << 6 | n3 & 63;
                      min = 65536;
                    } else {
                      throw new Error(INVALID_UTF8);
                    }
                    if (b < min || b >= 55296 && b <= 57343) {
                      throw new Error(INVALID_UTF8);
                    }
                    if (b >= 65536) {
                      if (b > 1114111) {
                        throw new Error(INVALID_UTF8);
                      }
                      b -= 65536;
                      chars.push(String.fromCharCode(55296 | b >> 10));
                      b = 56320 | b & 1023;
                    }
                  }
                  chars.push(String.fromCharCode(b));
                }
                return chars.join("");
              }
              exports2.decode = decode;
            },
            /* 2 */
            /***/
            function(module2, exports2, __webpack_require__) {
              module2.exports = __webpack_require__(3).default;
            },
            /* 3 */
            /***/
            function(module2, __webpack_exports__, __webpack_require__) {
              "use strict";
              __webpack_require__.r(__webpack_exports__);
              var ScriptReceiverFactory = function() {
                function ScriptReceiverFactory2(prefix2, name) {
                  this.lastId = 0;
                  this.prefix = prefix2;
                  this.name = name;
                }
                ScriptReceiverFactory2.prototype.create = function(callback) {
                  this.lastId++;
                  var number = this.lastId;
                  var id = this.prefix + number;
                  var name = this.name + "[" + number + "]";
                  var called = false;
                  var callbackWrapper = function() {
                    if (!called) {
                      callback.apply(null, arguments);
                      called = true;
                    }
                  };
                  this[number] = callbackWrapper;
                  return { number, id, name, callback: callbackWrapper };
                };
                ScriptReceiverFactory2.prototype.remove = function(receiver) {
                  delete this[receiver.number];
                };
                return ScriptReceiverFactory2;
              }();
              var ScriptReceivers = new ScriptReceiverFactory("_pusher_script_", "Pusher.ScriptReceivers");
              var Defaults = {
                VERSION: "7.6.0",
                PROTOCOL: 7,
                wsPort: 80,
                wssPort: 443,
                wsPath: "",
                httpHost: "sockjs.pusher.com",
                httpPort: 80,
                httpsPort: 443,
                httpPath: "/pusher",
                stats_host: "stats.pusher.com",
                authEndpoint: "/pusher/auth",
                authTransport: "ajax",
                activityTimeout: 12e4,
                pongTimeout: 3e4,
                unavailableTimeout: 1e4,
                cluster: "mt1",
                userAuthentication: {
                  endpoint: "/pusher/user-auth",
                  transport: "ajax"
                },
                channelAuthorization: {
                  endpoint: "/pusher/auth",
                  transport: "ajax"
                },
                cdn_http: "http://js.pusher.com",
                cdn_https: "https://js.pusher.com",
                dependency_suffix: ""
              };
              var defaults = Defaults;
              var dependency_loader_DependencyLoader = function() {
                function DependencyLoader(options) {
                  this.options = options;
                  this.receivers = options.receivers || ScriptReceivers;
                  this.loading = {};
                }
                DependencyLoader.prototype.load = function(name, options, callback) {
                  var self = this;
                  if (self.loading[name] && self.loading[name].length > 0) {
                    self.loading[name].push(callback);
                  } else {
                    self.loading[name] = [callback];
                    var request = runtime.createScriptRequest(self.getPath(name, options));
                    var receiver = self.receivers.create(function(error) {
                      self.receivers.remove(receiver);
                      if (self.loading[name]) {
                        var callbacks = self.loading[name];
                        delete self.loading[name];
                        var successCallback = function(wasSuccessful) {
                          if (!wasSuccessful) {
                            request.cleanup();
                          }
                        };
                        for (var i = 0; i < callbacks.length; i++) {
                          callbacks[i](error, successCallback);
                        }
                      }
                    });
                    request.send(receiver);
                  }
                };
                DependencyLoader.prototype.getRoot = function(options) {
                  var cdn;
                  var protocol = runtime.getDocument().location.protocol;
                  if (options && options.useTLS || protocol === "https:") {
                    cdn = this.options.cdn_https;
                  } else {
                    cdn = this.options.cdn_http;
                  }
                  return cdn.replace(/\/*$/, "") + "/" + this.options.version;
                };
                DependencyLoader.prototype.getPath = function(name, options) {
                  return this.getRoot(options) + "/" + name + this.options.suffix + ".js";
                };
                return DependencyLoader;
              }();
              var dependency_loader = dependency_loader_DependencyLoader;
              var DependenciesReceivers = new ScriptReceiverFactory("_pusher_dependencies", "Pusher.DependenciesReceivers");
              var Dependencies = new dependency_loader({
                cdn_http: defaults.cdn_http,
                cdn_https: defaults.cdn_https,
                version: defaults.VERSION,
                suffix: defaults.dependency_suffix,
                receivers: DependenciesReceivers
              });
              var urlStore = {
                baseUrl: "https://pusher.com",
                urls: {
                  authenticationEndpoint: {
                    path: "/docs/channels/server_api/authenticating_users"
                  },
                  authorizationEndpoint: {
                    path: "/docs/channels/server_api/authorizing-users/"
                  },
                  javascriptQuickStart: {
                    path: "/docs/javascript_quick_start"
                  },
                  triggeringClientEvents: {
                    path: "/docs/client_api_guide/client_events#trigger-events"
                  },
                  encryptedChannelSupport: {
                    fullUrl: "https://github.com/pusher/pusher-js/tree/cc491015371a4bde5743d1c87a0fbac0feb53195#encrypted-channel-support"
                  }
                }
              };
              var buildLogSuffix = function(key) {
                var urlPrefix = "See:";
                var urlObj = urlStore.urls[key];
                if (!urlObj)
                  return "";
                var url;
                if (urlObj.fullUrl) {
                  url = urlObj.fullUrl;
                } else if (urlObj.path) {
                  url = urlStore.baseUrl + urlObj.path;
                }
                if (!url)
                  return "";
                return urlPrefix + " " + url;
              };
              var url_store = { buildLogSuffix };
              var AuthRequestType;
              (function(AuthRequestType2) {
                AuthRequestType2["UserAuthentication"] = "user-authentication";
                AuthRequestType2["ChannelAuthorization"] = "channel-authorization";
              })(AuthRequestType || (AuthRequestType = {}));
              var __extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var BadEventName = function(_super) {
                __extends(BadEventName2, _super);
                function BadEventName2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return BadEventName2;
              }(Error);
              var BadChannelName = function(_super) {
                __extends(BadChannelName2, _super);
                function BadChannelName2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return BadChannelName2;
              }(Error);
              var RequestTimedOut = function(_super) {
                __extends(RequestTimedOut2, _super);
                function RequestTimedOut2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return RequestTimedOut2;
              }(Error);
              var TransportPriorityTooLow = function(_super) {
                __extends(TransportPriorityTooLow2, _super);
                function TransportPriorityTooLow2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return TransportPriorityTooLow2;
              }(Error);
              var TransportClosed = function(_super) {
                __extends(TransportClosed2, _super);
                function TransportClosed2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return TransportClosed2;
              }(Error);
              var UnsupportedFeature = function(_super) {
                __extends(UnsupportedFeature2, _super);
                function UnsupportedFeature2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return UnsupportedFeature2;
              }(Error);
              var UnsupportedTransport = function(_super) {
                __extends(UnsupportedTransport2, _super);
                function UnsupportedTransport2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return UnsupportedTransport2;
              }(Error);
              var UnsupportedStrategy = function(_super) {
                __extends(UnsupportedStrategy2, _super);
                function UnsupportedStrategy2(msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return UnsupportedStrategy2;
              }(Error);
              var HTTPAuthError = function(_super) {
                __extends(HTTPAuthError2, _super);
                function HTTPAuthError2(status, msg) {
                  var _newTarget = this.constructor;
                  var _this = _super.call(this, msg) || this;
                  _this.status = status;
                  Object.setPrototypeOf(_this, _newTarget.prototype);
                  return _this;
                }
                return HTTPAuthError2;
              }(Error);
              var ajax = function(context, query, authOptions, authRequestType, callback) {
                var xhr = runtime.createXHR();
                xhr.open("POST", authOptions.endpoint, true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                for (var headerName in authOptions.headers) {
                  xhr.setRequestHeader(headerName, authOptions.headers[headerName]);
                }
                if (authOptions.headersProvider != null) {
                  var dynamicHeaders = authOptions.headersProvider();
                  for (var headerName in dynamicHeaders) {
                    xhr.setRequestHeader(headerName, dynamicHeaders[headerName]);
                  }
                }
                xhr.onreadystatechange = function() {
                  if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                      var data = void 0;
                      var parsed = false;
                      try {
                        data = JSON.parse(xhr.responseText);
                        parsed = true;
                      } catch (e) {
                        callback(new HTTPAuthError(200, "JSON returned from " + authRequestType.toString() + " endpoint was invalid, yet status code was 200. Data was: " + xhr.responseText), null);
                      }
                      if (parsed) {
                        callback(null, data);
                      }
                    } else {
                      var suffix = "";
                      switch (authRequestType) {
                        case AuthRequestType.UserAuthentication:
                          suffix = url_store.buildLogSuffix("authenticationEndpoint");
                          break;
                        case AuthRequestType.ChannelAuthorization:
                          suffix = "Clients must be authorized to join private or presence channels. " + url_store.buildLogSuffix("authorizationEndpoint");
                          break;
                      }
                      callback(new HTTPAuthError(xhr.status, "Unable to retrieve auth string from " + authRequestType.toString() + " endpoint - " + ("received status: " + xhr.status + " from " + authOptions.endpoint + ". " + suffix)), null);
                    }
                  }
                };
                xhr.send(query);
                return xhr;
              };
              var xhr_auth = ajax;
              function encode(s) {
                return btoa(utob(s));
              }
              var fromCharCode = String.fromCharCode;
              var b64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
              var b64tab = {};
              for (var base64_i = 0, l = b64chars.length; base64_i < l; base64_i++) {
                b64tab[b64chars.charAt(base64_i)] = base64_i;
              }
              var cb_utob = function(c) {
                var cc = c.charCodeAt(0);
                return cc < 128 ? c : cc < 2048 ? fromCharCode(192 | cc >>> 6) + fromCharCode(128 | cc & 63) : fromCharCode(224 | cc >>> 12 & 15) + fromCharCode(128 | cc >>> 6 & 63) + fromCharCode(128 | cc & 63);
              };
              var utob = function(u) {
                return u.replace(/[^\x00-\x7F]/g, cb_utob);
              };
              var cb_encode = function(ccc) {
                var padlen = [0, 2, 1][ccc.length % 3];
                var ord = ccc.charCodeAt(0) << 16 | (ccc.length > 1 ? ccc.charCodeAt(1) : 0) << 8 | (ccc.length > 2 ? ccc.charCodeAt(2) : 0);
                var chars = [
                  b64chars.charAt(ord >>> 18),
                  b64chars.charAt(ord >>> 12 & 63),
                  padlen >= 2 ? "=" : b64chars.charAt(ord >>> 6 & 63),
                  padlen >= 1 ? "=" : b64chars.charAt(ord & 63)
                ];
                return chars.join("");
              };
              var btoa = window.btoa || function(b) {
                return b.replace(/[\s\S]{1,3}/g, cb_encode);
              };
              var Timer = function() {
                function Timer2(set, clear, delay, callback) {
                  var _this = this;
                  this.clear = clear;
                  this.timer = set(function() {
                    if (_this.timer) {
                      _this.timer = callback(_this.timer);
                    }
                  }, delay);
                }
                Timer2.prototype.isRunning = function() {
                  return this.timer !== null;
                };
                Timer2.prototype.ensureAborted = function() {
                  if (this.timer) {
                    this.clear(this.timer);
                    this.timer = null;
                  }
                };
                return Timer2;
              }();
              var abstract_timer = Timer;
              var timers_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              function timers_clearTimeout(timer) {
                window.clearTimeout(timer);
              }
              function timers_clearInterval(timer) {
                window.clearInterval(timer);
              }
              var OneOffTimer = function(_super) {
                timers_extends(OneOffTimer2, _super);
                function OneOffTimer2(delay, callback) {
                  return _super.call(this, setTimeout, timers_clearTimeout, delay, function(timer) {
                    callback();
                    return null;
                  }) || this;
                }
                return OneOffTimer2;
              }(abstract_timer);
              var PeriodicTimer = function(_super) {
                timers_extends(PeriodicTimer2, _super);
                function PeriodicTimer2(delay, callback) {
                  return _super.call(this, setInterval, timers_clearInterval, delay, function(timer) {
                    callback();
                    return timer;
                  }) || this;
                }
                return PeriodicTimer2;
              }(abstract_timer);
              var Util = {
                now: function() {
                  if (Date.now) {
                    return Date.now();
                  } else {
                    return (/* @__PURE__ */ new Date()).valueOf();
                  }
                },
                defer: function(callback) {
                  return new OneOffTimer(0, callback);
                },
                method: function(name) {
                  var args = [];
                  for (var _i = 1; _i < arguments.length; _i++) {
                    args[_i - 1] = arguments[_i];
                  }
                  var boundArguments = Array.prototype.slice.call(arguments, 1);
                  return function(object) {
                    return object[name].apply(object, boundArguments.concat(arguments));
                  };
                }
              };
              var util = Util;
              function extend(target) {
                var sources = [];
                for (var _i = 1; _i < arguments.length; _i++) {
                  sources[_i - 1] = arguments[_i];
                }
                for (var i = 0; i < sources.length; i++) {
                  var extensions = sources[i];
                  for (var property in extensions) {
                    if (extensions[property] && extensions[property].constructor && extensions[property].constructor === Object) {
                      target[property] = extend(target[property] || {}, extensions[property]);
                    } else {
                      target[property] = extensions[property];
                    }
                  }
                }
                return target;
              }
              function stringify() {
                var m = ["Pusher"];
                for (var i = 0; i < arguments.length; i++) {
                  if (typeof arguments[i] === "string") {
                    m.push(arguments[i]);
                  } else {
                    m.push(safeJSONStringify(arguments[i]));
                  }
                }
                return m.join(" : ");
              }
              function arrayIndexOf(array, item) {
                var nativeIndexOf = Array.prototype.indexOf;
                if (array === null) {
                  return -1;
                }
                if (nativeIndexOf && array.indexOf === nativeIndexOf) {
                  return array.indexOf(item);
                }
                for (var i = 0, l2 = array.length; i < l2; i++) {
                  if (array[i] === item) {
                    return i;
                  }
                }
                return -1;
              }
              function objectApply(object, f) {
                for (var key in object) {
                  if (Object.prototype.hasOwnProperty.call(object, key)) {
                    f(object[key], key, object);
                  }
                }
              }
              function keys(object) {
                var keys2 = [];
                objectApply(object, function(_, key) {
                  keys2.push(key);
                });
                return keys2;
              }
              function values(object) {
                var values2 = [];
                objectApply(object, function(value) {
                  values2.push(value);
                });
                return values2;
              }
              function apply(array, f, context) {
                for (var i = 0; i < array.length; i++) {
                  f.call(context || window, array[i], i, array);
                }
              }
              function map(array, f) {
                var result = [];
                for (var i = 0; i < array.length; i++) {
                  result.push(f(array[i], i, array, result));
                }
                return result;
              }
              function mapObject(object, f) {
                var result = {};
                objectApply(object, function(value, key) {
                  result[key] = f(value);
                });
                return result;
              }
              function filter(array, test) {
                test = test || function(value) {
                  return !!value;
                };
                var result = [];
                for (var i = 0; i < array.length; i++) {
                  if (test(array[i], i, array, result)) {
                    result.push(array[i]);
                  }
                }
                return result;
              }
              function filterObject(object, test) {
                var result = {};
                objectApply(object, function(value, key) {
                  if (test && test(value, key, object, result) || Boolean(value)) {
                    result[key] = value;
                  }
                });
                return result;
              }
              function flatten(object) {
                var result = [];
                objectApply(object, function(value, key) {
                  result.push([key, value]);
                });
                return result;
              }
              function any(array, test) {
                for (var i = 0; i < array.length; i++) {
                  if (test(array[i], i, array)) {
                    return true;
                  }
                }
                return false;
              }
              function collections_all(array, test) {
                for (var i = 0; i < array.length; i++) {
                  if (!test(array[i], i, array)) {
                    return false;
                  }
                }
                return true;
              }
              function encodeParamsObject(data) {
                return mapObject(data, function(value) {
                  if (typeof value === "object") {
                    value = safeJSONStringify(value);
                  }
                  return encodeURIComponent(encode(value.toString()));
                });
              }
              function buildQueryString(data) {
                var params = filterObject(data, function(value) {
                  return value !== void 0;
                });
                var query = map(flatten(encodeParamsObject(params)), util.method("join", "=")).join("&");
                return query;
              }
              function decycleObject(object) {
                var objects = [], paths = [];
                return function derez(value, path) {
                  var i, name, nu;
                  switch (typeof value) {
                    case "object":
                      if (!value) {
                        return null;
                      }
                      for (i = 0; i < objects.length; i += 1) {
                        if (objects[i] === value) {
                          return { $ref: paths[i] };
                        }
                      }
                      objects.push(value);
                      paths.push(path);
                      if (Object.prototype.toString.apply(value) === "[object Array]") {
                        nu = [];
                        for (i = 0; i < value.length; i += 1) {
                          nu[i] = derez(value[i], path + "[" + i + "]");
                        }
                      } else {
                        nu = {};
                        for (name in value) {
                          if (Object.prototype.hasOwnProperty.call(value, name)) {
                            nu[name] = derez(value[name], path + "[" + JSON.stringify(name) + "]");
                          }
                        }
                      }
                      return nu;
                    case "number":
                    case "string":
                    case "boolean":
                      return value;
                  }
                }(object, "$");
              }
              function safeJSONStringify(source) {
                try {
                  return JSON.stringify(source);
                } catch (e) {
                  return JSON.stringify(decycleObject(source));
                }
              }
              var logger_Logger = function() {
                function Logger() {
                  this.globalLog = function(message) {
                    if (window.console && window.console.log) {
                      window.console.log(message);
                    }
                  };
                }
                Logger.prototype.debug = function() {
                  var args = [];
                  for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                  }
                  this.log(this.globalLog, args);
                };
                Logger.prototype.warn = function() {
                  var args = [];
                  for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                  }
                  this.log(this.globalLogWarn, args);
                };
                Logger.prototype.error = function() {
                  var args = [];
                  for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                  }
                  this.log(this.globalLogError, args);
                };
                Logger.prototype.globalLogWarn = function(message) {
                  if (window.console && window.console.warn) {
                    window.console.warn(message);
                  } else {
                    this.globalLog(message);
                  }
                };
                Logger.prototype.globalLogError = function(message) {
                  if (window.console && window.console.error) {
                    window.console.error(message);
                  } else {
                    this.globalLogWarn(message);
                  }
                };
                Logger.prototype.log = function(defaultLoggingFunction) {
                  var args = [];
                  for (var _i = 1; _i < arguments.length; _i++) {
                    args[_i - 1] = arguments[_i];
                  }
                  var message = stringify.apply(this, arguments);
                  if (core_pusher.log) {
                    core_pusher.log(message);
                  } else if (core_pusher.logToConsole) {
                    var log = defaultLoggingFunction.bind(this);
                    log(message);
                  }
                };
                return Logger;
              }();
              var logger = new logger_Logger();
              var jsonp = function(context, query, authOptions, authRequestType, callback) {
                if (authOptions.headers !== void 0 || authOptions.headersProvider != null) {
                  logger.warn("To send headers with the " + authRequestType.toString() + " request, you must use AJAX, rather than JSONP.");
                }
                var callbackName = context.nextAuthCallbackID.toString();
                context.nextAuthCallbackID++;
                var document2 = context.getDocument();
                var script = document2.createElement("script");
                context.auth_callbacks[callbackName] = function(data) {
                  callback(null, data);
                };
                var callback_name = "Pusher.auth_callbacks['" + callbackName + "']";
                script.src = authOptions.endpoint + "?callback=" + encodeURIComponent(callback_name) + "&" + query;
                var head = document2.getElementsByTagName("head")[0] || document2.documentElement;
                head.insertBefore(script, head.firstChild);
              };
              var jsonp_auth = jsonp;
              var ScriptRequest = function() {
                function ScriptRequest2(src) {
                  this.src = src;
                }
                ScriptRequest2.prototype.send = function(receiver) {
                  var self = this;
                  var errorString = "Error loading " + self.src;
                  self.script = document.createElement("script");
                  self.script.id = receiver.id;
                  self.script.src = self.src;
                  self.script.type = "text/javascript";
                  self.script.charset = "UTF-8";
                  if (self.script.addEventListener) {
                    self.script.onerror = function() {
                      receiver.callback(errorString);
                    };
                    self.script.onload = function() {
                      receiver.callback(null);
                    };
                  } else {
                    self.script.onreadystatechange = function() {
                      if (self.script.readyState === "loaded" || self.script.readyState === "complete") {
                        receiver.callback(null);
                      }
                    };
                  }
                  if (self.script.async === void 0 && document.attachEvent && /opera/i.test(navigator.userAgent)) {
                    self.errorScript = document.createElement("script");
                    self.errorScript.id = receiver.id + "_error";
                    self.errorScript.text = receiver.name + "('" + errorString + "');";
                    self.script.async = self.errorScript.async = false;
                  } else {
                    self.script.async = true;
                  }
                  var head = document.getElementsByTagName("head")[0];
                  head.insertBefore(self.script, head.firstChild);
                  if (self.errorScript) {
                    head.insertBefore(self.errorScript, self.script.nextSibling);
                  }
                };
                ScriptRequest2.prototype.cleanup = function() {
                  if (this.script) {
                    this.script.onload = this.script.onerror = null;
                    this.script.onreadystatechange = null;
                  }
                  if (this.script && this.script.parentNode) {
                    this.script.parentNode.removeChild(this.script);
                  }
                  if (this.errorScript && this.errorScript.parentNode) {
                    this.errorScript.parentNode.removeChild(this.errorScript);
                  }
                  this.script = null;
                  this.errorScript = null;
                };
                return ScriptRequest2;
              }();
              var script_request = ScriptRequest;
              var jsonp_request_JSONPRequest = function() {
                function JSONPRequest(url, data) {
                  this.url = url;
                  this.data = data;
                }
                JSONPRequest.prototype.send = function(receiver) {
                  if (this.request) {
                    return;
                  }
                  var query = buildQueryString(this.data);
                  var url = this.url + "/" + receiver.number + "?" + query;
                  this.request = runtime.createScriptRequest(url);
                  this.request.send(receiver);
                };
                JSONPRequest.prototype.cleanup = function() {
                  if (this.request) {
                    this.request.cleanup();
                  }
                };
                return JSONPRequest;
              }();
              var jsonp_request = jsonp_request_JSONPRequest;
              var getAgent = function(sender, useTLS) {
                return function(data, callback) {
                  var scheme = "http" + (useTLS ? "s" : "") + "://";
                  var url = scheme + (sender.host || sender.options.host) + sender.options.path;
                  var request = runtime.createJSONPRequest(url, data);
                  var receiver = runtime.ScriptReceivers.create(function(error, result) {
                    ScriptReceivers.remove(receiver);
                    request.cleanup();
                    if (result && result.host) {
                      sender.host = result.host;
                    }
                    if (callback) {
                      callback(error, result);
                    }
                  });
                  request.send(receiver);
                };
              };
              var jsonp_timeline_jsonp = {
                name: "jsonp",
                getAgent
              };
              var jsonp_timeline = jsonp_timeline_jsonp;
              function getGenericURL(baseScheme, params, path) {
                var scheme = baseScheme + (params.useTLS ? "s" : "");
                var host = params.useTLS ? params.hostTLS : params.hostNonTLS;
                return scheme + "://" + host + path;
              }
              function getGenericPath(key, queryString) {
                var path = "/app/" + key;
                var query = "?protocol=" + defaults.PROTOCOL + "&client=js&version=" + defaults.VERSION + (queryString ? "&" + queryString : "");
                return path + query;
              }
              var ws = {
                getInitial: function(key, params) {
                  var path = (params.httpPath || "") + getGenericPath(key, "flash=false");
                  return getGenericURL("ws", params, path);
                }
              };
              var http = {
                getInitial: function(key, params) {
                  var path = (params.httpPath || "/pusher") + getGenericPath(key);
                  return getGenericURL("http", params, path);
                }
              };
              var sockjs = {
                getInitial: function(key, params) {
                  return getGenericURL("http", params, params.httpPath || "/pusher");
                },
                getPath: function(key, params) {
                  return getGenericPath(key);
                }
              };
              var callback_registry_CallbackRegistry = function() {
                function CallbackRegistry() {
                  this._callbacks = {};
                }
                CallbackRegistry.prototype.get = function(name) {
                  return this._callbacks[prefix(name)];
                };
                CallbackRegistry.prototype.add = function(name, callback, context) {
                  var prefixedEventName = prefix(name);
                  this._callbacks[prefixedEventName] = this._callbacks[prefixedEventName] || [];
                  this._callbacks[prefixedEventName].push({
                    fn: callback,
                    context
                  });
                };
                CallbackRegistry.prototype.remove = function(name, callback, context) {
                  if (!name && !callback && !context) {
                    this._callbacks = {};
                    return;
                  }
                  var names = name ? [prefix(name)] : keys(this._callbacks);
                  if (callback || context) {
                    this.removeCallback(names, callback, context);
                  } else {
                    this.removeAllCallbacks(names);
                  }
                };
                CallbackRegistry.prototype.removeCallback = function(names, callback, context) {
                  apply(names, function(name) {
                    this._callbacks[name] = filter(this._callbacks[name] || [], function(binding) {
                      return callback && callback !== binding.fn || context && context !== binding.context;
                    });
                    if (this._callbacks[name].length === 0) {
                      delete this._callbacks[name];
                    }
                  }, this);
                };
                CallbackRegistry.prototype.removeAllCallbacks = function(names) {
                  apply(names, function(name) {
                    delete this._callbacks[name];
                  }, this);
                };
                return CallbackRegistry;
              }();
              var callback_registry = callback_registry_CallbackRegistry;
              function prefix(name) {
                return "_" + name;
              }
              var dispatcher_Dispatcher = function() {
                function Dispatcher(failThrough) {
                  this.callbacks = new callback_registry();
                  this.global_callbacks = [];
                  this.failThrough = failThrough;
                }
                Dispatcher.prototype.bind = function(eventName, callback, context) {
                  this.callbacks.add(eventName, callback, context);
                  return this;
                };
                Dispatcher.prototype.bind_global = function(callback) {
                  this.global_callbacks.push(callback);
                  return this;
                };
                Dispatcher.prototype.unbind = function(eventName, callback, context) {
                  this.callbacks.remove(eventName, callback, context);
                  return this;
                };
                Dispatcher.prototype.unbind_global = function(callback) {
                  if (!callback) {
                    this.global_callbacks = [];
                    return this;
                  }
                  this.global_callbacks = filter(this.global_callbacks || [], function(c) {
                    return c !== callback;
                  });
                  return this;
                };
                Dispatcher.prototype.unbind_all = function() {
                  this.unbind();
                  this.unbind_global();
                  return this;
                };
                Dispatcher.prototype.emit = function(eventName, data, metadata) {
                  for (var i = 0; i < this.global_callbacks.length; i++) {
                    this.global_callbacks[i](eventName, data);
                  }
                  var callbacks = this.callbacks.get(eventName);
                  var args = [];
                  if (metadata) {
                    args.push(data, metadata);
                  } else if (data) {
                    args.push(data);
                  }
                  if (callbacks && callbacks.length > 0) {
                    for (var i = 0; i < callbacks.length; i++) {
                      callbacks[i].fn.apply(callbacks[i].context || window, args);
                    }
                  } else if (this.failThrough) {
                    this.failThrough(eventName, data);
                  }
                  return this;
                };
                return Dispatcher;
              }();
              var dispatcher = dispatcher_Dispatcher;
              var transport_connection_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var transport_connection_TransportConnection = function(_super) {
                transport_connection_extends(TransportConnection, _super);
                function TransportConnection(hooks, name, priority, key, options) {
                  var _this = _super.call(this) || this;
                  _this.initialize = runtime.transportConnectionInitializer;
                  _this.hooks = hooks;
                  _this.name = name;
                  _this.priority = priority;
                  _this.key = key;
                  _this.options = options;
                  _this.state = "new";
                  _this.timeline = options.timeline;
                  _this.activityTimeout = options.activityTimeout;
                  _this.id = _this.timeline.generateUniqueID();
                  return _this;
                }
                TransportConnection.prototype.handlesActivityChecks = function() {
                  return Boolean(this.hooks.handlesActivityChecks);
                };
                TransportConnection.prototype.supportsPing = function() {
                  return Boolean(this.hooks.supportsPing);
                };
                TransportConnection.prototype.connect = function() {
                  var _this = this;
                  if (this.socket || this.state !== "initialized") {
                    return false;
                  }
                  var url = this.hooks.urls.getInitial(this.key, this.options);
                  try {
                    this.socket = this.hooks.getSocket(url, this.options);
                  } catch (e) {
                    util.defer(function() {
                      _this.onError(e);
                      _this.changeState("closed");
                    });
                    return false;
                  }
                  this.bindListeners();
                  logger.debug("Connecting", { transport: this.name, url });
                  this.changeState("connecting");
                  return true;
                };
                TransportConnection.prototype.close = function() {
                  if (this.socket) {
                    this.socket.close();
                    return true;
                  } else {
                    return false;
                  }
                };
                TransportConnection.prototype.send = function(data) {
                  var _this = this;
                  if (this.state === "open") {
                    util.defer(function() {
                      if (_this.socket) {
                        _this.socket.send(data);
                      }
                    });
                    return true;
                  } else {
                    return false;
                  }
                };
                TransportConnection.prototype.ping = function() {
                  if (this.state === "open" && this.supportsPing()) {
                    this.socket.ping();
                  }
                };
                TransportConnection.prototype.onOpen = function() {
                  if (this.hooks.beforeOpen) {
                    this.hooks.beforeOpen(this.socket, this.hooks.urls.getPath(this.key, this.options));
                  }
                  this.changeState("open");
                  this.socket.onopen = void 0;
                };
                TransportConnection.prototype.onError = function(error) {
                  this.emit("error", { type: "WebSocketError", error });
                  this.timeline.error(this.buildTimelineMessage({ error: error.toString() }));
                };
                TransportConnection.prototype.onClose = function(closeEvent) {
                  if (closeEvent) {
                    this.changeState("closed", {
                      code: closeEvent.code,
                      reason: closeEvent.reason,
                      wasClean: closeEvent.wasClean
                    });
                  } else {
                    this.changeState("closed");
                  }
                  this.unbindListeners();
                  this.socket = void 0;
                };
                TransportConnection.prototype.onMessage = function(message) {
                  this.emit("message", message);
                };
                TransportConnection.prototype.onActivity = function() {
                  this.emit("activity");
                };
                TransportConnection.prototype.bindListeners = function() {
                  var _this = this;
                  this.socket.onopen = function() {
                    _this.onOpen();
                  };
                  this.socket.onerror = function(error) {
                    _this.onError(error);
                  };
                  this.socket.onclose = function(closeEvent) {
                    _this.onClose(closeEvent);
                  };
                  this.socket.onmessage = function(message) {
                    _this.onMessage(message);
                  };
                  if (this.supportsPing()) {
                    this.socket.onactivity = function() {
                      _this.onActivity();
                    };
                  }
                };
                TransportConnection.prototype.unbindListeners = function() {
                  if (this.socket) {
                    this.socket.onopen = void 0;
                    this.socket.onerror = void 0;
                    this.socket.onclose = void 0;
                    this.socket.onmessage = void 0;
                    if (this.supportsPing()) {
                      this.socket.onactivity = void 0;
                    }
                  }
                };
                TransportConnection.prototype.changeState = function(state2, params) {
                  this.state = state2;
                  this.timeline.info(this.buildTimelineMessage({
                    state: state2,
                    params
                  }));
                  this.emit(state2, params);
                };
                TransportConnection.prototype.buildTimelineMessage = function(message) {
                  return extend({ cid: this.id }, message);
                };
                return TransportConnection;
              }(dispatcher);
              var transport_connection = transport_connection_TransportConnection;
              var transport_Transport = function() {
                function Transport(hooks) {
                  this.hooks = hooks;
                }
                Transport.prototype.isSupported = function(environment) {
                  return this.hooks.isSupported(environment);
                };
                Transport.prototype.createConnection = function(name, priority, key, options) {
                  return new transport_connection(this.hooks, name, priority, key, options);
                };
                return Transport;
              }();
              var transports_transport = transport_Transport;
              var WSTransport = new transports_transport({
                urls: ws,
                handlesActivityChecks: false,
                supportsPing: false,
                isInitialized: function() {
                  return Boolean(runtime.getWebSocketAPI());
                },
                isSupported: function() {
                  return Boolean(runtime.getWebSocketAPI());
                },
                getSocket: function(url) {
                  return runtime.createWebSocket(url);
                }
              });
              var httpConfiguration = {
                urls: http,
                handlesActivityChecks: false,
                supportsPing: true,
                isInitialized: function() {
                  return true;
                }
              };
              var streamingConfiguration = extend({
                getSocket: function(url) {
                  return runtime.HTTPFactory.createStreamingSocket(url);
                }
              }, httpConfiguration);
              var pollingConfiguration = extend({
                getSocket: function(url) {
                  return runtime.HTTPFactory.createPollingSocket(url);
                }
              }, httpConfiguration);
              var xhrConfiguration = {
                isSupported: function() {
                  return runtime.isXHRSupported();
                }
              };
              var XHRStreamingTransport = new transports_transport(extend({}, streamingConfiguration, xhrConfiguration));
              var XHRPollingTransport = new transports_transport(extend({}, pollingConfiguration, xhrConfiguration));
              var Transports = {
                ws: WSTransport,
                xhr_streaming: XHRStreamingTransport,
                xhr_polling: XHRPollingTransport
              };
              var transports = Transports;
              var SockJSTransport = new transports_transport({
                file: "sockjs",
                urls: sockjs,
                handlesActivityChecks: true,
                supportsPing: false,
                isSupported: function() {
                  return true;
                },
                isInitialized: function() {
                  return window.SockJS !== void 0;
                },
                getSocket: function(url, options) {
                  return new window.SockJS(url, null, {
                    js_path: Dependencies.getPath("sockjs", {
                      useTLS: options.useTLS
                    }),
                    ignore_null_origin: options.ignoreNullOrigin
                  });
                },
                beforeOpen: function(socket, path) {
                  socket.send(JSON.stringify({
                    path
                  }));
                }
              });
              var xdrConfiguration = {
                isSupported: function(environment) {
                  var yes = runtime.isXDRSupported(environment.useTLS);
                  return yes;
                }
              };
              var XDRStreamingTransport = new transports_transport(extend({}, streamingConfiguration, xdrConfiguration));
              var XDRPollingTransport = new transports_transport(extend({}, pollingConfiguration, xdrConfiguration));
              transports.xdr_streaming = XDRStreamingTransport;
              transports.xdr_polling = XDRPollingTransport;
              transports.sockjs = SockJSTransport;
              var transports_transports = transports;
              var net_info_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var NetInfo = function(_super) {
                net_info_extends(NetInfo2, _super);
                function NetInfo2() {
                  var _this = _super.call(this) || this;
                  var self = _this;
                  if (window.addEventListener !== void 0) {
                    window.addEventListener("online", function() {
                      self.emit("online");
                    }, false);
                    window.addEventListener("offline", function() {
                      self.emit("offline");
                    }, false);
                  }
                  return _this;
                }
                NetInfo2.prototype.isOnline = function() {
                  if (window.navigator.onLine === void 0) {
                    return true;
                  } else {
                    return window.navigator.onLine;
                  }
                };
                return NetInfo2;
              }(dispatcher);
              var net_info_Network = new NetInfo();
              var assistant_to_the_transport_manager_AssistantToTheTransportManager = function() {
                function AssistantToTheTransportManager(manager, transport, options) {
                  this.manager = manager;
                  this.transport = transport;
                  this.minPingDelay = options.minPingDelay;
                  this.maxPingDelay = options.maxPingDelay;
                  this.pingDelay = void 0;
                }
                AssistantToTheTransportManager.prototype.createConnection = function(name, priority, key, options) {
                  var _this = this;
                  options = extend({}, options, {
                    activityTimeout: this.pingDelay
                  });
                  var connection = this.transport.createConnection(name, priority, key, options);
                  var openTimestamp = null;
                  var onOpen = function() {
                    connection.unbind("open", onOpen);
                    connection.bind("closed", onClosed);
                    openTimestamp = util.now();
                  };
                  var onClosed = function(closeEvent) {
                    connection.unbind("closed", onClosed);
                    if (closeEvent.code === 1002 || closeEvent.code === 1003) {
                      _this.manager.reportDeath();
                    } else if (!closeEvent.wasClean && openTimestamp) {
                      var lifespan = util.now() - openTimestamp;
                      if (lifespan < 2 * _this.maxPingDelay) {
                        _this.manager.reportDeath();
                        _this.pingDelay = Math.max(lifespan / 2, _this.minPingDelay);
                      }
                    }
                  };
                  connection.bind("open", onOpen);
                  return connection;
                };
                AssistantToTheTransportManager.prototype.isSupported = function(environment) {
                  return this.manager.isAlive() && this.transport.isSupported(environment);
                };
                return AssistantToTheTransportManager;
              }();
              var assistant_to_the_transport_manager = assistant_to_the_transport_manager_AssistantToTheTransportManager;
              var Protocol = {
                decodeMessage: function(messageEvent) {
                  try {
                    var messageData = JSON.parse(messageEvent.data);
                    var pusherEventData = messageData.data;
                    if (typeof pusherEventData === "string") {
                      try {
                        pusherEventData = JSON.parse(messageData.data);
                      } catch (e) {
                      }
                    }
                    var pusherEvent = {
                      event: messageData.event,
                      channel: messageData.channel,
                      data: pusherEventData
                    };
                    if (messageData.user_id) {
                      pusherEvent.user_id = messageData.user_id;
                    }
                    return pusherEvent;
                  } catch (e) {
                    throw { type: "MessageParseError", error: e, data: messageEvent.data };
                  }
                },
                encodeMessage: function(event) {
                  return JSON.stringify(event);
                },
                processHandshake: function(messageEvent) {
                  var message = Protocol.decodeMessage(messageEvent);
                  if (message.event === "pusher:connection_established") {
                    if (!message.data.activity_timeout) {
                      throw "No activity timeout specified in handshake";
                    }
                    return {
                      action: "connected",
                      id: message.data.socket_id,
                      activityTimeout: message.data.activity_timeout * 1e3
                    };
                  } else if (message.event === "pusher:error") {
                    return {
                      action: this.getCloseAction(message.data),
                      error: this.getCloseError(message.data)
                    };
                  } else {
                    throw "Invalid handshake";
                  }
                },
                getCloseAction: function(closeEvent) {
                  if (closeEvent.code < 4e3) {
                    if (closeEvent.code >= 1002 && closeEvent.code <= 1004) {
                      return "backoff";
                    } else {
                      return null;
                    }
                  } else if (closeEvent.code === 4e3) {
                    return "tls_only";
                  } else if (closeEvent.code < 4100) {
                    return "refused";
                  } else if (closeEvent.code < 4200) {
                    return "backoff";
                  } else if (closeEvent.code < 4300) {
                    return "retry";
                  } else {
                    return "refused";
                  }
                },
                getCloseError: function(closeEvent) {
                  if (closeEvent.code !== 1e3 && closeEvent.code !== 1001) {
                    return {
                      type: "PusherError",
                      data: {
                        code: closeEvent.code,
                        message: closeEvent.reason || closeEvent.message
                      }
                    };
                  } else {
                    return null;
                  }
                }
              };
              var protocol_protocol = Protocol;
              var connection_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var connection_Connection = function(_super) {
                connection_extends(Connection, _super);
                function Connection(id, transport) {
                  var _this = _super.call(this) || this;
                  _this.id = id;
                  _this.transport = transport;
                  _this.activityTimeout = transport.activityTimeout;
                  _this.bindListeners();
                  return _this;
                }
                Connection.prototype.handlesActivityChecks = function() {
                  return this.transport.handlesActivityChecks();
                };
                Connection.prototype.send = function(data) {
                  return this.transport.send(data);
                };
                Connection.prototype.send_event = function(name, data, channel) {
                  var event = { event: name, data };
                  if (channel) {
                    event.channel = channel;
                  }
                  logger.debug("Event sent", event);
                  return this.send(protocol_protocol.encodeMessage(event));
                };
                Connection.prototype.ping = function() {
                  if (this.transport.supportsPing()) {
                    this.transport.ping();
                  } else {
                    this.send_event("pusher:ping", {});
                  }
                };
                Connection.prototype.close = function() {
                  this.transport.close();
                };
                Connection.prototype.bindListeners = function() {
                  var _this = this;
                  var listeners = {
                    message: function(messageEvent) {
                      var pusherEvent;
                      try {
                        pusherEvent = protocol_protocol.decodeMessage(messageEvent);
                      } catch (e) {
                        _this.emit("error", {
                          type: "MessageParseError",
                          error: e,
                          data: messageEvent.data
                        });
                      }
                      if (pusherEvent !== void 0) {
                        logger.debug("Event recd", pusherEvent);
                        switch (pusherEvent.event) {
                          case "pusher:error":
                            _this.emit("error", {
                              type: "PusherError",
                              data: pusherEvent.data
                            });
                            break;
                          case "pusher:ping":
                            _this.emit("ping");
                            break;
                          case "pusher:pong":
                            _this.emit("pong");
                            break;
                        }
                        _this.emit("message", pusherEvent);
                      }
                    },
                    activity: function() {
                      _this.emit("activity");
                    },
                    error: function(error) {
                      _this.emit("error", error);
                    },
                    closed: function(closeEvent) {
                      unbindListeners();
                      if (closeEvent && closeEvent.code) {
                        _this.handleCloseEvent(closeEvent);
                      }
                      _this.transport = null;
                      _this.emit("closed");
                    }
                  };
                  var unbindListeners = function() {
                    objectApply(listeners, function(listener, event) {
                      _this.transport.unbind(event, listener);
                    });
                  };
                  objectApply(listeners, function(listener, event) {
                    _this.transport.bind(event, listener);
                  });
                };
                Connection.prototype.handleCloseEvent = function(closeEvent) {
                  var action = protocol_protocol.getCloseAction(closeEvent);
                  var error = protocol_protocol.getCloseError(closeEvent);
                  if (error) {
                    this.emit("error", error);
                  }
                  if (action) {
                    this.emit(action, { action, error });
                  }
                };
                return Connection;
              }(dispatcher);
              var connection_connection = connection_Connection;
              var handshake_Handshake = function() {
                function Handshake(transport, callback) {
                  this.transport = transport;
                  this.callback = callback;
                  this.bindListeners();
                }
                Handshake.prototype.close = function() {
                  this.unbindListeners();
                  this.transport.close();
                };
                Handshake.prototype.bindListeners = function() {
                  var _this = this;
                  this.onMessage = function(m) {
                    _this.unbindListeners();
                    var result;
                    try {
                      result = protocol_protocol.processHandshake(m);
                    } catch (e) {
                      _this.finish("error", { error: e });
                      _this.transport.close();
                      return;
                    }
                    if (result.action === "connected") {
                      _this.finish("connected", {
                        connection: new connection_connection(result.id, _this.transport),
                        activityTimeout: result.activityTimeout
                      });
                    } else {
                      _this.finish(result.action, { error: result.error });
                      _this.transport.close();
                    }
                  };
                  this.onClosed = function(closeEvent) {
                    _this.unbindListeners();
                    var action = protocol_protocol.getCloseAction(closeEvent) || "backoff";
                    var error = protocol_protocol.getCloseError(closeEvent);
                    _this.finish(action, { error });
                  };
                  this.transport.bind("message", this.onMessage);
                  this.transport.bind("closed", this.onClosed);
                };
                Handshake.prototype.unbindListeners = function() {
                  this.transport.unbind("message", this.onMessage);
                  this.transport.unbind("closed", this.onClosed);
                };
                Handshake.prototype.finish = function(action, params) {
                  this.callback(extend({ transport: this.transport, action }, params));
                };
                return Handshake;
              }();
              var connection_handshake = handshake_Handshake;
              var timeline_sender_TimelineSender = function() {
                function TimelineSender(timeline, options) {
                  this.timeline = timeline;
                  this.options = options || {};
                }
                TimelineSender.prototype.send = function(useTLS, callback) {
                  if (this.timeline.isEmpty()) {
                    return;
                  }
                  this.timeline.send(runtime.TimelineTransport.getAgent(this, useTLS), callback);
                };
                return TimelineSender;
              }();
              var timeline_sender = timeline_sender_TimelineSender;
              var channel_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var channel_Channel = function(_super) {
                channel_extends(Channel2, _super);
                function Channel2(name, pusher) {
                  var _this = _super.call(this, function(event, data) {
                    logger.debug("No callbacks on " + name + " for " + event);
                  }) || this;
                  _this.name = name;
                  _this.pusher = pusher;
                  _this.subscribed = false;
                  _this.subscriptionPending = false;
                  _this.subscriptionCancelled = false;
                  return _this;
                }
                Channel2.prototype.authorize = function(socketId, callback) {
                  return callback(null, { auth: "" });
                };
                Channel2.prototype.trigger = function(event, data) {
                  if (event.indexOf("client-") !== 0) {
                    throw new BadEventName("Event '" + event + "' does not start with 'client-'");
                  }
                  if (!this.subscribed) {
                    var suffix = url_store.buildLogSuffix("triggeringClientEvents");
                    logger.warn("Client event triggered before channel 'subscription_succeeded' event . " + suffix);
                  }
                  return this.pusher.send_event(event, data, this.name);
                };
                Channel2.prototype.disconnect = function() {
                  this.subscribed = false;
                  this.subscriptionPending = false;
                };
                Channel2.prototype.handleEvent = function(event) {
                  var eventName = event.event;
                  var data = event.data;
                  if (eventName === "pusher_internal:subscription_succeeded") {
                    this.handleSubscriptionSucceededEvent(event);
                  } else if (eventName === "pusher_internal:subscription_count") {
                    this.handleSubscriptionCountEvent(event);
                  } else if (eventName.indexOf("pusher_internal:") !== 0) {
                    var metadata = {};
                    this.emit(eventName, data, metadata);
                  }
                };
                Channel2.prototype.handleSubscriptionSucceededEvent = function(event) {
                  this.subscriptionPending = false;
                  this.subscribed = true;
                  if (this.subscriptionCancelled) {
                    this.pusher.unsubscribe(this.name);
                  } else {
                    this.emit("pusher:subscription_succeeded", event.data);
                  }
                };
                Channel2.prototype.handleSubscriptionCountEvent = function(event) {
                  if (event.data.subscription_count) {
                    this.subscriptionCount = event.data.subscription_count;
                  }
                  this.emit("pusher:subscription_count", event.data);
                };
                Channel2.prototype.subscribe = function() {
                  var _this = this;
                  if (this.subscribed) {
                    return;
                  }
                  this.subscriptionPending = true;
                  this.subscriptionCancelled = false;
                  this.authorize(this.pusher.connection.socket_id, function(error, data) {
                    if (error) {
                      _this.subscriptionPending = false;
                      logger.error(error.toString());
                      _this.emit("pusher:subscription_error", Object.assign({}, {
                        type: "AuthError",
                        error: error.message
                      }, error instanceof HTTPAuthError ? { status: error.status } : {}));
                    } else {
                      _this.pusher.send_event("pusher:subscribe", {
                        auth: data.auth,
                        channel_data: data.channel_data,
                        channel: _this.name
                      });
                    }
                  });
                };
                Channel2.prototype.unsubscribe = function() {
                  this.subscribed = false;
                  this.pusher.send_event("pusher:unsubscribe", {
                    channel: this.name
                  });
                };
                Channel2.prototype.cancelSubscription = function() {
                  this.subscriptionCancelled = true;
                };
                Channel2.prototype.reinstateSubscription = function() {
                  this.subscriptionCancelled = false;
                };
                return Channel2;
              }(dispatcher);
              var channels_channel = channel_Channel;
              var private_channel_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var PrivateChannel = function(_super) {
                private_channel_extends(PrivateChannel2, _super);
                function PrivateChannel2() {
                  return _super !== null && _super.apply(this, arguments) || this;
                }
                PrivateChannel2.prototype.authorize = function(socketId, callback) {
                  return this.pusher.config.channelAuthorizer({
                    channelName: this.name,
                    socketId
                  }, callback);
                };
                return PrivateChannel2;
              }(channels_channel);
              var private_channel = PrivateChannel;
              var members_Members = function() {
                function Members() {
                  this.reset();
                }
                Members.prototype.get = function(id) {
                  if (Object.prototype.hasOwnProperty.call(this.members, id)) {
                    return {
                      id,
                      info: this.members[id]
                    };
                  } else {
                    return null;
                  }
                };
                Members.prototype.each = function(callback) {
                  var _this = this;
                  objectApply(this.members, function(member, id) {
                    callback(_this.get(id));
                  });
                };
                Members.prototype.setMyID = function(id) {
                  this.myID = id;
                };
                Members.prototype.onSubscription = function(subscriptionData) {
                  this.members = subscriptionData.presence.hash;
                  this.count = subscriptionData.presence.count;
                  this.me = this.get(this.myID);
                };
                Members.prototype.addMember = function(memberData) {
                  if (this.get(memberData.user_id) === null) {
                    this.count++;
                  }
                  this.members[memberData.user_id] = memberData.user_info;
                  return this.get(memberData.user_id);
                };
                Members.prototype.removeMember = function(memberData) {
                  var member = this.get(memberData.user_id);
                  if (member) {
                    delete this.members[memberData.user_id];
                    this.count--;
                  }
                  return member;
                };
                Members.prototype.reset = function() {
                  this.members = {};
                  this.count = 0;
                  this.myID = null;
                  this.me = null;
                };
                return Members;
              }();
              var members = members_Members;
              var presence_channel_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var __awaiter = function(thisArg, _arguments, P, generator) {
                function adopt(value) {
                  return value instanceof P ? value : new P(function(resolve) {
                    resolve(value);
                  });
                }
                return new (P || (P = Promise))(function(resolve, reject) {
                  function fulfilled(value) {
                    try {
                      step(generator.next(value));
                    } catch (e) {
                      reject(e);
                    }
                  }
                  function rejected(value) {
                    try {
                      step(generator["throw"](value));
                    } catch (e) {
                      reject(e);
                    }
                  }
                  function step(result) {
                    result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                  }
                  step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
              };
              var __generator = function(thisArg, body) {
                var _ = { label: 0, sent: function() {
                  if (t[0] & 1)
                    throw t[1];
                  return t[1];
                }, trys: [], ops: [] }, f, y, t, g;
                return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() {
                  return this;
                }), g;
                function verb(n) {
                  return function(v) {
                    return step([n, v]);
                  };
                }
                function step(op) {
                  if (f)
                    throw new TypeError("Generator is already executing.");
                  while (_)
                    try {
                      if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done)
                        return t;
                      if (y = 0, t)
                        op = [op[0] & 2, t.value];
                      switch (op[0]) {
                        case 0:
                        case 1:
                          t = op;
                          break;
                        case 4:
                          _.label++;
                          return { value: op[1], done: false };
                        case 5:
                          _.label++;
                          y = op[1];
                          op = [0];
                          continue;
                        case 7:
                          op = _.ops.pop();
                          _.trys.pop();
                          continue;
                        default:
                          if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) {
                            _ = 0;
                            continue;
                          }
                          if (op[0] === 3 && (!t || op[1] > t[0] && op[1] < t[3])) {
                            _.label = op[1];
                            break;
                          }
                          if (op[0] === 6 && _.label < t[1]) {
                            _.label = t[1];
                            t = op;
                            break;
                          }
                          if (t && _.label < t[2]) {
                            _.label = t[2];
                            _.ops.push(op);
                            break;
                          }
                          if (t[2])
                            _.ops.pop();
                          _.trys.pop();
                          continue;
                      }
                      op = body.call(thisArg, _);
                    } catch (e) {
                      op = [6, e];
                      y = 0;
                    } finally {
                      f = t = 0;
                    }
                  if (op[0] & 5)
                    throw op[1];
                  return { value: op[0] ? op[1] : void 0, done: true };
                }
              };
              var presence_channel_PresenceChannel = function(_super) {
                presence_channel_extends(PresenceChannel, _super);
                function PresenceChannel(name, pusher) {
                  var _this = _super.call(this, name, pusher) || this;
                  _this.members = new members();
                  return _this;
                }
                PresenceChannel.prototype.authorize = function(socketId, callback) {
                  var _this = this;
                  _super.prototype.authorize.call(this, socketId, function(error, authData) {
                    return __awaiter(_this, void 0, void 0, function() {
                      var channelData, suffix;
                      return __generator(this, function(_a) {
                        switch (_a.label) {
                          case 0:
                            if (!!error)
                              return [3, 3];
                            authData = authData;
                            if (!(authData.channel_data != null))
                              return [3, 1];
                            channelData = JSON.parse(authData.channel_data);
                            this.members.setMyID(channelData.user_id);
                            return [3, 3];
                          case 1:
                            return [4, this.pusher.user.signinDonePromise];
                          case 2:
                            _a.sent();
                            if (this.pusher.user.user_data != null) {
                              this.members.setMyID(this.pusher.user.user_data.id);
                            } else {
                              suffix = url_store.buildLogSuffix("authorizationEndpoint");
                              logger.error("Invalid auth response for channel '" + this.name + "', " + ("expected 'channel_data' field. " + suffix + ", ") + "or the user should be signed in.");
                              callback("Invalid auth response");
                              return [2];
                            }
                            _a.label = 3;
                          case 3:
                            callback(error, authData);
                            return [2];
                        }
                      });
                    });
                  });
                };
                PresenceChannel.prototype.handleEvent = function(event) {
                  var eventName = event.event;
                  if (eventName.indexOf("pusher_internal:") === 0) {
                    this.handleInternalEvent(event);
                  } else {
                    var data = event.data;
                    var metadata = {};
                    if (event.user_id) {
                      metadata.user_id = event.user_id;
                    }
                    this.emit(eventName, data, metadata);
                  }
                };
                PresenceChannel.prototype.handleInternalEvent = function(event) {
                  var eventName = event.event;
                  var data = event.data;
                  switch (eventName) {
                    case "pusher_internal:subscription_succeeded":
                      this.handleSubscriptionSucceededEvent(event);
                      break;
                    case "pusher_internal:subscription_count":
                      this.handleSubscriptionCountEvent(event);
                      break;
                    case "pusher_internal:member_added":
                      var addedMember = this.members.addMember(data);
                      this.emit("pusher:member_added", addedMember);
                      break;
                    case "pusher_internal:member_removed":
                      var removedMember = this.members.removeMember(data);
                      if (removedMember) {
                        this.emit("pusher:member_removed", removedMember);
                      }
                      break;
                  }
                };
                PresenceChannel.prototype.handleSubscriptionSucceededEvent = function(event) {
                  this.subscriptionPending = false;
                  this.subscribed = true;
                  if (this.subscriptionCancelled) {
                    this.pusher.unsubscribe(this.name);
                  } else {
                    this.members.onSubscription(event.data);
                    this.emit("pusher:subscription_succeeded", this.members);
                  }
                };
                PresenceChannel.prototype.disconnect = function() {
                  this.members.reset();
                  _super.prototype.disconnect.call(this);
                };
                return PresenceChannel;
              }(private_channel);
              var presence_channel = presence_channel_PresenceChannel;
              var utf8 = __webpack_require__(1);
              var base64 = __webpack_require__(0);
              var encrypted_channel_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var encrypted_channel_EncryptedChannel = function(_super) {
                encrypted_channel_extends(EncryptedChannel, _super);
                function EncryptedChannel(name, pusher, nacl) {
                  var _this = _super.call(this, name, pusher) || this;
                  _this.key = null;
                  _this.nacl = nacl;
                  return _this;
                }
                EncryptedChannel.prototype.authorize = function(socketId, callback) {
                  var _this = this;
                  _super.prototype.authorize.call(this, socketId, function(error, authData) {
                    if (error) {
                      callback(error, authData);
                      return;
                    }
                    var sharedSecret = authData["shared_secret"];
                    if (!sharedSecret) {
                      callback(new Error("No shared_secret key in auth payload for encrypted channel: " + _this.name), null);
                      return;
                    }
                    _this.key = Object(base64["decode"])(sharedSecret);
                    delete authData["shared_secret"];
                    callback(null, authData);
                  });
                };
                EncryptedChannel.prototype.trigger = function(event, data) {
                  throw new UnsupportedFeature("Client events are not currently supported for encrypted channels");
                };
                EncryptedChannel.prototype.handleEvent = function(event) {
                  var eventName = event.event;
                  var data = event.data;
                  if (eventName.indexOf("pusher_internal:") === 0 || eventName.indexOf("pusher:") === 0) {
                    _super.prototype.handleEvent.call(this, event);
                    return;
                  }
                  this.handleEncryptedEvent(eventName, data);
                };
                EncryptedChannel.prototype.handleEncryptedEvent = function(event, data) {
                  var _this = this;
                  if (!this.key) {
                    logger.debug("Received encrypted event before key has been retrieved from the authEndpoint");
                    return;
                  }
                  if (!data.ciphertext || !data.nonce) {
                    logger.error("Unexpected format for encrypted event, expected object with `ciphertext` and `nonce` fields, got: " + data);
                    return;
                  }
                  var cipherText = Object(base64["decode"])(data.ciphertext);
                  if (cipherText.length < this.nacl.secretbox.overheadLength) {
                    logger.error("Expected encrypted event ciphertext length to be " + this.nacl.secretbox.overheadLength + ", got: " + cipherText.length);
                    return;
                  }
                  var nonce = Object(base64["decode"])(data.nonce);
                  if (nonce.length < this.nacl.secretbox.nonceLength) {
                    logger.error("Expected encrypted event nonce length to be " + this.nacl.secretbox.nonceLength + ", got: " + nonce.length);
                    return;
                  }
                  var bytes = this.nacl.secretbox.open(cipherText, nonce, this.key);
                  if (bytes === null) {
                    logger.debug("Failed to decrypt an event, probably because it was encrypted with a different key. Fetching a new key from the authEndpoint...");
                    this.authorize(this.pusher.connection.socket_id, function(error, authData) {
                      if (error) {
                        logger.error("Failed to make a request to the authEndpoint: " + authData + ". Unable to fetch new key, so dropping encrypted event");
                        return;
                      }
                      bytes = _this.nacl.secretbox.open(cipherText, nonce, _this.key);
                      if (bytes === null) {
                        logger.error("Failed to decrypt event with new key. Dropping encrypted event");
                        return;
                      }
                      _this.emit(event, _this.getDataToEmit(bytes));
                      return;
                    });
                    return;
                  }
                  this.emit(event, this.getDataToEmit(bytes));
                };
                EncryptedChannel.prototype.getDataToEmit = function(bytes) {
                  var raw = Object(utf8["decode"])(bytes);
                  try {
                    return JSON.parse(raw);
                  } catch (_a) {
                    return raw;
                  }
                };
                return EncryptedChannel;
              }(private_channel);
              var encrypted_channel = encrypted_channel_EncryptedChannel;
              var connection_manager_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var connection_manager_ConnectionManager = function(_super) {
                connection_manager_extends(ConnectionManager, _super);
                function ConnectionManager(key, options) {
                  var _this = _super.call(this) || this;
                  _this.state = "initialized";
                  _this.connection = null;
                  _this.key = key;
                  _this.options = options;
                  _this.timeline = _this.options.timeline;
                  _this.usingTLS = _this.options.useTLS;
                  _this.errorCallbacks = _this.buildErrorCallbacks();
                  _this.connectionCallbacks = _this.buildConnectionCallbacks(_this.errorCallbacks);
                  _this.handshakeCallbacks = _this.buildHandshakeCallbacks(_this.errorCallbacks);
                  var Network = runtime.getNetwork();
                  Network.bind("online", function() {
                    _this.timeline.info({ netinfo: "online" });
                    if (_this.state === "connecting" || _this.state === "unavailable") {
                      _this.retryIn(0);
                    }
                  });
                  Network.bind("offline", function() {
                    _this.timeline.info({ netinfo: "offline" });
                    if (_this.connection) {
                      _this.sendActivityCheck();
                    }
                  });
                  _this.updateStrategy();
                  return _this;
                }
                ConnectionManager.prototype.connect = function() {
                  if (this.connection || this.runner) {
                    return;
                  }
                  if (!this.strategy.isSupported()) {
                    this.updateState("failed");
                    return;
                  }
                  this.updateState("connecting");
                  this.startConnecting();
                  this.setUnavailableTimer();
                };
                ConnectionManager.prototype.send = function(data) {
                  if (this.connection) {
                    return this.connection.send(data);
                  } else {
                    return false;
                  }
                };
                ConnectionManager.prototype.send_event = function(name, data, channel) {
                  if (this.connection) {
                    return this.connection.send_event(name, data, channel);
                  } else {
                    return false;
                  }
                };
                ConnectionManager.prototype.disconnect = function() {
                  this.disconnectInternally();
                  this.updateState("disconnected");
                };
                ConnectionManager.prototype.isUsingTLS = function() {
                  return this.usingTLS;
                };
                ConnectionManager.prototype.startConnecting = function() {
                  var _this = this;
                  var callback = function(error, handshake) {
                    if (error) {
                      _this.runner = _this.strategy.connect(0, callback);
                    } else {
                      if (handshake.action === "error") {
                        _this.emit("error", {
                          type: "HandshakeError",
                          error: handshake.error
                        });
                        _this.timeline.error({ handshakeError: handshake.error });
                      } else {
                        _this.abortConnecting();
                        _this.handshakeCallbacks[handshake.action](handshake);
                      }
                    }
                  };
                  this.runner = this.strategy.connect(0, callback);
                };
                ConnectionManager.prototype.abortConnecting = function() {
                  if (this.runner) {
                    this.runner.abort();
                    this.runner = null;
                  }
                };
                ConnectionManager.prototype.disconnectInternally = function() {
                  this.abortConnecting();
                  this.clearRetryTimer();
                  this.clearUnavailableTimer();
                  if (this.connection) {
                    var connection = this.abandonConnection();
                    connection.close();
                  }
                };
                ConnectionManager.prototype.updateStrategy = function() {
                  this.strategy = this.options.getStrategy({
                    key: this.key,
                    timeline: this.timeline,
                    useTLS: this.usingTLS
                  });
                };
                ConnectionManager.prototype.retryIn = function(delay) {
                  var _this = this;
                  this.timeline.info({ action: "retry", delay });
                  if (delay > 0) {
                    this.emit("connecting_in", Math.round(delay / 1e3));
                  }
                  this.retryTimer = new OneOffTimer(delay || 0, function() {
                    _this.disconnectInternally();
                    _this.connect();
                  });
                };
                ConnectionManager.prototype.clearRetryTimer = function() {
                  if (this.retryTimer) {
                    this.retryTimer.ensureAborted();
                    this.retryTimer = null;
                  }
                };
                ConnectionManager.prototype.setUnavailableTimer = function() {
                  var _this = this;
                  this.unavailableTimer = new OneOffTimer(this.options.unavailableTimeout, function() {
                    _this.updateState("unavailable");
                  });
                };
                ConnectionManager.prototype.clearUnavailableTimer = function() {
                  if (this.unavailableTimer) {
                    this.unavailableTimer.ensureAborted();
                  }
                };
                ConnectionManager.prototype.sendActivityCheck = function() {
                  var _this = this;
                  this.stopActivityCheck();
                  this.connection.ping();
                  this.activityTimer = new OneOffTimer(this.options.pongTimeout, function() {
                    _this.timeline.error({ pong_timed_out: _this.options.pongTimeout });
                    _this.retryIn(0);
                  });
                };
                ConnectionManager.prototype.resetActivityCheck = function() {
                  var _this = this;
                  this.stopActivityCheck();
                  if (this.connection && !this.connection.handlesActivityChecks()) {
                    this.activityTimer = new OneOffTimer(this.activityTimeout, function() {
                      _this.sendActivityCheck();
                    });
                  }
                };
                ConnectionManager.prototype.stopActivityCheck = function() {
                  if (this.activityTimer) {
                    this.activityTimer.ensureAborted();
                  }
                };
                ConnectionManager.prototype.buildConnectionCallbacks = function(errorCallbacks) {
                  var _this = this;
                  return extend({}, errorCallbacks, {
                    message: function(message) {
                      _this.resetActivityCheck();
                      _this.emit("message", message);
                    },
                    ping: function() {
                      _this.send_event("pusher:pong", {});
                    },
                    activity: function() {
                      _this.resetActivityCheck();
                    },
                    error: function(error) {
                      _this.emit("error", error);
                    },
                    closed: function() {
                      _this.abandonConnection();
                      if (_this.shouldRetry()) {
                        _this.retryIn(1e3);
                      }
                    }
                  });
                };
                ConnectionManager.prototype.buildHandshakeCallbacks = function(errorCallbacks) {
                  var _this = this;
                  return extend({}, errorCallbacks, {
                    connected: function(handshake) {
                      _this.activityTimeout = Math.min(_this.options.activityTimeout, handshake.activityTimeout, handshake.connection.activityTimeout || Infinity);
                      _this.clearUnavailableTimer();
                      _this.setConnection(handshake.connection);
                      _this.socket_id = _this.connection.id;
                      _this.updateState("connected", { socket_id: _this.socket_id });
                    }
                  });
                };
                ConnectionManager.prototype.buildErrorCallbacks = function() {
                  var _this = this;
                  var withErrorEmitted = function(callback) {
                    return function(result) {
                      if (result.error) {
                        _this.emit("error", { type: "WebSocketError", error: result.error });
                      }
                      callback(result);
                    };
                  };
                  return {
                    tls_only: withErrorEmitted(function() {
                      _this.usingTLS = true;
                      _this.updateStrategy();
                      _this.retryIn(0);
                    }),
                    refused: withErrorEmitted(function() {
                      _this.disconnect();
                    }),
                    backoff: withErrorEmitted(function() {
                      _this.retryIn(1e3);
                    }),
                    retry: withErrorEmitted(function() {
                      _this.retryIn(0);
                    })
                  };
                };
                ConnectionManager.prototype.setConnection = function(connection) {
                  this.connection = connection;
                  for (var event in this.connectionCallbacks) {
                    this.connection.bind(event, this.connectionCallbacks[event]);
                  }
                  this.resetActivityCheck();
                };
                ConnectionManager.prototype.abandonConnection = function() {
                  if (!this.connection) {
                    return;
                  }
                  this.stopActivityCheck();
                  for (var event in this.connectionCallbacks) {
                    this.connection.unbind(event, this.connectionCallbacks[event]);
                  }
                  var connection = this.connection;
                  this.connection = null;
                  return connection;
                };
                ConnectionManager.prototype.updateState = function(newState, data) {
                  var previousState = this.state;
                  this.state = newState;
                  if (previousState !== newState) {
                    var newStateDescription = newState;
                    if (newStateDescription === "connected") {
                      newStateDescription += " with new socket ID " + data.socket_id;
                    }
                    logger.debug("State changed", previousState + " -> " + newStateDescription);
                    this.timeline.info({ state: newState, params: data });
                    this.emit("state_change", { previous: previousState, current: newState });
                    this.emit(newState, data);
                  }
                };
                ConnectionManager.prototype.shouldRetry = function() {
                  return this.state === "connecting" || this.state === "connected";
                };
                return ConnectionManager;
              }(dispatcher);
              var connection_manager = connection_manager_ConnectionManager;
              var channels_Channels = function() {
                function Channels() {
                  this.channels = {};
                }
                Channels.prototype.add = function(name, pusher) {
                  if (!this.channels[name]) {
                    this.channels[name] = createChannel(name, pusher);
                  }
                  return this.channels[name];
                };
                Channels.prototype.all = function() {
                  return values(this.channels);
                };
                Channels.prototype.find = function(name) {
                  return this.channels[name];
                };
                Channels.prototype.remove = function(name) {
                  var channel = this.channels[name];
                  delete this.channels[name];
                  return channel;
                };
                Channels.prototype.disconnect = function() {
                  objectApply(this.channels, function(channel) {
                    channel.disconnect();
                  });
                };
                return Channels;
              }();
              var channels = channels_Channels;
              function createChannel(name, pusher) {
                if (name.indexOf("private-encrypted-") === 0) {
                  if (pusher.config.nacl) {
                    return factory.createEncryptedChannel(name, pusher, pusher.config.nacl);
                  }
                  var errMsg = "Tried to subscribe to a private-encrypted- channel but no nacl implementation available";
                  var suffix = url_store.buildLogSuffix("encryptedChannelSupport");
                  throw new UnsupportedFeature(errMsg + ". " + suffix);
                } else if (name.indexOf("private-") === 0) {
                  return factory.createPrivateChannel(name, pusher);
                } else if (name.indexOf("presence-") === 0) {
                  return factory.createPresenceChannel(name, pusher);
                } else if (name.indexOf("#") === 0) {
                  throw new BadChannelName('Cannot create a channel with name "' + name + '".');
                } else {
                  return factory.createChannel(name, pusher);
                }
              }
              var Factory = {
                createChannels: function() {
                  return new channels();
                },
                createConnectionManager: function(key, options) {
                  return new connection_manager(key, options);
                },
                createChannel: function(name, pusher) {
                  return new channels_channel(name, pusher);
                },
                createPrivateChannel: function(name, pusher) {
                  return new private_channel(name, pusher);
                },
                createPresenceChannel: function(name, pusher) {
                  return new presence_channel(name, pusher);
                },
                createEncryptedChannel: function(name, pusher, nacl) {
                  return new encrypted_channel(name, pusher, nacl);
                },
                createTimelineSender: function(timeline, options) {
                  return new timeline_sender(timeline, options);
                },
                createHandshake: function(transport, callback) {
                  return new connection_handshake(transport, callback);
                },
                createAssistantToTheTransportManager: function(manager, transport, options) {
                  return new assistant_to_the_transport_manager(manager, transport, options);
                }
              };
              var factory = Factory;
              var transport_manager_TransportManager = function() {
                function TransportManager(options) {
                  this.options = options || {};
                  this.livesLeft = this.options.lives || Infinity;
                }
                TransportManager.prototype.getAssistant = function(transport) {
                  return factory.createAssistantToTheTransportManager(this, transport, {
                    minPingDelay: this.options.minPingDelay,
                    maxPingDelay: this.options.maxPingDelay
                  });
                };
                TransportManager.prototype.isAlive = function() {
                  return this.livesLeft > 0;
                };
                TransportManager.prototype.reportDeath = function() {
                  this.livesLeft -= 1;
                };
                return TransportManager;
              }();
              var transport_manager = transport_manager_TransportManager;
              var sequential_strategy_SequentialStrategy = function() {
                function SequentialStrategy(strategies, options) {
                  this.strategies = strategies;
                  this.loop = Boolean(options.loop);
                  this.failFast = Boolean(options.failFast);
                  this.timeout = options.timeout;
                  this.timeoutLimit = options.timeoutLimit;
                }
                SequentialStrategy.prototype.isSupported = function() {
                  return any(this.strategies, util.method("isSupported"));
                };
                SequentialStrategy.prototype.connect = function(minPriority, callback) {
                  var _this = this;
                  var strategies = this.strategies;
                  var current = 0;
                  var timeout = this.timeout;
                  var runner = null;
                  var tryNextStrategy = function(error, handshake) {
                    if (handshake) {
                      callback(null, handshake);
                    } else {
                      current = current + 1;
                      if (_this.loop) {
                        current = current % strategies.length;
                      }
                      if (current < strategies.length) {
                        if (timeout) {
                          timeout = timeout * 2;
                          if (_this.timeoutLimit) {
                            timeout = Math.min(timeout, _this.timeoutLimit);
                          }
                        }
                        runner = _this.tryStrategy(strategies[current], minPriority, { timeout, failFast: _this.failFast }, tryNextStrategy);
                      } else {
                        callback(true);
                      }
                    }
                  };
                  runner = this.tryStrategy(strategies[current], minPriority, { timeout, failFast: this.failFast }, tryNextStrategy);
                  return {
                    abort: function() {
                      runner.abort();
                    },
                    forceMinPriority: function(p) {
                      minPriority = p;
                      if (runner) {
                        runner.forceMinPriority(p);
                      }
                    }
                  };
                };
                SequentialStrategy.prototype.tryStrategy = function(strategy, minPriority, options, callback) {
                  var timer = null;
                  var runner = null;
                  if (options.timeout > 0) {
                    timer = new OneOffTimer(options.timeout, function() {
                      runner.abort();
                      callback(true);
                    });
                  }
                  runner = strategy.connect(minPriority, function(error, handshake) {
                    if (error && timer && timer.isRunning() && !options.failFast) {
                      return;
                    }
                    if (timer) {
                      timer.ensureAborted();
                    }
                    callback(error, handshake);
                  });
                  return {
                    abort: function() {
                      if (timer) {
                        timer.ensureAborted();
                      }
                      runner.abort();
                    },
                    forceMinPriority: function(p) {
                      runner.forceMinPriority(p);
                    }
                  };
                };
                return SequentialStrategy;
              }();
              var sequential_strategy = sequential_strategy_SequentialStrategy;
              var best_connected_ever_strategy_BestConnectedEverStrategy = function() {
                function BestConnectedEverStrategy(strategies) {
                  this.strategies = strategies;
                }
                BestConnectedEverStrategy.prototype.isSupported = function() {
                  return any(this.strategies, util.method("isSupported"));
                };
                BestConnectedEverStrategy.prototype.connect = function(minPriority, callback) {
                  return connect(this.strategies, minPriority, function(i, runners) {
                    return function(error, handshake) {
                      runners[i].error = error;
                      if (error) {
                        if (allRunnersFailed(runners)) {
                          callback(true);
                        }
                        return;
                      }
                      apply(runners, function(runner) {
                        runner.forceMinPriority(handshake.transport.priority);
                      });
                      callback(null, handshake);
                    };
                  });
                };
                return BestConnectedEverStrategy;
              }();
              var best_connected_ever_strategy = best_connected_ever_strategy_BestConnectedEverStrategy;
              function connect(strategies, minPriority, callbackBuilder) {
                var runners = map(strategies, function(strategy, i, _, rs) {
                  return strategy.connect(minPriority, callbackBuilder(i, rs));
                });
                return {
                  abort: function() {
                    apply(runners, abortRunner);
                  },
                  forceMinPriority: function(p) {
                    apply(runners, function(runner) {
                      runner.forceMinPriority(p);
                    });
                  }
                };
              }
              function allRunnersFailed(runners) {
                return collections_all(runners, function(runner) {
                  return Boolean(runner.error);
                });
              }
              function abortRunner(runner) {
                if (!runner.error && !runner.aborted) {
                  runner.abort();
                  runner.aborted = true;
                }
              }
              var cached_strategy_CachedStrategy = function() {
                function CachedStrategy(strategy, transports2, options) {
                  this.strategy = strategy;
                  this.transports = transports2;
                  this.ttl = options.ttl || 1800 * 1e3;
                  this.usingTLS = options.useTLS;
                  this.timeline = options.timeline;
                }
                CachedStrategy.prototype.isSupported = function() {
                  return this.strategy.isSupported();
                };
                CachedStrategy.prototype.connect = function(minPriority, callback) {
                  var usingTLS = this.usingTLS;
                  var info = fetchTransportCache(usingTLS);
                  var strategies = [this.strategy];
                  if (info && info.timestamp + this.ttl >= util.now()) {
                    var transport = this.transports[info.transport];
                    if (transport) {
                      this.timeline.info({
                        cached: true,
                        transport: info.transport,
                        latency: info.latency
                      });
                      strategies.push(new sequential_strategy([transport], {
                        timeout: info.latency * 2 + 1e3,
                        failFast: true
                      }));
                    }
                  }
                  var startTimestamp = util.now();
                  var runner = strategies.pop().connect(minPriority, function cb(error, handshake) {
                    if (error) {
                      flushTransportCache(usingTLS);
                      if (strategies.length > 0) {
                        startTimestamp = util.now();
                        runner = strategies.pop().connect(minPriority, cb);
                      } else {
                        callback(error);
                      }
                    } else {
                      storeTransportCache(usingTLS, handshake.transport.name, util.now() - startTimestamp);
                      callback(null, handshake);
                    }
                  });
                  return {
                    abort: function() {
                      runner.abort();
                    },
                    forceMinPriority: function(p) {
                      minPriority = p;
                      if (runner) {
                        runner.forceMinPriority(p);
                      }
                    }
                  };
                };
                return CachedStrategy;
              }();
              var cached_strategy = cached_strategy_CachedStrategy;
              function getTransportCacheKey(usingTLS) {
                return "pusherTransport" + (usingTLS ? "TLS" : "NonTLS");
              }
              function fetchTransportCache(usingTLS) {
                var storage = runtime.getLocalStorage();
                if (storage) {
                  try {
                    var serializedCache = storage[getTransportCacheKey(usingTLS)];
                    if (serializedCache) {
                      return JSON.parse(serializedCache);
                    }
                  } catch (e) {
                    flushTransportCache(usingTLS);
                  }
                }
                return null;
              }
              function storeTransportCache(usingTLS, transport, latency) {
                var storage = runtime.getLocalStorage();
                if (storage) {
                  try {
                    storage[getTransportCacheKey(usingTLS)] = safeJSONStringify({
                      timestamp: util.now(),
                      transport,
                      latency
                    });
                  } catch (e) {
                  }
                }
              }
              function flushTransportCache(usingTLS) {
                var storage = runtime.getLocalStorage();
                if (storage) {
                  try {
                    delete storage[getTransportCacheKey(usingTLS)];
                  } catch (e) {
                  }
                }
              }
              var delayed_strategy_DelayedStrategy = function() {
                function DelayedStrategy(strategy, _a) {
                  var number = _a.delay;
                  this.strategy = strategy;
                  this.options = { delay: number };
                }
                DelayedStrategy.prototype.isSupported = function() {
                  return this.strategy.isSupported();
                };
                DelayedStrategy.prototype.connect = function(minPriority, callback) {
                  var strategy = this.strategy;
                  var runner;
                  var timer = new OneOffTimer(this.options.delay, function() {
                    runner = strategy.connect(minPriority, callback);
                  });
                  return {
                    abort: function() {
                      timer.ensureAborted();
                      if (runner) {
                        runner.abort();
                      }
                    },
                    forceMinPriority: function(p) {
                      minPriority = p;
                      if (runner) {
                        runner.forceMinPriority(p);
                      }
                    }
                  };
                };
                return DelayedStrategy;
              }();
              var delayed_strategy = delayed_strategy_DelayedStrategy;
              var IfStrategy = function() {
                function IfStrategy2(test, trueBranch, falseBranch) {
                  this.test = test;
                  this.trueBranch = trueBranch;
                  this.falseBranch = falseBranch;
                }
                IfStrategy2.prototype.isSupported = function() {
                  var branch = this.test() ? this.trueBranch : this.falseBranch;
                  return branch.isSupported();
                };
                IfStrategy2.prototype.connect = function(minPriority, callback) {
                  var branch = this.test() ? this.trueBranch : this.falseBranch;
                  return branch.connect(minPriority, callback);
                };
                return IfStrategy2;
              }();
              var if_strategy = IfStrategy;
              var FirstConnectedStrategy = function() {
                function FirstConnectedStrategy2(strategy) {
                  this.strategy = strategy;
                }
                FirstConnectedStrategy2.prototype.isSupported = function() {
                  return this.strategy.isSupported();
                };
                FirstConnectedStrategy2.prototype.connect = function(minPriority, callback) {
                  var runner = this.strategy.connect(minPriority, function(error, handshake) {
                    if (handshake) {
                      runner.abort();
                    }
                    callback(error, handshake);
                  });
                  return runner;
                };
                return FirstConnectedStrategy2;
              }();
              var first_connected_strategy = FirstConnectedStrategy;
              function testSupportsStrategy(strategy) {
                return function() {
                  return strategy.isSupported();
                };
              }
              var getDefaultStrategy = function(config, baseOptions, defineTransport) {
                var definedTransports = {};
                function defineTransportStrategy(name, type, priority, options, manager) {
                  var transport = defineTransport(config, name, type, priority, options, manager);
                  definedTransports[name] = transport;
                  return transport;
                }
                var ws_options = Object.assign({}, baseOptions, {
                  hostNonTLS: config.wsHost + ":" + config.wsPort,
                  hostTLS: config.wsHost + ":" + config.wssPort,
                  httpPath: config.wsPath
                });
                var wss_options = Object.assign({}, ws_options, {
                  useTLS: true
                });
                var sockjs_options = Object.assign({}, baseOptions, {
                  hostNonTLS: config.httpHost + ":" + config.httpPort,
                  hostTLS: config.httpHost + ":" + config.httpsPort,
                  httpPath: config.httpPath
                });
                var timeouts = {
                  loop: true,
                  timeout: 15e3,
                  timeoutLimit: 6e4
                };
                var ws_manager = new transport_manager({
                  lives: 2,
                  minPingDelay: 1e4,
                  maxPingDelay: config.activityTimeout
                });
                var streaming_manager = new transport_manager({
                  lives: 2,
                  minPingDelay: 1e4,
                  maxPingDelay: config.activityTimeout
                });
                var ws_transport = defineTransportStrategy("ws", "ws", 3, ws_options, ws_manager);
                var wss_transport = defineTransportStrategy("wss", "ws", 3, wss_options, ws_manager);
                var sockjs_transport = defineTransportStrategy("sockjs", "sockjs", 1, sockjs_options);
                var xhr_streaming_transport = defineTransportStrategy("xhr_streaming", "xhr_streaming", 1, sockjs_options, streaming_manager);
                var xdr_streaming_transport = defineTransportStrategy("xdr_streaming", "xdr_streaming", 1, sockjs_options, streaming_manager);
                var xhr_polling_transport = defineTransportStrategy("xhr_polling", "xhr_polling", 1, sockjs_options);
                var xdr_polling_transport = defineTransportStrategy("xdr_polling", "xdr_polling", 1, sockjs_options);
                var ws_loop = new sequential_strategy([ws_transport], timeouts);
                var wss_loop = new sequential_strategy([wss_transport], timeouts);
                var sockjs_loop = new sequential_strategy([sockjs_transport], timeouts);
                var streaming_loop = new sequential_strategy([
                  new if_strategy(testSupportsStrategy(xhr_streaming_transport), xhr_streaming_transport, xdr_streaming_transport)
                ], timeouts);
                var polling_loop = new sequential_strategy([
                  new if_strategy(testSupportsStrategy(xhr_polling_transport), xhr_polling_transport, xdr_polling_transport)
                ], timeouts);
                var http_loop = new sequential_strategy([
                  new if_strategy(testSupportsStrategy(streaming_loop), new best_connected_ever_strategy([
                    streaming_loop,
                    new delayed_strategy(polling_loop, { delay: 4e3 })
                  ]), polling_loop)
                ], timeouts);
                var http_fallback_loop = new if_strategy(testSupportsStrategy(http_loop), http_loop, sockjs_loop);
                var wsStrategy;
                if (baseOptions.useTLS) {
                  wsStrategy = new best_connected_ever_strategy([
                    ws_loop,
                    new delayed_strategy(http_fallback_loop, { delay: 2e3 })
                  ]);
                } else {
                  wsStrategy = new best_connected_ever_strategy([
                    ws_loop,
                    new delayed_strategy(wss_loop, { delay: 2e3 }),
                    new delayed_strategy(http_fallback_loop, { delay: 5e3 })
                  ]);
                }
                return new cached_strategy(new first_connected_strategy(new if_strategy(testSupportsStrategy(ws_transport), wsStrategy, http_fallback_loop)), definedTransports, {
                  ttl: 18e5,
                  timeline: baseOptions.timeline,
                  useTLS: baseOptions.useTLS
                });
              };
              var default_strategy = getDefaultStrategy;
              var transport_connection_initializer = function() {
                var self = this;
                self.timeline.info(self.buildTimelineMessage({
                  transport: self.name + (self.options.useTLS ? "s" : "")
                }));
                if (self.hooks.isInitialized()) {
                  self.changeState("initialized");
                } else if (self.hooks.file) {
                  self.changeState("initializing");
                  Dependencies.load(self.hooks.file, { useTLS: self.options.useTLS }, function(error, callback) {
                    if (self.hooks.isInitialized()) {
                      self.changeState("initialized");
                      callback(true);
                    } else {
                      if (error) {
                        self.onError(error);
                      }
                      self.onClose();
                      callback(false);
                    }
                  });
                } else {
                  self.onClose();
                }
              };
              var http_xdomain_request_hooks = {
                getRequest: function(socket) {
                  var xdr = new window.XDomainRequest();
                  xdr.ontimeout = function() {
                    socket.emit("error", new RequestTimedOut());
                    socket.close();
                  };
                  xdr.onerror = function(e) {
                    socket.emit("error", e);
                    socket.close();
                  };
                  xdr.onprogress = function() {
                    if (xdr.responseText && xdr.responseText.length > 0) {
                      socket.onChunk(200, xdr.responseText);
                    }
                  };
                  xdr.onload = function() {
                    if (xdr.responseText && xdr.responseText.length > 0) {
                      socket.onChunk(200, xdr.responseText);
                    }
                    socket.emit("finished", 200);
                    socket.close();
                  };
                  return xdr;
                },
                abortRequest: function(xdr) {
                  xdr.ontimeout = xdr.onerror = xdr.onprogress = xdr.onload = null;
                  xdr.abort();
                }
              };
              var http_xdomain_request = http_xdomain_request_hooks;
              var http_request_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var MAX_BUFFER_LENGTH = 256 * 1024;
              var http_request_HTTPRequest = function(_super) {
                http_request_extends(HTTPRequest, _super);
                function HTTPRequest(hooks, method, url) {
                  var _this = _super.call(this) || this;
                  _this.hooks = hooks;
                  _this.method = method;
                  _this.url = url;
                  return _this;
                }
                HTTPRequest.prototype.start = function(payload) {
                  var _this = this;
                  this.position = 0;
                  this.xhr = this.hooks.getRequest(this);
                  this.unloader = function() {
                    _this.close();
                  };
                  runtime.addUnloadListener(this.unloader);
                  this.xhr.open(this.method, this.url, true);
                  if (this.xhr.setRequestHeader) {
                    this.xhr.setRequestHeader("Content-Type", "application/json");
                  }
                  this.xhr.send(payload);
                };
                HTTPRequest.prototype.close = function() {
                  if (this.unloader) {
                    runtime.removeUnloadListener(this.unloader);
                    this.unloader = null;
                  }
                  if (this.xhr) {
                    this.hooks.abortRequest(this.xhr);
                    this.xhr = null;
                  }
                };
                HTTPRequest.prototype.onChunk = function(status, data) {
                  while (true) {
                    var chunk = this.advanceBuffer(data);
                    if (chunk) {
                      this.emit("chunk", { status, data: chunk });
                    } else {
                      break;
                    }
                  }
                  if (this.isBufferTooLong(data)) {
                    this.emit("buffer_too_long");
                  }
                };
                HTTPRequest.prototype.advanceBuffer = function(buffer) {
                  var unreadData = buffer.slice(this.position);
                  var endOfLinePosition = unreadData.indexOf("\n");
                  if (endOfLinePosition !== -1) {
                    this.position += endOfLinePosition + 1;
                    return unreadData.slice(0, endOfLinePosition);
                  } else {
                    return null;
                  }
                };
                HTTPRequest.prototype.isBufferTooLong = function(buffer) {
                  return this.position === buffer.length && buffer.length > MAX_BUFFER_LENGTH;
                };
                return HTTPRequest;
              }(dispatcher);
              var http_request = http_request_HTTPRequest;
              var State;
              (function(State2) {
                State2[State2["CONNECTING"] = 0] = "CONNECTING";
                State2[State2["OPEN"] = 1] = "OPEN";
                State2[State2["CLOSED"] = 3] = "CLOSED";
              })(State || (State = {}));
              var state = State;
              var autoIncrement = 1;
              var http_socket_HTTPSocket = function() {
                function HTTPSocket(hooks, url) {
                  this.hooks = hooks;
                  this.session = randomNumber(1e3) + "/" + randomString(8);
                  this.location = getLocation(url);
                  this.readyState = state.CONNECTING;
                  this.openStream();
                }
                HTTPSocket.prototype.send = function(payload) {
                  return this.sendRaw(JSON.stringify([payload]));
                };
                HTTPSocket.prototype.ping = function() {
                  this.hooks.sendHeartbeat(this);
                };
                HTTPSocket.prototype.close = function(code, reason) {
                  this.onClose(code, reason, true);
                };
                HTTPSocket.prototype.sendRaw = function(payload) {
                  if (this.readyState === state.OPEN) {
                    try {
                      runtime.createSocketRequest("POST", getUniqueURL(getSendURL(this.location, this.session))).start(payload);
                      return true;
                    } catch (e) {
                      return false;
                    }
                  } else {
                    return false;
                  }
                };
                HTTPSocket.prototype.reconnect = function() {
                  this.closeStream();
                  this.openStream();
                };
                HTTPSocket.prototype.onClose = function(code, reason, wasClean) {
                  this.closeStream();
                  this.readyState = state.CLOSED;
                  if (this.onclose) {
                    this.onclose({
                      code,
                      reason,
                      wasClean
                    });
                  }
                };
                HTTPSocket.prototype.onChunk = function(chunk) {
                  if (chunk.status !== 200) {
                    return;
                  }
                  if (this.readyState === state.OPEN) {
                    this.onActivity();
                  }
                  var payload;
                  var type = chunk.data.slice(0, 1);
                  switch (type) {
                    case "o":
                      payload = JSON.parse(chunk.data.slice(1) || "{}");
                      this.onOpen(payload);
                      break;
                    case "a":
                      payload = JSON.parse(chunk.data.slice(1) || "[]");
                      for (var i = 0; i < payload.length; i++) {
                        this.onEvent(payload[i]);
                      }
                      break;
                    case "m":
                      payload = JSON.parse(chunk.data.slice(1) || "null");
                      this.onEvent(payload);
                      break;
                    case "h":
                      this.hooks.onHeartbeat(this);
                      break;
                    case "c":
                      payload = JSON.parse(chunk.data.slice(1) || "[]");
                      this.onClose(payload[0], payload[1], true);
                      break;
                  }
                };
                HTTPSocket.prototype.onOpen = function(options) {
                  if (this.readyState === state.CONNECTING) {
                    if (options && options.hostname) {
                      this.location.base = replaceHost(this.location.base, options.hostname);
                    }
                    this.readyState = state.OPEN;
                    if (this.onopen) {
                      this.onopen();
                    }
                  } else {
                    this.onClose(1006, "Server lost session", true);
                  }
                };
                HTTPSocket.prototype.onEvent = function(event) {
                  if (this.readyState === state.OPEN && this.onmessage) {
                    this.onmessage({ data: event });
                  }
                };
                HTTPSocket.prototype.onActivity = function() {
                  if (this.onactivity) {
                    this.onactivity();
                  }
                };
                HTTPSocket.prototype.onError = function(error) {
                  if (this.onerror) {
                    this.onerror(error);
                  }
                };
                HTTPSocket.prototype.openStream = function() {
                  var _this = this;
                  this.stream = runtime.createSocketRequest("POST", getUniqueURL(this.hooks.getReceiveURL(this.location, this.session)));
                  this.stream.bind("chunk", function(chunk) {
                    _this.onChunk(chunk);
                  });
                  this.stream.bind("finished", function(status) {
                    _this.hooks.onFinished(_this, status);
                  });
                  this.stream.bind("buffer_too_long", function() {
                    _this.reconnect();
                  });
                  try {
                    this.stream.start();
                  } catch (error) {
                    util.defer(function() {
                      _this.onError(error);
                      _this.onClose(1006, "Could not start streaming", false);
                    });
                  }
                };
                HTTPSocket.prototype.closeStream = function() {
                  if (this.stream) {
                    this.stream.unbind_all();
                    this.stream.close();
                    this.stream = null;
                  }
                };
                return HTTPSocket;
              }();
              function getLocation(url) {
                var parts = /([^\?]*)\/*(\??.*)/.exec(url);
                return {
                  base: parts[1],
                  queryString: parts[2]
                };
              }
              function getSendURL(url, session) {
                return url.base + "/" + session + "/xhr_send";
              }
              function getUniqueURL(url) {
                var separator = url.indexOf("?") === -1 ? "?" : "&";
                return url + separator + "t=" + +/* @__PURE__ */ new Date() + "&n=" + autoIncrement++;
              }
              function replaceHost(url, hostname) {
                var urlParts = /(https?:\/\/)([^\/:]+)((\/|:)?.*)/.exec(url);
                return urlParts[1] + hostname + urlParts[3];
              }
              function randomNumber(max) {
                return runtime.randomInt(max);
              }
              function randomString(length) {
                var result = [];
                for (var i = 0; i < length; i++) {
                  result.push(randomNumber(32).toString(32));
                }
                return result.join("");
              }
              var http_socket = http_socket_HTTPSocket;
              var http_streaming_socket_hooks = {
                getReceiveURL: function(url, session) {
                  return url.base + "/" + session + "/xhr_streaming" + url.queryString;
                },
                onHeartbeat: function(socket) {
                  socket.sendRaw("[]");
                },
                sendHeartbeat: function(socket) {
                  socket.sendRaw("[]");
                },
                onFinished: function(socket, status) {
                  socket.onClose(1006, "Connection interrupted (" + status + ")", false);
                }
              };
              var http_streaming_socket = http_streaming_socket_hooks;
              var http_polling_socket_hooks = {
                getReceiveURL: function(url, session) {
                  return url.base + "/" + session + "/xhr" + url.queryString;
                },
                onHeartbeat: function() {
                },
                sendHeartbeat: function(socket) {
                  socket.sendRaw("[]");
                },
                onFinished: function(socket, status) {
                  if (status === 200) {
                    socket.reconnect();
                  } else {
                    socket.onClose(1006, "Connection interrupted (" + status + ")", false);
                  }
                }
              };
              var http_polling_socket = http_polling_socket_hooks;
              var http_xhr_request_hooks = {
                getRequest: function(socket) {
                  var Constructor = runtime.getXHRAPI();
                  var xhr = new Constructor();
                  xhr.onreadystatechange = xhr.onprogress = function() {
                    switch (xhr.readyState) {
                      case 3:
                        if (xhr.responseText && xhr.responseText.length > 0) {
                          socket.onChunk(xhr.status, xhr.responseText);
                        }
                        break;
                      case 4:
                        if (xhr.responseText && xhr.responseText.length > 0) {
                          socket.onChunk(xhr.status, xhr.responseText);
                        }
                        socket.emit("finished", xhr.status);
                        socket.close();
                        break;
                    }
                  };
                  return xhr;
                },
                abortRequest: function(xhr) {
                  xhr.onreadystatechange = null;
                  xhr.abort();
                }
              };
              var http_xhr_request = http_xhr_request_hooks;
              var HTTP = {
                createStreamingSocket: function(url) {
                  return this.createSocket(http_streaming_socket, url);
                },
                createPollingSocket: function(url) {
                  return this.createSocket(http_polling_socket, url);
                },
                createSocket: function(hooks, url) {
                  return new http_socket(hooks, url);
                },
                createXHR: function(method, url) {
                  return this.createRequest(http_xhr_request, method, url);
                },
                createRequest: function(hooks, method, url) {
                  return new http_request(hooks, method, url);
                }
              };
              var http_http = HTTP;
              http_http.createXDR = function(method, url) {
                return this.createRequest(http_xdomain_request, method, url);
              };
              var web_http_http = http_http;
              var Runtime = {
                nextAuthCallbackID: 1,
                auth_callbacks: {},
                ScriptReceivers,
                DependenciesReceivers,
                getDefaultStrategy: default_strategy,
                Transports: transports_transports,
                transportConnectionInitializer: transport_connection_initializer,
                HTTPFactory: web_http_http,
                TimelineTransport: jsonp_timeline,
                getXHRAPI: function() {
                  return window.XMLHttpRequest;
                },
                getWebSocketAPI: function() {
                  return window.WebSocket || window.MozWebSocket;
                },
                setup: function(PusherClass) {
                  var _this = this;
                  window.Pusher = PusherClass;
                  var initializeOnDocumentBody = function() {
                    _this.onDocumentBody(PusherClass.ready);
                  };
                  if (!window.JSON) {
                    Dependencies.load("json2", {}, initializeOnDocumentBody);
                  } else {
                    initializeOnDocumentBody();
                  }
                },
                getDocument: function() {
                  return document;
                },
                getProtocol: function() {
                  return this.getDocument().location.protocol;
                },
                getAuthorizers: function() {
                  return { ajax: xhr_auth, jsonp: jsonp_auth };
                },
                onDocumentBody: function(callback) {
                  var _this = this;
                  if (document.body) {
                    callback();
                  } else {
                    setTimeout(function() {
                      _this.onDocumentBody(callback);
                    }, 0);
                  }
                },
                createJSONPRequest: function(url, data) {
                  return new jsonp_request(url, data);
                },
                createScriptRequest: function(src) {
                  return new script_request(src);
                },
                getLocalStorage: function() {
                  try {
                    return window.localStorage;
                  } catch (e) {
                    return void 0;
                  }
                },
                createXHR: function() {
                  if (this.getXHRAPI()) {
                    return this.createXMLHttpRequest();
                  } else {
                    return this.createMicrosoftXHR();
                  }
                },
                createXMLHttpRequest: function() {
                  var Constructor = this.getXHRAPI();
                  return new Constructor();
                },
                createMicrosoftXHR: function() {
                  return new ActiveXObject("Microsoft.XMLHTTP");
                },
                getNetwork: function() {
                  return net_info_Network;
                },
                createWebSocket: function(url) {
                  var Constructor = this.getWebSocketAPI();
                  return new Constructor(url);
                },
                createSocketRequest: function(method, url) {
                  if (this.isXHRSupported()) {
                    return this.HTTPFactory.createXHR(method, url);
                  } else if (this.isXDRSupported(url.indexOf("https:") === 0)) {
                    return this.HTTPFactory.createXDR(method, url);
                  } else {
                    throw "Cross-origin HTTP requests are not supported";
                  }
                },
                isXHRSupported: function() {
                  var Constructor = this.getXHRAPI();
                  return Boolean(Constructor) && new Constructor().withCredentials !== void 0;
                },
                isXDRSupported: function(useTLS) {
                  var protocol = useTLS ? "https:" : "http:";
                  var documentProtocol = this.getProtocol();
                  return Boolean(window["XDomainRequest"]) && documentProtocol === protocol;
                },
                addUnloadListener: function(listener) {
                  if (window.addEventListener !== void 0) {
                    window.addEventListener("unload", listener, false);
                  } else if (window.attachEvent !== void 0) {
                    window.attachEvent("onunload", listener);
                  }
                },
                removeUnloadListener: function(listener) {
                  if (window.addEventListener !== void 0) {
                    window.removeEventListener("unload", listener, false);
                  } else if (window.detachEvent !== void 0) {
                    window.detachEvent("onunload", listener);
                  }
                },
                randomInt: function(max) {
                  var random = function() {
                    var crypto = window.crypto || window["msCrypto"];
                    var random2 = crypto.getRandomValues(new Uint32Array(1))[0];
                    return random2 / Math.pow(2, 32);
                  };
                  return Math.floor(random() * max);
                }
              };
              var runtime = Runtime;
              var TimelineLevel;
              (function(TimelineLevel2) {
                TimelineLevel2[TimelineLevel2["ERROR"] = 3] = "ERROR";
                TimelineLevel2[TimelineLevel2["INFO"] = 6] = "INFO";
                TimelineLevel2[TimelineLevel2["DEBUG"] = 7] = "DEBUG";
              })(TimelineLevel || (TimelineLevel = {}));
              var timeline_level = TimelineLevel;
              var timeline_Timeline = function() {
                function Timeline(key, session, options) {
                  this.key = key;
                  this.session = session;
                  this.events = [];
                  this.options = options || {};
                  this.sent = 0;
                  this.uniqueID = 0;
                }
                Timeline.prototype.log = function(level, event) {
                  if (level <= this.options.level) {
                    this.events.push(extend({}, event, { timestamp: util.now() }));
                    if (this.options.limit && this.events.length > this.options.limit) {
                      this.events.shift();
                    }
                  }
                };
                Timeline.prototype.error = function(event) {
                  this.log(timeline_level.ERROR, event);
                };
                Timeline.prototype.info = function(event) {
                  this.log(timeline_level.INFO, event);
                };
                Timeline.prototype.debug = function(event) {
                  this.log(timeline_level.DEBUG, event);
                };
                Timeline.prototype.isEmpty = function() {
                  return this.events.length === 0;
                };
                Timeline.prototype.send = function(sendfn, callback) {
                  var _this = this;
                  var data = extend({
                    session: this.session,
                    bundle: this.sent + 1,
                    key: this.key,
                    lib: "js",
                    version: this.options.version,
                    cluster: this.options.cluster,
                    features: this.options.features,
                    timeline: this.events
                  }, this.options.params);
                  this.events = [];
                  sendfn(data, function(error, result) {
                    if (!error) {
                      _this.sent++;
                    }
                    if (callback) {
                      callback(error, result);
                    }
                  });
                  return true;
                };
                Timeline.prototype.generateUniqueID = function() {
                  this.uniqueID++;
                  return this.uniqueID;
                };
                return Timeline;
              }();
              var timeline_timeline = timeline_Timeline;
              var transport_strategy_TransportStrategy = function() {
                function TransportStrategy(name, priority, transport, options) {
                  this.name = name;
                  this.priority = priority;
                  this.transport = transport;
                  this.options = options || {};
                }
                TransportStrategy.prototype.isSupported = function() {
                  return this.transport.isSupported({
                    useTLS: this.options.useTLS
                  });
                };
                TransportStrategy.prototype.connect = function(minPriority, callback) {
                  var _this = this;
                  if (!this.isSupported()) {
                    return failAttempt(new UnsupportedStrategy(), callback);
                  } else if (this.priority < minPriority) {
                    return failAttempt(new TransportPriorityTooLow(), callback);
                  }
                  var connected = false;
                  var transport = this.transport.createConnection(this.name, this.priority, this.options.key, this.options);
                  var handshake = null;
                  var onInitialized = function() {
                    transport.unbind("initialized", onInitialized);
                    transport.connect();
                  };
                  var onOpen = function() {
                    handshake = factory.createHandshake(transport, function(result) {
                      connected = true;
                      unbindListeners();
                      callback(null, result);
                    });
                  };
                  var onError = function(error) {
                    unbindListeners();
                    callback(error);
                  };
                  var onClosed = function() {
                    unbindListeners();
                    var serializedTransport;
                    serializedTransport = safeJSONStringify(transport);
                    callback(new TransportClosed(serializedTransport));
                  };
                  var unbindListeners = function() {
                    transport.unbind("initialized", onInitialized);
                    transport.unbind("open", onOpen);
                    transport.unbind("error", onError);
                    transport.unbind("closed", onClosed);
                  };
                  transport.bind("initialized", onInitialized);
                  transport.bind("open", onOpen);
                  transport.bind("error", onError);
                  transport.bind("closed", onClosed);
                  transport.initialize();
                  return {
                    abort: function() {
                      if (connected) {
                        return;
                      }
                      unbindListeners();
                      if (handshake) {
                        handshake.close();
                      } else {
                        transport.close();
                      }
                    },
                    forceMinPriority: function(p) {
                      if (connected) {
                        return;
                      }
                      if (_this.priority < p) {
                        if (handshake) {
                          handshake.close();
                        } else {
                          transport.close();
                        }
                      }
                    }
                  };
                };
                return TransportStrategy;
              }();
              var transport_strategy = transport_strategy_TransportStrategy;
              function failAttempt(error, callback) {
                util.defer(function() {
                  callback(error);
                });
                return {
                  abort: function() {
                  },
                  forceMinPriority: function() {
                  }
                };
              }
              var strategy_builder_Transports = runtime.Transports;
              var strategy_builder_defineTransport = function(config, name, type, priority, options, manager) {
                var transportClass = strategy_builder_Transports[type];
                if (!transportClass) {
                  throw new UnsupportedTransport(type);
                }
                var enabled = (!config.enabledTransports || arrayIndexOf(config.enabledTransports, name) !== -1) && (!config.disabledTransports || arrayIndexOf(config.disabledTransports, name) === -1);
                var transport;
                if (enabled) {
                  options = Object.assign({ ignoreNullOrigin: config.ignoreNullOrigin }, options);
                  transport = new transport_strategy(name, priority, manager ? manager.getAssistant(transportClass) : transportClass, options);
                } else {
                  transport = strategy_builder_UnsupportedStrategy;
                }
                return transport;
              };
              var strategy_builder_UnsupportedStrategy = {
                isSupported: function() {
                  return false;
                },
                connect: function(_, callback) {
                  var deferred = util.defer(function() {
                    callback(new UnsupportedStrategy());
                  });
                  return {
                    abort: function() {
                      deferred.ensureAborted();
                    },
                    forceMinPriority: function() {
                    }
                  };
                }
              };
              var composeChannelQuery = function(params, authOptions) {
                var query = "socket_id=" + encodeURIComponent(params.socketId);
                for (var key in authOptions.params) {
                  query += "&" + encodeURIComponent(key) + "=" + encodeURIComponent(authOptions.params[key]);
                }
                if (authOptions.paramsProvider != null) {
                  var dynamicParams = authOptions.paramsProvider();
                  for (var key in dynamicParams) {
                    query += "&" + encodeURIComponent(key) + "=" + encodeURIComponent(dynamicParams[key]);
                  }
                }
                return query;
              };
              var UserAuthenticator = function(authOptions) {
                if (typeof runtime.getAuthorizers()[authOptions.transport] === "undefined") {
                  throw "'" + authOptions.transport + "' is not a recognized auth transport";
                }
                return function(params, callback) {
                  var query = composeChannelQuery(params, authOptions);
                  runtime.getAuthorizers()[authOptions.transport](runtime, query, authOptions, AuthRequestType.UserAuthentication, callback);
                };
              };
              var user_authenticator = UserAuthenticator;
              var channel_authorizer_composeChannelQuery = function(params, authOptions) {
                var query = "socket_id=" + encodeURIComponent(params.socketId);
                query += "&channel_name=" + encodeURIComponent(params.channelName);
                for (var key in authOptions.params) {
                  query += "&" + encodeURIComponent(key) + "=" + encodeURIComponent(authOptions.params[key]);
                }
                if (authOptions.paramsProvider != null) {
                  var dynamicParams = authOptions.paramsProvider();
                  for (var key in dynamicParams) {
                    query += "&" + encodeURIComponent(key) + "=" + encodeURIComponent(dynamicParams[key]);
                  }
                }
                return query;
              };
              var ChannelAuthorizer = function(authOptions) {
                if (typeof runtime.getAuthorizers()[authOptions.transport] === "undefined") {
                  throw "'" + authOptions.transport + "' is not a recognized auth transport";
                }
                return function(params, callback) {
                  var query = channel_authorizer_composeChannelQuery(params, authOptions);
                  runtime.getAuthorizers()[authOptions.transport](runtime, query, authOptions, AuthRequestType.ChannelAuthorization, callback);
                };
              };
              var channel_authorizer = ChannelAuthorizer;
              var ChannelAuthorizerProxy = function(pusher, authOptions, channelAuthorizerGenerator) {
                var deprecatedAuthorizerOptions = {
                  authTransport: authOptions.transport,
                  authEndpoint: authOptions.endpoint,
                  auth: {
                    params: authOptions.params,
                    headers: authOptions.headers
                  }
                };
                return function(params, callback) {
                  var channel = pusher.channel(params.channelName);
                  var channelAuthorizer = channelAuthorizerGenerator(channel, deprecatedAuthorizerOptions);
                  channelAuthorizer.authorize(params.socketId, callback);
                };
              };
              var __assign = function() {
                __assign = Object.assign || function(t) {
                  for (var s, i = 1, n = arguments.length; i < n; i++) {
                    s = arguments[i];
                    for (var p in s)
                      if (Object.prototype.hasOwnProperty.call(s, p))
                        t[p] = s[p];
                  }
                  return t;
                };
                return __assign.apply(this, arguments);
              };
              function getConfig(opts, pusher) {
                var config = {
                  activityTimeout: opts.activityTimeout || defaults.activityTimeout,
                  cluster: opts.cluster || defaults.cluster,
                  httpPath: opts.httpPath || defaults.httpPath,
                  httpPort: opts.httpPort || defaults.httpPort,
                  httpsPort: opts.httpsPort || defaults.httpsPort,
                  pongTimeout: opts.pongTimeout || defaults.pongTimeout,
                  statsHost: opts.statsHost || defaults.stats_host,
                  unavailableTimeout: opts.unavailableTimeout || defaults.unavailableTimeout,
                  wsPath: opts.wsPath || defaults.wsPath,
                  wsPort: opts.wsPort || defaults.wsPort,
                  wssPort: opts.wssPort || defaults.wssPort,
                  enableStats: getEnableStatsConfig(opts),
                  httpHost: getHttpHost(opts),
                  useTLS: shouldUseTLS(opts),
                  wsHost: getWebsocketHost(opts),
                  userAuthenticator: buildUserAuthenticator(opts),
                  channelAuthorizer: buildChannelAuthorizer(opts, pusher)
                };
                if ("disabledTransports" in opts)
                  config.disabledTransports = opts.disabledTransports;
                if ("enabledTransports" in opts)
                  config.enabledTransports = opts.enabledTransports;
                if ("ignoreNullOrigin" in opts)
                  config.ignoreNullOrigin = opts.ignoreNullOrigin;
                if ("timelineParams" in opts)
                  config.timelineParams = opts.timelineParams;
                if ("nacl" in opts) {
                  config.nacl = opts.nacl;
                }
                return config;
              }
              function getHttpHost(opts) {
                if (opts.httpHost) {
                  return opts.httpHost;
                }
                if (opts.cluster) {
                  return "sockjs-" + opts.cluster + ".pusher.com";
                }
                return defaults.httpHost;
              }
              function getWebsocketHost(opts) {
                if (opts.wsHost) {
                  return opts.wsHost;
                }
                if (opts.cluster) {
                  return getWebsocketHostFromCluster(opts.cluster);
                }
                return getWebsocketHostFromCluster(defaults.cluster);
              }
              function getWebsocketHostFromCluster(cluster) {
                return "ws-" + cluster + ".pusher.com";
              }
              function shouldUseTLS(opts) {
                if (runtime.getProtocol() === "https:") {
                  return true;
                } else if (opts.forceTLS === false) {
                  return false;
                }
                return true;
              }
              function getEnableStatsConfig(opts) {
                if ("enableStats" in opts) {
                  return opts.enableStats;
                }
                if ("disableStats" in opts) {
                  return !opts.disableStats;
                }
                return false;
              }
              function buildUserAuthenticator(opts) {
                var userAuthentication = __assign(__assign({}, defaults.userAuthentication), opts.userAuthentication);
                if ("customHandler" in userAuthentication && userAuthentication["customHandler"] != null) {
                  return userAuthentication["customHandler"];
                }
                return user_authenticator(userAuthentication);
              }
              function buildChannelAuth(opts, pusher) {
                var channelAuthorization;
                if ("channelAuthorization" in opts) {
                  channelAuthorization = __assign(__assign({}, defaults.channelAuthorization), opts.channelAuthorization);
                } else {
                  channelAuthorization = {
                    transport: opts.authTransport || defaults.authTransport,
                    endpoint: opts.authEndpoint || defaults.authEndpoint
                  };
                  if ("auth" in opts) {
                    if ("params" in opts.auth)
                      channelAuthorization.params = opts.auth.params;
                    if ("headers" in opts.auth)
                      channelAuthorization.headers = opts.auth.headers;
                  }
                  if ("authorizer" in opts)
                    channelAuthorization.customHandler = ChannelAuthorizerProxy(pusher, channelAuthorization, opts.authorizer);
                }
                return channelAuthorization;
              }
              function buildChannelAuthorizer(opts, pusher) {
                var channelAuthorization = buildChannelAuth(opts, pusher);
                if ("customHandler" in channelAuthorization && channelAuthorization["customHandler"] != null) {
                  return channelAuthorization["customHandler"];
                }
                return channel_authorizer(channelAuthorization);
              }
              var watchlist_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var watchlist_WatchlistFacade = function(_super) {
                watchlist_extends(WatchlistFacade, _super);
                function WatchlistFacade(pusher) {
                  var _this = _super.call(this, function(eventName, data) {
                    logger.debug("No callbacks on watchlist events for " + eventName);
                  }) || this;
                  _this.pusher = pusher;
                  _this.bindWatchlistInternalEvent();
                  return _this;
                }
                WatchlistFacade.prototype.handleEvent = function(pusherEvent) {
                  var _this = this;
                  pusherEvent.data.events.forEach(function(watchlistEvent) {
                    _this.emit(watchlistEvent.name, watchlistEvent);
                  });
                };
                WatchlistFacade.prototype.bindWatchlistInternalEvent = function() {
                  var _this = this;
                  this.pusher.connection.bind("message", function(pusherEvent) {
                    var eventName = pusherEvent.event;
                    if (eventName === "pusher_internal:watchlist_events") {
                      _this.handleEvent(pusherEvent);
                    }
                  });
                };
                return WatchlistFacade;
              }(dispatcher);
              var watchlist = watchlist_WatchlistFacade;
              function flatPromise() {
                var resolve, reject;
                var promise = new Promise(function(res, rej) {
                  resolve = res;
                  reject = rej;
                });
                return { promise, resolve, reject };
              }
              var flat_promise = flatPromise;
              var user_extends = function() {
                var extendStatics = function(d, b) {
                  extendStatics = Object.setPrototypeOf || { __proto__: [] } instanceof Array && function(d2, b2) {
                    d2.__proto__ = b2;
                  } || function(d2, b2) {
                    for (var p in b2)
                      if (b2.hasOwnProperty(p))
                        d2[p] = b2[p];
                  };
                  return extendStatics(d, b);
                };
                return function(d, b) {
                  extendStatics(d, b);
                  function __() {
                    this.constructor = d;
                  }
                  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
                };
              }();
              var user_UserFacade = function(_super) {
                user_extends(UserFacade, _super);
                function UserFacade(pusher) {
                  var _this = _super.call(this, function(eventName, data) {
                    logger.debug("No callbacks on user for " + eventName);
                  }) || this;
                  _this.signin_requested = false;
                  _this.user_data = null;
                  _this.serverToUserChannel = null;
                  _this.signinDonePromise = null;
                  _this._signinDoneResolve = null;
                  _this._onAuthorize = function(err, authData) {
                    if (err) {
                      logger.warn("Error during signin: " + err);
                      _this._cleanup();
                      return;
                    }
                    _this.pusher.send_event("pusher:signin", {
                      auth: authData.auth,
                      user_data: authData.user_data
                    });
                  };
                  _this.pusher = pusher;
                  _this.pusher.connection.bind("state_change", function(_a) {
                    var previous = _a.previous, current = _a.current;
                    if (previous !== "connected" && current === "connected") {
                      _this._signin();
                    }
                    if (previous === "connected" && current !== "connected") {
                      _this._cleanup();
                      _this._newSigninPromiseIfNeeded();
                    }
                  });
                  _this.watchlist = new watchlist(pusher);
                  _this.pusher.connection.bind("message", function(event) {
                    var eventName = event.event;
                    if (eventName === "pusher:signin_success") {
                      _this._onSigninSuccess(event.data);
                    }
                    if (_this.serverToUserChannel && _this.serverToUserChannel.name === event.channel) {
                      _this.serverToUserChannel.handleEvent(event);
                    }
                  });
                  return _this;
                }
                UserFacade.prototype.signin = function() {
                  if (this.signin_requested) {
                    return;
                  }
                  this.signin_requested = true;
                  this._signin();
                };
                UserFacade.prototype._signin = function() {
                  if (!this.signin_requested) {
                    return;
                  }
                  this._newSigninPromiseIfNeeded();
                  if (this.pusher.connection.state !== "connected") {
                    return;
                  }
                  this.pusher.config.userAuthenticator({
                    socketId: this.pusher.connection.socket_id
                  }, this._onAuthorize);
                };
                UserFacade.prototype._onSigninSuccess = function(data) {
                  try {
                    this.user_data = JSON.parse(data.user_data);
                  } catch (e) {
                    logger.error("Failed parsing user data after signin: " + data.user_data);
                    this._cleanup();
                    return;
                  }
                  if (typeof this.user_data.id !== "string" || this.user_data.id === "") {
                    logger.error("user_data doesn't contain an id. user_data: " + this.user_data);
                    this._cleanup();
                    return;
                  }
                  this._signinDoneResolve();
                  this._subscribeChannels();
                };
                UserFacade.prototype._subscribeChannels = function() {
                  var _this = this;
                  var ensure_subscribed = function(channel) {
                    if (channel.subscriptionPending && channel.subscriptionCancelled) {
                      channel.reinstateSubscription();
                    } else if (!channel.subscriptionPending && _this.pusher.connection.state === "connected") {
                      channel.subscribe();
                    }
                  };
                  this.serverToUserChannel = new channels_channel("#server-to-user-" + this.user_data.id, this.pusher);
                  this.serverToUserChannel.bind_global(function(eventName, data) {
                    if (eventName.indexOf("pusher_internal:") === 0 || eventName.indexOf("pusher:") === 0) {
                      return;
                    }
                    _this.emit(eventName, data);
                  });
                  ensure_subscribed(this.serverToUserChannel);
                };
                UserFacade.prototype._cleanup = function() {
                  this.user_data = null;
                  if (this.serverToUserChannel) {
                    this.serverToUserChannel.unbind_all();
                    this.serverToUserChannel.disconnect();
                    this.serverToUserChannel = null;
                  }
                  if (this.signin_requested) {
                    this._signinDoneResolve();
                  }
                };
                UserFacade.prototype._newSigninPromiseIfNeeded = function() {
                  if (!this.signin_requested) {
                    return;
                  }
                  if (this.signinDonePromise && !this.signinDonePromise.done) {
                    return;
                  }
                  var _a = flat_promise(), promise = _a.promise, resolve = _a.resolve, _ = _a.reject;
                  promise.done = false;
                  var setDone = function() {
                    promise.done = true;
                  };
                  promise.then(setDone)["catch"](setDone);
                  this.signinDonePromise = promise;
                  this._signinDoneResolve = resolve;
                };
                return UserFacade;
              }(dispatcher);
              var user = user_UserFacade;
              var pusher_Pusher = function() {
                function Pusher3(app_key, options) {
                  var _this = this;
                  checkAppKey(app_key);
                  options = options || {};
                  if (!options.cluster && !(options.wsHost || options.httpHost)) {
                    var suffix = url_store.buildLogSuffix("javascriptQuickStart");
                    logger.warn("You should always specify a cluster when connecting. " + suffix);
                  }
                  if ("disableStats" in options) {
                    logger.warn("The disableStats option is deprecated in favor of enableStats");
                  }
                  this.key = app_key;
                  this.config = getConfig(options, this);
                  this.channels = factory.createChannels();
                  this.global_emitter = new dispatcher();
                  this.sessionID = runtime.randomInt(1e9);
                  this.timeline = new timeline_timeline(this.key, this.sessionID, {
                    cluster: this.config.cluster,
                    features: Pusher3.getClientFeatures(),
                    params: this.config.timelineParams || {},
                    limit: 50,
                    level: timeline_level.INFO,
                    version: defaults.VERSION
                  });
                  if (this.config.enableStats) {
                    this.timelineSender = factory.createTimelineSender(this.timeline, {
                      host: this.config.statsHost,
                      path: "/timeline/v2/" + runtime.TimelineTransport.name
                    });
                  }
                  var getStrategy = function(options2) {
                    return runtime.getDefaultStrategy(_this.config, options2, strategy_builder_defineTransport);
                  };
                  this.connection = factory.createConnectionManager(this.key, {
                    getStrategy,
                    timeline: this.timeline,
                    activityTimeout: this.config.activityTimeout,
                    pongTimeout: this.config.pongTimeout,
                    unavailableTimeout: this.config.unavailableTimeout,
                    useTLS: Boolean(this.config.useTLS)
                  });
                  this.connection.bind("connected", function() {
                    _this.subscribeAll();
                    if (_this.timelineSender) {
                      _this.timelineSender.send(_this.connection.isUsingTLS());
                    }
                  });
                  this.connection.bind("message", function(event) {
                    var eventName = event.event;
                    var internal = eventName.indexOf("pusher_internal:") === 0;
                    if (event.channel) {
                      var channel = _this.channel(event.channel);
                      if (channel) {
                        channel.handleEvent(event);
                      }
                    }
                    if (!internal) {
                      _this.global_emitter.emit(event.event, event.data);
                    }
                  });
                  this.connection.bind("connecting", function() {
                    _this.channels.disconnect();
                  });
                  this.connection.bind("disconnected", function() {
                    _this.channels.disconnect();
                  });
                  this.connection.bind("error", function(err) {
                    logger.warn(err);
                  });
                  Pusher3.instances.push(this);
                  this.timeline.info({ instances: Pusher3.instances.length });
                  this.user = new user(this);
                  if (Pusher3.isReady) {
                    this.connect();
                  }
                }
                Pusher3.ready = function() {
                  Pusher3.isReady = true;
                  for (var i = 0, l2 = Pusher3.instances.length; i < l2; i++) {
                    Pusher3.instances[i].connect();
                  }
                };
                Pusher3.getClientFeatures = function() {
                  return keys(filterObject({ ws: runtime.Transports.ws }, function(t) {
                    return t.isSupported({});
                  }));
                };
                Pusher3.prototype.channel = function(name) {
                  return this.channels.find(name);
                };
                Pusher3.prototype.allChannels = function() {
                  return this.channels.all();
                };
                Pusher3.prototype.connect = function() {
                  this.connection.connect();
                  if (this.timelineSender) {
                    if (!this.timelineSenderTimer) {
                      var usingTLS = this.connection.isUsingTLS();
                      var timelineSender = this.timelineSender;
                      this.timelineSenderTimer = new PeriodicTimer(6e4, function() {
                        timelineSender.send(usingTLS);
                      });
                    }
                  }
                };
                Pusher3.prototype.disconnect = function() {
                  this.connection.disconnect();
                  if (this.timelineSenderTimer) {
                    this.timelineSenderTimer.ensureAborted();
                    this.timelineSenderTimer = null;
                  }
                };
                Pusher3.prototype.bind = function(event_name, callback, context) {
                  this.global_emitter.bind(event_name, callback, context);
                  return this;
                };
                Pusher3.prototype.unbind = function(event_name, callback, context) {
                  this.global_emitter.unbind(event_name, callback, context);
                  return this;
                };
                Pusher3.prototype.bind_global = function(callback) {
                  this.global_emitter.bind_global(callback);
                  return this;
                };
                Pusher3.prototype.unbind_global = function(callback) {
                  this.global_emitter.unbind_global(callback);
                  return this;
                };
                Pusher3.prototype.unbind_all = function(callback) {
                  this.global_emitter.unbind_all();
                  return this;
                };
                Pusher3.prototype.subscribeAll = function() {
                  var channelName;
                  for (channelName in this.channels.channels) {
                    if (this.channels.channels.hasOwnProperty(channelName)) {
                      this.subscribe(channelName);
                    }
                  }
                };
                Pusher3.prototype.subscribe = function(channel_name) {
                  var channel = this.channels.add(channel_name, this);
                  if (channel.subscriptionPending && channel.subscriptionCancelled) {
                    channel.reinstateSubscription();
                  } else if (!channel.subscriptionPending && this.connection.state === "connected") {
                    channel.subscribe();
                  }
                  return channel;
                };
                Pusher3.prototype.unsubscribe = function(channel_name) {
                  var channel = this.channels.find(channel_name);
                  if (channel && channel.subscriptionPending) {
                    channel.cancelSubscription();
                  } else {
                    channel = this.channels.remove(channel_name);
                    if (channel && channel.subscribed) {
                      channel.unsubscribe();
                    }
                  }
                };
                Pusher3.prototype.send_event = function(event_name, data, channel) {
                  return this.connection.send_event(event_name, data, channel);
                };
                Pusher3.prototype.shouldUseTLS = function() {
                  return this.config.useTLS;
                };
                Pusher3.prototype.signin = function() {
                  this.user.signin();
                };
                Pusher3.instances = [];
                Pusher3.isReady = false;
                Pusher3.logToConsole = false;
                Pusher3.Runtime = runtime;
                Pusher3.ScriptReceivers = runtime.ScriptReceivers;
                Pusher3.DependenciesReceivers = runtime.DependenciesReceivers;
                Pusher3.auth_callbacks = runtime.auth_callbacks;
                return Pusher3;
              }();
              var core_pusher = __webpack_exports__["default"] = pusher_Pusher;
              function checkAppKey(key) {
                if (key === null || key === void 0) {
                  throw "You must pass your app key when you instantiate Pusher.";
                }
              }
              runtime.setup(pusher_Pusher);
            }
            /******/
          ])
        );
      });
    }
  });

  // node_modules/laravel-echo/dist/echo.js
  function _typeof(obj) {
    "@babel/helpers - typeof";
    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj2) {
      return typeof obj2;
    } : function(obj2) {
      return obj2 && "function" == typeof Symbol && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
    }, _typeof(obj);
  }
  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }
  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor)
        descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }
  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps)
      _defineProperties(Constructor.prototype, protoProps);
    if (staticProps)
      _defineProperties(Constructor, staticProps);
    Object.defineProperty(Constructor, "prototype", {
      writable: false
    });
    return Constructor;
  }
  function _extends() {
    _extends = Object.assign || function(target) {
      for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i];
        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }
      return target;
    };
    return _extends.apply(this, arguments);
  }
  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function");
    }
    subClass.prototype = Object.create(superClass && superClass.prototype, {
      constructor: {
        value: subClass,
        writable: true,
        configurable: true
      }
    });
    Object.defineProperty(subClass, "prototype", {
      writable: false
    });
    if (superClass)
      _setPrototypeOf(subClass, superClass);
  }
  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf2(o2) {
      return o2.__proto__ || Object.getPrototypeOf(o2);
    };
    return _getPrototypeOf(o);
  }
  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf2(o2, p2) {
      o2.__proto__ = p2;
      return o2;
    };
    return _setPrototypeOf(o, p);
  }
  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct)
      return false;
    if (Reflect.construct.sham)
      return false;
    if (typeof Proxy === "function")
      return true;
    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function() {
      }));
      return true;
    } catch (e) {
      return false;
    }
  }
  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }
    return self;
  }
  function _possibleConstructorReturn(self, call) {
    if (call && (typeof call === "object" || typeof call === "function")) {
      return call;
    } else if (call !== void 0) {
      throw new TypeError("Derived constructors may only return object or undefined");
    }
    return _assertThisInitialized(self);
  }
  function _createSuper(Derived) {
    var hasNativeReflectConstruct = _isNativeReflectConstruct();
    return function _createSuperInternal() {
      var Super = _getPrototypeOf(Derived), result;
      if (hasNativeReflectConstruct) {
        var NewTarget = _getPrototypeOf(this).constructor;
        result = Reflect.construct(Super, arguments, NewTarget);
      } else {
        result = Super.apply(this, arguments);
      }
      return _possibleConstructorReturn(this, result);
    };
  }
  var Channel = /* @__PURE__ */ function() {
    function Channel2() {
      _classCallCheck(this, Channel2);
    }
    _createClass(Channel2, [{
      key: "listenForWhisper",
      value: (
        /**
         * Listen for a whisper event on the channel instance.
         */
        function listenForWhisper(event, callback) {
          return this.listen(".client-" + event, callback);
        }
      )
      /**
       * Listen for an event on the channel instance.
       */
    }, {
      key: "notification",
      value: function notification(callback) {
        return this.listen(".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated", callback);
      }
      /**
       * Stop listening for a whisper event on the channel instance.
       */
    }, {
      key: "stopListeningForWhisper",
      value: function stopListeningForWhisper(event, callback) {
        return this.stopListening(".client-" + event, callback);
      }
    }]);
    return Channel2;
  }();
  var EventFormatter = /* @__PURE__ */ function() {
    function EventFormatter2(namespace) {
      _classCallCheck(this, EventFormatter2);
      this.setNamespace(namespace);
    }
    _createClass(EventFormatter2, [{
      key: "format",
      value: function format(event) {
        if (event.charAt(0) === "." || event.charAt(0) === "\\") {
          return event.substr(1);
        } else if (this.namespace) {
          event = this.namespace + "." + event;
        }
        return event.replace(/\./g, "\\");
      }
      /**
       * Set the event namespace.
       */
    }, {
      key: "setNamespace",
      value: function setNamespace(value) {
        this.namespace = value;
      }
    }]);
    return EventFormatter2;
  }();
  var PusherChannel = /* @__PURE__ */ function(_Channel) {
    _inherits(PusherChannel2, _Channel);
    var _super = _createSuper(PusherChannel2);
    function PusherChannel2(pusher, name, options) {
      var _this;
      _classCallCheck(this, PusherChannel2);
      _this = _super.call(this);
      _this.name = name;
      _this.pusher = pusher;
      _this.options = options;
      _this.eventFormatter = new EventFormatter(_this.options.namespace);
      _this.subscribe();
      return _this;
    }
    _createClass(PusherChannel2, [{
      key: "subscribe",
      value: function subscribe() {
        this.subscription = this.pusher.subscribe(this.name);
      }
      /**
       * Unsubscribe from a Pusher channel.
       */
    }, {
      key: "unsubscribe",
      value: function unsubscribe() {
        this.pusher.unsubscribe(this.name);
      }
      /**
       * Listen for an event on the channel instance.
       */
    }, {
      key: "listen",
      value: function listen(event, callback) {
        this.on(this.eventFormatter.format(event), callback);
        return this;
      }
      /**
       * Listen for all events on the channel instance.
       */
    }, {
      key: "listenToAll",
      value: function listenToAll(callback) {
        var _this2 = this;
        this.subscription.bind_global(function(event, data) {
          if (event.startsWith("pusher:")) {
            return;
          }
          var namespace = _this2.options.namespace.replace(/\./g, "\\");
          var formattedEvent = event.startsWith(namespace) ? event.substring(namespace.length + 1) : "." + event;
          callback(formattedEvent, data);
        });
        return this;
      }
      /**
       * Stop listening for an event on the channel instance.
       */
    }, {
      key: "stopListening",
      value: function stopListening(event, callback) {
        if (callback) {
          this.subscription.unbind(this.eventFormatter.format(event), callback);
        } else {
          this.subscription.unbind(this.eventFormatter.format(event));
        }
        return this;
      }
      /**
       * Stop listening for all events on the channel instance.
       */
    }, {
      key: "stopListeningToAll",
      value: function stopListeningToAll(callback) {
        if (callback) {
          this.subscription.unbind_global(callback);
        } else {
          this.subscription.unbind_global();
        }
        return this;
      }
      /**
       * Register a callback to be called anytime a subscription succeeds.
       */
    }, {
      key: "subscribed",
      value: function subscribed(callback) {
        this.on("pusher:subscription_succeeded", function() {
          callback();
        });
        return this;
      }
      /**
       * Register a callback to be called anytime a subscription error occurs.
       */
    }, {
      key: "error",
      value: function error(callback) {
        this.on("pusher:subscription_error", function(status) {
          callback(status);
        });
        return this;
      }
      /**
       * Bind a channel to an event.
       */
    }, {
      key: "on",
      value: function on(event, callback) {
        this.subscription.bind(event, callback);
        return this;
      }
    }]);
    return PusherChannel2;
  }(Channel);
  var PusherPrivateChannel = /* @__PURE__ */ function(_PusherChannel) {
    _inherits(PusherPrivateChannel2, _PusherChannel);
    var _super = _createSuper(PusherPrivateChannel2);
    function PusherPrivateChannel2() {
      _classCallCheck(this, PusherPrivateChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(PusherPrivateChannel2, [{
      key: "whisper",
      value: (
        /**
         * Send a whisper event to other clients in the channel.
         */
        function whisper(eventName, data) {
          this.pusher.channels.channels[this.name].trigger("client-".concat(eventName), data);
          return this;
        }
      )
    }]);
    return PusherPrivateChannel2;
  }(PusherChannel);
  var PusherEncryptedPrivateChannel = /* @__PURE__ */ function(_PusherChannel) {
    _inherits(PusherEncryptedPrivateChannel2, _PusherChannel);
    var _super = _createSuper(PusherEncryptedPrivateChannel2);
    function PusherEncryptedPrivateChannel2() {
      _classCallCheck(this, PusherEncryptedPrivateChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(PusherEncryptedPrivateChannel2, [{
      key: "whisper",
      value: (
        /**
         * Send a whisper event to other clients in the channel.
         */
        function whisper(eventName, data) {
          this.pusher.channels.channels[this.name].trigger("client-".concat(eventName), data);
          return this;
        }
      )
    }]);
    return PusherEncryptedPrivateChannel2;
  }(PusherChannel);
  var PusherPresenceChannel = /* @__PURE__ */ function(_PusherChannel) {
    _inherits(PusherPresenceChannel2, _PusherChannel);
    var _super = _createSuper(PusherPresenceChannel2);
    function PusherPresenceChannel2() {
      _classCallCheck(this, PusherPresenceChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(PusherPresenceChannel2, [{
      key: "here",
      value: (
        /**
         * Register a callback to be called anytime the member list changes.
         */
        function here(callback) {
          this.on("pusher:subscription_succeeded", function(data) {
            callback(Object.keys(data.members).map(function(k) {
              return data.members[k];
            }));
          });
          return this;
        }
      )
      /**
       * Listen for someone joining the channel.
       */
    }, {
      key: "joining",
      value: function joining(callback) {
        this.on("pusher:member_added", function(member) {
          callback(member.info);
        });
        return this;
      }
      /**
       * Send a whisper event to other clients in the channel.
       */
    }, {
      key: "whisper",
      value: function whisper(eventName, data) {
        this.pusher.channels.channels[this.name].trigger("client-".concat(eventName), data);
        return this;
      }
      /**
       * Listen for someone leaving the channel.
       */
    }, {
      key: "leaving",
      value: function leaving(callback) {
        this.on("pusher:member_removed", function(member) {
          callback(member.info);
        });
        return this;
      }
    }]);
    return PusherPresenceChannel2;
  }(PusherChannel);
  var SocketIoChannel = /* @__PURE__ */ function(_Channel) {
    _inherits(SocketIoChannel2, _Channel);
    var _super = _createSuper(SocketIoChannel2);
    function SocketIoChannel2(socket, name, options) {
      var _this;
      _classCallCheck(this, SocketIoChannel2);
      _this = _super.call(this);
      _this.events = {};
      _this.listeners = {};
      _this.name = name;
      _this.socket = socket;
      _this.options = options;
      _this.eventFormatter = new EventFormatter(_this.options.namespace);
      _this.subscribe();
      return _this;
    }
    _createClass(SocketIoChannel2, [{
      key: "subscribe",
      value: function subscribe() {
        this.socket.emit("subscribe", {
          channel: this.name,
          auth: this.options.auth || {}
        });
      }
      /**
       * Unsubscribe from channel and ubind event callbacks.
       */
    }, {
      key: "unsubscribe",
      value: function unsubscribe() {
        this.unbind();
        this.socket.emit("unsubscribe", {
          channel: this.name,
          auth: this.options.auth || {}
        });
      }
      /**
       * Listen for an event on the channel instance.
       */
    }, {
      key: "listen",
      value: function listen(event, callback) {
        this.on(this.eventFormatter.format(event), callback);
        return this;
      }
      /**
       * Stop listening for an event on the channel instance.
       */
    }, {
      key: "stopListening",
      value: function stopListening(event, callback) {
        this.unbindEvent(this.eventFormatter.format(event), callback);
        return this;
      }
      /**
       * Register a callback to be called anytime a subscription succeeds.
       */
    }, {
      key: "subscribed",
      value: function subscribed(callback) {
        this.on("connect", function(socket) {
          callback(socket);
        });
        return this;
      }
      /**
       * Register a callback to be called anytime an error occurs.
       */
    }, {
      key: "error",
      value: function error(callback) {
        return this;
      }
      /**
       * Bind the channel's socket to an event and store the callback.
       */
    }, {
      key: "on",
      value: function on(event, callback) {
        var _this2 = this;
        this.listeners[event] = this.listeners[event] || [];
        if (!this.events[event]) {
          this.events[event] = function(channel, data) {
            if (_this2.name === channel && _this2.listeners[event]) {
              _this2.listeners[event].forEach(function(cb) {
                return cb(data);
              });
            }
          };
          this.socket.on(event, this.events[event]);
        }
        this.listeners[event].push(callback);
        return this;
      }
      /**
       * Unbind the channel's socket from all stored event callbacks.
       */
    }, {
      key: "unbind",
      value: function unbind() {
        var _this3 = this;
        Object.keys(this.events).forEach(function(event) {
          _this3.unbindEvent(event);
        });
      }
      /**
       * Unbind the listeners for the given event.
       */
    }, {
      key: "unbindEvent",
      value: function unbindEvent(event, callback) {
        this.listeners[event] = this.listeners[event] || [];
        if (callback) {
          this.listeners[event] = this.listeners[event].filter(function(cb) {
            return cb !== callback;
          });
        }
        if (!callback || this.listeners[event].length === 0) {
          if (this.events[event]) {
            this.socket.removeListener(event, this.events[event]);
            delete this.events[event];
          }
          delete this.listeners[event];
        }
      }
    }]);
    return SocketIoChannel2;
  }(Channel);
  var SocketIoPrivateChannel = /* @__PURE__ */ function(_SocketIoChannel) {
    _inherits(SocketIoPrivateChannel2, _SocketIoChannel);
    var _super = _createSuper(SocketIoPrivateChannel2);
    function SocketIoPrivateChannel2() {
      _classCallCheck(this, SocketIoPrivateChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(SocketIoPrivateChannel2, [{
      key: "whisper",
      value: (
        /**
         * Send a whisper event to other clients in the channel.
         */
        function whisper(eventName, data) {
          this.socket.emit("client event", {
            channel: this.name,
            event: "client-".concat(eventName),
            data
          });
          return this;
        }
      )
    }]);
    return SocketIoPrivateChannel2;
  }(SocketIoChannel);
  var SocketIoPresenceChannel = /* @__PURE__ */ function(_SocketIoPrivateChann) {
    _inherits(SocketIoPresenceChannel2, _SocketIoPrivateChann);
    var _super = _createSuper(SocketIoPresenceChannel2);
    function SocketIoPresenceChannel2() {
      _classCallCheck(this, SocketIoPresenceChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(SocketIoPresenceChannel2, [{
      key: "here",
      value: (
        /**
         * Register a callback to be called anytime the member list changes.
         */
        function here(callback) {
          this.on("presence:subscribed", function(members) {
            callback(members.map(function(m) {
              return m.user_info;
            }));
          });
          return this;
        }
      )
      /**
       * Listen for someone joining the channel.
       */
    }, {
      key: "joining",
      value: function joining(callback) {
        this.on("presence:joining", function(member) {
          return callback(member.user_info);
        });
        return this;
      }
      /**
       * Send a whisper event to other clients in the channel.
       */
    }, {
      key: "whisper",
      value: function whisper(eventName, data) {
        this.socket.emit("client event", {
          channel: this.name,
          event: "client-".concat(eventName),
          data
        });
        return this;
      }
      /**
       * Listen for someone leaving the channel.
       */
    }, {
      key: "leaving",
      value: function leaving(callback) {
        this.on("presence:leaving", function(member) {
          return callback(member.user_info);
        });
        return this;
      }
    }]);
    return SocketIoPresenceChannel2;
  }(SocketIoPrivateChannel);
  var NullChannel = /* @__PURE__ */ function(_Channel) {
    _inherits(NullChannel2, _Channel);
    var _super = _createSuper(NullChannel2);
    function NullChannel2() {
      _classCallCheck(this, NullChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(NullChannel2, [{
      key: "subscribe",
      value: (
        /**
         * Subscribe to a channel.
         */
        function subscribe() {
        }
      )
      /**
       * Unsubscribe from a channel.
       */
    }, {
      key: "unsubscribe",
      value: function unsubscribe() {
      }
      /**
       * Listen for an event on the channel instance.
       */
    }, {
      key: "listen",
      value: function listen(event, callback) {
        return this;
      }
      /**
       * Listen for all events on the channel instance.
       */
    }, {
      key: "listenToAll",
      value: function listenToAll(callback) {
        return this;
      }
      /**
       * Stop listening for an event on the channel instance.
       */
    }, {
      key: "stopListening",
      value: function stopListening(event, callback) {
        return this;
      }
      /**
       * Register a callback to be called anytime a subscription succeeds.
       */
    }, {
      key: "subscribed",
      value: function subscribed(callback) {
        return this;
      }
      /**
       * Register a callback to be called anytime an error occurs.
       */
    }, {
      key: "error",
      value: function error(callback) {
        return this;
      }
      /**
       * Bind a channel to an event.
       */
    }, {
      key: "on",
      value: function on(event, callback) {
        return this;
      }
    }]);
    return NullChannel2;
  }(Channel);
  var NullPrivateChannel = /* @__PURE__ */ function(_NullChannel) {
    _inherits(NullPrivateChannel2, _NullChannel);
    var _super = _createSuper(NullPrivateChannel2);
    function NullPrivateChannel2() {
      _classCallCheck(this, NullPrivateChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(NullPrivateChannel2, [{
      key: "whisper",
      value: (
        /**
         * Send a whisper event to other clients in the channel.
         */
        function whisper(eventName, data) {
          return this;
        }
      )
    }]);
    return NullPrivateChannel2;
  }(NullChannel);
  var NullPresenceChannel = /* @__PURE__ */ function(_NullChannel) {
    _inherits(NullPresenceChannel2, _NullChannel);
    var _super = _createSuper(NullPresenceChannel2);
    function NullPresenceChannel2() {
      _classCallCheck(this, NullPresenceChannel2);
      return _super.apply(this, arguments);
    }
    _createClass(NullPresenceChannel2, [{
      key: "here",
      value: (
        /**
         * Register a callback to be called anytime the member list changes.
         */
        function here(callback) {
          return this;
        }
      )
      /**
       * Listen for someone joining the channel.
       */
    }, {
      key: "joining",
      value: function joining(callback) {
        return this;
      }
      /**
       * Send a whisper event to other clients in the channel.
       */
    }, {
      key: "whisper",
      value: function whisper(eventName, data) {
        return this;
      }
      /**
       * Listen for someone leaving the channel.
       */
    }, {
      key: "leaving",
      value: function leaving(callback) {
        return this;
      }
    }]);
    return NullPresenceChannel2;
  }(NullChannel);
  var Connector = /* @__PURE__ */ function() {
    function Connector2(options) {
      _classCallCheck(this, Connector2);
      this._defaultOptions = {
        auth: {
          headers: {}
        },
        authEndpoint: "/broadcasting/auth",
        userAuthentication: {
          endpoint: "/broadcasting/user-auth",
          headers: {}
        },
        broadcaster: "pusher",
        csrfToken: null,
        bearerToken: null,
        host: null,
        key: null,
        namespace: "App.Events"
      };
      this.setOptions(options);
      this.connect();
    }
    _createClass(Connector2, [{
      key: "setOptions",
      value: function setOptions(options) {
        this.options = _extends(this._defaultOptions, options);
        var token = this.csrfToken();
        if (token) {
          this.options.auth.headers["X-CSRF-TOKEN"] = token;
          this.options.userAuthentication.headers["X-CSRF-TOKEN"] = token;
        }
        token = this.options.bearerToken;
        if (token) {
          this.options.auth.headers["Authorization"] = "Bearer " + token;
          this.options.userAuthentication.headers["Authorization"] = "Bearer " + token;
        }
        return options;
      }
      /**
       * Extract the CSRF token from the page.
       */
    }, {
      key: "csrfToken",
      value: function csrfToken() {
        var selector;
        if (typeof window !== "undefined" && window["Laravel"] && window["Laravel"].csrfToken) {
          return window["Laravel"].csrfToken;
        } else if (this.options.csrfToken) {
          return this.options.csrfToken;
        } else if (typeof document !== "undefined" && typeof document.querySelector === "function" && (selector = document.querySelector('meta[name="csrf-token"]'))) {
          return selector.getAttribute("content");
        }
        return null;
      }
    }]);
    return Connector2;
  }();
  var PusherConnector = /* @__PURE__ */ function(_Connector) {
    _inherits(PusherConnector2, _Connector);
    var _super = _createSuper(PusherConnector2);
    function PusherConnector2() {
      var _this;
      _classCallCheck(this, PusherConnector2);
      _this = _super.apply(this, arguments);
      _this.channels = {};
      return _this;
    }
    _createClass(PusherConnector2, [{
      key: "connect",
      value: function connect() {
        if (typeof this.options.client !== "undefined") {
          this.pusher = this.options.client;
        } else if (this.options.Pusher) {
          this.pusher = new this.options.Pusher(this.options.key, this.options);
        } else {
          this.pusher = new Pusher(this.options.key, this.options);
        }
      }
      /**
       * Sign in the user via Pusher user authentication (https://pusher.com/docs/channels/using_channels/user-authentication/).
       */
    }, {
      key: "signin",
      value: function signin() {
        this.pusher.signin();
      }
      /**
       * Listen for an event on a channel instance.
       */
    }, {
      key: "listen",
      value: function listen(name, event, callback) {
        return this.channel(name).listen(event, callback);
      }
      /**
       * Get a channel instance by name.
       */
    }, {
      key: "channel",
      value: function channel(name) {
        if (!this.channels[name]) {
          this.channels[name] = new PusherChannel(this.pusher, name, this.options);
        }
        return this.channels[name];
      }
      /**
       * Get a private channel instance by name.
       */
    }, {
      key: "privateChannel",
      value: function privateChannel(name) {
        if (!this.channels["private-" + name]) {
          this.channels["private-" + name] = new PusherPrivateChannel(this.pusher, "private-" + name, this.options);
        }
        return this.channels["private-" + name];
      }
      /**
       * Get a private encrypted channel instance by name.
       */
    }, {
      key: "encryptedPrivateChannel",
      value: function encryptedPrivateChannel(name) {
        if (!this.channels["private-encrypted-" + name]) {
          this.channels["private-encrypted-" + name] = new PusherEncryptedPrivateChannel(this.pusher, "private-encrypted-" + name, this.options);
        }
        return this.channels["private-encrypted-" + name];
      }
      /**
       * Get a presence channel instance by name.
       */
    }, {
      key: "presenceChannel",
      value: function presenceChannel(name) {
        if (!this.channels["presence-" + name]) {
          this.channels["presence-" + name] = new PusherPresenceChannel(this.pusher, "presence-" + name, this.options);
        }
        return this.channels["presence-" + name];
      }
      /**
       * Leave the given channel, as well as its private and presence variants.
       */
    }, {
      key: "leave",
      value: function leave(name) {
        var _this2 = this;
        var channels = [name, "private-" + name, "private-encrypted-" + name, "presence-" + name];
        channels.forEach(function(name2, index) {
          _this2.leaveChannel(name2);
        });
      }
      /**
       * Leave the given channel.
       */
    }, {
      key: "leaveChannel",
      value: function leaveChannel(name) {
        if (this.channels[name]) {
          this.channels[name].unsubscribe();
          delete this.channels[name];
        }
      }
      /**
       * Get the socket ID for the connection.
       */
    }, {
      key: "socketId",
      value: function socketId() {
        return this.pusher.connection.socket_id;
      }
      /**
       * Disconnect Pusher connection.
       */
    }, {
      key: "disconnect",
      value: function disconnect() {
        this.pusher.disconnect();
      }
    }]);
    return PusherConnector2;
  }(Connector);
  var SocketIoConnector = /* @__PURE__ */ function(_Connector) {
    _inherits(SocketIoConnector2, _Connector);
    var _super = _createSuper(SocketIoConnector2);
    function SocketIoConnector2() {
      var _this;
      _classCallCheck(this, SocketIoConnector2);
      _this = _super.apply(this, arguments);
      _this.channels = {};
      return _this;
    }
    _createClass(SocketIoConnector2, [{
      key: "connect",
      value: function connect() {
        var _this2 = this;
        var io2 = this.getSocketIO();
        this.socket = io2(this.options.host, this.options);
        this.socket.on("reconnect", function() {
          Object.values(_this2.channels).forEach(function(channel) {
            channel.subscribe();
          });
        });
        return this.socket;
      }
      /**
       * Get socket.io module from global scope or options.
       */
    }, {
      key: "getSocketIO",
      value: function getSocketIO() {
        if (typeof this.options.client !== "undefined") {
          return this.options.client;
        }
        if (typeof io !== "undefined") {
          return io;
        }
        throw new Error("Socket.io client not found. Should be globally available or passed via options.client");
      }
      /**
       * Listen for an event on a channel instance.
       */
    }, {
      key: "listen",
      value: function listen(name, event, callback) {
        return this.channel(name).listen(event, callback);
      }
      /**
       * Get a channel instance by name.
       */
    }, {
      key: "channel",
      value: function channel(name) {
        if (!this.channels[name]) {
          this.channels[name] = new SocketIoChannel(this.socket, name, this.options);
        }
        return this.channels[name];
      }
      /**
       * Get a private channel instance by name.
       */
    }, {
      key: "privateChannel",
      value: function privateChannel(name) {
        if (!this.channels["private-" + name]) {
          this.channels["private-" + name] = new SocketIoPrivateChannel(this.socket, "private-" + name, this.options);
        }
        return this.channels["private-" + name];
      }
      /**
       * Get a presence channel instance by name.
       */
    }, {
      key: "presenceChannel",
      value: function presenceChannel(name) {
        if (!this.channels["presence-" + name]) {
          this.channels["presence-" + name] = new SocketIoPresenceChannel(this.socket, "presence-" + name, this.options);
        }
        return this.channels["presence-" + name];
      }
      /**
       * Leave the given channel, as well as its private and presence variants.
       */
    }, {
      key: "leave",
      value: function leave(name) {
        var _this3 = this;
        var channels = [name, "private-" + name, "presence-" + name];
        channels.forEach(function(name2) {
          _this3.leaveChannel(name2);
        });
      }
      /**
       * Leave the given channel.
       */
    }, {
      key: "leaveChannel",
      value: function leaveChannel(name) {
        if (this.channels[name]) {
          this.channels[name].unsubscribe();
          delete this.channels[name];
        }
      }
      /**
       * Get the socket ID for the connection.
       */
    }, {
      key: "socketId",
      value: function socketId() {
        return this.socket.id;
      }
      /**
       * Disconnect Socketio connection.
       */
    }, {
      key: "disconnect",
      value: function disconnect() {
        this.socket.disconnect();
      }
    }]);
    return SocketIoConnector2;
  }(Connector);
  var NullConnector = /* @__PURE__ */ function(_Connector) {
    _inherits(NullConnector2, _Connector);
    var _super = _createSuper(NullConnector2);
    function NullConnector2() {
      var _this;
      _classCallCheck(this, NullConnector2);
      _this = _super.apply(this, arguments);
      _this.channels = {};
      return _this;
    }
    _createClass(NullConnector2, [{
      key: "connect",
      value: function connect() {
      }
      /**
       * Listen for an event on a channel instance.
       */
    }, {
      key: "listen",
      value: function listen(name, event, callback) {
        return new NullChannel();
      }
      /**
       * Get a channel instance by name.
       */
    }, {
      key: "channel",
      value: function channel(name) {
        return new NullChannel();
      }
      /**
       * Get a private channel instance by name.
       */
    }, {
      key: "privateChannel",
      value: function privateChannel(name) {
        return new NullPrivateChannel();
      }
      /**
       * Get a private encrypted channel instance by name.
       */
    }, {
      key: "encryptedPrivateChannel",
      value: function encryptedPrivateChannel(name) {
        return new NullPrivateChannel();
      }
      /**
       * Get a presence channel instance by name.
       */
    }, {
      key: "presenceChannel",
      value: function presenceChannel(name) {
        return new NullPresenceChannel();
      }
      /**
       * Leave the given channel, as well as its private and presence variants.
       */
    }, {
      key: "leave",
      value: function leave(name) {
      }
      /**
       * Leave the given channel.
       */
    }, {
      key: "leaveChannel",
      value: function leaveChannel(name) {
      }
      /**
       * Get the socket ID for the connection.
       */
    }, {
      key: "socketId",
      value: function socketId() {
        return "fake-socket-id";
      }
      /**
       * Disconnect the connection.
       */
    }, {
      key: "disconnect",
      value: function disconnect() {
      }
    }]);
    return NullConnector2;
  }(Connector);
  var Echo = /* @__PURE__ */ function() {
    function Echo2(options) {
      _classCallCheck(this, Echo2);
      this.options = options;
      this.connect();
      if (!this.options.withoutInterceptors) {
        this.registerInterceptors();
      }
    }
    _createClass(Echo2, [{
      key: "channel",
      value: function channel(_channel) {
        return this.connector.channel(_channel);
      }
      /**
       * Create a new connection.
       */
    }, {
      key: "connect",
      value: function connect() {
        if (this.options.broadcaster == "pusher") {
          this.connector = new PusherConnector(this.options);
        } else if (this.options.broadcaster == "socket.io") {
          this.connector = new SocketIoConnector(this.options);
        } else if (this.options.broadcaster == "null") {
          this.connector = new NullConnector(this.options);
        } else if (typeof this.options.broadcaster == "function") {
          this.connector = new this.options.broadcaster(this.options);
        }
      }
      /**
       * Disconnect from the Echo server.
       */
    }, {
      key: "disconnect",
      value: function disconnect() {
        this.connector.disconnect();
      }
      /**
       * Get a presence channel instance by name.
       */
    }, {
      key: "join",
      value: function join(channel) {
        return this.connector.presenceChannel(channel);
      }
      /**
       * Leave the given channel, as well as its private and presence variants.
       */
    }, {
      key: "leave",
      value: function leave(channel) {
        this.connector.leave(channel);
      }
      /**
       * Leave the given channel.
       */
    }, {
      key: "leaveChannel",
      value: function leaveChannel(channel) {
        this.connector.leaveChannel(channel);
      }
      /**
       * Leave all channels.
       */
    }, {
      key: "leaveAllChannels",
      value: function leaveAllChannels() {
        for (var channel in this.connector.channels) {
          this.leaveChannel(channel);
        }
      }
      /**
       * Listen for an event on a channel instance.
       */
    }, {
      key: "listen",
      value: function listen(channel, event, callback) {
        return this.connector.listen(channel, event, callback);
      }
      /**
       * Get a private channel instance by name.
       */
    }, {
      key: "private",
      value: function _private(channel) {
        return this.connector.privateChannel(channel);
      }
      /**
       * Get a private encrypted channel instance by name.
       */
    }, {
      key: "encryptedPrivate",
      value: function encryptedPrivate(channel) {
        return this.connector.encryptedPrivateChannel(channel);
      }
      /**
       * Get the Socket ID for the connection.
       */
    }, {
      key: "socketId",
      value: function socketId() {
        return this.connector.socketId();
      }
      /**
       * Register 3rd party request interceptiors. These are used to automatically
       * send a connections socket id to a Laravel app with a X-Socket-Id header.
       */
    }, {
      key: "registerInterceptors",
      value: function registerInterceptors() {
        if (typeof Vue === "function" && Vue.http) {
          this.registerVueRequestInterceptor();
        }
        if (typeof axios === "function") {
          this.registerAxiosRequestInterceptor();
        }
        if (typeof jQuery === "function") {
          this.registerjQueryAjaxSetup();
        }
        if ((typeof Turbo === "undefined" ? "undefined" : _typeof(Turbo)) === "object") {
          this.registerTurboRequestInterceptor();
        }
      }
      /**
       * Register a Vue HTTP interceptor to add the X-Socket-ID header.
       */
    }, {
      key: "registerVueRequestInterceptor",
      value: function registerVueRequestInterceptor() {
        var _this = this;
        Vue.http.interceptors.push(function(request, next) {
          if (_this.socketId()) {
            request.headers.set("X-Socket-ID", _this.socketId());
          }
          next();
        });
      }
      /**
       * Register an Axios HTTP interceptor to add the X-Socket-ID header.
       */
    }, {
      key: "registerAxiosRequestInterceptor",
      value: function registerAxiosRequestInterceptor() {
        var _this2 = this;
        axios.interceptors.request.use(function(config) {
          if (_this2.socketId()) {
            config.headers["X-Socket-Id"] = _this2.socketId();
          }
          return config;
        });
      }
      /**
       * Register jQuery AjaxPrefilter to add the X-Socket-ID header.
       */
    }, {
      key: "registerjQueryAjaxSetup",
      value: function registerjQueryAjaxSetup() {
        var _this3 = this;
        if (typeof jQuery.ajax != "undefined") {
          jQuery.ajaxPrefilter(function(options, originalOptions, xhr) {
            if (_this3.socketId()) {
              xhr.setRequestHeader("X-Socket-Id", _this3.socketId());
            }
          });
        }
      }
      /**
       * Register the Turbo Request interceptor to add the X-Socket-ID header.
       */
    }, {
      key: "registerTurboRequestInterceptor",
      value: function registerTurboRequestInterceptor() {
        var _this4 = this;
        document.addEventListener("turbo:before-fetch-request", function(event) {
          event.detail.fetchOptions.headers["X-Socket-Id"] = _this4.socketId();
        });
      }
    }]);
    return Echo2;
  }();

  // packages/app/resources/js/echo.js
  var import_pusher = __toESM(require_pusher());
  window.EchoFactory = Echo;
  window.Pusher = import_pusher.default;
})();
/*! Bundled license information:

pusher-js/dist/web/pusher.js:
  (*!
   * Pusher JavaScript Library v7.6.0
   * https://pusher.com/
   *
   * Copyright 2020, Pusher
   * Released under the MIT licence.
   *)
*/
