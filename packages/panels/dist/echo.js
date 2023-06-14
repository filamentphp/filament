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

  // packages/panels/resources/js/echo.js
  var import_pusher = __toESM(require_pusher(), 1);
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvd2VicGFjay91bml2ZXJzYWxNb2R1bGVEZWZpbml0aW9uIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3dlYnBhY2svYm9vdHN0cmFwIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL25vZGVfbW9kdWxlcy9Ac3RhYmxlbGliL2Jhc2U2NC9iYXNlNjQudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvbm9kZV9tb2R1bGVzL0BzdGFibGVsaWIvdXRmOC91dGY4LnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3B1c2hlci5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvd2ViL2RvbS9zY3JpcHRfcmVjZWl2ZXJfZmFjdG9yeS50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9kZWZhdWx0cy50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvd2ViL2RvbS9kZXBlbmRlbmN5X2xvYWRlci50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvd2ViL2RvbS9kZXBlbmRlbmNpZXMudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvdXRpbHMvdXJsX3N0b3JlLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL2F1dGgvb3B0aW9ucy50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9lcnJvcnMudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL3J1bnRpbWVzL2lzb21vcnBoaWMvYXV0aC94aHJfYXV0aC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9iYXNlNjQudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvdXRpbHMvdGltZXJzL2Fic3RyYWN0X3RpbWVyLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3V0aWxzL3RpbWVycy9pbmRleC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS91dGlsLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3V0aWxzL2NvbGxlY3Rpb25zLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL2xvZ2dlci50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvd2ViL2F1dGgvanNvbnBfYXV0aC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvd2ViL2RvbS9zY3JpcHRfcmVxdWVzdC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvd2ViL2RvbS9qc29ucF9yZXF1ZXN0LnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9ydW50aW1lcy93ZWIvdGltZWxpbmUvanNvbnBfdGltZWxpbmUudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvdHJhbnNwb3J0cy91cmxfc2NoZW1lcy50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9ldmVudHMvY2FsbGJhY2tfcmVnaXN0cnkudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvZXZlbnRzL2Rpc3BhdGNoZXIudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvdHJhbnNwb3J0cy90cmFuc3BvcnRfY29ubmVjdGlvbi50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS90cmFuc3BvcnRzL3RyYW5zcG9ydC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvaXNvbW9ycGhpYy90cmFuc3BvcnRzL3RyYW5zcG9ydHMudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL3J1bnRpbWVzL3dlYi90cmFuc3BvcnRzL3RyYW5zcG9ydHMudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL3J1bnRpbWVzL3dlYi9uZXRfaW5mby50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS90cmFuc3BvcnRzL2Fzc2lzdGFudF90b190aGVfdHJhbnNwb3J0X21hbmFnZXIudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvY29ubmVjdGlvbi9wcm90b2NvbC9wcm90b2NvbC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9jb25uZWN0aW9uL2Nvbm5lY3Rpb24udHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvY29ubmVjdGlvbi9oYW5kc2hha2UvaW5kZXgudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvdGltZWxpbmUvdGltZWxpbmVfc2VuZGVyLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL2NoYW5uZWxzL2NoYW5uZWwudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvY2hhbm5lbHMvcHJpdmF0ZV9jaGFubmVsLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL2NoYW5uZWxzL21lbWJlcnMudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvY2hhbm5lbHMvcHJlc2VuY2VfY2hhbm5lbC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9jaGFubmVscy9lbmNyeXB0ZWRfY2hhbm5lbC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9jb25uZWN0aW9uL2Nvbm5lY3Rpb25fbWFuYWdlci50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9jaGFubmVscy9jaGFubmVscy50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS91dGlscy9mYWN0b3J5LnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3RyYW5zcG9ydHMvdHJhbnNwb3J0X21hbmFnZXIudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvc3RyYXRlZ2llcy9zZXF1ZW50aWFsX3N0cmF0ZWd5LnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3N0cmF0ZWdpZXMvYmVzdF9jb25uZWN0ZWRfZXZlcl9zdHJhdGVneS50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9zdHJhdGVnaWVzL2NhY2hlZF9zdHJhdGVneS50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9zdHJhdGVnaWVzL2RlbGF5ZWRfc3RyYXRlZ3kudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvc3RyYXRlZ2llcy9pZl9zdHJhdGVneS50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9zdHJhdGVnaWVzL2ZpcnN0X2Nvbm5lY3RlZF9zdHJhdGVneS50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvd2ViL2RlZmF1bHRfc3RyYXRlZ3kudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL3J1bnRpbWVzL3dlYi90cmFuc3BvcnRzL3RyYW5zcG9ydF9jb25uZWN0aW9uX2luaXRpYWxpemVyLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9ydW50aW1lcy93ZWIvaHR0cC9odHRwX3hkb21haW5fcmVxdWVzdC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9odHRwL2h0dHBfcmVxdWVzdC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9odHRwL3N0YXRlLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL2h0dHAvaHR0cF9zb2NrZXQudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvaHR0cC9odHRwX3N0cmVhbWluZ19zb2NrZXQudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvaHR0cC9odHRwX3BvbGxpbmdfc29ja2V0LnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9ydW50aW1lcy9pc29tb3JwaGljL2h0dHAvaHR0cF94aHJfcmVxdWVzdC50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvcnVudGltZXMvaXNvbW9ycGhpYy9odHRwL2h0dHAudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL3J1bnRpbWVzL3dlYi9odHRwL2h0dHAudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL3J1bnRpbWVzL3dlYi9ydW50aW1lLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3RpbWVsaW5lL2xldmVsLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3RpbWVsaW5lL3RpbWVsaW5lLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3N0cmF0ZWdpZXMvdHJhbnNwb3J0X3N0cmF0ZWd5LnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3N0cmF0ZWdpZXMvc3RyYXRlZ3lfYnVpbGRlci50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9hdXRoL3VzZXJfYXV0aGVudGljYXRvci50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9hdXRoL2NoYW5uZWxfYXV0aG9yaXplci50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS9hdXRoL2RlcHJlY2F0ZWRfY2hhbm5lbF9hdXRob3JpemVyLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL2NvbmZpZy50cyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvcHVzaGVyLWpzL2Rpc3Qvd2ViL3dlYnBhY2s6L1B1c2hlci9zcmMvY29yZS93YXRjaGxpc3QudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvdXRpbHMvZmxhdF9wcm9taXNlLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9wdXNoZXItanMvZGlzdC93ZWIvd2VicGFjazovUHVzaGVyL3NyYy9jb3JlL3VzZXIudHMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3B1c2hlci1qcy9kaXN0L3dlYi93ZWJwYWNrOi9QdXNoZXIvc3JjL2NvcmUvcHVzaGVyLnRzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9sYXJhdmVsLWVjaG8vZGlzdC9lY2hvLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9lY2hvLmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIoZnVuY3Rpb24gd2VicGFja1VuaXZlcnNhbE1vZHVsZURlZmluaXRpb24ocm9vdCwgZmFjdG9yeSkge1xuXHRpZih0eXBlb2YgZXhwb3J0cyA9PT0gJ29iamVjdCcgJiYgdHlwZW9mIG1vZHVsZSA9PT0gJ29iamVjdCcpXG5cdFx0bW9kdWxlLmV4cG9ydHMgPSBmYWN0b3J5KCk7XG5cdGVsc2UgaWYodHlwZW9mIGRlZmluZSA9PT0gJ2Z1bmN0aW9uJyAmJiBkZWZpbmUuYW1kKVxuXHRcdGRlZmluZShbXSwgZmFjdG9yeSk7XG5cdGVsc2UgaWYodHlwZW9mIGV4cG9ydHMgPT09ICdvYmplY3QnKVxuXHRcdGV4cG9ydHNbXCJQdXNoZXJcIl0gPSBmYWN0b3J5KCk7XG5cdGVsc2Vcblx0XHRyb290W1wiUHVzaGVyXCJdID0gZmFjdG9yeSgpO1xufSkod2luZG93LCBmdW5jdGlvbigpIHtcbnJldHVybiAiLCAiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDIpO1xuIiwgIi8vIENvcHlyaWdodCAoQykgMjAxNiBEbWl0cnkgQ2hlc3RueWtoXG4vLyBNSVQgTGljZW5zZS4gU2VlIExJQ0VOU0UgZmlsZSBmb3IgZGV0YWlscy5cblxuLyoqXG4gKiBQYWNrYWdlIGJhc2U2NCBpbXBsZW1lbnRzIEJhc2U2NCBlbmNvZGluZyBhbmQgZGVjb2RpbmcuXG4gKi9cblxuLy8gSW52YWxpZCBjaGFyYWN0ZXIgdXNlZCBpbiBkZWNvZGluZyB0byBpbmRpY2F0ZVxuLy8gdGhhdCB0aGUgY2hhcmFjdGVyIHRvIGRlY29kZSBpcyBvdXQgb2YgcmFuZ2Ugb2Zcbi8vIGFscGhhYmV0IGFuZCBjYW5ub3QgYmUgZGVjb2RlZC5cbmNvbnN0IElOVkFMSURfQllURSA9IDI1NjtcblxuLyoqXG4gKiBJbXBsZW1lbnRzIHN0YW5kYXJkIEJhc2U2NCBlbmNvZGluZy5cbiAqXG4gKiBPcGVyYXRlcyBpbiBjb25zdGFudCB0aW1lLlxuICovXG5leHBvcnQgY2xhc3MgQ29kZXIge1xuICAgIC8vIFRPRE8oZGNoZXN0KTogbWV0aG9kcyB0byBlbmNvZGUgY2h1bmstYnktY2h1bmsuXG5cbiAgICBjb25zdHJ1Y3Rvcihwcml2YXRlIF9wYWRkaW5nQ2hhcmFjdGVyID0gXCI9XCIpIHsgfVxuXG4gICAgZW5jb2RlZExlbmd0aChsZW5ndGg6IG51bWJlcik6IG51bWJlciB7XG4gICAgICAgIGlmICghdGhpcy5fcGFkZGluZ0NoYXJhY3Rlcikge1xuICAgICAgICAgICAgcmV0dXJuIChsZW5ndGggKiA4ICsgNSkgLyA2IHwgMDtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gKGxlbmd0aCArIDIpIC8gMyAqIDQgfCAwO1xuICAgIH1cblxuICAgIGVuY29kZShkYXRhOiBVaW50OEFycmF5KTogc3RyaW5nIHtcbiAgICAgICAgbGV0IG91dCA9IFwiXCI7XG5cbiAgICAgICAgbGV0IGkgPSAwO1xuICAgICAgICBmb3IgKDsgaSA8IGRhdGEubGVuZ3RoIC0gMjsgaSArPSAzKSB7XG4gICAgICAgICAgICBsZXQgYyA9IChkYXRhW2ldIDw8IDE2KSB8IChkYXRhW2kgKyAxXSA8PCA4KSB8IChkYXRhW2kgKyAyXSk7XG4gICAgICAgICAgICBvdXQgKz0gdGhpcy5fZW5jb2RlQnl0ZSgoYyA+Pj4gMyAqIDYpICYgNjMpO1xuICAgICAgICAgICAgb3V0ICs9IHRoaXMuX2VuY29kZUJ5dGUoKGMgPj4+IDIgKiA2KSAmIDYzKTtcbiAgICAgICAgICAgIG91dCArPSB0aGlzLl9lbmNvZGVCeXRlKChjID4+PiAxICogNikgJiA2Myk7XG4gICAgICAgICAgICBvdXQgKz0gdGhpcy5fZW5jb2RlQnl0ZSgoYyA+Pj4gMCAqIDYpICYgNjMpO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgbGVmdCA9IGRhdGEubGVuZ3RoIC0gaTtcbiAgICAgICAgaWYgKGxlZnQgPiAwKSB7XG4gICAgICAgICAgICBsZXQgYyA9IChkYXRhW2ldIDw8IDE2KSB8IChsZWZ0ID09PSAyID8gZGF0YVtpICsgMV0gPDwgOCA6IDApO1xuICAgICAgICAgICAgb3V0ICs9IHRoaXMuX2VuY29kZUJ5dGUoKGMgPj4+IDMgKiA2KSAmIDYzKTtcbiAgICAgICAgICAgIG91dCArPSB0aGlzLl9lbmNvZGVCeXRlKChjID4+PiAyICogNikgJiA2Myk7XG4gICAgICAgICAgICBpZiAobGVmdCA9PT0gMikge1xuICAgICAgICAgICAgICAgIG91dCArPSB0aGlzLl9lbmNvZGVCeXRlKChjID4+PiAxICogNikgJiA2Myk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIG91dCArPSB0aGlzLl9wYWRkaW5nQ2hhcmFjdGVyIHx8IFwiXCI7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBvdXQgKz0gdGhpcy5fcGFkZGluZ0NoYXJhY3RlciB8fCBcIlwiO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIG91dDtcbiAgICB9XG5cbiAgICBtYXhEZWNvZGVkTGVuZ3RoKGxlbmd0aDogbnVtYmVyKTogbnVtYmVyIHtcbiAgICAgICAgaWYgKCF0aGlzLl9wYWRkaW5nQ2hhcmFjdGVyKSB7XG4gICAgICAgICAgICByZXR1cm4gKGxlbmd0aCAqIDYgKyA3KSAvIDggfCAwO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBsZW5ndGggLyA0ICogMyB8IDA7XG4gICAgfVxuXG4gICAgZGVjb2RlZExlbmd0aChzOiBzdHJpbmcpOiBudW1iZXIge1xuICAgICAgICByZXR1cm4gdGhpcy5tYXhEZWNvZGVkTGVuZ3RoKHMubGVuZ3RoIC0gdGhpcy5fZ2V0UGFkZGluZ0xlbmd0aChzKSk7XG4gICAgfVxuXG4gICAgZGVjb2RlKHM6IHN0cmluZyk6IFVpbnQ4QXJyYXkge1xuICAgICAgICBpZiAocy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIHJldHVybiBuZXcgVWludDhBcnJheSgwKTtcbiAgICAgICAgfVxuICAgICAgICBjb25zdCBwYWRkaW5nTGVuZ3RoID0gdGhpcy5fZ2V0UGFkZGluZ0xlbmd0aChzKTtcbiAgICAgICAgY29uc3QgbGVuZ3RoID0gcy5sZW5ndGggLSBwYWRkaW5nTGVuZ3RoO1xuICAgICAgICBjb25zdCBvdXQgPSBuZXcgVWludDhBcnJheSh0aGlzLm1heERlY29kZWRMZW5ndGgobGVuZ3RoKSk7XG4gICAgICAgIGxldCBvcCA9IDA7XG4gICAgICAgIGxldCBpID0gMDtcbiAgICAgICAgbGV0IGhhdmVCYWQgPSAwO1xuICAgICAgICBsZXQgdjAgPSAwLCB2MSA9IDAsIHYyID0gMCwgdjMgPSAwO1xuICAgICAgICBmb3IgKDsgaSA8IGxlbmd0aCAtIDQ7IGkgKz0gNCkge1xuICAgICAgICAgICAgdjAgPSB0aGlzLl9kZWNvZGVDaGFyKHMuY2hhckNvZGVBdChpICsgMCkpO1xuICAgICAgICAgICAgdjEgPSB0aGlzLl9kZWNvZGVDaGFyKHMuY2hhckNvZGVBdChpICsgMSkpO1xuICAgICAgICAgICAgdjIgPSB0aGlzLl9kZWNvZGVDaGFyKHMuY2hhckNvZGVBdChpICsgMikpO1xuICAgICAgICAgICAgdjMgPSB0aGlzLl9kZWNvZGVDaGFyKHMuY2hhckNvZGVBdChpICsgMykpO1xuICAgICAgICAgICAgb3V0W29wKytdID0gKHYwIDw8IDIpIHwgKHYxID4+PiA0KTtcbiAgICAgICAgICAgIG91dFtvcCsrXSA9ICh2MSA8PCA0KSB8ICh2MiA+Pj4gMik7XG4gICAgICAgICAgICBvdXRbb3ArK10gPSAodjIgPDwgNikgfCB2MztcbiAgICAgICAgICAgIGhhdmVCYWQgfD0gdjAgJiBJTlZBTElEX0JZVEU7XG4gICAgICAgICAgICBoYXZlQmFkIHw9IHYxICYgSU5WQUxJRF9CWVRFO1xuICAgICAgICAgICAgaGF2ZUJhZCB8PSB2MiAmIElOVkFMSURfQllURTtcbiAgICAgICAgICAgIGhhdmVCYWQgfD0gdjMgJiBJTlZBTElEX0JZVEU7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGkgPCBsZW5ndGggLSAxKSB7XG4gICAgICAgICAgICB2MCA9IHRoaXMuX2RlY29kZUNoYXIocy5jaGFyQ29kZUF0KGkpKTtcbiAgICAgICAgICAgIHYxID0gdGhpcy5fZGVjb2RlQ2hhcihzLmNoYXJDb2RlQXQoaSArIDEpKTtcbiAgICAgICAgICAgIG91dFtvcCsrXSA9ICh2MCA8PCAyKSB8ICh2MSA+Pj4gNCk7XG4gICAgICAgICAgICBoYXZlQmFkIHw9IHYwICYgSU5WQUxJRF9CWVRFO1xuICAgICAgICAgICAgaGF2ZUJhZCB8PSB2MSAmIElOVkFMSURfQllURTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoaSA8IGxlbmd0aCAtIDIpIHtcbiAgICAgICAgICAgIHYyID0gdGhpcy5fZGVjb2RlQ2hhcihzLmNoYXJDb2RlQXQoaSArIDIpKTtcbiAgICAgICAgICAgIG91dFtvcCsrXSA9ICh2MSA8PCA0KSB8ICh2MiA+Pj4gMik7XG4gICAgICAgICAgICBoYXZlQmFkIHw9IHYyICYgSU5WQUxJRF9CWVRFO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpIDwgbGVuZ3RoIC0gMykge1xuICAgICAgICAgICAgdjMgPSB0aGlzLl9kZWNvZGVDaGFyKHMuY2hhckNvZGVBdChpICsgMykpO1xuICAgICAgICAgICAgb3V0W29wKytdID0gKHYyIDw8IDYpIHwgdjM7XG4gICAgICAgICAgICBoYXZlQmFkIHw9IHYzICYgSU5WQUxJRF9CWVRFO1xuICAgICAgICB9XG4gICAgICAgIGlmIChoYXZlQmFkICE9PSAwKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoXCJCYXNlNjRDb2RlcjogaW5jb3JyZWN0IGNoYXJhY3RlcnMgZm9yIGRlY29kaW5nXCIpO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBvdXQ7XG4gICAgfVxuXG4gICAgLy8gU3RhbmRhcmQgZW5jb2RpbmcgaGF2ZSB0aGUgZm9sbG93aW5nIGVuY29kZWQvZGVjb2RlZCByYW5nZXMsXG4gICAgLy8gd2hpY2ggd2UgbmVlZCB0byBjb252ZXJ0IGJldHdlZW4uXG4gICAgLy9cbiAgICAvLyBBQkNERUZHSElKS0xNTk9QUVJTVFVWV1hZWiBhYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5eiAwMTIzNDU2Nzg5ICArICAgL1xuICAgIC8vIEluZGV4OiAgIDAgLSAyNSAgICAgICAgICAgICAgICAgICAgMjYgLSA1MSAgICAgICAgICAgICAgNTIgLSA2MSAgIDYyICA2M1xuICAgIC8vIEFTQ0lJOiAgNjUgLSA5MCAgICAgICAgICAgICAgICAgICAgOTcgLSAxMjIgICAgICAgICAgICAgNDggLSA1NyAgIDQzICA0N1xuICAgIC8vXG5cbiAgICAvLyBFbmNvZGUgNiBiaXRzIGluIGIgaW50byBhIG5ldyBjaGFyYWN0ZXIuXG4gICAgcHJvdGVjdGVkIF9lbmNvZGVCeXRlKGI6IG51bWJlcik6IHN0cmluZyB7XG4gICAgICAgIC8vIEVuY29kaW5nIHVzZXMgY29uc3RhbnQgdGltZSBvcGVyYXRpb25zIGFzIGZvbGxvd3M6XG4gICAgICAgIC8vXG4gICAgICAgIC8vIDEuIERlZmluZSBjb21wYXJpc29uIG9mIEEgd2l0aCBCIHVzaW5nIChBIC0gQikgPj4+IDg6XG4gICAgICAgIC8vICAgICAgICAgIGlmIEEgPiBCLCB0aGVuIHJlc3VsdCBpcyBwb3NpdGl2ZSBpbnRlZ2VyXG4gICAgICAgIC8vICAgICAgICAgIGlmIEEgPD0gQiwgdGhlbiByZXN1bHQgaXMgMFxuICAgICAgICAvL1xuICAgICAgICAvLyAyLiBEZWZpbmUgc2VsZWN0aW9uIG9mIEMgb3IgMCB1c2luZyBiaXR3aXNlIEFORDogWCAmIEM6XG4gICAgICAgIC8vICAgICAgICAgIGlmIFggPT0gMCwgdGhlbiByZXN1bHQgaXMgMFxuICAgICAgICAvLyAgICAgICAgICBpZiBYICE9IDAsIHRoZW4gcmVzdWx0IGlzIENcbiAgICAgICAgLy9cbiAgICAgICAgLy8gMy4gU3RhcnQgd2l0aCB0aGUgc21hbGxlc3QgY29tcGFyaXNvbiAoYiA+PSAwKSwgd2hpY2ggaXMgYWx3YXlzXG4gICAgICAgIC8vICAgIHRydWUsIHNvIHNldCB0aGUgcmVzdWx0IHRvIHRoZSBzdGFydGluZyBBU0NJSSB2YWx1ZSAoNjUpLlxuICAgICAgICAvL1xuICAgICAgICAvLyA0LiBDb250aW51ZSBjb21wYXJpbmcgYiB0byBoaWdoZXIgQVNDSUkgdmFsdWVzLCBhbmQgc2VsZWN0aW5nXG4gICAgICAgIC8vICAgIHplcm8gaWYgY29tcGFyaXNvbiBpc24ndCB0cnVlLCBvdGhlcndpc2Ugc2VsZWN0aW5nIGEgdmFsdWVcbiAgICAgICAgLy8gICAgdG8gYWRkIHRvIHJlc3VsdCwgd2hpY2g6XG4gICAgICAgIC8vXG4gICAgICAgIC8vICAgICAgICAgIGEpIHVuZG9lcyB0aGUgcHJldmlvdXMgYWRkaXRpb25cbiAgICAgICAgLy8gICAgICAgICAgYikgcHJvdmlkZXMgbmV3IHZhbHVlIHRvIGFkZFxuICAgICAgICAvL1xuICAgICAgICBsZXQgcmVzdWx0ID0gYjtcbiAgICAgICAgLy8gYiA+PSAwXG4gICAgICAgIHJlc3VsdCArPSA2NTtcbiAgICAgICAgLy8gYiA+IDI1XG4gICAgICAgIHJlc3VsdCArPSAoKDI1IC0gYikgPj4+IDgpICYgKCgwIC0gNjUpIC0gMjYgKyA5Nyk7XG4gICAgICAgIC8vIGIgPiA1MVxuICAgICAgICByZXN1bHQgKz0gKCg1MSAtIGIpID4+PiA4KSAmICgoMjYgLSA5NykgLSA1MiArIDQ4KTtcbiAgICAgICAgLy8gYiA+IDYxXG4gICAgICAgIHJlc3VsdCArPSAoKDYxIC0gYikgPj4+IDgpICYgKCg1MiAtIDQ4KSAtIDYyICsgNDMpO1xuICAgICAgICAvLyBiID4gNjJcbiAgICAgICAgcmVzdWx0ICs9ICgoNjIgLSBiKSA+Pj4gOCkgJiAoKDYyIC0gNDMpIC0gNjMgKyA0Nyk7XG5cbiAgICAgICAgcmV0dXJuIFN0cmluZy5mcm9tQ2hhckNvZGUocmVzdWx0KTtcbiAgICB9XG5cbiAgICAvLyBEZWNvZGUgYSBjaGFyYWN0ZXIgY29kZSBpbnRvIGEgYnl0ZS5cbiAgICAvLyBNdXN0IHJldHVybiAyNTYgaWYgY2hhcmFjdGVyIGlzIG91dCBvZiBhbHBoYWJldCByYW5nZS5cbiAgICBwcm90ZWN0ZWQgX2RlY29kZUNoYXIoYzogbnVtYmVyKTogbnVtYmVyIHtcbiAgICAgICAgLy8gRGVjb2Rpbmcgd29ya3Mgc2ltaWxhciB0byBlbmNvZGluZzogdXNpbmcgdGhlIHNhbWUgY29tcGFyaXNvblxuICAgICAgICAvLyBmdW5jdGlvbiwgYnV0IG5vdyBpdCB3b3JrcyBvbiByYW5nZXM6IHJlc3VsdCBpcyBhbHdheXMgaW5jcmVtZW50ZWRcbiAgICAgICAgLy8gYnkgdmFsdWUsIGJ1dCB0aGlzIHZhbHVlIGJlY29tZXMgemVybyBpZiB0aGUgcmFuZ2UgaXMgbm90XG4gICAgICAgIC8vIHNhdGlzZmllZC5cbiAgICAgICAgLy9cbiAgICAgICAgLy8gRGVjb2Rpbmcgc3RhcnRzIHdpdGggaW52YWxpZCB2YWx1ZSwgMjU2LCB3aGljaCBpcyB0aGVuXG4gICAgICAgIC8vIHN1YnRyYWN0ZWQgd2hlbiB0aGUgcmFuZ2UgaXMgc2F0aXNmaWVkLiBJZiBub25lIG9mIHRoZSByYW5nZXNcbiAgICAgICAgLy8gYXBwbHksIHRoZSBmdW5jdGlvbiByZXR1cm5zIDI1Niwgd2hpY2ggaXMgdGhlbiBjaGVja2VkIGJ5XG4gICAgICAgIC8vIHRoZSBjYWxsZXIgdG8gdGhyb3cgZXJyb3IuXG4gICAgICAgIGxldCByZXN1bHQgPSBJTlZBTElEX0JZVEU7IC8vIHN0YXJ0IHdpdGggaW52YWxpZCBjaGFyYWN0ZXJcblxuICAgICAgICAvLyBjID09IDQzIChjID4gNDIgYW5kIGMgPCA0NClcbiAgICAgICAgcmVzdWx0ICs9ICgoKDQyIC0gYykgJiAoYyAtIDQ0KSkgPj4+IDgpICYgKC1JTlZBTElEX0JZVEUgKyBjIC0gNDMgKyA2Mik7XG4gICAgICAgIC8vIGMgPT0gNDcgKGMgPiA0NiBhbmQgYyA8IDQ4KVxuICAgICAgICByZXN1bHQgKz0gKCgoNDYgLSBjKSAmIChjIC0gNDgpKSA+Pj4gOCkgJiAoLUlOVkFMSURfQllURSArIGMgLSA0NyArIDYzKTtcbiAgICAgICAgLy8gYyA+IDQ3IGFuZCBjIDwgNThcbiAgICAgICAgcmVzdWx0ICs9ICgoKDQ3IC0gYykgJiAoYyAtIDU4KSkgPj4+IDgpICYgKC1JTlZBTElEX0JZVEUgKyBjIC0gNDggKyA1Mik7XG4gICAgICAgIC8vIGMgPiA2NCBhbmQgYyA8IDkxXG4gICAgICAgIHJlc3VsdCArPSAoKCg2NCAtIGMpICYgKGMgLSA5MSkpID4+PiA4KSAmICgtSU5WQUxJRF9CWVRFICsgYyAtIDY1ICsgMCk7XG4gICAgICAgIC8vIGMgPiA5NiBhbmQgYyA8IDEyM1xuICAgICAgICByZXN1bHQgKz0gKCgoOTYgLSBjKSAmIChjIC0gMTIzKSkgPj4+IDgpICYgKC1JTlZBTElEX0JZVEUgKyBjIC0gOTcgKyAyNik7XG5cbiAgICAgICAgcmV0dXJuIHJlc3VsdDtcbiAgICB9XG5cbiAgICBwcml2YXRlIF9nZXRQYWRkaW5nTGVuZ3RoKHM6IHN0cmluZyk6IG51bWJlciB7XG4gICAgICAgIGxldCBwYWRkaW5nTGVuZ3RoID0gMDtcbiAgICAgICAgaWYgKHRoaXMuX3BhZGRpbmdDaGFyYWN0ZXIpIHtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSBzLmxlbmd0aCAtIDE7IGkgPj0gMDsgaS0tKSB7XG4gICAgICAgICAgICAgICAgaWYgKHNbaV0gIT09IHRoaXMuX3BhZGRpbmdDaGFyYWN0ZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIHBhZGRpbmdMZW5ndGgrKztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChzLmxlbmd0aCA8IDQgfHwgcGFkZGluZ0xlbmd0aCA+IDIpIHtcbiAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoXCJCYXNlNjRDb2RlcjogaW5jb3JyZWN0IHBhZGRpbmdcIik7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHBhZGRpbmdMZW5ndGg7XG4gICAgfVxuXG59XG5cbmNvbnN0IHN0ZENvZGVyID0gbmV3IENvZGVyKCk7XG5cbmV4cG9ydCBmdW5jdGlvbiBlbmNvZGUoZGF0YTogVWludDhBcnJheSk6IHN0cmluZyB7XG4gICAgcmV0dXJuIHN0ZENvZGVyLmVuY29kZShkYXRhKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGRlY29kZShzOiBzdHJpbmcpOiBVaW50OEFycmF5IHtcbiAgICByZXR1cm4gc3RkQ29kZXIuZGVjb2RlKHMpO1xufVxuXG4vKipcbiAqIEltcGxlbWVudHMgVVJMLXNhZmUgQmFzZTY0IGVuY29kaW5nLlxuICogKFNhbWUgYXMgQmFzZTY0LCBidXQgJysnIGlzIHJlcGxhY2VkIHdpdGggJy0nLCBhbmQgJy8nIHdpdGggJ18nKS5cbiAqXG4gKiBPcGVyYXRlcyBpbiBjb25zdGFudCB0aW1lLlxuICovXG5leHBvcnQgY2xhc3MgVVJMU2FmZUNvZGVyIGV4dGVuZHMgQ29kZXIge1xuICAgIC8vIFVSTC1zYWZlIGVuY29kaW5nIGhhdmUgdGhlIGZvbGxvd2luZyBlbmNvZGVkL2RlY29kZWQgcmFuZ2VzOlxuICAgIC8vXG4gICAgLy8gQUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVogYWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXogMDEyMzQ1Njc4OSAgLSAgIF9cbiAgICAvLyBJbmRleDogICAwIC0gMjUgICAgICAgICAgICAgICAgICAgIDI2IC0gNTEgICAgICAgICAgICAgIDUyIC0gNjEgICA2MiAgNjNcbiAgICAvLyBBU0NJSTogIDY1IC0gOTAgICAgICAgICAgICAgICAgICAgIDk3IC0gMTIyICAgICAgICAgICAgIDQ4IC0gNTcgICA0NSAgOTVcbiAgICAvL1xuXG4gICAgcHJvdGVjdGVkIF9lbmNvZGVCeXRlKGI6IG51bWJlcik6IHN0cmluZyB7XG4gICAgICAgIGxldCByZXN1bHQgPSBiO1xuICAgICAgICAvLyBiID49IDBcbiAgICAgICAgcmVzdWx0ICs9IDY1O1xuICAgICAgICAvLyBiID4gMjVcbiAgICAgICAgcmVzdWx0ICs9ICgoMjUgLSBiKSA+Pj4gOCkgJiAoKDAgLSA2NSkgLSAyNiArIDk3KTtcbiAgICAgICAgLy8gYiA+IDUxXG4gICAgICAgIHJlc3VsdCArPSAoKDUxIC0gYikgPj4+IDgpICYgKCgyNiAtIDk3KSAtIDUyICsgNDgpO1xuICAgICAgICAvLyBiID4gNjFcbiAgICAgICAgcmVzdWx0ICs9ICgoNjEgLSBiKSA+Pj4gOCkgJiAoKDUyIC0gNDgpIC0gNjIgKyA0NSk7XG4gICAgICAgIC8vIGIgPiA2MlxuICAgICAgICByZXN1bHQgKz0gKCg2MiAtIGIpID4+PiA4KSAmICgoNjIgLSA0NSkgLSA2MyArIDk1KTtcblxuICAgICAgICByZXR1cm4gU3RyaW5nLmZyb21DaGFyQ29kZShyZXN1bHQpO1xuICAgIH1cblxuICAgIHByb3RlY3RlZCBfZGVjb2RlQ2hhcihjOiBudW1iZXIpOiBudW1iZXIge1xuICAgICAgICBsZXQgcmVzdWx0ID0gSU5WQUxJRF9CWVRFO1xuXG4gICAgICAgIC8vIGMgPT0gNDUgKGMgPiA0NCBhbmQgYyA8IDQ2KVxuICAgICAgICByZXN1bHQgKz0gKCgoNDQgLSBjKSAmIChjIC0gNDYpKSA+Pj4gOCkgJiAoLUlOVkFMSURfQllURSArIGMgLSA0NSArIDYyKTtcbiAgICAgICAgLy8gYyA9PSA5NSAoYyA+IDk0IGFuZCBjIDwgOTYpXG4gICAgICAgIHJlc3VsdCArPSAoKCg5NCAtIGMpICYgKGMgLSA5NikpID4+PiA4KSAmICgtSU5WQUxJRF9CWVRFICsgYyAtIDk1ICsgNjMpO1xuICAgICAgICAvLyBjID4gNDcgYW5kIGMgPCA1OFxuICAgICAgICByZXN1bHQgKz0gKCgoNDcgLSBjKSAmIChjIC0gNTgpKSA+Pj4gOCkgJiAoLUlOVkFMSURfQllURSArIGMgLSA0OCArIDUyKTtcbiAgICAgICAgLy8gYyA+IDY0IGFuZCBjIDwgOTFcbiAgICAgICAgcmVzdWx0ICs9ICgoKDY0IC0gYykgJiAoYyAtIDkxKSkgPj4+IDgpICYgKC1JTlZBTElEX0JZVEUgKyBjIC0gNjUgKyAwKTtcbiAgICAgICAgLy8gYyA+IDk2IGFuZCBjIDwgMTIzXG4gICAgICAgIHJlc3VsdCArPSAoKCg5NiAtIGMpICYgKGMgLSAxMjMpKSA+Pj4gOCkgJiAoLUlOVkFMSURfQllURSArIGMgLSA5NyArIDI2KTtcblxuICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgIH1cbn1cblxuY29uc3QgdXJsU2FmZUNvZGVyID0gbmV3IFVSTFNhZmVDb2RlcigpO1xuXG5leHBvcnQgZnVuY3Rpb24gZW5jb2RlVVJMU2FmZShkYXRhOiBVaW50OEFycmF5KTogc3RyaW5nIHtcbiAgICByZXR1cm4gdXJsU2FmZUNvZGVyLmVuY29kZShkYXRhKTtcbn1cblxuZXhwb3J0IGZ1bmN0aW9uIGRlY29kZVVSTFNhZmUoczogc3RyaW5nKTogVWludDhBcnJheSB7XG4gICAgcmV0dXJuIHVybFNhZmVDb2Rlci5kZWNvZGUocyk7XG59XG5cblxuZXhwb3J0IGNvbnN0IGVuY29kZWRMZW5ndGggPSAobGVuZ3RoOiBudW1iZXIpID0+XG4gICAgc3RkQ29kZXIuZW5jb2RlZExlbmd0aChsZW5ndGgpO1xuXG5leHBvcnQgY29uc3QgbWF4RGVjb2RlZExlbmd0aCA9IChsZW5ndGg6IG51bWJlcikgPT5cbiAgICBzdGRDb2Rlci5tYXhEZWNvZGVkTGVuZ3RoKGxlbmd0aCk7XG5cbmV4cG9ydCBjb25zdCBkZWNvZGVkTGVuZ3RoID0gKHM6IHN0cmluZykgPT5cbiAgICBzdGRDb2Rlci5kZWNvZGVkTGVuZ3RoKHMpO1xuIiwgIi8vIENvcHlyaWdodCAoQykgMjAxNiBEbWl0cnkgQ2hlc3RueWtoXG4vLyBNSVQgTGljZW5zZS4gU2VlIExJQ0VOU0UgZmlsZSBmb3IgZGV0YWlscy5cblxuLyoqXG4gKiBQYWNrYWdlIHV0ZjggaW1wbGVtZW50cyBVVEYtOCBlbmNvZGluZyBhbmQgZGVjb2RpbmcuXG4gKi9cblxuY29uc3QgSU5WQUxJRF9VVEYxNiA9IFwidXRmODogaW52YWxpZCBzdHJpbmdcIjtcbmNvbnN0IElOVkFMSURfVVRGOCA9IFwidXRmODogaW52YWxpZCBzb3VyY2UgZW5jb2RpbmdcIjtcblxuLyoqXG4gKiBFbmNvZGVzIHRoZSBnaXZlbiBzdHJpbmcgaW50byBVVEYtOCBieXRlIGFycmF5LlxuICogVGhyb3dzIGlmIHRoZSBzb3VyY2Ugc3RyaW5nIGhhcyBpbnZhbGlkIFVURi0xNiBlbmNvZGluZy5cbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGVuY29kZShzOiBzdHJpbmcpOiBVaW50OEFycmF5IHtcbiAgICAvLyBDYWxjdWxhdGUgcmVzdWx0IGxlbmd0aCBhbmQgYWxsb2NhdGUgb3V0cHV0IGFycmF5LlxuICAgIC8vIGVuY29kZWRMZW5ndGgoKSBhbHNvIHZhbGlkYXRlcyBzdHJpbmcgYW5kIHRocm93cyBlcnJvcnMsXG4gICAgLy8gc28gd2UgZG9uJ3QgbmVlZCByZXBlYXQgdmFsaWRhdGlvbiBoZXJlLlxuICAgIGNvbnN0IGFyciA9IG5ldyBVaW50OEFycmF5KGVuY29kZWRMZW5ndGgocykpO1xuXG4gICAgbGV0IHBvcyA9IDA7XG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgIGxldCBjID0gcy5jaGFyQ29kZUF0KGkpO1xuICAgICAgICBpZiAoYyA8IDB4ODApIHtcbiAgICAgICAgICAgIGFycltwb3MrK10gPSBjO1xuICAgICAgICB9IGVsc2UgaWYgKGMgPCAweDgwMCkge1xuICAgICAgICAgICAgYXJyW3BvcysrXSA9IDB4YzAgfCBjID4+IDY7XG4gICAgICAgICAgICBhcnJbcG9zKytdID0gMHg4MCB8IGMgJiAweDNmO1xuICAgICAgICB9IGVsc2UgaWYgKGMgPCAweGQ4MDApIHtcbiAgICAgICAgICAgIGFycltwb3MrK10gPSAweGUwIHwgYyA+PiAxMjtcbiAgICAgICAgICAgIGFycltwb3MrK10gPSAweDgwIHwgKGMgPj4gNikgJiAweDNmO1xuICAgICAgICAgICAgYXJyW3BvcysrXSA9IDB4ODAgfCBjICYgMHgzZjtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGkrKzsgLy8gZ2V0IG9uZSBtb3JlIGNoYXJhY3RlclxuICAgICAgICAgICAgYyA9IChjICYgMHgzZmYpIDw8IDEwO1xuICAgICAgICAgICAgYyB8PSBzLmNoYXJDb2RlQXQoaSkgJiAweDNmZjtcbiAgICAgICAgICAgIGMgKz0gMHgxMDAwMDtcblxuICAgICAgICAgICAgYXJyW3BvcysrXSA9IDB4ZjAgfCBjID4+IDE4O1xuICAgICAgICAgICAgYXJyW3BvcysrXSA9IDB4ODAgfCAoYyA+PiAxMikgJiAweDNmO1xuICAgICAgICAgICAgYXJyW3BvcysrXSA9IDB4ODAgfCAoYyA+PiA2KSAmIDB4M2Y7XG4gICAgICAgICAgICBhcnJbcG9zKytdID0gMHg4MCB8IGMgJiAweDNmO1xuICAgICAgICB9XG4gICAgfVxuICAgIHJldHVybiBhcnI7XG59XG5cbi8qKlxuICogUmV0dXJucyB0aGUgbnVtYmVyIG9mIGJ5dGVzIHJlcXVpcmVkIHRvIGVuY29kZSB0aGUgZ2l2ZW4gc3RyaW5nIGludG8gVVRGLTguXG4gKiBUaHJvd3MgaWYgdGhlIHNvdXJjZSBzdHJpbmcgaGFzIGludmFsaWQgVVRGLTE2IGVuY29kaW5nLlxuICovXG5leHBvcnQgZnVuY3Rpb24gZW5jb2RlZExlbmd0aChzOiBzdHJpbmcpOiBudW1iZXIge1xuICAgIGxldCByZXN1bHQgPSAwO1xuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgcy5sZW5ndGg7IGkrKykge1xuICAgICAgICBjb25zdCBjID0gcy5jaGFyQ29kZUF0KGkpO1xuICAgICAgICBpZiAoYyA8IDB4ODApIHtcbiAgICAgICAgICAgIHJlc3VsdCArPSAxO1xuICAgICAgICB9IGVsc2UgaWYgKGMgPCAweDgwMCkge1xuICAgICAgICAgICAgcmVzdWx0ICs9IDI7XG4gICAgICAgIH0gZWxzZSBpZiAoYyA8IDB4ZDgwMCkge1xuICAgICAgICAgICAgcmVzdWx0ICs9IDM7XG4gICAgICAgIH0gZWxzZSBpZiAoYyA8PSAweGRmZmYpIHtcbiAgICAgICAgICAgIGlmIChpID49IHMubGVuZ3RoIC0gMSkge1xuICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihJTlZBTElEX1VURjE2KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGkrKzsgLy8gXCJlYXRcIiBuZXh0IGNoYXJhY3RlclxuICAgICAgICAgICAgcmVzdWx0ICs9IDQ7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoSU5WQUxJRF9VVEYxNik7XG4gICAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIHJlc3VsdDtcbn1cblxuLyoqXG4gKiBEZWNvZGVzIHRoZSBnaXZlbiBieXRlIGFycmF5IGZyb20gVVRGLTggaW50byBhIHN0cmluZy5cbiAqIFRocm93cyBpZiBlbmNvZGluZyBpcyBpbnZhbGlkLlxuICovXG5leHBvcnQgZnVuY3Rpb24gZGVjb2RlKGFycjogVWludDhBcnJheSk6IHN0cmluZyB7XG4gICAgY29uc3QgY2hhcnM6IHN0cmluZ1tdID0gW107XG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBhcnIubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgbGV0IGIgPSBhcnJbaV07XG5cbiAgICAgICAgaWYgKGIgJiAweDgwKSB7XG4gICAgICAgICAgICBsZXQgbWluO1xuICAgICAgICAgICAgaWYgKGIgPCAweGUwKSB7XG4gICAgICAgICAgICAgICAgLy8gTmVlZCAxIG1vcmUgYnl0ZS5cbiAgICAgICAgICAgICAgICBpZiAoaSA+PSBhcnIubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihJTlZBTElEX1VURjgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBjb25zdCBuMSA9IGFyclsrK2ldO1xuICAgICAgICAgICAgICAgIGlmICgobjEgJiAweGMwKSAhPT0gMHg4MCkge1xuICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoSU5WQUxJRF9VVEY4KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgYiA9IChiICYgMHgxZikgPDwgNiB8IChuMSAmIDB4M2YpO1xuICAgICAgICAgICAgICAgIG1pbiA9IDB4ODA7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKGIgPCAweGYwKSB7XG4gICAgICAgICAgICAgICAgLy8gTmVlZCAyIG1vcmUgYnl0ZXMuXG4gICAgICAgICAgICAgICAgaWYgKGkgPj0gYXJyLmxlbmd0aCAtIDEpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKElOVkFMSURfVVRGOCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGNvbnN0IG4xID0gYXJyWysraV07XG4gICAgICAgICAgICAgICAgY29uc3QgbjIgPSBhcnJbKytpXTtcbiAgICAgICAgICAgICAgICBpZiAoKG4xICYgMHhjMCkgIT09IDB4ODAgfHwgKG4yICYgMHhjMCkgIT09IDB4ODApIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKElOVkFMSURfVVRGOCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGIgPSAoYiAmIDB4MGYpIDw8IDEyIHwgKG4xICYgMHgzZikgPDwgNiB8IChuMiAmIDB4M2YpO1xuICAgICAgICAgICAgICAgIG1pbiA9IDB4ODAwO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChiIDwgMHhmOCkge1xuICAgICAgICAgICAgICAgIC8vIE5lZWQgMyBtb3JlIGJ5dGVzLlxuICAgICAgICAgICAgICAgIGlmIChpID49IGFyci5sZW5ndGggLSAyKSB7XG4gICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihJTlZBTElEX1VURjgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBjb25zdCBuMSA9IGFyclsrK2ldO1xuICAgICAgICAgICAgICAgIGNvbnN0IG4yID0gYXJyWysraV07XG4gICAgICAgICAgICAgICAgY29uc3QgbjMgPSBhcnJbKytpXTtcbiAgICAgICAgICAgICAgICBpZiAoKG4xICYgMHhjMCkgIT09IDB4ODAgfHwgKG4yICYgMHhjMCkgIT09IDB4ODAgfHwgKG4zICYgMHhjMCkgIT09IDB4ODApIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKElOVkFMSURfVVRGOCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGIgPSAoYiAmIDB4MGYpIDw8IDE4IHwgKG4xICYgMHgzZikgPDwgMTIgfCAobjIgJiAweDNmKSA8PCA2IHwgKG4zICYgMHgzZik7XG4gICAgICAgICAgICAgICAgbWluID0gMHgxMDAwMDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKElOVkFMSURfVVRGOCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChiIDwgbWluIHx8IChiID49IDB4ZDgwMCAmJiBiIDw9IDB4ZGZmZikpIHtcbiAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoSU5WQUxJRF9VVEY4KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGIgPj0gMHgxMDAwMCkge1xuICAgICAgICAgICAgICAgIC8vIFN1cnJvZ2F0ZSBwYWlyLlxuICAgICAgICAgICAgICAgIGlmIChiID4gMHgxMGZmZmYpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKElOVkFMSURfVVRGOCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGIgLT0gMHgxMDAwMDtcbiAgICAgICAgICAgICAgICBjaGFycy5wdXNoKFN0cmluZy5mcm9tQ2hhckNvZGUoMHhkODAwIHwgKGIgPj4gMTApKSk7XG4gICAgICAgICAgICAgICAgYiA9IDB4ZGMwMCB8IChiICYgMHgzZmYpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgY2hhcnMucHVzaChTdHJpbmcuZnJvbUNoYXJDb2RlKGIpKTtcbiAgICB9XG4gICAgcmV0dXJuIGNoYXJzLmpvaW4oXCJcIik7XG59XG4iLCAiLy8gcmVxdWlyZWQgc28gd2UgZG9uJ3QgaGF2ZSB0byBkbyByZXF1aXJlKCdwdXNoZXInKS5kZWZhdWx0IGV0Yy5cbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9wdXNoZXInKS5kZWZhdWx0O1xuIiwgImltcG9ydCBTY3JpcHRSZWNlaXZlciBmcm9tICcuL3NjcmlwdF9yZWNlaXZlcic7XG5cbi8qKiBCdWlsZHMgcmVjZWl2ZXJzIGZvciBKU09OUCBhbmQgU2NyaXB0IHJlcXVlc3RzLlxuICpcbiAqIEVhY2ggcmVjZWl2ZXIgaXMgYW4gb2JqZWN0IHdpdGggZm9sbG93aW5nIGZpZWxkczpcbiAqIC0gbnVtYmVyIC0gdW5pcXVlIChmb3IgdGhlIGZhY3RvcnkgaW5zdGFuY2UpLCBudW1lcmljYWwgaWQgb2YgdGhlIHJlY2VpdmVyXG4gKiAtIGlkIC0gYSBzdHJpbmcgSUQgdGhhdCBjYW4gYmUgdXNlZCBpbiBET00gYXR0cmlidXRlc1xuICogLSBuYW1lIC0gbmFtZSBvZiB0aGUgZnVuY3Rpb24gdHJpZ2dlcmluZyB0aGUgcmVjZWl2ZXJcbiAqIC0gY2FsbGJhY2sgLSBjYWxsYmFjayBmdW5jdGlvblxuICpcbiAqIFJlY2VpdmVycyBhcmUgdHJpZ2dlcmVkIG9ubHkgb25jZSwgb24gdGhlIGZpcnN0IGNhbGxiYWNrIGNhbGwuXG4gKlxuICogUmVjZWl2ZXJzIGNhbiBiZSBjYWxsZWQgYnkgdGhlaXIgbmFtZSBvciBieSBhY2Nlc3NpbmcgZmFjdG9yeSBvYmplY3RcbiAqIGJ5IHRoZSBudW1iZXIga2V5LlxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBwcmVmaXggdGhlIHByZWZpeCB1c2VkIGluIGlkc1xuICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgdGhlIG5hbWUgb2YgdGhlIG9iamVjdFxuICovXG5leHBvcnQgY2xhc3MgU2NyaXB0UmVjZWl2ZXJGYWN0b3J5IHtcbiAgbGFzdElkOiBudW1iZXI7XG4gIHByZWZpeDogc3RyaW5nO1xuICBuYW1lOiBzdHJpbmc7XG5cbiAgY29uc3RydWN0b3IocHJlZml4OiBzdHJpbmcsIG5hbWU6IHN0cmluZykge1xuICAgIHRoaXMubGFzdElkID0gMDtcbiAgICB0aGlzLnByZWZpeCA9IHByZWZpeDtcbiAgICB0aGlzLm5hbWUgPSBuYW1lO1xuICB9XG5cbiAgY3JlYXRlKGNhbGxiYWNrOiBGdW5jdGlvbik6IFNjcmlwdFJlY2VpdmVyIHtcbiAgICB0aGlzLmxhc3RJZCsrO1xuXG4gICAgdmFyIG51bWJlciA9IHRoaXMubGFzdElkO1xuICAgIHZhciBpZCA9IHRoaXMucHJlZml4ICsgbnVtYmVyO1xuICAgIHZhciBuYW1lID0gdGhpcy5uYW1lICsgJ1snICsgbnVtYmVyICsgJ10nO1xuXG4gICAgdmFyIGNhbGxlZCA9IGZhbHNlO1xuICAgIHZhciBjYWxsYmFja1dyYXBwZXIgPSBmdW5jdGlvbigpIHtcbiAgICAgIGlmICghY2FsbGVkKSB7XG4gICAgICAgIGNhbGxiYWNrLmFwcGx5KG51bGwsIGFyZ3VtZW50cyk7XG4gICAgICAgIGNhbGxlZCA9IHRydWU7XG4gICAgICB9XG4gICAgfTtcblxuICAgIHRoaXNbbnVtYmVyXSA9IGNhbGxiYWNrV3JhcHBlcjtcbiAgICByZXR1cm4geyBudW1iZXI6IG51bWJlciwgaWQ6IGlkLCBuYW1lOiBuYW1lLCBjYWxsYmFjazogY2FsbGJhY2tXcmFwcGVyIH07XG4gIH1cblxuICByZW1vdmUocmVjZWl2ZXI6IFNjcmlwdFJlY2VpdmVyKSB7XG4gICAgZGVsZXRlIHRoaXNbcmVjZWl2ZXIubnVtYmVyXTtcbiAgfVxufVxuXG5leHBvcnQgdmFyIFNjcmlwdFJlY2VpdmVycyA9IG5ldyBTY3JpcHRSZWNlaXZlckZhY3RvcnkoXG4gICdfcHVzaGVyX3NjcmlwdF8nLFxuICAnUHVzaGVyLlNjcmlwdFJlY2VpdmVycydcbik7XG4iLCAiaW1wb3J0IHtcbiAgQ2hhbm5lbEF1dGhvcml6YXRpb25PcHRpb25zLFxuICBVc2VyQXV0aGVudGljYXRpb25PcHRpb25zXG59IGZyb20gJy4vYXV0aC9vcHRpb25zJztcbmltcG9ydCB7IEF1dGhUcmFuc3BvcnQgfSBmcm9tICcuL2NvbmZpZyc7XG5cbmV4cG9ydCBpbnRlcmZhY2UgRGVmYXVsdENvbmZpZyB7XG4gIFZFUlNJT046IHN0cmluZztcbiAgUFJPVE9DT0w6IG51bWJlcjtcbiAgd3NQb3J0OiBudW1iZXI7XG4gIHdzc1BvcnQ6IG51bWJlcjtcbiAgd3NQYXRoOiBzdHJpbmc7XG4gIGh0dHBIb3N0OiBzdHJpbmc7XG4gIGh0dHBQb3J0OiBudW1iZXI7XG4gIGh0dHBzUG9ydDogbnVtYmVyO1xuICBodHRwUGF0aDogc3RyaW5nO1xuICBzdGF0c19ob3N0OiBzdHJpbmc7XG4gIGF1dGhFbmRwb2ludDogc3RyaW5nO1xuICBhdXRoVHJhbnNwb3J0OiBBdXRoVHJhbnNwb3J0O1xuICBhY3Rpdml0eVRpbWVvdXQ6IG51bWJlcjtcbiAgcG9uZ1RpbWVvdXQ6IG51bWJlcjtcbiAgdW5hdmFpbGFibGVUaW1lb3V0OiBudW1iZXI7XG4gIGNsdXN0ZXI6IHN0cmluZztcbiAgdXNlckF1dGhlbnRpY2F0aW9uOiBVc2VyQXV0aGVudGljYXRpb25PcHRpb25zO1xuICBjaGFubmVsQXV0aG9yaXphdGlvbjogQ2hhbm5lbEF1dGhvcml6YXRpb25PcHRpb25zO1xuXG4gIGNkbl9odHRwPzogc3RyaW5nO1xuICBjZG5faHR0cHM/OiBzdHJpbmc7XG4gIGRlcGVuZGVuY3lfc3VmZml4Pzogc3RyaW5nO1xufVxuXG52YXIgRGVmYXVsdHM6IERlZmF1bHRDb25maWcgPSB7XG4gIFZFUlNJT046IFZFUlNJT04sXG4gIFBST1RPQ09MOiA3LFxuXG4gIHdzUG9ydDogODAsXG4gIHdzc1BvcnQ6IDQ0MyxcbiAgd3NQYXRoOiAnJyxcbiAgLy8gREVQUkVDQVRFRDogU29ja0pTIGZhbGxiYWNrIHBhcmFtZXRlcnNcbiAgaHR0cEhvc3Q6ICdzb2NranMucHVzaGVyLmNvbScsXG4gIGh0dHBQb3J0OiA4MCxcbiAgaHR0cHNQb3J0OiA0NDMsXG4gIGh0dHBQYXRoOiAnL3B1c2hlcicsXG4gIC8vIERFUFJFQ0FURUQ6IFN0YXRzXG4gIHN0YXRzX2hvc3Q6ICdzdGF0cy5wdXNoZXIuY29tJyxcbiAgLy8gREVQUkVDQVRFRDogT3RoZXIgc2V0dGluZ3NcbiAgYXV0aEVuZHBvaW50OiAnL3B1c2hlci9hdXRoJyxcbiAgYXV0aFRyYW5zcG9ydDogJ2FqYXgnLFxuICBhY3Rpdml0eVRpbWVvdXQ6IDEyMDAwMCxcbiAgcG9uZ1RpbWVvdXQ6IDMwMDAwLFxuICB1bmF2YWlsYWJsZVRpbWVvdXQ6IDEwMDAwLFxuICBjbHVzdGVyOiAnbXQxJyxcbiAgdXNlckF1dGhlbnRpY2F0aW9uOiB7XG4gICAgZW5kcG9pbnQ6ICcvcHVzaGVyL3VzZXItYXV0aCcsXG4gICAgdHJhbnNwb3J0OiAnYWpheCdcbiAgfSxcbiAgY2hhbm5lbEF1dGhvcml6YXRpb246IHtcbiAgICBlbmRwb2ludDogJy9wdXNoZXIvYXV0aCcsXG4gICAgdHJhbnNwb3J0OiAnYWpheCdcbiAgfSxcblxuICAvLyBDRE4gY29uZmlndXJhdGlvblxuICBjZG5faHR0cDogQ0ROX0hUVFAsXG4gIGNkbl9odHRwczogQ0ROX0hUVFBTLFxuICBkZXBlbmRlbmN5X3N1ZmZpeDogREVQRU5ERU5DWV9TVUZGSVhcbn07XG5cbmV4cG9ydCBkZWZhdWx0IERlZmF1bHRzO1xuIiwgImltcG9ydCB7XG4gIFNjcmlwdFJlY2VpdmVycyxcbiAgU2NyaXB0UmVjZWl2ZXJGYWN0b3J5XG59IGZyb20gJy4vc2NyaXB0X3JlY2VpdmVyX2ZhY3RvcnknO1xuaW1wb3J0IFJ1bnRpbWUgZnJvbSAncnVudGltZSc7XG5pbXBvcnQgU2NyaXB0UmVxdWVzdCBmcm9tICcuL3NjcmlwdF9yZXF1ZXN0JztcblxuLyoqIEhhbmRsZXMgbG9hZGluZyBkZXBlbmRlbmN5IGZpbGVzLlxuICpcbiAqIERlcGVuZGVuY3kgbG9hZGVycyBkb24ndCByZW1lbWJlciB3aGV0aGVyIGEgcmVzb3VyY2UgaGFzIGJlZW4gbG9hZGVkIG9yXG4gKiBub3QuIEl0IGlzIGNhbGxlcidzIHJlc3BvbnNpYmlsaXR5IHRvIG1ha2Ugc3VyZSB0aGUgcmVzb3VyY2UgaXMgbm90IGxvYWRlZFxuICogdHdpY2UuIFRoaXMgaXMgYmVjYXVzZSBpdCdzIGltcG9zc2libGUgdG8gZGV0ZWN0IHJlc291cmNlIGxvYWRpbmcgc3RhdHVzXG4gKiB3aXRob3V0IGtub3dpbmcgaXRzIGNvbnRlbnQuXG4gKlxuICogT3B0aW9uczpcbiAqIC0gY2RuX2h0dHAgLSB1cmwgdG8gSFRUUCBDTkRcbiAqIC0gY2RuX2h0dHBzIC0gdXJsIHRvIEhUVFBTIENETlxuICogLSB2ZXJzaW9uIC0gdmVyc2lvbiBvZiBwdXNoZXItanNcbiAqIC0gc3VmZml4IC0gc3VmZml4IGFwcGVuZGVkIHRvIGFsbCBuYW1lcyBvZiBkZXBlbmRlbmN5IGZpbGVzXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRGVwZW5kZW5jeUxvYWRlciB7XG4gIG9wdGlvbnM6IGFueTtcbiAgcmVjZWl2ZXJzOiBTY3JpcHRSZWNlaXZlckZhY3Rvcnk7XG4gIGxvYWRpbmc6IGFueTtcblxuICBjb25zdHJ1Y3RvcihvcHRpb25zOiBhbnkpIHtcbiAgICB0aGlzLm9wdGlvbnMgPSBvcHRpb25zO1xuICAgIHRoaXMucmVjZWl2ZXJzID0gb3B0aW9ucy5yZWNlaXZlcnMgfHwgU2NyaXB0UmVjZWl2ZXJzO1xuICAgIHRoaXMubG9hZGluZyA9IHt9O1xuICB9XG5cbiAgLyoqIExvYWRzIHRoZSBkZXBlbmRlbmN5IGZyb20gQ0ROLlxuICAgKlxuICAgKiBAcGFyYW0gIHtTdHJpbmd9IG5hbWVcbiAgICogQHBhcmFtICB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAqL1xuICBsb2FkKG5hbWU6IHN0cmluZywgb3B0aW9uczogYW55LCBjYWxsYmFjazogRnVuY3Rpb24pIHtcbiAgICB2YXIgc2VsZiA9IHRoaXM7XG5cbiAgICBpZiAoc2VsZi5sb2FkaW5nW25hbWVdICYmIHNlbGYubG9hZGluZ1tuYW1lXS5sZW5ndGggPiAwKSB7XG4gICAgICBzZWxmLmxvYWRpbmdbbmFtZV0ucHVzaChjYWxsYmFjayk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHNlbGYubG9hZGluZ1tuYW1lXSA9IFtjYWxsYmFja107XG5cbiAgICAgIHZhciByZXF1ZXN0ID0gUnVudGltZS5jcmVhdGVTY3JpcHRSZXF1ZXN0KHNlbGYuZ2V0UGF0aChuYW1lLCBvcHRpb25zKSk7XG4gICAgICB2YXIgcmVjZWl2ZXIgPSBzZWxmLnJlY2VpdmVycy5jcmVhdGUoZnVuY3Rpb24oZXJyb3IpIHtcbiAgICAgICAgc2VsZi5yZWNlaXZlcnMucmVtb3ZlKHJlY2VpdmVyKTtcblxuICAgICAgICBpZiAoc2VsZi5sb2FkaW5nW25hbWVdKSB7XG4gICAgICAgICAgdmFyIGNhbGxiYWNrcyA9IHNlbGYubG9hZGluZ1tuYW1lXTtcbiAgICAgICAgICBkZWxldGUgc2VsZi5sb2FkaW5nW25hbWVdO1xuXG4gICAgICAgICAgdmFyIHN1Y2Nlc3NDYWxsYmFjayA9IGZ1bmN0aW9uKHdhc1N1Y2Nlc3NmdWwpIHtcbiAgICAgICAgICAgIGlmICghd2FzU3VjY2Vzc2Z1bCkge1xuICAgICAgICAgICAgICByZXF1ZXN0LmNsZWFudXAoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9O1xuICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgY2FsbGJhY2tzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICBjYWxsYmFja3NbaV0oZXJyb3IsIHN1Y2Nlc3NDYWxsYmFjayk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9KTtcbiAgICAgIHJlcXVlc3Quc2VuZChyZWNlaXZlcik7XG4gICAgfVxuICB9XG5cbiAgLyoqIFJldHVybnMgYSByb290IFVSTCBmb3IgcHVzaGVyLWpzIENETi5cbiAgICpcbiAgICogQHJldHVybnMge1N0cmluZ31cbiAgICovXG4gIGdldFJvb3Qob3B0aW9uczogYW55KTogc3RyaW5nIHtcbiAgICB2YXIgY2RuO1xuICAgIHZhciBwcm90b2NvbCA9IFJ1bnRpbWUuZ2V0RG9jdW1lbnQoKS5sb2NhdGlvbi5wcm90b2NvbDtcbiAgICBpZiAoKG9wdGlvbnMgJiYgb3B0aW9ucy51c2VUTFMpIHx8IHByb3RvY29sID09PSAnaHR0cHM6Jykge1xuICAgICAgY2RuID0gdGhpcy5vcHRpb25zLmNkbl9odHRwcztcbiAgICB9IGVsc2Uge1xuICAgICAgY2RuID0gdGhpcy5vcHRpb25zLmNkbl9odHRwO1xuICAgIH1cbiAgICAvLyBtYWtlIHN1cmUgdGhlcmUgYXJlIG5vIGRvdWJsZSBzbGFzaGVzXG4gICAgcmV0dXJuIGNkbi5yZXBsYWNlKC9cXC8qJC8sICcnKSArICcvJyArIHRoaXMub3B0aW9ucy52ZXJzaW9uO1xuICB9XG5cbiAgLyoqIFJldHVybnMgYSBmdWxsIHBhdGggdG8gYSBkZXBlbmRlbmN5IGZpbGUuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAqL1xuICBnZXRQYXRoKG5hbWU6IHN0cmluZywgb3B0aW9uczogYW55KTogc3RyaW5nIHtcbiAgICByZXR1cm4gdGhpcy5nZXRSb290KG9wdGlvbnMpICsgJy8nICsgbmFtZSArIHRoaXMub3B0aW9ucy5zdWZmaXggKyAnLmpzJztcbiAgfVxufVxuIiwgImltcG9ydCB7IFNjcmlwdFJlY2VpdmVyRmFjdG9yeSB9IGZyb20gJy4vc2NyaXB0X3JlY2VpdmVyX2ZhY3RvcnknO1xuaW1wb3J0IERlZmF1bHRzIGZyb20gJ2NvcmUvZGVmYXVsdHMnO1xuaW1wb3J0IERlcGVuZGVuY3lMb2FkZXIgZnJvbSAnLi9kZXBlbmRlbmN5X2xvYWRlcic7XG5cbmV4cG9ydCB2YXIgRGVwZW5kZW5jaWVzUmVjZWl2ZXJzID0gbmV3IFNjcmlwdFJlY2VpdmVyRmFjdG9yeShcbiAgJ19wdXNoZXJfZGVwZW5kZW5jaWVzJyxcbiAgJ1B1c2hlci5EZXBlbmRlbmNpZXNSZWNlaXZlcnMnXG4pO1xuXG5leHBvcnQgdmFyIERlcGVuZGVuY2llcyA9IG5ldyBEZXBlbmRlbmN5TG9hZGVyKHtcbiAgY2RuX2h0dHA6IERlZmF1bHRzLmNkbl9odHRwLFxuICBjZG5faHR0cHM6IERlZmF1bHRzLmNkbl9odHRwcyxcbiAgdmVyc2lvbjogRGVmYXVsdHMuVkVSU0lPTixcbiAgc3VmZml4OiBEZWZhdWx0cy5kZXBlbmRlbmN5X3N1ZmZpeCxcbiAgcmVjZWl2ZXJzOiBEZXBlbmRlbmNpZXNSZWNlaXZlcnNcbn0pO1xuIiwgIi8qKlxuICogQSBwbGFjZSB0byBzdG9yZSBoZWxwIFVSTHMgZm9yIGVycm9yIG1lc3NhZ2VzIGV0Y1xuICovXG5cbmNvbnN0IHVybFN0b3JlID0ge1xuICBiYXNlVXJsOiAnaHR0cHM6Ly9wdXNoZXIuY29tJyxcbiAgdXJsczoge1xuICAgIGF1dGhlbnRpY2F0aW9uRW5kcG9pbnQ6IHtcbiAgICAgIHBhdGg6ICcvZG9jcy9jaGFubmVscy9zZXJ2ZXJfYXBpL2F1dGhlbnRpY2F0aW5nX3VzZXJzJ1xuICAgIH0sXG4gICAgYXV0aG9yaXphdGlvbkVuZHBvaW50OiB7XG4gICAgICBwYXRoOiAnL2RvY3MvY2hhbm5lbHMvc2VydmVyX2FwaS9hdXRob3JpemluZy11c2Vycy8nXG4gICAgfSxcbiAgICBqYXZhc2NyaXB0UXVpY2tTdGFydDoge1xuICAgICAgcGF0aDogJy9kb2NzL2phdmFzY3JpcHRfcXVpY2tfc3RhcnQnXG4gICAgfSxcbiAgICB0cmlnZ2VyaW5nQ2xpZW50RXZlbnRzOiB7XG4gICAgICBwYXRoOiAnL2RvY3MvY2xpZW50X2FwaV9ndWlkZS9jbGllbnRfZXZlbnRzI3RyaWdnZXItZXZlbnRzJ1xuICAgIH0sXG4gICAgZW5jcnlwdGVkQ2hhbm5lbFN1cHBvcnQ6IHtcbiAgICAgIGZ1bGxVcmw6XG4gICAgICAgICdodHRwczovL2dpdGh1Yi5jb20vcHVzaGVyL3B1c2hlci1qcy90cmVlL2NjNDkxMDE1MzcxYTRiZGU1NzQzZDFjODdhMGZiYWMwZmViNTMxOTUjZW5jcnlwdGVkLWNoYW5uZWwtc3VwcG9ydCdcbiAgICB9XG4gIH1cbn07XG5cbi8qKiBCdWlsZHMgYSBjb25zaXN0ZW50IHN0cmluZyB3aXRoIGxpbmtzIHRvIHB1c2hlciBkb2N1bWVudGF0aW9uXG4gKlxuICogQHBhcmFtIHtzdHJpbmd9IGtleSAtIHJlbGV2YW50IGtleSBpbiB0aGUgdXJsX3N0b3JlLnVybHMgb2JqZWN0XG4gKiBAcmV0dXJuIHtzdHJpbmd9IHN1ZmZpeCBzdHJpbmcgdG8gYXBwZW5kIHRvIGxvZyBtZXNzYWdlXG4gKi9cbmNvbnN0IGJ1aWxkTG9nU3VmZml4ID0gZnVuY3Rpb24oa2V5OiBzdHJpbmcpOiBzdHJpbmcge1xuICBjb25zdCB1cmxQcmVmaXggPSAnU2VlOic7XG4gIGNvbnN0IHVybE9iaiA9IHVybFN0b3JlLnVybHNba2V5XTtcbiAgaWYgKCF1cmxPYmopIHJldHVybiAnJztcblxuICBsZXQgdXJsO1xuICBpZiAodXJsT2JqLmZ1bGxVcmwpIHtcbiAgICB1cmwgPSB1cmxPYmouZnVsbFVybDtcbiAgfSBlbHNlIGlmICh1cmxPYmoucGF0aCkge1xuICAgIHVybCA9IHVybFN0b3JlLmJhc2VVcmwgKyB1cmxPYmoucGF0aDtcbiAgfVxuXG4gIGlmICghdXJsKSByZXR1cm4gJyc7XG4gIHJldHVybiBgJHt1cmxQcmVmaXh9ICR7dXJsfWA7XG59O1xuXG5leHBvcnQgZGVmYXVsdCB7IGJ1aWxkTG9nU3VmZml4IH07XG4iLCAiZXhwb3J0IGVudW0gQXV0aFJlcXVlc3RUeXBlIHtcbiAgVXNlckF1dGhlbnRpY2F0aW9uID0gJ3VzZXItYXV0aGVudGljYXRpb24nLFxuICBDaGFubmVsQXV0aG9yaXphdGlvbiA9ICdjaGFubmVsLWF1dGhvcml6YXRpb24nXG59XG5cbmV4cG9ydCBpbnRlcmZhY2UgQ2hhbm5lbEF1dGhvcml6YXRpb25EYXRhIHtcbiAgYXV0aDogc3RyaW5nO1xuICBjaGFubmVsX2RhdGE/OiBzdHJpbmc7XG4gIHNoYXJlZF9zZWNyZXQ/OiBzdHJpbmc7XG59XG5cbmV4cG9ydCB0eXBlIENoYW5uZWxBdXRob3JpemF0aW9uQ2FsbGJhY2sgPSAoXG4gIGVycm9yOiBFcnJvciB8IG51bGwsXG4gIGF1dGhEYXRhOiBDaGFubmVsQXV0aG9yaXphdGlvbkRhdGEgfCBudWxsXG4pID0+IHZvaWQ7XG5cbmV4cG9ydCBpbnRlcmZhY2UgQ2hhbm5lbEF1dGhvcml6YXRpb25SZXF1ZXN0UGFyYW1zIHtcbiAgc29ja2V0SWQ6IHN0cmluZztcbiAgY2hhbm5lbE5hbWU6IHN0cmluZztcbn1cblxuZXhwb3J0IGludGVyZmFjZSBDaGFubmVsQXV0aG9yaXphdGlvbkhhbmRsZXIge1xuICAoXG4gICAgcGFyYW1zOiBDaGFubmVsQXV0aG9yaXphdGlvblJlcXVlc3RQYXJhbXMsXG4gICAgY2FsbGJhY2s6IENoYW5uZWxBdXRob3JpemF0aW9uQ2FsbGJhY2tcbiAgKTogdm9pZDtcbn1cblxuZXhwb3J0IGludGVyZmFjZSBVc2VyQXV0aGVudGljYXRpb25EYXRhIHtcbiAgYXV0aDogc3RyaW5nO1xuICB1c2VyX2RhdGE6IHN0cmluZztcbn1cblxuZXhwb3J0IHR5cGUgVXNlckF1dGhlbnRpY2F0aW9uQ2FsbGJhY2sgPSAoXG4gIGVycm9yOiBFcnJvciB8IG51bGwsXG4gIGF1dGhEYXRhOiBVc2VyQXV0aGVudGljYXRpb25EYXRhIHwgbnVsbFxuKSA9PiB2b2lkO1xuXG5leHBvcnQgaW50ZXJmYWNlIFVzZXJBdXRoZW50aWNhdGlvblJlcXVlc3RQYXJhbXMge1xuICBzb2NrZXRJZDogc3RyaW5nO1xufVxuXG5leHBvcnQgaW50ZXJmYWNlIFVzZXJBdXRoZW50aWNhdGlvbkhhbmRsZXIge1xuICAoXG4gICAgcGFyYW1zOiBVc2VyQXV0aGVudGljYXRpb25SZXF1ZXN0UGFyYW1zLFxuICAgIGNhbGxiYWNrOiBVc2VyQXV0aGVudGljYXRpb25DYWxsYmFja1xuICApOiB2b2lkO1xufVxuXG5leHBvcnQgdHlwZSBBdXRoVHJhbnNwb3J0Q2FsbGJhY2sgPVxuICB8IENoYW5uZWxBdXRob3JpemF0aW9uQ2FsbGJhY2tcbiAgfCBVc2VyQXV0aGVudGljYXRpb25DYWxsYmFjaztcblxuZXhwb3J0IGludGVyZmFjZSBBdXRoT3B0aW9uc1Q8QXV0aEhhbmRsZXI+IHtcbiAgdHJhbnNwb3J0OiAnYWpheCcgfCAnanNvbnAnO1xuICBlbmRwb2ludDogc3RyaW5nO1xuICBwYXJhbXM/OiBhbnk7XG4gIGhlYWRlcnM/OiBhbnk7XG4gIHBhcmFtc1Byb3ZpZGVyPzogKCkgPT4gYW55O1xuICBoZWFkZXJzUHJvdmlkZXI/OiAoKSA9PiBhbnk7XG4gIGN1c3RvbUhhbmRsZXI/OiBBdXRoSGFuZGxlcjtcbn1cblxuZXhwb3J0IGRlY2xhcmUgdHlwZSBVc2VyQXV0aGVudGljYXRpb25PcHRpb25zID0gQXV0aE9wdGlvbnNUPFxuICBVc2VyQXV0aGVudGljYXRpb25IYW5kbGVyXG4+O1xuZXhwb3J0IGRlY2xhcmUgdHlwZSBDaGFubmVsQXV0aG9yaXphdGlvbk9wdGlvbnMgPSBBdXRoT3B0aW9uc1Q8XG4gIENoYW5uZWxBdXRob3JpemF0aW9uSGFuZGxlclxuPjtcblxuZXhwb3J0IGludGVyZmFjZSBJbnRlcm5hbEF1dGhPcHRpb25zIHtcbiAgdHJhbnNwb3J0OiAnYWpheCcgfCAnanNvbnAnO1xuICBlbmRwb2ludDogc3RyaW5nO1xuICBwYXJhbXM/OiBhbnk7XG4gIGhlYWRlcnM/OiBhbnk7XG4gIHBhcmFtc1Byb3ZpZGVyPzogKCkgPT4gYW55O1xuICBoZWFkZXJzUHJvdmlkZXI/OiAoKSA9PiBhbnk7XG59XG4iLCAiLyoqIEVycm9yIGNsYXNzZXMgdXNlZCB0aHJvdWdob3V0IHRoZSBsaWJyYXJ5LiAqL1xuLy8gaHR0cHM6Ly9naXRodWIuY29tL01pY3Jvc29mdC9UeXBlU2NyaXB0LXdpa2kvYmxvYi9tYXN0ZXIvQnJlYWtpbmctQ2hhbmdlcy5tZCNleHRlbmRpbmctYnVpbHQtaW5zLWxpa2UtZXJyb3ItYXJyYXktYW5kLW1hcC1tYXktbm8tbG9uZ2VyLXdvcmtcbmV4cG9ydCBjbGFzcyBCYWRFdmVudE5hbWUgZXh0ZW5kcyBFcnJvciB7XG4gIGNvbnN0cnVjdG9yKG1zZz86IHN0cmluZykge1xuICAgIHN1cGVyKG1zZyk7XG5cbiAgICBPYmplY3Quc2V0UHJvdG90eXBlT2YodGhpcywgbmV3LnRhcmdldC5wcm90b3R5cGUpO1xuICB9XG59XG5cbmV4cG9ydCBjbGFzcyBCYWRDaGFubmVsTmFtZSBleHRlbmRzIEVycm9yIHtcbiAgY29uc3RydWN0b3IobXNnPzogc3RyaW5nKSB7XG4gICAgc3VwZXIobXNnKTtcblxuICAgIE9iamVjdC5zZXRQcm90b3R5cGVPZih0aGlzLCBuZXcudGFyZ2V0LnByb3RvdHlwZSk7XG4gIH1cbn1cblxuZXhwb3J0IGNsYXNzIFJlcXVlc3RUaW1lZE91dCBleHRlbmRzIEVycm9yIHtcbiAgY29uc3RydWN0b3IobXNnPzogc3RyaW5nKSB7XG4gICAgc3VwZXIobXNnKTtcblxuICAgIE9iamVjdC5zZXRQcm90b3R5cGVPZih0aGlzLCBuZXcudGFyZ2V0LnByb3RvdHlwZSk7XG4gIH1cbn1cbmV4cG9ydCBjbGFzcyBUcmFuc3BvcnRQcmlvcml0eVRvb0xvdyBleHRlbmRzIEVycm9yIHtcbiAgY29uc3RydWN0b3IobXNnPzogc3RyaW5nKSB7XG4gICAgc3VwZXIobXNnKTtcblxuICAgIE9iamVjdC5zZXRQcm90b3R5cGVPZih0aGlzLCBuZXcudGFyZ2V0LnByb3RvdHlwZSk7XG4gIH1cbn1cbmV4cG9ydCBjbGFzcyBUcmFuc3BvcnRDbG9zZWQgZXh0ZW5kcyBFcnJvciB7XG4gIGNvbnN0cnVjdG9yKG1zZz86IHN0cmluZykge1xuICAgIHN1cGVyKG1zZyk7XG5cbiAgICBPYmplY3Quc2V0UHJvdG90eXBlT2YodGhpcywgbmV3LnRhcmdldC5wcm90b3R5cGUpO1xuICB9XG59XG5leHBvcnQgY2xhc3MgVW5zdXBwb3J0ZWRGZWF0dXJlIGV4dGVuZHMgRXJyb3Ige1xuICBjb25zdHJ1Y3Rvcihtc2c/OiBzdHJpbmcpIHtcbiAgICBzdXBlcihtc2cpO1xuXG4gICAgT2JqZWN0LnNldFByb3RvdHlwZU9mKHRoaXMsIG5ldy50YXJnZXQucHJvdG90eXBlKTtcbiAgfVxufVxuZXhwb3J0IGNsYXNzIFVuc3VwcG9ydGVkVHJhbnNwb3J0IGV4dGVuZHMgRXJyb3Ige1xuICBjb25zdHJ1Y3Rvcihtc2c/OiBzdHJpbmcpIHtcbiAgICBzdXBlcihtc2cpO1xuXG4gICAgT2JqZWN0LnNldFByb3RvdHlwZU9mKHRoaXMsIG5ldy50YXJnZXQucHJvdG90eXBlKTtcbiAgfVxufVxuZXhwb3J0IGNsYXNzIFVuc3VwcG9ydGVkU3RyYXRlZ3kgZXh0ZW5kcyBFcnJvciB7XG4gIGNvbnN0cnVjdG9yKG1zZz86IHN0cmluZykge1xuICAgIHN1cGVyKG1zZyk7XG5cbiAgICBPYmplY3Quc2V0UHJvdG90eXBlT2YodGhpcywgbmV3LnRhcmdldC5wcm90b3R5cGUpO1xuICB9XG59XG5leHBvcnQgY2xhc3MgSFRUUEF1dGhFcnJvciBleHRlbmRzIEVycm9yIHtcbiAgc3RhdHVzOiBudW1iZXI7XG4gIGNvbnN0cnVjdG9yKHN0YXR1czogbnVtYmVyLCBtc2c/OiBzdHJpbmcpIHtcbiAgICBzdXBlcihtc2cpO1xuICAgIHRoaXMuc3RhdHVzID0gc3RhdHVzO1xuXG4gICAgT2JqZWN0LnNldFByb3RvdHlwZU9mKHRoaXMsIG5ldy50YXJnZXQucHJvdG90eXBlKTtcbiAgfVxufVxuIiwgImltcG9ydCBUaW1lbGluZVNlbmRlciBmcm9tICdjb3JlL3RpbWVsaW5lL3RpbWVsaW5lX3NlbmRlcic7XG5pbXBvcnQgKiBhcyBDb2xsZWN0aW9ucyBmcm9tICdjb3JlL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBVdGlsIGZyb20gJ2NvcmUvdXRpbCc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcbmltcG9ydCB7IEF1dGhUcmFuc3BvcnQgfSBmcm9tICdjb3JlL2F1dGgvYXV0aF90cmFuc3BvcnRzJztcbmltcG9ydCBBYnN0cmFjdFJ1bnRpbWUgZnJvbSAncnVudGltZXMvaW50ZXJmYWNlJztcbmltcG9ydCBVcmxTdG9yZSBmcm9tICdjb3JlL3V0aWxzL3VybF9zdG9yZSc7XG5pbXBvcnQge1xuICBBdXRoUmVxdWVzdFR5cGUsXG4gIEF1dGhUcmFuc3BvcnRDYWxsYmFjayxcbiAgSW50ZXJuYWxBdXRoT3B0aW9uc1xufSBmcm9tICdjb3JlL2F1dGgvb3B0aW9ucyc7XG5pbXBvcnQgeyBIVFRQQXV0aEVycm9yIH0gZnJvbSAnY29yZS9lcnJvcnMnO1xuXG5jb25zdCBhamF4OiBBdXRoVHJhbnNwb3J0ID0gZnVuY3Rpb24oXG4gIGNvbnRleHQ6IEFic3RyYWN0UnVudGltZSxcbiAgcXVlcnk6IHN0cmluZyxcbiAgYXV0aE9wdGlvbnM6IEludGVybmFsQXV0aE9wdGlvbnMsXG4gIGF1dGhSZXF1ZXN0VHlwZTogQXV0aFJlcXVlc3RUeXBlLFxuICBjYWxsYmFjazogQXV0aFRyYW5zcG9ydENhbGxiYWNrXG4pIHtcbiAgY29uc3QgeGhyID0gUnVudGltZS5jcmVhdGVYSFIoKTtcbiAgeGhyLm9wZW4oJ1BPU1QnLCBhdXRoT3B0aW9ucy5lbmRwb2ludCwgdHJ1ZSk7XG5cbiAgLy8gYWRkIHJlcXVlc3QgaGVhZGVyc1xuICB4aHIuc2V0UmVxdWVzdEhlYWRlcignQ29udGVudC1UeXBlJywgJ2FwcGxpY2F0aW9uL3gtd3d3LWZvcm0tdXJsZW5jb2RlZCcpO1xuICBmb3IgKHZhciBoZWFkZXJOYW1lIGluIGF1dGhPcHRpb25zLmhlYWRlcnMpIHtcbiAgICB4aHIuc2V0UmVxdWVzdEhlYWRlcihoZWFkZXJOYW1lLCBhdXRoT3B0aW9ucy5oZWFkZXJzW2hlYWRlck5hbWVdKTtcbiAgfVxuICBpZiAoYXV0aE9wdGlvbnMuaGVhZGVyc1Byb3ZpZGVyICE9IG51bGwpIHtcbiAgICBsZXQgZHluYW1pY0hlYWRlcnMgPSBhdXRoT3B0aW9ucy5oZWFkZXJzUHJvdmlkZXIoKTtcbiAgICBmb3IgKHZhciBoZWFkZXJOYW1lIGluIGR5bmFtaWNIZWFkZXJzKSB7XG4gICAgICB4aHIuc2V0UmVxdWVzdEhlYWRlcihoZWFkZXJOYW1lLCBkeW5hbWljSGVhZGVyc1toZWFkZXJOYW1lXSk7XG4gICAgfVxuICB9XG5cbiAgeGhyLm9ucmVhZHlzdGF0ZWNoYW5nZSA9IGZ1bmN0aW9uKCkge1xuICAgIGlmICh4aHIucmVhZHlTdGF0ZSA9PT0gNCkge1xuICAgICAgaWYgKHhoci5zdGF0dXMgPT09IDIwMCkge1xuICAgICAgICBsZXQgZGF0YTtcbiAgICAgICAgbGV0IHBhcnNlZCA9IGZhbHNlO1xuXG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgZGF0YSA9IEpTT04ucGFyc2UoeGhyLnJlc3BvbnNlVGV4dCk7XG4gICAgICAgICAgcGFyc2VkID0gdHJ1ZTtcbiAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgIGNhbGxiYWNrKFxuICAgICAgICAgICAgbmV3IEhUVFBBdXRoRXJyb3IoXG4gICAgICAgICAgICAgIDIwMCxcbiAgICAgICAgICAgICAgYEpTT04gcmV0dXJuZWQgZnJvbSAke2F1dGhSZXF1ZXN0VHlwZS50b1N0cmluZygpfSBlbmRwb2ludCB3YXMgaW52YWxpZCwgeWV0IHN0YXR1cyBjb2RlIHdhcyAyMDAuIERhdGEgd2FzOiAke1xuICAgICAgICAgICAgICAgIHhoci5yZXNwb25zZVRleHRcbiAgICAgICAgICAgICAgfWBcbiAgICAgICAgICAgICksXG4gICAgICAgICAgICBudWxsXG4gICAgICAgICAgKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChwYXJzZWQpIHtcbiAgICAgICAgICAvLyBwcmV2ZW50cyBkb3VibGUgZXhlY3V0aW9uLlxuICAgICAgICAgIGNhbGxiYWNrKG51bGwsIGRhdGEpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBsZXQgc3VmZml4ID0gJyc7XG4gICAgICAgIHN3aXRjaCAoYXV0aFJlcXVlc3RUeXBlKSB7XG4gICAgICAgICAgY2FzZSBBdXRoUmVxdWVzdFR5cGUuVXNlckF1dGhlbnRpY2F0aW9uOlxuICAgICAgICAgICAgc3VmZml4ID0gVXJsU3RvcmUuYnVpbGRMb2dTdWZmaXgoJ2F1dGhlbnRpY2F0aW9uRW5kcG9pbnQnKTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgQXV0aFJlcXVlc3RUeXBlLkNoYW5uZWxBdXRob3JpemF0aW9uOlxuICAgICAgICAgICAgc3VmZml4ID0gYENsaWVudHMgbXVzdCBiZSBhdXRob3JpemVkIHRvIGpvaW4gcHJpdmF0ZSBvciBwcmVzZW5jZSBjaGFubmVscy4gJHtVcmxTdG9yZS5idWlsZExvZ1N1ZmZpeChcbiAgICAgICAgICAgICAgJ2F1dGhvcml6YXRpb25FbmRwb2ludCdcbiAgICAgICAgICAgICl9YDtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgICAgIGNhbGxiYWNrKFxuICAgICAgICAgIG5ldyBIVFRQQXV0aEVycm9yKFxuICAgICAgICAgICAgeGhyLnN0YXR1cyxcbiAgICAgICAgICAgIGBVbmFibGUgdG8gcmV0cmlldmUgYXV0aCBzdHJpbmcgZnJvbSAke2F1dGhSZXF1ZXN0VHlwZS50b1N0cmluZygpfSBlbmRwb2ludCAtIGAgK1xuICAgICAgICAgICAgICBgcmVjZWl2ZWQgc3RhdHVzOiAke3hoci5zdGF0dXN9IGZyb20gJHthdXRoT3B0aW9ucy5lbmRwb2ludH0uICR7c3VmZml4fWBcbiAgICAgICAgICApLFxuICAgICAgICAgIG51bGxcbiAgICAgICAgKTtcbiAgICAgIH1cbiAgICB9XG4gIH07XG5cbiAgeGhyLnNlbmQocXVlcnkpO1xuICByZXR1cm4geGhyO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgYWpheDtcbiIsICJleHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBlbmNvZGUoczogYW55KTogc3RyaW5nIHtcbiAgcmV0dXJuIGJ0b2EodXRvYihzKSk7XG59XG5cbnZhciBmcm9tQ2hhckNvZGUgPSBTdHJpbmcuZnJvbUNoYXJDb2RlO1xuXG52YXIgYjY0Y2hhcnMgPVxuICAnQUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVphYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODkrLyc7XG52YXIgYjY0dGFiID0ge307XG5cbmZvciAodmFyIGkgPSAwLCBsID0gYjY0Y2hhcnMubGVuZ3RoOyBpIDwgbDsgaSsrKSB7XG4gIGI2NHRhYltiNjRjaGFycy5jaGFyQXQoaSldID0gaTtcbn1cblxudmFyIGNiX3V0b2IgPSBmdW5jdGlvbihjKSB7XG4gIHZhciBjYyA9IGMuY2hhckNvZGVBdCgwKTtcbiAgcmV0dXJuIGNjIDwgMHg4MFxuICAgID8gY1xuICAgIDogY2MgPCAweDgwMFxuICAgID8gZnJvbUNoYXJDb2RlKDB4YzAgfCAoY2MgPj4+IDYpKSArIGZyb21DaGFyQ29kZSgweDgwIHwgKGNjICYgMHgzZikpXG4gICAgOiBmcm9tQ2hhckNvZGUoMHhlMCB8ICgoY2MgPj4+IDEyKSAmIDB4MGYpKSArXG4gICAgICBmcm9tQ2hhckNvZGUoMHg4MCB8ICgoY2MgPj4+IDYpICYgMHgzZikpICtcbiAgICAgIGZyb21DaGFyQ29kZSgweDgwIHwgKGNjICYgMHgzZikpO1xufTtcblxudmFyIHV0b2IgPSBmdW5jdGlvbih1KSB7XG4gIHJldHVybiB1LnJlcGxhY2UoL1teXFx4MDAtXFx4N0ZdL2csIGNiX3V0b2IpO1xufTtcblxudmFyIGNiX2VuY29kZSA9IGZ1bmN0aW9uKGNjYykge1xuICB2YXIgcGFkbGVuID0gWzAsIDIsIDFdW2NjYy5sZW5ndGggJSAzXTtcbiAgdmFyIG9yZCA9XG4gICAgKGNjYy5jaGFyQ29kZUF0KDApIDw8IDE2KSB8XG4gICAgKChjY2MubGVuZ3RoID4gMSA/IGNjYy5jaGFyQ29kZUF0KDEpIDogMCkgPDwgOCkgfFxuICAgIChjY2MubGVuZ3RoID4gMiA/IGNjYy5jaGFyQ29kZUF0KDIpIDogMCk7XG4gIHZhciBjaGFycyA9IFtcbiAgICBiNjRjaGFycy5jaGFyQXQob3JkID4+PiAxOCksXG4gICAgYjY0Y2hhcnMuY2hhckF0KChvcmQgPj4+IDEyKSAmIDYzKSxcbiAgICBwYWRsZW4gPj0gMiA/ICc9JyA6IGI2NGNoYXJzLmNoYXJBdCgob3JkID4+PiA2KSAmIDYzKSxcbiAgICBwYWRsZW4gPj0gMSA/ICc9JyA6IGI2NGNoYXJzLmNoYXJBdChvcmQgJiA2MylcbiAgXTtcbiAgcmV0dXJuIGNoYXJzLmpvaW4oJycpO1xufTtcblxudmFyIGJ0b2EgPVxuICBnbG9iYWwuYnRvYSB8fFxuICBmdW5jdGlvbihiKSB7XG4gICAgcmV0dXJuIGIucmVwbGFjZSgvW1xcc1xcU117MSwzfS9nLCBjYl9lbmNvZGUpO1xuICB9O1xuIiwgImltcG9ydCBUaW1lZENhbGxiYWNrIGZyb20gJy4vdGltZWRfY2FsbGJhY2snO1xuaW1wb3J0IHsgRGVsYXksIFNjaGVkdWxlciwgQ2FuY2VsbGVyIH0gZnJvbSAnLi9zY2hlZHVsaW5nJztcblxuYWJzdHJhY3QgY2xhc3MgVGltZXIge1xuICBwcm90ZWN0ZWQgY2xlYXI6IENhbmNlbGxlcjtcbiAgcHJvdGVjdGVkIHRpbWVyOiBudW1iZXIgfCB2b2lkO1xuXG4gIGNvbnN0cnVjdG9yKFxuICAgIHNldDogU2NoZWR1bGVyLFxuICAgIGNsZWFyOiBDYW5jZWxsZXIsXG4gICAgZGVsYXk6IERlbGF5LFxuICAgIGNhbGxiYWNrOiBUaW1lZENhbGxiYWNrXG4gICkge1xuICAgIHRoaXMuY2xlYXIgPSBjbGVhcjtcbiAgICB0aGlzLnRpbWVyID0gc2V0KCgpID0+IHtcbiAgICAgIGlmICh0aGlzLnRpbWVyKSB7XG4gICAgICAgIHRoaXMudGltZXIgPSBjYWxsYmFjayh0aGlzLnRpbWVyKTtcbiAgICAgIH1cbiAgICB9LCBkZWxheSk7XG4gIH1cblxuICAvKiogUmV0dXJucyB3aGV0aGVyIHRoZSB0aW1lciBpcyBzdGlsbCBydW5uaW5nLlxuICAgKlxuICAgKiBAcmV0dXJuIHtCb29sZWFufVxuICAgKi9cbiAgaXNSdW5uaW5nKCk6IGJvb2xlYW4ge1xuICAgIHJldHVybiB0aGlzLnRpbWVyICE9PSBudWxsO1xuICB9XG5cbiAgLyoqIEFib3J0cyBhIHRpbWVyIHdoZW4gaXQncyBydW5uaW5nLiAqL1xuICBlbnN1cmVBYm9ydGVkKCkge1xuICAgIGlmICh0aGlzLnRpbWVyKSB7XG4gICAgICB0aGlzLmNsZWFyKHRoaXMudGltZXIpO1xuICAgICAgdGhpcy50aW1lciA9IG51bGw7XG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRpbWVyO1xuIiwgImltcG9ydCBUaW1lciBmcm9tICcuL2Fic3RyYWN0X3RpbWVyJztcbmltcG9ydCBUaW1lZENhbGxiYWNrIGZyb20gJy4vdGltZWRfY2FsbGJhY2snO1xuaW1wb3J0IHsgRGVsYXkgfSBmcm9tICcuL3NjaGVkdWxpbmcnO1xuXG4vLyBXZSBuZWVkIHRvIGJpbmQgY2xlYXIgZnVuY3Rpb25zIHRoaXMgd2F5IHRvIGF2b2lkIGV4Y2VwdGlvbnMgb24gSUU4XG5mdW5jdGlvbiBjbGVhclRpbWVvdXQodGltZXIpIHtcbiAgZ2xvYmFsLmNsZWFyVGltZW91dCh0aW1lcik7XG59XG5mdW5jdGlvbiBjbGVhckludGVydmFsKHRpbWVyKSB7XG4gIGdsb2JhbC5jbGVhckludGVydmFsKHRpbWVyKTtcbn1cblxuLyoqIENyb3NzLWJyb3dzZXIgY29tcGF0aWJsZSBvbmUtb2ZmIHRpbWVyIGFic3RyYWN0aW9uLlxuICpcbiAqIEBwYXJhbSB7TnVtYmVyfSBkZWxheVxuICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAqL1xuZXhwb3J0IGNsYXNzIE9uZU9mZlRpbWVyIGV4dGVuZHMgVGltZXIge1xuICBjb25zdHJ1Y3RvcihkZWxheTogRGVsYXksIGNhbGxiYWNrOiBUaW1lZENhbGxiYWNrKSB7XG4gICAgc3VwZXIoc2V0VGltZW91dCwgY2xlYXJUaW1lb3V0LCBkZWxheSwgZnVuY3Rpb24odGltZXIpIHtcbiAgICAgIGNhbGxiYWNrKCk7XG4gICAgICByZXR1cm4gbnVsbDtcbiAgICB9KTtcbiAgfVxufVxuXG4vKiogQ3Jvc3MtYnJvd3NlciBjb21wYXRpYmxlIHBlcmlvZGljIHRpbWVyIGFic3RyYWN0aW9uLlxuICpcbiAqIEBwYXJhbSB7TnVtYmVyfSBkZWxheVxuICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAqL1xuZXhwb3J0IGNsYXNzIFBlcmlvZGljVGltZXIgZXh0ZW5kcyBUaW1lciB7XG4gIGNvbnN0cnVjdG9yKGRlbGF5OiBEZWxheSwgY2FsbGJhY2s6IFRpbWVkQ2FsbGJhY2spIHtcbiAgICBzdXBlcihzZXRJbnRlcnZhbCwgY2xlYXJJbnRlcnZhbCwgZGVsYXksIGZ1bmN0aW9uKHRpbWVyKSB7XG4gICAgICBjYWxsYmFjaygpO1xuICAgICAgcmV0dXJuIHRpbWVyO1xuICAgIH0pO1xuICB9XG59XG4iLCAiaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnLi91dGlscy9jb2xsZWN0aW9ucyc7XG5pbXBvcnQgVGltZWRDYWxsYmFjayBmcm9tICcuL3V0aWxzL3RpbWVycy90aW1lZF9jYWxsYmFjayc7XG5pbXBvcnQgeyBPbmVPZmZUaW1lciwgUGVyaW9kaWNUaW1lciB9IGZyb20gJy4vdXRpbHMvdGltZXJzJztcblxudmFyIFV0aWwgPSB7XG4gIG5vdygpOiBudW1iZXIge1xuICAgIGlmIChEYXRlLm5vdykge1xuICAgICAgcmV0dXJuIERhdGUubm93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiBuZXcgRGF0ZSgpLnZhbHVlT2YoKTtcbiAgICB9XG4gIH0sXG5cbiAgZGVmZXIoY2FsbGJhY2s6IFRpbWVkQ2FsbGJhY2spOiBPbmVPZmZUaW1lciB7XG4gICAgcmV0dXJuIG5ldyBPbmVPZmZUaW1lcigwLCBjYWxsYmFjayk7XG4gIH0sXG5cbiAgLyoqIEJ1aWxkcyBhIGZ1bmN0aW9uIHRoYXQgd2lsbCBwcm94eSBhIG1ldGhvZCBjYWxsIHRvIGl0cyBmaXJzdCBhcmd1bWVudC5cbiAgICpcbiAgICogQWxsb3dzIHBhcnRpYWwgYXBwbGljYXRpb24gb2YgYXJndW1lbnRzLCBzbyBhZGRpdGlvbmFsIGFyZ3VtZW50cyBhcmVcbiAgICogcHJlcGVuZGVkIHRvIHRoZSBhcmd1bWVudCBsaXN0LlxuICAgKlxuICAgKiBAcGFyYW0gIHtTdHJpbmd9IG5hbWUgbWV0aG9kIG5hbWVcbiAgICogQHJldHVybiB7RnVuY3Rpb259IHByb3h5IGZ1bmN0aW9uXG4gICAqL1xuICBtZXRob2QobmFtZTogc3RyaW5nLCAuLi5hcmdzOiBhbnlbXSk6IEZ1bmN0aW9uIHtcbiAgICB2YXIgYm91bmRBcmd1bWVudHMgPSBBcnJheS5wcm90b3R5cGUuc2xpY2UuY2FsbChhcmd1bWVudHMsIDEpO1xuICAgIHJldHVybiBmdW5jdGlvbihvYmplY3QpIHtcbiAgICAgIHJldHVybiBvYmplY3RbbmFtZV0uYXBwbHkob2JqZWN0LCBib3VuZEFyZ3VtZW50cy5jb25jYXQoYXJndW1lbnRzKSk7XG4gICAgfTtcbiAgfVxufTtcblxuZXhwb3J0IGRlZmF1bHQgVXRpbDtcbiIsICJpbXBvcnQgYmFzZTY0ZW5jb2RlIGZyb20gJy4uL2Jhc2U2NCc7XG5pbXBvcnQgVXRpbCBmcm9tICcuLi91dGlsJztcblxuLyoqIE1lcmdlcyBtdWx0aXBsZSBvYmplY3RzIGludG8gdGhlIHRhcmdldCBhcmd1bWVudC5cbiAqXG4gKiBGb3IgcHJvcGVydGllcyB0aGF0IGFyZSBwbGFpbiBPYmplY3RzLCBwZXJmb3JtcyBhIGRlZXAtbWVyZ2UuIEZvciB0aGVcbiAqIHJlc3QgaXQganVzdCBjb3BpZXMgdGhlIHZhbHVlIG9mIHRoZSBwcm9wZXJ0eS5cbiAqXG4gKiBUbyBleHRlbmQgcHJvdG90eXBlcyB1c2UgaXQgYXMgZm9sbG93aW5nOlxuICogICBQdXNoZXIuVXRpbC5leHRlbmQoVGFyZ2V0LnByb3RvdHlwZSwgQmFzZS5wcm90b3R5cGUpXG4gKlxuICogWW91IGNhbiBhbHNvIHVzZSBpdCB0byBtZXJnZSBvYmplY3RzIHdpdGhvdXQgYWx0ZXJpbmcgdGhlbTpcbiAqICAgUHVzaGVyLlV0aWwuZXh0ZW5kKHt9LCBvYmplY3QxLCBvYmplY3QyKVxuICpcbiAqIEBwYXJhbSAge09iamVjdH0gdGFyZ2V0XG4gKiBAcmV0dXJuIHtPYmplY3R9IHRoZSB0YXJnZXQgYXJndW1lbnRcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGV4dGVuZDxUPih0YXJnZXQ6IGFueSwgLi4uc291cmNlczogYW55W10pOiBUIHtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBzb3VyY2VzLmxlbmd0aDsgaSsrKSB7XG4gICAgdmFyIGV4dGVuc2lvbnMgPSBzb3VyY2VzW2ldO1xuICAgIGZvciAodmFyIHByb3BlcnR5IGluIGV4dGVuc2lvbnMpIHtcbiAgICAgIGlmIChcbiAgICAgICAgZXh0ZW5zaW9uc1twcm9wZXJ0eV0gJiZcbiAgICAgICAgZXh0ZW5zaW9uc1twcm9wZXJ0eV0uY29uc3RydWN0b3IgJiZcbiAgICAgICAgZXh0ZW5zaW9uc1twcm9wZXJ0eV0uY29uc3RydWN0b3IgPT09IE9iamVjdFxuICAgICAgKSB7XG4gICAgICAgIHRhcmdldFtwcm9wZXJ0eV0gPSBleHRlbmQodGFyZ2V0W3Byb3BlcnR5XSB8fCB7fSwgZXh0ZW5zaW9uc1twcm9wZXJ0eV0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGFyZ2V0W3Byb3BlcnR5XSA9IGV4dGVuc2lvbnNbcHJvcGVydHldO1xuICAgICAgfVxuICAgIH1cbiAgfVxuICByZXR1cm4gdGFyZ2V0O1xufVxuXG5leHBvcnQgZnVuY3Rpb24gc3RyaW5naWZ5KCk6IHN0cmluZyB7XG4gIHZhciBtID0gWydQdXNoZXInXTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIHtcbiAgICBpZiAodHlwZW9mIGFyZ3VtZW50c1tpXSA9PT0gJ3N0cmluZycpIHtcbiAgICAgIG0ucHVzaChhcmd1bWVudHNbaV0pO1xuICAgIH0gZWxzZSB7XG4gICAgICBtLnB1c2goc2FmZUpTT05TdHJpbmdpZnkoYXJndW1lbnRzW2ldKSk7XG4gICAgfVxuICB9XG4gIHJldHVybiBtLmpvaW4oJyA6ICcpO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gYXJyYXlJbmRleE9mKGFycmF5OiBhbnlbXSwgaXRlbTogYW55KTogbnVtYmVyIHtcbiAgLy8gTVNJRSBkb2Vzbid0IGhhdmUgYXJyYXkuaW5kZXhPZlxuICB2YXIgbmF0aXZlSW5kZXhPZiA9IEFycmF5LnByb3RvdHlwZS5pbmRleE9mO1xuICBpZiAoYXJyYXkgPT09IG51bGwpIHtcbiAgICByZXR1cm4gLTE7XG4gIH1cbiAgaWYgKG5hdGl2ZUluZGV4T2YgJiYgYXJyYXkuaW5kZXhPZiA9PT0gbmF0aXZlSW5kZXhPZikge1xuICAgIHJldHVybiBhcnJheS5pbmRleE9mKGl0ZW0pO1xuICB9XG4gIGZvciAodmFyIGkgPSAwLCBsID0gYXJyYXkubGVuZ3RoOyBpIDwgbDsgaSsrKSB7XG4gICAgaWYgKGFycmF5W2ldID09PSBpdGVtKSB7XG4gICAgICByZXR1cm4gaTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIC0xO1xufVxuXG4vKiogQXBwbGllcyBhIGZ1bmN0aW9uIGYgdG8gYWxsIHByb3BlcnRpZXMgb2YgYW4gb2JqZWN0LlxuICpcbiAqIEZ1bmN0aW9uIGYgZ2V0cyAzIGFyZ3VtZW50cyBwYXNzZWQ6XG4gKiAtIGVsZW1lbnQgZnJvbSB0aGUgb2JqZWN0XG4gKiAtIGtleSBvZiB0aGUgZWxlbWVudFxuICogLSByZWZlcmVuY2UgdG8gdGhlIG9iamVjdFxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSBvYmplY3RcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGZcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIG9iamVjdEFwcGx5KG9iamVjdDogYW55LCBmOiBGdW5jdGlvbikge1xuICBmb3IgKHZhciBrZXkgaW4gb2JqZWN0KSB7XG4gICAgaWYgKE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIGtleSkpIHtcbiAgICAgIGYob2JqZWN0W2tleV0sIGtleSwgb2JqZWN0KTtcbiAgICB9XG4gIH1cbn1cblxuLyoqIFJldHVybiBhIGxpc3Qgb2Ygb2JqZWN0cyBvd24gcHJvZXJ0eSBrZXlzXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdFxuICogQHJldHVybnMge0FycmF5fVxuICovXG5leHBvcnQgZnVuY3Rpb24ga2V5cyhvYmplY3Q6IGFueSk6IHN0cmluZ1tdIHtcbiAgdmFyIGtleXMgPSBbXTtcbiAgb2JqZWN0QXBwbHkob2JqZWN0LCBmdW5jdGlvbihfLCBrZXkpIHtcbiAgICBrZXlzLnB1c2goa2V5KTtcbiAgfSk7XG4gIHJldHVybiBrZXlzO1xufVxuXG4vKiogUmV0dXJuIGEgbGlzdCBvZiBvYmplY3QncyBvd24gcHJvcGVydHkgdmFsdWVzXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IG9iamVjdFxuICogQHJldHVybnMge0FycmF5fVxuICovXG5leHBvcnQgZnVuY3Rpb24gdmFsdWVzKG9iamVjdDogYW55KTogYW55W10ge1xuICB2YXIgdmFsdWVzID0gW107XG4gIG9iamVjdEFwcGx5KG9iamVjdCwgZnVuY3Rpb24odmFsdWUpIHtcbiAgICB2YWx1ZXMucHVzaCh2YWx1ZSk7XG4gIH0pO1xuICByZXR1cm4gdmFsdWVzO1xufVxuXG4vKiogQXBwbGllcyBhIGZ1bmN0aW9uIGYgdG8gYWxsIGVsZW1lbnRzIG9mIGFuIGFycmF5LlxuICpcbiAqIEZ1bmN0aW9uIGYgZ2V0cyAzIGFyZ3VtZW50cyBwYXNzZWQ6XG4gKiAtIGVsZW1lbnQgZnJvbSB0aGUgYXJyYXlcbiAqIC0gaW5kZXggb2YgdGhlIGVsZW1lbnRcbiAqIC0gcmVmZXJlbmNlIHRvIHRoZSBhcnJheVxuICpcbiAqIEBwYXJhbSB7QXJyYXl9IGFycmF5XG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBmXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBhcHBseShhcnJheTogYW55W10sIGY6IEZ1bmN0aW9uLCBjb250ZXh0PzogYW55KSB7XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgYXJyYXkubGVuZ3RoOyBpKyspIHtcbiAgICBmLmNhbGwoY29udGV4dCB8fCBnbG9iYWwsIGFycmF5W2ldLCBpLCBhcnJheSk7XG4gIH1cbn1cblxuLyoqIE1hcHMgYWxsIGVsZW1lbnRzIG9mIHRoZSBhcnJheSBhbmQgcmV0dXJucyB0aGUgcmVzdWx0LlxuICpcbiAqIEZ1bmN0aW9uIGYgZ2V0cyA0IGFyZ3VtZW50cyBwYXNzZWQ6XG4gKiAtIGVsZW1lbnQgZnJvbSB0aGUgYXJyYXlcbiAqIC0gaW5kZXggb2YgdGhlIGVsZW1lbnRcbiAqIC0gcmVmZXJlbmNlIHRvIHRoZSBzb3VyY2UgYXJyYXlcbiAqIC0gcmVmZXJlbmNlIHRvIHRoZSBkZXN0aW5hdGlvbiBhcnJheVxuICpcbiAqIEBwYXJhbSB7QXJyYXl9IGFycmF5XG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBmXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBtYXAoYXJyYXk6IGFueVtdLCBmOiBGdW5jdGlvbik6IGFueVtdIHtcbiAgdmFyIHJlc3VsdCA9IFtdO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IGFycmF5Lmxlbmd0aDsgaSsrKSB7XG4gICAgcmVzdWx0LnB1c2goZihhcnJheVtpXSwgaSwgYXJyYXksIHJlc3VsdCkpO1xuICB9XG4gIHJldHVybiByZXN1bHQ7XG59XG5cbi8qKiBNYXBzIGFsbCBlbGVtZW50cyBvZiB0aGUgb2JqZWN0IGFuZCByZXR1cm5zIHRoZSByZXN1bHQuXG4gKlxuICogRnVuY3Rpb24gZiBnZXRzIDQgYXJndW1lbnRzIHBhc3NlZDpcbiAqIC0gZWxlbWVudCBmcm9tIHRoZSBvYmplY3RcbiAqIC0ga2V5IG9mIHRoZSBlbGVtZW50XG4gKiAtIHJlZmVyZW5jZSB0byB0aGUgc291cmNlIG9iamVjdFxuICogLSByZWZlcmVuY2UgdG8gdGhlIGRlc3RpbmF0aW9uIG9iamVjdFxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSBvYmplY3RcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGZcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIG1hcE9iamVjdChvYmplY3Q6IGFueSwgZjogRnVuY3Rpb24pOiBhbnkge1xuICB2YXIgcmVzdWx0ID0ge307XG4gIG9iamVjdEFwcGx5KG9iamVjdCwgZnVuY3Rpb24odmFsdWUsIGtleSkge1xuICAgIHJlc3VsdFtrZXldID0gZih2YWx1ZSk7XG4gIH0pO1xuICByZXR1cm4gcmVzdWx0O1xufVxuXG4vKiogRmlsdGVycyBlbGVtZW50cyBvZiB0aGUgYXJyYXkgdXNpbmcgYSB0ZXN0IGZ1bmN0aW9uLlxuICpcbiAqIEZ1bmN0aW9uIHRlc3QgZ2V0cyA0IGFyZ3VtZW50cyBwYXNzZWQ6XG4gKiAtIGVsZW1lbnQgZnJvbSB0aGUgYXJyYXlcbiAqIC0gaW5kZXggb2YgdGhlIGVsZW1lbnRcbiAqIC0gcmVmZXJlbmNlIHRvIHRoZSBzb3VyY2UgYXJyYXlcbiAqIC0gcmVmZXJlbmNlIHRvIHRoZSBkZXN0aW5hdGlvbiBhcnJheVxuICpcbiAqIEBwYXJhbSB7QXJyYXl9IGFycmF5XG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBmXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBmaWx0ZXIoYXJyYXk6IGFueVtdLCB0ZXN0OiBGdW5jdGlvbik6IGFueVtdIHtcbiAgdGVzdCA9XG4gICAgdGVzdCB8fFxuICAgIGZ1bmN0aW9uKHZhbHVlKSB7XG4gICAgICByZXR1cm4gISF2YWx1ZTtcbiAgICB9O1xuXG4gIHZhciByZXN1bHQgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBhcnJheS5sZW5ndGg7IGkrKykge1xuICAgIGlmICh0ZXN0KGFycmF5W2ldLCBpLCBhcnJheSwgcmVzdWx0KSkge1xuICAgICAgcmVzdWx0LnB1c2goYXJyYXlbaV0pO1xuICAgIH1cbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufVxuXG4vKiogRmlsdGVycyBwcm9wZXJ0aWVzIG9mIHRoZSBvYmplY3QgdXNpbmcgYSB0ZXN0IGZ1bmN0aW9uLlxuICpcbiAqIEZ1bmN0aW9uIHRlc3QgZ2V0cyA0IGFyZ3VtZW50cyBwYXNzZWQ6XG4gKiAtIGVsZW1lbnQgZnJvbSB0aGUgb2JqZWN0XG4gKiAtIGtleSBvZiB0aGUgZWxlbWVudFxuICogLSByZWZlcmVuY2UgdG8gdGhlIHNvdXJjZSBvYmplY3RcbiAqIC0gcmVmZXJlbmNlIHRvIHRoZSBkZXN0aW5hdGlvbiBvYmplY3RcbiAqXG4gKiBAcGFyYW0ge09iamVjdH0gb2JqZWN0XG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBmXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBmaWx0ZXJPYmplY3Qob2JqZWN0OiBPYmplY3QsIHRlc3Q6IEZ1bmN0aW9uKSB7XG4gIHZhciByZXN1bHQgPSB7fTtcbiAgb2JqZWN0QXBwbHkob2JqZWN0LCBmdW5jdGlvbih2YWx1ZSwga2V5KSB7XG4gICAgaWYgKCh0ZXN0ICYmIHRlc3QodmFsdWUsIGtleSwgb2JqZWN0LCByZXN1bHQpKSB8fCBCb29sZWFuKHZhbHVlKSkge1xuICAgICAgcmVzdWx0W2tleV0gPSB2YWx1ZTtcbiAgICB9XG4gIH0pO1xuICByZXR1cm4gcmVzdWx0O1xufVxuXG4vKiogRmxhdHRlbnMgYW4gb2JqZWN0IGludG8gYSB0d28tZGltZW5zaW9uYWwgYXJyYXkuXG4gKlxuICogQHBhcmFtICB7T2JqZWN0fSBvYmplY3RcbiAqIEByZXR1cm4ge0FycmF5fSByZXN1bHRpbmcgYXJyYXkgb2YgW2tleSwgdmFsdWVdIHBhaXJzXG4gKi9cbmV4cG9ydCBmdW5jdGlvbiBmbGF0dGVuKG9iamVjdDogT2JqZWN0KTogYW55W10ge1xuICB2YXIgcmVzdWx0ID0gW107XG4gIG9iamVjdEFwcGx5KG9iamVjdCwgZnVuY3Rpb24odmFsdWUsIGtleSkge1xuICAgIHJlc3VsdC5wdXNoKFtrZXksIHZhbHVlXSk7XG4gIH0pO1xuICByZXR1cm4gcmVzdWx0O1xufVxuXG4vKiogQ2hlY2tzIHdoZXRoZXIgYW55IGVsZW1lbnQgb2YgdGhlIGFycmF5IHBhc3NlcyB0aGUgdGVzdC5cbiAqXG4gKiBGdW5jdGlvbiB0ZXN0IGdldHMgMyBhcmd1bWVudHMgcGFzc2VkOlxuICogLSBlbGVtZW50IGZyb20gdGhlIGFycmF5XG4gKiAtIGluZGV4IG9mIHRoZSBlbGVtZW50XG4gKiAtIHJlZmVyZW5jZSB0byB0aGUgc291cmNlIGFycmF5XG4gKlxuICogQHBhcmFtIHtBcnJheX0gYXJyYXlcbiAqIEBwYXJhbSB7RnVuY3Rpb259IGZcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGFueShhcnJheTogYW55W10sIHRlc3Q6IEZ1bmN0aW9uKTogYm9vbGVhbiB7XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgYXJyYXkubGVuZ3RoOyBpKyspIHtcbiAgICBpZiAodGVzdChhcnJheVtpXSwgaSwgYXJyYXkpKSB7XG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIGZhbHNlO1xufVxuXG4vKiogQ2hlY2tzIHdoZXRoZXIgYWxsIGVsZW1lbnRzIG9mIHRoZSBhcnJheSBwYXNzIHRoZSB0ZXN0LlxuICpcbiAqIEZ1bmN0aW9uIHRlc3QgZ2V0cyAzIGFyZ3VtZW50cyBwYXNzZWQ6XG4gKiAtIGVsZW1lbnQgZnJvbSB0aGUgYXJyYXlcbiAqIC0gaW5kZXggb2YgdGhlIGVsZW1lbnRcbiAqIC0gcmVmZXJlbmNlIHRvIHRoZSBzb3VyY2UgYXJyYXlcbiAqXG4gKiBAcGFyYW0ge0FycmF5fSBhcnJheVxuICogQHBhcmFtIHtGdW5jdGlvbn0gZlxuICovXG5leHBvcnQgZnVuY3Rpb24gYWxsKGFycmF5OiBhbnlbXSwgdGVzdDogRnVuY3Rpb24pOiBib29sZWFuIHtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBhcnJheS5sZW5ndGg7IGkrKykge1xuICAgIGlmICghdGVzdChhcnJheVtpXSwgaSwgYXJyYXkpKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG4gIHJldHVybiB0cnVlO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gZW5jb2RlUGFyYW1zT2JqZWN0KGRhdGEpOiBzdHJpbmcge1xuICByZXR1cm4gbWFwT2JqZWN0KGRhdGEsIGZ1bmN0aW9uKHZhbHVlKSB7XG4gICAgaWYgKHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcpIHtcbiAgICAgIHZhbHVlID0gc2FmZUpTT05TdHJpbmdpZnkodmFsdWUpO1xuICAgIH1cbiAgICByZXR1cm4gZW5jb2RlVVJJQ29tcG9uZW50KGJhc2U2NGVuY29kZSh2YWx1ZS50b1N0cmluZygpKSk7XG4gIH0pO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gYnVpbGRRdWVyeVN0cmluZyhkYXRhOiBhbnkpOiBzdHJpbmcge1xuICB2YXIgcGFyYW1zID0gZmlsdGVyT2JqZWN0KGRhdGEsIGZ1bmN0aW9uKHZhbHVlKSB7XG4gICAgcmV0dXJuIHZhbHVlICE9PSB1bmRlZmluZWQ7XG4gIH0pO1xuXG4gIHZhciBxdWVyeSA9IG1hcChcbiAgICBmbGF0dGVuKGVuY29kZVBhcmFtc09iamVjdChwYXJhbXMpKSxcbiAgICBVdGlsLm1ldGhvZCgnam9pbicsICc9JylcbiAgKS5qb2luKCcmJyk7XG5cbiAgcmV0dXJuIHF1ZXJ5O1xufVxuXG4vKipcbiAqIFNlZSBodHRwczovL2dpdGh1Yi5jb20vZG91Z2xhc2Nyb2NrZm9yZC9KU09OLWpzL2Jsb2IvbWFzdGVyL2N5Y2xlLmpzXG4gKlxuICogUmVtb3ZlIGNpcmN1bGFyIHJlZmVyZW5jZXMgZnJvbSBhbiBvYmplY3QuIFJlcXVpcmVkIGZvciBKU09OLnN0cmluZ2lmeSBpblxuICogUmVhY3QgTmF0aXZlLCB3aGljaCB0ZW5kcyB0byBibG93IHVwIGEgbG90LlxuICpcbiAqIEBwYXJhbSAge2FueX0gb2JqZWN0XG4gKiBAcmV0dXJuIHthbnl9ICAgICAgICBEZWN5Y2xlZCBvYmplY3RcbiAqL1xuZXhwb3J0IGZ1bmN0aW9uIGRlY3ljbGVPYmplY3Qob2JqZWN0OiBhbnkpOiBhbnkge1xuICB2YXIgb2JqZWN0cyA9IFtdLFxuICAgIHBhdGhzID0gW107XG5cbiAgcmV0dXJuIChmdW5jdGlvbiBkZXJleih2YWx1ZSwgcGF0aCkge1xuICAgIHZhciBpLCBuYW1lLCBudTtcblxuICAgIHN3aXRjaCAodHlwZW9mIHZhbHVlKSB7XG4gICAgICBjYXNlICdvYmplY3QnOlxuICAgICAgICBpZiAoIXZhbHVlKSB7XG4gICAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgICAgIH1cbiAgICAgICAgZm9yIChpID0gMDsgaSA8IG9iamVjdHMubGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICBpZiAob2JqZWN0c1tpXSA9PT0gdmFsdWUpIHtcbiAgICAgICAgICAgIHJldHVybiB7ICRyZWY6IHBhdGhzW2ldIH07XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgb2JqZWN0cy5wdXNoKHZhbHVlKTtcbiAgICAgICAgcGF0aHMucHVzaChwYXRoKTtcblxuICAgICAgICBpZiAoT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5hcHBseSh2YWx1ZSkgPT09ICdbb2JqZWN0IEFycmF5XScpIHtcbiAgICAgICAgICBudSA9IFtdO1xuICAgICAgICAgIGZvciAoaSA9IDA7IGkgPCB2YWx1ZS5sZW5ndGg7IGkgKz0gMSkge1xuICAgICAgICAgICAgbnVbaV0gPSBkZXJleih2YWx1ZVtpXSwgcGF0aCArICdbJyArIGkgKyAnXScpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBudSA9IHt9O1xuICAgICAgICAgIGZvciAobmFtZSBpbiB2YWx1ZSkge1xuICAgICAgICAgICAgaWYgKE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbCh2YWx1ZSwgbmFtZSkpIHtcbiAgICAgICAgICAgICAgbnVbbmFtZV0gPSBkZXJleihcbiAgICAgICAgICAgICAgICB2YWx1ZVtuYW1lXSxcbiAgICAgICAgICAgICAgICBwYXRoICsgJ1snICsgSlNPTi5zdHJpbmdpZnkobmFtZSkgKyAnXSdcbiAgICAgICAgICAgICAgKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIG51O1xuICAgICAgY2FzZSAnbnVtYmVyJzpcbiAgICAgIGNhc2UgJ3N0cmluZyc6XG4gICAgICBjYXNlICdib29sZWFuJzpcbiAgICAgICAgcmV0dXJuIHZhbHVlO1xuICAgIH1cbiAgfSkob2JqZWN0LCAnJCcpO1xufVxuXG4vKipcbiAqIFByb3ZpZGVzIGEgY3Jvc3MtYnJvd3NlciBhbmQgY3Jvc3MtcGxhdGZvcm0gd2F5IHRvIHNhZmVseSBzdHJpbmdpZnkgb2JqZWN0c1xuICogaW50byBKU09OLiBUaGlzIGlzIHBhcnRpY3VsYXJseSBuZWNlc3NhcnkgZm9yIFJlYWN0TmF0aXZlLCB3aGVyZSBjaXJjdWxhciBKU09OXG4gKiBzdHJ1Y3R1cmVzIHRocm93IGFuIGV4Y2VwdGlvbi5cbiAqXG4gKiBAcGFyYW0gIHthbnl9ICAgIHNvdXJjZSBUaGUgb2JqZWN0IHRvIHN0cmluZ2lmeVxuICogQHJldHVybiB7c3RyaW5nfSAgICAgICAgVGhlIHNlcmlhbGl6ZWQgb3V0cHV0LlxuICovXG5leHBvcnQgZnVuY3Rpb24gc2FmZUpTT05TdHJpbmdpZnkoc291cmNlOiBhbnkpOiBzdHJpbmcge1xuICB0cnkge1xuICAgIHJldHVybiBKU09OLnN0cmluZ2lmeShzb3VyY2UpO1xuICB9IGNhdGNoIChlKSB7XG4gICAgcmV0dXJuIEpTT04uc3RyaW5naWZ5KGRlY3ljbGVPYmplY3Qoc291cmNlKSk7XG4gIH1cbn1cbiIsICJpbXBvcnQgeyBzdHJpbmdpZnkgfSBmcm9tICcuL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBQdXNoZXIgZnJvbSAnLi9wdXNoZXInO1xuXG5jbGFzcyBMb2dnZXIge1xuICBkZWJ1ZyguLi5hcmdzOiBhbnlbXSkge1xuICAgIHRoaXMubG9nKHRoaXMuZ2xvYmFsTG9nLCBhcmdzKTtcbiAgfVxuXG4gIHdhcm4oLi4uYXJnczogYW55W10pIHtcbiAgICB0aGlzLmxvZyh0aGlzLmdsb2JhbExvZ1dhcm4sIGFyZ3MpO1xuICB9XG5cbiAgZXJyb3IoLi4uYXJnczogYW55W10pIHtcbiAgICB0aGlzLmxvZyh0aGlzLmdsb2JhbExvZ0Vycm9yLCBhcmdzKTtcbiAgfVxuXG4gIHByaXZhdGUgZ2xvYmFsTG9nID0gKG1lc3NhZ2U6IHN0cmluZykgPT4ge1xuICAgIGlmIChnbG9iYWwuY29uc29sZSAmJiBnbG9iYWwuY29uc29sZS5sb2cpIHtcbiAgICAgIGdsb2JhbC5jb25zb2xlLmxvZyhtZXNzYWdlKTtcbiAgICB9XG4gIH07XG5cbiAgcHJpdmF0ZSBnbG9iYWxMb2dXYXJuKG1lc3NhZ2U6IHN0cmluZykge1xuICAgIGlmIChnbG9iYWwuY29uc29sZSAmJiBnbG9iYWwuY29uc29sZS53YXJuKSB7XG4gICAgICBnbG9iYWwuY29uc29sZS53YXJuKG1lc3NhZ2UpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLmdsb2JhbExvZyhtZXNzYWdlKTtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIGdsb2JhbExvZ0Vycm9yKG1lc3NhZ2U6IHN0cmluZykge1xuICAgIGlmIChnbG9iYWwuY29uc29sZSAmJiBnbG9iYWwuY29uc29sZS5lcnJvcikge1xuICAgICAgZ2xvYmFsLmNvbnNvbGUuZXJyb3IobWVzc2FnZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuZ2xvYmFsTG9nV2FybihtZXNzYWdlKTtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIGxvZyhcbiAgICBkZWZhdWx0TG9nZ2luZ0Z1bmN0aW9uOiAobWVzc2FnZTogc3RyaW5nKSA9PiB2b2lkLFxuICAgIC4uLmFyZ3M6IGFueVtdXG4gICkge1xuICAgIHZhciBtZXNzYWdlID0gc3RyaW5naWZ5LmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG4gICAgaWYgKFB1c2hlci5sb2cpIHtcbiAgICAgIFB1c2hlci5sb2cobWVzc2FnZSk7XG4gICAgfSBlbHNlIGlmIChQdXNoZXIubG9nVG9Db25zb2xlKSB7XG4gICAgICBjb25zdCBsb2cgPSBkZWZhdWx0TG9nZ2luZ0Z1bmN0aW9uLmJpbmQodGhpcyk7XG4gICAgICBsb2cobWVzc2FnZSk7XG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IG5ldyBMb2dnZXIoKTtcbiIsICJpbXBvcnQgQnJvd3NlciBmcm9tICcuLi9icm93c2VyJztcbmltcG9ydCBMb2dnZXIgZnJvbSAnY29yZS9sb2dnZXInO1xuaW1wb3J0IEpTT05QUmVxdWVzdCBmcm9tICcuLi9kb20vanNvbnBfcmVxdWVzdCc7XG5pbXBvcnQgeyBTY3JpcHRSZWNlaXZlcnMgfSBmcm9tICcuLi9kb20vc2NyaXB0X3JlY2VpdmVyX2ZhY3RvcnknO1xuaW1wb3J0IHsgQXV0aFRyYW5zcG9ydCB9IGZyb20gJ2NvcmUvYXV0aC9hdXRoX3RyYW5zcG9ydHMnO1xuaW1wb3J0IHtcbiAgQXV0aFJlcXVlc3RUeXBlLFxuICBBdXRoVHJhbnNwb3J0Q2FsbGJhY2ssXG4gIEludGVybmFsQXV0aE9wdGlvbnNcbn0gZnJvbSAnY29yZS9hdXRoL29wdGlvbnMnO1xuXG52YXIganNvbnA6IEF1dGhUcmFuc3BvcnQgPSBmdW5jdGlvbihcbiAgY29udGV4dDogQnJvd3NlcixcbiAgcXVlcnk6IHN0cmluZyxcbiAgYXV0aE9wdGlvbnM6IEludGVybmFsQXV0aE9wdGlvbnMsXG4gIGF1dGhSZXF1ZXN0VHlwZTogQXV0aFJlcXVlc3RUeXBlLFxuICBjYWxsYmFjazogQXV0aFRyYW5zcG9ydENhbGxiYWNrXG4pIHtcbiAgaWYgKFxuICAgIGF1dGhPcHRpb25zLmhlYWRlcnMgIT09IHVuZGVmaW5lZCB8fFxuICAgIGF1dGhPcHRpb25zLmhlYWRlcnNQcm92aWRlciAhPSBudWxsXG4gICkge1xuICAgIExvZ2dlci53YXJuKFxuICAgICAgYFRvIHNlbmQgaGVhZGVycyB3aXRoIHRoZSAke2F1dGhSZXF1ZXN0VHlwZS50b1N0cmluZygpfSByZXF1ZXN0LCB5b3UgbXVzdCB1c2UgQUpBWCwgcmF0aGVyIHRoYW4gSlNPTlAuYFxuICAgICk7XG4gIH1cblxuICB2YXIgY2FsbGJhY2tOYW1lID0gY29udGV4dC5uZXh0QXV0aENhbGxiYWNrSUQudG9TdHJpbmcoKTtcbiAgY29udGV4dC5uZXh0QXV0aENhbGxiYWNrSUQrKztcblxuICB2YXIgZG9jdW1lbnQgPSBjb250ZXh0LmdldERvY3VtZW50KCk7XG4gIHZhciBzY3JpcHQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdzY3JpcHQnKTtcbiAgLy8gSGFja2VkIHdyYXBwZXIuXG4gIGNvbnRleHQuYXV0aF9jYWxsYmFja3NbY2FsbGJhY2tOYW1lXSA9IGZ1bmN0aW9uKGRhdGEpIHtcbiAgICBjYWxsYmFjayhudWxsLCBkYXRhKTtcbiAgfTtcblxuICB2YXIgY2FsbGJhY2tfbmFtZSA9IFwiUHVzaGVyLmF1dGhfY2FsbGJhY2tzWydcIiArIGNhbGxiYWNrTmFtZSArIFwiJ11cIjtcbiAgc2NyaXB0LnNyYyA9XG4gICAgYXV0aE9wdGlvbnMuZW5kcG9pbnQgK1xuICAgICc/Y2FsbGJhY2s9JyArXG4gICAgZW5jb2RlVVJJQ29tcG9uZW50KGNhbGxiYWNrX25hbWUpICtcbiAgICAnJicgK1xuICAgIHF1ZXJ5O1xuXG4gIHZhciBoZWFkID1cbiAgICBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnaGVhZCcpWzBdIHx8IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudDtcbiAgaGVhZC5pbnNlcnRCZWZvcmUoc2NyaXB0LCBoZWFkLmZpcnN0Q2hpbGQpO1xufTtcblxuZXhwb3J0IGRlZmF1bHQganNvbnA7XG4iLCAiaW1wb3J0IFNjcmlwdFJlY2VpdmVyIGZyb20gJy4vc2NyaXB0X3JlY2VpdmVyJztcblxuLyoqIFNlbmRzIGEgZ2VuZXJpYyBIVFRQIEdFVCByZXF1ZXN0IHVzaW5nIGEgc2NyaXB0IHRhZy5cbiAqXG4gKiBCeSBjb25zdHJ1Y3RpbmcgVVJMIGluIGEgc3BlY2lmaWMgd2F5LCBpdCBjYW4gYmUgdXNlZCBmb3IgbG9hZGluZ1xuICogSmF2YVNjcmlwdCByZXNvdXJjZXMgb3IgSlNPTlAgcmVxdWVzdHMuIEl0IGNhbiBub3RpZnkgYWJvdXQgZXJyb3JzLCBidXRcbiAqIG9ubHkgaW4gY2VydGFpbiBlbnZpcm9ubWVudHMuIFBsZWFzZSB0YWtlIGNhcmUgb2YgbW9uaXRvcmluZyB0aGUgc3RhdGUgb2ZcbiAqIHRoZSByZXF1ZXN0IHlvdXJzZWxmLlxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBzcmNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU2NyaXB0UmVxdWVzdCB7XG4gIHNyYzogc3RyaW5nO1xuICBzY3JpcHQ6IGFueTtcbiAgZXJyb3JTY3JpcHQ6IGFueTtcblxuICBjb25zdHJ1Y3RvcihzcmM6IHN0cmluZykge1xuICAgIHRoaXMuc3JjID0gc3JjO1xuICB9XG5cbiAgc2VuZChyZWNlaXZlcjogU2NyaXB0UmVjZWl2ZXIpIHtcbiAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgdmFyIGVycm9yU3RyaW5nID0gJ0Vycm9yIGxvYWRpbmcgJyArIHNlbGYuc3JjO1xuXG4gICAgc2VsZi5zY3JpcHQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdzY3JpcHQnKTtcbiAgICBzZWxmLnNjcmlwdC5pZCA9IHJlY2VpdmVyLmlkO1xuICAgIHNlbGYuc2NyaXB0LnNyYyA9IHNlbGYuc3JjO1xuICAgIHNlbGYuc2NyaXB0LnR5cGUgPSAndGV4dC9qYXZhc2NyaXB0JztcbiAgICBzZWxmLnNjcmlwdC5jaGFyc2V0ID0gJ1VURi04JztcblxuICAgIGlmIChzZWxmLnNjcmlwdC5hZGRFdmVudExpc3RlbmVyKSB7XG4gICAgICBzZWxmLnNjcmlwdC5vbmVycm9yID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIHJlY2VpdmVyLmNhbGxiYWNrKGVycm9yU3RyaW5nKTtcbiAgICAgIH07XG4gICAgICBzZWxmLnNjcmlwdC5vbmxvYWQgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgcmVjZWl2ZXIuY2FsbGJhY2sobnVsbCk7XG4gICAgICB9O1xuICAgIH0gZWxzZSB7XG4gICAgICBzZWxmLnNjcmlwdC5vbnJlYWR5c3RhdGVjaGFuZ2UgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKFxuICAgICAgICAgIHNlbGYuc2NyaXB0LnJlYWR5U3RhdGUgPT09ICdsb2FkZWQnIHx8XG4gICAgICAgICAgc2VsZi5zY3JpcHQucmVhZHlTdGF0ZSA9PT0gJ2NvbXBsZXRlJ1xuICAgICAgICApIHtcbiAgICAgICAgICByZWNlaXZlci5jYWxsYmFjayhudWxsKTtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICB9XG5cbiAgICAvLyBPcGVyYTwxMS42IGhhY2sgZm9yIG1pc3Npbmcgb25lcnJvciBjYWxsYmFja1xuICAgIGlmIChcbiAgICAgIHNlbGYuc2NyaXB0LmFzeW5jID09PSB1bmRlZmluZWQgJiZcbiAgICAgICg8YW55PmRvY3VtZW50KS5hdHRhY2hFdmVudCAmJlxuICAgICAgL29wZXJhL2kudGVzdChuYXZpZ2F0b3IudXNlckFnZW50KVxuICAgICkge1xuICAgICAgc2VsZi5lcnJvclNjcmlwdCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3NjcmlwdCcpO1xuICAgICAgc2VsZi5lcnJvclNjcmlwdC5pZCA9IHJlY2VpdmVyLmlkICsgJ19lcnJvcic7XG4gICAgICBzZWxmLmVycm9yU2NyaXB0LnRleHQgPSByZWNlaXZlci5uYW1lICsgXCIoJ1wiICsgZXJyb3JTdHJpbmcgKyBcIicpO1wiO1xuICAgICAgc2VsZi5zY3JpcHQuYXN5bmMgPSBzZWxmLmVycm9yU2NyaXB0LmFzeW5jID0gZmFsc2U7XG4gICAgfSBlbHNlIHtcbiAgICAgIHNlbGYuc2NyaXB0LmFzeW5jID0gdHJ1ZTtcbiAgICB9XG5cbiAgICB2YXIgaGVhZCA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdoZWFkJylbMF07XG4gICAgaGVhZC5pbnNlcnRCZWZvcmUoc2VsZi5zY3JpcHQsIGhlYWQuZmlyc3RDaGlsZCk7XG4gICAgaWYgKHNlbGYuZXJyb3JTY3JpcHQpIHtcbiAgICAgIGhlYWQuaW5zZXJ0QmVmb3JlKHNlbGYuZXJyb3JTY3JpcHQsIHNlbGYuc2NyaXB0Lm5leHRTaWJsaW5nKTtcbiAgICB9XG4gIH1cblxuICAvKiogQ2xlYW5zIHVwIHRoZSBET00gcmVtYWlucyBvZiB0aGUgc2NyaXB0IHJlcXVlc3QuICovXG4gIGNsZWFudXAoKSB7XG4gICAgaWYgKHRoaXMuc2NyaXB0KSB7XG4gICAgICB0aGlzLnNjcmlwdC5vbmxvYWQgPSB0aGlzLnNjcmlwdC5vbmVycm9yID0gbnVsbDtcbiAgICAgIHRoaXMuc2NyaXB0Lm9ucmVhZHlzdGF0ZWNoYW5nZSA9IG51bGw7XG4gICAgfVxuICAgIGlmICh0aGlzLnNjcmlwdCAmJiB0aGlzLnNjcmlwdC5wYXJlbnROb2RlKSB7XG4gICAgICB0aGlzLnNjcmlwdC5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKHRoaXMuc2NyaXB0KTtcbiAgICB9XG4gICAgaWYgKHRoaXMuZXJyb3JTY3JpcHQgJiYgdGhpcy5lcnJvclNjcmlwdC5wYXJlbnROb2RlKSB7XG4gICAgICB0aGlzLmVycm9yU2NyaXB0LnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQodGhpcy5lcnJvclNjcmlwdCk7XG4gICAgfVxuICAgIHRoaXMuc2NyaXB0ID0gbnVsbDtcbiAgICB0aGlzLmVycm9yU2NyaXB0ID0gbnVsbDtcbiAgfVxufVxuIiwgImltcG9ydCBTY3JpcHRSZWNlaXZlciBmcm9tICcuL3NjcmlwdF9yZWNlaXZlcic7XG5pbXBvcnQgU2NyaXB0UmVxdWVzdCBmcm9tICcuL3NjcmlwdF9yZXF1ZXN0JztcbmltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJ2NvcmUvdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IFV0aWwgZnJvbSAnY29yZS91dGlsJztcbmltcG9ydCBSdW50aW1lIGZyb20gJy4uL3J1bnRpbWUnO1xuXG4vKiogU2VuZHMgZGF0YSB2aWEgSlNPTlAuXG4gKlxuICogRGF0YSBpcyBhIGtleS12YWx1ZSBtYXAuIEl0cyB2YWx1ZXMgYXJlIEpTT04tZW5jb2RlZCBhbmQgdGhlbiBwYXNzZWRcbiAqIHRocm91Z2ggYmFzZTY0LiBGaW5hbGx5LCBrZXlzIGFuZCBlbmNvZGVkIHZhbHVlcyBhcmUgYXBwZW5kZWQgdG8gdGhlIHF1ZXJ5XG4gKiBzdHJpbmcuXG4gKlxuICogVGhlIGNsYXNzIGl0c2VsZiBkb2VzIG5vdCBndWFyYW50ZWUgcmFpc2luZyBlcnJvcnMgb24gZmFpbHVyZXMsIGFzIGl0J3Mgbm90XG4gKiBwb3NzaWJsZSB0byBzdXBwb3J0IHN1Y2ggZmVhdHVyZSBvbiBhbGwgYnJvd3NlcnMuIEluc3RlYWQsIEpTT05QIGVuZHBvaW50XG4gKiBzaG91bGQgY2FsbCBiYWNrIGluIGEgd2F5IHRoYXQncyBlYXN5IHRvIGRpc3Rpbmd1aXNoIGZyb20gYnJvd3NlciBjYWxscyxcbiAqIGZvciBleGFtcGxlIGJ5IHBhc3NpbmcgYSBzZWNvbmQgYXJndW1lbnQgdG8gdGhlIHJlY2VpdmVyLlxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSB1cmxcbiAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIGtleS12YWx1ZSBtYXAgb2YgZGF0YSB0byBiZSBzdWJtaXR0ZWRcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSlNPTlBSZXF1ZXN0IHtcbiAgdXJsOiBzdHJpbmc7XG4gIGRhdGE6IGFueTtcbiAgcmVxdWVzdDogU2NyaXB0UmVxdWVzdDtcblxuICBjb25zdHJ1Y3Rvcih1cmw6IHN0cmluZywgZGF0YTogYW55KSB7XG4gICAgdGhpcy51cmwgPSB1cmw7XG4gICAgdGhpcy5kYXRhID0gZGF0YTtcbiAgfVxuXG4gIC8qKiBTZW5kcyB0aGUgYWN0dWFsIEpTT05QIHJlcXVlc3QuXG4gICAqXG4gICAqIEBwYXJhbSB7U2NyaXB0UmVjZWl2ZXJ9IHJlY2VpdmVyXG4gICAqL1xuICBzZW5kKHJlY2VpdmVyOiBTY3JpcHRSZWNlaXZlcikge1xuICAgIGlmICh0aGlzLnJlcXVlc3QpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB2YXIgcXVlcnkgPSBDb2xsZWN0aW9ucy5idWlsZFF1ZXJ5U3RyaW5nKHRoaXMuZGF0YSk7XG4gICAgdmFyIHVybCA9IHRoaXMudXJsICsgJy8nICsgcmVjZWl2ZXIubnVtYmVyICsgJz8nICsgcXVlcnk7XG4gICAgdGhpcy5yZXF1ZXN0ID0gUnVudGltZS5jcmVhdGVTY3JpcHRSZXF1ZXN0KHVybCk7XG4gICAgdGhpcy5yZXF1ZXN0LnNlbmQocmVjZWl2ZXIpO1xuICB9XG5cbiAgLyoqIENsZWFucyB1cCB0aGUgRE9NIHJlbWFpbnMgb2YgdGhlIEpTT05QIHJlcXVlc3QuICovXG4gIGNsZWFudXAoKSB7XG4gICAgaWYgKHRoaXMucmVxdWVzdCkge1xuICAgICAgdGhpcy5yZXF1ZXN0LmNsZWFudXAoKTtcbiAgICB9XG4gIH1cbn1cbiIsICJpbXBvcnQgVGltZWxpbmVTZW5kZXIgZnJvbSAnY29yZS90aW1lbGluZS90aW1lbGluZV9zZW5kZXInO1xuaW1wb3J0IFRpbWVsaW5lVHJhbnNwb3J0IGZyb20gJ2NvcmUvdGltZWxpbmUvdGltZWxpbmVfdHJhbnNwb3J0JztcbmltcG9ydCBCcm93c2VyIGZyb20gJ3J1bnRpbWUnO1xuaW1wb3J0IHsgQXV0aFRyYW5zcG9ydCB9IGZyb20gJ2NvcmUvYXV0aC9hdXRoX3RyYW5zcG9ydHMnO1xuaW1wb3J0IHsgU2NyaXB0UmVjZWl2ZXJzIH0gZnJvbSAnLi4vZG9tL3NjcmlwdF9yZWNlaXZlcl9mYWN0b3J5JztcblxudmFyIGdldEFnZW50ID0gZnVuY3Rpb24oc2VuZGVyOiBUaW1lbGluZVNlbmRlciwgdXNlVExTOiBib29sZWFuKSB7XG4gIHJldHVybiBmdW5jdGlvbihkYXRhOiBhbnksIGNhbGxiYWNrOiBGdW5jdGlvbikge1xuICAgIHZhciBzY2hlbWUgPSAnaHR0cCcgKyAodXNlVExTID8gJ3MnIDogJycpICsgJzovLyc7XG4gICAgdmFyIHVybCA9XG4gICAgICBzY2hlbWUgKyAoc2VuZGVyLmhvc3QgfHwgc2VuZGVyLm9wdGlvbnMuaG9zdCkgKyBzZW5kZXIub3B0aW9ucy5wYXRoO1xuICAgIHZhciByZXF1ZXN0ID0gQnJvd3Nlci5jcmVhdGVKU09OUFJlcXVlc3QodXJsLCBkYXRhKTtcblxuICAgIHZhciByZWNlaXZlciA9IEJyb3dzZXIuU2NyaXB0UmVjZWl2ZXJzLmNyZWF0ZShmdW5jdGlvbihlcnJvciwgcmVzdWx0KSB7XG4gICAgICBTY3JpcHRSZWNlaXZlcnMucmVtb3ZlKHJlY2VpdmVyKTtcbiAgICAgIHJlcXVlc3QuY2xlYW51cCgpO1xuXG4gICAgICBpZiAocmVzdWx0ICYmIHJlc3VsdC5ob3N0KSB7XG4gICAgICAgIHNlbmRlci5ob3N0ID0gcmVzdWx0Lmhvc3Q7XG4gICAgICB9XG4gICAgICBpZiAoY2FsbGJhY2spIHtcbiAgICAgICAgY2FsbGJhY2soZXJyb3IsIHJlc3VsdCk7XG4gICAgICB9XG4gICAgfSk7XG4gICAgcmVxdWVzdC5zZW5kKHJlY2VpdmVyKTtcbiAgfTtcbn07XG5cbnZhciBqc29ucCA9IHtcbiAgbmFtZTogJ2pzb25wJyxcbiAgZ2V0QWdlbnRcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGpzb25wO1xuIiwgImltcG9ydCBEZWZhdWx0cyBmcm9tICcuLi9kZWZhdWx0cyc7XG5pbXBvcnQgeyBkZWZhdWx0IGFzIFVSTFNjaGVtZSwgVVJMU2NoZW1lUGFyYW1zIH0gZnJvbSAnLi91cmxfc2NoZW1lJztcblxuZnVuY3Rpb24gZ2V0R2VuZXJpY1VSTChcbiAgYmFzZVNjaGVtZTogc3RyaW5nLFxuICBwYXJhbXM6IFVSTFNjaGVtZVBhcmFtcyxcbiAgcGF0aDogc3RyaW5nXG4pOiBzdHJpbmcge1xuICB2YXIgc2NoZW1lID0gYmFzZVNjaGVtZSArIChwYXJhbXMudXNlVExTID8gJ3MnIDogJycpO1xuICB2YXIgaG9zdCA9IHBhcmFtcy51c2VUTFMgPyBwYXJhbXMuaG9zdFRMUyA6IHBhcmFtcy5ob3N0Tm9uVExTO1xuICByZXR1cm4gc2NoZW1lICsgJzovLycgKyBob3N0ICsgcGF0aDtcbn1cblxuZnVuY3Rpb24gZ2V0R2VuZXJpY1BhdGgoa2V5OiBzdHJpbmcsIHF1ZXJ5U3RyaW5nPzogc3RyaW5nKTogc3RyaW5nIHtcbiAgdmFyIHBhdGggPSAnL2FwcC8nICsga2V5O1xuICB2YXIgcXVlcnkgPVxuICAgICc/cHJvdG9jb2w9JyArXG4gICAgRGVmYXVsdHMuUFJPVE9DT0wgK1xuICAgICcmY2xpZW50PWpzJyArXG4gICAgJyZ2ZXJzaW9uPScgK1xuICAgIERlZmF1bHRzLlZFUlNJT04gK1xuICAgIChxdWVyeVN0cmluZyA/ICcmJyArIHF1ZXJ5U3RyaW5nIDogJycpO1xuICByZXR1cm4gcGF0aCArIHF1ZXJ5O1xufVxuXG5leHBvcnQgdmFyIHdzOiBVUkxTY2hlbWUgPSB7XG4gIGdldEluaXRpYWw6IGZ1bmN0aW9uKGtleTogc3RyaW5nLCBwYXJhbXM6IFVSTFNjaGVtZVBhcmFtcyk6IHN0cmluZyB7XG4gICAgdmFyIHBhdGggPSAocGFyYW1zLmh0dHBQYXRoIHx8ICcnKSArIGdldEdlbmVyaWNQYXRoKGtleSwgJ2ZsYXNoPWZhbHNlJyk7XG4gICAgcmV0dXJuIGdldEdlbmVyaWNVUkwoJ3dzJywgcGFyYW1zLCBwYXRoKTtcbiAgfVxufTtcblxuZXhwb3J0IHZhciBodHRwOiBVUkxTY2hlbWUgPSB7XG4gIGdldEluaXRpYWw6IGZ1bmN0aW9uKGtleTogc3RyaW5nLCBwYXJhbXM6IFVSTFNjaGVtZVBhcmFtcyk6IHN0cmluZyB7XG4gICAgdmFyIHBhdGggPSAocGFyYW1zLmh0dHBQYXRoIHx8ICcvcHVzaGVyJykgKyBnZXRHZW5lcmljUGF0aChrZXkpO1xuICAgIHJldHVybiBnZXRHZW5lcmljVVJMKCdodHRwJywgcGFyYW1zLCBwYXRoKTtcbiAgfVxufTtcblxuZXhwb3J0IHZhciBzb2NranM6IFVSTFNjaGVtZSA9IHtcbiAgZ2V0SW5pdGlhbDogZnVuY3Rpb24oa2V5OiBzdHJpbmcsIHBhcmFtczogVVJMU2NoZW1lUGFyYW1zKTogc3RyaW5nIHtcbiAgICByZXR1cm4gZ2V0R2VuZXJpY1VSTCgnaHR0cCcsIHBhcmFtcywgcGFyYW1zLmh0dHBQYXRoIHx8ICcvcHVzaGVyJyk7XG4gIH0sXG4gIGdldFBhdGg6IGZ1bmN0aW9uKGtleTogc3RyaW5nLCBwYXJhbXM6IFVSTFNjaGVtZVBhcmFtcyk6IHN0cmluZyB7XG4gICAgcmV0dXJuIGdldEdlbmVyaWNQYXRoKGtleSk7XG4gIH1cbn07XG4iLCAiaW1wb3J0IENhbGxiYWNrIGZyb20gJy4vY2FsbGJhY2snO1xuaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnLi4vdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IENhbGxiYWNrVGFibGUgZnJvbSAnLi9jYWxsYmFja190YWJsZSc7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENhbGxiYWNrUmVnaXN0cnkge1xuICBfY2FsbGJhY2tzOiBDYWxsYmFja1RhYmxlO1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuX2NhbGxiYWNrcyA9IHt9O1xuICB9XG5cbiAgZ2V0KG5hbWU6IHN0cmluZyk6IENhbGxiYWNrW10ge1xuICAgIHJldHVybiB0aGlzLl9jYWxsYmFja3NbcHJlZml4KG5hbWUpXTtcbiAgfVxuXG4gIGFkZChuYW1lOiBzdHJpbmcsIGNhbGxiYWNrOiBGdW5jdGlvbiwgY29udGV4dDogYW55KSB7XG4gICAgdmFyIHByZWZpeGVkRXZlbnROYW1lID0gcHJlZml4KG5hbWUpO1xuICAgIHRoaXMuX2NhbGxiYWNrc1twcmVmaXhlZEV2ZW50TmFtZV0gPVxuICAgICAgdGhpcy5fY2FsbGJhY2tzW3ByZWZpeGVkRXZlbnROYW1lXSB8fCBbXTtcbiAgICB0aGlzLl9jYWxsYmFja3NbcHJlZml4ZWRFdmVudE5hbWVdLnB1c2goe1xuICAgICAgZm46IGNhbGxiYWNrLFxuICAgICAgY29udGV4dDogY29udGV4dFxuICAgIH0pO1xuICB9XG5cbiAgcmVtb3ZlKG5hbWU/OiBzdHJpbmcsIGNhbGxiYWNrPzogRnVuY3Rpb24sIGNvbnRleHQ/OiBhbnkpIHtcbiAgICBpZiAoIW5hbWUgJiYgIWNhbGxiYWNrICYmICFjb250ZXh0KSB7XG4gICAgICB0aGlzLl9jYWxsYmFja3MgPSB7fTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB2YXIgbmFtZXMgPSBuYW1lID8gW3ByZWZpeChuYW1lKV0gOiBDb2xsZWN0aW9ucy5rZXlzKHRoaXMuX2NhbGxiYWNrcyk7XG5cbiAgICBpZiAoY2FsbGJhY2sgfHwgY29udGV4dCkge1xuICAgICAgdGhpcy5yZW1vdmVDYWxsYmFjayhuYW1lcywgY2FsbGJhY2ssIGNvbnRleHQpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLnJlbW92ZUFsbENhbGxiYWNrcyhuYW1lcyk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSByZW1vdmVDYWxsYmFjayhuYW1lczogc3RyaW5nW10sIGNhbGxiYWNrOiBGdW5jdGlvbiwgY29udGV4dDogYW55KSB7XG4gICAgQ29sbGVjdGlvbnMuYXBwbHkoXG4gICAgICBuYW1lcyxcbiAgICAgIGZ1bmN0aW9uKG5hbWUpIHtcbiAgICAgICAgdGhpcy5fY2FsbGJhY2tzW25hbWVdID0gQ29sbGVjdGlvbnMuZmlsdGVyKFxuICAgICAgICAgIHRoaXMuX2NhbGxiYWNrc1tuYW1lXSB8fCBbXSxcbiAgICAgICAgICBmdW5jdGlvbihiaW5kaW5nKSB7XG4gICAgICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgICAoY2FsbGJhY2sgJiYgY2FsbGJhY2sgIT09IGJpbmRpbmcuZm4pIHx8XG4gICAgICAgICAgICAgIChjb250ZXh0ICYmIGNvbnRleHQgIT09IGJpbmRpbmcuY29udGV4dClcbiAgICAgICAgICAgICk7XG4gICAgICAgICAgfVxuICAgICAgICApO1xuICAgICAgICBpZiAodGhpcy5fY2FsbGJhY2tzW25hbWVdLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgIGRlbGV0ZSB0aGlzLl9jYWxsYmFja3NbbmFtZV07XG4gICAgICAgIH1cbiAgICAgIH0sXG4gICAgICB0aGlzXG4gICAgKTtcbiAgfVxuXG4gIHByaXZhdGUgcmVtb3ZlQWxsQ2FsbGJhY2tzKG5hbWVzOiBzdHJpbmdbXSkge1xuICAgIENvbGxlY3Rpb25zLmFwcGx5KFxuICAgICAgbmFtZXMsXG4gICAgICBmdW5jdGlvbihuYW1lKSB7XG4gICAgICAgIGRlbGV0ZSB0aGlzLl9jYWxsYmFja3NbbmFtZV07XG4gICAgICB9LFxuICAgICAgdGhpc1xuICAgICk7XG4gIH1cbn1cblxuZnVuY3Rpb24gcHJlZml4KG5hbWU6IHN0cmluZyk6IHN0cmluZyB7XG4gIHJldHVybiAnXycgKyBuYW1lO1xufVxuIiwgImltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBDYWxsYmFjayBmcm9tICcuL2NhbGxiYWNrJztcbmltcG9ydCBNZXRhZGF0YSBmcm9tICcuLi9jaGFubmVscy9tZXRhZGF0YSc7XG5pbXBvcnQgQ2FsbGJhY2tSZWdpc3RyeSBmcm9tICcuL2NhbGxiYWNrX3JlZ2lzdHJ5JztcblxuLyoqIE1hbmFnZXMgY2FsbGJhY2sgYmluZGluZ3MgYW5kIGV2ZW50IGVtaXR0aW5nLlxuICpcbiAqIEBwYXJhbSBGdW5jdGlvbiBmYWlsVGhyb3VnaCBjYWxsZWQgd2hlbiBubyBsaXN0ZW5lcnMgYXJlIGJvdW5kIHRvIGFuIGV2ZW50XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIERpc3BhdGNoZXIge1xuICBjYWxsYmFja3M6IENhbGxiYWNrUmVnaXN0cnk7XG4gIGdsb2JhbF9jYWxsYmFja3M6IEZ1bmN0aW9uW107XG4gIGZhaWxUaHJvdWdoOiBGdW5jdGlvbjtcblxuICBjb25zdHJ1Y3RvcihmYWlsVGhyb3VnaD86IEZ1bmN0aW9uKSB7XG4gICAgdGhpcy5jYWxsYmFja3MgPSBuZXcgQ2FsbGJhY2tSZWdpc3RyeSgpO1xuICAgIHRoaXMuZ2xvYmFsX2NhbGxiYWNrcyA9IFtdO1xuICAgIHRoaXMuZmFpbFRocm91Z2ggPSBmYWlsVGhyb3VnaDtcbiAgfVxuXG4gIGJpbmQoZXZlbnROYW1lOiBzdHJpbmcsIGNhbGxiYWNrOiBGdW5jdGlvbiwgY29udGV4dD86IGFueSkge1xuICAgIHRoaXMuY2FsbGJhY2tzLmFkZChldmVudE5hbWUsIGNhbGxiYWNrLCBjb250ZXh0KTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIGJpbmRfZ2xvYmFsKGNhbGxiYWNrOiBGdW5jdGlvbikge1xuICAgIHRoaXMuZ2xvYmFsX2NhbGxiYWNrcy5wdXNoKGNhbGxiYWNrKTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIHVuYmluZChldmVudE5hbWU/OiBzdHJpbmcsIGNhbGxiYWNrPzogRnVuY3Rpb24sIGNvbnRleHQ/OiBhbnkpIHtcbiAgICB0aGlzLmNhbGxiYWNrcy5yZW1vdmUoZXZlbnROYW1lLCBjYWxsYmFjaywgY29udGV4dCk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICB1bmJpbmRfZ2xvYmFsKGNhbGxiYWNrPzogRnVuY3Rpb24pIHtcbiAgICBpZiAoIWNhbGxiYWNrKSB7XG4gICAgICB0aGlzLmdsb2JhbF9jYWxsYmFja3MgPSBbXTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cblxuICAgIHRoaXMuZ2xvYmFsX2NhbGxiYWNrcyA9IENvbGxlY3Rpb25zLmZpbHRlcihcbiAgICAgIHRoaXMuZ2xvYmFsX2NhbGxiYWNrcyB8fCBbXSxcbiAgICAgIGMgPT4gYyAhPT0gY2FsbGJhY2tcbiAgICApO1xuXG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICB1bmJpbmRfYWxsKCkge1xuICAgIHRoaXMudW5iaW5kKCk7XG4gICAgdGhpcy51bmJpbmRfZ2xvYmFsKCk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICBlbWl0KGV2ZW50TmFtZTogc3RyaW5nLCBkYXRhPzogYW55LCBtZXRhZGF0YT86IE1ldGFkYXRhKTogRGlzcGF0Y2hlciB7XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCB0aGlzLmdsb2JhbF9jYWxsYmFja3MubGVuZ3RoOyBpKyspIHtcbiAgICAgIHRoaXMuZ2xvYmFsX2NhbGxiYWNrc1tpXShldmVudE5hbWUsIGRhdGEpO1xuICAgIH1cblxuICAgIHZhciBjYWxsYmFja3MgPSB0aGlzLmNhbGxiYWNrcy5nZXQoZXZlbnROYW1lKTtcbiAgICB2YXIgYXJncyA9IFtdO1xuXG4gICAgaWYgKG1ldGFkYXRhKSB7XG4gICAgICAvLyBpZiB0aGVyZSdzIGEgbWV0YWRhdGEgYXJndW1lbnQsIHdlIG5lZWQgdG8gY2FsbCB0aGUgY2FsbGJhY2sgd2l0aCBib3RoXG4gICAgICAvLyBkYXRhIGFuZCBtZXRhZGF0YSByZWdhcmRsZXNzIG9mIHdoZXRoZXIgZGF0YSBpcyB1bmRlZmluZWRcbiAgICAgIGFyZ3MucHVzaChkYXRhLCBtZXRhZGF0YSk7XG4gICAgfSBlbHNlIGlmIChkYXRhKSB7XG4gICAgICAvLyBtZXRhZGF0YSBpcyB1bmRlZmluZWQsIHNvIHdlIG9ubHkgbmVlZCB0byBjYWxsIHRoZSBjYWxsYmFjayB3aXRoIGRhdGFcbiAgICAgIC8vIGlmIGRhdGEgZXhpc3RzXG4gICAgICBhcmdzLnB1c2goZGF0YSk7XG4gICAgfVxuXG4gICAgaWYgKGNhbGxiYWNrcyAmJiBjYWxsYmFja3MubGVuZ3RoID4gMCkge1xuICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBjYWxsYmFja3MubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgY2FsbGJhY2tzW2ldLmZuLmFwcGx5KGNhbGxiYWNrc1tpXS5jb250ZXh0IHx8IGdsb2JhbCwgYXJncyk7XG4gICAgICB9XG4gICAgfSBlbHNlIGlmICh0aGlzLmZhaWxUaHJvdWdoKSB7XG4gICAgICB0aGlzLmZhaWxUaHJvdWdoKGV2ZW50TmFtZSwgZGF0YSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cbn1cbiIsICJpbXBvcnQgVXRpbCBmcm9tICcuLi91dGlsJztcbmltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCB7IGRlZmF1bHQgYXMgRXZlbnRzRGlzcGF0Y2hlciB9IGZyb20gJy4uL2V2ZW50cy9kaXNwYXRjaGVyJztcbmltcG9ydCBMb2dnZXIgZnJvbSAnLi4vbG9nZ2VyJztcbmltcG9ydCBUcmFuc3BvcnRIb29rcyBmcm9tICcuL3RyYW5zcG9ydF9ob29rcyc7XG5pbXBvcnQgU29ja2V0IGZyb20gJy4uL3NvY2tldCc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcbmltcG9ydCBUaW1lbGluZSBmcm9tICcuLi90aW1lbGluZS90aW1lbGluZSc7XG5pbXBvcnQgVHJhbnNwb3J0Q29ubmVjdGlvbk9wdGlvbnMgZnJvbSAnLi90cmFuc3BvcnRfY29ubmVjdGlvbl9vcHRpb25zJztcblxuLyoqIFByb3ZpZGVzIHVuaXZlcnNhbCBBUEkgZm9yIHRyYW5zcG9ydCBjb25uZWN0aW9ucy5cbiAqXG4gKiBUcmFuc3BvcnQgY29ubmVjdGlvbiBpcyBhIGxvdy1sZXZlbCBvYmplY3QgdGhhdCB3cmFwcyBhIGNvbm5lY3Rpb24gbWV0aG9kXG4gKiBhbmQgZXhwb3NlcyBhIHNpbXBsZSBldmVudGVkIGludGVyZmFjZSBmb3IgdGhlIGNvbm5lY3Rpb24gc3RhdGUgYW5kXG4gKiBtZXNzYWdpbmcuIEl0IGRvZXMgbm90IGltcGxlbWVudCBQdXNoZXItc3BlY2lmaWMgV2ViU29ja2V0IHByb3RvY29sLlxuICpcbiAqIEFkZGl0aW9uYWxseSwgaXQgZmV0Y2hlcyByZXNvdXJjZXMgbmVlZGVkIGZvciB0cmFuc3BvcnQgdG8gd29yayBhbmQgZXhwb3Nlc1xuICogYW4gaW50ZXJmYWNlIGZvciBxdWVyeWluZyB0cmFuc3BvcnQgZmVhdHVyZXMuXG4gKlxuICogU3RhdGVzOlxuICogLSBuZXcgLSBpbml0aWFsIHN0YXRlIGFmdGVyIGNvbnN0cnVjdGluZyB0aGUgb2JqZWN0XG4gKiAtIGluaXRpYWxpemluZyAtIGR1cmluZyBpbml0aWFsaXphdGlvbiBwaGFzZSwgdXN1YWxseSBmZXRjaGluZyByZXNvdXJjZXNcbiAqIC0gaW50aWFsaXplZCAtIHJlYWR5IHRvIGVzdGFibGlzaCBhIGNvbm5lY3Rpb25cbiAqIC0gY29ubmVjdGlvbiAtIHdoZW4gY29ubmVjdGlvbiBpcyBiZWluZyBlc3RhYmxpc2hlZFxuICogLSBvcGVuIC0gd2hlbiBjb25uZWN0aW9uIHJlYWR5IHRvIGJlIHVzZWRcbiAqIC0gY2xvc2VkIC0gYWZ0ZXIgY29ubmVjdGlvbiB3YXMgY2xvc2VkIGJlIGVpdGhlciBzaWRlXG4gKlxuICogRW1pdHM6XG4gKiAtIGVycm9yIC0gYWZ0ZXIgdGhlIGNvbm5lY3Rpb24gcmFpc2VkIGFuIGVycm9yXG4gKlxuICogT3B0aW9uczpcbiAqIC0gdXNlVExTIC0gd2hldGhlciBjb25uZWN0aW9uIHNob3VsZCBiZSBvdmVyIFRMU1xuICogLSBob3N0VExTIC0gaG9zdCB0byBjb25uZWN0IHRvIHdoZW4gY29ubmVjdGlvbiBpcyBvdmVyIFRMU1xuICogLSBob3N0Tm9uVExTIC0gaG9zdCB0byBjb25uZWN0IHRvIHdoZW4gY29ubmVjdGlvbiBpcyBvdmVyIFRMU1xuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBrZXkgYXBwbGljYXRpb24ga2V5XG4gKiBAcGFyYW0ge09iamVjdH0gb3B0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBUcmFuc3BvcnRDb25uZWN0aW9uIGV4dGVuZHMgRXZlbnRzRGlzcGF0Y2hlciB7XG4gIGhvb2tzOiBUcmFuc3BvcnRIb29rcztcbiAgbmFtZTogc3RyaW5nO1xuICBwcmlvcml0eTogbnVtYmVyO1xuICBrZXk6IHN0cmluZztcbiAgb3B0aW9uczogVHJhbnNwb3J0Q29ubmVjdGlvbk9wdGlvbnM7XG4gIHN0YXRlOiBzdHJpbmc7XG4gIHRpbWVsaW5lOiBUaW1lbGluZTtcbiAgYWN0aXZpdHlUaW1lb3V0OiBudW1iZXI7XG4gIGlkOiBudW1iZXI7XG4gIHNvY2tldDogU29ja2V0O1xuICBiZWZvcmVPcGVuOiBGdW5jdGlvbjtcbiAgaW5pdGlhbGl6ZTogRnVuY3Rpb247XG5cbiAgY29uc3RydWN0b3IoXG4gICAgaG9va3M6IFRyYW5zcG9ydEhvb2tzLFxuICAgIG5hbWU6IHN0cmluZyxcbiAgICBwcmlvcml0eTogbnVtYmVyLFxuICAgIGtleTogc3RyaW5nLFxuICAgIG9wdGlvbnM6IFRyYW5zcG9ydENvbm5lY3Rpb25PcHRpb25zXG4gICkge1xuICAgIHN1cGVyKCk7XG4gICAgdGhpcy5pbml0aWFsaXplID0gUnVudGltZS50cmFuc3BvcnRDb25uZWN0aW9uSW5pdGlhbGl6ZXI7XG4gICAgdGhpcy5ob29rcyA9IGhvb2tzO1xuICAgIHRoaXMubmFtZSA9IG5hbWU7XG4gICAgdGhpcy5wcmlvcml0eSA9IHByaW9yaXR5O1xuICAgIHRoaXMua2V5ID0ga2V5O1xuICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnM7XG5cbiAgICB0aGlzLnN0YXRlID0gJ25ldyc7XG4gICAgdGhpcy50aW1lbGluZSA9IG9wdGlvbnMudGltZWxpbmU7XG4gICAgdGhpcy5hY3Rpdml0eVRpbWVvdXQgPSBvcHRpb25zLmFjdGl2aXR5VGltZW91dDtcbiAgICB0aGlzLmlkID0gdGhpcy50aW1lbGluZS5nZW5lcmF0ZVVuaXF1ZUlEKCk7XG4gIH1cblxuICAvKiogQ2hlY2tzIHdoZXRoZXIgdGhlIHRyYW5zcG9ydCBoYW5kbGVzIGFjdGl2aXR5IGNoZWNrcyBieSBpdHNlbGYuXG4gICAqXG4gICAqIEByZXR1cm4ge0Jvb2xlYW59XG4gICAqL1xuICBoYW5kbGVzQWN0aXZpdHlDaGVja3MoKTogYm9vbGVhbiB7XG4gICAgcmV0dXJuIEJvb2xlYW4odGhpcy5ob29rcy5oYW5kbGVzQWN0aXZpdHlDaGVja3MpO1xuICB9XG5cbiAgLyoqIENoZWNrcyB3aGV0aGVyIHRoZSB0cmFuc3BvcnQgc3VwcG9ydHMgdGhlIHBpbmcvcG9uZyBBUEkuXG4gICAqXG4gICAqIEByZXR1cm4ge0Jvb2xlYW59XG4gICAqL1xuICBzdXBwb3J0c1BpbmcoKTogYm9vbGVhbiB7XG4gICAgcmV0dXJuIEJvb2xlYW4odGhpcy5ob29rcy5zdXBwb3J0c1BpbmcpO1xuICB9XG5cbiAgLyoqIFRyaWVzIHRvIGVzdGFibGlzaCBhIGNvbm5lY3Rpb24uXG4gICAqXG4gICAqIEByZXR1cm5zIHtCb29sZWFufSBmYWxzZSBpZiB0cmFuc3BvcnQgaXMgaW4gaW52YWxpZCBzdGF0ZVxuICAgKi9cbiAgY29ubmVjdCgpOiBib29sZWFuIHtcbiAgICBpZiAodGhpcy5zb2NrZXQgfHwgdGhpcy5zdGF0ZSAhPT0gJ2luaXRpYWxpemVkJykge1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIHZhciB1cmwgPSB0aGlzLmhvb2tzLnVybHMuZ2V0SW5pdGlhbCh0aGlzLmtleSwgdGhpcy5vcHRpb25zKTtcbiAgICB0cnkge1xuICAgICAgdGhpcy5zb2NrZXQgPSB0aGlzLmhvb2tzLmdldFNvY2tldCh1cmwsIHRoaXMub3B0aW9ucyk7XG4gICAgfSBjYXRjaCAoZSkge1xuICAgICAgVXRpbC5kZWZlcigoKSA9PiB7XG4gICAgICAgIHRoaXMub25FcnJvcihlKTtcbiAgICAgICAgdGhpcy5jaGFuZ2VTdGF0ZSgnY2xvc2VkJyk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG5cbiAgICB0aGlzLmJpbmRMaXN0ZW5lcnMoKTtcblxuICAgIExvZ2dlci5kZWJ1ZygnQ29ubmVjdGluZycsIHsgdHJhbnNwb3J0OiB0aGlzLm5hbWUsIHVybCB9KTtcbiAgICB0aGlzLmNoYW5nZVN0YXRlKCdjb25uZWN0aW5nJyk7XG4gICAgcmV0dXJuIHRydWU7XG4gIH1cblxuICAvKiogQ2xvc2VzIHRoZSBjb25uZWN0aW9uLlxuICAgKlxuICAgKiBAcmV0dXJuIHtCb29sZWFufSB0cnVlIGlmIHRoZXJlIHdhcyBhIGNvbm5lY3Rpb24gdG8gY2xvc2VcbiAgICovXG4gIGNsb3NlKCk6IGJvb2xlYW4ge1xuICAgIGlmICh0aGlzLnNvY2tldCkge1xuICAgICAgdGhpcy5zb2NrZXQuY2xvc2UoKTtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG5cbiAgLyoqIFNlbmRzIGRhdGEgb3ZlciB0aGUgb3BlbiBjb25uZWN0aW9uLlxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gZGF0YVxuICAgKiBAcmV0dXJuIHtCb29sZWFufSB0cnVlIG9ubHkgd2hlbiBpbiB0aGUgXCJvcGVuXCIgc3RhdGVcbiAgICovXG4gIHNlbmQoZGF0YTogYW55KTogYm9vbGVhbiB7XG4gICAgaWYgKHRoaXMuc3RhdGUgPT09ICdvcGVuJykge1xuICAgICAgLy8gV29ya2Fyb3VuZCBmb3IgTW9iaWxlU2FmYXJpIGJ1ZyAoc2VlIGh0dHBzOi8vZ2lzdC5naXRodWIuY29tLzIwNTIwMDYpXG4gICAgICBVdGlsLmRlZmVyKCgpID0+IHtcbiAgICAgICAgaWYgKHRoaXMuc29ja2V0KSB7XG4gICAgICAgICAgdGhpcy5zb2NrZXQuc2VuZChkYXRhKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cbiAgfVxuXG4gIC8qKiBTZW5kcyBhIHBpbmcgaWYgdGhlIGNvbm5lY3Rpb24gaXMgb3BlbiBhbmQgdHJhbnNwb3J0IHN1cHBvcnRzIGl0LiAqL1xuICBwaW5nKCkge1xuICAgIGlmICh0aGlzLnN0YXRlID09PSAnb3BlbicgJiYgdGhpcy5zdXBwb3J0c1BpbmcoKSkge1xuICAgICAgdGhpcy5zb2NrZXQucGluZygpO1xuICAgIH1cbiAgfVxuXG4gIHByaXZhdGUgb25PcGVuKCkge1xuICAgIGlmICh0aGlzLmhvb2tzLmJlZm9yZU9wZW4pIHtcbiAgICAgIHRoaXMuaG9va3MuYmVmb3JlT3BlbihcbiAgICAgICAgdGhpcy5zb2NrZXQsXG4gICAgICAgIHRoaXMuaG9va3MudXJscy5nZXRQYXRoKHRoaXMua2V5LCB0aGlzLm9wdGlvbnMpXG4gICAgICApO1xuICAgIH1cbiAgICB0aGlzLmNoYW5nZVN0YXRlKCdvcGVuJyk7XG4gICAgdGhpcy5zb2NrZXQub25vcGVuID0gdW5kZWZpbmVkO1xuICB9XG5cbiAgcHJpdmF0ZSBvbkVycm9yKGVycm9yKSB7XG4gICAgdGhpcy5lbWl0KCdlcnJvcicsIHsgdHlwZTogJ1dlYlNvY2tldEVycm9yJywgZXJyb3I6IGVycm9yIH0pO1xuICAgIHRoaXMudGltZWxpbmUuZXJyb3IodGhpcy5idWlsZFRpbWVsaW5lTWVzc2FnZSh7IGVycm9yOiBlcnJvci50b1N0cmluZygpIH0pKTtcbiAgfVxuXG4gIHByaXZhdGUgb25DbG9zZShjbG9zZUV2ZW50PzogYW55KSB7XG4gICAgaWYgKGNsb3NlRXZlbnQpIHtcbiAgICAgIHRoaXMuY2hhbmdlU3RhdGUoJ2Nsb3NlZCcsIHtcbiAgICAgICAgY29kZTogY2xvc2VFdmVudC5jb2RlLFxuICAgICAgICByZWFzb246IGNsb3NlRXZlbnQucmVhc29uLFxuICAgICAgICB3YXNDbGVhbjogY2xvc2VFdmVudC53YXNDbGVhblxuICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuY2hhbmdlU3RhdGUoJ2Nsb3NlZCcpO1xuICAgIH1cbiAgICB0aGlzLnVuYmluZExpc3RlbmVycygpO1xuICAgIHRoaXMuc29ja2V0ID0gdW5kZWZpbmVkO1xuICB9XG5cbiAgcHJpdmF0ZSBvbk1lc3NhZ2UobWVzc2FnZSkge1xuICAgIHRoaXMuZW1pdCgnbWVzc2FnZScsIG1lc3NhZ2UpO1xuICB9XG5cbiAgcHJpdmF0ZSBvbkFjdGl2aXR5KCkge1xuICAgIHRoaXMuZW1pdCgnYWN0aXZpdHknKTtcbiAgfVxuXG4gIHByaXZhdGUgYmluZExpc3RlbmVycygpIHtcbiAgICB0aGlzLnNvY2tldC5vbm9wZW4gPSAoKSA9PiB7XG4gICAgICB0aGlzLm9uT3BlbigpO1xuICAgIH07XG4gICAgdGhpcy5zb2NrZXQub25lcnJvciA9IGVycm9yID0+IHtcbiAgICAgIHRoaXMub25FcnJvcihlcnJvcik7XG4gICAgfTtcbiAgICB0aGlzLnNvY2tldC5vbmNsb3NlID0gY2xvc2VFdmVudCA9PiB7XG4gICAgICB0aGlzLm9uQ2xvc2UoY2xvc2VFdmVudCk7XG4gICAgfTtcbiAgICB0aGlzLnNvY2tldC5vbm1lc3NhZ2UgPSBtZXNzYWdlID0+IHtcbiAgICAgIHRoaXMub25NZXNzYWdlKG1lc3NhZ2UpO1xuICAgIH07XG5cbiAgICBpZiAodGhpcy5zdXBwb3J0c1BpbmcoKSkge1xuICAgICAgdGhpcy5zb2NrZXQub25hY3Rpdml0eSA9ICgpID0+IHtcbiAgICAgICAgdGhpcy5vbkFjdGl2aXR5KCk7XG4gICAgICB9O1xuICAgIH1cbiAgfVxuXG4gIHByaXZhdGUgdW5iaW5kTGlzdGVuZXJzKCkge1xuICAgIGlmICh0aGlzLnNvY2tldCkge1xuICAgICAgdGhpcy5zb2NrZXQub25vcGVuID0gdW5kZWZpbmVkO1xuICAgICAgdGhpcy5zb2NrZXQub25lcnJvciA9IHVuZGVmaW5lZDtcbiAgICAgIHRoaXMuc29ja2V0Lm9uY2xvc2UgPSB1bmRlZmluZWQ7XG4gICAgICB0aGlzLnNvY2tldC5vbm1lc3NhZ2UgPSB1bmRlZmluZWQ7XG4gICAgICBpZiAodGhpcy5zdXBwb3J0c1BpbmcoKSkge1xuICAgICAgICB0aGlzLnNvY2tldC5vbmFjdGl2aXR5ID0gdW5kZWZpbmVkO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHByaXZhdGUgY2hhbmdlU3RhdGUoc3RhdGU6IHN0cmluZywgcGFyYW1zPzogYW55KSB7XG4gICAgdGhpcy5zdGF0ZSA9IHN0YXRlO1xuICAgIHRoaXMudGltZWxpbmUuaW5mbyhcbiAgICAgIHRoaXMuYnVpbGRUaW1lbGluZU1lc3NhZ2Uoe1xuICAgICAgICBzdGF0ZTogc3RhdGUsXG4gICAgICAgIHBhcmFtczogcGFyYW1zXG4gICAgICB9KVxuICAgICk7XG4gICAgdGhpcy5lbWl0KHN0YXRlLCBwYXJhbXMpO1xuICB9XG5cbiAgYnVpbGRUaW1lbGluZU1lc3NhZ2UobWVzc2FnZSk6IGFueSB7XG4gICAgcmV0dXJuIENvbGxlY3Rpb25zLmV4dGVuZCh7IGNpZDogdGhpcy5pZCB9LCBtZXNzYWdlKTtcbiAgfVxufVxuIiwgImltcG9ydCBGYWN0b3J5IGZyb20gJy4uL3V0aWxzL2ZhY3RvcnknO1xuaW1wb3J0IFRyYW5zcG9ydEhvb2tzIGZyb20gJy4vdHJhbnNwb3J0X2hvb2tzJztcbmltcG9ydCBUcmFuc3BvcnRDb25uZWN0aW9uIGZyb20gJy4vdHJhbnNwb3J0X2Nvbm5lY3Rpb24nO1xuaW1wb3J0IFRyYW5zcG9ydENvbm5lY3Rpb25PcHRpb25zIGZyb20gJy4vdHJhbnNwb3J0X2Nvbm5lY3Rpb25fb3B0aW9ucyc7XG5cbi8qKiBQcm92aWRlcyBpbnRlcmZhY2UgZm9yIHRyYW5zcG9ydCBjb25uZWN0aW9uIGluc3RhbnRpYXRpb24uXG4gKlxuICogVGFrZXMgdHJhbnNwb3J0LXNwZWNpZmljIGhvb2tzIGFzIHRoZSBvbmx5IGFyZ3VtZW50LCB3aGljaCBhbGxvdyBjaGVja2luZ1xuICogZm9yIHRyYW5zcG9ydCBzdXBwb3J0IGFuZCBjcmVhdGluZyBpdHMgY29ubmVjdGlvbnMuXG4gKlxuICogU3VwcG9ydGVkIGhvb2tzOiAqIC0gZmlsZSAtIHRoZSBuYW1lIG9mIHRoZSBmaWxlIHRvIGJlIGZldGNoZWQgZHVyaW5nIGluaXRpYWxpemF0aW9uXG4gKiAtIHVybHMgLSBVUkwgc2NoZW1lIHRvIGJlIHVzZWQgYnkgdHJhbnNwb3J0XG4gKiAtIGhhbmRsZXNBY3Rpdml0eUNoZWNrIC0gdHJ1ZSB3aGVuIHRoZSB0cmFuc3BvcnQgaGFuZGxlcyBhY3Rpdml0eSBjaGVja3NcbiAqIC0gc3VwcG9ydHNQaW5nIC0gdHJ1ZSB3aGVuIHRoZSB0cmFuc3BvcnQgaGFzIGEgcGluZy9hY3Rpdml0eSBBUElcbiAqIC0gaXNTdXBwb3J0ZWQgLSB0ZWxscyB3aGV0aGVyIHRoZSB0cmFuc3BvcnQgaXMgc3VwcG9ydGVkIGluIHRoZSBlbnZpcm9ubWVudFxuICogLSBnZXRTb2NrZXQgLSBjcmVhdGVzIGEgV2ViU29ja2V0LWNvbXBhdGlibGUgdHJhbnNwb3J0IHNvY2tldFxuICpcbiAqIFNlZSB0cmFuc3BvcnRzLmpzIGZvciBzcGVjaWZpYyBpbXBsZW1lbnRhdGlvbnMuXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IGhvb2tzIG9iamVjdCBjb250YWluaW5nIGFsbCBuZWVkZWQgdHJhbnNwb3J0IGhvb2tzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFRyYW5zcG9ydCB7XG4gIGhvb2tzOiBUcmFuc3BvcnRIb29rcztcblxuICBjb25zdHJ1Y3Rvcihob29rczogVHJhbnNwb3J0SG9va3MpIHtcbiAgICB0aGlzLmhvb2tzID0gaG9va3M7XG4gIH1cblxuICAvKiogUmV0dXJucyB3aGV0aGVyIHRoZSB0cmFuc3BvcnQgaXMgc3VwcG9ydGVkIGluIHRoZSBlbnZpcm9ubWVudC5cbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IGVudnJvbm1lbnQgdGUgZW52aXJvbm1lbnQgZGV0YWlscyAoZW5jcnlwdGlvbiwgc2V0dGluZ3MpXG4gICAqIEByZXR1cm5zIHtCb29sZWFufSB0cnVlIHdoZW4gdGhlIHRyYW5zcG9ydCBpcyBzdXBwb3J0ZWRcbiAgICovXG4gIGlzU3VwcG9ydGVkKGVudmlyb25tZW50OiBhbnkpOiBib29sZWFuIHtcbiAgICByZXR1cm4gdGhpcy5ob29rcy5pc1N1cHBvcnRlZChlbnZpcm9ubWVudCk7XG4gIH1cblxuICAvKiogQ3JlYXRlcyBhIHRyYW5zcG9ydCBjb25uZWN0aW9uLlxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICAgKiBAcGFyYW0ge051bWJlcn0gcHJpb3JpdHlcbiAgICogQHBhcmFtIHtTdHJpbmd9IGtleSB0aGUgYXBwbGljYXRpb24ga2V5XG4gICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zXG4gICAqIEByZXR1cm5zIHtUcmFuc3BvcnRDb25uZWN0aW9ufVxuICAgKi9cbiAgY3JlYXRlQ29ubmVjdGlvbihcbiAgICBuYW1lOiBzdHJpbmcsXG4gICAgcHJpb3JpdHk6IG51bWJlcixcbiAgICBrZXk6IHN0cmluZyxcbiAgICBvcHRpb25zOiBhbnlcbiAgKTogVHJhbnNwb3J0Q29ubmVjdGlvbiB7XG4gICAgcmV0dXJuIG5ldyBUcmFuc3BvcnRDb25uZWN0aW9uKHRoaXMuaG9va3MsIG5hbWUsIHByaW9yaXR5LCBrZXksIG9wdGlvbnMpO1xuICB9XG59XG4iLCAiaW1wb3J0ICogYXMgVVJMU2NoZW1lcyBmcm9tICdjb3JlL3RyYW5zcG9ydHMvdXJsX3NjaGVtZXMnO1xuaW1wb3J0IFVSTFNjaGVtZSBmcm9tICdjb3JlL3RyYW5zcG9ydHMvdXJsX3NjaGVtZSc7XG5pbXBvcnQgVHJhbnNwb3J0IGZyb20gJ2NvcmUvdHJhbnNwb3J0cy90cmFuc3BvcnQnO1xuaW1wb3J0IFV0aWwgZnJvbSAnY29yZS91dGlsJztcbmltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJ2NvcmUvdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IFRyYW5zcG9ydEhvb2tzIGZyb20gJ2NvcmUvdHJhbnNwb3J0cy90cmFuc3BvcnRfaG9va3MnO1xuaW1wb3J0IFRyYW5zcG9ydHNUYWJsZSBmcm9tICdjb3JlL3RyYW5zcG9ydHMvdHJhbnNwb3J0c190YWJsZSc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcblxuLyoqIFdlYlNvY2tldCB0cmFuc3BvcnQuXG4gKlxuICogVXNlcyBuYXRpdmUgV2ViU29ja2V0IGltcGxlbWVudGF0aW9uLCBpbmNsdWRpbmcgTW96V2ViU29ja2V0IHN1cHBvcnRlZCBieVxuICogZWFybGllciBGaXJlZm94IHZlcnNpb25zLlxuICovXG52YXIgV1NUcmFuc3BvcnQgPSBuZXcgVHJhbnNwb3J0KDxUcmFuc3BvcnRIb29rcz57XG4gIHVybHM6IFVSTFNjaGVtZXMud3MsXG4gIGhhbmRsZXNBY3Rpdml0eUNoZWNrczogZmFsc2UsXG4gIHN1cHBvcnRzUGluZzogZmFsc2UsXG5cbiAgaXNJbml0aWFsaXplZDogZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIEJvb2xlYW4oUnVudGltZS5nZXRXZWJTb2NrZXRBUEkoKSk7XG4gIH0sXG4gIGlzU3VwcG9ydGVkOiBmdW5jdGlvbigpOiBib29sZWFuIHtcbiAgICByZXR1cm4gQm9vbGVhbihSdW50aW1lLmdldFdlYlNvY2tldEFQSSgpKTtcbiAgfSxcbiAgZ2V0U29ja2V0OiBmdW5jdGlvbih1cmwpIHtcbiAgICByZXR1cm4gUnVudGltZS5jcmVhdGVXZWJTb2NrZXQodXJsKTtcbiAgfVxufSk7XG5cbnZhciBodHRwQ29uZmlndXJhdGlvbiA9IHtcbiAgdXJsczogVVJMU2NoZW1lcy5odHRwLFxuICBoYW5kbGVzQWN0aXZpdHlDaGVja3M6IGZhbHNlLFxuICBzdXBwb3J0c1Bpbmc6IHRydWUsXG4gIGlzSW5pdGlhbGl6ZWQ6IGZ1bmN0aW9uKCkge1xuICAgIHJldHVybiB0cnVlO1xuICB9XG59O1xuXG5leHBvcnQgdmFyIHN0cmVhbWluZ0NvbmZpZ3VyYXRpb24gPSBDb2xsZWN0aW9ucy5leHRlbmQoXG4gIHtcbiAgICBnZXRTb2NrZXQ6IGZ1bmN0aW9uKHVybCkge1xuICAgICAgcmV0dXJuIFJ1bnRpbWUuSFRUUEZhY3RvcnkuY3JlYXRlU3RyZWFtaW5nU29ja2V0KHVybCk7XG4gICAgfVxuICB9LFxuICBodHRwQ29uZmlndXJhdGlvblxuKTtcbmV4cG9ydCB2YXIgcG9sbGluZ0NvbmZpZ3VyYXRpb24gPSBDb2xsZWN0aW9ucy5leHRlbmQoXG4gIHtcbiAgICBnZXRTb2NrZXQ6IGZ1bmN0aW9uKHVybCkge1xuICAgICAgcmV0dXJuIFJ1bnRpbWUuSFRUUEZhY3RvcnkuY3JlYXRlUG9sbGluZ1NvY2tldCh1cmwpO1xuICAgIH1cbiAgfSxcbiAgaHR0cENvbmZpZ3VyYXRpb25cbik7XG5cbnZhciB4aHJDb25maWd1cmF0aW9uID0ge1xuICBpc1N1cHBvcnRlZDogZnVuY3Rpb24oKTogYm9vbGVhbiB7XG4gICAgcmV0dXJuIFJ1bnRpbWUuaXNYSFJTdXBwb3J0ZWQoKTtcbiAgfVxufTtcblxuLyoqIEhUVFAgc3RyZWFtaW5nIHRyYW5zcG9ydCB1c2luZyBDT1JTLWVuYWJsZWQgWE1MSHR0cFJlcXVlc3QuICovXG52YXIgWEhSU3RyZWFtaW5nVHJhbnNwb3J0ID0gbmV3IFRyYW5zcG9ydChcbiAgPFRyYW5zcG9ydEhvb2tzPihcbiAgICBDb2xsZWN0aW9ucy5leHRlbmQoe30sIHN0cmVhbWluZ0NvbmZpZ3VyYXRpb24sIHhockNvbmZpZ3VyYXRpb24pXG4gIClcbik7XG5cbi8qKiBIVFRQIGxvbmctcG9sbGluZyB0cmFuc3BvcnQgdXNpbmcgQ09SUy1lbmFibGVkIFhNTEh0dHBSZXF1ZXN0LiAqL1xudmFyIFhIUlBvbGxpbmdUcmFuc3BvcnQgPSBuZXcgVHJhbnNwb3J0KFxuICA8VHJhbnNwb3J0SG9va3M+Q29sbGVjdGlvbnMuZXh0ZW5kKHt9LCBwb2xsaW5nQ29uZmlndXJhdGlvbiwgeGhyQ29uZmlndXJhdGlvbilcbik7XG5cbnZhciBUcmFuc3BvcnRzOiBUcmFuc3BvcnRzVGFibGUgPSB7XG4gIHdzOiBXU1RyYW5zcG9ydCxcbiAgeGhyX3N0cmVhbWluZzogWEhSU3RyZWFtaW5nVHJhbnNwb3J0LFxuICB4aHJfcG9sbGluZzogWEhSUG9sbGluZ1RyYW5zcG9ydFxufTtcblxuZXhwb3J0IGRlZmF1bHQgVHJhbnNwb3J0cztcbiIsICJpbXBvcnQge1xuICBkZWZhdWx0IGFzIFRyYW5zcG9ydHMsXG4gIHN0cmVhbWluZ0NvbmZpZ3VyYXRpb24sXG4gIHBvbGxpbmdDb25maWd1cmF0aW9uXG59IGZyb20gJ2lzb21vcnBoaWMvdHJhbnNwb3J0cy90cmFuc3BvcnRzJztcbmltcG9ydCBUcmFuc3BvcnQgZnJvbSAnY29yZS90cmFuc3BvcnRzL3RyYW5zcG9ydCc7XG5pbXBvcnQgVHJhbnNwb3J0SG9va3MgZnJvbSAnY29yZS90cmFuc3BvcnRzL3RyYW5zcG9ydF9ob29rcyc7XG5pbXBvcnQgKiBhcyBVUkxTY2hlbWVzIGZyb20gJ2NvcmUvdHJhbnNwb3J0cy91cmxfc2NoZW1lcyc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcbmltcG9ydCB7IERlcGVuZGVuY2llcyB9IGZyb20gJy4uL2RvbS9kZXBlbmRlbmNpZXMnO1xuaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnY29yZS91dGlscy9jb2xsZWN0aW9ucyc7XG5cbnZhciBTb2NrSlNUcmFuc3BvcnQgPSBuZXcgVHJhbnNwb3J0KDxUcmFuc3BvcnRIb29rcz57XG4gIGZpbGU6ICdzb2NranMnLFxuICB1cmxzOiBVUkxTY2hlbWVzLnNvY2tqcyxcbiAgaGFuZGxlc0FjdGl2aXR5Q2hlY2tzOiB0cnVlLFxuICBzdXBwb3J0c1Bpbmc6IGZhbHNlLFxuXG4gIGlzU3VwcG9ydGVkOiBmdW5jdGlvbigpIHtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfSxcbiAgaXNJbml0aWFsaXplZDogZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIHdpbmRvdy5Tb2NrSlMgIT09IHVuZGVmaW5lZDtcbiAgfSxcbiAgZ2V0U29ja2V0OiBmdW5jdGlvbih1cmwsIG9wdGlvbnMpIHtcbiAgICByZXR1cm4gbmV3IHdpbmRvdy5Tb2NrSlModXJsLCBudWxsLCB7XG4gICAgICBqc19wYXRoOiBEZXBlbmRlbmNpZXMuZ2V0UGF0aCgnc29ja2pzJywge1xuICAgICAgICB1c2VUTFM6IG9wdGlvbnMudXNlVExTXG4gICAgICB9KSxcbiAgICAgIGlnbm9yZV9udWxsX29yaWdpbjogb3B0aW9ucy5pZ25vcmVOdWxsT3JpZ2luXG4gICAgfSk7XG4gIH0sXG4gIGJlZm9yZU9wZW46IGZ1bmN0aW9uKHNvY2tldCwgcGF0aCkge1xuICAgIHNvY2tldC5zZW5kKFxuICAgICAgSlNPTi5zdHJpbmdpZnkoe1xuICAgICAgICBwYXRoOiBwYXRoXG4gICAgICB9KVxuICAgICk7XG4gIH1cbn0pO1xuXG52YXIgeGRyQ29uZmlndXJhdGlvbiA9IHtcbiAgaXNTdXBwb3J0ZWQ6IGZ1bmN0aW9uKGVudmlyb25tZW50KTogYm9vbGVhbiB7XG4gICAgdmFyIHllcyA9IFJ1bnRpbWUuaXNYRFJTdXBwb3J0ZWQoZW52aXJvbm1lbnQudXNlVExTKTtcbiAgICByZXR1cm4geWVzO1xuICB9XG59O1xuXG4vKiogSFRUUCBzdHJlYW1pbmcgdHJhbnNwb3J0IHVzaW5nIFhEb21haW5SZXF1ZXN0IChJRSA4LDkpLiAqL1xudmFyIFhEUlN0cmVhbWluZ1RyYW5zcG9ydCA9IG5ldyBUcmFuc3BvcnQoXG4gIDxUcmFuc3BvcnRIb29rcz4oXG4gICAgQ29sbGVjdGlvbnMuZXh0ZW5kKHt9LCBzdHJlYW1pbmdDb25maWd1cmF0aW9uLCB4ZHJDb25maWd1cmF0aW9uKVxuICApXG4pO1xuXG4vKiogSFRUUCBsb25nLXBvbGxpbmcgdHJhbnNwb3J0IHVzaW5nIFhEb21haW5SZXF1ZXN0IChJRSA4LDkpLiAqL1xudmFyIFhEUlBvbGxpbmdUcmFuc3BvcnQgPSBuZXcgVHJhbnNwb3J0KFxuICA8VHJhbnNwb3J0SG9va3M+Q29sbGVjdGlvbnMuZXh0ZW5kKHt9LCBwb2xsaW5nQ29uZmlndXJhdGlvbiwgeGRyQ29uZmlndXJhdGlvbilcbik7XG5cblRyYW5zcG9ydHMueGRyX3N0cmVhbWluZyA9IFhEUlN0cmVhbWluZ1RyYW5zcG9ydDtcblRyYW5zcG9ydHMueGRyX3BvbGxpbmcgPSBYRFJQb2xsaW5nVHJhbnNwb3J0O1xuVHJhbnNwb3J0cy5zb2NranMgPSBTb2NrSlNUcmFuc3BvcnQ7XG5cbmV4cG9ydCBkZWZhdWx0IFRyYW5zcG9ydHM7XG4iLCAiaW1wb3J0IFJlYWNoYWJpbGl0eSBmcm9tICdjb3JlL3JlYWNoYWJpbGl0eSc7XG5pbXBvcnQgeyBkZWZhdWx0IGFzIEV2ZW50c0Rpc3BhdGNoZXIgfSBmcm9tICdjb3JlL2V2ZW50cy9kaXNwYXRjaGVyJztcblxuLyoqIFJlYWxseSBiYXNpYyBpbnRlcmZhY2UgcHJvdmlkaW5nIG5ldHdvcmsgYXZhaWxhYmlsaXR5IGluZm8uXG4gKlxuICogRW1pdHM6XG4gKiAtIG9ubGluZSAtIHdoZW4gYnJvd3NlciBnb2VzIG9ubGluZVxuICogLSBvZmZsaW5lIC0gd2hlbiBicm93c2VyIGdvZXMgb2ZmbGluZVxuICovXG5leHBvcnQgY2xhc3MgTmV0SW5mbyBleHRlbmRzIEV2ZW50c0Rpc3BhdGNoZXIgaW1wbGVtZW50cyBSZWFjaGFiaWxpdHkge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICBzdXBlcigpO1xuICAgIHZhciBzZWxmID0gdGhpcztcbiAgICAvLyBUaGlzIGlzIG9rYXksIGFzIElFIGRvZXNuJ3Qgc3VwcG9ydCB0aGlzIHN0dWZmIGFueXdheS5cbiAgICBpZiAod2luZG93LmFkZEV2ZW50TGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXG4gICAgICAgICdvbmxpbmUnLFxuICAgICAgICBmdW5jdGlvbigpIHtcbiAgICAgICAgICBzZWxmLmVtaXQoJ29ubGluZScpO1xuICAgICAgICB9LFxuICAgICAgICBmYWxzZVxuICAgICAgKTtcbiAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFxuICAgICAgICAnb2ZmbGluZScsXG4gICAgICAgIGZ1bmN0aW9uKCkge1xuICAgICAgICAgIHNlbGYuZW1pdCgnb2ZmbGluZScpO1xuICAgICAgICB9LFxuICAgICAgICBmYWxzZVxuICAgICAgKTtcbiAgICB9XG4gIH1cblxuICAvKiogUmV0dXJucyB3aGV0aGVyIGJyb3dzZXIgaXMgb25saW5lIG9yIG5vdFxuICAgKlxuICAgKiBPZmZsaW5lIG1lYW5zIGRlZmluaXRlbHkgb2ZmbGluZSAobm8gY29ubmVjdGlvbiB0byByb3V0ZXIpLlxuICAgKiBJbnZlcnNlIGRvZXMgTk9UIG1lYW4gZGVmaW5pdGVseSBvbmxpbmUgKG9ubHkgY3VycmVudGx5IHN1cHBvcnRlZCBpbiBTYWZhcmlcbiAgICogYW5kIGV2ZW4gdGhlcmUgb25seSBtZWFucyB0aGUgZGV2aWNlIGhhcyBhIGNvbm5lY3Rpb24gdG8gdGhlIHJvdXRlcikuXG4gICAqXG4gICAqIEByZXR1cm4ge0Jvb2xlYW59XG4gICAqL1xuICBpc09ubGluZSgpOiBib29sZWFuIHtcbiAgICBpZiAod2luZG93Lm5hdmlnYXRvci5vbkxpbmUgPT09IHVuZGVmaW5lZCkge1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiB3aW5kb3cubmF2aWdhdG9yLm9uTGluZTtcbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IHZhciBOZXR3b3JrID0gbmV3IE5ldEluZm8oKTtcbiIsICJpbXBvcnQgVXRpbCBmcm9tICcuLi91dGlsJztcbmltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBUcmFuc3BvcnRNYW5hZ2VyIGZyb20gJy4vdHJhbnNwb3J0X21hbmFnZXInO1xuaW1wb3J0IFRyYW5zcG9ydENvbm5lY3Rpb24gZnJvbSAnLi90cmFuc3BvcnRfY29ubmVjdGlvbic7XG5pbXBvcnQgVHJhbnNwb3J0IGZyb20gJy4vdHJhbnNwb3J0JztcbmltcG9ydCBQaW5nRGVsYXlPcHRpb25zIGZyb20gJy4vcGluZ19kZWxheV9vcHRpb25zJztcblxuLyoqIENyZWF0ZXMgdHJhbnNwb3J0IGNvbm5lY3Rpb25zIG1vbml0b3JlZCBieSBhIHRyYW5zcG9ydCBtYW5hZ2VyLlxuICpcbiAqIFdoZW4gYSB0cmFuc3BvcnQgaXMgY2xvc2VkLCBpdCBtaWdodCBtZWFuIHRoZSBlbnZpcm9ubWVudCBkb2VzIG5vdCBzdXBwb3J0XG4gKiBpdC4gSXQncyBwb3NzaWJsZSB0aGF0IG1lc3NhZ2VzIGdldCBzdHVjayBpbiBhbiBpbnRlcm1lZGlhdGUgYnVmZmVyIG9yXG4gKiBwcm94aWVzIHRlcm1pbmF0ZSBpbmFjdGl2ZSBjb25uZWN0aW9ucy4gVG8gY29tYmF0IHRoZXNlIHByb2JsZW1zLFxuICogYXNzaXN0YW50cyBtb25pdG9yIHRoZSBjb25uZWN0aW9uIGxpZmV0aW1lLCByZXBvcnQgdW5jbGVhbiBleGl0cyBhbmRcbiAqIGFkanVzdCBwaW5nIHRpbWVvdXRzIHRvIGtlZXAgdGhlIGNvbm5lY3Rpb24gYWN0aXZlLiBUaGUgZGVjaXNpb24gdG8gZGlzYWJsZVxuICogYSB0cmFuc3BvcnQgaXMgdGhlIG1hbmFnZXIncyByZXNwb25zaWJpbGl0eS5cbiAqXG4gKiBAcGFyYW0ge1RyYW5zcG9ydE1hbmFnZXJ9IG1hbmFnZXJcbiAqIEBwYXJhbSB7VHJhbnNwb3J0Q29ubmVjdGlvbn0gdHJhbnNwb3J0XG4gKiBAcGFyYW0ge09iamVjdH0gb3B0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBBc3Npc3RhbnRUb1RoZVRyYW5zcG9ydE1hbmFnZXIge1xuICBtYW5hZ2VyOiBUcmFuc3BvcnRNYW5hZ2VyO1xuICB0cmFuc3BvcnQ6IFRyYW5zcG9ydDtcbiAgbWluUGluZ0RlbGF5OiBudW1iZXI7XG4gIG1heFBpbmdEZWxheTogbnVtYmVyO1xuICBwaW5nRGVsYXk6IG51bWJlcjtcblxuICBjb25zdHJ1Y3RvcihcbiAgICBtYW5hZ2VyOiBUcmFuc3BvcnRNYW5hZ2VyLFxuICAgIHRyYW5zcG9ydDogVHJhbnNwb3J0LFxuICAgIG9wdGlvbnM6IFBpbmdEZWxheU9wdGlvbnNcbiAgKSB7XG4gICAgdGhpcy5tYW5hZ2VyID0gbWFuYWdlcjtcbiAgICB0aGlzLnRyYW5zcG9ydCA9IHRyYW5zcG9ydDtcbiAgICB0aGlzLm1pblBpbmdEZWxheSA9IG9wdGlvbnMubWluUGluZ0RlbGF5O1xuICAgIHRoaXMubWF4UGluZ0RlbGF5ID0gb3B0aW9ucy5tYXhQaW5nRGVsYXk7XG4gICAgdGhpcy5waW5nRGVsYXkgPSB1bmRlZmluZWQ7XG4gIH1cblxuICAvKiogQ3JlYXRlcyBhIHRyYW5zcG9ydCBjb25uZWN0aW9uLlxuICAgKlxuICAgKiBUaGlzIGZ1bmN0aW9uIGhhcyB0aGUgc2FtZSBBUEkgYXMgVHJhbnNwb3J0I2NyZWF0ZUNvbm5lY3Rpb24uXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBwcmlvcml0eVxuICAgKiBAcGFyYW0ge1N0cmluZ30ga2V5IHRoZSBhcHBsaWNhdGlvbiBrZXlcbiAgICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAgICogQHJldHVybnMge1RyYW5zcG9ydENvbm5lY3Rpb259XG4gICAqL1xuICBjcmVhdGVDb25uZWN0aW9uKFxuICAgIG5hbWU6IHN0cmluZyxcbiAgICBwcmlvcml0eTogbnVtYmVyLFxuICAgIGtleTogc3RyaW5nLFxuICAgIG9wdGlvbnM6IE9iamVjdFxuICApOiBUcmFuc3BvcnRDb25uZWN0aW9uIHtcbiAgICBvcHRpb25zID0gQ29sbGVjdGlvbnMuZXh0ZW5kKHt9LCBvcHRpb25zLCB7XG4gICAgICBhY3Rpdml0eVRpbWVvdXQ6IHRoaXMucGluZ0RlbGF5XG4gICAgfSk7XG4gICAgdmFyIGNvbm5lY3Rpb24gPSB0aGlzLnRyYW5zcG9ydC5jcmVhdGVDb25uZWN0aW9uKFxuICAgICAgbmFtZSxcbiAgICAgIHByaW9yaXR5LFxuICAgICAga2V5LFxuICAgICAgb3B0aW9uc1xuICAgICk7XG5cbiAgICB2YXIgb3BlblRpbWVzdGFtcCA9IG51bGw7XG5cbiAgICB2YXIgb25PcGVuID0gZnVuY3Rpb24oKSB7XG4gICAgICBjb25uZWN0aW9uLnVuYmluZCgnb3BlbicsIG9uT3Blbik7XG4gICAgICBjb25uZWN0aW9uLmJpbmQoJ2Nsb3NlZCcsIG9uQ2xvc2VkKTtcbiAgICAgIG9wZW5UaW1lc3RhbXAgPSBVdGlsLm5vdygpO1xuICAgIH07XG4gICAgdmFyIG9uQ2xvc2VkID0gY2xvc2VFdmVudCA9PiB7XG4gICAgICBjb25uZWN0aW9uLnVuYmluZCgnY2xvc2VkJywgb25DbG9zZWQpO1xuXG4gICAgICBpZiAoY2xvc2VFdmVudC5jb2RlID09PSAxMDAyIHx8IGNsb3NlRXZlbnQuY29kZSA9PT0gMTAwMykge1xuICAgICAgICAvLyB3ZSBkb24ndCB3YW50IHRvIHVzZSB0cmFuc3BvcnRzIG5vdCBvYmV5aW5nIHRoZSBwcm90b2NvbFxuICAgICAgICB0aGlzLm1hbmFnZXIucmVwb3J0RGVhdGgoKTtcbiAgICAgIH0gZWxzZSBpZiAoIWNsb3NlRXZlbnQud2FzQ2xlYW4gJiYgb3BlblRpbWVzdGFtcCkge1xuICAgICAgICAvLyByZXBvcnQgZGVhdGhzIG9ubHkgZm9yIHNob3J0LWxpdmluZyB0cmFuc3BvcnRcbiAgICAgICAgdmFyIGxpZmVzcGFuID0gVXRpbC5ub3coKSAtIG9wZW5UaW1lc3RhbXA7XG4gICAgICAgIGlmIChsaWZlc3BhbiA8IDIgKiB0aGlzLm1heFBpbmdEZWxheSkge1xuICAgICAgICAgIHRoaXMubWFuYWdlci5yZXBvcnREZWF0aCgpO1xuICAgICAgICAgIHRoaXMucGluZ0RlbGF5ID0gTWF0aC5tYXgobGlmZXNwYW4gLyAyLCB0aGlzLm1pblBpbmdEZWxheSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9O1xuXG4gICAgY29ubmVjdGlvbi5iaW5kKCdvcGVuJywgb25PcGVuKTtcbiAgICByZXR1cm4gY29ubmVjdGlvbjtcbiAgfVxuXG4gIC8qKiBSZXR1cm5zIHdoZXRoZXIgdGhlIHRyYW5zcG9ydCBpcyBzdXBwb3J0ZWQgaW4gdGhlIGVudmlyb25tZW50LlxuICAgKlxuICAgKiBUaGlzIGZ1bmN0aW9uIGhhcyB0aGUgc2FtZSBBUEkgYXMgVHJhbnNwb3J0I2lzU3VwcG9ydGVkLiBNaWdodCByZXR1cm4gZmFsc2VcbiAgICogd2hlbiB0aGUgbWFuYWdlciBkZWNpZGVzIHRvIGtpbGwgdGhlIHRyYW5zcG9ydC5cbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IGVudmlyb25tZW50IHRoZSBlbnZpcm9ubWVudCBkZXRhaWxzIChlbmNyeXB0aW9uLCBzZXR0aW5ncylcbiAgICogQHJldHVybnMge0Jvb2xlYW59IHRydWUgd2hlbiB0aGUgdHJhbnNwb3J0IGlzIHN1cHBvcnRlZFxuICAgKi9cbiAgaXNTdXBwb3J0ZWQoZW52aXJvbm1lbnQ6IHN0cmluZyk6IGJvb2xlYW4ge1xuICAgIHJldHVybiB0aGlzLm1hbmFnZXIuaXNBbGl2ZSgpICYmIHRoaXMudHJhbnNwb3J0LmlzU3VwcG9ydGVkKGVudmlyb25tZW50KTtcbiAgfVxufVxuIiwgImltcG9ydCBBY3Rpb24gZnJvbSAnLi9hY3Rpb24nO1xuaW1wb3J0IHsgUHVzaGVyRXZlbnQgfSBmcm9tICcuL21lc3NhZ2UtdHlwZXMnO1xuLyoqXG4gKiBQcm92aWRlcyBmdW5jdGlvbnMgZm9yIGhhbmRsaW5nIFB1c2hlciBwcm90b2NvbC1zcGVjaWZpYyBtZXNzYWdlcy5cbiAqL1xuXG5jb25zdCBQcm90b2NvbCA9IHtcbiAgLyoqXG4gICAqIERlY29kZXMgYSBtZXNzYWdlIGluIGEgUHVzaGVyIGZvcm1hdC5cbiAgICpcbiAgICogVGhlIE1lc3NhZ2VFdmVudCB3ZSByZWNlaXZlIGZyb20gdGhlIHRyYW5zcG9ydCBzaG91bGQgY29udGFpbiBhIHB1c2hlciBldmVudFxuICAgKiAoaHR0cHM6Ly9wdXNoZXIuY29tL2RvY3MvcHVzaGVyX3Byb3RvY29sI2V2ZW50cykgc2VyaWFsaXplZCBhcyBKU09OIGluIHRoZVxuICAgKiBkYXRhIGZpZWxkXG4gICAqXG4gICAqIFRoZSBwdXNoZXIgZXZlbnQgbWF5IGNvbnRhaW4gYSBkYXRhIGZpZWxkIHRvbywgYW5kIGl0IG1heSBhbHNvIGJlXG4gICAqIHNlcmlhbGlzZWQgYXMgSlNPTlxuICAgKlxuICAgKiBUaHJvd3MgZXJyb3JzIHdoZW4gbWVzc2FnZXMgYXJlIG5vdCBwYXJzZSdhYmxlLlxuICAgKlxuICAgKiBAcGFyYW0gIHtNZXNzYWdlRXZlbnR9IG1lc3NhZ2VFdmVudFxuICAgKiBAcmV0dXJuIHtQdXNoZXJFdmVudH1cbiAgICovXG4gIGRlY29kZU1lc3NhZ2U6IGZ1bmN0aW9uKG1lc3NhZ2VFdmVudDogTWVzc2FnZUV2ZW50KTogUHVzaGVyRXZlbnQge1xuICAgIHRyeSB7XG4gICAgICB2YXIgbWVzc2FnZURhdGEgPSBKU09OLnBhcnNlKG1lc3NhZ2VFdmVudC5kYXRhKTtcbiAgICAgIHZhciBwdXNoZXJFdmVudERhdGEgPSBtZXNzYWdlRGF0YS5kYXRhO1xuICAgICAgaWYgKHR5cGVvZiBwdXNoZXJFdmVudERhdGEgPT09ICdzdHJpbmcnKSB7XG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgcHVzaGVyRXZlbnREYXRhID0gSlNPTi5wYXJzZShtZXNzYWdlRGF0YS5kYXRhKTtcbiAgICAgICAgfSBjYXRjaCAoZSkge31cbiAgICAgIH1cbiAgICAgIHZhciBwdXNoZXJFdmVudDogUHVzaGVyRXZlbnQgPSB7XG4gICAgICAgIGV2ZW50OiBtZXNzYWdlRGF0YS5ldmVudCxcbiAgICAgICAgY2hhbm5lbDogbWVzc2FnZURhdGEuY2hhbm5lbCxcbiAgICAgICAgZGF0YTogcHVzaGVyRXZlbnREYXRhXG4gICAgICB9O1xuICAgICAgaWYgKG1lc3NhZ2VEYXRhLnVzZXJfaWQpIHtcbiAgICAgICAgcHVzaGVyRXZlbnQudXNlcl9pZCA9IG1lc3NhZ2VEYXRhLnVzZXJfaWQ7XG4gICAgICB9XG4gICAgICByZXR1cm4gcHVzaGVyRXZlbnQ7XG4gICAgfSBjYXRjaCAoZSkge1xuICAgICAgdGhyb3cgeyB0eXBlOiAnTWVzc2FnZVBhcnNlRXJyb3InLCBlcnJvcjogZSwgZGF0YTogbWVzc2FnZUV2ZW50LmRhdGEgfTtcbiAgICB9XG4gIH0sXG5cbiAgLyoqXG4gICAqIEVuY29kZXMgYSBtZXNzYWdlIHRvIGJlIHNlbnQuXG4gICAqXG4gICAqIEBwYXJhbSAge1B1c2hlckV2ZW50fSBldmVudFxuICAgKiBAcmV0dXJuIHtTdHJpbmd9XG4gICAqL1xuICBlbmNvZGVNZXNzYWdlOiBmdW5jdGlvbihldmVudDogUHVzaGVyRXZlbnQpOiBzdHJpbmcge1xuICAgIHJldHVybiBKU09OLnN0cmluZ2lmeShldmVudCk7XG4gIH0sXG5cbiAgLyoqXG4gICAqIFByb2Nlc3NlcyBhIGhhbmRzaGFrZSBtZXNzYWdlIGFuZCByZXR1cm5zIGFwcHJvcHJpYXRlIGFjdGlvbnMuXG4gICAqXG4gICAqIFJldHVybnMgYW4gb2JqZWN0IHdpdGggYW4gJ2FjdGlvbicgYW5kIG90aGVyIGFjdGlvbi1zcGVjaWZpYyBwcm9wZXJ0aWVzLlxuICAgKlxuICAgKiBUaGVyZSBhcmUgdGhyZWUgb3V0Y29tZXMgd2hlbiBjYWxsaW5nIHRoaXMgZnVuY3Rpb24uIEZpcnN0IGlzIGEgc3VjY2Vzc2Z1bFxuICAgKiBjb25uZWN0aW9uIGF0dGVtcHQsIHdoZW4gcHVzaGVyOmNvbm5lY3Rpb25fZXN0YWJsaXNoZWQgaXMgcmVjZWl2ZWQsIHdoaWNoXG4gICAqIHJlc3VsdHMgaW4gYSAnY29ubmVjdGVkJyBhY3Rpb24gd2l0aCBhbiAnaWQnIHByb3BlcnR5LiBXaGVuIHBhc3NlZCBhXG4gICAqIHB1c2hlcjplcnJvciBldmVudCwgaXQgcmV0dXJucyBhIHJlc3VsdCB3aXRoIGFjdGlvbiBhcHByb3ByaWF0ZSB0byB0aGVcbiAgICogY2xvc2UgY29kZSBhbmQgYW4gZXJyb3IuIE90aGVyd2lzZSwgaXQgcmFpc2VzIGFuIGV4Y2VwdGlvbi5cbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IG1lc3NhZ2VcbiAgICogQHJlc3VsdCBPYmplY3RcbiAgICovXG4gIHByb2Nlc3NIYW5kc2hha2U6IGZ1bmN0aW9uKG1lc3NhZ2VFdmVudDogTWVzc2FnZUV2ZW50KTogQWN0aW9uIHtcbiAgICB2YXIgbWVzc2FnZSA9IFByb3RvY29sLmRlY29kZU1lc3NhZ2UobWVzc2FnZUV2ZW50KTtcblxuICAgIGlmIChtZXNzYWdlLmV2ZW50ID09PSAncHVzaGVyOmNvbm5lY3Rpb25fZXN0YWJsaXNoZWQnKSB7XG4gICAgICBpZiAoIW1lc3NhZ2UuZGF0YS5hY3Rpdml0eV90aW1lb3V0KSB7XG4gICAgICAgIHRocm93ICdObyBhY3Rpdml0eSB0aW1lb3V0IHNwZWNpZmllZCBpbiBoYW5kc2hha2UnO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHtcbiAgICAgICAgYWN0aW9uOiAnY29ubmVjdGVkJyxcbiAgICAgICAgaWQ6IG1lc3NhZ2UuZGF0YS5zb2NrZXRfaWQsXG4gICAgICAgIGFjdGl2aXR5VGltZW91dDogbWVzc2FnZS5kYXRhLmFjdGl2aXR5X3RpbWVvdXQgKiAxMDAwXG4gICAgICB9O1xuICAgIH0gZWxzZSBpZiAobWVzc2FnZS5ldmVudCA9PT0gJ3B1c2hlcjplcnJvcicpIHtcbiAgICAgIC8vIEZyb20gcHJvdG9jb2wgNiBjbG9zZSBjb2RlcyBhcmUgc2VudCBvbmx5IG9uY2UsIHNvIHRoaXMgb25seVxuICAgICAgLy8gaGFwcGVucyB3aGVuIGNvbm5lY3Rpb24gZG9lcyBub3Qgc3VwcG9ydCBjbG9zZSBjb2Rlc1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgYWN0aW9uOiB0aGlzLmdldENsb3NlQWN0aW9uKG1lc3NhZ2UuZGF0YSksXG4gICAgICAgIGVycm9yOiB0aGlzLmdldENsb3NlRXJyb3IobWVzc2FnZS5kYXRhKVxuICAgICAgfTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhyb3cgJ0ludmFsaWQgaGFuZHNoYWtlJztcbiAgICB9XG4gIH0sXG5cbiAgLyoqXG4gICAqIERpc3BhdGNoZXMgdGhlIGNsb3NlIGV2ZW50IGFuZCByZXR1cm5zIGFuIGFwcHJvcHJpYXRlIGFjdGlvbiBuYW1lLlxuICAgKlxuICAgKiBTZWU6XG4gICAqIDEuIGh0dHBzOi8vZGV2ZWxvcGVyLm1vemlsbGEub3JnL2VuLVVTL2RvY3MvV2ViU29ja2V0cy9XZWJTb2NrZXRzX3JlZmVyZW5jZS9DbG9zZUV2ZW50XG4gICAqIDIuIGh0dHA6Ly9wdXNoZXIuY29tL2RvY3MvcHVzaGVyX3Byb3RvY29sXG4gICAqXG4gICAqIEBwYXJhbSAge0Nsb3NlRXZlbnR9IGNsb3NlRXZlbnRcbiAgICogQHJldHVybiB7U3RyaW5nfSBjbG9zZSBhY3Rpb24gbmFtZVxuICAgKi9cbiAgZ2V0Q2xvc2VBY3Rpb246IGZ1bmN0aW9uKGNsb3NlRXZlbnQpOiBzdHJpbmcge1xuICAgIGlmIChjbG9zZUV2ZW50LmNvZGUgPCA0MDAwKSB7XG4gICAgICAvLyBpZ25vcmUgMTAwMCBDTE9TRV9OT1JNQUwsIDEwMDEgQ0xPU0VfR09JTkdfQVdBWSxcbiAgICAgIC8vICAgICAgICAxMDA1IENMT1NFX05PX1NUQVRVUywgMTAwNiBDTE9TRV9BQk5PUk1BTFxuICAgICAgLy8gaWdub3JlIDEwMDcuLi4zOTk5XG4gICAgICAvLyBoYW5kbGUgMTAwMiBDTE9TRV9QUk9UT0NPTF9FUlJPUiwgMTAwMyBDTE9TRV9VTlNVUFBPUlRFRCxcbiAgICAgIC8vICAgICAgICAxMDA0IENMT1NFX1RPT19MQVJHRVxuICAgICAgaWYgKGNsb3NlRXZlbnQuY29kZSA+PSAxMDAyICYmIGNsb3NlRXZlbnQuY29kZSA8PSAxMDA0KSB7XG4gICAgICAgIHJldHVybiAnYmFja29mZic7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgIH1cbiAgICB9IGVsc2UgaWYgKGNsb3NlRXZlbnQuY29kZSA9PT0gNDAwMCkge1xuICAgICAgcmV0dXJuICd0bHNfb25seSc7XG4gICAgfSBlbHNlIGlmIChjbG9zZUV2ZW50LmNvZGUgPCA0MTAwKSB7XG4gICAgICByZXR1cm4gJ3JlZnVzZWQnO1xuICAgIH0gZWxzZSBpZiAoY2xvc2VFdmVudC5jb2RlIDwgNDIwMCkge1xuICAgICAgcmV0dXJuICdiYWNrb2ZmJztcbiAgICB9IGVsc2UgaWYgKGNsb3NlRXZlbnQuY29kZSA8IDQzMDApIHtcbiAgICAgIHJldHVybiAncmV0cnknO1xuICAgIH0gZWxzZSB7XG4gICAgICAvLyB1bmtub3duIGVycm9yXG4gICAgICByZXR1cm4gJ3JlZnVzZWQnO1xuICAgIH1cbiAgfSxcblxuICAvKipcbiAgICogUmV0dXJucyBhbiBlcnJvciBvciBudWxsIGJhc2luZyBvbiB0aGUgY2xvc2UgZXZlbnQuXG4gICAqXG4gICAqIE51bGwgaXMgcmV0dXJuZWQgd2hlbiBjb25uZWN0aW9uIHdhcyBjbG9zZWQgY2xlYW5seS4gT3RoZXJ3aXNlLCBhbiBvYmplY3RcbiAgICogd2l0aCBlcnJvciBkZXRhaWxzIGlzIHJldHVybmVkLlxuICAgKlxuICAgKiBAcGFyYW0gIHtDbG9zZUV2ZW50fSBjbG9zZUV2ZW50XG4gICAqIEByZXR1cm4ge09iamVjdH0gZXJyb3Igb2JqZWN0XG4gICAqL1xuICBnZXRDbG9zZUVycm9yOiBmdW5jdGlvbihjbG9zZUV2ZW50KTogYW55IHtcbiAgICBpZiAoY2xvc2VFdmVudC5jb2RlICE9PSAxMDAwICYmIGNsb3NlRXZlbnQuY29kZSAhPT0gMTAwMSkge1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgdHlwZTogJ1B1c2hlckVycm9yJyxcbiAgICAgICAgZGF0YToge1xuICAgICAgICAgIGNvZGU6IGNsb3NlRXZlbnQuY29kZSxcbiAgICAgICAgICBtZXNzYWdlOiBjbG9zZUV2ZW50LnJlYXNvbiB8fCBjbG9zZUV2ZW50Lm1lc3NhZ2VcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuICB9XG59O1xuXG5leHBvcnQgZGVmYXVsdCBQcm90b2NvbDtcbiIsICJpbXBvcnQgKiBhcyBDb2xsZWN0aW9ucyBmcm9tICcuLi91dGlscy9jb2xsZWN0aW9ucyc7XG5pbXBvcnQgeyBkZWZhdWx0IGFzIEV2ZW50c0Rpc3BhdGNoZXIgfSBmcm9tICcuLi9ldmVudHMvZGlzcGF0Y2hlcic7XG5pbXBvcnQgUHJvdG9jb2wgZnJvbSAnLi9wcm90b2NvbC9wcm90b2NvbCc7XG5pbXBvcnQgeyBQdXNoZXJFdmVudCB9IGZyb20gJy4vcHJvdG9jb2wvbWVzc2FnZS10eXBlcyc7XG5pbXBvcnQgTG9nZ2VyIGZyb20gJy4uL2xvZ2dlcic7XG5pbXBvcnQgVHJhbnNwb3J0Q29ubmVjdGlvbiBmcm9tICcuLi90cmFuc3BvcnRzL3RyYW5zcG9ydF9jb25uZWN0aW9uJztcbmltcG9ydCBTb2NrZXQgZnJvbSAnLi4vc29ja2V0Jztcbi8qKlxuICogUHJvdmlkZXMgUHVzaGVyIHByb3RvY29sIGludGVyZmFjZSBmb3IgdHJhbnNwb3J0cy5cbiAqXG4gKiBFbWl0cyBmb2xsb3dpbmcgZXZlbnRzOlxuICogLSBtZXNzYWdlIC0gb24gcmVjZWl2ZWQgbWVzc2FnZXNcbiAqIC0gcGluZyAtIG9uIHBpbmcgcmVxdWVzdHNcbiAqIC0gcG9uZyAtIG9uIHBvbmcgcmVzcG9uc2VzXG4gKiAtIGVycm9yIC0gd2hlbiB0aGUgdHJhbnNwb3J0IGVtaXRzIGFuIGVycm9yXG4gKiAtIGNsb3NlZCAtIGFmdGVyIGNsb3NpbmcgdGhlIHRyYW5zcG9ydFxuICpcbiAqIEl0IGFsc28gZW1pdHMgbW9yZSBldmVudHMgd2hlbiBjb25uZWN0aW9uIGNsb3NlcyB3aXRoIGEgY29kZS5cbiAqIFNlZSBQcm90b2NvbC5nZXRDbG9zZUFjdGlvbiB0byBnZXQgbW9yZSBkZXRhaWxzLlxuICpcbiAqIEBwYXJhbSB7TnVtYmVyfSBpZFxuICogQHBhcmFtIHtBYnN0cmFjdFRyYW5zcG9ydH0gdHJhbnNwb3J0XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENvbm5lY3Rpb24gZXh0ZW5kcyBFdmVudHNEaXNwYXRjaGVyIGltcGxlbWVudHMgU29ja2V0IHtcbiAgaWQ6IHN0cmluZztcbiAgdHJhbnNwb3J0OiBUcmFuc3BvcnRDb25uZWN0aW9uO1xuICBhY3Rpdml0eVRpbWVvdXQ6IG51bWJlcjtcblxuICBjb25zdHJ1Y3RvcihpZDogc3RyaW5nLCB0cmFuc3BvcnQ6IFRyYW5zcG9ydENvbm5lY3Rpb24pIHtcbiAgICBzdXBlcigpO1xuICAgIHRoaXMuaWQgPSBpZDtcbiAgICB0aGlzLnRyYW5zcG9ydCA9IHRyYW5zcG9ydDtcbiAgICB0aGlzLmFjdGl2aXR5VGltZW91dCA9IHRyYW5zcG9ydC5hY3Rpdml0eVRpbWVvdXQ7XG4gICAgdGhpcy5iaW5kTGlzdGVuZXJzKCk7XG4gIH1cblxuICAvKiogUmV0dXJucyB3aGV0aGVyIHVzZWQgdHJhbnNwb3J0IGhhbmRsZXMgYWN0aXZpdHkgY2hlY2tzIGJ5IGl0c2VsZlxuICAgKlxuICAgKiBAcmV0dXJucyB7Qm9vbGVhbn0gdHJ1ZSBpZiBhY3Rpdml0eSBjaGVja3MgYXJlIGhhbmRsZWQgYnkgdGhlIHRyYW5zcG9ydFxuICAgKi9cbiAgaGFuZGxlc0FjdGl2aXR5Q2hlY2tzKCkge1xuICAgIHJldHVybiB0aGlzLnRyYW5zcG9ydC5oYW5kbGVzQWN0aXZpdHlDaGVja3MoKTtcbiAgfVxuXG4gIC8qKiBTZW5kcyByYXcgZGF0YS5cbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IGRhdGFcbiAgICovXG4gIHNlbmQoZGF0YTogYW55KTogYm9vbGVhbiB7XG4gICAgcmV0dXJuIHRoaXMudHJhbnNwb3J0LnNlbmQoZGF0YSk7XG4gIH1cblxuICAvKiogU2VuZHMgYW4gZXZlbnQuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBkYXRhXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBbY2hhbm5lbF1cbiAgICogQHJldHVybnMge0Jvb2xlYW59IHdoZXRoZXIgbWVzc2FnZSB3YXMgc2VudCBvciBub3RcbiAgICovXG4gIHNlbmRfZXZlbnQobmFtZTogc3RyaW5nLCBkYXRhOiBhbnksIGNoYW5uZWw/OiBzdHJpbmcpOiBib29sZWFuIHtcbiAgICB2YXIgZXZlbnQ6IFB1c2hlckV2ZW50ID0geyBldmVudDogbmFtZSwgZGF0YTogZGF0YSB9O1xuICAgIGlmIChjaGFubmVsKSB7XG4gICAgICBldmVudC5jaGFubmVsID0gY2hhbm5lbDtcbiAgICB9XG4gICAgTG9nZ2VyLmRlYnVnKCdFdmVudCBzZW50JywgZXZlbnQpO1xuICAgIHJldHVybiB0aGlzLnNlbmQoUHJvdG9jb2wuZW5jb2RlTWVzc2FnZShldmVudCkpO1xuICB9XG5cbiAgLyoqIFNlbmRzIGEgcGluZyBtZXNzYWdlIHRvIHRoZSBzZXJ2ZXIuXG4gICAqXG4gICAqIEJhc2luZyBvbiB0aGUgdW5kZXJseWluZyB0cmFuc3BvcnQsIGl0IG1pZ2h0IHNlbmQgZWl0aGVyIHRyYW5zcG9ydCdzXG4gICAqIHByb3RvY29sLXNwZWNpZmljIHBpbmcgb3IgcHVzaGVyOnBpbmcgZXZlbnQuXG4gICAqL1xuICBwaW5nKCkge1xuICAgIGlmICh0aGlzLnRyYW5zcG9ydC5zdXBwb3J0c1BpbmcoKSkge1xuICAgICAgdGhpcy50cmFuc3BvcnQucGluZygpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLnNlbmRfZXZlbnQoJ3B1c2hlcjpwaW5nJywge30pO1xuICAgIH1cbiAgfVxuXG4gIC8qKiBDbG9zZXMgdGhlIGNvbm5lY3Rpb24uICovXG4gIGNsb3NlKCkge1xuICAgIHRoaXMudHJhbnNwb3J0LmNsb3NlKCk7XG4gIH1cblxuICBwcml2YXRlIGJpbmRMaXN0ZW5lcnMoKSB7XG4gICAgdmFyIGxpc3RlbmVycyA9IHtcbiAgICAgIG1lc3NhZ2U6IChtZXNzYWdlRXZlbnQ6IE1lc3NhZ2VFdmVudCkgPT4ge1xuICAgICAgICB2YXIgcHVzaGVyRXZlbnQ7XG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgcHVzaGVyRXZlbnQgPSBQcm90b2NvbC5kZWNvZGVNZXNzYWdlKG1lc3NhZ2VFdmVudCk7XG4gICAgICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgICB0aGlzLmVtaXQoJ2Vycm9yJywge1xuICAgICAgICAgICAgdHlwZTogJ01lc3NhZ2VQYXJzZUVycm9yJyxcbiAgICAgICAgICAgIGVycm9yOiBlLFxuICAgICAgICAgICAgZGF0YTogbWVzc2FnZUV2ZW50LmRhdGFcbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChwdXNoZXJFdmVudCAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgTG9nZ2VyLmRlYnVnKCdFdmVudCByZWNkJywgcHVzaGVyRXZlbnQpO1xuXG4gICAgICAgICAgc3dpdGNoIChwdXNoZXJFdmVudC5ldmVudCkge1xuICAgICAgICAgICAgY2FzZSAncHVzaGVyOmVycm9yJzpcbiAgICAgICAgICAgICAgdGhpcy5lbWl0KCdlcnJvcicsIHtcbiAgICAgICAgICAgICAgICB0eXBlOiAnUHVzaGVyRXJyb3InLFxuICAgICAgICAgICAgICAgIGRhdGE6IHB1c2hlckV2ZW50LmRhdGFcbiAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgY2FzZSAncHVzaGVyOnBpbmcnOlxuICAgICAgICAgICAgICB0aGlzLmVtaXQoJ3BpbmcnKTtcbiAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlICdwdXNoZXI6cG9uZyc6XG4gICAgICAgICAgICAgIHRoaXMuZW1pdCgncG9uZycpO1xuICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgICAgdGhpcy5lbWl0KCdtZXNzYWdlJywgcHVzaGVyRXZlbnQpO1xuICAgICAgICB9XG4gICAgICB9LFxuICAgICAgYWN0aXZpdHk6ICgpID0+IHtcbiAgICAgICAgdGhpcy5lbWl0KCdhY3Rpdml0eScpO1xuICAgICAgfSxcbiAgICAgIGVycm9yOiBlcnJvciA9PiB7XG4gICAgICAgIHRoaXMuZW1pdCgnZXJyb3InLCBlcnJvcik7XG4gICAgICB9LFxuICAgICAgY2xvc2VkOiBjbG9zZUV2ZW50ID0+IHtcbiAgICAgICAgdW5iaW5kTGlzdGVuZXJzKCk7XG5cbiAgICAgICAgaWYgKGNsb3NlRXZlbnQgJiYgY2xvc2VFdmVudC5jb2RlKSB7XG4gICAgICAgICAgdGhpcy5oYW5kbGVDbG9zZUV2ZW50KGNsb3NlRXZlbnQpO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy50cmFuc3BvcnQgPSBudWxsO1xuICAgICAgICB0aGlzLmVtaXQoJ2Nsb3NlZCcpO1xuICAgICAgfVxuICAgIH07XG5cbiAgICB2YXIgdW5iaW5kTGlzdGVuZXJzID0gKCkgPT4ge1xuICAgICAgQ29sbGVjdGlvbnMub2JqZWN0QXBwbHkobGlzdGVuZXJzLCAobGlzdGVuZXIsIGV2ZW50KSA9PiB7XG4gICAgICAgIHRoaXMudHJhbnNwb3J0LnVuYmluZChldmVudCwgbGlzdGVuZXIpO1xuICAgICAgfSk7XG4gICAgfTtcblxuICAgIENvbGxlY3Rpb25zLm9iamVjdEFwcGx5KGxpc3RlbmVycywgKGxpc3RlbmVyLCBldmVudCkgPT4ge1xuICAgICAgdGhpcy50cmFuc3BvcnQuYmluZChldmVudCwgbGlzdGVuZXIpO1xuICAgIH0pO1xuICB9XG5cbiAgcHJpdmF0ZSBoYW5kbGVDbG9zZUV2ZW50KGNsb3NlRXZlbnQ6IGFueSkge1xuICAgIHZhciBhY3Rpb24gPSBQcm90b2NvbC5nZXRDbG9zZUFjdGlvbihjbG9zZUV2ZW50KTtcbiAgICB2YXIgZXJyb3IgPSBQcm90b2NvbC5nZXRDbG9zZUVycm9yKGNsb3NlRXZlbnQpO1xuICAgIGlmIChlcnJvcikge1xuICAgICAgdGhpcy5lbWl0KCdlcnJvcicsIGVycm9yKTtcbiAgICB9XG4gICAgaWYgKGFjdGlvbikge1xuICAgICAgdGhpcy5lbWl0KGFjdGlvbiwgeyBhY3Rpb246IGFjdGlvbiwgZXJyb3I6IGVycm9yIH0pO1xuICAgIH1cbiAgfVxufVxuIiwgImltcG9ydCBVdGlsIGZyb20gJy4uLy4uL3V0aWwnO1xuaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnLi4vLi4vdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IFByb3RvY29sIGZyb20gJy4uL3Byb3RvY29sL3Byb3RvY29sJztcbmltcG9ydCBDb25uZWN0aW9uIGZyb20gJy4uL2Nvbm5lY3Rpb24nO1xuaW1wb3J0IFRyYW5zcG9ydENvbm5lY3Rpb24gZnJvbSAnLi4vLi4vdHJhbnNwb3J0cy90cmFuc3BvcnRfY29ubmVjdGlvbic7XG5pbXBvcnQgSGFuZHNoYWtlUGF5bG9hZCBmcm9tICcuL2hhbmRzaGFrZV9wYXlsb2FkJztcblxuLyoqXG4gKiBIYW5kbGVzIFB1c2hlciBwcm90b2NvbCBoYW5kc2hha2VzIGZvciB0cmFuc3BvcnRzLlxuICpcbiAqIENhbGxzIGJhY2sgd2l0aCBhIHJlc3VsdCBvYmplY3QgYWZ0ZXIgaGFuZHNoYWtlIGlzIGNvbXBsZXRlZC4gUmVzdWx0c1xuICogYWx3YXlzIGhhdmUgdHdvIGZpZWxkczpcbiAqIC0gYWN0aW9uIC0gc3RyaW5nIGRlc2NyaWJpbmcgYWN0aW9uIHRvIGJlIHRha2VuIGFmdGVyIHRoZSBoYW5kc2hha2VcbiAqIC0gdHJhbnNwb3J0IC0gdGhlIHRyYW5zcG9ydCBvYmplY3QgcGFzc2VkIHRvIHRoZSBjb25zdHJ1Y3RvclxuICpcbiAqIERpZmZlcmVudCBhY3Rpb25zIGNhbiBzZXQgZGlmZmVyZW50IGFkZGl0aW9uYWwgcHJvcGVydGllcyBvbiB0aGUgcmVzdWx0LlxuICogSW4gdGhlIGNhc2Ugb2YgJ2Nvbm5lY3RlZCcgYWN0aW9uLCB0aGVyZSB3aWxsIGJlIGEgJ2Nvbm5lY3Rpb24nIHByb3BlcnR5XG4gKiBjb250YWluaW5nIGEgQ29ubmVjdGlvbiBvYmplY3QgZm9yIHRoZSB0cmFuc3BvcnQuIE90aGVyIGFjdGlvbnMgc2hvdWxkXG4gKiBjYXJyeSBhbiAnZXJyb3InIHByb3BlcnR5LlxuICpcbiAqIEBwYXJhbSB7QWJzdHJhY3RUcmFuc3BvcnR9IHRyYW5zcG9ydFxuICogQHBhcmFtIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSGFuZHNoYWtlIHtcbiAgdHJhbnNwb3J0OiBUcmFuc3BvcnRDb25uZWN0aW9uO1xuICBjYWxsYmFjazogKEhhbmRzaGFrZVBheWxvYWQpID0+IHZvaWQ7XG4gIG9uTWVzc2FnZTogRnVuY3Rpb247XG4gIG9uQ2xvc2VkOiBGdW5jdGlvbjtcblxuICBjb25zdHJ1Y3RvcihcbiAgICB0cmFuc3BvcnQ6IFRyYW5zcG9ydENvbm5lY3Rpb24sXG4gICAgY2FsbGJhY2s6IChIYW5kc2hha2VQYXlsb2FkKSA9PiB2b2lkXG4gICkge1xuICAgIHRoaXMudHJhbnNwb3J0ID0gdHJhbnNwb3J0O1xuICAgIHRoaXMuY2FsbGJhY2sgPSBjYWxsYmFjaztcbiAgICB0aGlzLmJpbmRMaXN0ZW5lcnMoKTtcbiAgfVxuXG4gIGNsb3NlKCkge1xuICAgIHRoaXMudW5iaW5kTGlzdGVuZXJzKCk7XG4gICAgdGhpcy50cmFuc3BvcnQuY2xvc2UoKTtcbiAgfVxuXG4gIHByaXZhdGUgYmluZExpc3RlbmVycygpIHtcbiAgICB0aGlzLm9uTWVzc2FnZSA9IG0gPT4ge1xuICAgICAgdGhpcy51bmJpbmRMaXN0ZW5lcnMoKTtcblxuICAgICAgdmFyIHJlc3VsdDtcbiAgICAgIHRyeSB7XG4gICAgICAgIHJlc3VsdCA9IFByb3RvY29sLnByb2Nlc3NIYW5kc2hha2UobSk7XG4gICAgICB9IGNhdGNoIChlKSB7XG4gICAgICAgIHRoaXMuZmluaXNoKCdlcnJvcicsIHsgZXJyb3I6IGUgfSk7XG4gICAgICAgIHRoaXMudHJhbnNwb3J0LmNsb3NlKCk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgaWYgKHJlc3VsdC5hY3Rpb24gPT09ICdjb25uZWN0ZWQnKSB7XG4gICAgICAgIHRoaXMuZmluaXNoKCdjb25uZWN0ZWQnLCB7XG4gICAgICAgICAgY29ubmVjdGlvbjogbmV3IENvbm5lY3Rpb24ocmVzdWx0LmlkLCB0aGlzLnRyYW5zcG9ydCksXG4gICAgICAgICAgYWN0aXZpdHlUaW1lb3V0OiByZXN1bHQuYWN0aXZpdHlUaW1lb3V0XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5maW5pc2gocmVzdWx0LmFjdGlvbiwgeyBlcnJvcjogcmVzdWx0LmVycm9yIH0pO1xuICAgICAgICB0aGlzLnRyYW5zcG9ydC5jbG9zZSgpO1xuICAgICAgfVxuICAgIH07XG5cbiAgICB0aGlzLm9uQ2xvc2VkID0gY2xvc2VFdmVudCA9PiB7XG4gICAgICB0aGlzLnVuYmluZExpc3RlbmVycygpO1xuXG4gICAgICB2YXIgYWN0aW9uID0gUHJvdG9jb2wuZ2V0Q2xvc2VBY3Rpb24oY2xvc2VFdmVudCkgfHwgJ2JhY2tvZmYnO1xuICAgICAgdmFyIGVycm9yID0gUHJvdG9jb2wuZ2V0Q2xvc2VFcnJvcihjbG9zZUV2ZW50KTtcbiAgICAgIHRoaXMuZmluaXNoKGFjdGlvbiwgeyBlcnJvcjogZXJyb3IgfSk7XG4gICAgfTtcblxuICAgIHRoaXMudHJhbnNwb3J0LmJpbmQoJ21lc3NhZ2UnLCB0aGlzLm9uTWVzc2FnZSk7XG4gICAgdGhpcy50cmFuc3BvcnQuYmluZCgnY2xvc2VkJywgdGhpcy5vbkNsb3NlZCk7XG4gIH1cblxuICBwcml2YXRlIHVuYmluZExpc3RlbmVycygpIHtcbiAgICB0aGlzLnRyYW5zcG9ydC51bmJpbmQoJ21lc3NhZ2UnLCB0aGlzLm9uTWVzc2FnZSk7XG4gICAgdGhpcy50cmFuc3BvcnQudW5iaW5kKCdjbG9zZWQnLCB0aGlzLm9uQ2xvc2VkKTtcbiAgfVxuXG4gIHByaXZhdGUgZmluaXNoKGFjdGlvbjogc3RyaW5nLCBwYXJhbXM6IGFueSkge1xuICAgIHRoaXMuY2FsbGJhY2soXG4gICAgICBDb2xsZWN0aW9ucy5leHRlbmQoeyB0cmFuc3BvcnQ6IHRoaXMudHJhbnNwb3J0LCBhY3Rpb246IGFjdGlvbiB9LCBwYXJhbXMpXG4gICAgKTtcbiAgfVxufVxuIiwgImltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBVdGlsIGZyb20gJy4uL3V0aWwnO1xuaW1wb3J0IGJhc2U2NGVuY29kZSBmcm9tICcuLi9iYXNlNjQnO1xuaW1wb3J0IFRpbWVsaW5lIGZyb20gJy4vdGltZWxpbmUnO1xuaW1wb3J0IFJ1bnRpbWUgZnJvbSAncnVudGltZSc7XG5cbmV4cG9ydCBpbnRlcmZhY2UgVGltZWxpbmVTZW5kZXJPcHRpb25zIHtcbiAgaG9zdD86IHN0cmluZztcbiAgcG9ydD86IG51bWJlcjtcbiAgcGF0aD86IHN0cmluZztcbn1cblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVGltZWxpbmVTZW5kZXIge1xuICB0aW1lbGluZTogVGltZWxpbmU7XG4gIG9wdGlvbnM6IFRpbWVsaW5lU2VuZGVyT3B0aW9ucztcbiAgaG9zdDogc3RyaW5nO1xuXG4gIGNvbnN0cnVjdG9yKHRpbWVsaW5lOiBUaW1lbGluZSwgb3B0aW9uczogVGltZWxpbmVTZW5kZXJPcHRpb25zKSB7XG4gICAgdGhpcy50aW1lbGluZSA9IHRpbWVsaW5lO1xuICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG4gIH1cblxuICBzZW5kKHVzZVRMUzogYm9vbGVhbiwgY2FsbGJhY2s/OiBGdW5jdGlvbikge1xuICAgIGlmICh0aGlzLnRpbWVsaW5lLmlzRW1wdHkoKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMudGltZWxpbmUuc2VuZChcbiAgICAgIFJ1bnRpbWUuVGltZWxpbmVUcmFuc3BvcnQuZ2V0QWdlbnQodGhpcywgdXNlVExTKSxcbiAgICAgIGNhbGxiYWNrXG4gICAgKTtcbiAgfVxufVxuIiwgImltcG9ydCB7IGRlZmF1bHQgYXMgRXZlbnRzRGlzcGF0Y2hlciB9IGZyb20gJy4uL2V2ZW50cy9kaXNwYXRjaGVyJztcbmltcG9ydCAqIGFzIEVycm9ycyBmcm9tICcuLi9lcnJvcnMnO1xuaW1wb3J0IExvZ2dlciBmcm9tICcuLi9sb2dnZXInO1xuaW1wb3J0IFB1c2hlciBmcm9tICcuLi9wdXNoZXInO1xuaW1wb3J0IHsgUHVzaGVyRXZlbnQgfSBmcm9tICcuLi9jb25uZWN0aW9uL3Byb3RvY29sL21lc3NhZ2UtdHlwZXMnO1xuaW1wb3J0IE1ldGFkYXRhIGZyb20gJy4vbWV0YWRhdGEnO1xuaW1wb3J0IFVybFN0b3JlIGZyb20gJy4uL3V0aWxzL3VybF9zdG9yZSc7XG5pbXBvcnQge1xuICBDaGFubmVsQXV0aG9yaXphdGlvbkRhdGEsXG4gIENoYW5uZWxBdXRob3JpemF0aW9uQ2FsbGJhY2tcbn0gZnJvbSAnLi4vYXV0aC9vcHRpb25zJztcbmltcG9ydCB7IEhUVFBBdXRoRXJyb3IgfSBmcm9tICcuLi9lcnJvcnMnO1xuXG4vKiogUHJvdmlkZXMgYmFzZSBwdWJsaWMgY2hhbm5lbCBpbnRlcmZhY2Ugd2l0aCBhbiBldmVudCBlbWl0dGVyLlxuICpcbiAqIEVtaXRzOlxuICogLSBwdXNoZXI6c3Vic2NyaXB0aW9uX3N1Y2NlZWRlZCAtIGFmdGVyIHN1YnNjcmliaW5nIHN1Y2Nlc3NmdWxseVxuICogLSBvdGhlciBub24taW50ZXJuYWwgZXZlbnRzXG4gKlxuICogQHBhcmFtIHtTdHJpbmd9IG5hbWVcbiAqIEBwYXJhbSB7UHVzaGVyfSBwdXNoZXJcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hhbm5lbCBleHRlbmRzIEV2ZW50c0Rpc3BhdGNoZXIge1xuICBuYW1lOiBzdHJpbmc7XG4gIHB1c2hlcjogUHVzaGVyO1xuICBzdWJzY3JpYmVkOiBib29sZWFuO1xuICBzdWJzY3JpcHRpb25QZW5kaW5nOiBib29sZWFuO1xuICBzdWJzY3JpcHRpb25DYW5jZWxsZWQ6IGJvb2xlYW47XG4gIHN1YnNjcmlwdGlvbkNvdW50OiBudWxsO1xuXG4gIGNvbnN0cnVjdG9yKG5hbWU6IHN0cmluZywgcHVzaGVyOiBQdXNoZXIpIHtcbiAgICBzdXBlcihmdW5jdGlvbihldmVudCwgZGF0YSkge1xuICAgICAgTG9nZ2VyLmRlYnVnKCdObyBjYWxsYmFja3Mgb24gJyArIG5hbWUgKyAnIGZvciAnICsgZXZlbnQpO1xuICAgIH0pO1xuXG4gICAgdGhpcy5uYW1lID0gbmFtZTtcbiAgICB0aGlzLnB1c2hlciA9IHB1c2hlcjtcbiAgICB0aGlzLnN1YnNjcmliZWQgPSBmYWxzZTtcbiAgICB0aGlzLnN1YnNjcmlwdGlvblBlbmRpbmcgPSBmYWxzZTtcbiAgICB0aGlzLnN1YnNjcmlwdGlvbkNhbmNlbGxlZCA9IGZhbHNlO1xuICB9XG5cbiAgLyoqIFNraXBzIGF1dGhvcml6YXRpb24sIHNpbmNlIHB1YmxpYyBjaGFubmVscyBkb24ndCByZXF1aXJlIGl0LlxuICAgKlxuICAgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFja1xuICAgKi9cbiAgYXV0aG9yaXplKHNvY2tldElkOiBzdHJpbmcsIGNhbGxiYWNrOiBDaGFubmVsQXV0aG9yaXphdGlvbkNhbGxiYWNrKSB7XG4gICAgcmV0dXJuIGNhbGxiYWNrKG51bGwsIHsgYXV0aDogJycgfSk7XG4gIH1cblxuICAvKiogVHJpZ2dlcnMgYW4gZXZlbnQgKi9cbiAgdHJpZ2dlcihldmVudDogc3RyaW5nLCBkYXRhOiBhbnkpIHtcbiAgICBpZiAoZXZlbnQuaW5kZXhPZignY2xpZW50LScpICE9PSAwKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3JzLkJhZEV2ZW50TmFtZShcbiAgICAgICAgXCJFdmVudCAnXCIgKyBldmVudCArIFwiJyBkb2VzIG5vdCBzdGFydCB3aXRoICdjbGllbnQtJ1wiXG4gICAgICApO1xuICAgIH1cbiAgICBpZiAoIXRoaXMuc3Vic2NyaWJlZCkge1xuICAgICAgdmFyIHN1ZmZpeCA9IFVybFN0b3JlLmJ1aWxkTG9nU3VmZml4KCd0cmlnZ2VyaW5nQ2xpZW50RXZlbnRzJyk7XG4gICAgICBMb2dnZXIud2FybihcbiAgICAgICAgYENsaWVudCBldmVudCB0cmlnZ2VyZWQgYmVmb3JlIGNoYW5uZWwgJ3N1YnNjcmlwdGlvbl9zdWNjZWVkZWQnIGV2ZW50IC4gJHtzdWZmaXh9YFxuICAgICAgKTtcbiAgICB9XG4gICAgcmV0dXJuIHRoaXMucHVzaGVyLnNlbmRfZXZlbnQoZXZlbnQsIGRhdGEsIHRoaXMubmFtZSk7XG4gIH1cblxuICAvKiogU2lnbmFscyBkaXNjb25uZWN0aW9uIHRvIHRoZSBjaGFubmVsLiBGb3IgaW50ZXJuYWwgdXNlIG9ubHkuICovXG4gIGRpc2Nvbm5lY3QoKSB7XG4gICAgdGhpcy5zdWJzY3JpYmVkID0gZmFsc2U7XG4gICAgdGhpcy5zdWJzY3JpcHRpb25QZW5kaW5nID0gZmFsc2U7XG4gIH1cblxuICAvKiogSGFuZGxlcyBhIFB1c2hlckV2ZW50LiBGb3IgaW50ZXJuYWwgdXNlIG9ubHkuXG4gICAqXG4gICAqIEBwYXJhbSB7UHVzaGVyRXZlbnR9IGV2ZW50XG4gICAqL1xuICBoYW5kbGVFdmVudChldmVudDogUHVzaGVyRXZlbnQpIHtcbiAgICB2YXIgZXZlbnROYW1lID0gZXZlbnQuZXZlbnQ7XG4gICAgdmFyIGRhdGEgPSBldmVudC5kYXRhO1xuICAgIGlmIChldmVudE5hbWUgPT09ICdwdXNoZXJfaW50ZXJuYWw6c3Vic2NyaXB0aW9uX3N1Y2NlZWRlZCcpIHtcbiAgICAgIHRoaXMuaGFuZGxlU3Vic2NyaXB0aW9uU3VjY2VlZGVkRXZlbnQoZXZlbnQpO1xuICAgIH0gZWxzZSBpZiAoZXZlbnROYW1lID09PSAncHVzaGVyX2ludGVybmFsOnN1YnNjcmlwdGlvbl9jb3VudCcpIHtcbiAgICAgIHRoaXMuaGFuZGxlU3Vic2NyaXB0aW9uQ291bnRFdmVudChldmVudCk7XG4gICAgfSBlbHNlIGlmIChldmVudE5hbWUuaW5kZXhPZigncHVzaGVyX2ludGVybmFsOicpICE9PSAwKSB7XG4gICAgICB2YXIgbWV0YWRhdGE6IE1ldGFkYXRhID0ge307XG4gICAgICB0aGlzLmVtaXQoZXZlbnROYW1lLCBkYXRhLCBtZXRhZGF0YSk7XG4gICAgfVxuICB9XG5cbiAgaGFuZGxlU3Vic2NyaXB0aW9uU3VjY2VlZGVkRXZlbnQoZXZlbnQ6IFB1c2hlckV2ZW50KSB7XG4gICAgdGhpcy5zdWJzY3JpcHRpb25QZW5kaW5nID0gZmFsc2U7XG4gICAgdGhpcy5zdWJzY3JpYmVkID0gdHJ1ZTtcbiAgICBpZiAodGhpcy5zdWJzY3JpcHRpb25DYW5jZWxsZWQpIHtcbiAgICAgIHRoaXMucHVzaGVyLnVuc3Vic2NyaWJlKHRoaXMubmFtZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuZW1pdCgncHVzaGVyOnN1YnNjcmlwdGlvbl9zdWNjZWVkZWQnLCBldmVudC5kYXRhKTtcbiAgICB9XG4gIH1cblxuICBoYW5kbGVTdWJzY3JpcHRpb25Db3VudEV2ZW50KGV2ZW50OiBQdXNoZXJFdmVudCkge1xuICAgIGlmIChldmVudC5kYXRhLnN1YnNjcmlwdGlvbl9jb3VudCkge1xuICAgICAgdGhpcy5zdWJzY3JpcHRpb25Db3VudCA9IGV2ZW50LmRhdGEuc3Vic2NyaXB0aW9uX2NvdW50O1xuICAgIH1cblxuICAgIHRoaXMuZW1pdCgncHVzaGVyOnN1YnNjcmlwdGlvbl9jb3VudCcsIGV2ZW50LmRhdGEpO1xuICB9XG5cbiAgLyoqIFNlbmRzIGEgc3Vic2NyaXB0aW9uIHJlcXVlc3QuIEZvciBpbnRlcm5hbCB1c2Ugb25seS4gKi9cbiAgc3Vic2NyaWJlKCkge1xuICAgIGlmICh0aGlzLnN1YnNjcmliZWQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgdGhpcy5zdWJzY3JpcHRpb25QZW5kaW5nID0gdHJ1ZTtcbiAgICB0aGlzLnN1YnNjcmlwdGlvbkNhbmNlbGxlZCA9IGZhbHNlO1xuICAgIHRoaXMuYXV0aG9yaXplKFxuICAgICAgdGhpcy5wdXNoZXIuY29ubmVjdGlvbi5zb2NrZXRfaWQsXG4gICAgICAoZXJyb3I6IEVycm9yIHwgbnVsbCwgZGF0YTogQ2hhbm5lbEF1dGhvcml6YXRpb25EYXRhKSA9PiB7XG4gICAgICAgIGlmIChlcnJvcikge1xuICAgICAgICAgIHRoaXMuc3Vic2NyaXB0aW9uUGVuZGluZyA9IGZhbHNlO1xuICAgICAgICAgIC8vIFdoeSBub3QgYmluZCB0byAncHVzaGVyOnN1YnNjcmlwdGlvbl9lcnJvcicgYSBsZXZlbCB1cCwgYW5kIGxvZyB0aGVyZT9cbiAgICAgICAgICAvLyBCaW5kaW5nIHRvIHRoaXMgZXZlbnQgd291bGQgY2F1c2UgdGhlIHdhcm5pbmcgYWJvdXQgbm8gY2FsbGJhY2tzIGJlaW5nXG4gICAgICAgICAgLy8gYm91bmQgKHNlZSBjb25zdHJ1Y3RvcikgdG8gYmUgc3VwcHJlc3NlZCwgdGhhdCdzIG5vdCB3aGF0IHdlIHdhbnQuXG4gICAgICAgICAgTG9nZ2VyLmVycm9yKGVycm9yLnRvU3RyaW5nKCkpO1xuICAgICAgICAgIHRoaXMuZW1pdChcbiAgICAgICAgICAgICdwdXNoZXI6c3Vic2NyaXB0aW9uX2Vycm9yJyxcbiAgICAgICAgICAgIE9iamVjdC5hc3NpZ24oXG4gICAgICAgICAgICAgIHt9LFxuICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgdHlwZTogJ0F1dGhFcnJvcicsXG4gICAgICAgICAgICAgICAgZXJyb3I6IGVycm9yLm1lc3NhZ2VcbiAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgZXJyb3IgaW5zdGFuY2VvZiBIVFRQQXV0aEVycm9yID8geyBzdGF0dXM6IGVycm9yLnN0YXR1cyB9IDoge31cbiAgICAgICAgICAgIClcbiAgICAgICAgICApO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHRoaXMucHVzaGVyLnNlbmRfZXZlbnQoJ3B1c2hlcjpzdWJzY3JpYmUnLCB7XG4gICAgICAgICAgICBhdXRoOiBkYXRhLmF1dGgsXG4gICAgICAgICAgICBjaGFubmVsX2RhdGE6IGRhdGEuY2hhbm5lbF9kYXRhLFxuICAgICAgICAgICAgY2hhbm5lbDogdGhpcy5uYW1lXG4gICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgLyoqIFNlbmRzIGFuIHVuc3Vic2NyaXB0aW9uIHJlcXVlc3QuIEZvciBpbnRlcm5hbCB1c2Ugb25seS4gKi9cbiAgdW5zdWJzY3JpYmUoKSB7XG4gICAgdGhpcy5zdWJzY3JpYmVkID0gZmFsc2U7XG4gICAgdGhpcy5wdXNoZXIuc2VuZF9ldmVudCgncHVzaGVyOnVuc3Vic2NyaWJlJywge1xuICAgICAgY2hhbm5lbDogdGhpcy5uYW1lXG4gICAgfSk7XG4gIH1cblxuICAvKiogQ2FuY2VscyBhbiBpbiBwcm9ncmVzcyBzdWJzY3JpcHRpb24uIEZvciBpbnRlcm5hbCB1c2Ugb25seS4gKi9cbiAgY2FuY2VsU3Vic2NyaXB0aW9uKCkge1xuICAgIHRoaXMuc3Vic2NyaXB0aW9uQ2FuY2VsbGVkID0gdHJ1ZTtcbiAgfVxuXG4gIC8qKiBSZWluc3RhdGVzIGFuIGluIHByb2dyZXNzIHN1YnNjcmlwaXRvbi4gRm9yIGludGVybmFsIHVzZSBvbmx5LiAqL1xuICByZWluc3RhdGVTdWJzY3JpcHRpb24oKSB7XG4gICAgdGhpcy5zdWJzY3JpcHRpb25DYW5jZWxsZWQgPSBmYWxzZTtcbiAgfVxufVxuIiwgImltcG9ydCBGYWN0b3J5IGZyb20gJy4uL3V0aWxzL2ZhY3RvcnknO1xuaW1wb3J0IENoYW5uZWwgZnJvbSAnLi9jaGFubmVsJztcbmltcG9ydCB7IENoYW5uZWxBdXRob3JpemF0aW9uQ2FsbGJhY2sgfSBmcm9tICcuLi9hdXRoL29wdGlvbnMnO1xuXG4vKiogRXh0ZW5kcyBwdWJsaWMgY2hhbm5lbHMgdG8gcHJvdmlkZSBwcml2YXRlIGNoYW5uZWwgaW50ZXJmYWNlLlxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gKiBAcGFyYW0ge1B1c2hlcn0gcHVzaGVyXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFByaXZhdGVDaGFubmVsIGV4dGVuZHMgQ2hhbm5lbCB7XG4gIC8qKiBBdXRob3JpemVzIHRoZSBjb25uZWN0aW9uIHRvIHVzZSB0aGUgY2hhbm5lbC5cbiAgICpcbiAgICogQHBhcmFtICB7U3RyaW5nfSBzb2NrZXRJZFxuICAgKiBAcGFyYW0gIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAgICovXG4gIGF1dGhvcml6ZShzb2NrZXRJZDogc3RyaW5nLCBjYWxsYmFjazogQ2hhbm5lbEF1dGhvcml6YXRpb25DYWxsYmFjaykge1xuICAgIHJldHVybiB0aGlzLnB1c2hlci5jb25maWcuY2hhbm5lbEF1dGhvcml6ZXIoXG4gICAgICB7XG4gICAgICAgIGNoYW5uZWxOYW1lOiB0aGlzLm5hbWUsXG4gICAgICAgIHNvY2tldElkOiBzb2NrZXRJZFxuICAgICAgfSxcbiAgICAgIGNhbGxiYWNrXG4gICAgKTtcbiAgfVxufVxuIiwgImltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcblxuLyoqIFJlcHJlc2VudHMgYSBjb2xsZWN0aW9uIG9mIG1lbWJlcnMgb2YgYSBwcmVzZW5jZSBjaGFubmVsLiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTWVtYmVycyB7XG4gIG1lbWJlcnM6IGFueTtcbiAgY291bnQ6IG51bWJlcjtcbiAgbXlJRDogYW55O1xuICBtZTogYW55O1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucmVzZXQoKTtcbiAgfVxuXG4gIC8qKiBSZXR1cm5zIG1lbWJlcidzIGluZm8gZm9yIGdpdmVuIGlkLlxuICAgKlxuICAgKiBSZXN1bHRpbmcgb2JqZWN0IGNvbnRhaW50cyB0d28gZmllbGRzIC0gaWQgYW5kIGluZm8uXG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBpZFxuICAgKiBAcmV0dXJuIHtPYmplY3R9IG1lbWJlcidzIGluZm8gb3IgbnVsbFxuICAgKi9cbiAgZ2V0KGlkOiBzdHJpbmcpOiBhbnkge1xuICAgIGlmIChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwodGhpcy5tZW1iZXJzLCBpZCkpIHtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIGlkOiBpZCxcbiAgICAgICAgaW5mbzogdGhpcy5tZW1iZXJzW2lkXVxuICAgICAgfTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuICB9XG5cbiAgLyoqIENhbGxzIGJhY2sgZm9yIGVhY2ggbWVtYmVyIGluIHVuc3BlY2lmaWVkIG9yZGVyLlxuICAgKlxuICAgKiBAcGFyYW0gIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAgICovXG4gIGVhY2goY2FsbGJhY2s6IEZ1bmN0aW9uKSB7XG4gICAgQ29sbGVjdGlvbnMub2JqZWN0QXBwbHkodGhpcy5tZW1iZXJzLCAobWVtYmVyLCBpZCkgPT4ge1xuICAgICAgY2FsbGJhY2sodGhpcy5nZXQoaWQpKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKiBVcGRhdGVzIHRoZSBpZCBmb3IgY29ubmVjdGVkIG1lbWJlci4gRm9yIGludGVybmFsIHVzZSBvbmx5LiAqL1xuICBzZXRNeUlEKGlkOiBzdHJpbmcpIHtcbiAgICB0aGlzLm15SUQgPSBpZDtcbiAgfVxuXG4gIC8qKiBIYW5kbGVzIHN1YnNjcmlwdGlvbiBkYXRhLiBGb3IgaW50ZXJuYWwgdXNlIG9ubHkuICovXG4gIG9uU3Vic2NyaXB0aW9uKHN1YnNjcmlwdGlvbkRhdGE6IGFueSkge1xuICAgIHRoaXMubWVtYmVycyA9IHN1YnNjcmlwdGlvbkRhdGEucHJlc2VuY2UuaGFzaDtcbiAgICB0aGlzLmNvdW50ID0gc3Vic2NyaXB0aW9uRGF0YS5wcmVzZW5jZS5jb3VudDtcbiAgICB0aGlzLm1lID0gdGhpcy5nZXQodGhpcy5teUlEKTtcbiAgfVxuXG4gIC8qKiBBZGRzIGEgbmV3IG1lbWJlciB0byB0aGUgY29sbGVjdGlvbi4gRm9yIGludGVybmFsIHVzZSBvbmx5LiAqL1xuICBhZGRNZW1iZXIobWVtYmVyRGF0YTogYW55KSB7XG4gICAgaWYgKHRoaXMuZ2V0KG1lbWJlckRhdGEudXNlcl9pZCkgPT09IG51bGwpIHtcbiAgICAgIHRoaXMuY291bnQrKztcbiAgICB9XG4gICAgdGhpcy5tZW1iZXJzW21lbWJlckRhdGEudXNlcl9pZF0gPSBtZW1iZXJEYXRhLnVzZXJfaW5mbztcbiAgICByZXR1cm4gdGhpcy5nZXQobWVtYmVyRGF0YS51c2VyX2lkKTtcbiAgfVxuXG4gIC8qKiBBZGRzIGEgbWVtYmVyIGZyb20gdGhlIGNvbGxlY3Rpb24uIEZvciBpbnRlcm5hbCB1c2Ugb25seS4gKi9cbiAgcmVtb3ZlTWVtYmVyKG1lbWJlckRhdGE6IGFueSkge1xuICAgIHZhciBtZW1iZXIgPSB0aGlzLmdldChtZW1iZXJEYXRhLnVzZXJfaWQpO1xuICAgIGlmIChtZW1iZXIpIHtcbiAgICAgIGRlbGV0ZSB0aGlzLm1lbWJlcnNbbWVtYmVyRGF0YS51c2VyX2lkXTtcbiAgICAgIHRoaXMuY291bnQtLTtcbiAgICB9XG4gICAgcmV0dXJuIG1lbWJlcjtcbiAgfVxuXG4gIC8qKiBSZXNldHMgdGhlIGNvbGxlY3Rpb24gdG8gdGhlIGluaXRpYWwgc3RhdGUuIEZvciBpbnRlcm5hbCB1c2Ugb25seS4gKi9cbiAgcmVzZXQoKSB7XG4gICAgdGhpcy5tZW1iZXJzID0ge307XG4gICAgdGhpcy5jb3VudCA9IDA7XG4gICAgdGhpcy5teUlEID0gbnVsbDtcbiAgICB0aGlzLm1lID0gbnVsbDtcbiAgfVxufVxuIiwgImltcG9ydCBQcml2YXRlQ2hhbm5lbCBmcm9tICcuL3ByaXZhdGVfY2hhbm5lbCc7XG5pbXBvcnQgTG9nZ2VyIGZyb20gJy4uL2xvZ2dlcic7XG5pbXBvcnQgTWVtYmVycyBmcm9tICcuL21lbWJlcnMnO1xuaW1wb3J0IFB1c2hlciBmcm9tICcuLi9wdXNoZXInO1xuaW1wb3J0IFVybFN0b3JlIGZyb20gJ2NvcmUvdXRpbHMvdXJsX3N0b3JlJztcbmltcG9ydCB7IFB1c2hlckV2ZW50IH0gZnJvbSAnLi4vY29ubmVjdGlvbi9wcm90b2NvbC9tZXNzYWdlLXR5cGVzJztcbmltcG9ydCBNZXRhZGF0YSBmcm9tICcuL21ldGFkYXRhJztcbmltcG9ydCB7IENoYW5uZWxBdXRob3JpemF0aW9uRGF0YSB9IGZyb20gJy4uL2F1dGgvb3B0aW9ucyc7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFByZXNlbmNlQ2hhbm5lbCBleHRlbmRzIFByaXZhdGVDaGFubmVsIHtcbiAgbWVtYmVyczogTWVtYmVycztcblxuICAvKiogQWRkcyBwcmVzZW5jZSBjaGFubmVsIGZ1bmN0aW9uYWxpdHkgdG8gcHJpdmF0ZSBjaGFubmVscy5cbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWVcbiAgICogQHBhcmFtIHtQdXNoZXJ9IHB1c2hlclxuICAgKi9cbiAgY29uc3RydWN0b3IobmFtZTogc3RyaW5nLCBwdXNoZXI6IFB1c2hlcikge1xuICAgIHN1cGVyKG5hbWUsIHB1c2hlcik7XG4gICAgdGhpcy5tZW1iZXJzID0gbmV3IE1lbWJlcnMoKTtcbiAgfVxuXG4gIC8qKiBBdXRob3JpemVzIHRoZSBjb25uZWN0aW9uIGFzIGEgbWVtYmVyIG9mIHRoZSBjaGFubmVsLlxuICAgKlxuICAgKiBAcGFyYW0gIHtTdHJpbmd9IHNvY2tldElkXG4gICAqIEBwYXJhbSAge0Z1bmN0aW9ufSBjYWxsYmFja1xuICAgKi9cbiAgYXV0aG9yaXplKHNvY2tldElkOiBzdHJpbmcsIGNhbGxiYWNrOiBGdW5jdGlvbikge1xuICAgIHN1cGVyLmF1dGhvcml6ZShzb2NrZXRJZCwgYXN5bmMgKGVycm9yLCBhdXRoRGF0YSkgPT4ge1xuICAgICAgaWYgKCFlcnJvcikge1xuICAgICAgICBhdXRoRGF0YSA9IGF1dGhEYXRhIGFzIENoYW5uZWxBdXRob3JpemF0aW9uRGF0YTtcbiAgICAgICAgaWYgKGF1dGhEYXRhLmNoYW5uZWxfZGF0YSAhPSBudWxsKSB7XG4gICAgICAgICAgdmFyIGNoYW5uZWxEYXRhID0gSlNPTi5wYXJzZShhdXRoRGF0YS5jaGFubmVsX2RhdGEpO1xuICAgICAgICAgIHRoaXMubWVtYmVycy5zZXRNeUlEKGNoYW5uZWxEYXRhLnVzZXJfaWQpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGF3YWl0IHRoaXMucHVzaGVyLnVzZXIuc2lnbmluRG9uZVByb21pc2U7XG4gICAgICAgICAgaWYgKHRoaXMucHVzaGVyLnVzZXIudXNlcl9kYXRhICE9IG51bGwpIHtcbiAgICAgICAgICAgIC8vIElmIHRoZSB1c2VyIGlzIHNpZ25lZCBpbiwgZ2V0IHRoZSBpZCBvZiB0aGUgYXV0aGVudGljYXRlZCB1c2VyXG4gICAgICAgICAgICAvLyBhbmQgYWxsb3cgdGhlIHByZXNlbmNlIGF1dGhvcml6YXRpb24gdG8gY29udGludWUuXG4gICAgICAgICAgICB0aGlzLm1lbWJlcnMuc2V0TXlJRCh0aGlzLnB1c2hlci51c2VyLnVzZXJfZGF0YS5pZCk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGxldCBzdWZmaXggPSBVcmxTdG9yZS5idWlsZExvZ1N1ZmZpeCgnYXV0aG9yaXphdGlvbkVuZHBvaW50Jyk7XG4gICAgICAgICAgICBMb2dnZXIuZXJyb3IoXG4gICAgICAgICAgICAgIGBJbnZhbGlkIGF1dGggcmVzcG9uc2UgZm9yIGNoYW5uZWwgJyR7dGhpcy5uYW1lfScsIGAgK1xuICAgICAgICAgICAgICAgIGBleHBlY3RlZCAnY2hhbm5lbF9kYXRhJyBmaWVsZC4gJHtzdWZmaXh9LCBgICtcbiAgICAgICAgICAgICAgICBgb3IgdGhlIHVzZXIgc2hvdWxkIGJlIHNpZ25lZCBpbi5gXG4gICAgICAgICAgICApO1xuICAgICAgICAgICAgY2FsbGJhY2soJ0ludmFsaWQgYXV0aCByZXNwb25zZScpO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgY2FsbGJhY2soZXJyb3IsIGF1dGhEYXRhKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKiBIYW5kbGVzIHByZXNlbmNlIGFuZCBzdWJzY3JpcHRpb24gZXZlbnRzLiBGb3IgaW50ZXJuYWwgdXNlIG9ubHkuXG4gICAqXG4gICAqIEBwYXJhbSB7UHVzaGVyRXZlbnR9IGV2ZW50XG4gICAqL1xuICBoYW5kbGVFdmVudChldmVudDogUHVzaGVyRXZlbnQpIHtcbiAgICB2YXIgZXZlbnROYW1lID0gZXZlbnQuZXZlbnQ7XG4gICAgaWYgKGV2ZW50TmFtZS5pbmRleE9mKCdwdXNoZXJfaW50ZXJuYWw6JykgPT09IDApIHtcbiAgICAgIHRoaXMuaGFuZGxlSW50ZXJuYWxFdmVudChldmVudCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhciBkYXRhID0gZXZlbnQuZGF0YTtcbiAgICAgIHZhciBtZXRhZGF0YTogTWV0YWRhdGEgPSB7fTtcbiAgICAgIGlmIChldmVudC51c2VyX2lkKSB7XG4gICAgICAgIG1ldGFkYXRhLnVzZXJfaWQgPSBldmVudC51c2VyX2lkO1xuICAgICAgfVxuICAgICAgdGhpcy5lbWl0KGV2ZW50TmFtZSwgZGF0YSwgbWV0YWRhdGEpO1xuICAgIH1cbiAgfVxuICBoYW5kbGVJbnRlcm5hbEV2ZW50KGV2ZW50OiBQdXNoZXJFdmVudCkge1xuICAgIHZhciBldmVudE5hbWUgPSBldmVudC5ldmVudDtcbiAgICB2YXIgZGF0YSA9IGV2ZW50LmRhdGE7XG4gICAgc3dpdGNoIChldmVudE5hbWUpIHtcbiAgICAgIGNhc2UgJ3B1c2hlcl9pbnRlcm5hbDpzdWJzY3JpcHRpb25fc3VjY2VlZGVkJzpcbiAgICAgICAgdGhpcy5oYW5kbGVTdWJzY3JpcHRpb25TdWNjZWVkZWRFdmVudChldmVudCk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAncHVzaGVyX2ludGVybmFsOnN1YnNjcmlwdGlvbl9jb3VudCc6XG4gICAgICAgIHRoaXMuaGFuZGxlU3Vic2NyaXB0aW9uQ291bnRFdmVudChldmVudCk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAncHVzaGVyX2ludGVybmFsOm1lbWJlcl9hZGRlZCc6XG4gICAgICAgIHZhciBhZGRlZE1lbWJlciA9IHRoaXMubWVtYmVycy5hZGRNZW1iZXIoZGF0YSk7XG4gICAgICAgIHRoaXMuZW1pdCgncHVzaGVyOm1lbWJlcl9hZGRlZCcsIGFkZGVkTWVtYmVyKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlICdwdXNoZXJfaW50ZXJuYWw6bWVtYmVyX3JlbW92ZWQnOlxuICAgICAgICB2YXIgcmVtb3ZlZE1lbWJlciA9IHRoaXMubWVtYmVycy5yZW1vdmVNZW1iZXIoZGF0YSk7XG4gICAgICAgIGlmIChyZW1vdmVkTWVtYmVyKSB7XG4gICAgICAgICAgdGhpcy5lbWl0KCdwdXNoZXI6bWVtYmVyX3JlbW92ZWQnLCByZW1vdmVkTWVtYmVyKTtcbiAgICAgICAgfVxuICAgICAgICBicmVhaztcbiAgICB9XG4gIH1cblxuICBoYW5kbGVTdWJzY3JpcHRpb25TdWNjZWVkZWRFdmVudChldmVudDogUHVzaGVyRXZlbnQpIHtcbiAgICB0aGlzLnN1YnNjcmlwdGlvblBlbmRpbmcgPSBmYWxzZTtcbiAgICB0aGlzLnN1YnNjcmliZWQgPSB0cnVlO1xuICAgIGlmICh0aGlzLnN1YnNjcmlwdGlvbkNhbmNlbGxlZCkge1xuICAgICAgdGhpcy5wdXNoZXIudW5zdWJzY3JpYmUodGhpcy5uYW1lKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5tZW1iZXJzLm9uU3Vic2NyaXB0aW9uKGV2ZW50LmRhdGEpO1xuICAgICAgdGhpcy5lbWl0KCdwdXNoZXI6c3Vic2NyaXB0aW9uX3N1Y2NlZWRlZCcsIHRoaXMubWVtYmVycyk7XG4gICAgfVxuICB9XG5cbiAgLyoqIFJlc2V0cyB0aGUgY2hhbm5lbCBzdGF0ZSwgaW5jbHVkaW5nIG1lbWJlcnMgbWFwLiBGb3IgaW50ZXJuYWwgdXNlIG9ubHkuICovXG4gIGRpc2Nvbm5lY3QoKSB7XG4gICAgdGhpcy5tZW1iZXJzLnJlc2V0KCk7XG4gICAgc3VwZXIuZGlzY29ubmVjdCgpO1xuICB9XG59XG4iLCAiaW1wb3J0IFByaXZhdGVDaGFubmVsIGZyb20gJy4vcHJpdmF0ZV9jaGFubmVsJztcbmltcG9ydCAqIGFzIEVycm9ycyBmcm9tICcuLi9lcnJvcnMnO1xuaW1wb3J0IExvZ2dlciBmcm9tICcuLi9sb2dnZXInO1xuaW1wb3J0IFB1c2hlciBmcm9tICcuLi9wdXNoZXInO1xuaW1wb3J0IHsgZGVjb2RlIGFzIGVuY29kZVVURjggfSBmcm9tICdAc3RhYmxlbGliL3V0ZjgnO1xuaW1wb3J0IHsgZGVjb2RlIGFzIGRlY29kZUJhc2U2NCB9IGZyb20gJ0BzdGFibGVsaWIvYmFzZTY0JztcbmltcG9ydCBEaXNwYXRjaGVyIGZyb20gJy4uL2V2ZW50cy9kaXNwYXRjaGVyJztcbmltcG9ydCB7IFB1c2hlckV2ZW50IH0gZnJvbSAnLi4vY29ubmVjdGlvbi9wcm90b2NvbC9tZXNzYWdlLXR5cGVzJztcbmltcG9ydCB7XG4gIENoYW5uZWxBdXRob3JpemF0aW9uRGF0YSxcbiAgQ2hhbm5lbEF1dGhvcml6YXRpb25DYWxsYmFja1xufSBmcm9tICcuLi9hdXRoL29wdGlvbnMnO1xuaW1wb3J0ICogYXMgbmFjbCBmcm9tICd0d2VldG5hY2wnO1xuXG4vKiogRXh0ZW5kcyBwcml2YXRlIGNoYW5uZWxzIHRvIHByb3ZpZGUgZW5jcnlwdGVkIGNoYW5uZWwgaW50ZXJmYWNlLlxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gKiBAcGFyYW0ge1B1c2hlcn0gcHVzaGVyXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEVuY3J5cHRlZENoYW5uZWwgZXh0ZW5kcyBQcml2YXRlQ2hhbm5lbCB7XG4gIGtleTogVWludDhBcnJheSA9IG51bGw7XG4gIG5hY2w6IG5hY2w7XG5cbiAgY29uc3RydWN0b3IobmFtZTogc3RyaW5nLCBwdXNoZXI6IFB1c2hlciwgbmFjbDogbmFjbCkge1xuICAgIHN1cGVyKG5hbWUsIHB1c2hlcik7XG4gICAgdGhpcy5uYWNsID0gbmFjbDtcbiAgfVxuXG4gIC8qKiBBdXRob3JpemVzIHRoZSBjb25uZWN0aW9uIHRvIHVzZSB0aGUgY2hhbm5lbC5cbiAgICpcbiAgICogQHBhcmFtICB7U3RyaW5nfSBzb2NrZXRJZFxuICAgKiBAcGFyYW0gIHtGdW5jdGlvbn0gY2FsbGJhY2tcbiAgICovXG4gIGF1dGhvcml6ZShzb2NrZXRJZDogc3RyaW5nLCBjYWxsYmFjazogQ2hhbm5lbEF1dGhvcml6YXRpb25DYWxsYmFjaykge1xuICAgIHN1cGVyLmF1dGhvcml6ZShcbiAgICAgIHNvY2tldElkLFxuICAgICAgKGVycm9yOiBFcnJvciB8IG51bGwsIGF1dGhEYXRhOiBDaGFubmVsQXV0aG9yaXphdGlvbkRhdGEpID0+IHtcbiAgICAgICAgaWYgKGVycm9yKSB7XG4gICAgICAgICAgY2FsbGJhY2soZXJyb3IsIGF1dGhEYXRhKTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgbGV0IHNoYXJlZFNlY3JldCA9IGF1dGhEYXRhWydzaGFyZWRfc2VjcmV0J107XG4gICAgICAgIGlmICghc2hhcmVkU2VjcmV0KSB7XG4gICAgICAgICAgY2FsbGJhY2soXG4gICAgICAgICAgICBuZXcgRXJyb3IoXG4gICAgICAgICAgICAgIGBObyBzaGFyZWRfc2VjcmV0IGtleSBpbiBhdXRoIHBheWxvYWQgZm9yIGVuY3J5cHRlZCBjaGFubmVsOiAke3RoaXMubmFtZX1gXG4gICAgICAgICAgICApLFxuICAgICAgICAgICAgbnVsbFxuICAgICAgICAgICk7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMua2V5ID0gZGVjb2RlQmFzZTY0KHNoYXJlZFNlY3JldCk7XG4gICAgICAgIGRlbGV0ZSBhdXRoRGF0YVsnc2hhcmVkX3NlY3JldCddO1xuICAgICAgICBjYWxsYmFjayhudWxsLCBhdXRoRGF0YSk7XG4gICAgICB9XG4gICAgKTtcbiAgfVxuXG4gIHRyaWdnZXIoZXZlbnQ6IHN0cmluZywgZGF0YTogYW55KTogYm9vbGVhbiB7XG4gICAgdGhyb3cgbmV3IEVycm9ycy5VbnN1cHBvcnRlZEZlYXR1cmUoXG4gICAgICAnQ2xpZW50IGV2ZW50cyBhcmUgbm90IGN1cnJlbnRseSBzdXBwb3J0ZWQgZm9yIGVuY3J5cHRlZCBjaGFubmVscydcbiAgICApO1xuICB9XG5cbiAgLyoqIEhhbmRsZXMgYW4gZXZlbnQuIEZvciBpbnRlcm5hbCB1c2Ugb25seS5cbiAgICpcbiAgICogQHBhcmFtIHtQdXNoZXJFdmVudH0gZXZlbnRcbiAgICovXG4gIGhhbmRsZUV2ZW50KGV2ZW50OiBQdXNoZXJFdmVudCkge1xuICAgIHZhciBldmVudE5hbWUgPSBldmVudC5ldmVudDtcbiAgICB2YXIgZGF0YSA9IGV2ZW50LmRhdGE7XG4gICAgaWYgKFxuICAgICAgZXZlbnROYW1lLmluZGV4T2YoJ3B1c2hlcl9pbnRlcm5hbDonKSA9PT0gMCB8fFxuICAgICAgZXZlbnROYW1lLmluZGV4T2YoJ3B1c2hlcjonKSA9PT0gMFxuICAgICkge1xuICAgICAgc3VwZXIuaGFuZGxlRXZlbnQoZXZlbnQpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLmhhbmRsZUVuY3J5cHRlZEV2ZW50KGV2ZW50TmFtZSwgZGF0YSk7XG4gIH1cblxuICBwcml2YXRlIGhhbmRsZUVuY3J5cHRlZEV2ZW50KGV2ZW50OiBzdHJpbmcsIGRhdGE6IGFueSk6IHZvaWQge1xuICAgIGlmICghdGhpcy5rZXkpIHtcbiAgICAgIExvZ2dlci5kZWJ1ZyhcbiAgICAgICAgJ1JlY2VpdmVkIGVuY3J5cHRlZCBldmVudCBiZWZvcmUga2V5IGhhcyBiZWVuIHJldHJpZXZlZCBmcm9tIHRoZSBhdXRoRW5kcG9pbnQnXG4gICAgICApO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBpZiAoIWRhdGEuY2lwaGVydGV4dCB8fCAhZGF0YS5ub25jZSkge1xuICAgICAgTG9nZ2VyLmVycm9yKFxuICAgICAgICAnVW5leHBlY3RlZCBmb3JtYXQgZm9yIGVuY3J5cHRlZCBldmVudCwgZXhwZWN0ZWQgb2JqZWN0IHdpdGggYGNpcGhlcnRleHRgIGFuZCBgbm9uY2VgIGZpZWxkcywgZ290OiAnICtcbiAgICAgICAgICBkYXRhXG4gICAgICApO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBsZXQgY2lwaGVyVGV4dCA9IGRlY29kZUJhc2U2NChkYXRhLmNpcGhlcnRleHQpO1xuICAgIGlmIChjaXBoZXJUZXh0Lmxlbmd0aCA8IHRoaXMubmFjbC5zZWNyZXRib3gub3ZlcmhlYWRMZW5ndGgpIHtcbiAgICAgIExvZ2dlci5lcnJvcihcbiAgICAgICAgYEV4cGVjdGVkIGVuY3J5cHRlZCBldmVudCBjaXBoZXJ0ZXh0IGxlbmd0aCB0byBiZSAke3RoaXMubmFjbC5zZWNyZXRib3gub3ZlcmhlYWRMZW5ndGh9LCBnb3Q6ICR7Y2lwaGVyVGV4dC5sZW5ndGh9YFxuICAgICAgKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgbGV0IG5vbmNlID0gZGVjb2RlQmFzZTY0KGRhdGEubm9uY2UpO1xuICAgIGlmIChub25jZS5sZW5ndGggPCB0aGlzLm5hY2wuc2VjcmV0Ym94Lm5vbmNlTGVuZ3RoKSB7XG4gICAgICBMb2dnZXIuZXJyb3IoXG4gICAgICAgIGBFeHBlY3RlZCBlbmNyeXB0ZWQgZXZlbnQgbm9uY2UgbGVuZ3RoIHRvIGJlICR7dGhpcy5uYWNsLnNlY3JldGJveC5ub25jZUxlbmd0aH0sIGdvdDogJHtub25jZS5sZW5ndGh9YFxuICAgICAgKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBsZXQgYnl0ZXMgPSB0aGlzLm5hY2wuc2VjcmV0Ym94Lm9wZW4oY2lwaGVyVGV4dCwgbm9uY2UsIHRoaXMua2V5KTtcbiAgICBpZiAoYnl0ZXMgPT09IG51bGwpIHtcbiAgICAgIExvZ2dlci5kZWJ1ZyhcbiAgICAgICAgJ0ZhaWxlZCB0byBkZWNyeXB0IGFuIGV2ZW50LCBwcm9iYWJseSBiZWNhdXNlIGl0IHdhcyBlbmNyeXB0ZWQgd2l0aCBhIGRpZmZlcmVudCBrZXkuIEZldGNoaW5nIGEgbmV3IGtleSBmcm9tIHRoZSBhdXRoRW5kcG9pbnQuLi4nXG4gICAgICApO1xuICAgICAgLy8gVHJ5IGEgc2luZ2xlIHRpbWUgdG8gcmV0cmlldmUgYSBuZXcgYXV0aCBrZXkgYW5kIGRlY3J5cHQgdGhlIGV2ZW50IHdpdGggaXRcbiAgICAgIC8vIElmIHRoaXMgZmFpbHMsIGEgbmV3IGtleSB3aWxsIGJlIHJlcXVlc3RlZCB3aGVuIGEgbmV3IG1lc3NhZ2UgaXMgcmVjZWl2ZWRcbiAgICAgIHRoaXMuYXV0aG9yaXplKHRoaXMucHVzaGVyLmNvbm5lY3Rpb24uc29ja2V0X2lkLCAoZXJyb3IsIGF1dGhEYXRhKSA9PiB7XG4gICAgICAgIGlmIChlcnJvcikge1xuICAgICAgICAgIExvZ2dlci5lcnJvcihcbiAgICAgICAgICAgIGBGYWlsZWQgdG8gbWFrZSBhIHJlcXVlc3QgdG8gdGhlIGF1dGhFbmRwb2ludDogJHthdXRoRGF0YX0uIFVuYWJsZSB0byBmZXRjaCBuZXcga2V5LCBzbyBkcm9wcGluZyBlbmNyeXB0ZWQgZXZlbnRgXG4gICAgICAgICAgKTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgYnl0ZXMgPSB0aGlzLm5hY2wuc2VjcmV0Ym94Lm9wZW4oY2lwaGVyVGV4dCwgbm9uY2UsIHRoaXMua2V5KTtcbiAgICAgICAgaWYgKGJ5dGVzID09PSBudWxsKSB7XG4gICAgICAgICAgTG9nZ2VyLmVycm9yKFxuICAgICAgICAgICAgYEZhaWxlZCB0byBkZWNyeXB0IGV2ZW50IHdpdGggbmV3IGtleS4gRHJvcHBpbmcgZW5jcnlwdGVkIGV2ZW50YFxuICAgICAgICAgICk7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIHRoaXMuZW1pdChldmVudCwgdGhpcy5nZXREYXRhVG9FbWl0KGJ5dGVzKSk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLmVtaXQoZXZlbnQsIHRoaXMuZ2V0RGF0YVRvRW1pdChieXRlcykpO1xuICB9XG5cbiAgLy8gVHJ5IGFuZCBwYXJzZSB0aGUgZGVjcnlwdGVkIGJ5dGVzIGFzIEpTT04uIElmIHdlIGNhbid0IHBhcnNlIGl0LCBqdXN0XG4gIC8vIHJldHVybiB0aGUgdXRmLTggc3RyaW5nXG4gIGdldERhdGFUb0VtaXQoYnl0ZXM6IFVpbnQ4QXJyYXkpOiBzdHJpbmcge1xuICAgIGxldCByYXcgPSBlbmNvZGVVVEY4KGJ5dGVzKTtcbiAgICB0cnkge1xuICAgICAgcmV0dXJuIEpTT04ucGFyc2UocmF3KTtcbiAgICB9IGNhdGNoIHtcbiAgICAgIHJldHVybiByYXc7XG4gICAgfVxuICB9XG59XG4iLCAiaW1wb3J0IHsgZGVmYXVsdCBhcyBFdmVudHNEaXNwYXRjaGVyIH0gZnJvbSAnLi4vZXZlbnRzL2Rpc3BhdGNoZXInO1xuaW1wb3J0IHsgT25lT2ZmVGltZXIgYXMgVGltZXIgfSBmcm9tICcuLi91dGlscy90aW1lcnMnO1xuaW1wb3J0IHsgQ29uZmlnIH0gZnJvbSAnLi4vY29uZmlnJztcbmltcG9ydCBMb2dnZXIgZnJvbSAnLi4vbG9nZ2VyJztcbmltcG9ydCBIYW5kc2hha2VQYXlsb2FkIGZyb20gJy4vaGFuZHNoYWtlL2hhbmRzaGFrZV9wYXlsb2FkJztcbmltcG9ydCBDb25uZWN0aW9uIGZyb20gJy4vY29ubmVjdGlvbic7XG5pbXBvcnQgU3RyYXRlZ3kgZnJvbSAnLi4vc3RyYXRlZ2llcy9zdHJhdGVneSc7XG5pbXBvcnQgU3RyYXRlZ3lSdW5uZXIgZnJvbSAnLi4vc3RyYXRlZ2llcy9zdHJhdGVneV9ydW5uZXInO1xuaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnLi4vdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IFRpbWVsaW5lIGZyb20gJy4uL3RpbWVsaW5lL3RpbWVsaW5lJztcbmltcG9ydCBDb25uZWN0aW9uTWFuYWdlck9wdGlvbnMgZnJvbSAnLi9jb25uZWN0aW9uX21hbmFnZXJfb3B0aW9ucyc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcblxuaW1wb3J0IHtcbiAgRXJyb3JDYWxsYmFja3MsXG4gIEhhbmRzaGFrZUNhbGxiYWNrcyxcbiAgQ29ubmVjdGlvbkNhbGxiYWNrc1xufSBmcm9tICcuL2NhbGxiYWNrcyc7XG5pbXBvcnQgQWN0aW9uIGZyb20gJy4vcHJvdG9jb2wvYWN0aW9uJztcblxuLyoqIE1hbmFnZXMgY29ubmVjdGlvbiB0byBQdXNoZXIuXG4gKlxuICogVXNlcyBhIHN0cmF0ZWd5IChjdXJyZW50bHkgb25seSBkZWZhdWx0KSwgdGltZXJzIGFuZCBuZXR3b3JrIGF2YWlsYWJpbGl0eVxuICogaW5mbyB0byBlc3RhYmxpc2ggYSBjb25uZWN0aW9uIGFuZCBleHBvcnQgaXRzIHN0YXRlLiBJbiBjYXNlIG9mIGZhaWx1cmVzLFxuICogbWFuYWdlcyByZWNvbm5lY3Rpb24gYXR0ZW1wdHMuXG4gKlxuICogRXhwb3J0cyBzdGF0ZSBjaGFuZ2VzIGFzIGZvbGxvd2luZyBldmVudHM6XG4gKiAtIFwic3RhdGVfY2hhbmdlXCIsIHsgcHJldmlvdXM6IHAsIGN1cnJlbnQ6IHN0YXRlIH1cbiAqIC0gc3RhdGVcbiAqXG4gKiBTdGF0ZXM6XG4gKiAtIGluaXRpYWxpemVkIC0gaW5pdGlhbCBzdGF0ZSwgbmV2ZXIgdHJhbnNpdGlvbmVkIHRvXG4gKiAtIGNvbm5lY3RpbmcgLSBjb25uZWN0aW9uIGlzIGJlaW5nIGVzdGFibGlzaGVkXG4gKiAtIGNvbm5lY3RlZCAtIGNvbm5lY3Rpb24gaGFzIGJlZW4gZnVsbHkgZXN0YWJsaXNoZWRcbiAqIC0gZGlzY29ubmVjdGVkIC0gb24gcmVxdWVzdGVkIGRpc2Nvbm5lY3Rpb25cbiAqIC0gdW5hdmFpbGFibGUgLSBhZnRlciBjb25uZWN0aW9uIHRpbWVvdXQgb3Igd2hlbiB0aGVyZSdzIG5vIG5ldHdvcmtcbiAqIC0gZmFpbGVkIC0gd2hlbiB0aGUgY29ubmVjdGlvbiBzdHJhdGVneSBpcyBub3Qgc3VwcG9ydGVkXG4gKlxuICogT3B0aW9uczpcbiAqIC0gdW5hdmFpbGFibGVUaW1lb3V0IC0gdGltZSB0byB0cmFuc2l0aW9uIHRvIHVuYXZhaWxhYmxlIHN0YXRlXG4gKiAtIGFjdGl2aXR5VGltZW91dCAtIHRpbWUgYWZ0ZXIgd2hpY2ggcGluZyBtZXNzYWdlIHNob3VsZCBiZSBzZW50XG4gKiAtIHBvbmdUaW1lb3V0IC0gdGltZSBmb3IgUHVzaGVyIHRvIHJlc3BvbmQgd2l0aCBwb25nIGJlZm9yZSByZWNvbm5lY3RpbmdcbiAqXG4gKiBAcGFyYW0ge1N0cmluZ30ga2V5IGFwcGxpY2F0aW9uIGtleVxuICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ29ubmVjdGlvbk1hbmFnZXIgZXh0ZW5kcyBFdmVudHNEaXNwYXRjaGVyIHtcbiAga2V5OiBzdHJpbmc7XG4gIG9wdGlvbnM6IENvbm5lY3Rpb25NYW5hZ2VyT3B0aW9ucztcbiAgc3RhdGU6IHN0cmluZztcbiAgY29ubmVjdGlvbjogQ29ubmVjdGlvbjtcbiAgdXNpbmdUTFM6IGJvb2xlYW47XG4gIHRpbWVsaW5lOiBUaW1lbGluZTtcbiAgc29ja2V0X2lkOiBzdHJpbmc7XG4gIHVuYXZhaWxhYmxlVGltZXI6IFRpbWVyO1xuICBhY3Rpdml0eVRpbWVyOiBUaW1lcjtcbiAgcmV0cnlUaW1lcjogVGltZXI7XG4gIGFjdGl2aXR5VGltZW91dDogbnVtYmVyO1xuICBzdHJhdGVneTogU3RyYXRlZ3k7XG4gIHJ1bm5lcjogU3RyYXRlZ3lSdW5uZXI7XG4gIGVycm9yQ2FsbGJhY2tzOiBFcnJvckNhbGxiYWNrcztcbiAgaGFuZHNoYWtlQ2FsbGJhY2tzOiBIYW5kc2hha2VDYWxsYmFja3M7XG4gIGNvbm5lY3Rpb25DYWxsYmFja3M6IENvbm5lY3Rpb25DYWxsYmFja3M7XG5cbiAgY29uc3RydWN0b3Ioa2V5OiBzdHJpbmcsIG9wdGlvbnM6IENvbm5lY3Rpb25NYW5hZ2VyT3B0aW9ucykge1xuICAgIHN1cGVyKCk7XG4gICAgdGhpcy5zdGF0ZSA9ICdpbml0aWFsaXplZCc7XG4gICAgdGhpcy5jb25uZWN0aW9uID0gbnVsbDtcblxuICAgIHRoaXMua2V5ID0ga2V5O1xuICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnM7XG4gICAgdGhpcy50aW1lbGluZSA9IHRoaXMub3B0aW9ucy50aW1lbGluZTtcbiAgICB0aGlzLnVzaW5nVExTID0gdGhpcy5vcHRpb25zLnVzZVRMUztcblxuICAgIHRoaXMuZXJyb3JDYWxsYmFja3MgPSB0aGlzLmJ1aWxkRXJyb3JDYWxsYmFja3MoKTtcbiAgICB0aGlzLmNvbm5lY3Rpb25DYWxsYmFja3MgPSB0aGlzLmJ1aWxkQ29ubmVjdGlvbkNhbGxiYWNrcyhcbiAgICAgIHRoaXMuZXJyb3JDYWxsYmFja3NcbiAgICApO1xuICAgIHRoaXMuaGFuZHNoYWtlQ2FsbGJhY2tzID0gdGhpcy5idWlsZEhhbmRzaGFrZUNhbGxiYWNrcyh0aGlzLmVycm9yQ2FsbGJhY2tzKTtcblxuICAgIHZhciBOZXR3b3JrID0gUnVudGltZS5nZXROZXR3b3JrKCk7XG5cbiAgICBOZXR3b3JrLmJpbmQoJ29ubGluZScsICgpID0+IHtcbiAgICAgIHRoaXMudGltZWxpbmUuaW5mbyh7IG5ldGluZm86ICdvbmxpbmUnIH0pO1xuICAgICAgaWYgKHRoaXMuc3RhdGUgPT09ICdjb25uZWN0aW5nJyB8fCB0aGlzLnN0YXRlID09PSAndW5hdmFpbGFibGUnKSB7XG4gICAgICAgIHRoaXMucmV0cnlJbigwKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgICBOZXR3b3JrLmJpbmQoJ29mZmxpbmUnLCAoKSA9PiB7XG4gICAgICB0aGlzLnRpbWVsaW5lLmluZm8oeyBuZXRpbmZvOiAnb2ZmbGluZScgfSk7XG4gICAgICBpZiAodGhpcy5jb25uZWN0aW9uKSB7XG4gICAgICAgIHRoaXMuc2VuZEFjdGl2aXR5Q2hlY2soKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHRoaXMudXBkYXRlU3RyYXRlZ3koKTtcbiAgfVxuXG4gIC8qKiBFc3RhYmxpc2hlcyBhIGNvbm5lY3Rpb24gdG8gUHVzaGVyLlxuICAgKlxuICAgKiBEb2VzIG5vdGhpbmcgd2hlbiBjb25uZWN0aW9uIGlzIGFscmVhZHkgZXN0YWJsaXNoZWQuIFNlZSB0b3AtbGV2ZWwgZG9jXG4gICAqIHRvIGZpbmQgZXZlbnRzIGVtaXR0ZWQgb24gY29ubmVjdGlvbiBhdHRlbXB0cy5cbiAgICovXG4gIGNvbm5lY3QoKSB7XG4gICAgaWYgKHRoaXMuY29ubmVjdGlvbiB8fCB0aGlzLnJ1bm5lcikge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBpZiAoIXRoaXMuc3RyYXRlZ3kuaXNTdXBwb3J0ZWQoKSkge1xuICAgICAgdGhpcy51cGRhdGVTdGF0ZSgnZmFpbGVkJyk7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHRoaXMudXBkYXRlU3RhdGUoJ2Nvbm5lY3RpbmcnKTtcbiAgICB0aGlzLnN0YXJ0Q29ubmVjdGluZygpO1xuICAgIHRoaXMuc2V0VW5hdmFpbGFibGVUaW1lcigpO1xuICB9XG5cbiAgLyoqIFNlbmRzIHJhdyBkYXRhLlxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gZGF0YVxuICAgKi9cbiAgc2VuZChkYXRhKSB7XG4gICAgaWYgKHRoaXMuY29ubmVjdGlvbikge1xuICAgICAgcmV0dXJuIHRoaXMuY29ubmVjdGlvbi5zZW5kKGRhdGEpO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG5cbiAgLyoqIFNlbmRzIGFuIGV2ZW50LlxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICAgKiBAcGFyYW0ge1N0cmluZ30gZGF0YVxuICAgKiBAcGFyYW0ge1N0cmluZ30gW2NoYW5uZWxdXG4gICAqIEByZXR1cm5zIHtCb29sZWFufSB3aGV0aGVyIG1lc3NhZ2Ugd2FzIHNlbnQgb3Igbm90XG4gICAqL1xuICBzZW5kX2V2ZW50KG5hbWU6IHN0cmluZywgZGF0YTogYW55LCBjaGFubmVsPzogc3RyaW5nKSB7XG4gICAgaWYgKHRoaXMuY29ubmVjdGlvbikge1xuICAgICAgcmV0dXJuIHRoaXMuY29ubmVjdGlvbi5zZW5kX2V2ZW50KG5hbWUsIGRhdGEsIGNoYW5uZWwpO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG5cbiAgLyoqIENsb3NlcyB0aGUgY29ubmVjdGlvbi4gKi9cbiAgZGlzY29ubmVjdCgpIHtcbiAgICB0aGlzLmRpc2Nvbm5lY3RJbnRlcm5hbGx5KCk7XG4gICAgdGhpcy51cGRhdGVTdGF0ZSgnZGlzY29ubmVjdGVkJyk7XG4gIH1cblxuICBpc1VzaW5nVExTKCkge1xuICAgIHJldHVybiB0aGlzLnVzaW5nVExTO1xuICB9XG5cbiAgcHJpdmF0ZSBzdGFydENvbm5lY3RpbmcoKSB7XG4gICAgdmFyIGNhbGxiYWNrID0gKGVycm9yLCBoYW5kc2hha2UpID0+IHtcbiAgICAgIGlmIChlcnJvcikge1xuICAgICAgICB0aGlzLnJ1bm5lciA9IHRoaXMuc3RyYXRlZ3kuY29ubmVjdCgwLCBjYWxsYmFjayk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBpZiAoaGFuZHNoYWtlLmFjdGlvbiA9PT0gJ2Vycm9yJykge1xuICAgICAgICAgIHRoaXMuZW1pdCgnZXJyb3InLCB7XG4gICAgICAgICAgICB0eXBlOiAnSGFuZHNoYWtlRXJyb3InLFxuICAgICAgICAgICAgZXJyb3I6IGhhbmRzaGFrZS5lcnJvclxuICAgICAgICAgIH0pO1xuICAgICAgICAgIHRoaXMudGltZWxpbmUuZXJyb3IoeyBoYW5kc2hha2VFcnJvcjogaGFuZHNoYWtlLmVycm9yIH0pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHRoaXMuYWJvcnRDb25uZWN0aW5nKCk7IC8vIHdlIGRvbid0IHN1cHBvcnQgc3dpdGNoaW5nIGNvbm5lY3Rpb25zIHlldFxuICAgICAgICAgIHRoaXMuaGFuZHNoYWtlQ2FsbGJhY2tzW2hhbmRzaGFrZS5hY3Rpb25dKGhhbmRzaGFrZSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9O1xuICAgIHRoaXMucnVubmVyID0gdGhpcy5zdHJhdGVneS5jb25uZWN0KDAsIGNhbGxiYWNrKTtcbiAgfVxuXG4gIHByaXZhdGUgYWJvcnRDb25uZWN0aW5nKCkge1xuICAgIGlmICh0aGlzLnJ1bm5lcikge1xuICAgICAgdGhpcy5ydW5uZXIuYWJvcnQoKTtcbiAgICAgIHRoaXMucnVubmVyID0gbnVsbDtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIGRpc2Nvbm5lY3RJbnRlcm5hbGx5KCkge1xuICAgIHRoaXMuYWJvcnRDb25uZWN0aW5nKCk7XG4gICAgdGhpcy5jbGVhclJldHJ5VGltZXIoKTtcbiAgICB0aGlzLmNsZWFyVW5hdmFpbGFibGVUaW1lcigpO1xuICAgIGlmICh0aGlzLmNvbm5lY3Rpb24pIHtcbiAgICAgIHZhciBjb25uZWN0aW9uID0gdGhpcy5hYmFuZG9uQ29ubmVjdGlvbigpO1xuICAgICAgY29ubmVjdGlvbi5jbG9zZSgpO1xuICAgIH1cbiAgfVxuXG4gIHByaXZhdGUgdXBkYXRlU3RyYXRlZ3koKSB7XG4gICAgdGhpcy5zdHJhdGVneSA9IHRoaXMub3B0aW9ucy5nZXRTdHJhdGVneSh7XG4gICAgICBrZXk6IHRoaXMua2V5LFxuICAgICAgdGltZWxpbmU6IHRoaXMudGltZWxpbmUsXG4gICAgICB1c2VUTFM6IHRoaXMudXNpbmdUTFNcbiAgICB9KTtcbiAgfVxuXG4gIHByaXZhdGUgcmV0cnlJbihkZWxheSkge1xuICAgIHRoaXMudGltZWxpbmUuaW5mbyh7IGFjdGlvbjogJ3JldHJ5JywgZGVsYXk6IGRlbGF5IH0pO1xuICAgIGlmIChkZWxheSA+IDApIHtcbiAgICAgIHRoaXMuZW1pdCgnY29ubmVjdGluZ19pbicsIE1hdGgucm91bmQoZGVsYXkgLyAxMDAwKSk7XG4gICAgfVxuICAgIHRoaXMucmV0cnlUaW1lciA9IG5ldyBUaW1lcihkZWxheSB8fCAwLCAoKSA9PiB7XG4gICAgICB0aGlzLmRpc2Nvbm5lY3RJbnRlcm5hbGx5KCk7XG4gICAgICB0aGlzLmNvbm5lY3QoKTtcbiAgICB9KTtcbiAgfVxuXG4gIHByaXZhdGUgY2xlYXJSZXRyeVRpbWVyKCkge1xuICAgIGlmICh0aGlzLnJldHJ5VGltZXIpIHtcbiAgICAgIHRoaXMucmV0cnlUaW1lci5lbnN1cmVBYm9ydGVkKCk7XG4gICAgICB0aGlzLnJldHJ5VGltZXIgPSBudWxsO1xuICAgIH1cbiAgfVxuXG4gIHByaXZhdGUgc2V0VW5hdmFpbGFibGVUaW1lcigpIHtcbiAgICB0aGlzLnVuYXZhaWxhYmxlVGltZXIgPSBuZXcgVGltZXIodGhpcy5vcHRpb25zLnVuYXZhaWxhYmxlVGltZW91dCwgKCkgPT4ge1xuICAgICAgdGhpcy51cGRhdGVTdGF0ZSgndW5hdmFpbGFibGUnKTtcbiAgICB9KTtcbiAgfVxuXG4gIHByaXZhdGUgY2xlYXJVbmF2YWlsYWJsZVRpbWVyKCkge1xuICAgIGlmICh0aGlzLnVuYXZhaWxhYmxlVGltZXIpIHtcbiAgICAgIHRoaXMudW5hdmFpbGFibGVUaW1lci5lbnN1cmVBYm9ydGVkKCk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBzZW5kQWN0aXZpdHlDaGVjaygpIHtcbiAgICB0aGlzLnN0b3BBY3Rpdml0eUNoZWNrKCk7XG4gICAgdGhpcy5jb25uZWN0aW9uLnBpbmcoKTtcbiAgICAvLyB3YWl0IGZvciBwb25nIHJlc3BvbnNlXG4gICAgdGhpcy5hY3Rpdml0eVRpbWVyID0gbmV3IFRpbWVyKHRoaXMub3B0aW9ucy5wb25nVGltZW91dCwgKCkgPT4ge1xuICAgICAgdGhpcy50aW1lbGluZS5lcnJvcih7IHBvbmdfdGltZWRfb3V0OiB0aGlzLm9wdGlvbnMucG9uZ1RpbWVvdXQgfSk7XG4gICAgICB0aGlzLnJldHJ5SW4oMCk7XG4gICAgfSk7XG4gIH1cblxuICBwcml2YXRlIHJlc2V0QWN0aXZpdHlDaGVjaygpIHtcbiAgICB0aGlzLnN0b3BBY3Rpdml0eUNoZWNrKCk7XG4gICAgLy8gc2VuZCBwaW5nIGFmdGVyIGluYWN0aXZpdHlcbiAgICBpZiAodGhpcy5jb25uZWN0aW9uICYmICF0aGlzLmNvbm5lY3Rpb24uaGFuZGxlc0FjdGl2aXR5Q2hlY2tzKCkpIHtcbiAgICAgIHRoaXMuYWN0aXZpdHlUaW1lciA9IG5ldyBUaW1lcih0aGlzLmFjdGl2aXR5VGltZW91dCwgKCkgPT4ge1xuICAgICAgICB0aGlzLnNlbmRBY3Rpdml0eUNoZWNrKCk7XG4gICAgICB9KTtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIHN0b3BBY3Rpdml0eUNoZWNrKCkge1xuICAgIGlmICh0aGlzLmFjdGl2aXR5VGltZXIpIHtcbiAgICAgIHRoaXMuYWN0aXZpdHlUaW1lci5lbnN1cmVBYm9ydGVkKCk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBidWlsZENvbm5lY3Rpb25DYWxsYmFja3MoXG4gICAgZXJyb3JDYWxsYmFja3M6IEVycm9yQ2FsbGJhY2tzXG4gICk6IENvbm5lY3Rpb25DYWxsYmFja3Mge1xuICAgIHJldHVybiBDb2xsZWN0aW9ucy5leHRlbmQ8Q29ubmVjdGlvbkNhbGxiYWNrcz4oe30sIGVycm9yQ2FsbGJhY2tzLCB7XG4gICAgICBtZXNzYWdlOiBtZXNzYWdlID0+IHtcbiAgICAgICAgLy8gaW5jbHVkZXMgcG9uZyBtZXNzYWdlcyBmcm9tIHNlcnZlclxuICAgICAgICB0aGlzLnJlc2V0QWN0aXZpdHlDaGVjaygpO1xuICAgICAgICB0aGlzLmVtaXQoJ21lc3NhZ2UnLCBtZXNzYWdlKTtcbiAgICAgIH0sXG4gICAgICBwaW5nOiAoKSA9PiB7XG4gICAgICAgIHRoaXMuc2VuZF9ldmVudCgncHVzaGVyOnBvbmcnLCB7fSk7XG4gICAgICB9LFxuICAgICAgYWN0aXZpdHk6ICgpID0+IHtcbiAgICAgICAgdGhpcy5yZXNldEFjdGl2aXR5Q2hlY2soKTtcbiAgICAgIH0sXG4gICAgICBlcnJvcjogZXJyb3IgPT4ge1xuICAgICAgICAvLyBqdXN0IGVtaXQgZXJyb3IgdG8gdXNlciAtIHNvY2tldCB3aWxsIGFscmVhZHkgYmUgY2xvc2VkIGJ5IGJyb3dzZXJcbiAgICAgICAgdGhpcy5lbWl0KCdlcnJvcicsIGVycm9yKTtcbiAgICAgIH0sXG4gICAgICBjbG9zZWQ6ICgpID0+IHtcbiAgICAgICAgdGhpcy5hYmFuZG9uQ29ubmVjdGlvbigpO1xuICAgICAgICBpZiAodGhpcy5zaG91bGRSZXRyeSgpKSB7XG4gICAgICAgICAgdGhpcy5yZXRyeUluKDEwMDApO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICBwcml2YXRlIGJ1aWxkSGFuZHNoYWtlQ2FsbGJhY2tzKFxuICAgIGVycm9yQ2FsbGJhY2tzOiBFcnJvckNhbGxiYWNrc1xuICApOiBIYW5kc2hha2VDYWxsYmFja3Mge1xuICAgIHJldHVybiBDb2xsZWN0aW9ucy5leHRlbmQ8SGFuZHNoYWtlQ2FsbGJhY2tzPih7fSwgZXJyb3JDYWxsYmFja3MsIHtcbiAgICAgIGNvbm5lY3RlZDogKGhhbmRzaGFrZTogSGFuZHNoYWtlUGF5bG9hZCkgPT4ge1xuICAgICAgICB0aGlzLmFjdGl2aXR5VGltZW91dCA9IE1hdGgubWluKFxuICAgICAgICAgIHRoaXMub3B0aW9ucy5hY3Rpdml0eVRpbWVvdXQsXG4gICAgICAgICAgaGFuZHNoYWtlLmFjdGl2aXR5VGltZW91dCxcbiAgICAgICAgICBoYW5kc2hha2UuY29ubmVjdGlvbi5hY3Rpdml0eVRpbWVvdXQgfHwgSW5maW5pdHlcbiAgICAgICAgKTtcbiAgICAgICAgdGhpcy5jbGVhclVuYXZhaWxhYmxlVGltZXIoKTtcbiAgICAgICAgdGhpcy5zZXRDb25uZWN0aW9uKGhhbmRzaGFrZS5jb25uZWN0aW9uKTtcbiAgICAgICAgdGhpcy5zb2NrZXRfaWQgPSB0aGlzLmNvbm5lY3Rpb24uaWQ7XG4gICAgICAgIHRoaXMudXBkYXRlU3RhdGUoJ2Nvbm5lY3RlZCcsIHsgc29ja2V0X2lkOiB0aGlzLnNvY2tldF9pZCB9KTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIHByaXZhdGUgYnVpbGRFcnJvckNhbGxiYWNrcygpOiBFcnJvckNhbGxiYWNrcyB7XG4gICAgbGV0IHdpdGhFcnJvckVtaXR0ZWQgPSBjYWxsYmFjayA9PiB7XG4gICAgICByZXR1cm4gKHJlc3VsdDogQWN0aW9uIHwgSGFuZHNoYWtlUGF5bG9hZCkgPT4ge1xuICAgICAgICBpZiAocmVzdWx0LmVycm9yKSB7XG4gICAgICAgICAgdGhpcy5lbWl0KCdlcnJvcicsIHsgdHlwZTogJ1dlYlNvY2tldEVycm9yJywgZXJyb3I6IHJlc3VsdC5lcnJvciB9KTtcbiAgICAgICAgfVxuICAgICAgICBjYWxsYmFjayhyZXN1bHQpO1xuICAgICAgfTtcbiAgICB9O1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIHRsc19vbmx5OiB3aXRoRXJyb3JFbWl0dGVkKCgpID0+IHtcbiAgICAgICAgdGhpcy51c2luZ1RMUyA9IHRydWU7XG4gICAgICAgIHRoaXMudXBkYXRlU3RyYXRlZ3koKTtcbiAgICAgICAgdGhpcy5yZXRyeUluKDApO1xuICAgICAgfSksXG4gICAgICByZWZ1c2VkOiB3aXRoRXJyb3JFbWl0dGVkKCgpID0+IHtcbiAgICAgICAgdGhpcy5kaXNjb25uZWN0KCk7XG4gICAgICB9KSxcbiAgICAgIGJhY2tvZmY6IHdpdGhFcnJvckVtaXR0ZWQoKCkgPT4ge1xuICAgICAgICB0aGlzLnJldHJ5SW4oMTAwMCk7XG4gICAgICB9KSxcbiAgICAgIHJldHJ5OiB3aXRoRXJyb3JFbWl0dGVkKCgpID0+IHtcbiAgICAgICAgdGhpcy5yZXRyeUluKDApO1xuICAgICAgfSlcbiAgICB9O1xuICB9XG5cbiAgcHJpdmF0ZSBzZXRDb25uZWN0aW9uKGNvbm5lY3Rpb24pIHtcbiAgICB0aGlzLmNvbm5lY3Rpb24gPSBjb25uZWN0aW9uO1xuICAgIGZvciAodmFyIGV2ZW50IGluIHRoaXMuY29ubmVjdGlvbkNhbGxiYWNrcykge1xuICAgICAgdGhpcy5jb25uZWN0aW9uLmJpbmQoZXZlbnQsIHRoaXMuY29ubmVjdGlvbkNhbGxiYWNrc1tldmVudF0pO1xuICAgIH1cbiAgICB0aGlzLnJlc2V0QWN0aXZpdHlDaGVjaygpO1xuICB9XG5cbiAgcHJpdmF0ZSBhYmFuZG9uQ29ubmVjdGlvbigpIHtcbiAgICBpZiAoIXRoaXMuY29ubmVjdGlvbikge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLnN0b3BBY3Rpdml0eUNoZWNrKCk7XG4gICAgZm9yICh2YXIgZXZlbnQgaW4gdGhpcy5jb25uZWN0aW9uQ2FsbGJhY2tzKSB7XG4gICAgICB0aGlzLmNvbm5lY3Rpb24udW5iaW5kKGV2ZW50LCB0aGlzLmNvbm5lY3Rpb25DYWxsYmFja3NbZXZlbnRdKTtcbiAgICB9XG4gICAgdmFyIGNvbm5lY3Rpb24gPSB0aGlzLmNvbm5lY3Rpb247XG4gICAgdGhpcy5jb25uZWN0aW9uID0gbnVsbDtcbiAgICByZXR1cm4gY29ubmVjdGlvbjtcbiAgfVxuXG4gIHByaXZhdGUgdXBkYXRlU3RhdGUobmV3U3RhdGU6IHN0cmluZywgZGF0YT86IGFueSkge1xuICAgIHZhciBwcmV2aW91c1N0YXRlID0gdGhpcy5zdGF0ZTtcbiAgICB0aGlzLnN0YXRlID0gbmV3U3RhdGU7XG4gICAgaWYgKHByZXZpb3VzU3RhdGUgIT09IG5ld1N0YXRlKSB7XG4gICAgICB2YXIgbmV3U3RhdGVEZXNjcmlwdGlvbiA9IG5ld1N0YXRlO1xuICAgICAgaWYgKG5ld1N0YXRlRGVzY3JpcHRpb24gPT09ICdjb25uZWN0ZWQnKSB7XG4gICAgICAgIG5ld1N0YXRlRGVzY3JpcHRpb24gKz0gJyB3aXRoIG5ldyBzb2NrZXQgSUQgJyArIGRhdGEuc29ja2V0X2lkO1xuICAgICAgfVxuICAgICAgTG9nZ2VyLmRlYnVnKFxuICAgICAgICAnU3RhdGUgY2hhbmdlZCcsXG4gICAgICAgIHByZXZpb3VzU3RhdGUgKyAnIC0+ICcgKyBuZXdTdGF0ZURlc2NyaXB0aW9uXG4gICAgICApO1xuICAgICAgdGhpcy50aW1lbGluZS5pbmZvKHsgc3RhdGU6IG5ld1N0YXRlLCBwYXJhbXM6IGRhdGEgfSk7XG4gICAgICB0aGlzLmVtaXQoJ3N0YXRlX2NoYW5nZScsIHsgcHJldmlvdXM6IHByZXZpb3VzU3RhdGUsIGN1cnJlbnQ6IG5ld1N0YXRlIH0pO1xuICAgICAgdGhpcy5lbWl0KG5ld1N0YXRlLCBkYXRhKTtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIHNob3VsZFJldHJ5KCk6IGJvb2xlYW4ge1xuICAgIHJldHVybiB0aGlzLnN0YXRlID09PSAnY29ubmVjdGluZycgfHwgdGhpcy5zdGF0ZSA9PT0gJ2Nvbm5lY3RlZCc7XG4gIH1cbn1cbiIsICJpbXBvcnQgQ2hhbm5lbCBmcm9tICcuL2NoYW5uZWwnO1xuaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnLi4vdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IENoYW5uZWxUYWJsZSBmcm9tICcuL2NoYW5uZWxfdGFibGUnO1xuaW1wb3J0IEZhY3RvcnkgZnJvbSAnLi4vdXRpbHMvZmFjdG9yeSc7XG5pbXBvcnQgUHVzaGVyIGZyb20gJy4uL3B1c2hlcic7XG5pbXBvcnQgTG9nZ2VyIGZyb20gJy4uL2xvZ2dlcic7XG5pbXBvcnQgKiBhcyBFcnJvcnMgZnJvbSAnLi4vZXJyb3JzJztcbmltcG9ydCB1cmxTdG9yZSBmcm9tICcuLi91dGlscy91cmxfc3RvcmUnO1xuXG4vKiogSGFuZGxlcyBhIGNoYW5uZWwgbWFwLiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hhbm5lbHMge1xuICBjaGFubmVsczogQ2hhbm5lbFRhYmxlO1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuY2hhbm5lbHMgPSB7fTtcbiAgfVxuXG4gIC8qKiBDcmVhdGVzIG9yIHJldHJpZXZlcyBhbiBleGlzdGluZyBjaGFubmVsIGJ5IGl0cyBuYW1lLlxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICAgKiBAcGFyYW0ge1B1c2hlcn0gcHVzaGVyXG4gICAqIEByZXR1cm4ge0NoYW5uZWx9XG4gICAqL1xuICBhZGQobmFtZTogc3RyaW5nLCBwdXNoZXI6IFB1c2hlcikge1xuICAgIGlmICghdGhpcy5jaGFubmVsc1tuYW1lXSkge1xuICAgICAgdGhpcy5jaGFubmVsc1tuYW1lXSA9IGNyZWF0ZUNoYW5uZWwobmFtZSwgcHVzaGVyKTtcbiAgICB9XG4gICAgcmV0dXJuIHRoaXMuY2hhbm5lbHNbbmFtZV07XG4gIH1cblxuICAvKiogUmV0dXJucyBhIGxpc3Qgb2YgYWxsIGNoYW5uZWxzXG4gICAqXG4gICAqIEByZXR1cm4ge0FycmF5fVxuICAgKi9cbiAgYWxsKCk6IENoYW5uZWxbXSB7XG4gICAgcmV0dXJuIENvbGxlY3Rpb25zLnZhbHVlcyh0aGlzLmNoYW5uZWxzKTtcbiAgfVxuXG4gIC8qKiBGaW5kcyBhIGNoYW5uZWwgYnkgaXRzIG5hbWUuXG4gICAqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lXG4gICAqIEByZXR1cm4ge0NoYW5uZWx9IGNoYW5uZWwgb3IgbnVsbCBpZiBpdCBkb2Vzbid0IGV4aXN0XG4gICAqL1xuICBmaW5kKG5hbWU6IHN0cmluZykge1xuICAgIHJldHVybiB0aGlzLmNoYW5uZWxzW25hbWVdO1xuICB9XG5cbiAgLyoqIFJlbW92ZXMgYSBjaGFubmVsIGZyb20gdGhlIG1hcC5cbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IG5hbWVcbiAgICovXG4gIHJlbW92ZShuYW1lOiBzdHJpbmcpIHtcbiAgICB2YXIgY2hhbm5lbCA9IHRoaXMuY2hhbm5lbHNbbmFtZV07XG4gICAgZGVsZXRlIHRoaXMuY2hhbm5lbHNbbmFtZV07XG4gICAgcmV0dXJuIGNoYW5uZWw7XG4gIH1cblxuICAvKiogUHJveGllcyBkaXNjb25uZWN0aW9uIHNpZ25hbCB0byBhbGwgY2hhbm5lbHMuICovXG4gIGRpc2Nvbm5lY3QoKSB7XG4gICAgQ29sbGVjdGlvbnMub2JqZWN0QXBwbHkodGhpcy5jaGFubmVscywgZnVuY3Rpb24oY2hhbm5lbCkge1xuICAgICAgY2hhbm5lbC5kaXNjb25uZWN0KCk7XG4gICAgfSk7XG4gIH1cbn1cblxuZnVuY3Rpb24gY3JlYXRlQ2hhbm5lbChuYW1lOiBzdHJpbmcsIHB1c2hlcjogUHVzaGVyKTogQ2hhbm5lbCB7XG4gIGlmIChuYW1lLmluZGV4T2YoJ3ByaXZhdGUtZW5jcnlwdGVkLScpID09PSAwKSB7XG4gICAgaWYgKHB1c2hlci5jb25maWcubmFjbCkge1xuICAgICAgcmV0dXJuIEZhY3RvcnkuY3JlYXRlRW5jcnlwdGVkQ2hhbm5lbChuYW1lLCBwdXNoZXIsIHB1c2hlci5jb25maWcubmFjbCk7XG4gICAgfVxuICAgIGxldCBlcnJNc2cgPVxuICAgICAgJ1RyaWVkIHRvIHN1YnNjcmliZSB0byBhIHByaXZhdGUtZW5jcnlwdGVkLSBjaGFubmVsIGJ1dCBubyBuYWNsIGltcGxlbWVudGF0aW9uIGF2YWlsYWJsZSc7XG4gICAgbGV0IHN1ZmZpeCA9IHVybFN0b3JlLmJ1aWxkTG9nU3VmZml4KCdlbmNyeXB0ZWRDaGFubmVsU3VwcG9ydCcpO1xuICAgIHRocm93IG5ldyBFcnJvcnMuVW5zdXBwb3J0ZWRGZWF0dXJlKGAke2Vyck1zZ30uICR7c3VmZml4fWApO1xuICB9IGVsc2UgaWYgKG5hbWUuaW5kZXhPZigncHJpdmF0ZS0nKSA9PT0gMCkge1xuICAgIHJldHVybiBGYWN0b3J5LmNyZWF0ZVByaXZhdGVDaGFubmVsKG5hbWUsIHB1c2hlcik7XG4gIH0gZWxzZSBpZiAobmFtZS5pbmRleE9mKCdwcmVzZW5jZS0nKSA9PT0gMCkge1xuICAgIHJldHVybiBGYWN0b3J5LmNyZWF0ZVByZXNlbmNlQ2hhbm5lbChuYW1lLCBwdXNoZXIpO1xuICB9IGVsc2UgaWYgKG5hbWUuaW5kZXhPZignIycpID09PSAwKSB7XG4gICAgdGhyb3cgbmV3IEVycm9ycy5CYWRDaGFubmVsTmFtZShcbiAgICAgICdDYW5ub3QgY3JlYXRlIGEgY2hhbm5lbCB3aXRoIG5hbWUgXCInICsgbmFtZSArICdcIi4nXG4gICAgKTtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gRmFjdG9yeS5jcmVhdGVDaGFubmVsKG5hbWUsIHB1c2hlcik7XG4gIH1cbn1cbiIsICJpbXBvcnQgQXNzaXN0YW50VG9UaGVUcmFuc3BvcnRNYW5hZ2VyIGZyb20gJy4uL3RyYW5zcG9ydHMvYXNzaXN0YW50X3RvX3RoZV90cmFuc3BvcnRfbWFuYWdlcic7XG5pbXBvcnQgUGluZ0RlbGF5T3B0aW9ucyBmcm9tICcuLi90cmFuc3BvcnRzL3BpbmdfZGVsYXlfb3B0aW9ucyc7XG5pbXBvcnQgVHJhbnNwb3J0IGZyb20gJy4uL3RyYW5zcG9ydHMvdHJhbnNwb3J0JztcbmltcG9ydCBUcmFuc3BvcnRNYW5hZ2VyIGZyb20gJy4uL3RyYW5zcG9ydHMvdHJhbnNwb3J0X21hbmFnZXInO1xuaW1wb3J0IEhhbmRzaGFrZSBmcm9tICcuLi9jb25uZWN0aW9uL2hhbmRzaGFrZSc7XG5pbXBvcnQgVHJhbnNwb3J0Q29ubmVjdGlvbiBmcm9tICcuLi90cmFuc3BvcnRzL3RyYW5zcG9ydF9jb25uZWN0aW9uJztcbmltcG9ydCBTb2NrZXRIb29rcyBmcm9tICcuLi9odHRwL3NvY2tldF9ob29rcyc7XG5pbXBvcnQgSFRUUFNvY2tldCBmcm9tICcuLi9odHRwL2h0dHBfc29ja2V0JztcblxuaW1wb3J0IFRpbWVsaW5lIGZyb20gJy4uL3RpbWVsaW5lL3RpbWVsaW5lJztcbmltcG9ydCB7XG4gIGRlZmF1bHQgYXMgVGltZWxpbmVTZW5kZXIsXG4gIFRpbWVsaW5lU2VuZGVyT3B0aW9uc1xufSBmcm9tICcuLi90aW1lbGluZS90aW1lbGluZV9zZW5kZXInO1xuaW1wb3J0IFByZXNlbmNlQ2hhbm5lbCBmcm9tICcuLi9jaGFubmVscy9wcmVzZW5jZV9jaGFubmVsJztcbmltcG9ydCBQcml2YXRlQ2hhbm5lbCBmcm9tICcuLi9jaGFubmVscy9wcml2YXRlX2NoYW5uZWwnO1xuaW1wb3J0IEVuY3J5cHRlZENoYW5uZWwgZnJvbSAnLi4vY2hhbm5lbHMvZW5jcnlwdGVkX2NoYW5uZWwnO1xuaW1wb3J0IENoYW5uZWwgZnJvbSAnLi4vY2hhbm5lbHMvY2hhbm5lbCc7XG5pbXBvcnQgQ29ubmVjdGlvbk1hbmFnZXIgZnJvbSAnLi4vY29ubmVjdGlvbi9jb25uZWN0aW9uX21hbmFnZXInO1xuaW1wb3J0IENvbm5lY3Rpb25NYW5hZ2VyT3B0aW9ucyBmcm9tICcuLi9jb25uZWN0aW9uL2Nvbm5lY3Rpb25fbWFuYWdlcl9vcHRpb25zJztcbmltcG9ydCBBamF4IGZyb20gJy4uL2h0dHAvYWpheCc7XG5pbXBvcnQgQ2hhbm5lbHMgZnJvbSAnLi4vY2hhbm5lbHMvY2hhbm5lbHMnO1xuaW1wb3J0IFB1c2hlciBmcm9tICcuLi9wdXNoZXInO1xuaW1wb3J0IHsgQ29uZmlnIH0gZnJvbSAnLi4vY29uZmlnJztcbmltcG9ydCAqIGFzIG5hY2wgZnJvbSAndHdlZXRuYWNsJztcblxudmFyIEZhY3RvcnkgPSB7XG4gIGNyZWF0ZUNoYW5uZWxzKCk6IENoYW5uZWxzIHtcbiAgICByZXR1cm4gbmV3IENoYW5uZWxzKCk7XG4gIH0sXG5cbiAgY3JlYXRlQ29ubmVjdGlvbk1hbmFnZXIoXG4gICAga2V5OiBzdHJpbmcsXG4gICAgb3B0aW9uczogQ29ubmVjdGlvbk1hbmFnZXJPcHRpb25zXG4gICk6IENvbm5lY3Rpb25NYW5hZ2VyIHtcbiAgICByZXR1cm4gbmV3IENvbm5lY3Rpb25NYW5hZ2VyKGtleSwgb3B0aW9ucyk7XG4gIH0sXG5cbiAgY3JlYXRlQ2hhbm5lbChuYW1lOiBzdHJpbmcsIHB1c2hlcjogUHVzaGVyKTogQ2hhbm5lbCB7XG4gICAgcmV0dXJuIG5ldyBDaGFubmVsKG5hbWUsIHB1c2hlcik7XG4gIH0sXG5cbiAgY3JlYXRlUHJpdmF0ZUNoYW5uZWwobmFtZTogc3RyaW5nLCBwdXNoZXI6IFB1c2hlcik6IFByaXZhdGVDaGFubmVsIHtcbiAgICByZXR1cm4gbmV3IFByaXZhdGVDaGFubmVsKG5hbWUsIHB1c2hlcik7XG4gIH0sXG5cbiAgY3JlYXRlUHJlc2VuY2VDaGFubmVsKG5hbWU6IHN0cmluZywgcHVzaGVyOiBQdXNoZXIpOiBQcmVzZW5jZUNoYW5uZWwge1xuICAgIHJldHVybiBuZXcgUHJlc2VuY2VDaGFubmVsKG5hbWUsIHB1c2hlcik7XG4gIH0sXG5cbiAgY3JlYXRlRW5jcnlwdGVkQ2hhbm5lbChcbiAgICBuYW1lOiBzdHJpbmcsXG4gICAgcHVzaGVyOiBQdXNoZXIsXG4gICAgbmFjbDogbmFjbFxuICApOiBFbmNyeXB0ZWRDaGFubmVsIHtcbiAgICByZXR1cm4gbmV3IEVuY3J5cHRlZENoYW5uZWwobmFtZSwgcHVzaGVyLCBuYWNsKTtcbiAgfSxcblxuICBjcmVhdGVUaW1lbGluZVNlbmRlcih0aW1lbGluZTogVGltZWxpbmUsIG9wdGlvbnM6IFRpbWVsaW5lU2VuZGVyT3B0aW9ucykge1xuICAgIHJldHVybiBuZXcgVGltZWxpbmVTZW5kZXIodGltZWxpbmUsIG9wdGlvbnMpO1xuICB9LFxuXG4gIGNyZWF0ZUhhbmRzaGFrZShcbiAgICB0cmFuc3BvcnQ6IFRyYW5zcG9ydENvbm5lY3Rpb24sXG4gICAgY2FsbGJhY2s6IChIYW5kc2hha2VQYXlsb2FkKSA9PiB2b2lkXG4gICk6IEhhbmRzaGFrZSB7XG4gICAgcmV0dXJuIG5ldyBIYW5kc2hha2UodHJhbnNwb3J0LCBjYWxsYmFjayk7XG4gIH0sXG5cbiAgY3JlYXRlQXNzaXN0YW50VG9UaGVUcmFuc3BvcnRNYW5hZ2VyKFxuICAgIG1hbmFnZXI6IFRyYW5zcG9ydE1hbmFnZXIsXG4gICAgdHJhbnNwb3J0OiBUcmFuc3BvcnQsXG4gICAgb3B0aW9uczogUGluZ0RlbGF5T3B0aW9uc1xuICApOiBBc3Npc3RhbnRUb1RoZVRyYW5zcG9ydE1hbmFnZXIge1xuICAgIHJldHVybiBuZXcgQXNzaXN0YW50VG9UaGVUcmFuc3BvcnRNYW5hZ2VyKG1hbmFnZXIsIHRyYW5zcG9ydCwgb3B0aW9ucyk7XG4gIH1cbn07XG5cbmV4cG9ydCBkZWZhdWx0IEZhY3Rvcnk7XG4iLCAiaW1wb3J0IEFzc2lzdGFudFRvVGhlVHJhbnNwb3J0TWFuYWdlciBmcm9tICcuL2Fzc2lzdGFudF90b190aGVfdHJhbnNwb3J0X21hbmFnZXInO1xuaW1wb3J0IFRyYW5zcG9ydCBmcm9tICcuL3RyYW5zcG9ydCc7XG5pbXBvcnQgUGluZ0RlbGF5T3B0aW9ucyBmcm9tICcuL3BpbmdfZGVsYXlfb3B0aW9ucyc7XG5pbXBvcnQgRmFjdG9yeSBmcm9tICcuLi91dGlscy9mYWN0b3J5JztcblxuZXhwb3J0IGludGVyZmFjZSBUcmFuc3BvcnRNYW5hZ2VyT3B0aW9ucyBleHRlbmRzIFBpbmdEZWxheU9wdGlvbnMge1xuICBsaXZlcz86IG51bWJlcjtcbn1cblxuLyoqIEtlZXBzIHRyYWNrIG9mIHRoZSBudW1iZXIgb2YgbGl2ZXMgbGVmdCBmb3IgYSB0cmFuc3BvcnQuXG4gKlxuICogSW4gdGhlIGJlZ2lubmluZyBvZiBhIHNlc3Npb24sIHRyYW5zcG9ydHMgbWF5IGJlIGFzc2lnbmVkIGEgbnVtYmVyIG9mXG4gKiBsaXZlcy4gV2hlbiBhbiBBc3Npc3RhbnRUb1RoZVRyYW5zcG9ydE1hbmFnZXIgaW5zdGFuY2UgcmVwb3J0cyBhIHRyYW5zcG9ydFxuICogY29ubmVjdGlvbiBjbG9zZWQgdW5jbGVhbmx5LCB0aGUgdHJhbnNwb3J0IGxvc2VzIGEgbGlmZS4gV2hlbiB0aGUgbnVtYmVyXG4gKiBvZiBsaXZlcyBkcm9wcyB0byB6ZXJvLCB0aGUgdHJhbnNwb3J0IGdldHMgZGlzYWJsZWQgYnkgaXRzIG1hbmFnZXIuXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVHJhbnNwb3J0TWFuYWdlciB7XG4gIG9wdGlvbnM6IFRyYW5zcG9ydE1hbmFnZXJPcHRpb25zO1xuICBsaXZlc0xlZnQ6IG51bWJlcjtcblxuICBjb25zdHJ1Y3RvcihvcHRpb25zOiBUcmFuc3BvcnRNYW5hZ2VyT3B0aW9ucykge1xuICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG4gICAgdGhpcy5saXZlc0xlZnQgPSB0aGlzLm9wdGlvbnMubGl2ZXMgfHwgSW5maW5pdHk7XG4gIH1cblxuICAvKiogQ3JlYXRlcyBhIGFzc2lzdGFudCBmb3IgdGhlIHRyYW5zcG9ydC5cbiAgICpcbiAgICogQHBhcmFtIHtUcmFuc3BvcnR9IHRyYW5zcG9ydFxuICAgKiBAcmV0dXJucyB7QXNzaXN0YW50VG9UaGVUcmFuc3BvcnRNYW5hZ2VyfVxuICAgKi9cbiAgZ2V0QXNzaXN0YW50KHRyYW5zcG9ydDogVHJhbnNwb3J0KTogQXNzaXN0YW50VG9UaGVUcmFuc3BvcnRNYW5hZ2VyIHtcbiAgICByZXR1cm4gRmFjdG9yeS5jcmVhdGVBc3Npc3RhbnRUb1RoZVRyYW5zcG9ydE1hbmFnZXIodGhpcywgdHJhbnNwb3J0LCB7XG4gICAgICBtaW5QaW5nRGVsYXk6IHRoaXMub3B0aW9ucy5taW5QaW5nRGVsYXksXG4gICAgICBtYXhQaW5nRGVsYXk6IHRoaXMub3B0aW9ucy5tYXhQaW5nRGVsYXlcbiAgICB9KTtcbiAgfVxuXG4gIC8qKiBSZXR1cm5zIHdoZXRoZXIgdGhlIHRyYW5zcG9ydCBoYXMgYW55IGxpdmVzIGxlZnQuXG4gICAqXG4gICAqIEByZXR1cm5zIHtCb29sZWFufVxuICAgKi9cbiAgaXNBbGl2ZSgpOiBib29sZWFuIHtcbiAgICByZXR1cm4gdGhpcy5saXZlc0xlZnQgPiAwO1xuICB9XG5cbiAgLyoqIFRha2VzIG9uZSBsaWZlIGZyb20gdGhlIHRyYW5zcG9ydC4gKi9cbiAgcmVwb3J0RGVhdGgoKSB7XG4gICAgdGhpcy5saXZlc0xlZnQgLT0gMTtcbiAgfVxufVxuIiwgImltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBVdGlsIGZyb20gJy4uL3V0aWwnO1xuaW1wb3J0IHsgT25lT2ZmVGltZXIgYXMgVGltZXIgfSBmcm9tICcuLi91dGlscy90aW1lcnMnO1xuaW1wb3J0IFN0cmF0ZWd5IGZyb20gJy4vc3RyYXRlZ3knO1xuaW1wb3J0IFN0cmF0ZWd5T3B0aW9ucyBmcm9tICcuL3N0cmF0ZWd5X29wdGlvbnMnO1xuXG4vKiogTG9vcHMgdGhyb3VnaCBzdHJhdGVnaWVzIHdpdGggb3B0aW9uYWwgdGltZW91dHMuXG4gKlxuICogT3B0aW9uczpcbiAqIC0gbG9vcCAtIHdoZXRoZXIgaXQgc2hvdWxkIGxvb3AgdGhyb3VnaCB0aGUgc3Vic3RyYXRlZ3kgbGlzdFxuICogLSB0aW1lb3V0IC0gaW5pdGlhbCB0aW1lb3V0IGZvciBhIHNpbmdsZSBzdWJzdHJhdGVneVxuICogLSB0aW1lb3V0TGltaXQgLSBtYXhpbXVtIHRpbWVvdXRcbiAqXG4gKiBAcGFyYW0ge1N0cmF0ZWd5W119IHN0cmF0ZWdpZXNcbiAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNlcXVlbnRpYWxTdHJhdGVneSBpbXBsZW1lbnRzIFN0cmF0ZWd5IHtcbiAgc3RyYXRlZ2llczogU3RyYXRlZ3lbXTtcbiAgbG9vcDogYm9vbGVhbjtcbiAgZmFpbEZhc3Q6IGJvb2xlYW47XG4gIHRpbWVvdXQ6IG51bWJlcjtcbiAgdGltZW91dExpbWl0OiBudW1iZXI7XG5cbiAgY29uc3RydWN0b3Ioc3RyYXRlZ2llczogU3RyYXRlZ3lbXSwgb3B0aW9uczogU3RyYXRlZ3lPcHRpb25zKSB7XG4gICAgdGhpcy5zdHJhdGVnaWVzID0gc3RyYXRlZ2llcztcbiAgICB0aGlzLmxvb3AgPSBCb29sZWFuKG9wdGlvbnMubG9vcCk7XG4gICAgdGhpcy5mYWlsRmFzdCA9IEJvb2xlYW4ob3B0aW9ucy5mYWlsRmFzdCk7XG4gICAgdGhpcy50aW1lb3V0ID0gb3B0aW9ucy50aW1lb3V0O1xuICAgIHRoaXMudGltZW91dExpbWl0ID0gb3B0aW9ucy50aW1lb3V0TGltaXQ7XG4gIH1cblxuICBpc1N1cHBvcnRlZCgpOiBib29sZWFuIHtcbiAgICByZXR1cm4gQ29sbGVjdGlvbnMuYW55KHRoaXMuc3RyYXRlZ2llcywgVXRpbC5tZXRob2QoJ2lzU3VwcG9ydGVkJykpO1xuICB9XG5cbiAgY29ubmVjdChtaW5Qcmlvcml0eTogbnVtYmVyLCBjYWxsYmFjazogRnVuY3Rpb24pIHtcbiAgICB2YXIgc3RyYXRlZ2llcyA9IHRoaXMuc3RyYXRlZ2llcztcbiAgICB2YXIgY3VycmVudCA9IDA7XG4gICAgdmFyIHRpbWVvdXQgPSB0aGlzLnRpbWVvdXQ7XG4gICAgdmFyIHJ1bm5lciA9IG51bGw7XG5cbiAgICB2YXIgdHJ5TmV4dFN0cmF0ZWd5ID0gKGVycm9yLCBoYW5kc2hha2UpID0+IHtcbiAgICAgIGlmIChoYW5kc2hha2UpIHtcbiAgICAgICAgY2FsbGJhY2sobnVsbCwgaGFuZHNoYWtlKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGN1cnJlbnQgPSBjdXJyZW50ICsgMTtcbiAgICAgICAgaWYgKHRoaXMubG9vcCkge1xuICAgICAgICAgIGN1cnJlbnQgPSBjdXJyZW50ICUgc3RyYXRlZ2llcy5sZW5ndGg7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoY3VycmVudCA8IHN0cmF0ZWdpZXMubGVuZ3RoKSB7XG4gICAgICAgICAgaWYgKHRpbWVvdXQpIHtcbiAgICAgICAgICAgIHRpbWVvdXQgPSB0aW1lb3V0ICogMjtcbiAgICAgICAgICAgIGlmICh0aGlzLnRpbWVvdXRMaW1pdCkge1xuICAgICAgICAgICAgICB0aW1lb3V0ID0gTWF0aC5taW4odGltZW91dCwgdGhpcy50aW1lb3V0TGltaXQpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgICBydW5uZXIgPSB0aGlzLnRyeVN0cmF0ZWd5KFxuICAgICAgICAgICAgc3RyYXRlZ2llc1tjdXJyZW50XSxcbiAgICAgICAgICAgIG1pblByaW9yaXR5LFxuICAgICAgICAgICAgeyB0aW1lb3V0LCBmYWlsRmFzdDogdGhpcy5mYWlsRmFzdCB9LFxuICAgICAgICAgICAgdHJ5TmV4dFN0cmF0ZWd5XG4gICAgICAgICAgKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBjYWxsYmFjayh0cnVlKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH07XG5cbiAgICBydW5uZXIgPSB0aGlzLnRyeVN0cmF0ZWd5KFxuICAgICAgc3RyYXRlZ2llc1tjdXJyZW50XSxcbiAgICAgIG1pblByaW9yaXR5LFxuICAgICAgeyB0aW1lb3V0OiB0aW1lb3V0LCBmYWlsRmFzdDogdGhpcy5mYWlsRmFzdCB9LFxuICAgICAgdHJ5TmV4dFN0cmF0ZWd5XG4gICAgKTtcblxuICAgIHJldHVybiB7XG4gICAgICBhYm9ydDogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJ1bm5lci5hYm9ydCgpO1xuICAgICAgfSxcbiAgICAgIGZvcmNlTWluUHJpb3JpdHk6IGZ1bmN0aW9uKHApIHtcbiAgICAgICAgbWluUHJpb3JpdHkgPSBwO1xuICAgICAgICBpZiAocnVubmVyKSB7XG4gICAgICAgICAgcnVubmVyLmZvcmNlTWluUHJpb3JpdHkocCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9O1xuICB9XG5cbiAgcHJpdmF0ZSB0cnlTdHJhdGVneShcbiAgICBzdHJhdGVneTogU3RyYXRlZ3ksXG4gICAgbWluUHJpb3JpdHk6IG51bWJlcixcbiAgICBvcHRpb25zOiBTdHJhdGVneU9wdGlvbnMsXG4gICAgY2FsbGJhY2s6IEZ1bmN0aW9uXG4gICkge1xuICAgIHZhciB0aW1lciA9IG51bGw7XG4gICAgdmFyIHJ1bm5lciA9IG51bGw7XG5cbiAgICBpZiAob3B0aW9ucy50aW1lb3V0ID4gMCkge1xuICAgICAgdGltZXIgPSBuZXcgVGltZXIob3B0aW9ucy50aW1lb3V0LCBmdW5jdGlvbigpIHtcbiAgICAgICAgcnVubmVyLmFib3J0KCk7XG4gICAgICAgIGNhbGxiYWNrKHRydWUpO1xuICAgICAgfSk7XG4gICAgfVxuXG4gICAgcnVubmVyID0gc3RyYXRlZ3kuY29ubmVjdChtaW5Qcmlvcml0eSwgZnVuY3Rpb24oZXJyb3IsIGhhbmRzaGFrZSkge1xuICAgICAgaWYgKGVycm9yICYmIHRpbWVyICYmIHRpbWVyLmlzUnVubmluZygpICYmICFvcHRpb25zLmZhaWxGYXN0KSB7XG4gICAgICAgIC8vIGFkdmFuY2UgdG8gdGhlIG5leHQgc3RyYXRlZ3kgYWZ0ZXIgdGhlIHRpbWVvdXRcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaWYgKHRpbWVyKSB7XG4gICAgICAgIHRpbWVyLmVuc3VyZUFib3J0ZWQoKTtcbiAgICAgIH1cbiAgICAgIGNhbGxiYWNrKGVycm9yLCBoYW5kc2hha2UpO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIGFib3J0OiBmdW5jdGlvbigpIHtcbiAgICAgICAgaWYgKHRpbWVyKSB7XG4gICAgICAgICAgdGltZXIuZW5zdXJlQWJvcnRlZCgpO1xuICAgICAgICB9XG4gICAgICAgIHJ1bm5lci5hYm9ydCgpO1xuICAgICAgfSxcbiAgICAgIGZvcmNlTWluUHJpb3JpdHk6IGZ1bmN0aW9uKHApIHtcbiAgICAgICAgcnVubmVyLmZvcmNlTWluUHJpb3JpdHkocCk7XG4gICAgICB9XG4gICAgfTtcbiAgfVxufVxuIiwgImltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBVdGlsIGZyb20gJy4uL3V0aWwnO1xuaW1wb3J0IFN0cmF0ZWd5IGZyb20gJy4vc3RyYXRlZ3knO1xuXG4vKiogTGF1bmNoZXMgYWxsIHN1YnN0cmF0ZWdpZXMgYW5kIGVtaXRzIHByaW9yaXRpemVkIGNvbm5lY3RlZCB0cmFuc3BvcnRzLlxuICpcbiAqIEBwYXJhbSB7QXJyYXl9IHN0cmF0ZWdpZXNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQmVzdENvbm5lY3RlZEV2ZXJTdHJhdGVneSBpbXBsZW1lbnRzIFN0cmF0ZWd5IHtcbiAgc3RyYXRlZ2llczogU3RyYXRlZ3lbXTtcblxuICBjb25zdHJ1Y3RvcihzdHJhdGVnaWVzOiBTdHJhdGVneVtdKSB7XG4gICAgdGhpcy5zdHJhdGVnaWVzID0gc3RyYXRlZ2llcztcbiAgfVxuXG4gIGlzU3VwcG9ydGVkKCk6IGJvb2xlYW4ge1xuICAgIHJldHVybiBDb2xsZWN0aW9ucy5hbnkodGhpcy5zdHJhdGVnaWVzLCBVdGlsLm1ldGhvZCgnaXNTdXBwb3J0ZWQnKSk7XG4gIH1cblxuICBjb25uZWN0KG1pblByaW9yaXR5OiBudW1iZXIsIGNhbGxiYWNrOiBGdW5jdGlvbikge1xuICAgIHJldHVybiBjb25uZWN0KHRoaXMuc3RyYXRlZ2llcywgbWluUHJpb3JpdHksIGZ1bmN0aW9uKGksIHJ1bm5lcnMpIHtcbiAgICAgIHJldHVybiBmdW5jdGlvbihlcnJvciwgaGFuZHNoYWtlKSB7XG4gICAgICAgIHJ1bm5lcnNbaV0uZXJyb3IgPSBlcnJvcjtcbiAgICAgICAgaWYgKGVycm9yKSB7XG4gICAgICAgICAgaWYgKGFsbFJ1bm5lcnNGYWlsZWQocnVubmVycykpIHtcbiAgICAgICAgICAgIGNhbGxiYWNrKHRydWUpO1xuICAgICAgICAgIH1cbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgQ29sbGVjdGlvbnMuYXBwbHkocnVubmVycywgZnVuY3Rpb24ocnVubmVyKSB7XG4gICAgICAgICAgcnVubmVyLmZvcmNlTWluUHJpb3JpdHkoaGFuZHNoYWtlLnRyYW5zcG9ydC5wcmlvcml0eSk7XG4gICAgICAgIH0pO1xuICAgICAgICBjYWxsYmFjayhudWxsLCBoYW5kc2hha2UpO1xuICAgICAgfTtcbiAgICB9KTtcbiAgfVxufVxuXG4vKiogQ29ubmVjdHMgdG8gYWxsIHN0cmF0ZWdpZXMgaW4gcGFyYWxsZWwuXG4gKlxuICogQ2FsbGJhY2sgYnVpbGRlciBzaG91bGQgYmUgYSBmdW5jdGlvbiB0aGF0IHRha2VzIHR3byBhcmd1bWVudHM6IGluZGV4XG4gKiBhbmQgYSBsaXN0IG9mIHJ1bm5lcnMuIEl0IHNob3VsZCByZXR1cm4gYW5vdGhlciBmdW5jdGlvbiB0aGF0IHdpbGwgYmVcbiAqIHBhc3NlZCB0byB0aGUgc3Vic3RyYXRlZ3kgd2l0aCBnaXZlbiBpbmRleC4gUnVubmVycyBjYW4gYmUgYWJvcnRlZCB1c2luZ1xuICogYWJvcnRSdW5uZXIocykgZnVuY3Rpb25zIGZyb20gdGhpcyBjbGFzcy5cbiAqXG4gKiBAcGFyYW0gIHtBcnJheX0gc3RyYXRlZ2llc1xuICogQHBhcmFtICB7RnVuY3Rpb259IGNhbGxiYWNrQnVpbGRlclxuICogQHJldHVybiB7T2JqZWN0fSBzdHJhdGVneSBydW5uZXJcbiAqL1xuZnVuY3Rpb24gY29ubmVjdChcbiAgc3RyYXRlZ2llczogU3RyYXRlZ3lbXSxcbiAgbWluUHJpb3JpdHk6IG51bWJlcixcbiAgY2FsbGJhY2tCdWlsZGVyOiBGdW5jdGlvblxuKSB7XG4gIHZhciBydW5uZXJzID0gQ29sbGVjdGlvbnMubWFwKHN0cmF0ZWdpZXMsIGZ1bmN0aW9uKHN0cmF0ZWd5LCBpLCBfLCBycykge1xuICAgIHJldHVybiBzdHJhdGVneS5jb25uZWN0KG1pblByaW9yaXR5LCBjYWxsYmFja0J1aWxkZXIoaSwgcnMpKTtcbiAgfSk7XG4gIHJldHVybiB7XG4gICAgYWJvcnQ6IGZ1bmN0aW9uKCkge1xuICAgICAgQ29sbGVjdGlvbnMuYXBwbHkocnVubmVycywgYWJvcnRSdW5uZXIpO1xuICAgIH0sXG4gICAgZm9yY2VNaW5Qcmlvcml0eTogZnVuY3Rpb24ocCkge1xuICAgICAgQ29sbGVjdGlvbnMuYXBwbHkocnVubmVycywgZnVuY3Rpb24ocnVubmVyKSB7XG4gICAgICAgIHJ1bm5lci5mb3JjZU1pblByaW9yaXR5KHApO1xuICAgICAgfSk7XG4gICAgfVxuICB9O1xufVxuXG5mdW5jdGlvbiBhbGxSdW5uZXJzRmFpbGVkKHJ1bm5lcnMpOiBib29sZWFuIHtcbiAgcmV0dXJuIENvbGxlY3Rpb25zLmFsbChydW5uZXJzLCBmdW5jdGlvbihydW5uZXIpIHtcbiAgICByZXR1cm4gQm9vbGVhbihydW5uZXIuZXJyb3IpO1xuICB9KTtcbn1cblxuZnVuY3Rpb24gYWJvcnRSdW5uZXIocnVubmVyKSB7XG4gIGlmICghcnVubmVyLmVycm9yICYmICFydW5uZXIuYWJvcnRlZCkge1xuICAgIHJ1bm5lci5hYm9ydCgpO1xuICAgIHJ1bm5lci5hYm9ydGVkID0gdHJ1ZTtcbiAgfVxufVxuIiwgImltcG9ydCBVdGlsIGZyb20gJy4uL3V0aWwnO1xuaW1wb3J0IFJ1bnRpbWUgZnJvbSAncnVudGltZSc7XG5pbXBvcnQgU3RyYXRlZ3kgZnJvbSAnLi9zdHJhdGVneSc7XG5pbXBvcnQgU2VxdWVudGlhbFN0cmF0ZWd5IGZyb20gJy4vc2VxdWVudGlhbF9zdHJhdGVneSc7XG5pbXBvcnQgU3RyYXRlZ3lPcHRpb25zIGZyb20gJy4vc3RyYXRlZ3lfb3B0aW9ucyc7XG5pbXBvcnQgVHJhbnNwb3J0U3RyYXRlZ3kgZnJvbSAnLi90cmFuc3BvcnRfc3RyYXRlZ3knO1xuaW1wb3J0IFRpbWVsaW5lIGZyb20gJy4uL3RpbWVsaW5lL3RpbWVsaW5lJztcbmltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcblxuZXhwb3J0IGludGVyZmFjZSBUcmFuc3BvcnRTdHJhdGVneURpY3Rpb25hcnkge1xuICBba2V5OiBzdHJpbmddOiBUcmFuc3BvcnRTdHJhdGVneTtcbn1cblxuLyoqIENhY2hlcyBsYXN0IHN1Y2Nlc3NmdWwgdHJhbnNwb3J0IGFuZCB1c2VzIGl0IGZvciBmb2xsb3dpbmcgYXR0ZW1wdHMuXG4gKlxuICogQHBhcmFtIHtTdHJhdGVneX0gc3RyYXRlZ3lcbiAqIEBwYXJhbSB7T2JqZWN0fSB0cmFuc3BvcnRzXG4gKiBAcGFyYW0ge09iamVjdH0gb3B0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDYWNoZWRTdHJhdGVneSBpbXBsZW1lbnRzIFN0cmF0ZWd5IHtcbiAgc3RyYXRlZ3k6IFN0cmF0ZWd5O1xuICB0cmFuc3BvcnRzOiBUcmFuc3BvcnRTdHJhdGVneURpY3Rpb25hcnk7XG4gIHR0bDogbnVtYmVyO1xuICB1c2luZ1RMUzogYm9vbGVhbjtcbiAgdGltZWxpbmU6IFRpbWVsaW5lO1xuXG4gIGNvbnN0cnVjdG9yKFxuICAgIHN0cmF0ZWd5OiBTdHJhdGVneSxcbiAgICB0cmFuc3BvcnRzOiBUcmFuc3BvcnRTdHJhdGVneURpY3Rpb25hcnksXG4gICAgb3B0aW9uczogU3RyYXRlZ3lPcHRpb25zXG4gICkge1xuICAgIHRoaXMuc3RyYXRlZ3kgPSBzdHJhdGVneTtcbiAgICB0aGlzLnRyYW5zcG9ydHMgPSB0cmFuc3BvcnRzO1xuICAgIHRoaXMudHRsID0gb3B0aW9ucy50dGwgfHwgMTgwMCAqIDEwMDA7XG4gICAgdGhpcy51c2luZ1RMUyA9IG9wdGlvbnMudXNlVExTO1xuICAgIHRoaXMudGltZWxpbmUgPSBvcHRpb25zLnRpbWVsaW5lO1xuICB9XG5cbiAgaXNTdXBwb3J0ZWQoKTogYm9vbGVhbiB7XG4gICAgcmV0dXJuIHRoaXMuc3RyYXRlZ3kuaXNTdXBwb3J0ZWQoKTtcbiAgfVxuXG4gIGNvbm5lY3QobWluUHJpb3JpdHk6IG51bWJlciwgY2FsbGJhY2s6IEZ1bmN0aW9uKSB7XG4gICAgdmFyIHVzaW5nVExTID0gdGhpcy51c2luZ1RMUztcbiAgICB2YXIgaW5mbyA9IGZldGNoVHJhbnNwb3J0Q2FjaGUodXNpbmdUTFMpO1xuXG4gICAgdmFyIHN0cmF0ZWdpZXMgPSBbdGhpcy5zdHJhdGVneV07XG4gICAgaWYgKGluZm8gJiYgaW5mby50aW1lc3RhbXAgKyB0aGlzLnR0bCA+PSBVdGlsLm5vdygpKSB7XG4gICAgICB2YXIgdHJhbnNwb3J0ID0gdGhpcy50cmFuc3BvcnRzW2luZm8udHJhbnNwb3J0XTtcbiAgICAgIGlmICh0cmFuc3BvcnQpIHtcbiAgICAgICAgdGhpcy50aW1lbGluZS5pbmZvKHtcbiAgICAgICAgICBjYWNoZWQ6IHRydWUsXG4gICAgICAgICAgdHJhbnNwb3J0OiBpbmZvLnRyYW5zcG9ydCxcbiAgICAgICAgICBsYXRlbmN5OiBpbmZvLmxhdGVuY3lcbiAgICAgICAgfSk7XG4gICAgICAgIHN0cmF0ZWdpZXMucHVzaChcbiAgICAgICAgICBuZXcgU2VxdWVudGlhbFN0cmF0ZWd5KFt0cmFuc3BvcnRdLCB7XG4gICAgICAgICAgICB0aW1lb3V0OiBpbmZvLmxhdGVuY3kgKiAyICsgMTAwMCxcbiAgICAgICAgICAgIGZhaWxGYXN0OiB0cnVlXG4gICAgICAgICAgfSlcbiAgICAgICAgKTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICB2YXIgc3RhcnRUaW1lc3RhbXAgPSBVdGlsLm5vdygpO1xuICAgIHZhciBydW5uZXIgPSBzdHJhdGVnaWVzXG4gICAgICAucG9wKClcbiAgICAgIC5jb25uZWN0KG1pblByaW9yaXR5LCBmdW5jdGlvbiBjYihlcnJvciwgaGFuZHNoYWtlKSB7XG4gICAgICAgIGlmIChlcnJvcikge1xuICAgICAgICAgIGZsdXNoVHJhbnNwb3J0Q2FjaGUodXNpbmdUTFMpO1xuICAgICAgICAgIGlmIChzdHJhdGVnaWVzLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgIHN0YXJ0VGltZXN0YW1wID0gVXRpbC5ub3coKTtcbiAgICAgICAgICAgIHJ1bm5lciA9IHN0cmF0ZWdpZXMucG9wKCkuY29ubmVjdChtaW5Qcmlvcml0eSwgY2IpO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBjYWxsYmFjayhlcnJvcik7XG4gICAgICAgICAgfVxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHN0b3JlVHJhbnNwb3J0Q2FjaGUoXG4gICAgICAgICAgICB1c2luZ1RMUyxcbiAgICAgICAgICAgIGhhbmRzaGFrZS50cmFuc3BvcnQubmFtZSxcbiAgICAgICAgICAgIFV0aWwubm93KCkgLSBzdGFydFRpbWVzdGFtcFxuICAgICAgICAgICk7XG4gICAgICAgICAgY2FsbGJhY2sobnVsbCwgaGFuZHNoYWtlKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgYWJvcnQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICBydW5uZXIuYWJvcnQoKTtcbiAgICAgIH0sXG4gICAgICBmb3JjZU1pblByaW9yaXR5OiBmdW5jdGlvbihwKSB7XG4gICAgICAgIG1pblByaW9yaXR5ID0gcDtcbiAgICAgICAgaWYgKHJ1bm5lcikge1xuICAgICAgICAgIHJ1bm5lci5mb3JjZU1pblByaW9yaXR5KHApO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfTtcbiAgfVxufVxuXG5mdW5jdGlvbiBnZXRUcmFuc3BvcnRDYWNoZUtleSh1c2luZ1RMUzogYm9vbGVhbik6IHN0cmluZyB7XG4gIHJldHVybiAncHVzaGVyVHJhbnNwb3J0JyArICh1c2luZ1RMUyA/ICdUTFMnIDogJ05vblRMUycpO1xufVxuXG5mdW5jdGlvbiBmZXRjaFRyYW5zcG9ydENhY2hlKHVzaW5nVExTOiBib29sZWFuKTogYW55IHtcbiAgdmFyIHN0b3JhZ2UgPSBSdW50aW1lLmdldExvY2FsU3RvcmFnZSgpO1xuICBpZiAoc3RvcmFnZSkge1xuICAgIHRyeSB7XG4gICAgICB2YXIgc2VyaWFsaXplZENhY2hlID0gc3RvcmFnZVtnZXRUcmFuc3BvcnRDYWNoZUtleSh1c2luZ1RMUyldO1xuICAgICAgaWYgKHNlcmlhbGl6ZWRDYWNoZSkge1xuICAgICAgICByZXR1cm4gSlNPTi5wYXJzZShzZXJpYWxpemVkQ2FjaGUpO1xuICAgICAgfVxuICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgIGZsdXNoVHJhbnNwb3J0Q2FjaGUodXNpbmdUTFMpO1xuICAgIH1cbiAgfVxuICByZXR1cm4gbnVsbDtcbn1cblxuZnVuY3Rpb24gc3RvcmVUcmFuc3BvcnRDYWNoZShcbiAgdXNpbmdUTFM6IGJvb2xlYW4sXG4gIHRyYW5zcG9ydDogVHJhbnNwb3J0U3RyYXRlZ3ksXG4gIGxhdGVuY3k6IG51bWJlclxuKSB7XG4gIHZhciBzdG9yYWdlID0gUnVudGltZS5nZXRMb2NhbFN0b3JhZ2UoKTtcbiAgaWYgKHN0b3JhZ2UpIHtcbiAgICB0cnkge1xuICAgICAgc3RvcmFnZVtnZXRUcmFuc3BvcnRDYWNoZUtleSh1c2luZ1RMUyldID0gQ29sbGVjdGlvbnMuc2FmZUpTT05TdHJpbmdpZnkoe1xuICAgICAgICB0aW1lc3RhbXA6IFV0aWwubm93KCksXG4gICAgICAgIHRyYW5zcG9ydDogdHJhbnNwb3J0LFxuICAgICAgICBsYXRlbmN5OiBsYXRlbmN5XG4gICAgICB9KTtcbiAgICB9IGNhdGNoIChlKSB7XG4gICAgICAvLyBjYXRjaCBvdmVyIHF1b3RhIGV4Y2VwdGlvbnMgcmFpc2VkIGJ5IGxvY2FsU3RvcmFnZVxuICAgIH1cbiAgfVxufVxuXG5mdW5jdGlvbiBmbHVzaFRyYW5zcG9ydENhY2hlKHVzaW5nVExTOiBib29sZWFuKSB7XG4gIHZhciBzdG9yYWdlID0gUnVudGltZS5nZXRMb2NhbFN0b3JhZ2UoKTtcbiAgaWYgKHN0b3JhZ2UpIHtcbiAgICB0cnkge1xuICAgICAgZGVsZXRlIHN0b3JhZ2VbZ2V0VHJhbnNwb3J0Q2FjaGVLZXkodXNpbmdUTFMpXTtcbiAgICB9IGNhdGNoIChlKSB7XG4gICAgICAvLyBjYXRjaCBleGNlcHRpb25zIHJhaXNlZCBieSBsb2NhbFN0b3JhZ2VcbiAgICB9XG4gIH1cbn1cbiIsICJpbXBvcnQgeyBPbmVPZmZUaW1lciBhcyBUaW1lciB9IGZyb20gJy4uL3V0aWxzL3RpbWVycyc7XG5pbXBvcnQgU3RyYXRlZ3kgZnJvbSAnLi9zdHJhdGVneSc7XG5pbXBvcnQgU3RyYXRlZ3lPcHRpb25zIGZyb20gJy4vc3RyYXRlZ3lfb3B0aW9ucyc7XG5cbi8qKiBSdW5zIHN1YnN0cmF0ZWd5IGFmdGVyIHNwZWNpZmllZCBkZWxheS5cbiAqXG4gKiBPcHRpb25zOlxuICogLSBkZWxheSAtIHRpbWUgaW4gbWlsaXNlY29uZHMgdG8gZGVsYXkgdGhlIHN1YnN0cmF0ZWd5IGF0dGVtcHRcbiAqXG4gKiBAcGFyYW0ge1N0cmF0ZWd5fSBzdHJhdGVneVxuICogQHBhcmFtIHtPYmplY3R9IG9wdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRGVsYXllZFN0cmF0ZWd5IGltcGxlbWVudHMgU3RyYXRlZ3kge1xuICBzdHJhdGVneTogU3RyYXRlZ3k7XG4gIG9wdGlvbnM6IHsgZGVsYXk6IG51bWJlciB9O1xuXG4gIGNvbnN0cnVjdG9yKHN0cmF0ZWd5OiBTdHJhdGVneSwgeyBkZWxheTogbnVtYmVyIH0pIHtcbiAgICB0aGlzLnN0cmF0ZWd5ID0gc3RyYXRlZ3k7XG4gICAgdGhpcy5vcHRpb25zID0geyBkZWxheTogbnVtYmVyIH07XG4gIH1cblxuICBpc1N1cHBvcnRlZCgpOiBib29sZWFuIHtcbiAgICByZXR1cm4gdGhpcy5zdHJhdGVneS5pc1N1cHBvcnRlZCgpO1xuICB9XG5cbiAgY29ubmVjdChtaW5Qcmlvcml0eTogbnVtYmVyLCBjYWxsYmFjazogRnVuY3Rpb24pIHtcbiAgICB2YXIgc3RyYXRlZ3kgPSB0aGlzLnN0cmF0ZWd5O1xuICAgIHZhciBydW5uZXI7XG4gICAgdmFyIHRpbWVyID0gbmV3IFRpbWVyKHRoaXMub3B0aW9ucy5kZWxheSwgZnVuY3Rpb24oKSB7XG4gICAgICBydW5uZXIgPSBzdHJhdGVneS5jb25uZWN0KG1pblByaW9yaXR5LCBjYWxsYmFjayk7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgYWJvcnQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICB0aW1lci5lbnN1cmVBYm9ydGVkKCk7XG4gICAgICAgIGlmIChydW5uZXIpIHtcbiAgICAgICAgICBydW5uZXIuYWJvcnQoKTtcbiAgICAgICAgfVxuICAgICAgfSxcbiAgICAgIGZvcmNlTWluUHJpb3JpdHk6IGZ1bmN0aW9uKHApIHtcbiAgICAgICAgbWluUHJpb3JpdHkgPSBwO1xuICAgICAgICBpZiAocnVubmVyKSB7XG4gICAgICAgICAgcnVubmVyLmZvcmNlTWluUHJpb3JpdHkocCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9O1xuICB9XG59XG4iLCAiaW1wb3J0IFN0cmF0ZWd5IGZyb20gJy4vc3RyYXRlZ3knO1xuaW1wb3J0IFN0cmF0ZWd5UnVubmVyIGZyb20gJy4vc3RyYXRlZ3lfcnVubmVyJztcblxuLyoqIFByb3hpZXMgbWV0aG9kIGNhbGxzIHRvIG9uZSBvZiBzdWJzdHJhdGVnaWVzIGJhc2luZyBvbiB0aGUgdGVzdCBmdW5jdGlvbi5cbiAqXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSB0ZXN0XG4gKiBAcGFyYW0ge1N0cmF0ZWd5fSB0cnVlQnJhbmNoIHN0cmF0ZWd5IHVzZWQgd2hlbiB0ZXN0IHJldHVybnMgdHJ1ZVxuICogQHBhcmFtIHtTdHJhdGVneX0gZmFsc2VCcmFuY2ggc3RyYXRlZ3kgdXNlZCB3aGVuIHRlc3QgcmV0dXJucyBmYWxzZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBJZlN0cmF0ZWd5IGltcGxlbWVudHMgU3RyYXRlZ3kge1xuICB0ZXN0OiAoKSA9PiBib29sZWFuO1xuICB0cnVlQnJhbmNoOiBTdHJhdGVneTtcbiAgZmFsc2VCcmFuY2g6IFN0cmF0ZWd5O1xuXG4gIGNvbnN0cnVjdG9yKFxuICAgIHRlc3Q6ICgpID0+IGJvb2xlYW4sXG4gICAgdHJ1ZUJyYW5jaDogU3RyYXRlZ3ksXG4gICAgZmFsc2VCcmFuY2g6IFN0cmF0ZWd5XG4gICkge1xuICAgIHRoaXMudGVzdCA9IHRlc3Q7XG4gICAgdGhpcy50cnVlQnJhbmNoID0gdHJ1ZUJyYW5jaDtcbiAgICB0aGlzLmZhbHNlQnJhbmNoID0gZmFsc2VCcmFuY2g7XG4gIH1cblxuICBpc1N1cHBvcnRlZCgpOiBib29sZWFuIHtcbiAgICB2YXIgYnJhbmNoID0gdGhpcy50ZXN0KCkgPyB0aGlzLnRydWVCcmFuY2ggOiB0aGlzLmZhbHNlQnJhbmNoO1xuICAgIHJldHVybiBicmFuY2guaXNTdXBwb3J0ZWQoKTtcbiAgfVxuXG4gIGNvbm5lY3QobWluUHJpb3JpdHk6IG51bWJlciwgY2FsbGJhY2s6IEZ1bmN0aW9uKTogU3RyYXRlZ3lSdW5uZXIge1xuICAgIHZhciBicmFuY2ggPSB0aGlzLnRlc3QoKSA/IHRoaXMudHJ1ZUJyYW5jaCA6IHRoaXMuZmFsc2VCcmFuY2g7XG4gICAgcmV0dXJuIGJyYW5jaC5jb25uZWN0KG1pblByaW9yaXR5LCBjYWxsYmFjayk7XG4gIH1cbn1cbiIsICJpbXBvcnQgU3RyYXRlZ3kgZnJvbSAnLi9zdHJhdGVneSc7XG5pbXBvcnQgU3RyYXRlZ3lSdW5uZXIgZnJvbSAnLi9zdHJhdGVneV9ydW5uZXInO1xuXG4vKiogTGF1bmNoZXMgdGhlIHN1YnN0cmF0ZWd5IGFuZCB0ZXJtaW5hdGVzIG9uIHRoZSBmaXJzdCBvcGVuIGNvbm5lY3Rpb24uXG4gKlxuICogQHBhcmFtIHtTdHJhdGVneX0gc3RyYXRlZ3lcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlyc3RDb25uZWN0ZWRTdHJhdGVneSBpbXBsZW1lbnRzIFN0cmF0ZWd5IHtcbiAgc3RyYXRlZ3k6IFN0cmF0ZWd5O1xuXG4gIGNvbnN0cnVjdG9yKHN0cmF0ZWd5OiBTdHJhdGVneSkge1xuICAgIHRoaXMuc3RyYXRlZ3kgPSBzdHJhdGVneTtcbiAgfVxuXG4gIGlzU3VwcG9ydGVkKCk6IGJvb2xlYW4ge1xuICAgIHJldHVybiB0aGlzLnN0cmF0ZWd5LmlzU3VwcG9ydGVkKCk7XG4gIH1cblxuICBjb25uZWN0KG1pblByaW9yaXR5OiBudW1iZXIsIGNhbGxiYWNrOiBGdW5jdGlvbik6IFN0cmF0ZWd5UnVubmVyIHtcbiAgICB2YXIgcnVubmVyID0gdGhpcy5zdHJhdGVneS5jb25uZWN0KG1pblByaW9yaXR5LCBmdW5jdGlvbihlcnJvciwgaGFuZHNoYWtlKSB7XG4gICAgICBpZiAoaGFuZHNoYWtlKSB7XG4gICAgICAgIHJ1bm5lci5hYm9ydCgpO1xuICAgICAgfVxuICAgICAgY2FsbGJhY2soZXJyb3IsIGhhbmRzaGFrZSk7XG4gICAgfSk7XG4gICAgcmV0dXJuIHJ1bm5lcjtcbiAgfVxufVxuIiwgImltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJ2NvcmUvdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IFRyYW5zcG9ydE1hbmFnZXIgZnJvbSAnY29yZS90cmFuc3BvcnRzL3RyYW5zcG9ydF9tYW5hZ2VyJztcbmltcG9ydCBTdHJhdGVneSBmcm9tICdjb3JlL3N0cmF0ZWdpZXMvc3RyYXRlZ3knO1xuaW1wb3J0IFN0cmF0ZWd5T3B0aW9ucyBmcm9tICdjb3JlL3N0cmF0ZWdpZXMvc3RyYXRlZ3lfb3B0aW9ucyc7XG5pbXBvcnQgU2VxdWVudGlhbFN0cmF0ZWd5IGZyb20gJ2NvcmUvc3RyYXRlZ2llcy9zZXF1ZW50aWFsX3N0cmF0ZWd5JztcbmltcG9ydCBCZXN0Q29ubmVjdGVkRXZlclN0cmF0ZWd5IGZyb20gJ2NvcmUvc3RyYXRlZ2llcy9iZXN0X2Nvbm5lY3RlZF9ldmVyX3N0cmF0ZWd5JztcbmltcG9ydCBDYWNoZWRTdHJhdGVneSwge1xuICBUcmFuc3BvcnRTdHJhdGVneURpY3Rpb25hcnlcbn0gZnJvbSAnY29yZS9zdHJhdGVnaWVzL2NhY2hlZF9zdHJhdGVneSc7XG5pbXBvcnQgRGVsYXllZFN0cmF0ZWd5IGZyb20gJ2NvcmUvc3RyYXRlZ2llcy9kZWxheWVkX3N0cmF0ZWd5JztcbmltcG9ydCBJZlN0cmF0ZWd5IGZyb20gJ2NvcmUvc3RyYXRlZ2llcy9pZl9zdHJhdGVneSc7XG5pbXBvcnQgRmlyc3RDb25uZWN0ZWRTdHJhdGVneSBmcm9tICdjb3JlL3N0cmF0ZWdpZXMvZmlyc3RfY29ubmVjdGVkX3N0cmF0ZWd5JztcbmltcG9ydCB7IENvbmZpZyB9IGZyb20gJ2NvcmUvY29uZmlnJztcblxuZnVuY3Rpb24gdGVzdFN1cHBvcnRzU3RyYXRlZ3koc3RyYXRlZ3k6IFN0cmF0ZWd5KSB7XG4gIHJldHVybiBmdW5jdGlvbigpIHtcbiAgICByZXR1cm4gc3RyYXRlZ3kuaXNTdXBwb3J0ZWQoKTtcbiAgfTtcbn1cblxudmFyIGdldERlZmF1bHRTdHJhdGVneSA9IGZ1bmN0aW9uKFxuICBjb25maWc6IENvbmZpZyxcbiAgYmFzZU9wdGlvbnM6IFN0cmF0ZWd5T3B0aW9ucyxcbiAgZGVmaW5lVHJhbnNwb3J0OiBGdW5jdGlvblxuKTogU3RyYXRlZ3kge1xuICB2YXIgZGVmaW5lZFRyYW5zcG9ydHMgPSA8VHJhbnNwb3J0U3RyYXRlZ3lEaWN0aW9uYXJ5Pnt9O1xuXG4gIGZ1bmN0aW9uIGRlZmluZVRyYW5zcG9ydFN0cmF0ZWd5KFxuICAgIG5hbWU6IHN0cmluZyxcbiAgICB0eXBlOiBzdHJpbmcsXG4gICAgcHJpb3JpdHk6IG51bWJlcixcbiAgICBvcHRpb25zOiBTdHJhdGVneU9wdGlvbnMsXG4gICAgbWFuYWdlcj86IFRyYW5zcG9ydE1hbmFnZXJcbiAgKSB7XG4gICAgdmFyIHRyYW5zcG9ydCA9IGRlZmluZVRyYW5zcG9ydChcbiAgICAgIGNvbmZpZyxcbiAgICAgIG5hbWUsXG4gICAgICB0eXBlLFxuICAgICAgcHJpb3JpdHksXG4gICAgICBvcHRpb25zLFxuICAgICAgbWFuYWdlclxuICAgICk7XG5cbiAgICBkZWZpbmVkVHJhbnNwb3J0c1tuYW1lXSA9IHRyYW5zcG9ydDtcblxuICAgIHJldHVybiB0cmFuc3BvcnQ7XG4gIH1cblxuICB2YXIgd3Nfb3B0aW9uczogU3RyYXRlZ3lPcHRpb25zID0gT2JqZWN0LmFzc2lnbih7fSwgYmFzZU9wdGlvbnMsIHtcbiAgICBob3N0Tm9uVExTOiBjb25maWcud3NIb3N0ICsgJzonICsgY29uZmlnLndzUG9ydCxcbiAgICBob3N0VExTOiBjb25maWcud3NIb3N0ICsgJzonICsgY29uZmlnLndzc1BvcnQsXG4gICAgaHR0cFBhdGg6IGNvbmZpZy53c1BhdGhcbiAgfSk7XG4gIHZhciB3c3Nfb3B0aW9uczogU3RyYXRlZ3lPcHRpb25zID0gT2JqZWN0LmFzc2lnbih7fSwgd3Nfb3B0aW9ucywge1xuICAgIHVzZVRMUzogdHJ1ZVxuICB9KTtcbiAgdmFyIHNvY2tqc19vcHRpb25zOiBTdHJhdGVneU9wdGlvbnMgPSBPYmplY3QuYXNzaWduKHt9LCBiYXNlT3B0aW9ucywge1xuICAgIGhvc3ROb25UTFM6IGNvbmZpZy5odHRwSG9zdCArICc6JyArIGNvbmZpZy5odHRwUG9ydCxcbiAgICBob3N0VExTOiBjb25maWcuaHR0cEhvc3QgKyAnOicgKyBjb25maWcuaHR0cHNQb3J0LFxuICAgIGh0dHBQYXRoOiBjb25maWcuaHR0cFBhdGhcbiAgfSk7XG5cbiAgdmFyIHRpbWVvdXRzID0ge1xuICAgIGxvb3A6IHRydWUsXG4gICAgdGltZW91dDogMTUwMDAsXG4gICAgdGltZW91dExpbWl0OiA2MDAwMFxuICB9O1xuXG4gIHZhciB3c19tYW5hZ2VyID0gbmV3IFRyYW5zcG9ydE1hbmFnZXIoe1xuICAgIGxpdmVzOiAyLFxuICAgIG1pblBpbmdEZWxheTogMTAwMDAsXG4gICAgbWF4UGluZ0RlbGF5OiBjb25maWcuYWN0aXZpdHlUaW1lb3V0XG4gIH0pO1xuICB2YXIgc3RyZWFtaW5nX21hbmFnZXIgPSBuZXcgVHJhbnNwb3J0TWFuYWdlcih7XG4gICAgbGl2ZXM6IDIsXG4gICAgbWluUGluZ0RlbGF5OiAxMDAwMCxcbiAgICBtYXhQaW5nRGVsYXk6IGNvbmZpZy5hY3Rpdml0eVRpbWVvdXRcbiAgfSk7XG5cbiAgdmFyIHdzX3RyYW5zcG9ydCA9IGRlZmluZVRyYW5zcG9ydFN0cmF0ZWd5KFxuICAgICd3cycsXG4gICAgJ3dzJyxcbiAgICAzLFxuICAgIHdzX29wdGlvbnMsXG4gICAgd3NfbWFuYWdlclxuICApO1xuICB2YXIgd3NzX3RyYW5zcG9ydCA9IGRlZmluZVRyYW5zcG9ydFN0cmF0ZWd5KFxuICAgICd3c3MnLFxuICAgICd3cycsXG4gICAgMyxcbiAgICB3c3Nfb3B0aW9ucyxcbiAgICB3c19tYW5hZ2VyXG4gICk7XG4gIHZhciBzb2NranNfdHJhbnNwb3J0ID0gZGVmaW5lVHJhbnNwb3J0U3RyYXRlZ3koXG4gICAgJ3NvY2tqcycsXG4gICAgJ3NvY2tqcycsXG4gICAgMSxcbiAgICBzb2NranNfb3B0aW9uc1xuICApO1xuICB2YXIgeGhyX3N0cmVhbWluZ190cmFuc3BvcnQgPSBkZWZpbmVUcmFuc3BvcnRTdHJhdGVneShcbiAgICAneGhyX3N0cmVhbWluZycsXG4gICAgJ3hocl9zdHJlYW1pbmcnLFxuICAgIDEsXG4gICAgc29ja2pzX29wdGlvbnMsXG4gICAgc3RyZWFtaW5nX21hbmFnZXJcbiAgKTtcbiAgdmFyIHhkcl9zdHJlYW1pbmdfdHJhbnNwb3J0ID0gZGVmaW5lVHJhbnNwb3J0U3RyYXRlZ3koXG4gICAgJ3hkcl9zdHJlYW1pbmcnLFxuICAgICd4ZHJfc3RyZWFtaW5nJyxcbiAgICAxLFxuICAgIHNvY2tqc19vcHRpb25zLFxuICAgIHN0cmVhbWluZ19tYW5hZ2VyXG4gICk7XG4gIHZhciB4aHJfcG9sbGluZ190cmFuc3BvcnQgPSBkZWZpbmVUcmFuc3BvcnRTdHJhdGVneShcbiAgICAneGhyX3BvbGxpbmcnLFxuICAgICd4aHJfcG9sbGluZycsXG4gICAgMSxcbiAgICBzb2NranNfb3B0aW9uc1xuICApO1xuICB2YXIgeGRyX3BvbGxpbmdfdHJhbnNwb3J0ID0gZGVmaW5lVHJhbnNwb3J0U3RyYXRlZ3koXG4gICAgJ3hkcl9wb2xsaW5nJyxcbiAgICAneGRyX3BvbGxpbmcnLFxuICAgIDEsXG4gICAgc29ja2pzX29wdGlvbnNcbiAgKTtcblxuICB2YXIgd3NfbG9vcCA9IG5ldyBTZXF1ZW50aWFsU3RyYXRlZ3koW3dzX3RyYW5zcG9ydF0sIHRpbWVvdXRzKTtcbiAgdmFyIHdzc19sb29wID0gbmV3IFNlcXVlbnRpYWxTdHJhdGVneShbd3NzX3RyYW5zcG9ydF0sIHRpbWVvdXRzKTtcbiAgdmFyIHNvY2tqc19sb29wID0gbmV3IFNlcXVlbnRpYWxTdHJhdGVneShbc29ja2pzX3RyYW5zcG9ydF0sIHRpbWVvdXRzKTtcbiAgdmFyIHN0cmVhbWluZ19sb29wID0gbmV3IFNlcXVlbnRpYWxTdHJhdGVneShcbiAgICBbXG4gICAgICBuZXcgSWZTdHJhdGVneShcbiAgICAgICAgdGVzdFN1cHBvcnRzU3RyYXRlZ3koeGhyX3N0cmVhbWluZ190cmFuc3BvcnQpLFxuICAgICAgICB4aHJfc3RyZWFtaW5nX3RyYW5zcG9ydCxcbiAgICAgICAgeGRyX3N0cmVhbWluZ190cmFuc3BvcnRcbiAgICAgIClcbiAgICBdLFxuICAgIHRpbWVvdXRzXG4gICk7XG4gIHZhciBwb2xsaW5nX2xvb3AgPSBuZXcgU2VxdWVudGlhbFN0cmF0ZWd5KFxuICAgIFtcbiAgICAgIG5ldyBJZlN0cmF0ZWd5KFxuICAgICAgICB0ZXN0U3VwcG9ydHNTdHJhdGVneSh4aHJfcG9sbGluZ190cmFuc3BvcnQpLFxuICAgICAgICB4aHJfcG9sbGluZ190cmFuc3BvcnQsXG4gICAgICAgIHhkcl9wb2xsaW5nX3RyYW5zcG9ydFxuICAgICAgKVxuICAgIF0sXG4gICAgdGltZW91dHNcbiAgKTtcblxuICB2YXIgaHR0cF9sb29wID0gbmV3IFNlcXVlbnRpYWxTdHJhdGVneShcbiAgICBbXG4gICAgICBuZXcgSWZTdHJhdGVneShcbiAgICAgICAgdGVzdFN1cHBvcnRzU3RyYXRlZ3koc3RyZWFtaW5nX2xvb3ApLFxuICAgICAgICBuZXcgQmVzdENvbm5lY3RlZEV2ZXJTdHJhdGVneShbXG4gICAgICAgICAgc3RyZWFtaW5nX2xvb3AsXG4gICAgICAgICAgbmV3IERlbGF5ZWRTdHJhdGVneShwb2xsaW5nX2xvb3AsIHsgZGVsYXk6IDQwMDAgfSlcbiAgICAgICAgXSksXG4gICAgICAgIHBvbGxpbmdfbG9vcFxuICAgICAgKVxuICAgIF0sXG4gICAgdGltZW91dHNcbiAgKTtcblxuICB2YXIgaHR0cF9mYWxsYmFja19sb29wID0gbmV3IElmU3RyYXRlZ3koXG4gICAgdGVzdFN1cHBvcnRzU3RyYXRlZ3koaHR0cF9sb29wKSxcbiAgICBodHRwX2xvb3AsXG4gICAgc29ja2pzX2xvb3BcbiAgKTtcblxuICB2YXIgd3NTdHJhdGVneTtcbiAgaWYgKGJhc2VPcHRpb25zLnVzZVRMUykge1xuICAgIHdzU3RyYXRlZ3kgPSBuZXcgQmVzdENvbm5lY3RlZEV2ZXJTdHJhdGVneShbXG4gICAgICB3c19sb29wLFxuICAgICAgbmV3IERlbGF5ZWRTdHJhdGVneShodHRwX2ZhbGxiYWNrX2xvb3AsIHsgZGVsYXk6IDIwMDAgfSlcbiAgICBdKTtcbiAgfSBlbHNlIHtcbiAgICB3c1N0cmF0ZWd5ID0gbmV3IEJlc3RDb25uZWN0ZWRFdmVyU3RyYXRlZ3koW1xuICAgICAgd3NfbG9vcCxcbiAgICAgIG5ldyBEZWxheWVkU3RyYXRlZ3kod3NzX2xvb3AsIHsgZGVsYXk6IDIwMDAgfSksXG4gICAgICBuZXcgRGVsYXllZFN0cmF0ZWd5KGh0dHBfZmFsbGJhY2tfbG9vcCwgeyBkZWxheTogNTAwMCB9KVxuICAgIF0pO1xuICB9XG5cbiAgcmV0dXJuIG5ldyBDYWNoZWRTdHJhdGVneShcbiAgICBuZXcgRmlyc3RDb25uZWN0ZWRTdHJhdGVneShcbiAgICAgIG5ldyBJZlN0cmF0ZWd5KFxuICAgICAgICB0ZXN0U3VwcG9ydHNTdHJhdGVneSh3c190cmFuc3BvcnQpLFxuICAgICAgICB3c1N0cmF0ZWd5LFxuICAgICAgICBodHRwX2ZhbGxiYWNrX2xvb3BcbiAgICAgIClcbiAgICApLFxuICAgIGRlZmluZWRUcmFuc3BvcnRzLFxuICAgIHtcbiAgICAgIHR0bDogMTgwMDAwMCxcbiAgICAgIHRpbWVsaW5lOiBiYXNlT3B0aW9ucy50aW1lbGluZSxcbiAgICAgIHVzZVRMUzogYmFzZU9wdGlvbnMudXNlVExTXG4gICAgfVxuICApO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgZ2V0RGVmYXVsdFN0cmF0ZWd5O1xuIiwgImltcG9ydCB7IERlcGVuZGVuY2llcyB9IGZyb20gJy4uL2RvbS9kZXBlbmRlbmNpZXMnO1xuXG4vKiogSW5pdGlhbGl6ZXMgdGhlIHRyYW5zcG9ydC5cbiAqXG4gKiBGZXRjaGVzIHJlc291cmNlcyBpZiBuZWVkZWQgYW5kIHRoZW4gdHJhbnNpdGlvbnMgdG8gaW5pdGlhbGl6ZWQuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uKCkge1xuICB2YXIgc2VsZiA9IHRoaXM7XG5cbiAgc2VsZi50aW1lbGluZS5pbmZvKFxuICAgIHNlbGYuYnVpbGRUaW1lbGluZU1lc3NhZ2Uoe1xuICAgICAgdHJhbnNwb3J0OiBzZWxmLm5hbWUgKyAoc2VsZi5vcHRpb25zLnVzZVRMUyA/ICdzJyA6ICcnKVxuICAgIH0pXG4gICk7XG5cbiAgaWYgKHNlbGYuaG9va3MuaXNJbml0aWFsaXplZCgpKSB7XG4gICAgc2VsZi5jaGFuZ2VTdGF0ZSgnaW5pdGlhbGl6ZWQnKTtcbiAgfSBlbHNlIGlmIChzZWxmLmhvb2tzLmZpbGUpIHtcbiAgICBzZWxmLmNoYW5nZVN0YXRlKCdpbml0aWFsaXppbmcnKTtcbiAgICBEZXBlbmRlbmNpZXMubG9hZChcbiAgICAgIHNlbGYuaG9va3MuZmlsZSxcbiAgICAgIHsgdXNlVExTOiBzZWxmLm9wdGlvbnMudXNlVExTIH0sXG4gICAgICBmdW5jdGlvbihlcnJvciwgY2FsbGJhY2spIHtcbiAgICAgICAgaWYgKHNlbGYuaG9va3MuaXNJbml0aWFsaXplZCgpKSB7XG4gICAgICAgICAgc2VsZi5jaGFuZ2VTdGF0ZSgnaW5pdGlhbGl6ZWQnKTtcbiAgICAgICAgICBjYWxsYmFjayh0cnVlKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBpZiAoZXJyb3IpIHtcbiAgICAgICAgICAgIHNlbGYub25FcnJvcihlcnJvcik7XG4gICAgICAgICAgfVxuICAgICAgICAgIHNlbGYub25DbG9zZSgpO1xuICAgICAgICAgIGNhbGxiYWNrKGZhbHNlKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICk7XG4gIH0gZWxzZSB7XG4gICAgc2VsZi5vbkNsb3NlKCk7XG4gIH1cbn1cbiIsICJpbXBvcnQgSFRUUFJlcXVlc3QgZnJvbSAnY29yZS9odHRwL2h0dHBfcmVxdWVzdCc7XG5pbXBvcnQgUmVxdWVzdEhvb2tzIGZyb20gJ2NvcmUvaHR0cC9yZXF1ZXN0X2hvb2tzJztcbmltcG9ydCBBamF4IGZyb20gJ2NvcmUvaHR0cC9hamF4JztcbmltcG9ydCAqIGFzIEVycm9ycyBmcm9tICdjb3JlL2Vycm9ycyc7XG5cbnZhciBob29rczogUmVxdWVzdEhvb2tzID0ge1xuICBnZXRSZXF1ZXN0OiBmdW5jdGlvbihzb2NrZXQ6IEhUVFBSZXF1ZXN0KTogQWpheCB7XG4gICAgdmFyIHhkciA9IG5ldyAoPGFueT53aW5kb3cpLlhEb21haW5SZXF1ZXN0KCk7XG4gICAgeGRyLm9udGltZW91dCA9IGZ1bmN0aW9uKCkge1xuICAgICAgc29ja2V0LmVtaXQoJ2Vycm9yJywgbmV3IEVycm9ycy5SZXF1ZXN0VGltZWRPdXQoKSk7XG4gICAgICBzb2NrZXQuY2xvc2UoKTtcbiAgICB9O1xuICAgIHhkci5vbmVycm9yID0gZnVuY3Rpb24oZSkge1xuICAgICAgc29ja2V0LmVtaXQoJ2Vycm9yJywgZSk7XG4gICAgICBzb2NrZXQuY2xvc2UoKTtcbiAgICB9O1xuICAgIHhkci5vbnByb2dyZXNzID0gZnVuY3Rpb24oKSB7XG4gICAgICBpZiAoeGRyLnJlc3BvbnNlVGV4dCAmJiB4ZHIucmVzcG9uc2VUZXh0Lmxlbmd0aCA+IDApIHtcbiAgICAgICAgc29ja2V0Lm9uQ2h1bmsoMjAwLCB4ZHIucmVzcG9uc2VUZXh0KTtcbiAgICAgIH1cbiAgICB9O1xuICAgIHhkci5vbmxvYWQgPSBmdW5jdGlvbigpIHtcbiAgICAgIGlmICh4ZHIucmVzcG9uc2VUZXh0ICYmIHhkci5yZXNwb25zZVRleHQubGVuZ3RoID4gMCkge1xuICAgICAgICBzb2NrZXQub25DaHVuaygyMDAsIHhkci5yZXNwb25zZVRleHQpO1xuICAgICAgfVxuICAgICAgc29ja2V0LmVtaXQoJ2ZpbmlzaGVkJywgMjAwKTtcbiAgICAgIHNvY2tldC5jbG9zZSgpO1xuICAgIH07XG4gICAgcmV0dXJuIHhkcjtcbiAgfSxcbiAgYWJvcnRSZXF1ZXN0OiBmdW5jdGlvbih4ZHI6IEFqYXgpIHtcbiAgICB4ZHIub250aW1lb3V0ID0geGRyLm9uZXJyb3IgPSB4ZHIub25wcm9ncmVzcyA9IHhkci5vbmxvYWQgPSBudWxsO1xuICAgIHhkci5hYm9ydCgpO1xuICB9XG59O1xuXG5leHBvcnQgZGVmYXVsdCBob29rcztcbiIsICJpbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcbmltcG9ydCBSZXF1ZXN0SG9va3MgZnJvbSAnLi9yZXF1ZXN0X2hvb2tzJztcbmltcG9ydCBBamF4IGZyb20gJy4vYWpheCc7XG5pbXBvcnQgeyBkZWZhdWx0IGFzIEV2ZW50c0Rpc3BhdGNoZXIgfSBmcm9tICcuLi9ldmVudHMvZGlzcGF0Y2hlcic7XG5cbmNvbnN0IE1BWF9CVUZGRVJfTEVOR1RIID0gMjU2ICogMTAyNDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSFRUUFJlcXVlc3QgZXh0ZW5kcyBFdmVudHNEaXNwYXRjaGVyIHtcbiAgaG9va3M6IFJlcXVlc3RIb29rcztcbiAgbWV0aG9kOiBzdHJpbmc7XG4gIHVybDogc3RyaW5nO1xuICBwb3NpdGlvbjogbnVtYmVyO1xuICB4aHI6IEFqYXg7XG4gIHVubG9hZGVyOiBGdW5jdGlvbjtcblxuICBjb25zdHJ1Y3Rvcihob29rczogUmVxdWVzdEhvb2tzLCBtZXRob2Q6IHN0cmluZywgdXJsOiBzdHJpbmcpIHtcbiAgICBzdXBlcigpO1xuICAgIHRoaXMuaG9va3MgPSBob29rcztcbiAgICB0aGlzLm1ldGhvZCA9IG1ldGhvZDtcbiAgICB0aGlzLnVybCA9IHVybDtcbiAgfVxuXG4gIHN0YXJ0KHBheWxvYWQ/OiBhbnkpIHtcbiAgICB0aGlzLnBvc2l0aW9uID0gMDtcbiAgICB0aGlzLnhociA9IHRoaXMuaG9va3MuZ2V0UmVxdWVzdCh0aGlzKTtcblxuICAgIHRoaXMudW5sb2FkZXIgPSAoKSA9PiB7XG4gICAgICB0aGlzLmNsb3NlKCk7XG4gICAgfTtcbiAgICBSdW50aW1lLmFkZFVubG9hZExpc3RlbmVyKHRoaXMudW5sb2FkZXIpO1xuXG4gICAgdGhpcy54aHIub3Blbih0aGlzLm1ldGhvZCwgdGhpcy51cmwsIHRydWUpO1xuXG4gICAgaWYgKHRoaXMueGhyLnNldFJlcXVlc3RIZWFkZXIpIHtcbiAgICAgIHRoaXMueGhyLnNldFJlcXVlc3RIZWFkZXIoJ0NvbnRlbnQtVHlwZScsICdhcHBsaWNhdGlvbi9qc29uJyk7IC8vIFJlYWN0TmF0aXZlIGRvZXNuJ3Qgc2V0IHRoaXMgaGVhZGVyIGJ5IGRlZmF1bHQuXG4gICAgfVxuICAgIHRoaXMueGhyLnNlbmQocGF5bG9hZCk7XG4gIH1cblxuICBjbG9zZSgpIHtcbiAgICBpZiAodGhpcy51bmxvYWRlcikge1xuICAgICAgUnVudGltZS5yZW1vdmVVbmxvYWRMaXN0ZW5lcih0aGlzLnVubG9hZGVyKTtcbiAgICAgIHRoaXMudW5sb2FkZXIgPSBudWxsO1xuICAgIH1cbiAgICBpZiAodGhpcy54aHIpIHtcbiAgICAgIHRoaXMuaG9va3MuYWJvcnRSZXF1ZXN0KHRoaXMueGhyKTtcbiAgICAgIHRoaXMueGhyID0gbnVsbDtcbiAgICB9XG4gIH1cblxuICBvbkNodW5rKHN0YXR1czogbnVtYmVyLCBkYXRhOiBhbnkpIHtcbiAgICB3aGlsZSAodHJ1ZSkge1xuICAgICAgdmFyIGNodW5rID0gdGhpcy5hZHZhbmNlQnVmZmVyKGRhdGEpO1xuICAgICAgaWYgKGNodW5rKSB7XG4gICAgICAgIHRoaXMuZW1pdCgnY2h1bmsnLCB7IHN0YXR1czogc3RhdHVzLCBkYXRhOiBjaHVuayB9KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGJyZWFrO1xuICAgICAgfVxuICAgIH1cbiAgICBpZiAodGhpcy5pc0J1ZmZlclRvb0xvbmcoZGF0YSkpIHtcbiAgICAgIHRoaXMuZW1pdCgnYnVmZmVyX3Rvb19sb25nJyk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBhZHZhbmNlQnVmZmVyKGJ1ZmZlcjogYW55W10pOiBhbnkge1xuICAgIHZhciB1bnJlYWREYXRhID0gYnVmZmVyLnNsaWNlKHRoaXMucG9zaXRpb24pO1xuICAgIHZhciBlbmRPZkxpbmVQb3NpdGlvbiA9IHVucmVhZERhdGEuaW5kZXhPZignXFxuJyk7XG5cbiAgICBpZiAoZW5kT2ZMaW5lUG9zaXRpb24gIT09IC0xKSB7XG4gICAgICB0aGlzLnBvc2l0aW9uICs9IGVuZE9mTGluZVBvc2l0aW9uICsgMTtcbiAgICAgIHJldHVybiB1bnJlYWREYXRhLnNsaWNlKDAsIGVuZE9mTGluZVBvc2l0aW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgLy8gY2h1bmsgaXMgbm90IGZpbmlzaGVkIHlldCwgZG9uJ3QgbW92ZSB0aGUgYnVmZmVyIHBvaW50ZXJcbiAgICAgIHJldHVybiBudWxsO1xuICAgIH1cbiAgfVxuXG4gIHByaXZhdGUgaXNCdWZmZXJUb29Mb25nKGJ1ZmZlcjogYW55KTogYm9vbGVhbiB7XG4gICAgcmV0dXJuIHRoaXMucG9zaXRpb24gPT09IGJ1ZmZlci5sZW5ndGggJiYgYnVmZmVyLmxlbmd0aCA+IE1BWF9CVUZGRVJfTEVOR1RIO1xuICB9XG59XG4iLCAiZW51bSBTdGF0ZSB7XG4gIENPTk5FQ1RJTkcgPSAwLFxuICBPUEVOID0gMSxcbiAgQ0xPU0VEID0gM1xufVxuXG5leHBvcnQgZGVmYXVsdCBTdGF0ZTtcbiIsICJpbXBvcnQgVVJMTG9jYXRpb24gZnJvbSAnLi91cmxfbG9jYXRpb24nO1xuaW1wb3J0IFN0YXRlIGZyb20gJy4vc3RhdGUnO1xuaW1wb3J0IFNvY2tldCBmcm9tICcuLi9zb2NrZXQnO1xuaW1wb3J0IFNvY2tldEhvb2tzIGZyb20gJy4vc29ja2V0X2hvb2tzJztcbmltcG9ydCBVdGlsIGZyb20gJy4uL3V0aWwnO1xuaW1wb3J0IEFqYXggZnJvbSAnLi9hamF4JztcbmltcG9ydCBIVFRQUmVxdWVzdCBmcm9tICcuL2h0dHBfcmVxdWVzdCc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcblxudmFyIGF1dG9JbmNyZW1lbnQgPSAxO1xuXG5jbGFzcyBIVFRQU29ja2V0IGltcGxlbWVudHMgU29ja2V0IHtcbiAgaG9va3M6IFNvY2tldEhvb2tzO1xuICBzZXNzaW9uOiBzdHJpbmc7XG4gIGxvY2F0aW9uOiBVUkxMb2NhdGlvbjtcbiAgcmVhZHlTdGF0ZTogU3RhdGU7XG4gIHN0cmVhbTogSFRUUFJlcXVlc3Q7XG5cbiAgb25vcGVuOiAoKSA9PiB2b2lkO1xuICBvbmVycm9yOiAoZXJyb3I6IGFueSkgPT4gdm9pZDtcbiAgb25jbG9zZTogKGNsb3NlRXZlbnQ6IGFueSkgPT4gdm9pZDtcbiAgb25tZXNzYWdlOiAobWVzc2FnZTogYW55KSA9PiB2b2lkO1xuICBvbmFjdGl2aXR5OiAoKSA9PiB2b2lkO1xuXG4gIGNvbnN0cnVjdG9yKGhvb2tzOiBTb2NrZXRIb29rcywgdXJsOiBzdHJpbmcpIHtcbiAgICB0aGlzLmhvb2tzID0gaG9va3M7XG4gICAgdGhpcy5zZXNzaW9uID0gcmFuZG9tTnVtYmVyKDEwMDApICsgJy8nICsgcmFuZG9tU3RyaW5nKDgpO1xuICAgIHRoaXMubG9jYXRpb24gPSBnZXRMb2NhdGlvbih1cmwpO1xuICAgIHRoaXMucmVhZHlTdGF0ZSA9IFN0YXRlLkNPTk5FQ1RJTkc7XG4gICAgdGhpcy5vcGVuU3RyZWFtKCk7XG4gIH1cblxuICBzZW5kKHBheWxvYWQ6IGFueSkge1xuICAgIHJldHVybiB0aGlzLnNlbmRSYXcoSlNPTi5zdHJpbmdpZnkoW3BheWxvYWRdKSk7XG4gIH1cblxuICBwaW5nKCkge1xuICAgIHRoaXMuaG9va3Muc2VuZEhlYXJ0YmVhdCh0aGlzKTtcbiAgfVxuXG4gIGNsb3NlKGNvZGU6IGFueSwgcmVhc29uOiBhbnkpIHtcbiAgICB0aGlzLm9uQ2xvc2UoY29kZSwgcmVhc29uLCB0cnVlKTtcbiAgfVxuXG4gIC8qKiBGb3IgaW50ZXJuYWwgdXNlIG9ubHkgKi9cbiAgc2VuZFJhdyhwYXlsb2FkOiBhbnkpOiBib29sZWFuIHtcbiAgICBpZiAodGhpcy5yZWFkeVN0YXRlID09PSBTdGF0ZS5PUEVOKSB7XG4gICAgICB0cnkge1xuICAgICAgICBSdW50aW1lLmNyZWF0ZVNvY2tldFJlcXVlc3QoXG4gICAgICAgICAgJ1BPU1QnLFxuICAgICAgICAgIGdldFVuaXF1ZVVSTChnZXRTZW5kVVJMKHRoaXMubG9jYXRpb24sIHRoaXMuc2Vzc2lvbikpXG4gICAgICAgICkuc3RhcnQocGF5bG9hZCk7XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gIH1cblxuICAvKiogRm9yIGludGVybmFsIHVzZSBvbmx5ICovXG4gIHJlY29ubmVjdCgpIHtcbiAgICB0aGlzLmNsb3NlU3RyZWFtKCk7XG4gICAgdGhpcy5vcGVuU3RyZWFtKCk7XG4gIH1cblxuICAvKiogRm9yIGludGVybmFsIHVzZSBvbmx5ICovXG4gIG9uQ2xvc2UoY29kZSwgcmVhc29uLCB3YXNDbGVhbikge1xuICAgIHRoaXMuY2xvc2VTdHJlYW0oKTtcbiAgICB0aGlzLnJlYWR5U3RhdGUgPSBTdGF0ZS5DTE9TRUQ7XG4gICAgaWYgKHRoaXMub25jbG9zZSkge1xuICAgICAgdGhpcy5vbmNsb3NlKHtcbiAgICAgICAgY29kZTogY29kZSxcbiAgICAgICAgcmVhc29uOiByZWFzb24sXG4gICAgICAgIHdhc0NsZWFuOiB3YXNDbGVhblxuICAgICAgfSk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBvbkNodW5rKGNodW5rKSB7XG4gICAgaWYgKGNodW5rLnN0YXR1cyAhPT0gMjAwKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGlmICh0aGlzLnJlYWR5U3RhdGUgPT09IFN0YXRlLk9QRU4pIHtcbiAgICAgIHRoaXMub25BY3Rpdml0eSgpO1xuICAgIH1cblxuICAgIHZhciBwYXlsb2FkO1xuICAgIHZhciB0eXBlID0gY2h1bmsuZGF0YS5zbGljZSgwLCAxKTtcbiAgICBzd2l0Y2ggKHR5cGUpIHtcbiAgICAgIGNhc2UgJ28nOlxuICAgICAgICBwYXlsb2FkID0gSlNPTi5wYXJzZShjaHVuay5kYXRhLnNsaWNlKDEpIHx8ICd7fScpO1xuICAgICAgICB0aGlzLm9uT3BlbihwYXlsb2FkKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlICdhJzpcbiAgICAgICAgcGF5bG9hZCA9IEpTT04ucGFyc2UoY2h1bmsuZGF0YS5zbGljZSgxKSB8fCAnW10nKTtcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBwYXlsb2FkLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgdGhpcy5vbkV2ZW50KHBheWxvYWRbaV0pO1xuICAgICAgICB9XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAnbSc6XG4gICAgICAgIHBheWxvYWQgPSBKU09OLnBhcnNlKGNodW5rLmRhdGEuc2xpY2UoMSkgfHwgJ251bGwnKTtcbiAgICAgICAgdGhpcy5vbkV2ZW50KHBheWxvYWQpO1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgJ2gnOlxuICAgICAgICB0aGlzLmhvb2tzLm9uSGVhcnRiZWF0KHRoaXMpO1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgJ2MnOlxuICAgICAgICBwYXlsb2FkID0gSlNPTi5wYXJzZShjaHVuay5kYXRhLnNsaWNlKDEpIHx8ICdbXScpO1xuICAgICAgICB0aGlzLm9uQ2xvc2UocGF5bG9hZFswXSwgcGF5bG9hZFsxXSwgdHJ1ZSk7XG4gICAgICAgIGJyZWFrO1xuICAgIH1cbiAgfVxuXG4gIHByaXZhdGUgb25PcGVuKG9wdGlvbnMpIHtcbiAgICBpZiAodGhpcy5yZWFkeVN0YXRlID09PSBTdGF0ZS5DT05ORUNUSU5HKSB7XG4gICAgICBpZiAob3B0aW9ucyAmJiBvcHRpb25zLmhvc3RuYW1lKSB7XG4gICAgICAgIHRoaXMubG9jYXRpb24uYmFzZSA9IHJlcGxhY2VIb3N0KHRoaXMubG9jYXRpb24uYmFzZSwgb3B0aW9ucy5ob3N0bmFtZSk7XG4gICAgICB9XG4gICAgICB0aGlzLnJlYWR5U3RhdGUgPSBTdGF0ZS5PUEVOO1xuXG4gICAgICBpZiAodGhpcy5vbm9wZW4pIHtcbiAgICAgICAgdGhpcy5vbm9wZW4oKTtcbiAgICAgIH1cbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5vbkNsb3NlKDEwMDYsICdTZXJ2ZXIgbG9zdCBzZXNzaW9uJywgdHJ1ZSk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBvbkV2ZW50KGV2ZW50KSB7XG4gICAgaWYgKHRoaXMucmVhZHlTdGF0ZSA9PT0gU3RhdGUuT1BFTiAmJiB0aGlzLm9ubWVzc2FnZSkge1xuICAgICAgdGhpcy5vbm1lc3NhZ2UoeyBkYXRhOiBldmVudCB9KTtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIG9uQWN0aXZpdHkoKSB7XG4gICAgaWYgKHRoaXMub25hY3Rpdml0eSkge1xuICAgICAgdGhpcy5vbmFjdGl2aXR5KCk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBvbkVycm9yKGVycm9yKSB7XG4gICAgaWYgKHRoaXMub25lcnJvcikge1xuICAgICAgdGhpcy5vbmVycm9yKGVycm9yKTtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIG9wZW5TdHJlYW0oKSB7XG4gICAgdGhpcy5zdHJlYW0gPSBSdW50aW1lLmNyZWF0ZVNvY2tldFJlcXVlc3QoXG4gICAgICAnUE9TVCcsXG4gICAgICBnZXRVbmlxdWVVUkwodGhpcy5ob29rcy5nZXRSZWNlaXZlVVJMKHRoaXMubG9jYXRpb24sIHRoaXMuc2Vzc2lvbikpXG4gICAgKTtcblxuICAgIHRoaXMuc3RyZWFtLmJpbmQoJ2NodW5rJywgY2h1bmsgPT4ge1xuICAgICAgdGhpcy5vbkNodW5rKGNodW5rKTtcbiAgICB9KTtcbiAgICB0aGlzLnN0cmVhbS5iaW5kKCdmaW5pc2hlZCcsIHN0YXR1cyA9PiB7XG4gICAgICB0aGlzLmhvb2tzLm9uRmluaXNoZWQodGhpcywgc3RhdHVzKTtcbiAgICB9KTtcbiAgICB0aGlzLnN0cmVhbS5iaW5kKCdidWZmZXJfdG9vX2xvbmcnLCAoKSA9PiB7XG4gICAgICB0aGlzLnJlY29ubmVjdCgpO1xuICAgIH0pO1xuXG4gICAgdHJ5IHtcbiAgICAgIHRoaXMuc3RyZWFtLnN0YXJ0KCk7XG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgIFV0aWwuZGVmZXIoKCkgPT4ge1xuICAgICAgICB0aGlzLm9uRXJyb3IoZXJyb3IpO1xuICAgICAgICB0aGlzLm9uQ2xvc2UoMTAwNiwgJ0NvdWxkIG5vdCBzdGFydCBzdHJlYW1pbmcnLCBmYWxzZSk7XG4gICAgICB9KTtcbiAgICB9XG4gIH1cblxuICBwcml2YXRlIGNsb3NlU3RyZWFtKCkge1xuICAgIGlmICh0aGlzLnN0cmVhbSkge1xuICAgICAgdGhpcy5zdHJlYW0udW5iaW5kX2FsbCgpO1xuICAgICAgdGhpcy5zdHJlYW0uY2xvc2UoKTtcbiAgICAgIHRoaXMuc3RyZWFtID0gbnVsbDtcbiAgICB9XG4gIH1cbn1cblxuZnVuY3Rpb24gZ2V0TG9jYXRpb24odXJsKTogVVJMTG9jYXRpb24ge1xuICB2YXIgcGFydHMgPSAvKFteXFw/XSopXFwvKihcXD8/LiopLy5leGVjKHVybCk7XG4gIHJldHVybiB7XG4gICAgYmFzZTogcGFydHNbMV0sXG4gICAgcXVlcnlTdHJpbmc6IHBhcnRzWzJdXG4gIH07XG59XG5cbmZ1bmN0aW9uIGdldFNlbmRVUkwodXJsOiBVUkxMb2NhdGlvbiwgc2Vzc2lvbjogc3RyaW5nKTogc3RyaW5nIHtcbiAgcmV0dXJuIHVybC5iYXNlICsgJy8nICsgc2Vzc2lvbiArICcveGhyX3NlbmQnO1xufVxuXG5mdW5jdGlvbiBnZXRVbmlxdWVVUkwodXJsOiBzdHJpbmcpOiBzdHJpbmcge1xuICB2YXIgc2VwYXJhdG9yID0gdXJsLmluZGV4T2YoJz8nKSA9PT0gLTEgPyAnPycgOiAnJic7XG4gIHJldHVybiB1cmwgKyBzZXBhcmF0b3IgKyAndD0nICsgK25ldyBEYXRlKCkgKyAnJm49JyArIGF1dG9JbmNyZW1lbnQrKztcbn1cblxuZnVuY3Rpb24gcmVwbGFjZUhvc3QodXJsOiBzdHJpbmcsIGhvc3RuYW1lOiBzdHJpbmcpOiBzdHJpbmcge1xuICB2YXIgdXJsUGFydHMgPSAvKGh0dHBzPzpcXC9cXC8pKFteXFwvOl0rKSgoXFwvfDopPy4qKS8uZXhlYyh1cmwpO1xuICByZXR1cm4gdXJsUGFydHNbMV0gKyBob3N0bmFtZSArIHVybFBhcnRzWzNdO1xufVxuXG5mdW5jdGlvbiByYW5kb21OdW1iZXIobWF4OiBudW1iZXIpOiBudW1iZXIge1xuICByZXR1cm4gUnVudGltZS5yYW5kb21JbnQobWF4KTtcbn1cblxuZnVuY3Rpb24gcmFuZG9tU3RyaW5nKGxlbmd0aDogbnVtYmVyKTogc3RyaW5nIHtcbiAgdmFyIHJlc3VsdCA9IFtdO1xuXG4gIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuZ3RoOyBpKyspIHtcbiAgICByZXN1bHQucHVzaChyYW5kb21OdW1iZXIoMzIpLnRvU3RyaW5nKDMyKSk7XG4gIH1cblxuICByZXR1cm4gcmVzdWx0LmpvaW4oJycpO1xufVxuXG5leHBvcnQgZGVmYXVsdCBIVFRQU29ja2V0O1xuIiwgImltcG9ydCBTb2NrZXRIb29rcyBmcm9tICcuL3NvY2tldF9ob29rcyc7XG5pbXBvcnQgSFRUUFNvY2tldCBmcm9tICcuL2h0dHBfc29ja2V0JztcblxudmFyIGhvb2tzOiBTb2NrZXRIb29rcyA9IHtcbiAgZ2V0UmVjZWl2ZVVSTDogZnVuY3Rpb24odXJsLCBzZXNzaW9uKSB7XG4gICAgcmV0dXJuIHVybC5iYXNlICsgJy8nICsgc2Vzc2lvbiArICcveGhyX3N0cmVhbWluZycgKyB1cmwucXVlcnlTdHJpbmc7XG4gIH0sXG4gIG9uSGVhcnRiZWF0OiBmdW5jdGlvbihzb2NrZXQpIHtcbiAgICBzb2NrZXQuc2VuZFJhdygnW10nKTtcbiAgfSxcbiAgc2VuZEhlYXJ0YmVhdDogZnVuY3Rpb24oc29ja2V0KSB7XG4gICAgc29ja2V0LnNlbmRSYXcoJ1tdJyk7XG4gIH0sXG4gIG9uRmluaXNoZWQ6IGZ1bmN0aW9uKHNvY2tldCwgc3RhdHVzKSB7XG4gICAgc29ja2V0Lm9uQ2xvc2UoMTAwNiwgJ0Nvbm5lY3Rpb24gaW50ZXJydXB0ZWQgKCcgKyBzdGF0dXMgKyAnKScsIGZhbHNlKTtcbiAgfVxufTtcblxuZXhwb3J0IGRlZmF1bHQgaG9va3M7XG4iLCAiaW1wb3J0IFNvY2tldEhvb2tzIGZyb20gJy4vc29ja2V0X2hvb2tzJztcbmltcG9ydCBVUkxMb2NhdGlvbiBmcm9tICcuL3VybF9sb2NhdGlvbic7XG5pbXBvcnQgSFRUUFNvY2tldCBmcm9tICcuL2h0dHBfc29ja2V0JztcblxudmFyIGhvb2tzOiBTb2NrZXRIb29rcyA9IHtcbiAgZ2V0UmVjZWl2ZVVSTDogZnVuY3Rpb24odXJsOiBVUkxMb2NhdGlvbiwgc2Vzc2lvbjogc3RyaW5nKTogc3RyaW5nIHtcbiAgICByZXR1cm4gdXJsLmJhc2UgKyAnLycgKyBzZXNzaW9uICsgJy94aHInICsgdXJsLnF1ZXJ5U3RyaW5nO1xuICB9LFxuICBvbkhlYXJ0YmVhdDogZnVuY3Rpb24oKSB7XG4gICAgLy8gbmV4dCBIVFRQIHJlcXVlc3Qgd2lsbCByZXNldCBzZXJ2ZXIncyBhY3Rpdml0eSB0aW1lclxuICB9LFxuICBzZW5kSGVhcnRiZWF0OiBmdW5jdGlvbihzb2NrZXQpIHtcbiAgICBzb2NrZXQuc2VuZFJhdygnW10nKTtcbiAgfSxcbiAgb25GaW5pc2hlZDogZnVuY3Rpb24oc29ja2V0LCBzdGF0dXMpIHtcbiAgICBpZiAoc3RhdHVzID09PSAyMDApIHtcbiAgICAgIHNvY2tldC5yZWNvbm5lY3QoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgc29ja2V0Lm9uQ2xvc2UoMTAwNiwgJ0Nvbm5lY3Rpb24gaW50ZXJydXB0ZWQgKCcgKyBzdGF0dXMgKyAnKScsIGZhbHNlKTtcbiAgICB9XG4gIH1cbn07XG5cbmV4cG9ydCBkZWZhdWx0IGhvb2tzO1xuIiwgImltcG9ydCBIVFRQUmVxdWVzdCBmcm9tICdjb3JlL2h0dHAvaHR0cF9yZXF1ZXN0JztcbmltcG9ydCBSZXF1ZXN0SG9va3MgZnJvbSAnY29yZS9odHRwL3JlcXVlc3RfaG9va3MnO1xuaW1wb3J0IEFqYXggZnJvbSAnY29yZS9odHRwL2FqYXgnO1xuaW1wb3J0IFJ1bnRpbWUgZnJvbSAncnVudGltZSc7XG5cbnZhciBob29rczogUmVxdWVzdEhvb2tzID0ge1xuICBnZXRSZXF1ZXN0OiBmdW5jdGlvbihzb2NrZXQ6IEhUVFBSZXF1ZXN0KTogQWpheCB7XG4gICAgdmFyIENvbnN0cnVjdG9yID0gUnVudGltZS5nZXRYSFJBUEkoKTtcbiAgICB2YXIgeGhyID0gbmV3IENvbnN0cnVjdG9yKCk7XG4gICAgeGhyLm9ucmVhZHlzdGF0ZWNoYW5nZSA9IHhoci5vbnByb2dyZXNzID0gZnVuY3Rpb24oKSB7XG4gICAgICBzd2l0Y2ggKHhoci5yZWFkeVN0YXRlKSB7XG4gICAgICAgIGNhc2UgMzpcbiAgICAgICAgICBpZiAoeGhyLnJlc3BvbnNlVGV4dCAmJiB4aHIucmVzcG9uc2VUZXh0Lmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgIHNvY2tldC5vbkNodW5rKHhoci5zdGF0dXMsIHhoci5yZXNwb25zZVRleHQpO1xuICAgICAgICAgIH1cbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSA0OlxuICAgICAgICAgIC8vIHRoaXMgaGFwcGVucyBvbmx5IG9uIGVycm9ycywgbmV2ZXIgYWZ0ZXIgY2FsbGluZyBjbG9zZVxuICAgICAgICAgIGlmICh4aHIucmVzcG9uc2VUZXh0ICYmIHhoci5yZXNwb25zZVRleHQubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgc29ja2V0Lm9uQ2h1bmsoeGhyLnN0YXR1cywgeGhyLnJlc3BvbnNlVGV4dCk7XG4gICAgICAgICAgfVxuICAgICAgICAgIHNvY2tldC5lbWl0KCdmaW5pc2hlZCcsIHhoci5zdGF0dXMpO1xuICAgICAgICAgIHNvY2tldC5jbG9zZSgpO1xuICAgICAgICAgIGJyZWFrO1xuICAgICAgfVxuICAgIH07XG4gICAgcmV0dXJuIHhocjtcbiAgfSxcbiAgYWJvcnRSZXF1ZXN0OiBmdW5jdGlvbih4aHI6IEFqYXgpIHtcbiAgICB4aHIub25yZWFkeXN0YXRlY2hhbmdlID0gbnVsbDtcbiAgICB4aHIuYWJvcnQoKTtcbiAgfVxufTtcblxuZXhwb3J0IGRlZmF1bHQgaG9va3M7XG4iLCAiaW1wb3J0IEhUVFBSZXF1ZXN0IGZyb20gJ2NvcmUvaHR0cC9odHRwX3JlcXVlc3QnO1xuaW1wb3J0IEhUVFBTb2NrZXQgZnJvbSAnY29yZS9odHRwL2h0dHBfc29ja2V0JztcbmltcG9ydCBTb2NrZXRIb29rcyBmcm9tICdjb3JlL2h0dHAvc29ja2V0X2hvb2tzJztcbmltcG9ydCBSZXF1ZXN0SG9va3MgZnJvbSAnY29yZS9odHRwL3JlcXVlc3RfaG9va3MnO1xuaW1wb3J0IHN0cmVhbWluZ0hvb2tzIGZyb20gJ2NvcmUvaHR0cC9odHRwX3N0cmVhbWluZ19zb2NrZXQnO1xuaW1wb3J0IHBvbGxpbmdIb29rcyBmcm9tICdjb3JlL2h0dHAvaHR0cF9wb2xsaW5nX3NvY2tldCc7XG5pbXBvcnQgeGhySG9va3MgZnJvbSAnLi9odHRwX3hocl9yZXF1ZXN0JztcbmltcG9ydCBIVFRQRmFjdG9yeSBmcm9tICdjb3JlL2h0dHAvaHR0cF9mYWN0b3J5JztcblxudmFyIEhUVFA6IEhUVFBGYWN0b3J5ID0ge1xuICBjcmVhdGVTdHJlYW1pbmdTb2NrZXQodXJsOiBzdHJpbmcpOiBIVFRQU29ja2V0IHtcbiAgICByZXR1cm4gdGhpcy5jcmVhdGVTb2NrZXQoc3RyZWFtaW5nSG9va3MsIHVybCk7XG4gIH0sXG5cbiAgY3JlYXRlUG9sbGluZ1NvY2tldCh1cmw6IHN0cmluZyk6IEhUVFBTb2NrZXQge1xuICAgIHJldHVybiB0aGlzLmNyZWF0ZVNvY2tldChwb2xsaW5nSG9va3MsIHVybCk7XG4gIH0sXG5cbiAgY3JlYXRlU29ja2V0KGhvb2tzOiBTb2NrZXRIb29rcywgdXJsOiBzdHJpbmcpOiBIVFRQU29ja2V0IHtcbiAgICByZXR1cm4gbmV3IEhUVFBTb2NrZXQoaG9va3MsIHVybCk7XG4gIH0sXG5cbiAgY3JlYXRlWEhSKG1ldGhvZDogc3RyaW5nLCB1cmw6IHN0cmluZyk6IEhUVFBSZXF1ZXN0IHtcbiAgICByZXR1cm4gdGhpcy5jcmVhdGVSZXF1ZXN0KHhockhvb2tzLCBtZXRob2QsIHVybCk7XG4gIH0sXG5cbiAgY3JlYXRlUmVxdWVzdChob29rczogUmVxdWVzdEhvb2tzLCBtZXRob2Q6IHN0cmluZywgdXJsOiBzdHJpbmcpOiBIVFRQUmVxdWVzdCB7XG4gICAgcmV0dXJuIG5ldyBIVFRQUmVxdWVzdChob29rcywgbWV0aG9kLCB1cmwpO1xuICB9XG59O1xuXG5leHBvcnQgZGVmYXVsdCBIVFRQO1xuIiwgImltcG9ydCB4ZHJIb29rcyBmcm9tICcuL2h0dHBfeGRvbWFpbl9yZXF1ZXN0JztcbmltcG9ydCBIVFRQIGZyb20gJ2lzb21vcnBoaWMvaHR0cC9odHRwJztcblxuSFRUUC5jcmVhdGVYRFIgPSBmdW5jdGlvbihtZXRob2QsIHVybCkge1xuICByZXR1cm4gdGhpcy5jcmVhdGVSZXF1ZXN0KHhkckhvb2tzLCBtZXRob2QsIHVybCk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBIVFRQO1xuIiwgImltcG9ydCBCcm93c2VyIGZyb20gJy4vYnJvd3Nlcic7XG5pbXBvcnQgeyBEZXBlbmRlbmNpZXMsIERlcGVuZGVuY2llc1JlY2VpdmVycyB9IGZyb20gJy4vZG9tL2RlcGVuZGVuY2llcyc7XG5pbXBvcnQgeyBBdXRoVHJhbnNwb3J0LCBBdXRoVHJhbnNwb3J0cyB9IGZyb20gJ2NvcmUvYXV0aC9hdXRoX3RyYW5zcG9ydHMnO1xuaW1wb3J0IHhockF1dGggZnJvbSAnaXNvbW9ycGhpYy9hdXRoL3hocl9hdXRoJztcbmltcG9ydCBqc29ucEF1dGggZnJvbSAnLi9hdXRoL2pzb25wX2F1dGgnO1xuaW1wb3J0IFRpbWVsaW5lVHJhbnNwb3J0IGZyb20gJ2NvcmUvdGltZWxpbmUvdGltZWxpbmVfdHJhbnNwb3J0JztcbmltcG9ydCBUaW1lbGluZVNlbmRlciBmcm9tICdjb3JlL3RpbWVsaW5lL3RpbWVsaW5lX3NlbmRlcic7XG5pbXBvcnQgU2NyaXB0UmVxdWVzdCBmcm9tICcuL2RvbS9zY3JpcHRfcmVxdWVzdCc7XG5pbXBvcnQgSlNPTlBSZXF1ZXN0IGZyb20gJy4vZG9tL2pzb25wX3JlcXVlc3QnO1xuaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnY29yZS91dGlscy9jb2xsZWN0aW9ucyc7XG5pbXBvcnQgeyBTY3JpcHRSZWNlaXZlcnMgfSBmcm9tICcuL2RvbS9zY3JpcHRfcmVjZWl2ZXJfZmFjdG9yeSc7XG5pbXBvcnQganNvbnBUaW1lbGluZSBmcm9tICcuL3RpbWVsaW5lL2pzb25wX3RpbWVsaW5lJztcbmltcG9ydCBUcmFuc3BvcnRzIGZyb20gJy4vdHJhbnNwb3J0cy90cmFuc3BvcnRzJztcbmltcG9ydCBBamF4IGZyb20gJ2NvcmUvaHR0cC9hamF4JztcbmltcG9ydCB7IE5ldHdvcmsgfSBmcm9tICcuL25ldF9pbmZvJztcbmltcG9ydCBnZXREZWZhdWx0U3RyYXRlZ3kgZnJvbSAnLi9kZWZhdWx0X3N0cmF0ZWd5JztcbmltcG9ydCB0cmFuc3BvcnRDb25uZWN0aW9uSW5pdGlhbGl6ZXIgZnJvbSAnLi90cmFuc3BvcnRzL3RyYW5zcG9ydF9jb25uZWN0aW9uX2luaXRpYWxpemVyJztcbmltcG9ydCBIVFRQRmFjdG9yeSBmcm9tICcuL2h0dHAvaHR0cCc7XG5pbXBvcnQgSFRUUFJlcXVlc3QgZnJvbSAnY29yZS9odHRwL2h0dHBfcmVxdWVzdCc7XG5cbnZhciBSdW50aW1lOiBCcm93c2VyID0ge1xuICAvLyBmb3IganNvbnAgYXV0aFxuICBuZXh0QXV0aENhbGxiYWNrSUQ6IDEsXG4gIGF1dGhfY2FsbGJhY2tzOiB7fSxcbiAgU2NyaXB0UmVjZWl2ZXJzLFxuICBEZXBlbmRlbmNpZXNSZWNlaXZlcnMsXG4gIGdldERlZmF1bHRTdHJhdGVneSxcbiAgVHJhbnNwb3J0cyxcbiAgdHJhbnNwb3J0Q29ubmVjdGlvbkluaXRpYWxpemVyLFxuICBIVFRQRmFjdG9yeSxcblxuICBUaW1lbGluZVRyYW5zcG9ydDoganNvbnBUaW1lbGluZSxcblxuICBnZXRYSFJBUEkoKSB7XG4gICAgcmV0dXJuIHdpbmRvdy5YTUxIdHRwUmVxdWVzdDtcbiAgfSxcblxuICBnZXRXZWJTb2NrZXRBUEkoKSB7XG4gICAgcmV0dXJuIHdpbmRvdy5XZWJTb2NrZXQgfHwgd2luZG93Lk1veldlYlNvY2tldDtcbiAgfSxcblxuICBzZXR1cChQdXNoZXJDbGFzcyk6IHZvaWQge1xuICAgICg8YW55PndpbmRvdykuUHVzaGVyID0gUHVzaGVyQ2xhc3M7IC8vIEpTT05wIHJlcXVpcmVzIFB1c2hlciB0byBiZSBpbiB0aGUgZ2xvYmFsIHNjb3BlLlxuICAgIHZhciBpbml0aWFsaXplT25Eb2N1bWVudEJvZHkgPSAoKSA9PiB7XG4gICAgICB0aGlzLm9uRG9jdW1lbnRCb2R5KFB1c2hlckNsYXNzLnJlYWR5KTtcbiAgICB9O1xuICAgIGlmICghKDxhbnk+d2luZG93KS5KU09OKSB7XG4gICAgICBEZXBlbmRlbmNpZXMubG9hZCgnanNvbjInLCB7fSwgaW5pdGlhbGl6ZU9uRG9jdW1lbnRCb2R5KTtcbiAgICB9IGVsc2Uge1xuICAgICAgaW5pdGlhbGl6ZU9uRG9jdW1lbnRCb2R5KCk7XG4gICAgfVxuICB9LFxuXG4gIGdldERvY3VtZW50KCk6IERvY3VtZW50IHtcbiAgICByZXR1cm4gZG9jdW1lbnQ7XG4gIH0sXG5cbiAgZ2V0UHJvdG9jb2woKTogc3RyaW5nIHtcbiAgICByZXR1cm4gdGhpcy5nZXREb2N1bWVudCgpLmxvY2F0aW9uLnByb3RvY29sO1xuICB9LFxuXG4gIGdldEF1dGhvcml6ZXJzKCk6IEF1dGhUcmFuc3BvcnRzIHtcbiAgICByZXR1cm4geyBhamF4OiB4aHJBdXRoLCBqc29ucDoganNvbnBBdXRoIH07XG4gIH0sXG5cbiAgb25Eb2N1bWVudEJvZHkoY2FsbGJhY2s6IEZ1bmN0aW9uKSB7XG4gICAgaWYgKGRvY3VtZW50LmJvZHkpIHtcbiAgICAgIGNhbGxiYWNrKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICB0aGlzLm9uRG9jdW1lbnRCb2R5KGNhbGxiYWNrKTtcbiAgICAgIH0sIDApO1xuICAgIH1cbiAgfSxcblxuICBjcmVhdGVKU09OUFJlcXVlc3QodXJsOiBzdHJpbmcsIGRhdGE6IGFueSk6IEpTT05QUmVxdWVzdCB7XG4gICAgcmV0dXJuIG5ldyBKU09OUFJlcXVlc3QodXJsLCBkYXRhKTtcbiAgfSxcblxuICBjcmVhdGVTY3JpcHRSZXF1ZXN0KHNyYzogc3RyaW5nKTogU2NyaXB0UmVxdWVzdCB7XG4gICAgcmV0dXJuIG5ldyBTY3JpcHRSZXF1ZXN0KHNyYyk7XG4gIH0sXG5cbiAgZ2V0TG9jYWxTdG9yYWdlKCkge1xuICAgIHRyeSB7XG4gICAgICByZXR1cm4gd2luZG93LmxvY2FsU3RvcmFnZTtcbiAgICB9IGNhdGNoIChlKSB7XG4gICAgICByZXR1cm4gdW5kZWZpbmVkO1xuICAgIH1cbiAgfSxcblxuICBjcmVhdGVYSFIoKTogQWpheCB7XG4gICAgaWYgKHRoaXMuZ2V0WEhSQVBJKCkpIHtcbiAgICAgIHJldHVybiB0aGlzLmNyZWF0ZVhNTEh0dHBSZXF1ZXN0KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiB0aGlzLmNyZWF0ZU1pY3Jvc29mdFhIUigpO1xuICAgIH1cbiAgfSxcblxuICBjcmVhdGVYTUxIdHRwUmVxdWVzdCgpOiBBamF4IHtcbiAgICB2YXIgQ29uc3RydWN0b3IgPSB0aGlzLmdldFhIUkFQSSgpO1xuICAgIHJldHVybiBuZXcgQ29uc3RydWN0b3IoKTtcbiAgfSxcblxuICBjcmVhdGVNaWNyb3NvZnRYSFIoKTogQWpheCB7XG4gICAgcmV0dXJuIG5ldyBBY3RpdmVYT2JqZWN0KCdNaWNyb3NvZnQuWE1MSFRUUCcpO1xuICB9LFxuXG4gIGdldE5ldHdvcmsoKSB7XG4gICAgcmV0dXJuIE5ldHdvcms7XG4gIH0sXG5cbiAgY3JlYXRlV2ViU29ja2V0KHVybDogc3RyaW5nKTogYW55IHtcbiAgICB2YXIgQ29uc3RydWN0b3IgPSB0aGlzLmdldFdlYlNvY2tldEFQSSgpO1xuICAgIHJldHVybiBuZXcgQ29uc3RydWN0b3IodXJsKTtcbiAgfSxcblxuICBjcmVhdGVTb2NrZXRSZXF1ZXN0KG1ldGhvZDogc3RyaW5nLCB1cmw6IHN0cmluZyk6IEhUVFBSZXF1ZXN0IHtcbiAgICBpZiAodGhpcy5pc1hIUlN1cHBvcnRlZCgpKSB7XG4gICAgICByZXR1cm4gdGhpcy5IVFRQRmFjdG9yeS5jcmVhdGVYSFIobWV0aG9kLCB1cmwpO1xuICAgIH0gZWxzZSBpZiAodGhpcy5pc1hEUlN1cHBvcnRlZCh1cmwuaW5kZXhPZignaHR0cHM6JykgPT09IDApKSB7XG4gICAgICByZXR1cm4gdGhpcy5IVFRQRmFjdG9yeS5jcmVhdGVYRFIobWV0aG9kLCB1cmwpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aHJvdyAnQ3Jvc3Mtb3JpZ2luIEhUVFAgcmVxdWVzdHMgYXJlIG5vdCBzdXBwb3J0ZWQnO1xuICAgIH1cbiAgfSxcblxuICBpc1hIUlN1cHBvcnRlZCgpOiBib29sZWFuIHtcbiAgICB2YXIgQ29uc3RydWN0b3IgPSB0aGlzLmdldFhIUkFQSSgpO1xuICAgIHJldHVybiAoXG4gICAgICBCb29sZWFuKENvbnN0cnVjdG9yKSAmJiBuZXcgQ29uc3RydWN0b3IoKS53aXRoQ3JlZGVudGlhbHMgIT09IHVuZGVmaW5lZFxuICAgICk7XG4gIH0sXG5cbiAgaXNYRFJTdXBwb3J0ZWQodXNlVExTPzogYm9vbGVhbik6IGJvb2xlYW4ge1xuICAgIHZhciBwcm90b2NvbCA9IHVzZVRMUyA/ICdodHRwczonIDogJ2h0dHA6JztcbiAgICB2YXIgZG9jdW1lbnRQcm90b2NvbCA9IHRoaXMuZ2V0UHJvdG9jb2woKTtcbiAgICByZXR1cm4gKFxuICAgICAgQm9vbGVhbig8YW55PndpbmRvd1snWERvbWFpblJlcXVlc3QnXSkgJiYgZG9jdW1lbnRQcm90b2NvbCA9PT0gcHJvdG9jb2xcbiAgICApO1xuICB9LFxuXG4gIGFkZFVubG9hZExpc3RlbmVyKGxpc3RlbmVyOiBhbnkpIHtcbiAgICBpZiAod2luZG93LmFkZEV2ZW50TGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ3VubG9hZCcsIGxpc3RlbmVyLCBmYWxzZSk7XG4gICAgfSBlbHNlIGlmICh3aW5kb3cuYXR0YWNoRXZlbnQgIT09IHVuZGVmaW5lZCkge1xuICAgICAgd2luZG93LmF0dGFjaEV2ZW50KCdvbnVubG9hZCcsIGxpc3RlbmVyKTtcbiAgICB9XG4gIH0sXG5cbiAgcmVtb3ZlVW5sb2FkTGlzdGVuZXIobGlzdGVuZXI6IGFueSkge1xuICAgIGlmICh3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lciAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcigndW5sb2FkJywgbGlzdGVuZXIsIGZhbHNlKTtcbiAgICB9IGVsc2UgaWYgKHdpbmRvdy5kZXRhY2hFdmVudCAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICB3aW5kb3cuZGV0YWNoRXZlbnQoJ29udW5sb2FkJywgbGlzdGVuZXIpO1xuICAgIH1cbiAgfSxcblxuICByYW5kb21JbnQobWF4OiBudW1iZXIpOiBudW1iZXIge1xuICAgIC8qKlxuICAgICAqIFJldHVybiB2YWx1ZXMgaW4gdGhlIHJhbmdlIG9mIFswLCAxW1xuICAgICAqL1xuICAgIGNvbnN0IHJhbmRvbSA9IGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgY3J5cHRvID0gd2luZG93LmNyeXB0byB8fCB3aW5kb3dbJ21zQ3J5cHRvJ107XG4gICAgICBjb25zdCByYW5kb20gPSBjcnlwdG8uZ2V0UmFuZG9tVmFsdWVzKG5ldyBVaW50MzJBcnJheSgxKSlbMF07XG5cbiAgICAgIHJldHVybiByYW5kb20gLyAyICoqIDMyO1xuICAgIH07XG5cbiAgICByZXR1cm4gTWF0aC5mbG9vcihyYW5kb20oKSAqIG1heCk7XG4gIH1cbn07XG5cbmV4cG9ydCBkZWZhdWx0IFJ1bnRpbWU7XG4iLCAiZW51bSBUaW1lbGluZUxldmVsIHtcbiAgRVJST1IgPSAzLFxuICBJTkZPID0gNixcbiAgREVCVUcgPSA3XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRpbWVsaW5lTGV2ZWw7XG4iLCAiaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnLi4vdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IFV0aWwgZnJvbSAnLi4vdXRpbCc7XG5pbXBvcnQgeyBkZWZhdWx0IGFzIExldmVsIH0gZnJvbSAnLi9sZXZlbCc7XG5cbmV4cG9ydCBpbnRlcmZhY2UgVGltZWxpbmVPcHRpb25zIHtcbiAgbGV2ZWw/OiBMZXZlbDtcbiAgbGltaXQ/OiBudW1iZXI7XG4gIHZlcnNpb24/OiBzdHJpbmc7XG4gIGNsdXN0ZXI/OiBzdHJpbmc7XG4gIGZlYXR1cmVzPzogc3RyaW5nW107XG4gIHBhcmFtcz86IGFueTtcbn1cblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVGltZWxpbmUge1xuICBrZXk6IHN0cmluZztcbiAgc2Vzc2lvbjogbnVtYmVyO1xuICBldmVudHM6IGFueVtdO1xuICBvcHRpb25zOiBUaW1lbGluZU9wdGlvbnM7XG4gIHNlbnQ6IG51bWJlcjtcbiAgdW5pcXVlSUQ6IG51bWJlcjtcblxuICBjb25zdHJ1Y3RvcihrZXk6IHN0cmluZywgc2Vzc2lvbjogbnVtYmVyLCBvcHRpb25zOiBUaW1lbGluZU9wdGlvbnMpIHtcbiAgICB0aGlzLmtleSA9IGtleTtcbiAgICB0aGlzLnNlc3Npb24gPSBzZXNzaW9uO1xuICAgIHRoaXMuZXZlbnRzID0gW107XG4gICAgdGhpcy5vcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcbiAgICB0aGlzLnNlbnQgPSAwO1xuICAgIHRoaXMudW5pcXVlSUQgPSAwO1xuICB9XG5cbiAgbG9nKGxldmVsLCBldmVudCkge1xuICAgIGlmIChsZXZlbCA8PSB0aGlzLm9wdGlvbnMubGV2ZWwpIHtcbiAgICAgIHRoaXMuZXZlbnRzLnB1c2goXG4gICAgICAgIENvbGxlY3Rpb25zLmV4dGVuZCh7fSwgZXZlbnQsIHsgdGltZXN0YW1wOiBVdGlsLm5vdygpIH0pXG4gICAgICApO1xuICAgICAgaWYgKHRoaXMub3B0aW9ucy5saW1pdCAmJiB0aGlzLmV2ZW50cy5sZW5ndGggPiB0aGlzLm9wdGlvbnMubGltaXQpIHtcbiAgICAgICAgdGhpcy5ldmVudHMuc2hpZnQoKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICBlcnJvcihldmVudCkge1xuICAgIHRoaXMubG9nKExldmVsLkVSUk9SLCBldmVudCk7XG4gIH1cblxuICBpbmZvKGV2ZW50KSB7XG4gICAgdGhpcy5sb2coTGV2ZWwuSU5GTywgZXZlbnQpO1xuICB9XG5cbiAgZGVidWcoZXZlbnQpIHtcbiAgICB0aGlzLmxvZyhMZXZlbC5ERUJVRywgZXZlbnQpO1xuICB9XG5cbiAgaXNFbXB0eSgpIHtcbiAgICByZXR1cm4gdGhpcy5ldmVudHMubGVuZ3RoID09PSAwO1xuICB9XG5cbiAgc2VuZChzZW5kZm4sIGNhbGxiYWNrKSB7XG4gICAgdmFyIGRhdGEgPSBDb2xsZWN0aW9ucy5leHRlbmQoXG4gICAgICB7XG4gICAgICAgIHNlc3Npb246IHRoaXMuc2Vzc2lvbixcbiAgICAgICAgYnVuZGxlOiB0aGlzLnNlbnQgKyAxLFxuICAgICAgICBrZXk6IHRoaXMua2V5LFxuICAgICAgICBsaWI6ICdqcycsXG4gICAgICAgIHZlcnNpb246IHRoaXMub3B0aW9ucy52ZXJzaW9uLFxuICAgICAgICBjbHVzdGVyOiB0aGlzLm9wdGlvbnMuY2x1c3RlcixcbiAgICAgICAgZmVhdHVyZXM6IHRoaXMub3B0aW9ucy5mZWF0dXJlcyxcbiAgICAgICAgdGltZWxpbmU6IHRoaXMuZXZlbnRzXG4gICAgICB9LFxuICAgICAgdGhpcy5vcHRpb25zLnBhcmFtc1xuICAgICk7XG5cbiAgICB0aGlzLmV2ZW50cyA9IFtdO1xuICAgIHNlbmRmbihkYXRhLCAoZXJyb3IsIHJlc3VsdCkgPT4ge1xuICAgICAgaWYgKCFlcnJvcikge1xuICAgICAgICB0aGlzLnNlbnQrKztcbiAgICAgIH1cbiAgICAgIGlmIChjYWxsYmFjaykge1xuICAgICAgICBjYWxsYmFjayhlcnJvciwgcmVzdWx0KTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiB0cnVlO1xuICB9XG5cbiAgZ2VuZXJhdGVVbmlxdWVJRCgpOiBudW1iZXIge1xuICAgIHRoaXMudW5pcXVlSUQrKztcbiAgICByZXR1cm4gdGhpcy51bmlxdWVJRDtcbiAgfVxufVxuIiwgImltcG9ydCBGYWN0b3J5IGZyb20gJy4uL3V0aWxzL2ZhY3RvcnknO1xuaW1wb3J0IFV0aWwgZnJvbSAnLi4vdXRpbCc7XG5pbXBvcnQgKiBhcyBFcnJvcnMgZnJvbSAnLi4vZXJyb3JzJztcbmltcG9ydCAqIGFzIENvbGxlY3Rpb25zIGZyb20gJy4uL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBTdHJhdGVneSBmcm9tICcuL3N0cmF0ZWd5JztcbmltcG9ydCBUcmFuc3BvcnQgZnJvbSAnLi4vdHJhbnNwb3J0cy90cmFuc3BvcnQnO1xuaW1wb3J0IFN0cmF0ZWd5T3B0aW9ucyBmcm9tICcuL3N0cmF0ZWd5X29wdGlvbnMnO1xuaW1wb3J0IEhhbmRzaGFrZSBmcm9tICcuLi9jb25uZWN0aW9uL2hhbmRzaGFrZSc7XG5cbi8qKiBQcm92aWRlcyBhIHN0cmF0ZWd5IGludGVyZmFjZSBmb3IgdHJhbnNwb3J0cy5cbiAqXG4gKiBAcGFyYW0ge1N0cmluZ30gbmFtZVxuICogQHBhcmFtIHtOdW1iZXJ9IHByaW9yaXR5XG4gKiBAcGFyYW0ge0NsYXNzfSB0cmFuc3BvcnRcbiAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFRyYW5zcG9ydFN0cmF0ZWd5IGltcGxlbWVudHMgU3RyYXRlZ3kge1xuICBuYW1lOiBzdHJpbmc7XG4gIHByaW9yaXR5OiBudW1iZXI7XG4gIHRyYW5zcG9ydDogVHJhbnNwb3J0O1xuICBvcHRpb25zOiBTdHJhdGVneU9wdGlvbnM7XG5cbiAgY29uc3RydWN0b3IoXG4gICAgbmFtZTogc3RyaW5nLFxuICAgIHByaW9yaXR5OiBudW1iZXIsXG4gICAgdHJhbnNwb3J0OiBUcmFuc3BvcnQsXG4gICAgb3B0aW9uczogU3RyYXRlZ3lPcHRpb25zXG4gICkge1xuICAgIHRoaXMubmFtZSA9IG5hbWU7XG4gICAgdGhpcy5wcmlvcml0eSA9IHByaW9yaXR5O1xuICAgIHRoaXMudHJhbnNwb3J0ID0gdHJhbnNwb3J0O1xuICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG4gIH1cblxuICAvKiogUmV0dXJucyB3aGV0aGVyIHRoZSB0cmFuc3BvcnQgaXMgc3VwcG9ydGVkIGluIHRoZSBicm93c2VyLlxuICAgKlxuICAgKiBAcmV0dXJucyB7Qm9vbGVhbn1cbiAgICovXG4gIGlzU3VwcG9ydGVkKCk6IGJvb2xlYW4ge1xuICAgIHJldHVybiB0aGlzLnRyYW5zcG9ydC5pc1N1cHBvcnRlZCh7XG4gICAgICB1c2VUTFM6IHRoaXMub3B0aW9ucy51c2VUTFNcbiAgICB9KTtcbiAgfVxuXG4gIC8qKiBMYXVuY2hlcyBhIGNvbm5lY3Rpb24gYXR0ZW1wdCBhbmQgcmV0dXJucyBhIHN0cmF0ZWd5IHJ1bm5lci5cbiAgICpcbiAgICogQHBhcmFtICB7RnVuY3Rpb259IGNhbGxiYWNrXG4gICAqIEByZXR1cm4ge09iamVjdH0gc3RyYXRlZ3kgcnVubmVyXG4gICAqL1xuICBjb25uZWN0KG1pblByaW9yaXR5OiBudW1iZXIsIGNhbGxiYWNrOiBGdW5jdGlvbikge1xuICAgIGlmICghdGhpcy5pc1N1cHBvcnRlZCgpKSB7XG4gICAgICByZXR1cm4gZmFpbEF0dGVtcHQobmV3IEVycm9ycy5VbnN1cHBvcnRlZFN0cmF0ZWd5KCksIGNhbGxiYWNrKTtcbiAgICB9IGVsc2UgaWYgKHRoaXMucHJpb3JpdHkgPCBtaW5Qcmlvcml0eSkge1xuICAgICAgcmV0dXJuIGZhaWxBdHRlbXB0KG5ldyBFcnJvcnMuVHJhbnNwb3J0UHJpb3JpdHlUb29Mb3coKSwgY2FsbGJhY2spO1xuICAgIH1cblxuICAgIHZhciBjb25uZWN0ZWQgPSBmYWxzZTtcbiAgICB2YXIgdHJhbnNwb3J0ID0gdGhpcy50cmFuc3BvcnQuY3JlYXRlQ29ubmVjdGlvbihcbiAgICAgIHRoaXMubmFtZSxcbiAgICAgIHRoaXMucHJpb3JpdHksXG4gICAgICB0aGlzLm9wdGlvbnMua2V5LFxuICAgICAgdGhpcy5vcHRpb25zXG4gICAgKTtcbiAgICB2YXIgaGFuZHNoYWtlID0gbnVsbDtcblxuICAgIHZhciBvbkluaXRpYWxpemVkID0gZnVuY3Rpb24oKSB7XG4gICAgICB0cmFuc3BvcnQudW5iaW5kKCdpbml0aWFsaXplZCcsIG9uSW5pdGlhbGl6ZWQpO1xuICAgICAgdHJhbnNwb3J0LmNvbm5lY3QoKTtcbiAgICB9O1xuICAgIHZhciBvbk9wZW4gPSBmdW5jdGlvbigpIHtcbiAgICAgIGhhbmRzaGFrZSA9IEZhY3RvcnkuY3JlYXRlSGFuZHNoYWtlKHRyYW5zcG9ydCwgZnVuY3Rpb24ocmVzdWx0KSB7XG4gICAgICAgIGNvbm5lY3RlZCA9IHRydWU7XG4gICAgICAgIHVuYmluZExpc3RlbmVycygpO1xuICAgICAgICBjYWxsYmFjayhudWxsLCByZXN1bHQpO1xuICAgICAgfSk7XG4gICAgfTtcbiAgICB2YXIgb25FcnJvciA9IGZ1bmN0aW9uKGVycm9yKSB7XG4gICAgICB1bmJpbmRMaXN0ZW5lcnMoKTtcbiAgICAgIGNhbGxiYWNrKGVycm9yKTtcbiAgICB9O1xuICAgIHZhciBvbkNsb3NlZCA9IGZ1bmN0aW9uKCkge1xuICAgICAgdW5iaW5kTGlzdGVuZXJzKCk7XG4gICAgICB2YXIgc2VyaWFsaXplZFRyYW5zcG9ydDtcblxuICAgICAgLy8gVGhlIHJlYXNvbiBmb3IgdGhpcyB0cnkvY2F0Y2ggYmxvY2sgaXMgdGhhdCBvbiBSZWFjdCBOYXRpdmVcbiAgICAgIC8vIHRoZSBXZWJTb2NrZXQgb2JqZWN0IGlzIGNpcmN1bGFyLiBUaGVyZWZvcmUgdHJhbnNwb3J0LnNvY2tldCB3aWxsXG4gICAgICAvLyB0aHJvdyBlcnJvcnMgdXBvbiBzdHJpbmdpZmljYXRpb24uIENvbGxlY3Rpb25zLnNhZmVKU09OU3RyaW5naWZ5XG4gICAgICAvLyBkaXNjYXJkcyBjaXJjdWxhciByZWZlcmVuY2VzIHdoZW4gc2VyaWFsaXppbmcuXG4gICAgICBzZXJpYWxpemVkVHJhbnNwb3J0ID0gQ29sbGVjdGlvbnMuc2FmZUpTT05TdHJpbmdpZnkodHJhbnNwb3J0KTtcbiAgICAgIGNhbGxiYWNrKG5ldyBFcnJvcnMuVHJhbnNwb3J0Q2xvc2VkKHNlcmlhbGl6ZWRUcmFuc3BvcnQpKTtcbiAgICB9O1xuXG4gICAgdmFyIHVuYmluZExpc3RlbmVycyA9IGZ1bmN0aW9uKCkge1xuICAgICAgdHJhbnNwb3J0LnVuYmluZCgnaW5pdGlhbGl6ZWQnLCBvbkluaXRpYWxpemVkKTtcbiAgICAgIHRyYW5zcG9ydC51bmJpbmQoJ29wZW4nLCBvbk9wZW4pO1xuICAgICAgdHJhbnNwb3J0LnVuYmluZCgnZXJyb3InLCBvbkVycm9yKTtcbiAgICAgIHRyYW5zcG9ydC51bmJpbmQoJ2Nsb3NlZCcsIG9uQ2xvc2VkKTtcbiAgICB9O1xuXG4gICAgdHJhbnNwb3J0LmJpbmQoJ2luaXRpYWxpemVkJywgb25Jbml0aWFsaXplZCk7XG4gICAgdHJhbnNwb3J0LmJpbmQoJ29wZW4nLCBvbk9wZW4pO1xuICAgIHRyYW5zcG9ydC5iaW5kKCdlcnJvcicsIG9uRXJyb3IpO1xuICAgIHRyYW5zcG9ydC5iaW5kKCdjbG9zZWQnLCBvbkNsb3NlZCk7XG5cbiAgICAvLyBjb25uZWN0IHdpbGwgYmUgY2FsbGVkIGF1dG9tYXRpY2FsbHkgYWZ0ZXIgaW5pdGlhbGl6YXRpb25cbiAgICB0cmFuc3BvcnQuaW5pdGlhbGl6ZSgpO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIGFib3J0OiAoKSA9PiB7XG4gICAgICAgIGlmIChjb25uZWN0ZWQpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgdW5iaW5kTGlzdGVuZXJzKCk7XG4gICAgICAgIGlmIChoYW5kc2hha2UpIHtcbiAgICAgICAgICBoYW5kc2hha2UuY2xvc2UoKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICB0cmFuc3BvcnQuY2xvc2UoKTtcbiAgICAgICAgfVxuICAgICAgfSxcbiAgICAgIGZvcmNlTWluUHJpb3JpdHk6IHAgPT4ge1xuICAgICAgICBpZiAoY29ubmVjdGVkKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGlmICh0aGlzLnByaW9yaXR5IDwgcCkge1xuICAgICAgICAgIGlmIChoYW5kc2hha2UpIHtcbiAgICAgICAgICAgIGhhbmRzaGFrZS5jbG9zZSgpO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB0cmFuc3BvcnQuY2xvc2UoKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9O1xuICB9XG59XG5cbmZ1bmN0aW9uIGZhaWxBdHRlbXB0KGVycm9yOiBFcnJvciwgY2FsbGJhY2s6IEZ1bmN0aW9uKSB7XG4gIFV0aWwuZGVmZXIoZnVuY3Rpb24oKSB7XG4gICAgY2FsbGJhY2soZXJyb3IpO1xuICB9KTtcbiAgcmV0dXJuIHtcbiAgICBhYm9ydDogZnVuY3Rpb24oKSB7fSxcbiAgICBmb3JjZU1pblByaW9yaXR5OiBmdW5jdGlvbigpIHt9XG4gIH07XG59XG4iLCAiaW1wb3J0ICogYXMgQ29sbGVjdGlvbnMgZnJvbSAnLi4vdXRpbHMvY29sbGVjdGlvbnMnO1xuaW1wb3J0IFV0aWwgZnJvbSAnLi4vdXRpbCc7XG5pbXBvcnQgVHJhbnNwb3J0TWFuYWdlciBmcm9tICcuLi90cmFuc3BvcnRzL3RyYW5zcG9ydF9tYW5hZ2VyJztcbmltcG9ydCAqIGFzIEVycm9ycyBmcm9tICcuLi9lcnJvcnMnO1xuaW1wb3J0IFN0cmF0ZWd5IGZyb20gJy4vc3RyYXRlZ3knO1xuaW1wb3J0IFRyYW5zcG9ydFN0cmF0ZWd5IGZyb20gJy4vdHJhbnNwb3J0X3N0cmF0ZWd5JztcbmltcG9ydCBTdHJhdGVneU9wdGlvbnMgZnJvbSAnLi4vc3RyYXRlZ2llcy9zdHJhdGVneV9vcHRpb25zJztcbmltcG9ydCB7IENvbmZpZyB9IGZyb20gJy4uL2NvbmZpZyc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcblxuY29uc3QgeyBUcmFuc3BvcnRzIH0gPSBSdW50aW1lO1xuXG5leHBvcnQgdmFyIGRlZmluZVRyYW5zcG9ydCA9IGZ1bmN0aW9uKFxuICBjb25maWc6IENvbmZpZyxcbiAgbmFtZTogc3RyaW5nLFxuICB0eXBlOiBzdHJpbmcsXG4gIHByaW9yaXR5OiBudW1iZXIsXG4gIG9wdGlvbnM6IFN0cmF0ZWd5T3B0aW9ucyxcbiAgbWFuYWdlcj86IFRyYW5zcG9ydE1hbmFnZXJcbik6IFN0cmF0ZWd5IHtcbiAgdmFyIHRyYW5zcG9ydENsYXNzID0gVHJhbnNwb3J0c1t0eXBlXTtcbiAgaWYgKCF0cmFuc3BvcnRDbGFzcykge1xuICAgIHRocm93IG5ldyBFcnJvcnMuVW5zdXBwb3J0ZWRUcmFuc3BvcnQodHlwZSk7XG4gIH1cblxuICB2YXIgZW5hYmxlZCA9XG4gICAgKCFjb25maWcuZW5hYmxlZFRyYW5zcG9ydHMgfHxcbiAgICAgIENvbGxlY3Rpb25zLmFycmF5SW5kZXhPZihjb25maWcuZW5hYmxlZFRyYW5zcG9ydHMsIG5hbWUpICE9PSAtMSkgJiZcbiAgICAoIWNvbmZpZy5kaXNhYmxlZFRyYW5zcG9ydHMgfHxcbiAgICAgIENvbGxlY3Rpb25zLmFycmF5SW5kZXhPZihjb25maWcuZGlzYWJsZWRUcmFuc3BvcnRzLCBuYW1lKSA9PT0gLTEpO1xuXG4gIHZhciB0cmFuc3BvcnQ7XG4gIGlmIChlbmFibGVkKSB7XG4gICAgb3B0aW9ucyA9IE9iamVjdC5hc3NpZ24oXG4gICAgICB7IGlnbm9yZU51bGxPcmlnaW46IGNvbmZpZy5pZ25vcmVOdWxsT3JpZ2luIH0sXG4gICAgICBvcHRpb25zXG4gICAgKTtcblxuICAgIHRyYW5zcG9ydCA9IG5ldyBUcmFuc3BvcnRTdHJhdGVneShcbiAgICAgIG5hbWUsXG4gICAgICBwcmlvcml0eSxcbiAgICAgIG1hbmFnZXIgPyBtYW5hZ2VyLmdldEFzc2lzdGFudCh0cmFuc3BvcnRDbGFzcykgOiB0cmFuc3BvcnRDbGFzcyxcbiAgICAgIG9wdGlvbnNcbiAgICApO1xuICB9IGVsc2Uge1xuICAgIHRyYW5zcG9ydCA9IFVuc3VwcG9ydGVkU3RyYXRlZ3k7XG4gIH1cblxuICByZXR1cm4gdHJhbnNwb3J0O1xufTtcblxudmFyIFVuc3VwcG9ydGVkU3RyYXRlZ3k6IFN0cmF0ZWd5ID0ge1xuICBpc1N1cHBvcnRlZDogZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9LFxuICBjb25uZWN0OiBmdW5jdGlvbihfLCBjYWxsYmFjaykge1xuICAgIHZhciBkZWZlcnJlZCA9IFV0aWwuZGVmZXIoZnVuY3Rpb24oKSB7XG4gICAgICBjYWxsYmFjayhuZXcgRXJyb3JzLlVuc3VwcG9ydGVkU3RyYXRlZ3koKSk7XG4gICAgfSk7XG4gICAgcmV0dXJuIHtcbiAgICAgIGFib3J0OiBmdW5jdGlvbigpIHtcbiAgICAgICAgZGVmZXJyZWQuZW5zdXJlQWJvcnRlZCgpO1xuICAgICAgfSxcbiAgICAgIGZvcmNlTWluUHJpb3JpdHk6IGZ1bmN0aW9uKCkge31cbiAgICB9O1xuICB9XG59O1xuIiwgImltcG9ydCB7XG4gIFVzZXJBdXRoZW50aWNhdGlvbkNhbGxiYWNrLFxuICBJbnRlcm5hbEF1dGhPcHRpb25zLFxuICBVc2VyQXV0aGVudGljYXRpb25IYW5kbGVyLFxuICBVc2VyQXV0aGVudGljYXRpb25SZXF1ZXN0UGFyYW1zLFxuICBBdXRoUmVxdWVzdFR5cGVcbn0gZnJvbSAnLi9vcHRpb25zJztcblxuaW1wb3J0IFJ1bnRpbWUgZnJvbSAncnVudGltZSc7XG5cbmNvbnN0IGNvbXBvc2VDaGFubmVsUXVlcnkgPSAoXG4gIHBhcmFtczogVXNlckF1dGhlbnRpY2F0aW9uUmVxdWVzdFBhcmFtcyxcbiAgYXV0aE9wdGlvbnM6IEludGVybmFsQXV0aE9wdGlvbnNcbikgPT4ge1xuICB2YXIgcXVlcnkgPSAnc29ja2V0X2lkPScgKyBlbmNvZGVVUklDb21wb25lbnQocGFyYW1zLnNvY2tldElkKTtcblxuICBmb3IgKHZhciBrZXkgaW4gYXV0aE9wdGlvbnMucGFyYW1zKSB7XG4gICAgcXVlcnkgKz1cbiAgICAgICcmJyArXG4gICAgICBlbmNvZGVVUklDb21wb25lbnQoa2V5KSArXG4gICAgICAnPScgK1xuICAgICAgZW5jb2RlVVJJQ29tcG9uZW50KGF1dGhPcHRpb25zLnBhcmFtc1trZXldKTtcbiAgfVxuXG4gIGlmIChhdXRoT3B0aW9ucy5wYXJhbXNQcm92aWRlciAhPSBudWxsKSB7XG4gICAgbGV0IGR5bmFtaWNQYXJhbXMgPSBhdXRoT3B0aW9ucy5wYXJhbXNQcm92aWRlcigpO1xuICAgIGZvciAodmFyIGtleSBpbiBkeW5hbWljUGFyYW1zKSB7XG4gICAgICBxdWVyeSArPVxuICAgICAgICAnJicgK1xuICAgICAgICBlbmNvZGVVUklDb21wb25lbnQoa2V5KSArXG4gICAgICAgICc9JyArXG4gICAgICAgIGVuY29kZVVSSUNvbXBvbmVudChkeW5hbWljUGFyYW1zW2tleV0pO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBxdWVyeTtcbn07XG5cbmNvbnN0IFVzZXJBdXRoZW50aWNhdG9yID0gKFxuICBhdXRoT3B0aW9uczogSW50ZXJuYWxBdXRoT3B0aW9uc1xuKTogVXNlckF1dGhlbnRpY2F0aW9uSGFuZGxlciA9PiB7XG4gIGlmICh0eXBlb2YgUnVudGltZS5nZXRBdXRob3JpemVycygpW2F1dGhPcHRpb25zLnRyYW5zcG9ydF0gPT09ICd1bmRlZmluZWQnKSB7XG4gICAgdGhyb3cgYCcke2F1dGhPcHRpb25zLnRyYW5zcG9ydH0nIGlzIG5vdCBhIHJlY29nbml6ZWQgYXV0aCB0cmFuc3BvcnRgO1xuICB9XG5cbiAgcmV0dXJuIChcbiAgICBwYXJhbXM6IFVzZXJBdXRoZW50aWNhdGlvblJlcXVlc3RQYXJhbXMsXG4gICAgY2FsbGJhY2s6IFVzZXJBdXRoZW50aWNhdGlvbkNhbGxiYWNrXG4gICkgPT4ge1xuICAgIGNvbnN0IHF1ZXJ5ID0gY29tcG9zZUNoYW5uZWxRdWVyeShwYXJhbXMsIGF1dGhPcHRpb25zKTtcblxuICAgIFJ1bnRpbWUuZ2V0QXV0aG9yaXplcnMoKVthdXRoT3B0aW9ucy50cmFuc3BvcnRdKFxuICAgICAgUnVudGltZSxcbiAgICAgIHF1ZXJ5LFxuICAgICAgYXV0aE9wdGlvbnMsXG4gICAgICBBdXRoUmVxdWVzdFR5cGUuVXNlckF1dGhlbnRpY2F0aW9uLFxuICAgICAgY2FsbGJhY2tcbiAgICApO1xuICB9O1xufTtcblxuZXhwb3J0IGRlZmF1bHQgVXNlckF1dGhlbnRpY2F0b3I7XG4iLCAiaW1wb3J0IHtcbiAgQXV0aFJlcXVlc3RUeXBlLFxuICBJbnRlcm5hbEF1dGhPcHRpb25zLFxuICBDaGFubmVsQXV0aG9yaXphdGlvbkhhbmRsZXIsXG4gIENoYW5uZWxBdXRob3JpemF0aW9uUmVxdWVzdFBhcmFtcyxcbiAgQ2hhbm5lbEF1dGhvcml6YXRpb25DYWxsYmFja1xufSBmcm9tICcuL29wdGlvbnMnO1xuXG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcblxuY29uc3QgY29tcG9zZUNoYW5uZWxRdWVyeSA9IChcbiAgcGFyYW1zOiBDaGFubmVsQXV0aG9yaXphdGlvblJlcXVlc3RQYXJhbXMsXG4gIGF1dGhPcHRpb25zOiBJbnRlcm5hbEF1dGhPcHRpb25zXG4pID0+IHtcbiAgdmFyIHF1ZXJ5ID0gJ3NvY2tldF9pZD0nICsgZW5jb2RlVVJJQ29tcG9uZW50KHBhcmFtcy5zb2NrZXRJZCk7XG5cbiAgcXVlcnkgKz0gJyZjaGFubmVsX25hbWU9JyArIGVuY29kZVVSSUNvbXBvbmVudChwYXJhbXMuY2hhbm5lbE5hbWUpO1xuXG4gIGZvciAodmFyIGtleSBpbiBhdXRoT3B0aW9ucy5wYXJhbXMpIHtcbiAgICBxdWVyeSArPVxuICAgICAgJyYnICtcbiAgICAgIGVuY29kZVVSSUNvbXBvbmVudChrZXkpICtcbiAgICAgICc9JyArXG4gICAgICBlbmNvZGVVUklDb21wb25lbnQoYXV0aE9wdGlvbnMucGFyYW1zW2tleV0pO1xuICB9XG5cbiAgaWYgKGF1dGhPcHRpb25zLnBhcmFtc1Byb3ZpZGVyICE9IG51bGwpIHtcbiAgICBsZXQgZHluYW1pY1BhcmFtcyA9IGF1dGhPcHRpb25zLnBhcmFtc1Byb3ZpZGVyKCk7XG4gICAgZm9yICh2YXIga2V5IGluIGR5bmFtaWNQYXJhbXMpIHtcbiAgICAgIHF1ZXJ5ICs9XG4gICAgICAgICcmJyArXG4gICAgICAgIGVuY29kZVVSSUNvbXBvbmVudChrZXkpICtcbiAgICAgICAgJz0nICtcbiAgICAgICAgZW5jb2RlVVJJQ29tcG9uZW50KGR5bmFtaWNQYXJhbXNba2V5XSk7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHF1ZXJ5O1xufTtcblxuY29uc3QgQ2hhbm5lbEF1dGhvcml6ZXIgPSAoXG4gIGF1dGhPcHRpb25zOiBJbnRlcm5hbEF1dGhPcHRpb25zXG4pOiBDaGFubmVsQXV0aG9yaXphdGlvbkhhbmRsZXIgPT4ge1xuICBpZiAodHlwZW9mIFJ1bnRpbWUuZ2V0QXV0aG9yaXplcnMoKVthdXRoT3B0aW9ucy50cmFuc3BvcnRdID09PSAndW5kZWZpbmVkJykge1xuICAgIHRocm93IGAnJHthdXRoT3B0aW9ucy50cmFuc3BvcnR9JyBpcyBub3QgYSByZWNvZ25pemVkIGF1dGggdHJhbnNwb3J0YDtcbiAgfVxuXG4gIHJldHVybiAoXG4gICAgcGFyYW1zOiBDaGFubmVsQXV0aG9yaXphdGlvblJlcXVlc3RQYXJhbXMsXG4gICAgY2FsbGJhY2s6IENoYW5uZWxBdXRob3JpemF0aW9uQ2FsbGJhY2tcbiAgKSA9PiB7XG4gICAgY29uc3QgcXVlcnkgPSBjb21wb3NlQ2hhbm5lbFF1ZXJ5KHBhcmFtcywgYXV0aE9wdGlvbnMpO1xuXG4gICAgUnVudGltZS5nZXRBdXRob3JpemVycygpW2F1dGhPcHRpb25zLnRyYW5zcG9ydF0oXG4gICAgICBSdW50aW1lLFxuICAgICAgcXVlcnksXG4gICAgICBhdXRoT3B0aW9ucyxcbiAgICAgIEF1dGhSZXF1ZXN0VHlwZS5DaGFubmVsQXV0aG9yaXphdGlvbixcbiAgICAgIGNhbGxiYWNrXG4gICAgKTtcbiAgfTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IENoYW5uZWxBdXRob3JpemVyO1xuIiwgImltcG9ydCBDaGFubmVsIGZyb20gJy4uL2NoYW5uZWxzL2NoYW5uZWwnO1xuaW1wb3J0IHtcbiAgQ2hhbm5lbEF1dGhvcml6YXRpb25DYWxsYmFjayxcbiAgQ2hhbm5lbEF1dGhvcml6YXRpb25IYW5kbGVyLFxuICBDaGFubmVsQXV0aG9yaXphdGlvblJlcXVlc3RQYXJhbXMsXG4gIEludGVybmFsQXV0aE9wdGlvbnNcbn0gZnJvbSAnLi9vcHRpb25zJztcblxuZXhwb3J0IGludGVyZmFjZSBEZXByZWNhdGVkQ2hhbm5lbEF1dGhvcml6ZXIge1xuICBhdXRob3JpemUoc29ja2V0SWQ6IHN0cmluZywgY2FsbGJhY2s6IENoYW5uZWxBdXRob3JpemF0aW9uQ2FsbGJhY2spOiB2b2lkO1xufVxuXG5leHBvcnQgaW50ZXJmYWNlIENoYW5uZWxBdXRob3JpemVyR2VuZXJhdG9yIHtcbiAgKFxuICAgIGNoYW5uZWw6IENoYW5uZWwsXG4gICAgb3B0aW9uczogRGVwcmVjYXRlZEF1dGhvcml6ZXJPcHRpb25zXG4gICk6IERlcHJlY2F0ZWRDaGFubmVsQXV0aG9yaXplcjtcbn1cblxuZXhwb3J0IGludGVyZmFjZSBEZXByZWNhdGVkQXV0aE9wdGlvbnMge1xuICBwYXJhbXM/OiBhbnk7XG4gIGhlYWRlcnM/OiBhbnk7XG59XG5cbmV4cG9ydCBpbnRlcmZhY2UgRGVwcmVjYXRlZEF1dGhvcml6ZXJPcHRpb25zIHtcbiAgYXV0aFRyYW5zcG9ydDogJ2FqYXgnIHwgJ2pzb25wJztcbiAgYXV0aEVuZHBvaW50OiBzdHJpbmc7XG4gIGF1dGg/OiBEZXByZWNhdGVkQXV0aE9wdGlvbnM7XG59XG5cbmV4cG9ydCBjb25zdCBDaGFubmVsQXV0aG9yaXplclByb3h5ID0gKFxuICBwdXNoZXIsXG4gIGF1dGhPcHRpb25zOiBJbnRlcm5hbEF1dGhPcHRpb25zLFxuICBjaGFubmVsQXV0aG9yaXplckdlbmVyYXRvcjogQ2hhbm5lbEF1dGhvcml6ZXJHZW5lcmF0b3Jcbik6IENoYW5uZWxBdXRob3JpemF0aW9uSGFuZGxlciA9PiB7XG4gIGNvbnN0IGRlcHJlY2F0ZWRBdXRob3JpemVyT3B0aW9uczogRGVwcmVjYXRlZEF1dGhvcml6ZXJPcHRpb25zID0ge1xuICAgIGF1dGhUcmFuc3BvcnQ6IGF1dGhPcHRpb25zLnRyYW5zcG9ydCxcbiAgICBhdXRoRW5kcG9pbnQ6IGF1dGhPcHRpb25zLmVuZHBvaW50LFxuICAgIGF1dGg6IHtcbiAgICAgIHBhcmFtczogYXV0aE9wdGlvbnMucGFyYW1zLFxuICAgICAgaGVhZGVyczogYXV0aE9wdGlvbnMuaGVhZGVyc1xuICAgIH1cbiAgfTtcbiAgcmV0dXJuIChcbiAgICBwYXJhbXM6IENoYW5uZWxBdXRob3JpemF0aW9uUmVxdWVzdFBhcmFtcyxcbiAgICBjYWxsYmFjazogQ2hhbm5lbEF1dGhvcml6YXRpb25DYWxsYmFja1xuICApID0+IHtcbiAgICBjb25zdCBjaGFubmVsID0gcHVzaGVyLmNoYW5uZWwocGFyYW1zLmNoYW5uZWxOYW1lKTtcbiAgICAvLyBUaGlzIGxpbmUgY3JlYXRlcyBhIG5ldyBjaGFubmVsIGF1dGhvcml6ZXIgZXZlcnkgdGltZS5cbiAgICAvLyBJbiB0aGUgcGFzdCwgdGhpcyB3YXMgb25seSBkb25lIG9uY2UgcGVyIGNoYW5uZWwgYW5kIHJldXNlZC5cbiAgICAvLyBXZSBjYW4gZG8gdGhhdCBhZ2FpbiBpZiB3ZSB3YW50IHRvIGtlZXAgdGhpcyBiZWhhdmlvciBpbnRhY3QuXG4gICAgY29uc3QgY2hhbm5lbEF1dGhvcml6ZXI6IERlcHJlY2F0ZWRDaGFubmVsQXV0aG9yaXplciA9IGNoYW5uZWxBdXRob3JpemVyR2VuZXJhdG9yKFxuICAgICAgY2hhbm5lbCxcbiAgICAgIGRlcHJlY2F0ZWRBdXRob3JpemVyT3B0aW9uc1xuICAgICk7XG4gICAgY2hhbm5lbEF1dGhvcml6ZXIuYXV0aG9yaXplKHBhcmFtcy5zb2NrZXRJZCwgY2FsbGJhY2spO1xuICB9O1xufTtcbiIsICJpbXBvcnQgeyBPcHRpb25zIH0gZnJvbSAnLi9vcHRpb25zJztcbmltcG9ydCBEZWZhdWx0cyBmcm9tICcuL2RlZmF1bHRzJztcbmltcG9ydCB7XG4gIENoYW5uZWxBdXRob3JpemF0aW9uSGFuZGxlcixcbiAgVXNlckF1dGhlbnRpY2F0aW9uSGFuZGxlcixcbiAgQ2hhbm5lbEF1dGhvcml6YXRpb25PcHRpb25zXG59IGZyb20gJy4vYXV0aC9vcHRpb25zJztcbmltcG9ydCBVc2VyQXV0aGVudGljYXRvciBmcm9tICcuL2F1dGgvdXNlcl9hdXRoZW50aWNhdG9yJztcbmltcG9ydCBDaGFubmVsQXV0aG9yaXplciBmcm9tICcuL2F1dGgvY2hhbm5lbF9hdXRob3JpemVyJztcbmltcG9ydCB7IENoYW5uZWxBdXRob3JpemVyUHJveHkgfSBmcm9tICcuL2F1dGgvZGVwcmVjYXRlZF9jaGFubmVsX2F1dGhvcml6ZXInO1xuaW1wb3J0IFJ1bnRpbWUgZnJvbSAncnVudGltZSc7XG5pbXBvcnQgKiBhcyBuYWNsIGZyb20gJ3R3ZWV0bmFjbCc7XG5pbXBvcnQgTG9nZ2VyIGZyb20gJy4vbG9nZ2VyJztcblxuZXhwb3J0IHR5cGUgQXV0aFRyYW5zcG9ydCA9ICdhamF4JyB8ICdqc29ucCc7XG5leHBvcnQgdHlwZSBUcmFuc3BvcnQgPVxuICB8ICd3cydcbiAgfCAnd3NzJ1xuICB8ICd4aHJfc3RyZWFtaW5nJ1xuICB8ICd4aHJfcG9sbGluZydcbiAgfCAnc29ja2pzJztcblxuZXhwb3J0IGludGVyZmFjZSBDb25maWcge1xuICAvLyB0aGVzZSBhcmUgYWxsICdyZXF1aXJlZCcgY29uZmlnIHBhcmFtZXRlcnMsIGl0J3Mgbm90IG5lY2Vzc2FyeSBmb3IgdGhlIHVzZXJcbiAgLy8gdG8gc2V0IHRoZW0sIGJ1dCB0aGV5IGhhdmUgY29uZmlndXJlZCBkZWZhdWx0cy5cbiAgYWN0aXZpdHlUaW1lb3V0OiBudW1iZXI7XG4gIGVuYWJsZVN0YXRzOiBib29sZWFuO1xuICBodHRwSG9zdDogc3RyaW5nO1xuICBodHRwUGF0aDogc3RyaW5nO1xuICBodHRwUG9ydDogbnVtYmVyO1xuICBodHRwc1BvcnQ6IG51bWJlcjtcbiAgcG9uZ1RpbWVvdXQ6IG51bWJlcjtcbiAgc3RhdHNIb3N0OiBzdHJpbmc7XG4gIHVuYXZhaWxhYmxlVGltZW91dDogbnVtYmVyO1xuICB1c2VUTFM6IGJvb2xlYW47XG4gIHdzSG9zdDogc3RyaW5nO1xuICB3c1BhdGg6IHN0cmluZztcbiAgd3NQb3J0OiBudW1iZXI7XG4gIHdzc1BvcnQ6IG51bWJlcjtcbiAgdXNlckF1dGhlbnRpY2F0b3I6IFVzZXJBdXRoZW50aWNhdGlvbkhhbmRsZXI7XG4gIGNoYW5uZWxBdXRob3JpemVyOiBDaGFubmVsQXV0aG9yaXphdGlvbkhhbmRsZXI7XG5cbiAgLy8gdGhlc2UgYXJlIGFsbCBvcHRpb25hbCBwYXJhbWV0ZXJzIG9yIG92ZXJycmlkZXMuIFRoZSBjdXN0b21lciBjYW4gc2V0IHRoZXNlXG4gIC8vIGJ1dCBpdCdzIG5vdCBzdHJpY3RseSBuZWNlc3NhcnlcbiAgZm9yY2VUTFM/OiBib29sZWFuO1xuICBjbHVzdGVyPzogc3RyaW5nO1xuICBkaXNhYmxlZFRyYW5zcG9ydHM/OiBUcmFuc3BvcnRbXTtcbiAgZW5hYmxlZFRyYW5zcG9ydHM/OiBUcmFuc3BvcnRbXTtcbiAgaWdub3JlTnVsbE9yaWdpbj86IGJvb2xlYW47XG4gIG5hY2w/OiBuYWNsO1xuICB0aW1lbGluZVBhcmFtcz86IGFueTtcbn1cblxuLy8gZ2V0Q29uZmlnIG1haW5seSBzZXRzIHRoZSBkZWZhdWx0cyBmb3IgdGhlIG9wdGlvbnMgdGhhdCBhcmUgbm90IHByb3ZpZGVkXG5leHBvcnQgZnVuY3Rpb24gZ2V0Q29uZmlnKG9wdHM6IE9wdGlvbnMsIHB1c2hlcik6IENvbmZpZyB7XG4gIGxldCBjb25maWc6IENvbmZpZyA9IHtcbiAgICBhY3Rpdml0eVRpbWVvdXQ6IG9wdHMuYWN0aXZpdHlUaW1lb3V0IHx8IERlZmF1bHRzLmFjdGl2aXR5VGltZW91dCxcbiAgICBjbHVzdGVyOiBvcHRzLmNsdXN0ZXIgfHwgRGVmYXVsdHMuY2x1c3RlcixcbiAgICBodHRwUGF0aDogb3B0cy5odHRwUGF0aCB8fCBEZWZhdWx0cy5odHRwUGF0aCxcbiAgICBodHRwUG9ydDogb3B0cy5odHRwUG9ydCB8fCBEZWZhdWx0cy5odHRwUG9ydCxcbiAgICBodHRwc1BvcnQ6IG9wdHMuaHR0cHNQb3J0IHx8IERlZmF1bHRzLmh0dHBzUG9ydCxcbiAgICBwb25nVGltZW91dDogb3B0cy5wb25nVGltZW91dCB8fCBEZWZhdWx0cy5wb25nVGltZW91dCxcbiAgICBzdGF0c0hvc3Q6IG9wdHMuc3RhdHNIb3N0IHx8IERlZmF1bHRzLnN0YXRzX2hvc3QsXG4gICAgdW5hdmFpbGFibGVUaW1lb3V0OiBvcHRzLnVuYXZhaWxhYmxlVGltZW91dCB8fCBEZWZhdWx0cy51bmF2YWlsYWJsZVRpbWVvdXQsXG4gICAgd3NQYXRoOiBvcHRzLndzUGF0aCB8fCBEZWZhdWx0cy53c1BhdGgsXG4gICAgd3NQb3J0OiBvcHRzLndzUG9ydCB8fCBEZWZhdWx0cy53c1BvcnQsXG4gICAgd3NzUG9ydDogb3B0cy53c3NQb3J0IHx8IERlZmF1bHRzLndzc1BvcnQsXG5cbiAgICBlbmFibGVTdGF0czogZ2V0RW5hYmxlU3RhdHNDb25maWcob3B0cyksXG4gICAgaHR0cEhvc3Q6IGdldEh0dHBIb3N0KG9wdHMpLFxuICAgIHVzZVRMUzogc2hvdWxkVXNlVExTKG9wdHMpLFxuICAgIHdzSG9zdDogZ2V0V2Vic29ja2V0SG9zdChvcHRzKSxcblxuICAgIHVzZXJBdXRoZW50aWNhdG9yOiBidWlsZFVzZXJBdXRoZW50aWNhdG9yKG9wdHMpLFxuICAgIGNoYW5uZWxBdXRob3JpemVyOiBidWlsZENoYW5uZWxBdXRob3JpemVyKG9wdHMsIHB1c2hlcilcbiAgfTtcblxuICBpZiAoJ2Rpc2FibGVkVHJhbnNwb3J0cycgaW4gb3B0cylcbiAgICBjb25maWcuZGlzYWJsZWRUcmFuc3BvcnRzID0gb3B0cy5kaXNhYmxlZFRyYW5zcG9ydHM7XG4gIGlmICgnZW5hYmxlZFRyYW5zcG9ydHMnIGluIG9wdHMpXG4gICAgY29uZmlnLmVuYWJsZWRUcmFuc3BvcnRzID0gb3B0cy5lbmFibGVkVHJhbnNwb3J0cztcbiAgaWYgKCdpZ25vcmVOdWxsT3JpZ2luJyBpbiBvcHRzKVxuICAgIGNvbmZpZy5pZ25vcmVOdWxsT3JpZ2luID0gb3B0cy5pZ25vcmVOdWxsT3JpZ2luO1xuICBpZiAoJ3RpbWVsaW5lUGFyYW1zJyBpbiBvcHRzKSBjb25maWcudGltZWxpbmVQYXJhbXMgPSBvcHRzLnRpbWVsaW5lUGFyYW1zO1xuICBpZiAoJ25hY2wnIGluIG9wdHMpIHtcbiAgICBjb25maWcubmFjbCA9IG9wdHMubmFjbDtcbiAgfVxuXG4gIHJldHVybiBjb25maWc7XG59XG5cbmZ1bmN0aW9uIGdldEh0dHBIb3N0KG9wdHM6IE9wdGlvbnMpOiBzdHJpbmcge1xuICBpZiAob3B0cy5odHRwSG9zdCkge1xuICAgIHJldHVybiBvcHRzLmh0dHBIb3N0O1xuICB9XG4gIGlmIChvcHRzLmNsdXN0ZXIpIHtcbiAgICByZXR1cm4gYHNvY2tqcy0ke29wdHMuY2x1c3Rlcn0ucHVzaGVyLmNvbWA7XG4gIH1cbiAgcmV0dXJuIERlZmF1bHRzLmh0dHBIb3N0O1xufVxuXG5mdW5jdGlvbiBnZXRXZWJzb2NrZXRIb3N0KG9wdHM6IE9wdGlvbnMpOiBzdHJpbmcge1xuICBpZiAob3B0cy53c0hvc3QpIHtcbiAgICByZXR1cm4gb3B0cy53c0hvc3Q7XG4gIH1cbiAgaWYgKG9wdHMuY2x1c3Rlcikge1xuICAgIHJldHVybiBnZXRXZWJzb2NrZXRIb3N0RnJvbUNsdXN0ZXIob3B0cy5jbHVzdGVyKTtcbiAgfVxuICByZXR1cm4gZ2V0V2Vic29ja2V0SG9zdEZyb21DbHVzdGVyKERlZmF1bHRzLmNsdXN0ZXIpO1xufVxuXG5mdW5jdGlvbiBnZXRXZWJzb2NrZXRIb3N0RnJvbUNsdXN0ZXIoY2x1c3Rlcjogc3RyaW5nKTogc3RyaW5nIHtcbiAgcmV0dXJuIGB3cy0ke2NsdXN0ZXJ9LnB1c2hlci5jb21gO1xufVxuXG5mdW5jdGlvbiBzaG91bGRVc2VUTFMob3B0czogT3B0aW9ucyk6IGJvb2xlYW4ge1xuICBpZiAoUnVudGltZS5nZXRQcm90b2NvbCgpID09PSAnaHR0cHM6Jykge1xuICAgIHJldHVybiB0cnVlO1xuICB9IGVsc2UgaWYgKG9wdHMuZm9yY2VUTFMgPT09IGZhbHNlKSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG4gIHJldHVybiB0cnVlO1xufVxuXG4vLyBpZiBlbmFibGVTdGF0cyBpcyBzZXQgdGFrZSB0aGUgdmFsdWVcbi8vIGlmIGRpc2FibGVTdGF0cyBpcyBzZXQgdGFrZSB0aGUgaW52ZXJzZVxuLy8gb3RoZXJ3aXNlIGRlZmF1bHQgdG8gZmFsc2VcbmZ1bmN0aW9uIGdldEVuYWJsZVN0YXRzQ29uZmlnKG9wdHM6IE9wdGlvbnMpOiBib29sZWFuIHtcbiAgaWYgKCdlbmFibGVTdGF0cycgaW4gb3B0cykge1xuICAgIHJldHVybiBvcHRzLmVuYWJsZVN0YXRzO1xuICB9XG4gIGlmICgnZGlzYWJsZVN0YXRzJyBpbiBvcHRzKSB7XG4gICAgcmV0dXJuICFvcHRzLmRpc2FibGVTdGF0cztcbiAgfVxuICByZXR1cm4gZmFsc2U7XG59XG5cbmZ1bmN0aW9uIGJ1aWxkVXNlckF1dGhlbnRpY2F0b3Iob3B0czogT3B0aW9ucyk6IFVzZXJBdXRoZW50aWNhdGlvbkhhbmRsZXIge1xuICBjb25zdCB1c2VyQXV0aGVudGljYXRpb24gPSB7XG4gICAgLi4uRGVmYXVsdHMudXNlckF1dGhlbnRpY2F0aW9uLFxuICAgIC4uLm9wdHMudXNlckF1dGhlbnRpY2F0aW9uXG4gIH07XG4gIGlmIChcbiAgICAnY3VzdG9tSGFuZGxlcicgaW4gdXNlckF1dGhlbnRpY2F0aW9uICYmXG4gICAgdXNlckF1dGhlbnRpY2F0aW9uWydjdXN0b21IYW5kbGVyJ10gIT0gbnVsbFxuICApIHtcbiAgICByZXR1cm4gdXNlckF1dGhlbnRpY2F0aW9uWydjdXN0b21IYW5kbGVyJ107XG4gIH1cblxuICByZXR1cm4gVXNlckF1dGhlbnRpY2F0b3IodXNlckF1dGhlbnRpY2F0aW9uKTtcbn1cblxuZnVuY3Rpb24gYnVpbGRDaGFubmVsQXV0aChvcHRzOiBPcHRpb25zLCBwdXNoZXIpOiBDaGFubmVsQXV0aG9yaXphdGlvbk9wdGlvbnMge1xuICBsZXQgY2hhbm5lbEF1dGhvcml6YXRpb246IENoYW5uZWxBdXRob3JpemF0aW9uT3B0aW9ucztcbiAgaWYgKCdjaGFubmVsQXV0aG9yaXphdGlvbicgaW4gb3B0cykge1xuICAgIGNoYW5uZWxBdXRob3JpemF0aW9uID0ge1xuICAgICAgLi4uRGVmYXVsdHMuY2hhbm5lbEF1dGhvcml6YXRpb24sXG4gICAgICAuLi5vcHRzLmNoYW5uZWxBdXRob3JpemF0aW9uXG4gICAgfTtcbiAgfSBlbHNlIHtcbiAgICBjaGFubmVsQXV0aG9yaXphdGlvbiA9IHtcbiAgICAgIHRyYW5zcG9ydDogb3B0cy5hdXRoVHJhbnNwb3J0IHx8IERlZmF1bHRzLmF1dGhUcmFuc3BvcnQsXG4gICAgICBlbmRwb2ludDogb3B0cy5hdXRoRW5kcG9pbnQgfHwgRGVmYXVsdHMuYXV0aEVuZHBvaW50XG4gICAgfTtcbiAgICBpZiAoJ2F1dGgnIGluIG9wdHMpIHtcbiAgICAgIGlmICgncGFyYW1zJyBpbiBvcHRzLmF1dGgpIGNoYW5uZWxBdXRob3JpemF0aW9uLnBhcmFtcyA9IG9wdHMuYXV0aC5wYXJhbXM7XG4gICAgICBpZiAoJ2hlYWRlcnMnIGluIG9wdHMuYXV0aClcbiAgICAgICAgY2hhbm5lbEF1dGhvcml6YXRpb24uaGVhZGVycyA9IG9wdHMuYXV0aC5oZWFkZXJzO1xuICAgIH1cbiAgICBpZiAoJ2F1dGhvcml6ZXInIGluIG9wdHMpXG4gICAgICBjaGFubmVsQXV0aG9yaXphdGlvbi5jdXN0b21IYW5kbGVyID0gQ2hhbm5lbEF1dGhvcml6ZXJQcm94eShcbiAgICAgICAgcHVzaGVyLFxuICAgICAgICBjaGFubmVsQXV0aG9yaXphdGlvbixcbiAgICAgICAgb3B0cy5hdXRob3JpemVyXG4gICAgICApO1xuICB9XG4gIHJldHVybiBjaGFubmVsQXV0aG9yaXphdGlvbjtcbn1cblxuZnVuY3Rpb24gYnVpbGRDaGFubmVsQXV0aG9yaXplcihcbiAgb3B0czogT3B0aW9ucyxcbiAgcHVzaGVyXG4pOiBDaGFubmVsQXV0aG9yaXphdGlvbkhhbmRsZXIge1xuICBjb25zdCBjaGFubmVsQXV0aG9yaXphdGlvbiA9IGJ1aWxkQ2hhbm5lbEF1dGgob3B0cywgcHVzaGVyKTtcbiAgaWYgKFxuICAgICdjdXN0b21IYW5kbGVyJyBpbiBjaGFubmVsQXV0aG9yaXphdGlvbiAmJlxuICAgIGNoYW5uZWxBdXRob3JpemF0aW9uWydjdXN0b21IYW5kbGVyJ10gIT0gbnVsbFxuICApIHtcbiAgICByZXR1cm4gY2hhbm5lbEF1dGhvcml6YXRpb25bJ2N1c3RvbUhhbmRsZXInXTtcbiAgfVxuXG4gIHJldHVybiBDaGFubmVsQXV0aG9yaXplcihjaGFubmVsQXV0aG9yaXphdGlvbik7XG59XG4iLCAiaW1wb3J0IExvZ2dlciBmcm9tICcuL2xvZ2dlcic7XG5pbXBvcnQgUHVzaGVyIGZyb20gJy4vcHVzaGVyJztcbmltcG9ydCBFdmVudHNEaXNwYXRjaGVyIGZyb20gJy4vZXZlbnRzL2Rpc3BhdGNoZXInO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBXYXRjaGxpc3RGYWNhZGUgZXh0ZW5kcyBFdmVudHNEaXNwYXRjaGVyIHtcbiAgcHJpdmF0ZSBwdXNoZXI6IFB1c2hlcjtcblxuICBwdWJsaWMgY29uc3RydWN0b3IocHVzaGVyOiBQdXNoZXIpIHtcbiAgICBzdXBlcihmdW5jdGlvbihldmVudE5hbWUsIGRhdGEpIHtcbiAgICAgIExvZ2dlci5kZWJ1ZyhgTm8gY2FsbGJhY2tzIG9uIHdhdGNobGlzdCBldmVudHMgZm9yICR7ZXZlbnROYW1lfWApO1xuICAgIH0pO1xuXG4gICAgdGhpcy5wdXNoZXIgPSBwdXNoZXI7XG4gICAgdGhpcy5iaW5kV2F0Y2hsaXN0SW50ZXJuYWxFdmVudCgpO1xuICB9XG5cbiAgaGFuZGxlRXZlbnQocHVzaGVyRXZlbnQpIHtcbiAgICBwdXNoZXJFdmVudC5kYXRhLmV2ZW50cy5mb3JFYWNoKHdhdGNobGlzdEV2ZW50ID0+IHtcbiAgICAgIHRoaXMuZW1pdCh3YXRjaGxpc3RFdmVudC5uYW1lLCB3YXRjaGxpc3RFdmVudCk7XG4gICAgfSk7XG4gIH1cblxuICBwcml2YXRlIGJpbmRXYXRjaGxpc3RJbnRlcm5hbEV2ZW50KCkge1xuICAgIHRoaXMucHVzaGVyLmNvbm5lY3Rpb24uYmluZCgnbWVzc2FnZScsIHB1c2hlckV2ZW50ID0+IHtcbiAgICAgIHZhciBldmVudE5hbWUgPSBwdXNoZXJFdmVudC5ldmVudDtcbiAgICAgIGlmIChldmVudE5hbWUgPT09ICdwdXNoZXJfaW50ZXJuYWw6d2F0Y2hsaXN0X2V2ZW50cycpIHtcbiAgICAgICAgdGhpcy5oYW5kbGVFdmVudChwdXNoZXJFdmVudCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cbn1cbiIsICJmdW5jdGlvbiBmbGF0UHJvbWlzZSgpIHtcbiAgbGV0IHJlc29sdmUsIHJlamVjdDtcbiAgY29uc3QgcHJvbWlzZSA9IG5ldyBQcm9taXNlKChyZXMsIHJlaikgPT4ge1xuICAgIHJlc29sdmUgPSByZXM7XG4gICAgcmVqZWN0ID0gcmVqO1xuICB9KTtcbiAgcmV0dXJuIHsgcHJvbWlzZSwgcmVzb2x2ZSwgcmVqZWN0IH07XG59XG5cbmV4cG9ydCBkZWZhdWx0IGZsYXRQcm9taXNlO1xuIiwgImltcG9ydCBQdXNoZXIgZnJvbSAnLi9wdXNoZXInO1xuaW1wb3J0IExvZ2dlciBmcm9tICcuL2xvZ2dlcic7XG5pbXBvcnQge1xuICBVc2VyQXV0aGVudGljYXRpb25EYXRhLFxuICBVc2VyQXV0aGVudGljYXRpb25DYWxsYmFja1xufSBmcm9tICcuL2F1dGgvb3B0aW9ucyc7XG5pbXBvcnQgQ2hhbm5lbCBmcm9tICcuL2NoYW5uZWxzL2NoYW5uZWwnO1xuaW1wb3J0IFdhdGNobGlzdEZhY2FkZSBmcm9tICcuL3dhdGNobGlzdCc7XG5pbXBvcnQgRXZlbnRzRGlzcGF0Y2hlciBmcm9tICcuL2V2ZW50cy9kaXNwYXRjaGVyJztcbmltcG9ydCBmbGF0UHJvbWlzZSBmcm9tICcuL3V0aWxzL2ZsYXRfcHJvbWlzZSc7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFVzZXJGYWNhZGUgZXh0ZW5kcyBFdmVudHNEaXNwYXRjaGVyIHtcbiAgcHVzaGVyOiBQdXNoZXI7XG4gIHNpZ25pbl9yZXF1ZXN0ZWQ6IGJvb2xlYW4gPSBmYWxzZTtcbiAgdXNlcl9kYXRhOiBhbnkgPSBudWxsO1xuICBzZXJ2ZXJUb1VzZXJDaGFubmVsOiBDaGFubmVsID0gbnVsbDtcbiAgc2lnbmluRG9uZVByb21pc2U6IFByb21pc2U8YW55PiA9IG51bGw7XG4gIHdhdGNobGlzdDogV2F0Y2hsaXN0RmFjYWRlO1xuICBwcml2YXRlIF9zaWduaW5Eb25lUmVzb2x2ZTogRnVuY3Rpb24gPSBudWxsO1xuXG4gIHB1YmxpYyBjb25zdHJ1Y3RvcihwdXNoZXI6IFB1c2hlcikge1xuICAgIHN1cGVyKGZ1bmN0aW9uKGV2ZW50TmFtZSwgZGF0YSkge1xuICAgICAgTG9nZ2VyLmRlYnVnKCdObyBjYWxsYmFja3Mgb24gdXNlciBmb3IgJyArIGV2ZW50TmFtZSk7XG4gICAgfSk7XG4gICAgdGhpcy5wdXNoZXIgPSBwdXNoZXI7XG4gICAgdGhpcy5wdXNoZXIuY29ubmVjdGlvbi5iaW5kKCdzdGF0ZV9jaGFuZ2UnLCAoeyBwcmV2aW91cywgY3VycmVudCB9KSA9PiB7XG4gICAgICBpZiAocHJldmlvdXMgIT09ICdjb25uZWN0ZWQnICYmIGN1cnJlbnQgPT09ICdjb25uZWN0ZWQnKSB7XG4gICAgICAgIHRoaXMuX3NpZ25pbigpO1xuICAgICAgfVxuICAgICAgaWYgKHByZXZpb3VzID09PSAnY29ubmVjdGVkJyAmJiBjdXJyZW50ICE9PSAnY29ubmVjdGVkJykge1xuICAgICAgICB0aGlzLl9jbGVhbnVwKCk7XG4gICAgICAgIHRoaXMuX25ld1NpZ25pblByb21pc2VJZk5lZWRlZCgpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgdGhpcy53YXRjaGxpc3QgPSBuZXcgV2F0Y2hsaXN0RmFjYWRlKHB1c2hlcik7XG5cbiAgICB0aGlzLnB1c2hlci5jb25uZWN0aW9uLmJpbmQoJ21lc3NhZ2UnLCBldmVudCA9PiB7XG4gICAgICB2YXIgZXZlbnROYW1lID0gZXZlbnQuZXZlbnQ7XG4gICAgICBpZiAoZXZlbnROYW1lID09PSAncHVzaGVyOnNpZ25pbl9zdWNjZXNzJykge1xuICAgICAgICB0aGlzLl9vblNpZ25pblN1Y2Nlc3MoZXZlbnQuZGF0YSk7XG4gICAgICB9XG4gICAgICBpZiAoXG4gICAgICAgIHRoaXMuc2VydmVyVG9Vc2VyQ2hhbm5lbCAmJlxuICAgICAgICB0aGlzLnNlcnZlclRvVXNlckNoYW5uZWwubmFtZSA9PT0gZXZlbnQuY2hhbm5lbFxuICAgICAgKSB7XG4gICAgICAgIHRoaXMuc2VydmVyVG9Vc2VyQ2hhbm5lbC5oYW5kbGVFdmVudChldmVudCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICBwdWJsaWMgc2lnbmluKCkge1xuICAgIGlmICh0aGlzLnNpZ25pbl9yZXF1ZXN0ZWQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLnNpZ25pbl9yZXF1ZXN0ZWQgPSB0cnVlO1xuICAgIHRoaXMuX3NpZ25pbigpO1xuICB9XG5cbiAgcHJpdmF0ZSBfc2lnbmluKCkge1xuICAgIGlmICghdGhpcy5zaWduaW5fcmVxdWVzdGVkKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5fbmV3U2lnbmluUHJvbWlzZUlmTmVlZGVkKCk7XG5cbiAgICBpZiAodGhpcy5wdXNoZXIuY29ubmVjdGlvbi5zdGF0ZSAhPT0gJ2Nvbm5lY3RlZCcpIHtcbiAgICAgIC8vIFNpZ25pbiB3aWxsIGJlIGF0dGVtcHRlZCB3aGVuIHRoZSBjb25uZWN0aW9uIGlzIGNvbm5lY3RlZFxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMucHVzaGVyLmNvbmZpZy51c2VyQXV0aGVudGljYXRvcihcbiAgICAgIHtcbiAgICAgICAgc29ja2V0SWQ6IHRoaXMucHVzaGVyLmNvbm5lY3Rpb24uc29ja2V0X2lkXG4gICAgICB9LFxuICAgICAgdGhpcy5fb25BdXRob3JpemVcbiAgICApO1xuICB9XG5cbiAgcHJpdmF0ZSBfb25BdXRob3JpemU6IFVzZXJBdXRoZW50aWNhdGlvbkNhbGxiYWNrID0gKFxuICAgIGVycixcbiAgICBhdXRoRGF0YTogVXNlckF1dGhlbnRpY2F0aW9uRGF0YVxuICApID0+IHtcbiAgICBpZiAoZXJyKSB7XG4gICAgICBMb2dnZXIud2FybihgRXJyb3IgZHVyaW5nIHNpZ25pbjogJHtlcnJ9YCk7XG4gICAgICB0aGlzLl9jbGVhbnVwKCk7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5wdXNoZXIuc2VuZF9ldmVudCgncHVzaGVyOnNpZ25pbicsIHtcbiAgICAgIGF1dGg6IGF1dGhEYXRhLmF1dGgsXG4gICAgICB1c2VyX2RhdGE6IGF1dGhEYXRhLnVzZXJfZGF0YVxuICAgIH0pO1xuXG4gICAgLy8gTGF0ZXIgd2hlbiB3ZSBnZXQgcHVzaGVyOnNpbmdpbl9zdWNjZXNzIGV2ZW50LCB0aGUgdXNlciB3aWxsIGJlIG1hcmtlZCBhcyBzaWduZWQgaW5cbiAgfTtcblxuICBwcml2YXRlIF9vblNpZ25pblN1Y2Nlc3MoZGF0YTogYW55KSB7XG4gICAgdHJ5IHtcbiAgICAgIHRoaXMudXNlcl9kYXRhID0gSlNPTi5wYXJzZShkYXRhLnVzZXJfZGF0YSk7XG4gICAgfSBjYXRjaCAoZSkge1xuICAgICAgTG9nZ2VyLmVycm9yKGBGYWlsZWQgcGFyc2luZyB1c2VyIGRhdGEgYWZ0ZXIgc2lnbmluOiAke2RhdGEudXNlcl9kYXRhfWApO1xuICAgICAgdGhpcy5fY2xlYW51cCgpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy51c2VyX2RhdGEuaWQgIT09ICdzdHJpbmcnIHx8IHRoaXMudXNlcl9kYXRhLmlkID09PSAnJykge1xuICAgICAgTG9nZ2VyLmVycm9yKFxuICAgICAgICBgdXNlcl9kYXRhIGRvZXNuJ3QgY29udGFpbiBhbiBpZC4gdXNlcl9kYXRhOiAke3RoaXMudXNlcl9kYXRhfWBcbiAgICAgICk7XG4gICAgICB0aGlzLl9jbGVhbnVwKCk7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy8gU2lnbmluIHN1Y2NlZWRlZFxuICAgIHRoaXMuX3NpZ25pbkRvbmVSZXNvbHZlKCk7XG4gICAgdGhpcy5fc3Vic2NyaWJlQ2hhbm5lbHMoKTtcbiAgfVxuXG4gIHByaXZhdGUgX3N1YnNjcmliZUNoYW5uZWxzKCkge1xuICAgIGNvbnN0IGVuc3VyZV9zdWJzY3JpYmVkID0gY2hhbm5lbCA9PiB7XG4gICAgICBpZiAoY2hhbm5lbC5zdWJzY3JpcHRpb25QZW5kaW5nICYmIGNoYW5uZWwuc3Vic2NyaXB0aW9uQ2FuY2VsbGVkKSB7XG4gICAgICAgIGNoYW5uZWwucmVpbnN0YXRlU3Vic2NyaXB0aW9uKCk7XG4gICAgICB9IGVsc2UgaWYgKFxuICAgICAgICAhY2hhbm5lbC5zdWJzY3JpcHRpb25QZW5kaW5nICYmXG4gICAgICAgIHRoaXMucHVzaGVyLmNvbm5lY3Rpb24uc3RhdGUgPT09ICdjb25uZWN0ZWQnXG4gICAgICApIHtcbiAgICAgICAgY2hhbm5lbC5zdWJzY3JpYmUoKTtcbiAgICAgIH1cbiAgICB9O1xuXG4gICAgdGhpcy5zZXJ2ZXJUb1VzZXJDaGFubmVsID0gbmV3IENoYW5uZWwoXG4gICAgICBgI3NlcnZlci10by11c2VyLSR7dGhpcy51c2VyX2RhdGEuaWR9YCxcbiAgICAgIHRoaXMucHVzaGVyXG4gICAgKTtcbiAgICB0aGlzLnNlcnZlclRvVXNlckNoYW5uZWwuYmluZF9nbG9iYWwoKGV2ZW50TmFtZSwgZGF0YSkgPT4ge1xuICAgICAgaWYgKFxuICAgICAgICBldmVudE5hbWUuaW5kZXhPZigncHVzaGVyX2ludGVybmFsOicpID09PSAwIHx8XG4gICAgICAgIGV2ZW50TmFtZS5pbmRleE9mKCdwdXNoZXI6JykgPT09IDBcbiAgICAgICkge1xuICAgICAgICAvLyBpZ25vcmUgaW50ZXJuYWwgZXZlbnRzXG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIHRoaXMuZW1pdChldmVudE5hbWUsIGRhdGEpO1xuICAgIH0pO1xuICAgIGVuc3VyZV9zdWJzY3JpYmVkKHRoaXMuc2VydmVyVG9Vc2VyQ2hhbm5lbCk7XG4gIH1cblxuICBwcml2YXRlIF9jbGVhbnVwKCkge1xuICAgIHRoaXMudXNlcl9kYXRhID0gbnVsbDtcbiAgICBpZiAodGhpcy5zZXJ2ZXJUb1VzZXJDaGFubmVsKSB7XG4gICAgICB0aGlzLnNlcnZlclRvVXNlckNoYW5uZWwudW5iaW5kX2FsbCgpO1xuICAgICAgdGhpcy5zZXJ2ZXJUb1VzZXJDaGFubmVsLmRpc2Nvbm5lY3QoKTtcbiAgICAgIHRoaXMuc2VydmVyVG9Vc2VyQ2hhbm5lbCA9IG51bGw7XG4gICAgfVxuXG4gICAgaWYgKHRoaXMuc2lnbmluX3JlcXVlc3RlZCkge1xuICAgICAgLy8gSWYgc2lnbmluIGlzIGluIHByb2dyZXNzIGFuZCBjbGVhbnVwIGlzIGNhbGxlZCxcbiAgICAgIC8vIE1hcmsgdGhlIGN1cnJlbnQgc2lnbmluIHByb2Nlc3MgYXMgZG9uZS5cbiAgICAgIHRoaXMuX3NpZ25pbkRvbmVSZXNvbHZlKCk7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBfbmV3U2lnbmluUHJvbWlzZUlmTmVlZGVkKCkge1xuICAgIGlmICghdGhpcy5zaWduaW5fcmVxdWVzdGVkKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy8gSWYgdGhlcmUgaXMgYSBwcm9taXNlIGFuZCBpdCBpcyBub3QgcmVzb2x2ZWQsIHJldHVybiB3aXRob3V0IGNyZWF0aW5nIGEgbmV3IG9uZS5cbiAgICBpZiAodGhpcy5zaWduaW5Eb25lUHJvbWlzZSAmJiAhKHRoaXMuc2lnbmluRG9uZVByb21pc2UgYXMgYW55KS5kb25lKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy8gVGhpcyBwcm9taXNlIGlzIG5ldmVyIHJlamVjdGVkLlxuICAgIC8vIEl0IGdldHMgcmVzb2x2ZWQgd2hlbiB0aGUgc2lnbmluIHByb2Nlc3MgaXMgZG9uZSB3aGV0aGVyIGl0IGZhaWxlZCBvciBzdWNjZWVkZWRcbiAgICBjb25zdCB7IHByb21pc2UsIHJlc29sdmUsIHJlamVjdDogXyB9ID0gZmxhdFByb21pc2UoKTtcbiAgICAocHJvbWlzZSBhcyBhbnkpLmRvbmUgPSBmYWxzZTtcbiAgICBjb25zdCBzZXREb25lID0gKCkgPT4ge1xuICAgICAgKHByb21pc2UgYXMgYW55KS5kb25lID0gdHJ1ZTtcbiAgICB9O1xuICAgIHByb21pc2UudGhlbihzZXREb25lKS5jYXRjaChzZXREb25lKTtcbiAgICB0aGlzLnNpZ25pbkRvbmVQcm9taXNlID0gcHJvbWlzZTtcbiAgICB0aGlzLl9zaWduaW5Eb25lUmVzb2x2ZSA9IHJlc29sdmU7XG4gIH1cbn1cbiIsICJpbXBvcnQgQWJzdHJhY3RSdW50aW1lIGZyb20gJy4uL3J1bnRpbWVzL2ludGVyZmFjZSc7XG5pbXBvcnQgUnVudGltZSBmcm9tICdydW50aW1lJztcbmltcG9ydCBVdGlsIGZyb20gJy4vdXRpbCc7XG5pbXBvcnQgKiBhcyBDb2xsZWN0aW9ucyBmcm9tICcuL3V0aWxzL2NvbGxlY3Rpb25zJztcbmltcG9ydCBDaGFubmVscyBmcm9tICcuL2NoYW5uZWxzL2NoYW5uZWxzJztcbmltcG9ydCBDaGFubmVsIGZyb20gJy4vY2hhbm5lbHMvY2hhbm5lbCc7XG5pbXBvcnQgeyBkZWZhdWx0IGFzIEV2ZW50c0Rpc3BhdGNoZXIgfSBmcm9tICcuL2V2ZW50cy9kaXNwYXRjaGVyJztcbmltcG9ydCBUaW1lbGluZSBmcm9tICcuL3RpbWVsaW5lL3RpbWVsaW5lJztcbmltcG9ydCBUaW1lbGluZVNlbmRlciBmcm9tICcuL3RpbWVsaW5lL3RpbWVsaW5lX3NlbmRlcic7XG5pbXBvcnQgVGltZWxpbmVMZXZlbCBmcm9tICcuL3RpbWVsaW5lL2xldmVsJztcbmltcG9ydCB7IGRlZmluZVRyYW5zcG9ydCB9IGZyb20gJy4vc3RyYXRlZ2llcy9zdHJhdGVneV9idWlsZGVyJztcbmltcG9ydCBDb25uZWN0aW9uTWFuYWdlciBmcm9tICcuL2Nvbm5lY3Rpb24vY29ubmVjdGlvbl9tYW5hZ2VyJztcbmltcG9ydCBDb25uZWN0aW9uTWFuYWdlck9wdGlvbnMgZnJvbSAnLi9jb25uZWN0aW9uL2Nvbm5lY3Rpb25fbWFuYWdlcl9vcHRpb25zJztcbmltcG9ydCB7IFBlcmlvZGljVGltZXIgfSBmcm9tICcuL3V0aWxzL3RpbWVycyc7XG5pbXBvcnQgRGVmYXVsdHMgZnJvbSAnLi9kZWZhdWx0cyc7XG5pbXBvcnQgKiBhcyBEZWZhdWx0Q29uZmlnIGZyb20gJy4vY29uZmlnJztcbmltcG9ydCBMb2dnZXIgZnJvbSAnLi9sb2dnZXInO1xuaW1wb3J0IEZhY3RvcnkgZnJvbSAnLi91dGlscy9mYWN0b3J5JztcbmltcG9ydCBVcmxTdG9yZSBmcm9tICdjb3JlL3V0aWxzL3VybF9zdG9yZSc7XG5pbXBvcnQgeyBPcHRpb25zIH0gZnJvbSAnLi9vcHRpb25zJztcbmltcG9ydCB7IENvbmZpZywgZ2V0Q29uZmlnIH0gZnJvbSAnLi9jb25maWcnO1xuaW1wb3J0IFN0cmF0ZWd5T3B0aW9ucyBmcm9tICcuL3N0cmF0ZWdpZXMvc3RyYXRlZ3lfb3B0aW9ucyc7XG5pbXBvcnQgVXNlckZhY2FkZSBmcm9tICcuL3VzZXInO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBQdXNoZXIge1xuICAvKiAgU1RBVElDIFBST1BFUlRJRVMgKi9cbiAgc3RhdGljIGluc3RhbmNlczogUHVzaGVyW10gPSBbXTtcbiAgc3RhdGljIGlzUmVhZHk6IGJvb2xlYW4gPSBmYWxzZTtcbiAgc3RhdGljIGxvZ1RvQ29uc29sZTogYm9vbGVhbiA9IGZhbHNlO1xuXG4gIC8vIGZvciBqc29ucFxuICBzdGF0aWMgUnVudGltZTogQWJzdHJhY3RSdW50aW1lID0gUnVudGltZTtcbiAgc3RhdGljIFNjcmlwdFJlY2VpdmVyczogYW55ID0gKDxhbnk+UnVudGltZSkuU2NyaXB0UmVjZWl2ZXJzO1xuICBzdGF0aWMgRGVwZW5kZW5jaWVzUmVjZWl2ZXJzOiBhbnkgPSAoPGFueT5SdW50aW1lKS5EZXBlbmRlbmNpZXNSZWNlaXZlcnM7XG4gIHN0YXRpYyBhdXRoX2NhbGxiYWNrczogYW55ID0gKDxhbnk+UnVudGltZSkuYXV0aF9jYWxsYmFja3M7XG5cbiAgc3RhdGljIHJlYWR5KCkge1xuICAgIFB1c2hlci5pc1JlYWR5ID0gdHJ1ZTtcbiAgICBmb3IgKHZhciBpID0gMCwgbCA9IFB1c2hlci5pbnN0YW5jZXMubGVuZ3RoOyBpIDwgbDsgaSsrKSB7XG4gICAgICBQdXNoZXIuaW5zdGFuY2VzW2ldLmNvbm5lY3QoKTtcbiAgICB9XG4gIH1cblxuICBzdGF0aWMgbG9nOiAobWVzc2FnZTogYW55KSA9PiB2b2lkO1xuXG4gIHByaXZhdGUgc3RhdGljIGdldENsaWVudEZlYXR1cmVzKCk6IHN0cmluZ1tdIHtcbiAgICByZXR1cm4gQ29sbGVjdGlvbnMua2V5cyhcbiAgICAgIENvbGxlY3Rpb25zLmZpbHRlck9iamVjdCh7IHdzOiBSdW50aW1lLlRyYW5zcG9ydHMud3MgfSwgZnVuY3Rpb24odCkge1xuICAgICAgICByZXR1cm4gdC5pc1N1cHBvcnRlZCh7fSk7XG4gICAgICB9KVxuICAgICk7XG4gIH1cblxuICAvKiBJTlNUQU5DRSBQUk9QRVJUSUVTICovXG4gIGtleTogc3RyaW5nO1xuICBjb25maWc6IENvbmZpZztcbiAgY2hhbm5lbHM6IENoYW5uZWxzO1xuICBnbG9iYWxfZW1pdHRlcjogRXZlbnRzRGlzcGF0Y2hlcjtcbiAgc2Vzc2lvbklEOiBudW1iZXI7XG4gIHRpbWVsaW5lOiBUaW1lbGluZTtcbiAgdGltZWxpbmVTZW5kZXI6IFRpbWVsaW5lU2VuZGVyO1xuICBjb25uZWN0aW9uOiBDb25uZWN0aW9uTWFuYWdlcjtcbiAgdGltZWxpbmVTZW5kZXJUaW1lcjogUGVyaW9kaWNUaW1lcjtcbiAgdXNlcjogVXNlckZhY2FkZTtcbiAgY29uc3RydWN0b3IoYXBwX2tleTogc3RyaW5nLCBvcHRpb25zPzogT3B0aW9ucykge1xuICAgIGNoZWNrQXBwS2V5KGFwcF9rZXkpO1xuICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuICAgIGlmICghb3B0aW9ucy5jbHVzdGVyICYmICEob3B0aW9ucy53c0hvc3QgfHwgb3B0aW9ucy5odHRwSG9zdCkpIHtcbiAgICAgIGxldCBzdWZmaXggPSBVcmxTdG9yZS5idWlsZExvZ1N1ZmZpeCgnamF2YXNjcmlwdFF1aWNrU3RhcnQnKTtcbiAgICAgIExvZ2dlci53YXJuKFxuICAgICAgICBgWW91IHNob3VsZCBhbHdheXMgc3BlY2lmeSBhIGNsdXN0ZXIgd2hlbiBjb25uZWN0aW5nLiAke3N1ZmZpeH1gXG4gICAgICApO1xuICAgIH1cbiAgICBpZiAoJ2Rpc2FibGVTdGF0cycgaW4gb3B0aW9ucykge1xuICAgICAgTG9nZ2VyLndhcm4oXG4gICAgICAgICdUaGUgZGlzYWJsZVN0YXRzIG9wdGlvbiBpcyBkZXByZWNhdGVkIGluIGZhdm9yIG9mIGVuYWJsZVN0YXRzJ1xuICAgICAgKTtcbiAgICB9XG5cbiAgICB0aGlzLmtleSA9IGFwcF9rZXk7XG4gICAgdGhpcy5jb25maWcgPSBnZXRDb25maWcob3B0aW9ucywgdGhpcyk7XG5cbiAgICB0aGlzLmNoYW5uZWxzID0gRmFjdG9yeS5jcmVhdGVDaGFubmVscygpO1xuICAgIHRoaXMuZ2xvYmFsX2VtaXR0ZXIgPSBuZXcgRXZlbnRzRGlzcGF0Y2hlcigpO1xuICAgIHRoaXMuc2Vzc2lvbklEID0gUnVudGltZS5yYW5kb21JbnQoMTAwMDAwMDAwMCk7XG5cbiAgICB0aGlzLnRpbWVsaW5lID0gbmV3IFRpbWVsaW5lKHRoaXMua2V5LCB0aGlzLnNlc3Npb25JRCwge1xuICAgICAgY2x1c3RlcjogdGhpcy5jb25maWcuY2x1c3RlcixcbiAgICAgIGZlYXR1cmVzOiBQdXNoZXIuZ2V0Q2xpZW50RmVhdHVyZXMoKSxcbiAgICAgIHBhcmFtczogdGhpcy5jb25maWcudGltZWxpbmVQYXJhbXMgfHwge30sXG4gICAgICBsaW1pdDogNTAsXG4gICAgICBsZXZlbDogVGltZWxpbmVMZXZlbC5JTkZPLFxuICAgICAgdmVyc2lvbjogRGVmYXVsdHMuVkVSU0lPTlxuICAgIH0pO1xuICAgIGlmICh0aGlzLmNvbmZpZy5lbmFibGVTdGF0cykge1xuICAgICAgdGhpcy50aW1lbGluZVNlbmRlciA9IEZhY3RvcnkuY3JlYXRlVGltZWxpbmVTZW5kZXIodGhpcy50aW1lbGluZSwge1xuICAgICAgICBob3N0OiB0aGlzLmNvbmZpZy5zdGF0c0hvc3QsXG4gICAgICAgIHBhdGg6ICcvdGltZWxpbmUvdjIvJyArIFJ1bnRpbWUuVGltZWxpbmVUcmFuc3BvcnQubmFtZVxuICAgICAgfSk7XG4gICAgfVxuXG4gICAgdmFyIGdldFN0cmF0ZWd5ID0gKG9wdGlvbnM6IFN0cmF0ZWd5T3B0aW9ucykgPT4ge1xuICAgICAgcmV0dXJuIFJ1bnRpbWUuZ2V0RGVmYXVsdFN0cmF0ZWd5KHRoaXMuY29uZmlnLCBvcHRpb25zLCBkZWZpbmVUcmFuc3BvcnQpO1xuICAgIH07XG5cbiAgICB0aGlzLmNvbm5lY3Rpb24gPSBGYWN0b3J5LmNyZWF0ZUNvbm5lY3Rpb25NYW5hZ2VyKHRoaXMua2V5LCB7XG4gICAgICBnZXRTdHJhdGVneTogZ2V0U3RyYXRlZ3ksXG4gICAgICB0aW1lbGluZTogdGhpcy50aW1lbGluZSxcbiAgICAgIGFjdGl2aXR5VGltZW91dDogdGhpcy5jb25maWcuYWN0aXZpdHlUaW1lb3V0LFxuICAgICAgcG9uZ1RpbWVvdXQ6IHRoaXMuY29uZmlnLnBvbmdUaW1lb3V0LFxuICAgICAgdW5hdmFpbGFibGVUaW1lb3V0OiB0aGlzLmNvbmZpZy51bmF2YWlsYWJsZVRpbWVvdXQsXG4gICAgICB1c2VUTFM6IEJvb2xlYW4odGhpcy5jb25maWcudXNlVExTKVxuICAgIH0pO1xuXG4gICAgdGhpcy5jb25uZWN0aW9uLmJpbmQoJ2Nvbm5lY3RlZCcsICgpID0+IHtcbiAgICAgIHRoaXMuc3Vic2NyaWJlQWxsKCk7XG4gICAgICBpZiAodGhpcy50aW1lbGluZVNlbmRlcikge1xuICAgICAgICB0aGlzLnRpbWVsaW5lU2VuZGVyLnNlbmQodGhpcy5jb25uZWN0aW9uLmlzVXNpbmdUTFMoKSk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICB0aGlzLmNvbm5lY3Rpb24uYmluZCgnbWVzc2FnZScsIGV2ZW50ID0+IHtcbiAgICAgIHZhciBldmVudE5hbWUgPSBldmVudC5ldmVudDtcbiAgICAgIHZhciBpbnRlcm5hbCA9IGV2ZW50TmFtZS5pbmRleE9mKCdwdXNoZXJfaW50ZXJuYWw6JykgPT09IDA7XG4gICAgICBpZiAoZXZlbnQuY2hhbm5lbCkge1xuICAgICAgICB2YXIgY2hhbm5lbCA9IHRoaXMuY2hhbm5lbChldmVudC5jaGFubmVsKTtcbiAgICAgICAgaWYgKGNoYW5uZWwpIHtcbiAgICAgICAgICBjaGFubmVsLmhhbmRsZUV2ZW50KGV2ZW50KTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgLy8gRW1pdCBnbG9iYWxseSBbZGVwcmVjYXRlZF1cbiAgICAgIGlmICghaW50ZXJuYWwpIHtcbiAgICAgICAgdGhpcy5nbG9iYWxfZW1pdHRlci5lbWl0KGV2ZW50LmV2ZW50LCBldmVudC5kYXRhKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgICB0aGlzLmNvbm5lY3Rpb24uYmluZCgnY29ubmVjdGluZycsICgpID0+IHtcbiAgICAgIHRoaXMuY2hhbm5lbHMuZGlzY29ubmVjdCgpO1xuICAgIH0pO1xuICAgIHRoaXMuY29ubmVjdGlvbi5iaW5kKCdkaXNjb25uZWN0ZWQnLCAoKSA9PiB7XG4gICAgICB0aGlzLmNoYW5uZWxzLmRpc2Nvbm5lY3QoKTtcbiAgICB9KTtcbiAgICB0aGlzLmNvbm5lY3Rpb24uYmluZCgnZXJyb3InLCBlcnIgPT4ge1xuICAgICAgTG9nZ2VyLndhcm4oZXJyKTtcbiAgICB9KTtcblxuICAgIFB1c2hlci5pbnN0YW5jZXMucHVzaCh0aGlzKTtcbiAgICB0aGlzLnRpbWVsaW5lLmluZm8oeyBpbnN0YW5jZXM6IFB1c2hlci5pbnN0YW5jZXMubGVuZ3RoIH0pO1xuXG4gICAgdGhpcy51c2VyID0gbmV3IFVzZXJGYWNhZGUodGhpcyk7XG5cbiAgICBpZiAoUHVzaGVyLmlzUmVhZHkpIHtcbiAgICAgIHRoaXMuY29ubmVjdCgpO1xuICAgIH1cbiAgfVxuXG4gIGNoYW5uZWwobmFtZTogc3RyaW5nKTogQ2hhbm5lbCB7XG4gICAgcmV0dXJuIHRoaXMuY2hhbm5lbHMuZmluZChuYW1lKTtcbiAgfVxuXG4gIGFsbENoYW5uZWxzKCk6IENoYW5uZWxbXSB7XG4gICAgcmV0dXJuIHRoaXMuY2hhbm5lbHMuYWxsKCk7XG4gIH1cblxuICBjb25uZWN0KCkge1xuICAgIHRoaXMuY29ubmVjdGlvbi5jb25uZWN0KCk7XG5cbiAgICBpZiAodGhpcy50aW1lbGluZVNlbmRlcikge1xuICAgICAgaWYgKCF0aGlzLnRpbWVsaW5lU2VuZGVyVGltZXIpIHtcbiAgICAgICAgdmFyIHVzaW5nVExTID0gdGhpcy5jb25uZWN0aW9uLmlzVXNpbmdUTFMoKTtcbiAgICAgICAgdmFyIHRpbWVsaW5lU2VuZGVyID0gdGhpcy50aW1lbGluZVNlbmRlcjtcbiAgICAgICAgdGhpcy50aW1lbGluZVNlbmRlclRpbWVyID0gbmV3IFBlcmlvZGljVGltZXIoNjAwMDAsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgIHRpbWVsaW5lU2VuZGVyLnNlbmQodXNpbmdUTFMpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICBkaXNjb25uZWN0KCkge1xuICAgIHRoaXMuY29ubmVjdGlvbi5kaXNjb25uZWN0KCk7XG5cbiAgICBpZiAodGhpcy50aW1lbGluZVNlbmRlclRpbWVyKSB7XG4gICAgICB0aGlzLnRpbWVsaW5lU2VuZGVyVGltZXIuZW5zdXJlQWJvcnRlZCgpO1xuICAgICAgdGhpcy50aW1lbGluZVNlbmRlclRpbWVyID0gbnVsbDtcbiAgICB9XG4gIH1cblxuICBiaW5kKGV2ZW50X25hbWU6IHN0cmluZywgY2FsbGJhY2s6IEZ1bmN0aW9uLCBjb250ZXh0PzogYW55KTogUHVzaGVyIHtcbiAgICB0aGlzLmdsb2JhbF9lbWl0dGVyLmJpbmQoZXZlbnRfbmFtZSwgY2FsbGJhY2ssIGNvbnRleHQpO1xuICAgIHJldHVybiB0aGlzO1xuICB9XG5cbiAgdW5iaW5kKGV2ZW50X25hbWU/OiBzdHJpbmcsIGNhbGxiYWNrPzogRnVuY3Rpb24sIGNvbnRleHQ/OiBhbnkpOiBQdXNoZXIge1xuICAgIHRoaXMuZ2xvYmFsX2VtaXR0ZXIudW5iaW5kKGV2ZW50X25hbWUsIGNhbGxiYWNrLCBjb250ZXh0KTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIGJpbmRfZ2xvYmFsKGNhbGxiYWNrOiBGdW5jdGlvbik6IFB1c2hlciB7XG4gICAgdGhpcy5nbG9iYWxfZW1pdHRlci5iaW5kX2dsb2JhbChjYWxsYmFjayk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICB1bmJpbmRfZ2xvYmFsKGNhbGxiYWNrPzogRnVuY3Rpb24pOiBQdXNoZXIge1xuICAgIHRoaXMuZ2xvYmFsX2VtaXR0ZXIudW5iaW5kX2dsb2JhbChjYWxsYmFjayk7XG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICB1bmJpbmRfYWxsKGNhbGxiYWNrPzogRnVuY3Rpb24pOiBQdXNoZXIge1xuICAgIHRoaXMuZ2xvYmFsX2VtaXR0ZXIudW5iaW5kX2FsbCgpO1xuICAgIHJldHVybiB0aGlzO1xuICB9XG5cbiAgc3Vic2NyaWJlQWxsKCkge1xuICAgIHZhciBjaGFubmVsTmFtZTtcbiAgICBmb3IgKGNoYW5uZWxOYW1lIGluIHRoaXMuY2hhbm5lbHMuY2hhbm5lbHMpIHtcbiAgICAgIGlmICh0aGlzLmNoYW5uZWxzLmNoYW5uZWxzLmhhc093blByb3BlcnR5KGNoYW5uZWxOYW1lKSkge1xuICAgICAgICB0aGlzLnN1YnNjcmliZShjaGFubmVsTmFtZSk7XG4gICAgICB9XG4gICAgfVxuICB9XG5cbiAgc3Vic2NyaWJlKGNoYW5uZWxfbmFtZTogc3RyaW5nKSB7XG4gICAgdmFyIGNoYW5uZWwgPSB0aGlzLmNoYW5uZWxzLmFkZChjaGFubmVsX25hbWUsIHRoaXMpO1xuICAgIGlmIChjaGFubmVsLnN1YnNjcmlwdGlvblBlbmRpbmcgJiYgY2hhbm5lbC5zdWJzY3JpcHRpb25DYW5jZWxsZWQpIHtcbiAgICAgIGNoYW5uZWwucmVpbnN0YXRlU3Vic2NyaXB0aW9uKCk7XG4gICAgfSBlbHNlIGlmIChcbiAgICAgICFjaGFubmVsLnN1YnNjcmlwdGlvblBlbmRpbmcgJiZcbiAgICAgIHRoaXMuY29ubmVjdGlvbi5zdGF0ZSA9PT0gJ2Nvbm5lY3RlZCdcbiAgICApIHtcbiAgICAgIGNoYW5uZWwuc3Vic2NyaWJlKCk7XG4gICAgfVxuICAgIHJldHVybiBjaGFubmVsO1xuICB9XG5cbiAgdW5zdWJzY3JpYmUoY2hhbm5lbF9uYW1lOiBzdHJpbmcpIHtcbiAgICB2YXIgY2hhbm5lbCA9IHRoaXMuY2hhbm5lbHMuZmluZChjaGFubmVsX25hbWUpO1xuICAgIGlmIChjaGFubmVsICYmIGNoYW5uZWwuc3Vic2NyaXB0aW9uUGVuZGluZykge1xuICAgICAgY2hhbm5lbC5jYW5jZWxTdWJzY3JpcHRpb24oKTtcbiAgICB9IGVsc2Uge1xuICAgICAgY2hhbm5lbCA9IHRoaXMuY2hhbm5lbHMucmVtb3ZlKGNoYW5uZWxfbmFtZSk7XG4gICAgICBpZiAoY2hhbm5lbCAmJiBjaGFubmVsLnN1YnNjcmliZWQpIHtcbiAgICAgICAgY2hhbm5lbC51bnN1YnNjcmliZSgpO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHNlbmRfZXZlbnQoZXZlbnRfbmFtZTogc3RyaW5nLCBkYXRhOiBhbnksIGNoYW5uZWw/OiBzdHJpbmcpIHtcbiAgICByZXR1cm4gdGhpcy5jb25uZWN0aW9uLnNlbmRfZXZlbnQoZXZlbnRfbmFtZSwgZGF0YSwgY2hhbm5lbCk7XG4gIH1cblxuICBzaG91bGRVc2VUTFMoKTogYm9vbGVhbiB7XG4gICAgcmV0dXJuIHRoaXMuY29uZmlnLnVzZVRMUztcbiAgfVxuXG4gIHNpZ25pbigpIHtcbiAgICB0aGlzLnVzZXIuc2lnbmluKCk7XG4gIH1cbn1cblxuZnVuY3Rpb24gY2hlY2tBcHBLZXkoa2V5KSB7XG4gIGlmIChrZXkgPT09IG51bGwgfHwga2V5ID09PSB1bmRlZmluZWQpIHtcbiAgICB0aHJvdyAnWW91IG11c3QgcGFzcyB5b3VyIGFwcCBrZXkgd2hlbiB5b3UgaW5zdGFudGlhdGUgUHVzaGVyLic7XG4gIH1cbn1cblxuUnVudGltZS5zZXR1cChQdXNoZXIpO1xuIiwgImZ1bmN0aW9uIF90eXBlb2Yob2JqKSB7XG4gIFwiQGJhYmVsL2hlbHBlcnMgLSB0eXBlb2ZcIjtcblxuICByZXR1cm4gX3R5cGVvZiA9IFwiZnVuY3Rpb25cIiA9PSB0eXBlb2YgU3ltYm9sICYmIFwic3ltYm9sXCIgPT0gdHlwZW9mIFN5bWJvbC5pdGVyYXRvciA/IGZ1bmN0aW9uIChvYmopIHtcbiAgICByZXR1cm4gdHlwZW9mIG9iajtcbiAgfSA6IGZ1bmN0aW9uIChvYmopIHtcbiAgICByZXR1cm4gb2JqICYmIFwiZnVuY3Rpb25cIiA9PSB0eXBlb2YgU3ltYm9sICYmIG9iai5jb25zdHJ1Y3RvciA9PT0gU3ltYm9sICYmIG9iaiAhPT0gU3ltYm9sLnByb3RvdHlwZSA/IFwic3ltYm9sXCIgOiB0eXBlb2Ygb2JqO1xuICB9LCBfdHlwZW9mKG9iaik7XG59XG5cbmZ1bmN0aW9uIF9jbGFzc0NhbGxDaGVjayhpbnN0YW5jZSwgQ29uc3RydWN0b3IpIHtcbiAgaWYgKCEoaW5zdGFuY2UgaW5zdGFuY2VvZiBDb25zdHJ1Y3RvcikpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiQ2Fubm90IGNhbGwgYSBjbGFzcyBhcyBhIGZ1bmN0aW9uXCIpO1xuICB9XG59XG5cbmZ1bmN0aW9uIF9kZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgcHJvcHMpIHtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBwcm9wcy5sZW5ndGg7IGkrKykge1xuICAgIHZhciBkZXNjcmlwdG9yID0gcHJvcHNbaV07XG4gICAgZGVzY3JpcHRvci5lbnVtZXJhYmxlID0gZGVzY3JpcHRvci5lbnVtZXJhYmxlIHx8IGZhbHNlO1xuICAgIGRlc2NyaXB0b3IuY29uZmlndXJhYmxlID0gdHJ1ZTtcbiAgICBpZiAoXCJ2YWx1ZVwiIGluIGRlc2NyaXB0b3IpIGRlc2NyaXB0b3Iud3JpdGFibGUgPSB0cnVlO1xuICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0eSh0YXJnZXQsIGRlc2NyaXB0b3Iua2V5LCBkZXNjcmlwdG9yKTtcbiAgfVxufVxuXG5mdW5jdGlvbiBfY3JlYXRlQ2xhc3MoQ29uc3RydWN0b3IsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XG4gIGlmIChwcm90b1Byb3BzKSBfZGVmaW5lUHJvcGVydGllcyhDb25zdHJ1Y3Rvci5wcm90b3R5cGUsIHByb3RvUHJvcHMpO1xuICBpZiAoc3RhdGljUHJvcHMpIF9kZWZpbmVQcm9wZXJ0aWVzKENvbnN0cnVjdG9yLCBzdGF0aWNQcm9wcyk7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShDb25zdHJ1Y3RvciwgXCJwcm90b3R5cGVcIiwge1xuICAgIHdyaXRhYmxlOiBmYWxzZVxuICB9KTtcbiAgcmV0dXJuIENvbnN0cnVjdG9yO1xufVxuXG5mdW5jdGlvbiBfZXh0ZW5kcygpIHtcbiAgX2V4dGVuZHMgPSBPYmplY3QuYXNzaWduIHx8IGZ1bmN0aW9uICh0YXJnZXQpIHtcbiAgICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIHNvdXJjZSA9IGFyZ3VtZW50c1tpXTtcblxuICAgICAgZm9yICh2YXIga2V5IGluIHNvdXJjZSkge1xuICAgICAgICBpZiAoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKHNvdXJjZSwga2V5KSkge1xuICAgICAgICAgIHRhcmdldFtrZXldID0gc291cmNlW2tleV07XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gdGFyZ2V0O1xuICB9O1xuXG4gIHJldHVybiBfZXh0ZW5kcy5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xufVxuXG5mdW5jdGlvbiBfaW5oZXJpdHMoc3ViQ2xhc3MsIHN1cGVyQ2xhc3MpIHtcbiAgaWYgKHR5cGVvZiBzdXBlckNsYXNzICE9PSBcImZ1bmN0aW9uXCIgJiYgc3VwZXJDbGFzcyAhPT0gbnVsbCkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJTdXBlciBleHByZXNzaW9uIG11c3QgZWl0aGVyIGJlIG51bGwgb3IgYSBmdW5jdGlvblwiKTtcbiAgfVxuXG4gIHN1YkNsYXNzLnByb3RvdHlwZSA9IE9iamVjdC5jcmVhdGUoc3VwZXJDbGFzcyAmJiBzdXBlckNsYXNzLnByb3RvdHlwZSwge1xuICAgIGNvbnN0cnVjdG9yOiB7XG4gICAgICB2YWx1ZTogc3ViQ2xhc3MsXG4gICAgICB3cml0YWJsZTogdHJ1ZSxcbiAgICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICAgIH1cbiAgfSk7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShzdWJDbGFzcywgXCJwcm90b3R5cGVcIiwge1xuICAgIHdyaXRhYmxlOiBmYWxzZVxuICB9KTtcbiAgaWYgKHN1cGVyQ2xhc3MpIF9zZXRQcm90b3R5cGVPZihzdWJDbGFzcywgc3VwZXJDbGFzcyk7XG59XG5cbmZ1bmN0aW9uIF9nZXRQcm90b3R5cGVPZihvKSB7XG4gIF9nZXRQcm90b3R5cGVPZiA9IE9iamVjdC5zZXRQcm90b3R5cGVPZiA/IE9iamVjdC5nZXRQcm90b3R5cGVPZiA6IGZ1bmN0aW9uIF9nZXRQcm90b3R5cGVPZihvKSB7XG4gICAgcmV0dXJuIG8uX19wcm90b19fIHx8IE9iamVjdC5nZXRQcm90b3R5cGVPZihvKTtcbiAgfTtcbiAgcmV0dXJuIF9nZXRQcm90b3R5cGVPZihvKTtcbn1cblxuZnVuY3Rpb24gX3NldFByb3RvdHlwZU9mKG8sIHApIHtcbiAgX3NldFByb3RvdHlwZU9mID0gT2JqZWN0LnNldFByb3RvdHlwZU9mIHx8IGZ1bmN0aW9uIF9zZXRQcm90b3R5cGVPZihvLCBwKSB7XG4gICAgby5fX3Byb3RvX18gPSBwO1xuICAgIHJldHVybiBvO1xuICB9O1xuXG4gIHJldHVybiBfc2V0UHJvdG90eXBlT2YobywgcCk7XG59XG5cbmZ1bmN0aW9uIF9pc05hdGl2ZVJlZmxlY3RDb25zdHJ1Y3QoKSB7XG4gIGlmICh0eXBlb2YgUmVmbGVjdCA9PT0gXCJ1bmRlZmluZWRcIiB8fCAhUmVmbGVjdC5jb25zdHJ1Y3QpIHJldHVybiBmYWxzZTtcbiAgaWYgKFJlZmxlY3QuY29uc3RydWN0LnNoYW0pIHJldHVybiBmYWxzZTtcbiAgaWYgKHR5cGVvZiBQcm94eSA9PT0gXCJmdW5jdGlvblwiKSByZXR1cm4gdHJ1ZTtcblxuICB0cnkge1xuICAgIEJvb2xlYW4ucHJvdG90eXBlLnZhbHVlT2YuY2FsbChSZWZsZWN0LmNvbnN0cnVjdChCb29sZWFuLCBbXSwgZnVuY3Rpb24gKCkge30pKTtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfSBjYXRjaCAoZSkge1xuICAgIHJldHVybiBmYWxzZTtcbiAgfVxufVxuXG5mdW5jdGlvbiBfYXNzZXJ0VGhpc0luaXRpYWxpemVkKHNlbGYpIHtcbiAgaWYgKHNlbGYgPT09IHZvaWQgMCkge1xuICAgIHRocm93IG5ldyBSZWZlcmVuY2VFcnJvcihcInRoaXMgaGFzbid0IGJlZW4gaW5pdGlhbGlzZWQgLSBzdXBlcigpIGhhc24ndCBiZWVuIGNhbGxlZFwiKTtcbiAgfVxuXG4gIHJldHVybiBzZWxmO1xufVxuXG5mdW5jdGlvbiBfcG9zc2libGVDb25zdHJ1Y3RvclJldHVybihzZWxmLCBjYWxsKSB7XG4gIGlmIChjYWxsICYmICh0eXBlb2YgY2FsbCA9PT0gXCJvYmplY3RcIiB8fCB0eXBlb2YgY2FsbCA9PT0gXCJmdW5jdGlvblwiKSkge1xuICAgIHJldHVybiBjYWxsO1xuICB9IGVsc2UgaWYgKGNhbGwgIT09IHZvaWQgMCkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJEZXJpdmVkIGNvbnN0cnVjdG9ycyBtYXkgb25seSByZXR1cm4gb2JqZWN0IG9yIHVuZGVmaW5lZFwiKTtcbiAgfVxuXG4gIHJldHVybiBfYXNzZXJ0VGhpc0luaXRpYWxpemVkKHNlbGYpO1xufVxuXG5mdW5jdGlvbiBfY3JlYXRlU3VwZXIoRGVyaXZlZCkge1xuICB2YXIgaGFzTmF0aXZlUmVmbGVjdENvbnN0cnVjdCA9IF9pc05hdGl2ZVJlZmxlY3RDb25zdHJ1Y3QoKTtcblxuICByZXR1cm4gZnVuY3Rpb24gX2NyZWF0ZVN1cGVySW50ZXJuYWwoKSB7XG4gICAgdmFyIFN1cGVyID0gX2dldFByb3RvdHlwZU9mKERlcml2ZWQpLFxuICAgICAgICByZXN1bHQ7XG5cbiAgICBpZiAoaGFzTmF0aXZlUmVmbGVjdENvbnN0cnVjdCkge1xuICAgICAgdmFyIE5ld1RhcmdldCA9IF9nZXRQcm90b3R5cGVPZih0aGlzKS5jb25zdHJ1Y3RvcjtcblxuICAgICAgcmVzdWx0ID0gUmVmbGVjdC5jb25zdHJ1Y3QoU3VwZXIsIGFyZ3VtZW50cywgTmV3VGFyZ2V0KTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmVzdWx0ID0gU3VwZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG5cbiAgICByZXR1cm4gX3Bvc3NpYmxlQ29uc3RydWN0b3JSZXR1cm4odGhpcywgcmVzdWx0KTtcbiAgfTtcbn1cblxuLyoqXHJcbiAqIFRoaXMgY2xhc3MgcmVwcmVzZW50cyBhIGJhc2ljIGNoYW5uZWwuXHJcbiAqL1xudmFyIENoYW5uZWwgPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBDaGFubmVsKCkge1xuICAgIF9jbGFzc0NhbGxDaGVjayh0aGlzLCBDaGFubmVsKTtcbiAgfVxuXG4gIF9jcmVhdGVDbGFzcyhDaGFubmVsLCBbe1xuICAgIGtleTogXCJsaXN0ZW5Gb3JXaGlzcGVyXCIsXG4gICAgdmFsdWU6XG4gICAgLyoqXHJcbiAgICAgKiBMaXN0ZW4gZm9yIGEgd2hpc3BlciBldmVudCBvbiB0aGUgY2hhbm5lbCBpbnN0YW5jZS5cclxuICAgICAqL1xuICAgIGZ1bmN0aW9uIGxpc3RlbkZvcldoaXNwZXIoZXZlbnQsIGNhbGxiYWNrKSB7XG4gICAgICByZXR1cm4gdGhpcy5saXN0ZW4oJy5jbGllbnQtJyArIGV2ZW50LCBjYWxsYmFjayk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGlzdGVuIGZvciBhbiBldmVudCBvbiB0aGUgY2hhbm5lbCBpbnN0YW5jZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwibm90aWZpY2F0aW9uXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIG5vdGlmaWNhdGlvbihjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXMubGlzdGVuKCcuSWxsdW1pbmF0ZVxcXFxOb3RpZmljYXRpb25zXFxcXEV2ZW50c1xcXFxCcm9hZGNhc3ROb3RpZmljYXRpb25DcmVhdGVkJywgY2FsbGJhY2spO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFN0b3AgbGlzdGVuaW5nIGZvciBhIHdoaXNwZXIgZXZlbnQgb24gdGhlIGNoYW5uZWwgaW5zdGFuY2UuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInN0b3BMaXN0ZW5pbmdGb3JXaGlzcGVyXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHN0b3BMaXN0ZW5pbmdGb3JXaGlzcGVyKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXMuc3RvcExpc3RlbmluZygnLmNsaWVudC0nICsgZXZlbnQsIGNhbGxiYWNrKTtcbiAgICB9XG4gIH1dKTtcblxuICByZXR1cm4gQ2hhbm5lbDtcbn0oKTtcblxuLyoqXHJcbiAqIEV2ZW50IG5hbWUgZm9ybWF0dGVyXHJcbiAqL1xudmFyIEV2ZW50Rm9ybWF0dGVyID0gLyojX19QVVJFX18qL2Z1bmN0aW9uICgpIHtcbiAgLyoqXHJcbiAgICogQ3JlYXRlIGEgbmV3IGNsYXNzIGluc3RhbmNlLlxyXG4gICAqL1xuICBmdW5jdGlvbiBFdmVudEZvcm1hdHRlcihuYW1lc3BhY2UpIHtcbiAgICBfY2xhc3NDYWxsQ2hlY2sodGhpcywgRXZlbnRGb3JtYXR0ZXIpO1xuXG4gICAgdGhpcy5zZXROYW1lc3BhY2UobmFtZXNwYWNlKTtcbiAgfVxuICAvKipcclxuICAgKiBGb3JtYXQgdGhlIGdpdmVuIGV2ZW50IG5hbWUuXHJcbiAgICovXG5cblxuICBfY3JlYXRlQ2xhc3MoRXZlbnRGb3JtYXR0ZXIsIFt7XG4gICAga2V5OiBcImZvcm1hdFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBmb3JtYXQoZXZlbnQpIHtcbiAgICAgIGlmIChldmVudC5jaGFyQXQoMCkgPT09ICcuJyB8fCBldmVudC5jaGFyQXQoMCkgPT09ICdcXFxcJykge1xuICAgICAgICByZXR1cm4gZXZlbnQuc3Vic3RyKDEpO1xuICAgICAgfSBlbHNlIGlmICh0aGlzLm5hbWVzcGFjZSkge1xuICAgICAgICBldmVudCA9IHRoaXMubmFtZXNwYWNlICsgJy4nICsgZXZlbnQ7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiBldmVudC5yZXBsYWNlKC9cXC4vZywgJ1xcXFwnKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBTZXQgdGhlIGV2ZW50IG5hbWVzcGFjZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwic2V0TmFtZXNwYWNlXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHNldE5hbWVzcGFjZSh2YWx1ZSkge1xuICAgICAgdGhpcy5uYW1lc3BhY2UgPSB2YWx1ZTtcbiAgICB9XG4gIH1dKTtcblxuICByZXR1cm4gRXZlbnRGb3JtYXR0ZXI7XG59KCk7XG5cbi8qKlxyXG4gKiBUaGlzIGNsYXNzIHJlcHJlc2VudHMgYSBQdXNoZXIgY2hhbm5lbC5cclxuICovXG5cbnZhciBQdXNoZXJDaGFubmVsID0gLyojX19QVVJFX18qL2Z1bmN0aW9uIChfQ2hhbm5lbCkge1xuICBfaW5oZXJpdHMoUHVzaGVyQ2hhbm5lbCwgX0NoYW5uZWwpO1xuXG4gIHZhciBfc3VwZXIgPSBfY3JlYXRlU3VwZXIoUHVzaGVyQ2hhbm5lbCk7XG5cbiAgLyoqXHJcbiAgICogQ3JlYXRlIGEgbmV3IGNsYXNzIGluc3RhbmNlLlxyXG4gICAqL1xuICBmdW5jdGlvbiBQdXNoZXJDaGFubmVsKHB1c2hlciwgbmFtZSwgb3B0aW9ucykge1xuICAgIHZhciBfdGhpcztcblxuICAgIF9jbGFzc0NhbGxDaGVjayh0aGlzLCBQdXNoZXJDaGFubmVsKTtcblxuICAgIF90aGlzID0gX3N1cGVyLmNhbGwodGhpcyk7XG4gICAgX3RoaXMubmFtZSA9IG5hbWU7XG4gICAgX3RoaXMucHVzaGVyID0gcHVzaGVyO1xuICAgIF90aGlzLm9wdGlvbnMgPSBvcHRpb25zO1xuICAgIF90aGlzLmV2ZW50Rm9ybWF0dGVyID0gbmV3IEV2ZW50Rm9ybWF0dGVyKF90aGlzLm9wdGlvbnMubmFtZXNwYWNlKTtcblxuICAgIF90aGlzLnN1YnNjcmliZSgpO1xuXG4gICAgcmV0dXJuIF90aGlzO1xuICB9XG4gIC8qKlxyXG4gICAqIFN1YnNjcmliZSB0byBhIFB1c2hlciBjaGFubmVsLlxyXG4gICAqL1xuXG5cbiAgX2NyZWF0ZUNsYXNzKFB1c2hlckNoYW5uZWwsIFt7XG4gICAga2V5OiBcInN1YnNjcmliZVwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBzdWJzY3JpYmUoKSB7XG4gICAgICB0aGlzLnN1YnNjcmlwdGlvbiA9IHRoaXMucHVzaGVyLnN1YnNjcmliZSh0aGlzLm5hbWUpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFVuc3Vic2NyaWJlIGZyb20gYSBQdXNoZXIgY2hhbm5lbC5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwidW5zdWJzY3JpYmVcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gdW5zdWJzY3JpYmUoKSB7XG4gICAgICB0aGlzLnB1c2hlci51bnN1YnNjcmliZSh0aGlzLm5hbWUpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExpc3RlbiBmb3IgYW4gZXZlbnQgb24gdGhlIGNoYW5uZWwgaW5zdGFuY2UuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImxpc3RlblwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsaXN0ZW4oZXZlbnQsIGNhbGxiYWNrKSB7XG4gICAgICB0aGlzLm9uKHRoaXMuZXZlbnRGb3JtYXR0ZXIuZm9ybWF0KGV2ZW50KSwgY2FsbGJhY2spO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGlzdGVuIGZvciBhbGwgZXZlbnRzIG9uIHRoZSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5Ub0FsbFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsaXN0ZW5Ub0FsbChjYWxsYmFjaykge1xuICAgICAgdmFyIF90aGlzMiA9IHRoaXM7XG5cbiAgICAgIHRoaXMuc3Vic2NyaXB0aW9uLmJpbmRfZ2xvYmFsKGZ1bmN0aW9uIChldmVudCwgZGF0YSkge1xuICAgICAgICBpZiAoZXZlbnQuc3RhcnRzV2l0aCgncHVzaGVyOicpKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdmFyIG5hbWVzcGFjZSA9IF90aGlzMi5vcHRpb25zLm5hbWVzcGFjZS5yZXBsYWNlKC9cXC4vZywgJ1xcXFwnKTtcblxuICAgICAgICB2YXIgZm9ybWF0dGVkRXZlbnQgPSBldmVudC5zdGFydHNXaXRoKG5hbWVzcGFjZSkgPyBldmVudC5zdWJzdHJpbmcobmFtZXNwYWNlLmxlbmd0aCArIDEpIDogJy4nICsgZXZlbnQ7XG4gICAgICAgIGNhbGxiYWNrKGZvcm1hdHRlZEV2ZW50LCBkYXRhKTtcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogU3RvcCBsaXN0ZW5pbmcgZm9yIGFuIGV2ZW50IG9uIHRoZSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJzdG9wTGlzdGVuaW5nXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHN0b3BMaXN0ZW5pbmcoZXZlbnQsIGNhbGxiYWNrKSB7XG4gICAgICBpZiAoY2FsbGJhY2spIHtcbiAgICAgICAgdGhpcy5zdWJzY3JpcHRpb24udW5iaW5kKHRoaXMuZXZlbnRGb3JtYXR0ZXIuZm9ybWF0KGV2ZW50KSwgY2FsbGJhY2spO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5zdWJzY3JpcHRpb24udW5iaW5kKHRoaXMuZXZlbnRGb3JtYXR0ZXIuZm9ybWF0KGV2ZW50KSk7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFN0b3AgbGlzdGVuaW5nIGZvciBhbGwgZXZlbnRzIG9uIHRoZSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJzdG9wTGlzdGVuaW5nVG9BbGxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gc3RvcExpc3RlbmluZ1RvQWxsKGNhbGxiYWNrKSB7XG4gICAgICBpZiAoY2FsbGJhY2spIHtcbiAgICAgICAgdGhpcy5zdWJzY3JpcHRpb24udW5iaW5kX2dsb2JhbChjYWxsYmFjayk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLnN1YnNjcmlwdGlvbi51bmJpbmRfZ2xvYmFsKCk7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyIGEgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIGFueXRpbWUgYSBzdWJzY3JpcHRpb24gc3VjY2VlZHMuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInN1YnNjcmliZWRcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gc3Vic2NyaWJlZChjYWxsYmFjaykge1xuICAgICAgdGhpcy5vbigncHVzaGVyOnN1YnNjcmlwdGlvbl9zdWNjZWVkZWQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyIGEgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIGFueXRpbWUgYSBzdWJzY3JpcHRpb24gZXJyb3Igb2NjdXJzLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJlcnJvclwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBlcnJvcihjYWxsYmFjaykge1xuICAgICAgdGhpcy5vbigncHVzaGVyOnN1YnNjcmlwdGlvbl9lcnJvcicsIGZ1bmN0aW9uIChzdGF0dXMpIHtcbiAgICAgICAgY2FsbGJhY2soc3RhdHVzKTtcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogQmluZCBhIGNoYW5uZWwgdG8gYW4gZXZlbnQuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcIm9uXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIG9uKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgdGhpcy5zdWJzY3JpcHRpb24uYmluZChldmVudCwgY2FsbGJhY2spO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICB9XSk7XG5cbiAgcmV0dXJuIFB1c2hlckNoYW5uZWw7XG59KENoYW5uZWwpO1xuXG4vKipcclxuICogVGhpcyBjbGFzcyByZXByZXNlbnRzIGEgUHVzaGVyIHByaXZhdGUgY2hhbm5lbC5cclxuICovXG5cbnZhciBQdXNoZXJQcml2YXRlQ2hhbm5lbCA9IC8qI19fUFVSRV9fKi9mdW5jdGlvbiAoX1B1c2hlckNoYW5uZWwpIHtcbiAgX2luaGVyaXRzKFB1c2hlclByaXZhdGVDaGFubmVsLCBfUHVzaGVyQ2hhbm5lbCk7XG5cbiAgdmFyIF9zdXBlciA9IF9jcmVhdGVTdXBlcihQdXNoZXJQcml2YXRlQ2hhbm5lbCk7XG5cbiAgZnVuY3Rpb24gUHVzaGVyUHJpdmF0ZUNoYW5uZWwoKSB7XG4gICAgX2NsYXNzQ2FsbENoZWNrKHRoaXMsIFB1c2hlclByaXZhdGVDaGFubmVsKTtcblxuICAgIHJldHVybiBfc3VwZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgfVxuXG4gIF9jcmVhdGVDbGFzcyhQdXNoZXJQcml2YXRlQ2hhbm5lbCwgW3tcbiAgICBrZXk6IFwid2hpc3BlclwiLFxuICAgIHZhbHVlOlxuICAgIC8qKlxyXG4gICAgICogU2VuZCBhIHdoaXNwZXIgZXZlbnQgdG8gb3RoZXIgY2xpZW50cyBpbiB0aGUgY2hhbm5lbC5cclxuICAgICAqL1xuICAgIGZ1bmN0aW9uIHdoaXNwZXIoZXZlbnROYW1lLCBkYXRhKSB7XG4gICAgICB0aGlzLnB1c2hlci5jaGFubmVscy5jaGFubmVsc1t0aGlzLm5hbWVdLnRyaWdnZXIoXCJjbGllbnQtXCIuY29uY2F0KGV2ZW50TmFtZSksIGRhdGEpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICB9XSk7XG5cbiAgcmV0dXJuIFB1c2hlclByaXZhdGVDaGFubmVsO1xufShQdXNoZXJDaGFubmVsKTtcblxuLyoqXHJcbiAqIFRoaXMgY2xhc3MgcmVwcmVzZW50cyBhIFB1c2hlciBwcml2YXRlIGNoYW5uZWwuXHJcbiAqL1xuXG52YXIgUHVzaGVyRW5jcnlwdGVkUHJpdmF0ZUNoYW5uZWwgPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKF9QdXNoZXJDaGFubmVsKSB7XG4gIF9pbmhlcml0cyhQdXNoZXJFbmNyeXB0ZWRQcml2YXRlQ2hhbm5lbCwgX1B1c2hlckNoYW5uZWwpO1xuXG4gIHZhciBfc3VwZXIgPSBfY3JlYXRlU3VwZXIoUHVzaGVyRW5jcnlwdGVkUHJpdmF0ZUNoYW5uZWwpO1xuXG4gIGZ1bmN0aW9uIFB1c2hlckVuY3J5cHRlZFByaXZhdGVDaGFubmVsKCkge1xuICAgIF9jbGFzc0NhbGxDaGVjayh0aGlzLCBQdXNoZXJFbmNyeXB0ZWRQcml2YXRlQ2hhbm5lbCk7XG5cbiAgICByZXR1cm4gX3N1cGVyLmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG4gIH1cblxuICBfY3JlYXRlQ2xhc3MoUHVzaGVyRW5jcnlwdGVkUHJpdmF0ZUNoYW5uZWwsIFt7XG4gICAga2V5OiBcIndoaXNwZXJcIixcbiAgICB2YWx1ZTpcbiAgICAvKipcclxuICAgICAqIFNlbmQgYSB3aGlzcGVyIGV2ZW50IHRvIG90aGVyIGNsaWVudHMgaW4gdGhlIGNoYW5uZWwuXHJcbiAgICAgKi9cbiAgICBmdW5jdGlvbiB3aGlzcGVyKGV2ZW50TmFtZSwgZGF0YSkge1xuICAgICAgdGhpcy5wdXNoZXIuY2hhbm5lbHMuY2hhbm5lbHNbdGhpcy5uYW1lXS50cmlnZ2VyKFwiY2xpZW50LVwiLmNvbmNhdChldmVudE5hbWUpLCBkYXRhKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgfV0pO1xuXG4gIHJldHVybiBQdXNoZXJFbmNyeXB0ZWRQcml2YXRlQ2hhbm5lbDtcbn0oUHVzaGVyQ2hhbm5lbCk7XG5cbi8qKlxyXG4gKiBUaGlzIGNsYXNzIHJlcHJlc2VudHMgYSBQdXNoZXIgcHJlc2VuY2UgY2hhbm5lbC5cclxuICovXG5cbnZhciBQdXNoZXJQcmVzZW5jZUNoYW5uZWwgPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKF9QdXNoZXJDaGFubmVsKSB7XG4gIF9pbmhlcml0cyhQdXNoZXJQcmVzZW5jZUNoYW5uZWwsIF9QdXNoZXJDaGFubmVsKTtcblxuICB2YXIgX3N1cGVyID0gX2NyZWF0ZVN1cGVyKFB1c2hlclByZXNlbmNlQ2hhbm5lbCk7XG5cbiAgZnVuY3Rpb24gUHVzaGVyUHJlc2VuY2VDaGFubmVsKCkge1xuICAgIF9jbGFzc0NhbGxDaGVjayh0aGlzLCBQdXNoZXJQcmVzZW5jZUNoYW5uZWwpO1xuXG4gICAgcmV0dXJuIF9zdXBlci5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICB9XG5cbiAgX2NyZWF0ZUNsYXNzKFB1c2hlclByZXNlbmNlQ2hhbm5lbCwgW3tcbiAgICBrZXk6IFwiaGVyZVwiLFxuICAgIHZhbHVlOlxuICAgIC8qKlxyXG4gICAgICogUmVnaXN0ZXIgYSBjYWxsYmFjayB0byBiZSBjYWxsZWQgYW55dGltZSB0aGUgbWVtYmVyIGxpc3QgY2hhbmdlcy5cclxuICAgICAqL1xuICAgIGZ1bmN0aW9uIGhlcmUoY2FsbGJhY2spIHtcbiAgICAgIHRoaXMub24oJ3B1c2hlcjpzdWJzY3JpcHRpb25fc3VjY2VlZGVkJywgZnVuY3Rpb24gKGRhdGEpIHtcbiAgICAgICAgY2FsbGJhY2soT2JqZWN0LmtleXMoZGF0YS5tZW1iZXJzKS5tYXAoZnVuY3Rpb24gKGspIHtcbiAgICAgICAgICByZXR1cm4gZGF0YS5tZW1iZXJzW2tdO1xuICAgICAgICB9KSk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExpc3RlbiBmb3Igc29tZW9uZSBqb2luaW5nIHRoZSBjaGFubmVsLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJqb2luaW5nXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGpvaW5pbmcoY2FsbGJhY2spIHtcbiAgICAgIHRoaXMub24oJ3B1c2hlcjptZW1iZXJfYWRkZWQnLCBmdW5jdGlvbiAobWVtYmVyKSB7XG4gICAgICAgIGNhbGxiYWNrKG1lbWJlci5pbmZvKTtcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogU2VuZCBhIHdoaXNwZXIgZXZlbnQgdG8gb3RoZXIgY2xpZW50cyBpbiB0aGUgY2hhbm5lbC5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwid2hpc3BlclwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiB3aGlzcGVyKGV2ZW50TmFtZSwgZGF0YSkge1xuICAgICAgdGhpcy5wdXNoZXIuY2hhbm5lbHMuY2hhbm5lbHNbdGhpcy5uYW1lXS50cmlnZ2VyKFwiY2xpZW50LVwiLmNvbmNhdChldmVudE5hbWUpLCBkYXRhKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExpc3RlbiBmb3Igc29tZW9uZSBsZWF2aW5nIHRoZSBjaGFubmVsLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsZWF2aW5nXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGxlYXZpbmcoY2FsbGJhY2spIHtcbiAgICAgIHRoaXMub24oJ3B1c2hlcjptZW1iZXJfcmVtb3ZlZCcsIGZ1bmN0aW9uIChtZW1iZXIpIHtcbiAgICAgICAgY2FsbGJhY2sobWVtYmVyLmluZm8pO1xuICAgICAgfSk7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gIH1dKTtcblxuICByZXR1cm4gUHVzaGVyUHJlc2VuY2VDaGFubmVsO1xufShQdXNoZXJDaGFubmVsKTtcblxuLyoqXHJcbiAqIFRoaXMgY2xhc3MgcmVwcmVzZW50cyBhIFNvY2tldC5pbyBjaGFubmVsLlxyXG4gKi9cblxudmFyIFNvY2tldElvQ2hhbm5lbCA9IC8qI19fUFVSRV9fKi9mdW5jdGlvbiAoX0NoYW5uZWwpIHtcbiAgX2luaGVyaXRzKFNvY2tldElvQ2hhbm5lbCwgX0NoYW5uZWwpO1xuXG4gIHZhciBfc3VwZXIgPSBfY3JlYXRlU3VwZXIoU29ja2V0SW9DaGFubmVsKTtcblxuICAvKipcclxuICAgKiBDcmVhdGUgYSBuZXcgY2xhc3MgaW5zdGFuY2UuXHJcbiAgICovXG4gIGZ1bmN0aW9uIFNvY2tldElvQ2hhbm5lbChzb2NrZXQsIG5hbWUsIG9wdGlvbnMpIHtcbiAgICB2YXIgX3RoaXM7XG5cbiAgICBfY2xhc3NDYWxsQ2hlY2sodGhpcywgU29ja2V0SW9DaGFubmVsKTtcblxuICAgIF90aGlzID0gX3N1cGVyLmNhbGwodGhpcyk7XG4gICAgLyoqXHJcbiAgICAgKiBUaGUgZXZlbnQgY2FsbGJhY2tzIGFwcGxpZWQgdG8gdGhlIHNvY2tldC5cclxuICAgICAqL1xuXG4gICAgX3RoaXMuZXZlbnRzID0ge307XG4gICAgLyoqXHJcbiAgICAgKiBVc2VyIHN1cHBsaWVkIGNhbGxiYWNrcyBmb3IgZXZlbnRzIG9uIHRoaXMgY2hhbm5lbC5cclxuICAgICAqL1xuXG4gICAgX3RoaXMubGlzdGVuZXJzID0ge307XG4gICAgX3RoaXMubmFtZSA9IG5hbWU7XG4gICAgX3RoaXMuc29ja2V0ID0gc29ja2V0O1xuICAgIF90aGlzLm9wdGlvbnMgPSBvcHRpb25zO1xuICAgIF90aGlzLmV2ZW50Rm9ybWF0dGVyID0gbmV3IEV2ZW50Rm9ybWF0dGVyKF90aGlzLm9wdGlvbnMubmFtZXNwYWNlKTtcblxuICAgIF90aGlzLnN1YnNjcmliZSgpO1xuXG4gICAgcmV0dXJuIF90aGlzO1xuICB9XG4gIC8qKlxyXG4gICAqIFN1YnNjcmliZSB0byBhIFNvY2tldC5pbyBjaGFubmVsLlxyXG4gICAqL1xuXG5cbiAgX2NyZWF0ZUNsYXNzKFNvY2tldElvQ2hhbm5lbCwgW3tcbiAgICBrZXk6IFwic3Vic2NyaWJlXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHN1YnNjcmliZSgpIHtcbiAgICAgIHRoaXMuc29ja2V0LmVtaXQoJ3N1YnNjcmliZScsIHtcbiAgICAgICAgY2hhbm5lbDogdGhpcy5uYW1lLFxuICAgICAgICBhdXRoOiB0aGlzLm9wdGlvbnMuYXV0aCB8fCB7fVxuICAgICAgfSk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogVW5zdWJzY3JpYmUgZnJvbSBjaGFubmVsIGFuZCB1YmluZCBldmVudCBjYWxsYmFja3MuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInVuc3Vic2NyaWJlXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHVuc3Vic2NyaWJlKCkge1xuICAgICAgdGhpcy51bmJpbmQoKTtcbiAgICAgIHRoaXMuc29ja2V0LmVtaXQoJ3Vuc3Vic2NyaWJlJywge1xuICAgICAgICBjaGFubmVsOiB0aGlzLm5hbWUsXG4gICAgICAgIGF1dGg6IHRoaXMub3B0aW9ucy5hdXRoIHx8IHt9XG4gICAgICB9KTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBMaXN0ZW4gZm9yIGFuIGV2ZW50IG9uIHRoZSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5cIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGlzdGVuKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgdGhpcy5vbih0aGlzLmV2ZW50Rm9ybWF0dGVyLmZvcm1hdChldmVudCksIGNhbGxiYWNrKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFN0b3AgbGlzdGVuaW5nIGZvciBhbiBldmVudCBvbiB0aGUgY2hhbm5lbCBpbnN0YW5jZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwic3RvcExpc3RlbmluZ1wiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBzdG9wTGlzdGVuaW5nKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgdGhpcy51bmJpbmRFdmVudCh0aGlzLmV2ZW50Rm9ybWF0dGVyLmZvcm1hdChldmVudCksIGNhbGxiYWNrKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyIGEgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIGFueXRpbWUgYSBzdWJzY3JpcHRpb24gc3VjY2VlZHMuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInN1YnNjcmliZWRcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gc3Vic2NyaWJlZChjYWxsYmFjaykge1xuICAgICAgdGhpcy5vbignY29ubmVjdCcsIGZ1bmN0aW9uIChzb2NrZXQpIHtcbiAgICAgICAgY2FsbGJhY2soc29ja2V0KTtcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogUmVnaXN0ZXIgYSBjYWxsYmFjayB0byBiZSBjYWxsZWQgYW55dGltZSBhbiBlcnJvciBvY2N1cnMuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImVycm9yXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGVycm9yKGNhbGxiYWNrKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBCaW5kIHRoZSBjaGFubmVsJ3Mgc29ja2V0IHRvIGFuIGV2ZW50IGFuZCBzdG9yZSB0aGUgY2FsbGJhY2suXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcIm9uXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIG9uKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgdmFyIF90aGlzMiA9IHRoaXM7XG5cbiAgICAgIHRoaXMubGlzdGVuZXJzW2V2ZW50XSA9IHRoaXMubGlzdGVuZXJzW2V2ZW50XSB8fCBbXTtcblxuICAgICAgaWYgKCF0aGlzLmV2ZW50c1tldmVudF0pIHtcbiAgICAgICAgdGhpcy5ldmVudHNbZXZlbnRdID0gZnVuY3Rpb24gKGNoYW5uZWwsIGRhdGEpIHtcbiAgICAgICAgICBpZiAoX3RoaXMyLm5hbWUgPT09IGNoYW5uZWwgJiYgX3RoaXMyLmxpc3RlbmVyc1tldmVudF0pIHtcbiAgICAgICAgICAgIF90aGlzMi5saXN0ZW5lcnNbZXZlbnRdLmZvckVhY2goZnVuY3Rpb24gKGNiKSB7XG4gICAgICAgICAgICAgIHJldHVybiBjYihkYXRhKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLnNvY2tldC5vbihldmVudCwgdGhpcy5ldmVudHNbZXZlbnRdKTtcbiAgICAgIH1cblxuICAgICAgdGhpcy5saXN0ZW5lcnNbZXZlbnRdLnB1c2goY2FsbGJhY2spO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogVW5iaW5kIHRoZSBjaGFubmVsJ3Mgc29ja2V0IGZyb20gYWxsIHN0b3JlZCBldmVudCBjYWxsYmFja3MuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInVuYmluZFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiB1bmJpbmQoKSB7XG4gICAgICB2YXIgX3RoaXMzID0gdGhpcztcblxuICAgICAgT2JqZWN0LmtleXModGhpcy5ldmVudHMpLmZvckVhY2goZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgIF90aGlzMy51bmJpbmRFdmVudChldmVudCk7XG4gICAgICB9KTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBVbmJpbmQgdGhlIGxpc3RlbmVycyBmb3IgdGhlIGdpdmVuIGV2ZW50LlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJ1bmJpbmRFdmVudFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiB1bmJpbmRFdmVudChldmVudCwgY2FsbGJhY2spIHtcbiAgICAgIHRoaXMubGlzdGVuZXJzW2V2ZW50XSA9IHRoaXMubGlzdGVuZXJzW2V2ZW50XSB8fCBbXTtcblxuICAgICAgaWYgKGNhbGxiYWNrKSB7XG4gICAgICAgIHRoaXMubGlzdGVuZXJzW2V2ZW50XSA9IHRoaXMubGlzdGVuZXJzW2V2ZW50XS5maWx0ZXIoZnVuY3Rpb24gKGNiKSB7XG4gICAgICAgICAgcmV0dXJuIGNiICE9PSBjYWxsYmFjaztcbiAgICAgICAgfSk7XG4gICAgICB9XG5cbiAgICAgIGlmICghY2FsbGJhY2sgfHwgdGhpcy5saXN0ZW5lcnNbZXZlbnRdLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICBpZiAodGhpcy5ldmVudHNbZXZlbnRdKSB7XG4gICAgICAgICAgdGhpcy5zb2NrZXQucmVtb3ZlTGlzdGVuZXIoZXZlbnQsIHRoaXMuZXZlbnRzW2V2ZW50XSk7XG4gICAgICAgICAgZGVsZXRlIHRoaXMuZXZlbnRzW2V2ZW50XTtcbiAgICAgICAgfVxuXG4gICAgICAgIGRlbGV0ZSB0aGlzLmxpc3RlbmVyc1tldmVudF07XG4gICAgICB9XG4gICAgfVxuICB9XSk7XG5cbiAgcmV0dXJuIFNvY2tldElvQ2hhbm5lbDtcbn0oQ2hhbm5lbCk7XG5cbi8qKlxyXG4gKiBUaGlzIGNsYXNzIHJlcHJlc2VudHMgYSBTb2NrZXQuaW8gcHJpdmF0ZSBjaGFubmVsLlxyXG4gKi9cblxudmFyIFNvY2tldElvUHJpdmF0ZUNoYW5uZWwgPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKF9Tb2NrZXRJb0NoYW5uZWwpIHtcbiAgX2luaGVyaXRzKFNvY2tldElvUHJpdmF0ZUNoYW5uZWwsIF9Tb2NrZXRJb0NoYW5uZWwpO1xuXG4gIHZhciBfc3VwZXIgPSBfY3JlYXRlU3VwZXIoU29ja2V0SW9Qcml2YXRlQ2hhbm5lbCk7XG5cbiAgZnVuY3Rpb24gU29ja2V0SW9Qcml2YXRlQ2hhbm5lbCgpIHtcbiAgICBfY2xhc3NDYWxsQ2hlY2sodGhpcywgU29ja2V0SW9Qcml2YXRlQ2hhbm5lbCk7XG5cbiAgICByZXR1cm4gX3N1cGVyLmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG4gIH1cblxuICBfY3JlYXRlQ2xhc3MoU29ja2V0SW9Qcml2YXRlQ2hhbm5lbCwgW3tcbiAgICBrZXk6IFwid2hpc3BlclwiLFxuICAgIHZhbHVlOlxuICAgIC8qKlxyXG4gICAgICogU2VuZCBhIHdoaXNwZXIgZXZlbnQgdG8gb3RoZXIgY2xpZW50cyBpbiB0aGUgY2hhbm5lbC5cclxuICAgICAqL1xuICAgIGZ1bmN0aW9uIHdoaXNwZXIoZXZlbnROYW1lLCBkYXRhKSB7XG4gICAgICB0aGlzLnNvY2tldC5lbWl0KCdjbGllbnQgZXZlbnQnLCB7XG4gICAgICAgIGNoYW5uZWw6IHRoaXMubmFtZSxcbiAgICAgICAgZXZlbnQ6IFwiY2xpZW50LVwiLmNvbmNhdChldmVudE5hbWUpLFxuICAgICAgICBkYXRhOiBkYXRhXG4gICAgICB9KTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgfV0pO1xuXG4gIHJldHVybiBTb2NrZXRJb1ByaXZhdGVDaGFubmVsO1xufShTb2NrZXRJb0NoYW5uZWwpO1xuXG4vKipcclxuICogVGhpcyBjbGFzcyByZXByZXNlbnRzIGEgU29ja2V0LmlvIHByZXNlbmNlIGNoYW5uZWwuXHJcbiAqL1xuXG52YXIgU29ja2V0SW9QcmVzZW5jZUNoYW5uZWwgPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKF9Tb2NrZXRJb1ByaXZhdGVDaGFubikge1xuICBfaW5oZXJpdHMoU29ja2V0SW9QcmVzZW5jZUNoYW5uZWwsIF9Tb2NrZXRJb1ByaXZhdGVDaGFubik7XG5cbiAgdmFyIF9zdXBlciA9IF9jcmVhdGVTdXBlcihTb2NrZXRJb1ByZXNlbmNlQ2hhbm5lbCk7XG5cbiAgZnVuY3Rpb24gU29ja2V0SW9QcmVzZW5jZUNoYW5uZWwoKSB7XG4gICAgX2NsYXNzQ2FsbENoZWNrKHRoaXMsIFNvY2tldElvUHJlc2VuY2VDaGFubmVsKTtcblxuICAgIHJldHVybiBfc3VwZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgfVxuXG4gIF9jcmVhdGVDbGFzcyhTb2NrZXRJb1ByZXNlbmNlQ2hhbm5lbCwgW3tcbiAgICBrZXk6IFwiaGVyZVwiLFxuICAgIHZhbHVlOlxuICAgIC8qKlxyXG4gICAgICogUmVnaXN0ZXIgYSBjYWxsYmFjayB0byBiZSBjYWxsZWQgYW55dGltZSB0aGUgbWVtYmVyIGxpc3QgY2hhbmdlcy5cclxuICAgICAqL1xuICAgIGZ1bmN0aW9uIGhlcmUoY2FsbGJhY2spIHtcbiAgICAgIHRoaXMub24oJ3ByZXNlbmNlOnN1YnNjcmliZWQnLCBmdW5jdGlvbiAobWVtYmVycykge1xuICAgICAgICBjYWxsYmFjayhtZW1iZXJzLm1hcChmdW5jdGlvbiAobSkge1xuICAgICAgICAgIHJldHVybiBtLnVzZXJfaW5mbztcbiAgICAgICAgfSkpO1xuICAgICAgfSk7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBMaXN0ZW4gZm9yIHNvbWVvbmUgam9pbmluZyB0aGUgY2hhbm5lbC5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwiam9pbmluZ1wiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBqb2luaW5nKGNhbGxiYWNrKSB7XG4gICAgICB0aGlzLm9uKCdwcmVzZW5jZTpqb2luaW5nJywgZnVuY3Rpb24gKG1lbWJlcikge1xuICAgICAgICByZXR1cm4gY2FsbGJhY2sobWVtYmVyLnVzZXJfaW5mbyk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFNlbmQgYSB3aGlzcGVyIGV2ZW50IHRvIG90aGVyIGNsaWVudHMgaW4gdGhlIGNoYW5uZWwuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcIndoaXNwZXJcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gd2hpc3BlcihldmVudE5hbWUsIGRhdGEpIHtcbiAgICAgIHRoaXMuc29ja2V0LmVtaXQoJ2NsaWVudCBldmVudCcsIHtcbiAgICAgICAgY2hhbm5lbDogdGhpcy5uYW1lLFxuICAgICAgICBldmVudDogXCJjbGllbnQtXCIuY29uY2F0KGV2ZW50TmFtZSksXG4gICAgICAgIGRhdGE6IGRhdGFcbiAgICAgIH0pO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGlzdGVuIGZvciBzb21lb25lIGxlYXZpbmcgdGhlIGNoYW5uZWwuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImxlYXZpbmdcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGVhdmluZyhjYWxsYmFjaykge1xuICAgICAgdGhpcy5vbigncHJlc2VuY2U6bGVhdmluZycsIGZ1bmN0aW9uIChtZW1iZXIpIHtcbiAgICAgICAgcmV0dXJuIGNhbGxiYWNrKG1lbWJlci51c2VyX2luZm8pO1xuICAgICAgfSk7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gIH1dKTtcblxuICByZXR1cm4gU29ja2V0SW9QcmVzZW5jZUNoYW5uZWw7XG59KFNvY2tldElvUHJpdmF0ZUNoYW5uZWwpO1xuXG4vKipcclxuICogVGhpcyBjbGFzcyByZXByZXNlbnRzIGEgbnVsbCBjaGFubmVsLlxyXG4gKi9cblxudmFyIE51bGxDaGFubmVsID0gLyojX19QVVJFX18qL2Z1bmN0aW9uIChfQ2hhbm5lbCkge1xuICBfaW5oZXJpdHMoTnVsbENoYW5uZWwsIF9DaGFubmVsKTtcblxuICB2YXIgX3N1cGVyID0gX2NyZWF0ZVN1cGVyKE51bGxDaGFubmVsKTtcblxuICBmdW5jdGlvbiBOdWxsQ2hhbm5lbCgpIHtcbiAgICBfY2xhc3NDYWxsQ2hlY2sodGhpcywgTnVsbENoYW5uZWwpO1xuXG4gICAgcmV0dXJuIF9zdXBlci5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICB9XG5cbiAgX2NyZWF0ZUNsYXNzKE51bGxDaGFubmVsLCBbe1xuICAgIGtleTogXCJzdWJzY3JpYmVcIixcbiAgICB2YWx1ZTpcbiAgICAvKipcclxuICAgICAqIFN1YnNjcmliZSB0byBhIGNoYW5uZWwuXHJcbiAgICAgKi9cbiAgICBmdW5jdGlvbiBzdWJzY3JpYmUoKSB7Ly9cbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBVbnN1YnNjcmliZSBmcm9tIGEgY2hhbm5lbC5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwidW5zdWJzY3JpYmVcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gdW5zdWJzY3JpYmUoKSB7Ly9cbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBMaXN0ZW4gZm9yIGFuIGV2ZW50IG9uIHRoZSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5cIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGlzdGVuKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGlzdGVuIGZvciBhbGwgZXZlbnRzIG9uIHRoZSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5Ub0FsbFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsaXN0ZW5Ub0FsbChjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogU3RvcCBsaXN0ZW5pbmcgZm9yIGFuIGV2ZW50IG9uIHRoZSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJzdG9wTGlzdGVuaW5nXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHN0b3BMaXN0ZW5pbmcoZXZlbnQsIGNhbGxiYWNrKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBSZWdpc3RlciBhIGNhbGxiYWNrIHRvIGJlIGNhbGxlZCBhbnl0aW1lIGEgc3Vic2NyaXB0aW9uIHN1Y2NlZWRzLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJzdWJzY3JpYmVkXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHN1YnNjcmliZWQoY2FsbGJhY2spIHtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyIGEgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIGFueXRpbWUgYW4gZXJyb3Igb2NjdXJzLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJlcnJvclwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBlcnJvcihjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogQmluZCBhIGNoYW5uZWwgdG8gYW4gZXZlbnQuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcIm9uXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIG9uKGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICB9XSk7XG5cbiAgcmV0dXJuIE51bGxDaGFubmVsO1xufShDaGFubmVsKTtcblxuLyoqXHJcbiAqIFRoaXMgY2xhc3MgcmVwcmVzZW50cyBhIG51bGwgcHJpdmF0ZSBjaGFubmVsLlxyXG4gKi9cblxudmFyIE51bGxQcml2YXRlQ2hhbm5lbCA9IC8qI19fUFVSRV9fKi9mdW5jdGlvbiAoX051bGxDaGFubmVsKSB7XG4gIF9pbmhlcml0cyhOdWxsUHJpdmF0ZUNoYW5uZWwsIF9OdWxsQ2hhbm5lbCk7XG5cbiAgdmFyIF9zdXBlciA9IF9jcmVhdGVTdXBlcihOdWxsUHJpdmF0ZUNoYW5uZWwpO1xuXG4gIGZ1bmN0aW9uIE51bGxQcml2YXRlQ2hhbm5lbCgpIHtcbiAgICBfY2xhc3NDYWxsQ2hlY2sodGhpcywgTnVsbFByaXZhdGVDaGFubmVsKTtcblxuICAgIHJldHVybiBfc3VwZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgfVxuXG4gIF9jcmVhdGVDbGFzcyhOdWxsUHJpdmF0ZUNoYW5uZWwsIFt7XG4gICAga2V5OiBcIndoaXNwZXJcIixcbiAgICB2YWx1ZTpcbiAgICAvKipcclxuICAgICAqIFNlbmQgYSB3aGlzcGVyIGV2ZW50IHRvIG90aGVyIGNsaWVudHMgaW4gdGhlIGNoYW5uZWwuXHJcbiAgICAgKi9cbiAgICBmdW5jdGlvbiB3aGlzcGVyKGV2ZW50TmFtZSwgZGF0YSkge1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfVxuICB9XSk7XG5cbiAgcmV0dXJuIE51bGxQcml2YXRlQ2hhbm5lbDtcbn0oTnVsbENoYW5uZWwpO1xuXG4vKipcclxuICogVGhpcyBjbGFzcyByZXByZXNlbnRzIGEgbnVsbCBwcmVzZW5jZSBjaGFubmVsLlxyXG4gKi9cblxudmFyIE51bGxQcmVzZW5jZUNoYW5uZWwgPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKF9OdWxsQ2hhbm5lbCkge1xuICBfaW5oZXJpdHMoTnVsbFByZXNlbmNlQ2hhbm5lbCwgX051bGxDaGFubmVsKTtcblxuICB2YXIgX3N1cGVyID0gX2NyZWF0ZVN1cGVyKE51bGxQcmVzZW5jZUNoYW5uZWwpO1xuXG4gIGZ1bmN0aW9uIE51bGxQcmVzZW5jZUNoYW5uZWwoKSB7XG4gICAgX2NsYXNzQ2FsbENoZWNrKHRoaXMsIE51bGxQcmVzZW5jZUNoYW5uZWwpO1xuXG4gICAgcmV0dXJuIF9zdXBlci5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICB9XG5cbiAgX2NyZWF0ZUNsYXNzKE51bGxQcmVzZW5jZUNoYW5uZWwsIFt7XG4gICAga2V5OiBcImhlcmVcIixcbiAgICB2YWx1ZTpcbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyIGEgY2FsbGJhY2sgdG8gYmUgY2FsbGVkIGFueXRpbWUgdGhlIG1lbWJlciBsaXN0IGNoYW5nZXMuXHJcbiAgICAgKi9cbiAgICBmdW5jdGlvbiBoZXJlKGNhbGxiYWNrKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBMaXN0ZW4gZm9yIHNvbWVvbmUgam9pbmluZyB0aGUgY2hhbm5lbC5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwiam9pbmluZ1wiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBqb2luaW5nKGNhbGxiYWNrKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBTZW5kIGEgd2hpc3BlciBldmVudCB0byBvdGhlciBjbGllbnRzIGluIHRoZSBjaGFubmVsLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJ3aGlzcGVyXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHdoaXNwZXIoZXZlbnROYW1lLCBkYXRhKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBMaXN0ZW4gZm9yIHNvbWVvbmUgbGVhdmluZyB0aGUgY2hhbm5lbC5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwibGVhdmluZ1wiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsZWF2aW5nKGNhbGxiYWNrKSB7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gIH1dKTtcblxuICByZXR1cm4gTnVsbFByZXNlbmNlQ2hhbm5lbDtcbn0oTnVsbENoYW5uZWwpO1xuXG52YXIgQ29ubmVjdG9yID0gLyojX19QVVJFX18qL2Z1bmN0aW9uICgpIHtcbiAgLyoqXHJcbiAgICogQ3JlYXRlIGEgbmV3IGNsYXNzIGluc3RhbmNlLlxyXG4gICAqL1xuICBmdW5jdGlvbiBDb25uZWN0b3Iob3B0aW9ucykge1xuICAgIF9jbGFzc0NhbGxDaGVjayh0aGlzLCBDb25uZWN0b3IpO1xuXG4gICAgLyoqXHJcbiAgICAgKiBEZWZhdWx0IGNvbm5lY3RvciBvcHRpb25zLlxyXG4gICAgICovXG4gICAgdGhpcy5fZGVmYXVsdE9wdGlvbnMgPSB7XG4gICAgICBhdXRoOiB7XG4gICAgICAgIGhlYWRlcnM6IHt9XG4gICAgICB9LFxuICAgICAgYXV0aEVuZHBvaW50OiAnL2Jyb2FkY2FzdGluZy9hdXRoJyxcbiAgICAgIHVzZXJBdXRoZW50aWNhdGlvbjoge1xuICAgICAgICBlbmRwb2ludDogJy9icm9hZGNhc3RpbmcvdXNlci1hdXRoJyxcbiAgICAgICAgaGVhZGVyczoge31cbiAgICAgIH0sXG4gICAgICBicm9hZGNhc3RlcjogJ3B1c2hlcicsXG4gICAgICBjc3JmVG9rZW46IG51bGwsXG4gICAgICBiZWFyZXJUb2tlbjogbnVsbCxcbiAgICAgIGhvc3Q6IG51bGwsXG4gICAgICBrZXk6IG51bGwsXG4gICAgICBuYW1lc3BhY2U6ICdBcHAuRXZlbnRzJ1xuICAgIH07XG4gICAgdGhpcy5zZXRPcHRpb25zKG9wdGlvbnMpO1xuICAgIHRoaXMuY29ubmVjdCgpO1xuICB9XG4gIC8qKlxyXG4gICAqIE1lcmdlIHRoZSBjdXN0b20gb3B0aW9ucyB3aXRoIHRoZSBkZWZhdWx0cy5cclxuICAgKi9cblxuXG4gIF9jcmVhdGVDbGFzcyhDb25uZWN0b3IsIFt7XG4gICAga2V5OiBcInNldE9wdGlvbnNcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gc2V0T3B0aW9ucyhvcHRpb25zKSB7XG4gICAgICB0aGlzLm9wdGlvbnMgPSBfZXh0ZW5kcyh0aGlzLl9kZWZhdWx0T3B0aW9ucywgb3B0aW9ucyk7XG4gICAgICB2YXIgdG9rZW4gPSB0aGlzLmNzcmZUb2tlbigpO1xuXG4gICAgICBpZiAodG9rZW4pIHtcbiAgICAgICAgdGhpcy5vcHRpb25zLmF1dGguaGVhZGVyc1snWC1DU1JGLVRPS0VOJ10gPSB0b2tlbjtcbiAgICAgICAgdGhpcy5vcHRpb25zLnVzZXJBdXRoZW50aWNhdGlvbi5oZWFkZXJzWydYLUNTUkYtVE9LRU4nXSA9IHRva2VuO1xuICAgICAgfVxuXG4gICAgICB0b2tlbiA9IHRoaXMub3B0aW9ucy5iZWFyZXJUb2tlbjtcblxuICAgICAgaWYgKHRva2VuKSB7XG4gICAgICAgIHRoaXMub3B0aW9ucy5hdXRoLmhlYWRlcnNbJ0F1dGhvcml6YXRpb24nXSA9ICdCZWFyZXIgJyArIHRva2VuO1xuICAgICAgICB0aGlzLm9wdGlvbnMudXNlckF1dGhlbnRpY2F0aW9uLmhlYWRlcnNbJ0F1dGhvcml6YXRpb24nXSA9ICdCZWFyZXIgJyArIHRva2VuO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gb3B0aW9ucztcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBFeHRyYWN0IHRoZSBDU1JGIHRva2VuIGZyb20gdGhlIHBhZ2UuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImNzcmZUb2tlblwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBjc3JmVG9rZW4oKSB7XG4gICAgICB2YXIgc2VsZWN0b3I7XG5cbiAgICAgIGlmICh0eXBlb2Ygd2luZG93ICE9PSAndW5kZWZpbmVkJyAmJiB3aW5kb3dbJ0xhcmF2ZWwnXSAmJiB3aW5kb3dbJ0xhcmF2ZWwnXS5jc3JmVG9rZW4pIHtcbiAgICAgICAgcmV0dXJuIHdpbmRvd1snTGFyYXZlbCddLmNzcmZUb2tlbjtcbiAgICAgIH0gZWxzZSBpZiAodGhpcy5vcHRpb25zLmNzcmZUb2tlbikge1xuICAgICAgICByZXR1cm4gdGhpcy5vcHRpb25zLmNzcmZUb2tlbjtcbiAgICAgIH0gZWxzZSBpZiAodHlwZW9mIGRvY3VtZW50ICE9PSAndW5kZWZpbmVkJyAmJiB0eXBlb2YgZG9jdW1lbnQucXVlcnlTZWxlY3RvciA9PT0gJ2Z1bmN0aW9uJyAmJiAoc2VsZWN0b3IgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdtZXRhW25hbWU9XCJjc3JmLXRva2VuXCJdJykpKSB7XG4gICAgICAgIHJldHVybiBzZWxlY3Rvci5nZXRBdHRyaWJ1dGUoJ2NvbnRlbnQnKTtcbiAgICAgIH1cblxuICAgICAgcmV0dXJuIG51bGw7XG4gICAgfVxuICB9XSk7XG5cbiAgcmV0dXJuIENvbm5lY3Rvcjtcbn0oKTtcblxuLyoqXHJcbiAqIFRoaXMgY2xhc3MgY3JlYXRlcyBhIGNvbm5lY3RvciB0byBQdXNoZXIuXHJcbiAqL1xuXG52YXIgUHVzaGVyQ29ubmVjdG9yID0gLyojX19QVVJFX18qL2Z1bmN0aW9uIChfQ29ubmVjdG9yKSB7XG4gIF9pbmhlcml0cyhQdXNoZXJDb25uZWN0b3IsIF9Db25uZWN0b3IpO1xuXG4gIHZhciBfc3VwZXIgPSBfY3JlYXRlU3VwZXIoUHVzaGVyQ29ubmVjdG9yKTtcblxuICBmdW5jdGlvbiBQdXNoZXJDb25uZWN0b3IoKSB7XG4gICAgdmFyIF90aGlzO1xuXG4gICAgX2NsYXNzQ2FsbENoZWNrKHRoaXMsIFB1c2hlckNvbm5lY3Rvcik7XG5cbiAgICBfdGhpcyA9IF9zdXBlci5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICAgIC8qKlxyXG4gICAgICogQWxsIG9mIHRoZSBzdWJzY3JpYmVkIGNoYW5uZWwgbmFtZXMuXHJcbiAgICAgKi9cblxuICAgIF90aGlzLmNoYW5uZWxzID0ge307XG4gICAgcmV0dXJuIF90aGlzO1xuICB9XG4gIC8qKlxyXG4gICAqIENyZWF0ZSBhIGZyZXNoIFB1c2hlciBjb25uZWN0aW9uLlxyXG4gICAqL1xuXG5cbiAgX2NyZWF0ZUNsYXNzKFB1c2hlckNvbm5lY3RvciwgW3tcbiAgICBrZXk6IFwiY29ubmVjdFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBjb25uZWN0KCkge1xuICAgICAgaWYgKHR5cGVvZiB0aGlzLm9wdGlvbnMuY2xpZW50ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICB0aGlzLnB1c2hlciA9IHRoaXMub3B0aW9ucy5jbGllbnQ7XG4gICAgICB9IGVsc2UgaWYgKHRoaXMub3B0aW9ucy5QdXNoZXIpIHtcbiAgICAgICAgdGhpcy5wdXNoZXIgPSBuZXcgdGhpcy5vcHRpb25zLlB1c2hlcih0aGlzLm9wdGlvbnMua2V5LCB0aGlzLm9wdGlvbnMpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5wdXNoZXIgPSBuZXcgUHVzaGVyKHRoaXMub3B0aW9ucy5rZXksIHRoaXMub3B0aW9ucyk7XG4gICAgICB9XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogU2lnbiBpbiB0aGUgdXNlciB2aWEgUHVzaGVyIHVzZXIgYXV0aGVudGljYXRpb24gKGh0dHBzOi8vcHVzaGVyLmNvbS9kb2NzL2NoYW5uZWxzL3VzaW5nX2NoYW5uZWxzL3VzZXItYXV0aGVudGljYXRpb24vKS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwic2lnbmluXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHNpZ25pbigpIHtcbiAgICAgIHRoaXMucHVzaGVyLnNpZ25pbigpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExpc3RlbiBmb3IgYW4gZXZlbnQgb24gYSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5cIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGlzdGVuKG5hbWUsIGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXMuY2hhbm5lbChuYW1lKS5saXN0ZW4oZXZlbnQsIGNhbGxiYWNrKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYSBjaGFubmVsIGluc3RhbmNlIGJ5IG5hbWUuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImNoYW5uZWxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gY2hhbm5lbChuYW1lKSB7XG4gICAgICBpZiAoIXRoaXMuY2hhbm5lbHNbbmFtZV0pIHtcbiAgICAgICAgdGhpcy5jaGFubmVsc1tuYW1lXSA9IG5ldyBQdXNoZXJDaGFubmVsKHRoaXMucHVzaGVyLCBuYW1lLCB0aGlzLm9wdGlvbnMpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcy5jaGFubmVsc1tuYW1lXTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYSBwcml2YXRlIGNoYW5uZWwgaW5zdGFuY2UgYnkgbmFtZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwicHJpdmF0ZUNoYW5uZWxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gcHJpdmF0ZUNoYW5uZWwobmFtZSkge1xuICAgICAgaWYgKCF0aGlzLmNoYW5uZWxzWydwcml2YXRlLScgKyBuYW1lXSkge1xuICAgICAgICB0aGlzLmNoYW5uZWxzWydwcml2YXRlLScgKyBuYW1lXSA9IG5ldyBQdXNoZXJQcml2YXRlQ2hhbm5lbCh0aGlzLnB1c2hlciwgJ3ByaXZhdGUtJyArIG5hbWUsIHRoaXMub3B0aW9ucyk7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzLmNoYW5uZWxzWydwcml2YXRlLScgKyBuYW1lXTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYSBwcml2YXRlIGVuY3J5cHRlZCBjaGFubmVsIGluc3RhbmNlIGJ5IG5hbWUuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImVuY3J5cHRlZFByaXZhdGVDaGFubmVsXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGVuY3J5cHRlZFByaXZhdGVDaGFubmVsKG5hbWUpIHtcbiAgICAgIGlmICghdGhpcy5jaGFubmVsc1sncHJpdmF0ZS1lbmNyeXB0ZWQtJyArIG5hbWVdKSB7XG4gICAgICAgIHRoaXMuY2hhbm5lbHNbJ3ByaXZhdGUtZW5jcnlwdGVkLScgKyBuYW1lXSA9IG5ldyBQdXNoZXJFbmNyeXB0ZWRQcml2YXRlQ2hhbm5lbCh0aGlzLnB1c2hlciwgJ3ByaXZhdGUtZW5jcnlwdGVkLScgKyBuYW1lLCB0aGlzLm9wdGlvbnMpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcy5jaGFubmVsc1sncHJpdmF0ZS1lbmNyeXB0ZWQtJyArIG5hbWVdO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIEdldCBhIHByZXNlbmNlIGNoYW5uZWwgaW5zdGFuY2UgYnkgbmFtZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwicHJlc2VuY2VDaGFubmVsXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHByZXNlbmNlQ2hhbm5lbChuYW1lKSB7XG4gICAgICBpZiAoIXRoaXMuY2hhbm5lbHNbJ3ByZXNlbmNlLScgKyBuYW1lXSkge1xuICAgICAgICB0aGlzLmNoYW5uZWxzWydwcmVzZW5jZS0nICsgbmFtZV0gPSBuZXcgUHVzaGVyUHJlc2VuY2VDaGFubmVsKHRoaXMucHVzaGVyLCAncHJlc2VuY2UtJyArIG5hbWUsIHRoaXMub3B0aW9ucyk7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzLmNoYW5uZWxzWydwcmVzZW5jZS0nICsgbmFtZV07XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGVhdmUgdGhlIGdpdmVuIGNoYW5uZWwsIGFzIHdlbGwgYXMgaXRzIHByaXZhdGUgYW5kIHByZXNlbmNlIHZhcmlhbnRzLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsZWF2ZVwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsZWF2ZShuYW1lKSB7XG4gICAgICB2YXIgX3RoaXMyID0gdGhpcztcblxuICAgICAgdmFyIGNoYW5uZWxzID0gW25hbWUsICdwcml2YXRlLScgKyBuYW1lLCAncHJpdmF0ZS1lbmNyeXB0ZWQtJyArIG5hbWUsICdwcmVzZW5jZS0nICsgbmFtZV07XG4gICAgICBjaGFubmVscy5mb3JFYWNoKGZ1bmN0aW9uIChuYW1lLCBpbmRleCkge1xuICAgICAgICBfdGhpczIubGVhdmVDaGFubmVsKG5hbWUpO1xuICAgICAgfSk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGVhdmUgdGhlIGdpdmVuIGNoYW5uZWwuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImxlYXZlQ2hhbm5lbFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsZWF2ZUNoYW5uZWwobmFtZSkge1xuICAgICAgaWYgKHRoaXMuY2hhbm5lbHNbbmFtZV0pIHtcbiAgICAgICAgdGhpcy5jaGFubmVsc1tuYW1lXS51bnN1YnNjcmliZSgpO1xuICAgICAgICBkZWxldGUgdGhpcy5jaGFubmVsc1tuYW1lXTtcbiAgICAgIH1cbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgdGhlIHNvY2tldCBJRCBmb3IgdGhlIGNvbm5lY3Rpb24uXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInNvY2tldElkXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHNvY2tldElkKCkge1xuICAgICAgcmV0dXJuIHRoaXMucHVzaGVyLmNvbm5lY3Rpb24uc29ja2V0X2lkO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIERpc2Nvbm5lY3QgUHVzaGVyIGNvbm5lY3Rpb24uXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImRpc2Nvbm5lY3RcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gZGlzY29ubmVjdCgpIHtcbiAgICAgIHRoaXMucHVzaGVyLmRpc2Nvbm5lY3QoKTtcbiAgICB9XG4gIH1dKTtcblxuICByZXR1cm4gUHVzaGVyQ29ubmVjdG9yO1xufShDb25uZWN0b3IpO1xuXG4vKipcclxuICogVGhpcyBjbGFzcyBjcmVhdGVzIGEgY29ubm5lY3RvciB0byBhIFNvY2tldC5pbyBzZXJ2ZXIuXHJcbiAqL1xuXG52YXIgU29ja2V0SW9Db25uZWN0b3IgPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKF9Db25uZWN0b3IpIHtcbiAgX2luaGVyaXRzKFNvY2tldElvQ29ubmVjdG9yLCBfQ29ubmVjdG9yKTtcblxuICB2YXIgX3N1cGVyID0gX2NyZWF0ZVN1cGVyKFNvY2tldElvQ29ubmVjdG9yKTtcblxuICBmdW5jdGlvbiBTb2NrZXRJb0Nvbm5lY3RvcigpIHtcbiAgICB2YXIgX3RoaXM7XG5cbiAgICBfY2xhc3NDYWxsQ2hlY2sodGhpcywgU29ja2V0SW9Db25uZWN0b3IpO1xuXG4gICAgX3RoaXMgPSBfc3VwZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICAvKipcclxuICAgICAqIEFsbCBvZiB0aGUgc3Vic2NyaWJlZCBjaGFubmVsIG5hbWVzLlxyXG4gICAgICovXG5cbiAgICBfdGhpcy5jaGFubmVscyA9IHt9O1xuICAgIHJldHVybiBfdGhpcztcbiAgfVxuICAvKipcclxuICAgKiBDcmVhdGUgYSBmcmVzaCBTb2NrZXQuaW8gY29ubmVjdGlvbi5cclxuICAgKi9cblxuXG4gIF9jcmVhdGVDbGFzcyhTb2NrZXRJb0Nvbm5lY3RvciwgW3tcbiAgICBrZXk6IFwiY29ubmVjdFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBjb25uZWN0KCkge1xuICAgICAgdmFyIF90aGlzMiA9IHRoaXM7XG5cbiAgICAgIHZhciBpbyA9IHRoaXMuZ2V0U29ja2V0SU8oKTtcbiAgICAgIHRoaXMuc29ja2V0ID0gaW8odGhpcy5vcHRpb25zLmhvc3QsIHRoaXMub3B0aW9ucyk7XG4gICAgICB0aGlzLnNvY2tldC5vbigncmVjb25uZWN0JywgZnVuY3Rpb24gKCkge1xuICAgICAgICBPYmplY3QudmFsdWVzKF90aGlzMi5jaGFubmVscykuZm9yRWFjaChmdW5jdGlvbiAoY2hhbm5lbCkge1xuICAgICAgICAgIGNoYW5uZWwuc3Vic2NyaWJlKCk7XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgICByZXR1cm4gdGhpcy5zb2NrZXQ7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogR2V0IHNvY2tldC5pbyBtb2R1bGUgZnJvbSBnbG9iYWwgc2NvcGUgb3Igb3B0aW9ucy5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwiZ2V0U29ja2V0SU9cIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gZ2V0U29ja2V0SU8oKSB7XG4gICAgICBpZiAodHlwZW9mIHRoaXMub3B0aW9ucy5jbGllbnQgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIHJldHVybiB0aGlzLm9wdGlvbnMuY2xpZW50O1xuICAgICAgfVxuXG4gICAgICBpZiAodHlwZW9mIGlvICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICByZXR1cm4gaW87XG4gICAgICB9XG5cbiAgICAgIHRocm93IG5ldyBFcnJvcignU29ja2V0LmlvIGNsaWVudCBub3QgZm91bmQuIFNob3VsZCBiZSBnbG9iYWxseSBhdmFpbGFibGUgb3IgcGFzc2VkIHZpYSBvcHRpb25zLmNsaWVudCcpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExpc3RlbiBmb3IgYW4gZXZlbnQgb24gYSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5cIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGlzdGVuKG5hbWUsIGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXMuY2hhbm5lbChuYW1lKS5saXN0ZW4oZXZlbnQsIGNhbGxiYWNrKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYSBjaGFubmVsIGluc3RhbmNlIGJ5IG5hbWUuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImNoYW5uZWxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gY2hhbm5lbChuYW1lKSB7XG4gICAgICBpZiAoIXRoaXMuY2hhbm5lbHNbbmFtZV0pIHtcbiAgICAgICAgdGhpcy5jaGFubmVsc1tuYW1lXSA9IG5ldyBTb2NrZXRJb0NoYW5uZWwodGhpcy5zb2NrZXQsIG5hbWUsIHRoaXMub3B0aW9ucyk7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzLmNoYW5uZWxzW25hbWVdO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIEdldCBhIHByaXZhdGUgY2hhbm5lbCBpbnN0YW5jZSBieSBuYW1lLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJwcml2YXRlQ2hhbm5lbFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBwcml2YXRlQ2hhbm5lbChuYW1lKSB7XG4gICAgICBpZiAoIXRoaXMuY2hhbm5lbHNbJ3ByaXZhdGUtJyArIG5hbWVdKSB7XG4gICAgICAgIHRoaXMuY2hhbm5lbHNbJ3ByaXZhdGUtJyArIG5hbWVdID0gbmV3IFNvY2tldElvUHJpdmF0ZUNoYW5uZWwodGhpcy5zb2NrZXQsICdwcml2YXRlLScgKyBuYW1lLCB0aGlzLm9wdGlvbnMpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcy5jaGFubmVsc1sncHJpdmF0ZS0nICsgbmFtZV07XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogR2V0IGEgcHJlc2VuY2UgY2hhbm5lbCBpbnN0YW5jZSBieSBuYW1lLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJwcmVzZW5jZUNoYW5uZWxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gcHJlc2VuY2VDaGFubmVsKG5hbWUpIHtcbiAgICAgIGlmICghdGhpcy5jaGFubmVsc1sncHJlc2VuY2UtJyArIG5hbWVdKSB7XG4gICAgICAgIHRoaXMuY2hhbm5lbHNbJ3ByZXNlbmNlLScgKyBuYW1lXSA9IG5ldyBTb2NrZXRJb1ByZXNlbmNlQ2hhbm5lbCh0aGlzLnNvY2tldCwgJ3ByZXNlbmNlLScgKyBuYW1lLCB0aGlzLm9wdGlvbnMpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcy5jaGFubmVsc1sncHJlc2VuY2UtJyArIG5hbWVdO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExlYXZlIHRoZSBnaXZlbiBjaGFubmVsLCBhcyB3ZWxsIGFzIGl0cyBwcml2YXRlIGFuZCBwcmVzZW5jZSB2YXJpYW50cy5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwibGVhdmVcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGVhdmUobmFtZSkge1xuICAgICAgdmFyIF90aGlzMyA9IHRoaXM7XG5cbiAgICAgIHZhciBjaGFubmVscyA9IFtuYW1lLCAncHJpdmF0ZS0nICsgbmFtZSwgJ3ByZXNlbmNlLScgKyBuYW1lXTtcbiAgICAgIGNoYW5uZWxzLmZvckVhY2goZnVuY3Rpb24gKG5hbWUpIHtcbiAgICAgICAgX3RoaXMzLmxlYXZlQ2hhbm5lbChuYW1lKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExlYXZlIHRoZSBnaXZlbiBjaGFubmVsLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsZWF2ZUNoYW5uZWxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGVhdmVDaGFubmVsKG5hbWUpIHtcbiAgICAgIGlmICh0aGlzLmNoYW5uZWxzW25hbWVdKSB7XG4gICAgICAgIHRoaXMuY2hhbm5lbHNbbmFtZV0udW5zdWJzY3JpYmUoKTtcbiAgICAgICAgZGVsZXRlIHRoaXMuY2hhbm5lbHNbbmFtZV07XG4gICAgICB9XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogR2V0IHRoZSBzb2NrZXQgSUQgZm9yIHRoZSBjb25uZWN0aW9uLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJzb2NrZXRJZFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBzb2NrZXRJZCgpIHtcbiAgICAgIHJldHVybiB0aGlzLnNvY2tldC5pZDtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBEaXNjb25uZWN0IFNvY2tldGlvIGNvbm5lY3Rpb24uXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImRpc2Nvbm5lY3RcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gZGlzY29ubmVjdCgpIHtcbiAgICAgIHRoaXMuc29ja2V0LmRpc2Nvbm5lY3QoKTtcbiAgICB9XG4gIH1dKTtcblxuICByZXR1cm4gU29ja2V0SW9Db25uZWN0b3I7XG59KENvbm5lY3Rvcik7XG5cbi8qKlxyXG4gKiBUaGlzIGNsYXNzIGNyZWF0ZXMgYSBudWxsIGNvbm5lY3Rvci5cclxuICovXG5cbnZhciBOdWxsQ29ubmVjdG9yID0gLyojX19QVVJFX18qL2Z1bmN0aW9uIChfQ29ubmVjdG9yKSB7XG4gIF9pbmhlcml0cyhOdWxsQ29ubmVjdG9yLCBfQ29ubmVjdG9yKTtcblxuICB2YXIgX3N1cGVyID0gX2NyZWF0ZVN1cGVyKE51bGxDb25uZWN0b3IpO1xuXG4gIGZ1bmN0aW9uIE51bGxDb25uZWN0b3IoKSB7XG4gICAgdmFyIF90aGlzO1xuXG4gICAgX2NsYXNzQ2FsbENoZWNrKHRoaXMsIE51bGxDb25uZWN0b3IpO1xuXG4gICAgX3RoaXMgPSBfc3VwZXIuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICAvKipcclxuICAgICAqIEFsbCBvZiB0aGUgc3Vic2NyaWJlZCBjaGFubmVsIG5hbWVzLlxyXG4gICAgICovXG5cbiAgICBfdGhpcy5jaGFubmVscyA9IHt9O1xuICAgIHJldHVybiBfdGhpcztcbiAgfVxuICAvKipcclxuICAgKiBDcmVhdGUgYSBmcmVzaCBjb25uZWN0aW9uLlxyXG4gICAqL1xuXG5cbiAgX2NyZWF0ZUNsYXNzKE51bGxDb25uZWN0b3IsIFt7XG4gICAga2V5OiBcImNvbm5lY3RcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gY29ubmVjdCgpIHsvL1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExpc3RlbiBmb3IgYW4gZXZlbnQgb24gYSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5cIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGlzdGVuKG5hbWUsIGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIG5ldyBOdWxsQ2hhbm5lbCgpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIEdldCBhIGNoYW5uZWwgaW5zdGFuY2UgYnkgbmFtZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwiY2hhbm5lbFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBjaGFubmVsKG5hbWUpIHtcbiAgICAgIHJldHVybiBuZXcgTnVsbENoYW5uZWwoKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYSBwcml2YXRlIGNoYW5uZWwgaW5zdGFuY2UgYnkgbmFtZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwicHJpdmF0ZUNoYW5uZWxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gcHJpdmF0ZUNoYW5uZWwobmFtZSkge1xuICAgICAgcmV0dXJuIG5ldyBOdWxsUHJpdmF0ZUNoYW5uZWwoKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYSBwcml2YXRlIGVuY3J5cHRlZCBjaGFubmVsIGluc3RhbmNlIGJ5IG5hbWUuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImVuY3J5cHRlZFByaXZhdGVDaGFubmVsXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGVuY3J5cHRlZFByaXZhdGVDaGFubmVsKG5hbWUpIHtcbiAgICAgIHJldHVybiBuZXcgTnVsbFByaXZhdGVDaGFubmVsKCk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogR2V0IGEgcHJlc2VuY2UgY2hhbm5lbCBpbnN0YW5jZSBieSBuYW1lLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJwcmVzZW5jZUNoYW5uZWxcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gcHJlc2VuY2VDaGFubmVsKG5hbWUpIHtcbiAgICAgIHJldHVybiBuZXcgTnVsbFByZXNlbmNlQ2hhbm5lbCgpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIExlYXZlIHRoZSBnaXZlbiBjaGFubmVsLCBhcyB3ZWxsIGFzIGl0cyBwcml2YXRlIGFuZCBwcmVzZW5jZSB2YXJpYW50cy5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwibGVhdmVcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGVhdmUobmFtZSkgey8vXG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGVhdmUgdGhlIGdpdmVuIGNoYW5uZWwuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImxlYXZlQ2hhbm5lbFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsZWF2ZUNoYW5uZWwobmFtZSkgey8vXG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogR2V0IHRoZSBzb2NrZXQgSUQgZm9yIHRoZSBjb25uZWN0aW9uLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJzb2NrZXRJZFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBzb2NrZXRJZCgpIHtcbiAgICAgIHJldHVybiAnZmFrZS1zb2NrZXQtaWQnO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIERpc2Nvbm5lY3QgdGhlIGNvbm5lY3Rpb24uXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImRpc2Nvbm5lY3RcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gZGlzY29ubmVjdCgpIHsvL1xuICAgIH1cbiAgfV0pO1xuXG4gIHJldHVybiBOdWxsQ29ubmVjdG9yO1xufShDb25uZWN0b3IpO1xuXG4vKipcclxuICogVGhpcyBjbGFzcyBpcyB0aGUgcHJpbWFyeSBBUEkgZm9yIGludGVyYWN0aW5nIHdpdGggYnJvYWRjYXN0aW5nLlxyXG4gKi9cblxudmFyIEVjaG8gPSAvKiNfX1BVUkVfXyovZnVuY3Rpb24gKCkge1xuICAvKipcclxuICAgKiBDcmVhdGUgYSBuZXcgY2xhc3MgaW5zdGFuY2UuXHJcbiAgICovXG4gIGZ1bmN0aW9uIEVjaG8ob3B0aW9ucykge1xuICAgIF9jbGFzc0NhbGxDaGVjayh0aGlzLCBFY2hvKTtcblxuICAgIHRoaXMub3B0aW9ucyA9IG9wdGlvbnM7XG4gICAgdGhpcy5jb25uZWN0KCk7XG5cbiAgICBpZiAoIXRoaXMub3B0aW9ucy53aXRob3V0SW50ZXJjZXB0b3JzKSB7XG4gICAgICB0aGlzLnJlZ2lzdGVySW50ZXJjZXB0b3JzKCk7XG4gICAgfVxuICB9XG4gIC8qKlxyXG4gICAqIEdldCBhIGNoYW5uZWwgaW5zdGFuY2UgYnkgbmFtZS5cclxuICAgKi9cblxuXG4gIF9jcmVhdGVDbGFzcyhFY2hvLCBbe1xuICAgIGtleTogXCJjaGFubmVsXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGNoYW5uZWwoX2NoYW5uZWwpIHtcbiAgICAgIHJldHVybiB0aGlzLmNvbm5lY3Rvci5jaGFubmVsKF9jaGFubmVsKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBDcmVhdGUgYSBuZXcgY29ubmVjdGlvbi5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwiY29ubmVjdFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBjb25uZWN0KCkge1xuICAgICAgaWYgKHRoaXMub3B0aW9ucy5icm9hZGNhc3RlciA9PSAncHVzaGVyJykge1xuICAgICAgICB0aGlzLmNvbm5lY3RvciA9IG5ldyBQdXNoZXJDb25uZWN0b3IodGhpcy5vcHRpb25zKTtcbiAgICAgIH0gZWxzZSBpZiAodGhpcy5vcHRpb25zLmJyb2FkY2FzdGVyID09ICdzb2NrZXQuaW8nKSB7XG4gICAgICAgIHRoaXMuY29ubmVjdG9yID0gbmV3IFNvY2tldElvQ29ubmVjdG9yKHRoaXMub3B0aW9ucyk7XG4gICAgICB9IGVsc2UgaWYgKHRoaXMub3B0aW9ucy5icm9hZGNhc3RlciA9PSAnbnVsbCcpIHtcbiAgICAgICAgdGhpcy5jb25uZWN0b3IgPSBuZXcgTnVsbENvbm5lY3Rvcih0aGlzLm9wdGlvbnMpO1xuICAgICAgfSBlbHNlIGlmICh0eXBlb2YgdGhpcy5vcHRpb25zLmJyb2FkY2FzdGVyID09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpcy5jb25uZWN0b3IgPSBuZXcgdGhpcy5vcHRpb25zLmJyb2FkY2FzdGVyKHRoaXMub3B0aW9ucyk7XG4gICAgICB9XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogRGlzY29ubmVjdCBmcm9tIHRoZSBFY2hvIHNlcnZlci5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwiZGlzY29ubmVjdFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBkaXNjb25uZWN0KCkge1xuICAgICAgdGhpcy5jb25uZWN0b3IuZGlzY29ubmVjdCgpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIEdldCBhIHByZXNlbmNlIGNoYW5uZWwgaW5zdGFuY2UgYnkgbmFtZS5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwiam9pblwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBqb2luKGNoYW5uZWwpIHtcbiAgICAgIHJldHVybiB0aGlzLmNvbm5lY3Rvci5wcmVzZW5jZUNoYW5uZWwoY2hhbm5lbCk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGVhdmUgdGhlIGdpdmVuIGNoYW5uZWwsIGFzIHdlbGwgYXMgaXRzIHByaXZhdGUgYW5kIHByZXNlbmNlIHZhcmlhbnRzLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsZWF2ZVwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBsZWF2ZShjaGFubmVsKSB7XG4gICAgICB0aGlzLmNvbm5lY3Rvci5sZWF2ZShjaGFubmVsKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBMZWF2ZSB0aGUgZ2l2ZW4gY2hhbm5lbC5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwibGVhdmVDaGFubmVsXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGxlYXZlQ2hhbm5lbChjaGFubmVsKSB7XG4gICAgICB0aGlzLmNvbm5lY3Rvci5sZWF2ZUNoYW5uZWwoY2hhbm5lbCk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogTGVhdmUgYWxsIGNoYW5uZWxzLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsZWF2ZUFsbENoYW5uZWxzXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIGxlYXZlQWxsQ2hhbm5lbHMoKSB7XG4gICAgICBmb3IgKHZhciBjaGFubmVsIGluIHRoaXMuY29ubmVjdG9yLmNoYW5uZWxzKSB7XG4gICAgICAgIHRoaXMubGVhdmVDaGFubmVsKGNoYW5uZWwpO1xuICAgICAgfVxuICAgIH1cbiAgICAvKipcclxuICAgICAqIExpc3RlbiBmb3IgYW4gZXZlbnQgb24gYSBjaGFubmVsIGluc3RhbmNlLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJsaXN0ZW5cIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gbGlzdGVuKGNoYW5uZWwsIGV2ZW50LCBjYWxsYmFjaykge1xuICAgICAgcmV0dXJuIHRoaXMuY29ubmVjdG9yLmxpc3RlbihjaGFubmVsLCBldmVudCwgY2FsbGJhY2spO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIEdldCBhIHByaXZhdGUgY2hhbm5lbCBpbnN0YW5jZSBieSBuYW1lLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJwcml2YXRlXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIF9wcml2YXRlKGNoYW5uZWwpIHtcbiAgICAgIHJldHVybiB0aGlzLmNvbm5lY3Rvci5wcml2YXRlQ2hhbm5lbChjaGFubmVsKTtcbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBHZXQgYSBwcml2YXRlIGVuY3J5cHRlZCBjaGFubmVsIGluc3RhbmNlIGJ5IG5hbWUuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcImVuY3J5cHRlZFByaXZhdGVcIixcbiAgICB2YWx1ZTogZnVuY3Rpb24gZW5jcnlwdGVkUHJpdmF0ZShjaGFubmVsKSB7XG4gICAgICByZXR1cm4gdGhpcy5jb25uZWN0b3IuZW5jcnlwdGVkUHJpdmF0ZUNoYW5uZWwoY2hhbm5lbCk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogR2V0IHRoZSBTb2NrZXQgSUQgZm9yIHRoZSBjb25uZWN0aW9uLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJzb2NrZXRJZFwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiBzb2NrZXRJZCgpIHtcbiAgICAgIHJldHVybiB0aGlzLmNvbm5lY3Rvci5zb2NrZXRJZCgpO1xuICAgIH1cbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyIDNyZCBwYXJ0eSByZXF1ZXN0IGludGVyY2VwdGlvcnMuIFRoZXNlIGFyZSB1c2VkIHRvIGF1dG9tYXRpY2FsbHlcclxuICAgICAqIHNlbmQgYSBjb25uZWN0aW9ucyBzb2NrZXQgaWQgdG8gYSBMYXJhdmVsIGFwcCB3aXRoIGEgWC1Tb2NrZXQtSWQgaGVhZGVyLlxyXG4gICAgICovXG5cbiAgfSwge1xuICAgIGtleTogXCJyZWdpc3RlckludGVyY2VwdG9yc1wiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiByZWdpc3RlckludGVyY2VwdG9ycygpIHtcbiAgICAgIGlmICh0eXBlb2YgVnVlID09PSAnZnVuY3Rpb24nICYmIFZ1ZS5odHRwKSB7XG4gICAgICAgIHRoaXMucmVnaXN0ZXJWdWVSZXF1ZXN0SW50ZXJjZXB0b3IoKTtcbiAgICAgIH1cblxuICAgICAgaWYgKHR5cGVvZiBheGlvcyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aGlzLnJlZ2lzdGVyQXhpb3NSZXF1ZXN0SW50ZXJjZXB0b3IoKTtcbiAgICAgIH1cblxuICAgICAgaWYgKHR5cGVvZiBqUXVlcnkgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpcy5yZWdpc3RlcmpRdWVyeUFqYXhTZXR1cCgpO1xuICAgICAgfVxuXG4gICAgICBpZiAoKHR5cGVvZiBUdXJibyA9PT0gXCJ1bmRlZmluZWRcIiA/IFwidW5kZWZpbmVkXCIgOiBfdHlwZW9mKFR1cmJvKSkgPT09ICdvYmplY3QnKSB7XG4gICAgICAgIHRoaXMucmVnaXN0ZXJUdXJib1JlcXVlc3RJbnRlcmNlcHRvcigpO1xuICAgICAgfVxuICAgIH1cbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyIGEgVnVlIEhUVFAgaW50ZXJjZXB0b3IgdG8gYWRkIHRoZSBYLVNvY2tldC1JRCBoZWFkZXIuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInJlZ2lzdGVyVnVlUmVxdWVzdEludGVyY2VwdG9yXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHJlZ2lzdGVyVnVlUmVxdWVzdEludGVyY2VwdG9yKCkge1xuICAgICAgdmFyIF90aGlzID0gdGhpcztcblxuICAgICAgVnVlLmh0dHAuaW50ZXJjZXB0b3JzLnB1c2goZnVuY3Rpb24gKHJlcXVlc3QsIG5leHQpIHtcbiAgICAgICAgaWYgKF90aGlzLnNvY2tldElkKCkpIHtcbiAgICAgICAgICByZXF1ZXN0LmhlYWRlcnMuc2V0KCdYLVNvY2tldC1JRCcsIF90aGlzLnNvY2tldElkKCkpO1xuICAgICAgICB9XG5cbiAgICAgICAgbmV4dCgpO1xuICAgICAgfSk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogUmVnaXN0ZXIgYW4gQXhpb3MgSFRUUCBpbnRlcmNlcHRvciB0byBhZGQgdGhlIFgtU29ja2V0LUlEIGhlYWRlci5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwicmVnaXN0ZXJBeGlvc1JlcXVlc3RJbnRlcmNlcHRvclwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiByZWdpc3RlckF4aW9zUmVxdWVzdEludGVyY2VwdG9yKCkge1xuICAgICAgdmFyIF90aGlzMiA9IHRoaXM7XG5cbiAgICAgIGF4aW9zLmludGVyY2VwdG9ycy5yZXF1ZXN0LnVzZShmdW5jdGlvbiAoY29uZmlnKSB7XG4gICAgICAgIGlmIChfdGhpczIuc29ja2V0SWQoKSkge1xuICAgICAgICAgIGNvbmZpZy5oZWFkZXJzWydYLVNvY2tldC1JZCddID0gX3RoaXMyLnNvY2tldElkKCk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gY29uZmlnO1xuICAgICAgfSk7XG4gICAgfVxuICAgIC8qKlxyXG4gICAgICogUmVnaXN0ZXIgalF1ZXJ5IEFqYXhQcmVmaWx0ZXIgdG8gYWRkIHRoZSBYLVNvY2tldC1JRCBoZWFkZXIuXHJcbiAgICAgKi9cblxuICB9LCB7XG4gICAga2V5OiBcInJlZ2lzdGVyalF1ZXJ5QWpheFNldHVwXCIsXG4gICAgdmFsdWU6IGZ1bmN0aW9uIHJlZ2lzdGVyalF1ZXJ5QWpheFNldHVwKCkge1xuICAgICAgdmFyIF90aGlzMyA9IHRoaXM7XG5cbiAgICAgIGlmICh0eXBlb2YgalF1ZXJ5LmFqYXggIT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgalF1ZXJ5LmFqYXhQcmVmaWx0ZXIoZnVuY3Rpb24gKG9wdGlvbnMsIG9yaWdpbmFsT3B0aW9ucywgeGhyKSB7XG4gICAgICAgICAgaWYgKF90aGlzMy5zb2NrZXRJZCgpKSB7XG4gICAgICAgICAgICB4aHIuc2V0UmVxdWVzdEhlYWRlcignWC1Tb2NrZXQtSWQnLCBfdGhpczMuc29ja2V0SWQoKSk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9XG4gICAgLyoqXHJcbiAgICAgKiBSZWdpc3RlciB0aGUgVHVyYm8gUmVxdWVzdCBpbnRlcmNlcHRvciB0byBhZGQgdGhlIFgtU29ja2V0LUlEIGhlYWRlci5cclxuICAgICAqL1xuXG4gIH0sIHtcbiAgICBrZXk6IFwicmVnaXN0ZXJUdXJib1JlcXVlc3RJbnRlcmNlcHRvclwiLFxuICAgIHZhbHVlOiBmdW5jdGlvbiByZWdpc3RlclR1cmJvUmVxdWVzdEludGVyY2VwdG9yKCkge1xuICAgICAgdmFyIF90aGlzNCA9IHRoaXM7XG5cbiAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ3R1cmJvOmJlZm9yZS1mZXRjaC1yZXF1ZXN0JywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgIGV2ZW50LmRldGFpbC5mZXRjaE9wdGlvbnMuaGVhZGVyc1snWC1Tb2NrZXQtSWQnXSA9IF90aGlzNC5zb2NrZXRJZCgpO1xuICAgICAgfSk7XG4gICAgfVxuICB9XSk7XG5cbiAgcmV0dXJuIEVjaG87XG59KCk7XG5cbmV4cG9ydCB7IENoYW5uZWwsIEVjaG8gYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBFY2hvIGZyb20gJ2xhcmF2ZWwtZWNobydcbmltcG9ydCBQdXNoZXIgZnJvbSAncHVzaGVyLWpzL2Rpc3Qvd2ViL3B1c2hlcidcblxud2luZG93LkVjaG9GYWN0b3J5ID0gRWNob1xud2luZG93LlB1c2hlciA9IFB1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Q2hELFlBQUksU0FBUztBQUViLGNBQU0sYUFBYSxRQUFRLElBQUksU0FBVSxRQUFRO0FBQy9DLGNBQUksT0FBTyxTQUFTLEdBQUc7QUFDckIsbUJBQU8sUUFBUSxhQUFhLElBQUksT0FBTyxTQUFTO0FBQUEsVUFDbEQ7QUFFQSxpQkFBTztBQUFBLFFBQ1QsQ0FBQztBQUFBLE1BQ0g7QUFBQTtBQUFBO0FBQUE7QUFBQSxJQUtGLEdBQUc7QUFBQSxNQUNELEtBQUs7QUFBQSxNQUNMLE9BQU8sU0FBUywwQkFBMEI7QUFDeEMsWUFBSSxTQUFTO0FBRWIsWUFBSSxPQUFPLE9BQU8sUUFBUSxhQUFhO0FBQ3JDLGlCQUFPLGNBQWMsU0FBVSxTQUFTLGlCQUFpQixLQUFLO0FBQzVELGdCQUFJLE9BQU8sU0FBUyxHQUFHO0FBQ3JCLGtCQUFJLGlCQUFpQixlQUFlLE9BQU8sU0FBUyxDQUFDO0FBQUEsWUFDdkQ7QUFBQSxVQUNGLENBQUM7QUFBQSxRQUNIO0FBQUEsTUFDRjtBQUFBO0FBQUE7QUFBQTtBQUFBLElBS0YsR0FBRztBQUFBLE1BQ0QsS0FBSztBQUFBLE1BQ0wsT0FBTyxTQUFTLGtDQUFrQztBQUNoRCxZQUFJLFNBQVM7QUFFYixpQkFBUyxpQkFBaUIsOEJBQThCLFNBQVUsT0FBTztBQUN2RSxnQkFBTSxPQUFPLGFBQWEsUUFBUSxhQUFhLElBQUksT0FBTyxTQUFTO0FBQUEsUUFDckUsQ0FBQztBQUFBLE1BQ0g7QUFBQSxJQUNGLENBQUMsQ0FBQztBQUVGLFdBQU9BO0FBQUEsRUFDVCxFQUFFOzs7QUNwb0RGLHNCQUFtQjtBQUVuQixTQUFPLGNBQWM7QUFDckIsU0FBTyxTQUFTLGNBQUFDOyIsCiAgIm5hbWVzIjogWyJtb2R1bGUiLCAiZXhwb3J0cyIsICJrZXkiLCAiQ29kZXIiLCAiVVJMU2FmZUNvZGVyIiwgIlNjcmlwdFJlY2VpdmVyRmFjdG9yeSIsICJwcmVmaXgiLCAiQXV0aFJlcXVlc3RUeXBlIiwgIkJhZEV2ZW50TmFtZSIsICJCYWRDaGFubmVsTmFtZSIsICJSZXF1ZXN0VGltZWRPdXQiLCAiVHJhbnNwb3J0UHJpb3JpdHlUb29Mb3ciLCAiVHJhbnNwb3J0Q2xvc2VkIiwgIlVuc3VwcG9ydGVkRmVhdHVyZSIsICJVbnN1cHBvcnRlZFRyYW5zcG9ydCIsICJVbnN1cHBvcnRlZFN0cmF0ZWd5IiwgIkhUVFBBdXRoRXJyb3IiLCAiVGltZXIiLCAiT25lT2ZmVGltZXIiLCAiUGVyaW9kaWNUaW1lciIsICJsIiwgImtleXMiLCAidmFsdWVzIiwgImRvY3VtZW50IiwgIlNjcmlwdFJlcXVlc3QiLCAic3RhdGUiLCAiTmV0SW5mbyIsICJDaGFubmVsIiwgIlByaXZhdGVDaGFubmVsIiwgInRyYW5zcG9ydHMiLCAiSWZTdHJhdGVneSIsICJGaXJzdENvbm5lY3RlZFN0cmF0ZWd5IiwgIlN0YXRlIiwgInJhbmRvbSIsICJUaW1lbGluZUxldmVsIiwgIlB1c2hlciIsICJvcHRpb25zIiwgIm9iaiIsICJfZ2V0UHJvdG90eXBlT2YiLCAibyIsICJfc2V0UHJvdG90eXBlT2YiLCAicCIsICJDaGFubmVsIiwgIkV2ZW50Rm9ybWF0dGVyIiwgIlB1c2hlckNoYW5uZWwiLCAiUHVzaGVyUHJpdmF0ZUNoYW5uZWwiLCAiUHVzaGVyRW5jcnlwdGVkUHJpdmF0ZUNoYW5uZWwiLCAiUHVzaGVyUHJlc2VuY2VDaGFubmVsIiwgIlNvY2tldElvQ2hhbm5lbCIsICJTb2NrZXRJb1ByaXZhdGVDaGFubmVsIiwgIlNvY2tldElvUHJlc2VuY2VDaGFubmVsIiwgIk51bGxDaGFubmVsIiwgIk51bGxQcml2YXRlQ2hhbm5lbCIsICJOdWxsUHJlc2VuY2VDaGFubmVsIiwgIkNvbm5lY3RvciIsICJQdXNoZXJDb25uZWN0b3IiLCAibmFtZSIsICJTb2NrZXRJb0Nvbm5lY3RvciIsICJpbyIsICJOdWxsQ29ubmVjdG9yIiwgIkVjaG8iLCAiUHVzaGVyIl0KfQo=
