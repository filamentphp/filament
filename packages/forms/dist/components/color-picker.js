// node_modules/vanilla-colorful/lib/utils/math.js
var clamp = (number, min = 0, max = 1) => {
  return number > max ? max : number < min ? min : number;
};
var round = (number, digits = 0, base = Math.pow(10, digits)) => {
  return Math.round(base * number) / base;
};

// node_modules/vanilla-colorful/lib/utils/convert.js
var angleUnits = {
  grad: 360 / 400,
  turn: 360,
  rad: 360 / (Math.PI * 2)
};
var hexToHsva = (hex) => rgbaToHsva(hexToRgba(hex));
var hexToRgba = (hex) => {
  if (hex[0] === "#")
    hex = hex.substr(1);
  if (hex.length < 6) {
    return {
      r: parseInt(hex[0] + hex[0], 16),
      g: parseInt(hex[1] + hex[1], 16),
      b: parseInt(hex[2] + hex[2], 16),
      a: 1
    };
  }
  return {
    r: parseInt(hex.substr(0, 2), 16),
    g: parseInt(hex.substr(2, 2), 16),
    b: parseInt(hex.substr(4, 2), 16),
    a: 1
  };
};
var parseHue = (value, unit = "deg") => {
  return Number(value) * (angleUnits[unit] || 1);
};
var hslaStringToHsva = (hslString) => {
  const matcher = /hsla?\(?\s*(-?\d*\.?\d+)(deg|rad|grad|turn)?[,\s]+(-?\d*\.?\d+)%?[,\s]+(-?\d*\.?\d+)%?,?\s*[/\s]*(-?\d*\.?\d+)?(%)?\s*\)?/i;
  const match = matcher.exec(hslString);
  if (!match)
    return { h: 0, s: 0, v: 0, a: 1 };
  return hslaToHsva({
    h: parseHue(match[1], match[2]),
    s: Number(match[3]),
    l: Number(match[4]),
    a: match[5] === void 0 ? 1 : Number(match[5]) / (match[6] ? 100 : 1)
  });
};
var hslStringToHsva = hslaStringToHsva;
var hslaToHsva = ({ h, s, l, a }) => {
  s *= (l < 50 ? l : 100 - l) / 100;
  return {
    h,
    s: s > 0 ? 2 * s / (l + s) * 100 : 0,
    v: l + s,
    a
  };
};
var hsvaToHex = (hsva) => rgbaToHex(hsvaToRgba(hsva));
var hsvaToHsla = ({ h, s, v, a }) => {
  const hh = (200 - s) * v / 100;
  return {
    h: round(h),
    s: round(hh > 0 && hh < 200 ? s * v / 100 / (hh <= 100 ? hh : 200 - hh) * 100 : 0),
    l: round(hh / 2),
    a: round(a, 2)
  };
};
var hsvaToHslString = (hsva) => {
  const { h, s, l } = hsvaToHsla(hsva);
  return `hsl(${h}, ${s}%, ${l}%)`;
};
var hsvaToHslaString = (hsva) => {
  const { h, s, l, a } = hsvaToHsla(hsva);
  return `hsla(${h}, ${s}%, ${l}%, ${a})`;
};
var hsvaToRgba = ({ h, s, v, a }) => {
  h = h / 360 * 6;
  s = s / 100;
  v = v / 100;
  const hh = Math.floor(h), b = v * (1 - s), c = v * (1 - (h - hh) * s), d = v * (1 - (1 - h + hh) * s), module = hh % 6;
  return {
    r: round([v, c, b, b, d, v][module] * 255),
    g: round([d, v, v, c, b, b][module] * 255),
    b: round([b, b, d, v, v, c][module] * 255),
    a: round(a, 2)
  };
};
var hsvaToRgbString = (hsva) => {
  const { r, g, b } = hsvaToRgba(hsva);
  return `rgb(${r}, ${g}, ${b})`;
};
var hsvaToRgbaString = (hsva) => {
  const { r, g, b, a } = hsvaToRgba(hsva);
  return `rgba(${r}, ${g}, ${b}, ${a})`;
};
var rgbaStringToHsva = (rgbaString) => {
  const matcher = /rgba?\(?\s*(-?\d*\.?\d+)(%)?[,\s]+(-?\d*\.?\d+)(%)?[,\s]+(-?\d*\.?\d+)(%)?,?\s*[/\s]*(-?\d*\.?\d+)?(%)?\s*\)?/i;
  const match = matcher.exec(rgbaString);
  if (!match)
    return { h: 0, s: 0, v: 0, a: 1 };
  return rgbaToHsva({
    r: Number(match[1]) / (match[2] ? 100 / 255 : 1),
    g: Number(match[3]) / (match[4] ? 100 / 255 : 1),
    b: Number(match[5]) / (match[6] ? 100 / 255 : 1),
    a: match[7] === void 0 ? 1 : Number(match[7]) / (match[8] ? 100 : 1)
  });
};
var rgbStringToHsva = rgbaStringToHsva;
var format = (number) => {
  const hex = number.toString(16);
  return hex.length < 2 ? "0" + hex : hex;
};
var rgbaToHex = ({ r, g, b }) => {
  return "#" + format(r) + format(g) + format(b);
};
var rgbaToHsva = ({ r, g, b, a }) => {
  const max = Math.max(r, g, b);
  const delta = max - Math.min(r, g, b);
  const hh = delta ? max === r ? (g - b) / delta : max === g ? 2 + (b - r) / delta : 4 + (r - g) / delta : 0;
  return {
    h: round(60 * (hh < 0 ? hh + 6 : hh)),
    s: round(max ? delta / max * 100 : 0),
    v: round(max / 255 * 100),
    a
  };
};

// node_modules/vanilla-colorful/lib/utils/compare.js
var equalColorObjects = (first, second) => {
  if (first === second)
    return true;
  for (const prop in first) {
    if (first[prop] !== second[prop])
      return false;
  }
  return true;
};
var equalColorString = (first, second) => {
  return first.replace(/\s/g, "") === second.replace(/\s/g, "");
};
var equalHex = (first, second) => {
  if (first.toLowerCase() === second.toLowerCase())
    return true;
  return equalColorObjects(hexToRgba(first), hexToRgba(second));
};

// node_modules/vanilla-colorful/lib/utils/dom.js
var cache = {};
var tpl = (html) => {
  let template = cache[html];
  if (!template) {
    template = document.createElement("template");
    template.innerHTML = html;
    cache[html] = template;
  }
  return template;
};
var fire = (target, type, detail) => {
  target.dispatchEvent(new CustomEvent(type, {
    bubbles: true,
    detail
  }));
};

// node_modules/vanilla-colorful/lib/components/slider.js
var hasTouched = false;
var isTouch = (e) => "touches" in e;
var isValid = (event) => {
  if (hasTouched && !isTouch(event))
    return false;
  if (!hasTouched)
    hasTouched = isTouch(event);
  return true;
};
var pointerMove = (target, event) => {
  const pointer = isTouch(event) ? event.touches[0] : event;
  const rect = target.el.getBoundingClientRect();
  fire(target.el, "move", target.getMove({
    x: clamp((pointer.pageX - (rect.left + window.pageXOffset)) / rect.width),
    y: clamp((pointer.pageY - (rect.top + window.pageYOffset)) / rect.height)
  }));
};
var keyMove = (target, event) => {
  const keyCode = event.keyCode;
  if (keyCode > 40 || target.xy && keyCode < 37 || keyCode < 33)
    return;
  event.preventDefault();
  fire(target.el, "move", target.getMove({
    x: keyCode === 39 ? 0.01 : keyCode === 37 ? -0.01 : keyCode === 34 ? 0.05 : keyCode === 33 ? -0.05 : keyCode === 35 ? 1 : keyCode === 36 ? -1 : 0,
    y: keyCode === 40 ? 0.01 : keyCode === 38 ? -0.01 : 0
  }, true));
};
var Slider = class {
  constructor(root, part, aria, xy) {
    const template = tpl(`<div role="slider" tabindex="0" part="${part}" ${aria}><div part="${part}-pointer"></div></div>`);
    root.appendChild(template.content.cloneNode(true));
    const el = root.querySelector(`[part=${part}]`);
    el.addEventListener("mousedown", this);
    el.addEventListener("touchstart", this);
    el.addEventListener("keydown", this);
    this.el = el;
    this.xy = xy;
    this.nodes = [el.firstChild, el];
  }
  set dragging(state) {
    const toggleEvent = state ? document.addEventListener : document.removeEventListener;
    toggleEvent(hasTouched ? "touchmove" : "mousemove", this);
    toggleEvent(hasTouched ? "touchend" : "mouseup", this);
  }
  handleEvent(event) {
    switch (event.type) {
      case "mousedown":
      case "touchstart":
        event.preventDefault();
        if (!isValid(event) || !hasTouched && event.button != 0)
          return;
        this.el.focus();
        pointerMove(this, event);
        this.dragging = true;
        break;
      case "mousemove":
      case "touchmove":
        event.preventDefault();
        pointerMove(this, event);
        break;
      case "mouseup":
      case "touchend":
        this.dragging = false;
        break;
      case "keydown":
        keyMove(this, event);
        break;
    }
  }
  style(styles) {
    styles.forEach((style, i) => {
      for (const p in style) {
        this.nodes[i].style.setProperty(p, style[p]);
      }
    });
  }
};

// node_modules/vanilla-colorful/lib/components/hue.js
var Hue = class extends Slider {
  constructor(root) {
    super(root, "hue", 'aria-label="Hue" aria-valuemin="0" aria-valuemax="360"', false);
  }
  update({ h }) {
    this.h = h;
    this.style([
      {
        left: `${h / 360 * 100}%`,
        color: hsvaToHslString({ h, s: 100, v: 100, a: 1 })
      }
    ]);
    this.el.setAttribute("aria-valuenow", `${round(h)}`);
  }
  getMove(offset, key) {
    return { h: key ? clamp(this.h + offset.x * 360, 0, 360) : 360 * offset.x };
  }
};

// node_modules/vanilla-colorful/lib/components/saturation.js
var Saturation = class extends Slider {
  constructor(root) {
    super(root, "saturation", 'aria-label="Color"', true);
  }
  update(hsva) {
    this.hsva = hsva;
    this.style([
      {
        top: `${100 - hsva.v}%`,
        left: `${hsva.s}%`,
        color: hsvaToHslString(hsva)
      },
      {
        "background-color": hsvaToHslString({ h: hsva.h, s: 100, v: 100, a: 1 })
      }
    ]);
    this.el.setAttribute("aria-valuetext", `Saturation ${round(hsva.s)}%, Brightness ${round(hsva.v)}%`);
  }
  getMove(offset, key) {
    return {
      s: key ? clamp(this.hsva.s + offset.x * 100, 0, 100) : offset.x * 100,
      v: key ? clamp(this.hsva.v - offset.y * 100, 0, 100) : Math.round(100 - offset.y * 100)
    };
  }
};

// node_modules/vanilla-colorful/lib/styles/color-picker.js
var color_picker_default = `:host{display:flex;flex-direction:column;position:relative;width:200px;height:200px;user-select:none;-webkit-user-select:none;cursor:default}:host([hidden]){display:none!important}[role=slider]{position:relative;touch-action:none;user-select:none;-webkit-user-select:none;outline:0}[role=slider]:last-child{border-radius:0 0 8px 8px}[part$=pointer]{position:absolute;z-index:1;box-sizing:border-box;width:28px;height:28px;transform:translate(-50%,-50%);background-color:#fff;border:2px solid #fff;border-radius:50%;box-shadow:0 2px 4px rgba(0,0,0,.2)}[part$=pointer]::after{display:block;content:'';position:absolute;left:0;top:0;right:0;bottom:0;border-radius:inherit;background-color:currentColor}[role=slider]:focus [part$=pointer]{transform:translate(-50%,-50%) scale(1.1)}`;

// node_modules/vanilla-colorful/lib/styles/hue.js
var hue_default = `[part=hue]{flex:0 0 24px;background:linear-gradient(to right,red 0,#ff0 17%,#0f0 33%,#0ff 50%,#00f 67%,#f0f 83%,red 100%)}[part=hue-pointer]{top:50%;z-index:2}`;

// node_modules/vanilla-colorful/lib/styles/saturation.js
var saturation_default = `[part=saturation]{flex-grow:1;border-color:transparent;border-bottom:12px solid #000;border-radius:8px 8px 0 0;background-image:linear-gradient(to top,#000,transparent),linear-gradient(to right,#fff,rgba(255,255,255,0));box-shadow:inset 0 0 0 1px rgba(0,0,0,.05)}[part=saturation-pointer]{z-index:3}`;

// node_modules/vanilla-colorful/lib/components/color-picker.js
var $isSame = Symbol("same");
var $color = Symbol("color");
var $hsva = Symbol("hsva");
var $change = Symbol("change");
var $update = Symbol("update");
var $parts = Symbol("parts");
var $css = Symbol("css");
var $sliders = Symbol("sliders");
var ColorPicker = class extends HTMLElement {
  static get observedAttributes() {
    return ["color"];
  }
  get [$css]() {
    return [color_picker_default, hue_default, saturation_default];
  }
  get [$sliders]() {
    return [Saturation, Hue];
  }
  get color() {
    return this[$color];
  }
  set color(newColor) {
    if (!this[$isSame](newColor)) {
      const newHsva = this.colorModel.toHsva(newColor);
      this[$update](newHsva);
      this[$change](newColor);
    }
  }
  constructor() {
    super();
    const template = tpl(`<style>${this[$css].join("")}</style>`);
    const root = this.attachShadow({ mode: "open" });
    root.appendChild(template.content.cloneNode(true));
    root.addEventListener("move", this);
    this[$parts] = this[$sliders].map((slider) => new slider(root));
  }
  connectedCallback() {
    if (this.hasOwnProperty("color")) {
      const value = this.color;
      delete this["color"];
      this.color = value;
    } else if (!this.color) {
      this.color = this.colorModel.defaultColor;
    }
  }
  attributeChangedCallback(_attr, _oldVal, newVal) {
    const color = this.colorModel.fromAttr(newVal);
    if (!this[$isSame](color)) {
      this.color = color;
    }
  }
  handleEvent(event) {
    const oldHsva = this[$hsva];
    const newHsva = { ...oldHsva, ...event.detail };
    this[$update](newHsva);
    let newColor;
    if (!equalColorObjects(newHsva, oldHsva) && !this[$isSame](newColor = this.colorModel.fromHsva(newHsva))) {
      this[$change](newColor);
    }
  }
  [$isSame](color) {
    return this.color && this.colorModel.equal(color, this.color);
  }
  [$update](hsva) {
    this[$hsva] = hsva;
    this[$parts].forEach((part) => part.update(hsva));
  }
  [$change](value) {
    this[$color] = value;
    fire(this, "color-changed", { value });
  }
};

// node_modules/vanilla-colorful/lib/entrypoints/hex.js
var colorModel = {
  defaultColor: "#000",
  toHsva: hexToHsva,
  fromHsva: hsvaToHex,
  equal: equalHex,
  fromAttr: (color) => color
};
var HexBase = class extends ColorPicker {
  get colorModel() {
    return colorModel;
  }
};

// node_modules/vanilla-colorful/hex-color-picker.js
var HexColorPicker = class extends HexBase {
};
customElements.define("hex-color-picker", HexColorPicker);

// node_modules/vanilla-colorful/lib/entrypoints/hsl-string.js
var colorModel2 = {
  defaultColor: "hsl(0, 0%, 0%)",
  toHsva: hslStringToHsva,
  fromHsva: hsvaToHslString,
  equal: equalColorString,
  fromAttr: (color) => color
};
var HslStringBase = class extends ColorPicker {
  get colorModel() {
    return colorModel2;
  }
};

// node_modules/vanilla-colorful/hsl-string-color-picker.js
var HslStringColorPicker = class extends HslStringBase {
};
customElements.define("hsl-string-color-picker", HslStringColorPicker);

// node_modules/vanilla-colorful/lib/entrypoints/rgb-string.js
var colorModel3 = {
  defaultColor: "rgb(0, 0, 0)",
  toHsva: rgbStringToHsva,
  fromHsva: hsvaToRgbString,
  equal: equalColorString,
  fromAttr: (color) => color
};
var RgbStringBase = class extends ColorPicker {
  get colorModel() {
    return colorModel3;
  }
};

// node_modules/vanilla-colorful/rgb-string-color-picker.js
var RgbStringColorPicker = class extends RgbStringBase {
};
customElements.define("rgb-string-color-picker", RgbStringColorPicker);

// node_modules/vanilla-colorful/lib/components/alpha.js
var Alpha = class extends Slider {
  constructor(root) {
    super(root, "alpha", 'aria-label="Alpha" aria-valuemin="0" aria-valuemax="1"', false);
  }
  update(hsva) {
    this.hsva = hsva;
    const colorFrom = hsvaToHslaString({ ...hsva, a: 0 });
    const colorTo = hsvaToHslaString({ ...hsva, a: 1 });
    const value = hsva.a * 100;
    this.style([
      {
        left: `${value}%`,
        color: hsvaToHslaString(hsva)
      },
      {
        "--gradient": `linear-gradient(90deg, ${colorFrom}, ${colorTo}`
      }
    ]);
    const v = round(value);
    this.el.setAttribute("aria-valuenow", `${v}`);
    this.el.setAttribute("aria-valuetext", `${v}%`);
  }
  getMove(offset, key) {
    return { a: key ? clamp(this.hsva.a + offset.x) : offset.x };
  }
};

// node_modules/vanilla-colorful/lib/styles/alpha.js
var alpha_default = `[part=alpha]{flex:0 0 24px}[part=alpha]::after{display:block;content:'';position:absolute;top:0;left:0;right:0;bottom:0;border-radius:inherit;background-image:var(--gradient);box-shadow:inset 0 0 0 1px rgba(0,0,0,.05)}[part^=alpha]{background-color:#fff;background-image:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill-opacity=".05"><rect x="8" width="8" height="8"/><rect y="8" width="8" height="8"/></svg>')}[part=alpha-pointer]{top:50%}`;

// node_modules/vanilla-colorful/lib/components/alpha-color-picker.js
var AlphaColorPicker = class extends ColorPicker {
  get [$css]() {
    return [...super[$css], alpha_default];
  }
  get [$sliders]() {
    return [...super[$sliders], Alpha];
  }
};

// node_modules/vanilla-colorful/lib/entrypoints/rgba-string.js
var colorModel4 = {
  defaultColor: "rgba(0, 0, 0, 1)",
  toHsva: rgbaStringToHsva,
  fromHsva: hsvaToRgbaString,
  equal: equalColorString,
  fromAttr: (color) => color
};
var RgbaStringBase = class extends AlphaColorPicker {
  get colorModel() {
    return colorModel4;
  }
};

// node_modules/vanilla-colorful/rgba-string-color-picker.js
var RgbaStringColorPicker = class extends RgbaStringBase {
};
customElements.define("rgba-string-color-picker", RgbaStringColorPicker);

// packages/forms/resources/js/components/color-picker.js
function colorPickerFormComponent({
  isAutofocused,
  isDisabled,
  state
}) {
  return {
    state,
    init: function() {
      if (!(this.state === null || this.state === "")) {
        this.setState(this.state);
      }
      if (isAutofocused) {
        this.togglePanelVisibility(this.$refs.input);
      }
      this.$refs.input.addEventListener("change", (event) => {
        this.setState(event.target.value);
      });
      this.$refs.panel.addEventListener("color-changed", (event) => {
        this.setState(event.detail.value);
      });
    },
    togglePanelVisibility: function() {
      if (isDisabled) {
        return;
      }
      this.$refs.panel.toggle(this.$refs.input);
    },
    setState: function(value) {
      this.state = value;
      this.$refs.input.value = value;
      this.$refs.panel.color = value;
    },
    isOpen: function() {
      return this.$refs.panel.style.display === "block";
    }
  };
}
export {
  colorPickerFormComponent as default
};
