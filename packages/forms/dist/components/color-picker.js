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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3ZhbmlsbGEtY29sb3JmdWwvc3JjL2xpYi91dGlscy9tYXRoLnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9saWIvdXRpbHMvY29udmVydC50cyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdmFuaWxsYS1jb2xvcmZ1bC9zcmMvbGliL3V0aWxzL2NvbXBhcmUudHMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3ZhbmlsbGEtY29sb3JmdWwvc3JjL2xpYi91dGlscy9kb20udHMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3ZhbmlsbGEtY29sb3JmdWwvc3JjL2xpYi9jb21wb25lbnRzL3NsaWRlci50cyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdmFuaWxsYS1jb2xvcmZ1bC9zcmMvbGliL2NvbXBvbmVudHMvaHVlLnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9saWIvY29tcG9uZW50cy9zYXR1cmF0aW9uLnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9saWIvc3R5bGVzL2NvbG9yLXBpY2tlci50cyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdmFuaWxsYS1jb2xvcmZ1bC9zcmMvbGliL3N0eWxlcy9odWUudHMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3ZhbmlsbGEtY29sb3JmdWwvc3JjL2xpYi9zdHlsZXMvc2F0dXJhdGlvbi50cyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdmFuaWxsYS1jb2xvcmZ1bC9zcmMvbGliL2NvbXBvbmVudHMvY29sb3ItcGlja2VyLnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9saWIvZW50cnlwb2ludHMvaGV4LnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9oZXgtY29sb3ItcGlja2VyLnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9saWIvZW50cnlwb2ludHMvaHNsLXN0cmluZy50cyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdmFuaWxsYS1jb2xvcmZ1bC9zcmMvaHNsLXN0cmluZy1jb2xvci1waWNrZXIudHMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3ZhbmlsbGEtY29sb3JmdWwvc3JjL2xpYi9lbnRyeXBvaW50cy9yZ2Itc3RyaW5nLnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9yZ2Itc3RyaW5nLWNvbG9yLXBpY2tlci50cyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdmFuaWxsYS1jb2xvcmZ1bC9zcmMvbGliL2NvbXBvbmVudHMvYWxwaGEudHMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3ZhbmlsbGEtY29sb3JmdWwvc3JjL2xpYi9zdHlsZXMvYWxwaGEudHMiLCAiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL3ZhbmlsbGEtY29sb3JmdWwvc3JjL2xpYi9jb21wb25lbnRzL2FscGhhLWNvbG9yLXBpY2tlci50cyIsICIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvdmFuaWxsYS1jb2xvcmZ1bC9zcmMvbGliL2VudHJ5cG9pbnRzL3JnYmEtc3RyaW5nLnRzIiwgIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy92YW5pbGxhLWNvbG9yZnVsL3NyYy9yZ2JhLXN0cmluZy1jb2xvci1waWNrZXIudHMiLCAiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvY29sb3ItcGlja2VyLmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvLyBDbGFtcHMgYSB2YWx1ZSBiZXR3ZWVuIGFuIHVwcGVyIGFuZCBsb3dlciBib3VuZC5cbi8vIFdlIHVzZSB0ZXJuYXJ5IG9wZXJhdG9ycyBiZWNhdXNlIGl0IG1ha2VzIHRoZSBtaW5pZmllZCBjb2RlXG4vLyAyIHRpbWVzIHNob3J0ZXIgdGhlbiBgTWF0aC5taW4oTWF0aC5tYXgoYSxiKSxjKWBcbmV4cG9ydCBjb25zdCBjbGFtcCA9IChudW1iZXI6IG51bWJlciwgbWluID0gMCwgbWF4ID0gMSk6IG51bWJlciA9PiB7XG4gIHJldHVybiBudW1iZXIgPiBtYXggPyBtYXggOiBudW1iZXIgPCBtaW4gPyBtaW4gOiBudW1iZXI7XG59O1xuXG5leHBvcnQgY29uc3Qgcm91bmQgPSAobnVtYmVyOiBudW1iZXIsIGRpZ2l0cyA9IDAsIGJhc2UgPSBNYXRoLnBvdygxMCwgZGlnaXRzKSk6IG51bWJlciA9PiB7XG4gIHJldHVybiBNYXRoLnJvdW5kKGJhc2UgKiBudW1iZXIpIC8gYmFzZTtcbn07XG4iLCAiaW1wb3J0IHsgUmdiYUNvbG9yLCBSZ2JDb2xvciwgSHNsYUNvbG9yLCBIc2xDb2xvciwgSHN2YUNvbG9yLCBIc3ZDb2xvciB9IGZyb20gJy4uL3R5cGVzJztcbmltcG9ydCB7IHJvdW5kIH0gZnJvbSAnLi9tYXRoLmpzJztcblxuLyoqXG4gKiBWYWxpZCBDU1MgPGFuZ2xlPiB1bml0cy5cbiAqIGh0dHBzOi8vZGV2ZWxvcGVyLm1vemlsbGEub3JnL2VuLVVTL2RvY3MvV2ViL0NTUy9hbmdsZVxuICovXG5jb25zdCBhbmdsZVVuaXRzOiBSZWNvcmQ8c3RyaW5nLCBudW1iZXI+ID0ge1xuICBncmFkOiAzNjAgLyA0MDAsXG4gIHR1cm46IDM2MCxcbiAgcmFkOiAzNjAgLyAoTWF0aC5QSSAqIDIpXG59O1xuXG5leHBvcnQgY29uc3QgaGV4VG9Ic3ZhID0gKGhleDogc3RyaW5nKTogSHN2YUNvbG9yID0+IHJnYmFUb0hzdmEoaGV4VG9SZ2JhKGhleCkpO1xuXG5leHBvcnQgY29uc3QgaGV4VG9SZ2JhID0gKGhleDogc3RyaW5nKTogUmdiYUNvbG9yID0+IHtcbiAgaWYgKGhleFswXSA9PT0gJyMnKSBoZXggPSBoZXguc3Vic3RyKDEpO1xuXG4gIGlmIChoZXgubGVuZ3RoIDwgNikge1xuICAgIHJldHVybiB7XG4gICAgICByOiBwYXJzZUludChoZXhbMF0gKyBoZXhbMF0sIDE2KSxcbiAgICAgIGc6IHBhcnNlSW50KGhleFsxXSArIGhleFsxXSwgMTYpLFxuICAgICAgYjogcGFyc2VJbnQoaGV4WzJdICsgaGV4WzJdLCAxNiksXG4gICAgICBhOiAxXG4gICAgfTtcbiAgfVxuXG4gIHJldHVybiB7XG4gICAgcjogcGFyc2VJbnQoaGV4LnN1YnN0cigwLCAyKSwgMTYpLFxuICAgIGc6IHBhcnNlSW50KGhleC5zdWJzdHIoMiwgMiksIDE2KSxcbiAgICBiOiBwYXJzZUludChoZXguc3Vic3RyKDQsIDIpLCAxNiksXG4gICAgYTogMVxuICB9O1xufTtcblxuZXhwb3J0IGNvbnN0IHBhcnNlSHVlID0gKHZhbHVlOiBzdHJpbmcsIHVuaXQgPSAnZGVnJyk6IG51bWJlciA9PiB7XG4gIHJldHVybiBOdW1iZXIodmFsdWUpICogKGFuZ2xlVW5pdHNbdW5pdF0gfHwgMSk7XG59O1xuXG5leHBvcnQgY29uc3QgaHNsYVN0cmluZ1RvSHN2YSA9IChoc2xTdHJpbmc6IHN0cmluZyk6IEhzdmFDb2xvciA9PiB7XG4gIGNvbnN0IG1hdGNoZXIgPVxuICAgIC9oc2xhP1xcKD9cXHMqKC0/XFxkKlxcLj9cXGQrKShkZWd8cmFkfGdyYWR8dHVybik/WyxcXHNdKygtP1xcZCpcXC4/XFxkKyklP1ssXFxzXSsoLT9cXGQqXFwuP1xcZCspJT8sP1xccypbL1xcc10qKC0/XFxkKlxcLj9cXGQrKT8oJSk/XFxzKlxcKT8vaTtcbiAgY29uc3QgbWF0Y2ggPSBtYXRjaGVyLmV4ZWMoaHNsU3RyaW5nKTtcblxuICBpZiAoIW1hdGNoKSByZXR1cm4geyBoOiAwLCBzOiAwLCB2OiAwLCBhOiAxIH07XG5cbiAgcmV0dXJuIGhzbGFUb0hzdmEoe1xuICAgIGg6IHBhcnNlSHVlKG1hdGNoWzFdLCBtYXRjaFsyXSksXG4gICAgczogTnVtYmVyKG1hdGNoWzNdKSxcbiAgICBsOiBOdW1iZXIobWF0Y2hbNF0pLFxuICAgIGE6IG1hdGNoWzVdID09PSB1bmRlZmluZWQgPyAxIDogTnVtYmVyKG1hdGNoWzVdKSAvIChtYXRjaFs2XSA/IDEwMCA6IDEpXG4gIH0pO1xufTtcblxuZXhwb3J0IGNvbnN0IGhzbFN0cmluZ1RvSHN2YSA9IGhzbGFTdHJpbmdUb0hzdmE7XG5cbmV4cG9ydCBjb25zdCBoc2xhVG9Ic3ZhID0gKHsgaCwgcywgbCwgYSB9OiBIc2xhQ29sb3IpOiBIc3ZhQ29sb3IgPT4ge1xuICBzICo9IChsIDwgNTAgPyBsIDogMTAwIC0gbCkgLyAxMDA7XG5cbiAgcmV0dXJuIHtcbiAgICBoOiBoLFxuICAgIHM6IHMgPiAwID8gKCgyICogcykgLyAobCArIHMpKSAqIDEwMCA6IDAsXG4gICAgdjogbCArIHMsXG4gICAgYVxuICB9O1xufTtcblxuZXhwb3J0IGNvbnN0IGhzdmFUb0hleCA9IChoc3ZhOiBIc3ZhQ29sb3IpOiBzdHJpbmcgPT4gcmdiYVRvSGV4KGhzdmFUb1JnYmEoaHN2YSkpO1xuXG5leHBvcnQgY29uc3QgaHN2YVRvSHNsYSA9ICh7IGgsIHMsIHYsIGEgfTogSHN2YUNvbG9yKTogSHNsYUNvbG9yID0+IHtcbiAgY29uc3QgaGggPSAoKDIwMCAtIHMpICogdikgLyAxMDA7XG5cbiAgcmV0dXJuIHtcbiAgICBoOiByb3VuZChoKSxcbiAgICBzOiByb3VuZChoaCA+IDAgJiYgaGggPCAyMDAgPyAoKHMgKiB2KSAvIDEwMCAvIChoaCA8PSAxMDAgPyBoaCA6IDIwMCAtIGhoKSkgKiAxMDAgOiAwKSxcbiAgICBsOiByb3VuZChoaCAvIDIpLFxuICAgIGE6IHJvdW5kKGEsIDIpXG4gIH07XG59O1xuXG5leHBvcnQgY29uc3QgaHN2YVRvSHN2U3RyaW5nID0gKGhzdmE6IEhzdmFDb2xvcik6IHN0cmluZyA9PiB7XG4gIGNvbnN0IHsgaCwgcywgdiB9ID0gcm91bmRIc3ZhKGhzdmEpO1xuICByZXR1cm4gYGhzdigke2h9LCAke3N9JSwgJHt2fSUpYDtcbn07XG5cbmV4cG9ydCBjb25zdCBoc3ZhVG9Ic3ZhU3RyaW5nID0gKGhzdmE6IEhzdmFDb2xvcik6IHN0cmluZyA9PiB7XG4gIGNvbnN0IHsgaCwgcywgdiwgYSB9ID0gcm91bmRIc3ZhKGhzdmEpO1xuICByZXR1cm4gYGhzdmEoJHtofSwgJHtzfSUsICR7dn0lLCAke2F9KWA7XG59O1xuXG5leHBvcnQgY29uc3QgaHN2YVRvSHNsU3RyaW5nID0gKGhzdmE6IEhzdmFDb2xvcik6IHN0cmluZyA9PiB7XG4gIGNvbnN0IHsgaCwgcywgbCB9ID0gaHN2YVRvSHNsYShoc3ZhKTtcbiAgcmV0dXJuIGBoc2woJHtofSwgJHtzfSUsICR7bH0lKWA7XG59O1xuXG5leHBvcnQgY29uc3QgaHN2YVRvSHNsYVN0cmluZyA9IChoc3ZhOiBIc3ZhQ29sb3IpOiBzdHJpbmcgPT4ge1xuICBjb25zdCB7IGgsIHMsIGwsIGEgfSA9IGhzdmFUb0hzbGEoaHN2YSk7XG4gIHJldHVybiBgaHNsYSgke2h9LCAke3N9JSwgJHtsfSUsICR7YX0pYDtcbn07XG5cbmV4cG9ydCBjb25zdCBoc3ZhVG9SZ2JhID0gKHsgaCwgcywgdiwgYSB9OiBIc3ZhQ29sb3IpOiBSZ2JhQ29sb3IgPT4ge1xuICBoID0gKGggLyAzNjApICogNjtcbiAgcyA9IHMgLyAxMDA7XG4gIHYgPSB2IC8gMTAwO1xuXG4gIGNvbnN0IGhoID0gTWF0aC5mbG9vcihoKSxcbiAgICBiID0gdiAqICgxIC0gcyksXG4gICAgYyA9IHYgKiAoMSAtIChoIC0gaGgpICogcyksXG4gICAgZCA9IHYgKiAoMSAtICgxIC0gaCArIGhoKSAqIHMpLFxuICAgIG1vZHVsZSA9IGhoICUgNjtcblxuICByZXR1cm4ge1xuICAgIHI6IHJvdW5kKFt2LCBjLCBiLCBiLCBkLCB2XVttb2R1bGVdICogMjU1KSxcbiAgICBnOiByb3VuZChbZCwgdiwgdiwgYywgYiwgYl1bbW9kdWxlXSAqIDI1NSksXG4gICAgYjogcm91bmQoW2IsIGIsIGQsIHYsIHYsIGNdW21vZHVsZV0gKiAyNTUpLFxuICAgIGE6IHJvdW5kKGEsIDIpXG4gIH07XG59O1xuXG5leHBvcnQgY29uc3QgaHN2YVRvUmdiU3RyaW5nID0gKGhzdmE6IEhzdmFDb2xvcik6IHN0cmluZyA9PiB7XG4gIGNvbnN0IHsgciwgZywgYiB9ID0gaHN2YVRvUmdiYShoc3ZhKTtcbiAgcmV0dXJuIGByZ2IoJHtyfSwgJHtnfSwgJHtifSlgO1xufTtcblxuZXhwb3J0IGNvbnN0IGhzdmFUb1JnYmFTdHJpbmcgPSAoaHN2YTogSHN2YUNvbG9yKTogc3RyaW5nID0+IHtcbiAgY29uc3QgeyByLCBnLCBiLCBhIH0gPSBoc3ZhVG9SZ2JhKGhzdmEpO1xuICByZXR1cm4gYHJnYmEoJHtyfSwgJHtnfSwgJHtifSwgJHthfSlgO1xufTtcblxuZXhwb3J0IGNvbnN0IGhzdmFTdHJpbmdUb0hzdmEgPSAoaHN2U3RyaW5nOiBzdHJpbmcpOiBIc3ZhQ29sb3IgPT4ge1xuICBjb25zdCBtYXRjaGVyID1cbiAgICAvaHN2YT9cXCg/XFxzKigtP1xcZCpcXC4/XFxkKykoZGVnfHJhZHxncmFkfHR1cm4pP1ssXFxzXSsoLT9cXGQqXFwuP1xcZCspJT9bLFxcc10rKC0/XFxkKlxcLj9cXGQrKSU/LD9cXHMqWy9cXHNdKigtP1xcZCpcXC4/XFxkKyk/KCUpP1xccypcXCk/L2k7XG4gIGNvbnN0IG1hdGNoID0gbWF0Y2hlci5leGVjKGhzdlN0cmluZyk7XG5cbiAgaWYgKCFtYXRjaCkgcmV0dXJuIHsgaDogMCwgczogMCwgdjogMCwgYTogMSB9O1xuXG4gIHJldHVybiByb3VuZEhzdmEoe1xuICAgIGg6IHBhcnNlSHVlKG1hdGNoWzFdLCBtYXRjaFsyXSksXG4gICAgczogTnVtYmVyKG1hdGNoWzNdKSxcbiAgICB2OiBOdW1iZXIobWF0Y2hbNF0pLFxuICAgIGE6IG1hdGNoWzVdID09PSB1bmRlZmluZWQgPyAxIDogTnVtYmVyKG1hdGNoWzVdKSAvIChtYXRjaFs2XSA/IDEwMCA6IDEpXG4gIH0pO1xufTtcblxuZXhwb3J0IGNvbnN0IGhzdlN0cmluZ1RvSHN2YSA9IGhzdmFTdHJpbmdUb0hzdmE7XG5cbmV4cG9ydCBjb25zdCByZ2JhU3RyaW5nVG9Ic3ZhID0gKHJnYmFTdHJpbmc6IHN0cmluZyk6IEhzdmFDb2xvciA9PiB7XG4gIGNvbnN0IG1hdGNoZXIgPVxuICAgIC9yZ2JhP1xcKD9cXHMqKC0/XFxkKlxcLj9cXGQrKSglKT9bLFxcc10rKC0/XFxkKlxcLj9cXGQrKSglKT9bLFxcc10rKC0/XFxkKlxcLj9cXGQrKSglKT8sP1xccypbL1xcc10qKC0/XFxkKlxcLj9cXGQrKT8oJSk/XFxzKlxcKT8vaTtcbiAgY29uc3QgbWF0Y2ggPSBtYXRjaGVyLmV4ZWMocmdiYVN0cmluZyk7XG5cbiAgaWYgKCFtYXRjaCkgcmV0dXJuIHsgaDogMCwgczogMCwgdjogMCwgYTogMSB9O1xuXG4gIHJldHVybiByZ2JhVG9Ic3ZhKHtcbiAgICByOiBOdW1iZXIobWF0Y2hbMV0pIC8gKG1hdGNoWzJdID8gMTAwIC8gMjU1IDogMSksXG4gICAgZzogTnVtYmVyKG1hdGNoWzNdKSAvIChtYXRjaFs0XSA/IDEwMCAvIDI1NSA6IDEpLFxuICAgIGI6IE51bWJlcihtYXRjaFs1XSkgLyAobWF0Y2hbNl0gPyAxMDAgLyAyNTUgOiAxKSxcbiAgICBhOiBtYXRjaFs3XSA9PT0gdW5kZWZpbmVkID8gMSA6IE51bWJlcihtYXRjaFs3XSkgLyAobWF0Y2hbOF0gPyAxMDAgOiAxKVxuICB9KTtcbn07XG5cbmV4cG9ydCBjb25zdCByZ2JTdHJpbmdUb0hzdmEgPSByZ2JhU3RyaW5nVG9Ic3ZhO1xuXG5jb25zdCBmb3JtYXQgPSAobnVtYmVyOiBudW1iZXIpID0+IHtcbiAgY29uc3QgaGV4ID0gbnVtYmVyLnRvU3RyaW5nKDE2KTtcbiAgcmV0dXJuIGhleC5sZW5ndGggPCAyID8gJzAnICsgaGV4IDogaGV4O1xufTtcblxuZXhwb3J0IGNvbnN0IHJnYmFUb0hleCA9ICh7IHIsIGcsIGIgfTogUmdiYUNvbG9yKTogc3RyaW5nID0+IHtcbiAgcmV0dXJuICcjJyArIGZvcm1hdChyKSArIGZvcm1hdChnKSArIGZvcm1hdChiKTtcbn07XG5cbmV4cG9ydCBjb25zdCByZ2JhVG9Ic3ZhID0gKHsgciwgZywgYiwgYSB9OiBSZ2JhQ29sb3IpOiBIc3ZhQ29sb3IgPT4ge1xuICBjb25zdCBtYXggPSBNYXRoLm1heChyLCBnLCBiKTtcbiAgY29uc3QgZGVsdGEgPSBtYXggLSBNYXRoLm1pbihyLCBnLCBiKTtcblxuICAvLyBwcmV0dGllci1pZ25vcmVcbiAgY29uc3QgaGggPSBkZWx0YVxuICAgID8gbWF4ID09PSByXG4gICAgICA/IChnIC0gYikgLyBkZWx0YVxuICAgICAgOiBtYXggPT09IGdcbiAgICAgICAgPyAyICsgKGIgLSByKSAvIGRlbHRhXG4gICAgICAgIDogNCArIChyIC0gZykgLyBkZWx0YVxuICAgIDogMDtcblxuICByZXR1cm4ge1xuICAgIGg6IHJvdW5kKDYwICogKGhoIDwgMCA/IGhoICsgNiA6IGhoKSksXG4gICAgczogcm91bmQobWF4ID8gKGRlbHRhIC8gbWF4KSAqIDEwMCA6IDApLFxuICAgIHY6IHJvdW5kKChtYXggLyAyNTUpICogMTAwKSxcbiAgICBhXG4gIH07XG59O1xuXG5leHBvcnQgY29uc3Qgcm91bmRIc3ZhID0gKGhzdmE6IEhzdmFDb2xvcik6IEhzdmFDb2xvciA9PiAoe1xuICBoOiByb3VuZChoc3ZhLmgpLFxuICBzOiByb3VuZChoc3ZhLnMpLFxuICB2OiByb3VuZChoc3ZhLnYpLFxuICBhOiByb3VuZChoc3ZhLmEsIDIpXG59KTtcblxuZXhwb3J0IGNvbnN0IHJnYmFUb1JnYiA9ICh7IHIsIGcsIGIgfTogUmdiYUNvbG9yKTogUmdiQ29sb3IgPT4gKHsgciwgZywgYiB9KTtcblxuZXhwb3J0IGNvbnN0IGhzbGFUb0hzbCA9ICh7IGgsIHMsIGwgfTogSHNsYUNvbG9yKTogSHNsQ29sb3IgPT4gKHsgaCwgcywgbCB9KTtcblxuZXhwb3J0IGNvbnN0IGhzdmFUb0hzdiA9IChoc3ZhOiBIc3ZhQ29sb3IpOiBIc3ZDb2xvciA9PiB7XG4gIGNvbnN0IHsgaCwgcywgdiB9ID0gcm91bmRIc3ZhKGhzdmEpO1xuICByZXR1cm4geyBoLCBzLCB2IH07XG59O1xuIiwgImltcG9ydCB7IGhleFRvUmdiYSB9IGZyb20gJy4vY29udmVydC5qcyc7XG5pbXBvcnQgdHlwZSB7IE9iamVjdENvbG9yIH0gZnJvbSAnLi4vdHlwZXMnO1xuXG5leHBvcnQgY29uc3QgZXF1YWxDb2xvck9iamVjdHMgPSAoZmlyc3Q6IE9iamVjdENvbG9yLCBzZWNvbmQ6IE9iamVjdENvbG9yKTogYm9vbGVhbiA9PiB7XG4gIGlmIChmaXJzdCA9PT0gc2Vjb25kKSByZXR1cm4gdHJ1ZTtcblxuICBmb3IgKGNvbnN0IHByb3AgaW4gZmlyc3QpIHtcbiAgICAvLyBUaGUgZm9sbG93aW5nIGFsbG93cyBmb3IgYSB0eXBlLXNhZmUgY2FsbGluZyBvZiB0aGlzIGZ1bmN0aW9uIChmaXJzdCAmIHNlY29uZCBoYXZlIHRvIGJlIEhTTCwgSFNWLCBvciBSR0IpXG4gICAgLy8gd2l0aCB0eXBlLXVuc2FmZSBpdGVyYXRpbmcgb3ZlciBvYmplY3Qga2V5cy4gVFMgZG9lcyBub3QgYWxsb3cgdGhpcyB3aXRob3V0IGFuIGluZGV4IChgW2tleTogc3RyaW5nXTogbnVtYmVyYClcbiAgICAvLyBvbiBhbiBvYmplY3QgdG8gZGVmaW5lIGhvdyBpdGVyYXRpb24gaXMgbm9ybWFsbHkgZG9uZS4gVG8gZW5zdXJlIGV4dHJhIGtleXMgYXJlIG5vdCBhbGxvd2VkIG9uIG91ciB0eXBlcyxcbiAgICAvLyB3ZSBtdXN0IGNhc3Qgb3VyIG9iamVjdCB0byB1bmtub3duIChhcyBSR0IgZGVtYW5kcyBgcmAgYmUgYSBrZXksIHdoaWxlIGBSZWNvcmQ8c3RyaW5nLCB4PmAgZG9lcyBub3QgY2FyZSBpZlxuICAgIC8vIHRoZXJlIGlzIG9yIG5vdCksIGFuZCB0aGVuIGFzIGEgdHlwZSBUUyBjYW4gaXRlcmF0ZSBvdmVyLlxuICAgIGlmIChcbiAgICAgIChmaXJzdCBhcyB1bmtub3duIGFzIFJlY29yZDxzdHJpbmcsIG51bWJlcj4pW3Byb3BdICE9PVxuICAgICAgKHNlY29uZCBhcyB1bmtub3duIGFzIFJlY29yZDxzdHJpbmcsIG51bWJlcj4pW3Byb3BdXG4gICAgKVxuICAgICAgcmV0dXJuIGZhbHNlO1xuICB9XG5cbiAgcmV0dXJuIHRydWU7XG59O1xuXG5leHBvcnQgY29uc3QgZXF1YWxDb2xvclN0cmluZyA9IChmaXJzdDogc3RyaW5nLCBzZWNvbmQ6IHN0cmluZyk6IGJvb2xlYW4gPT4ge1xuICByZXR1cm4gZmlyc3QucmVwbGFjZSgvXFxzL2csICcnKSA9PT0gc2Vjb25kLnJlcGxhY2UoL1xccy9nLCAnJyk7XG59O1xuXG5leHBvcnQgY29uc3QgZXF1YWxIZXggPSAoZmlyc3Q6IHN0cmluZywgc2Vjb25kOiBzdHJpbmcpOiBib29sZWFuID0+IHtcbiAgaWYgKGZpcnN0LnRvTG93ZXJDYXNlKCkgPT09IHNlY29uZC50b0xvd2VyQ2FzZSgpKSByZXR1cm4gdHJ1ZTtcblxuICAvLyBUbyBjb21wYXJlIGNvbG9ycyBsaWtlIGAjRkZGYCBhbmQgYGZmZmZmZmAgd2UgY29udmVydCB0aGVtIGludG8gUkdCIG9iamVjdHNcbiAgcmV0dXJuIGVxdWFsQ29sb3JPYmplY3RzKGhleFRvUmdiYShmaXJzdCksIGhleFRvUmdiYShzZWNvbmQpKTtcbn07XG4iLCAiY29uc3QgY2FjaGU6IFJlY29yZDxzdHJpbmcsIEhUTUxUZW1wbGF0ZUVsZW1lbnQ+ID0ge307XG5cbmV4cG9ydCBjb25zdCB0cGwgPSAoaHRtbDogc3RyaW5nKTogSFRNTFRlbXBsYXRlRWxlbWVudCA9PiB7XG4gIGxldCB0ZW1wbGF0ZSA9IGNhY2hlW2h0bWxdO1xuICBpZiAoIXRlbXBsYXRlKSB7XG4gICAgdGVtcGxhdGUgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCd0ZW1wbGF0ZScpO1xuICAgIHRlbXBsYXRlLmlubmVySFRNTCA9IGh0bWw7XG4gICAgY2FjaGVbaHRtbF0gPSB0ZW1wbGF0ZTtcbiAgfVxuICByZXR1cm4gdGVtcGxhdGU7XG59O1xuXG5leHBvcnQgY29uc3QgZmlyZSA9ICh0YXJnZXQ6IEhUTUxFbGVtZW50LCB0eXBlOiBzdHJpbmcsIGRldGFpbDogUmVjb3JkPHN0cmluZywgdW5rbm93bj4pOiB2b2lkID0+IHtcbiAgdGFyZ2V0LmRpc3BhdGNoRXZlbnQoXG4gICAgbmV3IEN1c3RvbUV2ZW50KHR5cGUsIHtcbiAgICAgIGJ1YmJsZXM6IHRydWUsXG4gICAgICBkZXRhaWxcbiAgICB9KVxuICApO1xufTtcbiIsICJpbXBvcnQgdHlwZSB7IEhzdmFDb2xvciB9IGZyb20gJy4uL3R5cGVzLmpzJztcbmltcG9ydCB7IGZpcmUsIHRwbCB9IGZyb20gJy4uL3V0aWxzL2RvbS5qcyc7XG5pbXBvcnQgeyBjbGFtcCB9IGZyb20gJy4uL3V0aWxzL21hdGguanMnO1xuXG5leHBvcnQgaW50ZXJmYWNlIE9mZnNldCB7XG4gIHg6IG51bWJlcjtcbiAgeTogbnVtYmVyO1xufVxuXG5sZXQgaGFzVG91Y2hlZCA9IGZhbHNlO1xuXG4vLyBDaGVjayBpZiBhbiBldmVudCB3YXMgdHJpZ2dlcmVkIGJ5IHRvdWNoXG5jb25zdCBpc1RvdWNoID0gKGU6IEV2ZW50KTogZSBpcyBUb3VjaEV2ZW50ID0+ICd0b3VjaGVzJyBpbiBlO1xuXG4vLyBQcmV2ZW50IG1vYmlsZSBicm93c2VycyBmcm9tIGhhbmRsaW5nIG1vdXNlIGV2ZW50cyAoY29uZmxpY3Rpbmcgd2l0aCB0b3VjaCBvbmVzKS5cbi8vIElmIHdlIGRldGVjdGVkIGEgdG91Y2ggaW50ZXJhY3Rpb24gYmVmb3JlLCB3ZSBwcmVmZXIgcmVhY3RpbmcgdG8gdG91Y2ggZXZlbnRzIG9ubHkuXG5jb25zdCBpc1ZhbGlkID0gKGV2ZW50OiBFdmVudCk6IGJvb2xlYW4gPT4ge1xuICBpZiAoaGFzVG91Y2hlZCAmJiAhaXNUb3VjaChldmVudCkpIHJldHVybiBmYWxzZTtcbiAgaWYgKCFoYXNUb3VjaGVkKSBoYXNUb3VjaGVkID0gaXNUb3VjaChldmVudCk7XG4gIHJldHVybiB0cnVlO1xufTtcblxuY29uc3QgcG9pbnRlck1vdmUgPSAodGFyZ2V0OiBTbGlkZXIsIGV2ZW50OiBFdmVudCk6IHZvaWQgPT4ge1xuICBjb25zdCBwb2ludGVyID0gaXNUb3VjaChldmVudCkgPyBldmVudC50b3VjaGVzWzBdIDogKGV2ZW50IGFzIE1vdXNlRXZlbnQpO1xuICBjb25zdCByZWN0ID0gdGFyZ2V0LmVsLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuXG4gIGZpcmUoXG4gICAgdGFyZ2V0LmVsLFxuICAgICdtb3ZlJyxcbiAgICB0YXJnZXQuZ2V0TW92ZSh7XG4gICAgICB4OiBjbGFtcCgocG9pbnRlci5wYWdlWCAtIChyZWN0LmxlZnQgKyB3aW5kb3cucGFnZVhPZmZzZXQpKSAvIHJlY3Qud2lkdGgpLFxuICAgICAgeTogY2xhbXAoKHBvaW50ZXIucGFnZVkgLSAocmVjdC50b3AgKyB3aW5kb3cucGFnZVlPZmZzZXQpKSAvIHJlY3QuaGVpZ2h0KVxuICAgIH0pXG4gICk7XG59O1xuXG5jb25zdCBrZXlNb3ZlID0gKHRhcmdldDogU2xpZGVyLCBldmVudDogS2V5Ym9hcmRFdmVudCk6IHZvaWQgPT4ge1xuICAvLyBXZSB1c2UgYGtleUNvZGVgIGluc3RlYWQgb2YgYGtleWAgdG8gcmVkdWNlIHRoZSBzaXplIG9mIHRoZSBsaWJyYXJ5LlxuICBjb25zdCBrZXlDb2RlID0gZXZlbnQua2V5Q29kZTtcbiAgLy8gSWdub3JlIGFsbCBrZXlzIGV4Y2VwdCBhcnJvdyBvbmVzLCBQYWdlIFVwLCBQYWdlIERvd24sIEhvbWUgYW5kIEVuZC5cbiAgaWYgKGtleUNvZGUgPiA0MCB8fCAodGFyZ2V0Lnh5ICYmIGtleUNvZGUgPCAzNykgfHwga2V5Q29kZSA8IDMzKSByZXR1cm47XG4gIC8vIERvIG5vdCBzY3JvbGwgcGFnZSBieSBrZXlzIHdoZW4gY29sb3IgcGlja2VyIGVsZW1lbnQgaGFzIGZvY3VzLlxuICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAvLyBTZW5kIHJlbGF0aXZlIG9mZnNldCB0byB0aGUgcGFyZW50IGNvbXBvbmVudC5cbiAgZmlyZShcbiAgICB0YXJnZXQuZWwsXG4gICAgJ21vdmUnLFxuICAgIHRhcmdldC5nZXRNb3ZlKFxuICAgICAge1xuICAgICAgICB4OlxuICAgICAgICAgIGtleUNvZGUgPT09IDM5IC8vIEFycm93IFJpZ2h0XG4gICAgICAgICAgICA/IDAuMDFcbiAgICAgICAgICAgIDoga2V5Q29kZSA9PT0gMzcgLy8gQXJyb3cgTGVmdFxuICAgICAgICAgICAgPyAtMC4wMVxuICAgICAgICAgICAgOiBrZXlDb2RlID09PSAzNCAvLyBQYWdlIERvd25cbiAgICAgICAgICAgID8gMC4wNVxuICAgICAgICAgICAgOiBrZXlDb2RlID09PSAzMyAvLyBQYWdlIFVwXG4gICAgICAgICAgICA/IC0wLjA1XG4gICAgICAgICAgICA6IGtleUNvZGUgPT09IDM1IC8vIEVuZFxuICAgICAgICAgICAgPyAxXG4gICAgICAgICAgICA6IGtleUNvZGUgPT09IDM2IC8vIEhvbWVcbiAgICAgICAgICAgID8gLTFcbiAgICAgICAgICAgIDogMCxcbiAgICAgICAgeTpcbiAgICAgICAgICBrZXlDb2RlID09PSA0MCAvLyBBcnJvdyBkb3duXG4gICAgICAgICAgICA/IDAuMDFcbiAgICAgICAgICAgIDoga2V5Q29kZSA9PT0gMzggLy8gQXJyb3cgVXBcbiAgICAgICAgICAgID8gLTAuMDFcbiAgICAgICAgICAgIDogMFxuICAgICAgfSxcbiAgICAgIHRydWVcbiAgICApXG4gICk7XG59O1xuXG5leHBvcnQgYWJzdHJhY3QgY2xhc3MgU2xpZGVyIHtcbiAgZGVjbGFyZSBub2RlczogSFRNTEVsZW1lbnRbXTtcblxuICBkZWNsYXJlIGVsOiBIVE1MRWxlbWVudDtcblxuICBkZWNsYXJlIHh5OiBib29sZWFuO1xuXG4gIGNvbnN0cnVjdG9yKHJvb3Q6IFNoYWRvd1Jvb3QsIHBhcnQ6IHN0cmluZywgYXJpYTogc3RyaW5nLCB4eTogYm9vbGVhbikge1xuICAgIGNvbnN0IHRlbXBsYXRlID0gdHBsKFxuICAgICAgYDxkaXYgcm9sZT1cInNsaWRlclwiIHRhYmluZGV4PVwiMFwiIHBhcnQ9XCIke3BhcnR9XCIgJHthcmlhfT48ZGl2IHBhcnQ9XCIke3BhcnR9LXBvaW50ZXJcIj48L2Rpdj48L2Rpdj5gXG4gICAgKTtcbiAgICByb290LmFwcGVuZENoaWxkKHRlbXBsYXRlLmNvbnRlbnQuY2xvbmVOb2RlKHRydWUpKTtcblxuICAgIGNvbnN0IGVsID0gcm9vdC5xdWVyeVNlbGVjdG9yKGBbcGFydD0ke3BhcnR9XWApIGFzIEhUTUxFbGVtZW50O1xuICAgIGVsLmFkZEV2ZW50TGlzdGVuZXIoJ21vdXNlZG93bicsIHRoaXMpO1xuICAgIGVsLmFkZEV2ZW50TGlzdGVuZXIoJ3RvdWNoc3RhcnQnLCB0aGlzKTtcbiAgICBlbC5hZGRFdmVudExpc3RlbmVyKCdrZXlkb3duJywgdGhpcyk7XG4gICAgdGhpcy5lbCA9IGVsO1xuXG4gICAgdGhpcy54eSA9IHh5O1xuICAgIHRoaXMubm9kZXMgPSBbZWwuZmlyc3RDaGlsZCBhcyBIVE1MRWxlbWVudCwgZWxdO1xuICB9XG5cbiAgc2V0IGRyYWdnaW5nKHN0YXRlOiBib29sZWFuKSB7XG4gICAgY29uc3QgdG9nZ2xlRXZlbnQgPSBzdGF0ZSA/IGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIgOiBkb2N1bWVudC5yZW1vdmVFdmVudExpc3RlbmVyO1xuICAgIHRvZ2dsZUV2ZW50KGhhc1RvdWNoZWQgPyAndG91Y2htb3ZlJyA6ICdtb3VzZW1vdmUnLCB0aGlzKTtcbiAgICB0b2dnbGVFdmVudChoYXNUb3VjaGVkID8gJ3RvdWNoZW5kJyA6ICdtb3VzZXVwJywgdGhpcyk7XG4gIH1cblxuICBoYW5kbGVFdmVudChldmVudDogRXZlbnQpOiB2b2lkIHtcbiAgICBzd2l0Y2ggKGV2ZW50LnR5cGUpIHtcbiAgICAgIGNhc2UgJ21vdXNlZG93bic6XG4gICAgICBjYXNlICd0b3VjaHN0YXJ0JzpcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgLy8gZXZlbnQuYnV0dG9uIGlzIDAgaW4gbW91c2Vkb3duIGZvciBsZWZ0IGJ1dHRvbiBhY3RpdmF0aW9uXG4gICAgICAgIGlmICghaXNWYWxpZChldmVudCkgfHwgKCFoYXNUb3VjaGVkICYmIChldmVudCBhcyBNb3VzZUV2ZW50KS5idXR0b24gIT0gMCkpIHJldHVybjtcbiAgICAgICAgdGhpcy5lbC5mb2N1cygpO1xuICAgICAgICBwb2ludGVyTW92ZSh0aGlzLCBldmVudCk7XG4gICAgICAgIHRoaXMuZHJhZ2dpbmcgPSB0cnVlO1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgJ21vdXNlbW92ZSc6XG4gICAgICBjYXNlICd0b3VjaG1vdmUnOlxuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBwb2ludGVyTW92ZSh0aGlzLCBldmVudCk7XG4gICAgICAgIGJyZWFrO1xuICAgICAgY2FzZSAnbW91c2V1cCc6XG4gICAgICBjYXNlICd0b3VjaGVuZCc6XG4gICAgICAgIHRoaXMuZHJhZ2dpbmcgPSBmYWxzZTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlICdrZXlkb3duJzpcbiAgICAgICAga2V5TW92ZSh0aGlzLCBldmVudCBhcyBLZXlib2FyZEV2ZW50KTtcbiAgICAgICAgYnJlYWs7XG4gICAgfVxuICB9XG5cbiAgYWJzdHJhY3QgZ2V0TW92ZShvZmZzZXQ6IE9mZnNldCwga2V5PzogYm9vbGVhbik6IFJlY29yZDxzdHJpbmcsIG51bWJlcj47XG5cbiAgYWJzdHJhY3QgdXBkYXRlKGhzdmE6IEhzdmFDb2xvcik6IHZvaWQ7XG5cbiAgc3R5bGUoc3R5bGVzOiBBcnJheTxSZWNvcmQ8c3RyaW5nLCBzdHJpbmc+Pik6IHZvaWQge1xuICAgIHN0eWxlcy5mb3JFYWNoKChzdHlsZSwgaSkgPT4ge1xuICAgICAgZm9yIChjb25zdCBwIGluIHN0eWxlKSB7XG4gICAgICAgIHRoaXMubm9kZXNbaV0uc3R5bGUuc2V0UHJvcGVydHkocCwgc3R5bGVbcF0pO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG4iLCAiaW1wb3J0IHsgU2xpZGVyLCBPZmZzZXQgfSBmcm9tICcuL3NsaWRlci5qcyc7XG5pbXBvcnQgeyBoc3ZhVG9Ic2xTdHJpbmcgfSBmcm9tICcuLi91dGlscy9jb252ZXJ0LmpzJztcbmltcG9ydCB7IGNsYW1wLCByb3VuZCB9IGZyb20gJy4uL3V0aWxzL21hdGguanMnO1xuaW1wb3J0IHR5cGUgeyBIc3ZhQ29sb3IgfSBmcm9tICcuLi90eXBlcyc7XG5cbmV4cG9ydCBjbGFzcyBIdWUgZXh0ZW5kcyBTbGlkZXIge1xuICBkZWNsYXJlIGg6IG51bWJlcjtcblxuICBjb25zdHJ1Y3Rvcihyb290OiBTaGFkb3dSb290KSB7XG4gICAgc3VwZXIocm9vdCwgJ2h1ZScsICdhcmlhLWxhYmVsPVwiSHVlXCIgYXJpYS12YWx1ZW1pbj1cIjBcIiBhcmlhLXZhbHVlbWF4PVwiMzYwXCInLCBmYWxzZSk7XG4gIH1cblxuICB1cGRhdGUoeyBoIH06IEhzdmFDb2xvcik6IHZvaWQge1xuICAgIHRoaXMuaCA9IGg7XG4gICAgdGhpcy5zdHlsZShbXG4gICAgICB7XG4gICAgICAgIGxlZnQ6IGAkeyhoIC8gMzYwKSAqIDEwMH0lYCxcbiAgICAgICAgY29sb3I6IGhzdmFUb0hzbFN0cmluZyh7IGgsIHM6IDEwMCwgdjogMTAwLCBhOiAxIH0pXG4gICAgICB9XG4gICAgXSk7XG4gICAgdGhpcy5lbC5zZXRBdHRyaWJ1dGUoJ2FyaWEtdmFsdWVub3cnLCBgJHtyb3VuZChoKX1gKTtcbiAgfVxuXG4gIGdldE1vdmUob2Zmc2V0OiBPZmZzZXQsIGtleT86IGJvb2xlYW4pOiBSZWNvcmQ8c3RyaW5nLCBudW1iZXI+IHtcbiAgICAvLyBIdWUgbWVhc3VyZWQgaW4gZGVncmVlcyBvZiB0aGUgY29sb3IgY2lyY2xlIHJhbmdpbmcgZnJvbSAwIHRvIDM2MFxuICAgIHJldHVybiB7IGg6IGtleSA/IGNsYW1wKHRoaXMuaCArIG9mZnNldC54ICogMzYwLCAwLCAzNjApIDogMzYwICogb2Zmc2V0LnggfTtcbiAgfVxufVxuIiwgImltcG9ydCB7IFNsaWRlciwgT2Zmc2V0IH0gZnJvbSAnLi9zbGlkZXIuanMnO1xuaW1wb3J0IHsgaHN2YVRvSHNsU3RyaW5nIH0gZnJvbSAnLi4vdXRpbHMvY29udmVydC5qcyc7XG5pbXBvcnQgeyBjbGFtcCwgcm91bmQgfSBmcm9tICcuLi91dGlscy9tYXRoLmpzJztcbmltcG9ydCB0eXBlIHsgSHN2YUNvbG9yIH0gZnJvbSAnLi4vdHlwZXMnO1xuXG5leHBvcnQgY2xhc3MgU2F0dXJhdGlvbiBleHRlbmRzIFNsaWRlciB7XG4gIGRlY2xhcmUgaHN2YTogSHN2YUNvbG9yO1xuXG4gIGNvbnN0cnVjdG9yKHJvb3Q6IFNoYWRvd1Jvb3QpIHtcbiAgICBzdXBlcihyb290LCAnc2F0dXJhdGlvbicsICdhcmlhLWxhYmVsPVwiQ29sb3JcIicsIHRydWUpO1xuICB9XG5cbiAgdXBkYXRlKGhzdmE6IEhzdmFDb2xvcik6IHZvaWQge1xuICAgIHRoaXMuaHN2YSA9IGhzdmE7XG4gICAgdGhpcy5zdHlsZShbXG4gICAgICB7XG4gICAgICAgIHRvcDogYCR7MTAwIC0gaHN2YS52fSVgLFxuICAgICAgICBsZWZ0OiBgJHtoc3ZhLnN9JWAsXG4gICAgICAgIGNvbG9yOiBoc3ZhVG9Ic2xTdHJpbmcoaHN2YSlcbiAgICAgIH0sXG4gICAgICB7XG4gICAgICAgICdiYWNrZ3JvdW5kLWNvbG9yJzogaHN2YVRvSHNsU3RyaW5nKHsgaDogaHN2YS5oLCBzOiAxMDAsIHY6IDEwMCwgYTogMSB9KVxuICAgICAgfVxuICAgIF0pO1xuICAgIHRoaXMuZWwuc2V0QXR0cmlidXRlKFxuICAgICAgJ2FyaWEtdmFsdWV0ZXh0JyxcbiAgICAgIGBTYXR1cmF0aW9uICR7cm91bmQoaHN2YS5zKX0lLCBCcmlnaHRuZXNzICR7cm91bmQoaHN2YS52KX0lYFxuICAgICk7XG4gIH1cblxuICBnZXRNb3ZlKG9mZnNldDogT2Zmc2V0LCBrZXk/OiBib29sZWFuKTogUmVjb3JkPHN0cmluZywgbnVtYmVyPiB7XG4gICAgLy8gU2F0dXJhdGlvbiBhbmQgYnJpZ2h0bmVzcyBhbHdheXMgZml0IGludG8gWzAsIDEwMF0gcmFuZ2VcbiAgICByZXR1cm4ge1xuICAgICAgczoga2V5ID8gY2xhbXAodGhpcy5oc3ZhLnMgKyBvZmZzZXQueCAqIDEwMCwgMCwgMTAwKSA6IG9mZnNldC54ICogMTAwLFxuICAgICAgdjoga2V5ID8gY2xhbXAodGhpcy5oc3ZhLnYgLSBvZmZzZXQueSAqIDEwMCwgMCwgMTAwKSA6IE1hdGgucm91bmQoMTAwIC0gb2Zmc2V0LnkgKiAxMDApXG4gICAgfTtcbiAgfVxufVxuIiwgImV4cG9ydCBkZWZhdWx0IGA6aG9zdHtkaXNwbGF5OmZsZXg7ZmxleC1kaXJlY3Rpb246Y29sdW1uO3Bvc2l0aW9uOnJlbGF0aXZlO3dpZHRoOjIwMHB4O2hlaWdodDoyMDBweDt1c2VyLXNlbGVjdDpub25lOy13ZWJraXQtdXNlci1zZWxlY3Q6bm9uZTtjdXJzb3I6ZGVmYXVsdH06aG9zdChbaGlkZGVuXSl7ZGlzcGxheTpub25lIWltcG9ydGFudH1bcm9sZT1zbGlkZXJde3Bvc2l0aW9uOnJlbGF0aXZlO3RvdWNoLWFjdGlvbjpub25lO3VzZXItc2VsZWN0Om5vbmU7LXdlYmtpdC11c2VyLXNlbGVjdDpub25lO291dGxpbmU6MH1bcm9sZT1zbGlkZXJdOmxhc3QtY2hpbGR7Ym9yZGVyLXJhZGl1czowIDAgOHB4IDhweH1bcGFydCQ9cG9pbnRlcl17cG9zaXRpb246YWJzb2x1dGU7ei1pbmRleDoxO2JveC1zaXppbmc6Ym9yZGVyLWJveDt3aWR0aDoyOHB4O2hlaWdodDoyOHB4O3RyYW5zZm9ybTp0cmFuc2xhdGUoLTUwJSwtNTAlKTtiYWNrZ3JvdW5kLWNvbG9yOiNmZmY7Ym9yZGVyOjJweCBzb2xpZCAjZmZmO2JvcmRlci1yYWRpdXM6NTAlO2JveC1zaGFkb3c6MCAycHggNHB4IHJnYmEoMCwwLDAsLjIpfVtwYXJ0JD1wb2ludGVyXTo6YWZ0ZXJ7ZGlzcGxheTpibG9jaztjb250ZW50OicnO3Bvc2l0aW9uOmFic29sdXRlO2xlZnQ6MDt0b3A6MDtyaWdodDowO2JvdHRvbTowO2JvcmRlci1yYWRpdXM6aW5oZXJpdDtiYWNrZ3JvdW5kLWNvbG9yOmN1cnJlbnRDb2xvcn1bcm9sZT1zbGlkZXJdOmZvY3VzIFtwYXJ0JD1wb2ludGVyXXt0cmFuc2Zvcm06dHJhbnNsYXRlKC01MCUsLTUwJSkgc2NhbGUoMS4xKX1gO1xuIiwgImV4cG9ydCBkZWZhdWx0IGBbcGFydD1odWVde2ZsZXg6MCAwIDI0cHg7YmFja2dyb3VuZDpsaW5lYXItZ3JhZGllbnQodG8gcmlnaHQscmVkIDAsI2ZmMCAxNyUsIzBmMCAzMyUsIzBmZiA1MCUsIzAwZiA2NyUsI2YwZiA4MyUscmVkIDEwMCUpfVtwYXJ0PWh1ZS1wb2ludGVyXXt0b3A6NTAlO3otaW5kZXg6Mn1gO1xuIiwgImV4cG9ydCBkZWZhdWx0IGBbcGFydD1zYXR1cmF0aW9uXXtmbGV4LWdyb3c6MTtib3JkZXItY29sb3I6dHJhbnNwYXJlbnQ7Ym9yZGVyLWJvdHRvbToxMnB4IHNvbGlkICMwMDA7Ym9yZGVyLXJhZGl1czo4cHggOHB4IDAgMDtiYWNrZ3JvdW5kLWltYWdlOmxpbmVhci1ncmFkaWVudCh0byB0b3AsIzAwMCx0cmFuc3BhcmVudCksbGluZWFyLWdyYWRpZW50KHRvIHJpZ2h0LCNmZmYscmdiYSgyNTUsMjU1LDI1NSwwKSk7Ym94LXNoYWRvdzppbnNldCAwIDAgMCAxcHggcmdiYSgwLDAsMCwuMDUpfVtwYXJ0PXNhdHVyYXRpb24tcG9pbnRlcl17ei1pbmRleDozfWA7XG4iLCAiaW1wb3J0IHsgZXF1YWxDb2xvck9iamVjdHMgfSBmcm9tICcuLi91dGlscy9jb21wYXJlLmpzJztcbmltcG9ydCB7IGZpcmUsIHRwbCB9IGZyb20gJy4uL3V0aWxzL2RvbS5qcyc7XG5pbXBvcnQgdHlwZSB7IEFueUNvbG9yLCBDb2xvck1vZGVsLCBIc3ZhQ29sb3IgfSBmcm9tICcuLi90eXBlcyc7XG5pbXBvcnQgeyBIdWUgfSBmcm9tICcuL2h1ZS5qcyc7XG5pbXBvcnQgeyBTYXR1cmF0aW9uIH0gZnJvbSAnLi9zYXR1cmF0aW9uLmpzJztcbmltcG9ydCB0eXBlIHsgU2xpZGVyIH0gZnJvbSAnLi9zbGlkZXIuanMnO1xuaW1wb3J0IGNzcyBmcm9tICcuLi9zdHlsZXMvY29sb3ItcGlja2VyLmpzJztcbmltcG9ydCBodWVDc3MgZnJvbSAnLi4vc3R5bGVzL2h1ZS5qcyc7XG5pbXBvcnQgc2F0dXJhdGlvbkNzcyBmcm9tICcuLi9zdHlsZXMvc2F0dXJhdGlvbi5qcyc7XG5cbmNvbnN0ICRpc1NhbWUgPSBTeW1ib2woJ3NhbWUnKTtcbmNvbnN0ICRjb2xvciA9IFN5bWJvbCgnY29sb3InKTtcbmNvbnN0ICRoc3ZhID0gU3ltYm9sKCdoc3ZhJyk7XG5jb25zdCAkY2hhbmdlID0gU3ltYm9sKCdjaGFuZ2UnKTtcbmNvbnN0ICR1cGRhdGUgPSBTeW1ib2woJ3VwZGF0ZScpO1xuY29uc3QgJHBhcnRzID0gU3ltYm9sKCdwYXJ0cycpO1xuXG5leHBvcnQgY29uc3QgJGNzcyA9IFN5bWJvbCgnY3NzJyk7XG5leHBvcnQgY29uc3QgJHNsaWRlcnMgPSBTeW1ib2woJ3NsaWRlcnMnKTtcblxuZXhwb3J0IHR5cGUgU2xpZGVycyA9IEFycmF5PG5ldyAocm9vdDogU2hhZG93Um9vdCkgPT4gU2xpZGVyPjtcblxuZXhwb3J0IGFic3RyYWN0IGNsYXNzIENvbG9yUGlja2VyPEMgZXh0ZW5kcyBBbnlDb2xvcj4gZXh0ZW5kcyBIVE1MRWxlbWVudCB7XG4gIHN0YXRpYyBnZXQgb2JzZXJ2ZWRBdHRyaWJ1dGVzKCk6IHN0cmluZ1tdIHtcbiAgICByZXR1cm4gWydjb2xvciddO1xuICB9XG5cbiAgcHJvdGVjdGVkIGdldCBbJGNzc10oKTogc3RyaW5nW10ge1xuICAgIHJldHVybiBbY3NzLCBodWVDc3MsIHNhdHVyYXRpb25Dc3NdO1xuICB9XG5cbiAgcHJvdGVjdGVkIGdldCBbJHNsaWRlcnNdKCk6IFNsaWRlcnMge1xuICAgIHJldHVybiBbU2F0dXJhdGlvbiwgSHVlXTtcbiAgfVxuXG4gIHByb3RlY3RlZCBhYnN0cmFjdCBnZXQgY29sb3JNb2RlbCgpOiBDb2xvck1vZGVsPEM+O1xuXG4gIHByaXZhdGUgZGVjbGFyZSBbJGhzdmFdOiBIc3ZhQ29sb3I7XG5cbiAgcHJpdmF0ZSBkZWNsYXJlIFskY29sb3JdOiBDO1xuXG4gIHByaXZhdGUgZGVjbGFyZSBbJHBhcnRzXTogU2xpZGVyW107XG5cbiAgZ2V0IGNvbG9yKCk6IEMge1xuICAgIHJldHVybiB0aGlzWyRjb2xvcl07XG4gIH1cblxuICBzZXQgY29sb3IobmV3Q29sb3I6IEMpIHtcbiAgICBpZiAoIXRoaXNbJGlzU2FtZV0obmV3Q29sb3IpKSB7XG4gICAgICBjb25zdCBuZXdIc3ZhID0gdGhpcy5jb2xvck1vZGVsLnRvSHN2YShuZXdDb2xvcik7XG4gICAgICB0aGlzWyR1cGRhdGVdKG5ld0hzdmEpO1xuICAgICAgdGhpc1skY2hhbmdlXShuZXdDb2xvcik7XG4gICAgfVxuICB9XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgc3VwZXIoKTtcbiAgICBjb25zdCB0ZW1wbGF0ZSA9IHRwbChgPHN0eWxlPiR7dGhpc1skY3NzXS5qb2luKCcnKX08L3N0eWxlPmApO1xuICAgIGNvbnN0IHJvb3QgPSB0aGlzLmF0dGFjaFNoYWRvdyh7IG1vZGU6ICdvcGVuJyB9KTtcbiAgICByb290LmFwcGVuZENoaWxkKHRlbXBsYXRlLmNvbnRlbnQuY2xvbmVOb2RlKHRydWUpKTtcbiAgICByb290LmFkZEV2ZW50TGlzdGVuZXIoJ21vdmUnLCB0aGlzKTtcbiAgICB0aGlzWyRwYXJ0c10gPSB0aGlzWyRzbGlkZXJzXS5tYXAoKHNsaWRlcikgPT4gbmV3IHNsaWRlcihyb290KSk7XG4gIH1cblxuICBjb25uZWN0ZWRDYWxsYmFjaygpOiB2b2lkIHtcbiAgICAvLyBBIHVzZXIgbWF5IHNldCBhIHByb3BlcnR5IG9uIGFuIF9pbnN0YW5jZV8gb2YgYW4gZWxlbWVudCxcbiAgICAvLyBiZWZvcmUgaXRzIHByb3RvdHlwZSBoYXMgYmVlbiBjb25uZWN0ZWQgdG8gdGhpcyBjbGFzcy5cbiAgICAvLyBJZiBzbywgd2UgbmVlZCB0byBydW4gaXQgdGhyb3VnaCB0aGUgcHJvcGVyIGNsYXNzIHNldHRlci5cbiAgICBpZiAodGhpcy5oYXNPd25Qcm9wZXJ0eSgnY29sb3InKSkge1xuICAgICAgY29uc3QgdmFsdWUgPSB0aGlzLmNvbG9yO1xuICAgICAgZGVsZXRlIHRoaXNbJ2NvbG9yJyBhcyBrZXlvZiB0aGlzXTtcbiAgICAgIHRoaXMuY29sb3IgPSB2YWx1ZTtcbiAgICB9IGVsc2UgaWYgKCF0aGlzLmNvbG9yKSB7XG4gICAgICB0aGlzLmNvbG9yID0gdGhpcy5jb2xvck1vZGVsLmRlZmF1bHRDb2xvcjtcbiAgICB9XG4gIH1cblxuICBhdHRyaWJ1dGVDaGFuZ2VkQ2FsbGJhY2soX2F0dHI6IHN0cmluZywgX29sZFZhbDogc3RyaW5nLCBuZXdWYWw6IHN0cmluZyk6IHZvaWQge1xuICAgIGNvbnN0IGNvbG9yID0gdGhpcy5jb2xvck1vZGVsLmZyb21BdHRyKG5ld1ZhbCk7XG4gICAgaWYgKCF0aGlzWyRpc1NhbWVdKGNvbG9yKSkge1xuICAgICAgdGhpcy5jb2xvciA9IGNvbG9yO1xuICAgIH1cbiAgfVxuXG4gIGhhbmRsZUV2ZW50KGV2ZW50OiBDdXN0b21FdmVudCk6IHZvaWQge1xuICAgIC8vIE1lcmdlIHRoZSBjdXJyZW50IEhTViBjb2xvciBvYmplY3Qgd2l0aCB1cGRhdGVkIHBhcmFtcy5cbiAgICBjb25zdCBvbGRIc3ZhID0gdGhpc1skaHN2YV07XG4gICAgY29uc3QgbmV3SHN2YSA9IHsgLi4ub2xkSHN2YSwgLi4uZXZlbnQuZGV0YWlsIH07XG4gICAgdGhpc1skdXBkYXRlXShuZXdIc3ZhKTtcbiAgICBsZXQgbmV3Q29sb3I7XG4gICAgaWYgKFxuICAgICAgIWVxdWFsQ29sb3JPYmplY3RzKG5ld0hzdmEsIG9sZEhzdmEpICYmXG4gICAgICAhdGhpc1skaXNTYW1lXSgobmV3Q29sb3IgPSB0aGlzLmNvbG9yTW9kZWwuZnJvbUhzdmEobmV3SHN2YSkpKVxuICAgICkge1xuICAgICAgdGhpc1skY2hhbmdlXShuZXdDb2xvcik7XG4gICAgfVxuICB9XG5cbiAgcHJpdmF0ZSBbJGlzU2FtZV0oY29sb3I6IEMpOiBib29sZWFuIHtcbiAgICByZXR1cm4gdGhpcy5jb2xvciAmJiB0aGlzLmNvbG9yTW9kZWwuZXF1YWwoY29sb3IsIHRoaXMuY29sb3IpO1xuICB9XG5cbiAgcHJpdmF0ZSBbJHVwZGF0ZV0oaHN2YTogSHN2YUNvbG9yKTogdm9pZCB7XG4gICAgdGhpc1skaHN2YV0gPSBoc3ZhO1xuICAgIHRoaXNbJHBhcnRzXS5mb3JFYWNoKChwYXJ0KSA9PiBwYXJ0LnVwZGF0ZShoc3ZhKSk7XG4gIH1cblxuICBwcml2YXRlIFskY2hhbmdlXSh2YWx1ZTogQyk6IHZvaWQge1xuICAgIHRoaXNbJGNvbG9yXSA9IHZhbHVlO1xuICAgIGZpcmUodGhpcywgJ2NvbG9yLWNoYW5nZWQnLCB7IHZhbHVlIH0pO1xuICB9XG59XG4iLCAiaW1wb3J0IHR5cGUgeyBDb2xvck1vZGVsLCBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXIsIENvbG9yUGlja2VyRXZlbnRNYXAgfSBmcm9tICcuLi90eXBlcyc7XG5pbXBvcnQgeyBDb2xvclBpY2tlciB9IGZyb20gJy4uL2NvbXBvbmVudHMvY29sb3ItcGlja2VyLmpzJztcbmltcG9ydCB7IGhleFRvSHN2YSwgaHN2YVRvSGV4IH0gZnJvbSAnLi4vdXRpbHMvY29udmVydC5qcyc7XG5pbXBvcnQgeyBlcXVhbEhleCB9IGZyb20gJy4uL3V0aWxzL2NvbXBhcmUuanMnO1xuXG5jb25zdCBjb2xvck1vZGVsOiBDb2xvck1vZGVsPHN0cmluZz4gPSB7XG4gIGRlZmF1bHRDb2xvcjogJyMwMDAnLFxuICB0b0hzdmE6IGhleFRvSHN2YSxcbiAgZnJvbUhzdmE6IGhzdmFUb0hleCxcbiAgZXF1YWw6IGVxdWFsSGV4LFxuICBmcm9tQXR0cjogKGNvbG9yKSA9PiBjb2xvclxufTtcblxuZXhwb3J0IGludGVyZmFjZSBIZXhCYXNlIHtcbiAgYWRkRXZlbnRMaXN0ZW5lcjxUIGV4dGVuZHMga2V5b2YgQ29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+PihcbiAgICB0eXBlOiBULFxuICAgIGxpc3RlbmVyOiBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXI8Q29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+W1RdPixcbiAgICBvcHRpb25zPzogYm9vbGVhbiB8IEFkZEV2ZW50TGlzdGVuZXJPcHRpb25zXG4gICk6IHZvaWQ7XG5cbiAgcmVtb3ZlRXZlbnRMaXN0ZW5lcjxUIGV4dGVuZHMga2V5b2YgQ29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+PihcbiAgICB0eXBlOiBULFxuICAgIGxpc3RlbmVyOiBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXI8Q29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+W1RdPixcbiAgICBvcHRpb25zPzogYm9vbGVhbiB8IEV2ZW50TGlzdGVuZXJPcHRpb25zXG4gICk6IHZvaWQ7XG59XG5cbmV4cG9ydCBjbGFzcyBIZXhCYXNlIGV4dGVuZHMgQ29sb3JQaWNrZXI8c3RyaW5nPiB7XG4gIHByb3RlY3RlZCBnZXQgY29sb3JNb2RlbCgpOiBDb2xvck1vZGVsPHN0cmluZz4ge1xuICAgIHJldHVybiBjb2xvck1vZGVsO1xuICB9XG59XG4iLCAiaW1wb3J0IHsgSGV4QmFzZSB9IGZyb20gJy4vbGliL2VudHJ5cG9pbnRzL2hleC5qcyc7XG5cbi8qKlxuICogQSBjb2xvciBwaWNrZXIgY3VzdG9tIGVsZW1lbnQgdGhhdCB1c2VzIEhFWCBmb3JtYXQuXG4gKlxuICogQGVsZW1lbnQgaGV4LWNvbG9yLXBpY2tlclxuICpcbiAqIEBwcm9wIHtzdHJpbmd9IGNvbG9yIC0gU2VsZWN0ZWQgY29sb3IgaW4gSEVYIGZvcm1hdC5cbiAqIEBhdHRyIHtzdHJpbmd9IGNvbG9yIC0gU2VsZWN0ZWQgY29sb3IgaW4gSEVYIGZvcm1hdC5cbiAqXG4gKiBAZmlyZXMgY29sb3ItY2hhbmdlZCAtIEV2ZW50IGZpcmVkIHdoZW4gY29sb3IgcHJvcGVydHkgY2hhbmdlcy5cbiAqXG4gKiBAY3NzcGFydCBodWUgLSBBIGh1ZSBzZWxlY3RvciBjb250YWluZXIuXG4gKiBAY3NzcGFydCBzYXR1cmF0aW9uIC0gQSBzYXR1cmF0aW9uIHNlbGVjdG9yIGNvbnRhaW5lclxuICogQGNzc3BhcnQgaHVlLXBvaW50ZXIgLSBBIGh1ZSBwb2ludGVyIGVsZW1lbnQuXG4gKiBAY3NzcGFydCBzYXR1cmF0aW9uLXBvaW50ZXIgLSBBIHNhdHVyYXRpb24gcG9pbnRlciBlbGVtZW50LlxuICovXG5leHBvcnQgY2xhc3MgSGV4Q29sb3JQaWNrZXIgZXh0ZW5kcyBIZXhCYXNlIHt9XG5cbmN1c3RvbUVsZW1lbnRzLmRlZmluZSgnaGV4LWNvbG9yLXBpY2tlcicsIEhleENvbG9yUGlja2VyKTtcblxuZGVjbGFyZSBnbG9iYWwge1xuICBpbnRlcmZhY2UgSFRNTEVsZW1lbnRUYWdOYW1lTWFwIHtcbiAgICAnaGV4LWNvbG9yLXBpY2tlcic6IEhleENvbG9yUGlja2VyO1xuICB9XG59XG4iLCAiaW1wb3J0IHR5cGUgeyBDb2xvck1vZGVsLCBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXIsIENvbG9yUGlja2VyRXZlbnRNYXAgfSBmcm9tICcuLi90eXBlcyc7XG5pbXBvcnQgeyBDb2xvclBpY2tlciB9IGZyb20gJy4uL2NvbXBvbmVudHMvY29sb3ItcGlja2VyLmpzJztcbmltcG9ydCB7IGhzbFN0cmluZ1RvSHN2YSwgaHN2YVRvSHNsU3RyaW5nIH0gZnJvbSAnLi4vdXRpbHMvY29udmVydC5qcyc7XG5pbXBvcnQgeyBlcXVhbENvbG9yU3RyaW5nIH0gZnJvbSAnLi4vdXRpbHMvY29tcGFyZS5qcyc7XG5cbmNvbnN0IGNvbG9yTW9kZWw6IENvbG9yTW9kZWw8c3RyaW5nPiA9IHtcbiAgZGVmYXVsdENvbG9yOiAnaHNsKDAsIDAlLCAwJSknLFxuICB0b0hzdmE6IGhzbFN0cmluZ1RvSHN2YSxcbiAgZnJvbUhzdmE6IGhzdmFUb0hzbFN0cmluZyxcbiAgZXF1YWw6IGVxdWFsQ29sb3JTdHJpbmcsXG4gIGZyb21BdHRyOiAoY29sb3IpID0+IGNvbG9yXG59O1xuXG5leHBvcnQgaW50ZXJmYWNlIEhzbFN0cmluZ0Jhc2Uge1xuICBhZGRFdmVudExpc3RlbmVyPFQgZXh0ZW5kcyBrZXlvZiBDb2xvclBpY2tlckV2ZW50TWFwPHN0cmluZz4+KFxuICAgIHR5cGU6IFQsXG4gICAgbGlzdGVuZXI6IENvbG9yUGlja2VyRXZlbnRMaXN0ZW5lcjxDb2xvclBpY2tlckV2ZW50TWFwPHN0cmluZz5bVF0+LFxuICAgIG9wdGlvbnM/OiBib29sZWFuIHwgQWRkRXZlbnRMaXN0ZW5lck9wdGlvbnNcbiAgKTogdm9pZDtcblxuICByZW1vdmVFdmVudExpc3RlbmVyPFQgZXh0ZW5kcyBrZXlvZiBDb2xvclBpY2tlckV2ZW50TWFwPHN0cmluZz4+KFxuICAgIHR5cGU6IFQsXG4gICAgbGlzdGVuZXI6IENvbG9yUGlja2VyRXZlbnRMaXN0ZW5lcjxDb2xvclBpY2tlckV2ZW50TWFwPHN0cmluZz5bVF0+LFxuICAgIG9wdGlvbnM/OiBib29sZWFuIHwgRXZlbnRMaXN0ZW5lck9wdGlvbnNcbiAgKTogdm9pZDtcbn1cblxuZXhwb3J0IGNsYXNzIEhzbFN0cmluZ0Jhc2UgZXh0ZW5kcyBDb2xvclBpY2tlcjxzdHJpbmc+IHtcbiAgcHJvdGVjdGVkIGdldCBjb2xvck1vZGVsKCk6IENvbG9yTW9kZWw8c3RyaW5nPiB7XG4gICAgcmV0dXJuIGNvbG9yTW9kZWw7XG4gIH1cbn1cbiIsICJpbXBvcnQgeyBIc2xTdHJpbmdCYXNlIH0gZnJvbSAnLi9saWIvZW50cnlwb2ludHMvaHNsLXN0cmluZy5qcyc7XG5cbi8qKlxuICogQSBjb2xvciBwaWNrZXIgY3VzdG9tIGVsZW1lbnQgdGhhdCB1c2VzIEhTTCBzdHJpbmcgZm9ybWF0LlxuICpcbiAqIEBlbGVtZW50IGhzbC1zdHJpbmctY29sb3ItcGlja2VyXG4gKlxuICogQHByb3Age3N0cmluZ30gY29sb3IgLSBTZWxlY3RlZCBjb2xvciBpbiBIU0wgc3RyaW5nIGZvcm1hdC5cbiAqIEBhdHRyIHtzdHJpbmd9IGNvbG9yIC0gU2VsZWN0ZWQgY29sb3IgaW4gSFNMIHN0cmluZyBmb3JtYXQuXG4gKlxuICogQGZpcmVzIGNvbG9yLWNoYW5nZWQgLSBFdmVudCBmaXJlZCB3aGVuIGNvbG9yIHByb3BlcnR5IGNoYW5nZXMuXG4gKlxuICogQGNzc3BhcnQgaHVlIC0gQSBodWUgc2VsZWN0b3IgY29udGFpbmVyLlxuICogQGNzc3BhcnQgc2F0dXJhdGlvbiAtIEEgc2F0dXJhdGlvbiBzZWxlY3RvciBjb250YWluZXJcbiAqIEBjc3NwYXJ0IGh1ZS1wb2ludGVyIC0gQSBodWUgcG9pbnRlciBlbGVtZW50LlxuICogQGNzc3BhcnQgc2F0dXJhdGlvbi1wb2ludGVyIC0gQSBzYXR1cmF0aW9uIHBvaW50ZXIgZWxlbWVudC5cbiAqL1xuZXhwb3J0IGNsYXNzIEhzbFN0cmluZ0NvbG9yUGlja2VyIGV4dGVuZHMgSHNsU3RyaW5nQmFzZSB7fVxuXG5jdXN0b21FbGVtZW50cy5kZWZpbmUoJ2hzbC1zdHJpbmctY29sb3ItcGlja2VyJywgSHNsU3RyaW5nQ29sb3JQaWNrZXIpO1xuXG5kZWNsYXJlIGdsb2JhbCB7XG4gIGludGVyZmFjZSBIVE1MRWxlbWVudFRhZ05hbWVNYXAge1xuICAgICdoc2wtc3RyaW5nLWNvbG9yLXBpY2tlcic6IEhzbFN0cmluZ0NvbG9yUGlja2VyO1xuICB9XG59XG4iLCAiaW1wb3J0IHR5cGUgeyBDb2xvck1vZGVsLCBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXIsIENvbG9yUGlja2VyRXZlbnRNYXAgfSBmcm9tICcuLi90eXBlcyc7XG5pbXBvcnQgeyBDb2xvclBpY2tlciB9IGZyb20gJy4uL2NvbXBvbmVudHMvY29sb3ItcGlja2VyLmpzJztcbmltcG9ydCB7IHJnYlN0cmluZ1RvSHN2YSwgaHN2YVRvUmdiU3RyaW5nIH0gZnJvbSAnLi4vdXRpbHMvY29udmVydC5qcyc7XG5pbXBvcnQgeyBlcXVhbENvbG9yU3RyaW5nIH0gZnJvbSAnLi4vdXRpbHMvY29tcGFyZS5qcyc7XG5cbmNvbnN0IGNvbG9yTW9kZWw6IENvbG9yTW9kZWw8c3RyaW5nPiA9IHtcbiAgZGVmYXVsdENvbG9yOiAncmdiKDAsIDAsIDApJyxcbiAgdG9Ic3ZhOiByZ2JTdHJpbmdUb0hzdmEsXG4gIGZyb21Ic3ZhOiBoc3ZhVG9SZ2JTdHJpbmcsXG4gIGVxdWFsOiBlcXVhbENvbG9yU3RyaW5nLFxuICBmcm9tQXR0cjogKGNvbG9yKSA9PiBjb2xvclxufTtcblxuZXhwb3J0IGludGVyZmFjZSBSZ2JTdHJpbmdCYXNlIHtcbiAgYWRkRXZlbnRMaXN0ZW5lcjxUIGV4dGVuZHMga2V5b2YgQ29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+PihcbiAgICB0eXBlOiBULFxuICAgIGxpc3RlbmVyOiBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXI8Q29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+W1RdPixcbiAgICBvcHRpb25zPzogYm9vbGVhbiB8IEFkZEV2ZW50TGlzdGVuZXJPcHRpb25zXG4gICk6IHZvaWQ7XG5cbiAgcmVtb3ZlRXZlbnRMaXN0ZW5lcjxUIGV4dGVuZHMga2V5b2YgQ29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+PihcbiAgICB0eXBlOiBULFxuICAgIGxpc3RlbmVyOiBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXI8Q29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+W1RdPixcbiAgICBvcHRpb25zPzogYm9vbGVhbiB8IEV2ZW50TGlzdGVuZXJPcHRpb25zXG4gICk6IHZvaWQ7XG59XG5cbmV4cG9ydCBjbGFzcyBSZ2JTdHJpbmdCYXNlIGV4dGVuZHMgQ29sb3JQaWNrZXI8c3RyaW5nPiB7XG4gIHByb3RlY3RlZCBnZXQgY29sb3JNb2RlbCgpOiBDb2xvck1vZGVsPHN0cmluZz4ge1xuICAgIHJldHVybiBjb2xvck1vZGVsO1xuICB9XG59XG4iLCAiaW1wb3J0IHsgUmdiU3RyaW5nQmFzZSB9IGZyb20gJy4vbGliL2VudHJ5cG9pbnRzL3JnYi1zdHJpbmcuanMnO1xuXG4vKipcbiAqIEEgY29sb3IgcGlja2VyIGN1c3RvbSBlbGVtZW50IHRoYXQgdXNlcyBSR0Igc3RyaW5nIGZvcm1hdC5cbiAqXG4gKiBAZWxlbWVudCByZ2Itc3RyaW5nLWNvbG9yLXBpY2tlclxuICpcbiAqIEBwcm9wIHtzdHJpbmd9IGNvbG9yIC0gU2VsZWN0ZWQgY29sb3IgaW4gUkdCIHN0cmluZyBmb3JtYXQuXG4gKiBAYXR0ciB7c3RyaW5nfSBjb2xvciAtIFNlbGVjdGVkIGNvbG9yIGluIFJHQiBzdHJpbmcgZm9ybWF0LlxuICpcbiAqIEBmaXJlcyBjb2xvci1jaGFuZ2VkIC0gRXZlbnQgZmlyZWQgd2hlbiBjb2xvciBwcm9wZXJ0eSBjaGFuZ2VzLlxuICpcbiAqIEBjc3NwYXJ0IGh1ZSAtIEEgaHVlIHNlbGVjdG9yIGNvbnRhaW5lci5cbiAqIEBjc3NwYXJ0IHNhdHVyYXRpb24gLSBBIHNhdHVyYXRpb24gc2VsZWN0b3IgY29udGFpbmVyXG4gKiBAY3NzcGFydCBodWUtcG9pbnRlciAtIEEgaHVlIHBvaW50ZXIgZWxlbWVudC5cbiAqIEBjc3NwYXJ0IHNhdHVyYXRpb24tcG9pbnRlciAtIEEgc2F0dXJhdGlvbiBwb2ludGVyIGVsZW1lbnQuXG4gKi9cbmV4cG9ydCBjbGFzcyBSZ2JTdHJpbmdDb2xvclBpY2tlciBleHRlbmRzIFJnYlN0cmluZ0Jhc2Uge31cblxuY3VzdG9tRWxlbWVudHMuZGVmaW5lKCdyZ2Itc3RyaW5nLWNvbG9yLXBpY2tlcicsIFJnYlN0cmluZ0NvbG9yUGlja2VyKTtcblxuZGVjbGFyZSBnbG9iYWwge1xuICBpbnRlcmZhY2UgSFRNTEVsZW1lbnRUYWdOYW1lTWFwIHtcbiAgICAncmdiLXN0cmluZy1jb2xvci1waWNrZXInOiBSZ2JTdHJpbmdDb2xvclBpY2tlcjtcbiAgfVxufVxuIiwgImltcG9ydCB7IFNsaWRlciwgT2Zmc2V0IH0gZnJvbSAnLi9zbGlkZXIuanMnO1xuaW1wb3J0IHsgaHN2YVRvSHNsYVN0cmluZyB9IGZyb20gJy4uL3V0aWxzL2NvbnZlcnQuanMnO1xuaW1wb3J0IHsgY2xhbXAsIHJvdW5kIH0gZnJvbSAnLi4vdXRpbHMvbWF0aC5qcyc7XG5pbXBvcnQgdHlwZSB7IEhzdmFDb2xvciB9IGZyb20gJy4uL3R5cGVzJztcblxuZXhwb3J0IGNsYXNzIEFscGhhIGV4dGVuZHMgU2xpZGVyIHtcbiAgZGVjbGFyZSBoc3ZhOiBIc3ZhQ29sb3I7XG5cbiAgY29uc3RydWN0b3Iocm9vdDogU2hhZG93Um9vdCkge1xuICAgIHN1cGVyKHJvb3QsICdhbHBoYScsICdhcmlhLWxhYmVsPVwiQWxwaGFcIiBhcmlhLXZhbHVlbWluPVwiMFwiIGFyaWEtdmFsdWVtYXg9XCIxXCInLCBmYWxzZSk7XG4gIH1cblxuICB1cGRhdGUoaHN2YTogSHN2YUNvbG9yKTogdm9pZCB7XG4gICAgdGhpcy5oc3ZhID0gaHN2YTtcbiAgICBjb25zdCBjb2xvckZyb20gPSBoc3ZhVG9Ic2xhU3RyaW5nKHsgLi4uaHN2YSwgYTogMCB9KTtcbiAgICBjb25zdCBjb2xvclRvID0gaHN2YVRvSHNsYVN0cmluZyh7IC4uLmhzdmEsIGE6IDEgfSk7XG4gICAgY29uc3QgdmFsdWUgPSBoc3ZhLmEgKiAxMDA7XG5cbiAgICB0aGlzLnN0eWxlKFtcbiAgICAgIHtcbiAgICAgICAgbGVmdDogYCR7dmFsdWV9JWAsXG4gICAgICAgIGNvbG9yOiBoc3ZhVG9Ic2xhU3RyaW5nKGhzdmEpXG4gICAgICB9LFxuICAgICAge1xuICAgICAgICAnLS1ncmFkaWVudCc6IGBsaW5lYXItZ3JhZGllbnQoOTBkZWcsICR7Y29sb3JGcm9tfSwgJHtjb2xvclRvfWBcbiAgICAgIH1cbiAgICBdKTtcblxuICAgIGNvbnN0IHYgPSByb3VuZCh2YWx1ZSk7XG4gICAgdGhpcy5lbC5zZXRBdHRyaWJ1dGUoJ2FyaWEtdmFsdWVub3cnLCBgJHt2fWApO1xuICAgIHRoaXMuZWwuc2V0QXR0cmlidXRlKCdhcmlhLXZhbHVldGV4dCcsIGAke3Z9JWApO1xuICB9XG5cbiAgZ2V0TW92ZShvZmZzZXQ6IE9mZnNldCwga2V5PzogYm9vbGVhbik6IFJlY29yZDxzdHJpbmcsIG51bWJlcj4ge1xuICAgIC8vIEFscGhhIGFsd2F5cyBmaXQgaW50byBbMCwgMV0gcmFuZ2VcbiAgICByZXR1cm4geyBhOiBrZXkgPyBjbGFtcCh0aGlzLmhzdmEuYSArIG9mZnNldC54KSA6IG9mZnNldC54IH07XG4gIH1cbn1cbiIsICJleHBvcnQgZGVmYXVsdCBgW3BhcnQ9YWxwaGFde2ZsZXg6MCAwIDI0cHh9W3BhcnQ9YWxwaGFdOjphZnRlcntkaXNwbGF5OmJsb2NrO2NvbnRlbnQ6Jyc7cG9zaXRpb246YWJzb2x1dGU7dG9wOjA7bGVmdDowO3JpZ2h0OjA7Ym90dG9tOjA7Ym9yZGVyLXJhZGl1czppbmhlcml0O2JhY2tncm91bmQtaW1hZ2U6dmFyKC0tZ3JhZGllbnQpO2JveC1zaGFkb3c6aW5zZXQgMCAwIDAgMXB4IHJnYmEoMCwwLDAsLjA1KX1bcGFydF49YWxwaGFde2JhY2tncm91bmQtY29sb3I6I2ZmZjtiYWNrZ3JvdW5kLWltYWdlOnVybCgnZGF0YTppbWFnZS9zdmcreG1sLDxzdmcgeG1sbnM9XCJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2Z1wiIHdpZHRoPVwiMTZcIiBoZWlnaHQ9XCIxNlwiIGZpbGwtb3BhY2l0eT1cIi4wNVwiPjxyZWN0IHg9XCI4XCIgd2lkdGg9XCI4XCIgaGVpZ2h0PVwiOFwiLz48cmVjdCB5PVwiOFwiIHdpZHRoPVwiOFwiIGhlaWdodD1cIjhcIi8+PC9zdmc+Jyl9W3BhcnQ9YWxwaGEtcG9pbnRlcl17dG9wOjUwJX1gO1xuIiwgImltcG9ydCB7IENvbG9yUGlja2VyLCBTbGlkZXJzLCAkY3NzLCAkc2xpZGVycyB9IGZyb20gJy4vY29sb3ItcGlja2VyLmpzJztcbmltcG9ydCB0eXBlIHsgQW55Q29sb3IgfSBmcm9tICcuLi90eXBlcyc7XG5pbXBvcnQgeyBBbHBoYSB9IGZyb20gJy4vYWxwaGEuanMnO1xuaW1wb3J0IGFscGhhQ3NzIGZyb20gJy4uL3N0eWxlcy9hbHBoYS5qcyc7XG5cbmV4cG9ydCBhYnN0cmFjdCBjbGFzcyBBbHBoYUNvbG9yUGlja2VyPEMgZXh0ZW5kcyBBbnlDb2xvcj4gZXh0ZW5kcyBDb2xvclBpY2tlcjxDPiB7XG4gIHByb3RlY3RlZCBnZXQgWyRjc3NdKCk6IHN0cmluZ1tdIHtcbiAgICByZXR1cm4gWy4uLnN1cGVyWyRjc3NdLCBhbHBoYUNzc107XG4gIH1cblxuICBwcm90ZWN0ZWQgZ2V0IFskc2xpZGVyc10oKTogU2xpZGVycyB7XG4gICAgcmV0dXJuIFsuLi5zdXBlclskc2xpZGVyc10sIEFscGhhXTtcbiAgfVxufVxuIiwgImltcG9ydCB0eXBlIHsgQ29sb3JNb2RlbCwgQ29sb3JQaWNrZXJFdmVudExpc3RlbmVyLCBDb2xvclBpY2tlckV2ZW50TWFwIH0gZnJvbSAnLi4vdHlwZXMnO1xuaW1wb3J0IHsgQWxwaGFDb2xvclBpY2tlciB9IGZyb20gJy4uL2NvbXBvbmVudHMvYWxwaGEtY29sb3ItcGlja2VyLmpzJztcbmltcG9ydCB7IHJnYmFTdHJpbmdUb0hzdmEsIGhzdmFUb1JnYmFTdHJpbmcgfSBmcm9tICcuLi91dGlscy9jb252ZXJ0LmpzJztcbmltcG9ydCB7IGVxdWFsQ29sb3JTdHJpbmcgfSBmcm9tICcuLi91dGlscy9jb21wYXJlLmpzJztcblxuY29uc3QgY29sb3JNb2RlbDogQ29sb3JNb2RlbDxzdHJpbmc+ID0ge1xuICBkZWZhdWx0Q29sb3I6ICdyZ2JhKDAsIDAsIDAsIDEpJyxcbiAgdG9Ic3ZhOiByZ2JhU3RyaW5nVG9Ic3ZhLFxuICBmcm9tSHN2YTogaHN2YVRvUmdiYVN0cmluZyxcbiAgZXF1YWw6IGVxdWFsQ29sb3JTdHJpbmcsXG4gIGZyb21BdHRyOiAoY29sb3IpID0+IGNvbG9yXG59O1xuXG5leHBvcnQgaW50ZXJmYWNlIFJnYmFTdHJpbmdCYXNlIHtcbiAgYWRkRXZlbnRMaXN0ZW5lcjxUIGV4dGVuZHMga2V5b2YgQ29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+PihcbiAgICB0eXBlOiBULFxuICAgIGxpc3RlbmVyOiBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXI8Q29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+W1RdPixcbiAgICBvcHRpb25zPzogYm9vbGVhbiB8IEFkZEV2ZW50TGlzdGVuZXJPcHRpb25zXG4gICk6IHZvaWQ7XG5cbiAgcmVtb3ZlRXZlbnRMaXN0ZW5lcjxUIGV4dGVuZHMga2V5b2YgQ29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+PihcbiAgICB0eXBlOiBULFxuICAgIGxpc3RlbmVyOiBDb2xvclBpY2tlckV2ZW50TGlzdGVuZXI8Q29sb3JQaWNrZXJFdmVudE1hcDxzdHJpbmc+W1RdPixcbiAgICBvcHRpb25zPzogYm9vbGVhbiB8IEV2ZW50TGlzdGVuZXJPcHRpb25zXG4gICk6IHZvaWQ7XG59XG5cbmV4cG9ydCBjbGFzcyBSZ2JhU3RyaW5nQmFzZSBleHRlbmRzIEFscGhhQ29sb3JQaWNrZXI8c3RyaW5nPiB7XG4gIHByb3RlY3RlZCBnZXQgY29sb3JNb2RlbCgpOiBDb2xvck1vZGVsPHN0cmluZz4ge1xuICAgIHJldHVybiBjb2xvck1vZGVsO1xuICB9XG59XG4iLCAiaW1wb3J0IHsgUmdiYVN0cmluZ0Jhc2UgfSBmcm9tICcuL2xpYi9lbnRyeXBvaW50cy9yZ2JhLXN0cmluZy5qcyc7XG5cbi8qKlxuICogQSBjb2xvciBwaWNrZXIgY3VzdG9tIGVsZW1lbnQgdGhhdCB1c2VzIFJHQkEgc3RyaW5nIGZvcm1hdC5cbiAqXG4gKiBAZWxlbWVudCByZ2JhLXN0cmluZy1jb2xvci1waWNrZXJcbiAqXG4gKiBAcHJvcCB7c3RyaW5nfSBjb2xvciAtIFNlbGVjdGVkIGNvbG9yIGluIFJHQkEgc3RyaW5nIGZvcm1hdC5cbiAqIEBhdHRyIHtzdHJpbmd9IGNvbG9yIC0gU2VsZWN0ZWQgY29sb3IgaW4gUkdCQSBzdHJpbmcgZm9ybWF0LlxuICpcbiAqIEBmaXJlcyBjb2xvci1jaGFuZ2VkIC0gRXZlbnQgZmlyZWQgd2hlbiBjb2xvciBwcm9wZXJ0eSBjaGFuZ2VzLlxuICpcbiAqIEBjc3NwYXJ0IGh1ZSAtIEEgaHVlIHNlbGVjdG9yIGNvbnRhaW5lci5cbiAqIEBjc3NwYXJ0IHNhdHVyYXRpb24gLSBBIHNhdHVyYXRpb24gc2VsZWN0b3IgY29udGFpbmVyXG4gKiBAY3NzcGFydCBhbHBoYSAtIEFuIGFscGhhIHNlbGVjdG9yIGNvbnRhaW5lci5cbiAqIEBjc3NwYXJ0IGh1ZS1wb2ludGVyIC0gQSBodWUgcG9pbnRlciBlbGVtZW50LlxuICogQGNzc3BhcnQgc2F0dXJhdGlvbi1wb2ludGVyIC0gQSBzYXR1cmF0aW9uIHBvaW50ZXIgZWxlbWVudC5cbiAqIEBjc3NwYXJ0IGFscGhhLXBvaW50ZXIgLSBBbiBhbHBoYSBwb2ludGVyIGVsZW1lbnQuXG4gKi9cbmV4cG9ydCBjbGFzcyBSZ2JhU3RyaW5nQ29sb3JQaWNrZXIgZXh0ZW5kcyBSZ2JhU3RyaW5nQmFzZSB7fVxuXG5jdXN0b21FbGVtZW50cy5kZWZpbmUoJ3JnYmEtc3RyaW5nLWNvbG9yLXBpY2tlcicsIFJnYmFTdHJpbmdDb2xvclBpY2tlcik7XG5cbmRlY2xhcmUgZ2xvYmFsIHtcbiAgaW50ZXJmYWNlIEhUTUxFbGVtZW50VGFnTmFtZU1hcCB7XG4gICAgJ3JnYmEtc3RyaW5nLWNvbG9yLXBpY2tlcic6IFJnYmFTdHJpbmdDb2xvclBpY2tlcjtcbiAgfVxufVxuIiwgImltcG9ydCAndmFuaWxsYS1jb2xvcmZ1bC9oZXgtY29sb3ItcGlja2VyLmpzJ1xuaW1wb3J0ICd2YW5pbGxhLWNvbG9yZnVsL2hzbC1zdHJpbmctY29sb3ItcGlja2VyLmpzJ1xuaW1wb3J0ICd2YW5pbGxhLWNvbG9yZnVsL3JnYi1zdHJpbmctY29sb3ItcGlja2VyLmpzJ1xuaW1wb3J0ICd2YW5pbGxhLWNvbG9yZnVsL3JnYmEtc3RyaW5nLWNvbG9yLXBpY2tlci5qcydcblxuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gY29sb3JQaWNrZXJGb3JtQ29tcG9uZW50KHtcbiAgICBpc0F1dG9mb2N1c2VkLFxuICAgIGlzRGlzYWJsZWQsXG4gICAgc3RhdGUsXG59KSB7XG4gICAgcmV0dXJuIHtcbiAgICAgICAgc3RhdGUsXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYgKCEodGhpcy5zdGF0ZSA9PT0gbnVsbCB8fCB0aGlzLnN0YXRlID09PSAnJykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHRoaXMuc3RhdGUpXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChpc0F1dG9mb2N1c2VkKSB7XG4gICAgICAgICAgICAgICAgdGhpcy50b2dnbGVQYW5lbFZpc2liaWxpdHkodGhpcy4kcmVmcy5pbnB1dClcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy4kcmVmcy5pbnB1dC5hZGRFdmVudExpc3RlbmVyKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKGV2ZW50LnRhcmdldC52YWx1ZSlcbiAgICAgICAgICAgIH0pXG5cbiAgICAgICAgICAgIHRoaXMuJHJlZnMucGFuZWwuYWRkRXZlbnRMaXN0ZW5lcignY29sb3ItY2hhbmdlZCcsIChldmVudCkgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoZXZlbnQuZGV0YWlsLnZhbHVlKVxuICAgICAgICAgICAgfSlcbiAgICAgICAgfSxcblxuICAgICAgICB0b2dnbGVQYW5lbFZpc2liaWxpdHk6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmIChpc0Rpc2FibGVkKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuJHJlZnMucGFuZWwudG9nZ2xlKHRoaXMuJHJlZnMuaW5wdXQpXG4gICAgICAgIH0sXG5cbiAgICAgICAgc2V0U3RhdGU6IGZ1bmN0aW9uICh2YWx1ZSkge1xuICAgICAgICAgICAgdGhpcy5zdGF0ZSA9IHZhbHVlXG5cbiAgICAgICAgICAgIHRoaXMuJHJlZnMuaW5wdXQudmFsdWUgPSB2YWx1ZVxuICAgICAgICAgICAgdGhpcy4kcmVmcy5wYW5lbC5jb2xvciA9IHZhbHVlXG4gICAgICAgIH0sXG5cbiAgICAgICAgaXNPcGVuOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy4kcmVmcy5wYW5lbC5zdHlsZS5kaXNwbGF5ID09PSAnYmxvY2snXG4gICAgICAgIH0sXG4gICAgfVxufVxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUdPLElBQU0sUUFBUSxDQUFDLFFBQWdCLE1BQU0sR0FBRyxNQUFNLE1BQWE7QUFDaEUsU0FBTyxTQUFTLE1BQU0sTUFBTSxTQUFTLE1BQU0sTUFBTTtBQUNuRDtBQUVPLElBQU0sUUFBUSxDQUFDLFFBQWdCLFNBQVMsR0FBRyxPQUFPLEtBQUssSUFBSSxJQUFJLE1BQU0sTUFBYTtBQUN2RixTQUFPLEtBQUssTUFBTSxPQUFPLE1BQU0sSUFBSTtBQUNyQzs7O0FDRkEsSUFBTSxhQUFxQztFQUN6QyxNQUFNLE1BQU07RUFDWixNQUFNO0VBQ04sS0FBSyxPQUFPLEtBQUssS0FBSzs7QUFHakIsSUFBTSxZQUFZLENBQUMsUUFBMkIsV0FBVyxVQUFVLEdBQUcsQ0FBQztBQUV2RSxJQUFNLFlBQVksQ0FBQyxRQUEwQjtBQUNsRCxNQUFJLElBQUksQ0FBQyxNQUFNO0FBQUssVUFBTSxJQUFJLE9BQU8sQ0FBQztBQUV0QyxNQUFJLElBQUksU0FBUyxHQUFHO0FBQ2xCLFdBQU87TUFDTCxHQUFHLFNBQVMsSUFBSSxDQUFDLElBQUksSUFBSSxDQUFDLEdBQUcsRUFBRTtNQUMvQixHQUFHLFNBQVMsSUFBSSxDQUFDLElBQUksSUFBSSxDQUFDLEdBQUcsRUFBRTtNQUMvQixHQUFHLFNBQVMsSUFBSSxDQUFDLElBQUksSUFBSSxDQUFDLEdBQUcsRUFBRTtNQUMvQixHQUFHOzs7QUFJUCxTQUFPO0lBQ0wsR0FBRyxTQUFTLElBQUksT0FBTyxHQUFHLENBQUMsR0FBRyxFQUFFO0lBQ2hDLEdBQUcsU0FBUyxJQUFJLE9BQU8sR0FBRyxDQUFDLEdBQUcsRUFBRTtJQUNoQyxHQUFHLFNBQVMsSUFBSSxPQUFPLEdBQUcsQ0FBQyxHQUFHLEVBQUU7SUFDaEMsR0FBRzs7QUFFUDtBQUVPLElBQU0sV0FBVyxDQUFDLE9BQWUsT0FBTyxVQUFpQjtBQUM5RCxT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RyxJQUFJO0VBQ2hEO0VBRUEsUUFBUSxRQUFnQixLQUFhO0FBRW5DLFdBQU8sRUFBRSxHQUFHLE1BQU0sTUFBTSxLQUFLLEtBQUssSUFBSSxPQUFPLENBQUMsSUFBSSxPQUFPLEVBQUM7RUFDNUQ7Ozs7QUNwQ0YsSUFBQSxnQkFBZTs7O0FDS1QsSUFBZ0IsbUJBQWhCLGNBQTZELFlBQWM7RUFDL0UsS0FBZSxJQUFJLElBQUM7QUFDbEIsV0FBTyxDQUFDLEdBQUcsTUFBTSxJQUFJLEdBQUcsYUFBUTtFQUNsQztFQUVBLEtBQWUsUUFBUSxJQUFDO0FBQ3RCLFdBQU8sQ0FBQyxHQUFHLE1BQU0sUUFBUSxHQUFHLEtBQUs7RUFDbkM7Ozs7QUNQRixJQUFNQyxjQUFpQztFQUNyQyxjQUFjO0VBQ2QsUUFBUTtFQUNSLFVBQVU7RUFDVixPQUFPO0VBQ1AsVUFBVSxDQUFDLFVBQVU7O0FBaUJqQixJQUFPLGlCQUFQLGNBQThCLGlCQUF3QjtFQUMxRCxJQUFjLGFBQVU7QUFDdEIsV0FBT0E7RUFDVDs7OztBQ1hJLElBQU8sd0JBQVAsY0FBcUMsZUFBYzs7QUFFekQsZUFBZSxPQUFPLDRCQUE0QixxQkFBcUI7OztBQ2hCeEQsU0FBUix5QkFBMEM7QUFBQSxFQUM3QztBQUFBLEVBQ0E7QUFBQSxFQUNBO0FBQ0osR0FBRztBQUNDLFNBQU87QUFBQSxJQUNIO0FBQUEsSUFFQSxNQUFNLFdBQVk7QUFDZCxVQUFJLEVBQUUsS0FBSyxVQUFVLFFBQVEsS0FBSyxVQUFVLEtBQUs7QUFDN0MsYUFBSyxTQUFTLEtBQUssS0FBSztBQUFBLE1BQzVCO0FBRUEsVUFBSSxlQUFlO0FBQ2YsYUFBSyxzQkFBc0IsS0FBSyxNQUFNLEtBQUs7QUFBQSxNQUMvQztBQUVBLFdBQUssTUFBTSxNQUFNLGlCQUFpQixVQUFVLENBQUMsVUFBVTtBQUNuRCxhQUFLLFNBQVMsTUFBTSxPQUFPLEtBQUs7QUFBQSxNQUNwQyxDQUFDO0FBRUQsV0FBSyxNQUFNLE1BQU0saUJBQWlCLGlCQUFpQixDQUFDLFVBQVU7QUFDMUQsYUFBSyxTQUFTLE1BQU0sT0FBTyxLQUFLO0FBQUEsTUFDcEMsQ0FBQztBQUFBLElBQ0w7QUFBQSxJQUVBLHVCQUF1QixXQUFZO0FBQy9CLFVBQUksWUFBWTtBQUNaO0FBQUEsTUFDSjtBQUVBLFdBQUssTUFBTSxNQUFNLE9BQU8sS0FBSyxNQUFNLEtBQUs7QUFBQSxJQUM1QztBQUFBLElBRUEsVUFBVSxTQUFVLE9BQU87QUFDdkIsV0FBSyxRQUFRO0FBRWIsV0FBSyxNQUFNLE1BQU0sUUFBUTtBQUN6QixXQUFLLE1BQU0sTUFBTSxRQUFRO0FBQUEsSUFDN0I7QUFBQSxJQUVBLFFBQVEsV0FBWTtBQUNoQixhQUFPLEtBQUssTUFBTSxNQUFNLE1BQU0sWUFBWTtBQUFBLElBQzlDO0FBQUEsRUFDSjtBQUNKOyIsCiAgIm5hbWVzIjogWyJjb2xvck1vZGVsIiwgImNvbG9yTW9kZWwiLCAiY29sb3JNb2RlbCJdCn0K
