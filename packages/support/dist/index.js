(() => {
  // node_modules/@awcodes/alpine-floating-ui/dist/module.esm.js
  function getSide(placement) {
    return placement.split("-")[0];
  }
  function getAlignment(placement) {
    return placement.split("-")[1];
  }
  function getMainAxisFromPlacement(placement) {
    return ["top", "bottom"].includes(getSide(placement)) ? "x" : "y";
  }
  function getLengthFromAxis(axis) {
    return axis === "y" ? "height" : "width";
  }
  function computeCoordsFromPlacement(_ref, placement, rtl) {
    let {
      reference,
      floating
    } = _ref;
    const commonX = reference.x + reference.width / 2 - floating.width / 2;
    const commonY = reference.y + reference.height / 2 - floating.height / 2;
    const mainAxis = getMainAxisFromPlacement(placement);
    const length = getLengthFromAxis(mainAxis);
    const commonAlign = reference[length] / 2 - floating[length] / 2;
    const side = getSide(placement);
    const isVertical = mainAxis === "x";
    let coords;
    switch (side) {
      case "top":
        coords = {
          x: commonX,
          y: reference.y - floating.height
        };
        break;
      case "bottom":
        coords = {
          x: commonX,
          y: reference.y + reference.height
        };
        break;
      case "right":
        coords = {
          x: reference.x + reference.width,
          y: commonY
        };
        break;
      case "left":
        coords = {
          x: reference.x - floating.width,
          y: commonY
        };
        break;
      default:
        coords = {
          x: reference.x,
          y: reference.y
        };
    }
    switch (getAlignment(placement)) {
      case "start":
        coords[mainAxis] -= commonAlign * (rtl && isVertical ? -1 : 1);
        break;
      case "end":
        coords[mainAxis] += commonAlign * (rtl && isVertical ? -1 : 1);
        break;
    }
    return coords;
  }
  var computePosition = async (reference, floating, config) => {
    const {
      placement = "bottom",
      strategy = "absolute",
      middleware = [],
      platform: platform2
    } = config;
    const rtl = await (platform2.isRTL == null ? void 0 : platform2.isRTL(floating));
    if (true) {
      if (platform2 == null) {
        console.error(["Floating UI: `platform` property was not passed to config. If you", "want to use Floating UI on the web, install @floating-ui/dom", "instead of the /core package. Otherwise, you can create your own", "`platform`: https://floating-ui.com/docs/platform"].join(" "));
      }
      if (middleware.filter((_ref) => {
        let {
          name
        } = _ref;
        return name === "autoPlacement" || name === "flip";
      }).length > 1) {
        throw new Error(["Floating UI: duplicate `flip` and/or `autoPlacement`", "middleware detected. This will lead to an infinite loop. Ensure only", "one of either has been passed to the `middleware` array."].join(" "));
      }
    }
    let rects = await platform2.getElementRects({
      reference,
      floating,
      strategy
    });
    let {
      x,
      y
    } = computeCoordsFromPlacement(rects, placement, rtl);
    let statefulPlacement = placement;
    let middlewareData = {};
    let _debug_loop_count_ = 0;
    for (let i = 0; i < middleware.length; i++) {
      if (true) {
        _debug_loop_count_++;
        if (_debug_loop_count_ > 100) {
          throw new Error(["Floating UI: The middleware lifecycle appears to be", "running in an infinite loop. This is usually caused by a `reset`", "continually being returned without a break condition."].join(" "));
        }
      }
      const {
        name,
        fn
      } = middleware[i];
      const {
        x: nextX,
        y: nextY,
        data,
        reset
      } = await fn({
        x,
        y,
        initialPlacement: placement,
        placement: statefulPlacement,
        strategy,
        middlewareData,
        rects,
        platform: platform2,
        elements: {
          reference,
          floating
        }
      });
      x = nextX != null ? nextX : x;
      y = nextY != null ? nextY : y;
      middlewareData = {
        ...middlewareData,
        [name]: {
          ...middlewareData[name],
          ...data
        }
      };
      if (reset) {
        if (typeof reset === "object") {
          if (reset.placement) {
            statefulPlacement = reset.placement;
          }
          if (reset.rects) {
            rects = reset.rects === true ? await platform2.getElementRects({
              reference,
              floating,
              strategy
            }) : reset.rects;
          }
          ({
            x,
            y
          } = computeCoordsFromPlacement(rects, statefulPlacement, rtl));
        }
        i = -1;
        continue;
      }
    }
    return {
      x,
      y,
      placement: statefulPlacement,
      strategy,
      middlewareData
    };
  };
  function expandPaddingObject(padding) {
    return {
      top: 0,
      right: 0,
      bottom: 0,
      left: 0,
      ...padding
    };
  }
  function getSideObjectFromPadding(padding) {
    return typeof padding !== "number" ? expandPaddingObject(padding) : {
      top: padding,
      right: padding,
      bottom: padding,
      left: padding
    };
  }
  function rectToClientRect(rect) {
    return {
      ...rect,
      top: rect.y,
      left: rect.x,
      right: rect.x + rect.width,
      bottom: rect.y + rect.height
    };
  }
  async function detectOverflow(middlewareArguments, options) {
    var _await$platform$isEle;
    if (options === void 0) {
      options = {};
    }
    const {
      x,
      y,
      platform: platform2,
      rects,
      elements,
      strategy
    } = middlewareArguments;
    const {
      boundary = "clippingAncestors",
      rootBoundary = "viewport",
      elementContext = "floating",
      altBoundary = false,
      padding = 0
    } = options;
    const paddingObject = getSideObjectFromPadding(padding);
    const altContext = elementContext === "floating" ? "reference" : "floating";
    const element = elements[altBoundary ? altContext : elementContext];
    const clippingClientRect = rectToClientRect(await platform2.getClippingRect({
      element: ((_await$platform$isEle = await (platform2.isElement == null ? void 0 : platform2.isElement(element))) != null ? _await$platform$isEle : true) ? element : element.contextElement || await (platform2.getDocumentElement == null ? void 0 : platform2.getDocumentElement(elements.floating)),
      boundary,
      rootBoundary,
      strategy
    }));
    const elementClientRect = rectToClientRect(platform2.convertOffsetParentRelativeRectToViewportRelativeRect ? await platform2.convertOffsetParentRelativeRectToViewportRelativeRect({
      rect: elementContext === "floating" ? {
        ...rects.floating,
        x,
        y
      } : rects.reference,
      offsetParent: await (platform2.getOffsetParent == null ? void 0 : platform2.getOffsetParent(elements.floating)),
      strategy
    }) : rects[elementContext]);
    return {
      top: clippingClientRect.top - elementClientRect.top + paddingObject.top,
      bottom: elementClientRect.bottom - clippingClientRect.bottom + paddingObject.bottom,
      left: clippingClientRect.left - elementClientRect.left + paddingObject.left,
      right: elementClientRect.right - clippingClientRect.right + paddingObject.right
    };
  }
  var min = Math.min;
  var max = Math.max;
  function within(min$1, value, max$1) {
    return max(min$1, min(value, max$1));
  }
  var arrow = (options) => ({
    name: "arrow",
    options,
    async fn(middlewareArguments) {
      const {
        element,
        padding = 0
      } = options != null ? options : {};
      const {
        x,
        y,
        placement,
        rects,
        platform: platform2
      } = middlewareArguments;
      if (element == null) {
        if (true) {
          console.warn("Floating UI: No `element` was passed to the `arrow` middleware.");
        }
        return {};
      }
      const paddingObject = getSideObjectFromPadding(padding);
      const coords = {
        x,
        y
      };
      const axis = getMainAxisFromPlacement(placement);
      const length = getLengthFromAxis(axis);
      const arrowDimensions = await platform2.getDimensions(element);
      const minProp = axis === "y" ? "top" : "left";
      const maxProp = axis === "y" ? "bottom" : "right";
      const endDiff = rects.reference[length] + rects.reference[axis] - coords[axis] - rects.floating[length];
      const startDiff = coords[axis] - rects.reference[axis];
      const arrowOffsetParent = await (platform2.getOffsetParent == null ? void 0 : platform2.getOffsetParent(element));
      const clientSize = arrowOffsetParent ? axis === "y" ? arrowOffsetParent.clientHeight || 0 : arrowOffsetParent.clientWidth || 0 : 0;
      const centerToReference = endDiff / 2 - startDiff / 2;
      const min3 = paddingObject[minProp];
      const max3 = clientSize - arrowDimensions[length] - paddingObject[maxProp];
      const center = clientSize / 2 - arrowDimensions[length] / 2 + centerToReference;
      const offset2 = within(min3, center, max3);
      return {
        data: {
          [axis]: offset2,
          centerOffset: center - offset2
        }
      };
    }
  });
  var hash$1 = {
    left: "right",
    right: "left",
    bottom: "top",
    top: "bottom"
  };
  function getOppositePlacement(placement) {
    return placement.replace(/left|right|bottom|top/g, (matched) => hash$1[matched]);
  }
  function getAlignmentSides(placement, rects, rtl) {
    if (rtl === void 0) {
      rtl = false;
    }
    const alignment = getAlignment(placement);
    const mainAxis = getMainAxisFromPlacement(placement);
    const length = getLengthFromAxis(mainAxis);
    let mainAlignmentSide = mainAxis === "x" ? alignment === (rtl ? "end" : "start") ? "right" : "left" : alignment === "start" ? "bottom" : "top";
    if (rects.reference[length] > rects.floating[length]) {
      mainAlignmentSide = getOppositePlacement(mainAlignmentSide);
    }
    return {
      main: mainAlignmentSide,
      cross: getOppositePlacement(mainAlignmentSide)
    };
  }
  var hash = {
    start: "end",
    end: "start"
  };
  function getOppositeAlignmentPlacement(placement) {
    return placement.replace(/start|end/g, (matched) => hash[matched]);
  }
  var sides = ["top", "right", "bottom", "left"];
  var allPlacements = /* @__PURE__ */ sides.reduce((acc, side) => acc.concat(side, side + "-start", side + "-end"), []);
  function getPlacementList(alignment, autoAlignment, allowedPlacements) {
    const allowedPlacementsSortedByAlignment = alignment ? [...allowedPlacements.filter((placement) => getAlignment(placement) === alignment), ...allowedPlacements.filter((placement) => getAlignment(placement) !== alignment)] : allowedPlacements.filter((placement) => getSide(placement) === placement);
    return allowedPlacementsSortedByAlignment.filter((placement) => {
      if (alignment) {
        return getAlignment(placement) === alignment || (autoAlignment ? getOppositeAlignmentPlacement(placement) !== placement : false);
      }
      return true;
    });
  }
  var autoPlacement = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "autoPlacement",
      options,
      async fn(middlewareArguments) {
        var _middlewareData$autoP, _middlewareData$autoP2, _middlewareData$autoP3, _middlewareData$autoP4, _placementsSortedByLe;
        const {
          x,
          y,
          rects,
          middlewareData,
          placement,
          platform: platform2,
          elements
        } = middlewareArguments;
        const {
          alignment = null,
          allowedPlacements = allPlacements,
          autoAlignment = true,
          ...detectOverflowOptions
        } = options;
        const placements = getPlacementList(alignment, autoAlignment, allowedPlacements);
        const overflow = await detectOverflow(middlewareArguments, detectOverflowOptions);
        const currentIndex = (_middlewareData$autoP = (_middlewareData$autoP2 = middlewareData.autoPlacement) == null ? void 0 : _middlewareData$autoP2.index) != null ? _middlewareData$autoP : 0;
        const currentPlacement = placements[currentIndex];
        if (currentPlacement == null) {
          return {};
        }
        const {
          main,
          cross
        } = getAlignmentSides(currentPlacement, rects, await (platform2.isRTL == null ? void 0 : platform2.isRTL(elements.floating)));
        if (placement !== currentPlacement) {
          return {
            x,
            y,
            reset: {
              placement: placements[0]
            }
          };
        }
        const currentOverflows = [overflow[getSide(currentPlacement)], overflow[main], overflow[cross]];
        const allOverflows = [...(_middlewareData$autoP3 = (_middlewareData$autoP4 = middlewareData.autoPlacement) == null ? void 0 : _middlewareData$autoP4.overflows) != null ? _middlewareData$autoP3 : [], {
          placement: currentPlacement,
          overflows: currentOverflows
        }];
        const nextPlacement = placements[currentIndex + 1];
        if (nextPlacement) {
          return {
            data: {
              index: currentIndex + 1,
              overflows: allOverflows
            },
            reset: {
              placement: nextPlacement
            }
          };
        }
        const placementsSortedByLeastOverflow = allOverflows.slice().sort((a, b) => a.overflows[0] - b.overflows[0]);
        const placementThatFitsOnAllSides = (_placementsSortedByLe = placementsSortedByLeastOverflow.find((_ref) => {
          let {
            overflows
          } = _ref;
          return overflows.every((overflow2) => overflow2 <= 0);
        })) == null ? void 0 : _placementsSortedByLe.placement;
        const resetPlacement = placementThatFitsOnAllSides != null ? placementThatFitsOnAllSides : placementsSortedByLeastOverflow[0].placement;
        if (resetPlacement !== placement) {
          return {
            data: {
              index: currentIndex + 1,
              overflows: allOverflows
            },
            reset: {
              placement: resetPlacement
            }
          };
        }
        return {};
      }
    };
  };
  function getExpandedPlacements(placement) {
    const oppositePlacement = getOppositePlacement(placement);
    return [getOppositeAlignmentPlacement(placement), oppositePlacement, getOppositeAlignmentPlacement(oppositePlacement)];
  }
  var flip = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "flip",
      options,
      async fn(middlewareArguments) {
        var _middlewareData$flip;
        const {
          placement,
          middlewareData,
          rects,
          initialPlacement,
          platform: platform2,
          elements
        } = middlewareArguments;
        const {
          mainAxis: checkMainAxis = true,
          crossAxis: checkCrossAxis = true,
          fallbackPlacements: specifiedFallbackPlacements,
          fallbackStrategy = "bestFit",
          flipAlignment = true,
          ...detectOverflowOptions
        } = options;
        const side = getSide(placement);
        const isBasePlacement = side === initialPlacement;
        const fallbackPlacements = specifiedFallbackPlacements || (isBasePlacement || !flipAlignment ? [getOppositePlacement(initialPlacement)] : getExpandedPlacements(initialPlacement));
        const placements = [initialPlacement, ...fallbackPlacements];
        const overflow = await detectOverflow(middlewareArguments, detectOverflowOptions);
        const overflows = [];
        let overflowsData = ((_middlewareData$flip = middlewareData.flip) == null ? void 0 : _middlewareData$flip.overflows) || [];
        if (checkMainAxis) {
          overflows.push(overflow[side]);
        }
        if (checkCrossAxis) {
          const {
            main,
            cross
          } = getAlignmentSides(placement, rects, await (platform2.isRTL == null ? void 0 : platform2.isRTL(elements.floating)));
          overflows.push(overflow[main], overflow[cross]);
        }
        overflowsData = [...overflowsData, {
          placement,
          overflows
        }];
        if (!overflows.every((side2) => side2 <= 0)) {
          var _middlewareData$flip$, _middlewareData$flip2;
          const nextIndex = ((_middlewareData$flip$ = (_middlewareData$flip2 = middlewareData.flip) == null ? void 0 : _middlewareData$flip2.index) != null ? _middlewareData$flip$ : 0) + 1;
          const nextPlacement = placements[nextIndex];
          if (nextPlacement) {
            return {
              data: {
                index: nextIndex,
                overflows: overflowsData
              },
              reset: {
                placement: nextPlacement
              }
            };
          }
          let resetPlacement = "bottom";
          switch (fallbackStrategy) {
            case "bestFit": {
              var _overflowsData$map$so;
              const placement2 = (_overflowsData$map$so = overflowsData.map((d) => [d, d.overflows.filter((overflow2) => overflow2 > 0).reduce((acc, overflow2) => acc + overflow2, 0)]).sort((a, b) => a[1] - b[1])[0]) == null ? void 0 : _overflowsData$map$so[0].placement;
              if (placement2) {
                resetPlacement = placement2;
              }
              break;
            }
            case "initialPlacement":
              resetPlacement = initialPlacement;
              break;
          }
          if (placement !== resetPlacement) {
            return {
              reset: {
                placement: resetPlacement
              }
            };
          }
        }
        return {};
      }
    };
  };
  function getSideOffsets(overflow, rect) {
    return {
      top: overflow.top - rect.height,
      right: overflow.right - rect.width,
      bottom: overflow.bottom - rect.height,
      left: overflow.left - rect.width
    };
  }
  function isAnySideFullyClipped(overflow) {
    return sides.some((side) => overflow[side] >= 0);
  }
  var hide = function(_temp) {
    let {
      strategy = "referenceHidden",
      ...detectOverflowOptions
    } = _temp === void 0 ? {} : _temp;
    return {
      name: "hide",
      async fn(middlewareArguments) {
        const {
          rects
        } = middlewareArguments;
        switch (strategy) {
          case "referenceHidden": {
            const overflow = await detectOverflow(middlewareArguments, {
              ...detectOverflowOptions,
              elementContext: "reference"
            });
            const offsets = getSideOffsets(overflow, rects.reference);
            return {
              data: {
                referenceHiddenOffsets: offsets,
                referenceHidden: isAnySideFullyClipped(offsets)
              }
            };
          }
          case "escaped": {
            const overflow = await detectOverflow(middlewareArguments, {
              ...detectOverflowOptions,
              altBoundary: true
            });
            const offsets = getSideOffsets(overflow, rects.floating);
            return {
              data: {
                escapedOffsets: offsets,
                escaped: isAnySideFullyClipped(offsets)
              }
            };
          }
          default: {
            return {};
          }
        }
      }
    };
  };
  function convertValueToCoords(placement, rects, value, rtl) {
    if (rtl === void 0) {
      rtl = false;
    }
    const side = getSide(placement);
    const alignment = getAlignment(placement);
    const isVertical = getMainAxisFromPlacement(placement) === "x";
    const mainAxisMulti = ["left", "top"].includes(side) ? -1 : 1;
    const crossAxisMulti = rtl && isVertical ? -1 : 1;
    const rawValue = typeof value === "function" ? value({
      ...rects,
      placement
    }) : value;
    let {
      mainAxis,
      crossAxis,
      alignmentAxis
    } = typeof rawValue === "number" ? {
      mainAxis: rawValue,
      crossAxis: 0,
      alignmentAxis: null
    } : {
      mainAxis: 0,
      crossAxis: 0,
      alignmentAxis: null,
      ...rawValue
    };
    if (alignment && typeof alignmentAxis === "number") {
      crossAxis = alignment === "end" ? alignmentAxis * -1 : alignmentAxis;
    }
    return isVertical ? {
      x: crossAxis * crossAxisMulti,
      y: mainAxis * mainAxisMulti
    } : {
      x: mainAxis * mainAxisMulti,
      y: crossAxis * crossAxisMulti
    };
  }
  var offset = function(value) {
    if (value === void 0) {
      value = 0;
    }
    return {
      name: "offset",
      options: value,
      async fn(middlewareArguments) {
        const {
          x,
          y,
          placement,
          rects,
          platform: platform2,
          elements
        } = middlewareArguments;
        const diffCoords = convertValueToCoords(placement, rects, value, await (platform2.isRTL == null ? void 0 : platform2.isRTL(elements.floating)));
        return {
          x: x + diffCoords.x,
          y: y + diffCoords.y,
          data: diffCoords
        };
      }
    };
  };
  function getCrossAxis(axis) {
    return axis === "x" ? "y" : "x";
  }
  var shift = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "shift",
      options,
      async fn(middlewareArguments) {
        const {
          x,
          y,
          placement
        } = middlewareArguments;
        const {
          mainAxis: checkMainAxis = true,
          crossAxis: checkCrossAxis = false,
          limiter = {
            fn: (_ref) => {
              let {
                x: x2,
                y: y2
              } = _ref;
              return {
                x: x2,
                y: y2
              };
            }
          },
          ...detectOverflowOptions
        } = options;
        const coords = {
          x,
          y
        };
        const overflow = await detectOverflow(middlewareArguments, detectOverflowOptions);
        const mainAxis = getMainAxisFromPlacement(getSide(placement));
        const crossAxis = getCrossAxis(mainAxis);
        let mainAxisCoord = coords[mainAxis];
        let crossAxisCoord = coords[crossAxis];
        if (checkMainAxis) {
          const minSide = mainAxis === "y" ? "top" : "left";
          const maxSide = mainAxis === "y" ? "bottom" : "right";
          const min3 = mainAxisCoord + overflow[minSide];
          const max3 = mainAxisCoord - overflow[maxSide];
          mainAxisCoord = within(min3, mainAxisCoord, max3);
        }
        if (checkCrossAxis) {
          const minSide = crossAxis === "y" ? "top" : "left";
          const maxSide = crossAxis === "y" ? "bottom" : "right";
          const min3 = crossAxisCoord + overflow[minSide];
          const max3 = crossAxisCoord - overflow[maxSide];
          crossAxisCoord = within(min3, crossAxisCoord, max3);
        }
        const limitedCoords = limiter.fn({
          ...middlewareArguments,
          [mainAxis]: mainAxisCoord,
          [crossAxis]: crossAxisCoord
        });
        return {
          ...limitedCoords,
          data: {
            x: limitedCoords.x - x,
            y: limitedCoords.y - y
          }
        };
      }
    };
  };
  var size = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "size",
      options,
      async fn(middlewareArguments) {
        const {
          placement,
          rects,
          platform: platform2,
          elements
        } = middlewareArguments;
        const {
          apply,
          ...detectOverflowOptions
        } = options;
        const overflow = await detectOverflow(middlewareArguments, detectOverflowOptions);
        const side = getSide(placement);
        const alignment = getAlignment(placement);
        let heightSide;
        let widthSide;
        if (side === "top" || side === "bottom") {
          heightSide = side;
          widthSide = alignment === (await (platform2.isRTL == null ? void 0 : platform2.isRTL(elements.floating)) ? "start" : "end") ? "left" : "right";
        } else {
          widthSide = side;
          heightSide = alignment === "end" ? "top" : "bottom";
        }
        const xMin = max(overflow.left, 0);
        const xMax = max(overflow.right, 0);
        const yMin = max(overflow.top, 0);
        const yMax = max(overflow.bottom, 0);
        const dimensions = {
          height: rects.floating.height - (["left", "right"].includes(placement) ? 2 * (yMin !== 0 || yMax !== 0 ? yMin + yMax : max(overflow.top, overflow.bottom)) : overflow[heightSide]),
          width: rects.floating.width - (["top", "bottom"].includes(placement) ? 2 * (xMin !== 0 || xMax !== 0 ? xMin + xMax : max(overflow.left, overflow.right)) : overflow[widthSide])
        };
        const prevDimensions = await platform2.getDimensions(elements.floating);
        apply == null ? void 0 : apply({
          ...dimensions,
          ...rects
        });
        const nextDimensions = await platform2.getDimensions(elements.floating);
        if (prevDimensions.width !== nextDimensions.width || prevDimensions.height !== nextDimensions.height) {
          return {
            reset: {
              rects: true
            }
          };
        }
        return {};
      }
    };
  };
  var inline = function(options) {
    if (options === void 0) {
      options = {};
    }
    return {
      name: "inline",
      options,
      async fn(middlewareArguments) {
        var _await$platform$getCl;
        const {
          placement,
          elements,
          rects,
          platform: platform2,
          strategy
        } = middlewareArguments;
        const {
          padding = 2,
          x,
          y
        } = options;
        const fallback = rectToClientRect(platform2.convertOffsetParentRelativeRectToViewportRelativeRect ? await platform2.convertOffsetParentRelativeRectToViewportRelativeRect({
          rect: rects.reference,
          offsetParent: await (platform2.getOffsetParent == null ? void 0 : platform2.getOffsetParent(elements.floating)),
          strategy
        }) : rects.reference);
        const clientRects = (_await$platform$getCl = await (platform2.getClientRects == null ? void 0 : platform2.getClientRects(elements.reference))) != null ? _await$platform$getCl : [];
        const paddingObject = getSideObjectFromPadding(padding);
        function getBoundingClientRect2() {
          if (clientRects.length === 2 && clientRects[0].left > clientRects[1].right && x != null && y != null) {
            var _clientRects$find;
            return (_clientRects$find = clientRects.find((rect) => x > rect.left - paddingObject.left && x < rect.right + paddingObject.right && y > rect.top - paddingObject.top && y < rect.bottom + paddingObject.bottom)) != null ? _clientRects$find : fallback;
          }
          if (clientRects.length >= 2) {
            if (getMainAxisFromPlacement(placement) === "x") {
              const firstRect = clientRects[0];
              const lastRect = clientRects[clientRects.length - 1];
              const isTop = getSide(placement) === "top";
              const top2 = firstRect.top;
              const bottom2 = lastRect.bottom;
              const left2 = isTop ? firstRect.left : lastRect.left;
              const right2 = isTop ? firstRect.right : lastRect.right;
              const width2 = right2 - left2;
              const height2 = bottom2 - top2;
              return {
                top: top2,
                bottom: bottom2,
                left: left2,
                right: right2,
                width: width2,
                height: height2,
                x: left2,
                y: top2
              };
            }
            const isLeftSide = getSide(placement) === "left";
            const maxRight = max(...clientRects.map((rect) => rect.right));
            const minLeft = min(...clientRects.map((rect) => rect.left));
            const measureRects = clientRects.filter((rect) => isLeftSide ? rect.left === minLeft : rect.right === maxRight);
            const top = measureRects[0].top;
            const bottom = measureRects[measureRects.length - 1].bottom;
            const left = minLeft;
            const right = maxRight;
            const width = right - left;
            const height = bottom - top;
            return {
              top,
              bottom,
              left,
              right,
              width,
              height,
              x: left,
              y: top
            };
          }
          return fallback;
        }
        const resetRects = await platform2.getElementRects({
          reference: {
            getBoundingClientRect: getBoundingClientRect2
          },
          floating: elements.floating,
          strategy
        });
        if (rects.reference.x !== resetRects.reference.x || rects.reference.y !== resetRects.reference.y || rects.reference.width !== resetRects.reference.width || rects.reference.height !== resetRects.reference.height) {
          return {
            reset: {
              rects: resetRects
            }
          };
        }
        return {};
      }
    };
  };
  function isWindow(value) {
    return value && value.document && value.location && value.alert && value.setInterval;
  }
  function getWindow(node) {
    if (node == null) {
      return window;
    }
    if (!isWindow(node)) {
      const ownerDocument = node.ownerDocument;
      return ownerDocument ? ownerDocument.defaultView || window : window;
    }
    return node;
  }
  function getComputedStyle$1(element) {
    return getWindow(element).getComputedStyle(element);
  }
  function getNodeName(node) {
    return isWindow(node) ? "" : node ? (node.nodeName || "").toLowerCase() : "";
  }
  function isHTMLElement(value) {
    return value instanceof getWindow(value).HTMLElement;
  }
  function isElement(value) {
    return value instanceof getWindow(value).Element;
  }
  function isNode(value) {
    return value instanceof getWindow(value).Node;
  }
  function isShadowRoot(node) {
    if (typeof ShadowRoot === "undefined") {
      return false;
    }
    const OwnElement = getWindow(node).ShadowRoot;
    return node instanceof OwnElement || node instanceof ShadowRoot;
  }
  function isOverflowElement(element) {
    const {
      overflow,
      overflowX,
      overflowY
    } = getComputedStyle$1(element);
    return /auto|scroll|overlay|hidden/.test(overflow + overflowY + overflowX);
  }
  function isTableElement(element) {
    return ["table", "td", "th"].includes(getNodeName(element));
  }
  function isContainingBlock(element) {
    const isFirefox = navigator.userAgent.toLowerCase().includes("firefox");
    const css2 = getComputedStyle$1(element);
    return css2.transform !== "none" || css2.perspective !== "none" || css2.contain === "paint" || ["transform", "perspective"].includes(css2.willChange) || isFirefox && css2.willChange === "filter" || isFirefox && (css2.filter ? css2.filter !== "none" : false);
  }
  function isLayoutViewport() {
    return !/^((?!chrome|android).)*safari/i.test(navigator.userAgent);
  }
  var min2 = Math.min;
  var max2 = Math.max;
  var round = Math.round;
  function getBoundingClientRect(element, includeScale, isFixedStrategy) {
    var _win$visualViewport$o, _win$visualViewport, _win$visualViewport$o2, _win$visualViewport2;
    if (includeScale === void 0) {
      includeScale = false;
    }
    if (isFixedStrategy === void 0) {
      isFixedStrategy = false;
    }
    const clientRect = element.getBoundingClientRect();
    let scaleX = 1;
    let scaleY = 1;
    if (includeScale && isHTMLElement(element)) {
      scaleX = element.offsetWidth > 0 ? round(clientRect.width) / element.offsetWidth || 1 : 1;
      scaleY = element.offsetHeight > 0 ? round(clientRect.height) / element.offsetHeight || 1 : 1;
    }
    const win = isElement(element) ? getWindow(element) : window;
    const addVisualOffsets = !isLayoutViewport() && isFixedStrategy;
    const x = (clientRect.left + (addVisualOffsets ? (_win$visualViewport$o = (_win$visualViewport = win.visualViewport) == null ? void 0 : _win$visualViewport.offsetLeft) != null ? _win$visualViewport$o : 0 : 0)) / scaleX;
    const y = (clientRect.top + (addVisualOffsets ? (_win$visualViewport$o2 = (_win$visualViewport2 = win.visualViewport) == null ? void 0 : _win$visualViewport2.offsetTop) != null ? _win$visualViewport$o2 : 0 : 0)) / scaleY;
    const width = clientRect.width / scaleX;
    const height = clientRect.height / scaleY;
    return {
      width,
      height,
      top: y,
      right: x + width,
      bottom: y + height,
      left: x,
      x,
      y
    };
  }
  function getDocumentElement(node) {
    return ((isNode(node) ? node.ownerDocument : node.document) || window.document).documentElement;
  }
  function getNodeScroll(element) {
    if (isElement(element)) {
      return {
        scrollLeft: element.scrollLeft,
        scrollTop: element.scrollTop
      };
    }
    return {
      scrollLeft: element.pageXOffset,
      scrollTop: element.pageYOffset
    };
  }
  function getWindowScrollBarX(element) {
    return getBoundingClientRect(getDocumentElement(element)).left + getNodeScroll(element).scrollLeft;
  }
  function isScaled(element) {
    const rect = getBoundingClientRect(element);
    return round(rect.width) !== element.offsetWidth || round(rect.height) !== element.offsetHeight;
  }
  function getRectRelativeToOffsetParent(element, offsetParent, strategy) {
    const isOffsetParentAnElement = isHTMLElement(offsetParent);
    const documentElement = getDocumentElement(offsetParent);
    const rect = getBoundingClientRect(
      element,
      isOffsetParentAnElement && isScaled(offsetParent),
      strategy === "fixed"
    );
    let scroll = {
      scrollLeft: 0,
      scrollTop: 0
    };
    const offsets = {
      x: 0,
      y: 0
    };
    if (isOffsetParentAnElement || !isOffsetParentAnElement && strategy !== "fixed") {
      if (getNodeName(offsetParent) !== "body" || isOverflowElement(documentElement)) {
        scroll = getNodeScroll(offsetParent);
      }
      if (isHTMLElement(offsetParent)) {
        const offsetRect = getBoundingClientRect(offsetParent, true);
        offsets.x = offsetRect.x + offsetParent.clientLeft;
        offsets.y = offsetRect.y + offsetParent.clientTop;
      } else if (documentElement) {
        offsets.x = getWindowScrollBarX(documentElement);
      }
    }
    return {
      x: rect.left + scroll.scrollLeft - offsets.x,
      y: rect.top + scroll.scrollTop - offsets.y,
      width: rect.width,
      height: rect.height
    };
  }
  function getParentNode(node) {
    if (getNodeName(node) === "html") {
      return node;
    }
    return node.assignedSlot || node.parentNode || (isShadowRoot(node) ? node.host : null) || getDocumentElement(node);
  }
  function getTrueOffsetParent(element) {
    if (!isHTMLElement(element) || getComputedStyle(element).position === "fixed") {
      return null;
    }
    return element.offsetParent;
  }
  function getContainingBlock(element) {
    let currentNode = getParentNode(element);
    if (isShadowRoot(currentNode)) {
      currentNode = currentNode.host;
    }
    while (isHTMLElement(currentNode) && !["html", "body"].includes(getNodeName(currentNode))) {
      if (isContainingBlock(currentNode)) {
        return currentNode;
      } else {
        currentNode = currentNode.parentNode;
      }
    }
    return null;
  }
  function getOffsetParent(element) {
    const window2 = getWindow(element);
    let offsetParent = getTrueOffsetParent(element);
    while (offsetParent && isTableElement(offsetParent) && getComputedStyle(offsetParent).position === "static") {
      offsetParent = getTrueOffsetParent(offsetParent);
    }
    if (offsetParent && (getNodeName(offsetParent) === "html" || getNodeName(offsetParent) === "body" && getComputedStyle(offsetParent).position === "static" && !isContainingBlock(offsetParent))) {
      return window2;
    }
    return offsetParent || getContainingBlock(element) || window2;
  }
  function getDimensions(element) {
    if (isHTMLElement(element)) {
      return {
        width: element.offsetWidth,
        height: element.offsetHeight
      };
    }
    const rect = getBoundingClientRect(element);
    return {
      width: rect.width,
      height: rect.height
    };
  }
  function convertOffsetParentRelativeRectToViewportRelativeRect(_ref) {
    let {
      rect,
      offsetParent,
      strategy
    } = _ref;
    const isOffsetParentAnElement = isHTMLElement(offsetParent);
    const documentElement = getDocumentElement(offsetParent);
    if (offsetParent === documentElement) {
      return rect;
    }
    let scroll = {
      scrollLeft: 0,
      scrollTop: 0
    };
    const offsets = {
      x: 0,
      y: 0
    };
    if (isOffsetParentAnElement || !isOffsetParentAnElement && strategy !== "fixed") {
      if (getNodeName(offsetParent) !== "body" || isOverflowElement(documentElement)) {
        scroll = getNodeScroll(offsetParent);
      }
      if (isHTMLElement(offsetParent)) {
        const offsetRect = getBoundingClientRect(offsetParent, true);
        offsets.x = offsetRect.x + offsetParent.clientLeft;
        offsets.y = offsetRect.y + offsetParent.clientTop;
      }
    }
    return {
      ...rect,
      x: rect.x - scroll.scrollLeft + offsets.x,
      y: rect.y - scroll.scrollTop + offsets.y
    };
  }
  function getViewportRect(element, strategy) {
    const win = getWindow(element);
    const html = getDocumentElement(element);
    const visualViewport = win.visualViewport;
    let width = html.clientWidth;
    let height = html.clientHeight;
    let x = 0;
    let y = 0;
    if (visualViewport) {
      width = visualViewport.width;
      height = visualViewport.height;
      const layoutViewport = isLayoutViewport();
      if (layoutViewport || !layoutViewport && strategy === "fixed") {
        x = visualViewport.offsetLeft;
        y = visualViewport.offsetTop;
      }
    }
    return {
      width,
      height,
      x,
      y
    };
  }
  function getDocumentRect(element) {
    var _element$ownerDocumen;
    const html = getDocumentElement(element);
    const scroll = getNodeScroll(element);
    const body = (_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body;
    const width = max2(html.scrollWidth, html.clientWidth, body ? body.scrollWidth : 0, body ? body.clientWidth : 0);
    const height = max2(html.scrollHeight, html.clientHeight, body ? body.scrollHeight : 0, body ? body.clientHeight : 0);
    let x = -scroll.scrollLeft + getWindowScrollBarX(element);
    const y = -scroll.scrollTop;
    if (getComputedStyle$1(body || html).direction === "rtl") {
      x += max2(html.clientWidth, body ? body.clientWidth : 0) - width;
    }
    return {
      width,
      height,
      x,
      y
    };
  }
  function getNearestOverflowAncestor(node) {
    const parentNode = getParentNode(node);
    if (["html", "body", "#document"].includes(getNodeName(parentNode))) {
      return node.ownerDocument.body;
    }
    if (isHTMLElement(parentNode) && isOverflowElement(parentNode)) {
      return parentNode;
    }
    return getNearestOverflowAncestor(parentNode);
  }
  function getOverflowAncestors(node, list) {
    var _node$ownerDocument;
    if (list === void 0) {
      list = [];
    }
    const scrollableAncestor = getNearestOverflowAncestor(node);
    const isBody = scrollableAncestor === ((_node$ownerDocument = node.ownerDocument) == null ? void 0 : _node$ownerDocument.body);
    const win = getWindow(scrollableAncestor);
    const target = isBody ? [win].concat(win.visualViewport || [], isOverflowElement(scrollableAncestor) ? scrollableAncestor : []) : scrollableAncestor;
    const updatedList = list.concat(target);
    return isBody ? updatedList : updatedList.concat(getOverflowAncestors(target));
  }
  function contains(parent, child) {
    const rootNode = child == null ? void 0 : child.getRootNode == null ? void 0 : child.getRootNode();
    if (parent != null && parent.contains(child)) {
      return true;
    } else if (rootNode && isShadowRoot(rootNode)) {
      let next = child;
      do {
        if (next && parent === next) {
          return true;
        }
        next = next.parentNode || next.host;
      } while (next);
    }
    return false;
  }
  function getInnerBoundingClientRect(element, strategy) {
    const clientRect = getBoundingClientRect(element, false, strategy === "fixed");
    const top = clientRect.top + element.clientTop;
    const left = clientRect.left + element.clientLeft;
    return {
      top,
      left,
      x: left,
      y: top,
      right: left + element.clientWidth,
      bottom: top + element.clientHeight,
      width: element.clientWidth,
      height: element.clientHeight
    };
  }
  function getClientRectFromClippingAncestor(element, clippingParent, strategy) {
    if (clippingParent === "viewport") {
      return rectToClientRect(getViewportRect(element, strategy));
    }
    if (isElement(clippingParent)) {
      return getInnerBoundingClientRect(clippingParent, strategy);
    }
    return rectToClientRect(getDocumentRect(getDocumentElement(element)));
  }
  function getClippingAncestors(element) {
    const clippingAncestors = getOverflowAncestors(element);
    const canEscapeClipping = ["absolute", "fixed"].includes(getComputedStyle$1(element).position);
    const clipperElement = canEscapeClipping && isHTMLElement(element) ? getOffsetParent(element) : element;
    if (!isElement(clipperElement)) {
      return [];
    }
    return clippingAncestors.filter((clippingAncestors2) => isElement(clippingAncestors2) && contains(clippingAncestors2, clipperElement) && getNodeName(clippingAncestors2) !== "body");
  }
  function getClippingRect(_ref) {
    let {
      element,
      boundary,
      rootBoundary,
      strategy
    } = _ref;
    const mainClippingAncestors = boundary === "clippingAncestors" ? getClippingAncestors(element) : [].concat(boundary);
    const clippingAncestors = [...mainClippingAncestors, rootBoundary];
    const firstClippingAncestor = clippingAncestors[0];
    const clippingRect = clippingAncestors.reduce((accRect, clippingAncestor) => {
      const rect = getClientRectFromClippingAncestor(element, clippingAncestor, strategy);
      accRect.top = max2(rect.top, accRect.top);
      accRect.right = min2(rect.right, accRect.right);
      accRect.bottom = min2(rect.bottom, accRect.bottom);
      accRect.left = max2(rect.left, accRect.left);
      return accRect;
    }, getClientRectFromClippingAncestor(element, firstClippingAncestor, strategy));
    return {
      width: clippingRect.right - clippingRect.left,
      height: clippingRect.bottom - clippingRect.top,
      x: clippingRect.left,
      y: clippingRect.top
    };
  }
  var platform = {
    getClippingRect,
    convertOffsetParentRelativeRectToViewportRelativeRect,
    isElement,
    getDimensions,
    getOffsetParent,
    getDocumentElement,
    getElementRects: (_ref) => {
      let {
        reference,
        floating,
        strategy
      } = _ref;
      return {
        reference: getRectRelativeToOffsetParent(reference, getOffsetParent(floating), strategy),
        floating: {
          ...getDimensions(floating),
          x: 0,
          y: 0
        }
      };
    },
    getClientRects: (element) => Array.from(element.getClientRects()),
    isRTL: (element) => getComputedStyle$1(element).direction === "rtl"
  };
  function autoUpdate(reference, floating, update, options) {
    if (options === void 0) {
      options = {};
    }
    const {
      ancestorScroll: _ancestorScroll = true,
      ancestorResize: _ancestorResize = true,
      elementResize: _elementResize = true,
      animationFrame = false
    } = options;
    let cleanedUp = false;
    const ancestorScroll = _ancestorScroll && !animationFrame;
    const ancestorResize = _ancestorResize && !animationFrame;
    const elementResize = _elementResize && !animationFrame;
    const ancestors = ancestorScroll || ancestorResize ? [...isElement(reference) ? getOverflowAncestors(reference) : [], ...getOverflowAncestors(floating)] : [];
    ancestors.forEach((ancestor) => {
      ancestorScroll && ancestor.addEventListener("scroll", update, {
        passive: true
      });
      ancestorResize && ancestor.addEventListener("resize", update);
    });
    let observer2 = null;
    if (elementResize) {
      observer2 = new ResizeObserver(update);
      isElement(reference) && observer2.observe(reference);
      observer2.observe(floating);
    }
    let frameId;
    let prevRefRect = animationFrame ? getBoundingClientRect(reference) : null;
    if (animationFrame) {
      frameLoop();
    }
    function frameLoop() {
      if (cleanedUp) {
        return;
      }
      const nextRefRect = getBoundingClientRect(reference);
      if (prevRefRect && (nextRefRect.x !== prevRefRect.x || nextRefRect.y !== prevRefRect.y || nextRefRect.width !== prevRefRect.width || nextRefRect.height !== prevRefRect.height)) {
        update();
      }
      prevRefRect = nextRefRect;
      frameId = requestAnimationFrame(frameLoop);
    }
    return () => {
      var _observer;
      cleanedUp = true;
      ancestors.forEach((ancestor) => {
        ancestorScroll && ancestor.removeEventListener("scroll", update);
        ancestorResize && ancestor.removeEventListener("resize", update);
      });
      (_observer = observer2) == null ? void 0 : _observer.disconnect();
      observer2 = null;
      if (animationFrame) {
        cancelAnimationFrame(frameId);
      }
    };
  }
  var computePosition2 = (reference, floating, options) => computePosition(reference, floating, {
    platform,
    ...options
  });
  var buildConfigFromModifiers = (modifiers) => {
    const config = {
      placement: "bottom",
      middleware: []
    };
    const keys = Object.keys(modifiers);
    const getModifierArgument = (modifier) => {
      return modifiers[modifier];
    };
    if (keys.includes("offset")) {
      config.middleware.push(offset(getModifierArgument("offset")));
    }
    if (keys.includes("placement")) {
      config.placement = getModifierArgument("placement");
    }
    if (keys.includes("autoPlacement") && !keys.includes("flip")) {
      config.middleware.push(autoPlacement(getModifierArgument("autoPlacement")));
    }
    if (keys.includes("flip")) {
      config.middleware.push(flip(getModifierArgument("flip")));
    }
    if (keys.includes("shift")) {
      config.middleware.push(shift(getModifierArgument("shift")));
    }
    if (keys.includes("inline")) {
      config.middleware.push(inline(getModifierArgument("inline")));
    }
    if (keys.includes("arrow")) {
      config.middleware.push(arrow(getModifierArgument("arrow")));
    }
    if (keys.includes("hide")) {
      config.middleware.push(hide(getModifierArgument("hide")));
    }
    if (keys.includes("size")) {
      config.middleware.push(size(getModifierArgument("size")));
    }
    return config;
  };
  var buildDirectiveConfigFromModifiers = (modifiers, settings) => {
    const config = {
      component: {
        trap: false
      },
      float: {
        placement: "bottom",
        strategy: "absolute",
        middleware: []
      }
    };
    const getModifierArgument = (modifier) => {
      return modifiers[modifiers.indexOf(modifier) + 1];
    };
    if (modifiers.includes("trap")) {
      config.component.trap = true;
    }
    if (modifiers.includes("teleport")) {
      config.float.strategy = "fixed";
    }
    if (modifiers.includes("offset")) {
      config.float.middleware.push(offset(settings["offset"] || 10));
    }
    if (modifiers.includes("placement")) {
      config.float.placement = getModifierArgument("placement");
    }
    if (modifiers.includes("autoPlacement") && !modifiers.includes("flip")) {
      config.float.middleware.push(autoPlacement(settings["autoPlacement"]));
    }
    if (modifiers.includes("flip")) {
      config.float.middleware.push(flip(settings["flip"]));
    }
    if (modifiers.includes("shift")) {
      config.float.middleware.push(shift(settings["shift"]));
    }
    if (modifiers.includes("inline")) {
      config.float.middleware.push(inline(settings["inline"]));
    }
    if (modifiers.includes("arrow")) {
      config.float.middleware.push(arrow(settings["arrow"]));
    }
    if (modifiers.includes("hide")) {
      config.float.middleware.push(hide(settings["hide"]));
    }
    if (modifiers.includes("size")) {
      config.float.middleware.push(size(settings["size"]));
    }
    return config;
  };
  var randomString = (length) => {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz".split("");
    var str = "";
    if (!length) {
      length = Math.floor(Math.random() * chars.length);
    }
    for (var i = 0; i < length; i++) {
      str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
  };
  var onAttributeAddeds = [];
  var onElRemoveds = [];
  var onElAddeds = [];
  function cleanupAttributes(el, names) {
    if (!el._x_attributeCleanups)
      return;
    Object.entries(el._x_attributeCleanups).forEach(([name, value]) => {
      if (names === void 0 || names.includes(name)) {
        value.forEach((i) => i());
        delete el._x_attributeCleanups[name];
      }
    });
  }
  var observer = new MutationObserver(onMutate);
  var currentlyObserving = false;
  function startObservingMutations() {
    observer.observe(document, { subtree: true, childList: true, attributes: true, attributeOldValue: true });
    currentlyObserving = true;
  }
  function stopObservingMutations() {
    flushObserver();
    observer.disconnect();
    currentlyObserving = false;
  }
  var recordQueue = [];
  var willProcessRecordQueue = false;
  function flushObserver() {
    recordQueue = recordQueue.concat(observer.takeRecords());
    if (recordQueue.length && !willProcessRecordQueue) {
      willProcessRecordQueue = true;
      queueMicrotask(() => {
        processRecordQueue();
        willProcessRecordQueue = false;
      });
    }
  }
  function processRecordQueue() {
    onMutate(recordQueue);
    recordQueue.length = 0;
  }
  function mutateDom(callback) {
    if (!currentlyObserving)
      return callback();
    stopObservingMutations();
    let result = callback();
    startObservingMutations();
    return result;
  }
  var isCollecting = false;
  var deferredMutations = [];
  function onMutate(mutations) {
    if (isCollecting) {
      deferredMutations = deferredMutations.concat(mutations);
      return;
    }
    let addedNodes = [];
    let removedNodes = [];
    let addedAttributes = /* @__PURE__ */ new Map();
    let removedAttributes = /* @__PURE__ */ new Map();
    for (let i = 0; i < mutations.length; i++) {
      if (mutations[i].target._x_ignoreMutationObserver)
        continue;
      if (mutations[i].type === "childList") {
        mutations[i].addedNodes.forEach((node) => node.nodeType === 1 && addedNodes.push(node));
        mutations[i].removedNodes.forEach((node) => node.nodeType === 1 && removedNodes.push(node));
      }
      if (mutations[i].type === "attributes") {
        let el = mutations[i].target;
        let name = mutations[i].attributeName;
        let oldValue = mutations[i].oldValue;
        let add = () => {
          if (!addedAttributes.has(el))
            addedAttributes.set(el, []);
          addedAttributes.get(el).push({ name, value: el.getAttribute(name) });
        };
        let remove = () => {
          if (!removedAttributes.has(el))
            removedAttributes.set(el, []);
          removedAttributes.get(el).push(name);
        };
        if (el.hasAttribute(name) && oldValue === null) {
          add();
        } else if (el.hasAttribute(name)) {
          remove();
          add();
        } else {
          remove();
        }
      }
    }
    removedAttributes.forEach((attrs, el) => {
      cleanupAttributes(el, attrs);
    });
    addedAttributes.forEach((attrs, el) => {
      onAttributeAddeds.forEach((i) => i(el, attrs));
    });
    for (let node of removedNodes) {
      if (addedNodes.includes(node))
        continue;
      onElRemoveds.forEach((i) => i(node));
      if (node._x_cleanups) {
        while (node._x_cleanups.length)
          node._x_cleanups.pop()();
      }
    }
    addedNodes.forEach((node) => {
      node._x_ignoreSelf = true;
      node._x_ignore = true;
    });
    for (let node of addedNodes) {
      if (removedNodes.includes(node))
        continue;
      if (!node.isConnected)
        continue;
      delete node._x_ignoreSelf;
      delete node._x_ignore;
      onElAddeds.forEach((i) => i(node));
      node._x_ignore = true;
      node._x_ignoreSelf = true;
    }
    addedNodes.forEach((node) => {
      delete node._x_ignoreSelf;
      delete node._x_ignore;
    });
    addedNodes = null;
    removedNodes = null;
    addedAttributes = null;
    removedAttributes = null;
  }
  function once(callback, fallback = () => {
  }) {
    let called = false;
    return function() {
      if (!called) {
        called = true;
        callback.apply(this, arguments);
      } else {
        fallback.apply(this, arguments);
      }
    };
  }
  function src_default(Alpine) {
    const defaultOptions = {
      dismissable: true,
      trap: false
    };
    function setupA11y(component, trigger, panel = null) {
      if (!trigger)
        return;
      if (!trigger.hasAttribute("aria-expanded")) {
        trigger.setAttribute("aria-expanded", false);
      }
      if (!panel.hasAttribute("id")) {
        const panelId = `panel-${randomString(8)}`;
        trigger.setAttribute("aria-controls", panelId);
        panel.setAttribute("id", panelId);
      } else {
        trigger.setAttribute("aria-controls", panel.getAttribute("id"));
      }
      panel.setAttribute("aria-modal", true);
      panel.setAttribute("role", "dialog");
    }
    const atMagicTrigger = document.querySelectorAll('[\\@click^="$float"]');
    const xMagicTrigger = document.querySelectorAll('[x-on\\:click^="$float"]');
    [...atMagicTrigger, ...xMagicTrigger].forEach((trigger) => {
      const component = trigger.parentElement.closest("[x-data]");
      const panel = component.querySelector('[x-ref="panel"]');
      setupA11y(component, trigger, panel);
    });
    Alpine.magic("float", (el) => {
      return (modifiers = {}, settings = {}) => {
        const options = { ...defaultOptions, ...settings };
        const config = Object.keys(modifiers).length > 0 ? buildConfigFromModifiers(modifiers) : { middleware: [autoPlacement()] };
        const trigger = el;
        const component = el.parentElement.closest("[x-data]");
        const panel = component.querySelector('[x-ref="panel"]');
        function isFloating() {
          return panel.style.display == "block";
        }
        function closePanel() {
          panel.style.display = "";
          trigger.setAttribute("aria-expanded", false);
          if (options.trap)
            panel.setAttribute("x-trap", false);
          autoUpdate(el, panel, update);
        }
        function openPanel() {
          panel.style.display = "block";
          trigger.setAttribute("aria-expanded", true);
          if (options.trap)
            panel.setAttribute("x-trap", true);
          update();
        }
        function togglePanel() {
          isFloating() ? closePanel() : openPanel();
        }
        async function update() {
          return await computePosition2(el, panel, config).then(({ middlewareData, placement, x, y }) => {
            if (middlewareData.arrow) {
              const ax = middlewareData.arrow?.x;
              const ay = middlewareData.arrow?.y;
              const aEl = config.middleware.filter((middleware) => middleware.name == "arrow")[0].options.element;
              const staticSide = {
                top: "bottom",
                right: "left",
                bottom: "top",
                left: "right"
              }[placement.split("-")[0]];
              Object.assign(aEl.style, {
                left: ax != null ? `${ax}px` : "",
                top: ay != null ? `${ay}px` : "",
                right: "",
                bottom: "",
                [staticSide]: "-4px"
              });
            }
            if (middlewareData.hide) {
              const { referenceHidden } = middlewareData.hide;
              Object.assign(panel.style, {
                visibility: referenceHidden ? "hidden" : "visible"
              });
            }
            Object.assign(panel.style, {
              left: `${x}px`,
              top: `${y}px`
            });
          });
        }
        if (options.dismissable) {
          window.addEventListener("click", (event) => {
            if (!component.contains(event.target) && isFloating()) {
              togglePanel();
            }
          });
          window.addEventListener(
            "keydown",
            (event) => {
              if (event.key === "Escape" && isFloating()) {
                togglePanel();
              }
            },
            true
          );
        }
        togglePanel();
      };
    });
    Alpine.directive("float", (panel, { modifiers, expression }, { evaluate, effect }) => {
      const settings = expression ? evaluate(expression) : {};
      const config = modifiers.length > 0 ? buildDirectiveConfigFromModifiers(modifiers, settings) : {};
      let cleanup = null;
      if (config.float.strategy == "fixed") {
        panel.style.position = "fixed";
      }
      const clickAway = (event) => panel.parentElement && !panel.parentElement.closest("[x-data]").contains(event.target) ? panel.close() : null;
      const keyEscape = (event) => event.key === "Escape" ? panel.close() : null;
      const refName = panel.getAttribute("x-ref");
      const component = panel.parentElement.closest("[x-data]");
      const atTrigger = component.querySelectorAll(`[\\@click^="$refs.${refName}"]`);
      const xTrigger = component.querySelectorAll(`[x-on\\:click^="$refs.${refName}"]`);
      panel.style.setProperty("display", "none");
      setupA11y(component, [...atTrigger, ...xTrigger][0], panel);
      panel._x_isShown = false;
      panel.trigger = null;
      if (!panel._x_doHide)
        panel._x_doHide = () => {
          mutateDom(() => {
            panel.style.setProperty("display", "none", modifiers.includes("important") ? "important" : void 0);
          });
        };
      if (!panel._x_doShow)
        panel._x_doShow = () => {
          mutateDom(() => {
            panel.style.setProperty("display", "block", modifiers.includes("important") ? "important" : void 0);
          });
        };
      let hide2 = () => {
        panel._x_doHide();
        panel._x_isShown = false;
      };
      let show = () => {
        panel._x_doShow();
        panel._x_isShown = true;
      };
      let clickAwayCompatibleShow = () => setTimeout(show);
      let toggle = once(
        (value) => value ? show() : hide2(),
        (value) => {
          if (typeof panel._x_toggleAndCascadeWithTransitions === "function") {
            panel._x_toggleAndCascadeWithTransitions(panel, value, show, hide2);
          } else {
            value ? clickAwayCompatibleShow() : hide2();
          }
        }
      );
      let oldValue;
      let firstTime = true;
      effect(
        () => evaluate((value) => {
          if (!firstTime && value === oldValue)
            return;
          if (modifiers.includes("immediate"))
            value ? clickAwayCompatibleShow() : hide2();
          toggle(value);
          oldValue = value;
          firstTime = false;
        })
      );
      panel.open = async function(event) {
        panel.trigger = event.currentTarget ? event.currentTarget : event;
        toggle(true);
        panel.trigger.setAttribute("aria-expanded", true);
        if (config.component.trap)
          panel.setAttribute("x-trap", true);
        cleanup = autoUpdate(panel.trigger, panel, () => {
          computePosition2(panel.trigger, panel, config.float).then(({ middlewareData, placement, x, y }) => {
            if (middlewareData.arrow) {
              const ax = middlewareData.arrow?.x;
              const ay = middlewareData.arrow?.y;
              const aEl = config.float.middleware.filter((middleware) => middleware.name == "arrow")[0].options.element;
              const staticSide = {
                top: "bottom",
                right: "left",
                bottom: "top",
                left: "right"
              }[placement.split("-")[0]];
              Object.assign(aEl.style, {
                left: ax != null ? `${ax}px` : "",
                top: ay != null ? `${ay}px` : "",
                right: "",
                bottom: "",
                [staticSide]: "-4px"
              });
            }
            if (middlewareData.hide) {
              const { referenceHidden } = middlewareData.hide;
              Object.assign(panel.style, {
                visibility: referenceHidden ? "hidden" : "visible"
              });
            }
            Object.assign(panel.style, {
              left: `${x}px`,
              top: `${y}px`
            });
          });
        });
        window.addEventListener("click", clickAway);
        window.addEventListener("keydown", keyEscape, true);
      };
      panel.close = function() {
        toggle(false);
        panel.trigger.setAttribute("aria-expanded", false);
        if (config.component.trap)
          panel.setAttribute("x-trap", false);
        cleanup();
        window.removeEventListener("click", clickAway);
        window.removeEventListener("keydown", keyEscape, false);
      };
      panel.toggle = function(event) {
        panel._x_isShown ? panel.close() : panel.open(event);
      };
    });
  }
  var module_default = src_default;

  // node_modules/alpine-lazy-load-assets/dist/alpine-lazy-load-assets.esm.js
  function alpine_lazy_load_assets_default(Alpine) {
    Alpine.store("lazyLoadedAssets", {
      loaded: /* @__PURE__ */ new Set(),
      check(paths) {
        return Array.isArray(paths) ? paths.every((path) => this.loaded.has(path)) : this.loaded.has(paths);
      },
      markLoaded(paths) {
        Array.isArray(paths) ? paths.forEach((path) => this.loaded.add(path)) : this.loaded.add(paths);
      }
    });
    function assetLoadedEvent(eventName) {
      return new CustomEvent(eventName, {
        bubbles: true,
        composed: true,
        cancelable: true
      });
    }
    async function loadCSS(path, mediaAttr) {
      if (document.querySelector(`link[href="${path}"]`) || Alpine.store("lazyLoadedAssets").check(path)) {
        return;
      }
      const link = document.createElement("link");
      link.type = "text/css";
      link.rel = "stylesheet";
      link.href = path;
      if (mediaAttr) {
        link.media = mediaAttr;
      }
      document.head.append(link);
      await new Promise((resolve, reject) => {
        link.onload = () => {
          Alpine.store("lazyLoadedAssets").markLoaded(path);
          resolve();
        };
        link.onerror = () => {
          reject(new Error(`Failed to load CSS: ${path}`));
        };
      });
    }
    async function loadJS(path, position) {
      if (document.querySelector(`script[src="${path}"]`) || Alpine.store("lazyLoadedAssets").check(path)) {
        return;
      }
      const script = document.createElement("script");
      script.src = path;
      position.has("body-start") ? document.body.prepend(script) : document[position.has("body-end") ? "body" : "head"].append(script);
      await new Promise((resolve, reject) => {
        script.onload = () => {
          Alpine.store("lazyLoadedAssets").markLoaded(path);
          resolve();
        };
        script.onerror = () => {
          reject(new Error(`Failed to load JS: ${path}`));
        };
      });
    }
    Alpine.directive("load-css", (el, { expression }, { evaluate }) => {
      const paths = evaluate(expression);
      const mediaAttr = el.media;
      const eventName = el.getAttribute("data-dispatch");
      Promise.all(paths.map((path) => loadCSS(path, mediaAttr))).then(() => {
        if (eventName) {
          window.dispatchEvent(assetLoadedEvent(eventName + "-css"));
        }
      }).catch((error) => {
        console.error(error);
      });
    });
    Alpine.directive("load-js", (el, { expression, modifiers }, { evaluate }) => {
      const paths = evaluate(expression);
      const position = new Set(modifiers);
      const eventName = el.getAttribute("data-dispatch");
      Promise.all(paths.map((path) => loadJS(path, position))).then(() => {
        if (eventName) {
          window.dispatchEvent(assetLoadedEvent(eventName + "-js"));
        }
      }).catch((error) => {
        console.error(error);
      });
    });
  }
  var module_default2 = alpine_lazy_load_assets_default;

  // node_modules/sortablejs/modular/sortable.esm.js
  function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);
    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(object);
      if (enumerableOnly) {
        symbols = symbols.filter(function(sym) {
          return Object.getOwnPropertyDescriptor(object, sym).enumerable;
        });
      }
      keys.push.apply(keys, symbols);
    }
    return keys;
  }
  function _objectSpread2(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};
      if (i % 2) {
        ownKeys(Object(source), true).forEach(function(key) {
          _defineProperty(target, key, source[key]);
        });
      } else if (Object.getOwnPropertyDescriptors) {
        Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
      } else {
        ownKeys(Object(source)).forEach(function(key) {
          Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
        });
      }
    }
    return target;
  }
  function _typeof(obj) {
    "@babel/helpers - typeof";
    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
      _typeof = function(obj2) {
        return typeof obj2;
      };
    } else {
      _typeof = function(obj2) {
        return obj2 && typeof Symbol === "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
      };
    }
    return _typeof(obj);
  }
  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }
    return obj;
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
  function _objectWithoutProperties(source, excluded) {
    if (source == null)
      return {};
    var target = _objectWithoutPropertiesLoose(source, excluded);
    var key, i;
    if (Object.getOwnPropertySymbols) {
      var sourceSymbolKeys = Object.getOwnPropertySymbols(source);
      for (i = 0; i < sourceSymbolKeys.length; i++) {
        key = sourceSymbolKeys[i];
        if (excluded.indexOf(key) >= 0)
          continue;
        if (!Object.prototype.propertyIsEnumerable.call(source, key))
          continue;
        target[key] = source[key];
      }
    }
    return target;
  }
  var version = "1.15.0";
  function userAgent(pattern) {
    if (typeof window !== "undefined" && window.navigator) {
      return !!/* @__PURE__ */ navigator.userAgent.match(pattern);
    }
  }
  var IE11OrLess = userAgent(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i);
  var Edge = userAgent(/Edge/i);
  var FireFox = userAgent(/firefox/i);
  var Safari = userAgent(/safari/i) && !userAgent(/chrome/i) && !userAgent(/android/i);
  var IOS = userAgent(/iP(ad|od|hone)/i);
  var ChromeForAndroid = userAgent(/chrome/i) && userAgent(/android/i);
  var captureMode = {
    capture: false,
    passive: false
  };
  function on(el, event, fn) {
    el.addEventListener(event, fn, !IE11OrLess && captureMode);
  }
  function off(el, event, fn) {
    el.removeEventListener(event, fn, !IE11OrLess && captureMode);
  }
  function matches(el, selector) {
    if (!selector)
      return;
    selector[0] === ">" && (selector = selector.substring(1));
    if (el) {
      try {
        if (el.matches) {
          return el.matches(selector);
        } else if (el.msMatchesSelector) {
          return el.msMatchesSelector(selector);
        } else if (el.webkitMatchesSelector) {
          return el.webkitMatchesSelector(selector);
        }
      } catch (_) {
        return false;
      }
    }
    return false;
  }
  function getParentOrHost(el) {
    return el.host && el !== document && el.host.nodeType ? el.host : el.parentNode;
  }
  function closest(el, selector, ctx, includeCTX) {
    if (el) {
      ctx = ctx || document;
      do {
        if (selector != null && (selector[0] === ">" ? el.parentNode === ctx && matches(el, selector) : matches(el, selector)) || includeCTX && el === ctx) {
          return el;
        }
        if (el === ctx)
          break;
      } while (el = getParentOrHost(el));
    }
    return null;
  }
  var R_SPACE = /\s+/g;
  function toggleClass(el, name, state) {
    if (el && name) {
      if (el.classList) {
        el.classList[state ? "add" : "remove"](name);
      } else {
        var className = (" " + el.className + " ").replace(R_SPACE, " ").replace(" " + name + " ", " ");
        el.className = (className + (state ? " " + name : "")).replace(R_SPACE, " ");
      }
    }
  }
  function css(el, prop, val) {
    var style = el && el.style;
    if (style) {
      if (val === void 0) {
        if (document.defaultView && document.defaultView.getComputedStyle) {
          val = document.defaultView.getComputedStyle(el, "");
        } else if (el.currentStyle) {
          val = el.currentStyle;
        }
        return prop === void 0 ? val : val[prop];
      } else {
        if (!(prop in style) && prop.indexOf("webkit") === -1) {
          prop = "-webkit-" + prop;
        }
        style[prop] = val + (typeof val === "string" ? "" : "px");
      }
    }
  }
  function matrix(el, selfOnly) {
    var appliedTransforms = "";
    if (typeof el === "string") {
      appliedTransforms = el;
    } else {
      do {
        var transform = css(el, "transform");
        if (transform && transform !== "none") {
          appliedTransforms = transform + " " + appliedTransforms;
        }
      } while (!selfOnly && (el = el.parentNode));
    }
    var matrixFn = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
    return matrixFn && new matrixFn(appliedTransforms);
  }
  function find(ctx, tagName, iterator) {
    if (ctx) {
      var list = ctx.getElementsByTagName(tagName), i = 0, n = list.length;
      if (iterator) {
        for (; i < n; i++) {
          iterator(list[i], i);
        }
      }
      return list;
    }
    return [];
  }
  function getWindowScrollingElement() {
    var scrollingElement = document.scrollingElement;
    if (scrollingElement) {
      return scrollingElement;
    } else {
      return document.documentElement;
    }
  }
  function getRect(el, relativeToContainingBlock, relativeToNonStaticParent, undoScale, container) {
    if (!el.getBoundingClientRect && el !== window)
      return;
    var elRect, top, left, bottom, right, height, width;
    if (el !== window && el.parentNode && el !== getWindowScrollingElement()) {
      elRect = el.getBoundingClientRect();
      top = elRect.top;
      left = elRect.left;
      bottom = elRect.bottom;
      right = elRect.right;
      height = elRect.height;
      width = elRect.width;
    } else {
      top = 0;
      left = 0;
      bottom = window.innerHeight;
      right = window.innerWidth;
      height = window.innerHeight;
      width = window.innerWidth;
    }
    if ((relativeToContainingBlock || relativeToNonStaticParent) && el !== window) {
      container = container || el.parentNode;
      if (!IE11OrLess) {
        do {
          if (container && container.getBoundingClientRect && (css(container, "transform") !== "none" || relativeToNonStaticParent && css(container, "position") !== "static")) {
            var containerRect = container.getBoundingClientRect();
            top -= containerRect.top + parseInt(css(container, "border-top-width"));
            left -= containerRect.left + parseInt(css(container, "border-left-width"));
            bottom = top + elRect.height;
            right = left + elRect.width;
            break;
          }
        } while (container = container.parentNode);
      }
    }
    if (undoScale && el !== window) {
      var elMatrix = matrix(container || el), scaleX = elMatrix && elMatrix.a, scaleY = elMatrix && elMatrix.d;
      if (elMatrix) {
        top /= scaleY;
        left /= scaleX;
        width /= scaleX;
        height /= scaleY;
        bottom = top + height;
        right = left + width;
      }
    }
    return {
      top,
      left,
      bottom,
      right,
      width,
      height
    };
  }
  function isScrolledPast(el, elSide, parentSide) {
    var parent = getParentAutoScrollElement(el, true), elSideVal = getRect(el)[elSide];
    while (parent) {
      var parentSideVal = getRect(parent)[parentSide], visible = void 0;
      if (parentSide === "top" || parentSide === "left") {
        visible = elSideVal >= parentSideVal;
      } else {
        visible = elSideVal <= parentSideVal;
      }
      if (!visible)
        return parent;
      if (parent === getWindowScrollingElement())
        break;
      parent = getParentAutoScrollElement(parent, false);
    }
    return false;
  }
  function getChild(el, childNum, options, includeDragEl) {
    var currentChild = 0, i = 0, children = el.children;
    while (i < children.length) {
      if (children[i].style.display !== "none" && children[i] !== Sortable.ghost && (includeDragEl || children[i] !== Sortable.dragged) && closest(children[i], options.draggable, el, false)) {
        if (currentChild === childNum) {
          return children[i];
        }
        currentChild++;
      }
      i++;
    }
    return null;
  }
  function lastChild(el, selector) {
    var last = el.lastElementChild;
    while (last && (last === Sortable.ghost || css(last, "display") === "none" || selector && !matches(last, selector))) {
      last = last.previousElementSibling;
    }
    return last || null;
  }
  function index(el, selector) {
    var index2 = 0;
    if (!el || !el.parentNode) {
      return -1;
    }
    while (el = el.previousElementSibling) {
      if (el.nodeName.toUpperCase() !== "TEMPLATE" && el !== Sortable.clone && (!selector || matches(el, selector))) {
        index2++;
      }
    }
    return index2;
  }
  function getRelativeScrollOffset(el) {
    var offsetLeft = 0, offsetTop = 0, winScroller = getWindowScrollingElement();
    if (el) {
      do {
        var elMatrix = matrix(el), scaleX = elMatrix.a, scaleY = elMatrix.d;
        offsetLeft += el.scrollLeft * scaleX;
        offsetTop += el.scrollTop * scaleY;
      } while (el !== winScroller && (el = el.parentNode));
    }
    return [offsetLeft, offsetTop];
  }
  function indexOfObject(arr, obj) {
    for (var i in arr) {
      if (!arr.hasOwnProperty(i))
        continue;
      for (var key in obj) {
        if (obj.hasOwnProperty(key) && obj[key] === arr[i][key])
          return Number(i);
      }
    }
    return -1;
  }
  function getParentAutoScrollElement(el, includeSelf) {
    if (!el || !el.getBoundingClientRect)
      return getWindowScrollingElement();
    var elem = el;
    var gotSelf = false;
    do {
      if (elem.clientWidth < elem.scrollWidth || elem.clientHeight < elem.scrollHeight) {
        var elemCSS = css(elem);
        if (elem.clientWidth < elem.scrollWidth && (elemCSS.overflowX == "auto" || elemCSS.overflowX == "scroll") || elem.clientHeight < elem.scrollHeight && (elemCSS.overflowY == "auto" || elemCSS.overflowY == "scroll")) {
          if (!elem.getBoundingClientRect || elem === document.body)
            return getWindowScrollingElement();
          if (gotSelf || includeSelf)
            return elem;
          gotSelf = true;
        }
      }
    } while (elem = elem.parentNode);
    return getWindowScrollingElement();
  }
  function extend(dst, src) {
    if (dst && src) {
      for (var key in src) {
        if (src.hasOwnProperty(key)) {
          dst[key] = src[key];
        }
      }
    }
    return dst;
  }
  function isRectEqual(rect1, rect2) {
    return Math.round(rect1.top) === Math.round(rect2.top) && Math.round(rect1.left) === Math.round(rect2.left) && Math.round(rect1.height) === Math.round(rect2.height) && Math.round(rect1.width) === Math.round(rect2.width);
  }
  var _throttleTimeout;
  function throttle(callback, ms) {
    return function() {
      if (!_throttleTimeout) {
        var args = arguments, _this = this;
        if (args.length === 1) {
          callback.call(_this, args[0]);
        } else {
          callback.apply(_this, args);
        }
        _throttleTimeout = setTimeout(function() {
          _throttleTimeout = void 0;
        }, ms);
      }
    };
  }
  function cancelThrottle() {
    clearTimeout(_throttleTimeout);
    _throttleTimeout = void 0;
  }
  function scrollBy(el, x, y) {
    el.scrollLeft += x;
    el.scrollTop += y;
  }
  function clone(el) {
    var Polymer = window.Polymer;
    var $ = window.jQuery || window.Zepto;
    if (Polymer && Polymer.dom) {
      return Polymer.dom(el).cloneNode(true);
    } else if ($) {
      return $(el).clone(true)[0];
    } else {
      return el.cloneNode(true);
    }
  }
  var expando = "Sortable" + (/* @__PURE__ */ new Date()).getTime();
  function AnimationStateManager() {
    var animationStates = [], animationCallbackId;
    return {
      captureAnimationState: function captureAnimationState() {
        animationStates = [];
        if (!this.options.animation)
          return;
        var children = [].slice.call(this.el.children);
        children.forEach(function(child) {
          if (css(child, "display") === "none" || child === Sortable.ghost)
            return;
          animationStates.push({
            target: child,
            rect: getRect(child)
          });
          var fromRect = _objectSpread2({}, animationStates[animationStates.length - 1].rect);
          if (child.thisAnimationDuration) {
            var childMatrix = matrix(child, true);
            if (childMatrix) {
              fromRect.top -= childMatrix.f;
              fromRect.left -= childMatrix.e;
            }
          }
          child.fromRect = fromRect;
        });
      },
      addAnimationState: function addAnimationState(state) {
        animationStates.push(state);
      },
      removeAnimationState: function removeAnimationState(target) {
        animationStates.splice(indexOfObject(animationStates, {
          target
        }), 1);
      },
      animateAll: function animateAll(callback) {
        var _this = this;
        if (!this.options.animation) {
          clearTimeout(animationCallbackId);
          if (typeof callback === "function")
            callback();
          return;
        }
        var animating = false, animationTime = 0;
        animationStates.forEach(function(state) {
          var time = 0, target = state.target, fromRect = target.fromRect, toRect = getRect(target), prevFromRect = target.prevFromRect, prevToRect = target.prevToRect, animatingRect = state.rect, targetMatrix = matrix(target, true);
          if (targetMatrix) {
            toRect.top -= targetMatrix.f;
            toRect.left -= targetMatrix.e;
          }
          target.toRect = toRect;
          if (target.thisAnimationDuration) {
            if (isRectEqual(prevFromRect, toRect) && !isRectEqual(fromRect, toRect) && // Make sure animatingRect is on line between toRect & fromRect
            (animatingRect.top - toRect.top) / (animatingRect.left - toRect.left) === (fromRect.top - toRect.top) / (fromRect.left - toRect.left)) {
              time = calculateRealTime(animatingRect, prevFromRect, prevToRect, _this.options);
            }
          }
          if (!isRectEqual(toRect, fromRect)) {
            target.prevFromRect = fromRect;
            target.prevToRect = toRect;
            if (!time) {
              time = _this.options.animation;
            }
            _this.animate(target, animatingRect, toRect, time);
          }
          if (time) {
            animating = true;
            animationTime = Math.max(animationTime, time);
            clearTimeout(target.animationResetTimer);
            target.animationResetTimer = setTimeout(function() {
              target.animationTime = 0;
              target.prevFromRect = null;
              target.fromRect = null;
              target.prevToRect = null;
              target.thisAnimationDuration = null;
            }, time);
            target.thisAnimationDuration = time;
          }
        });
        clearTimeout(animationCallbackId);
        if (!animating) {
          if (typeof callback === "function")
            callback();
        } else {
          animationCallbackId = setTimeout(function() {
            if (typeof callback === "function")
              callback();
          }, animationTime);
        }
        animationStates = [];
      },
      animate: function animate(target, currentRect, toRect, duration) {
        if (duration) {
          css(target, "transition", "");
          css(target, "transform", "");
          var elMatrix = matrix(this.el), scaleX = elMatrix && elMatrix.a, scaleY = elMatrix && elMatrix.d, translateX = (currentRect.left - toRect.left) / (scaleX || 1), translateY = (currentRect.top - toRect.top) / (scaleY || 1);
          target.animatingX = !!translateX;
          target.animatingY = !!translateY;
          css(target, "transform", "translate3d(" + translateX + "px," + translateY + "px,0)");
          this.forRepaintDummy = repaint(target);
          css(target, "transition", "transform " + duration + "ms" + (this.options.easing ? " " + this.options.easing : ""));
          css(target, "transform", "translate3d(0,0,0)");
          typeof target.animated === "number" && clearTimeout(target.animated);
          target.animated = setTimeout(function() {
            css(target, "transition", "");
            css(target, "transform", "");
            target.animated = false;
            target.animatingX = false;
            target.animatingY = false;
          }, duration);
        }
      }
    };
  }
  function repaint(target) {
    return target.offsetWidth;
  }
  function calculateRealTime(animatingRect, fromRect, toRect, options) {
    return Math.sqrt(Math.pow(fromRect.top - animatingRect.top, 2) + Math.pow(fromRect.left - animatingRect.left, 2)) / Math.sqrt(Math.pow(fromRect.top - toRect.top, 2) + Math.pow(fromRect.left - toRect.left, 2)) * options.animation;
  }
  var plugins = [];
  var defaults = {
    initializeByDefault: true
  };
  var PluginManager = {
    mount: function mount(plugin) {
      for (var option2 in defaults) {
        if (defaults.hasOwnProperty(option2) && !(option2 in plugin)) {
          plugin[option2] = defaults[option2];
        }
      }
      plugins.forEach(function(p) {
        if (p.pluginName === plugin.pluginName) {
          throw "Sortable: Cannot mount plugin ".concat(plugin.pluginName, " more than once");
        }
      });
      plugins.push(plugin);
    },
    pluginEvent: function pluginEvent(eventName, sortable, evt) {
      var _this = this;
      this.eventCanceled = false;
      evt.cancel = function() {
        _this.eventCanceled = true;
      };
      var eventNameGlobal = eventName + "Global";
      plugins.forEach(function(plugin) {
        if (!sortable[plugin.pluginName])
          return;
        if (sortable[plugin.pluginName][eventNameGlobal]) {
          sortable[plugin.pluginName][eventNameGlobal](_objectSpread2({
            sortable
          }, evt));
        }
        if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) {
          sortable[plugin.pluginName][eventName](_objectSpread2({
            sortable
          }, evt));
        }
      });
    },
    initializePlugins: function initializePlugins(sortable, el, defaults2, options) {
      plugins.forEach(function(plugin) {
        var pluginName = plugin.pluginName;
        if (!sortable.options[pluginName] && !plugin.initializeByDefault)
          return;
        var initialized = new plugin(sortable, el, sortable.options);
        initialized.sortable = sortable;
        initialized.options = sortable.options;
        sortable[pluginName] = initialized;
        _extends(defaults2, initialized.defaults);
      });
      for (var option2 in sortable.options) {
        if (!sortable.options.hasOwnProperty(option2))
          continue;
        var modified = this.modifyOption(sortable, option2, sortable.options[option2]);
        if (typeof modified !== "undefined") {
          sortable.options[option2] = modified;
        }
      }
    },
    getEventProperties: function getEventProperties(name, sortable) {
      var eventProperties = {};
      plugins.forEach(function(plugin) {
        if (typeof plugin.eventProperties !== "function")
          return;
        _extends(eventProperties, plugin.eventProperties.call(sortable[plugin.pluginName], name));
      });
      return eventProperties;
    },
    modifyOption: function modifyOption(sortable, name, value) {
      var modifiedValue;
      plugins.forEach(function(plugin) {
        if (!sortable[plugin.pluginName])
          return;
        if (plugin.optionListeners && typeof plugin.optionListeners[name] === "function") {
          modifiedValue = plugin.optionListeners[name].call(sortable[plugin.pluginName], value);
        }
      });
      return modifiedValue;
    }
  };
  function dispatchEvent(_ref) {
    var sortable = _ref.sortable, rootEl2 = _ref.rootEl, name = _ref.name, targetEl = _ref.targetEl, cloneEl2 = _ref.cloneEl, toEl = _ref.toEl, fromEl = _ref.fromEl, oldIndex2 = _ref.oldIndex, newIndex2 = _ref.newIndex, oldDraggableIndex2 = _ref.oldDraggableIndex, newDraggableIndex2 = _ref.newDraggableIndex, originalEvent = _ref.originalEvent, putSortable2 = _ref.putSortable, extraEventProperties = _ref.extraEventProperties;
    sortable = sortable || rootEl2 && rootEl2[expando];
    if (!sortable)
      return;
    var evt, options = sortable.options, onName = "on" + name.charAt(0).toUpperCase() + name.substr(1);
    if (window.CustomEvent && !IE11OrLess && !Edge) {
      evt = new CustomEvent(name, {
        bubbles: true,
        cancelable: true
      });
    } else {
      evt = document.createEvent("Event");
      evt.initEvent(name, true, true);
    }
    evt.to = toEl || rootEl2;
    evt.from = fromEl || rootEl2;
    evt.item = targetEl || rootEl2;
    evt.clone = cloneEl2;
    evt.oldIndex = oldIndex2;
    evt.newIndex = newIndex2;
    evt.oldDraggableIndex = oldDraggableIndex2;
    evt.newDraggableIndex = newDraggableIndex2;
    evt.originalEvent = originalEvent;
    evt.pullMode = putSortable2 ? putSortable2.lastPutMode : void 0;
    var allEventProperties = _objectSpread2(_objectSpread2({}, extraEventProperties), PluginManager.getEventProperties(name, sortable));
    for (var option2 in allEventProperties) {
      evt[option2] = allEventProperties[option2];
    }
    if (rootEl2) {
      rootEl2.dispatchEvent(evt);
    }
    if (options[onName]) {
      options[onName].call(sortable, evt);
    }
  }
  var _excluded = ["evt"];
  var pluginEvent2 = function pluginEvent3(eventName, sortable) {
    var _ref = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {}, originalEvent = _ref.evt, data = _objectWithoutProperties(_ref, _excluded);
    PluginManager.pluginEvent.bind(Sortable)(eventName, sortable, _objectSpread2({
      dragEl,
      parentEl,
      ghostEl,
      rootEl,
      nextEl,
      lastDownEl,
      cloneEl,
      cloneHidden,
      dragStarted: moved,
      putSortable,
      activeSortable: Sortable.active,
      originalEvent,
      oldIndex,
      oldDraggableIndex,
      newIndex,
      newDraggableIndex,
      hideGhostForTarget: _hideGhostForTarget,
      unhideGhostForTarget: _unhideGhostForTarget,
      cloneNowHidden: function cloneNowHidden() {
        cloneHidden = true;
      },
      cloneNowShown: function cloneNowShown() {
        cloneHidden = false;
      },
      dispatchSortableEvent: function dispatchSortableEvent(name) {
        _dispatchEvent({
          sortable,
          name,
          originalEvent
        });
      }
    }, data));
  };
  function _dispatchEvent(info) {
    dispatchEvent(_objectSpread2({
      putSortable,
      cloneEl,
      targetEl: dragEl,
      rootEl,
      oldIndex,
      oldDraggableIndex,
      newIndex,
      newDraggableIndex
    }, info));
  }
  var dragEl;
  var parentEl;
  var ghostEl;
  var rootEl;
  var nextEl;
  var lastDownEl;
  var cloneEl;
  var cloneHidden;
  var oldIndex;
  var newIndex;
  var oldDraggableIndex;
  var newDraggableIndex;
  var activeGroup;
  var putSortable;
  var awaitingDragStarted = false;
  var ignoreNextClick = false;
  var sortables = [];
  var tapEvt;
  var touchEvt;
  var lastDx;
  var lastDy;
  var tapDistanceLeft;
  var tapDistanceTop;
  var moved;
  var lastTarget;
  var lastDirection;
  var pastFirstInvertThresh = false;
  var isCircumstantialInvert = false;
  var targetMoveDistance;
  var ghostRelativeParent;
  var ghostRelativeParentInitialScroll = [];
  var _silent = false;
  var savedInputChecked = [];
  var documentExists = typeof document !== "undefined";
  var PositionGhostAbsolutely = IOS;
  var CSSFloatProperty = Edge || IE11OrLess ? "cssFloat" : "float";
  var supportDraggable = documentExists && !ChromeForAndroid && !IOS && "draggable" in document.createElement("div");
  var supportCssPointerEvents = function() {
    if (!documentExists)
      return;
    if (IE11OrLess) {
      return false;
    }
    var el = document.createElement("x");
    el.style.cssText = "pointer-events:auto";
    return el.style.pointerEvents === "auto";
  }();
  var _detectDirection = function _detectDirection2(el, options) {
    var elCSS = css(el), elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth), child1 = getChild(el, 0, options), child2 = getChild(el, 1, options), firstChildCSS = child1 && css(child1), secondChildCSS = child2 && css(child2), firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + getRect(child1).width, secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + getRect(child2).width;
    if (elCSS.display === "flex") {
      return elCSS.flexDirection === "column" || elCSS.flexDirection === "column-reverse" ? "vertical" : "horizontal";
    }
    if (elCSS.display === "grid") {
      return elCSS.gridTemplateColumns.split(" ").length <= 1 ? "vertical" : "horizontal";
    }
    if (child1 && firstChildCSS["float"] && firstChildCSS["float"] !== "none") {
      var touchingSideChild2 = firstChildCSS["float"] === "left" ? "left" : "right";
      return child2 && (secondChildCSS.clear === "both" || secondChildCSS.clear === touchingSideChild2) ? "vertical" : "horizontal";
    }
    return child1 && (firstChildCSS.display === "block" || firstChildCSS.display === "flex" || firstChildCSS.display === "table" || firstChildCSS.display === "grid" || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === "none" || child2 && elCSS[CSSFloatProperty] === "none" && firstChildWidth + secondChildWidth > elWidth) ? "vertical" : "horizontal";
  };
  var _dragElInRowColumn = function _dragElInRowColumn2(dragRect, targetRect, vertical) {
    var dragElS1Opp = vertical ? dragRect.left : dragRect.top, dragElS2Opp = vertical ? dragRect.right : dragRect.bottom, dragElOppLength = vertical ? dragRect.width : dragRect.height, targetS1Opp = vertical ? targetRect.left : targetRect.top, targetS2Opp = vertical ? targetRect.right : targetRect.bottom, targetOppLength = vertical ? targetRect.width : targetRect.height;
    return dragElS1Opp === targetS1Opp || dragElS2Opp === targetS2Opp || dragElS1Opp + dragElOppLength / 2 === targetS1Opp + targetOppLength / 2;
  };
  var _detectNearestEmptySortable = function _detectNearestEmptySortable2(x, y) {
    var ret;
    sortables.some(function(sortable) {
      var threshold = sortable[expando].options.emptyInsertThreshold;
      if (!threshold || lastChild(sortable))
        return;
      var rect = getRect(sortable), insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold, insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;
      if (insideHorizontally && insideVertically) {
        return ret = sortable;
      }
    });
    return ret;
  };
  var _prepareGroup = function _prepareGroup2(options) {
    function toFn(value, pull) {
      return function(to, from, dragEl2, evt) {
        var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;
        if (value == null && (pull || sameGroup)) {
          return true;
        } else if (value == null || value === false) {
          return false;
        } else if (pull && value === "clone") {
          return value;
        } else if (typeof value === "function") {
          return toFn(value(to, from, dragEl2, evt), pull)(to, from, dragEl2, evt);
        } else {
          var otherGroup = (pull ? to : from).options.group.name;
          return value === true || typeof value === "string" && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
        }
      };
    }
    var group = {};
    var originalGroup = options.group;
    if (!originalGroup || _typeof(originalGroup) != "object") {
      originalGroup = {
        name: originalGroup
      };
    }
    group.name = originalGroup.name;
    group.checkPull = toFn(originalGroup.pull, true);
    group.checkPut = toFn(originalGroup.put);
    group.revertClone = originalGroup.revertClone;
    options.group = group;
  };
  var _hideGhostForTarget = function _hideGhostForTarget2() {
    if (!supportCssPointerEvents && ghostEl) {
      css(ghostEl, "display", "none");
    }
  };
  var _unhideGhostForTarget = function _unhideGhostForTarget2() {
    if (!supportCssPointerEvents && ghostEl) {
      css(ghostEl, "display", "");
    }
  };
  if (documentExists && !ChromeForAndroid) {
    document.addEventListener("click", function(evt) {
      if (ignoreNextClick) {
        evt.preventDefault();
        evt.stopPropagation && evt.stopPropagation();
        evt.stopImmediatePropagation && evt.stopImmediatePropagation();
        ignoreNextClick = false;
        return false;
      }
    }, true);
  }
  var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent2(evt) {
    if (dragEl) {
      evt = evt.touches ? evt.touches[0] : evt;
      var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);
      if (nearest) {
        var event = {};
        for (var i in evt) {
          if (evt.hasOwnProperty(i)) {
            event[i] = evt[i];
          }
        }
        event.target = event.rootEl = nearest;
        event.preventDefault = void 0;
        event.stopPropagation = void 0;
        nearest[expando]._onDragOver(event);
      }
    }
  };
  var _checkOutsideTargetEl = function _checkOutsideTargetEl2(evt) {
    if (dragEl) {
      dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
    }
  };
  function Sortable(el, options) {
    if (!(el && el.nodeType && el.nodeType === 1)) {
      throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(el));
    }
    this.el = el;
    this.options = options = _extends({}, options);
    el[expando] = this;
    var defaults2 = {
      group: null,
      sort: true,
      disabled: false,
      store: null,
      handle: null,
      draggable: /^[uo]l$/i.test(el.nodeName) ? ">li" : ">*",
      swapThreshold: 1,
      // percentage; 0 <= x <= 1
      invertSwap: false,
      // invert always
      invertedSwapThreshold: null,
      // will be set to same as swapThreshold if default
      removeCloneOnHide: true,
      direction: function direction() {
        return _detectDirection(el, this.options);
      },
      ghostClass: "sortable-ghost",
      chosenClass: "sortable-chosen",
      dragClass: "sortable-drag",
      ignore: "a, img",
      filter: null,
      preventOnFilter: true,
      animation: 0,
      easing: null,
      setData: function setData(dataTransfer, dragEl2) {
        dataTransfer.setData("Text", dragEl2.textContent);
      },
      dropBubble: false,
      dragoverBubble: false,
      dataIdAttr: "data-id",
      delay: 0,
      delayOnTouchOnly: false,
      touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
      forceFallback: false,
      fallbackClass: "sortable-fallback",
      fallbackOnBody: false,
      fallbackTolerance: 0,
      fallbackOffset: {
        x: 0,
        y: 0
      },
      supportPointer: Sortable.supportPointer !== false && "PointerEvent" in window && !Safari,
      emptyInsertThreshold: 5
    };
    PluginManager.initializePlugins(this, el, defaults2);
    for (var name in defaults2) {
      !(name in options) && (options[name] = defaults2[name]);
    }
    _prepareGroup(options);
    for (var fn in this) {
      if (fn.charAt(0) === "_" && typeof this[fn] === "function") {
        this[fn] = this[fn].bind(this);
      }
    }
    this.nativeDraggable = options.forceFallback ? false : supportDraggable;
    if (this.nativeDraggable) {
      this.options.touchStartThreshold = 1;
    }
    if (options.supportPointer) {
      on(el, "pointerdown", this._onTapStart);
    } else {
      on(el, "mousedown", this._onTapStart);
      on(el, "touchstart", this._onTapStart);
    }
    if (this.nativeDraggable) {
      on(el, "dragover", this);
      on(el, "dragenter", this);
    }
    sortables.push(this.el);
    options.store && options.store.get && this.sort(options.store.get(this) || []);
    _extends(this, AnimationStateManager());
  }
  Sortable.prototype = /** @lends Sortable.prototype */
  {
    constructor: Sortable,
    _isOutsideThisEl: function _isOutsideThisEl(target) {
      if (!this.el.contains(target) && target !== this.el) {
        lastTarget = null;
      }
    },
    _getDirection: function _getDirection(evt, target) {
      return typeof this.options.direction === "function" ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
    },
    _onTapStart: function _onTapStart(evt) {
      if (!evt.cancelable)
        return;
      var _this = this, el = this.el, options = this.options, preventOnFilter = options.preventOnFilter, type = evt.type, touch = evt.touches && evt.touches[0] || evt.pointerType && evt.pointerType === "touch" && evt, target = (touch || evt).target, originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target, filter = options.filter;
      _saveInputCheckedState(el);
      if (dragEl) {
        return;
      }
      if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options.disabled) {
        return;
      }
      if (originalTarget.isContentEditable) {
        return;
      }
      if (!this.nativeDraggable && Safari && target && target.tagName.toUpperCase() === "SELECT") {
        return;
      }
      target = closest(target, options.draggable, el, false);
      if (target && target.animated) {
        return;
      }
      if (lastDownEl === target) {
        return;
      }
      oldIndex = index(target);
      oldDraggableIndex = index(target, options.draggable);
      if (typeof filter === "function") {
        if (filter.call(this, evt, target, this)) {
          _dispatchEvent({
            sortable: _this,
            rootEl: originalTarget,
            name: "filter",
            targetEl: target,
            toEl: el,
            fromEl: el
          });
          pluginEvent2("filter", _this, {
            evt
          });
          preventOnFilter && evt.cancelable && evt.preventDefault();
          return;
        }
      } else if (filter) {
        filter = filter.split(",").some(function(criteria) {
          criteria = closest(originalTarget, criteria.trim(), el, false);
          if (criteria) {
            _dispatchEvent({
              sortable: _this,
              rootEl: criteria,
              name: "filter",
              targetEl: target,
              fromEl: el,
              toEl: el
            });
            pluginEvent2("filter", _this, {
              evt
            });
            return true;
          }
        });
        if (filter) {
          preventOnFilter && evt.cancelable && evt.preventDefault();
          return;
        }
      }
      if (options.handle && !closest(originalTarget, options.handle, el, false)) {
        return;
      }
      this._prepareDragStart(evt, touch, target);
    },
    _prepareDragStart: function _prepareDragStart(evt, touch, target) {
      var _this = this, el = _this.el, options = _this.options, ownerDocument = el.ownerDocument, dragStartFn;
      if (target && !dragEl && target.parentNode === el) {
        var dragRect = getRect(target);
        rootEl = el;
        dragEl = target;
        parentEl = dragEl.parentNode;
        nextEl = dragEl.nextSibling;
        lastDownEl = target;
        activeGroup = options.group;
        Sortable.dragged = dragEl;
        tapEvt = {
          target: dragEl,
          clientX: (touch || evt).clientX,
          clientY: (touch || evt).clientY
        };
        tapDistanceLeft = tapEvt.clientX - dragRect.left;
        tapDistanceTop = tapEvt.clientY - dragRect.top;
        this._lastX = (touch || evt).clientX;
        this._lastY = (touch || evt).clientY;
        dragEl.style["will-change"] = "all";
        dragStartFn = function dragStartFn2() {
          pluginEvent2("delayEnded", _this, {
            evt
          });
          if (Sortable.eventCanceled) {
            _this._onDrop();
            return;
          }
          _this._disableDelayedDragEvents();
          if (!FireFox && _this.nativeDraggable) {
            dragEl.draggable = true;
          }
          _this._triggerDragStart(evt, touch);
          _dispatchEvent({
            sortable: _this,
            name: "choose",
            originalEvent: evt
          });
          toggleClass(dragEl, options.chosenClass, true);
        };
        options.ignore.split(",").forEach(function(criteria) {
          find(dragEl, criteria.trim(), _disableDraggable);
        });
        on(ownerDocument, "dragover", nearestEmptyInsertDetectEvent);
        on(ownerDocument, "mousemove", nearestEmptyInsertDetectEvent);
        on(ownerDocument, "touchmove", nearestEmptyInsertDetectEvent);
        on(ownerDocument, "mouseup", _this._onDrop);
        on(ownerDocument, "touchend", _this._onDrop);
        on(ownerDocument, "touchcancel", _this._onDrop);
        if (FireFox && this.nativeDraggable) {
          this.options.touchStartThreshold = 4;
          dragEl.draggable = true;
        }
        pluginEvent2("delayStart", this, {
          evt
        });
        if (options.delay && (!options.delayOnTouchOnly || touch) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
          if (Sortable.eventCanceled) {
            this._onDrop();
            return;
          }
          on(ownerDocument, "mouseup", _this._disableDelayedDrag);
          on(ownerDocument, "touchend", _this._disableDelayedDrag);
          on(ownerDocument, "touchcancel", _this._disableDelayedDrag);
          on(ownerDocument, "mousemove", _this._delayedDragTouchMoveHandler);
          on(ownerDocument, "touchmove", _this._delayedDragTouchMoveHandler);
          options.supportPointer && on(ownerDocument, "pointermove", _this._delayedDragTouchMoveHandler);
          _this._dragStartTimer = setTimeout(dragStartFn, options.delay);
        } else {
          dragStartFn();
        }
      }
    },
    _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(e) {
      var touch = e.touches ? e.touches[0] : e;
      if (Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) {
        this._disableDelayedDrag();
      }
    },
    _disableDelayedDrag: function _disableDelayedDrag() {
      dragEl && _disableDraggable(dragEl);
      clearTimeout(this._dragStartTimer);
      this._disableDelayedDragEvents();
    },
    _disableDelayedDragEvents: function _disableDelayedDragEvents() {
      var ownerDocument = this.el.ownerDocument;
      off(ownerDocument, "mouseup", this._disableDelayedDrag);
      off(ownerDocument, "touchend", this._disableDelayedDrag);
      off(ownerDocument, "touchcancel", this._disableDelayedDrag);
      off(ownerDocument, "mousemove", this._delayedDragTouchMoveHandler);
      off(ownerDocument, "touchmove", this._delayedDragTouchMoveHandler);
      off(ownerDocument, "pointermove", this._delayedDragTouchMoveHandler);
    },
    _triggerDragStart: function _triggerDragStart(evt, touch) {
      touch = touch || evt.pointerType == "touch" && evt;
      if (!this.nativeDraggable || touch) {
        if (this.options.supportPointer) {
          on(document, "pointermove", this._onTouchMove);
        } else if (touch) {
          on(document, "touchmove", this._onTouchMove);
        } else {
          on(document, "mousemove", this._onTouchMove);
        }
      } else {
        on(dragEl, "dragend", this);
        on(rootEl, "dragstart", this._onDragStart);
      }
      try {
        if (document.selection) {
          _nextTick(function() {
            document.selection.empty();
          });
        } else {
          window.getSelection().removeAllRanges();
        }
      } catch (err) {
      }
    },
    _dragStarted: function _dragStarted(fallback, evt) {
      awaitingDragStarted = false;
      if (rootEl && dragEl) {
        pluginEvent2("dragStarted", this, {
          evt
        });
        if (this.nativeDraggable) {
          on(document, "dragover", _checkOutsideTargetEl);
        }
        var options = this.options;
        !fallback && toggleClass(dragEl, options.dragClass, false);
        toggleClass(dragEl, options.ghostClass, true);
        Sortable.active = this;
        fallback && this._appendGhost();
        _dispatchEvent({
          sortable: this,
          name: "start",
          originalEvent: evt
        });
      } else {
        this._nulling();
      }
    },
    _emulateDragOver: function _emulateDragOver() {
      if (touchEvt) {
        this._lastX = touchEvt.clientX;
        this._lastY = touchEvt.clientY;
        _hideGhostForTarget();
        var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
        var parent = target;
        while (target && target.shadowRoot) {
          target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
          if (target === parent)
            break;
          parent = target;
        }
        dragEl.parentNode[expando]._isOutsideThisEl(target);
        if (parent) {
          do {
            if (parent[expando]) {
              var inserted = void 0;
              inserted = parent[expando]._onDragOver({
                clientX: touchEvt.clientX,
                clientY: touchEvt.clientY,
                target,
                rootEl: parent
              });
              if (inserted && !this.options.dragoverBubble) {
                break;
              }
            }
            target = parent;
          } while (parent = parent.parentNode);
        }
        _unhideGhostForTarget();
      }
    },
    _onTouchMove: function _onTouchMove(evt) {
      if (tapEvt) {
        var options = this.options, fallbackTolerance = options.fallbackTolerance, fallbackOffset = options.fallbackOffset, touch = evt.touches ? evt.touches[0] : evt, ghostMatrix = ghostEl && matrix(ghostEl, true), scaleX = ghostEl && ghostMatrix && ghostMatrix.a, scaleY = ghostEl && ghostMatrix && ghostMatrix.d, relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && getRelativeScrollOffset(ghostRelativeParent), dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1), dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1);
        if (!Sortable.active && !awaitingDragStarted) {
          if (fallbackTolerance && Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) < fallbackTolerance) {
            return;
          }
          this._onDragStart(evt, true);
        }
        if (ghostEl) {
          if (ghostMatrix) {
            ghostMatrix.e += dx - (lastDx || 0);
            ghostMatrix.f += dy - (lastDy || 0);
          } else {
            ghostMatrix = {
              a: 1,
              b: 0,
              c: 0,
              d: 1,
              e: dx,
              f: dy
            };
          }
          var cssMatrix = "matrix(".concat(ghostMatrix.a, ",").concat(ghostMatrix.b, ",").concat(ghostMatrix.c, ",").concat(ghostMatrix.d, ",").concat(ghostMatrix.e, ",").concat(ghostMatrix.f, ")");
          css(ghostEl, "webkitTransform", cssMatrix);
          css(ghostEl, "mozTransform", cssMatrix);
          css(ghostEl, "msTransform", cssMatrix);
          css(ghostEl, "transform", cssMatrix);
          lastDx = dx;
          lastDy = dy;
          touchEvt = touch;
        }
        evt.cancelable && evt.preventDefault();
      }
    },
    _appendGhost: function _appendGhost() {
      if (!ghostEl) {
        var container = this.options.fallbackOnBody ? document.body : rootEl, rect = getRect(dragEl, true, PositionGhostAbsolutely, true, container), options = this.options;
        if (PositionGhostAbsolutely) {
          ghostRelativeParent = container;
          while (css(ghostRelativeParent, "position") === "static" && css(ghostRelativeParent, "transform") === "none" && ghostRelativeParent !== document) {
            ghostRelativeParent = ghostRelativeParent.parentNode;
          }
          if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
            if (ghostRelativeParent === document)
              ghostRelativeParent = getWindowScrollingElement();
            rect.top += ghostRelativeParent.scrollTop;
            rect.left += ghostRelativeParent.scrollLeft;
          } else {
            ghostRelativeParent = getWindowScrollingElement();
          }
          ghostRelativeParentInitialScroll = getRelativeScrollOffset(ghostRelativeParent);
        }
        ghostEl = dragEl.cloneNode(true);
        toggleClass(ghostEl, options.ghostClass, false);
        toggleClass(ghostEl, options.fallbackClass, true);
        toggleClass(ghostEl, options.dragClass, true);
        css(ghostEl, "transition", "");
        css(ghostEl, "transform", "");
        css(ghostEl, "box-sizing", "border-box");
        css(ghostEl, "margin", 0);
        css(ghostEl, "top", rect.top);
        css(ghostEl, "left", rect.left);
        css(ghostEl, "width", rect.width);
        css(ghostEl, "height", rect.height);
        css(ghostEl, "opacity", "0.8");
        css(ghostEl, "position", PositionGhostAbsolutely ? "absolute" : "fixed");
        css(ghostEl, "zIndex", "100000");
        css(ghostEl, "pointerEvents", "none");
        Sortable.ghost = ghostEl;
        container.appendChild(ghostEl);
        css(ghostEl, "transform-origin", tapDistanceLeft / parseInt(ghostEl.style.width) * 100 + "% " + tapDistanceTop / parseInt(ghostEl.style.height) * 100 + "%");
      }
    },
    _onDragStart: function _onDragStart(evt, fallback) {
      var _this = this;
      var dataTransfer = evt.dataTransfer;
      var options = _this.options;
      pluginEvent2("dragStart", this, {
        evt
      });
      if (Sortable.eventCanceled) {
        this._onDrop();
        return;
      }
      pluginEvent2("setupClone", this);
      if (!Sortable.eventCanceled) {
        cloneEl = clone(dragEl);
        cloneEl.removeAttribute("id");
        cloneEl.draggable = false;
        cloneEl.style["will-change"] = "";
        this._hideClone();
        toggleClass(cloneEl, this.options.chosenClass, false);
        Sortable.clone = cloneEl;
      }
      _this.cloneId = _nextTick(function() {
        pluginEvent2("clone", _this);
        if (Sortable.eventCanceled)
          return;
        if (!_this.options.removeCloneOnHide) {
          rootEl.insertBefore(cloneEl, dragEl);
        }
        _this._hideClone();
        _dispatchEvent({
          sortable: _this,
          name: "clone"
        });
      });
      !fallback && toggleClass(dragEl, options.dragClass, true);
      if (fallback) {
        ignoreNextClick = true;
        _this._loopId = setInterval(_this._emulateDragOver, 50);
      } else {
        off(document, "mouseup", _this._onDrop);
        off(document, "touchend", _this._onDrop);
        off(document, "touchcancel", _this._onDrop);
        if (dataTransfer) {
          dataTransfer.effectAllowed = "move";
          options.setData && options.setData.call(_this, dataTransfer, dragEl);
        }
        on(document, "drop", _this);
        css(dragEl, "transform", "translateZ(0)");
      }
      awaitingDragStarted = true;
      _this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));
      on(document, "selectstart", _this);
      moved = true;
      if (Safari) {
        css(document.body, "user-select", "none");
      }
    },
    // Returns true - if no further action is needed (either inserted or another condition)
    _onDragOver: function _onDragOver(evt) {
      var el = this.el, target = evt.target, dragRect, targetRect, revert, options = this.options, group = options.group, activeSortable = Sortable.active, isOwner = activeGroup === group, canSort = options.sort, fromSortable = putSortable || activeSortable, vertical, _this = this, completedFired = false;
      if (_silent)
        return;
      function dragOverEvent(name, extra) {
        pluginEvent2(name, _this, _objectSpread2({
          evt,
          isOwner,
          axis: vertical ? "vertical" : "horizontal",
          revert,
          dragRect,
          targetRect,
          canSort,
          fromSortable,
          target,
          completed,
          onMove: function onMove(target2, after2) {
            return _onMove(rootEl, el, dragEl, dragRect, target2, getRect(target2), evt, after2);
          },
          changed
        }, extra));
      }
      function capture() {
        dragOverEvent("dragOverAnimationCapture");
        _this.captureAnimationState();
        if (_this !== fromSortable) {
          fromSortable.captureAnimationState();
        }
      }
      function completed(insertion) {
        dragOverEvent("dragOverCompleted", {
          insertion
        });
        if (insertion) {
          if (isOwner) {
            activeSortable._hideClone();
          } else {
            activeSortable._showClone(_this);
          }
          if (_this !== fromSortable) {
            toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);
            toggleClass(dragEl, options.ghostClass, true);
          }
          if (putSortable !== _this && _this !== Sortable.active) {
            putSortable = _this;
          } else if (_this === Sortable.active && putSortable) {
            putSortable = null;
          }
          if (fromSortable === _this) {
            _this._ignoreWhileAnimating = target;
          }
          _this.animateAll(function() {
            dragOverEvent("dragOverAnimationComplete");
            _this._ignoreWhileAnimating = null;
          });
          if (_this !== fromSortable) {
            fromSortable.animateAll();
            fromSortable._ignoreWhileAnimating = null;
          }
        }
        if (target === dragEl && !dragEl.animated || target === el && !target.animated) {
          lastTarget = null;
        }
        if (!options.dragoverBubble && !evt.rootEl && target !== document) {
          dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
          !insertion && nearestEmptyInsertDetectEvent(evt);
        }
        !options.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
        return completedFired = true;
      }
      function changed() {
        newIndex = index(dragEl);
        newDraggableIndex = index(dragEl, options.draggable);
        _dispatchEvent({
          sortable: _this,
          name: "change",
          toEl: el,
          newIndex,
          newDraggableIndex,
          originalEvent: evt
        });
      }
      if (evt.preventDefault !== void 0) {
        evt.cancelable && evt.preventDefault();
      }
      target = closest(target, options.draggable, el, true);
      dragOverEvent("dragOver");
      if (Sortable.eventCanceled)
        return completedFired;
      if (dragEl.contains(evt.target) || target.animated && target.animatingX && target.animatingY || _this._ignoreWhileAnimating === target) {
        return completed(false);
      }
      ignoreNextClick = false;
      if (activeSortable && !options.disabled && (isOwner ? canSort || (revert = parentEl !== rootEl) : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
        vertical = this._getDirection(evt, target) === "vertical";
        dragRect = getRect(dragEl);
        dragOverEvent("dragOverValid");
        if (Sortable.eventCanceled)
          return completedFired;
        if (revert) {
          parentEl = rootEl;
          capture();
          this._hideClone();
          dragOverEvent("revert");
          if (!Sortable.eventCanceled) {
            if (nextEl) {
              rootEl.insertBefore(dragEl, nextEl);
            } else {
              rootEl.appendChild(dragEl);
            }
          }
          return completed(true);
        }
        var elLastChild = lastChild(el, options.draggable);
        if (!elLastChild || _ghostIsLast(evt, vertical, this) && !elLastChild.animated) {
          if (elLastChild === dragEl) {
            return completed(false);
          }
          if (elLastChild && el === evt.target) {
            target = elLastChild;
          }
          if (target) {
            targetRect = getRect(target);
          }
          if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
            capture();
            if (elLastChild && elLastChild.nextSibling) {
              el.insertBefore(dragEl, elLastChild.nextSibling);
            } else {
              el.appendChild(dragEl);
            }
            parentEl = el;
            changed();
            return completed(true);
          }
        } else if (elLastChild && _ghostIsFirst(evt, vertical, this)) {
          var firstChild = getChild(el, 0, options, true);
          if (firstChild === dragEl) {
            return completed(false);
          }
          target = firstChild;
          targetRect = getRect(target);
          if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, false) !== false) {
            capture();
            el.insertBefore(dragEl, firstChild);
            parentEl = el;
            changed();
            return completed(true);
          }
        } else if (target.parentNode === el) {
          targetRect = getRect(target);
          var direction = 0, targetBeforeFirstSwap, differentLevel = dragEl.parentNode !== el, differentRowCol = !_dragElInRowColumn(dragEl.animated && dragEl.toRect || dragRect, target.animated && target.toRect || targetRect, vertical), side1 = vertical ? "top" : "left", scrolledPastTop = isScrolledPast(target, "top", "top") || isScrolledPast(dragEl, "top", "top"), scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;
          if (lastTarget !== target) {
            targetBeforeFirstSwap = targetRect[side1];
            pastFirstInvertThresh = false;
            isCircumstantialInvert = !differentRowCol && options.invertSwap || differentLevel;
          }
          direction = _getSwapDirection(evt, target, targetRect, vertical, differentRowCol ? 1 : options.swapThreshold, options.invertedSwapThreshold == null ? options.swapThreshold : options.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
          var sibling;
          if (direction !== 0) {
            var dragIndex = index(dragEl);
            do {
              dragIndex -= direction;
              sibling = parentEl.children[dragIndex];
            } while (sibling && (css(sibling, "display") === "none" || sibling === ghostEl));
          }
          if (direction === 0 || sibling === target) {
            return completed(false);
          }
          lastTarget = target;
          lastDirection = direction;
          var nextSibling = target.nextElementSibling, after = false;
          after = direction === 1;
          var moveVector = _onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, after);
          if (moveVector !== false) {
            if (moveVector === 1 || moveVector === -1) {
              after = moveVector === 1;
            }
            _silent = true;
            setTimeout(_unsilent, 30);
            capture();
            if (after && !nextSibling) {
              el.appendChild(dragEl);
            } else {
              target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
            }
            if (scrolledPastTop) {
              scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
            }
            parentEl = dragEl.parentNode;
            if (targetBeforeFirstSwap !== void 0 && !isCircumstantialInvert) {
              targetMoveDistance = Math.abs(targetBeforeFirstSwap - getRect(target)[side1]);
            }
            changed();
            return completed(true);
          }
        }
        if (el.contains(dragEl)) {
          return completed(false);
        }
      }
      return false;
    },
    _ignoreWhileAnimating: null,
    _offMoveEvents: function _offMoveEvents() {
      off(document, "mousemove", this._onTouchMove);
      off(document, "touchmove", this._onTouchMove);
      off(document, "pointermove", this._onTouchMove);
      off(document, "dragover", nearestEmptyInsertDetectEvent);
      off(document, "mousemove", nearestEmptyInsertDetectEvent);
      off(document, "touchmove", nearestEmptyInsertDetectEvent);
    },
    _offUpEvents: function _offUpEvents() {
      var ownerDocument = this.el.ownerDocument;
      off(ownerDocument, "mouseup", this._onDrop);
      off(ownerDocument, "touchend", this._onDrop);
      off(ownerDocument, "pointerup", this._onDrop);
      off(ownerDocument, "touchcancel", this._onDrop);
      off(document, "selectstart", this);
    },
    _onDrop: function _onDrop(evt) {
      var el = this.el, options = this.options;
      newIndex = index(dragEl);
      newDraggableIndex = index(dragEl, options.draggable);
      pluginEvent2("drop", this, {
        evt
      });
      parentEl = dragEl && dragEl.parentNode;
      newIndex = index(dragEl);
      newDraggableIndex = index(dragEl, options.draggable);
      if (Sortable.eventCanceled) {
        this._nulling();
        return;
      }
      awaitingDragStarted = false;
      isCircumstantialInvert = false;
      pastFirstInvertThresh = false;
      clearInterval(this._loopId);
      clearTimeout(this._dragStartTimer);
      _cancelNextTick(this.cloneId);
      _cancelNextTick(this._dragStartId);
      if (this.nativeDraggable) {
        off(document, "drop", this);
        off(el, "dragstart", this._onDragStart);
      }
      this._offMoveEvents();
      this._offUpEvents();
      if (Safari) {
        css(document.body, "user-select", "");
      }
      css(dragEl, "transform", "");
      if (evt) {
        if (moved) {
          evt.cancelable && evt.preventDefault();
          !options.dropBubble && evt.stopPropagation();
        }
        ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);
        if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== "clone") {
          cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
        }
        if (dragEl) {
          if (this.nativeDraggable) {
            off(dragEl, "dragend", this);
          }
          _disableDraggable(dragEl);
          dragEl.style["will-change"] = "";
          if (moved && !awaitingDragStarted) {
            toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);
          }
          toggleClass(dragEl, this.options.chosenClass, false);
          _dispatchEvent({
            sortable: this,
            name: "unchoose",
            toEl: parentEl,
            newIndex: null,
            newDraggableIndex: null,
            originalEvent: evt
          });
          if (rootEl !== parentEl) {
            if (newIndex >= 0) {
              _dispatchEvent({
                rootEl: parentEl,
                name: "add",
                toEl: parentEl,
                fromEl: rootEl,
                originalEvent: evt
              });
              _dispatchEvent({
                sortable: this,
                name: "remove",
                toEl: parentEl,
                originalEvent: evt
              });
              _dispatchEvent({
                rootEl: parentEl,
                name: "sort",
                toEl: parentEl,
                fromEl: rootEl,
                originalEvent: evt
              });
              _dispatchEvent({
                sortable: this,
                name: "sort",
                toEl: parentEl,
                originalEvent: evt
              });
            }
            putSortable && putSortable.save();
          } else {
            if (newIndex !== oldIndex) {
              if (newIndex >= 0) {
                _dispatchEvent({
                  sortable: this,
                  name: "update",
                  toEl: parentEl,
                  originalEvent: evt
                });
                _dispatchEvent({
                  sortable: this,
                  name: "sort",
                  toEl: parentEl,
                  originalEvent: evt
                });
              }
            }
          }
          if (Sortable.active) {
            if (newIndex == null || newIndex === -1) {
              newIndex = oldIndex;
              newDraggableIndex = oldDraggableIndex;
            }
            _dispatchEvent({
              sortable: this,
              name: "end",
              toEl: parentEl,
              originalEvent: evt
            });
            this.save();
          }
        }
      }
      this._nulling();
    },
    _nulling: function _nulling() {
      pluginEvent2("nulling", this);
      rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = cloneHidden = tapEvt = touchEvt = moved = newIndex = newDraggableIndex = oldIndex = oldDraggableIndex = lastTarget = lastDirection = putSortable = activeGroup = Sortable.dragged = Sortable.ghost = Sortable.clone = Sortable.active = null;
      savedInputChecked.forEach(function(el) {
        el.checked = true;
      });
      savedInputChecked.length = lastDx = lastDy = 0;
    },
    handleEvent: function handleEvent(evt) {
      switch (evt.type) {
        case "drop":
        case "dragend":
          this._onDrop(evt);
          break;
        case "dragenter":
        case "dragover":
          if (dragEl) {
            this._onDragOver(evt);
            _globalDragOver(evt);
          }
          break;
        case "selectstart":
          evt.preventDefault();
          break;
      }
    },
    /**
     * Serializes the item into an array of string.
     * @returns {String[]}
     */
    toArray: function toArray() {
      var order = [], el, children = this.el.children, i = 0, n = children.length, options = this.options;
      for (; i < n; i++) {
        el = children[i];
        if (closest(el, options.draggable, this.el, false)) {
          order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
        }
      }
      return order;
    },
    /**
     * Sorts the elements according to the array.
     * @param  {String[]}  order  order of the items
     */
    sort: function sort(order, useAnimation) {
      var items = {}, rootEl2 = this.el;
      this.toArray().forEach(function(id, i) {
        var el = rootEl2.children[i];
        if (closest(el, this.options.draggable, rootEl2, false)) {
          items[id] = el;
        }
      }, this);
      useAnimation && this.captureAnimationState();
      order.forEach(function(id) {
        if (items[id]) {
          rootEl2.removeChild(items[id]);
          rootEl2.appendChild(items[id]);
        }
      });
      useAnimation && this.animateAll();
    },
    /**
     * Save the current sorting
     */
    save: function save() {
      var store = this.options.store;
      store && store.set && store.set(this);
    },
    /**
     * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
     * @param   {HTMLElement}  el
     * @param   {String}       [selector]  default: `options.draggable`
     * @returns {HTMLElement|null}
     */
    closest: function closest$1(el, selector) {
      return closest(el, selector || this.options.draggable, this.el, false);
    },
    /**
     * Set/get option
     * @param   {string} name
     * @param   {*}      [value]
     * @returns {*}
     */
    option: function option(name, value) {
      var options = this.options;
      if (value === void 0) {
        return options[name];
      } else {
        var modifiedValue = PluginManager.modifyOption(this, name, value);
        if (typeof modifiedValue !== "undefined") {
          options[name] = modifiedValue;
        } else {
          options[name] = value;
        }
        if (name === "group") {
          _prepareGroup(options);
        }
      }
    },
    /**
     * Destroy
     */
    destroy: function destroy() {
      pluginEvent2("destroy", this);
      var el = this.el;
      el[expando] = null;
      off(el, "mousedown", this._onTapStart);
      off(el, "touchstart", this._onTapStart);
      off(el, "pointerdown", this._onTapStart);
      if (this.nativeDraggable) {
        off(el, "dragover", this);
        off(el, "dragenter", this);
      }
      Array.prototype.forEach.call(el.querySelectorAll("[draggable]"), function(el2) {
        el2.removeAttribute("draggable");
      });
      this._onDrop();
      this._disableDelayedDragEvents();
      sortables.splice(sortables.indexOf(this.el), 1);
      this.el = el = null;
    },
    _hideClone: function _hideClone() {
      if (!cloneHidden) {
        pluginEvent2("hideClone", this);
        if (Sortable.eventCanceled)
          return;
        css(cloneEl, "display", "none");
        if (this.options.removeCloneOnHide && cloneEl.parentNode) {
          cloneEl.parentNode.removeChild(cloneEl);
        }
        cloneHidden = true;
      }
    },
    _showClone: function _showClone(putSortable2) {
      if (putSortable2.lastPutMode !== "clone") {
        this._hideClone();
        return;
      }
      if (cloneHidden) {
        pluginEvent2("showClone", this);
        if (Sortable.eventCanceled)
          return;
        if (dragEl.parentNode == rootEl && !this.options.group.revertClone) {
          rootEl.insertBefore(cloneEl, dragEl);
        } else if (nextEl) {
          rootEl.insertBefore(cloneEl, nextEl);
        } else {
          rootEl.appendChild(cloneEl);
        }
        if (this.options.group.revertClone) {
          this.animate(dragEl, cloneEl);
        }
        css(cloneEl, "display", "");
        cloneHidden = false;
      }
    }
  };
  function _globalDragOver(evt) {
    if (evt.dataTransfer) {
      evt.dataTransfer.dropEffect = "move";
    }
    evt.cancelable && evt.preventDefault();
  }
  function _onMove(fromEl, toEl, dragEl2, dragRect, targetEl, targetRect, originalEvent, willInsertAfter) {
    var evt, sortable = fromEl[expando], onMoveFn = sortable.options.onMove, retVal;
    if (window.CustomEvent && !IE11OrLess && !Edge) {
      evt = new CustomEvent("move", {
        bubbles: true,
        cancelable: true
      });
    } else {
      evt = document.createEvent("Event");
      evt.initEvent("move", true, true);
    }
    evt.to = toEl;
    evt.from = fromEl;
    evt.dragged = dragEl2;
    evt.draggedRect = dragRect;
    evt.related = targetEl || toEl;
    evt.relatedRect = targetRect || getRect(toEl);
    evt.willInsertAfter = willInsertAfter;
    evt.originalEvent = originalEvent;
    fromEl.dispatchEvent(evt);
    if (onMoveFn) {
      retVal = onMoveFn.call(sortable, evt, originalEvent);
    }
    return retVal;
  }
  function _disableDraggable(el) {
    el.draggable = false;
  }
  function _unsilent() {
    _silent = false;
  }
  function _ghostIsFirst(evt, vertical, sortable) {
    var rect = getRect(getChild(sortable.el, 0, sortable.options, true));
    var spacer = 10;
    return vertical ? evt.clientX < rect.left - spacer || evt.clientY < rect.top && evt.clientX < rect.right : evt.clientY < rect.top - spacer || evt.clientY < rect.bottom && evt.clientX < rect.left;
  }
  function _ghostIsLast(evt, vertical, sortable) {
    var rect = getRect(lastChild(sortable.el, sortable.options.draggable));
    var spacer = 10;
    return vertical ? evt.clientX > rect.right + spacer || evt.clientX <= rect.right && evt.clientY > rect.bottom && evt.clientX >= rect.left : evt.clientX > rect.right && evt.clientY > rect.top || evt.clientX <= rect.right && evt.clientY > rect.bottom + spacer;
  }
  function _getSwapDirection(evt, target, targetRect, vertical, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
    var mouseOnAxis = vertical ? evt.clientY : evt.clientX, targetLength = vertical ? targetRect.height : targetRect.width, targetS1 = vertical ? targetRect.top : targetRect.left, targetS2 = vertical ? targetRect.bottom : targetRect.right, invert = false;
    if (!invertSwap) {
      if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
        if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) {
          pastFirstInvertThresh = true;
        }
        if (!pastFirstInvertThresh) {
          if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance : mouseOnAxis > targetS2 - targetMoveDistance) {
            return -lastDirection;
          }
        } else {
          invert = true;
        }
      } else {
        if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) {
          return _getInsertDirection(target);
        }
      }
    }
    invert = invert || invertSwap;
    if (invert) {
      if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) {
        return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
      }
    }
    return 0;
  }
  function _getInsertDirection(target) {
    if (index(dragEl) < index(target)) {
      return 1;
    } else {
      return -1;
    }
  }
  function _generateId(el) {
    var str = el.tagName + el.className + el.src + el.href + el.textContent, i = str.length, sum = 0;
    while (i--) {
      sum += str.charCodeAt(i);
    }
    return sum.toString(36);
  }
  function _saveInputCheckedState(root) {
    savedInputChecked.length = 0;
    var inputs = root.getElementsByTagName("input");
    var idx = inputs.length;
    while (idx--) {
      var el = inputs[idx];
      el.checked && savedInputChecked.push(el);
    }
  }
  function _nextTick(fn) {
    return setTimeout(fn, 0);
  }
  function _cancelNextTick(id) {
    return clearTimeout(id);
  }
  if (documentExists) {
    on(document, "touchmove", function(evt) {
      if ((Sortable.active || awaitingDragStarted) && evt.cancelable) {
        evt.preventDefault();
      }
    });
  }
  Sortable.utils = {
    on,
    off,
    css,
    find,
    is: function is(el, selector) {
      return !!closest(el, selector, el, false);
    },
    extend,
    throttle,
    closest,
    toggleClass,
    clone,
    index,
    nextTick: _nextTick,
    cancelNextTick: _cancelNextTick,
    detectDirection: _detectDirection,
    getChild
  };
  Sortable.get = function(element) {
    return element[expando];
  };
  Sortable.mount = function() {
    for (var _len = arguments.length, plugins2 = new Array(_len), _key = 0; _key < _len; _key++) {
      plugins2[_key] = arguments[_key];
    }
    if (plugins2[0].constructor === Array)
      plugins2 = plugins2[0];
    plugins2.forEach(function(plugin) {
      if (!plugin.prototype || !plugin.prototype.constructor) {
        throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
      }
      if (plugin.utils)
        Sortable.utils = _objectSpread2(_objectSpread2({}, Sortable.utils), plugin.utils);
      PluginManager.mount(plugin);
    });
  };
  Sortable.create = function(el, options) {
    return new Sortable(el, options);
  };
  Sortable.version = version;
  var autoScrolls = [];
  var scrollEl;
  var scrollRootEl;
  var scrolling = false;
  var lastAutoScrollX;
  var lastAutoScrollY;
  var touchEvt$1;
  var pointerElemChangedInterval;
  function AutoScrollPlugin() {
    function AutoScroll() {
      this.defaults = {
        scroll: true,
        forceAutoScrollFallback: false,
        scrollSensitivity: 30,
        scrollSpeed: 10,
        bubbleScroll: true
      };
      for (var fn in this) {
        if (fn.charAt(0) === "_" && typeof this[fn] === "function") {
          this[fn] = this[fn].bind(this);
        }
      }
    }
    AutoScroll.prototype = {
      dragStarted: function dragStarted(_ref) {
        var originalEvent = _ref.originalEvent;
        if (this.sortable.nativeDraggable) {
          on(document, "dragover", this._handleAutoScroll);
        } else {
          if (this.options.supportPointer) {
            on(document, "pointermove", this._handleFallbackAutoScroll);
          } else if (originalEvent.touches) {
            on(document, "touchmove", this._handleFallbackAutoScroll);
          } else {
            on(document, "mousemove", this._handleFallbackAutoScroll);
          }
        }
      },
      dragOverCompleted: function dragOverCompleted(_ref2) {
        var originalEvent = _ref2.originalEvent;
        if (!this.options.dragOverBubble && !originalEvent.rootEl) {
          this._handleAutoScroll(originalEvent);
        }
      },
      drop: function drop3() {
        if (this.sortable.nativeDraggable) {
          off(document, "dragover", this._handleAutoScroll);
        } else {
          off(document, "pointermove", this._handleFallbackAutoScroll);
          off(document, "touchmove", this._handleFallbackAutoScroll);
          off(document, "mousemove", this._handleFallbackAutoScroll);
        }
        clearPointerElemChangedInterval();
        clearAutoScrolls();
        cancelThrottle();
      },
      nulling: function nulling() {
        touchEvt$1 = scrollRootEl = scrollEl = scrolling = pointerElemChangedInterval = lastAutoScrollX = lastAutoScrollY = null;
        autoScrolls.length = 0;
      },
      _handleFallbackAutoScroll: function _handleFallbackAutoScroll(evt) {
        this._handleAutoScroll(evt, true);
      },
      _handleAutoScroll: function _handleAutoScroll(evt, fallback) {
        var _this = this;
        var x = (evt.touches ? evt.touches[0] : evt).clientX, y = (evt.touches ? evt.touches[0] : evt).clientY, elem = document.elementFromPoint(x, y);
        touchEvt$1 = evt;
        if (fallback || this.options.forceAutoScrollFallback || Edge || IE11OrLess || Safari) {
          autoScroll(evt, this.options, elem, fallback);
          var ogElemScroller = getParentAutoScrollElement(elem, true);
          if (scrolling && (!pointerElemChangedInterval || x !== lastAutoScrollX || y !== lastAutoScrollY)) {
            pointerElemChangedInterval && clearPointerElemChangedInterval();
            pointerElemChangedInterval = setInterval(function() {
              var newElem = getParentAutoScrollElement(document.elementFromPoint(x, y), true);
              if (newElem !== ogElemScroller) {
                ogElemScroller = newElem;
                clearAutoScrolls();
              }
              autoScroll(evt, _this.options, newElem, fallback);
            }, 10);
            lastAutoScrollX = x;
            lastAutoScrollY = y;
          }
        } else {
          if (!this.options.bubbleScroll || getParentAutoScrollElement(elem, true) === getWindowScrollingElement()) {
            clearAutoScrolls();
            return;
          }
          autoScroll(evt, this.options, getParentAutoScrollElement(elem, false), false);
        }
      }
    };
    return _extends(AutoScroll, {
      pluginName: "scroll",
      initializeByDefault: true
    });
  }
  function clearAutoScrolls() {
    autoScrolls.forEach(function(autoScroll2) {
      clearInterval(autoScroll2.pid);
    });
    autoScrolls = [];
  }
  function clearPointerElemChangedInterval() {
    clearInterval(pointerElemChangedInterval);
  }
  var autoScroll = throttle(function(evt, options, rootEl2, isFallback) {
    if (!options.scroll)
      return;
    var x = (evt.touches ? evt.touches[0] : evt).clientX, y = (evt.touches ? evt.touches[0] : evt).clientY, sens = options.scrollSensitivity, speed = options.scrollSpeed, winScroller = getWindowScrollingElement();
    var scrollThisInstance = false, scrollCustomFn;
    if (scrollRootEl !== rootEl2) {
      scrollRootEl = rootEl2;
      clearAutoScrolls();
      scrollEl = options.scroll;
      scrollCustomFn = options.scrollFn;
      if (scrollEl === true) {
        scrollEl = getParentAutoScrollElement(rootEl2, true);
      }
    }
    var layersOut = 0;
    var currentParent = scrollEl;
    do {
      var el = currentParent, rect = getRect(el), top = rect.top, bottom = rect.bottom, left = rect.left, right = rect.right, width = rect.width, height = rect.height, canScrollX = void 0, canScrollY = void 0, scrollWidth = el.scrollWidth, scrollHeight = el.scrollHeight, elCSS = css(el), scrollPosX = el.scrollLeft, scrollPosY = el.scrollTop;
      if (el === winScroller) {
        canScrollX = width < scrollWidth && (elCSS.overflowX === "auto" || elCSS.overflowX === "scroll" || elCSS.overflowX === "visible");
        canScrollY = height < scrollHeight && (elCSS.overflowY === "auto" || elCSS.overflowY === "scroll" || elCSS.overflowY === "visible");
      } else {
        canScrollX = width < scrollWidth && (elCSS.overflowX === "auto" || elCSS.overflowX === "scroll");
        canScrollY = height < scrollHeight && (elCSS.overflowY === "auto" || elCSS.overflowY === "scroll");
      }
      var vx = canScrollX && (Math.abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (Math.abs(left - x) <= sens && !!scrollPosX);
      var vy = canScrollY && (Math.abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (Math.abs(top - y) <= sens && !!scrollPosY);
      if (!autoScrolls[layersOut]) {
        for (var i = 0; i <= layersOut; i++) {
          if (!autoScrolls[i]) {
            autoScrolls[i] = {};
          }
        }
      }
      if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el) {
        autoScrolls[layersOut].el = el;
        autoScrolls[layersOut].vx = vx;
        autoScrolls[layersOut].vy = vy;
        clearInterval(autoScrolls[layersOut].pid);
        if (vx != 0 || vy != 0) {
          scrollThisInstance = true;
          autoScrolls[layersOut].pid = setInterval(function() {
            if (isFallback && this.layer === 0) {
              Sortable.active._onTouchMove(touchEvt$1);
            }
            var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
            var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;
            if (typeof scrollCustomFn === "function") {
              if (scrollCustomFn.call(Sortable.dragged.parentNode[expando], scrollOffsetX, scrollOffsetY, evt, touchEvt$1, autoScrolls[this.layer].el) !== "continue") {
                return;
              }
            }
            scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
          }.bind({
            layer: layersOut
          }), 24);
        }
      }
      layersOut++;
    } while (options.bubbleScroll && currentParent !== winScroller && (currentParent = getParentAutoScrollElement(currentParent, false)));
    scrolling = scrollThisInstance;
  }, 30);
  var drop = function drop2(_ref) {
    var originalEvent = _ref.originalEvent, putSortable2 = _ref.putSortable, dragEl2 = _ref.dragEl, activeSortable = _ref.activeSortable, dispatchSortableEvent = _ref.dispatchSortableEvent, hideGhostForTarget = _ref.hideGhostForTarget, unhideGhostForTarget = _ref.unhideGhostForTarget;
    if (!originalEvent)
      return;
    var toSortable = putSortable2 || activeSortable;
    hideGhostForTarget();
    var touch = originalEvent.changedTouches && originalEvent.changedTouches.length ? originalEvent.changedTouches[0] : originalEvent;
    var target = document.elementFromPoint(touch.clientX, touch.clientY);
    unhideGhostForTarget();
    if (toSortable && !toSortable.el.contains(target)) {
      dispatchSortableEvent("spill");
      this.onSpill({
        dragEl: dragEl2,
        putSortable: putSortable2
      });
    }
  };
  function Revert() {
  }
  Revert.prototype = {
    startIndex: null,
    dragStart: function dragStart(_ref2) {
      var oldDraggableIndex2 = _ref2.oldDraggableIndex;
      this.startIndex = oldDraggableIndex2;
    },
    onSpill: function onSpill(_ref3) {
      var dragEl2 = _ref3.dragEl, putSortable2 = _ref3.putSortable;
      this.sortable.captureAnimationState();
      if (putSortable2) {
        putSortable2.captureAnimationState();
      }
      var nextSibling = getChild(this.sortable.el, this.startIndex, this.options);
      if (nextSibling) {
        this.sortable.el.insertBefore(dragEl2, nextSibling);
      } else {
        this.sortable.el.appendChild(dragEl2);
      }
      this.sortable.animateAll();
      if (putSortable2) {
        putSortable2.animateAll();
      }
    },
    drop
  };
  _extends(Revert, {
    pluginName: "revertOnSpill"
  });
  function Remove() {
  }
  Remove.prototype = {
    onSpill: function onSpill2(_ref4) {
      var dragEl2 = _ref4.dragEl, putSortable2 = _ref4.putSortable;
      var parentSortable = putSortable2 || this.sortable;
      parentSortable.captureAnimationState();
      dragEl2.parentNode && dragEl2.parentNode.removeChild(dragEl2);
      parentSortable.animateAll();
    },
    drop
  };
  _extends(Remove, {
    pluginName: "removeOnSpill"
  });
  Sortable.mount(new AutoScrollPlugin());
  Sortable.mount(Remove, Revert);
  var sortable_esm_default = Sortable;

  // packages/support/resources/js/sortable.js
  window.Sortable = sortable_esm_default;
  var sortable_default = (Alpine) => {
    Alpine.directive("sortable", (el) => {
      el.sortable = sortable_esm_default.create(el, {
        draggable: "[x-sortable-item]",
        handle: "[x-sortable-handle]",
        dataIdAttr: "x-sortable-item"
      });
    });
  };

  // node_modules/@ryangjchandler/alpine-tooltip/dist/module.esm.js
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __markAsModule = (target) => __defProp(target, "__esModule", { value: true });
  var __commonJS = (callback, module) => () => {
    if (!module) {
      module = { exports: {} };
      callback(module.exports, module);
    }
    return module.exports;
  };
  var __exportStar = (target, module, desc) => {
    if (module && typeof module === "object" || typeof module === "function") {
      for (let key of __getOwnPropNames(module))
        if (!__hasOwnProp.call(target, key) && key !== "default")
          __defProp(target, key, { get: () => module[key], enumerable: !(desc = __getOwnPropDesc(module, key)) || desc.enumerable });
    }
    return target;
  };
  var __toModule = (module) => {
    return __exportStar(__markAsModule(__defProp(module != null ? __create(__getProtoOf(module)) : {}, "default", module && module.__esModule && "default" in module ? { get: () => module.default, enumerable: true } : { value: module, enumerable: true })), module);
  };
  var require_popper = __commonJS((exports) => {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    function getBoundingClientRect2(element) {
      var rect = element.getBoundingClientRect();
      return {
        width: rect.width,
        height: rect.height,
        top: rect.top,
        right: rect.right,
        bottom: rect.bottom,
        left: rect.left,
        x: rect.left,
        y: rect.top
      };
    }
    function getWindow2(node) {
      if (node == null) {
        return window;
      }
      if (node.toString() !== "[object Window]") {
        var ownerDocument = node.ownerDocument;
        return ownerDocument ? ownerDocument.defaultView || window : window;
      }
      return node;
    }
    function getWindowScroll(node) {
      var win = getWindow2(node);
      var scrollLeft = win.pageXOffset;
      var scrollTop = win.pageYOffset;
      return {
        scrollLeft,
        scrollTop
      };
    }
    function isElement2(node) {
      var OwnElement = getWindow2(node).Element;
      return node instanceof OwnElement || node instanceof Element;
    }
    function isHTMLElement2(node) {
      var OwnElement = getWindow2(node).HTMLElement;
      return node instanceof OwnElement || node instanceof HTMLElement;
    }
    function isShadowRoot2(node) {
      if (typeof ShadowRoot === "undefined") {
        return false;
      }
      var OwnElement = getWindow2(node).ShadowRoot;
      return node instanceof OwnElement || node instanceof ShadowRoot;
    }
    function getHTMLElementScroll(element) {
      return {
        scrollLeft: element.scrollLeft,
        scrollTop: element.scrollTop
      };
    }
    function getNodeScroll2(node) {
      if (node === getWindow2(node) || !isHTMLElement2(node)) {
        return getWindowScroll(node);
      } else {
        return getHTMLElementScroll(node);
      }
    }
    function getNodeName2(element) {
      return element ? (element.nodeName || "").toLowerCase() : null;
    }
    function getDocumentElement2(element) {
      return ((isElement2(element) ? element.ownerDocument : element.document) || window.document).documentElement;
    }
    function getWindowScrollBarX2(element) {
      return getBoundingClientRect2(getDocumentElement2(element)).left + getWindowScroll(element).scrollLeft;
    }
    function getComputedStyle2(element) {
      return getWindow2(element).getComputedStyle(element);
    }
    function isScrollParent(element) {
      var _getComputedStyle = getComputedStyle2(element), overflow = _getComputedStyle.overflow, overflowX = _getComputedStyle.overflowX, overflowY = _getComputedStyle.overflowY;
      return /auto|scroll|overlay|hidden/.test(overflow + overflowY + overflowX);
    }
    function getCompositeRect(elementOrVirtualElement, offsetParent, isFixed) {
      if (isFixed === void 0) {
        isFixed = false;
      }
      var documentElement = getDocumentElement2(offsetParent);
      var rect = getBoundingClientRect2(elementOrVirtualElement);
      var isOffsetParentAnElement = isHTMLElement2(offsetParent);
      var scroll = {
        scrollLeft: 0,
        scrollTop: 0
      };
      var offsets = {
        x: 0,
        y: 0
      };
      if (isOffsetParentAnElement || !isOffsetParentAnElement && !isFixed) {
        if (getNodeName2(offsetParent) !== "body" || isScrollParent(documentElement)) {
          scroll = getNodeScroll2(offsetParent);
        }
        if (isHTMLElement2(offsetParent)) {
          offsets = getBoundingClientRect2(offsetParent);
          offsets.x += offsetParent.clientLeft;
          offsets.y += offsetParent.clientTop;
        } else if (documentElement) {
          offsets.x = getWindowScrollBarX2(documentElement);
        }
      }
      return {
        x: rect.left + scroll.scrollLeft - offsets.x,
        y: rect.top + scroll.scrollTop - offsets.y,
        width: rect.width,
        height: rect.height
      };
    }
    function getLayoutRect(element) {
      var clientRect = getBoundingClientRect2(element);
      var width = element.offsetWidth;
      var height = element.offsetHeight;
      if (Math.abs(clientRect.width - width) <= 1) {
        width = clientRect.width;
      }
      if (Math.abs(clientRect.height - height) <= 1) {
        height = clientRect.height;
      }
      return {
        x: element.offsetLeft,
        y: element.offsetTop,
        width,
        height
      };
    }
    function getParentNode2(element) {
      if (getNodeName2(element) === "html") {
        return element;
      }
      return element.assignedSlot || element.parentNode || (isShadowRoot2(element) ? element.host : null) || getDocumentElement2(element);
    }
    function getScrollParent(node) {
      if (["html", "body", "#document"].indexOf(getNodeName2(node)) >= 0) {
        return node.ownerDocument.body;
      }
      if (isHTMLElement2(node) && isScrollParent(node)) {
        return node;
      }
      return getScrollParent(getParentNode2(node));
    }
    function listScrollParents(element, list) {
      var _element$ownerDocumen;
      if (list === void 0) {
        list = [];
      }
      var scrollParent = getScrollParent(element);
      var isBody = scrollParent === ((_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body);
      var win = getWindow2(scrollParent);
      var target = isBody ? [win].concat(win.visualViewport || [], isScrollParent(scrollParent) ? scrollParent : []) : scrollParent;
      var updatedList = list.concat(target);
      return isBody ? updatedList : updatedList.concat(listScrollParents(getParentNode2(target)));
    }
    function isTableElement2(element) {
      return ["table", "td", "th"].indexOf(getNodeName2(element)) >= 0;
    }
    function getTrueOffsetParent2(element) {
      if (!isHTMLElement2(element) || getComputedStyle2(element).position === "fixed") {
        return null;
      }
      return element.offsetParent;
    }
    function getContainingBlock2(element) {
      var isFirefox = navigator.userAgent.toLowerCase().indexOf("firefox") !== -1;
      var isIE = navigator.userAgent.indexOf("Trident") !== -1;
      if (isIE && isHTMLElement2(element)) {
        var elementCss = getComputedStyle2(element);
        if (elementCss.position === "fixed") {
          return null;
        }
      }
      var currentNode = getParentNode2(element);
      while (isHTMLElement2(currentNode) && ["html", "body"].indexOf(getNodeName2(currentNode)) < 0) {
        var css2 = getComputedStyle2(currentNode);
        if (css2.transform !== "none" || css2.perspective !== "none" || css2.contain === "paint" || ["transform", "perspective"].indexOf(css2.willChange) !== -1 || isFirefox && css2.willChange === "filter" || isFirefox && css2.filter && css2.filter !== "none") {
          return currentNode;
        } else {
          currentNode = currentNode.parentNode;
        }
      }
      return null;
    }
    function getOffsetParent2(element) {
      var window2 = getWindow2(element);
      var offsetParent = getTrueOffsetParent2(element);
      while (offsetParent && isTableElement2(offsetParent) && getComputedStyle2(offsetParent).position === "static") {
        offsetParent = getTrueOffsetParent2(offsetParent);
      }
      if (offsetParent && (getNodeName2(offsetParent) === "html" || getNodeName2(offsetParent) === "body" && getComputedStyle2(offsetParent).position === "static")) {
        return window2;
      }
      return offsetParent || getContainingBlock2(element) || window2;
    }
    var top = "top";
    var bottom = "bottom";
    var right = "right";
    var left = "left";
    var auto = "auto";
    var basePlacements = [top, bottom, right, left];
    var start = "start";
    var end = "end";
    var clippingParents = "clippingParents";
    var viewport = "viewport";
    var popper = "popper";
    var reference = "reference";
    var variationPlacements = /* @__PURE__ */ basePlacements.reduce(function(acc, placement) {
      return acc.concat([placement + "-" + start, placement + "-" + end]);
    }, []);
    var placements = /* @__PURE__ */ [].concat(basePlacements, [auto]).reduce(function(acc, placement) {
      return acc.concat([placement, placement + "-" + start, placement + "-" + end]);
    }, []);
    var beforeRead = "beforeRead";
    var read = "read";
    var afterRead = "afterRead";
    var beforeMain = "beforeMain";
    var main = "main";
    var afterMain = "afterMain";
    var beforeWrite = "beforeWrite";
    var write = "write";
    var afterWrite = "afterWrite";
    var modifierPhases = [beforeRead, read, afterRead, beforeMain, main, afterMain, beforeWrite, write, afterWrite];
    function order(modifiers) {
      var map = /* @__PURE__ */ new Map();
      var visited = /* @__PURE__ */ new Set();
      var result = [];
      modifiers.forEach(function(modifier) {
        map.set(modifier.name, modifier);
      });
      function sort2(modifier) {
        visited.add(modifier.name);
        var requires = [].concat(modifier.requires || [], modifier.requiresIfExists || []);
        requires.forEach(function(dep) {
          if (!visited.has(dep)) {
            var depModifier = map.get(dep);
            if (depModifier) {
              sort2(depModifier);
            }
          }
        });
        result.push(modifier);
      }
      modifiers.forEach(function(modifier) {
        if (!visited.has(modifier.name)) {
          sort2(modifier);
        }
      });
      return result;
    }
    function orderModifiers(modifiers) {
      var orderedModifiers = order(modifiers);
      return modifierPhases.reduce(function(acc, phase) {
        return acc.concat(orderedModifiers.filter(function(modifier) {
          return modifier.phase === phase;
        }));
      }, []);
    }
    function debounce(fn) {
      var pending;
      return function() {
        if (!pending) {
          pending = new Promise(function(resolve) {
            Promise.resolve().then(function() {
              pending = void 0;
              resolve(fn());
            });
          });
        }
        return pending;
      };
    }
    function format(str) {
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }
      return [].concat(args).reduce(function(p, c) {
        return p.replace(/%s/, c);
      }, str);
    }
    var INVALID_MODIFIER_ERROR = 'Popper: modifier "%s" provided an invalid %s property, expected %s but got %s';
    var MISSING_DEPENDENCY_ERROR = 'Popper: modifier "%s" requires "%s", but "%s" modifier is not available';
    var VALID_PROPERTIES = ["name", "enabled", "phase", "fn", "effect", "requires", "options"];
    function validateModifiers(modifiers) {
      modifiers.forEach(function(modifier) {
        Object.keys(modifier).forEach(function(key) {
          switch (key) {
            case "name":
              if (typeof modifier.name !== "string") {
                console.error(format(INVALID_MODIFIER_ERROR, String(modifier.name), '"name"', '"string"', '"' + String(modifier.name) + '"'));
              }
              break;
            case "enabled":
              if (typeof modifier.enabled !== "boolean") {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"enabled"', '"boolean"', '"' + String(modifier.enabled) + '"'));
              }
            case "phase":
              if (modifierPhases.indexOf(modifier.phase) < 0) {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"phase"', "either " + modifierPhases.join(", "), '"' + String(modifier.phase) + '"'));
              }
              break;
            case "fn":
              if (typeof modifier.fn !== "function") {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"fn"', '"function"', '"' + String(modifier.fn) + '"'));
              }
              break;
            case "effect":
              if (typeof modifier.effect !== "function") {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"effect"', '"function"', '"' + String(modifier.fn) + '"'));
              }
              break;
            case "requires":
              if (!Array.isArray(modifier.requires)) {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"requires"', '"array"', '"' + String(modifier.requires) + '"'));
              }
              break;
            case "requiresIfExists":
              if (!Array.isArray(modifier.requiresIfExists)) {
                console.error(format(INVALID_MODIFIER_ERROR, modifier.name, '"requiresIfExists"', '"array"', '"' + String(modifier.requiresIfExists) + '"'));
              }
              break;
            case "options":
            case "data":
              break;
            default:
              console.error('PopperJS: an invalid property has been provided to the "' + modifier.name + '" modifier, valid properties are ' + VALID_PROPERTIES.map(function(s) {
                return '"' + s + '"';
              }).join(", ") + '; but "' + key + '" was provided.');
          }
          modifier.requires && modifier.requires.forEach(function(requirement) {
            if (modifiers.find(function(mod) {
              return mod.name === requirement;
            }) == null) {
              console.error(format(MISSING_DEPENDENCY_ERROR, String(modifier.name), requirement, requirement));
            }
          });
        });
      });
    }
    function uniqueBy(arr, fn) {
      var identifiers = /* @__PURE__ */ new Set();
      return arr.filter(function(item) {
        var identifier = fn(item);
        if (!identifiers.has(identifier)) {
          identifiers.add(identifier);
          return true;
        }
      });
    }
    function getBasePlacement(placement) {
      return placement.split("-")[0];
    }
    function mergeByName(modifiers) {
      var merged = modifiers.reduce(function(merged2, current) {
        var existing = merged2[current.name];
        merged2[current.name] = existing ? Object.assign({}, existing, current, {
          options: Object.assign({}, existing.options, current.options),
          data: Object.assign({}, existing.data, current.data)
        }) : current;
        return merged2;
      }, {});
      return Object.keys(merged).map(function(key) {
        return merged[key];
      });
    }
    function getViewportRect2(element) {
      var win = getWindow2(element);
      var html = getDocumentElement2(element);
      var visualViewport = win.visualViewport;
      var width = html.clientWidth;
      var height = html.clientHeight;
      var x = 0;
      var y = 0;
      if (visualViewport) {
        width = visualViewport.width;
        height = visualViewport.height;
        if (!/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
          x = visualViewport.offsetLeft;
          y = visualViewport.offsetTop;
        }
      }
      return {
        width,
        height,
        x: x + getWindowScrollBarX2(element),
        y
      };
    }
    var max3 = Math.max;
    var min3 = Math.min;
    var round2 = Math.round;
    function getDocumentRect2(element) {
      var _element$ownerDocumen;
      var html = getDocumentElement2(element);
      var winScroll = getWindowScroll(element);
      var body = (_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body;
      var width = max3(html.scrollWidth, html.clientWidth, body ? body.scrollWidth : 0, body ? body.clientWidth : 0);
      var height = max3(html.scrollHeight, html.clientHeight, body ? body.scrollHeight : 0, body ? body.clientHeight : 0);
      var x = -winScroll.scrollLeft + getWindowScrollBarX2(element);
      var y = -winScroll.scrollTop;
      if (getComputedStyle2(body || html).direction === "rtl") {
        x += max3(html.clientWidth, body ? body.clientWidth : 0) - width;
      }
      return {
        width,
        height,
        x,
        y
      };
    }
    function contains2(parent, child) {
      var rootNode = child.getRootNode && child.getRootNode();
      if (parent.contains(child)) {
        return true;
      } else if (rootNode && isShadowRoot2(rootNode)) {
        var next = child;
        do {
          if (next && parent.isSameNode(next)) {
            return true;
          }
          next = next.parentNode || next.host;
        } while (next);
      }
      return false;
    }
    function rectToClientRect2(rect) {
      return Object.assign({}, rect, {
        left: rect.x,
        top: rect.y,
        right: rect.x + rect.width,
        bottom: rect.y + rect.height
      });
    }
    function getInnerBoundingClientRect2(element) {
      var rect = getBoundingClientRect2(element);
      rect.top = rect.top + element.clientTop;
      rect.left = rect.left + element.clientLeft;
      rect.bottom = rect.top + element.clientHeight;
      rect.right = rect.left + element.clientWidth;
      rect.width = element.clientWidth;
      rect.height = element.clientHeight;
      rect.x = rect.left;
      rect.y = rect.top;
      return rect;
    }
    function getClientRectFromMixedType(element, clippingParent) {
      return clippingParent === viewport ? rectToClientRect2(getViewportRect2(element)) : isHTMLElement2(clippingParent) ? getInnerBoundingClientRect2(clippingParent) : rectToClientRect2(getDocumentRect2(getDocumentElement2(element)));
    }
    function getClippingParents(element) {
      var clippingParents2 = listScrollParents(getParentNode2(element));
      var canEscapeClipping = ["absolute", "fixed"].indexOf(getComputedStyle2(element).position) >= 0;
      var clipperElement = canEscapeClipping && isHTMLElement2(element) ? getOffsetParent2(element) : element;
      if (!isElement2(clipperElement)) {
        return [];
      }
      return clippingParents2.filter(function(clippingParent) {
        return isElement2(clippingParent) && contains2(clippingParent, clipperElement) && getNodeName2(clippingParent) !== "body";
      });
    }
    function getClippingRect2(element, boundary, rootBoundary) {
      var mainClippingParents = boundary === "clippingParents" ? getClippingParents(element) : [].concat(boundary);
      var clippingParents2 = [].concat(mainClippingParents, [rootBoundary]);
      var firstClippingParent = clippingParents2[0];
      var clippingRect = clippingParents2.reduce(function(accRect, clippingParent) {
        var rect = getClientRectFromMixedType(element, clippingParent);
        accRect.top = max3(rect.top, accRect.top);
        accRect.right = min3(rect.right, accRect.right);
        accRect.bottom = min3(rect.bottom, accRect.bottom);
        accRect.left = max3(rect.left, accRect.left);
        return accRect;
      }, getClientRectFromMixedType(element, firstClippingParent));
      clippingRect.width = clippingRect.right - clippingRect.left;
      clippingRect.height = clippingRect.bottom - clippingRect.top;
      clippingRect.x = clippingRect.left;
      clippingRect.y = clippingRect.top;
      return clippingRect;
    }
    function getVariation(placement) {
      return placement.split("-")[1];
    }
    function getMainAxisFromPlacement2(placement) {
      return ["top", "bottom"].indexOf(placement) >= 0 ? "x" : "y";
    }
    function computeOffsets(_ref) {
      var reference2 = _ref.reference, element = _ref.element, placement = _ref.placement;
      var basePlacement = placement ? getBasePlacement(placement) : null;
      var variation = placement ? getVariation(placement) : null;
      var commonX = reference2.x + reference2.width / 2 - element.width / 2;
      var commonY = reference2.y + reference2.height / 2 - element.height / 2;
      var offsets;
      switch (basePlacement) {
        case top:
          offsets = {
            x: commonX,
            y: reference2.y - element.height
          };
          break;
        case bottom:
          offsets = {
            x: commonX,
            y: reference2.y + reference2.height
          };
          break;
        case right:
          offsets = {
            x: reference2.x + reference2.width,
            y: commonY
          };
          break;
        case left:
          offsets = {
            x: reference2.x - element.width,
            y: commonY
          };
          break;
        default:
          offsets = {
            x: reference2.x,
            y: reference2.y
          };
      }
      var mainAxis = basePlacement ? getMainAxisFromPlacement2(basePlacement) : null;
      if (mainAxis != null) {
        var len = mainAxis === "y" ? "height" : "width";
        switch (variation) {
          case start:
            offsets[mainAxis] = offsets[mainAxis] - (reference2[len] / 2 - element[len] / 2);
            break;
          case end:
            offsets[mainAxis] = offsets[mainAxis] + (reference2[len] / 2 - element[len] / 2);
            break;
        }
      }
      return offsets;
    }
    function getFreshSideObject() {
      return {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
      };
    }
    function mergePaddingObject(paddingObject) {
      return Object.assign({}, getFreshSideObject(), paddingObject);
    }
    function expandToHashMap(value, keys) {
      return keys.reduce(function(hashMap, key) {
        hashMap[key] = value;
        return hashMap;
      }, {});
    }
    function detectOverflow2(state, options) {
      if (options === void 0) {
        options = {};
      }
      var _options = options, _options$placement = _options.placement, placement = _options$placement === void 0 ? state.placement : _options$placement, _options$boundary = _options.boundary, boundary = _options$boundary === void 0 ? clippingParents : _options$boundary, _options$rootBoundary = _options.rootBoundary, rootBoundary = _options$rootBoundary === void 0 ? viewport : _options$rootBoundary, _options$elementConte = _options.elementContext, elementContext = _options$elementConte === void 0 ? popper : _options$elementConte, _options$altBoundary = _options.altBoundary, altBoundary = _options$altBoundary === void 0 ? false : _options$altBoundary, _options$padding = _options.padding, padding = _options$padding === void 0 ? 0 : _options$padding;
      var paddingObject = mergePaddingObject(typeof padding !== "number" ? padding : expandToHashMap(padding, basePlacements));
      var altContext = elementContext === popper ? reference : popper;
      var referenceElement = state.elements.reference;
      var popperRect = state.rects.popper;
      var element = state.elements[altBoundary ? altContext : elementContext];
      var clippingClientRect = getClippingRect2(isElement2(element) ? element : element.contextElement || getDocumentElement2(state.elements.popper), boundary, rootBoundary);
      var referenceClientRect = getBoundingClientRect2(referenceElement);
      var popperOffsets2 = computeOffsets({
        reference: referenceClientRect,
        element: popperRect,
        strategy: "absolute",
        placement
      });
      var popperClientRect = rectToClientRect2(Object.assign({}, popperRect, popperOffsets2));
      var elementClientRect = elementContext === popper ? popperClientRect : referenceClientRect;
      var overflowOffsets = {
        top: clippingClientRect.top - elementClientRect.top + paddingObject.top,
        bottom: elementClientRect.bottom - clippingClientRect.bottom + paddingObject.bottom,
        left: clippingClientRect.left - elementClientRect.left + paddingObject.left,
        right: elementClientRect.right - clippingClientRect.right + paddingObject.right
      };
      var offsetData = state.modifiersData.offset;
      if (elementContext === popper && offsetData) {
        var offset22 = offsetData[placement];
        Object.keys(overflowOffsets).forEach(function(key) {
          var multiply = [right, bottom].indexOf(key) >= 0 ? 1 : -1;
          var axis = [top, bottom].indexOf(key) >= 0 ? "y" : "x";
          overflowOffsets[key] += offset22[axis] * multiply;
        });
      }
      return overflowOffsets;
    }
    var INVALID_ELEMENT_ERROR = "Popper: Invalid reference or popper argument provided. They must be either a DOM element or virtual element.";
    var INFINITE_LOOP_ERROR = "Popper: An infinite loop in the modifiers cycle has been detected! The cycle has been interrupted to prevent a browser crash.";
    var DEFAULT_OPTIONS = {
      placement: "bottom",
      modifiers: [],
      strategy: "absolute"
    };
    function areValidElements() {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      return !args.some(function(element) {
        return !(element && typeof element.getBoundingClientRect === "function");
      });
    }
    function popperGenerator(generatorOptions) {
      if (generatorOptions === void 0) {
        generatorOptions = {};
      }
      var _generatorOptions = generatorOptions, _generatorOptions$def = _generatorOptions.defaultModifiers, defaultModifiers2 = _generatorOptions$def === void 0 ? [] : _generatorOptions$def, _generatorOptions$def2 = _generatorOptions.defaultOptions, defaultOptions = _generatorOptions$def2 === void 0 ? DEFAULT_OPTIONS : _generatorOptions$def2;
      return function createPopper2(reference2, popper2, options) {
        if (options === void 0) {
          options = defaultOptions;
        }
        var state = {
          placement: "bottom",
          orderedModifiers: [],
          options: Object.assign({}, DEFAULT_OPTIONS, defaultOptions),
          modifiersData: {},
          elements: {
            reference: reference2,
            popper: popper2
          },
          attributes: {},
          styles: {}
        };
        var effectCleanupFns = [];
        var isDestroyed = false;
        var instance = {
          state,
          setOptions: function setOptions(options2) {
            cleanupModifierEffects();
            state.options = Object.assign({}, defaultOptions, state.options, options2);
            state.scrollParents = {
              reference: isElement2(reference2) ? listScrollParents(reference2) : reference2.contextElement ? listScrollParents(reference2.contextElement) : [],
              popper: listScrollParents(popper2)
            };
            var orderedModifiers = orderModifiers(mergeByName([].concat(defaultModifiers2, state.options.modifiers)));
            state.orderedModifiers = orderedModifiers.filter(function(m) {
              return m.enabled;
            });
            if (true) {
              var modifiers = uniqueBy([].concat(orderedModifiers, state.options.modifiers), function(_ref) {
                var name = _ref.name;
                return name;
              });
              validateModifiers(modifiers);
              if (getBasePlacement(state.options.placement) === auto) {
                var flipModifier = state.orderedModifiers.find(function(_ref2) {
                  var name = _ref2.name;
                  return name === "flip";
                });
                if (!flipModifier) {
                  console.error(['Popper: "auto" placements require the "flip" modifier be', "present and enabled to work."].join(" "));
                }
              }
              var _getComputedStyle = getComputedStyle2(popper2), marginTop = _getComputedStyle.marginTop, marginRight = _getComputedStyle.marginRight, marginBottom = _getComputedStyle.marginBottom, marginLeft = _getComputedStyle.marginLeft;
              if ([marginTop, marginRight, marginBottom, marginLeft].some(function(margin) {
                return parseFloat(margin);
              })) {
                console.warn(['Popper: CSS "margin" styles cannot be used to apply padding', "between the popper and its reference element or boundary.", "To replicate margin, use the `offset` modifier, as well as", "the `padding` option in the `preventOverflow` and `flip`", "modifiers."].join(" "));
              }
            }
            runModifierEffects();
            return instance.update();
          },
          forceUpdate: function forceUpdate() {
            if (isDestroyed) {
              return;
            }
            var _state$elements = state.elements, reference3 = _state$elements.reference, popper3 = _state$elements.popper;
            if (!areValidElements(reference3, popper3)) {
              if (true) {
                console.error(INVALID_ELEMENT_ERROR);
              }
              return;
            }
            state.rects = {
              reference: getCompositeRect(reference3, getOffsetParent2(popper3), state.options.strategy === "fixed"),
              popper: getLayoutRect(popper3)
            };
            state.reset = false;
            state.placement = state.options.placement;
            state.orderedModifiers.forEach(function(modifier) {
              return state.modifiersData[modifier.name] = Object.assign({}, modifier.data);
            });
            var __debug_loops__ = 0;
            for (var index2 = 0; index2 < state.orderedModifiers.length; index2++) {
              if (true) {
                __debug_loops__ += 1;
                if (__debug_loops__ > 100) {
                  console.error(INFINITE_LOOP_ERROR);
                  break;
                }
              }
              if (state.reset === true) {
                state.reset = false;
                index2 = -1;
                continue;
              }
              var _state$orderedModifie = state.orderedModifiers[index2], fn = _state$orderedModifie.fn, _state$orderedModifie2 = _state$orderedModifie.options, _options = _state$orderedModifie2 === void 0 ? {} : _state$orderedModifie2, name = _state$orderedModifie.name;
              if (typeof fn === "function") {
                state = fn({
                  state,
                  options: _options,
                  name,
                  instance
                }) || state;
              }
            }
          },
          update: debounce(function() {
            return new Promise(function(resolve) {
              instance.forceUpdate();
              resolve(state);
            });
          }),
          destroy: function destroy2() {
            cleanupModifierEffects();
            isDestroyed = true;
          }
        };
        if (!areValidElements(reference2, popper2)) {
          if (true) {
            console.error(INVALID_ELEMENT_ERROR);
          }
          return instance;
        }
        instance.setOptions(options).then(function(state2) {
          if (!isDestroyed && options.onFirstUpdate) {
            options.onFirstUpdate(state2);
          }
        });
        function runModifierEffects() {
          state.orderedModifiers.forEach(function(_ref3) {
            var name = _ref3.name, _ref3$options = _ref3.options, options2 = _ref3$options === void 0 ? {} : _ref3$options, effect2 = _ref3.effect;
            if (typeof effect2 === "function") {
              var cleanupFn = effect2({
                state,
                name,
                instance,
                options: options2
              });
              var noopFn = function noopFn2() {
              };
              effectCleanupFns.push(cleanupFn || noopFn);
            }
          });
        }
        function cleanupModifierEffects() {
          effectCleanupFns.forEach(function(fn) {
            return fn();
          });
          effectCleanupFns = [];
        }
        return instance;
      };
    }
    var passive = {
      passive: true
    };
    function effect$2(_ref) {
      var state = _ref.state, instance = _ref.instance, options = _ref.options;
      var _options$scroll = options.scroll, scroll = _options$scroll === void 0 ? true : _options$scroll, _options$resize = options.resize, resize = _options$resize === void 0 ? true : _options$resize;
      var window2 = getWindow2(state.elements.popper);
      var scrollParents = [].concat(state.scrollParents.reference, state.scrollParents.popper);
      if (scroll) {
        scrollParents.forEach(function(scrollParent) {
          scrollParent.addEventListener("scroll", instance.update, passive);
        });
      }
      if (resize) {
        window2.addEventListener("resize", instance.update, passive);
      }
      return function() {
        if (scroll) {
          scrollParents.forEach(function(scrollParent) {
            scrollParent.removeEventListener("scroll", instance.update, passive);
          });
        }
        if (resize) {
          window2.removeEventListener("resize", instance.update, passive);
        }
      };
    }
    var eventListeners = {
      name: "eventListeners",
      enabled: true,
      phase: "write",
      fn: function fn() {
      },
      effect: effect$2,
      data: {}
    };
    function popperOffsets(_ref) {
      var state = _ref.state, name = _ref.name;
      state.modifiersData[name] = computeOffsets({
        reference: state.rects.reference,
        element: state.rects.popper,
        strategy: "absolute",
        placement: state.placement
      });
    }
    var popperOffsets$1 = {
      name: "popperOffsets",
      enabled: true,
      phase: "read",
      fn: popperOffsets,
      data: {}
    };
    var unsetSides = {
      top: "auto",
      right: "auto",
      bottom: "auto",
      left: "auto"
    };
    function roundOffsetsByDPR(_ref) {
      var x = _ref.x, y = _ref.y;
      var win = window;
      var dpr = win.devicePixelRatio || 1;
      return {
        x: round2(round2(x * dpr) / dpr) || 0,
        y: round2(round2(y * dpr) / dpr) || 0
      };
    }
    function mapToStyles(_ref2) {
      var _Object$assign2;
      var popper2 = _ref2.popper, popperRect = _ref2.popperRect, placement = _ref2.placement, offsets = _ref2.offsets, position = _ref2.position, gpuAcceleration = _ref2.gpuAcceleration, adaptive = _ref2.adaptive, roundOffsets = _ref2.roundOffsets;
      var _ref3 = roundOffsets === true ? roundOffsetsByDPR(offsets) : typeof roundOffsets === "function" ? roundOffsets(offsets) : offsets, _ref3$x = _ref3.x, x = _ref3$x === void 0 ? 0 : _ref3$x, _ref3$y = _ref3.y, y = _ref3$y === void 0 ? 0 : _ref3$y;
      var hasX = offsets.hasOwnProperty("x");
      var hasY = offsets.hasOwnProperty("y");
      var sideX = left;
      var sideY = top;
      var win = window;
      if (adaptive) {
        var offsetParent = getOffsetParent2(popper2);
        var heightProp = "clientHeight";
        var widthProp = "clientWidth";
        if (offsetParent === getWindow2(popper2)) {
          offsetParent = getDocumentElement2(popper2);
          if (getComputedStyle2(offsetParent).position !== "static") {
            heightProp = "scrollHeight";
            widthProp = "scrollWidth";
          }
        }
        offsetParent = offsetParent;
        if (placement === top) {
          sideY = bottom;
          y -= offsetParent[heightProp] - popperRect.height;
          y *= gpuAcceleration ? 1 : -1;
        }
        if (placement === left) {
          sideX = right;
          x -= offsetParent[widthProp] - popperRect.width;
          x *= gpuAcceleration ? 1 : -1;
        }
      }
      var commonStyles = Object.assign({
        position
      }, adaptive && unsetSides);
      if (gpuAcceleration) {
        var _Object$assign;
        return Object.assign({}, commonStyles, (_Object$assign = {}, _Object$assign[sideY] = hasY ? "0" : "", _Object$assign[sideX] = hasX ? "0" : "", _Object$assign.transform = (win.devicePixelRatio || 1) < 2 ? "translate(" + x + "px, " + y + "px)" : "translate3d(" + x + "px, " + y + "px, 0)", _Object$assign));
      }
      return Object.assign({}, commonStyles, (_Object$assign2 = {}, _Object$assign2[sideY] = hasY ? y + "px" : "", _Object$assign2[sideX] = hasX ? x + "px" : "", _Object$assign2.transform = "", _Object$assign2));
    }
    function computeStyles(_ref4) {
      var state = _ref4.state, options = _ref4.options;
      var _options$gpuAccelerat = options.gpuAcceleration, gpuAcceleration = _options$gpuAccelerat === void 0 ? true : _options$gpuAccelerat, _options$adaptive = options.adaptive, adaptive = _options$adaptive === void 0 ? true : _options$adaptive, _options$roundOffsets = options.roundOffsets, roundOffsets = _options$roundOffsets === void 0 ? true : _options$roundOffsets;
      if (true) {
        var transitionProperty = getComputedStyle2(state.elements.popper).transitionProperty || "";
        if (adaptive && ["transform", "top", "right", "bottom", "left"].some(function(property) {
          return transitionProperty.indexOf(property) >= 0;
        })) {
          console.warn(["Popper: Detected CSS transitions on at least one of the following", 'CSS properties: "transform", "top", "right", "bottom", "left".', "\n\n", 'Disable the "computeStyles" modifier\'s `adaptive` option to allow', "for smooth transitions, or remove these properties from the CSS", "transition declaration on the popper element if only transitioning", "opacity or background-color for example.", "\n\n", "We recommend using the popper element as a wrapper around an inner", "element that can have any CSS property transitioned for animations."].join(" "));
        }
      }
      var commonStyles = {
        placement: getBasePlacement(state.placement),
        popper: state.elements.popper,
        popperRect: state.rects.popper,
        gpuAcceleration
      };
      if (state.modifiersData.popperOffsets != null) {
        state.styles.popper = Object.assign({}, state.styles.popper, mapToStyles(Object.assign({}, commonStyles, {
          offsets: state.modifiersData.popperOffsets,
          position: state.options.strategy,
          adaptive,
          roundOffsets
        })));
      }
      if (state.modifiersData.arrow != null) {
        state.styles.arrow = Object.assign({}, state.styles.arrow, mapToStyles(Object.assign({}, commonStyles, {
          offsets: state.modifiersData.arrow,
          position: "absolute",
          adaptive: false,
          roundOffsets
        })));
      }
      state.attributes.popper = Object.assign({}, state.attributes.popper, {
        "data-popper-placement": state.placement
      });
    }
    var computeStyles$1 = {
      name: "computeStyles",
      enabled: true,
      phase: "beforeWrite",
      fn: computeStyles,
      data: {}
    };
    function applyStyles(_ref) {
      var state = _ref.state;
      Object.keys(state.elements).forEach(function(name) {
        var style = state.styles[name] || {};
        var attributes = state.attributes[name] || {};
        var element = state.elements[name];
        if (!isHTMLElement2(element) || !getNodeName2(element)) {
          return;
        }
        Object.assign(element.style, style);
        Object.keys(attributes).forEach(function(name2) {
          var value = attributes[name2];
          if (value === false) {
            element.removeAttribute(name2);
          } else {
            element.setAttribute(name2, value === true ? "" : value);
          }
        });
      });
    }
    function effect$1(_ref2) {
      var state = _ref2.state;
      var initialStyles = {
        popper: {
          position: state.options.strategy,
          left: "0",
          top: "0",
          margin: "0"
        },
        arrow: {
          position: "absolute"
        },
        reference: {}
      };
      Object.assign(state.elements.popper.style, initialStyles.popper);
      state.styles = initialStyles;
      if (state.elements.arrow) {
        Object.assign(state.elements.arrow.style, initialStyles.arrow);
      }
      return function() {
        Object.keys(state.elements).forEach(function(name) {
          var element = state.elements[name];
          var attributes = state.attributes[name] || {};
          var styleProperties = Object.keys(state.styles.hasOwnProperty(name) ? state.styles[name] : initialStyles[name]);
          var style = styleProperties.reduce(function(style2, property) {
            style2[property] = "";
            return style2;
          }, {});
          if (!isHTMLElement2(element) || !getNodeName2(element)) {
            return;
          }
          Object.assign(element.style, style);
          Object.keys(attributes).forEach(function(attribute) {
            element.removeAttribute(attribute);
          });
        });
      };
    }
    var applyStyles$1 = {
      name: "applyStyles",
      enabled: true,
      phase: "write",
      fn: applyStyles,
      effect: effect$1,
      requires: ["computeStyles"]
    };
    function distanceAndSkiddingToXY(placement, rects, offset22) {
      var basePlacement = getBasePlacement(placement);
      var invertDistance = [left, top].indexOf(basePlacement) >= 0 ? -1 : 1;
      var _ref = typeof offset22 === "function" ? offset22(Object.assign({}, rects, {
        placement
      })) : offset22, skidding = _ref[0], distance = _ref[1];
      skidding = skidding || 0;
      distance = (distance || 0) * invertDistance;
      return [left, right].indexOf(basePlacement) >= 0 ? {
        x: distance,
        y: skidding
      } : {
        x: skidding,
        y: distance
      };
    }
    function offset2(_ref2) {
      var state = _ref2.state, options = _ref2.options, name = _ref2.name;
      var _options$offset = options.offset, offset22 = _options$offset === void 0 ? [0, 0] : _options$offset;
      var data = placements.reduce(function(acc, placement) {
        acc[placement] = distanceAndSkiddingToXY(placement, state.rects, offset22);
        return acc;
      }, {});
      var _data$state$placement = data[state.placement], x = _data$state$placement.x, y = _data$state$placement.y;
      if (state.modifiersData.popperOffsets != null) {
        state.modifiersData.popperOffsets.x += x;
        state.modifiersData.popperOffsets.y += y;
      }
      state.modifiersData[name] = data;
    }
    var offset$1 = {
      name: "offset",
      enabled: true,
      phase: "main",
      requires: ["popperOffsets"],
      fn: offset2
    };
    var hash$12 = {
      left: "right",
      right: "left",
      bottom: "top",
      top: "bottom"
    };
    function getOppositePlacement2(placement) {
      return placement.replace(/left|right|bottom|top/g, function(matched) {
        return hash$12[matched];
      });
    }
    var hash2 = {
      start: "end",
      end: "start"
    };
    function getOppositeVariationPlacement(placement) {
      return placement.replace(/start|end/g, function(matched) {
        return hash2[matched];
      });
    }
    function computeAutoPlacement(state, options) {
      if (options === void 0) {
        options = {};
      }
      var _options = options, placement = _options.placement, boundary = _options.boundary, rootBoundary = _options.rootBoundary, padding = _options.padding, flipVariations = _options.flipVariations, _options$allowedAutoP = _options.allowedAutoPlacements, allowedAutoPlacements = _options$allowedAutoP === void 0 ? placements : _options$allowedAutoP;
      var variation = getVariation(placement);
      var placements$1 = variation ? flipVariations ? variationPlacements : variationPlacements.filter(function(placement2) {
        return getVariation(placement2) === variation;
      }) : basePlacements;
      var allowedPlacements = placements$1.filter(function(placement2) {
        return allowedAutoPlacements.indexOf(placement2) >= 0;
      });
      if (allowedPlacements.length === 0) {
        allowedPlacements = placements$1;
        if (true) {
          console.error(["Popper: The `allowedAutoPlacements` option did not allow any", "placements. Ensure the `placement` option matches the variation", "of the allowed placements.", 'For example, "auto" cannot be used to allow "bottom-start".', 'Use "auto-start" instead.'].join(" "));
        }
      }
      var overflows = allowedPlacements.reduce(function(acc, placement2) {
        acc[placement2] = detectOverflow2(state, {
          placement: placement2,
          boundary,
          rootBoundary,
          padding
        })[getBasePlacement(placement2)];
        return acc;
      }, {});
      return Object.keys(overflows).sort(function(a, b) {
        return overflows[a] - overflows[b];
      });
    }
    function getExpandedFallbackPlacements(placement) {
      if (getBasePlacement(placement) === auto) {
        return [];
      }
      var oppositePlacement = getOppositePlacement2(placement);
      return [getOppositeVariationPlacement(placement), oppositePlacement, getOppositeVariationPlacement(oppositePlacement)];
    }
    function flip2(_ref) {
      var state = _ref.state, options = _ref.options, name = _ref.name;
      if (state.modifiersData[name]._skip) {
        return;
      }
      var _options$mainAxis = options.mainAxis, checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis, _options$altAxis = options.altAxis, checkAltAxis = _options$altAxis === void 0 ? true : _options$altAxis, specifiedFallbackPlacements = options.fallbackPlacements, padding = options.padding, boundary = options.boundary, rootBoundary = options.rootBoundary, altBoundary = options.altBoundary, _options$flipVariatio = options.flipVariations, flipVariations = _options$flipVariatio === void 0 ? true : _options$flipVariatio, allowedAutoPlacements = options.allowedAutoPlacements;
      var preferredPlacement = state.options.placement;
      var basePlacement = getBasePlacement(preferredPlacement);
      var isBasePlacement = basePlacement === preferredPlacement;
      var fallbackPlacements = specifiedFallbackPlacements || (isBasePlacement || !flipVariations ? [getOppositePlacement2(preferredPlacement)] : getExpandedFallbackPlacements(preferredPlacement));
      var placements2 = [preferredPlacement].concat(fallbackPlacements).reduce(function(acc, placement2) {
        return acc.concat(getBasePlacement(placement2) === auto ? computeAutoPlacement(state, {
          placement: placement2,
          boundary,
          rootBoundary,
          padding,
          flipVariations,
          allowedAutoPlacements
        }) : placement2);
      }, []);
      var referenceRect = state.rects.reference;
      var popperRect = state.rects.popper;
      var checksMap = /* @__PURE__ */ new Map();
      var makeFallbackChecks = true;
      var firstFittingPlacement = placements2[0];
      for (var i = 0; i < placements2.length; i++) {
        var placement = placements2[i];
        var _basePlacement = getBasePlacement(placement);
        var isStartVariation = getVariation(placement) === start;
        var isVertical = [top, bottom].indexOf(_basePlacement) >= 0;
        var len = isVertical ? "width" : "height";
        var overflow = detectOverflow2(state, {
          placement,
          boundary,
          rootBoundary,
          altBoundary,
          padding
        });
        var mainVariationSide = isVertical ? isStartVariation ? right : left : isStartVariation ? bottom : top;
        if (referenceRect[len] > popperRect[len]) {
          mainVariationSide = getOppositePlacement2(mainVariationSide);
        }
        var altVariationSide = getOppositePlacement2(mainVariationSide);
        var checks = [];
        if (checkMainAxis) {
          checks.push(overflow[_basePlacement] <= 0);
        }
        if (checkAltAxis) {
          checks.push(overflow[mainVariationSide] <= 0, overflow[altVariationSide] <= 0);
        }
        if (checks.every(function(check) {
          return check;
        })) {
          firstFittingPlacement = placement;
          makeFallbackChecks = false;
          break;
        }
        checksMap.set(placement, checks);
      }
      if (makeFallbackChecks) {
        var numberOfChecks = flipVariations ? 3 : 1;
        var _loop = function _loop2(_i2) {
          var fittingPlacement = placements2.find(function(placement2) {
            var checks2 = checksMap.get(placement2);
            if (checks2) {
              return checks2.slice(0, _i2).every(function(check) {
                return check;
              });
            }
          });
          if (fittingPlacement) {
            firstFittingPlacement = fittingPlacement;
            return "break";
          }
        };
        for (var _i = numberOfChecks; _i > 0; _i--) {
          var _ret = _loop(_i);
          if (_ret === "break")
            break;
        }
      }
      if (state.placement !== firstFittingPlacement) {
        state.modifiersData[name]._skip = true;
        state.placement = firstFittingPlacement;
        state.reset = true;
      }
    }
    var flip$1 = {
      name: "flip",
      enabled: true,
      phase: "main",
      fn: flip2,
      requiresIfExists: ["offset"],
      data: {
        _skip: false
      }
    };
    function getAltAxis(axis) {
      return axis === "x" ? "y" : "x";
    }
    function within2(min$1, value, max$1) {
      return max3(min$1, min3(value, max$1));
    }
    function preventOverflow(_ref) {
      var state = _ref.state, options = _ref.options, name = _ref.name;
      var _options$mainAxis = options.mainAxis, checkMainAxis = _options$mainAxis === void 0 ? true : _options$mainAxis, _options$altAxis = options.altAxis, checkAltAxis = _options$altAxis === void 0 ? false : _options$altAxis, boundary = options.boundary, rootBoundary = options.rootBoundary, altBoundary = options.altBoundary, padding = options.padding, _options$tether = options.tether, tether = _options$tether === void 0 ? true : _options$tether, _options$tetherOffset = options.tetherOffset, tetherOffset = _options$tetherOffset === void 0 ? 0 : _options$tetherOffset;
      var overflow = detectOverflow2(state, {
        boundary,
        rootBoundary,
        padding,
        altBoundary
      });
      var basePlacement = getBasePlacement(state.placement);
      var variation = getVariation(state.placement);
      var isBasePlacement = !variation;
      var mainAxis = getMainAxisFromPlacement2(basePlacement);
      var altAxis = getAltAxis(mainAxis);
      var popperOffsets2 = state.modifiersData.popperOffsets;
      var referenceRect = state.rects.reference;
      var popperRect = state.rects.popper;
      var tetherOffsetValue = typeof tetherOffset === "function" ? tetherOffset(Object.assign({}, state.rects, {
        placement: state.placement
      })) : tetherOffset;
      var data = {
        x: 0,
        y: 0
      };
      if (!popperOffsets2) {
        return;
      }
      if (checkMainAxis || checkAltAxis) {
        var mainSide = mainAxis === "y" ? top : left;
        var altSide = mainAxis === "y" ? bottom : right;
        var len = mainAxis === "y" ? "height" : "width";
        var offset22 = popperOffsets2[mainAxis];
        var min$1 = popperOffsets2[mainAxis] + overflow[mainSide];
        var max$1 = popperOffsets2[mainAxis] - overflow[altSide];
        var additive = tether ? -popperRect[len] / 2 : 0;
        var minLen = variation === start ? referenceRect[len] : popperRect[len];
        var maxLen = variation === start ? -popperRect[len] : -referenceRect[len];
        var arrowElement = state.elements.arrow;
        var arrowRect = tether && arrowElement ? getLayoutRect(arrowElement) : {
          width: 0,
          height: 0
        };
        var arrowPaddingObject = state.modifiersData["arrow#persistent"] ? state.modifiersData["arrow#persistent"].padding : getFreshSideObject();
        var arrowPaddingMin = arrowPaddingObject[mainSide];
        var arrowPaddingMax = arrowPaddingObject[altSide];
        var arrowLen = within2(0, referenceRect[len], arrowRect[len]);
        var minOffset = isBasePlacement ? referenceRect[len] / 2 - additive - arrowLen - arrowPaddingMin - tetherOffsetValue : minLen - arrowLen - arrowPaddingMin - tetherOffsetValue;
        var maxOffset = isBasePlacement ? -referenceRect[len] / 2 + additive + arrowLen + arrowPaddingMax + tetherOffsetValue : maxLen + arrowLen + arrowPaddingMax + tetherOffsetValue;
        var arrowOffsetParent = state.elements.arrow && getOffsetParent2(state.elements.arrow);
        var clientOffset = arrowOffsetParent ? mainAxis === "y" ? arrowOffsetParent.clientTop || 0 : arrowOffsetParent.clientLeft || 0 : 0;
        var offsetModifierValue = state.modifiersData.offset ? state.modifiersData.offset[state.placement][mainAxis] : 0;
        var tetherMin = popperOffsets2[mainAxis] + minOffset - offsetModifierValue - clientOffset;
        var tetherMax = popperOffsets2[mainAxis] + maxOffset - offsetModifierValue;
        if (checkMainAxis) {
          var preventedOffset = within2(tether ? min3(min$1, tetherMin) : min$1, offset22, tether ? max3(max$1, tetherMax) : max$1);
          popperOffsets2[mainAxis] = preventedOffset;
          data[mainAxis] = preventedOffset - offset22;
        }
        if (checkAltAxis) {
          var _mainSide = mainAxis === "x" ? top : left;
          var _altSide = mainAxis === "x" ? bottom : right;
          var _offset = popperOffsets2[altAxis];
          var _min = _offset + overflow[_mainSide];
          var _max = _offset - overflow[_altSide];
          var _preventedOffset = within2(tether ? min3(_min, tetherMin) : _min, _offset, tether ? max3(_max, tetherMax) : _max);
          popperOffsets2[altAxis] = _preventedOffset;
          data[altAxis] = _preventedOffset - _offset;
        }
      }
      state.modifiersData[name] = data;
    }
    var preventOverflow$1 = {
      name: "preventOverflow",
      enabled: true,
      phase: "main",
      fn: preventOverflow,
      requiresIfExists: ["offset"]
    };
    var toPaddingObject = function toPaddingObject2(padding, state) {
      padding = typeof padding === "function" ? padding(Object.assign({}, state.rects, {
        placement: state.placement
      })) : padding;
      return mergePaddingObject(typeof padding !== "number" ? padding : expandToHashMap(padding, basePlacements));
    };
    function arrow2(_ref) {
      var _state$modifiersData$;
      var state = _ref.state, name = _ref.name, options = _ref.options;
      var arrowElement = state.elements.arrow;
      var popperOffsets2 = state.modifiersData.popperOffsets;
      var basePlacement = getBasePlacement(state.placement);
      var axis = getMainAxisFromPlacement2(basePlacement);
      var isVertical = [left, right].indexOf(basePlacement) >= 0;
      var len = isVertical ? "height" : "width";
      if (!arrowElement || !popperOffsets2) {
        return;
      }
      var paddingObject = toPaddingObject(options.padding, state);
      var arrowRect = getLayoutRect(arrowElement);
      var minProp = axis === "y" ? top : left;
      var maxProp = axis === "y" ? bottom : right;
      var endDiff = state.rects.reference[len] + state.rects.reference[axis] - popperOffsets2[axis] - state.rects.popper[len];
      var startDiff = popperOffsets2[axis] - state.rects.reference[axis];
      var arrowOffsetParent = getOffsetParent2(arrowElement);
      var clientSize = arrowOffsetParent ? axis === "y" ? arrowOffsetParent.clientHeight || 0 : arrowOffsetParent.clientWidth || 0 : 0;
      var centerToReference = endDiff / 2 - startDiff / 2;
      var min22 = paddingObject[minProp];
      var max22 = clientSize - arrowRect[len] - paddingObject[maxProp];
      var center = clientSize / 2 - arrowRect[len] / 2 + centerToReference;
      var offset22 = within2(min22, center, max22);
      var axisProp = axis;
      state.modifiersData[name] = (_state$modifiersData$ = {}, _state$modifiersData$[axisProp] = offset22, _state$modifiersData$.centerOffset = offset22 - center, _state$modifiersData$);
    }
    function effect(_ref2) {
      var state = _ref2.state, options = _ref2.options;
      var _options$element = options.element, arrowElement = _options$element === void 0 ? "[data-popper-arrow]" : _options$element;
      if (arrowElement == null) {
        return;
      }
      if (typeof arrowElement === "string") {
        arrowElement = state.elements.popper.querySelector(arrowElement);
        if (!arrowElement) {
          return;
        }
      }
      if (true) {
        if (!isHTMLElement2(arrowElement)) {
          console.error(['Popper: "arrow" element must be an HTMLElement (not an SVGElement).', "To use an SVG arrow, wrap it in an HTMLElement that will be used as", "the arrow."].join(" "));
        }
      }
      if (!contains2(state.elements.popper, arrowElement)) {
        if (true) {
          console.error(['Popper: "arrow" modifier\'s `element` must be a child of the popper', "element."].join(" "));
        }
        return;
      }
      state.elements.arrow = arrowElement;
    }
    var arrow$1 = {
      name: "arrow",
      enabled: true,
      phase: "main",
      fn: arrow2,
      effect,
      requires: ["popperOffsets"],
      requiresIfExists: ["preventOverflow"]
    };
    function getSideOffsets2(overflow, rect, preventedOffsets) {
      if (preventedOffsets === void 0) {
        preventedOffsets = {
          x: 0,
          y: 0
        };
      }
      return {
        top: overflow.top - rect.height - preventedOffsets.y,
        right: overflow.right - rect.width + preventedOffsets.x,
        bottom: overflow.bottom - rect.height + preventedOffsets.y,
        left: overflow.left - rect.width - preventedOffsets.x
      };
    }
    function isAnySideFullyClipped2(overflow) {
      return [top, right, bottom, left].some(function(side) {
        return overflow[side] >= 0;
      });
    }
    function hide2(_ref) {
      var state = _ref.state, name = _ref.name;
      var referenceRect = state.rects.reference;
      var popperRect = state.rects.popper;
      var preventedOffsets = state.modifiersData.preventOverflow;
      var referenceOverflow = detectOverflow2(state, {
        elementContext: "reference"
      });
      var popperAltOverflow = detectOverflow2(state, {
        altBoundary: true
      });
      var referenceClippingOffsets = getSideOffsets2(referenceOverflow, referenceRect);
      var popperEscapeOffsets = getSideOffsets2(popperAltOverflow, popperRect, preventedOffsets);
      var isReferenceHidden = isAnySideFullyClipped2(referenceClippingOffsets);
      var hasPopperEscaped = isAnySideFullyClipped2(popperEscapeOffsets);
      state.modifiersData[name] = {
        referenceClippingOffsets,
        popperEscapeOffsets,
        isReferenceHidden,
        hasPopperEscaped
      };
      state.attributes.popper = Object.assign({}, state.attributes.popper, {
        "data-popper-reference-hidden": isReferenceHidden,
        "data-popper-escaped": hasPopperEscaped
      });
    }
    var hide$1 = {
      name: "hide",
      enabled: true,
      phase: "main",
      requiresIfExists: ["preventOverflow"],
      fn: hide2
    };
    var defaultModifiers$1 = [eventListeners, popperOffsets$1, computeStyles$1, applyStyles$1];
    var createPopper$1 = /* @__PURE__ */ popperGenerator({
      defaultModifiers: defaultModifiers$1
    });
    var defaultModifiers = [eventListeners, popperOffsets$1, computeStyles$1, applyStyles$1, offset$1, flip$1, preventOverflow$1, arrow$1, hide$1];
    var createPopper = /* @__PURE__ */ popperGenerator({
      defaultModifiers
    });
    exports.applyStyles = applyStyles$1;
    exports.arrow = arrow$1;
    exports.computeStyles = computeStyles$1;
    exports.createPopper = createPopper;
    exports.createPopperLite = createPopper$1;
    exports.defaultModifiers = defaultModifiers;
    exports.detectOverflow = detectOverflow2;
    exports.eventListeners = eventListeners;
    exports.flip = flip$1;
    exports.hide = hide$1;
    exports.offset = offset$1;
    exports.popperGenerator = popperGenerator;
    exports.popperOffsets = popperOffsets$1;
    exports.preventOverflow = preventOverflow$1;
  });
  var require_tippy_cjs = __commonJS((exports) => {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    var core = require_popper();
    var ROUND_ARROW = '<svg width="16" height="6" xmlns="http://www.w3.org/2000/svg"><path d="M0 6s1.796-.013 4.67-3.615C5.851.9 6.93.006 8 0c1.07-.006 2.148.887 3.343 2.385C14.233 6.005 16 6 16 6H0z"></svg>';
    var BOX_CLASS = "tippy-box";
    var CONTENT_CLASS = "tippy-content";
    var BACKDROP_CLASS = "tippy-backdrop";
    var ARROW_CLASS = "tippy-arrow";
    var SVG_ARROW_CLASS = "tippy-svg-arrow";
    var TOUCH_OPTIONS = {
      passive: true,
      capture: true
    };
    function hasOwnProperty(obj, key) {
      return {}.hasOwnProperty.call(obj, key);
    }
    function getValueAtIndexOrReturn(value, index2, defaultValue) {
      if (Array.isArray(value)) {
        var v = value[index2];
        return v == null ? Array.isArray(defaultValue) ? defaultValue[index2] : defaultValue : v;
      }
      return value;
    }
    function isType(value, type) {
      var str = {}.toString.call(value);
      return str.indexOf("[object") === 0 && str.indexOf(type + "]") > -1;
    }
    function invokeWithArgsOrReturn(value, args) {
      return typeof value === "function" ? value.apply(void 0, args) : value;
    }
    function debounce(fn, ms) {
      if (ms === 0) {
        return fn;
      }
      var timeout;
      return function(arg) {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
          fn(arg);
        }, ms);
      };
    }
    function removeProperties(obj, keys) {
      var clone2 = Object.assign({}, obj);
      keys.forEach(function(key) {
        delete clone2[key];
      });
      return clone2;
    }
    function splitBySpaces(value) {
      return value.split(/\s+/).filter(Boolean);
    }
    function normalizeToArray(value) {
      return [].concat(value);
    }
    function pushIfUnique(arr, value) {
      if (arr.indexOf(value) === -1) {
        arr.push(value);
      }
    }
    function unique(arr) {
      return arr.filter(function(item, index2) {
        return arr.indexOf(item) === index2;
      });
    }
    function getBasePlacement(placement) {
      return placement.split("-")[0];
    }
    function arrayFrom(value) {
      return [].slice.call(value);
    }
    function removeUndefinedProps(obj) {
      return Object.keys(obj).reduce(function(acc, key) {
        if (obj[key] !== void 0) {
          acc[key] = obj[key];
        }
        return acc;
      }, {});
    }
    function div() {
      return document.createElement("div");
    }
    function isElement2(value) {
      return ["Element", "Fragment"].some(function(type) {
        return isType(value, type);
      });
    }
    function isNodeList(value) {
      return isType(value, "NodeList");
    }
    function isMouseEvent(value) {
      return isType(value, "MouseEvent");
    }
    function isReferenceElement(value) {
      return !!(value && value._tippy && value._tippy.reference === value);
    }
    function getArrayOfElements(value) {
      if (isElement2(value)) {
        return [value];
      }
      if (isNodeList(value)) {
        return arrayFrom(value);
      }
      if (Array.isArray(value)) {
        return value;
      }
      return arrayFrom(document.querySelectorAll(value));
    }
    function setTransitionDuration(els, value) {
      els.forEach(function(el) {
        if (el) {
          el.style.transitionDuration = value + "ms";
        }
      });
    }
    function setVisibilityState(els, state) {
      els.forEach(function(el) {
        if (el) {
          el.setAttribute("data-state", state);
        }
      });
    }
    function getOwnerDocument(elementOrElements) {
      var _element$ownerDocumen;
      var _normalizeToArray = normalizeToArray(elementOrElements), element = _normalizeToArray[0];
      return (element == null ? void 0 : (_element$ownerDocumen = element.ownerDocument) == null ? void 0 : _element$ownerDocumen.body) ? element.ownerDocument : document;
    }
    function isCursorOutsideInteractiveBorder(popperTreeData, event) {
      var clientX = event.clientX, clientY = event.clientY;
      return popperTreeData.every(function(_ref) {
        var popperRect = _ref.popperRect, popperState = _ref.popperState, props = _ref.props;
        var interactiveBorder = props.interactiveBorder;
        var basePlacement = getBasePlacement(popperState.placement);
        var offsetData = popperState.modifiersData.offset;
        if (!offsetData) {
          return true;
        }
        var topDistance = basePlacement === "bottom" ? offsetData.top.y : 0;
        var bottomDistance = basePlacement === "top" ? offsetData.bottom.y : 0;
        var leftDistance = basePlacement === "right" ? offsetData.left.x : 0;
        var rightDistance = basePlacement === "left" ? offsetData.right.x : 0;
        var exceedsTop = popperRect.top - clientY + topDistance > interactiveBorder;
        var exceedsBottom = clientY - popperRect.bottom - bottomDistance > interactiveBorder;
        var exceedsLeft = popperRect.left - clientX + leftDistance > interactiveBorder;
        var exceedsRight = clientX - popperRect.right - rightDistance > interactiveBorder;
        return exceedsTop || exceedsBottom || exceedsLeft || exceedsRight;
      });
    }
    function updateTransitionEndListener(box, action, listener) {
      var method = action + "EventListener";
      ["transitionend", "webkitTransitionEnd"].forEach(function(event) {
        box[method](event, listener);
      });
    }
    var currentInput = {
      isTouch: false
    };
    var lastMouseMoveTime = 0;
    function onDocumentTouchStart() {
      if (currentInput.isTouch) {
        return;
      }
      currentInput.isTouch = true;
      if (window.performance) {
        document.addEventListener("mousemove", onDocumentMouseMove);
      }
    }
    function onDocumentMouseMove() {
      var now = performance.now();
      if (now - lastMouseMoveTime < 20) {
        currentInput.isTouch = false;
        document.removeEventListener("mousemove", onDocumentMouseMove);
      }
      lastMouseMoveTime = now;
    }
    function onWindowBlur() {
      var activeElement = document.activeElement;
      if (isReferenceElement(activeElement)) {
        var instance = activeElement._tippy;
        if (activeElement.blur && !instance.state.isVisible) {
          activeElement.blur();
        }
      }
    }
    function bindGlobalEventListeners() {
      document.addEventListener("touchstart", onDocumentTouchStart, TOUCH_OPTIONS);
      window.addEventListener("blur", onWindowBlur);
    }
    var isBrowser = typeof window !== "undefined" && typeof document !== "undefined";
    var ua = isBrowser ? navigator.userAgent : "";
    var isIE = /MSIE |Trident\//.test(ua);
    function createMemoryLeakWarning(method) {
      var txt = method === "destroy" ? "n already-" : " ";
      return [method + "() was called on a" + txt + "destroyed instance. This is a no-op but", "indicates a potential memory leak."].join(" ");
    }
    function clean(value) {
      var spacesAndTabs = /[ \t]{2,}/g;
      var lineStartWithSpaces = /^[ \t]*/gm;
      return value.replace(spacesAndTabs, " ").replace(lineStartWithSpaces, "").trim();
    }
    function getDevMessage(message) {
      return clean("\n  %ctippy.js\n\n  %c" + clean(message) + "\n\n  %c\u{1F477}\u200D This is a development-only message. It will be removed in production.\n  ");
    }
    function getFormattedMessage(message) {
      return [
        getDevMessage(message),
        "color: #00C584; font-size: 1.3em; font-weight: bold;",
        "line-height: 1.5",
        "color: #a6a095;"
      ];
    }
    var visitedMessages;
    if (true) {
      resetVisitedMessages();
    }
    function resetVisitedMessages() {
      visitedMessages = /* @__PURE__ */ new Set();
    }
    function warnWhen(condition, message) {
      if (condition && !visitedMessages.has(message)) {
        var _console;
        visitedMessages.add(message);
        (_console = console).warn.apply(_console, getFormattedMessage(message));
      }
    }
    function errorWhen(condition, message) {
      if (condition && !visitedMessages.has(message)) {
        var _console2;
        visitedMessages.add(message);
        (_console2 = console).error.apply(_console2, getFormattedMessage(message));
      }
    }
    function validateTargets(targets) {
      var didPassFalsyValue = !targets;
      var didPassPlainObject = Object.prototype.toString.call(targets) === "[object Object]" && !targets.addEventListener;
      errorWhen(didPassFalsyValue, ["tippy() was passed", "`" + String(targets) + "`", "as its targets (first) argument. Valid types are: String, Element,", "Element[], or NodeList."].join(" "));
      errorWhen(didPassPlainObject, ["tippy() was passed a plain object which is not supported as an argument", "for virtual positioning. Use props.getReferenceClientRect instead."].join(" "));
    }
    var pluginProps = {
      animateFill: false,
      followCursor: false,
      inlinePositioning: false,
      sticky: false
    };
    var renderProps = {
      allowHTML: false,
      animation: "fade",
      arrow: true,
      content: "",
      inertia: false,
      maxWidth: 350,
      role: "tooltip",
      theme: "",
      zIndex: 9999
    };
    var defaultProps = Object.assign({
      appendTo: function appendTo() {
        return document.body;
      },
      aria: {
        content: "auto",
        expanded: "auto"
      },
      delay: 0,
      duration: [300, 250],
      getReferenceClientRect: null,
      hideOnClick: true,
      ignoreAttributes: false,
      interactive: false,
      interactiveBorder: 2,
      interactiveDebounce: 0,
      moveTransition: "",
      offset: [0, 10],
      onAfterUpdate: function onAfterUpdate() {
      },
      onBeforeUpdate: function onBeforeUpdate() {
      },
      onCreate: function onCreate() {
      },
      onDestroy: function onDestroy() {
      },
      onHidden: function onHidden() {
      },
      onHide: function onHide() {
      },
      onMount: function onMount() {
      },
      onShow: function onShow() {
      },
      onShown: function onShown() {
      },
      onTrigger: function onTrigger() {
      },
      onUntrigger: function onUntrigger() {
      },
      onClickOutside: function onClickOutside() {
      },
      placement: "top",
      plugins: [],
      popperOptions: {},
      render: null,
      showOnCreate: false,
      touch: true,
      trigger: "mouseenter focus",
      triggerTarget: null
    }, pluginProps, {}, renderProps);
    var defaultKeys = Object.keys(defaultProps);
    var setDefaultProps = function setDefaultProps2(partialProps) {
      if (true) {
        validateProps(partialProps, []);
      }
      var keys = Object.keys(partialProps);
      keys.forEach(function(key) {
        defaultProps[key] = partialProps[key];
      });
    };
    function getExtendedPassedProps(passedProps) {
      var plugins2 = passedProps.plugins || [];
      var pluginProps2 = plugins2.reduce(function(acc, plugin) {
        var name = plugin.name, defaultValue = plugin.defaultValue;
        if (name) {
          acc[name] = passedProps[name] !== void 0 ? passedProps[name] : defaultValue;
        }
        return acc;
      }, {});
      return Object.assign({}, passedProps, {}, pluginProps2);
    }
    function getDataAttributeProps(reference, plugins2) {
      var propKeys = plugins2 ? Object.keys(getExtendedPassedProps(Object.assign({}, defaultProps, {
        plugins: plugins2
      }))) : defaultKeys;
      var props = propKeys.reduce(function(acc, key) {
        var valueAsString = (reference.getAttribute("data-tippy-" + key) || "").trim();
        if (!valueAsString) {
          return acc;
        }
        if (key === "content") {
          acc[key] = valueAsString;
        } else {
          try {
            acc[key] = JSON.parse(valueAsString);
          } catch (e) {
            acc[key] = valueAsString;
          }
        }
        return acc;
      }, {});
      return props;
    }
    function evaluateProps(reference, props) {
      var out = Object.assign({}, props, {
        content: invokeWithArgsOrReturn(props.content, [reference])
      }, props.ignoreAttributes ? {} : getDataAttributeProps(reference, props.plugins));
      out.aria = Object.assign({}, defaultProps.aria, {}, out.aria);
      out.aria = {
        expanded: out.aria.expanded === "auto" ? props.interactive : out.aria.expanded,
        content: out.aria.content === "auto" ? props.interactive ? null : "describedby" : out.aria.content
      };
      return out;
    }
    function validateProps(partialProps, plugins2) {
      if (partialProps === void 0) {
        partialProps = {};
      }
      if (plugins2 === void 0) {
        plugins2 = [];
      }
      var keys = Object.keys(partialProps);
      keys.forEach(function(prop) {
        var nonPluginProps = removeProperties(defaultProps, Object.keys(pluginProps));
        var didPassUnknownProp = !hasOwnProperty(nonPluginProps, prop);
        if (didPassUnknownProp) {
          didPassUnknownProp = plugins2.filter(function(plugin) {
            return plugin.name === prop;
          }).length === 0;
        }
        warnWhen(didPassUnknownProp, ["`" + prop + "`", "is not a valid prop. You may have spelled it incorrectly, or if it's", "a plugin, forgot to pass it in an array as props.plugins.", "\n\n", "All props: https://atomiks.github.io/tippyjs/v6/all-props/\n", "Plugins: https://atomiks.github.io/tippyjs/v6/plugins/"].join(" "));
      });
    }
    var innerHTML = function innerHTML2() {
      return "innerHTML";
    };
    function dangerouslySetInnerHTML(element, html) {
      element[innerHTML()] = html;
    }
    function createArrowElement(value) {
      var arrow2 = div();
      if (value === true) {
        arrow2.className = ARROW_CLASS;
      } else {
        arrow2.className = SVG_ARROW_CLASS;
        if (isElement2(value)) {
          arrow2.appendChild(value);
        } else {
          dangerouslySetInnerHTML(arrow2, value);
        }
      }
      return arrow2;
    }
    function setContent(content, props) {
      if (isElement2(props.content)) {
        dangerouslySetInnerHTML(content, "");
        content.appendChild(props.content);
      } else if (typeof props.content !== "function") {
        if (props.allowHTML) {
          dangerouslySetInnerHTML(content, props.content);
        } else {
          content.textContent = props.content;
        }
      }
    }
    function getChildren(popper) {
      var box = popper.firstElementChild;
      var boxChildren = arrayFrom(box.children);
      return {
        box,
        content: boxChildren.find(function(node) {
          return node.classList.contains(CONTENT_CLASS);
        }),
        arrow: boxChildren.find(function(node) {
          return node.classList.contains(ARROW_CLASS) || node.classList.contains(SVG_ARROW_CLASS);
        }),
        backdrop: boxChildren.find(function(node) {
          return node.classList.contains(BACKDROP_CLASS);
        })
      };
    }
    function render(instance) {
      var popper = div();
      var box = div();
      box.className = BOX_CLASS;
      box.setAttribute("data-state", "hidden");
      box.setAttribute("tabindex", "-1");
      var content = div();
      content.className = CONTENT_CLASS;
      content.setAttribute("data-state", "hidden");
      setContent(content, instance.props);
      popper.appendChild(box);
      box.appendChild(content);
      onUpdate(instance.props, instance.props);
      function onUpdate(prevProps, nextProps) {
        var _getChildren = getChildren(popper), box2 = _getChildren.box, content2 = _getChildren.content, arrow2 = _getChildren.arrow;
        if (nextProps.theme) {
          box2.setAttribute("data-theme", nextProps.theme);
        } else {
          box2.removeAttribute("data-theme");
        }
        if (typeof nextProps.animation === "string") {
          box2.setAttribute("data-animation", nextProps.animation);
        } else {
          box2.removeAttribute("data-animation");
        }
        if (nextProps.inertia) {
          box2.setAttribute("data-inertia", "");
        } else {
          box2.removeAttribute("data-inertia");
        }
        box2.style.maxWidth = typeof nextProps.maxWidth === "number" ? nextProps.maxWidth + "px" : nextProps.maxWidth;
        if (nextProps.role) {
          box2.setAttribute("role", nextProps.role);
        } else {
          box2.removeAttribute("role");
        }
        if (prevProps.content !== nextProps.content || prevProps.allowHTML !== nextProps.allowHTML) {
          setContent(content2, instance.props);
        }
        if (nextProps.arrow) {
          if (!arrow2) {
            box2.appendChild(createArrowElement(nextProps.arrow));
          } else if (prevProps.arrow !== nextProps.arrow) {
            box2.removeChild(arrow2);
            box2.appendChild(createArrowElement(nextProps.arrow));
          }
        } else if (arrow2) {
          box2.removeChild(arrow2);
        }
      }
      return {
        popper,
        onUpdate
      };
    }
    render.$$tippy = true;
    var idCounter = 1;
    var mouseMoveListeners = [];
    var mountedInstances = [];
    function createTippy(reference, passedProps) {
      var props = evaluateProps(reference, Object.assign({}, defaultProps, {}, getExtendedPassedProps(removeUndefinedProps(passedProps))));
      var showTimeout;
      var hideTimeout;
      var scheduleHideAnimationFrame;
      var isVisibleFromClick = false;
      var didHideDueToDocumentMouseDown = false;
      var didTouchMove = false;
      var ignoreOnFirstUpdate = false;
      var lastTriggerEvent;
      var currentTransitionEndListener;
      var onFirstUpdate;
      var listeners = [];
      var debouncedOnMouseMove = debounce(onMouseMove, props.interactiveDebounce);
      var currentTarget;
      var id = idCounter++;
      var popperInstance = null;
      var plugins2 = unique(props.plugins);
      var state = {
        isEnabled: true,
        isVisible: false,
        isDestroyed: false,
        isMounted: false,
        isShown: false
      };
      var instance = {
        id,
        reference,
        popper: div(),
        popperInstance,
        props,
        state,
        plugins: plugins2,
        clearDelayTimeouts,
        setProps,
        setContent: setContent2,
        show,
        hide: hide2,
        hideWithInteractivity,
        enable,
        disable,
        unmount,
        destroy: destroy2
      };
      if (!props.render) {
        if (true) {
          errorWhen(true, "render() function has not been supplied.");
        }
        return instance;
      }
      var _props$render = props.render(instance), popper = _props$render.popper, onUpdate = _props$render.onUpdate;
      popper.setAttribute("data-tippy-root", "");
      popper.id = "tippy-" + instance.id;
      instance.popper = popper;
      reference._tippy = instance;
      popper._tippy = instance;
      var pluginsHooks = plugins2.map(function(plugin) {
        return plugin.fn(instance);
      });
      var hasAriaExpanded = reference.hasAttribute("aria-expanded");
      addListeners();
      handleAriaExpandedAttribute();
      handleStyles();
      invokeHook("onCreate", [instance]);
      if (props.showOnCreate) {
        scheduleShow();
      }
      popper.addEventListener("mouseenter", function() {
        if (instance.props.interactive && instance.state.isVisible) {
          instance.clearDelayTimeouts();
        }
      });
      popper.addEventListener("mouseleave", function(event) {
        if (instance.props.interactive && instance.props.trigger.indexOf("mouseenter") >= 0) {
          getDocument().addEventListener("mousemove", debouncedOnMouseMove);
          debouncedOnMouseMove(event);
        }
      });
      return instance;
      function getNormalizedTouchSettings() {
        var touch = instance.props.touch;
        return Array.isArray(touch) ? touch : [touch, 0];
      }
      function getIsCustomTouchBehavior() {
        return getNormalizedTouchSettings()[0] === "hold";
      }
      function getIsDefaultRenderFn() {
        var _instance$props$rende;
        return !!((_instance$props$rende = instance.props.render) == null ? void 0 : _instance$props$rende.$$tippy);
      }
      function getCurrentTarget() {
        return currentTarget || reference;
      }
      function getDocument() {
        var parent = getCurrentTarget().parentNode;
        return parent ? getOwnerDocument(parent) : document;
      }
      function getDefaultTemplateChildren() {
        return getChildren(popper);
      }
      function getDelay(isShow) {
        if (instance.state.isMounted && !instance.state.isVisible || currentInput.isTouch || lastTriggerEvent && lastTriggerEvent.type === "focus") {
          return 0;
        }
        return getValueAtIndexOrReturn(instance.props.delay, isShow ? 0 : 1, defaultProps.delay);
      }
      function handleStyles() {
        popper.style.pointerEvents = instance.props.interactive && instance.state.isVisible ? "" : "none";
        popper.style.zIndex = "" + instance.props.zIndex;
      }
      function invokeHook(hook, args, shouldInvokePropsHook) {
        if (shouldInvokePropsHook === void 0) {
          shouldInvokePropsHook = true;
        }
        pluginsHooks.forEach(function(pluginHooks) {
          if (pluginHooks[hook]) {
            pluginHooks[hook].apply(void 0, args);
          }
        });
        if (shouldInvokePropsHook) {
          var _instance$props;
          (_instance$props = instance.props)[hook].apply(_instance$props, args);
        }
      }
      function handleAriaContentAttribute() {
        var aria = instance.props.aria;
        if (!aria.content) {
          return;
        }
        var attr = "aria-" + aria.content;
        var id2 = popper.id;
        var nodes = normalizeToArray(instance.props.triggerTarget || reference);
        nodes.forEach(function(node) {
          var currentValue = node.getAttribute(attr);
          if (instance.state.isVisible) {
            node.setAttribute(attr, currentValue ? currentValue + " " + id2 : id2);
          } else {
            var nextValue = currentValue && currentValue.replace(id2, "").trim();
            if (nextValue) {
              node.setAttribute(attr, nextValue);
            } else {
              node.removeAttribute(attr);
            }
          }
        });
      }
      function handleAriaExpandedAttribute() {
        if (hasAriaExpanded || !instance.props.aria.expanded) {
          return;
        }
        var nodes = normalizeToArray(instance.props.triggerTarget || reference);
        nodes.forEach(function(node) {
          if (instance.props.interactive) {
            node.setAttribute("aria-expanded", instance.state.isVisible && node === getCurrentTarget() ? "true" : "false");
          } else {
            node.removeAttribute("aria-expanded");
          }
        });
      }
      function cleanupInteractiveMouseListeners() {
        getDocument().removeEventListener("mousemove", debouncedOnMouseMove);
        mouseMoveListeners = mouseMoveListeners.filter(function(listener) {
          return listener !== debouncedOnMouseMove;
        });
      }
      function onDocumentPress(event) {
        if (currentInput.isTouch) {
          if (didTouchMove || event.type === "mousedown") {
            return;
          }
        }
        if (instance.props.interactive && popper.contains(event.target)) {
          return;
        }
        if (getCurrentTarget().contains(event.target)) {
          if (currentInput.isTouch) {
            return;
          }
          if (instance.state.isVisible && instance.props.trigger.indexOf("click") >= 0) {
            return;
          }
        } else {
          invokeHook("onClickOutside", [instance, event]);
        }
        if (instance.props.hideOnClick === true) {
          instance.clearDelayTimeouts();
          instance.hide();
          didHideDueToDocumentMouseDown = true;
          setTimeout(function() {
            didHideDueToDocumentMouseDown = false;
          });
          if (!instance.state.isMounted) {
            removeDocumentPress();
          }
        }
      }
      function onTouchMove() {
        didTouchMove = true;
      }
      function onTouchStart() {
        didTouchMove = false;
      }
      function addDocumentPress() {
        var doc = getDocument();
        doc.addEventListener("mousedown", onDocumentPress, true);
        doc.addEventListener("touchend", onDocumentPress, TOUCH_OPTIONS);
        doc.addEventListener("touchstart", onTouchStart, TOUCH_OPTIONS);
        doc.addEventListener("touchmove", onTouchMove, TOUCH_OPTIONS);
      }
      function removeDocumentPress() {
        var doc = getDocument();
        doc.removeEventListener("mousedown", onDocumentPress, true);
        doc.removeEventListener("touchend", onDocumentPress, TOUCH_OPTIONS);
        doc.removeEventListener("touchstart", onTouchStart, TOUCH_OPTIONS);
        doc.removeEventListener("touchmove", onTouchMove, TOUCH_OPTIONS);
      }
      function onTransitionedOut(duration, callback) {
        onTransitionEnd(duration, function() {
          if (!instance.state.isVisible && popper.parentNode && popper.parentNode.contains(popper)) {
            callback();
          }
        });
      }
      function onTransitionedIn(duration, callback) {
        onTransitionEnd(duration, callback);
      }
      function onTransitionEnd(duration, callback) {
        var box = getDefaultTemplateChildren().box;
        function listener(event) {
          if (event.target === box) {
            updateTransitionEndListener(box, "remove", listener);
            callback();
          }
        }
        if (duration === 0) {
          return callback();
        }
        updateTransitionEndListener(box, "remove", currentTransitionEndListener);
        updateTransitionEndListener(box, "add", listener);
        currentTransitionEndListener = listener;
      }
      function on2(eventType, handler, options) {
        if (options === void 0) {
          options = false;
        }
        var nodes = normalizeToArray(instance.props.triggerTarget || reference);
        nodes.forEach(function(node) {
          node.addEventListener(eventType, handler, options);
          listeners.push({
            node,
            eventType,
            handler,
            options
          });
        });
      }
      function addListeners() {
        if (getIsCustomTouchBehavior()) {
          on2("touchstart", onTrigger, {
            passive: true
          });
          on2("touchend", onMouseLeave, {
            passive: true
          });
        }
        splitBySpaces(instance.props.trigger).forEach(function(eventType) {
          if (eventType === "manual") {
            return;
          }
          on2(eventType, onTrigger);
          switch (eventType) {
            case "mouseenter":
              on2("mouseleave", onMouseLeave);
              break;
            case "focus":
              on2(isIE ? "focusout" : "blur", onBlurOrFocusOut);
              break;
            case "focusin":
              on2("focusout", onBlurOrFocusOut);
              break;
          }
        });
      }
      function removeListeners() {
        listeners.forEach(function(_ref) {
          var node = _ref.node, eventType = _ref.eventType, handler = _ref.handler, options = _ref.options;
          node.removeEventListener(eventType, handler, options);
        });
        listeners = [];
      }
      function onTrigger(event) {
        var _lastTriggerEvent;
        var shouldScheduleClickHide = false;
        if (!instance.state.isEnabled || isEventListenerStopped(event) || didHideDueToDocumentMouseDown) {
          return;
        }
        var wasFocused = ((_lastTriggerEvent = lastTriggerEvent) == null ? void 0 : _lastTriggerEvent.type) === "focus";
        lastTriggerEvent = event;
        currentTarget = event.currentTarget;
        handleAriaExpandedAttribute();
        if (!instance.state.isVisible && isMouseEvent(event)) {
          mouseMoveListeners.forEach(function(listener) {
            return listener(event);
          });
        }
        if (event.type === "click" && (instance.props.trigger.indexOf("mouseenter") < 0 || isVisibleFromClick) && instance.props.hideOnClick !== false && instance.state.isVisible) {
          shouldScheduleClickHide = true;
        } else {
          scheduleShow(event);
        }
        if (event.type === "click") {
          isVisibleFromClick = !shouldScheduleClickHide;
        }
        if (shouldScheduleClickHide && !wasFocused) {
          scheduleHide(event);
        }
      }
      function onMouseMove(event) {
        var target = event.target;
        var isCursorOverReferenceOrPopper = getCurrentTarget().contains(target) || popper.contains(target);
        if (event.type === "mousemove" && isCursorOverReferenceOrPopper) {
          return;
        }
        var popperTreeData = getNestedPopperTree().concat(popper).map(function(popper2) {
          var _instance$popperInsta;
          var instance2 = popper2._tippy;
          var state2 = (_instance$popperInsta = instance2.popperInstance) == null ? void 0 : _instance$popperInsta.state;
          if (state2) {
            return {
              popperRect: popper2.getBoundingClientRect(),
              popperState: state2,
              props
            };
          }
          return null;
        }).filter(Boolean);
        if (isCursorOutsideInteractiveBorder(popperTreeData, event)) {
          cleanupInteractiveMouseListeners();
          scheduleHide(event);
        }
      }
      function onMouseLeave(event) {
        var shouldBail = isEventListenerStopped(event) || instance.props.trigger.indexOf("click") >= 0 && isVisibleFromClick;
        if (shouldBail) {
          return;
        }
        if (instance.props.interactive) {
          instance.hideWithInteractivity(event);
          return;
        }
        scheduleHide(event);
      }
      function onBlurOrFocusOut(event) {
        if (instance.props.trigger.indexOf("focusin") < 0 && event.target !== getCurrentTarget()) {
          return;
        }
        if (instance.props.interactive && event.relatedTarget && popper.contains(event.relatedTarget)) {
          return;
        }
        scheduleHide(event);
      }
      function isEventListenerStopped(event) {
        return currentInput.isTouch ? getIsCustomTouchBehavior() !== event.type.indexOf("touch") >= 0 : false;
      }
      function createPopperInstance() {
        destroyPopperInstance();
        var _instance$props2 = instance.props, popperOptions = _instance$props2.popperOptions, placement = _instance$props2.placement, offset2 = _instance$props2.offset, getReferenceClientRect = _instance$props2.getReferenceClientRect, moveTransition = _instance$props2.moveTransition;
        var arrow2 = getIsDefaultRenderFn() ? getChildren(popper).arrow : null;
        var computedReference = getReferenceClientRect ? {
          getBoundingClientRect: getReferenceClientRect,
          contextElement: getReferenceClientRect.contextElement || getCurrentTarget()
        } : reference;
        var tippyModifier = {
          name: "$$tippy",
          enabled: true,
          phase: "beforeWrite",
          requires: ["computeStyles"],
          fn: function fn(_ref2) {
            var state2 = _ref2.state;
            if (getIsDefaultRenderFn()) {
              var _getDefaultTemplateCh = getDefaultTemplateChildren(), box = _getDefaultTemplateCh.box;
              ["placement", "reference-hidden", "escaped"].forEach(function(attr) {
                if (attr === "placement") {
                  box.setAttribute("data-placement", state2.placement);
                } else {
                  if (state2.attributes.popper["data-popper-" + attr]) {
                    box.setAttribute("data-" + attr, "");
                  } else {
                    box.removeAttribute("data-" + attr);
                  }
                }
              });
              state2.attributes.popper = {};
            }
          }
        };
        var modifiers = [{
          name: "offset",
          options: {
            offset: offset2
          }
        }, {
          name: "preventOverflow",
          options: {
            padding: {
              top: 2,
              bottom: 2,
              left: 5,
              right: 5
            }
          }
        }, {
          name: "flip",
          options: {
            padding: 5
          }
        }, {
          name: "computeStyles",
          options: {
            adaptive: !moveTransition
          }
        }, tippyModifier];
        if (getIsDefaultRenderFn() && arrow2) {
          modifiers.push({
            name: "arrow",
            options: {
              element: arrow2,
              padding: 3
            }
          });
        }
        modifiers.push.apply(modifiers, (popperOptions == null ? void 0 : popperOptions.modifiers) || []);
        instance.popperInstance = core.createPopper(computedReference, popper, Object.assign({}, popperOptions, {
          placement,
          onFirstUpdate,
          modifiers
        }));
      }
      function destroyPopperInstance() {
        if (instance.popperInstance) {
          instance.popperInstance.destroy();
          instance.popperInstance = null;
        }
      }
      function mount2() {
        var appendTo = instance.props.appendTo;
        var parentNode;
        var node = getCurrentTarget();
        if (instance.props.interactive && appendTo === defaultProps.appendTo || appendTo === "parent") {
          parentNode = node.parentNode;
        } else {
          parentNode = invokeWithArgsOrReturn(appendTo, [node]);
        }
        if (!parentNode.contains(popper)) {
          parentNode.appendChild(popper);
        }
        createPopperInstance();
        if (true) {
          warnWhen(instance.props.interactive && appendTo === defaultProps.appendTo && node.nextElementSibling !== popper, ["Interactive tippy element may not be accessible via keyboard", "navigation because it is not directly after the reference element", "in the DOM source order.", "\n\n", "Using a wrapper <div> or <span> tag around the reference element", "solves this by creating a new parentNode context.", "\n\n", "Specifying `appendTo: document.body` silences this warning, but it", "assumes you are using a focus management solution to handle", "keyboard navigation.", "\n\n", "See: https://atomiks.github.io/tippyjs/v6/accessibility/#interactivity"].join(" "));
        }
      }
      function getNestedPopperTree() {
        return arrayFrom(popper.querySelectorAll("[data-tippy-root]"));
      }
      function scheduleShow(event) {
        instance.clearDelayTimeouts();
        if (event) {
          invokeHook("onTrigger", [instance, event]);
        }
        addDocumentPress();
        var delay = getDelay(true);
        var _getNormalizedTouchSe = getNormalizedTouchSettings(), touchValue = _getNormalizedTouchSe[0], touchDelay = _getNormalizedTouchSe[1];
        if (currentInput.isTouch && touchValue === "hold" && touchDelay) {
          delay = touchDelay;
        }
        if (delay) {
          showTimeout = setTimeout(function() {
            instance.show();
          }, delay);
        } else {
          instance.show();
        }
      }
      function scheduleHide(event) {
        instance.clearDelayTimeouts();
        invokeHook("onUntrigger", [instance, event]);
        if (!instance.state.isVisible) {
          removeDocumentPress();
          return;
        }
        if (instance.props.trigger.indexOf("mouseenter") >= 0 && instance.props.trigger.indexOf("click") >= 0 && ["mouseleave", "mousemove"].indexOf(event.type) >= 0 && isVisibleFromClick) {
          return;
        }
        var delay = getDelay(false);
        if (delay) {
          hideTimeout = setTimeout(function() {
            if (instance.state.isVisible) {
              instance.hide();
            }
          }, delay);
        } else {
          scheduleHideAnimationFrame = requestAnimationFrame(function() {
            instance.hide();
          });
        }
      }
      function enable() {
        instance.state.isEnabled = true;
      }
      function disable() {
        instance.hide();
        instance.state.isEnabled = false;
      }
      function clearDelayTimeouts() {
        clearTimeout(showTimeout);
        clearTimeout(hideTimeout);
        cancelAnimationFrame(scheduleHideAnimationFrame);
      }
      function setProps(partialProps) {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("setProps"));
        }
        if (instance.state.isDestroyed) {
          return;
        }
        invokeHook("onBeforeUpdate", [instance, partialProps]);
        removeListeners();
        var prevProps = instance.props;
        var nextProps = evaluateProps(reference, Object.assign({}, instance.props, {}, partialProps, {
          ignoreAttributes: true
        }));
        instance.props = nextProps;
        addListeners();
        if (prevProps.interactiveDebounce !== nextProps.interactiveDebounce) {
          cleanupInteractiveMouseListeners();
          debouncedOnMouseMove = debounce(onMouseMove, nextProps.interactiveDebounce);
        }
        if (prevProps.triggerTarget && !nextProps.triggerTarget) {
          normalizeToArray(prevProps.triggerTarget).forEach(function(node) {
            node.removeAttribute("aria-expanded");
          });
        } else if (nextProps.triggerTarget) {
          reference.removeAttribute("aria-expanded");
        }
        handleAriaExpandedAttribute();
        handleStyles();
        if (onUpdate) {
          onUpdate(prevProps, nextProps);
        }
        if (instance.popperInstance) {
          createPopperInstance();
          getNestedPopperTree().forEach(function(nestedPopper) {
            requestAnimationFrame(nestedPopper._tippy.popperInstance.forceUpdate);
          });
        }
        invokeHook("onAfterUpdate", [instance, partialProps]);
      }
      function setContent2(content) {
        instance.setProps({
          content
        });
      }
      function show() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("show"));
        }
        var isAlreadyVisible = instance.state.isVisible;
        var isDestroyed = instance.state.isDestroyed;
        var isDisabled = !instance.state.isEnabled;
        var isTouchAndTouchDisabled = currentInput.isTouch && !instance.props.touch;
        var duration = getValueAtIndexOrReturn(instance.props.duration, 0, defaultProps.duration);
        if (isAlreadyVisible || isDestroyed || isDisabled || isTouchAndTouchDisabled) {
          return;
        }
        if (getCurrentTarget().hasAttribute("disabled")) {
          return;
        }
        invokeHook("onShow", [instance], false);
        if (instance.props.onShow(instance) === false) {
          return;
        }
        instance.state.isVisible = true;
        if (getIsDefaultRenderFn()) {
          popper.style.visibility = "visible";
        }
        handleStyles();
        addDocumentPress();
        if (!instance.state.isMounted) {
          popper.style.transition = "none";
        }
        if (getIsDefaultRenderFn()) {
          var _getDefaultTemplateCh2 = getDefaultTemplateChildren(), box = _getDefaultTemplateCh2.box, content = _getDefaultTemplateCh2.content;
          setTransitionDuration([box, content], 0);
        }
        onFirstUpdate = function onFirstUpdate2() {
          var _instance$popperInsta2;
          if (!instance.state.isVisible || ignoreOnFirstUpdate) {
            return;
          }
          ignoreOnFirstUpdate = true;
          void popper.offsetHeight;
          popper.style.transition = instance.props.moveTransition;
          if (getIsDefaultRenderFn() && instance.props.animation) {
            var _getDefaultTemplateCh3 = getDefaultTemplateChildren(), _box = _getDefaultTemplateCh3.box, _content = _getDefaultTemplateCh3.content;
            setTransitionDuration([_box, _content], duration);
            setVisibilityState([_box, _content], "visible");
          }
          handleAriaContentAttribute();
          handleAriaExpandedAttribute();
          pushIfUnique(mountedInstances, instance);
          (_instance$popperInsta2 = instance.popperInstance) == null ? void 0 : _instance$popperInsta2.forceUpdate();
          instance.state.isMounted = true;
          invokeHook("onMount", [instance]);
          if (instance.props.animation && getIsDefaultRenderFn()) {
            onTransitionedIn(duration, function() {
              instance.state.isShown = true;
              invokeHook("onShown", [instance]);
            });
          }
        };
        mount2();
      }
      function hide2() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("hide"));
        }
        var isAlreadyHidden = !instance.state.isVisible;
        var isDestroyed = instance.state.isDestroyed;
        var isDisabled = !instance.state.isEnabled;
        var duration = getValueAtIndexOrReturn(instance.props.duration, 1, defaultProps.duration);
        if (isAlreadyHidden || isDestroyed || isDisabled) {
          return;
        }
        invokeHook("onHide", [instance], false);
        if (instance.props.onHide(instance) === false) {
          return;
        }
        instance.state.isVisible = false;
        instance.state.isShown = false;
        ignoreOnFirstUpdate = false;
        isVisibleFromClick = false;
        if (getIsDefaultRenderFn()) {
          popper.style.visibility = "hidden";
        }
        cleanupInteractiveMouseListeners();
        removeDocumentPress();
        handleStyles();
        if (getIsDefaultRenderFn()) {
          var _getDefaultTemplateCh4 = getDefaultTemplateChildren(), box = _getDefaultTemplateCh4.box, content = _getDefaultTemplateCh4.content;
          if (instance.props.animation) {
            setTransitionDuration([box, content], duration);
            setVisibilityState([box, content], "hidden");
          }
        }
        handleAriaContentAttribute();
        handleAriaExpandedAttribute();
        if (instance.props.animation) {
          if (getIsDefaultRenderFn()) {
            onTransitionedOut(duration, instance.unmount);
          }
        } else {
          instance.unmount();
        }
      }
      function hideWithInteractivity(event) {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("hideWithInteractivity"));
        }
        getDocument().addEventListener("mousemove", debouncedOnMouseMove);
        pushIfUnique(mouseMoveListeners, debouncedOnMouseMove);
        debouncedOnMouseMove(event);
      }
      function unmount() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("unmount"));
        }
        if (instance.state.isVisible) {
          instance.hide();
        }
        if (!instance.state.isMounted) {
          return;
        }
        destroyPopperInstance();
        getNestedPopperTree().forEach(function(nestedPopper) {
          nestedPopper._tippy.unmount();
        });
        if (popper.parentNode) {
          popper.parentNode.removeChild(popper);
        }
        mountedInstances = mountedInstances.filter(function(i) {
          return i !== instance;
        });
        instance.state.isMounted = false;
        invokeHook("onHidden", [instance]);
      }
      function destroy2() {
        if (true) {
          warnWhen(instance.state.isDestroyed, createMemoryLeakWarning("destroy"));
        }
        if (instance.state.isDestroyed) {
          return;
        }
        instance.clearDelayTimeouts();
        instance.unmount();
        removeListeners();
        delete reference._tippy;
        instance.state.isDestroyed = true;
        invokeHook("onDestroy", [instance]);
      }
    }
    function tippy2(targets, optionalProps) {
      if (optionalProps === void 0) {
        optionalProps = {};
      }
      var plugins2 = defaultProps.plugins.concat(optionalProps.plugins || []);
      if (true) {
        validateTargets(targets);
        validateProps(optionalProps, plugins2);
      }
      bindGlobalEventListeners();
      var passedProps = Object.assign({}, optionalProps, {
        plugins: plugins2
      });
      var elements = getArrayOfElements(targets);
      if (true) {
        var isSingleContentElement = isElement2(passedProps.content);
        var isMoreThanOneReferenceElement = elements.length > 1;
        warnWhen(isSingleContentElement && isMoreThanOneReferenceElement, ["tippy() was passed an Element as the `content` prop, but more than", "one tippy instance was created by this invocation. This means the", "content element will only be appended to the last tippy instance.", "\n\n", "Instead, pass the .innerHTML of the element, or use a function that", "returns a cloned version of the element instead.", "\n\n", "1) content: element.innerHTML\n", "2) content: () => element.cloneNode(true)"].join(" "));
      }
      var instances = elements.reduce(function(acc, reference) {
        var instance = reference && createTippy(reference, passedProps);
        if (instance) {
          acc.push(instance);
        }
        return acc;
      }, []);
      return isElement2(targets) ? instances[0] : instances;
    }
    tippy2.defaultProps = defaultProps;
    tippy2.setDefaultProps = setDefaultProps;
    tippy2.currentInput = currentInput;
    var hideAll = function hideAll2(_temp) {
      var _ref = _temp === void 0 ? {} : _temp, excludedReferenceOrInstance = _ref.exclude, duration = _ref.duration;
      mountedInstances.forEach(function(instance) {
        var isExcluded = false;
        if (excludedReferenceOrInstance) {
          isExcluded = isReferenceElement(excludedReferenceOrInstance) ? instance.reference === excludedReferenceOrInstance : instance.popper === excludedReferenceOrInstance.popper;
        }
        if (!isExcluded) {
          var originalDuration = instance.props.duration;
          instance.setProps({
            duration
          });
          instance.hide();
          if (!instance.state.isDestroyed) {
            instance.setProps({
              duration: originalDuration
            });
          }
        }
      });
    };
    var applyStylesModifier = Object.assign({}, core.applyStyles, {
      effect: function effect(_ref) {
        var state = _ref.state;
        var initialStyles = {
          popper: {
            position: state.options.strategy,
            left: "0",
            top: "0",
            margin: "0"
          },
          arrow: {
            position: "absolute"
          },
          reference: {}
        };
        Object.assign(state.elements.popper.style, initialStyles.popper);
        state.styles = initialStyles;
        if (state.elements.arrow) {
          Object.assign(state.elements.arrow.style, initialStyles.arrow);
        }
      }
    });
    var createSingleton = function createSingleton2(tippyInstances, optionalProps) {
      var _optionalProps$popper;
      if (optionalProps === void 0) {
        optionalProps = {};
      }
      if (true) {
        errorWhen(!Array.isArray(tippyInstances), ["The first argument passed to createSingleton() must be an array of", "tippy instances. The passed value was", String(tippyInstances)].join(" "));
      }
      var individualInstances = tippyInstances;
      var references = [];
      var currentTarget;
      var overrides = optionalProps.overrides;
      var interceptSetPropsCleanups = [];
      var shownOnCreate = false;
      function setReferences() {
        references = individualInstances.map(function(instance) {
          return instance.reference;
        });
      }
      function enableInstances(isEnabled) {
        individualInstances.forEach(function(instance) {
          if (isEnabled) {
            instance.enable();
          } else {
            instance.disable();
          }
        });
      }
      function interceptSetProps(singleton2) {
        return individualInstances.map(function(instance) {
          var originalSetProps2 = instance.setProps;
          instance.setProps = function(props) {
            originalSetProps2(props);
            if (instance.reference === currentTarget) {
              singleton2.setProps(props);
            }
          };
          return function() {
            instance.setProps = originalSetProps2;
          };
        });
      }
      function prepareInstance(singleton2, target) {
        var index2 = references.indexOf(target);
        if (target === currentTarget) {
          return;
        }
        currentTarget = target;
        var overrideProps = (overrides || []).concat("content").reduce(function(acc, prop) {
          acc[prop] = individualInstances[index2].props[prop];
          return acc;
        }, {});
        singleton2.setProps(Object.assign({}, overrideProps, {
          getReferenceClientRect: typeof overrideProps.getReferenceClientRect === "function" ? overrideProps.getReferenceClientRect : function() {
            return target.getBoundingClientRect();
          }
        }));
      }
      enableInstances(false);
      setReferences();
      var plugin = {
        fn: function fn() {
          return {
            onDestroy: function onDestroy() {
              enableInstances(true);
            },
            onHidden: function onHidden() {
              currentTarget = null;
            },
            onClickOutside: function onClickOutside(instance) {
              if (instance.props.showOnCreate && !shownOnCreate) {
                shownOnCreate = true;
                currentTarget = null;
              }
            },
            onShow: function onShow(instance) {
              if (instance.props.showOnCreate && !shownOnCreate) {
                shownOnCreate = true;
                prepareInstance(instance, references[0]);
              }
            },
            onTrigger: function onTrigger(instance, event) {
              prepareInstance(instance, event.currentTarget);
            }
          };
        }
      };
      var singleton = tippy2(div(), Object.assign({}, removeProperties(optionalProps, ["overrides"]), {
        plugins: [plugin].concat(optionalProps.plugins || []),
        triggerTarget: references,
        popperOptions: Object.assign({}, optionalProps.popperOptions, {
          modifiers: [].concat(((_optionalProps$popper = optionalProps.popperOptions) == null ? void 0 : _optionalProps$popper.modifiers) || [], [applyStylesModifier])
        })
      }));
      var originalShow = singleton.show;
      singleton.show = function(target) {
        originalShow();
        if (!currentTarget && target == null) {
          return prepareInstance(singleton, references[0]);
        }
        if (currentTarget && target == null) {
          return;
        }
        if (typeof target === "number") {
          return references[target] && prepareInstance(singleton, references[target]);
        }
        if (individualInstances.includes(target)) {
          var ref = target.reference;
          return prepareInstance(singleton, ref);
        }
        if (references.includes(target)) {
          return prepareInstance(singleton, target);
        }
      };
      singleton.showNext = function() {
        var first = references[0];
        if (!currentTarget) {
          return singleton.show(0);
        }
        var index2 = references.indexOf(currentTarget);
        singleton.show(references[index2 + 1] || first);
      };
      singleton.showPrevious = function() {
        var last = references[references.length - 1];
        if (!currentTarget) {
          return singleton.show(last);
        }
        var index2 = references.indexOf(currentTarget);
        var target = references[index2 - 1] || last;
        singleton.show(target);
      };
      var originalSetProps = singleton.setProps;
      singleton.setProps = function(props) {
        overrides = props.overrides || overrides;
        originalSetProps(props);
      };
      singleton.setInstances = function(nextInstances) {
        enableInstances(true);
        interceptSetPropsCleanups.forEach(function(fn) {
          return fn();
        });
        individualInstances = nextInstances;
        enableInstances(false);
        setReferences();
        interceptSetProps(singleton);
        singleton.setProps({
          triggerTarget: references
        });
      };
      interceptSetPropsCleanups = interceptSetProps(singleton);
      return singleton;
    };
    var BUBBLING_EVENTS_MAP = {
      mouseover: "mouseenter",
      focusin: "focus",
      click: "click"
    };
    function delegate(targets, props) {
      if (true) {
        errorWhen(!(props && props.target), ["You must specity a `target` prop indicating a CSS selector string matching", "the target elements that should receive a tippy."].join(" "));
      }
      var listeners = [];
      var childTippyInstances = [];
      var disabled = false;
      var target = props.target;
      var nativeProps = removeProperties(props, ["target"]);
      var parentProps = Object.assign({}, nativeProps, {
        trigger: "manual",
        touch: false
      });
      var childProps = Object.assign({}, nativeProps, {
        showOnCreate: true
      });
      var returnValue = tippy2(targets, parentProps);
      var normalizedReturnValue = normalizeToArray(returnValue);
      function onTrigger(event) {
        if (!event.target || disabled) {
          return;
        }
        var targetNode = event.target.closest(target);
        if (!targetNode) {
          return;
        }
        var trigger = targetNode.getAttribute("data-tippy-trigger") || props.trigger || defaultProps.trigger;
        if (targetNode._tippy) {
          return;
        }
        if (event.type === "touchstart" && typeof childProps.touch === "boolean") {
          return;
        }
        if (event.type !== "touchstart" && trigger.indexOf(BUBBLING_EVENTS_MAP[event.type]) < 0) {
          return;
        }
        var instance = tippy2(targetNode, childProps);
        if (instance) {
          childTippyInstances = childTippyInstances.concat(instance);
        }
      }
      function on2(node, eventType, handler, options) {
        if (options === void 0) {
          options = false;
        }
        node.addEventListener(eventType, handler, options);
        listeners.push({
          node,
          eventType,
          handler,
          options
        });
      }
      function addEventListeners(instance) {
        var reference = instance.reference;
        on2(reference, "touchstart", onTrigger, TOUCH_OPTIONS);
        on2(reference, "mouseover", onTrigger);
        on2(reference, "focusin", onTrigger);
        on2(reference, "click", onTrigger);
      }
      function removeEventListeners() {
        listeners.forEach(function(_ref) {
          var node = _ref.node, eventType = _ref.eventType, handler = _ref.handler, options = _ref.options;
          node.removeEventListener(eventType, handler, options);
        });
        listeners = [];
      }
      function applyMutations(instance) {
        var originalDestroy = instance.destroy;
        var originalEnable = instance.enable;
        var originalDisable = instance.disable;
        instance.destroy = function(shouldDestroyChildInstances) {
          if (shouldDestroyChildInstances === void 0) {
            shouldDestroyChildInstances = true;
          }
          if (shouldDestroyChildInstances) {
            childTippyInstances.forEach(function(instance2) {
              instance2.destroy();
            });
          }
          childTippyInstances = [];
          removeEventListeners();
          originalDestroy();
        };
        instance.enable = function() {
          originalEnable();
          childTippyInstances.forEach(function(instance2) {
            return instance2.enable();
          });
          disabled = false;
        };
        instance.disable = function() {
          originalDisable();
          childTippyInstances.forEach(function(instance2) {
            return instance2.disable();
          });
          disabled = true;
        };
        addEventListeners(instance);
      }
      normalizedReturnValue.forEach(applyMutations);
      return returnValue;
    }
    var animateFill = {
      name: "animateFill",
      defaultValue: false,
      fn: function fn(instance) {
        var _instance$props$rende;
        if (!((_instance$props$rende = instance.props.render) == null ? void 0 : _instance$props$rende.$$tippy)) {
          if (true) {
            errorWhen(instance.props.animateFill, "The `animateFill` plugin requires the default render function.");
          }
          return {};
        }
        var _getChildren = getChildren(instance.popper), box = _getChildren.box, content = _getChildren.content;
        var backdrop = instance.props.animateFill ? createBackdropElement() : null;
        return {
          onCreate: function onCreate() {
            if (backdrop) {
              box.insertBefore(backdrop, box.firstElementChild);
              box.setAttribute("data-animatefill", "");
              box.style.overflow = "hidden";
              instance.setProps({
                arrow: false,
                animation: "shift-away"
              });
            }
          },
          onMount: function onMount() {
            if (backdrop) {
              var transitionDuration = box.style.transitionDuration;
              var duration = Number(transitionDuration.replace("ms", ""));
              content.style.transitionDelay = Math.round(duration / 10) + "ms";
              backdrop.style.transitionDuration = transitionDuration;
              setVisibilityState([backdrop], "visible");
            }
          },
          onShow: function onShow() {
            if (backdrop) {
              backdrop.style.transitionDuration = "0ms";
            }
          },
          onHide: function onHide() {
            if (backdrop) {
              setVisibilityState([backdrop], "hidden");
            }
          }
        };
      }
    };
    function createBackdropElement() {
      var backdrop = div();
      backdrop.className = BACKDROP_CLASS;
      setVisibilityState([backdrop], "hidden");
      return backdrop;
    }
    var mouseCoords = {
      clientX: 0,
      clientY: 0
    };
    var activeInstances = [];
    function storeMouseCoords(_ref) {
      var clientX = _ref.clientX, clientY = _ref.clientY;
      mouseCoords = {
        clientX,
        clientY
      };
    }
    function addMouseCoordsListener(doc) {
      doc.addEventListener("mousemove", storeMouseCoords);
    }
    function removeMouseCoordsListener(doc) {
      doc.removeEventListener("mousemove", storeMouseCoords);
    }
    var followCursor2 = {
      name: "followCursor",
      defaultValue: false,
      fn: function fn(instance) {
        var reference = instance.reference;
        var doc = getOwnerDocument(instance.props.triggerTarget || reference);
        var isInternalUpdate = false;
        var wasFocusEvent = false;
        var isUnmounted = true;
        var prevProps = instance.props;
        function getIsInitialBehavior() {
          return instance.props.followCursor === "initial" && instance.state.isVisible;
        }
        function addListener() {
          doc.addEventListener("mousemove", onMouseMove);
        }
        function removeListener() {
          doc.removeEventListener("mousemove", onMouseMove);
        }
        function unsetGetReferenceClientRect() {
          isInternalUpdate = true;
          instance.setProps({
            getReferenceClientRect: null
          });
          isInternalUpdate = false;
        }
        function onMouseMove(event) {
          var isCursorOverReference = event.target ? reference.contains(event.target) : true;
          var followCursor3 = instance.props.followCursor;
          var clientX = event.clientX, clientY = event.clientY;
          var rect = reference.getBoundingClientRect();
          var relativeX = clientX - rect.left;
          var relativeY = clientY - rect.top;
          if (isCursorOverReference || !instance.props.interactive) {
            instance.setProps({
              getReferenceClientRect: function getReferenceClientRect() {
                var rect2 = reference.getBoundingClientRect();
                var x = clientX;
                var y = clientY;
                if (followCursor3 === "initial") {
                  x = rect2.left + relativeX;
                  y = rect2.top + relativeY;
                }
                var top = followCursor3 === "horizontal" ? rect2.top : y;
                var right = followCursor3 === "vertical" ? rect2.right : x;
                var bottom = followCursor3 === "horizontal" ? rect2.bottom : y;
                var left = followCursor3 === "vertical" ? rect2.left : x;
                return {
                  width: right - left,
                  height: bottom - top,
                  top,
                  right,
                  bottom,
                  left
                };
              }
            });
          }
        }
        function create() {
          if (instance.props.followCursor) {
            activeInstances.push({
              instance,
              doc
            });
            addMouseCoordsListener(doc);
          }
        }
        function destroy2() {
          activeInstances = activeInstances.filter(function(data) {
            return data.instance !== instance;
          });
          if (activeInstances.filter(function(data) {
            return data.doc === doc;
          }).length === 0) {
            removeMouseCoordsListener(doc);
          }
        }
        return {
          onCreate: create,
          onDestroy: destroy2,
          onBeforeUpdate: function onBeforeUpdate() {
            prevProps = instance.props;
          },
          onAfterUpdate: function onAfterUpdate(_, _ref2) {
            var followCursor3 = _ref2.followCursor;
            if (isInternalUpdate) {
              return;
            }
            if (followCursor3 !== void 0 && prevProps.followCursor !== followCursor3) {
              destroy2();
              if (followCursor3) {
                create();
                if (instance.state.isMounted && !wasFocusEvent && !getIsInitialBehavior()) {
                  addListener();
                }
              } else {
                removeListener();
                unsetGetReferenceClientRect();
              }
            }
          },
          onMount: function onMount() {
            if (instance.props.followCursor && !wasFocusEvent) {
              if (isUnmounted) {
                onMouseMove(mouseCoords);
                isUnmounted = false;
              }
              if (!getIsInitialBehavior()) {
                addListener();
              }
            }
          },
          onTrigger: function onTrigger(_, event) {
            if (isMouseEvent(event)) {
              mouseCoords = {
                clientX: event.clientX,
                clientY: event.clientY
              };
            }
            wasFocusEvent = event.type === "focus";
          },
          onHidden: function onHidden() {
            if (instance.props.followCursor) {
              unsetGetReferenceClientRect();
              removeListener();
              isUnmounted = true;
            }
          }
        };
      }
    };
    function getProps(props, modifier) {
      var _props$popperOptions;
      return {
        popperOptions: Object.assign({}, props.popperOptions, {
          modifiers: [].concat((((_props$popperOptions = props.popperOptions) == null ? void 0 : _props$popperOptions.modifiers) || []).filter(function(_ref) {
            var name = _ref.name;
            return name !== modifier.name;
          }), [modifier])
        })
      };
    }
    var inlinePositioning = {
      name: "inlinePositioning",
      defaultValue: false,
      fn: function fn(instance) {
        var reference = instance.reference;
        function isEnabled() {
          return !!instance.props.inlinePositioning;
        }
        var placement;
        var cursorRectIndex = -1;
        var isInternalUpdate = false;
        var modifier = {
          name: "tippyInlinePositioning",
          enabled: true,
          phase: "afterWrite",
          fn: function fn2(_ref2) {
            var state = _ref2.state;
            if (isEnabled()) {
              if (placement !== state.placement) {
                instance.setProps({
                  getReferenceClientRect: function getReferenceClientRect() {
                    return _getReferenceClientRect(state.placement);
                  }
                });
              }
              placement = state.placement;
            }
          }
        };
        function _getReferenceClientRect(placement2) {
          return getInlineBoundingClientRect(getBasePlacement(placement2), reference.getBoundingClientRect(), arrayFrom(reference.getClientRects()), cursorRectIndex);
        }
        function setInternalProps(partialProps) {
          isInternalUpdate = true;
          instance.setProps(partialProps);
          isInternalUpdate = false;
        }
        function addModifier() {
          if (!isInternalUpdate) {
            setInternalProps(getProps(instance.props, modifier));
          }
        }
        return {
          onCreate: addModifier,
          onAfterUpdate: addModifier,
          onTrigger: function onTrigger(_, event) {
            if (isMouseEvent(event)) {
              var rects = arrayFrom(instance.reference.getClientRects());
              var cursorRect = rects.find(function(rect) {
                return rect.left - 2 <= event.clientX && rect.right + 2 >= event.clientX && rect.top - 2 <= event.clientY && rect.bottom + 2 >= event.clientY;
              });
              cursorRectIndex = rects.indexOf(cursorRect);
            }
          },
          onUntrigger: function onUntrigger() {
            cursorRectIndex = -1;
          }
        };
      }
    };
    function getInlineBoundingClientRect(currentBasePlacement, boundingRect, clientRects, cursorRectIndex) {
      if (clientRects.length < 2 || currentBasePlacement === null) {
        return boundingRect;
      }
      if (clientRects.length === 2 && cursorRectIndex >= 0 && clientRects[0].left > clientRects[1].right) {
        return clientRects[cursorRectIndex] || boundingRect;
      }
      switch (currentBasePlacement) {
        case "top":
        case "bottom": {
          var firstRect = clientRects[0];
          var lastRect = clientRects[clientRects.length - 1];
          var isTop = currentBasePlacement === "top";
          var top = firstRect.top;
          var bottom = lastRect.bottom;
          var left = isTop ? firstRect.left : lastRect.left;
          var right = isTop ? firstRect.right : lastRect.right;
          var width = right - left;
          var height = bottom - top;
          return {
            top,
            bottom,
            left,
            right,
            width,
            height
          };
        }
        case "left":
        case "right": {
          var minLeft = Math.min.apply(Math, clientRects.map(function(rects) {
            return rects.left;
          }));
          var maxRight = Math.max.apply(Math, clientRects.map(function(rects) {
            return rects.right;
          }));
          var measureRects = clientRects.filter(function(rect) {
            return currentBasePlacement === "left" ? rect.left === minLeft : rect.right === maxRight;
          });
          var _top = measureRects[0].top;
          var _bottom = measureRects[measureRects.length - 1].bottom;
          var _left = minLeft;
          var _right = maxRight;
          var _width = _right - _left;
          var _height = _bottom - _top;
          return {
            top: _top,
            bottom: _bottom,
            left: _left,
            right: _right,
            width: _width,
            height: _height
          };
        }
        default: {
          return boundingRect;
        }
      }
    }
    var sticky = {
      name: "sticky",
      defaultValue: false,
      fn: function fn(instance) {
        var reference = instance.reference, popper = instance.popper;
        function getReference() {
          return instance.popperInstance ? instance.popperInstance.state.elements.reference : reference;
        }
        function shouldCheck(value) {
          return instance.props.sticky === true || instance.props.sticky === value;
        }
        var prevRefRect = null;
        var prevPopRect = null;
        function updatePosition() {
          var currentRefRect = shouldCheck("reference") ? getReference().getBoundingClientRect() : null;
          var currentPopRect = shouldCheck("popper") ? popper.getBoundingClientRect() : null;
          if (currentRefRect && areRectsDifferent(prevRefRect, currentRefRect) || currentPopRect && areRectsDifferent(prevPopRect, currentPopRect)) {
            if (instance.popperInstance) {
              instance.popperInstance.update();
            }
          }
          prevRefRect = currentRefRect;
          prevPopRect = currentPopRect;
          if (instance.state.isMounted) {
            requestAnimationFrame(updatePosition);
          }
        }
        return {
          onMount: function onMount() {
            if (instance.props.sticky) {
              updatePosition();
            }
          }
        };
      }
    };
    function areRectsDifferent(rectA, rectB) {
      if (rectA && rectB) {
        return rectA.top !== rectB.top || rectA.right !== rectB.right || rectA.bottom !== rectB.bottom || rectA.left !== rectB.left;
      }
      return true;
    }
    tippy2.setDefaultProps({
      render
    });
    exports.animateFill = animateFill;
    exports.createSingleton = createSingleton;
    exports.default = tippy2;
    exports.delegate = delegate;
    exports.followCursor = followCursor2;
    exports.hideAll = hideAll;
    exports.inlinePositioning = inlinePositioning;
    exports.roundArrow = ROUND_ARROW;
    exports.sticky = sticky;
  });
  var import_tippy2 = __toModule(require_tippy_cjs());
  var import_tippy = __toModule(require_tippy_cjs());
  var buildConfigFromModifiers2 = (modifiers) => {
    const config = {
      plugins: []
    };
    const getModifierArgument = (modifier) => {
      return modifiers[modifiers.indexOf(modifier) + 1];
    };
    if (modifiers.includes("animation")) {
      config.animation = getModifierArgument("animation");
    }
    if (modifiers.includes("duration")) {
      config.duration = parseInt(getModifierArgument("duration"));
    }
    if (modifiers.includes("delay")) {
      const delay = getModifierArgument("delay");
      config.delay = delay.includes("-") ? delay.split("-").map((n) => parseInt(n)) : parseInt(delay);
    }
    if (modifiers.includes("cursor")) {
      config.plugins.push(import_tippy.followCursor);
      const next = getModifierArgument("cursor");
      if (["x", "initial"].includes(next)) {
        config.followCursor = next === "x" ? "horizontal" : "initial";
      } else {
        config.followCursor = true;
      }
    }
    if (modifiers.includes("on")) {
      config.trigger = getModifierArgument("on");
    }
    if (modifiers.includes("arrowless")) {
      config.arrow = false;
    }
    if (modifiers.includes("html")) {
      config.allowHTML = true;
    }
    if (modifiers.includes("interactive")) {
      config.interactive = true;
    }
    if (modifiers.includes("border") && config.interactive) {
      config.interactiveBorder = parseInt(getModifierArgument("border"));
    }
    if (modifiers.includes("debounce") && config.interactive) {
      config.interactiveDebounce = parseInt(getModifierArgument("debounce"));
    }
    if (modifiers.includes("max-width")) {
      config.maxWidth = parseInt(getModifierArgument("max-width"));
    }
    if (modifiers.includes("theme")) {
      config.theme = getModifierArgument("theme");
    }
    if (modifiers.includes("placement")) {
      config.placement = getModifierArgument("placement");
    }
    return config;
  };
  function src_default2(Alpine) {
    Alpine.magic("tooltip", (el) => {
      return (content, config = {}) => {
        const instance = (0, import_tippy2.default)(el, {
          content,
          trigger: "manual",
          ...config
        });
        instance.show();
        setTimeout(() => {
          instance.hide();
          setTimeout(() => instance.destroy(), config.duration || 300);
        }, config.timeout || 2e3);
      };
    });
    Alpine.directive("tooltip", (el, { modifiers, expression }, { evaluateLater, effect }) => {
      const config = modifiers.length > 0 ? buildConfigFromModifiers2(modifiers) : {};
      if (!el.__x_tippy) {
        el.__x_tippy = (0, import_tippy2.default)(el, config);
      }
      const enableTooltip = () => el.__x_tippy.enable();
      const disableTooltip = () => el.__x_tippy.disable();
      const setupTooltip = (content) => {
        if (!content) {
          disableTooltip();
        } else {
          enableTooltip();
          el.__x_tippy.setContent(content);
        }
      };
      if (modifiers.includes("raw")) {
        setupTooltip(expression);
      } else {
        const getContent = evaluateLater(expression);
        effect(() => {
          getContent((content) => {
            if (typeof content === "object") {
              el.__x_tippy.setProps(content);
              enableTooltip();
            } else {
              setupTooltip(content);
            }
          });
        });
      }
    });
  }
  var module_default3 = src_default2;

  // packages/support/resources/js/index.js
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(module_default);
    window.Alpine.plugin(module_default2);
    window.Alpine.plugin(sortable_default);
    window.Alpine.plugin(module_default3);
  });
  var pluralize = function(text, number, variables) {
    function extract(segments2, number2) {
      for (const part of segments2) {
        const line = extractFromString(part, number2);
        if (line !== null) {
          return line;
        }
      }
    }
    function extractFromString(part, number2) {
      const matches2 = part.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s);
      if (matches2 === null || matches2.length !== 3) {
        return null;
      }
      const condition = matches2[1];
      const value2 = matches2[2];
      if (condition.includes(",")) {
        const [from, to] = condition.split(",", 2);
        if (to === "*" && number2 >= from) {
          return value2;
        } else if (from === "*" && number2 <= to) {
          return value2;
        } else if (number2 >= from && number2 <= to) {
          return value2;
        }
      }
      return condition == number2 ? value2 : null;
    }
    function ucfirst(string) {
      return string.toString().charAt(0).toUpperCase() + string.toString().slice(1);
    }
    function replace(line, replace2) {
      if (replace2.length === 0) {
        return line;
      }
      const shouldReplace = {};
      for (let [key, value2] of Object.entries(replace2)) {
        shouldReplace[":" + ucfirst(key ?? "")] = ucfirst(value2 ?? "");
        shouldReplace[":" + key.toUpperCase()] = value2.toString().toUpperCase();
        shouldReplace[":" + key] = value2;
      }
      Object.entries(shouldReplace).forEach(([key, value2]) => {
        line = line.replaceAll(key, value2);
      });
      return line;
    }
    function stripConditions(segments2) {
      return segments2.map(
        (part) => part.replace(/^[\{\[]([^\[\]\{\}]*)[\}\]]/, "")
      );
    }
    let segments = text.split("|");
    const value = extract(segments, number);
    if (value !== null && value !== void 0) {
      return replace(value.trim(), variables);
    }
    segments = stripConditions(segments);
    return replace(
      segments.length > 1 && number > 1 ? segments[1] : segments[0],
      variables
    );
  };
  window.pluralize = pluralize;
})();
/*! Bundled license information:

sortablejs/modular/sortable.esm.js:
  (**!
   * Sortable 1.15.0
   * @author	RubaXa   <trash@rubaxa.org>
   * @author	owenm    <owen23355@gmail.com>
   * @license MIT
   *)
*/
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0Bhd2NvZGVzL2FscGluZS1mbG9hdGluZy11aS9kaXN0L21vZHVsZS5lc20uanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2FscGluZS1sYXp5LWxvYWQtYXNzZXRzL2Rpc3QvYWxwaW5lLWxhenktbG9hZC1hc3NldHMuZXNtLmpzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9zb3J0YWJsZWpzL21vZHVsYXIvc29ydGFibGUuZXNtLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9zb3J0YWJsZS5qcyIsICIuLi8uLi8uLi9ub2RlX21vZHVsZXMvQHJ5YW5namNoYW5kbGVyL2FscGluZS10b29sdGlwL2Rpc3QvbW9kdWxlLmVzbS5qcyIsICIuLi9yZXNvdXJjZXMvanMvaW5kZXguanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbIi8vIG5vZGVfbW9kdWxlcy9AZmxvYXRpbmctdWkvY29yZS9kaXN0L2Zsb2F0aW5nLXVpLmNvcmUuZXNtLmpzXG5mdW5jdGlvbiBnZXRTaWRlKHBsYWNlbWVudCkge1xuICByZXR1cm4gcGxhY2VtZW50LnNwbGl0KFwiLVwiKVswXTtcbn1cbmZ1bmN0aW9uIGdldEFsaWdubWVudChwbGFjZW1lbnQpIHtcbiAgcmV0dXJuIHBsYWNlbWVudC5zcGxpdChcIi1cIilbMV07XG59XG5mdW5jdGlvbiBnZXRNYWluQXhpc0Zyb21QbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gIHJldHVybiBbXCJ0b3BcIiwgXCJib3R0b21cIl0uaW5jbHVkZXMoZ2V0U2lkZShwbGFjZW1lbnQpKSA/IFwieFwiIDogXCJ5XCI7XG59XG5mdW5jdGlvbiBnZXRMZW5ndGhGcm9tQXhpcyhheGlzKSB7XG4gIHJldHVybiBheGlzID09PSBcInlcIiA/IFwiaGVpZ2h0XCIgOiBcIndpZHRoXCI7XG59XG5mdW5jdGlvbiBjb21wdXRlQ29vcmRzRnJvbVBsYWNlbWVudChfcmVmLCBwbGFjZW1lbnQsIHJ0bCkge1xuICBsZXQge1xuICAgIHJlZmVyZW5jZSxcbiAgICBmbG9hdGluZ1xuICB9ID0gX3JlZjtcbiAgY29uc3QgY29tbW9uWCA9IHJlZmVyZW5jZS54ICsgcmVmZXJlbmNlLndpZHRoIC8gMiAtIGZsb2F0aW5nLndpZHRoIC8gMjtcbiAgY29uc3QgY29tbW9uWSA9IHJlZmVyZW5jZS55ICsgcmVmZXJlbmNlLmhlaWdodCAvIDIgLSBmbG9hdGluZy5oZWlnaHQgLyAyO1xuICBjb25zdCBtYWluQXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBsZW5ndGggPSBnZXRMZW5ndGhGcm9tQXhpcyhtYWluQXhpcyk7XG4gIGNvbnN0IGNvbW1vbkFsaWduID0gcmVmZXJlbmNlW2xlbmd0aF0gLyAyIC0gZmxvYXRpbmdbbGVuZ3RoXSAvIDI7XG4gIGNvbnN0IHNpZGUgPSBnZXRTaWRlKHBsYWNlbWVudCk7XG4gIGNvbnN0IGlzVmVydGljYWwgPSBtYWluQXhpcyA9PT0gXCJ4XCI7XG4gIGxldCBjb29yZHM7XG4gIHN3aXRjaCAoc2lkZSkge1xuICAgIGNhc2UgXCJ0b3BcIjpcbiAgICAgIGNvb3JkcyA9IHtcbiAgICAgICAgeDogY29tbW9uWCxcbiAgICAgICAgeTogcmVmZXJlbmNlLnkgLSBmbG9hdGluZy5oZWlnaHRcbiAgICAgIH07XG4gICAgICBicmVhaztcbiAgICBjYXNlIFwiYm90dG9tXCI6XG4gICAgICBjb29yZHMgPSB7XG4gICAgICAgIHg6IGNvbW1vblgsXG4gICAgICAgIHk6IHJlZmVyZW5jZS55ICsgcmVmZXJlbmNlLmhlaWdodFxuICAgICAgfTtcbiAgICAgIGJyZWFrO1xuICAgIGNhc2UgXCJyaWdodFwiOlxuICAgICAgY29vcmRzID0ge1xuICAgICAgICB4OiByZWZlcmVuY2UueCArIHJlZmVyZW5jZS53aWR0aCxcbiAgICAgICAgeTogY29tbW9uWVxuICAgICAgfTtcbiAgICAgIGJyZWFrO1xuICAgIGNhc2UgXCJsZWZ0XCI6XG4gICAgICBjb29yZHMgPSB7XG4gICAgICAgIHg6IHJlZmVyZW5jZS54IC0gZmxvYXRpbmcud2lkdGgsXG4gICAgICAgIHk6IGNvbW1vbllcbiAgICAgIH07XG4gICAgICBicmVhaztcbiAgICBkZWZhdWx0OlxuICAgICAgY29vcmRzID0ge1xuICAgICAgICB4OiByZWZlcmVuY2UueCxcbiAgICAgICAgeTogcmVmZXJlbmNlLnlcbiAgICAgIH07XG4gIH1cbiAgc3dpdGNoIChnZXRBbGlnbm1lbnQocGxhY2VtZW50KSkge1xuICAgIGNhc2UgXCJzdGFydFwiOlxuICAgICAgY29vcmRzW21haW5BeGlzXSAtPSBjb21tb25BbGlnbiAqIChydGwgJiYgaXNWZXJ0aWNhbCA/IC0xIDogMSk7XG4gICAgICBicmVhaztcbiAgICBjYXNlIFwiZW5kXCI6XG4gICAgICBjb29yZHNbbWFpbkF4aXNdICs9IGNvbW1vbkFsaWduICogKHJ0bCAmJiBpc1ZlcnRpY2FsID8gLTEgOiAxKTtcbiAgICAgIGJyZWFrO1xuICB9XG4gIHJldHVybiBjb29yZHM7XG59XG52YXIgY29tcHV0ZVBvc2l0aW9uID0gYXN5bmMgKHJlZmVyZW5jZSwgZmxvYXRpbmcsIGNvbmZpZykgPT4ge1xuICBjb25zdCB7XG4gICAgcGxhY2VtZW50ID0gXCJib3R0b21cIixcbiAgICBzdHJhdGVneSA9IFwiYWJzb2x1dGVcIixcbiAgICBtaWRkbGV3YXJlID0gW10sXG4gICAgcGxhdGZvcm06IHBsYXRmb3JtMlxuICB9ID0gY29uZmlnO1xuICBjb25zdCBydGwgPSBhd2FpdCAocGxhdGZvcm0yLmlzUlRMID09IG51bGwgPyB2b2lkIDAgOiBwbGF0Zm9ybTIuaXNSVEwoZmxvYXRpbmcpKTtcbiAgaWYgKHRydWUpIHtcbiAgICBpZiAocGxhdGZvcm0yID09IG51bGwpIHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoW1wiRmxvYXRpbmcgVUk6IGBwbGF0Zm9ybWAgcHJvcGVydHkgd2FzIG5vdCBwYXNzZWQgdG8gY29uZmlnLiBJZiB5b3VcIiwgXCJ3YW50IHRvIHVzZSBGbG9hdGluZyBVSSBvbiB0aGUgd2ViLCBpbnN0YWxsIEBmbG9hdGluZy11aS9kb21cIiwgXCJpbnN0ZWFkIG9mIHRoZSAvY29yZSBwYWNrYWdlLiBPdGhlcndpc2UsIHlvdSBjYW4gY3JlYXRlIHlvdXIgb3duXCIsIFwiYHBsYXRmb3JtYDogaHR0cHM6Ly9mbG9hdGluZy11aS5jb20vZG9jcy9wbGF0Zm9ybVwiXS5qb2luKFwiIFwiKSk7XG4gICAgfVxuICAgIGlmIChtaWRkbGV3YXJlLmZpbHRlcigoX3JlZikgPT4ge1xuICAgICAgbGV0IHtcbiAgICAgICAgbmFtZVxuICAgICAgfSA9IF9yZWY7XG4gICAgICByZXR1cm4gbmFtZSA9PT0gXCJhdXRvUGxhY2VtZW50XCIgfHwgbmFtZSA9PT0gXCJmbGlwXCI7XG4gICAgfSkubGVuZ3RoID4gMSkge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKFtcIkZsb2F0aW5nIFVJOiBkdXBsaWNhdGUgYGZsaXBgIGFuZC9vciBgYXV0b1BsYWNlbWVudGBcIiwgXCJtaWRkbGV3YXJlIGRldGVjdGVkLiBUaGlzIHdpbGwgbGVhZCB0byBhbiBpbmZpbml0ZSBsb29wLiBFbnN1cmUgb25seVwiLCBcIm9uZSBvZiBlaXRoZXIgaGFzIGJlZW4gcGFzc2VkIHRvIHRoZSBgbWlkZGxld2FyZWAgYXJyYXkuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICB9XG4gIH1cbiAgbGV0IHJlY3RzID0gYXdhaXQgcGxhdGZvcm0yLmdldEVsZW1lbnRSZWN0cyh7XG4gICAgcmVmZXJlbmNlLFxuICAgIGZsb2F0aW5nLFxuICAgIHN0cmF0ZWd5XG4gIH0pO1xuICBsZXQge1xuICAgIHgsXG4gICAgeVxuICB9ID0gY29tcHV0ZUNvb3Jkc0Zyb21QbGFjZW1lbnQocmVjdHMsIHBsYWNlbWVudCwgcnRsKTtcbiAgbGV0IHN0YXRlZnVsUGxhY2VtZW50ID0gcGxhY2VtZW50O1xuICBsZXQgbWlkZGxld2FyZURhdGEgPSB7fTtcbiAgbGV0IF9kZWJ1Z19sb29wX2NvdW50XyA9IDA7XG4gIGZvciAobGV0IGkgPSAwOyBpIDwgbWlkZGxld2FyZS5sZW5ndGg7IGkrKykge1xuICAgIGlmICh0cnVlKSB7XG4gICAgICBfZGVidWdfbG9vcF9jb3VudF8rKztcbiAgICAgIGlmIChfZGVidWdfbG9vcF9jb3VudF8gPiAxMDApIHtcbiAgICAgICAgdGhyb3cgbmV3IEVycm9yKFtcIkZsb2F0aW5nIFVJOiBUaGUgbWlkZGxld2FyZSBsaWZlY3ljbGUgYXBwZWFycyB0byBiZVwiLCBcInJ1bm5pbmcgaW4gYW4gaW5maW5pdGUgbG9vcC4gVGhpcyBpcyB1c3VhbGx5IGNhdXNlZCBieSBhIGByZXNldGBcIiwgXCJjb250aW51YWxseSBiZWluZyByZXR1cm5lZCB3aXRob3V0IGEgYnJlYWsgY29uZGl0aW9uLlwiXS5qb2luKFwiIFwiKSk7XG4gICAgICB9XG4gICAgfVxuICAgIGNvbnN0IHtcbiAgICAgIG5hbWUsXG4gICAgICBmblxuICAgIH0gPSBtaWRkbGV3YXJlW2ldO1xuICAgIGNvbnN0IHtcbiAgICAgIHg6IG5leHRYLFxuICAgICAgeTogbmV4dFksXG4gICAgICBkYXRhLFxuICAgICAgcmVzZXRcbiAgICB9ID0gYXdhaXQgZm4oe1xuICAgICAgeCxcbiAgICAgIHksXG4gICAgICBpbml0aWFsUGxhY2VtZW50OiBwbGFjZW1lbnQsXG4gICAgICBwbGFjZW1lbnQ6IHN0YXRlZnVsUGxhY2VtZW50LFxuICAgICAgc3RyYXRlZ3ksXG4gICAgICBtaWRkbGV3YXJlRGF0YSxcbiAgICAgIHJlY3RzLFxuICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMixcbiAgICAgIGVsZW1lbnRzOiB7XG4gICAgICAgIHJlZmVyZW5jZSxcbiAgICAgICAgZmxvYXRpbmdcbiAgICAgIH1cbiAgICB9KTtcbiAgICB4ID0gbmV4dFggIT0gbnVsbCA/IG5leHRYIDogeDtcbiAgICB5ID0gbmV4dFkgIT0gbnVsbCA/IG5leHRZIDogeTtcbiAgICBtaWRkbGV3YXJlRGF0YSA9IHtcbiAgICAgIC4uLm1pZGRsZXdhcmVEYXRhLFxuICAgICAgW25hbWVdOiB7XG4gICAgICAgIC4uLm1pZGRsZXdhcmVEYXRhW25hbWVdLFxuICAgICAgICAuLi5kYXRhXG4gICAgICB9XG4gICAgfTtcbiAgICBpZiAocmVzZXQpIHtcbiAgICAgIGlmICh0eXBlb2YgcmVzZXQgPT09IFwib2JqZWN0XCIpIHtcbiAgICAgICAgaWYgKHJlc2V0LnBsYWNlbWVudCkge1xuICAgICAgICAgIHN0YXRlZnVsUGxhY2VtZW50ID0gcmVzZXQucGxhY2VtZW50O1xuICAgICAgICB9XG4gICAgICAgIGlmIChyZXNldC5yZWN0cykge1xuICAgICAgICAgIHJlY3RzID0gcmVzZXQucmVjdHMgPT09IHRydWUgPyBhd2FpdCBwbGF0Zm9ybTIuZ2V0RWxlbWVudFJlY3RzKHtcbiAgICAgICAgICAgIHJlZmVyZW5jZSxcbiAgICAgICAgICAgIGZsb2F0aW5nLFxuICAgICAgICAgICAgc3RyYXRlZ3lcbiAgICAgICAgICB9KSA6IHJlc2V0LnJlY3RzO1xuICAgICAgICB9XG4gICAgICAgICh7XG4gICAgICAgICAgeCxcbiAgICAgICAgICB5XG4gICAgICAgIH0gPSBjb21wdXRlQ29vcmRzRnJvbVBsYWNlbWVudChyZWN0cywgc3RhdGVmdWxQbGFjZW1lbnQsIHJ0bCkpO1xuICAgICAgfVxuICAgICAgaSA9IC0xO1xuICAgICAgY29udGludWU7XG4gICAgfVxuICB9XG4gIHJldHVybiB7XG4gICAgeCxcbiAgICB5LFxuICAgIHBsYWNlbWVudDogc3RhdGVmdWxQbGFjZW1lbnQsXG4gICAgc3RyYXRlZ3ksXG4gICAgbWlkZGxld2FyZURhdGFcbiAgfTtcbn07XG5mdW5jdGlvbiBleHBhbmRQYWRkaW5nT2JqZWN0KHBhZGRpbmcpIHtcbiAgcmV0dXJuIHtcbiAgICB0b3A6IDAsXG4gICAgcmlnaHQ6IDAsXG4gICAgYm90dG9tOiAwLFxuICAgIGxlZnQ6IDAsXG4gICAgLi4ucGFkZGluZ1xuICB9O1xufVxuZnVuY3Rpb24gZ2V0U2lkZU9iamVjdEZyb21QYWRkaW5nKHBhZGRpbmcpIHtcbiAgcmV0dXJuIHR5cGVvZiBwYWRkaW5nICE9PSBcIm51bWJlclwiID8gZXhwYW5kUGFkZGluZ09iamVjdChwYWRkaW5nKSA6IHtcbiAgICB0b3A6IHBhZGRpbmcsXG4gICAgcmlnaHQ6IHBhZGRpbmcsXG4gICAgYm90dG9tOiBwYWRkaW5nLFxuICAgIGxlZnQ6IHBhZGRpbmdcbiAgfTtcbn1cbmZ1bmN0aW9uIHJlY3RUb0NsaWVudFJlY3QocmVjdCkge1xuICByZXR1cm4ge1xuICAgIC4uLnJlY3QsXG4gICAgdG9wOiByZWN0LnksXG4gICAgbGVmdDogcmVjdC54LFxuICAgIHJpZ2h0OiByZWN0LnggKyByZWN0LndpZHRoLFxuICAgIGJvdHRvbTogcmVjdC55ICsgcmVjdC5oZWlnaHRcbiAgfTtcbn1cbmFzeW5jIGZ1bmN0aW9uIGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIG9wdGlvbnMpIHtcbiAgdmFyIF9hd2FpdCRwbGF0Zm9ybSRpc0VsZTtcbiAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgIG9wdGlvbnMgPSB7fTtcbiAgfVxuICBjb25zdCB7XG4gICAgeCxcbiAgICB5LFxuICAgIHBsYXRmb3JtOiBwbGF0Zm9ybTIsXG4gICAgcmVjdHMsXG4gICAgZWxlbWVudHMsXG4gICAgc3RyYXRlZ3lcbiAgfSA9IG1pZGRsZXdhcmVBcmd1bWVudHM7XG4gIGNvbnN0IHtcbiAgICBib3VuZGFyeSA9IFwiY2xpcHBpbmdBbmNlc3RvcnNcIixcbiAgICByb290Qm91bmRhcnkgPSBcInZpZXdwb3J0XCIsXG4gICAgZWxlbWVudENvbnRleHQgPSBcImZsb2F0aW5nXCIsXG4gICAgYWx0Qm91bmRhcnkgPSBmYWxzZSxcbiAgICBwYWRkaW5nID0gMFxuICB9ID0gb3B0aW9ucztcbiAgY29uc3QgcGFkZGluZ09iamVjdCA9IGdldFNpZGVPYmplY3RGcm9tUGFkZGluZyhwYWRkaW5nKTtcbiAgY29uc3QgYWx0Q29udGV4dCA9IGVsZW1lbnRDb250ZXh0ID09PSBcImZsb2F0aW5nXCIgPyBcInJlZmVyZW5jZVwiIDogXCJmbG9hdGluZ1wiO1xuICBjb25zdCBlbGVtZW50ID0gZWxlbWVudHNbYWx0Qm91bmRhcnkgPyBhbHRDb250ZXh0IDogZWxlbWVudENvbnRleHRdO1xuICBjb25zdCBjbGlwcGluZ0NsaWVudFJlY3QgPSByZWN0VG9DbGllbnRSZWN0KGF3YWl0IHBsYXRmb3JtMi5nZXRDbGlwcGluZ1JlY3Qoe1xuICAgIGVsZW1lbnQ6ICgoX2F3YWl0JHBsYXRmb3JtJGlzRWxlID0gYXdhaXQgKHBsYXRmb3JtMi5pc0VsZW1lbnQgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5pc0VsZW1lbnQoZWxlbWVudCkpKSAhPSBudWxsID8gX2F3YWl0JHBsYXRmb3JtJGlzRWxlIDogdHJ1ZSkgPyBlbGVtZW50IDogZWxlbWVudC5jb250ZXh0RWxlbWVudCB8fCBhd2FpdCAocGxhdGZvcm0yLmdldERvY3VtZW50RWxlbWVudCA9PSBudWxsID8gdm9pZCAwIDogcGxhdGZvcm0yLmdldERvY3VtZW50RWxlbWVudChlbGVtZW50cy5mbG9hdGluZykpLFxuICAgIGJvdW5kYXJ5LFxuICAgIHJvb3RCb3VuZGFyeSxcbiAgICBzdHJhdGVneVxuICB9KSk7XG4gIGNvbnN0IGVsZW1lbnRDbGllbnRSZWN0ID0gcmVjdFRvQ2xpZW50UmVjdChwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3QgPyBhd2FpdCBwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3Qoe1xuICAgIHJlY3Q6IGVsZW1lbnRDb250ZXh0ID09PSBcImZsb2F0aW5nXCIgPyB7XG4gICAgICAuLi5yZWN0cy5mbG9hdGluZyxcbiAgICAgIHgsXG4gICAgICB5XG4gICAgfSA6IHJlY3RzLnJlZmVyZW5jZSxcbiAgICBvZmZzZXRQYXJlbnQ6IGF3YWl0IChwbGF0Zm9ybTIuZ2V0T2Zmc2V0UGFyZW50ID09IG51bGwgPyB2b2lkIDAgOiBwbGF0Zm9ybTIuZ2V0T2Zmc2V0UGFyZW50KGVsZW1lbnRzLmZsb2F0aW5nKSksXG4gICAgc3RyYXRlZ3lcbiAgfSkgOiByZWN0c1tlbGVtZW50Q29udGV4dF0pO1xuICByZXR1cm4ge1xuICAgIHRvcDogY2xpcHBpbmdDbGllbnRSZWN0LnRvcCAtIGVsZW1lbnRDbGllbnRSZWN0LnRvcCArIHBhZGRpbmdPYmplY3QudG9wLFxuICAgIGJvdHRvbTogZWxlbWVudENsaWVudFJlY3QuYm90dG9tIC0gY2xpcHBpbmdDbGllbnRSZWN0LmJvdHRvbSArIHBhZGRpbmdPYmplY3QuYm90dG9tLFxuICAgIGxlZnQ6IGNsaXBwaW5nQ2xpZW50UmVjdC5sZWZ0IC0gZWxlbWVudENsaWVudFJlY3QubGVmdCArIHBhZGRpbmdPYmplY3QubGVmdCxcbiAgICByaWdodDogZWxlbWVudENsaWVudFJlY3QucmlnaHQgLSBjbGlwcGluZ0NsaWVudFJlY3QucmlnaHQgKyBwYWRkaW5nT2JqZWN0LnJpZ2h0XG4gIH07XG59XG52YXIgbWluID0gTWF0aC5taW47XG52YXIgbWF4ID0gTWF0aC5tYXg7XG5mdW5jdGlvbiB3aXRoaW4obWluJDEsIHZhbHVlLCBtYXgkMSkge1xuICByZXR1cm4gbWF4KG1pbiQxLCBtaW4odmFsdWUsIG1heCQxKSk7XG59XG52YXIgYXJyb3cgPSAob3B0aW9ucykgPT4gKHtcbiAgbmFtZTogXCJhcnJvd1wiLFxuICBvcHRpb25zLFxuICBhc3luYyBmbihtaWRkbGV3YXJlQXJndW1lbnRzKSB7XG4gICAgY29uc3Qge1xuICAgICAgZWxlbWVudCxcbiAgICAgIHBhZGRpbmcgPSAwXG4gICAgfSA9IG9wdGlvbnMgIT0gbnVsbCA/IG9wdGlvbnMgOiB7fTtcbiAgICBjb25zdCB7XG4gICAgICB4LFxuICAgICAgeSxcbiAgICAgIHBsYWNlbWVudCxcbiAgICAgIHJlY3RzLFxuICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMlxuICAgIH0gPSBtaWRkbGV3YXJlQXJndW1lbnRzO1xuICAgIGlmIChlbGVtZW50ID09IG51bGwpIHtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIGNvbnNvbGUud2FybihcIkZsb2F0aW5nIFVJOiBObyBgZWxlbWVudGAgd2FzIHBhc3NlZCB0byB0aGUgYGFycm93YCBtaWRkbGV3YXJlLlwiKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB7fTtcbiAgICB9XG4gICAgY29uc3QgcGFkZGluZ09iamVjdCA9IGdldFNpZGVPYmplY3RGcm9tUGFkZGluZyhwYWRkaW5nKTtcbiAgICBjb25zdCBjb29yZHMgPSB7XG4gICAgICB4LFxuICAgICAgeVxuICAgIH07XG4gICAgY29uc3QgYXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICAgIGNvbnN0IGxlbmd0aCA9IGdldExlbmd0aEZyb21BeGlzKGF4aXMpO1xuICAgIGNvbnN0IGFycm93RGltZW5zaW9ucyA9IGF3YWl0IHBsYXRmb3JtMi5nZXREaW1lbnNpb25zKGVsZW1lbnQpO1xuICAgIGNvbnN0IG1pblByb3AgPSBheGlzID09PSBcInlcIiA/IFwidG9wXCIgOiBcImxlZnRcIjtcbiAgICBjb25zdCBtYXhQcm9wID0gYXhpcyA9PT0gXCJ5XCIgPyBcImJvdHRvbVwiIDogXCJyaWdodFwiO1xuICAgIGNvbnN0IGVuZERpZmYgPSByZWN0cy5yZWZlcmVuY2VbbGVuZ3RoXSArIHJlY3RzLnJlZmVyZW5jZVtheGlzXSAtIGNvb3Jkc1theGlzXSAtIHJlY3RzLmZsb2F0aW5nW2xlbmd0aF07XG4gICAgY29uc3Qgc3RhcnREaWZmID0gY29vcmRzW2F4aXNdIC0gcmVjdHMucmVmZXJlbmNlW2F4aXNdO1xuICAgIGNvbnN0IGFycm93T2Zmc2V0UGFyZW50ID0gYXdhaXQgKHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQoZWxlbWVudCkpO1xuICAgIGNvbnN0IGNsaWVudFNpemUgPSBhcnJvd09mZnNldFBhcmVudCA/IGF4aXMgPT09IFwieVwiID8gYXJyb3dPZmZzZXRQYXJlbnQuY2xpZW50SGVpZ2h0IHx8IDAgOiBhcnJvd09mZnNldFBhcmVudC5jbGllbnRXaWR0aCB8fCAwIDogMDtcbiAgICBjb25zdCBjZW50ZXJUb1JlZmVyZW5jZSA9IGVuZERpZmYgLyAyIC0gc3RhcnREaWZmIC8gMjtcbiAgICBjb25zdCBtaW4zID0gcGFkZGluZ09iamVjdFttaW5Qcm9wXTtcbiAgICBjb25zdCBtYXgzID0gY2xpZW50U2l6ZSAtIGFycm93RGltZW5zaW9uc1tsZW5ndGhdIC0gcGFkZGluZ09iamVjdFttYXhQcm9wXTtcbiAgICBjb25zdCBjZW50ZXIgPSBjbGllbnRTaXplIC8gMiAtIGFycm93RGltZW5zaW9uc1tsZW5ndGhdIC8gMiArIGNlbnRlclRvUmVmZXJlbmNlO1xuICAgIGNvbnN0IG9mZnNldDIgPSB3aXRoaW4obWluMywgY2VudGVyLCBtYXgzKTtcbiAgICByZXR1cm4ge1xuICAgICAgZGF0YToge1xuICAgICAgICBbYXhpc106IG9mZnNldDIsXG4gICAgICAgIGNlbnRlck9mZnNldDogY2VudGVyIC0gb2Zmc2V0MlxuICAgICAgfVxuICAgIH07XG4gIH1cbn0pO1xudmFyIGhhc2gkMSA9IHtcbiAgbGVmdDogXCJyaWdodFwiLFxuICByaWdodDogXCJsZWZ0XCIsXG4gIGJvdHRvbTogXCJ0b3BcIixcbiAgdG9wOiBcImJvdHRvbVwiXG59O1xuZnVuY3Rpb24gZ2V0T3Bwb3NpdGVQbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gIHJldHVybiBwbGFjZW1lbnQucmVwbGFjZSgvbGVmdHxyaWdodHxib3R0b218dG9wL2csIChtYXRjaGVkKSA9PiBoYXNoJDFbbWF0Y2hlZF0pO1xufVxuZnVuY3Rpb24gZ2V0QWxpZ25tZW50U2lkZXMocGxhY2VtZW50LCByZWN0cywgcnRsKSB7XG4gIGlmIChydGwgPT09IHZvaWQgMCkge1xuICAgIHJ0bCA9IGZhbHNlO1xuICB9XG4gIGNvbnN0IGFsaWdubWVudCA9IGdldEFsaWdubWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBtYWluQXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBsZW5ndGggPSBnZXRMZW5ndGhGcm9tQXhpcyhtYWluQXhpcyk7XG4gIGxldCBtYWluQWxpZ25tZW50U2lkZSA9IG1haW5BeGlzID09PSBcInhcIiA/IGFsaWdubWVudCA9PT0gKHJ0bCA/IFwiZW5kXCIgOiBcInN0YXJ0XCIpID8gXCJyaWdodFwiIDogXCJsZWZ0XCIgOiBhbGlnbm1lbnQgPT09IFwic3RhcnRcIiA/IFwiYm90dG9tXCIgOiBcInRvcFwiO1xuICBpZiAocmVjdHMucmVmZXJlbmNlW2xlbmd0aF0gPiByZWN0cy5mbG9hdGluZ1tsZW5ndGhdKSB7XG4gICAgbWFpbkFsaWdubWVudFNpZGUgPSBnZXRPcHBvc2l0ZVBsYWNlbWVudChtYWluQWxpZ25tZW50U2lkZSk7XG4gIH1cbiAgcmV0dXJuIHtcbiAgICBtYWluOiBtYWluQWxpZ25tZW50U2lkZSxcbiAgICBjcm9zczogZ2V0T3Bwb3NpdGVQbGFjZW1lbnQobWFpbkFsaWdubWVudFNpZGUpXG4gIH07XG59XG52YXIgaGFzaCA9IHtcbiAgc3RhcnQ6IFwiZW5kXCIsXG4gIGVuZDogXCJzdGFydFwiXG59O1xuZnVuY3Rpb24gZ2V0T3Bwb3NpdGVBbGlnbm1lbnRQbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gIHJldHVybiBwbGFjZW1lbnQucmVwbGFjZSgvc3RhcnR8ZW5kL2csIChtYXRjaGVkKSA9PiBoYXNoW21hdGNoZWRdKTtcbn1cbnZhciBzaWRlcyA9IFtcInRvcFwiLCBcInJpZ2h0XCIsIFwiYm90dG9tXCIsIFwibGVmdFwiXTtcbnZhciBhbGxQbGFjZW1lbnRzID0gLyogQF9fUFVSRV9fICovIHNpZGVzLnJlZHVjZSgoYWNjLCBzaWRlKSA9PiBhY2MuY29uY2F0KHNpZGUsIHNpZGUgKyBcIi1zdGFydFwiLCBzaWRlICsgXCItZW5kXCIpLCBbXSk7XG5mdW5jdGlvbiBnZXRQbGFjZW1lbnRMaXN0KGFsaWdubWVudCwgYXV0b0FsaWdubWVudCwgYWxsb3dlZFBsYWNlbWVudHMpIHtcbiAgY29uc3QgYWxsb3dlZFBsYWNlbWVudHNTb3J0ZWRCeUFsaWdubWVudCA9IGFsaWdubWVudCA/IFsuLi5hbGxvd2VkUGxhY2VtZW50cy5maWx0ZXIoKHBsYWNlbWVudCkgPT4gZ2V0QWxpZ25tZW50KHBsYWNlbWVudCkgPT09IGFsaWdubWVudCksIC4uLmFsbG93ZWRQbGFjZW1lbnRzLmZpbHRlcigocGxhY2VtZW50KSA9PiBnZXRBbGlnbm1lbnQocGxhY2VtZW50KSAhPT0gYWxpZ25tZW50KV0gOiBhbGxvd2VkUGxhY2VtZW50cy5maWx0ZXIoKHBsYWNlbWVudCkgPT4gZ2V0U2lkZShwbGFjZW1lbnQpID09PSBwbGFjZW1lbnQpO1xuICByZXR1cm4gYWxsb3dlZFBsYWNlbWVudHNTb3J0ZWRCeUFsaWdubWVudC5maWx0ZXIoKHBsYWNlbWVudCkgPT4ge1xuICAgIGlmIChhbGlnbm1lbnQpIHtcbiAgICAgIHJldHVybiBnZXRBbGlnbm1lbnQocGxhY2VtZW50KSA9PT0gYWxpZ25tZW50IHx8IChhdXRvQWxpZ25tZW50ID8gZ2V0T3Bwb3NpdGVBbGlnbm1lbnRQbGFjZW1lbnQocGxhY2VtZW50KSAhPT0gcGxhY2VtZW50IDogZmFsc2UpO1xuICAgIH1cbiAgICByZXR1cm4gdHJ1ZTtcbiAgfSk7XG59XG52YXIgYXV0b1BsYWNlbWVudCA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcbiAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgIG9wdGlvbnMgPSB7fTtcbiAgfVxuICByZXR1cm4ge1xuICAgIG5hbWU6IFwiYXV0b1BsYWNlbWVudFwiLFxuICAgIG9wdGlvbnMsXG4gICAgYXN5bmMgZm4obWlkZGxld2FyZUFyZ3VtZW50cykge1xuICAgICAgdmFyIF9taWRkbGV3YXJlRGF0YSRhdXRvUCwgX21pZGRsZXdhcmVEYXRhJGF1dG9QMiwgX21pZGRsZXdhcmVEYXRhJGF1dG9QMywgX21pZGRsZXdhcmVEYXRhJGF1dG9QNCwgX3BsYWNlbWVudHNTb3J0ZWRCeUxlO1xuICAgICAgY29uc3Qge1xuICAgICAgICB4LFxuICAgICAgICB5LFxuICAgICAgICByZWN0cyxcbiAgICAgICAgbWlkZGxld2FyZURhdGEsXG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMixcbiAgICAgICAgZWxlbWVudHNcbiAgICAgIH0gPSBtaWRkbGV3YXJlQXJndW1lbnRzO1xuICAgICAgY29uc3Qge1xuICAgICAgICBhbGlnbm1lbnQgPSBudWxsLFxuICAgICAgICBhbGxvd2VkUGxhY2VtZW50cyA9IGFsbFBsYWNlbWVudHMsXG4gICAgICAgIGF1dG9BbGlnbm1lbnQgPSB0cnVlLFxuICAgICAgICAuLi5kZXRlY3RPdmVyZmxvd09wdGlvbnNcbiAgICAgIH0gPSBvcHRpb25zO1xuICAgICAgY29uc3QgcGxhY2VtZW50cyA9IGdldFBsYWNlbWVudExpc3QoYWxpZ25tZW50LCBhdXRvQWxpZ25tZW50LCBhbGxvd2VkUGxhY2VtZW50cyk7XG4gICAgICBjb25zdCBvdmVyZmxvdyA9IGF3YWl0IGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIGRldGVjdE92ZXJmbG93T3B0aW9ucyk7XG4gICAgICBjb25zdCBjdXJyZW50SW5kZXggPSAoX21pZGRsZXdhcmVEYXRhJGF1dG9QID0gKF9taWRkbGV3YXJlRGF0YSRhdXRvUDIgPSBtaWRkbGV3YXJlRGF0YS5hdXRvUGxhY2VtZW50KSA9PSBudWxsID8gdm9pZCAwIDogX21pZGRsZXdhcmVEYXRhJGF1dG9QMi5pbmRleCkgIT0gbnVsbCA/IF9taWRkbGV3YXJlRGF0YSRhdXRvUCA6IDA7XG4gICAgICBjb25zdCBjdXJyZW50UGxhY2VtZW50ID0gcGxhY2VtZW50c1tjdXJyZW50SW5kZXhdO1xuICAgICAgaWYgKGN1cnJlbnRQbGFjZW1lbnQgPT0gbnVsbCkge1xuICAgICAgICByZXR1cm4ge307XG4gICAgICB9XG4gICAgICBjb25zdCB7XG4gICAgICAgIG1haW4sXG4gICAgICAgIGNyb3NzXG4gICAgICB9ID0gZ2V0QWxpZ25tZW50U2lkZXMoY3VycmVudFBsYWNlbWVudCwgcmVjdHMsIGF3YWl0IChwbGF0Zm9ybTIuaXNSVEwgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5pc1JUTChlbGVtZW50cy5mbG9hdGluZykpKTtcbiAgICAgIGlmIChwbGFjZW1lbnQgIT09IGN1cnJlbnRQbGFjZW1lbnQpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICB4LFxuICAgICAgICAgIHksXG4gICAgICAgICAgcmVzZXQ6IHtcbiAgICAgICAgICAgIHBsYWNlbWVudDogcGxhY2VtZW50c1swXVxuICAgICAgICAgIH1cbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIGNvbnN0IGN1cnJlbnRPdmVyZmxvd3MgPSBbb3ZlcmZsb3dbZ2V0U2lkZShjdXJyZW50UGxhY2VtZW50KV0sIG92ZXJmbG93W21haW5dLCBvdmVyZmxvd1tjcm9zc11dO1xuICAgICAgY29uc3QgYWxsT3ZlcmZsb3dzID0gWy4uLihfbWlkZGxld2FyZURhdGEkYXV0b1AzID0gKF9taWRkbGV3YXJlRGF0YSRhdXRvUDQgPSBtaWRkbGV3YXJlRGF0YS5hdXRvUGxhY2VtZW50KSA9PSBudWxsID8gdm9pZCAwIDogX21pZGRsZXdhcmVEYXRhJGF1dG9QNC5vdmVyZmxvd3MpICE9IG51bGwgPyBfbWlkZGxld2FyZURhdGEkYXV0b1AzIDogW10sIHtcbiAgICAgICAgcGxhY2VtZW50OiBjdXJyZW50UGxhY2VtZW50LFxuICAgICAgICBvdmVyZmxvd3M6IGN1cnJlbnRPdmVyZmxvd3NcbiAgICAgIH1dO1xuICAgICAgY29uc3QgbmV4dFBsYWNlbWVudCA9IHBsYWNlbWVudHNbY3VycmVudEluZGV4ICsgMV07XG4gICAgICBpZiAobmV4dFBsYWNlbWVudCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgIGluZGV4OiBjdXJyZW50SW5kZXggKyAxLFxuICAgICAgICAgICAgb3ZlcmZsb3dzOiBhbGxPdmVyZmxvd3NcbiAgICAgICAgICB9LFxuICAgICAgICAgIHJlc2V0OiB7XG4gICAgICAgICAgICBwbGFjZW1lbnQ6IG5leHRQbGFjZW1lbnRcbiAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICBjb25zdCBwbGFjZW1lbnRzU29ydGVkQnlMZWFzdE92ZXJmbG93ID0gYWxsT3ZlcmZsb3dzLnNsaWNlKCkuc29ydCgoYSwgYikgPT4gYS5vdmVyZmxvd3NbMF0gLSBiLm92ZXJmbG93c1swXSk7XG4gICAgICBjb25zdCBwbGFjZW1lbnRUaGF0Rml0c09uQWxsU2lkZXMgPSAoX3BsYWNlbWVudHNTb3J0ZWRCeUxlID0gcGxhY2VtZW50c1NvcnRlZEJ5TGVhc3RPdmVyZmxvdy5maW5kKChfcmVmKSA9PiB7XG4gICAgICAgIGxldCB7XG4gICAgICAgICAgb3ZlcmZsb3dzXG4gICAgICAgIH0gPSBfcmVmO1xuICAgICAgICByZXR1cm4gb3ZlcmZsb3dzLmV2ZXJ5KChvdmVyZmxvdzIpID0+IG92ZXJmbG93MiA8PSAwKTtcbiAgICAgIH0pKSA9PSBudWxsID8gdm9pZCAwIDogX3BsYWNlbWVudHNTb3J0ZWRCeUxlLnBsYWNlbWVudDtcbiAgICAgIGNvbnN0IHJlc2V0UGxhY2VtZW50ID0gcGxhY2VtZW50VGhhdEZpdHNPbkFsbFNpZGVzICE9IG51bGwgPyBwbGFjZW1lbnRUaGF0Rml0c09uQWxsU2lkZXMgOiBwbGFjZW1lbnRzU29ydGVkQnlMZWFzdE92ZXJmbG93WzBdLnBsYWNlbWVudDtcbiAgICAgIGlmIChyZXNldFBsYWNlbWVudCAhPT0gcGxhY2VtZW50KSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgaW5kZXg6IGN1cnJlbnRJbmRleCArIDEsXG4gICAgICAgICAgICBvdmVyZmxvd3M6IGFsbE92ZXJmbG93c1xuICAgICAgICAgIH0sXG4gICAgICAgICAgcmVzZXQ6IHtcbiAgICAgICAgICAgIHBsYWNlbWVudDogcmVzZXRQbGFjZW1lbnRcbiAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICByZXR1cm4ge307XG4gICAgfVxuICB9O1xufTtcbmZ1bmN0aW9uIGdldEV4cGFuZGVkUGxhY2VtZW50cyhwbGFjZW1lbnQpIHtcbiAgY29uc3Qgb3Bwb3NpdGVQbGFjZW1lbnQgPSBnZXRPcHBvc2l0ZVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICByZXR1cm4gW2dldE9wcG9zaXRlQWxpZ25tZW50UGxhY2VtZW50KHBsYWNlbWVudCksIG9wcG9zaXRlUGxhY2VtZW50LCBnZXRPcHBvc2l0ZUFsaWdubWVudFBsYWNlbWVudChvcHBvc2l0ZVBsYWNlbWVudCldO1xufVxudmFyIGZsaXAgPSBmdW5jdGlvbihvcHRpb25zKSB7XG4gIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICBvcHRpb25zID0ge307XG4gIH1cbiAgcmV0dXJuIHtcbiAgICBuYW1lOiBcImZsaXBcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIHZhciBfbWlkZGxld2FyZURhdGEkZmxpcDtcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgcGxhY2VtZW50LFxuICAgICAgICBtaWRkbGV3YXJlRGF0YSxcbiAgICAgICAgcmVjdHMsXG4gICAgICAgIGluaXRpYWxQbGFjZW1lbnQsXG4gICAgICAgIHBsYXRmb3JtOiBwbGF0Zm9ybTIsXG4gICAgICAgIGVsZW1lbnRzXG4gICAgICB9ID0gbWlkZGxld2FyZUFyZ3VtZW50cztcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgbWFpbkF4aXM6IGNoZWNrTWFpbkF4aXMgPSB0cnVlLFxuICAgICAgICBjcm9zc0F4aXM6IGNoZWNrQ3Jvc3NBeGlzID0gdHJ1ZSxcbiAgICAgICAgZmFsbGJhY2tQbGFjZW1lbnRzOiBzcGVjaWZpZWRGYWxsYmFja1BsYWNlbWVudHMsXG4gICAgICAgIGZhbGxiYWNrU3RyYXRlZ3kgPSBcImJlc3RGaXRcIixcbiAgICAgICAgZmxpcEFsaWdubWVudCA9IHRydWUsXG4gICAgICAgIC4uLmRldGVjdE92ZXJmbG93T3B0aW9uc1xuICAgICAgfSA9IG9wdGlvbnM7XG4gICAgICBjb25zdCBzaWRlID0gZ2V0U2lkZShwbGFjZW1lbnQpO1xuICAgICAgY29uc3QgaXNCYXNlUGxhY2VtZW50ID0gc2lkZSA9PT0gaW5pdGlhbFBsYWNlbWVudDtcbiAgICAgIGNvbnN0IGZhbGxiYWNrUGxhY2VtZW50cyA9IHNwZWNpZmllZEZhbGxiYWNrUGxhY2VtZW50cyB8fCAoaXNCYXNlUGxhY2VtZW50IHx8ICFmbGlwQWxpZ25tZW50ID8gW2dldE9wcG9zaXRlUGxhY2VtZW50KGluaXRpYWxQbGFjZW1lbnQpXSA6IGdldEV4cGFuZGVkUGxhY2VtZW50cyhpbml0aWFsUGxhY2VtZW50KSk7XG4gICAgICBjb25zdCBwbGFjZW1lbnRzID0gW2luaXRpYWxQbGFjZW1lbnQsIC4uLmZhbGxiYWNrUGxhY2VtZW50c107XG4gICAgICBjb25zdCBvdmVyZmxvdyA9IGF3YWl0IGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIGRldGVjdE92ZXJmbG93T3B0aW9ucyk7XG4gICAgICBjb25zdCBvdmVyZmxvd3MgPSBbXTtcbiAgICAgIGxldCBvdmVyZmxvd3NEYXRhID0gKChfbWlkZGxld2FyZURhdGEkZmxpcCA9IG1pZGRsZXdhcmVEYXRhLmZsaXApID09IG51bGwgPyB2b2lkIDAgOiBfbWlkZGxld2FyZURhdGEkZmxpcC5vdmVyZmxvd3MpIHx8IFtdO1xuICAgICAgaWYgKGNoZWNrTWFpbkF4aXMpIHtcbiAgICAgICAgb3ZlcmZsb3dzLnB1c2gob3ZlcmZsb3dbc2lkZV0pO1xuICAgICAgfVxuICAgICAgaWYgKGNoZWNrQ3Jvc3NBeGlzKSB7XG4gICAgICAgIGNvbnN0IHtcbiAgICAgICAgICBtYWluLFxuICAgICAgICAgIGNyb3NzXG4gICAgICAgIH0gPSBnZXRBbGlnbm1lbnRTaWRlcyhwbGFjZW1lbnQsIHJlY3RzLCBhd2FpdCAocGxhdGZvcm0yLmlzUlRMID09IG51bGwgPyB2b2lkIDAgOiBwbGF0Zm9ybTIuaXNSVEwoZWxlbWVudHMuZmxvYXRpbmcpKSk7XG4gICAgICAgIG92ZXJmbG93cy5wdXNoKG92ZXJmbG93W21haW5dLCBvdmVyZmxvd1tjcm9zc10pO1xuICAgICAgfVxuICAgICAgb3ZlcmZsb3dzRGF0YSA9IFsuLi5vdmVyZmxvd3NEYXRhLCB7XG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgb3ZlcmZsb3dzXG4gICAgICB9XTtcbiAgICAgIGlmICghb3ZlcmZsb3dzLmV2ZXJ5KChzaWRlMikgPT4gc2lkZTIgPD0gMCkpIHtcbiAgICAgICAgdmFyIF9taWRkbGV3YXJlRGF0YSRmbGlwJCwgX21pZGRsZXdhcmVEYXRhJGZsaXAyO1xuICAgICAgICBjb25zdCBuZXh0SW5kZXggPSAoKF9taWRkbGV3YXJlRGF0YSRmbGlwJCA9IChfbWlkZGxld2FyZURhdGEkZmxpcDIgPSBtaWRkbGV3YXJlRGF0YS5mbGlwKSA9PSBudWxsID8gdm9pZCAwIDogX21pZGRsZXdhcmVEYXRhJGZsaXAyLmluZGV4KSAhPSBudWxsID8gX21pZGRsZXdhcmVEYXRhJGZsaXAkIDogMCkgKyAxO1xuICAgICAgICBjb25zdCBuZXh0UGxhY2VtZW50ID0gcGxhY2VtZW50c1tuZXh0SW5kZXhdO1xuICAgICAgICBpZiAobmV4dFBsYWNlbWVudCkge1xuICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgIGluZGV4OiBuZXh0SW5kZXgsXG4gICAgICAgICAgICAgIG92ZXJmbG93czogb3ZlcmZsb3dzRGF0YVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHJlc2V0OiB7XG4gICAgICAgICAgICAgIHBsYWNlbWVudDogbmV4dFBsYWNlbWVudFxuICAgICAgICAgICAgfVxuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgbGV0IHJlc2V0UGxhY2VtZW50ID0gXCJib3R0b21cIjtcbiAgICAgICAgc3dpdGNoIChmYWxsYmFja1N0cmF0ZWd5KSB7XG4gICAgICAgICAgY2FzZSBcImJlc3RGaXRcIjoge1xuICAgICAgICAgICAgdmFyIF9vdmVyZmxvd3NEYXRhJG1hcCRzbztcbiAgICAgICAgICAgIGNvbnN0IHBsYWNlbWVudDIgPSAoX292ZXJmbG93c0RhdGEkbWFwJHNvID0gb3ZlcmZsb3dzRGF0YS5tYXAoKGQpID0+IFtkLCBkLm92ZXJmbG93cy5maWx0ZXIoKG92ZXJmbG93MikgPT4gb3ZlcmZsb3cyID4gMCkucmVkdWNlKChhY2MsIG92ZXJmbG93MikgPT4gYWNjICsgb3ZlcmZsb3cyLCAwKV0pLnNvcnQoKGEsIGIpID0+IGFbMV0gLSBiWzFdKVswXSkgPT0gbnVsbCA/IHZvaWQgMCA6IF9vdmVyZmxvd3NEYXRhJG1hcCRzb1swXS5wbGFjZW1lbnQ7XG4gICAgICAgICAgICBpZiAocGxhY2VtZW50Mikge1xuICAgICAgICAgICAgICByZXNldFBsYWNlbWVudCA9IHBsYWNlbWVudDI7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgICAgY2FzZSBcImluaXRpYWxQbGFjZW1lbnRcIjpcbiAgICAgICAgICAgIHJlc2V0UGxhY2VtZW50ID0gaW5pdGlhbFBsYWNlbWVudDtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgICAgIGlmIChwbGFjZW1lbnQgIT09IHJlc2V0UGxhY2VtZW50KSB7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlc2V0OiB7XG4gICAgICAgICAgICAgIHBsYWNlbWVudDogcmVzZXRQbGFjZW1lbnRcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9O1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4ge307XG4gICAgfVxuICB9O1xufTtcbmZ1bmN0aW9uIGdldFNpZGVPZmZzZXRzKG92ZXJmbG93LCByZWN0KSB7XG4gIHJldHVybiB7XG4gICAgdG9wOiBvdmVyZmxvdy50b3AgLSByZWN0LmhlaWdodCxcbiAgICByaWdodDogb3ZlcmZsb3cucmlnaHQgLSByZWN0LndpZHRoLFxuICAgIGJvdHRvbTogb3ZlcmZsb3cuYm90dG9tIC0gcmVjdC5oZWlnaHQsXG4gICAgbGVmdDogb3ZlcmZsb3cubGVmdCAtIHJlY3Qud2lkdGhcbiAgfTtcbn1cbmZ1bmN0aW9uIGlzQW55U2lkZUZ1bGx5Q2xpcHBlZChvdmVyZmxvdykge1xuICByZXR1cm4gc2lkZXMuc29tZSgoc2lkZSkgPT4gb3ZlcmZsb3dbc2lkZV0gPj0gMCk7XG59XG52YXIgaGlkZSA9IGZ1bmN0aW9uKF90ZW1wKSB7XG4gIGxldCB7XG4gICAgc3RyYXRlZ3kgPSBcInJlZmVyZW5jZUhpZGRlblwiLFxuICAgIC4uLmRldGVjdE92ZXJmbG93T3B0aW9uc1xuICB9ID0gX3RlbXAgPT09IHZvaWQgMCA/IHt9IDogX3RlbXA7XG4gIHJldHVybiB7XG4gICAgbmFtZTogXCJoaWRlXCIsXG4gICAgYXN5bmMgZm4obWlkZGxld2FyZUFyZ3VtZW50cykge1xuICAgICAgY29uc3Qge1xuICAgICAgICByZWN0c1xuICAgICAgfSA9IG1pZGRsZXdhcmVBcmd1bWVudHM7XG4gICAgICBzd2l0Y2ggKHN0cmF0ZWd5KSB7XG4gICAgICAgIGNhc2UgXCJyZWZlcmVuY2VIaWRkZW5cIjoge1xuICAgICAgICAgIGNvbnN0IG92ZXJmbG93ID0gYXdhaXQgZGV0ZWN0T3ZlcmZsb3cobWlkZGxld2FyZUFyZ3VtZW50cywge1xuICAgICAgICAgICAgLi4uZGV0ZWN0T3ZlcmZsb3dPcHRpb25zLFxuICAgICAgICAgICAgZWxlbWVudENvbnRleHQ6IFwicmVmZXJlbmNlXCJcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBjb25zdCBvZmZzZXRzID0gZ2V0U2lkZU9mZnNldHMob3ZlcmZsb3csIHJlY3RzLnJlZmVyZW5jZSk7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgcmVmZXJlbmNlSGlkZGVuT2Zmc2V0czogb2Zmc2V0cyxcbiAgICAgICAgICAgICAgcmVmZXJlbmNlSGlkZGVuOiBpc0FueVNpZGVGdWxseUNsaXBwZWQob2Zmc2V0cylcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9O1xuICAgICAgICB9XG4gICAgICAgIGNhc2UgXCJlc2NhcGVkXCI6IHtcbiAgICAgICAgICBjb25zdCBvdmVyZmxvdyA9IGF3YWl0IGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIHtcbiAgICAgICAgICAgIC4uLmRldGVjdE92ZXJmbG93T3B0aW9ucyxcbiAgICAgICAgICAgIGFsdEJvdW5kYXJ5OiB0cnVlXG4gICAgICAgICAgfSk7XG4gICAgICAgICAgY29uc3Qgb2Zmc2V0cyA9IGdldFNpZGVPZmZzZXRzKG92ZXJmbG93LCByZWN0cy5mbG9hdGluZyk7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgZXNjYXBlZE9mZnNldHM6IG9mZnNldHMsXG4gICAgICAgICAgICAgIGVzY2FwZWQ6IGlzQW55U2lkZUZ1bGx5Q2xpcHBlZChvZmZzZXRzKVxuICAgICAgICAgICAgfVxuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgZGVmYXVsdDoge1xuICAgICAgICAgIHJldHVybiB7fTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cbiAgfTtcbn07XG5mdW5jdGlvbiBjb252ZXJ0VmFsdWVUb0Nvb3JkcyhwbGFjZW1lbnQsIHJlY3RzLCB2YWx1ZSwgcnRsKSB7XG4gIGlmIChydGwgPT09IHZvaWQgMCkge1xuICAgIHJ0bCA9IGZhbHNlO1xuICB9XG4gIGNvbnN0IHNpZGUgPSBnZXRTaWRlKHBsYWNlbWVudCk7XG4gIGNvbnN0IGFsaWdubWVudCA9IGdldEFsaWdubWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBpc1ZlcnRpY2FsID0gZ2V0TWFpbkF4aXNGcm9tUGxhY2VtZW50KHBsYWNlbWVudCkgPT09IFwieFwiO1xuICBjb25zdCBtYWluQXhpc011bHRpID0gW1wibGVmdFwiLCBcInRvcFwiXS5pbmNsdWRlcyhzaWRlKSA/IC0xIDogMTtcbiAgY29uc3QgY3Jvc3NBeGlzTXVsdGkgPSBydGwgJiYgaXNWZXJ0aWNhbCA/IC0xIDogMTtcbiAgY29uc3QgcmF3VmFsdWUgPSB0eXBlb2YgdmFsdWUgPT09IFwiZnVuY3Rpb25cIiA/IHZhbHVlKHtcbiAgICAuLi5yZWN0cyxcbiAgICBwbGFjZW1lbnRcbiAgfSkgOiB2YWx1ZTtcbiAgbGV0IHtcbiAgICBtYWluQXhpcyxcbiAgICBjcm9zc0F4aXMsXG4gICAgYWxpZ25tZW50QXhpc1xuICB9ID0gdHlwZW9mIHJhd1ZhbHVlID09PSBcIm51bWJlclwiID8ge1xuICAgIG1haW5BeGlzOiByYXdWYWx1ZSxcbiAgICBjcm9zc0F4aXM6IDAsXG4gICAgYWxpZ25tZW50QXhpczogbnVsbFxuICB9IDoge1xuICAgIG1haW5BeGlzOiAwLFxuICAgIGNyb3NzQXhpczogMCxcbiAgICBhbGlnbm1lbnRBeGlzOiBudWxsLFxuICAgIC4uLnJhd1ZhbHVlXG4gIH07XG4gIGlmIChhbGlnbm1lbnQgJiYgdHlwZW9mIGFsaWdubWVudEF4aXMgPT09IFwibnVtYmVyXCIpIHtcbiAgICBjcm9zc0F4aXMgPSBhbGlnbm1lbnQgPT09IFwiZW5kXCIgPyBhbGlnbm1lbnRBeGlzICogLTEgOiBhbGlnbm1lbnRBeGlzO1xuICB9XG4gIHJldHVybiBpc1ZlcnRpY2FsID8ge1xuICAgIHg6IGNyb3NzQXhpcyAqIGNyb3NzQXhpc011bHRpLFxuICAgIHk6IG1haW5BeGlzICogbWFpbkF4aXNNdWx0aVxuICB9IDoge1xuICAgIHg6IG1haW5BeGlzICogbWFpbkF4aXNNdWx0aSxcbiAgICB5OiBjcm9zc0F4aXMgKiBjcm9zc0F4aXNNdWx0aVxuICB9O1xufVxudmFyIG9mZnNldCA9IGZ1bmN0aW9uKHZhbHVlKSB7XG4gIGlmICh2YWx1ZSA9PT0gdm9pZCAwKSB7XG4gICAgdmFsdWUgPSAwO1xuICB9XG4gIHJldHVybiB7XG4gICAgbmFtZTogXCJvZmZzZXRcIixcbiAgICBvcHRpb25zOiB2YWx1ZSxcbiAgICBhc3luYyBmbihtaWRkbGV3YXJlQXJndW1lbnRzKSB7XG4gICAgICBjb25zdCB7XG4gICAgICAgIHgsXG4gICAgICAgIHksXG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgcmVjdHMsXG4gICAgICAgIHBsYXRmb3JtOiBwbGF0Zm9ybTIsXG4gICAgICAgIGVsZW1lbnRzXG4gICAgICB9ID0gbWlkZGxld2FyZUFyZ3VtZW50cztcbiAgICAgIGNvbnN0IGRpZmZDb29yZHMgPSBjb252ZXJ0VmFsdWVUb0Nvb3JkcyhwbGFjZW1lbnQsIHJlY3RzLCB2YWx1ZSwgYXdhaXQgKHBsYXRmb3JtMi5pc1JUTCA9PSBudWxsID8gdm9pZCAwIDogcGxhdGZvcm0yLmlzUlRMKGVsZW1lbnRzLmZsb2F0aW5nKSkpO1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgeDogeCArIGRpZmZDb29yZHMueCxcbiAgICAgICAgeTogeSArIGRpZmZDb29yZHMueSxcbiAgICAgICAgZGF0YTogZGlmZkNvb3Jkc1xuICAgICAgfTtcbiAgICB9XG4gIH07XG59O1xuZnVuY3Rpb24gZ2V0Q3Jvc3NBeGlzKGF4aXMpIHtcbiAgcmV0dXJuIGF4aXMgPT09IFwieFwiID8gXCJ5XCIgOiBcInhcIjtcbn1cbnZhciBzaGlmdCA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcbiAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgIG9wdGlvbnMgPSB7fTtcbiAgfVxuICByZXR1cm4ge1xuICAgIG5hbWU6IFwic2hpZnRcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgeCxcbiAgICAgICAgeSxcbiAgICAgICAgcGxhY2VtZW50XG4gICAgICB9ID0gbWlkZGxld2FyZUFyZ3VtZW50cztcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgbWFpbkF4aXM6IGNoZWNrTWFpbkF4aXMgPSB0cnVlLFxuICAgICAgICBjcm9zc0F4aXM6IGNoZWNrQ3Jvc3NBeGlzID0gZmFsc2UsXG4gICAgICAgIGxpbWl0ZXIgPSB7XG4gICAgICAgICAgZm46IChfcmVmKSA9PiB7XG4gICAgICAgICAgICBsZXQge1xuICAgICAgICAgICAgICB4OiB4MixcbiAgICAgICAgICAgICAgeTogeTJcbiAgICAgICAgICAgIH0gPSBfcmVmO1xuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgeDogeDIsXG4gICAgICAgICAgICAgIHk6IHkyXG4gICAgICAgICAgICB9O1xuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgLi4uZGV0ZWN0T3ZlcmZsb3dPcHRpb25zXG4gICAgICB9ID0gb3B0aW9ucztcbiAgICAgIGNvbnN0IGNvb3JkcyA9IHtcbiAgICAgICAgeCxcbiAgICAgICAgeVxuICAgICAgfTtcbiAgICAgIGNvbnN0IG92ZXJmbG93ID0gYXdhaXQgZGV0ZWN0T3ZlcmZsb3cobWlkZGxld2FyZUFyZ3VtZW50cywgZGV0ZWN0T3ZlcmZsb3dPcHRpb25zKTtcbiAgICAgIGNvbnN0IG1haW5BeGlzID0gZ2V0TWFpbkF4aXNGcm9tUGxhY2VtZW50KGdldFNpZGUocGxhY2VtZW50KSk7XG4gICAgICBjb25zdCBjcm9zc0F4aXMgPSBnZXRDcm9zc0F4aXMobWFpbkF4aXMpO1xuICAgICAgbGV0IG1haW5BeGlzQ29vcmQgPSBjb29yZHNbbWFpbkF4aXNdO1xuICAgICAgbGV0IGNyb3NzQXhpc0Nvb3JkID0gY29vcmRzW2Nyb3NzQXhpc107XG4gICAgICBpZiAoY2hlY2tNYWluQXhpcykge1xuICAgICAgICBjb25zdCBtaW5TaWRlID0gbWFpbkF4aXMgPT09IFwieVwiID8gXCJ0b3BcIiA6IFwibGVmdFwiO1xuICAgICAgICBjb25zdCBtYXhTaWRlID0gbWFpbkF4aXMgPT09IFwieVwiID8gXCJib3R0b21cIiA6IFwicmlnaHRcIjtcbiAgICAgICAgY29uc3QgbWluMyA9IG1haW5BeGlzQ29vcmQgKyBvdmVyZmxvd1ttaW5TaWRlXTtcbiAgICAgICAgY29uc3QgbWF4MyA9IG1haW5BeGlzQ29vcmQgLSBvdmVyZmxvd1ttYXhTaWRlXTtcbiAgICAgICAgbWFpbkF4aXNDb29yZCA9IHdpdGhpbihtaW4zLCBtYWluQXhpc0Nvb3JkLCBtYXgzKTtcbiAgICAgIH1cbiAgICAgIGlmIChjaGVja0Nyb3NzQXhpcykge1xuICAgICAgICBjb25zdCBtaW5TaWRlID0gY3Jvc3NBeGlzID09PSBcInlcIiA/IFwidG9wXCIgOiBcImxlZnRcIjtcbiAgICAgICAgY29uc3QgbWF4U2lkZSA9IGNyb3NzQXhpcyA9PT0gXCJ5XCIgPyBcImJvdHRvbVwiIDogXCJyaWdodFwiO1xuICAgICAgICBjb25zdCBtaW4zID0gY3Jvc3NBeGlzQ29vcmQgKyBvdmVyZmxvd1ttaW5TaWRlXTtcbiAgICAgICAgY29uc3QgbWF4MyA9IGNyb3NzQXhpc0Nvb3JkIC0gb3ZlcmZsb3dbbWF4U2lkZV07XG4gICAgICAgIGNyb3NzQXhpc0Nvb3JkID0gd2l0aGluKG1pbjMsIGNyb3NzQXhpc0Nvb3JkLCBtYXgzKTtcbiAgICAgIH1cbiAgICAgIGNvbnN0IGxpbWl0ZWRDb29yZHMgPSBsaW1pdGVyLmZuKHtcbiAgICAgICAgLi4ubWlkZGxld2FyZUFyZ3VtZW50cyxcbiAgICAgICAgW21haW5BeGlzXTogbWFpbkF4aXNDb29yZCxcbiAgICAgICAgW2Nyb3NzQXhpc106IGNyb3NzQXhpc0Nvb3JkXG4gICAgICB9KTtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIC4uLmxpbWl0ZWRDb29yZHMsXG4gICAgICAgIGRhdGE6IHtcbiAgICAgICAgICB4OiBsaW1pdGVkQ29vcmRzLnggLSB4LFxuICAgICAgICAgIHk6IGxpbWl0ZWRDb29yZHMueSAtIHlcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICB9XG4gIH07XG59O1xudmFyIHNpemUgPSBmdW5jdGlvbihvcHRpb25zKSB7XG4gIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICBvcHRpb25zID0ge307XG4gIH1cbiAgcmV0dXJuIHtcbiAgICBuYW1lOiBcInNpemVcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgcGxhY2VtZW50LFxuICAgICAgICByZWN0cyxcbiAgICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMixcbiAgICAgICAgZWxlbWVudHNcbiAgICAgIH0gPSBtaWRkbGV3YXJlQXJndW1lbnRzO1xuICAgICAgY29uc3Qge1xuICAgICAgICBhcHBseSxcbiAgICAgICAgLi4uZGV0ZWN0T3ZlcmZsb3dPcHRpb25zXG4gICAgICB9ID0gb3B0aW9ucztcbiAgICAgIGNvbnN0IG92ZXJmbG93ID0gYXdhaXQgZGV0ZWN0T3ZlcmZsb3cobWlkZGxld2FyZUFyZ3VtZW50cywgZGV0ZWN0T3ZlcmZsb3dPcHRpb25zKTtcbiAgICAgIGNvbnN0IHNpZGUgPSBnZXRTaWRlKHBsYWNlbWVudCk7XG4gICAgICBjb25zdCBhbGlnbm1lbnQgPSBnZXRBbGlnbm1lbnQocGxhY2VtZW50KTtcbiAgICAgIGxldCBoZWlnaHRTaWRlO1xuICAgICAgbGV0IHdpZHRoU2lkZTtcbiAgICAgIGlmIChzaWRlID09PSBcInRvcFwiIHx8IHNpZGUgPT09IFwiYm90dG9tXCIpIHtcbiAgICAgICAgaGVpZ2h0U2lkZSA9IHNpZGU7XG4gICAgICAgIHdpZHRoU2lkZSA9IGFsaWdubWVudCA9PT0gKGF3YWl0IChwbGF0Zm9ybTIuaXNSVEwgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5pc1JUTChlbGVtZW50cy5mbG9hdGluZykpID8gXCJzdGFydFwiIDogXCJlbmRcIikgPyBcImxlZnRcIiA6IFwicmlnaHRcIjtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHdpZHRoU2lkZSA9IHNpZGU7XG4gICAgICAgIGhlaWdodFNpZGUgPSBhbGlnbm1lbnQgPT09IFwiZW5kXCIgPyBcInRvcFwiIDogXCJib3R0b21cIjtcbiAgICAgIH1cbiAgICAgIGNvbnN0IHhNaW4gPSBtYXgob3ZlcmZsb3cubGVmdCwgMCk7XG4gICAgICBjb25zdCB4TWF4ID0gbWF4KG92ZXJmbG93LnJpZ2h0LCAwKTtcbiAgICAgIGNvbnN0IHlNaW4gPSBtYXgob3ZlcmZsb3cudG9wLCAwKTtcbiAgICAgIGNvbnN0IHlNYXggPSBtYXgob3ZlcmZsb3cuYm90dG9tLCAwKTtcbiAgICAgIGNvbnN0IGRpbWVuc2lvbnMgPSB7XG4gICAgICAgIGhlaWdodDogcmVjdHMuZmxvYXRpbmcuaGVpZ2h0IC0gKFtcImxlZnRcIiwgXCJyaWdodFwiXS5pbmNsdWRlcyhwbGFjZW1lbnQpID8gMiAqICh5TWluICE9PSAwIHx8IHlNYXggIT09IDAgPyB5TWluICsgeU1heCA6IG1heChvdmVyZmxvdy50b3AsIG92ZXJmbG93LmJvdHRvbSkpIDogb3ZlcmZsb3dbaGVpZ2h0U2lkZV0pLFxuICAgICAgICB3aWR0aDogcmVjdHMuZmxvYXRpbmcud2lkdGggLSAoW1widG9wXCIsIFwiYm90dG9tXCJdLmluY2x1ZGVzKHBsYWNlbWVudCkgPyAyICogKHhNaW4gIT09IDAgfHwgeE1heCAhPT0gMCA/IHhNaW4gKyB4TWF4IDogbWF4KG92ZXJmbG93LmxlZnQsIG92ZXJmbG93LnJpZ2h0KSkgOiBvdmVyZmxvd1t3aWR0aFNpZGVdKVxuICAgICAgfTtcbiAgICAgIGNvbnN0IHByZXZEaW1lbnNpb25zID0gYXdhaXQgcGxhdGZvcm0yLmdldERpbWVuc2lvbnMoZWxlbWVudHMuZmxvYXRpbmcpO1xuICAgICAgYXBwbHkgPT0gbnVsbCA/IHZvaWQgMCA6IGFwcGx5KHtcbiAgICAgICAgLi4uZGltZW5zaW9ucyxcbiAgICAgICAgLi4ucmVjdHNcbiAgICAgIH0pO1xuICAgICAgY29uc3QgbmV4dERpbWVuc2lvbnMgPSBhd2FpdCBwbGF0Zm9ybTIuZ2V0RGltZW5zaW9ucyhlbGVtZW50cy5mbG9hdGluZyk7XG4gICAgICBpZiAocHJldkRpbWVuc2lvbnMud2lkdGggIT09IG5leHREaW1lbnNpb25zLndpZHRoIHx8IHByZXZEaW1lbnNpb25zLmhlaWdodCAhPT0gbmV4dERpbWVuc2lvbnMuaGVpZ2h0KSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgcmVzZXQ6IHtcbiAgICAgICAgICAgIHJlY3RzOiB0cnVlXG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgfVxuICAgICAgcmV0dXJuIHt9O1xuICAgIH1cbiAgfTtcbn07XG52YXIgaW5saW5lID0gZnVuY3Rpb24ob3B0aW9ucykge1xuICBpZiAob3B0aW9ucyA9PT0gdm9pZCAwKSB7XG4gICAgb3B0aW9ucyA9IHt9O1xuICB9XG4gIHJldHVybiB7XG4gICAgbmFtZTogXCJpbmxpbmVcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIHZhciBfYXdhaXQkcGxhdGZvcm0kZ2V0Q2w7XG4gICAgICBjb25zdCB7XG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgZWxlbWVudHMsXG4gICAgICAgIHJlY3RzLFxuICAgICAgICBwbGF0Zm9ybTogcGxhdGZvcm0yLFxuICAgICAgICBzdHJhdGVneVxuICAgICAgfSA9IG1pZGRsZXdhcmVBcmd1bWVudHM7XG4gICAgICBjb25zdCB7XG4gICAgICAgIHBhZGRpbmcgPSAyLFxuICAgICAgICB4LFxuICAgICAgICB5XG4gICAgICB9ID0gb3B0aW9ucztcbiAgICAgIGNvbnN0IGZhbGxiYWNrID0gcmVjdFRvQ2xpZW50UmVjdChwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3QgPyBhd2FpdCBwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3Qoe1xuICAgICAgICByZWN0OiByZWN0cy5yZWZlcmVuY2UsXG4gICAgICAgIG9mZnNldFBhcmVudDogYXdhaXQgKHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQoZWxlbWVudHMuZmxvYXRpbmcpKSxcbiAgICAgICAgc3RyYXRlZ3lcbiAgICAgIH0pIDogcmVjdHMucmVmZXJlbmNlKTtcbiAgICAgIGNvbnN0IGNsaWVudFJlY3RzID0gKF9hd2FpdCRwbGF0Zm9ybSRnZXRDbCA9IGF3YWl0IChwbGF0Zm9ybTIuZ2V0Q2xpZW50UmVjdHMgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5nZXRDbGllbnRSZWN0cyhlbGVtZW50cy5yZWZlcmVuY2UpKSkgIT0gbnVsbCA/IF9hd2FpdCRwbGF0Zm9ybSRnZXRDbCA6IFtdO1xuICAgICAgY29uc3QgcGFkZGluZ09iamVjdCA9IGdldFNpZGVPYmplY3RGcm9tUGFkZGluZyhwYWRkaW5nKTtcbiAgICAgIGZ1bmN0aW9uIGdldEJvdW5kaW5nQ2xpZW50UmVjdDIoKSB7XG4gICAgICAgIGlmIChjbGllbnRSZWN0cy5sZW5ndGggPT09IDIgJiYgY2xpZW50UmVjdHNbMF0ubGVmdCA+IGNsaWVudFJlY3RzWzFdLnJpZ2h0ICYmIHggIT0gbnVsbCAmJiB5ICE9IG51bGwpIHtcbiAgICAgICAgICB2YXIgX2NsaWVudFJlY3RzJGZpbmQ7XG4gICAgICAgICAgcmV0dXJuIChfY2xpZW50UmVjdHMkZmluZCA9IGNsaWVudFJlY3RzLmZpbmQoKHJlY3QpID0+IHggPiByZWN0LmxlZnQgLSBwYWRkaW5nT2JqZWN0LmxlZnQgJiYgeCA8IHJlY3QucmlnaHQgKyBwYWRkaW5nT2JqZWN0LnJpZ2h0ICYmIHkgPiByZWN0LnRvcCAtIHBhZGRpbmdPYmplY3QudG9wICYmIHkgPCByZWN0LmJvdHRvbSArIHBhZGRpbmdPYmplY3QuYm90dG9tKSkgIT0gbnVsbCA/IF9jbGllbnRSZWN0cyRmaW5kIDogZmFsbGJhY2s7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGNsaWVudFJlY3RzLmxlbmd0aCA+PSAyKSB7XG4gICAgICAgICAgaWYgKGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpID09PSBcInhcIikge1xuICAgICAgICAgICAgY29uc3QgZmlyc3RSZWN0ID0gY2xpZW50UmVjdHNbMF07XG4gICAgICAgICAgICBjb25zdCBsYXN0UmVjdCA9IGNsaWVudFJlY3RzW2NsaWVudFJlY3RzLmxlbmd0aCAtIDFdO1xuICAgICAgICAgICAgY29uc3QgaXNUb3AgPSBnZXRTaWRlKHBsYWNlbWVudCkgPT09IFwidG9wXCI7XG4gICAgICAgICAgICBjb25zdCB0b3AyID0gZmlyc3RSZWN0LnRvcDtcbiAgICAgICAgICAgIGNvbnN0IGJvdHRvbTIgPSBsYXN0UmVjdC5ib3R0b207XG4gICAgICAgICAgICBjb25zdCBsZWZ0MiA9IGlzVG9wID8gZmlyc3RSZWN0LmxlZnQgOiBsYXN0UmVjdC5sZWZ0O1xuICAgICAgICAgICAgY29uc3QgcmlnaHQyID0gaXNUb3AgPyBmaXJzdFJlY3QucmlnaHQgOiBsYXN0UmVjdC5yaWdodDtcbiAgICAgICAgICAgIGNvbnN0IHdpZHRoMiA9IHJpZ2h0MiAtIGxlZnQyO1xuICAgICAgICAgICAgY29uc3QgaGVpZ2h0MiA9IGJvdHRvbTIgLSB0b3AyO1xuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgdG9wOiB0b3AyLFxuICAgICAgICAgICAgICBib3R0b206IGJvdHRvbTIsXG4gICAgICAgICAgICAgIGxlZnQ6IGxlZnQyLFxuICAgICAgICAgICAgICByaWdodDogcmlnaHQyLFxuICAgICAgICAgICAgICB3aWR0aDogd2lkdGgyLFxuICAgICAgICAgICAgICBoZWlnaHQ6IGhlaWdodDIsXG4gICAgICAgICAgICAgIHg6IGxlZnQyLFxuICAgICAgICAgICAgICB5OiB0b3AyXG4gICAgICAgICAgICB9O1xuICAgICAgICAgIH1cbiAgICAgICAgICBjb25zdCBpc0xlZnRTaWRlID0gZ2V0U2lkZShwbGFjZW1lbnQpID09PSBcImxlZnRcIjtcbiAgICAgICAgICBjb25zdCBtYXhSaWdodCA9IG1heCguLi5jbGllbnRSZWN0cy5tYXAoKHJlY3QpID0+IHJlY3QucmlnaHQpKTtcbiAgICAgICAgICBjb25zdCBtaW5MZWZ0ID0gbWluKC4uLmNsaWVudFJlY3RzLm1hcCgocmVjdCkgPT4gcmVjdC5sZWZ0KSk7XG4gICAgICAgICAgY29uc3QgbWVhc3VyZVJlY3RzID0gY2xpZW50UmVjdHMuZmlsdGVyKChyZWN0KSA9PiBpc0xlZnRTaWRlID8gcmVjdC5sZWZ0ID09PSBtaW5MZWZ0IDogcmVjdC5yaWdodCA9PT0gbWF4UmlnaHQpO1xuICAgICAgICAgIGNvbnN0IHRvcCA9IG1lYXN1cmVSZWN0c1swXS50b3A7XG4gICAgICAgICAgY29uc3QgYm90dG9tID0gbWVhc3VyZVJlY3RzW21lYXN1cmVSZWN0cy5sZW5ndGggLSAxXS5ib3R0b207XG4gICAgICAgICAgY29uc3QgbGVmdCA9IG1pbkxlZnQ7XG4gICAgICAgICAgY29uc3QgcmlnaHQgPSBtYXhSaWdodDtcbiAgICAgICAgICBjb25zdCB3aWR0aCA9IHJpZ2h0IC0gbGVmdDtcbiAgICAgICAgICBjb25zdCBoZWlnaHQgPSBib3R0b20gLSB0b3A7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHRvcCxcbiAgICAgICAgICAgIGJvdHRvbSxcbiAgICAgICAgICAgIGxlZnQsXG4gICAgICAgICAgICByaWdodCxcbiAgICAgICAgICAgIHdpZHRoLFxuICAgICAgICAgICAgaGVpZ2h0LFxuICAgICAgICAgICAgeDogbGVmdCxcbiAgICAgICAgICAgIHk6IHRvcFxuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIGZhbGxiYWNrO1xuICAgICAgfVxuICAgICAgY29uc3QgcmVzZXRSZWN0cyA9IGF3YWl0IHBsYXRmb3JtMi5nZXRFbGVtZW50UmVjdHMoe1xuICAgICAgICByZWZlcmVuY2U6IHtcbiAgICAgICAgICBnZXRCb3VuZGluZ0NsaWVudFJlY3Q6IGdldEJvdW5kaW5nQ2xpZW50UmVjdDJcbiAgICAgICAgfSxcbiAgICAgICAgZmxvYXRpbmc6IGVsZW1lbnRzLmZsb2F0aW5nLFxuICAgICAgICBzdHJhdGVneVxuICAgICAgfSk7XG4gICAgICBpZiAocmVjdHMucmVmZXJlbmNlLnggIT09IHJlc2V0UmVjdHMucmVmZXJlbmNlLnggfHwgcmVjdHMucmVmZXJlbmNlLnkgIT09IHJlc2V0UmVjdHMucmVmZXJlbmNlLnkgfHwgcmVjdHMucmVmZXJlbmNlLndpZHRoICE9PSByZXNldFJlY3RzLnJlZmVyZW5jZS53aWR0aCB8fCByZWN0cy5yZWZlcmVuY2UuaGVpZ2h0ICE9PSByZXNldFJlY3RzLnJlZmVyZW5jZS5oZWlnaHQpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICByZXNldDoge1xuICAgICAgICAgICAgcmVjdHM6IHJlc2V0UmVjdHNcbiAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICByZXR1cm4ge307XG4gICAgfVxuICB9O1xufTtcblxuLy8gbm9kZV9tb2R1bGVzL0BmbG9hdGluZy11aS9kb20vZGlzdC9mbG9hdGluZy11aS5kb20uZXNtLmpzXG5mdW5jdGlvbiBpc1dpbmRvdyh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgJiYgdmFsdWUuZG9jdW1lbnQgJiYgdmFsdWUubG9jYXRpb24gJiYgdmFsdWUuYWxlcnQgJiYgdmFsdWUuc2V0SW50ZXJ2YWw7XG59XG5mdW5jdGlvbiBnZXRXaW5kb3cobm9kZSkge1xuICBpZiAobm9kZSA9PSBudWxsKSB7XG4gICAgcmV0dXJuIHdpbmRvdztcbiAgfVxuICBpZiAoIWlzV2luZG93KG5vZGUpKSB7XG4gICAgY29uc3Qgb3duZXJEb2N1bWVudCA9IG5vZGUub3duZXJEb2N1bWVudDtcbiAgICByZXR1cm4gb3duZXJEb2N1bWVudCA/IG93bmVyRG9jdW1lbnQuZGVmYXVsdFZpZXcgfHwgd2luZG93IDogd2luZG93O1xuICB9XG4gIHJldHVybiBub2RlO1xufVxuZnVuY3Rpb24gZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpIHtcbiAgcmV0dXJuIGdldFdpbmRvdyhlbGVtZW50KS5nZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpO1xufVxuZnVuY3Rpb24gZ2V0Tm9kZU5hbWUobm9kZSkge1xuICByZXR1cm4gaXNXaW5kb3cobm9kZSkgPyBcIlwiIDogbm9kZSA/IChub2RlLm5vZGVOYW1lIHx8IFwiXCIpLnRvTG93ZXJDYXNlKCkgOiBcIlwiO1xufVxuZnVuY3Rpb24gaXNIVE1MRWxlbWVudCh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgaW5zdGFuY2VvZiBnZXRXaW5kb3codmFsdWUpLkhUTUxFbGVtZW50O1xufVxuZnVuY3Rpb24gaXNFbGVtZW50KHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSBpbnN0YW5jZW9mIGdldFdpbmRvdyh2YWx1ZSkuRWxlbWVudDtcbn1cbmZ1bmN0aW9uIGlzTm9kZSh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgaW5zdGFuY2VvZiBnZXRXaW5kb3codmFsdWUpLk5vZGU7XG59XG5mdW5jdGlvbiBpc1NoYWRvd1Jvb3Qobm9kZSkge1xuICBpZiAodHlwZW9mIFNoYWRvd1Jvb3QgPT09IFwidW5kZWZpbmVkXCIpIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgY29uc3QgT3duRWxlbWVudCA9IGdldFdpbmRvdyhub2RlKS5TaGFkb3dSb290O1xuICByZXR1cm4gbm9kZSBpbnN0YW5jZW9mIE93bkVsZW1lbnQgfHwgbm9kZSBpbnN0YW5jZW9mIFNoYWRvd1Jvb3Q7XG59XG5mdW5jdGlvbiBpc092ZXJmbG93RWxlbWVudChlbGVtZW50KSB7XG4gIGNvbnN0IHtcbiAgICBvdmVyZmxvdyxcbiAgICBvdmVyZmxvd1gsXG4gICAgb3ZlcmZsb3dZXG4gIH0gPSBnZXRDb21wdXRlZFN0eWxlJDEoZWxlbWVudCk7XG4gIHJldHVybiAvYXV0b3xzY3JvbGx8b3ZlcmxheXxoaWRkZW4vLnRlc3Qob3ZlcmZsb3cgKyBvdmVyZmxvd1kgKyBvdmVyZmxvd1gpO1xufVxuZnVuY3Rpb24gaXNUYWJsZUVsZW1lbnQoZWxlbWVudCkge1xuICByZXR1cm4gW1widGFibGVcIiwgXCJ0ZFwiLCBcInRoXCJdLmluY2x1ZGVzKGdldE5vZGVOYW1lKGVsZW1lbnQpKTtcbn1cbmZ1bmN0aW9uIGlzQ29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHtcbiAgY29uc3QgaXNGaXJlZm94ID0gbmF2aWdhdG9yLnVzZXJBZ2VudC50b0xvd2VyQ2FzZSgpLmluY2x1ZGVzKFwiZmlyZWZveFwiKTtcbiAgY29uc3QgY3NzID0gZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpO1xuICByZXR1cm4gY3NzLnRyYW5zZm9ybSAhPT0gXCJub25lXCIgfHwgY3NzLnBlcnNwZWN0aXZlICE9PSBcIm5vbmVcIiB8fCBjc3MuY29udGFpbiA9PT0gXCJwYWludFwiIHx8IFtcInRyYW5zZm9ybVwiLCBcInBlcnNwZWN0aXZlXCJdLmluY2x1ZGVzKGNzcy53aWxsQ2hhbmdlKSB8fCBpc0ZpcmVmb3ggJiYgY3NzLndpbGxDaGFuZ2UgPT09IFwiZmlsdGVyXCIgfHwgaXNGaXJlZm94ICYmIChjc3MuZmlsdGVyID8gY3NzLmZpbHRlciAhPT0gXCJub25lXCIgOiBmYWxzZSk7XG59XG5mdW5jdGlvbiBpc0xheW91dFZpZXdwb3J0KCkge1xuICByZXR1cm4gIS9eKCg/IWNocm9tZXxhbmRyb2lkKS4pKnNhZmFyaS9pLnRlc3QobmF2aWdhdG9yLnVzZXJBZ2VudCk7XG59XG52YXIgbWluMiA9IE1hdGgubWluO1xudmFyIG1heDIgPSBNYXRoLm1heDtcbnZhciByb3VuZCA9IE1hdGgucm91bmQ7XG5mdW5jdGlvbiBnZXRCb3VuZGluZ0NsaWVudFJlY3QoZWxlbWVudCwgaW5jbHVkZVNjYWxlLCBpc0ZpeGVkU3RyYXRlZ3kpIHtcbiAgdmFyIF93aW4kdmlzdWFsVmlld3BvcnQkbywgX3dpbiR2aXN1YWxWaWV3cG9ydCwgX3dpbiR2aXN1YWxWaWV3cG9ydCRvMiwgX3dpbiR2aXN1YWxWaWV3cG9ydDI7XG4gIGlmIChpbmNsdWRlU2NhbGUgPT09IHZvaWQgMCkge1xuICAgIGluY2x1ZGVTY2FsZSA9IGZhbHNlO1xuICB9XG4gIGlmIChpc0ZpeGVkU3RyYXRlZ3kgPT09IHZvaWQgMCkge1xuICAgIGlzRml4ZWRTdHJhdGVneSA9IGZhbHNlO1xuICB9XG4gIGNvbnN0IGNsaWVudFJlY3QgPSBlbGVtZW50LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICBsZXQgc2NhbGVYID0gMTtcbiAgbGV0IHNjYWxlWSA9IDE7XG4gIGlmIChpbmNsdWRlU2NhbGUgJiYgaXNIVE1MRWxlbWVudChlbGVtZW50KSkge1xuICAgIHNjYWxlWCA9IGVsZW1lbnQub2Zmc2V0V2lkdGggPiAwID8gcm91bmQoY2xpZW50UmVjdC53aWR0aCkgLyBlbGVtZW50Lm9mZnNldFdpZHRoIHx8IDEgOiAxO1xuICAgIHNjYWxlWSA9IGVsZW1lbnQub2Zmc2V0SGVpZ2h0ID4gMCA/IHJvdW5kKGNsaWVudFJlY3QuaGVpZ2h0KSAvIGVsZW1lbnQub2Zmc2V0SGVpZ2h0IHx8IDEgOiAxO1xuICB9XG4gIGNvbnN0IHdpbiA9IGlzRWxlbWVudChlbGVtZW50KSA/IGdldFdpbmRvdyhlbGVtZW50KSA6IHdpbmRvdztcbiAgY29uc3QgYWRkVmlzdWFsT2Zmc2V0cyA9ICFpc0xheW91dFZpZXdwb3J0KCkgJiYgaXNGaXhlZFN0cmF0ZWd5O1xuICBjb25zdCB4ID0gKGNsaWVudFJlY3QubGVmdCArIChhZGRWaXN1YWxPZmZzZXRzID8gKF93aW4kdmlzdWFsVmlld3BvcnQkbyA9IChfd2luJHZpc3VhbFZpZXdwb3J0ID0gd2luLnZpc3VhbFZpZXdwb3J0KSA9PSBudWxsID8gdm9pZCAwIDogX3dpbiR2aXN1YWxWaWV3cG9ydC5vZmZzZXRMZWZ0KSAhPSBudWxsID8gX3dpbiR2aXN1YWxWaWV3cG9ydCRvIDogMCA6IDApKSAvIHNjYWxlWDtcbiAgY29uc3QgeSA9IChjbGllbnRSZWN0LnRvcCArIChhZGRWaXN1YWxPZmZzZXRzID8gKF93aW4kdmlzdWFsVmlld3BvcnQkbzIgPSAoX3dpbiR2aXN1YWxWaWV3cG9ydDIgPSB3aW4udmlzdWFsVmlld3BvcnQpID09IG51bGwgPyB2b2lkIDAgOiBfd2luJHZpc3VhbFZpZXdwb3J0Mi5vZmZzZXRUb3ApICE9IG51bGwgPyBfd2luJHZpc3VhbFZpZXdwb3J0JG8yIDogMCA6IDApKSAvIHNjYWxlWTtcbiAgY29uc3Qgd2lkdGggPSBjbGllbnRSZWN0LndpZHRoIC8gc2NhbGVYO1xuICBjb25zdCBoZWlnaHQgPSBjbGllbnRSZWN0LmhlaWdodCAvIHNjYWxlWTtcbiAgcmV0dXJuIHtcbiAgICB3aWR0aCxcbiAgICBoZWlnaHQsXG4gICAgdG9wOiB5LFxuICAgIHJpZ2h0OiB4ICsgd2lkdGgsXG4gICAgYm90dG9tOiB5ICsgaGVpZ2h0LFxuICAgIGxlZnQ6IHgsXG4gICAgeCxcbiAgICB5XG4gIH07XG59XG5mdW5jdGlvbiBnZXREb2N1bWVudEVsZW1lbnQobm9kZSkge1xuICByZXR1cm4gKChpc05vZGUobm9kZSkgPyBub2RlLm93bmVyRG9jdW1lbnQgOiBub2RlLmRvY3VtZW50KSB8fCB3aW5kb3cuZG9jdW1lbnQpLmRvY3VtZW50RWxlbWVudDtcbn1cbmZ1bmN0aW9uIGdldE5vZGVTY3JvbGwoZWxlbWVudCkge1xuICBpZiAoaXNFbGVtZW50KGVsZW1lbnQpKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIHNjcm9sbExlZnQ6IGVsZW1lbnQuc2Nyb2xsTGVmdCxcbiAgICAgIHNjcm9sbFRvcDogZWxlbWVudC5zY3JvbGxUb3BcbiAgICB9O1xuICB9XG4gIHJldHVybiB7XG4gICAgc2Nyb2xsTGVmdDogZWxlbWVudC5wYWdlWE9mZnNldCxcbiAgICBzY3JvbGxUb3A6IGVsZW1lbnQucGFnZVlPZmZzZXRcbiAgfTtcbn1cbmZ1bmN0aW9uIGdldFdpbmRvd1Njcm9sbEJhclgoZWxlbWVudCkge1xuICByZXR1cm4gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGdldERvY3VtZW50RWxlbWVudChlbGVtZW50KSkubGVmdCArIGdldE5vZGVTY3JvbGwoZWxlbWVudCkuc2Nyb2xsTGVmdDtcbn1cbmZ1bmN0aW9uIGlzU2NhbGVkKGVsZW1lbnQpIHtcbiAgY29uc3QgcmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50KTtcbiAgcmV0dXJuIHJvdW5kKHJlY3Qud2lkdGgpICE9PSBlbGVtZW50Lm9mZnNldFdpZHRoIHx8IHJvdW5kKHJlY3QuaGVpZ2h0KSAhPT0gZWxlbWVudC5vZmZzZXRIZWlnaHQ7XG59XG5mdW5jdGlvbiBnZXRSZWN0UmVsYXRpdmVUb09mZnNldFBhcmVudChlbGVtZW50LCBvZmZzZXRQYXJlbnQsIHN0cmF0ZWd5KSB7XG4gIGNvbnN0IGlzT2Zmc2V0UGFyZW50QW5FbGVtZW50ID0gaXNIVE1MRWxlbWVudChvZmZzZXRQYXJlbnQpO1xuICBjb25zdCBkb2N1bWVudEVsZW1lbnQgPSBnZXREb2N1bWVudEVsZW1lbnQob2Zmc2V0UGFyZW50KTtcbiAgY29uc3QgcmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChcbiAgICBlbGVtZW50LFxuICAgIGlzT2Zmc2V0UGFyZW50QW5FbGVtZW50ICYmIGlzU2NhbGVkKG9mZnNldFBhcmVudCksXG4gICAgc3RyYXRlZ3kgPT09IFwiZml4ZWRcIlxuICApO1xuICBsZXQgc2Nyb2xsID0ge1xuICAgIHNjcm9sbExlZnQ6IDAsXG4gICAgc2Nyb2xsVG9wOiAwXG4gIH07XG4gIGNvbnN0IG9mZnNldHMgPSB7XG4gICAgeDogMCxcbiAgICB5OiAwXG4gIH07XG4gIGlmIChpc09mZnNldFBhcmVudEFuRWxlbWVudCB8fCAhaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgJiYgc3RyYXRlZ3kgIT09IFwiZml4ZWRcIikge1xuICAgIGlmIChnZXROb2RlTmFtZShvZmZzZXRQYXJlbnQpICE9PSBcImJvZHlcIiB8fCBpc092ZXJmbG93RWxlbWVudChkb2N1bWVudEVsZW1lbnQpKSB7XG4gICAgICBzY3JvbGwgPSBnZXROb2RlU2Nyb2xsKG9mZnNldFBhcmVudCk7XG4gICAgfVxuICAgIGlmIChpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCkpIHtcbiAgICAgIGNvbnN0IG9mZnNldFJlY3QgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3Qob2Zmc2V0UGFyZW50LCB0cnVlKTtcbiAgICAgIG9mZnNldHMueCA9IG9mZnNldFJlY3QueCArIG9mZnNldFBhcmVudC5jbGllbnRMZWZ0O1xuICAgICAgb2Zmc2V0cy55ID0gb2Zmc2V0UmVjdC55ICsgb2Zmc2V0UGFyZW50LmNsaWVudFRvcDtcbiAgICB9IGVsc2UgaWYgKGRvY3VtZW50RWxlbWVudCkge1xuICAgICAgb2Zmc2V0cy54ID0gZ2V0V2luZG93U2Nyb2xsQmFyWChkb2N1bWVudEVsZW1lbnQpO1xuICAgIH1cbiAgfVxuICByZXR1cm4ge1xuICAgIHg6IHJlY3QubGVmdCArIHNjcm9sbC5zY3JvbGxMZWZ0IC0gb2Zmc2V0cy54LFxuICAgIHk6IHJlY3QudG9wICsgc2Nyb2xsLnNjcm9sbFRvcCAtIG9mZnNldHMueSxcbiAgICB3aWR0aDogcmVjdC53aWR0aCxcbiAgICBoZWlnaHQ6IHJlY3QuaGVpZ2h0XG4gIH07XG59XG5mdW5jdGlvbiBnZXRQYXJlbnROb2RlKG5vZGUpIHtcbiAgaWYgKGdldE5vZGVOYW1lKG5vZGUpID09PSBcImh0bWxcIikge1xuICAgIHJldHVybiBub2RlO1xuICB9XG4gIHJldHVybiBub2RlLmFzc2lnbmVkU2xvdCB8fCBub2RlLnBhcmVudE5vZGUgfHwgKGlzU2hhZG93Um9vdChub2RlKSA/IG5vZGUuaG9zdCA6IG51bGwpIHx8IGdldERvY3VtZW50RWxlbWVudChub2RlKTtcbn1cbmZ1bmN0aW9uIGdldFRydWVPZmZzZXRQYXJlbnQoZWxlbWVudCkge1xuICBpZiAoIWlzSFRNTEVsZW1lbnQoZWxlbWVudCkgfHwgZ2V0Q29tcHV0ZWRTdHlsZShlbGVtZW50KS5wb3NpdGlvbiA9PT0gXCJmaXhlZFwiKSB7XG4gICAgcmV0dXJuIG51bGw7XG4gIH1cbiAgcmV0dXJuIGVsZW1lbnQub2Zmc2V0UGFyZW50O1xufVxuZnVuY3Rpb24gZ2V0Q29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHtcbiAgbGV0IGN1cnJlbnROb2RlID0gZ2V0UGFyZW50Tm9kZShlbGVtZW50KTtcbiAgaWYgKGlzU2hhZG93Um9vdChjdXJyZW50Tm9kZSkpIHtcbiAgICBjdXJyZW50Tm9kZSA9IGN1cnJlbnROb2RlLmhvc3Q7XG4gIH1cbiAgd2hpbGUgKGlzSFRNTEVsZW1lbnQoY3VycmVudE5vZGUpICYmICFbXCJodG1sXCIsIFwiYm9keVwiXS5pbmNsdWRlcyhnZXROb2RlTmFtZShjdXJyZW50Tm9kZSkpKSB7XG4gICAgaWYgKGlzQ29udGFpbmluZ0Jsb2NrKGN1cnJlbnROb2RlKSkge1xuICAgICAgcmV0dXJuIGN1cnJlbnROb2RlO1xuICAgIH0gZWxzZSB7XG4gICAgICBjdXJyZW50Tm9kZSA9IGN1cnJlbnROb2RlLnBhcmVudE5vZGU7XG4gICAgfVxuICB9XG4gIHJldHVybiBudWxsO1xufVxuZnVuY3Rpb24gZ2V0T2Zmc2V0UGFyZW50KGVsZW1lbnQpIHtcbiAgY29uc3Qgd2luZG93MiA9IGdldFdpbmRvdyhlbGVtZW50KTtcbiAgbGV0IG9mZnNldFBhcmVudCA9IGdldFRydWVPZmZzZXRQYXJlbnQoZWxlbWVudCk7XG4gIHdoaWxlIChvZmZzZXRQYXJlbnQgJiYgaXNUYWJsZUVsZW1lbnQob2Zmc2V0UGFyZW50KSAmJiBnZXRDb21wdXRlZFN0eWxlKG9mZnNldFBhcmVudCkucG9zaXRpb24gPT09IFwic3RhdGljXCIpIHtcbiAgICBvZmZzZXRQYXJlbnQgPSBnZXRUcnVlT2Zmc2V0UGFyZW50KG9mZnNldFBhcmVudCk7XG4gIH1cbiAgaWYgKG9mZnNldFBhcmVudCAmJiAoZ2V0Tm9kZU5hbWUob2Zmc2V0UGFyZW50KSA9PT0gXCJodG1sXCIgfHwgZ2V0Tm9kZU5hbWUob2Zmc2V0UGFyZW50KSA9PT0gXCJib2R5XCIgJiYgZ2V0Q29tcHV0ZWRTdHlsZShvZmZzZXRQYXJlbnQpLnBvc2l0aW9uID09PSBcInN0YXRpY1wiICYmICFpc0NvbnRhaW5pbmdCbG9jayhvZmZzZXRQYXJlbnQpKSkge1xuICAgIHJldHVybiB3aW5kb3cyO1xuICB9XG4gIHJldHVybiBvZmZzZXRQYXJlbnQgfHwgZ2V0Q29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHx8IHdpbmRvdzI7XG59XG5mdW5jdGlvbiBnZXREaW1lbnNpb25zKGVsZW1lbnQpIHtcbiAgaWYgKGlzSFRNTEVsZW1lbnQoZWxlbWVudCkpIHtcbiAgICByZXR1cm4ge1xuICAgICAgd2lkdGg6IGVsZW1lbnQub2Zmc2V0V2lkdGgsXG4gICAgICBoZWlnaHQ6IGVsZW1lbnQub2Zmc2V0SGVpZ2h0XG4gICAgfTtcbiAgfVxuICBjb25zdCByZWN0ID0gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGVsZW1lbnQpO1xuICByZXR1cm4ge1xuICAgIHdpZHRoOiByZWN0LndpZHRoLFxuICAgIGhlaWdodDogcmVjdC5oZWlnaHRcbiAgfTtcbn1cbmZ1bmN0aW9uIGNvbnZlcnRPZmZzZXRQYXJlbnRSZWxhdGl2ZVJlY3RUb1ZpZXdwb3J0UmVsYXRpdmVSZWN0KF9yZWYpIHtcbiAgbGV0IHtcbiAgICByZWN0LFxuICAgIG9mZnNldFBhcmVudCxcbiAgICBzdHJhdGVneVxuICB9ID0gX3JlZjtcbiAgY29uc3QgaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgPSBpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCk7XG4gIGNvbnN0IGRvY3VtZW50RWxlbWVudCA9IGdldERvY3VtZW50RWxlbWVudChvZmZzZXRQYXJlbnQpO1xuICBpZiAob2Zmc2V0UGFyZW50ID09PSBkb2N1bWVudEVsZW1lbnQpIHtcbiAgICByZXR1cm4gcmVjdDtcbiAgfVxuICBsZXQgc2Nyb2xsID0ge1xuICAgIHNjcm9sbExlZnQ6IDAsXG4gICAgc2Nyb2xsVG9wOiAwXG4gIH07XG4gIGNvbnN0IG9mZnNldHMgPSB7XG4gICAgeDogMCxcbiAgICB5OiAwXG4gIH07XG4gIGlmIChpc09mZnNldFBhcmVudEFuRWxlbWVudCB8fCAhaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgJiYgc3RyYXRlZ3kgIT09IFwiZml4ZWRcIikge1xuICAgIGlmIChnZXROb2RlTmFtZShvZmZzZXRQYXJlbnQpICE9PSBcImJvZHlcIiB8fCBpc092ZXJmbG93RWxlbWVudChkb2N1bWVudEVsZW1lbnQpKSB7XG4gICAgICBzY3JvbGwgPSBnZXROb2RlU2Nyb2xsKG9mZnNldFBhcmVudCk7XG4gICAgfVxuICAgIGlmIChpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCkpIHtcbiAgICAgIGNvbnN0IG9mZnNldFJlY3QgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3Qob2Zmc2V0UGFyZW50LCB0cnVlKTtcbiAgICAgIG9mZnNldHMueCA9IG9mZnNldFJlY3QueCArIG9mZnNldFBhcmVudC5jbGllbnRMZWZ0O1xuICAgICAgb2Zmc2V0cy55ID0gb2Zmc2V0UmVjdC55ICsgb2Zmc2V0UGFyZW50LmNsaWVudFRvcDtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHtcbiAgICAuLi5yZWN0LFxuICAgIHg6IHJlY3QueCAtIHNjcm9sbC5zY3JvbGxMZWZ0ICsgb2Zmc2V0cy54LFxuICAgIHk6IHJlY3QueSAtIHNjcm9sbC5zY3JvbGxUb3AgKyBvZmZzZXRzLnlcbiAgfTtcbn1cbmZ1bmN0aW9uIGdldFZpZXdwb3J0UmVjdChlbGVtZW50LCBzdHJhdGVneSkge1xuICBjb25zdCB3aW4gPSBnZXRXaW5kb3coZWxlbWVudCk7XG4gIGNvbnN0IGh0bWwgPSBnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCk7XG4gIGNvbnN0IHZpc3VhbFZpZXdwb3J0ID0gd2luLnZpc3VhbFZpZXdwb3J0O1xuICBsZXQgd2lkdGggPSBodG1sLmNsaWVudFdpZHRoO1xuICBsZXQgaGVpZ2h0ID0gaHRtbC5jbGllbnRIZWlnaHQ7XG4gIGxldCB4ID0gMDtcbiAgbGV0IHkgPSAwO1xuICBpZiAodmlzdWFsVmlld3BvcnQpIHtcbiAgICB3aWR0aCA9IHZpc3VhbFZpZXdwb3J0LndpZHRoO1xuICAgIGhlaWdodCA9IHZpc3VhbFZpZXdwb3J0LmhlaWdodDtcbiAgICBjb25zdCBsYXlvdXRWaWV3cG9ydCA9IGlzTGF5b3V0Vmlld3BvcnQoKTtcbiAgICBpZiAobGF5b3V0Vmlld3BvcnQgfHwgIWxheW91dFZpZXdwb3J0ICYmIHN0cmF0ZWd5ID09PSBcImZpeGVkXCIpIHtcbiAgICAgIHggPSB2aXN1YWxWaWV3cG9ydC5vZmZzZXRMZWZ0O1xuICAgICAgeSA9IHZpc3VhbFZpZXdwb3J0Lm9mZnNldFRvcDtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHtcbiAgICB3aWR0aCxcbiAgICBoZWlnaHQsXG4gICAgeCxcbiAgICB5XG4gIH07XG59XG5mdW5jdGlvbiBnZXREb2N1bWVudFJlY3QoZWxlbWVudCkge1xuICB2YXIgX2VsZW1lbnQkb3duZXJEb2N1bWVuO1xuICBjb25zdCBodG1sID0gZ2V0RG9jdW1lbnRFbGVtZW50KGVsZW1lbnQpO1xuICBjb25zdCBzY3JvbGwgPSBnZXROb2RlU2Nyb2xsKGVsZW1lbnQpO1xuICBjb25zdCBib2R5ID0gKF9lbGVtZW50JG93bmVyRG9jdW1lbiA9IGVsZW1lbnQub3duZXJEb2N1bWVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9lbGVtZW50JG93bmVyRG9jdW1lbi5ib2R5O1xuICBjb25zdCB3aWR0aCA9IG1heDIoaHRtbC5zY3JvbGxXaWR0aCwgaHRtbC5jbGllbnRXaWR0aCwgYm9keSA/IGJvZHkuc2Nyb2xsV2lkdGggOiAwLCBib2R5ID8gYm9keS5jbGllbnRXaWR0aCA6IDApO1xuICBjb25zdCBoZWlnaHQgPSBtYXgyKGh0bWwuc2Nyb2xsSGVpZ2h0LCBodG1sLmNsaWVudEhlaWdodCwgYm9keSA/IGJvZHkuc2Nyb2xsSGVpZ2h0IDogMCwgYm9keSA/IGJvZHkuY2xpZW50SGVpZ2h0IDogMCk7XG4gIGxldCB4ID0gLXNjcm9sbC5zY3JvbGxMZWZ0ICsgZ2V0V2luZG93U2Nyb2xsQmFyWChlbGVtZW50KTtcbiAgY29uc3QgeSA9IC1zY3JvbGwuc2Nyb2xsVG9wO1xuICBpZiAoZ2V0Q29tcHV0ZWRTdHlsZSQxKGJvZHkgfHwgaHRtbCkuZGlyZWN0aW9uID09PSBcInJ0bFwiKSB7XG4gICAgeCArPSBtYXgyKGh0bWwuY2xpZW50V2lkdGgsIGJvZHkgPyBib2R5LmNsaWVudFdpZHRoIDogMCkgLSB3aWR0aDtcbiAgfVxuICByZXR1cm4ge1xuICAgIHdpZHRoLFxuICAgIGhlaWdodCxcbiAgICB4LFxuICAgIHlcbiAgfTtcbn1cbmZ1bmN0aW9uIGdldE5lYXJlc3RPdmVyZmxvd0FuY2VzdG9yKG5vZGUpIHtcbiAgY29uc3QgcGFyZW50Tm9kZSA9IGdldFBhcmVudE5vZGUobm9kZSk7XG4gIGlmIChbXCJodG1sXCIsIFwiYm9keVwiLCBcIiNkb2N1bWVudFwiXS5pbmNsdWRlcyhnZXROb2RlTmFtZShwYXJlbnROb2RlKSkpIHtcbiAgICByZXR1cm4gbm9kZS5vd25lckRvY3VtZW50LmJvZHk7XG4gIH1cbiAgaWYgKGlzSFRNTEVsZW1lbnQocGFyZW50Tm9kZSkgJiYgaXNPdmVyZmxvd0VsZW1lbnQocGFyZW50Tm9kZSkpIHtcbiAgICByZXR1cm4gcGFyZW50Tm9kZTtcbiAgfVxuICByZXR1cm4gZ2V0TmVhcmVzdE92ZXJmbG93QW5jZXN0b3IocGFyZW50Tm9kZSk7XG59XG5mdW5jdGlvbiBnZXRPdmVyZmxvd0FuY2VzdG9ycyhub2RlLCBsaXN0KSB7XG4gIHZhciBfbm9kZSRvd25lckRvY3VtZW50O1xuICBpZiAobGlzdCA9PT0gdm9pZCAwKSB7XG4gICAgbGlzdCA9IFtdO1xuICB9XG4gIGNvbnN0IHNjcm9sbGFibGVBbmNlc3RvciA9IGdldE5lYXJlc3RPdmVyZmxvd0FuY2VzdG9yKG5vZGUpO1xuICBjb25zdCBpc0JvZHkgPSBzY3JvbGxhYmxlQW5jZXN0b3IgPT09ICgoX25vZGUkb3duZXJEb2N1bWVudCA9IG5vZGUub3duZXJEb2N1bWVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9ub2RlJG93bmVyRG9jdW1lbnQuYm9keSk7XG4gIGNvbnN0IHdpbiA9IGdldFdpbmRvdyhzY3JvbGxhYmxlQW5jZXN0b3IpO1xuICBjb25zdCB0YXJnZXQgPSBpc0JvZHkgPyBbd2luXS5jb25jYXQod2luLnZpc3VhbFZpZXdwb3J0IHx8IFtdLCBpc092ZXJmbG93RWxlbWVudChzY3JvbGxhYmxlQW5jZXN0b3IpID8gc2Nyb2xsYWJsZUFuY2VzdG9yIDogW10pIDogc2Nyb2xsYWJsZUFuY2VzdG9yO1xuICBjb25zdCB1cGRhdGVkTGlzdCA9IGxpc3QuY29uY2F0KHRhcmdldCk7XG4gIHJldHVybiBpc0JvZHkgPyB1cGRhdGVkTGlzdCA6IHVwZGF0ZWRMaXN0LmNvbmNhdChnZXRPdmVyZmxvd0FuY2VzdG9ycyh0YXJnZXQpKTtcbn1cbmZ1bmN0aW9uIGNvbnRhaW5zKHBhcmVudCwgY2hpbGQpIHtcbiAgY29uc3Qgcm9vdE5vZGUgPSBjaGlsZCA9PSBudWxsID8gdm9pZCAwIDogY2hpbGQuZ2V0Um9vdE5vZGUgPT0gbnVsbCA/IHZvaWQgMCA6IGNoaWxkLmdldFJvb3ROb2RlKCk7XG4gIGlmIChwYXJlbnQgIT0gbnVsbCAmJiBwYXJlbnQuY29udGFpbnMoY2hpbGQpKSB7XG4gICAgcmV0dXJuIHRydWU7XG4gIH0gZWxzZSBpZiAocm9vdE5vZGUgJiYgaXNTaGFkb3dSb290KHJvb3ROb2RlKSkge1xuICAgIGxldCBuZXh0ID0gY2hpbGQ7XG4gICAgZG8ge1xuICAgICAgaWYgKG5leHQgJiYgcGFyZW50ID09PSBuZXh0KSB7XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfVxuICAgICAgbmV4dCA9IG5leHQucGFyZW50Tm9kZSB8fCBuZXh0Lmhvc3Q7XG4gICAgfSB3aGlsZSAobmV4dCk7XG4gIH1cbiAgcmV0dXJuIGZhbHNlO1xufVxuZnVuY3Rpb24gZ2V0SW5uZXJCb3VuZGluZ0NsaWVudFJlY3QoZWxlbWVudCwgc3RyYXRlZ3kpIHtcbiAgY29uc3QgY2xpZW50UmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50LCBmYWxzZSwgc3RyYXRlZ3kgPT09IFwiZml4ZWRcIik7XG4gIGNvbnN0IHRvcCA9IGNsaWVudFJlY3QudG9wICsgZWxlbWVudC5jbGllbnRUb3A7XG4gIGNvbnN0IGxlZnQgPSBjbGllbnRSZWN0LmxlZnQgKyBlbGVtZW50LmNsaWVudExlZnQ7XG4gIHJldHVybiB7XG4gICAgdG9wLFxuICAgIGxlZnQsXG4gICAgeDogbGVmdCxcbiAgICB5OiB0b3AsXG4gICAgcmlnaHQ6IGxlZnQgKyBlbGVtZW50LmNsaWVudFdpZHRoLFxuICAgIGJvdHRvbTogdG9wICsgZWxlbWVudC5jbGllbnRIZWlnaHQsXG4gICAgd2lkdGg6IGVsZW1lbnQuY2xpZW50V2lkdGgsXG4gICAgaGVpZ2h0OiBlbGVtZW50LmNsaWVudEhlaWdodFxuICB9O1xufVxuZnVuY3Rpb24gZ2V0Q2xpZW50UmVjdEZyb21DbGlwcGluZ0FuY2VzdG9yKGVsZW1lbnQsIGNsaXBwaW5nUGFyZW50LCBzdHJhdGVneSkge1xuICBpZiAoY2xpcHBpbmdQYXJlbnQgPT09IFwidmlld3BvcnRcIikge1xuICAgIHJldHVybiByZWN0VG9DbGllbnRSZWN0KGdldFZpZXdwb3J0UmVjdChlbGVtZW50LCBzdHJhdGVneSkpO1xuICB9XG4gIGlmIChpc0VsZW1lbnQoY2xpcHBpbmdQYXJlbnQpKSB7XG4gICAgcmV0dXJuIGdldElubmVyQm91bmRpbmdDbGllbnRSZWN0KGNsaXBwaW5nUGFyZW50LCBzdHJhdGVneSk7XG4gIH1cbiAgcmV0dXJuIHJlY3RUb0NsaWVudFJlY3QoZ2V0RG9jdW1lbnRSZWN0KGdldERvY3VtZW50RWxlbWVudChlbGVtZW50KSkpO1xufVxuZnVuY3Rpb24gZ2V0Q2xpcHBpbmdBbmNlc3RvcnMoZWxlbWVudCkge1xuICBjb25zdCBjbGlwcGluZ0FuY2VzdG9ycyA9IGdldE92ZXJmbG93QW5jZXN0b3JzKGVsZW1lbnQpO1xuICBjb25zdCBjYW5Fc2NhcGVDbGlwcGluZyA9IFtcImFic29sdXRlXCIsIFwiZml4ZWRcIl0uaW5jbHVkZXMoZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpLnBvc2l0aW9uKTtcbiAgY29uc3QgY2xpcHBlckVsZW1lbnQgPSBjYW5Fc2NhcGVDbGlwcGluZyAmJiBpc0hUTUxFbGVtZW50KGVsZW1lbnQpID8gZ2V0T2Zmc2V0UGFyZW50KGVsZW1lbnQpIDogZWxlbWVudDtcbiAgaWYgKCFpc0VsZW1lbnQoY2xpcHBlckVsZW1lbnQpKSB7XG4gICAgcmV0dXJuIFtdO1xuICB9XG4gIHJldHVybiBjbGlwcGluZ0FuY2VzdG9ycy5maWx0ZXIoKGNsaXBwaW5nQW5jZXN0b3JzMikgPT4gaXNFbGVtZW50KGNsaXBwaW5nQW5jZXN0b3JzMikgJiYgY29udGFpbnMoY2xpcHBpbmdBbmNlc3RvcnMyLCBjbGlwcGVyRWxlbWVudCkgJiYgZ2V0Tm9kZU5hbWUoY2xpcHBpbmdBbmNlc3RvcnMyKSAhPT0gXCJib2R5XCIpO1xufVxuZnVuY3Rpb24gZ2V0Q2xpcHBpbmdSZWN0KF9yZWYpIHtcbiAgbGV0IHtcbiAgICBlbGVtZW50LFxuICAgIGJvdW5kYXJ5LFxuICAgIHJvb3RCb3VuZGFyeSxcbiAgICBzdHJhdGVneVxuICB9ID0gX3JlZjtcbiAgY29uc3QgbWFpbkNsaXBwaW5nQW5jZXN0b3JzID0gYm91bmRhcnkgPT09IFwiY2xpcHBpbmdBbmNlc3RvcnNcIiA/IGdldENsaXBwaW5nQW5jZXN0b3JzKGVsZW1lbnQpIDogW10uY29uY2F0KGJvdW5kYXJ5KTtcbiAgY29uc3QgY2xpcHBpbmdBbmNlc3RvcnMgPSBbLi4ubWFpbkNsaXBwaW5nQW5jZXN0b3JzLCByb290Qm91bmRhcnldO1xuICBjb25zdCBmaXJzdENsaXBwaW5nQW5jZXN0b3IgPSBjbGlwcGluZ0FuY2VzdG9yc1swXTtcbiAgY29uc3QgY2xpcHBpbmdSZWN0ID0gY2xpcHBpbmdBbmNlc3RvcnMucmVkdWNlKChhY2NSZWN0LCBjbGlwcGluZ0FuY2VzdG9yKSA9PiB7XG4gICAgY29uc3QgcmVjdCA9IGdldENsaWVudFJlY3RGcm9tQ2xpcHBpbmdBbmNlc3RvcihlbGVtZW50LCBjbGlwcGluZ0FuY2VzdG9yLCBzdHJhdGVneSk7XG4gICAgYWNjUmVjdC50b3AgPSBtYXgyKHJlY3QudG9wLCBhY2NSZWN0LnRvcCk7XG4gICAgYWNjUmVjdC5yaWdodCA9IG1pbjIocmVjdC5yaWdodCwgYWNjUmVjdC5yaWdodCk7XG4gICAgYWNjUmVjdC5ib3R0b20gPSBtaW4yKHJlY3QuYm90dG9tLCBhY2NSZWN0LmJvdHRvbSk7XG4gICAgYWNjUmVjdC5sZWZ0ID0gbWF4MihyZWN0LmxlZnQsIGFjY1JlY3QubGVmdCk7XG4gICAgcmV0dXJuIGFjY1JlY3Q7XG4gIH0sIGdldENsaWVudFJlY3RGcm9tQ2xpcHBpbmdBbmNlc3RvcihlbGVtZW50LCBmaXJzdENsaXBwaW5nQW5jZXN0b3IsIHN0cmF0ZWd5KSk7XG4gIHJldHVybiB7XG4gICAgd2lkdGg6IGNsaXBwaW5nUmVjdC5yaWdodCAtIGNsaXBwaW5nUmVjdC5sZWZ0LFxuICAgIGhlaWdodDogY2xpcHBpbmdSZWN0LmJvdHRvbSAtIGNsaXBwaW5nUmVjdC50b3AsXG4gICAgeDogY2xpcHBpbmdSZWN0LmxlZnQsXG4gICAgeTogY2xpcHBpbmdSZWN0LnRvcFxuICB9O1xufVxudmFyIHBsYXRmb3JtID0ge1xuICBnZXRDbGlwcGluZ1JlY3QsXG4gIGNvbnZlcnRPZmZzZXRQYXJlbnRSZWxhdGl2ZVJlY3RUb1ZpZXdwb3J0UmVsYXRpdmVSZWN0LFxuICBpc0VsZW1lbnQsXG4gIGdldERpbWVuc2lvbnMsXG4gIGdldE9mZnNldFBhcmVudCxcbiAgZ2V0RG9jdW1lbnRFbGVtZW50LFxuICBnZXRFbGVtZW50UmVjdHM6IChfcmVmKSA9PiB7XG4gICAgbGV0IHtcbiAgICAgIHJlZmVyZW5jZSxcbiAgICAgIGZsb2F0aW5nLFxuICAgICAgc3RyYXRlZ3lcbiAgICB9ID0gX3JlZjtcbiAgICByZXR1cm4ge1xuICAgICAgcmVmZXJlbmNlOiBnZXRSZWN0UmVsYXRpdmVUb09mZnNldFBhcmVudChyZWZlcmVuY2UsIGdldE9mZnNldFBhcmVudChmbG9hdGluZyksIHN0cmF0ZWd5KSxcbiAgICAgIGZsb2F0aW5nOiB7XG4gICAgICAgIC4uLmdldERpbWVuc2lvbnMoZmxvYXRpbmcpLFxuICAgICAgICB4OiAwLFxuICAgICAgICB5OiAwXG4gICAgICB9XG4gICAgfTtcbiAgfSxcbiAgZ2V0Q2xpZW50UmVjdHM6IChlbGVtZW50KSA9PiBBcnJheS5mcm9tKGVsZW1lbnQuZ2V0Q2xpZW50UmVjdHMoKSksXG4gIGlzUlRMOiAoZWxlbWVudCkgPT4gZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpLmRpcmVjdGlvbiA9PT0gXCJydGxcIlxufTtcbmZ1bmN0aW9uIGF1dG9VcGRhdGUocmVmZXJlbmNlLCBmbG9hdGluZywgdXBkYXRlLCBvcHRpb25zKSB7XG4gIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICBvcHRpb25zID0ge307XG4gIH1cbiAgY29uc3Qge1xuICAgIGFuY2VzdG9yU2Nyb2xsOiBfYW5jZXN0b3JTY3JvbGwgPSB0cnVlLFxuICAgIGFuY2VzdG9yUmVzaXplOiBfYW5jZXN0b3JSZXNpemUgPSB0cnVlLFxuICAgIGVsZW1lbnRSZXNpemU6IF9lbGVtZW50UmVzaXplID0gdHJ1ZSxcbiAgICBhbmltYXRpb25GcmFtZSA9IGZhbHNlXG4gIH0gPSBvcHRpb25zO1xuICBsZXQgY2xlYW5lZFVwID0gZmFsc2U7XG4gIGNvbnN0IGFuY2VzdG9yU2Nyb2xsID0gX2FuY2VzdG9yU2Nyb2xsICYmICFhbmltYXRpb25GcmFtZTtcbiAgY29uc3QgYW5jZXN0b3JSZXNpemUgPSBfYW5jZXN0b3JSZXNpemUgJiYgIWFuaW1hdGlvbkZyYW1lO1xuICBjb25zdCBlbGVtZW50UmVzaXplID0gX2VsZW1lbnRSZXNpemUgJiYgIWFuaW1hdGlvbkZyYW1lO1xuICBjb25zdCBhbmNlc3RvcnMgPSBhbmNlc3RvclNjcm9sbCB8fCBhbmNlc3RvclJlc2l6ZSA/IFsuLi5pc0VsZW1lbnQocmVmZXJlbmNlKSA/IGdldE92ZXJmbG93QW5jZXN0b3JzKHJlZmVyZW5jZSkgOiBbXSwgLi4uZ2V0T3ZlcmZsb3dBbmNlc3RvcnMoZmxvYXRpbmcpXSA6IFtdO1xuICBhbmNlc3RvcnMuZm9yRWFjaCgoYW5jZXN0b3IpID0+IHtcbiAgICBhbmNlc3RvclNjcm9sbCAmJiBhbmNlc3Rvci5hZGRFdmVudExpc3RlbmVyKFwic2Nyb2xsXCIsIHVwZGF0ZSwge1xuICAgICAgcGFzc2l2ZTogdHJ1ZVxuICAgIH0pO1xuICAgIGFuY2VzdG9yUmVzaXplICYmIGFuY2VzdG9yLmFkZEV2ZW50TGlzdGVuZXIoXCJyZXNpemVcIiwgdXBkYXRlKTtcbiAgfSk7XG4gIGxldCBvYnNlcnZlcjIgPSBudWxsO1xuICBpZiAoZWxlbWVudFJlc2l6ZSkge1xuICAgIG9ic2VydmVyMiA9IG5ldyBSZXNpemVPYnNlcnZlcih1cGRhdGUpO1xuICAgIGlzRWxlbWVudChyZWZlcmVuY2UpICYmIG9ic2VydmVyMi5vYnNlcnZlKHJlZmVyZW5jZSk7XG4gICAgb2JzZXJ2ZXIyLm9ic2VydmUoZmxvYXRpbmcpO1xuICB9XG4gIGxldCBmcmFtZUlkO1xuICBsZXQgcHJldlJlZlJlY3QgPSBhbmltYXRpb25GcmFtZSA/IGdldEJvdW5kaW5nQ2xpZW50UmVjdChyZWZlcmVuY2UpIDogbnVsbDtcbiAgaWYgKGFuaW1hdGlvbkZyYW1lKSB7XG4gICAgZnJhbWVMb29wKCk7XG4gIH1cbiAgZnVuY3Rpb24gZnJhbWVMb29wKCkge1xuICAgIGlmIChjbGVhbmVkVXApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbmV4dFJlZlJlY3QgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3QocmVmZXJlbmNlKTtcbiAgICBpZiAocHJldlJlZlJlY3QgJiYgKG5leHRSZWZSZWN0LnggIT09IHByZXZSZWZSZWN0LnggfHwgbmV4dFJlZlJlY3QueSAhPT0gcHJldlJlZlJlY3QueSB8fCBuZXh0UmVmUmVjdC53aWR0aCAhPT0gcHJldlJlZlJlY3Qud2lkdGggfHwgbmV4dFJlZlJlY3QuaGVpZ2h0ICE9PSBwcmV2UmVmUmVjdC5oZWlnaHQpKSB7XG4gICAgICB1cGRhdGUoKTtcbiAgICB9XG4gICAgcHJldlJlZlJlY3QgPSBuZXh0UmVmUmVjdDtcbiAgICBmcmFtZUlkID0gcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZyYW1lTG9vcCk7XG4gIH1cbiAgcmV0dXJuICgpID0+IHtcbiAgICB2YXIgX29ic2VydmVyO1xuICAgIGNsZWFuZWRVcCA9IHRydWU7XG4gICAgYW5jZXN0b3JzLmZvckVhY2goKGFuY2VzdG9yKSA9PiB7XG4gICAgICBhbmNlc3RvclNjcm9sbCAmJiBhbmNlc3Rvci5yZW1vdmVFdmVudExpc3RlbmVyKFwic2Nyb2xsXCIsIHVwZGF0ZSk7XG4gICAgICBhbmNlc3RvclJlc2l6ZSAmJiBhbmNlc3Rvci5yZW1vdmVFdmVudExpc3RlbmVyKFwicmVzaXplXCIsIHVwZGF0ZSk7XG4gICAgfSk7XG4gICAgKF9vYnNlcnZlciA9IG9ic2VydmVyMikgPT0gbnVsbCA/IHZvaWQgMCA6IF9vYnNlcnZlci5kaXNjb25uZWN0KCk7XG4gICAgb2JzZXJ2ZXIyID0gbnVsbDtcbiAgICBpZiAoYW5pbWF0aW9uRnJhbWUpIHtcbiAgICAgIGNhbmNlbEFuaW1hdGlvbkZyYW1lKGZyYW1lSWQpO1xuICAgIH1cbiAgfTtcbn1cbnZhciBjb21wdXRlUG9zaXRpb24yID0gKHJlZmVyZW5jZSwgZmxvYXRpbmcsIG9wdGlvbnMpID0+IGNvbXB1dGVQb3NpdGlvbihyZWZlcmVuY2UsIGZsb2F0aW5nLCB7XG4gIHBsYXRmb3JtLFxuICAuLi5vcHRpb25zXG59KTtcblxuLy8gc3JjL2J1aWxkQ29uZmlnRnJvbU1vZGlmaWVycy5qc1xudmFyIGJ1aWxkQ29uZmlnRnJvbU1vZGlmaWVycyA9IChtb2RpZmllcnMpID0+IHtcbiAgY29uc3QgY29uZmlnID0ge1xuICAgIHBsYWNlbWVudDogXCJib3R0b21cIixcbiAgICBtaWRkbGV3YXJlOiBbXVxuICB9O1xuICBjb25zdCBrZXlzID0gT2JqZWN0LmtleXMobW9kaWZpZXJzKTtcbiAgY29uc3QgZ2V0TW9kaWZpZXJBcmd1bWVudCA9IChtb2RpZmllcikgPT4ge1xuICAgIHJldHVybiBtb2RpZmllcnNbbW9kaWZpZXJdO1xuICB9O1xuICBpZiAoa2V5cy5pbmNsdWRlcyhcIm9mZnNldFwiKSkge1xuICAgIGNvbmZpZy5taWRkbGV3YXJlLnB1c2gob2Zmc2V0KGdldE1vZGlmaWVyQXJndW1lbnQoXCJvZmZzZXRcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcInBsYWNlbWVudFwiKSkge1xuICAgIGNvbmZpZy5wbGFjZW1lbnQgPSBnZXRNb2RpZmllckFyZ3VtZW50KFwicGxhY2VtZW50XCIpO1xuICB9XG4gIGlmIChrZXlzLmluY2x1ZGVzKFwiYXV0b1BsYWNlbWVudFwiKSAmJiAha2V5cy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKGF1dG9QbGFjZW1lbnQoZ2V0TW9kaWZpZXJBcmd1bWVudChcImF1dG9QbGFjZW1lbnRcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKGZsaXAoZ2V0TW9kaWZpZXJBcmd1bWVudChcImZsaXBcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcInNoaWZ0XCIpKSB7XG4gICAgY29uZmlnLm1pZGRsZXdhcmUucHVzaChzaGlmdChnZXRNb2RpZmllckFyZ3VtZW50KFwic2hpZnRcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImlubGluZVwiKSkge1xuICAgIGNvbmZpZy5taWRkbGV3YXJlLnB1c2goaW5saW5lKGdldE1vZGlmaWVyQXJndW1lbnQoXCJpbmxpbmVcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImFycm93XCIpKSB7XG4gICAgY29uZmlnLm1pZGRsZXdhcmUucHVzaChhcnJvdyhnZXRNb2RpZmllckFyZ3VtZW50KFwiYXJyb3dcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImhpZGVcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKGhpZGUoZ2V0TW9kaWZpZXJBcmd1bWVudChcImhpZGVcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcInNpemVcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKHNpemUoZ2V0TW9kaWZpZXJBcmd1bWVudChcInNpemVcIikpKTtcbiAgfVxuICByZXR1cm4gY29uZmlnO1xufTtcblxuLy8gc3JjL2J1aWxkRGlyZWN0aXZlQ29uZmlnRnJvbU1vZGlmaWVycy5qc1xudmFyIGJ1aWxkRGlyZWN0aXZlQ29uZmlnRnJvbU1vZGlmaWVycyA9IChtb2RpZmllcnMsIHNldHRpbmdzKSA9PiB7XG4gIGNvbnN0IGNvbmZpZyA9IHtcbiAgICBjb21wb25lbnQ6IHtcbiAgICAgIHRyYXA6IGZhbHNlXG4gICAgfSxcbiAgICBmbG9hdDoge1xuICAgICAgcGxhY2VtZW50OiBcImJvdHRvbVwiLFxuICAgICAgc3RyYXRlZ3k6IFwiYWJzb2x1dGVcIixcbiAgICAgIG1pZGRsZXdhcmU6IFtdXG4gICAgfVxuICB9O1xuICBjb25zdCBnZXRNb2RpZmllckFyZ3VtZW50ID0gKG1vZGlmaWVyKSA9PiB7XG4gICAgcmV0dXJuIG1vZGlmaWVyc1ttb2RpZmllcnMuaW5kZXhPZihtb2RpZmllcikgKyAxXTtcbiAgfTtcbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInRyYXBcIikpIHtcbiAgICBjb25maWcuY29tcG9uZW50LnRyYXAgPSB0cnVlO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJ0ZWxlcG9ydFwiKSkge1xuICAgIGNvbmZpZy5mbG9hdC5zdHJhdGVneSA9IFwiZml4ZWRcIjtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwib2Zmc2V0XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUucHVzaChvZmZzZXQoc2V0dGluZ3NbXCJvZmZzZXRcIl0gfHwgMTApKTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwicGxhY2VtZW50XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0LnBsYWNlbWVudCA9IGdldE1vZGlmaWVyQXJndW1lbnQoXCJwbGFjZW1lbnRcIik7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImF1dG9QbGFjZW1lbnRcIikgJiYgIW1vZGlmaWVycy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKGF1dG9QbGFjZW1lbnQoc2V0dGluZ3NbXCJhdXRvUGxhY2VtZW50XCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKGZsaXAoc2V0dGluZ3NbXCJmbGlwXCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInNoaWZ0XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUucHVzaChzaGlmdChzZXR0aW5nc1tcInNoaWZ0XCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImlubGluZVwiKSkge1xuICAgIGNvbmZpZy5mbG9hdC5taWRkbGV3YXJlLnB1c2goaW5saW5lKHNldHRpbmdzW1wiaW5saW5lXCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImFycm93XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUucHVzaChhcnJvdyhzZXR0aW5nc1tcImFycm93XCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImhpZGVcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKGhpZGUoc2V0dGluZ3NbXCJoaWRlXCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInNpemVcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKHNpemUoc2V0dGluZ3NbXCJzaXplXCJdKSk7XG4gIH1cbiAgcmV0dXJuIGNvbmZpZztcbn07XG5cbi8vIHNyYy9yYW5kb21TdHJpbmcuanNcbnZhciByYW5kb21TdHJpbmcgPSAobGVuZ3RoKSA9PiB7XG4gIHZhciBjaGFycyA9IFwiMDEyMzQ1Njc4OUFCQ0RFRkdISUpLTE1OT1BRUlNUVVZXWFRaYWJjZGVmZ2hpa2xtbm9wcXJzdHV2d3h5elwiLnNwbGl0KFwiXCIpO1xuICB2YXIgc3RyID0gXCJcIjtcbiAgaWYgKCFsZW5ndGgpIHtcbiAgICBsZW5ndGggPSBNYXRoLmZsb29yKE1hdGgucmFuZG9tKCkgKiBjaGFycy5sZW5ndGgpO1xuICB9XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuZ3RoOyBpKyspIHtcbiAgICBzdHIgKz0gY2hhcnNbTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogY2hhcnMubGVuZ3RoKV07XG4gIH1cbiAgcmV0dXJuIHN0cjtcbn07XG5cbi8vIG5vZGVfbW9kdWxlcy9hbHBpbmVqcy9zcmMvbXV0YXRpb24uanNcbnZhciBvbkF0dHJpYnV0ZUFkZGVkcyA9IFtdO1xudmFyIG9uRWxSZW1vdmVkcyA9IFtdO1xudmFyIG9uRWxBZGRlZHMgPSBbXTtcbmZ1bmN0aW9uIGNsZWFudXBBdHRyaWJ1dGVzKGVsLCBuYW1lcykge1xuICBpZiAoIWVsLl94X2F0dHJpYnV0ZUNsZWFudXBzKVxuICAgIHJldHVybjtcbiAgT2JqZWN0LmVudHJpZXMoZWwuX3hfYXR0cmlidXRlQ2xlYW51cHMpLmZvckVhY2goKFtuYW1lLCB2YWx1ZV0pID0+IHtcbiAgICBpZiAobmFtZXMgPT09IHZvaWQgMCB8fCBuYW1lcy5pbmNsdWRlcyhuYW1lKSkge1xuICAgICAgdmFsdWUuZm9yRWFjaCgoaSkgPT4gaSgpKTtcbiAgICAgIGRlbGV0ZSBlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXTtcbiAgICB9XG4gIH0pO1xufVxudmFyIG9ic2VydmVyID0gbmV3IE11dGF0aW9uT2JzZXJ2ZXIob25NdXRhdGUpO1xudmFyIGN1cnJlbnRseU9ic2VydmluZyA9IGZhbHNlO1xuZnVuY3Rpb24gc3RhcnRPYnNlcnZpbmdNdXRhdGlvbnMoKSB7XG4gIG9ic2VydmVyLm9ic2VydmUoZG9jdW1lbnQsIHsgc3VidHJlZTogdHJ1ZSwgY2hpbGRMaXN0OiB0cnVlLCBhdHRyaWJ1dGVzOiB0cnVlLCBhdHRyaWJ1dGVPbGRWYWx1ZTogdHJ1ZSB9KTtcbiAgY3VycmVudGx5T2JzZXJ2aW5nID0gdHJ1ZTtcbn1cbmZ1bmN0aW9uIHN0b3BPYnNlcnZpbmdNdXRhdGlvbnMoKSB7XG4gIGZsdXNoT2JzZXJ2ZXIoKTtcbiAgb2JzZXJ2ZXIuZGlzY29ubmVjdCgpO1xuICBjdXJyZW50bHlPYnNlcnZpbmcgPSBmYWxzZTtcbn1cbnZhciByZWNvcmRRdWV1ZSA9IFtdO1xudmFyIHdpbGxQcm9jZXNzUmVjb3JkUXVldWUgPSBmYWxzZTtcbmZ1bmN0aW9uIGZsdXNoT2JzZXJ2ZXIoKSB7XG4gIHJlY29yZFF1ZXVlID0gcmVjb3JkUXVldWUuY29uY2F0KG9ic2VydmVyLnRha2VSZWNvcmRzKCkpO1xuICBpZiAocmVjb3JkUXVldWUubGVuZ3RoICYmICF3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlKSB7XG4gICAgd2lsbFByb2Nlc3NSZWNvcmRRdWV1ZSA9IHRydWU7XG4gICAgcXVldWVNaWNyb3Rhc2soKCkgPT4ge1xuICAgICAgcHJvY2Vzc1JlY29yZFF1ZXVlKCk7XG4gICAgICB3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlID0gZmFsc2U7XG4gICAgfSk7XG4gIH1cbn1cbmZ1bmN0aW9uIHByb2Nlc3NSZWNvcmRRdWV1ZSgpIHtcbiAgb25NdXRhdGUocmVjb3JkUXVldWUpO1xuICByZWNvcmRRdWV1ZS5sZW5ndGggPSAwO1xufVxuZnVuY3Rpb24gbXV0YXRlRG9tKGNhbGxiYWNrKSB7XG4gIGlmICghY3VycmVudGx5T2JzZXJ2aW5nKVxuICAgIHJldHVybiBjYWxsYmFjaygpO1xuICBzdG9wT2JzZXJ2aW5nTXV0YXRpb25zKCk7XG4gIGxldCByZXN1bHQgPSBjYWxsYmFjaygpO1xuICBzdGFydE9ic2VydmluZ011dGF0aW9ucygpO1xuICByZXR1cm4gcmVzdWx0O1xufVxudmFyIGlzQ29sbGVjdGluZyA9IGZhbHNlO1xudmFyIGRlZmVycmVkTXV0YXRpb25zID0gW107XG5mdW5jdGlvbiBvbk11dGF0ZShtdXRhdGlvbnMpIHtcbiAgaWYgKGlzQ29sbGVjdGluZykge1xuICAgIGRlZmVycmVkTXV0YXRpb25zID0gZGVmZXJyZWRNdXRhdGlvbnMuY29uY2F0KG11dGF0aW9ucyk7XG4gICAgcmV0dXJuO1xuICB9XG4gIGxldCBhZGRlZE5vZGVzID0gW107XG4gIGxldCByZW1vdmVkTm9kZXMgPSBbXTtcbiAgbGV0IGFkZGVkQXR0cmlidXRlcyA9IC8qIEBfX1BVUkVfXyAqLyBuZXcgTWFwKCk7XG4gIGxldCByZW1vdmVkQXR0cmlidXRlcyA9IC8qIEBfX1BVUkVfXyAqLyBuZXcgTWFwKCk7XG4gIGZvciAobGV0IGkgPSAwOyBpIDwgbXV0YXRpb25zLmxlbmd0aDsgaSsrKSB7XG4gICAgaWYgKG11dGF0aW9uc1tpXS50YXJnZXQuX3hfaWdub3JlTXV0YXRpb25PYnNlcnZlcilcbiAgICAgIGNvbnRpbnVlO1xuICAgIGlmIChtdXRhdGlvbnNbaV0udHlwZSA9PT0gXCJjaGlsZExpc3RcIikge1xuICAgICAgbXV0YXRpb25zW2ldLmFkZGVkTm9kZXMuZm9yRWFjaCgobm9kZSkgPT4gbm9kZS5ub2RlVHlwZSA9PT0gMSAmJiBhZGRlZE5vZGVzLnB1c2gobm9kZSkpO1xuICAgICAgbXV0YXRpb25zW2ldLnJlbW92ZWROb2Rlcy5mb3JFYWNoKChub2RlKSA9PiBub2RlLm5vZGVUeXBlID09PSAxICYmIHJlbW92ZWROb2Rlcy5wdXNoKG5vZGUpKTtcbiAgICB9XG4gICAgaWYgKG11dGF0aW9uc1tpXS50eXBlID09PSBcImF0dHJpYnV0ZXNcIikge1xuICAgICAgbGV0IGVsID0gbXV0YXRpb25zW2ldLnRhcmdldDtcbiAgICAgIGxldCBuYW1lID0gbXV0YXRpb25zW2ldLmF0dHJpYnV0ZU5hbWU7XG4gICAgICBsZXQgb2xkVmFsdWUgPSBtdXRhdGlvbnNbaV0ub2xkVmFsdWU7XG4gICAgICBsZXQgYWRkID0gKCkgPT4ge1xuICAgICAgICBpZiAoIWFkZGVkQXR0cmlidXRlcy5oYXMoZWwpKVxuICAgICAgICAgIGFkZGVkQXR0cmlidXRlcy5zZXQoZWwsIFtdKTtcbiAgICAgICAgYWRkZWRBdHRyaWJ1dGVzLmdldChlbCkucHVzaCh7IG5hbWUsIHZhbHVlOiBlbC5nZXRBdHRyaWJ1dGUobmFtZSkgfSk7XG4gICAgICB9O1xuICAgICAgbGV0IHJlbW92ZSA9ICgpID0+IHtcbiAgICAgICAgaWYgKCFyZW1vdmVkQXR0cmlidXRlcy5oYXMoZWwpKVxuICAgICAgICAgIHJlbW92ZWRBdHRyaWJ1dGVzLnNldChlbCwgW10pO1xuICAgICAgICByZW1vdmVkQXR0cmlidXRlcy5nZXQoZWwpLnB1c2gobmFtZSk7XG4gICAgICB9O1xuICAgICAgaWYgKGVsLmhhc0F0dHJpYnV0ZShuYW1lKSAmJiBvbGRWYWx1ZSA9PT0gbnVsbCkge1xuICAgICAgICBhZGQoKTtcbiAgICAgIH0gZWxzZSBpZiAoZWwuaGFzQXR0cmlidXRlKG5hbWUpKSB7XG4gICAgICAgIHJlbW92ZSgpO1xuICAgICAgICBhZGQoKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHJlbW92ZSgpO1xuICAgICAgfVxuICAgIH1cbiAgfVxuICByZW1vdmVkQXR0cmlidXRlcy5mb3JFYWNoKChhdHRycywgZWwpID0+IHtcbiAgICBjbGVhbnVwQXR0cmlidXRlcyhlbCwgYXR0cnMpO1xuICB9KTtcbiAgYWRkZWRBdHRyaWJ1dGVzLmZvckVhY2goKGF0dHJzLCBlbCkgPT4ge1xuICAgIG9uQXR0cmlidXRlQWRkZWRzLmZvckVhY2goKGkpID0+IGkoZWwsIGF0dHJzKSk7XG4gIH0pO1xuICBmb3IgKGxldCBub2RlIG9mIHJlbW92ZWROb2Rlcykge1xuICAgIGlmIChhZGRlZE5vZGVzLmluY2x1ZGVzKG5vZGUpKVxuICAgICAgY29udGludWU7XG4gICAgb25FbFJlbW92ZWRzLmZvckVhY2goKGkpID0+IGkobm9kZSkpO1xuICAgIGlmIChub2RlLl94X2NsZWFudXBzKSB7XG4gICAgICB3aGlsZSAobm9kZS5feF9jbGVhbnVwcy5sZW5ndGgpXG4gICAgICAgIG5vZGUuX3hfY2xlYW51cHMucG9wKCkoKTtcbiAgICB9XG4gIH1cbiAgYWRkZWROb2Rlcy5mb3JFYWNoKChub2RlKSA9PiB7XG4gICAgbm9kZS5feF9pZ25vcmVTZWxmID0gdHJ1ZTtcbiAgICBub2RlLl94X2lnbm9yZSA9IHRydWU7XG4gIH0pO1xuICBmb3IgKGxldCBub2RlIG9mIGFkZGVkTm9kZXMpIHtcbiAgICBpZiAocmVtb3ZlZE5vZGVzLmluY2x1ZGVzKG5vZGUpKVxuICAgICAgY29udGludWU7XG4gICAgaWYgKCFub2RlLmlzQ29ubmVjdGVkKVxuICAgICAgY29udGludWU7XG4gICAgZGVsZXRlIG5vZGUuX3hfaWdub3JlU2VsZjtcbiAgICBkZWxldGUgbm9kZS5feF9pZ25vcmU7XG4gICAgb25FbEFkZGVkcy5mb3JFYWNoKChpKSA9PiBpKG5vZGUpKTtcbiAgICBub2RlLl94X2lnbm9yZSA9IHRydWU7XG4gICAgbm9kZS5feF9pZ25vcmVTZWxmID0gdHJ1ZTtcbiAgfVxuICBhZGRlZE5vZGVzLmZvckVhY2goKG5vZGUpID0+IHtcbiAgICBkZWxldGUgbm9kZS5feF9pZ25vcmVTZWxmO1xuICAgIGRlbGV0ZSBub2RlLl94X2lnbm9yZTtcbiAgfSk7XG4gIGFkZGVkTm9kZXMgPSBudWxsO1xuICByZW1vdmVkTm9kZXMgPSBudWxsO1xuICBhZGRlZEF0dHJpYnV0ZXMgPSBudWxsO1xuICByZW1vdmVkQXR0cmlidXRlcyA9IG51bGw7XG59XG5cbi8vIG5vZGVfbW9kdWxlcy9hbHBpbmVqcy9zcmMvdXRpbHMvb25jZS5qc1xuZnVuY3Rpb24gb25jZShjYWxsYmFjaywgZmFsbGJhY2sgPSAoKSA9PiB7XG59KSB7XG4gIGxldCBjYWxsZWQgPSBmYWxzZTtcbiAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgIGlmICghY2FsbGVkKSB7XG4gICAgICBjYWxsZWQgPSB0cnVlO1xuICAgICAgY2FsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZmFsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG4gIH07XG59XG5cbi8vIHNyYy9pbmRleC5qc1xuZnVuY3Rpb24gc3JjX2RlZmF1bHQoQWxwaW5lKSB7XG4gIGNvbnN0IGRlZmF1bHRPcHRpb25zID0ge1xuICAgIGRpc21pc3NhYmxlOiB0cnVlLFxuICAgIHRyYXA6IGZhbHNlXG4gIH07XG4gIGZ1bmN0aW9uIHNldHVwQTExeShjb21wb25lbnQsIHRyaWdnZXIsIHBhbmVsID0gbnVsbCkge1xuICAgIGlmICghdHJpZ2dlcilcbiAgICAgIHJldHVybjtcbiAgICBpZiAoIXRyaWdnZXIuaGFzQXR0cmlidXRlKFwiYXJpYS1leHBhbmRlZFwiKSkge1xuICAgICAgdHJpZ2dlci5zZXRBdHRyaWJ1dGUoXCJhcmlhLWV4cGFuZGVkXCIsIGZhbHNlKTtcbiAgICB9XG4gICAgaWYgKCFwYW5lbC5oYXNBdHRyaWJ1dGUoXCJpZFwiKSkge1xuICAgICAgY29uc3QgcGFuZWxJZCA9IGBwYW5lbC0ke3JhbmRvbVN0cmluZyg4KX1gO1xuICAgICAgdHJpZ2dlci5zZXRBdHRyaWJ1dGUoXCJhcmlhLWNvbnRyb2xzXCIsIHBhbmVsSWQpO1xuICAgICAgcGFuZWwuc2V0QXR0cmlidXRlKFwiaWRcIiwgcGFuZWxJZCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRyaWdnZXIuc2V0QXR0cmlidXRlKFwiYXJpYS1jb250cm9sc1wiLCBwYW5lbC5nZXRBdHRyaWJ1dGUoXCJpZFwiKSk7XG4gICAgfVxuICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcImFyaWEtbW9kYWxcIiwgdHJ1ZSk7XG4gICAgcGFuZWwuc2V0QXR0cmlidXRlKFwicm9sZVwiLCBcImRpYWxvZ1wiKTtcbiAgfVxuICBjb25zdCBhdE1hZ2ljVHJpZ2dlciA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ1tcXFxcQGNsaWNrXj1cIiRmbG9hdFwiXScpO1xuICBjb25zdCB4TWFnaWNUcmlnZ2VyID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW3gtb25cXFxcOmNsaWNrXj1cIiRmbG9hdFwiXScpO1xuICBbLi4uYXRNYWdpY1RyaWdnZXIsIC4uLnhNYWdpY1RyaWdnZXJdLmZvckVhY2goKHRyaWdnZXIpID0+IHtcbiAgICBjb25zdCBjb21wb25lbnQgPSB0cmlnZ2VyLnBhcmVudEVsZW1lbnQuY2xvc2VzdChcIlt4LWRhdGFdXCIpO1xuICAgIGNvbnN0IHBhbmVsID0gY29tcG9uZW50LnF1ZXJ5U2VsZWN0b3IoJ1t4LXJlZj1cInBhbmVsXCJdJyk7XG4gICAgc2V0dXBBMTF5KGNvbXBvbmVudCwgdHJpZ2dlciwgcGFuZWwpO1xuICB9KTtcbiAgQWxwaW5lLm1hZ2ljKFwiZmxvYXRcIiwgKGVsKSA9PiB7XG4gICAgcmV0dXJuIChtb2RpZmllcnMgPSB7fSwgc2V0dGluZ3MgPSB7fSkgPT4ge1xuICAgICAgY29uc3Qgb3B0aW9ucyA9IHsgLi4uZGVmYXVsdE9wdGlvbnMsIC4uLnNldHRpbmdzIH07XG4gICAgICBjb25zdCBjb25maWcgPSBPYmplY3Qua2V5cyhtb2RpZmllcnMpLmxlbmd0aCA+IDAgPyBidWlsZENvbmZpZ0Zyb21Nb2RpZmllcnMobW9kaWZpZXJzKSA6IHsgbWlkZGxld2FyZTogW2F1dG9QbGFjZW1lbnQoKV0gfTtcbiAgICAgIGNvbnN0IHRyaWdnZXIgPSBlbDtcbiAgICAgIGNvbnN0IGNvbXBvbmVudCA9IGVsLnBhcmVudEVsZW1lbnQuY2xvc2VzdChcIlt4LWRhdGFdXCIpO1xuICAgICAgY29uc3QgcGFuZWwgPSBjb21wb25lbnQucXVlcnlTZWxlY3RvcignW3gtcmVmPVwicGFuZWxcIl0nKTtcbiAgICAgIGZ1bmN0aW9uIGlzRmxvYXRpbmcoKSB7XG4gICAgICAgIHJldHVybiBwYW5lbC5zdHlsZS5kaXNwbGF5ID09IFwiYmxvY2tcIjtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIGNsb3NlUGFuZWwoKSB7XG4gICAgICAgIHBhbmVsLnN0eWxlLmRpc3BsYXkgPSBcIlwiO1xuICAgICAgICB0cmlnZ2VyLnNldEF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIiwgZmFsc2UpO1xuICAgICAgICBpZiAob3B0aW9ucy50cmFwKVxuICAgICAgICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcIngtdHJhcFwiLCBmYWxzZSk7XG4gICAgICAgIGF1dG9VcGRhdGUoZWwsIHBhbmVsLCB1cGRhdGUpO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gb3BlblBhbmVsKCkge1xuICAgICAgICBwYW5lbC5zdHlsZS5kaXNwbGF5ID0gXCJibG9ja1wiO1xuICAgICAgICB0cmlnZ2VyLnNldEF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIiwgdHJ1ZSk7XG4gICAgICAgIGlmIChvcHRpb25zLnRyYXApXG4gICAgICAgICAgcGFuZWwuc2V0QXR0cmlidXRlKFwieC10cmFwXCIsIHRydWUpO1xuICAgICAgICB1cGRhdGUoKTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIHRvZ2dsZVBhbmVsKCkge1xuICAgICAgICBpc0Zsb2F0aW5nKCkgPyBjbG9zZVBhbmVsKCkgOiBvcGVuUGFuZWwoKTtcbiAgICAgIH1cbiAgICAgIGFzeW5jIGZ1bmN0aW9uIHVwZGF0ZSgpIHtcbiAgICAgICAgcmV0dXJuIGF3YWl0IGNvbXB1dGVQb3NpdGlvbjIoZWwsIHBhbmVsLCBjb25maWcpLnRoZW4oKHsgbWlkZGxld2FyZURhdGEsIHBsYWNlbWVudCwgeCwgeSB9KSA9PiB7XG4gICAgICAgICAgaWYgKG1pZGRsZXdhcmVEYXRhLmFycm93KSB7XG4gICAgICAgICAgICBjb25zdCBheCA9IG1pZGRsZXdhcmVEYXRhLmFycm93Py54O1xuICAgICAgICAgICAgY29uc3QgYXkgPSBtaWRkbGV3YXJlRGF0YS5hcnJvdz8ueTtcbiAgICAgICAgICAgIGNvbnN0IGFFbCA9IGNvbmZpZy5taWRkbGV3YXJlLmZpbHRlcigobWlkZGxld2FyZSkgPT4gbWlkZGxld2FyZS5uYW1lID09IFwiYXJyb3dcIilbMF0ub3B0aW9ucy5lbGVtZW50O1xuICAgICAgICAgICAgY29uc3Qgc3RhdGljU2lkZSA9IHtcbiAgICAgICAgICAgICAgdG9wOiBcImJvdHRvbVwiLFxuICAgICAgICAgICAgICByaWdodDogXCJsZWZ0XCIsXG4gICAgICAgICAgICAgIGJvdHRvbTogXCJ0b3BcIixcbiAgICAgICAgICAgICAgbGVmdDogXCJyaWdodFwiXG4gICAgICAgICAgICB9W3BsYWNlbWVudC5zcGxpdChcIi1cIilbMF1dO1xuICAgICAgICAgICAgT2JqZWN0LmFzc2lnbihhRWwuc3R5bGUsIHtcbiAgICAgICAgICAgICAgbGVmdDogYXggIT0gbnVsbCA/IGAke2F4fXB4YCA6IFwiXCIsXG4gICAgICAgICAgICAgIHRvcDogYXkgIT0gbnVsbCA/IGAke2F5fXB4YCA6IFwiXCIsXG4gICAgICAgICAgICAgIHJpZ2h0OiBcIlwiLFxuICAgICAgICAgICAgICBib3R0b206IFwiXCIsXG4gICAgICAgICAgICAgIFtzdGF0aWNTaWRlXTogXCItNHB4XCJcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAobWlkZGxld2FyZURhdGEuaGlkZSkge1xuICAgICAgICAgICAgY29uc3QgeyByZWZlcmVuY2VIaWRkZW4gfSA9IG1pZGRsZXdhcmVEYXRhLmhpZGU7XG4gICAgICAgICAgICBPYmplY3QuYXNzaWduKHBhbmVsLnN0eWxlLCB7XG4gICAgICAgICAgICAgIHZpc2liaWxpdHk6IHJlZmVyZW5jZUhpZGRlbiA/IFwiaGlkZGVuXCIgOiBcInZpc2libGVcIlxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfVxuICAgICAgICAgIE9iamVjdC5hc3NpZ24ocGFuZWwuc3R5bGUsIHtcbiAgICAgICAgICAgIGxlZnQ6IGAke3h9cHhgLFxuICAgICAgICAgICAgdG9wOiBgJHt5fXB4YFxuICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLmRpc21pc3NhYmxlKSB7XG4gICAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwiY2xpY2tcIiwgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgaWYgKCFjb21wb25lbnQuY29udGFpbnMoZXZlbnQudGFyZ2V0KSAmJiBpc0Zsb2F0aW5nKCkpIHtcbiAgICAgICAgICAgIHRvZ2dsZVBhbmVsKCk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXG4gICAgICAgICAgXCJrZXlkb3duXCIsXG4gICAgICAgICAgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICBpZiAoZXZlbnQua2V5ID09PSBcIkVzY2FwZVwiICYmIGlzRmxvYXRpbmcoKSkge1xuICAgICAgICAgICAgICB0b2dnbGVQYW5lbCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0sXG4gICAgICAgICAgdHJ1ZVxuICAgICAgICApO1xuICAgICAgfVxuICAgICAgdG9nZ2xlUGFuZWwoKTtcbiAgICB9O1xuICB9KTtcbiAgQWxwaW5lLmRpcmVjdGl2ZShcImZsb2F0XCIsIChwYW5lbCwgeyBtb2RpZmllcnMsIGV4cHJlc3Npb24gfSwgeyBldmFsdWF0ZSwgZWZmZWN0IH0pID0+IHtcbiAgICBjb25zdCBzZXR0aW5ncyA9IGV4cHJlc3Npb24gPyBldmFsdWF0ZShleHByZXNzaW9uKSA6IHt9O1xuICAgIGNvbnN0IGNvbmZpZyA9IG1vZGlmaWVycy5sZW5ndGggPiAwID8gYnVpbGREaXJlY3RpdmVDb25maWdGcm9tTW9kaWZpZXJzKG1vZGlmaWVycywgc2V0dGluZ3MpIDoge307XG4gICAgbGV0IGNsZWFudXAgPSBudWxsO1xuICAgIGlmIChjb25maWcuZmxvYXQuc3RyYXRlZ3kgPT0gXCJmaXhlZFwiKSB7XG4gICAgICBwYW5lbC5zdHlsZS5wb3NpdGlvbiA9IFwiZml4ZWRcIjtcbiAgICB9XG4gICAgY29uc3QgY2xpY2tBd2F5ID0gKGV2ZW50KSA9PiBwYW5lbC5wYXJlbnRFbGVtZW50ICYmICFwYW5lbC5wYXJlbnRFbGVtZW50LmNsb3Nlc3QoXCJbeC1kYXRhXVwiKS5jb250YWlucyhldmVudC50YXJnZXQpID8gcGFuZWwuY2xvc2UoKSA6IG51bGw7XG4gICAgY29uc3Qga2V5RXNjYXBlID0gKGV2ZW50KSA9PiBldmVudC5rZXkgPT09IFwiRXNjYXBlXCIgPyBwYW5lbC5jbG9zZSgpIDogbnVsbDtcbiAgICBjb25zdCByZWZOYW1lID0gcGFuZWwuZ2V0QXR0cmlidXRlKFwieC1yZWZcIik7XG4gICAgY29uc3QgY29tcG9uZW50ID0gcGFuZWwucGFyZW50RWxlbWVudC5jbG9zZXN0KFwiW3gtZGF0YV1cIik7XG4gICAgY29uc3QgYXRUcmlnZ2VyID0gY29tcG9uZW50LnF1ZXJ5U2VsZWN0b3JBbGwoYFtcXFxcQGNsaWNrXj1cIiRyZWZzLiR7cmVmTmFtZX1cIl1gKTtcbiAgICBjb25zdCB4VHJpZ2dlciA9IGNvbXBvbmVudC5xdWVyeVNlbGVjdG9yQWxsKGBbeC1vblxcXFw6Y2xpY2tePVwiJHJlZnMuJHtyZWZOYW1lfVwiXWApO1xuICAgIHBhbmVsLnN0eWxlLnNldFByb3BlcnR5KFwiZGlzcGxheVwiLCBcIm5vbmVcIik7XG4gICAgc2V0dXBBMTF5KGNvbXBvbmVudCwgWy4uLmF0VHJpZ2dlciwgLi4ueFRyaWdnZXJdWzBdLCBwYW5lbCk7XG4gICAgcGFuZWwuX3hfaXNTaG93biA9IGZhbHNlO1xuICAgIHBhbmVsLnRyaWdnZXIgPSBudWxsO1xuICAgIGlmICghcGFuZWwuX3hfZG9IaWRlKVxuICAgICAgcGFuZWwuX3hfZG9IaWRlID0gKCkgPT4ge1xuICAgICAgICBtdXRhdGVEb20oKCkgPT4ge1xuICAgICAgICAgIHBhbmVsLnN0eWxlLnNldFByb3BlcnR5KFwiZGlzcGxheVwiLCBcIm5vbmVcIiwgbW9kaWZpZXJzLmluY2x1ZGVzKFwiaW1wb3J0YW50XCIpID8gXCJpbXBvcnRhbnRcIiA6IHZvaWQgMCk7XG4gICAgICAgIH0pO1xuICAgICAgfTtcbiAgICBpZiAoIXBhbmVsLl94X2RvU2hvdylcbiAgICAgIHBhbmVsLl94X2RvU2hvdyA9ICgpID0+IHtcbiAgICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgICBwYW5lbC5zdHlsZS5zZXRQcm9wZXJ0eShcImRpc3BsYXlcIiwgXCJibG9ja1wiLCBtb2RpZmllcnMuaW5jbHVkZXMoXCJpbXBvcnRhbnRcIikgPyBcImltcG9ydGFudFwiIDogdm9pZCAwKTtcbiAgICAgICAgfSk7XG4gICAgICB9O1xuICAgIGxldCBoaWRlMiA9ICgpID0+IHtcbiAgICAgIHBhbmVsLl94X2RvSGlkZSgpO1xuICAgICAgcGFuZWwuX3hfaXNTaG93biA9IGZhbHNlO1xuICAgIH07XG4gICAgbGV0IHNob3cgPSAoKSA9PiB7XG4gICAgICBwYW5lbC5feF9kb1Nob3coKTtcbiAgICAgIHBhbmVsLl94X2lzU2hvd24gPSB0cnVlO1xuICAgIH07XG4gICAgbGV0IGNsaWNrQXdheUNvbXBhdGlibGVTaG93ID0gKCkgPT4gc2V0VGltZW91dChzaG93KTtcbiAgICBsZXQgdG9nZ2xlID0gb25jZShcbiAgICAgICh2YWx1ZSkgPT4gdmFsdWUgPyBzaG93KCkgOiBoaWRlMigpLFxuICAgICAgKHZhbHVlKSA9PiB7XG4gICAgICAgIGlmICh0eXBlb2YgcGFuZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgICAgICAgcGFuZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyhwYW5lbCwgdmFsdWUsIHNob3csIGhpZGUyKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICB2YWx1ZSA/IGNsaWNrQXdheUNvbXBhdGlibGVTaG93KCkgOiBoaWRlMigpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgKTtcbiAgICBsZXQgb2xkVmFsdWU7XG4gICAgbGV0IGZpcnN0VGltZSA9IHRydWU7XG4gICAgZWZmZWN0KFxuICAgICAgKCkgPT4gZXZhbHVhdGUoKHZhbHVlKSA9PiB7XG4gICAgICAgIGlmICghZmlyc3RUaW1lICYmIHZhbHVlID09PSBvbGRWYWx1ZSlcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJpbW1lZGlhdGVcIikpXG4gICAgICAgICAgdmFsdWUgPyBjbGlja0F3YXlDb21wYXRpYmxlU2hvdygpIDogaGlkZTIoKTtcbiAgICAgICAgdG9nZ2xlKHZhbHVlKTtcbiAgICAgICAgb2xkVmFsdWUgPSB2YWx1ZTtcbiAgICAgICAgZmlyc3RUaW1lID0gZmFsc2U7XG4gICAgICB9KVxuICAgICk7XG4gICAgcGFuZWwub3BlbiA9IGFzeW5jIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICBwYW5lbC50cmlnZ2VyID0gZXZlbnQuY3VycmVudFRhcmdldCA/IGV2ZW50LmN1cnJlbnRUYXJnZXQgOiBldmVudDtcbiAgICAgIHRvZ2dsZSh0cnVlKTtcbiAgICAgIHBhbmVsLnRyaWdnZXIuc2V0QXR0cmlidXRlKFwiYXJpYS1leHBhbmRlZFwiLCB0cnVlKTtcbiAgICAgIGlmIChjb25maWcuY29tcG9uZW50LnRyYXApXG4gICAgICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcIngtdHJhcFwiLCB0cnVlKTtcbiAgICAgIGNsZWFudXAgPSBhdXRvVXBkYXRlKHBhbmVsLnRyaWdnZXIsIHBhbmVsLCAoKSA9PiB7XG4gICAgICAgIGNvbXB1dGVQb3NpdGlvbjIocGFuZWwudHJpZ2dlciwgcGFuZWwsIGNvbmZpZy5mbG9hdCkudGhlbigoeyBtaWRkbGV3YXJlRGF0YSwgcGxhY2VtZW50LCB4LCB5IH0pID0+IHtcbiAgICAgICAgICBpZiAobWlkZGxld2FyZURhdGEuYXJyb3cpIHtcbiAgICAgICAgICAgIGNvbnN0IGF4ID0gbWlkZGxld2FyZURhdGEuYXJyb3c/Lng7XG4gICAgICAgICAgICBjb25zdCBheSA9IG1pZGRsZXdhcmVEYXRhLmFycm93Py55O1xuICAgICAgICAgICAgY29uc3QgYUVsID0gY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUuZmlsdGVyKChtaWRkbGV3YXJlKSA9PiBtaWRkbGV3YXJlLm5hbWUgPT0gXCJhcnJvd1wiKVswXS5vcHRpb25zLmVsZW1lbnQ7XG4gICAgICAgICAgICBjb25zdCBzdGF0aWNTaWRlID0ge1xuICAgICAgICAgICAgICB0b3A6IFwiYm90dG9tXCIsXG4gICAgICAgICAgICAgIHJpZ2h0OiBcImxlZnRcIixcbiAgICAgICAgICAgICAgYm90dG9tOiBcInRvcFwiLFxuICAgICAgICAgICAgICBsZWZ0OiBcInJpZ2h0XCJcbiAgICAgICAgICAgIH1bcGxhY2VtZW50LnNwbGl0KFwiLVwiKVswXV07XG4gICAgICAgICAgICBPYmplY3QuYXNzaWduKGFFbC5zdHlsZSwge1xuICAgICAgICAgICAgICBsZWZ0OiBheCAhPSBudWxsID8gYCR7YXh9cHhgIDogXCJcIixcbiAgICAgICAgICAgICAgdG9wOiBheSAhPSBudWxsID8gYCR7YXl9cHhgIDogXCJcIixcbiAgICAgICAgICAgICAgcmlnaHQ6IFwiXCIsXG4gICAgICAgICAgICAgIGJvdHRvbTogXCJcIixcbiAgICAgICAgICAgICAgW3N0YXRpY1NpZGVdOiBcIi00cHhcIlxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmIChtaWRkbGV3YXJlRGF0YS5oaWRlKSB7XG4gICAgICAgICAgICBjb25zdCB7IHJlZmVyZW5jZUhpZGRlbiB9ID0gbWlkZGxld2FyZURhdGEuaGlkZTtcbiAgICAgICAgICAgIE9iamVjdC5hc3NpZ24ocGFuZWwuc3R5bGUsIHtcbiAgICAgICAgICAgICAgdmlzaWJpbGl0eTogcmVmZXJlbmNlSGlkZGVuID8gXCJoaWRkZW5cIiA6IFwidmlzaWJsZVwiXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG4gICAgICAgICAgT2JqZWN0LmFzc2lnbihwYW5lbC5zdHlsZSwge1xuICAgICAgICAgICAgbGVmdDogYCR7eH1weGAsXG4gICAgICAgICAgICB0b3A6IGAke3l9cHhgXG4gICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGNsaWNrQXdheSk7XG4gICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihcImtleWRvd25cIiwga2V5RXNjYXBlLCB0cnVlKTtcbiAgICB9O1xuICAgIHBhbmVsLmNsb3NlID0gZnVuY3Rpb24oKSB7XG4gICAgICB0b2dnbGUoZmFsc2UpO1xuICAgICAgcGFuZWwudHJpZ2dlci5zZXRBdHRyaWJ1dGUoXCJhcmlhLWV4cGFuZGVkXCIsIGZhbHNlKTtcbiAgICAgIGlmIChjb25maWcuY29tcG9uZW50LnRyYXApXG4gICAgICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcIngtdHJhcFwiLCBmYWxzZSk7XG4gICAgICBjbGVhbnVwKCk7XG4gICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGNsaWNrQXdheSk7XG4gICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcihcImtleWRvd25cIiwga2V5RXNjYXBlLCBmYWxzZSk7XG4gICAgfTtcbiAgICBwYW5lbC50b2dnbGUgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgcGFuZWwuX3hfaXNTaG93biA/IHBhbmVsLmNsb3NlKCkgOiBwYW5lbC5vcGVuKGV2ZW50KTtcbiAgICB9O1xuICB9KTtcbn1cblxuLy8gYnVpbGRzL21vZHVsZS5qc1xudmFyIG1vZHVsZV9kZWZhdWx0ID0gc3JjX2RlZmF1bHQ7XG5leHBvcnQge1xuICBtb2R1bGVfZGVmYXVsdCBhcyBkZWZhdWx0XG59O1xuIiwgIi8vIHNyYy9jb3JlL2FscGluZS1sYXp5LWxvYWQtYXNzZXRzLmpzXG5mdW5jdGlvbiBhbHBpbmVfbGF6eV9sb2FkX2Fzc2V0c19kZWZhdWx0KEFscGluZSkge1xuICBBbHBpbmUuc3RvcmUoXCJsYXp5TG9hZGVkQXNzZXRzXCIsIHtcbiAgICBsb2FkZWQ6IG5ldyBTZXQoKSxcbiAgICBjaGVjayhwYXRocykge1xuICAgICAgcmV0dXJuIEFycmF5LmlzQXJyYXkocGF0aHMpID8gcGF0aHMuZXZlcnkoKHBhdGgpID0+IHRoaXMubG9hZGVkLmhhcyhwYXRoKSkgOiB0aGlzLmxvYWRlZC5oYXMocGF0aHMpO1xuICAgIH0sXG4gICAgbWFya0xvYWRlZChwYXRocykge1xuICAgICAgQXJyYXkuaXNBcnJheShwYXRocykgPyBwYXRocy5mb3JFYWNoKChwYXRoKSA9PiB0aGlzLmxvYWRlZC5hZGQocGF0aCkpIDogdGhpcy5sb2FkZWQuYWRkKHBhdGhzKTtcbiAgICB9XG4gIH0pO1xuICBmdW5jdGlvbiBhc3NldExvYWRlZEV2ZW50KGV2ZW50TmFtZSkge1xuICAgIHJldHVybiBuZXcgQ3VzdG9tRXZlbnQoZXZlbnROYW1lLCB7XG4gICAgICBidWJibGVzOiB0cnVlLFxuICAgICAgY29tcG9zZWQ6IHRydWUsXG4gICAgICBjYW5jZWxhYmxlOiB0cnVlXG4gICAgfSk7XG4gIH1cbiAgYXN5bmMgZnVuY3Rpb24gbG9hZENTUyhwYXRoLCBtZWRpYUF0dHIpIHtcbiAgICBpZiAoZG9jdW1lbnQucXVlcnlTZWxlY3RvcihgbGlua1tocmVmPVwiJHtwYXRofVwiXWApIHx8IEFscGluZS5zdG9yZShcImxhenlMb2FkZWRBc3NldHNcIikuY2hlY2socGF0aCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbGluayA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJsaW5rXCIpO1xuICAgIGxpbmsudHlwZSA9IFwidGV4dC9jc3NcIjtcbiAgICBsaW5rLnJlbCA9IFwic3R5bGVzaGVldFwiO1xuICAgIGxpbmsuaHJlZiA9IHBhdGg7XG4gICAgaWYgKG1lZGlhQXR0cikge1xuICAgICAgbGluay5tZWRpYSA9IG1lZGlhQXR0cjtcbiAgICB9XG4gICAgZG9jdW1lbnQuaGVhZC5hcHBlbmQobGluayk7XG4gICAgYXdhaXQgbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgbGluay5vbmxvYWQgPSAoKSA9PiB7XG4gICAgICAgIEFscGluZS5zdG9yZShcImxhenlMb2FkZWRBc3NldHNcIikubWFya0xvYWRlZChwYXRoKTtcbiAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgfTtcbiAgICAgIGxpbmsub25lcnJvciA9ICgpID0+IHtcbiAgICAgICAgcmVqZWN0KG5ldyBFcnJvcihgRmFpbGVkIHRvIGxvYWQgQ1NTOiAke3BhdGh9YCkpO1xuICAgICAgfTtcbiAgICB9KTtcbiAgfVxuICBhc3luYyBmdW5jdGlvbiBsb2FkSlMocGF0aCwgcG9zaXRpb24pIHtcbiAgICBpZiAoZG9jdW1lbnQucXVlcnlTZWxlY3Rvcihgc2NyaXB0W3NyYz1cIiR7cGF0aH1cIl1gKSB8fCBBbHBpbmUuc3RvcmUoXCJsYXp5TG9hZGVkQXNzZXRzXCIpLmNoZWNrKHBhdGgpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGNvbnN0IHNjcmlwdCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzY3JpcHRcIik7XG4gICAgc2NyaXB0LnNyYyA9IHBhdGg7XG4gICAgcG9zaXRpb24uaGFzKFwiYm9keS1zdGFydFwiKSA/IGRvY3VtZW50LmJvZHkucHJlcGVuZChzY3JpcHQpIDogZG9jdW1lbnRbcG9zaXRpb24uaGFzKFwiYm9keS1lbmRcIikgPyBcImJvZHlcIiA6IFwiaGVhZFwiXS5hcHBlbmQoc2NyaXB0KTtcbiAgICBhd2FpdCBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICBzY3JpcHQub25sb2FkID0gKCkgPT4ge1xuICAgICAgICBBbHBpbmUuc3RvcmUoXCJsYXp5TG9hZGVkQXNzZXRzXCIpLm1hcmtMb2FkZWQocGF0aCk7XG4gICAgICAgIHJlc29sdmUoKTtcbiAgICAgIH07XG4gICAgICBzY3JpcHQub25lcnJvciA9ICgpID0+IHtcbiAgICAgICAgcmVqZWN0KG5ldyBFcnJvcihgRmFpbGVkIHRvIGxvYWQgSlM6ICR7cGF0aH1gKSk7XG4gICAgICB9O1xuICAgIH0pO1xuICB9XG4gIEFscGluZS5kaXJlY3RpdmUoXCJsb2FkLWNzc1wiLCAoZWwsIHsgZXhwcmVzc2lvbiB9LCB7IGV2YWx1YXRlIH0pID0+IHtcbiAgICBjb25zdCBwYXRocyA9IGV2YWx1YXRlKGV4cHJlc3Npb24pO1xuICAgIGNvbnN0IG1lZGlhQXR0ciA9IGVsLm1lZGlhO1xuICAgIGNvbnN0IGV2ZW50TmFtZSA9IGVsLmdldEF0dHJpYnV0ZShcImRhdGEtZGlzcGF0Y2hcIik7XG4gICAgUHJvbWlzZS5hbGwocGF0aHMubWFwKChwYXRoKSA9PiBsb2FkQ1NTKHBhdGgsIG1lZGlhQXR0cikpKS50aGVuKCgpID0+IHtcbiAgICAgIGlmIChldmVudE5hbWUpIHtcbiAgICAgICAgd2luZG93LmRpc3BhdGNoRXZlbnQoYXNzZXRMb2FkZWRFdmVudChldmVudE5hbWUgKyBcIi1jc3NcIikpO1xuICAgICAgfVxuICAgIH0pLmNhdGNoKChlcnJvcikgPT4ge1xuICAgICAgY29uc29sZS5lcnJvcihlcnJvcik7XG4gICAgfSk7XG4gIH0pO1xuICBBbHBpbmUuZGlyZWN0aXZlKFwibG9hZC1qc1wiLCAoZWwsIHsgZXhwcmVzc2lvbiwgbW9kaWZpZXJzIH0sIHsgZXZhbHVhdGUgfSkgPT4ge1xuICAgIGNvbnN0IHBhdGhzID0gZXZhbHVhdGUoZXhwcmVzc2lvbik7XG4gICAgY29uc3QgcG9zaXRpb24gPSBuZXcgU2V0KG1vZGlmaWVycyk7XG4gICAgY29uc3QgZXZlbnROYW1lID0gZWwuZ2V0QXR0cmlidXRlKFwiZGF0YS1kaXNwYXRjaFwiKTtcbiAgICBQcm9taXNlLmFsbChwYXRocy5tYXAoKHBhdGgpID0+IGxvYWRKUyhwYXRoLCBwb3NpdGlvbikpKS50aGVuKCgpID0+IHtcbiAgICAgIGlmIChldmVudE5hbWUpIHtcbiAgICAgICAgd2luZG93LmRpc3BhdGNoRXZlbnQoYXNzZXRMb2FkZWRFdmVudChldmVudE5hbWUgKyBcIi1qc1wiKSk7XG4gICAgICB9XG4gICAgfSkuY2F0Y2goKGVycm9yKSA9PiB7XG4gICAgICBjb25zb2xlLmVycm9yKGVycm9yKTtcbiAgICB9KTtcbiAgfSk7XG59XG5cbi8vIHNyYy9tb2R1bGUuanNcbnZhciBtb2R1bGVfZGVmYXVsdCA9IGFscGluZV9sYXp5X2xvYWRfYXNzZXRzX2RlZmF1bHQ7XG5leHBvcnQge1xuICBtb2R1bGVfZGVmYXVsdCBhcyBkZWZhdWx0XG59O1xuIiwgIi8qKiFcbiAqIFNvcnRhYmxlIDEuMTUuMFxuICogQGF1dGhvclx0UnViYVhhICAgPHRyYXNoQHJ1YmF4YS5vcmc+XG4gKiBAYXV0aG9yXHRvd2VubSAgICA8b3dlbjIzMzU1QGdtYWlsLmNvbT5cbiAqIEBsaWNlbnNlIE1JVFxuICovXG5mdW5jdGlvbiBvd25LZXlzKG9iamVjdCwgZW51bWVyYWJsZU9ubHkpIHtcbiAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhvYmplY3QpO1xuXG4gIGlmIChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKSB7XG4gICAgdmFyIHN5bWJvbHMgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKG9iamVjdCk7XG5cbiAgICBpZiAoZW51bWVyYWJsZU9ubHkpIHtcbiAgICAgIHN5bWJvbHMgPSBzeW1ib2xzLmZpbHRlcihmdW5jdGlvbiAoc3ltKSB7XG4gICAgICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yKG9iamVjdCwgc3ltKS5lbnVtZXJhYmxlO1xuICAgICAgfSk7XG4gICAgfVxuXG4gICAga2V5cy5wdXNoLmFwcGx5KGtleXMsIHN5bWJvbHMpO1xuICB9XG5cbiAgcmV0dXJuIGtleXM7XG59XG5cbmZ1bmN0aW9uIF9vYmplY3RTcHJlYWQyKHRhcmdldCkge1xuICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykge1xuICAgIHZhciBzb3VyY2UgPSBhcmd1bWVudHNbaV0gIT0gbnVsbCA/IGFyZ3VtZW50c1tpXSA6IHt9O1xuXG4gICAgaWYgKGkgJSAyKSB7XG4gICAgICBvd25LZXlzKE9iamVjdChzb3VyY2UpLCB0cnVlKS5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgICAgX2RlZmluZVByb3BlcnR5KHRhcmdldCwga2V5LCBzb3VyY2Vba2V5XSk7XG4gICAgICB9KTtcbiAgICB9IGVsc2UgaWYgKE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3JzKSB7XG4gICAgICBPYmplY3QuZGVmaW5lUHJvcGVydGllcyh0YXJnZXQsIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3JzKHNvdXJjZSkpO1xuICAgIH0gZWxzZSB7XG4gICAgICBvd25LZXlzKE9iamVjdChzb3VyY2UpKS5mb3JFYWNoKGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KHRhcmdldCwga2V5LCBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yKHNvdXJjZSwga2V5KSk7XG4gICAgICB9KTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGFyZ2V0O1xufVxuXG5mdW5jdGlvbiBfdHlwZW9mKG9iaikge1xuICBcIkBiYWJlbC9oZWxwZXJzIC0gdHlwZW9mXCI7XG5cbiAgaWYgKHR5cGVvZiBTeW1ib2wgPT09IFwiZnVuY3Rpb25cIiAmJiB0eXBlb2YgU3ltYm9sLml0ZXJhdG9yID09PSBcInN5bWJvbFwiKSB7XG4gICAgX3R5cGVvZiA9IGZ1bmN0aW9uIChvYmopIHtcbiAgICAgIHJldHVybiB0eXBlb2Ygb2JqO1xuICAgIH07XG4gIH0gZWxzZSB7XG4gICAgX3R5cGVvZiA9IGZ1bmN0aW9uIChvYmopIHtcbiAgICAgIHJldHVybiBvYmogJiYgdHlwZW9mIFN5bWJvbCA9PT0gXCJmdW5jdGlvblwiICYmIG9iai5jb25zdHJ1Y3RvciA9PT0gU3ltYm9sICYmIG9iaiAhPT0gU3ltYm9sLnByb3RvdHlwZSA/IFwic3ltYm9sXCIgOiB0eXBlb2Ygb2JqO1xuICAgIH07XG4gIH1cblxuICByZXR1cm4gX3R5cGVvZihvYmopO1xufVxuXG5mdW5jdGlvbiBfZGVmaW5lUHJvcGVydHkob2JqLCBrZXksIHZhbHVlKSB7XG4gIGlmIChrZXkgaW4gb2JqKSB7XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KG9iaiwga2V5LCB7XG4gICAgICB2YWx1ZTogdmFsdWUsXG4gICAgICBlbnVtZXJhYmxlOiB0cnVlLFxuICAgICAgY29uZmlndXJhYmxlOiB0cnVlLFxuICAgICAgd3JpdGFibGU6IHRydWVcbiAgICB9KTtcbiAgfSBlbHNlIHtcbiAgICBvYmpba2V5XSA9IHZhbHVlO1xuICB9XG5cbiAgcmV0dXJuIG9iajtcbn1cblxuZnVuY3Rpb24gX2V4dGVuZHMoKSB7XG4gIF9leHRlbmRzID0gT2JqZWN0LmFzc2lnbiB8fCBmdW5jdGlvbiAodGFyZ2V0KSB7XG4gICAgZm9yICh2YXIgaSA9IDE7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIHtcbiAgICAgIHZhciBzb3VyY2UgPSBhcmd1bWVudHNbaV07XG5cbiAgICAgIGZvciAodmFyIGtleSBpbiBzb3VyY2UpIHtcbiAgICAgICAgaWYgKE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChzb3VyY2UsIGtleSkpIHtcbiAgICAgICAgICB0YXJnZXRba2V5XSA9IHNvdXJjZVtrZXldO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIHRhcmdldDtcbiAgfTtcblxuICByZXR1cm4gX2V4dGVuZHMuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbn1cblxuZnVuY3Rpb24gX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzTG9vc2Uoc291cmNlLCBleGNsdWRlZCkge1xuICBpZiAoc291cmNlID09IG51bGwpIHJldHVybiB7fTtcbiAgdmFyIHRhcmdldCA9IHt9O1xuICB2YXIgc291cmNlS2V5cyA9IE9iamVjdC5rZXlzKHNvdXJjZSk7XG4gIHZhciBrZXksIGk7XG5cbiAgZm9yIChpID0gMDsgaSA8IHNvdXJjZUtleXMubGVuZ3RoOyBpKyspIHtcbiAgICBrZXkgPSBzb3VyY2VLZXlzW2ldO1xuICAgIGlmIChleGNsdWRlZC5pbmRleE9mKGtleSkgPj0gMCkgY29udGludWU7XG4gICAgdGFyZ2V0W2tleV0gPSBzb3VyY2Vba2V5XTtcbiAgfVxuXG4gIHJldHVybiB0YXJnZXQ7XG59XG5cbmZ1bmN0aW9uIF9vYmplY3RXaXRob3V0UHJvcGVydGllcyhzb3VyY2UsIGV4Y2x1ZGVkKSB7XG4gIGlmIChzb3VyY2UgPT0gbnVsbCkgcmV0dXJuIHt9O1xuXG4gIHZhciB0YXJnZXQgPSBfb2JqZWN0V2l0aG91dFByb3BlcnRpZXNMb29zZShzb3VyY2UsIGV4Y2x1ZGVkKTtcblxuICB2YXIga2V5LCBpO1xuXG4gIGlmIChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKSB7XG4gICAgdmFyIHNvdXJjZVN5bWJvbEtleXMgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKHNvdXJjZSk7XG5cbiAgICBmb3IgKGkgPSAwOyBpIDwgc291cmNlU3ltYm9sS2V5cy5sZW5ndGg7IGkrKykge1xuICAgICAga2V5ID0gc291cmNlU3ltYm9sS2V5c1tpXTtcbiAgICAgIGlmIChleGNsdWRlZC5pbmRleE9mKGtleSkgPj0gMCkgY29udGludWU7XG4gICAgICBpZiAoIU9iamVjdC5wcm90b3R5cGUucHJvcGVydHlJc0VudW1lcmFibGUuY2FsbChzb3VyY2UsIGtleSkpIGNvbnRpbnVlO1xuICAgICAgdGFyZ2V0W2tleV0gPSBzb3VyY2Vba2V5XTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGFyZ2V0O1xufVxuXG5mdW5jdGlvbiBfdG9Db25zdW1hYmxlQXJyYXkoYXJyKSB7XG4gIHJldHVybiBfYXJyYXlXaXRob3V0SG9sZXMoYXJyKSB8fCBfaXRlcmFibGVUb0FycmF5KGFycikgfHwgX3Vuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5KGFycikgfHwgX25vbkl0ZXJhYmxlU3ByZWFkKCk7XG59XG5cbmZ1bmN0aW9uIF9hcnJheVdpdGhvdXRIb2xlcyhhcnIpIHtcbiAgaWYgKEFycmF5LmlzQXJyYXkoYXJyKSkgcmV0dXJuIF9hcnJheUxpa2VUb0FycmF5KGFycik7XG59XG5cbmZ1bmN0aW9uIF9pdGVyYWJsZVRvQXJyYXkoaXRlcikge1xuICBpZiAodHlwZW9mIFN5bWJvbCAhPT0gXCJ1bmRlZmluZWRcIiAmJiBpdGVyW1N5bWJvbC5pdGVyYXRvcl0gIT0gbnVsbCB8fCBpdGVyW1wiQEBpdGVyYXRvclwiXSAhPSBudWxsKSByZXR1cm4gQXJyYXkuZnJvbShpdGVyKTtcbn1cblxuZnVuY3Rpb24gX3Vuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5KG8sIG1pbkxlbikge1xuICBpZiAoIW8pIHJldHVybjtcbiAgaWYgKHR5cGVvZiBvID09PSBcInN0cmluZ1wiKSByZXR1cm4gX2FycmF5TGlrZVRvQXJyYXkobywgbWluTGVuKTtcbiAgdmFyIG4gPSBPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nLmNhbGwobykuc2xpY2UoOCwgLTEpO1xuICBpZiAobiA9PT0gXCJPYmplY3RcIiAmJiBvLmNvbnN0cnVjdG9yKSBuID0gby5jb25zdHJ1Y3Rvci5uYW1lO1xuICBpZiAobiA9PT0gXCJNYXBcIiB8fCBuID09PSBcIlNldFwiKSByZXR1cm4gQXJyYXkuZnJvbShvKTtcbiAgaWYgKG4gPT09IFwiQXJndW1lbnRzXCIgfHwgL14oPzpVaXxJKW50KD86OHwxNnwzMikoPzpDbGFtcGVkKT9BcnJheSQvLnRlc3QobikpIHJldHVybiBfYXJyYXlMaWtlVG9BcnJheShvLCBtaW5MZW4pO1xufVxuXG5mdW5jdGlvbiBfYXJyYXlMaWtlVG9BcnJheShhcnIsIGxlbikge1xuICBpZiAobGVuID09IG51bGwgfHwgbGVuID4gYXJyLmxlbmd0aCkgbGVuID0gYXJyLmxlbmd0aDtcblxuICBmb3IgKHZhciBpID0gMCwgYXJyMiA9IG5ldyBBcnJheShsZW4pOyBpIDwgbGVuOyBpKyspIGFycjJbaV0gPSBhcnJbaV07XG5cbiAgcmV0dXJuIGFycjI7XG59XG5cbmZ1bmN0aW9uIF9ub25JdGVyYWJsZVNwcmVhZCgpIHtcbiAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkludmFsaWQgYXR0ZW1wdCB0byBzcHJlYWQgbm9uLWl0ZXJhYmxlIGluc3RhbmNlLlxcbkluIG9yZGVyIHRvIGJlIGl0ZXJhYmxlLCBub24tYXJyYXkgb2JqZWN0cyBtdXN0IGhhdmUgYSBbU3ltYm9sLml0ZXJhdG9yXSgpIG1ldGhvZC5cIik7XG59XG5cbnZhciB2ZXJzaW9uID0gXCIxLjE1LjBcIjtcblxuZnVuY3Rpb24gdXNlckFnZW50KHBhdHRlcm4pIHtcbiAgaWYgKHR5cGVvZiB3aW5kb3cgIT09ICd1bmRlZmluZWQnICYmIHdpbmRvdy5uYXZpZ2F0b3IpIHtcbiAgICByZXR1cm4gISEgLypAX19QVVJFX18qL25hdmlnYXRvci51c2VyQWdlbnQubWF0Y2gocGF0dGVybik7XG4gIH1cbn1cblxudmFyIElFMTFPckxlc3MgPSB1c2VyQWdlbnQoLyg/OlRyaWRlbnQuKnJ2WyA6XT8xMVxcLnxtc2llfGllbW9iaWxlfFdpbmRvd3MgUGhvbmUpL2kpO1xudmFyIEVkZ2UgPSB1c2VyQWdlbnQoL0VkZ2UvaSk7XG52YXIgRmlyZUZveCA9IHVzZXJBZ2VudCgvZmlyZWZveC9pKTtcbnZhciBTYWZhcmkgPSB1c2VyQWdlbnQoL3NhZmFyaS9pKSAmJiAhdXNlckFnZW50KC9jaHJvbWUvaSkgJiYgIXVzZXJBZ2VudCgvYW5kcm9pZC9pKTtcbnZhciBJT1MgPSB1c2VyQWdlbnQoL2lQKGFkfG9kfGhvbmUpL2kpO1xudmFyIENocm9tZUZvckFuZHJvaWQgPSB1c2VyQWdlbnQoL2Nocm9tZS9pKSAmJiB1c2VyQWdlbnQoL2FuZHJvaWQvaSk7XG5cbnZhciBjYXB0dXJlTW9kZSA9IHtcbiAgY2FwdHVyZTogZmFsc2UsXG4gIHBhc3NpdmU6IGZhbHNlXG59O1xuXG5mdW5jdGlvbiBvbihlbCwgZXZlbnQsIGZuKSB7XG4gIGVsLmFkZEV2ZW50TGlzdGVuZXIoZXZlbnQsIGZuLCAhSUUxMU9yTGVzcyAmJiBjYXB0dXJlTW9kZSk7XG59XG5cbmZ1bmN0aW9uIG9mZihlbCwgZXZlbnQsIGZuKSB7XG4gIGVsLnJlbW92ZUV2ZW50TGlzdGVuZXIoZXZlbnQsIGZuLCAhSUUxMU9yTGVzcyAmJiBjYXB0dXJlTW9kZSk7XG59XG5cbmZ1bmN0aW9uIG1hdGNoZXMoXG4vKipIVE1MRWxlbWVudCovXG5lbCxcbi8qKlN0cmluZyovXG5zZWxlY3Rvcikge1xuICBpZiAoIXNlbGVjdG9yKSByZXR1cm47XG4gIHNlbGVjdG9yWzBdID09PSAnPicgJiYgKHNlbGVjdG9yID0gc2VsZWN0b3Iuc3Vic3RyaW5nKDEpKTtcblxuICBpZiAoZWwpIHtcbiAgICB0cnkge1xuICAgICAgaWYgKGVsLm1hdGNoZXMpIHtcbiAgICAgICAgcmV0dXJuIGVsLm1hdGNoZXMoc2VsZWN0b3IpO1xuICAgICAgfSBlbHNlIGlmIChlbC5tc01hdGNoZXNTZWxlY3Rvcikge1xuICAgICAgICByZXR1cm4gZWwubXNNYXRjaGVzU2VsZWN0b3Ioc2VsZWN0b3IpO1xuICAgICAgfSBlbHNlIGlmIChlbC53ZWJraXRNYXRjaGVzU2VsZWN0b3IpIHtcbiAgICAgICAgcmV0dXJuIGVsLndlYmtpdE1hdGNoZXNTZWxlY3RvcihzZWxlY3Rvcik7XG4gICAgICB9XG4gICAgfSBjYXRjaCAoXykge1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBmYWxzZTtcbn1cblxuZnVuY3Rpb24gZ2V0UGFyZW50T3JIb3N0KGVsKSB7XG4gIHJldHVybiBlbC5ob3N0ICYmIGVsICE9PSBkb2N1bWVudCAmJiBlbC5ob3N0Lm5vZGVUeXBlID8gZWwuaG9zdCA6IGVsLnBhcmVudE5vZGU7XG59XG5cbmZ1bmN0aW9uIGNsb3Nlc3QoXG4vKipIVE1MRWxlbWVudCovXG5lbCxcbi8qKlN0cmluZyovXG5zZWxlY3Rvcixcbi8qKkhUTUxFbGVtZW50Ki9cbmN0eCwgaW5jbHVkZUNUWCkge1xuICBpZiAoZWwpIHtcbiAgICBjdHggPSBjdHggfHwgZG9jdW1lbnQ7XG5cbiAgICBkbyB7XG4gICAgICBpZiAoc2VsZWN0b3IgIT0gbnVsbCAmJiAoc2VsZWN0b3JbMF0gPT09ICc+JyA/IGVsLnBhcmVudE5vZGUgPT09IGN0eCAmJiBtYXRjaGVzKGVsLCBzZWxlY3RvcikgOiBtYXRjaGVzKGVsLCBzZWxlY3RvcikpIHx8IGluY2x1ZGVDVFggJiYgZWwgPT09IGN0eCkge1xuICAgICAgICByZXR1cm4gZWw7XG4gICAgICB9XG5cbiAgICAgIGlmIChlbCA9PT0gY3R4KSBicmVhaztcbiAgICAgIC8qIGpzaGludCBib3NzOnRydWUgKi9cbiAgICB9IHdoaWxlIChlbCA9IGdldFBhcmVudE9ySG9zdChlbCkpO1xuICB9XG5cbiAgcmV0dXJuIG51bGw7XG59XG5cbnZhciBSX1NQQUNFID0gL1xccysvZztcblxuZnVuY3Rpb24gdG9nZ2xlQ2xhc3MoZWwsIG5hbWUsIHN0YXRlKSB7XG4gIGlmIChlbCAmJiBuYW1lKSB7XG4gICAgaWYgKGVsLmNsYXNzTGlzdCkge1xuICAgICAgZWwuY2xhc3NMaXN0W3N0YXRlID8gJ2FkZCcgOiAncmVtb3ZlJ10obmFtZSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhciBjbGFzc05hbWUgPSAoJyAnICsgZWwuY2xhc3NOYW1lICsgJyAnKS5yZXBsYWNlKFJfU1BBQ0UsICcgJykucmVwbGFjZSgnICcgKyBuYW1lICsgJyAnLCAnICcpO1xuICAgICAgZWwuY2xhc3NOYW1lID0gKGNsYXNzTmFtZSArIChzdGF0ZSA/ICcgJyArIG5hbWUgOiAnJykpLnJlcGxhY2UoUl9TUEFDRSwgJyAnKTtcbiAgICB9XG4gIH1cbn1cblxuZnVuY3Rpb24gY3NzKGVsLCBwcm9wLCB2YWwpIHtcbiAgdmFyIHN0eWxlID0gZWwgJiYgZWwuc3R5bGU7XG5cbiAgaWYgKHN0eWxlKSB7XG4gICAgaWYgKHZhbCA9PT0gdm9pZCAwKSB7XG4gICAgICBpZiAoZG9jdW1lbnQuZGVmYXVsdFZpZXcgJiYgZG9jdW1lbnQuZGVmYXVsdFZpZXcuZ2V0Q29tcHV0ZWRTdHlsZSkge1xuICAgICAgICB2YWwgPSBkb2N1bWVudC5kZWZhdWx0Vmlldy5nZXRDb21wdXRlZFN0eWxlKGVsLCAnJyk7XG4gICAgICB9IGVsc2UgaWYgKGVsLmN1cnJlbnRTdHlsZSkge1xuICAgICAgICB2YWwgPSBlbC5jdXJyZW50U3R5bGU7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiBwcm9wID09PSB2b2lkIDAgPyB2YWwgOiB2YWxbcHJvcF07XG4gICAgfSBlbHNlIHtcbiAgICAgIGlmICghKHByb3AgaW4gc3R5bGUpICYmIHByb3AuaW5kZXhPZignd2Via2l0JykgPT09IC0xKSB7XG4gICAgICAgIHByb3AgPSAnLXdlYmtpdC0nICsgcHJvcDtcbiAgICAgIH1cblxuICAgICAgc3R5bGVbcHJvcF0gPSB2YWwgKyAodHlwZW9mIHZhbCA9PT0gJ3N0cmluZycgPyAnJyA6ICdweCcpO1xuICAgIH1cbiAgfVxufVxuXG5mdW5jdGlvbiBtYXRyaXgoZWwsIHNlbGZPbmx5KSB7XG4gIHZhciBhcHBsaWVkVHJhbnNmb3JtcyA9ICcnO1xuXG4gIGlmICh0eXBlb2YgZWwgPT09ICdzdHJpbmcnKSB7XG4gICAgYXBwbGllZFRyYW5zZm9ybXMgPSBlbDtcbiAgfSBlbHNlIHtcbiAgICBkbyB7XG4gICAgICB2YXIgdHJhbnNmb3JtID0gY3NzKGVsLCAndHJhbnNmb3JtJyk7XG5cbiAgICAgIGlmICh0cmFuc2Zvcm0gJiYgdHJhbnNmb3JtICE9PSAnbm9uZScpIHtcbiAgICAgICAgYXBwbGllZFRyYW5zZm9ybXMgPSB0cmFuc2Zvcm0gKyAnICcgKyBhcHBsaWVkVHJhbnNmb3JtcztcbiAgICAgIH1cbiAgICAgIC8qIGpzaGludCBib3NzOnRydWUgKi9cblxuICAgIH0gd2hpbGUgKCFzZWxmT25seSAmJiAoZWwgPSBlbC5wYXJlbnROb2RlKSk7XG4gIH1cblxuICB2YXIgbWF0cml4Rm4gPSB3aW5kb3cuRE9NTWF0cml4IHx8IHdpbmRvdy5XZWJLaXRDU1NNYXRyaXggfHwgd2luZG93LkNTU01hdHJpeCB8fCB3aW5kb3cuTVNDU1NNYXRyaXg7XG4gIC8qanNoaW50IC1XMDU2ICovXG5cbiAgcmV0dXJuIG1hdHJpeEZuICYmIG5ldyBtYXRyaXhGbihhcHBsaWVkVHJhbnNmb3Jtcyk7XG59XG5cbmZ1bmN0aW9uIGZpbmQoY3R4LCB0YWdOYW1lLCBpdGVyYXRvcikge1xuICBpZiAoY3R4KSB7XG4gICAgdmFyIGxpc3QgPSBjdHguZ2V0RWxlbWVudHNCeVRhZ05hbWUodGFnTmFtZSksXG4gICAgICAgIGkgPSAwLFxuICAgICAgICBuID0gbGlzdC5sZW5ndGg7XG5cbiAgICBpZiAoaXRlcmF0b3IpIHtcbiAgICAgIGZvciAoOyBpIDwgbjsgaSsrKSB7XG4gICAgICAgIGl0ZXJhdG9yKGxpc3RbaV0sIGkpO1xuICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBsaXN0O1xuICB9XG5cbiAgcmV0dXJuIFtdO1xufVxuXG5mdW5jdGlvbiBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCkge1xuICB2YXIgc2Nyb2xsaW5nRWxlbWVudCA9IGRvY3VtZW50LnNjcm9sbGluZ0VsZW1lbnQ7XG5cbiAgaWYgKHNjcm9sbGluZ0VsZW1lbnQpIHtcbiAgICByZXR1cm4gc2Nyb2xsaW5nRWxlbWVudDtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50O1xuICB9XG59XG4vKipcbiAqIFJldHVybnMgdGhlIFwiYm91bmRpbmcgY2xpZW50IHJlY3RcIiBvZiBnaXZlbiBlbGVtZW50XG4gKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gZWwgICAgICAgICAgICAgICAgICAgICAgIFRoZSBlbGVtZW50IHdob3NlIGJvdW5kaW5nQ2xpZW50UmVjdCBpcyB3YW50ZWRcbiAqIEBwYXJhbSAge1tCb29sZWFuXX0gcmVsYXRpdmVUb0NvbnRhaW5pbmdCbG9jayAgV2hldGhlciB0aGUgcmVjdCBzaG91bGQgYmUgcmVsYXRpdmUgdG8gdGhlIGNvbnRhaW5pbmcgYmxvY2sgb2YgKGluY2x1ZGluZykgdGhlIGNvbnRhaW5lclxuICogQHBhcmFtICB7W0Jvb2xlYW5dfSByZWxhdGl2ZVRvTm9uU3RhdGljUGFyZW50ICBXaGV0aGVyIHRoZSByZWN0IHNob3VsZCBiZSByZWxhdGl2ZSB0byB0aGUgcmVsYXRpdmUgcGFyZW50IG9mIChpbmNsdWRpbmcpIHRoZSBjb250YWllbnJcbiAqIEBwYXJhbSAge1tCb29sZWFuXX0gdW5kb1NjYWxlICAgICAgICAgICAgICAgICAgV2hldGhlciB0aGUgY29udGFpbmVyJ3Mgc2NhbGUoKSBzaG91bGQgYmUgdW5kb25lXG4gKiBAcGFyYW0gIHtbSFRNTEVsZW1lbnRdfSBjb250YWluZXIgICAgICAgICAgICAgIFRoZSBwYXJlbnQgdGhlIGVsZW1lbnQgd2lsbCBiZSBwbGFjZWQgaW5cbiAqIEByZXR1cm4ge09iamVjdH0gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgVGhlIGJvdW5kaW5nQ2xpZW50UmVjdCBvZiBlbCwgd2l0aCBzcGVjaWZpZWQgYWRqdXN0bWVudHNcbiAqL1xuXG5cbmZ1bmN0aW9uIGdldFJlY3QoZWwsIHJlbGF0aXZlVG9Db250YWluaW5nQmxvY2ssIHJlbGF0aXZlVG9Ob25TdGF0aWNQYXJlbnQsIHVuZG9TY2FsZSwgY29udGFpbmVyKSB7XG4gIGlmICghZWwuZ2V0Qm91bmRpbmdDbGllbnRSZWN0ICYmIGVsICE9PSB3aW5kb3cpIHJldHVybjtcbiAgdmFyIGVsUmVjdCwgdG9wLCBsZWZ0LCBib3R0b20sIHJpZ2h0LCBoZWlnaHQsIHdpZHRoO1xuXG4gIGlmIChlbCAhPT0gd2luZG93ICYmIGVsLnBhcmVudE5vZGUgJiYgZWwgIT09IGdldFdpbmRvd1Njcm9sbGluZ0VsZW1lbnQoKSkge1xuICAgIGVsUmVjdCA9IGVsLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgIHRvcCA9IGVsUmVjdC50b3A7XG4gICAgbGVmdCA9IGVsUmVjdC5sZWZ0O1xuICAgIGJvdHRvbSA9IGVsUmVjdC5ib3R0b207XG4gICAgcmlnaHQgPSBlbFJlY3QucmlnaHQ7XG4gICAgaGVpZ2h0ID0gZWxSZWN0LmhlaWdodDtcbiAgICB3aWR0aCA9IGVsUmVjdC53aWR0aDtcbiAgfSBlbHNlIHtcbiAgICB0b3AgPSAwO1xuICAgIGxlZnQgPSAwO1xuICAgIGJvdHRvbSA9IHdpbmRvdy5pbm5lckhlaWdodDtcbiAgICByaWdodCA9IHdpbmRvdy5pbm5lcldpZHRoO1xuICAgIGhlaWdodCA9IHdpbmRvdy5pbm5lckhlaWdodDtcbiAgICB3aWR0aCA9IHdpbmRvdy5pbm5lcldpZHRoO1xuICB9XG5cbiAgaWYgKChyZWxhdGl2ZVRvQ29udGFpbmluZ0Jsb2NrIHx8IHJlbGF0aXZlVG9Ob25TdGF0aWNQYXJlbnQpICYmIGVsICE9PSB3aW5kb3cpIHtcbiAgICAvLyBBZGp1c3QgZm9yIHRyYW5zbGF0ZSgpXG4gICAgY29udGFpbmVyID0gY29udGFpbmVyIHx8IGVsLnBhcmVudE5vZGU7IC8vIHNvbHZlcyAjMTEyMyAoc2VlOiBodHRwczovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMzc5NTM4MDYvNjA4ODMxMilcbiAgICAvLyBOb3QgbmVlZGVkIG9uIDw9IElFMTFcblxuICAgIGlmICghSUUxMU9yTGVzcykge1xuICAgICAgZG8ge1xuICAgICAgICBpZiAoY29udGFpbmVyICYmIGNvbnRhaW5lci5nZXRCb3VuZGluZ0NsaWVudFJlY3QgJiYgKGNzcyhjb250YWluZXIsICd0cmFuc2Zvcm0nKSAhPT0gJ25vbmUnIHx8IHJlbGF0aXZlVG9Ob25TdGF0aWNQYXJlbnQgJiYgY3NzKGNvbnRhaW5lciwgJ3Bvc2l0aW9uJykgIT09ICdzdGF0aWMnKSkge1xuICAgICAgICAgIHZhciBjb250YWluZXJSZWN0ID0gY29udGFpbmVyLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpOyAvLyBTZXQgcmVsYXRpdmUgdG8gZWRnZXMgb2YgcGFkZGluZyBib3ggb2YgY29udGFpbmVyXG5cbiAgICAgICAgICB0b3AgLT0gY29udGFpbmVyUmVjdC50b3AgKyBwYXJzZUludChjc3MoY29udGFpbmVyLCAnYm9yZGVyLXRvcC13aWR0aCcpKTtcbiAgICAgICAgICBsZWZ0IC09IGNvbnRhaW5lclJlY3QubGVmdCArIHBhcnNlSW50KGNzcyhjb250YWluZXIsICdib3JkZXItbGVmdC13aWR0aCcpKTtcbiAgICAgICAgICBib3R0b20gPSB0b3AgKyBlbFJlY3QuaGVpZ2h0O1xuICAgICAgICAgIHJpZ2h0ID0gbGVmdCArIGVsUmVjdC53aWR0aDtcbiAgICAgICAgICBicmVhaztcbiAgICAgICAgfVxuICAgICAgICAvKiBqc2hpbnQgYm9zczp0cnVlICovXG5cbiAgICAgIH0gd2hpbGUgKGNvbnRhaW5lciA9IGNvbnRhaW5lci5wYXJlbnROb2RlKTtcbiAgICB9XG4gIH1cblxuICBpZiAodW5kb1NjYWxlICYmIGVsICE9PSB3aW5kb3cpIHtcbiAgICAvLyBBZGp1c3QgZm9yIHNjYWxlKClcbiAgICB2YXIgZWxNYXRyaXggPSBtYXRyaXgoY29udGFpbmVyIHx8IGVsKSxcbiAgICAgICAgc2NhbGVYID0gZWxNYXRyaXggJiYgZWxNYXRyaXguYSxcbiAgICAgICAgc2NhbGVZID0gZWxNYXRyaXggJiYgZWxNYXRyaXguZDtcblxuICAgIGlmIChlbE1hdHJpeCkge1xuICAgICAgdG9wIC89IHNjYWxlWTtcbiAgICAgIGxlZnQgLz0gc2NhbGVYO1xuICAgICAgd2lkdGggLz0gc2NhbGVYO1xuICAgICAgaGVpZ2h0IC89IHNjYWxlWTtcbiAgICAgIGJvdHRvbSA9IHRvcCArIGhlaWdodDtcbiAgICAgIHJpZ2h0ID0gbGVmdCArIHdpZHRoO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiB7XG4gICAgdG9wOiB0b3AsXG4gICAgbGVmdDogbGVmdCxcbiAgICBib3R0b206IGJvdHRvbSxcbiAgICByaWdodDogcmlnaHQsXG4gICAgd2lkdGg6IHdpZHRoLFxuICAgIGhlaWdodDogaGVpZ2h0XG4gIH07XG59XG4vKipcbiAqIENoZWNrcyBpZiBhIHNpZGUgb2YgYW4gZWxlbWVudCBpcyBzY3JvbGxlZCBwYXN0IGEgc2lkZSBvZiBpdHMgcGFyZW50c1xuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9ICBlbCAgICAgICAgICAgVGhlIGVsZW1lbnQgd2hvJ3Mgc2lkZSBiZWluZyBzY3JvbGxlZCBvdXQgb2YgdmlldyBpcyBpbiBxdWVzdGlvblxuICogQHBhcmFtICB7U3RyaW5nfSAgICAgICBlbFNpZGUgICAgICAgU2lkZSBvZiB0aGUgZWxlbWVudCBpbiBxdWVzdGlvbiAoJ3RvcCcsICdsZWZ0JywgJ3JpZ2h0JywgJ2JvdHRvbScpXG4gKiBAcGFyYW0gIHtTdHJpbmd9ICAgICAgIHBhcmVudFNpZGUgICBTaWRlIG9mIHRoZSBwYXJlbnQgaW4gcXVlc3Rpb24gKCd0b3AnLCAnbGVmdCcsICdyaWdodCcsICdib3R0b20nKVxuICogQHJldHVybiB7SFRNTEVsZW1lbnR9ICAgICAgICAgICAgICAgVGhlIHBhcmVudCBzY3JvbGwgZWxlbWVudCB0aGF0IHRoZSBlbCdzIHNpZGUgaXMgc2Nyb2xsZWQgcGFzdCwgb3IgbnVsbCBpZiB0aGVyZSBpcyBubyBzdWNoIGVsZW1lbnRcbiAqL1xuXG5cbmZ1bmN0aW9uIGlzU2Nyb2xsZWRQYXN0KGVsLCBlbFNpZGUsIHBhcmVudFNpZGUpIHtcbiAgdmFyIHBhcmVudCA9IGdldFBhcmVudEF1dG9TY3JvbGxFbGVtZW50KGVsLCB0cnVlKSxcbiAgICAgIGVsU2lkZVZhbCA9IGdldFJlY3QoZWwpW2VsU2lkZV07XG4gIC8qIGpzaGludCBib3NzOnRydWUgKi9cblxuICB3aGlsZSAocGFyZW50KSB7XG4gICAgdmFyIHBhcmVudFNpZGVWYWwgPSBnZXRSZWN0KHBhcmVudClbcGFyZW50U2lkZV0sXG4gICAgICAgIHZpc2libGUgPSB2b2lkIDA7XG5cbiAgICBpZiAocGFyZW50U2lkZSA9PT0gJ3RvcCcgfHwgcGFyZW50U2lkZSA9PT0gJ2xlZnQnKSB7XG4gICAgICB2aXNpYmxlID0gZWxTaWRlVmFsID49IHBhcmVudFNpZGVWYWw7XG4gICAgfSBlbHNlIHtcbiAgICAgIHZpc2libGUgPSBlbFNpZGVWYWwgPD0gcGFyZW50U2lkZVZhbDtcbiAgICB9XG5cbiAgICBpZiAoIXZpc2libGUpIHJldHVybiBwYXJlbnQ7XG4gICAgaWYgKHBhcmVudCA9PT0gZ2V0V2luZG93U2Nyb2xsaW5nRWxlbWVudCgpKSBicmVhaztcbiAgICBwYXJlbnQgPSBnZXRQYXJlbnRBdXRvU2Nyb2xsRWxlbWVudChwYXJlbnQsIGZhbHNlKTtcbiAgfVxuXG4gIHJldHVybiBmYWxzZTtcbn1cbi8qKlxuICogR2V0cyBudGggY2hpbGQgb2YgZWwsIGlnbm9yaW5nIGhpZGRlbiBjaGlsZHJlbiwgc29ydGFibGUncyBlbGVtZW50cyAoZG9lcyBub3QgaWdub3JlIGNsb25lIGlmIGl0J3MgdmlzaWJsZSlcbiAqIGFuZCBub24tZHJhZ2dhYmxlIGVsZW1lbnRzXG4gKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gZWwgICAgICAgVGhlIHBhcmVudCBlbGVtZW50XG4gKiBAcGFyYW0gIHtOdW1iZXJ9IGNoaWxkTnVtICAgICAgVGhlIGluZGV4IG9mIHRoZSBjaGlsZFxuICogQHBhcmFtICB7T2JqZWN0fSBvcHRpb25zICAgICAgIFBhcmVudCBTb3J0YWJsZSdzIG9wdGlvbnNcbiAqIEByZXR1cm4ge0hUTUxFbGVtZW50fSAgICAgICAgICBUaGUgY2hpbGQgYXQgaW5kZXggY2hpbGROdW0sIG9yIG51bGwgaWYgbm90IGZvdW5kXG4gKi9cblxuXG5mdW5jdGlvbiBnZXRDaGlsZChlbCwgY2hpbGROdW0sIG9wdGlvbnMsIGluY2x1ZGVEcmFnRWwpIHtcbiAgdmFyIGN1cnJlbnRDaGlsZCA9IDAsXG4gICAgICBpID0gMCxcbiAgICAgIGNoaWxkcmVuID0gZWwuY2hpbGRyZW47XG5cbiAgd2hpbGUgKGkgPCBjaGlsZHJlbi5sZW5ndGgpIHtcbiAgICBpZiAoY2hpbGRyZW5baV0uc3R5bGUuZGlzcGxheSAhPT0gJ25vbmUnICYmIGNoaWxkcmVuW2ldICE9PSBTb3J0YWJsZS5naG9zdCAmJiAoaW5jbHVkZURyYWdFbCB8fCBjaGlsZHJlbltpXSAhPT0gU29ydGFibGUuZHJhZ2dlZCkgJiYgY2xvc2VzdChjaGlsZHJlbltpXSwgb3B0aW9ucy5kcmFnZ2FibGUsIGVsLCBmYWxzZSkpIHtcbiAgICAgIGlmIChjdXJyZW50Q2hpbGQgPT09IGNoaWxkTnVtKSB7XG4gICAgICAgIHJldHVybiBjaGlsZHJlbltpXTtcbiAgICAgIH1cblxuICAgICAgY3VycmVudENoaWxkKys7XG4gICAgfVxuXG4gICAgaSsrO1xuICB9XG5cbiAgcmV0dXJuIG51bGw7XG59XG4vKipcbiAqIEdldHMgdGhlIGxhc3QgY2hpbGQgaW4gdGhlIGVsLCBpZ25vcmluZyBnaG9zdEVsIG9yIGludmlzaWJsZSBlbGVtZW50cyAoY2xvbmVzKVxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IGVsICAgICAgIFBhcmVudCBlbGVtZW50XG4gKiBAcGFyYW0gIHtzZWxlY3Rvcn0gc2VsZWN0b3IgICAgQW55IG90aGVyIGVsZW1lbnRzIHRoYXQgc2hvdWxkIGJlIGlnbm9yZWRcbiAqIEByZXR1cm4ge0hUTUxFbGVtZW50fSAgICAgICAgICBUaGUgbGFzdCBjaGlsZCwgaWdub3JpbmcgZ2hvc3RFbFxuICovXG5cblxuZnVuY3Rpb24gbGFzdENoaWxkKGVsLCBzZWxlY3Rvcikge1xuICB2YXIgbGFzdCA9IGVsLmxhc3RFbGVtZW50Q2hpbGQ7XG5cbiAgd2hpbGUgKGxhc3QgJiYgKGxhc3QgPT09IFNvcnRhYmxlLmdob3N0IHx8IGNzcyhsYXN0LCAnZGlzcGxheScpID09PSAnbm9uZScgfHwgc2VsZWN0b3IgJiYgIW1hdGNoZXMobGFzdCwgc2VsZWN0b3IpKSkge1xuICAgIGxhc3QgPSBsYXN0LnByZXZpb3VzRWxlbWVudFNpYmxpbmc7XG4gIH1cblxuICByZXR1cm4gbGFzdCB8fCBudWxsO1xufVxuLyoqXG4gKiBSZXR1cm5zIHRoZSBpbmRleCBvZiBhbiBlbGVtZW50IHdpdGhpbiBpdHMgcGFyZW50IGZvciBhIHNlbGVjdGVkIHNldCBvZlxuICogZWxlbWVudHNcbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSBlbFxuICogQHBhcmFtICB7c2VsZWN0b3J9IHNlbGVjdG9yXG4gKiBAcmV0dXJuIHtudW1iZXJ9XG4gKi9cblxuXG5mdW5jdGlvbiBpbmRleChlbCwgc2VsZWN0b3IpIHtcbiAgdmFyIGluZGV4ID0gMDtcblxuICBpZiAoIWVsIHx8ICFlbC5wYXJlbnROb2RlKSB7XG4gICAgcmV0dXJuIC0xO1xuICB9XG4gIC8qIGpzaGludCBib3NzOnRydWUgKi9cblxuXG4gIHdoaWxlIChlbCA9IGVsLnByZXZpb3VzRWxlbWVudFNpYmxpbmcpIHtcbiAgICBpZiAoZWwubm9kZU5hbWUudG9VcHBlckNhc2UoKSAhPT0gJ1RFTVBMQVRFJyAmJiBlbCAhPT0gU29ydGFibGUuY2xvbmUgJiYgKCFzZWxlY3RvciB8fCBtYXRjaGVzKGVsLCBzZWxlY3RvcikpKSB7XG4gICAgICBpbmRleCsrO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBpbmRleDtcbn1cbi8qKlxuICogUmV0dXJucyB0aGUgc2Nyb2xsIG9mZnNldCBvZiB0aGUgZ2l2ZW4gZWxlbWVudCwgYWRkZWQgd2l0aCBhbGwgdGhlIHNjcm9sbCBvZmZzZXRzIG9mIHBhcmVudCBlbGVtZW50cy5cbiAqIFRoZSB2YWx1ZSBpcyByZXR1cm5lZCBpbiByZWFsIHBpeGVscy5cbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSBlbFxuICogQHJldHVybiB7QXJyYXl9ICAgICAgICAgICAgIE9mZnNldHMgaW4gdGhlIGZvcm1hdCBvZiBbbGVmdCwgdG9wXVxuICovXG5cblxuZnVuY3Rpb24gZ2V0UmVsYXRpdmVTY3JvbGxPZmZzZXQoZWwpIHtcbiAgdmFyIG9mZnNldExlZnQgPSAwLFxuICAgICAgb2Zmc2V0VG9wID0gMCxcbiAgICAgIHdpblNjcm9sbGVyID0gZ2V0V2luZG93U2Nyb2xsaW5nRWxlbWVudCgpO1xuXG4gIGlmIChlbCkge1xuICAgIGRvIHtcbiAgICAgIHZhciBlbE1hdHJpeCA9IG1hdHJpeChlbCksXG4gICAgICAgICAgc2NhbGVYID0gZWxNYXRyaXguYSxcbiAgICAgICAgICBzY2FsZVkgPSBlbE1hdHJpeC5kO1xuICAgICAgb2Zmc2V0TGVmdCArPSBlbC5zY3JvbGxMZWZ0ICogc2NhbGVYO1xuICAgICAgb2Zmc2V0VG9wICs9IGVsLnNjcm9sbFRvcCAqIHNjYWxlWTtcbiAgICB9IHdoaWxlIChlbCAhPT0gd2luU2Nyb2xsZXIgJiYgKGVsID0gZWwucGFyZW50Tm9kZSkpO1xuICB9XG5cbiAgcmV0dXJuIFtvZmZzZXRMZWZ0LCBvZmZzZXRUb3BdO1xufVxuLyoqXG4gKiBSZXR1cm5zIHRoZSBpbmRleCBvZiB0aGUgb2JqZWN0IHdpdGhpbiB0aGUgZ2l2ZW4gYXJyYXlcbiAqIEBwYXJhbSAge0FycmF5fSBhcnIgICBBcnJheSB0aGF0IG1heSBvciBtYXkgbm90IGhvbGQgdGhlIG9iamVjdFxuICogQHBhcmFtICB7T2JqZWN0fSBvYmogIEFuIG9iamVjdCB0aGF0IGhhcyBhIGtleS12YWx1ZSBwYWlyIHVuaXF1ZSB0byBhbmQgaWRlbnRpY2FsIHRvIGEga2V5LXZhbHVlIHBhaXIgaW4gdGhlIG9iamVjdCB5b3Ugd2FudCB0byBmaW5kXG4gKiBAcmV0dXJuIHtOdW1iZXJ9ICAgICAgVGhlIGluZGV4IG9mIHRoZSBvYmplY3QgaW4gdGhlIGFycmF5LCBvciAtMVxuICovXG5cblxuZnVuY3Rpb24gaW5kZXhPZk9iamVjdChhcnIsIG9iaikge1xuICBmb3IgKHZhciBpIGluIGFycikge1xuICAgIGlmICghYXJyLmhhc093blByb3BlcnR5KGkpKSBjb250aW51ZTtcblxuICAgIGZvciAodmFyIGtleSBpbiBvYmopIHtcbiAgICAgIGlmIChvYmouaGFzT3duUHJvcGVydHkoa2V5KSAmJiBvYmpba2V5XSA9PT0gYXJyW2ldW2tleV0pIHJldHVybiBOdW1iZXIoaSk7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIC0xO1xufVxuXG5mdW5jdGlvbiBnZXRQYXJlbnRBdXRvU2Nyb2xsRWxlbWVudChlbCwgaW5jbHVkZVNlbGYpIHtcbiAgLy8gc2tpcCB0byB3aW5kb3dcbiAgaWYgKCFlbCB8fCAhZWwuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KSByZXR1cm4gZ2V0V2luZG93U2Nyb2xsaW5nRWxlbWVudCgpO1xuICB2YXIgZWxlbSA9IGVsO1xuICB2YXIgZ290U2VsZiA9IGZhbHNlO1xuXG4gIGRvIHtcbiAgICAvLyB3ZSBkb24ndCBuZWVkIHRvIGdldCBlbGVtIGNzcyBpZiBpdCBpc24ndCBldmVuIG92ZXJmbG93aW5nIGluIHRoZSBmaXJzdCBwbGFjZSAocGVyZm9ybWFuY2UpXG4gICAgaWYgKGVsZW0uY2xpZW50V2lkdGggPCBlbGVtLnNjcm9sbFdpZHRoIHx8IGVsZW0uY2xpZW50SGVpZ2h0IDwgZWxlbS5zY3JvbGxIZWlnaHQpIHtcbiAgICAgIHZhciBlbGVtQ1NTID0gY3NzKGVsZW0pO1xuXG4gICAgICBpZiAoZWxlbS5jbGllbnRXaWR0aCA8IGVsZW0uc2Nyb2xsV2lkdGggJiYgKGVsZW1DU1Mub3ZlcmZsb3dYID09ICdhdXRvJyB8fCBlbGVtQ1NTLm92ZXJmbG93WCA9PSAnc2Nyb2xsJykgfHwgZWxlbS5jbGllbnRIZWlnaHQgPCBlbGVtLnNjcm9sbEhlaWdodCAmJiAoZWxlbUNTUy5vdmVyZmxvd1kgPT0gJ2F1dG8nIHx8IGVsZW1DU1Mub3ZlcmZsb3dZID09ICdzY3JvbGwnKSkge1xuICAgICAgICBpZiAoIWVsZW0uZ2V0Qm91bmRpbmdDbGllbnRSZWN0IHx8IGVsZW0gPT09IGRvY3VtZW50LmJvZHkpIHJldHVybiBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCk7XG4gICAgICAgIGlmIChnb3RTZWxmIHx8IGluY2x1ZGVTZWxmKSByZXR1cm4gZWxlbTtcbiAgICAgICAgZ290U2VsZiA9IHRydWU7XG4gICAgICB9XG4gICAgfVxuICAgIC8qIGpzaGludCBib3NzOnRydWUgKi9cblxuICB9IHdoaWxlIChlbGVtID0gZWxlbS5wYXJlbnROb2RlKTtcblxuICByZXR1cm4gZ2V0V2luZG93U2Nyb2xsaW5nRWxlbWVudCgpO1xufVxuXG5mdW5jdGlvbiBleHRlbmQoZHN0LCBzcmMpIHtcbiAgaWYgKGRzdCAmJiBzcmMpIHtcbiAgICBmb3IgKHZhciBrZXkgaW4gc3JjKSB7XG4gICAgICBpZiAoc3JjLmhhc093blByb3BlcnR5KGtleSkpIHtcbiAgICAgICAgZHN0W2tleV0gPSBzcmNba2V5XTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICByZXR1cm4gZHN0O1xufVxuXG5mdW5jdGlvbiBpc1JlY3RFcXVhbChyZWN0MSwgcmVjdDIpIHtcbiAgcmV0dXJuIE1hdGgucm91bmQocmVjdDEudG9wKSA9PT0gTWF0aC5yb3VuZChyZWN0Mi50b3ApICYmIE1hdGgucm91bmQocmVjdDEubGVmdCkgPT09IE1hdGgucm91bmQocmVjdDIubGVmdCkgJiYgTWF0aC5yb3VuZChyZWN0MS5oZWlnaHQpID09PSBNYXRoLnJvdW5kKHJlY3QyLmhlaWdodCkgJiYgTWF0aC5yb3VuZChyZWN0MS53aWR0aCkgPT09IE1hdGgucm91bmQocmVjdDIud2lkdGgpO1xufVxuXG52YXIgX3Rocm90dGxlVGltZW91dDtcblxuZnVuY3Rpb24gdGhyb3R0bGUoY2FsbGJhY2ssIG1zKSB7XG4gIHJldHVybiBmdW5jdGlvbiAoKSB7XG4gICAgaWYgKCFfdGhyb3R0bGVUaW1lb3V0KSB7XG4gICAgICB2YXIgYXJncyA9IGFyZ3VtZW50cyxcbiAgICAgICAgICBfdGhpcyA9IHRoaXM7XG5cbiAgICAgIGlmIChhcmdzLmxlbmd0aCA9PT0gMSkge1xuICAgICAgICBjYWxsYmFjay5jYWxsKF90aGlzLCBhcmdzWzBdKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNhbGxiYWNrLmFwcGx5KF90aGlzLCBhcmdzKTtcbiAgICAgIH1cblxuICAgICAgX3Rocm90dGxlVGltZW91dCA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICBfdGhyb3R0bGVUaW1lb3V0ID0gdm9pZCAwO1xuICAgICAgfSwgbXMpO1xuICAgIH1cbiAgfTtcbn1cblxuZnVuY3Rpb24gY2FuY2VsVGhyb3R0bGUoKSB7XG4gIGNsZWFyVGltZW91dChfdGhyb3R0bGVUaW1lb3V0KTtcbiAgX3Rocm90dGxlVGltZW91dCA9IHZvaWQgMDtcbn1cblxuZnVuY3Rpb24gc2Nyb2xsQnkoZWwsIHgsIHkpIHtcbiAgZWwuc2Nyb2xsTGVmdCArPSB4O1xuICBlbC5zY3JvbGxUb3AgKz0geTtcbn1cblxuZnVuY3Rpb24gY2xvbmUoZWwpIHtcbiAgdmFyIFBvbHltZXIgPSB3aW5kb3cuUG9seW1lcjtcbiAgdmFyICQgPSB3aW5kb3cualF1ZXJ5IHx8IHdpbmRvdy5aZXB0bztcblxuICBpZiAoUG9seW1lciAmJiBQb2x5bWVyLmRvbSkge1xuICAgIHJldHVybiBQb2x5bWVyLmRvbShlbCkuY2xvbmVOb2RlKHRydWUpO1xuICB9IGVsc2UgaWYgKCQpIHtcbiAgICByZXR1cm4gJChlbCkuY2xvbmUodHJ1ZSlbMF07XG4gIH0gZWxzZSB7XG4gICAgcmV0dXJuIGVsLmNsb25lTm9kZSh0cnVlKTtcbiAgfVxufVxuXG5mdW5jdGlvbiBzZXRSZWN0KGVsLCByZWN0KSB7XG4gIGNzcyhlbCwgJ3Bvc2l0aW9uJywgJ2Fic29sdXRlJyk7XG4gIGNzcyhlbCwgJ3RvcCcsIHJlY3QudG9wKTtcbiAgY3NzKGVsLCAnbGVmdCcsIHJlY3QubGVmdCk7XG4gIGNzcyhlbCwgJ3dpZHRoJywgcmVjdC53aWR0aCk7XG4gIGNzcyhlbCwgJ2hlaWdodCcsIHJlY3QuaGVpZ2h0KTtcbn1cblxuZnVuY3Rpb24gdW5zZXRSZWN0KGVsKSB7XG4gIGNzcyhlbCwgJ3Bvc2l0aW9uJywgJycpO1xuICBjc3MoZWwsICd0b3AnLCAnJyk7XG4gIGNzcyhlbCwgJ2xlZnQnLCAnJyk7XG4gIGNzcyhlbCwgJ3dpZHRoJywgJycpO1xuICBjc3MoZWwsICdoZWlnaHQnLCAnJyk7XG59XG5cbnZhciBleHBhbmRvID0gJ1NvcnRhYmxlJyArIG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xuXG5mdW5jdGlvbiBBbmltYXRpb25TdGF0ZU1hbmFnZXIoKSB7XG4gIHZhciBhbmltYXRpb25TdGF0ZXMgPSBbXSxcbiAgICAgIGFuaW1hdGlvbkNhbGxiYWNrSWQ7XG4gIHJldHVybiB7XG4gICAgY2FwdHVyZUFuaW1hdGlvblN0YXRlOiBmdW5jdGlvbiBjYXB0dXJlQW5pbWF0aW9uU3RhdGUoKSB7XG4gICAgICBhbmltYXRpb25TdGF0ZXMgPSBbXTtcbiAgICAgIGlmICghdGhpcy5vcHRpb25zLmFuaW1hdGlvbikgcmV0dXJuO1xuICAgICAgdmFyIGNoaWxkcmVuID0gW10uc2xpY2UuY2FsbCh0aGlzLmVsLmNoaWxkcmVuKTtcbiAgICAgIGNoaWxkcmVuLmZvckVhY2goZnVuY3Rpb24gKGNoaWxkKSB7XG4gICAgICAgIGlmIChjc3MoY2hpbGQsICdkaXNwbGF5JykgPT09ICdub25lJyB8fCBjaGlsZCA9PT0gU29ydGFibGUuZ2hvc3QpIHJldHVybjtcbiAgICAgICAgYW5pbWF0aW9uU3RhdGVzLnB1c2goe1xuICAgICAgICAgIHRhcmdldDogY2hpbGQsXG4gICAgICAgICAgcmVjdDogZ2V0UmVjdChjaGlsZClcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdmFyIGZyb21SZWN0ID0gX29iamVjdFNwcmVhZDIoe30sIGFuaW1hdGlvblN0YXRlc1thbmltYXRpb25TdGF0ZXMubGVuZ3RoIC0gMV0ucmVjdCk7IC8vIElmIGFuaW1hdGluZzogY29tcGVuc2F0ZSBmb3IgY3VycmVudCBhbmltYXRpb25cblxuXG4gICAgICAgIGlmIChjaGlsZC50aGlzQW5pbWF0aW9uRHVyYXRpb24pIHtcbiAgICAgICAgICB2YXIgY2hpbGRNYXRyaXggPSBtYXRyaXgoY2hpbGQsIHRydWUpO1xuXG4gICAgICAgICAgaWYgKGNoaWxkTWF0cml4KSB7XG4gICAgICAgICAgICBmcm9tUmVjdC50b3AgLT0gY2hpbGRNYXRyaXguZjtcbiAgICAgICAgICAgIGZyb21SZWN0LmxlZnQgLT0gY2hpbGRNYXRyaXguZTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBjaGlsZC5mcm9tUmVjdCA9IGZyb21SZWN0O1xuICAgICAgfSk7XG4gICAgfSxcbiAgICBhZGRBbmltYXRpb25TdGF0ZTogZnVuY3Rpb24gYWRkQW5pbWF0aW9uU3RhdGUoc3RhdGUpIHtcbiAgICAgIGFuaW1hdGlvblN0YXRlcy5wdXNoKHN0YXRlKTtcbiAgICB9LFxuICAgIHJlbW92ZUFuaW1hdGlvblN0YXRlOiBmdW5jdGlvbiByZW1vdmVBbmltYXRpb25TdGF0ZSh0YXJnZXQpIHtcbiAgICAgIGFuaW1hdGlvblN0YXRlcy5zcGxpY2UoaW5kZXhPZk9iamVjdChhbmltYXRpb25TdGF0ZXMsIHtcbiAgICAgICAgdGFyZ2V0OiB0YXJnZXRcbiAgICAgIH0pLCAxKTtcbiAgICB9LFxuICAgIGFuaW1hdGVBbGw6IGZ1bmN0aW9uIGFuaW1hdGVBbGwoY2FsbGJhY2spIHtcbiAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG5cbiAgICAgIGlmICghdGhpcy5vcHRpb25zLmFuaW1hdGlvbikge1xuICAgICAgICBjbGVhclRpbWVvdXQoYW5pbWF0aW9uQ2FsbGJhY2tJZCk7XG4gICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICdmdW5jdGlvbicpIGNhbGxiYWNrKCk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgdmFyIGFuaW1hdGluZyA9IGZhbHNlLFxuICAgICAgICAgIGFuaW1hdGlvblRpbWUgPSAwO1xuICAgICAgYW5pbWF0aW9uU3RhdGVzLmZvckVhY2goZnVuY3Rpb24gKHN0YXRlKSB7XG4gICAgICAgIHZhciB0aW1lID0gMCxcbiAgICAgICAgICAgIHRhcmdldCA9IHN0YXRlLnRhcmdldCxcbiAgICAgICAgICAgIGZyb21SZWN0ID0gdGFyZ2V0LmZyb21SZWN0LFxuICAgICAgICAgICAgdG9SZWN0ID0gZ2V0UmVjdCh0YXJnZXQpLFxuICAgICAgICAgICAgcHJldkZyb21SZWN0ID0gdGFyZ2V0LnByZXZGcm9tUmVjdCxcbiAgICAgICAgICAgIHByZXZUb1JlY3QgPSB0YXJnZXQucHJldlRvUmVjdCxcbiAgICAgICAgICAgIGFuaW1hdGluZ1JlY3QgPSBzdGF0ZS5yZWN0LFxuICAgICAgICAgICAgdGFyZ2V0TWF0cml4ID0gbWF0cml4KHRhcmdldCwgdHJ1ZSk7XG5cbiAgICAgICAgaWYgKHRhcmdldE1hdHJpeCkge1xuICAgICAgICAgIC8vIENvbXBlbnNhdGUgZm9yIGN1cnJlbnQgYW5pbWF0aW9uXG4gICAgICAgICAgdG9SZWN0LnRvcCAtPSB0YXJnZXRNYXRyaXguZjtcbiAgICAgICAgICB0b1JlY3QubGVmdCAtPSB0YXJnZXRNYXRyaXguZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRhcmdldC50b1JlY3QgPSB0b1JlY3Q7XG5cbiAgICAgICAgaWYgKHRhcmdldC50aGlzQW5pbWF0aW9uRHVyYXRpb24pIHtcbiAgICAgICAgICAvLyBDb3VsZCBhbHNvIGNoZWNrIGlmIGFuaW1hdGluZ1JlY3QgaXMgYmV0d2VlbiBmcm9tUmVjdCBhbmQgdG9SZWN0XG4gICAgICAgICAgaWYgKGlzUmVjdEVxdWFsKHByZXZGcm9tUmVjdCwgdG9SZWN0KSAmJiAhaXNSZWN0RXF1YWwoZnJvbVJlY3QsIHRvUmVjdCkgJiYgLy8gTWFrZSBzdXJlIGFuaW1hdGluZ1JlY3QgaXMgb24gbGluZSBiZXR3ZWVuIHRvUmVjdCAmIGZyb21SZWN0XG4gICAgICAgICAgKGFuaW1hdGluZ1JlY3QudG9wIC0gdG9SZWN0LnRvcCkgLyAoYW5pbWF0aW5nUmVjdC5sZWZ0IC0gdG9SZWN0LmxlZnQpID09PSAoZnJvbVJlY3QudG9wIC0gdG9SZWN0LnRvcCkgLyAoZnJvbVJlY3QubGVmdCAtIHRvUmVjdC5sZWZ0KSkge1xuICAgICAgICAgICAgLy8gSWYgcmV0dXJuaW5nIHRvIHNhbWUgcGxhY2UgYXMgc3RhcnRlZCBmcm9tIGFuaW1hdGlvbiBhbmQgb24gc2FtZSBheGlzXG4gICAgICAgICAgICB0aW1lID0gY2FsY3VsYXRlUmVhbFRpbWUoYW5pbWF0aW5nUmVjdCwgcHJldkZyb21SZWN0LCBwcmV2VG9SZWN0LCBfdGhpcy5vcHRpb25zKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0gLy8gaWYgZnJvbVJlY3QgIT0gdG9SZWN0OiBhbmltYXRlXG5cblxuICAgICAgICBpZiAoIWlzUmVjdEVxdWFsKHRvUmVjdCwgZnJvbVJlY3QpKSB7XG4gICAgICAgICAgdGFyZ2V0LnByZXZGcm9tUmVjdCA9IGZyb21SZWN0O1xuICAgICAgICAgIHRhcmdldC5wcmV2VG9SZWN0ID0gdG9SZWN0O1xuXG4gICAgICAgICAgaWYgKCF0aW1lKSB7XG4gICAgICAgICAgICB0aW1lID0gX3RoaXMub3B0aW9ucy5hbmltYXRpb247XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgX3RoaXMuYW5pbWF0ZSh0YXJnZXQsIGFuaW1hdGluZ1JlY3QsIHRvUmVjdCwgdGltZSk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGltZSkge1xuICAgICAgICAgIGFuaW1hdGluZyA9IHRydWU7XG4gICAgICAgICAgYW5pbWF0aW9uVGltZSA9IE1hdGgubWF4KGFuaW1hdGlvblRpbWUsIHRpbWUpO1xuICAgICAgICAgIGNsZWFyVGltZW91dCh0YXJnZXQuYW5pbWF0aW9uUmVzZXRUaW1lcik7XG4gICAgICAgICAgdGFyZ2V0LmFuaW1hdGlvblJlc2V0VGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRhcmdldC5hbmltYXRpb25UaW1lID0gMDtcbiAgICAgICAgICAgIHRhcmdldC5wcmV2RnJvbVJlY3QgPSBudWxsO1xuICAgICAgICAgICAgdGFyZ2V0LmZyb21SZWN0ID0gbnVsbDtcbiAgICAgICAgICAgIHRhcmdldC5wcmV2VG9SZWN0ID0gbnVsbDtcbiAgICAgICAgICAgIHRhcmdldC50aGlzQW5pbWF0aW9uRHVyYXRpb24gPSBudWxsO1xuICAgICAgICAgIH0sIHRpbWUpO1xuICAgICAgICAgIHRhcmdldC50aGlzQW5pbWF0aW9uRHVyYXRpb24gPSB0aW1lO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICAgIGNsZWFyVGltZW91dChhbmltYXRpb25DYWxsYmFja0lkKTtcblxuICAgICAgaWYgKCFhbmltYXRpbmcpIHtcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ2Z1bmN0aW9uJykgY2FsbGJhY2soKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGFuaW1hdGlvbkNhbGxiYWNrSWQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICBpZiAodHlwZW9mIGNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSBjYWxsYmFjaygpO1xuICAgICAgICB9LCBhbmltYXRpb25UaW1lKTtcbiAgICAgIH1cblxuICAgICAgYW5pbWF0aW9uU3RhdGVzID0gW107XG4gICAgfSxcbiAgICBhbmltYXRlOiBmdW5jdGlvbiBhbmltYXRlKHRhcmdldCwgY3VycmVudFJlY3QsIHRvUmVjdCwgZHVyYXRpb24pIHtcbiAgICAgIGlmIChkdXJhdGlvbikge1xuICAgICAgICBjc3ModGFyZ2V0LCAndHJhbnNpdGlvbicsICcnKTtcbiAgICAgICAgY3NzKHRhcmdldCwgJ3RyYW5zZm9ybScsICcnKTtcbiAgICAgICAgdmFyIGVsTWF0cml4ID0gbWF0cml4KHRoaXMuZWwpLFxuICAgICAgICAgICAgc2NhbGVYID0gZWxNYXRyaXggJiYgZWxNYXRyaXguYSxcbiAgICAgICAgICAgIHNjYWxlWSA9IGVsTWF0cml4ICYmIGVsTWF0cml4LmQsXG4gICAgICAgICAgICB0cmFuc2xhdGVYID0gKGN1cnJlbnRSZWN0LmxlZnQgLSB0b1JlY3QubGVmdCkgLyAoc2NhbGVYIHx8IDEpLFxuICAgICAgICAgICAgdHJhbnNsYXRlWSA9IChjdXJyZW50UmVjdC50b3AgLSB0b1JlY3QudG9wKSAvIChzY2FsZVkgfHwgMSk7XG4gICAgICAgIHRhcmdldC5hbmltYXRpbmdYID0gISF0cmFuc2xhdGVYO1xuICAgICAgICB0YXJnZXQuYW5pbWF0aW5nWSA9ICEhdHJhbnNsYXRlWTtcbiAgICAgICAgY3NzKHRhcmdldCwgJ3RyYW5zZm9ybScsICd0cmFuc2xhdGUzZCgnICsgdHJhbnNsYXRlWCArICdweCwnICsgdHJhbnNsYXRlWSArICdweCwwKScpO1xuICAgICAgICB0aGlzLmZvclJlcGFpbnREdW1teSA9IHJlcGFpbnQodGFyZ2V0KTsgLy8gcmVwYWludFxuXG4gICAgICAgIGNzcyh0YXJnZXQsICd0cmFuc2l0aW9uJywgJ3RyYW5zZm9ybSAnICsgZHVyYXRpb24gKyAnbXMnICsgKHRoaXMub3B0aW9ucy5lYXNpbmcgPyAnICcgKyB0aGlzLm9wdGlvbnMuZWFzaW5nIDogJycpKTtcbiAgICAgICAgY3NzKHRhcmdldCwgJ3RyYW5zZm9ybScsICd0cmFuc2xhdGUzZCgwLDAsMCknKTtcbiAgICAgICAgdHlwZW9mIHRhcmdldC5hbmltYXRlZCA9PT0gJ251bWJlcicgJiYgY2xlYXJUaW1lb3V0KHRhcmdldC5hbmltYXRlZCk7XG4gICAgICAgIHRhcmdldC5hbmltYXRlZCA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgIGNzcyh0YXJnZXQsICd0cmFuc2l0aW9uJywgJycpO1xuICAgICAgICAgIGNzcyh0YXJnZXQsICd0cmFuc2Zvcm0nLCAnJyk7XG4gICAgICAgICAgdGFyZ2V0LmFuaW1hdGVkID0gZmFsc2U7XG4gICAgICAgICAgdGFyZ2V0LmFuaW1hdGluZ1ggPSBmYWxzZTtcbiAgICAgICAgICB0YXJnZXQuYW5pbWF0aW5nWSA9IGZhbHNlO1xuICAgICAgICB9LCBkdXJhdGlvbik7XG4gICAgICB9XG4gICAgfVxuICB9O1xufVxuXG5mdW5jdGlvbiByZXBhaW50KHRhcmdldCkge1xuICByZXR1cm4gdGFyZ2V0Lm9mZnNldFdpZHRoO1xufVxuXG5mdW5jdGlvbiBjYWxjdWxhdGVSZWFsVGltZShhbmltYXRpbmdSZWN0LCBmcm9tUmVjdCwgdG9SZWN0LCBvcHRpb25zKSB7XG4gIHJldHVybiBNYXRoLnNxcnQoTWF0aC5wb3coZnJvbVJlY3QudG9wIC0gYW5pbWF0aW5nUmVjdC50b3AsIDIpICsgTWF0aC5wb3coZnJvbVJlY3QubGVmdCAtIGFuaW1hdGluZ1JlY3QubGVmdCwgMikpIC8gTWF0aC5zcXJ0KE1hdGgucG93KGZyb21SZWN0LnRvcCAtIHRvUmVjdC50b3AsIDIpICsgTWF0aC5wb3coZnJvbVJlY3QubGVmdCAtIHRvUmVjdC5sZWZ0LCAyKSkgKiBvcHRpb25zLmFuaW1hdGlvbjtcbn1cblxudmFyIHBsdWdpbnMgPSBbXTtcbnZhciBkZWZhdWx0cyA9IHtcbiAgaW5pdGlhbGl6ZUJ5RGVmYXVsdDogdHJ1ZVxufTtcbnZhciBQbHVnaW5NYW5hZ2VyID0ge1xuICBtb3VudDogZnVuY3Rpb24gbW91bnQocGx1Z2luKSB7XG4gICAgLy8gU2V0IGRlZmF1bHQgc3RhdGljIHByb3BlcnRpZXNcbiAgICBmb3IgKHZhciBvcHRpb24gaW4gZGVmYXVsdHMpIHtcbiAgICAgIGlmIChkZWZhdWx0cy5oYXNPd25Qcm9wZXJ0eShvcHRpb24pICYmICEob3B0aW9uIGluIHBsdWdpbikpIHtcbiAgICAgICAgcGx1Z2luW29wdGlvbl0gPSBkZWZhdWx0c1tvcHRpb25dO1xuICAgICAgfVxuICAgIH1cblxuICAgIHBsdWdpbnMuZm9yRWFjaChmdW5jdGlvbiAocCkge1xuICAgICAgaWYgKHAucGx1Z2luTmFtZSA9PT0gcGx1Z2luLnBsdWdpbk5hbWUpIHtcbiAgICAgICAgdGhyb3cgXCJTb3J0YWJsZTogQ2Fubm90IG1vdW50IHBsdWdpbiBcIi5jb25jYXQocGx1Z2luLnBsdWdpbk5hbWUsIFwiIG1vcmUgdGhhbiBvbmNlXCIpO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHBsdWdpbnMucHVzaChwbHVnaW4pO1xuICB9LFxuICBwbHVnaW5FdmVudDogZnVuY3Rpb24gcGx1Z2luRXZlbnQoZXZlbnROYW1lLCBzb3J0YWJsZSwgZXZ0KSB7XG4gICAgdmFyIF90aGlzID0gdGhpcztcblxuICAgIHRoaXMuZXZlbnRDYW5jZWxlZCA9IGZhbHNlO1xuXG4gICAgZXZ0LmNhbmNlbCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgIF90aGlzLmV2ZW50Q2FuY2VsZWQgPSB0cnVlO1xuICAgIH07XG5cbiAgICB2YXIgZXZlbnROYW1lR2xvYmFsID0gZXZlbnROYW1lICsgJ0dsb2JhbCc7XG4gICAgcGx1Z2lucy5mb3JFYWNoKGZ1bmN0aW9uIChwbHVnaW4pIHtcbiAgICAgIGlmICghc29ydGFibGVbcGx1Z2luLnBsdWdpbk5hbWVdKSByZXR1cm47IC8vIEZpcmUgZ2xvYmFsIGV2ZW50cyBpZiBpdCBleGlzdHMgaW4gdGhpcyBzb3J0YWJsZVxuXG4gICAgICBpZiAoc29ydGFibGVbcGx1Z2luLnBsdWdpbk5hbWVdW2V2ZW50TmFtZUdsb2JhbF0pIHtcbiAgICAgICAgc29ydGFibGVbcGx1Z2luLnBsdWdpbk5hbWVdW2V2ZW50TmFtZUdsb2JhbF0oX29iamVjdFNwcmVhZDIoe1xuICAgICAgICAgIHNvcnRhYmxlOiBzb3J0YWJsZVxuICAgICAgICB9LCBldnQpKTtcbiAgICAgIH0gLy8gT25seSBmaXJlIHBsdWdpbiBldmVudCBpZiBwbHVnaW4gaXMgZW5hYmxlZCBpbiB0aGlzIHNvcnRhYmxlLFxuICAgICAgLy8gYW5kIHBsdWdpbiBoYXMgZXZlbnQgZGVmaW5lZFxuXG5cbiAgICAgIGlmIChzb3J0YWJsZS5vcHRpb25zW3BsdWdpbi5wbHVnaW5OYW1lXSAmJiBzb3J0YWJsZVtwbHVnaW4ucGx1Z2luTmFtZV1bZXZlbnROYW1lXSkge1xuICAgICAgICBzb3J0YWJsZVtwbHVnaW4ucGx1Z2luTmFtZV1bZXZlbnROYW1lXShfb2JqZWN0U3ByZWFkMih7XG4gICAgICAgICAgc29ydGFibGU6IHNvcnRhYmxlXG4gICAgICAgIH0sIGV2dCkpO1xuICAgICAgfVxuICAgIH0pO1xuICB9LFxuICBpbml0aWFsaXplUGx1Z2luczogZnVuY3Rpb24gaW5pdGlhbGl6ZVBsdWdpbnMoc29ydGFibGUsIGVsLCBkZWZhdWx0cywgb3B0aW9ucykge1xuICAgIHBsdWdpbnMuZm9yRWFjaChmdW5jdGlvbiAocGx1Z2luKSB7XG4gICAgICB2YXIgcGx1Z2luTmFtZSA9IHBsdWdpbi5wbHVnaW5OYW1lO1xuICAgICAgaWYgKCFzb3J0YWJsZS5vcHRpb25zW3BsdWdpbk5hbWVdICYmICFwbHVnaW4uaW5pdGlhbGl6ZUJ5RGVmYXVsdCkgcmV0dXJuO1xuICAgICAgdmFyIGluaXRpYWxpemVkID0gbmV3IHBsdWdpbihzb3J0YWJsZSwgZWwsIHNvcnRhYmxlLm9wdGlvbnMpO1xuICAgICAgaW5pdGlhbGl6ZWQuc29ydGFibGUgPSBzb3J0YWJsZTtcbiAgICAgIGluaXRpYWxpemVkLm9wdGlvbnMgPSBzb3J0YWJsZS5vcHRpb25zO1xuICAgICAgc29ydGFibGVbcGx1Z2luTmFtZV0gPSBpbml0aWFsaXplZDsgLy8gQWRkIGRlZmF1bHQgb3B0aW9ucyBmcm9tIHBsdWdpblxuXG4gICAgICBfZXh0ZW5kcyhkZWZhdWx0cywgaW5pdGlhbGl6ZWQuZGVmYXVsdHMpO1xuICAgIH0pO1xuXG4gICAgZm9yICh2YXIgb3B0aW9uIGluIHNvcnRhYmxlLm9wdGlvbnMpIHtcbiAgICAgIGlmICghc29ydGFibGUub3B0aW9ucy5oYXNPd25Qcm9wZXJ0eShvcHRpb24pKSBjb250aW51ZTtcbiAgICAgIHZhciBtb2RpZmllZCA9IHRoaXMubW9kaWZ5T3B0aW9uKHNvcnRhYmxlLCBvcHRpb24sIHNvcnRhYmxlLm9wdGlvbnNbb3B0aW9uXSk7XG5cbiAgICAgIGlmICh0eXBlb2YgbW9kaWZpZWQgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIHNvcnRhYmxlLm9wdGlvbnNbb3B0aW9uXSA9IG1vZGlmaWVkO1xuICAgICAgfVxuICAgIH1cbiAgfSxcbiAgZ2V0RXZlbnRQcm9wZXJ0aWVzOiBmdW5jdGlvbiBnZXRFdmVudFByb3BlcnRpZXMobmFtZSwgc29ydGFibGUpIHtcbiAgICB2YXIgZXZlbnRQcm9wZXJ0aWVzID0ge307XG4gICAgcGx1Z2lucy5mb3JFYWNoKGZ1bmN0aW9uIChwbHVnaW4pIHtcbiAgICAgIGlmICh0eXBlb2YgcGx1Z2luLmV2ZW50UHJvcGVydGllcyAhPT0gJ2Z1bmN0aW9uJykgcmV0dXJuO1xuXG4gICAgICBfZXh0ZW5kcyhldmVudFByb3BlcnRpZXMsIHBsdWdpbi5ldmVudFByb3BlcnRpZXMuY2FsbChzb3J0YWJsZVtwbHVnaW4ucGx1Z2luTmFtZV0sIG5hbWUpKTtcbiAgICB9KTtcbiAgICByZXR1cm4gZXZlbnRQcm9wZXJ0aWVzO1xuICB9LFxuICBtb2RpZnlPcHRpb246IGZ1bmN0aW9uIG1vZGlmeU9wdGlvbihzb3J0YWJsZSwgbmFtZSwgdmFsdWUpIHtcbiAgICB2YXIgbW9kaWZpZWRWYWx1ZTtcbiAgICBwbHVnaW5zLmZvckVhY2goZnVuY3Rpb24gKHBsdWdpbikge1xuICAgICAgLy8gUGx1Z2luIG11c3QgZXhpc3Qgb24gdGhlIFNvcnRhYmxlXG4gICAgICBpZiAoIXNvcnRhYmxlW3BsdWdpbi5wbHVnaW5OYW1lXSkgcmV0dXJuOyAvLyBJZiBzdGF0aWMgb3B0aW9uIGxpc3RlbmVyIGV4aXN0cyBmb3IgdGhpcyBvcHRpb24sIGNhbGwgaW4gdGhlIGNvbnRleHQgb2YgdGhlIFNvcnRhYmxlJ3MgaW5zdGFuY2Ugb2YgdGhpcyBwbHVnaW5cblxuICAgICAgaWYgKHBsdWdpbi5vcHRpb25MaXN0ZW5lcnMgJiYgdHlwZW9mIHBsdWdpbi5vcHRpb25MaXN0ZW5lcnNbbmFtZV0gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgbW9kaWZpZWRWYWx1ZSA9IHBsdWdpbi5vcHRpb25MaXN0ZW5lcnNbbmFtZV0uY2FsbChzb3J0YWJsZVtwbHVnaW4ucGx1Z2luTmFtZV0sIHZhbHVlKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgICByZXR1cm4gbW9kaWZpZWRWYWx1ZTtcbiAgfVxufTtcblxuZnVuY3Rpb24gZGlzcGF0Y2hFdmVudChfcmVmKSB7XG4gIHZhciBzb3J0YWJsZSA9IF9yZWYuc29ydGFibGUsXG4gICAgICByb290RWwgPSBfcmVmLnJvb3RFbCxcbiAgICAgIG5hbWUgPSBfcmVmLm5hbWUsXG4gICAgICB0YXJnZXRFbCA9IF9yZWYudGFyZ2V0RWwsXG4gICAgICBjbG9uZUVsID0gX3JlZi5jbG9uZUVsLFxuICAgICAgdG9FbCA9IF9yZWYudG9FbCxcbiAgICAgIGZyb21FbCA9IF9yZWYuZnJvbUVsLFxuICAgICAgb2xkSW5kZXggPSBfcmVmLm9sZEluZGV4LFxuICAgICAgbmV3SW5kZXggPSBfcmVmLm5ld0luZGV4LFxuICAgICAgb2xkRHJhZ2dhYmxlSW5kZXggPSBfcmVmLm9sZERyYWdnYWJsZUluZGV4LFxuICAgICAgbmV3RHJhZ2dhYmxlSW5kZXggPSBfcmVmLm5ld0RyYWdnYWJsZUluZGV4LFxuICAgICAgb3JpZ2luYWxFdmVudCA9IF9yZWYub3JpZ2luYWxFdmVudCxcbiAgICAgIHB1dFNvcnRhYmxlID0gX3JlZi5wdXRTb3J0YWJsZSxcbiAgICAgIGV4dHJhRXZlbnRQcm9wZXJ0aWVzID0gX3JlZi5leHRyYUV2ZW50UHJvcGVydGllcztcbiAgc29ydGFibGUgPSBzb3J0YWJsZSB8fCByb290RWwgJiYgcm9vdEVsW2V4cGFuZG9dO1xuICBpZiAoIXNvcnRhYmxlKSByZXR1cm47XG4gIHZhciBldnQsXG4gICAgICBvcHRpb25zID0gc29ydGFibGUub3B0aW9ucyxcbiAgICAgIG9uTmFtZSA9ICdvbicgKyBuYW1lLmNoYXJBdCgwKS50b1VwcGVyQ2FzZSgpICsgbmFtZS5zdWJzdHIoMSk7IC8vIFN1cHBvcnQgZm9yIG5ldyBDdXN0b21FdmVudCBmZWF0dXJlXG5cbiAgaWYgKHdpbmRvdy5DdXN0b21FdmVudCAmJiAhSUUxMU9yTGVzcyAmJiAhRWRnZSkge1xuICAgIGV2dCA9IG5ldyBDdXN0b21FdmVudChuYW1lLCB7XG4gICAgICBidWJibGVzOiB0cnVlLFxuICAgICAgY2FuY2VsYWJsZTogdHJ1ZVxuICAgIH0pO1xuICB9IGVsc2Uge1xuICAgIGV2dCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCdFdmVudCcpO1xuICAgIGV2dC5pbml0RXZlbnQobmFtZSwgdHJ1ZSwgdHJ1ZSk7XG4gIH1cblxuICBldnQudG8gPSB0b0VsIHx8IHJvb3RFbDtcbiAgZXZ0LmZyb20gPSBmcm9tRWwgfHwgcm9vdEVsO1xuICBldnQuaXRlbSA9IHRhcmdldEVsIHx8IHJvb3RFbDtcbiAgZXZ0LmNsb25lID0gY2xvbmVFbDtcbiAgZXZ0Lm9sZEluZGV4ID0gb2xkSW5kZXg7XG4gIGV2dC5uZXdJbmRleCA9IG5ld0luZGV4O1xuICBldnQub2xkRHJhZ2dhYmxlSW5kZXggPSBvbGREcmFnZ2FibGVJbmRleDtcbiAgZXZ0Lm5ld0RyYWdnYWJsZUluZGV4ID0gbmV3RHJhZ2dhYmxlSW5kZXg7XG4gIGV2dC5vcmlnaW5hbEV2ZW50ID0gb3JpZ2luYWxFdmVudDtcbiAgZXZ0LnB1bGxNb2RlID0gcHV0U29ydGFibGUgPyBwdXRTb3J0YWJsZS5sYXN0UHV0TW9kZSA6IHVuZGVmaW5lZDtcblxuICB2YXIgYWxsRXZlbnRQcm9wZXJ0aWVzID0gX29iamVjdFNwcmVhZDIoX29iamVjdFNwcmVhZDIoe30sIGV4dHJhRXZlbnRQcm9wZXJ0aWVzKSwgUGx1Z2luTWFuYWdlci5nZXRFdmVudFByb3BlcnRpZXMobmFtZSwgc29ydGFibGUpKTtcblxuICBmb3IgKHZhciBvcHRpb24gaW4gYWxsRXZlbnRQcm9wZXJ0aWVzKSB7XG4gICAgZXZ0W29wdGlvbl0gPSBhbGxFdmVudFByb3BlcnRpZXNbb3B0aW9uXTtcbiAgfVxuXG4gIGlmIChyb290RWwpIHtcbiAgICByb290RWwuZGlzcGF0Y2hFdmVudChldnQpO1xuICB9XG5cbiAgaWYgKG9wdGlvbnNbb25OYW1lXSkge1xuICAgIG9wdGlvbnNbb25OYW1lXS5jYWxsKHNvcnRhYmxlLCBldnQpO1xuICB9XG59XG5cbnZhciBfZXhjbHVkZWQgPSBbXCJldnRcIl07XG5cbnZhciBwbHVnaW5FdmVudCA9IGZ1bmN0aW9uIHBsdWdpbkV2ZW50KGV2ZW50TmFtZSwgc29ydGFibGUpIHtcbiAgdmFyIF9yZWYgPSBhcmd1bWVudHMubGVuZ3RoID4gMiAmJiBhcmd1bWVudHNbMl0gIT09IHVuZGVmaW5lZCA/IGFyZ3VtZW50c1syXSA6IHt9LFxuICAgICAgb3JpZ2luYWxFdmVudCA9IF9yZWYuZXZ0LFxuICAgICAgZGF0YSA9IF9vYmplY3RXaXRob3V0UHJvcGVydGllcyhfcmVmLCBfZXhjbHVkZWQpO1xuXG4gIFBsdWdpbk1hbmFnZXIucGx1Z2luRXZlbnQuYmluZChTb3J0YWJsZSkoZXZlbnROYW1lLCBzb3J0YWJsZSwgX29iamVjdFNwcmVhZDIoe1xuICAgIGRyYWdFbDogZHJhZ0VsLFxuICAgIHBhcmVudEVsOiBwYXJlbnRFbCxcbiAgICBnaG9zdEVsOiBnaG9zdEVsLFxuICAgIHJvb3RFbDogcm9vdEVsLFxuICAgIG5leHRFbDogbmV4dEVsLFxuICAgIGxhc3REb3duRWw6IGxhc3REb3duRWwsXG4gICAgY2xvbmVFbDogY2xvbmVFbCxcbiAgICBjbG9uZUhpZGRlbjogY2xvbmVIaWRkZW4sXG4gICAgZHJhZ1N0YXJ0ZWQ6IG1vdmVkLFxuICAgIHB1dFNvcnRhYmxlOiBwdXRTb3J0YWJsZSxcbiAgICBhY3RpdmVTb3J0YWJsZTogU29ydGFibGUuYWN0aXZlLFxuICAgIG9yaWdpbmFsRXZlbnQ6IG9yaWdpbmFsRXZlbnQsXG4gICAgb2xkSW5kZXg6IG9sZEluZGV4LFxuICAgIG9sZERyYWdnYWJsZUluZGV4OiBvbGREcmFnZ2FibGVJbmRleCxcbiAgICBuZXdJbmRleDogbmV3SW5kZXgsXG4gICAgbmV3RHJhZ2dhYmxlSW5kZXg6IG5ld0RyYWdnYWJsZUluZGV4LFxuICAgIGhpZGVHaG9zdEZvclRhcmdldDogX2hpZGVHaG9zdEZvclRhcmdldCxcbiAgICB1bmhpZGVHaG9zdEZvclRhcmdldDogX3VuaGlkZUdob3N0Rm9yVGFyZ2V0LFxuICAgIGNsb25lTm93SGlkZGVuOiBmdW5jdGlvbiBjbG9uZU5vd0hpZGRlbigpIHtcbiAgICAgIGNsb25lSGlkZGVuID0gdHJ1ZTtcbiAgICB9LFxuICAgIGNsb25lTm93U2hvd246IGZ1bmN0aW9uIGNsb25lTm93U2hvd24oKSB7XG4gICAgICBjbG9uZUhpZGRlbiA9IGZhbHNlO1xuICAgIH0sXG4gICAgZGlzcGF0Y2hTb3J0YWJsZUV2ZW50OiBmdW5jdGlvbiBkaXNwYXRjaFNvcnRhYmxlRXZlbnQobmFtZSkge1xuICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICBzb3J0YWJsZTogc29ydGFibGUsXG4gICAgICAgIG5hbWU6IG5hbWUsXG4gICAgICAgIG9yaWdpbmFsRXZlbnQ6IG9yaWdpbmFsRXZlbnRcbiAgICAgIH0pO1xuICAgIH1cbiAgfSwgZGF0YSkpO1xufTtcblxuZnVuY3Rpb24gX2Rpc3BhdGNoRXZlbnQoaW5mbykge1xuICBkaXNwYXRjaEV2ZW50KF9vYmplY3RTcHJlYWQyKHtcbiAgICBwdXRTb3J0YWJsZTogcHV0U29ydGFibGUsXG4gICAgY2xvbmVFbDogY2xvbmVFbCxcbiAgICB0YXJnZXRFbDogZHJhZ0VsLFxuICAgIHJvb3RFbDogcm9vdEVsLFxuICAgIG9sZEluZGV4OiBvbGRJbmRleCxcbiAgICBvbGREcmFnZ2FibGVJbmRleDogb2xkRHJhZ2dhYmxlSW5kZXgsXG4gICAgbmV3SW5kZXg6IG5ld0luZGV4LFxuICAgIG5ld0RyYWdnYWJsZUluZGV4OiBuZXdEcmFnZ2FibGVJbmRleFxuICB9LCBpbmZvKSk7XG59XG5cbnZhciBkcmFnRWwsXG4gICAgcGFyZW50RWwsXG4gICAgZ2hvc3RFbCxcbiAgICByb290RWwsXG4gICAgbmV4dEVsLFxuICAgIGxhc3REb3duRWwsXG4gICAgY2xvbmVFbCxcbiAgICBjbG9uZUhpZGRlbixcbiAgICBvbGRJbmRleCxcbiAgICBuZXdJbmRleCxcbiAgICBvbGREcmFnZ2FibGVJbmRleCxcbiAgICBuZXdEcmFnZ2FibGVJbmRleCxcbiAgICBhY3RpdmVHcm91cCxcbiAgICBwdXRTb3J0YWJsZSxcbiAgICBhd2FpdGluZ0RyYWdTdGFydGVkID0gZmFsc2UsXG4gICAgaWdub3JlTmV4dENsaWNrID0gZmFsc2UsXG4gICAgc29ydGFibGVzID0gW10sXG4gICAgdGFwRXZ0LFxuICAgIHRvdWNoRXZ0LFxuICAgIGxhc3REeCxcbiAgICBsYXN0RHksXG4gICAgdGFwRGlzdGFuY2VMZWZ0LFxuICAgIHRhcERpc3RhbmNlVG9wLFxuICAgIG1vdmVkLFxuICAgIGxhc3RUYXJnZXQsXG4gICAgbGFzdERpcmVjdGlvbixcbiAgICBwYXN0Rmlyc3RJbnZlcnRUaHJlc2ggPSBmYWxzZSxcbiAgICBpc0NpcmN1bXN0YW50aWFsSW52ZXJ0ID0gZmFsc2UsXG4gICAgdGFyZ2V0TW92ZURpc3RhbmNlLFxuICAgIC8vIEZvciBwb3NpdGlvbmluZyBnaG9zdCBhYnNvbHV0ZWx5XG5naG9zdFJlbGF0aXZlUGFyZW50LFxuICAgIGdob3N0UmVsYXRpdmVQYXJlbnRJbml0aWFsU2Nyb2xsID0gW10sXG4gICAgLy8gKGxlZnQsIHRvcClcbl9zaWxlbnQgPSBmYWxzZSxcbiAgICBzYXZlZElucHV0Q2hlY2tlZCA9IFtdO1xuLyoqIEBjb25zdCAqL1xuXG52YXIgZG9jdW1lbnRFeGlzdHMgPSB0eXBlb2YgZG9jdW1lbnQgIT09ICd1bmRlZmluZWQnLFxuICAgIFBvc2l0aW9uR2hvc3RBYnNvbHV0ZWx5ID0gSU9TLFxuICAgIENTU0Zsb2F0UHJvcGVydHkgPSBFZGdlIHx8IElFMTFPckxlc3MgPyAnY3NzRmxvYXQnIDogJ2Zsb2F0JyxcbiAgICAvLyBUaGlzIHdpbGwgbm90IHBhc3MgZm9yIElFOSwgYmVjYXVzZSBJRTkgRG5EIG9ubHkgd29ya3Mgb24gYW5jaG9yc1xuc3VwcG9ydERyYWdnYWJsZSA9IGRvY3VtZW50RXhpc3RzICYmICFDaHJvbWVGb3JBbmRyb2lkICYmICFJT1MgJiYgJ2RyYWdnYWJsZScgaW4gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2JyksXG4gICAgc3VwcG9ydENzc1BvaW50ZXJFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG4gIGlmICghZG9jdW1lbnRFeGlzdHMpIHJldHVybjsgLy8gZmFsc2Ugd2hlbiA8PSBJRTExXG5cbiAgaWYgKElFMTFPckxlc3MpIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cblxuICB2YXIgZWwgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCd4Jyk7XG4gIGVsLnN0eWxlLmNzc1RleHQgPSAncG9pbnRlci1ldmVudHM6YXV0byc7XG4gIHJldHVybiBlbC5zdHlsZS5wb2ludGVyRXZlbnRzID09PSAnYXV0byc7XG59KCksXG4gICAgX2RldGVjdERpcmVjdGlvbiA9IGZ1bmN0aW9uIF9kZXRlY3REaXJlY3Rpb24oZWwsIG9wdGlvbnMpIHtcbiAgdmFyIGVsQ1NTID0gY3NzKGVsKSxcbiAgICAgIGVsV2lkdGggPSBwYXJzZUludChlbENTUy53aWR0aCkgLSBwYXJzZUludChlbENTUy5wYWRkaW5nTGVmdCkgLSBwYXJzZUludChlbENTUy5wYWRkaW5nUmlnaHQpIC0gcGFyc2VJbnQoZWxDU1MuYm9yZGVyTGVmdFdpZHRoKSAtIHBhcnNlSW50KGVsQ1NTLmJvcmRlclJpZ2h0V2lkdGgpLFxuICAgICAgY2hpbGQxID0gZ2V0Q2hpbGQoZWwsIDAsIG9wdGlvbnMpLFxuICAgICAgY2hpbGQyID0gZ2V0Q2hpbGQoZWwsIDEsIG9wdGlvbnMpLFxuICAgICAgZmlyc3RDaGlsZENTUyA9IGNoaWxkMSAmJiBjc3MoY2hpbGQxKSxcbiAgICAgIHNlY29uZENoaWxkQ1NTID0gY2hpbGQyICYmIGNzcyhjaGlsZDIpLFxuICAgICAgZmlyc3RDaGlsZFdpZHRoID0gZmlyc3RDaGlsZENTUyAmJiBwYXJzZUludChmaXJzdENoaWxkQ1NTLm1hcmdpbkxlZnQpICsgcGFyc2VJbnQoZmlyc3RDaGlsZENTUy5tYXJnaW5SaWdodCkgKyBnZXRSZWN0KGNoaWxkMSkud2lkdGgsXG4gICAgICBzZWNvbmRDaGlsZFdpZHRoID0gc2Vjb25kQ2hpbGRDU1MgJiYgcGFyc2VJbnQoc2Vjb25kQ2hpbGRDU1MubWFyZ2luTGVmdCkgKyBwYXJzZUludChzZWNvbmRDaGlsZENTUy5tYXJnaW5SaWdodCkgKyBnZXRSZWN0KGNoaWxkMikud2lkdGg7XG5cbiAgaWYgKGVsQ1NTLmRpc3BsYXkgPT09ICdmbGV4Jykge1xuICAgIHJldHVybiBlbENTUy5mbGV4RGlyZWN0aW9uID09PSAnY29sdW1uJyB8fCBlbENTUy5mbGV4RGlyZWN0aW9uID09PSAnY29sdW1uLXJldmVyc2UnID8gJ3ZlcnRpY2FsJyA6ICdob3Jpem9udGFsJztcbiAgfVxuXG4gIGlmIChlbENTUy5kaXNwbGF5ID09PSAnZ3JpZCcpIHtcbiAgICByZXR1cm4gZWxDU1MuZ3JpZFRlbXBsYXRlQ29sdW1ucy5zcGxpdCgnICcpLmxlbmd0aCA8PSAxID8gJ3ZlcnRpY2FsJyA6ICdob3Jpem9udGFsJztcbiAgfVxuXG4gIGlmIChjaGlsZDEgJiYgZmlyc3RDaGlsZENTU1tcImZsb2F0XCJdICYmIGZpcnN0Q2hpbGRDU1NbXCJmbG9hdFwiXSAhPT0gJ25vbmUnKSB7XG4gICAgdmFyIHRvdWNoaW5nU2lkZUNoaWxkMiA9IGZpcnN0Q2hpbGRDU1NbXCJmbG9hdFwiXSA9PT0gJ2xlZnQnID8gJ2xlZnQnIDogJ3JpZ2h0JztcbiAgICByZXR1cm4gY2hpbGQyICYmIChzZWNvbmRDaGlsZENTUy5jbGVhciA9PT0gJ2JvdGgnIHx8IHNlY29uZENoaWxkQ1NTLmNsZWFyID09PSB0b3VjaGluZ1NpZGVDaGlsZDIpID8gJ3ZlcnRpY2FsJyA6ICdob3Jpem9udGFsJztcbiAgfVxuXG4gIHJldHVybiBjaGlsZDEgJiYgKGZpcnN0Q2hpbGRDU1MuZGlzcGxheSA9PT0gJ2Jsb2NrJyB8fCBmaXJzdENoaWxkQ1NTLmRpc3BsYXkgPT09ICdmbGV4JyB8fCBmaXJzdENoaWxkQ1NTLmRpc3BsYXkgPT09ICd0YWJsZScgfHwgZmlyc3RDaGlsZENTUy5kaXNwbGF5ID09PSAnZ3JpZCcgfHwgZmlyc3RDaGlsZFdpZHRoID49IGVsV2lkdGggJiYgZWxDU1NbQ1NTRmxvYXRQcm9wZXJ0eV0gPT09ICdub25lJyB8fCBjaGlsZDIgJiYgZWxDU1NbQ1NTRmxvYXRQcm9wZXJ0eV0gPT09ICdub25lJyAmJiBmaXJzdENoaWxkV2lkdGggKyBzZWNvbmRDaGlsZFdpZHRoID4gZWxXaWR0aCkgPyAndmVydGljYWwnIDogJ2hvcml6b250YWwnO1xufSxcbiAgICBfZHJhZ0VsSW5Sb3dDb2x1bW4gPSBmdW5jdGlvbiBfZHJhZ0VsSW5Sb3dDb2x1bW4oZHJhZ1JlY3QsIHRhcmdldFJlY3QsIHZlcnRpY2FsKSB7XG4gIHZhciBkcmFnRWxTMU9wcCA9IHZlcnRpY2FsID8gZHJhZ1JlY3QubGVmdCA6IGRyYWdSZWN0LnRvcCxcbiAgICAgIGRyYWdFbFMyT3BwID0gdmVydGljYWwgPyBkcmFnUmVjdC5yaWdodCA6IGRyYWdSZWN0LmJvdHRvbSxcbiAgICAgIGRyYWdFbE9wcExlbmd0aCA9IHZlcnRpY2FsID8gZHJhZ1JlY3Qud2lkdGggOiBkcmFnUmVjdC5oZWlnaHQsXG4gICAgICB0YXJnZXRTMU9wcCA9IHZlcnRpY2FsID8gdGFyZ2V0UmVjdC5sZWZ0IDogdGFyZ2V0UmVjdC50b3AsXG4gICAgICB0YXJnZXRTMk9wcCA9IHZlcnRpY2FsID8gdGFyZ2V0UmVjdC5yaWdodCA6IHRhcmdldFJlY3QuYm90dG9tLFxuICAgICAgdGFyZ2V0T3BwTGVuZ3RoID0gdmVydGljYWwgPyB0YXJnZXRSZWN0LndpZHRoIDogdGFyZ2V0UmVjdC5oZWlnaHQ7XG4gIHJldHVybiBkcmFnRWxTMU9wcCA9PT0gdGFyZ2V0UzFPcHAgfHwgZHJhZ0VsUzJPcHAgPT09IHRhcmdldFMyT3BwIHx8IGRyYWdFbFMxT3BwICsgZHJhZ0VsT3BwTGVuZ3RoIC8gMiA9PT0gdGFyZ2V0UzFPcHAgKyB0YXJnZXRPcHBMZW5ndGggLyAyO1xufSxcblxuLyoqXHJcbiAqIERldGVjdHMgZmlyc3QgbmVhcmVzdCBlbXB0eSBzb3J0YWJsZSB0byBYIGFuZCBZIHBvc2l0aW9uIHVzaW5nIGVtcHR5SW5zZXJ0VGhyZXNob2xkLlxyXG4gKiBAcGFyYW0gIHtOdW1iZXJ9IHggICAgICBYIHBvc2l0aW9uXHJcbiAqIEBwYXJhbSAge051bWJlcn0geSAgICAgIFkgcG9zaXRpb25cclxuICogQHJldHVybiB7SFRNTEVsZW1lbnR9ICAgRWxlbWVudCBvZiB0aGUgZmlyc3QgZm91bmQgbmVhcmVzdCBTb3J0YWJsZVxyXG4gKi9cbl9kZXRlY3ROZWFyZXN0RW1wdHlTb3J0YWJsZSA9IGZ1bmN0aW9uIF9kZXRlY3ROZWFyZXN0RW1wdHlTb3J0YWJsZSh4LCB5KSB7XG4gIHZhciByZXQ7XG4gIHNvcnRhYmxlcy5zb21lKGZ1bmN0aW9uIChzb3J0YWJsZSkge1xuICAgIHZhciB0aHJlc2hvbGQgPSBzb3J0YWJsZVtleHBhbmRvXS5vcHRpb25zLmVtcHR5SW5zZXJ0VGhyZXNob2xkO1xuICAgIGlmICghdGhyZXNob2xkIHx8IGxhc3RDaGlsZChzb3J0YWJsZSkpIHJldHVybjtcbiAgICB2YXIgcmVjdCA9IGdldFJlY3Qoc29ydGFibGUpLFxuICAgICAgICBpbnNpZGVIb3Jpem9udGFsbHkgPSB4ID49IHJlY3QubGVmdCAtIHRocmVzaG9sZCAmJiB4IDw9IHJlY3QucmlnaHQgKyB0aHJlc2hvbGQsXG4gICAgICAgIGluc2lkZVZlcnRpY2FsbHkgPSB5ID49IHJlY3QudG9wIC0gdGhyZXNob2xkICYmIHkgPD0gcmVjdC5ib3R0b20gKyB0aHJlc2hvbGQ7XG5cbiAgICBpZiAoaW5zaWRlSG9yaXpvbnRhbGx5ICYmIGluc2lkZVZlcnRpY2FsbHkpIHtcbiAgICAgIHJldHVybiByZXQgPSBzb3J0YWJsZTtcbiAgICB9XG4gIH0pO1xuICByZXR1cm4gcmV0O1xufSxcbiAgICBfcHJlcGFyZUdyb3VwID0gZnVuY3Rpb24gX3ByZXBhcmVHcm91cChvcHRpb25zKSB7XG4gIGZ1bmN0aW9uIHRvRm4odmFsdWUsIHB1bGwpIHtcbiAgICByZXR1cm4gZnVuY3Rpb24gKHRvLCBmcm9tLCBkcmFnRWwsIGV2dCkge1xuICAgICAgdmFyIHNhbWVHcm91cCA9IHRvLm9wdGlvbnMuZ3JvdXAubmFtZSAmJiBmcm9tLm9wdGlvbnMuZ3JvdXAubmFtZSAmJiB0by5vcHRpb25zLmdyb3VwLm5hbWUgPT09IGZyb20ub3B0aW9ucy5ncm91cC5uYW1lO1xuXG4gICAgICBpZiAodmFsdWUgPT0gbnVsbCAmJiAocHVsbCB8fCBzYW1lR3JvdXApKSB7XG4gICAgICAgIC8vIERlZmF1bHQgcHVsbCB2YWx1ZVxuICAgICAgICAvLyBEZWZhdWx0IHB1bGwgYW5kIHB1dCB2YWx1ZSBpZiBzYW1lIGdyb3VwXG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfSBlbHNlIGlmICh2YWx1ZSA9PSBudWxsIHx8IHZhbHVlID09PSBmYWxzZSkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9IGVsc2UgaWYgKHB1bGwgJiYgdmFsdWUgPT09ICdjbG9uZScpIHtcbiAgICAgICAgcmV0dXJuIHZhbHVlO1xuICAgICAgfSBlbHNlIGlmICh0eXBlb2YgdmFsdWUgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgcmV0dXJuIHRvRm4odmFsdWUodG8sIGZyb20sIGRyYWdFbCwgZXZ0KSwgcHVsbCkodG8sIGZyb20sIGRyYWdFbCwgZXZ0KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHZhciBvdGhlckdyb3VwID0gKHB1bGwgPyB0byA6IGZyb20pLm9wdGlvbnMuZ3JvdXAubmFtZTtcbiAgICAgICAgcmV0dXJuIHZhbHVlID09PSB0cnVlIHx8IHR5cGVvZiB2YWx1ZSA9PT0gJ3N0cmluZycgJiYgdmFsdWUgPT09IG90aGVyR3JvdXAgfHwgdmFsdWUuam9pbiAmJiB2YWx1ZS5pbmRleE9mKG90aGVyR3JvdXApID4gLTE7XG4gICAgICB9XG4gICAgfTtcbiAgfVxuXG4gIHZhciBncm91cCA9IHt9O1xuICB2YXIgb3JpZ2luYWxHcm91cCA9IG9wdGlvbnMuZ3JvdXA7XG5cbiAgaWYgKCFvcmlnaW5hbEdyb3VwIHx8IF90eXBlb2Yob3JpZ2luYWxHcm91cCkgIT0gJ29iamVjdCcpIHtcbiAgICBvcmlnaW5hbEdyb3VwID0ge1xuICAgICAgbmFtZTogb3JpZ2luYWxHcm91cFxuICAgIH07XG4gIH1cblxuICBncm91cC5uYW1lID0gb3JpZ2luYWxHcm91cC5uYW1lO1xuICBncm91cC5jaGVja1B1bGwgPSB0b0ZuKG9yaWdpbmFsR3JvdXAucHVsbCwgdHJ1ZSk7XG4gIGdyb3VwLmNoZWNrUHV0ID0gdG9GbihvcmlnaW5hbEdyb3VwLnB1dCk7XG4gIGdyb3VwLnJldmVydENsb25lID0gb3JpZ2luYWxHcm91cC5yZXZlcnRDbG9uZTtcbiAgb3B0aW9ucy5ncm91cCA9IGdyb3VwO1xufSxcbiAgICBfaGlkZUdob3N0Rm9yVGFyZ2V0ID0gZnVuY3Rpb24gX2hpZGVHaG9zdEZvclRhcmdldCgpIHtcbiAgaWYgKCFzdXBwb3J0Q3NzUG9pbnRlckV2ZW50cyAmJiBnaG9zdEVsKSB7XG4gICAgY3NzKGdob3N0RWwsICdkaXNwbGF5JywgJ25vbmUnKTtcbiAgfVxufSxcbiAgICBfdW5oaWRlR2hvc3RGb3JUYXJnZXQgPSBmdW5jdGlvbiBfdW5oaWRlR2hvc3RGb3JUYXJnZXQoKSB7XG4gIGlmICghc3VwcG9ydENzc1BvaW50ZXJFdmVudHMgJiYgZ2hvc3RFbCkge1xuICAgIGNzcyhnaG9zdEVsLCAnZGlzcGxheScsICcnKTtcbiAgfVxufTsgLy8gIzExODQgZml4IC0gUHJldmVudCBjbGljayBldmVudCBvbiBmYWxsYmFjayBpZiBkcmFnZ2VkIGJ1dCBpdGVtIG5vdCBjaGFuZ2VkIHBvc2l0aW9uXG5cblxuaWYgKGRvY3VtZW50RXhpc3RzICYmICFDaHJvbWVGb3JBbmRyb2lkKSB7XG4gIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgZnVuY3Rpb24gKGV2dCkge1xuICAgIGlmIChpZ25vcmVOZXh0Q2xpY2spIHtcbiAgICAgIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZ0LnN0b3BQcm9wYWdhdGlvbiAmJiBldnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICBldnQuc3RvcEltbWVkaWF0ZVByb3BhZ2F0aW9uICYmIGV2dC5zdG9wSW1tZWRpYXRlUHJvcGFnYXRpb24oKTtcbiAgICAgIGlnbm9yZU5leHRDbGljayA9IGZhbHNlO1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cbiAgfSwgdHJ1ZSk7XG59XG5cbnZhciBuZWFyZXN0RW1wdHlJbnNlcnREZXRlY3RFdmVudCA9IGZ1bmN0aW9uIG5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50KGV2dCkge1xuICBpZiAoZHJhZ0VsKSB7XG4gICAgZXZ0ID0gZXZ0LnRvdWNoZXMgPyBldnQudG91Y2hlc1swXSA6IGV2dDtcblxuICAgIHZhciBuZWFyZXN0ID0gX2RldGVjdE5lYXJlc3RFbXB0eVNvcnRhYmxlKGV2dC5jbGllbnRYLCBldnQuY2xpZW50WSk7XG5cbiAgICBpZiAobmVhcmVzdCkge1xuICAgICAgLy8gQ3JlYXRlIGltaXRhdGlvbiBldmVudFxuICAgICAgdmFyIGV2ZW50ID0ge307XG5cbiAgICAgIGZvciAodmFyIGkgaW4gZXZ0KSB7XG4gICAgICAgIGlmIChldnQuaGFzT3duUHJvcGVydHkoaSkpIHtcbiAgICAgICAgICBldmVudFtpXSA9IGV2dFtpXTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBldmVudC50YXJnZXQgPSBldmVudC5yb290RWwgPSBuZWFyZXN0O1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQgPSB2b2lkIDA7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24gPSB2b2lkIDA7XG5cbiAgICAgIG5lYXJlc3RbZXhwYW5kb10uX29uRHJhZ092ZXIoZXZlbnQpO1xuICAgIH1cbiAgfVxufTtcblxudmFyIF9jaGVja091dHNpZGVUYXJnZXRFbCA9IGZ1bmN0aW9uIF9jaGVja091dHNpZGVUYXJnZXRFbChldnQpIHtcbiAgaWYgKGRyYWdFbCkge1xuICAgIGRyYWdFbC5wYXJlbnROb2RlW2V4cGFuZG9dLl9pc091dHNpZGVUaGlzRWwoZXZ0LnRhcmdldCk7XG4gIH1cbn07XG4vKipcclxuICogQGNsYXNzICBTb3J0YWJsZVxyXG4gKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gIGVsXHJcbiAqIEBwYXJhbSAge09iamVjdH0gICAgICAgW29wdGlvbnNdXHJcbiAqL1xuXG5cbmZ1bmN0aW9uIFNvcnRhYmxlKGVsLCBvcHRpb25zKSB7XG4gIGlmICghKGVsICYmIGVsLm5vZGVUeXBlICYmIGVsLm5vZGVUeXBlID09PSAxKSkge1xuICAgIHRocm93IFwiU29ydGFibGU6IGBlbGAgbXVzdCBiZSBhbiBIVE1MRWxlbWVudCwgbm90IFwiLmNvbmNhdCh7fS50b1N0cmluZy5jYWxsKGVsKSk7XG4gIH1cblxuICB0aGlzLmVsID0gZWw7IC8vIHJvb3QgZWxlbWVudFxuXG4gIHRoaXMub3B0aW9ucyA9IG9wdGlvbnMgPSBfZXh0ZW5kcyh7fSwgb3B0aW9ucyk7IC8vIEV4cG9ydCBpbnN0YW5jZVxuXG4gIGVsW2V4cGFuZG9dID0gdGhpcztcbiAgdmFyIGRlZmF1bHRzID0ge1xuICAgIGdyb3VwOiBudWxsLFxuICAgIHNvcnQ6IHRydWUsXG4gICAgZGlzYWJsZWQ6IGZhbHNlLFxuICAgIHN0b3JlOiBudWxsLFxuICAgIGhhbmRsZTogbnVsbCxcbiAgICBkcmFnZ2FibGU6IC9eW3VvXWwkL2kudGVzdChlbC5ub2RlTmFtZSkgPyAnPmxpJyA6ICc+KicsXG4gICAgc3dhcFRocmVzaG9sZDogMSxcbiAgICAvLyBwZXJjZW50YWdlOyAwIDw9IHggPD0gMVxuICAgIGludmVydFN3YXA6IGZhbHNlLFxuICAgIC8vIGludmVydCBhbHdheXNcbiAgICBpbnZlcnRlZFN3YXBUaHJlc2hvbGQ6IG51bGwsXG4gICAgLy8gd2lsbCBiZSBzZXQgdG8gc2FtZSBhcyBzd2FwVGhyZXNob2xkIGlmIGRlZmF1bHRcbiAgICByZW1vdmVDbG9uZU9uSGlkZTogdHJ1ZSxcbiAgICBkaXJlY3Rpb246IGZ1bmN0aW9uIGRpcmVjdGlvbigpIHtcbiAgICAgIHJldHVybiBfZGV0ZWN0RGlyZWN0aW9uKGVsLCB0aGlzLm9wdGlvbnMpO1xuICAgIH0sXG4gICAgZ2hvc3RDbGFzczogJ3NvcnRhYmxlLWdob3N0JyxcbiAgICBjaG9zZW5DbGFzczogJ3NvcnRhYmxlLWNob3NlbicsXG4gICAgZHJhZ0NsYXNzOiAnc29ydGFibGUtZHJhZycsXG4gICAgaWdub3JlOiAnYSwgaW1nJyxcbiAgICBmaWx0ZXI6IG51bGwsXG4gICAgcHJldmVudE9uRmlsdGVyOiB0cnVlLFxuICAgIGFuaW1hdGlvbjogMCxcbiAgICBlYXNpbmc6IG51bGwsXG4gICAgc2V0RGF0YTogZnVuY3Rpb24gc2V0RGF0YShkYXRhVHJhbnNmZXIsIGRyYWdFbCkge1xuICAgICAgZGF0YVRyYW5zZmVyLnNldERhdGEoJ1RleHQnLCBkcmFnRWwudGV4dENvbnRlbnQpO1xuICAgIH0sXG4gICAgZHJvcEJ1YmJsZTogZmFsc2UsXG4gICAgZHJhZ292ZXJCdWJibGU6IGZhbHNlLFxuICAgIGRhdGFJZEF0dHI6ICdkYXRhLWlkJyxcbiAgICBkZWxheTogMCxcbiAgICBkZWxheU9uVG91Y2hPbmx5OiBmYWxzZSxcbiAgICB0b3VjaFN0YXJ0VGhyZXNob2xkOiAoTnVtYmVyLnBhcnNlSW50ID8gTnVtYmVyIDogd2luZG93KS5wYXJzZUludCh3aW5kb3cuZGV2aWNlUGl4ZWxSYXRpbywgMTApIHx8IDEsXG4gICAgZm9yY2VGYWxsYmFjazogZmFsc2UsXG4gICAgZmFsbGJhY2tDbGFzczogJ3NvcnRhYmxlLWZhbGxiYWNrJyxcbiAgICBmYWxsYmFja09uQm9keTogZmFsc2UsXG4gICAgZmFsbGJhY2tUb2xlcmFuY2U6IDAsXG4gICAgZmFsbGJhY2tPZmZzZXQ6IHtcbiAgICAgIHg6IDAsXG4gICAgICB5OiAwXG4gICAgfSxcbiAgICBzdXBwb3J0UG9pbnRlcjogU29ydGFibGUuc3VwcG9ydFBvaW50ZXIgIT09IGZhbHNlICYmICdQb2ludGVyRXZlbnQnIGluIHdpbmRvdyAmJiAhU2FmYXJpLFxuICAgIGVtcHR5SW5zZXJ0VGhyZXNob2xkOiA1XG4gIH07XG4gIFBsdWdpbk1hbmFnZXIuaW5pdGlhbGl6ZVBsdWdpbnModGhpcywgZWwsIGRlZmF1bHRzKTsgLy8gU2V0IGRlZmF1bHQgb3B0aW9uc1xuXG4gIGZvciAodmFyIG5hbWUgaW4gZGVmYXVsdHMpIHtcbiAgICAhKG5hbWUgaW4gb3B0aW9ucykgJiYgKG9wdGlvbnNbbmFtZV0gPSBkZWZhdWx0c1tuYW1lXSk7XG4gIH1cblxuICBfcHJlcGFyZUdyb3VwKG9wdGlvbnMpOyAvLyBCaW5kIGFsbCBwcml2YXRlIG1ldGhvZHNcblxuXG4gIGZvciAodmFyIGZuIGluIHRoaXMpIHtcbiAgICBpZiAoZm4uY2hhckF0KDApID09PSAnXycgJiYgdHlwZW9mIHRoaXNbZm5dID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICB0aGlzW2ZuXSA9IHRoaXNbZm5dLmJpbmQodGhpcyk7XG4gICAgfVxuICB9IC8vIFNldHVwIGRyYWcgbW9kZVxuXG5cbiAgdGhpcy5uYXRpdmVEcmFnZ2FibGUgPSBvcHRpb25zLmZvcmNlRmFsbGJhY2sgPyBmYWxzZSA6IHN1cHBvcnREcmFnZ2FibGU7XG5cbiAgaWYgKHRoaXMubmF0aXZlRHJhZ2dhYmxlKSB7XG4gICAgLy8gVG91Y2ggc3RhcnQgdGhyZXNob2xkIGNhbm5vdCBiZSBncmVhdGVyIHRoYW4gdGhlIG5hdGl2ZSBkcmFnc3RhcnQgdGhyZXNob2xkXG4gICAgdGhpcy5vcHRpb25zLnRvdWNoU3RhcnRUaHJlc2hvbGQgPSAxO1xuICB9IC8vIEJpbmQgZXZlbnRzXG5cblxuICBpZiAob3B0aW9ucy5zdXBwb3J0UG9pbnRlcikge1xuICAgIG9uKGVsLCAncG9pbnRlcmRvd24nLCB0aGlzLl9vblRhcFN0YXJ0KTtcbiAgfSBlbHNlIHtcbiAgICBvbihlbCwgJ21vdXNlZG93bicsIHRoaXMuX29uVGFwU3RhcnQpO1xuICAgIG9uKGVsLCAndG91Y2hzdGFydCcsIHRoaXMuX29uVGFwU3RhcnQpO1xuICB9XG5cbiAgaWYgKHRoaXMubmF0aXZlRHJhZ2dhYmxlKSB7XG4gICAgb24oZWwsICdkcmFnb3ZlcicsIHRoaXMpO1xuICAgIG9uKGVsLCAnZHJhZ2VudGVyJywgdGhpcyk7XG4gIH1cblxuICBzb3J0YWJsZXMucHVzaCh0aGlzLmVsKTsgLy8gUmVzdG9yZSBzb3J0aW5nXG5cbiAgb3B0aW9ucy5zdG9yZSAmJiBvcHRpb25zLnN0b3JlLmdldCAmJiB0aGlzLnNvcnQob3B0aW9ucy5zdG9yZS5nZXQodGhpcykgfHwgW10pOyAvLyBBZGQgYW5pbWF0aW9uIHN0YXRlIG1hbmFnZXJcblxuICBfZXh0ZW5kcyh0aGlzLCBBbmltYXRpb25TdGF0ZU1hbmFnZXIoKSk7XG59XG5cblNvcnRhYmxlLnByb3RvdHlwZSA9XG4vKiogQGxlbmRzIFNvcnRhYmxlLnByb3RvdHlwZSAqL1xue1xuICBjb25zdHJ1Y3RvcjogU29ydGFibGUsXG4gIF9pc091dHNpZGVUaGlzRWw6IGZ1bmN0aW9uIF9pc091dHNpZGVUaGlzRWwodGFyZ2V0KSB7XG4gICAgaWYgKCF0aGlzLmVsLmNvbnRhaW5zKHRhcmdldCkgJiYgdGFyZ2V0ICE9PSB0aGlzLmVsKSB7XG4gICAgICBsYXN0VGFyZ2V0ID0gbnVsbDtcbiAgICB9XG4gIH0sXG4gIF9nZXREaXJlY3Rpb246IGZ1bmN0aW9uIF9nZXREaXJlY3Rpb24oZXZ0LCB0YXJnZXQpIHtcbiAgICByZXR1cm4gdHlwZW9mIHRoaXMub3B0aW9ucy5kaXJlY3Rpb24gPT09ICdmdW5jdGlvbicgPyB0aGlzLm9wdGlvbnMuZGlyZWN0aW9uLmNhbGwodGhpcywgZXZ0LCB0YXJnZXQsIGRyYWdFbCkgOiB0aGlzLm9wdGlvbnMuZGlyZWN0aW9uO1xuICB9LFxuICBfb25UYXBTdGFydDogZnVuY3Rpb24gX29uVGFwU3RhcnQoXG4gIC8qKiBFdmVudHxUb3VjaEV2ZW50ICovXG4gIGV2dCkge1xuICAgIGlmICghZXZ0LmNhbmNlbGFibGUpIHJldHVybjtcblxuICAgIHZhciBfdGhpcyA9IHRoaXMsXG4gICAgICAgIGVsID0gdGhpcy5lbCxcbiAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgcHJldmVudE9uRmlsdGVyID0gb3B0aW9ucy5wcmV2ZW50T25GaWx0ZXIsXG4gICAgICAgIHR5cGUgPSBldnQudHlwZSxcbiAgICAgICAgdG91Y2ggPSBldnQudG91Y2hlcyAmJiBldnQudG91Y2hlc1swXSB8fCBldnQucG9pbnRlclR5cGUgJiYgZXZ0LnBvaW50ZXJUeXBlID09PSAndG91Y2gnICYmIGV2dCxcbiAgICAgICAgdGFyZ2V0ID0gKHRvdWNoIHx8IGV2dCkudGFyZ2V0LFxuICAgICAgICBvcmlnaW5hbFRhcmdldCA9IGV2dC50YXJnZXQuc2hhZG93Um9vdCAmJiAoZXZ0LnBhdGggJiYgZXZ0LnBhdGhbMF0gfHwgZXZ0LmNvbXBvc2VkUGF0aCAmJiBldnQuY29tcG9zZWRQYXRoKClbMF0pIHx8IHRhcmdldCxcbiAgICAgICAgZmlsdGVyID0gb3B0aW9ucy5maWx0ZXI7XG5cbiAgICBfc2F2ZUlucHV0Q2hlY2tlZFN0YXRlKGVsKTsgLy8gRG9uJ3QgdHJpZ2dlciBzdGFydCBldmVudCB3aGVuIGFuIGVsZW1lbnQgaXMgYmVlbiBkcmFnZ2VkLCBvdGhlcndpc2UgdGhlIGV2dC5vbGRpbmRleCBhbHdheXMgd3Jvbmcgd2hlbiBzZXQgb3B0aW9uLmdyb3VwLlxuXG5cbiAgICBpZiAoZHJhZ0VsKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKC9tb3VzZWRvd258cG9pbnRlcmRvd24vLnRlc3QodHlwZSkgJiYgZXZ0LmJ1dHRvbiAhPT0gMCB8fCBvcHRpb25zLmRpc2FibGVkKSB7XG4gICAgICByZXR1cm47IC8vIG9ubHkgbGVmdCBidXR0b24gYW5kIGVuYWJsZWRcbiAgICB9IC8vIGNhbmNlbCBkbmQgaWYgb3JpZ2luYWwgdGFyZ2V0IGlzIGNvbnRlbnQgZWRpdGFibGVcblxuXG4gICAgaWYgKG9yaWdpbmFsVGFyZ2V0LmlzQ29udGVudEVkaXRhYmxlKSB7XG4gICAgICByZXR1cm47XG4gICAgfSAvLyBTYWZhcmkgaWdub3JlcyBmdXJ0aGVyIGV2ZW50IGhhbmRsaW5nIGFmdGVyIG1vdXNlZG93blxuXG5cbiAgICBpZiAoIXRoaXMubmF0aXZlRHJhZ2dhYmxlICYmIFNhZmFyaSAmJiB0YXJnZXQgJiYgdGFyZ2V0LnRhZ05hbWUudG9VcHBlckNhc2UoKSA9PT0gJ1NFTEVDVCcpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0YXJnZXQgPSBjbG9zZXN0KHRhcmdldCwgb3B0aW9ucy5kcmFnZ2FibGUsIGVsLCBmYWxzZSk7XG5cbiAgICBpZiAodGFyZ2V0ICYmIHRhcmdldC5hbmltYXRlZCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmIChsYXN0RG93bkVsID09PSB0YXJnZXQpIHtcbiAgICAgIC8vIElnbm9yaW5nIGR1cGxpY2F0ZSBgZG93bmBcbiAgICAgIHJldHVybjtcbiAgICB9IC8vIEdldCB0aGUgaW5kZXggb2YgdGhlIGRyYWdnZWQgZWxlbWVudCB3aXRoaW4gaXRzIHBhcmVudFxuXG5cbiAgICBvbGRJbmRleCA9IGluZGV4KHRhcmdldCk7XG4gICAgb2xkRHJhZ2dhYmxlSW5kZXggPSBpbmRleCh0YXJnZXQsIG9wdGlvbnMuZHJhZ2dhYmxlKTsgLy8gQ2hlY2sgZmlsdGVyXG5cbiAgICBpZiAodHlwZW9mIGZpbHRlciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgaWYgKGZpbHRlci5jYWxsKHRoaXMsIGV2dCwgdGFyZ2V0LCB0aGlzKSkge1xuICAgICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgc29ydGFibGU6IF90aGlzLFxuICAgICAgICAgIHJvb3RFbDogb3JpZ2luYWxUYXJnZXQsXG4gICAgICAgICAgbmFtZTogJ2ZpbHRlcicsXG4gICAgICAgICAgdGFyZ2V0RWw6IHRhcmdldCxcbiAgICAgICAgICB0b0VsOiBlbCxcbiAgICAgICAgICBmcm9tRWw6IGVsXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHBsdWdpbkV2ZW50KCdmaWx0ZXInLCBfdGhpcywge1xuICAgICAgICAgIGV2dDogZXZ0XG4gICAgICAgIH0pO1xuICAgICAgICBwcmV2ZW50T25GaWx0ZXIgJiYgZXZ0LmNhbmNlbGFibGUgJiYgZXZ0LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIHJldHVybjsgLy8gY2FuY2VsIGRuZFxuICAgICAgfVxuICAgIH0gZWxzZSBpZiAoZmlsdGVyKSB7XG4gICAgICBmaWx0ZXIgPSBmaWx0ZXIuc3BsaXQoJywnKS5zb21lKGZ1bmN0aW9uIChjcml0ZXJpYSkge1xuICAgICAgICBjcml0ZXJpYSA9IGNsb3Nlc3Qob3JpZ2luYWxUYXJnZXQsIGNyaXRlcmlhLnRyaW0oKSwgZWwsIGZhbHNlKTtcblxuICAgICAgICBpZiAoY3JpdGVyaWEpIHtcbiAgICAgICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgICBzb3J0YWJsZTogX3RoaXMsXG4gICAgICAgICAgICByb290RWw6IGNyaXRlcmlhLFxuICAgICAgICAgICAgbmFtZTogJ2ZpbHRlcicsXG4gICAgICAgICAgICB0YXJnZXRFbDogdGFyZ2V0LFxuICAgICAgICAgICAgZnJvbUVsOiBlbCxcbiAgICAgICAgICAgIHRvRWw6IGVsXG4gICAgICAgICAgfSk7XG5cbiAgICAgICAgICBwbHVnaW5FdmVudCgnZmlsdGVyJywgX3RoaXMsIHtcbiAgICAgICAgICAgIGV2dDogZXZ0XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuXG4gICAgICBpZiAoZmlsdGVyKSB7XG4gICAgICAgIHByZXZlbnRPbkZpbHRlciAmJiBldnQuY2FuY2VsYWJsZSAmJiBldnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgcmV0dXJuOyAvLyBjYW5jZWwgZG5kXG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKG9wdGlvbnMuaGFuZGxlICYmICFjbG9zZXN0KG9yaWdpbmFsVGFyZ2V0LCBvcHRpb25zLmhhbmRsZSwgZWwsIGZhbHNlKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH0gLy8gUHJlcGFyZSBgZHJhZ3N0YXJ0YFxuXG5cbiAgICB0aGlzLl9wcmVwYXJlRHJhZ1N0YXJ0KGV2dCwgdG91Y2gsIHRhcmdldCk7XG4gIH0sXG4gIF9wcmVwYXJlRHJhZ1N0YXJ0OiBmdW5jdGlvbiBfcHJlcGFyZURyYWdTdGFydChcbiAgLyoqIEV2ZW50ICovXG4gIGV2dCxcbiAgLyoqIFRvdWNoICovXG4gIHRvdWNoLFxuICAvKiogSFRNTEVsZW1lbnQgKi9cbiAgdGFyZ2V0KSB7XG4gICAgdmFyIF90aGlzID0gdGhpcyxcbiAgICAgICAgZWwgPSBfdGhpcy5lbCxcbiAgICAgICAgb3B0aW9ucyA9IF90aGlzLm9wdGlvbnMsXG4gICAgICAgIG93bmVyRG9jdW1lbnQgPSBlbC5vd25lckRvY3VtZW50LFxuICAgICAgICBkcmFnU3RhcnRGbjtcblxuICAgIGlmICh0YXJnZXQgJiYgIWRyYWdFbCAmJiB0YXJnZXQucGFyZW50Tm9kZSA9PT0gZWwpIHtcbiAgICAgIHZhciBkcmFnUmVjdCA9IGdldFJlY3QodGFyZ2V0KTtcbiAgICAgIHJvb3RFbCA9IGVsO1xuICAgICAgZHJhZ0VsID0gdGFyZ2V0O1xuICAgICAgcGFyZW50RWwgPSBkcmFnRWwucGFyZW50Tm9kZTtcbiAgICAgIG5leHRFbCA9IGRyYWdFbC5uZXh0U2libGluZztcbiAgICAgIGxhc3REb3duRWwgPSB0YXJnZXQ7XG4gICAgICBhY3RpdmVHcm91cCA9IG9wdGlvbnMuZ3JvdXA7XG4gICAgICBTb3J0YWJsZS5kcmFnZ2VkID0gZHJhZ0VsO1xuICAgICAgdGFwRXZ0ID0ge1xuICAgICAgICB0YXJnZXQ6IGRyYWdFbCxcbiAgICAgICAgY2xpZW50WDogKHRvdWNoIHx8IGV2dCkuY2xpZW50WCxcbiAgICAgICAgY2xpZW50WTogKHRvdWNoIHx8IGV2dCkuY2xpZW50WVxuICAgICAgfTtcbiAgICAgIHRhcERpc3RhbmNlTGVmdCA9IHRhcEV2dC5jbGllbnRYIC0gZHJhZ1JlY3QubGVmdDtcbiAgICAgIHRhcERpc3RhbmNlVG9wID0gdGFwRXZ0LmNsaWVudFkgLSBkcmFnUmVjdC50b3A7XG4gICAgICB0aGlzLl9sYXN0WCA9ICh0b3VjaCB8fCBldnQpLmNsaWVudFg7XG4gICAgICB0aGlzLl9sYXN0WSA9ICh0b3VjaCB8fCBldnQpLmNsaWVudFk7XG4gICAgICBkcmFnRWwuc3R5bGVbJ3dpbGwtY2hhbmdlJ10gPSAnYWxsJztcblxuICAgICAgZHJhZ1N0YXJ0Rm4gPSBmdW5jdGlvbiBkcmFnU3RhcnRGbigpIHtcbiAgICAgICAgcGx1Z2luRXZlbnQoJ2RlbGF5RW5kZWQnLCBfdGhpcywge1xuICAgICAgICAgIGV2dDogZXZ0XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGlmIChTb3J0YWJsZS5ldmVudENhbmNlbGVkKSB7XG4gICAgICAgICAgX3RoaXMuX29uRHJvcCgpO1xuXG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9IC8vIERlbGF5ZWQgZHJhZyBoYXMgYmVlbiB0cmlnZ2VyZWRcbiAgICAgICAgLy8gd2UgY2FuIHJlLWVuYWJsZSB0aGUgZXZlbnRzOiB0b3VjaG1vdmUvbW91c2Vtb3ZlXG5cblxuICAgICAgICBfdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnRXZlbnRzKCk7XG5cbiAgICAgICAgaWYgKCFGaXJlRm94ICYmIF90aGlzLm5hdGl2ZURyYWdnYWJsZSkge1xuICAgICAgICAgIGRyYWdFbC5kcmFnZ2FibGUgPSB0cnVlO1xuICAgICAgICB9IC8vIEJpbmQgdGhlIGV2ZW50czogZHJhZ3N0YXJ0L2RyYWdlbmRcblxuXG4gICAgICAgIF90aGlzLl90cmlnZ2VyRHJhZ1N0YXJ0KGV2dCwgdG91Y2gpOyAvLyBEcmFnIHN0YXJ0IGV2ZW50XG5cblxuICAgICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgc29ydGFibGU6IF90aGlzLFxuICAgICAgICAgIG5hbWU6ICdjaG9vc2UnLFxuICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICB9KTsgLy8gQ2hvc2VuIGl0ZW1cblxuXG4gICAgICAgIHRvZ2dsZUNsYXNzKGRyYWdFbCwgb3B0aW9ucy5jaG9zZW5DbGFzcywgdHJ1ZSk7XG4gICAgICB9OyAvLyBEaXNhYmxlIFwiZHJhZ2dhYmxlXCJcblxuXG4gICAgICBvcHRpb25zLmlnbm9yZS5zcGxpdCgnLCcpLmZvckVhY2goZnVuY3Rpb24gKGNyaXRlcmlhKSB7XG4gICAgICAgIGZpbmQoZHJhZ0VsLCBjcml0ZXJpYS50cmltKCksIF9kaXNhYmxlRHJhZ2dhYmxlKTtcbiAgICAgIH0pO1xuICAgICAgb24ob3duZXJEb2N1bWVudCwgJ2RyYWdvdmVyJywgbmVhcmVzdEVtcHR5SW5zZXJ0RGV0ZWN0RXZlbnQpO1xuICAgICAgb24ob3duZXJEb2N1bWVudCwgJ21vdXNlbW92ZScsIG5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50KTtcbiAgICAgIG9uKG93bmVyRG9jdW1lbnQsICd0b3VjaG1vdmUnLCBuZWFyZXN0RW1wdHlJbnNlcnREZXRlY3RFdmVudCk7XG4gICAgICBvbihvd25lckRvY3VtZW50LCAnbW91c2V1cCcsIF90aGlzLl9vbkRyb3ApO1xuICAgICAgb24ob3duZXJEb2N1bWVudCwgJ3RvdWNoZW5kJywgX3RoaXMuX29uRHJvcCk7XG4gICAgICBvbihvd25lckRvY3VtZW50LCAndG91Y2hjYW5jZWwnLCBfdGhpcy5fb25Ecm9wKTsgLy8gTWFrZSBkcmFnRWwgZHJhZ2dhYmxlIChtdXN0IGJlIGJlZm9yZSBkZWxheSBmb3IgRmlyZUZveClcblxuICAgICAgaWYgKEZpcmVGb3ggJiYgdGhpcy5uYXRpdmVEcmFnZ2FibGUpIHtcbiAgICAgICAgdGhpcy5vcHRpb25zLnRvdWNoU3RhcnRUaHJlc2hvbGQgPSA0O1xuICAgICAgICBkcmFnRWwuZHJhZ2dhYmxlID0gdHJ1ZTtcbiAgICAgIH1cblxuICAgICAgcGx1Z2luRXZlbnQoJ2RlbGF5U3RhcnQnLCB0aGlzLCB7XG4gICAgICAgIGV2dDogZXZ0XG4gICAgICB9KTsgLy8gRGVsYXkgaXMgaW1wb3NzaWJsZSBmb3IgbmF0aXZlIERuRCBpbiBFZGdlIG9yIElFXG5cbiAgICAgIGlmIChvcHRpb25zLmRlbGF5ICYmICghb3B0aW9ucy5kZWxheU9uVG91Y2hPbmx5IHx8IHRvdWNoKSAmJiAoIXRoaXMubmF0aXZlRHJhZ2dhYmxlIHx8ICEoRWRnZSB8fCBJRTExT3JMZXNzKSkpIHtcbiAgICAgICAgaWYgKFNvcnRhYmxlLmV2ZW50Q2FuY2VsZWQpIHtcbiAgICAgICAgICB0aGlzLl9vbkRyb3AoKTtcblxuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfSAvLyBJZiB0aGUgdXNlciBtb3ZlcyB0aGUgcG9pbnRlciBvciBsZXQgZ28gdGhlIGNsaWNrIG9yIHRvdWNoXG4gICAgICAgIC8vIGJlZm9yZSB0aGUgZGVsYXkgaGFzIGJlZW4gcmVhY2hlZDpcbiAgICAgICAgLy8gZGlzYWJsZSB0aGUgZGVsYXllZCBkcmFnXG5cblxuICAgICAgICBvbihvd25lckRvY3VtZW50LCAnbW91c2V1cCcsIF90aGlzLl9kaXNhYmxlRGVsYXllZERyYWcpO1xuICAgICAgICBvbihvd25lckRvY3VtZW50LCAndG91Y2hlbmQnLCBfdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnKTtcbiAgICAgICAgb24ob3duZXJEb2N1bWVudCwgJ3RvdWNoY2FuY2VsJywgX3RoaXMuX2Rpc2FibGVEZWxheWVkRHJhZyk7XG4gICAgICAgIG9uKG93bmVyRG9jdW1lbnQsICdtb3VzZW1vdmUnLCBfdGhpcy5fZGVsYXllZERyYWdUb3VjaE1vdmVIYW5kbGVyKTtcbiAgICAgICAgb24ob3duZXJEb2N1bWVudCwgJ3RvdWNobW92ZScsIF90aGlzLl9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXIpO1xuICAgICAgICBvcHRpb25zLnN1cHBvcnRQb2ludGVyICYmIG9uKG93bmVyRG9jdW1lbnQsICdwb2ludGVybW92ZScsIF90aGlzLl9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXIpO1xuICAgICAgICBfdGhpcy5fZHJhZ1N0YXJ0VGltZXIgPSBzZXRUaW1lb3V0KGRyYWdTdGFydEZuLCBvcHRpb25zLmRlbGF5KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGRyYWdTdGFydEZuKCk7XG4gICAgICB9XG4gICAgfVxuICB9LFxuICBfZGVsYXllZERyYWdUb3VjaE1vdmVIYW5kbGVyOiBmdW5jdGlvbiBfZGVsYXllZERyYWdUb3VjaE1vdmVIYW5kbGVyKFxuICAvKiogVG91Y2hFdmVudHxQb2ludGVyRXZlbnQgKiovXG4gIGUpIHtcbiAgICB2YXIgdG91Y2ggPSBlLnRvdWNoZXMgPyBlLnRvdWNoZXNbMF0gOiBlO1xuXG4gICAgaWYgKE1hdGgubWF4KE1hdGguYWJzKHRvdWNoLmNsaWVudFggLSB0aGlzLl9sYXN0WCksIE1hdGguYWJzKHRvdWNoLmNsaWVudFkgLSB0aGlzLl9sYXN0WSkpID49IE1hdGguZmxvb3IodGhpcy5vcHRpb25zLnRvdWNoU3RhcnRUaHJlc2hvbGQgLyAodGhpcy5uYXRpdmVEcmFnZ2FibGUgJiYgd2luZG93LmRldmljZVBpeGVsUmF0aW8gfHwgMSkpKSB7XG4gICAgICB0aGlzLl9kaXNhYmxlRGVsYXllZERyYWcoKTtcbiAgICB9XG4gIH0sXG4gIF9kaXNhYmxlRGVsYXllZERyYWc6IGZ1bmN0aW9uIF9kaXNhYmxlRGVsYXllZERyYWcoKSB7XG4gICAgZHJhZ0VsICYmIF9kaXNhYmxlRHJhZ2dhYmxlKGRyYWdFbCk7XG4gICAgY2xlYXJUaW1lb3V0KHRoaXMuX2RyYWdTdGFydFRpbWVyKTtcblxuICAgIHRoaXMuX2Rpc2FibGVEZWxheWVkRHJhZ0V2ZW50cygpO1xuICB9LFxuICBfZGlzYWJsZURlbGF5ZWREcmFnRXZlbnRzOiBmdW5jdGlvbiBfZGlzYWJsZURlbGF5ZWREcmFnRXZlbnRzKCkge1xuICAgIHZhciBvd25lckRvY3VtZW50ID0gdGhpcy5lbC5vd25lckRvY3VtZW50O1xuICAgIG9mZihvd25lckRvY3VtZW50LCAnbW91c2V1cCcsIHRoaXMuX2Rpc2FibGVEZWxheWVkRHJhZyk7XG4gICAgb2ZmKG93bmVyRG9jdW1lbnQsICd0b3VjaGVuZCcsIHRoaXMuX2Rpc2FibGVEZWxheWVkRHJhZyk7XG4gICAgb2ZmKG93bmVyRG9jdW1lbnQsICd0b3VjaGNhbmNlbCcsIHRoaXMuX2Rpc2FibGVEZWxheWVkRHJhZyk7XG4gICAgb2ZmKG93bmVyRG9jdW1lbnQsICdtb3VzZW1vdmUnLCB0aGlzLl9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXIpO1xuICAgIG9mZihvd25lckRvY3VtZW50LCAndG91Y2htb3ZlJywgdGhpcy5fZGVsYXllZERyYWdUb3VjaE1vdmVIYW5kbGVyKTtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ3BvaW50ZXJtb3ZlJywgdGhpcy5fZGVsYXllZERyYWdUb3VjaE1vdmVIYW5kbGVyKTtcbiAgfSxcbiAgX3RyaWdnZXJEcmFnU3RhcnQ6IGZ1bmN0aW9uIF90cmlnZ2VyRHJhZ1N0YXJ0KFxuICAvKiogRXZlbnQgKi9cbiAgZXZ0LFxuICAvKiogVG91Y2ggKi9cbiAgdG91Y2gpIHtcbiAgICB0b3VjaCA9IHRvdWNoIHx8IGV2dC5wb2ludGVyVHlwZSA9PSAndG91Y2gnICYmIGV2dDtcblxuICAgIGlmICghdGhpcy5uYXRpdmVEcmFnZ2FibGUgfHwgdG91Y2gpIHtcbiAgICAgIGlmICh0aGlzLm9wdGlvbnMuc3VwcG9ydFBvaW50ZXIpIHtcbiAgICAgICAgb24oZG9jdW1lbnQsICdwb2ludGVybW92ZScsIHRoaXMuX29uVG91Y2hNb3ZlKTtcbiAgICAgIH0gZWxzZSBpZiAodG91Y2gpIHtcbiAgICAgICAgb24oZG9jdW1lbnQsICd0b3VjaG1vdmUnLCB0aGlzLl9vblRvdWNoTW92ZSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBvbihkb2N1bWVudCwgJ21vdXNlbW92ZScsIHRoaXMuX29uVG91Y2hNb3ZlKTtcbiAgICAgIH1cbiAgICB9IGVsc2Uge1xuICAgICAgb24oZHJhZ0VsLCAnZHJhZ2VuZCcsIHRoaXMpO1xuICAgICAgb24ocm9vdEVsLCAnZHJhZ3N0YXJ0JywgdGhpcy5fb25EcmFnU3RhcnQpO1xuICAgIH1cblxuICAgIHRyeSB7XG4gICAgICBpZiAoZG9jdW1lbnQuc2VsZWN0aW9uKSB7XG4gICAgICAgIC8vIFRpbWVvdXQgbmVjY2Vzc2FyeSBmb3IgSUU5XG4gICAgICAgIF9uZXh0VGljayhmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgZG9jdW1lbnQuc2VsZWN0aW9uLmVtcHR5KCk7XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgd2luZG93LmdldFNlbGVjdGlvbigpLnJlbW92ZUFsbFJhbmdlcygpO1xuICAgICAgfVxuICAgIH0gY2F0Y2ggKGVycikge31cbiAgfSxcbiAgX2RyYWdTdGFydGVkOiBmdW5jdGlvbiBfZHJhZ1N0YXJ0ZWQoZmFsbGJhY2ssIGV2dCkge1xuXG4gICAgYXdhaXRpbmdEcmFnU3RhcnRlZCA9IGZhbHNlO1xuXG4gICAgaWYgKHJvb3RFbCAmJiBkcmFnRWwpIHtcbiAgICAgIHBsdWdpbkV2ZW50KCdkcmFnU3RhcnRlZCcsIHRoaXMsIHtcbiAgICAgICAgZXZ0OiBldnRcbiAgICAgIH0pO1xuXG4gICAgICBpZiAodGhpcy5uYXRpdmVEcmFnZ2FibGUpIHtcbiAgICAgICAgb24oZG9jdW1lbnQsICdkcmFnb3ZlcicsIF9jaGVja091dHNpZGVUYXJnZXRFbCk7XG4gICAgICB9XG5cbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zOyAvLyBBcHBseSBlZmZlY3RcblxuICAgICAgIWZhbGxiYWNrICYmIHRvZ2dsZUNsYXNzKGRyYWdFbCwgb3B0aW9ucy5kcmFnQ2xhc3MsIGZhbHNlKTtcbiAgICAgIHRvZ2dsZUNsYXNzKGRyYWdFbCwgb3B0aW9ucy5naG9zdENsYXNzLCB0cnVlKTtcbiAgICAgIFNvcnRhYmxlLmFjdGl2ZSA9IHRoaXM7XG4gICAgICBmYWxsYmFjayAmJiB0aGlzLl9hcHBlbmRHaG9zdCgpOyAvLyBEcmFnIHN0YXJ0IGV2ZW50XG5cbiAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgc29ydGFibGU6IHRoaXMsXG4gICAgICAgIG5hbWU6ICdzdGFydCcsXG4gICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuX251bGxpbmcoKTtcbiAgICB9XG4gIH0sXG4gIF9lbXVsYXRlRHJhZ092ZXI6IGZ1bmN0aW9uIF9lbXVsYXRlRHJhZ092ZXIoKSB7XG4gICAgaWYgKHRvdWNoRXZ0KSB7XG4gICAgICB0aGlzLl9sYXN0WCA9IHRvdWNoRXZ0LmNsaWVudFg7XG4gICAgICB0aGlzLl9sYXN0WSA9IHRvdWNoRXZ0LmNsaWVudFk7XG5cbiAgICAgIF9oaWRlR2hvc3RGb3JUYXJnZXQoKTtcblxuICAgICAgdmFyIHRhcmdldCA9IGRvY3VtZW50LmVsZW1lbnRGcm9tUG9pbnQodG91Y2hFdnQuY2xpZW50WCwgdG91Y2hFdnQuY2xpZW50WSk7XG4gICAgICB2YXIgcGFyZW50ID0gdGFyZ2V0O1xuXG4gICAgICB3aGlsZSAodGFyZ2V0ICYmIHRhcmdldC5zaGFkb3dSb290KSB7XG4gICAgICAgIHRhcmdldCA9IHRhcmdldC5zaGFkb3dSb290LmVsZW1lbnRGcm9tUG9pbnQodG91Y2hFdnQuY2xpZW50WCwgdG91Y2hFdnQuY2xpZW50WSk7XG4gICAgICAgIGlmICh0YXJnZXQgPT09IHBhcmVudCkgYnJlYWs7XG4gICAgICAgIHBhcmVudCA9IHRhcmdldDtcbiAgICAgIH1cblxuICAgICAgZHJhZ0VsLnBhcmVudE5vZGVbZXhwYW5kb10uX2lzT3V0c2lkZVRoaXNFbCh0YXJnZXQpO1xuXG4gICAgICBpZiAocGFyZW50KSB7XG4gICAgICAgIGRvIHtcbiAgICAgICAgICBpZiAocGFyZW50W2V4cGFuZG9dKSB7XG4gICAgICAgICAgICB2YXIgaW5zZXJ0ZWQgPSB2b2lkIDA7XG4gICAgICAgICAgICBpbnNlcnRlZCA9IHBhcmVudFtleHBhbmRvXS5fb25EcmFnT3Zlcih7XG4gICAgICAgICAgICAgIGNsaWVudFg6IHRvdWNoRXZ0LmNsaWVudFgsXG4gICAgICAgICAgICAgIGNsaWVudFk6IHRvdWNoRXZ0LmNsaWVudFksXG4gICAgICAgICAgICAgIHRhcmdldDogdGFyZ2V0LFxuICAgICAgICAgICAgICByb290RWw6IHBhcmVudFxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGlmIChpbnNlcnRlZCAmJiAhdGhpcy5vcHRpb25zLmRyYWdvdmVyQnViYmxlKSB7XG4gICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cblxuICAgICAgICAgIHRhcmdldCA9IHBhcmVudDsgLy8gc3RvcmUgbGFzdCBlbGVtZW50XG4gICAgICAgIH1cbiAgICAgICAgLyoganNoaW50IGJvc3M6dHJ1ZSAqL1xuICAgICAgICB3aGlsZSAocGFyZW50ID0gcGFyZW50LnBhcmVudE5vZGUpO1xuICAgICAgfVxuXG4gICAgICBfdW5oaWRlR2hvc3RGb3JUYXJnZXQoKTtcbiAgICB9XG4gIH0sXG4gIF9vblRvdWNoTW92ZTogZnVuY3Rpb24gX29uVG91Y2hNb3ZlKFxuICAvKipUb3VjaEV2ZW50Ki9cbiAgZXZ0KSB7XG4gICAgaWYgKHRhcEV2dCkge1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgICAgZmFsbGJhY2tUb2xlcmFuY2UgPSBvcHRpb25zLmZhbGxiYWNrVG9sZXJhbmNlLFxuICAgICAgICAgIGZhbGxiYWNrT2Zmc2V0ID0gb3B0aW9ucy5mYWxsYmFja09mZnNldCxcbiAgICAgICAgICB0b3VjaCA9IGV2dC50b3VjaGVzID8gZXZ0LnRvdWNoZXNbMF0gOiBldnQsXG4gICAgICAgICAgZ2hvc3RNYXRyaXggPSBnaG9zdEVsICYmIG1hdHJpeChnaG9zdEVsLCB0cnVlKSxcbiAgICAgICAgICBzY2FsZVggPSBnaG9zdEVsICYmIGdob3N0TWF0cml4ICYmIGdob3N0TWF0cml4LmEsXG4gICAgICAgICAgc2NhbGVZID0gZ2hvc3RFbCAmJiBnaG9zdE1hdHJpeCAmJiBnaG9zdE1hdHJpeC5kLFxuICAgICAgICAgIHJlbGF0aXZlU2Nyb2xsT2Zmc2V0ID0gUG9zaXRpb25HaG9zdEFic29sdXRlbHkgJiYgZ2hvc3RSZWxhdGl2ZVBhcmVudCAmJiBnZXRSZWxhdGl2ZVNjcm9sbE9mZnNldChnaG9zdFJlbGF0aXZlUGFyZW50KSxcbiAgICAgICAgICBkeCA9ICh0b3VjaC5jbGllbnRYIC0gdGFwRXZ0LmNsaWVudFggKyBmYWxsYmFja09mZnNldC54KSAvIChzY2FsZVggfHwgMSkgKyAocmVsYXRpdmVTY3JvbGxPZmZzZXQgPyByZWxhdGl2ZVNjcm9sbE9mZnNldFswXSAtIGdob3N0UmVsYXRpdmVQYXJlbnRJbml0aWFsU2Nyb2xsWzBdIDogMCkgLyAoc2NhbGVYIHx8IDEpLFxuICAgICAgICAgIGR5ID0gKHRvdWNoLmNsaWVudFkgLSB0YXBFdnQuY2xpZW50WSArIGZhbGxiYWNrT2Zmc2V0LnkpIC8gKHNjYWxlWSB8fCAxKSArIChyZWxhdGl2ZVNjcm9sbE9mZnNldCA/IHJlbGF0aXZlU2Nyb2xsT2Zmc2V0WzFdIC0gZ2hvc3RSZWxhdGl2ZVBhcmVudEluaXRpYWxTY3JvbGxbMV0gOiAwKSAvIChzY2FsZVkgfHwgMSk7IC8vIG9ubHkgc2V0IHRoZSBzdGF0dXMgdG8gZHJhZ2dpbmcsIHdoZW4gd2UgYXJlIGFjdHVhbGx5IGRyYWdnaW5nXG5cbiAgICAgIGlmICghU29ydGFibGUuYWN0aXZlICYmICFhd2FpdGluZ0RyYWdTdGFydGVkKSB7XG4gICAgICAgIGlmIChmYWxsYmFja1RvbGVyYW5jZSAmJiBNYXRoLm1heChNYXRoLmFicyh0b3VjaC5jbGllbnRYIC0gdGhpcy5fbGFzdFgpLCBNYXRoLmFicyh0b3VjaC5jbGllbnRZIC0gdGhpcy5fbGFzdFkpKSA8IGZhbGxiYWNrVG9sZXJhbmNlKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy5fb25EcmFnU3RhcnQoZXZ0LCB0cnVlKTtcbiAgICAgIH1cblxuICAgICAgaWYgKGdob3N0RWwpIHtcbiAgICAgICAgaWYgKGdob3N0TWF0cml4KSB7XG4gICAgICAgICAgZ2hvc3RNYXRyaXguZSArPSBkeCAtIChsYXN0RHggfHwgMCk7XG4gICAgICAgICAgZ2hvc3RNYXRyaXguZiArPSBkeSAtIChsYXN0RHkgfHwgMCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgZ2hvc3RNYXRyaXggPSB7XG4gICAgICAgICAgICBhOiAxLFxuICAgICAgICAgICAgYjogMCxcbiAgICAgICAgICAgIGM6IDAsXG4gICAgICAgICAgICBkOiAxLFxuICAgICAgICAgICAgZTogZHgsXG4gICAgICAgICAgICBmOiBkeVxuICAgICAgICAgIH07XG4gICAgICAgIH1cblxuICAgICAgICB2YXIgY3NzTWF0cml4ID0gXCJtYXRyaXgoXCIuY29uY2F0KGdob3N0TWF0cml4LmEsIFwiLFwiKS5jb25jYXQoZ2hvc3RNYXRyaXguYiwgXCIsXCIpLmNvbmNhdChnaG9zdE1hdHJpeC5jLCBcIixcIikuY29uY2F0KGdob3N0TWF0cml4LmQsIFwiLFwiKS5jb25jYXQoZ2hvc3RNYXRyaXguZSwgXCIsXCIpLmNvbmNhdChnaG9zdE1hdHJpeC5mLCBcIilcIik7XG4gICAgICAgIGNzcyhnaG9zdEVsLCAnd2Via2l0VHJhbnNmb3JtJywgY3NzTWF0cml4KTtcbiAgICAgICAgY3NzKGdob3N0RWwsICdtb3pUcmFuc2Zvcm0nLCBjc3NNYXRyaXgpO1xuICAgICAgICBjc3MoZ2hvc3RFbCwgJ21zVHJhbnNmb3JtJywgY3NzTWF0cml4KTtcbiAgICAgICAgY3NzKGdob3N0RWwsICd0cmFuc2Zvcm0nLCBjc3NNYXRyaXgpO1xuICAgICAgICBsYXN0RHggPSBkeDtcbiAgICAgICAgbGFzdER5ID0gZHk7XG4gICAgICAgIHRvdWNoRXZ0ID0gdG91Y2g7XG4gICAgICB9XG5cbiAgICAgIGV2dC5jYW5jZWxhYmxlICYmIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIH1cbiAgfSxcbiAgX2FwcGVuZEdob3N0OiBmdW5jdGlvbiBfYXBwZW5kR2hvc3QoKSB7XG4gICAgLy8gQnVnIGlmIHVzaW5nIHNjYWxlKCk6IGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vcXVlc3Rpb25zLzI2MzcwNThcbiAgICAvLyBOb3QgYmVpbmcgYWRqdXN0ZWQgZm9yXG4gICAgaWYgKCFnaG9zdEVsKSB7XG4gICAgICB2YXIgY29udGFpbmVyID0gdGhpcy5vcHRpb25zLmZhbGxiYWNrT25Cb2R5ID8gZG9jdW1lbnQuYm9keSA6IHJvb3RFbCxcbiAgICAgICAgICByZWN0ID0gZ2V0UmVjdChkcmFnRWwsIHRydWUsIFBvc2l0aW9uR2hvc3RBYnNvbHV0ZWx5LCB0cnVlLCBjb250YWluZXIpLFxuICAgICAgICAgIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7IC8vIFBvc2l0aW9uIGFic29sdXRlbHlcblxuICAgICAgaWYgKFBvc2l0aW9uR2hvc3RBYnNvbHV0ZWx5KSB7XG4gICAgICAgIC8vIEdldCByZWxhdGl2ZWx5IHBvc2l0aW9uZWQgcGFyZW50XG4gICAgICAgIGdob3N0UmVsYXRpdmVQYXJlbnQgPSBjb250YWluZXI7XG5cbiAgICAgICAgd2hpbGUgKGNzcyhnaG9zdFJlbGF0aXZlUGFyZW50LCAncG9zaXRpb24nKSA9PT0gJ3N0YXRpYycgJiYgY3NzKGdob3N0UmVsYXRpdmVQYXJlbnQsICd0cmFuc2Zvcm0nKSA9PT0gJ25vbmUnICYmIGdob3N0UmVsYXRpdmVQYXJlbnQgIT09IGRvY3VtZW50KSB7XG4gICAgICAgICAgZ2hvc3RSZWxhdGl2ZVBhcmVudCA9IGdob3N0UmVsYXRpdmVQYXJlbnQucGFyZW50Tm9kZTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChnaG9zdFJlbGF0aXZlUGFyZW50ICE9PSBkb2N1bWVudC5ib2R5ICYmIGdob3N0UmVsYXRpdmVQYXJlbnQgIT09IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudCkge1xuICAgICAgICAgIGlmIChnaG9zdFJlbGF0aXZlUGFyZW50ID09PSBkb2N1bWVudCkgZ2hvc3RSZWxhdGl2ZVBhcmVudCA9IGdldFdpbmRvd1Njcm9sbGluZ0VsZW1lbnQoKTtcbiAgICAgICAgICByZWN0LnRvcCArPSBnaG9zdFJlbGF0aXZlUGFyZW50LnNjcm9sbFRvcDtcbiAgICAgICAgICByZWN0LmxlZnQgKz0gZ2hvc3RSZWxhdGl2ZVBhcmVudC5zY3JvbGxMZWZ0O1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGdob3N0UmVsYXRpdmVQYXJlbnQgPSBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCk7XG4gICAgICAgIH1cblxuICAgICAgICBnaG9zdFJlbGF0aXZlUGFyZW50SW5pdGlhbFNjcm9sbCA9IGdldFJlbGF0aXZlU2Nyb2xsT2Zmc2V0KGdob3N0UmVsYXRpdmVQYXJlbnQpO1xuICAgICAgfVxuXG4gICAgICBnaG9zdEVsID0gZHJhZ0VsLmNsb25lTm9kZSh0cnVlKTtcbiAgICAgIHRvZ2dsZUNsYXNzKGdob3N0RWwsIG9wdGlvbnMuZ2hvc3RDbGFzcywgZmFsc2UpO1xuICAgICAgdG9nZ2xlQ2xhc3MoZ2hvc3RFbCwgb3B0aW9ucy5mYWxsYmFja0NsYXNzLCB0cnVlKTtcbiAgICAgIHRvZ2dsZUNsYXNzKGdob3N0RWwsIG9wdGlvbnMuZHJhZ0NsYXNzLCB0cnVlKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAndHJhbnNpdGlvbicsICcnKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAndHJhbnNmb3JtJywgJycpO1xuICAgICAgY3NzKGdob3N0RWwsICdib3gtc2l6aW5nJywgJ2JvcmRlci1ib3gnKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAnbWFyZ2luJywgMCk7XG4gICAgICBjc3MoZ2hvc3RFbCwgJ3RvcCcsIHJlY3QudG9wKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAnbGVmdCcsIHJlY3QubGVmdCk7XG4gICAgICBjc3MoZ2hvc3RFbCwgJ3dpZHRoJywgcmVjdC53aWR0aCk7XG4gICAgICBjc3MoZ2hvc3RFbCwgJ2hlaWdodCcsIHJlY3QuaGVpZ2h0KTtcbiAgICAgIGNzcyhnaG9zdEVsLCAnb3BhY2l0eScsICcwLjgnKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAncG9zaXRpb24nLCBQb3NpdGlvbkdob3N0QWJzb2x1dGVseSA/ICdhYnNvbHV0ZScgOiAnZml4ZWQnKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAnekluZGV4JywgJzEwMDAwMCcpO1xuICAgICAgY3NzKGdob3N0RWwsICdwb2ludGVyRXZlbnRzJywgJ25vbmUnKTtcbiAgICAgIFNvcnRhYmxlLmdob3N0ID0gZ2hvc3RFbDtcbiAgICAgIGNvbnRhaW5lci5hcHBlbmRDaGlsZChnaG9zdEVsKTsgLy8gU2V0IHRyYW5zZm9ybS1vcmlnaW5cblxuICAgICAgY3NzKGdob3N0RWwsICd0cmFuc2Zvcm0tb3JpZ2luJywgdGFwRGlzdGFuY2VMZWZ0IC8gcGFyc2VJbnQoZ2hvc3RFbC5zdHlsZS53aWR0aCkgKiAxMDAgKyAnJSAnICsgdGFwRGlzdGFuY2VUb3AgLyBwYXJzZUludChnaG9zdEVsLnN0eWxlLmhlaWdodCkgKiAxMDAgKyAnJScpO1xuICAgIH1cbiAgfSxcbiAgX29uRHJhZ1N0YXJ0OiBmdW5jdGlvbiBfb25EcmFnU3RhcnQoXG4gIC8qKkV2ZW50Ki9cbiAgZXZ0LFxuICAvKipib29sZWFuKi9cbiAgZmFsbGJhY2spIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuXG4gICAgdmFyIGRhdGFUcmFuc2ZlciA9IGV2dC5kYXRhVHJhbnNmZXI7XG4gICAgdmFyIG9wdGlvbnMgPSBfdGhpcy5vcHRpb25zO1xuICAgIHBsdWdpbkV2ZW50KCdkcmFnU3RhcnQnLCB0aGlzLCB7XG4gICAgICBldnQ6IGV2dFxuICAgIH0pO1xuXG4gICAgaWYgKFNvcnRhYmxlLmV2ZW50Q2FuY2VsZWQpIHtcbiAgICAgIHRoaXMuX29uRHJvcCgpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgcGx1Z2luRXZlbnQoJ3NldHVwQ2xvbmUnLCB0aGlzKTtcblxuICAgIGlmICghU29ydGFibGUuZXZlbnRDYW5jZWxlZCkge1xuICAgICAgY2xvbmVFbCA9IGNsb25lKGRyYWdFbCk7XG4gICAgICBjbG9uZUVsLnJlbW92ZUF0dHJpYnV0ZShcImlkXCIpO1xuICAgICAgY2xvbmVFbC5kcmFnZ2FibGUgPSBmYWxzZTtcbiAgICAgIGNsb25lRWwuc3R5bGVbJ3dpbGwtY2hhbmdlJ10gPSAnJztcblxuICAgICAgdGhpcy5faGlkZUNsb25lKCk7XG5cbiAgICAgIHRvZ2dsZUNsYXNzKGNsb25lRWwsIHRoaXMub3B0aW9ucy5jaG9zZW5DbGFzcywgZmFsc2UpO1xuICAgICAgU29ydGFibGUuY2xvbmUgPSBjbG9uZUVsO1xuICAgIH0gLy8gIzExNDM6IElGcmFtZSBzdXBwb3J0IHdvcmthcm91bmRcblxuXG4gICAgX3RoaXMuY2xvbmVJZCA9IF9uZXh0VGljayhmdW5jdGlvbiAoKSB7XG4gICAgICBwbHVnaW5FdmVudCgnY2xvbmUnLCBfdGhpcyk7XG4gICAgICBpZiAoU29ydGFibGUuZXZlbnRDYW5jZWxlZCkgcmV0dXJuO1xuXG4gICAgICBpZiAoIV90aGlzLm9wdGlvbnMucmVtb3ZlQ2xvbmVPbkhpZGUpIHtcbiAgICAgICAgcm9vdEVsLmluc2VydEJlZm9yZShjbG9uZUVsLCBkcmFnRWwpO1xuICAgICAgfVxuXG4gICAgICBfdGhpcy5faGlkZUNsb25lKCk7XG5cbiAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgc29ydGFibGU6IF90aGlzLFxuICAgICAgICBuYW1lOiAnY2xvbmUnXG4gICAgICB9KTtcbiAgICB9KTtcbiAgICAhZmFsbGJhY2sgJiYgdG9nZ2xlQ2xhc3MoZHJhZ0VsLCBvcHRpb25zLmRyYWdDbGFzcywgdHJ1ZSk7IC8vIFNldCBwcm9wZXIgZHJvcCBldmVudHNcblxuICAgIGlmIChmYWxsYmFjaykge1xuICAgICAgaWdub3JlTmV4dENsaWNrID0gdHJ1ZTtcbiAgICAgIF90aGlzLl9sb29wSWQgPSBzZXRJbnRlcnZhbChfdGhpcy5fZW11bGF0ZURyYWdPdmVyLCA1MCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIC8vIFVuZG8gd2hhdCB3YXMgc2V0IGluIF9wcmVwYXJlRHJhZ1N0YXJ0IGJlZm9yZSBkcmFnIHN0YXJ0ZWRcbiAgICAgIG9mZihkb2N1bWVudCwgJ21vdXNldXAnLCBfdGhpcy5fb25Ecm9wKTtcbiAgICAgIG9mZihkb2N1bWVudCwgJ3RvdWNoZW5kJywgX3RoaXMuX29uRHJvcCk7XG4gICAgICBvZmYoZG9jdW1lbnQsICd0b3VjaGNhbmNlbCcsIF90aGlzLl9vbkRyb3ApO1xuXG4gICAgICBpZiAoZGF0YVRyYW5zZmVyKSB7XG4gICAgICAgIGRhdGFUcmFuc2Zlci5lZmZlY3RBbGxvd2VkID0gJ21vdmUnO1xuICAgICAgICBvcHRpb25zLnNldERhdGEgJiYgb3B0aW9ucy5zZXREYXRhLmNhbGwoX3RoaXMsIGRhdGFUcmFuc2ZlciwgZHJhZ0VsKTtcbiAgICAgIH1cblxuICAgICAgb24oZG9jdW1lbnQsICdkcm9wJywgX3RoaXMpOyAvLyAjMTI3NiBmaXg6XG5cbiAgICAgIGNzcyhkcmFnRWwsICd0cmFuc2Zvcm0nLCAndHJhbnNsYXRlWigwKScpO1xuICAgIH1cblxuICAgIGF3YWl0aW5nRHJhZ1N0YXJ0ZWQgPSB0cnVlO1xuICAgIF90aGlzLl9kcmFnU3RhcnRJZCA9IF9uZXh0VGljayhfdGhpcy5fZHJhZ1N0YXJ0ZWQuYmluZChfdGhpcywgZmFsbGJhY2ssIGV2dCkpO1xuICAgIG9uKGRvY3VtZW50LCAnc2VsZWN0c3RhcnQnLCBfdGhpcyk7XG4gICAgbW92ZWQgPSB0cnVlO1xuXG4gICAgaWYgKFNhZmFyaSkge1xuICAgICAgY3NzKGRvY3VtZW50LmJvZHksICd1c2VyLXNlbGVjdCcsICdub25lJyk7XG4gICAgfVxuICB9LFxuICAvLyBSZXR1cm5zIHRydWUgLSBpZiBubyBmdXJ0aGVyIGFjdGlvbiBpcyBuZWVkZWQgKGVpdGhlciBpbnNlcnRlZCBvciBhbm90aGVyIGNvbmRpdGlvbilcbiAgX29uRHJhZ092ZXI6IGZ1bmN0aW9uIF9vbkRyYWdPdmVyKFxuICAvKipFdmVudCovXG4gIGV2dCkge1xuICAgIHZhciBlbCA9IHRoaXMuZWwsXG4gICAgICAgIHRhcmdldCA9IGV2dC50YXJnZXQsXG4gICAgICAgIGRyYWdSZWN0LFxuICAgICAgICB0YXJnZXRSZWN0LFxuICAgICAgICByZXZlcnQsXG4gICAgICAgIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgIGdyb3VwID0gb3B0aW9ucy5ncm91cCxcbiAgICAgICAgYWN0aXZlU29ydGFibGUgPSBTb3J0YWJsZS5hY3RpdmUsXG4gICAgICAgIGlzT3duZXIgPSBhY3RpdmVHcm91cCA9PT0gZ3JvdXAsXG4gICAgICAgIGNhblNvcnQgPSBvcHRpb25zLnNvcnQsXG4gICAgICAgIGZyb21Tb3J0YWJsZSA9IHB1dFNvcnRhYmxlIHx8IGFjdGl2ZVNvcnRhYmxlLFxuICAgICAgICB2ZXJ0aWNhbCxcbiAgICAgICAgX3RoaXMgPSB0aGlzLFxuICAgICAgICBjb21wbGV0ZWRGaXJlZCA9IGZhbHNlO1xuXG4gICAgaWYgKF9zaWxlbnQpIHJldHVybjtcblxuICAgIGZ1bmN0aW9uIGRyYWdPdmVyRXZlbnQobmFtZSwgZXh0cmEpIHtcbiAgICAgIHBsdWdpbkV2ZW50KG5hbWUsIF90aGlzLCBfb2JqZWN0U3ByZWFkMih7XG4gICAgICAgIGV2dDogZXZ0LFxuICAgICAgICBpc093bmVyOiBpc093bmVyLFxuICAgICAgICBheGlzOiB2ZXJ0aWNhbCA/ICd2ZXJ0aWNhbCcgOiAnaG9yaXpvbnRhbCcsXG4gICAgICAgIHJldmVydDogcmV2ZXJ0LFxuICAgICAgICBkcmFnUmVjdDogZHJhZ1JlY3QsXG4gICAgICAgIHRhcmdldFJlY3Q6IHRhcmdldFJlY3QsXG4gICAgICAgIGNhblNvcnQ6IGNhblNvcnQsXG4gICAgICAgIGZyb21Tb3J0YWJsZTogZnJvbVNvcnRhYmxlLFxuICAgICAgICB0YXJnZXQ6IHRhcmdldCxcbiAgICAgICAgY29tcGxldGVkOiBjb21wbGV0ZWQsXG4gICAgICAgIG9uTW92ZTogZnVuY3Rpb24gb25Nb3ZlKHRhcmdldCwgYWZ0ZXIpIHtcbiAgICAgICAgICByZXR1cm4gX29uTW92ZShyb290RWwsIGVsLCBkcmFnRWwsIGRyYWdSZWN0LCB0YXJnZXQsIGdldFJlY3QodGFyZ2V0KSwgZXZ0LCBhZnRlcik7XG4gICAgICAgIH0sXG4gICAgICAgIGNoYW5nZWQ6IGNoYW5nZWRcbiAgICAgIH0sIGV4dHJhKSk7XG4gICAgfSAvLyBDYXB0dXJlIGFuaW1hdGlvbiBzdGF0ZVxuXG5cbiAgICBmdW5jdGlvbiBjYXB0dXJlKCkge1xuICAgICAgZHJhZ092ZXJFdmVudCgnZHJhZ092ZXJBbmltYXRpb25DYXB0dXJlJyk7XG5cbiAgICAgIF90aGlzLmNhcHR1cmVBbmltYXRpb25TdGF0ZSgpO1xuXG4gICAgICBpZiAoX3RoaXMgIT09IGZyb21Tb3J0YWJsZSkge1xuICAgICAgICBmcm9tU29ydGFibGUuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG4gICAgICB9XG4gICAgfSAvLyBSZXR1cm4gaW52b2NhdGlvbiB3aGVuIGRyYWdFbCBpcyBpbnNlcnRlZCAob3IgY29tcGxldGVkKVxuXG5cbiAgICBmdW5jdGlvbiBjb21wbGV0ZWQoaW5zZXJ0aW9uKSB7XG4gICAgICBkcmFnT3ZlckV2ZW50KCdkcmFnT3ZlckNvbXBsZXRlZCcsIHtcbiAgICAgICAgaW5zZXJ0aW9uOiBpbnNlcnRpb25cbiAgICAgIH0pO1xuXG4gICAgICBpZiAoaW5zZXJ0aW9uKSB7XG4gICAgICAgIC8vIENsb25lcyBtdXN0IGJlIGhpZGRlbiBiZWZvcmUgZm9sZGluZyBhbmltYXRpb24gdG8gY2FwdHVyZSBkcmFnUmVjdEFic29sdXRlIHByb3Blcmx5XG4gICAgICAgIGlmIChpc093bmVyKSB7XG4gICAgICAgICAgYWN0aXZlU29ydGFibGUuX2hpZGVDbG9uZSgpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGFjdGl2ZVNvcnRhYmxlLl9zaG93Q2xvbmUoX3RoaXMpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKF90aGlzICE9PSBmcm9tU29ydGFibGUpIHtcbiAgICAgICAgICAvLyBTZXQgZ2hvc3QgY2xhc3MgdG8gbmV3IHNvcnRhYmxlJ3MgZ2hvc3QgY2xhc3NcbiAgICAgICAgICB0b2dnbGVDbGFzcyhkcmFnRWwsIHB1dFNvcnRhYmxlID8gcHV0U29ydGFibGUub3B0aW9ucy5naG9zdENsYXNzIDogYWN0aXZlU29ydGFibGUub3B0aW9ucy5naG9zdENsYXNzLCBmYWxzZSk7XG4gICAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0VsLCBvcHRpb25zLmdob3N0Q2xhc3MsIHRydWUpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHB1dFNvcnRhYmxlICE9PSBfdGhpcyAmJiBfdGhpcyAhPT0gU29ydGFibGUuYWN0aXZlKSB7XG4gICAgICAgICAgcHV0U29ydGFibGUgPSBfdGhpcztcbiAgICAgICAgfSBlbHNlIGlmIChfdGhpcyA9PT0gU29ydGFibGUuYWN0aXZlICYmIHB1dFNvcnRhYmxlKSB7XG4gICAgICAgICAgcHV0U29ydGFibGUgPSBudWxsO1xuICAgICAgICB9IC8vIEFuaW1hdGlvblxuXG5cbiAgICAgICAgaWYgKGZyb21Tb3J0YWJsZSA9PT0gX3RoaXMpIHtcbiAgICAgICAgICBfdGhpcy5faWdub3JlV2hpbGVBbmltYXRpbmcgPSB0YXJnZXQ7XG4gICAgICAgIH1cblxuICAgICAgICBfdGhpcy5hbmltYXRlQWxsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICBkcmFnT3ZlckV2ZW50KCdkcmFnT3ZlckFuaW1hdGlvbkNvbXBsZXRlJyk7XG4gICAgICAgICAgX3RoaXMuX2lnbm9yZVdoaWxlQW5pbWF0aW5nID0gbnVsbDtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgaWYgKF90aGlzICE9PSBmcm9tU29ydGFibGUpIHtcbiAgICAgICAgICBmcm9tU29ydGFibGUuYW5pbWF0ZUFsbCgpO1xuICAgICAgICAgIGZyb21Tb3J0YWJsZS5faWdub3JlV2hpbGVBbmltYXRpbmcgPSBudWxsO1xuICAgICAgICB9XG4gICAgICB9IC8vIE51bGwgbGFzdFRhcmdldCBpZiBpdCBpcyBub3QgaW5zaWRlIGEgcHJldmlvdXNseSBzd2FwcGVkIGVsZW1lbnRcblxuXG4gICAgICBpZiAodGFyZ2V0ID09PSBkcmFnRWwgJiYgIWRyYWdFbC5hbmltYXRlZCB8fCB0YXJnZXQgPT09IGVsICYmICF0YXJnZXQuYW5pbWF0ZWQpIHtcbiAgICAgICAgbGFzdFRhcmdldCA9IG51bGw7XG4gICAgICB9IC8vIG5vIGJ1YmJsaW5nIGFuZCBub3QgZmFsbGJhY2tcblxuXG4gICAgICBpZiAoIW9wdGlvbnMuZHJhZ292ZXJCdWJibGUgJiYgIWV2dC5yb290RWwgJiYgdGFyZ2V0ICE9PSBkb2N1bWVudCkge1xuICAgICAgICBkcmFnRWwucGFyZW50Tm9kZVtleHBhbmRvXS5faXNPdXRzaWRlVGhpc0VsKGV2dC50YXJnZXQpOyAvLyBEbyBub3QgZGV0ZWN0IGZvciBlbXB0eSBpbnNlcnQgaWYgYWxyZWFkeSBpbnNlcnRlZFxuXG5cbiAgICAgICAgIWluc2VydGlvbiAmJiBuZWFyZXN0RW1wdHlJbnNlcnREZXRlY3RFdmVudChldnQpO1xuICAgICAgfVxuXG4gICAgICAhb3B0aW9ucy5kcmFnb3ZlckJ1YmJsZSAmJiBldnQuc3RvcFByb3BhZ2F0aW9uICYmIGV2dC5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIHJldHVybiBjb21wbGV0ZWRGaXJlZCA9IHRydWU7XG4gICAgfSAvLyBDYWxsIHdoZW4gZHJhZ0VsIGhhcyBiZWVuIGluc2VydGVkXG5cblxuICAgIGZ1bmN0aW9uIGNoYW5nZWQoKSB7XG4gICAgICBuZXdJbmRleCA9IGluZGV4KGRyYWdFbCk7XG4gICAgICBuZXdEcmFnZ2FibGVJbmRleCA9IGluZGV4KGRyYWdFbCwgb3B0aW9ucy5kcmFnZ2FibGUpO1xuXG4gICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgIHNvcnRhYmxlOiBfdGhpcyxcbiAgICAgICAgbmFtZTogJ2NoYW5nZScsXG4gICAgICAgIHRvRWw6IGVsLFxuICAgICAgICBuZXdJbmRleDogbmV3SW5kZXgsXG4gICAgICAgIG5ld0RyYWdnYWJsZUluZGV4OiBuZXdEcmFnZ2FibGVJbmRleCxcbiAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICB9KTtcbiAgICB9XG5cbiAgICBpZiAoZXZ0LnByZXZlbnREZWZhdWx0ICE9PSB2b2lkIDApIHtcbiAgICAgIGV2dC5jYW5jZWxhYmxlICYmIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIH1cblxuICAgIHRhcmdldCA9IGNsb3Nlc3QodGFyZ2V0LCBvcHRpb25zLmRyYWdnYWJsZSwgZWwsIHRydWUpO1xuICAgIGRyYWdPdmVyRXZlbnQoJ2RyYWdPdmVyJyk7XG4gICAgaWYgKFNvcnRhYmxlLmV2ZW50Q2FuY2VsZWQpIHJldHVybiBjb21wbGV0ZWRGaXJlZDtcblxuICAgIGlmIChkcmFnRWwuY29udGFpbnMoZXZ0LnRhcmdldCkgfHwgdGFyZ2V0LmFuaW1hdGVkICYmIHRhcmdldC5hbmltYXRpbmdYICYmIHRhcmdldC5hbmltYXRpbmdZIHx8IF90aGlzLl9pZ25vcmVXaGlsZUFuaW1hdGluZyA9PT0gdGFyZ2V0KSB7XG4gICAgICByZXR1cm4gY29tcGxldGVkKGZhbHNlKTtcbiAgICB9XG5cbiAgICBpZ25vcmVOZXh0Q2xpY2sgPSBmYWxzZTtcblxuICAgIGlmIChhY3RpdmVTb3J0YWJsZSAmJiAhb3B0aW9ucy5kaXNhYmxlZCAmJiAoaXNPd25lciA/IGNhblNvcnQgfHwgKHJldmVydCA9IHBhcmVudEVsICE9PSByb290RWwpIC8vIFJldmVydGluZyBpdGVtIGludG8gdGhlIG9yaWdpbmFsIGxpc3RcbiAgICA6IHB1dFNvcnRhYmxlID09PSB0aGlzIHx8ICh0aGlzLmxhc3RQdXRNb2RlID0gYWN0aXZlR3JvdXAuY2hlY2tQdWxsKHRoaXMsIGFjdGl2ZVNvcnRhYmxlLCBkcmFnRWwsIGV2dCkpICYmIGdyb3VwLmNoZWNrUHV0KHRoaXMsIGFjdGl2ZVNvcnRhYmxlLCBkcmFnRWwsIGV2dCkpKSB7XG4gICAgICB2ZXJ0aWNhbCA9IHRoaXMuX2dldERpcmVjdGlvbihldnQsIHRhcmdldCkgPT09ICd2ZXJ0aWNhbCc7XG4gICAgICBkcmFnUmVjdCA9IGdldFJlY3QoZHJhZ0VsKTtcbiAgICAgIGRyYWdPdmVyRXZlbnQoJ2RyYWdPdmVyVmFsaWQnKTtcbiAgICAgIGlmIChTb3J0YWJsZS5ldmVudENhbmNlbGVkKSByZXR1cm4gY29tcGxldGVkRmlyZWQ7XG5cbiAgICAgIGlmIChyZXZlcnQpIHtcbiAgICAgICAgcGFyZW50RWwgPSByb290RWw7IC8vIGFjdHVhbGl6YXRpb25cblxuICAgICAgICBjYXB0dXJlKCk7XG5cbiAgICAgICAgdGhpcy5faGlkZUNsb25lKCk7XG5cbiAgICAgICAgZHJhZ092ZXJFdmVudCgncmV2ZXJ0Jyk7XG5cbiAgICAgICAgaWYgKCFTb3J0YWJsZS5ldmVudENhbmNlbGVkKSB7XG4gICAgICAgICAgaWYgKG5leHRFbCkge1xuICAgICAgICAgICAgcm9vdEVsLmluc2VydEJlZm9yZShkcmFnRWwsIG5leHRFbCk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHJvb3RFbC5hcHBlbmRDaGlsZChkcmFnRWwpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBjb21wbGV0ZWQodHJ1ZSk7XG4gICAgICB9XG5cbiAgICAgIHZhciBlbExhc3RDaGlsZCA9IGxhc3RDaGlsZChlbCwgb3B0aW9ucy5kcmFnZ2FibGUpO1xuXG4gICAgICBpZiAoIWVsTGFzdENoaWxkIHx8IF9naG9zdElzTGFzdChldnQsIHZlcnRpY2FsLCB0aGlzKSAmJiAhZWxMYXN0Q2hpbGQuYW5pbWF0ZWQpIHtcbiAgICAgICAgLy8gSW5zZXJ0IHRvIGVuZCBvZiBsaXN0XG4gICAgICAgIC8vIElmIGFscmVhZHkgYXQgZW5kIG9mIGxpc3Q6IERvIG5vdCBpbnNlcnRcbiAgICAgICAgaWYgKGVsTGFzdENoaWxkID09PSBkcmFnRWwpIHtcbiAgICAgICAgICByZXR1cm4gY29tcGxldGVkKGZhbHNlKTtcbiAgICAgICAgfSAvLyBpZiB0aGVyZSBpcyBhIGxhc3QgZWxlbWVudCwgaXQgaXMgdGhlIHRhcmdldFxuXG5cbiAgICAgICAgaWYgKGVsTGFzdENoaWxkICYmIGVsID09PSBldnQudGFyZ2V0KSB7XG4gICAgICAgICAgdGFyZ2V0ID0gZWxMYXN0Q2hpbGQ7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAodGFyZ2V0KSB7XG4gICAgICAgICAgdGFyZ2V0UmVjdCA9IGdldFJlY3QodGFyZ2V0KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChfb25Nb3ZlKHJvb3RFbCwgZWwsIGRyYWdFbCwgZHJhZ1JlY3QsIHRhcmdldCwgdGFyZ2V0UmVjdCwgZXZ0LCAhIXRhcmdldCkgIT09IGZhbHNlKSB7XG4gICAgICAgICAgY2FwdHVyZSgpO1xuXG4gICAgICAgICAgaWYgKGVsTGFzdENoaWxkICYmIGVsTGFzdENoaWxkLm5leHRTaWJsaW5nKSB7XG4gICAgICAgICAgICAvLyB0aGUgbGFzdCBkcmFnZ2FibGUgZWxlbWVudCBpcyBub3QgdGhlIGxhc3Qgbm9kZVxuICAgICAgICAgICAgZWwuaW5zZXJ0QmVmb3JlKGRyYWdFbCwgZWxMYXN0Q2hpbGQubmV4dFNpYmxpbmcpO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBlbC5hcHBlbmRDaGlsZChkcmFnRWwpO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIHBhcmVudEVsID0gZWw7IC8vIGFjdHVhbGl6YXRpb25cblxuICAgICAgICAgIGNoYW5nZWQoKTtcbiAgICAgICAgICByZXR1cm4gY29tcGxldGVkKHRydWUpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKGVsTGFzdENoaWxkICYmIF9naG9zdElzRmlyc3QoZXZ0LCB2ZXJ0aWNhbCwgdGhpcykpIHtcbiAgICAgICAgLy8gSW5zZXJ0IHRvIHN0YXJ0IG9mIGxpc3RcbiAgICAgICAgdmFyIGZpcnN0Q2hpbGQgPSBnZXRDaGlsZChlbCwgMCwgb3B0aW9ucywgdHJ1ZSk7XG5cbiAgICAgICAgaWYgKGZpcnN0Q2hpbGQgPT09IGRyYWdFbCkge1xuICAgICAgICAgIHJldHVybiBjb21wbGV0ZWQoZmFsc2UpO1xuICAgICAgICB9XG5cbiAgICAgICAgdGFyZ2V0ID0gZmlyc3RDaGlsZDtcbiAgICAgICAgdGFyZ2V0UmVjdCA9IGdldFJlY3QodGFyZ2V0KTtcblxuICAgICAgICBpZiAoX29uTW92ZShyb290RWwsIGVsLCBkcmFnRWwsIGRyYWdSZWN0LCB0YXJnZXQsIHRhcmdldFJlY3QsIGV2dCwgZmFsc2UpICE9PSBmYWxzZSkge1xuICAgICAgICAgIGNhcHR1cmUoKTtcbiAgICAgICAgICBlbC5pbnNlcnRCZWZvcmUoZHJhZ0VsLCBmaXJzdENoaWxkKTtcbiAgICAgICAgICBwYXJlbnRFbCA9IGVsOyAvLyBhY3R1YWxpemF0aW9uXG5cbiAgICAgICAgICBjaGFuZ2VkKCk7XG4gICAgICAgICAgcmV0dXJuIGNvbXBsZXRlZCh0cnVlKTtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIGlmICh0YXJnZXQucGFyZW50Tm9kZSA9PT0gZWwpIHtcbiAgICAgICAgdGFyZ2V0UmVjdCA9IGdldFJlY3QodGFyZ2V0KTtcbiAgICAgICAgdmFyIGRpcmVjdGlvbiA9IDAsXG4gICAgICAgICAgICB0YXJnZXRCZWZvcmVGaXJzdFN3YXAsXG4gICAgICAgICAgICBkaWZmZXJlbnRMZXZlbCA9IGRyYWdFbC5wYXJlbnROb2RlICE9PSBlbCxcbiAgICAgICAgICAgIGRpZmZlcmVudFJvd0NvbCA9ICFfZHJhZ0VsSW5Sb3dDb2x1bW4oZHJhZ0VsLmFuaW1hdGVkICYmIGRyYWdFbC50b1JlY3QgfHwgZHJhZ1JlY3QsIHRhcmdldC5hbmltYXRlZCAmJiB0YXJnZXQudG9SZWN0IHx8IHRhcmdldFJlY3QsIHZlcnRpY2FsKSxcbiAgICAgICAgICAgIHNpZGUxID0gdmVydGljYWwgPyAndG9wJyA6ICdsZWZ0JyxcbiAgICAgICAgICAgIHNjcm9sbGVkUGFzdFRvcCA9IGlzU2Nyb2xsZWRQYXN0KHRhcmdldCwgJ3RvcCcsICd0b3AnKSB8fCBpc1Njcm9sbGVkUGFzdChkcmFnRWwsICd0b3AnLCAndG9wJyksXG4gICAgICAgICAgICBzY3JvbGxCZWZvcmUgPSBzY3JvbGxlZFBhc3RUb3AgPyBzY3JvbGxlZFBhc3RUb3Auc2Nyb2xsVG9wIDogdm9pZCAwO1xuXG4gICAgICAgIGlmIChsYXN0VGFyZ2V0ICE9PSB0YXJnZXQpIHtcbiAgICAgICAgICB0YXJnZXRCZWZvcmVGaXJzdFN3YXAgPSB0YXJnZXRSZWN0W3NpZGUxXTtcbiAgICAgICAgICBwYXN0Rmlyc3RJbnZlcnRUaHJlc2ggPSBmYWxzZTtcbiAgICAgICAgICBpc0NpcmN1bXN0YW50aWFsSW52ZXJ0ID0gIWRpZmZlcmVudFJvd0NvbCAmJiBvcHRpb25zLmludmVydFN3YXAgfHwgZGlmZmVyZW50TGV2ZWw7XG4gICAgICAgIH1cblxuICAgICAgICBkaXJlY3Rpb24gPSBfZ2V0U3dhcERpcmVjdGlvbihldnQsIHRhcmdldCwgdGFyZ2V0UmVjdCwgdmVydGljYWwsIGRpZmZlcmVudFJvd0NvbCA/IDEgOiBvcHRpb25zLnN3YXBUaHJlc2hvbGQsIG9wdGlvbnMuaW52ZXJ0ZWRTd2FwVGhyZXNob2xkID09IG51bGwgPyBvcHRpb25zLnN3YXBUaHJlc2hvbGQgOiBvcHRpb25zLmludmVydGVkU3dhcFRocmVzaG9sZCwgaXNDaXJjdW1zdGFudGlhbEludmVydCwgbGFzdFRhcmdldCA9PT0gdGFyZ2V0KTtcbiAgICAgICAgdmFyIHNpYmxpbmc7XG5cbiAgICAgICAgaWYgKGRpcmVjdGlvbiAhPT0gMCkge1xuICAgICAgICAgIC8vIENoZWNrIGlmIHRhcmdldCBpcyBiZXNpZGUgZHJhZ0VsIGluIHJlc3BlY3RpdmUgZGlyZWN0aW9uIChpZ25vcmluZyBoaWRkZW4gZWxlbWVudHMpXG4gICAgICAgICAgdmFyIGRyYWdJbmRleCA9IGluZGV4KGRyYWdFbCk7XG5cbiAgICAgICAgICBkbyB7XG4gICAgICAgICAgICBkcmFnSW5kZXggLT0gZGlyZWN0aW9uO1xuICAgICAgICAgICAgc2libGluZyA9IHBhcmVudEVsLmNoaWxkcmVuW2RyYWdJbmRleF07XG4gICAgICAgICAgfSB3aGlsZSAoc2libGluZyAmJiAoY3NzKHNpYmxpbmcsICdkaXNwbGF5JykgPT09ICdub25lJyB8fCBzaWJsaW5nID09PSBnaG9zdEVsKSk7XG4gICAgICAgIH0gLy8gSWYgZHJhZ0VsIGlzIGFscmVhZHkgYmVzaWRlIHRhcmdldDogRG8gbm90IGluc2VydFxuXG5cbiAgICAgICAgaWYgKGRpcmVjdGlvbiA9PT0gMCB8fCBzaWJsaW5nID09PSB0YXJnZXQpIHtcbiAgICAgICAgICByZXR1cm4gY29tcGxldGVkKGZhbHNlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGxhc3RUYXJnZXQgPSB0YXJnZXQ7XG4gICAgICAgIGxhc3REaXJlY3Rpb24gPSBkaXJlY3Rpb247XG4gICAgICAgIHZhciBuZXh0U2libGluZyA9IHRhcmdldC5uZXh0RWxlbWVudFNpYmxpbmcsXG4gICAgICAgICAgICBhZnRlciA9IGZhbHNlO1xuICAgICAgICBhZnRlciA9IGRpcmVjdGlvbiA9PT0gMTtcblxuICAgICAgICB2YXIgbW92ZVZlY3RvciA9IF9vbk1vdmUocm9vdEVsLCBlbCwgZHJhZ0VsLCBkcmFnUmVjdCwgdGFyZ2V0LCB0YXJnZXRSZWN0LCBldnQsIGFmdGVyKTtcblxuICAgICAgICBpZiAobW92ZVZlY3RvciAhPT0gZmFsc2UpIHtcbiAgICAgICAgICBpZiAobW92ZVZlY3RvciA9PT0gMSB8fCBtb3ZlVmVjdG9yID09PSAtMSkge1xuICAgICAgICAgICAgYWZ0ZXIgPSBtb3ZlVmVjdG9yID09PSAxO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIF9zaWxlbnQgPSB0cnVlO1xuICAgICAgICAgIHNldFRpbWVvdXQoX3Vuc2lsZW50LCAzMCk7XG4gICAgICAgICAgY2FwdHVyZSgpO1xuXG4gICAgICAgICAgaWYgKGFmdGVyICYmICFuZXh0U2libGluZykge1xuICAgICAgICAgICAgZWwuYXBwZW5kQ2hpbGQoZHJhZ0VsKTtcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgdGFyZ2V0LnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKGRyYWdFbCwgYWZ0ZXIgPyBuZXh0U2libGluZyA6IHRhcmdldCk7XG4gICAgICAgICAgfSAvLyBVbmRvIGNocm9tZSdzIHNjcm9sbCBhZGp1c3RtZW50IChoYXMgbm8gZWZmZWN0IG9uIG90aGVyIGJyb3dzZXJzKVxuXG5cbiAgICAgICAgICBpZiAoc2Nyb2xsZWRQYXN0VG9wKSB7XG4gICAgICAgICAgICBzY3JvbGxCeShzY3JvbGxlZFBhc3RUb3AsIDAsIHNjcm9sbEJlZm9yZSAtIHNjcm9sbGVkUGFzdFRvcC5zY3JvbGxUb3ApO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIHBhcmVudEVsID0gZHJhZ0VsLnBhcmVudE5vZGU7IC8vIGFjdHVhbGl6YXRpb25cbiAgICAgICAgICAvLyBtdXN0IGJlIGRvbmUgYmVmb3JlIGFuaW1hdGlvblxuXG4gICAgICAgICAgaWYgKHRhcmdldEJlZm9yZUZpcnN0U3dhcCAhPT0gdW5kZWZpbmVkICYmICFpc0NpcmN1bXN0YW50aWFsSW52ZXJ0KSB7XG4gICAgICAgICAgICB0YXJnZXRNb3ZlRGlzdGFuY2UgPSBNYXRoLmFicyh0YXJnZXRCZWZvcmVGaXJzdFN3YXAgLSBnZXRSZWN0KHRhcmdldClbc2lkZTFdKTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBjaGFuZ2VkKCk7XG4gICAgICAgICAgcmV0dXJuIGNvbXBsZXRlZCh0cnVlKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBpZiAoZWwuY29udGFpbnMoZHJhZ0VsKSkge1xuICAgICAgICByZXR1cm4gY29tcGxldGVkKGZhbHNlKTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gZmFsc2U7XG4gIH0sXG4gIF9pZ25vcmVXaGlsZUFuaW1hdGluZzogbnVsbCxcbiAgX29mZk1vdmVFdmVudHM6IGZ1bmN0aW9uIF9vZmZNb3ZlRXZlbnRzKCkge1xuICAgIG9mZihkb2N1bWVudCwgJ21vdXNlbW92ZScsIHRoaXMuX29uVG91Y2hNb3ZlKTtcbiAgICBvZmYoZG9jdW1lbnQsICd0b3VjaG1vdmUnLCB0aGlzLl9vblRvdWNoTW92ZSk7XG4gICAgb2ZmKGRvY3VtZW50LCAncG9pbnRlcm1vdmUnLCB0aGlzLl9vblRvdWNoTW92ZSk7XG4gICAgb2ZmKGRvY3VtZW50LCAnZHJhZ292ZXInLCBuZWFyZXN0RW1wdHlJbnNlcnREZXRlY3RFdmVudCk7XG4gICAgb2ZmKGRvY3VtZW50LCAnbW91c2Vtb3ZlJywgbmVhcmVzdEVtcHR5SW5zZXJ0RGV0ZWN0RXZlbnQpO1xuICAgIG9mZihkb2N1bWVudCwgJ3RvdWNobW92ZScsIG5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50KTtcbiAgfSxcbiAgX29mZlVwRXZlbnRzOiBmdW5jdGlvbiBfb2ZmVXBFdmVudHMoKSB7XG4gICAgdmFyIG93bmVyRG9jdW1lbnQgPSB0aGlzLmVsLm93bmVyRG9jdW1lbnQ7XG4gICAgb2ZmKG93bmVyRG9jdW1lbnQsICdtb3VzZXVwJywgdGhpcy5fb25Ecm9wKTtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ3RvdWNoZW5kJywgdGhpcy5fb25Ecm9wKTtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ3BvaW50ZXJ1cCcsIHRoaXMuX29uRHJvcCk7XG4gICAgb2ZmKG93bmVyRG9jdW1lbnQsICd0b3VjaGNhbmNlbCcsIHRoaXMuX29uRHJvcCk7XG4gICAgb2ZmKGRvY3VtZW50LCAnc2VsZWN0c3RhcnQnLCB0aGlzKTtcbiAgfSxcbiAgX29uRHJvcDogZnVuY3Rpb24gX29uRHJvcChcbiAgLyoqRXZlbnQqL1xuICBldnQpIHtcbiAgICB2YXIgZWwgPSB0aGlzLmVsLFxuICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zOyAvLyBHZXQgdGhlIGluZGV4IG9mIHRoZSBkcmFnZ2VkIGVsZW1lbnQgd2l0aGluIGl0cyBwYXJlbnRcblxuICAgIG5ld0luZGV4ID0gaW5kZXgoZHJhZ0VsKTtcbiAgICBuZXdEcmFnZ2FibGVJbmRleCA9IGluZGV4KGRyYWdFbCwgb3B0aW9ucy5kcmFnZ2FibGUpO1xuICAgIHBsdWdpbkV2ZW50KCdkcm9wJywgdGhpcywge1xuICAgICAgZXZ0OiBldnRcbiAgICB9KTtcbiAgICBwYXJlbnRFbCA9IGRyYWdFbCAmJiBkcmFnRWwucGFyZW50Tm9kZTsgLy8gR2V0IGFnYWluIGFmdGVyIHBsdWdpbiBldmVudFxuXG4gICAgbmV3SW5kZXggPSBpbmRleChkcmFnRWwpO1xuICAgIG5ld0RyYWdnYWJsZUluZGV4ID0gaW5kZXgoZHJhZ0VsLCBvcHRpb25zLmRyYWdnYWJsZSk7XG5cbiAgICBpZiAoU29ydGFibGUuZXZlbnRDYW5jZWxlZCkge1xuICAgICAgdGhpcy5fbnVsbGluZygpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgYXdhaXRpbmdEcmFnU3RhcnRlZCA9IGZhbHNlO1xuICAgIGlzQ2lyY3Vtc3RhbnRpYWxJbnZlcnQgPSBmYWxzZTtcbiAgICBwYXN0Rmlyc3RJbnZlcnRUaHJlc2ggPSBmYWxzZTtcbiAgICBjbGVhckludGVydmFsKHRoaXMuX2xvb3BJZCk7XG4gICAgY2xlYXJUaW1lb3V0KHRoaXMuX2RyYWdTdGFydFRpbWVyKTtcblxuICAgIF9jYW5jZWxOZXh0VGljayh0aGlzLmNsb25lSWQpO1xuXG4gICAgX2NhbmNlbE5leHRUaWNrKHRoaXMuX2RyYWdTdGFydElkKTsgLy8gVW5iaW5kIGV2ZW50c1xuXG5cbiAgICBpZiAodGhpcy5uYXRpdmVEcmFnZ2FibGUpIHtcbiAgICAgIG9mZihkb2N1bWVudCwgJ2Ryb3AnLCB0aGlzKTtcbiAgICAgIG9mZihlbCwgJ2RyYWdzdGFydCcsIHRoaXMuX29uRHJhZ1N0YXJ0KTtcbiAgICB9XG5cbiAgICB0aGlzLl9vZmZNb3ZlRXZlbnRzKCk7XG5cbiAgICB0aGlzLl9vZmZVcEV2ZW50cygpO1xuXG4gICAgaWYgKFNhZmFyaSkge1xuICAgICAgY3NzKGRvY3VtZW50LmJvZHksICd1c2VyLXNlbGVjdCcsICcnKTtcbiAgICB9XG5cbiAgICBjc3MoZHJhZ0VsLCAndHJhbnNmb3JtJywgJycpO1xuXG4gICAgaWYgKGV2dCkge1xuICAgICAgaWYgKG1vdmVkKSB7XG4gICAgICAgIGV2dC5jYW5jZWxhYmxlICYmIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAhb3B0aW9ucy5kcm9wQnViYmxlICYmIGV2dC5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIH1cblxuICAgICAgZ2hvc3RFbCAmJiBnaG9zdEVsLnBhcmVudE5vZGUgJiYgZ2hvc3RFbC5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKGdob3N0RWwpO1xuXG4gICAgICBpZiAocm9vdEVsID09PSBwYXJlbnRFbCB8fCBwdXRTb3J0YWJsZSAmJiBwdXRTb3J0YWJsZS5sYXN0UHV0TW9kZSAhPT0gJ2Nsb25lJykge1xuICAgICAgICAvLyBSZW1vdmUgY2xvbmUocylcbiAgICAgICAgY2xvbmVFbCAmJiBjbG9uZUVsLnBhcmVudE5vZGUgJiYgY2xvbmVFbC5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKGNsb25lRWwpO1xuICAgICAgfVxuXG4gICAgICBpZiAoZHJhZ0VsKSB7XG4gICAgICAgIGlmICh0aGlzLm5hdGl2ZURyYWdnYWJsZSkge1xuICAgICAgICAgIG9mZihkcmFnRWwsICdkcmFnZW5kJywgdGhpcyk7XG4gICAgICAgIH1cblxuICAgICAgICBfZGlzYWJsZURyYWdnYWJsZShkcmFnRWwpO1xuXG4gICAgICAgIGRyYWdFbC5zdHlsZVsnd2lsbC1jaGFuZ2UnXSA9ICcnOyAvLyBSZW1vdmUgY2xhc3Nlc1xuICAgICAgICAvLyBnaG9zdENsYXNzIGlzIGFkZGVkIGluIGRyYWdTdGFydGVkXG5cbiAgICAgICAgaWYgKG1vdmVkICYmICFhd2FpdGluZ0RyYWdTdGFydGVkKSB7XG4gICAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0VsLCBwdXRTb3J0YWJsZSA/IHB1dFNvcnRhYmxlLm9wdGlvbnMuZ2hvc3RDbGFzcyA6IHRoaXMub3B0aW9ucy5naG9zdENsYXNzLCBmYWxzZSk7XG4gICAgICAgIH1cblxuICAgICAgICB0b2dnbGVDbGFzcyhkcmFnRWwsIHRoaXMub3B0aW9ucy5jaG9zZW5DbGFzcywgZmFsc2UpOyAvLyBEcmFnIHN0b3AgZXZlbnRcblxuICAgICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgc29ydGFibGU6IHRoaXMsXG4gICAgICAgICAgbmFtZTogJ3VuY2hvb3NlJyxcbiAgICAgICAgICB0b0VsOiBwYXJlbnRFbCxcbiAgICAgICAgICBuZXdJbmRleDogbnVsbCxcbiAgICAgICAgICBuZXdEcmFnZ2FibGVJbmRleDogbnVsbCxcbiAgICAgICAgICBvcmlnaW5hbEV2ZW50OiBldnRcbiAgICAgICAgfSk7XG5cbiAgICAgICAgaWYgKHJvb3RFbCAhPT0gcGFyZW50RWwpIHtcbiAgICAgICAgICBpZiAobmV3SW5kZXggPj0gMCkge1xuICAgICAgICAgICAgLy8gQWRkIGV2ZW50XG4gICAgICAgICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgICAgIHJvb3RFbDogcGFyZW50RWwsXG4gICAgICAgICAgICAgIG5hbWU6ICdhZGQnLFxuICAgICAgICAgICAgICB0b0VsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgZnJvbUVsOiByb290RWwsXG4gICAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgICAgfSk7IC8vIFJlbW92ZSBldmVudFxuXG5cbiAgICAgICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgICAgc29ydGFibGU6IHRoaXMsXG4gICAgICAgICAgICAgIG5hbWU6ICdyZW1vdmUnLFxuICAgICAgICAgICAgICB0b0VsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgICAgICB9KTsgLy8gZHJhZyBmcm9tIG9uZSBsaXN0IGFuZCBkcm9wIGludG8gYW5vdGhlclxuXG5cbiAgICAgICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgICAgcm9vdEVsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgbmFtZTogJ3NvcnQnLFxuICAgICAgICAgICAgICB0b0VsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgZnJvbUVsOiByb290RWwsXG4gICAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgICAgc29ydGFibGU6IHRoaXMsXG4gICAgICAgICAgICAgIG5hbWU6ICdzb3J0JyxcbiAgICAgICAgICAgICAgdG9FbDogcGFyZW50RWwsXG4gICAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgcHV0U29ydGFibGUgJiYgcHV0U29ydGFibGUuc2F2ZSgpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGlmIChuZXdJbmRleCAhPT0gb2xkSW5kZXgpIHtcbiAgICAgICAgICAgIGlmIChuZXdJbmRleCA+PSAwKSB7XG4gICAgICAgICAgICAgIC8vIGRyYWcgJiBkcm9wIHdpdGhpbiB0aGUgc2FtZSBsaXN0XG4gICAgICAgICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgICAgICBzb3J0YWJsZTogdGhpcyxcbiAgICAgICAgICAgICAgICBuYW1lOiAndXBkYXRlJyxcbiAgICAgICAgICAgICAgICB0b0VsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgICBvcmlnaW5hbEV2ZW50OiBldnRcbiAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgICAgIHNvcnRhYmxlOiB0aGlzLFxuICAgICAgICAgICAgICAgIG5hbWU6ICdzb3J0JyxcbiAgICAgICAgICAgICAgICB0b0VsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgICBvcmlnaW5hbEV2ZW50OiBldnRcbiAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKFNvcnRhYmxlLmFjdGl2ZSkge1xuICAgICAgICAgIC8qIGpzaGludCBlcW51bGw6dHJ1ZSAqL1xuICAgICAgICAgIGlmIChuZXdJbmRleCA9PSBudWxsIHx8IG5ld0luZGV4ID09PSAtMSkge1xuICAgICAgICAgICAgbmV3SW5kZXggPSBvbGRJbmRleDtcbiAgICAgICAgICAgIG5ld0RyYWdnYWJsZUluZGV4ID0gb2xkRHJhZ2dhYmxlSW5kZXg7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgc29ydGFibGU6IHRoaXMsXG4gICAgICAgICAgICBuYW1lOiAnZW5kJyxcbiAgICAgICAgICAgIHRvRWw6IHBhcmVudEVsLFxuICAgICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgICAgfSk7IC8vIFNhdmUgc29ydGluZ1xuXG5cbiAgICAgICAgICB0aGlzLnNhdmUoKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cblxuICAgIHRoaXMuX251bGxpbmcoKTtcbiAgfSxcbiAgX251bGxpbmc6IGZ1bmN0aW9uIF9udWxsaW5nKCkge1xuICAgIHBsdWdpbkV2ZW50KCdudWxsaW5nJywgdGhpcyk7XG4gICAgcm9vdEVsID0gZHJhZ0VsID0gcGFyZW50RWwgPSBnaG9zdEVsID0gbmV4dEVsID0gY2xvbmVFbCA9IGxhc3REb3duRWwgPSBjbG9uZUhpZGRlbiA9IHRhcEV2dCA9IHRvdWNoRXZ0ID0gbW92ZWQgPSBuZXdJbmRleCA9IG5ld0RyYWdnYWJsZUluZGV4ID0gb2xkSW5kZXggPSBvbGREcmFnZ2FibGVJbmRleCA9IGxhc3RUYXJnZXQgPSBsYXN0RGlyZWN0aW9uID0gcHV0U29ydGFibGUgPSBhY3RpdmVHcm91cCA9IFNvcnRhYmxlLmRyYWdnZWQgPSBTb3J0YWJsZS5naG9zdCA9IFNvcnRhYmxlLmNsb25lID0gU29ydGFibGUuYWN0aXZlID0gbnVsbDtcbiAgICBzYXZlZElucHV0Q2hlY2tlZC5mb3JFYWNoKGZ1bmN0aW9uIChlbCkge1xuICAgICAgZWwuY2hlY2tlZCA9IHRydWU7XG4gICAgfSk7XG4gICAgc2F2ZWRJbnB1dENoZWNrZWQubGVuZ3RoID0gbGFzdER4ID0gbGFzdER5ID0gMDtcbiAgfSxcbiAgaGFuZGxlRXZlbnQ6IGZ1bmN0aW9uIGhhbmRsZUV2ZW50KFxuICAvKipFdmVudCovXG4gIGV2dCkge1xuICAgIHN3aXRjaCAoZXZ0LnR5cGUpIHtcbiAgICAgIGNhc2UgJ2Ryb3AnOlxuICAgICAgY2FzZSAnZHJhZ2VuZCc6XG4gICAgICAgIHRoaXMuX29uRHJvcChldnQpO1xuXG4gICAgICAgIGJyZWFrO1xuXG4gICAgICBjYXNlICdkcmFnZW50ZXInOlxuICAgICAgY2FzZSAnZHJhZ292ZXInOlxuICAgICAgICBpZiAoZHJhZ0VsKSB7XG4gICAgICAgICAgdGhpcy5fb25EcmFnT3ZlcihldnQpO1xuXG4gICAgICAgICAgX2dsb2JhbERyYWdPdmVyKGV2dCk7XG4gICAgICAgIH1cblxuICAgICAgICBicmVhaztcblxuICAgICAgY2FzZSAnc2VsZWN0c3RhcnQnOlxuICAgICAgICBldnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgYnJlYWs7XG4gICAgfVxuICB9LFxuXG4gIC8qKlxyXG4gICAqIFNlcmlhbGl6ZXMgdGhlIGl0ZW0gaW50byBhbiBhcnJheSBvZiBzdHJpbmcuXHJcbiAgICogQHJldHVybnMge1N0cmluZ1tdfVxyXG4gICAqL1xuICB0b0FycmF5OiBmdW5jdGlvbiB0b0FycmF5KCkge1xuICAgIHZhciBvcmRlciA9IFtdLFxuICAgICAgICBlbCxcbiAgICAgICAgY2hpbGRyZW4gPSB0aGlzLmVsLmNoaWxkcmVuLFxuICAgICAgICBpID0gMCxcbiAgICAgICAgbiA9IGNoaWxkcmVuLmxlbmd0aCxcbiAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9ucztcblxuICAgIGZvciAoOyBpIDwgbjsgaSsrKSB7XG4gICAgICBlbCA9IGNoaWxkcmVuW2ldO1xuXG4gICAgICBpZiAoY2xvc2VzdChlbCwgb3B0aW9ucy5kcmFnZ2FibGUsIHRoaXMuZWwsIGZhbHNlKSkge1xuICAgICAgICBvcmRlci5wdXNoKGVsLmdldEF0dHJpYnV0ZShvcHRpb25zLmRhdGFJZEF0dHIpIHx8IF9nZW5lcmF0ZUlkKGVsKSk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIG9yZGVyO1xuICB9LFxuXG4gIC8qKlxyXG4gICAqIFNvcnRzIHRoZSBlbGVtZW50cyBhY2NvcmRpbmcgdG8gdGhlIGFycmF5LlxyXG4gICAqIEBwYXJhbSAge1N0cmluZ1tdfSAgb3JkZXIgIG9yZGVyIG9mIHRoZSBpdGVtc1xyXG4gICAqL1xuICBzb3J0OiBmdW5jdGlvbiBzb3J0KG9yZGVyLCB1c2VBbmltYXRpb24pIHtcbiAgICB2YXIgaXRlbXMgPSB7fSxcbiAgICAgICAgcm9vdEVsID0gdGhpcy5lbDtcbiAgICB0aGlzLnRvQXJyYXkoKS5mb3JFYWNoKGZ1bmN0aW9uIChpZCwgaSkge1xuICAgICAgdmFyIGVsID0gcm9vdEVsLmNoaWxkcmVuW2ldO1xuXG4gICAgICBpZiAoY2xvc2VzdChlbCwgdGhpcy5vcHRpb25zLmRyYWdnYWJsZSwgcm9vdEVsLCBmYWxzZSkpIHtcbiAgICAgICAgaXRlbXNbaWRdID0gZWw7XG4gICAgICB9XG4gICAgfSwgdGhpcyk7XG4gICAgdXNlQW5pbWF0aW9uICYmIHRoaXMuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG4gICAgb3JkZXIuZm9yRWFjaChmdW5jdGlvbiAoaWQpIHtcbiAgICAgIGlmIChpdGVtc1tpZF0pIHtcbiAgICAgICAgcm9vdEVsLnJlbW92ZUNoaWxkKGl0ZW1zW2lkXSk7XG4gICAgICAgIHJvb3RFbC5hcHBlbmRDaGlsZChpdGVtc1tpZF0pO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHVzZUFuaW1hdGlvbiAmJiB0aGlzLmFuaW1hdGVBbGwoKTtcbiAgfSxcblxuICAvKipcclxuICAgKiBTYXZlIHRoZSBjdXJyZW50IHNvcnRpbmdcclxuICAgKi9cbiAgc2F2ZTogZnVuY3Rpb24gc2F2ZSgpIHtcbiAgICB2YXIgc3RvcmUgPSB0aGlzLm9wdGlvbnMuc3RvcmU7XG4gICAgc3RvcmUgJiYgc3RvcmUuc2V0ICYmIHN0b3JlLnNldCh0aGlzKTtcbiAgfSxcblxuICAvKipcclxuICAgKiBGb3IgZWFjaCBlbGVtZW50IGluIHRoZSBzZXQsIGdldCB0aGUgZmlyc3QgZWxlbWVudCB0aGF0IG1hdGNoZXMgdGhlIHNlbGVjdG9yIGJ5IHRlc3RpbmcgdGhlIGVsZW1lbnQgaXRzZWxmIGFuZCB0cmF2ZXJzaW5nIHVwIHRocm91Z2ggaXRzIGFuY2VzdG9ycyBpbiB0aGUgRE9NIHRyZWUuXHJcbiAgICogQHBhcmFtICAge0hUTUxFbGVtZW50fSAgZWxcclxuICAgKiBAcGFyYW0gICB7U3RyaW5nfSAgICAgICBbc2VsZWN0b3JdICBkZWZhdWx0OiBgb3B0aW9ucy5kcmFnZ2FibGVgXHJcbiAgICogQHJldHVybnMge0hUTUxFbGVtZW50fG51bGx9XHJcbiAgICovXG4gIGNsb3Nlc3Q6IGZ1bmN0aW9uIGNsb3Nlc3QkMShlbCwgc2VsZWN0b3IpIHtcbiAgICByZXR1cm4gY2xvc2VzdChlbCwgc2VsZWN0b3IgfHwgdGhpcy5vcHRpb25zLmRyYWdnYWJsZSwgdGhpcy5lbCwgZmFsc2UpO1xuICB9LFxuXG4gIC8qKlxyXG4gICAqIFNldC9nZXQgb3B0aW9uXHJcbiAgICogQHBhcmFtICAge3N0cmluZ30gbmFtZVxyXG4gICAqIEBwYXJhbSAgIHsqfSAgICAgIFt2YWx1ZV1cclxuICAgKiBAcmV0dXJucyB7Kn1cclxuICAgKi9cbiAgb3B0aW9uOiBmdW5jdGlvbiBvcHRpb24obmFtZSwgdmFsdWUpIHtcbiAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucztcblxuICAgIGlmICh2YWx1ZSA9PT0gdm9pZCAwKSB7XG4gICAgICByZXR1cm4gb3B0aW9uc1tuYW1lXTtcbiAgICB9IGVsc2Uge1xuICAgICAgdmFyIG1vZGlmaWVkVmFsdWUgPSBQbHVnaW5NYW5hZ2VyLm1vZGlmeU9wdGlvbih0aGlzLCBuYW1lLCB2YWx1ZSk7XG5cbiAgICAgIGlmICh0eXBlb2YgbW9kaWZpZWRWYWx1ZSAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgb3B0aW9uc1tuYW1lXSA9IG1vZGlmaWVkVmFsdWU7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBvcHRpb25zW25hbWVdID0gdmFsdWU7XG4gICAgICB9XG5cbiAgICAgIGlmIChuYW1lID09PSAnZ3JvdXAnKSB7XG4gICAgICAgIF9wcmVwYXJlR3JvdXAob3B0aW9ucyk7XG4gICAgICB9XG4gICAgfVxuICB9LFxuXG4gIC8qKlxyXG4gICAqIERlc3Ryb3lcclxuICAgKi9cbiAgZGVzdHJveTogZnVuY3Rpb24gZGVzdHJveSgpIHtcbiAgICBwbHVnaW5FdmVudCgnZGVzdHJveScsIHRoaXMpO1xuICAgIHZhciBlbCA9IHRoaXMuZWw7XG4gICAgZWxbZXhwYW5kb10gPSBudWxsO1xuICAgIG9mZihlbCwgJ21vdXNlZG93bicsIHRoaXMuX29uVGFwU3RhcnQpO1xuICAgIG9mZihlbCwgJ3RvdWNoc3RhcnQnLCB0aGlzLl9vblRhcFN0YXJ0KTtcbiAgICBvZmYoZWwsICdwb2ludGVyZG93bicsIHRoaXMuX29uVGFwU3RhcnQpO1xuXG4gICAgaWYgKHRoaXMubmF0aXZlRHJhZ2dhYmxlKSB7XG4gICAgICBvZmYoZWwsICdkcmFnb3ZlcicsIHRoaXMpO1xuICAgICAgb2ZmKGVsLCAnZHJhZ2VudGVyJywgdGhpcyk7XG4gICAgfSAvLyBSZW1vdmUgZHJhZ2dhYmxlIGF0dHJpYnV0ZXNcblxuXG4gICAgQXJyYXkucHJvdG90eXBlLmZvckVhY2guY2FsbChlbC5xdWVyeVNlbGVjdG9yQWxsKCdbZHJhZ2dhYmxlXScpLCBmdW5jdGlvbiAoZWwpIHtcbiAgICAgIGVsLnJlbW92ZUF0dHJpYnV0ZSgnZHJhZ2dhYmxlJyk7XG4gICAgfSk7XG5cbiAgICB0aGlzLl9vbkRyb3AoKTtcblxuICAgIHRoaXMuX2Rpc2FibGVEZWxheWVkRHJhZ0V2ZW50cygpO1xuXG4gICAgc29ydGFibGVzLnNwbGljZShzb3J0YWJsZXMuaW5kZXhPZih0aGlzLmVsKSwgMSk7XG4gICAgdGhpcy5lbCA9IGVsID0gbnVsbDtcbiAgfSxcbiAgX2hpZGVDbG9uZTogZnVuY3Rpb24gX2hpZGVDbG9uZSgpIHtcbiAgICBpZiAoIWNsb25lSGlkZGVuKSB7XG4gICAgICBwbHVnaW5FdmVudCgnaGlkZUNsb25lJywgdGhpcyk7XG4gICAgICBpZiAoU29ydGFibGUuZXZlbnRDYW5jZWxlZCkgcmV0dXJuO1xuICAgICAgY3NzKGNsb25lRWwsICdkaXNwbGF5JywgJ25vbmUnKTtcblxuICAgICAgaWYgKHRoaXMub3B0aW9ucy5yZW1vdmVDbG9uZU9uSGlkZSAmJiBjbG9uZUVsLnBhcmVudE5vZGUpIHtcbiAgICAgICAgY2xvbmVFbC5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKGNsb25lRWwpO1xuICAgICAgfVxuXG4gICAgICBjbG9uZUhpZGRlbiA9IHRydWU7XG4gICAgfVxuICB9LFxuICBfc2hvd0Nsb25lOiBmdW5jdGlvbiBfc2hvd0Nsb25lKHB1dFNvcnRhYmxlKSB7XG4gICAgaWYgKHB1dFNvcnRhYmxlLmxhc3RQdXRNb2RlICE9PSAnY2xvbmUnKSB7XG4gICAgICB0aGlzLl9oaWRlQ2xvbmUoKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmIChjbG9uZUhpZGRlbikge1xuICAgICAgcGx1Z2luRXZlbnQoJ3Nob3dDbG9uZScsIHRoaXMpO1xuICAgICAgaWYgKFNvcnRhYmxlLmV2ZW50Q2FuY2VsZWQpIHJldHVybjsgLy8gc2hvdyBjbG9uZSBhdCBkcmFnRWwgb3Igb3JpZ2luYWwgcG9zaXRpb25cblxuICAgICAgaWYgKGRyYWdFbC5wYXJlbnROb2RlID09IHJvb3RFbCAmJiAhdGhpcy5vcHRpb25zLmdyb3VwLnJldmVydENsb25lKSB7XG4gICAgICAgIHJvb3RFbC5pbnNlcnRCZWZvcmUoY2xvbmVFbCwgZHJhZ0VsKTtcbiAgICAgIH0gZWxzZSBpZiAobmV4dEVsKSB7XG4gICAgICAgIHJvb3RFbC5pbnNlcnRCZWZvcmUoY2xvbmVFbCwgbmV4dEVsKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHJvb3RFbC5hcHBlbmRDaGlsZChjbG9uZUVsKTtcbiAgICAgIH1cblxuICAgICAgaWYgKHRoaXMub3B0aW9ucy5ncm91cC5yZXZlcnRDbG9uZSkge1xuICAgICAgICB0aGlzLmFuaW1hdGUoZHJhZ0VsLCBjbG9uZUVsKTtcbiAgICAgIH1cblxuICAgICAgY3NzKGNsb25lRWwsICdkaXNwbGF5JywgJycpO1xuICAgICAgY2xvbmVIaWRkZW4gPSBmYWxzZTtcbiAgICB9XG4gIH1cbn07XG5cbmZ1bmN0aW9uIF9nbG9iYWxEcmFnT3Zlcihcbi8qKkV2ZW50Ki9cbmV2dCkge1xuICBpZiAoZXZ0LmRhdGFUcmFuc2Zlcikge1xuICAgIGV2dC5kYXRhVHJhbnNmZXIuZHJvcEVmZmVjdCA9ICdtb3ZlJztcbiAgfVxuXG4gIGV2dC5jYW5jZWxhYmxlICYmIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xufVxuXG5mdW5jdGlvbiBfb25Nb3ZlKGZyb21FbCwgdG9FbCwgZHJhZ0VsLCBkcmFnUmVjdCwgdGFyZ2V0RWwsIHRhcmdldFJlY3QsIG9yaWdpbmFsRXZlbnQsIHdpbGxJbnNlcnRBZnRlcikge1xuICB2YXIgZXZ0LFxuICAgICAgc29ydGFibGUgPSBmcm9tRWxbZXhwYW5kb10sXG4gICAgICBvbk1vdmVGbiA9IHNvcnRhYmxlLm9wdGlvbnMub25Nb3ZlLFxuICAgICAgcmV0VmFsOyAvLyBTdXBwb3J0IGZvciBuZXcgQ3VzdG9tRXZlbnQgZmVhdHVyZVxuXG4gIGlmICh3aW5kb3cuQ3VzdG9tRXZlbnQgJiYgIUlFMTFPckxlc3MgJiYgIUVkZ2UpIHtcbiAgICBldnQgPSBuZXcgQ3VzdG9tRXZlbnQoJ21vdmUnLCB7XG4gICAgICBidWJibGVzOiB0cnVlLFxuICAgICAgY2FuY2VsYWJsZTogdHJ1ZVxuICAgIH0pO1xuICB9IGVsc2Uge1xuICAgIGV2dCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCdFdmVudCcpO1xuICAgIGV2dC5pbml0RXZlbnQoJ21vdmUnLCB0cnVlLCB0cnVlKTtcbiAgfVxuXG4gIGV2dC50byA9IHRvRWw7XG4gIGV2dC5mcm9tID0gZnJvbUVsO1xuICBldnQuZHJhZ2dlZCA9IGRyYWdFbDtcbiAgZXZ0LmRyYWdnZWRSZWN0ID0gZHJhZ1JlY3Q7XG4gIGV2dC5yZWxhdGVkID0gdGFyZ2V0RWwgfHwgdG9FbDtcbiAgZXZ0LnJlbGF0ZWRSZWN0ID0gdGFyZ2V0UmVjdCB8fCBnZXRSZWN0KHRvRWwpO1xuICBldnQud2lsbEluc2VydEFmdGVyID0gd2lsbEluc2VydEFmdGVyO1xuICBldnQub3JpZ2luYWxFdmVudCA9IG9yaWdpbmFsRXZlbnQ7XG4gIGZyb21FbC5kaXNwYXRjaEV2ZW50KGV2dCk7XG5cbiAgaWYgKG9uTW92ZUZuKSB7XG4gICAgcmV0VmFsID0gb25Nb3ZlRm4uY2FsbChzb3J0YWJsZSwgZXZ0LCBvcmlnaW5hbEV2ZW50KTtcbiAgfVxuXG4gIHJldHVybiByZXRWYWw7XG59XG5cbmZ1bmN0aW9uIF9kaXNhYmxlRHJhZ2dhYmxlKGVsKSB7XG4gIGVsLmRyYWdnYWJsZSA9IGZhbHNlO1xufVxuXG5mdW5jdGlvbiBfdW5zaWxlbnQoKSB7XG4gIF9zaWxlbnQgPSBmYWxzZTtcbn1cblxuZnVuY3Rpb24gX2dob3N0SXNGaXJzdChldnQsIHZlcnRpY2FsLCBzb3J0YWJsZSkge1xuICB2YXIgcmVjdCA9IGdldFJlY3QoZ2V0Q2hpbGQoc29ydGFibGUuZWwsIDAsIHNvcnRhYmxlLm9wdGlvbnMsIHRydWUpKTtcbiAgdmFyIHNwYWNlciA9IDEwO1xuICByZXR1cm4gdmVydGljYWwgPyBldnQuY2xpZW50WCA8IHJlY3QubGVmdCAtIHNwYWNlciB8fCBldnQuY2xpZW50WSA8IHJlY3QudG9wICYmIGV2dC5jbGllbnRYIDwgcmVjdC5yaWdodCA6IGV2dC5jbGllbnRZIDwgcmVjdC50b3AgLSBzcGFjZXIgfHwgZXZ0LmNsaWVudFkgPCByZWN0LmJvdHRvbSAmJiBldnQuY2xpZW50WCA8IHJlY3QubGVmdDtcbn1cblxuZnVuY3Rpb24gX2dob3N0SXNMYXN0KGV2dCwgdmVydGljYWwsIHNvcnRhYmxlKSB7XG4gIHZhciByZWN0ID0gZ2V0UmVjdChsYXN0Q2hpbGQoc29ydGFibGUuZWwsIHNvcnRhYmxlLm9wdGlvbnMuZHJhZ2dhYmxlKSk7XG4gIHZhciBzcGFjZXIgPSAxMDtcbiAgcmV0dXJuIHZlcnRpY2FsID8gZXZ0LmNsaWVudFggPiByZWN0LnJpZ2h0ICsgc3BhY2VyIHx8IGV2dC5jbGllbnRYIDw9IHJlY3QucmlnaHQgJiYgZXZ0LmNsaWVudFkgPiByZWN0LmJvdHRvbSAmJiBldnQuY2xpZW50WCA+PSByZWN0LmxlZnQgOiBldnQuY2xpZW50WCA+IHJlY3QucmlnaHQgJiYgZXZ0LmNsaWVudFkgPiByZWN0LnRvcCB8fCBldnQuY2xpZW50WCA8PSByZWN0LnJpZ2h0ICYmIGV2dC5jbGllbnRZID4gcmVjdC5ib3R0b20gKyBzcGFjZXI7XG59XG5cbmZ1bmN0aW9uIF9nZXRTd2FwRGlyZWN0aW9uKGV2dCwgdGFyZ2V0LCB0YXJnZXRSZWN0LCB2ZXJ0aWNhbCwgc3dhcFRocmVzaG9sZCwgaW52ZXJ0ZWRTd2FwVGhyZXNob2xkLCBpbnZlcnRTd2FwLCBpc0xhc3RUYXJnZXQpIHtcbiAgdmFyIG1vdXNlT25BeGlzID0gdmVydGljYWwgPyBldnQuY2xpZW50WSA6IGV2dC5jbGllbnRYLFxuICAgICAgdGFyZ2V0TGVuZ3RoID0gdmVydGljYWwgPyB0YXJnZXRSZWN0LmhlaWdodCA6IHRhcmdldFJlY3Qud2lkdGgsXG4gICAgICB0YXJnZXRTMSA9IHZlcnRpY2FsID8gdGFyZ2V0UmVjdC50b3AgOiB0YXJnZXRSZWN0LmxlZnQsXG4gICAgICB0YXJnZXRTMiA9IHZlcnRpY2FsID8gdGFyZ2V0UmVjdC5ib3R0b20gOiB0YXJnZXRSZWN0LnJpZ2h0LFxuICAgICAgaW52ZXJ0ID0gZmFsc2U7XG5cbiAgaWYgKCFpbnZlcnRTd2FwKSB7XG4gICAgLy8gTmV2ZXIgaW52ZXJ0IG9yIGNyZWF0ZSBkcmFnRWwgc2hhZG93IHdoZW4gdGFyZ2V0IG1vdmVtZW5ldCBjYXVzZXMgbW91c2UgdG8gbW92ZSBwYXN0IHRoZSBlbmQgb2YgcmVndWxhciBzd2FwVGhyZXNob2xkXG4gICAgaWYgKGlzTGFzdFRhcmdldCAmJiB0YXJnZXRNb3ZlRGlzdGFuY2UgPCB0YXJnZXRMZW5ndGggKiBzd2FwVGhyZXNob2xkKSB7XG4gICAgICAvLyBtdWx0aXBsaWVkIG9ubHkgYnkgc3dhcFRocmVzaG9sZCBiZWNhdXNlIG1vdXNlIHdpbGwgYWxyZWFkeSBiZSBpbnNpZGUgdGFyZ2V0IGJ5ICgxIC0gdGhyZXNob2xkKSAqIHRhcmdldExlbmd0aCAvIDJcbiAgICAgIC8vIGNoZWNrIGlmIHBhc3QgZmlyc3QgaW52ZXJ0IHRocmVzaG9sZCBvbiBzaWRlIG9wcG9zaXRlIG9mIGxhc3REaXJlY3Rpb25cbiAgICAgIGlmICghcGFzdEZpcnN0SW52ZXJ0VGhyZXNoICYmIChsYXN0RGlyZWN0aW9uID09PSAxID8gbW91c2VPbkF4aXMgPiB0YXJnZXRTMSArIHRhcmdldExlbmd0aCAqIGludmVydGVkU3dhcFRocmVzaG9sZCAvIDIgOiBtb3VzZU9uQXhpcyA8IHRhcmdldFMyIC0gdGFyZ2V0TGVuZ3RoICogaW52ZXJ0ZWRTd2FwVGhyZXNob2xkIC8gMikpIHtcbiAgICAgICAgLy8gcGFzdCBmaXJzdCBpbnZlcnQgdGhyZXNob2xkLCBkbyBub3QgcmVzdHJpY3QgaW52ZXJ0ZWQgdGhyZXNob2xkIHRvIGRyYWdFbCBzaGFkb3dcbiAgICAgICAgcGFzdEZpcnN0SW52ZXJ0VGhyZXNoID0gdHJ1ZTtcbiAgICAgIH1cblxuICAgICAgaWYgKCFwYXN0Rmlyc3RJbnZlcnRUaHJlc2gpIHtcbiAgICAgICAgLy8gZHJhZ0VsIHNoYWRvdyAodGFyZ2V0IG1vdmUgZGlzdGFuY2Ugc2hhZG93KVxuICAgICAgICBpZiAobGFzdERpcmVjdGlvbiA9PT0gMSA/IG1vdXNlT25BeGlzIDwgdGFyZ2V0UzEgKyB0YXJnZXRNb3ZlRGlzdGFuY2UgLy8gb3ZlciBkcmFnRWwgc2hhZG93XG4gICAgICAgIDogbW91c2VPbkF4aXMgPiB0YXJnZXRTMiAtIHRhcmdldE1vdmVEaXN0YW5jZSkge1xuICAgICAgICAgIHJldHVybiAtbGFzdERpcmVjdGlvbjtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgaW52ZXJ0ID0gdHJ1ZTtcbiAgICAgIH1cbiAgICB9IGVsc2Uge1xuICAgICAgLy8gUmVndWxhclxuICAgICAgaWYgKG1vdXNlT25BeGlzID4gdGFyZ2V0UzEgKyB0YXJnZXRMZW5ndGggKiAoMSAtIHN3YXBUaHJlc2hvbGQpIC8gMiAmJiBtb3VzZU9uQXhpcyA8IHRhcmdldFMyIC0gdGFyZ2V0TGVuZ3RoICogKDEgLSBzd2FwVGhyZXNob2xkKSAvIDIpIHtcbiAgICAgICAgcmV0dXJuIF9nZXRJbnNlcnREaXJlY3Rpb24odGFyZ2V0KTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICBpbnZlcnQgPSBpbnZlcnQgfHwgaW52ZXJ0U3dhcDtcblxuICBpZiAoaW52ZXJ0KSB7XG4gICAgLy8gSW52ZXJ0IG9mIHJlZ3VsYXJcbiAgICBpZiAobW91c2VPbkF4aXMgPCB0YXJnZXRTMSArIHRhcmdldExlbmd0aCAqIGludmVydGVkU3dhcFRocmVzaG9sZCAvIDIgfHwgbW91c2VPbkF4aXMgPiB0YXJnZXRTMiAtIHRhcmdldExlbmd0aCAqIGludmVydGVkU3dhcFRocmVzaG9sZCAvIDIpIHtcbiAgICAgIHJldHVybiBtb3VzZU9uQXhpcyA+IHRhcmdldFMxICsgdGFyZ2V0TGVuZ3RoIC8gMiA/IDEgOiAtMTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gMDtcbn1cbi8qKlxyXG4gKiBHZXRzIHRoZSBkaXJlY3Rpb24gZHJhZ0VsIG11c3QgYmUgc3dhcHBlZCByZWxhdGl2ZSB0byB0YXJnZXQgaW4gb3JkZXIgdG8gbWFrZSBpdFxyXG4gKiBzZWVtIHRoYXQgZHJhZ0VsIGhhcyBiZWVuIFwiaW5zZXJ0ZWRcIiBpbnRvIHRoYXQgZWxlbWVudCdzIHBvc2l0aW9uXHJcbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSB0YXJnZXQgICAgICAgVGhlIHRhcmdldCB3aG9zZSBwb3NpdGlvbiBkcmFnRWwgaXMgYmVpbmcgaW5zZXJ0ZWQgYXRcclxuICogQHJldHVybiB7TnVtYmVyfSAgICAgICAgICAgICAgICAgICBEaXJlY3Rpb24gZHJhZ0VsIG11c3QgYmUgc3dhcHBlZFxyXG4gKi9cblxuXG5mdW5jdGlvbiBfZ2V0SW5zZXJ0RGlyZWN0aW9uKHRhcmdldCkge1xuICBpZiAoaW5kZXgoZHJhZ0VsKSA8IGluZGV4KHRhcmdldCkpIHtcbiAgICByZXR1cm4gMTtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gLTE7XG4gIH1cbn1cbi8qKlxyXG4gKiBHZW5lcmF0ZSBpZFxyXG4gKiBAcGFyYW0gICB7SFRNTEVsZW1lbnR9IGVsXHJcbiAqIEByZXR1cm5zIHtTdHJpbmd9XHJcbiAqIEBwcml2YXRlXHJcbiAqL1xuXG5cbmZ1bmN0aW9uIF9nZW5lcmF0ZUlkKGVsKSB7XG4gIHZhciBzdHIgPSBlbC50YWdOYW1lICsgZWwuY2xhc3NOYW1lICsgZWwuc3JjICsgZWwuaHJlZiArIGVsLnRleHRDb250ZW50LFxuICAgICAgaSA9IHN0ci5sZW5ndGgsXG4gICAgICBzdW0gPSAwO1xuXG4gIHdoaWxlIChpLS0pIHtcbiAgICBzdW0gKz0gc3RyLmNoYXJDb2RlQXQoaSk7XG4gIH1cblxuICByZXR1cm4gc3VtLnRvU3RyaW5nKDM2KTtcbn1cblxuZnVuY3Rpb24gX3NhdmVJbnB1dENoZWNrZWRTdGF0ZShyb290KSB7XG4gIHNhdmVkSW5wdXRDaGVja2VkLmxlbmd0aCA9IDA7XG4gIHZhciBpbnB1dHMgPSByb290LmdldEVsZW1lbnRzQnlUYWdOYW1lKCdpbnB1dCcpO1xuICB2YXIgaWR4ID0gaW5wdXRzLmxlbmd0aDtcblxuICB3aGlsZSAoaWR4LS0pIHtcbiAgICB2YXIgZWwgPSBpbnB1dHNbaWR4XTtcbiAgICBlbC5jaGVja2VkICYmIHNhdmVkSW5wdXRDaGVja2VkLnB1c2goZWwpO1xuICB9XG59XG5cbmZ1bmN0aW9uIF9uZXh0VGljayhmbikge1xuICByZXR1cm4gc2V0VGltZW91dChmbiwgMCk7XG59XG5cbmZ1bmN0aW9uIF9jYW5jZWxOZXh0VGljayhpZCkge1xuICByZXR1cm4gY2xlYXJUaW1lb3V0KGlkKTtcbn0gLy8gRml4ZWQgIzk3MzpcblxuXG5pZiAoZG9jdW1lbnRFeGlzdHMpIHtcbiAgb24oZG9jdW1lbnQsICd0b3VjaG1vdmUnLCBmdW5jdGlvbiAoZXZ0KSB7XG4gICAgaWYgKChTb3J0YWJsZS5hY3RpdmUgfHwgYXdhaXRpbmdEcmFnU3RhcnRlZCkgJiYgZXZ0LmNhbmNlbGFibGUpIHtcbiAgICAgIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIH1cbiAgfSk7XG59IC8vIEV4cG9ydCB1dGlsc1xuXG5cblNvcnRhYmxlLnV0aWxzID0ge1xuICBvbjogb24sXG4gIG9mZjogb2ZmLFxuICBjc3M6IGNzcyxcbiAgZmluZDogZmluZCxcbiAgaXM6IGZ1bmN0aW9uIGlzKGVsLCBzZWxlY3Rvcikge1xuICAgIHJldHVybiAhIWNsb3Nlc3QoZWwsIHNlbGVjdG9yLCBlbCwgZmFsc2UpO1xuICB9LFxuICBleHRlbmQ6IGV4dGVuZCxcbiAgdGhyb3R0bGU6IHRocm90dGxlLFxuICBjbG9zZXN0OiBjbG9zZXN0LFxuICB0b2dnbGVDbGFzczogdG9nZ2xlQ2xhc3MsXG4gIGNsb25lOiBjbG9uZSxcbiAgaW5kZXg6IGluZGV4LFxuICBuZXh0VGljazogX25leHRUaWNrLFxuICBjYW5jZWxOZXh0VGljazogX2NhbmNlbE5leHRUaWNrLFxuICBkZXRlY3REaXJlY3Rpb246IF9kZXRlY3REaXJlY3Rpb24sXG4gIGdldENoaWxkOiBnZXRDaGlsZFxufTtcbi8qKlxyXG4gKiBHZXQgdGhlIFNvcnRhYmxlIGluc3RhbmNlIG9mIGFuIGVsZW1lbnRcclxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IGVsZW1lbnQgVGhlIGVsZW1lbnRcclxuICogQHJldHVybiB7U29ydGFibGV8dW5kZWZpbmVkfSAgICAgICAgIFRoZSBpbnN0YW5jZSBvZiBTb3J0YWJsZVxyXG4gKi9cblxuU29ydGFibGUuZ2V0ID0gZnVuY3Rpb24gKGVsZW1lbnQpIHtcbiAgcmV0dXJuIGVsZW1lbnRbZXhwYW5kb107XG59O1xuLyoqXHJcbiAqIE1vdW50IGEgcGx1Z2luIHRvIFNvcnRhYmxlXHJcbiAqIEBwYXJhbSAgey4uLlNvcnRhYmxlUGx1Z2lufFNvcnRhYmxlUGx1Z2luW119IHBsdWdpbnMgICAgICAgUGx1Z2lucyBiZWluZyBtb3VudGVkXHJcbiAqL1xuXG5cblNvcnRhYmxlLm1vdW50ID0gZnVuY3Rpb24gKCkge1xuICBmb3IgKHZhciBfbGVuID0gYXJndW1lbnRzLmxlbmd0aCwgcGx1Z2lucyA9IG5ldyBBcnJheShfbGVuKSwgX2tleSA9IDA7IF9rZXkgPCBfbGVuOyBfa2V5KyspIHtcbiAgICBwbHVnaW5zW19rZXldID0gYXJndW1lbnRzW19rZXldO1xuICB9XG5cbiAgaWYgKHBsdWdpbnNbMF0uY29uc3RydWN0b3IgPT09IEFycmF5KSBwbHVnaW5zID0gcGx1Z2luc1swXTtcbiAgcGx1Z2lucy5mb3JFYWNoKGZ1bmN0aW9uIChwbHVnaW4pIHtcbiAgICBpZiAoIXBsdWdpbi5wcm90b3R5cGUgfHwgIXBsdWdpbi5wcm90b3R5cGUuY29uc3RydWN0b3IpIHtcbiAgICAgIHRocm93IFwiU29ydGFibGU6IE1vdW50ZWQgcGx1Z2luIG11c3QgYmUgYSBjb25zdHJ1Y3RvciBmdW5jdGlvbiwgbm90IFwiLmNvbmNhdCh7fS50b1N0cmluZy5jYWxsKHBsdWdpbikpO1xuICAgIH1cblxuICAgIGlmIChwbHVnaW4udXRpbHMpIFNvcnRhYmxlLnV0aWxzID0gX29iamVjdFNwcmVhZDIoX29iamVjdFNwcmVhZDIoe30sIFNvcnRhYmxlLnV0aWxzKSwgcGx1Z2luLnV0aWxzKTtcbiAgICBQbHVnaW5NYW5hZ2VyLm1vdW50KHBsdWdpbik7XG4gIH0pO1xufTtcbi8qKlxyXG4gKiBDcmVhdGUgc29ydGFibGUgaW5zdGFuY2VcclxuICogQHBhcmFtIHtIVE1MRWxlbWVudH0gIGVsXHJcbiAqIEBwYXJhbSB7T2JqZWN0fSAgICAgIFtvcHRpb25zXVxyXG4gKi9cblxuXG5Tb3J0YWJsZS5jcmVhdGUgPSBmdW5jdGlvbiAoZWwsIG9wdGlvbnMpIHtcbiAgcmV0dXJuIG5ldyBTb3J0YWJsZShlbCwgb3B0aW9ucyk7XG59OyAvLyBFeHBvcnRcblxuXG5Tb3J0YWJsZS52ZXJzaW9uID0gdmVyc2lvbjtcblxudmFyIGF1dG9TY3JvbGxzID0gW10sXG4gICAgc2Nyb2xsRWwsXG4gICAgc2Nyb2xsUm9vdEVsLFxuICAgIHNjcm9sbGluZyA9IGZhbHNlLFxuICAgIGxhc3RBdXRvU2Nyb2xsWCxcbiAgICBsYXN0QXV0b1Njcm9sbFksXG4gICAgdG91Y2hFdnQkMSxcbiAgICBwb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbDtcblxuZnVuY3Rpb24gQXV0b1Njcm9sbFBsdWdpbigpIHtcbiAgZnVuY3Rpb24gQXV0b1Njcm9sbCgpIHtcbiAgICB0aGlzLmRlZmF1bHRzID0ge1xuICAgICAgc2Nyb2xsOiB0cnVlLFxuICAgICAgZm9yY2VBdXRvU2Nyb2xsRmFsbGJhY2s6IGZhbHNlLFxuICAgICAgc2Nyb2xsU2Vuc2l0aXZpdHk6IDMwLFxuICAgICAgc2Nyb2xsU3BlZWQ6IDEwLFxuICAgICAgYnViYmxlU2Nyb2xsOiB0cnVlXG4gICAgfTsgLy8gQmluZCBhbGwgcHJpdmF0ZSBtZXRob2RzXG5cbiAgICBmb3IgKHZhciBmbiBpbiB0aGlzKSB7XG4gICAgICBpZiAoZm4uY2hhckF0KDApID09PSAnXycgJiYgdHlwZW9mIHRoaXNbZm5dID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRoaXNbZm5dID0gdGhpc1tmbl0uYmluZCh0aGlzKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cblxuICBBdXRvU2Nyb2xsLnByb3RvdHlwZSA9IHtcbiAgICBkcmFnU3RhcnRlZDogZnVuY3Rpb24gZHJhZ1N0YXJ0ZWQoX3JlZikge1xuICAgICAgdmFyIG9yaWdpbmFsRXZlbnQgPSBfcmVmLm9yaWdpbmFsRXZlbnQ7XG5cbiAgICAgIGlmICh0aGlzLnNvcnRhYmxlLm5hdGl2ZURyYWdnYWJsZSkge1xuICAgICAgICBvbihkb2N1bWVudCwgJ2RyYWdvdmVyJywgdGhpcy5faGFuZGxlQXV0b1Njcm9sbCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBpZiAodGhpcy5vcHRpb25zLnN1cHBvcnRQb2ludGVyKSB7XG4gICAgICAgICAgb24oZG9jdW1lbnQsICdwb2ludGVybW92ZScsIHRoaXMuX2hhbmRsZUZhbGxiYWNrQXV0b1Njcm9sbCk7XG4gICAgICAgIH0gZWxzZSBpZiAob3JpZ2luYWxFdmVudC50b3VjaGVzKSB7XG4gICAgICAgICAgb24oZG9jdW1lbnQsICd0b3VjaG1vdmUnLCB0aGlzLl9oYW5kbGVGYWxsYmFja0F1dG9TY3JvbGwpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIG9uKGRvY3VtZW50LCAnbW91c2Vtb3ZlJywgdGhpcy5faGFuZGxlRmFsbGJhY2tBdXRvU2Nyb2xsKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sXG4gICAgZHJhZ092ZXJDb21wbGV0ZWQ6IGZ1bmN0aW9uIGRyYWdPdmVyQ29tcGxldGVkKF9yZWYyKSB7XG4gICAgICB2YXIgb3JpZ2luYWxFdmVudCA9IF9yZWYyLm9yaWdpbmFsRXZlbnQ7XG5cbiAgICAgIC8vIEZvciB3aGVuIGJ1YmJsaW5nIGlzIGNhbmNlbGVkIGFuZCB1c2luZyBmYWxsYmFjayAoZmFsbGJhY2sgJ3RvdWNobW92ZScgYWx3YXlzIHJlYWNoZWQpXG4gICAgICBpZiAoIXRoaXMub3B0aW9ucy5kcmFnT3ZlckJ1YmJsZSAmJiAhb3JpZ2luYWxFdmVudC5yb290RWwpIHtcbiAgICAgICAgdGhpcy5faGFuZGxlQXV0b1Njcm9sbChvcmlnaW5hbEV2ZW50KTtcbiAgICAgIH1cbiAgICB9LFxuICAgIGRyb3A6IGZ1bmN0aW9uIGRyb3AoKSB7XG4gICAgICBpZiAodGhpcy5zb3J0YWJsZS5uYXRpdmVEcmFnZ2FibGUpIHtcbiAgICAgICAgb2ZmKGRvY3VtZW50LCAnZHJhZ292ZXInLCB0aGlzLl9oYW5kbGVBdXRvU2Nyb2xsKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIG9mZihkb2N1bWVudCwgJ3BvaW50ZXJtb3ZlJywgdGhpcy5faGFuZGxlRmFsbGJhY2tBdXRvU2Nyb2xsKTtcbiAgICAgICAgb2ZmKGRvY3VtZW50LCAndG91Y2htb3ZlJywgdGhpcy5faGFuZGxlRmFsbGJhY2tBdXRvU2Nyb2xsKTtcbiAgICAgICAgb2ZmKGRvY3VtZW50LCAnbW91c2Vtb3ZlJywgdGhpcy5faGFuZGxlRmFsbGJhY2tBdXRvU2Nyb2xsKTtcbiAgICAgIH1cblxuICAgICAgY2xlYXJQb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbCgpO1xuICAgICAgY2xlYXJBdXRvU2Nyb2xscygpO1xuICAgICAgY2FuY2VsVGhyb3R0bGUoKTtcbiAgICB9LFxuICAgIG51bGxpbmc6IGZ1bmN0aW9uIG51bGxpbmcoKSB7XG4gICAgICB0b3VjaEV2dCQxID0gc2Nyb2xsUm9vdEVsID0gc2Nyb2xsRWwgPSBzY3JvbGxpbmcgPSBwb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbCA9IGxhc3RBdXRvU2Nyb2xsWCA9IGxhc3RBdXRvU2Nyb2xsWSA9IG51bGw7XG4gICAgICBhdXRvU2Nyb2xscy5sZW5ndGggPSAwO1xuICAgIH0sXG4gICAgX2hhbmRsZUZhbGxiYWNrQXV0b1Njcm9sbDogZnVuY3Rpb24gX2hhbmRsZUZhbGxiYWNrQXV0b1Njcm9sbChldnQpIHtcbiAgICAgIHRoaXMuX2hhbmRsZUF1dG9TY3JvbGwoZXZ0LCB0cnVlKTtcbiAgICB9LFxuICAgIF9oYW5kbGVBdXRvU2Nyb2xsOiBmdW5jdGlvbiBfaGFuZGxlQXV0b1Njcm9sbChldnQsIGZhbGxiYWNrKSB7XG4gICAgICB2YXIgX3RoaXMgPSB0aGlzO1xuXG4gICAgICB2YXIgeCA9IChldnQudG91Y2hlcyA/IGV2dC50b3VjaGVzWzBdIDogZXZ0KS5jbGllbnRYLFxuICAgICAgICAgIHkgPSAoZXZ0LnRvdWNoZXMgPyBldnQudG91Y2hlc1swXSA6IGV2dCkuY2xpZW50WSxcbiAgICAgICAgICBlbGVtID0gZG9jdW1lbnQuZWxlbWVudEZyb21Qb2ludCh4LCB5KTtcbiAgICAgIHRvdWNoRXZ0JDEgPSBldnQ7IC8vIElFIGRvZXMgbm90IHNlZW0gdG8gaGF2ZSBuYXRpdmUgYXV0b3Njcm9sbCxcbiAgICAgIC8vIEVkZ2UncyBhdXRvc2Nyb2xsIHNlZW1zIHRvbyBjb25kaXRpb25hbCxcbiAgICAgIC8vIE1BQ09TIFNhZmFyaSBkb2VzIG5vdCBoYXZlIGF1dG9zY3JvbGwsXG4gICAgICAvLyBGaXJlZm94IGFuZCBDaHJvbWUgYXJlIGdvb2RcblxuICAgICAgaWYgKGZhbGxiYWNrIHx8IHRoaXMub3B0aW9ucy5mb3JjZUF1dG9TY3JvbGxGYWxsYmFjayB8fCBFZGdlIHx8IElFMTFPckxlc3MgfHwgU2FmYXJpKSB7XG4gICAgICAgIGF1dG9TY3JvbGwoZXZ0LCB0aGlzLm9wdGlvbnMsIGVsZW0sIGZhbGxiYWNrKTsgLy8gTGlzdGVuZXIgZm9yIHBvaW50ZXIgZWxlbWVudCBjaGFuZ2VcblxuICAgICAgICB2YXIgb2dFbGVtU2Nyb2xsZXIgPSBnZXRQYXJlbnRBdXRvU2Nyb2xsRWxlbWVudChlbGVtLCB0cnVlKTtcblxuICAgICAgICBpZiAoc2Nyb2xsaW5nICYmICghcG9pbnRlckVsZW1DaGFuZ2VkSW50ZXJ2YWwgfHwgeCAhPT0gbGFzdEF1dG9TY3JvbGxYIHx8IHkgIT09IGxhc3RBdXRvU2Nyb2xsWSkpIHtcbiAgICAgICAgICBwb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbCAmJiBjbGVhclBvaW50ZXJFbGVtQ2hhbmdlZEludGVydmFsKCk7IC8vIERldGVjdCBmb3IgcG9pbnRlciBlbGVtIGNoYW5nZSwgZW11bGF0aW5nIG5hdGl2ZSBEbkQgYmVoYXZpb3VyXG5cbiAgICAgICAgICBwb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbCA9IHNldEludGVydmFsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciBuZXdFbGVtID0gZ2V0UGFyZW50QXV0b1Njcm9sbEVsZW1lbnQoZG9jdW1lbnQuZWxlbWVudEZyb21Qb2ludCh4LCB5KSwgdHJ1ZSk7XG5cbiAgICAgICAgICAgIGlmIChuZXdFbGVtICE9PSBvZ0VsZW1TY3JvbGxlcikge1xuICAgICAgICAgICAgICBvZ0VsZW1TY3JvbGxlciA9IG5ld0VsZW07XG4gICAgICAgICAgICAgIGNsZWFyQXV0b1Njcm9sbHMoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgYXV0b1Njcm9sbChldnQsIF90aGlzLm9wdGlvbnMsIG5ld0VsZW0sIGZhbGxiYWNrKTtcbiAgICAgICAgICB9LCAxMCk7XG4gICAgICAgICAgbGFzdEF1dG9TY3JvbGxYID0geDtcbiAgICAgICAgICBsYXN0QXV0b1Njcm9sbFkgPSB5O1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAvLyBpZiBEbkQgaXMgZW5hYmxlZCAoYW5kIGJyb3dzZXIgaGFzIGdvb2QgYXV0b3Njcm9sbGluZyksIGZpcnN0IGF1dG9zY3JvbGwgd2lsbCBhbHJlYWR5IHNjcm9sbCwgc28gZ2V0IHBhcmVudCBhdXRvc2Nyb2xsIG9mIGZpcnN0IGF1dG9zY3JvbGxcbiAgICAgICAgaWYgKCF0aGlzLm9wdGlvbnMuYnViYmxlU2Nyb2xsIHx8IGdldFBhcmVudEF1dG9TY3JvbGxFbGVtZW50KGVsZW0sIHRydWUpID09PSBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCkpIHtcbiAgICAgICAgICBjbGVhckF1dG9TY3JvbGxzKCk7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgYXV0b1Njcm9sbChldnQsIHRoaXMub3B0aW9ucywgZ2V0UGFyZW50QXV0b1Njcm9sbEVsZW1lbnQoZWxlbSwgZmFsc2UpLCBmYWxzZSk7XG4gICAgICB9XG4gICAgfVxuICB9O1xuICByZXR1cm4gX2V4dGVuZHMoQXV0b1Njcm9sbCwge1xuICAgIHBsdWdpbk5hbWU6ICdzY3JvbGwnLFxuICAgIGluaXRpYWxpemVCeURlZmF1bHQ6IHRydWVcbiAgfSk7XG59XG5cbmZ1bmN0aW9uIGNsZWFyQXV0b1Njcm9sbHMoKSB7XG4gIGF1dG9TY3JvbGxzLmZvckVhY2goZnVuY3Rpb24gKGF1dG9TY3JvbGwpIHtcbiAgICBjbGVhckludGVydmFsKGF1dG9TY3JvbGwucGlkKTtcbiAgfSk7XG4gIGF1dG9TY3JvbGxzID0gW107XG59XG5cbmZ1bmN0aW9uIGNsZWFyUG9pbnRlckVsZW1DaGFuZ2VkSW50ZXJ2YWwoKSB7XG4gIGNsZWFySW50ZXJ2YWwocG9pbnRlckVsZW1DaGFuZ2VkSW50ZXJ2YWwpO1xufVxuXG52YXIgYXV0b1Njcm9sbCA9IHRocm90dGxlKGZ1bmN0aW9uIChldnQsIG9wdGlvbnMsIHJvb3RFbCwgaXNGYWxsYmFjaykge1xuICAvLyBCdWc6IGh0dHBzOi8vYnVnemlsbGEubW96aWxsYS5vcmcvc2hvd19idWcuY2dpP2lkPTUwNTUyMVxuICBpZiAoIW9wdGlvbnMuc2Nyb2xsKSByZXR1cm47XG4gIHZhciB4ID0gKGV2dC50b3VjaGVzID8gZXZ0LnRvdWNoZXNbMF0gOiBldnQpLmNsaWVudFgsXG4gICAgICB5ID0gKGV2dC50b3VjaGVzID8gZXZ0LnRvdWNoZXNbMF0gOiBldnQpLmNsaWVudFksXG4gICAgICBzZW5zID0gb3B0aW9ucy5zY3JvbGxTZW5zaXRpdml0eSxcbiAgICAgIHNwZWVkID0gb3B0aW9ucy5zY3JvbGxTcGVlZCxcbiAgICAgIHdpblNjcm9sbGVyID0gZ2V0V2luZG93U2Nyb2xsaW5nRWxlbWVudCgpO1xuICB2YXIgc2Nyb2xsVGhpc0luc3RhbmNlID0gZmFsc2UsXG4gICAgICBzY3JvbGxDdXN0b21GbjsgLy8gTmV3IHNjcm9sbCByb290LCBzZXQgc2Nyb2xsRWxcblxuICBpZiAoc2Nyb2xsUm9vdEVsICE9PSByb290RWwpIHtcbiAgICBzY3JvbGxSb290RWwgPSByb290RWw7XG4gICAgY2xlYXJBdXRvU2Nyb2xscygpO1xuICAgIHNjcm9sbEVsID0gb3B0aW9ucy5zY3JvbGw7XG4gICAgc2Nyb2xsQ3VzdG9tRm4gPSBvcHRpb25zLnNjcm9sbEZuO1xuXG4gICAgaWYgKHNjcm9sbEVsID09PSB0cnVlKSB7XG4gICAgICBzY3JvbGxFbCA9IGdldFBhcmVudEF1dG9TY3JvbGxFbGVtZW50KHJvb3RFbCwgdHJ1ZSk7XG4gICAgfVxuICB9XG5cbiAgdmFyIGxheWVyc091dCA9IDA7XG4gIHZhciBjdXJyZW50UGFyZW50ID0gc2Nyb2xsRWw7XG5cbiAgZG8ge1xuICAgIHZhciBlbCA9IGN1cnJlbnRQYXJlbnQsXG4gICAgICAgIHJlY3QgPSBnZXRSZWN0KGVsKSxcbiAgICAgICAgdG9wID0gcmVjdC50b3AsXG4gICAgICAgIGJvdHRvbSA9IHJlY3QuYm90dG9tLFxuICAgICAgICBsZWZ0ID0gcmVjdC5sZWZ0LFxuICAgICAgICByaWdodCA9IHJlY3QucmlnaHQsXG4gICAgICAgIHdpZHRoID0gcmVjdC53aWR0aCxcbiAgICAgICAgaGVpZ2h0ID0gcmVjdC5oZWlnaHQsXG4gICAgICAgIGNhblNjcm9sbFggPSB2b2lkIDAsXG4gICAgICAgIGNhblNjcm9sbFkgPSB2b2lkIDAsXG4gICAgICAgIHNjcm9sbFdpZHRoID0gZWwuc2Nyb2xsV2lkdGgsXG4gICAgICAgIHNjcm9sbEhlaWdodCA9IGVsLnNjcm9sbEhlaWdodCxcbiAgICAgICAgZWxDU1MgPSBjc3MoZWwpLFxuICAgICAgICBzY3JvbGxQb3NYID0gZWwuc2Nyb2xsTGVmdCxcbiAgICAgICAgc2Nyb2xsUG9zWSA9IGVsLnNjcm9sbFRvcDtcblxuICAgIGlmIChlbCA9PT0gd2luU2Nyb2xsZXIpIHtcbiAgICAgIGNhblNjcm9sbFggPSB3aWR0aCA8IHNjcm9sbFdpZHRoICYmIChlbENTUy5vdmVyZmxvd1ggPT09ICdhdXRvJyB8fCBlbENTUy5vdmVyZmxvd1ggPT09ICdzY3JvbGwnIHx8IGVsQ1NTLm92ZXJmbG93WCA9PT0gJ3Zpc2libGUnKTtcbiAgICAgIGNhblNjcm9sbFkgPSBoZWlnaHQgPCBzY3JvbGxIZWlnaHQgJiYgKGVsQ1NTLm92ZXJmbG93WSA9PT0gJ2F1dG8nIHx8IGVsQ1NTLm92ZXJmbG93WSA9PT0gJ3Njcm9sbCcgfHwgZWxDU1Mub3ZlcmZsb3dZID09PSAndmlzaWJsZScpO1xuICAgIH0gZWxzZSB7XG4gICAgICBjYW5TY3JvbGxYID0gd2lkdGggPCBzY3JvbGxXaWR0aCAmJiAoZWxDU1Mub3ZlcmZsb3dYID09PSAnYXV0bycgfHwgZWxDU1Mub3ZlcmZsb3dYID09PSAnc2Nyb2xsJyk7XG4gICAgICBjYW5TY3JvbGxZID0gaGVpZ2h0IDwgc2Nyb2xsSGVpZ2h0ICYmIChlbENTUy5vdmVyZmxvd1kgPT09ICdhdXRvJyB8fCBlbENTUy5vdmVyZmxvd1kgPT09ICdzY3JvbGwnKTtcbiAgICB9XG5cbiAgICB2YXIgdnggPSBjYW5TY3JvbGxYICYmIChNYXRoLmFicyhyaWdodCAtIHgpIDw9IHNlbnMgJiYgc2Nyb2xsUG9zWCArIHdpZHRoIDwgc2Nyb2xsV2lkdGgpIC0gKE1hdGguYWJzKGxlZnQgLSB4KSA8PSBzZW5zICYmICEhc2Nyb2xsUG9zWCk7XG4gICAgdmFyIHZ5ID0gY2FuU2Nyb2xsWSAmJiAoTWF0aC5hYnMoYm90dG9tIC0geSkgPD0gc2VucyAmJiBzY3JvbGxQb3NZICsgaGVpZ2h0IDwgc2Nyb2xsSGVpZ2h0KSAtIChNYXRoLmFicyh0b3AgLSB5KSA8PSBzZW5zICYmICEhc2Nyb2xsUG9zWSk7XG5cbiAgICBpZiAoIWF1dG9TY3JvbGxzW2xheWVyc091dF0pIHtcbiAgICAgIGZvciAodmFyIGkgPSAwOyBpIDw9IGxheWVyc091dDsgaSsrKSB7XG4gICAgICAgIGlmICghYXV0b1Njcm9sbHNbaV0pIHtcbiAgICAgICAgICBhdXRvU2Nyb2xsc1tpXSA9IHt9O1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKGF1dG9TY3JvbGxzW2xheWVyc091dF0udnggIT0gdnggfHwgYXV0b1Njcm9sbHNbbGF5ZXJzT3V0XS52eSAhPSB2eSB8fCBhdXRvU2Nyb2xsc1tsYXllcnNPdXRdLmVsICE9PSBlbCkge1xuICAgICAgYXV0b1Njcm9sbHNbbGF5ZXJzT3V0XS5lbCA9IGVsO1xuICAgICAgYXV0b1Njcm9sbHNbbGF5ZXJzT3V0XS52eCA9IHZ4O1xuICAgICAgYXV0b1Njcm9sbHNbbGF5ZXJzT3V0XS52eSA9IHZ5O1xuICAgICAgY2xlYXJJbnRlcnZhbChhdXRvU2Nyb2xsc1tsYXllcnNPdXRdLnBpZCk7XG5cbiAgICAgIGlmICh2eCAhPSAwIHx8IHZ5ICE9IDApIHtcbiAgICAgICAgc2Nyb2xsVGhpc0luc3RhbmNlID0gdHJ1ZTtcbiAgICAgICAgLyoganNoaW50IGxvb3BmdW5jOnRydWUgKi9cblxuICAgICAgICBhdXRvU2Nyb2xsc1tsYXllcnNPdXRdLnBpZCA9IHNldEludGVydmFsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAvLyBlbXVsYXRlIGRyYWcgb3ZlciBkdXJpbmcgYXV0b3Njcm9sbCAoZmFsbGJhY2spLCBlbXVsYXRpbmcgbmF0aXZlIERuRCBiZWhhdmlvdXJcbiAgICAgICAgICBpZiAoaXNGYWxsYmFjayAmJiB0aGlzLmxheWVyID09PSAwKSB7XG4gICAgICAgICAgICBTb3J0YWJsZS5hY3RpdmUuX29uVG91Y2hNb3ZlKHRvdWNoRXZ0JDEpOyAvLyBUbyBtb3ZlIGdob3N0IGlmIGl0IGlzIHBvc2l0aW9uZWQgYWJzb2x1dGVseVxuXG4gICAgICAgICAgfVxuXG4gICAgICAgICAgdmFyIHNjcm9sbE9mZnNldFkgPSBhdXRvU2Nyb2xsc1t0aGlzLmxheWVyXS52eSA/IGF1dG9TY3JvbGxzW3RoaXMubGF5ZXJdLnZ5ICogc3BlZWQgOiAwO1xuICAgICAgICAgIHZhciBzY3JvbGxPZmZzZXRYID0gYXV0b1Njcm9sbHNbdGhpcy5sYXllcl0udnggPyBhdXRvU2Nyb2xsc1t0aGlzLmxheWVyXS52eCAqIHNwZWVkIDogMDtcblxuICAgICAgICAgIGlmICh0eXBlb2Ygc2Nyb2xsQ3VzdG9tRm4gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgIGlmIChzY3JvbGxDdXN0b21Gbi5jYWxsKFNvcnRhYmxlLmRyYWdnZWQucGFyZW50Tm9kZVtleHBhbmRvXSwgc2Nyb2xsT2Zmc2V0WCwgc2Nyb2xsT2Zmc2V0WSwgZXZ0LCB0b3VjaEV2dCQxLCBhdXRvU2Nyb2xsc1t0aGlzLmxheWVyXS5lbCkgIT09ICdjb250aW51ZScpIHtcbiAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cblxuICAgICAgICAgIHNjcm9sbEJ5KGF1dG9TY3JvbGxzW3RoaXMubGF5ZXJdLmVsLCBzY3JvbGxPZmZzZXRYLCBzY3JvbGxPZmZzZXRZKTtcbiAgICAgICAgfS5iaW5kKHtcbiAgICAgICAgICBsYXllcjogbGF5ZXJzT3V0XG4gICAgICAgIH0pLCAyNCk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgbGF5ZXJzT3V0Kys7XG4gIH0gd2hpbGUgKG9wdGlvbnMuYnViYmxlU2Nyb2xsICYmIGN1cnJlbnRQYXJlbnQgIT09IHdpblNjcm9sbGVyICYmIChjdXJyZW50UGFyZW50ID0gZ2V0UGFyZW50QXV0b1Njcm9sbEVsZW1lbnQoY3VycmVudFBhcmVudCwgZmFsc2UpKSk7XG5cbiAgc2Nyb2xsaW5nID0gc2Nyb2xsVGhpc0luc3RhbmNlOyAvLyBpbiBjYXNlIGFub3RoZXIgZnVuY3Rpb24gY2F0Y2hlcyBzY3JvbGxpbmcgYXMgZmFsc2UgaW4gYmV0d2VlbiB3aGVuIGl0IGlzIG5vdFxufSwgMzApO1xuXG52YXIgZHJvcCA9IGZ1bmN0aW9uIGRyb3AoX3JlZikge1xuICB2YXIgb3JpZ2luYWxFdmVudCA9IF9yZWYub3JpZ2luYWxFdmVudCxcbiAgICAgIHB1dFNvcnRhYmxlID0gX3JlZi5wdXRTb3J0YWJsZSxcbiAgICAgIGRyYWdFbCA9IF9yZWYuZHJhZ0VsLFxuICAgICAgYWN0aXZlU29ydGFibGUgPSBfcmVmLmFjdGl2ZVNvcnRhYmxlLFxuICAgICAgZGlzcGF0Y2hTb3J0YWJsZUV2ZW50ID0gX3JlZi5kaXNwYXRjaFNvcnRhYmxlRXZlbnQsXG4gICAgICBoaWRlR2hvc3RGb3JUYXJnZXQgPSBfcmVmLmhpZGVHaG9zdEZvclRhcmdldCxcbiAgICAgIHVuaGlkZUdob3N0Rm9yVGFyZ2V0ID0gX3JlZi51bmhpZGVHaG9zdEZvclRhcmdldDtcbiAgaWYgKCFvcmlnaW5hbEV2ZW50KSByZXR1cm47XG4gIHZhciB0b1NvcnRhYmxlID0gcHV0U29ydGFibGUgfHwgYWN0aXZlU29ydGFibGU7XG4gIGhpZGVHaG9zdEZvclRhcmdldCgpO1xuICB2YXIgdG91Y2ggPSBvcmlnaW5hbEV2ZW50LmNoYW5nZWRUb3VjaGVzICYmIG9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXMubGVuZ3RoID8gb3JpZ2luYWxFdmVudC5jaGFuZ2VkVG91Y2hlc1swXSA6IG9yaWdpbmFsRXZlbnQ7XG4gIHZhciB0YXJnZXQgPSBkb2N1bWVudC5lbGVtZW50RnJvbVBvaW50KHRvdWNoLmNsaWVudFgsIHRvdWNoLmNsaWVudFkpO1xuICB1bmhpZGVHaG9zdEZvclRhcmdldCgpO1xuXG4gIGlmICh0b1NvcnRhYmxlICYmICF0b1NvcnRhYmxlLmVsLmNvbnRhaW5zKHRhcmdldCkpIHtcbiAgICBkaXNwYXRjaFNvcnRhYmxlRXZlbnQoJ3NwaWxsJyk7XG4gICAgdGhpcy5vblNwaWxsKHtcbiAgICAgIGRyYWdFbDogZHJhZ0VsLFxuICAgICAgcHV0U29ydGFibGU6IHB1dFNvcnRhYmxlXG4gICAgfSk7XG4gIH1cbn07XG5cbmZ1bmN0aW9uIFJldmVydCgpIHt9XG5cblJldmVydC5wcm90b3R5cGUgPSB7XG4gIHN0YXJ0SW5kZXg6IG51bGwsXG4gIGRyYWdTdGFydDogZnVuY3Rpb24gZHJhZ1N0YXJ0KF9yZWYyKSB7XG4gICAgdmFyIG9sZERyYWdnYWJsZUluZGV4ID0gX3JlZjIub2xkRHJhZ2dhYmxlSW5kZXg7XG4gICAgdGhpcy5zdGFydEluZGV4ID0gb2xkRHJhZ2dhYmxlSW5kZXg7XG4gIH0sXG4gIG9uU3BpbGw6IGZ1bmN0aW9uIG9uU3BpbGwoX3JlZjMpIHtcbiAgICB2YXIgZHJhZ0VsID0gX3JlZjMuZHJhZ0VsLFxuICAgICAgICBwdXRTb3J0YWJsZSA9IF9yZWYzLnB1dFNvcnRhYmxlO1xuICAgIHRoaXMuc29ydGFibGUuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG5cbiAgICBpZiAocHV0U29ydGFibGUpIHtcbiAgICAgIHB1dFNvcnRhYmxlLmNhcHR1cmVBbmltYXRpb25TdGF0ZSgpO1xuICAgIH1cblxuICAgIHZhciBuZXh0U2libGluZyA9IGdldENoaWxkKHRoaXMuc29ydGFibGUuZWwsIHRoaXMuc3RhcnRJbmRleCwgdGhpcy5vcHRpb25zKTtcblxuICAgIGlmIChuZXh0U2libGluZykge1xuICAgICAgdGhpcy5zb3J0YWJsZS5lbC5pbnNlcnRCZWZvcmUoZHJhZ0VsLCBuZXh0U2libGluZyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuc29ydGFibGUuZWwuYXBwZW5kQ2hpbGQoZHJhZ0VsKTtcbiAgICB9XG5cbiAgICB0aGlzLnNvcnRhYmxlLmFuaW1hdGVBbGwoKTtcblxuICAgIGlmIChwdXRTb3J0YWJsZSkge1xuICAgICAgcHV0U29ydGFibGUuYW5pbWF0ZUFsbCgpO1xuICAgIH1cbiAgfSxcbiAgZHJvcDogZHJvcFxufTtcblxuX2V4dGVuZHMoUmV2ZXJ0LCB7XG4gIHBsdWdpbk5hbWU6ICdyZXZlcnRPblNwaWxsJ1xufSk7XG5cbmZ1bmN0aW9uIFJlbW92ZSgpIHt9XG5cblJlbW92ZS5wcm90b3R5cGUgPSB7XG4gIG9uU3BpbGw6IGZ1bmN0aW9uIG9uU3BpbGwoX3JlZjQpIHtcbiAgICB2YXIgZHJhZ0VsID0gX3JlZjQuZHJhZ0VsLFxuICAgICAgICBwdXRTb3J0YWJsZSA9IF9yZWY0LnB1dFNvcnRhYmxlO1xuICAgIHZhciBwYXJlbnRTb3J0YWJsZSA9IHB1dFNvcnRhYmxlIHx8IHRoaXMuc29ydGFibGU7XG4gICAgcGFyZW50U29ydGFibGUuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG4gICAgZHJhZ0VsLnBhcmVudE5vZGUgJiYgZHJhZ0VsLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoZHJhZ0VsKTtcbiAgICBwYXJlbnRTb3J0YWJsZS5hbmltYXRlQWxsKCk7XG4gIH0sXG4gIGRyb3A6IGRyb3Bcbn07XG5cbl9leHRlbmRzKFJlbW92ZSwge1xuICBwbHVnaW5OYW1lOiAncmVtb3ZlT25TcGlsbCdcbn0pO1xuXG52YXIgbGFzdFN3YXBFbDtcblxuZnVuY3Rpb24gU3dhcFBsdWdpbigpIHtcbiAgZnVuY3Rpb24gU3dhcCgpIHtcbiAgICB0aGlzLmRlZmF1bHRzID0ge1xuICAgICAgc3dhcENsYXNzOiAnc29ydGFibGUtc3dhcC1oaWdobGlnaHQnXG4gICAgfTtcbiAgfVxuXG4gIFN3YXAucHJvdG90eXBlID0ge1xuICAgIGRyYWdTdGFydDogZnVuY3Rpb24gZHJhZ1N0YXJ0KF9yZWYpIHtcbiAgICAgIHZhciBkcmFnRWwgPSBfcmVmLmRyYWdFbDtcbiAgICAgIGxhc3RTd2FwRWwgPSBkcmFnRWw7XG4gICAgfSxcbiAgICBkcmFnT3ZlclZhbGlkOiBmdW5jdGlvbiBkcmFnT3ZlclZhbGlkKF9yZWYyKSB7XG4gICAgICB2YXIgY29tcGxldGVkID0gX3JlZjIuY29tcGxldGVkLFxuICAgICAgICAgIHRhcmdldCA9IF9yZWYyLnRhcmdldCxcbiAgICAgICAgICBvbk1vdmUgPSBfcmVmMi5vbk1vdmUsXG4gICAgICAgICAgYWN0aXZlU29ydGFibGUgPSBfcmVmMi5hY3RpdmVTb3J0YWJsZSxcbiAgICAgICAgICBjaGFuZ2VkID0gX3JlZjIuY2hhbmdlZCxcbiAgICAgICAgICBjYW5jZWwgPSBfcmVmMi5jYW5jZWw7XG4gICAgICBpZiAoIWFjdGl2ZVNvcnRhYmxlLm9wdGlvbnMuc3dhcCkgcmV0dXJuO1xuICAgICAgdmFyIGVsID0gdGhpcy5zb3J0YWJsZS5lbCxcbiAgICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zO1xuXG4gICAgICBpZiAodGFyZ2V0ICYmIHRhcmdldCAhPT0gZWwpIHtcbiAgICAgICAgdmFyIHByZXZTd2FwRWwgPSBsYXN0U3dhcEVsO1xuXG4gICAgICAgIGlmIChvbk1vdmUodGFyZ2V0KSAhPT0gZmFsc2UpIHtcbiAgICAgICAgICB0b2dnbGVDbGFzcyh0YXJnZXQsIG9wdGlvbnMuc3dhcENsYXNzLCB0cnVlKTtcbiAgICAgICAgICBsYXN0U3dhcEVsID0gdGFyZ2V0O1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGxhc3RTd2FwRWwgPSBudWxsO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHByZXZTd2FwRWwgJiYgcHJldlN3YXBFbCAhPT0gbGFzdFN3YXBFbCkge1xuICAgICAgICAgIHRvZ2dsZUNsYXNzKHByZXZTd2FwRWwsIG9wdGlvbnMuc3dhcENsYXNzLCBmYWxzZSk7XG4gICAgICAgIH1cbiAgICAgIH1cblxuICAgICAgY2hhbmdlZCgpO1xuICAgICAgY29tcGxldGVkKHRydWUpO1xuICAgICAgY2FuY2VsKCk7XG4gICAgfSxcbiAgICBkcm9wOiBmdW5jdGlvbiBkcm9wKF9yZWYzKSB7XG4gICAgICB2YXIgYWN0aXZlU29ydGFibGUgPSBfcmVmMy5hY3RpdmVTb3J0YWJsZSxcbiAgICAgICAgICBwdXRTb3J0YWJsZSA9IF9yZWYzLnB1dFNvcnRhYmxlLFxuICAgICAgICAgIGRyYWdFbCA9IF9yZWYzLmRyYWdFbDtcbiAgICAgIHZhciB0b1NvcnRhYmxlID0gcHV0U29ydGFibGUgfHwgdGhpcy5zb3J0YWJsZTtcbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zO1xuICAgICAgbGFzdFN3YXBFbCAmJiB0b2dnbGVDbGFzcyhsYXN0U3dhcEVsLCBvcHRpb25zLnN3YXBDbGFzcywgZmFsc2UpO1xuXG4gICAgICBpZiAobGFzdFN3YXBFbCAmJiAob3B0aW9ucy5zd2FwIHx8IHB1dFNvcnRhYmxlICYmIHB1dFNvcnRhYmxlLm9wdGlvbnMuc3dhcCkpIHtcbiAgICAgICAgaWYgKGRyYWdFbCAhPT0gbGFzdFN3YXBFbCkge1xuICAgICAgICAgIHRvU29ydGFibGUuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG4gICAgICAgICAgaWYgKHRvU29ydGFibGUgIT09IGFjdGl2ZVNvcnRhYmxlKSBhY3RpdmVTb3J0YWJsZS5jYXB0dXJlQW5pbWF0aW9uU3RhdGUoKTtcbiAgICAgICAgICBzd2FwTm9kZXMoZHJhZ0VsLCBsYXN0U3dhcEVsKTtcbiAgICAgICAgICB0b1NvcnRhYmxlLmFuaW1hdGVBbGwoKTtcbiAgICAgICAgICBpZiAodG9Tb3J0YWJsZSAhPT0gYWN0aXZlU29ydGFibGUpIGFjdGl2ZVNvcnRhYmxlLmFuaW1hdGVBbGwoKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sXG4gICAgbnVsbGluZzogZnVuY3Rpb24gbnVsbGluZygpIHtcbiAgICAgIGxhc3RTd2FwRWwgPSBudWxsO1xuICAgIH1cbiAgfTtcbiAgcmV0dXJuIF9leHRlbmRzKFN3YXAsIHtcbiAgICBwbHVnaW5OYW1lOiAnc3dhcCcsXG4gICAgZXZlbnRQcm9wZXJ0aWVzOiBmdW5jdGlvbiBldmVudFByb3BlcnRpZXMoKSB7XG4gICAgICByZXR1cm4ge1xuICAgICAgICBzd2FwSXRlbTogbGFzdFN3YXBFbFxuICAgICAgfTtcbiAgICB9XG4gIH0pO1xufVxuXG5mdW5jdGlvbiBzd2FwTm9kZXMobjEsIG4yKSB7XG4gIHZhciBwMSA9IG4xLnBhcmVudE5vZGUsXG4gICAgICBwMiA9IG4yLnBhcmVudE5vZGUsXG4gICAgICBpMSxcbiAgICAgIGkyO1xuICBpZiAoIXAxIHx8ICFwMiB8fCBwMS5pc0VxdWFsTm9kZShuMikgfHwgcDIuaXNFcXVhbE5vZGUobjEpKSByZXR1cm47XG4gIGkxID0gaW5kZXgobjEpO1xuICBpMiA9IGluZGV4KG4yKTtcblxuICBpZiAocDEuaXNFcXVhbE5vZGUocDIpICYmIGkxIDwgaTIpIHtcbiAgICBpMisrO1xuICB9XG5cbiAgcDEuaW5zZXJ0QmVmb3JlKG4yLCBwMS5jaGlsZHJlbltpMV0pO1xuICBwMi5pbnNlcnRCZWZvcmUobjEsIHAyLmNoaWxkcmVuW2kyXSk7XG59XG5cbnZhciBtdWx0aURyYWdFbGVtZW50cyA9IFtdLFxuICAgIG11bHRpRHJhZ0Nsb25lcyA9IFtdLFxuICAgIGxhc3RNdWx0aURyYWdTZWxlY3QsXG4gICAgLy8gZm9yIHNlbGVjdGlvbiB3aXRoIG1vZGlmaWVyIGtleSBkb3duIChTSElGVClcbm11bHRpRHJhZ1NvcnRhYmxlLFxuICAgIGluaXRpYWxGb2xkaW5nID0gZmFsc2UsXG4gICAgLy8gSW5pdGlhbCBtdWx0aS1kcmFnIGZvbGQgd2hlbiBkcmFnIHN0YXJ0ZWRcbmZvbGRpbmcgPSBmYWxzZSxcbiAgICAvLyBGb2xkaW5nIGFueSBvdGhlciB0aW1lXG5kcmFnU3RhcnRlZCA9IGZhbHNlLFxuICAgIGRyYWdFbCQxLFxuICAgIGNsb25lc0Zyb21SZWN0LFxuICAgIGNsb25lc0hpZGRlbjtcblxuZnVuY3Rpb24gTXVsdGlEcmFnUGx1Z2luKCkge1xuICBmdW5jdGlvbiBNdWx0aURyYWcoc29ydGFibGUpIHtcbiAgICAvLyBCaW5kIGFsbCBwcml2YXRlIG1ldGhvZHNcbiAgICBmb3IgKHZhciBmbiBpbiB0aGlzKSB7XG4gICAgICBpZiAoZm4uY2hhckF0KDApID09PSAnXycgJiYgdHlwZW9mIHRoaXNbZm5dID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRoaXNbZm5dID0gdGhpc1tmbl0uYmluZCh0aGlzKTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBpZiAoIXNvcnRhYmxlLm9wdGlvbnMuYXZvaWRJbXBsaWNpdERlc2VsZWN0KSB7XG4gICAgICBpZiAoc29ydGFibGUub3B0aW9ucy5zdXBwb3J0UG9pbnRlcikge1xuICAgICAgICBvbihkb2N1bWVudCwgJ3BvaW50ZXJ1cCcsIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIG9uKGRvY3VtZW50LCAnbW91c2V1cCcsIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKTtcbiAgICAgICAgb24oZG9jdW1lbnQsICd0b3VjaGVuZCcsIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBvbihkb2N1bWVudCwgJ2tleWRvd24nLCB0aGlzLl9jaGVja0tleURvd24pO1xuICAgIG9uKGRvY3VtZW50LCAna2V5dXAnLCB0aGlzLl9jaGVja0tleVVwKTtcbiAgICB0aGlzLmRlZmF1bHRzID0ge1xuICAgICAgc2VsZWN0ZWRDbGFzczogJ3NvcnRhYmxlLXNlbGVjdGVkJyxcbiAgICAgIG11bHRpRHJhZ0tleTogbnVsbCxcbiAgICAgIGF2b2lkSW1wbGljaXREZXNlbGVjdDogZmFsc2UsXG4gICAgICBzZXREYXRhOiBmdW5jdGlvbiBzZXREYXRhKGRhdGFUcmFuc2ZlciwgZHJhZ0VsKSB7XG4gICAgICAgIHZhciBkYXRhID0gJyc7XG5cbiAgICAgICAgaWYgKG11bHRpRHJhZ0VsZW1lbnRzLmxlbmd0aCAmJiBtdWx0aURyYWdTb3J0YWJsZSA9PT0gc29ydGFibGUpIHtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50LCBpKSB7XG4gICAgICAgICAgICBkYXRhICs9ICghaSA/ICcnIDogJywgJykgKyBtdWx0aURyYWdFbGVtZW50LnRleHRDb250ZW50O1xuICAgICAgICAgIH0pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGRhdGEgPSBkcmFnRWwudGV4dENvbnRlbnQ7XG4gICAgICAgIH1cblxuICAgICAgICBkYXRhVHJhbnNmZXIuc2V0RGF0YSgnVGV4dCcsIGRhdGEpO1xuICAgICAgfVxuICAgIH07XG4gIH1cblxuICBNdWx0aURyYWcucHJvdG90eXBlID0ge1xuICAgIG11bHRpRHJhZ0tleURvd246IGZhbHNlLFxuICAgIGlzTXVsdGlEcmFnOiBmYWxzZSxcbiAgICBkZWxheVN0YXJ0R2xvYmFsOiBmdW5jdGlvbiBkZWxheVN0YXJ0R2xvYmFsKF9yZWYpIHtcbiAgICAgIHZhciBkcmFnZ2VkID0gX3JlZi5kcmFnRWw7XG4gICAgICBkcmFnRWwkMSA9IGRyYWdnZWQ7XG4gICAgfSxcbiAgICBkZWxheUVuZGVkOiBmdW5jdGlvbiBkZWxheUVuZGVkKCkge1xuICAgICAgdGhpcy5pc011bHRpRHJhZyA9IH5tdWx0aURyYWdFbGVtZW50cy5pbmRleE9mKGRyYWdFbCQxKTtcbiAgICB9LFxuICAgIHNldHVwQ2xvbmU6IGZ1bmN0aW9uIHNldHVwQ2xvbmUoX3JlZjIpIHtcbiAgICAgIHZhciBzb3J0YWJsZSA9IF9yZWYyLnNvcnRhYmxlLFxuICAgICAgICAgIGNhbmNlbCA9IF9yZWYyLmNhbmNlbDtcbiAgICAgIGlmICghdGhpcy5pc011bHRpRHJhZykgcmV0dXJuO1xuXG4gICAgICBmb3IgKHZhciBpID0gMDsgaSA8IG11bHRpRHJhZ0VsZW1lbnRzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgIG11bHRpRHJhZ0Nsb25lcy5wdXNoKGNsb25lKG11bHRpRHJhZ0VsZW1lbnRzW2ldKSk7XG4gICAgICAgIG11bHRpRHJhZ0Nsb25lc1tpXS5zb3J0YWJsZUluZGV4ID0gbXVsdGlEcmFnRWxlbWVudHNbaV0uc29ydGFibGVJbmRleDtcbiAgICAgICAgbXVsdGlEcmFnQ2xvbmVzW2ldLmRyYWdnYWJsZSA9IGZhbHNlO1xuICAgICAgICBtdWx0aURyYWdDbG9uZXNbaV0uc3R5bGVbJ3dpbGwtY2hhbmdlJ10gPSAnJztcbiAgICAgICAgdG9nZ2xlQ2xhc3MobXVsdGlEcmFnQ2xvbmVzW2ldLCB0aGlzLm9wdGlvbnMuc2VsZWN0ZWRDbGFzcywgZmFsc2UpO1xuICAgICAgICBtdWx0aURyYWdFbGVtZW50c1tpXSA9PT0gZHJhZ0VsJDEgJiYgdG9nZ2xlQ2xhc3MobXVsdGlEcmFnQ2xvbmVzW2ldLCB0aGlzLm9wdGlvbnMuY2hvc2VuQ2xhc3MsIGZhbHNlKTtcbiAgICAgIH1cblxuICAgICAgc29ydGFibGUuX2hpZGVDbG9uZSgpO1xuXG4gICAgICBjYW5jZWwoKTtcbiAgICB9LFxuICAgIGNsb25lOiBmdW5jdGlvbiBjbG9uZShfcmVmMykge1xuICAgICAgdmFyIHNvcnRhYmxlID0gX3JlZjMuc29ydGFibGUsXG4gICAgICAgICAgcm9vdEVsID0gX3JlZjMucm9vdEVsLFxuICAgICAgICAgIGRpc3BhdGNoU29ydGFibGVFdmVudCA9IF9yZWYzLmRpc3BhdGNoU29ydGFibGVFdmVudCxcbiAgICAgICAgICBjYW5jZWwgPSBfcmVmMy5jYW5jZWw7XG4gICAgICBpZiAoIXRoaXMuaXNNdWx0aURyYWcpIHJldHVybjtcblxuICAgICAgaWYgKCF0aGlzLm9wdGlvbnMucmVtb3ZlQ2xvbmVPbkhpZGUpIHtcbiAgICAgICAgaWYgKG11bHRpRHJhZ0VsZW1lbnRzLmxlbmd0aCAmJiBtdWx0aURyYWdTb3J0YWJsZSA9PT0gc29ydGFibGUpIHtcbiAgICAgICAgICBpbnNlcnRNdWx0aURyYWdDbG9uZXModHJ1ZSwgcm9vdEVsKTtcbiAgICAgICAgICBkaXNwYXRjaFNvcnRhYmxlRXZlbnQoJ2Nsb25lJyk7XG4gICAgICAgICAgY2FuY2VsKCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LFxuICAgIHNob3dDbG9uZTogZnVuY3Rpb24gc2hvd0Nsb25lKF9yZWY0KSB7XG4gICAgICB2YXIgY2xvbmVOb3dTaG93biA9IF9yZWY0LmNsb25lTm93U2hvd24sXG4gICAgICAgICAgcm9vdEVsID0gX3JlZjQucm9vdEVsLFxuICAgICAgICAgIGNhbmNlbCA9IF9yZWY0LmNhbmNlbDtcbiAgICAgIGlmICghdGhpcy5pc011bHRpRHJhZykgcmV0dXJuO1xuICAgICAgaW5zZXJ0TXVsdGlEcmFnQ2xvbmVzKGZhbHNlLCByb290RWwpO1xuICAgICAgbXVsdGlEcmFnQ2xvbmVzLmZvckVhY2goZnVuY3Rpb24gKGNsb25lKSB7XG4gICAgICAgIGNzcyhjbG9uZSwgJ2Rpc3BsYXknLCAnJyk7XG4gICAgICB9KTtcbiAgICAgIGNsb25lTm93U2hvd24oKTtcbiAgICAgIGNsb25lc0hpZGRlbiA9IGZhbHNlO1xuICAgICAgY2FuY2VsKCk7XG4gICAgfSxcbiAgICBoaWRlQ2xvbmU6IGZ1bmN0aW9uIGhpZGVDbG9uZShfcmVmNSkge1xuICAgICAgdmFyIF90aGlzID0gdGhpcztcblxuICAgICAgdmFyIHNvcnRhYmxlID0gX3JlZjUuc29ydGFibGUsXG4gICAgICAgICAgY2xvbmVOb3dIaWRkZW4gPSBfcmVmNS5jbG9uZU5vd0hpZGRlbixcbiAgICAgICAgICBjYW5jZWwgPSBfcmVmNS5jYW5jZWw7XG4gICAgICBpZiAoIXRoaXMuaXNNdWx0aURyYWcpIHJldHVybjtcbiAgICAgIG11bHRpRHJhZ0Nsb25lcy5mb3JFYWNoKGZ1bmN0aW9uIChjbG9uZSkge1xuICAgICAgICBjc3MoY2xvbmUsICdkaXNwbGF5JywgJ25vbmUnKTtcblxuICAgICAgICBpZiAoX3RoaXMub3B0aW9ucy5yZW1vdmVDbG9uZU9uSGlkZSAmJiBjbG9uZS5wYXJlbnROb2RlKSB7XG4gICAgICAgICAgY2xvbmUucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChjbG9uZSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgICAgY2xvbmVOb3dIaWRkZW4oKTtcbiAgICAgIGNsb25lc0hpZGRlbiA9IHRydWU7XG4gICAgICBjYW5jZWwoKTtcbiAgICB9LFxuICAgIGRyYWdTdGFydEdsb2JhbDogZnVuY3Rpb24gZHJhZ1N0YXJ0R2xvYmFsKF9yZWY2KSB7XG4gICAgICB2YXIgc29ydGFibGUgPSBfcmVmNi5zb3J0YWJsZTtcblxuICAgICAgaWYgKCF0aGlzLmlzTXVsdGlEcmFnICYmIG11bHRpRHJhZ1NvcnRhYmxlKSB7XG4gICAgICAgIG11bHRpRHJhZ1NvcnRhYmxlLm11bHRpRHJhZy5fZGVzZWxlY3RNdWx0aURyYWcoKTtcbiAgICAgIH1cblxuICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICBtdWx0aURyYWdFbGVtZW50LnNvcnRhYmxlSW5kZXggPSBpbmRleChtdWx0aURyYWdFbGVtZW50KTtcbiAgICAgIH0pOyAvLyBTb3J0IG11bHRpLWRyYWcgZWxlbWVudHNcblxuICAgICAgbXVsdGlEcmFnRWxlbWVudHMgPSBtdWx0aURyYWdFbGVtZW50cy5zb3J0KGZ1bmN0aW9uIChhLCBiKSB7XG4gICAgICAgIHJldHVybiBhLnNvcnRhYmxlSW5kZXggLSBiLnNvcnRhYmxlSW5kZXg7XG4gICAgICB9KTtcbiAgICAgIGRyYWdTdGFydGVkID0gdHJ1ZTtcbiAgICB9LFxuICAgIGRyYWdTdGFydGVkOiBmdW5jdGlvbiBkcmFnU3RhcnRlZChfcmVmNykge1xuICAgICAgdmFyIF90aGlzMiA9IHRoaXM7XG5cbiAgICAgIHZhciBzb3J0YWJsZSA9IF9yZWY3LnNvcnRhYmxlO1xuICAgICAgaWYgKCF0aGlzLmlzTXVsdGlEcmFnKSByZXR1cm47XG5cbiAgICAgIGlmICh0aGlzLm9wdGlvbnMuc29ydCkge1xuICAgICAgICAvLyBDYXB0dXJlIHJlY3RzLFxuICAgICAgICAvLyBoaWRlIG11bHRpIGRyYWcgZWxlbWVudHMgKGJ5IHBvc2l0aW9uaW5nIHRoZW0gYWJzb2x1dGUpLFxuICAgICAgICAvLyBzZXQgbXVsdGkgZHJhZyBlbGVtZW50cyByZWN0cyB0byBkcmFnUmVjdCxcbiAgICAgICAgLy8gc2hvdyBtdWx0aSBkcmFnIGVsZW1lbnRzLFxuICAgICAgICAvLyBhbmltYXRlIHRvIHJlY3RzLFxuICAgICAgICAvLyB1bnNldCByZWN0cyAmIHJlbW92ZSBmcm9tIERPTVxuICAgICAgICBzb3J0YWJsZS5jYXB0dXJlQW5pbWF0aW9uU3RhdGUoKTtcblxuICAgICAgICBpZiAodGhpcy5vcHRpb25zLmFuaW1hdGlvbikge1xuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgICAgIGlmIChtdWx0aURyYWdFbGVtZW50ID09PSBkcmFnRWwkMSkgcmV0dXJuO1xuICAgICAgICAgICAgY3NzKG11bHRpRHJhZ0VsZW1lbnQsICdwb3NpdGlvbicsICdhYnNvbHV0ZScpO1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIHZhciBkcmFnUmVjdCA9IGdldFJlY3QoZHJhZ0VsJDEsIGZhbHNlLCB0cnVlLCB0cnVlKTtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50KSB7XG4gICAgICAgICAgICBpZiAobXVsdGlEcmFnRWxlbWVudCA9PT0gZHJhZ0VsJDEpIHJldHVybjtcbiAgICAgICAgICAgIHNldFJlY3QobXVsdGlEcmFnRWxlbWVudCwgZHJhZ1JlY3QpO1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIGZvbGRpbmcgPSB0cnVlO1xuICAgICAgICAgIGluaXRpYWxGb2xkaW5nID0gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBzb3J0YWJsZS5hbmltYXRlQWxsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgZm9sZGluZyA9IGZhbHNlO1xuICAgICAgICBpbml0aWFsRm9sZGluZyA9IGZhbHNlO1xuXG4gICAgICAgIGlmIChfdGhpczIub3B0aW9ucy5hbmltYXRpb24pIHtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50KSB7XG4gICAgICAgICAgICB1bnNldFJlY3QobXVsdGlEcmFnRWxlbWVudCk7XG4gICAgICAgICAgfSk7XG4gICAgICAgIH0gLy8gUmVtb3ZlIGFsbCBhdXhpbGlhcnkgbXVsdGlkcmFnIGl0ZW1zIGZyb20gZWwsIGlmIHNvcnRpbmcgZW5hYmxlZFxuXG5cbiAgICAgICAgaWYgKF90aGlzMi5vcHRpb25zLnNvcnQpIHtcbiAgICAgICAgICByZW1vdmVNdWx0aURyYWdFbGVtZW50cygpO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9LFxuICAgIGRyYWdPdmVyOiBmdW5jdGlvbiBkcmFnT3ZlcihfcmVmOCkge1xuICAgICAgdmFyIHRhcmdldCA9IF9yZWY4LnRhcmdldCxcbiAgICAgICAgICBjb21wbGV0ZWQgPSBfcmVmOC5jb21wbGV0ZWQsXG4gICAgICAgICAgY2FuY2VsID0gX3JlZjguY2FuY2VsO1xuXG4gICAgICBpZiAoZm9sZGluZyAmJiB+bXVsdGlEcmFnRWxlbWVudHMuaW5kZXhPZih0YXJnZXQpKSB7XG4gICAgICAgIGNvbXBsZXRlZChmYWxzZSk7XG4gICAgICAgIGNhbmNlbCgpO1xuICAgICAgfVxuICAgIH0sXG4gICAgcmV2ZXJ0OiBmdW5jdGlvbiByZXZlcnQoX3JlZjkpIHtcbiAgICAgIHZhciBmcm9tU29ydGFibGUgPSBfcmVmOS5mcm9tU29ydGFibGUsXG4gICAgICAgICAgcm9vdEVsID0gX3JlZjkucm9vdEVsLFxuICAgICAgICAgIHNvcnRhYmxlID0gX3JlZjkuc29ydGFibGUsXG4gICAgICAgICAgZHJhZ1JlY3QgPSBfcmVmOS5kcmFnUmVjdDtcblxuICAgICAgaWYgKG11bHRpRHJhZ0VsZW1lbnRzLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgLy8gU2V0dXAgdW5mb2xkIGFuaW1hdGlvblxuICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50KSB7XG4gICAgICAgICAgc29ydGFibGUuYWRkQW5pbWF0aW9uU3RhdGUoe1xuICAgICAgICAgICAgdGFyZ2V0OiBtdWx0aURyYWdFbGVtZW50LFxuICAgICAgICAgICAgcmVjdDogZm9sZGluZyA/IGdldFJlY3QobXVsdGlEcmFnRWxlbWVudCkgOiBkcmFnUmVjdFxuICAgICAgICAgIH0pO1xuICAgICAgICAgIHVuc2V0UmVjdChtdWx0aURyYWdFbGVtZW50KTtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50LmZyb21SZWN0ID0gZHJhZ1JlY3Q7XG4gICAgICAgICAgZnJvbVNvcnRhYmxlLnJlbW92ZUFuaW1hdGlvblN0YXRlKG11bHRpRHJhZ0VsZW1lbnQpO1xuICAgICAgICB9KTtcbiAgICAgICAgZm9sZGluZyA9IGZhbHNlO1xuICAgICAgICBpbnNlcnRNdWx0aURyYWdFbGVtZW50cyghdGhpcy5vcHRpb25zLnJlbW92ZUNsb25lT25IaWRlLCByb290RWwpO1xuICAgICAgfVxuICAgIH0sXG4gICAgZHJhZ092ZXJDb21wbGV0ZWQ6IGZ1bmN0aW9uIGRyYWdPdmVyQ29tcGxldGVkKF9yZWYxMCkge1xuICAgICAgdmFyIHNvcnRhYmxlID0gX3JlZjEwLnNvcnRhYmxlLFxuICAgICAgICAgIGlzT3duZXIgPSBfcmVmMTAuaXNPd25lcixcbiAgICAgICAgICBpbnNlcnRpb24gPSBfcmVmMTAuaW5zZXJ0aW9uLFxuICAgICAgICAgIGFjdGl2ZVNvcnRhYmxlID0gX3JlZjEwLmFjdGl2ZVNvcnRhYmxlLFxuICAgICAgICAgIHBhcmVudEVsID0gX3JlZjEwLnBhcmVudEVsLFxuICAgICAgICAgIHB1dFNvcnRhYmxlID0gX3JlZjEwLnB1dFNvcnRhYmxlO1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7XG5cbiAgICAgIGlmIChpbnNlcnRpb24pIHtcbiAgICAgICAgLy8gQ2xvbmVzIG11c3QgYmUgaGlkZGVuIGJlZm9yZSBmb2xkaW5nIGFuaW1hdGlvbiB0byBjYXB0dXJlIGRyYWdSZWN0QWJzb2x1dGUgcHJvcGVybHlcbiAgICAgICAgaWYgKGlzT3duZXIpIHtcbiAgICAgICAgICBhY3RpdmVTb3J0YWJsZS5faGlkZUNsb25lKCk7XG4gICAgICAgIH1cblxuICAgICAgICBpbml0aWFsRm9sZGluZyA9IGZhbHNlOyAvLyBJZiBsZWF2aW5nIHNvcnQ6ZmFsc2Ugcm9vdCwgb3IgYWxyZWFkeSBmb2xkaW5nIC0gRm9sZCB0byBuZXcgbG9jYXRpb25cblxuICAgICAgICBpZiAob3B0aW9ucy5hbmltYXRpb24gJiYgbXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoID4gMSAmJiAoZm9sZGluZyB8fCAhaXNPd25lciAmJiAhYWN0aXZlU29ydGFibGUub3B0aW9ucy5zb3J0ICYmICFwdXRTb3J0YWJsZSkpIHtcbiAgICAgICAgICAvLyBGb2xkOiBTZXQgYWxsIG11bHRpIGRyYWcgZWxlbWVudHMncyByZWN0cyB0byBkcmFnRWwncyByZWN0IHdoZW4gbXVsdGktZHJhZyBlbGVtZW50cyBhcmUgaW52aXNpYmxlXG4gICAgICAgICAgdmFyIGRyYWdSZWN0QWJzb2x1dGUgPSBnZXRSZWN0KGRyYWdFbCQxLCBmYWxzZSwgdHJ1ZSwgdHJ1ZSk7XG4gICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICAgICAgaWYgKG11bHRpRHJhZ0VsZW1lbnQgPT09IGRyYWdFbCQxKSByZXR1cm47XG4gICAgICAgICAgICBzZXRSZWN0KG11bHRpRHJhZ0VsZW1lbnQsIGRyYWdSZWN0QWJzb2x1dGUpOyAvLyBNb3ZlIGVsZW1lbnQocykgdG8gZW5kIG9mIHBhcmVudEVsIHNvIHRoYXQgaXQgZG9lcyBub3QgaW50ZXJmZXJlIHdpdGggbXVsdGktZHJhZyBjbG9uZXMgaW5zZXJ0aW9uIGlmIHRoZXkgYXJlIGluc2VydGVkXG4gICAgICAgICAgICAvLyB3aGlsZSBmb2xkaW5nLCBhbmQgc28gdGhhdCB3ZSBjYW4gY2FwdHVyZSB0aGVtIGFnYWluIGJlY2F1c2Ugb2xkIHNvcnRhYmxlIHdpbGwgbm8gbG9uZ2VyIGJlIGZyb21Tb3J0YWJsZVxuXG4gICAgICAgICAgICBwYXJlbnRFbC5hcHBlbmRDaGlsZChtdWx0aURyYWdFbGVtZW50KTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBmb2xkaW5nID0gdHJ1ZTtcbiAgICAgICAgfSAvLyBDbG9uZXMgbXVzdCBiZSBzaG93biAoYW5kIGNoZWNrIHRvIHJlbW92ZSBtdWx0aSBkcmFncykgYWZ0ZXIgZm9sZGluZyB3aGVuIGludGVyZmVyaW5nIG11bHRpRHJhZ0VsZW1lbnRzIGFyZSBtb3ZlZCBvdXRcblxuXG4gICAgICAgIGlmICghaXNPd25lcikge1xuICAgICAgICAgIC8vIE9ubHkgcmVtb3ZlIGlmIG5vdCBmb2xkaW5nIChmb2xkaW5nIHdpbGwgcmVtb3ZlIHRoZW0gYW55d2F5cylcbiAgICAgICAgICBpZiAoIWZvbGRpbmcpIHtcbiAgICAgICAgICAgIHJlbW92ZU11bHRpRHJhZ0VsZW1lbnRzKCk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgaWYgKG11bHRpRHJhZ0VsZW1lbnRzLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICAgIHZhciBjbG9uZXNIaWRkZW5CZWZvcmUgPSBjbG9uZXNIaWRkZW47XG5cbiAgICAgICAgICAgIGFjdGl2ZVNvcnRhYmxlLl9zaG93Q2xvbmUoc29ydGFibGUpOyAvLyBVbmZvbGQgYW5pbWF0aW9uIGZvciBjbG9uZXMgaWYgc2hvd2luZyBmcm9tIGhpZGRlblxuXG5cbiAgICAgICAgICAgIGlmIChhY3RpdmVTb3J0YWJsZS5vcHRpb25zLmFuaW1hdGlvbiAmJiAhY2xvbmVzSGlkZGVuICYmIGNsb25lc0hpZGRlbkJlZm9yZSkge1xuICAgICAgICAgICAgICBtdWx0aURyYWdDbG9uZXMuZm9yRWFjaChmdW5jdGlvbiAoY2xvbmUpIHtcbiAgICAgICAgICAgICAgICBhY3RpdmVTb3J0YWJsZS5hZGRBbmltYXRpb25TdGF0ZSh7XG4gICAgICAgICAgICAgICAgICB0YXJnZXQ6IGNsb25lLFxuICAgICAgICAgICAgICAgICAgcmVjdDogY2xvbmVzRnJvbVJlY3RcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBjbG9uZS5mcm9tUmVjdCA9IGNsb25lc0Zyb21SZWN0O1xuICAgICAgICAgICAgICAgIGNsb25lLnRoaXNBbmltYXRpb25EdXJhdGlvbiA9IG51bGw7XG4gICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBhY3RpdmVTb3J0YWJsZS5fc2hvd0Nsb25lKHNvcnRhYmxlKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LFxuICAgIGRyYWdPdmVyQW5pbWF0aW9uQ2FwdHVyZTogZnVuY3Rpb24gZHJhZ092ZXJBbmltYXRpb25DYXB0dXJlKF9yZWYxMSkge1xuICAgICAgdmFyIGRyYWdSZWN0ID0gX3JlZjExLmRyYWdSZWN0LFxuICAgICAgICAgIGlzT3duZXIgPSBfcmVmMTEuaXNPd25lcixcbiAgICAgICAgICBhY3RpdmVTb3J0YWJsZSA9IF9yZWYxMS5hY3RpdmVTb3J0YWJsZTtcbiAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgbXVsdGlEcmFnRWxlbWVudC50aGlzQW5pbWF0aW9uRHVyYXRpb24gPSBudWxsO1xuICAgICAgfSk7XG5cbiAgICAgIGlmIChhY3RpdmVTb3J0YWJsZS5vcHRpb25zLmFuaW1hdGlvbiAmJiAhaXNPd25lciAmJiBhY3RpdmVTb3J0YWJsZS5tdWx0aURyYWcuaXNNdWx0aURyYWcpIHtcbiAgICAgICAgY2xvbmVzRnJvbVJlY3QgPSBfZXh0ZW5kcyh7fSwgZHJhZ1JlY3QpO1xuICAgICAgICB2YXIgZHJhZ01hdHJpeCA9IG1hdHJpeChkcmFnRWwkMSwgdHJ1ZSk7XG4gICAgICAgIGNsb25lc0Zyb21SZWN0LnRvcCAtPSBkcmFnTWF0cml4LmY7XG4gICAgICAgIGNsb25lc0Zyb21SZWN0LmxlZnQgLT0gZHJhZ01hdHJpeC5lO1xuICAgICAgfVxuICAgIH0sXG4gICAgZHJhZ092ZXJBbmltYXRpb25Db21wbGV0ZTogZnVuY3Rpb24gZHJhZ092ZXJBbmltYXRpb25Db21wbGV0ZSgpIHtcbiAgICAgIGlmIChmb2xkaW5nKSB7XG4gICAgICAgIGZvbGRpbmcgPSBmYWxzZTtcbiAgICAgICAgcmVtb3ZlTXVsdGlEcmFnRWxlbWVudHMoKTtcbiAgICAgIH1cbiAgICB9LFxuICAgIGRyb3A6IGZ1bmN0aW9uIGRyb3AoX3JlZjEyKSB7XG4gICAgICB2YXIgZXZ0ID0gX3JlZjEyLm9yaWdpbmFsRXZlbnQsXG4gICAgICAgICAgcm9vdEVsID0gX3JlZjEyLnJvb3RFbCxcbiAgICAgICAgICBwYXJlbnRFbCA9IF9yZWYxMi5wYXJlbnRFbCxcbiAgICAgICAgICBzb3J0YWJsZSA9IF9yZWYxMi5zb3J0YWJsZSxcbiAgICAgICAgICBkaXNwYXRjaFNvcnRhYmxlRXZlbnQgPSBfcmVmMTIuZGlzcGF0Y2hTb3J0YWJsZUV2ZW50LFxuICAgICAgICAgIG9sZEluZGV4ID0gX3JlZjEyLm9sZEluZGV4LFxuICAgICAgICAgIHB1dFNvcnRhYmxlID0gX3JlZjEyLnB1dFNvcnRhYmxlO1xuICAgICAgdmFyIHRvU29ydGFibGUgPSBwdXRTb3J0YWJsZSB8fCB0aGlzLnNvcnRhYmxlO1xuICAgICAgaWYgKCFldnQpIHJldHVybjtcbiAgICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICAgIGNoaWxkcmVuID0gcGFyZW50RWwuY2hpbGRyZW47IC8vIE11bHRpLWRyYWcgc2VsZWN0aW9uXG5cbiAgICAgIGlmICghZHJhZ1N0YXJ0ZWQpIHtcbiAgICAgICAgaWYgKG9wdGlvbnMubXVsdGlEcmFnS2V5ICYmICF0aGlzLm11bHRpRHJhZ0tleURvd24pIHtcbiAgICAgICAgICB0aGlzLl9kZXNlbGVjdE11bHRpRHJhZygpO1xuICAgICAgICB9XG5cbiAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0VsJDEsIG9wdGlvbnMuc2VsZWN0ZWRDbGFzcywgIX5tdWx0aURyYWdFbGVtZW50cy5pbmRleE9mKGRyYWdFbCQxKSk7XG5cbiAgICAgICAgaWYgKCF+bXVsdGlEcmFnRWxlbWVudHMuaW5kZXhPZihkcmFnRWwkMSkpIHtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5wdXNoKGRyYWdFbCQxKTtcbiAgICAgICAgICBkaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgIHNvcnRhYmxlOiBzb3J0YWJsZSxcbiAgICAgICAgICAgIHJvb3RFbDogcm9vdEVsLFxuICAgICAgICAgICAgbmFtZTogJ3NlbGVjdCcsXG4gICAgICAgICAgICB0YXJnZXRFbDogZHJhZ0VsJDEsXG4gICAgICAgICAgICBvcmlnaW5hbEV2ZW50OiBldnRcbiAgICAgICAgICB9KTsgLy8gTW9kaWZpZXIgYWN0aXZhdGVkLCBzZWxlY3QgZnJvbSBsYXN0IHRvIGRyYWdFbFxuXG4gICAgICAgICAgaWYgKGV2dC5zaGlmdEtleSAmJiBsYXN0TXVsdGlEcmFnU2VsZWN0ICYmIHNvcnRhYmxlLmVsLmNvbnRhaW5zKGxhc3RNdWx0aURyYWdTZWxlY3QpKSB7XG4gICAgICAgICAgICB2YXIgbGFzdEluZGV4ID0gaW5kZXgobGFzdE11bHRpRHJhZ1NlbGVjdCksXG4gICAgICAgICAgICAgICAgY3VycmVudEluZGV4ID0gaW5kZXgoZHJhZ0VsJDEpO1xuXG4gICAgICAgICAgICBpZiAofmxhc3RJbmRleCAmJiB+Y3VycmVudEluZGV4ICYmIGxhc3RJbmRleCAhPT0gY3VycmVudEluZGV4KSB7XG4gICAgICAgICAgICAgIC8vIE11c3QgaW5jbHVkZSBsYXN0TXVsdGlEcmFnU2VsZWN0IChzZWxlY3QgaXQpLCBpbiBjYXNlIG1vZGlmaWVkIHNlbGVjdGlvbiBmcm9tIG5vIHNlbGVjdGlvblxuICAgICAgICAgICAgICAvLyAoYnV0IHByZXZpb3VzIHNlbGVjdGlvbiBleGlzdGVkKVxuICAgICAgICAgICAgICB2YXIgbiwgaTtcblxuICAgICAgICAgICAgICBpZiAoY3VycmVudEluZGV4ID4gbGFzdEluZGV4KSB7XG4gICAgICAgICAgICAgICAgaSA9IGxhc3RJbmRleDtcbiAgICAgICAgICAgICAgICBuID0gY3VycmVudEluZGV4O1xuICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGkgPSBjdXJyZW50SW5kZXg7XG4gICAgICAgICAgICAgICAgbiA9IGxhc3RJbmRleCArIDE7XG4gICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICBmb3IgKDsgaSA8IG47IGkrKykge1xuICAgICAgICAgICAgICAgIGlmICh+bXVsdGlEcmFnRWxlbWVudHMuaW5kZXhPZihjaGlsZHJlbltpXSkpIGNvbnRpbnVlO1xuICAgICAgICAgICAgICAgIHRvZ2dsZUNsYXNzKGNoaWxkcmVuW2ldLCBvcHRpb25zLnNlbGVjdGVkQ2xhc3MsIHRydWUpO1xuICAgICAgICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLnB1c2goY2hpbGRyZW5baV0pO1xuICAgICAgICAgICAgICAgIGRpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgICAgICAgc29ydGFibGU6IHNvcnRhYmxlLFxuICAgICAgICAgICAgICAgICAgcm9vdEVsOiByb290RWwsXG4gICAgICAgICAgICAgICAgICBuYW1lOiAnc2VsZWN0JyxcbiAgICAgICAgICAgICAgICAgIHRhcmdldEVsOiBjaGlsZHJlbltpXSxcbiAgICAgICAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGxhc3RNdWx0aURyYWdTZWxlY3QgPSBkcmFnRWwkMTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBtdWx0aURyYWdTb3J0YWJsZSA9IHRvU29ydGFibGU7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuc3BsaWNlKG11bHRpRHJhZ0VsZW1lbnRzLmluZGV4T2YoZHJhZ0VsJDEpLCAxKTtcbiAgICAgICAgICBsYXN0TXVsdGlEcmFnU2VsZWN0ID0gbnVsbDtcbiAgICAgICAgICBkaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgIHNvcnRhYmxlOiBzb3J0YWJsZSxcbiAgICAgICAgICAgIHJvb3RFbDogcm9vdEVsLFxuICAgICAgICAgICAgbmFtZTogJ2Rlc2VsZWN0JyxcbiAgICAgICAgICAgIHRhcmdldEVsOiBkcmFnRWwkMSxcbiAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICB9IC8vIE11bHRpLWRyYWcgZHJvcFxuXG5cbiAgICAgIGlmIChkcmFnU3RhcnRlZCAmJiB0aGlzLmlzTXVsdGlEcmFnKSB7XG4gICAgICAgIGZvbGRpbmcgPSBmYWxzZTsgLy8gRG8gbm90IFwidW5mb2xkXCIgYWZ0ZXIgYXJvdW5kIGRyYWdFbCBpZiByZXZlcnRlZFxuXG4gICAgICAgIGlmICgocGFyZW50RWxbZXhwYW5kb10ub3B0aW9ucy5zb3J0IHx8IHBhcmVudEVsICE9PSByb290RWwpICYmIG11bHRpRHJhZ0VsZW1lbnRzLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICB2YXIgZHJhZ1JlY3QgPSBnZXRSZWN0KGRyYWdFbCQxKSxcbiAgICAgICAgICAgICAgbXVsdGlEcmFnSW5kZXggPSBpbmRleChkcmFnRWwkMSwgJzpub3QoLicgKyB0aGlzLm9wdGlvbnMuc2VsZWN0ZWRDbGFzcyArICcpJyk7XG4gICAgICAgICAgaWYgKCFpbml0aWFsRm9sZGluZyAmJiBvcHRpb25zLmFuaW1hdGlvbikgZHJhZ0VsJDEudGhpc0FuaW1hdGlvbkR1cmF0aW9uID0gbnVsbDtcbiAgICAgICAgICB0b1NvcnRhYmxlLmNhcHR1cmVBbmltYXRpb25TdGF0ZSgpO1xuXG4gICAgICAgICAgaWYgKCFpbml0aWFsRm9sZGluZykge1xuICAgICAgICAgICAgaWYgKG9wdGlvbnMuYW5pbWF0aW9uKSB7XG4gICAgICAgICAgICAgIGRyYWdFbCQxLmZyb21SZWN0ID0gZHJhZ1JlY3Q7XG4gICAgICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgICAgICAgICBtdWx0aURyYWdFbGVtZW50LnRoaXNBbmltYXRpb25EdXJhdGlvbiA9IG51bGw7XG5cbiAgICAgICAgICAgICAgICBpZiAobXVsdGlEcmFnRWxlbWVudCAhPT0gZHJhZ0VsJDEpIHtcbiAgICAgICAgICAgICAgICAgIHZhciByZWN0ID0gZm9sZGluZyA/IGdldFJlY3QobXVsdGlEcmFnRWxlbWVudCkgOiBkcmFnUmVjdDtcbiAgICAgICAgICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnQuZnJvbVJlY3QgPSByZWN0OyAvLyBQcmVwYXJlIHVuZm9sZCBhbmltYXRpb25cblxuICAgICAgICAgICAgICAgICAgdG9Tb3J0YWJsZS5hZGRBbmltYXRpb25TdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIHRhcmdldDogbXVsdGlEcmFnRWxlbWVudCxcbiAgICAgICAgICAgICAgICAgICAgcmVjdDogcmVjdFxuICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0gLy8gTXVsdGkgZHJhZyBlbGVtZW50cyBhcmUgbm90IG5lY2Vzc2FyaWx5IHJlbW92ZWQgZnJvbSB0aGUgRE9NIG9uIGRyb3AsIHNvIHRvIHJlaW5zZXJ0XG4gICAgICAgICAgICAvLyBwcm9wZXJseSB0aGV5IG11c3QgYWxsIGJlIHJlbW92ZWRcblxuXG4gICAgICAgICAgICByZW1vdmVNdWx0aURyYWdFbGVtZW50cygpO1xuICAgICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICAgICAgICBpZiAoY2hpbGRyZW5bbXVsdGlEcmFnSW5kZXhdKSB7XG4gICAgICAgICAgICAgICAgcGFyZW50RWwuaW5zZXJ0QmVmb3JlKG11bHRpRHJhZ0VsZW1lbnQsIGNoaWxkcmVuW211bHRpRHJhZ0luZGV4XSk7XG4gICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcGFyZW50RWwuYXBwZW5kQ2hpbGQobXVsdGlEcmFnRWxlbWVudCk7XG4gICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICBtdWx0aURyYWdJbmRleCsrO1xuICAgICAgICAgICAgfSk7IC8vIElmIGluaXRpYWwgZm9sZGluZyBpcyBkb25lLCB0aGUgZWxlbWVudHMgbWF5IGhhdmUgY2hhbmdlZCBwb3NpdGlvbiBiZWNhdXNlIHRoZXkgYXJlIG5vd1xuICAgICAgICAgICAgLy8gdW5mb2xkaW5nIGFyb3VuZCBkcmFnRWwsIGV2ZW4gdGhvdWdoIGRyYWdFbCBtYXkgbm90IGhhdmUgaGlzIGluZGV4IGNoYW5nZWQsIHNvIHVwZGF0ZSBldmVudFxuICAgICAgICAgICAgLy8gbXVzdCBiZSBmaXJlZCBoZXJlIGFzIFNvcnRhYmxlIHdpbGwgbm90LlxuXG4gICAgICAgICAgICBpZiAob2xkSW5kZXggPT09IGluZGV4KGRyYWdFbCQxKSkge1xuICAgICAgICAgICAgICB2YXIgdXBkYXRlID0gZmFsc2U7XG4gICAgICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgICAgICAgICBpZiAobXVsdGlEcmFnRWxlbWVudC5zb3J0YWJsZUluZGV4ICE9PSBpbmRleChtdWx0aURyYWdFbGVtZW50KSkge1xuICAgICAgICAgICAgICAgICAgdXBkYXRlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgIGlmICh1cGRhdGUpIHtcbiAgICAgICAgICAgICAgICBkaXNwYXRjaFNvcnRhYmxlRXZlbnQoJ3VwZGF0ZScpO1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSAvLyBNdXN0IGJlIGRvbmUgYWZ0ZXIgY2FwdHVyaW5nIGluZGl2aWR1YWwgcmVjdHMgKHNjcm9sbCBiYXIpXG5cblxuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgICAgIHVuc2V0UmVjdChtdWx0aURyYWdFbGVtZW50KTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgICB0b1NvcnRhYmxlLmFuaW1hdGVBbGwoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIG11bHRpRHJhZ1NvcnRhYmxlID0gdG9Tb3J0YWJsZTtcbiAgICAgIH0gLy8gUmVtb3ZlIGNsb25lcyBpZiBuZWNlc3NhcnlcblxuXG4gICAgICBpZiAocm9vdEVsID09PSBwYXJlbnRFbCB8fCBwdXRTb3J0YWJsZSAmJiBwdXRTb3J0YWJsZS5sYXN0UHV0TW9kZSAhPT0gJ2Nsb25lJykge1xuICAgICAgICBtdWx0aURyYWdDbG9uZXMuZm9yRWFjaChmdW5jdGlvbiAoY2xvbmUpIHtcbiAgICAgICAgICBjbG9uZS5wYXJlbnROb2RlICYmIGNsb25lLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoY2xvbmUpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9LFxuICAgIG51bGxpbmdHbG9iYWw6IGZ1bmN0aW9uIG51bGxpbmdHbG9iYWwoKSB7XG4gICAgICB0aGlzLmlzTXVsdGlEcmFnID0gZHJhZ1N0YXJ0ZWQgPSBmYWxzZTtcbiAgICAgIG11bHRpRHJhZ0Nsb25lcy5sZW5ndGggPSAwO1xuICAgIH0sXG4gICAgZGVzdHJveUdsb2JhbDogZnVuY3Rpb24gZGVzdHJveUdsb2JhbCgpIHtcbiAgICAgIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKCk7XG5cbiAgICAgIG9mZihkb2N1bWVudCwgJ3BvaW50ZXJ1cCcsIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKTtcbiAgICAgIG9mZihkb2N1bWVudCwgJ21vdXNldXAnLCB0aGlzLl9kZXNlbGVjdE11bHRpRHJhZyk7XG4gICAgICBvZmYoZG9jdW1lbnQsICd0b3VjaGVuZCcsIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKTtcbiAgICAgIG9mZihkb2N1bWVudCwgJ2tleWRvd24nLCB0aGlzLl9jaGVja0tleURvd24pO1xuICAgICAgb2ZmKGRvY3VtZW50LCAna2V5dXAnLCB0aGlzLl9jaGVja0tleVVwKTtcbiAgICB9LFxuICAgIF9kZXNlbGVjdE11bHRpRHJhZzogZnVuY3Rpb24gX2Rlc2VsZWN0TXVsdGlEcmFnKGV2dCkge1xuICAgICAgaWYgKHR5cGVvZiBkcmFnU3RhcnRlZCAhPT0gXCJ1bmRlZmluZWRcIiAmJiBkcmFnU3RhcnRlZCkgcmV0dXJuOyAvLyBPbmx5IGRlc2VsZWN0IGlmIHNlbGVjdGlvbiBpcyBpbiB0aGlzIHNvcnRhYmxlXG5cbiAgICAgIGlmIChtdWx0aURyYWdTb3J0YWJsZSAhPT0gdGhpcy5zb3J0YWJsZSkgcmV0dXJuOyAvLyBPbmx5IGRlc2VsZWN0IGlmIHRhcmdldCBpcyBub3QgaXRlbSBpbiB0aGlzIHNvcnRhYmxlXG5cbiAgICAgIGlmIChldnQgJiYgY2xvc2VzdChldnQudGFyZ2V0LCB0aGlzLm9wdGlvbnMuZHJhZ2dhYmxlLCB0aGlzLnNvcnRhYmxlLmVsLCBmYWxzZSkpIHJldHVybjsgLy8gT25seSBkZXNlbGVjdCBpZiBsZWZ0IGNsaWNrXG5cbiAgICAgIGlmIChldnQgJiYgZXZ0LmJ1dHRvbiAhPT0gMCkgcmV0dXJuO1xuXG4gICAgICB3aGlsZSAobXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoKSB7XG4gICAgICAgIHZhciBlbCA9IG11bHRpRHJhZ0VsZW1lbnRzWzBdO1xuICAgICAgICB0b2dnbGVDbGFzcyhlbCwgdGhpcy5vcHRpb25zLnNlbGVjdGVkQ2xhc3MsIGZhbHNlKTtcbiAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuc2hpZnQoKTtcbiAgICAgICAgZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgc29ydGFibGU6IHRoaXMuc29ydGFibGUsXG4gICAgICAgICAgcm9vdEVsOiB0aGlzLnNvcnRhYmxlLmVsLFxuICAgICAgICAgIG5hbWU6ICdkZXNlbGVjdCcsXG4gICAgICAgICAgdGFyZ2V0RWw6IGVsLFxuICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9LFxuICAgIF9jaGVja0tleURvd246IGZ1bmN0aW9uIF9jaGVja0tleURvd24oZXZ0KSB7XG4gICAgICBpZiAoZXZ0LmtleSA9PT0gdGhpcy5vcHRpb25zLm11bHRpRHJhZ0tleSkge1xuICAgICAgICB0aGlzLm11bHRpRHJhZ0tleURvd24gPSB0cnVlO1xuICAgICAgfVxuICAgIH0sXG4gICAgX2NoZWNrS2V5VXA6IGZ1bmN0aW9uIF9jaGVja0tleVVwKGV2dCkge1xuICAgICAgaWYgKGV2dC5rZXkgPT09IHRoaXMub3B0aW9ucy5tdWx0aURyYWdLZXkpIHtcbiAgICAgICAgdGhpcy5tdWx0aURyYWdLZXlEb3duID0gZmFsc2U7XG4gICAgICB9XG4gICAgfVxuICB9O1xuICByZXR1cm4gX2V4dGVuZHMoTXVsdGlEcmFnLCB7XG4gICAgLy8gU3RhdGljIG1ldGhvZHMgJiBwcm9wZXJ0aWVzXG4gICAgcGx1Z2luTmFtZTogJ211bHRpRHJhZycsXG4gICAgdXRpbHM6IHtcbiAgICAgIC8qKlxyXG4gICAgICAgKiBTZWxlY3RzIHRoZSBwcm92aWRlZCBtdWx0aS1kcmFnIGl0ZW1cclxuICAgICAgICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IGVsICAgIFRoZSBlbGVtZW50IHRvIGJlIHNlbGVjdGVkXHJcbiAgICAgICAqL1xuICAgICAgc2VsZWN0OiBmdW5jdGlvbiBzZWxlY3QoZWwpIHtcbiAgICAgICAgdmFyIHNvcnRhYmxlID0gZWwucGFyZW50Tm9kZVtleHBhbmRvXTtcbiAgICAgICAgaWYgKCFzb3J0YWJsZSB8fCAhc29ydGFibGUub3B0aW9ucy5tdWx0aURyYWcgfHwgfm11bHRpRHJhZ0VsZW1lbnRzLmluZGV4T2YoZWwpKSByZXR1cm47XG5cbiAgICAgICAgaWYgKG11bHRpRHJhZ1NvcnRhYmxlICYmIG11bHRpRHJhZ1NvcnRhYmxlICE9PSBzb3J0YWJsZSkge1xuICAgICAgICAgIG11bHRpRHJhZ1NvcnRhYmxlLm11bHRpRHJhZy5fZGVzZWxlY3RNdWx0aURyYWcoKTtcblxuICAgICAgICAgIG11bHRpRHJhZ1NvcnRhYmxlID0gc29ydGFibGU7XG4gICAgICAgIH1cblxuICAgICAgICB0b2dnbGVDbGFzcyhlbCwgc29ydGFibGUub3B0aW9ucy5zZWxlY3RlZENsYXNzLCB0cnVlKTtcbiAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMucHVzaChlbCk7XG4gICAgICB9LFxuXG4gICAgICAvKipcclxuICAgICAgICogRGVzZWxlY3RzIHRoZSBwcm92aWRlZCBtdWx0aS1kcmFnIGl0ZW1cclxuICAgICAgICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IGVsICAgIFRoZSBlbGVtZW50IHRvIGJlIGRlc2VsZWN0ZWRcclxuICAgICAgICovXG4gICAgICBkZXNlbGVjdDogZnVuY3Rpb24gZGVzZWxlY3QoZWwpIHtcbiAgICAgICAgdmFyIHNvcnRhYmxlID0gZWwucGFyZW50Tm9kZVtleHBhbmRvXSxcbiAgICAgICAgICAgIGluZGV4ID0gbXVsdGlEcmFnRWxlbWVudHMuaW5kZXhPZihlbCk7XG4gICAgICAgIGlmICghc29ydGFibGUgfHwgIXNvcnRhYmxlLm9wdGlvbnMubXVsdGlEcmFnIHx8ICF+aW5kZXgpIHJldHVybjtcbiAgICAgICAgdG9nZ2xlQ2xhc3MoZWwsIHNvcnRhYmxlLm9wdGlvbnMuc2VsZWN0ZWRDbGFzcywgZmFsc2UpO1xuICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5zcGxpY2UoaW5kZXgsIDEpO1xuICAgICAgfVxuICAgIH0sXG4gICAgZXZlbnRQcm9wZXJ0aWVzOiBmdW5jdGlvbiBldmVudFByb3BlcnRpZXMoKSB7XG4gICAgICB2YXIgX3RoaXMzID0gdGhpcztcblxuICAgICAgdmFyIG9sZEluZGljaWVzID0gW10sXG4gICAgICAgICAgbmV3SW5kaWNpZXMgPSBbXTtcbiAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgb2xkSW5kaWNpZXMucHVzaCh7XG4gICAgICAgICAgbXVsdGlEcmFnRWxlbWVudDogbXVsdGlEcmFnRWxlbWVudCxcbiAgICAgICAgICBpbmRleDogbXVsdGlEcmFnRWxlbWVudC5zb3J0YWJsZUluZGV4XG4gICAgICAgIH0pOyAvLyBtdWx0aURyYWdFbGVtZW50cyB3aWxsIGFscmVhZHkgYmUgc29ydGVkIGlmIGZvbGRpbmdcblxuICAgICAgICB2YXIgbmV3SW5kZXg7XG5cbiAgICAgICAgaWYgKGZvbGRpbmcgJiYgbXVsdGlEcmFnRWxlbWVudCAhPT0gZHJhZ0VsJDEpIHtcbiAgICAgICAgICBuZXdJbmRleCA9IC0xO1xuICAgICAgICB9IGVsc2UgaWYgKGZvbGRpbmcpIHtcbiAgICAgICAgICBuZXdJbmRleCA9IGluZGV4KG11bHRpRHJhZ0VsZW1lbnQsICc6bm90KC4nICsgX3RoaXMzLm9wdGlvbnMuc2VsZWN0ZWRDbGFzcyArICcpJyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgbmV3SW5kZXggPSBpbmRleChtdWx0aURyYWdFbGVtZW50KTtcbiAgICAgICAgfVxuXG4gICAgICAgIG5ld0luZGljaWVzLnB1c2goe1xuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnQ6IG11bHRpRHJhZ0VsZW1lbnQsXG4gICAgICAgICAgaW5kZXg6IG5ld0luZGV4XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgICByZXR1cm4ge1xuICAgICAgICBpdGVtczogX3RvQ29uc3VtYWJsZUFycmF5KG11bHRpRHJhZ0VsZW1lbnRzKSxcbiAgICAgICAgY2xvbmVzOiBbXS5jb25jYXQobXVsdGlEcmFnQ2xvbmVzKSxcbiAgICAgICAgb2xkSW5kaWNpZXM6IG9sZEluZGljaWVzLFxuICAgICAgICBuZXdJbmRpY2llczogbmV3SW5kaWNpZXNcbiAgICAgIH07XG4gICAgfSxcbiAgICBvcHRpb25MaXN0ZW5lcnM6IHtcbiAgICAgIG11bHRpRHJhZ0tleTogZnVuY3Rpb24gbXVsdGlEcmFnS2V5KGtleSkge1xuICAgICAgICBrZXkgPSBrZXkudG9Mb3dlckNhc2UoKTtcblxuICAgICAgICBpZiAoa2V5ID09PSAnY3RybCcpIHtcbiAgICAgICAgICBrZXkgPSAnQ29udHJvbCc7XG4gICAgICAgIH0gZWxzZSBpZiAoa2V5Lmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICBrZXkgPSBrZXkuY2hhckF0KDApLnRvVXBwZXJDYXNlKCkgKyBrZXkuc3Vic3RyKDEpO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGtleTtcbiAgICAgIH1cbiAgICB9XG4gIH0pO1xufVxuXG5mdW5jdGlvbiBpbnNlcnRNdWx0aURyYWdFbGVtZW50cyhjbG9uZXNJbnNlcnRlZCwgcm9vdEVsKSB7XG4gIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQsIGkpIHtcbiAgICB2YXIgdGFyZ2V0ID0gcm9vdEVsLmNoaWxkcmVuW211bHRpRHJhZ0VsZW1lbnQuc29ydGFibGVJbmRleCArIChjbG9uZXNJbnNlcnRlZCA/IE51bWJlcihpKSA6IDApXTtcblxuICAgIGlmICh0YXJnZXQpIHtcbiAgICAgIHJvb3RFbC5pbnNlcnRCZWZvcmUobXVsdGlEcmFnRWxlbWVudCwgdGFyZ2V0KTtcbiAgICB9IGVsc2Uge1xuICAgICAgcm9vdEVsLmFwcGVuZENoaWxkKG11bHRpRHJhZ0VsZW1lbnQpO1xuICAgIH1cbiAgfSk7XG59XG4vKipcclxuICogSW5zZXJ0IG11bHRpLWRyYWcgY2xvbmVzXHJcbiAqIEBwYXJhbSAge1tCb29sZWFuXX0gZWxlbWVudHNJbnNlcnRlZCAgV2hldGhlciB0aGUgbXVsdGktZHJhZyBlbGVtZW50cyBhcmUgaW5zZXJ0ZWRcclxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IHJvb3RFbFxyXG4gKi9cblxuXG5mdW5jdGlvbiBpbnNlcnRNdWx0aURyYWdDbG9uZXMoZWxlbWVudHNJbnNlcnRlZCwgcm9vdEVsKSB7XG4gIG11bHRpRHJhZ0Nsb25lcy5mb3JFYWNoKGZ1bmN0aW9uIChjbG9uZSwgaSkge1xuICAgIHZhciB0YXJnZXQgPSByb290RWwuY2hpbGRyZW5bY2xvbmUuc29ydGFibGVJbmRleCArIChlbGVtZW50c0luc2VydGVkID8gTnVtYmVyKGkpIDogMCldO1xuXG4gICAgaWYgKHRhcmdldCkge1xuICAgICAgcm9vdEVsLmluc2VydEJlZm9yZShjbG9uZSwgdGFyZ2V0KTtcbiAgICB9IGVsc2Uge1xuICAgICAgcm9vdEVsLmFwcGVuZENoaWxkKGNsb25lKTtcbiAgICB9XG4gIH0pO1xufVxuXG5mdW5jdGlvbiByZW1vdmVNdWx0aURyYWdFbGVtZW50cygpIHtcbiAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgIGlmIChtdWx0aURyYWdFbGVtZW50ID09PSBkcmFnRWwkMSkgcmV0dXJuO1xuICAgIG11bHRpRHJhZ0VsZW1lbnQucGFyZW50Tm9kZSAmJiBtdWx0aURyYWdFbGVtZW50LnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQobXVsdGlEcmFnRWxlbWVudCk7XG4gIH0pO1xufVxuXG5Tb3J0YWJsZS5tb3VudChuZXcgQXV0b1Njcm9sbFBsdWdpbigpKTtcblNvcnRhYmxlLm1vdW50KFJlbW92ZSwgUmV2ZXJ0KTtcblxuZXhwb3J0IGRlZmF1bHQgU29ydGFibGU7XG5leHBvcnQgeyBNdWx0aURyYWdQbHVnaW4gYXMgTXVsdGlEcmFnLCBTb3J0YWJsZSwgU3dhcFBsdWdpbiBhcyBTd2FwIH07XG4iLCAiaW1wb3J0IFNvcnRhYmxlIGZyb20gJ3NvcnRhYmxlanMnXG5cbndpbmRvdy5Tb3J0YWJsZSA9IFNvcnRhYmxlXG5cbmV4cG9ydCBkZWZhdWx0IChBbHBpbmUpID0+IHtcbiAgICBBbHBpbmUuZGlyZWN0aXZlKCdzb3J0YWJsZScsIChlbCkgPT4ge1xuICAgICAgICBlbC5zb3J0YWJsZSA9IFNvcnRhYmxlLmNyZWF0ZShlbCwge1xuICAgICAgICAgICAgZHJhZ2dhYmxlOiAnW3gtc29ydGFibGUtaXRlbV0nLFxuICAgICAgICAgICAgaGFuZGxlOiAnW3gtc29ydGFibGUtaGFuZGxlXScsXG4gICAgICAgICAgICBkYXRhSWRBdHRyOiAneC1zb3J0YWJsZS1pdGVtJyxcbiAgICAgICAgfSlcbiAgICB9KVxufVxuIiwgInZhciBfX2NyZWF0ZSA9IE9iamVjdC5jcmVhdGU7XG52YXIgX19kZWZQcm9wID0gT2JqZWN0LmRlZmluZVByb3BlcnR5O1xudmFyIF9fZ2V0UHJvdG9PZiA9IE9iamVjdC5nZXRQcm90b3R5cGVPZjtcbnZhciBfX2hhc093blByb3AgPSBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5O1xudmFyIF9fZ2V0T3duUHJvcE5hbWVzID0gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXM7XG52YXIgX19nZXRPd25Qcm9wRGVzYyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3I7XG52YXIgX19tYXJrQXNNb2R1bGUgPSAodGFyZ2V0KSA9PiBfX2RlZlByb3AodGFyZ2V0LCBcIl9fZXNNb2R1bGVcIiwge3ZhbHVlOiB0cnVlfSk7XG52YXIgX19jb21tb25KUyA9IChjYWxsYmFjaywgbW9kdWxlKSA9PiAoKSA9PiB7XG4gIGlmICghbW9kdWxlKSB7XG4gICAgbW9kdWxlID0ge2V4cG9ydHM6IHt9fTtcbiAgICBjYWxsYmFjayhtb2R1bGUuZXhwb3J0cywgbW9kdWxlKTtcbiAgfVxuICByZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59O1xudmFyIF9fZXhwb3J0U3RhciA9ICh0YXJnZXQsIG1vZHVsZSwgZGVzYykgPT4ge1xuICBpZiAobW9kdWxlICYmIHR5cGVvZiBtb2R1bGUgPT09IFwib2JqZWN0XCIgfHwgdHlwZW9mIG1vZHVsZSA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgZm9yIChsZXQga2V5IG9mIF9fZ2V0T3duUHJvcE5hbWVzKG1vZHVsZSkpXG4gICAgICBpZiAoIV9faGFzT3duUHJvcC5jYWxsKHRhcmdldCwga2V5KSAmJiBrZXkgIT09IFwiZGVmYXVsdFwiKVxuICAgICAgICBfX2RlZlByb3AodGFyZ2V0LCBrZXksIHtnZXQ6ICgpID0+IG1vZHVsZVtrZXldLCBlbnVtZXJhYmxlOiAhKGRlc2MgPSBfX2dldE93blByb3BEZXNjKG1vZHVsZSwga2V5KSkgfHwgZGVzYy5lbnVtZXJhYmxlfSk7XG4gIH1cbiAgcmV0dXJuIHRhcmdldDtcbn07XG52YXIgX190b01vZHVsZSA9IChtb2R1bGUpID0+IHtcbiAgcmV0dXJuIF9fZXhwb3J0U3RhcihfX21hcmtBc01vZHVsZShfX2RlZlByb3AobW9kdWxlICE9IG51bGwgPyBfX2NyZWF0ZShfX2dldFByb3RvT2YobW9kdWxlKSkgOiB7fSwgXCJkZWZhdWx0XCIsIG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSAmJiBcImRlZmF1bHRcIiBpbiBtb2R1bGUgPyB7Z2V0OiAoKSA9PiBtb2R1bGUuZGVmYXVsdCwgZW51bWVyYWJsZTogdHJ1ZX0gOiB7dmFsdWU6IG1vZHVsZSwgZW51bWVyYWJsZTogdHJ1ZX0pKSwgbW9kdWxlKTtcbn07XG5cbi8vIG5vZGVfbW9kdWxlcy9AcG9wcGVyanMvY29yZS9kaXN0L2Nqcy9wb3BwZXIuanNcbnZhciByZXF1aXJlX3BvcHBlciA9IF9fY29tbW9uSlMoKGV4cG9ydHMpID0+IHtcbiAgXCJ1c2Ugc3RyaWN0XCI7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwge3ZhbHVlOiB0cnVlfSk7XG4gIGZ1bmN0aW9uIGdldEJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50KSB7XG4gICAgdmFyIHJlY3QgPSBlbGVtZW50LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgIHJldHVybiB7XG4gICAgICB3aWR0aDogcmVjdC53aWR0aCxcbiAgICAgIGhlaWdodDogcmVjdC5oZWlnaHQsXG4gICAgICB0b3A6IHJlY3QudG9wLFxuICAgICAgcmlnaHQ6IHJlY3QucmlnaHQsXG4gICAgICBib3R0b206IHJlY3QuYm90dG9tLFxuICAgICAgbGVmdDogcmVjdC5sZWZ0LFxuICAgICAgeDogcmVjdC5sZWZ0LFxuICAgICAgeTogcmVjdC50b3BcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIGdldFdpbmRvdyhub2RlKSB7XG4gICAgaWYgKG5vZGUgPT0gbnVsbCkge1xuICAgICAgcmV0dXJuIHdpbmRvdztcbiAgICB9XG4gICAgaWYgKG5vZGUudG9TdHJpbmcoKSAhPT0gXCJbb2JqZWN0IFdpbmRvd11cIikge1xuICAgICAgdmFyIG93bmVyRG9jdW1lbnQgPSBub2RlLm93bmVyRG9jdW1lbnQ7XG4gICAgICByZXR1cm4gb3duZXJEb2N1bWVudCA/IG93bmVyRG9jdW1lbnQuZGVmYXVsdFZpZXcgfHwgd2luZG93IDogd2luZG93O1xuICAgIH1cbiAgICByZXR1cm4gbm9kZTtcbiAgfVxuICBmdW5jdGlvbiBnZXRXaW5kb3dTY3JvbGwobm9kZSkge1xuICAgIHZhciB3aW4gPSBnZXRXaW5kb3cobm9kZSk7XG4gICAgdmFyIHNjcm9sbExlZnQgPSB3aW4ucGFnZVhPZmZzZXQ7XG4gICAgdmFyIHNjcm9sbFRvcCA9IHdpbi5wYWdlWU9mZnNldDtcbiAgICByZXR1cm4ge1xuICAgICAgc2Nyb2xsTGVmdCxcbiAgICAgIHNjcm9sbFRvcFxuICAgIH07XG4gIH1cbiAgZnVuY3Rpb24gaXNFbGVtZW50KG5vZGUpIHtcbiAgICB2YXIgT3duRWxlbWVudCA9IGdldFdpbmRvdyhub2RlKS5FbGVtZW50O1xuICAgIHJldHVybiBub2RlIGluc3RhbmNlb2YgT3duRWxlbWVudCB8fCBub2RlIGluc3RhbmNlb2YgRWxlbWVudDtcbiAgfVxuICBmdW5jdGlvbiBpc0hUTUxFbGVtZW50KG5vZGUpIHtcbiAgICB2YXIgT3duRWxlbWVudCA9IGdldFdpbmRvdyhub2RlKS5IVE1MRWxlbWVudDtcbiAgICByZXR1cm4gbm9kZSBpbnN0YW5jZW9mIE93bkVsZW1lbnQgfHwgbm9kZSBpbnN0YW5jZW9mIEhUTUxFbGVtZW50O1xuICB9XG4gIGZ1bmN0aW9uIGlzU2hhZG93Um9vdChub2RlKSB7XG4gICAgaWYgKHR5cGVvZiBTaGFkb3dSb290ID09PSBcInVuZGVmaW5lZFwiKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICAgIHZhciBPd25FbGVtZW50ID0gZ2V0V2luZG93KG5vZGUpLlNoYWRvd1Jvb3Q7XG4gICAgcmV0dXJuIG5vZGUgaW5zdGFuY2VvZiBPd25FbGVtZW50IHx8IG5vZGUgaW5zdGFuY2VvZiBTaGFkb3dSb290O1xuICB9XG4gIGZ1bmN0aW9uIGdldEhUTUxFbGVtZW50U2Nyb2xsKGVsZW1lbnQpIHtcbiAgICByZXR1cm4ge1xuICAgICAgc2Nyb2xsTGVmdDogZWxlbWVudC5zY3JvbGxMZWZ0LFxuICAgICAgc2Nyb2xsVG9wOiBlbGVtZW50LnNjcm9sbFRvcFxuICAgIH07XG4gIH1cbiAgZnVuY3Rpb24gZ2V0Tm9kZVNjcm9sbChub2RlKSB7XG4gICAgaWYgKG5vZGUgPT09IGdldFdpbmRvdyhub2RlKSB8fCAhaXNIVE1MRWxlbWVudChub2RlKSkge1xuICAgICAgcmV0dXJuIGdldFdpbmRvd1Njcm9sbChub2RlKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuIGdldEhUTUxFbGVtZW50U2Nyb2xsKG5vZGUpO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiBnZXROb2RlTmFtZShlbGVtZW50KSB7XG4gICAgcmV0dXJuIGVsZW1lbnQgPyAoZWxlbWVudC5ub2RlTmFtZSB8fCBcIlwiKS50b0xvd2VyQ2FzZSgpIDogbnVsbDtcbiAgfVxuICBmdW5jdGlvbiBnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCkge1xuICAgIHJldHVybiAoKGlzRWxlbWVudChlbGVtZW50KSA/IGVsZW1lbnQub3duZXJEb2N1bWVudCA6IGVsZW1lbnQuZG9jdW1lbnQpIHx8IHdpbmRvdy5kb2N1bWVudCkuZG9jdW1lbnRFbGVtZW50O1xuICB9XG4gIGZ1bmN0aW9uIGdldFdpbmRvd1Njcm9sbEJhclgoZWxlbWVudCkge1xuICAgIHJldHVybiBnZXRCb3VuZGluZ0NsaWVudFJlY3QoZ2V0RG9jdW1lbnRFbGVtZW50KGVsZW1lbnQpKS5sZWZ0ICsgZ2V0V2luZG93U2Nyb2xsKGVsZW1lbnQpLnNjcm9sbExlZnQ7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0Q29tcHV0ZWRTdHlsZShlbGVtZW50KSB7XG4gICAgcmV0dXJuIGdldFdpbmRvdyhlbGVtZW50KS5nZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpO1xuICB9XG4gIGZ1bmN0aW9uIGlzU2Nyb2xsUGFyZW50KGVsZW1lbnQpIHtcbiAgICB2YXIgX2dldENvbXB1dGVkU3R5bGUgPSBnZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpLCBvdmVyZmxvdyA9IF9nZXRDb21wdXRlZFN0eWxlLm92ZXJmbG93LCBvdmVyZmxvd1ggPSBfZ2V0Q29tcHV0ZWRTdHlsZS5vdmVyZmxvd1gsIG92ZXJmbG93WSA9IF9nZXRDb21wdXRlZFN0eWxlLm92ZXJmbG93WTtcbiAgICByZXR1cm4gL2F1dG98c2Nyb2xsfG92ZXJsYXl8aGlkZGVuLy50ZXN0KG92ZXJmbG93ICsgb3ZlcmZsb3dZICsgb3ZlcmZsb3dYKTtcbiAgfVxuICBmdW5jdGlvbiBnZXRDb21wb3NpdGVSZWN0KGVsZW1lbnRPclZpcnR1YWxFbGVtZW50LCBvZmZzZXRQYXJlbnQsIGlzRml4ZWQpIHtcbiAgICBpZiAoaXNGaXhlZCA9PT0gdm9pZCAwKSB7XG4gICAgICBpc0ZpeGVkID0gZmFsc2U7XG4gICAgfVxuICAgIHZhciBkb2N1bWVudEVsZW1lbnQgPSBnZXREb2N1bWVudEVsZW1lbnQob2Zmc2V0UGFyZW50KTtcbiAgICB2YXIgcmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50T3JWaXJ0dWFsRWxlbWVudCk7XG4gICAgdmFyIGlzT2Zmc2V0UGFyZW50QW5FbGVtZW50ID0gaXNIVE1MRWxlbWVudChvZmZzZXRQYXJlbnQpO1xuICAgIHZhciBzY3JvbGwgPSB7XG4gICAgICBzY3JvbGxMZWZ0OiAwLFxuICAgICAgc2Nyb2xsVG9wOiAwXG4gICAgfTtcbiAgICB2YXIgb2Zmc2V0cyA9IHtcbiAgICAgIHg6IDAsXG4gICAgICB5OiAwXG4gICAgfTtcbiAgICBpZiAoaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgfHwgIWlzT2Zmc2V0UGFyZW50QW5FbGVtZW50ICYmICFpc0ZpeGVkKSB7XG4gICAgICBpZiAoZ2V0Tm9kZU5hbWUob2Zmc2V0UGFyZW50KSAhPT0gXCJib2R5XCIgfHwgaXNTY3JvbGxQYXJlbnQoZG9jdW1lbnRFbGVtZW50KSkge1xuICAgICAgICBzY3JvbGwgPSBnZXROb2RlU2Nyb2xsKG9mZnNldFBhcmVudCk7XG4gICAgICB9XG4gICAgICBpZiAoaXNIVE1MRWxlbWVudChvZmZzZXRQYXJlbnQpKSB7XG4gICAgICAgIG9mZnNldHMgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3Qob2Zmc2V0UGFyZW50KTtcbiAgICAgICAgb2Zmc2V0cy54ICs9IG9mZnNldFBhcmVudC5jbGllbnRMZWZ0O1xuICAgICAgICBvZmZzZXRzLnkgKz0gb2Zmc2V0UGFyZW50LmNsaWVudFRvcDtcbiAgICAgIH0gZWxzZSBpZiAoZG9jdW1lbnRFbGVtZW50KSB7XG4gICAgICAgIG9mZnNldHMueCA9IGdldFdpbmRvd1Njcm9sbEJhclgoZG9jdW1lbnRFbGVtZW50KTtcbiAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIHtcbiAgICAgIHg6IHJlY3QubGVmdCArIHNjcm9sbC5zY3JvbGxMZWZ0IC0gb2Zmc2V0cy54LFxuICAgICAgeTogcmVjdC50b3AgKyBzY3JvbGwuc2Nyb2xsVG9wIC0gb2Zmc2V0cy55LFxuICAgICAgd2lkdGg6IHJlY3Qud2lkdGgsXG4gICAgICBoZWlnaHQ6IHJlY3QuaGVpZ2h0XG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBnZXRMYXlvdXRSZWN0KGVsZW1lbnQpIHtcbiAgICB2YXIgY2xpZW50UmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50KTtcbiAgICB2YXIgd2lkdGggPSBlbGVtZW50Lm9mZnNldFdpZHRoO1xuICAgIHZhciBoZWlnaHQgPSBlbGVtZW50Lm9mZnNldEhlaWdodDtcbiAgICBpZiAoTWF0aC5hYnMoY2xpZW50UmVjdC53aWR0aCAtIHdpZHRoKSA8PSAxKSB7XG4gICAgICB3aWR0aCA9IGNsaWVudFJlY3Qud2lkdGg7XG4gICAgfVxuICAgIGlmIChNYXRoLmFicyhjbGllbnRSZWN0LmhlaWdodCAtIGhlaWdodCkgPD0gMSkge1xuICAgICAgaGVpZ2h0ID0gY2xpZW50UmVjdC5oZWlnaHQ7XG4gICAgfVxuICAgIHJldHVybiB7XG4gICAgICB4OiBlbGVtZW50Lm9mZnNldExlZnQsXG4gICAgICB5OiBlbGVtZW50Lm9mZnNldFRvcCxcbiAgICAgIHdpZHRoLFxuICAgICAgaGVpZ2h0XG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBnZXRQYXJlbnROb2RlKGVsZW1lbnQpIHtcbiAgICBpZiAoZ2V0Tm9kZU5hbWUoZWxlbWVudCkgPT09IFwiaHRtbFwiKSB7XG4gICAgICByZXR1cm4gZWxlbWVudDtcbiAgICB9XG4gICAgcmV0dXJuIGVsZW1lbnQuYXNzaWduZWRTbG90IHx8IGVsZW1lbnQucGFyZW50Tm9kZSB8fCAoaXNTaGFkb3dSb290KGVsZW1lbnQpID8gZWxlbWVudC5ob3N0IDogbnVsbCkgfHwgZ2V0RG9jdW1lbnRFbGVtZW50KGVsZW1lbnQpO1xuICB9XG4gIGZ1bmN0aW9uIGdldFNjcm9sbFBhcmVudChub2RlKSB7XG4gICAgaWYgKFtcImh0bWxcIiwgXCJib2R5XCIsIFwiI2RvY3VtZW50XCJdLmluZGV4T2YoZ2V0Tm9kZU5hbWUobm9kZSkpID49IDApIHtcbiAgICAgIHJldHVybiBub2RlLm93bmVyRG9jdW1lbnQuYm9keTtcbiAgICB9XG4gICAgaWYgKGlzSFRNTEVsZW1lbnQobm9kZSkgJiYgaXNTY3JvbGxQYXJlbnQobm9kZSkpIHtcbiAgICAgIHJldHVybiBub2RlO1xuICAgIH1cbiAgICByZXR1cm4gZ2V0U2Nyb2xsUGFyZW50KGdldFBhcmVudE5vZGUobm9kZSkpO1xuICB9XG4gIGZ1bmN0aW9uIGxpc3RTY3JvbGxQYXJlbnRzKGVsZW1lbnQsIGxpc3QpIHtcbiAgICB2YXIgX2VsZW1lbnQkb3duZXJEb2N1bWVuO1xuICAgIGlmIChsaXN0ID09PSB2b2lkIDApIHtcbiAgICAgIGxpc3QgPSBbXTtcbiAgICB9XG4gICAgdmFyIHNjcm9sbFBhcmVudCA9IGdldFNjcm9sbFBhcmVudChlbGVtZW50KTtcbiAgICB2YXIgaXNCb2R5ID0gc2Nyb2xsUGFyZW50ID09PSAoKF9lbGVtZW50JG93bmVyRG9jdW1lbiA9IGVsZW1lbnQub3duZXJEb2N1bWVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9lbGVtZW50JG93bmVyRG9jdW1lbi5ib2R5KTtcbiAgICB2YXIgd2luID0gZ2V0V2luZG93KHNjcm9sbFBhcmVudCk7XG4gICAgdmFyIHRhcmdldCA9IGlzQm9keSA/IFt3aW5dLmNvbmNhdCh3aW4udmlzdWFsVmlld3BvcnQgfHwgW10sIGlzU2Nyb2xsUGFyZW50KHNjcm9sbFBhcmVudCkgPyBzY3JvbGxQYXJlbnQgOiBbXSkgOiBzY3JvbGxQYXJlbnQ7XG4gICAgdmFyIHVwZGF0ZWRMaXN0ID0gbGlzdC5jb25jYXQodGFyZ2V0KTtcbiAgICByZXR1cm4gaXNCb2R5ID8gdXBkYXRlZExpc3QgOiB1cGRhdGVkTGlzdC5jb25jYXQobGlzdFNjcm9sbFBhcmVudHMoZ2V0UGFyZW50Tm9kZSh0YXJnZXQpKSk7XG4gIH1cbiAgZnVuY3Rpb24gaXNUYWJsZUVsZW1lbnQoZWxlbWVudCkge1xuICAgIHJldHVybiBbXCJ0YWJsZVwiLCBcInRkXCIsIFwidGhcIl0uaW5kZXhPZihnZXROb2RlTmFtZShlbGVtZW50KSkgPj0gMDtcbiAgfVxuICBmdW5jdGlvbiBnZXRUcnVlT2Zmc2V0UGFyZW50KGVsZW1lbnQpIHtcbiAgICBpZiAoIWlzSFRNTEVsZW1lbnQoZWxlbWVudCkgfHwgZ2V0Q29tcHV0ZWRTdHlsZShlbGVtZW50KS5wb3NpdGlvbiA9PT0gXCJmaXhlZFwiKSB7XG4gICAgICByZXR1cm4gbnVsbDtcbiAgICB9XG4gICAgcmV0dXJuIGVsZW1lbnQub2Zmc2V0UGFyZW50O1xuICB9XG4gIGZ1bmN0aW9uIGdldENvbnRhaW5pbmdCbG9jayhlbGVtZW50KSB7XG4gICAgdmFyIGlzRmlyZWZveCA9IG5hdmlnYXRvci51c2VyQWdlbnQudG9Mb3dlckNhc2UoKS5pbmRleE9mKFwiZmlyZWZveFwiKSAhPT0gLTE7XG4gICAgdmFyIGlzSUUgPSBuYXZpZ2F0b3IudXNlckFnZW50LmluZGV4T2YoXCJUcmlkZW50XCIpICE9PSAtMTtcbiAgICBpZiAoaXNJRSAmJiBpc0hUTUxFbGVtZW50KGVsZW1lbnQpKSB7XG4gICAgICB2YXIgZWxlbWVudENzcyA9IGdldENvbXB1dGVkU3R5bGUoZWxlbWVudCk7XG4gICAgICBpZiAoZWxlbWVudENzcy5wb3NpdGlvbiA9PT0gXCJmaXhlZFwiKSB7XG4gICAgICAgIHJldHVybiBudWxsO1xuICAgICAgfVxuICAgIH1cbiAgICB2YXIgY3VycmVudE5vZGUgPSBnZXRQYXJlbnROb2RlKGVsZW1lbnQpO1xuICAgIHdoaWxlIChpc0hUTUxFbGVtZW50KGN1cnJlbnROb2RlKSAmJiBbXCJodG1sXCIsIFwiYm9keVwiXS5pbmRleE9mKGdldE5vZGVOYW1lKGN1cnJlbnROb2RlKSkgPCAwKSB7XG4gICAgICB2YXIgY3NzID0gZ2V0Q29tcHV0ZWRTdHlsZShjdXJyZW50Tm9kZSk7XG4gICAgICBpZiAoY3NzLnRyYW5zZm9ybSAhPT0gXCJub25lXCIgfHwgY3NzLnBlcnNwZWN0aXZlICE9PSBcIm5vbmVcIiB8fCBjc3MuY29udGFpbiA9PT0gXCJwYWludFwiIHx8IFtcInRyYW5zZm9ybVwiLCBcInBlcnNwZWN0aXZlXCJdLmluZGV4T2YoY3NzLndpbGxDaGFuZ2UpICE9PSAtMSB8fCBpc0ZpcmVmb3ggJiYgY3NzLndpbGxDaGFuZ2UgPT09IFwiZmlsdGVyXCIgfHwgaXNGaXJlZm94ICYmIGNzcy5maWx0ZXIgJiYgY3NzLmZpbHRlciAhPT0gXCJub25lXCIpIHtcbiAgICAgICAgcmV0dXJuIGN1cnJlbnROb2RlO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY3VycmVudE5vZGUgPSBjdXJyZW50Tm9kZS5wYXJlbnROb2RlO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gbnVsbDtcbiAgfVxuICBmdW5jdGlvbiBnZXRPZmZzZXRQYXJlbnQoZWxlbWVudCkge1xuICAgIHZhciB3aW5kb3cyID0gZ2V0V2luZG93KGVsZW1lbnQpO1xuICAgIHZhciBvZmZzZXRQYXJlbnQgPSBnZXRUcnVlT2Zmc2V0UGFyZW50KGVsZW1lbnQpO1xuICAgIHdoaWxlIChvZmZzZXRQYXJlbnQgJiYgaXNUYWJsZUVsZW1lbnQob2Zmc2V0UGFyZW50KSAmJiBnZXRDb21wdXRlZFN0eWxlKG9mZnNldFBhcmVudCkucG9zaXRpb24gPT09IFwic3RhdGljXCIpIHtcbiAgICAgIG9mZnNldFBhcmVudCA9IGdldFRydWVPZmZzZXRQYXJlbnQob2Zmc2V0UGFyZW50KTtcbiAgICB9XG4gICAgaWYgKG9mZnNldFBhcmVudCAmJiAoZ2V0Tm9kZU5hbWUob2Zmc2V0UGFyZW50KSA9PT0gXCJodG1sXCIgfHwgZ2V0Tm9kZU5hbWUob2Zmc2V0UGFyZW50KSA9PT0gXCJib2R5XCIgJiYgZ2V0Q29tcHV0ZWRTdHlsZShvZmZzZXRQYXJlbnQpLnBvc2l0aW9uID09PSBcInN0YXRpY1wiKSkge1xuICAgICAgcmV0dXJuIHdpbmRvdzI7XG4gICAgfVxuICAgIHJldHVybiBvZmZzZXRQYXJlbnQgfHwgZ2V0Q29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHx8IHdpbmRvdzI7XG4gIH1cbiAgdmFyIHRvcCA9IFwidG9wXCI7XG4gIHZhciBib3R0b20gPSBcImJvdHRvbVwiO1xuICB2YXIgcmlnaHQgPSBcInJpZ2h0XCI7XG4gIHZhciBsZWZ0ID0gXCJsZWZ0XCI7XG4gIHZhciBhdXRvID0gXCJhdXRvXCI7XG4gIHZhciBiYXNlUGxhY2VtZW50cyA9IFt0b3AsIGJvdHRvbSwgcmlnaHQsIGxlZnRdO1xuICB2YXIgc3RhcnQgPSBcInN0YXJ0XCI7XG4gIHZhciBlbmQgPSBcImVuZFwiO1xuICB2YXIgY2xpcHBpbmdQYXJlbnRzID0gXCJjbGlwcGluZ1BhcmVudHNcIjtcbiAgdmFyIHZpZXdwb3J0ID0gXCJ2aWV3cG9ydFwiO1xuICB2YXIgcG9wcGVyID0gXCJwb3BwZXJcIjtcbiAgdmFyIHJlZmVyZW5jZSA9IFwicmVmZXJlbmNlXCI7XG4gIHZhciB2YXJpYXRpb25QbGFjZW1lbnRzID0gLyogQF9fUFVSRV9fICovIGJhc2VQbGFjZW1lbnRzLnJlZHVjZShmdW5jdGlvbihhY2MsIHBsYWNlbWVudCkge1xuICAgIHJldHVybiBhY2MuY29uY2F0KFtwbGFjZW1lbnQgKyBcIi1cIiArIHN0YXJ0LCBwbGFjZW1lbnQgKyBcIi1cIiArIGVuZF0pO1xuICB9LCBbXSk7XG4gIHZhciBwbGFjZW1lbnRzID0gLyogQF9fUFVSRV9fICovIFtdLmNvbmNhdChiYXNlUGxhY2VtZW50cywgW2F1dG9dKS5yZWR1Y2UoZnVuY3Rpb24oYWNjLCBwbGFjZW1lbnQpIHtcbiAgICByZXR1cm4gYWNjLmNvbmNhdChbcGxhY2VtZW50LCBwbGFjZW1lbnQgKyBcIi1cIiArIHN0YXJ0LCBwbGFjZW1lbnQgKyBcIi1cIiArIGVuZF0pO1xuICB9LCBbXSk7XG4gIHZhciBiZWZvcmVSZWFkID0gXCJiZWZvcmVSZWFkXCI7XG4gIHZhciByZWFkID0gXCJyZWFkXCI7XG4gIHZhciBhZnRlclJlYWQgPSBcImFmdGVyUmVhZFwiO1xuICB2YXIgYmVmb3JlTWFpbiA9IFwiYmVmb3JlTWFpblwiO1xuICB2YXIgbWFpbiA9IFwibWFpblwiO1xuICB2YXIgYWZ0ZXJNYWluID0gXCJhZnRlck1haW5cIjtcbiAgdmFyIGJlZm9yZVdyaXRlID0gXCJiZWZvcmVXcml0ZVwiO1xuICB2YXIgd3JpdGUgPSBcIndyaXRlXCI7XG4gIHZhciBhZnRlcldyaXRlID0gXCJhZnRlcldyaXRlXCI7XG4gIHZhciBtb2RpZmllclBoYXNlcyA9IFtiZWZvcmVSZWFkLCByZWFkLCBhZnRlclJlYWQsIGJlZm9yZU1haW4sIG1haW4sIGFmdGVyTWFpbiwgYmVmb3JlV3JpdGUsIHdyaXRlLCBhZnRlcldyaXRlXTtcbiAgZnVuY3Rpb24gb3JkZXIobW9kaWZpZXJzKSB7XG4gICAgdmFyIG1hcCA9IG5ldyBNYXAoKTtcbiAgICB2YXIgdmlzaXRlZCA9IG5ldyBTZXQoKTtcbiAgICB2YXIgcmVzdWx0ID0gW107XG4gICAgbW9kaWZpZXJzLmZvckVhY2goZnVuY3Rpb24obW9kaWZpZXIpIHtcbiAgICAgIG1hcC5zZXQobW9kaWZpZXIubmFtZSwgbW9kaWZpZXIpO1xuICAgIH0pO1xuICAgIGZ1bmN0aW9uIHNvcnQobW9kaWZpZXIpIHtcbiAgICAgIHZpc2l0ZWQuYWRkKG1vZGlmaWVyLm5hbWUpO1xuICAgICAgdmFyIHJlcXVpcmVzID0gW10uY29uY2F0KG1vZGlmaWVyLnJlcXVpcmVzIHx8IFtdLCBtb2RpZmllci5yZXF1aXJlc0lmRXhpc3RzIHx8IFtdKTtcbiAgICAgIHJlcXVpcmVzLmZvckVhY2goZnVuY3Rpb24oZGVwKSB7XG4gICAgICAgIGlmICghdmlzaXRlZC5oYXMoZGVwKSkge1xuICAgICAgICAgIHZhciBkZXBNb2RpZmllciA9IG1hcC5nZXQoZGVwKTtcbiAgICAgICAgICBpZiAoZGVwTW9kaWZpZXIpIHtcbiAgICAgICAgICAgIHNvcnQoZGVwTW9kaWZpZXIpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgICByZXN1bHQucHVzaChtb2RpZmllcik7XG4gICAgfVxuICAgIG1vZGlmaWVycy5mb3JFYWNoKGZ1bmN0aW9uKG1vZGlmaWVyKSB7XG4gICAgICBpZiAoIXZpc2l0ZWQuaGFzKG1vZGlmaWVyLm5hbWUpKSB7XG4gICAgICAgIHNvcnQobW9kaWZpZXIpO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHJldHVybiByZXN1bHQ7XG4gIH1cbiAgZnVuY3Rpb24gb3JkZXJNb2RpZmllcnMobW9kaWZpZXJzKSB7XG4gICAgdmFyIG9yZGVyZWRNb2RpZmllcnMgPSBvcmRlcihtb2RpZmllcnMpO1xuICAgIHJldHVybiBtb2RpZmllclBoYXNlcy5yZWR1Y2UoZnVuY3Rpb24oYWNjLCBwaGFzZSkge1xuICAgICAgcmV0dXJuIGFjYy5jb25jYXQob3JkZXJlZE1vZGlmaWVycy5maWx0ZXIoZnVuY3Rpb24obW9kaWZpZXIpIHtcbiAgICAgICAgcmV0dXJuIG1vZGlmaWVyLnBoYXNlID09PSBwaGFzZTtcbiAgICAgIH0pKTtcbiAgICB9LCBbXSk7XG4gIH1cbiAgZnVuY3Rpb24gZGVib3VuY2UoZm4pIHtcbiAgICB2YXIgcGVuZGluZztcbiAgICByZXR1cm4gZnVuY3Rpb24oKSB7XG4gICAgICBpZiAoIXBlbmRpbmcpIHtcbiAgICAgICAgcGVuZGluZyA9IG5ldyBQcm9taXNlKGZ1bmN0aW9uKHJlc29sdmUpIHtcbiAgICAgICAgICBQcm9taXNlLnJlc29sdmUoKS50aGVuKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgcGVuZGluZyA9IHZvaWQgMDtcbiAgICAgICAgICAgIHJlc29sdmUoZm4oKSk7XG4gICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHBlbmRpbmc7XG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBmb3JtYXQoc3RyKSB7XG4gICAgZm9yICh2YXIgX2xlbiA9IGFyZ3VtZW50cy5sZW5ndGgsIGFyZ3MgPSBuZXcgQXJyYXkoX2xlbiA+IDEgPyBfbGVuIC0gMSA6IDApLCBfa2V5ID0gMTsgX2tleSA8IF9sZW47IF9rZXkrKykge1xuICAgICAgYXJnc1tfa2V5IC0gMV0gPSBhcmd1bWVudHNbX2tleV07XG4gICAgfVxuICAgIHJldHVybiBbXS5jb25jYXQoYXJncykucmVkdWNlKGZ1bmN0aW9uKHAsIGMpIHtcbiAgICAgIHJldHVybiBwLnJlcGxhY2UoLyVzLywgYyk7XG4gICAgfSwgc3RyKTtcbiAgfVxuICB2YXIgSU5WQUxJRF9NT0RJRklFUl9FUlJPUiA9ICdQb3BwZXI6IG1vZGlmaWVyIFwiJXNcIiBwcm92aWRlZCBhbiBpbnZhbGlkICVzIHByb3BlcnR5LCBleHBlY3RlZCAlcyBidXQgZ290ICVzJztcbiAgdmFyIE1JU1NJTkdfREVQRU5ERU5DWV9FUlJPUiA9ICdQb3BwZXI6IG1vZGlmaWVyIFwiJXNcIiByZXF1aXJlcyBcIiVzXCIsIGJ1dCBcIiVzXCIgbW9kaWZpZXIgaXMgbm90IGF2YWlsYWJsZSc7XG4gIHZhciBWQUxJRF9QUk9QRVJUSUVTID0gW1wibmFtZVwiLCBcImVuYWJsZWRcIiwgXCJwaGFzZVwiLCBcImZuXCIsIFwiZWZmZWN0XCIsIFwicmVxdWlyZXNcIiwgXCJvcHRpb25zXCJdO1xuICBmdW5jdGlvbiB2YWxpZGF0ZU1vZGlmaWVycyhtb2RpZmllcnMpIHtcbiAgICBtb2RpZmllcnMuZm9yRWFjaChmdW5jdGlvbihtb2RpZmllcikge1xuICAgICAgT2JqZWN0LmtleXMobW9kaWZpZXIpLmZvckVhY2goZnVuY3Rpb24oa2V5KSB7XG4gICAgICAgIHN3aXRjaCAoa2V5KSB7XG4gICAgICAgICAgY2FzZSBcIm5hbWVcIjpcbiAgICAgICAgICAgIGlmICh0eXBlb2YgbW9kaWZpZXIubmFtZSAhPT0gXCJzdHJpbmdcIikge1xuICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGZvcm1hdChJTlZBTElEX01PRElGSUVSX0VSUk9SLCBTdHJpbmcobW9kaWZpZXIubmFtZSksICdcIm5hbWVcIicsICdcInN0cmluZ1wiJywgJ1wiJyArIFN0cmluZyhtb2RpZmllci5uYW1lKSArICdcIicpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgXCJlbmFibGVkXCI6XG4gICAgICAgICAgICBpZiAodHlwZW9mIG1vZGlmaWVyLmVuYWJsZWQgIT09IFwiYm9vbGVhblwiKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KElOVkFMSURfTU9ESUZJRVJfRVJST1IsIG1vZGlmaWVyLm5hbWUsICdcImVuYWJsZWRcIicsICdcImJvb2xlYW5cIicsICdcIicgKyBTdHJpbmcobW9kaWZpZXIuZW5hYmxlZCkgKyAnXCInKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgY2FzZSBcInBoYXNlXCI6XG4gICAgICAgICAgICBpZiAobW9kaWZpZXJQaGFzZXMuaW5kZXhPZihtb2RpZmllci5waGFzZSkgPCAwKSB7XG4gICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KElOVkFMSURfTU9ESUZJRVJfRVJST1IsIG1vZGlmaWVyLm5hbWUsICdcInBoYXNlXCInLCBcImVpdGhlciBcIiArIG1vZGlmaWVyUGhhc2VzLmpvaW4oXCIsIFwiKSwgJ1wiJyArIFN0cmluZyhtb2RpZmllci5waGFzZSkgKyAnXCInKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICBjYXNlIFwiZm5cIjpcbiAgICAgICAgICAgIGlmICh0eXBlb2YgbW9kaWZpZXIuZm4gIT09IFwiZnVuY3Rpb25cIikge1xuICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGZvcm1hdChJTlZBTElEX01PRElGSUVSX0VSUk9SLCBtb2RpZmllci5uYW1lLCAnXCJmblwiJywgJ1wiZnVuY3Rpb25cIicsICdcIicgKyBTdHJpbmcobW9kaWZpZXIuZm4pICsgJ1wiJykpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgY2FzZSBcImVmZmVjdFwiOlxuICAgICAgICAgICAgaWYgKHR5cGVvZiBtb2RpZmllci5lZmZlY3QgIT09IFwiZnVuY3Rpb25cIikge1xuICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGZvcm1hdChJTlZBTElEX01PRElGSUVSX0VSUk9SLCBtb2RpZmllci5uYW1lLCAnXCJlZmZlY3RcIicsICdcImZ1bmN0aW9uXCInLCAnXCInICsgU3RyaW5nKG1vZGlmaWVyLmZuKSArICdcIicpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgXCJyZXF1aXJlc1wiOlxuICAgICAgICAgICAgaWYgKCFBcnJheS5pc0FycmF5KG1vZGlmaWVyLnJlcXVpcmVzKSkge1xuICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGZvcm1hdChJTlZBTElEX01PRElGSUVSX0VSUk9SLCBtb2RpZmllci5uYW1lLCAnXCJyZXF1aXJlc1wiJywgJ1wiYXJyYXlcIicsICdcIicgKyBTdHJpbmcobW9kaWZpZXIucmVxdWlyZXMpICsgJ1wiJykpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgY2FzZSBcInJlcXVpcmVzSWZFeGlzdHNcIjpcbiAgICAgICAgICAgIGlmICghQXJyYXkuaXNBcnJheShtb2RpZmllci5yZXF1aXJlc0lmRXhpc3RzKSkge1xuICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKGZvcm1hdChJTlZBTElEX01PRElGSUVSX0VSUk9SLCBtb2RpZmllci5uYW1lLCAnXCJyZXF1aXJlc0lmRXhpc3RzXCInLCAnXCJhcnJheVwiJywgJ1wiJyArIFN0cmluZyhtb2RpZmllci5yZXF1aXJlc0lmRXhpc3RzKSArICdcIicpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgXCJvcHRpb25zXCI6XG4gICAgICAgICAgY2FzZSBcImRhdGFcIjpcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGRlZmF1bHQ6XG4gICAgICAgICAgICBjb25zb2xlLmVycm9yKCdQb3BwZXJKUzogYW4gaW52YWxpZCBwcm9wZXJ0eSBoYXMgYmVlbiBwcm92aWRlZCB0byB0aGUgXCInICsgbW9kaWZpZXIubmFtZSArICdcIiBtb2RpZmllciwgdmFsaWQgcHJvcGVydGllcyBhcmUgJyArIFZBTElEX1BST1BFUlRJRVMubWFwKGZ1bmN0aW9uKHMpIHtcbiAgICAgICAgICAgICAgcmV0dXJuICdcIicgKyBzICsgJ1wiJztcbiAgICAgICAgICAgIH0pLmpvaW4oXCIsIFwiKSArICc7IGJ1dCBcIicgKyBrZXkgKyAnXCIgd2FzIHByb3ZpZGVkLicpO1xuICAgICAgICB9XG4gICAgICAgIG1vZGlmaWVyLnJlcXVpcmVzICYmIG1vZGlmaWVyLnJlcXVpcmVzLmZvckVhY2goZnVuY3Rpb24ocmVxdWlyZW1lbnQpIHtcbiAgICAgICAgICBpZiAobW9kaWZpZXJzLmZpbmQoZnVuY3Rpb24obW9kKSB7XG4gICAgICAgICAgICByZXR1cm4gbW9kLm5hbWUgPT09IHJlcXVpcmVtZW50O1xuICAgICAgICAgIH0pID09IG51bGwpIHtcbiAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoZm9ybWF0KE1JU1NJTkdfREVQRU5ERU5DWV9FUlJPUiwgU3RyaW5nKG1vZGlmaWVyLm5hbWUpLCByZXF1aXJlbWVudCwgcmVxdWlyZW1lbnQpKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gdW5pcXVlQnkoYXJyLCBmbikge1xuICAgIHZhciBpZGVudGlmaWVycyA9IG5ldyBTZXQoKTtcbiAgICByZXR1cm4gYXJyLmZpbHRlcihmdW5jdGlvbihpdGVtKSB7XG4gICAgICB2YXIgaWRlbnRpZmllciA9IGZuKGl0ZW0pO1xuICAgICAgaWYgKCFpZGVudGlmaWVycy5oYXMoaWRlbnRpZmllcikpIHtcbiAgICAgICAgaWRlbnRpZmllcnMuYWRkKGlkZW50aWZpZXIpO1xuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBnZXRCYXNlUGxhY2VtZW50KHBsYWNlbWVudCkge1xuICAgIHJldHVybiBwbGFjZW1lbnQuc3BsaXQoXCItXCIpWzBdO1xuICB9XG4gIGZ1bmN0aW9uIG1lcmdlQnlOYW1lKG1vZGlmaWVycykge1xuICAgIHZhciBtZXJnZWQgPSBtb2RpZmllcnMucmVkdWNlKGZ1bmN0aW9uKG1lcmdlZDIsIGN1cnJlbnQpIHtcbiAgICAgIHZhciBleGlzdGluZyA9IG1lcmdlZDJbY3VycmVudC5uYW1lXTtcbiAgICAgIG1lcmdlZDJbY3VycmVudC5uYW1lXSA9IGV4aXN0aW5nID8gT2JqZWN0LmFzc2lnbih7fSwgZXhpc3RpbmcsIGN1cnJlbnQsIHtcbiAgICAgICAgb3B0aW9uczogT2JqZWN0LmFzc2lnbih7fSwgZXhpc3Rpbmcub3B0aW9ucywgY3VycmVudC5vcHRpb25zKSxcbiAgICAgICAgZGF0YTogT2JqZWN0LmFzc2lnbih7fSwgZXhpc3RpbmcuZGF0YSwgY3VycmVudC5kYXRhKVxuICAgICAgfSkgOiBjdXJyZW50O1xuICAgICAgcmV0dXJuIG1lcmdlZDI7XG4gICAgfSwge30pO1xuICAgIHJldHVybiBPYmplY3Qua2V5cyhtZXJnZWQpLm1hcChmdW5jdGlvbihrZXkpIHtcbiAgICAgIHJldHVybiBtZXJnZWRba2V5XTtcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBnZXRWaWV3cG9ydFJlY3QoZWxlbWVudCkge1xuICAgIHZhciB3aW4gPSBnZXRXaW5kb3coZWxlbWVudCk7XG4gICAgdmFyIGh0bWwgPSBnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCk7XG4gICAgdmFyIHZpc3VhbFZpZXdwb3J0ID0gd2luLnZpc3VhbFZpZXdwb3J0O1xuICAgIHZhciB3aWR0aCA9IGh0bWwuY2xpZW50V2lkdGg7XG4gICAgdmFyIGhlaWdodCA9IGh0bWwuY2xpZW50SGVpZ2h0O1xuICAgIHZhciB4ID0gMDtcbiAgICB2YXIgeSA9IDA7XG4gICAgaWYgKHZpc3VhbFZpZXdwb3J0KSB7XG4gICAgICB3aWR0aCA9IHZpc3VhbFZpZXdwb3J0LndpZHRoO1xuICAgICAgaGVpZ2h0ID0gdmlzdWFsVmlld3BvcnQuaGVpZ2h0O1xuICAgICAgaWYgKCEvXigoPyFjaHJvbWV8YW5kcm9pZCkuKSpzYWZhcmkvaS50ZXN0KG5hdmlnYXRvci51c2VyQWdlbnQpKSB7XG4gICAgICAgIHggPSB2aXN1YWxWaWV3cG9ydC5vZmZzZXRMZWZ0O1xuICAgICAgICB5ID0gdmlzdWFsVmlld3BvcnQub2Zmc2V0VG9wO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4ge1xuICAgICAgd2lkdGgsXG4gICAgICBoZWlnaHQsXG4gICAgICB4OiB4ICsgZ2V0V2luZG93U2Nyb2xsQmFyWChlbGVtZW50KSxcbiAgICAgIHlcbiAgICB9O1xuICB9XG4gIHZhciBtYXggPSBNYXRoLm1heDtcbiAgdmFyIG1pbiA9IE1hdGgubWluO1xuICB2YXIgcm91bmQgPSBNYXRoLnJvdW5kO1xuICBmdW5jdGlvbiBnZXREb2N1bWVudFJlY3QoZWxlbWVudCkge1xuICAgIHZhciBfZWxlbWVudCRvd25lckRvY3VtZW47XG4gICAgdmFyIGh0bWwgPSBnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCk7XG4gICAgdmFyIHdpblNjcm9sbCA9IGdldFdpbmRvd1Njcm9sbChlbGVtZW50KTtcbiAgICB2YXIgYm9keSA9IChfZWxlbWVudCRvd25lckRvY3VtZW4gPSBlbGVtZW50Lm93bmVyRG9jdW1lbnQpID09IG51bGwgPyB2b2lkIDAgOiBfZWxlbWVudCRvd25lckRvY3VtZW4uYm9keTtcbiAgICB2YXIgd2lkdGggPSBtYXgoaHRtbC5zY3JvbGxXaWR0aCwgaHRtbC5jbGllbnRXaWR0aCwgYm9keSA/IGJvZHkuc2Nyb2xsV2lkdGggOiAwLCBib2R5ID8gYm9keS5jbGllbnRXaWR0aCA6IDApO1xuICAgIHZhciBoZWlnaHQgPSBtYXgoaHRtbC5zY3JvbGxIZWlnaHQsIGh0bWwuY2xpZW50SGVpZ2h0LCBib2R5ID8gYm9keS5zY3JvbGxIZWlnaHQgOiAwLCBib2R5ID8gYm9keS5jbGllbnRIZWlnaHQgOiAwKTtcbiAgICB2YXIgeCA9IC13aW5TY3JvbGwuc2Nyb2xsTGVmdCArIGdldFdpbmRvd1Njcm9sbEJhclgoZWxlbWVudCk7XG4gICAgdmFyIHkgPSAtd2luU2Nyb2xsLnNjcm9sbFRvcDtcbiAgICBpZiAoZ2V0Q29tcHV0ZWRTdHlsZShib2R5IHx8IGh0bWwpLmRpcmVjdGlvbiA9PT0gXCJydGxcIikge1xuICAgICAgeCArPSBtYXgoaHRtbC5jbGllbnRXaWR0aCwgYm9keSA/IGJvZHkuY2xpZW50V2lkdGggOiAwKSAtIHdpZHRoO1xuICAgIH1cbiAgICByZXR1cm4ge1xuICAgICAgd2lkdGgsXG4gICAgICBoZWlnaHQsXG4gICAgICB4LFxuICAgICAgeVxuICAgIH07XG4gIH1cbiAgZnVuY3Rpb24gY29udGFpbnMocGFyZW50LCBjaGlsZCkge1xuICAgIHZhciByb290Tm9kZSA9IGNoaWxkLmdldFJvb3ROb2RlICYmIGNoaWxkLmdldFJvb3ROb2RlKCk7XG4gICAgaWYgKHBhcmVudC5jb250YWlucyhjaGlsZCkpIHtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0gZWxzZSBpZiAocm9vdE5vZGUgJiYgaXNTaGFkb3dSb290KHJvb3ROb2RlKSkge1xuICAgICAgdmFyIG5leHQgPSBjaGlsZDtcbiAgICAgIGRvIHtcbiAgICAgICAgaWYgKG5leHQgJiYgcGFyZW50LmlzU2FtZU5vZGUobmV4dCkpIHtcbiAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgICBuZXh0ID0gbmV4dC5wYXJlbnROb2RlIHx8IG5leHQuaG9zdDtcbiAgICAgIH0gd2hpbGUgKG5leHQpO1xuICAgIH1cbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgZnVuY3Rpb24gcmVjdFRvQ2xpZW50UmVjdChyZWN0KSB7XG4gICAgcmV0dXJuIE9iamVjdC5hc3NpZ24oe30sIHJlY3QsIHtcbiAgICAgIGxlZnQ6IHJlY3QueCxcbiAgICAgIHRvcDogcmVjdC55LFxuICAgICAgcmlnaHQ6IHJlY3QueCArIHJlY3Qud2lkdGgsXG4gICAgICBib3R0b206IHJlY3QueSArIHJlY3QuaGVpZ2h0XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0SW5uZXJCb3VuZGluZ0NsaWVudFJlY3QoZWxlbWVudCkge1xuICAgIHZhciByZWN0ID0gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGVsZW1lbnQpO1xuICAgIHJlY3QudG9wID0gcmVjdC50b3AgKyBlbGVtZW50LmNsaWVudFRvcDtcbiAgICByZWN0LmxlZnQgPSByZWN0LmxlZnQgKyBlbGVtZW50LmNsaWVudExlZnQ7XG4gICAgcmVjdC5ib3R0b20gPSByZWN0LnRvcCArIGVsZW1lbnQuY2xpZW50SGVpZ2h0O1xuICAgIHJlY3QucmlnaHQgPSByZWN0LmxlZnQgKyBlbGVtZW50LmNsaWVudFdpZHRoO1xuICAgIHJlY3Qud2lkdGggPSBlbGVtZW50LmNsaWVudFdpZHRoO1xuICAgIHJlY3QuaGVpZ2h0ID0gZWxlbWVudC5jbGllbnRIZWlnaHQ7XG4gICAgcmVjdC54ID0gcmVjdC5sZWZ0O1xuICAgIHJlY3QueSA9IHJlY3QudG9wO1xuICAgIHJldHVybiByZWN0O1xuICB9XG4gIGZ1bmN0aW9uIGdldENsaWVudFJlY3RGcm9tTWl4ZWRUeXBlKGVsZW1lbnQsIGNsaXBwaW5nUGFyZW50KSB7XG4gICAgcmV0dXJuIGNsaXBwaW5nUGFyZW50ID09PSB2aWV3cG9ydCA/IHJlY3RUb0NsaWVudFJlY3QoZ2V0Vmlld3BvcnRSZWN0KGVsZW1lbnQpKSA6IGlzSFRNTEVsZW1lbnQoY2xpcHBpbmdQYXJlbnQpID8gZ2V0SW5uZXJCb3VuZGluZ0NsaWVudFJlY3QoY2xpcHBpbmdQYXJlbnQpIDogcmVjdFRvQ2xpZW50UmVjdChnZXREb2N1bWVudFJlY3QoZ2V0RG9jdW1lbnRFbGVtZW50KGVsZW1lbnQpKSk7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0Q2xpcHBpbmdQYXJlbnRzKGVsZW1lbnQpIHtcbiAgICB2YXIgY2xpcHBpbmdQYXJlbnRzMiA9IGxpc3RTY3JvbGxQYXJlbnRzKGdldFBhcmVudE5vZGUoZWxlbWVudCkpO1xuICAgIHZhciBjYW5Fc2NhcGVDbGlwcGluZyA9IFtcImFic29sdXRlXCIsIFwiZml4ZWRcIl0uaW5kZXhPZihnZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpLnBvc2l0aW9uKSA+PSAwO1xuICAgIHZhciBjbGlwcGVyRWxlbWVudCA9IGNhbkVzY2FwZUNsaXBwaW5nICYmIGlzSFRNTEVsZW1lbnQoZWxlbWVudCkgPyBnZXRPZmZzZXRQYXJlbnQoZWxlbWVudCkgOiBlbGVtZW50O1xuICAgIGlmICghaXNFbGVtZW50KGNsaXBwZXJFbGVtZW50KSkge1xuICAgICAgcmV0dXJuIFtdO1xuICAgIH1cbiAgICByZXR1cm4gY2xpcHBpbmdQYXJlbnRzMi5maWx0ZXIoZnVuY3Rpb24oY2xpcHBpbmdQYXJlbnQpIHtcbiAgICAgIHJldHVybiBpc0VsZW1lbnQoY2xpcHBpbmdQYXJlbnQpICYmIGNvbnRhaW5zKGNsaXBwaW5nUGFyZW50LCBjbGlwcGVyRWxlbWVudCkgJiYgZ2V0Tm9kZU5hbWUoY2xpcHBpbmdQYXJlbnQpICE9PSBcImJvZHlcIjtcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBnZXRDbGlwcGluZ1JlY3QoZWxlbWVudCwgYm91bmRhcnksIHJvb3RCb3VuZGFyeSkge1xuICAgIHZhciBtYWluQ2xpcHBpbmdQYXJlbnRzID0gYm91bmRhcnkgPT09IFwiY2xpcHBpbmdQYXJlbnRzXCIgPyBnZXRDbGlwcGluZ1BhcmVudHMoZWxlbWVudCkgOiBbXS5jb25jYXQoYm91bmRhcnkpO1xuICAgIHZhciBjbGlwcGluZ1BhcmVudHMyID0gW10uY29uY2F0KG1haW5DbGlwcGluZ1BhcmVudHMsIFtyb290Qm91bmRhcnldKTtcbiAgICB2YXIgZmlyc3RDbGlwcGluZ1BhcmVudCA9IGNsaXBwaW5nUGFyZW50czJbMF07XG4gICAgdmFyIGNsaXBwaW5nUmVjdCA9IGNsaXBwaW5nUGFyZW50czIucmVkdWNlKGZ1bmN0aW9uKGFjY1JlY3QsIGNsaXBwaW5nUGFyZW50KSB7XG4gICAgICB2YXIgcmVjdCA9IGdldENsaWVudFJlY3RGcm9tTWl4ZWRUeXBlKGVsZW1lbnQsIGNsaXBwaW5nUGFyZW50KTtcbiAgICAgIGFjY1JlY3QudG9wID0gbWF4KHJlY3QudG9wLCBhY2NSZWN0LnRvcCk7XG4gICAgICBhY2NSZWN0LnJpZ2h0ID0gbWluKHJlY3QucmlnaHQsIGFjY1JlY3QucmlnaHQpO1xuICAgICAgYWNjUmVjdC5ib3R0b20gPSBtaW4ocmVjdC5ib3R0b20sIGFjY1JlY3QuYm90dG9tKTtcbiAgICAgIGFjY1JlY3QubGVmdCA9IG1heChyZWN0LmxlZnQsIGFjY1JlY3QubGVmdCk7XG4gICAgICByZXR1cm4gYWNjUmVjdDtcbiAgICB9LCBnZXRDbGllbnRSZWN0RnJvbU1peGVkVHlwZShlbGVtZW50LCBmaXJzdENsaXBwaW5nUGFyZW50KSk7XG4gICAgY2xpcHBpbmdSZWN0LndpZHRoID0gY2xpcHBpbmdSZWN0LnJpZ2h0IC0gY2xpcHBpbmdSZWN0LmxlZnQ7XG4gICAgY2xpcHBpbmdSZWN0LmhlaWdodCA9IGNsaXBwaW5nUmVjdC5ib3R0b20gLSBjbGlwcGluZ1JlY3QudG9wO1xuICAgIGNsaXBwaW5nUmVjdC54ID0gY2xpcHBpbmdSZWN0LmxlZnQ7XG4gICAgY2xpcHBpbmdSZWN0LnkgPSBjbGlwcGluZ1JlY3QudG9wO1xuICAgIHJldHVybiBjbGlwcGluZ1JlY3Q7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0VmFyaWF0aW9uKHBsYWNlbWVudCkge1xuICAgIHJldHVybiBwbGFjZW1lbnQuc3BsaXQoXCItXCIpWzFdO1xuICB9XG4gIGZ1bmN0aW9uIGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpIHtcbiAgICByZXR1cm4gW1widG9wXCIsIFwiYm90dG9tXCJdLmluZGV4T2YocGxhY2VtZW50KSA+PSAwID8gXCJ4XCIgOiBcInlcIjtcbiAgfVxuICBmdW5jdGlvbiBjb21wdXRlT2Zmc2V0cyhfcmVmKSB7XG4gICAgdmFyIHJlZmVyZW5jZTIgPSBfcmVmLnJlZmVyZW5jZSwgZWxlbWVudCA9IF9yZWYuZWxlbWVudCwgcGxhY2VtZW50ID0gX3JlZi5wbGFjZW1lbnQ7XG4gICAgdmFyIGJhc2VQbGFjZW1lbnQgPSBwbGFjZW1lbnQgPyBnZXRCYXNlUGxhY2VtZW50KHBsYWNlbWVudCkgOiBudWxsO1xuICAgIHZhciB2YXJpYXRpb24gPSBwbGFjZW1lbnQgPyBnZXRWYXJpYXRpb24ocGxhY2VtZW50KSA6IG51bGw7XG4gICAgdmFyIGNvbW1vblggPSByZWZlcmVuY2UyLnggKyByZWZlcmVuY2UyLndpZHRoIC8gMiAtIGVsZW1lbnQud2lkdGggLyAyO1xuICAgIHZhciBjb21tb25ZID0gcmVmZXJlbmNlMi55ICsgcmVmZXJlbmNlMi5oZWlnaHQgLyAyIC0gZWxlbWVudC5oZWlnaHQgLyAyO1xuICAgIHZhciBvZmZzZXRzO1xuICAgIHN3aXRjaCAoYmFzZVBsYWNlbWVudCkge1xuICAgICAgY2FzZSB0b3A6XG4gICAgICAgIG9mZnNldHMgPSB7XG4gICAgICAgICAgeDogY29tbW9uWCxcbiAgICAgICAgICB5OiByZWZlcmVuY2UyLnkgLSBlbGVtZW50LmhlaWdodFxuICAgICAgICB9O1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgYm90dG9tOlxuICAgICAgICBvZmZzZXRzID0ge1xuICAgICAgICAgIHg6IGNvbW1vblgsXG4gICAgICAgICAgeTogcmVmZXJlbmNlMi55ICsgcmVmZXJlbmNlMi5oZWlnaHRcbiAgICAgICAgfTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBjYXNlIHJpZ2h0OlxuICAgICAgICBvZmZzZXRzID0ge1xuICAgICAgICAgIHg6IHJlZmVyZW5jZTIueCArIHJlZmVyZW5jZTIud2lkdGgsXG4gICAgICAgICAgeTogY29tbW9uWVxuICAgICAgICB9O1xuICAgICAgICBicmVhaztcbiAgICAgIGNhc2UgbGVmdDpcbiAgICAgICAgb2Zmc2V0cyA9IHtcbiAgICAgICAgICB4OiByZWZlcmVuY2UyLnggLSBlbGVtZW50LndpZHRoLFxuICAgICAgICAgIHk6IGNvbW1vbllcbiAgICAgICAgfTtcbiAgICAgICAgYnJlYWs7XG4gICAgICBkZWZhdWx0OlxuICAgICAgICBvZmZzZXRzID0ge1xuICAgICAgICAgIHg6IHJlZmVyZW5jZTIueCxcbiAgICAgICAgICB5OiByZWZlcmVuY2UyLnlcbiAgICAgICAgfTtcbiAgICB9XG4gICAgdmFyIG1haW5BeGlzID0gYmFzZVBsYWNlbWVudCA/IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChiYXNlUGxhY2VtZW50KSA6IG51bGw7XG4gICAgaWYgKG1haW5BeGlzICE9IG51bGwpIHtcbiAgICAgIHZhciBsZW4gPSBtYWluQXhpcyA9PT0gXCJ5XCIgPyBcImhlaWdodFwiIDogXCJ3aWR0aFwiO1xuICAgICAgc3dpdGNoICh2YXJpYXRpb24pIHtcbiAgICAgICAgY2FzZSBzdGFydDpcbiAgICAgICAgICBvZmZzZXRzW21haW5BeGlzXSA9IG9mZnNldHNbbWFpbkF4aXNdIC0gKHJlZmVyZW5jZTJbbGVuXSAvIDIgLSBlbGVtZW50W2xlbl0gLyAyKTtcbiAgICAgICAgICBicmVhaztcbiAgICAgICAgY2FzZSBlbmQ6XG4gICAgICAgICAgb2Zmc2V0c1ttYWluQXhpc10gPSBvZmZzZXRzW21haW5BeGlzXSArIChyZWZlcmVuY2UyW2xlbl0gLyAyIC0gZWxlbWVudFtsZW5dIC8gMik7XG4gICAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuICAgIHJldHVybiBvZmZzZXRzO1xuICB9XG4gIGZ1bmN0aW9uIGdldEZyZXNoU2lkZU9iamVjdCgpIHtcbiAgICByZXR1cm4ge1xuICAgICAgdG9wOiAwLFxuICAgICAgcmlnaHQ6IDAsXG4gICAgICBib3R0b206IDAsXG4gICAgICBsZWZ0OiAwXG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBtZXJnZVBhZGRpbmdPYmplY3QocGFkZGluZ09iamVjdCkge1xuICAgIHJldHVybiBPYmplY3QuYXNzaWduKHt9LCBnZXRGcmVzaFNpZGVPYmplY3QoKSwgcGFkZGluZ09iamVjdCk7XG4gIH1cbiAgZnVuY3Rpb24gZXhwYW5kVG9IYXNoTWFwKHZhbHVlLCBrZXlzKSB7XG4gICAgcmV0dXJuIGtleXMucmVkdWNlKGZ1bmN0aW9uKGhhc2hNYXAsIGtleSkge1xuICAgICAgaGFzaE1hcFtrZXldID0gdmFsdWU7XG4gICAgICByZXR1cm4gaGFzaE1hcDtcbiAgICB9LCB7fSk7XG4gIH1cbiAgZnVuY3Rpb24gZGV0ZWN0T3ZlcmZsb3coc3RhdGUsIG9wdGlvbnMpIHtcbiAgICBpZiAob3B0aW9ucyA9PT0gdm9pZCAwKSB7XG4gICAgICBvcHRpb25zID0ge307XG4gICAgfVxuICAgIHZhciBfb3B0aW9ucyA9IG9wdGlvbnMsIF9vcHRpb25zJHBsYWNlbWVudCA9IF9vcHRpb25zLnBsYWNlbWVudCwgcGxhY2VtZW50ID0gX29wdGlvbnMkcGxhY2VtZW50ID09PSB2b2lkIDAgPyBzdGF0ZS5wbGFjZW1lbnQgOiBfb3B0aW9ucyRwbGFjZW1lbnQsIF9vcHRpb25zJGJvdW5kYXJ5ID0gX29wdGlvbnMuYm91bmRhcnksIGJvdW5kYXJ5ID0gX29wdGlvbnMkYm91bmRhcnkgPT09IHZvaWQgMCA/IGNsaXBwaW5nUGFyZW50cyA6IF9vcHRpb25zJGJvdW5kYXJ5LCBfb3B0aW9ucyRyb290Qm91bmRhcnkgPSBfb3B0aW9ucy5yb290Qm91bmRhcnksIHJvb3RCb3VuZGFyeSA9IF9vcHRpb25zJHJvb3RCb3VuZGFyeSA9PT0gdm9pZCAwID8gdmlld3BvcnQgOiBfb3B0aW9ucyRyb290Qm91bmRhcnksIF9vcHRpb25zJGVsZW1lbnRDb250ZSA9IF9vcHRpb25zLmVsZW1lbnRDb250ZXh0LCBlbGVtZW50Q29udGV4dCA9IF9vcHRpb25zJGVsZW1lbnRDb250ZSA9PT0gdm9pZCAwID8gcG9wcGVyIDogX29wdGlvbnMkZWxlbWVudENvbnRlLCBfb3B0aW9ucyRhbHRCb3VuZGFyeSA9IF9vcHRpb25zLmFsdEJvdW5kYXJ5LCBhbHRCb3VuZGFyeSA9IF9vcHRpb25zJGFsdEJvdW5kYXJ5ID09PSB2b2lkIDAgPyBmYWxzZSA6IF9vcHRpb25zJGFsdEJvdW5kYXJ5LCBfb3B0aW9ucyRwYWRkaW5nID0gX29wdGlvbnMucGFkZGluZywgcGFkZGluZyA9IF9vcHRpb25zJHBhZGRpbmcgPT09IHZvaWQgMCA/IDAgOiBfb3B0aW9ucyRwYWRkaW5nO1xuICAgIHZhciBwYWRkaW5nT2JqZWN0ID0gbWVyZ2VQYWRkaW5nT2JqZWN0KHR5cGVvZiBwYWRkaW5nICE9PSBcIm51bWJlclwiID8gcGFkZGluZyA6IGV4cGFuZFRvSGFzaE1hcChwYWRkaW5nLCBiYXNlUGxhY2VtZW50cykpO1xuICAgIHZhciBhbHRDb250ZXh0ID0gZWxlbWVudENvbnRleHQgPT09IHBvcHBlciA/IHJlZmVyZW5jZSA6IHBvcHBlcjtcbiAgICB2YXIgcmVmZXJlbmNlRWxlbWVudCA9IHN0YXRlLmVsZW1lbnRzLnJlZmVyZW5jZTtcbiAgICB2YXIgcG9wcGVyUmVjdCA9IHN0YXRlLnJlY3RzLnBvcHBlcjtcbiAgICB2YXIgZWxlbWVudCA9IHN0YXRlLmVsZW1lbnRzW2FsdEJvdW5kYXJ5ID8gYWx0Q29udGV4dCA6IGVsZW1lbnRDb250ZXh0XTtcbiAgICB2YXIgY2xpcHBpbmdDbGllbnRSZWN0ID0gZ2V0Q2xpcHBpbmdSZWN0KGlzRWxlbWVudChlbGVtZW50KSA/IGVsZW1lbnQgOiBlbGVtZW50LmNvbnRleHRFbGVtZW50IHx8IGdldERvY3VtZW50RWxlbWVudChzdGF0ZS5lbGVtZW50cy5wb3BwZXIpLCBib3VuZGFyeSwgcm9vdEJvdW5kYXJ5KTtcbiAgICB2YXIgcmVmZXJlbmNlQ2xpZW50UmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChyZWZlcmVuY2VFbGVtZW50KTtcbiAgICB2YXIgcG9wcGVyT2Zmc2V0czIgPSBjb21wdXRlT2Zmc2V0cyh7XG4gICAgICByZWZlcmVuY2U6IHJlZmVyZW5jZUNsaWVudFJlY3QsXG4gICAgICBlbGVtZW50OiBwb3BwZXJSZWN0LFxuICAgICAgc3RyYXRlZ3k6IFwiYWJzb2x1dGVcIixcbiAgICAgIHBsYWNlbWVudFxuICAgIH0pO1xuICAgIHZhciBwb3BwZXJDbGllbnRSZWN0ID0gcmVjdFRvQ2xpZW50UmVjdChPYmplY3QuYXNzaWduKHt9LCBwb3BwZXJSZWN0LCBwb3BwZXJPZmZzZXRzMikpO1xuICAgIHZhciBlbGVtZW50Q2xpZW50UmVjdCA9IGVsZW1lbnRDb250ZXh0ID09PSBwb3BwZXIgPyBwb3BwZXJDbGllbnRSZWN0IDogcmVmZXJlbmNlQ2xpZW50UmVjdDtcbiAgICB2YXIgb3ZlcmZsb3dPZmZzZXRzID0ge1xuICAgICAgdG9wOiBjbGlwcGluZ0NsaWVudFJlY3QudG9wIC0gZWxlbWVudENsaWVudFJlY3QudG9wICsgcGFkZGluZ09iamVjdC50b3AsXG4gICAgICBib3R0b206IGVsZW1lbnRDbGllbnRSZWN0LmJvdHRvbSAtIGNsaXBwaW5nQ2xpZW50UmVjdC5ib3R0b20gKyBwYWRkaW5nT2JqZWN0LmJvdHRvbSxcbiAgICAgIGxlZnQ6IGNsaXBwaW5nQ2xpZW50UmVjdC5sZWZ0IC0gZWxlbWVudENsaWVudFJlY3QubGVmdCArIHBhZGRpbmdPYmplY3QubGVmdCxcbiAgICAgIHJpZ2h0OiBlbGVtZW50Q2xpZW50UmVjdC5yaWdodCAtIGNsaXBwaW5nQ2xpZW50UmVjdC5yaWdodCArIHBhZGRpbmdPYmplY3QucmlnaHRcbiAgICB9O1xuICAgIHZhciBvZmZzZXREYXRhID0gc3RhdGUubW9kaWZpZXJzRGF0YS5vZmZzZXQ7XG4gICAgaWYgKGVsZW1lbnRDb250ZXh0ID09PSBwb3BwZXIgJiYgb2Zmc2V0RGF0YSkge1xuICAgICAgdmFyIG9mZnNldDIgPSBvZmZzZXREYXRhW3BsYWNlbWVudF07XG4gICAgICBPYmplY3Qua2V5cyhvdmVyZmxvd09mZnNldHMpLmZvckVhY2goZnVuY3Rpb24oa2V5KSB7XG4gICAgICAgIHZhciBtdWx0aXBseSA9IFtyaWdodCwgYm90dG9tXS5pbmRleE9mKGtleSkgPj0gMCA/IDEgOiAtMTtcbiAgICAgICAgdmFyIGF4aXMgPSBbdG9wLCBib3R0b21dLmluZGV4T2Yoa2V5KSA+PSAwID8gXCJ5XCIgOiBcInhcIjtcbiAgICAgICAgb3ZlcmZsb3dPZmZzZXRzW2tleV0gKz0gb2Zmc2V0MltheGlzXSAqIG11bHRpcGx5O1xuICAgICAgfSk7XG4gICAgfVxuICAgIHJldHVybiBvdmVyZmxvd09mZnNldHM7XG4gIH1cbiAgdmFyIElOVkFMSURfRUxFTUVOVF9FUlJPUiA9IFwiUG9wcGVyOiBJbnZhbGlkIHJlZmVyZW5jZSBvciBwb3BwZXIgYXJndW1lbnQgcHJvdmlkZWQuIFRoZXkgbXVzdCBiZSBlaXRoZXIgYSBET00gZWxlbWVudCBvciB2aXJ0dWFsIGVsZW1lbnQuXCI7XG4gIHZhciBJTkZJTklURV9MT09QX0VSUk9SID0gXCJQb3BwZXI6IEFuIGluZmluaXRlIGxvb3AgaW4gdGhlIG1vZGlmaWVycyBjeWNsZSBoYXMgYmVlbiBkZXRlY3RlZCEgVGhlIGN5Y2xlIGhhcyBiZWVuIGludGVycnVwdGVkIHRvIHByZXZlbnQgYSBicm93c2VyIGNyYXNoLlwiO1xuICB2YXIgREVGQVVMVF9PUFRJT05TID0ge1xuICAgIHBsYWNlbWVudDogXCJib3R0b21cIixcbiAgICBtb2RpZmllcnM6IFtdLFxuICAgIHN0cmF0ZWd5OiBcImFic29sdXRlXCJcbiAgfTtcbiAgZnVuY3Rpb24gYXJlVmFsaWRFbGVtZW50cygpIHtcbiAgICBmb3IgKHZhciBfbGVuID0gYXJndW1lbnRzLmxlbmd0aCwgYXJncyA9IG5ldyBBcnJheShfbGVuKSwgX2tleSA9IDA7IF9rZXkgPCBfbGVuOyBfa2V5KyspIHtcbiAgICAgIGFyZ3NbX2tleV0gPSBhcmd1bWVudHNbX2tleV07XG4gICAgfVxuICAgIHJldHVybiAhYXJncy5zb21lKGZ1bmN0aW9uKGVsZW1lbnQpIHtcbiAgICAgIHJldHVybiAhKGVsZW1lbnQgJiYgdHlwZW9mIGVsZW1lbnQuZ2V0Qm91bmRpbmdDbGllbnRSZWN0ID09PSBcImZ1bmN0aW9uXCIpO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIHBvcHBlckdlbmVyYXRvcihnZW5lcmF0b3JPcHRpb25zKSB7XG4gICAgaWYgKGdlbmVyYXRvck9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgICAgZ2VuZXJhdG9yT3B0aW9ucyA9IHt9O1xuICAgIH1cbiAgICB2YXIgX2dlbmVyYXRvck9wdGlvbnMgPSBnZW5lcmF0b3JPcHRpb25zLCBfZ2VuZXJhdG9yT3B0aW9ucyRkZWYgPSBfZ2VuZXJhdG9yT3B0aW9ucy5kZWZhdWx0TW9kaWZpZXJzLCBkZWZhdWx0TW9kaWZpZXJzMiA9IF9nZW5lcmF0b3JPcHRpb25zJGRlZiA9PT0gdm9pZCAwID8gW10gOiBfZ2VuZXJhdG9yT3B0aW9ucyRkZWYsIF9nZW5lcmF0b3JPcHRpb25zJGRlZjIgPSBfZ2VuZXJhdG9yT3B0aW9ucy5kZWZhdWx0T3B0aW9ucywgZGVmYXVsdE9wdGlvbnMgPSBfZ2VuZXJhdG9yT3B0aW9ucyRkZWYyID09PSB2b2lkIDAgPyBERUZBVUxUX09QVElPTlMgOiBfZ2VuZXJhdG9yT3B0aW9ucyRkZWYyO1xuICAgIHJldHVybiBmdW5jdGlvbiBjcmVhdGVQb3BwZXIyKHJlZmVyZW5jZTIsIHBvcHBlcjIsIG9wdGlvbnMpIHtcbiAgICAgIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICAgICAgb3B0aW9ucyA9IGRlZmF1bHRPcHRpb25zO1xuICAgICAgfVxuICAgICAgdmFyIHN0YXRlID0ge1xuICAgICAgICBwbGFjZW1lbnQ6IFwiYm90dG9tXCIsXG4gICAgICAgIG9yZGVyZWRNb2RpZmllcnM6IFtdLFxuICAgICAgICBvcHRpb25zOiBPYmplY3QuYXNzaWduKHt9LCBERUZBVUxUX09QVElPTlMsIGRlZmF1bHRPcHRpb25zKSxcbiAgICAgICAgbW9kaWZpZXJzRGF0YToge30sXG4gICAgICAgIGVsZW1lbnRzOiB7XG4gICAgICAgICAgcmVmZXJlbmNlOiByZWZlcmVuY2UyLFxuICAgICAgICAgIHBvcHBlcjogcG9wcGVyMlxuICAgICAgICB9LFxuICAgICAgICBhdHRyaWJ1dGVzOiB7fSxcbiAgICAgICAgc3R5bGVzOiB7fVxuICAgICAgfTtcbiAgICAgIHZhciBlZmZlY3RDbGVhbnVwRm5zID0gW107XG4gICAgICB2YXIgaXNEZXN0cm95ZWQgPSBmYWxzZTtcbiAgICAgIHZhciBpbnN0YW5jZSA9IHtcbiAgICAgICAgc3RhdGUsXG4gICAgICAgIHNldE9wdGlvbnM6IGZ1bmN0aW9uIHNldE9wdGlvbnMob3B0aW9uczIpIHtcbiAgICAgICAgICBjbGVhbnVwTW9kaWZpZXJFZmZlY3RzKCk7XG4gICAgICAgICAgc3RhdGUub3B0aW9ucyA9IE9iamVjdC5hc3NpZ24oe30sIGRlZmF1bHRPcHRpb25zLCBzdGF0ZS5vcHRpb25zLCBvcHRpb25zMik7XG4gICAgICAgICAgc3RhdGUuc2Nyb2xsUGFyZW50cyA9IHtcbiAgICAgICAgICAgIHJlZmVyZW5jZTogaXNFbGVtZW50KHJlZmVyZW5jZTIpID8gbGlzdFNjcm9sbFBhcmVudHMocmVmZXJlbmNlMikgOiByZWZlcmVuY2UyLmNvbnRleHRFbGVtZW50ID8gbGlzdFNjcm9sbFBhcmVudHMocmVmZXJlbmNlMi5jb250ZXh0RWxlbWVudCkgOiBbXSxcbiAgICAgICAgICAgIHBvcHBlcjogbGlzdFNjcm9sbFBhcmVudHMocG9wcGVyMilcbiAgICAgICAgICB9O1xuICAgICAgICAgIHZhciBvcmRlcmVkTW9kaWZpZXJzID0gb3JkZXJNb2RpZmllcnMobWVyZ2VCeU5hbWUoW10uY29uY2F0KGRlZmF1bHRNb2RpZmllcnMyLCBzdGF0ZS5vcHRpb25zLm1vZGlmaWVycykpKTtcbiAgICAgICAgICBzdGF0ZS5vcmRlcmVkTW9kaWZpZXJzID0gb3JkZXJlZE1vZGlmaWVycy5maWx0ZXIoZnVuY3Rpb24obSkge1xuICAgICAgICAgICAgcmV0dXJuIG0uZW5hYmxlZDtcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBpZiAodHJ1ZSkge1xuICAgICAgICAgICAgdmFyIG1vZGlmaWVycyA9IHVuaXF1ZUJ5KFtdLmNvbmNhdChvcmRlcmVkTW9kaWZpZXJzLCBzdGF0ZS5vcHRpb25zLm1vZGlmaWVycyksIGZ1bmN0aW9uKF9yZWYpIHtcbiAgICAgICAgICAgICAgdmFyIG5hbWUgPSBfcmVmLm5hbWU7XG4gICAgICAgICAgICAgIHJldHVybiBuYW1lO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB2YWxpZGF0ZU1vZGlmaWVycyhtb2RpZmllcnMpO1xuICAgICAgICAgICAgaWYgKGdldEJhc2VQbGFjZW1lbnQoc3RhdGUub3B0aW9ucy5wbGFjZW1lbnQpID09PSBhdXRvKSB7XG4gICAgICAgICAgICAgIHZhciBmbGlwTW9kaWZpZXIgPSBzdGF0ZS5vcmRlcmVkTW9kaWZpZXJzLmZpbmQoZnVuY3Rpb24oX3JlZjIpIHtcbiAgICAgICAgICAgICAgICB2YXIgbmFtZSA9IF9yZWYyLm5hbWU7XG4gICAgICAgICAgICAgICAgcmV0dXJuIG5hbWUgPT09IFwiZmxpcFwiO1xuICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgaWYgKCFmbGlwTW9kaWZpZXIpIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKFsnUG9wcGVyOiBcImF1dG9cIiBwbGFjZW1lbnRzIHJlcXVpcmUgdGhlIFwiZmxpcFwiIG1vZGlmaWVyIGJlJywgXCJwcmVzZW50IGFuZCBlbmFibGVkIHRvIHdvcmsuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgdmFyIF9nZXRDb21wdXRlZFN0eWxlID0gZ2V0Q29tcHV0ZWRTdHlsZShwb3BwZXIyKSwgbWFyZ2luVG9wID0gX2dldENvbXB1dGVkU3R5bGUubWFyZ2luVG9wLCBtYXJnaW5SaWdodCA9IF9nZXRDb21wdXRlZFN0eWxlLm1hcmdpblJpZ2h0LCBtYXJnaW5Cb3R0b20gPSBfZ2V0Q29tcHV0ZWRTdHlsZS5tYXJnaW5Cb3R0b20sIG1hcmdpbkxlZnQgPSBfZ2V0Q29tcHV0ZWRTdHlsZS5tYXJnaW5MZWZ0O1xuICAgICAgICAgICAgaWYgKFttYXJnaW5Ub3AsIG1hcmdpblJpZ2h0LCBtYXJnaW5Cb3R0b20sIG1hcmdpbkxlZnRdLnNvbWUoZnVuY3Rpb24obWFyZ2luKSB7XG4gICAgICAgICAgICAgIHJldHVybiBwYXJzZUZsb2F0KG1hcmdpbik7XG4gICAgICAgICAgICB9KSkge1xuICAgICAgICAgICAgICBjb25zb2xlLndhcm4oWydQb3BwZXI6IENTUyBcIm1hcmdpblwiIHN0eWxlcyBjYW5ub3QgYmUgdXNlZCB0byBhcHBseSBwYWRkaW5nJywgXCJiZXR3ZWVuIHRoZSBwb3BwZXIgYW5kIGl0cyByZWZlcmVuY2UgZWxlbWVudCBvciBib3VuZGFyeS5cIiwgXCJUbyByZXBsaWNhdGUgbWFyZ2luLCB1c2UgdGhlIGBvZmZzZXRgIG1vZGlmaWVyLCBhcyB3ZWxsIGFzXCIsIFwidGhlIGBwYWRkaW5nYCBvcHRpb24gaW4gdGhlIGBwcmV2ZW50T3ZlcmZsb3dgIGFuZCBgZmxpcGBcIiwgXCJtb2RpZmllcnMuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICAgcnVuTW9kaWZpZXJFZmZlY3RzKCk7XG4gICAgICAgICAgcmV0dXJuIGluc3RhbmNlLnVwZGF0ZSgpO1xuICAgICAgICB9LFxuICAgICAgICBmb3JjZVVwZGF0ZTogZnVuY3Rpb24gZm9yY2VVcGRhdGUoKSB7XG4gICAgICAgICAgaWYgKGlzRGVzdHJveWVkKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgfVxuICAgICAgICAgIHZhciBfc3RhdGUkZWxlbWVudHMgPSBzdGF0ZS5lbGVtZW50cywgcmVmZXJlbmNlMyA9IF9zdGF0ZSRlbGVtZW50cy5yZWZlcmVuY2UsIHBvcHBlcjMgPSBfc3RhdGUkZWxlbWVudHMucG9wcGVyO1xuICAgICAgICAgIGlmICghYXJlVmFsaWRFbGVtZW50cyhyZWZlcmVuY2UzLCBwb3BwZXIzKSkge1xuICAgICAgICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgICAgICAgY29uc29sZS5lcnJvcihJTlZBTElEX0VMRU1FTlRfRVJST1IpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgIH1cbiAgICAgICAgICBzdGF0ZS5yZWN0cyA9IHtcbiAgICAgICAgICAgIHJlZmVyZW5jZTogZ2V0Q29tcG9zaXRlUmVjdChyZWZlcmVuY2UzLCBnZXRPZmZzZXRQYXJlbnQocG9wcGVyMyksIHN0YXRlLm9wdGlvbnMuc3RyYXRlZ3kgPT09IFwiZml4ZWRcIiksXG4gICAgICAgICAgICBwb3BwZXI6IGdldExheW91dFJlY3QocG9wcGVyMylcbiAgICAgICAgICB9O1xuICAgICAgICAgIHN0YXRlLnJlc2V0ID0gZmFsc2U7XG4gICAgICAgICAgc3RhdGUucGxhY2VtZW50ID0gc3RhdGUub3B0aW9ucy5wbGFjZW1lbnQ7XG4gICAgICAgICAgc3RhdGUub3JkZXJlZE1vZGlmaWVycy5mb3JFYWNoKGZ1bmN0aW9uKG1vZGlmaWVyKSB7XG4gICAgICAgICAgICByZXR1cm4gc3RhdGUubW9kaWZpZXJzRGF0YVttb2RpZmllci5uYW1lXSA9IE9iamVjdC5hc3NpZ24oe30sIG1vZGlmaWVyLmRhdGEpO1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIHZhciBfX2RlYnVnX2xvb3BzX18gPSAwO1xuICAgICAgICAgIGZvciAodmFyIGluZGV4ID0gMDsgaW5kZXggPCBzdGF0ZS5vcmRlcmVkTW9kaWZpZXJzLmxlbmd0aDsgaW5kZXgrKykge1xuICAgICAgICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgICAgICAgX19kZWJ1Z19sb29wc19fICs9IDE7XG4gICAgICAgICAgICAgIGlmIChfX2RlYnVnX2xvb3BzX18gPiAxMDApIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKElORklOSVRFX0xPT1BfRVJST1IpO1xuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAoc3RhdGUucmVzZXQgPT09IHRydWUpIHtcbiAgICAgICAgICAgICAgc3RhdGUucmVzZXQgPSBmYWxzZTtcbiAgICAgICAgICAgICAgaW5kZXggPSAtMTtcbiAgICAgICAgICAgICAgY29udGludWU7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB2YXIgX3N0YXRlJG9yZGVyZWRNb2RpZmllID0gc3RhdGUub3JkZXJlZE1vZGlmaWVyc1tpbmRleF0sIGZuID0gX3N0YXRlJG9yZGVyZWRNb2RpZmllLmZuLCBfc3RhdGUkb3JkZXJlZE1vZGlmaWUyID0gX3N0YXRlJG9yZGVyZWRNb2RpZmllLm9wdGlvbnMsIF9vcHRpb25zID0gX3N0YXRlJG9yZGVyZWRNb2RpZmllMiA9PT0gdm9pZCAwID8ge30gOiBfc3RhdGUkb3JkZXJlZE1vZGlmaWUyLCBuYW1lID0gX3N0YXRlJG9yZGVyZWRNb2RpZmllLm5hbWU7XG4gICAgICAgICAgICBpZiAodHlwZW9mIGZuID09PSBcImZ1bmN0aW9uXCIpIHtcbiAgICAgICAgICAgICAgc3RhdGUgPSBmbih7XG4gICAgICAgICAgICAgICAgc3RhdGUsXG4gICAgICAgICAgICAgICAgb3B0aW9uczogX29wdGlvbnMsXG4gICAgICAgICAgICAgICAgbmFtZSxcbiAgICAgICAgICAgICAgICBpbnN0YW5jZVxuICAgICAgICAgICAgICB9KSB8fCBzdGF0ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIHVwZGF0ZTogZGVib3VuY2UoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKGZ1bmN0aW9uKHJlc29sdmUpIHtcbiAgICAgICAgICAgIGluc3RhbmNlLmZvcmNlVXBkYXRlKCk7XG4gICAgICAgICAgICByZXNvbHZlKHN0YXRlKTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgfSksXG4gICAgICAgIGRlc3Ryb3k6IGZ1bmN0aW9uIGRlc3Ryb3koKSB7XG4gICAgICAgICAgY2xlYW51cE1vZGlmaWVyRWZmZWN0cygpO1xuICAgICAgICAgIGlzRGVzdHJveWVkID0gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICAgIGlmICghYXJlVmFsaWRFbGVtZW50cyhyZWZlcmVuY2UyLCBwb3BwZXIyKSkge1xuICAgICAgICBpZiAodHJ1ZSkge1xuICAgICAgICAgIGNvbnNvbGUuZXJyb3IoSU5WQUxJRF9FTEVNRU5UX0VSUk9SKTtcbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gaW5zdGFuY2U7XG4gICAgICB9XG4gICAgICBpbnN0YW5jZS5zZXRPcHRpb25zKG9wdGlvbnMpLnRoZW4oZnVuY3Rpb24oc3RhdGUyKSB7XG4gICAgICAgIGlmICghaXNEZXN0cm95ZWQgJiYgb3B0aW9ucy5vbkZpcnN0VXBkYXRlKSB7XG4gICAgICAgICAgb3B0aW9ucy5vbkZpcnN0VXBkYXRlKHN0YXRlMik7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgICAgZnVuY3Rpb24gcnVuTW9kaWZpZXJFZmZlY3RzKCkge1xuICAgICAgICBzdGF0ZS5vcmRlcmVkTW9kaWZpZXJzLmZvckVhY2goZnVuY3Rpb24oX3JlZjMpIHtcbiAgICAgICAgICB2YXIgbmFtZSA9IF9yZWYzLm5hbWUsIF9yZWYzJG9wdGlvbnMgPSBfcmVmMy5vcHRpb25zLCBvcHRpb25zMiA9IF9yZWYzJG9wdGlvbnMgPT09IHZvaWQgMCA/IHt9IDogX3JlZjMkb3B0aW9ucywgZWZmZWN0MiA9IF9yZWYzLmVmZmVjdDtcbiAgICAgICAgICBpZiAodHlwZW9mIGVmZmVjdDIgPT09IFwiZnVuY3Rpb25cIikge1xuICAgICAgICAgICAgdmFyIGNsZWFudXBGbiA9IGVmZmVjdDIoe1xuICAgICAgICAgICAgICBzdGF0ZSxcbiAgICAgICAgICAgICAgbmFtZSxcbiAgICAgICAgICAgICAgaW5zdGFuY2UsXG4gICAgICAgICAgICAgIG9wdGlvbnM6IG9wdGlvbnMyXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIHZhciBub29wRm4gPSBmdW5jdGlvbiBub29wRm4yKCkge1xuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIGVmZmVjdENsZWFudXBGbnMucHVzaChjbGVhbnVwRm4gfHwgbm9vcEZuKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gY2xlYW51cE1vZGlmaWVyRWZmZWN0cygpIHtcbiAgICAgICAgZWZmZWN0Q2xlYW51cEZucy5mb3JFYWNoKGZ1bmN0aW9uKGZuKSB7XG4gICAgICAgICAgcmV0dXJuIGZuKCk7XG4gICAgICAgIH0pO1xuICAgICAgICBlZmZlY3RDbGVhbnVwRm5zID0gW107XG4gICAgICB9XG4gICAgICByZXR1cm4gaW5zdGFuY2U7XG4gICAgfTtcbiAgfVxuICB2YXIgcGFzc2l2ZSA9IHtcbiAgICBwYXNzaXZlOiB0cnVlXG4gIH07XG4gIGZ1bmN0aW9uIGVmZmVjdCQyKF9yZWYpIHtcbiAgICB2YXIgc3RhdGUgPSBfcmVmLnN0YXRlLCBpbnN0YW5jZSA9IF9yZWYuaW5zdGFuY2UsIG9wdGlvbnMgPSBfcmVmLm9wdGlvbnM7XG4gICAgdmFyIF9vcHRpb25zJHNjcm9sbCA9IG9wdGlvbnMuc2Nyb2xsLCBzY3JvbGwgPSBfb3B0aW9ucyRzY3JvbGwgPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyRzY3JvbGwsIF9vcHRpb25zJHJlc2l6ZSA9IG9wdGlvbnMucmVzaXplLCByZXNpemUgPSBfb3B0aW9ucyRyZXNpemUgPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyRyZXNpemU7XG4gICAgdmFyIHdpbmRvdzIgPSBnZXRXaW5kb3coc3RhdGUuZWxlbWVudHMucG9wcGVyKTtcbiAgICB2YXIgc2Nyb2xsUGFyZW50cyA9IFtdLmNvbmNhdChzdGF0ZS5zY3JvbGxQYXJlbnRzLnJlZmVyZW5jZSwgc3RhdGUuc2Nyb2xsUGFyZW50cy5wb3BwZXIpO1xuICAgIGlmIChzY3JvbGwpIHtcbiAgICAgIHNjcm9sbFBhcmVudHMuZm9yRWFjaChmdW5jdGlvbihzY3JvbGxQYXJlbnQpIHtcbiAgICAgICAgc2Nyb2xsUGFyZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJzY3JvbGxcIiwgaW5zdGFuY2UudXBkYXRlLCBwYXNzaXZlKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICBpZiAocmVzaXplKSB7XG4gICAgICB3aW5kb3cyLmFkZEV2ZW50TGlzdGVuZXIoXCJyZXNpemVcIiwgaW5zdGFuY2UudXBkYXRlLCBwYXNzaXZlKTtcbiAgICB9XG4gICAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgICAgaWYgKHNjcm9sbCkge1xuICAgICAgICBzY3JvbGxQYXJlbnRzLmZvckVhY2goZnVuY3Rpb24oc2Nyb2xsUGFyZW50KSB7XG4gICAgICAgICAgc2Nyb2xsUGFyZW50LnJlbW92ZUV2ZW50TGlzdGVuZXIoXCJzY3JvbGxcIiwgaW5zdGFuY2UudXBkYXRlLCBwYXNzaXZlKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBpZiAocmVzaXplKSB7XG4gICAgICAgIHdpbmRvdzIucmVtb3ZlRXZlbnRMaXN0ZW5lcihcInJlc2l6ZVwiLCBpbnN0YW5jZS51cGRhdGUsIHBhc3NpdmUpO1xuICAgICAgfVxuICAgIH07XG4gIH1cbiAgdmFyIGV2ZW50TGlzdGVuZXJzID0ge1xuICAgIG5hbWU6IFwiZXZlbnRMaXN0ZW5lcnNcIixcbiAgICBlbmFibGVkOiB0cnVlLFxuICAgIHBoYXNlOiBcIndyaXRlXCIsXG4gICAgZm46IGZ1bmN0aW9uIGZuKCkge1xuICAgIH0sXG4gICAgZWZmZWN0OiBlZmZlY3QkMixcbiAgICBkYXRhOiB7fVxuICB9O1xuICBmdW5jdGlvbiBwb3BwZXJPZmZzZXRzKF9yZWYpIHtcbiAgICB2YXIgc3RhdGUgPSBfcmVmLnN0YXRlLCBuYW1lID0gX3JlZi5uYW1lO1xuICAgIHN0YXRlLm1vZGlmaWVyc0RhdGFbbmFtZV0gPSBjb21wdXRlT2Zmc2V0cyh7XG4gICAgICByZWZlcmVuY2U6IHN0YXRlLnJlY3RzLnJlZmVyZW5jZSxcbiAgICAgIGVsZW1lbnQ6IHN0YXRlLnJlY3RzLnBvcHBlcixcbiAgICAgIHN0cmF0ZWd5OiBcImFic29sdXRlXCIsXG4gICAgICBwbGFjZW1lbnQ6IHN0YXRlLnBsYWNlbWVudFxuICAgIH0pO1xuICB9XG4gIHZhciBwb3BwZXJPZmZzZXRzJDEgPSB7XG4gICAgbmFtZTogXCJwb3BwZXJPZmZzZXRzXCIsXG4gICAgZW5hYmxlZDogdHJ1ZSxcbiAgICBwaGFzZTogXCJyZWFkXCIsXG4gICAgZm46IHBvcHBlck9mZnNldHMsXG4gICAgZGF0YToge31cbiAgfTtcbiAgdmFyIHVuc2V0U2lkZXMgPSB7XG4gICAgdG9wOiBcImF1dG9cIixcbiAgICByaWdodDogXCJhdXRvXCIsXG4gICAgYm90dG9tOiBcImF1dG9cIixcbiAgICBsZWZ0OiBcImF1dG9cIlxuICB9O1xuICBmdW5jdGlvbiByb3VuZE9mZnNldHNCeURQUihfcmVmKSB7XG4gICAgdmFyIHggPSBfcmVmLngsIHkgPSBfcmVmLnk7XG4gICAgdmFyIHdpbiA9IHdpbmRvdztcbiAgICB2YXIgZHByID0gd2luLmRldmljZVBpeGVsUmF0aW8gfHwgMTtcbiAgICByZXR1cm4ge1xuICAgICAgeDogcm91bmQocm91bmQoeCAqIGRwcikgLyBkcHIpIHx8IDAsXG4gICAgICB5OiByb3VuZChyb3VuZCh5ICogZHByKSAvIGRwcikgfHwgMFxuICAgIH07XG4gIH1cbiAgZnVuY3Rpb24gbWFwVG9TdHlsZXMoX3JlZjIpIHtcbiAgICB2YXIgX09iamVjdCRhc3NpZ24yO1xuICAgIHZhciBwb3BwZXIyID0gX3JlZjIucG9wcGVyLCBwb3BwZXJSZWN0ID0gX3JlZjIucG9wcGVyUmVjdCwgcGxhY2VtZW50ID0gX3JlZjIucGxhY2VtZW50LCBvZmZzZXRzID0gX3JlZjIub2Zmc2V0cywgcG9zaXRpb24gPSBfcmVmMi5wb3NpdGlvbiwgZ3B1QWNjZWxlcmF0aW9uID0gX3JlZjIuZ3B1QWNjZWxlcmF0aW9uLCBhZGFwdGl2ZSA9IF9yZWYyLmFkYXB0aXZlLCByb3VuZE9mZnNldHMgPSBfcmVmMi5yb3VuZE9mZnNldHM7XG4gICAgdmFyIF9yZWYzID0gcm91bmRPZmZzZXRzID09PSB0cnVlID8gcm91bmRPZmZzZXRzQnlEUFIob2Zmc2V0cykgOiB0eXBlb2Ygcm91bmRPZmZzZXRzID09PSBcImZ1bmN0aW9uXCIgPyByb3VuZE9mZnNldHMob2Zmc2V0cykgOiBvZmZzZXRzLCBfcmVmMyR4ID0gX3JlZjMueCwgeCA9IF9yZWYzJHggPT09IHZvaWQgMCA/IDAgOiBfcmVmMyR4LCBfcmVmMyR5ID0gX3JlZjMueSwgeSA9IF9yZWYzJHkgPT09IHZvaWQgMCA/IDAgOiBfcmVmMyR5O1xuICAgIHZhciBoYXNYID0gb2Zmc2V0cy5oYXNPd25Qcm9wZXJ0eShcInhcIik7XG4gICAgdmFyIGhhc1kgPSBvZmZzZXRzLmhhc093blByb3BlcnR5KFwieVwiKTtcbiAgICB2YXIgc2lkZVggPSBsZWZ0O1xuICAgIHZhciBzaWRlWSA9IHRvcDtcbiAgICB2YXIgd2luID0gd2luZG93O1xuICAgIGlmIChhZGFwdGl2ZSkge1xuICAgICAgdmFyIG9mZnNldFBhcmVudCA9IGdldE9mZnNldFBhcmVudChwb3BwZXIyKTtcbiAgICAgIHZhciBoZWlnaHRQcm9wID0gXCJjbGllbnRIZWlnaHRcIjtcbiAgICAgIHZhciB3aWR0aFByb3AgPSBcImNsaWVudFdpZHRoXCI7XG4gICAgICBpZiAob2Zmc2V0UGFyZW50ID09PSBnZXRXaW5kb3cocG9wcGVyMikpIHtcbiAgICAgICAgb2Zmc2V0UGFyZW50ID0gZ2V0RG9jdW1lbnRFbGVtZW50KHBvcHBlcjIpO1xuICAgICAgICBpZiAoZ2V0Q29tcHV0ZWRTdHlsZShvZmZzZXRQYXJlbnQpLnBvc2l0aW9uICE9PSBcInN0YXRpY1wiKSB7XG4gICAgICAgICAgaGVpZ2h0UHJvcCA9IFwic2Nyb2xsSGVpZ2h0XCI7XG4gICAgICAgICAgd2lkdGhQcm9wID0gXCJzY3JvbGxXaWR0aFwiO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBvZmZzZXRQYXJlbnQgPSBvZmZzZXRQYXJlbnQ7XG4gICAgICBpZiAocGxhY2VtZW50ID09PSB0b3ApIHtcbiAgICAgICAgc2lkZVkgPSBib3R0b207XG4gICAgICAgIHkgLT0gb2Zmc2V0UGFyZW50W2hlaWdodFByb3BdIC0gcG9wcGVyUmVjdC5oZWlnaHQ7XG4gICAgICAgIHkgKj0gZ3B1QWNjZWxlcmF0aW9uID8gMSA6IC0xO1xuICAgICAgfVxuICAgICAgaWYgKHBsYWNlbWVudCA9PT0gbGVmdCkge1xuICAgICAgICBzaWRlWCA9IHJpZ2h0O1xuICAgICAgICB4IC09IG9mZnNldFBhcmVudFt3aWR0aFByb3BdIC0gcG9wcGVyUmVjdC53aWR0aDtcbiAgICAgICAgeCAqPSBncHVBY2NlbGVyYXRpb24gPyAxIDogLTE7XG4gICAgICB9XG4gICAgfVxuICAgIHZhciBjb21tb25TdHlsZXMgPSBPYmplY3QuYXNzaWduKHtcbiAgICAgIHBvc2l0aW9uXG4gICAgfSwgYWRhcHRpdmUgJiYgdW5zZXRTaWRlcyk7XG4gICAgaWYgKGdwdUFjY2VsZXJhdGlvbikge1xuICAgICAgdmFyIF9PYmplY3QkYXNzaWduO1xuICAgICAgcmV0dXJuIE9iamVjdC5hc3NpZ24oe30sIGNvbW1vblN0eWxlcywgKF9PYmplY3QkYXNzaWduID0ge30sIF9PYmplY3QkYXNzaWduW3NpZGVZXSA9IGhhc1kgPyBcIjBcIiA6IFwiXCIsIF9PYmplY3QkYXNzaWduW3NpZGVYXSA9IGhhc1ggPyBcIjBcIiA6IFwiXCIsIF9PYmplY3QkYXNzaWduLnRyYW5zZm9ybSA9ICh3aW4uZGV2aWNlUGl4ZWxSYXRpbyB8fCAxKSA8IDIgPyBcInRyYW5zbGF0ZShcIiArIHggKyBcInB4LCBcIiArIHkgKyBcInB4KVwiIDogXCJ0cmFuc2xhdGUzZChcIiArIHggKyBcInB4LCBcIiArIHkgKyBcInB4LCAwKVwiLCBfT2JqZWN0JGFzc2lnbikpO1xuICAgIH1cbiAgICByZXR1cm4gT2JqZWN0LmFzc2lnbih7fSwgY29tbW9uU3R5bGVzLCAoX09iamVjdCRhc3NpZ24yID0ge30sIF9PYmplY3QkYXNzaWduMltzaWRlWV0gPSBoYXNZID8geSArIFwicHhcIiA6IFwiXCIsIF9PYmplY3QkYXNzaWduMltzaWRlWF0gPSBoYXNYID8geCArIFwicHhcIiA6IFwiXCIsIF9PYmplY3QkYXNzaWduMi50cmFuc2Zvcm0gPSBcIlwiLCBfT2JqZWN0JGFzc2lnbjIpKTtcbiAgfVxuICBmdW5jdGlvbiBjb21wdXRlU3R5bGVzKF9yZWY0KSB7XG4gICAgdmFyIHN0YXRlID0gX3JlZjQuc3RhdGUsIG9wdGlvbnMgPSBfcmVmNC5vcHRpb25zO1xuICAgIHZhciBfb3B0aW9ucyRncHVBY2NlbGVyYXQgPSBvcHRpb25zLmdwdUFjY2VsZXJhdGlvbiwgZ3B1QWNjZWxlcmF0aW9uID0gX29wdGlvbnMkZ3B1QWNjZWxlcmF0ID09PSB2b2lkIDAgPyB0cnVlIDogX29wdGlvbnMkZ3B1QWNjZWxlcmF0LCBfb3B0aW9ucyRhZGFwdGl2ZSA9IG9wdGlvbnMuYWRhcHRpdmUsIGFkYXB0aXZlID0gX29wdGlvbnMkYWRhcHRpdmUgPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyRhZGFwdGl2ZSwgX29wdGlvbnMkcm91bmRPZmZzZXRzID0gb3B0aW9ucy5yb3VuZE9mZnNldHMsIHJvdW5kT2Zmc2V0cyA9IF9vcHRpb25zJHJvdW5kT2Zmc2V0cyA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJHJvdW5kT2Zmc2V0cztcbiAgICBpZiAodHJ1ZSkge1xuICAgICAgdmFyIHRyYW5zaXRpb25Qcm9wZXJ0eSA9IGdldENvbXB1dGVkU3R5bGUoc3RhdGUuZWxlbWVudHMucG9wcGVyKS50cmFuc2l0aW9uUHJvcGVydHkgfHwgXCJcIjtcbiAgICAgIGlmIChhZGFwdGl2ZSAmJiBbXCJ0cmFuc2Zvcm1cIiwgXCJ0b3BcIiwgXCJyaWdodFwiLCBcImJvdHRvbVwiLCBcImxlZnRcIl0uc29tZShmdW5jdGlvbihwcm9wZXJ0eSkge1xuICAgICAgICByZXR1cm4gdHJhbnNpdGlvblByb3BlcnR5LmluZGV4T2YocHJvcGVydHkpID49IDA7XG4gICAgICB9KSkge1xuICAgICAgICBjb25zb2xlLndhcm4oW1wiUG9wcGVyOiBEZXRlY3RlZCBDU1MgdHJhbnNpdGlvbnMgb24gYXQgbGVhc3Qgb25lIG9mIHRoZSBmb2xsb3dpbmdcIiwgJ0NTUyBwcm9wZXJ0aWVzOiBcInRyYW5zZm9ybVwiLCBcInRvcFwiLCBcInJpZ2h0XCIsIFwiYm90dG9tXCIsIFwibGVmdFwiLicsIFwiXFxuXFxuXCIsICdEaXNhYmxlIHRoZSBcImNvbXB1dGVTdHlsZXNcIiBtb2RpZmllclxcJ3MgYGFkYXB0aXZlYCBvcHRpb24gdG8gYWxsb3cnLCBcImZvciBzbW9vdGggdHJhbnNpdGlvbnMsIG9yIHJlbW92ZSB0aGVzZSBwcm9wZXJ0aWVzIGZyb20gdGhlIENTU1wiLCBcInRyYW5zaXRpb24gZGVjbGFyYXRpb24gb24gdGhlIHBvcHBlciBlbGVtZW50IGlmIG9ubHkgdHJhbnNpdGlvbmluZ1wiLCBcIm9wYWNpdHkgb3IgYmFja2dyb3VuZC1jb2xvciBmb3IgZXhhbXBsZS5cIiwgXCJcXG5cXG5cIiwgXCJXZSByZWNvbW1lbmQgdXNpbmcgdGhlIHBvcHBlciBlbGVtZW50IGFzIGEgd3JhcHBlciBhcm91bmQgYW4gaW5uZXJcIiwgXCJlbGVtZW50IHRoYXQgY2FuIGhhdmUgYW55IENTUyBwcm9wZXJ0eSB0cmFuc2l0aW9uZWQgZm9yIGFuaW1hdGlvbnMuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICAgIH1cbiAgICB9XG4gICAgdmFyIGNvbW1vblN0eWxlcyA9IHtcbiAgICAgIHBsYWNlbWVudDogZ2V0QmFzZVBsYWNlbWVudChzdGF0ZS5wbGFjZW1lbnQpLFxuICAgICAgcG9wcGVyOiBzdGF0ZS5lbGVtZW50cy5wb3BwZXIsXG4gICAgICBwb3BwZXJSZWN0OiBzdGF0ZS5yZWN0cy5wb3BwZXIsXG4gICAgICBncHVBY2NlbGVyYXRpb25cbiAgICB9O1xuICAgIGlmIChzdGF0ZS5tb2RpZmllcnNEYXRhLnBvcHBlck9mZnNldHMgIT0gbnVsbCkge1xuICAgICAgc3RhdGUuc3R5bGVzLnBvcHBlciA9IE9iamVjdC5hc3NpZ24oe30sIHN0YXRlLnN0eWxlcy5wb3BwZXIsIG1hcFRvU3R5bGVzKE9iamVjdC5hc3NpZ24oe30sIGNvbW1vblN0eWxlcywge1xuICAgICAgICBvZmZzZXRzOiBzdGF0ZS5tb2RpZmllcnNEYXRhLnBvcHBlck9mZnNldHMsXG4gICAgICAgIHBvc2l0aW9uOiBzdGF0ZS5vcHRpb25zLnN0cmF0ZWd5LFxuICAgICAgICBhZGFwdGl2ZSxcbiAgICAgICAgcm91bmRPZmZzZXRzXG4gICAgICB9KSkpO1xuICAgIH1cbiAgICBpZiAoc3RhdGUubW9kaWZpZXJzRGF0YS5hcnJvdyAhPSBudWxsKSB7XG4gICAgICBzdGF0ZS5zdHlsZXMuYXJyb3cgPSBPYmplY3QuYXNzaWduKHt9LCBzdGF0ZS5zdHlsZXMuYXJyb3csIG1hcFRvU3R5bGVzKE9iamVjdC5hc3NpZ24oe30sIGNvbW1vblN0eWxlcywge1xuICAgICAgICBvZmZzZXRzOiBzdGF0ZS5tb2RpZmllcnNEYXRhLmFycm93LFxuICAgICAgICBwb3NpdGlvbjogXCJhYnNvbHV0ZVwiLFxuICAgICAgICBhZGFwdGl2ZTogZmFsc2UsXG4gICAgICAgIHJvdW5kT2Zmc2V0c1xuICAgICAgfSkpKTtcbiAgICB9XG4gICAgc3RhdGUuYXR0cmlidXRlcy5wb3BwZXIgPSBPYmplY3QuYXNzaWduKHt9LCBzdGF0ZS5hdHRyaWJ1dGVzLnBvcHBlciwge1xuICAgICAgXCJkYXRhLXBvcHBlci1wbGFjZW1lbnRcIjogc3RhdGUucGxhY2VtZW50XG4gICAgfSk7XG4gIH1cbiAgdmFyIGNvbXB1dGVTdHlsZXMkMSA9IHtcbiAgICBuYW1lOiBcImNvbXB1dGVTdHlsZXNcIixcbiAgICBlbmFibGVkOiB0cnVlLFxuICAgIHBoYXNlOiBcImJlZm9yZVdyaXRlXCIsXG4gICAgZm46IGNvbXB1dGVTdHlsZXMsXG4gICAgZGF0YToge31cbiAgfTtcbiAgZnVuY3Rpb24gYXBwbHlTdHlsZXMoX3JlZikge1xuICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGU7XG4gICAgT2JqZWN0LmtleXMoc3RhdGUuZWxlbWVudHMpLmZvckVhY2goZnVuY3Rpb24obmFtZSkge1xuICAgICAgdmFyIHN0eWxlID0gc3RhdGUuc3R5bGVzW25hbWVdIHx8IHt9O1xuICAgICAgdmFyIGF0dHJpYnV0ZXMgPSBzdGF0ZS5hdHRyaWJ1dGVzW25hbWVdIHx8IHt9O1xuICAgICAgdmFyIGVsZW1lbnQgPSBzdGF0ZS5lbGVtZW50c1tuYW1lXTtcbiAgICAgIGlmICghaXNIVE1MRWxlbWVudChlbGVtZW50KSB8fCAhZ2V0Tm9kZU5hbWUoZWxlbWVudCkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgT2JqZWN0LmFzc2lnbihlbGVtZW50LnN0eWxlLCBzdHlsZSk7XG4gICAgICBPYmplY3Qua2V5cyhhdHRyaWJ1dGVzKS5mb3JFYWNoKGZ1bmN0aW9uKG5hbWUyKSB7XG4gICAgICAgIHZhciB2YWx1ZSA9IGF0dHJpYnV0ZXNbbmFtZTJdO1xuICAgICAgICBpZiAodmFsdWUgPT09IGZhbHNlKSB7XG4gICAgICAgICAgZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUobmFtZTIpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGVsZW1lbnQuc2V0QXR0cmlidXRlKG5hbWUyLCB2YWx1ZSA9PT0gdHJ1ZSA/IFwiXCIgOiB2YWx1ZSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGVmZmVjdCQxKF9yZWYyKSB7XG4gICAgdmFyIHN0YXRlID0gX3JlZjIuc3RhdGU7XG4gICAgdmFyIGluaXRpYWxTdHlsZXMgPSB7XG4gICAgICBwb3BwZXI6IHtcbiAgICAgICAgcG9zaXRpb246IHN0YXRlLm9wdGlvbnMuc3RyYXRlZ3ksXG4gICAgICAgIGxlZnQ6IFwiMFwiLFxuICAgICAgICB0b3A6IFwiMFwiLFxuICAgICAgICBtYXJnaW46IFwiMFwiXG4gICAgICB9LFxuICAgICAgYXJyb3c6IHtcbiAgICAgICAgcG9zaXRpb246IFwiYWJzb2x1dGVcIlxuICAgICAgfSxcbiAgICAgIHJlZmVyZW5jZToge31cbiAgICB9O1xuICAgIE9iamVjdC5hc3NpZ24oc3RhdGUuZWxlbWVudHMucG9wcGVyLnN0eWxlLCBpbml0aWFsU3R5bGVzLnBvcHBlcik7XG4gICAgc3RhdGUuc3R5bGVzID0gaW5pdGlhbFN0eWxlcztcbiAgICBpZiAoc3RhdGUuZWxlbWVudHMuYXJyb3cpIHtcbiAgICAgIE9iamVjdC5hc3NpZ24oc3RhdGUuZWxlbWVudHMuYXJyb3cuc3R5bGUsIGluaXRpYWxTdHlsZXMuYXJyb3cpO1xuICAgIH1cbiAgICByZXR1cm4gZnVuY3Rpb24oKSB7XG4gICAgICBPYmplY3Qua2V5cyhzdGF0ZS5lbGVtZW50cykuZm9yRWFjaChmdW5jdGlvbihuYW1lKSB7XG4gICAgICAgIHZhciBlbGVtZW50ID0gc3RhdGUuZWxlbWVudHNbbmFtZV07XG4gICAgICAgIHZhciBhdHRyaWJ1dGVzID0gc3RhdGUuYXR0cmlidXRlc1tuYW1lXSB8fCB7fTtcbiAgICAgICAgdmFyIHN0eWxlUHJvcGVydGllcyA9IE9iamVjdC5rZXlzKHN0YXRlLnN0eWxlcy5oYXNPd25Qcm9wZXJ0eShuYW1lKSA/IHN0YXRlLnN0eWxlc1tuYW1lXSA6IGluaXRpYWxTdHlsZXNbbmFtZV0pO1xuICAgICAgICB2YXIgc3R5bGUgPSBzdHlsZVByb3BlcnRpZXMucmVkdWNlKGZ1bmN0aW9uKHN0eWxlMiwgcHJvcGVydHkpIHtcbiAgICAgICAgICBzdHlsZTJbcHJvcGVydHldID0gXCJcIjtcbiAgICAgICAgICByZXR1cm4gc3R5bGUyO1xuICAgICAgICB9LCB7fSk7XG4gICAgICAgIGlmICghaXNIVE1MRWxlbWVudChlbGVtZW50KSB8fCAhZ2V0Tm9kZU5hbWUoZWxlbWVudCkpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgT2JqZWN0LmFzc2lnbihlbGVtZW50LnN0eWxlLCBzdHlsZSk7XG4gICAgICAgIE9iamVjdC5rZXlzKGF0dHJpYnV0ZXMpLmZvckVhY2goZnVuY3Rpb24oYXR0cmlidXRlKSB7XG4gICAgICAgICAgZWxlbWVudC5yZW1vdmVBdHRyaWJ1dGUoYXR0cmlidXRlKTtcbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICB9O1xuICB9XG4gIHZhciBhcHBseVN0eWxlcyQxID0ge1xuICAgIG5hbWU6IFwiYXBwbHlTdHlsZXNcIixcbiAgICBlbmFibGVkOiB0cnVlLFxuICAgIHBoYXNlOiBcIndyaXRlXCIsXG4gICAgZm46IGFwcGx5U3R5bGVzLFxuICAgIGVmZmVjdDogZWZmZWN0JDEsXG4gICAgcmVxdWlyZXM6IFtcImNvbXB1dGVTdHlsZXNcIl1cbiAgfTtcbiAgZnVuY3Rpb24gZGlzdGFuY2VBbmRTa2lkZGluZ1RvWFkocGxhY2VtZW50LCByZWN0cywgb2Zmc2V0Mikge1xuICAgIHZhciBiYXNlUGxhY2VtZW50ID0gZ2V0QmFzZVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICAgIHZhciBpbnZlcnREaXN0YW5jZSA9IFtsZWZ0LCB0b3BdLmluZGV4T2YoYmFzZVBsYWNlbWVudCkgPj0gMCA/IC0xIDogMTtcbiAgICB2YXIgX3JlZiA9IHR5cGVvZiBvZmZzZXQyID09PSBcImZ1bmN0aW9uXCIgPyBvZmZzZXQyKE9iamVjdC5hc3NpZ24oe30sIHJlY3RzLCB7XG4gICAgICBwbGFjZW1lbnRcbiAgICB9KSkgOiBvZmZzZXQyLCBza2lkZGluZyA9IF9yZWZbMF0sIGRpc3RhbmNlID0gX3JlZlsxXTtcbiAgICBza2lkZGluZyA9IHNraWRkaW5nIHx8IDA7XG4gICAgZGlzdGFuY2UgPSAoZGlzdGFuY2UgfHwgMCkgKiBpbnZlcnREaXN0YW5jZTtcbiAgICByZXR1cm4gW2xlZnQsIHJpZ2h0XS5pbmRleE9mKGJhc2VQbGFjZW1lbnQpID49IDAgPyB7XG4gICAgICB4OiBkaXN0YW5jZSxcbiAgICAgIHk6IHNraWRkaW5nXG4gICAgfSA6IHtcbiAgICAgIHg6IHNraWRkaW5nLFxuICAgICAgeTogZGlzdGFuY2VcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIG9mZnNldChfcmVmMikge1xuICAgIHZhciBzdGF0ZSA9IF9yZWYyLnN0YXRlLCBvcHRpb25zID0gX3JlZjIub3B0aW9ucywgbmFtZSA9IF9yZWYyLm5hbWU7XG4gICAgdmFyIF9vcHRpb25zJG9mZnNldCA9IG9wdGlvbnMub2Zmc2V0LCBvZmZzZXQyID0gX29wdGlvbnMkb2Zmc2V0ID09PSB2b2lkIDAgPyBbMCwgMF0gOiBfb3B0aW9ucyRvZmZzZXQ7XG4gICAgdmFyIGRhdGEgPSBwbGFjZW1lbnRzLnJlZHVjZShmdW5jdGlvbihhY2MsIHBsYWNlbWVudCkge1xuICAgICAgYWNjW3BsYWNlbWVudF0gPSBkaXN0YW5jZUFuZFNraWRkaW5nVG9YWShwbGFjZW1lbnQsIHN0YXRlLnJlY3RzLCBvZmZzZXQyKTtcbiAgICAgIHJldHVybiBhY2M7XG4gICAgfSwge30pO1xuICAgIHZhciBfZGF0YSRzdGF0ZSRwbGFjZW1lbnQgPSBkYXRhW3N0YXRlLnBsYWNlbWVudF0sIHggPSBfZGF0YSRzdGF0ZSRwbGFjZW1lbnQueCwgeSA9IF9kYXRhJHN0YXRlJHBsYWNlbWVudC55O1xuICAgIGlmIChzdGF0ZS5tb2RpZmllcnNEYXRhLnBvcHBlck9mZnNldHMgIT0gbnVsbCkge1xuICAgICAgc3RhdGUubW9kaWZpZXJzRGF0YS5wb3BwZXJPZmZzZXRzLnggKz0geDtcbiAgICAgIHN0YXRlLm1vZGlmaWVyc0RhdGEucG9wcGVyT2Zmc2V0cy55ICs9IHk7XG4gICAgfVxuICAgIHN0YXRlLm1vZGlmaWVyc0RhdGFbbmFtZV0gPSBkYXRhO1xuICB9XG4gIHZhciBvZmZzZXQkMSA9IHtcbiAgICBuYW1lOiBcIm9mZnNldFwiLFxuICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgcGhhc2U6IFwibWFpblwiLFxuICAgIHJlcXVpcmVzOiBbXCJwb3BwZXJPZmZzZXRzXCJdLFxuICAgIGZuOiBvZmZzZXRcbiAgfTtcbiAgdmFyIGhhc2gkMSA9IHtcbiAgICBsZWZ0OiBcInJpZ2h0XCIsXG4gICAgcmlnaHQ6IFwibGVmdFwiLFxuICAgIGJvdHRvbTogXCJ0b3BcIixcbiAgICB0b3A6IFwiYm90dG9tXCJcbiAgfTtcbiAgZnVuY3Rpb24gZ2V0T3Bwb3NpdGVQbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gICAgcmV0dXJuIHBsYWNlbWVudC5yZXBsYWNlKC9sZWZ0fHJpZ2h0fGJvdHRvbXx0b3AvZywgZnVuY3Rpb24obWF0Y2hlZCkge1xuICAgICAgcmV0dXJuIGhhc2gkMVttYXRjaGVkXTtcbiAgICB9KTtcbiAgfVxuICB2YXIgaGFzaCA9IHtcbiAgICBzdGFydDogXCJlbmRcIixcbiAgICBlbmQ6IFwic3RhcnRcIlxuICB9O1xuICBmdW5jdGlvbiBnZXRPcHBvc2l0ZVZhcmlhdGlvblBsYWNlbWVudChwbGFjZW1lbnQpIHtcbiAgICByZXR1cm4gcGxhY2VtZW50LnJlcGxhY2UoL3N0YXJ0fGVuZC9nLCBmdW5jdGlvbihtYXRjaGVkKSB7XG4gICAgICByZXR1cm4gaGFzaFttYXRjaGVkXTtcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBjb21wdXRlQXV0b1BsYWNlbWVudChzdGF0ZSwgb3B0aW9ucykge1xuICAgIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICAgIG9wdGlvbnMgPSB7fTtcbiAgICB9XG4gICAgdmFyIF9vcHRpb25zID0gb3B0aW9ucywgcGxhY2VtZW50ID0gX29wdGlvbnMucGxhY2VtZW50LCBib3VuZGFyeSA9IF9vcHRpb25zLmJvdW5kYXJ5LCByb290Qm91bmRhcnkgPSBfb3B0aW9ucy5yb290Qm91bmRhcnksIHBhZGRpbmcgPSBfb3B0aW9ucy5wYWRkaW5nLCBmbGlwVmFyaWF0aW9ucyA9IF9vcHRpb25zLmZsaXBWYXJpYXRpb25zLCBfb3B0aW9ucyRhbGxvd2VkQXV0b1AgPSBfb3B0aW9ucy5hbGxvd2VkQXV0b1BsYWNlbWVudHMsIGFsbG93ZWRBdXRvUGxhY2VtZW50cyA9IF9vcHRpb25zJGFsbG93ZWRBdXRvUCA9PT0gdm9pZCAwID8gcGxhY2VtZW50cyA6IF9vcHRpb25zJGFsbG93ZWRBdXRvUDtcbiAgICB2YXIgdmFyaWF0aW9uID0gZ2V0VmFyaWF0aW9uKHBsYWNlbWVudCk7XG4gICAgdmFyIHBsYWNlbWVudHMkMSA9IHZhcmlhdGlvbiA/IGZsaXBWYXJpYXRpb25zID8gdmFyaWF0aW9uUGxhY2VtZW50cyA6IHZhcmlhdGlvblBsYWNlbWVudHMuZmlsdGVyKGZ1bmN0aW9uKHBsYWNlbWVudDIpIHtcbiAgICAgIHJldHVybiBnZXRWYXJpYXRpb24ocGxhY2VtZW50MikgPT09IHZhcmlhdGlvbjtcbiAgICB9KSA6IGJhc2VQbGFjZW1lbnRzO1xuICAgIHZhciBhbGxvd2VkUGxhY2VtZW50cyA9IHBsYWNlbWVudHMkMS5maWx0ZXIoZnVuY3Rpb24ocGxhY2VtZW50Mikge1xuICAgICAgcmV0dXJuIGFsbG93ZWRBdXRvUGxhY2VtZW50cy5pbmRleE9mKHBsYWNlbWVudDIpID49IDA7XG4gICAgfSk7XG4gICAgaWYgKGFsbG93ZWRQbGFjZW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgYWxsb3dlZFBsYWNlbWVudHMgPSBwbGFjZW1lbnRzJDE7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICBjb25zb2xlLmVycm9yKFtcIlBvcHBlcjogVGhlIGBhbGxvd2VkQXV0b1BsYWNlbWVudHNgIG9wdGlvbiBkaWQgbm90IGFsbG93IGFueVwiLCBcInBsYWNlbWVudHMuIEVuc3VyZSB0aGUgYHBsYWNlbWVudGAgb3B0aW9uIG1hdGNoZXMgdGhlIHZhcmlhdGlvblwiLCBcIm9mIHRoZSBhbGxvd2VkIHBsYWNlbWVudHMuXCIsICdGb3IgZXhhbXBsZSwgXCJhdXRvXCIgY2Fubm90IGJlIHVzZWQgdG8gYWxsb3cgXCJib3R0b20tc3RhcnRcIi4nLCAnVXNlIFwiYXV0by1zdGFydFwiIGluc3RlYWQuJ10uam9pbihcIiBcIikpO1xuICAgICAgfVxuICAgIH1cbiAgICB2YXIgb3ZlcmZsb3dzID0gYWxsb3dlZFBsYWNlbWVudHMucmVkdWNlKGZ1bmN0aW9uKGFjYywgcGxhY2VtZW50Mikge1xuICAgICAgYWNjW3BsYWNlbWVudDJdID0gZGV0ZWN0T3ZlcmZsb3coc3RhdGUsIHtcbiAgICAgICAgcGxhY2VtZW50OiBwbGFjZW1lbnQyLFxuICAgICAgICBib3VuZGFyeSxcbiAgICAgICAgcm9vdEJvdW5kYXJ5LFxuICAgICAgICBwYWRkaW5nXG4gICAgICB9KVtnZXRCYXNlUGxhY2VtZW50KHBsYWNlbWVudDIpXTtcbiAgICAgIHJldHVybiBhY2M7XG4gICAgfSwge30pO1xuICAgIHJldHVybiBPYmplY3Qua2V5cyhvdmVyZmxvd3MpLnNvcnQoZnVuY3Rpb24oYSwgYikge1xuICAgICAgcmV0dXJuIG92ZXJmbG93c1thXSAtIG92ZXJmbG93c1tiXTtcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBnZXRFeHBhbmRlZEZhbGxiYWNrUGxhY2VtZW50cyhwbGFjZW1lbnQpIHtcbiAgICBpZiAoZ2V0QmFzZVBsYWNlbWVudChwbGFjZW1lbnQpID09PSBhdXRvKSB7XG4gICAgICByZXR1cm4gW107XG4gICAgfVxuICAgIHZhciBvcHBvc2l0ZVBsYWNlbWVudCA9IGdldE9wcG9zaXRlUGxhY2VtZW50KHBsYWNlbWVudCk7XG4gICAgcmV0dXJuIFtnZXRPcHBvc2l0ZVZhcmlhdGlvblBsYWNlbWVudChwbGFjZW1lbnQpLCBvcHBvc2l0ZVBsYWNlbWVudCwgZ2V0T3Bwb3NpdGVWYXJpYXRpb25QbGFjZW1lbnQob3Bwb3NpdGVQbGFjZW1lbnQpXTtcbiAgfVxuICBmdW5jdGlvbiBmbGlwKF9yZWYpIHtcbiAgICB2YXIgc3RhdGUgPSBfcmVmLnN0YXRlLCBvcHRpb25zID0gX3JlZi5vcHRpb25zLCBuYW1lID0gX3JlZi5uYW1lO1xuICAgIGlmIChzdGF0ZS5tb2RpZmllcnNEYXRhW25hbWVdLl9za2lwKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHZhciBfb3B0aW9ucyRtYWluQXhpcyA9IG9wdGlvbnMubWFpbkF4aXMsIGNoZWNrTWFpbkF4aXMgPSBfb3B0aW9ucyRtYWluQXhpcyA9PT0gdm9pZCAwID8gdHJ1ZSA6IF9vcHRpb25zJG1haW5BeGlzLCBfb3B0aW9ucyRhbHRBeGlzID0gb3B0aW9ucy5hbHRBeGlzLCBjaGVja0FsdEF4aXMgPSBfb3B0aW9ucyRhbHRBeGlzID09PSB2b2lkIDAgPyB0cnVlIDogX29wdGlvbnMkYWx0QXhpcywgc3BlY2lmaWVkRmFsbGJhY2tQbGFjZW1lbnRzID0gb3B0aW9ucy5mYWxsYmFja1BsYWNlbWVudHMsIHBhZGRpbmcgPSBvcHRpb25zLnBhZGRpbmcsIGJvdW5kYXJ5ID0gb3B0aW9ucy5ib3VuZGFyeSwgcm9vdEJvdW5kYXJ5ID0gb3B0aW9ucy5yb290Qm91bmRhcnksIGFsdEJvdW5kYXJ5ID0gb3B0aW9ucy5hbHRCb3VuZGFyeSwgX29wdGlvbnMkZmxpcFZhcmlhdGlvID0gb3B0aW9ucy5mbGlwVmFyaWF0aW9ucywgZmxpcFZhcmlhdGlvbnMgPSBfb3B0aW9ucyRmbGlwVmFyaWF0aW8gPT09IHZvaWQgMCA/IHRydWUgOiBfb3B0aW9ucyRmbGlwVmFyaWF0aW8sIGFsbG93ZWRBdXRvUGxhY2VtZW50cyA9IG9wdGlvbnMuYWxsb3dlZEF1dG9QbGFjZW1lbnRzO1xuICAgIHZhciBwcmVmZXJyZWRQbGFjZW1lbnQgPSBzdGF0ZS5vcHRpb25zLnBsYWNlbWVudDtcbiAgICB2YXIgYmFzZVBsYWNlbWVudCA9IGdldEJhc2VQbGFjZW1lbnQocHJlZmVycmVkUGxhY2VtZW50KTtcbiAgICB2YXIgaXNCYXNlUGxhY2VtZW50ID0gYmFzZVBsYWNlbWVudCA9PT0gcHJlZmVycmVkUGxhY2VtZW50O1xuICAgIHZhciBmYWxsYmFja1BsYWNlbWVudHMgPSBzcGVjaWZpZWRGYWxsYmFja1BsYWNlbWVudHMgfHwgKGlzQmFzZVBsYWNlbWVudCB8fCAhZmxpcFZhcmlhdGlvbnMgPyBbZ2V0T3Bwb3NpdGVQbGFjZW1lbnQocHJlZmVycmVkUGxhY2VtZW50KV0gOiBnZXRFeHBhbmRlZEZhbGxiYWNrUGxhY2VtZW50cyhwcmVmZXJyZWRQbGFjZW1lbnQpKTtcbiAgICB2YXIgcGxhY2VtZW50czIgPSBbcHJlZmVycmVkUGxhY2VtZW50XS5jb25jYXQoZmFsbGJhY2tQbGFjZW1lbnRzKS5yZWR1Y2UoZnVuY3Rpb24oYWNjLCBwbGFjZW1lbnQyKSB7XG4gICAgICByZXR1cm4gYWNjLmNvbmNhdChnZXRCYXNlUGxhY2VtZW50KHBsYWNlbWVudDIpID09PSBhdXRvID8gY29tcHV0ZUF1dG9QbGFjZW1lbnQoc3RhdGUsIHtcbiAgICAgICAgcGxhY2VtZW50OiBwbGFjZW1lbnQyLFxuICAgICAgICBib3VuZGFyeSxcbiAgICAgICAgcm9vdEJvdW5kYXJ5LFxuICAgICAgICBwYWRkaW5nLFxuICAgICAgICBmbGlwVmFyaWF0aW9ucyxcbiAgICAgICAgYWxsb3dlZEF1dG9QbGFjZW1lbnRzXG4gICAgICB9KSA6IHBsYWNlbWVudDIpO1xuICAgIH0sIFtdKTtcbiAgICB2YXIgcmVmZXJlbmNlUmVjdCA9IHN0YXRlLnJlY3RzLnJlZmVyZW5jZTtcbiAgICB2YXIgcG9wcGVyUmVjdCA9IHN0YXRlLnJlY3RzLnBvcHBlcjtcbiAgICB2YXIgY2hlY2tzTWFwID0gbmV3IE1hcCgpO1xuICAgIHZhciBtYWtlRmFsbGJhY2tDaGVja3MgPSB0cnVlO1xuICAgIHZhciBmaXJzdEZpdHRpbmdQbGFjZW1lbnQgPSBwbGFjZW1lbnRzMlswXTtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHBsYWNlbWVudHMyLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgcGxhY2VtZW50ID0gcGxhY2VtZW50czJbaV07XG4gICAgICB2YXIgX2Jhc2VQbGFjZW1lbnQgPSBnZXRCYXNlUGxhY2VtZW50KHBsYWNlbWVudCk7XG4gICAgICB2YXIgaXNTdGFydFZhcmlhdGlvbiA9IGdldFZhcmlhdGlvbihwbGFjZW1lbnQpID09PSBzdGFydDtcbiAgICAgIHZhciBpc1ZlcnRpY2FsID0gW3RvcCwgYm90dG9tXS5pbmRleE9mKF9iYXNlUGxhY2VtZW50KSA+PSAwO1xuICAgICAgdmFyIGxlbiA9IGlzVmVydGljYWwgPyBcIndpZHRoXCIgOiBcImhlaWdodFwiO1xuICAgICAgdmFyIG92ZXJmbG93ID0gZGV0ZWN0T3ZlcmZsb3coc3RhdGUsIHtcbiAgICAgICAgcGxhY2VtZW50LFxuICAgICAgICBib3VuZGFyeSxcbiAgICAgICAgcm9vdEJvdW5kYXJ5LFxuICAgICAgICBhbHRCb3VuZGFyeSxcbiAgICAgICAgcGFkZGluZ1xuICAgICAgfSk7XG4gICAgICB2YXIgbWFpblZhcmlhdGlvblNpZGUgPSBpc1ZlcnRpY2FsID8gaXNTdGFydFZhcmlhdGlvbiA/IHJpZ2h0IDogbGVmdCA6IGlzU3RhcnRWYXJpYXRpb24gPyBib3R0b20gOiB0b3A7XG4gICAgICBpZiAocmVmZXJlbmNlUmVjdFtsZW5dID4gcG9wcGVyUmVjdFtsZW5dKSB7XG4gICAgICAgIG1haW5WYXJpYXRpb25TaWRlID0gZ2V0T3Bwb3NpdGVQbGFjZW1lbnQobWFpblZhcmlhdGlvblNpZGUpO1xuICAgICAgfVxuICAgICAgdmFyIGFsdFZhcmlhdGlvblNpZGUgPSBnZXRPcHBvc2l0ZVBsYWNlbWVudChtYWluVmFyaWF0aW9uU2lkZSk7XG4gICAgICB2YXIgY2hlY2tzID0gW107XG4gICAgICBpZiAoY2hlY2tNYWluQXhpcykge1xuICAgICAgICBjaGVja3MucHVzaChvdmVyZmxvd1tfYmFzZVBsYWNlbWVudF0gPD0gMCk7XG4gICAgICB9XG4gICAgICBpZiAoY2hlY2tBbHRBeGlzKSB7XG4gICAgICAgIGNoZWNrcy5wdXNoKG92ZXJmbG93W21haW5WYXJpYXRpb25TaWRlXSA8PSAwLCBvdmVyZmxvd1thbHRWYXJpYXRpb25TaWRlXSA8PSAwKTtcbiAgICAgIH1cbiAgICAgIGlmIChjaGVja3MuZXZlcnkoZnVuY3Rpb24oY2hlY2spIHtcbiAgICAgICAgcmV0dXJuIGNoZWNrO1xuICAgICAgfSkpIHtcbiAgICAgICAgZmlyc3RGaXR0aW5nUGxhY2VtZW50ID0gcGxhY2VtZW50O1xuICAgICAgICBtYWtlRmFsbGJhY2tDaGVja3MgPSBmYWxzZTtcbiAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgICBjaGVja3NNYXAuc2V0KHBsYWNlbWVudCwgY2hlY2tzKTtcbiAgICB9XG4gICAgaWYgKG1ha2VGYWxsYmFja0NoZWNrcykge1xuICAgICAgdmFyIG51bWJlck9mQ2hlY2tzID0gZmxpcFZhcmlhdGlvbnMgPyAzIDogMTtcbiAgICAgIHZhciBfbG9vcCA9IGZ1bmN0aW9uIF9sb29wMihfaTIpIHtcbiAgICAgICAgdmFyIGZpdHRpbmdQbGFjZW1lbnQgPSBwbGFjZW1lbnRzMi5maW5kKGZ1bmN0aW9uKHBsYWNlbWVudDIpIHtcbiAgICAgICAgICB2YXIgY2hlY2tzMiA9IGNoZWNrc01hcC5nZXQocGxhY2VtZW50Mik7XG4gICAgICAgICAgaWYgKGNoZWNrczIpIHtcbiAgICAgICAgICAgIHJldHVybiBjaGVja3MyLnNsaWNlKDAsIF9pMikuZXZlcnkoZnVuY3Rpb24oY2hlY2spIHtcbiAgICAgICAgICAgICAgcmV0dXJuIGNoZWNrO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgICAgaWYgKGZpdHRpbmdQbGFjZW1lbnQpIHtcbiAgICAgICAgICBmaXJzdEZpdHRpbmdQbGFjZW1lbnQgPSBmaXR0aW5nUGxhY2VtZW50O1xuICAgICAgICAgIHJldHVybiBcImJyZWFrXCI7XG4gICAgICAgIH1cbiAgICAgIH07XG4gICAgICBmb3IgKHZhciBfaSA9IG51bWJlck9mQ2hlY2tzOyBfaSA+IDA7IF9pLS0pIHtcbiAgICAgICAgdmFyIF9yZXQgPSBfbG9vcChfaSk7XG4gICAgICAgIGlmIChfcmV0ID09PSBcImJyZWFrXCIpXG4gICAgICAgICAgYnJlYWs7XG4gICAgICB9XG4gICAgfVxuICAgIGlmIChzdGF0ZS5wbGFjZW1lbnQgIT09IGZpcnN0Rml0dGluZ1BsYWNlbWVudCkge1xuICAgICAgc3RhdGUubW9kaWZpZXJzRGF0YVtuYW1lXS5fc2tpcCA9IHRydWU7XG4gICAgICBzdGF0ZS5wbGFjZW1lbnQgPSBmaXJzdEZpdHRpbmdQbGFjZW1lbnQ7XG4gICAgICBzdGF0ZS5yZXNldCA9IHRydWU7XG4gICAgfVxuICB9XG4gIHZhciBmbGlwJDEgPSB7XG4gICAgbmFtZTogXCJmbGlwXCIsXG4gICAgZW5hYmxlZDogdHJ1ZSxcbiAgICBwaGFzZTogXCJtYWluXCIsXG4gICAgZm46IGZsaXAsXG4gICAgcmVxdWlyZXNJZkV4aXN0czogW1wib2Zmc2V0XCJdLFxuICAgIGRhdGE6IHtcbiAgICAgIF9za2lwOiBmYWxzZVxuICAgIH1cbiAgfTtcbiAgZnVuY3Rpb24gZ2V0QWx0QXhpcyhheGlzKSB7XG4gICAgcmV0dXJuIGF4aXMgPT09IFwieFwiID8gXCJ5XCIgOiBcInhcIjtcbiAgfVxuICBmdW5jdGlvbiB3aXRoaW4obWluJDEsIHZhbHVlLCBtYXgkMSkge1xuICAgIHJldHVybiBtYXgobWluJDEsIG1pbih2YWx1ZSwgbWF4JDEpKTtcbiAgfVxuICBmdW5jdGlvbiBwcmV2ZW50T3ZlcmZsb3coX3JlZikge1xuICAgIHZhciBzdGF0ZSA9IF9yZWYuc3RhdGUsIG9wdGlvbnMgPSBfcmVmLm9wdGlvbnMsIG5hbWUgPSBfcmVmLm5hbWU7XG4gICAgdmFyIF9vcHRpb25zJG1haW5BeGlzID0gb3B0aW9ucy5tYWluQXhpcywgY2hlY2tNYWluQXhpcyA9IF9vcHRpb25zJG1haW5BeGlzID09PSB2b2lkIDAgPyB0cnVlIDogX29wdGlvbnMkbWFpbkF4aXMsIF9vcHRpb25zJGFsdEF4aXMgPSBvcHRpb25zLmFsdEF4aXMsIGNoZWNrQWx0QXhpcyA9IF9vcHRpb25zJGFsdEF4aXMgPT09IHZvaWQgMCA/IGZhbHNlIDogX29wdGlvbnMkYWx0QXhpcywgYm91bmRhcnkgPSBvcHRpb25zLmJvdW5kYXJ5LCByb290Qm91bmRhcnkgPSBvcHRpb25zLnJvb3RCb3VuZGFyeSwgYWx0Qm91bmRhcnkgPSBvcHRpb25zLmFsdEJvdW5kYXJ5LCBwYWRkaW5nID0gb3B0aW9ucy5wYWRkaW5nLCBfb3B0aW9ucyR0ZXRoZXIgPSBvcHRpb25zLnRldGhlciwgdGV0aGVyID0gX29wdGlvbnMkdGV0aGVyID09PSB2b2lkIDAgPyB0cnVlIDogX29wdGlvbnMkdGV0aGVyLCBfb3B0aW9ucyR0ZXRoZXJPZmZzZXQgPSBvcHRpb25zLnRldGhlck9mZnNldCwgdGV0aGVyT2Zmc2V0ID0gX29wdGlvbnMkdGV0aGVyT2Zmc2V0ID09PSB2b2lkIDAgPyAwIDogX29wdGlvbnMkdGV0aGVyT2Zmc2V0O1xuICAgIHZhciBvdmVyZmxvdyA9IGRldGVjdE92ZXJmbG93KHN0YXRlLCB7XG4gICAgICBib3VuZGFyeSxcbiAgICAgIHJvb3RCb3VuZGFyeSxcbiAgICAgIHBhZGRpbmcsXG4gICAgICBhbHRCb3VuZGFyeVxuICAgIH0pO1xuICAgIHZhciBiYXNlUGxhY2VtZW50ID0gZ2V0QmFzZVBsYWNlbWVudChzdGF0ZS5wbGFjZW1lbnQpO1xuICAgIHZhciB2YXJpYXRpb24gPSBnZXRWYXJpYXRpb24oc3RhdGUucGxhY2VtZW50KTtcbiAgICB2YXIgaXNCYXNlUGxhY2VtZW50ID0gIXZhcmlhdGlvbjtcbiAgICB2YXIgbWFpbkF4aXMgPSBnZXRNYWluQXhpc0Zyb21QbGFjZW1lbnQoYmFzZVBsYWNlbWVudCk7XG4gICAgdmFyIGFsdEF4aXMgPSBnZXRBbHRBeGlzKG1haW5BeGlzKTtcbiAgICB2YXIgcG9wcGVyT2Zmc2V0czIgPSBzdGF0ZS5tb2RpZmllcnNEYXRhLnBvcHBlck9mZnNldHM7XG4gICAgdmFyIHJlZmVyZW5jZVJlY3QgPSBzdGF0ZS5yZWN0cy5yZWZlcmVuY2U7XG4gICAgdmFyIHBvcHBlclJlY3QgPSBzdGF0ZS5yZWN0cy5wb3BwZXI7XG4gICAgdmFyIHRldGhlck9mZnNldFZhbHVlID0gdHlwZW9mIHRldGhlck9mZnNldCA9PT0gXCJmdW5jdGlvblwiID8gdGV0aGVyT2Zmc2V0KE9iamVjdC5hc3NpZ24oe30sIHN0YXRlLnJlY3RzLCB7XG4gICAgICBwbGFjZW1lbnQ6IHN0YXRlLnBsYWNlbWVudFxuICAgIH0pKSA6IHRldGhlck9mZnNldDtcbiAgICB2YXIgZGF0YSA9IHtcbiAgICAgIHg6IDAsXG4gICAgICB5OiAwXG4gICAgfTtcbiAgICBpZiAoIXBvcHBlck9mZnNldHMyKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGlmIChjaGVja01haW5BeGlzIHx8IGNoZWNrQWx0QXhpcykge1xuICAgICAgdmFyIG1haW5TaWRlID0gbWFpbkF4aXMgPT09IFwieVwiID8gdG9wIDogbGVmdDtcbiAgICAgIHZhciBhbHRTaWRlID0gbWFpbkF4aXMgPT09IFwieVwiID8gYm90dG9tIDogcmlnaHQ7XG4gICAgICB2YXIgbGVuID0gbWFpbkF4aXMgPT09IFwieVwiID8gXCJoZWlnaHRcIiA6IFwid2lkdGhcIjtcbiAgICAgIHZhciBvZmZzZXQyID0gcG9wcGVyT2Zmc2V0czJbbWFpbkF4aXNdO1xuICAgICAgdmFyIG1pbiQxID0gcG9wcGVyT2Zmc2V0czJbbWFpbkF4aXNdICsgb3ZlcmZsb3dbbWFpblNpZGVdO1xuICAgICAgdmFyIG1heCQxID0gcG9wcGVyT2Zmc2V0czJbbWFpbkF4aXNdIC0gb3ZlcmZsb3dbYWx0U2lkZV07XG4gICAgICB2YXIgYWRkaXRpdmUgPSB0ZXRoZXIgPyAtcG9wcGVyUmVjdFtsZW5dIC8gMiA6IDA7XG4gICAgICB2YXIgbWluTGVuID0gdmFyaWF0aW9uID09PSBzdGFydCA/IHJlZmVyZW5jZVJlY3RbbGVuXSA6IHBvcHBlclJlY3RbbGVuXTtcbiAgICAgIHZhciBtYXhMZW4gPSB2YXJpYXRpb24gPT09IHN0YXJ0ID8gLXBvcHBlclJlY3RbbGVuXSA6IC1yZWZlcmVuY2VSZWN0W2xlbl07XG4gICAgICB2YXIgYXJyb3dFbGVtZW50ID0gc3RhdGUuZWxlbWVudHMuYXJyb3c7XG4gICAgICB2YXIgYXJyb3dSZWN0ID0gdGV0aGVyICYmIGFycm93RWxlbWVudCA/IGdldExheW91dFJlY3QoYXJyb3dFbGVtZW50KSA6IHtcbiAgICAgICAgd2lkdGg6IDAsXG4gICAgICAgIGhlaWdodDogMFxuICAgICAgfTtcbiAgICAgIHZhciBhcnJvd1BhZGRpbmdPYmplY3QgPSBzdGF0ZS5tb2RpZmllcnNEYXRhW1wiYXJyb3cjcGVyc2lzdGVudFwiXSA/IHN0YXRlLm1vZGlmaWVyc0RhdGFbXCJhcnJvdyNwZXJzaXN0ZW50XCJdLnBhZGRpbmcgOiBnZXRGcmVzaFNpZGVPYmplY3QoKTtcbiAgICAgIHZhciBhcnJvd1BhZGRpbmdNaW4gPSBhcnJvd1BhZGRpbmdPYmplY3RbbWFpblNpZGVdO1xuICAgICAgdmFyIGFycm93UGFkZGluZ01heCA9IGFycm93UGFkZGluZ09iamVjdFthbHRTaWRlXTtcbiAgICAgIHZhciBhcnJvd0xlbiA9IHdpdGhpbigwLCByZWZlcmVuY2VSZWN0W2xlbl0sIGFycm93UmVjdFtsZW5dKTtcbiAgICAgIHZhciBtaW5PZmZzZXQgPSBpc0Jhc2VQbGFjZW1lbnQgPyByZWZlcmVuY2VSZWN0W2xlbl0gLyAyIC0gYWRkaXRpdmUgLSBhcnJvd0xlbiAtIGFycm93UGFkZGluZ01pbiAtIHRldGhlck9mZnNldFZhbHVlIDogbWluTGVuIC0gYXJyb3dMZW4gLSBhcnJvd1BhZGRpbmdNaW4gLSB0ZXRoZXJPZmZzZXRWYWx1ZTtcbiAgICAgIHZhciBtYXhPZmZzZXQgPSBpc0Jhc2VQbGFjZW1lbnQgPyAtcmVmZXJlbmNlUmVjdFtsZW5dIC8gMiArIGFkZGl0aXZlICsgYXJyb3dMZW4gKyBhcnJvd1BhZGRpbmdNYXggKyB0ZXRoZXJPZmZzZXRWYWx1ZSA6IG1heExlbiArIGFycm93TGVuICsgYXJyb3dQYWRkaW5nTWF4ICsgdGV0aGVyT2Zmc2V0VmFsdWU7XG4gICAgICB2YXIgYXJyb3dPZmZzZXRQYXJlbnQgPSBzdGF0ZS5lbGVtZW50cy5hcnJvdyAmJiBnZXRPZmZzZXRQYXJlbnQoc3RhdGUuZWxlbWVudHMuYXJyb3cpO1xuICAgICAgdmFyIGNsaWVudE9mZnNldCA9IGFycm93T2Zmc2V0UGFyZW50ID8gbWFpbkF4aXMgPT09IFwieVwiID8gYXJyb3dPZmZzZXRQYXJlbnQuY2xpZW50VG9wIHx8IDAgOiBhcnJvd09mZnNldFBhcmVudC5jbGllbnRMZWZ0IHx8IDAgOiAwO1xuICAgICAgdmFyIG9mZnNldE1vZGlmaWVyVmFsdWUgPSBzdGF0ZS5tb2RpZmllcnNEYXRhLm9mZnNldCA/IHN0YXRlLm1vZGlmaWVyc0RhdGEub2Zmc2V0W3N0YXRlLnBsYWNlbWVudF1bbWFpbkF4aXNdIDogMDtcbiAgICAgIHZhciB0ZXRoZXJNaW4gPSBwb3BwZXJPZmZzZXRzMlttYWluQXhpc10gKyBtaW5PZmZzZXQgLSBvZmZzZXRNb2RpZmllclZhbHVlIC0gY2xpZW50T2Zmc2V0O1xuICAgICAgdmFyIHRldGhlck1heCA9IHBvcHBlck9mZnNldHMyW21haW5BeGlzXSArIG1heE9mZnNldCAtIG9mZnNldE1vZGlmaWVyVmFsdWU7XG4gICAgICBpZiAoY2hlY2tNYWluQXhpcykge1xuICAgICAgICB2YXIgcHJldmVudGVkT2Zmc2V0ID0gd2l0aGluKHRldGhlciA/IG1pbihtaW4kMSwgdGV0aGVyTWluKSA6IG1pbiQxLCBvZmZzZXQyLCB0ZXRoZXIgPyBtYXgobWF4JDEsIHRldGhlck1heCkgOiBtYXgkMSk7XG4gICAgICAgIHBvcHBlck9mZnNldHMyW21haW5BeGlzXSA9IHByZXZlbnRlZE9mZnNldDtcbiAgICAgICAgZGF0YVttYWluQXhpc10gPSBwcmV2ZW50ZWRPZmZzZXQgLSBvZmZzZXQyO1xuICAgICAgfVxuICAgICAgaWYgKGNoZWNrQWx0QXhpcykge1xuICAgICAgICB2YXIgX21haW5TaWRlID0gbWFpbkF4aXMgPT09IFwieFwiID8gdG9wIDogbGVmdDtcbiAgICAgICAgdmFyIF9hbHRTaWRlID0gbWFpbkF4aXMgPT09IFwieFwiID8gYm90dG9tIDogcmlnaHQ7XG4gICAgICAgIHZhciBfb2Zmc2V0ID0gcG9wcGVyT2Zmc2V0czJbYWx0QXhpc107XG4gICAgICAgIHZhciBfbWluID0gX29mZnNldCArIG92ZXJmbG93W19tYWluU2lkZV07XG4gICAgICAgIHZhciBfbWF4ID0gX29mZnNldCAtIG92ZXJmbG93W19hbHRTaWRlXTtcbiAgICAgICAgdmFyIF9wcmV2ZW50ZWRPZmZzZXQgPSB3aXRoaW4odGV0aGVyID8gbWluKF9taW4sIHRldGhlck1pbikgOiBfbWluLCBfb2Zmc2V0LCB0ZXRoZXIgPyBtYXgoX21heCwgdGV0aGVyTWF4KSA6IF9tYXgpO1xuICAgICAgICBwb3BwZXJPZmZzZXRzMlthbHRBeGlzXSA9IF9wcmV2ZW50ZWRPZmZzZXQ7XG4gICAgICAgIGRhdGFbYWx0QXhpc10gPSBfcHJldmVudGVkT2Zmc2V0IC0gX29mZnNldDtcbiAgICAgIH1cbiAgICB9XG4gICAgc3RhdGUubW9kaWZpZXJzRGF0YVtuYW1lXSA9IGRhdGE7XG4gIH1cbiAgdmFyIHByZXZlbnRPdmVyZmxvdyQxID0ge1xuICAgIG5hbWU6IFwicHJldmVudE92ZXJmbG93XCIsXG4gICAgZW5hYmxlZDogdHJ1ZSxcbiAgICBwaGFzZTogXCJtYWluXCIsXG4gICAgZm46IHByZXZlbnRPdmVyZmxvdyxcbiAgICByZXF1aXJlc0lmRXhpc3RzOiBbXCJvZmZzZXRcIl1cbiAgfTtcbiAgdmFyIHRvUGFkZGluZ09iamVjdCA9IGZ1bmN0aW9uIHRvUGFkZGluZ09iamVjdDIocGFkZGluZywgc3RhdGUpIHtcbiAgICBwYWRkaW5nID0gdHlwZW9mIHBhZGRpbmcgPT09IFwiZnVuY3Rpb25cIiA/IHBhZGRpbmcoT2JqZWN0LmFzc2lnbih7fSwgc3RhdGUucmVjdHMsIHtcbiAgICAgIHBsYWNlbWVudDogc3RhdGUucGxhY2VtZW50XG4gICAgfSkpIDogcGFkZGluZztcbiAgICByZXR1cm4gbWVyZ2VQYWRkaW5nT2JqZWN0KHR5cGVvZiBwYWRkaW5nICE9PSBcIm51bWJlclwiID8gcGFkZGluZyA6IGV4cGFuZFRvSGFzaE1hcChwYWRkaW5nLCBiYXNlUGxhY2VtZW50cykpO1xuICB9O1xuICBmdW5jdGlvbiBhcnJvdyhfcmVmKSB7XG4gICAgdmFyIF9zdGF0ZSRtb2RpZmllcnNEYXRhJDtcbiAgICB2YXIgc3RhdGUgPSBfcmVmLnN0YXRlLCBuYW1lID0gX3JlZi5uYW1lLCBvcHRpb25zID0gX3JlZi5vcHRpb25zO1xuICAgIHZhciBhcnJvd0VsZW1lbnQgPSBzdGF0ZS5lbGVtZW50cy5hcnJvdztcbiAgICB2YXIgcG9wcGVyT2Zmc2V0czIgPSBzdGF0ZS5tb2RpZmllcnNEYXRhLnBvcHBlck9mZnNldHM7XG4gICAgdmFyIGJhc2VQbGFjZW1lbnQgPSBnZXRCYXNlUGxhY2VtZW50KHN0YXRlLnBsYWNlbWVudCk7XG4gICAgdmFyIGF4aXMgPSBnZXRNYWluQXhpc0Zyb21QbGFjZW1lbnQoYmFzZVBsYWNlbWVudCk7XG4gICAgdmFyIGlzVmVydGljYWwgPSBbbGVmdCwgcmlnaHRdLmluZGV4T2YoYmFzZVBsYWNlbWVudCkgPj0gMDtcbiAgICB2YXIgbGVuID0gaXNWZXJ0aWNhbCA/IFwiaGVpZ2h0XCIgOiBcIndpZHRoXCI7XG4gICAgaWYgKCFhcnJvd0VsZW1lbnQgfHwgIXBvcHBlck9mZnNldHMyKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHZhciBwYWRkaW5nT2JqZWN0ID0gdG9QYWRkaW5nT2JqZWN0KG9wdGlvbnMucGFkZGluZywgc3RhdGUpO1xuICAgIHZhciBhcnJvd1JlY3QgPSBnZXRMYXlvdXRSZWN0KGFycm93RWxlbWVudCk7XG4gICAgdmFyIG1pblByb3AgPSBheGlzID09PSBcInlcIiA/IHRvcCA6IGxlZnQ7XG4gICAgdmFyIG1heFByb3AgPSBheGlzID09PSBcInlcIiA/IGJvdHRvbSA6IHJpZ2h0O1xuICAgIHZhciBlbmREaWZmID0gc3RhdGUucmVjdHMucmVmZXJlbmNlW2xlbl0gKyBzdGF0ZS5yZWN0cy5yZWZlcmVuY2VbYXhpc10gLSBwb3BwZXJPZmZzZXRzMltheGlzXSAtIHN0YXRlLnJlY3RzLnBvcHBlcltsZW5dO1xuICAgIHZhciBzdGFydERpZmYgPSBwb3BwZXJPZmZzZXRzMltheGlzXSAtIHN0YXRlLnJlY3RzLnJlZmVyZW5jZVtheGlzXTtcbiAgICB2YXIgYXJyb3dPZmZzZXRQYXJlbnQgPSBnZXRPZmZzZXRQYXJlbnQoYXJyb3dFbGVtZW50KTtcbiAgICB2YXIgY2xpZW50U2l6ZSA9IGFycm93T2Zmc2V0UGFyZW50ID8gYXhpcyA9PT0gXCJ5XCIgPyBhcnJvd09mZnNldFBhcmVudC5jbGllbnRIZWlnaHQgfHwgMCA6IGFycm93T2Zmc2V0UGFyZW50LmNsaWVudFdpZHRoIHx8IDAgOiAwO1xuICAgIHZhciBjZW50ZXJUb1JlZmVyZW5jZSA9IGVuZERpZmYgLyAyIC0gc3RhcnREaWZmIC8gMjtcbiAgICB2YXIgbWluMiA9IHBhZGRpbmdPYmplY3RbbWluUHJvcF07XG4gICAgdmFyIG1heDIgPSBjbGllbnRTaXplIC0gYXJyb3dSZWN0W2xlbl0gLSBwYWRkaW5nT2JqZWN0W21heFByb3BdO1xuICAgIHZhciBjZW50ZXIgPSBjbGllbnRTaXplIC8gMiAtIGFycm93UmVjdFtsZW5dIC8gMiArIGNlbnRlclRvUmVmZXJlbmNlO1xuICAgIHZhciBvZmZzZXQyID0gd2l0aGluKG1pbjIsIGNlbnRlciwgbWF4Mik7XG4gICAgdmFyIGF4aXNQcm9wID0gYXhpcztcbiAgICBzdGF0ZS5tb2RpZmllcnNEYXRhW25hbWVdID0gKF9zdGF0ZSRtb2RpZmllcnNEYXRhJCA9IHt9LCBfc3RhdGUkbW9kaWZpZXJzRGF0YSRbYXhpc1Byb3BdID0gb2Zmc2V0MiwgX3N0YXRlJG1vZGlmaWVyc0RhdGEkLmNlbnRlck9mZnNldCA9IG9mZnNldDIgLSBjZW50ZXIsIF9zdGF0ZSRtb2RpZmllcnNEYXRhJCk7XG4gIH1cbiAgZnVuY3Rpb24gZWZmZWN0KF9yZWYyKSB7XG4gICAgdmFyIHN0YXRlID0gX3JlZjIuc3RhdGUsIG9wdGlvbnMgPSBfcmVmMi5vcHRpb25zO1xuICAgIHZhciBfb3B0aW9ucyRlbGVtZW50ID0gb3B0aW9ucy5lbGVtZW50LCBhcnJvd0VsZW1lbnQgPSBfb3B0aW9ucyRlbGVtZW50ID09PSB2b2lkIDAgPyBcIltkYXRhLXBvcHBlci1hcnJvd11cIiA6IF9vcHRpb25zJGVsZW1lbnQ7XG4gICAgaWYgKGFycm93RWxlbWVudCA9PSBudWxsKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGlmICh0eXBlb2YgYXJyb3dFbGVtZW50ID09PSBcInN0cmluZ1wiKSB7XG4gICAgICBhcnJvd0VsZW1lbnQgPSBzdGF0ZS5lbGVtZW50cy5wb3BwZXIucXVlcnlTZWxlY3RvcihhcnJvd0VsZW1lbnQpO1xuICAgICAgaWYgKCFhcnJvd0VsZW1lbnQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgIH1cbiAgICBpZiAodHJ1ZSkge1xuICAgICAgaWYgKCFpc0hUTUxFbGVtZW50KGFycm93RWxlbWVudCkpIHtcbiAgICAgICAgY29uc29sZS5lcnJvcihbJ1BvcHBlcjogXCJhcnJvd1wiIGVsZW1lbnQgbXVzdCBiZSBhbiBIVE1MRWxlbWVudCAobm90IGFuIFNWR0VsZW1lbnQpLicsIFwiVG8gdXNlIGFuIFNWRyBhcnJvdywgd3JhcCBpdCBpbiBhbiBIVE1MRWxlbWVudCB0aGF0IHdpbGwgYmUgdXNlZCBhc1wiLCBcInRoZSBhcnJvdy5cIl0uam9pbihcIiBcIikpO1xuICAgICAgfVxuICAgIH1cbiAgICBpZiAoIWNvbnRhaW5zKHN0YXRlLmVsZW1lbnRzLnBvcHBlciwgYXJyb3dFbGVtZW50KSkge1xuICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgY29uc29sZS5lcnJvcihbJ1BvcHBlcjogXCJhcnJvd1wiIG1vZGlmaWVyXFwncyBgZWxlbWVudGAgbXVzdCBiZSBhIGNoaWxkIG9mIHRoZSBwb3BwZXInLCBcImVsZW1lbnQuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICAgIH1cbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgc3RhdGUuZWxlbWVudHMuYXJyb3cgPSBhcnJvd0VsZW1lbnQ7XG4gIH1cbiAgdmFyIGFycm93JDEgPSB7XG4gICAgbmFtZTogXCJhcnJvd1wiLFxuICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgcGhhc2U6IFwibWFpblwiLFxuICAgIGZuOiBhcnJvdyxcbiAgICBlZmZlY3QsXG4gICAgcmVxdWlyZXM6IFtcInBvcHBlck9mZnNldHNcIl0sXG4gICAgcmVxdWlyZXNJZkV4aXN0czogW1wicHJldmVudE92ZXJmbG93XCJdXG4gIH07XG4gIGZ1bmN0aW9uIGdldFNpZGVPZmZzZXRzKG92ZXJmbG93LCByZWN0LCBwcmV2ZW50ZWRPZmZzZXRzKSB7XG4gICAgaWYgKHByZXZlbnRlZE9mZnNldHMgPT09IHZvaWQgMCkge1xuICAgICAgcHJldmVudGVkT2Zmc2V0cyA9IHtcbiAgICAgICAgeDogMCxcbiAgICAgICAgeTogMFxuICAgICAgfTtcbiAgICB9XG4gICAgcmV0dXJuIHtcbiAgICAgIHRvcDogb3ZlcmZsb3cudG9wIC0gcmVjdC5oZWlnaHQgLSBwcmV2ZW50ZWRPZmZzZXRzLnksXG4gICAgICByaWdodDogb3ZlcmZsb3cucmlnaHQgLSByZWN0LndpZHRoICsgcHJldmVudGVkT2Zmc2V0cy54LFxuICAgICAgYm90dG9tOiBvdmVyZmxvdy5ib3R0b20gLSByZWN0LmhlaWdodCArIHByZXZlbnRlZE9mZnNldHMueSxcbiAgICAgIGxlZnQ6IG92ZXJmbG93LmxlZnQgLSByZWN0LndpZHRoIC0gcHJldmVudGVkT2Zmc2V0cy54XG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiBpc0FueVNpZGVGdWxseUNsaXBwZWQob3ZlcmZsb3cpIHtcbiAgICByZXR1cm4gW3RvcCwgcmlnaHQsIGJvdHRvbSwgbGVmdF0uc29tZShmdW5jdGlvbihzaWRlKSB7XG4gICAgICByZXR1cm4gb3ZlcmZsb3dbc2lkZV0gPj0gMDtcbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBoaWRlKF9yZWYpIHtcbiAgICB2YXIgc3RhdGUgPSBfcmVmLnN0YXRlLCBuYW1lID0gX3JlZi5uYW1lO1xuICAgIHZhciByZWZlcmVuY2VSZWN0ID0gc3RhdGUucmVjdHMucmVmZXJlbmNlO1xuICAgIHZhciBwb3BwZXJSZWN0ID0gc3RhdGUucmVjdHMucG9wcGVyO1xuICAgIHZhciBwcmV2ZW50ZWRPZmZzZXRzID0gc3RhdGUubW9kaWZpZXJzRGF0YS5wcmV2ZW50T3ZlcmZsb3c7XG4gICAgdmFyIHJlZmVyZW5jZU92ZXJmbG93ID0gZGV0ZWN0T3ZlcmZsb3coc3RhdGUsIHtcbiAgICAgIGVsZW1lbnRDb250ZXh0OiBcInJlZmVyZW5jZVwiXG4gICAgfSk7XG4gICAgdmFyIHBvcHBlckFsdE92ZXJmbG93ID0gZGV0ZWN0T3ZlcmZsb3coc3RhdGUsIHtcbiAgICAgIGFsdEJvdW5kYXJ5OiB0cnVlXG4gICAgfSk7XG4gICAgdmFyIHJlZmVyZW5jZUNsaXBwaW5nT2Zmc2V0cyA9IGdldFNpZGVPZmZzZXRzKHJlZmVyZW5jZU92ZXJmbG93LCByZWZlcmVuY2VSZWN0KTtcbiAgICB2YXIgcG9wcGVyRXNjYXBlT2Zmc2V0cyA9IGdldFNpZGVPZmZzZXRzKHBvcHBlckFsdE92ZXJmbG93LCBwb3BwZXJSZWN0LCBwcmV2ZW50ZWRPZmZzZXRzKTtcbiAgICB2YXIgaXNSZWZlcmVuY2VIaWRkZW4gPSBpc0FueVNpZGVGdWxseUNsaXBwZWQocmVmZXJlbmNlQ2xpcHBpbmdPZmZzZXRzKTtcbiAgICB2YXIgaGFzUG9wcGVyRXNjYXBlZCA9IGlzQW55U2lkZUZ1bGx5Q2xpcHBlZChwb3BwZXJFc2NhcGVPZmZzZXRzKTtcbiAgICBzdGF0ZS5tb2RpZmllcnNEYXRhW25hbWVdID0ge1xuICAgICAgcmVmZXJlbmNlQ2xpcHBpbmdPZmZzZXRzLFxuICAgICAgcG9wcGVyRXNjYXBlT2Zmc2V0cyxcbiAgICAgIGlzUmVmZXJlbmNlSGlkZGVuLFxuICAgICAgaGFzUG9wcGVyRXNjYXBlZFxuICAgIH07XG4gICAgc3RhdGUuYXR0cmlidXRlcy5wb3BwZXIgPSBPYmplY3QuYXNzaWduKHt9LCBzdGF0ZS5hdHRyaWJ1dGVzLnBvcHBlciwge1xuICAgICAgXCJkYXRhLXBvcHBlci1yZWZlcmVuY2UtaGlkZGVuXCI6IGlzUmVmZXJlbmNlSGlkZGVuLFxuICAgICAgXCJkYXRhLXBvcHBlci1lc2NhcGVkXCI6IGhhc1BvcHBlckVzY2FwZWRcbiAgICB9KTtcbiAgfVxuICB2YXIgaGlkZSQxID0ge1xuICAgIG5hbWU6IFwiaGlkZVwiLFxuICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgcGhhc2U6IFwibWFpblwiLFxuICAgIHJlcXVpcmVzSWZFeGlzdHM6IFtcInByZXZlbnRPdmVyZmxvd1wiXSxcbiAgICBmbjogaGlkZVxuICB9O1xuICB2YXIgZGVmYXVsdE1vZGlmaWVycyQxID0gW2V2ZW50TGlzdGVuZXJzLCBwb3BwZXJPZmZzZXRzJDEsIGNvbXB1dGVTdHlsZXMkMSwgYXBwbHlTdHlsZXMkMV07XG4gIHZhciBjcmVhdGVQb3BwZXIkMSA9IC8qIEBfX1BVUkVfXyAqLyBwb3BwZXJHZW5lcmF0b3Ioe1xuICAgIGRlZmF1bHRNb2RpZmllcnM6IGRlZmF1bHRNb2RpZmllcnMkMVxuICB9KTtcbiAgdmFyIGRlZmF1bHRNb2RpZmllcnMgPSBbZXZlbnRMaXN0ZW5lcnMsIHBvcHBlck9mZnNldHMkMSwgY29tcHV0ZVN0eWxlcyQxLCBhcHBseVN0eWxlcyQxLCBvZmZzZXQkMSwgZmxpcCQxLCBwcmV2ZW50T3ZlcmZsb3ckMSwgYXJyb3ckMSwgaGlkZSQxXTtcbiAgdmFyIGNyZWF0ZVBvcHBlciA9IC8qIEBfX1BVUkVfXyAqLyBwb3BwZXJHZW5lcmF0b3Ioe1xuICAgIGRlZmF1bHRNb2RpZmllcnNcbiAgfSk7XG4gIGV4cG9ydHMuYXBwbHlTdHlsZXMgPSBhcHBseVN0eWxlcyQxO1xuICBleHBvcnRzLmFycm93ID0gYXJyb3ckMTtcbiAgZXhwb3J0cy5jb21wdXRlU3R5bGVzID0gY29tcHV0ZVN0eWxlcyQxO1xuICBleHBvcnRzLmNyZWF0ZVBvcHBlciA9IGNyZWF0ZVBvcHBlcjtcbiAgZXhwb3J0cy5jcmVhdGVQb3BwZXJMaXRlID0gY3JlYXRlUG9wcGVyJDE7XG4gIGV4cG9ydHMuZGVmYXVsdE1vZGlmaWVycyA9IGRlZmF1bHRNb2RpZmllcnM7XG4gIGV4cG9ydHMuZGV0ZWN0T3ZlcmZsb3cgPSBkZXRlY3RPdmVyZmxvdztcbiAgZXhwb3J0cy5ldmVudExpc3RlbmVycyA9IGV2ZW50TGlzdGVuZXJzO1xuICBleHBvcnRzLmZsaXAgPSBmbGlwJDE7XG4gIGV4cG9ydHMuaGlkZSA9IGhpZGUkMTtcbiAgZXhwb3J0cy5vZmZzZXQgPSBvZmZzZXQkMTtcbiAgZXhwb3J0cy5wb3BwZXJHZW5lcmF0b3IgPSBwb3BwZXJHZW5lcmF0b3I7XG4gIGV4cG9ydHMucG9wcGVyT2Zmc2V0cyA9IHBvcHBlck9mZnNldHMkMTtcbiAgZXhwb3J0cy5wcmV2ZW50T3ZlcmZsb3cgPSBwcmV2ZW50T3ZlcmZsb3ckMTtcbn0pO1xuXG4vLyBub2RlX21vZHVsZXMvdGlwcHkuanMvZGlzdC90aXBweS5janMuanNcbnZhciByZXF1aXJlX3RpcHB5X2NqcyA9IF9fY29tbW9uSlMoKGV4cG9ydHMpID0+IHtcbiAgXCJ1c2Ugc3RyaWN0XCI7XG4gIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwge3ZhbHVlOiB0cnVlfSk7XG4gIHZhciBjb3JlID0gcmVxdWlyZV9wb3BwZXIoKTtcbiAgdmFyIFJPVU5EX0FSUk9XID0gJzxzdmcgd2lkdGg9XCIxNlwiIGhlaWdodD1cIjZcIiB4bWxucz1cImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnXCI+PHBhdGggZD1cIk0wIDZzMS43OTYtLjAxMyA0LjY3LTMuNjE1QzUuODUxLjkgNi45My4wMDYgOCAwYzEuMDctLjAwNiAyLjE0OC44ODcgMy4zNDMgMi4zODVDMTQuMjMzIDYuMDA1IDE2IDYgMTYgNkgwelwiPjwvc3ZnPic7XG4gIHZhciBCT1hfQ0xBU1MgPSBcInRpcHB5LWJveFwiO1xuICB2YXIgQ09OVEVOVF9DTEFTUyA9IFwidGlwcHktY29udGVudFwiO1xuICB2YXIgQkFDS0RST1BfQ0xBU1MgPSBcInRpcHB5LWJhY2tkcm9wXCI7XG4gIHZhciBBUlJPV19DTEFTUyA9IFwidGlwcHktYXJyb3dcIjtcbiAgdmFyIFNWR19BUlJPV19DTEFTUyA9IFwidGlwcHktc3ZnLWFycm93XCI7XG4gIHZhciBUT1VDSF9PUFRJT05TID0ge1xuICAgIHBhc3NpdmU6IHRydWUsXG4gICAgY2FwdHVyZTogdHJ1ZVxuICB9O1xuICBmdW5jdGlvbiBoYXNPd25Qcm9wZXJ0eShvYmosIGtleSkge1xuICAgIHJldHVybiB7fS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwga2V5KTtcbiAgfVxuICBmdW5jdGlvbiBnZXRWYWx1ZUF0SW5kZXhPclJldHVybih2YWx1ZSwgaW5kZXgsIGRlZmF1bHRWYWx1ZSkge1xuICAgIGlmIChBcnJheS5pc0FycmF5KHZhbHVlKSkge1xuICAgICAgdmFyIHYgPSB2YWx1ZVtpbmRleF07XG4gICAgICByZXR1cm4gdiA9PSBudWxsID8gQXJyYXkuaXNBcnJheShkZWZhdWx0VmFsdWUpID8gZGVmYXVsdFZhbHVlW2luZGV4XSA6IGRlZmF1bHRWYWx1ZSA6IHY7XG4gICAgfVxuICAgIHJldHVybiB2YWx1ZTtcbiAgfVxuICBmdW5jdGlvbiBpc1R5cGUodmFsdWUsIHR5cGUpIHtcbiAgICB2YXIgc3RyID0ge30udG9TdHJpbmcuY2FsbCh2YWx1ZSk7XG4gICAgcmV0dXJuIHN0ci5pbmRleE9mKFwiW29iamVjdFwiKSA9PT0gMCAmJiBzdHIuaW5kZXhPZih0eXBlICsgXCJdXCIpID4gLTE7XG4gIH1cbiAgZnVuY3Rpb24gaW52b2tlV2l0aEFyZ3NPclJldHVybih2YWx1ZSwgYXJncykge1xuICAgIHJldHVybiB0eXBlb2YgdmFsdWUgPT09IFwiZnVuY3Rpb25cIiA/IHZhbHVlLmFwcGx5KHZvaWQgMCwgYXJncykgOiB2YWx1ZTtcbiAgfVxuICBmdW5jdGlvbiBkZWJvdW5jZShmbiwgbXMpIHtcbiAgICBpZiAobXMgPT09IDApIHtcbiAgICAgIHJldHVybiBmbjtcbiAgICB9XG4gICAgdmFyIHRpbWVvdXQ7XG4gICAgcmV0dXJuIGZ1bmN0aW9uKGFyZykge1xuICAgICAgY2xlYXJUaW1lb3V0KHRpbWVvdXQpO1xuICAgICAgdGltZW91dCA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgIGZuKGFyZyk7XG4gICAgICB9LCBtcyk7XG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiByZW1vdmVQcm9wZXJ0aWVzKG9iaiwga2V5cykge1xuICAgIHZhciBjbG9uZSA9IE9iamVjdC5hc3NpZ24oe30sIG9iaik7XG4gICAga2V5cy5mb3JFYWNoKGZ1bmN0aW9uKGtleSkge1xuICAgICAgZGVsZXRlIGNsb25lW2tleV07XG4gICAgfSk7XG4gICAgcmV0dXJuIGNsb25lO1xuICB9XG4gIGZ1bmN0aW9uIHNwbGl0QnlTcGFjZXModmFsdWUpIHtcbiAgICByZXR1cm4gdmFsdWUuc3BsaXQoL1xccysvKS5maWx0ZXIoQm9vbGVhbik7XG4gIH1cbiAgZnVuY3Rpb24gbm9ybWFsaXplVG9BcnJheSh2YWx1ZSkge1xuICAgIHJldHVybiBbXS5jb25jYXQodmFsdWUpO1xuICB9XG4gIGZ1bmN0aW9uIHB1c2hJZlVuaXF1ZShhcnIsIHZhbHVlKSB7XG4gICAgaWYgKGFyci5pbmRleE9mKHZhbHVlKSA9PT0gLTEpIHtcbiAgICAgIGFyci5wdXNoKHZhbHVlKTtcbiAgICB9XG4gIH1cbiAgZnVuY3Rpb24gdW5pcXVlKGFycikge1xuICAgIHJldHVybiBhcnIuZmlsdGVyKGZ1bmN0aW9uKGl0ZW0sIGluZGV4KSB7XG4gICAgICByZXR1cm4gYXJyLmluZGV4T2YoaXRlbSkgPT09IGluZGV4O1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIGdldEJhc2VQbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gICAgcmV0dXJuIHBsYWNlbWVudC5zcGxpdChcIi1cIilbMF07XG4gIH1cbiAgZnVuY3Rpb24gYXJyYXlGcm9tKHZhbHVlKSB7XG4gICAgcmV0dXJuIFtdLnNsaWNlLmNhbGwodmFsdWUpO1xuICB9XG4gIGZ1bmN0aW9uIHJlbW92ZVVuZGVmaW5lZFByb3BzKG9iaikge1xuICAgIHJldHVybiBPYmplY3Qua2V5cyhvYmopLnJlZHVjZShmdW5jdGlvbihhY2MsIGtleSkge1xuICAgICAgaWYgKG9ialtrZXldICE9PSB2b2lkIDApIHtcbiAgICAgICAgYWNjW2tleV0gPSBvYmpba2V5XTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBhY2M7XG4gICAgfSwge30pO1xuICB9XG4gIGZ1bmN0aW9uIGRpdigpIHtcbiAgICByZXR1cm4gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcImRpdlwiKTtcbiAgfVxuICBmdW5jdGlvbiBpc0VsZW1lbnQodmFsdWUpIHtcbiAgICByZXR1cm4gW1wiRWxlbWVudFwiLCBcIkZyYWdtZW50XCJdLnNvbWUoZnVuY3Rpb24odHlwZSkge1xuICAgICAgcmV0dXJuIGlzVHlwZSh2YWx1ZSwgdHlwZSk7XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gaXNOb2RlTGlzdCh2YWx1ZSkge1xuICAgIHJldHVybiBpc1R5cGUodmFsdWUsIFwiTm9kZUxpc3RcIik7XG4gIH1cbiAgZnVuY3Rpb24gaXNNb3VzZUV2ZW50KHZhbHVlKSB7XG4gICAgcmV0dXJuIGlzVHlwZSh2YWx1ZSwgXCJNb3VzZUV2ZW50XCIpO1xuICB9XG4gIGZ1bmN0aW9uIGlzUmVmZXJlbmNlRWxlbWVudCh2YWx1ZSkge1xuICAgIHJldHVybiAhISh2YWx1ZSAmJiB2YWx1ZS5fdGlwcHkgJiYgdmFsdWUuX3RpcHB5LnJlZmVyZW5jZSA9PT0gdmFsdWUpO1xuICB9XG4gIGZ1bmN0aW9uIGdldEFycmF5T2ZFbGVtZW50cyh2YWx1ZSkge1xuICAgIGlmIChpc0VsZW1lbnQodmFsdWUpKSB7XG4gICAgICByZXR1cm4gW3ZhbHVlXTtcbiAgICB9XG4gICAgaWYgKGlzTm9kZUxpc3QodmFsdWUpKSB7XG4gICAgICByZXR1cm4gYXJyYXlGcm9tKHZhbHVlKTtcbiAgICB9XG4gICAgaWYgKEFycmF5LmlzQXJyYXkodmFsdWUpKSB7XG4gICAgICByZXR1cm4gdmFsdWU7XG4gICAgfVxuICAgIHJldHVybiBhcnJheUZyb20oZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCh2YWx1ZSkpO1xuICB9XG4gIGZ1bmN0aW9uIHNldFRyYW5zaXRpb25EdXJhdGlvbihlbHMsIHZhbHVlKSB7XG4gICAgZWxzLmZvckVhY2goZnVuY3Rpb24oZWwpIHtcbiAgICAgIGlmIChlbCkge1xuICAgICAgICBlbC5zdHlsZS50cmFuc2l0aW9uRHVyYXRpb24gPSB2YWx1ZSArIFwibXNcIjtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuICBmdW5jdGlvbiBzZXRWaXNpYmlsaXR5U3RhdGUoZWxzLCBzdGF0ZSkge1xuICAgIGVscy5mb3JFYWNoKGZ1bmN0aW9uKGVsKSB7XG4gICAgICBpZiAoZWwpIHtcbiAgICAgICAgZWwuc2V0QXR0cmlidXRlKFwiZGF0YS1zdGF0ZVwiLCBzdGF0ZSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cbiAgZnVuY3Rpb24gZ2V0T3duZXJEb2N1bWVudChlbGVtZW50T3JFbGVtZW50cykge1xuICAgIHZhciBfZWxlbWVudCRvd25lckRvY3VtZW47XG4gICAgdmFyIF9ub3JtYWxpemVUb0FycmF5ID0gbm9ybWFsaXplVG9BcnJheShlbGVtZW50T3JFbGVtZW50cyksIGVsZW1lbnQgPSBfbm9ybWFsaXplVG9BcnJheVswXTtcbiAgICByZXR1cm4gKGVsZW1lbnQgPT0gbnVsbCA/IHZvaWQgMCA6IChfZWxlbWVudCRvd25lckRvY3VtZW4gPSBlbGVtZW50Lm93bmVyRG9jdW1lbnQpID09IG51bGwgPyB2b2lkIDAgOiBfZWxlbWVudCRvd25lckRvY3VtZW4uYm9keSkgPyBlbGVtZW50Lm93bmVyRG9jdW1lbnQgOiBkb2N1bWVudDtcbiAgfVxuICBmdW5jdGlvbiBpc0N1cnNvck91dHNpZGVJbnRlcmFjdGl2ZUJvcmRlcihwb3BwZXJUcmVlRGF0YSwgZXZlbnQpIHtcbiAgICB2YXIgY2xpZW50WCA9IGV2ZW50LmNsaWVudFgsIGNsaWVudFkgPSBldmVudC5jbGllbnRZO1xuICAgIHJldHVybiBwb3BwZXJUcmVlRGF0YS5ldmVyeShmdW5jdGlvbihfcmVmKSB7XG4gICAgICB2YXIgcG9wcGVyUmVjdCA9IF9yZWYucG9wcGVyUmVjdCwgcG9wcGVyU3RhdGUgPSBfcmVmLnBvcHBlclN0YXRlLCBwcm9wcyA9IF9yZWYucHJvcHM7XG4gICAgICB2YXIgaW50ZXJhY3RpdmVCb3JkZXIgPSBwcm9wcy5pbnRlcmFjdGl2ZUJvcmRlcjtcbiAgICAgIHZhciBiYXNlUGxhY2VtZW50ID0gZ2V0QmFzZVBsYWNlbWVudChwb3BwZXJTdGF0ZS5wbGFjZW1lbnQpO1xuICAgICAgdmFyIG9mZnNldERhdGEgPSBwb3BwZXJTdGF0ZS5tb2RpZmllcnNEYXRhLm9mZnNldDtcbiAgICAgIGlmICghb2Zmc2V0RGF0YSkge1xuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgIH1cbiAgICAgIHZhciB0b3BEaXN0YW5jZSA9IGJhc2VQbGFjZW1lbnQgPT09IFwiYm90dG9tXCIgPyBvZmZzZXREYXRhLnRvcC55IDogMDtcbiAgICAgIHZhciBib3R0b21EaXN0YW5jZSA9IGJhc2VQbGFjZW1lbnQgPT09IFwidG9wXCIgPyBvZmZzZXREYXRhLmJvdHRvbS55IDogMDtcbiAgICAgIHZhciBsZWZ0RGlzdGFuY2UgPSBiYXNlUGxhY2VtZW50ID09PSBcInJpZ2h0XCIgPyBvZmZzZXREYXRhLmxlZnQueCA6IDA7XG4gICAgICB2YXIgcmlnaHREaXN0YW5jZSA9IGJhc2VQbGFjZW1lbnQgPT09IFwibGVmdFwiID8gb2Zmc2V0RGF0YS5yaWdodC54IDogMDtcbiAgICAgIHZhciBleGNlZWRzVG9wID0gcG9wcGVyUmVjdC50b3AgLSBjbGllbnRZICsgdG9wRGlzdGFuY2UgPiBpbnRlcmFjdGl2ZUJvcmRlcjtcbiAgICAgIHZhciBleGNlZWRzQm90dG9tID0gY2xpZW50WSAtIHBvcHBlclJlY3QuYm90dG9tIC0gYm90dG9tRGlzdGFuY2UgPiBpbnRlcmFjdGl2ZUJvcmRlcjtcbiAgICAgIHZhciBleGNlZWRzTGVmdCA9IHBvcHBlclJlY3QubGVmdCAtIGNsaWVudFggKyBsZWZ0RGlzdGFuY2UgPiBpbnRlcmFjdGl2ZUJvcmRlcjtcbiAgICAgIHZhciBleGNlZWRzUmlnaHQgPSBjbGllbnRYIC0gcG9wcGVyUmVjdC5yaWdodCAtIHJpZ2h0RGlzdGFuY2UgPiBpbnRlcmFjdGl2ZUJvcmRlcjtcbiAgICAgIHJldHVybiBleGNlZWRzVG9wIHx8IGV4Y2VlZHNCb3R0b20gfHwgZXhjZWVkc0xlZnQgfHwgZXhjZWVkc1JpZ2h0O1xuICAgIH0pO1xuICB9XG4gIGZ1bmN0aW9uIHVwZGF0ZVRyYW5zaXRpb25FbmRMaXN0ZW5lcihib3gsIGFjdGlvbiwgbGlzdGVuZXIpIHtcbiAgICB2YXIgbWV0aG9kID0gYWN0aW9uICsgXCJFdmVudExpc3RlbmVyXCI7XG4gICAgW1widHJhbnNpdGlvbmVuZFwiLCBcIndlYmtpdFRyYW5zaXRpb25FbmRcIl0uZm9yRWFjaChmdW5jdGlvbihldmVudCkge1xuICAgICAgYm94W21ldGhvZF0oZXZlbnQsIGxpc3RlbmVyKTtcbiAgICB9KTtcbiAgfVxuICB2YXIgY3VycmVudElucHV0ID0ge1xuICAgIGlzVG91Y2g6IGZhbHNlXG4gIH07XG4gIHZhciBsYXN0TW91c2VNb3ZlVGltZSA9IDA7XG4gIGZ1bmN0aW9uIG9uRG9jdW1lbnRUb3VjaFN0YXJ0KCkge1xuICAgIGlmIChjdXJyZW50SW5wdXQuaXNUb3VjaCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBjdXJyZW50SW5wdXQuaXNUb3VjaCA9IHRydWU7XG4gICAgaWYgKHdpbmRvdy5wZXJmb3JtYW5jZSkge1xuICAgICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihcIm1vdXNlbW92ZVwiLCBvbkRvY3VtZW50TW91c2VNb3ZlKTtcbiAgICB9XG4gIH1cbiAgZnVuY3Rpb24gb25Eb2N1bWVudE1vdXNlTW92ZSgpIHtcbiAgICB2YXIgbm93ID0gcGVyZm9ybWFuY2Uubm93KCk7XG4gICAgaWYgKG5vdyAtIGxhc3RNb3VzZU1vdmVUaW1lIDwgMjApIHtcbiAgICAgIGN1cnJlbnRJbnB1dC5pc1RvdWNoID0gZmFsc2U7XG4gICAgICBkb2N1bWVudC5yZW1vdmVFdmVudExpc3RlbmVyKFwibW91c2Vtb3ZlXCIsIG9uRG9jdW1lbnRNb3VzZU1vdmUpO1xuICAgIH1cbiAgICBsYXN0TW91c2VNb3ZlVGltZSA9IG5vdztcbiAgfVxuICBmdW5jdGlvbiBvbldpbmRvd0JsdXIoKSB7XG4gICAgdmFyIGFjdGl2ZUVsZW1lbnQgPSBkb2N1bWVudC5hY3RpdmVFbGVtZW50O1xuICAgIGlmIChpc1JlZmVyZW5jZUVsZW1lbnQoYWN0aXZlRWxlbWVudCkpIHtcbiAgICAgIHZhciBpbnN0YW5jZSA9IGFjdGl2ZUVsZW1lbnQuX3RpcHB5O1xuICAgICAgaWYgKGFjdGl2ZUVsZW1lbnQuYmx1ciAmJiAhaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlKSB7XG4gICAgICAgIGFjdGl2ZUVsZW1lbnQuYmx1cigpO1xuICAgICAgfVxuICAgIH1cbiAgfVxuICBmdW5jdGlvbiBiaW5kR2xvYmFsRXZlbnRMaXN0ZW5lcnMoKSB7XG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihcInRvdWNoc3RhcnRcIiwgb25Eb2N1bWVudFRvdWNoU3RhcnQsIFRPVUNIX09QVElPTlMpO1xuICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwiYmx1clwiLCBvbldpbmRvd0JsdXIpO1xuICB9XG4gIHZhciBpc0Jyb3dzZXIgPSB0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiICYmIHR5cGVvZiBkb2N1bWVudCAhPT0gXCJ1bmRlZmluZWRcIjtcbiAgdmFyIHVhID0gaXNCcm93c2VyID8gbmF2aWdhdG9yLnVzZXJBZ2VudCA6IFwiXCI7XG4gIHZhciBpc0lFID0gL01TSUUgfFRyaWRlbnRcXC8vLnRlc3QodWEpO1xuICBmdW5jdGlvbiBjcmVhdGVNZW1vcnlMZWFrV2FybmluZyhtZXRob2QpIHtcbiAgICB2YXIgdHh0ID0gbWV0aG9kID09PSBcImRlc3Ryb3lcIiA/IFwibiBhbHJlYWR5LVwiIDogXCIgXCI7XG4gICAgcmV0dXJuIFttZXRob2QgKyBcIigpIHdhcyBjYWxsZWQgb24gYVwiICsgdHh0ICsgXCJkZXN0cm95ZWQgaW5zdGFuY2UuIFRoaXMgaXMgYSBuby1vcCBidXRcIiwgXCJpbmRpY2F0ZXMgYSBwb3RlbnRpYWwgbWVtb3J5IGxlYWsuXCJdLmpvaW4oXCIgXCIpO1xuICB9XG4gIGZ1bmN0aW9uIGNsZWFuKHZhbHVlKSB7XG4gICAgdmFyIHNwYWNlc0FuZFRhYnMgPSAvWyBcXHRdezIsfS9nO1xuICAgIHZhciBsaW5lU3RhcnRXaXRoU3BhY2VzID0gL15bIFxcdF0qL2dtO1xuICAgIHJldHVybiB2YWx1ZS5yZXBsYWNlKHNwYWNlc0FuZFRhYnMsIFwiIFwiKS5yZXBsYWNlKGxpbmVTdGFydFdpdGhTcGFjZXMsIFwiXCIpLnRyaW0oKTtcbiAgfVxuICBmdW5jdGlvbiBnZXREZXZNZXNzYWdlKG1lc3NhZ2UpIHtcbiAgICByZXR1cm4gY2xlYW4oXCJcXG4gICVjdGlwcHkuanNcXG5cXG4gICVjXCIgKyBjbGVhbihtZXNzYWdlKSArIFwiXFxuXFxuICAlY1xcdXsxRjQ3N31cXHUyMDBEIFRoaXMgaXMgYSBkZXZlbG9wbWVudC1vbmx5IG1lc3NhZ2UuIEl0IHdpbGwgYmUgcmVtb3ZlZCBpbiBwcm9kdWN0aW9uLlxcbiAgXCIpO1xuICB9XG4gIGZ1bmN0aW9uIGdldEZvcm1hdHRlZE1lc3NhZ2UobWVzc2FnZSkge1xuICAgIHJldHVybiBbXG4gICAgICBnZXREZXZNZXNzYWdlKG1lc3NhZ2UpLFxuICAgICAgXCJjb2xvcjogIzAwQzU4NDsgZm9udC1zaXplOiAxLjNlbTsgZm9udC13ZWlnaHQ6IGJvbGQ7XCIsXG4gICAgICBcImxpbmUtaGVpZ2h0OiAxLjVcIixcbiAgICAgIFwiY29sb3I6ICNhNmEwOTU7XCJcbiAgICBdO1xuICB9XG4gIHZhciB2aXNpdGVkTWVzc2FnZXM7XG4gIGlmICh0cnVlKSB7XG4gICAgcmVzZXRWaXNpdGVkTWVzc2FnZXMoKTtcbiAgfVxuICBmdW5jdGlvbiByZXNldFZpc2l0ZWRNZXNzYWdlcygpIHtcbiAgICB2aXNpdGVkTWVzc2FnZXMgPSBuZXcgU2V0KCk7XG4gIH1cbiAgZnVuY3Rpb24gd2FybldoZW4oY29uZGl0aW9uLCBtZXNzYWdlKSB7XG4gICAgaWYgKGNvbmRpdGlvbiAmJiAhdmlzaXRlZE1lc3NhZ2VzLmhhcyhtZXNzYWdlKSkge1xuICAgICAgdmFyIF9jb25zb2xlO1xuICAgICAgdmlzaXRlZE1lc3NhZ2VzLmFkZChtZXNzYWdlKTtcbiAgICAgIChfY29uc29sZSA9IGNvbnNvbGUpLndhcm4uYXBwbHkoX2NvbnNvbGUsIGdldEZvcm1hdHRlZE1lc3NhZ2UobWVzc2FnZSkpO1xuICAgIH1cbiAgfVxuICBmdW5jdGlvbiBlcnJvcldoZW4oY29uZGl0aW9uLCBtZXNzYWdlKSB7XG4gICAgaWYgKGNvbmRpdGlvbiAmJiAhdmlzaXRlZE1lc3NhZ2VzLmhhcyhtZXNzYWdlKSkge1xuICAgICAgdmFyIF9jb25zb2xlMjtcbiAgICAgIHZpc2l0ZWRNZXNzYWdlcy5hZGQobWVzc2FnZSk7XG4gICAgICAoX2NvbnNvbGUyID0gY29uc29sZSkuZXJyb3IuYXBwbHkoX2NvbnNvbGUyLCBnZXRGb3JtYXR0ZWRNZXNzYWdlKG1lc3NhZ2UpKTtcbiAgICB9XG4gIH1cbiAgZnVuY3Rpb24gdmFsaWRhdGVUYXJnZXRzKHRhcmdldHMpIHtcbiAgICB2YXIgZGlkUGFzc0ZhbHN5VmFsdWUgPSAhdGFyZ2V0cztcbiAgICB2YXIgZGlkUGFzc1BsYWluT2JqZWN0ID0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKHRhcmdldHMpID09PSBcIltvYmplY3QgT2JqZWN0XVwiICYmICF0YXJnZXRzLmFkZEV2ZW50TGlzdGVuZXI7XG4gICAgZXJyb3JXaGVuKGRpZFBhc3NGYWxzeVZhbHVlLCBbXCJ0aXBweSgpIHdhcyBwYXNzZWRcIiwgXCJgXCIgKyBTdHJpbmcodGFyZ2V0cykgKyBcImBcIiwgXCJhcyBpdHMgdGFyZ2V0cyAoZmlyc3QpIGFyZ3VtZW50LiBWYWxpZCB0eXBlcyBhcmU6IFN0cmluZywgRWxlbWVudCxcIiwgXCJFbGVtZW50W10sIG9yIE5vZGVMaXN0LlwiXS5qb2luKFwiIFwiKSk7XG4gICAgZXJyb3JXaGVuKGRpZFBhc3NQbGFpbk9iamVjdCwgW1widGlwcHkoKSB3YXMgcGFzc2VkIGEgcGxhaW4gb2JqZWN0IHdoaWNoIGlzIG5vdCBzdXBwb3J0ZWQgYXMgYW4gYXJndW1lbnRcIiwgXCJmb3IgdmlydHVhbCBwb3NpdGlvbmluZy4gVXNlIHByb3BzLmdldFJlZmVyZW5jZUNsaWVudFJlY3QgaW5zdGVhZC5cIl0uam9pbihcIiBcIikpO1xuICB9XG4gIHZhciBwbHVnaW5Qcm9wcyA9IHtcbiAgICBhbmltYXRlRmlsbDogZmFsc2UsXG4gICAgZm9sbG93Q3Vyc29yOiBmYWxzZSxcbiAgICBpbmxpbmVQb3NpdGlvbmluZzogZmFsc2UsXG4gICAgc3RpY2t5OiBmYWxzZVxuICB9O1xuICB2YXIgcmVuZGVyUHJvcHMgPSB7XG4gICAgYWxsb3dIVE1MOiBmYWxzZSxcbiAgICBhbmltYXRpb246IFwiZmFkZVwiLFxuICAgIGFycm93OiB0cnVlLFxuICAgIGNvbnRlbnQ6IFwiXCIsXG4gICAgaW5lcnRpYTogZmFsc2UsXG4gICAgbWF4V2lkdGg6IDM1MCxcbiAgICByb2xlOiBcInRvb2x0aXBcIixcbiAgICB0aGVtZTogXCJcIixcbiAgICB6SW5kZXg6IDk5OTlcbiAgfTtcbiAgdmFyIGRlZmF1bHRQcm9wcyA9IE9iamVjdC5hc3NpZ24oe1xuICAgIGFwcGVuZFRvOiBmdW5jdGlvbiBhcHBlbmRUbygpIHtcbiAgICAgIHJldHVybiBkb2N1bWVudC5ib2R5O1xuICAgIH0sXG4gICAgYXJpYToge1xuICAgICAgY29udGVudDogXCJhdXRvXCIsXG4gICAgICBleHBhbmRlZDogXCJhdXRvXCJcbiAgICB9LFxuICAgIGRlbGF5OiAwLFxuICAgIGR1cmF0aW9uOiBbMzAwLCAyNTBdLFxuICAgIGdldFJlZmVyZW5jZUNsaWVudFJlY3Q6IG51bGwsXG4gICAgaGlkZU9uQ2xpY2s6IHRydWUsXG4gICAgaWdub3JlQXR0cmlidXRlczogZmFsc2UsXG4gICAgaW50ZXJhY3RpdmU6IGZhbHNlLFxuICAgIGludGVyYWN0aXZlQm9yZGVyOiAyLFxuICAgIGludGVyYWN0aXZlRGVib3VuY2U6IDAsXG4gICAgbW92ZVRyYW5zaXRpb246IFwiXCIsXG4gICAgb2Zmc2V0OiBbMCwgMTBdLFxuICAgIG9uQWZ0ZXJVcGRhdGU6IGZ1bmN0aW9uIG9uQWZ0ZXJVcGRhdGUoKSB7XG4gICAgfSxcbiAgICBvbkJlZm9yZVVwZGF0ZTogZnVuY3Rpb24gb25CZWZvcmVVcGRhdGUoKSB7XG4gICAgfSxcbiAgICBvbkNyZWF0ZTogZnVuY3Rpb24gb25DcmVhdGUoKSB7XG4gICAgfSxcbiAgICBvbkRlc3Ryb3k6IGZ1bmN0aW9uIG9uRGVzdHJveSgpIHtcbiAgICB9LFxuICAgIG9uSGlkZGVuOiBmdW5jdGlvbiBvbkhpZGRlbigpIHtcbiAgICB9LFxuICAgIG9uSGlkZTogZnVuY3Rpb24gb25IaWRlKCkge1xuICAgIH0sXG4gICAgb25Nb3VudDogZnVuY3Rpb24gb25Nb3VudCgpIHtcbiAgICB9LFxuICAgIG9uU2hvdzogZnVuY3Rpb24gb25TaG93KCkge1xuICAgIH0sXG4gICAgb25TaG93bjogZnVuY3Rpb24gb25TaG93bigpIHtcbiAgICB9LFxuICAgIG9uVHJpZ2dlcjogZnVuY3Rpb24gb25UcmlnZ2VyKCkge1xuICAgIH0sXG4gICAgb25VbnRyaWdnZXI6IGZ1bmN0aW9uIG9uVW50cmlnZ2VyKCkge1xuICAgIH0sXG4gICAgb25DbGlja091dHNpZGU6IGZ1bmN0aW9uIG9uQ2xpY2tPdXRzaWRlKCkge1xuICAgIH0sXG4gICAgcGxhY2VtZW50OiBcInRvcFwiLFxuICAgIHBsdWdpbnM6IFtdLFxuICAgIHBvcHBlck9wdGlvbnM6IHt9LFxuICAgIHJlbmRlcjogbnVsbCxcbiAgICBzaG93T25DcmVhdGU6IGZhbHNlLFxuICAgIHRvdWNoOiB0cnVlLFxuICAgIHRyaWdnZXI6IFwibW91c2VlbnRlciBmb2N1c1wiLFxuICAgIHRyaWdnZXJUYXJnZXQ6IG51bGxcbiAgfSwgcGx1Z2luUHJvcHMsIHt9LCByZW5kZXJQcm9wcyk7XG4gIHZhciBkZWZhdWx0S2V5cyA9IE9iamVjdC5rZXlzKGRlZmF1bHRQcm9wcyk7XG4gIHZhciBzZXREZWZhdWx0UHJvcHMgPSBmdW5jdGlvbiBzZXREZWZhdWx0UHJvcHMyKHBhcnRpYWxQcm9wcykge1xuICAgIGlmICh0cnVlKSB7XG4gICAgICB2YWxpZGF0ZVByb3BzKHBhcnRpYWxQcm9wcywgW10pO1xuICAgIH1cbiAgICB2YXIga2V5cyA9IE9iamVjdC5rZXlzKHBhcnRpYWxQcm9wcyk7XG4gICAga2V5cy5mb3JFYWNoKGZ1bmN0aW9uKGtleSkge1xuICAgICAgZGVmYXVsdFByb3BzW2tleV0gPSBwYXJ0aWFsUHJvcHNba2V5XTtcbiAgICB9KTtcbiAgfTtcbiAgZnVuY3Rpb24gZ2V0RXh0ZW5kZWRQYXNzZWRQcm9wcyhwYXNzZWRQcm9wcykge1xuICAgIHZhciBwbHVnaW5zID0gcGFzc2VkUHJvcHMucGx1Z2lucyB8fCBbXTtcbiAgICB2YXIgcGx1Z2luUHJvcHMyID0gcGx1Z2lucy5yZWR1Y2UoZnVuY3Rpb24oYWNjLCBwbHVnaW4pIHtcbiAgICAgIHZhciBuYW1lID0gcGx1Z2luLm5hbWUsIGRlZmF1bHRWYWx1ZSA9IHBsdWdpbi5kZWZhdWx0VmFsdWU7XG4gICAgICBpZiAobmFtZSkge1xuICAgICAgICBhY2NbbmFtZV0gPSBwYXNzZWRQcm9wc1tuYW1lXSAhPT0gdm9pZCAwID8gcGFzc2VkUHJvcHNbbmFtZV0gOiBkZWZhdWx0VmFsdWU7XG4gICAgICB9XG4gICAgICByZXR1cm4gYWNjO1xuICAgIH0sIHt9KTtcbiAgICByZXR1cm4gT2JqZWN0LmFzc2lnbih7fSwgcGFzc2VkUHJvcHMsIHt9LCBwbHVnaW5Qcm9wczIpO1xuICB9XG4gIGZ1bmN0aW9uIGdldERhdGFBdHRyaWJ1dGVQcm9wcyhyZWZlcmVuY2UsIHBsdWdpbnMpIHtcbiAgICB2YXIgcHJvcEtleXMgPSBwbHVnaW5zID8gT2JqZWN0LmtleXMoZ2V0RXh0ZW5kZWRQYXNzZWRQcm9wcyhPYmplY3QuYXNzaWduKHt9LCBkZWZhdWx0UHJvcHMsIHtcbiAgICAgIHBsdWdpbnNcbiAgICB9KSkpIDogZGVmYXVsdEtleXM7XG4gICAgdmFyIHByb3BzID0gcHJvcEtleXMucmVkdWNlKGZ1bmN0aW9uKGFjYywga2V5KSB7XG4gICAgICB2YXIgdmFsdWVBc1N0cmluZyA9IChyZWZlcmVuY2UuZ2V0QXR0cmlidXRlKFwiZGF0YS10aXBweS1cIiArIGtleSkgfHwgXCJcIikudHJpbSgpO1xuICAgICAgaWYgKCF2YWx1ZUFzU3RyaW5nKSB7XG4gICAgICAgIHJldHVybiBhY2M7XG4gICAgICB9XG4gICAgICBpZiAoa2V5ID09PSBcImNvbnRlbnRcIikge1xuICAgICAgICBhY2Nba2V5XSA9IHZhbHVlQXNTdHJpbmc7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0cnkge1xuICAgICAgICAgIGFjY1trZXldID0gSlNPTi5wYXJzZSh2YWx1ZUFzU3RyaW5nKTtcbiAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgIGFjY1trZXldID0gdmFsdWVBc1N0cmluZztcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgcmV0dXJuIGFjYztcbiAgICB9LCB7fSk7XG4gICAgcmV0dXJuIHByb3BzO1xuICB9XG4gIGZ1bmN0aW9uIGV2YWx1YXRlUHJvcHMocmVmZXJlbmNlLCBwcm9wcykge1xuICAgIHZhciBvdXQgPSBPYmplY3QuYXNzaWduKHt9LCBwcm9wcywge1xuICAgICAgY29udGVudDogaW52b2tlV2l0aEFyZ3NPclJldHVybihwcm9wcy5jb250ZW50LCBbcmVmZXJlbmNlXSlcbiAgICB9LCBwcm9wcy5pZ25vcmVBdHRyaWJ1dGVzID8ge30gOiBnZXREYXRhQXR0cmlidXRlUHJvcHMocmVmZXJlbmNlLCBwcm9wcy5wbHVnaW5zKSk7XG4gICAgb3V0LmFyaWEgPSBPYmplY3QuYXNzaWduKHt9LCBkZWZhdWx0UHJvcHMuYXJpYSwge30sIG91dC5hcmlhKTtcbiAgICBvdXQuYXJpYSA9IHtcbiAgICAgIGV4cGFuZGVkOiBvdXQuYXJpYS5leHBhbmRlZCA9PT0gXCJhdXRvXCIgPyBwcm9wcy5pbnRlcmFjdGl2ZSA6IG91dC5hcmlhLmV4cGFuZGVkLFxuICAgICAgY29udGVudDogb3V0LmFyaWEuY29udGVudCA9PT0gXCJhdXRvXCIgPyBwcm9wcy5pbnRlcmFjdGl2ZSA/IG51bGwgOiBcImRlc2NyaWJlZGJ5XCIgOiBvdXQuYXJpYS5jb250ZW50XG4gICAgfTtcbiAgICByZXR1cm4gb3V0O1xuICB9XG4gIGZ1bmN0aW9uIHZhbGlkYXRlUHJvcHMocGFydGlhbFByb3BzLCBwbHVnaW5zKSB7XG4gICAgaWYgKHBhcnRpYWxQcm9wcyA9PT0gdm9pZCAwKSB7XG4gICAgICBwYXJ0aWFsUHJvcHMgPSB7fTtcbiAgICB9XG4gICAgaWYgKHBsdWdpbnMgPT09IHZvaWQgMCkge1xuICAgICAgcGx1Z2lucyA9IFtdO1xuICAgIH1cbiAgICB2YXIga2V5cyA9IE9iamVjdC5rZXlzKHBhcnRpYWxQcm9wcyk7XG4gICAga2V5cy5mb3JFYWNoKGZ1bmN0aW9uKHByb3ApIHtcbiAgICAgIHZhciBub25QbHVnaW5Qcm9wcyA9IHJlbW92ZVByb3BlcnRpZXMoZGVmYXVsdFByb3BzLCBPYmplY3Qua2V5cyhwbHVnaW5Qcm9wcykpO1xuICAgICAgdmFyIGRpZFBhc3NVbmtub3duUHJvcCA9ICFoYXNPd25Qcm9wZXJ0eShub25QbHVnaW5Qcm9wcywgcHJvcCk7XG4gICAgICBpZiAoZGlkUGFzc1Vua25vd25Qcm9wKSB7XG4gICAgICAgIGRpZFBhc3NVbmtub3duUHJvcCA9IHBsdWdpbnMuZmlsdGVyKGZ1bmN0aW9uKHBsdWdpbikge1xuICAgICAgICAgIHJldHVybiBwbHVnaW4ubmFtZSA9PT0gcHJvcDtcbiAgICAgICAgfSkubGVuZ3RoID09PSAwO1xuICAgICAgfVxuICAgICAgd2FybldoZW4oZGlkUGFzc1Vua25vd25Qcm9wLCBbXCJgXCIgKyBwcm9wICsgXCJgXCIsIFwiaXMgbm90IGEgdmFsaWQgcHJvcC4gWW91IG1heSBoYXZlIHNwZWxsZWQgaXQgaW5jb3JyZWN0bHksIG9yIGlmIGl0J3NcIiwgXCJhIHBsdWdpbiwgZm9yZ290IHRvIHBhc3MgaXQgaW4gYW4gYXJyYXkgYXMgcHJvcHMucGx1Z2lucy5cIiwgXCJcXG5cXG5cIiwgXCJBbGwgcHJvcHM6IGh0dHBzOi8vYXRvbWlrcy5naXRodWIuaW8vdGlwcHlqcy92Ni9hbGwtcHJvcHMvXFxuXCIsIFwiUGx1Z2luczogaHR0cHM6Ly9hdG9taWtzLmdpdGh1Yi5pby90aXBweWpzL3Y2L3BsdWdpbnMvXCJdLmpvaW4oXCIgXCIpKTtcbiAgICB9KTtcbiAgfVxuICB2YXIgaW5uZXJIVE1MID0gZnVuY3Rpb24gaW5uZXJIVE1MMigpIHtcbiAgICByZXR1cm4gXCJpbm5lckhUTUxcIjtcbiAgfTtcbiAgZnVuY3Rpb24gZGFuZ2Vyb3VzbHlTZXRJbm5lckhUTUwoZWxlbWVudCwgaHRtbCkge1xuICAgIGVsZW1lbnRbaW5uZXJIVE1MKCldID0gaHRtbDtcbiAgfVxuICBmdW5jdGlvbiBjcmVhdGVBcnJvd0VsZW1lbnQodmFsdWUpIHtcbiAgICB2YXIgYXJyb3cgPSBkaXYoKTtcbiAgICBpZiAodmFsdWUgPT09IHRydWUpIHtcbiAgICAgIGFycm93LmNsYXNzTmFtZSA9IEFSUk9XX0NMQVNTO1xuICAgIH0gZWxzZSB7XG4gICAgICBhcnJvdy5jbGFzc05hbWUgPSBTVkdfQVJST1dfQ0xBU1M7XG4gICAgICBpZiAoaXNFbGVtZW50KHZhbHVlKSkge1xuICAgICAgICBhcnJvdy5hcHBlbmRDaGlsZCh2YWx1ZSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBkYW5nZXJvdXNseVNldElubmVySFRNTChhcnJvdywgdmFsdWUpO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gYXJyb3c7XG4gIH1cbiAgZnVuY3Rpb24gc2V0Q29udGVudChjb250ZW50LCBwcm9wcykge1xuICAgIGlmIChpc0VsZW1lbnQocHJvcHMuY29udGVudCkpIHtcbiAgICAgIGRhbmdlcm91c2x5U2V0SW5uZXJIVE1MKGNvbnRlbnQsIFwiXCIpO1xuICAgICAgY29udGVudC5hcHBlbmRDaGlsZChwcm9wcy5jb250ZW50KTtcbiAgICB9IGVsc2UgaWYgKHR5cGVvZiBwcm9wcy5jb250ZW50ICE9PSBcImZ1bmN0aW9uXCIpIHtcbiAgICAgIGlmIChwcm9wcy5hbGxvd0hUTUwpIHtcbiAgICAgICAgZGFuZ2Vyb3VzbHlTZXRJbm5lckhUTUwoY29udGVudCwgcHJvcHMuY29udGVudCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBjb250ZW50LnRleHRDb250ZW50ID0gcHJvcHMuY29udGVudDtcbiAgICAgIH1cbiAgICB9XG4gIH1cbiAgZnVuY3Rpb24gZ2V0Q2hpbGRyZW4ocG9wcGVyKSB7XG4gICAgdmFyIGJveCA9IHBvcHBlci5maXJzdEVsZW1lbnRDaGlsZDtcbiAgICB2YXIgYm94Q2hpbGRyZW4gPSBhcnJheUZyb20oYm94LmNoaWxkcmVuKTtcbiAgICByZXR1cm4ge1xuICAgICAgYm94LFxuICAgICAgY29udGVudDogYm94Q2hpbGRyZW4uZmluZChmdW5jdGlvbihub2RlKSB7XG4gICAgICAgIHJldHVybiBub2RlLmNsYXNzTGlzdC5jb250YWlucyhDT05URU5UX0NMQVNTKTtcbiAgICAgIH0pLFxuICAgICAgYXJyb3c6IGJveENoaWxkcmVuLmZpbmQoZnVuY3Rpb24obm9kZSkge1xuICAgICAgICByZXR1cm4gbm9kZS5jbGFzc0xpc3QuY29udGFpbnMoQVJST1dfQ0xBU1MpIHx8IG5vZGUuY2xhc3NMaXN0LmNvbnRhaW5zKFNWR19BUlJPV19DTEFTUyk7XG4gICAgICB9KSxcbiAgICAgIGJhY2tkcm9wOiBib3hDaGlsZHJlbi5maW5kKGZ1bmN0aW9uKG5vZGUpIHtcbiAgICAgICAgcmV0dXJuIG5vZGUuY2xhc3NMaXN0LmNvbnRhaW5zKEJBQ0tEUk9QX0NMQVNTKTtcbiAgICAgIH0pXG4gICAgfTtcbiAgfVxuICBmdW5jdGlvbiByZW5kZXIoaW5zdGFuY2UpIHtcbiAgICB2YXIgcG9wcGVyID0gZGl2KCk7XG4gICAgdmFyIGJveCA9IGRpdigpO1xuICAgIGJveC5jbGFzc05hbWUgPSBCT1hfQ0xBU1M7XG4gICAgYm94LnNldEF0dHJpYnV0ZShcImRhdGEtc3RhdGVcIiwgXCJoaWRkZW5cIik7XG4gICAgYm94LnNldEF0dHJpYnV0ZShcInRhYmluZGV4XCIsIFwiLTFcIik7XG4gICAgdmFyIGNvbnRlbnQgPSBkaXYoKTtcbiAgICBjb250ZW50LmNsYXNzTmFtZSA9IENPTlRFTlRfQ0xBU1M7XG4gICAgY29udGVudC5zZXRBdHRyaWJ1dGUoXCJkYXRhLXN0YXRlXCIsIFwiaGlkZGVuXCIpO1xuICAgIHNldENvbnRlbnQoY29udGVudCwgaW5zdGFuY2UucHJvcHMpO1xuICAgIHBvcHBlci5hcHBlbmRDaGlsZChib3gpO1xuICAgIGJveC5hcHBlbmRDaGlsZChjb250ZW50KTtcbiAgICBvblVwZGF0ZShpbnN0YW5jZS5wcm9wcywgaW5zdGFuY2UucHJvcHMpO1xuICAgIGZ1bmN0aW9uIG9uVXBkYXRlKHByZXZQcm9wcywgbmV4dFByb3BzKSB7XG4gICAgICB2YXIgX2dldENoaWxkcmVuID0gZ2V0Q2hpbGRyZW4ocG9wcGVyKSwgYm94MiA9IF9nZXRDaGlsZHJlbi5ib3gsIGNvbnRlbnQyID0gX2dldENoaWxkcmVuLmNvbnRlbnQsIGFycm93ID0gX2dldENoaWxkcmVuLmFycm93O1xuICAgICAgaWYgKG5leHRQcm9wcy50aGVtZSkge1xuICAgICAgICBib3gyLnNldEF0dHJpYnV0ZShcImRhdGEtdGhlbWVcIiwgbmV4dFByb3BzLnRoZW1lKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGJveDIucmVtb3ZlQXR0cmlidXRlKFwiZGF0YS10aGVtZVwiKTtcbiAgICAgIH1cbiAgICAgIGlmICh0eXBlb2YgbmV4dFByb3BzLmFuaW1hdGlvbiA9PT0gXCJzdHJpbmdcIikge1xuICAgICAgICBib3gyLnNldEF0dHJpYnV0ZShcImRhdGEtYW5pbWF0aW9uXCIsIG5leHRQcm9wcy5hbmltYXRpb24pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYm94Mi5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLWFuaW1hdGlvblwiKTtcbiAgICAgIH1cbiAgICAgIGlmIChuZXh0UHJvcHMuaW5lcnRpYSkge1xuICAgICAgICBib3gyLnNldEF0dHJpYnV0ZShcImRhdGEtaW5lcnRpYVwiLCBcIlwiKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGJveDIucmVtb3ZlQXR0cmlidXRlKFwiZGF0YS1pbmVydGlhXCIpO1xuICAgICAgfVxuICAgICAgYm94Mi5zdHlsZS5tYXhXaWR0aCA9IHR5cGVvZiBuZXh0UHJvcHMubWF4V2lkdGggPT09IFwibnVtYmVyXCIgPyBuZXh0UHJvcHMubWF4V2lkdGggKyBcInB4XCIgOiBuZXh0UHJvcHMubWF4V2lkdGg7XG4gICAgICBpZiAobmV4dFByb3BzLnJvbGUpIHtcbiAgICAgICAgYm94Mi5zZXRBdHRyaWJ1dGUoXCJyb2xlXCIsIG5leHRQcm9wcy5yb2xlKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGJveDIucmVtb3ZlQXR0cmlidXRlKFwicm9sZVwiKTtcbiAgICAgIH1cbiAgICAgIGlmIChwcmV2UHJvcHMuY29udGVudCAhPT0gbmV4dFByb3BzLmNvbnRlbnQgfHwgcHJldlByb3BzLmFsbG93SFRNTCAhPT0gbmV4dFByb3BzLmFsbG93SFRNTCkge1xuICAgICAgICBzZXRDb250ZW50KGNvbnRlbnQyLCBpbnN0YW5jZS5wcm9wcyk7XG4gICAgICB9XG4gICAgICBpZiAobmV4dFByb3BzLmFycm93KSB7XG4gICAgICAgIGlmICghYXJyb3cpIHtcbiAgICAgICAgICBib3gyLmFwcGVuZENoaWxkKGNyZWF0ZUFycm93RWxlbWVudChuZXh0UHJvcHMuYXJyb3cpKTtcbiAgICAgICAgfSBlbHNlIGlmIChwcmV2UHJvcHMuYXJyb3cgIT09IG5leHRQcm9wcy5hcnJvdykge1xuICAgICAgICAgIGJveDIucmVtb3ZlQ2hpbGQoYXJyb3cpO1xuICAgICAgICAgIGJveDIuYXBwZW5kQ2hpbGQoY3JlYXRlQXJyb3dFbGVtZW50KG5leHRQcm9wcy5hcnJvdykpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKGFycm93KSB7XG4gICAgICAgIGJveDIucmVtb3ZlQ2hpbGQoYXJyb3cpO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4ge1xuICAgICAgcG9wcGVyLFxuICAgICAgb25VcGRhdGVcbiAgICB9O1xuICB9XG4gIHJlbmRlci4kJHRpcHB5ID0gdHJ1ZTtcbiAgdmFyIGlkQ291bnRlciA9IDE7XG4gIHZhciBtb3VzZU1vdmVMaXN0ZW5lcnMgPSBbXTtcbiAgdmFyIG1vdW50ZWRJbnN0YW5jZXMgPSBbXTtcbiAgZnVuY3Rpb24gY3JlYXRlVGlwcHkocmVmZXJlbmNlLCBwYXNzZWRQcm9wcykge1xuICAgIHZhciBwcm9wcyA9IGV2YWx1YXRlUHJvcHMocmVmZXJlbmNlLCBPYmplY3QuYXNzaWduKHt9LCBkZWZhdWx0UHJvcHMsIHt9LCBnZXRFeHRlbmRlZFBhc3NlZFByb3BzKHJlbW92ZVVuZGVmaW5lZFByb3BzKHBhc3NlZFByb3BzKSkpKTtcbiAgICB2YXIgc2hvd1RpbWVvdXQ7XG4gICAgdmFyIGhpZGVUaW1lb3V0O1xuICAgIHZhciBzY2hlZHVsZUhpZGVBbmltYXRpb25GcmFtZTtcbiAgICB2YXIgaXNWaXNpYmxlRnJvbUNsaWNrID0gZmFsc2U7XG4gICAgdmFyIGRpZEhpZGVEdWVUb0RvY3VtZW50TW91c2VEb3duID0gZmFsc2U7XG4gICAgdmFyIGRpZFRvdWNoTW92ZSA9IGZhbHNlO1xuICAgIHZhciBpZ25vcmVPbkZpcnN0VXBkYXRlID0gZmFsc2U7XG4gICAgdmFyIGxhc3RUcmlnZ2VyRXZlbnQ7XG4gICAgdmFyIGN1cnJlbnRUcmFuc2l0aW9uRW5kTGlzdGVuZXI7XG4gICAgdmFyIG9uRmlyc3RVcGRhdGU7XG4gICAgdmFyIGxpc3RlbmVycyA9IFtdO1xuICAgIHZhciBkZWJvdW5jZWRPbk1vdXNlTW92ZSA9IGRlYm91bmNlKG9uTW91c2VNb3ZlLCBwcm9wcy5pbnRlcmFjdGl2ZURlYm91bmNlKTtcbiAgICB2YXIgY3VycmVudFRhcmdldDtcbiAgICB2YXIgaWQgPSBpZENvdW50ZXIrKztcbiAgICB2YXIgcG9wcGVySW5zdGFuY2UgPSBudWxsO1xuICAgIHZhciBwbHVnaW5zID0gdW5pcXVlKHByb3BzLnBsdWdpbnMpO1xuICAgIHZhciBzdGF0ZSA9IHtcbiAgICAgIGlzRW5hYmxlZDogdHJ1ZSxcbiAgICAgIGlzVmlzaWJsZTogZmFsc2UsXG4gICAgICBpc0Rlc3Ryb3llZDogZmFsc2UsXG4gICAgICBpc01vdW50ZWQ6IGZhbHNlLFxuICAgICAgaXNTaG93bjogZmFsc2VcbiAgICB9O1xuICAgIHZhciBpbnN0YW5jZSA9IHtcbiAgICAgIGlkLFxuICAgICAgcmVmZXJlbmNlLFxuICAgICAgcG9wcGVyOiBkaXYoKSxcbiAgICAgIHBvcHBlckluc3RhbmNlLFxuICAgICAgcHJvcHMsXG4gICAgICBzdGF0ZSxcbiAgICAgIHBsdWdpbnMsXG4gICAgICBjbGVhckRlbGF5VGltZW91dHMsXG4gICAgICBzZXRQcm9wcyxcbiAgICAgIHNldENvbnRlbnQ6IHNldENvbnRlbnQyLFxuICAgICAgc2hvdyxcbiAgICAgIGhpZGUsXG4gICAgICBoaWRlV2l0aEludGVyYWN0aXZpdHksXG4gICAgICBlbmFibGUsXG4gICAgICBkaXNhYmxlLFxuICAgICAgdW5tb3VudCxcbiAgICAgIGRlc3Ryb3lcbiAgICB9O1xuICAgIGlmICghcHJvcHMucmVuZGVyKSB7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICBlcnJvcldoZW4odHJ1ZSwgXCJyZW5kZXIoKSBmdW5jdGlvbiBoYXMgbm90IGJlZW4gc3VwcGxpZWQuXCIpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIGluc3RhbmNlO1xuICAgIH1cbiAgICB2YXIgX3Byb3BzJHJlbmRlciA9IHByb3BzLnJlbmRlcihpbnN0YW5jZSksIHBvcHBlciA9IF9wcm9wcyRyZW5kZXIucG9wcGVyLCBvblVwZGF0ZSA9IF9wcm9wcyRyZW5kZXIub25VcGRhdGU7XG4gICAgcG9wcGVyLnNldEF0dHJpYnV0ZShcImRhdGEtdGlwcHktcm9vdFwiLCBcIlwiKTtcbiAgICBwb3BwZXIuaWQgPSBcInRpcHB5LVwiICsgaW5zdGFuY2UuaWQ7XG4gICAgaW5zdGFuY2UucG9wcGVyID0gcG9wcGVyO1xuICAgIHJlZmVyZW5jZS5fdGlwcHkgPSBpbnN0YW5jZTtcbiAgICBwb3BwZXIuX3RpcHB5ID0gaW5zdGFuY2U7XG4gICAgdmFyIHBsdWdpbnNIb29rcyA9IHBsdWdpbnMubWFwKGZ1bmN0aW9uKHBsdWdpbikge1xuICAgICAgcmV0dXJuIHBsdWdpbi5mbihpbnN0YW5jZSk7XG4gICAgfSk7XG4gICAgdmFyIGhhc0FyaWFFeHBhbmRlZCA9IHJlZmVyZW5jZS5oYXNBdHRyaWJ1dGUoXCJhcmlhLWV4cGFuZGVkXCIpO1xuICAgIGFkZExpc3RlbmVycygpO1xuICAgIGhhbmRsZUFyaWFFeHBhbmRlZEF0dHJpYnV0ZSgpO1xuICAgIGhhbmRsZVN0eWxlcygpO1xuICAgIGludm9rZUhvb2soXCJvbkNyZWF0ZVwiLCBbaW5zdGFuY2VdKTtcbiAgICBpZiAocHJvcHMuc2hvd09uQ3JlYXRlKSB7XG4gICAgICBzY2hlZHVsZVNob3coKTtcbiAgICB9XG4gICAgcG9wcGVyLmFkZEV2ZW50TGlzdGVuZXIoXCJtb3VzZWVudGVyXCIsIGZ1bmN0aW9uKCkge1xuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLmludGVyYWN0aXZlICYmIGluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSkge1xuICAgICAgICBpbnN0YW5jZS5jbGVhckRlbGF5VGltZW91dHMoKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgICBwb3BwZXIuYWRkRXZlbnRMaXN0ZW5lcihcIm1vdXNlbGVhdmVcIiwgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSAmJiBpbnN0YW5jZS5wcm9wcy50cmlnZ2VyLmluZGV4T2YoXCJtb3VzZWVudGVyXCIpID49IDApIHtcbiAgICAgICAgZ2V0RG9jdW1lbnQoKS5hZGRFdmVudExpc3RlbmVyKFwibW91c2Vtb3ZlXCIsIGRlYm91bmNlZE9uTW91c2VNb3ZlKTtcbiAgICAgICAgZGVib3VuY2VkT25Nb3VzZU1vdmUoZXZlbnQpO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHJldHVybiBpbnN0YW5jZTtcbiAgICBmdW5jdGlvbiBnZXROb3JtYWxpemVkVG91Y2hTZXR0aW5ncygpIHtcbiAgICAgIHZhciB0b3VjaCA9IGluc3RhbmNlLnByb3BzLnRvdWNoO1xuICAgICAgcmV0dXJuIEFycmF5LmlzQXJyYXkodG91Y2gpID8gdG91Y2ggOiBbdG91Y2gsIDBdO1xuICAgIH1cbiAgICBmdW5jdGlvbiBnZXRJc0N1c3RvbVRvdWNoQmVoYXZpb3IoKSB7XG4gICAgICByZXR1cm4gZ2V0Tm9ybWFsaXplZFRvdWNoU2V0dGluZ3MoKVswXSA9PT0gXCJob2xkXCI7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGdldElzRGVmYXVsdFJlbmRlckZuKCkge1xuICAgICAgdmFyIF9pbnN0YW5jZSRwcm9wcyRyZW5kZTtcbiAgICAgIHJldHVybiAhISgoX2luc3RhbmNlJHByb3BzJHJlbmRlID0gaW5zdGFuY2UucHJvcHMucmVuZGVyKSA9PSBudWxsID8gdm9pZCAwIDogX2luc3RhbmNlJHByb3BzJHJlbmRlLiQkdGlwcHkpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBnZXRDdXJyZW50VGFyZ2V0KCkge1xuICAgICAgcmV0dXJuIGN1cnJlbnRUYXJnZXQgfHwgcmVmZXJlbmNlO1xuICAgIH1cbiAgICBmdW5jdGlvbiBnZXREb2N1bWVudCgpIHtcbiAgICAgIHZhciBwYXJlbnQgPSBnZXRDdXJyZW50VGFyZ2V0KCkucGFyZW50Tm9kZTtcbiAgICAgIHJldHVybiBwYXJlbnQgPyBnZXRPd25lckRvY3VtZW50KHBhcmVudCkgOiBkb2N1bWVudDtcbiAgICB9XG4gICAgZnVuY3Rpb24gZ2V0RGVmYXVsdFRlbXBsYXRlQ2hpbGRyZW4oKSB7XG4gICAgICByZXR1cm4gZ2V0Q2hpbGRyZW4ocG9wcGVyKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gZ2V0RGVsYXkoaXNTaG93KSB7XG4gICAgICBpZiAoaW5zdGFuY2Uuc3RhdGUuaXNNb3VudGVkICYmICFpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUgfHwgY3VycmVudElucHV0LmlzVG91Y2ggfHwgbGFzdFRyaWdnZXJFdmVudCAmJiBsYXN0VHJpZ2dlckV2ZW50LnR5cGUgPT09IFwiZm9jdXNcIikge1xuICAgICAgICByZXR1cm4gMDtcbiAgICAgIH1cbiAgICAgIHJldHVybiBnZXRWYWx1ZUF0SW5kZXhPclJldHVybihpbnN0YW5jZS5wcm9wcy5kZWxheSwgaXNTaG93ID8gMCA6IDEsIGRlZmF1bHRQcm9wcy5kZWxheSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGhhbmRsZVN0eWxlcygpIHtcbiAgICAgIHBvcHBlci5zdHlsZS5wb2ludGVyRXZlbnRzID0gaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUgJiYgaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlID8gXCJcIiA6IFwibm9uZVwiO1xuICAgICAgcG9wcGVyLnN0eWxlLnpJbmRleCA9IFwiXCIgKyBpbnN0YW5jZS5wcm9wcy56SW5kZXg7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGludm9rZUhvb2soaG9vaywgYXJncywgc2hvdWxkSW52b2tlUHJvcHNIb29rKSB7XG4gICAgICBpZiAoc2hvdWxkSW52b2tlUHJvcHNIb29rID09PSB2b2lkIDApIHtcbiAgICAgICAgc2hvdWxkSW52b2tlUHJvcHNIb29rID0gdHJ1ZTtcbiAgICAgIH1cbiAgICAgIHBsdWdpbnNIb29rcy5mb3JFYWNoKGZ1bmN0aW9uKHBsdWdpbkhvb2tzKSB7XG4gICAgICAgIGlmIChwbHVnaW5Ib29rc1tob29rXSkge1xuICAgICAgICAgIHBsdWdpbkhvb2tzW2hvb2tdLmFwcGx5KHZvaWQgMCwgYXJncyk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgICAgaWYgKHNob3VsZEludm9rZVByb3BzSG9vaykge1xuICAgICAgICB2YXIgX2luc3RhbmNlJHByb3BzO1xuICAgICAgICAoX2luc3RhbmNlJHByb3BzID0gaW5zdGFuY2UucHJvcHMpW2hvb2tdLmFwcGx5KF9pbnN0YW5jZSRwcm9wcywgYXJncyk7XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIGhhbmRsZUFyaWFDb250ZW50QXR0cmlidXRlKCkge1xuICAgICAgdmFyIGFyaWEgPSBpbnN0YW5jZS5wcm9wcy5hcmlhO1xuICAgICAgaWYgKCFhcmlhLmNvbnRlbnQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIGF0dHIgPSBcImFyaWEtXCIgKyBhcmlhLmNvbnRlbnQ7XG4gICAgICB2YXIgaWQyID0gcG9wcGVyLmlkO1xuICAgICAgdmFyIG5vZGVzID0gbm9ybWFsaXplVG9BcnJheShpbnN0YW5jZS5wcm9wcy50cmlnZ2VyVGFyZ2V0IHx8IHJlZmVyZW5jZSk7XG4gICAgICBub2Rlcy5mb3JFYWNoKGZ1bmN0aW9uKG5vZGUpIHtcbiAgICAgICAgdmFyIGN1cnJlbnRWYWx1ZSA9IG5vZGUuZ2V0QXR0cmlidXRlKGF0dHIpO1xuICAgICAgICBpZiAoaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlKSB7XG4gICAgICAgICAgbm9kZS5zZXRBdHRyaWJ1dGUoYXR0ciwgY3VycmVudFZhbHVlID8gY3VycmVudFZhbHVlICsgXCIgXCIgKyBpZDIgOiBpZDIpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIHZhciBuZXh0VmFsdWUgPSBjdXJyZW50VmFsdWUgJiYgY3VycmVudFZhbHVlLnJlcGxhY2UoaWQyLCBcIlwiKS50cmltKCk7XG4gICAgICAgICAgaWYgKG5leHRWYWx1ZSkge1xuICAgICAgICAgICAgbm9kZS5zZXRBdHRyaWJ1dGUoYXR0ciwgbmV4dFZhbHVlKTtcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgbm9kZS5yZW1vdmVBdHRyaWJ1dGUoYXR0cik7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gaGFuZGxlQXJpYUV4cGFuZGVkQXR0cmlidXRlKCkge1xuICAgICAgaWYgKGhhc0FyaWFFeHBhbmRlZCB8fCAhaW5zdGFuY2UucHJvcHMuYXJpYS5leHBhbmRlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgbm9kZXMgPSBub3JtYWxpemVUb0FycmF5KGluc3RhbmNlLnByb3BzLnRyaWdnZXJUYXJnZXQgfHwgcmVmZXJlbmNlKTtcbiAgICAgIG5vZGVzLmZvckVhY2goZnVuY3Rpb24obm9kZSkge1xuICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUpIHtcbiAgICAgICAgICBub2RlLnNldEF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIiwgaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlICYmIG5vZGUgPT09IGdldEN1cnJlbnRUYXJnZXQoKSA/IFwidHJ1ZVwiIDogXCJmYWxzZVwiKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBub2RlLnJlbW92ZUF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIik7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBjbGVhbnVwSW50ZXJhY3RpdmVNb3VzZUxpc3RlbmVycygpIHtcbiAgICAgIGdldERvY3VtZW50KCkucmVtb3ZlRXZlbnRMaXN0ZW5lcihcIm1vdXNlbW92ZVwiLCBkZWJvdW5jZWRPbk1vdXNlTW92ZSk7XG4gICAgICBtb3VzZU1vdmVMaXN0ZW5lcnMgPSBtb3VzZU1vdmVMaXN0ZW5lcnMuZmlsdGVyKGZ1bmN0aW9uKGxpc3RlbmVyKSB7XG4gICAgICAgIHJldHVybiBsaXN0ZW5lciAhPT0gZGVib3VuY2VkT25Nb3VzZU1vdmU7XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gb25Eb2N1bWVudFByZXNzKGV2ZW50KSB7XG4gICAgICBpZiAoY3VycmVudElucHV0LmlzVG91Y2gpIHtcbiAgICAgICAgaWYgKGRpZFRvdWNoTW92ZSB8fCBldmVudC50eXBlID09PSBcIm1vdXNlZG93blwiKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUgJiYgcG9wcGVyLmNvbnRhaW5zKGV2ZW50LnRhcmdldCkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaWYgKGdldEN1cnJlbnRUYXJnZXQoKS5jb250YWlucyhldmVudC50YXJnZXQpKSB7XG4gICAgICAgIGlmIChjdXJyZW50SW5wdXQuaXNUb3VjaCkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBpZiAoaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlICYmIGluc3RhbmNlLnByb3BzLnRyaWdnZXIuaW5kZXhPZihcImNsaWNrXCIpID49IDApIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGludm9rZUhvb2soXCJvbkNsaWNrT3V0c2lkZVwiLCBbaW5zdGFuY2UsIGV2ZW50XSk7XG4gICAgICB9XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMuaGlkZU9uQ2xpY2sgPT09IHRydWUpIHtcbiAgICAgICAgaW5zdGFuY2UuY2xlYXJEZWxheVRpbWVvdXRzKCk7XG4gICAgICAgIGluc3RhbmNlLmhpZGUoKTtcbiAgICAgICAgZGlkSGlkZUR1ZVRvRG9jdW1lbnRNb3VzZURvd24gPSB0cnVlO1xuICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgIGRpZEhpZGVEdWVUb0RvY3VtZW50TW91c2VEb3duID0gZmFsc2U7XG4gICAgICAgIH0pO1xuICAgICAgICBpZiAoIWluc3RhbmNlLnN0YXRlLmlzTW91bnRlZCkge1xuICAgICAgICAgIHJlbW92ZURvY3VtZW50UHJlc3MoKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cbiAgICBmdW5jdGlvbiBvblRvdWNoTW92ZSgpIHtcbiAgICAgIGRpZFRvdWNoTW92ZSA9IHRydWU7XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uVG91Y2hTdGFydCgpIHtcbiAgICAgIGRpZFRvdWNoTW92ZSA9IGZhbHNlO1xuICAgIH1cbiAgICBmdW5jdGlvbiBhZGREb2N1bWVudFByZXNzKCkge1xuICAgICAgdmFyIGRvYyA9IGdldERvY3VtZW50KCk7XG4gICAgICBkb2MuYWRkRXZlbnRMaXN0ZW5lcihcIm1vdXNlZG93blwiLCBvbkRvY3VtZW50UHJlc3MsIHRydWUpO1xuICAgICAgZG9jLmFkZEV2ZW50TGlzdGVuZXIoXCJ0b3VjaGVuZFwiLCBvbkRvY3VtZW50UHJlc3MsIFRPVUNIX09QVElPTlMpO1xuICAgICAgZG9jLmFkZEV2ZW50TGlzdGVuZXIoXCJ0b3VjaHN0YXJ0XCIsIG9uVG91Y2hTdGFydCwgVE9VQ0hfT1BUSU9OUyk7XG4gICAgICBkb2MuYWRkRXZlbnRMaXN0ZW5lcihcInRvdWNobW92ZVwiLCBvblRvdWNoTW92ZSwgVE9VQ0hfT1BUSU9OUyk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIHJlbW92ZURvY3VtZW50UHJlc3MoKSB7XG4gICAgICB2YXIgZG9jID0gZ2V0RG9jdW1lbnQoKTtcbiAgICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwibW91c2Vkb3duXCIsIG9uRG9jdW1lbnRQcmVzcywgdHJ1ZSk7XG4gICAgICBkb2MucmVtb3ZlRXZlbnRMaXN0ZW5lcihcInRvdWNoZW5kXCIsIG9uRG9jdW1lbnRQcmVzcywgVE9VQ0hfT1BUSU9OUyk7XG4gICAgICBkb2MucmVtb3ZlRXZlbnRMaXN0ZW5lcihcInRvdWNoc3RhcnRcIiwgb25Ub3VjaFN0YXJ0LCBUT1VDSF9PUFRJT05TKTtcbiAgICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwidG91Y2htb3ZlXCIsIG9uVG91Y2hNb3ZlLCBUT1VDSF9PUFRJT05TKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gb25UcmFuc2l0aW9uZWRPdXQoZHVyYXRpb24sIGNhbGxiYWNrKSB7XG4gICAgICBvblRyYW5zaXRpb25FbmQoZHVyYXRpb24sIGZ1bmN0aW9uKCkge1xuICAgICAgICBpZiAoIWluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSAmJiBwb3BwZXIucGFyZW50Tm9kZSAmJiBwb3BwZXIucGFyZW50Tm9kZS5jb250YWlucyhwb3BwZXIpKSB7XG4gICAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uVHJhbnNpdGlvbmVkSW4oZHVyYXRpb24sIGNhbGxiYWNrKSB7XG4gICAgICBvblRyYW5zaXRpb25FbmQoZHVyYXRpb24sIGNhbGxiYWNrKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gb25UcmFuc2l0aW9uRW5kKGR1cmF0aW9uLCBjYWxsYmFjaykge1xuICAgICAgdmFyIGJveCA9IGdldERlZmF1bHRUZW1wbGF0ZUNoaWxkcmVuKCkuYm94O1xuICAgICAgZnVuY3Rpb24gbGlzdGVuZXIoZXZlbnQpIHtcbiAgICAgICAgaWYgKGV2ZW50LnRhcmdldCA9PT0gYm94KSB7XG4gICAgICAgICAgdXBkYXRlVHJhbnNpdGlvbkVuZExpc3RlbmVyKGJveCwgXCJyZW1vdmVcIiwgbGlzdGVuZXIpO1xuICAgICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGlmIChkdXJhdGlvbiA9PT0gMCkge1xuICAgICAgICByZXR1cm4gY2FsbGJhY2soKTtcbiAgICAgIH1cbiAgICAgIHVwZGF0ZVRyYW5zaXRpb25FbmRMaXN0ZW5lcihib3gsIFwicmVtb3ZlXCIsIGN1cnJlbnRUcmFuc2l0aW9uRW5kTGlzdGVuZXIpO1xuICAgICAgdXBkYXRlVHJhbnNpdGlvbkVuZExpc3RlbmVyKGJveCwgXCJhZGRcIiwgbGlzdGVuZXIpO1xuICAgICAgY3VycmVudFRyYW5zaXRpb25FbmRMaXN0ZW5lciA9IGxpc3RlbmVyO1xuICAgIH1cbiAgICBmdW5jdGlvbiBvbihldmVudFR5cGUsIGhhbmRsZXIsIG9wdGlvbnMpIHtcbiAgICAgIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICAgICAgb3B0aW9ucyA9IGZhbHNlO1xuICAgICAgfVxuICAgICAgdmFyIG5vZGVzID0gbm9ybWFsaXplVG9BcnJheShpbnN0YW5jZS5wcm9wcy50cmlnZ2VyVGFyZ2V0IHx8IHJlZmVyZW5jZSk7XG4gICAgICBub2Rlcy5mb3JFYWNoKGZ1bmN0aW9uKG5vZGUpIHtcbiAgICAgICAgbm9kZS5hZGRFdmVudExpc3RlbmVyKGV2ZW50VHlwZSwgaGFuZGxlciwgb3B0aW9ucyk7XG4gICAgICAgIGxpc3RlbmVycy5wdXNoKHtcbiAgICAgICAgICBub2RlLFxuICAgICAgICAgIGV2ZW50VHlwZSxcbiAgICAgICAgICBoYW5kbGVyLFxuICAgICAgICAgIG9wdGlvbnNcbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gYWRkTGlzdGVuZXJzKCkge1xuICAgICAgaWYgKGdldElzQ3VzdG9tVG91Y2hCZWhhdmlvcigpKSB7XG4gICAgICAgIG9uKFwidG91Y2hzdGFydFwiLCBvblRyaWdnZXIsIHtcbiAgICAgICAgICBwYXNzaXZlOiB0cnVlXG4gICAgICAgIH0pO1xuICAgICAgICBvbihcInRvdWNoZW5kXCIsIG9uTW91c2VMZWF2ZSwge1xuICAgICAgICAgIHBhc3NpdmU6IHRydWVcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBzcGxpdEJ5U3BhY2VzKGluc3RhbmNlLnByb3BzLnRyaWdnZXIpLmZvckVhY2goZnVuY3Rpb24oZXZlbnRUeXBlKSB7XG4gICAgICAgIGlmIChldmVudFR5cGUgPT09IFwibWFudWFsXCIpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgb24oZXZlbnRUeXBlLCBvblRyaWdnZXIpO1xuICAgICAgICBzd2l0Y2ggKGV2ZW50VHlwZSkge1xuICAgICAgICAgIGNhc2UgXCJtb3VzZWVudGVyXCI6XG4gICAgICAgICAgICBvbihcIm1vdXNlbGVhdmVcIiwgb25Nb3VzZUxlYXZlKTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgXCJmb2N1c1wiOlxuICAgICAgICAgICAgb24oaXNJRSA/IFwiZm9jdXNvdXRcIiA6IFwiYmx1clwiLCBvbkJsdXJPckZvY3VzT3V0KTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIGNhc2UgXCJmb2N1c2luXCI6XG4gICAgICAgICAgICBvbihcImZvY3Vzb3V0XCIsIG9uQmx1ck9yRm9jdXNPdXQpO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiByZW1vdmVMaXN0ZW5lcnMoKSB7XG4gICAgICBsaXN0ZW5lcnMuZm9yRWFjaChmdW5jdGlvbihfcmVmKSB7XG4gICAgICAgIHZhciBub2RlID0gX3JlZi5ub2RlLCBldmVudFR5cGUgPSBfcmVmLmV2ZW50VHlwZSwgaGFuZGxlciA9IF9yZWYuaGFuZGxlciwgb3B0aW9ucyA9IF9yZWYub3B0aW9ucztcbiAgICAgICAgbm9kZS5yZW1vdmVFdmVudExpc3RlbmVyKGV2ZW50VHlwZSwgaGFuZGxlciwgb3B0aW9ucyk7XG4gICAgICB9KTtcbiAgICAgIGxpc3RlbmVycyA9IFtdO1xuICAgIH1cbiAgICBmdW5jdGlvbiBvblRyaWdnZXIoZXZlbnQpIHtcbiAgICAgIHZhciBfbGFzdFRyaWdnZXJFdmVudDtcbiAgICAgIHZhciBzaG91bGRTY2hlZHVsZUNsaWNrSGlkZSA9IGZhbHNlO1xuICAgICAgaWYgKCFpbnN0YW5jZS5zdGF0ZS5pc0VuYWJsZWQgfHwgaXNFdmVudExpc3RlbmVyU3RvcHBlZChldmVudCkgfHwgZGlkSGlkZUR1ZVRvRG9jdW1lbnRNb3VzZURvd24pIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIHdhc0ZvY3VzZWQgPSAoKF9sYXN0VHJpZ2dlckV2ZW50ID0gbGFzdFRyaWdnZXJFdmVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9sYXN0VHJpZ2dlckV2ZW50LnR5cGUpID09PSBcImZvY3VzXCI7XG4gICAgICBsYXN0VHJpZ2dlckV2ZW50ID0gZXZlbnQ7XG4gICAgICBjdXJyZW50VGFyZ2V0ID0gZXZlbnQuY3VycmVudFRhcmdldDtcbiAgICAgIGhhbmRsZUFyaWFFeHBhbmRlZEF0dHJpYnV0ZSgpO1xuICAgICAgaWYgKCFpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUgJiYgaXNNb3VzZUV2ZW50KGV2ZW50KSkge1xuICAgICAgICBtb3VzZU1vdmVMaXN0ZW5lcnMuZm9yRWFjaChmdW5jdGlvbihsaXN0ZW5lcikge1xuICAgICAgICAgIHJldHVybiBsaXN0ZW5lcihldmVudCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICAgaWYgKGV2ZW50LnR5cGUgPT09IFwiY2xpY2tcIiAmJiAoaW5zdGFuY2UucHJvcHMudHJpZ2dlci5pbmRleE9mKFwibW91c2VlbnRlclwiKSA8IDAgfHwgaXNWaXNpYmxlRnJvbUNsaWNrKSAmJiBpbnN0YW5jZS5wcm9wcy5oaWRlT25DbGljayAhPT0gZmFsc2UgJiYgaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlKSB7XG4gICAgICAgIHNob3VsZFNjaGVkdWxlQ2xpY2tIaWRlID0gdHJ1ZTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHNjaGVkdWxlU2hvdyhldmVudCk7XG4gICAgICB9XG4gICAgICBpZiAoZXZlbnQudHlwZSA9PT0gXCJjbGlja1wiKSB7XG4gICAgICAgIGlzVmlzaWJsZUZyb21DbGljayA9ICFzaG91bGRTY2hlZHVsZUNsaWNrSGlkZTtcbiAgICAgIH1cbiAgICAgIGlmIChzaG91bGRTY2hlZHVsZUNsaWNrSGlkZSAmJiAhd2FzRm9jdXNlZCkge1xuICAgICAgICBzY2hlZHVsZUhpZGUoZXZlbnQpO1xuICAgICAgfVxuICAgIH1cbiAgICBmdW5jdGlvbiBvbk1vdXNlTW92ZShldmVudCkge1xuICAgICAgdmFyIHRhcmdldCA9IGV2ZW50LnRhcmdldDtcbiAgICAgIHZhciBpc0N1cnNvck92ZXJSZWZlcmVuY2VPclBvcHBlciA9IGdldEN1cnJlbnRUYXJnZXQoKS5jb250YWlucyh0YXJnZXQpIHx8IHBvcHBlci5jb250YWlucyh0YXJnZXQpO1xuICAgICAgaWYgKGV2ZW50LnR5cGUgPT09IFwibW91c2Vtb3ZlXCIgJiYgaXNDdXJzb3JPdmVyUmVmZXJlbmNlT3JQb3BwZXIpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIHBvcHBlclRyZWVEYXRhID0gZ2V0TmVzdGVkUG9wcGVyVHJlZSgpLmNvbmNhdChwb3BwZXIpLm1hcChmdW5jdGlvbihwb3BwZXIyKSB7XG4gICAgICAgIHZhciBfaW5zdGFuY2UkcG9wcGVySW5zdGE7XG4gICAgICAgIHZhciBpbnN0YW5jZTIgPSBwb3BwZXIyLl90aXBweTtcbiAgICAgICAgdmFyIHN0YXRlMiA9IChfaW5zdGFuY2UkcG9wcGVySW5zdGEgPSBpbnN0YW5jZTIucG9wcGVySW5zdGFuY2UpID09IG51bGwgPyB2b2lkIDAgOiBfaW5zdGFuY2UkcG9wcGVySW5zdGEuc3RhdGU7XG4gICAgICAgIGlmIChzdGF0ZTIpIHtcbiAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgcG9wcGVyUmVjdDogcG9wcGVyMi5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKSxcbiAgICAgICAgICAgIHBvcHBlclN0YXRlOiBzdGF0ZTIsXG4gICAgICAgICAgICBwcm9wc1xuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgICB9KS5maWx0ZXIoQm9vbGVhbik7XG4gICAgICBpZiAoaXNDdXJzb3JPdXRzaWRlSW50ZXJhY3RpdmVCb3JkZXIocG9wcGVyVHJlZURhdGEsIGV2ZW50KSkge1xuICAgICAgICBjbGVhbnVwSW50ZXJhY3RpdmVNb3VzZUxpc3RlbmVycygpO1xuICAgICAgICBzY2hlZHVsZUhpZGUoZXZlbnQpO1xuICAgICAgfVxuICAgIH1cbiAgICBmdW5jdGlvbiBvbk1vdXNlTGVhdmUoZXZlbnQpIHtcbiAgICAgIHZhciBzaG91bGRCYWlsID0gaXNFdmVudExpc3RlbmVyU3RvcHBlZChldmVudCkgfHwgaW5zdGFuY2UucHJvcHMudHJpZ2dlci5pbmRleE9mKFwiY2xpY2tcIikgPj0gMCAmJiBpc1Zpc2libGVGcm9tQ2xpY2s7XG4gICAgICBpZiAoc2hvdWxkQmFpbCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUpIHtcbiAgICAgICAgaW5zdGFuY2UuaGlkZVdpdGhJbnRlcmFjdGl2aXR5KGV2ZW50KTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgc2NoZWR1bGVIaWRlKGV2ZW50KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gb25CbHVyT3JGb2N1c091dChldmVudCkge1xuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLnRyaWdnZXIuaW5kZXhPZihcImZvY3VzaW5cIikgPCAwICYmIGV2ZW50LnRhcmdldCAhPT0gZ2V0Q3VycmVudFRhcmdldCgpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5pbnRlcmFjdGl2ZSAmJiBldmVudC5yZWxhdGVkVGFyZ2V0ICYmIHBvcHBlci5jb250YWlucyhldmVudC5yZWxhdGVkVGFyZ2V0KSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBzY2hlZHVsZUhpZGUoZXZlbnQpO1xuICAgIH1cbiAgICBmdW5jdGlvbiBpc0V2ZW50TGlzdGVuZXJTdG9wcGVkKGV2ZW50KSB7XG4gICAgICByZXR1cm4gY3VycmVudElucHV0LmlzVG91Y2ggPyBnZXRJc0N1c3RvbVRvdWNoQmVoYXZpb3IoKSAhPT0gZXZlbnQudHlwZS5pbmRleE9mKFwidG91Y2hcIikgPj0gMCA6IGZhbHNlO1xuICAgIH1cbiAgICBmdW5jdGlvbiBjcmVhdGVQb3BwZXJJbnN0YW5jZSgpIHtcbiAgICAgIGRlc3Ryb3lQb3BwZXJJbnN0YW5jZSgpO1xuICAgICAgdmFyIF9pbnN0YW5jZSRwcm9wczIgPSBpbnN0YW5jZS5wcm9wcywgcG9wcGVyT3B0aW9ucyA9IF9pbnN0YW5jZSRwcm9wczIucG9wcGVyT3B0aW9ucywgcGxhY2VtZW50ID0gX2luc3RhbmNlJHByb3BzMi5wbGFjZW1lbnQsIG9mZnNldCA9IF9pbnN0YW5jZSRwcm9wczIub2Zmc2V0LCBnZXRSZWZlcmVuY2VDbGllbnRSZWN0ID0gX2luc3RhbmNlJHByb3BzMi5nZXRSZWZlcmVuY2VDbGllbnRSZWN0LCBtb3ZlVHJhbnNpdGlvbiA9IF9pbnN0YW5jZSRwcm9wczIubW92ZVRyYW5zaXRpb247XG4gICAgICB2YXIgYXJyb3cgPSBnZXRJc0RlZmF1bHRSZW5kZXJGbigpID8gZ2V0Q2hpbGRyZW4ocG9wcGVyKS5hcnJvdyA6IG51bGw7XG4gICAgICB2YXIgY29tcHV0ZWRSZWZlcmVuY2UgPSBnZXRSZWZlcmVuY2VDbGllbnRSZWN0ID8ge1xuICAgICAgICBnZXRCb3VuZGluZ0NsaWVudFJlY3Q6IGdldFJlZmVyZW5jZUNsaWVudFJlY3QsXG4gICAgICAgIGNvbnRleHRFbGVtZW50OiBnZXRSZWZlcmVuY2VDbGllbnRSZWN0LmNvbnRleHRFbGVtZW50IHx8IGdldEN1cnJlbnRUYXJnZXQoKVxuICAgICAgfSA6IHJlZmVyZW5jZTtcbiAgICAgIHZhciB0aXBweU1vZGlmaWVyID0ge1xuICAgICAgICBuYW1lOiBcIiQkdGlwcHlcIixcbiAgICAgICAgZW5hYmxlZDogdHJ1ZSxcbiAgICAgICAgcGhhc2U6IFwiYmVmb3JlV3JpdGVcIixcbiAgICAgICAgcmVxdWlyZXM6IFtcImNvbXB1dGVTdHlsZXNcIl0sXG4gICAgICAgIGZuOiBmdW5jdGlvbiBmbihfcmVmMikge1xuICAgICAgICAgIHZhciBzdGF0ZTIgPSBfcmVmMi5zdGF0ZTtcbiAgICAgICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSkge1xuICAgICAgICAgICAgdmFyIF9nZXREZWZhdWx0VGVtcGxhdGVDaCA9IGdldERlZmF1bHRUZW1wbGF0ZUNoaWxkcmVuKCksIGJveCA9IF9nZXREZWZhdWx0VGVtcGxhdGVDaC5ib3g7XG4gICAgICAgICAgICBbXCJwbGFjZW1lbnRcIiwgXCJyZWZlcmVuY2UtaGlkZGVuXCIsIFwiZXNjYXBlZFwiXS5mb3JFYWNoKGZ1bmN0aW9uKGF0dHIpIHtcbiAgICAgICAgICAgICAgaWYgKGF0dHIgPT09IFwicGxhY2VtZW50XCIpIHtcbiAgICAgICAgICAgICAgICBib3guc2V0QXR0cmlidXRlKFwiZGF0YS1wbGFjZW1lbnRcIiwgc3RhdGUyLnBsYWNlbWVudCk7XG4gICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgaWYgKHN0YXRlMi5hdHRyaWJ1dGVzLnBvcHBlcltcImRhdGEtcG9wcGVyLVwiICsgYXR0cl0pIHtcbiAgICAgICAgICAgICAgICAgIGJveC5zZXRBdHRyaWJ1dGUoXCJkYXRhLVwiICsgYXR0ciwgXCJcIik7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgIGJveC5yZW1vdmVBdHRyaWJ1dGUoXCJkYXRhLVwiICsgYXR0cik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIHN0YXRlMi5hdHRyaWJ1dGVzLnBvcHBlciA9IHt9O1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfTtcbiAgICAgIHZhciBtb2RpZmllcnMgPSBbe1xuICAgICAgICBuYW1lOiBcIm9mZnNldFwiLFxuICAgICAgICBvcHRpb25zOiB7XG4gICAgICAgICAgb2Zmc2V0XG4gICAgICAgIH1cbiAgICAgIH0sIHtcbiAgICAgICAgbmFtZTogXCJwcmV2ZW50T3ZlcmZsb3dcIixcbiAgICAgICAgb3B0aW9uczoge1xuICAgICAgICAgIHBhZGRpbmc6IHtcbiAgICAgICAgICAgIHRvcDogMixcbiAgICAgICAgICAgIGJvdHRvbTogMixcbiAgICAgICAgICAgIGxlZnQ6IDUsXG4gICAgICAgICAgICByaWdodDogNVxuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfSwge1xuICAgICAgICBuYW1lOiBcImZsaXBcIixcbiAgICAgICAgb3B0aW9uczoge1xuICAgICAgICAgIHBhZGRpbmc6IDVcbiAgICAgICAgfVxuICAgICAgfSwge1xuICAgICAgICBuYW1lOiBcImNvbXB1dGVTdHlsZXNcIixcbiAgICAgICAgb3B0aW9uczoge1xuICAgICAgICAgIGFkYXB0aXZlOiAhbW92ZVRyYW5zaXRpb25cbiAgICAgICAgfVxuICAgICAgfSwgdGlwcHlNb2RpZmllcl07XG4gICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSAmJiBhcnJvdykge1xuICAgICAgICBtb2RpZmllcnMucHVzaCh7XG4gICAgICAgICAgbmFtZTogXCJhcnJvd1wiLFxuICAgICAgICAgIG9wdGlvbnM6IHtcbiAgICAgICAgICAgIGVsZW1lbnQ6IGFycm93LFxuICAgICAgICAgICAgcGFkZGluZzogM1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgICBtb2RpZmllcnMucHVzaC5hcHBseShtb2RpZmllcnMsIChwb3BwZXJPcHRpb25zID09IG51bGwgPyB2b2lkIDAgOiBwb3BwZXJPcHRpb25zLm1vZGlmaWVycykgfHwgW10pO1xuICAgICAgaW5zdGFuY2UucG9wcGVySW5zdGFuY2UgPSBjb3JlLmNyZWF0ZVBvcHBlcihjb21wdXRlZFJlZmVyZW5jZSwgcG9wcGVyLCBPYmplY3QuYXNzaWduKHt9LCBwb3BwZXJPcHRpb25zLCB7XG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgb25GaXJzdFVwZGF0ZSxcbiAgICAgICAgbW9kaWZpZXJzXG4gICAgICB9KSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGRlc3Ryb3lQb3BwZXJJbnN0YW5jZSgpIHtcbiAgICAgIGlmIChpbnN0YW5jZS5wb3BwZXJJbnN0YW5jZSkge1xuICAgICAgICBpbnN0YW5jZS5wb3BwZXJJbnN0YW5jZS5kZXN0cm95KCk7XG4gICAgICAgIGluc3RhbmNlLnBvcHBlckluc3RhbmNlID0gbnVsbDtcbiAgICAgIH1cbiAgICB9XG4gICAgZnVuY3Rpb24gbW91bnQoKSB7XG4gICAgICB2YXIgYXBwZW5kVG8gPSBpbnN0YW5jZS5wcm9wcy5hcHBlbmRUbztcbiAgICAgIHZhciBwYXJlbnROb2RlO1xuICAgICAgdmFyIG5vZGUgPSBnZXRDdXJyZW50VGFyZ2V0KCk7XG4gICAgICBpZiAoaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUgJiYgYXBwZW5kVG8gPT09IGRlZmF1bHRQcm9wcy5hcHBlbmRUbyB8fCBhcHBlbmRUbyA9PT0gXCJwYXJlbnRcIikge1xuICAgICAgICBwYXJlbnROb2RlID0gbm9kZS5wYXJlbnROb2RlO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgcGFyZW50Tm9kZSA9IGludm9rZVdpdGhBcmdzT3JSZXR1cm4oYXBwZW5kVG8sIFtub2RlXSk7XG4gICAgICB9XG4gICAgICBpZiAoIXBhcmVudE5vZGUuY29udGFpbnMocG9wcGVyKSkge1xuICAgICAgICBwYXJlbnROb2RlLmFwcGVuZENoaWxkKHBvcHBlcik7XG4gICAgICB9XG4gICAgICBjcmVhdGVQb3BwZXJJbnN0YW5jZSgpO1xuICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgd2FybldoZW4oaW5zdGFuY2UucHJvcHMuaW50ZXJhY3RpdmUgJiYgYXBwZW5kVG8gPT09IGRlZmF1bHRQcm9wcy5hcHBlbmRUbyAmJiBub2RlLm5leHRFbGVtZW50U2libGluZyAhPT0gcG9wcGVyLCBbXCJJbnRlcmFjdGl2ZSB0aXBweSBlbGVtZW50IG1heSBub3QgYmUgYWNjZXNzaWJsZSB2aWEga2V5Ym9hcmRcIiwgXCJuYXZpZ2F0aW9uIGJlY2F1c2UgaXQgaXMgbm90IGRpcmVjdGx5IGFmdGVyIHRoZSByZWZlcmVuY2UgZWxlbWVudFwiLCBcImluIHRoZSBET00gc291cmNlIG9yZGVyLlwiLCBcIlxcblxcblwiLCBcIlVzaW5nIGEgd3JhcHBlciA8ZGl2PiBvciA8c3Bhbj4gdGFnIGFyb3VuZCB0aGUgcmVmZXJlbmNlIGVsZW1lbnRcIiwgXCJzb2x2ZXMgdGhpcyBieSBjcmVhdGluZyBhIG5ldyBwYXJlbnROb2RlIGNvbnRleHQuXCIsIFwiXFxuXFxuXCIsIFwiU3BlY2lmeWluZyBgYXBwZW5kVG86IGRvY3VtZW50LmJvZHlgIHNpbGVuY2VzIHRoaXMgd2FybmluZywgYnV0IGl0XCIsIFwiYXNzdW1lcyB5b3UgYXJlIHVzaW5nIGEgZm9jdXMgbWFuYWdlbWVudCBzb2x1dGlvbiB0byBoYW5kbGVcIiwgXCJrZXlib2FyZCBuYXZpZ2F0aW9uLlwiLCBcIlxcblxcblwiLCBcIlNlZTogaHR0cHM6Ly9hdG9taWtzLmdpdGh1Yi5pby90aXBweWpzL3Y2L2FjY2Vzc2liaWxpdHkvI2ludGVyYWN0aXZpdHlcIl0uam9pbihcIiBcIikpO1xuICAgICAgfVxuICAgIH1cbiAgICBmdW5jdGlvbiBnZXROZXN0ZWRQb3BwZXJUcmVlKCkge1xuICAgICAgcmV0dXJuIGFycmF5RnJvbShwb3BwZXIucXVlcnlTZWxlY3RvckFsbChcIltkYXRhLXRpcHB5LXJvb3RdXCIpKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gc2NoZWR1bGVTaG93KGV2ZW50KSB7XG4gICAgICBpbnN0YW5jZS5jbGVhckRlbGF5VGltZW91dHMoKTtcbiAgICAgIGlmIChldmVudCkge1xuICAgICAgICBpbnZva2VIb29rKFwib25UcmlnZ2VyXCIsIFtpbnN0YW5jZSwgZXZlbnRdKTtcbiAgICAgIH1cbiAgICAgIGFkZERvY3VtZW50UHJlc3MoKTtcbiAgICAgIHZhciBkZWxheSA9IGdldERlbGF5KHRydWUpO1xuICAgICAgdmFyIF9nZXROb3JtYWxpemVkVG91Y2hTZSA9IGdldE5vcm1hbGl6ZWRUb3VjaFNldHRpbmdzKCksIHRvdWNoVmFsdWUgPSBfZ2V0Tm9ybWFsaXplZFRvdWNoU2VbMF0sIHRvdWNoRGVsYXkgPSBfZ2V0Tm9ybWFsaXplZFRvdWNoU2VbMV07XG4gICAgICBpZiAoY3VycmVudElucHV0LmlzVG91Y2ggJiYgdG91Y2hWYWx1ZSA9PT0gXCJob2xkXCIgJiYgdG91Y2hEZWxheSkge1xuICAgICAgICBkZWxheSA9IHRvdWNoRGVsYXk7XG4gICAgICB9XG4gICAgICBpZiAoZGVsYXkpIHtcbiAgICAgICAgc2hvd1RpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgIGluc3RhbmNlLnNob3coKTtcbiAgICAgICAgfSwgZGVsYXkpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgaW5zdGFuY2Uuc2hvdygpO1xuICAgICAgfVxuICAgIH1cbiAgICBmdW5jdGlvbiBzY2hlZHVsZUhpZGUoZXZlbnQpIHtcbiAgICAgIGluc3RhbmNlLmNsZWFyRGVsYXlUaW1lb3V0cygpO1xuICAgICAgaW52b2tlSG9vayhcIm9uVW50cmlnZ2VyXCIsIFtpbnN0YW5jZSwgZXZlbnRdKTtcbiAgICAgIGlmICghaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlKSB7XG4gICAgICAgIHJlbW92ZURvY3VtZW50UHJlc3MoKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLnRyaWdnZXIuaW5kZXhPZihcIm1vdXNlZW50ZXJcIikgPj0gMCAmJiBpbnN0YW5jZS5wcm9wcy50cmlnZ2VyLmluZGV4T2YoXCJjbGlja1wiKSA+PSAwICYmIFtcIm1vdXNlbGVhdmVcIiwgXCJtb3VzZW1vdmVcIl0uaW5kZXhPZihldmVudC50eXBlKSA+PSAwICYmIGlzVmlzaWJsZUZyb21DbGljaykge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgZGVsYXkgPSBnZXREZWxheShmYWxzZSk7XG4gICAgICBpZiAoZGVsYXkpIHtcbiAgICAgICAgaGlkZVRpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgIGlmIChpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUpIHtcbiAgICAgICAgICAgIGluc3RhbmNlLmhpZGUoKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0sIGRlbGF5KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHNjaGVkdWxlSGlkZUFuaW1hdGlvbkZyYW1lID0gcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZ1bmN0aW9uKCkge1xuICAgICAgICAgIGluc3RhbmNlLmhpZGUoKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIGVuYWJsZSgpIHtcbiAgICAgIGluc3RhbmNlLnN0YXRlLmlzRW5hYmxlZCA9IHRydWU7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGRpc2FibGUoKSB7XG4gICAgICBpbnN0YW5jZS5oaWRlKCk7XG4gICAgICBpbnN0YW5jZS5zdGF0ZS5pc0VuYWJsZWQgPSBmYWxzZTtcbiAgICB9XG4gICAgZnVuY3Rpb24gY2xlYXJEZWxheVRpbWVvdXRzKCkge1xuICAgICAgY2xlYXJUaW1lb3V0KHNob3dUaW1lb3V0KTtcbiAgICAgIGNsZWFyVGltZW91dChoaWRlVGltZW91dCk7XG4gICAgICBjYW5jZWxBbmltYXRpb25GcmFtZShzY2hlZHVsZUhpZGVBbmltYXRpb25GcmFtZSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIHNldFByb3BzKHBhcnRpYWxQcm9wcykge1xuICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgd2FybldoZW4oaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQsIGNyZWF0ZU1lbW9yeUxlYWtXYXJuaW5nKFwic2V0UHJvcHNcIikpO1xuICAgICAgfVxuICAgICAgaWYgKGluc3RhbmNlLnN0YXRlLmlzRGVzdHJveWVkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGludm9rZUhvb2soXCJvbkJlZm9yZVVwZGF0ZVwiLCBbaW5zdGFuY2UsIHBhcnRpYWxQcm9wc10pO1xuICAgICAgcmVtb3ZlTGlzdGVuZXJzKCk7XG4gICAgICB2YXIgcHJldlByb3BzID0gaW5zdGFuY2UucHJvcHM7XG4gICAgICB2YXIgbmV4dFByb3BzID0gZXZhbHVhdGVQcm9wcyhyZWZlcmVuY2UsIE9iamVjdC5hc3NpZ24oe30sIGluc3RhbmNlLnByb3BzLCB7fSwgcGFydGlhbFByb3BzLCB7XG4gICAgICAgIGlnbm9yZUF0dHJpYnV0ZXM6IHRydWVcbiAgICAgIH0pKTtcbiAgICAgIGluc3RhbmNlLnByb3BzID0gbmV4dFByb3BzO1xuICAgICAgYWRkTGlzdGVuZXJzKCk7XG4gICAgICBpZiAocHJldlByb3BzLmludGVyYWN0aXZlRGVib3VuY2UgIT09IG5leHRQcm9wcy5pbnRlcmFjdGl2ZURlYm91bmNlKSB7XG4gICAgICAgIGNsZWFudXBJbnRlcmFjdGl2ZU1vdXNlTGlzdGVuZXJzKCk7XG4gICAgICAgIGRlYm91bmNlZE9uTW91c2VNb3ZlID0gZGVib3VuY2Uob25Nb3VzZU1vdmUsIG5leHRQcm9wcy5pbnRlcmFjdGl2ZURlYm91bmNlKTtcbiAgICAgIH1cbiAgICAgIGlmIChwcmV2UHJvcHMudHJpZ2dlclRhcmdldCAmJiAhbmV4dFByb3BzLnRyaWdnZXJUYXJnZXQpIHtcbiAgICAgICAgbm9ybWFsaXplVG9BcnJheShwcmV2UHJvcHMudHJpZ2dlclRhcmdldCkuZm9yRWFjaChmdW5jdGlvbihub2RlKSB7XG4gICAgICAgICAgbm9kZS5yZW1vdmVBdHRyaWJ1dGUoXCJhcmlhLWV4cGFuZGVkXCIpO1xuICAgICAgICB9KTtcbiAgICAgIH0gZWxzZSBpZiAobmV4dFByb3BzLnRyaWdnZXJUYXJnZXQpIHtcbiAgICAgICAgcmVmZXJlbmNlLnJlbW92ZUF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIik7XG4gICAgICB9XG4gICAgICBoYW5kbGVBcmlhRXhwYW5kZWRBdHRyaWJ1dGUoKTtcbiAgICAgIGhhbmRsZVN0eWxlcygpO1xuICAgICAgaWYgKG9uVXBkYXRlKSB7XG4gICAgICAgIG9uVXBkYXRlKHByZXZQcm9wcywgbmV4dFByb3BzKTtcbiAgICAgIH1cbiAgICAgIGlmIChpbnN0YW5jZS5wb3BwZXJJbnN0YW5jZSkge1xuICAgICAgICBjcmVhdGVQb3BwZXJJbnN0YW5jZSgpO1xuICAgICAgICBnZXROZXN0ZWRQb3BwZXJUcmVlKCkuZm9yRWFjaChmdW5jdGlvbihuZXN0ZWRQb3BwZXIpIHtcbiAgICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUobmVzdGVkUG9wcGVyLl90aXBweS5wb3BwZXJJbnN0YW5jZS5mb3JjZVVwZGF0ZSk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICAgaW52b2tlSG9vayhcIm9uQWZ0ZXJVcGRhdGVcIiwgW2luc3RhbmNlLCBwYXJ0aWFsUHJvcHNdKTtcbiAgICB9XG4gICAgZnVuY3Rpb24gc2V0Q29udGVudDIoY29udGVudCkge1xuICAgICAgaW5zdGFuY2Uuc2V0UHJvcHMoe1xuICAgICAgICBjb250ZW50XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gc2hvdygpIHtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIHdhcm5XaGVuKGluc3RhbmNlLnN0YXRlLmlzRGVzdHJveWVkLCBjcmVhdGVNZW1vcnlMZWFrV2FybmluZyhcInNob3dcIikpO1xuICAgICAgfVxuICAgICAgdmFyIGlzQWxyZWFkeVZpc2libGUgPSBpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGU7XG4gICAgICB2YXIgaXNEZXN0cm95ZWQgPSBpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZDtcbiAgICAgIHZhciBpc0Rpc2FibGVkID0gIWluc3RhbmNlLnN0YXRlLmlzRW5hYmxlZDtcbiAgICAgIHZhciBpc1RvdWNoQW5kVG91Y2hEaXNhYmxlZCA9IGN1cnJlbnRJbnB1dC5pc1RvdWNoICYmICFpbnN0YW5jZS5wcm9wcy50b3VjaDtcbiAgICAgIHZhciBkdXJhdGlvbiA9IGdldFZhbHVlQXRJbmRleE9yUmV0dXJuKGluc3RhbmNlLnByb3BzLmR1cmF0aW9uLCAwLCBkZWZhdWx0UHJvcHMuZHVyYXRpb24pO1xuICAgICAgaWYgKGlzQWxyZWFkeVZpc2libGUgfHwgaXNEZXN0cm95ZWQgfHwgaXNEaXNhYmxlZCB8fCBpc1RvdWNoQW5kVG91Y2hEaXNhYmxlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpZiAoZ2V0Q3VycmVudFRhcmdldCgpLmhhc0F0dHJpYnV0ZShcImRpc2FibGVkXCIpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGludm9rZUhvb2soXCJvblNob3dcIiwgW2luc3RhbmNlXSwgZmFsc2UpO1xuICAgICAgaWYgKGluc3RhbmNlLnByb3BzLm9uU2hvdyhpbnN0YW5jZSkgPT09IGZhbHNlKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSA9IHRydWU7XG4gICAgICBpZiAoZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSkge1xuICAgICAgICBwb3BwZXIuc3R5bGUudmlzaWJpbGl0eSA9IFwidmlzaWJsZVwiO1xuICAgICAgfVxuICAgICAgaGFuZGxlU3R5bGVzKCk7XG4gICAgICBhZGREb2N1bWVudFByZXNzKCk7XG4gICAgICBpZiAoIWluc3RhbmNlLnN0YXRlLmlzTW91bnRlZCkge1xuICAgICAgICBwb3BwZXIuc3R5bGUudHJhbnNpdGlvbiA9IFwibm9uZVwiO1xuICAgICAgfVxuICAgICAgaWYgKGdldElzRGVmYXVsdFJlbmRlckZuKCkpIHtcbiAgICAgICAgdmFyIF9nZXREZWZhdWx0VGVtcGxhdGVDaDIgPSBnZXREZWZhdWx0VGVtcGxhdGVDaGlsZHJlbigpLCBib3ggPSBfZ2V0RGVmYXVsdFRlbXBsYXRlQ2gyLmJveCwgY29udGVudCA9IF9nZXREZWZhdWx0VGVtcGxhdGVDaDIuY29udGVudDtcbiAgICAgICAgc2V0VHJhbnNpdGlvbkR1cmF0aW9uKFtib3gsIGNvbnRlbnRdLCAwKTtcbiAgICAgIH1cbiAgICAgIG9uRmlyc3RVcGRhdGUgPSBmdW5jdGlvbiBvbkZpcnN0VXBkYXRlMigpIHtcbiAgICAgICAgdmFyIF9pbnN0YW5jZSRwb3BwZXJJbnN0YTI7XG4gICAgICAgIGlmICghaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlIHx8IGlnbm9yZU9uRmlyc3RVcGRhdGUpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cbiAgICAgICAgaWdub3JlT25GaXJzdFVwZGF0ZSA9IHRydWU7XG4gICAgICAgIHZvaWQgcG9wcGVyLm9mZnNldEhlaWdodDtcbiAgICAgICAgcG9wcGVyLnN0eWxlLnRyYW5zaXRpb24gPSBpbnN0YW5jZS5wcm9wcy5tb3ZlVHJhbnNpdGlvbjtcbiAgICAgICAgaWYgKGdldElzRGVmYXVsdFJlbmRlckZuKCkgJiYgaW5zdGFuY2UucHJvcHMuYW5pbWF0aW9uKSB7XG4gICAgICAgICAgdmFyIF9nZXREZWZhdWx0VGVtcGxhdGVDaDMgPSBnZXREZWZhdWx0VGVtcGxhdGVDaGlsZHJlbigpLCBfYm94ID0gX2dldERlZmF1bHRUZW1wbGF0ZUNoMy5ib3gsIF9jb250ZW50ID0gX2dldERlZmF1bHRUZW1wbGF0ZUNoMy5jb250ZW50O1xuICAgICAgICAgIHNldFRyYW5zaXRpb25EdXJhdGlvbihbX2JveCwgX2NvbnRlbnRdLCBkdXJhdGlvbik7XG4gICAgICAgICAgc2V0VmlzaWJpbGl0eVN0YXRlKFtfYm94LCBfY29udGVudF0sIFwidmlzaWJsZVwiKTtcbiAgICAgICAgfVxuICAgICAgICBoYW5kbGVBcmlhQ29udGVudEF0dHJpYnV0ZSgpO1xuICAgICAgICBoYW5kbGVBcmlhRXhwYW5kZWRBdHRyaWJ1dGUoKTtcbiAgICAgICAgcHVzaElmVW5pcXVlKG1vdW50ZWRJbnN0YW5jZXMsIGluc3RhbmNlKTtcbiAgICAgICAgKF9pbnN0YW5jZSRwb3BwZXJJbnN0YTIgPSBpbnN0YW5jZS5wb3BwZXJJbnN0YW5jZSkgPT0gbnVsbCA/IHZvaWQgMCA6IF9pbnN0YW5jZSRwb3BwZXJJbnN0YTIuZm9yY2VVcGRhdGUoKTtcbiAgICAgICAgaW5zdGFuY2Uuc3RhdGUuaXNNb3VudGVkID0gdHJ1ZTtcbiAgICAgICAgaW52b2tlSG9vayhcIm9uTW91bnRcIiwgW2luc3RhbmNlXSk7XG4gICAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5hbmltYXRpb24gJiYgZ2V0SXNEZWZhdWx0UmVuZGVyRm4oKSkge1xuICAgICAgICAgIG9uVHJhbnNpdGlvbmVkSW4oZHVyYXRpb24sIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgaW5zdGFuY2Uuc3RhdGUuaXNTaG93biA9IHRydWU7XG4gICAgICAgICAgICBpbnZva2VIb29rKFwib25TaG93blwiLCBbaW5zdGFuY2VdKTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICAgIG1vdW50KCk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGhpZGUoKSB7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICB3YXJuV2hlbihpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZCwgY3JlYXRlTWVtb3J5TGVha1dhcm5pbmcoXCJoaWRlXCIpKTtcbiAgICAgIH1cbiAgICAgIHZhciBpc0FscmVhZHlIaWRkZW4gPSAhaW5zdGFuY2Uuc3RhdGUuaXNWaXNpYmxlO1xuICAgICAgdmFyIGlzRGVzdHJveWVkID0gaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQ7XG4gICAgICB2YXIgaXNEaXNhYmxlZCA9ICFpbnN0YW5jZS5zdGF0ZS5pc0VuYWJsZWQ7XG4gICAgICB2YXIgZHVyYXRpb24gPSBnZXRWYWx1ZUF0SW5kZXhPclJldHVybihpbnN0YW5jZS5wcm9wcy5kdXJhdGlvbiwgMSwgZGVmYXVsdFByb3BzLmR1cmF0aW9uKTtcbiAgICAgIGlmIChpc0FscmVhZHlIaWRkZW4gfHwgaXNEZXN0cm95ZWQgfHwgaXNEaXNhYmxlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpbnZva2VIb29rKFwib25IaWRlXCIsIFtpbnN0YW5jZV0sIGZhbHNlKTtcbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5vbkhpZGUoaW5zdGFuY2UpID09PSBmYWxzZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBpbnN0YW5jZS5zdGF0ZS5pc1Zpc2libGUgPSBmYWxzZTtcbiAgICAgIGluc3RhbmNlLnN0YXRlLmlzU2hvd24gPSBmYWxzZTtcbiAgICAgIGlnbm9yZU9uRmlyc3RVcGRhdGUgPSBmYWxzZTtcbiAgICAgIGlzVmlzaWJsZUZyb21DbGljayA9IGZhbHNlO1xuICAgICAgaWYgKGdldElzRGVmYXVsdFJlbmRlckZuKCkpIHtcbiAgICAgICAgcG9wcGVyLnN0eWxlLnZpc2liaWxpdHkgPSBcImhpZGRlblwiO1xuICAgICAgfVxuICAgICAgY2xlYW51cEludGVyYWN0aXZlTW91c2VMaXN0ZW5lcnMoKTtcbiAgICAgIHJlbW92ZURvY3VtZW50UHJlc3MoKTtcbiAgICAgIGhhbmRsZVN0eWxlcygpO1xuICAgICAgaWYgKGdldElzRGVmYXVsdFJlbmRlckZuKCkpIHtcbiAgICAgICAgdmFyIF9nZXREZWZhdWx0VGVtcGxhdGVDaDQgPSBnZXREZWZhdWx0VGVtcGxhdGVDaGlsZHJlbigpLCBib3ggPSBfZ2V0RGVmYXVsdFRlbXBsYXRlQ2g0LmJveCwgY29udGVudCA9IF9nZXREZWZhdWx0VGVtcGxhdGVDaDQuY29udGVudDtcbiAgICAgICAgaWYgKGluc3RhbmNlLnByb3BzLmFuaW1hdGlvbikge1xuICAgICAgICAgIHNldFRyYW5zaXRpb25EdXJhdGlvbihbYm94LCBjb250ZW50XSwgZHVyYXRpb24pO1xuICAgICAgICAgIHNldFZpc2liaWxpdHlTdGF0ZShbYm94LCBjb250ZW50XSwgXCJoaWRkZW5cIik7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGhhbmRsZUFyaWFDb250ZW50QXR0cmlidXRlKCk7XG4gICAgICBoYW5kbGVBcmlhRXhwYW5kZWRBdHRyaWJ1dGUoKTtcbiAgICAgIGlmIChpbnN0YW5jZS5wcm9wcy5hbmltYXRpb24pIHtcbiAgICAgICAgaWYgKGdldElzRGVmYXVsdFJlbmRlckZuKCkpIHtcbiAgICAgICAgICBvblRyYW5zaXRpb25lZE91dChkdXJhdGlvbiwgaW5zdGFuY2UudW5tb3VudCk7XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGluc3RhbmNlLnVubW91bnQoKTtcbiAgICAgIH1cbiAgICB9XG4gICAgZnVuY3Rpb24gaGlkZVdpdGhJbnRlcmFjdGl2aXR5KGV2ZW50KSB7XG4gICAgICBpZiAodHJ1ZSkge1xuICAgICAgICB3YXJuV2hlbihpbnN0YW5jZS5zdGF0ZS5pc0Rlc3Ryb3llZCwgY3JlYXRlTWVtb3J5TGVha1dhcm5pbmcoXCJoaWRlV2l0aEludGVyYWN0aXZpdHlcIikpO1xuICAgICAgfVxuICAgICAgZ2V0RG9jdW1lbnQoKS5hZGRFdmVudExpc3RlbmVyKFwibW91c2Vtb3ZlXCIsIGRlYm91bmNlZE9uTW91c2VNb3ZlKTtcbiAgICAgIHB1c2hJZlVuaXF1ZShtb3VzZU1vdmVMaXN0ZW5lcnMsIGRlYm91bmNlZE9uTW91c2VNb3ZlKTtcbiAgICAgIGRlYm91bmNlZE9uTW91c2VNb3ZlKGV2ZW50KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gdW5tb3VudCgpIHtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIHdhcm5XaGVuKGluc3RhbmNlLnN0YXRlLmlzRGVzdHJveWVkLCBjcmVhdGVNZW1vcnlMZWFrV2FybmluZyhcInVubW91bnRcIikpO1xuICAgICAgfVxuICAgICAgaWYgKGluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZSkge1xuICAgICAgICBpbnN0YW5jZS5oaWRlKCk7XG4gICAgICB9XG4gICAgICBpZiAoIWluc3RhbmNlLnN0YXRlLmlzTW91bnRlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBkZXN0cm95UG9wcGVySW5zdGFuY2UoKTtcbiAgICAgIGdldE5lc3RlZFBvcHBlclRyZWUoKS5mb3JFYWNoKGZ1bmN0aW9uKG5lc3RlZFBvcHBlcikge1xuICAgICAgICBuZXN0ZWRQb3BwZXIuX3RpcHB5LnVubW91bnQoKTtcbiAgICAgIH0pO1xuICAgICAgaWYgKHBvcHBlci5wYXJlbnROb2RlKSB7XG4gICAgICAgIHBvcHBlci5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKHBvcHBlcik7XG4gICAgICB9XG4gICAgICBtb3VudGVkSW5zdGFuY2VzID0gbW91bnRlZEluc3RhbmNlcy5maWx0ZXIoZnVuY3Rpb24oaSkge1xuICAgICAgICByZXR1cm4gaSAhPT0gaW5zdGFuY2U7XG4gICAgICB9KTtcbiAgICAgIGluc3RhbmNlLnN0YXRlLmlzTW91bnRlZCA9IGZhbHNlO1xuICAgICAgaW52b2tlSG9vayhcIm9uSGlkZGVuXCIsIFtpbnN0YW5jZV0pO1xuICAgIH1cbiAgICBmdW5jdGlvbiBkZXN0cm95KCkge1xuICAgICAgaWYgKHRydWUpIHtcbiAgICAgICAgd2FybldoZW4oaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQsIGNyZWF0ZU1lbW9yeUxlYWtXYXJuaW5nKFwiZGVzdHJveVwiKSk7XG4gICAgICB9XG4gICAgICBpZiAoaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaW5zdGFuY2UuY2xlYXJEZWxheVRpbWVvdXRzKCk7XG4gICAgICBpbnN0YW5jZS51bm1vdW50KCk7XG4gICAgICByZW1vdmVMaXN0ZW5lcnMoKTtcbiAgICAgIGRlbGV0ZSByZWZlcmVuY2UuX3RpcHB5O1xuICAgICAgaW5zdGFuY2Uuc3RhdGUuaXNEZXN0cm95ZWQgPSB0cnVlO1xuICAgICAgaW52b2tlSG9vayhcIm9uRGVzdHJveVwiLCBbaW5zdGFuY2VdKTtcbiAgICB9XG4gIH1cbiAgZnVuY3Rpb24gdGlwcHkyKHRhcmdldHMsIG9wdGlvbmFsUHJvcHMpIHtcbiAgICBpZiAob3B0aW9uYWxQcm9wcyA9PT0gdm9pZCAwKSB7XG4gICAgICBvcHRpb25hbFByb3BzID0ge307XG4gICAgfVxuICAgIHZhciBwbHVnaW5zID0gZGVmYXVsdFByb3BzLnBsdWdpbnMuY29uY2F0KG9wdGlvbmFsUHJvcHMucGx1Z2lucyB8fCBbXSk7XG4gICAgaWYgKHRydWUpIHtcbiAgICAgIHZhbGlkYXRlVGFyZ2V0cyh0YXJnZXRzKTtcbiAgICAgIHZhbGlkYXRlUHJvcHMob3B0aW9uYWxQcm9wcywgcGx1Z2lucyk7XG4gICAgfVxuICAgIGJpbmRHbG9iYWxFdmVudExpc3RlbmVycygpO1xuICAgIHZhciBwYXNzZWRQcm9wcyA9IE9iamVjdC5hc3NpZ24oe30sIG9wdGlvbmFsUHJvcHMsIHtcbiAgICAgIHBsdWdpbnNcbiAgICB9KTtcbiAgICB2YXIgZWxlbWVudHMgPSBnZXRBcnJheU9mRWxlbWVudHModGFyZ2V0cyk7XG4gICAgaWYgKHRydWUpIHtcbiAgICAgIHZhciBpc1NpbmdsZUNvbnRlbnRFbGVtZW50ID0gaXNFbGVtZW50KHBhc3NlZFByb3BzLmNvbnRlbnQpO1xuICAgICAgdmFyIGlzTW9yZVRoYW5PbmVSZWZlcmVuY2VFbGVtZW50ID0gZWxlbWVudHMubGVuZ3RoID4gMTtcbiAgICAgIHdhcm5XaGVuKGlzU2luZ2xlQ29udGVudEVsZW1lbnQgJiYgaXNNb3JlVGhhbk9uZVJlZmVyZW5jZUVsZW1lbnQsIFtcInRpcHB5KCkgd2FzIHBhc3NlZCBhbiBFbGVtZW50IGFzIHRoZSBgY29udGVudGAgcHJvcCwgYnV0IG1vcmUgdGhhblwiLCBcIm9uZSB0aXBweSBpbnN0YW5jZSB3YXMgY3JlYXRlZCBieSB0aGlzIGludm9jYXRpb24uIFRoaXMgbWVhbnMgdGhlXCIsIFwiY29udGVudCBlbGVtZW50IHdpbGwgb25seSBiZSBhcHBlbmRlZCB0byB0aGUgbGFzdCB0aXBweSBpbnN0YW5jZS5cIiwgXCJcXG5cXG5cIiwgXCJJbnN0ZWFkLCBwYXNzIHRoZSAuaW5uZXJIVE1MIG9mIHRoZSBlbGVtZW50LCBvciB1c2UgYSBmdW5jdGlvbiB0aGF0XCIsIFwicmV0dXJucyBhIGNsb25lZCB2ZXJzaW9uIG9mIHRoZSBlbGVtZW50IGluc3RlYWQuXCIsIFwiXFxuXFxuXCIsIFwiMSkgY29udGVudDogZWxlbWVudC5pbm5lckhUTUxcXG5cIiwgXCIyKSBjb250ZW50OiAoKSA9PiBlbGVtZW50LmNsb25lTm9kZSh0cnVlKVwiXS5qb2luKFwiIFwiKSk7XG4gICAgfVxuICAgIHZhciBpbnN0YW5jZXMgPSBlbGVtZW50cy5yZWR1Y2UoZnVuY3Rpb24oYWNjLCByZWZlcmVuY2UpIHtcbiAgICAgIHZhciBpbnN0YW5jZSA9IHJlZmVyZW5jZSAmJiBjcmVhdGVUaXBweShyZWZlcmVuY2UsIHBhc3NlZFByb3BzKTtcbiAgICAgIGlmIChpbnN0YW5jZSkge1xuICAgICAgICBhY2MucHVzaChpbnN0YW5jZSk7XG4gICAgICB9XG4gICAgICByZXR1cm4gYWNjO1xuICAgIH0sIFtdKTtcbiAgICByZXR1cm4gaXNFbGVtZW50KHRhcmdldHMpID8gaW5zdGFuY2VzWzBdIDogaW5zdGFuY2VzO1xuICB9XG4gIHRpcHB5Mi5kZWZhdWx0UHJvcHMgPSBkZWZhdWx0UHJvcHM7XG4gIHRpcHB5Mi5zZXREZWZhdWx0UHJvcHMgPSBzZXREZWZhdWx0UHJvcHM7XG4gIHRpcHB5Mi5jdXJyZW50SW5wdXQgPSBjdXJyZW50SW5wdXQ7XG4gIHZhciBoaWRlQWxsID0gZnVuY3Rpb24gaGlkZUFsbDIoX3RlbXApIHtcbiAgICB2YXIgX3JlZiA9IF90ZW1wID09PSB2b2lkIDAgPyB7fSA6IF90ZW1wLCBleGNsdWRlZFJlZmVyZW5jZU9ySW5zdGFuY2UgPSBfcmVmLmV4Y2x1ZGUsIGR1cmF0aW9uID0gX3JlZi5kdXJhdGlvbjtcbiAgICBtb3VudGVkSW5zdGFuY2VzLmZvckVhY2goZnVuY3Rpb24oaW5zdGFuY2UpIHtcbiAgICAgIHZhciBpc0V4Y2x1ZGVkID0gZmFsc2U7XG4gICAgICBpZiAoZXhjbHVkZWRSZWZlcmVuY2VPckluc3RhbmNlKSB7XG4gICAgICAgIGlzRXhjbHVkZWQgPSBpc1JlZmVyZW5jZUVsZW1lbnQoZXhjbHVkZWRSZWZlcmVuY2VPckluc3RhbmNlKSA/IGluc3RhbmNlLnJlZmVyZW5jZSA9PT0gZXhjbHVkZWRSZWZlcmVuY2VPckluc3RhbmNlIDogaW5zdGFuY2UucG9wcGVyID09PSBleGNsdWRlZFJlZmVyZW5jZU9ySW5zdGFuY2UucG9wcGVyO1xuICAgICAgfVxuICAgICAgaWYgKCFpc0V4Y2x1ZGVkKSB7XG4gICAgICAgIHZhciBvcmlnaW5hbER1cmF0aW9uID0gaW5zdGFuY2UucHJvcHMuZHVyYXRpb247XG4gICAgICAgIGluc3RhbmNlLnNldFByb3BzKHtcbiAgICAgICAgICBkdXJhdGlvblxuICAgICAgICB9KTtcbiAgICAgICAgaW5zdGFuY2UuaGlkZSgpO1xuICAgICAgICBpZiAoIWluc3RhbmNlLnN0YXRlLmlzRGVzdHJveWVkKSB7XG4gICAgICAgICAgaW5zdGFuY2Uuc2V0UHJvcHMoe1xuICAgICAgICAgICAgZHVyYXRpb246IG9yaWdpbmFsRHVyYXRpb25cbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0pO1xuICB9O1xuICB2YXIgYXBwbHlTdHlsZXNNb2RpZmllciA9IE9iamVjdC5hc3NpZ24oe30sIGNvcmUuYXBwbHlTdHlsZXMsIHtcbiAgICBlZmZlY3Q6IGZ1bmN0aW9uIGVmZmVjdChfcmVmKSB7XG4gICAgICB2YXIgc3RhdGUgPSBfcmVmLnN0YXRlO1xuICAgICAgdmFyIGluaXRpYWxTdHlsZXMgPSB7XG4gICAgICAgIHBvcHBlcjoge1xuICAgICAgICAgIHBvc2l0aW9uOiBzdGF0ZS5vcHRpb25zLnN0cmF0ZWd5LFxuICAgICAgICAgIGxlZnQ6IFwiMFwiLFxuICAgICAgICAgIHRvcDogXCIwXCIsXG4gICAgICAgICAgbWFyZ2luOiBcIjBcIlxuICAgICAgICB9LFxuICAgICAgICBhcnJvdzoge1xuICAgICAgICAgIHBvc2l0aW9uOiBcImFic29sdXRlXCJcbiAgICAgICAgfSxcbiAgICAgICAgcmVmZXJlbmNlOiB7fVxuICAgICAgfTtcbiAgICAgIE9iamVjdC5hc3NpZ24oc3RhdGUuZWxlbWVudHMucG9wcGVyLnN0eWxlLCBpbml0aWFsU3R5bGVzLnBvcHBlcik7XG4gICAgICBzdGF0ZS5zdHlsZXMgPSBpbml0aWFsU3R5bGVzO1xuICAgICAgaWYgKHN0YXRlLmVsZW1lbnRzLmFycm93KSB7XG4gICAgICAgIE9iamVjdC5hc3NpZ24oc3RhdGUuZWxlbWVudHMuYXJyb3cuc3R5bGUsIGluaXRpYWxTdHlsZXMuYXJyb3cpO1xuICAgICAgfVxuICAgIH1cbiAgfSk7XG4gIHZhciBjcmVhdGVTaW5nbGV0b24gPSBmdW5jdGlvbiBjcmVhdGVTaW5nbGV0b24yKHRpcHB5SW5zdGFuY2VzLCBvcHRpb25hbFByb3BzKSB7XG4gICAgdmFyIF9vcHRpb25hbFByb3BzJHBvcHBlcjtcbiAgICBpZiAob3B0aW9uYWxQcm9wcyA9PT0gdm9pZCAwKSB7XG4gICAgICBvcHRpb25hbFByb3BzID0ge307XG4gICAgfVxuICAgIGlmICh0cnVlKSB7XG4gICAgICBlcnJvcldoZW4oIUFycmF5LmlzQXJyYXkodGlwcHlJbnN0YW5jZXMpLCBbXCJUaGUgZmlyc3QgYXJndW1lbnQgcGFzc2VkIHRvIGNyZWF0ZVNpbmdsZXRvbigpIG11c3QgYmUgYW4gYXJyYXkgb2ZcIiwgXCJ0aXBweSBpbnN0YW5jZXMuIFRoZSBwYXNzZWQgdmFsdWUgd2FzXCIsIFN0cmluZyh0aXBweUluc3RhbmNlcyldLmpvaW4oXCIgXCIpKTtcbiAgICB9XG4gICAgdmFyIGluZGl2aWR1YWxJbnN0YW5jZXMgPSB0aXBweUluc3RhbmNlcztcbiAgICB2YXIgcmVmZXJlbmNlcyA9IFtdO1xuICAgIHZhciBjdXJyZW50VGFyZ2V0O1xuICAgIHZhciBvdmVycmlkZXMgPSBvcHRpb25hbFByb3BzLm92ZXJyaWRlcztcbiAgICB2YXIgaW50ZXJjZXB0U2V0UHJvcHNDbGVhbnVwcyA9IFtdO1xuICAgIHZhciBzaG93bk9uQ3JlYXRlID0gZmFsc2U7XG4gICAgZnVuY3Rpb24gc2V0UmVmZXJlbmNlcygpIHtcbiAgICAgIHJlZmVyZW5jZXMgPSBpbmRpdmlkdWFsSW5zdGFuY2VzLm1hcChmdW5jdGlvbihpbnN0YW5jZSkge1xuICAgICAgICByZXR1cm4gaW5zdGFuY2UucmVmZXJlbmNlO1xuICAgICAgfSk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGVuYWJsZUluc3RhbmNlcyhpc0VuYWJsZWQpIHtcbiAgICAgIGluZGl2aWR1YWxJbnN0YW5jZXMuZm9yRWFjaChmdW5jdGlvbihpbnN0YW5jZSkge1xuICAgICAgICBpZiAoaXNFbmFibGVkKSB7XG4gICAgICAgICAgaW5zdGFuY2UuZW5hYmxlKCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgaW5zdGFuY2UuZGlzYWJsZSgpO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gaW50ZXJjZXB0U2V0UHJvcHMoc2luZ2xldG9uMikge1xuICAgICAgcmV0dXJuIGluZGl2aWR1YWxJbnN0YW5jZXMubWFwKGZ1bmN0aW9uKGluc3RhbmNlKSB7XG4gICAgICAgIHZhciBvcmlnaW5hbFNldFByb3BzMiA9IGluc3RhbmNlLnNldFByb3BzO1xuICAgICAgICBpbnN0YW5jZS5zZXRQcm9wcyA9IGZ1bmN0aW9uKHByb3BzKSB7XG4gICAgICAgICAgb3JpZ2luYWxTZXRQcm9wczIocHJvcHMpO1xuICAgICAgICAgIGlmIChpbnN0YW5jZS5yZWZlcmVuY2UgPT09IGN1cnJlbnRUYXJnZXQpIHtcbiAgICAgICAgICAgIHNpbmdsZXRvbjIuc2V0UHJvcHMocHJvcHMpO1xuICAgICAgICAgIH1cbiAgICAgICAgfTtcbiAgICAgICAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgICAgICAgIGluc3RhbmNlLnNldFByb3BzID0gb3JpZ2luYWxTZXRQcm9wczI7XG4gICAgICAgIH07XG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gcHJlcGFyZUluc3RhbmNlKHNpbmdsZXRvbjIsIHRhcmdldCkge1xuICAgICAgdmFyIGluZGV4ID0gcmVmZXJlbmNlcy5pbmRleE9mKHRhcmdldCk7XG4gICAgICBpZiAodGFyZ2V0ID09PSBjdXJyZW50VGFyZ2V0KSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGN1cnJlbnRUYXJnZXQgPSB0YXJnZXQ7XG4gICAgICB2YXIgb3ZlcnJpZGVQcm9wcyA9IChvdmVycmlkZXMgfHwgW10pLmNvbmNhdChcImNvbnRlbnRcIikucmVkdWNlKGZ1bmN0aW9uKGFjYywgcHJvcCkge1xuICAgICAgICBhY2NbcHJvcF0gPSBpbmRpdmlkdWFsSW5zdGFuY2VzW2luZGV4XS5wcm9wc1twcm9wXTtcbiAgICAgICAgcmV0dXJuIGFjYztcbiAgICAgIH0sIHt9KTtcbiAgICAgIHNpbmdsZXRvbjIuc2V0UHJvcHMoT2JqZWN0LmFzc2lnbih7fSwgb3ZlcnJpZGVQcm9wcywge1xuICAgICAgICBnZXRSZWZlcmVuY2VDbGllbnRSZWN0OiB0eXBlb2Ygb3ZlcnJpZGVQcm9wcy5nZXRSZWZlcmVuY2VDbGllbnRSZWN0ID09PSBcImZ1bmN0aW9uXCIgPyBvdmVycmlkZVByb3BzLmdldFJlZmVyZW5jZUNsaWVudFJlY3QgOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICByZXR1cm4gdGFyZ2V0LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgICAgICB9XG4gICAgICB9KSk7XG4gICAgfVxuICAgIGVuYWJsZUluc3RhbmNlcyhmYWxzZSk7XG4gICAgc2V0UmVmZXJlbmNlcygpO1xuICAgIHZhciBwbHVnaW4gPSB7XG4gICAgICBmbjogZnVuY3Rpb24gZm4oKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgb25EZXN0cm95OiBmdW5jdGlvbiBvbkRlc3Ryb3koKSB7XG4gICAgICAgICAgICBlbmFibGVJbnN0YW5jZXModHJ1ZSk7XG4gICAgICAgICAgfSxcbiAgICAgICAgICBvbkhpZGRlbjogZnVuY3Rpb24gb25IaWRkZW4oKSB7XG4gICAgICAgICAgICBjdXJyZW50VGFyZ2V0ID0gbnVsbDtcbiAgICAgICAgICB9LFxuICAgICAgICAgIG9uQ2xpY2tPdXRzaWRlOiBmdW5jdGlvbiBvbkNsaWNrT3V0c2lkZShpbnN0YW5jZSkge1xuICAgICAgICAgICAgaWYgKGluc3RhbmNlLnByb3BzLnNob3dPbkNyZWF0ZSAmJiAhc2hvd25PbkNyZWF0ZSkge1xuICAgICAgICAgICAgICBzaG93bk9uQ3JlYXRlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgY3VycmVudFRhcmdldCA9IG51bGw7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSxcbiAgICAgICAgICBvblNob3c6IGZ1bmN0aW9uIG9uU2hvdyhpbnN0YW5jZSkge1xuICAgICAgICAgICAgaWYgKGluc3RhbmNlLnByb3BzLnNob3dPbkNyZWF0ZSAmJiAhc2hvd25PbkNyZWF0ZSkge1xuICAgICAgICAgICAgICBzaG93bk9uQ3JlYXRlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgcHJlcGFyZUluc3RhbmNlKGluc3RhbmNlLCByZWZlcmVuY2VzWzBdKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9LFxuICAgICAgICAgIG9uVHJpZ2dlcjogZnVuY3Rpb24gb25UcmlnZ2VyKGluc3RhbmNlLCBldmVudCkge1xuICAgICAgICAgICAgcHJlcGFyZUluc3RhbmNlKGluc3RhbmNlLCBldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgfTtcbiAgICB2YXIgc2luZ2xldG9uID0gdGlwcHkyKGRpdigpLCBPYmplY3QuYXNzaWduKHt9LCByZW1vdmVQcm9wZXJ0aWVzKG9wdGlvbmFsUHJvcHMsIFtcIm92ZXJyaWRlc1wiXSksIHtcbiAgICAgIHBsdWdpbnM6IFtwbHVnaW5dLmNvbmNhdChvcHRpb25hbFByb3BzLnBsdWdpbnMgfHwgW10pLFxuICAgICAgdHJpZ2dlclRhcmdldDogcmVmZXJlbmNlcyxcbiAgICAgIHBvcHBlck9wdGlvbnM6IE9iamVjdC5hc3NpZ24oe30sIG9wdGlvbmFsUHJvcHMucG9wcGVyT3B0aW9ucywge1xuICAgICAgICBtb2RpZmllcnM6IFtdLmNvbmNhdCgoKF9vcHRpb25hbFByb3BzJHBvcHBlciA9IG9wdGlvbmFsUHJvcHMucG9wcGVyT3B0aW9ucykgPT0gbnVsbCA/IHZvaWQgMCA6IF9vcHRpb25hbFByb3BzJHBvcHBlci5tb2RpZmllcnMpIHx8IFtdLCBbYXBwbHlTdHlsZXNNb2RpZmllcl0pXG4gICAgICB9KVxuICAgIH0pKTtcbiAgICB2YXIgb3JpZ2luYWxTaG93ID0gc2luZ2xldG9uLnNob3c7XG4gICAgc2luZ2xldG9uLnNob3cgPSBmdW5jdGlvbih0YXJnZXQpIHtcbiAgICAgIG9yaWdpbmFsU2hvdygpO1xuICAgICAgaWYgKCFjdXJyZW50VGFyZ2V0ICYmIHRhcmdldCA9PSBudWxsKSB7XG4gICAgICAgIHJldHVybiBwcmVwYXJlSW5zdGFuY2Uoc2luZ2xldG9uLCByZWZlcmVuY2VzWzBdKTtcbiAgICAgIH1cbiAgICAgIGlmIChjdXJyZW50VGFyZ2V0ICYmIHRhcmdldCA9PSBudWxsKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGlmICh0eXBlb2YgdGFyZ2V0ID09PSBcIm51bWJlclwiKSB7XG4gICAgICAgIHJldHVybiByZWZlcmVuY2VzW3RhcmdldF0gJiYgcHJlcGFyZUluc3RhbmNlKHNpbmdsZXRvbiwgcmVmZXJlbmNlc1t0YXJnZXRdKTtcbiAgICAgIH1cbiAgICAgIGlmIChpbmRpdmlkdWFsSW5zdGFuY2VzLmluY2x1ZGVzKHRhcmdldCkpIHtcbiAgICAgICAgdmFyIHJlZiA9IHRhcmdldC5yZWZlcmVuY2U7XG4gICAgICAgIHJldHVybiBwcmVwYXJlSW5zdGFuY2Uoc2luZ2xldG9uLCByZWYpO1xuICAgICAgfVxuICAgICAgaWYgKHJlZmVyZW5jZXMuaW5jbHVkZXModGFyZ2V0KSkge1xuICAgICAgICByZXR1cm4gcHJlcGFyZUluc3RhbmNlKHNpbmdsZXRvbiwgdGFyZ2V0KTtcbiAgICAgIH1cbiAgICB9O1xuICAgIHNpbmdsZXRvbi5zaG93TmV4dCA9IGZ1bmN0aW9uKCkge1xuICAgICAgdmFyIGZpcnN0ID0gcmVmZXJlbmNlc1swXTtcbiAgICAgIGlmICghY3VycmVudFRhcmdldCkge1xuICAgICAgICByZXR1cm4gc2luZ2xldG9uLnNob3coMCk7XG4gICAgICB9XG4gICAgICB2YXIgaW5kZXggPSByZWZlcmVuY2VzLmluZGV4T2YoY3VycmVudFRhcmdldCk7XG4gICAgICBzaW5nbGV0b24uc2hvdyhyZWZlcmVuY2VzW2luZGV4ICsgMV0gfHwgZmlyc3QpO1xuICAgIH07XG4gICAgc2luZ2xldG9uLnNob3dQcmV2aW91cyA9IGZ1bmN0aW9uKCkge1xuICAgICAgdmFyIGxhc3QgPSByZWZlcmVuY2VzW3JlZmVyZW5jZXMubGVuZ3RoIC0gMV07XG4gICAgICBpZiAoIWN1cnJlbnRUYXJnZXQpIHtcbiAgICAgICAgcmV0dXJuIHNpbmdsZXRvbi5zaG93KGxhc3QpO1xuICAgICAgfVxuICAgICAgdmFyIGluZGV4ID0gcmVmZXJlbmNlcy5pbmRleE9mKGN1cnJlbnRUYXJnZXQpO1xuICAgICAgdmFyIHRhcmdldCA9IHJlZmVyZW5jZXNbaW5kZXggLSAxXSB8fCBsYXN0O1xuICAgICAgc2luZ2xldG9uLnNob3codGFyZ2V0KTtcbiAgICB9O1xuICAgIHZhciBvcmlnaW5hbFNldFByb3BzID0gc2luZ2xldG9uLnNldFByb3BzO1xuICAgIHNpbmdsZXRvbi5zZXRQcm9wcyA9IGZ1bmN0aW9uKHByb3BzKSB7XG4gICAgICBvdmVycmlkZXMgPSBwcm9wcy5vdmVycmlkZXMgfHwgb3ZlcnJpZGVzO1xuICAgICAgb3JpZ2luYWxTZXRQcm9wcyhwcm9wcyk7XG4gICAgfTtcbiAgICBzaW5nbGV0b24uc2V0SW5zdGFuY2VzID0gZnVuY3Rpb24obmV4dEluc3RhbmNlcykge1xuICAgICAgZW5hYmxlSW5zdGFuY2VzKHRydWUpO1xuICAgICAgaW50ZXJjZXB0U2V0UHJvcHNDbGVhbnVwcy5mb3JFYWNoKGZ1bmN0aW9uKGZuKSB7XG4gICAgICAgIHJldHVybiBmbigpO1xuICAgICAgfSk7XG4gICAgICBpbmRpdmlkdWFsSW5zdGFuY2VzID0gbmV4dEluc3RhbmNlcztcbiAgICAgIGVuYWJsZUluc3RhbmNlcyhmYWxzZSk7XG4gICAgICBzZXRSZWZlcmVuY2VzKCk7XG4gICAgICBpbnRlcmNlcHRTZXRQcm9wcyhzaW5nbGV0b24pO1xuICAgICAgc2luZ2xldG9uLnNldFByb3BzKHtcbiAgICAgICAgdHJpZ2dlclRhcmdldDogcmVmZXJlbmNlc1xuICAgICAgfSk7XG4gICAgfTtcbiAgICBpbnRlcmNlcHRTZXRQcm9wc0NsZWFudXBzID0gaW50ZXJjZXB0U2V0UHJvcHMoc2luZ2xldG9uKTtcbiAgICByZXR1cm4gc2luZ2xldG9uO1xuICB9O1xuICB2YXIgQlVCQkxJTkdfRVZFTlRTX01BUCA9IHtcbiAgICBtb3VzZW92ZXI6IFwibW91c2VlbnRlclwiLFxuICAgIGZvY3VzaW46IFwiZm9jdXNcIixcbiAgICBjbGljazogXCJjbGlja1wiXG4gIH07XG4gIGZ1bmN0aW9uIGRlbGVnYXRlKHRhcmdldHMsIHByb3BzKSB7XG4gICAgaWYgKHRydWUpIHtcbiAgICAgIGVycm9yV2hlbighKHByb3BzICYmIHByb3BzLnRhcmdldCksIFtcIllvdSBtdXN0IHNwZWNpdHkgYSBgdGFyZ2V0YCBwcm9wIGluZGljYXRpbmcgYSBDU1Mgc2VsZWN0b3Igc3RyaW5nIG1hdGNoaW5nXCIsIFwidGhlIHRhcmdldCBlbGVtZW50cyB0aGF0IHNob3VsZCByZWNlaXZlIGEgdGlwcHkuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICB9XG4gICAgdmFyIGxpc3RlbmVycyA9IFtdO1xuICAgIHZhciBjaGlsZFRpcHB5SW5zdGFuY2VzID0gW107XG4gICAgdmFyIGRpc2FibGVkID0gZmFsc2U7XG4gICAgdmFyIHRhcmdldCA9IHByb3BzLnRhcmdldDtcbiAgICB2YXIgbmF0aXZlUHJvcHMgPSByZW1vdmVQcm9wZXJ0aWVzKHByb3BzLCBbXCJ0YXJnZXRcIl0pO1xuICAgIHZhciBwYXJlbnRQcm9wcyA9IE9iamVjdC5hc3NpZ24oe30sIG5hdGl2ZVByb3BzLCB7XG4gICAgICB0cmlnZ2VyOiBcIm1hbnVhbFwiLFxuICAgICAgdG91Y2g6IGZhbHNlXG4gICAgfSk7XG4gICAgdmFyIGNoaWxkUHJvcHMgPSBPYmplY3QuYXNzaWduKHt9LCBuYXRpdmVQcm9wcywge1xuICAgICAgc2hvd09uQ3JlYXRlOiB0cnVlXG4gICAgfSk7XG4gICAgdmFyIHJldHVyblZhbHVlID0gdGlwcHkyKHRhcmdldHMsIHBhcmVudFByb3BzKTtcbiAgICB2YXIgbm9ybWFsaXplZFJldHVyblZhbHVlID0gbm9ybWFsaXplVG9BcnJheShyZXR1cm5WYWx1ZSk7XG4gICAgZnVuY3Rpb24gb25UcmlnZ2VyKGV2ZW50KSB7XG4gICAgICBpZiAoIWV2ZW50LnRhcmdldCB8fCBkaXNhYmxlZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB2YXIgdGFyZ2V0Tm9kZSA9IGV2ZW50LnRhcmdldC5jbG9zZXN0KHRhcmdldCk7XG4gICAgICBpZiAoIXRhcmdldE5vZGUpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIHRyaWdnZXIgPSB0YXJnZXROb2RlLmdldEF0dHJpYnV0ZShcImRhdGEtdGlwcHktdHJpZ2dlclwiKSB8fCBwcm9wcy50cmlnZ2VyIHx8IGRlZmF1bHRQcm9wcy50cmlnZ2VyO1xuICAgICAgaWYgKHRhcmdldE5vZGUuX3RpcHB5KSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGlmIChldmVudC50eXBlID09PSBcInRvdWNoc3RhcnRcIiAmJiB0eXBlb2YgY2hpbGRQcm9wcy50b3VjaCA9PT0gXCJib29sZWFuXCIpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgaWYgKGV2ZW50LnR5cGUgIT09IFwidG91Y2hzdGFydFwiICYmIHRyaWdnZXIuaW5kZXhPZihCVUJCTElOR19FVkVOVFNfTUFQW2V2ZW50LnR5cGVdKSA8IDApIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdmFyIGluc3RhbmNlID0gdGlwcHkyKHRhcmdldE5vZGUsIGNoaWxkUHJvcHMpO1xuICAgICAgaWYgKGluc3RhbmNlKSB7XG4gICAgICAgIGNoaWxkVGlwcHlJbnN0YW5jZXMgPSBjaGlsZFRpcHB5SW5zdGFuY2VzLmNvbmNhdChpbnN0YW5jZSk7XG4gICAgICB9XG4gICAgfVxuICAgIGZ1bmN0aW9uIG9uKG5vZGUsIGV2ZW50VHlwZSwgaGFuZGxlciwgb3B0aW9ucykge1xuICAgICAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgICAgICBvcHRpb25zID0gZmFsc2U7XG4gICAgICB9XG4gICAgICBub2RlLmFkZEV2ZW50TGlzdGVuZXIoZXZlbnRUeXBlLCBoYW5kbGVyLCBvcHRpb25zKTtcbiAgICAgIGxpc3RlbmVycy5wdXNoKHtcbiAgICAgICAgbm9kZSxcbiAgICAgICAgZXZlbnRUeXBlLFxuICAgICAgICBoYW5kbGVyLFxuICAgICAgICBvcHRpb25zXG4gICAgICB9KTtcbiAgICB9XG4gICAgZnVuY3Rpb24gYWRkRXZlbnRMaXN0ZW5lcnMoaW5zdGFuY2UpIHtcbiAgICAgIHZhciByZWZlcmVuY2UgPSBpbnN0YW5jZS5yZWZlcmVuY2U7XG4gICAgICBvbihyZWZlcmVuY2UsIFwidG91Y2hzdGFydFwiLCBvblRyaWdnZXIsIFRPVUNIX09QVElPTlMpO1xuICAgICAgb24ocmVmZXJlbmNlLCBcIm1vdXNlb3ZlclwiLCBvblRyaWdnZXIpO1xuICAgICAgb24ocmVmZXJlbmNlLCBcImZvY3VzaW5cIiwgb25UcmlnZ2VyKTtcbiAgICAgIG9uKHJlZmVyZW5jZSwgXCJjbGlja1wiLCBvblRyaWdnZXIpO1xuICAgIH1cbiAgICBmdW5jdGlvbiByZW1vdmVFdmVudExpc3RlbmVycygpIHtcbiAgICAgIGxpc3RlbmVycy5mb3JFYWNoKGZ1bmN0aW9uKF9yZWYpIHtcbiAgICAgICAgdmFyIG5vZGUgPSBfcmVmLm5vZGUsIGV2ZW50VHlwZSA9IF9yZWYuZXZlbnRUeXBlLCBoYW5kbGVyID0gX3JlZi5oYW5kbGVyLCBvcHRpb25zID0gX3JlZi5vcHRpb25zO1xuICAgICAgICBub2RlLnJlbW92ZUV2ZW50TGlzdGVuZXIoZXZlbnRUeXBlLCBoYW5kbGVyLCBvcHRpb25zKTtcbiAgICAgIH0pO1xuICAgICAgbGlzdGVuZXJzID0gW107XG4gICAgfVxuICAgIGZ1bmN0aW9uIGFwcGx5TXV0YXRpb25zKGluc3RhbmNlKSB7XG4gICAgICB2YXIgb3JpZ2luYWxEZXN0cm95ID0gaW5zdGFuY2UuZGVzdHJveTtcbiAgICAgIHZhciBvcmlnaW5hbEVuYWJsZSA9IGluc3RhbmNlLmVuYWJsZTtcbiAgICAgIHZhciBvcmlnaW5hbERpc2FibGUgPSBpbnN0YW5jZS5kaXNhYmxlO1xuICAgICAgaW5zdGFuY2UuZGVzdHJveSA9IGZ1bmN0aW9uKHNob3VsZERlc3Ryb3lDaGlsZEluc3RhbmNlcykge1xuICAgICAgICBpZiAoc2hvdWxkRGVzdHJveUNoaWxkSW5zdGFuY2VzID09PSB2b2lkIDApIHtcbiAgICAgICAgICBzaG91bGREZXN0cm95Q2hpbGRJbnN0YW5jZXMgPSB0cnVlO1xuICAgICAgICB9XG4gICAgICAgIGlmIChzaG91bGREZXN0cm95Q2hpbGRJbnN0YW5jZXMpIHtcbiAgICAgICAgICBjaGlsZFRpcHB5SW5zdGFuY2VzLmZvckVhY2goZnVuY3Rpb24oaW5zdGFuY2UyKSB7XG4gICAgICAgICAgICBpbnN0YW5jZTIuZGVzdHJveSgpO1xuICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICAgIGNoaWxkVGlwcHlJbnN0YW5jZXMgPSBbXTtcbiAgICAgICAgcmVtb3ZlRXZlbnRMaXN0ZW5lcnMoKTtcbiAgICAgICAgb3JpZ2luYWxEZXN0cm95KCk7XG4gICAgICB9O1xuICAgICAgaW5zdGFuY2UuZW5hYmxlID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIG9yaWdpbmFsRW5hYmxlKCk7XG4gICAgICAgIGNoaWxkVGlwcHlJbnN0YW5jZXMuZm9yRWFjaChmdW5jdGlvbihpbnN0YW5jZTIpIHtcbiAgICAgICAgICByZXR1cm4gaW5zdGFuY2UyLmVuYWJsZSgpO1xuICAgICAgICB9KTtcbiAgICAgICAgZGlzYWJsZWQgPSBmYWxzZTtcbiAgICAgIH07XG4gICAgICBpbnN0YW5jZS5kaXNhYmxlID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIG9yaWdpbmFsRGlzYWJsZSgpO1xuICAgICAgICBjaGlsZFRpcHB5SW5zdGFuY2VzLmZvckVhY2goZnVuY3Rpb24oaW5zdGFuY2UyKSB7XG4gICAgICAgICAgcmV0dXJuIGluc3RhbmNlMi5kaXNhYmxlKCk7XG4gICAgICAgIH0pO1xuICAgICAgICBkaXNhYmxlZCA9IHRydWU7XG4gICAgICB9O1xuICAgICAgYWRkRXZlbnRMaXN0ZW5lcnMoaW5zdGFuY2UpO1xuICAgIH1cbiAgICBub3JtYWxpemVkUmV0dXJuVmFsdWUuZm9yRWFjaChhcHBseU11dGF0aW9ucyk7XG4gICAgcmV0dXJuIHJldHVyblZhbHVlO1xuICB9XG4gIHZhciBhbmltYXRlRmlsbCA9IHtcbiAgICBuYW1lOiBcImFuaW1hdGVGaWxsXCIsXG4gICAgZGVmYXVsdFZhbHVlOiBmYWxzZSxcbiAgICBmbjogZnVuY3Rpb24gZm4oaW5zdGFuY2UpIHtcbiAgICAgIHZhciBfaW5zdGFuY2UkcHJvcHMkcmVuZGU7XG4gICAgICBpZiAoISgoX2luc3RhbmNlJHByb3BzJHJlbmRlID0gaW5zdGFuY2UucHJvcHMucmVuZGVyKSA9PSBudWxsID8gdm9pZCAwIDogX2luc3RhbmNlJHByb3BzJHJlbmRlLiQkdGlwcHkpKSB7XG4gICAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgICAgZXJyb3JXaGVuKGluc3RhbmNlLnByb3BzLmFuaW1hdGVGaWxsLCBcIlRoZSBgYW5pbWF0ZUZpbGxgIHBsdWdpbiByZXF1aXJlcyB0aGUgZGVmYXVsdCByZW5kZXIgZnVuY3Rpb24uXCIpO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB7fTtcbiAgICAgIH1cbiAgICAgIHZhciBfZ2V0Q2hpbGRyZW4gPSBnZXRDaGlsZHJlbihpbnN0YW5jZS5wb3BwZXIpLCBib3ggPSBfZ2V0Q2hpbGRyZW4uYm94LCBjb250ZW50ID0gX2dldENoaWxkcmVuLmNvbnRlbnQ7XG4gICAgICB2YXIgYmFja2Ryb3AgPSBpbnN0YW5jZS5wcm9wcy5hbmltYXRlRmlsbCA/IGNyZWF0ZUJhY2tkcm9wRWxlbWVudCgpIDogbnVsbDtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIG9uQ3JlYXRlOiBmdW5jdGlvbiBvbkNyZWF0ZSgpIHtcbiAgICAgICAgICBpZiAoYmFja2Ryb3ApIHtcbiAgICAgICAgICAgIGJveC5pbnNlcnRCZWZvcmUoYmFja2Ryb3AsIGJveC5maXJzdEVsZW1lbnRDaGlsZCk7XG4gICAgICAgICAgICBib3guc2V0QXR0cmlidXRlKFwiZGF0YS1hbmltYXRlZmlsbFwiLCBcIlwiKTtcbiAgICAgICAgICAgIGJveC5zdHlsZS5vdmVyZmxvdyA9IFwiaGlkZGVuXCI7XG4gICAgICAgICAgICBpbnN0YW5jZS5zZXRQcm9wcyh7XG4gICAgICAgICAgICAgIGFycm93OiBmYWxzZSxcbiAgICAgICAgICAgICAgYW5pbWF0aW9uOiBcInNoaWZ0LWF3YXlcIlxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBvbk1vdW50OiBmdW5jdGlvbiBvbk1vdW50KCkge1xuICAgICAgICAgIGlmIChiYWNrZHJvcCkge1xuICAgICAgICAgICAgdmFyIHRyYW5zaXRpb25EdXJhdGlvbiA9IGJveC5zdHlsZS50cmFuc2l0aW9uRHVyYXRpb247XG4gICAgICAgICAgICB2YXIgZHVyYXRpb24gPSBOdW1iZXIodHJhbnNpdGlvbkR1cmF0aW9uLnJlcGxhY2UoXCJtc1wiLCBcIlwiKSk7XG4gICAgICAgICAgICBjb250ZW50LnN0eWxlLnRyYW5zaXRpb25EZWxheSA9IE1hdGgucm91bmQoZHVyYXRpb24gLyAxMCkgKyBcIm1zXCI7XG4gICAgICAgICAgICBiYWNrZHJvcC5zdHlsZS50cmFuc2l0aW9uRHVyYXRpb24gPSB0cmFuc2l0aW9uRHVyYXRpb247XG4gICAgICAgICAgICBzZXRWaXNpYmlsaXR5U3RhdGUoW2JhY2tkcm9wXSwgXCJ2aXNpYmxlXCIpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgb25TaG93OiBmdW5jdGlvbiBvblNob3coKSB7XG4gICAgICAgICAgaWYgKGJhY2tkcm9wKSB7XG4gICAgICAgICAgICBiYWNrZHJvcC5zdHlsZS50cmFuc2l0aW9uRHVyYXRpb24gPSBcIjBtc1wiO1xuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgb25IaWRlOiBmdW5jdGlvbiBvbkhpZGUoKSB7XG4gICAgICAgICAgaWYgKGJhY2tkcm9wKSB7XG4gICAgICAgICAgICBzZXRWaXNpYmlsaXR5U3RhdGUoW2JhY2tkcm9wXSwgXCJoaWRkZW5cIik7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9O1xuICAgIH1cbiAgfTtcbiAgZnVuY3Rpb24gY3JlYXRlQmFja2Ryb3BFbGVtZW50KCkge1xuICAgIHZhciBiYWNrZHJvcCA9IGRpdigpO1xuICAgIGJhY2tkcm9wLmNsYXNzTmFtZSA9IEJBQ0tEUk9QX0NMQVNTO1xuICAgIHNldFZpc2liaWxpdHlTdGF0ZShbYmFja2Ryb3BdLCBcImhpZGRlblwiKTtcbiAgICByZXR1cm4gYmFja2Ryb3A7XG4gIH1cbiAgdmFyIG1vdXNlQ29vcmRzID0ge1xuICAgIGNsaWVudFg6IDAsXG4gICAgY2xpZW50WTogMFxuICB9O1xuICB2YXIgYWN0aXZlSW5zdGFuY2VzID0gW107XG4gIGZ1bmN0aW9uIHN0b3JlTW91c2VDb29yZHMoX3JlZikge1xuICAgIHZhciBjbGllbnRYID0gX3JlZi5jbGllbnRYLCBjbGllbnRZID0gX3JlZi5jbGllbnRZO1xuICAgIG1vdXNlQ29vcmRzID0ge1xuICAgICAgY2xpZW50WCxcbiAgICAgIGNsaWVudFlcbiAgICB9O1xuICB9XG4gIGZ1bmN0aW9uIGFkZE1vdXNlQ29vcmRzTGlzdGVuZXIoZG9jKSB7XG4gICAgZG9jLmFkZEV2ZW50TGlzdGVuZXIoXCJtb3VzZW1vdmVcIiwgc3RvcmVNb3VzZUNvb3Jkcyk7XG4gIH1cbiAgZnVuY3Rpb24gcmVtb3ZlTW91c2VDb29yZHNMaXN0ZW5lcihkb2MpIHtcbiAgICBkb2MucmVtb3ZlRXZlbnRMaXN0ZW5lcihcIm1vdXNlbW92ZVwiLCBzdG9yZU1vdXNlQ29vcmRzKTtcbiAgfVxuICB2YXIgZm9sbG93Q3Vyc29yMiA9IHtcbiAgICBuYW1lOiBcImZvbGxvd0N1cnNvclwiLFxuICAgIGRlZmF1bHRWYWx1ZTogZmFsc2UsXG4gICAgZm46IGZ1bmN0aW9uIGZuKGluc3RhbmNlKSB7XG4gICAgICB2YXIgcmVmZXJlbmNlID0gaW5zdGFuY2UucmVmZXJlbmNlO1xuICAgICAgdmFyIGRvYyA9IGdldE93bmVyRG9jdW1lbnQoaW5zdGFuY2UucHJvcHMudHJpZ2dlclRhcmdldCB8fCByZWZlcmVuY2UpO1xuICAgICAgdmFyIGlzSW50ZXJuYWxVcGRhdGUgPSBmYWxzZTtcbiAgICAgIHZhciB3YXNGb2N1c0V2ZW50ID0gZmFsc2U7XG4gICAgICB2YXIgaXNVbm1vdW50ZWQgPSB0cnVlO1xuICAgICAgdmFyIHByZXZQcm9wcyA9IGluc3RhbmNlLnByb3BzO1xuICAgICAgZnVuY3Rpb24gZ2V0SXNJbml0aWFsQmVoYXZpb3IoKSB7XG4gICAgICAgIHJldHVybiBpbnN0YW5jZS5wcm9wcy5mb2xsb3dDdXJzb3IgPT09IFwiaW5pdGlhbFwiICYmIGluc3RhbmNlLnN0YXRlLmlzVmlzaWJsZTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIGFkZExpc3RlbmVyKCkge1xuICAgICAgICBkb2MuYWRkRXZlbnRMaXN0ZW5lcihcIm1vdXNlbW92ZVwiLCBvbk1vdXNlTW92ZSk7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiByZW1vdmVMaXN0ZW5lcigpIHtcbiAgICAgICAgZG9jLnJlbW92ZUV2ZW50TGlzdGVuZXIoXCJtb3VzZW1vdmVcIiwgb25Nb3VzZU1vdmUpO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gdW5zZXRHZXRSZWZlcmVuY2VDbGllbnRSZWN0KCkge1xuICAgICAgICBpc0ludGVybmFsVXBkYXRlID0gdHJ1ZTtcbiAgICAgICAgaW5zdGFuY2Uuc2V0UHJvcHMoe1xuICAgICAgICAgIGdldFJlZmVyZW5jZUNsaWVudFJlY3Q6IG51bGxcbiAgICAgICAgfSk7XG4gICAgICAgIGlzSW50ZXJuYWxVcGRhdGUgPSBmYWxzZTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIG9uTW91c2VNb3ZlKGV2ZW50KSB7XG4gICAgICAgIHZhciBpc0N1cnNvck92ZXJSZWZlcmVuY2UgPSBldmVudC50YXJnZXQgPyByZWZlcmVuY2UuY29udGFpbnMoZXZlbnQudGFyZ2V0KSA6IHRydWU7XG4gICAgICAgIHZhciBmb2xsb3dDdXJzb3IzID0gaW5zdGFuY2UucHJvcHMuZm9sbG93Q3Vyc29yO1xuICAgICAgICB2YXIgY2xpZW50WCA9IGV2ZW50LmNsaWVudFgsIGNsaWVudFkgPSBldmVudC5jbGllbnRZO1xuICAgICAgICB2YXIgcmVjdCA9IHJlZmVyZW5jZS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKTtcbiAgICAgICAgdmFyIHJlbGF0aXZlWCA9IGNsaWVudFggLSByZWN0LmxlZnQ7XG4gICAgICAgIHZhciByZWxhdGl2ZVkgPSBjbGllbnRZIC0gcmVjdC50b3A7XG4gICAgICAgIGlmIChpc0N1cnNvck92ZXJSZWZlcmVuY2UgfHwgIWluc3RhbmNlLnByb3BzLmludGVyYWN0aXZlKSB7XG4gICAgICAgICAgaW5zdGFuY2Uuc2V0UHJvcHMoe1xuICAgICAgICAgICAgZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdDogZnVuY3Rpb24gZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdCgpIHtcbiAgICAgICAgICAgICAgdmFyIHJlY3QyID0gcmVmZXJlbmNlLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICAgICAgICAgICAgICB2YXIgeCA9IGNsaWVudFg7XG4gICAgICAgICAgICAgIHZhciB5ID0gY2xpZW50WTtcbiAgICAgICAgICAgICAgaWYgKGZvbGxvd0N1cnNvcjMgPT09IFwiaW5pdGlhbFwiKSB7XG4gICAgICAgICAgICAgICAgeCA9IHJlY3QyLmxlZnQgKyByZWxhdGl2ZVg7XG4gICAgICAgICAgICAgICAgeSA9IHJlY3QyLnRvcCArIHJlbGF0aXZlWTtcbiAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICB2YXIgdG9wID0gZm9sbG93Q3Vyc29yMyA9PT0gXCJob3Jpem9udGFsXCIgPyByZWN0Mi50b3AgOiB5O1xuICAgICAgICAgICAgICB2YXIgcmlnaHQgPSBmb2xsb3dDdXJzb3IzID09PSBcInZlcnRpY2FsXCIgPyByZWN0Mi5yaWdodCA6IHg7XG4gICAgICAgICAgICAgIHZhciBib3R0b20gPSBmb2xsb3dDdXJzb3IzID09PSBcImhvcml6b250YWxcIiA/IHJlY3QyLmJvdHRvbSA6IHk7XG4gICAgICAgICAgICAgIHZhciBsZWZ0ID0gZm9sbG93Q3Vyc29yMyA9PT0gXCJ2ZXJ0aWNhbFwiID8gcmVjdDIubGVmdCA6IHg7XG4gICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgd2lkdGg6IHJpZ2h0IC0gbGVmdCxcbiAgICAgICAgICAgICAgICBoZWlnaHQ6IGJvdHRvbSAtIHRvcCxcbiAgICAgICAgICAgICAgICB0b3AsXG4gICAgICAgICAgICAgICAgcmlnaHQsXG4gICAgICAgICAgICAgICAgYm90dG9tLFxuICAgICAgICAgICAgICAgIGxlZnRcbiAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgZnVuY3Rpb24gY3JlYXRlKCkge1xuICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuZm9sbG93Q3Vyc29yKSB7XG4gICAgICAgICAgYWN0aXZlSW5zdGFuY2VzLnB1c2goe1xuICAgICAgICAgICAgaW5zdGFuY2UsXG4gICAgICAgICAgICBkb2NcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBhZGRNb3VzZUNvb3Jkc0xpc3RlbmVyKGRvYyk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIGRlc3Ryb3koKSB7XG4gICAgICAgIGFjdGl2ZUluc3RhbmNlcyA9IGFjdGl2ZUluc3RhbmNlcy5maWx0ZXIoZnVuY3Rpb24oZGF0YSkge1xuICAgICAgICAgIHJldHVybiBkYXRhLmluc3RhbmNlICE9PSBpbnN0YW5jZTtcbiAgICAgICAgfSk7XG4gICAgICAgIGlmIChhY3RpdmVJbnN0YW5jZXMuZmlsdGVyKGZ1bmN0aW9uKGRhdGEpIHtcbiAgICAgICAgICByZXR1cm4gZGF0YS5kb2MgPT09IGRvYztcbiAgICAgICAgfSkubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgcmVtb3ZlTW91c2VDb29yZHNMaXN0ZW5lcihkb2MpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4ge1xuICAgICAgICBvbkNyZWF0ZTogY3JlYXRlLFxuICAgICAgICBvbkRlc3Ryb3k6IGRlc3Ryb3ksXG4gICAgICAgIG9uQmVmb3JlVXBkYXRlOiBmdW5jdGlvbiBvbkJlZm9yZVVwZGF0ZSgpIHtcbiAgICAgICAgICBwcmV2UHJvcHMgPSBpbnN0YW5jZS5wcm9wcztcbiAgICAgICAgfSxcbiAgICAgICAgb25BZnRlclVwZGF0ZTogZnVuY3Rpb24gb25BZnRlclVwZGF0ZShfLCBfcmVmMikge1xuICAgICAgICAgIHZhciBmb2xsb3dDdXJzb3IzID0gX3JlZjIuZm9sbG93Q3Vyc29yO1xuICAgICAgICAgIGlmIChpc0ludGVybmFsVXBkYXRlKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmIChmb2xsb3dDdXJzb3IzICE9PSB2b2lkIDAgJiYgcHJldlByb3BzLmZvbGxvd0N1cnNvciAhPT0gZm9sbG93Q3Vyc29yMykge1xuICAgICAgICAgICAgZGVzdHJveSgpO1xuICAgICAgICAgICAgaWYgKGZvbGxvd0N1cnNvcjMpIHtcbiAgICAgICAgICAgICAgY3JlYXRlKCk7XG4gICAgICAgICAgICAgIGlmIChpbnN0YW5jZS5zdGF0ZS5pc01vdW50ZWQgJiYgIXdhc0ZvY3VzRXZlbnQgJiYgIWdldElzSW5pdGlhbEJlaGF2aW9yKCkpIHtcbiAgICAgICAgICAgICAgICBhZGRMaXN0ZW5lcigpO1xuICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICByZW1vdmVMaXN0ZW5lcigpO1xuICAgICAgICAgICAgICB1bnNldEdldFJlZmVyZW5jZUNsaWVudFJlY3QoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIG9uTW91bnQ6IGZ1bmN0aW9uIG9uTW91bnQoKSB7XG4gICAgICAgICAgaWYgKGluc3RhbmNlLnByb3BzLmZvbGxvd0N1cnNvciAmJiAhd2FzRm9jdXNFdmVudCkge1xuICAgICAgICAgICAgaWYgKGlzVW5tb3VudGVkKSB7XG4gICAgICAgICAgICAgIG9uTW91c2VNb3ZlKG1vdXNlQ29vcmRzKTtcbiAgICAgICAgICAgICAgaXNVbm1vdW50ZWQgPSBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmICghZ2V0SXNJbml0aWFsQmVoYXZpb3IoKSkge1xuICAgICAgICAgICAgICBhZGRMaXN0ZW5lcigpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgb25UcmlnZ2VyOiBmdW5jdGlvbiBvblRyaWdnZXIoXywgZXZlbnQpIHtcbiAgICAgICAgICBpZiAoaXNNb3VzZUV2ZW50KGV2ZW50KSkge1xuICAgICAgICAgICAgbW91c2VDb29yZHMgPSB7XG4gICAgICAgICAgICAgIGNsaWVudFg6IGV2ZW50LmNsaWVudFgsXG4gICAgICAgICAgICAgIGNsaWVudFk6IGV2ZW50LmNsaWVudFlcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgfVxuICAgICAgICAgIHdhc0ZvY3VzRXZlbnQgPSBldmVudC50eXBlID09PSBcImZvY3VzXCI7XG4gICAgICAgIH0sXG4gICAgICAgIG9uSGlkZGVuOiBmdW5jdGlvbiBvbkhpZGRlbigpIHtcbiAgICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuZm9sbG93Q3Vyc29yKSB7XG4gICAgICAgICAgICB1bnNldEdldFJlZmVyZW5jZUNsaWVudFJlY3QoKTtcbiAgICAgICAgICAgIHJlbW92ZUxpc3RlbmVyKCk7XG4gICAgICAgICAgICBpc1VubW91bnRlZCA9IHRydWU7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9O1xuICAgIH1cbiAgfTtcbiAgZnVuY3Rpb24gZ2V0UHJvcHMocHJvcHMsIG1vZGlmaWVyKSB7XG4gICAgdmFyIF9wcm9wcyRwb3BwZXJPcHRpb25zO1xuICAgIHJldHVybiB7XG4gICAgICBwb3BwZXJPcHRpb25zOiBPYmplY3QuYXNzaWduKHt9LCBwcm9wcy5wb3BwZXJPcHRpb25zLCB7XG4gICAgICAgIG1vZGlmaWVyczogW10uY29uY2F0KCgoKF9wcm9wcyRwb3BwZXJPcHRpb25zID0gcHJvcHMucG9wcGVyT3B0aW9ucykgPT0gbnVsbCA/IHZvaWQgMCA6IF9wcm9wcyRwb3BwZXJPcHRpb25zLm1vZGlmaWVycykgfHwgW10pLmZpbHRlcihmdW5jdGlvbihfcmVmKSB7XG4gICAgICAgICAgdmFyIG5hbWUgPSBfcmVmLm5hbWU7XG4gICAgICAgICAgcmV0dXJuIG5hbWUgIT09IG1vZGlmaWVyLm5hbWU7XG4gICAgICAgIH0pLCBbbW9kaWZpZXJdKVxuICAgICAgfSlcbiAgICB9O1xuICB9XG4gIHZhciBpbmxpbmVQb3NpdGlvbmluZyA9IHtcbiAgICBuYW1lOiBcImlubGluZVBvc2l0aW9uaW5nXCIsXG4gICAgZGVmYXVsdFZhbHVlOiBmYWxzZSxcbiAgICBmbjogZnVuY3Rpb24gZm4oaW5zdGFuY2UpIHtcbiAgICAgIHZhciByZWZlcmVuY2UgPSBpbnN0YW5jZS5yZWZlcmVuY2U7XG4gICAgICBmdW5jdGlvbiBpc0VuYWJsZWQoKSB7XG4gICAgICAgIHJldHVybiAhIWluc3RhbmNlLnByb3BzLmlubGluZVBvc2l0aW9uaW5nO1xuICAgICAgfVxuICAgICAgdmFyIHBsYWNlbWVudDtcbiAgICAgIHZhciBjdXJzb3JSZWN0SW5kZXggPSAtMTtcbiAgICAgIHZhciBpc0ludGVybmFsVXBkYXRlID0gZmFsc2U7XG4gICAgICB2YXIgbW9kaWZpZXIgPSB7XG4gICAgICAgIG5hbWU6IFwidGlwcHlJbmxpbmVQb3NpdGlvbmluZ1wiLFxuICAgICAgICBlbmFibGVkOiB0cnVlLFxuICAgICAgICBwaGFzZTogXCJhZnRlcldyaXRlXCIsXG4gICAgICAgIGZuOiBmdW5jdGlvbiBmbjIoX3JlZjIpIHtcbiAgICAgICAgICB2YXIgc3RhdGUgPSBfcmVmMi5zdGF0ZTtcbiAgICAgICAgICBpZiAoaXNFbmFibGVkKCkpIHtcbiAgICAgICAgICAgIGlmIChwbGFjZW1lbnQgIT09IHN0YXRlLnBsYWNlbWVudCkge1xuICAgICAgICAgICAgICBpbnN0YW5jZS5zZXRQcm9wcyh7XG4gICAgICAgICAgICAgICAgZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdDogZnVuY3Rpb24gZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdCgpIHtcbiAgICAgICAgICAgICAgICAgIHJldHVybiBfZ2V0UmVmZXJlbmNlQ2xpZW50UmVjdChzdGF0ZS5wbGFjZW1lbnQpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBwbGFjZW1lbnQgPSBzdGF0ZS5wbGFjZW1lbnQ7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9O1xuICAgICAgZnVuY3Rpb24gX2dldFJlZmVyZW5jZUNsaWVudFJlY3QocGxhY2VtZW50Mikge1xuICAgICAgICByZXR1cm4gZ2V0SW5saW5lQm91bmRpbmdDbGllbnRSZWN0KGdldEJhc2VQbGFjZW1lbnQocGxhY2VtZW50MiksIHJlZmVyZW5jZS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKSwgYXJyYXlGcm9tKHJlZmVyZW5jZS5nZXRDbGllbnRSZWN0cygpKSwgY3Vyc29yUmVjdEluZGV4KTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIHNldEludGVybmFsUHJvcHMocGFydGlhbFByb3BzKSB7XG4gICAgICAgIGlzSW50ZXJuYWxVcGRhdGUgPSB0cnVlO1xuICAgICAgICBpbnN0YW5jZS5zZXRQcm9wcyhwYXJ0aWFsUHJvcHMpO1xuICAgICAgICBpc0ludGVybmFsVXBkYXRlID0gZmFsc2U7XG4gICAgICB9XG4gICAgICBmdW5jdGlvbiBhZGRNb2RpZmllcigpIHtcbiAgICAgICAgaWYgKCFpc0ludGVybmFsVXBkYXRlKSB7XG4gICAgICAgICAgc2V0SW50ZXJuYWxQcm9wcyhnZXRQcm9wcyhpbnN0YW5jZS5wcm9wcywgbW9kaWZpZXIpKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgcmV0dXJuIHtcbiAgICAgICAgb25DcmVhdGU6IGFkZE1vZGlmaWVyLFxuICAgICAgICBvbkFmdGVyVXBkYXRlOiBhZGRNb2RpZmllcixcbiAgICAgICAgb25UcmlnZ2VyOiBmdW5jdGlvbiBvblRyaWdnZXIoXywgZXZlbnQpIHtcbiAgICAgICAgICBpZiAoaXNNb3VzZUV2ZW50KGV2ZW50KSkge1xuICAgICAgICAgICAgdmFyIHJlY3RzID0gYXJyYXlGcm9tKGluc3RhbmNlLnJlZmVyZW5jZS5nZXRDbGllbnRSZWN0cygpKTtcbiAgICAgICAgICAgIHZhciBjdXJzb3JSZWN0ID0gcmVjdHMuZmluZChmdW5jdGlvbihyZWN0KSB7XG4gICAgICAgICAgICAgIHJldHVybiByZWN0LmxlZnQgLSAyIDw9IGV2ZW50LmNsaWVudFggJiYgcmVjdC5yaWdodCArIDIgPj0gZXZlbnQuY2xpZW50WCAmJiByZWN0LnRvcCAtIDIgPD0gZXZlbnQuY2xpZW50WSAmJiByZWN0LmJvdHRvbSArIDIgPj0gZXZlbnQuY2xpZW50WTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgY3Vyc29yUmVjdEluZGV4ID0gcmVjdHMuaW5kZXhPZihjdXJzb3JSZWN0KTtcbiAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgICAgIG9uVW50cmlnZ2VyOiBmdW5jdGlvbiBvblVudHJpZ2dlcigpIHtcbiAgICAgICAgICBjdXJzb3JSZWN0SW5kZXggPSAtMTtcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICB9XG4gIH07XG4gIGZ1bmN0aW9uIGdldElubGluZUJvdW5kaW5nQ2xpZW50UmVjdChjdXJyZW50QmFzZVBsYWNlbWVudCwgYm91bmRpbmdSZWN0LCBjbGllbnRSZWN0cywgY3Vyc29yUmVjdEluZGV4KSB7XG4gICAgaWYgKGNsaWVudFJlY3RzLmxlbmd0aCA8IDIgfHwgY3VycmVudEJhc2VQbGFjZW1lbnQgPT09IG51bGwpIHtcbiAgICAgIHJldHVybiBib3VuZGluZ1JlY3Q7XG4gICAgfVxuICAgIGlmIChjbGllbnRSZWN0cy5sZW5ndGggPT09IDIgJiYgY3Vyc29yUmVjdEluZGV4ID49IDAgJiYgY2xpZW50UmVjdHNbMF0ubGVmdCA+IGNsaWVudFJlY3RzWzFdLnJpZ2h0KSB7XG4gICAgICByZXR1cm4gY2xpZW50UmVjdHNbY3Vyc29yUmVjdEluZGV4XSB8fCBib3VuZGluZ1JlY3Q7XG4gICAgfVxuICAgIHN3aXRjaCAoY3VycmVudEJhc2VQbGFjZW1lbnQpIHtcbiAgICAgIGNhc2UgXCJ0b3BcIjpcbiAgICAgIGNhc2UgXCJib3R0b21cIjoge1xuICAgICAgICB2YXIgZmlyc3RSZWN0ID0gY2xpZW50UmVjdHNbMF07XG4gICAgICAgIHZhciBsYXN0UmVjdCA9IGNsaWVudFJlY3RzW2NsaWVudFJlY3RzLmxlbmd0aCAtIDFdO1xuICAgICAgICB2YXIgaXNUb3AgPSBjdXJyZW50QmFzZVBsYWNlbWVudCA9PT0gXCJ0b3BcIjtcbiAgICAgICAgdmFyIHRvcCA9IGZpcnN0UmVjdC50b3A7XG4gICAgICAgIHZhciBib3R0b20gPSBsYXN0UmVjdC5ib3R0b207XG4gICAgICAgIHZhciBsZWZ0ID0gaXNUb3AgPyBmaXJzdFJlY3QubGVmdCA6IGxhc3RSZWN0LmxlZnQ7XG4gICAgICAgIHZhciByaWdodCA9IGlzVG9wID8gZmlyc3RSZWN0LnJpZ2h0IDogbGFzdFJlY3QucmlnaHQ7XG4gICAgICAgIHZhciB3aWR0aCA9IHJpZ2h0IC0gbGVmdDtcbiAgICAgICAgdmFyIGhlaWdodCA9IGJvdHRvbSAtIHRvcDtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICB0b3AsXG4gICAgICAgICAgYm90dG9tLFxuICAgICAgICAgIGxlZnQsXG4gICAgICAgICAgcmlnaHQsXG4gICAgICAgICAgd2lkdGgsXG4gICAgICAgICAgaGVpZ2h0XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICBjYXNlIFwibGVmdFwiOlxuICAgICAgY2FzZSBcInJpZ2h0XCI6IHtcbiAgICAgICAgdmFyIG1pbkxlZnQgPSBNYXRoLm1pbi5hcHBseShNYXRoLCBjbGllbnRSZWN0cy5tYXAoZnVuY3Rpb24ocmVjdHMpIHtcbiAgICAgICAgICByZXR1cm4gcmVjdHMubGVmdDtcbiAgICAgICAgfSkpO1xuICAgICAgICB2YXIgbWF4UmlnaHQgPSBNYXRoLm1heC5hcHBseShNYXRoLCBjbGllbnRSZWN0cy5tYXAoZnVuY3Rpb24ocmVjdHMpIHtcbiAgICAgICAgICByZXR1cm4gcmVjdHMucmlnaHQ7XG4gICAgICAgIH0pKTtcbiAgICAgICAgdmFyIG1lYXN1cmVSZWN0cyA9IGNsaWVudFJlY3RzLmZpbHRlcihmdW5jdGlvbihyZWN0KSB7XG4gICAgICAgICAgcmV0dXJuIGN1cnJlbnRCYXNlUGxhY2VtZW50ID09PSBcImxlZnRcIiA/IHJlY3QubGVmdCA9PT0gbWluTGVmdCA6IHJlY3QucmlnaHQgPT09IG1heFJpZ2h0O1xuICAgICAgICB9KTtcbiAgICAgICAgdmFyIF90b3AgPSBtZWFzdXJlUmVjdHNbMF0udG9wO1xuICAgICAgICB2YXIgX2JvdHRvbSA9IG1lYXN1cmVSZWN0c1ttZWFzdXJlUmVjdHMubGVuZ3RoIC0gMV0uYm90dG9tO1xuICAgICAgICB2YXIgX2xlZnQgPSBtaW5MZWZ0O1xuICAgICAgICB2YXIgX3JpZ2h0ID0gbWF4UmlnaHQ7XG4gICAgICAgIHZhciBfd2lkdGggPSBfcmlnaHQgLSBfbGVmdDtcbiAgICAgICAgdmFyIF9oZWlnaHQgPSBfYm90dG9tIC0gX3RvcDtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICB0b3A6IF90b3AsXG4gICAgICAgICAgYm90dG9tOiBfYm90dG9tLFxuICAgICAgICAgIGxlZnQ6IF9sZWZ0LFxuICAgICAgICAgIHJpZ2h0OiBfcmlnaHQsXG4gICAgICAgICAgd2lkdGg6IF93aWR0aCxcbiAgICAgICAgICBoZWlnaHQ6IF9oZWlnaHRcbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIGRlZmF1bHQ6IHtcbiAgICAgICAgcmV0dXJuIGJvdW5kaW5nUmVjdDtcbiAgICAgIH1cbiAgICB9XG4gIH1cbiAgdmFyIHN0aWNreSA9IHtcbiAgICBuYW1lOiBcInN0aWNreVwiLFxuICAgIGRlZmF1bHRWYWx1ZTogZmFsc2UsXG4gICAgZm46IGZ1bmN0aW9uIGZuKGluc3RhbmNlKSB7XG4gICAgICB2YXIgcmVmZXJlbmNlID0gaW5zdGFuY2UucmVmZXJlbmNlLCBwb3BwZXIgPSBpbnN0YW5jZS5wb3BwZXI7XG4gICAgICBmdW5jdGlvbiBnZXRSZWZlcmVuY2UoKSB7XG4gICAgICAgIHJldHVybiBpbnN0YW5jZS5wb3BwZXJJbnN0YW5jZSA/IGluc3RhbmNlLnBvcHBlckluc3RhbmNlLnN0YXRlLmVsZW1lbnRzLnJlZmVyZW5jZSA6IHJlZmVyZW5jZTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIHNob3VsZENoZWNrKHZhbHVlKSB7XG4gICAgICAgIHJldHVybiBpbnN0YW5jZS5wcm9wcy5zdGlja3kgPT09IHRydWUgfHwgaW5zdGFuY2UucHJvcHMuc3RpY2t5ID09PSB2YWx1ZTtcbiAgICAgIH1cbiAgICAgIHZhciBwcmV2UmVmUmVjdCA9IG51bGw7XG4gICAgICB2YXIgcHJldlBvcFJlY3QgPSBudWxsO1xuICAgICAgZnVuY3Rpb24gdXBkYXRlUG9zaXRpb24oKSB7XG4gICAgICAgIHZhciBjdXJyZW50UmVmUmVjdCA9IHNob3VsZENoZWNrKFwicmVmZXJlbmNlXCIpID8gZ2V0UmVmZXJlbmNlKCkuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkgOiBudWxsO1xuICAgICAgICB2YXIgY3VycmVudFBvcFJlY3QgPSBzaG91bGRDaGVjayhcInBvcHBlclwiKSA/IHBvcHBlci5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKSA6IG51bGw7XG4gICAgICAgIGlmIChjdXJyZW50UmVmUmVjdCAmJiBhcmVSZWN0c0RpZmZlcmVudChwcmV2UmVmUmVjdCwgY3VycmVudFJlZlJlY3QpIHx8IGN1cnJlbnRQb3BSZWN0ICYmIGFyZVJlY3RzRGlmZmVyZW50KHByZXZQb3BSZWN0LCBjdXJyZW50UG9wUmVjdCkpIHtcbiAgICAgICAgICBpZiAoaW5zdGFuY2UucG9wcGVySW5zdGFuY2UpIHtcbiAgICAgICAgICAgIGluc3RhbmNlLnBvcHBlckluc3RhbmNlLnVwZGF0ZSgpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgICBwcmV2UmVmUmVjdCA9IGN1cnJlbnRSZWZSZWN0O1xuICAgICAgICBwcmV2UG9wUmVjdCA9IGN1cnJlbnRQb3BSZWN0O1xuICAgICAgICBpZiAoaW5zdGFuY2Uuc3RhdGUuaXNNb3VudGVkKSB7XG4gICAgICAgICAgcmVxdWVzdEFuaW1hdGlvbkZyYW1lKHVwZGF0ZVBvc2l0aW9uKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICAgcmV0dXJuIHtcbiAgICAgICAgb25Nb3VudDogZnVuY3Rpb24gb25Nb3VudCgpIHtcbiAgICAgICAgICBpZiAoaW5zdGFuY2UucHJvcHMuc3RpY2t5KSB7XG4gICAgICAgICAgICB1cGRhdGVQb3NpdGlvbigpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfTtcbiAgICB9XG4gIH07XG4gIGZ1bmN0aW9uIGFyZVJlY3RzRGlmZmVyZW50KHJlY3RBLCByZWN0Qikge1xuICAgIGlmIChyZWN0QSAmJiByZWN0Qikge1xuICAgICAgcmV0dXJuIHJlY3RBLnRvcCAhPT0gcmVjdEIudG9wIHx8IHJlY3RBLnJpZ2h0ICE9PSByZWN0Qi5yaWdodCB8fCByZWN0QS5ib3R0b20gIT09IHJlY3RCLmJvdHRvbSB8fCByZWN0QS5sZWZ0ICE9PSByZWN0Qi5sZWZ0O1xuICAgIH1cbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxuICB0aXBweTIuc2V0RGVmYXVsdFByb3BzKHtcbiAgICByZW5kZXJcbiAgfSk7XG4gIGV4cG9ydHMuYW5pbWF0ZUZpbGwgPSBhbmltYXRlRmlsbDtcbiAgZXhwb3J0cy5jcmVhdGVTaW5nbGV0b24gPSBjcmVhdGVTaW5nbGV0b247XG4gIGV4cG9ydHMuZGVmYXVsdCA9IHRpcHB5MjtcbiAgZXhwb3J0cy5kZWxlZ2F0ZSA9IGRlbGVnYXRlO1xuICBleHBvcnRzLmZvbGxvd0N1cnNvciA9IGZvbGxvd0N1cnNvcjI7XG4gIGV4cG9ydHMuaGlkZUFsbCA9IGhpZGVBbGw7XG4gIGV4cG9ydHMuaW5saW5lUG9zaXRpb25pbmcgPSBpbmxpbmVQb3NpdGlvbmluZztcbiAgZXhwb3J0cy5yb3VuZEFycm93ID0gUk9VTkRfQVJST1c7XG4gIGV4cG9ydHMuc3RpY2t5ID0gc3RpY2t5O1xufSk7XG5cbi8vIHNyYy9pbmRleC5qc1xudmFyIGltcG9ydF90aXBweTIgPSBfX3RvTW9kdWxlKHJlcXVpcmVfdGlwcHlfY2pzKCkpO1xuXG4vLyBzcmMvYnVpbGRDb25maWdGcm9tTW9kaWZpZXJzLmpzXG52YXIgaW1wb3J0X3RpcHB5ID0gX190b01vZHVsZShyZXF1aXJlX3RpcHB5X2NqcygpKTtcbnZhciBidWlsZENvbmZpZ0Zyb21Nb2RpZmllcnMgPSAobW9kaWZpZXJzKSA9PiB7XG4gIGNvbnN0IGNvbmZpZyA9IHtcbiAgICBwbHVnaW5zOiBbXVxuICB9O1xuICBjb25zdCBnZXRNb2RpZmllckFyZ3VtZW50ID0gKG1vZGlmaWVyKSA9PiB7XG4gICAgcmV0dXJuIG1vZGlmaWVyc1ttb2RpZmllcnMuaW5kZXhPZihtb2RpZmllcikgKyAxXTtcbiAgfTtcbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImFuaW1hdGlvblwiKSkge1xuICAgIGNvbmZpZy5hbmltYXRpb24gPSBnZXRNb2RpZmllckFyZ3VtZW50KFwiYW5pbWF0aW9uXCIpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJkdXJhdGlvblwiKSkge1xuICAgIGNvbmZpZy5kdXJhdGlvbiA9IHBhcnNlSW50KGdldE1vZGlmaWVyQXJndW1lbnQoXCJkdXJhdGlvblwiKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImRlbGF5XCIpKSB7XG4gICAgY29uc3QgZGVsYXkgPSBnZXRNb2RpZmllckFyZ3VtZW50KFwiZGVsYXlcIik7XG4gICAgY29uZmlnLmRlbGF5ID0gZGVsYXkuaW5jbHVkZXMoXCItXCIpID8gZGVsYXkuc3BsaXQoXCItXCIpLm1hcCgobikgPT4gcGFyc2VJbnQobikpIDogcGFyc2VJbnQoZGVsYXkpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJjdXJzb3JcIikpIHtcbiAgICBjb25maWcucGx1Z2lucy5wdXNoKGltcG9ydF90aXBweS5mb2xsb3dDdXJzb3IpO1xuICAgIGNvbnN0IG5leHQgPSBnZXRNb2RpZmllckFyZ3VtZW50KFwiY3Vyc29yXCIpO1xuICAgIGlmIChbXCJ4XCIsIFwiaW5pdGlhbFwiXS5pbmNsdWRlcyhuZXh0KSkge1xuICAgICAgY29uZmlnLmZvbGxvd0N1cnNvciA9IG5leHQgPT09IFwieFwiID8gXCJob3Jpem9udGFsXCIgOiBcImluaXRpYWxcIjtcbiAgICB9IGVsc2Uge1xuICAgICAgY29uZmlnLmZvbGxvd0N1cnNvciA9IHRydWU7XG4gICAgfVxuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJvblwiKSkge1xuICAgIGNvbmZpZy50cmlnZ2VyID0gZ2V0TW9kaWZpZXJBcmd1bWVudChcIm9uXCIpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJhcnJvd2xlc3NcIikpIHtcbiAgICBjb25maWcuYXJyb3cgPSBmYWxzZTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiaHRtbFwiKSkge1xuICAgIGNvbmZpZy5hbGxvd0hUTUwgPSB0cnVlO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJpbnRlcmFjdGl2ZVwiKSkge1xuICAgIGNvbmZpZy5pbnRlcmFjdGl2ZSA9IHRydWU7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImJvcmRlclwiKSAmJiBjb25maWcuaW50ZXJhY3RpdmUpIHtcbiAgICBjb25maWcuaW50ZXJhY3RpdmVCb3JkZXIgPSBwYXJzZUludChnZXRNb2RpZmllckFyZ3VtZW50KFwiYm9yZGVyXCIpKTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwiZGVib3VuY2VcIikgJiYgY29uZmlnLmludGVyYWN0aXZlKSB7XG4gICAgY29uZmlnLmludGVyYWN0aXZlRGVib3VuY2UgPSBwYXJzZUludChnZXRNb2RpZmllckFyZ3VtZW50KFwiZGVib3VuY2VcIikpO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJtYXgtd2lkdGhcIikpIHtcbiAgICBjb25maWcubWF4V2lkdGggPSBwYXJzZUludChnZXRNb2RpZmllckFyZ3VtZW50KFwibWF4LXdpZHRoXCIpKTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwidGhlbWVcIikpIHtcbiAgICBjb25maWcudGhlbWUgPSBnZXRNb2RpZmllckFyZ3VtZW50KFwidGhlbWVcIik7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInBsYWNlbWVudFwiKSkge1xuICAgIGNvbmZpZy5wbGFjZW1lbnQgPSBnZXRNb2RpZmllckFyZ3VtZW50KFwicGxhY2VtZW50XCIpO1xuICB9XG4gIHJldHVybiBjb25maWc7XG59O1xuXG4vLyBzcmMvaW5kZXguanNcbmZ1bmN0aW9uIHNyY19kZWZhdWx0KEFscGluZSkge1xuICBBbHBpbmUubWFnaWMoXCJ0b29sdGlwXCIsIChlbCkgPT4ge1xuICAgIHJldHVybiAoY29udGVudCwgY29uZmlnID0ge30pID0+IHtcbiAgICAgIGNvbnN0IGluc3RhbmNlID0gKDAsIGltcG9ydF90aXBweTIuZGVmYXVsdCkoZWwsIHtcbiAgICAgICAgY29udGVudCxcbiAgICAgICAgdHJpZ2dlcjogXCJtYW51YWxcIixcbiAgICAgICAgLi4uY29uZmlnXG4gICAgICB9KTtcbiAgICAgIGluc3RhbmNlLnNob3coKTtcbiAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICBpbnN0YW5jZS5oaWRlKCk7XG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4gaW5zdGFuY2UuZGVzdHJveSgpLCBjb25maWcuZHVyYXRpb24gfHwgMzAwKTtcbiAgICAgIH0sIGNvbmZpZy50aW1lb3V0IHx8IDJlMyk7XG4gICAgfTtcbiAgfSk7XG4gIEFscGluZS5kaXJlY3RpdmUoXCJ0b29sdGlwXCIsIChlbCwge21vZGlmaWVycywgZXhwcmVzc2lvbn0sIHtldmFsdWF0ZUxhdGVyLCBlZmZlY3R9KSA9PiB7XG4gICAgY29uc3QgY29uZmlnID0gbW9kaWZpZXJzLmxlbmd0aCA+IDAgPyBidWlsZENvbmZpZ0Zyb21Nb2RpZmllcnMobW9kaWZpZXJzKSA6IHt9O1xuICAgIGlmICghZWwuX194X3RpcHB5KSB7XG4gICAgICBlbC5fX3hfdGlwcHkgPSAoMCwgaW1wb3J0X3RpcHB5Mi5kZWZhdWx0KShlbCwgY29uZmlnKTtcbiAgICB9XG4gICAgY29uc3QgZW5hYmxlVG9vbHRpcCA9ICgpID0+IGVsLl9feF90aXBweS5lbmFibGUoKTtcbiAgICBjb25zdCBkaXNhYmxlVG9vbHRpcCA9ICgpID0+IGVsLl9feF90aXBweS5kaXNhYmxlKCk7XG4gICAgY29uc3Qgc2V0dXBUb29sdGlwID0gKGNvbnRlbnQpID0+IHtcbiAgICAgIGlmICghY29udGVudCkge1xuICAgICAgICBkaXNhYmxlVG9vbHRpcCgpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgZW5hYmxlVG9vbHRpcCgpO1xuICAgICAgICBlbC5fX3hfdGlwcHkuc2V0Q29udGVudChjb250ZW50KTtcbiAgICAgIH1cbiAgICB9O1xuICAgIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJyYXdcIikpIHtcbiAgICAgIHNldHVwVG9vbHRpcChleHByZXNzaW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgY29uc3QgZ2V0Q29udGVudCA9IGV2YWx1YXRlTGF0ZXIoZXhwcmVzc2lvbik7XG4gICAgICBlZmZlY3QoKCkgPT4ge1xuICAgICAgICBnZXRDb250ZW50KChjb250ZW50KSA9PiB7XG4gICAgICAgICAgaWYgKHR5cGVvZiBjb250ZW50ID09PSBcIm9iamVjdFwiKSB7XG4gICAgICAgICAgICBlbC5fX3hfdGlwcHkuc2V0UHJvcHMoY29udGVudCk7XG4gICAgICAgICAgICBlbmFibGVUb29sdGlwKCk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHNldHVwVG9vbHRpcChjb250ZW50KTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgfVxuICB9KTtcbn1cblxuLy8gYnVpbGRzL21vZHVsZS5qc1xudmFyIG1vZHVsZV9kZWZhdWx0ID0gc3JjX2RlZmF1bHQ7XG5leHBvcnQge1xuICBtb2R1bGVfZGVmYXVsdCBhcyBkZWZhdWx0XG59O1xuIiwgImltcG9ydCBBbHBpbmVGbG9hdGluZ1VJIGZyb20gJ0Bhd2NvZGVzL2FscGluZS1mbG9hdGluZy11aSdcbmltcG9ydCBBbHBpbmVMYXp5TG9hZEFzc2V0cyBmcm9tICdhbHBpbmUtbGF6eS1sb2FkLWFzc2V0cydcbmltcG9ydCBTb3J0YWJsZSBmcm9tICcuL3NvcnRhYmxlJ1xuaW1wb3J0IFRvb2x0aXAgZnJvbSAnQHJ5YW5namNoYW5kbGVyL2FscGluZS10b29sdGlwJ1xuXG5pbXBvcnQgJ3RpcHB5LmpzL2Rpc3QvdGlwcHkuY3NzJ1xuaW1wb3J0ICd0aXBweS5qcy90aGVtZXMvbGlnaHQuY3NzJ1xuXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdhbHBpbmU6aW5pdCcsICgpID0+IHtcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihBbHBpbmVGbG9hdGluZ1VJKVxuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKEFscGluZUxhenlMb2FkQXNzZXRzKVxuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKFNvcnRhYmxlKVxuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKFRvb2x0aXApXG59KVxuXG4vLyBodHRwczovL2dpdGh1Yi5jb20vbGFyYXZlbC9mcmFtZXdvcmsvYmxvYi81Mjk5YzIyMzIxYzBmMWVhOGZmNzcwYjg0YTZjNjQ2OWM0ZDZlZGVjL3NyYy9JbGx1bWluYXRlL1RyYW5zbGF0aW9uL01lc3NhZ2VTZWxlY3Rvci5waHAjTDE1XG5jb25zdCBwbHVyYWxpemUgPSBmdW5jdGlvbiAodGV4dCwgbnVtYmVyLCB2YXJpYWJsZXMpIHtcbiAgICBmdW5jdGlvbiBleHRyYWN0KHNlZ21lbnRzLCBudW1iZXIpIHtcbiAgICAgICAgZm9yIChjb25zdCBwYXJ0IG9mIHNlZ21lbnRzKSB7XG4gICAgICAgICAgICBjb25zdCBsaW5lID0gZXh0cmFjdEZyb21TdHJpbmcocGFydCwgbnVtYmVyKVxuXG4gICAgICAgICAgICBpZiAobGluZSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIHJldHVybiBsaW5lXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBleHRyYWN0RnJvbVN0cmluZyhwYXJ0LCBudW1iZXIpIHtcbiAgICAgICAgY29uc3QgbWF0Y2hlcyA9IHBhcnQubWF0Y2goL15bXFx7XFxbXShbXlxcW1xcXVxce1xcfV0qKVtcXH1cXF1dKC4qKS9zKVxuXG4gICAgICAgIGlmIChtYXRjaGVzID09PSBudWxsIHx8IG1hdGNoZXMubGVuZ3RoICE9PSAzKSB7XG4gICAgICAgICAgICByZXR1cm4gbnVsbFxuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgY29uZGl0aW9uID0gbWF0Y2hlc1sxXVxuXG4gICAgICAgIGNvbnN0IHZhbHVlID0gbWF0Y2hlc1syXVxuXG4gICAgICAgIGlmIChjb25kaXRpb24uaW5jbHVkZXMoJywnKSkge1xuICAgICAgICAgICAgY29uc3QgW2Zyb20sIHRvXSA9IGNvbmRpdGlvbi5zcGxpdCgnLCcsIDIpXG5cbiAgICAgICAgICAgIGlmICh0byA9PT0gJyonICYmIG51bWJlciA+PSBmcm9tKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHZhbHVlXG4gICAgICAgICAgICB9IGVsc2UgaWYgKGZyb20gPT09ICcqJyAmJiBudW1iZXIgPD0gdG8pIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdmFsdWVcbiAgICAgICAgICAgIH0gZWxzZSBpZiAobnVtYmVyID49IGZyb20gJiYgbnVtYmVyIDw9IHRvKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHZhbHVlXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gY29uZGl0aW9uID09IG51bWJlciA/IHZhbHVlIDogbnVsbFxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHVjZmlyc3Qoc3RyaW5nKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICBzdHJpbmcudG9TdHJpbmcoKS5jaGFyQXQoMCkudG9VcHBlckNhc2UoKSArXG4gICAgICAgICAgICBzdHJpbmcudG9TdHJpbmcoKS5zbGljZSgxKVxuICAgICAgICApXG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcmVwbGFjZShsaW5lLCByZXBsYWNlKSB7XG4gICAgICAgIGlmIChyZXBsYWNlLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgICAgcmV0dXJuIGxpbmVcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHNob3VsZFJlcGxhY2UgPSB7fVxuXG4gICAgICAgIGZvciAobGV0IFtrZXksIHZhbHVlXSBvZiBPYmplY3QuZW50cmllcyhyZXBsYWNlKSkge1xuICAgICAgICAgICAgc2hvdWxkUmVwbGFjZVsnOicgKyB1Y2ZpcnN0KGtleSA/PyAnJyldID0gdWNmaXJzdCh2YWx1ZSA/PyAnJylcbiAgICAgICAgICAgIHNob3VsZFJlcGxhY2VbJzonICsga2V5LnRvVXBwZXJDYXNlKCldID0gdmFsdWVcbiAgICAgICAgICAgICAgICAudG9TdHJpbmcoKVxuICAgICAgICAgICAgICAgIC50b1VwcGVyQ2FzZSgpXG4gICAgICAgICAgICBzaG91bGRSZXBsYWNlWyc6JyArIGtleV0gPSB2YWx1ZVxuICAgICAgICB9XG5cbiAgICAgICAgT2JqZWN0LmVudHJpZXMoc2hvdWxkUmVwbGFjZSkuZm9yRWFjaCgoW2tleSwgdmFsdWVdKSA9PiB7XG4gICAgICAgICAgICBsaW5lID0gbGluZS5yZXBsYWNlQWxsKGtleSwgdmFsdWUpXG4gICAgICAgIH0pXG5cbiAgICAgICAgcmV0dXJuIGxpbmVcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzdHJpcENvbmRpdGlvbnMoc2VnbWVudHMpIHtcbiAgICAgICAgcmV0dXJuIHNlZ21lbnRzLm1hcCgocGFydCkgPT5cbiAgICAgICAgICAgIHBhcnQucmVwbGFjZSgvXltcXHtcXFtdKFteXFxbXFxdXFx7XFx9XSopW1xcfVxcXV0vLCAnJyksXG4gICAgICAgIClcbiAgICB9XG5cbiAgICBsZXQgc2VnbWVudHMgPSB0ZXh0LnNwbGl0KCd8JylcblxuICAgIGNvbnN0IHZhbHVlID0gZXh0cmFjdChzZWdtZW50cywgbnVtYmVyKVxuXG4gICAgaWYgKHZhbHVlICE9PSBudWxsICYmIHZhbHVlICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgcmV0dXJuIHJlcGxhY2UodmFsdWUudHJpbSgpLCB2YXJpYWJsZXMpXG4gICAgfVxuXG4gICAgc2VnbWVudHMgPSBzdHJpcENvbmRpdGlvbnMoc2VnbWVudHMpXG5cbiAgICByZXR1cm4gcmVwbGFjZShcbiAgICAgICAgc2VnbWVudHMubGVuZ3RoID4gMSAmJiBudW1iZXIgPiAxID8gc2VnbWVudHNbMV0gOiBzZWdtZW50c1swXSxcbiAgICAgICAgdmFyaWFibGVzLFxuICAgIClcbn1cblxud2luZG93LnBsdXJhbGl6ZSA9IHBsdXJhbGl6ZVxuIl0sCiAgIm1hcHBpbmdzIjogIjs7QUFDQSxXQUFTLFFBQVEsV0FBVztBQUMxQixXQUFPLFVBQVUsTUFBTSxHQUFHLEVBQUUsQ0FBQztBQUFBLEVBQy9CO0FBQ0EsV0FBUyxhQUFhLFdBQVc7QUFDL0IsV0FBTyxVQUFVLE1BQU0sR0FBRyxFQUFFLENBQUM7QUFBQSxFQUMvQjtBQUNBLFdBQVMseUJBQXlCLFdBQVc7QUFDM0MsV0FBTyxDQUFDLE9BQU8sUUFBUSxFQUFFLFNBQVMsUUFBUSxTQUFTLENBQUMsSUFBSSxNQUFNO0FBQUEsRUFDaEU7QUFDQSxXQUFTLGtCQUFrQixNQUFNO0FBQy9CLFdBQU8sU0FBUyxNQUFNLFdBQVc7QUFBQSxFQUNuQztBQUNBLFdBQVMsMkJBQTJCLE1BQU0sV0FBVyxLQUFLO0FBQ3hELFFBQUk7QUFBQSxNQUNGO0FBQUEsTUFDQTtBQUFBLElBQ0YsSUFBSTtBQUNKLFVBQU0sVUFBVSxVQUFVLElBQUksVUFBVSxRQUFRLElBQUksU0FBUyxRQUFRO0FBQ3JFLFVBQU0sVUFBVSxVQUFVLElBQUksVUFBVSxTQUFTLElBQUksU0FBUyxTQUFTO0FBQ3ZFLFVBQU0sV0FBVyx5QkFBeUIsU0FBUztBQUNuRCxVQUFNLFNBQVMsa0JBQWtCLFFBQVE7QUFDekMs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ZUFBVyxnQkFBZ0IsUUFBUTtBQUVuQyxXQUFPO0FBQUEsTUFDSCxTQUFTLFNBQVMsS0FBSyxTQUFTLElBQUksU0FBUyxDQUFDLElBQUksU0FBUyxDQUFDO0FBQUEsTUFDNUQ7QUFBQSxJQUNKO0FBQUEsRUFDSjtBQUVBLFNBQU8sWUFBWTsiLAogICJuYW1lcyI6IFsiY3NzIiwgIm1vZHVsZV9kZWZhdWx0IiwgIm9iaiIsICJpbmRleCIsICJvcHRpb24iLCAiZGVmYXVsdHMiLCAicm9vdEVsIiwgImNsb25lRWwiLCAib2xkSW5kZXgiLCAibmV3SW5kZXgiLCAib2xkRHJhZ2dhYmxlSW5kZXgiLCAibmV3RHJhZ2dhYmxlSW5kZXgiLCAicHV0U29ydGFibGUiLCAicGx1Z2luRXZlbnQiLCAiX2RldGVjdERpcmVjdGlvbiIsICJfZHJhZ0VsSW5Sb3dDb2x1bW4iLCAiX2RldGVjdE5lYXJlc3RFbXB0eVNvcnRhYmxlIiwgIl9wcmVwYXJlR3JvdXAiLCAiZHJhZ0VsIiwgIl9oaWRlR2hvc3RGb3JUYXJnZXQiLCAiX3VuaGlkZUdob3N0Rm9yVGFyZ2V0IiwgIm5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50IiwgIl9jaGVja091dHNpZGVUYXJnZXRFbCIsICJkcmFnU3RhcnRGbiIsICJ0YXJnZXQiLCAiYWZ0ZXIiLCAiZWwiLCAicGx1Z2lucyIsICJkcm9wIiwgImF1dG9TY3JvbGwiLCAib25TcGlsbCIsICJnZXRCb3VuZGluZ0NsaWVudFJlY3QiLCAiZ2V0V2luZG93IiwgImlzRWxlbWVudCIsICJpc0hUTUxFbGVtZW50IiwgImlzU2hhZG93Um9vdCIsICJnZXROb2RlU2Nyb2xsIiwgImdldE5vZGVOYW1lIiwgImdldERvY3VtZW50RWxlbWVudCIsICJnZXRXaW5kb3dTY3JvbGxCYXJYIiwgImdldENvbXB1dGVkU3R5bGUiLCAiZ2V0UGFyZW50Tm9kZSIsICJpc1RhYmxlRWxlbWVudCIsICJnZXRUcnVlT2Zmc2V0UGFyZW50IiwgImdldENvbnRhaW5pbmdCbG9jayIsICJjc3MiLCAiZ2V0T2Zmc2V0UGFyZW50IiwgInNvcnQiLCAiZ2V0Vmlld3BvcnRSZWN0IiwgIm1heCIsICJtaW4iLCAicm91bmQiLCAiZ2V0RG9jdW1lbnRSZWN0IiwgImNvbnRhaW5zIiwgInJlY3RUb0NsaWVudFJlY3QiLCAiZ2V0SW5uZXJCb3VuZGluZ0NsaWVudFJlY3QiLCAiZ2V0Q2xpcHBpbmdSZWN0IiwgImdldE1haW5BeGlzRnJvbVBsYWNlbWVudCIsICJkZXRlY3RPdmVyZmxvdyIsICJvZmZzZXQyIiwgImluZGV4IiwgImRlc3Ryb3kiLCAib2Zmc2V0IiwgImhhc2gkMSIsICJnZXRPcHBvc2l0ZVBsYWNlbWVudCIsICJoYXNoIiwgImZsaXAiLCAid2l0aGluIiwgImFycm93IiwgIm1pbjIiLCAibWF4MiIsICJnZXRTaWRlT2Zmc2V0cyIsICJpc0FueVNpZGVGdWxseUNsaXBwZWQiLCAiaGlkZSIsICJjbG9uZSIsICJwbHVnaW5zIiwgIm9uIiwgIm1vdW50IiwgImJ1aWxkQ29uZmlnRnJvbU1vZGlmaWVycyIsICJzcmNfZGVmYXVsdCIsICJtb2R1bGVfZGVmYXVsdCIsICJtb2R1bGVfZGVmYXVsdCIsICJzZWdtZW50cyIsICJudW1iZXIiLCAibWF0Y2hlcyIsICJ2YWx1ZSIsICJyZXBsYWNlIl0KfQo=
