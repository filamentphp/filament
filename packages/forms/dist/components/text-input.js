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
    let flags = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {
      input: true
    };
    this.reset();
    this.append(value, flags, "");
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
    if ((this.eager === true || this.eager === "append") && flags !== null && flags !== void 0 && flags.input && str) {
      details.aggregate(this._appendEager());
    }
    if (checkTail != null) {
      details.tailShift += this.appendTail(checkTail).tailShift;
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
      if (typeof this.state !== "undefined") {
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9fcm9sbHVwUGx1Z2luQmFiZWxIZWxwZXJzLTZiM2JkNDA0LmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vY29yZS9ob2xkZXIuanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9jb3JlL2NoYW5nZS1kZXRhaWxzLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vY29yZS91dGlscy5qcyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvaW1hc2svZXNtL2NvcmUvYWN0aW9uLWRldGFpbHMuanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL2Jhc2UuanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9tYXNrZWQvZmFjdG9yeS5qcyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvaW1hc2svZXNtL21hc2tlZC9wYXR0ZXJuL2lucHV0LWRlZmluaXRpb24uanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9tYXNrZWQvcGF0dGVybi9maXhlZC1kZWZpbml0aW9uLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL3BhdHRlcm4vY2h1bmstdGFpbC1kZXRhaWxzLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL3BhdHRlcm4vY3Vyc29yLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL3JlZ2V4cC5qcyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvaW1hc2svZXNtL21hc2tlZC9wYXR0ZXJuLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL3JhbmdlLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL2RhdGUuanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9jb250cm9scy9tYXNrLWVsZW1lbnQuanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9jb250cm9scy9odG1sLW1hc2stZWxlbWVudC5qcyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvaW1hc2svZXNtL2NvbnRyb2xzL2h0bWwtY29udGVudGVkaXRhYmxlLW1hc2stZWxlbWVudC5qcyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvaW1hc2svZXNtL2NvbnRyb2xzL2lucHV0LmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL2VudW0uanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9tYXNrZWQvbnVtYmVyLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL2Z1bmN0aW9uLmpzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9pbWFzay9lc20vbWFza2VkL2R5bmFtaWMuanMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2ltYXNrL2VzbS9tYXNrZWQvcGlwZS5qcyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvaW1hc2svZXNtL2luZGV4LmpzIiwgIi4uLy4uL3Jlc291cmNlcy9qcy9jb21wb25lbnRzL3RleHQtaW5wdXQuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImZ1bmN0aW9uIF9vYmplY3RXaXRob3V0UHJvcGVydGllc0xvb3NlKHNvdXJjZSwgZXhjbHVkZWQpIHtcbiAgaWYgKHNvdXJjZSA9PSBudWxsKSByZXR1cm4ge307XG4gIHZhciB0YXJnZXQgPSB7fTtcbiAgdmFyIHNvdXJjZUtleXMgPSBPYmplY3Qua2V5cyhzb3VyY2UpO1xuICB2YXIga2V5LCBpO1xuICBmb3IgKGkgPSAwOyBpIDwgc291cmNlS2V5cy5sZW5ndGg7IGkrKykge1xuICAgIGtleSA9IHNvdXJjZUtleXNbaV07XG4gICAgaWYgKGV4Y2x1ZGVkLmluZGV4T2Yoa2V5KSA+PSAwKSBjb250aW51ZTtcbiAgICB0YXJnZXRba2V5XSA9IHNvdXJjZVtrZXldO1xuICB9XG4gIHJldHVybiB0YXJnZXQ7XG59XG5cbmV4cG9ydCB7IF9vYmplY3RXaXRob3V0UHJvcGVydGllc0xvb3NlIGFzIF8gfTtcbiIsICIvKipcbiAqIEFwcGxpZXMgbWFzayBvbiBlbGVtZW50LlxuICogQGNvbnN0cnVjdG9yXG4gKiBAcGFyYW0ge0hUTUxJbnB1dEVsZW1lbnR8SFRNTFRleHRBcmVhRWxlbWVudHxNYXNrRWxlbWVudH0gZWwgLSBFbGVtZW50IHRvIGFwcGx5IG1hc2tcbiAqIEBwYXJhbSB7T2JqZWN0fSBvcHRzIC0gQ3VzdG9tIG1hc2sgb3B0aW9uc1xuICogQHJldHVybiB7SW5wdXRNYXNrfVxuICovXG5mdW5jdGlvbiBJTWFzayhlbCkge1xuICBsZXQgb3B0cyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDoge307XG4gIC8vIGN1cnJlbnRseSBhdmFpbGFibGUgb25seSBmb3IgaW5wdXQtbGlrZSBlbGVtZW50c1xuICByZXR1cm4gbmV3IElNYXNrLklucHV0TWFzayhlbCwgb3B0cyk7XG59XG5cbmV4cG9ydCB7IElNYXNrIGFzIGRlZmF1bHQgfTtcbiIsICJpbXBvcnQgSU1hc2sgZnJvbSAnLi9ob2xkZXIuanMnO1xuXG4vKipcbiAgUHJvdmlkZXMgZGV0YWlscyBvZiBjaGFuZ2luZyBtb2RlbCB2YWx1ZVxuICBAcGFyYW0ge09iamVjdH0gW2RldGFpbHNdXG4gIEBwYXJhbSB7c3RyaW5nfSBbZGV0YWlscy5pbnNlcnRlZF0gLSBJbnNlcnRlZCBzeW1ib2xzXG4gIEBwYXJhbSB7Ym9vbGVhbn0gW2RldGFpbHMuc2tpcF0gLSBDYW4gc2tpcCBjaGFyc1xuICBAcGFyYW0ge251bWJlcn0gW2RldGFpbHMucmVtb3ZlQ291bnRdIC0gUmVtb3ZlZCBzeW1ib2xzIGNvdW50XG4gIEBwYXJhbSB7bnVtYmVyfSBbZGV0YWlscy50YWlsU2hpZnRdIC0gQWRkaXRpb25hbCBvZmZzZXQgaWYgYW55IGNoYW5nZXMgb2NjdXJyZWQgYmVmb3JlIHRhaWxcbiovXG5jbGFzcyBDaGFuZ2VEZXRhaWxzIHtcbiAgLyoqIEluc2VydGVkIHN5bWJvbHMgKi9cblxuICAvKiogQ2FuIHNraXAgY2hhcnMgKi9cblxuICAvKiogQWRkaXRpb25hbCBvZmZzZXQgaWYgYW55IGNoYW5nZXMgb2NjdXJyZWQgYmVmb3JlIHRhaWwgKi9cblxuICAvKiogUmF3IGluc2VydGVkIGlzIHVzZWQgYnkgZHluYW1pYyBtYXNrICovXG5cbiAgY29uc3RydWN0b3IoZGV0YWlscykge1xuICAgIE9iamVjdC5hc3NpZ24odGhpcywge1xuICAgICAgaW5zZXJ0ZWQ6ICcnLFxuICAgICAgcmF3SW5zZXJ0ZWQ6ICcnLFxuICAgICAgc2tpcDogZmFsc2UsXG4gICAgICB0YWlsU2hpZnQ6IDBcbiAgICB9LCBkZXRhaWxzKTtcbiAgfVxuXG4gIC8qKlxuICAgIEFnZ3JlZ2F0ZSBjaGFuZ2VzXG4gICAgQHJldHVybnMge0NoYW5nZURldGFpbHN9IGB0aGlzYFxuICAqL1xuICBhZ2dyZWdhdGUoZGV0YWlscykge1xuICAgIHRoaXMucmF3SW5zZXJ0ZWQgKz0gZGV0YWlscy5yYXdJbnNlcnRlZDtcbiAgICB0aGlzLnNraXAgPSB0aGlzLnNraXAgfHwgZGV0YWlscy5za2lwO1xuICAgIHRoaXMuaW5zZXJ0ZWQgKz0gZGV0YWlscy5pbnNlcnRlZDtcbiAgICB0aGlzLnRhaWxTaGlmdCArPSBkZXRhaWxzLnRhaWxTaGlmdDtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8qKiBUb3RhbCBvZmZzZXQgY29uc2lkZXJpbmcgYWxsIGNoYW5nZXMgKi9cbiAgZ2V0IG9mZnNldCgpIHtcbiAgICByZXR1cm4gdGhpcy50YWlsU2hpZnQgKyB0aGlzLmluc2VydGVkLmxlbmd0aDtcbiAgfVxufVxuSU1hc2suQ2hhbmdlRGV0YWlscyA9IENoYW5nZURldGFpbHM7XG5cbmV4cG9ydCB7IENoYW5nZURldGFpbHMgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBDaGFuZ2VEZXRhaWxzIGZyb20gJy4vY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuL2hvbGRlci5qcyc7XG5cbi8qKiBDaGVja3MgaWYgdmFsdWUgaXMgc3RyaW5nICovXG5mdW5jdGlvbiBpc1N0cmluZyhzdHIpIHtcbiAgcmV0dXJuIHR5cGVvZiBzdHIgPT09ICdzdHJpbmcnIHx8IHN0ciBpbnN0YW5jZW9mIFN0cmluZztcbn1cblxuLyoqXG4gIERpcmVjdGlvblxuICBAcHJvcCB7c3RyaW5nfSBOT05FXG4gIEBwcm9wIHtzdHJpbmd9IExFRlRcbiAgQHByb3Age3N0cmluZ30gRk9SQ0VfTEVGVFxuICBAcHJvcCB7c3RyaW5nfSBSSUdIVFxuICBAcHJvcCB7c3RyaW5nfSBGT1JDRV9SSUdIVFxuKi9cbmNvbnN0IERJUkVDVElPTiA9IHtcbiAgTk9ORTogJ05PTkUnLFxuICBMRUZUOiAnTEVGVCcsXG4gIEZPUkNFX0xFRlQ6ICdGT1JDRV9MRUZUJyxcbiAgUklHSFQ6ICdSSUdIVCcsXG4gIEZPUkNFX1JJR0hUOiAnRk9SQ0VfUklHSFQnXG59O1xuLyoqXG4gIERpcmVjdGlvblxuICBAZW51bSB7c3RyaW5nfVxuKi9cblxuLyoqIFJldHVybnMgbmV4dCBjaGFyIGluZGV4IGluIGRpcmVjdGlvbiAqL1xuZnVuY3Rpb24gaW5kZXhJbkRpcmVjdGlvbihwb3MsIGRpcmVjdGlvbikge1xuICBpZiAoZGlyZWN0aW9uID09PSBESVJFQ1RJT04uTEVGVCkgLS1wb3M7XG4gIHJldHVybiBwb3M7XG59XG5cbi8qKiBSZXR1cm5zIG5leHQgY2hhciBwb3NpdGlvbiBpbiBkaXJlY3Rpb24gKi9cbmZ1bmN0aW9uIHBvc0luRGlyZWN0aW9uKHBvcywgZGlyZWN0aW9uKSB7XG4gIHN3aXRjaCAoZGlyZWN0aW9uKSB7XG4gICAgY2FzZSBESVJFQ1RJT04uTEVGVDpcbiAgICBjYXNlIERJUkVDVElPTi5GT1JDRV9MRUZUOlxuICAgICAgcmV0dXJuIC0tcG9zO1xuICAgIGNhc2UgRElSRUNUSU9OLlJJR0hUOlxuICAgIGNhc2UgRElSRUNUSU9OLkZPUkNFX1JJR0hUOlxuICAgICAgcmV0dXJuICsrcG9zO1xuICAgIGRlZmF1bHQ6XG4gICAgICByZXR1cm4gcG9zO1xuICB9XG59XG5cbi8qKiAqL1xuZnVuY3Rpb24gZm9yY2VEaXJlY3Rpb24oZGlyZWN0aW9uKSB7XG4gIHN3aXRjaCAoZGlyZWN0aW9uKSB7XG4gICAgY2FzZSBESVJFQ1RJT04uTEVGVDpcbiAgICAgIHJldHVybiBESVJFQ1RJT04uRk9SQ0VfTEVGVDtcbiAgICBjYXNlIERJUkVDVElPTi5SSUdIVDpcbiAgICAgIHJldHVybiBESVJFQ1RJT04uRk9SQ0VfUklHSFQ7XG4gICAgZGVmYXVsdDpcbiAgICAgIHJldHVybiBkaXJlY3Rpb247XG4gIH1cbn1cblxuLyoqIEVzY2FwZXMgcmVndWxhciBleHByZXNzaW9uIGNvbnRyb2wgY2hhcnMgKi9cbmZ1bmN0aW9uIGVzY2FwZVJlZ0V4cChzdHIpIHtcbiAgcmV0dXJuIHN0ci5yZXBsYWNlKC8oWy4qKz9ePSE6JHt9KCl8W1xcXVxcL1xcXFxdKS9nLCAnXFxcXCQxJyk7XG59XG5mdW5jdGlvbiBub3JtYWxpemVQcmVwYXJlKHByZXApIHtcbiAgcmV0dXJuIEFycmF5LmlzQXJyYXkocHJlcCkgPyBwcmVwIDogW3ByZXAsIG5ldyBDaGFuZ2VEZXRhaWxzKCldO1xufVxuXG4vLyBjbG9uZWQgZnJvbSBodHRwczovL2dpdGh1Yi5jb20vZXBvYmVyZXpraW4vZmFzdC1kZWVwLWVxdWFsIHdpdGggc21hbGwgY2hhbmdlc1xuZnVuY3Rpb24gb2JqZWN0SW5jbHVkZXMoYiwgYSkge1xuICBpZiAoYSA9PT0gYikgcmV0dXJuIHRydWU7XG4gIHZhciBhcnJBID0gQXJyYXkuaXNBcnJheShhKSxcbiAgICBhcnJCID0gQXJyYXkuaXNBcnJheShiKSxcbiAgICBpO1xuICBpZiAoYXJyQSAmJiBhcnJCKSB7XG4gICAgaWYgKGEubGVuZ3RoICE9IGIubGVuZ3RoKSByZXR1cm4gZmFsc2U7XG4gICAgZm9yIChpID0gMDsgaSA8IGEubGVuZ3RoOyBpKyspIGlmICghb2JqZWN0SW5jbHVkZXMoYVtpXSwgYltpXSkpIHJldHVybiBmYWxzZTtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxuICBpZiAoYXJyQSAhPSBhcnJCKSByZXR1cm4gZmFsc2U7XG4gIGlmIChhICYmIGIgJiYgdHlwZW9mIGEgPT09ICdvYmplY3QnICYmIHR5cGVvZiBiID09PSAnb2JqZWN0Jykge1xuICAgIHZhciBkYXRlQSA9IGEgaW5zdGFuY2VvZiBEYXRlLFxuICAgICAgZGF0ZUIgPSBiIGluc3RhbmNlb2YgRGF0ZTtcbiAgICBpZiAoZGF0ZUEgJiYgZGF0ZUIpIHJldHVybiBhLmdldFRpbWUoKSA9PSBiLmdldFRpbWUoKTtcbiAgICBpZiAoZGF0ZUEgIT0gZGF0ZUIpIHJldHVybiBmYWxzZTtcbiAgICB2YXIgcmVnZXhwQSA9IGEgaW5zdGFuY2VvZiBSZWdFeHAsXG4gICAgICByZWdleHBCID0gYiBpbnN0YW5jZW9mIFJlZ0V4cDtcbiAgICBpZiAocmVnZXhwQSAmJiByZWdleHBCKSByZXR1cm4gYS50b1N0cmluZygpID09IGIudG9TdHJpbmcoKTtcbiAgICBpZiAocmVnZXhwQSAhPSByZWdleHBCKSByZXR1cm4gZmFsc2U7XG4gICAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhhKTtcbiAgICAvLyBpZiAoa2V5cy5sZW5ndGggIT09IE9iamVjdC5rZXlzKGIpLmxlbmd0aCkgcmV0dXJuIGZhbHNlO1xuXG4gICAgZm9yIChpID0gMDsgaSA8IGtleXMubGVuZ3RoOyBpKyspXG4gICAgLy8gJEZsb3dGaXhNZSAuLi4gPz8/XG4gICAgaWYgKCFPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwoYiwga2V5c1tpXSkpIHJldHVybiBmYWxzZTtcbiAgICBmb3IgKGkgPSAwOyBpIDwga2V5cy5sZW5ndGg7IGkrKykgaWYgKCFvYmplY3RJbmNsdWRlcyhiW2tleXNbaV1dLCBhW2tleXNbaV1dKSkgcmV0dXJuIGZhbHNlO1xuICAgIHJldHVybiB0cnVlO1xuICB9IGVsc2UgaWYgKGEgJiYgYiAmJiB0eXBlb2YgYSA9PT0gJ2Z1bmN0aW9uJyAmJiB0eXBlb2YgYiA9PT0gJ2Z1bmN0aW9uJykge1xuICAgIHJldHVybiBhLnRvU3RyaW5nKCkgPT09IGIudG9TdHJpbmcoKTtcbiAgfVxuICByZXR1cm4gZmFsc2U7XG59XG5cbi8qKiBTZWxlY3Rpb24gcmFuZ2UgKi9cblxuZXhwb3J0IHsgRElSRUNUSU9OLCBlc2NhcGVSZWdFeHAsIGZvcmNlRGlyZWN0aW9uLCBpbmRleEluRGlyZWN0aW9uLCBpc1N0cmluZywgbm9ybWFsaXplUHJlcGFyZSwgb2JqZWN0SW5jbHVkZXMsIHBvc0luRGlyZWN0aW9uIH07XG4iLCAiaW1wb3J0IHsgRElSRUNUSU9OIH0gZnJvbSAnLi91dGlscy5qcyc7XG5pbXBvcnQgJy4vY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuL2hvbGRlci5qcyc7XG5cbi8qKiBQcm92aWRlcyBkZXRhaWxzIG9mIGNoYW5naW5nIGlucHV0ICovXG5jbGFzcyBBY3Rpb25EZXRhaWxzIHtcbiAgLyoqIEN1cnJlbnQgaW5wdXQgdmFsdWUgKi9cblxuICAvKiogQ3VycmVudCBjdXJzb3IgcG9zaXRpb24gKi9cblxuICAvKiogT2xkIGlucHV0IHZhbHVlICovXG5cbiAgLyoqIE9sZCBzZWxlY3Rpb24gKi9cblxuICBjb25zdHJ1Y3Rvcih2YWx1ZSwgY3Vyc29yUG9zLCBvbGRWYWx1ZSwgb2xkU2VsZWN0aW9uKSB7XG4gICAgdGhpcy52YWx1ZSA9IHZhbHVlO1xuICAgIHRoaXMuY3Vyc29yUG9zID0gY3Vyc29yUG9zO1xuICAgIHRoaXMub2xkVmFsdWUgPSBvbGRWYWx1ZTtcbiAgICB0aGlzLm9sZFNlbGVjdGlvbiA9IG9sZFNlbGVjdGlvbjtcblxuICAgIC8vIGRvdWJsZSBjaGVjayBpZiBsZWZ0IHBhcnQgd2FzIGNoYW5nZWQgKGF1dG9maWxsaW5nLCBvdGhlciBub24tc3RhbmRhcmQgaW5wdXQgdHJpZ2dlcnMpXG4gICAgd2hpbGUgKHRoaXMudmFsdWUuc2xpY2UoMCwgdGhpcy5zdGFydENoYW5nZVBvcykgIT09IHRoaXMub2xkVmFsdWUuc2xpY2UoMCwgdGhpcy5zdGFydENoYW5nZVBvcykpIHtcbiAgICAgIC0tdGhpcy5vbGRTZWxlY3Rpb24uc3RhcnQ7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAgU3RhcnQgY2hhbmdpbmcgcG9zaXRpb25cbiAgICBAcmVhZG9ubHlcbiAgKi9cbiAgZ2V0IHN0YXJ0Q2hhbmdlUG9zKCkge1xuICAgIHJldHVybiBNYXRoLm1pbih0aGlzLmN1cnNvclBvcywgdGhpcy5vbGRTZWxlY3Rpb24uc3RhcnQpO1xuICB9XG5cbiAgLyoqXG4gICAgSW5zZXJ0ZWQgc3ltYm9scyBjb3VudFxuICAgIEByZWFkb25seVxuICAqL1xuICBnZXQgaW5zZXJ0ZWRDb3VudCgpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJzb3JQb3MgLSB0aGlzLnN0YXJ0Q2hhbmdlUG9zO1xuICB9XG5cbiAgLyoqXG4gICAgSW5zZXJ0ZWQgc3ltYm9sc1xuICAgIEByZWFkb25seVxuICAqL1xuICBnZXQgaW5zZXJ0ZWQoKSB7XG4gICAgcmV0dXJuIHRoaXMudmFsdWUuc3Vic3RyKHRoaXMuc3RhcnRDaGFuZ2VQb3MsIHRoaXMuaW5zZXJ0ZWRDb3VudCk7XG4gIH1cblxuICAvKipcbiAgICBSZW1vdmVkIHN5bWJvbHMgY291bnRcbiAgICBAcmVhZG9ubHlcbiAgKi9cbiAgZ2V0IHJlbW92ZWRDb3VudCgpIHtcbiAgICAvLyBNYXRoLm1heCBmb3Igb3Bwb3NpdGUgb3BlcmF0aW9uXG4gICAgcmV0dXJuIE1hdGgubWF4KHRoaXMub2xkU2VsZWN0aW9uLmVuZCAtIHRoaXMuc3RhcnRDaGFuZ2VQb3MgfHxcbiAgICAvLyBmb3IgRGVsZXRlXG4gICAgdGhpcy5vbGRWYWx1ZS5sZW5ndGggLSB0aGlzLnZhbHVlLmxlbmd0aCwgMCk7XG4gIH1cblxuICAvKipcbiAgICBSZW1vdmVkIHN5bWJvbHNcbiAgICBAcmVhZG9ubHlcbiAgKi9cbiAgZ2V0IHJlbW92ZWQoKSB7XG4gICAgcmV0dXJuIHRoaXMub2xkVmFsdWUuc3Vic3RyKHRoaXMuc3RhcnRDaGFuZ2VQb3MsIHRoaXMucmVtb3ZlZENvdW50KTtcbiAgfVxuXG4gIC8qKlxuICAgIFVuY2hhbmdlZCBoZWFkIHN5bWJvbHNcbiAgICBAcmVhZG9ubHlcbiAgKi9cbiAgZ2V0IGhlYWQoKSB7XG4gICAgcmV0dXJuIHRoaXMudmFsdWUuc3Vic3RyaW5nKDAsIHRoaXMuc3RhcnRDaGFuZ2VQb3MpO1xuICB9XG5cbiAgLyoqXG4gICAgVW5jaGFuZ2VkIHRhaWwgc3ltYm9sc1xuICAgIEByZWFkb25seVxuICAqL1xuICBnZXQgdGFpbCgpIHtcbiAgICByZXR1cm4gdGhpcy52YWx1ZS5zdWJzdHJpbmcodGhpcy5zdGFydENoYW5nZVBvcyArIHRoaXMuaW5zZXJ0ZWRDb3VudCk7XG4gIH1cblxuICAvKipcbiAgICBSZW1vdmUgZGlyZWN0aW9uXG4gICAgQHJlYWRvbmx5XG4gICovXG4gIGdldCByZW1vdmVEaXJlY3Rpb24oKSB7XG4gICAgaWYgKCF0aGlzLnJlbW92ZWRDb3VudCB8fCB0aGlzLmluc2VydGVkQ291bnQpIHJldHVybiBESVJFQ1RJT04uTk9ORTtcblxuICAgIC8vIGFsaWduIHJpZ2h0IGlmIGRlbGV0ZSBhdCByaWdodFxuICAgIHJldHVybiAodGhpcy5vbGRTZWxlY3Rpb24uZW5kID09PSB0aGlzLmN1cnNvclBvcyB8fCB0aGlzLm9sZFNlbGVjdGlvbi5zdGFydCA9PT0gdGhpcy5jdXJzb3JQb3MpICYmXG4gICAgLy8gaWYgbm90IHJhbmdlIHJlbW92ZWQgKGV2ZW50IHdpdGggYmFja3NwYWNlKVxuICAgIHRoaXMub2xkU2VsZWN0aW9uLmVuZCA9PT0gdGhpcy5vbGRTZWxlY3Rpb24uc3RhcnQgPyBESVJFQ1RJT04uUklHSFQgOiBESVJFQ1RJT04uTEVGVDtcbiAgfVxufVxuXG5leHBvcnQgeyBBY3Rpb25EZXRhaWxzIGFzIGRlZmF1bHQgfTtcbiIsICIvKiogUHJvdmlkZXMgZGV0YWlscyBvZiBjb250aW51b3VzIGV4dHJhY3RlZCB0YWlsICovXG5jbGFzcyBDb250aW51b3VzVGFpbERldGFpbHMge1xuICAvKiogVGFpbCB2YWx1ZSBhcyBzdHJpbmcgKi9cblxuICAvKiogVGFpbCBzdGFydCBwb3NpdGlvbiAqL1xuXG4gIC8qKiBTdGFydCBwb3NpdGlvbiAqL1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIGxldCB2YWx1ZSA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDogJyc7XG4gICAgbGV0IGZyb20gPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IDA7XG4gICAgbGV0IHN0b3AgPSBhcmd1bWVudHMubGVuZ3RoID4gMiA/IGFyZ3VtZW50c1syXSA6IHVuZGVmaW5lZDtcbiAgICB0aGlzLnZhbHVlID0gdmFsdWU7XG4gICAgdGhpcy5mcm9tID0gZnJvbTtcbiAgICB0aGlzLnN0b3AgPSBzdG9wO1xuICB9XG4gIHRvU3RyaW5nKCkge1xuICAgIHJldHVybiB0aGlzLnZhbHVlO1xuICB9XG4gIGV4dGVuZCh0YWlsKSB7XG4gICAgdGhpcy52YWx1ZSArPSBTdHJpbmcodGFpbCk7XG4gIH1cbiAgYXBwZW5kVG8obWFza2VkKSB7XG4gICAgcmV0dXJuIG1hc2tlZC5hcHBlbmQodGhpcy50b1N0cmluZygpLCB7XG4gICAgICB0YWlsOiB0cnVlXG4gICAgfSkuYWdncmVnYXRlKG1hc2tlZC5fYXBwZW5kUGxhY2Vob2xkZXIoKSk7XG4gIH1cbiAgZ2V0IHN0YXRlKCkge1xuICAgIHJldHVybiB7XG4gICAgICB2YWx1ZTogdGhpcy52YWx1ZSxcbiAgICAgIGZyb206IHRoaXMuZnJvbSxcbiAgICAgIHN0b3A6IHRoaXMuc3RvcFxuICAgIH07XG4gIH1cbiAgc2V0IHN0YXRlKHN0YXRlKSB7XG4gICAgT2JqZWN0LmFzc2lnbih0aGlzLCBzdGF0ZSk7XG4gIH1cbiAgdW5zaGlmdChiZWZvcmVQb3MpIHtcbiAgICBpZiAoIXRoaXMudmFsdWUubGVuZ3RoIHx8IGJlZm9yZVBvcyAhPSBudWxsICYmIHRoaXMuZnJvbSA+PSBiZWZvcmVQb3MpIHJldHVybiAnJztcbiAgICBjb25zdCBzaGlmdENoYXIgPSB0aGlzLnZhbHVlWzBdO1xuICAgIHRoaXMudmFsdWUgPSB0aGlzLnZhbHVlLnNsaWNlKDEpO1xuICAgIHJldHVybiBzaGlmdENoYXI7XG4gIH1cbiAgc2hpZnQoKSB7XG4gICAgaWYgKCF0aGlzLnZhbHVlLmxlbmd0aCkgcmV0dXJuICcnO1xuICAgIGNvbnN0IHNoaWZ0Q2hhciA9IHRoaXMudmFsdWVbdGhpcy52YWx1ZS5sZW5ndGggLSAxXTtcbiAgICB0aGlzLnZhbHVlID0gdGhpcy52YWx1ZS5zbGljZSgwLCAtMSk7XG4gICAgcmV0dXJuIHNoaWZ0Q2hhcjtcbiAgfVxufVxuXG5leHBvcnQgeyBDb250aW51b3VzVGFpbERldGFpbHMgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBDaGFuZ2VEZXRhaWxzIGZyb20gJy4uL2NvcmUvY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0IENvbnRpbnVvdXNUYWlsRGV0YWlscyBmcm9tICcuLi9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzJztcbmltcG9ydCB7IGlzU3RyaW5nLCBub3JtYWxpemVQcmVwYXJlLCBESVJFQ1RJT04sIGZvcmNlRGlyZWN0aW9uIH0gZnJvbSAnLi4vY29yZS91dGlscy5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuXG4vKiogU3VwcG9ydGVkIG1hc2sgdHlwZSAqL1xuXG4vKiogQXBwZW5kIGZsYWdzICovXG5cbi8qKiBFeHRyYWN0IGZsYWdzICovXG5cbi8qKiBQcm92aWRlcyBjb21tb24gbWFza2luZyBzdHVmZiAqL1xuY2xhc3MgTWFza2VkIHtcbiAgLy8gJFNoYXBlPE1hc2tlZE9wdGlvbnM+OyBUT0RPIGFmdGVyIGZpeCBodHRwczovL2dpdGh1Yi5jb20vZmFjZWJvb2svZmxvdy9pc3N1ZXMvNDc3M1xuXG4gIC8qKiBAdHlwZSB7TWFza30gKi9cblxuICAvKiogKi8gLy8gJEZsb3dGaXhNZSBubyBpZGVhc1xuICAvKiogVHJhbnNmb3JtcyB2YWx1ZSBiZWZvcmUgbWFzayBwcm9jZXNzaW5nICovXG4gIC8qKiBWYWxpZGF0ZXMgaWYgdmFsdWUgaXMgYWNjZXB0YWJsZSAqL1xuICAvKiogRG9lcyBhZGRpdGlvbmFsIHByb2Nlc3NpbmcgaW4gdGhlIGVuZCBvZiBlZGl0aW5nICovXG4gIC8qKiBGb3JtYXQgdHlwZWQgdmFsdWUgdG8gc3RyaW5nICovXG4gIC8qKiBQYXJzZSBzdHJnaW4gdG8gZ2V0IHR5cGVkIHZhbHVlICovXG4gIC8qKiBFbmFibGUgY2hhcmFjdGVycyBvdmVyd3JpdGluZyAqL1xuICAvKiogKi9cbiAgLyoqICovXG4gIC8qKiAqL1xuICBjb25zdHJ1Y3RvcihvcHRzKSB7XG4gICAgdGhpcy5fdmFsdWUgPSAnJztcbiAgICB0aGlzLl91cGRhdGUoT2JqZWN0LmFzc2lnbih7fSwgTWFza2VkLkRFRkFVTFRTLCBvcHRzKSk7XG4gICAgdGhpcy5pc0luaXRpYWxpemVkID0gdHJ1ZTtcbiAgfVxuXG4gIC8qKiBTZXRzIGFuZCBhcHBsaWVzIG5ldyBvcHRpb25zICovXG4gIHVwZGF0ZU9wdGlvbnMob3B0cykge1xuICAgIGlmICghT2JqZWN0LmtleXMob3B0cykubGVuZ3RoKSByZXR1cm47XG4gICAgLy8gJEZsb3dGaXhNZVxuICAgIHRoaXMud2l0aFZhbHVlUmVmcmVzaCh0aGlzLl91cGRhdGUuYmluZCh0aGlzLCBvcHRzKSk7XG4gIH1cblxuICAvKipcbiAgICBTZXRzIG5ldyBvcHRpb25zXG4gICAgQHByb3RlY3RlZFxuICAqL1xuICBfdXBkYXRlKG9wdHMpIHtcbiAgICBPYmplY3QuYXNzaWduKHRoaXMsIG9wdHMpO1xuICB9XG5cbiAgLyoqIE1hc2sgc3RhdGUgKi9cbiAgZ2V0IHN0YXRlKCkge1xuICAgIHJldHVybiB7XG4gICAgICBfdmFsdWU6IHRoaXMudmFsdWVcbiAgICB9O1xuICB9XG4gIHNldCBzdGF0ZShzdGF0ZSkge1xuICAgIHRoaXMuX3ZhbHVlID0gc3RhdGUuX3ZhbHVlO1xuICB9XG5cbiAgLyoqIFJlc2V0cyB2YWx1ZSAqL1xuICByZXNldCgpIHtcbiAgICB0aGlzLl92YWx1ZSA9ICcnO1xuICB9XG5cbiAgLyoqICovXG4gIGdldCB2YWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5fdmFsdWU7XG4gIH1cbiAgc2V0IHZhbHVlKHZhbHVlKSB7XG4gICAgdGhpcy5yZXNvbHZlKHZhbHVlKTtcbiAgfVxuXG4gIC8qKiBSZXNvbHZlIG5ldyB2YWx1ZSAqL1xuICByZXNvbHZlKHZhbHVlKSB7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB7XG4gICAgICBpbnB1dDogdHJ1ZVxuICAgIH07XG4gICAgdGhpcy5yZXNldCgpO1xuICAgIHRoaXMuYXBwZW5kKHZhbHVlLCBmbGFncywgJycpO1xuICAgIHRoaXMuZG9Db21taXQoKTtcbiAgICByZXR1cm4gdGhpcy52YWx1ZTtcbiAgfVxuXG4gIC8qKiAqL1xuICBnZXQgdW5tYXNrZWRWYWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy52YWx1ZTtcbiAgfVxuICBzZXQgdW5tYXNrZWRWYWx1ZSh2YWx1ZSkge1xuICAgIHRoaXMucmVzZXQoKTtcbiAgICB0aGlzLmFwcGVuZCh2YWx1ZSwge30sICcnKTtcbiAgICB0aGlzLmRvQ29tbWl0KCk7XG4gIH1cblxuICAvKiogKi9cbiAgZ2V0IHR5cGVkVmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMuZG9QYXJzZSh0aGlzLnZhbHVlKTtcbiAgfVxuICBzZXQgdHlwZWRWYWx1ZSh2YWx1ZSkge1xuICAgIHRoaXMudmFsdWUgPSB0aGlzLmRvRm9ybWF0KHZhbHVlKTtcbiAgfVxuXG4gIC8qKiBWYWx1ZSB0aGF0IGluY2x1ZGVzIHJhdyB1c2VyIGlucHV0ICovXG4gIGdldCByYXdJbnB1dFZhbHVlKCkge1xuICAgIHJldHVybiB0aGlzLmV4dHJhY3RJbnB1dCgwLCB0aGlzLnZhbHVlLmxlbmd0aCwge1xuICAgICAgcmF3OiB0cnVlXG4gICAgfSk7XG4gIH1cbiAgc2V0IHJhd0lucHV0VmFsdWUodmFsdWUpIHtcbiAgICB0aGlzLnJlc2V0KCk7XG4gICAgdGhpcy5hcHBlbmQodmFsdWUsIHtcbiAgICAgIHJhdzogdHJ1ZVxuICAgIH0sICcnKTtcbiAgICB0aGlzLmRvQ29tbWl0KCk7XG4gIH1cbiAgZ2V0IGRpc3BsYXlWYWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy52YWx1ZTtcbiAgfVxuXG4gIC8qKiAqL1xuICBnZXQgaXNDb21wbGV0ZSgpIHtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxuXG4gIC8qKiAqL1xuICBnZXQgaXNGaWxsZWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuaXNDb21wbGV0ZTtcbiAgfVxuXG4gIC8qKiBGaW5kcyBuZWFyZXN0IGlucHV0IHBvc2l0aW9uIGluIGRpcmVjdGlvbiAqL1xuICBuZWFyZXN0SW5wdXRQb3MoY3Vyc29yUG9zLCBkaXJlY3Rpb24pIHtcbiAgICByZXR1cm4gY3Vyc29yUG9zO1xuICB9XG4gIHRvdGFsSW5wdXRQb3NpdGlvbnMoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICByZXR1cm4gTWF0aC5taW4odGhpcy52YWx1ZS5sZW5ndGgsIHRvUG9zIC0gZnJvbVBvcyk7XG4gIH1cblxuICAvKiogRXh0cmFjdHMgdmFsdWUgaW4gcmFuZ2UgY29uc2lkZXJpbmcgZmxhZ3MgKi9cbiAgZXh0cmFjdElucHV0KCkge1xuICAgIGxldCBmcm9tUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDAgJiYgYXJndW1lbnRzWzBdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMF0gOiAwO1xuICAgIGxldCB0b1BvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogdGhpcy52YWx1ZS5sZW5ndGg7XG4gICAgcmV0dXJuIHRoaXMudmFsdWUuc2xpY2UoZnJvbVBvcywgdG9Qb3MpO1xuICB9XG5cbiAgLyoqIEV4dHJhY3RzIHRhaWwgaW4gcmFuZ2UgKi9cbiAgZXh0cmFjdFRhaWwoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICByZXR1cm4gbmV3IENvbnRpbnVvdXNUYWlsRGV0YWlscyh0aGlzLmV4dHJhY3RJbnB1dChmcm9tUG9zLCB0b1BvcyksIGZyb21Qb3MpO1xuICB9XG5cbiAgLyoqIEFwcGVuZHMgdGFpbCAqL1xuICAvLyAkRmxvd0ZpeE1lIG5vIGlkZWFzXG4gIGFwcGVuZFRhaWwodGFpbCkge1xuICAgIGlmIChpc1N0cmluZyh0YWlsKSkgdGFpbCA9IG5ldyBDb250aW51b3VzVGFpbERldGFpbHMoU3RyaW5nKHRhaWwpKTtcbiAgICByZXR1cm4gdGFpbC5hcHBlbmRUbyh0aGlzKTtcbiAgfVxuXG4gIC8qKiBBcHBlbmRzIGNoYXIgKi9cbiAgX2FwcGVuZENoYXJSYXcoY2gpIHtcbiAgICBpZiAoIWNoKSByZXR1cm4gbmV3IENoYW5nZURldGFpbHMoKTtcbiAgICB0aGlzLl92YWx1ZSArPSBjaDtcbiAgICByZXR1cm4gbmV3IENoYW5nZURldGFpbHMoe1xuICAgICAgaW5zZXJ0ZWQ6IGNoLFxuICAgICAgcmF3SW5zZXJ0ZWQ6IGNoXG4gICAgfSk7XG4gIH1cblxuICAvKiogQXBwZW5kcyBjaGFyICovXG4gIF9hcHBlbmRDaGFyKGNoKSB7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB7fTtcbiAgICBsZXQgY2hlY2tUYWlsID0gYXJndW1lbnRzLmxlbmd0aCA+IDIgPyBhcmd1bWVudHNbMl0gOiB1bmRlZmluZWQ7XG4gICAgY29uc3QgY29uc2lzdGVudFN0YXRlID0gdGhpcy5zdGF0ZTtcbiAgICBsZXQgZGV0YWlscztcbiAgICBbY2gsIGRldGFpbHNdID0gbm9ybWFsaXplUHJlcGFyZSh0aGlzLmRvUHJlcGFyZShjaCwgZmxhZ3MpKTtcbiAgICBkZXRhaWxzID0gZGV0YWlscy5hZ2dyZWdhdGUodGhpcy5fYXBwZW5kQ2hhclJhdyhjaCwgZmxhZ3MpKTtcbiAgICBpZiAoZGV0YWlscy5pbnNlcnRlZCkge1xuICAgICAgbGV0IGNvbnNpc3RlbnRUYWlsO1xuICAgICAgbGV0IGFwcGVuZGVkID0gdGhpcy5kb1ZhbGlkYXRlKGZsYWdzKSAhPT0gZmFsc2U7XG4gICAgICBpZiAoYXBwZW5kZWQgJiYgY2hlY2tUYWlsICE9IG51bGwpIHtcbiAgICAgICAgLy8gdmFsaWRhdGlvbiBvaywgY2hlY2sgdGFpbFxuICAgICAgICBjb25zdCBiZWZvcmVUYWlsU3RhdGUgPSB0aGlzLnN0YXRlO1xuICAgICAgICBpZiAodGhpcy5vdmVyd3JpdGUgPT09IHRydWUpIHtcbiAgICAgICAgICBjb25zaXN0ZW50VGFpbCA9IGNoZWNrVGFpbC5zdGF0ZTtcbiAgICAgICAgICBjaGVja1RhaWwudW5zaGlmdCh0aGlzLnZhbHVlLmxlbmd0aCAtIGRldGFpbHMudGFpbFNoaWZ0KTtcbiAgICAgICAgfVxuICAgICAgICBsZXQgdGFpbERldGFpbHMgPSB0aGlzLmFwcGVuZFRhaWwoY2hlY2tUYWlsKTtcbiAgICAgICAgYXBwZW5kZWQgPSB0YWlsRGV0YWlscy5yYXdJbnNlcnRlZCA9PT0gY2hlY2tUYWlsLnRvU3RyaW5nKCk7XG5cbiAgICAgICAgLy8gbm90IG9rLCB0cnkgc2hpZnRcbiAgICAgICAgaWYgKCEoYXBwZW5kZWQgJiYgdGFpbERldGFpbHMuaW5zZXJ0ZWQpICYmIHRoaXMub3ZlcndyaXRlID09PSAnc2hpZnQnKSB7XG4gICAgICAgICAgdGhpcy5zdGF0ZSA9IGJlZm9yZVRhaWxTdGF0ZTtcbiAgICAgICAgICBjb25zaXN0ZW50VGFpbCA9IGNoZWNrVGFpbC5zdGF0ZTtcbiAgICAgICAgICBjaGVja1RhaWwuc2hpZnQoKTtcbiAgICAgICAgICB0YWlsRGV0YWlscyA9IHRoaXMuYXBwZW5kVGFpbChjaGVja1RhaWwpO1xuICAgICAgICAgIGFwcGVuZGVkID0gdGFpbERldGFpbHMucmF3SW5zZXJ0ZWQgPT09IGNoZWNrVGFpbC50b1N0cmluZygpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gaWYgb2ssIHJvbGxiYWNrIHN0YXRlIGFmdGVyIHRhaWxcbiAgICAgICAgaWYgKGFwcGVuZGVkICYmIHRhaWxEZXRhaWxzLmluc2VydGVkKSB0aGlzLnN0YXRlID0gYmVmb3JlVGFpbFN0YXRlO1xuICAgICAgfVxuXG4gICAgICAvLyByZXZlcnQgYWxsIGlmIHNvbWV0aGluZyB3ZW50IHdyb25nXG4gICAgICBpZiAoIWFwcGVuZGVkKSB7XG4gICAgICAgIGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgICAgICB0aGlzLnN0YXRlID0gY29uc2lzdGVudFN0YXRlO1xuICAgICAgICBpZiAoY2hlY2tUYWlsICYmIGNvbnNpc3RlbnRUYWlsKSBjaGVja1RhaWwuc3RhdGUgPSBjb25zaXN0ZW50VGFpbDtcbiAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIGRldGFpbHM7XG4gIH1cblxuICAvKiogQXBwZW5kcyBvcHRpb25hbCBwbGFjZWhvbGRlciBhdCBlbmQgKi9cbiAgX2FwcGVuZFBsYWNlaG9sZGVyKCkge1xuICAgIHJldHVybiBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICB9XG5cbiAgLyoqIEFwcGVuZHMgb3B0aW9uYWwgZWFnZXIgcGxhY2Vob2xkZXIgYXQgZW5kICovXG4gIF9hcHBlbmRFYWdlcigpIHtcbiAgICByZXR1cm4gbmV3IENoYW5nZURldGFpbHMoKTtcbiAgfVxuXG4gIC8qKiBBcHBlbmRzIHN5bWJvbHMgY29uc2lkZXJpbmcgZmxhZ3MgKi9cbiAgLy8gJEZsb3dGaXhNZSBubyBpZGVhc1xuICBhcHBlbmQoc3RyLCBmbGFncywgdGFpbCkge1xuICAgIGlmICghaXNTdHJpbmcoc3RyKSkgdGhyb3cgbmV3IEVycm9yKCd2YWx1ZSBzaG91bGQgYmUgc3RyaW5nJyk7XG4gICAgY29uc3QgZGV0YWlscyA9IG5ldyBDaGFuZ2VEZXRhaWxzKCk7XG4gICAgY29uc3QgY2hlY2tUYWlsID0gaXNTdHJpbmcodGFpbCkgPyBuZXcgQ29udGludW91c1RhaWxEZXRhaWxzKFN0cmluZyh0YWlsKSkgOiB0YWlsO1xuICAgIGlmIChmbGFncyAhPT0gbnVsbCAmJiBmbGFncyAhPT0gdm9pZCAwICYmIGZsYWdzLnRhaWwpIGZsYWdzLl9iZWZvcmVUYWlsU3RhdGUgPSB0aGlzLnN0YXRlO1xuICAgIGZvciAobGV0IGNpID0gMDsgY2kgPCBzdHIubGVuZ3RoOyArK2NpKSB7XG4gICAgICBjb25zdCBkID0gdGhpcy5fYXBwZW5kQ2hhcihzdHJbY2ldLCBmbGFncywgY2hlY2tUYWlsKTtcbiAgICAgIGlmICghZC5yYXdJbnNlcnRlZCAmJiAhdGhpcy5kb1NraXBJbnZhbGlkKHN0cltjaV0sIGZsYWdzLCBjaGVja1RhaWwpKSBicmVhaztcbiAgICAgIGRldGFpbHMuYWdncmVnYXRlKGQpO1xuICAgIH1cbiAgICBpZiAoKHRoaXMuZWFnZXIgPT09IHRydWUgfHwgdGhpcy5lYWdlciA9PT0gJ2FwcGVuZCcpICYmIGZsYWdzICE9PSBudWxsICYmIGZsYWdzICE9PSB2b2lkIDAgJiYgZmxhZ3MuaW5wdXQgJiYgc3RyKSB7XG4gICAgICBkZXRhaWxzLmFnZ3JlZ2F0ZSh0aGlzLl9hcHBlbmRFYWdlcigpKTtcbiAgICB9XG5cbiAgICAvLyBhcHBlbmQgdGFpbCBidXQgYWdncmVnYXRlIG9ubHkgdGFpbFNoaWZ0XG4gICAgaWYgKGNoZWNrVGFpbCAhPSBudWxsKSB7XG4gICAgICBkZXRhaWxzLnRhaWxTaGlmdCArPSB0aGlzLmFwcGVuZFRhaWwoY2hlY2tUYWlsKS50YWlsU2hpZnQ7XG4gICAgICAvLyBUT0RPIGl0J3MgYSBnb29kIGlkZWEgdG8gY2xlYXIgc3RhdGUgYWZ0ZXIgYXBwZW5kaW5nIGVuZHNcbiAgICAgIC8vIGJ1dCBpdCBjYXVzZXMgYnVncyB3aGVuIG9uZSBhcHBlbmQgY2FsbHMgYW5vdGhlciAod2hlbiBkeW5hbWljIGRpc3BhdGNoIHNldCByYXdJbnB1dFZhbHVlKVxuICAgICAgLy8gdGhpcy5fcmVzZXRCZWZvcmVUYWlsU3RhdGUoKTtcbiAgICB9XG5cbiAgICByZXR1cm4gZGV0YWlscztcbiAgfVxuXG4gIC8qKiAqL1xuICByZW1vdmUoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICB0aGlzLl92YWx1ZSA9IHRoaXMudmFsdWUuc2xpY2UoMCwgZnJvbVBvcykgKyB0aGlzLnZhbHVlLnNsaWNlKHRvUG9zKTtcbiAgICByZXR1cm4gbmV3IENoYW5nZURldGFpbHMoKTtcbiAgfVxuXG4gIC8qKiBDYWxscyBmdW5jdGlvbiBhbmQgcmVhcHBsaWVzIGN1cnJlbnQgdmFsdWUgKi9cbiAgd2l0aFZhbHVlUmVmcmVzaChmbikge1xuICAgIGlmICh0aGlzLl9yZWZyZXNoaW5nIHx8ICF0aGlzLmlzSW5pdGlhbGl6ZWQpIHJldHVybiBmbigpO1xuICAgIHRoaXMuX3JlZnJlc2hpbmcgPSB0cnVlO1xuICAgIGNvbnN0IHJhd0lucHV0ID0gdGhpcy5yYXdJbnB1dFZhbHVlO1xuICAgIGNvbnN0IHZhbHVlID0gdGhpcy52YWx1ZTtcbiAgICBjb25zdCByZXQgPSBmbigpO1xuICAgIHRoaXMucmF3SW5wdXRWYWx1ZSA9IHJhd0lucHV0O1xuICAgIC8vIGFwcGVuZCBsb3N0IHRyYWlsaW5nIGNoYXJzIGF0IGVuZFxuICAgIGlmICh0aGlzLnZhbHVlICYmIHRoaXMudmFsdWUgIT09IHZhbHVlICYmIHZhbHVlLmluZGV4T2YodGhpcy52YWx1ZSkgPT09IDApIHtcbiAgICAgIHRoaXMuYXBwZW5kKHZhbHVlLnNsaWNlKHRoaXMudmFsdWUubGVuZ3RoKSwge30sICcnKTtcbiAgICB9XG4gICAgZGVsZXRlIHRoaXMuX3JlZnJlc2hpbmc7XG4gICAgcmV0dXJuIHJldDtcbiAgfVxuXG4gIC8qKiAqL1xuICBydW5Jc29sYXRlZChmbikge1xuICAgIGlmICh0aGlzLl9pc29sYXRlZCB8fCAhdGhpcy5pc0luaXRpYWxpemVkKSByZXR1cm4gZm4odGhpcyk7XG4gICAgdGhpcy5faXNvbGF0ZWQgPSB0cnVlO1xuICAgIGNvbnN0IHN0YXRlID0gdGhpcy5zdGF0ZTtcbiAgICBjb25zdCByZXQgPSBmbih0aGlzKTtcbiAgICB0aGlzLnN0YXRlID0gc3RhdGU7XG4gICAgZGVsZXRlIHRoaXMuX2lzb2xhdGVkO1xuICAgIHJldHVybiByZXQ7XG4gIH1cblxuICAvKiogKi9cbiAgZG9Ta2lwSW52YWxpZChjaCkge1xuICAgIHJldHVybiB0aGlzLnNraXBJbnZhbGlkO1xuICB9XG5cbiAgLyoqXG4gICAgUHJlcGFyZXMgc3RyaW5nIGJlZm9yZSBtYXNrIHByb2Nlc3NpbmdcbiAgICBAcHJvdGVjdGVkXG4gICovXG4gIGRvUHJlcGFyZShzdHIpIHtcbiAgICBsZXQgZmxhZ3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHt9O1xuICAgIHJldHVybiB0aGlzLnByZXBhcmUgPyB0aGlzLnByZXBhcmUoc3RyLCB0aGlzLCBmbGFncykgOiBzdHI7XG4gIH1cblxuICAvKipcbiAgICBWYWxpZGF0ZXMgaWYgdmFsdWUgaXMgYWNjZXB0YWJsZVxuICAgIEBwcm90ZWN0ZWRcbiAgKi9cbiAgZG9WYWxpZGF0ZShmbGFncykge1xuICAgIHJldHVybiAoIXRoaXMudmFsaWRhdGUgfHwgdGhpcy52YWxpZGF0ZSh0aGlzLnZhbHVlLCB0aGlzLCBmbGFncykpICYmICghdGhpcy5wYXJlbnQgfHwgdGhpcy5wYXJlbnQuZG9WYWxpZGF0ZShmbGFncykpO1xuICB9XG5cbiAgLyoqXG4gICAgRG9lcyBhZGRpdGlvbmFsIHByb2Nlc3NpbmcgaW4gdGhlIGVuZCBvZiBlZGl0aW5nXG4gICAgQHByb3RlY3RlZFxuICAqL1xuICBkb0NvbW1pdCgpIHtcbiAgICBpZiAodGhpcy5jb21taXQpIHRoaXMuY29tbWl0KHRoaXMudmFsdWUsIHRoaXMpO1xuICB9XG5cbiAgLyoqICovXG4gIGRvRm9ybWF0KHZhbHVlKSB7XG4gICAgcmV0dXJuIHRoaXMuZm9ybWF0ID8gdGhpcy5mb3JtYXQodmFsdWUsIHRoaXMpIDogdmFsdWU7XG4gIH1cblxuICAvKiogKi9cbiAgZG9QYXJzZShzdHIpIHtcbiAgICByZXR1cm4gdGhpcy5wYXJzZSA/IHRoaXMucGFyc2Uoc3RyLCB0aGlzKSA6IHN0cjtcbiAgfVxuXG4gIC8qKiAqL1xuICBzcGxpY2Uoc3RhcnQsIGRlbGV0ZUNvdW50LCBpbnNlcnRlZCwgcmVtb3ZlRGlyZWN0aW9uKSB7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDQgJiYgYXJndW1lbnRzWzRdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbNF0gOiB7XG4gICAgICBpbnB1dDogdHJ1ZVxuICAgIH07XG4gICAgY29uc3QgdGFpbFBvcyA9IHN0YXJ0ICsgZGVsZXRlQ291bnQ7XG4gICAgY29uc3QgdGFpbCA9IHRoaXMuZXh0cmFjdFRhaWwodGFpbFBvcyk7XG4gICAgY29uc3QgZWFnZXJSZW1vdmUgPSB0aGlzLmVhZ2VyID09PSB0cnVlIHx8IHRoaXMuZWFnZXIgPT09ICdyZW1vdmUnO1xuICAgIGxldCBvbGRSYXdWYWx1ZTtcbiAgICBpZiAoZWFnZXJSZW1vdmUpIHtcbiAgICAgIHJlbW92ZURpcmVjdGlvbiA9IGZvcmNlRGlyZWN0aW9uKHJlbW92ZURpcmVjdGlvbik7XG4gICAgICBvbGRSYXdWYWx1ZSA9IHRoaXMuZXh0cmFjdElucHV0KDAsIHRhaWxQb3MsIHtcbiAgICAgICAgcmF3OiB0cnVlXG4gICAgICB9KTtcbiAgICB9XG4gICAgbGV0IHN0YXJ0Q2hhbmdlUG9zID0gc3RhcnQ7XG4gICAgY29uc3QgZGV0YWlscyA9IG5ldyBDaGFuZ2VEZXRhaWxzKCk7XG5cbiAgICAvLyBpZiBpdCBpcyBqdXN0IGRlbGV0aW9uIHdpdGhvdXQgaW5zZXJ0aW9uXG4gICAgaWYgKHJlbW92ZURpcmVjdGlvbiAhPT0gRElSRUNUSU9OLk5PTkUpIHtcbiAgICAgIHN0YXJ0Q2hhbmdlUG9zID0gdGhpcy5uZWFyZXN0SW5wdXRQb3Moc3RhcnQsIGRlbGV0ZUNvdW50ID4gMSAmJiBzdGFydCAhPT0gMCAmJiAhZWFnZXJSZW1vdmUgPyBESVJFQ1RJT04uTk9ORSA6IHJlbW92ZURpcmVjdGlvbik7XG5cbiAgICAgIC8vIGFkanVzdCB0YWlsU2hpZnQgaWYgc3RhcnQgd2FzIGFsaWduZWRcbiAgICAgIGRldGFpbHMudGFpbFNoaWZ0ID0gc3RhcnRDaGFuZ2VQb3MgLSBzdGFydDtcbiAgICB9XG4gICAgZGV0YWlscy5hZ2dyZWdhdGUodGhpcy5yZW1vdmUoc3RhcnRDaGFuZ2VQb3MpKTtcbiAgICBpZiAoZWFnZXJSZW1vdmUgJiYgcmVtb3ZlRGlyZWN0aW9uICE9PSBESVJFQ1RJT04uTk9ORSAmJiBvbGRSYXdWYWx1ZSA9PT0gdGhpcy5yYXdJbnB1dFZhbHVlKSB7XG4gICAgICBpZiAocmVtb3ZlRGlyZWN0aW9uID09PSBESVJFQ1RJT04uRk9SQ0VfTEVGVCkge1xuICAgICAgICBsZXQgdmFsTGVuZ3RoO1xuICAgICAgICB3aGlsZSAob2xkUmF3VmFsdWUgPT09IHRoaXMucmF3SW5wdXRWYWx1ZSAmJiAodmFsTGVuZ3RoID0gdGhpcy52YWx1ZS5sZW5ndGgpKSB7XG4gICAgICAgICAgZGV0YWlscy5hZ2dyZWdhdGUobmV3IENoYW5nZURldGFpbHMoe1xuICAgICAgICAgICAgdGFpbFNoaWZ0OiAtMVxuICAgICAgICAgIH0pKS5hZ2dyZWdhdGUodGhpcy5yZW1vdmUodmFsTGVuZ3RoIC0gMSkpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKHJlbW92ZURpcmVjdGlvbiA9PT0gRElSRUNUSU9OLkZPUkNFX1JJR0hUKSB7XG4gICAgICAgIHRhaWwudW5zaGlmdCgpO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gZGV0YWlscy5hZ2dyZWdhdGUodGhpcy5hcHBlbmQoaW5zZXJ0ZWQsIGZsYWdzLCB0YWlsKSk7XG4gIH1cbiAgbWFza0VxdWFscyhtYXNrKSB7XG4gICAgcmV0dXJuIHRoaXMubWFzayA9PT0gbWFzaztcbiAgfVxuICB0eXBlZFZhbHVlRXF1YWxzKHZhbHVlKSB7XG4gICAgY29uc3QgdHZhbCA9IHRoaXMudHlwZWRWYWx1ZTtcbiAgICByZXR1cm4gdmFsdWUgPT09IHR2YWwgfHwgTWFza2VkLkVNUFRZX1ZBTFVFUy5pbmNsdWRlcyh2YWx1ZSkgJiYgTWFza2VkLkVNUFRZX1ZBTFVFUy5pbmNsdWRlcyh0dmFsKSB8fCB0aGlzLmRvRm9ybWF0KHZhbHVlKSA9PT0gdGhpcy5kb0Zvcm1hdCh0aGlzLnR5cGVkVmFsdWUpO1xuICB9XG59XG5NYXNrZWQuREVGQVVMVFMgPSB7XG4gIGZvcm1hdDogU3RyaW5nLFxuICBwYXJzZTogdiA9PiB2LFxuICBza2lwSW52YWxpZDogdHJ1ZVxufTtcbk1hc2tlZC5FTVBUWV9WQUxVRVMgPSBbdW5kZWZpbmVkLCBudWxsLCAnJ107XG5JTWFzay5NYXNrZWQgPSBNYXNrZWQ7XG5cbmV4cG9ydCB7IE1hc2tlZCBhcyBkZWZhdWx0IH07XG4iLCAiaW1wb3J0IHsgaXNTdHJpbmcgfSBmcm9tICcuLi9jb3JlL3V0aWxzLmpzJztcbmltcG9ydCBJTWFzayBmcm9tICcuLi9jb3JlL2hvbGRlci5qcyc7XG5pbXBvcnQgJy4uL2NvcmUvY2hhbmdlLWRldGFpbHMuanMnO1xuXG4vKiogR2V0IE1hc2tlZCBjbGFzcyBieSBtYXNrIHR5cGUgKi9cbmZ1bmN0aW9uIG1hc2tlZENsYXNzKG1hc2spIHtcbiAgaWYgKG1hc2sgPT0gbnVsbCkge1xuICAgIHRocm93IG5ldyBFcnJvcignbWFzayBwcm9wZXJ0eSBzaG91bGQgYmUgZGVmaW5lZCcpO1xuICB9XG5cbiAgLy8gJEZsb3dGaXhNZVxuICBpZiAobWFzayBpbnN0YW5jZW9mIFJlZ0V4cCkgcmV0dXJuIElNYXNrLk1hc2tlZFJlZ0V4cDtcbiAgLy8gJEZsb3dGaXhNZVxuICBpZiAoaXNTdHJpbmcobWFzaykpIHJldHVybiBJTWFzay5NYXNrZWRQYXR0ZXJuO1xuICAvLyAkRmxvd0ZpeE1lXG4gIGlmIChtYXNrIGluc3RhbmNlb2YgRGF0ZSB8fCBtYXNrID09PSBEYXRlKSByZXR1cm4gSU1hc2suTWFza2VkRGF0ZTtcbiAgLy8gJEZsb3dGaXhNZVxuICBpZiAobWFzayBpbnN0YW5jZW9mIE51bWJlciB8fCB0eXBlb2YgbWFzayA9PT0gJ251bWJlcicgfHwgbWFzayA9PT0gTnVtYmVyKSByZXR1cm4gSU1hc2suTWFza2VkTnVtYmVyO1xuICAvLyAkRmxvd0ZpeE1lXG4gIGlmIChBcnJheS5pc0FycmF5KG1hc2spIHx8IG1hc2sgPT09IEFycmF5KSByZXR1cm4gSU1hc2suTWFza2VkRHluYW1pYztcbiAgLy8gJEZsb3dGaXhNZVxuICBpZiAoSU1hc2suTWFza2VkICYmIG1hc2sucHJvdG90eXBlIGluc3RhbmNlb2YgSU1hc2suTWFza2VkKSByZXR1cm4gbWFzaztcbiAgLy8gJEZsb3dGaXhNZVxuICBpZiAobWFzayBpbnN0YW5jZW9mIElNYXNrLk1hc2tlZCkgcmV0dXJuIG1hc2suY29uc3RydWN0b3I7XG4gIC8vICRGbG93Rml4TWVcbiAgaWYgKG1hc2sgaW5zdGFuY2VvZiBGdW5jdGlvbikgcmV0dXJuIElNYXNrLk1hc2tlZEZ1bmN0aW9uO1xuICBjb25zb2xlLndhcm4oJ01hc2sgbm90IGZvdW5kIGZvciBtYXNrJywgbWFzayk7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tY29uc29sZVxuICAvLyAkRmxvd0ZpeE1lXG4gIHJldHVybiBJTWFzay5NYXNrZWQ7XG59XG5cbi8qKiBDcmVhdGVzIG5ldyB7QGxpbmsgTWFza2VkfSBkZXBlbmRpbmcgb24gbWFzayB0eXBlICovXG5mdW5jdGlvbiBjcmVhdGVNYXNrKG9wdHMpIHtcbiAgLy8gJEZsb3dGaXhNZVxuICBpZiAoSU1hc2suTWFza2VkICYmIG9wdHMgaW5zdGFuY2VvZiBJTWFzay5NYXNrZWQpIHJldHVybiBvcHRzO1xuICBvcHRzID0gT2JqZWN0LmFzc2lnbih7fSwgb3B0cyk7XG4gIGNvbnN0IG1hc2sgPSBvcHRzLm1hc2s7XG5cbiAgLy8gJEZsb3dGaXhNZVxuICBpZiAoSU1hc2suTWFza2VkICYmIG1hc2sgaW5zdGFuY2VvZiBJTWFzay5NYXNrZWQpIHJldHVybiBtYXNrO1xuICBjb25zdCBNYXNrZWRDbGFzcyA9IG1hc2tlZENsYXNzKG1hc2spO1xuICBpZiAoIU1hc2tlZENsYXNzKSB0aHJvdyBuZXcgRXJyb3IoJ01hc2tlZCBjbGFzcyBpcyBub3QgZm91bmQgZm9yIHByb3ZpZGVkIG1hc2ssIGFwcHJvcHJpYXRlIG1vZHVsZSBuZWVkcyB0byBiZSBpbXBvcnQgbWFudWFsbHkgYmVmb3JlIGNyZWF0aW5nIG1hc2suJyk7XG4gIHJldHVybiBuZXcgTWFza2VkQ2xhc3Mob3B0cyk7XG59XG5JTWFzay5jcmVhdGVNYXNrID0gY3JlYXRlTWFzaztcblxuZXhwb3J0IHsgY3JlYXRlTWFzayBhcyBkZWZhdWx0LCBtYXNrZWRDbGFzcyB9O1xuIiwgImltcG9ydCB7IF8gYXMgX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2UgfSBmcm9tICcuLi8uLi9fcm9sbHVwUGx1Z2luQmFiZWxIZWxwZXJzLTZiM2JkNDA0LmpzJztcbmltcG9ydCBjcmVhdGVNYXNrIGZyb20gJy4uL2ZhY3RvcnkuanMnO1xuaW1wb3J0IENoYW5nZURldGFpbHMgZnJvbSAnLi4vLi4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5pbXBvcnQgeyBESVJFQ1RJT04gfSBmcm9tICcuLi8uLi9jb3JlL3V0aWxzLmpzJztcbmltcG9ydCAnLi4vLi4vY29yZS9ob2xkZXIuanMnO1xuXG5jb25zdCBfZXhjbHVkZWQgPSBbXCJwYXJlbnRcIiwgXCJpc09wdGlvbmFsXCIsIFwicGxhY2Vob2xkZXJDaGFyXCIsIFwiZGlzcGxheUNoYXJcIiwgXCJsYXp5XCIsIFwiZWFnZXJcIl07XG5cbi8qKiAqL1xuXG5jb25zdCBERUZBVUxUX0lOUFVUX0RFRklOSVRJT05TID0ge1xuICAnMCc6IC9cXGQvLFxuICAnYSc6IC9bXFx1MDA0MS1cXHUwMDVBXFx1MDA2MS1cXHUwMDdBXFx1MDBBQVxcdTAwQjVcXHUwMEJBXFx1MDBDMC1cXHUwMEQ2XFx1MDBEOC1cXHUwMEY2XFx1MDBGOC1cXHUwMkMxXFx1MDJDNi1cXHUwMkQxXFx1MDJFMC1cXHUwMkU0XFx1MDJFQ1xcdTAyRUVcXHUwMzcwLVxcdTAzNzRcXHUwMzc2XFx1MDM3N1xcdTAzN0EtXFx1MDM3RFxcdTAzODZcXHUwMzg4LVxcdTAzOEFcXHUwMzhDXFx1MDM4RS1cXHUwM0ExXFx1MDNBMy1cXHUwM0Y1XFx1MDNGNy1cXHUwNDgxXFx1MDQ4QS1cXHUwNTI3XFx1MDUzMS1cXHUwNTU2XFx1MDU1OVxcdTA1NjEtXFx1MDU4N1xcdTA1RDAtXFx1MDVFQVxcdTA1RjAtXFx1MDVGMlxcdTA2MjAtXFx1MDY0QVxcdTA2NkVcXHUwNjZGXFx1MDY3MS1cXHUwNkQzXFx1MDZENVxcdTA2RTVcXHUwNkU2XFx1MDZFRVxcdTA2RUZcXHUwNkZBLVxcdTA2RkNcXHUwNkZGXFx1MDcxMFxcdTA3MTItXFx1MDcyRlxcdTA3NEQtXFx1MDdBNVxcdTA3QjFcXHUwN0NBLVxcdTA3RUFcXHUwN0Y0XFx1MDdGNVxcdTA3RkFcXHUwODAwLVxcdTA4MTVcXHUwODFBXFx1MDgyNFxcdTA4MjhcXHUwODQwLVxcdTA4NThcXHUwOEEwXFx1MDhBMi1cXHUwOEFDXFx1MDkwNC1cXHUwOTM5XFx1MDkzRFxcdTA5NTBcXHUwOTU4LVxcdTA5NjFcXHUwOTcxLVxcdTA5NzdcXHUwOTc5LVxcdTA5N0ZcXHUwOTg1LVxcdTA5OENcXHUwOThGXFx1MDk5MFxcdTA5OTMtXFx1MDlBOFxcdTA5QUEtXFx1MDlCMFxcdTA5QjJcXHUwOUI2LVxcdTA5QjlcXHUwOUJEXFx1MDlDRVxcdTA5RENcXHUwOUREXFx1MDlERi1cXHUwOUUxXFx1MDlGMFxcdTA5RjFcXHUwQTA1LVxcdTBBMEFcXHUwQTBGXFx1MEExMFxcdTBBMTMtXFx1MEEyOFxcdTBBMkEtXFx1MEEzMFxcdTBBMzJcXHUwQTMzXFx1MEEzNVxcdTBBMzZcXHUwQTM4XFx1MEEzOVxcdTBBNTktXFx1MEE1Q1xcdTBBNUVcXHUwQTcyLVxcdTBBNzRcXHUwQTg1LVxcdTBBOERcXHUwQThGLVxcdTBBOTFcXHUwQTkzLVxcdTBBQThcXHUwQUFBLVxcdTBBQjBcXHUwQUIyXFx1MEFCM1xcdTBBQjUtXFx1MEFCOVxcdTBBQkRcXHUwQUQwXFx1MEFFMFxcdTBBRTFcXHUwQjA1LVxcdTBCMENcXHUwQjBGXFx1MEIxMFxcdTBCMTMtXFx1MEIyOFxcdTBCMkEtXFx1MEIzMFxcdTBCMzJcXHUwQjMzXFx1MEIzNS1cXHUwQjM5XFx1MEIzRFxcdTBCNUNcXHUwQjVEXFx1MEI1Ri1cXHUwQjYxXFx1MEI3MVxcdTBCODNcXHUwQjg1LVxcdTBCOEFcXHUwQjhFLVxcdTBCOTBcXHUwQjkyLVxcdTBCOTVcXHUwQjk5XFx1MEI5QVxcdTBCOUNcXHUwQjlFXFx1MEI5RlxcdTBCQTNcXHUwQkE0XFx1MEJBOC1cXHUwQkFBXFx1MEJBRS1cXHUwQkI5XFx1MEJEMFxcdTBDMDUtXFx1MEMwQ1xcdTBDMEUtXFx1MEMxMFxcdTBDMTItXFx1MEMyOFxcdTBDMkEtXFx1MEMzM1xcdTBDMzUtXFx1MEMzOVxcdTBDM0RcXHUwQzU4XFx1MEM1OVxcdTBDNjBcXHUwQzYxXFx1MEM4NS1cXHUwQzhDXFx1MEM4RS1cXHUwQzkwXFx1MEM5Mi1cXHUwQ0E4XFx1MENBQS1cXHUwQ0IzXFx1MENCNS1cXHUwQ0I5XFx1MENCRFxcdTBDREVcXHUwQ0UwXFx1MENFMVxcdTBDRjFcXHUwQ0YyXFx1MEQwNS1cXHUwRDBDXFx1MEQwRS1cXHUwRDEwXFx1MEQxMi1cXHUwRDNBXFx1MEQzRFxcdTBENEVcXHUwRDYwXFx1MEQ2MVxcdTBEN0EtXFx1MEQ3RlxcdTBEODUtXFx1MEQ5NlxcdTBEOUEtXFx1MERCMVxcdTBEQjMtXFx1MERCQlxcdTBEQkRcXHUwREMwLVxcdTBEQzZcXHUwRTAxLVxcdTBFMzBcXHUwRTMyXFx1MEUzM1xcdTBFNDAtXFx1MEU0NlxcdTBFODFcXHUwRTgyXFx1MEU4NFxcdTBFODdcXHUwRTg4XFx1MEU4QVxcdTBFOERcXHUwRTk0LVxcdTBFOTdcXHUwRTk5LVxcdTBFOUZcXHUwRUExLVxcdTBFQTNcXHUwRUE1XFx1MEVBN1xcdTBFQUFcXHUwRUFCXFx1MEVBRC1cXHUwRUIwXFx1MEVCMlxcdTBFQjNcXHUwRUJEXFx1MEVDMC1cXHUwRUM0XFx1MEVDNlxcdTBFREMtXFx1MEVERlxcdTBGMDBcXHUwRjQwLVxcdTBGNDdcXHUwRjQ5LVxcdTBGNkNcXHUwRjg4LVxcdTBGOENcXHUxMDAwLVxcdTEwMkFcXHUxMDNGXFx1MTA1MC1cXHUxMDU1XFx1MTA1QS1cXHUxMDVEXFx1MTA2MVxcdTEwNjVcXHUxMDY2XFx1MTA2RS1cXHUxMDcwXFx1MTA3NS1cXHUxMDgxXFx1MTA4RVxcdTEwQTAtXFx1MTBDNVxcdTEwQzdcXHUxMENEXFx1MTBEMC1cXHUxMEZBXFx1MTBGQy1cXHUxMjQ4XFx1MTI0QS1cXHUxMjREXFx1MTI1MC1cXHUxMjU2XFx1MTI1OFxcdTEyNUEtXFx1MTI1RFxcdTEyNjAtXFx1MTI4OFxcdTEyOEEtXFx1MTI4RFxcdTEyOTAtXFx1MTJCMFxcdTEyQjItXFx1MTJCNVxcdTEyQjgtXFx1MTJCRVxcdTEyQzBcXHUxMkMyLVxcdTEyQzVcXHUxMkM4LVxcdTEyRDZcXHUxMkQ4LVxcdTEzMTBcXHUxMzEyLVxcdTEzMTVcXHUxMzE4LVxcdTEzNUFcXHUxMzgwLVxcdTEzOEZcXHUxM0EwLVxcdTEzRjRcXHUxNDAxLVxcdTE2NkNcXHUxNjZGLVxcdTE2N0ZcXHUxNjgxLVxcdTE2OUFcXHUxNkEwLVxcdTE2RUFcXHUxNzAwLVxcdTE3MENcXHUxNzBFLVxcdTE3MTFcXHUxNzIwLVxcdTE3MzFcXHUxNzQwLVxcdTE3NTFcXHUxNzYwLVxcdTE3NkNcXHUxNzZFLVxcdTE3NzBcXHUxNzgwLVxcdTE3QjNcXHUxN0Q3XFx1MTdEQ1xcdTE4MjAtXFx1MTg3N1xcdTE4ODAtXFx1MThBOFxcdTE4QUFcXHUxOEIwLVxcdTE4RjVcXHUxOTAwLVxcdTE5MUNcXHUxOTUwLVxcdTE5NkRcXHUxOTcwLVxcdTE5NzRcXHUxOTgwLVxcdTE5QUJcXHUxOUMxLVxcdTE5QzdcXHUxQTAwLVxcdTFBMTZcXHUxQTIwLVxcdTFBNTRcXHUxQUE3XFx1MUIwNS1cXHUxQjMzXFx1MUI0NS1cXHUxQjRCXFx1MUI4My1cXHUxQkEwXFx1MUJBRVxcdTFCQUZcXHUxQkJBLVxcdTFCRTVcXHUxQzAwLVxcdTFDMjNcXHUxQzRELVxcdTFDNEZcXHUxQzVBLVxcdTFDN0RcXHUxQ0U5LVxcdTFDRUNcXHUxQ0VFLVxcdTFDRjFcXHUxQ0Y1XFx1MUNGNlxcdTFEMDAtXFx1MURCRlxcdTFFMDAtXFx1MUYxNVxcdTFGMTgtXFx1MUYxRFxcdTFGMjAtXFx1MUY0NVxcdTFGNDgtXFx1MUY0RFxcdTFGNTAtXFx1MUY1N1xcdTFGNTlcXHUxRjVCXFx1MUY1RFxcdTFGNUYtXFx1MUY3RFxcdTFGODAtXFx1MUZCNFxcdTFGQjYtXFx1MUZCQ1xcdTFGQkVcXHUxRkMyLVxcdTFGQzRcXHUxRkM2LVxcdTFGQ0NcXHUxRkQwLVxcdTFGRDNcXHUxRkQ2LVxcdTFGREJcXHUxRkUwLVxcdTFGRUNcXHUxRkYyLVxcdTFGRjRcXHUxRkY2LVxcdTFGRkNcXHUyMDcxXFx1MjA3RlxcdTIwOTAtXFx1MjA5Q1xcdTIxMDJcXHUyMTA3XFx1MjEwQS1cXHUyMTEzXFx1MjExNVxcdTIxMTktXFx1MjExRFxcdTIxMjRcXHUyMTI2XFx1MjEyOFxcdTIxMkEtXFx1MjEyRFxcdTIxMkYtXFx1MjEzOVxcdTIxM0MtXFx1MjEzRlxcdTIxNDUtXFx1MjE0OVxcdTIxNEVcXHUyMTgzXFx1MjE4NFxcdTJDMDAtXFx1MkMyRVxcdTJDMzAtXFx1MkM1RVxcdTJDNjAtXFx1MkNFNFxcdTJDRUItXFx1MkNFRVxcdTJDRjJcXHUyQ0YzXFx1MkQwMC1cXHUyRDI1XFx1MkQyN1xcdTJEMkRcXHUyRDMwLVxcdTJENjdcXHUyRDZGXFx1MkQ4MC1cXHUyRDk2XFx1MkRBMC1cXHUyREE2XFx1MkRBOC1cXHUyREFFXFx1MkRCMC1cXHUyREI2XFx1MkRCOC1cXHUyREJFXFx1MkRDMC1cXHUyREM2XFx1MkRDOC1cXHUyRENFXFx1MkREMC1cXHUyREQ2XFx1MkREOC1cXHUyRERFXFx1MkUyRlxcdTMwMDVcXHUzMDA2XFx1MzAzMS1cXHUzMDM1XFx1MzAzQlxcdTMwM0NcXHUzMDQxLVxcdTMwOTZcXHUzMDlELVxcdTMwOUZcXHUzMEExLVxcdTMwRkFcXHUzMEZDLVxcdTMwRkZcXHUzMTA1LVxcdTMxMkRcXHUzMTMxLVxcdTMxOEVcXHUzMUEwLVxcdTMxQkFcXHUzMUYwLVxcdTMxRkZcXHUzNDAwLVxcdTREQjVcXHU0RTAwLVxcdTlGQ0NcXHVBMDAwLVxcdUE0OENcXHVBNEQwLVxcdUE0RkRcXHVBNTAwLVxcdUE2MENcXHVBNjEwLVxcdUE2MUZcXHVBNjJBXFx1QTYyQlxcdUE2NDAtXFx1QTY2RVxcdUE2N0YtXFx1QTY5N1xcdUE2QTAtXFx1QTZFNVxcdUE3MTctXFx1QTcxRlxcdUE3MjItXFx1QTc4OFxcdUE3OEItXFx1QTc4RVxcdUE3OTAtXFx1QTc5M1xcdUE3QTAtXFx1QTdBQVxcdUE3RjgtXFx1QTgwMVxcdUE4MDMtXFx1QTgwNVxcdUE4MDctXFx1QTgwQVxcdUE4MEMtXFx1QTgyMlxcdUE4NDAtXFx1QTg3M1xcdUE4ODItXFx1QThCM1xcdUE4RjItXFx1QThGN1xcdUE4RkJcXHVBOTBBLVxcdUE5MjVcXHVBOTMwLVxcdUE5NDZcXHVBOTYwLVxcdUE5N0NcXHVBOTg0LVxcdUE5QjJcXHVBOUNGXFx1QUEwMC1cXHVBQTI4XFx1QUE0MC1cXHVBQTQyXFx1QUE0NC1cXHVBQTRCXFx1QUE2MC1cXHVBQTc2XFx1QUE3QVxcdUFBODAtXFx1QUFBRlxcdUFBQjFcXHVBQUI1XFx1QUFCNlxcdUFBQjktXFx1QUFCRFxcdUFBQzBcXHVBQUMyXFx1QUFEQi1cXHVBQUREXFx1QUFFMC1cXHVBQUVBXFx1QUFGMi1cXHVBQUY0XFx1QUIwMS1cXHVBQjA2XFx1QUIwOS1cXHVBQjBFXFx1QUIxMS1cXHVBQjE2XFx1QUIyMC1cXHVBQjI2XFx1QUIyOC1cXHVBQjJFXFx1QUJDMC1cXHVBQkUyXFx1QUMwMC1cXHVEN0EzXFx1RDdCMC1cXHVEN0M2XFx1RDdDQi1cXHVEN0ZCXFx1RjkwMC1cXHVGQTZEXFx1RkE3MC1cXHVGQUQ5XFx1RkIwMC1cXHVGQjA2XFx1RkIxMy1cXHVGQjE3XFx1RkIxRFxcdUZCMUYtXFx1RkIyOFxcdUZCMkEtXFx1RkIzNlxcdUZCMzgtXFx1RkIzQ1xcdUZCM0VcXHVGQjQwXFx1RkI0MVxcdUZCNDNcXHVGQjQ0XFx1RkI0Ni1cXHVGQkIxXFx1RkJEMy1cXHVGRDNEXFx1RkQ1MC1cXHVGRDhGXFx1RkQ5Mi1cXHVGREM3XFx1RkRGMC1cXHVGREZCXFx1RkU3MC1cXHVGRTc0XFx1RkU3Ni1cXHVGRUZDXFx1RkYyMS1cXHVGRjNBXFx1RkY0MS1cXHVGRjVBXFx1RkY2Ni1cXHVGRkJFXFx1RkZDMi1cXHVGRkM3XFx1RkZDQS1cXHVGRkNGXFx1RkZEMi1cXHVGRkQ3XFx1RkZEQS1cXHVGRkRDXS8sXG4gIC8vIGh0dHA6Ly9zdGFja292ZXJmbG93LmNvbS9hLzIyMDc1MDcwXG4gICcqJzogLy4vXG59O1xuXG4vKiogKi9cbmNsYXNzIFBhdHRlcm5JbnB1dERlZmluaXRpb24ge1xuICAvKiogKi9cblxuICAvKiogKi9cblxuICAvKiogKi9cblxuICAvKiogKi9cblxuICAvKiogKi9cblxuICAvKiogKi9cblxuICAvKiogKi9cblxuICAvKiogKi9cblxuICBjb25zdHJ1Y3RvcihvcHRzKSB7XG4gICAgY29uc3Qge1xuICAgICAgICBwYXJlbnQsXG4gICAgICAgIGlzT3B0aW9uYWwsXG4gICAgICAgIHBsYWNlaG9sZGVyQ2hhcixcbiAgICAgICAgZGlzcGxheUNoYXIsXG4gICAgICAgIGxhenksXG4gICAgICAgIGVhZ2VyXG4gICAgICB9ID0gb3B0cyxcbiAgICAgIG1hc2tPcHRzID0gX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2Uob3B0cywgX2V4Y2x1ZGVkKTtcbiAgICB0aGlzLm1hc2tlZCA9IGNyZWF0ZU1hc2sobWFza09wdHMpO1xuICAgIE9iamVjdC5hc3NpZ24odGhpcywge1xuICAgICAgcGFyZW50LFxuICAgICAgaXNPcHRpb25hbCxcbiAgICAgIHBsYWNlaG9sZGVyQ2hhcixcbiAgICAgIGRpc3BsYXlDaGFyLFxuICAgICAgbGF6eSxcbiAgICAgIGVhZ2VyXG4gICAgfSk7XG4gIH1cbiAgcmVzZXQoKSB7XG4gICAgdGhpcy5pc0ZpbGxlZCA9IGZhbHNlO1xuICAgIHRoaXMubWFza2VkLnJlc2V0KCk7XG4gIH1cbiAgcmVtb3ZlKCkge1xuICAgIGxldCBmcm9tUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDAgJiYgYXJndW1lbnRzWzBdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMF0gOiAwO1xuICAgIGxldCB0b1BvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogdGhpcy52YWx1ZS5sZW5ndGg7XG4gICAgaWYgKGZyb21Qb3MgPT09IDAgJiYgdG9Qb3MgPj0gMSkge1xuICAgICAgdGhpcy5pc0ZpbGxlZCA9IGZhbHNlO1xuICAgICAgcmV0dXJuIHRoaXMubWFza2VkLnJlbW92ZShmcm9tUG9zLCB0b1Bvcyk7XG4gICAgfVxuICAgIHJldHVybiBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICB9XG4gIGdldCB2YWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5tYXNrZWQudmFsdWUgfHwgKHRoaXMuaXNGaWxsZWQgJiYgIXRoaXMuaXNPcHRpb25hbCA/IHRoaXMucGxhY2Vob2xkZXJDaGFyIDogJycpO1xuICB9XG4gIGdldCB1bm1hc2tlZFZhbHVlKCkge1xuICAgIHJldHVybiB0aGlzLm1hc2tlZC51bm1hc2tlZFZhbHVlO1xuICB9XG4gIGdldCBkaXNwbGF5VmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMubWFza2VkLnZhbHVlICYmIHRoaXMuZGlzcGxheUNoYXIgfHwgdGhpcy52YWx1ZTtcbiAgfVxuICBnZXQgaXNDb21wbGV0ZSgpIHtcbiAgICByZXR1cm4gQm9vbGVhbih0aGlzLm1hc2tlZC52YWx1ZSkgfHwgdGhpcy5pc09wdGlvbmFsO1xuICB9XG4gIF9hcHBlbmRDaGFyKGNoKSB7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB7fTtcbiAgICBpZiAodGhpcy5pc0ZpbGxlZCkgcmV0dXJuIG5ldyBDaGFuZ2VEZXRhaWxzKCk7XG4gICAgY29uc3Qgc3RhdGUgPSB0aGlzLm1hc2tlZC5zdGF0ZTtcbiAgICAvLyBzaW11bGF0ZSBpbnB1dFxuICAgIGNvbnN0IGRldGFpbHMgPSB0aGlzLm1hc2tlZC5fYXBwZW5kQ2hhcihjaCwgZmxhZ3MpO1xuICAgIGlmIChkZXRhaWxzLmluc2VydGVkICYmIHRoaXMuZG9WYWxpZGF0ZShmbGFncykgPT09IGZhbHNlKSB7XG4gICAgICBkZXRhaWxzLmluc2VydGVkID0gZGV0YWlscy5yYXdJbnNlcnRlZCA9ICcnO1xuICAgICAgdGhpcy5tYXNrZWQuc3RhdGUgPSBzdGF0ZTtcbiAgICB9XG4gICAgaWYgKCFkZXRhaWxzLmluc2VydGVkICYmICF0aGlzLmlzT3B0aW9uYWwgJiYgIXRoaXMubGF6eSAmJiAhZmxhZ3MuaW5wdXQpIHtcbiAgICAgIGRldGFpbHMuaW5zZXJ0ZWQgPSB0aGlzLnBsYWNlaG9sZGVyQ2hhcjtcbiAgICB9XG4gICAgZGV0YWlscy5za2lwID0gIWRldGFpbHMuaW5zZXJ0ZWQgJiYgIXRoaXMuaXNPcHRpb25hbDtcbiAgICB0aGlzLmlzRmlsbGVkID0gQm9vbGVhbihkZXRhaWxzLmluc2VydGVkKTtcbiAgICByZXR1cm4gZGV0YWlscztcbiAgfVxuICBhcHBlbmQoKSB7XG4gICAgLy8gVE9ETyBwcm9iYWJseSBzaG91bGQgYmUgZG9uZSB2aWEgX2FwcGVuZENoYXJcbiAgICByZXR1cm4gdGhpcy5tYXNrZWQuYXBwZW5kKC4uLmFyZ3VtZW50cyk7XG4gIH1cbiAgX2FwcGVuZFBsYWNlaG9sZGVyKCkge1xuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgIGlmICh0aGlzLmlzRmlsbGVkIHx8IHRoaXMuaXNPcHRpb25hbCkgcmV0dXJuIGRldGFpbHM7XG4gICAgdGhpcy5pc0ZpbGxlZCA9IHRydWU7XG4gICAgZGV0YWlscy5pbnNlcnRlZCA9IHRoaXMucGxhY2Vob2xkZXJDaGFyO1xuICAgIHJldHVybiBkZXRhaWxzO1xuICB9XG4gIF9hcHBlbmRFYWdlcigpIHtcbiAgICByZXR1cm4gbmV3IENoYW5nZURldGFpbHMoKTtcbiAgfVxuICBleHRyYWN0VGFpbCgpIHtcbiAgICByZXR1cm4gdGhpcy5tYXNrZWQuZXh0cmFjdFRhaWwoLi4uYXJndW1lbnRzKTtcbiAgfVxuICBhcHBlbmRUYWlsKCkge1xuICAgIHJldHVybiB0aGlzLm1hc2tlZC5hcHBlbmRUYWlsKC4uLmFyZ3VtZW50cyk7XG4gIH1cbiAgZXh0cmFjdElucHV0KCkge1xuICAgIGxldCBmcm9tUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDAgJiYgYXJndW1lbnRzWzBdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMF0gOiAwO1xuICAgIGxldCB0b1BvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogdGhpcy52YWx1ZS5sZW5ndGg7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDIgPyBhcmd1bWVudHNbMl0gOiB1bmRlZmluZWQ7XG4gICAgcmV0dXJuIHRoaXMubWFza2VkLmV4dHJhY3RJbnB1dChmcm9tUG9zLCB0b1BvcywgZmxhZ3MpO1xuICB9XG4gIG5lYXJlc3RJbnB1dFBvcyhjdXJzb3JQb3MpIHtcbiAgICBsZXQgZGlyZWN0aW9uID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiBESVJFQ1RJT04uTk9ORTtcbiAgICBjb25zdCBtaW5Qb3MgPSAwO1xuICAgIGNvbnN0IG1heFBvcyA9IHRoaXMudmFsdWUubGVuZ3RoO1xuICAgIGNvbnN0IGJvdW5kUG9zID0gTWF0aC5taW4oTWF0aC5tYXgoY3Vyc29yUG9zLCBtaW5Qb3MpLCBtYXhQb3MpO1xuICAgIHN3aXRjaCAoZGlyZWN0aW9uKSB7XG4gICAgICBjYXNlIERJUkVDVElPTi5MRUZUOlxuICAgICAgY2FzZSBESVJFQ1RJT04uRk9SQ0VfTEVGVDpcbiAgICAgICAgcmV0dXJuIHRoaXMuaXNDb21wbGV0ZSA/IGJvdW5kUG9zIDogbWluUG9zO1xuICAgICAgY2FzZSBESVJFQ1RJT04uUklHSFQ6XG4gICAgICBjYXNlIERJUkVDVElPTi5GT1JDRV9SSUdIVDpcbiAgICAgICAgcmV0dXJuIHRoaXMuaXNDb21wbGV0ZSA/IGJvdW5kUG9zIDogbWF4UG9zO1xuICAgICAgY2FzZSBESVJFQ1RJT04uTk9ORTpcbiAgICAgIGRlZmF1bHQ6XG4gICAgICAgIHJldHVybiBib3VuZFBvcztcbiAgICB9XG4gIH1cbiAgdG90YWxJbnB1dFBvc2l0aW9ucygpIHtcbiAgICBsZXQgZnJvbVBvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDogMDtcbiAgICBsZXQgdG9Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHRoaXMudmFsdWUubGVuZ3RoO1xuICAgIHJldHVybiB0aGlzLnZhbHVlLnNsaWNlKGZyb21Qb3MsIHRvUG9zKS5sZW5ndGg7XG4gIH1cbiAgZG9WYWxpZGF0ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5tYXNrZWQuZG9WYWxpZGF0ZSguLi5hcmd1bWVudHMpICYmICghdGhpcy5wYXJlbnQgfHwgdGhpcy5wYXJlbnQuZG9WYWxpZGF0ZSguLi5hcmd1bWVudHMpKTtcbiAgfVxuICBkb0NvbW1pdCgpIHtcbiAgICB0aGlzLm1hc2tlZC5kb0NvbW1pdCgpO1xuICB9XG4gIGdldCBzdGF0ZSgpIHtcbiAgICByZXR1cm4ge1xuICAgICAgbWFza2VkOiB0aGlzLm1hc2tlZC5zdGF0ZSxcbiAgICAgIGlzRmlsbGVkOiB0aGlzLmlzRmlsbGVkXG4gICAgfTtcbiAgfVxuICBzZXQgc3RhdGUoc3RhdGUpIHtcbiAgICB0aGlzLm1hc2tlZC5zdGF0ZSA9IHN0YXRlLm1hc2tlZDtcbiAgICB0aGlzLmlzRmlsbGVkID0gc3RhdGUuaXNGaWxsZWQ7XG4gIH1cbn1cblxuZXhwb3J0IHsgREVGQVVMVF9JTlBVVF9ERUZJTklUSU9OUywgUGF0dGVybklucHV0RGVmaW5pdGlvbiBhcyBkZWZhdWx0IH07XG4iLCAiaW1wb3J0IENoYW5nZURldGFpbHMgZnJvbSAnLi4vLi4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5pbXBvcnQgeyBESVJFQ1RJT04sIGlzU3RyaW5nIH0gZnJvbSAnLi4vLi4vY29yZS91dGlscy5qcyc7XG5pbXBvcnQgQ29udGludW91c1RhaWxEZXRhaWxzIGZyb20gJy4uLy4uL2NvcmUvY29udGludW91cy10YWlsLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuLi8uLi9jb3JlL2hvbGRlci5qcyc7XG5cbi8qKiAqL1xuXG5jbGFzcyBQYXR0ZXJuRml4ZWREZWZpbml0aW9uIHtcbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgY29uc3RydWN0b3Iob3B0cykge1xuICAgIE9iamVjdC5hc3NpZ24odGhpcywgb3B0cyk7XG4gICAgdGhpcy5fdmFsdWUgPSAnJztcbiAgICB0aGlzLmlzRml4ZWQgPSB0cnVlO1xuICB9XG4gIGdldCB2YWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5fdmFsdWU7XG4gIH1cbiAgZ2V0IHVubWFza2VkVmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMuaXNVbm1hc2tpbmcgPyB0aGlzLnZhbHVlIDogJyc7XG4gIH1cbiAgZ2V0IGRpc3BsYXlWYWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy52YWx1ZTtcbiAgfVxuICByZXNldCgpIHtcbiAgICB0aGlzLl9pc1Jhd0lucHV0ID0gZmFsc2U7XG4gICAgdGhpcy5fdmFsdWUgPSAnJztcbiAgfVxuICByZW1vdmUoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLl92YWx1ZS5sZW5ndGg7XG4gICAgdGhpcy5fdmFsdWUgPSB0aGlzLl92YWx1ZS5zbGljZSgwLCBmcm9tUG9zKSArIHRoaXMuX3ZhbHVlLnNsaWNlKHRvUG9zKTtcbiAgICBpZiAoIXRoaXMuX3ZhbHVlKSB0aGlzLl9pc1Jhd0lucHV0ID0gZmFsc2U7XG4gICAgcmV0dXJuIG5ldyBDaGFuZ2VEZXRhaWxzKCk7XG4gIH1cbiAgbmVhcmVzdElucHV0UG9zKGN1cnNvclBvcykge1xuICAgIGxldCBkaXJlY3Rpb24gPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IERJUkVDVElPTi5OT05FO1xuICAgIGNvbnN0IG1pblBvcyA9IDA7XG4gICAgY29uc3QgbWF4UG9zID0gdGhpcy5fdmFsdWUubGVuZ3RoO1xuICAgIHN3aXRjaCAoZGlyZWN0aW9uKSB7XG4gICAgICBjYXNlIERJUkVDVElPTi5MRUZUOlxuICAgICAgY2FzZSBESVJFQ1RJT04uRk9SQ0VfTEVGVDpcbiAgICAgICAgcmV0dXJuIG1pblBvcztcbiAgICAgIGNhc2UgRElSRUNUSU9OLk5PTkU6XG4gICAgICBjYXNlIERJUkVDVElPTi5SSUdIVDpcbiAgICAgIGNhc2UgRElSRUNUSU9OLkZPUkNFX1JJR0hUOlxuICAgICAgZGVmYXVsdDpcbiAgICAgICAgcmV0dXJuIG1heFBvcztcbiAgICB9XG4gIH1cbiAgdG90YWxJbnB1dFBvc2l0aW9ucygpIHtcbiAgICBsZXQgZnJvbVBvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDogMDtcbiAgICBsZXQgdG9Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHRoaXMuX3ZhbHVlLmxlbmd0aDtcbiAgICByZXR1cm4gdGhpcy5faXNSYXdJbnB1dCA/IHRvUG9zIC0gZnJvbVBvcyA6IDA7XG4gIH1cbiAgZXh0cmFjdElucHV0KCkge1xuICAgIGxldCBmcm9tUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDAgJiYgYXJndW1lbnRzWzBdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMF0gOiAwO1xuICAgIGxldCB0b1BvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogdGhpcy5fdmFsdWUubGVuZ3RoO1xuICAgIGxldCBmbGFncyA9IGFyZ3VtZW50cy5sZW5ndGggPiAyICYmIGFyZ3VtZW50c1syXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzJdIDoge307XG4gICAgcmV0dXJuIGZsYWdzLnJhdyAmJiB0aGlzLl9pc1Jhd0lucHV0ICYmIHRoaXMuX3ZhbHVlLnNsaWNlKGZyb21Qb3MsIHRvUG9zKSB8fCAnJztcbiAgfVxuICBnZXQgaXNDb21wbGV0ZSgpIHtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxuICBnZXQgaXNGaWxsZWQoKSB7XG4gICAgcmV0dXJuIEJvb2xlYW4odGhpcy5fdmFsdWUpO1xuICB9XG4gIF9hcHBlbmRDaGFyKGNoKSB7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB7fTtcbiAgICBjb25zdCBkZXRhaWxzID0gbmV3IENoYW5nZURldGFpbHMoKTtcbiAgICBpZiAodGhpcy5pc0ZpbGxlZCkgcmV0dXJuIGRldGFpbHM7XG4gICAgY29uc3QgYXBwZW5kRWFnZXIgPSB0aGlzLmVhZ2VyID09PSB0cnVlIHx8IHRoaXMuZWFnZXIgPT09ICdhcHBlbmQnO1xuICAgIGNvbnN0IGFwcGVuZGVkID0gdGhpcy5jaGFyID09PSBjaDtcbiAgICBjb25zdCBpc1Jlc29sdmVkID0gYXBwZW5kZWQgJiYgKHRoaXMuaXNVbm1hc2tpbmcgfHwgZmxhZ3MuaW5wdXQgfHwgZmxhZ3MucmF3KSAmJiAoIWZsYWdzLnJhdyB8fCAhYXBwZW5kRWFnZXIpICYmICFmbGFncy50YWlsO1xuICAgIGlmIChpc1Jlc29sdmVkKSBkZXRhaWxzLnJhd0luc2VydGVkID0gdGhpcy5jaGFyO1xuICAgIHRoaXMuX3ZhbHVlID0gZGV0YWlscy5pbnNlcnRlZCA9IHRoaXMuY2hhcjtcbiAgICB0aGlzLl9pc1Jhd0lucHV0ID0gaXNSZXNvbHZlZCAmJiAoZmxhZ3MucmF3IHx8IGZsYWdzLmlucHV0KTtcbiAgICByZXR1cm4gZGV0YWlscztcbiAgfVxuICBfYXBwZW5kRWFnZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuX2FwcGVuZENoYXIodGhpcy5jaGFyLCB7XG4gICAgICB0YWlsOiB0cnVlXG4gICAgfSk7XG4gIH1cbiAgX2FwcGVuZFBsYWNlaG9sZGVyKCkge1xuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgIGlmICh0aGlzLmlzRmlsbGVkKSByZXR1cm4gZGV0YWlscztcbiAgICB0aGlzLl92YWx1ZSA9IGRldGFpbHMuaW5zZXJ0ZWQgPSB0aGlzLmNoYXI7XG4gICAgcmV0dXJuIGRldGFpbHM7XG4gIH1cbiAgZXh0cmFjdFRhaWwoKSB7XG4gICAgYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICByZXR1cm4gbmV3IENvbnRpbnVvdXNUYWlsRGV0YWlscygnJyk7XG4gIH1cblxuICAvLyAkRmxvd0ZpeE1lIG5vIGlkZWFzXG4gIGFwcGVuZFRhaWwodGFpbCkge1xuICAgIGlmIChpc1N0cmluZyh0YWlsKSkgdGFpbCA9IG5ldyBDb250aW51b3VzVGFpbERldGFpbHMoU3RyaW5nKHRhaWwpKTtcbiAgICByZXR1cm4gdGFpbC5hcHBlbmRUbyh0aGlzKTtcbiAgfVxuICBhcHBlbmQoc3RyLCBmbGFncywgdGFpbCkge1xuICAgIGNvbnN0IGRldGFpbHMgPSB0aGlzLl9hcHBlbmRDaGFyKHN0clswXSwgZmxhZ3MpO1xuICAgIGlmICh0YWlsICE9IG51bGwpIHtcbiAgICAgIGRldGFpbHMudGFpbFNoaWZ0ICs9IHRoaXMuYXBwZW5kVGFpbCh0YWlsKS50YWlsU2hpZnQ7XG4gICAgfVxuICAgIHJldHVybiBkZXRhaWxzO1xuICB9XG4gIGRvQ29tbWl0KCkge31cbiAgZ2V0IHN0YXRlKCkge1xuICAgIHJldHVybiB7XG4gICAgICBfdmFsdWU6IHRoaXMuX3ZhbHVlLFxuICAgICAgX2lzUmF3SW5wdXQ6IHRoaXMuX2lzUmF3SW5wdXRcbiAgICB9O1xuICB9XG4gIHNldCBzdGF0ZShzdGF0ZSkge1xuICAgIE9iamVjdC5hc3NpZ24odGhpcywgc3RhdGUpO1xuICB9XG59XG5cbmV4cG9ydCB7IFBhdHRlcm5GaXhlZERlZmluaXRpb24gYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCB7IF8gYXMgX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2UgfSBmcm9tICcuLi8uLi9fcm9sbHVwUGx1Z2luQmFiZWxIZWxwZXJzLTZiM2JkNDA0LmpzJztcbmltcG9ydCBDaGFuZ2VEZXRhaWxzIGZyb20gJy4uLy4uL2NvcmUvY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0IHsgaXNTdHJpbmcgfSBmcm9tICcuLi8uLi9jb3JlL3V0aWxzLmpzJztcbmltcG9ydCBDb250aW51b3VzVGFpbERldGFpbHMgZnJvbSAnLi4vLi4vY29yZS9jb250aW51b3VzLXRhaWwtZGV0YWlscy5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vLi4vY29yZS9ob2xkZXIuanMnO1xuXG5jb25zdCBfZXhjbHVkZWQgPSBbXCJjaHVua3NcIl07XG5jbGFzcyBDaHVua3NUYWlsRGV0YWlscyB7XG4gIC8qKiAqL1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIGxldCBjaHVua3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IFtdO1xuICAgIGxldCBmcm9tID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiAwO1xuICAgIHRoaXMuY2h1bmtzID0gY2h1bmtzO1xuICAgIHRoaXMuZnJvbSA9IGZyb207XG4gIH1cbiAgdG9TdHJpbmcoKSB7XG4gICAgcmV0dXJuIHRoaXMuY2h1bmtzLm1hcChTdHJpbmcpLmpvaW4oJycpO1xuICB9XG5cbiAgLy8gJEZsb3dGaXhNZSBubyBpZGVhc1xuICBleHRlbmQodGFpbENodW5rKSB7XG4gICAgaWYgKCFTdHJpbmcodGFpbENodW5rKSkgcmV0dXJuO1xuICAgIGlmIChpc1N0cmluZyh0YWlsQ2h1bmspKSB0YWlsQ2h1bmsgPSBuZXcgQ29udGludW91c1RhaWxEZXRhaWxzKFN0cmluZyh0YWlsQ2h1bmspKTtcbiAgICBjb25zdCBsYXN0Q2h1bmsgPSB0aGlzLmNodW5rc1t0aGlzLmNodW5rcy5sZW5ndGggLSAxXTtcbiAgICBjb25zdCBleHRlbmRMYXN0ID0gbGFzdENodW5rICYmIChcbiAgICAvLyBpZiBzdG9wcyBhcmUgc2FtZSBvciB0YWlsIGhhcyBubyBzdG9wXG4gICAgbGFzdENodW5rLnN0b3AgPT09IHRhaWxDaHVuay5zdG9wIHx8IHRhaWxDaHVuay5zdG9wID09IG51bGwpICYmXG4gICAgLy8gaWYgdGFpbCBjaHVuayBnb2VzIGp1c3QgYWZ0ZXIgbGFzdCBjaHVua1xuICAgIHRhaWxDaHVuay5mcm9tID09PSBsYXN0Q2h1bmsuZnJvbSArIGxhc3RDaHVuay50b1N0cmluZygpLmxlbmd0aDtcbiAgICBpZiAodGFpbENodW5rIGluc3RhbmNlb2YgQ29udGludW91c1RhaWxEZXRhaWxzKSB7XG4gICAgICAvLyBjaGVjayB0aGUgYWJpbGl0eSB0byBleHRlbmQgcHJldmlvdXMgY2h1bmtcbiAgICAgIGlmIChleHRlbmRMYXN0KSB7XG4gICAgICAgIC8vIGV4dGVuZCBwcmV2aW91cyBjaHVua1xuICAgICAgICBsYXN0Q2h1bmsuZXh0ZW5kKHRhaWxDaHVuay50b1N0cmluZygpKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIC8vIGFwcGVuZCBuZXcgY2h1bmtcbiAgICAgICAgdGhpcy5jaHVua3MucHVzaCh0YWlsQ2h1bmspO1xuICAgICAgfVxuICAgIH0gZWxzZSBpZiAodGFpbENodW5rIGluc3RhbmNlb2YgQ2h1bmtzVGFpbERldGFpbHMpIHtcbiAgICAgIGlmICh0YWlsQ2h1bmsuc3RvcCA9PSBudWxsKSB7XG4gICAgICAgIC8vIHVud3JhcCBmbG9hdGluZyBjaHVua3MgdG8gcGFyZW50LCBrZWVwaW5nIGBmcm9tYCBwb3NcbiAgICAgICAgbGV0IGZpcnN0VGFpbENodW5rO1xuICAgICAgICB3aGlsZSAodGFpbENodW5rLmNodW5rcy5sZW5ndGggJiYgdGFpbENodW5rLmNodW5rc1swXS5zdG9wID09IG51bGwpIHtcbiAgICAgICAgICBmaXJzdFRhaWxDaHVuayA9IHRhaWxDaHVuay5jaHVua3Muc2hpZnQoKTtcbiAgICAgICAgICBmaXJzdFRhaWxDaHVuay5mcm9tICs9IHRhaWxDaHVuay5mcm9tO1xuICAgICAgICAgIHRoaXMuZXh0ZW5kKGZpcnN0VGFpbENodW5rKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICAvLyBpZiB0YWlsIGNodW5rIHN0aWxsIGhhcyB2YWx1ZVxuICAgICAgaWYgKHRhaWxDaHVuay50b1N0cmluZygpKSB7XG4gICAgICAgIC8vIGlmIGNodW5rcyBjb250YWlucyBzdG9wcywgdGhlbiBwb3B1cCBzdG9wIHRvIGNvbnRhaW5lclxuICAgICAgICB0YWlsQ2h1bmsuc3RvcCA9IHRhaWxDaHVuay5ibG9ja0luZGV4O1xuICAgICAgICB0aGlzLmNodW5rcy5wdXNoKHRhaWxDaHVuayk7XG4gICAgICB9XG4gICAgfVxuICB9XG4gIGFwcGVuZFRvKG1hc2tlZCkge1xuICAgIC8vICRGbG93Rml4TWVcbiAgICBpZiAoIShtYXNrZWQgaW5zdGFuY2VvZiBJTWFzay5NYXNrZWRQYXR0ZXJuKSkge1xuICAgICAgY29uc3QgdGFpbCA9IG5ldyBDb250aW51b3VzVGFpbERldGFpbHModGhpcy50b1N0cmluZygpKTtcbiAgICAgIHJldHVybiB0YWlsLmFwcGVuZFRvKG1hc2tlZCk7XG4gICAgfVxuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgIGZvciAobGV0IGNpID0gMDsgY2kgPCB0aGlzLmNodW5rcy5sZW5ndGggJiYgIWRldGFpbHMuc2tpcDsgKytjaSkge1xuICAgICAgY29uc3QgY2h1bmsgPSB0aGlzLmNodW5rc1tjaV07XG4gICAgICBjb25zdCBsYXN0QmxvY2tJdGVyID0gbWFza2VkLl9tYXBQb3NUb0Jsb2NrKG1hc2tlZC52YWx1ZS5sZW5ndGgpO1xuICAgICAgY29uc3Qgc3RvcCA9IGNodW5rLnN0b3A7XG4gICAgICBsZXQgY2h1bmtCbG9jaztcbiAgICAgIGlmIChzdG9wICE9IG51bGwgJiYgKFxuICAgICAgLy8gaWYgYmxvY2sgbm90IGZvdW5kIG9yIHN0b3AgaXMgYmVoaW5kIGxhc3RCbG9ja1xuICAgICAgIWxhc3RCbG9ja0l0ZXIgfHwgbGFzdEJsb2NrSXRlci5pbmRleCA8PSBzdG9wKSkge1xuICAgICAgICBpZiAoY2h1bmsgaW5zdGFuY2VvZiBDaHVua3NUYWlsRGV0YWlscyB8fFxuICAgICAgICAvLyBmb3IgY29udGludW91cyBibG9jayBhbHNvIGNoZWNrIGlmIHN0b3AgaXMgZXhpc3RcbiAgICAgICAgbWFza2VkLl9zdG9wcy5pbmRleE9mKHN0b3ApID49IDApIHtcbiAgICAgICAgICBjb25zdCBwaERldGFpbHMgPSBtYXNrZWQuX2FwcGVuZFBsYWNlaG9sZGVyKHN0b3ApO1xuICAgICAgICAgIGRldGFpbHMuYWdncmVnYXRlKHBoRGV0YWlscyk7XG4gICAgICAgIH1cbiAgICAgICAgY2h1bmtCbG9jayA9IGNodW5rIGluc3RhbmNlb2YgQ2h1bmtzVGFpbERldGFpbHMgJiYgbWFza2VkLl9ibG9ja3Nbc3RvcF07XG4gICAgICB9XG4gICAgICBpZiAoY2h1bmtCbG9jaykge1xuICAgICAgICBjb25zdCB0YWlsRGV0YWlscyA9IGNodW5rQmxvY2suYXBwZW5kVGFpbChjaHVuayk7XG4gICAgICAgIHRhaWxEZXRhaWxzLnNraXAgPSBmYWxzZTsgLy8gYWx3YXlzIGlnbm9yZSBza2lwLCBpdCB3aWxsIGJlIHNldCBvbiBsYXN0XG4gICAgICAgIGRldGFpbHMuYWdncmVnYXRlKHRhaWxEZXRhaWxzKTtcbiAgICAgICAgbWFza2VkLl92YWx1ZSArPSB0YWlsRGV0YWlscy5pbnNlcnRlZDtcblxuICAgICAgICAvLyBnZXQgbm90IGluc2VydGVkIGNoYXJzXG4gICAgICAgIGNvbnN0IHJlbWFpbkNoYXJzID0gY2h1bmsudG9TdHJpbmcoKS5zbGljZSh0YWlsRGV0YWlscy5yYXdJbnNlcnRlZC5sZW5ndGgpO1xuICAgICAgICBpZiAocmVtYWluQ2hhcnMpIGRldGFpbHMuYWdncmVnYXRlKG1hc2tlZC5hcHBlbmQocmVtYWluQ2hhcnMsIHtcbiAgICAgICAgICB0YWlsOiB0cnVlXG4gICAgICAgIH0pKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGRldGFpbHMuYWdncmVnYXRlKG1hc2tlZC5hcHBlbmQoY2h1bmsudG9TdHJpbmcoKSwge1xuICAgICAgICAgIHRhaWw6IHRydWVcbiAgICAgICAgfSkpO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gZGV0YWlscztcbiAgfVxuICBnZXQgc3RhdGUoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGNodW5rczogdGhpcy5jaHVua3MubWFwKGMgPT4gYy5zdGF0ZSksXG4gICAgICBmcm9tOiB0aGlzLmZyb20sXG4gICAgICBzdG9wOiB0aGlzLnN0b3AsXG4gICAgICBibG9ja0luZGV4OiB0aGlzLmJsb2NrSW5kZXhcbiAgICB9O1xuICB9XG4gIHNldCBzdGF0ZShzdGF0ZSkge1xuICAgIGNvbnN0IHtcbiAgICAgICAgY2h1bmtzXG4gICAgICB9ID0gc3RhdGUsXG4gICAgICBwcm9wcyA9IF9vYmplY3RXaXRob3V0UHJvcGVydGllc0xvb3NlKHN0YXRlLCBfZXhjbHVkZWQpO1xuICAgIE9iamVjdC5hc3NpZ24odGhpcywgcHJvcHMpO1xuICAgIHRoaXMuY2h1bmtzID0gY2h1bmtzLm1hcChjc3RhdGUgPT4ge1xuICAgICAgY29uc3QgY2h1bmsgPSBcImNodW5rc1wiIGluIGNzdGF0ZSA/IG5ldyBDaHVua3NUYWlsRGV0YWlscygpIDogbmV3IENvbnRpbnVvdXNUYWlsRGV0YWlscygpO1xuICAgICAgLy8gJEZsb3dGaXhNZSBhbHJlYWR5IGNoZWNrZWQgYWJvdmVcbiAgICAgIGNodW5rLnN0YXRlID0gY3N0YXRlO1xuICAgICAgcmV0dXJuIGNodW5rO1xuICAgIH0pO1xuICB9XG4gIHVuc2hpZnQoYmVmb3JlUG9zKSB7XG4gICAgaWYgKCF0aGlzLmNodW5rcy5sZW5ndGggfHwgYmVmb3JlUG9zICE9IG51bGwgJiYgdGhpcy5mcm9tID49IGJlZm9yZVBvcykgcmV0dXJuICcnO1xuICAgIGNvbnN0IGNodW5rU2hpZnRQb3MgPSBiZWZvcmVQb3MgIT0gbnVsbCA/IGJlZm9yZVBvcyAtIHRoaXMuZnJvbSA6IGJlZm9yZVBvcztcbiAgICBsZXQgY2kgPSAwO1xuICAgIHdoaWxlIChjaSA8IHRoaXMuY2h1bmtzLmxlbmd0aCkge1xuICAgICAgY29uc3QgY2h1bmsgPSB0aGlzLmNodW5rc1tjaV07XG4gICAgICBjb25zdCBzaGlmdENoYXIgPSBjaHVuay51bnNoaWZ0KGNodW5rU2hpZnRQb3MpO1xuICAgICAgaWYgKGNodW5rLnRvU3RyaW5nKCkpIHtcbiAgICAgICAgLy8gY2h1bmsgc3RpbGwgY29udGFpbnMgdmFsdWVcbiAgICAgICAgLy8gYnV0IG5vdCBzaGlmdGVkIC0gbWVhbnMgbm8gbW9yZSBhdmFpbGFibGUgY2hhcnMgdG8gc2hpZnRcbiAgICAgICAgaWYgKCFzaGlmdENoYXIpIGJyZWFrO1xuICAgICAgICArK2NpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgLy8gY2xlYW4gaWYgY2h1bmsgaGFzIG5vIHZhbHVlXG4gICAgICAgIHRoaXMuY2h1bmtzLnNwbGljZShjaSwgMSk7XG4gICAgICB9XG4gICAgICBpZiAoc2hpZnRDaGFyKSByZXR1cm4gc2hpZnRDaGFyO1xuICAgIH1cbiAgICByZXR1cm4gJyc7XG4gIH1cbiAgc2hpZnQoKSB7XG4gICAgaWYgKCF0aGlzLmNodW5rcy5sZW5ndGgpIHJldHVybiAnJztcbiAgICBsZXQgY2kgPSB0aGlzLmNodW5rcy5sZW5ndGggLSAxO1xuICAgIHdoaWxlICgwIDw9IGNpKSB7XG4gICAgICBjb25zdCBjaHVuayA9IHRoaXMuY2h1bmtzW2NpXTtcbiAgICAgIGNvbnN0IHNoaWZ0Q2hhciA9IGNodW5rLnNoaWZ0KCk7XG4gICAgICBpZiAoY2h1bmsudG9TdHJpbmcoKSkge1xuICAgICAgICAvLyBjaHVuayBzdGlsbCBjb250YWlucyB2YWx1ZVxuICAgICAgICAvLyBidXQgbm90IHNoaWZ0ZWQgLSBtZWFucyBubyBtb3JlIGF2YWlsYWJsZSBjaGFycyB0byBzaGlmdFxuICAgICAgICBpZiAoIXNoaWZ0Q2hhcikgYnJlYWs7XG4gICAgICAgIC0tY2k7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAvLyBjbGVhbiBpZiBjaHVuayBoYXMgbm8gdmFsdWVcbiAgICAgICAgdGhpcy5jaHVua3Muc3BsaWNlKGNpLCAxKTtcbiAgICAgIH1cbiAgICAgIGlmIChzaGlmdENoYXIpIHJldHVybiBzaGlmdENoYXI7XG4gICAgfVxuICAgIHJldHVybiAnJztcbiAgfVxufVxuXG5leHBvcnQgeyBDaHVua3NUYWlsRGV0YWlscyBhcyBkZWZhdWx0IH07XG4iLCAiaW1wb3J0IHsgRElSRUNUSU9OIH0gZnJvbSAnLi4vLi4vY29yZS91dGlscy5qcyc7XG5pbXBvcnQgJy4uLy4uL2NvcmUvY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuLi8uLi9jb3JlL2hvbGRlci5qcyc7XG5cbmNsYXNzIFBhdHRlcm5DdXJzb3Ige1xuICBjb25zdHJ1Y3RvcihtYXNrZWQsIHBvcykge1xuICAgIHRoaXMubWFza2VkID0gbWFza2VkO1xuICAgIHRoaXMuX2xvZyA9IFtdO1xuICAgIGNvbnN0IHtcbiAgICAgIG9mZnNldCxcbiAgICAgIGluZGV4XG4gICAgfSA9IG1hc2tlZC5fbWFwUG9zVG9CbG9jayhwb3MpIHx8IChwb3MgPCAwID9cbiAgICAvLyBmaXJzdFxuICAgIHtcbiAgICAgIGluZGV4OiAwLFxuICAgICAgb2Zmc2V0OiAwXG4gICAgfSA6XG4gICAgLy8gbGFzdFxuICAgIHtcbiAgICAgIGluZGV4OiB0aGlzLm1hc2tlZC5fYmxvY2tzLmxlbmd0aCxcbiAgICAgIG9mZnNldDogMFxuICAgIH0pO1xuICAgIHRoaXMub2Zmc2V0ID0gb2Zmc2V0O1xuICAgIHRoaXMuaW5kZXggPSBpbmRleDtcbiAgICB0aGlzLm9rID0gZmFsc2U7XG4gIH1cbiAgZ2V0IGJsb2NrKCkge1xuICAgIHJldHVybiB0aGlzLm1hc2tlZC5fYmxvY2tzW3RoaXMuaW5kZXhdO1xuICB9XG4gIGdldCBwb3MoKSB7XG4gICAgcmV0dXJuIHRoaXMubWFza2VkLl9ibG9ja1N0YXJ0UG9zKHRoaXMuaW5kZXgpICsgdGhpcy5vZmZzZXQ7XG4gIH1cbiAgZ2V0IHN0YXRlKCkge1xuICAgIHJldHVybiB7XG4gICAgICBpbmRleDogdGhpcy5pbmRleCxcbiAgICAgIG9mZnNldDogdGhpcy5vZmZzZXQsXG4gICAgICBvazogdGhpcy5va1xuICAgIH07XG4gIH1cbiAgc2V0IHN0YXRlKHMpIHtcbiAgICBPYmplY3QuYXNzaWduKHRoaXMsIHMpO1xuICB9XG4gIHB1c2hTdGF0ZSgpIHtcbiAgICB0aGlzLl9sb2cucHVzaCh0aGlzLnN0YXRlKTtcbiAgfVxuICBwb3BTdGF0ZSgpIHtcbiAgICBjb25zdCBzID0gdGhpcy5fbG9nLnBvcCgpO1xuICAgIHRoaXMuc3RhdGUgPSBzO1xuICAgIHJldHVybiBzO1xuICB9XG4gIGJpbmRCbG9jaygpIHtcbiAgICBpZiAodGhpcy5ibG9jaykgcmV0dXJuO1xuICAgIGlmICh0aGlzLmluZGV4IDwgMCkge1xuICAgICAgdGhpcy5pbmRleCA9IDA7XG4gICAgICB0aGlzLm9mZnNldCA9IDA7XG4gICAgfVxuICAgIGlmICh0aGlzLmluZGV4ID49IHRoaXMubWFza2VkLl9ibG9ja3MubGVuZ3RoKSB7XG4gICAgICB0aGlzLmluZGV4ID0gdGhpcy5tYXNrZWQuX2Jsb2Nrcy5sZW5ndGggLSAxO1xuICAgICAgdGhpcy5vZmZzZXQgPSB0aGlzLmJsb2NrLnZhbHVlLmxlbmd0aDtcbiAgICB9XG4gIH1cbiAgX3B1c2hMZWZ0KGZuKSB7XG4gICAgdGhpcy5wdXNoU3RhdGUoKTtcbiAgICBmb3IgKHRoaXMuYmluZEJsb2NrKCk7IDAgPD0gdGhpcy5pbmRleDsgLS10aGlzLmluZGV4LCB0aGlzLm9mZnNldCA9ICgoX3RoaXMkYmxvY2sgPSB0aGlzLmJsb2NrKSA9PT0gbnVsbCB8fCBfdGhpcyRibG9jayA9PT0gdm9pZCAwID8gdm9pZCAwIDogX3RoaXMkYmxvY2sudmFsdWUubGVuZ3RoKSB8fCAwKSB7XG4gICAgICB2YXIgX3RoaXMkYmxvY2s7XG4gICAgICBpZiAoZm4oKSkgcmV0dXJuIHRoaXMub2sgPSB0cnVlO1xuICAgIH1cbiAgICByZXR1cm4gdGhpcy5vayA9IGZhbHNlO1xuICB9XG4gIF9wdXNoUmlnaHQoZm4pIHtcbiAgICB0aGlzLnB1c2hTdGF0ZSgpO1xuICAgIGZvciAodGhpcy5iaW5kQmxvY2soKTsgdGhpcy5pbmRleCA8IHRoaXMubWFza2VkLl9ibG9ja3MubGVuZ3RoOyArK3RoaXMuaW5kZXgsIHRoaXMub2Zmc2V0ID0gMCkge1xuICAgICAgaWYgKGZuKCkpIHJldHVybiB0aGlzLm9rID0gdHJ1ZTtcbiAgICB9XG4gICAgcmV0dXJuIHRoaXMub2sgPSBmYWxzZTtcbiAgfVxuICBwdXNoTGVmdEJlZm9yZUZpbGxlZCgpIHtcbiAgICByZXR1cm4gdGhpcy5fcHVzaExlZnQoKCkgPT4ge1xuICAgICAgaWYgKHRoaXMuYmxvY2suaXNGaXhlZCB8fCAhdGhpcy5ibG9jay52YWx1ZSkgcmV0dXJuO1xuICAgICAgdGhpcy5vZmZzZXQgPSB0aGlzLmJsb2NrLm5lYXJlc3RJbnB1dFBvcyh0aGlzLm9mZnNldCwgRElSRUNUSU9OLkZPUkNFX0xFRlQpO1xuICAgICAgaWYgKHRoaXMub2Zmc2V0ICE9PSAwKSByZXR1cm4gdHJ1ZTtcbiAgICB9KTtcbiAgfVxuICBwdXNoTGVmdEJlZm9yZUlucHV0KCkge1xuICAgIC8vIGNhc2VzOlxuICAgIC8vIGZpbGxlZCBpbnB1dDogMDB8XG4gICAgLy8gb3B0aW9uYWwgZW1wdHkgaW5wdXQ6IDAwW118XG4gICAgLy8gbmVzdGVkIGJsb2NrOiBYWDxbXT58XG4gICAgcmV0dXJuIHRoaXMuX3B1c2hMZWZ0KCgpID0+IHtcbiAgICAgIGlmICh0aGlzLmJsb2NrLmlzRml4ZWQpIHJldHVybjtcbiAgICAgIHRoaXMub2Zmc2V0ID0gdGhpcy5ibG9jay5uZWFyZXN0SW5wdXRQb3ModGhpcy5vZmZzZXQsIERJUkVDVElPTi5MRUZUKTtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0pO1xuICB9XG4gIHB1c2hMZWZ0QmVmb3JlUmVxdWlyZWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuX3B1c2hMZWZ0KCgpID0+IHtcbiAgICAgIGlmICh0aGlzLmJsb2NrLmlzRml4ZWQgfHwgdGhpcy5ibG9jay5pc09wdGlvbmFsICYmICF0aGlzLmJsb2NrLnZhbHVlKSByZXR1cm47XG4gICAgICB0aGlzLm9mZnNldCA9IHRoaXMuYmxvY2submVhcmVzdElucHV0UG9zKHRoaXMub2Zmc2V0LCBESVJFQ1RJT04uTEVGVCk7XG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9KTtcbiAgfVxuICBwdXNoUmlnaHRCZWZvcmVGaWxsZWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuX3B1c2hSaWdodCgoKSA9PiB7XG4gICAgICBpZiAodGhpcy5ibG9jay5pc0ZpeGVkIHx8ICF0aGlzLmJsb2NrLnZhbHVlKSByZXR1cm47XG4gICAgICB0aGlzLm9mZnNldCA9IHRoaXMuYmxvY2submVhcmVzdElucHV0UG9zKHRoaXMub2Zmc2V0LCBESVJFQ1RJT04uRk9SQ0VfUklHSFQpO1xuICAgICAgaWYgKHRoaXMub2Zmc2V0ICE9PSB0aGlzLmJsb2NrLnZhbHVlLmxlbmd0aCkgcmV0dXJuIHRydWU7XG4gICAgfSk7XG4gIH1cbiAgcHVzaFJpZ2h0QmVmb3JlSW5wdXQoKSB7XG4gICAgcmV0dXJuIHRoaXMuX3B1c2hSaWdodCgoKSA9PiB7XG4gICAgICBpZiAodGhpcy5ibG9jay5pc0ZpeGVkKSByZXR1cm47XG5cbiAgICAgIC8vIGNvbnN0IG8gPSB0aGlzLm9mZnNldDtcbiAgICAgIHRoaXMub2Zmc2V0ID0gdGhpcy5ibG9jay5uZWFyZXN0SW5wdXRQb3ModGhpcy5vZmZzZXQsIERJUkVDVElPTi5OT05FKTtcbiAgICAgIC8vIEhBQ0sgY2FzZXMgbGlrZSAoU1RJTEwgRE9FUyBOT1QgV09SSyBGT1IgTkVTVEVEKVxuICAgICAgLy8gYWF8WFxuICAgICAgLy8gYWE8WHxbXT5YXyAgICAtIHRoaXMgd2lsbCBub3Qgd29ya1xuICAgICAgLy8gaWYgKG8gJiYgbyA9PT0gdGhpcy5vZmZzZXQgJiYgdGhpcy5ibG9jayBpbnN0YW5jZW9mIFBhdHRlcm5JbnB1dERlZmluaXRpb24pIGNvbnRpbnVlO1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfSk7XG4gIH1cbiAgcHVzaFJpZ2h0QmVmb3JlUmVxdWlyZWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuX3B1c2hSaWdodCgoKSA9PiB7XG4gICAgICBpZiAodGhpcy5ibG9jay5pc0ZpeGVkIHx8IHRoaXMuYmxvY2suaXNPcHRpb25hbCAmJiAhdGhpcy5ibG9jay52YWx1ZSkgcmV0dXJuO1xuXG4gICAgICAvLyBUT0RPIGNoZWNrIHxbKl1YWF9cbiAgICAgIHRoaXMub2Zmc2V0ID0gdGhpcy5ibG9jay5uZWFyZXN0SW5wdXRQb3ModGhpcy5vZmZzZXQsIERJUkVDVElPTi5OT05FKTtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0pO1xuICB9XG59XG5cbmV4cG9ydCB7IFBhdHRlcm5DdXJzb3IgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBNYXNrZWQgZnJvbSAnLi9iYXNlLmpzJztcbmltcG9ydCBJTWFzayBmcm9tICcuLi9jb3JlL2hvbGRlci5qcyc7XG5pbXBvcnQgJy4uL2NvcmUvY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuLi9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzJztcbmltcG9ydCAnLi4vY29yZS91dGlscy5qcyc7XG5cbi8qKiBNYXNraW5nIGJ5IFJlZ0V4cCAqL1xuY2xhc3MgTWFza2VkUmVnRXhwIGV4dGVuZHMgTWFza2VkIHtcbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICAgQHBhcmFtIHtPYmplY3R9IG9wdHNcbiAgKi9cbiAgX3VwZGF0ZShvcHRzKSB7XG4gICAgaWYgKG9wdHMubWFzaykgb3B0cy52YWxpZGF0ZSA9IHZhbHVlID0+IHZhbHVlLnNlYXJjaChvcHRzLm1hc2spID49IDA7XG4gICAgc3VwZXIuX3VwZGF0ZShvcHRzKTtcbiAgfVxufVxuSU1hc2suTWFza2VkUmVnRXhwID0gTWFza2VkUmVnRXhwO1xuXG5leHBvcnQgeyBNYXNrZWRSZWdFeHAgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCB7IF8gYXMgX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2UgfSBmcm9tICcuLi9fcm9sbHVwUGx1Z2luQmFiZWxIZWxwZXJzLTZiM2JkNDA0LmpzJztcbmltcG9ydCB7IERJUkVDVElPTiB9IGZyb20gJy4uL2NvcmUvdXRpbHMuanMnO1xuaW1wb3J0IENoYW5nZURldGFpbHMgZnJvbSAnLi4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5pbXBvcnQgTWFza2VkIGZyb20gJy4vYmFzZS5qcyc7XG5pbXBvcnQgUGF0dGVybklucHV0RGVmaW5pdGlvbiwgeyBERUZBVUxUX0lOUFVUX0RFRklOSVRJT05TIH0gZnJvbSAnLi9wYXR0ZXJuL2lucHV0LWRlZmluaXRpb24uanMnO1xuaW1wb3J0IFBhdHRlcm5GaXhlZERlZmluaXRpb24gZnJvbSAnLi9wYXR0ZXJuL2ZpeGVkLWRlZmluaXRpb24uanMnO1xuaW1wb3J0IENodW5rc1RhaWxEZXRhaWxzIGZyb20gJy4vcGF0dGVybi9jaHVuay10YWlsLWRldGFpbHMuanMnO1xuaW1wb3J0IFBhdHRlcm5DdXJzb3IgZnJvbSAnLi9wYXR0ZXJuL2N1cnNvci5qcyc7XG5pbXBvcnQgY3JlYXRlTWFzayBmcm9tICcuL2ZhY3RvcnkuanMnO1xuaW1wb3J0IElNYXNrIGZyb20gJy4uL2NvcmUvaG9sZGVyLmpzJztcbmltcG9ydCAnLi9yZWdleHAuanMnO1xuaW1wb3J0ICcuLi9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzJztcblxuY29uc3QgX2V4Y2x1ZGVkID0gW1wiX2Jsb2Nrc1wiXTtcblxuLyoqXG4gIFBhdHRlcm4gbWFza1xuICBAcGFyYW0ge09iamVjdH0gb3B0c1xuICBAcGFyYW0ge09iamVjdH0gb3B0cy5ibG9ja3NcbiAgQHBhcmFtIHtPYmplY3R9IG9wdHMuZGVmaW5pdGlvbnNcbiAgQHBhcmFtIHtzdHJpbmd9IG9wdHMucGxhY2Vob2xkZXJDaGFyXG4gIEBwYXJhbSB7c3RyaW5nfSBvcHRzLmRpc3BsYXlDaGFyXG4gIEBwYXJhbSB7Ym9vbGVhbn0gb3B0cy5sYXp5XG4qL1xuY2xhc3MgTWFza2VkUGF0dGVybiBleHRlbmRzIE1hc2tlZCB7XG4gIC8qKiAqL1xuXG4gIC8qKiAqL1xuXG4gIC8qKiBTaW5nbGUgY2hhciBmb3IgZW1wdHkgaW5wdXQgKi9cblxuICAvKiogU2luZ2xlIGNoYXIgZm9yIGZpbGxlZCBpbnB1dCAqL1xuXG4gIC8qKiBTaG93IHBsYWNlaG9sZGVyIG9ubHkgd2hlbiBuZWVkZWQgKi9cblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICBsZXQgb3B0cyA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDoge307XG4gICAgLy8gVE9ETyB0eXBlICRTaGFwZTxNYXNrZWRQYXR0ZXJuT3B0aW9ucz49e30gZG9lcyBub3Qgd29ya1xuICAgIG9wdHMuZGVmaW5pdGlvbnMgPSBPYmplY3QuYXNzaWduKHt9LCBERUZBVUxUX0lOUFVUX0RFRklOSVRJT05TLCBvcHRzLmRlZmluaXRpb25zKTtcbiAgICBzdXBlcihPYmplY3QuYXNzaWduKHt9LCBNYXNrZWRQYXR0ZXJuLkRFRkFVTFRTLCBvcHRzKSk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgICBAcGFyYW0ge09iamVjdH0gb3B0c1xuICAqL1xuICBfdXBkYXRlKCkge1xuICAgIGxldCBvcHRzID0gYXJndW1lbnRzLmxlbmd0aCA+IDAgJiYgYXJndW1lbnRzWzBdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMF0gOiB7fTtcbiAgICBvcHRzLmRlZmluaXRpb25zID0gT2JqZWN0LmFzc2lnbih7fSwgdGhpcy5kZWZpbml0aW9ucywgb3B0cy5kZWZpbml0aW9ucyk7XG4gICAgc3VwZXIuX3VwZGF0ZShvcHRzKTtcbiAgICB0aGlzLl9yZWJ1aWxkTWFzaygpO1xuICB9XG5cbiAgLyoqICovXG4gIF9yZWJ1aWxkTWFzaygpIHtcbiAgICBjb25zdCBkZWZzID0gdGhpcy5kZWZpbml0aW9ucztcbiAgICB0aGlzLl9ibG9ja3MgPSBbXTtcbiAgICB0aGlzLl9zdG9wcyA9IFtdO1xuICAgIHRoaXMuX21hc2tlZEJsb2NrcyA9IHt9O1xuICAgIGxldCBwYXR0ZXJuID0gdGhpcy5tYXNrO1xuICAgIGlmICghcGF0dGVybiB8fCAhZGVmcykgcmV0dXJuO1xuICAgIGxldCB1bm1hc2tpbmdCbG9jayA9IGZhbHNlO1xuICAgIGxldCBvcHRpb25hbEJsb2NrID0gZmFsc2U7XG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBwYXR0ZXJuLmxlbmd0aDsgKytpKSB7XG4gICAgICB2YXIgX2RlZnMkY2hhciwgX2RlZnMkY2hhcjI7XG4gICAgICBpZiAodGhpcy5ibG9ja3MpIHtcbiAgICAgICAgY29uc3QgcCA9IHBhdHRlcm4uc2xpY2UoaSk7XG4gICAgICAgIGNvbnN0IGJOYW1lcyA9IE9iamVjdC5rZXlzKHRoaXMuYmxvY2tzKS5maWx0ZXIoYk5hbWUgPT4gcC5pbmRleE9mKGJOYW1lKSA9PT0gMCk7XG4gICAgICAgIC8vIG9yZGVyIGJ5IGtleSBsZW5ndGhcbiAgICAgICAgYk5hbWVzLnNvcnQoKGEsIGIpID0+IGIubGVuZ3RoIC0gYS5sZW5ndGgpO1xuICAgICAgICAvLyB1c2UgYmxvY2sgbmFtZSB3aXRoIG1heCBsZW5ndGhcbiAgICAgICAgY29uc3QgYk5hbWUgPSBiTmFtZXNbMF07XG4gICAgICAgIGlmIChiTmFtZSkge1xuICAgICAgICAgIC8vICRGbG93Rml4TWUgbm8gaWRlYXNcbiAgICAgICAgICBjb25zdCBtYXNrZWRCbG9jayA9IGNyZWF0ZU1hc2soT2JqZWN0LmFzc2lnbih7XG4gICAgICAgICAgICBwYXJlbnQ6IHRoaXMsXG4gICAgICAgICAgICBsYXp5OiB0aGlzLmxhenksXG4gICAgICAgICAgICBlYWdlcjogdGhpcy5lYWdlcixcbiAgICAgICAgICAgIHBsYWNlaG9sZGVyQ2hhcjogdGhpcy5wbGFjZWhvbGRlckNoYXIsXG4gICAgICAgICAgICBkaXNwbGF5Q2hhcjogdGhpcy5kaXNwbGF5Q2hhcixcbiAgICAgICAgICAgIG92ZXJ3cml0ZTogdGhpcy5vdmVyd3JpdGVcbiAgICAgICAgICB9LCB0aGlzLmJsb2Nrc1tiTmFtZV0pKTtcbiAgICAgICAgICBpZiAobWFza2VkQmxvY2spIHtcbiAgICAgICAgICAgIHRoaXMuX2Jsb2Nrcy5wdXNoKG1hc2tlZEJsb2NrKTtcblxuICAgICAgICAgICAgLy8gc3RvcmUgYmxvY2sgaW5kZXhcbiAgICAgICAgICAgIGlmICghdGhpcy5fbWFza2VkQmxvY2tzW2JOYW1lXSkgdGhpcy5fbWFza2VkQmxvY2tzW2JOYW1lXSA9IFtdO1xuICAgICAgICAgICAgdGhpcy5fbWFza2VkQmxvY2tzW2JOYW1lXS5wdXNoKHRoaXMuX2Jsb2Nrcy5sZW5ndGggLSAxKTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaSArPSBiTmFtZS5sZW5ndGggLSAxO1xuICAgICAgICAgIGNvbnRpbnVlO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBsZXQgY2hhciA9IHBhdHRlcm5baV07XG4gICAgICBsZXQgaXNJbnB1dCA9IChjaGFyIGluIGRlZnMpO1xuICAgICAgaWYgKGNoYXIgPT09IE1hc2tlZFBhdHRlcm4uU1RPUF9DSEFSKSB7XG4gICAgICAgIHRoaXMuX3N0b3BzLnB1c2godGhpcy5fYmxvY2tzLmxlbmd0aCk7XG4gICAgICAgIGNvbnRpbnVlO1xuICAgICAgfVxuICAgICAgaWYgKGNoYXIgPT09ICd7JyB8fCBjaGFyID09PSAnfScpIHtcbiAgICAgICAgdW5tYXNraW5nQmxvY2sgPSAhdW5tYXNraW5nQmxvY2s7XG4gICAgICAgIGNvbnRpbnVlO1xuICAgICAgfVxuICAgICAgaWYgKGNoYXIgPT09ICdbJyB8fCBjaGFyID09PSAnXScpIHtcbiAgICAgICAgb3B0aW9uYWxCbG9jayA9ICFvcHRpb25hbEJsb2NrO1xuICAgICAgICBjb250aW51ZTtcbiAgICAgIH1cbiAgICAgIGlmIChjaGFyID09PSBNYXNrZWRQYXR0ZXJuLkVTQ0FQRV9DSEFSKSB7XG4gICAgICAgICsraTtcbiAgICAgICAgY2hhciA9IHBhdHRlcm5baV07XG4gICAgICAgIGlmICghY2hhcikgYnJlYWs7XG4gICAgICAgIGlzSW5wdXQgPSBmYWxzZTtcbiAgICAgIH1cbiAgICAgIGNvbnN0IG1hc2tPcHRzID0gKF9kZWZzJGNoYXIgPSBkZWZzW2NoYXJdKSAhPT0gbnVsbCAmJiBfZGVmcyRjaGFyICE9PSB2b2lkIDAgJiYgX2RlZnMkY2hhci5tYXNrICYmICEoKChfZGVmcyRjaGFyMiA9IGRlZnNbY2hhcl0pID09PSBudWxsIHx8IF9kZWZzJGNoYXIyID09PSB2b2lkIDAgPyB2b2lkIDAgOiBfZGVmcyRjaGFyMi5tYXNrLnByb3RvdHlwZSkgaW5zdGFuY2VvZiBJTWFzay5NYXNrZWQpID8gZGVmc1tjaGFyXSA6IHtcbiAgICAgICAgbWFzazogZGVmc1tjaGFyXVxuICAgICAgfTtcbiAgICAgIGNvbnN0IGRlZiA9IGlzSW5wdXQgPyBuZXcgUGF0dGVybklucHV0RGVmaW5pdGlvbihPYmplY3QuYXNzaWduKHtcbiAgICAgICAgcGFyZW50OiB0aGlzLFxuICAgICAgICBpc09wdGlvbmFsOiBvcHRpb25hbEJsb2NrLFxuICAgICAgICBsYXp5OiB0aGlzLmxhenksXG4gICAgICAgIGVhZ2VyOiB0aGlzLmVhZ2VyLFxuICAgICAgICBwbGFjZWhvbGRlckNoYXI6IHRoaXMucGxhY2Vob2xkZXJDaGFyLFxuICAgICAgICBkaXNwbGF5Q2hhcjogdGhpcy5kaXNwbGF5Q2hhclxuICAgICAgfSwgbWFza09wdHMpKSA6IG5ldyBQYXR0ZXJuRml4ZWREZWZpbml0aW9uKHtcbiAgICAgICAgY2hhcixcbiAgICAgICAgZWFnZXI6IHRoaXMuZWFnZXIsXG4gICAgICAgIGlzVW5tYXNraW5nOiB1bm1hc2tpbmdCbG9ja1xuICAgICAgfSk7XG4gICAgICB0aGlzLl9ibG9ja3MucHVzaChkZWYpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBnZXQgc3RhdGUoKSB7XG4gICAgcmV0dXJuIE9iamVjdC5hc3NpZ24oe30sIHN1cGVyLnN0YXRlLCB7XG4gICAgICBfYmxvY2tzOiB0aGlzLl9ibG9ja3MubWFwKGIgPT4gYi5zdGF0ZSlcbiAgICB9KTtcbiAgfVxuICBzZXQgc3RhdGUoc3RhdGUpIHtcbiAgICBjb25zdCB7XG4gICAgICAgIF9ibG9ja3NcbiAgICAgIH0gPSBzdGF0ZSxcbiAgICAgIG1hc2tlZFN0YXRlID0gX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2Uoc3RhdGUsIF9leGNsdWRlZCk7XG4gICAgdGhpcy5fYmxvY2tzLmZvckVhY2goKGIsIGJpKSA9PiBiLnN0YXRlID0gX2Jsb2Nrc1tiaV0pO1xuICAgIHN1cGVyLnN0YXRlID0gbWFza2VkU3RhdGU7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgcmVzZXQoKSB7XG4gICAgc3VwZXIucmVzZXQoKTtcbiAgICB0aGlzLl9ibG9ja3MuZm9yRWFjaChiID0+IGIucmVzZXQoKSk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IGlzQ29tcGxldGUoKSB7XG4gICAgcmV0dXJuIHRoaXMuX2Jsb2Nrcy5ldmVyeShiID0+IGIuaXNDb21wbGV0ZSk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IGlzRmlsbGVkKCkge1xuICAgIHJldHVybiB0aGlzLl9ibG9ja3MuZXZlcnkoYiA9PiBiLmlzRmlsbGVkKTtcbiAgfVxuICBnZXQgaXNGaXhlZCgpIHtcbiAgICByZXR1cm4gdGhpcy5fYmxvY2tzLmV2ZXJ5KGIgPT4gYi5pc0ZpeGVkKTtcbiAgfVxuICBnZXQgaXNPcHRpb25hbCgpIHtcbiAgICByZXR1cm4gdGhpcy5fYmxvY2tzLmV2ZXJ5KGIgPT4gYi5pc09wdGlvbmFsKTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBkb0NvbW1pdCgpIHtcbiAgICB0aGlzLl9ibG9ja3MuZm9yRWFjaChiID0+IGIuZG9Db21taXQoKSk7XG4gICAgc3VwZXIuZG9Db21taXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBnZXQgdW5tYXNrZWRWYWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5fYmxvY2tzLnJlZHVjZSgoc3RyLCBiKSA9PiBzdHIgKz0gYi51bm1hc2tlZFZhbHVlLCAnJyk7XG4gIH1cbiAgc2V0IHVubWFza2VkVmFsdWUodW5tYXNrZWRWYWx1ZSkge1xuICAgIHN1cGVyLnVubWFza2VkVmFsdWUgPSB1bm1hc2tlZFZhbHVlO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCB2YWx1ZSgpIHtcbiAgICAvLyBUT0RPIHJldHVybiBfdmFsdWUgd2hlbiBub3QgaW4gY2hhbmdlP1xuICAgIHJldHVybiB0aGlzLl9ibG9ja3MucmVkdWNlKChzdHIsIGIpID0+IHN0ciArPSBiLnZhbHVlLCAnJyk7XG4gIH1cbiAgc2V0IHZhbHVlKHZhbHVlKSB7XG4gICAgc3VwZXIudmFsdWUgPSB2YWx1ZTtcbiAgfVxuICBnZXQgZGlzcGxheVZhbHVlKCkge1xuICAgIHJldHVybiB0aGlzLl9ibG9ja3MucmVkdWNlKChzdHIsIGIpID0+IHN0ciArPSBiLmRpc3BsYXlWYWx1ZSwgJycpO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGFwcGVuZFRhaWwodGFpbCkge1xuICAgIHJldHVybiBzdXBlci5hcHBlbmRUYWlsKHRhaWwpLmFnZ3JlZ2F0ZSh0aGlzLl9hcHBlbmRQbGFjZWhvbGRlcigpKTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBfYXBwZW5kRWFnZXIoKSB7XG4gICAgdmFyIF90aGlzJF9tYXBQb3NUb0Jsb2NrO1xuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgIGxldCBzdGFydEJsb2NrSW5kZXggPSAoX3RoaXMkX21hcFBvc1RvQmxvY2sgPSB0aGlzLl9tYXBQb3NUb0Jsb2NrKHRoaXMudmFsdWUubGVuZ3RoKSkgPT09IG51bGwgfHwgX3RoaXMkX21hcFBvc1RvQmxvY2sgPT09IHZvaWQgMCA/IHZvaWQgMCA6IF90aGlzJF9tYXBQb3NUb0Jsb2NrLmluZGV4O1xuICAgIGlmIChzdGFydEJsb2NrSW5kZXggPT0gbnVsbCkgcmV0dXJuIGRldGFpbHM7XG5cbiAgICAvLyBUT0RPIHRlc3QgaWYgaXQgd29ya3MgZm9yIG5lc3RlZCBwYXR0ZXJuIG1hc2tzXG4gICAgaWYgKHRoaXMuX2Jsb2Nrc1tzdGFydEJsb2NrSW5kZXhdLmlzRmlsbGVkKSArK3N0YXJ0QmxvY2tJbmRleDtcbiAgICBmb3IgKGxldCBiaSA9IHN0YXJ0QmxvY2tJbmRleDsgYmkgPCB0aGlzLl9ibG9ja3MubGVuZ3RoOyArK2JpKSB7XG4gICAgICBjb25zdCBkID0gdGhpcy5fYmxvY2tzW2JpXS5fYXBwZW5kRWFnZXIoKTtcbiAgICAgIGlmICghZC5pbnNlcnRlZCkgYnJlYWs7XG4gICAgICBkZXRhaWxzLmFnZ3JlZ2F0ZShkKTtcbiAgICB9XG4gICAgcmV0dXJuIGRldGFpbHM7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgX2FwcGVuZENoYXJSYXcoY2gpIHtcbiAgICBsZXQgZmxhZ3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHt9O1xuICAgIGNvbnN0IGJsb2NrSXRlciA9IHRoaXMuX21hcFBvc1RvQmxvY2sodGhpcy52YWx1ZS5sZW5ndGgpO1xuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgIGlmICghYmxvY2tJdGVyKSByZXR1cm4gZGV0YWlscztcbiAgICBmb3IgKGxldCBiaSA9IGJsb2NrSXRlci5pbmRleDs7ICsrYmkpIHtcbiAgICAgIHZhciBfZmxhZ3MkX2JlZm9yZVRhaWxTdGEsIF9mbGFncyRfYmVmb3JlVGFpbFN0YTI7XG4gICAgICBjb25zdCBibG9jayA9IHRoaXMuX2Jsb2Nrc1tiaV07XG4gICAgICBpZiAoIWJsb2NrKSBicmVhaztcbiAgICAgIGNvbnN0IGJsb2NrRGV0YWlscyA9IGJsb2NrLl9hcHBlbmRDaGFyKGNoLCBPYmplY3QuYXNzaWduKHt9LCBmbGFncywge1xuICAgICAgICBfYmVmb3JlVGFpbFN0YXRlOiAoX2ZsYWdzJF9iZWZvcmVUYWlsU3RhID0gZmxhZ3MuX2JlZm9yZVRhaWxTdGF0ZSkgPT09IG51bGwgfHwgX2ZsYWdzJF9iZWZvcmVUYWlsU3RhID09PSB2b2lkIDAgPyB2b2lkIDAgOiAoX2ZsYWdzJF9iZWZvcmVUYWlsU3RhMiA9IF9mbGFncyRfYmVmb3JlVGFpbFN0YS5fYmxvY2tzKSA9PT0gbnVsbCB8fCBfZmxhZ3MkX2JlZm9yZVRhaWxTdGEyID09PSB2b2lkIDAgPyB2b2lkIDAgOiBfZmxhZ3MkX2JlZm9yZVRhaWxTdGEyW2JpXVxuICAgICAgfSkpO1xuICAgICAgY29uc3Qgc2tpcCA9IGJsb2NrRGV0YWlscy5za2lwO1xuICAgICAgZGV0YWlscy5hZ2dyZWdhdGUoYmxvY2tEZXRhaWxzKTtcbiAgICAgIGlmIChza2lwIHx8IGJsb2NrRGV0YWlscy5yYXdJbnNlcnRlZCkgYnJlYWs7IC8vIGdvIG5leHQgY2hhclxuICAgIH1cblxuICAgIHJldHVybiBkZXRhaWxzO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGV4dHJhY3RUYWlsKCkge1xuICAgIGxldCBmcm9tUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDAgJiYgYXJndW1lbnRzWzBdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMF0gOiAwO1xuICAgIGxldCB0b1BvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogdGhpcy52YWx1ZS5sZW5ndGg7XG4gICAgY29uc3QgY2h1bmtUYWlsID0gbmV3IENodW5rc1RhaWxEZXRhaWxzKCk7XG4gICAgaWYgKGZyb21Qb3MgPT09IHRvUG9zKSByZXR1cm4gY2h1bmtUYWlsO1xuICAgIHRoaXMuX2ZvckVhY2hCbG9ja3NJblJhbmdlKGZyb21Qb3MsIHRvUG9zLCAoYiwgYmksIGJGcm9tUG9zLCBiVG9Qb3MpID0+IHtcbiAgICAgIGNvbnN0IGJsb2NrQ2h1bmsgPSBiLmV4dHJhY3RUYWlsKGJGcm9tUG9zLCBiVG9Qb3MpO1xuICAgICAgYmxvY2tDaHVuay5zdG9wID0gdGhpcy5fZmluZFN0b3BCZWZvcmUoYmkpO1xuICAgICAgYmxvY2tDaHVuay5mcm9tID0gdGhpcy5fYmxvY2tTdGFydFBvcyhiaSk7XG4gICAgICBpZiAoYmxvY2tDaHVuayBpbnN0YW5jZW9mIENodW5rc1RhaWxEZXRhaWxzKSBibG9ja0NodW5rLmJsb2NrSW5kZXggPSBiaTtcbiAgICAgIGNodW5rVGFpbC5leHRlbmQoYmxvY2tDaHVuayk7XG4gICAgfSk7XG4gICAgcmV0dXJuIGNodW5rVGFpbDtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBleHRyYWN0SW5wdXQoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICBsZXQgZmxhZ3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMiAmJiBhcmd1bWVudHNbMl0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1syXSA6IHt9O1xuICAgIGlmIChmcm9tUG9zID09PSB0b1BvcykgcmV0dXJuICcnO1xuICAgIGxldCBpbnB1dCA9ICcnO1xuICAgIHRoaXMuX2ZvckVhY2hCbG9ja3NJblJhbmdlKGZyb21Qb3MsIHRvUG9zLCAoYiwgXywgZnJvbVBvcywgdG9Qb3MpID0+IHtcbiAgICAgIGlucHV0ICs9IGIuZXh0cmFjdElucHV0KGZyb21Qb3MsIHRvUG9zLCBmbGFncyk7XG4gICAgfSk7XG4gICAgcmV0dXJuIGlucHV0O1xuICB9XG4gIF9maW5kU3RvcEJlZm9yZShibG9ja0luZGV4KSB7XG4gICAgbGV0IHN0b3BCZWZvcmU7XG4gICAgZm9yIChsZXQgc2kgPSAwOyBzaSA8IHRoaXMuX3N0b3BzLmxlbmd0aDsgKytzaSkge1xuICAgICAgY29uc3Qgc3RvcCA9IHRoaXMuX3N0b3BzW3NpXTtcbiAgICAgIGlmIChzdG9wIDw9IGJsb2NrSW5kZXgpIHN0b3BCZWZvcmUgPSBzdG9wO2Vsc2UgYnJlYWs7XG4gICAgfVxuICAgIHJldHVybiBzdG9wQmVmb3JlO1xuICB9XG5cbiAgLyoqIEFwcGVuZHMgcGxhY2Vob2xkZXIgZGVwZW5kaW5nIG9uIGxhemluZXNzICovXG4gIF9hcHBlbmRQbGFjZWhvbGRlcih0b0Jsb2NrSW5kZXgpIHtcbiAgICBjb25zdCBkZXRhaWxzID0gbmV3IENoYW5nZURldGFpbHMoKTtcbiAgICBpZiAodGhpcy5sYXp5ICYmIHRvQmxvY2tJbmRleCA9PSBudWxsKSByZXR1cm4gZGV0YWlscztcbiAgICBjb25zdCBzdGFydEJsb2NrSXRlciA9IHRoaXMuX21hcFBvc1RvQmxvY2sodGhpcy52YWx1ZS5sZW5ndGgpO1xuICAgIGlmICghc3RhcnRCbG9ja0l0ZXIpIHJldHVybiBkZXRhaWxzO1xuICAgIGNvbnN0IHN0YXJ0QmxvY2tJbmRleCA9IHN0YXJ0QmxvY2tJdGVyLmluZGV4O1xuICAgIGNvbnN0IGVuZEJsb2NrSW5kZXggPSB0b0Jsb2NrSW5kZXggIT0gbnVsbCA/IHRvQmxvY2tJbmRleCA6IHRoaXMuX2Jsb2Nrcy5sZW5ndGg7XG4gICAgdGhpcy5fYmxvY2tzLnNsaWNlKHN0YXJ0QmxvY2tJbmRleCwgZW5kQmxvY2tJbmRleCkuZm9yRWFjaChiID0+IHtcbiAgICAgIGlmICghYi5sYXp5IHx8IHRvQmxvY2tJbmRleCAhPSBudWxsKSB7XG4gICAgICAgIC8vICRGbG93Rml4TWUgYF9ibG9ja3NgIG1heSBub3QgYmUgcHJlc2VudFxuICAgICAgICBjb25zdCBhcmdzID0gYi5fYmxvY2tzICE9IG51bGwgPyBbYi5fYmxvY2tzLmxlbmd0aF0gOiBbXTtcbiAgICAgICAgY29uc3QgYkRldGFpbHMgPSBiLl9hcHBlbmRQbGFjZWhvbGRlciguLi5hcmdzKTtcbiAgICAgICAgdGhpcy5fdmFsdWUgKz0gYkRldGFpbHMuaW5zZXJ0ZWQ7XG4gICAgICAgIGRldGFpbHMuYWdncmVnYXRlKGJEZXRhaWxzKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgICByZXR1cm4gZGV0YWlscztcbiAgfVxuXG4gIC8qKiBGaW5kcyBibG9jayBpbiBwb3MgKi9cbiAgX21hcFBvc1RvQmxvY2socG9zKSB7XG4gICAgbGV0IGFjY1ZhbCA9ICcnO1xuICAgIGZvciAobGV0IGJpID0gMDsgYmkgPCB0aGlzLl9ibG9ja3MubGVuZ3RoOyArK2JpKSB7XG4gICAgICBjb25zdCBibG9jayA9IHRoaXMuX2Jsb2Nrc1tiaV07XG4gICAgICBjb25zdCBibG9ja1N0YXJ0UG9zID0gYWNjVmFsLmxlbmd0aDtcbiAgICAgIGFjY1ZhbCArPSBibG9jay52YWx1ZTtcbiAgICAgIGlmIChwb3MgPD0gYWNjVmFsLmxlbmd0aCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIGluZGV4OiBiaSxcbiAgICAgICAgICBvZmZzZXQ6IHBvcyAtIGJsb2NrU3RhcnRQb3NcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICAvKiogKi9cbiAgX2Jsb2NrU3RhcnRQb3MoYmxvY2tJbmRleCkge1xuICAgIHJldHVybiB0aGlzLl9ibG9ja3Muc2xpY2UoMCwgYmxvY2tJbmRleCkucmVkdWNlKChwb3MsIGIpID0+IHBvcyArPSBiLnZhbHVlLmxlbmd0aCwgMCk7XG4gIH1cblxuICAvKiogKi9cbiAgX2ZvckVhY2hCbG9ja3NJblJhbmdlKGZyb21Qb3MpIHtcbiAgICBsZXQgdG9Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHRoaXMudmFsdWUubGVuZ3RoO1xuICAgIGxldCBmbiA9IGFyZ3VtZW50cy5sZW5ndGggPiAyID8gYXJndW1lbnRzWzJdIDogdW5kZWZpbmVkO1xuICAgIGNvbnN0IGZyb21CbG9ja0l0ZXIgPSB0aGlzLl9tYXBQb3NUb0Jsb2NrKGZyb21Qb3MpO1xuICAgIGlmIChmcm9tQmxvY2tJdGVyKSB7XG4gICAgICBjb25zdCB0b0Jsb2NrSXRlciA9IHRoaXMuX21hcFBvc1RvQmxvY2sodG9Qb3MpO1xuICAgICAgLy8gcHJvY2VzcyBmaXJzdCBibG9ja1xuICAgICAgY29uc3QgaXNTYW1lQmxvY2sgPSB0b0Jsb2NrSXRlciAmJiBmcm9tQmxvY2tJdGVyLmluZGV4ID09PSB0b0Jsb2NrSXRlci5pbmRleDtcbiAgICAgIGNvbnN0IGZyb21CbG9ja1N0YXJ0UG9zID0gZnJvbUJsb2NrSXRlci5vZmZzZXQ7XG4gICAgICBjb25zdCBmcm9tQmxvY2tFbmRQb3MgPSB0b0Jsb2NrSXRlciAmJiBpc1NhbWVCbG9jayA/IHRvQmxvY2tJdGVyLm9mZnNldCA6IHRoaXMuX2Jsb2Nrc1tmcm9tQmxvY2tJdGVyLmluZGV4XS52YWx1ZS5sZW5ndGg7XG4gICAgICBmbih0aGlzLl9ibG9ja3NbZnJvbUJsb2NrSXRlci5pbmRleF0sIGZyb21CbG9ja0l0ZXIuaW5kZXgsIGZyb21CbG9ja1N0YXJ0UG9zLCBmcm9tQmxvY2tFbmRQb3MpO1xuICAgICAgaWYgKHRvQmxvY2tJdGVyICYmICFpc1NhbWVCbG9jaykge1xuICAgICAgICAvLyBwcm9jZXNzIGludGVybWVkaWF0ZSBibG9ja3NcbiAgICAgICAgZm9yIChsZXQgYmkgPSBmcm9tQmxvY2tJdGVyLmluZGV4ICsgMTsgYmkgPCB0b0Jsb2NrSXRlci5pbmRleDsgKytiaSkge1xuICAgICAgICAgIGZuKHRoaXMuX2Jsb2Nrc1tiaV0sIGJpLCAwLCB0aGlzLl9ibG9ja3NbYmldLnZhbHVlLmxlbmd0aCk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBwcm9jZXNzIGxhc3QgYmxvY2tcbiAgICAgICAgZm4odGhpcy5fYmxvY2tzW3RvQmxvY2tJdGVyLmluZGV4XSwgdG9CbG9ja0l0ZXIuaW5kZXgsIDAsIHRvQmxvY2tJdGVyLm9mZnNldCk7XG4gICAgICB9XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIHJlbW92ZSgpIHtcbiAgICBsZXQgZnJvbVBvcyA9IGFyZ3VtZW50cy5sZW5ndGggPiAwICYmIGFyZ3VtZW50c1swXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzBdIDogMDtcbiAgICBsZXQgdG9Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHRoaXMudmFsdWUubGVuZ3RoO1xuICAgIGNvbnN0IHJlbW92ZURldGFpbHMgPSBzdXBlci5yZW1vdmUoZnJvbVBvcywgdG9Qb3MpO1xuICAgIHRoaXMuX2ZvckVhY2hCbG9ja3NJblJhbmdlKGZyb21Qb3MsIHRvUG9zLCAoYiwgXywgYkZyb21Qb3MsIGJUb1BvcykgPT4ge1xuICAgICAgcmVtb3ZlRGV0YWlscy5hZ2dyZWdhdGUoYi5yZW1vdmUoYkZyb21Qb3MsIGJUb1BvcykpO1xuICAgIH0pO1xuICAgIHJldHVybiByZW1vdmVEZXRhaWxzO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIG5lYXJlc3RJbnB1dFBvcyhjdXJzb3JQb3MpIHtcbiAgICBsZXQgZGlyZWN0aW9uID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiBESVJFQ1RJT04uTk9ORTtcbiAgICBpZiAoIXRoaXMuX2Jsb2Nrcy5sZW5ndGgpIHJldHVybiAwO1xuICAgIGNvbnN0IGN1cnNvciA9IG5ldyBQYXR0ZXJuQ3Vyc29yKHRoaXMsIGN1cnNvclBvcyk7XG4gICAgaWYgKGRpcmVjdGlvbiA9PT0gRElSRUNUSU9OLk5PTkUpIHtcbiAgICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAgIC8vIE5PTkUgc2hvdWxkIG9ubHkgZ28gb3V0IGZyb20gZml4ZWQgdG8gdGhlIHJpZ2h0IVxuICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgICAgaWYgKGN1cnNvci5wdXNoUmlnaHRCZWZvcmVJbnB1dCgpKSByZXR1cm4gY3Vyc29yLnBvcztcbiAgICAgIGN1cnNvci5wb3BTdGF0ZSgpO1xuICAgICAgaWYgKGN1cnNvci5wdXNoTGVmdEJlZm9yZUlucHV0KCkpIHJldHVybiBjdXJzb3IucG9zO1xuICAgICAgcmV0dXJuIHRoaXMudmFsdWUubGVuZ3RoO1xuICAgIH1cblxuICAgIC8vIEZPUkNFIGlzIG9ubHkgYWJvdXQgYXwqIG90aGVyd2lzZSBpcyAwXG4gICAgaWYgKGRpcmVjdGlvbiA9PT0gRElSRUNUSU9OLkxFRlQgfHwgZGlyZWN0aW9uID09PSBESVJFQ1RJT04uRk9SQ0VfTEVGVCkge1xuICAgICAgLy8gdHJ5IHRvIGJyZWFrIGZhc3Qgd2hlbiAqfGFcbiAgICAgIGlmIChkaXJlY3Rpb24gPT09IERJUkVDVElPTi5MRUZUKSB7XG4gICAgICAgIGN1cnNvci5wdXNoUmlnaHRCZWZvcmVGaWxsZWQoKTtcbiAgICAgICAgaWYgKGN1cnNvci5vayAmJiBjdXJzb3IucG9zID09PSBjdXJzb3JQb3MpIHJldHVybiBjdXJzb3JQb3M7XG4gICAgICAgIGN1cnNvci5wb3BTdGF0ZSgpO1xuICAgICAgfVxuXG4gICAgICAvLyBmb3J3YXJkIGZsb3dcbiAgICAgIGN1cnNvci5wdXNoTGVmdEJlZm9yZUlucHV0KCk7XG4gICAgICBjdXJzb3IucHVzaExlZnRCZWZvcmVSZXF1aXJlZCgpO1xuICAgICAgY3Vyc29yLnB1c2hMZWZ0QmVmb3JlRmlsbGVkKCk7XG5cbiAgICAgIC8vIGJhY2t3YXJkIGZsb3dcbiAgICAgIGlmIChkaXJlY3Rpb24gPT09IERJUkVDVElPTi5MRUZUKSB7XG4gICAgICAgIGN1cnNvci5wdXNoUmlnaHRCZWZvcmVJbnB1dCgpO1xuICAgICAgICBjdXJzb3IucHVzaFJpZ2h0QmVmb3JlUmVxdWlyZWQoKTtcbiAgICAgICAgaWYgKGN1cnNvci5vayAmJiBjdXJzb3IucG9zIDw9IGN1cnNvclBvcykgcmV0dXJuIGN1cnNvci5wb3M7XG4gICAgICAgIGN1cnNvci5wb3BTdGF0ZSgpO1xuICAgICAgICBpZiAoY3Vyc29yLm9rICYmIGN1cnNvci5wb3MgPD0gY3Vyc29yUG9zKSByZXR1cm4gY3Vyc29yLnBvcztcbiAgICAgICAgY3Vyc29yLnBvcFN0YXRlKCk7XG4gICAgICB9XG4gICAgICBpZiAoY3Vyc29yLm9rKSByZXR1cm4gY3Vyc29yLnBvcztcbiAgICAgIGlmIChkaXJlY3Rpb24gPT09IERJUkVDVElPTi5GT1JDRV9MRUZUKSByZXR1cm4gMDtcbiAgICAgIGN1cnNvci5wb3BTdGF0ZSgpO1xuICAgICAgaWYgKGN1cnNvci5vaykgcmV0dXJuIGN1cnNvci5wb3M7XG4gICAgICBjdXJzb3IucG9wU3RhdGUoKTtcbiAgICAgIGlmIChjdXJzb3Iub2spIHJldHVybiBjdXJzb3IucG9zO1xuXG4gICAgICAvLyBjdXJzb3IucG9wU3RhdGUoKTtcbiAgICAgIC8vIGlmIChcbiAgICAgIC8vICAgY3Vyc29yLnB1c2hSaWdodEJlZm9yZUlucHV0KCkgJiZcbiAgICAgIC8vICAgLy8gVE9ETyBIQUNLIGZvciBsYXp5IGlmIGhhcyBhbGlnbmVkIGxlZnQgaW5zaWRlIGZpeGVkIGFuZCBoYXMgY2FtZSB0byB0aGUgc3RhcnQgLSB1c2Ugc3RhcnQgcG9zaXRpb25cbiAgICAgIC8vICAgKCF0aGlzLmxhenkgfHwgdGhpcy5leHRyYWN0SW5wdXQoKSlcbiAgICAgIC8vICkgcmV0dXJuIGN1cnNvci5wb3M7XG5cbiAgICAgIHJldHVybiAwO1xuICAgIH1cbiAgICBpZiAoZGlyZWN0aW9uID09PSBESVJFQ1RJT04uUklHSFQgfHwgZGlyZWN0aW9uID09PSBESVJFQ1RJT04uRk9SQ0VfUklHSFQpIHtcbiAgICAgIC8vIGZvcndhcmQgZmxvd1xuICAgICAgY3Vyc29yLnB1c2hSaWdodEJlZm9yZUlucHV0KCk7XG4gICAgICBjdXJzb3IucHVzaFJpZ2h0QmVmb3JlUmVxdWlyZWQoKTtcbiAgICAgIGlmIChjdXJzb3IucHVzaFJpZ2h0QmVmb3JlRmlsbGVkKCkpIHJldHVybiBjdXJzb3IucG9zO1xuICAgICAgaWYgKGRpcmVjdGlvbiA9PT0gRElSRUNUSU9OLkZPUkNFX1JJR0hUKSByZXR1cm4gdGhpcy52YWx1ZS5sZW5ndGg7XG5cbiAgICAgIC8vIGJhY2t3YXJkIGZsb3dcbiAgICAgIGN1cnNvci5wb3BTdGF0ZSgpO1xuICAgICAgaWYgKGN1cnNvci5vaykgcmV0dXJuIGN1cnNvci5wb3M7XG4gICAgICBjdXJzb3IucG9wU3RhdGUoKTtcbiAgICAgIGlmIChjdXJzb3Iub2spIHJldHVybiBjdXJzb3IucG9zO1xuICAgICAgcmV0dXJuIHRoaXMubmVhcmVzdElucHV0UG9zKGN1cnNvclBvcywgRElSRUNUSU9OLkxFRlQpO1xuICAgIH1cbiAgICByZXR1cm4gY3Vyc29yUG9zO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIHRvdGFsSW5wdXRQb3NpdGlvbnMoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICBsZXQgdG90YWwgPSAwO1xuICAgIHRoaXMuX2ZvckVhY2hCbG9ja3NJblJhbmdlKGZyb21Qb3MsIHRvUG9zLCAoYiwgXywgYkZyb21Qb3MsIGJUb1BvcykgPT4ge1xuICAgICAgdG90YWwgKz0gYi50b3RhbElucHV0UG9zaXRpb25zKGJGcm9tUG9zLCBiVG9Qb3MpO1xuICAgIH0pO1xuICAgIHJldHVybiB0b3RhbDtcbiAgfVxuXG4gIC8qKiBHZXQgYmxvY2sgYnkgbmFtZSAqL1xuICBtYXNrZWRCbG9jayhuYW1lKSB7XG4gICAgcmV0dXJuIHRoaXMubWFza2VkQmxvY2tzKG5hbWUpWzBdO1xuICB9XG5cbiAgLyoqIEdldCBhbGwgYmxvY2tzIGJ5IG5hbWUgKi9cbiAgbWFza2VkQmxvY2tzKG5hbWUpIHtcbiAgICBjb25zdCBpbmRpY2VzID0gdGhpcy5fbWFza2VkQmxvY2tzW25hbWVdO1xuICAgIGlmICghaW5kaWNlcykgcmV0dXJuIFtdO1xuICAgIHJldHVybiBpbmRpY2VzLm1hcChnaSA9PiB0aGlzLl9ibG9ja3NbZ2ldKTtcbiAgfVxufVxuTWFza2VkUGF0dGVybi5ERUZBVUxUUyA9IHtcbiAgbGF6eTogdHJ1ZSxcbiAgcGxhY2Vob2xkZXJDaGFyOiAnXydcbn07XG5NYXNrZWRQYXR0ZXJuLlNUT1BfQ0hBUiA9ICdgJztcbk1hc2tlZFBhdHRlcm4uRVNDQVBFX0NIQVIgPSAnXFxcXCc7XG5NYXNrZWRQYXR0ZXJuLklucHV0RGVmaW5pdGlvbiA9IFBhdHRlcm5JbnB1dERlZmluaXRpb247XG5NYXNrZWRQYXR0ZXJuLkZpeGVkRGVmaW5pdGlvbiA9IFBhdHRlcm5GaXhlZERlZmluaXRpb247XG5JTWFzay5NYXNrZWRQYXR0ZXJuID0gTWFza2VkUGF0dGVybjtcblxuZXhwb3J0IHsgTWFza2VkUGF0dGVybiBhcyBkZWZhdWx0IH07XG4iLCAiaW1wb3J0IE1hc2tlZFBhdHRlcm4gZnJvbSAnLi9wYXR0ZXJuLmpzJztcbmltcG9ydCAnLi4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5pbXBvcnQgeyBub3JtYWxpemVQcmVwYXJlIH0gZnJvbSAnLi4vY29yZS91dGlscy5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuaW1wb3J0ICcuLi9fcm9sbHVwUGx1Z2luQmFiZWxIZWxwZXJzLTZiM2JkNDA0LmpzJztcbmltcG9ydCAnLi9iYXNlLmpzJztcbmltcG9ydCAnLi4vY29yZS9jb250aW51b3VzLXRhaWwtZGV0YWlscy5qcyc7XG5pbXBvcnQgJy4vcGF0dGVybi9pbnB1dC1kZWZpbml0aW9uLmpzJztcbmltcG9ydCAnLi9mYWN0b3J5LmpzJztcbmltcG9ydCAnLi9wYXR0ZXJuL2ZpeGVkLWRlZmluaXRpb24uanMnO1xuaW1wb3J0ICcuL3BhdHRlcm4vY2h1bmstdGFpbC1kZXRhaWxzLmpzJztcbmltcG9ydCAnLi9wYXR0ZXJuL2N1cnNvci5qcyc7XG5pbXBvcnQgJy4vcmVnZXhwLmpzJztcblxuLyoqIFBhdHRlcm4gd2hpY2ggYWNjZXB0cyByYW5nZXMgKi9cbmNsYXNzIE1hc2tlZFJhbmdlIGV4dGVuZHMgTWFza2VkUGF0dGVybiB7XG4gIC8qKlxuICAgIE9wdGlvbmFsbHkgc2V0cyBtYXggbGVuZ3RoIG9mIHBhdHRlcm4uXG4gICAgVXNlZCB3aGVuIHBhdHRlcm4gbGVuZ3RoIGlzIGxvbmdlciB0aGVuIGB0b2AgcGFyYW0gbGVuZ3RoLiBQYWRzIHplcm9zIGF0IHN0YXJ0IGluIHRoaXMgY2FzZS5cbiAgKi9cblxuICAvKiogTWluIGJvdW5kICovXG5cbiAgLyoqIE1heCBib3VuZCAqL1xuXG4gIC8qKiAqL1xuXG4gIGdldCBfbWF0Y2hGcm9tKCkge1xuICAgIHJldHVybiB0aGlzLm1heExlbmd0aCAtIFN0cmluZyh0aGlzLmZyb20pLmxlbmd0aDtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBfdXBkYXRlKG9wdHMpIHtcbiAgICAvLyBUT0RPIHR5cGVcbiAgICBvcHRzID0gT2JqZWN0LmFzc2lnbih7XG4gICAgICB0bzogdGhpcy50byB8fCAwLFxuICAgICAgZnJvbTogdGhpcy5mcm9tIHx8IDAsXG4gICAgICBtYXhMZW5ndGg6IHRoaXMubWF4TGVuZ3RoIHx8IDBcbiAgICB9LCBvcHRzKTtcbiAgICBsZXQgbWF4TGVuZ3RoID0gU3RyaW5nKG9wdHMudG8pLmxlbmd0aDtcbiAgICBpZiAob3B0cy5tYXhMZW5ndGggIT0gbnVsbCkgbWF4TGVuZ3RoID0gTWF0aC5tYXgobWF4TGVuZ3RoLCBvcHRzLm1heExlbmd0aCk7XG4gICAgb3B0cy5tYXhMZW5ndGggPSBtYXhMZW5ndGg7XG4gICAgY29uc3QgZnJvbVN0ciA9IFN0cmluZyhvcHRzLmZyb20pLnBhZFN0YXJ0KG1heExlbmd0aCwgJzAnKTtcbiAgICBjb25zdCB0b1N0ciA9IFN0cmluZyhvcHRzLnRvKS5wYWRTdGFydChtYXhMZW5ndGgsICcwJyk7XG4gICAgbGV0IHNhbWVDaGFyc0NvdW50ID0gMDtcbiAgICB3aGlsZSAoc2FtZUNoYXJzQ291bnQgPCB0b1N0ci5sZW5ndGggJiYgdG9TdHJbc2FtZUNoYXJzQ291bnRdID09PSBmcm9tU3RyW3NhbWVDaGFyc0NvdW50XSkgKytzYW1lQ2hhcnNDb3VudDtcbiAgICBvcHRzLm1hc2sgPSB0b1N0ci5zbGljZSgwLCBzYW1lQ2hhcnNDb3VudCkucmVwbGFjZSgvMC9nLCAnXFxcXDAnKSArICcwJy5yZXBlYXQobWF4TGVuZ3RoIC0gc2FtZUNoYXJzQ291bnQpO1xuICAgIHN1cGVyLl91cGRhdGUob3B0cyk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IGlzQ29tcGxldGUoKSB7XG4gICAgcmV0dXJuIHN1cGVyLmlzQ29tcGxldGUgJiYgQm9vbGVhbih0aGlzLnZhbHVlKTtcbiAgfVxuICBib3VuZGFyaWVzKHN0cikge1xuICAgIGxldCBtaW5zdHIgPSAnJztcbiAgICBsZXQgbWF4c3RyID0gJyc7XG4gICAgY29uc3QgWywgcGxhY2Vob2xkZXIsIG51bV0gPSBzdHIubWF0Y2goL14oXFxEKikoXFxkKikoXFxEKikvKSB8fCBbXTtcbiAgICBpZiAobnVtKSB7XG4gICAgICBtaW5zdHIgPSAnMCcucmVwZWF0KHBsYWNlaG9sZGVyLmxlbmd0aCkgKyBudW07XG4gICAgICBtYXhzdHIgPSAnOScucmVwZWF0KHBsYWNlaG9sZGVyLmxlbmd0aCkgKyBudW07XG4gICAgfVxuICAgIG1pbnN0ciA9IG1pbnN0ci5wYWRFbmQodGhpcy5tYXhMZW5ndGgsICcwJyk7XG4gICAgbWF4c3RyID0gbWF4c3RyLnBhZEVuZCh0aGlzLm1heExlbmd0aCwgJzknKTtcbiAgICByZXR1cm4gW21pbnN0ciwgbWF4c3RyXTtcbiAgfVxuXG4gIC8vIFRPRE8gc3RyIGlzIGEgc2luZ2xlIGNoYXIgZXZlcnl0aW1lXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBkb1ByZXBhcmUoY2gpIHtcbiAgICBsZXQgZmxhZ3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHt9O1xuICAgIGxldCBkZXRhaWxzO1xuICAgIFtjaCwgZGV0YWlsc10gPSBub3JtYWxpemVQcmVwYXJlKHN1cGVyLmRvUHJlcGFyZShjaC5yZXBsYWNlKC9cXEQvZywgJycpLCBmbGFncykpO1xuICAgIGlmICghdGhpcy5hdXRvZml4IHx8ICFjaCkgcmV0dXJuIGNoO1xuICAgIGNvbnN0IGZyb21TdHIgPSBTdHJpbmcodGhpcy5mcm9tKS5wYWRTdGFydCh0aGlzLm1heExlbmd0aCwgJzAnKTtcbiAgICBjb25zdCB0b1N0ciA9IFN0cmluZyh0aGlzLnRvKS5wYWRTdGFydCh0aGlzLm1heExlbmd0aCwgJzAnKTtcbiAgICBsZXQgbmV4dFZhbCA9IHRoaXMudmFsdWUgKyBjaDtcbiAgICBpZiAobmV4dFZhbC5sZW5ndGggPiB0aGlzLm1heExlbmd0aCkgcmV0dXJuICcnO1xuICAgIGNvbnN0IFttaW5zdHIsIG1heHN0cl0gPSB0aGlzLmJvdW5kYXJpZXMobmV4dFZhbCk7XG4gICAgaWYgKE51bWJlcihtYXhzdHIpIDwgdGhpcy5mcm9tKSByZXR1cm4gZnJvbVN0cltuZXh0VmFsLmxlbmd0aCAtIDFdO1xuICAgIGlmIChOdW1iZXIobWluc3RyKSA+IHRoaXMudG8pIHtcbiAgICAgIGlmICh0aGlzLmF1dG9maXggPT09ICdwYWQnICYmIG5leHRWYWwubGVuZ3RoIDwgdGhpcy5tYXhMZW5ndGgpIHtcbiAgICAgICAgcmV0dXJuIFsnJywgZGV0YWlscy5hZ2dyZWdhdGUodGhpcy5hcHBlbmQoZnJvbVN0cltuZXh0VmFsLmxlbmd0aCAtIDFdICsgY2gsIGZsYWdzKSldO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHRvU3RyW25leHRWYWwubGVuZ3RoIC0gMV07XG4gICAgfVxuICAgIHJldHVybiBjaDtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBkb1ZhbGlkYXRlKCkge1xuICAgIGNvbnN0IHN0ciA9IHRoaXMudmFsdWU7XG4gICAgY29uc3QgZmlyc3ROb25aZXJvID0gc3RyLnNlYXJjaCgvW14wXS8pO1xuICAgIGlmIChmaXJzdE5vblplcm8gPT09IC0xICYmIHN0ci5sZW5ndGggPD0gdGhpcy5fbWF0Y2hGcm9tKSByZXR1cm4gdHJ1ZTtcbiAgICBjb25zdCBbbWluc3RyLCBtYXhzdHJdID0gdGhpcy5ib3VuZGFyaWVzKHN0cik7XG4gICAgcmV0dXJuIHRoaXMuZnJvbSA8PSBOdW1iZXIobWF4c3RyKSAmJiBOdW1iZXIobWluc3RyKSA8PSB0aGlzLnRvICYmIHN1cGVyLmRvVmFsaWRhdGUoLi4uYXJndW1lbnRzKTtcbiAgfVxufVxuSU1hc2suTWFza2VkUmFuZ2UgPSBNYXNrZWRSYW5nZTtcblxuZXhwb3J0IHsgTWFza2VkUmFuZ2UgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBNYXNrZWRQYXR0ZXJuIGZyb20gJy4vcGF0dGVybi5qcyc7XG5pbXBvcnQgTWFza2VkUmFuZ2UgZnJvbSAnLi9yYW5nZS5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuaW1wb3J0ICcuLi9fcm9sbHVwUGx1Z2luQmFiZWxIZWxwZXJzLTZiM2JkNDA0LmpzJztcbmltcG9ydCAnLi4vY29yZS91dGlscy5qcyc7XG5pbXBvcnQgJy4uL2NvcmUvY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuL2Jhc2UuanMnO1xuaW1wb3J0ICcuLi9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzJztcbmltcG9ydCAnLi9wYXR0ZXJuL2lucHV0LWRlZmluaXRpb24uanMnO1xuaW1wb3J0ICcuL2ZhY3RvcnkuanMnO1xuaW1wb3J0ICcuL3BhdHRlcm4vZml4ZWQtZGVmaW5pdGlvbi5qcyc7XG5pbXBvcnQgJy4vcGF0dGVybi9jaHVuay10YWlsLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuL3BhdHRlcm4vY3Vyc29yLmpzJztcbmltcG9ydCAnLi9yZWdleHAuanMnO1xuXG4vKiogRGF0ZSBtYXNrICovXG5jbGFzcyBNYXNrZWREYXRlIGV4dGVuZHMgTWFza2VkUGF0dGVybiB7XG4gIC8qKiBQYXR0ZXJuIG1hc2sgZm9yIGRhdGUgYWNjb3JkaW5nIHRvIHtAbGluayBNYXNrZWREYXRlI2Zvcm1hdH0gKi9cblxuICAvKiogU3RhcnQgZGF0ZSAqL1xuXG4gIC8qKiBFbmQgZGF0ZSAqL1xuXG4gIC8qKiAqL1xuXG4gIC8qKlxuICAgIEBwYXJhbSB7T2JqZWN0fSBvcHRzXG4gICovXG4gIGNvbnN0cnVjdG9yKG9wdHMpIHtcbiAgICBzdXBlcihPYmplY3QuYXNzaWduKHt9LCBNYXNrZWREYXRlLkRFRkFVTFRTLCBvcHRzKSk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgX3VwZGF0ZShvcHRzKSB7XG4gICAgaWYgKG9wdHMubWFzayA9PT0gRGF0ZSkgZGVsZXRlIG9wdHMubWFzaztcbiAgICBpZiAob3B0cy5wYXR0ZXJuKSBvcHRzLm1hc2sgPSBvcHRzLnBhdHRlcm47XG4gICAgY29uc3QgYmxvY2tzID0gb3B0cy5ibG9ja3M7XG4gICAgb3B0cy5ibG9ja3MgPSBPYmplY3QuYXNzaWduKHt9LCBNYXNrZWREYXRlLkdFVF9ERUZBVUxUX0JMT0NLUygpKTtcbiAgICAvLyBhZGp1c3QgeWVhciBibG9ja1xuICAgIGlmIChvcHRzLm1pbikgb3B0cy5ibG9ja3MuWS5mcm9tID0gb3B0cy5taW4uZ2V0RnVsbFllYXIoKTtcbiAgICBpZiAob3B0cy5tYXgpIG9wdHMuYmxvY2tzLlkudG8gPSBvcHRzLm1heC5nZXRGdWxsWWVhcigpO1xuICAgIGlmIChvcHRzLm1pbiAmJiBvcHRzLm1heCAmJiBvcHRzLmJsb2Nrcy5ZLmZyb20gPT09IG9wdHMuYmxvY2tzLlkudG8pIHtcbiAgICAgIG9wdHMuYmxvY2tzLm0uZnJvbSA9IG9wdHMubWluLmdldE1vbnRoKCkgKyAxO1xuICAgICAgb3B0cy5ibG9ja3MubS50byA9IG9wdHMubWF4LmdldE1vbnRoKCkgKyAxO1xuICAgICAgaWYgKG9wdHMuYmxvY2tzLm0uZnJvbSA9PT0gb3B0cy5ibG9ja3MubS50bykge1xuICAgICAgICBvcHRzLmJsb2Nrcy5kLmZyb20gPSBvcHRzLm1pbi5nZXREYXRlKCk7XG4gICAgICAgIG9wdHMuYmxvY2tzLmQudG8gPSBvcHRzLm1heC5nZXREYXRlKCk7XG4gICAgICB9XG4gICAgfVxuICAgIE9iamVjdC5hc3NpZ24ob3B0cy5ibG9ja3MsIHRoaXMuYmxvY2tzLCBibG9ja3MpO1xuXG4gICAgLy8gYWRkIGF1dG9maXhcbiAgICBPYmplY3Qua2V5cyhvcHRzLmJsb2NrcykuZm9yRWFjaChiayA9PiB7XG4gICAgICBjb25zdCBiID0gb3B0cy5ibG9ja3NbYmtdO1xuICAgICAgaWYgKCEoJ2F1dG9maXgnIGluIGIpICYmICdhdXRvZml4JyBpbiBvcHRzKSBiLmF1dG9maXggPSBvcHRzLmF1dG9maXg7XG4gICAgfSk7XG4gICAgc3VwZXIuX3VwZGF0ZShvcHRzKTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBkb1ZhbGlkYXRlKCkge1xuICAgIGNvbnN0IGRhdGUgPSB0aGlzLmRhdGU7XG4gICAgcmV0dXJuIHN1cGVyLmRvVmFsaWRhdGUoLi4uYXJndW1lbnRzKSAmJiAoIXRoaXMuaXNDb21wbGV0ZSB8fCB0aGlzLmlzRGF0ZUV4aXN0KHRoaXMudmFsdWUpICYmIGRhdGUgIT0gbnVsbCAmJiAodGhpcy5taW4gPT0gbnVsbCB8fCB0aGlzLm1pbiA8PSBkYXRlKSAmJiAodGhpcy5tYXggPT0gbnVsbCB8fCBkYXRlIDw9IHRoaXMubWF4KSk7XG4gIH1cblxuICAvKiogQ2hlY2tzIGlmIGRhdGUgaXMgZXhpc3RzICovXG4gIGlzRGF0ZUV4aXN0KHN0cikge1xuICAgIHJldHVybiB0aGlzLmZvcm1hdCh0aGlzLnBhcnNlKHN0ciwgdGhpcyksIHRoaXMpLmluZGV4T2Yoc3RyKSA+PSAwO1xuICB9XG5cbiAgLyoqIFBhcnNlZCBEYXRlICovXG4gIGdldCBkYXRlKCkge1xuICAgIHJldHVybiB0aGlzLnR5cGVkVmFsdWU7XG4gIH1cbiAgc2V0IGRhdGUoZGF0ZSkge1xuICAgIHRoaXMudHlwZWRWYWx1ZSA9IGRhdGU7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IHR5cGVkVmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMuaXNDb21wbGV0ZSA/IHN1cGVyLnR5cGVkVmFsdWUgOiBudWxsO1xuICB9XG4gIHNldCB0eXBlZFZhbHVlKHZhbHVlKSB7XG4gICAgc3VwZXIudHlwZWRWYWx1ZSA9IHZhbHVlO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIG1hc2tFcXVhbHMobWFzaykge1xuICAgIHJldHVybiBtYXNrID09PSBEYXRlIHx8IHN1cGVyLm1hc2tFcXVhbHMobWFzayk7XG4gIH1cbn1cbk1hc2tlZERhdGUuREVGQVVMVFMgPSB7XG4gIHBhdHRlcm46ICdkey59YG17Ln1gWScsXG4gIGZvcm1hdDogZGF0ZSA9PiB7XG4gICAgaWYgKCFkYXRlKSByZXR1cm4gJyc7XG4gICAgY29uc3QgZGF5ID0gU3RyaW5nKGRhdGUuZ2V0RGF0ZSgpKS5wYWRTdGFydCgyLCAnMCcpO1xuICAgIGNvbnN0IG1vbnRoID0gU3RyaW5nKGRhdGUuZ2V0TW9udGgoKSArIDEpLnBhZFN0YXJ0KDIsICcwJyk7XG4gICAgY29uc3QgeWVhciA9IGRhdGUuZ2V0RnVsbFllYXIoKTtcbiAgICByZXR1cm4gW2RheSwgbW9udGgsIHllYXJdLmpvaW4oJy4nKTtcbiAgfSxcbiAgcGFyc2U6IHN0ciA9PiB7XG4gICAgY29uc3QgW2RheSwgbW9udGgsIHllYXJdID0gc3RyLnNwbGl0KCcuJyk7XG4gICAgcmV0dXJuIG5ldyBEYXRlKHllYXIsIG1vbnRoIC0gMSwgZGF5KTtcbiAgfVxufTtcbk1hc2tlZERhdGUuR0VUX0RFRkFVTFRfQkxPQ0tTID0gKCkgPT4gKHtcbiAgZDoge1xuICAgIG1hc2s6IE1hc2tlZFJhbmdlLFxuICAgIGZyb206IDEsXG4gICAgdG86IDMxLFxuICAgIG1heExlbmd0aDogMlxuICB9LFxuICBtOiB7XG4gICAgbWFzazogTWFza2VkUmFuZ2UsXG4gICAgZnJvbTogMSxcbiAgICB0bzogMTIsXG4gICAgbWF4TGVuZ3RoOiAyXG4gIH0sXG4gIFk6IHtcbiAgICBtYXNrOiBNYXNrZWRSYW5nZSxcbiAgICBmcm9tOiAxOTAwLFxuICAgIHRvOiA5OTk5XG4gIH1cbn0pO1xuSU1hc2suTWFza2VkRGF0ZSA9IE1hc2tlZERhdGU7XG5cbmV4cG9ydCB7IE1hc2tlZERhdGUgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBJTWFzayBmcm9tICcuLi9jb3JlL2hvbGRlci5qcyc7XG5cbi8qKlxuICBHZW5lcmljIGVsZW1lbnQgQVBJIHRvIHVzZSB3aXRoIG1hc2tcbiAgQGludGVyZmFjZVxuKi9cbmNsYXNzIE1hc2tFbGVtZW50IHtcbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgLyoqIFNhZmVseSByZXR1cm5zIHNlbGVjdGlvbiBzdGFydCAqL1xuICBnZXQgc2VsZWN0aW9uU3RhcnQoKSB7XG4gICAgbGV0IHN0YXJ0O1xuICAgIHRyeSB7XG4gICAgICBzdGFydCA9IHRoaXMuX3Vuc2FmZVNlbGVjdGlvblN0YXJ0O1xuICAgIH0gY2F0Y2ggKGUpIHt9XG4gICAgcmV0dXJuIHN0YXJ0ICE9IG51bGwgPyBzdGFydCA6IHRoaXMudmFsdWUubGVuZ3RoO1xuICB9XG5cbiAgLyoqIFNhZmVseSByZXR1cm5zIHNlbGVjdGlvbiBlbmQgKi9cbiAgZ2V0IHNlbGVjdGlvbkVuZCgpIHtcbiAgICBsZXQgZW5kO1xuICAgIHRyeSB7XG4gICAgICBlbmQgPSB0aGlzLl91bnNhZmVTZWxlY3Rpb25FbmQ7XG4gICAgfSBjYXRjaCAoZSkge31cbiAgICByZXR1cm4gZW5kICE9IG51bGwgPyBlbmQgOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgfVxuXG4gIC8qKiBTYWZlbHkgc2V0cyBlbGVtZW50IHNlbGVjdGlvbiAqL1xuICBzZWxlY3Qoc3RhcnQsIGVuZCkge1xuICAgIGlmIChzdGFydCA9PSBudWxsIHx8IGVuZCA9PSBudWxsIHx8IHN0YXJ0ID09PSB0aGlzLnNlbGVjdGlvblN0YXJ0ICYmIGVuZCA9PT0gdGhpcy5zZWxlY3Rpb25FbmQpIHJldHVybjtcbiAgICB0cnkge1xuICAgICAgdGhpcy5fdW5zYWZlU2VsZWN0KHN0YXJ0LCBlbmQpO1xuICAgIH0gY2F0Y2ggKGUpIHt9XG4gIH1cblxuICAvKiogU2hvdWxkIGJlIG92ZXJyaWRlbiBpbiBzdWJjbGFzc2VzICovXG4gIF91bnNhZmVTZWxlY3Qoc3RhcnQsIGVuZCkge31cbiAgLyoqIFNob3VsZCBiZSBvdmVycmlkZW4gaW4gc3ViY2xhc3NlcyAqL1xuICBnZXQgaXNBY3RpdmUoKSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG4gIC8qKiBTaG91bGQgYmUgb3ZlcnJpZGVuIGluIHN1YmNsYXNzZXMgKi9cbiAgYmluZEV2ZW50cyhoYW5kbGVycykge31cbiAgLyoqIFNob3VsZCBiZSBvdmVycmlkZW4gaW4gc3ViY2xhc3NlcyAqL1xuICB1bmJpbmRFdmVudHMoKSB7fVxufVxuSU1hc2suTWFza0VsZW1lbnQgPSBNYXNrRWxlbWVudDtcblxuZXhwb3J0IHsgTWFza0VsZW1lbnQgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBNYXNrRWxlbWVudCBmcm9tICcuL21hc2stZWxlbWVudC5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuXG4vKiogQnJpZGdlIGJldHdlZW4gSFRNTEVsZW1lbnQgYW5kIHtAbGluayBNYXNrZWR9ICovXG5jbGFzcyBIVE1MTWFza0VsZW1lbnQgZXh0ZW5kcyBNYXNrRWxlbWVudCB7XG4gIC8qKiBNYXBwaW5nIGJldHdlZW4gSFRNTEVsZW1lbnQgZXZlbnRzIGFuZCBtYXNrIGludGVybmFsIGV2ZW50cyAqL1xuXG4gIC8qKiBIVE1MRWxlbWVudCB0byB1c2UgbWFzayBvbiAqL1xuXG4gIC8qKlxuICAgIEBwYXJhbSB7SFRNTElucHV0RWxlbWVudHxIVE1MVGV4dEFyZWFFbGVtZW50fSBpbnB1dFxuICAqL1xuICBjb25zdHJ1Y3RvcihpbnB1dCkge1xuICAgIHN1cGVyKCk7XG4gICAgdGhpcy5pbnB1dCA9IGlucHV0O1xuICAgIHRoaXMuX2hhbmRsZXJzID0ge307XG4gIH1cblxuICAvKiogKi9cbiAgLy8gJEZsb3dGaXhNZSBodHRwczovL2dpdGh1Yi5jb20vZmFjZWJvb2svZmxvdy9pc3N1ZXMvMjgzOVxuICBnZXQgcm9vdEVsZW1lbnQoKSB7XG4gICAgdmFyIF90aGlzJGlucHV0JGdldFJvb3RObywgX3RoaXMkaW5wdXQkZ2V0Um9vdE5vMiwgX3RoaXMkaW5wdXQ7XG4gICAgcmV0dXJuIChfdGhpcyRpbnB1dCRnZXRSb290Tm8gPSAoX3RoaXMkaW5wdXQkZ2V0Um9vdE5vMiA9IChfdGhpcyRpbnB1dCA9IHRoaXMuaW5wdXQpLmdldFJvb3ROb2RlKSA9PT0gbnVsbCB8fCBfdGhpcyRpbnB1dCRnZXRSb290Tm8yID09PSB2b2lkIDAgPyB2b2lkIDAgOiBfdGhpcyRpbnB1dCRnZXRSb290Tm8yLmNhbGwoX3RoaXMkaW5wdXQpKSAhPT0gbnVsbCAmJiBfdGhpcyRpbnB1dCRnZXRSb290Tm8gIT09IHZvaWQgMCA/IF90aGlzJGlucHV0JGdldFJvb3RObyA6IGRvY3VtZW50O1xuICB9XG5cbiAgLyoqXG4gICAgSXMgZWxlbWVudCBpbiBmb2N1c1xuICAgIEByZWFkb25seVxuICAqL1xuICBnZXQgaXNBY3RpdmUoKSB7XG4gICAgLy8kRmxvd0ZpeE1lXG4gICAgcmV0dXJuIHRoaXMuaW5wdXQgPT09IHRoaXMucm9vdEVsZW1lbnQuYWN0aXZlRWxlbWVudDtcbiAgfVxuXG4gIC8qKlxuICAgIFJldHVybnMgSFRNTEVsZW1lbnQgc2VsZWN0aW9uIHN0YXJ0XG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCBfdW5zYWZlU2VsZWN0aW9uU3RhcnQoKSB7XG4gICAgcmV0dXJuIHRoaXMuaW5wdXQuc2VsZWN0aW9uU3RhcnQ7XG4gIH1cblxuICAvKipcbiAgICBSZXR1cm5zIEhUTUxFbGVtZW50IHNlbGVjdGlvbiBlbmRcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IF91bnNhZmVTZWxlY3Rpb25FbmQoKSB7XG4gICAgcmV0dXJuIHRoaXMuaW5wdXQuc2VsZWN0aW9uRW5kO1xuICB9XG5cbiAgLyoqXG4gICAgU2V0cyBIVE1MRWxlbWVudCBzZWxlY3Rpb25cbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgX3Vuc2FmZVNlbGVjdChzdGFydCwgZW5kKSB7XG4gICAgdGhpcy5pbnB1dC5zZXRTZWxlY3Rpb25SYW5nZShzdGFydCwgZW5kKTtcbiAgfVxuXG4gIC8qKlxuICAgIEhUTUxFbGVtZW50IHZhbHVlXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCB2YWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5pbnB1dC52YWx1ZTtcbiAgfVxuICBzZXQgdmFsdWUodmFsdWUpIHtcbiAgICB0aGlzLmlucHV0LnZhbHVlID0gdmFsdWU7XG4gIH1cblxuICAvKipcbiAgICBCaW5kcyBIVE1MRWxlbWVudCBldmVudHMgdG8gbWFzayBpbnRlcm5hbCBldmVudHNcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgYmluZEV2ZW50cyhoYW5kbGVycykge1xuICAgIE9iamVjdC5rZXlzKGhhbmRsZXJzKS5mb3JFYWNoKGV2ZW50ID0+IHRoaXMuX3RvZ2dsZUV2ZW50SGFuZGxlcihIVE1MTWFza0VsZW1lbnQuRVZFTlRTX01BUFtldmVudF0sIGhhbmRsZXJzW2V2ZW50XSkpO1xuICB9XG5cbiAgLyoqXG4gICAgVW5iaW5kcyBIVE1MRWxlbWVudCBldmVudHMgdG8gbWFzayBpbnRlcm5hbCBldmVudHNcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgdW5iaW5kRXZlbnRzKCkge1xuICAgIE9iamVjdC5rZXlzKHRoaXMuX2hhbmRsZXJzKS5mb3JFYWNoKGV2ZW50ID0+IHRoaXMuX3RvZ2dsZUV2ZW50SGFuZGxlcihldmVudCkpO1xuICB9XG5cbiAgLyoqICovXG4gIF90b2dnbGVFdmVudEhhbmRsZXIoZXZlbnQsIGhhbmRsZXIpIHtcbiAgICBpZiAodGhpcy5faGFuZGxlcnNbZXZlbnRdKSB7XG4gICAgICB0aGlzLmlucHV0LnJlbW92ZUV2ZW50TGlzdGVuZXIoZXZlbnQsIHRoaXMuX2hhbmRsZXJzW2V2ZW50XSk7XG4gICAgICBkZWxldGUgdGhpcy5faGFuZGxlcnNbZXZlbnRdO1xuICAgIH1cbiAgICBpZiAoaGFuZGxlcikge1xuICAgICAgdGhpcy5pbnB1dC5hZGRFdmVudExpc3RlbmVyKGV2ZW50LCBoYW5kbGVyKTtcbiAgICAgIHRoaXMuX2hhbmRsZXJzW2V2ZW50XSA9IGhhbmRsZXI7XG4gICAgfVxuICB9XG59XG5IVE1MTWFza0VsZW1lbnQuRVZFTlRTX01BUCA9IHtcbiAgc2VsZWN0aW9uQ2hhbmdlOiAna2V5ZG93bicsXG4gIGlucHV0OiAnaW5wdXQnLFxuICBkcm9wOiAnZHJvcCcsXG4gIGNsaWNrOiAnY2xpY2snLFxuICBmb2N1czogJ2ZvY3VzJyxcbiAgY29tbWl0OiAnYmx1cidcbn07XG5JTWFzay5IVE1MTWFza0VsZW1lbnQgPSBIVE1MTWFza0VsZW1lbnQ7XG5cbmV4cG9ydCB7IEhUTUxNYXNrRWxlbWVudCBhcyBkZWZhdWx0IH07XG4iLCAiaW1wb3J0IEhUTUxNYXNrRWxlbWVudCBmcm9tICcuL2h0bWwtbWFzay1lbGVtZW50LmpzJztcbmltcG9ydCBJTWFzayBmcm9tICcuLi9jb3JlL2hvbGRlci5qcyc7XG5pbXBvcnQgJy4vbWFzay1lbGVtZW50LmpzJztcblxuY2xhc3MgSFRNTENvbnRlbnRlZGl0YWJsZU1hc2tFbGVtZW50IGV4dGVuZHMgSFRNTE1hc2tFbGVtZW50IHtcbiAgLyoqXG4gICAgUmV0dXJucyBIVE1MRWxlbWVudCBzZWxlY3Rpb24gc3RhcnRcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IF91bnNhZmVTZWxlY3Rpb25TdGFydCgpIHtcbiAgICBjb25zdCByb290ID0gdGhpcy5yb290RWxlbWVudDtcbiAgICBjb25zdCBzZWxlY3Rpb24gPSByb290LmdldFNlbGVjdGlvbiAmJiByb290LmdldFNlbGVjdGlvbigpO1xuICAgIGNvbnN0IGFuY2hvck9mZnNldCA9IHNlbGVjdGlvbiAmJiBzZWxlY3Rpb24uYW5jaG9yT2Zmc2V0O1xuICAgIGNvbnN0IGZvY3VzT2Zmc2V0ID0gc2VsZWN0aW9uICYmIHNlbGVjdGlvbi5mb2N1c09mZnNldDtcbiAgICBpZiAoZm9jdXNPZmZzZXQgPT0gbnVsbCB8fCBhbmNob3JPZmZzZXQgPT0gbnVsbCB8fCBhbmNob3JPZmZzZXQgPCBmb2N1c09mZnNldCkge1xuICAgICAgcmV0dXJuIGFuY2hvck9mZnNldDtcbiAgICB9XG4gICAgcmV0dXJuIGZvY3VzT2Zmc2V0O1xuICB9XG5cbiAgLyoqXG4gICAgUmV0dXJucyBIVE1MRWxlbWVudCBzZWxlY3Rpb24gZW5kXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCBfdW5zYWZlU2VsZWN0aW9uRW5kKCkge1xuICAgIGNvbnN0IHJvb3QgPSB0aGlzLnJvb3RFbGVtZW50O1xuICAgIGNvbnN0IHNlbGVjdGlvbiA9IHJvb3QuZ2V0U2VsZWN0aW9uICYmIHJvb3QuZ2V0U2VsZWN0aW9uKCk7XG4gICAgY29uc3QgYW5jaG9yT2Zmc2V0ID0gc2VsZWN0aW9uICYmIHNlbGVjdGlvbi5hbmNob3JPZmZzZXQ7XG4gICAgY29uc3QgZm9jdXNPZmZzZXQgPSBzZWxlY3Rpb24gJiYgc2VsZWN0aW9uLmZvY3VzT2Zmc2V0O1xuICAgIGlmIChmb2N1c09mZnNldCA9PSBudWxsIHx8IGFuY2hvck9mZnNldCA9PSBudWxsIHx8IGFuY2hvck9mZnNldCA+IGZvY3VzT2Zmc2V0KSB7XG4gICAgICByZXR1cm4gYW5jaG9yT2Zmc2V0O1xuICAgIH1cbiAgICByZXR1cm4gZm9jdXNPZmZzZXQ7XG4gIH1cblxuICAvKipcbiAgICBTZXRzIEhUTUxFbGVtZW50IHNlbGVjdGlvblxuICAgIEBvdmVycmlkZVxuICAqL1xuICBfdW5zYWZlU2VsZWN0KHN0YXJ0LCBlbmQpIHtcbiAgICBpZiAoIXRoaXMucm9vdEVsZW1lbnQuY3JlYXRlUmFuZ2UpIHJldHVybjtcbiAgICBjb25zdCByYW5nZSA9IHRoaXMucm9vdEVsZW1lbnQuY3JlYXRlUmFuZ2UoKTtcbiAgICByYW5nZS5zZXRTdGFydCh0aGlzLmlucHV0LmZpcnN0Q2hpbGQgfHwgdGhpcy5pbnB1dCwgc3RhcnQpO1xuICAgIHJhbmdlLnNldEVuZCh0aGlzLmlucHV0Lmxhc3RDaGlsZCB8fCB0aGlzLmlucHV0LCBlbmQpO1xuICAgIGNvbnN0IHJvb3QgPSB0aGlzLnJvb3RFbGVtZW50O1xuICAgIGNvbnN0IHNlbGVjdGlvbiA9IHJvb3QuZ2V0U2VsZWN0aW9uICYmIHJvb3QuZ2V0U2VsZWN0aW9uKCk7XG4gICAgaWYgKHNlbGVjdGlvbikge1xuICAgICAgc2VsZWN0aW9uLnJlbW92ZUFsbFJhbmdlcygpO1xuICAgICAgc2VsZWN0aW9uLmFkZFJhbmdlKHJhbmdlKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICBIVE1MRWxlbWVudCB2YWx1ZVxuICAgIEBvdmVycmlkZVxuICAqL1xuICBnZXQgdmFsdWUoKSB7XG4gICAgLy8gJEZsb3dGaXhNZVxuICAgIHJldHVybiB0aGlzLmlucHV0LnRleHRDb250ZW50O1xuICB9XG4gIHNldCB2YWx1ZSh2YWx1ZSkge1xuICAgIHRoaXMuaW5wdXQudGV4dENvbnRlbnQgPSB2YWx1ZTtcbiAgfVxufVxuSU1hc2suSFRNTENvbnRlbnRlZGl0YWJsZU1hc2tFbGVtZW50ID0gSFRNTENvbnRlbnRlZGl0YWJsZU1hc2tFbGVtZW50O1xuXG5leHBvcnQgeyBIVE1MQ29udGVudGVkaXRhYmxlTWFza0VsZW1lbnQgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCB7IF8gYXMgX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2UgfSBmcm9tICcuLi9fcm9sbHVwUGx1Z2luQmFiZWxIZWxwZXJzLTZiM2JkNDA0LmpzJztcbmltcG9ydCB7IG9iamVjdEluY2x1ZGVzLCBESVJFQ1RJT04gfSBmcm9tICcuLi9jb3JlL3V0aWxzLmpzJztcbmltcG9ydCBBY3Rpb25EZXRhaWxzIGZyb20gJy4uL2NvcmUvYWN0aW9uLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuLi9tYXNrZWQvZGF0ZS5qcyc7XG5pbXBvcnQgY3JlYXRlTWFzaywgeyBtYXNrZWRDbGFzcyB9IGZyb20gJy4uL21hc2tlZC9mYWN0b3J5LmpzJztcbmltcG9ydCBNYXNrRWxlbWVudCBmcm9tICcuL21hc2stZWxlbWVudC5qcyc7XG5pbXBvcnQgSFRNTE1hc2tFbGVtZW50IGZyb20gJy4vaHRtbC1tYXNrLWVsZW1lbnQuanMnO1xuaW1wb3J0IEhUTUxDb250ZW50ZWRpdGFibGVNYXNrRWxlbWVudCBmcm9tICcuL2h0bWwtY29udGVudGVkaXRhYmxlLW1hc2stZWxlbWVudC5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuaW1wb3J0ICcuLi9jb3JlL2NoYW5nZS1kZXRhaWxzLmpzJztcbmltcG9ydCAnLi4vbWFza2VkL3BhdHRlcm4uanMnO1xuaW1wb3J0ICcuLi9tYXNrZWQvYmFzZS5qcyc7XG5pbXBvcnQgJy4uL2NvcmUvY29udGludW91cy10YWlsLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuLi9tYXNrZWQvcGF0dGVybi9pbnB1dC1kZWZpbml0aW9uLmpzJztcbmltcG9ydCAnLi4vbWFza2VkL3BhdHRlcm4vZml4ZWQtZGVmaW5pdGlvbi5qcyc7XG5pbXBvcnQgJy4uL21hc2tlZC9wYXR0ZXJuL2NodW5rLXRhaWwtZGV0YWlscy5qcyc7XG5pbXBvcnQgJy4uL21hc2tlZC9wYXR0ZXJuL2N1cnNvci5qcyc7XG5pbXBvcnQgJy4uL21hc2tlZC9yZWdleHAuanMnO1xuaW1wb3J0ICcuLi9tYXNrZWQvcmFuZ2UuanMnO1xuXG5jb25zdCBfZXhjbHVkZWQgPSBbXCJtYXNrXCJdO1xuXG4vKiogTGlzdGVucyB0byBlbGVtZW50IGV2ZW50cyBhbmQgY29udHJvbHMgY2hhbmdlcyBiZXR3ZWVuIGVsZW1lbnQgYW5kIHtAbGluayBNYXNrZWR9ICovXG5jbGFzcyBJbnB1dE1hc2sge1xuICAvKipcbiAgICBWaWV3IGVsZW1lbnRcbiAgICBAcmVhZG9ubHlcbiAgKi9cblxuICAvKipcbiAgICBJbnRlcm5hbCB7QGxpbmsgTWFza2VkfSBtb2RlbFxuICAgIEByZWFkb25seVxuICAqL1xuXG4gIC8qKlxuICAgIEBwYXJhbSB7TWFza0VsZW1lbnR8SFRNTElucHV0RWxlbWVudHxIVE1MVGV4dEFyZWFFbGVtZW50fSBlbFxuICAgIEBwYXJhbSB7T2JqZWN0fSBvcHRzXG4gICovXG4gIGNvbnN0cnVjdG9yKGVsLCBvcHRzKSB7XG4gICAgdGhpcy5lbCA9IGVsIGluc3RhbmNlb2YgTWFza0VsZW1lbnQgPyBlbCA6IGVsLmlzQ29udGVudEVkaXRhYmxlICYmIGVsLnRhZ05hbWUgIT09ICdJTlBVVCcgJiYgZWwudGFnTmFtZSAhPT0gJ1RFWFRBUkVBJyA/IG5ldyBIVE1MQ29udGVudGVkaXRhYmxlTWFza0VsZW1lbnQoZWwpIDogbmV3IEhUTUxNYXNrRWxlbWVudChlbCk7XG4gICAgdGhpcy5tYXNrZWQgPSBjcmVhdGVNYXNrKG9wdHMpO1xuICAgIHRoaXMuX2xpc3RlbmVycyA9IHt9O1xuICAgIHRoaXMuX3ZhbHVlID0gJyc7XG4gICAgdGhpcy5fdW5tYXNrZWRWYWx1ZSA9ICcnO1xuICAgIHRoaXMuX3NhdmVTZWxlY3Rpb24gPSB0aGlzLl9zYXZlU2VsZWN0aW9uLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25JbnB1dCA9IHRoaXMuX29uSW5wdXQuYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vbkNoYW5nZSA9IHRoaXMuX29uQ2hhbmdlLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25Ecm9wID0gdGhpcy5fb25Ecm9wLmJpbmQodGhpcyk7XG4gICAgdGhpcy5fb25Gb2N1cyA9IHRoaXMuX29uRm9jdXMuYmluZCh0aGlzKTtcbiAgICB0aGlzLl9vbkNsaWNrID0gdGhpcy5fb25DbGljay5iaW5kKHRoaXMpO1xuICAgIHRoaXMuYWxpZ25DdXJzb3IgPSB0aGlzLmFsaWduQ3Vyc29yLmJpbmQodGhpcyk7XG4gICAgdGhpcy5hbGlnbkN1cnNvckZyaWVuZGx5ID0gdGhpcy5hbGlnbkN1cnNvckZyaWVuZGx5LmJpbmQodGhpcyk7XG4gICAgdGhpcy5fYmluZEV2ZW50cygpO1xuXG4gICAgLy8gcmVmcmVzaFxuICAgIHRoaXMudXBkYXRlVmFsdWUoKTtcbiAgICB0aGlzLl9vbkNoYW5nZSgpO1xuICB9XG5cbiAgLyoqIFJlYWQgb3IgdXBkYXRlIG1hc2sgKi9cbiAgZ2V0IG1hc2soKSB7XG4gICAgcmV0dXJuIHRoaXMubWFza2VkLm1hc2s7XG4gIH1cbiAgbWFza0VxdWFscyhtYXNrKSB7XG4gICAgdmFyIF90aGlzJG1hc2tlZDtcbiAgICByZXR1cm4gbWFzayA9PSBudWxsIHx8ICgoX3RoaXMkbWFza2VkID0gdGhpcy5tYXNrZWQpID09PSBudWxsIHx8IF90aGlzJG1hc2tlZCA9PT0gdm9pZCAwID8gdm9pZCAwIDogX3RoaXMkbWFza2VkLm1hc2tFcXVhbHMobWFzaykpO1xuICB9XG4gIHNldCBtYXNrKG1hc2spIHtcbiAgICBpZiAodGhpcy5tYXNrRXF1YWxzKG1hc2spKSByZXR1cm47XG5cbiAgICAvLyAkRmxvd0ZpeE1lIE5vIGlkZWFzIC4uLiBhZnRlciB1cGRhdGVcbiAgICBpZiAoIShtYXNrIGluc3RhbmNlb2YgSU1hc2suTWFza2VkKSAmJiB0aGlzLm1hc2tlZC5jb25zdHJ1Y3RvciA9PT0gbWFza2VkQ2xhc3MobWFzaykpIHtcbiAgICAgIHRoaXMubWFza2VkLnVwZGF0ZU9wdGlvbnMoe1xuICAgICAgICBtYXNrXG4gICAgICB9KTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFza2VkID0gY3JlYXRlTWFzayh7XG4gICAgICBtYXNrXG4gICAgfSk7XG4gICAgbWFza2VkLnVubWFza2VkVmFsdWUgPSB0aGlzLm1hc2tlZC51bm1hc2tlZFZhbHVlO1xuICAgIHRoaXMubWFza2VkID0gbWFza2VkO1xuICB9XG5cbiAgLyoqIFJhdyB2YWx1ZSAqL1xuICBnZXQgdmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMuX3ZhbHVlO1xuICB9XG4gIHNldCB2YWx1ZShzdHIpIHtcbiAgICBpZiAodGhpcy52YWx1ZSA9PT0gc3RyKSByZXR1cm47XG4gICAgdGhpcy5tYXNrZWQudmFsdWUgPSBzdHI7XG4gICAgdGhpcy51cGRhdGVDb250cm9sKCk7XG4gICAgdGhpcy5hbGlnbkN1cnNvcigpO1xuICB9XG5cbiAgLyoqIFVubWFza2VkIHZhbHVlICovXG4gIGdldCB1bm1hc2tlZFZhbHVlKCkge1xuICAgIHJldHVybiB0aGlzLl91bm1hc2tlZFZhbHVlO1xuICB9XG4gIHNldCB1bm1hc2tlZFZhbHVlKHN0cikge1xuICAgIGlmICh0aGlzLnVubWFza2VkVmFsdWUgPT09IHN0cikgcmV0dXJuO1xuICAgIHRoaXMubWFza2VkLnVubWFza2VkVmFsdWUgPSBzdHI7XG4gICAgdGhpcy51cGRhdGVDb250cm9sKCk7XG4gICAgdGhpcy5hbGlnbkN1cnNvcigpO1xuICB9XG5cbiAgLyoqIFR5cGVkIHVubWFza2VkIHZhbHVlICovXG4gIGdldCB0eXBlZFZhbHVlKCkge1xuICAgIHJldHVybiB0aGlzLm1hc2tlZC50eXBlZFZhbHVlO1xuICB9XG4gIHNldCB0eXBlZFZhbHVlKHZhbCkge1xuICAgIGlmICh0aGlzLm1hc2tlZC50eXBlZFZhbHVlRXF1YWxzKHZhbCkpIHJldHVybjtcbiAgICB0aGlzLm1hc2tlZC50eXBlZFZhbHVlID0gdmFsO1xuICAgIHRoaXMudXBkYXRlQ29udHJvbCgpO1xuICAgIHRoaXMuYWxpZ25DdXJzb3IoKTtcbiAgfVxuXG4gIC8qKiBEaXNwbGF5IHZhbHVlICovXG4gIGdldCBkaXNwbGF5VmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMubWFza2VkLmRpc3BsYXlWYWx1ZTtcbiAgfVxuXG4gIC8qKlxuICAgIFN0YXJ0cyBsaXN0ZW5pbmcgdG8gZWxlbWVudCBldmVudHNcbiAgICBAcHJvdGVjdGVkXG4gICovXG4gIF9iaW5kRXZlbnRzKCkge1xuICAgIHRoaXMuZWwuYmluZEV2ZW50cyh7XG4gICAgICBzZWxlY3Rpb25DaGFuZ2U6IHRoaXMuX3NhdmVTZWxlY3Rpb24sXG4gICAgICBpbnB1dDogdGhpcy5fb25JbnB1dCxcbiAgICAgIGRyb3A6IHRoaXMuX29uRHJvcCxcbiAgICAgIGNsaWNrOiB0aGlzLl9vbkNsaWNrLFxuICAgICAgZm9jdXM6IHRoaXMuX29uRm9jdXMsXG4gICAgICBjb21taXQ6IHRoaXMuX29uQ2hhbmdlXG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICBTdG9wcyBsaXN0ZW5pbmcgdG8gZWxlbWVudCBldmVudHNcbiAgICBAcHJvdGVjdGVkXG4gICAqL1xuICBfdW5iaW5kRXZlbnRzKCkge1xuICAgIGlmICh0aGlzLmVsKSB0aGlzLmVsLnVuYmluZEV2ZW50cygpO1xuICB9XG5cbiAgLyoqXG4gICAgRmlyZXMgY3VzdG9tIGV2ZW50XG4gICAgQHByb3RlY3RlZFxuICAgKi9cbiAgX2ZpcmVFdmVudChldikge1xuICAgIGZvciAodmFyIF9sZW4gPSBhcmd1bWVudHMubGVuZ3RoLCBhcmdzID0gbmV3IEFycmF5KF9sZW4gPiAxID8gX2xlbiAtIDEgOiAwKSwgX2tleSA9IDE7IF9rZXkgPCBfbGVuOyBfa2V5KyspIHtcbiAgICAgIGFyZ3NbX2tleSAtIDFdID0gYXJndW1lbnRzW19rZXldO1xuICAgIH1cbiAgICBjb25zdCBsaXN0ZW5lcnMgPSB0aGlzLl9saXN0ZW5lcnNbZXZdO1xuICAgIGlmICghbGlzdGVuZXJzKSByZXR1cm47XG4gICAgbGlzdGVuZXJzLmZvckVhY2gobCA9PiBsKC4uLmFyZ3MpKTtcbiAgfVxuXG4gIC8qKlxuICAgIEN1cnJlbnQgc2VsZWN0aW9uIHN0YXJ0XG4gICAgQHJlYWRvbmx5XG4gICovXG4gIGdldCBzZWxlY3Rpb25TdGFydCgpIHtcbiAgICByZXR1cm4gdGhpcy5fY3Vyc29yQ2hhbmdpbmcgPyB0aGlzLl9jaGFuZ2luZ0N1cnNvclBvcyA6IHRoaXMuZWwuc2VsZWN0aW9uU3RhcnQ7XG4gIH1cblxuICAvKiogQ3VycmVudCBjdXJzb3IgcG9zaXRpb24gKi9cbiAgZ2V0IGN1cnNvclBvcygpIHtcbiAgICByZXR1cm4gdGhpcy5fY3Vyc29yQ2hhbmdpbmcgPyB0aGlzLl9jaGFuZ2luZ0N1cnNvclBvcyA6IHRoaXMuZWwuc2VsZWN0aW9uRW5kO1xuICB9XG4gIHNldCBjdXJzb3JQb3MocG9zKSB7XG4gICAgaWYgKCF0aGlzLmVsIHx8ICF0aGlzLmVsLmlzQWN0aXZlKSByZXR1cm47XG4gICAgdGhpcy5lbC5zZWxlY3QocG9zLCBwb3MpO1xuICAgIHRoaXMuX3NhdmVTZWxlY3Rpb24oKTtcbiAgfVxuXG4gIC8qKlxuICAgIFN0b3JlcyBjdXJyZW50IHNlbGVjdGlvblxuICAgIEBwcm90ZWN0ZWRcbiAgKi9cbiAgX3NhdmVTZWxlY3Rpb24oIC8qIGV2ICovXG4gICkge1xuICAgIGlmICh0aGlzLmRpc3BsYXlWYWx1ZSAhPT0gdGhpcy5lbC52YWx1ZSkge1xuICAgICAgY29uc29sZS53YXJuKCdFbGVtZW50IHZhbHVlIHdhcyBjaGFuZ2VkIG91dHNpZGUgb2YgbWFzay4gU3luY3Jvbml6ZSBtYXNrIHVzaW5nIGBtYXNrLnVwZGF0ZVZhbHVlKClgIHRvIHdvcmsgcHJvcGVybHkuJyk7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tY29uc29sZVxuICAgIH1cblxuICAgIHRoaXMuX3NlbGVjdGlvbiA9IHtcbiAgICAgIHN0YXJ0OiB0aGlzLnNlbGVjdGlvblN0YXJ0LFxuICAgICAgZW5kOiB0aGlzLmN1cnNvclBvc1xuICAgIH07XG4gIH1cblxuICAvKiogU3luY3Jvbml6ZXMgbW9kZWwgdmFsdWUgZnJvbSB2aWV3ICovXG4gIHVwZGF0ZVZhbHVlKCkge1xuICAgIHRoaXMubWFza2VkLnZhbHVlID0gdGhpcy5lbC52YWx1ZTtcbiAgICB0aGlzLl92YWx1ZSA9IHRoaXMubWFza2VkLnZhbHVlO1xuICB9XG5cbiAgLyoqIFN5bmNyb25pemVzIHZpZXcgZnJvbSBtb2RlbCB2YWx1ZSwgZmlyZXMgY2hhbmdlIGV2ZW50cyAqL1xuICB1cGRhdGVDb250cm9sKCkge1xuICAgIGNvbnN0IG5ld1VubWFza2VkVmFsdWUgPSB0aGlzLm1hc2tlZC51bm1hc2tlZFZhbHVlO1xuICAgIGNvbnN0IG5ld1ZhbHVlID0gdGhpcy5tYXNrZWQudmFsdWU7XG4gICAgY29uc3QgbmV3RGlzcGxheVZhbHVlID0gdGhpcy5kaXNwbGF5VmFsdWU7XG4gICAgY29uc3QgaXNDaGFuZ2VkID0gdGhpcy51bm1hc2tlZFZhbHVlICE9PSBuZXdVbm1hc2tlZFZhbHVlIHx8IHRoaXMudmFsdWUgIT09IG5ld1ZhbHVlO1xuICAgIHRoaXMuX3VubWFza2VkVmFsdWUgPSBuZXdVbm1hc2tlZFZhbHVlO1xuICAgIHRoaXMuX3ZhbHVlID0gbmV3VmFsdWU7XG4gICAgaWYgKHRoaXMuZWwudmFsdWUgIT09IG5ld0Rpc3BsYXlWYWx1ZSkgdGhpcy5lbC52YWx1ZSA9IG5ld0Rpc3BsYXlWYWx1ZTtcbiAgICBpZiAoaXNDaGFuZ2VkKSB0aGlzLl9maXJlQ2hhbmdlRXZlbnRzKCk7XG4gIH1cblxuICAvKiogVXBkYXRlcyBvcHRpb25zIHdpdGggZGVlcCBlcXVhbCBjaGVjaywgcmVjcmVhdGVzIEB7bGluayBNYXNrZWR9IG1vZGVsIGlmIG1hc2sgdHlwZSBjaGFuZ2VzICovXG4gIHVwZGF0ZU9wdGlvbnMob3B0cykge1xuICAgIGNvbnN0IHtcbiAgICAgICAgbWFza1xuICAgICAgfSA9IG9wdHMsXG4gICAgICByZXN0T3B0cyA9IF9vYmplY3RXaXRob3V0UHJvcGVydGllc0xvb3NlKG9wdHMsIF9leGNsdWRlZCk7XG4gICAgY29uc3QgdXBkYXRlTWFzayA9ICF0aGlzLm1hc2tFcXVhbHMobWFzayk7XG4gICAgY29uc3QgdXBkYXRlT3B0cyA9ICFvYmplY3RJbmNsdWRlcyh0aGlzLm1hc2tlZCwgcmVzdE9wdHMpO1xuICAgIGlmICh1cGRhdGVNYXNrKSB0aGlzLm1hc2sgPSBtYXNrO1xuICAgIGlmICh1cGRhdGVPcHRzKSB0aGlzLm1hc2tlZC51cGRhdGVPcHRpb25zKHJlc3RPcHRzKTtcbiAgICBpZiAodXBkYXRlTWFzayB8fCB1cGRhdGVPcHRzKSB0aGlzLnVwZGF0ZUNvbnRyb2woKTtcbiAgfVxuXG4gIC8qKiBVcGRhdGVzIGN1cnNvciAqL1xuICB1cGRhdGVDdXJzb3IoY3Vyc29yUG9zKSB7XG4gICAgaWYgKGN1cnNvclBvcyA9PSBudWxsKSByZXR1cm47XG4gICAgdGhpcy5jdXJzb3JQb3MgPSBjdXJzb3JQb3M7XG5cbiAgICAvLyBhbHNvIHF1ZXVlIGNoYW5nZSBjdXJzb3IgZm9yIG1vYmlsZSBicm93c2Vyc1xuICAgIHRoaXMuX2RlbGF5VXBkYXRlQ3Vyc29yKGN1cnNvclBvcyk7XG4gIH1cblxuICAvKipcbiAgICBEZWxheXMgY3Vyc29yIHVwZGF0ZSB0byBzdXBwb3J0IG1vYmlsZSBicm93c2Vyc1xuICAgIEBwcml2YXRlXG4gICovXG4gIF9kZWxheVVwZGF0ZUN1cnNvcihjdXJzb3JQb3MpIHtcbiAgICB0aGlzLl9hYm9ydFVwZGF0ZUN1cnNvcigpO1xuICAgIHRoaXMuX2NoYW5naW5nQ3Vyc29yUG9zID0gY3Vyc29yUG9zO1xuICAgIHRoaXMuX2N1cnNvckNoYW5naW5nID0gc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICBpZiAoIXRoaXMuZWwpIHJldHVybjsgLy8gaWYgd2FzIGRlc3Ryb3llZFxuICAgICAgdGhpcy5jdXJzb3JQb3MgPSB0aGlzLl9jaGFuZ2luZ0N1cnNvclBvcztcbiAgICAgIHRoaXMuX2Fib3J0VXBkYXRlQ3Vyc29yKCk7XG4gICAgfSwgMTApO1xuICB9XG5cbiAgLyoqXG4gICAgRmlyZXMgY3VzdG9tIGV2ZW50c1xuICAgIEBwcm90ZWN0ZWRcbiAgKi9cbiAgX2ZpcmVDaGFuZ2VFdmVudHMoKSB7XG4gICAgdGhpcy5fZmlyZUV2ZW50KCdhY2NlcHQnLCB0aGlzLl9pbnB1dEV2ZW50KTtcbiAgICBpZiAodGhpcy5tYXNrZWQuaXNDb21wbGV0ZSkgdGhpcy5fZmlyZUV2ZW50KCdjb21wbGV0ZScsIHRoaXMuX2lucHV0RXZlbnQpO1xuICB9XG5cbiAgLyoqXG4gICAgQWJvcnRzIGRlbGF5ZWQgY3Vyc29yIHVwZGF0ZVxuICAgIEBwcml2YXRlXG4gICovXG4gIF9hYm9ydFVwZGF0ZUN1cnNvcigpIHtcbiAgICBpZiAodGhpcy5fY3Vyc29yQ2hhbmdpbmcpIHtcbiAgICAgIGNsZWFyVGltZW91dCh0aGlzLl9jdXJzb3JDaGFuZ2luZyk7XG4gICAgICBkZWxldGUgdGhpcy5fY3Vyc29yQ2hhbmdpbmc7XG4gICAgfVxuICB9XG5cbiAgLyoqIEFsaWducyBjdXJzb3IgdG8gbmVhcmVzdCBhdmFpbGFibGUgcG9zaXRpb24gKi9cbiAgYWxpZ25DdXJzb3IoKSB7XG4gICAgdGhpcy5jdXJzb3JQb3MgPSB0aGlzLm1hc2tlZC5uZWFyZXN0SW5wdXRQb3ModGhpcy5tYXNrZWQubmVhcmVzdElucHV0UG9zKHRoaXMuY3Vyc29yUG9zLCBESVJFQ1RJT04uTEVGVCkpO1xuICB9XG5cbiAgLyoqIEFsaWducyBjdXJzb3Igb25seSBpZiBzZWxlY3Rpb24gaXMgZW1wdHkgKi9cbiAgYWxpZ25DdXJzb3JGcmllbmRseSgpIHtcbiAgICBpZiAodGhpcy5zZWxlY3Rpb25TdGFydCAhPT0gdGhpcy5jdXJzb3JQb3MpIHJldHVybjsgLy8gc2tpcCBpZiByYW5nZSBpcyBzZWxlY3RlZFxuICAgIHRoaXMuYWxpZ25DdXJzb3IoKTtcbiAgfVxuXG4gIC8qKiBBZGRzIGxpc3RlbmVyIG9uIGN1c3RvbSBldmVudCAqL1xuICBvbihldiwgaGFuZGxlcikge1xuICAgIGlmICghdGhpcy5fbGlzdGVuZXJzW2V2XSkgdGhpcy5fbGlzdGVuZXJzW2V2XSA9IFtdO1xuICAgIHRoaXMuX2xpc3RlbmVyc1tldl0ucHVzaChoYW5kbGVyKTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8qKiBSZW1vdmVzIGN1c3RvbSBldmVudCBsaXN0ZW5lciAqL1xuICBvZmYoZXYsIGhhbmRsZXIpIHtcbiAgICBpZiAoIXRoaXMuX2xpc3RlbmVyc1tldl0pIHJldHVybiB0aGlzO1xuICAgIGlmICghaGFuZGxlcikge1xuICAgICAgZGVsZXRlIHRoaXMuX2xpc3RlbmVyc1tldl07XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gICAgY29uc3QgaEluZGV4ID0gdGhpcy5fbGlzdGVuZXJzW2V2XS5pbmRleE9mKGhhbmRsZXIpO1xuICAgIGlmIChoSW5kZXggPj0gMCkgdGhpcy5fbGlzdGVuZXJzW2V2XS5zcGxpY2UoaEluZGV4LCAxKTtcbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8qKiBIYW5kbGVzIHZpZXcgaW5wdXQgZXZlbnQgKi9cbiAgX29uSW5wdXQoZSkge1xuICAgIHRoaXMuX2lucHV0RXZlbnQgPSBlO1xuICAgIHRoaXMuX2Fib3J0VXBkYXRlQ3Vyc29yKCk7XG5cbiAgICAvLyBmaXggc3RyYW5nZSBJRSBiZWhhdmlvclxuICAgIGlmICghdGhpcy5fc2VsZWN0aW9uKSByZXR1cm4gdGhpcy51cGRhdGVWYWx1ZSgpO1xuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQWN0aW9uRGV0YWlscyhcbiAgICAvLyBuZXcgc3RhdGVcbiAgICB0aGlzLmVsLnZhbHVlLCB0aGlzLmN1cnNvclBvcyxcbiAgICAvLyBvbGQgc3RhdGVcbiAgICB0aGlzLmRpc3BsYXlWYWx1ZSwgdGhpcy5fc2VsZWN0aW9uKTtcbiAgICBjb25zdCBvbGRSYXdWYWx1ZSA9IHRoaXMubWFza2VkLnJhd0lucHV0VmFsdWU7XG4gICAgY29uc3Qgb2Zmc2V0ID0gdGhpcy5tYXNrZWQuc3BsaWNlKGRldGFpbHMuc3RhcnRDaGFuZ2VQb3MsIGRldGFpbHMucmVtb3ZlZC5sZW5ndGgsIGRldGFpbHMuaW5zZXJ0ZWQsIGRldGFpbHMucmVtb3ZlRGlyZWN0aW9uLCB7XG4gICAgICBpbnB1dDogdHJ1ZSxcbiAgICAgIHJhdzogdHJ1ZVxuICAgIH0pLm9mZnNldDtcblxuICAgIC8vIGZvcmNlIGFsaWduIGluIHJlbW92ZSBkaXJlY3Rpb24gb25seSBpZiBubyBpbnB1dCBjaGFycyB3ZXJlIHJlbW92ZWRcbiAgICAvLyBvdGhlcndpc2Ugd2Ugc3RpbGwgbmVlZCB0byBhbGlnbiB3aXRoIE5PTkUgKHRvIGdldCBvdXQgZnJvbSBmaXhlZCBzeW1ib2xzIGZvciBpbnN0YW5jZSlcbiAgICBjb25zdCByZW1vdmVEaXJlY3Rpb24gPSBvbGRSYXdWYWx1ZSA9PT0gdGhpcy5tYXNrZWQucmF3SW5wdXRWYWx1ZSA/IGRldGFpbHMucmVtb3ZlRGlyZWN0aW9uIDogRElSRUNUSU9OLk5PTkU7XG4gICAgbGV0IGN1cnNvclBvcyA9IHRoaXMubWFza2VkLm5lYXJlc3RJbnB1dFBvcyhkZXRhaWxzLnN0YXJ0Q2hhbmdlUG9zICsgb2Zmc2V0LCByZW1vdmVEaXJlY3Rpb24pO1xuICAgIGlmIChyZW1vdmVEaXJlY3Rpb24gIT09IERJUkVDVElPTi5OT05FKSBjdXJzb3JQb3MgPSB0aGlzLm1hc2tlZC5uZWFyZXN0SW5wdXRQb3MoY3Vyc29yUG9zLCBESVJFQ1RJT04uTk9ORSk7XG4gICAgdGhpcy51cGRhdGVDb250cm9sKCk7XG4gICAgdGhpcy51cGRhdGVDdXJzb3IoY3Vyc29yUG9zKTtcbiAgICBkZWxldGUgdGhpcy5faW5wdXRFdmVudDtcbiAgfVxuXG4gIC8qKiBIYW5kbGVzIHZpZXcgY2hhbmdlIGV2ZW50IGFuZCBjb21taXRzIG1vZGVsIHZhbHVlICovXG4gIF9vbkNoYW5nZSgpIHtcbiAgICBpZiAodGhpcy5kaXNwbGF5VmFsdWUgIT09IHRoaXMuZWwudmFsdWUpIHtcbiAgICAgIHRoaXMudXBkYXRlVmFsdWUoKTtcbiAgICB9XG4gICAgdGhpcy5tYXNrZWQuZG9Db21taXQoKTtcbiAgICB0aGlzLnVwZGF0ZUNvbnRyb2woKTtcbiAgICB0aGlzLl9zYXZlU2VsZWN0aW9uKCk7XG4gIH1cblxuICAvKiogSGFuZGxlcyB2aWV3IGRyb3AgZXZlbnQsIHByZXZlbnRzIGJ5IGRlZmF1bHQgKi9cbiAgX29uRHJvcChldikge1xuICAgIGV2LnByZXZlbnREZWZhdWx0KCk7XG4gICAgZXYuc3RvcFByb3BhZ2F0aW9uKCk7XG4gIH1cblxuICAvKiogUmVzdG9yZSBsYXN0IHNlbGVjdGlvbiBvbiBmb2N1cyAqL1xuICBfb25Gb2N1cyhldikge1xuICAgIHRoaXMuYWxpZ25DdXJzb3JGcmllbmRseSgpO1xuICB9XG5cbiAgLyoqIFJlc3RvcmUgbGFzdCBzZWxlY3Rpb24gb24gZm9jdXMgKi9cbiAgX29uQ2xpY2soZXYpIHtcbiAgICB0aGlzLmFsaWduQ3Vyc29yRnJpZW5kbHkoKTtcbiAgfVxuXG4gIC8qKiBVbmJpbmQgdmlldyBldmVudHMgYW5kIHJlbW92ZXMgZWxlbWVudCByZWZlcmVuY2UgKi9cbiAgZGVzdHJveSgpIHtcbiAgICB0aGlzLl91bmJpbmRFdmVudHMoKTtcbiAgICAvLyAkRmxvd0ZpeE1lIHdoeSBub3QgZG8gc28/XG4gICAgdGhpcy5fbGlzdGVuZXJzLmxlbmd0aCA9IDA7XG4gICAgLy8gJEZsb3dGaXhNZVxuICAgIGRlbGV0ZSB0aGlzLmVsO1xuICB9XG59XG5JTWFzay5JbnB1dE1hc2sgPSBJbnB1dE1hc2s7XG5cbmV4cG9ydCB7IElucHV0TWFzayBhcyBkZWZhdWx0IH07XG4iLCAiaW1wb3J0IE1hc2tlZFBhdHRlcm4gZnJvbSAnLi9wYXR0ZXJuLmpzJztcbmltcG9ydCBJTWFzayBmcm9tICcuLi9jb3JlL2hvbGRlci5qcyc7XG5pbXBvcnQgJy4uL19yb2xsdXBQbHVnaW5CYWJlbEhlbHBlcnMtNmIzYmQ0MDQuanMnO1xuaW1wb3J0ICcuLi9jb3JlL3V0aWxzLmpzJztcbmltcG9ydCAnLi4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5pbXBvcnQgJy4vYmFzZS5qcyc7XG5pbXBvcnQgJy4uL2NvcmUvY29udGludW91cy10YWlsLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuL3BhdHRlcm4vaW5wdXQtZGVmaW5pdGlvbi5qcyc7XG5pbXBvcnQgJy4vZmFjdG9yeS5qcyc7XG5pbXBvcnQgJy4vcGF0dGVybi9maXhlZC1kZWZpbml0aW9uLmpzJztcbmltcG9ydCAnLi9wYXR0ZXJuL2NodW5rLXRhaWwtZGV0YWlscy5qcyc7XG5pbXBvcnQgJy4vcGF0dGVybi9jdXJzb3IuanMnO1xuaW1wb3J0ICcuL3JlZ2V4cC5qcyc7XG5cbi8qKiBQYXR0ZXJuIHdoaWNoIHZhbGlkYXRlcyBlbnVtIHZhbHVlcyAqL1xuY2xhc3MgTWFza2VkRW51bSBleHRlbmRzIE1hc2tlZFBhdHRlcm4ge1xuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgICBAcGFyYW0ge09iamVjdH0gb3B0c1xuICAqL1xuICBfdXBkYXRlKG9wdHMpIHtcbiAgICAvLyBUT0RPIHR5cGVcbiAgICBpZiAob3B0cy5lbnVtKSBvcHRzLm1hc2sgPSAnKicucmVwZWF0KG9wdHMuZW51bVswXS5sZW5ndGgpO1xuICAgIHN1cGVyLl91cGRhdGUob3B0cyk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZG9WYWxpZGF0ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5lbnVtLnNvbWUoZSA9PiBlLmluZGV4T2YodGhpcy51bm1hc2tlZFZhbHVlKSA+PSAwKSAmJiBzdXBlci5kb1ZhbGlkYXRlKC4uLmFyZ3VtZW50cyk7XG4gIH1cbn1cbklNYXNrLk1hc2tlZEVudW0gPSBNYXNrZWRFbnVtO1xuXG5leHBvcnQgeyBNYXNrZWRFbnVtIGFzIGRlZmF1bHQgfTtcbiIsICJpbXBvcnQgeyBlc2NhcGVSZWdFeHAsIG5vcm1hbGl6ZVByZXBhcmUsIERJUkVDVElPTiB9IGZyb20gJy4uL2NvcmUvdXRpbHMuanMnO1xuaW1wb3J0IENoYW5nZURldGFpbHMgZnJvbSAnLi4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5pbXBvcnQgTWFza2VkIGZyb20gJy4vYmFzZS5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuaW1wb3J0ICcuLi9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzJztcblxuLyoqXG4gIE51bWJlciBtYXNrXG4gIEBwYXJhbSB7T2JqZWN0fSBvcHRzXG4gIEBwYXJhbSB7c3RyaW5nfSBvcHRzLnJhZGl4IC0gU2luZ2xlIGNoYXJcbiAgQHBhcmFtIHtzdHJpbmd9IG9wdHMudGhvdXNhbmRzU2VwYXJhdG9yIC0gU2luZ2xlIGNoYXJcbiAgQHBhcmFtIHtBcnJheTxzdHJpbmc+fSBvcHRzLm1hcFRvUmFkaXggLSBBcnJheSBvZiBzaW5nbGUgY2hhcnNcbiAgQHBhcmFtIHtudW1iZXJ9IG9wdHMubWluXG4gIEBwYXJhbSB7bnVtYmVyfSBvcHRzLm1heFxuICBAcGFyYW0ge251bWJlcn0gb3B0cy5zY2FsZSAtIERpZ2l0cyBhZnRlciBwb2ludFxuICBAcGFyYW0ge2Jvb2xlYW59IG9wdHMuc2lnbmVkIC0gQWxsb3cgbmVnYXRpdmVcbiAgQHBhcmFtIHtib29sZWFufSBvcHRzLm5vcm1hbGl6ZVplcm9zIC0gRmxhZyB0byByZW1vdmUgbGVhZGluZyBhbmQgdHJhaWxpbmcgemVyb3MgaW4gdGhlIGVuZCBvZiBlZGl0aW5nXG4gIEBwYXJhbSB7Ym9vbGVhbn0gb3B0cy5wYWRGcmFjdGlvbmFsWmVyb3MgLSBGbGFnIHRvIHBhZCB0cmFpbGluZyB6ZXJvcyBhZnRlciBwb2ludCBpbiB0aGUgZW5kIG9mIGVkaXRpbmdcbiovXG5jbGFzcyBNYXNrZWROdW1iZXIgZXh0ZW5kcyBNYXNrZWQge1xuICAvKiogU2luZ2xlIGNoYXIgKi9cblxuICAvKiogU2luZ2xlIGNoYXIgKi9cblxuICAvKiogQXJyYXkgb2Ygc2luZ2xlIGNoYXJzICovXG5cbiAgLyoqICovXG5cbiAgLyoqICovXG5cbiAgLyoqIERpZ2l0cyBhZnRlciBwb2ludCAqL1xuXG4gIC8qKiAqL1xuXG4gIC8qKiBGbGFnIHRvIHJlbW92ZSBsZWFkaW5nIGFuZCB0cmFpbGluZyB6ZXJvcyBpbiB0aGUgZW5kIG9mIGVkaXRpbmcgKi9cblxuICAvKiogRmxhZyB0byBwYWQgdHJhaWxpbmcgemVyb3MgYWZ0ZXIgcG9pbnQgaW4gdGhlIGVuZCBvZiBlZGl0aW5nICovXG5cbiAgY29uc3RydWN0b3Iob3B0cykge1xuICAgIHN1cGVyKE9iamVjdC5hc3NpZ24oe30sIE1hc2tlZE51bWJlci5ERUZBVUxUUywgb3B0cykpO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIF91cGRhdGUob3B0cykge1xuICAgIHN1cGVyLl91cGRhdGUob3B0cyk7XG4gICAgdGhpcy5fdXBkYXRlUmVnRXhwcygpO1xuICB9XG5cbiAgLyoqICovXG4gIF91cGRhdGVSZWdFeHBzKCkge1xuICAgIGxldCBzdGFydCA9ICdeJyArICh0aGlzLmFsbG93TmVnYXRpdmUgPyAnWyt8XFxcXC1dPycgOiAnJyk7XG4gICAgbGV0IG1pZCA9ICdcXFxcZConO1xuICAgIGxldCBlbmQgPSAodGhpcy5zY2FsZSA/IFwiKFwiLmNvbmNhdChlc2NhcGVSZWdFeHAodGhpcy5yYWRpeCksIFwiXFxcXGR7MCxcIikuY29uY2F0KHRoaXMuc2NhbGUsIFwifSk/XCIpIDogJycpICsgJyQnO1xuICAgIHRoaXMuX251bWJlclJlZ0V4cCA9IG5ldyBSZWdFeHAoc3RhcnQgKyBtaWQgKyBlbmQpO1xuICAgIHRoaXMuX21hcFRvUmFkaXhSZWdFeHAgPSBuZXcgUmVnRXhwKFwiW1wiLmNvbmNhdCh0aGlzLm1hcFRvUmFkaXgubWFwKGVzY2FwZVJlZ0V4cCkuam9pbignJyksIFwiXVwiKSwgJ2cnKTtcbiAgICB0aGlzLl90aG91c2FuZHNTZXBhcmF0b3JSZWdFeHAgPSBuZXcgUmVnRXhwKGVzY2FwZVJlZ0V4cCh0aGlzLnRob3VzYW5kc1NlcGFyYXRvciksICdnJyk7XG4gIH1cblxuICAvKiogKi9cbiAgX3JlbW92ZVRob3VzYW5kc1NlcGFyYXRvcnModmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUucmVwbGFjZSh0aGlzLl90aG91c2FuZHNTZXBhcmF0b3JSZWdFeHAsICcnKTtcbiAgfVxuXG4gIC8qKiAqL1xuICBfaW5zZXJ0VGhvdXNhbmRzU2VwYXJhdG9ycyh2YWx1ZSkge1xuICAgIC8vIGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vcXVlc3Rpb25zLzI5MDExMDIvaG93LXRvLXByaW50LWEtbnVtYmVyLXdpdGgtY29tbWFzLWFzLXRob3VzYW5kcy1zZXBhcmF0b3JzLWluLWphdmFzY3JpcHRcbiAgICBjb25zdCBwYXJ0cyA9IHZhbHVlLnNwbGl0KHRoaXMucmFkaXgpO1xuICAgIHBhcnRzWzBdID0gcGFydHNbMF0ucmVwbGFjZSgvXFxCKD89KFxcZHszfSkrKD8hXFxkKSkvZywgdGhpcy50aG91c2FuZHNTZXBhcmF0b3IpO1xuICAgIHJldHVybiBwYXJ0cy5qb2luKHRoaXMucmFkaXgpO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGRvUHJlcGFyZShjaCkge1xuICAgIGxldCBmbGFncyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDoge307XG4gICAgY2ggPSB0aGlzLl9yZW1vdmVUaG91c2FuZHNTZXBhcmF0b3JzKHRoaXMuc2NhbGUgJiYgdGhpcy5tYXBUb1JhZGl4Lmxlbmd0aCAmJiAoXG4gICAgLypcbiAgICAgIHJhZGl4IHNob3VsZCBiZSBtYXBwZWQgd2hlblxuICAgICAgMSkgaW5wdXQgaXMgZG9uZSBmcm9tIGtleWJvYXJkID0gZmxhZ3MuaW5wdXQgJiYgZmxhZ3MucmF3XG4gICAgICAyKSB1bm1hc2tlZCB2YWx1ZSBpcyBzZXQgPSAhZmxhZ3MuaW5wdXQgJiYgIWZsYWdzLnJhd1xuICAgICAgYW5kIHNob3VsZCBub3QgYmUgbWFwcGVkIHdoZW5cbiAgICAgIDEpIHZhbHVlIGlzIHNldCA9IGZsYWdzLmlucHV0ICYmICFmbGFncy5yYXdcbiAgICAgIDIpIHJhdyB2YWx1ZSBpcyBzZXQgPSAhZmxhZ3MuaW5wdXQgJiYgZmxhZ3MucmF3XG4gICAgKi9cbiAgICBmbGFncy5pbnB1dCAmJiBmbGFncy5yYXcgfHwgIWZsYWdzLmlucHV0ICYmICFmbGFncy5yYXcpID8gY2gucmVwbGFjZSh0aGlzLl9tYXBUb1JhZGl4UmVnRXhwLCB0aGlzLnJhZGl4KSA6IGNoKTtcbiAgICBjb25zdCBbcHJlcENoLCBkZXRhaWxzXSA9IG5vcm1hbGl6ZVByZXBhcmUoc3VwZXIuZG9QcmVwYXJlKGNoLCBmbGFncykpO1xuICAgIGlmIChjaCAmJiAhcHJlcENoKSBkZXRhaWxzLnNraXAgPSB0cnVlO1xuICAgIHJldHVybiBbcHJlcENoLCBkZXRhaWxzXTtcbiAgfVxuXG4gIC8qKiAqL1xuICBfc2VwYXJhdG9yc0NvdW50KHRvKSB7XG4gICAgbGV0IGV4dGVuZE9uU2VwYXJhdG9ycyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDogZmFsc2U7XG4gICAgbGV0IGNvdW50ID0gMDtcbiAgICBmb3IgKGxldCBwb3MgPSAwOyBwb3MgPCB0bzsgKytwb3MpIHtcbiAgICAgIGlmICh0aGlzLl92YWx1ZS5pbmRleE9mKHRoaXMudGhvdXNhbmRzU2VwYXJhdG9yLCBwb3MpID09PSBwb3MpIHtcbiAgICAgICAgKytjb3VudDtcbiAgICAgICAgaWYgKGV4dGVuZE9uU2VwYXJhdG9ycykgdG8gKz0gdGhpcy50aG91c2FuZHNTZXBhcmF0b3IubGVuZ3RoO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gY291bnQ7XG4gIH1cblxuICAvKiogKi9cbiAgX3NlcGFyYXRvcnNDb3VudEZyb21TbGljZSgpIHtcbiAgICBsZXQgc2xpY2UgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IHRoaXMuX3ZhbHVlO1xuICAgIHJldHVybiB0aGlzLl9zZXBhcmF0b3JzQ291bnQodGhpcy5fcmVtb3ZlVGhvdXNhbmRzU2VwYXJhdG9ycyhzbGljZSkubGVuZ3RoLCB0cnVlKTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBleHRyYWN0SW5wdXQoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICBsZXQgZmxhZ3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMiA/IGFyZ3VtZW50c1syXSA6IHVuZGVmaW5lZDtcbiAgICBbZnJvbVBvcywgdG9Qb3NdID0gdGhpcy5fYWRqdXN0UmFuZ2VXaXRoU2VwYXJhdG9ycyhmcm9tUG9zLCB0b1Bvcyk7XG4gICAgcmV0dXJuIHRoaXMuX3JlbW92ZVRob3VzYW5kc1NlcGFyYXRvcnMoc3VwZXIuZXh0cmFjdElucHV0KGZyb21Qb3MsIHRvUG9zLCBmbGFncykpO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIF9hcHBlbmRDaGFyUmF3KGNoKSB7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB7fTtcbiAgICBpZiAoIXRoaXMudGhvdXNhbmRzU2VwYXJhdG9yKSByZXR1cm4gc3VwZXIuX2FwcGVuZENoYXJSYXcoY2gsIGZsYWdzKTtcbiAgICBjb25zdCBwcmV2QmVmb3JlVGFpbFZhbHVlID0gZmxhZ3MudGFpbCAmJiBmbGFncy5fYmVmb3JlVGFpbFN0YXRlID8gZmxhZ3MuX2JlZm9yZVRhaWxTdGF0ZS5fdmFsdWUgOiB0aGlzLl92YWx1ZTtcbiAgICBjb25zdCBwcmV2QmVmb3JlVGFpbFNlcGFyYXRvcnNDb3VudCA9IHRoaXMuX3NlcGFyYXRvcnNDb3VudEZyb21TbGljZShwcmV2QmVmb3JlVGFpbFZhbHVlKTtcbiAgICB0aGlzLl92YWx1ZSA9IHRoaXMuX3JlbW92ZVRob3VzYW5kc1NlcGFyYXRvcnModGhpcy52YWx1ZSk7XG4gICAgY29uc3QgYXBwZW5kRGV0YWlscyA9IHN1cGVyLl9hcHBlbmRDaGFyUmF3KGNoLCBmbGFncyk7XG4gICAgdGhpcy5fdmFsdWUgPSB0aGlzLl9pbnNlcnRUaG91c2FuZHNTZXBhcmF0b3JzKHRoaXMuX3ZhbHVlKTtcbiAgICBjb25zdCBiZWZvcmVUYWlsVmFsdWUgPSBmbGFncy50YWlsICYmIGZsYWdzLl9iZWZvcmVUYWlsU3RhdGUgPyBmbGFncy5fYmVmb3JlVGFpbFN0YXRlLl92YWx1ZSA6IHRoaXMuX3ZhbHVlO1xuICAgIGNvbnN0IGJlZm9yZVRhaWxTZXBhcmF0b3JzQ291bnQgPSB0aGlzLl9zZXBhcmF0b3JzQ291bnRGcm9tU2xpY2UoYmVmb3JlVGFpbFZhbHVlKTtcbiAgICBhcHBlbmREZXRhaWxzLnRhaWxTaGlmdCArPSAoYmVmb3JlVGFpbFNlcGFyYXRvcnNDb3VudCAtIHByZXZCZWZvcmVUYWlsU2VwYXJhdG9yc0NvdW50KSAqIHRoaXMudGhvdXNhbmRzU2VwYXJhdG9yLmxlbmd0aDtcbiAgICBhcHBlbmREZXRhaWxzLnNraXAgPSAhYXBwZW5kRGV0YWlscy5yYXdJbnNlcnRlZCAmJiBjaCA9PT0gdGhpcy50aG91c2FuZHNTZXBhcmF0b3I7XG4gICAgcmV0dXJuIGFwcGVuZERldGFpbHM7XG4gIH1cblxuICAvKiogKi9cbiAgX2ZpbmRTZXBhcmF0b3JBcm91bmQocG9zKSB7XG4gICAgaWYgKHRoaXMudGhvdXNhbmRzU2VwYXJhdG9yKSB7XG4gICAgICBjb25zdCBzZWFyY2hGcm9tID0gcG9zIC0gdGhpcy50aG91c2FuZHNTZXBhcmF0b3IubGVuZ3RoICsgMTtcbiAgICAgIGNvbnN0IHNlcGFyYXRvclBvcyA9IHRoaXMudmFsdWUuaW5kZXhPZih0aGlzLnRob3VzYW5kc1NlcGFyYXRvciwgc2VhcmNoRnJvbSk7XG4gICAgICBpZiAoc2VwYXJhdG9yUG9zIDw9IHBvcykgcmV0dXJuIHNlcGFyYXRvclBvcztcbiAgICB9XG4gICAgcmV0dXJuIC0xO1xuICB9XG4gIF9hZGp1c3RSYW5nZVdpdGhTZXBhcmF0b3JzKGZyb20sIHRvKSB7XG4gICAgY29uc3Qgc2VwYXJhdG9yQXJvdW5kRnJvbVBvcyA9IHRoaXMuX2ZpbmRTZXBhcmF0b3JBcm91bmQoZnJvbSk7XG4gICAgaWYgKHNlcGFyYXRvckFyb3VuZEZyb21Qb3MgPj0gMCkgZnJvbSA9IHNlcGFyYXRvckFyb3VuZEZyb21Qb3M7XG4gICAgY29uc3Qgc2VwYXJhdG9yQXJvdW5kVG9Qb3MgPSB0aGlzLl9maW5kU2VwYXJhdG9yQXJvdW5kKHRvKTtcbiAgICBpZiAoc2VwYXJhdG9yQXJvdW5kVG9Qb3MgPj0gMCkgdG8gPSBzZXBhcmF0b3JBcm91bmRUb1BvcyArIHRoaXMudGhvdXNhbmRzU2VwYXJhdG9yLmxlbmd0aDtcbiAgICByZXR1cm4gW2Zyb20sIHRvXTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICByZW1vdmUoKSB7XG4gICAgbGV0IGZyb21Qb3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6IDA7XG4gICAgbGV0IHRvUG9zID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB0aGlzLnZhbHVlLmxlbmd0aDtcbiAgICBbZnJvbVBvcywgdG9Qb3NdID0gdGhpcy5fYWRqdXN0UmFuZ2VXaXRoU2VwYXJhdG9ycyhmcm9tUG9zLCB0b1Bvcyk7XG4gICAgY29uc3QgdmFsdWVCZWZvcmVQb3MgPSB0aGlzLnZhbHVlLnNsaWNlKDAsIGZyb21Qb3MpO1xuICAgIGNvbnN0IHZhbHVlQWZ0ZXJQb3MgPSB0aGlzLnZhbHVlLnNsaWNlKHRvUG9zKTtcbiAgICBjb25zdCBwcmV2QmVmb3JlVGFpbFNlcGFyYXRvcnNDb3VudCA9IHRoaXMuX3NlcGFyYXRvcnNDb3VudCh2YWx1ZUJlZm9yZVBvcy5sZW5ndGgpO1xuICAgIHRoaXMuX3ZhbHVlID0gdGhpcy5faW5zZXJ0VGhvdXNhbmRzU2VwYXJhdG9ycyh0aGlzLl9yZW1vdmVUaG91c2FuZHNTZXBhcmF0b3JzKHZhbHVlQmVmb3JlUG9zICsgdmFsdWVBZnRlclBvcykpO1xuICAgIGNvbnN0IGJlZm9yZVRhaWxTZXBhcmF0b3JzQ291bnQgPSB0aGlzLl9zZXBhcmF0b3JzQ291bnRGcm9tU2xpY2UodmFsdWVCZWZvcmVQb3MpO1xuICAgIHJldHVybiBuZXcgQ2hhbmdlRGV0YWlscyh7XG4gICAgICB0YWlsU2hpZnQ6IChiZWZvcmVUYWlsU2VwYXJhdG9yc0NvdW50IC0gcHJldkJlZm9yZVRhaWxTZXBhcmF0b3JzQ291bnQpICogdGhpcy50aG91c2FuZHNTZXBhcmF0b3IubGVuZ3RoXG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgbmVhcmVzdElucHV0UG9zKGN1cnNvclBvcywgZGlyZWN0aW9uKSB7XG4gICAgaWYgKCF0aGlzLnRob3VzYW5kc1NlcGFyYXRvcikgcmV0dXJuIGN1cnNvclBvcztcbiAgICBzd2l0Y2ggKGRpcmVjdGlvbikge1xuICAgICAgY2FzZSBESVJFQ1RJT04uTk9ORTpcbiAgICAgIGNhc2UgRElSRUNUSU9OLkxFRlQ6XG4gICAgICBjYXNlIERJUkVDVElPTi5GT1JDRV9MRUZUOlxuICAgICAgICB7XG4gICAgICAgICAgY29uc3Qgc2VwYXJhdG9yQXRMZWZ0UG9zID0gdGhpcy5fZmluZFNlcGFyYXRvckFyb3VuZChjdXJzb3JQb3MgLSAxKTtcbiAgICAgICAgICBpZiAoc2VwYXJhdG9yQXRMZWZ0UG9zID49IDApIHtcbiAgICAgICAgICAgIGNvbnN0IHNlcGFyYXRvckF0TGVmdEVuZFBvcyA9IHNlcGFyYXRvckF0TGVmdFBvcyArIHRoaXMudGhvdXNhbmRzU2VwYXJhdG9yLmxlbmd0aDtcbiAgICAgICAgICAgIGlmIChjdXJzb3JQb3MgPCBzZXBhcmF0b3JBdExlZnRFbmRQb3MgfHwgdGhpcy52YWx1ZS5sZW5ndGggPD0gc2VwYXJhdG9yQXRMZWZ0RW5kUG9zIHx8IGRpcmVjdGlvbiA9PT0gRElSRUNUSU9OLkZPUkNFX0xFRlQpIHtcbiAgICAgICAgICAgICAgcmV0dXJuIHNlcGFyYXRvckF0TGVmdFBvcztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cbiAgICAgIGNhc2UgRElSRUNUSU9OLlJJR0hUOlxuICAgICAgY2FzZSBESVJFQ1RJT04uRk9SQ0VfUklHSFQ6XG4gICAgICAgIHtcbiAgICAgICAgICBjb25zdCBzZXBhcmF0b3JBdFJpZ2h0UG9zID0gdGhpcy5fZmluZFNlcGFyYXRvckFyb3VuZChjdXJzb3JQb3MpO1xuICAgICAgICAgIGlmIChzZXBhcmF0b3JBdFJpZ2h0UG9zID49IDApIHtcbiAgICAgICAgICAgIHJldHVybiBzZXBhcmF0b3JBdFJpZ2h0UG9zICsgdGhpcy50aG91c2FuZHNTZXBhcmF0b3IubGVuZ3RoO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gY3Vyc29yUG9zO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGRvVmFsaWRhdGUoZmxhZ3MpIHtcbiAgICAvLyB2YWxpZGF0ZSBhcyBzdHJpbmdcbiAgICBsZXQgdmFsaWQgPSBCb29sZWFuKHRoaXMuX3JlbW92ZVRob3VzYW5kc1NlcGFyYXRvcnModGhpcy52YWx1ZSkubWF0Y2godGhpcy5fbnVtYmVyUmVnRXhwKSk7XG4gICAgaWYgKHZhbGlkKSB7XG4gICAgICAvLyB2YWxpZGF0ZSBhcyBudW1iZXJcbiAgICAgIGNvbnN0IG51bWJlciA9IHRoaXMubnVtYmVyO1xuICAgICAgdmFsaWQgPSB2YWxpZCAmJiAhaXNOYU4obnVtYmVyKSAmJiAoXG4gICAgICAvLyBjaGVjayBtaW4gYm91bmQgZm9yIG5lZ2F0aXZlIHZhbHVlc1xuICAgICAgdGhpcy5taW4gPT0gbnVsbCB8fCB0aGlzLm1pbiA+PSAwIHx8IHRoaXMubWluIDw9IHRoaXMubnVtYmVyKSAmJiAoXG4gICAgICAvLyBjaGVjayBtYXggYm91bmQgZm9yIHBvc2l0aXZlIHZhbHVlc1xuICAgICAgdGhpcy5tYXggPT0gbnVsbCB8fCB0aGlzLm1heCA8PSAwIHx8IHRoaXMubnVtYmVyIDw9IHRoaXMubWF4KTtcbiAgICB9XG4gICAgcmV0dXJuIHZhbGlkICYmIHN1cGVyLmRvVmFsaWRhdGUoZmxhZ3MpO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGRvQ29tbWl0KCkge1xuICAgIGlmICh0aGlzLnZhbHVlKSB7XG4gICAgICBjb25zdCBudW1iZXIgPSB0aGlzLm51bWJlcjtcbiAgICAgIGxldCB2YWxpZG51bSA9IG51bWJlcjtcblxuICAgICAgLy8gY2hlY2sgYm91bmRzXG4gICAgICBpZiAodGhpcy5taW4gIT0gbnVsbCkgdmFsaWRudW0gPSBNYXRoLm1heCh2YWxpZG51bSwgdGhpcy5taW4pO1xuICAgICAgaWYgKHRoaXMubWF4ICE9IG51bGwpIHZhbGlkbnVtID0gTWF0aC5taW4odmFsaWRudW0sIHRoaXMubWF4KTtcbiAgICAgIGlmICh2YWxpZG51bSAhPT0gbnVtYmVyKSB0aGlzLnVubWFza2VkVmFsdWUgPSB0aGlzLmRvRm9ybWF0KHZhbGlkbnVtKTtcbiAgICAgIGxldCBmb3JtYXR0ZWQgPSB0aGlzLnZhbHVlO1xuICAgICAgaWYgKHRoaXMubm9ybWFsaXplWmVyb3MpIGZvcm1hdHRlZCA9IHRoaXMuX25vcm1hbGl6ZVplcm9zKGZvcm1hdHRlZCk7XG4gICAgICBpZiAodGhpcy5wYWRGcmFjdGlvbmFsWmVyb3MgJiYgdGhpcy5zY2FsZSA+IDApIGZvcm1hdHRlZCA9IHRoaXMuX3BhZEZyYWN0aW9uYWxaZXJvcyhmb3JtYXR0ZWQpO1xuICAgICAgdGhpcy5fdmFsdWUgPSBmb3JtYXR0ZWQ7XG4gICAgfVxuICAgIHN1cGVyLmRvQ29tbWl0KCk7XG4gIH1cblxuICAvKiogKi9cbiAgX25vcm1hbGl6ZVplcm9zKHZhbHVlKSB7XG4gICAgY29uc3QgcGFydHMgPSB0aGlzLl9yZW1vdmVUaG91c2FuZHNTZXBhcmF0b3JzKHZhbHVlKS5zcGxpdCh0aGlzLnJhZGl4KTtcblxuICAgIC8vIHJlbW92ZSBsZWFkaW5nIHplcm9zXG4gICAgcGFydHNbMF0gPSBwYXJ0c1swXS5yZXBsYWNlKC9eKFxcRCopKDAqKShcXGQqKS8sIChtYXRjaCwgc2lnbiwgemVyb3MsIG51bSkgPT4gc2lnbiArIG51bSk7XG4gICAgLy8gYWRkIGxlYWRpbmcgemVyb1xuICAgIGlmICh2YWx1ZS5sZW5ndGggJiYgIS9cXGQkLy50ZXN0KHBhcnRzWzBdKSkgcGFydHNbMF0gPSBwYXJ0c1swXSArICcwJztcbiAgICBpZiAocGFydHMubGVuZ3RoID4gMSkge1xuICAgICAgcGFydHNbMV0gPSBwYXJ0c1sxXS5yZXBsYWNlKC8wKiQvLCAnJyk7IC8vIHJlbW92ZSB0cmFpbGluZyB6ZXJvc1xuICAgICAgaWYgKCFwYXJ0c1sxXS5sZW5ndGgpIHBhcnRzLmxlbmd0aCA9IDE7IC8vIHJlbW92ZSBmcmFjdGlvbmFsXG4gICAgfVxuXG4gICAgcmV0dXJuIHRoaXMuX2luc2VydFRob3VzYW5kc1NlcGFyYXRvcnMocGFydHMuam9pbih0aGlzLnJhZGl4KSk7XG4gIH1cblxuICAvKiogKi9cbiAgX3BhZEZyYWN0aW9uYWxaZXJvcyh2YWx1ZSkge1xuICAgIGlmICghdmFsdWUpIHJldHVybiB2YWx1ZTtcbiAgICBjb25zdCBwYXJ0cyA9IHZhbHVlLnNwbGl0KHRoaXMucmFkaXgpO1xuICAgIGlmIChwYXJ0cy5sZW5ndGggPCAyKSBwYXJ0cy5wdXNoKCcnKTtcbiAgICBwYXJ0c1sxXSA9IHBhcnRzWzFdLnBhZEVuZCh0aGlzLnNjYWxlLCAnMCcpO1xuICAgIHJldHVybiBwYXJ0cy5qb2luKHRoaXMucmFkaXgpO1xuICB9XG5cbiAgLyoqICovXG4gIGRvU2tpcEludmFsaWQoY2gpIHtcbiAgICBsZXQgZmxhZ3MgPSBhcmd1bWVudHMubGVuZ3RoID4gMSAmJiBhcmd1bWVudHNbMV0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1sxXSA6IHt9O1xuICAgIGxldCBjaGVja1RhaWwgPSBhcmd1bWVudHMubGVuZ3RoID4gMiA/IGFyZ3VtZW50c1syXSA6IHVuZGVmaW5lZDtcbiAgICBjb25zdCBkcm9wRnJhY3Rpb25hbCA9IHRoaXMuc2NhbGUgPT09IDAgJiYgY2ggIT09IHRoaXMudGhvdXNhbmRzU2VwYXJhdG9yICYmIChjaCA9PT0gdGhpcy5yYWRpeCB8fCBjaCA9PT0gTWFza2VkTnVtYmVyLlVOTUFTS0VEX1JBRElYIHx8IHRoaXMubWFwVG9SYWRpeC5pbmNsdWRlcyhjaCkpO1xuICAgIHJldHVybiBzdXBlci5kb1NraXBJbnZhbGlkKGNoLCBmbGFncywgY2hlY2tUYWlsKSAmJiAhZHJvcEZyYWN0aW9uYWw7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IHVubWFza2VkVmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMuX3JlbW92ZVRob3VzYW5kc1NlcGFyYXRvcnModGhpcy5fbm9ybWFsaXplWmVyb3ModGhpcy52YWx1ZSkpLnJlcGxhY2UodGhpcy5yYWRpeCwgTWFza2VkTnVtYmVyLlVOTUFTS0VEX1JBRElYKTtcbiAgfVxuICBzZXQgdW5tYXNrZWRWYWx1ZSh1bm1hc2tlZFZhbHVlKSB7XG4gICAgc3VwZXIudW5tYXNrZWRWYWx1ZSA9IHVubWFza2VkVmFsdWU7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IHR5cGVkVmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMuZG9QYXJzZSh0aGlzLnVubWFza2VkVmFsdWUpO1xuICB9XG4gIHNldCB0eXBlZFZhbHVlKG4pIHtcbiAgICB0aGlzLnJhd0lucHV0VmFsdWUgPSB0aGlzLmRvRm9ybWF0KG4pLnJlcGxhY2UoTWFza2VkTnVtYmVyLlVOTUFTS0VEX1JBRElYLCB0aGlzLnJhZGl4KTtcbiAgfVxuXG4gIC8qKiBQYXJzZWQgTnVtYmVyICovXG4gIGdldCBudW1iZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMudHlwZWRWYWx1ZTtcbiAgfVxuICBzZXQgbnVtYmVyKG51bWJlcikge1xuICAgIHRoaXMudHlwZWRWYWx1ZSA9IG51bWJlcjtcbiAgfVxuXG4gIC8qKlxuICAgIElzIG5lZ2F0aXZlIGFsbG93ZWRcbiAgICBAcmVhZG9ubHlcbiAgKi9cbiAgZ2V0IGFsbG93TmVnYXRpdmUoKSB7XG4gICAgcmV0dXJuIHRoaXMuc2lnbmVkIHx8IHRoaXMubWluICE9IG51bGwgJiYgdGhpcy5taW4gPCAwIHx8IHRoaXMubWF4ICE9IG51bGwgJiYgdGhpcy5tYXggPCAwO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIHR5cGVkVmFsdWVFcXVhbHModmFsdWUpIHtcbiAgICAvLyBoYW5kbGUgIDAgLT4gJycgY2FzZSAodHlwZWQgPSAwIGV2ZW4gaWYgdmFsdWUgPSAnJylcbiAgICAvLyBmb3IgZGV0YWlscyBzZWUgaHR0cHM6Ly9naXRodWIuY29tL3VObUFuTmVSL2ltYXNranMvaXNzdWVzLzEzNFxuICAgIHJldHVybiAoc3VwZXIudHlwZWRWYWx1ZUVxdWFscyh2YWx1ZSkgfHwgTWFza2VkTnVtYmVyLkVNUFRZX1ZBTFVFUy5pbmNsdWRlcyh2YWx1ZSkgJiYgTWFza2VkTnVtYmVyLkVNUFRZX1ZBTFVFUy5pbmNsdWRlcyh0aGlzLnR5cGVkVmFsdWUpKSAmJiAhKHZhbHVlID09PSAwICYmIHRoaXMudmFsdWUgPT09ICcnKTtcbiAgfVxufVxuTWFza2VkTnVtYmVyLlVOTUFTS0VEX1JBRElYID0gJy4nO1xuTWFza2VkTnVtYmVyLkRFRkFVTFRTID0ge1xuICByYWRpeDogJywnLFxuICB0aG91c2FuZHNTZXBhcmF0b3I6ICcnLFxuICBtYXBUb1JhZGl4OiBbTWFza2VkTnVtYmVyLlVOTUFTS0VEX1JBRElYXSxcbiAgc2NhbGU6IDIsXG4gIHNpZ25lZDogZmFsc2UsXG4gIG5vcm1hbGl6ZVplcm9zOiB0cnVlLFxuICBwYWRGcmFjdGlvbmFsWmVyb3M6IGZhbHNlLFxuICBwYXJzZTogTnVtYmVyLFxuICBmb3JtYXQ6IG4gPT4gbi50b0xvY2FsZVN0cmluZygnZW4tVVMnLCB7XG4gICAgdXNlR3JvdXBpbmc6IGZhbHNlLFxuICAgIG1heGltdW1GcmFjdGlvbkRpZ2l0czogMjBcbiAgfSlcbn07XG5NYXNrZWROdW1iZXIuRU1QVFlfVkFMVUVTID0gWy4uLk1hc2tlZC5FTVBUWV9WQUxVRVMsIDBdO1xuSU1hc2suTWFza2VkTnVtYmVyID0gTWFza2VkTnVtYmVyO1xuXG5leHBvcnQgeyBNYXNrZWROdW1iZXIgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBNYXNrZWQgZnJvbSAnLi9iYXNlLmpzJztcbmltcG9ydCBJTWFzayBmcm9tICcuLi9jb3JlL2hvbGRlci5qcyc7XG5pbXBvcnQgJy4uL2NvcmUvY2hhbmdlLWRldGFpbHMuanMnO1xuaW1wb3J0ICcuLi9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzJztcbmltcG9ydCAnLi4vY29yZS91dGlscy5qcyc7XG5cbi8qKiBNYXNraW5nIGJ5IGN1c3RvbSBGdW5jdGlvbiAqL1xuY2xhc3MgTWFza2VkRnVuY3Rpb24gZXh0ZW5kcyBNYXNrZWQge1xuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgICBAcGFyYW0ge09iamVjdH0gb3B0c1xuICAqL1xuICBfdXBkYXRlKG9wdHMpIHtcbiAgICBpZiAob3B0cy5tYXNrKSBvcHRzLnZhbGlkYXRlID0gb3B0cy5tYXNrO1xuICAgIHN1cGVyLl91cGRhdGUob3B0cyk7XG4gIH1cbn1cbklNYXNrLk1hc2tlZEZ1bmN0aW9uID0gTWFza2VkRnVuY3Rpb247XG5cbmV4cG9ydCB7IE1hc2tlZEZ1bmN0aW9uIGFzIGRlZmF1bHQgfTtcbiIsICJpbXBvcnQgeyBfIGFzIF9vYmplY3RXaXRob3V0UHJvcGVydGllc0xvb3NlIH0gZnJvbSAnLi4vX3JvbGx1cFBsdWdpbkJhYmVsSGVscGVycy02YjNiZDQwNC5qcyc7XG5pbXBvcnQgeyBESVJFQ1RJT04sIG5vcm1hbGl6ZVByZXBhcmUsIG9iamVjdEluY2x1ZGVzIH0gZnJvbSAnLi4vY29yZS91dGlscy5qcyc7XG5pbXBvcnQgQ2hhbmdlRGV0YWlscyBmcm9tICcuLi9jb3JlL2NoYW5nZS1kZXRhaWxzLmpzJztcbmltcG9ydCBjcmVhdGVNYXNrIGZyb20gJy4vZmFjdG9yeS5qcyc7XG5pbXBvcnQgTWFza2VkIGZyb20gJy4vYmFzZS5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuaW1wb3J0ICcuLi9jb3JlL2NvbnRpbnVvdXMtdGFpbC1kZXRhaWxzLmpzJztcblxuY29uc3QgX2V4Y2x1ZGVkID0gW1wiY29tcGlsZWRNYXNrc1wiLCBcImN1cnJlbnRNYXNrUmVmXCIsIFwiY3VycmVudE1hc2tcIl0sXG4gIF9leGNsdWRlZDIgPSBbXCJtYXNrXCJdO1xuLyoqIER5bmFtaWMgbWFzayBmb3IgY2hvb3NpbmcgYXByb3ByaWF0ZSBtYXNrIGluIHJ1bi10aW1lICovXG5jbGFzcyBNYXNrZWREeW5hbWljIGV4dGVuZHMgTWFza2VkIHtcbiAgLyoqIEN1cnJlbnRseSBjaG9zZW4gbWFzayAqL1xuXG4gIC8qKiBDb21wbGlsZWQge0BsaW5rIE1hc2tlZH0gb3B0aW9ucyAqL1xuXG4gIC8qKiBDaG9vc2VzIHtAbGluayBNYXNrZWR9IGRlcGVuZGluZyBvbiBpbnB1dCB2YWx1ZSAqL1xuXG4gIC8qKlxuICAgIEBwYXJhbSB7T2JqZWN0fSBvcHRzXG4gICovXG4gIGNvbnN0cnVjdG9yKG9wdHMpIHtcbiAgICBzdXBlcihPYmplY3QuYXNzaWduKHt9LCBNYXNrZWREeW5hbWljLkRFRkFVTFRTLCBvcHRzKSk7XG4gICAgdGhpcy5jdXJyZW50TWFzayA9IG51bGw7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgX3VwZGF0ZShvcHRzKSB7XG4gICAgc3VwZXIuX3VwZGF0ZShvcHRzKTtcbiAgICBpZiAoJ21hc2snIGluIG9wdHMpIHtcbiAgICAgIC8vIG1hc2sgY291bGQgYmUgdG90YWxseSBkeW5hbWljIHdpdGggb25seSBgZGlzcGF0Y2hgIG9wdGlvblxuICAgICAgdGhpcy5jb21waWxlZE1hc2tzID0gQXJyYXkuaXNBcnJheShvcHRzLm1hc2spID8gb3B0cy5tYXNrLm1hcChtID0+IGNyZWF0ZU1hc2sobSkpIDogW107XG5cbiAgICAgIC8vIHRoaXMuY3VycmVudE1hc2sgPSB0aGlzLmRvRGlzcGF0Y2goJycpOyAvLyBwcm9iYWJseSBub3QgbmVlZGVkIGJ1dCBsZXRzIHNlZVxuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBfYXBwZW5kQ2hhclJhdyhjaCkge1xuICAgIGxldCBmbGFncyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDoge307XG4gICAgY29uc3QgZGV0YWlscyA9IHRoaXMuX2FwcGx5RGlzcGF0Y2goY2gsIGZsYWdzKTtcbiAgICBpZiAodGhpcy5jdXJyZW50TWFzaykge1xuICAgICAgZGV0YWlscy5hZ2dyZWdhdGUodGhpcy5jdXJyZW50TWFzay5fYXBwZW5kQ2hhcihjaCwgdGhpcy5jdXJyZW50TWFza0ZsYWdzKGZsYWdzKSkpO1xuICAgIH1cbiAgICByZXR1cm4gZGV0YWlscztcbiAgfVxuICBfYXBwbHlEaXNwYXRjaCgpIHtcbiAgICBsZXQgYXBwZW5kZWQgPSBhcmd1bWVudHMubGVuZ3RoID4gMCAmJiBhcmd1bWVudHNbMF0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1swXSA6ICcnO1xuICAgIGxldCBmbGFncyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDoge307XG4gICAgbGV0IHRhaWwgPSBhcmd1bWVudHMubGVuZ3RoID4gMiAmJiBhcmd1bWVudHNbMl0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1syXSA6ICcnO1xuICAgIGNvbnN0IHByZXZWYWx1ZUJlZm9yZVRhaWwgPSBmbGFncy50YWlsICYmIGZsYWdzLl9iZWZvcmVUYWlsU3RhdGUgIT0gbnVsbCA/IGZsYWdzLl9iZWZvcmVUYWlsU3RhdGUuX3ZhbHVlIDogdGhpcy52YWx1ZTtcbiAgICBjb25zdCBpbnB1dFZhbHVlID0gdGhpcy5yYXdJbnB1dFZhbHVlO1xuICAgIGNvbnN0IGluc2VydFZhbHVlID0gZmxhZ3MudGFpbCAmJiBmbGFncy5fYmVmb3JlVGFpbFN0YXRlICE9IG51bGwgP1xuICAgIC8vICRGbG93Rml4TWUgLSB0aXJlZCB0byBmaWdodCB3aXRoIHR5cGUgc3lzdGVtXG4gICAgZmxhZ3MuX2JlZm9yZVRhaWxTdGF0ZS5fcmF3SW5wdXRWYWx1ZSA6IGlucHV0VmFsdWU7XG4gICAgY29uc3QgdGFpbFZhbHVlID0gaW5wdXRWYWx1ZS5zbGljZShpbnNlcnRWYWx1ZS5sZW5ndGgpO1xuICAgIGNvbnN0IHByZXZNYXNrID0gdGhpcy5jdXJyZW50TWFzaztcbiAgICBjb25zdCBkZXRhaWxzID0gbmV3IENoYW5nZURldGFpbHMoKTtcbiAgICBjb25zdCBwcmV2TWFza1N0YXRlID0gcHJldk1hc2sgPT09IG51bGwgfHwgcHJldk1hc2sgPT09IHZvaWQgMCA/IHZvaWQgMCA6IHByZXZNYXNrLnN0YXRlO1xuXG4gICAgLy8gY2xvbmUgZmxhZ3MgdG8gcHJldmVudCBvdmVyd3JpdGluZyBgX2JlZm9yZVRhaWxTdGF0ZWBcbiAgICB0aGlzLmN1cnJlbnRNYXNrID0gdGhpcy5kb0Rpc3BhdGNoKGFwcGVuZGVkLCBPYmplY3QuYXNzaWduKHt9LCBmbGFncyksIHRhaWwpO1xuXG4gICAgLy8gcmVzdG9yZSBzdGF0ZSBhZnRlciBkaXNwYXRjaFxuICAgIGlmICh0aGlzLmN1cnJlbnRNYXNrKSB7XG4gICAgICBpZiAodGhpcy5jdXJyZW50TWFzayAhPT0gcHJldk1hc2spIHtcbiAgICAgICAgLy8gaWYgbWFzayBjaGFuZ2VkIHJlYXBwbHkgaW5wdXRcbiAgICAgICAgdGhpcy5jdXJyZW50TWFzay5yZXNldCgpO1xuICAgICAgICBpZiAoaW5zZXJ0VmFsdWUpIHtcbiAgICAgICAgICAvLyAkRmxvd0ZpeE1lIC0gaXQncyBvaywgd2UgZG9uJ3QgY2hhbmdlIGN1cnJlbnQgbWFzayBhYm92ZVxuICAgICAgICAgIGNvbnN0IGQgPSB0aGlzLmN1cnJlbnRNYXNrLmFwcGVuZChpbnNlcnRWYWx1ZSwge1xuICAgICAgICAgICAgcmF3OiB0cnVlXG4gICAgICAgICAgfSk7XG4gICAgICAgICAgZGV0YWlscy50YWlsU2hpZnQgPSBkLmluc2VydGVkLmxlbmd0aCAtIHByZXZWYWx1ZUJlZm9yZVRhaWwubGVuZ3RoO1xuICAgICAgICB9XG4gICAgICAgIGlmICh0YWlsVmFsdWUpIHtcbiAgICAgICAgICAvLyAkRmxvd0ZpeE1lIC0gaXQncyBvaywgd2UgZG9uJ3QgY2hhbmdlIGN1cnJlbnQgbWFzayBhYm92ZVxuICAgICAgICAgIGRldGFpbHMudGFpbFNoaWZ0ICs9IHRoaXMuY3VycmVudE1hc2suYXBwZW5kKHRhaWxWYWx1ZSwge1xuICAgICAgICAgICAgcmF3OiB0cnVlLFxuICAgICAgICAgICAgdGFpbDogdHJ1ZVxuICAgICAgICAgIH0pLnRhaWxTaGlmdDtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgLy8gRGlzcGF0Y2ggY2FuIGRvIHNvbWV0aGluZyBiYWQgd2l0aCBzdGF0ZSwgc29cbiAgICAgICAgLy8gcmVzdG9yZSBwcmV2IG1hc2sgc3RhdGVcbiAgICAgICAgdGhpcy5jdXJyZW50TWFzay5zdGF0ZSA9IHByZXZNYXNrU3RhdGU7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBkZXRhaWxzO1xuICB9XG4gIF9hcHBlbmRQbGFjZWhvbGRlcigpIHtcbiAgICBjb25zdCBkZXRhaWxzID0gdGhpcy5fYXBwbHlEaXNwYXRjaCguLi5hcmd1bWVudHMpO1xuICAgIGlmICh0aGlzLmN1cnJlbnRNYXNrKSB7XG4gICAgICBkZXRhaWxzLmFnZ3JlZ2F0ZSh0aGlzLmN1cnJlbnRNYXNrLl9hcHBlbmRQbGFjZWhvbGRlcigpKTtcbiAgICB9XG4gICAgcmV0dXJuIGRldGFpbHM7XG4gIH1cblxuICAvKipcbiAgIEBvdmVycmlkZVxuICAqL1xuICBfYXBwZW5kRWFnZXIoKSB7XG4gICAgY29uc3QgZGV0YWlscyA9IHRoaXMuX2FwcGx5RGlzcGF0Y2goLi4uYXJndW1lbnRzKTtcbiAgICBpZiAodGhpcy5jdXJyZW50TWFzaykge1xuICAgICAgZGV0YWlscy5hZ2dyZWdhdGUodGhpcy5jdXJyZW50TWFzay5fYXBwZW5kRWFnZXIoKSk7XG4gICAgfVxuICAgIHJldHVybiBkZXRhaWxzO1xuICB9XG4gIGFwcGVuZFRhaWwodGFpbCkge1xuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgIGlmICh0YWlsKSBkZXRhaWxzLmFnZ3JlZ2F0ZSh0aGlzLl9hcHBseURpc3BhdGNoKCcnLCB7fSwgdGFpbCkpO1xuICAgIHJldHVybiBkZXRhaWxzLmFnZ3JlZ2F0ZSh0aGlzLmN1cnJlbnRNYXNrID8gdGhpcy5jdXJyZW50TWFzay5hcHBlbmRUYWlsKHRhaWwpIDogc3VwZXIuYXBwZW5kVGFpbCh0YWlsKSk7XG4gIH1cbiAgY3VycmVudE1hc2tGbGFncyhmbGFncykge1xuICAgIHZhciBfZmxhZ3MkX2JlZm9yZVRhaWxTdGEsIF9mbGFncyRfYmVmb3JlVGFpbFN0YTI7XG4gICAgcmV0dXJuIE9iamVjdC5hc3NpZ24oe30sIGZsYWdzLCB7XG4gICAgICBfYmVmb3JlVGFpbFN0YXRlOiAoKF9mbGFncyRfYmVmb3JlVGFpbFN0YSA9IGZsYWdzLl9iZWZvcmVUYWlsU3RhdGUpID09PSBudWxsIHx8IF9mbGFncyRfYmVmb3JlVGFpbFN0YSA9PT0gdm9pZCAwID8gdm9pZCAwIDogX2ZsYWdzJF9iZWZvcmVUYWlsU3RhLmN1cnJlbnRNYXNrUmVmKSA9PT0gdGhpcy5jdXJyZW50TWFzayAmJiAoKF9mbGFncyRfYmVmb3JlVGFpbFN0YTIgPSBmbGFncy5fYmVmb3JlVGFpbFN0YXRlKSA9PT0gbnVsbCB8fCBfZmxhZ3MkX2JlZm9yZVRhaWxTdGEyID09PSB2b2lkIDAgPyB2b2lkIDAgOiBfZmxhZ3MkX2JlZm9yZVRhaWxTdGEyLmN1cnJlbnRNYXNrKSB8fCBmbGFncy5fYmVmb3JlVGFpbFN0YXRlXG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZG9EaXNwYXRjaChhcHBlbmRlZCkge1xuICAgIGxldCBmbGFncyA9IGFyZ3VtZW50cy5sZW5ndGggPiAxICYmIGFyZ3VtZW50c1sxXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzFdIDoge307XG4gICAgbGV0IHRhaWwgPSBhcmd1bWVudHMubGVuZ3RoID4gMiAmJiBhcmd1bWVudHNbMl0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1syXSA6ICcnO1xuICAgIHJldHVybiB0aGlzLmRpc3BhdGNoKGFwcGVuZGVkLCB0aGlzLCBmbGFncywgdGFpbCk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZG9WYWxpZGF0ZShmbGFncykge1xuICAgIHJldHVybiBzdXBlci5kb1ZhbGlkYXRlKGZsYWdzKSAmJiAoIXRoaXMuY3VycmVudE1hc2sgfHwgdGhpcy5jdXJyZW50TWFzay5kb1ZhbGlkYXRlKHRoaXMuY3VycmVudE1hc2tGbGFncyhmbGFncykpKTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBkb1ByZXBhcmUoc3RyKSB7XG4gICAgbGV0IGZsYWdzID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiB7fTtcbiAgICBsZXQgW3MsIGRldGFpbHNdID0gbm9ybWFsaXplUHJlcGFyZShzdXBlci5kb1ByZXBhcmUoc3RyLCBmbGFncykpO1xuICAgIGlmICh0aGlzLmN1cnJlbnRNYXNrKSB7XG4gICAgICBsZXQgY3VycmVudERldGFpbHM7XG4gICAgICBbcywgY3VycmVudERldGFpbHNdID0gbm9ybWFsaXplUHJlcGFyZShzdXBlci5kb1ByZXBhcmUocywgdGhpcy5jdXJyZW50TWFza0ZsYWdzKGZsYWdzKSkpO1xuICAgICAgZGV0YWlscyA9IGRldGFpbHMuYWdncmVnYXRlKGN1cnJlbnREZXRhaWxzKTtcbiAgICB9XG4gICAgcmV0dXJuIFtzLCBkZXRhaWxzXTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICByZXNldCgpIHtcbiAgICB2YXIgX3RoaXMkY3VycmVudE1hc2s7XG4gICAgKF90aGlzJGN1cnJlbnRNYXNrID0gdGhpcy5jdXJyZW50TWFzaykgPT09IG51bGwgfHwgX3RoaXMkY3VycmVudE1hc2sgPT09IHZvaWQgMCA/IHZvaWQgMCA6IF90aGlzJGN1cnJlbnRNYXNrLnJlc2V0KCk7XG4gICAgdGhpcy5jb21waWxlZE1hc2tzLmZvckVhY2gobSA9PiBtLnJlc2V0KCkpO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCB2YWx1ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW50TWFzayA/IHRoaXMuY3VycmVudE1hc2sudmFsdWUgOiAnJztcbiAgfVxuICBzZXQgdmFsdWUodmFsdWUpIHtcbiAgICBzdXBlci52YWx1ZSA9IHZhbHVlO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCB1bm1hc2tlZFZhbHVlKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnRNYXNrID8gdGhpcy5jdXJyZW50TWFzay51bm1hc2tlZFZhbHVlIDogJyc7XG4gIH1cbiAgc2V0IHVubWFza2VkVmFsdWUodW5tYXNrZWRWYWx1ZSkge1xuICAgIHN1cGVyLnVubWFza2VkVmFsdWUgPSB1bm1hc2tlZFZhbHVlO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCB0eXBlZFZhbHVlKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnRNYXNrID8gdGhpcy5jdXJyZW50TWFzay50eXBlZFZhbHVlIDogJyc7XG4gIH1cblxuICAvLyBwcm9iYWJseSB0eXBlZFZhbHVlIHNob3VsZCBub3QgYmUgdXNlZCB3aXRoIGR5bmFtaWNcbiAgc2V0IHR5cGVkVmFsdWUodmFsdWUpIHtcbiAgICBsZXQgdW5tYXNrZWRWYWx1ZSA9IFN0cmluZyh2YWx1ZSk7XG5cbiAgICAvLyBkb3VibGUgY2hlY2sgaXRcbiAgICBpZiAodGhpcy5jdXJyZW50TWFzaykge1xuICAgICAgdGhpcy5jdXJyZW50TWFzay50eXBlZFZhbHVlID0gdmFsdWU7XG4gICAgICB1bm1hc2tlZFZhbHVlID0gdGhpcy5jdXJyZW50TWFzay51bm1hc2tlZFZhbHVlO1xuICAgIH1cbiAgICB0aGlzLnVubWFza2VkVmFsdWUgPSB1bm1hc2tlZFZhbHVlO1xuICB9XG4gIGdldCBkaXNwbGF5VmFsdWUoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVudE1hc2sgPyB0aGlzLmN1cnJlbnRNYXNrLmRpc3BsYXlWYWx1ZSA6ICcnO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCBpc0NvbXBsZXRlKCkge1xuICAgIHZhciBfdGhpcyRjdXJyZW50TWFzazI7XG4gICAgcmV0dXJuIEJvb2xlYW4oKF90aGlzJGN1cnJlbnRNYXNrMiA9IHRoaXMuY3VycmVudE1hc2spID09PSBudWxsIHx8IF90aGlzJGN1cnJlbnRNYXNrMiA9PT0gdm9pZCAwID8gdm9pZCAwIDogX3RoaXMkY3VycmVudE1hc2syLmlzQ29tcGxldGUpO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGdldCBpc0ZpbGxlZCgpIHtcbiAgICB2YXIgX3RoaXMkY3VycmVudE1hc2szO1xuICAgIHJldHVybiBCb29sZWFuKChfdGhpcyRjdXJyZW50TWFzazMgPSB0aGlzLmN1cnJlbnRNYXNrKSA9PT0gbnVsbCB8fCBfdGhpcyRjdXJyZW50TWFzazMgPT09IHZvaWQgMCA/IHZvaWQgMCA6IF90aGlzJGN1cnJlbnRNYXNrMy5pc0ZpbGxlZCk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgcmVtb3ZlKCkge1xuICAgIGNvbnN0IGRldGFpbHMgPSBuZXcgQ2hhbmdlRGV0YWlscygpO1xuICAgIGlmICh0aGlzLmN1cnJlbnRNYXNrKSB7XG4gICAgICBkZXRhaWxzLmFnZ3JlZ2F0ZSh0aGlzLmN1cnJlbnRNYXNrLnJlbW92ZSguLi5hcmd1bWVudHMpKVxuICAgICAgLy8gdXBkYXRlIHdpdGggZGlzcGF0Y2hcbiAgICAgIC5hZ2dyZWdhdGUodGhpcy5fYXBwbHlEaXNwYXRjaCgpKTtcbiAgICB9XG4gICAgcmV0dXJuIGRldGFpbHM7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZ2V0IHN0YXRlKCkge1xuICAgIHZhciBfdGhpcyRjdXJyZW50TWFzazQ7XG4gICAgcmV0dXJuIE9iamVjdC5hc3NpZ24oe30sIHN1cGVyLnN0YXRlLCB7XG4gICAgICBfcmF3SW5wdXRWYWx1ZTogdGhpcy5yYXdJbnB1dFZhbHVlLFxuICAgICAgY29tcGlsZWRNYXNrczogdGhpcy5jb21waWxlZE1hc2tzLm1hcChtID0+IG0uc3RhdGUpLFxuICAgICAgY3VycmVudE1hc2tSZWY6IHRoaXMuY3VycmVudE1hc2ssXG4gICAgICBjdXJyZW50TWFzazogKF90aGlzJGN1cnJlbnRNYXNrNCA9IHRoaXMuY3VycmVudE1hc2spID09PSBudWxsIHx8IF90aGlzJGN1cnJlbnRNYXNrNCA9PT0gdm9pZCAwID8gdm9pZCAwIDogX3RoaXMkY3VycmVudE1hc2s0LnN0YXRlXG4gICAgfSk7XG4gIH1cbiAgc2V0IHN0YXRlKHN0YXRlKSB7XG4gICAgY29uc3Qge1xuICAgICAgICBjb21waWxlZE1hc2tzLFxuICAgICAgICBjdXJyZW50TWFza1JlZixcbiAgICAgICAgY3VycmVudE1hc2tcbiAgICAgIH0gPSBzdGF0ZSxcbiAgICAgIG1hc2tlZFN0YXRlID0gX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2Uoc3RhdGUsIF9leGNsdWRlZCk7XG4gICAgdGhpcy5jb21waWxlZE1hc2tzLmZvckVhY2goKG0sIG1pKSA9PiBtLnN0YXRlID0gY29tcGlsZWRNYXNrc1ttaV0pO1xuICAgIGlmIChjdXJyZW50TWFza1JlZiAhPSBudWxsKSB7XG4gICAgICB0aGlzLmN1cnJlbnRNYXNrID0gY3VycmVudE1hc2tSZWY7XG4gICAgICB0aGlzLmN1cnJlbnRNYXNrLnN0YXRlID0gY3VycmVudE1hc2s7XG4gICAgfVxuICAgIHN1cGVyLnN0YXRlID0gbWFza2VkU3RhdGU7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgZXh0cmFjdElucHV0KCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnRNYXNrID8gdGhpcy5jdXJyZW50TWFzay5leHRyYWN0SW5wdXQoLi4uYXJndW1lbnRzKSA6ICcnO1xuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIGV4dHJhY3RUYWlsKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnRNYXNrID8gdGhpcy5jdXJyZW50TWFzay5leHRyYWN0VGFpbCguLi5hcmd1bWVudHMpIDogc3VwZXIuZXh0cmFjdFRhaWwoLi4uYXJndW1lbnRzKTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICBkb0NvbW1pdCgpIHtcbiAgICBpZiAodGhpcy5jdXJyZW50TWFzaykgdGhpcy5jdXJyZW50TWFzay5kb0NvbW1pdCgpO1xuICAgIHN1cGVyLmRvQ29tbWl0KCk7XG4gIH1cblxuICAvKipcbiAgICBAb3ZlcnJpZGVcbiAgKi9cbiAgbmVhcmVzdElucHV0UG9zKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnRNYXNrID8gdGhpcy5jdXJyZW50TWFzay5uZWFyZXN0SW5wdXRQb3MoLi4uYXJndW1lbnRzKSA6IHN1cGVyLm5lYXJlc3RJbnB1dFBvcyguLi5hcmd1bWVudHMpO1xuICB9XG4gIGdldCBvdmVyd3JpdGUoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVudE1hc2sgPyB0aGlzLmN1cnJlbnRNYXNrLm92ZXJ3cml0ZSA6IHN1cGVyLm92ZXJ3cml0ZTtcbiAgfVxuICBzZXQgb3ZlcndyaXRlKG92ZXJ3cml0ZSkge1xuICAgIGNvbnNvbGUud2FybignXCJvdmVyd3JpdGVcIiBvcHRpb24gaXMgbm90IGF2YWlsYWJsZSBpbiBkeW5hbWljIG1hc2ssIHVzZSB0aGlzIG9wdGlvbiBpbiBzaWJsaW5ncycpO1xuICB9XG4gIGdldCBlYWdlcigpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW50TWFzayA/IHRoaXMuY3VycmVudE1hc2suZWFnZXIgOiBzdXBlci5lYWdlcjtcbiAgfVxuICBzZXQgZWFnZXIoZWFnZXIpIHtcbiAgICBjb25zb2xlLndhcm4oJ1wiZWFnZXJcIiBvcHRpb24gaXMgbm90IGF2YWlsYWJsZSBpbiBkeW5hbWljIG1hc2ssIHVzZSB0aGlzIG9wdGlvbiBpbiBzaWJsaW5ncycpO1xuICB9XG4gIGdldCBza2lwSW52YWxpZCgpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW50TWFzayA/IHRoaXMuY3VycmVudE1hc2suc2tpcEludmFsaWQgOiBzdXBlci5za2lwSW52YWxpZDtcbiAgfVxuICBzZXQgc2tpcEludmFsaWQoc2tpcEludmFsaWQpIHtcbiAgICBpZiAodGhpcy5pc0luaXRpYWxpemVkIHx8IHNraXBJbnZhbGlkICE9PSBNYXNrZWQuREVGQVVMVFMuc2tpcEludmFsaWQpIHtcbiAgICAgIGNvbnNvbGUud2FybignXCJza2lwSW52YWxpZFwiIG9wdGlvbiBpcyBub3QgYXZhaWxhYmxlIGluIGR5bmFtaWMgbWFzaywgdXNlIHRoaXMgb3B0aW9uIGluIHNpYmxpbmdzJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAgQG92ZXJyaWRlXG4gICovXG4gIG1hc2tFcXVhbHMobWFzaykge1xuICAgIHJldHVybiBBcnJheS5pc0FycmF5KG1hc2spICYmIHRoaXMuY29tcGlsZWRNYXNrcy5ldmVyeSgobSwgbWkpID0+IHtcbiAgICAgIGlmICghbWFza1ttaV0pIHJldHVybjtcbiAgICAgIGNvbnN0IF9tYXNrJG1pID0gbWFza1ttaV0sXG4gICAgICAgIHtcbiAgICAgICAgICBtYXNrOiBvbGRNYXNrXG4gICAgICAgIH0gPSBfbWFzayRtaSxcbiAgICAgICAgcmVzdE9wdHMgPSBfb2JqZWN0V2l0aG91dFByb3BlcnRpZXNMb29zZShfbWFzayRtaSwgX2V4Y2x1ZGVkMik7XG4gICAgICByZXR1cm4gb2JqZWN0SW5jbHVkZXMobSwgcmVzdE9wdHMpICYmIG0ubWFza0VxdWFscyhvbGRNYXNrKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgIEBvdmVycmlkZVxuICAqL1xuICB0eXBlZFZhbHVlRXF1YWxzKHZhbHVlKSB7XG4gICAgdmFyIF90aGlzJGN1cnJlbnRNYXNrNTtcbiAgICByZXR1cm4gQm9vbGVhbigoX3RoaXMkY3VycmVudE1hc2s1ID0gdGhpcy5jdXJyZW50TWFzaykgPT09IG51bGwgfHwgX3RoaXMkY3VycmVudE1hc2s1ID09PSB2b2lkIDAgPyB2b2lkIDAgOiBfdGhpcyRjdXJyZW50TWFzazUudHlwZWRWYWx1ZUVxdWFscyh2YWx1ZSkpO1xuICB9XG59XG5NYXNrZWREeW5hbWljLkRFRkFVTFRTID0ge1xuICBkaXNwYXRjaDogKGFwcGVuZGVkLCBtYXNrZWQsIGZsYWdzLCB0YWlsKSA9PiB7XG4gICAgaWYgKCFtYXNrZWQuY29tcGlsZWRNYXNrcy5sZW5ndGgpIHJldHVybjtcbiAgICBjb25zdCBpbnB1dFZhbHVlID0gbWFza2VkLnJhd0lucHV0VmFsdWU7XG5cbiAgICAvLyBzaW11bGF0ZSBpbnB1dFxuICAgIGNvbnN0IGlucHV0cyA9IG1hc2tlZC5jb21waWxlZE1hc2tzLm1hcCgobSwgaW5kZXgpID0+IHtcbiAgICAgIGNvbnN0IGlzQ3VycmVudCA9IG1hc2tlZC5jdXJyZW50TWFzayA9PT0gbTtcbiAgICAgIGNvbnN0IHN0YXJ0SW5wdXRQb3MgPSBpc0N1cnJlbnQgPyBtLnZhbHVlLmxlbmd0aCA6IG0ubmVhcmVzdElucHV0UG9zKG0udmFsdWUubGVuZ3RoLCBESVJFQ1RJT04uRk9SQ0VfTEVGVCk7XG4gICAgICBpZiAobS5yYXdJbnB1dFZhbHVlICE9PSBpbnB1dFZhbHVlKSB7XG4gICAgICAgIG0ucmVzZXQoKTtcbiAgICAgICAgbS5hcHBlbmQoaW5wdXRWYWx1ZSwge1xuICAgICAgICAgIHJhdzogdHJ1ZVxuICAgICAgICB9KTtcbiAgICAgIH0gZWxzZSBpZiAoIWlzQ3VycmVudCkge1xuICAgICAgICBtLnJlbW92ZShzdGFydElucHV0UG9zKTtcbiAgICAgIH1cbiAgICAgIG0uYXBwZW5kKGFwcGVuZGVkLCBtYXNrZWQuY3VycmVudE1hc2tGbGFncyhmbGFncykpO1xuICAgICAgbS5hcHBlbmRUYWlsKHRhaWwpO1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgaW5kZXgsXG4gICAgICAgIHdlaWdodDogbS5yYXdJbnB1dFZhbHVlLmxlbmd0aCxcbiAgICAgICAgdG90YWxJbnB1dFBvc2l0aW9uczogbS50b3RhbElucHV0UG9zaXRpb25zKDAsIE1hdGgubWF4KHN0YXJ0SW5wdXRQb3MsIG0ubmVhcmVzdElucHV0UG9zKG0udmFsdWUubGVuZ3RoLCBESVJFQ1RJT04uRk9SQ0VfTEVGVCkpKVxuICAgICAgfTtcbiAgICB9KTtcblxuICAgIC8vIHBvcCBtYXNrcyB3aXRoIGxvbmdlciB2YWx1ZXMgZmlyc3RcbiAgICBpbnB1dHMuc29ydCgoaTEsIGkyKSA9PiBpMi53ZWlnaHQgLSBpMS53ZWlnaHQgfHwgaTIudG90YWxJbnB1dFBvc2l0aW9ucyAtIGkxLnRvdGFsSW5wdXRQb3NpdGlvbnMpO1xuICAgIHJldHVybiBtYXNrZWQuY29tcGlsZWRNYXNrc1tpbnB1dHNbMF0uaW5kZXhdO1xuICB9XG59O1xuSU1hc2suTWFza2VkRHluYW1pYyA9IE1hc2tlZER5bmFtaWM7XG5cbmV4cG9ydCB7IE1hc2tlZER5bmFtaWMgYXMgZGVmYXVsdCB9O1xuIiwgImltcG9ydCBjcmVhdGVNYXNrIGZyb20gJy4vZmFjdG9yeS5qcyc7XG5pbXBvcnQgSU1hc2sgZnJvbSAnLi4vY29yZS9ob2xkZXIuanMnO1xuaW1wb3J0ICcuLi9jb3JlL3V0aWxzLmpzJztcbmltcG9ydCAnLi4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5cbi8qKiBNYXNrIHBpcGUgc291cmNlIGFuZCBkZXN0aW5hdGlvbiB0eXBlcyAqL1xuY29uc3QgUElQRV9UWVBFID0ge1xuICBNQVNLRUQ6ICd2YWx1ZScsXG4gIFVOTUFTS0VEOiAndW5tYXNrZWRWYWx1ZScsXG4gIFRZUEVEOiAndHlwZWRWYWx1ZSdcbn07XG5cbi8qKiBDcmVhdGVzIG5ldyBwaXBlIGZ1bmN0aW9uIGRlcGVuZGluZyBvbiBtYXNrIHR5cGUsIHNvdXJjZSBhbmQgZGVzdGluYXRpb24gb3B0aW9ucyAqL1xuZnVuY3Rpb24gY3JlYXRlUGlwZShtYXNrKSB7XG4gIGxldCBmcm9tID0gYXJndW1lbnRzLmxlbmd0aCA+IDEgJiYgYXJndW1lbnRzWzFdICE9PSB1bmRlZmluZWQgPyBhcmd1bWVudHNbMV0gOiBQSVBFX1RZUEUuTUFTS0VEO1xuICBsZXQgdG8gPSBhcmd1bWVudHMubGVuZ3RoID4gMiAmJiBhcmd1bWVudHNbMl0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1syXSA6IFBJUEVfVFlQRS5NQVNLRUQ7XG4gIGNvbnN0IG1hc2tlZCA9IGNyZWF0ZU1hc2sobWFzayk7XG4gIHJldHVybiB2YWx1ZSA9PiBtYXNrZWQucnVuSXNvbGF0ZWQobSA9PiB7XG4gICAgbVtmcm9tXSA9IHZhbHVlO1xuICAgIHJldHVybiBtW3RvXTtcbiAgfSk7XG59XG5cbi8qKiBQaXBlcyB2YWx1ZSB0aHJvdWdoIG1hc2sgZGVwZW5kaW5nIG9uIG1hc2sgdHlwZSwgc291cmNlIGFuZCBkZXN0aW5hdGlvbiBvcHRpb25zICovXG5mdW5jdGlvbiBwaXBlKHZhbHVlKSB7XG4gIGZvciAodmFyIF9sZW4gPSBhcmd1bWVudHMubGVuZ3RoLCBwaXBlQXJncyA9IG5ldyBBcnJheShfbGVuID4gMSA/IF9sZW4gLSAxIDogMCksIF9rZXkgPSAxOyBfa2V5IDwgX2xlbjsgX2tleSsrKSB7XG4gICAgcGlwZUFyZ3NbX2tleSAtIDFdID0gYXJndW1lbnRzW19rZXldO1xuICB9XG4gIHJldHVybiBjcmVhdGVQaXBlKC4uLnBpcGVBcmdzKSh2YWx1ZSk7XG59XG5JTWFzay5QSVBFX1RZUEUgPSBQSVBFX1RZUEU7XG5JTWFzay5jcmVhdGVQaXBlID0gY3JlYXRlUGlwZTtcbklNYXNrLnBpcGUgPSBwaXBlO1xuXG5leHBvcnQgeyBQSVBFX1RZUEUsIGNyZWF0ZVBpcGUsIHBpcGUgfTtcbiIsICJleHBvcnQgeyBkZWZhdWx0IGFzIElucHV0TWFzayB9IGZyb20gJy4vY29udHJvbHMvaW5wdXQuanMnO1xuaW1wb3J0IElNYXNrIGZyb20gJy4vY29yZS9ob2xkZXIuanMnO1xuZXhwb3J0IHsgZGVmYXVsdCB9IGZyb20gJy4vY29yZS9ob2xkZXIuanMnO1xuZXhwb3J0IHsgZGVmYXVsdCBhcyBNYXNrZWQgfSBmcm9tICcuL21hc2tlZC9iYXNlLmpzJztcbmV4cG9ydCB7IGRlZmF1bHQgYXMgTWFza2VkUGF0dGVybiB9IGZyb20gJy4vbWFza2VkL3BhdHRlcm4uanMnO1xuZXhwb3J0IHsgZGVmYXVsdCBhcyBNYXNrZWRFbnVtIH0gZnJvbSAnLi9tYXNrZWQvZW51bS5qcyc7XG5leHBvcnQgeyBkZWZhdWx0IGFzIE1hc2tlZFJhbmdlIH0gZnJvbSAnLi9tYXNrZWQvcmFuZ2UuanMnO1xuZXhwb3J0IHsgZGVmYXVsdCBhcyBNYXNrZWROdW1iZXIgfSBmcm9tICcuL21hc2tlZC9udW1iZXIuanMnO1xuZXhwb3J0IHsgZGVmYXVsdCBhcyBNYXNrZWREYXRlIH0gZnJvbSAnLi9tYXNrZWQvZGF0ZS5qcyc7XG5leHBvcnQgeyBkZWZhdWx0IGFzIE1hc2tlZFJlZ0V4cCB9IGZyb20gJy4vbWFza2VkL3JlZ2V4cC5qcyc7XG5leHBvcnQgeyBkZWZhdWx0IGFzIE1hc2tlZEZ1bmN0aW9uIH0gZnJvbSAnLi9tYXNrZWQvZnVuY3Rpb24uanMnO1xuZXhwb3J0IHsgZGVmYXVsdCBhcyBNYXNrZWREeW5hbWljIH0gZnJvbSAnLi9tYXNrZWQvZHluYW1pYy5qcyc7XG5leHBvcnQgeyBkZWZhdWx0IGFzIGNyZWF0ZU1hc2sgfSBmcm9tICcuL21hc2tlZC9mYWN0b3J5LmpzJztcbmV4cG9ydCB7IGRlZmF1bHQgYXMgTWFza0VsZW1lbnQgfSBmcm9tICcuL2NvbnRyb2xzL21hc2stZWxlbWVudC5qcyc7XG5leHBvcnQgeyBkZWZhdWx0IGFzIEhUTUxNYXNrRWxlbWVudCB9IGZyb20gJy4vY29udHJvbHMvaHRtbC1tYXNrLWVsZW1lbnQuanMnO1xuZXhwb3J0IHsgZGVmYXVsdCBhcyBIVE1MQ29udGVudGVkaXRhYmxlTWFza0VsZW1lbnQgfSBmcm9tICcuL2NvbnRyb2xzL2h0bWwtY29udGVudGVkaXRhYmxlLW1hc2stZWxlbWVudC5qcyc7XG5leHBvcnQgeyBQSVBFX1RZUEUsIGNyZWF0ZVBpcGUsIHBpcGUgfSBmcm9tICcuL21hc2tlZC9waXBlLmpzJztcbmV4cG9ydCB7IGRlZmF1bHQgYXMgQ2hhbmdlRGV0YWlscyB9IGZyb20gJy4vY29yZS9jaGFuZ2UtZGV0YWlscy5qcyc7XG5pbXBvcnQgJy4vX3JvbGx1cFBsdWdpbkJhYmVsSGVscGVycy02YjNiZDQwNC5qcyc7XG5pbXBvcnQgJy4vY29yZS91dGlscy5qcyc7XG5pbXBvcnQgJy4vY29yZS9hY3Rpb24tZGV0YWlscy5qcyc7XG5pbXBvcnQgJy4vY29yZS9jb250aW51b3VzLXRhaWwtZGV0YWlscy5qcyc7XG5pbXBvcnQgJy4vbWFza2VkL3BhdHRlcm4vaW5wdXQtZGVmaW5pdGlvbi5qcyc7XG5pbXBvcnQgJy4vbWFza2VkL3BhdHRlcm4vZml4ZWQtZGVmaW5pdGlvbi5qcyc7XG5pbXBvcnQgJy4vbWFza2VkL3BhdHRlcm4vY2h1bmstdGFpbC1kZXRhaWxzLmpzJztcbmltcG9ydCAnLi9tYXNrZWQvcGF0dGVybi9jdXJzb3IuanMnO1xuXG50cnkge1xuICBnbG9iYWxUaGlzLklNYXNrID0gSU1hc2s7XG59IGNhdGNoIChlKSB7fVxuIiwgImltcG9ydCBJTWFzayBmcm9tICdpbWFzaydcblxuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gdGV4dElucHV0Rm9ybUNvbXBvbmVudCh7IGdldE1hc2tPcHRpb25zVXNpbmcsIHN0YXRlIH0pIHtcbiAgICByZXR1cm4ge1xuICAgICAgICBpc1N0YXRlQmVpbmdVcGRhdGVkOiBmYWxzZSxcblxuICAgICAgICBtYXNrOiBudWxsLFxuXG4gICAgICAgIHN0YXRlLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmICghZ2V0TWFza09wdGlvbnNVc2luZykge1xuICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAodHlwZW9mIHRoaXMuc3RhdGUgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgdGhpcy4kZWwudmFsdWUgPSB0aGlzLnN0YXRlPy52YWx1ZU9mKClcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5tYXNrID0gSU1hc2sodGhpcy4kZWwsIGdldE1hc2tPcHRpb25zVXNpbmcoSU1hc2spKS5vbihcbiAgICAgICAgICAgICAgICAnYWNjZXB0JyxcbiAgICAgICAgICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuaXNTdGF0ZUJlaW5nVXBkYXRlZCA9IHRydWVcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLnN0YXRlID0gdGhpcy5tYXNrLnVubWFza2VkVmFsdWVcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLiRuZXh0VGljaygoKSA9PiAodGhpcy5pc1N0YXRlQmVpbmdVcGRhdGVkID0gZmFsc2UpKVxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICApXG5cbiAgICAgICAgICAgIHRoaXMuJHdhdGNoKCdzdGF0ZScsICgpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5pc1N0YXRlQmVpbmdVcGRhdGVkKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMubWFzay51bm1hc2tlZFZhbHVlID0gdGhpcy5zdGF0ZT8udmFsdWVPZigpID8/ICcnXG4gICAgICAgICAgICB9KVxuICAgICAgICB9LFxuICAgIH1cbn1cbiJdLAogICJtYXBwaW5ncyI6ICI7QUFB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LFNBQU87QUFBQSxJQUNILHFCQUFxQjtBQUFBLElBRXJCLE1BQU07QUFBQSxJQUVOO0FBQUEsSUFFQSxNQUFNLFdBQVk7QUFDZCxVQUFJLENBQUMscUJBQXFCO0FBQ3RCO0FBQUEsTUFDSjtBQUVBLFVBQUksT0FBTyxLQUFLLFVBQVUsYUFBYTtBQUNuQyxhQUFLLElBQUksUUFBUSxLQUFLLE9BQU8sUUFBUTtBQUFBLE1BQ3pDO0FBRUEsV0FBSyxPQUFPLE1BQU0sS0FBSyxLQUFLLG9CQUFvQixLQUFLLENBQUMsRUFBRTtBQUFBLFFBQ3BEO0FBQUEsUUFDQSxNQUFNO0FBQ0YsZUFBSyxzQkFBc0I7QUFFM0IsZUFBSyxRQUFRLEtBQUssS0FBSztBQUV2QixlQUFLLFVBQVUsTUFBTyxLQUFLLHNCQUFzQixLQUFNO0FBQUEsUUFDM0Q7QUFBQSxNQUNKO0FBRUEsV0FBSyxPQUFPLFNBQVMsTUFBTTtBQUN2QixZQUFJLEtBQUsscUJBQXFCO0FBQzFCO0FBQUEsUUFDSjtBQUVBLGFBQUssS0FBSyxnQkFBZ0IsS0FBSyxPQUFPLFFBQVEsS0FBSztBQUFBLE1BQ3ZELENBQUM7QUFBQSxJQUNMO0FBQUEsRUFDSjtBQUNKOyIsCiAgIm5hbWVzIjogWyJfZXhjbHVkZWQiLCAiX2V4Y2x1ZGVkIiwgImJOYW1lIiwgImZyb21Qb3MiLCAidG9Qb3MiLCAiX2V4Y2x1ZGVkIiwgIl9leGNsdWRlZCIsICJfZXhjbHVkZWQyIl0KfQo=
