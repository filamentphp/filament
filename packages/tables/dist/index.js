(() => {
  // node_modules/@alpinejs/collapse/dist/module.esm.js
  function src_default(Alpine) {
    Alpine.directive("collapse", collapse);
    collapse.inline = (el, { modifiers }) => {
      if (!modifiers.includes("min"))
        return;
      el._x_doShow = () => {
      };
      el._x_doHide = () => {
      };
    };
    function collapse(el, { modifiers }) {
      let duration = modifierValue(modifiers, "duration", 250) / 1e3;
      let floor = modifierValue(modifiers, "min", 0);
      let fullyHide = !modifiers.includes("min");
      if (!el._x_isShown)
        el.style.height = `${floor}px`;
      if (!el._x_isShown && fullyHide)
        el.hidden = true;
      if (!el._x_isShown)
        el.style.overflow = "hidden";
      let setFunction = (el2, styles) => {
        let revertFunction = Alpine.setStyles(el2, styles);
        return styles.height ? () => {
        } : revertFunction;
      };
      let transitionStyles = {
        transitionProperty: "height",
        transitionDuration: `${duration}s`,
        transitionTimingFunction: "cubic-bezier(0.4, 0.0, 0.2, 1)"
      };
      el._x_transition = {
        in(before = () => {
        }, after = () => {
        }) {
          if (fullyHide)
            el.hidden = false;
          if (fullyHide)
            el.style.display = null;
          let current = el.getBoundingClientRect().height;
          el.style.height = "auto";
          let full = el.getBoundingClientRect().height;
          if (current === full) {
            current = floor;
          }
          Alpine.transition(el, Alpine.setStyles, {
            during: transitionStyles,
            start: { height: current + "px" },
            end: { height: full + "px" }
          }, () => el._x_isShown = true, () => {
            if (el.getBoundingClientRect().height == full) {
              el.style.overflow = null;
            }
          });
        },
        out(before = () => {
        }, after = () => {
        }) {
          let full = el.getBoundingClientRect().height;
          Alpine.transition(el, setFunction, {
            during: transitionStyles,
            start: { height: full + "px" },
            end: { height: floor + "px" }
          }, () => el.style.overflow = "hidden", () => {
            el._x_isShown = false;
            if (el.style.height == `${floor}px` && fullyHide) {
              el.style.display = "none";
              el.hidden = true;
            }
          });
        }
      };
    }
  }
  function modifierValue(modifiers, key, fallback) {
    if (modifiers.indexOf(key) === -1)
      return fallback;
    const rawValue = modifiers[modifiers.indexOf(key) + 1];
    if (!rawValue)
      return fallback;
    if (key === "duration") {
      let match = rawValue.match(/([0-9]+)ms/);
      if (match)
        return match[1];
    }
    if (key === "min") {
      let match = rawValue.match(/([0-9]+)px/);
      if (match)
        return match[1];
    }
    return rawValue;
  }
  var module_default = src_default;

  // packages/tables/resources/js/index.js
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(module_default);
  });
})();
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0BhbHBpbmVqcy9jb2xsYXBzZS9kaXN0L21vZHVsZS5lc20uanMiLCAiLi4vcmVzb3VyY2VzL2pzL2luZGV4LmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyIvLyBwYWNrYWdlcy9jb2xsYXBzZS9zcmMvaW5kZXguanNcbmZ1bmN0aW9uIHNyY19kZWZhdWx0KEFscGluZSkge1xuICBBbHBpbmUuZGlyZWN0aXZlKFwiY29sbGFwc2VcIiwgY29sbGFwc2UpO1xuICBjb2xsYXBzZS5pbmxpbmUgPSAoZWwsIHttb2RpZmllcnN9KSA9PiB7XG4gICAgaWYgKCFtb2RpZmllcnMuaW5jbHVkZXMoXCJtaW5cIikpXG4gICAgICByZXR1cm47XG4gICAgZWwuX3hfZG9TaG93ID0gKCkgPT4ge1xuICAgIH07XG4gICAgZWwuX3hfZG9IaWRlID0gKCkgPT4ge1xuICAgIH07XG4gIH07XG4gIGZ1bmN0aW9uIGNvbGxhcHNlKGVsLCB7bW9kaWZpZXJzfSkge1xuICAgIGxldCBkdXJhdGlvbiA9IG1vZGlmaWVyVmFsdWUobW9kaWZpZXJzLCBcImR1cmF0aW9uXCIsIDI1MCkgLyAxZTM7XG4gICAgbGV0IGZsb29yID0gbW9kaWZpZXJWYWx1ZShtb2RpZmllcnMsIFwibWluXCIsIDApO1xuICAgIGxldCBmdWxseUhpZGUgPSAhbW9kaWZpZXJzLmluY2x1ZGVzKFwibWluXCIpO1xuICAgIGlmICghZWwuX3hfaXNTaG93bilcbiAgICAgIGVsLnN0eWxlLmhlaWdodCA9IGAke2Zsb29yfXB4YDtcbiAgICBpZiAoIWVsLl94X2lzU2hvd24gJiYgZnVsbHlIaWRlKVxuICAgICAgZWwuaGlkZGVuID0gdHJ1ZTtcbiAgICBpZiAoIWVsLl94X2lzU2hvd24pXG4gICAgICBlbC5zdHlsZS5vdmVyZmxvdyA9IFwiaGlkZGVuXCI7XG4gICAgbGV0IHNldEZ1bmN0aW9uID0gKGVsMiwgc3R5bGVzKSA9PiB7XG4gICAgICBsZXQgcmV2ZXJ0RnVuY3Rpb24gPSBBbHBpbmUuc2V0U3R5bGVzKGVsMiwgc3R5bGVzKTtcbiAgICAgIHJldHVybiBzdHlsZXMuaGVpZ2h0ID8gKCkgPT4ge1xuICAgICAgfSA6IHJldmVydEZ1bmN0aW9uO1xuICAgIH07XG4gICAgbGV0IHRyYW5zaXRpb25TdHlsZXMgPSB7XG4gICAgICB0cmFuc2l0aW9uUHJvcGVydHk6IFwiaGVpZ2h0XCIsXG4gICAgICB0cmFuc2l0aW9uRHVyYXRpb246IGAke2R1cmF0aW9ufXNgLFxuICAgICAgdHJhbnNpdGlvblRpbWluZ0Z1bmN0aW9uOiBcImN1YmljLWJlemllcigwLjQsIDAuMCwgMC4yLCAxKVwiXG4gICAgfTtcbiAgICBlbC5feF90cmFuc2l0aW9uID0ge1xuICAgICAgaW4oYmVmb3JlID0gKCkgPT4ge1xuICAgICAgfSwgYWZ0ZXIgPSAoKSA9PiB7XG4gICAgICB9KSB7XG4gICAgICAgIGlmIChmdWxseUhpZGUpXG4gICAgICAgICAgZWwuaGlkZGVuID0gZmFsc2U7XG4gICAgICAgIGlmIChmdWxseUhpZGUpXG4gICAgICAgICAgZWwuc3R5bGUuZGlzcGxheSA9IG51bGw7XG4gICAgICAgIGxldCBjdXJyZW50ID0gZWwuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkuaGVpZ2h0O1xuICAgICAgICBlbC5zdHlsZS5oZWlnaHQgPSBcImF1dG9cIjtcbiAgICAgICAgbGV0IGZ1bGwgPSBlbC5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKS5oZWlnaHQ7XG4gICAgICAgIGlmIChjdXJyZW50ID09PSBmdWxsKSB7XG4gICAgICAgICAgY3VycmVudCA9IGZsb29yO1xuICAgICAgICB9XG4gICAgICAgIEFscGluZS50cmFuc2l0aW9uKGVsLCBBbHBpbmUuc2V0U3R5bGVzLCB7XG4gICAgICAgICAgZHVyaW5nOiB0cmFuc2l0aW9uU3R5bGVzLFxuICAgICAgICAgIHN0YXJ0OiB7aGVpZ2h0OiBjdXJyZW50ICsgXCJweFwifSxcbiAgICAgICAgICBlbmQ6IHtoZWlnaHQ6IGZ1bGwgKyBcInB4XCJ9XG4gICAgICAgIH0sICgpID0+IGVsLl94X2lzU2hvd24gPSB0cnVlLCAoKSA9PiB7XG4gICAgICAgICAgaWYgKGVsLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpLmhlaWdodCA9PSBmdWxsKSB7XG4gICAgICAgICAgICBlbC5zdHlsZS5vdmVyZmxvdyA9IG51bGw7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH0sXG4gICAgICBvdXQoYmVmb3JlID0gKCkgPT4ge1xuICAgICAgfSwgYWZ0ZXIgPSAoKSA9PiB7XG4gICAgICB9KSB7XG4gICAgICAgIGxldCBmdWxsID0gZWwuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkuaGVpZ2h0O1xuICAgICAgICBBbHBpbmUudHJhbnNpdGlvbihlbCwgc2V0RnVuY3Rpb24sIHtcbiAgICAgICAgICBkdXJpbmc6IHRyYW5zaXRpb25TdHlsZXMsXG4gICAgICAgICAgc3RhcnQ6IHtoZWlnaHQ6IGZ1bGwgKyBcInB4XCJ9LFxuICAgICAgICAgIGVuZDoge2hlaWdodDogZmxvb3IgKyBcInB4XCJ9XG4gICAgICAgIH0sICgpID0+IGVsLnN0eWxlLm92ZXJmbG93ID0gXCJoaWRkZW5cIiwgKCkgPT4ge1xuICAgICAgICAgIGVsLl94X2lzU2hvd24gPSBmYWxzZTtcbiAgICAgICAgICBpZiAoZWwuc3R5bGUuaGVpZ2h0ID09IGAke2Zsb29yfXB4YCAmJiBmdWxseUhpZGUpIHtcbiAgICAgICAgICAgIGVsLnN0eWxlLmRpc3BsYXkgPSBcIm5vbmVcIjtcbiAgICAgICAgICAgIGVsLmhpZGRlbiA9IHRydWU7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9O1xuICB9XG59XG5mdW5jdGlvbiBtb2RpZmllclZhbHVlKG1vZGlmaWVycywga2V5LCBmYWxsYmFjaykge1xuICBpZiAobW9kaWZpZXJzLmluZGV4T2Yoa2V5KSA9PT0gLTEpXG4gICAgcmV0dXJuIGZhbGxiYWNrO1xuICBjb25zdCByYXdWYWx1ZSA9IG1vZGlmaWVyc1ttb2RpZmllcnMuaW5kZXhPZihrZXkpICsgMV07XG4gIGlmICghcmF3VmFsdWUpXG4gICAgcmV0dXJuIGZhbGxiYWNrO1xuICBpZiAoa2V5ID09PSBcImR1cmF0aW9uXCIpIHtcbiAgICBsZXQgbWF0Y2ggPSByYXdWYWx1ZS5tYXRjaCgvKFswLTldKyltcy8pO1xuICAgIGlmIChtYXRjaClcbiAgICAgIHJldHVybiBtYXRjaFsxXTtcbiAgfVxuICBpZiAoa2V5ID09PSBcIm1pblwiKSB7XG4gICAgbGV0IG1hdGNoID0gcmF3VmFsdWUubWF0Y2goLyhbMC05XSspcHgvKTtcbiAgICBpZiAobWF0Y2gpXG4gICAgICByZXR1cm4gbWF0Y2hbMV07XG4gIH1cbiAgcmV0dXJuIHJhd1ZhbHVlO1xufVxuXG4vLyBwYWNrYWdlcy9jb2xsYXBzZS9idWlsZHMvbW9kdWxlLmpzXG52YXIgbW9kdWxlX2RlZmF1bHQgPSBzcmNfZGVmYXVsdDtcbmV4cG9ydCB7XG4gIG1vZHVsZV9kZWZhdWx0IGFzIGRlZmF1bHRcbn07XG4iLCAiaW1wb3J0IENvbGxhcHNlIGZyb20gJ0BhbHBpbmVqcy9jb2xsYXBzZSdcblxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignYWxwaW5lOmluaXQnLCAoKSA9PiB7XG4gICAgd2luZG93LkFscGluZS5wbHVnaW4oQ29sbGFwc2UpXG59KVxuIl0sCiAgIm1hcHBpbmdzIjogIjs7QUFDQSxXQUFTLFlBQVksUUFBUTtBQUMzQixXQUFPLFVBQVUsWUFBWSxRQUFRO0FBQ3JDLGFBQVMsU0FBUyxDQUFDLElBQUksRUFBQyxVQUFTLE1BQU07QUFDckMsVUFBSSxDQUFDLFVBQVUsU0FBUyxLQUFLO0FBQzNCO0FBQ0YsU0FBRyxZQUFZLE1BQU07QUFBQSxNQUNyQjtBQUNBLFNBQUcsWUFBWSxNQUFNO0FBQUEsTUFDckI7QUFBQSxJQUNGO0FBQ0EsYUFBUyxTQUFTLElBQUksRUFBQyxVQUFTLEdBQUc7QUFDakMsVUFBSSxXQUFXLGNBQWMsV0FBVyxZQUFZLEdBQUcsSUFBSTtBQUMzRCxVQUFJLFFBQVEsY0FB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
