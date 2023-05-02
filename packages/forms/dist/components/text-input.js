// node_modules/imask/esm/_rollupPluginBabelHelpers-6b3bd404.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null)
    return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;
  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0)
      continue;
    target[key] = source[key];
  }
  return target;
}

// node_modules/imask/esm/core/holder.js
function IMask(el) {
  let opts = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
  return new IMask.InputMask(el, opts);
}

// node_modules/imask/esm/core/change-details.js
var ChangeDetails = class {
  /** Inserted symbols */
  /** Can skip chars */
  /** Additional offset if any changes occurred before tail */
  /** Raw inserted is used by dynamic mask */
  constructor(details) {
    Object.assign(this, {
      inserted: "",
      rawInserted: "",
      skip: false,
      tailShift: 0
    }, details);
  }
  /**
    Aggregate changes
    @returns {ChangeDetails} `this`
  */
  aggregate(details) {
    this.rawInserted += details.rawInserted;
    this.skip = this.skip || details.skip;
    this.inserted += details.inserted;
    this.tailShift += details.tailShift;
    return this;
  }
  /** Total offset considering all changes */
  get offset() {
    return this.tailShift + this.inserted.length;
  }
};
IMask.ChangeDetails = ChangeDetails;

// node_modules/imask/esm/core/utils.js
function isString(str) {
  return typeof str === "string" || str instanceof String;
}
var DIRECTION = {
  NONE: "NONE",
  LEFT: "LEFT",
  FORCE_LEFT: "FORCE_LEFT",
  RIGHT: "RIGHT",
  FORCE_RIGHT: "FORCE_RIGHT"
};
function forceDirection(direction) {
  switch (direction) {
    case DIRECTION.LEFT:
      return DIRECTION.FORCE_LEFT;
    case DIRECTION.RIGHT:
      return DIRECTION.FORCE_RIGHT;
    default:
      return direction;
  }
}
function escapeRegExp(str) {
  return str.replace(/([.*+?^=!:${}()|[\]\/\\])/g, "\\$1");
}
function normalizePrepare(prep) {
  return Array.isArray(prep) ? prep : [prep, new ChangeDetails()];
}
function objectIncludes(b, a) {
  if (a === b)
    return true;
  var arrA = Array.isArray(a), arrB = Array.isArray(b), i;
  if (arrA && arrB) {
    if (a.length != b.length)
      return false;
    for (i = 0; i < a.length; i++)
      if (!objectIncludes(a[i], b[i]))
        return false;
    return true;
  }
  if (arrA != arrB)
    return false;
  if (a && b && typeof a === "object" && typeof b === "object") {
    var dateA = a instanceof Date, dateB = b instanceof Date;
    if (dateA && dateB)
      return a.getTime() == b.getTime();
    if (dateA != dateB)
      return false;
    var regexpA = a instanceof RegExp, regexpB = b instanceof RegExp;
    if (regexpA && regexpB)
      return a.toString() == b.toString();
    if (regexpA != regexpB)
      return false;
    var keys = Object.keys(a);
    for (i = 0; i < keys.length; i++)
      if (!Object.prototype.hasOwnProperty.call(b, keys[i]))
        return false;
    for (i = 0; i < keys.length; i++)
      if (!objectIncludes(b[keys[i]], a[keys[i]]))
        return false;
    return true;
  } else if (a && b && typeof a === "function" && typeof b === "function") {
    return a.toString() === b.toString();
  }
  return false;
}

// node_modules/imask/esm/core/action-details.js
var ActionDetails = class {
  /** Current input value */
  /** Current cursor position */
  /** Old input value */
  /** Old selection */
  constructor(value, cursorPos, oldValue, oldSelection) {
    this.value = value;
    this.cursorPos = cursorPos;
    this.oldValue = oldValue;
    this.oldSelection = oldSelection;
    while (this.value.slice(0, this.startChangePos) !== this.oldValue.slice(0, this.startChangePos)) {
      --this.oldSelection.start;
    }
  }
  /**
    Start changing position
    @readonly
  */
  get startChangePos() {
    return Math.min(this.cursorPos, this.oldSelection.start);
  }
  /**
    Inserted symbols count
    @readonly
  */
  get insertedCount() {
    return this.cursorPos - this.startChangePos;
  }
  /**
    Inserted symbols
    @readonly
  */
  get inserted() {
    return this.value.substr(this.startChangePos, this.insertedCount);
  }
  /**
    Removed symbols count
    @readonly
  */
  get removedCount() {
    return Math.max(this.oldSelection.end - this.startChangePos || // for Delete
    this.oldValue.length - this.value.length, 0);
  }
  /**
    Removed symbols
    @readonly
  */
  get removed() {
    return this.oldValue.substr(this.startChangePos, this.removedCount);
  }
  /**
    Unchanged head symbols
    @readonly
  */
  get head() {
    return this.value.substring(0, this.startChangePos);
  }
  /**
    Unchanged tail symbols
    @readonly
  */
  get tail() {
    return this.value.substring(this.startChangePos + this.insertedCount);
  }
  /**
    Remove direction
    @readonly
  */
  get removeDirection() {
    if (!this.removedCount || this.insertedCount)
      return DIRECTION.NONE;
    return (this.oldSelection.end === this.cursorPos || this.oldSelection.start === this.cursorPos) && // if not range removed (event with backspace)
    this.oldSelection.end === this.oldSelection.start ? DIRECTION.RIGHT : DIRECTION.LEFT;
  }
};

// node_modules/imask/esm/core/continuous-tail-details.js
var ContinuousTailDetails = class {
  /** Tail value as string */
  /** Tail start position */
  /** Start position */
  constructor() {
    let value = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : "";
    let from = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : 0;
    let stop = arguments.length > 2 ? arguments[2] : void 0;
    this.value = value;
    this.from = from;
    this.stop = stop;
  }
  toString() {
    return this.value;
  }
  extend(tail) {
    this.value += String(tail);
  }
  appendTo(masked) {
    return masked.append(this.toString(), {
      tail: true
    }).aggregate(masked._appendPlaceholder());
  }
  get state() {
    return {
      value: this.value,
      from: this.from,
      stop: this.stop
    };
  }
  set state(state) {
    Object.assign(this, state);
  }
  unshift(beforePos) {
    if (!this.value.length || beforePos != null && this.from >= beforePos)
      return "";
    const shiftChar = this.value[0];
    this.value = this.value.slice(1);
    return shiftChar;
  }
  shift() {
    if (!this.value.length)
      return "";
    const shiftChar = this.value[this.value.length - 1];
    this.value = this.value.slice(0, -1);
    return shiftChar;
  }
};

// node_modules/imask/esm/masked/base.js
var Masked = class {
  // $Shape<MaskedOptions>; TODO after fix https://github.com/facebook/flow/issues/4773
  /** @type {Mask} */
  /** */
  // $FlowFixMe no ideas
  /** Transforms value before mask processing */
  /** Validates if value is acceptable */
  /** Does additional processing in the end of editing */
  /** Format typed value to string */
  /** Parse strgin to get typed value */
  /** Enable characters overwriting */
  /** */
  /** */
  /** */
  constructor(opts) {
    this._value = "";
    this._update(Object.assign({}, Masked.DEFAULTS, opts));
    this.isInitialized = true;
  }
  /** Sets and applies new options */
  updateOptions(opts) {
    if (!Object.keys(opts).length)
      return;
    this.withValueRefresh(this._update.bind(this, opts));
  }
  /**
    Sets new options
    @protected
  */
  _update(opts) {
    Object.assign(this, opts);
  }
  /** Mask state */
  get state() {
    return {
      _value: this.value
    };
  }
  set state(state) {
    this._value = state._value;
  }
  /** Resets value */
  reset() {
    this._value = "";
  }
  /** */
  get value() {
    return this._value;
  }
  set value(value) {
    this.resolve(value);
  }
  /** Resolve new value */
  resolve(value) {
    this.reset();
    this.append(value, {
      input: true
    }, "");
    this.doCommit();
    return this.value;
  }
  /** */
  get unmaskedValue() {
    return this.value;
  }
  set unmaskedValue(value) {
    this.reset();
    this.append(value, {}, "");
    this.doCommit();
  }
  /** */
  get typedValue() {
    return this.doParse(this.value);
  }
  set typedValue(value) {
    this.value = this.doFormat(value);
  }
  /** Value that includes raw user input */
  get rawInputValue() {
    return this.extractInput(0, this.value.length, {
      raw: true
    });
  }
  set rawInputValue(value) {
    this.reset();
    this.append(value, {
      raw: true
    }, "");
    this.doCommit();
  }
  get displayValue() {
    return this.value;
  }
  /** */
  get isComplete() {
    return true;
  }
  /** */
  get isFilled() {
    return this.isComplete;
  }
  /** Finds nearest input position in direction */
  nearestInputPos(cursorPos, direction) {
    return cursorPos;
  }
  totalInputPositions() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    return Math.min(this.value.length, toPos - fromPos);
  }
  /** Extracts value in range considering flags */
  extractInput() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    return this.value.slice(fromPos, toPos);
  }
  /** Extracts tail in range */
  extractTail() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    return new ContinuousTailDetails(this.extractInput(fromPos, toPos), fromPos);
  }
  /** Appends tail */
  // $FlowFixMe no ideas
  appendTail(tail) {
    if (isString(tail))
      tail = new ContinuousTailDetails(String(tail));
    return tail.appendTo(this);
  }
  /** Appends char */
  _appendCharRaw(ch) {
    if (!ch)
      return new ChangeDetails();
    this._value += ch;
    return new ChangeDetails({
      inserted: ch,
      rawInserted: ch
    });
  }
  /** Appends char */
  _appendChar(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    let checkTail = arguments.length > 2 ? arguments[2] : void 0;
    const consistentState = this.state;
    let details;
    [ch, details] = normalizePrepare(this.doPrepare(ch, flags));
    details = details.aggregate(this._appendCharRaw(ch, flags));
    if (details.inserted) {
      let consistentTail;
      let appended = this.doValidate(flags) !== false;
      if (appended && checkTail != null) {
        const beforeTailState = this.state;
        if (this.overwrite === true) {
          consistentTail = checkTail.state;
          checkTail.unshift(this.value.length - details.tailShift);
        }
        let tailDetails = this.appendTail(checkTail);
        appended = tailDetails.rawInserted === checkTail.toString();
        if (!(appended && tailDetails.inserted) && this.overwrite === "shift") {
          this.state = beforeTailState;
          consistentTail = checkTail.state;
          checkTail.shift();
          tailDetails = this.appendTail(checkTail);
          appended = tailDetails.rawInserted === checkTail.toString();
        }
        if (appended && tailDetails.inserted)
          this.state = beforeTailState;
      }
      if (!appended) {
        details = new ChangeDetails();
        this.state = consistentState;
        if (checkTail && consistentTail)
          checkTail.state = consistentTail;
      }
    }
    return details;
  }
  /** Appends optional placeholder at end */
  _appendPlaceholder() {
    return new ChangeDetails();
  }
  /** Appends optional eager placeholder at end */
  _appendEager() {
    return new ChangeDetails();
  }
  /** Appends symbols considering flags */
  // $FlowFixMe no ideas
  append(str, flags, tail) {
    if (!isString(str))
      throw new Error("value should be string");
    const details = new ChangeDetails();
    const checkTail = isString(tail) ? new ContinuousTailDetails(String(tail)) : tail;
    if (flags !== null && flags !== void 0 && flags.tail)
      flags._beforeTailState = this.state;
    for (let ci = 0; ci < str.length; ++ci) {
      const d = this._appendChar(str[ci], flags, checkTail);
      if (!d.rawInserted && !this.doSkipInvalid(str[ci], flags, checkTail))
        break;
      details.aggregate(d);
    }
    if (checkTail != null) {
      details.tailShift += this.appendTail(checkTail).tailShift;
    }
    if ((this.eager === true || this.eager === "append") && flags !== null && flags !== void 0 && flags.input && str) {
      details.aggregate(this._appendEager());
    }
    return details;
  }
  /** */
  remove() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    this._value = this.value.slice(0, fromPos) + this.value.slice(toPos);
    return new ChangeDetails();
  }
  /** Calls function and reapplies current value */
  withValueRefresh(fn) {
    if (this._refreshing || !this.isInitialized)
      return fn();
    this._refreshing = true;
    const rawInput = this.rawInputValue;
    const value = this.value;
    const ret = fn();
    this.rawInputValue = rawInput;
    if (this.value && this.value !== value && value.indexOf(this.value) === 0) {
      this.append(value.slice(this.value.length), {}, "");
    }
    delete this._refreshing;
    return ret;
  }
  /** */
  runIsolated(fn) {
    if (this._isolated || !this.isInitialized)
      return fn(this);
    this._isolated = true;
    const state = this.state;
    const ret = fn(this);
    this.state = state;
    delete this._isolated;
    return ret;
  }
  /** */
  doSkipInvalid(ch) {
    return this.skipInvalid;
  }
  /**
    Prepares string before mask processing
    @protected
  */
  doPrepare(str) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    return this.prepare ? this.prepare(str, this, flags) : str;
  }
  /**
    Validates if value is acceptable
    @protected
  */
  doValidate(flags) {
    return (!this.validate || this.validate(this.value, this, flags)) && (!this.parent || this.parent.doValidate(flags));
  }
  /**
    Does additional processing in the end of editing
    @protected
  */
  doCommit() {
    if (this.commit)
      this.commit(this.value, this);
  }
  /** */
  doFormat(value) {
    return this.format ? this.format(value, this) : value;
  }
  /** */
  doParse(str) {
    return this.parse ? this.parse(str, this) : str;
  }
  /** */
  splice(start, deleteCount, inserted, removeDirection) {
    let flags = arguments.length > 4 && arguments[4] !== void 0 ? arguments[4] : {
      input: true
    };
    const tailPos = start + deleteCount;
    const tail = this.extractTail(tailPos);
    const eagerRemove = this.eager === true || this.eager === "remove";
    let oldRawValue;
    if (eagerRemove) {
      removeDirection = forceDirection(removeDirection);
      oldRawValue = this.extractInput(0, tailPos, {
        raw: true
      });
    }
    let startChangePos = start;
    const details = new ChangeDetails();
    if (removeDirection !== DIRECTION.NONE) {
      startChangePos = this.nearestInputPos(start, deleteCount > 1 && start !== 0 && !eagerRemove ? DIRECTION.NONE : removeDirection);
      details.tailShift = startChangePos - start;
    }
    details.aggregate(this.remove(startChangePos));
    if (eagerRemove && removeDirection !== DIRECTION.NONE && oldRawValue === this.rawInputValue) {
      if (removeDirection === DIRECTION.FORCE_LEFT) {
        let valLength;
        while (oldRawValue === this.rawInputValue && (valLength = this.value.length)) {
          details.aggregate(new ChangeDetails({
            tailShift: -1
          })).aggregate(this.remove(valLength - 1));
        }
      } else if (removeDirection === DIRECTION.FORCE_RIGHT) {
        tail.unshift();
      }
    }
    return details.aggregate(this.append(inserted, flags, tail));
  }
  maskEquals(mask) {
    return this.mask === mask;
  }
  typedValueEquals(value) {
    const tval = this.typedValue;
    return value === tval || Masked.EMPTY_VALUES.includes(value) && Masked.EMPTY_VALUES.includes(tval) || this.doFormat(value) === this.doFormat(this.typedValue);
  }
};
Masked.DEFAULTS = {
  format: String,
  parse: (v) => v,
  skipInvalid: true
};
Masked.EMPTY_VALUES = [void 0, null, ""];
IMask.Masked = Masked;

// node_modules/imask/esm/masked/factory.js
function maskedClass(mask) {
  if (mask == null) {
    throw new Error("mask property should be defined");
  }
  if (mask instanceof RegExp)
    return IMask.MaskedRegExp;
  if (isString(mask))
    return IMask.MaskedPattern;
  if (mask instanceof Date || mask === Date)
    return IMask.MaskedDate;
  if (mask instanceof Number || typeof mask === "number" || mask === Number)
    return IMask.MaskedNumber;
  if (Array.isArray(mask) || mask === Array)
    return IMask.MaskedDynamic;
  if (IMask.Masked && mask.prototype instanceof IMask.Masked)
    return mask;
  if (mask instanceof IMask.Masked)
    return mask.constructor;
  if (mask instanceof Function)
    return IMask.MaskedFunction;
  console.warn("Mask not found for mask", mask);
  return IMask.Masked;
}
function createMask(opts) {
  if (IMask.Masked && opts instanceof IMask.Masked)
    return opts;
  opts = Object.assign({}, opts);
  const mask = opts.mask;
  if (IMask.Masked && mask instanceof IMask.Masked)
    return mask;
  const MaskedClass = maskedClass(mask);
  if (!MaskedClass)
    throw new Error("Masked class is not found for provided mask, appropriate module needs to be import manually before creating mask.");
  return new MaskedClass(opts);
}
IMask.createMask = createMask;

// node_modules/imask/esm/masked/pattern/input-definition.js
var _excluded = ["parent", "isOptional", "placeholderChar", "displayChar", "lazy", "eager"];
var DEFAULT_INPUT_DEFINITIONS = {
  "0": /\d/,
  "a": /[\u0041-\u005A\u0061-\u007A\u00AA\u00B5\u00BA\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u0527\u0531-\u0556\u0559\u0561-\u0587\u05D0-\u05EA\u05F0-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u08A0\u08A2-\u08AC\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0977\u0979-\u097F\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C33\u0C35-\u0C39\u0C3D\u0C58\u0C59\u0C60\u0C61\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D60\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F4\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1877\u1880-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191C\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19C1-\u19C7\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FCC\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA697\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA78E\uA790-\uA793\uA7A0-\uA7AA\uA7F8-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA80-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uABC0-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC]/,
  // http://stackoverflow.com/a/22075070
  "*": /./
};
var PatternInputDefinition = class {
  /** */
  /** */
  /** */
  /** */
  /** */
  /** */
  /** */
  /** */
  constructor(opts) {
    const {
      parent,
      isOptional,
      placeholderChar,
      displayChar,
      lazy,
      eager
    } = opts, maskOpts = _objectWithoutPropertiesLoose(opts, _excluded);
    this.masked = createMask(maskOpts);
    Object.assign(this, {
      parent,
      isOptional,
      placeholderChar,
      displayChar,
      lazy,
      eager
    });
  }
  reset() {
    this.isFilled = false;
    this.masked.reset();
  }
  remove() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    if (fromPos === 0 && toPos >= 1) {
      this.isFilled = false;
      return this.masked.remove(fromPos, toPos);
    }
    return new ChangeDetails();
  }
  get value() {
    return this.masked.value || (this.isFilled && !this.isOptional ? this.placeholderChar : "");
  }
  get unmaskedValue() {
    return this.masked.unmaskedValue;
  }
  get displayValue() {
    return this.masked.value && this.displayChar || this.value;
  }
  get isComplete() {
    return Boolean(this.masked.value) || this.isOptional;
  }
  _appendChar(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    if (this.isFilled)
      return new ChangeDetails();
    const state = this.masked.state;
    const details = this.masked._appendChar(ch, flags);
    if (details.inserted && this.doValidate(flags) === false) {
      details.inserted = details.rawInserted = "";
      this.masked.state = state;
    }
    if (!details.inserted && !this.isOptional && !this.lazy && !flags.input) {
      details.inserted = this.placeholderChar;
    }
    details.skip = !details.inserted && !this.isOptional;
    this.isFilled = Boolean(details.inserted);
    return details;
  }
  append() {
    return this.masked.append(...arguments);
  }
  _appendPlaceholder() {
    const details = new ChangeDetails();
    if (this.isFilled || this.isOptional)
      return details;
    this.isFilled = true;
    details.inserted = this.placeholderChar;
    return details;
  }
  _appendEager() {
    return new ChangeDetails();
  }
  extractTail() {
    return this.masked.extractTail(...arguments);
  }
  appendTail() {
    return this.masked.appendTail(...arguments);
  }
  extractInput() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    let flags = arguments.length > 2 ? arguments[2] : void 0;
    return this.masked.extractInput(fromPos, toPos, flags);
  }
  nearestInputPos(cursorPos) {
    let direction = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : DIRECTION.NONE;
    const minPos = 0;
    const maxPos = this.value.length;
    const boundPos = Math.min(Math.max(cursorPos, minPos), maxPos);
    switch (direction) {
      case DIRECTION.LEFT:
      case DIRECTION.FORCE_LEFT:
        return this.isComplete ? boundPos : minPos;
      case DIRECTION.RIGHT:
      case DIRECTION.FORCE_RIGHT:
        return this.isComplete ? boundPos : maxPos;
      case DIRECTION.NONE:
      default:
        return boundPos;
    }
  }
  totalInputPositions() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    return this.value.slice(fromPos, toPos).length;
  }
  doValidate() {
    return this.masked.doValidate(...arguments) && (!this.parent || this.parent.doValidate(...arguments));
  }
  doCommit() {
    this.masked.doCommit();
  }
  get state() {
    return {
      masked: this.masked.state,
      isFilled: this.isFilled
    };
  }
  set state(state) {
    this.masked.state = state.masked;
    this.isFilled = state.isFilled;
  }
};

// node_modules/imask/esm/masked/pattern/fixed-definition.js
var PatternFixedDefinition = class {
  /** */
  /** */
  /** */
  /** */
  /** */
  /** */
  constructor(opts) {
    Object.assign(this, opts);
    this._value = "";
    this.isFixed = true;
  }
  get value() {
    return this._value;
  }
  get unmaskedValue() {
    return this.isUnmasking ? this.value : "";
  }
  get displayValue() {
    return this.value;
  }
  reset() {
    this._isRawInput = false;
    this._value = "";
  }
  remove() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this._value.length;
    this._value = this._value.slice(0, fromPos) + this._value.slice(toPos);
    if (!this._value)
      this._isRawInput = false;
    return new ChangeDetails();
  }
  nearestInputPos(cursorPos) {
    let direction = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : DIRECTION.NONE;
    const minPos = 0;
    const maxPos = this._value.length;
    switch (direction) {
      case DIRECTION.LEFT:
      case DIRECTION.FORCE_LEFT:
        return minPos;
      case DIRECTION.NONE:
      case DIRECTION.RIGHT:
      case DIRECTION.FORCE_RIGHT:
      default:
        return maxPos;
    }
  }
  totalInputPositions() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this._value.length;
    return this._isRawInput ? toPos - fromPos : 0;
  }
  extractInput() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this._value.length;
    let flags = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
    return flags.raw && this._isRawInput && this._value.slice(fromPos, toPos) || "";
  }
  get isComplete() {
    return true;
  }
  get isFilled() {
    return Boolean(this._value);
  }
  _appendChar(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    const details = new ChangeDetails();
    if (this.isFilled)
      return details;
    const appendEager = this.eager === true || this.eager === "append";
    const appended = this.char === ch;
    const isResolved = appended && (this.isUnmasking || flags.input || flags.raw) && (!flags.raw || !appendEager) && !flags.tail;
    if (isResolved)
      details.rawInserted = this.char;
    this._value = details.inserted = this.char;
    this._isRawInput = isResolved && (flags.raw || flags.input);
    return details;
  }
  _appendEager() {
    return this._appendChar(this.char, {
      tail: true
    });
  }
  _appendPlaceholder() {
    const details = new ChangeDetails();
    if (this.isFilled)
      return details;
    this._value = details.inserted = this.char;
    return details;
  }
  extractTail() {
    arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    return new ContinuousTailDetails("");
  }
  // $FlowFixMe no ideas
  appendTail(tail) {
    if (isString(tail))
      tail = new ContinuousTailDetails(String(tail));
    return tail.appendTo(this);
  }
  append(str, flags, tail) {
    const details = this._appendChar(str[0], flags);
    if (tail != null) {
      details.tailShift += this.appendTail(tail).tailShift;
    }
    return details;
  }
  doCommit() {
  }
  get state() {
    return {
      _value: this._value,
      _isRawInput: this._isRawInput
    };
  }
  set state(state) {
    Object.assign(this, state);
  }
};

// node_modules/imask/esm/masked/pattern/chunk-tail-details.js
var _excluded2 = ["chunks"];
var ChunksTailDetails = class {
  /** */
  constructor() {
    let chunks = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : [];
    let from = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : 0;
    this.chunks = chunks;
    this.from = from;
  }
  toString() {
    return this.chunks.map(String).join("");
  }
  // $FlowFixMe no ideas
  extend(tailChunk) {
    if (!String(tailChunk))
      return;
    if (isString(tailChunk))
      tailChunk = new ContinuousTailDetails(String(tailChunk));
    const lastChunk = this.chunks[this.chunks.length - 1];
    const extendLast = lastChunk && // if stops are same or tail has no stop
    (lastChunk.stop === tailChunk.stop || tailChunk.stop == null) && // if tail chunk goes just after last chunk
    tailChunk.from === lastChunk.from + lastChunk.toString().length;
    if (tailChunk instanceof ContinuousTailDetails) {
      if (extendLast) {
        lastChunk.extend(tailChunk.toString());
      } else {
        this.chunks.push(tailChunk);
      }
    } else if (tailChunk instanceof ChunksTailDetails) {
      if (tailChunk.stop == null) {
        let firstTailChunk;
        while (tailChunk.chunks.length && tailChunk.chunks[0].stop == null) {
          firstTailChunk = tailChunk.chunks.shift();
          firstTailChunk.from += tailChunk.from;
          this.extend(firstTailChunk);
        }
      }
      if (tailChunk.toString()) {
        tailChunk.stop = tailChunk.blockIndex;
        this.chunks.push(tailChunk);
      }
    }
  }
  appendTo(masked) {
    if (!(masked instanceof IMask.MaskedPattern)) {
      const tail = new ContinuousTailDetails(this.toString());
      return tail.appendTo(masked);
    }
    const details = new ChangeDetails();
    for (let ci = 0; ci < this.chunks.length && !details.skip; ++ci) {
      const chunk = this.chunks[ci];
      const lastBlockIter = masked._mapPosToBlock(masked.value.length);
      const stop = chunk.stop;
      let chunkBlock;
      if (stop != null && // if block not found or stop is behind lastBlock
      (!lastBlockIter || lastBlockIter.index <= stop)) {
        if (chunk instanceof ChunksTailDetails || // for continuous block also check if stop is exist
        masked._stops.indexOf(stop) >= 0) {
          const phDetails = masked._appendPlaceholder(stop);
          details.aggregate(phDetails);
        }
        chunkBlock = chunk instanceof ChunksTailDetails && masked._blocks[stop];
      }
      if (chunkBlock) {
        const tailDetails = chunkBlock.appendTail(chunk);
        tailDetails.skip = false;
        details.aggregate(tailDetails);
        masked._value += tailDetails.inserted;
        const remainChars = chunk.toString().slice(tailDetails.rawInserted.length);
        if (remainChars)
          details.aggregate(masked.append(remainChars, {
            tail: true
          }));
      } else {
        details.aggregate(masked.append(chunk.toString(), {
          tail: true
        }));
      }
    }
    return details;
  }
  get state() {
    return {
      chunks: this.chunks.map((c) => c.state),
      from: this.from,
      stop: this.stop,
      blockIndex: this.blockIndex
    };
  }
  set state(state) {
    const {
      chunks
    } = state, props = _objectWithoutPropertiesLoose(state, _excluded2);
    Object.assign(this, props);
    this.chunks = chunks.map((cstate) => {
      const chunk = "chunks" in cstate ? new ChunksTailDetails() : new ContinuousTailDetails();
      chunk.state = cstate;
      return chunk;
    });
  }
  unshift(beforePos) {
    if (!this.chunks.length || beforePos != null && this.from >= beforePos)
      return "";
    const chunkShiftPos = beforePos != null ? beforePos - this.from : beforePos;
    let ci = 0;
    while (ci < this.chunks.length) {
      const chunk = this.chunks[ci];
      const shiftChar = chunk.unshift(chunkShiftPos);
      if (chunk.toString()) {
        if (!shiftChar)
          break;
        ++ci;
      } else {
        this.chunks.splice(ci, 1);
      }
      if (shiftChar)
        return shiftChar;
    }
    return "";
  }
  shift() {
    if (!this.chunks.length)
      return "";
    let ci = this.chunks.length - 1;
    while (0 <= ci) {
      const chunk = this.chunks[ci];
      const shiftChar = chunk.shift();
      if (chunk.toString()) {
        if (!shiftChar)
          break;
        --ci;
      } else {
        this.chunks.splice(ci, 1);
      }
      if (shiftChar)
        return shiftChar;
    }
    return "";
  }
};

// node_modules/imask/esm/masked/pattern/cursor.js
var PatternCursor = class {
  constructor(masked, pos) {
    this.masked = masked;
    this._log = [];
    const {
      offset,
      index
    } = masked._mapPosToBlock(pos) || (pos < 0 ? (
      // first
      {
        index: 0,
        offset: 0
      }
    ) : (
      // last
      {
        index: this.masked._blocks.length,
        offset: 0
      }
    ));
    this.offset = offset;
    this.index = index;
    this.ok = false;
  }
  get block() {
    return this.masked._blocks[this.index];
  }
  get pos() {
    return this.masked._blockStartPos(this.index) + this.offset;
  }
  get state() {
    return {
      index: this.index,
      offset: this.offset,
      ok: this.ok
    };
  }
  set state(s) {
    Object.assign(this, s);
  }
  pushState() {
    this._log.push(this.state);
  }
  popState() {
    const s = this._log.pop();
    this.state = s;
    return s;
  }
  bindBlock() {
    if (this.block)
      return;
    if (this.index < 0) {
      this.index = 0;
      this.offset = 0;
    }
    if (this.index >= this.masked._blocks.length) {
      this.index = this.masked._blocks.length - 1;
      this.offset = this.block.value.length;
    }
  }
  _pushLeft(fn) {
    this.pushState();
    for (this.bindBlock(); 0 <= this.index; --this.index, this.offset = ((_this$block = this.block) === null || _this$block === void 0 ? void 0 : _this$block.value.length) || 0) {
      var _this$block;
      if (fn())
        return this.ok = true;
    }
    return this.ok = false;
  }
  _pushRight(fn) {
    this.pushState();
    for (this.bindBlock(); this.index < this.masked._blocks.length; ++this.index, this.offset = 0) {
      if (fn())
        return this.ok = true;
    }
    return this.ok = false;
  }
  pushLeftBeforeFilled() {
    return this._pushLeft(() => {
      if (this.block.isFixed || !this.block.value)
        return;
      this.offset = this.block.nearestInputPos(this.offset, DIRECTION.FORCE_LEFT);
      if (this.offset !== 0)
        return true;
    });
  }
  pushLeftBeforeInput() {
    return this._pushLeft(() => {
      if (this.block.isFixed)
        return;
      this.offset = this.block.nearestInputPos(this.offset, DIRECTION.LEFT);
      return true;
    });
  }
  pushLeftBeforeRequired() {
    return this._pushLeft(() => {
      if (this.block.isFixed || this.block.isOptional && !this.block.value)
        return;
      this.offset = this.block.nearestInputPos(this.offset, DIRECTION.LEFT);
      return true;
    });
  }
  pushRightBeforeFilled() {
    return this._pushRight(() => {
      if (this.block.isFixed || !this.block.value)
        return;
      this.offset = this.block.nearestInputPos(this.offset, DIRECTION.FORCE_RIGHT);
      if (this.offset !== this.block.value.length)
        return true;
    });
  }
  pushRightBeforeInput() {
    return this._pushRight(() => {
      if (this.block.isFixed)
        return;
      this.offset = this.block.nearestInputPos(this.offset, DIRECTION.NONE);
      return true;
    });
  }
  pushRightBeforeRequired() {
    return this._pushRight(() => {
      if (this.block.isFixed || this.block.isOptional && !this.block.value)
        return;
      this.offset = this.block.nearestInputPos(this.offset, DIRECTION.NONE);
      return true;
    });
  }
};

// node_modules/imask/esm/masked/regexp.js
var MaskedRegExp = class extends Masked {
  /**
    @override
    @param {Object} opts
  */
  _update(opts) {
    if (opts.mask)
      opts.validate = (value) => value.search(opts.mask) >= 0;
    super._update(opts);
  }
};
IMask.MaskedRegExp = MaskedRegExp;

// node_modules/imask/esm/masked/pattern.js
var _excluded3 = ["_blocks"];
var MaskedPattern = class extends Masked {
  /** */
  /** */
  /** Single char for empty input */
  /** Single char for filled input */
  /** Show placeholder only when needed */
  constructor() {
    let opts = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
    opts.definitions = Object.assign({}, DEFAULT_INPUT_DEFINITIONS, opts.definitions);
    super(Object.assign({}, MaskedPattern.DEFAULTS, opts));
  }
  /**
    @override
    @param {Object} opts
  */
  _update() {
    let opts = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
    opts.definitions = Object.assign({}, this.definitions, opts.definitions);
    super._update(opts);
    this._rebuildMask();
  }
  /** */
  _rebuildMask() {
    const defs = this.definitions;
    this._blocks = [];
    this._stops = [];
    this._maskedBlocks = {};
    let pattern = this.mask;
    if (!pattern || !defs)
      return;
    let unmaskingBlock = false;
    let optionalBlock = false;
    for (let i = 0; i < pattern.length; ++i) {
      var _defs$char, _defs$char2;
      if (this.blocks) {
        const p = pattern.slice(i);
        const bNames = Object.keys(this.blocks).filter((bName2) => p.indexOf(bName2) === 0);
        bNames.sort((a, b) => b.length - a.length);
        const bName = bNames[0];
        if (bName) {
          const maskedBlock = createMask(Object.assign({
            parent: this,
            lazy: this.lazy,
            eager: this.eager,
            placeholderChar: this.placeholderChar,
            displayChar: this.displayChar,
            overwrite: this.overwrite
          }, this.blocks[bName]));
          if (maskedBlock) {
            this._blocks.push(maskedBlock);
            if (!this._maskedBlocks[bName])
              this._maskedBlocks[bName] = [];
            this._maskedBlocks[bName].push(this._blocks.length - 1);
          }
          i += bName.length - 1;
          continue;
        }
      }
      let char = pattern[i];
      let isInput = char in defs;
      if (char === MaskedPattern.STOP_CHAR) {
        this._stops.push(this._blocks.length);
        continue;
      }
      if (char === "{" || char === "}") {
        unmaskingBlock = !unmaskingBlock;
        continue;
      }
      if (char === "[" || char === "]") {
        optionalBlock = !optionalBlock;
        continue;
      }
      if (char === MaskedPattern.ESCAPE_CHAR) {
        ++i;
        char = pattern[i];
        if (!char)
          break;
        isInput = false;
      }
      const maskOpts = (_defs$char = defs[char]) !== null && _defs$char !== void 0 && _defs$char.mask && !(((_defs$char2 = defs[char]) === null || _defs$char2 === void 0 ? void 0 : _defs$char2.mask.prototype) instanceof IMask.Masked) ? defs[char] : {
        mask: defs[char]
      };
      const def = isInput ? new PatternInputDefinition(Object.assign({
        parent: this,
        isOptional: optionalBlock,
        lazy: this.lazy,
        eager: this.eager,
        placeholderChar: this.placeholderChar,
        displayChar: this.displayChar
      }, maskOpts)) : new PatternFixedDefinition({
        char,
        eager: this.eager,
        isUnmasking: unmaskingBlock
      });
      this._blocks.push(def);
    }
  }
  /**
    @override
  */
  get state() {
    return Object.assign({}, super.state, {
      _blocks: this._blocks.map((b) => b.state)
    });
  }
  set state(state) {
    const {
      _blocks
    } = state, maskedState = _objectWithoutPropertiesLoose(state, _excluded3);
    this._blocks.forEach((b, bi) => b.state = _blocks[bi]);
    super.state = maskedState;
  }
  /**
    @override
  */
  reset() {
    super.reset();
    this._blocks.forEach((b) => b.reset());
  }
  /**
    @override
  */
  get isComplete() {
    return this._blocks.every((b) => b.isComplete);
  }
  /**
    @override
  */
  get isFilled() {
    return this._blocks.every((b) => b.isFilled);
  }
  get isFixed() {
    return this._blocks.every((b) => b.isFixed);
  }
  get isOptional() {
    return this._blocks.every((b) => b.isOptional);
  }
  /**
    @override
  */
  doCommit() {
    this._blocks.forEach((b) => b.doCommit());
    super.doCommit();
  }
  /**
    @override
  */
  get unmaskedValue() {
    return this._blocks.reduce((str, b) => str += b.unmaskedValue, "");
  }
  set unmaskedValue(unmaskedValue) {
    super.unmaskedValue = unmaskedValue;
  }
  /**
    @override
  */
  get value() {
    return this._blocks.reduce((str, b) => str += b.value, "");
  }
  set value(value) {
    super.value = value;
  }
  get displayValue() {
    return this._blocks.reduce((str, b) => str += b.displayValue, "");
  }
  /**
    @override
  */
  appendTail(tail) {
    return super.appendTail(tail).aggregate(this._appendPlaceholder());
  }
  /**
    @override
  */
  _appendEager() {
    var _this$_mapPosToBlock;
    const details = new ChangeDetails();
    let startBlockIndex = (_this$_mapPosToBlock = this._mapPosToBlock(this.value.length)) === null || _this$_mapPosToBlock === void 0 ? void 0 : _this$_mapPosToBlock.index;
    if (startBlockIndex == null)
      return details;
    if (this._blocks[startBlockIndex].isFilled)
      ++startBlockIndex;
    for (let bi = startBlockIndex; bi < this._blocks.length; ++bi) {
      const d = this._blocks[bi]._appendEager();
      if (!d.inserted)
        break;
      details.aggregate(d);
    }
    return details;
  }
  /**
    @override
  */
  _appendCharRaw(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    const blockIter = this._mapPosToBlock(this.value.length);
    const details = new ChangeDetails();
    if (!blockIter)
      return details;
    for (let bi = blockIter.index; ; ++bi) {
      var _flags$_beforeTailSta, _flags$_beforeTailSta2;
      const block = this._blocks[bi];
      if (!block)
        break;
      const blockDetails = block._appendChar(ch, Object.assign({}, flags, {
        _beforeTailState: (_flags$_beforeTailSta = flags._beforeTailState) === null || _flags$_beforeTailSta === void 0 ? void 0 : (_flags$_beforeTailSta2 = _flags$_beforeTailSta._blocks) === null || _flags$_beforeTailSta2 === void 0 ? void 0 : _flags$_beforeTailSta2[bi]
      }));
      const skip = blockDetails.skip;
      details.aggregate(blockDetails);
      if (skip || blockDetails.rawInserted)
        break;
    }
    return details;
  }
  /**
    @override
  */
  extractTail() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    const chunkTail = new ChunksTailDetails();
    if (fromPos === toPos)
      return chunkTail;
    this._forEachBlocksInRange(fromPos, toPos, (b, bi, bFromPos, bToPos) => {
      const blockChunk = b.extractTail(bFromPos, bToPos);
      blockChunk.stop = this._findStopBefore(bi);
      blockChunk.from = this._blockStartPos(bi);
      if (blockChunk instanceof ChunksTailDetails)
        blockChunk.blockIndex = bi;
      chunkTail.extend(blockChunk);
    });
    return chunkTail;
  }
  /**
    @override
  */
  extractInput() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    let flags = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
    if (fromPos === toPos)
      return "";
    let input = "";
    this._forEachBlocksInRange(fromPos, toPos, (b, _, fromPos2, toPos2) => {
      input += b.extractInput(fromPos2, toPos2, flags);
    });
    return input;
  }
  _findStopBefore(blockIndex) {
    let stopBefore;
    for (let si = 0; si < this._stops.length; ++si) {
      const stop = this._stops[si];
      if (stop <= blockIndex)
        stopBefore = stop;
      else
        break;
    }
    return stopBefore;
  }
  /** Appends placeholder depending on laziness */
  _appendPlaceholder(toBlockIndex) {
    const details = new ChangeDetails();
    if (this.lazy && toBlockIndex == null)
      return details;
    const startBlockIter = this._mapPosToBlock(this.value.length);
    if (!startBlockIter)
      return details;
    const startBlockIndex = startBlockIter.index;
    const endBlockIndex = toBlockIndex != null ? toBlockIndex : this._blocks.length;
    this._blocks.slice(startBlockIndex, endBlockIndex).forEach((b) => {
      if (!b.lazy || toBlockIndex != null) {
        const args = b._blocks != null ? [b._blocks.length] : [];
        const bDetails = b._appendPlaceholder(...args);
        this._value += bDetails.inserted;
        details.aggregate(bDetails);
      }
    });
    return details;
  }
  /** Finds block in pos */
  _mapPosToBlock(pos) {
    let accVal = "";
    for (let bi = 0; bi < this._blocks.length; ++bi) {
      const block = this._blocks[bi];
      const blockStartPos = accVal.length;
      accVal += block.value;
      if (pos <= accVal.length) {
        return {
          index: bi,
          offset: pos - blockStartPos
        };
      }
    }
  }
  /** */
  _blockStartPos(blockIndex) {
    return this._blocks.slice(0, blockIndex).reduce((pos, b) => pos += b.value.length, 0);
  }
  /** */
  _forEachBlocksInRange(fromPos) {
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    let fn = arguments.length > 2 ? arguments[2] : void 0;
    const fromBlockIter = this._mapPosToBlock(fromPos);
    if (fromBlockIter) {
      const toBlockIter = this._mapPosToBlock(toPos);
      const isSameBlock = toBlockIter && fromBlockIter.index === toBlockIter.index;
      const fromBlockStartPos = fromBlockIter.offset;
      const fromBlockEndPos = toBlockIter && isSameBlock ? toBlockIter.offset : this._blocks[fromBlockIter.index].value.length;
      fn(this._blocks[fromBlockIter.index], fromBlockIter.index, fromBlockStartPos, fromBlockEndPos);
      if (toBlockIter && !isSameBlock) {
        for (let bi = fromBlockIter.index + 1; bi < toBlockIter.index; ++bi) {
          fn(this._blocks[bi], bi, 0, this._blocks[bi].value.length);
        }
        fn(this._blocks[toBlockIter.index], toBlockIter.index, 0, toBlockIter.offset);
      }
    }
  }
  /**
    @override
  */
  remove() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    const removeDetails = super.remove(fromPos, toPos);
    this._forEachBlocksInRange(fromPos, toPos, (b, _, bFromPos, bToPos) => {
      removeDetails.aggregate(b.remove(bFromPos, bToPos));
    });
    return removeDetails;
  }
  /**
    @override
  */
  nearestInputPos(cursorPos) {
    let direction = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : DIRECTION.NONE;
    if (!this._blocks.length)
      return 0;
    const cursor = new PatternCursor(this, cursorPos);
    if (direction === DIRECTION.NONE) {
      if (cursor.pushRightBeforeInput())
        return cursor.pos;
      cursor.popState();
      if (cursor.pushLeftBeforeInput())
        return cursor.pos;
      return this.value.length;
    }
    if (direction === DIRECTION.LEFT || direction === DIRECTION.FORCE_LEFT) {
      if (direction === DIRECTION.LEFT) {
        cursor.pushRightBeforeFilled();
        if (cursor.ok && cursor.pos === cursorPos)
          return cursorPos;
        cursor.popState();
      }
      cursor.pushLeftBeforeInput();
      cursor.pushLeftBeforeRequired();
      cursor.pushLeftBeforeFilled();
      if (direction === DIRECTION.LEFT) {
        cursor.pushRightBeforeInput();
        cursor.pushRightBeforeRequired();
        if (cursor.ok && cursor.pos <= cursorPos)
          return cursor.pos;
        cursor.popState();
        if (cursor.ok && cursor.pos <= cursorPos)
          return cursor.pos;
        cursor.popState();
      }
      if (cursor.ok)
        return cursor.pos;
      if (direction === DIRECTION.FORCE_LEFT)
        return 0;
      cursor.popState();
      if (cursor.ok)
        return cursor.pos;
      cursor.popState();
      if (cursor.ok)
        return cursor.pos;
      return 0;
    }
    if (direction === DIRECTION.RIGHT || direction === DIRECTION.FORCE_RIGHT) {
      cursor.pushRightBeforeInput();
      cursor.pushRightBeforeRequired();
      if (cursor.pushRightBeforeFilled())
        return cursor.pos;
      if (direction === DIRECTION.FORCE_RIGHT)
        return this.value.length;
      cursor.popState();
      if (cursor.ok)
        return cursor.pos;
      cursor.popState();
      if (cursor.ok)
        return cursor.pos;
      return this.nearestInputPos(cursorPos, DIRECTION.LEFT);
    }
    return cursorPos;
  }
  /**
    @override
  */
  totalInputPositions() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    let total = 0;
    this._forEachBlocksInRange(fromPos, toPos, (b, _, bFromPos, bToPos) => {
      total += b.totalInputPositions(bFromPos, bToPos);
    });
    return total;
  }
  /** Get block by name */
  maskedBlock(name) {
    return this.maskedBlocks(name)[0];
  }
  /** Get all blocks by name */
  maskedBlocks(name) {
    const indices = this._maskedBlocks[name];
    if (!indices)
      return [];
    return indices.map((gi) => this._blocks[gi]);
  }
};
MaskedPattern.DEFAULTS = {
  lazy: true,
  placeholderChar: "_"
};
MaskedPattern.STOP_CHAR = "`";
MaskedPattern.ESCAPE_CHAR = "\\";
MaskedPattern.InputDefinition = PatternInputDefinition;
MaskedPattern.FixedDefinition = PatternFixedDefinition;
IMask.MaskedPattern = MaskedPattern;

// node_modules/imask/esm/masked/range.js
var MaskedRange = class extends MaskedPattern {
  /**
    Optionally sets max length of pattern.
    Used when pattern length is longer then `to` param length. Pads zeros at start in this case.
  */
  /** Min bound */
  /** Max bound */
  /** */
  get _matchFrom() {
    return this.maxLength - String(this.from).length;
  }
  /**
    @override
  */
  _update(opts) {
    opts = Object.assign({
      to: this.to || 0,
      from: this.from || 0,
      maxLength: this.maxLength || 0
    }, opts);
    let maxLength = String(opts.to).length;
    if (opts.maxLength != null)
      maxLength = Math.max(maxLength, opts.maxLength);
    opts.maxLength = maxLength;
    const fromStr = String(opts.from).padStart(maxLength, "0");
    const toStr = String(opts.to).padStart(maxLength, "0");
    let sameCharsCount = 0;
    while (sameCharsCount < toStr.length && toStr[sameCharsCount] === fromStr[sameCharsCount])
      ++sameCharsCount;
    opts.mask = toStr.slice(0, sameCharsCount).replace(/0/g, "\\0") + "0".repeat(maxLength - sameCharsCount);
    super._update(opts);
  }
  /**
    @override
  */
  get isComplete() {
    return super.isComplete && Boolean(this.value);
  }
  boundaries(str) {
    let minstr = "";
    let maxstr = "";
    const [, placeholder, num] = str.match(/^(\D*)(\d*)(\D*)/) || [];
    if (num) {
      minstr = "0".repeat(placeholder.length) + num;
      maxstr = "9".repeat(placeholder.length) + num;
    }
    minstr = minstr.padEnd(this.maxLength, "0");
    maxstr = maxstr.padEnd(this.maxLength, "9");
    return [minstr, maxstr];
  }
  // TODO str is a single char everytime
  /**
    @override
  */
  doPrepare(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    let details;
    [ch, details] = normalizePrepare(super.doPrepare(ch.replace(/\D/g, ""), flags));
    if (!this.autofix || !ch)
      return ch;
    const fromStr = String(this.from).padStart(this.maxLength, "0");
    const toStr = String(this.to).padStart(this.maxLength, "0");
    let nextVal = this.value + ch;
    if (nextVal.length > this.maxLength)
      return "";
    const [minstr, maxstr] = this.boundaries(nextVal);
    if (Number(maxstr) < this.from)
      return fromStr[nextVal.length - 1];
    if (Number(minstr) > this.to) {
      if (this.autofix === "pad" && nextVal.length < this.maxLength) {
        return ["", details.aggregate(this.append(fromStr[nextVal.length - 1] + ch, flags))];
      }
      return toStr[nextVal.length - 1];
    }
    return ch;
  }
  /**
    @override
  */
  doValidate() {
    const str = this.value;
    const firstNonZero = str.search(/[^0]/);
    if (firstNonZero === -1 && str.length <= this._matchFrom)
      return true;
    const [minstr, maxstr] = this.boundaries(str);
    return this.from <= Number(maxstr) && Number(minstr) <= this.to && super.doValidate(...arguments);
  }
};
IMask.MaskedRange = MaskedRange;

// node_modules/imask/esm/masked/date.js
var MaskedDate = class extends MaskedPattern {
  /** Pattern mask for date according to {@link MaskedDate#format} */
  /** Start date */
  /** End date */
  /** */
  /**
    @param {Object} opts
  */
  constructor(opts) {
    super(Object.assign({}, MaskedDate.DEFAULTS, opts));
  }
  /**
    @override
  */
  _update(opts) {
    if (opts.mask === Date)
      delete opts.mask;
    if (opts.pattern)
      opts.mask = opts.pattern;
    const blocks = opts.blocks;
    opts.blocks = Object.assign({}, MaskedDate.GET_DEFAULT_BLOCKS());
    if (opts.min)
      opts.blocks.Y.from = opts.min.getFullYear();
    if (opts.max)
      opts.blocks.Y.to = opts.max.getFullYear();
    if (opts.min && opts.max && opts.blocks.Y.from === opts.blocks.Y.to) {
      opts.blocks.m.from = opts.min.getMonth() + 1;
      opts.blocks.m.to = opts.max.getMonth() + 1;
      if (opts.blocks.m.from === opts.blocks.m.to) {
        opts.blocks.d.from = opts.min.getDate();
        opts.blocks.d.to = opts.max.getDate();
      }
    }
    Object.assign(opts.blocks, this.blocks, blocks);
    Object.keys(opts.blocks).forEach((bk) => {
      const b = opts.blocks[bk];
      if (!("autofix" in b) && "autofix" in opts)
        b.autofix = opts.autofix;
    });
    super._update(opts);
  }
  /**
    @override
  */
  doValidate() {
    const date = this.date;
    return super.doValidate(...arguments) && (!this.isComplete || this.isDateExist(this.value) && date != null && (this.min == null || this.min <= date) && (this.max == null || date <= this.max));
  }
  /** Checks if date is exists */
  isDateExist(str) {
    return this.format(this.parse(str, this), this).indexOf(str) >= 0;
  }
  /** Parsed Date */
  get date() {
    return this.typedValue;
  }
  set date(date) {
    this.typedValue = date;
  }
  /**
    @override
  */
  get typedValue() {
    return this.isComplete ? super.typedValue : null;
  }
  set typedValue(value) {
    super.typedValue = value;
  }
  /**
    @override
  */
  maskEquals(mask) {
    return mask === Date || super.maskEquals(mask);
  }
};
MaskedDate.DEFAULTS = {
  pattern: "d{.}`m{.}`Y",
  format: (date) => {
    if (!date)
      return "";
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return [day, month, year].join(".");
  },
  parse: (str) => {
    const [day, month, year] = str.split(".");
    return new Date(year, month - 1, day);
  }
};
MaskedDate.GET_DEFAULT_BLOCKS = () => ({
  d: {
    mask: MaskedRange,
    from: 1,
    to: 31,
    maxLength: 2
  },
  m: {
    mask: MaskedRange,
    from: 1,
    to: 12,
    maxLength: 2
  },
  Y: {
    mask: MaskedRange,
    from: 1900,
    to: 9999
  }
});
IMask.MaskedDate = MaskedDate;

// node_modules/imask/esm/controls/mask-element.js
var MaskElement = class {
  /** */
  /** */
  /** */
  /** Safely returns selection start */
  get selectionStart() {
    let start;
    try {
      start = this._unsafeSelectionStart;
    } catch (e) {
    }
    return start != null ? start : this.value.length;
  }
  /** Safely returns selection end */
  get selectionEnd() {
    let end;
    try {
      end = this._unsafeSelectionEnd;
    } catch (e) {
    }
    return end != null ? end : this.value.length;
  }
  /** Safely sets element selection */
  select(start, end) {
    if (start == null || end == null || start === this.selectionStart && end === this.selectionEnd)
      return;
    try {
      this._unsafeSelect(start, end);
    } catch (e) {
    }
  }
  /** Should be overriden in subclasses */
  _unsafeSelect(start, end) {
  }
  /** Should be overriden in subclasses */
  get isActive() {
    return false;
  }
  /** Should be overriden in subclasses */
  bindEvents(handlers) {
  }
  /** Should be overriden in subclasses */
  unbindEvents() {
  }
};
IMask.MaskElement = MaskElement;

// node_modules/imask/esm/controls/html-mask-element.js
var HTMLMaskElement = class extends MaskElement {
  /** Mapping between HTMLElement events and mask internal events */
  /** HTMLElement to use mask on */
  /**
    @param {HTMLInputElement|HTMLTextAreaElement} input
  */
  constructor(input) {
    super();
    this.input = input;
    this._handlers = {};
  }
  /** */
  // $FlowFixMe https://github.com/facebook/flow/issues/2839
  get rootElement() {
    var _this$input$getRootNo, _this$input$getRootNo2, _this$input;
    return (_this$input$getRootNo = (_this$input$getRootNo2 = (_this$input = this.input).getRootNode) === null || _this$input$getRootNo2 === void 0 ? void 0 : _this$input$getRootNo2.call(_this$input)) !== null && _this$input$getRootNo !== void 0 ? _this$input$getRootNo : document;
  }
  /**
    Is element in focus
    @readonly
  */
  get isActive() {
    return this.input === this.rootElement.activeElement;
  }
  /**
    Returns HTMLElement selection start
    @override
  */
  get _unsafeSelectionStart() {
    return this.input.selectionStart;
  }
  /**
    Returns HTMLElement selection end
    @override
  */
  get _unsafeSelectionEnd() {
    return this.input.selectionEnd;
  }
  /**
    Sets HTMLElement selection
    @override
  */
  _unsafeSelect(start, end) {
    this.input.setSelectionRange(start, end);
  }
  /**
    HTMLElement value
    @override
  */
  get value() {
    return this.input.value;
  }
  set value(value) {
    this.input.value = value;
  }
  /**
    Binds HTMLElement events to mask internal events
    @override
  */
  bindEvents(handlers) {
    Object.keys(handlers).forEach((event) => this._toggleEventHandler(HTMLMaskElement.EVENTS_MAP[event], handlers[event]));
  }
  /**
    Unbinds HTMLElement events to mask internal events
    @override
  */
  unbindEvents() {
    Object.keys(this._handlers).forEach((event) => this._toggleEventHandler(event));
  }
  /** */
  _toggleEventHandler(event, handler) {
    if (this._handlers[event]) {
      this.input.removeEventListener(event, this._handlers[event]);
      delete this._handlers[event];
    }
    if (handler) {
      this.input.addEventListener(event, handler);
      this._handlers[event] = handler;
    }
  }
};
HTMLMaskElement.EVENTS_MAP = {
  selectionChange: "keydown",
  input: "input",
  drop: "drop",
  click: "click",
  focus: "focus",
  commit: "blur"
};
IMask.HTMLMaskElement = HTMLMaskElement;

// node_modules/imask/esm/controls/html-contenteditable-mask-element.js
var HTMLContenteditableMaskElement = class extends HTMLMaskElement {
  /**
    Returns HTMLElement selection start
    @override
  */
  get _unsafeSelectionStart() {
    const root = this.rootElement;
    const selection = root.getSelection && root.getSelection();
    const anchorOffset = selection && selection.anchorOffset;
    const focusOffset = selection && selection.focusOffset;
    if (focusOffset == null || anchorOffset == null || anchorOffset < focusOffset) {
      return anchorOffset;
    }
    return focusOffset;
  }
  /**
    Returns HTMLElement selection end
    @override
  */
  get _unsafeSelectionEnd() {
    const root = this.rootElement;
    const selection = root.getSelection && root.getSelection();
    const anchorOffset = selection && selection.anchorOffset;
    const focusOffset = selection && selection.focusOffset;
    if (focusOffset == null || anchorOffset == null || anchorOffset > focusOffset) {
      return anchorOffset;
    }
    return focusOffset;
  }
  /**
    Sets HTMLElement selection
    @override
  */
  _unsafeSelect(start, end) {
    if (!this.rootElement.createRange)
      return;
    const range = this.rootElement.createRange();
    range.setStart(this.input.firstChild || this.input, start);
    range.setEnd(this.input.lastChild || this.input, end);
    const root = this.rootElement;
    const selection = root.getSelection && root.getSelection();
    if (selection) {
      selection.removeAllRanges();
      selection.addRange(range);
    }
  }
  /**
    HTMLElement value
    @override
  */
  get value() {
    return this.input.textContent;
  }
  set value(value) {
    this.input.textContent = value;
  }
};
IMask.HTMLContenteditableMaskElement = HTMLContenteditableMaskElement;

// node_modules/imask/esm/controls/input.js
var _excluded4 = ["mask"];
var InputMask = class {
  /**
    View element
    @readonly
  */
  /**
    Internal {@link Masked} model
    @readonly
  */
  /**
    @param {MaskElement|HTMLInputElement|HTMLTextAreaElement} el
    @param {Object} opts
  */
  constructor(el, opts) {
    this.el = el instanceof MaskElement ? el : el.isContentEditable && el.tagName !== "INPUT" && el.tagName !== "TEXTAREA" ? new HTMLContenteditableMaskElement(el) : new HTMLMaskElement(el);
    this.masked = createMask(opts);
    this._listeners = {};
    this._value = "";
    this._unmaskedValue = "";
    this._saveSelection = this._saveSelection.bind(this);
    this._onInput = this._onInput.bind(this);
    this._onChange = this._onChange.bind(this);
    this._onDrop = this._onDrop.bind(this);
    this._onFocus = this._onFocus.bind(this);
    this._onClick = this._onClick.bind(this);
    this.alignCursor = this.alignCursor.bind(this);
    this.alignCursorFriendly = this.alignCursorFriendly.bind(this);
    this._bindEvents();
    this.updateValue();
    this._onChange();
  }
  /** Read or update mask */
  get mask() {
    return this.masked.mask;
  }
  maskEquals(mask) {
    var _this$masked;
    return mask == null || ((_this$masked = this.masked) === null || _this$masked === void 0 ? void 0 : _this$masked.maskEquals(mask));
  }
  set mask(mask) {
    if (this.maskEquals(mask))
      return;
    if (!(mask instanceof IMask.Masked) && this.masked.constructor === maskedClass(mask)) {
      this.masked.updateOptions({
        mask
      });
      return;
    }
    const masked = createMask({
      mask
    });
    masked.unmaskedValue = this.masked.unmaskedValue;
    this.masked = masked;
  }
  /** Raw value */
  get value() {
    return this._value;
  }
  set value(str) {
    if (this.value === str)
      return;
    this.masked.value = str;
    this.updateControl();
    this.alignCursor();
  }
  /** Unmasked value */
  get unmaskedValue() {
    return this._unmaskedValue;
  }
  set unmaskedValue(str) {
    if (this.unmaskedValue === str)
      return;
    this.masked.unmaskedValue = str;
    this.updateControl();
    this.alignCursor();
  }
  /** Typed unmasked value */
  get typedValue() {
    return this.masked.typedValue;
  }
  set typedValue(val) {
    if (this.masked.typedValueEquals(val))
      return;
    this.masked.typedValue = val;
    this.updateControl();
    this.alignCursor();
  }
  /** Display value */
  get displayValue() {
    return this.masked.displayValue;
  }
  /**
    Starts listening to element events
    @protected
  */
  _bindEvents() {
    this.el.bindEvents({
      selectionChange: this._saveSelection,
      input: this._onInput,
      drop: this._onDrop,
      click: this._onClick,
      focus: this._onFocus,
      commit: this._onChange
    });
  }
  /**
    Stops listening to element events
    @protected
   */
  _unbindEvents() {
    if (this.el)
      this.el.unbindEvents();
  }
  /**
    Fires custom event
    @protected
   */
  _fireEvent(ev) {
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }
    const listeners = this._listeners[ev];
    if (!listeners)
      return;
    listeners.forEach((l) => l(...args));
  }
  /**
    Current selection start
    @readonly
  */
  get selectionStart() {
    return this._cursorChanging ? this._changingCursorPos : this.el.selectionStart;
  }
  /** Current cursor position */
  get cursorPos() {
    return this._cursorChanging ? this._changingCursorPos : this.el.selectionEnd;
  }
  set cursorPos(pos) {
    if (!this.el || !this.el.isActive)
      return;
    this.el.select(pos, pos);
    this._saveSelection();
  }
  /**
    Stores current selection
    @protected
  */
  _saveSelection() {
    if (this.displayValue !== this.el.value) {
      console.warn("Element value was changed outside of mask. Syncronize mask using `mask.updateValue()` to work properly.");
    }
    this._selection = {
      start: this.selectionStart,
      end: this.cursorPos
    };
  }
  /** Syncronizes model value from view */
  updateValue() {
    this.masked.value = this.el.value;
    this._value = this.masked.value;
  }
  /** Syncronizes view from model value, fires change events */
  updateControl() {
    const newUnmaskedValue = this.masked.unmaskedValue;
    const newValue = this.masked.value;
    const newDisplayValue = this.displayValue;
    const isChanged = this.unmaskedValue !== newUnmaskedValue || this.value !== newValue;
    this._unmaskedValue = newUnmaskedValue;
    this._value = newValue;
    if (this.el.value !== newDisplayValue)
      this.el.value = newDisplayValue;
    if (isChanged)
      this._fireChangeEvents();
  }
  /** Updates options with deep equal check, recreates @{link Masked} model if mask type changes */
  updateOptions(opts) {
    const {
      mask
    } = opts, restOpts = _objectWithoutPropertiesLoose(opts, _excluded4);
    const updateMask = !this.maskEquals(mask);
    const updateOpts = !objectIncludes(this.masked, restOpts);
    if (updateMask)
      this.mask = mask;
    if (updateOpts)
      this.masked.updateOptions(restOpts);
    if (updateMask || updateOpts)
      this.updateControl();
  }
  /** Updates cursor */
  updateCursor(cursorPos) {
    if (cursorPos == null)
      return;
    this.cursorPos = cursorPos;
    this._delayUpdateCursor(cursorPos);
  }
  /**
    Delays cursor update to support mobile browsers
    @private
  */
  _delayUpdateCursor(cursorPos) {
    this._abortUpdateCursor();
    this._changingCursorPos = cursorPos;
    this._cursorChanging = setTimeout(() => {
      if (!this.el)
        return;
      this.cursorPos = this._changingCursorPos;
      this._abortUpdateCursor();
    }, 10);
  }
  /**
    Fires custom events
    @protected
  */
  _fireChangeEvents() {
    this._fireEvent("accept", this._inputEvent);
    if (this.masked.isComplete)
      this._fireEvent("complete", this._inputEvent);
  }
  /**
    Aborts delayed cursor update
    @private
  */
  _abortUpdateCursor() {
    if (this._cursorChanging) {
      clearTimeout(this._cursorChanging);
      delete this._cursorChanging;
    }
  }
  /** Aligns cursor to nearest available position */
  alignCursor() {
    this.cursorPos = this.masked.nearestInputPos(this.masked.nearestInputPos(this.cursorPos, DIRECTION.LEFT));
  }
  /** Aligns cursor only if selection is empty */
  alignCursorFriendly() {
    if (this.selectionStart !== this.cursorPos)
      return;
    this.alignCursor();
  }
  /** Adds listener on custom event */
  on(ev, handler) {
    if (!this._listeners[ev])
      this._listeners[ev] = [];
    this._listeners[ev].push(handler);
    return this;
  }
  /** Removes custom event listener */
  off(ev, handler) {
    if (!this._listeners[ev])
      return this;
    if (!handler) {
      delete this._listeners[ev];
      return this;
    }
    const hIndex = this._listeners[ev].indexOf(handler);
    if (hIndex >= 0)
      this._listeners[ev].splice(hIndex, 1);
    return this;
  }
  /** Handles view input event */
  _onInput(e) {
    this._inputEvent = e;
    this._abortUpdateCursor();
    if (!this._selection)
      return this.updateValue();
    const details = new ActionDetails(
      // new state
      this.el.value,
      this.cursorPos,
      // old state
      this.displayValue,
      this._selection
    );
    const oldRawValue = this.masked.rawInputValue;
    const offset = this.masked.splice(details.startChangePos, details.removed.length, details.inserted, details.removeDirection, {
      input: true,
      raw: true
    }).offset;
    const removeDirection = oldRawValue === this.masked.rawInputValue ? details.removeDirection : DIRECTION.NONE;
    let cursorPos = this.masked.nearestInputPos(details.startChangePos + offset, removeDirection);
    if (removeDirection !== DIRECTION.NONE)
      cursorPos = this.masked.nearestInputPos(cursorPos, DIRECTION.NONE);
    this.updateControl();
    this.updateCursor(cursorPos);
    delete this._inputEvent;
  }
  /** Handles view change event and commits model value */
  _onChange() {
    if (this.displayValue !== this.el.value) {
      this.updateValue();
    }
    this.masked.doCommit();
    this.updateControl();
    this._saveSelection();
  }
  /** Handles view drop event, prevents by default */
  _onDrop(ev) {
    ev.preventDefault();
    ev.stopPropagation();
  }
  /** Restore last selection on focus */
  _onFocus(ev) {
    this.alignCursorFriendly();
  }
  /** Restore last selection on focus */
  _onClick(ev) {
    this.alignCursorFriendly();
  }
  /** Unbind view events and removes element reference */
  destroy() {
    this._unbindEvents();
    this._listeners.length = 0;
    delete this.el;
  }
};
IMask.InputMask = InputMask;

// node_modules/imask/esm/masked/enum.js
var MaskedEnum = class extends MaskedPattern {
  /**
    @override
    @param {Object} opts
  */
  _update(opts) {
    if (opts.enum)
      opts.mask = "*".repeat(opts.enum[0].length);
    super._update(opts);
  }
  /**
    @override
  */
  doValidate() {
    return this.enum.some((e) => e.indexOf(this.unmaskedValue) >= 0) && super.doValidate(...arguments);
  }
};
IMask.MaskedEnum = MaskedEnum;

// node_modules/imask/esm/masked/number.js
var MaskedNumber = class extends Masked {
  /** Single char */
  /** Single char */
  /** Array of single chars */
  /** */
  /** */
  /** Digits after point */
  /** */
  /** Flag to remove leading and trailing zeros in the end of editing */
  /** Flag to pad trailing zeros after point in the end of editing */
  constructor(opts) {
    super(Object.assign({}, MaskedNumber.DEFAULTS, opts));
  }
  /**
    @override
  */
  _update(opts) {
    super._update(opts);
    this._updateRegExps();
  }
  /** */
  _updateRegExps() {
    let start = "^" + (this.allowNegative ? "[+|\\-]?" : "");
    let mid = "\\d*";
    let end = (this.scale ? "(".concat(escapeRegExp(this.radix), "\\d{0,").concat(this.scale, "})?") : "") + "$";
    this._numberRegExp = new RegExp(start + mid + end);
    this._mapToRadixRegExp = new RegExp("[".concat(this.mapToRadix.map(escapeRegExp).join(""), "]"), "g");
    this._thousandsSeparatorRegExp = new RegExp(escapeRegExp(this.thousandsSeparator), "g");
  }
  /** */
  _removeThousandsSeparators(value) {
    return value.replace(this._thousandsSeparatorRegExp, "");
  }
  /** */
  _insertThousandsSeparators(value) {
    const parts = value.split(this.radix);
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.thousandsSeparator);
    return parts.join(this.radix);
  }
  /**
    @override
  */
  doPrepare(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    ch = this._removeThousandsSeparators(this.scale && this.mapToRadix.length && /*
      radix should be mapped when
      1) input is done from keyboard = flags.input && flags.raw
      2) unmasked value is set = !flags.input && !flags.raw
      and should not be mapped when
      1) value is set = flags.input && !flags.raw
      2) raw value is set = !flags.input && flags.raw
    */
    (flags.input && flags.raw || !flags.input && !flags.raw) ? ch.replace(this._mapToRadixRegExp, this.radix) : ch);
    const [prepCh, details] = normalizePrepare(super.doPrepare(ch, flags));
    if (ch && !prepCh)
      details.skip = true;
    return [prepCh, details];
  }
  /** */
  _separatorsCount(to) {
    let extendOnSeparators = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : false;
    let count = 0;
    for (let pos = 0; pos < to; ++pos) {
      if (this._value.indexOf(this.thousandsSeparator, pos) === pos) {
        ++count;
        if (extendOnSeparators)
          to += this.thousandsSeparator.length;
      }
    }
    return count;
  }
  /** */
  _separatorsCountFromSlice() {
    let slice = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : this._value;
    return this._separatorsCount(this._removeThousandsSeparators(slice).length, true);
  }
  /**
    @override
  */
  extractInput() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    let flags = arguments.length > 2 ? arguments[2] : void 0;
    [fromPos, toPos] = this._adjustRangeWithSeparators(fromPos, toPos);
    return this._removeThousandsSeparators(super.extractInput(fromPos, toPos, flags));
  }
  /**
    @override
  */
  _appendCharRaw(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    if (!this.thousandsSeparator)
      return super._appendCharRaw(ch, flags);
    const prevBeforeTailValue = flags.tail && flags._beforeTailState ? flags._beforeTailState._value : this._value;
    const prevBeforeTailSeparatorsCount = this._separatorsCountFromSlice(prevBeforeTailValue);
    this._value = this._removeThousandsSeparators(this.value);
    const appendDetails = super._appendCharRaw(ch, flags);
    this._value = this._insertThousandsSeparators(this._value);
    const beforeTailValue = flags.tail && flags._beforeTailState ? flags._beforeTailState._value : this._value;
    const beforeTailSeparatorsCount = this._separatorsCountFromSlice(beforeTailValue);
    appendDetails.tailShift += (beforeTailSeparatorsCount - prevBeforeTailSeparatorsCount) * this.thousandsSeparator.length;
    appendDetails.skip = !appendDetails.rawInserted && ch === this.thousandsSeparator;
    return appendDetails;
  }
  /** */
  _findSeparatorAround(pos) {
    if (this.thousandsSeparator) {
      const searchFrom = pos - this.thousandsSeparator.length + 1;
      const separatorPos = this.value.indexOf(this.thousandsSeparator, searchFrom);
      if (separatorPos <= pos)
        return separatorPos;
    }
    return -1;
  }
  _adjustRangeWithSeparators(from, to) {
    const separatorAroundFromPos = this._findSeparatorAround(from);
    if (separatorAroundFromPos >= 0)
      from = separatorAroundFromPos;
    const separatorAroundToPos = this._findSeparatorAround(to);
    if (separatorAroundToPos >= 0)
      to = separatorAroundToPos + this.thousandsSeparator.length;
    return [from, to];
  }
  /**
    @override
  */
  remove() {
    let fromPos = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : 0;
    let toPos = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this.value.length;
    [fromPos, toPos] = this._adjustRangeWithSeparators(fromPos, toPos);
    const valueBeforePos = this.value.slice(0, fromPos);
    const valueAfterPos = this.value.slice(toPos);
    const prevBeforeTailSeparatorsCount = this._separatorsCount(valueBeforePos.length);
    this._value = this._insertThousandsSeparators(this._removeThousandsSeparators(valueBeforePos + valueAfterPos));
    const beforeTailSeparatorsCount = this._separatorsCountFromSlice(valueBeforePos);
    return new ChangeDetails({
      tailShift: (beforeTailSeparatorsCount - prevBeforeTailSeparatorsCount) * this.thousandsSeparator.length
    });
  }
  /**
    @override
  */
  nearestInputPos(cursorPos, direction) {
    if (!this.thousandsSeparator)
      return cursorPos;
    switch (direction) {
      case DIRECTION.NONE:
      case DIRECTION.LEFT:
      case DIRECTION.FORCE_LEFT: {
        const separatorAtLeftPos = this._findSeparatorAround(cursorPos - 1);
        if (separatorAtLeftPos >= 0) {
          const separatorAtLeftEndPos = separatorAtLeftPos + this.thousandsSeparator.length;
          if (cursorPos < separatorAtLeftEndPos || this.value.length <= separatorAtLeftEndPos || direction === DIRECTION.FORCE_LEFT) {
            return separatorAtLeftPos;
          }
        }
        break;
      }
      case DIRECTION.RIGHT:
      case DIRECTION.FORCE_RIGHT: {
        const separatorAtRightPos = this._findSeparatorAround(cursorPos);
        if (separatorAtRightPos >= 0) {
          return separatorAtRightPos + this.thousandsSeparator.length;
        }
      }
    }
    return cursorPos;
  }
  /**
    @override
  */
  doValidate(flags) {
    let valid = Boolean(this._removeThousandsSeparators(this.value).match(this._numberRegExp));
    if (valid) {
      const number = this.number;
      valid = valid && !isNaN(number) && // check min bound for negative values
      (this.min == null || this.min >= 0 || this.min <= this.number) && // check max bound for positive values
      (this.max == null || this.max <= 0 || this.number <= this.max);
    }
    return valid && super.doValidate(flags);
  }
  /**
    @override
  */
  doCommit() {
    if (this.value) {
      const number = this.number;
      let validnum = number;
      if (this.min != null)
        validnum = Math.max(validnum, this.min);
      if (this.max != null)
        validnum = Math.min(validnum, this.max);
      if (validnum !== number)
        this.unmaskedValue = this.doFormat(validnum);
      let formatted = this.value;
      if (this.normalizeZeros)
        formatted = this._normalizeZeros(formatted);
      if (this.padFractionalZeros && this.scale > 0)
        formatted = this._padFractionalZeros(formatted);
      this._value = formatted;
    }
    super.doCommit();
  }
  /** */
  _normalizeZeros(value) {
    const parts = this._removeThousandsSeparators(value).split(this.radix);
    parts[0] = parts[0].replace(/^(\D*)(0*)(\d*)/, (match, sign, zeros, num) => sign + num);
    if (value.length && !/\d$/.test(parts[0]))
      parts[0] = parts[0] + "0";
    if (parts.length > 1) {
      parts[1] = parts[1].replace(/0*$/, "");
      if (!parts[1].length)
        parts.length = 1;
    }
    return this._insertThousandsSeparators(parts.join(this.radix));
  }
  /** */
  _padFractionalZeros(value) {
    if (!value)
      return value;
    const parts = value.split(this.radix);
    if (parts.length < 2)
      parts.push("");
    parts[1] = parts[1].padEnd(this.scale, "0");
    return parts.join(this.radix);
  }
  /** */
  doSkipInvalid(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    let checkTail = arguments.length > 2 ? arguments[2] : void 0;
    const dropFractional = this.scale === 0 && ch !== this.thousandsSeparator && (ch === this.radix || ch === MaskedNumber.UNMASKED_RADIX || this.mapToRadix.includes(ch));
    return super.doSkipInvalid(ch, flags, checkTail) && !dropFractional;
  }
  /**
    @override
  */
  get unmaskedValue() {
    return this._removeThousandsSeparators(this._normalizeZeros(this.value)).replace(this.radix, MaskedNumber.UNMASKED_RADIX);
  }
  set unmaskedValue(unmaskedValue) {
    super.unmaskedValue = unmaskedValue;
  }
  /**
    @override
  */
  get typedValue() {
    return this.doParse(this.unmaskedValue);
  }
  set typedValue(n) {
    this.rawInputValue = this.doFormat(n).replace(MaskedNumber.UNMASKED_RADIX, this.radix);
  }
  /** Parsed Number */
  get number() {
    return this.typedValue;
  }
  set number(number) {
    this.typedValue = number;
  }
  /**
    Is negative allowed
    @readonly
  */
  get allowNegative() {
    return this.signed || this.min != null && this.min < 0 || this.max != null && this.max < 0;
  }
  /**
    @override
  */
  typedValueEquals(value) {
    return (super.typedValueEquals(value) || MaskedNumber.EMPTY_VALUES.includes(value) && MaskedNumber.EMPTY_VALUES.includes(this.typedValue)) && !(value === 0 && this.value === "");
  }
};
MaskedNumber.UNMASKED_RADIX = ".";
MaskedNumber.DEFAULTS = {
  radix: ",",
  thousandsSeparator: "",
  mapToRadix: [MaskedNumber.UNMASKED_RADIX],
  scale: 2,
  signed: false,
  normalizeZeros: true,
  padFractionalZeros: false,
  parse: Number,
  format: (n) => n.toLocaleString("en-US", {
    useGrouping: false,
    maximumFractionDigits: 20
  })
};
MaskedNumber.EMPTY_VALUES = [...Masked.EMPTY_VALUES, 0];
IMask.MaskedNumber = MaskedNumber;

// node_modules/imask/esm/masked/function.js
var MaskedFunction = class extends Masked {
  /**
    @override
    @param {Object} opts
  */
  _update(opts) {
    if (opts.mask)
      opts.validate = opts.mask;
    super._update(opts);
  }
};
IMask.MaskedFunction = MaskedFunction;

// node_modules/imask/esm/masked/dynamic.js
var _excluded5 = ["compiledMasks", "currentMaskRef", "currentMask"];
var _excluded22 = ["mask"];
var MaskedDynamic = class extends Masked {
  /** Currently chosen mask */
  /** Compliled {@link Masked} options */
  /** Chooses {@link Masked} depending on input value */
  /**
    @param {Object} opts
  */
  constructor(opts) {
    super(Object.assign({}, MaskedDynamic.DEFAULTS, opts));
    this.currentMask = null;
  }
  /**
    @override
  */
  _update(opts) {
    super._update(opts);
    if ("mask" in opts) {
      this.compiledMasks = Array.isArray(opts.mask) ? opts.mask.map((m) => createMask(m)) : [];
    }
  }
  /**
    @override
  */
  _appendCharRaw(ch) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    const details = this._applyDispatch(ch, flags);
    if (this.currentMask) {
      details.aggregate(this.currentMask._appendChar(ch, this.currentMaskFlags(flags)));
    }
    return details;
  }
  _applyDispatch() {
    let appended = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : "";
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    let tail = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : "";
    const prevValueBeforeTail = flags.tail && flags._beforeTailState != null ? flags._beforeTailState._value : this.value;
    const inputValue = this.rawInputValue;
    const insertValue = flags.tail && flags._beforeTailState != null ? (
      // $FlowFixMe - tired to fight with type system
      flags._beforeTailState._rawInputValue
    ) : inputValue;
    const tailValue = inputValue.slice(insertValue.length);
    const prevMask = this.currentMask;
    const details = new ChangeDetails();
    const prevMaskState = prevMask === null || prevMask === void 0 ? void 0 : prevMask.state;
    this.currentMask = this.doDispatch(appended, Object.assign({}, flags), tail);
    if (this.currentMask) {
      if (this.currentMask !== prevMask) {
        this.currentMask.reset();
        if (insertValue) {
          const d = this.currentMask.append(insertValue, {
            raw: true
          });
          details.tailShift = d.inserted.length - prevValueBeforeTail.length;
        }
        if (tailValue) {
          details.tailShift += this.currentMask.append(tailValue, {
            raw: true,
            tail: true
          }).tailShift;
        }
      } else {
        this.currentMask.state = prevMaskState;
      }
    }
    return details;
  }
  _appendPlaceholder() {
    const details = this._applyDispatch(...arguments);
    if (this.currentMask) {
      details.aggregate(this.currentMask._appendPlaceholder());
    }
    return details;
  }
  /**
   @override
  */
  _appendEager() {
    const details = this._applyDispatch(...arguments);
    if (this.currentMask) {
      details.aggregate(this.currentMask._appendEager());
    }
    return details;
  }
  appendTail(tail) {
    const details = new ChangeDetails();
    if (tail)
      details.aggregate(this._applyDispatch("", {}, tail));
    return details.aggregate(this.currentMask ? this.currentMask.appendTail(tail) : super.appendTail(tail));
  }
  currentMaskFlags(flags) {
    var _flags$_beforeTailSta, _flags$_beforeTailSta2;
    return Object.assign({}, flags, {
      _beforeTailState: ((_flags$_beforeTailSta = flags._beforeTailState) === null || _flags$_beforeTailSta === void 0 ? void 0 : _flags$_beforeTailSta.currentMaskRef) === this.currentMask && ((_flags$_beforeTailSta2 = flags._beforeTailState) === null || _flags$_beforeTailSta2 === void 0 ? void 0 : _flags$_beforeTailSta2.currentMask) || flags._beforeTailState
    });
  }
  /**
    @override
  */
  doDispatch(appended) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    let tail = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : "";
    return this.dispatch(appended, this, flags, tail);
  }
  /**
    @override
  */
  doValidate(flags) {
    return super.doValidate(flags) && (!this.currentMask || this.currentMask.doValidate(this.currentMaskFlags(flags)));
  }
  /**
    @override
  */
  doPrepare(str) {
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
    let [s, details] = normalizePrepare(super.doPrepare(str, flags));
    if (this.currentMask) {
      let currentDetails;
      [s, currentDetails] = normalizePrepare(super.doPrepare(s, this.currentMaskFlags(flags)));
      details = details.aggregate(currentDetails);
    }
    return [s, details];
  }
  /**
    @override
  */
  reset() {
    var _this$currentMask;
    (_this$currentMask = this.currentMask) === null || _this$currentMask === void 0 ? void 0 : _this$currentMask.reset();
    this.compiledMasks.forEach((m) => m.reset());
  }
  /**
    @override
  */
  get value() {
    return this.currentMask ? this.currentMask.value : "";
  }
  set value(value) {
    super.value = value;
  }
  /**
    @override
  */
  get unmaskedValue() {
    return this.currentMask ? this.currentMask.unmaskedValue : "";
  }
  set unmaskedValue(unmaskedValue) {
    super.unmaskedValue = unmaskedValue;
  }
  /**
    @override
  */
  get typedValue() {
    return this.currentMask ? this.currentMask.typedValue : "";
  }
  // probably typedValue should not be used with dynamic
  set typedValue(value) {
    let unmaskedValue = String(value);
    if (this.currentMask) {
      this.currentMask.typedValue = value;
      unmaskedValue = this.currentMask.unmaskedValue;
    }
    this.unmaskedValue = unmaskedValue;
  }
  get displayValue() {
    return this.currentMask ? this.currentMask.displayValue : "";
  }
  /**
    @override
  */
  get isComplete() {
    var _this$currentMask2;
    return Boolean((_this$currentMask2 = this.currentMask) === null || _this$currentMask2 === void 0 ? void 0 : _this$currentMask2.isComplete);
  }
  /**
    @override
  */
  get isFilled() {
    var _this$currentMask3;
    return Boolean((_this$currentMask3 = this.currentMask) === null || _this$currentMask3 === void 0 ? void 0 : _this$currentMask3.isFilled);
  }
  /**
    @override
  */
  remove() {
    const details = new ChangeDetails();
    if (this.currentMask) {
      details.aggregate(this.currentMask.remove(...arguments)).aggregate(this._applyDispatch());
    }
    return details;
  }
  /**
    @override
  */
  get state() {
    var _this$currentMask4;
    return Object.assign({}, super.state, {
      _rawInputValue: this.rawInputValue,
      compiledMasks: this.compiledMasks.map((m) => m.state),
      currentMaskRef: this.currentMask,
      currentMask: (_this$currentMask4 = this.currentMask) === null || _this$currentMask4 === void 0 ? void 0 : _this$currentMask4.state
    });
  }
  set state(state) {
    const {
      compiledMasks,
      currentMaskRef,
      currentMask
    } = state, maskedState = _objectWithoutPropertiesLoose(state, _excluded5);
    this.compiledMasks.forEach((m, mi) => m.state = compiledMasks[mi]);
    if (currentMaskRef != null) {
      this.currentMask = currentMaskRef;
      this.currentMask.state = currentMask;
    }
    super.state = maskedState;
  }
  /**
    @override
  */
  extractInput() {
    return this.currentMask ? this.currentMask.extractInput(...arguments) : "";
  }
  /**
    @override
  */
  extractTail() {
    return this.currentMask ? this.currentMask.extractTail(...arguments) : super.extractTail(...arguments);
  }
  /**
    @override
  */
  doCommit() {
    if (this.currentMask)
      this.currentMask.doCommit();
    super.doCommit();
  }
  /**
    @override
  */
  nearestInputPos() {
    return this.currentMask ? this.currentMask.nearestInputPos(...arguments) : super.nearestInputPos(...arguments);
  }
  get overwrite() {
    return this.currentMask ? this.currentMask.overwrite : super.overwrite;
  }
  set overwrite(overwrite) {
    console.warn('"overwrite" option is not available in dynamic mask, use this option in siblings');
  }
  get eager() {
    return this.currentMask ? this.currentMask.eager : super.eager;
  }
  set eager(eager) {
    console.warn('"eager" option is not available in dynamic mask, use this option in siblings');
  }
  get skipInvalid() {
    return this.currentMask ? this.currentMask.skipInvalid : super.skipInvalid;
  }
  set skipInvalid(skipInvalid) {
    if (this.isInitialized || skipInvalid !== Masked.DEFAULTS.skipInvalid) {
      console.warn('"skipInvalid" option is not available in dynamic mask, use this option in siblings');
    }
  }
  /**
    @override
  */
  maskEquals(mask) {
    return Array.isArray(mask) && this.compiledMasks.every((m, mi) => {
      if (!mask[mi])
        return;
      const _mask$mi = mask[mi], {
        mask: oldMask
      } = _mask$mi, restOpts = _objectWithoutPropertiesLoose(_mask$mi, _excluded22);
      return objectIncludes(m, restOpts) && m.maskEquals(oldMask);
    });
  }
  /**
    @override
  */
  typedValueEquals(value) {
    var _this$currentMask5;
    return Boolean((_this$currentMask5 = this.currentMask) === null || _this$currentMask5 === void 0 ? void 0 : _this$currentMask5.typedValueEquals(value));
  }
};
MaskedDynamic.DEFAULTS = {
  dispatch: (appended, masked, flags, tail) => {
    if (!masked.compiledMasks.length)
      return;
    const inputValue = masked.rawInputValue;
    const inputs = masked.compiledMasks.map((m, index) => {
      const isCurrent = masked.currentMask === m;
      const startInputPos = isCurrent ? m.value.length : m.nearestInputPos(m.value.length, DIRECTION.FORCE_LEFT);
      if (m.rawInputValue !== inputValue) {
        m.reset();
        m.append(inputValue, {
          raw: true
        });
      } else if (!isCurrent) {
        m.remove(startInputPos);
      }
      m.append(appended, masked.currentMaskFlags(flags));
      m.appendTail(tail);
      return {
        index,
        weight: m.rawInputValue.length,
        totalInputPositions: m.totalInputPositions(0, Math.max(startInputPos, m.nearestInputPos(m.value.length, DIRECTION.FORCE_LEFT)))
      };
    });
    inputs.sort((i1, i2) => i2.weight - i1.weight || i2.totalInputPositions - i1.totalInputPositions);
    return masked.compiledMasks[inputs[0].index];
  }
};
IMask.MaskedDynamic = MaskedDynamic;

// node_modules/imask/esm/masked/pipe.js
var PIPE_TYPE = {
  MASKED: "value",
  UNMASKED: "unmaskedValue",
  TYPED: "typedValue"
};
function createPipe(mask) {
  let from = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : PIPE_TYPE.MASKED;
  let to = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : PIPE_TYPE.MASKED;
  const masked = createMask(mask);
  return (value) => masked.runIsolated((m) => {
    m[from] = value;
    return m[to];
  });
}
function pipe(value) {
  for (var _len = arguments.length, pipeArgs = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    pipeArgs[_key - 1] = arguments[_key];
  }
  return createPipe(...pipeArgs)(value);
}
IMask.PIPE_TYPE = PIPE_TYPE;
IMask.createPipe = createPipe;
IMask.pipe = pipe;

// node_modules/imask/esm/index.js
try {
  globalThis.IMask = IMask;
} catch (e) {
}

// packages/forms/resources/js/components/text-input.js
function textInputFormComponent({ getMaskOptionsUsing, state }) {
  return {
    isStateBeingUpdated: false,
    mask: null,
    state,
    init: function() {
      if (!getMaskOptionsUsing) {
        return;
      }
      if (this.state) {
        this.$el.value = this.state?.valueOf();
      }
      this.mask = IMask(this.$el, getMaskOptionsUsing(IMask)).on(
        "accept",
        () => {
          this.isStateBeingUpdated = true;
          this.state = this.mask.unmaskedValue;
          this.$nextTick(() => this.isStateBeingUpdated = false);
        }
      );
      this.$watch("state", () => {
        if (this.isStateBeingUpdated) {
          return;
        }
        this.mask.unmaskedValue = this.state?.valueOf() ?? "";
      });
    }
  };
}
export {
  textInputFormComponent as default
};
