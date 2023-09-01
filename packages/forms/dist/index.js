(() => {
  // node_modules/@alpinejs/mask/dist/module.esm.js
  function src_default(Alpine) {
    Alpine.directive("mask", (el, { value, expression }, { effect, evaluateLater }) => {
      let templateFn = () => expression;
      let lastInputValue = "";
      queueMicrotask(() => {
        if (["function", "dynamic"].includes(value)) {
          let evaluator = evaluateLater(expression);
          effect(() => {
            templateFn = (input) => {
              let result;
              Alpine.dontAutoEvaluateFunctions(() => {
                evaluator((value2) => {
                  result = typeof value2 === "function" ? value2(input) : value2;
                }, { scope: {
                  $input: input,
                  $money: formatMoney.bind({ el })
                } });
              });
              return result;
            };
            processInputValue(el, false);
          });
        } else {
          processInputValue(el, false);
        }
        if (el._x_model)
          el._x_model.set(el.value);
      });
      el.addEventListener("input", () => processInputValue(el));
      el.addEventListener("blur", () => processInputValue(el, false));
      function processInputValue(el2, shouldRestoreCursor = true) {
        let input = el2.value;
        let template = templateFn(input);
        if (!template || template === "false")
          return false;
        if (lastInputValue.length - el2.value.length === 1) {
          return lastInputValue = el2.value;
        }
        let setInput = () => {
          lastInputValue = el2.value = formatInput(input, template);
        };
        if (shouldRestoreCursor) {
          restoreCursorPosition(el2, template, () => {
            setInput();
          });
        } else {
          setInput();
        }
      }
      function formatInput(input, template) {
        if (input === "")
          return "";
        let strippedDownInput = stripDown(template, input);
        let rebuiltInput = buildUp(template, strippedDownInput);
        return rebuiltInput;
      }
    }).before("model");
  }
  function restoreCursorPosition(el, template, callback) {
    let cursorPosition = el.selectionStart;
    let unformattedValue = el.value;
    callback();
    let beforeLeftOfCursorBeforeFormatting = unformattedValue.slice(0, cursorPosition);
    let newPosition = buildUp(template, stripDown(template, beforeLeftOfCursorBeforeFormatting)).length;
    el.setSelectionRange(newPosition, newPosition);
  }
  function stripDown(template, input) {
    let inputToBeStripped = input;
    let output = "";
    let regexes = {
      "9": /[0-9]/,
      a: /[a-zA-Z]/,
      "*": /[a-zA-Z0-9]/
    };
    let wildcardTemplate = "";
    for (let i = 0; i < template.length; i++) {
      if (["9", "a", "*"].includes(template[i])) {
        wildcardTemplate += template[i];
        continue;
      }
      for (let j = 0; j < inputToBeStripped.length; j++) {
        if (inputToBeStripped[j] === template[i]) {
          inputToBeStripped = inputToBeStripped.slice(0, j) + inputToBeStripped.slice(j + 1);
          break;
        }
      }
    }
    for (let i = 0; i < wildcardTemplate.length; i++) {
      let found = false;
      for (let j = 0; j < inputToBeStripped.length; j++) {
        if (regexes[wildcardTemplate[i]].test(inputToBeStripped[j])) {
          output += inputToBeStripped[j];
          inputToBeStripped = inputToBeStripped.slice(0, j) + inputToBeStripped.slice(j + 1);
          found = true;
          break;
        }
      }
      if (!found)
        break;
    }
    return output;
  }
  function buildUp(template, input) {
    let clean = Array.from(input);
    let output = "";
    for (let i = 0; i < template.length; i++) {
      if (!["9", "a", "*"].includes(template[i])) {
        output += template[i];
        continue;
      }
      if (clean.length === 0)
        break;
      output += clean.shift();
    }
    return output;
  }
  function formatMoney(input, delimiter = ".", thousands, precision = 2) {
    if (input === "-")
      return "-";
    if (/^\D+$/.test(input))
      return "9";
    thousands = thousands ?? (delimiter === "," ? "." : ",");
    let addThousands = (input2, thousands2) => {
      let output = "";
      let counter = 0;
      for (let i = input2.length - 1; i >= 0; i--) {
        if (input2[i] === thousands2)
          continue;
        if (counter === 3) {
          output = input2[i] + thousands2 + output;
          counter = 0;
        } else {
          output = input2[i] + output;
        }
        counter++;
      }
      return output;
    };
    let minus = input.startsWith("-") ? "-" : "";
    let strippedInput = input.replaceAll(new RegExp(`[^0-9\\${delimiter}]`, "g"), "");
    let template = Array.from({ length: strippedInput.split(delimiter)[0].length }).fill("9").join("");
    template = `${minus}${addThousands(template, thousands)}`;
    if (precision > 0 && input.includes(delimiter))
      template += `${delimiter}` + "9".repeat(precision);
    queueMicrotask(() => {
      if (this.el.value.endsWith(delimiter))
        return;
      if (this.el.value[this.el.selectionStart - 1] === delimiter) {
        this.el.setSelectionRange(this.el.selectionStart - 1, this.el.selectionStart - 1);
      }
    });
    return template;
  }
  var module_default = src_default;

  // packages/forms/resources/js/index.js
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(module_default);
  });
})();
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0BhbHBpbmVqcy9tYXNrL2Rpc3QvbW9kdWxlLmVzbS5qcyIsICIuLi9yZXNvdXJjZXMvanMvaW5kZXguanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbIi8vIHBhY2thZ2VzL21hc2svc3JjL2luZGV4LmpzXG5mdW5jdGlvbiBzcmNfZGVmYXVsdChBbHBpbmUpIHtcbiAgQWxwaW5lLmRpcmVjdGl2ZShcIm1hc2tcIiwgKGVsLCB7dmFsdWUsIGV4cHJlc3Npb259LCB7ZWZmZWN0LCBldmFsdWF0ZUxhdGVyfSkgPT4ge1xuICAgIGxldCB0ZW1wbGF0ZUZuID0gKCkgPT4gZXhwcmVzc2lvbjtcbiAgICBsZXQgbGFzdElucHV0VmFsdWUgPSBcIlwiO1xuICAgIHF1ZXVlTWljcm90YXNrKCgpID0+IHtcbiAgICAgIGlmIChbXCJmdW5jdGlvblwiLCBcImR5bmFtaWNcIl0uaW5jbHVkZXModmFsdWUpKSB7XG4gICAgICAgIGxldCBldmFsdWF0b3IgPSBldmFsdWF0ZUxhdGVyKGV4cHJlc3Npb24pO1xuICAgICAgICBlZmZlY3QoKCkgPT4ge1xuICAgICAgICAgIHRlbXBsYXRlRm4gPSAoaW5wdXQpID0+IHtcbiAgICAgICAgICAgIGxldCByZXN1bHQ7XG4gICAgICAgICAgICBBbHBpbmUuZG9udEF1dG9FdmFsdWF0ZUZ1bmN0aW9ucygoKSA9PiB7XG4gICAgICAgICAgICAgIGV2YWx1YXRvcigodmFsdWUyKSA9PiB7XG4gICAgICAgICAgICAgICAgcmVzdWx0ID0gdHlwZW9mIHZhbHVlMiA9PT0gXCJmdW5jdGlvblwiID8gdmFsdWUyKGlucHV0KSA6IHZhbHVlMjtcbiAgICAgICAgICAgICAgfSwge3Njb3BlOiB7XG4gICAgICAgICAgICAgICAgJGlucHV0OiBpbnB1dCxcbiAgICAgICAgICAgICAgICAkbW9uZXk6IGZvcm1hdE1vbmV5LmJpbmQoe2VsfSlcbiAgICAgICAgICAgICAgfX0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgICAgICAgIH07XG4gICAgICAgICAgcHJvY2Vzc0lucHV0VmFsdWUoZWwsIGZhbHNlKTtcbiAgICAgICAgfSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBwcm9jZXNzSW5wdXRWYWx1ZShlbCwgZmFsc2UpO1xuICAgICAgfVxuICAgICAgaWYgKGVsLl94X21vZGVsKVxuICAgICAgICBlbC5feF9tb2RlbC5zZXQoZWwudmFsdWUpO1xuICAgIH0pO1xuICAgIGVsLmFkZEV2ZW50TGlzdGVuZXIoXCJpbnB1dFwiLCAoKSA9PiBwcm9jZXNzSW5wdXRWYWx1ZShlbCkpO1xuICAgIGVsLmFkZEV2ZW50TGlzdGVuZXIoXCJibHVyXCIsICgpID0+IHByb2Nlc3NJbnB1dFZhbHVlKGVsLCBmYWxzZSkpO1xuICAgIGZ1bmN0aW9uIHByb2Nlc3NJbnB1dFZhbHVlKGVsMiwgc2hvdWxkUmVzdG9yZUN1cnNvciA9IHRydWUpIHtcbiAgICAgIGxldCBpbnB1dCA9IGVsMi52YWx1ZTtcbiAgICAgIGxldCB0ZW1wbGF0ZSA9IHRlbXBsYXRlRm4oaW5wdXQpO1xuICAgICAgaWYgKCF0ZW1wbGF0ZSB8fCB0ZW1wbGF0ZSA9PT0gXCJmYWxzZVwiKVxuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICBpZiAobGFzdElucHV0VmFsdWUubGVuZ3RoIC0gZWwyLnZhbHVlLmxlbmd0aCA9PT0gMSkge1xuICAgICAgICByZXR1cm4gbGFzdElucHV0VmFsdWUgPSBlbDIudmFsdWU7XG4gICAgICB9XG4gICAgICBsZXQgc2V0SW5wdXQgPSAoKSA9PiB7XG4gICAgICAgIGxhc3RJbnB1dFZhbHVlID0gZWwyLnZhbHVlID0gZm9ybWF0SW5wdXQoaW5wdXQsIHRlbXBsYXRlKTtcbiAgICAgIH07XG4gICAgICBpZiAoc2hvdWxkUmVzdG9yZUN1cnNvcikge1xuICAgICAgICByZXN0b3JlQ3Vyc29yUG9zaXRpb24oZWwyLCB0ZW1wbGF0ZSwgKCkgPT4ge1xuICAgICAgICAgIHNldElucHV0KCk7XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgc2V0SW5wdXQoKTtcbiAgICAgIH1cbiAgICB9XG4gICAgZnVuY3Rpb24gZm9ybWF0SW5wdXQoaW5wdXQsIHRlbXBsYXRlKSB7XG4gICAgICBpZiAoaW5wdXQgPT09IFwiXCIpXG4gICAgICAgIHJldHVybiBcIlwiO1xuICAgICAgbGV0IHN0cmlwcGVkRG93bklucHV0ID0gc3RyaXBEb3duKHRlbXBsYXRlLCBpbnB1dCk7XG4gICAgICBsZXQgcmVidWlsdElucHV0ID0gYnVpbGRVcCh0ZW1wbGF0ZSwgc3RyaXBwZWREb3duSW5wdXQpO1xuICAgICAgcmV0dXJuIHJlYnVpbHRJbnB1dDtcbiAgICB9XG4gIH0pLmJlZm9yZShcIm1vZGVsXCIpO1xufVxuZnVuY3Rpb24gcmVzdG9yZUN1cnNvclBvc2l0aW9uKGVsLCB0ZW1wbGF0ZSwgY2FsbGJhY2spIHtcbiAgbGV0IGN1cnNvclBvc2l0aW9uID0gZWwuc2VsZWN0aW9uU3RhcnQ7XG4gIGxldCB1bmZvcm1hdHRlZFZhbHVlID0gZWwudmFsdWU7XG4gIGNhbGxiYWNrKCk7XG4gIGxldCBiZWZvcmVMZWZ0T2ZDdXJzb3JCZWZvcmVGb3JtYXR0aW5nID0gdW5mb3JtYXR0ZWRWYWx1ZS5zbGljZSgwLCBjdXJzb3JQb3NpdGlvbik7XG4gIGxldCBuZXdQb3NpdGlvbiA9IGJ1aWxkVXAodGVtcGxhdGUsIHN0cmlwRG93bih0ZW1wbGF0ZSwgYmVmb3JlTGVmdE9mQ3Vyc29yQmVmb3JlRm9ybWF0dGluZykpLmxlbmd0aDtcbiAgZWwuc2V0U2VsZWN0aW9uUmFuZ2UobmV3UG9zaXRpb24sIG5ld1Bvc2l0aW9uKTtcbn1cbmZ1bmN0aW9uIHN0cmlwRG93bih0ZW1wbGF0ZSwgaW5wdXQpIHtcbiAgbGV0IGlucHV0VG9CZVN0cmlwcGVkID0gaW5wdXQ7XG4gIGxldCBvdXRwdXQgPSBcIlwiO1xuICBsZXQgcmVnZXhlcyA9IHtcbiAgICBcIjlcIjogL1swLTldLyxcbiAgICBhOiAvW2EtekEtWl0vLFxuICAgIFwiKlwiOiAvW2EtekEtWjAtOV0vXG4gIH07XG4gIGxldCB3aWxkY2FyZFRlbXBsYXRlID0gXCJcIjtcbiAgZm9yIChsZXQgaSA9IDA7IGkgPCB0ZW1wbGF0ZS5sZW5ndGg7IGkrKykge1xuICAgIGlmIChbXCI5XCIsIFwiYVwiLCBcIipcIl0uaW5jbHVkZXModGVtcGxhdGVbaV0pKSB7XG4gICAgICB3aWxkY2FyZFRlbXBsYXRlICs9IHRlbXBsYXRlW2ldO1xuICAgICAgY29udGludWU7XG4gICAgfVxuICAgIGZvciAobGV0IGogPSAwOyBqIDwgaW5wdXRUb0JlU3RyaXBwZWQubGVuZ3RoOyBqKyspIHtcbiAgICAgIGlmIChpbnB1dFRvQmVTdHJpcHBlZFtqXSA9PT0gdGVtcGxhdGVbaV0pIHtcbiAgICAgICAgaW5wdXRUb0JlU3RyaXBwZWQgPSBpbnB1dFRvQmVTdHJpcHBlZC5zbGljZSgwLCBqKSArIGlucHV0VG9CZVN0cmlwcGVkLnNsaWNlKGogKyAxKTtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuICB9XG4gIGZvciAobGV0IGkgPSAwOyBpIDwgd2lsZGNhcmRUZW1wbGF0ZS5sZW5ndGg7IGkrKykge1xuICAgIGxldCBmb3VuZCA9IGZhbHNlO1xuICAgIGZvciAobGV0IGogPSAwOyBqIDwgaW5wdXRUb0JlU3RyaXBwZWQubGVuZ3RoOyBqKyspIHtcbiAgICAgIGlmIChyZWdleGVzW3dpbGRjYXJkVGVtcGxhdGVbaV1dLnRlc3QoaW5wdXRUb0JlU3RyaXBwZWRbal0pKSB7XG4gICAgICAgIG91dHB1dCArPSBpbnB1dFRvQmVTdHJpcHBlZFtqXTtcbiAgICAgICAgaW5wdXRUb0JlU3RyaXBwZWQgPSBpbnB1dFRvQmVTdHJpcHBlZC5zbGljZSgwLCBqKSArIGlucHV0VG9CZVN0cmlwcGVkLnNsaWNlKGogKyAxKTtcbiAgICAgICAgZm91bmQgPSB0cnVlO1xuICAgICAgICBicmVhaztcbiAgICAgIH1cbiAgICB9XG4gICAgaWYgKCFmb3VuZClcbiAgICAgIGJyZWFrO1xuICB9XG4gIHJldHVybiBvdXRwdXQ7XG59XG5mdW5jdGlvbiBidWlsZFVwKHRlbXBsYXRlLCBpbnB1dCkge1xuICBsZXQgY2xlYW4gPSBBcnJheS5mcm9tKGlucHV0KTtcbiAgbGV0IG91dHB1dCA9IFwiXCI7XG4gIGZvciAobGV0IGkgPSAwOyBpIDwgdGVtcGxhdGUubGVuZ3RoOyBpKyspIHtcbiAgICBpZiAoIVtcIjlcIiwgXCJhXCIsIFwiKlwiXS5pbmNsdWRlcyh0ZW1wbGF0ZVtpXSkpIHtcbiAgICAgIG91dHB1dCArPSB0ZW1wbGF0ZVtpXTtcbiAgICAgIGNvbnRpbnVlO1xuICAgIH1cbiAgICBpZiAoY2xlYW4ubGVuZ3RoID09PSAwKVxuICAgICAgYnJlYWs7XG4gICAgb3V0cHV0ICs9IGNsZWFuLnNoaWZ0KCk7XG4gIH1cbiAgcmV0dXJuIG91dHB1dDtcbn1cbmZ1bmN0aW9uIGZvcm1hdE1vbmV5KGlucHV0LCBkZWxpbWl0ZXIgPSBcIi5cIiwgdGhvdXNhbmRzLCBwcmVjaXNpb24gPSAyKSB7XG4gIGlmIChpbnB1dCA9PT0gXCItXCIpXG4gICAgcmV0dXJuIFwiLVwiO1xuICBpZiAoL15cXEQrJC8udGVzdChpbnB1dCkpXG4gICAgcmV0dXJuIFwiOVwiO1xuICB0aG91c2FuZHMgPSB0aG91c2FuZHMgPz8gKGRlbGltaXRlciA9PT0gXCIsXCIgPyBcIi5cIiA6IFwiLFwiKTtcbiAgbGV0IGFkZFRob3VzYW5kcyA9IChpbnB1dDIsIHRob3VzYW5kczIpID0+IHtcbiAgICBsZXQgb3V0cHV0ID0gXCJcIjtcbiAgICBsZXQgY291bnRlciA9IDA7XG4gICAgZm9yIChsZXQgaSA9IGlucHV0Mi5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgaWYgKGlucHV0MltpXSA9PT0gdGhvdXNhbmRzMilcbiAgICAgICAgY29udGludWU7XG4gICAgICBpZiAoY291bnRlciA9PT0gMykge1xuICAgICAgICBvdXRwdXQgPSBpbnB1dDJbaV0gKyB0aG91c2FuZHMyICsgb3V0cHV0O1xuICAgICAgICBjb3VudGVyID0gMDtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIG91dHB1dCA9IGlucHV0MltpXSArIG91dHB1dDtcbiAgICAgIH1cbiAgICAgIGNvdW50ZXIrKztcbiAgICB9XG4gICAgcmV0dXJuIG91dHB1dDtcbiAgfTtcbiAgbGV0IG1pbnVzID0gaW5wdXQuc3RhcnRzV2l0aChcIi1cIikgPyBcIi1cIiA6IFwiXCI7XG4gIGxldCBzdHJpcHBlZElucHV0ID0gaW5wdXQucmVwbGFjZUFsbChuZXcgUmVnRXhwKGBbXjAtOVxcXFwke2RlbGltaXRlcn1dYCwgXCJnXCIpLCBcIlwiKTtcbiAgbGV0IHRlbXBsYXRlID0gQXJyYXkuZnJvbSh7bGVuZ3RoOiBzdHJpcHBlZElucHV0LnNwbGl0KGRlbGltaXRlcilbMF0ubGVuZ3RofSkuZmlsbChcIjlcIikuam9pbihcIlwiKTtcbiAgdGVtcGxhdGUgPSBgJHttaW51c30ke2FkZFRob3VzYW5kcyh0ZW1wbGF0ZSwgdGhvdXNhbmRzKX1gO1xuICBpZiAocHJlY2lzaW9uID4gMCAmJiBpbnB1dC5pbmNsdWRlcyhkZWxpbWl0ZXIpKVxuICAgIHRlbXBsYXRlICs9IGAke2RlbGltaXRlcn1gICsgXCI5XCIucmVwZWF0KHByZWNpc2lvbik7XG4gIHF1ZXVlTWljcm90YXNrKCgpID0+IHtcbiAgICBpZiAodGhpcy5lbC52YWx1ZS5lbmRzV2l0aChkZWxpbWl0ZXIpKVxuICAgICAgcmV0dXJuO1xuICAgIGlmICh0aGlzLmVsLnZhbHVlW3RoaXMuZWwuc2VsZWN0aW9uU3RhcnQgLSAxXSA9PT0gZGVsaW1pdGVyKSB7XG4gICAgICB0aGlzLmVsLnNldFNlbGVjdGlvblJhbmdlKHRoaXMuZWwuc2VsZWN0aW9uU3RhcnQgLSAxLCB0aGlzLmVsLnNlbGVjdGlvblN0YXJ0IC0gMSk7XG4gICAgfVxuICB9KTtcbiAgcmV0dXJuIHRlbXBsYXRlO1xufVxuXG4vLyBwYWNrYWdlcy9tYXNrL2J1aWxkcy9tb2R1bGUuanNcbnZhciBtb2R1bGVfZGVmYXVsdCA9IHNyY19kZWZhdWx0O1xuZXhwb3J0IHtcbiAgbW9kdWxlX2RlZmF1bHQgYXMgZGVmYXVsdCxcbiAgc3RyaXBEb3duXG59O1xuIiwgImltcG9ydCBtYXNrIGZyb20gJ0BhbHBpbmVqcy9tYXNrJ1xuXG5pbXBvcnQgJy4uL2Nzcy9jb21wb25lbnRzL2RhdGUtdGltZS1waWNrZXIuY3NzJ1xuaW1wb3J0ICcuLi9jc3MvY29tcG9uZW50cy9maWxlLXVwbG9hZC5jc3MnXG5pbXBvcnQgJy4uL2Nzcy9jb21wb25lbnRzL21hcmtkb3duLWVkaXRvci5jc3MnXG5pbXBvcnQgJy4uL2Nzcy9jb21wb25lbnRzL3JpY2gtZWRpdG9yLmNzcydcbmltcG9ydCAnLi4vY3NzL2NvbXBvbmVudHMvc2VsZWN0LmNzcydcbmltcG9ydCAnLi4vY3NzL2NvbXBvbmVudHMvdGFncy1pbnB1dC5jc3MnXG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2FscGluZTppbml0JywgKCkgPT4ge1xuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKG1hc2spXG59KVxuIl0sCiAgIm1hcHBpbmdzIjogIjs7QUFDQSxXQUFTLFlBQVksUUFBUTtBQUMzQixXQUFPLFVBQVUsUUFBUSxDQUFDLElBQUksRUFBQyxPQUFPLFdBQVUsR0FBRyxFQUFDLFFBQVEsY0FBYSxNQUFNO0FBQzdFLFVBQUksYUFBYSxNQUFNO0FBQ3ZCLFVBQUksaUJBQWlCO0FBQ3JCLHFCQUFlLE1BQU07QUFDbkIsWUFBSSxDQUFDLFlBQVksU0FBUyxFQUFFLFNBQVMsS0FBSyxHQUFHO0FBQzNDLGNBQUksWUFBWSxjQUFjLFVBQVU7QUFDeEMsaUJBQU8sTUFBTTtBQUNYLHlCQUFhLENBQUMsVUFBVTtBQUN0QixrQkFBSTtBQUNKLHFCQUFPLDBCQUEwQixNQUFNO0FBQ3JDLDBCQUFVLENBQUMsV0FBVztBQUNwQiwyQkFBUyxPQUFPLFdBQVcsYUFBYSxPQUFPLEtBQUssSUFBSTtBQUFBLGdCQUMxRCxHQUFHLEVBQUMsT0FBTztBQUFBLGtCQUNULFFBQVE7QUFBQSxrQkFDUixR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
