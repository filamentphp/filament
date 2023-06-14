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
    Alpine.directive("load-css", (el, { expression }, { evaluate }) => {
      try {
        const paths = evaluate(expression);
        paths?.forEach((path) => {
          if (document.querySelector(`link[href="${path}"]`)) {
            return;
          }
          const link = document.createElement("link");
          link.type = "text/css";
          link.rel = "stylesheet";
          link.href = path;
          const mediaAttr = el.attributes?.media?.value;
          if (mediaAttr) {
            link.media = mediaAttr;
          }
          const head = document.getElementsByTagName("head")[0];
          head.appendChild(link);
        });
      } catch (error) {
        console.error(error);
      }
    });
    Alpine.directive("load-js", (el, { expression }, { evaluate }) => {
      try {
        const paths = evaluate(expression);
        paths?.forEach((path) => {
          if (document.querySelector(`script[src="${path}"]`)) {
            return;
          }
          const script = document.createElement("script");
          script.src = path;
          const head = document.getElementsByTagName("head")[0];
          head.appendChild(script);
        });
      } catch (error) {
        console.error(error);
      }
    });
  }
  var module_default2 = alpine_lazy_load_assets_default;

  // node_modules/@alpinejs/focus/dist/module.esm.js
  var candidateSelectors = ["input", "select", "textarea", "a[href]", "button", "[tabindex]", "audio[controls]", "video[controls]", '[contenteditable]:not([contenteditable="false"])', "details>summary:first-of-type", "details"];
  var candidateSelector = /* @__PURE__ */ candidateSelectors.join(",");
  var matches = typeof Element === "undefined" ? function() {
  } : Element.prototype.matches || Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
  var getCandidates = function getCandidates2(el, includeContainer, filter) {
    var candidates = Array.prototype.slice.apply(el.querySelectorAll(candidateSelector));
    if (includeContainer && matches.call(el, candidateSelector)) {
      candidates.unshift(el);
    }
    candidates = candidates.filter(filter);
    return candidates;
  };
  var isContentEditable = function isContentEditable2(node) {
    return node.contentEditable === "true";
  };
  var getTabindex = function getTabindex2(node) {
    var tabindexAttr = parseInt(node.getAttribute("tabindex"), 10);
    if (!isNaN(tabindexAttr)) {
      return tabindexAttr;
    }
    if (isContentEditable(node)) {
      return 0;
    }
    if ((node.nodeName === "AUDIO" || node.nodeName === "VIDEO" || node.nodeName === "DETAILS") && node.getAttribute("tabindex") === null) {
      return 0;
    }
    return node.tabIndex;
  };
  var sortOrderedTabbables = function sortOrderedTabbables2(a, b) {
    return a.tabIndex === b.tabIndex ? a.documentOrder - b.documentOrder : a.tabIndex - b.tabIndex;
  };
  var isInput = function isInput2(node) {
    return node.tagName === "INPUT";
  };
  var isHiddenInput = function isHiddenInput2(node) {
    return isInput(node) && node.type === "hidden";
  };
  var isDetailsWithSummary = function isDetailsWithSummary2(node) {
    var r = node.tagName === "DETAILS" && Array.prototype.slice.apply(node.children).some(function(child) {
      return child.tagName === "SUMMARY";
    });
    return r;
  };
  var getCheckedRadio = function getCheckedRadio2(nodes, form) {
    for (var i = 0; i < nodes.length; i++) {
      if (nodes[i].checked && nodes[i].form === form) {
        return nodes[i];
      }
    }
  };
  var isTabbableRadio = function isTabbableRadio2(node) {
    if (!node.name) {
      return true;
    }
    var radioScope = node.form || node.ownerDocument;
    var queryRadios = function queryRadios2(name) {
      return radioScope.querySelectorAll('input[type="radio"][name="' + name + '"]');
    };
    var radioSet;
    if (typeof window !== "undefined" && typeof window.CSS !== "undefined" && typeof window.CSS.escape === "function") {
      radioSet = queryRadios(window.CSS.escape(node.name));
    } else {
      try {
        radioSet = queryRadios(node.name);
      } catch (err) {
        console.error("Looks like you have a radio button with a name attribute containing invalid CSS selector characters and need the CSS.escape polyfill: %s", err.message);
        return false;
      }
    }
    var checked = getCheckedRadio(radioSet, node.form);
    return !checked || checked === node;
  };
  var isRadio = function isRadio2(node) {
    return isInput(node) && node.type === "radio";
  };
  var isNonTabbableRadio = function isNonTabbableRadio2(node) {
    return isRadio(node) && !isTabbableRadio(node);
  };
  var isHidden = function isHidden2(node, displayCheck) {
    if (getComputedStyle(node).visibility === "hidden") {
      return true;
    }
    var isDirectSummary = matches.call(node, "details>summary:first-of-type");
    var nodeUnderDetails = isDirectSummary ? node.parentElement : node;
    if (matches.call(nodeUnderDetails, "details:not([open]) *")) {
      return true;
    }
    if (!displayCheck || displayCheck === "full") {
      while (node) {
        if (getComputedStyle(node).display === "none") {
          return true;
        }
        node = node.parentElement;
      }
    } else if (displayCheck === "non-zero-area") {
      var _node$getBoundingClie = node.getBoundingClientRect(), width = _node$getBoundingClie.width, height = _node$getBoundingClie.height;
      return width === 0 && height === 0;
    }
    return false;
  };
  var isDisabledFromFieldset = function isDisabledFromFieldset2(node) {
    if (isInput(node) || node.tagName === "SELECT" || node.tagName === "TEXTAREA" || node.tagName === "BUTTON") {
      var parentNode = node.parentElement;
      while (parentNode) {
        if (parentNode.tagName === "FIELDSET" && parentNode.disabled) {
          for (var i = 0; i < parentNode.children.length; i++) {
            var child = parentNode.children.item(i);
            if (child.tagName === "LEGEND") {
              if (child.contains(node)) {
                return false;
              }
              return true;
            }
          }
          return true;
        }
        parentNode = parentNode.parentElement;
      }
    }
    return false;
  };
  var isNodeMatchingSelectorFocusable = function isNodeMatchingSelectorFocusable2(options, node) {
    if (node.disabled || isHiddenInput(node) || isHidden(node, options.displayCheck) || isDetailsWithSummary(node) || isDisabledFromFieldset(node)) {
      return false;
    }
    return true;
  };
  var isNodeMatchingSelectorTabbable = function isNodeMatchingSelectorTabbable2(options, node) {
    if (!isNodeMatchingSelectorFocusable(options, node) || isNonTabbableRadio(node) || getTabindex(node) < 0) {
      return false;
    }
    return true;
  };
  var tabbable = function tabbable2(el, options) {
    options = options || {};
    var regularTabbables = [];
    var orderedTabbables = [];
    var candidates = getCandidates(el, options.includeContainer, isNodeMatchingSelectorTabbable.bind(null, options));
    candidates.forEach(function(candidate, i) {
      var candidateTabindex = getTabindex(candidate);
      if (candidateTabindex === 0) {
        regularTabbables.push(candidate);
      } else {
        orderedTabbables.push({
          documentOrder: i,
          tabIndex: candidateTabindex,
          node: candidate
        });
      }
    });
    var tabbableNodes = orderedTabbables.sort(sortOrderedTabbables).map(function(a) {
      return a.node;
    }).concat(regularTabbables);
    return tabbableNodes;
  };
  var focusable = function focusable2(el, options) {
    options = options || {};
    var candidates = getCandidates(el, options.includeContainer, isNodeMatchingSelectorFocusable.bind(null, options));
    return candidates;
  };
  var focusableCandidateSelector = /* @__PURE__ */ candidateSelectors.concat("iframe").join(",");
  var isFocusable = function isFocusable2(node, options) {
    options = options || {};
    if (!node) {
      throw new Error("No node provided");
    }
    if (matches.call(node, focusableCandidateSelector) === false) {
      return false;
    }
    return isNodeMatchingSelectorFocusable(options, node);
  };
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
  var activeFocusTraps = function() {
    var trapQueue = [];
    return {
      activateTrap: function activateTrap(trap) {
        if (trapQueue.length > 0) {
          var activeTrap = trapQueue[trapQueue.length - 1];
          if (activeTrap !== trap) {
            activeTrap.pause();
          }
        }
        var trapIndex = trapQueue.indexOf(trap);
        if (trapIndex === -1) {
          trapQueue.push(trap);
        } else {
          trapQueue.splice(trapIndex, 1);
          trapQueue.push(trap);
        }
      },
      deactivateTrap: function deactivateTrap(trap) {
        var trapIndex = trapQueue.indexOf(trap);
        if (trapIndex !== -1) {
          trapQueue.splice(trapIndex, 1);
        }
        if (trapQueue.length > 0) {
          trapQueue[trapQueue.length - 1].unpause();
        }
      }
    };
  }();
  var isSelectableInput = function isSelectableInput2(node) {
    return node.tagName && node.tagName.toLowerCase() === "input" && typeof node.select === "function";
  };
  var isEscapeEvent = function isEscapeEvent2(e) {
    return e.key === "Escape" || e.key === "Esc" || e.keyCode === 27;
  };
  var isTabEvent = function isTabEvent2(e) {
    return e.key === "Tab" || e.keyCode === 9;
  };
  var delay = function delay2(fn) {
    return setTimeout(fn, 0);
  };
  var findIndex = function findIndex2(arr, fn) {
    var idx = -1;
    arr.every(function(value, i) {
      if (fn(value)) {
        idx = i;
        return false;
      }
      return true;
    });
    return idx;
  };
  var valueOrHandler = function valueOrHandler2(value) {
    for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      params[_key - 1] = arguments[_key];
    }
    return typeof value === "function" ? value.apply(void 0, params) : value;
  };
  var createFocusTrap = function createFocusTrap2(elements, userOptions) {
    var doc = document;
    var config = _objectSpread2({
      returnFocusOnDeactivate: true,
      escapeDeactivates: true,
      delayInitialFocus: true
    }, userOptions);
    var state = {
      containers: [],
      tabbableGroups: [],
      nodeFocusedBeforeActivation: null,
      mostRecentlyFocusedNode: null,
      active: false,
      paused: false,
      delayInitialFocusTimer: void 0
    };
    var trap;
    var getOption = function getOption2(configOverrideOptions, optionName, configOptionName) {
      return configOverrideOptions && configOverrideOptions[optionName] !== void 0 ? configOverrideOptions[optionName] : config[configOptionName || optionName];
    };
    var containersContain = function containersContain2(element) {
      return state.containers.some(function(container) {
        return container.contains(element);
      });
    };
    var getNodeForOption = function getNodeForOption2(optionName) {
      var optionValue = config[optionName];
      if (!optionValue) {
        return null;
      }
      var node = optionValue;
      if (typeof optionValue === "string") {
        node = doc.querySelector(optionValue);
        if (!node) {
          throw new Error("`".concat(optionName, "` refers to no known node"));
        }
      }
      if (typeof optionValue === "function") {
        node = optionValue();
        if (!node) {
          throw new Error("`".concat(optionName, "` did not return a node"));
        }
      }
      return node;
    };
    var getInitialFocusNode = function getInitialFocusNode2() {
      var node;
      if (getOption({}, "initialFocus") === false) {
        return false;
      }
      if (getNodeForOption("initialFocus") !== null) {
        node = getNodeForOption("initialFocus");
      } else if (containersContain(doc.activeElement)) {
        node = doc.activeElement;
      } else {
        var firstTabbableGroup = state.tabbableGroups[0];
        var firstTabbableNode = firstTabbableGroup && firstTabbableGroup.firstTabbableNode;
        node = firstTabbableNode || getNodeForOption("fallbackFocus");
      }
      if (!node) {
        throw new Error("Your focus-trap needs to have at least one focusable element");
      }
      return node;
    };
    var updateTabbableNodes = function updateTabbableNodes2() {
      state.tabbableGroups = state.containers.map(function(container) {
        var tabbableNodes = tabbable(container);
        if (tabbableNodes.length > 0) {
          return {
            container,
            firstTabbableNode: tabbableNodes[0],
            lastTabbableNode: tabbableNodes[tabbableNodes.length - 1]
          };
        }
        return void 0;
      }).filter(function(group) {
        return !!group;
      });
      if (state.tabbableGroups.length <= 0 && !getNodeForOption("fallbackFocus")) {
        throw new Error("Your focus-trap must have at least one container with at least one tabbable node in it at all times");
      }
    };
    var tryFocus = function tryFocus2(node) {
      if (node === false) {
        return;
      }
      if (node === doc.activeElement) {
        return;
      }
      if (!node || !node.focus) {
        tryFocus2(getInitialFocusNode());
        return;
      }
      node.focus({
        preventScroll: !!config.preventScroll
      });
      state.mostRecentlyFocusedNode = node;
      if (isSelectableInput(node)) {
        node.select();
      }
    };
    var getReturnFocusNode = function getReturnFocusNode2(previousActiveElement) {
      var node = getNodeForOption("setReturnFocus");
      return node ? node : previousActiveElement;
    };
    var checkPointerDown = function checkPointerDown2(e) {
      if (containersContain(e.target)) {
        return;
      }
      if (valueOrHandler(config.clickOutsideDeactivates, e)) {
        trap.deactivate({
          returnFocus: config.returnFocusOnDeactivate && !isFocusable(e.target)
        });
        return;
      }
      if (valueOrHandler(config.allowOutsideClick, e)) {
        return;
      }
      e.preventDefault();
    };
    var checkFocusIn = function checkFocusIn2(e) {
      var targetContained = containersContain(e.target);
      if (targetContained || e.target instanceof Document) {
        if (targetContained) {
          state.mostRecentlyFocusedNode = e.target;
        }
      } else {
        e.stopImmediatePropagation();
        tryFocus(state.mostRecentlyFocusedNode || getInitialFocusNode());
      }
    };
    var checkTab = function checkTab2(e) {
      updateTabbableNodes();
      var destinationNode = null;
      if (state.tabbableGroups.length > 0) {
        var containerIndex = findIndex(state.tabbableGroups, function(_ref) {
          var container = _ref.container;
          return container.contains(e.target);
        });
        if (containerIndex < 0) {
          if (e.shiftKey) {
            destinationNode = state.tabbableGroups[state.tabbableGroups.length - 1].lastTabbableNode;
          } else {
            destinationNode = state.tabbableGroups[0].firstTabbableNode;
          }
        } else if (e.shiftKey) {
          var startOfGroupIndex = findIndex(state.tabbableGroups, function(_ref2) {
            var firstTabbableNode = _ref2.firstTabbableNode;
            return e.target === firstTabbableNode;
          });
          if (startOfGroupIndex < 0 && state.tabbableGroups[containerIndex].container === e.target) {
            startOfGroupIndex = containerIndex;
          }
          if (startOfGroupIndex >= 0) {
            var destinationGroupIndex = startOfGroupIndex === 0 ? state.tabbableGroups.length - 1 : startOfGroupIndex - 1;
            var destinationGroup = state.tabbableGroups[destinationGroupIndex];
            destinationNode = destinationGroup.lastTabbableNode;
          }
        } else {
          var lastOfGroupIndex = findIndex(state.tabbableGroups, function(_ref3) {
            var lastTabbableNode = _ref3.lastTabbableNode;
            return e.target === lastTabbableNode;
          });
          if (lastOfGroupIndex < 0 && state.tabbableGroups[containerIndex].container === e.target) {
            lastOfGroupIndex = containerIndex;
          }
          if (lastOfGroupIndex >= 0) {
            var _destinationGroupIndex = lastOfGroupIndex === state.tabbableGroups.length - 1 ? 0 : lastOfGroupIndex + 1;
            var _destinationGroup = state.tabbableGroups[_destinationGroupIndex];
            destinationNode = _destinationGroup.firstTabbableNode;
          }
        }
      } else {
        destinationNode = getNodeForOption("fallbackFocus");
      }
      if (destinationNode) {
        e.preventDefault();
        tryFocus(destinationNode);
      }
    };
    var checkKey = function checkKey2(e) {
      if (isEscapeEvent(e) && valueOrHandler(config.escapeDeactivates) !== false) {
        e.preventDefault();
        trap.deactivate();
        return;
      }
      if (isTabEvent(e)) {
        checkTab(e);
        return;
      }
    };
    var checkClick = function checkClick2(e) {
      if (valueOrHandler(config.clickOutsideDeactivates, e)) {
        return;
      }
      if (containersContain(e.target)) {
        return;
      }
      if (valueOrHandler(config.allowOutsideClick, e)) {
        return;
      }
      e.preventDefault();
      e.stopImmediatePropagation();
    };
    var addListeners = function addListeners2() {
      if (!state.active) {
        return;
      }
      activeFocusTraps.activateTrap(trap);
      state.delayInitialFocusTimer = config.delayInitialFocus ? delay(function() {
        tryFocus(getInitialFocusNode());
      }) : tryFocus(getInitialFocusNode());
      doc.addEventListener("focusin", checkFocusIn, true);
      doc.addEventListener("mousedown", checkPointerDown, {
        capture: true,
        passive: false
      });
      doc.addEventListener("touchstart", checkPointerDown, {
        capture: true,
        passive: false
      });
      doc.addEventListener("click", checkClick, {
        capture: true,
        passive: false
      });
      doc.addEventListener("keydown", checkKey, {
        capture: true,
        passive: false
      });
      return trap;
    };
    var removeListeners = function removeListeners2() {
      if (!state.active) {
        return;
      }
      doc.removeEventListener("focusin", checkFocusIn, true);
      doc.removeEventListener("mousedown", checkPointerDown, true);
      doc.removeEventListener("touchstart", checkPointerDown, true);
      doc.removeEventListener("click", checkClick, true);
      doc.removeEventListener("keydown", checkKey, true);
      return trap;
    };
    trap = {
      activate: function activate(activateOptions) {
        if (state.active) {
          return this;
        }
        var onActivate = getOption(activateOptions, "onActivate");
        var onPostActivate = getOption(activateOptions, "onPostActivate");
        var checkCanFocusTrap = getOption(activateOptions, "checkCanFocusTrap");
        if (!checkCanFocusTrap) {
          updateTabbableNodes();
        }
        state.active = true;
        state.paused = false;
        state.nodeFocusedBeforeActivation = doc.activeElement;
        if (onActivate) {
          onActivate();
        }
        var finishActivation = function finishActivation2() {
          if (checkCanFocusTrap) {
            updateTabbableNodes();
          }
          addListeners();
          if (onPostActivate) {
            onPostActivate();
          }
        };
        if (checkCanFocusTrap) {
          checkCanFocusTrap(state.containers.concat()).then(finishActivation, finishActivation);
          return this;
        }
        finishActivation();
        return this;
      },
      deactivate: function deactivate(deactivateOptions) {
        if (!state.active) {
          return this;
        }
        clearTimeout(state.delayInitialFocusTimer);
        state.delayInitialFocusTimer = void 0;
        removeListeners();
        state.active = false;
        state.paused = false;
        activeFocusTraps.deactivateTrap(trap);
        var onDeactivate = getOption(deactivateOptions, "onDeactivate");
        var onPostDeactivate = getOption(deactivateOptions, "onPostDeactivate");
        var checkCanReturnFocus = getOption(deactivateOptions, "checkCanReturnFocus");
        if (onDeactivate) {
          onDeactivate();
        }
        var returnFocus = getOption(deactivateOptions, "returnFocus", "returnFocusOnDeactivate");
        var finishDeactivation = function finishDeactivation2() {
          delay(function() {
            if (returnFocus) {
              tryFocus(getReturnFocusNode(state.nodeFocusedBeforeActivation));
            }
            if (onPostDeactivate) {
              onPostDeactivate();
            }
          });
        };
        if (returnFocus && checkCanReturnFocus) {
          checkCanReturnFocus(getReturnFocusNode(state.nodeFocusedBeforeActivation)).then(finishDeactivation, finishDeactivation);
          return this;
        }
        finishDeactivation();
        return this;
      },
      pause: function pause() {
        if (state.paused || !state.active) {
          return this;
        }
        state.paused = true;
        removeListeners();
        return this;
      },
      unpause: function unpause() {
        if (!state.paused || !state.active) {
          return this;
        }
        state.paused = false;
        updateTabbableNodes();
        addListeners();
        return this;
      },
      updateContainerElements: function updateContainerElements(containerElements) {
        var elementsAsArray = [].concat(containerElements).filter(Boolean);
        state.containers = elementsAsArray.map(function(element) {
          return typeof element === "string" ? doc.querySelector(element) : element;
        });
        if (state.active) {
          updateTabbableNodes();
        }
        return this;
      }
    };
    trap.updateContainerElements(elements);
    return trap;
  };
  function src_default2(Alpine) {
    let lastFocused;
    let currentFocused;
    window.addEventListener("focusin", () => {
      lastFocused = currentFocused;
      currentFocused = document.activeElement;
    });
    Alpine.magic("focus", (el) => {
      let within2 = el;
      return {
        __noscroll: false,
        __wrapAround: false,
        within(el2) {
          within2 = el2;
          return this;
        },
        withoutScrolling() {
          this.__noscroll = true;
          return this;
        },
        noscroll() {
          this.__noscroll = true;
          return this;
        },
        withWrapAround() {
          this.__wrapAround = true;
          return this;
        },
        wrap() {
          return this.withWrapAround();
        },
        focusable(el2) {
          return isFocusable(el2);
        },
        previouslyFocused() {
          return lastFocused;
        },
        lastFocused() {
          return lastFocused;
        },
        focused() {
          return currentFocused;
        },
        focusables() {
          if (Array.isArray(within2))
            return within2;
          return focusable(within2, { displayCheck: "none" });
        },
        all() {
          return this.focusables();
        },
        isFirst(el2) {
          let els = this.all();
          return els[0] && els[0].isSameNode(el2);
        },
        isLast(el2) {
          let els = this.all();
          return els.length && els.slice(-1)[0].isSameNode(el2);
        },
        getFirst() {
          return this.all()[0];
        },
        getLast() {
          return this.all().slice(-1)[0];
        },
        getNext() {
          let list = this.all();
          let current = document.activeElement;
          if (list.indexOf(current) === -1)
            return;
          if (this.__wrapAround && list.indexOf(current) === list.length - 1) {
            return list[0];
          }
          return list[list.indexOf(current) + 1];
        },
        getPrevious() {
          let list = this.all();
          let current = document.activeElement;
          if (list.indexOf(current) === -1)
            return;
          if (this.__wrapAround && list.indexOf(current) === 0) {
            return list.slice(-1)[0];
          }
          return list[list.indexOf(current) - 1];
        },
        first() {
          this.focus(this.getFirst());
        },
        last() {
          this.focus(this.getLast());
        },
        next() {
          this.focus(this.getNext());
        },
        previous() {
          this.focus(this.getPrevious());
        },
        prev() {
          return this.previous();
        },
        focus(el2) {
          if (!el2)
            return;
          setTimeout(() => {
            if (!el2.hasAttribute("tabindex"))
              el2.setAttribute("tabindex", "0");
            el2.focus({ preventScroll: this._noscroll });
          });
        }
      };
    });
    Alpine.directive("trap", Alpine.skipDuringClone((el, { expression, modifiers }, { effect, evaluateLater, cleanup }) => {
      let evaluator = evaluateLater(expression);
      let oldValue = false;
      let options = {
        escapeDeactivates: false,
        allowOutsideClick: true,
        fallbackFocus: () => el
      };
      let autofocusEl = el.querySelector("[autofocus]");
      if (autofocusEl)
        options.initialFocus = autofocusEl;
      let trap = createFocusTrap(el, options);
      let undoInert = () => {
      };
      let undoDisableScrolling = () => {
      };
      const releaseFocus = () => {
        undoInert();
        undoInert = () => {
        };
        undoDisableScrolling();
        undoDisableScrolling = () => {
        };
        trap.deactivate({
          returnFocus: !modifiers.includes("noreturn")
        });
      };
      effect(() => evaluator((value) => {
        if (oldValue === value)
          return;
        if (value && !oldValue) {
          setTimeout(() => {
            if (modifiers.includes("inert"))
              undoInert = setInert(el);
            if (modifiers.includes("noscroll"))
              undoDisableScrolling = disableScrolling();
            trap.activate();
          });
        }
        if (!value && oldValue) {
          releaseFocus();
        }
        oldValue = !!value;
      }));
      cleanup(releaseFocus);
    }, (el, { expression, modifiers }, { evaluate }) => {
      if (modifiers.includes("inert") && evaluate(expression))
        setInert(el);
    }));
  }
  function setInert(el) {
    let undos = [];
    crawlSiblingsUp(el, (sibling) => {
      let cache = sibling.hasAttribute("aria-hidden");
      sibling.setAttribute("aria-hidden", "true");
      undos.push(() => cache || sibling.removeAttribute("aria-hidden"));
    });
    return () => {
      while (undos.length)
        undos.pop()();
    };
  }
  function crawlSiblingsUp(el, callback) {
    if (el.isSameNode(document.body) || !el.parentNode)
      return;
    Array.from(el.parentNode.children).forEach((sibling) => {
      if (sibling.isSameNode(el)) {
        crawlSiblingsUp(el.parentNode, callback);
      } else {
        callback(sibling);
      }
    });
  }
  function disableScrolling() {
    let overflow = document.documentElement.style.overflow;
    let paddingRight = document.documentElement.style.paddingRight;
    let scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.documentElement.style.overflow = "hidden";
    document.documentElement.style.paddingRight = `${scrollbarWidth}px`;
    return () => {
      document.documentElement.style.overflow = overflow;
      document.documentElement.style.paddingRight = paddingRight;
    };
  }
  var module_default3 = src_default2;

  // node_modules/sortablejs/modular/sortable.esm.js
  function ownKeys2(object, enumerableOnly) {
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
  function _objectSpread22(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};
      if (i % 2) {
        ownKeys2(Object(source), true).forEach(function(key) {
          _defineProperty2(target, key, source[key]);
        });
      } else if (Object.getOwnPropertyDescriptors) {
        Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
      } else {
        ownKeys2(Object(source)).forEach(function(key) {
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
  function _defineProperty2(obj, key, value) {
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
  function matches2(el, selector) {
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
        if (selector != null && (selector[0] === ">" ? el.parentNode === ctx && matches2(el, selector) : matches2(el, selector)) || includeCTX && el === ctx) {
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
    while (last && (last === Sortable.ghost || css(last, "display") === "none" || selector && !matches2(last, selector))) {
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
      if (el.nodeName.toUpperCase() !== "TEMPLATE" && el !== Sortable.clone && (!selector || matches2(el, selector))) {
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
          var fromRect = _objectSpread22({}, animationStates[animationStates.length - 1].rect);
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
          sortable[plugin.pluginName][eventNameGlobal](_objectSpread22({
            sortable
          }, evt));
        }
        if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) {
          sortable[plugin.pluginName][eventName](_objectSpread22({
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
    var allEventProperties = _objectSpread22(_objectSpread22({}, extraEventProperties), PluginManager.getEventProperties(name, sortable));
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
    PluginManager.pluginEvent.bind(Sortable)(eventName, sortable, _objectSpread22({
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
    dispatchEvent(_objectSpread22({
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
        pluginEvent2(name, _this, _objectSpread22({
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
        Sortable.utils = _objectSpread22(_objectSpread22({}, Sortable.utils), plugin.utils);
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

  // packages/support/resources/js/index.js
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(module_default3);
    window.Alpine.plugin(sortable_default);
    window.Alpine.plugin(module_default);
    window.Alpine.plugin(module_default2);
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
      const matches3 = part.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s);
      if (matches3 === null || matches3.length !== 3) {
        return null;
      }
      const condition = matches3[1];
      const value2 = matches3[2];
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

@alpinejs/focus/dist/module.esm.js:
  (*!
  * tabbable 5.2.1
  * @license MIT, https://github.com/focus-trap/tabbable/blob/master/LICENSE
  *)
  (*!
  * focus-trap 6.6.1
  * @license MIT, https://github.com/focus-trap/focus-trap/blob/master/LICENSE
  *)

sortablejs/modular/sortable.esm.js:
  (**!
   * Sortable 1.15.0
   * @author	RubaXa   <trash@rubaxa.org>
   * @author	owenm    <owen23355@gmail.com>
   * @license MIT
   *)
*/
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0Bhd2NvZGVzL2FscGluZS1mbG9hdGluZy11aS9kaXN0L21vZHVsZS5lc20uanMiLCAiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL2FscGluZS1sYXp5LWxvYWQtYXNzZXRzL2Rpc3QvYWxwaW5lLWxhenktbG9hZC1hc3NldHMuZXNtLmpzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9AYWxwaW5lanMvZm9jdXMvZGlzdC9tb2R1bGUuZXNtLmpzIiwgIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9zb3J0YWJsZWpzL21vZHVsYXIvc29ydGFibGUuZXNtLmpzIiwgIi4uL3Jlc291cmNlcy9qcy9zb3J0YWJsZS5qcyIsICIuLi9yZXNvdXJjZXMvanMvaW5kZXguanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbIi8vIG5vZGVfbW9kdWxlcy9AZmxvYXRpbmctdWkvY29yZS9kaXN0L2Zsb2F0aW5nLXVpLmNvcmUuZXNtLmpzXG5mdW5jdGlvbiBnZXRTaWRlKHBsYWNlbWVudCkge1xuICByZXR1cm4gcGxhY2VtZW50LnNwbGl0KFwiLVwiKVswXTtcbn1cbmZ1bmN0aW9uIGdldEFsaWdubWVudChwbGFjZW1lbnQpIHtcbiAgcmV0dXJuIHBsYWNlbWVudC5zcGxpdChcIi1cIilbMV07XG59XG5mdW5jdGlvbiBnZXRNYWluQXhpc0Zyb21QbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gIHJldHVybiBbXCJ0b3BcIiwgXCJib3R0b21cIl0uaW5jbHVkZXMoZ2V0U2lkZShwbGFjZW1lbnQpKSA/IFwieFwiIDogXCJ5XCI7XG59XG5mdW5jdGlvbiBnZXRMZW5ndGhGcm9tQXhpcyhheGlzKSB7XG4gIHJldHVybiBheGlzID09PSBcInlcIiA/IFwiaGVpZ2h0XCIgOiBcIndpZHRoXCI7XG59XG5mdW5jdGlvbiBjb21wdXRlQ29vcmRzRnJvbVBsYWNlbWVudChfcmVmLCBwbGFjZW1lbnQsIHJ0bCkge1xuICBsZXQge1xuICAgIHJlZmVyZW5jZSxcbiAgICBmbG9hdGluZ1xuICB9ID0gX3JlZjtcbiAgY29uc3QgY29tbW9uWCA9IHJlZmVyZW5jZS54ICsgcmVmZXJlbmNlLndpZHRoIC8gMiAtIGZsb2F0aW5nLndpZHRoIC8gMjtcbiAgY29uc3QgY29tbW9uWSA9IHJlZmVyZW5jZS55ICsgcmVmZXJlbmNlLmhlaWdodCAvIDIgLSBmbG9hdGluZy5oZWlnaHQgLyAyO1xuICBjb25zdCBtYWluQXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBsZW5ndGggPSBnZXRMZW5ndGhGcm9tQXhpcyhtYWluQXhpcyk7XG4gIGNvbnN0IGNvbW1vbkFsaWduID0gcmVmZXJlbmNlW2xlbmd0aF0gLyAyIC0gZmxvYXRpbmdbbGVuZ3RoXSAvIDI7XG4gIGNvbnN0IHNpZGUgPSBnZXRTaWRlKHBsYWNlbWVudCk7XG4gIGNvbnN0IGlzVmVydGljYWwgPSBtYWluQXhpcyA9PT0gXCJ4XCI7XG4gIGxldCBjb29yZHM7XG4gIHN3aXRjaCAoc2lkZSkge1xuICAgIGNhc2UgXCJ0b3BcIjpcbiAgICAgIGNvb3JkcyA9IHtcbiAgICAgICAgeDogY29tbW9uWCxcbiAgICAgICAgeTogcmVmZXJlbmNlLnkgLSBmbG9hdGluZy5oZWlnaHRcbiAgICAgIH07XG4gICAgICBicmVhaztcbiAgICBjYXNlIFwiYm90dG9tXCI6XG4gICAgICBjb29yZHMgPSB7XG4gICAgICAgIHg6IGNvbW1vblgsXG4gICAgICAgIHk6IHJlZmVyZW5jZS55ICsgcmVmZXJlbmNlLmhlaWdodFxuICAgICAgfTtcbiAgICAgIGJyZWFrO1xuICAgIGNhc2UgXCJyaWdodFwiOlxuICAgICAgY29vcmRzID0ge1xuICAgICAgICB4OiByZWZlcmVuY2UueCArIHJlZmVyZW5jZS53aWR0aCxcbiAgICAgICAgeTogY29tbW9uWVxuICAgICAgfTtcbiAgICAgIGJyZWFrO1xuICAgIGNhc2UgXCJsZWZ0XCI6XG4gICAgICBjb29yZHMgPSB7XG4gICAgICAgIHg6IHJlZmVyZW5jZS54IC0gZmxvYXRpbmcud2lkdGgsXG4gICAgICAgIHk6IGNvbW1vbllcbiAgICAgIH07XG4gICAgICBicmVhaztcbiAgICBkZWZhdWx0OlxuICAgICAgY29vcmRzID0ge1xuICAgICAgICB4OiByZWZlcmVuY2UueCxcbiAgICAgICAgeTogcmVmZXJlbmNlLnlcbiAgICAgIH07XG4gIH1cbiAgc3dpdGNoIChnZXRBbGlnbm1lbnQocGxhY2VtZW50KSkge1xuICAgIGNhc2UgXCJzdGFydFwiOlxuICAgICAgY29vcmRzW21haW5BeGlzXSAtPSBjb21tb25BbGlnbiAqIChydGwgJiYgaXNWZXJ0aWNhbCA/IC0xIDogMSk7XG4gICAgICBicmVhaztcbiAgICBjYXNlIFwiZW5kXCI6XG4gICAgICBjb29yZHNbbWFpbkF4aXNdICs9IGNvbW1vbkFsaWduICogKHJ0bCAmJiBpc1ZlcnRpY2FsID8gLTEgOiAxKTtcbiAgICAgIGJyZWFrO1xuICB9XG4gIHJldHVybiBjb29yZHM7XG59XG52YXIgY29tcHV0ZVBvc2l0aW9uID0gYXN5bmMgKHJlZmVyZW5jZSwgZmxvYXRpbmcsIGNvbmZpZykgPT4ge1xuICBjb25zdCB7XG4gICAgcGxhY2VtZW50ID0gXCJib3R0b21cIixcbiAgICBzdHJhdGVneSA9IFwiYWJzb2x1dGVcIixcbiAgICBtaWRkbGV3YXJlID0gW10sXG4gICAgcGxhdGZvcm06IHBsYXRmb3JtMlxuICB9ID0gY29uZmlnO1xuICBjb25zdCBydGwgPSBhd2FpdCAocGxhdGZvcm0yLmlzUlRMID09IG51bGwgPyB2b2lkIDAgOiBwbGF0Zm9ybTIuaXNSVEwoZmxvYXRpbmcpKTtcbiAgaWYgKHRydWUpIHtcbiAgICBpZiAocGxhdGZvcm0yID09IG51bGwpIHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoW1wiRmxvYXRpbmcgVUk6IGBwbGF0Zm9ybWAgcHJvcGVydHkgd2FzIG5vdCBwYXNzZWQgdG8gY29uZmlnLiBJZiB5b3VcIiwgXCJ3YW50IHRvIHVzZSBGbG9hdGluZyBVSSBvbiB0aGUgd2ViLCBpbnN0YWxsIEBmbG9hdGluZy11aS9kb21cIiwgXCJpbnN0ZWFkIG9mIHRoZSAvY29yZSBwYWNrYWdlLiBPdGhlcndpc2UsIHlvdSBjYW4gY3JlYXRlIHlvdXIgb3duXCIsIFwiYHBsYXRmb3JtYDogaHR0cHM6Ly9mbG9hdGluZy11aS5jb20vZG9jcy9wbGF0Zm9ybVwiXS5qb2luKFwiIFwiKSk7XG4gICAgfVxuICAgIGlmIChtaWRkbGV3YXJlLmZpbHRlcigoX3JlZikgPT4ge1xuICAgICAgbGV0IHtcbiAgICAgICAgbmFtZVxuICAgICAgfSA9IF9yZWY7XG4gICAgICByZXR1cm4gbmFtZSA9PT0gXCJhdXRvUGxhY2VtZW50XCIgfHwgbmFtZSA9PT0gXCJmbGlwXCI7XG4gICAgfSkubGVuZ3RoID4gMSkge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKFtcIkZsb2F0aW5nIFVJOiBkdXBsaWNhdGUgYGZsaXBgIGFuZC9vciBgYXV0b1BsYWNlbWVudGBcIiwgXCJtaWRkbGV3YXJlIGRldGVjdGVkLiBUaGlzIHdpbGwgbGVhZCB0byBhbiBpbmZpbml0ZSBsb29wLiBFbnN1cmUgb25seVwiLCBcIm9uZSBvZiBlaXRoZXIgaGFzIGJlZW4gcGFzc2VkIHRvIHRoZSBgbWlkZGxld2FyZWAgYXJyYXkuXCJdLmpvaW4oXCIgXCIpKTtcbiAgICB9XG4gIH1cbiAgbGV0IHJlY3RzID0gYXdhaXQgcGxhdGZvcm0yLmdldEVsZW1lbnRSZWN0cyh7XG4gICAgcmVmZXJlbmNlLFxuICAgIGZsb2F0aW5nLFxuICAgIHN0cmF0ZWd5XG4gIH0pO1xuICBsZXQge1xuICAgIHgsXG4gICAgeVxuICB9ID0gY29tcHV0ZUNvb3Jkc0Zyb21QbGFjZW1lbnQocmVjdHMsIHBsYWNlbWVudCwgcnRsKTtcbiAgbGV0IHN0YXRlZnVsUGxhY2VtZW50ID0gcGxhY2VtZW50O1xuICBsZXQgbWlkZGxld2FyZURhdGEgPSB7fTtcbiAgbGV0IF9kZWJ1Z19sb29wX2NvdW50XyA9IDA7XG4gIGZvciAobGV0IGkgPSAwOyBpIDwgbWlkZGxld2FyZS5sZW5ndGg7IGkrKykge1xuICAgIGlmICh0cnVlKSB7XG4gICAgICBfZGVidWdfbG9vcF9jb3VudF8rKztcbiAgICAgIGlmIChfZGVidWdfbG9vcF9jb3VudF8gPiAxMDApIHtcbiAgICAgICAgdGhyb3cgbmV3IEVycm9yKFtcIkZsb2F0aW5nIFVJOiBUaGUgbWlkZGxld2FyZSBsaWZlY3ljbGUgYXBwZWFycyB0byBiZVwiLCBcInJ1bm5pbmcgaW4gYW4gaW5maW5pdGUgbG9vcC4gVGhpcyBpcyB1c3VhbGx5IGNhdXNlZCBieSBhIGByZXNldGBcIiwgXCJjb250aW51YWxseSBiZWluZyByZXR1cm5lZCB3aXRob3V0IGEgYnJlYWsgY29uZGl0aW9uLlwiXS5qb2luKFwiIFwiKSk7XG4gICAgICB9XG4gICAgfVxuICAgIGNvbnN0IHtcbiAgICAgIG5hbWUsXG4gICAgICBmblxuICAgIH0gPSBtaWRkbGV3YXJlW2ldO1xuICAgIGNvbnN0IHtcbiAgICAgIHg6IG5leHRYLFxuICAgICAgeTogbmV4dFksXG4gICAgICBkYXRhLFxuICAgICAgcmVzZXRcbiAgICB9ID0gYXdhaXQgZm4oe1xuICAgICAgeCxcbiAgICAgIHksXG4gICAgICBpbml0aWFsUGxhY2VtZW50OiBwbGFjZW1lbnQsXG4gICAgICBwbGFjZW1lbnQ6IHN0YXRlZnVsUGxhY2VtZW50LFxuICAgICAgc3RyYXRlZ3ksXG4gICAgICBtaWRkbGV3YXJlRGF0YSxcbiAgICAgIHJlY3RzLFxuICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMixcbiAgICAgIGVsZW1lbnRzOiB7XG4gICAgICAgIHJlZmVyZW5jZSxcbiAgICAgICAgZmxvYXRpbmdcbiAgICAgIH1cbiAgICB9KTtcbiAgICB4ID0gbmV4dFggIT0gbnVsbCA/IG5leHRYIDogeDtcbiAgICB5ID0gbmV4dFkgIT0gbnVsbCA/IG5leHRZIDogeTtcbiAgICBtaWRkbGV3YXJlRGF0YSA9IHtcbiAgICAgIC4uLm1pZGRsZXdhcmVEYXRhLFxuICAgICAgW25hbWVdOiB7XG4gICAgICAgIC4uLm1pZGRsZXdhcmVEYXRhW25hbWVdLFxuICAgICAgICAuLi5kYXRhXG4gICAgICB9XG4gICAgfTtcbiAgICBpZiAocmVzZXQpIHtcbiAgICAgIGlmICh0eXBlb2YgcmVzZXQgPT09IFwib2JqZWN0XCIpIHtcbiAgICAgICAgaWYgKHJlc2V0LnBsYWNlbWVudCkge1xuICAgICAgICAgIHN0YXRlZnVsUGxhY2VtZW50ID0gcmVzZXQucGxhY2VtZW50O1xuICAgICAgICB9XG4gICAgICAgIGlmIChyZXNldC5yZWN0cykge1xuICAgICAgICAgIHJlY3RzID0gcmVzZXQucmVjdHMgPT09IHRydWUgPyBhd2FpdCBwbGF0Zm9ybTIuZ2V0RWxlbWVudFJlY3RzKHtcbiAgICAgICAgICAgIHJlZmVyZW5jZSxcbiAgICAgICAgICAgIGZsb2F0aW5nLFxuICAgICAgICAgICAgc3RyYXRlZ3lcbiAgICAgICAgICB9KSA6IHJlc2V0LnJlY3RzO1xuICAgICAgICB9XG4gICAgICAgICh7XG4gICAgICAgICAgeCxcbiAgICAgICAgICB5XG4gICAgICAgIH0gPSBjb21wdXRlQ29vcmRzRnJvbVBsYWNlbWVudChyZWN0cywgc3RhdGVmdWxQbGFjZW1lbnQsIHJ0bCkpO1xuICAgICAgfVxuICAgICAgaSA9IC0xO1xuICAgICAgY29udGludWU7XG4gICAgfVxuICB9XG4gIHJldHVybiB7XG4gICAgeCxcbiAgICB5LFxuICAgIHBsYWNlbWVudDogc3RhdGVmdWxQbGFjZW1lbnQsXG4gICAgc3RyYXRlZ3ksXG4gICAgbWlkZGxld2FyZURhdGFcbiAgfTtcbn07XG5mdW5jdGlvbiBleHBhbmRQYWRkaW5nT2JqZWN0KHBhZGRpbmcpIHtcbiAgcmV0dXJuIHtcbiAgICB0b3A6IDAsXG4gICAgcmlnaHQ6IDAsXG4gICAgYm90dG9tOiAwLFxuICAgIGxlZnQ6IDAsXG4gICAgLi4ucGFkZGluZ1xuICB9O1xufVxuZnVuY3Rpb24gZ2V0U2lkZU9iamVjdEZyb21QYWRkaW5nKHBhZGRpbmcpIHtcbiAgcmV0dXJuIHR5cGVvZiBwYWRkaW5nICE9PSBcIm51bWJlclwiID8gZXhwYW5kUGFkZGluZ09iamVjdChwYWRkaW5nKSA6IHtcbiAgICB0b3A6IHBhZGRpbmcsXG4gICAgcmlnaHQ6IHBhZGRpbmcsXG4gICAgYm90dG9tOiBwYWRkaW5nLFxuICAgIGxlZnQ6IHBhZGRpbmdcbiAgfTtcbn1cbmZ1bmN0aW9uIHJlY3RUb0NsaWVudFJlY3QocmVjdCkge1xuICByZXR1cm4ge1xuICAgIC4uLnJlY3QsXG4gICAgdG9wOiByZWN0LnksXG4gICAgbGVmdDogcmVjdC54LFxuICAgIHJpZ2h0OiByZWN0LnggKyByZWN0LndpZHRoLFxuICAgIGJvdHRvbTogcmVjdC55ICsgcmVjdC5oZWlnaHRcbiAgfTtcbn1cbmFzeW5jIGZ1bmN0aW9uIGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIG9wdGlvbnMpIHtcbiAgdmFyIF9hd2FpdCRwbGF0Zm9ybSRpc0VsZTtcbiAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgIG9wdGlvbnMgPSB7fTtcbiAgfVxuICBjb25zdCB7XG4gICAgeCxcbiAgICB5LFxuICAgIHBsYXRmb3JtOiBwbGF0Zm9ybTIsXG4gICAgcmVjdHMsXG4gICAgZWxlbWVudHMsXG4gICAgc3RyYXRlZ3lcbiAgfSA9IG1pZGRsZXdhcmVBcmd1bWVudHM7XG4gIGNvbnN0IHtcbiAgICBib3VuZGFyeSA9IFwiY2xpcHBpbmdBbmNlc3RvcnNcIixcbiAgICByb290Qm91bmRhcnkgPSBcInZpZXdwb3J0XCIsXG4gICAgZWxlbWVudENvbnRleHQgPSBcImZsb2F0aW5nXCIsXG4gICAgYWx0Qm91bmRhcnkgPSBmYWxzZSxcbiAgICBwYWRkaW5nID0gMFxuICB9ID0gb3B0aW9ucztcbiAgY29uc3QgcGFkZGluZ09iamVjdCA9IGdldFNpZGVPYmplY3RGcm9tUGFkZGluZyhwYWRkaW5nKTtcbiAgY29uc3QgYWx0Q29udGV4dCA9IGVsZW1lbnRDb250ZXh0ID09PSBcImZsb2F0aW5nXCIgPyBcInJlZmVyZW5jZVwiIDogXCJmbG9hdGluZ1wiO1xuICBjb25zdCBlbGVtZW50ID0gZWxlbWVudHNbYWx0Qm91bmRhcnkgPyBhbHRDb250ZXh0IDogZWxlbWVudENvbnRleHRdO1xuICBjb25zdCBjbGlwcGluZ0NsaWVudFJlY3QgPSByZWN0VG9DbGllbnRSZWN0KGF3YWl0IHBsYXRmb3JtMi5nZXRDbGlwcGluZ1JlY3Qoe1xuICAgIGVsZW1lbnQ6ICgoX2F3YWl0JHBsYXRmb3JtJGlzRWxlID0gYXdhaXQgKHBsYXRmb3JtMi5pc0VsZW1lbnQgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5pc0VsZW1lbnQoZWxlbWVudCkpKSAhPSBudWxsID8gX2F3YWl0JHBsYXRmb3JtJGlzRWxlIDogdHJ1ZSkgPyBlbGVtZW50IDogZWxlbWVudC5jb250ZXh0RWxlbWVudCB8fCBhd2FpdCAocGxhdGZvcm0yLmdldERvY3VtZW50RWxlbWVudCA9PSBudWxsID8gdm9pZCAwIDogcGxhdGZvcm0yLmdldERvY3VtZW50RWxlbWVudChlbGVtZW50cy5mbG9hdGluZykpLFxuICAgIGJvdW5kYXJ5LFxuICAgIHJvb3RCb3VuZGFyeSxcbiAgICBzdHJhdGVneVxuICB9KSk7XG4gIGNvbnN0IGVsZW1lbnRDbGllbnRSZWN0ID0gcmVjdFRvQ2xpZW50UmVjdChwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3QgPyBhd2FpdCBwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3Qoe1xuICAgIHJlY3Q6IGVsZW1lbnRDb250ZXh0ID09PSBcImZsb2F0aW5nXCIgPyB7XG4gICAgICAuLi5yZWN0cy5mbG9hdGluZyxcbiAgICAgIHgsXG4gICAgICB5XG4gICAgfSA6IHJlY3RzLnJlZmVyZW5jZSxcbiAgICBvZmZzZXRQYXJlbnQ6IGF3YWl0IChwbGF0Zm9ybTIuZ2V0T2Zmc2V0UGFyZW50ID09IG51bGwgPyB2b2lkIDAgOiBwbGF0Zm9ybTIuZ2V0T2Zmc2V0UGFyZW50KGVsZW1lbnRzLmZsb2F0aW5nKSksXG4gICAgc3RyYXRlZ3lcbiAgfSkgOiByZWN0c1tlbGVtZW50Q29udGV4dF0pO1xuICByZXR1cm4ge1xuICAgIHRvcDogY2xpcHBpbmdDbGllbnRSZWN0LnRvcCAtIGVsZW1lbnRDbGllbnRSZWN0LnRvcCArIHBhZGRpbmdPYmplY3QudG9wLFxuICAgIGJvdHRvbTogZWxlbWVudENsaWVudFJlY3QuYm90dG9tIC0gY2xpcHBpbmdDbGllbnRSZWN0LmJvdHRvbSArIHBhZGRpbmdPYmplY3QuYm90dG9tLFxuICAgIGxlZnQ6IGNsaXBwaW5nQ2xpZW50UmVjdC5sZWZ0IC0gZWxlbWVudENsaWVudFJlY3QubGVmdCArIHBhZGRpbmdPYmplY3QubGVmdCxcbiAgICByaWdodDogZWxlbWVudENsaWVudFJlY3QucmlnaHQgLSBjbGlwcGluZ0NsaWVudFJlY3QucmlnaHQgKyBwYWRkaW5nT2JqZWN0LnJpZ2h0XG4gIH07XG59XG52YXIgbWluID0gTWF0aC5taW47XG52YXIgbWF4ID0gTWF0aC5tYXg7XG5mdW5jdGlvbiB3aXRoaW4obWluJDEsIHZhbHVlLCBtYXgkMSkge1xuICByZXR1cm4gbWF4KG1pbiQxLCBtaW4odmFsdWUsIG1heCQxKSk7XG59XG52YXIgYXJyb3cgPSAob3B0aW9ucykgPT4gKHtcbiAgbmFtZTogXCJhcnJvd1wiLFxuICBvcHRpb25zLFxuICBhc3luYyBmbihtaWRkbGV3YXJlQXJndW1lbnRzKSB7XG4gICAgY29uc3Qge1xuICAgICAgZWxlbWVudCxcbiAgICAgIHBhZGRpbmcgPSAwXG4gICAgfSA9IG9wdGlvbnMgIT0gbnVsbCA/IG9wdGlvbnMgOiB7fTtcbiAgICBjb25zdCB7XG4gICAgICB4LFxuICAgICAgeSxcbiAgICAgIHBsYWNlbWVudCxcbiAgICAgIHJlY3RzLFxuICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMlxuICAgIH0gPSBtaWRkbGV3YXJlQXJndW1lbnRzO1xuICAgIGlmIChlbGVtZW50ID09IG51bGwpIHtcbiAgICAgIGlmICh0cnVlKSB7XG4gICAgICAgIGNvbnNvbGUud2FybihcIkZsb2F0aW5nIFVJOiBObyBgZWxlbWVudGAgd2FzIHBhc3NlZCB0byB0aGUgYGFycm93YCBtaWRkbGV3YXJlLlwiKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB7fTtcbiAgICB9XG4gICAgY29uc3QgcGFkZGluZ09iamVjdCA9IGdldFNpZGVPYmplY3RGcm9tUGFkZGluZyhwYWRkaW5nKTtcbiAgICBjb25zdCBjb29yZHMgPSB7XG4gICAgICB4LFxuICAgICAgeVxuICAgIH07XG4gICAgY29uc3QgYXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICAgIGNvbnN0IGxlbmd0aCA9IGdldExlbmd0aEZyb21BeGlzKGF4aXMpO1xuICAgIGNvbnN0IGFycm93RGltZW5zaW9ucyA9IGF3YWl0IHBsYXRmb3JtMi5nZXREaW1lbnNpb25zKGVsZW1lbnQpO1xuICAgIGNvbnN0IG1pblByb3AgPSBheGlzID09PSBcInlcIiA/IFwidG9wXCIgOiBcImxlZnRcIjtcbiAgICBjb25zdCBtYXhQcm9wID0gYXhpcyA9PT0gXCJ5XCIgPyBcImJvdHRvbVwiIDogXCJyaWdodFwiO1xuICAgIGNvbnN0IGVuZERpZmYgPSByZWN0cy5yZWZlcmVuY2VbbGVuZ3RoXSArIHJlY3RzLnJlZmVyZW5jZVtheGlzXSAtIGNvb3Jkc1theGlzXSAtIHJlY3RzLmZsb2F0aW5nW2xlbmd0aF07XG4gICAgY29uc3Qgc3RhcnREaWZmID0gY29vcmRzW2F4aXNdIC0gcmVjdHMucmVmZXJlbmNlW2F4aXNdO1xuICAgIGNvbnN0IGFycm93T2Zmc2V0UGFyZW50ID0gYXdhaXQgKHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQoZWxlbWVudCkpO1xuICAgIGNvbnN0IGNsaWVudFNpemUgPSBhcnJvd09mZnNldFBhcmVudCA/IGF4aXMgPT09IFwieVwiID8gYXJyb3dPZmZzZXRQYXJlbnQuY2xpZW50SGVpZ2h0IHx8IDAgOiBhcnJvd09mZnNldFBhcmVudC5jbGllbnRXaWR0aCB8fCAwIDogMDtcbiAgICBjb25zdCBjZW50ZXJUb1JlZmVyZW5jZSA9IGVuZERpZmYgLyAyIC0gc3RhcnREaWZmIC8gMjtcbiAgICBjb25zdCBtaW4zID0gcGFkZGluZ09iamVjdFttaW5Qcm9wXTtcbiAgICBjb25zdCBtYXgzID0gY2xpZW50U2l6ZSAtIGFycm93RGltZW5zaW9uc1tsZW5ndGhdIC0gcGFkZGluZ09iamVjdFttYXhQcm9wXTtcbiAgICBjb25zdCBjZW50ZXIgPSBjbGllbnRTaXplIC8gMiAtIGFycm93RGltZW5zaW9uc1tsZW5ndGhdIC8gMiArIGNlbnRlclRvUmVmZXJlbmNlO1xuICAgIGNvbnN0IG9mZnNldDIgPSB3aXRoaW4obWluMywgY2VudGVyLCBtYXgzKTtcbiAgICByZXR1cm4ge1xuICAgICAgZGF0YToge1xuICAgICAgICBbYXhpc106IG9mZnNldDIsXG4gICAgICAgIGNlbnRlck9mZnNldDogY2VudGVyIC0gb2Zmc2V0MlxuICAgICAgfVxuICAgIH07XG4gIH1cbn0pO1xudmFyIGhhc2gkMSA9IHtcbiAgbGVmdDogXCJyaWdodFwiLFxuICByaWdodDogXCJsZWZ0XCIsXG4gIGJvdHRvbTogXCJ0b3BcIixcbiAgdG9wOiBcImJvdHRvbVwiXG59O1xuZnVuY3Rpb24gZ2V0T3Bwb3NpdGVQbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gIHJldHVybiBwbGFjZW1lbnQucmVwbGFjZSgvbGVmdHxyaWdodHxib3R0b218dG9wL2csIChtYXRjaGVkKSA9PiBoYXNoJDFbbWF0Y2hlZF0pO1xufVxuZnVuY3Rpb24gZ2V0QWxpZ25tZW50U2lkZXMocGxhY2VtZW50LCByZWN0cywgcnRsKSB7XG4gIGlmIChydGwgPT09IHZvaWQgMCkge1xuICAgIHJ0bCA9IGZhbHNlO1xuICB9XG4gIGNvbnN0IGFsaWdubWVudCA9IGdldEFsaWdubWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBtYWluQXhpcyA9IGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBsZW5ndGggPSBnZXRMZW5ndGhGcm9tQXhpcyhtYWluQXhpcyk7XG4gIGxldCBtYWluQWxpZ25tZW50U2lkZSA9IG1haW5BeGlzID09PSBcInhcIiA/IGFsaWdubWVudCA9PT0gKHJ0bCA/IFwiZW5kXCIgOiBcInN0YXJ0XCIpID8gXCJyaWdodFwiIDogXCJsZWZ0XCIgOiBhbGlnbm1lbnQgPT09IFwic3RhcnRcIiA/IFwiYm90dG9tXCIgOiBcInRvcFwiO1xuICBpZiAocmVjdHMucmVmZXJlbmNlW2xlbmd0aF0gPiByZWN0cy5mbG9hdGluZ1tsZW5ndGhdKSB7XG4gICAgbWFpbkFsaWdubWVudFNpZGUgPSBnZXRPcHBvc2l0ZVBsYWNlbWVudChtYWluQWxpZ25tZW50U2lkZSk7XG4gIH1cbiAgcmV0dXJuIHtcbiAgICBtYWluOiBtYWluQWxpZ25tZW50U2lkZSxcbiAgICBjcm9zczogZ2V0T3Bwb3NpdGVQbGFjZW1lbnQobWFpbkFsaWdubWVudFNpZGUpXG4gIH07XG59XG52YXIgaGFzaCA9IHtcbiAgc3RhcnQ6IFwiZW5kXCIsXG4gIGVuZDogXCJzdGFydFwiXG59O1xuZnVuY3Rpb24gZ2V0T3Bwb3NpdGVBbGlnbm1lbnRQbGFjZW1lbnQocGxhY2VtZW50KSB7XG4gIHJldHVybiBwbGFjZW1lbnQucmVwbGFjZSgvc3RhcnR8ZW5kL2csIChtYXRjaGVkKSA9PiBoYXNoW21hdGNoZWRdKTtcbn1cbnZhciBzaWRlcyA9IFtcInRvcFwiLCBcInJpZ2h0XCIsIFwiYm90dG9tXCIsIFwibGVmdFwiXTtcbnZhciBhbGxQbGFjZW1lbnRzID0gLyogQF9fUFVSRV9fICovIHNpZGVzLnJlZHVjZSgoYWNjLCBzaWRlKSA9PiBhY2MuY29uY2F0KHNpZGUsIHNpZGUgKyBcIi1zdGFydFwiLCBzaWRlICsgXCItZW5kXCIpLCBbXSk7XG5mdW5jdGlvbiBnZXRQbGFjZW1lbnRMaXN0KGFsaWdubWVudCwgYXV0b0FsaWdubWVudCwgYWxsb3dlZFBsYWNlbWVudHMpIHtcbiAgY29uc3QgYWxsb3dlZFBsYWNlbWVudHNTb3J0ZWRCeUFsaWdubWVudCA9IGFsaWdubWVudCA/IFsuLi5hbGxvd2VkUGxhY2VtZW50cy5maWx0ZXIoKHBsYWNlbWVudCkgPT4gZ2V0QWxpZ25tZW50KHBsYWNlbWVudCkgPT09IGFsaWdubWVudCksIC4uLmFsbG93ZWRQbGFjZW1lbnRzLmZpbHRlcigocGxhY2VtZW50KSA9PiBnZXRBbGlnbm1lbnQocGxhY2VtZW50KSAhPT0gYWxpZ25tZW50KV0gOiBhbGxvd2VkUGxhY2VtZW50cy5maWx0ZXIoKHBsYWNlbWVudCkgPT4gZ2V0U2lkZShwbGFjZW1lbnQpID09PSBwbGFjZW1lbnQpO1xuICByZXR1cm4gYWxsb3dlZFBsYWNlbWVudHNTb3J0ZWRCeUFsaWdubWVudC5maWx0ZXIoKHBsYWNlbWVudCkgPT4ge1xuICAgIGlmIChhbGlnbm1lbnQpIHtcbiAgICAgIHJldHVybiBnZXRBbGlnbm1lbnQocGxhY2VtZW50KSA9PT0gYWxpZ25tZW50IHx8IChhdXRvQWxpZ25tZW50ID8gZ2V0T3Bwb3NpdGVBbGlnbm1lbnRQbGFjZW1lbnQocGxhY2VtZW50KSAhPT0gcGxhY2VtZW50IDogZmFsc2UpO1xuICAgIH1cbiAgICByZXR1cm4gdHJ1ZTtcbiAgfSk7XG59XG52YXIgYXV0b1BsYWNlbWVudCA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcbiAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgIG9wdGlvbnMgPSB7fTtcbiAgfVxuICByZXR1cm4ge1xuICAgIG5hbWU6IFwiYXV0b1BsYWNlbWVudFwiLFxuICAgIG9wdGlvbnMsXG4gICAgYXN5bmMgZm4obWlkZGxld2FyZUFyZ3VtZW50cykge1xuICAgICAgdmFyIF9taWRkbGV3YXJlRGF0YSRhdXRvUCwgX21pZGRsZXdhcmVEYXRhJGF1dG9QMiwgX21pZGRsZXdhcmVEYXRhJGF1dG9QMywgX21pZGRsZXdhcmVEYXRhJGF1dG9QNCwgX3BsYWNlbWVudHNTb3J0ZWRCeUxlO1xuICAgICAgY29uc3Qge1xuICAgICAgICB4LFxuICAgICAgICB5LFxuICAgICAgICByZWN0cyxcbiAgICAgICAgbWlkZGxld2FyZURhdGEsXG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMixcbiAgICAgICAgZWxlbWVudHNcbiAgICAgIH0gPSBtaWRkbGV3YXJlQXJndW1lbnRzO1xuICAgICAgY29uc3Qge1xuICAgICAgICBhbGlnbm1lbnQgPSBudWxsLFxuICAgICAgICBhbGxvd2VkUGxhY2VtZW50cyA9IGFsbFBsYWNlbWVudHMsXG4gICAgICAgIGF1dG9BbGlnbm1lbnQgPSB0cnVlLFxuICAgICAgICAuLi5kZXRlY3RPdmVyZmxvd09wdGlvbnNcbiAgICAgIH0gPSBvcHRpb25zO1xuICAgICAgY29uc3QgcGxhY2VtZW50cyA9IGdldFBsYWNlbWVudExpc3QoYWxpZ25tZW50LCBhdXRvQWxpZ25tZW50LCBhbGxvd2VkUGxhY2VtZW50cyk7XG4gICAgICBjb25zdCBvdmVyZmxvdyA9IGF3YWl0IGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIGRldGVjdE92ZXJmbG93T3B0aW9ucyk7XG4gICAgICBjb25zdCBjdXJyZW50SW5kZXggPSAoX21pZGRsZXdhcmVEYXRhJGF1dG9QID0gKF9taWRkbGV3YXJlRGF0YSRhdXRvUDIgPSBtaWRkbGV3YXJlRGF0YS5hdXRvUGxhY2VtZW50KSA9PSBudWxsID8gdm9pZCAwIDogX21pZGRsZXdhcmVEYXRhJGF1dG9QMi5pbmRleCkgIT0gbnVsbCA/IF9taWRkbGV3YXJlRGF0YSRhdXRvUCA6IDA7XG4gICAgICBjb25zdCBjdXJyZW50UGxhY2VtZW50ID0gcGxhY2VtZW50c1tjdXJyZW50SW5kZXhdO1xuICAgICAgaWYgKGN1cnJlbnRQbGFjZW1lbnQgPT0gbnVsbCkge1xuICAgICAgICByZXR1cm4ge307XG4gICAgICB9XG4gICAgICBjb25zdCB7XG4gICAgICAgIG1haW4sXG4gICAgICAgIGNyb3NzXG4gICAgICB9ID0gZ2V0QWxpZ25tZW50U2lkZXMoY3VycmVudFBsYWNlbWVudCwgcmVjdHMsIGF3YWl0IChwbGF0Zm9ybTIuaXNSVEwgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5pc1JUTChlbGVtZW50cy5mbG9hdGluZykpKTtcbiAgICAgIGlmIChwbGFjZW1lbnQgIT09IGN1cnJlbnRQbGFjZW1lbnQpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICB4LFxuICAgICAgICAgIHksXG4gICAgICAgICAgcmVzZXQ6IHtcbiAgICAgICAgICAgIHBsYWNlbWVudDogcGxhY2VtZW50c1swXVxuICAgICAgICAgIH1cbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIGNvbnN0IGN1cnJlbnRPdmVyZmxvd3MgPSBbb3ZlcmZsb3dbZ2V0U2lkZShjdXJyZW50UGxhY2VtZW50KV0sIG92ZXJmbG93W21haW5dLCBvdmVyZmxvd1tjcm9zc11dO1xuICAgICAgY29uc3QgYWxsT3ZlcmZsb3dzID0gWy4uLihfbWlkZGxld2FyZURhdGEkYXV0b1AzID0gKF9taWRkbGV3YXJlRGF0YSRhdXRvUDQgPSBtaWRkbGV3YXJlRGF0YS5hdXRvUGxhY2VtZW50KSA9PSBudWxsID8gdm9pZCAwIDogX21pZGRsZXdhcmVEYXRhJGF1dG9QNC5vdmVyZmxvd3MpICE9IG51bGwgPyBfbWlkZGxld2FyZURhdGEkYXV0b1AzIDogW10sIHtcbiAgICAgICAgcGxhY2VtZW50OiBjdXJyZW50UGxhY2VtZW50LFxuICAgICAgICBvdmVyZmxvd3M6IGN1cnJlbnRPdmVyZmxvd3NcbiAgICAgIH1dO1xuICAgICAgY29uc3QgbmV4dFBsYWNlbWVudCA9IHBsYWNlbWVudHNbY3VycmVudEluZGV4ICsgMV07XG4gICAgICBpZiAobmV4dFBsYWNlbWVudCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgIGluZGV4OiBjdXJyZW50SW5kZXggKyAxLFxuICAgICAgICAgICAgb3ZlcmZsb3dzOiBhbGxPdmVyZmxvd3NcbiAgICAgICAgICB9LFxuICAgICAgICAgIHJlc2V0OiB7XG4gICAgICAgICAgICBwbGFjZW1lbnQ6IG5leHRQbGFjZW1lbnRcbiAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICBjb25zdCBwbGFjZW1lbnRzU29ydGVkQnlMZWFzdE92ZXJmbG93ID0gYWxsT3ZlcmZsb3dzLnNsaWNlKCkuc29ydCgoYSwgYikgPT4gYS5vdmVyZmxvd3NbMF0gLSBiLm92ZXJmbG93c1swXSk7XG4gICAgICBjb25zdCBwbGFjZW1lbnRUaGF0Rml0c09uQWxsU2lkZXMgPSAoX3BsYWNlbWVudHNTb3J0ZWRCeUxlID0gcGxhY2VtZW50c1NvcnRlZEJ5TGVhc3RPdmVyZmxvdy5maW5kKChfcmVmKSA9PiB7XG4gICAgICAgIGxldCB7XG4gICAgICAgICAgb3ZlcmZsb3dzXG4gICAgICAgIH0gPSBfcmVmO1xuICAgICAgICByZXR1cm4gb3ZlcmZsb3dzLmV2ZXJ5KChvdmVyZmxvdzIpID0+IG92ZXJmbG93MiA8PSAwKTtcbiAgICAgIH0pKSA9PSBudWxsID8gdm9pZCAwIDogX3BsYWNlbWVudHNTb3J0ZWRCeUxlLnBsYWNlbWVudDtcbiAgICAgIGNvbnN0IHJlc2V0UGxhY2VtZW50ID0gcGxhY2VtZW50VGhhdEZpdHNPbkFsbFNpZGVzICE9IG51bGwgPyBwbGFjZW1lbnRUaGF0Rml0c09uQWxsU2lkZXMgOiBwbGFjZW1lbnRzU29ydGVkQnlMZWFzdE92ZXJmbG93WzBdLnBsYWNlbWVudDtcbiAgICAgIGlmIChyZXNldFBsYWNlbWVudCAhPT0gcGxhY2VtZW50KSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgaW5kZXg6IGN1cnJlbnRJbmRleCArIDEsXG4gICAgICAgICAgICBvdmVyZmxvd3M6IGFsbE92ZXJmbG93c1xuICAgICAgICAgIH0sXG4gICAgICAgICAgcmVzZXQ6IHtcbiAgICAgICAgICAgIHBsYWNlbWVudDogcmVzZXRQbGFjZW1lbnRcbiAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICByZXR1cm4ge307XG4gICAgfVxuICB9O1xufTtcbmZ1bmN0aW9uIGdldEV4cGFuZGVkUGxhY2VtZW50cyhwbGFjZW1lbnQpIHtcbiAgY29uc3Qgb3Bwb3NpdGVQbGFjZW1lbnQgPSBnZXRPcHBvc2l0ZVBsYWNlbWVudChwbGFjZW1lbnQpO1xuICByZXR1cm4gW2dldE9wcG9zaXRlQWxpZ25tZW50UGxhY2VtZW50KHBsYWNlbWVudCksIG9wcG9zaXRlUGxhY2VtZW50LCBnZXRPcHBvc2l0ZUFsaWdubWVudFBsYWNlbWVudChvcHBvc2l0ZVBsYWNlbWVudCldO1xufVxudmFyIGZsaXAgPSBmdW5jdGlvbihvcHRpb25zKSB7XG4gIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICBvcHRpb25zID0ge307XG4gIH1cbiAgcmV0dXJuIHtcbiAgICBuYW1lOiBcImZsaXBcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIHZhciBfbWlkZGxld2FyZURhdGEkZmxpcDtcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgcGxhY2VtZW50LFxuICAgICAgICBtaWRkbGV3YXJlRGF0YSxcbiAgICAgICAgcmVjdHMsXG4gICAgICAgIGluaXRpYWxQbGFjZW1lbnQsXG4gICAgICAgIHBsYXRmb3JtOiBwbGF0Zm9ybTIsXG4gICAgICAgIGVsZW1lbnRzXG4gICAgICB9ID0gbWlkZGxld2FyZUFyZ3VtZW50cztcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgbWFpbkF4aXM6IGNoZWNrTWFpbkF4aXMgPSB0cnVlLFxuICAgICAgICBjcm9zc0F4aXM6IGNoZWNrQ3Jvc3NBeGlzID0gdHJ1ZSxcbiAgICAgICAgZmFsbGJhY2tQbGFjZW1lbnRzOiBzcGVjaWZpZWRGYWxsYmFja1BsYWNlbWVudHMsXG4gICAgICAgIGZhbGxiYWNrU3RyYXRlZ3kgPSBcImJlc3RGaXRcIixcbiAgICAgICAgZmxpcEFsaWdubWVudCA9IHRydWUsXG4gICAgICAgIC4uLmRldGVjdE92ZXJmbG93T3B0aW9uc1xuICAgICAgfSA9IG9wdGlvbnM7XG4gICAgICBjb25zdCBzaWRlID0gZ2V0U2lkZShwbGFjZW1lbnQpO1xuICAgICAgY29uc3QgaXNCYXNlUGxhY2VtZW50ID0gc2lkZSA9PT0gaW5pdGlhbFBsYWNlbWVudDtcbiAgICAgIGNvbnN0IGZhbGxiYWNrUGxhY2VtZW50cyA9IHNwZWNpZmllZEZhbGxiYWNrUGxhY2VtZW50cyB8fCAoaXNCYXNlUGxhY2VtZW50IHx8ICFmbGlwQWxpZ25tZW50ID8gW2dldE9wcG9zaXRlUGxhY2VtZW50KGluaXRpYWxQbGFjZW1lbnQpXSA6IGdldEV4cGFuZGVkUGxhY2VtZW50cyhpbml0aWFsUGxhY2VtZW50KSk7XG4gICAgICBjb25zdCBwbGFjZW1lbnRzID0gW2luaXRpYWxQbGFjZW1lbnQsIC4uLmZhbGxiYWNrUGxhY2VtZW50c107XG4gICAgICBjb25zdCBvdmVyZmxvdyA9IGF3YWl0IGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIGRldGVjdE92ZXJmbG93T3B0aW9ucyk7XG4gICAgICBjb25zdCBvdmVyZmxvd3MgPSBbXTtcbiAgICAgIGxldCBvdmVyZmxvd3NEYXRhID0gKChfbWlkZGxld2FyZURhdGEkZmxpcCA9IG1pZGRsZXdhcmVEYXRhLmZsaXApID09IG51bGwgPyB2b2lkIDAgOiBfbWlkZGxld2FyZURhdGEkZmxpcC5vdmVyZmxvd3MpIHx8IFtdO1xuICAgICAgaWYgKGNoZWNrTWFpbkF4aXMpIHtcbiAgICAgICAgb3ZlcmZsb3dzLnB1c2gob3ZlcmZsb3dbc2lkZV0pO1xuICAgICAgfVxuICAgICAgaWYgKGNoZWNrQ3Jvc3NBeGlzKSB7XG4gICAgICAgIGNvbnN0IHtcbiAgICAgICAgICBtYWluLFxuICAgICAgICAgIGNyb3NzXG4gICAgICAgIH0gPSBnZXRBbGlnbm1lbnRTaWRlcyhwbGFjZW1lbnQsIHJlY3RzLCBhd2FpdCAocGxhdGZvcm0yLmlzUlRMID09IG51bGwgPyB2b2lkIDAgOiBwbGF0Zm9ybTIuaXNSVEwoZWxlbWVudHMuZmxvYXRpbmcpKSk7XG4gICAgICAgIG92ZXJmbG93cy5wdXNoKG92ZXJmbG93W21haW5dLCBvdmVyZmxvd1tjcm9zc10pO1xuICAgICAgfVxuICAgICAgb3ZlcmZsb3dzRGF0YSA9IFsuLi5vdmVyZmxvd3NEYXRhLCB7XG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgb3ZlcmZsb3dzXG4gICAgICB9XTtcbiAgICAgIGlmICghb3ZlcmZsb3dzLmV2ZXJ5KChzaWRlMikgPT4gc2lkZTIgPD0gMCkpIHtcbiAgICAgICAgdmFyIF9taWRkbGV3YXJlRGF0YSRmbGlwJCwgX21pZGRsZXdhcmVEYXRhJGZsaXAyO1xuICAgICAgICBjb25zdCBuZXh0SW5kZXggPSAoKF9taWRkbGV3YXJlRGF0YSRmbGlwJCA9IChfbWlkZGxld2FyZURhdGEkZmxpcDIgPSBtaWRkbGV3YXJlRGF0YS5mbGlwKSA9PSBudWxsID8gdm9pZCAwIDogX21pZGRsZXdhcmVEYXRhJGZsaXAyLmluZGV4KSAhPSBudWxsID8gX21pZGRsZXdhcmVEYXRhJGZsaXAkIDogMCkgKyAxO1xuICAgICAgICBjb25zdCBuZXh0UGxhY2VtZW50ID0gcGxhY2VtZW50c1tuZXh0SW5kZXhdO1xuICAgICAgICBpZiAobmV4dFBsYWNlbWVudCkge1xuICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgIGluZGV4OiBuZXh0SW5kZXgsXG4gICAgICAgICAgICAgIG92ZXJmbG93czogb3ZlcmZsb3dzRGF0YVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHJlc2V0OiB7XG4gICAgICAgICAgICAgIHBsYWNlbWVudDogbmV4dFBsYWNlbWVudFxuICAgICAgICAgICAgfVxuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgbGV0IHJlc2V0UGxhY2VtZW50ID0gXCJib3R0b21cIjtcbiAgICAgICAgc3dpdGNoIChmYWxsYmFja1N0cmF0ZWd5KSB7XG4gICAgICAgICAgY2FzZSBcImJlc3RGaXRcIjoge1xuICAgICAgICAgICAgdmFyIF9vdmVyZmxvd3NEYXRhJG1hcCRzbztcbiAgICAgICAgICAgIGNvbnN0IHBsYWNlbWVudDIgPSAoX292ZXJmbG93c0RhdGEkbWFwJHNvID0gb3ZlcmZsb3dzRGF0YS5tYXAoKGQpID0+IFtkLCBkLm92ZXJmbG93cy5maWx0ZXIoKG92ZXJmbG93MikgPT4gb3ZlcmZsb3cyID4gMCkucmVkdWNlKChhY2MsIG92ZXJmbG93MikgPT4gYWNjICsgb3ZlcmZsb3cyLCAwKV0pLnNvcnQoKGEsIGIpID0+IGFbMV0gLSBiWzFdKVswXSkgPT0gbnVsbCA/IHZvaWQgMCA6IF9vdmVyZmxvd3NEYXRhJG1hcCRzb1swXS5wbGFjZW1lbnQ7XG4gICAgICAgICAgICBpZiAocGxhY2VtZW50Mikge1xuICAgICAgICAgICAgICByZXNldFBsYWNlbWVudCA9IHBsYWNlbWVudDI7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgICAgY2FzZSBcImluaXRpYWxQbGFjZW1lbnRcIjpcbiAgICAgICAgICAgIHJlc2V0UGxhY2VtZW50ID0gaW5pdGlhbFBsYWNlbWVudDtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgICAgIGlmIChwbGFjZW1lbnQgIT09IHJlc2V0UGxhY2VtZW50KSB7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHJlc2V0OiB7XG4gICAgICAgICAgICAgIHBsYWNlbWVudDogcmVzZXRQbGFjZW1lbnRcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9O1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICByZXR1cm4ge307XG4gICAgfVxuICB9O1xufTtcbmZ1bmN0aW9uIGdldFNpZGVPZmZzZXRzKG92ZXJmbG93LCByZWN0KSB7XG4gIHJldHVybiB7XG4gICAgdG9wOiBvdmVyZmxvdy50b3AgLSByZWN0LmhlaWdodCxcbiAgICByaWdodDogb3ZlcmZsb3cucmlnaHQgLSByZWN0LndpZHRoLFxuICAgIGJvdHRvbTogb3ZlcmZsb3cuYm90dG9tIC0gcmVjdC5oZWlnaHQsXG4gICAgbGVmdDogb3ZlcmZsb3cubGVmdCAtIHJlY3Qud2lkdGhcbiAgfTtcbn1cbmZ1bmN0aW9uIGlzQW55U2lkZUZ1bGx5Q2xpcHBlZChvdmVyZmxvdykge1xuICByZXR1cm4gc2lkZXMuc29tZSgoc2lkZSkgPT4gb3ZlcmZsb3dbc2lkZV0gPj0gMCk7XG59XG52YXIgaGlkZSA9IGZ1bmN0aW9uKF90ZW1wKSB7XG4gIGxldCB7XG4gICAgc3RyYXRlZ3kgPSBcInJlZmVyZW5jZUhpZGRlblwiLFxuICAgIC4uLmRldGVjdE92ZXJmbG93T3B0aW9uc1xuICB9ID0gX3RlbXAgPT09IHZvaWQgMCA/IHt9IDogX3RlbXA7XG4gIHJldHVybiB7XG4gICAgbmFtZTogXCJoaWRlXCIsXG4gICAgYXN5bmMgZm4obWlkZGxld2FyZUFyZ3VtZW50cykge1xuICAgICAgY29uc3Qge1xuICAgICAgICByZWN0c1xuICAgICAgfSA9IG1pZGRsZXdhcmVBcmd1bWVudHM7XG4gICAgICBzd2l0Y2ggKHN0cmF0ZWd5KSB7XG4gICAgICAgIGNhc2UgXCJyZWZlcmVuY2VIaWRkZW5cIjoge1xuICAgICAgICAgIGNvbnN0IG92ZXJmbG93ID0gYXdhaXQgZGV0ZWN0T3ZlcmZsb3cobWlkZGxld2FyZUFyZ3VtZW50cywge1xuICAgICAgICAgICAgLi4uZGV0ZWN0T3ZlcmZsb3dPcHRpb25zLFxuICAgICAgICAgICAgZWxlbWVudENvbnRleHQ6IFwicmVmZXJlbmNlXCJcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBjb25zdCBvZmZzZXRzID0gZ2V0U2lkZU9mZnNldHMob3ZlcmZsb3csIHJlY3RzLnJlZmVyZW5jZSk7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgcmVmZXJlbmNlSGlkZGVuT2Zmc2V0czogb2Zmc2V0cyxcbiAgICAgICAgICAgICAgcmVmZXJlbmNlSGlkZGVuOiBpc0FueVNpZGVGdWxseUNsaXBwZWQob2Zmc2V0cylcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9O1xuICAgICAgICB9XG4gICAgICAgIGNhc2UgXCJlc2NhcGVkXCI6IHtcbiAgICAgICAgICBjb25zdCBvdmVyZmxvdyA9IGF3YWl0IGRldGVjdE92ZXJmbG93KG1pZGRsZXdhcmVBcmd1bWVudHMsIHtcbiAgICAgICAgICAgIC4uLmRldGVjdE92ZXJmbG93T3B0aW9ucyxcbiAgICAgICAgICAgIGFsdEJvdW5kYXJ5OiB0cnVlXG4gICAgICAgICAgfSk7XG4gICAgICAgICAgY29uc3Qgb2Zmc2V0cyA9IGdldFNpZGVPZmZzZXRzKG92ZXJmbG93LCByZWN0cy5mbG9hdGluZyk7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgZXNjYXBlZE9mZnNldHM6IG9mZnNldHMsXG4gICAgICAgICAgICAgIGVzY2FwZWQ6IGlzQW55U2lkZUZ1bGx5Q2xpcHBlZChvZmZzZXRzKVxuICAgICAgICAgICAgfVxuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgZGVmYXVsdDoge1xuICAgICAgICAgIHJldHVybiB7fTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cbiAgfTtcbn07XG5mdW5jdGlvbiBjb252ZXJ0VmFsdWVUb0Nvb3JkcyhwbGFjZW1lbnQsIHJlY3RzLCB2YWx1ZSwgcnRsKSB7XG4gIGlmIChydGwgPT09IHZvaWQgMCkge1xuICAgIHJ0bCA9IGZhbHNlO1xuICB9XG4gIGNvbnN0IHNpZGUgPSBnZXRTaWRlKHBsYWNlbWVudCk7XG4gIGNvbnN0IGFsaWdubWVudCA9IGdldEFsaWdubWVudChwbGFjZW1lbnQpO1xuICBjb25zdCBpc1ZlcnRpY2FsID0gZ2V0TWFpbkF4aXNGcm9tUGxhY2VtZW50KHBsYWNlbWVudCkgPT09IFwieFwiO1xuICBjb25zdCBtYWluQXhpc011bHRpID0gW1wibGVmdFwiLCBcInRvcFwiXS5pbmNsdWRlcyhzaWRlKSA/IC0xIDogMTtcbiAgY29uc3QgY3Jvc3NBeGlzTXVsdGkgPSBydGwgJiYgaXNWZXJ0aWNhbCA/IC0xIDogMTtcbiAgY29uc3QgcmF3VmFsdWUgPSB0eXBlb2YgdmFsdWUgPT09IFwiZnVuY3Rpb25cIiA/IHZhbHVlKHtcbiAgICAuLi5yZWN0cyxcbiAgICBwbGFjZW1lbnRcbiAgfSkgOiB2YWx1ZTtcbiAgbGV0IHtcbiAgICBtYWluQXhpcyxcbiAgICBjcm9zc0F4aXMsXG4gICAgYWxpZ25tZW50QXhpc1xuICB9ID0gdHlwZW9mIHJhd1ZhbHVlID09PSBcIm51bWJlclwiID8ge1xuICAgIG1haW5BeGlzOiByYXdWYWx1ZSxcbiAgICBjcm9zc0F4aXM6IDAsXG4gICAgYWxpZ25tZW50QXhpczogbnVsbFxuICB9IDoge1xuICAgIG1haW5BeGlzOiAwLFxuICAgIGNyb3NzQXhpczogMCxcbiAgICBhbGlnbm1lbnRBeGlzOiBudWxsLFxuICAgIC4uLnJhd1ZhbHVlXG4gIH07XG4gIGlmIChhbGlnbm1lbnQgJiYgdHlwZW9mIGFsaWdubWVudEF4aXMgPT09IFwibnVtYmVyXCIpIHtcbiAgICBjcm9zc0F4aXMgPSBhbGlnbm1lbnQgPT09IFwiZW5kXCIgPyBhbGlnbm1lbnRBeGlzICogLTEgOiBhbGlnbm1lbnRBeGlzO1xuICB9XG4gIHJldHVybiBpc1ZlcnRpY2FsID8ge1xuICAgIHg6IGNyb3NzQXhpcyAqIGNyb3NzQXhpc011bHRpLFxuICAgIHk6IG1haW5BeGlzICogbWFpbkF4aXNNdWx0aVxuICB9IDoge1xuICAgIHg6IG1haW5BeGlzICogbWFpbkF4aXNNdWx0aSxcbiAgICB5OiBjcm9zc0F4aXMgKiBjcm9zc0F4aXNNdWx0aVxuICB9O1xufVxudmFyIG9mZnNldCA9IGZ1bmN0aW9uKHZhbHVlKSB7XG4gIGlmICh2YWx1ZSA9PT0gdm9pZCAwKSB7XG4gICAgdmFsdWUgPSAwO1xuICB9XG4gIHJldHVybiB7XG4gICAgbmFtZTogXCJvZmZzZXRcIixcbiAgICBvcHRpb25zOiB2YWx1ZSxcbiAgICBhc3luYyBmbihtaWRkbGV3YXJlQXJndW1lbnRzKSB7XG4gICAgICBjb25zdCB7XG4gICAgICAgIHgsXG4gICAgICAgIHksXG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgcmVjdHMsXG4gICAgICAgIHBsYXRmb3JtOiBwbGF0Zm9ybTIsXG4gICAgICAgIGVsZW1lbnRzXG4gICAgICB9ID0gbWlkZGxld2FyZUFyZ3VtZW50cztcbiAgICAgIGNvbnN0IGRpZmZDb29yZHMgPSBjb252ZXJ0VmFsdWVUb0Nvb3JkcyhwbGFjZW1lbnQsIHJlY3RzLCB2YWx1ZSwgYXdhaXQgKHBsYXRmb3JtMi5pc1JUTCA9PSBudWxsID8gdm9pZCAwIDogcGxhdGZvcm0yLmlzUlRMKGVsZW1lbnRzLmZsb2F0aW5nKSkpO1xuICAgICAgcmV0dXJuIHtcbiAgICAgICAgeDogeCArIGRpZmZDb29yZHMueCxcbiAgICAgICAgeTogeSArIGRpZmZDb29yZHMueSxcbiAgICAgICAgZGF0YTogZGlmZkNvb3Jkc1xuICAgICAgfTtcbiAgICB9XG4gIH07XG59O1xuZnVuY3Rpb24gZ2V0Q3Jvc3NBeGlzKGF4aXMpIHtcbiAgcmV0dXJuIGF4aXMgPT09IFwieFwiID8gXCJ5XCIgOiBcInhcIjtcbn1cbnZhciBzaGlmdCA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcbiAgaWYgKG9wdGlvbnMgPT09IHZvaWQgMCkge1xuICAgIG9wdGlvbnMgPSB7fTtcbiAgfVxuICByZXR1cm4ge1xuICAgIG5hbWU6IFwic2hpZnRcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgeCxcbiAgICAgICAgeSxcbiAgICAgICAgcGxhY2VtZW50XG4gICAgICB9ID0gbWlkZGxld2FyZUFyZ3VtZW50cztcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgbWFpbkF4aXM6IGNoZWNrTWFpbkF4aXMgPSB0cnVlLFxuICAgICAgICBjcm9zc0F4aXM6IGNoZWNrQ3Jvc3NBeGlzID0gZmFsc2UsXG4gICAgICAgIGxpbWl0ZXIgPSB7XG4gICAgICAgICAgZm46IChfcmVmKSA9PiB7XG4gICAgICAgICAgICBsZXQge1xuICAgICAgICAgICAgICB4OiB4MixcbiAgICAgICAgICAgICAgeTogeTJcbiAgICAgICAgICAgIH0gPSBfcmVmO1xuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgeDogeDIsXG4gICAgICAgICAgICAgIHk6IHkyXG4gICAgICAgICAgICB9O1xuICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgLi4uZGV0ZWN0T3ZlcmZsb3dPcHRpb25zXG4gICAgICB9ID0gb3B0aW9ucztcbiAgICAgIGNvbnN0IGNvb3JkcyA9IHtcbiAgICAgICAgeCxcbiAgICAgICAgeVxuICAgICAgfTtcbiAgICAgIGNvbnN0IG92ZXJmbG93ID0gYXdhaXQgZGV0ZWN0T3ZlcmZsb3cobWlkZGxld2FyZUFyZ3VtZW50cywgZGV0ZWN0T3ZlcmZsb3dPcHRpb25zKTtcbiAgICAgIGNvbnN0IG1haW5BeGlzID0gZ2V0TWFpbkF4aXNGcm9tUGxhY2VtZW50KGdldFNpZGUocGxhY2VtZW50KSk7XG4gICAgICBjb25zdCBjcm9zc0F4aXMgPSBnZXRDcm9zc0F4aXMobWFpbkF4aXMpO1xuICAgICAgbGV0IG1haW5BeGlzQ29vcmQgPSBjb29yZHNbbWFpbkF4aXNdO1xuICAgICAgbGV0IGNyb3NzQXhpc0Nvb3JkID0gY29vcmRzW2Nyb3NzQXhpc107XG4gICAgICBpZiAoY2hlY2tNYWluQXhpcykge1xuICAgICAgICBjb25zdCBtaW5TaWRlID0gbWFpbkF4aXMgPT09IFwieVwiID8gXCJ0b3BcIiA6IFwibGVmdFwiO1xuICAgICAgICBjb25zdCBtYXhTaWRlID0gbWFpbkF4aXMgPT09IFwieVwiID8gXCJib3R0b21cIiA6IFwicmlnaHRcIjtcbiAgICAgICAgY29uc3QgbWluMyA9IG1haW5BeGlzQ29vcmQgKyBvdmVyZmxvd1ttaW5TaWRlXTtcbiAgICAgICAgY29uc3QgbWF4MyA9IG1haW5BeGlzQ29vcmQgLSBvdmVyZmxvd1ttYXhTaWRlXTtcbiAgICAgICAgbWFpbkF4aXNDb29yZCA9IHdpdGhpbihtaW4zLCBtYWluQXhpc0Nvb3JkLCBtYXgzKTtcbiAgICAgIH1cbiAgICAgIGlmIChjaGVja0Nyb3NzQXhpcykge1xuICAgICAgICBjb25zdCBtaW5TaWRlID0gY3Jvc3NBeGlzID09PSBcInlcIiA/IFwidG9wXCIgOiBcImxlZnRcIjtcbiAgICAgICAgY29uc3QgbWF4U2lkZSA9IGNyb3NzQXhpcyA9PT0gXCJ5XCIgPyBcImJvdHRvbVwiIDogXCJyaWdodFwiO1xuICAgICAgICBjb25zdCBtaW4zID0gY3Jvc3NBeGlzQ29vcmQgKyBvdmVyZmxvd1ttaW5TaWRlXTtcbiAgICAgICAgY29uc3QgbWF4MyA9IGNyb3NzQXhpc0Nvb3JkIC0gb3ZlcmZsb3dbbWF4U2lkZV07XG4gICAgICAgIGNyb3NzQXhpc0Nvb3JkID0gd2l0aGluKG1pbjMsIGNyb3NzQXhpc0Nvb3JkLCBtYXgzKTtcbiAgICAgIH1cbiAgICAgIGNvbnN0IGxpbWl0ZWRDb29yZHMgPSBsaW1pdGVyLmZuKHtcbiAgICAgICAgLi4ubWlkZGxld2FyZUFyZ3VtZW50cyxcbiAgICAgICAgW21haW5BeGlzXTogbWFpbkF4aXNDb29yZCxcbiAgICAgICAgW2Nyb3NzQXhpc106IGNyb3NzQXhpc0Nvb3JkXG4gICAgICB9KTtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIC4uLmxpbWl0ZWRDb29yZHMsXG4gICAgICAgIGRhdGE6IHtcbiAgICAgICAgICB4OiBsaW1pdGVkQ29vcmRzLnggLSB4LFxuICAgICAgICAgIHk6IGxpbWl0ZWRDb29yZHMueSAtIHlcbiAgICAgICAgfVxuICAgICAgfTtcbiAgICB9XG4gIH07XG59O1xudmFyIHNpemUgPSBmdW5jdGlvbihvcHRpb25zKSB7XG4gIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICBvcHRpb25zID0ge307XG4gIH1cbiAgcmV0dXJuIHtcbiAgICBuYW1lOiBcInNpemVcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIGNvbnN0IHtcbiAgICAgICAgcGxhY2VtZW50LFxuICAgICAgICByZWN0cyxcbiAgICAgICAgcGxhdGZvcm06IHBsYXRmb3JtMixcbiAgICAgICAgZWxlbWVudHNcbiAgICAgIH0gPSBtaWRkbGV3YXJlQXJndW1lbnRzO1xuICAgICAgY29uc3Qge1xuICAgICAgICBhcHBseSxcbiAgICAgICAgLi4uZGV0ZWN0T3ZlcmZsb3dPcHRpb25zXG4gICAgICB9ID0gb3B0aW9ucztcbiAgICAgIGNvbnN0IG92ZXJmbG93ID0gYXdhaXQgZGV0ZWN0T3ZlcmZsb3cobWlkZGxld2FyZUFyZ3VtZW50cywgZGV0ZWN0T3ZlcmZsb3dPcHRpb25zKTtcbiAgICAgIGNvbnN0IHNpZGUgPSBnZXRTaWRlKHBsYWNlbWVudCk7XG4gICAgICBjb25zdCBhbGlnbm1lbnQgPSBnZXRBbGlnbm1lbnQocGxhY2VtZW50KTtcbiAgICAgIGxldCBoZWlnaHRTaWRlO1xuICAgICAgbGV0IHdpZHRoU2lkZTtcbiAgICAgIGlmIChzaWRlID09PSBcInRvcFwiIHx8IHNpZGUgPT09IFwiYm90dG9tXCIpIHtcbiAgICAgICAgaGVpZ2h0U2lkZSA9IHNpZGU7XG4gICAgICAgIHdpZHRoU2lkZSA9IGFsaWdubWVudCA9PT0gKGF3YWl0IChwbGF0Zm9ybTIuaXNSVEwgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5pc1JUTChlbGVtZW50cy5mbG9hdGluZykpID8gXCJzdGFydFwiIDogXCJlbmRcIikgPyBcImxlZnRcIiA6IFwicmlnaHRcIjtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHdpZHRoU2lkZSA9IHNpZGU7XG4gICAgICAgIGhlaWdodFNpZGUgPSBhbGlnbm1lbnQgPT09IFwiZW5kXCIgPyBcInRvcFwiIDogXCJib3R0b21cIjtcbiAgICAgIH1cbiAgICAgIGNvbnN0IHhNaW4gPSBtYXgob3ZlcmZsb3cubGVmdCwgMCk7XG4gICAgICBjb25zdCB4TWF4ID0gbWF4KG92ZXJmbG93LnJpZ2h0LCAwKTtcbiAgICAgIGNvbnN0IHlNaW4gPSBtYXgob3ZlcmZsb3cudG9wLCAwKTtcbiAgICAgIGNvbnN0IHlNYXggPSBtYXgob3ZlcmZsb3cuYm90dG9tLCAwKTtcbiAgICAgIGNvbnN0IGRpbWVuc2lvbnMgPSB7XG4gICAgICAgIGhlaWdodDogcmVjdHMuZmxvYXRpbmcuaGVpZ2h0IC0gKFtcImxlZnRcIiwgXCJyaWdodFwiXS5pbmNsdWRlcyhwbGFjZW1lbnQpID8gMiAqICh5TWluICE9PSAwIHx8IHlNYXggIT09IDAgPyB5TWluICsgeU1heCA6IG1heChvdmVyZmxvdy50b3AsIG92ZXJmbG93LmJvdHRvbSkpIDogb3ZlcmZsb3dbaGVpZ2h0U2lkZV0pLFxuICAgICAgICB3aWR0aDogcmVjdHMuZmxvYXRpbmcud2lkdGggLSAoW1widG9wXCIsIFwiYm90dG9tXCJdLmluY2x1ZGVzKHBsYWNlbWVudCkgPyAyICogKHhNaW4gIT09IDAgfHwgeE1heCAhPT0gMCA/IHhNaW4gKyB4TWF4IDogbWF4KG92ZXJmbG93LmxlZnQsIG92ZXJmbG93LnJpZ2h0KSkgOiBvdmVyZmxvd1t3aWR0aFNpZGVdKVxuICAgICAgfTtcbiAgICAgIGNvbnN0IHByZXZEaW1lbnNpb25zID0gYXdhaXQgcGxhdGZvcm0yLmdldERpbWVuc2lvbnMoZWxlbWVudHMuZmxvYXRpbmcpO1xuICAgICAgYXBwbHkgPT0gbnVsbCA/IHZvaWQgMCA6IGFwcGx5KHtcbiAgICAgICAgLi4uZGltZW5zaW9ucyxcbiAgICAgICAgLi4ucmVjdHNcbiAgICAgIH0pO1xuICAgICAgY29uc3QgbmV4dERpbWVuc2lvbnMgPSBhd2FpdCBwbGF0Zm9ybTIuZ2V0RGltZW5zaW9ucyhlbGVtZW50cy5mbG9hdGluZyk7XG4gICAgICBpZiAocHJldkRpbWVuc2lvbnMud2lkdGggIT09IG5leHREaW1lbnNpb25zLndpZHRoIHx8IHByZXZEaW1lbnNpb25zLmhlaWdodCAhPT0gbmV4dERpbWVuc2lvbnMuaGVpZ2h0KSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgcmVzZXQ6IHtcbiAgICAgICAgICAgIHJlY3RzOiB0cnVlXG4gICAgICAgICAgfVxuICAgICAgICB9O1xuICAgICAgfVxuICAgICAgcmV0dXJuIHt9O1xuICAgIH1cbiAgfTtcbn07XG52YXIgaW5saW5lID0gZnVuY3Rpb24ob3B0aW9ucykge1xuICBpZiAob3B0aW9ucyA9PT0gdm9pZCAwKSB7XG4gICAgb3B0aW9ucyA9IHt9O1xuICB9XG4gIHJldHVybiB7XG4gICAgbmFtZTogXCJpbmxpbmVcIixcbiAgICBvcHRpb25zLFxuICAgIGFzeW5jIGZuKG1pZGRsZXdhcmVBcmd1bWVudHMpIHtcbiAgICAgIHZhciBfYXdhaXQkcGxhdGZvcm0kZ2V0Q2w7XG4gICAgICBjb25zdCB7XG4gICAgICAgIHBsYWNlbWVudCxcbiAgICAgICAgZWxlbWVudHMsXG4gICAgICAgIHJlY3RzLFxuICAgICAgICBwbGF0Zm9ybTogcGxhdGZvcm0yLFxuICAgICAgICBzdHJhdGVneVxuICAgICAgfSA9IG1pZGRsZXdhcmVBcmd1bWVudHM7XG4gICAgICBjb25zdCB7XG4gICAgICAgIHBhZGRpbmcgPSAyLFxuICAgICAgICB4LFxuICAgICAgICB5XG4gICAgICB9ID0gb3B0aW9ucztcbiAgICAgIGNvbnN0IGZhbGxiYWNrID0gcmVjdFRvQ2xpZW50UmVjdChwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3QgPyBhd2FpdCBwbGF0Zm9ybTIuY29udmVydE9mZnNldFBhcmVudFJlbGF0aXZlUmVjdFRvVmlld3BvcnRSZWxhdGl2ZVJlY3Qoe1xuICAgICAgICByZWN0OiByZWN0cy5yZWZlcmVuY2UsXG4gICAgICAgIG9mZnNldFBhcmVudDogYXdhaXQgKHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5nZXRPZmZzZXRQYXJlbnQoZWxlbWVudHMuZmxvYXRpbmcpKSxcbiAgICAgICAgc3RyYXRlZ3lcbiAgICAgIH0pIDogcmVjdHMucmVmZXJlbmNlKTtcbiAgICAgIGNvbnN0IGNsaWVudFJlY3RzID0gKF9hd2FpdCRwbGF0Zm9ybSRnZXRDbCA9IGF3YWl0IChwbGF0Zm9ybTIuZ2V0Q2xpZW50UmVjdHMgPT0gbnVsbCA/IHZvaWQgMCA6IHBsYXRmb3JtMi5nZXRDbGllbnRSZWN0cyhlbGVtZW50cy5yZWZlcmVuY2UpKSkgIT0gbnVsbCA/IF9hd2FpdCRwbGF0Zm9ybSRnZXRDbCA6IFtdO1xuICAgICAgY29uc3QgcGFkZGluZ09iamVjdCA9IGdldFNpZGVPYmplY3RGcm9tUGFkZGluZyhwYWRkaW5nKTtcbiAgICAgIGZ1bmN0aW9uIGdldEJvdW5kaW5nQ2xpZW50UmVjdDIoKSB7XG4gICAgICAgIGlmIChjbGllbnRSZWN0cy5sZW5ndGggPT09IDIgJiYgY2xpZW50UmVjdHNbMF0ubGVmdCA+IGNsaWVudFJlY3RzWzFdLnJpZ2h0ICYmIHggIT0gbnVsbCAmJiB5ICE9IG51bGwpIHtcbiAgICAgICAgICB2YXIgX2NsaWVudFJlY3RzJGZpbmQ7XG4gICAgICAgICAgcmV0dXJuIChfY2xpZW50UmVjdHMkZmluZCA9IGNsaWVudFJlY3RzLmZpbmQoKHJlY3QpID0+IHggPiByZWN0LmxlZnQgLSBwYWRkaW5nT2JqZWN0LmxlZnQgJiYgeCA8IHJlY3QucmlnaHQgKyBwYWRkaW5nT2JqZWN0LnJpZ2h0ICYmIHkgPiByZWN0LnRvcCAtIHBhZGRpbmdPYmplY3QudG9wICYmIHkgPCByZWN0LmJvdHRvbSArIHBhZGRpbmdPYmplY3QuYm90dG9tKSkgIT0gbnVsbCA/IF9jbGllbnRSZWN0cyRmaW5kIDogZmFsbGJhY2s7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGNsaWVudFJlY3RzLmxlbmd0aCA+PSAyKSB7XG4gICAgICAgICAgaWYgKGdldE1haW5BeGlzRnJvbVBsYWNlbWVudChwbGFjZW1lbnQpID09PSBcInhcIikge1xuICAgICAgICAgICAgY29uc3QgZmlyc3RSZWN0ID0gY2xpZW50UmVjdHNbMF07XG4gICAgICAgICAgICBjb25zdCBsYXN0UmVjdCA9IGNsaWVudFJlY3RzW2NsaWVudFJlY3RzLmxlbmd0aCAtIDFdO1xuICAgICAgICAgICAgY29uc3QgaXNUb3AgPSBnZXRTaWRlKHBsYWNlbWVudCkgPT09IFwidG9wXCI7XG4gICAgICAgICAgICBjb25zdCB0b3AyID0gZmlyc3RSZWN0LnRvcDtcbiAgICAgICAgICAgIGNvbnN0IGJvdHRvbTIgPSBsYXN0UmVjdC5ib3R0b207XG4gICAgICAgICAgICBjb25zdCBsZWZ0MiA9IGlzVG9wID8gZmlyc3RSZWN0LmxlZnQgOiBsYXN0UmVjdC5sZWZ0O1xuICAgICAgICAgICAgY29uc3QgcmlnaHQyID0gaXNUb3AgPyBmaXJzdFJlY3QucmlnaHQgOiBsYXN0UmVjdC5yaWdodDtcbiAgICAgICAgICAgIGNvbnN0IHdpZHRoMiA9IHJpZ2h0MiAtIGxlZnQyO1xuICAgICAgICAgICAgY29uc3QgaGVpZ2h0MiA9IGJvdHRvbTIgLSB0b3AyO1xuICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgdG9wOiB0b3AyLFxuICAgICAgICAgICAgICBib3R0b206IGJvdHRvbTIsXG4gICAgICAgICAgICAgIGxlZnQ6IGxlZnQyLFxuICAgICAgICAgICAgICByaWdodDogcmlnaHQyLFxuICAgICAgICAgICAgICB3aWR0aDogd2lkdGgyLFxuICAgICAgICAgICAgICBoZWlnaHQ6IGhlaWdodDIsXG4gICAgICAgICAgICAgIHg6IGxlZnQyLFxuICAgICAgICAgICAgICB5OiB0b3AyXG4gICAgICAgICAgICB9O1xuICAgICAgICAgIH1cbiAgICAgICAgICBjb25zdCBpc0xlZnRTaWRlID0gZ2V0U2lkZShwbGFjZW1lbnQpID09PSBcImxlZnRcIjtcbiAgICAgICAgICBjb25zdCBtYXhSaWdodCA9IG1heCguLi5jbGllbnRSZWN0cy5tYXAoKHJlY3QpID0+IHJlY3QucmlnaHQpKTtcbiAgICAgICAgICBjb25zdCBtaW5MZWZ0ID0gbWluKC4uLmNsaWVudFJlY3RzLm1hcCgocmVjdCkgPT4gcmVjdC5sZWZ0KSk7XG4gICAgICAgICAgY29uc3QgbWVhc3VyZVJlY3RzID0gY2xpZW50UmVjdHMuZmlsdGVyKChyZWN0KSA9PiBpc0xlZnRTaWRlID8gcmVjdC5sZWZ0ID09PSBtaW5MZWZ0IDogcmVjdC5yaWdodCA9PT0gbWF4UmlnaHQpO1xuICAgICAgICAgIGNvbnN0IHRvcCA9IG1lYXN1cmVSZWN0c1swXS50b3A7XG4gICAgICAgICAgY29uc3QgYm90dG9tID0gbWVhc3VyZVJlY3RzW21lYXN1cmVSZWN0cy5sZW5ndGggLSAxXS5ib3R0b207XG4gICAgICAgICAgY29uc3QgbGVmdCA9IG1pbkxlZnQ7XG4gICAgICAgICAgY29uc3QgcmlnaHQgPSBtYXhSaWdodDtcbiAgICAgICAgICBjb25zdCB3aWR0aCA9IHJpZ2h0IC0gbGVmdDtcbiAgICAgICAgICBjb25zdCBoZWlnaHQgPSBib3R0b20gLSB0b3A7XG4gICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIHRvcCxcbiAgICAgICAgICAgIGJvdHRvbSxcbiAgICAgICAgICAgIGxlZnQsXG4gICAgICAgICAgICByaWdodCxcbiAgICAgICAgICAgIHdpZHRoLFxuICAgICAgICAgICAgaGVpZ2h0LFxuICAgICAgICAgICAgeDogbGVmdCxcbiAgICAgICAgICAgIHk6IHRvcFxuICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIGZhbGxiYWNrO1xuICAgICAgfVxuICAgICAgY29uc3QgcmVzZXRSZWN0cyA9IGF3YWl0IHBsYXRmb3JtMi5nZXRFbGVtZW50UmVjdHMoe1xuICAgICAgICByZWZlcmVuY2U6IHtcbiAgICAgICAgICBnZXRCb3VuZGluZ0NsaWVudFJlY3Q6IGdldEJvdW5kaW5nQ2xpZW50UmVjdDJcbiAgICAgICAgfSxcbiAgICAgICAgZmxvYXRpbmc6IGVsZW1lbnRzLmZsb2F0aW5nLFxuICAgICAgICBzdHJhdGVneVxuICAgICAgfSk7XG4gICAgICBpZiAocmVjdHMucmVmZXJlbmNlLnggIT09IHJlc2V0UmVjdHMucmVmZXJlbmNlLnggfHwgcmVjdHMucmVmZXJlbmNlLnkgIT09IHJlc2V0UmVjdHMucmVmZXJlbmNlLnkgfHwgcmVjdHMucmVmZXJlbmNlLndpZHRoICE9PSByZXNldFJlY3RzLnJlZmVyZW5jZS53aWR0aCB8fCByZWN0cy5yZWZlcmVuY2UuaGVpZ2h0ICE9PSByZXNldFJlY3RzLnJlZmVyZW5jZS5oZWlnaHQpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICByZXNldDoge1xuICAgICAgICAgICAgcmVjdHM6IHJlc2V0UmVjdHNcbiAgICAgICAgICB9XG4gICAgICAgIH07XG4gICAgICB9XG4gICAgICByZXR1cm4ge307XG4gICAgfVxuICB9O1xufTtcblxuLy8gbm9kZV9tb2R1bGVzL0BmbG9hdGluZy11aS9kb20vZGlzdC9mbG9hdGluZy11aS5kb20uZXNtLmpzXG5mdW5jdGlvbiBpc1dpbmRvdyh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgJiYgdmFsdWUuZG9jdW1lbnQgJiYgdmFsdWUubG9jYXRpb24gJiYgdmFsdWUuYWxlcnQgJiYgdmFsdWUuc2V0SW50ZXJ2YWw7XG59XG5mdW5jdGlvbiBnZXRXaW5kb3cobm9kZSkge1xuICBpZiAobm9kZSA9PSBudWxsKSB7XG4gICAgcmV0dXJuIHdpbmRvdztcbiAgfVxuICBpZiAoIWlzV2luZG93KG5vZGUpKSB7XG4gICAgY29uc3Qgb3duZXJEb2N1bWVudCA9IG5vZGUub3duZXJEb2N1bWVudDtcbiAgICByZXR1cm4gb3duZXJEb2N1bWVudCA/IG93bmVyRG9jdW1lbnQuZGVmYXVsdFZpZXcgfHwgd2luZG93IDogd2luZG93O1xuICB9XG4gIHJldHVybiBub2RlO1xufVxuZnVuY3Rpb24gZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpIHtcbiAgcmV0dXJuIGdldFdpbmRvdyhlbGVtZW50KS5nZXRDb21wdXRlZFN0eWxlKGVsZW1lbnQpO1xufVxuZnVuY3Rpb24gZ2V0Tm9kZU5hbWUobm9kZSkge1xuICByZXR1cm4gaXNXaW5kb3cobm9kZSkgPyBcIlwiIDogbm9kZSA/IChub2RlLm5vZGVOYW1lIHx8IFwiXCIpLnRvTG93ZXJDYXNlKCkgOiBcIlwiO1xufVxuZnVuY3Rpb24gaXNIVE1MRWxlbWVudCh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgaW5zdGFuY2VvZiBnZXRXaW5kb3codmFsdWUpLkhUTUxFbGVtZW50O1xufVxuZnVuY3Rpb24gaXNFbGVtZW50KHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSBpbnN0YW5jZW9mIGdldFdpbmRvdyh2YWx1ZSkuRWxlbWVudDtcbn1cbmZ1bmN0aW9uIGlzTm9kZSh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgaW5zdGFuY2VvZiBnZXRXaW5kb3codmFsdWUpLk5vZGU7XG59XG5mdW5jdGlvbiBpc1NoYWRvd1Jvb3Qobm9kZSkge1xuICBpZiAodHlwZW9mIFNoYWRvd1Jvb3QgPT09IFwidW5kZWZpbmVkXCIpIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgY29uc3QgT3duRWxlbWVudCA9IGdldFdpbmRvdyhub2RlKS5TaGFkb3dSb290O1xuICByZXR1cm4gbm9kZSBpbnN0YW5jZW9mIE93bkVsZW1lbnQgfHwgbm9kZSBpbnN0YW5jZW9mIFNoYWRvd1Jvb3Q7XG59XG5mdW5jdGlvbiBpc092ZXJmbG93RWxlbWVudChlbGVtZW50KSB7XG4gIGNvbnN0IHtcbiAgICBvdmVyZmxvdyxcbiAgICBvdmVyZmxvd1gsXG4gICAgb3ZlcmZsb3dZXG4gIH0gPSBnZXRDb21wdXRlZFN0eWxlJDEoZWxlbWVudCk7XG4gIHJldHVybiAvYXV0b3xzY3JvbGx8b3ZlcmxheXxoaWRkZW4vLnRlc3Qob3ZlcmZsb3cgKyBvdmVyZmxvd1kgKyBvdmVyZmxvd1gpO1xufVxuZnVuY3Rpb24gaXNUYWJsZUVsZW1lbnQoZWxlbWVudCkge1xuICByZXR1cm4gW1widGFibGVcIiwgXCJ0ZFwiLCBcInRoXCJdLmluY2x1ZGVzKGdldE5vZGVOYW1lKGVsZW1lbnQpKTtcbn1cbmZ1bmN0aW9uIGlzQ29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHtcbiAgY29uc3QgaXNGaXJlZm94ID0gbmF2aWdhdG9yLnVzZXJBZ2VudC50b0xvd2VyQ2FzZSgpLmluY2x1ZGVzKFwiZmlyZWZveFwiKTtcbiAgY29uc3QgY3NzID0gZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpO1xuICByZXR1cm4gY3NzLnRyYW5zZm9ybSAhPT0gXCJub25lXCIgfHwgY3NzLnBlcnNwZWN0aXZlICE9PSBcIm5vbmVcIiB8fCBjc3MuY29udGFpbiA9PT0gXCJwYWludFwiIHx8IFtcInRyYW5zZm9ybVwiLCBcInBlcnNwZWN0aXZlXCJdLmluY2x1ZGVzKGNzcy53aWxsQ2hhbmdlKSB8fCBpc0ZpcmVmb3ggJiYgY3NzLndpbGxDaGFuZ2UgPT09IFwiZmlsdGVyXCIgfHwgaXNGaXJlZm94ICYmIChjc3MuZmlsdGVyID8gY3NzLmZpbHRlciAhPT0gXCJub25lXCIgOiBmYWxzZSk7XG59XG5mdW5jdGlvbiBpc0xheW91dFZpZXdwb3J0KCkge1xuICByZXR1cm4gIS9eKCg/IWNocm9tZXxhbmRyb2lkKS4pKnNhZmFyaS9pLnRlc3QobmF2aWdhdG9yLnVzZXJBZ2VudCk7XG59XG52YXIgbWluMiA9IE1hdGgubWluO1xudmFyIG1heDIgPSBNYXRoLm1heDtcbnZhciByb3VuZCA9IE1hdGgucm91bmQ7XG5mdW5jdGlvbiBnZXRCb3VuZGluZ0NsaWVudFJlY3QoZWxlbWVudCwgaW5jbHVkZVNjYWxlLCBpc0ZpeGVkU3RyYXRlZ3kpIHtcbiAgdmFyIF93aW4kdmlzdWFsVmlld3BvcnQkbywgX3dpbiR2aXN1YWxWaWV3cG9ydCwgX3dpbiR2aXN1YWxWaWV3cG9ydCRvMiwgX3dpbiR2aXN1YWxWaWV3cG9ydDI7XG4gIGlmIChpbmNsdWRlU2NhbGUgPT09IHZvaWQgMCkge1xuICAgIGluY2x1ZGVTY2FsZSA9IGZhbHNlO1xuICB9XG4gIGlmIChpc0ZpeGVkU3RyYXRlZ3kgPT09IHZvaWQgMCkge1xuICAgIGlzRml4ZWRTdHJhdGVneSA9IGZhbHNlO1xuICB9XG4gIGNvbnN0IGNsaWVudFJlY3QgPSBlbGVtZW50LmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpO1xuICBsZXQgc2NhbGVYID0gMTtcbiAgbGV0IHNjYWxlWSA9IDE7XG4gIGlmIChpbmNsdWRlU2NhbGUgJiYgaXNIVE1MRWxlbWVudChlbGVtZW50KSkge1xuICAgIHNjYWxlWCA9IGVsZW1lbnQub2Zmc2V0V2lkdGggPiAwID8gcm91bmQoY2xpZW50UmVjdC53aWR0aCkgLyBlbGVtZW50Lm9mZnNldFdpZHRoIHx8IDEgOiAxO1xuICAgIHNjYWxlWSA9IGVsZW1lbnQub2Zmc2V0SGVpZ2h0ID4gMCA/IHJvdW5kKGNsaWVudFJlY3QuaGVpZ2h0KSAvIGVsZW1lbnQub2Zmc2V0SGVpZ2h0IHx8IDEgOiAxO1xuICB9XG4gIGNvbnN0IHdpbiA9IGlzRWxlbWVudChlbGVtZW50KSA/IGdldFdpbmRvdyhlbGVtZW50KSA6IHdpbmRvdztcbiAgY29uc3QgYWRkVmlzdWFsT2Zmc2V0cyA9ICFpc0xheW91dFZpZXdwb3J0KCkgJiYgaXNGaXhlZFN0cmF0ZWd5O1xuICBjb25zdCB4ID0gKGNsaWVudFJlY3QubGVmdCArIChhZGRWaXN1YWxPZmZzZXRzID8gKF93aW4kdmlzdWFsVmlld3BvcnQkbyA9IChfd2luJHZpc3VhbFZpZXdwb3J0ID0gd2luLnZpc3VhbFZpZXdwb3J0KSA9PSBudWxsID8gdm9pZCAwIDogX3dpbiR2aXN1YWxWaWV3cG9ydC5vZmZzZXRMZWZ0KSAhPSBudWxsID8gX3dpbiR2aXN1YWxWaWV3cG9ydCRvIDogMCA6IDApKSAvIHNjYWxlWDtcbiAgY29uc3QgeSA9IChjbGllbnRSZWN0LnRvcCArIChhZGRWaXN1YWxPZmZzZXRzID8gKF93aW4kdmlzdWFsVmlld3BvcnQkbzIgPSAoX3dpbiR2aXN1YWxWaWV3cG9ydDIgPSB3aW4udmlzdWFsVmlld3BvcnQpID09IG51bGwgPyB2b2lkIDAgOiBfd2luJHZpc3VhbFZpZXdwb3J0Mi5vZmZzZXRUb3ApICE9IG51bGwgPyBfd2luJHZpc3VhbFZpZXdwb3J0JG8yIDogMCA6IDApKSAvIHNjYWxlWTtcbiAgY29uc3Qgd2lkdGggPSBjbGllbnRSZWN0LndpZHRoIC8gc2NhbGVYO1xuICBjb25zdCBoZWlnaHQgPSBjbGllbnRSZWN0LmhlaWdodCAvIHNjYWxlWTtcbiAgcmV0dXJuIHtcbiAgICB3aWR0aCxcbiAgICBoZWlnaHQsXG4gICAgdG9wOiB5LFxuICAgIHJpZ2h0OiB4ICsgd2lkdGgsXG4gICAgYm90dG9tOiB5ICsgaGVpZ2h0LFxuICAgIGxlZnQ6IHgsXG4gICAgeCxcbiAgICB5XG4gIH07XG59XG5mdW5jdGlvbiBnZXREb2N1bWVudEVsZW1lbnQobm9kZSkge1xuICByZXR1cm4gKChpc05vZGUobm9kZSkgPyBub2RlLm93bmVyRG9jdW1lbnQgOiBub2RlLmRvY3VtZW50KSB8fCB3aW5kb3cuZG9jdW1lbnQpLmRvY3VtZW50RWxlbWVudDtcbn1cbmZ1bmN0aW9uIGdldE5vZGVTY3JvbGwoZWxlbWVudCkge1xuICBpZiAoaXNFbGVtZW50KGVsZW1lbnQpKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIHNjcm9sbExlZnQ6IGVsZW1lbnQuc2Nyb2xsTGVmdCxcbiAgICAgIHNjcm9sbFRvcDogZWxlbWVudC5zY3JvbGxUb3BcbiAgICB9O1xuICB9XG4gIHJldHVybiB7XG4gICAgc2Nyb2xsTGVmdDogZWxlbWVudC5wYWdlWE9mZnNldCxcbiAgICBzY3JvbGxUb3A6IGVsZW1lbnQucGFnZVlPZmZzZXRcbiAgfTtcbn1cbmZ1bmN0aW9uIGdldFdpbmRvd1Njcm9sbEJhclgoZWxlbWVudCkge1xuICByZXR1cm4gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGdldERvY3VtZW50RWxlbWVudChlbGVtZW50KSkubGVmdCArIGdldE5vZGVTY3JvbGwoZWxlbWVudCkuc2Nyb2xsTGVmdDtcbn1cbmZ1bmN0aW9uIGlzU2NhbGVkKGVsZW1lbnQpIHtcbiAgY29uc3QgcmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50KTtcbiAgcmV0dXJuIHJvdW5kKHJlY3Qud2lkdGgpICE9PSBlbGVtZW50Lm9mZnNldFdpZHRoIHx8IHJvdW5kKHJlY3QuaGVpZ2h0KSAhPT0gZWxlbWVudC5vZmZzZXRIZWlnaHQ7XG59XG5mdW5jdGlvbiBnZXRSZWN0UmVsYXRpdmVUb09mZnNldFBhcmVudChlbGVtZW50LCBvZmZzZXRQYXJlbnQsIHN0cmF0ZWd5KSB7XG4gIGNvbnN0IGlzT2Zmc2V0UGFyZW50QW5FbGVtZW50ID0gaXNIVE1MRWxlbWVudChvZmZzZXRQYXJlbnQpO1xuICBjb25zdCBkb2N1bWVudEVsZW1lbnQgPSBnZXREb2N1bWVudEVsZW1lbnQob2Zmc2V0UGFyZW50KTtcbiAgY29uc3QgcmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChcbiAgICBlbGVtZW50LFxuICAgIGlzT2Zmc2V0UGFyZW50QW5FbGVtZW50ICYmIGlzU2NhbGVkKG9mZnNldFBhcmVudCksXG4gICAgc3RyYXRlZ3kgPT09IFwiZml4ZWRcIlxuICApO1xuICBsZXQgc2Nyb2xsID0ge1xuICAgIHNjcm9sbExlZnQ6IDAsXG4gICAgc2Nyb2xsVG9wOiAwXG4gIH07XG4gIGNvbnN0IG9mZnNldHMgPSB7XG4gICAgeDogMCxcbiAgICB5OiAwXG4gIH07XG4gIGlmIChpc09mZnNldFBhcmVudEFuRWxlbWVudCB8fCAhaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgJiYgc3RyYXRlZ3kgIT09IFwiZml4ZWRcIikge1xuICAgIGlmIChnZXROb2RlTmFtZShvZmZzZXRQYXJlbnQpICE9PSBcImJvZHlcIiB8fCBpc092ZXJmbG93RWxlbWVudChkb2N1bWVudEVsZW1lbnQpKSB7XG4gICAgICBzY3JvbGwgPSBnZXROb2RlU2Nyb2xsKG9mZnNldFBhcmVudCk7XG4gICAgfVxuICAgIGlmIChpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCkpIHtcbiAgICAgIGNvbnN0IG9mZnNldFJlY3QgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3Qob2Zmc2V0UGFyZW50LCB0cnVlKTtcbiAgICAgIG9mZnNldHMueCA9IG9mZnNldFJlY3QueCArIG9mZnNldFBhcmVudC5jbGllbnRMZWZ0O1xuICAgICAgb2Zmc2V0cy55ID0gb2Zmc2V0UmVjdC55ICsgb2Zmc2V0UGFyZW50LmNsaWVudFRvcDtcbiAgICB9IGVsc2UgaWYgKGRvY3VtZW50RWxlbWVudCkge1xuICAgICAgb2Zmc2V0cy54ID0gZ2V0V2luZG93U2Nyb2xsQmFyWChkb2N1bWVudEVsZW1lbnQpO1xuICAgIH1cbiAgfVxuICByZXR1cm4ge1xuICAgIHg6IHJlY3QubGVmdCArIHNjcm9sbC5zY3JvbGxMZWZ0IC0gb2Zmc2V0cy54LFxuICAgIHk6IHJlY3QudG9wICsgc2Nyb2xsLnNjcm9sbFRvcCAtIG9mZnNldHMueSxcbiAgICB3aWR0aDogcmVjdC53aWR0aCxcbiAgICBoZWlnaHQ6IHJlY3QuaGVpZ2h0XG4gIH07XG59XG5mdW5jdGlvbiBnZXRQYXJlbnROb2RlKG5vZGUpIHtcbiAgaWYgKGdldE5vZGVOYW1lKG5vZGUpID09PSBcImh0bWxcIikge1xuICAgIHJldHVybiBub2RlO1xuICB9XG4gIHJldHVybiBub2RlLmFzc2lnbmVkU2xvdCB8fCBub2RlLnBhcmVudE5vZGUgfHwgKGlzU2hhZG93Um9vdChub2RlKSA/IG5vZGUuaG9zdCA6IG51bGwpIHx8IGdldERvY3VtZW50RWxlbWVudChub2RlKTtcbn1cbmZ1bmN0aW9uIGdldFRydWVPZmZzZXRQYXJlbnQoZWxlbWVudCkge1xuICBpZiAoIWlzSFRNTEVsZW1lbnQoZWxlbWVudCkgfHwgZ2V0Q29tcHV0ZWRTdHlsZShlbGVtZW50KS5wb3NpdGlvbiA9PT0gXCJmaXhlZFwiKSB7XG4gICAgcmV0dXJuIG51bGw7XG4gIH1cbiAgcmV0dXJuIGVsZW1lbnQub2Zmc2V0UGFyZW50O1xufVxuZnVuY3Rpb24gZ2V0Q29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHtcbiAgbGV0IGN1cnJlbnROb2RlID0gZ2V0UGFyZW50Tm9kZShlbGVtZW50KTtcbiAgaWYgKGlzU2hhZG93Um9vdChjdXJyZW50Tm9kZSkpIHtcbiAgICBjdXJyZW50Tm9kZSA9IGN1cnJlbnROb2RlLmhvc3Q7XG4gIH1cbiAgd2hpbGUgKGlzSFRNTEVsZW1lbnQoY3VycmVudE5vZGUpICYmICFbXCJodG1sXCIsIFwiYm9keVwiXS5pbmNsdWRlcyhnZXROb2RlTmFtZShjdXJyZW50Tm9kZSkpKSB7XG4gICAgaWYgKGlzQ29udGFpbmluZ0Jsb2NrKGN1cnJlbnROb2RlKSkge1xuICAgICAgcmV0dXJuIGN1cnJlbnROb2RlO1xuICAgIH0gZWxzZSB7XG4gICAgICBjdXJyZW50Tm9kZSA9IGN1cnJlbnROb2RlLnBhcmVudE5vZGU7XG4gICAgfVxuICB9XG4gIHJldHVybiBudWxsO1xufVxuZnVuY3Rpb24gZ2V0T2Zmc2V0UGFyZW50KGVsZW1lbnQpIHtcbiAgY29uc3Qgd2luZG93MiA9IGdldFdpbmRvdyhlbGVtZW50KTtcbiAgbGV0IG9mZnNldFBhcmVudCA9IGdldFRydWVPZmZzZXRQYXJlbnQoZWxlbWVudCk7XG4gIHdoaWxlIChvZmZzZXRQYXJlbnQgJiYgaXNUYWJsZUVsZW1lbnQob2Zmc2V0UGFyZW50KSAmJiBnZXRDb21wdXRlZFN0eWxlKG9mZnNldFBhcmVudCkucG9zaXRpb24gPT09IFwic3RhdGljXCIpIHtcbiAgICBvZmZzZXRQYXJlbnQgPSBnZXRUcnVlT2Zmc2V0UGFyZW50KG9mZnNldFBhcmVudCk7XG4gIH1cbiAgaWYgKG9mZnNldFBhcmVudCAmJiAoZ2V0Tm9kZU5hbWUob2Zmc2V0UGFyZW50KSA9PT0gXCJodG1sXCIgfHwgZ2V0Tm9kZU5hbWUob2Zmc2V0UGFyZW50KSA9PT0gXCJib2R5XCIgJiYgZ2V0Q29tcHV0ZWRTdHlsZShvZmZzZXRQYXJlbnQpLnBvc2l0aW9uID09PSBcInN0YXRpY1wiICYmICFpc0NvbnRhaW5pbmdCbG9jayhvZmZzZXRQYXJlbnQpKSkge1xuICAgIHJldHVybiB3aW5kb3cyO1xuICB9XG4gIHJldHVybiBvZmZzZXRQYXJlbnQgfHwgZ2V0Q29udGFpbmluZ0Jsb2NrKGVsZW1lbnQpIHx8IHdpbmRvdzI7XG59XG5mdW5jdGlvbiBnZXREaW1lbnNpb25zKGVsZW1lbnQpIHtcbiAgaWYgKGlzSFRNTEVsZW1lbnQoZWxlbWVudCkpIHtcbiAgICByZXR1cm4ge1xuICAgICAgd2lkdGg6IGVsZW1lbnQub2Zmc2V0V2lkdGgsXG4gICAgICBoZWlnaHQ6IGVsZW1lbnQub2Zmc2V0SGVpZ2h0XG4gICAgfTtcbiAgfVxuICBjb25zdCByZWN0ID0gZ2V0Qm91bmRpbmdDbGllbnRSZWN0KGVsZW1lbnQpO1xuICByZXR1cm4ge1xuICAgIHdpZHRoOiByZWN0LndpZHRoLFxuICAgIGhlaWdodDogcmVjdC5oZWlnaHRcbiAgfTtcbn1cbmZ1bmN0aW9uIGNvbnZlcnRPZmZzZXRQYXJlbnRSZWxhdGl2ZVJlY3RUb1ZpZXdwb3J0UmVsYXRpdmVSZWN0KF9yZWYpIHtcbiAgbGV0IHtcbiAgICByZWN0LFxuICAgIG9mZnNldFBhcmVudCxcbiAgICBzdHJhdGVneVxuICB9ID0gX3JlZjtcbiAgY29uc3QgaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgPSBpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCk7XG4gIGNvbnN0IGRvY3VtZW50RWxlbWVudCA9IGdldERvY3VtZW50RWxlbWVudChvZmZzZXRQYXJlbnQpO1xuICBpZiAob2Zmc2V0UGFyZW50ID09PSBkb2N1bWVudEVsZW1lbnQpIHtcbiAgICByZXR1cm4gcmVjdDtcbiAgfVxuICBsZXQgc2Nyb2xsID0ge1xuICAgIHNjcm9sbExlZnQ6IDAsXG4gICAgc2Nyb2xsVG9wOiAwXG4gIH07XG4gIGNvbnN0IG9mZnNldHMgPSB7XG4gICAgeDogMCxcbiAgICB5OiAwXG4gIH07XG4gIGlmIChpc09mZnNldFBhcmVudEFuRWxlbWVudCB8fCAhaXNPZmZzZXRQYXJlbnRBbkVsZW1lbnQgJiYgc3RyYXRlZ3kgIT09IFwiZml4ZWRcIikge1xuICAgIGlmIChnZXROb2RlTmFtZShvZmZzZXRQYXJlbnQpICE9PSBcImJvZHlcIiB8fCBpc092ZXJmbG93RWxlbWVudChkb2N1bWVudEVsZW1lbnQpKSB7XG4gICAgICBzY3JvbGwgPSBnZXROb2RlU2Nyb2xsKG9mZnNldFBhcmVudCk7XG4gICAgfVxuICAgIGlmIChpc0hUTUxFbGVtZW50KG9mZnNldFBhcmVudCkpIHtcbiAgICAgIGNvbnN0IG9mZnNldFJlY3QgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3Qob2Zmc2V0UGFyZW50LCB0cnVlKTtcbiAgICAgIG9mZnNldHMueCA9IG9mZnNldFJlY3QueCArIG9mZnNldFBhcmVudC5jbGllbnRMZWZ0O1xuICAgICAgb2Zmc2V0cy55ID0gb2Zmc2V0UmVjdC55ICsgb2Zmc2V0UGFyZW50LmNsaWVudFRvcDtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHtcbiAgICAuLi5yZWN0LFxuICAgIHg6IHJlY3QueCAtIHNjcm9sbC5zY3JvbGxMZWZ0ICsgb2Zmc2V0cy54LFxuICAgIHk6IHJlY3QueSAtIHNjcm9sbC5zY3JvbGxUb3AgKyBvZmZzZXRzLnlcbiAgfTtcbn1cbmZ1bmN0aW9uIGdldFZpZXdwb3J0UmVjdChlbGVtZW50LCBzdHJhdGVneSkge1xuICBjb25zdCB3aW4gPSBnZXRXaW5kb3coZWxlbWVudCk7XG4gIGNvbnN0IGh0bWwgPSBnZXREb2N1bWVudEVsZW1lbnQoZWxlbWVudCk7XG4gIGNvbnN0IHZpc3VhbFZpZXdwb3J0ID0gd2luLnZpc3VhbFZpZXdwb3J0O1xuICBsZXQgd2lkdGggPSBodG1sLmNsaWVudFdpZHRoO1xuICBsZXQgaGVpZ2h0ID0gaHRtbC5jbGllbnRIZWlnaHQ7XG4gIGxldCB4ID0gMDtcbiAgbGV0IHkgPSAwO1xuICBpZiAodmlzdWFsVmlld3BvcnQpIHtcbiAgICB3aWR0aCA9IHZpc3VhbFZpZXdwb3J0LndpZHRoO1xuICAgIGhlaWdodCA9IHZpc3VhbFZpZXdwb3J0LmhlaWdodDtcbiAgICBjb25zdCBsYXlvdXRWaWV3cG9ydCA9IGlzTGF5b3V0Vmlld3BvcnQoKTtcbiAgICBpZiAobGF5b3V0Vmlld3BvcnQgfHwgIWxheW91dFZpZXdwb3J0ICYmIHN0cmF0ZWd5ID09PSBcImZpeGVkXCIpIHtcbiAgICAgIHggPSB2aXN1YWxWaWV3cG9ydC5vZmZzZXRMZWZ0O1xuICAgICAgeSA9IHZpc3VhbFZpZXdwb3J0Lm9mZnNldFRvcDtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHtcbiAgICB3aWR0aCxcbiAgICBoZWlnaHQsXG4gICAgeCxcbiAgICB5XG4gIH07XG59XG5mdW5jdGlvbiBnZXREb2N1bWVudFJlY3QoZWxlbWVudCkge1xuICB2YXIgX2VsZW1lbnQkb3duZXJEb2N1bWVuO1xuICBjb25zdCBodG1sID0gZ2V0RG9jdW1lbnRFbGVtZW50KGVsZW1lbnQpO1xuICBjb25zdCBzY3JvbGwgPSBnZXROb2RlU2Nyb2xsKGVsZW1lbnQpO1xuICBjb25zdCBib2R5ID0gKF9lbGVtZW50JG93bmVyRG9jdW1lbiA9IGVsZW1lbnQub3duZXJEb2N1bWVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9lbGVtZW50JG93bmVyRG9jdW1lbi5ib2R5O1xuICBjb25zdCB3aWR0aCA9IG1heDIoaHRtbC5zY3JvbGxXaWR0aCwgaHRtbC5jbGllbnRXaWR0aCwgYm9keSA/IGJvZHkuc2Nyb2xsV2lkdGggOiAwLCBib2R5ID8gYm9keS5jbGllbnRXaWR0aCA6IDApO1xuICBjb25zdCBoZWlnaHQgPSBtYXgyKGh0bWwuc2Nyb2xsSGVpZ2h0LCBodG1sLmNsaWVudEhlaWdodCwgYm9keSA/IGJvZHkuc2Nyb2xsSGVpZ2h0IDogMCwgYm9keSA/IGJvZHkuY2xpZW50SGVpZ2h0IDogMCk7XG4gIGxldCB4ID0gLXNjcm9sbC5zY3JvbGxMZWZ0ICsgZ2V0V2luZG93U2Nyb2xsQmFyWChlbGVtZW50KTtcbiAgY29uc3QgeSA9IC1zY3JvbGwuc2Nyb2xsVG9wO1xuICBpZiAoZ2V0Q29tcHV0ZWRTdHlsZSQxKGJvZHkgfHwgaHRtbCkuZGlyZWN0aW9uID09PSBcInJ0bFwiKSB7XG4gICAgeCArPSBtYXgyKGh0bWwuY2xpZW50V2lkdGgsIGJvZHkgPyBib2R5LmNsaWVudFdpZHRoIDogMCkgLSB3aWR0aDtcbiAgfVxuICByZXR1cm4ge1xuICAgIHdpZHRoLFxuICAgIGhlaWdodCxcbiAgICB4LFxuICAgIHlcbiAgfTtcbn1cbmZ1bmN0aW9uIGdldE5lYXJlc3RPdmVyZmxvd0FuY2VzdG9yKG5vZGUpIHtcbiAgY29uc3QgcGFyZW50Tm9kZSA9IGdldFBhcmVudE5vZGUobm9kZSk7XG4gIGlmIChbXCJodG1sXCIsIFwiYm9keVwiLCBcIiNkb2N1bWVudFwiXS5pbmNsdWRlcyhnZXROb2RlTmFtZShwYXJlbnROb2RlKSkpIHtcbiAgICByZXR1cm4gbm9kZS5vd25lckRvY3VtZW50LmJvZHk7XG4gIH1cbiAgaWYgKGlzSFRNTEVsZW1lbnQocGFyZW50Tm9kZSkgJiYgaXNPdmVyZmxvd0VsZW1lbnQocGFyZW50Tm9kZSkpIHtcbiAgICByZXR1cm4gcGFyZW50Tm9kZTtcbiAgfVxuICByZXR1cm4gZ2V0TmVhcmVzdE92ZXJmbG93QW5jZXN0b3IocGFyZW50Tm9kZSk7XG59XG5mdW5jdGlvbiBnZXRPdmVyZmxvd0FuY2VzdG9ycyhub2RlLCBsaXN0KSB7XG4gIHZhciBfbm9kZSRvd25lckRvY3VtZW50O1xuICBpZiAobGlzdCA9PT0gdm9pZCAwKSB7XG4gICAgbGlzdCA9IFtdO1xuICB9XG4gIGNvbnN0IHNjcm9sbGFibGVBbmNlc3RvciA9IGdldE5lYXJlc3RPdmVyZmxvd0FuY2VzdG9yKG5vZGUpO1xuICBjb25zdCBpc0JvZHkgPSBzY3JvbGxhYmxlQW5jZXN0b3IgPT09ICgoX25vZGUkb3duZXJEb2N1bWVudCA9IG5vZGUub3duZXJEb2N1bWVudCkgPT0gbnVsbCA/IHZvaWQgMCA6IF9ub2RlJG93bmVyRG9jdW1lbnQuYm9keSk7XG4gIGNvbnN0IHdpbiA9IGdldFdpbmRvdyhzY3JvbGxhYmxlQW5jZXN0b3IpO1xuICBjb25zdCB0YXJnZXQgPSBpc0JvZHkgPyBbd2luXS5jb25jYXQod2luLnZpc3VhbFZpZXdwb3J0IHx8IFtdLCBpc092ZXJmbG93RWxlbWVudChzY3JvbGxhYmxlQW5jZXN0b3IpID8gc2Nyb2xsYWJsZUFuY2VzdG9yIDogW10pIDogc2Nyb2xsYWJsZUFuY2VzdG9yO1xuICBjb25zdCB1cGRhdGVkTGlzdCA9IGxpc3QuY29uY2F0KHRhcmdldCk7XG4gIHJldHVybiBpc0JvZHkgPyB1cGRhdGVkTGlzdCA6IHVwZGF0ZWRMaXN0LmNvbmNhdChnZXRPdmVyZmxvd0FuY2VzdG9ycyh0YXJnZXQpKTtcbn1cbmZ1bmN0aW9uIGNvbnRhaW5zKHBhcmVudCwgY2hpbGQpIHtcbiAgY29uc3Qgcm9vdE5vZGUgPSBjaGlsZCA9PSBudWxsID8gdm9pZCAwIDogY2hpbGQuZ2V0Um9vdE5vZGUgPT0gbnVsbCA/IHZvaWQgMCA6IGNoaWxkLmdldFJvb3ROb2RlKCk7XG4gIGlmIChwYXJlbnQgIT0gbnVsbCAmJiBwYXJlbnQuY29udGFpbnMoY2hpbGQpKSB7XG4gICAgcmV0dXJuIHRydWU7XG4gIH0gZWxzZSBpZiAocm9vdE5vZGUgJiYgaXNTaGFkb3dSb290KHJvb3ROb2RlKSkge1xuICAgIGxldCBuZXh0ID0gY2hpbGQ7XG4gICAgZG8ge1xuICAgICAgaWYgKG5leHQgJiYgcGFyZW50ID09PSBuZXh0KSB7XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfVxuICAgICAgbmV4dCA9IG5leHQucGFyZW50Tm9kZSB8fCBuZXh0Lmhvc3Q7XG4gICAgfSB3aGlsZSAobmV4dCk7XG4gIH1cbiAgcmV0dXJuIGZhbHNlO1xufVxuZnVuY3Rpb24gZ2V0SW5uZXJCb3VuZGluZ0NsaWVudFJlY3QoZWxlbWVudCwgc3RyYXRlZ3kpIHtcbiAgY29uc3QgY2xpZW50UmVjdCA9IGdldEJvdW5kaW5nQ2xpZW50UmVjdChlbGVtZW50LCBmYWxzZSwgc3RyYXRlZ3kgPT09IFwiZml4ZWRcIik7XG4gIGNvbnN0IHRvcCA9IGNsaWVudFJlY3QudG9wICsgZWxlbWVudC5jbGllbnRUb3A7XG4gIGNvbnN0IGxlZnQgPSBjbGllbnRSZWN0LmxlZnQgKyBlbGVtZW50LmNsaWVudExlZnQ7XG4gIHJldHVybiB7XG4gICAgdG9wLFxuICAgIGxlZnQsXG4gICAgeDogbGVmdCxcbiAgICB5OiB0b3AsXG4gICAgcmlnaHQ6IGxlZnQgKyBlbGVtZW50LmNsaWVudFdpZHRoLFxuICAgIGJvdHRvbTogdG9wICsgZWxlbWVudC5jbGllbnRIZWlnaHQsXG4gICAgd2lkdGg6IGVsZW1lbnQuY2xpZW50V2lkdGgsXG4gICAgaGVpZ2h0OiBlbGVtZW50LmNsaWVudEhlaWdodFxuICB9O1xufVxuZnVuY3Rpb24gZ2V0Q2xpZW50UmVjdEZyb21DbGlwcGluZ0FuY2VzdG9yKGVsZW1lbnQsIGNsaXBwaW5nUGFyZW50LCBzdHJhdGVneSkge1xuICBpZiAoY2xpcHBpbmdQYXJlbnQgPT09IFwidmlld3BvcnRcIikge1xuICAgIHJldHVybiByZWN0VG9DbGllbnRSZWN0KGdldFZpZXdwb3J0UmVjdChlbGVtZW50LCBzdHJhdGVneSkpO1xuICB9XG4gIGlmIChpc0VsZW1lbnQoY2xpcHBpbmdQYXJlbnQpKSB7XG4gICAgcmV0dXJuIGdldElubmVyQm91bmRpbmdDbGllbnRSZWN0KGNsaXBwaW5nUGFyZW50LCBzdHJhdGVneSk7XG4gIH1cbiAgcmV0dXJuIHJlY3RUb0NsaWVudFJlY3QoZ2V0RG9jdW1lbnRSZWN0KGdldERvY3VtZW50RWxlbWVudChlbGVtZW50KSkpO1xufVxuZnVuY3Rpb24gZ2V0Q2xpcHBpbmdBbmNlc3RvcnMoZWxlbWVudCkge1xuICBjb25zdCBjbGlwcGluZ0FuY2VzdG9ycyA9IGdldE92ZXJmbG93QW5jZXN0b3JzKGVsZW1lbnQpO1xuICBjb25zdCBjYW5Fc2NhcGVDbGlwcGluZyA9IFtcImFic29sdXRlXCIsIFwiZml4ZWRcIl0uaW5jbHVkZXMoZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpLnBvc2l0aW9uKTtcbiAgY29uc3QgY2xpcHBlckVsZW1lbnQgPSBjYW5Fc2NhcGVDbGlwcGluZyAmJiBpc0hUTUxFbGVtZW50KGVsZW1lbnQpID8gZ2V0T2Zmc2V0UGFyZW50KGVsZW1lbnQpIDogZWxlbWVudDtcbiAgaWYgKCFpc0VsZW1lbnQoY2xpcHBlckVsZW1lbnQpKSB7XG4gICAgcmV0dXJuIFtdO1xuICB9XG4gIHJldHVybiBjbGlwcGluZ0FuY2VzdG9ycy5maWx0ZXIoKGNsaXBwaW5nQW5jZXN0b3JzMikgPT4gaXNFbGVtZW50KGNsaXBwaW5nQW5jZXN0b3JzMikgJiYgY29udGFpbnMoY2xpcHBpbmdBbmNlc3RvcnMyLCBjbGlwcGVyRWxlbWVudCkgJiYgZ2V0Tm9kZU5hbWUoY2xpcHBpbmdBbmNlc3RvcnMyKSAhPT0gXCJib2R5XCIpO1xufVxuZnVuY3Rpb24gZ2V0Q2xpcHBpbmdSZWN0KF9yZWYpIHtcbiAgbGV0IHtcbiAgICBlbGVtZW50LFxuICAgIGJvdW5kYXJ5LFxuICAgIHJvb3RCb3VuZGFyeSxcbiAgICBzdHJhdGVneVxuICB9ID0gX3JlZjtcbiAgY29uc3QgbWFpbkNsaXBwaW5nQW5jZXN0b3JzID0gYm91bmRhcnkgPT09IFwiY2xpcHBpbmdBbmNlc3RvcnNcIiA/IGdldENsaXBwaW5nQW5jZXN0b3JzKGVsZW1lbnQpIDogW10uY29uY2F0KGJvdW5kYXJ5KTtcbiAgY29uc3QgY2xpcHBpbmdBbmNlc3RvcnMgPSBbLi4ubWFpbkNsaXBwaW5nQW5jZXN0b3JzLCByb290Qm91bmRhcnldO1xuICBjb25zdCBmaXJzdENsaXBwaW5nQW5jZXN0b3IgPSBjbGlwcGluZ0FuY2VzdG9yc1swXTtcbiAgY29uc3QgY2xpcHBpbmdSZWN0ID0gY2xpcHBpbmdBbmNlc3RvcnMucmVkdWNlKChhY2NSZWN0LCBjbGlwcGluZ0FuY2VzdG9yKSA9PiB7XG4gICAgY29uc3QgcmVjdCA9IGdldENsaWVudFJlY3RGcm9tQ2xpcHBpbmdBbmNlc3RvcihlbGVtZW50LCBjbGlwcGluZ0FuY2VzdG9yLCBzdHJhdGVneSk7XG4gICAgYWNjUmVjdC50b3AgPSBtYXgyKHJlY3QudG9wLCBhY2NSZWN0LnRvcCk7XG4gICAgYWNjUmVjdC5yaWdodCA9IG1pbjIocmVjdC5yaWdodCwgYWNjUmVjdC5yaWdodCk7XG4gICAgYWNjUmVjdC5ib3R0b20gPSBtaW4yKHJlY3QuYm90dG9tLCBhY2NSZWN0LmJvdHRvbSk7XG4gICAgYWNjUmVjdC5sZWZ0ID0gbWF4MihyZWN0LmxlZnQsIGFjY1JlY3QubGVmdCk7XG4gICAgcmV0dXJuIGFjY1JlY3Q7XG4gIH0sIGdldENsaWVudFJlY3RGcm9tQ2xpcHBpbmdBbmNlc3RvcihlbGVtZW50LCBmaXJzdENsaXBwaW5nQW5jZXN0b3IsIHN0cmF0ZWd5KSk7XG4gIHJldHVybiB7XG4gICAgd2lkdGg6IGNsaXBwaW5nUmVjdC5yaWdodCAtIGNsaXBwaW5nUmVjdC5sZWZ0LFxuICAgIGhlaWdodDogY2xpcHBpbmdSZWN0LmJvdHRvbSAtIGNsaXBwaW5nUmVjdC50b3AsXG4gICAgeDogY2xpcHBpbmdSZWN0LmxlZnQsXG4gICAgeTogY2xpcHBpbmdSZWN0LnRvcFxuICB9O1xufVxudmFyIHBsYXRmb3JtID0ge1xuICBnZXRDbGlwcGluZ1JlY3QsXG4gIGNvbnZlcnRPZmZzZXRQYXJlbnRSZWxhdGl2ZVJlY3RUb1ZpZXdwb3J0UmVsYXRpdmVSZWN0LFxuICBpc0VsZW1lbnQsXG4gIGdldERpbWVuc2lvbnMsXG4gIGdldE9mZnNldFBhcmVudCxcbiAgZ2V0RG9jdW1lbnRFbGVtZW50LFxuICBnZXRFbGVtZW50UmVjdHM6IChfcmVmKSA9PiB7XG4gICAgbGV0IHtcbiAgICAgIHJlZmVyZW5jZSxcbiAgICAgIGZsb2F0aW5nLFxuICAgICAgc3RyYXRlZ3lcbiAgICB9ID0gX3JlZjtcbiAgICByZXR1cm4ge1xuICAgICAgcmVmZXJlbmNlOiBnZXRSZWN0UmVsYXRpdmVUb09mZnNldFBhcmVudChyZWZlcmVuY2UsIGdldE9mZnNldFBhcmVudChmbG9hdGluZyksIHN0cmF0ZWd5KSxcbiAgICAgIGZsb2F0aW5nOiB7XG4gICAgICAgIC4uLmdldERpbWVuc2lvbnMoZmxvYXRpbmcpLFxuICAgICAgICB4OiAwLFxuICAgICAgICB5OiAwXG4gICAgICB9XG4gICAgfTtcbiAgfSxcbiAgZ2V0Q2xpZW50UmVjdHM6IChlbGVtZW50KSA9PiBBcnJheS5mcm9tKGVsZW1lbnQuZ2V0Q2xpZW50UmVjdHMoKSksXG4gIGlzUlRMOiAoZWxlbWVudCkgPT4gZ2V0Q29tcHV0ZWRTdHlsZSQxKGVsZW1lbnQpLmRpcmVjdGlvbiA9PT0gXCJydGxcIlxufTtcbmZ1bmN0aW9uIGF1dG9VcGRhdGUocmVmZXJlbmNlLCBmbG9hdGluZywgdXBkYXRlLCBvcHRpb25zKSB7XG4gIGlmIChvcHRpb25zID09PSB2b2lkIDApIHtcbiAgICBvcHRpb25zID0ge307XG4gIH1cbiAgY29uc3Qge1xuICAgIGFuY2VzdG9yU2Nyb2xsOiBfYW5jZXN0b3JTY3JvbGwgPSB0cnVlLFxuICAgIGFuY2VzdG9yUmVzaXplOiBfYW5jZXN0b3JSZXNpemUgPSB0cnVlLFxuICAgIGVsZW1lbnRSZXNpemU6IF9lbGVtZW50UmVzaXplID0gdHJ1ZSxcbiAgICBhbmltYXRpb25GcmFtZSA9IGZhbHNlXG4gIH0gPSBvcHRpb25zO1xuICBsZXQgY2xlYW5lZFVwID0gZmFsc2U7XG4gIGNvbnN0IGFuY2VzdG9yU2Nyb2xsID0gX2FuY2VzdG9yU2Nyb2xsICYmICFhbmltYXRpb25GcmFtZTtcbiAgY29uc3QgYW5jZXN0b3JSZXNpemUgPSBfYW5jZXN0b3JSZXNpemUgJiYgIWFuaW1hdGlvbkZyYW1lO1xuICBjb25zdCBlbGVtZW50UmVzaXplID0gX2VsZW1lbnRSZXNpemUgJiYgIWFuaW1hdGlvbkZyYW1lO1xuICBjb25zdCBhbmNlc3RvcnMgPSBhbmNlc3RvclNjcm9sbCB8fCBhbmNlc3RvclJlc2l6ZSA/IFsuLi5pc0VsZW1lbnQocmVmZXJlbmNlKSA/IGdldE92ZXJmbG93QW5jZXN0b3JzKHJlZmVyZW5jZSkgOiBbXSwgLi4uZ2V0T3ZlcmZsb3dBbmNlc3RvcnMoZmxvYXRpbmcpXSA6IFtdO1xuICBhbmNlc3RvcnMuZm9yRWFjaCgoYW5jZXN0b3IpID0+IHtcbiAgICBhbmNlc3RvclNjcm9sbCAmJiBhbmNlc3Rvci5hZGRFdmVudExpc3RlbmVyKFwic2Nyb2xsXCIsIHVwZGF0ZSwge1xuICAgICAgcGFzc2l2ZTogdHJ1ZVxuICAgIH0pO1xuICAgIGFuY2VzdG9yUmVzaXplICYmIGFuY2VzdG9yLmFkZEV2ZW50TGlzdGVuZXIoXCJyZXNpemVcIiwgdXBkYXRlKTtcbiAgfSk7XG4gIGxldCBvYnNlcnZlcjIgPSBudWxsO1xuICBpZiAoZWxlbWVudFJlc2l6ZSkge1xuICAgIG9ic2VydmVyMiA9IG5ldyBSZXNpemVPYnNlcnZlcih1cGRhdGUpO1xuICAgIGlzRWxlbWVudChyZWZlcmVuY2UpICYmIG9ic2VydmVyMi5vYnNlcnZlKHJlZmVyZW5jZSk7XG4gICAgb2JzZXJ2ZXIyLm9ic2VydmUoZmxvYXRpbmcpO1xuICB9XG4gIGxldCBmcmFtZUlkO1xuICBsZXQgcHJldlJlZlJlY3QgPSBhbmltYXRpb25GcmFtZSA/IGdldEJvdW5kaW5nQ2xpZW50UmVjdChyZWZlcmVuY2UpIDogbnVsbDtcbiAgaWYgKGFuaW1hdGlvbkZyYW1lKSB7XG4gICAgZnJhbWVMb29wKCk7XG4gIH1cbiAgZnVuY3Rpb24gZnJhbWVMb29wKCkge1xuICAgIGlmIChjbGVhbmVkVXApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbmV4dFJlZlJlY3QgPSBnZXRCb3VuZGluZ0NsaWVudFJlY3QocmVmZXJlbmNlKTtcbiAgICBpZiAocHJldlJlZlJlY3QgJiYgKG5leHRSZWZSZWN0LnggIT09IHByZXZSZWZSZWN0LnggfHwgbmV4dFJlZlJlY3QueSAhPT0gcHJldlJlZlJlY3QueSB8fCBuZXh0UmVmUmVjdC53aWR0aCAhPT0gcHJldlJlZlJlY3Qud2lkdGggfHwgbmV4dFJlZlJlY3QuaGVpZ2h0ICE9PSBwcmV2UmVmUmVjdC5oZWlnaHQpKSB7XG4gICAgICB1cGRhdGUoKTtcbiAgICB9XG4gICAgcHJldlJlZlJlY3QgPSBuZXh0UmVmUmVjdDtcbiAgICBmcmFtZUlkID0gcmVxdWVzdEFuaW1hdGlvbkZyYW1lKGZyYW1lTG9vcCk7XG4gIH1cbiAgcmV0dXJuICgpID0+IHtcbiAgICB2YXIgX29ic2VydmVyO1xuICAgIGNsZWFuZWRVcCA9IHRydWU7XG4gICAgYW5jZXN0b3JzLmZvckVhY2goKGFuY2VzdG9yKSA9PiB7XG4gICAgICBhbmNlc3RvclNjcm9sbCAmJiBhbmNlc3Rvci5yZW1vdmVFdmVudExpc3RlbmVyKFwic2Nyb2xsXCIsIHVwZGF0ZSk7XG4gICAgICBhbmNlc3RvclJlc2l6ZSAmJiBhbmNlc3Rvci5yZW1vdmVFdmVudExpc3RlbmVyKFwicmVzaXplXCIsIHVwZGF0ZSk7XG4gICAgfSk7XG4gICAgKF9vYnNlcnZlciA9IG9ic2VydmVyMikgPT0gbnVsbCA/IHZvaWQgMCA6IF9vYnNlcnZlci5kaXNjb25uZWN0KCk7XG4gICAgb2JzZXJ2ZXIyID0gbnVsbDtcbiAgICBpZiAoYW5pbWF0aW9uRnJhbWUpIHtcbiAgICAgIGNhbmNlbEFuaW1hdGlvbkZyYW1lKGZyYW1lSWQpO1xuICAgIH1cbiAgfTtcbn1cbnZhciBjb21wdXRlUG9zaXRpb24yID0gKHJlZmVyZW5jZSwgZmxvYXRpbmcsIG9wdGlvbnMpID0+IGNvbXB1dGVQb3NpdGlvbihyZWZlcmVuY2UsIGZsb2F0aW5nLCB7XG4gIHBsYXRmb3JtLFxuICAuLi5vcHRpb25zXG59KTtcblxuLy8gc3JjL2J1aWxkQ29uZmlnRnJvbU1vZGlmaWVycy5qc1xudmFyIGJ1aWxkQ29uZmlnRnJvbU1vZGlmaWVycyA9IChtb2RpZmllcnMpID0+IHtcbiAgY29uc3QgY29uZmlnID0ge1xuICAgIHBsYWNlbWVudDogXCJib3R0b21cIixcbiAgICBtaWRkbGV3YXJlOiBbXVxuICB9O1xuICBjb25zdCBrZXlzID0gT2JqZWN0LmtleXMobW9kaWZpZXJzKTtcbiAgY29uc3QgZ2V0TW9kaWZpZXJBcmd1bWVudCA9IChtb2RpZmllcikgPT4ge1xuICAgIHJldHVybiBtb2RpZmllcnNbbW9kaWZpZXJdO1xuICB9O1xuICBpZiAoa2V5cy5pbmNsdWRlcyhcIm9mZnNldFwiKSkge1xuICAgIGNvbmZpZy5taWRkbGV3YXJlLnB1c2gob2Zmc2V0KGdldE1vZGlmaWVyQXJndW1lbnQoXCJvZmZzZXRcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcInBsYWNlbWVudFwiKSkge1xuICAgIGNvbmZpZy5wbGFjZW1lbnQgPSBnZXRNb2RpZmllckFyZ3VtZW50KFwicGxhY2VtZW50XCIpO1xuICB9XG4gIGlmIChrZXlzLmluY2x1ZGVzKFwiYXV0b1BsYWNlbWVudFwiKSAmJiAha2V5cy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKGF1dG9QbGFjZW1lbnQoZ2V0TW9kaWZpZXJBcmd1bWVudChcImF1dG9QbGFjZW1lbnRcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKGZsaXAoZ2V0TW9kaWZpZXJBcmd1bWVudChcImZsaXBcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcInNoaWZ0XCIpKSB7XG4gICAgY29uZmlnLm1pZGRsZXdhcmUucHVzaChzaGlmdChnZXRNb2RpZmllckFyZ3VtZW50KFwic2hpZnRcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImlubGluZVwiKSkge1xuICAgIGNvbmZpZy5taWRkbGV3YXJlLnB1c2goaW5saW5lKGdldE1vZGlmaWVyQXJndW1lbnQoXCJpbmxpbmVcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImFycm93XCIpKSB7XG4gICAgY29uZmlnLm1pZGRsZXdhcmUucHVzaChhcnJvdyhnZXRNb2RpZmllckFyZ3VtZW50KFwiYXJyb3dcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcImhpZGVcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKGhpZGUoZ2V0TW9kaWZpZXJBcmd1bWVudChcImhpZGVcIikpKTtcbiAgfVxuICBpZiAoa2V5cy5pbmNsdWRlcyhcInNpemVcIikpIHtcbiAgICBjb25maWcubWlkZGxld2FyZS5wdXNoKHNpemUoZ2V0TW9kaWZpZXJBcmd1bWVudChcInNpemVcIikpKTtcbiAgfVxuICByZXR1cm4gY29uZmlnO1xufTtcblxuLy8gc3JjL2J1aWxkRGlyZWN0aXZlQ29uZmlnRnJvbU1vZGlmaWVycy5qc1xudmFyIGJ1aWxkRGlyZWN0aXZlQ29uZmlnRnJvbU1vZGlmaWVycyA9IChtb2RpZmllcnMsIHNldHRpbmdzKSA9PiB7XG4gIGNvbnN0IGNvbmZpZyA9IHtcbiAgICBjb21wb25lbnQ6IHtcbiAgICAgIHRyYXA6IGZhbHNlXG4gICAgfSxcbiAgICBmbG9hdDoge1xuICAgICAgcGxhY2VtZW50OiBcImJvdHRvbVwiLFxuICAgICAgc3RyYXRlZ3k6IFwiYWJzb2x1dGVcIixcbiAgICAgIG1pZGRsZXdhcmU6IFtdXG4gICAgfVxuICB9O1xuICBjb25zdCBnZXRNb2RpZmllckFyZ3VtZW50ID0gKG1vZGlmaWVyKSA9PiB7XG4gICAgcmV0dXJuIG1vZGlmaWVyc1ttb2RpZmllcnMuaW5kZXhPZihtb2RpZmllcikgKyAxXTtcbiAgfTtcbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInRyYXBcIikpIHtcbiAgICBjb25maWcuY29tcG9uZW50LnRyYXAgPSB0cnVlO1xuICB9XG4gIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJ0ZWxlcG9ydFwiKSkge1xuICAgIGNvbmZpZy5mbG9hdC5zdHJhdGVneSA9IFwiZml4ZWRcIjtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwib2Zmc2V0XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUucHVzaChvZmZzZXQoc2V0dGluZ3NbXCJvZmZzZXRcIl0gfHwgMTApKTtcbiAgfVxuICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwicGxhY2VtZW50XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0LnBsYWNlbWVudCA9IGdldE1vZGlmaWVyQXJndW1lbnQoXCJwbGFjZW1lbnRcIik7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImF1dG9QbGFjZW1lbnRcIikgJiYgIW1vZGlmaWVycy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKGF1dG9QbGFjZW1lbnQoc2V0dGluZ3NbXCJhdXRvUGxhY2VtZW50XCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImZsaXBcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKGZsaXAoc2V0dGluZ3NbXCJmbGlwXCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInNoaWZ0XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUucHVzaChzaGlmdChzZXR0aW5nc1tcInNoaWZ0XCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImlubGluZVwiKSkge1xuICAgIGNvbmZpZy5mbG9hdC5taWRkbGV3YXJlLnB1c2goaW5saW5lKHNldHRpbmdzW1wiaW5saW5lXCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImFycm93XCIpKSB7XG4gICAgY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUucHVzaChhcnJvdyhzZXR0aW5nc1tcImFycm93XCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImhpZGVcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKGhpZGUoc2V0dGluZ3NbXCJoaWRlXCJdKSk7XG4gIH1cbiAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcInNpemVcIikpIHtcbiAgICBjb25maWcuZmxvYXQubWlkZGxld2FyZS5wdXNoKHNpemUoc2V0dGluZ3NbXCJzaXplXCJdKSk7XG4gIH1cbiAgcmV0dXJuIGNvbmZpZztcbn07XG5cbi8vIHNyYy9yYW5kb21TdHJpbmcuanNcbnZhciByYW5kb21TdHJpbmcgPSAobGVuZ3RoKSA9PiB7XG4gIHZhciBjaGFycyA9IFwiMDEyMzQ1Njc4OUFCQ0RFRkdISUpLTE1OT1BRUlNUVVZXWFRaYWJjZGVmZ2hpa2xtbm9wcXJzdHV2d3h5elwiLnNwbGl0KFwiXCIpO1xuICB2YXIgc3RyID0gXCJcIjtcbiAgaWYgKCFsZW5ndGgpIHtcbiAgICBsZW5ndGggPSBNYXRoLmZsb29yKE1hdGgucmFuZG9tKCkgKiBjaGFycy5sZW5ndGgpO1xuICB9XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuZ3RoOyBpKyspIHtcbiAgICBzdHIgKz0gY2hhcnNbTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogY2hhcnMubGVuZ3RoKV07XG4gIH1cbiAgcmV0dXJuIHN0cjtcbn07XG5cbi8vIG5vZGVfbW9kdWxlcy9hbHBpbmVqcy9zcmMvbXV0YXRpb24uanNcbnZhciBvbkF0dHJpYnV0ZUFkZGVkcyA9IFtdO1xudmFyIG9uRWxSZW1vdmVkcyA9IFtdO1xudmFyIG9uRWxBZGRlZHMgPSBbXTtcbmZ1bmN0aW9uIGNsZWFudXBBdHRyaWJ1dGVzKGVsLCBuYW1lcykge1xuICBpZiAoIWVsLl94X2F0dHJpYnV0ZUNsZWFudXBzKVxuICAgIHJldHVybjtcbiAgT2JqZWN0LmVudHJpZXMoZWwuX3hfYXR0cmlidXRlQ2xlYW51cHMpLmZvckVhY2goKFtuYW1lLCB2YWx1ZV0pID0+IHtcbiAgICBpZiAobmFtZXMgPT09IHZvaWQgMCB8fCBuYW1lcy5pbmNsdWRlcyhuYW1lKSkge1xuICAgICAgdmFsdWUuZm9yRWFjaCgoaSkgPT4gaSgpKTtcbiAgICAgIGRlbGV0ZSBlbC5feF9hdHRyaWJ1dGVDbGVhbnVwc1tuYW1lXTtcbiAgICB9XG4gIH0pO1xufVxudmFyIG9ic2VydmVyID0gbmV3IE11dGF0aW9uT2JzZXJ2ZXIob25NdXRhdGUpO1xudmFyIGN1cnJlbnRseU9ic2VydmluZyA9IGZhbHNlO1xuZnVuY3Rpb24gc3RhcnRPYnNlcnZpbmdNdXRhdGlvbnMoKSB7XG4gIG9ic2VydmVyLm9ic2VydmUoZG9jdW1lbnQsIHsgc3VidHJlZTogdHJ1ZSwgY2hpbGRMaXN0OiB0cnVlLCBhdHRyaWJ1dGVzOiB0cnVlLCBhdHRyaWJ1dGVPbGRWYWx1ZTogdHJ1ZSB9KTtcbiAgY3VycmVudGx5T2JzZXJ2aW5nID0gdHJ1ZTtcbn1cbmZ1bmN0aW9uIHN0b3BPYnNlcnZpbmdNdXRhdGlvbnMoKSB7XG4gIGZsdXNoT2JzZXJ2ZXIoKTtcbiAgb2JzZXJ2ZXIuZGlzY29ubmVjdCgpO1xuICBjdXJyZW50bHlPYnNlcnZpbmcgPSBmYWxzZTtcbn1cbnZhciByZWNvcmRRdWV1ZSA9IFtdO1xudmFyIHdpbGxQcm9jZXNzUmVjb3JkUXVldWUgPSBmYWxzZTtcbmZ1bmN0aW9uIGZsdXNoT2JzZXJ2ZXIoKSB7XG4gIHJlY29yZFF1ZXVlID0gcmVjb3JkUXVldWUuY29uY2F0KG9ic2VydmVyLnRha2VSZWNvcmRzKCkpO1xuICBpZiAocmVjb3JkUXVldWUubGVuZ3RoICYmICF3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlKSB7XG4gICAgd2lsbFByb2Nlc3NSZWNvcmRRdWV1ZSA9IHRydWU7XG4gICAgcXVldWVNaWNyb3Rhc2soKCkgPT4ge1xuICAgICAgcHJvY2Vzc1JlY29yZFF1ZXVlKCk7XG4gICAgICB3aWxsUHJvY2Vzc1JlY29yZFF1ZXVlID0gZmFsc2U7XG4gICAgfSk7XG4gIH1cbn1cbmZ1bmN0aW9uIHByb2Nlc3NSZWNvcmRRdWV1ZSgpIHtcbiAgb25NdXRhdGUocmVjb3JkUXVldWUpO1xuICByZWNvcmRRdWV1ZS5sZW5ndGggPSAwO1xufVxuZnVuY3Rpb24gbXV0YXRlRG9tKGNhbGxiYWNrKSB7XG4gIGlmICghY3VycmVudGx5T2JzZXJ2aW5nKVxuICAgIHJldHVybiBjYWxsYmFjaygpO1xuICBzdG9wT2JzZXJ2aW5nTXV0YXRpb25zKCk7XG4gIGxldCByZXN1bHQgPSBjYWxsYmFjaygpO1xuICBzdGFydE9ic2VydmluZ011dGF0aW9ucygpO1xuICByZXR1cm4gcmVzdWx0O1xufVxudmFyIGlzQ29sbGVjdGluZyA9IGZhbHNlO1xudmFyIGRlZmVycmVkTXV0YXRpb25zID0gW107XG5mdW5jdGlvbiBvbk11dGF0ZShtdXRhdGlvbnMpIHtcbiAgaWYgKGlzQ29sbGVjdGluZykge1xuICAgIGRlZmVycmVkTXV0YXRpb25zID0gZGVmZXJyZWRNdXRhdGlvbnMuY29uY2F0KG11dGF0aW9ucyk7XG4gICAgcmV0dXJuO1xuICB9XG4gIGxldCBhZGRlZE5vZGVzID0gW107XG4gIGxldCByZW1vdmVkTm9kZXMgPSBbXTtcbiAgbGV0IGFkZGVkQXR0cmlidXRlcyA9IC8qIEBfX1BVUkVfXyAqLyBuZXcgTWFwKCk7XG4gIGxldCByZW1vdmVkQXR0cmlidXRlcyA9IC8qIEBfX1BVUkVfXyAqLyBuZXcgTWFwKCk7XG4gIGZvciAobGV0IGkgPSAwOyBpIDwgbXV0YXRpb25zLmxlbmd0aDsgaSsrKSB7XG4gICAgaWYgKG11dGF0aW9uc1tpXS50YXJnZXQuX3hfaWdub3JlTXV0YXRpb25PYnNlcnZlcilcbiAgICAgIGNvbnRpbnVlO1xuICAgIGlmIChtdXRhdGlvbnNbaV0udHlwZSA9PT0gXCJjaGlsZExpc3RcIikge1xuICAgICAgbXV0YXRpb25zW2ldLmFkZGVkTm9kZXMuZm9yRWFjaCgobm9kZSkgPT4gbm9kZS5ub2RlVHlwZSA9PT0gMSAmJiBhZGRlZE5vZGVzLnB1c2gobm9kZSkpO1xuICAgICAgbXV0YXRpb25zW2ldLnJlbW92ZWROb2Rlcy5mb3JFYWNoKChub2RlKSA9PiBub2RlLm5vZGVUeXBlID09PSAxICYmIHJlbW92ZWROb2Rlcy5wdXNoKG5vZGUpKTtcbiAgICB9XG4gICAgaWYgKG11dGF0aW9uc1tpXS50eXBlID09PSBcImF0dHJpYnV0ZXNcIikge1xuICAgICAgbGV0IGVsID0gbXV0YXRpb25zW2ldLnRhcmdldDtcbiAgICAgIGxldCBuYW1lID0gbXV0YXRpb25zW2ldLmF0dHJpYnV0ZU5hbWU7XG4gICAgICBsZXQgb2xkVmFsdWUgPSBtdXRhdGlvbnNbaV0ub2xkVmFsdWU7XG4gICAgICBsZXQgYWRkID0gKCkgPT4ge1xuICAgICAgICBpZiAoIWFkZGVkQXR0cmlidXRlcy5oYXMoZWwpKVxuICAgICAgICAgIGFkZGVkQXR0cmlidXRlcy5zZXQoZWwsIFtdKTtcbiAgICAgICAgYWRkZWRBdHRyaWJ1dGVzLmdldChlbCkucHVzaCh7IG5hbWUsIHZhbHVlOiBlbC5nZXRBdHRyaWJ1dGUobmFtZSkgfSk7XG4gICAgICB9O1xuICAgICAgbGV0IHJlbW92ZSA9ICgpID0+IHtcbiAgICAgICAgaWYgKCFyZW1vdmVkQXR0cmlidXRlcy5oYXMoZWwpKVxuICAgICAgICAgIHJlbW92ZWRBdHRyaWJ1dGVzLnNldChlbCwgW10pO1xuICAgICAgICByZW1vdmVkQXR0cmlidXRlcy5nZXQoZWwpLnB1c2gobmFtZSk7XG4gICAgICB9O1xuICAgICAgaWYgKGVsLmhhc0F0dHJpYnV0ZShuYW1lKSAmJiBvbGRWYWx1ZSA9PT0gbnVsbCkge1xuICAgICAgICBhZGQoKTtcbiAgICAgIH0gZWxzZSBpZiAoZWwuaGFzQXR0cmlidXRlKG5hbWUpKSB7XG4gICAgICAgIHJlbW92ZSgpO1xuICAgICAgICBhZGQoKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHJlbW92ZSgpO1xuICAgICAgfVxuICAgIH1cbiAgfVxuICByZW1vdmVkQXR0cmlidXRlcy5mb3JFYWNoKChhdHRycywgZWwpID0+IHtcbiAgICBjbGVhbnVwQXR0cmlidXRlcyhlbCwgYXR0cnMpO1xuICB9KTtcbiAgYWRkZWRBdHRyaWJ1dGVzLmZvckVhY2goKGF0dHJzLCBlbCkgPT4ge1xuICAgIG9uQXR0cmlidXRlQWRkZWRzLmZvckVhY2goKGkpID0+IGkoZWwsIGF0dHJzKSk7XG4gIH0pO1xuICBmb3IgKGxldCBub2RlIG9mIHJlbW92ZWROb2Rlcykge1xuICAgIGlmIChhZGRlZE5vZGVzLmluY2x1ZGVzKG5vZGUpKVxuICAgICAgY29udGludWU7XG4gICAgb25FbFJlbW92ZWRzLmZvckVhY2goKGkpID0+IGkobm9kZSkpO1xuICAgIGlmIChub2RlLl94X2NsZWFudXBzKSB7XG4gICAgICB3aGlsZSAobm9kZS5feF9jbGVhbnVwcy5sZW5ndGgpXG4gICAgICAgIG5vZGUuX3hfY2xlYW51cHMucG9wKCkoKTtcbiAgICB9XG4gIH1cbiAgYWRkZWROb2Rlcy5mb3JFYWNoKChub2RlKSA9PiB7XG4gICAgbm9kZS5feF9pZ25vcmVTZWxmID0gdHJ1ZTtcbiAgICBub2RlLl94X2lnbm9yZSA9IHRydWU7XG4gIH0pO1xuICBmb3IgKGxldCBub2RlIG9mIGFkZGVkTm9kZXMpIHtcbiAgICBpZiAocmVtb3ZlZE5vZGVzLmluY2x1ZGVzKG5vZGUpKVxuICAgICAgY29udGludWU7XG4gICAgaWYgKCFub2RlLmlzQ29ubmVjdGVkKVxuICAgICAgY29udGludWU7XG4gICAgZGVsZXRlIG5vZGUuX3hfaWdub3JlU2VsZjtcbiAgICBkZWxldGUgbm9kZS5feF9pZ25vcmU7XG4gICAgb25FbEFkZGVkcy5mb3JFYWNoKChpKSA9PiBpKG5vZGUpKTtcbiAgICBub2RlLl94X2lnbm9yZSA9IHRydWU7XG4gICAgbm9kZS5feF9pZ25vcmVTZWxmID0gdHJ1ZTtcbiAgfVxuICBhZGRlZE5vZGVzLmZvckVhY2goKG5vZGUpID0+IHtcbiAgICBkZWxldGUgbm9kZS5feF9pZ25vcmVTZWxmO1xuICAgIGRlbGV0ZSBub2RlLl94X2lnbm9yZTtcbiAgfSk7XG4gIGFkZGVkTm9kZXMgPSBudWxsO1xuICByZW1vdmVkTm9kZXMgPSBudWxsO1xuICBhZGRlZEF0dHJpYnV0ZXMgPSBudWxsO1xuICByZW1vdmVkQXR0cmlidXRlcyA9IG51bGw7XG59XG5cbi8vIG5vZGVfbW9kdWxlcy9hbHBpbmVqcy9zcmMvdXRpbHMvb25jZS5qc1xuZnVuY3Rpb24gb25jZShjYWxsYmFjaywgZmFsbGJhY2sgPSAoKSA9PiB7XG59KSB7XG4gIGxldCBjYWxsZWQgPSBmYWxzZTtcbiAgcmV0dXJuIGZ1bmN0aW9uKCkge1xuICAgIGlmICghY2FsbGVkKSB7XG4gICAgICBjYWxsZWQgPSB0cnVlO1xuICAgICAgY2FsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZmFsbGJhY2suYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICB9XG4gIH07XG59XG5cbi8vIHNyYy9pbmRleC5qc1xuZnVuY3Rpb24gc3JjX2RlZmF1bHQoQWxwaW5lKSB7XG4gIGNvbnN0IGRlZmF1bHRPcHRpb25zID0ge1xuICAgIGRpc21pc3NhYmxlOiB0cnVlLFxuICAgIHRyYXA6IGZhbHNlXG4gIH07XG4gIGZ1bmN0aW9uIHNldHVwQTExeShjb21wb25lbnQsIHRyaWdnZXIsIHBhbmVsID0gbnVsbCkge1xuICAgIGlmICghdHJpZ2dlcilcbiAgICAgIHJldHVybjtcbiAgICBpZiAoIXRyaWdnZXIuaGFzQXR0cmlidXRlKFwiYXJpYS1leHBhbmRlZFwiKSkge1xuICAgICAgdHJpZ2dlci5zZXRBdHRyaWJ1dGUoXCJhcmlhLWV4cGFuZGVkXCIsIGZhbHNlKTtcbiAgICB9XG4gICAgaWYgKCFwYW5lbC5oYXNBdHRyaWJ1dGUoXCJpZFwiKSkge1xuICAgICAgY29uc3QgcGFuZWxJZCA9IGBwYW5lbC0ke3JhbmRvbVN0cmluZyg4KX1gO1xuICAgICAgdHJpZ2dlci5zZXRBdHRyaWJ1dGUoXCJhcmlhLWNvbnRyb2xzXCIsIHBhbmVsSWQpO1xuICAgICAgcGFuZWwuc2V0QXR0cmlidXRlKFwiaWRcIiwgcGFuZWxJZCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRyaWdnZXIuc2V0QXR0cmlidXRlKFwiYXJpYS1jb250cm9sc1wiLCBwYW5lbC5nZXRBdHRyaWJ1dGUoXCJpZFwiKSk7XG4gICAgfVxuICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcImFyaWEtbW9kYWxcIiwgdHJ1ZSk7XG4gICAgcGFuZWwuc2V0QXR0cmlidXRlKFwicm9sZVwiLCBcImRpYWxvZ1wiKTtcbiAgfVxuICBjb25zdCBhdE1hZ2ljVHJpZ2dlciA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ1tcXFxcQGNsaWNrXj1cIiRmbG9hdFwiXScpO1xuICBjb25zdCB4TWFnaWNUcmlnZ2VyID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW3gtb25cXFxcOmNsaWNrXj1cIiRmbG9hdFwiXScpO1xuICBbLi4uYXRNYWdpY1RyaWdnZXIsIC4uLnhNYWdpY1RyaWdnZXJdLmZvckVhY2goKHRyaWdnZXIpID0+IHtcbiAgICBjb25zdCBjb21wb25lbnQgPSB0cmlnZ2VyLnBhcmVudEVsZW1lbnQuY2xvc2VzdChcIlt4LWRhdGFdXCIpO1xuICAgIGNvbnN0IHBhbmVsID0gY29tcG9uZW50LnF1ZXJ5U2VsZWN0b3IoJ1t4LXJlZj1cInBhbmVsXCJdJyk7XG4gICAgc2V0dXBBMTF5KGNvbXBvbmVudCwgdHJpZ2dlciwgcGFuZWwpO1xuICB9KTtcbiAgQWxwaW5lLm1hZ2ljKFwiZmxvYXRcIiwgKGVsKSA9PiB7XG4gICAgcmV0dXJuIChtb2RpZmllcnMgPSB7fSwgc2V0dGluZ3MgPSB7fSkgPT4ge1xuICAgICAgY29uc3Qgb3B0aW9ucyA9IHsgLi4uZGVmYXVsdE9wdGlvbnMsIC4uLnNldHRpbmdzIH07XG4gICAgICBjb25zdCBjb25maWcgPSBPYmplY3Qua2V5cyhtb2RpZmllcnMpLmxlbmd0aCA+IDAgPyBidWlsZENvbmZpZ0Zyb21Nb2RpZmllcnMobW9kaWZpZXJzKSA6IHsgbWlkZGxld2FyZTogW2F1dG9QbGFjZW1lbnQoKV0gfTtcbiAgICAgIGNvbnN0IHRyaWdnZXIgPSBlbDtcbiAgICAgIGNvbnN0IGNvbXBvbmVudCA9IGVsLnBhcmVudEVsZW1lbnQuY2xvc2VzdChcIlt4LWRhdGFdXCIpO1xuICAgICAgY29uc3QgcGFuZWwgPSBjb21wb25lbnQucXVlcnlTZWxlY3RvcignW3gtcmVmPVwicGFuZWxcIl0nKTtcbiAgICAgIGZ1bmN0aW9uIGlzRmxvYXRpbmcoKSB7XG4gICAgICAgIHJldHVybiBwYW5lbC5zdHlsZS5kaXNwbGF5ID09IFwiYmxvY2tcIjtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIGNsb3NlUGFuZWwoKSB7XG4gICAgICAgIHBhbmVsLnN0eWxlLmRpc3BsYXkgPSBcIlwiO1xuICAgICAgICB0cmlnZ2VyLnNldEF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIiwgZmFsc2UpO1xuICAgICAgICBpZiAob3B0aW9ucy50cmFwKVxuICAgICAgICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcIngtdHJhcFwiLCBmYWxzZSk7XG4gICAgICAgIGF1dG9VcGRhdGUoZWwsIHBhbmVsLCB1cGRhdGUpO1xuICAgICAgfVxuICAgICAgZnVuY3Rpb24gb3BlblBhbmVsKCkge1xuICAgICAgICBwYW5lbC5zdHlsZS5kaXNwbGF5ID0gXCJibG9ja1wiO1xuICAgICAgICB0cmlnZ2VyLnNldEF0dHJpYnV0ZShcImFyaWEtZXhwYW5kZWRcIiwgdHJ1ZSk7XG4gICAgICAgIGlmIChvcHRpb25zLnRyYXApXG4gICAgICAgICAgcGFuZWwuc2V0QXR0cmlidXRlKFwieC10cmFwXCIsIHRydWUpO1xuICAgICAgICB1cGRhdGUoKTtcbiAgICAgIH1cbiAgICAgIGZ1bmN0aW9uIHRvZ2dsZVBhbmVsKCkge1xuICAgICAgICBpc0Zsb2F0aW5nKCkgPyBjbG9zZVBhbmVsKCkgOiBvcGVuUGFuZWwoKTtcbiAgICAgIH1cbiAgICAgIGFzeW5jIGZ1bmN0aW9uIHVwZGF0ZSgpIHtcbiAgICAgICAgcmV0dXJuIGF3YWl0IGNvbXB1dGVQb3NpdGlvbjIoZWwsIHBhbmVsLCBjb25maWcpLnRoZW4oKHsgbWlkZGxld2FyZURhdGEsIHBsYWNlbWVudCwgeCwgeSB9KSA9PiB7XG4gICAgICAgICAgaWYgKG1pZGRsZXdhcmVEYXRhLmFycm93KSB7XG4gICAgICAgICAgICBjb25zdCBheCA9IG1pZGRsZXdhcmVEYXRhLmFycm93Py54O1xuICAgICAgICAgICAgY29uc3QgYXkgPSBtaWRkbGV3YXJlRGF0YS5hcnJvdz8ueTtcbiAgICAgICAgICAgIGNvbnN0IGFFbCA9IGNvbmZpZy5taWRkbGV3YXJlLmZpbHRlcigobWlkZGxld2FyZSkgPT4gbWlkZGxld2FyZS5uYW1lID09IFwiYXJyb3dcIilbMF0ub3B0aW9ucy5lbGVtZW50O1xuICAgICAgICAgICAgY29uc3Qgc3RhdGljU2lkZSA9IHtcbiAgICAgICAgICAgICAgdG9wOiBcImJvdHRvbVwiLFxuICAgICAgICAgICAgICByaWdodDogXCJsZWZ0XCIsXG4gICAgICAgICAgICAgIGJvdHRvbTogXCJ0b3BcIixcbiAgICAgICAgICAgICAgbGVmdDogXCJyaWdodFwiXG4gICAgICAgICAgICB9W3BsYWNlbWVudC5zcGxpdChcIi1cIilbMF1dO1xuICAgICAgICAgICAgT2JqZWN0LmFzc2lnbihhRWwuc3R5bGUsIHtcbiAgICAgICAgICAgICAgbGVmdDogYXggIT0gbnVsbCA/IGAke2F4fXB4YCA6IFwiXCIsXG4gICAgICAgICAgICAgIHRvcDogYXkgIT0gbnVsbCA/IGAke2F5fXB4YCA6IFwiXCIsXG4gICAgICAgICAgICAgIHJpZ2h0OiBcIlwiLFxuICAgICAgICAgICAgICBib3R0b206IFwiXCIsXG4gICAgICAgICAgICAgIFtzdGF0aWNTaWRlXTogXCItNHB4XCJcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgIH1cbiAgICAgICAgICBpZiAobWlkZGxld2FyZURhdGEuaGlkZSkge1xuICAgICAgICAgICAgY29uc3QgeyByZWZlcmVuY2VIaWRkZW4gfSA9IG1pZGRsZXdhcmVEYXRhLmhpZGU7XG4gICAgICAgICAgICBPYmplY3QuYXNzaWduKHBhbmVsLnN0eWxlLCB7XG4gICAgICAgICAgICAgIHZpc2liaWxpdHk6IHJlZmVyZW5jZUhpZGRlbiA/IFwiaGlkZGVuXCIgOiBcInZpc2libGVcIlxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfVxuICAgICAgICAgIE9iamVjdC5hc3NpZ24ocGFuZWwuc3R5bGUsIHtcbiAgICAgICAgICAgIGxlZnQ6IGAke3h9cHhgLFxuICAgICAgICAgICAgdG9wOiBgJHt5fXB4YFxuICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICAgIGlmIChvcHRpb25zLmRpc21pc3NhYmxlKSB7XG4gICAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwiY2xpY2tcIiwgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgaWYgKCFjb21wb25lbnQuY29udGFpbnMoZXZlbnQudGFyZ2V0KSAmJiBpc0Zsb2F0aW5nKCkpIHtcbiAgICAgICAgICAgIHRvZ2dsZVBhbmVsKCk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXG4gICAgICAgICAgXCJrZXlkb3duXCIsXG4gICAgICAgICAgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICBpZiAoZXZlbnQua2V5ID09PSBcIkVzY2FwZVwiICYmIGlzRmxvYXRpbmcoKSkge1xuICAgICAgICAgICAgICB0b2dnbGVQYW5lbCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgIH0sXG4gICAgICAgICAgdHJ1ZVxuICAgICAgICApO1xuICAgICAgfVxuICAgICAgdG9nZ2xlUGFuZWwoKTtcbiAgICB9O1xuICB9KTtcbiAgQWxwaW5lLmRpcmVjdGl2ZShcImZsb2F0XCIsIChwYW5lbCwgeyBtb2RpZmllcnMsIGV4cHJlc3Npb24gfSwgeyBldmFsdWF0ZSwgZWZmZWN0IH0pID0+IHtcbiAgICBjb25zdCBzZXR0aW5ncyA9IGV4cHJlc3Npb24gPyBldmFsdWF0ZShleHByZXNzaW9uKSA6IHt9O1xuICAgIGNvbnN0IGNvbmZpZyA9IG1vZGlmaWVycy5sZW5ndGggPiAwID8gYnVpbGREaXJlY3RpdmVDb25maWdGcm9tTW9kaWZpZXJzKG1vZGlmaWVycywgc2V0dGluZ3MpIDoge307XG4gICAgbGV0IGNsZWFudXAgPSBudWxsO1xuICAgIGlmIChjb25maWcuZmxvYXQuc3RyYXRlZ3kgPT0gXCJmaXhlZFwiKSB7XG4gICAgICBwYW5lbC5zdHlsZS5wb3NpdGlvbiA9IFwiZml4ZWRcIjtcbiAgICB9XG4gICAgY29uc3QgY2xpY2tBd2F5ID0gKGV2ZW50KSA9PiBwYW5lbC5wYXJlbnRFbGVtZW50ICYmICFwYW5lbC5wYXJlbnRFbGVtZW50LmNsb3Nlc3QoXCJbeC1kYXRhXVwiKS5jb250YWlucyhldmVudC50YXJnZXQpID8gcGFuZWwuY2xvc2UoKSA6IG51bGw7XG4gICAgY29uc3Qga2V5RXNjYXBlID0gKGV2ZW50KSA9PiBldmVudC5rZXkgPT09IFwiRXNjYXBlXCIgPyBwYW5lbC5jbG9zZSgpIDogbnVsbDtcbiAgICBjb25zdCByZWZOYW1lID0gcGFuZWwuZ2V0QXR0cmlidXRlKFwieC1yZWZcIik7XG4gICAgY29uc3QgY29tcG9uZW50ID0gcGFuZWwucGFyZW50RWxlbWVudC5jbG9zZXN0KFwiW3gtZGF0YV1cIik7XG4gICAgY29uc3QgYXRUcmlnZ2VyID0gY29tcG9uZW50LnF1ZXJ5U2VsZWN0b3JBbGwoYFtcXFxcQGNsaWNrXj1cIiRyZWZzLiR7cmVmTmFtZX1cIl1gKTtcbiAgICBjb25zdCB4VHJpZ2dlciA9IGNvbXBvbmVudC5xdWVyeVNlbGVjdG9yQWxsKGBbeC1vblxcXFw6Y2xpY2tePVwiJHJlZnMuJHtyZWZOYW1lfVwiXWApO1xuICAgIHBhbmVsLnN0eWxlLnNldFByb3BlcnR5KFwiZGlzcGxheVwiLCBcIm5vbmVcIik7XG4gICAgc2V0dXBBMTF5KGNvbXBvbmVudCwgWy4uLmF0VHJpZ2dlciwgLi4ueFRyaWdnZXJdWzBdLCBwYW5lbCk7XG4gICAgcGFuZWwuX3hfaXNTaG93biA9IGZhbHNlO1xuICAgIHBhbmVsLnRyaWdnZXIgPSBudWxsO1xuICAgIGlmICghcGFuZWwuX3hfZG9IaWRlKVxuICAgICAgcGFuZWwuX3hfZG9IaWRlID0gKCkgPT4ge1xuICAgICAgICBtdXRhdGVEb20oKCkgPT4ge1xuICAgICAgICAgIHBhbmVsLnN0eWxlLnNldFByb3BlcnR5KFwiZGlzcGxheVwiLCBcIm5vbmVcIiwgbW9kaWZpZXJzLmluY2x1ZGVzKFwiaW1wb3J0YW50XCIpID8gXCJpbXBvcnRhbnRcIiA6IHZvaWQgMCk7XG4gICAgICAgIH0pO1xuICAgICAgfTtcbiAgICBpZiAoIXBhbmVsLl94X2RvU2hvdylcbiAgICAgIHBhbmVsLl94X2RvU2hvdyA9ICgpID0+IHtcbiAgICAgICAgbXV0YXRlRG9tKCgpID0+IHtcbiAgICAgICAgICBwYW5lbC5zdHlsZS5zZXRQcm9wZXJ0eShcImRpc3BsYXlcIiwgXCJibG9ja1wiLCBtb2RpZmllcnMuaW5jbHVkZXMoXCJpbXBvcnRhbnRcIikgPyBcImltcG9ydGFudFwiIDogdm9pZCAwKTtcbiAgICAgICAgfSk7XG4gICAgICB9O1xuICAgIGxldCBoaWRlMiA9ICgpID0+IHtcbiAgICAgIHBhbmVsLl94X2RvSGlkZSgpO1xuICAgICAgcGFuZWwuX3hfaXNTaG93biA9IGZhbHNlO1xuICAgIH07XG4gICAgbGV0IHNob3cgPSAoKSA9PiB7XG4gICAgICBwYW5lbC5feF9kb1Nob3coKTtcbiAgICAgIHBhbmVsLl94X2lzU2hvd24gPSB0cnVlO1xuICAgIH07XG4gICAgbGV0IGNsaWNrQXdheUNvbXBhdGlibGVTaG93ID0gKCkgPT4gc2V0VGltZW91dChzaG93KTtcbiAgICBsZXQgdG9nZ2xlID0gb25jZShcbiAgICAgICh2YWx1ZSkgPT4gdmFsdWUgPyBzaG93KCkgOiBoaWRlMigpLFxuICAgICAgKHZhbHVlKSA9PiB7XG4gICAgICAgIGlmICh0eXBlb2YgcGFuZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgICAgICAgcGFuZWwuX3hfdG9nZ2xlQW5kQ2FzY2FkZVdpdGhUcmFuc2l0aW9ucyhwYW5lbCwgdmFsdWUsIHNob3csIGhpZGUyKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICB2YWx1ZSA/IGNsaWNrQXdheUNvbXBhdGlibGVTaG93KCkgOiBoaWRlMigpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgKTtcbiAgICBsZXQgb2xkVmFsdWU7XG4gICAgbGV0IGZpcnN0VGltZSA9IHRydWU7XG4gICAgZWZmZWN0KFxuICAgICAgKCkgPT4gZXZhbHVhdGUoKHZhbHVlKSA9PiB7XG4gICAgICAgIGlmICghZmlyc3RUaW1lICYmIHZhbHVlID09PSBvbGRWYWx1ZSlcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJpbW1lZGlhdGVcIikpXG4gICAgICAgICAgdmFsdWUgPyBjbGlja0F3YXlDb21wYXRpYmxlU2hvdygpIDogaGlkZTIoKTtcbiAgICAgICAgdG9nZ2xlKHZhbHVlKTtcbiAgICAgICAgb2xkVmFsdWUgPSB2YWx1ZTtcbiAgICAgICAgZmlyc3RUaW1lID0gZmFsc2U7XG4gICAgICB9KVxuICAgICk7XG4gICAgcGFuZWwub3BlbiA9IGFzeW5jIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICBwYW5lbC50cmlnZ2VyID0gZXZlbnQuY3VycmVudFRhcmdldCA/IGV2ZW50LmN1cnJlbnRUYXJnZXQgOiBldmVudDtcbiAgICAgIHRvZ2dsZSh0cnVlKTtcbiAgICAgIHBhbmVsLnRyaWdnZXIuc2V0QXR0cmlidXRlKFwiYXJpYS1leHBhbmRlZFwiLCB0cnVlKTtcbiAgICAgIGlmIChjb25maWcuY29tcG9uZW50LnRyYXApXG4gICAgICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcIngtdHJhcFwiLCB0cnVlKTtcbiAgICAgIGNsZWFudXAgPSBhdXRvVXBkYXRlKHBhbmVsLnRyaWdnZXIsIHBhbmVsLCAoKSA9PiB7XG4gICAgICAgIGNvbXB1dGVQb3NpdGlvbjIocGFuZWwudHJpZ2dlciwgcGFuZWwsIGNvbmZpZy5mbG9hdCkudGhlbigoeyBtaWRkbGV3YXJlRGF0YSwgcGxhY2VtZW50LCB4LCB5IH0pID0+IHtcbiAgICAgICAgICBpZiAobWlkZGxld2FyZURhdGEuYXJyb3cpIHtcbiAgICAgICAgICAgIGNvbnN0IGF4ID0gbWlkZGxld2FyZURhdGEuYXJyb3c/Lng7XG4gICAgICAgICAgICBjb25zdCBheSA9IG1pZGRsZXdhcmVEYXRhLmFycm93Py55O1xuICAgICAgICAgICAgY29uc3QgYUVsID0gY29uZmlnLmZsb2F0Lm1pZGRsZXdhcmUuZmlsdGVyKChtaWRkbGV3YXJlKSA9PiBtaWRkbGV3YXJlLm5hbWUgPT0gXCJhcnJvd1wiKVswXS5vcHRpb25zLmVsZW1lbnQ7XG4gICAgICAgICAgICBjb25zdCBzdGF0aWNTaWRlID0ge1xuICAgICAgICAgICAgICB0b3A6IFwiYm90dG9tXCIsXG4gICAgICAgICAgICAgIHJpZ2h0OiBcImxlZnRcIixcbiAgICAgICAgICAgICAgYm90dG9tOiBcInRvcFwiLFxuICAgICAgICAgICAgICBsZWZ0OiBcInJpZ2h0XCJcbiAgICAgICAgICAgIH1bcGxhY2VtZW50LnNwbGl0KFwiLVwiKVswXV07XG4gICAgICAgICAgICBPYmplY3QuYXNzaWduKGFFbC5zdHlsZSwge1xuICAgICAgICAgICAgICBsZWZ0OiBheCAhPSBudWxsID8gYCR7YXh9cHhgIDogXCJcIixcbiAgICAgICAgICAgICAgdG9wOiBheSAhPSBudWxsID8gYCR7YXl9cHhgIDogXCJcIixcbiAgICAgICAgICAgICAgcmlnaHQ6IFwiXCIsXG4gICAgICAgICAgICAgIGJvdHRvbTogXCJcIixcbiAgICAgICAgICAgICAgW3N0YXRpY1NpZGVdOiBcIi00cHhcIlxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfVxuICAgICAgICAgIGlmIChtaWRkbGV3YXJlRGF0YS5oaWRlKSB7XG4gICAgICAgICAgICBjb25zdCB7IHJlZmVyZW5jZUhpZGRlbiB9ID0gbWlkZGxld2FyZURhdGEuaGlkZTtcbiAgICAgICAgICAgIE9iamVjdC5hc3NpZ24ocGFuZWwuc3R5bGUsIHtcbiAgICAgICAgICAgICAgdmlzaWJpbGl0eTogcmVmZXJlbmNlSGlkZGVuID8gXCJoaWRkZW5cIiA6IFwidmlzaWJsZVwiXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG4gICAgICAgICAgT2JqZWN0LmFzc2lnbihwYW5lbC5zdHlsZSwge1xuICAgICAgICAgICAgbGVmdDogYCR7eH1weGAsXG4gICAgICAgICAgICB0b3A6IGAke3l9cHhgXG4gICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGNsaWNrQXdheSk7XG4gICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihcImtleWRvd25cIiwga2V5RXNjYXBlLCB0cnVlKTtcbiAgICB9O1xuICAgIHBhbmVsLmNsb3NlID0gZnVuY3Rpb24oKSB7XG4gICAgICB0b2dnbGUoZmFsc2UpO1xuICAgICAgcGFuZWwudHJpZ2dlci5zZXRBdHRyaWJ1dGUoXCJhcmlhLWV4cGFuZGVkXCIsIGZhbHNlKTtcbiAgICAgIGlmIChjb25maWcuY29tcG9uZW50LnRyYXApXG4gICAgICAgIHBhbmVsLnNldEF0dHJpYnV0ZShcIngtdHJhcFwiLCBmYWxzZSk7XG4gICAgICBjbGVhbnVwKCk7XG4gICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGNsaWNrQXdheSk7XG4gICAgICB3aW5kb3cucmVtb3ZlRXZlbnRMaXN0ZW5lcihcImtleWRvd25cIiwga2V5RXNjYXBlLCBmYWxzZSk7XG4gICAgfTtcbiAgICBwYW5lbC50b2dnbGUgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgcGFuZWwuX3hfaXNTaG93biA/IHBhbmVsLmNsb3NlKCkgOiBwYW5lbC5vcGVuKGV2ZW50KTtcbiAgICB9O1xuICB9KTtcbn1cblxuLy8gYnVpbGRzL21vZHVsZS5qc1xudmFyIG1vZHVsZV9kZWZhdWx0ID0gc3JjX2RlZmF1bHQ7XG5leHBvcnQge1xuICBtb2R1bGVfZGVmYXVsdCBhcyBkZWZhdWx0XG59O1xuIiwgIi8vIHNyYy9jb3JlL2FscGluZS1sYXp5LWxvYWQtYXNzZXRzLmpzXG5mdW5jdGlvbiBhbHBpbmVfbGF6eV9sb2FkX2Fzc2V0c19kZWZhdWx0KEFscGluZSkge1xuICBBbHBpbmUuZGlyZWN0aXZlKFwibG9hZC1jc3NcIiwgKGVsLCB7IGV4cHJlc3Npb24gfSwgeyBldmFsdWF0ZSB9KSA9PiB7XG4gICAgdHJ5IHtcbiAgICAgIGNvbnN0IHBhdGhzID0gZXZhbHVhdGUoZXhwcmVzc2lvbik7XG4gICAgICBwYXRocz8uZm9yRWFjaCgocGF0aCkgPT4ge1xuICAgICAgICBpZiAoZG9jdW1lbnQucXVlcnlTZWxlY3RvcihgbGlua1tocmVmPVwiJHtwYXRofVwiXWApKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGNvbnN0IGxpbmsgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwibGlua1wiKTtcbiAgICAgICAgbGluay50eXBlID0gXCJ0ZXh0L2Nzc1wiO1xuICAgICAgICBsaW5rLnJlbCA9IFwic3R5bGVzaGVldFwiO1xuICAgICAgICBsaW5rLmhyZWYgPSBwYXRoO1xuICAgICAgICBjb25zdCBtZWRpYUF0dHIgPSBlbC5hdHRyaWJ1dGVzPy5tZWRpYT8udmFsdWU7XG4gICAgICAgIGlmIChtZWRpYUF0dHIpIHtcbiAgICAgICAgICBsaW5rLm1lZGlhID0gbWVkaWFBdHRyO1xuICAgICAgICB9XG4gICAgICAgIGNvbnN0IGhlYWQgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZShcImhlYWRcIilbMF07XG4gICAgICAgIGhlYWQuYXBwZW5kQ2hpbGQobGluayk7XG4gICAgICB9KTtcbiAgICB9IGNhdGNoIChlcnJvcikge1xuICAgICAgY29uc29sZS5lcnJvcihlcnJvcik7XG4gICAgfVxuICB9KTtcbiAgQWxwaW5lLmRpcmVjdGl2ZShcImxvYWQtanNcIiwgKGVsLCB7IGV4cHJlc3Npb24gfSwgeyBldmFsdWF0ZSB9KSA9PiB7XG4gICAgdHJ5IHtcbiAgICAgIGNvbnN0IHBhdGhzID0gZXZhbHVhdGUoZXhwcmVzc2lvbik7XG4gICAgICBwYXRocz8uZm9yRWFjaCgocGF0aCkgPT4ge1xuICAgICAgICBpZiAoZG9jdW1lbnQucXVlcnlTZWxlY3Rvcihgc2NyaXB0W3NyYz1cIiR7cGF0aH1cIl1gKSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICBjb25zdCBzY3JpcHQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xuICAgICAgICBzY3JpcHQuc3JjID0gcGF0aDtcbiAgICAgICAgY29uc3QgaGVhZCA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlUYWdOYW1lKFwiaGVhZFwiKVswXTtcbiAgICAgICAgaGVhZC5hcHBlbmRDaGlsZChzY3JpcHQpO1xuICAgICAgfSk7XG4gICAgfSBjYXRjaCAoZXJyb3IpIHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoZXJyb3IpO1xuICAgIH1cbiAgfSk7XG59XG5cbi8vIHNyYy9tb2R1bGUuanNcbnZhciBtb2R1bGVfZGVmYXVsdCA9IGFscGluZV9sYXp5X2xvYWRfYXNzZXRzX2RlZmF1bHQ7XG5leHBvcnQge1xuICBtb2R1bGVfZGVmYXVsdCBhcyBkZWZhdWx0XG59O1xuIiwgIi8vIG5vZGVfbW9kdWxlcy90YWJiYWJsZS9kaXN0L2luZGV4LmVzbS5qc1xuLyohXG4qIHRhYmJhYmxlIDUuMi4xXG4qIEBsaWNlbnNlIE1JVCwgaHR0cHM6Ly9naXRodWIuY29tL2ZvY3VzLXRyYXAvdGFiYmFibGUvYmxvYi9tYXN0ZXIvTElDRU5TRVxuKi9cbnZhciBjYW5kaWRhdGVTZWxlY3RvcnMgPSBbXCJpbnB1dFwiLCBcInNlbGVjdFwiLCBcInRleHRhcmVhXCIsIFwiYVtocmVmXVwiLCBcImJ1dHRvblwiLCBcIlt0YWJpbmRleF1cIiwgXCJhdWRpb1tjb250cm9sc11cIiwgXCJ2aWRlb1tjb250cm9sc11cIiwgJ1tjb250ZW50ZWRpdGFibGVdOm5vdChbY29udGVudGVkaXRhYmxlPVwiZmFsc2VcIl0pJywgXCJkZXRhaWxzPnN1bW1hcnk6Zmlyc3Qtb2YtdHlwZVwiLCBcImRldGFpbHNcIl07XG52YXIgY2FuZGlkYXRlU2VsZWN0b3IgPSAvKiBAX19QVVJFX18gKi8gY2FuZGlkYXRlU2VsZWN0b3JzLmpvaW4oXCIsXCIpO1xudmFyIG1hdGNoZXMgPSB0eXBlb2YgRWxlbWVudCA9PT0gXCJ1bmRlZmluZWRcIiA/IGZ1bmN0aW9uKCkge1xufSA6IEVsZW1lbnQucHJvdG90eXBlLm1hdGNoZXMgfHwgRWxlbWVudC5wcm90b3R5cGUubXNNYXRjaGVzU2VsZWN0b3IgfHwgRWxlbWVudC5wcm90b3R5cGUud2Via2l0TWF0Y2hlc1NlbGVjdG9yO1xudmFyIGdldENhbmRpZGF0ZXMgPSBmdW5jdGlvbiBnZXRDYW5kaWRhdGVzMihlbCwgaW5jbHVkZUNvbnRhaW5lciwgZmlsdGVyKSB7XG4gIHZhciBjYW5kaWRhdGVzID0gQXJyYXkucHJvdG90eXBlLnNsaWNlLmFwcGx5KGVsLnF1ZXJ5U2VsZWN0b3JBbGwoY2FuZGlkYXRlU2VsZWN0b3IpKTtcbiAgaWYgKGluY2x1ZGVDb250YWluZXIgJiYgbWF0Y2hlcy5jYWxsKGVsLCBjYW5kaWRhdGVTZWxlY3RvcikpIHtcbiAgICBjYW5kaWRhdGVzLnVuc2hpZnQoZWwpO1xuICB9XG4gIGNhbmRpZGF0ZXMgPSBjYW5kaWRhdGVzLmZpbHRlcihmaWx0ZXIpO1xuICByZXR1cm4gY2FuZGlkYXRlcztcbn07XG52YXIgaXNDb250ZW50RWRpdGFibGUgPSBmdW5jdGlvbiBpc0NvbnRlbnRFZGl0YWJsZTIobm9kZSkge1xuICByZXR1cm4gbm9kZS5jb250ZW50RWRpdGFibGUgPT09IFwidHJ1ZVwiO1xufTtcbnZhciBnZXRUYWJpbmRleCA9IGZ1bmN0aW9uIGdldFRhYmluZGV4Mihub2RlKSB7XG4gIHZhciB0YWJpbmRleEF0dHIgPSBwYXJzZUludChub2RlLmdldEF0dHJpYnV0ZShcInRhYmluZGV4XCIpLCAxMCk7XG4gIGlmICghaXNOYU4odGFiaW5kZXhBdHRyKSkge1xuICAgIHJldHVybiB0YWJpbmRleEF0dHI7XG4gIH1cbiAgaWYgKGlzQ29udGVudEVkaXRhYmxlKG5vZGUpKSB7XG4gICAgcmV0dXJuIDA7XG4gIH1cbiAgaWYgKChub2RlLm5vZGVOYW1lID09PSBcIkFVRElPXCIgfHwgbm9kZS5ub2RlTmFtZSA9PT0gXCJWSURFT1wiIHx8IG5vZGUubm9kZU5hbWUgPT09IFwiREVUQUlMU1wiKSAmJiBub2RlLmdldEF0dHJpYnV0ZShcInRhYmluZGV4XCIpID09PSBudWxsKSB7XG4gICAgcmV0dXJuIDA7XG4gIH1cbiAgcmV0dXJuIG5vZGUudGFiSW5kZXg7XG59O1xudmFyIHNvcnRPcmRlcmVkVGFiYmFibGVzID0gZnVuY3Rpb24gc29ydE9yZGVyZWRUYWJiYWJsZXMyKGEsIGIpIHtcbiAgcmV0dXJuIGEudGFiSW5kZXggPT09IGIudGFiSW5kZXggPyBhLmRvY3VtZW50T3JkZXIgLSBiLmRvY3VtZW50T3JkZXIgOiBhLnRhYkluZGV4IC0gYi50YWJJbmRleDtcbn07XG52YXIgaXNJbnB1dCA9IGZ1bmN0aW9uIGlzSW5wdXQyKG5vZGUpIHtcbiAgcmV0dXJuIG5vZGUudGFnTmFtZSA9PT0gXCJJTlBVVFwiO1xufTtcbnZhciBpc0hpZGRlbklucHV0ID0gZnVuY3Rpb24gaXNIaWRkZW5JbnB1dDIobm9kZSkge1xuICByZXR1cm4gaXNJbnB1dChub2RlKSAmJiBub2RlLnR5cGUgPT09IFwiaGlkZGVuXCI7XG59O1xudmFyIGlzRGV0YWlsc1dpdGhTdW1tYXJ5ID0gZnVuY3Rpb24gaXNEZXRhaWxzV2l0aFN1bW1hcnkyKG5vZGUpIHtcbiAgdmFyIHIgPSBub2RlLnRhZ05hbWUgPT09IFwiREVUQUlMU1wiICYmIEFycmF5LnByb3RvdHlwZS5zbGljZS5hcHBseShub2RlLmNoaWxkcmVuKS5zb21lKGZ1bmN0aW9uKGNoaWxkKSB7XG4gICAgcmV0dXJuIGNoaWxkLnRhZ05hbWUgPT09IFwiU1VNTUFSWVwiO1xuICB9KTtcbiAgcmV0dXJuIHI7XG59O1xudmFyIGdldENoZWNrZWRSYWRpbyA9IGZ1bmN0aW9uIGdldENoZWNrZWRSYWRpbzIobm9kZXMsIGZvcm0pIHtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBub2Rlcy5sZW5ndGg7IGkrKykge1xuICAgIGlmIChub2Rlc1tpXS5jaGVja2VkICYmIG5vZGVzW2ldLmZvcm0gPT09IGZvcm0pIHtcbiAgICAgIHJldHVybiBub2Rlc1tpXTtcbiAgICB9XG4gIH1cbn07XG52YXIgaXNUYWJiYWJsZVJhZGlvID0gZnVuY3Rpb24gaXNUYWJiYWJsZVJhZGlvMihub2RlKSB7XG4gIGlmICghbm9kZS5uYW1lKSB7XG4gICAgcmV0dXJuIHRydWU7XG4gIH1cbiAgdmFyIHJhZGlvU2NvcGUgPSBub2RlLmZvcm0gfHwgbm9kZS5vd25lckRvY3VtZW50O1xuICB2YXIgcXVlcnlSYWRpb3MgPSBmdW5jdGlvbiBxdWVyeVJhZGlvczIobmFtZSkge1xuICAgIHJldHVybiByYWRpb1Njb3BlLnF1ZXJ5U2VsZWN0b3JBbGwoJ2lucHV0W3R5cGU9XCJyYWRpb1wiXVtuYW1lPVwiJyArIG5hbWUgKyAnXCJdJyk7XG4gIH07XG4gIHZhciByYWRpb1NldDtcbiAgaWYgKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgJiYgdHlwZW9mIHdpbmRvdy5DU1MgIT09IFwidW5kZWZpbmVkXCIgJiYgdHlwZW9mIHdpbmRvdy5DU1MuZXNjYXBlID09PSBcImZ1bmN0aW9uXCIpIHtcbiAgICByYWRpb1NldCA9IHF1ZXJ5UmFkaW9zKHdpbmRvdy5DU1MuZXNjYXBlKG5vZGUubmFtZSkpO1xuICB9IGVsc2Uge1xuICAgIHRyeSB7XG4gICAgICByYWRpb1NldCA9IHF1ZXJ5UmFkaW9zKG5vZGUubmFtZSk7XG4gICAgfSBjYXRjaCAoZXJyKSB7XG4gICAgICBjb25zb2xlLmVycm9yKFwiTG9va3MgbGlrZSB5b3UgaGF2ZSBhIHJhZGlvIGJ1dHRvbiB3aXRoIGEgbmFtZSBhdHRyaWJ1dGUgY29udGFpbmluZyBpbnZhbGlkIENTUyBzZWxlY3RvciBjaGFyYWN0ZXJzIGFuZCBuZWVkIHRoZSBDU1MuZXNjYXBlIHBvbHlmaWxsOiAlc1wiLCBlcnIubWVzc2FnZSk7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG4gIHZhciBjaGVja2VkID0gZ2V0Q2hlY2tlZFJhZGlvKHJhZGlvU2V0LCBub2RlLmZvcm0pO1xuICByZXR1cm4gIWNoZWNrZWQgfHwgY2hlY2tlZCA9PT0gbm9kZTtcbn07XG52YXIgaXNSYWRpbyA9IGZ1bmN0aW9uIGlzUmFkaW8yKG5vZGUpIHtcbiAgcmV0dXJuIGlzSW5wdXQobm9kZSkgJiYgbm9kZS50eXBlID09PSBcInJhZGlvXCI7XG59O1xudmFyIGlzTm9uVGFiYmFibGVSYWRpbyA9IGZ1bmN0aW9uIGlzTm9uVGFiYmFibGVSYWRpbzIobm9kZSkge1xuICByZXR1cm4gaXNSYWRpbyhub2RlKSAmJiAhaXNUYWJiYWJsZVJhZGlvKG5vZGUpO1xufTtcbnZhciBpc0hpZGRlbiA9IGZ1bmN0aW9uIGlzSGlkZGVuMihub2RlLCBkaXNwbGF5Q2hlY2spIHtcbiAgaWYgKGdldENvbXB1dGVkU3R5bGUobm9kZSkudmlzaWJpbGl0eSA9PT0gXCJoaWRkZW5cIikge1xuICAgIHJldHVybiB0cnVlO1xuICB9XG4gIHZhciBpc0RpcmVjdFN1bW1hcnkgPSBtYXRjaGVzLmNhbGwobm9kZSwgXCJkZXRhaWxzPnN1bW1hcnk6Zmlyc3Qtb2YtdHlwZVwiKTtcbiAgdmFyIG5vZGVVbmRlckRldGFpbHMgPSBpc0RpcmVjdFN1bW1hcnkgPyBub2RlLnBhcmVudEVsZW1lbnQgOiBub2RlO1xuICBpZiAobWF0Y2hlcy5jYWxsKG5vZGVVbmRlckRldGFpbHMsIFwiZGV0YWlsczpub3QoW29wZW5dKSAqXCIpKSB7XG4gICAgcmV0dXJuIHRydWU7XG4gIH1cbiAgaWYgKCFkaXNwbGF5Q2hlY2sgfHwgZGlzcGxheUNoZWNrID09PSBcImZ1bGxcIikge1xuICAgIHdoaWxlIChub2RlKSB7XG4gICAgICBpZiAoZ2V0Q29tcHV0ZWRTdHlsZShub2RlKS5kaXNwbGF5ID09PSBcIm5vbmVcIikge1xuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgIH1cbiAgICAgIG5vZGUgPSBub2RlLnBhcmVudEVsZW1lbnQ7XG4gICAgfVxuICB9IGVsc2UgaWYgKGRpc3BsYXlDaGVjayA9PT0gXCJub24temVyby1hcmVhXCIpIHtcbiAgICB2YXIgX25vZGUkZ2V0Qm91bmRpbmdDbGllID0gbm9kZS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKSwgd2lkdGggPSBfbm9kZSRnZXRCb3VuZGluZ0NsaWUud2lkdGgsIGhlaWdodCA9IF9ub2RlJGdldEJvdW5kaW5nQ2xpZS5oZWlnaHQ7XG4gICAgcmV0dXJuIHdpZHRoID09PSAwICYmIGhlaWdodCA9PT0gMDtcbiAgfVxuICByZXR1cm4gZmFsc2U7XG59O1xudmFyIGlzRGlzYWJsZWRGcm9tRmllbGRzZXQgPSBmdW5jdGlvbiBpc0Rpc2FibGVkRnJvbUZpZWxkc2V0Mihub2RlKSB7XG4gIGlmIChpc0lucHV0KG5vZGUpIHx8IG5vZGUudGFnTmFtZSA9PT0gXCJTRUxFQ1RcIiB8fCBub2RlLnRhZ05hbWUgPT09IFwiVEVYVEFSRUFcIiB8fCBub2RlLnRhZ05hbWUgPT09IFwiQlVUVE9OXCIpIHtcbiAgICB2YXIgcGFyZW50Tm9kZSA9IG5vZGUucGFyZW50RWxlbWVudDtcbiAgICB3aGlsZSAocGFyZW50Tm9kZSkge1xuICAgICAgaWYgKHBhcmVudE5vZGUudGFnTmFtZSA9PT0gXCJGSUVMRFNFVFwiICYmIHBhcmVudE5vZGUuZGlzYWJsZWQpIHtcbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCBwYXJlbnROb2RlLmNoaWxkcmVuLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgdmFyIGNoaWxkID0gcGFyZW50Tm9kZS5jaGlsZHJlbi5pdGVtKGkpO1xuICAgICAgICAgIGlmIChjaGlsZC50YWdOYW1lID09PSBcIkxFR0VORFwiKSB7XG4gICAgICAgICAgICBpZiAoY2hpbGQuY29udGFpbnMobm9kZSkpIHtcbiAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgfVxuICAgICAgcGFyZW50Tm9kZSA9IHBhcmVudE5vZGUucGFyZW50RWxlbWVudDtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIGZhbHNlO1xufTtcbnZhciBpc05vZGVNYXRjaGluZ1NlbGVjdG9yRm9jdXNhYmxlID0gZnVuY3Rpb24gaXNOb2RlTWF0Y2hpbmdTZWxlY3RvckZvY3VzYWJsZTIob3B0aW9ucywgbm9kZSkge1xuICBpZiAobm9kZS5kaXNhYmxlZCB8fCBpc0hpZGRlbklucHV0KG5vZGUpIHx8IGlzSGlkZGVuKG5vZGUsIG9wdGlvbnMuZGlzcGxheUNoZWNrKSB8fCBpc0RldGFpbHNXaXRoU3VtbWFyeShub2RlKSB8fCBpc0Rpc2FibGVkRnJvbUZpZWxkc2V0KG5vZGUpKSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG4gIHJldHVybiB0cnVlO1xufTtcbnZhciBpc05vZGVNYXRjaGluZ1NlbGVjdG9yVGFiYmFibGUgPSBmdW5jdGlvbiBpc05vZGVNYXRjaGluZ1NlbGVjdG9yVGFiYmFibGUyKG9wdGlvbnMsIG5vZGUpIHtcbiAgaWYgKCFpc05vZGVNYXRjaGluZ1NlbGVjdG9yRm9jdXNhYmxlKG9wdGlvbnMsIG5vZGUpIHx8IGlzTm9uVGFiYmFibGVSYWRpbyhub2RlKSB8fCBnZXRUYWJpbmRleChub2RlKSA8IDApIHtcbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cbiAgcmV0dXJuIHRydWU7XG59O1xudmFyIHRhYmJhYmxlID0gZnVuY3Rpb24gdGFiYmFibGUyKGVsLCBvcHRpb25zKSB7XG4gIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuICB2YXIgcmVndWxhclRhYmJhYmxlcyA9IFtdO1xuICB2YXIgb3JkZXJlZFRhYmJhYmxlcyA9IFtdO1xuICB2YXIgY2FuZGlkYXRlcyA9IGdldENhbmRpZGF0ZXMoZWwsIG9wdGlvbnMuaW5jbHVkZUNvbnRhaW5lciwgaXNOb2RlTWF0Y2hpbmdTZWxlY3RvclRhYmJhYmxlLmJpbmQobnVsbCwgb3B0aW9ucykpO1xuICBjYW5kaWRhdGVzLmZvckVhY2goZnVuY3Rpb24oY2FuZGlkYXRlLCBpKSB7XG4gICAgdmFyIGNhbmRpZGF0ZVRhYmluZGV4ID0gZ2V0VGFiaW5kZXgoY2FuZGlkYXRlKTtcbiAgICBpZiAoY2FuZGlkYXRlVGFiaW5kZXggPT09IDApIHtcbiAgICAgIHJlZ3VsYXJUYWJiYWJsZXMucHVzaChjYW5kaWRhdGUpO1xuICAgIH0gZWxzZSB7XG4gICAgICBvcmRlcmVkVGFiYmFibGVzLnB1c2goe1xuICAgICAgICBkb2N1bWVudE9yZGVyOiBpLFxuICAgICAgICB0YWJJbmRleDogY2FuZGlkYXRlVGFiaW5kZXgsXG4gICAgICAgIG5vZGU6IGNhbmRpZGF0ZVxuICAgICAgfSk7XG4gICAgfVxuICB9KTtcbiAgdmFyIHRhYmJhYmxlTm9kZXMgPSBvcmRlcmVkVGFiYmFibGVzLnNvcnQoc29ydE9yZGVyZWRUYWJiYWJsZXMpLm1hcChmdW5jdGlvbihhKSB7XG4gICAgcmV0dXJuIGEubm9kZTtcbiAgfSkuY29uY2F0KHJlZ3VsYXJUYWJiYWJsZXMpO1xuICByZXR1cm4gdGFiYmFibGVOb2Rlcztcbn07XG52YXIgZm9jdXNhYmxlID0gZnVuY3Rpb24gZm9jdXNhYmxlMihlbCwgb3B0aW9ucykge1xuICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcbiAgdmFyIGNhbmRpZGF0ZXMgPSBnZXRDYW5kaWRhdGVzKGVsLCBvcHRpb25zLmluY2x1ZGVDb250YWluZXIsIGlzTm9kZU1hdGNoaW5nU2VsZWN0b3JGb2N1c2FibGUuYmluZChudWxsLCBvcHRpb25zKSk7XG4gIHJldHVybiBjYW5kaWRhdGVzO1xufTtcbnZhciBmb2N1c2FibGVDYW5kaWRhdGVTZWxlY3RvciA9IC8qIEBfX1BVUkVfXyAqLyBjYW5kaWRhdGVTZWxlY3RvcnMuY29uY2F0KFwiaWZyYW1lXCIpLmpvaW4oXCIsXCIpO1xudmFyIGlzRm9jdXNhYmxlID0gZnVuY3Rpb24gaXNGb2N1c2FibGUyKG5vZGUsIG9wdGlvbnMpIHtcbiAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG4gIGlmICghbm9kZSkge1xuICAgIHRocm93IG5ldyBFcnJvcihcIk5vIG5vZGUgcHJvdmlkZWRcIik7XG4gIH1cbiAgaWYgKG1hdGNoZXMuY2FsbChub2RlLCBmb2N1c2FibGVDYW5kaWRhdGVTZWxlY3RvcikgPT09IGZhbHNlKSB7XG4gICAgcmV0dXJuIGZhbHNlO1xuICB9XG4gIHJldHVybiBpc05vZGVNYXRjaGluZ1NlbGVjdG9yRm9jdXNhYmxlKG9wdGlvbnMsIG5vZGUpO1xufTtcblxuLy8gbm9kZV9tb2R1bGVzL2ZvY3VzLXRyYXAvZGlzdC9mb2N1cy10cmFwLmVzbS5qc1xuLyohXG4qIGZvY3VzLXRyYXAgNi42LjFcbiogQGxpY2Vuc2UgTUlULCBodHRwczovL2dpdGh1Yi5jb20vZm9jdXMtdHJhcC9mb2N1cy10cmFwL2Jsb2IvbWFzdGVyL0xJQ0VOU0VcbiovXG5mdW5jdGlvbiBvd25LZXlzKG9iamVjdCwgZW51bWVyYWJsZU9ubHkpIHtcbiAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhvYmplY3QpO1xuICBpZiAoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scykge1xuICAgIHZhciBzeW1ib2xzID0gT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scyhvYmplY3QpO1xuICAgIGlmIChlbnVtZXJhYmxlT25seSkge1xuICAgICAgc3ltYm9scyA9IHN5bWJvbHMuZmlsdGVyKGZ1bmN0aW9uKHN5bSkge1xuICAgICAgICByZXR1cm4gT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcihvYmplY3QsIHN5bSkuZW51bWVyYWJsZTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICBrZXlzLnB1c2guYXBwbHkoa2V5cywgc3ltYm9scyk7XG4gIH1cbiAgcmV0dXJuIGtleXM7XG59XG5mdW5jdGlvbiBfb2JqZWN0U3ByZWFkMih0YXJnZXQpIHtcbiAgZm9yICh2YXIgaSA9IDE7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIHtcbiAgICB2YXIgc291cmNlID0gYXJndW1lbnRzW2ldICE9IG51bGwgPyBhcmd1bWVudHNbaV0gOiB7fTtcbiAgICBpZiAoaSAlIDIpIHtcbiAgICAgIG93bktleXMoT2JqZWN0KHNvdXJjZSksIHRydWUpLmZvckVhY2goZnVuY3Rpb24oa2V5KSB7XG4gICAgICAgIF9kZWZpbmVQcm9wZXJ0eSh0YXJnZXQsIGtleSwgc291cmNlW2tleV0pO1xuICAgICAgfSk7XG4gICAgfSBlbHNlIGlmIChPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9ycykge1xuICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnRpZXModGFyZ2V0LCBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9ycyhzb3VyY2UpKTtcbiAgICB9IGVsc2Uge1xuICAgICAgb3duS2V5cyhPYmplY3Qoc291cmNlKSkuZm9yRWFjaChmdW5jdGlvbihrZXkpIHtcbiAgICAgICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KHRhcmdldCwga2V5LCBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yKHNvdXJjZSwga2V5KSk7XG4gICAgICB9KTtcbiAgICB9XG4gIH1cbiAgcmV0dXJuIHRhcmdldDtcbn1cbmZ1bmN0aW9uIF9kZWZpbmVQcm9wZXJ0eShvYmosIGtleSwgdmFsdWUpIHtcbiAgaWYgKGtleSBpbiBvYmopIHtcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkob2JqLCBrZXksIHtcbiAgICAgIHZhbHVlLFxuICAgICAgZW51bWVyYWJsZTogdHJ1ZSxcbiAgICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZSxcbiAgICAgIHdyaXRhYmxlOiB0cnVlXG4gICAgfSk7XG4gIH0gZWxzZSB7XG4gICAgb2JqW2tleV0gPSB2YWx1ZTtcbiAgfVxuICByZXR1cm4gb2JqO1xufVxudmFyIGFjdGl2ZUZvY3VzVHJhcHMgPSBmdW5jdGlvbigpIHtcbiAgdmFyIHRyYXBRdWV1ZSA9IFtdO1xuICByZXR1cm4ge1xuICAgIGFjdGl2YXRlVHJhcDogZnVuY3Rpb24gYWN0aXZhdGVUcmFwKHRyYXApIHtcbiAgICAgIGlmICh0cmFwUXVldWUubGVuZ3RoID4gMCkge1xuICAgICAgICB2YXIgYWN0aXZlVHJhcCA9IHRyYXBRdWV1ZVt0cmFwUXVldWUubGVuZ3RoIC0gMV07XG4gICAgICAgIGlmIChhY3RpdmVUcmFwICE9PSB0cmFwKSB7XG4gICAgICAgICAgYWN0aXZlVHJhcC5wYXVzZSgpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgICB2YXIgdHJhcEluZGV4ID0gdHJhcFF1ZXVlLmluZGV4T2YodHJhcCk7XG4gICAgICBpZiAodHJhcEluZGV4ID09PSAtMSkge1xuICAgICAgICB0cmFwUXVldWUucHVzaCh0cmFwKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRyYXBRdWV1ZS5zcGxpY2UodHJhcEluZGV4LCAxKTtcbiAgICAgICAgdHJhcFF1ZXVlLnB1c2godHJhcCk7XG4gICAgICB9XG4gICAgfSxcbiAgICBkZWFjdGl2YXRlVHJhcDogZnVuY3Rpb24gZGVhY3RpdmF0ZVRyYXAodHJhcCkge1xuICAgICAgdmFyIHRyYXBJbmRleCA9IHRyYXBRdWV1ZS5pbmRleE9mKHRyYXApO1xuICAgICAgaWYgKHRyYXBJbmRleCAhPT0gLTEpIHtcbiAgICAgICAgdHJhcFF1ZXVlLnNwbGljZSh0cmFwSW5kZXgsIDEpO1xuICAgICAgfVxuICAgICAgaWYgKHRyYXBRdWV1ZS5sZW5ndGggPiAwKSB7XG4gICAgICAgIHRyYXBRdWV1ZVt0cmFwUXVldWUubGVuZ3RoIC0gMV0udW5wYXVzZSgpO1xuICAgICAgfVxuICAgIH1cbiAgfTtcbn0oKTtcbnZhciBpc1NlbGVjdGFibGVJbnB1dCA9IGZ1bmN0aW9uIGlzU2VsZWN0YWJsZUlucHV0Mihub2RlKSB7XG4gIHJldHVybiBub2RlLnRhZ05hbWUgJiYgbm9kZS50YWdOYW1lLnRvTG93ZXJDYXNlKCkgPT09IFwiaW5wdXRcIiAmJiB0eXBlb2Ygbm9kZS5zZWxlY3QgPT09IFwiZnVuY3Rpb25cIjtcbn07XG52YXIgaXNFc2NhcGVFdmVudCA9IGZ1bmN0aW9uIGlzRXNjYXBlRXZlbnQyKGUpIHtcbiAgcmV0dXJuIGUua2V5ID09PSBcIkVzY2FwZVwiIHx8IGUua2V5ID09PSBcIkVzY1wiIHx8IGUua2V5Q29kZSA9PT0gMjc7XG59O1xudmFyIGlzVGFiRXZlbnQgPSBmdW5jdGlvbiBpc1RhYkV2ZW50MihlKSB7XG4gIHJldHVybiBlLmtleSA9PT0gXCJUYWJcIiB8fCBlLmtleUNvZGUgPT09IDk7XG59O1xudmFyIGRlbGF5ID0gZnVuY3Rpb24gZGVsYXkyKGZuKSB7XG4gIHJldHVybiBzZXRUaW1lb3V0KGZuLCAwKTtcbn07XG52YXIgZmluZEluZGV4ID0gZnVuY3Rpb24gZmluZEluZGV4MihhcnIsIGZuKSB7XG4gIHZhciBpZHggPSAtMTtcbiAgYXJyLmV2ZXJ5KGZ1bmN0aW9uKHZhbHVlLCBpKSB7XG4gICAgaWYgKGZuKHZhbHVlKSkge1xuICAgICAgaWR4ID0gaTtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gICAgcmV0dXJuIHRydWU7XG4gIH0pO1xuICByZXR1cm4gaWR4O1xufTtcbnZhciB2YWx1ZU9ySGFuZGxlciA9IGZ1bmN0aW9uIHZhbHVlT3JIYW5kbGVyMih2YWx1ZSkge1xuICBmb3IgKHZhciBfbGVuID0gYXJndW1lbnRzLmxlbmd0aCwgcGFyYW1zID0gbmV3IEFycmF5KF9sZW4gPiAxID8gX2xlbiAtIDEgOiAwKSwgX2tleSA9IDE7IF9rZXkgPCBfbGVuOyBfa2V5KyspIHtcbiAgICBwYXJhbXNbX2tleSAtIDFdID0gYXJndW1lbnRzW19rZXldO1xuICB9XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT09IFwiZnVuY3Rpb25cIiA/IHZhbHVlLmFwcGx5KHZvaWQgMCwgcGFyYW1zKSA6IHZhbHVlO1xufTtcbnZhciBjcmVhdGVGb2N1c1RyYXAgPSBmdW5jdGlvbiBjcmVhdGVGb2N1c1RyYXAyKGVsZW1lbnRzLCB1c2VyT3B0aW9ucykge1xuICB2YXIgZG9jID0gZG9jdW1lbnQ7XG4gIHZhciBjb25maWcgPSBfb2JqZWN0U3ByZWFkMih7XG4gICAgcmV0dXJuRm9jdXNPbkRlYWN0aXZhdGU6IHRydWUsXG4gICAgZXNjYXBlRGVhY3RpdmF0ZXM6IHRydWUsXG4gICAgZGVsYXlJbml0aWFsRm9jdXM6IHRydWVcbiAgfSwgdXNlck9wdGlvbnMpO1xuICB2YXIgc3RhdGUgPSB7XG4gICAgY29udGFpbmVyczogW10sXG4gICAgdGFiYmFibGVHcm91cHM6IFtdLFxuICAgIG5vZGVGb2N1c2VkQmVmb3JlQWN0aXZhdGlvbjogbnVsbCxcbiAgICBtb3N0UmVjZW50bHlGb2N1c2VkTm9kZTogbnVsbCxcbiAgICBhY3RpdmU6IGZhbHNlLFxuICAgIHBhdXNlZDogZmFsc2UsXG4gICAgZGVsYXlJbml0aWFsRm9jdXNUaW1lcjogdm9pZCAwXG4gIH07XG4gIHZhciB0cmFwO1xuICB2YXIgZ2V0T3B0aW9uID0gZnVuY3Rpb24gZ2V0T3B0aW9uMihjb25maWdPdmVycmlkZU9wdGlvbnMsIG9wdGlvbk5hbWUsIGNvbmZpZ09wdGlvbk5hbWUpIHtcbiAgICByZXR1cm4gY29uZmlnT3ZlcnJpZGVPcHRpb25zICYmIGNvbmZpZ092ZXJyaWRlT3B0aW9uc1tvcHRpb25OYW1lXSAhPT0gdm9pZCAwID8gY29uZmlnT3ZlcnJpZGVPcHRpb25zW29wdGlvbk5hbWVdIDogY29uZmlnW2NvbmZpZ09wdGlvbk5hbWUgfHwgb3B0aW9uTmFtZV07XG4gIH07XG4gIHZhciBjb250YWluZXJzQ29udGFpbiA9IGZ1bmN0aW9uIGNvbnRhaW5lcnNDb250YWluMihlbGVtZW50KSB7XG4gICAgcmV0dXJuIHN0YXRlLmNvbnRhaW5lcnMuc29tZShmdW5jdGlvbihjb250YWluZXIpIHtcbiAgICAgIHJldHVybiBjb250YWluZXIuY29udGFpbnMoZWxlbWVudCk7XG4gICAgfSk7XG4gIH07XG4gIHZhciBnZXROb2RlRm9yT3B0aW9uID0gZnVuY3Rpb24gZ2V0Tm9kZUZvck9wdGlvbjIob3B0aW9uTmFtZSkge1xuICAgIHZhciBvcHRpb25WYWx1ZSA9IGNvbmZpZ1tvcHRpb25OYW1lXTtcbiAgICBpZiAoIW9wdGlvblZhbHVlKSB7XG4gICAgICByZXR1cm4gbnVsbDtcbiAgICB9XG4gICAgdmFyIG5vZGUgPSBvcHRpb25WYWx1ZTtcbiAgICBpZiAodHlwZW9mIG9wdGlvblZhbHVlID09PSBcInN0cmluZ1wiKSB7XG4gICAgICBub2RlID0gZG9jLnF1ZXJ5U2VsZWN0b3Iob3B0aW9uVmFsdWUpO1xuICAgICAgaWYgKCFub2RlKSB7XG4gICAgICAgIHRocm93IG5ldyBFcnJvcihcImBcIi5jb25jYXQob3B0aW9uTmFtZSwgXCJgIHJlZmVycyB0byBubyBrbm93biBub2RlXCIpKTtcbiAgICAgIH1cbiAgICB9XG4gICAgaWYgKHR5cGVvZiBvcHRpb25WYWx1ZSA9PT0gXCJmdW5jdGlvblwiKSB7XG4gICAgICBub2RlID0gb3B0aW9uVmFsdWUoKTtcbiAgICAgIGlmICghbm9kZSkge1xuICAgICAgICB0aHJvdyBuZXcgRXJyb3IoXCJgXCIuY29uY2F0KG9wdGlvbk5hbWUsIFwiYCBkaWQgbm90IHJldHVybiBhIG5vZGVcIikpO1xuICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gbm9kZTtcbiAgfTtcbiAgdmFyIGdldEluaXRpYWxGb2N1c05vZGUgPSBmdW5jdGlvbiBnZXRJbml0aWFsRm9jdXNOb2RlMigpIHtcbiAgICB2YXIgbm9kZTtcbiAgICBpZiAoZ2V0T3B0aW9uKHt9LCBcImluaXRpYWxGb2N1c1wiKSA9PT0gZmFsc2UpIHtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG4gICAgaWYgKGdldE5vZGVGb3JPcHRpb24oXCJpbml0aWFsRm9jdXNcIikgIT09IG51bGwpIHtcbiAgICAgIG5vZGUgPSBnZXROb2RlRm9yT3B0aW9uKFwiaW5pdGlhbEZvY3VzXCIpO1xuICAgIH0gZWxzZSBpZiAoY29udGFpbmVyc0NvbnRhaW4oZG9jLmFjdGl2ZUVsZW1lbnQpKSB7XG4gICAgICBub2RlID0gZG9jLmFjdGl2ZUVsZW1lbnQ7XG4gICAgfSBlbHNlIHtcbiAgICAgIHZhciBmaXJzdFRhYmJhYmxlR3JvdXAgPSBzdGF0ZS50YWJiYWJsZUdyb3Vwc1swXTtcbiAgICAgIHZhciBmaXJzdFRhYmJhYmxlTm9kZSA9IGZpcnN0VGFiYmFibGVHcm91cCAmJiBmaXJzdFRhYmJhYmxlR3JvdXAuZmlyc3RUYWJiYWJsZU5vZGU7XG4gICAgICBub2RlID0gZmlyc3RUYWJiYWJsZU5vZGUgfHwgZ2V0Tm9kZUZvck9wdGlvbihcImZhbGxiYWNrRm9jdXNcIik7XG4gICAgfVxuICAgIGlmICghbm9kZSkge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKFwiWW91ciBmb2N1cy10cmFwIG5lZWRzIHRvIGhhdmUgYXQgbGVhc3Qgb25lIGZvY3VzYWJsZSBlbGVtZW50XCIpO1xuICAgIH1cbiAgICByZXR1cm4gbm9kZTtcbiAgfTtcbiAgdmFyIHVwZGF0ZVRhYmJhYmxlTm9kZXMgPSBmdW5jdGlvbiB1cGRhdGVUYWJiYWJsZU5vZGVzMigpIHtcbiAgICBzdGF0ZS50YWJiYWJsZUdyb3VwcyA9IHN0YXRlLmNvbnRhaW5lcnMubWFwKGZ1bmN0aW9uKGNvbnRhaW5lcikge1xuICAgICAgdmFyIHRhYmJhYmxlTm9kZXMgPSB0YWJiYWJsZShjb250YWluZXIpO1xuICAgICAgaWYgKHRhYmJhYmxlTm9kZXMubGVuZ3RoID4gMCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgIGNvbnRhaW5lcixcbiAgICAgICAgICBmaXJzdFRhYmJhYmxlTm9kZTogdGFiYmFibGVOb2Rlc1swXSxcbiAgICAgICAgICBsYXN0VGFiYmFibGVOb2RlOiB0YWJiYWJsZU5vZGVzW3RhYmJhYmxlTm9kZXMubGVuZ3RoIC0gMV1cbiAgICAgICAgfTtcbiAgICAgIH1cbiAgICAgIHJldHVybiB2b2lkIDA7XG4gICAgfSkuZmlsdGVyKGZ1bmN0aW9uKGdyb3VwKSB7XG4gICAgICByZXR1cm4gISFncm91cDtcbiAgICB9KTtcbiAgICBpZiAoc3RhdGUudGFiYmFibGVHcm91cHMubGVuZ3RoIDw9IDAgJiYgIWdldE5vZGVGb3JPcHRpb24oXCJmYWxsYmFja0ZvY3VzXCIpKSB7XG4gICAgICB0aHJvdyBuZXcgRXJyb3IoXCJZb3VyIGZvY3VzLXRyYXAgbXVzdCBoYXZlIGF0IGxlYXN0IG9uZSBjb250YWluZXIgd2l0aCBhdCBsZWFzdCBvbmUgdGFiYmFibGUgbm9kZSBpbiBpdCBhdCBhbGwgdGltZXNcIik7XG4gICAgfVxuICB9O1xuICB2YXIgdHJ5Rm9jdXMgPSBmdW5jdGlvbiB0cnlGb2N1czIobm9kZSkge1xuICAgIGlmIChub2RlID09PSBmYWxzZSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBpZiAobm9kZSA9PT0gZG9jLmFjdGl2ZUVsZW1lbnQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKCFub2RlIHx8ICFub2RlLmZvY3VzKSB7XG4gICAgICB0cnlGb2N1czIoZ2V0SW5pdGlhbEZvY3VzTm9kZSgpKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgbm9kZS5mb2N1cyh7XG4gICAgICBwcmV2ZW50U2Nyb2xsOiAhIWNvbmZpZy5wcmV2ZW50U2Nyb2xsXG4gICAgfSk7XG4gICAgc3RhdGUubW9zdFJlY2VudGx5Rm9jdXNlZE5vZGUgPSBub2RlO1xuICAgIGlmIChpc1NlbGVjdGFibGVJbnB1dChub2RlKSkge1xuICAgICAgbm9kZS5zZWxlY3QoKTtcbiAgICB9XG4gIH07XG4gIHZhciBnZXRSZXR1cm5Gb2N1c05vZGUgPSBmdW5jdGlvbiBnZXRSZXR1cm5Gb2N1c05vZGUyKHByZXZpb3VzQWN0aXZlRWxlbWVudCkge1xuICAgIHZhciBub2RlID0gZ2V0Tm9kZUZvck9wdGlvbihcInNldFJldHVybkZvY3VzXCIpO1xuICAgIHJldHVybiBub2RlID8gbm9kZSA6IHByZXZpb3VzQWN0aXZlRWxlbWVudDtcbiAgfTtcbiAgdmFyIGNoZWNrUG9pbnRlckRvd24gPSBmdW5jdGlvbiBjaGVja1BvaW50ZXJEb3duMihlKSB7XG4gICAgaWYgKGNvbnRhaW5lcnNDb250YWluKGUudGFyZ2V0KSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBpZiAodmFsdWVPckhhbmRsZXIoY29uZmlnLmNsaWNrT3V0c2lkZURlYWN0aXZhdGVzLCBlKSkge1xuICAgICAgdHJhcC5kZWFjdGl2YXRlKHtcbiAgICAgICAgcmV0dXJuRm9jdXM6IGNvbmZpZy5yZXR1cm5Gb2N1c09uRGVhY3RpdmF0ZSAmJiAhaXNGb2N1c2FibGUoZS50YXJnZXQpXG4gICAgICB9KTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKHZhbHVlT3JIYW5kbGVyKGNvbmZpZy5hbGxvd091dHNpZGVDbGljaywgZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICB9O1xuICB2YXIgY2hlY2tGb2N1c0luID0gZnVuY3Rpb24gY2hlY2tGb2N1c0luMihlKSB7XG4gICAgdmFyIHRhcmdldENvbnRhaW5lZCA9IGNvbnRhaW5lcnNDb250YWluKGUudGFyZ2V0KTtcbiAgICBpZiAodGFyZ2V0Q29udGFpbmVkIHx8IGUudGFyZ2V0IGluc3RhbmNlb2YgRG9jdW1lbnQpIHtcbiAgICAgIGlmICh0YXJnZXRDb250YWluZWQpIHtcbiAgICAgICAgc3RhdGUubW9zdFJlY2VudGx5Rm9jdXNlZE5vZGUgPSBlLnRhcmdldDtcbiAgICAgIH1cbiAgICB9IGVsc2Uge1xuICAgICAgZS5zdG9wSW1tZWRpYXRlUHJvcGFnYXRpb24oKTtcbiAgICAgIHRyeUZvY3VzKHN0YXRlLm1vc3RSZWNlbnRseUZvY3VzZWROb2RlIHx8IGdldEluaXRpYWxGb2N1c05vZGUoKSk7XG4gICAgfVxuICB9O1xuICB2YXIgY2hlY2tUYWIgPSBmdW5jdGlvbiBjaGVja1RhYjIoZSkge1xuICAgIHVwZGF0ZVRhYmJhYmxlTm9kZXMoKTtcbiAgICB2YXIgZGVzdGluYXRpb25Ob2RlID0gbnVsbDtcbiAgICBpZiAoc3RhdGUudGFiYmFibGVHcm91cHMubGVuZ3RoID4gMCkge1xuICAgICAgdmFyIGNvbnRhaW5lckluZGV4ID0gZmluZEluZGV4KHN0YXRlLnRhYmJhYmxlR3JvdXBzLCBmdW5jdGlvbihfcmVmKSB7XG4gICAgICAgIHZhciBjb250YWluZXIgPSBfcmVmLmNvbnRhaW5lcjtcbiAgICAgICAgcmV0dXJuIGNvbnRhaW5lci5jb250YWlucyhlLnRhcmdldCk7XG4gICAgICB9KTtcbiAgICAgIGlmIChjb250YWluZXJJbmRleCA8IDApIHtcbiAgICAgICAgaWYgKGUuc2hpZnRLZXkpIHtcbiAgICAgICAgICBkZXN0aW5hdGlvbk5vZGUgPSBzdGF0ZS50YWJiYWJsZUdyb3Vwc1tzdGF0ZS50YWJiYWJsZUdyb3Vwcy5sZW5ndGggLSAxXS5sYXN0VGFiYmFibGVOb2RlO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIGRlc3RpbmF0aW9uTm9kZSA9IHN0YXRlLnRhYmJhYmxlR3JvdXBzWzBdLmZpcnN0VGFiYmFibGVOb2RlO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKGUuc2hpZnRLZXkpIHtcbiAgICAgICAgdmFyIHN0YXJ0T2ZHcm91cEluZGV4ID0gZmluZEluZGV4KHN0YXRlLnRhYmJhYmxlR3JvdXBzLCBmdW5jdGlvbihfcmVmMikge1xuICAgICAgICAgIHZhciBmaXJzdFRhYmJhYmxlTm9kZSA9IF9yZWYyLmZpcnN0VGFiYmFibGVOb2RlO1xuICAgICAgICAgIHJldHVybiBlLnRhcmdldCA9PT0gZmlyc3RUYWJiYWJsZU5vZGU7XG4gICAgICAgIH0pO1xuICAgICAgICBpZiAoc3RhcnRPZkdyb3VwSW5kZXggPCAwICYmIHN0YXRlLnRhYmJhYmxlR3JvdXBzW2NvbnRhaW5lckluZGV4XS5jb250YWluZXIgPT09IGUudGFyZ2V0KSB7XG4gICAgICAgICAgc3RhcnRPZkdyb3VwSW5kZXggPSBjb250YWluZXJJbmRleDtcbiAgICAgICAgfVxuICAgICAgICBpZiAoc3RhcnRPZkdyb3VwSW5kZXggPj0gMCkge1xuICAgICAgICAgIHZhciBkZXN0aW5hdGlvbkdyb3VwSW5kZXggPSBzdGFydE9mR3JvdXBJbmRleCA9PT0gMCA/IHN0YXRlLnRhYmJhYmxlR3JvdXBzLmxlbmd0aCAtIDEgOiBzdGFydE9mR3JvdXBJbmRleCAtIDE7XG4gICAgICAgICAgdmFyIGRlc3RpbmF0aW9uR3JvdXAgPSBzdGF0ZS50YWJiYWJsZUdyb3Vwc1tkZXN0aW5hdGlvbkdyb3VwSW5kZXhdO1xuICAgICAgICAgIGRlc3RpbmF0aW9uTm9kZSA9IGRlc3RpbmF0aW9uR3JvdXAubGFzdFRhYmJhYmxlTm9kZTtcbiAgICAgICAgfVxuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdmFyIGxhc3RPZkdyb3VwSW5kZXggPSBmaW5kSW5kZXgoc3RhdGUudGFiYmFibGVHcm91cHMsIGZ1bmN0aW9uKF9yZWYzKSB7XG4gICAgICAgICAgdmFyIGxhc3RUYWJiYWJsZU5vZGUgPSBfcmVmMy5sYXN0VGFiYmFibGVOb2RlO1xuICAgICAgICAgIHJldHVybiBlLnRhcmdldCA9PT0gbGFzdFRhYmJhYmxlTm9kZTtcbiAgICAgICAgfSk7XG4gICAgICAgIGlmIChsYXN0T2ZHcm91cEluZGV4IDwgMCAmJiBzdGF0ZS50YWJiYWJsZUdyb3Vwc1tjb250YWluZXJJbmRleF0uY29udGFpbmVyID09PSBlLnRhcmdldCkge1xuICAgICAgICAgIGxhc3RPZkdyb3VwSW5kZXggPSBjb250YWluZXJJbmRleDtcbiAgICAgICAgfVxuICAgICAgICBpZiAobGFzdE9mR3JvdXBJbmRleCA+PSAwKSB7XG4gICAgICAgICAgdmFyIF9kZXN0aW5hdGlvbkdyb3VwSW5kZXggPSBsYXN0T2ZHcm91cEluZGV4ID09PSBzdGF0ZS50YWJiYWJsZUdyb3Vwcy5sZW5ndGggLSAxID8gMCA6IGxhc3RPZkdyb3VwSW5kZXggKyAxO1xuICAgICAgICAgIHZhciBfZGVzdGluYXRpb25Hcm91cCA9IHN0YXRlLnRhYmJhYmxlR3JvdXBzW19kZXN0aW5hdGlvbkdyb3VwSW5kZXhdO1xuICAgICAgICAgIGRlc3RpbmF0aW9uTm9kZSA9IF9kZXN0aW5hdGlvbkdyb3VwLmZpcnN0VGFiYmFibGVOb2RlO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSBlbHNlIHtcbiAgICAgIGRlc3RpbmF0aW9uTm9kZSA9IGdldE5vZGVGb3JPcHRpb24oXCJmYWxsYmFja0ZvY3VzXCIpO1xuICAgIH1cbiAgICBpZiAoZGVzdGluYXRpb25Ob2RlKSB7XG4gICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB0cnlGb2N1cyhkZXN0aW5hdGlvbk5vZGUpO1xuICAgIH1cbiAgfTtcbiAgdmFyIGNoZWNrS2V5ID0gZnVuY3Rpb24gY2hlY2tLZXkyKGUpIHtcbiAgICBpZiAoaXNFc2NhcGVFdmVudChlKSAmJiB2YWx1ZU9ySGFuZGxlcihjb25maWcuZXNjYXBlRGVhY3RpdmF0ZXMpICE9PSBmYWxzZSkge1xuICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgdHJhcC5kZWFjdGl2YXRlKCk7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGlmIChpc1RhYkV2ZW50KGUpKSB7XG4gICAgICBjaGVja1RhYihlKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gIH07XG4gIHZhciBjaGVja0NsaWNrID0gZnVuY3Rpb24gY2hlY2tDbGljazIoZSkge1xuICAgIGlmICh2YWx1ZU9ySGFuZGxlcihjb25maWcuY2xpY2tPdXRzaWRlRGVhY3RpdmF0ZXMsIGUpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGlmIChjb250YWluZXJzQ29udGFpbihlLnRhcmdldCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgaWYgKHZhbHVlT3JIYW5kbGVyKGNvbmZpZy5hbGxvd091dHNpZGVDbGljaywgZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIGUuc3RvcEltbWVkaWF0ZVByb3BhZ2F0aW9uKCk7XG4gIH07XG4gIHZhciBhZGRMaXN0ZW5lcnMgPSBmdW5jdGlvbiBhZGRMaXN0ZW5lcnMyKCkge1xuICAgIGlmICghc3RhdGUuYWN0aXZlKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGFjdGl2ZUZvY3VzVHJhcHMuYWN0aXZhdGVUcmFwKHRyYXApO1xuICAgIHN0YXRlLmRlbGF5SW5pdGlhbEZvY3VzVGltZXIgPSBjb25maWcuZGVsYXlJbml0aWFsRm9jdXMgPyBkZWxheShmdW5jdGlvbigpIHtcbiAgICAgIHRyeUZvY3VzKGdldEluaXRpYWxGb2N1c05vZGUoKSk7XG4gICAgfSkgOiB0cnlGb2N1cyhnZXRJbml0aWFsRm9jdXNOb2RlKCkpO1xuICAgIGRvYy5hZGRFdmVudExpc3RlbmVyKFwiZm9jdXNpblwiLCBjaGVja0ZvY3VzSW4sIHRydWUpO1xuICAgIGRvYy5hZGRFdmVudExpc3RlbmVyKFwibW91c2Vkb3duXCIsIGNoZWNrUG9pbnRlckRvd24sIHtcbiAgICAgIGNhcHR1cmU6IHRydWUsXG4gICAgICBwYXNzaXZlOiBmYWxzZVxuICAgIH0pO1xuICAgIGRvYy5hZGRFdmVudExpc3RlbmVyKFwidG91Y2hzdGFydFwiLCBjaGVja1BvaW50ZXJEb3duLCB7XG4gICAgICBjYXB0dXJlOiB0cnVlLFxuICAgICAgcGFzc2l2ZTogZmFsc2VcbiAgICB9KTtcbiAgICBkb2MuYWRkRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGNoZWNrQ2xpY2ssIHtcbiAgICAgIGNhcHR1cmU6IHRydWUsXG4gICAgICBwYXNzaXZlOiBmYWxzZVxuICAgIH0pO1xuICAgIGRvYy5hZGRFdmVudExpc3RlbmVyKFwia2V5ZG93blwiLCBjaGVja0tleSwge1xuICAgICAgY2FwdHVyZTogdHJ1ZSxcbiAgICAgIHBhc3NpdmU6IGZhbHNlXG4gICAgfSk7XG4gICAgcmV0dXJuIHRyYXA7XG4gIH07XG4gIHZhciByZW1vdmVMaXN0ZW5lcnMgPSBmdW5jdGlvbiByZW1vdmVMaXN0ZW5lcnMyKCkge1xuICAgIGlmICghc3RhdGUuYWN0aXZlKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwiZm9jdXNpblwiLCBjaGVja0ZvY3VzSW4sIHRydWUpO1xuICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwibW91c2Vkb3duXCIsIGNoZWNrUG9pbnRlckRvd24sIHRydWUpO1xuICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwidG91Y2hzdGFydFwiLCBjaGVja1BvaW50ZXJEb3duLCB0cnVlKTtcbiAgICBkb2MucmVtb3ZlRXZlbnRMaXN0ZW5lcihcImNsaWNrXCIsIGNoZWNrQ2xpY2ssIHRydWUpO1xuICAgIGRvYy5yZW1vdmVFdmVudExpc3RlbmVyKFwia2V5ZG93blwiLCBjaGVja0tleSwgdHJ1ZSk7XG4gICAgcmV0dXJuIHRyYXA7XG4gIH07XG4gIHRyYXAgPSB7XG4gICAgYWN0aXZhdGU6IGZ1bmN0aW9uIGFjdGl2YXRlKGFjdGl2YXRlT3B0aW9ucykge1xuICAgICAgaWYgKHN0YXRlLmFjdGl2ZSkge1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cbiAgICAgIHZhciBvbkFjdGl2YXRlID0gZ2V0T3B0aW9uKGFjdGl2YXRlT3B0aW9ucywgXCJvbkFjdGl2YXRlXCIpO1xuICAgICAgdmFyIG9uUG9zdEFjdGl2YXRlID0gZ2V0T3B0aW9uKGFjdGl2YXRlT3B0aW9ucywgXCJvblBvc3RBY3RpdmF0ZVwiKTtcbiAgICAgIHZhciBjaGVja0NhbkZvY3VzVHJhcCA9IGdldE9wdGlvbihhY3RpdmF0ZU9wdGlvbnMsIFwiY2hlY2tDYW5Gb2N1c1RyYXBcIik7XG4gICAgICBpZiAoIWNoZWNrQ2FuRm9jdXNUcmFwKSB7XG4gICAgICAgIHVwZGF0ZVRhYmJhYmxlTm9kZXMoKTtcbiAgICAgIH1cbiAgICAgIHN0YXRlLmFjdGl2ZSA9IHRydWU7XG4gICAgICBzdGF0ZS5wYXVzZWQgPSBmYWxzZTtcbiAgICAgIHN0YXRlLm5vZGVGb2N1c2VkQmVmb3JlQWN0aXZhdGlvbiA9IGRvYy5hY3RpdmVFbGVtZW50O1xuICAgICAgaWYgKG9uQWN0aXZhdGUpIHtcbiAgICAgICAgb25BY3RpdmF0ZSgpO1xuICAgICAgfVxuICAgICAgdmFyIGZpbmlzaEFjdGl2YXRpb24gPSBmdW5jdGlvbiBmaW5pc2hBY3RpdmF0aW9uMigpIHtcbiAgICAgICAgaWYgKGNoZWNrQ2FuRm9jdXNUcmFwKSB7XG4gICAgICAgICAgdXBkYXRlVGFiYmFibGVOb2RlcygpO1xuICAgICAgICB9XG4gICAgICAgIGFkZExpc3RlbmVycygpO1xuICAgICAgICBpZiAob25Qb3N0QWN0aXZhdGUpIHtcbiAgICAgICAgICBvblBvc3RBY3RpdmF0ZSgpO1xuICAgICAgICB9XG4gICAgICB9O1xuICAgICAgaWYgKGNoZWNrQ2FuRm9jdXNUcmFwKSB7XG4gICAgICAgIGNoZWNrQ2FuRm9jdXNUcmFwKHN0YXRlLmNvbnRhaW5lcnMuY29uY2F0KCkpLnRoZW4oZmluaXNoQWN0aXZhdGlvbiwgZmluaXNoQWN0aXZhdGlvbik7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuICAgICAgZmluaXNoQWN0aXZhdGlvbigpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICBkZWFjdGl2YXRlOiBmdW5jdGlvbiBkZWFjdGl2YXRlKGRlYWN0aXZhdGVPcHRpb25zKSB7XG4gICAgICBpZiAoIXN0YXRlLmFjdGl2ZSkge1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cbiAgICAgIGNsZWFyVGltZW91dChzdGF0ZS5kZWxheUluaXRpYWxGb2N1c1RpbWVyKTtcbiAgICAgIHN0YXRlLmRlbGF5SW5pdGlhbEZvY3VzVGltZXIgPSB2b2lkIDA7XG4gICAgICByZW1vdmVMaXN0ZW5lcnMoKTtcbiAgICAgIHN0YXRlLmFjdGl2ZSA9IGZhbHNlO1xuICAgICAgc3RhdGUucGF1c2VkID0gZmFsc2U7XG4gICAgICBhY3RpdmVGb2N1c1RyYXBzLmRlYWN0aXZhdGVUcmFwKHRyYXApO1xuICAgICAgdmFyIG9uRGVhY3RpdmF0ZSA9IGdldE9wdGlvbihkZWFjdGl2YXRlT3B0aW9ucywgXCJvbkRlYWN0aXZhdGVcIik7XG4gICAgICB2YXIgb25Qb3N0RGVhY3RpdmF0ZSA9IGdldE9wdGlvbihkZWFjdGl2YXRlT3B0aW9ucywgXCJvblBvc3REZWFjdGl2YXRlXCIpO1xuICAgICAgdmFyIGNoZWNrQ2FuUmV0dXJuRm9jdXMgPSBnZXRPcHRpb24oZGVhY3RpdmF0ZU9wdGlvbnMsIFwiY2hlY2tDYW5SZXR1cm5Gb2N1c1wiKTtcbiAgICAgIGlmIChvbkRlYWN0aXZhdGUpIHtcbiAgICAgICAgb25EZWFjdGl2YXRlKCk7XG4gICAgICB9XG4gICAgICB2YXIgcmV0dXJuRm9jdXMgPSBnZXRPcHRpb24oZGVhY3RpdmF0ZU9wdGlvbnMsIFwicmV0dXJuRm9jdXNcIiwgXCJyZXR1cm5Gb2N1c09uRGVhY3RpdmF0ZVwiKTtcbiAgICAgIHZhciBmaW5pc2hEZWFjdGl2YXRpb24gPSBmdW5jdGlvbiBmaW5pc2hEZWFjdGl2YXRpb24yKCkge1xuICAgICAgICBkZWxheShmdW5jdGlvbigpIHtcbiAgICAgICAgICBpZiAocmV0dXJuRm9jdXMpIHtcbiAgICAgICAgICAgIHRyeUZvY3VzKGdldFJldHVybkZvY3VzTm9kZShzdGF0ZS5ub2RlRm9jdXNlZEJlZm9yZUFjdGl2YXRpb24pKTtcbiAgICAgICAgICB9XG4gICAgICAgICAgaWYgKG9uUG9zdERlYWN0aXZhdGUpIHtcbiAgICAgICAgICAgIG9uUG9zdERlYWN0aXZhdGUoKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgfTtcbiAgICAgIGlmIChyZXR1cm5Gb2N1cyAmJiBjaGVja0NhblJldHVybkZvY3VzKSB7XG4gICAgICAgIGNoZWNrQ2FuUmV0dXJuRm9jdXMoZ2V0UmV0dXJuRm9jdXNOb2RlKHN0YXRlLm5vZGVGb2N1c2VkQmVmb3JlQWN0aXZhdGlvbikpLnRoZW4oZmluaXNoRGVhY3RpdmF0aW9uLCBmaW5pc2hEZWFjdGl2YXRpb24pO1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cbiAgICAgIGZpbmlzaERlYWN0aXZhdGlvbigpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfSxcbiAgICBwYXVzZTogZnVuY3Rpb24gcGF1c2UoKSB7XG4gICAgICBpZiAoc3RhdGUucGF1c2VkIHx8ICFzdGF0ZS5hY3RpdmUpIHtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG4gICAgICBzdGF0ZS5wYXVzZWQgPSB0cnVlO1xuICAgICAgcmVtb3ZlTGlzdGVuZXJzKCk7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9LFxuICAgIHVucGF1c2U6IGZ1bmN0aW9uIHVucGF1c2UoKSB7XG4gICAgICBpZiAoIXN0YXRlLnBhdXNlZCB8fCAhc3RhdGUuYWN0aXZlKSB7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuICAgICAgc3RhdGUucGF1c2VkID0gZmFsc2U7XG4gICAgICB1cGRhdGVUYWJiYWJsZU5vZGVzKCk7XG4gICAgICBhZGRMaXN0ZW5lcnMoKTtcbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH0sXG4gICAgdXBkYXRlQ29udGFpbmVyRWxlbWVudHM6IGZ1bmN0aW9uIHVwZGF0ZUNvbnRhaW5lckVsZW1lbnRzKGNvbnRhaW5lckVsZW1lbnRzKSB7XG4gICAgICB2YXIgZWxlbWVudHNBc0FycmF5ID0gW10uY29uY2F0KGNvbnRhaW5lckVsZW1lbnRzKS5maWx0ZXIoQm9vbGVhbik7XG4gICAgICBzdGF0ZS5jb250YWluZXJzID0gZWxlbWVudHNBc0FycmF5Lm1hcChmdW5jdGlvbihlbGVtZW50KSB7XG4gICAgICAgIHJldHVybiB0eXBlb2YgZWxlbWVudCA9PT0gXCJzdHJpbmdcIiA/IGRvYy5xdWVyeVNlbGVjdG9yKGVsZW1lbnQpIDogZWxlbWVudDtcbiAgICAgIH0pO1xuICAgICAgaWYgKHN0YXRlLmFjdGl2ZSkge1xuICAgICAgICB1cGRhdGVUYWJiYWJsZU5vZGVzKCk7XG4gICAgICB9XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9XG4gIH07XG4gIHRyYXAudXBkYXRlQ29udGFpbmVyRWxlbWVudHMoZWxlbWVudHMpO1xuICByZXR1cm4gdHJhcDtcbn07XG5cbi8vIHBhY2thZ2VzL2ZvY3VzL3NyYy9pbmRleC5qc1xuZnVuY3Rpb24gc3JjX2RlZmF1bHQoQWxwaW5lKSB7XG4gIGxldCBsYXN0Rm9jdXNlZDtcbiAgbGV0IGN1cnJlbnRGb2N1c2VkO1xuICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihcImZvY3VzaW5cIiwgKCkgPT4ge1xuICAgIGxhc3RGb2N1c2VkID0gY3VycmVudEZvY3VzZWQ7XG4gICAgY3VycmVudEZvY3VzZWQgPSBkb2N1bWVudC5hY3RpdmVFbGVtZW50O1xuICB9KTtcbiAgQWxwaW5lLm1hZ2ljKFwiZm9jdXNcIiwgKGVsKSA9PiB7XG4gICAgbGV0IHdpdGhpbiA9IGVsO1xuICAgIHJldHVybiB7XG4gICAgICBfX25vc2Nyb2xsOiBmYWxzZSxcbiAgICAgIF9fd3JhcEFyb3VuZDogZmFsc2UsXG4gICAgICB3aXRoaW4oZWwyKSB7XG4gICAgICAgIHdpdGhpbiA9IGVsMjtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9LFxuICAgICAgd2l0aG91dFNjcm9sbGluZygpIHtcbiAgICAgICAgdGhpcy5fX25vc2Nyb2xsID0gdHJ1ZTtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9LFxuICAgICAgbm9zY3JvbGwoKSB7XG4gICAgICAgIHRoaXMuX19ub3Njcm9sbCA9IHRydWU7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfSxcbiAgICAgIHdpdGhXcmFwQXJvdW5kKCkge1xuICAgICAgICB0aGlzLl9fd3JhcEFyb3VuZCA9IHRydWU7XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfSxcbiAgICAgIHdyYXAoKSB7XG4gICAgICAgIHJldHVybiB0aGlzLndpdGhXcmFwQXJvdW5kKCk7XG4gICAgICB9LFxuICAgICAgZm9jdXNhYmxlKGVsMikge1xuICAgICAgICByZXR1cm4gaXNGb2N1c2FibGUoZWwyKTtcbiAgICAgIH0sXG4gICAgICBwcmV2aW91c2x5Rm9jdXNlZCgpIHtcbiAgICAgICAgcmV0dXJuIGxhc3RGb2N1c2VkO1xuICAgICAgfSxcbiAgICAgIGxhc3RGb2N1c2VkKCkge1xuICAgICAgICByZXR1cm4gbGFzdEZvY3VzZWQ7XG4gICAgICB9LFxuICAgICAgZm9jdXNlZCgpIHtcbiAgICAgICAgcmV0dXJuIGN1cnJlbnRGb2N1c2VkO1xuICAgICAgfSxcbiAgICAgIGZvY3VzYWJsZXMoKSB7XG4gICAgICAgIGlmIChBcnJheS5pc0FycmF5KHdpdGhpbikpXG4gICAgICAgICAgcmV0dXJuIHdpdGhpbjtcbiAgICAgICAgcmV0dXJuIGZvY3VzYWJsZSh3aXRoaW4sIHtkaXNwbGF5Q2hlY2s6IFwibm9uZVwifSk7XG4gICAgICB9LFxuICAgICAgYWxsKCkge1xuICAgICAgICByZXR1cm4gdGhpcy5mb2N1c2FibGVzKCk7XG4gICAgICB9LFxuICAgICAgaXNGaXJzdChlbDIpIHtcbiAgICAgICAgbGV0IGVscyA9IHRoaXMuYWxsKCk7XG4gICAgICAgIHJldHVybiBlbHNbMF0gJiYgZWxzWzBdLmlzU2FtZU5vZGUoZWwyKTtcbiAgICAgIH0sXG4gICAgICBpc0xhc3QoZWwyKSB7XG4gICAgICAgIGxldCBlbHMgPSB0aGlzLmFsbCgpO1xuICAgICAgICByZXR1cm4gZWxzLmxlbmd0aCAmJiBlbHMuc2xpY2UoLTEpWzBdLmlzU2FtZU5vZGUoZWwyKTtcbiAgICAgIH0sXG4gICAgICBnZXRGaXJzdCgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuYWxsKClbMF07XG4gICAgICB9LFxuICAgICAgZ2V0TGFzdCgpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMuYWxsKCkuc2xpY2UoLTEpWzBdO1xuICAgICAgfSxcbiAgICAgIGdldE5leHQoKSB7XG4gICAgICAgIGxldCBsaXN0ID0gdGhpcy5hbGwoKTtcbiAgICAgICAgbGV0IGN1cnJlbnQgPSBkb2N1bWVudC5hY3RpdmVFbGVtZW50O1xuICAgICAgICBpZiAobGlzdC5pbmRleE9mKGN1cnJlbnQpID09PSAtMSlcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIGlmICh0aGlzLl9fd3JhcEFyb3VuZCAmJiBsaXN0LmluZGV4T2YoY3VycmVudCkgPT09IGxpc3QubGVuZ3RoIC0gMSkge1xuICAgICAgICAgIHJldHVybiBsaXN0WzBdO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBsaXN0W2xpc3QuaW5kZXhPZihjdXJyZW50KSArIDFdO1xuICAgICAgfSxcbiAgICAgIGdldFByZXZpb3VzKCkge1xuICAgICAgICBsZXQgbGlzdCA9IHRoaXMuYWxsKCk7XG4gICAgICAgIGxldCBjdXJyZW50ID0gZG9jdW1lbnQuYWN0aXZlRWxlbWVudDtcbiAgICAgICAgaWYgKGxpc3QuaW5kZXhPZihjdXJyZW50KSA9PT0gLTEpXG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICBpZiAodGhpcy5fX3dyYXBBcm91bmQgJiYgbGlzdC5pbmRleE9mKGN1cnJlbnQpID09PSAwKSB7XG4gICAgICAgICAgcmV0dXJuIGxpc3Quc2xpY2UoLTEpWzBdO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiBsaXN0W2xpc3QuaW5kZXhPZihjdXJyZW50KSAtIDFdO1xuICAgICAgfSxcbiAgICAgIGZpcnN0KCkge1xuICAgICAgICB0aGlzLmZvY3VzKHRoaXMuZ2V0Rmlyc3QoKSk7XG4gICAgICB9LFxuICAgICAgbGFzdCgpIHtcbiAgICAgICAgdGhpcy5mb2N1cyh0aGlzLmdldExhc3QoKSk7XG4gICAgICB9LFxuICAgICAgbmV4dCgpIHtcbiAgICAgICAgdGhpcy5mb2N1cyh0aGlzLmdldE5leHQoKSk7XG4gICAgICB9LFxuICAgICAgcHJldmlvdXMoKSB7XG4gICAgICAgIHRoaXMuZm9jdXModGhpcy5nZXRQcmV2aW91cygpKTtcbiAgICAgIH0sXG4gICAgICBwcmV2KCkge1xuICAgICAgICByZXR1cm4gdGhpcy5wcmV2aW91cygpO1xuICAgICAgfSxcbiAgICAgIGZvY3VzKGVsMikge1xuICAgICAgICBpZiAoIWVsMilcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgIGlmICghZWwyLmhhc0F0dHJpYnV0ZShcInRhYmluZGV4XCIpKVxuICAgICAgICAgICAgZWwyLnNldEF0dHJpYnV0ZShcInRhYmluZGV4XCIsIFwiMFwiKTtcbiAgICAgICAgICBlbDIuZm9jdXMoe3ByZXZlbnRTY3JvbGw6IHRoaXMuX25vc2Nyb2xsfSk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH07XG4gIH0pO1xuICBBbHBpbmUuZGlyZWN0aXZlKFwidHJhcFwiLCBBbHBpbmUuc2tpcER1cmluZ0Nsb25lKChlbCwge2V4cHJlc3Npb24sIG1vZGlmaWVyc30sIHtlZmZlY3QsIGV2YWx1YXRlTGF0ZXIsIGNsZWFudXB9KSA9PiB7XG4gICAgbGV0IGV2YWx1YXRvciA9IGV2YWx1YXRlTGF0ZXIoZXhwcmVzc2lvbik7XG4gICAgbGV0IG9sZFZhbHVlID0gZmFsc2U7XG4gICAgbGV0IG9wdGlvbnMgPSB7XG4gICAgICBlc2NhcGVEZWFjdGl2YXRlczogZmFsc2UsXG4gICAgICBhbGxvd091dHNpZGVDbGljazogdHJ1ZSxcbiAgICAgIGZhbGxiYWNrRm9jdXM6ICgpID0+IGVsXG4gICAgfTtcbiAgICBsZXQgYXV0b2ZvY3VzRWwgPSBlbC5xdWVyeVNlbGVjdG9yKFwiW2F1dG9mb2N1c11cIik7XG4gICAgaWYgKGF1dG9mb2N1c0VsKVxuICAgICAgb3B0aW9ucy5pbml0aWFsRm9jdXMgPSBhdXRvZm9jdXNFbDtcbiAgICBsZXQgdHJhcCA9IGNyZWF0ZUZvY3VzVHJhcChlbCwgb3B0aW9ucyk7XG4gICAgbGV0IHVuZG9JbmVydCA9ICgpID0+IHtcbiAgICB9O1xuICAgIGxldCB1bmRvRGlzYWJsZVNjcm9sbGluZyA9ICgpID0+IHtcbiAgICB9O1xuICAgIGNvbnN0IHJlbGVhc2VGb2N1cyA9ICgpID0+IHtcbiAgICAgIHVuZG9JbmVydCgpO1xuICAgICAgdW5kb0luZXJ0ID0gKCkgPT4ge1xuICAgICAgfTtcbiAgICAgIHVuZG9EaXNhYmxlU2Nyb2xsaW5nKCk7XG4gICAgICB1bmRvRGlzYWJsZVNjcm9sbGluZyA9ICgpID0+IHtcbiAgICAgIH07XG4gICAgICB0cmFwLmRlYWN0aXZhdGUoe1xuICAgICAgICByZXR1cm5Gb2N1czogIW1vZGlmaWVycy5pbmNsdWRlcyhcIm5vcmV0dXJuXCIpXG4gICAgICB9KTtcbiAgICB9O1xuICAgIGVmZmVjdCgoKSA9PiBldmFsdWF0b3IoKHZhbHVlKSA9PiB7XG4gICAgICBpZiAob2xkVmFsdWUgPT09IHZhbHVlKVxuICAgICAgICByZXR1cm47XG4gICAgICBpZiAodmFsdWUgJiYgIW9sZFZhbHVlKSB7XG4gICAgICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgIGlmIChtb2RpZmllcnMuaW5jbHVkZXMoXCJpbmVydFwiKSlcbiAgICAgICAgICAgIHVuZG9JbmVydCA9IHNldEluZXJ0KGVsKTtcbiAgICAgICAgICBpZiAobW9kaWZpZXJzLmluY2x1ZGVzKFwibm9zY3JvbGxcIikpXG4gICAgICAgICAgICB1bmRvRGlzYWJsZVNjcm9sbGluZyA9IGRpc2FibGVTY3JvbGxpbmcoKTtcbiAgICAgICAgICB0cmFwLmFjdGl2YXRlKCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICAgaWYgKCF2YWx1ZSAmJiBvbGRWYWx1ZSkge1xuICAgICAgICByZWxlYXNlRm9jdXMoKTtcbiAgICAgIH1cbiAgICAgIG9sZFZhbHVlID0gISF2YWx1ZTtcbiAgICB9KSk7XG4gICAgY2xlYW51cChyZWxlYXNlRm9jdXMpO1xuICB9LCAoZWwsIHtleHByZXNzaW9uLCBtb2RpZmllcnN9LCB7ZXZhbHVhdGV9KSA9PiB7XG4gICAgaWYgKG1vZGlmaWVycy5pbmNsdWRlcyhcImluZXJ0XCIpICYmIGV2YWx1YXRlKGV4cHJlc3Npb24pKVxuICAgICAgc2V0SW5lcnQoZWwpO1xuICB9KSk7XG59XG5mdW5jdGlvbiBzZXRJbmVydChlbCkge1xuICBsZXQgdW5kb3MgPSBbXTtcbiAgY3Jhd2xTaWJsaW5nc1VwKGVsLCAoc2libGluZykgPT4ge1xuICAgIGxldCBjYWNoZSA9IHNpYmxpbmcuaGFzQXR0cmlidXRlKFwiYXJpYS1oaWRkZW5cIik7XG4gICAgc2libGluZy5zZXRBdHRyaWJ1dGUoXCJhcmlhLWhpZGRlblwiLCBcInRydWVcIik7XG4gICAgdW5kb3MucHVzaCgoKSA9PiBjYWNoZSB8fCBzaWJsaW5nLnJlbW92ZUF0dHJpYnV0ZShcImFyaWEtaGlkZGVuXCIpKTtcbiAgfSk7XG4gIHJldHVybiAoKSA9PiB7XG4gICAgd2hpbGUgKHVuZG9zLmxlbmd0aClcbiAgICAgIHVuZG9zLnBvcCgpKCk7XG4gIH07XG59XG5mdW5jdGlvbiBjcmF3bFNpYmxpbmdzVXAoZWwsIGNhbGxiYWNrKSB7XG4gIGlmIChlbC5pc1NhbWVOb2RlKGRvY3VtZW50LmJvZHkpIHx8ICFlbC5wYXJlbnROb2RlKVxuICAgIHJldHVybjtcbiAgQXJyYXkuZnJvbShlbC5wYXJlbnROb2RlLmNoaWxkcmVuKS5mb3JFYWNoKChzaWJsaW5nKSA9PiB7XG4gICAgaWYgKHNpYmxpbmcuaXNTYW1lTm9kZShlbCkpIHtcbiAgICAgIGNyYXdsU2libGluZ3NVcChlbC5wYXJlbnROb2RlLCBjYWxsYmFjayk7XG4gICAgfSBlbHNlIHtcbiAgICAgIGNhbGxiYWNrKHNpYmxpbmcpO1xuICAgIH1cbiAgfSk7XG59XG5mdW5jdGlvbiBkaXNhYmxlU2Nyb2xsaW5nKCkge1xuICBsZXQgb3ZlcmZsb3cgPSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuc3R5bGUub3ZlcmZsb3c7XG4gIGxldCBwYWRkaW5nUmlnaHQgPSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuc3R5bGUucGFkZGluZ1JpZ2h0O1xuICBsZXQgc2Nyb2xsYmFyV2lkdGggPSB3aW5kb3cuaW5uZXJXaWR0aCAtIGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5jbGllbnRXaWR0aDtcbiAgZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LnN0eWxlLm92ZXJmbG93ID0gXCJoaWRkZW5cIjtcbiAgZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LnN0eWxlLnBhZGRpbmdSaWdodCA9IGAke3Njcm9sbGJhcldpZHRofXB4YDtcbiAgcmV0dXJuICgpID0+IHtcbiAgICBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuc3R5bGUub3ZlcmZsb3cgPSBvdmVyZmxvdztcbiAgICBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuc3R5bGUucGFkZGluZ1JpZ2h0ID0gcGFkZGluZ1JpZ2h0O1xuICB9O1xufVxuXG4vLyBwYWNrYWdlcy9mb2N1cy9idWlsZHMvbW9kdWxlLmpzXG52YXIgbW9kdWxlX2RlZmF1bHQgPSBzcmNfZGVmYXVsdDtcbmV4cG9ydCB7XG4gIG1vZHVsZV9kZWZhdWx0IGFzIGRlZmF1bHRcbn07XG4iLCAiLyoqIVxuICogU29ydGFibGUgMS4xNS4wXG4gKiBAYXV0aG9yXHRSdWJhWGEgICA8dHJhc2hAcnViYXhhLm9yZz5cbiAqIEBhdXRob3JcdG93ZW5tICAgIDxvd2VuMjMzNTVAZ21haWwuY29tPlxuICogQGxpY2Vuc2UgTUlUXG4gKi9cbmZ1bmN0aW9uIG93bktleXMob2JqZWN0LCBlbnVtZXJhYmxlT25seSkge1xuICB2YXIga2V5cyA9IE9iamVjdC5rZXlzKG9iamVjdCk7XG5cbiAgaWYgKE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMpIHtcbiAgICB2YXIgc3ltYm9scyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMob2JqZWN0KTtcblxuICAgIGlmIChlbnVtZXJhYmxlT25seSkge1xuICAgICAgc3ltYm9scyA9IHN5bWJvbHMuZmlsdGVyKGZ1bmN0aW9uIChzeW0pIHtcbiAgICAgICAgcmV0dXJuIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3Iob2JqZWN0LCBzeW0pLmVudW1lcmFibGU7XG4gICAgICB9KTtcbiAgICB9XG5cbiAgICBrZXlzLnB1c2guYXBwbHkoa2V5cywgc3ltYm9scyk7XG4gIH1cblxuICByZXR1cm4ga2V5cztcbn1cblxuZnVuY3Rpb24gX29iamVjdFNwcmVhZDIodGFyZ2V0KSB7XG4gIGZvciAodmFyIGkgPSAxOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSB7XG4gICAgdmFyIHNvdXJjZSA9IGFyZ3VtZW50c1tpXSAhPSBudWxsID8gYXJndW1lbnRzW2ldIDoge307XG5cbiAgICBpZiAoaSAlIDIpIHtcbiAgICAgIG93bktleXMoT2JqZWN0KHNvdXJjZSksIHRydWUpLmZvckVhY2goZnVuY3Rpb24gKGtleSkge1xuICAgICAgICBfZGVmaW5lUHJvcGVydHkodGFyZ2V0LCBrZXksIHNvdXJjZVtrZXldKTtcbiAgICAgIH0pO1xuICAgIH0gZWxzZSBpZiAoT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcnMpIHtcbiAgICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcnMoc291cmNlKSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIG93bktleXMoT2JqZWN0KHNvdXJjZSkpLmZvckVhY2goZnVuY3Rpb24gKGtleSkge1xuICAgICAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkodGFyZ2V0LCBrZXksIE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3Ioc291cmNlLCBrZXkpKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiB0YXJnZXQ7XG59XG5cbmZ1bmN0aW9uIF90eXBlb2Yob2JqKSB7XG4gIFwiQGJhYmVsL2hlbHBlcnMgLSB0eXBlb2ZcIjtcblxuICBpZiAodHlwZW9mIFN5bWJvbCA9PT0gXCJmdW5jdGlvblwiICYmIHR5cGVvZiBTeW1ib2wuaXRlcmF0b3IgPT09IFwic3ltYm9sXCIpIHtcbiAgICBfdHlwZW9mID0gZnVuY3Rpb24gKG9iaikge1xuICAgICAgcmV0dXJuIHR5cGVvZiBvYmo7XG4gICAgfTtcbiAgfSBlbHNlIHtcbiAgICBfdHlwZW9mID0gZnVuY3Rpb24gKG9iaikge1xuICAgICAgcmV0dXJuIG9iaiAmJiB0eXBlb2YgU3ltYm9sID09PSBcImZ1bmN0aW9uXCIgJiYgb2JqLmNvbnN0cnVjdG9yID09PSBTeW1ib2wgJiYgb2JqICE9PSBTeW1ib2wucHJvdG90eXBlID8gXCJzeW1ib2xcIiA6IHR5cGVvZiBvYmo7XG4gICAgfTtcbiAgfVxuXG4gIHJldHVybiBfdHlwZW9mKG9iaik7XG59XG5cbmZ1bmN0aW9uIF9kZWZpbmVQcm9wZXJ0eShvYmosIGtleSwgdmFsdWUpIHtcbiAgaWYgKGtleSBpbiBvYmopIHtcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkob2JqLCBrZXksIHtcbiAgICAgIHZhbHVlOiB2YWx1ZSxcbiAgICAgIGVudW1lcmFibGU6IHRydWUsXG4gICAgICBjb25maWd1cmFibGU6IHRydWUsXG4gICAgICB3cml0YWJsZTogdHJ1ZVxuICAgIH0pO1xuICB9IGVsc2Uge1xuICAgIG9ialtrZXldID0gdmFsdWU7XG4gIH1cblxuICByZXR1cm4gb2JqO1xufVxuXG5mdW5jdGlvbiBfZXh0ZW5kcygpIHtcbiAgX2V4dGVuZHMgPSBPYmplY3QuYXNzaWduIHx8IGZ1bmN0aW9uICh0YXJnZXQpIHtcbiAgICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIHNvdXJjZSA9IGFyZ3VtZW50c1tpXTtcblxuICAgICAgZm9yICh2YXIga2V5IGluIHNvdXJjZSkge1xuICAgICAgICBpZiAoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKHNvdXJjZSwga2V5KSkge1xuICAgICAgICAgIHRhcmdldFtrZXldID0gc291cmNlW2tleV07XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gdGFyZ2V0O1xuICB9O1xuXG4gIHJldHVybiBfZXh0ZW5kcy5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xufVxuXG5mdW5jdGlvbiBfb2JqZWN0V2l0aG91dFByb3BlcnRpZXNMb29zZShzb3VyY2UsIGV4Y2x1ZGVkKSB7XG4gIGlmIChzb3VyY2UgPT0gbnVsbCkgcmV0dXJuIHt9O1xuICB2YXIgdGFyZ2V0ID0ge307XG4gIHZhciBzb3VyY2VLZXlzID0gT2JqZWN0LmtleXMoc291cmNlKTtcbiAgdmFyIGtleSwgaTtcblxuICBmb3IgKGkgPSAwOyBpIDwgc291cmNlS2V5cy5sZW5ndGg7IGkrKykge1xuICAgIGtleSA9IHNvdXJjZUtleXNbaV07XG4gICAgaWYgKGV4Y2x1ZGVkLmluZGV4T2Yoa2V5KSA+PSAwKSBjb250aW51ZTtcbiAgICB0YXJnZXRba2V5XSA9IHNvdXJjZVtrZXldO1xuICB9XG5cbiAgcmV0dXJuIHRhcmdldDtcbn1cblxuZnVuY3Rpb24gX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzKHNvdXJjZSwgZXhjbHVkZWQpIHtcbiAgaWYgKHNvdXJjZSA9PSBudWxsKSByZXR1cm4ge307XG5cbiAgdmFyIHRhcmdldCA9IF9vYmplY3RXaXRob3V0UHJvcGVydGllc0xvb3NlKHNvdXJjZSwgZXhjbHVkZWQpO1xuXG4gIHZhciBrZXksIGk7XG5cbiAgaWYgKE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMpIHtcbiAgICB2YXIgc291cmNlU3ltYm9sS2V5cyA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMoc291cmNlKTtcblxuICAgIGZvciAoaSA9IDA7IGkgPCBzb3VyY2VTeW1ib2xLZXlzLmxlbmd0aDsgaSsrKSB7XG4gICAgICBrZXkgPSBzb3VyY2VTeW1ib2xLZXlzW2ldO1xuICAgICAgaWYgKGV4Y2x1ZGVkLmluZGV4T2Yoa2V5KSA+PSAwKSBjb250aW51ZTtcbiAgICAgIGlmICghT2JqZWN0LnByb3RvdHlwZS5wcm9wZXJ0eUlzRW51bWVyYWJsZS5jYWxsKHNvdXJjZSwga2V5KSkgY29udGludWU7XG4gICAgICB0YXJnZXRba2V5XSA9IHNvdXJjZVtrZXldO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiB0YXJnZXQ7XG59XG5cbmZ1bmN0aW9uIF90b0NvbnN1bWFibGVBcnJheShhcnIpIHtcbiAgcmV0dXJuIF9hcnJheVdpdGhvdXRIb2xlcyhhcnIpIHx8IF9pdGVyYWJsZVRvQXJyYXkoYXJyKSB8fCBfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkoYXJyKSB8fCBfbm9uSXRlcmFibGVTcHJlYWQoKTtcbn1cblxuZnVuY3Rpb24gX2FycmF5V2l0aG91dEhvbGVzKGFycikge1xuICBpZiAoQXJyYXkuaXNBcnJheShhcnIpKSByZXR1cm4gX2FycmF5TGlrZVRvQXJyYXkoYXJyKTtcbn1cblxuZnVuY3Rpb24gX2l0ZXJhYmxlVG9BcnJheShpdGVyKSB7XG4gIGlmICh0eXBlb2YgU3ltYm9sICE9PSBcInVuZGVmaW5lZFwiICYmIGl0ZXJbU3ltYm9sLml0ZXJhdG9yXSAhPSBudWxsIHx8IGl0ZXJbXCJAQGl0ZXJhdG9yXCJdICE9IG51bGwpIHJldHVybiBBcnJheS5mcm9tKGl0ZXIpO1xufVxuXG5mdW5jdGlvbiBfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkobywgbWluTGVuKSB7XG4gIGlmICghbykgcmV0dXJuO1xuICBpZiAodHlwZW9mIG8gPT09IFwic3RyaW5nXCIpIHJldHVybiBfYXJyYXlMaWtlVG9BcnJheShvLCBtaW5MZW4pO1xuICB2YXIgbiA9IE9iamVjdC5wcm90b3R5cGUudG9TdHJpbmcuY2FsbChvKS5zbGljZSg4LCAtMSk7XG4gIGlmIChuID09PSBcIk9iamVjdFwiICYmIG8uY29uc3RydWN0b3IpIG4gPSBvLmNvbnN0cnVjdG9yLm5hbWU7XG4gIGlmIChuID09PSBcIk1hcFwiIHx8IG4gPT09IFwiU2V0XCIpIHJldHVybiBBcnJheS5mcm9tKG8pO1xuICBpZiAobiA9PT0gXCJBcmd1bWVudHNcIiB8fCAvXig/OlVpfEkpbnQoPzo4fDE2fDMyKSg/OkNsYW1wZWQpP0FycmF5JC8udGVzdChuKSkgcmV0dXJuIF9hcnJheUxpa2VUb0FycmF5KG8sIG1pbkxlbik7XG59XG5cbmZ1bmN0aW9uIF9hcnJheUxpa2VUb0FycmF5KGFyciwgbGVuKSB7XG4gIGlmIChsZW4gPT0gbnVsbCB8fCBsZW4gPiBhcnIubGVuZ3RoKSBsZW4gPSBhcnIubGVuZ3RoO1xuXG4gIGZvciAodmFyIGkgPSAwLCBhcnIyID0gbmV3IEFycmF5KGxlbik7IGkgPCBsZW47IGkrKykgYXJyMltpXSA9IGFycltpXTtcblxuICByZXR1cm4gYXJyMjtcbn1cblxuZnVuY3Rpb24gX25vbkl0ZXJhYmxlU3ByZWFkKCkge1xuICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiSW52YWxpZCBhdHRlbXB0IHRvIHNwcmVhZCBub24taXRlcmFibGUgaW5zdGFuY2UuXFxuSW4gb3JkZXIgdG8gYmUgaXRlcmFibGUsIG5vbi1hcnJheSBvYmplY3RzIG11c3QgaGF2ZSBhIFtTeW1ib2wuaXRlcmF0b3JdKCkgbWV0aG9kLlwiKTtcbn1cblxudmFyIHZlcnNpb24gPSBcIjEuMTUuMFwiO1xuXG5mdW5jdGlvbiB1c2VyQWdlbnQocGF0dGVybikge1xuICBpZiAodHlwZW9mIHdpbmRvdyAhPT0gJ3VuZGVmaW5lZCcgJiYgd2luZG93Lm5hdmlnYXRvcikge1xuICAgIHJldHVybiAhISAvKkBfX1BVUkVfXyovbmF2aWdhdG9yLnVzZXJBZ2VudC5tYXRjaChwYXR0ZXJuKTtcbiAgfVxufVxuXG52YXIgSUUxMU9yTGVzcyA9IHVzZXJBZ2VudCgvKD86VHJpZGVudC4qcnZbIDpdPzExXFwufG1zaWV8aWVtb2JpbGV8V2luZG93cyBQaG9uZSkvaSk7XG52YXIgRWRnZSA9IHVzZXJBZ2VudCgvRWRnZS9pKTtcbnZhciBGaXJlRm94ID0gdXNlckFnZW50KC9maXJlZm94L2kpO1xudmFyIFNhZmFyaSA9IHVzZXJBZ2VudCgvc2FmYXJpL2kpICYmICF1c2VyQWdlbnQoL2Nocm9tZS9pKSAmJiAhdXNlckFnZW50KC9hbmRyb2lkL2kpO1xudmFyIElPUyA9IHVzZXJBZ2VudCgvaVAoYWR8b2R8aG9uZSkvaSk7XG52YXIgQ2hyb21lRm9yQW5kcm9pZCA9IHVzZXJBZ2VudCgvY2hyb21lL2kpICYmIHVzZXJBZ2VudCgvYW5kcm9pZC9pKTtcblxudmFyIGNhcHR1cmVNb2RlID0ge1xuICBjYXB0dXJlOiBmYWxzZSxcbiAgcGFzc2l2ZTogZmFsc2Vcbn07XG5cbmZ1bmN0aW9uIG9uKGVsLCBldmVudCwgZm4pIHtcbiAgZWwuYWRkRXZlbnRMaXN0ZW5lcihldmVudCwgZm4sICFJRTExT3JMZXNzICYmIGNhcHR1cmVNb2RlKTtcbn1cblxuZnVuY3Rpb24gb2ZmKGVsLCBldmVudCwgZm4pIHtcbiAgZWwucmVtb3ZlRXZlbnRMaXN0ZW5lcihldmVudCwgZm4sICFJRTExT3JMZXNzICYmIGNhcHR1cmVNb2RlKTtcbn1cblxuZnVuY3Rpb24gbWF0Y2hlcyhcbi8qKkhUTUxFbGVtZW50Ki9cbmVsLFxuLyoqU3RyaW5nKi9cbnNlbGVjdG9yKSB7XG4gIGlmICghc2VsZWN0b3IpIHJldHVybjtcbiAgc2VsZWN0b3JbMF0gPT09ICc+JyAmJiAoc2VsZWN0b3IgPSBzZWxlY3Rvci5zdWJzdHJpbmcoMSkpO1xuXG4gIGlmIChlbCkge1xuICAgIHRyeSB7XG4gICAgICBpZiAoZWwubWF0Y2hlcykge1xuICAgICAgICByZXR1cm4gZWwubWF0Y2hlcyhzZWxlY3Rvcik7XG4gICAgICB9IGVsc2UgaWYgKGVsLm1zTWF0Y2hlc1NlbGVjdG9yKSB7XG4gICAgICAgIHJldHVybiBlbC5tc01hdGNoZXNTZWxlY3RvcihzZWxlY3Rvcik7XG4gICAgICB9IGVsc2UgaWYgKGVsLndlYmtpdE1hdGNoZXNTZWxlY3Rvcikge1xuICAgICAgICByZXR1cm4gZWwud2Via2l0TWF0Y2hlc1NlbGVjdG9yKHNlbGVjdG9yKTtcbiAgICAgIH1cbiAgICB9IGNhdGNoIChfKSB7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGZhbHNlO1xufVxuXG5mdW5jdGlvbiBnZXRQYXJlbnRPckhvc3QoZWwpIHtcbiAgcmV0dXJuIGVsLmhvc3QgJiYgZWwgIT09IGRvY3VtZW50ICYmIGVsLmhvc3Qubm9kZVR5cGUgPyBlbC5ob3N0IDogZWwucGFyZW50Tm9kZTtcbn1cblxuZnVuY3Rpb24gY2xvc2VzdChcbi8qKkhUTUxFbGVtZW50Ki9cbmVsLFxuLyoqU3RyaW5nKi9cbnNlbGVjdG9yLFxuLyoqSFRNTEVsZW1lbnQqL1xuY3R4LCBpbmNsdWRlQ1RYKSB7XG4gIGlmIChlbCkge1xuICAgIGN0eCA9IGN0eCB8fCBkb2N1bWVudDtcblxuICAgIGRvIHtcbiAgICAgIGlmIChzZWxlY3RvciAhPSBudWxsICYmIChzZWxlY3RvclswXSA9PT0gJz4nID8gZWwucGFyZW50Tm9kZSA9PT0gY3R4ICYmIG1hdGNoZXMoZWwsIHNlbGVjdG9yKSA6IG1hdGNoZXMoZWwsIHNlbGVjdG9yKSkgfHwgaW5jbHVkZUNUWCAmJiBlbCA9PT0gY3R4KSB7XG4gICAgICAgIHJldHVybiBlbDtcbiAgICAgIH1cblxuICAgICAgaWYgKGVsID09PSBjdHgpIGJyZWFrO1xuICAgICAgLyoganNoaW50IGJvc3M6dHJ1ZSAqL1xuICAgIH0gd2hpbGUgKGVsID0gZ2V0UGFyZW50T3JIb3N0KGVsKSk7XG4gIH1cblxuICByZXR1cm4gbnVsbDtcbn1cblxudmFyIFJfU1BBQ0UgPSAvXFxzKy9nO1xuXG5mdW5jdGlvbiB0b2dnbGVDbGFzcyhlbCwgbmFtZSwgc3RhdGUpIHtcbiAgaWYgKGVsICYmIG5hbWUpIHtcbiAgICBpZiAoZWwuY2xhc3NMaXN0KSB7XG4gICAgICBlbC5jbGFzc0xpc3Rbc3RhdGUgPyAnYWRkJyA6ICdyZW1vdmUnXShuYW1lKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdmFyIGNsYXNzTmFtZSA9ICgnICcgKyBlbC5jbGFzc05hbWUgKyAnICcpLnJlcGxhY2UoUl9TUEFDRSwgJyAnKS5yZXBsYWNlKCcgJyArIG5hbWUgKyAnICcsICcgJyk7XG4gICAgICBlbC5jbGFzc05hbWUgPSAoY2xhc3NOYW1lICsgKHN0YXRlID8gJyAnICsgbmFtZSA6ICcnKSkucmVwbGFjZShSX1NQQUNFLCAnICcpO1xuICAgIH1cbiAgfVxufVxuXG5mdW5jdGlvbiBjc3MoZWwsIHByb3AsIHZhbCkge1xuICB2YXIgc3R5bGUgPSBlbCAmJiBlbC5zdHlsZTtcblxuICBpZiAoc3R5bGUpIHtcbiAgICBpZiAodmFsID09PSB2b2lkIDApIHtcbiAgICAgIGlmIChkb2N1bWVudC5kZWZhdWx0VmlldyAmJiBkb2N1bWVudC5kZWZhdWx0Vmlldy5nZXRDb21wdXRlZFN0eWxlKSB7XG4gICAgICAgIHZhbCA9IGRvY3VtZW50LmRlZmF1bHRWaWV3LmdldENvbXB1dGVkU3R5bGUoZWwsICcnKTtcbiAgICAgIH0gZWxzZSBpZiAoZWwuY3VycmVudFN0eWxlKSB7XG4gICAgICAgIHZhbCA9IGVsLmN1cnJlbnRTdHlsZTtcbiAgICAgIH1cblxuICAgICAgcmV0dXJuIHByb3AgPT09IHZvaWQgMCA/IHZhbCA6IHZhbFtwcm9wXTtcbiAgICB9IGVsc2Uge1xuICAgICAgaWYgKCEocHJvcCBpbiBzdHlsZSkgJiYgcHJvcC5pbmRleE9mKCd3ZWJraXQnKSA9PT0gLTEpIHtcbiAgICAgICAgcHJvcCA9ICctd2Via2l0LScgKyBwcm9wO1xuICAgICAgfVxuXG4gICAgICBzdHlsZVtwcm9wXSA9IHZhbCArICh0eXBlb2YgdmFsID09PSAnc3RyaW5nJyA/ICcnIDogJ3B4Jyk7XG4gICAgfVxuICB9XG59XG5cbmZ1bmN0aW9uIG1hdHJpeChlbCwgc2VsZk9ubHkpIHtcbiAgdmFyIGFwcGxpZWRUcmFuc2Zvcm1zID0gJyc7XG5cbiAgaWYgKHR5cGVvZiBlbCA9PT0gJ3N0cmluZycpIHtcbiAgICBhcHBsaWVkVHJhbnNmb3JtcyA9IGVsO1xuICB9IGVsc2Uge1xuICAgIGRvIHtcbiAgICAgIHZhciB0cmFuc2Zvcm0gPSBjc3MoZWwsICd0cmFuc2Zvcm0nKTtcblxuICAgICAgaWYgKHRyYW5zZm9ybSAmJiB0cmFuc2Zvcm0gIT09ICdub25lJykge1xuICAgICAgICBhcHBsaWVkVHJhbnNmb3JtcyA9IHRyYW5zZm9ybSArICcgJyArIGFwcGxpZWRUcmFuc2Zvcm1zO1xuICAgICAgfVxuICAgICAgLyoganNoaW50IGJvc3M6dHJ1ZSAqL1xuXG4gICAgfSB3aGlsZSAoIXNlbGZPbmx5ICYmIChlbCA9IGVsLnBhcmVudE5vZGUpKTtcbiAgfVxuXG4gIHZhciBtYXRyaXhGbiA9IHdpbmRvdy5ET01NYXRyaXggfHwgd2luZG93LldlYktpdENTU01hdHJpeCB8fCB3aW5kb3cuQ1NTTWF0cml4IHx8IHdpbmRvdy5NU0NTU01hdHJpeDtcbiAgLypqc2hpbnQgLVcwNTYgKi9cblxuICByZXR1cm4gbWF0cml4Rm4gJiYgbmV3IG1hdHJpeEZuKGFwcGxpZWRUcmFuc2Zvcm1zKTtcbn1cblxuZnVuY3Rpb24gZmluZChjdHgsIHRhZ05hbWUsIGl0ZXJhdG9yKSB7XG4gIGlmIChjdHgpIHtcbiAgICB2YXIgbGlzdCA9IGN0eC5nZXRFbGVtZW50c0J5VGFnTmFtZSh0YWdOYW1lKSxcbiAgICAgICAgaSA9IDAsXG4gICAgICAgIG4gPSBsaXN0Lmxlbmd0aDtcblxuICAgIGlmIChpdGVyYXRvcikge1xuICAgICAgZm9yICg7IGkgPCBuOyBpKyspIHtcbiAgICAgICAgaXRlcmF0b3IobGlzdFtpXSwgaSk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIGxpc3Q7XG4gIH1cblxuICByZXR1cm4gW107XG59XG5cbmZ1bmN0aW9uIGdldFdpbmRvd1Njcm9sbGluZ0VsZW1lbnQoKSB7XG4gIHZhciBzY3JvbGxpbmdFbGVtZW50ID0gZG9jdW1lbnQuc2Nyb2xsaW5nRWxlbWVudDtcblxuICBpZiAoc2Nyb2xsaW5nRWxlbWVudCkge1xuICAgIHJldHVybiBzY3JvbGxpbmdFbGVtZW50O1xuICB9IGVsc2Uge1xuICAgIHJldHVybiBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQ7XG4gIH1cbn1cbi8qKlxuICogUmV0dXJucyB0aGUgXCJib3VuZGluZyBjbGllbnQgcmVjdFwiIG9mIGdpdmVuIGVsZW1lbnRcbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSBlbCAgICAgICAgICAgICAgICAgICAgICAgVGhlIGVsZW1lbnQgd2hvc2UgYm91bmRpbmdDbGllbnRSZWN0IGlzIHdhbnRlZFxuICogQHBhcmFtICB7W0Jvb2xlYW5dfSByZWxhdGl2ZVRvQ29udGFpbmluZ0Jsb2NrICBXaGV0aGVyIHRoZSByZWN0IHNob3VsZCBiZSByZWxhdGl2ZSB0byB0aGUgY29udGFpbmluZyBibG9jayBvZiAoaW5jbHVkaW5nKSB0aGUgY29udGFpbmVyXG4gKiBAcGFyYW0gIHtbQm9vbGVhbl19IHJlbGF0aXZlVG9Ob25TdGF0aWNQYXJlbnQgIFdoZXRoZXIgdGhlIHJlY3Qgc2hvdWxkIGJlIHJlbGF0aXZlIHRvIHRoZSByZWxhdGl2ZSBwYXJlbnQgb2YgKGluY2x1ZGluZykgdGhlIGNvbnRhaWVuclxuICogQHBhcmFtICB7W0Jvb2xlYW5dfSB1bmRvU2NhbGUgICAgICAgICAgICAgICAgICBXaGV0aGVyIHRoZSBjb250YWluZXIncyBzY2FsZSgpIHNob3VsZCBiZSB1bmRvbmVcbiAqIEBwYXJhbSAge1tIVE1MRWxlbWVudF19IGNvbnRhaW5lciAgICAgICAgICAgICAgVGhlIHBhcmVudCB0aGUgZWxlbWVudCB3aWxsIGJlIHBsYWNlZCBpblxuICogQHJldHVybiB7T2JqZWN0fSAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBUaGUgYm91bmRpbmdDbGllbnRSZWN0IG9mIGVsLCB3aXRoIHNwZWNpZmllZCBhZGp1c3RtZW50c1xuICovXG5cblxuZnVuY3Rpb24gZ2V0UmVjdChlbCwgcmVsYXRpdmVUb0NvbnRhaW5pbmdCbG9jaywgcmVsYXRpdmVUb05vblN0YXRpY1BhcmVudCwgdW5kb1NjYWxlLCBjb250YWluZXIpIHtcbiAgaWYgKCFlbC5nZXRCb3VuZGluZ0NsaWVudFJlY3QgJiYgZWwgIT09IHdpbmRvdykgcmV0dXJuO1xuICB2YXIgZWxSZWN0LCB0b3AsIGxlZnQsIGJvdHRvbSwgcmlnaHQsIGhlaWdodCwgd2lkdGg7XG5cbiAgaWYgKGVsICE9PSB3aW5kb3cgJiYgZWwucGFyZW50Tm9kZSAmJiBlbCAhPT0gZ2V0V2luZG93U2Nyb2xsaW5nRWxlbWVudCgpKSB7XG4gICAgZWxSZWN0ID0gZWwuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7XG4gICAgdG9wID0gZWxSZWN0LnRvcDtcbiAgICBsZWZ0ID0gZWxSZWN0LmxlZnQ7XG4gICAgYm90dG9tID0gZWxSZWN0LmJvdHRvbTtcbiAgICByaWdodCA9IGVsUmVjdC5yaWdodDtcbiAgICBoZWlnaHQgPSBlbFJlY3QuaGVpZ2h0O1xuICAgIHdpZHRoID0gZWxSZWN0LndpZHRoO1xuICB9IGVsc2Uge1xuICAgIHRvcCA9IDA7XG4gICAgbGVmdCA9IDA7XG4gICAgYm90dG9tID0gd2luZG93LmlubmVySGVpZ2h0O1xuICAgIHJpZ2h0ID0gd2luZG93LmlubmVyV2lkdGg7XG4gICAgaGVpZ2h0ID0gd2luZG93LmlubmVySGVpZ2h0O1xuICAgIHdpZHRoID0gd2luZG93LmlubmVyV2lkdGg7XG4gIH1cblxuICBpZiAoKHJlbGF0aXZlVG9Db250YWluaW5nQmxvY2sgfHwgcmVsYXRpdmVUb05vblN0YXRpY1BhcmVudCkgJiYgZWwgIT09IHdpbmRvdykge1xuICAgIC8vIEFkanVzdCBmb3IgdHJhbnNsYXRlKClcbiAgICBjb250YWluZXIgPSBjb250YWluZXIgfHwgZWwucGFyZW50Tm9kZTsgLy8gc29sdmVzICMxMTIzIChzZWU6IGh0dHBzOi8vc3RhY2tvdmVyZmxvdy5jb20vYS8zNzk1MzgwNi82MDg4MzEyKVxuICAgIC8vIE5vdCBuZWVkZWQgb24gPD0gSUUxMVxuXG4gICAgaWYgKCFJRTExT3JMZXNzKSB7XG4gICAgICBkbyB7XG4gICAgICAgIGlmIChjb250YWluZXIgJiYgY29udGFpbmVyLmdldEJvdW5kaW5nQ2xpZW50UmVjdCAmJiAoY3NzKGNvbnRhaW5lciwgJ3RyYW5zZm9ybScpICE9PSAnbm9uZScgfHwgcmVsYXRpdmVUb05vblN0YXRpY1BhcmVudCAmJiBjc3MoY29udGFpbmVyLCAncG9zaXRpb24nKSAhPT0gJ3N0YXRpYycpKSB7XG4gICAgICAgICAgdmFyIGNvbnRhaW5lclJlY3QgPSBjb250YWluZXIuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7IC8vIFNldCByZWxhdGl2ZSB0byBlZGdlcyBvZiBwYWRkaW5nIGJveCBvZiBjb250YWluZXJcblxuICAgICAgICAgIHRvcCAtPSBjb250YWluZXJSZWN0LnRvcCArIHBhcnNlSW50KGNzcyhjb250YWluZXIsICdib3JkZXItdG9wLXdpZHRoJykpO1xuICAgICAgICAgIGxlZnQgLT0gY29udGFpbmVyUmVjdC5sZWZ0ICsgcGFyc2VJbnQoY3NzKGNvbnRhaW5lciwgJ2JvcmRlci1sZWZ0LXdpZHRoJykpO1xuICAgICAgICAgIGJvdHRvbSA9IHRvcCArIGVsUmVjdC5oZWlnaHQ7XG4gICAgICAgICAgcmlnaHQgPSBsZWZ0ICsgZWxSZWN0LndpZHRoO1xuICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgICAgIC8qIGpzaGludCBib3NzOnRydWUgKi9cblxuICAgICAgfSB3aGlsZSAoY29udGFpbmVyID0gY29udGFpbmVyLnBhcmVudE5vZGUpO1xuICAgIH1cbiAgfVxuXG4gIGlmICh1bmRvU2NhbGUgJiYgZWwgIT09IHdpbmRvdykge1xuICAgIC8vIEFkanVzdCBmb3Igc2NhbGUoKVxuICAgIHZhciBlbE1hdHJpeCA9IG1hdHJpeChjb250YWluZXIgfHwgZWwpLFxuICAgICAgICBzY2FsZVggPSBlbE1hdHJpeCAmJiBlbE1hdHJpeC5hLFxuICAgICAgICBzY2FsZVkgPSBlbE1hdHJpeCAmJiBlbE1hdHJpeC5kO1xuXG4gICAgaWYgKGVsTWF0cml4KSB7XG4gICAgICB0b3AgLz0gc2NhbGVZO1xuICAgICAgbGVmdCAvPSBzY2FsZVg7XG4gICAgICB3aWR0aCAvPSBzY2FsZVg7XG4gICAgICBoZWlnaHQgLz0gc2NhbGVZO1xuICAgICAgYm90dG9tID0gdG9wICsgaGVpZ2h0O1xuICAgICAgcmlnaHQgPSBsZWZ0ICsgd2lkdGg7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHtcbiAgICB0b3A6IHRvcCxcbiAgICBsZWZ0OiBsZWZ0LFxuICAgIGJvdHRvbTogYm90dG9tLFxuICAgIHJpZ2h0OiByaWdodCxcbiAgICB3aWR0aDogd2lkdGgsXG4gICAgaGVpZ2h0OiBoZWlnaHRcbiAgfTtcbn1cbi8qKlxuICogQ2hlY2tzIGlmIGEgc2lkZSBvZiBhbiBlbGVtZW50IGlzIHNjcm9sbGVkIHBhc3QgYSBzaWRlIG9mIGl0cyBwYXJlbnRzXG4gKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gIGVsICAgICAgICAgICBUaGUgZWxlbWVudCB3aG8ncyBzaWRlIGJlaW5nIHNjcm9sbGVkIG91dCBvZiB2aWV3IGlzIGluIHF1ZXN0aW9uXG4gKiBAcGFyYW0gIHtTdHJpbmd9ICAgICAgIGVsU2lkZSAgICAgICBTaWRlIG9mIHRoZSBlbGVtZW50IGluIHF1ZXN0aW9uICgndG9wJywgJ2xlZnQnLCAncmlnaHQnLCAnYm90dG9tJylcbiAqIEBwYXJhbSAge1N0cmluZ30gICAgICAgcGFyZW50U2lkZSAgIFNpZGUgb2YgdGhlIHBhcmVudCBpbiBxdWVzdGlvbiAoJ3RvcCcsICdsZWZ0JywgJ3JpZ2h0JywgJ2JvdHRvbScpXG4gKiBAcmV0dXJuIHtIVE1MRWxlbWVudH0gICAgICAgICAgICAgICBUaGUgcGFyZW50IHNjcm9sbCBlbGVtZW50IHRoYXQgdGhlIGVsJ3Mgc2lkZSBpcyBzY3JvbGxlZCBwYXN0LCBvciBudWxsIGlmIHRoZXJlIGlzIG5vIHN1Y2ggZWxlbWVudFxuICovXG5cblxuZnVuY3Rpb24gaXNTY3JvbGxlZFBhc3QoZWwsIGVsU2lkZSwgcGFyZW50U2lkZSkge1xuICB2YXIgcGFyZW50ID0gZ2V0UGFyZW50QXV0b1Njcm9sbEVsZW1lbnQoZWwsIHRydWUpLFxuICAgICAgZWxTaWRlVmFsID0gZ2V0UmVjdChlbClbZWxTaWRlXTtcbiAgLyoganNoaW50IGJvc3M6dHJ1ZSAqL1xuXG4gIHdoaWxlIChwYXJlbnQpIHtcbiAgICB2YXIgcGFyZW50U2lkZVZhbCA9IGdldFJlY3QocGFyZW50KVtwYXJlbnRTaWRlXSxcbiAgICAgICAgdmlzaWJsZSA9IHZvaWQgMDtcblxuICAgIGlmIChwYXJlbnRTaWRlID09PSAndG9wJyB8fCBwYXJlbnRTaWRlID09PSAnbGVmdCcpIHtcbiAgICAgIHZpc2libGUgPSBlbFNpZGVWYWwgPj0gcGFyZW50U2lkZVZhbDtcbiAgICB9IGVsc2Uge1xuICAgICAgdmlzaWJsZSA9IGVsU2lkZVZhbCA8PSBwYXJlbnRTaWRlVmFsO1xuICAgIH1cblxuICAgIGlmICghdmlzaWJsZSkgcmV0dXJuIHBhcmVudDtcbiAgICBpZiAocGFyZW50ID09PSBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCkpIGJyZWFrO1xuICAgIHBhcmVudCA9IGdldFBhcmVudEF1dG9TY3JvbGxFbGVtZW50KHBhcmVudCwgZmFsc2UpO1xuICB9XG5cbiAgcmV0dXJuIGZhbHNlO1xufVxuLyoqXG4gKiBHZXRzIG50aCBjaGlsZCBvZiBlbCwgaWdub3JpbmcgaGlkZGVuIGNoaWxkcmVuLCBzb3J0YWJsZSdzIGVsZW1lbnRzIChkb2VzIG5vdCBpZ25vcmUgY2xvbmUgaWYgaXQncyB2aXNpYmxlKVxuICogYW5kIG5vbi1kcmFnZ2FibGUgZWxlbWVudHNcbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSBlbCAgICAgICBUaGUgcGFyZW50IGVsZW1lbnRcbiAqIEBwYXJhbSAge051bWJlcn0gY2hpbGROdW0gICAgICBUaGUgaW5kZXggb2YgdGhlIGNoaWxkXG4gKiBAcGFyYW0gIHtPYmplY3R9IG9wdGlvbnMgICAgICAgUGFyZW50IFNvcnRhYmxlJ3Mgb3B0aW9uc1xuICogQHJldHVybiB7SFRNTEVsZW1lbnR9ICAgICAgICAgIFRoZSBjaGlsZCBhdCBpbmRleCBjaGlsZE51bSwgb3IgbnVsbCBpZiBub3QgZm91bmRcbiAqL1xuXG5cbmZ1bmN0aW9uIGdldENoaWxkKGVsLCBjaGlsZE51bSwgb3B0aW9ucywgaW5jbHVkZURyYWdFbCkge1xuICB2YXIgY3VycmVudENoaWxkID0gMCxcbiAgICAgIGkgPSAwLFxuICAgICAgY2hpbGRyZW4gPSBlbC5jaGlsZHJlbjtcblxuICB3aGlsZSAoaSA8IGNoaWxkcmVuLmxlbmd0aCkge1xuICAgIGlmIChjaGlsZHJlbltpXS5zdHlsZS5kaXNwbGF5ICE9PSAnbm9uZScgJiYgY2hpbGRyZW5baV0gIT09IFNvcnRhYmxlLmdob3N0ICYmIChpbmNsdWRlRHJhZ0VsIHx8IGNoaWxkcmVuW2ldICE9PSBTb3J0YWJsZS5kcmFnZ2VkKSAmJiBjbG9zZXN0KGNoaWxkcmVuW2ldLCBvcHRpb25zLmRyYWdnYWJsZSwgZWwsIGZhbHNlKSkge1xuICAgICAgaWYgKGN1cnJlbnRDaGlsZCA9PT0gY2hpbGROdW0pIHtcbiAgICAgICAgcmV0dXJuIGNoaWxkcmVuW2ldO1xuICAgICAgfVxuXG4gICAgICBjdXJyZW50Q2hpbGQrKztcbiAgICB9XG5cbiAgICBpKys7XG4gIH1cblxuICByZXR1cm4gbnVsbDtcbn1cbi8qKlxuICogR2V0cyB0aGUgbGFzdCBjaGlsZCBpbiB0aGUgZWwsIGlnbm9yaW5nIGdob3N0RWwgb3IgaW52aXNpYmxlIGVsZW1lbnRzIChjbG9uZXMpXG4gKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gZWwgICAgICAgUGFyZW50IGVsZW1lbnRcbiAqIEBwYXJhbSAge3NlbGVjdG9yfSBzZWxlY3RvciAgICBBbnkgb3RoZXIgZWxlbWVudHMgdGhhdCBzaG91bGQgYmUgaWdub3JlZFxuICogQHJldHVybiB7SFRNTEVsZW1lbnR9ICAgICAgICAgIFRoZSBsYXN0IGNoaWxkLCBpZ25vcmluZyBnaG9zdEVsXG4gKi9cblxuXG5mdW5jdGlvbiBsYXN0Q2hpbGQoZWwsIHNlbGVjdG9yKSB7XG4gIHZhciBsYXN0ID0gZWwubGFzdEVsZW1lbnRDaGlsZDtcblxuICB3aGlsZSAobGFzdCAmJiAobGFzdCA9PT0gU29ydGFibGUuZ2hvc3QgfHwgY3NzKGxhc3QsICdkaXNwbGF5JykgPT09ICdub25lJyB8fCBzZWxlY3RvciAmJiAhbWF0Y2hlcyhsYXN0LCBzZWxlY3RvcikpKSB7XG4gICAgbGFzdCA9IGxhc3QucHJldmlvdXNFbGVtZW50U2libGluZztcbiAgfVxuXG4gIHJldHVybiBsYXN0IHx8IG51bGw7XG59XG4vKipcbiAqIFJldHVybnMgdGhlIGluZGV4IG9mIGFuIGVsZW1lbnQgd2l0aGluIGl0cyBwYXJlbnQgZm9yIGEgc2VsZWN0ZWQgc2V0IG9mXG4gKiBlbGVtZW50c1xuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IGVsXG4gKiBAcGFyYW0gIHtzZWxlY3Rvcn0gc2VsZWN0b3JcbiAqIEByZXR1cm4ge251bWJlcn1cbiAqL1xuXG5cbmZ1bmN0aW9uIGluZGV4KGVsLCBzZWxlY3Rvcikge1xuICB2YXIgaW5kZXggPSAwO1xuXG4gIGlmICghZWwgfHwgIWVsLnBhcmVudE5vZGUpIHtcbiAgICByZXR1cm4gLTE7XG4gIH1cbiAgLyoganNoaW50IGJvc3M6dHJ1ZSAqL1xuXG5cbiAgd2hpbGUgKGVsID0gZWwucHJldmlvdXNFbGVtZW50U2libGluZykge1xuICAgIGlmIChlbC5ub2RlTmFtZS50b1VwcGVyQ2FzZSgpICE9PSAnVEVNUExBVEUnICYmIGVsICE9PSBTb3J0YWJsZS5jbG9uZSAmJiAoIXNlbGVjdG9yIHx8IG1hdGNoZXMoZWwsIHNlbGVjdG9yKSkpIHtcbiAgICAgIGluZGV4Kys7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGluZGV4O1xufVxuLyoqXG4gKiBSZXR1cm5zIHRoZSBzY3JvbGwgb2Zmc2V0IG9mIHRoZSBnaXZlbiBlbGVtZW50LCBhZGRlZCB3aXRoIGFsbCB0aGUgc2Nyb2xsIG9mZnNldHMgb2YgcGFyZW50IGVsZW1lbnRzLlxuICogVGhlIHZhbHVlIGlzIHJldHVybmVkIGluIHJlYWwgcGl4ZWxzLlxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IGVsXG4gKiBAcmV0dXJuIHtBcnJheX0gICAgICAgICAgICAgT2Zmc2V0cyBpbiB0aGUgZm9ybWF0IG9mIFtsZWZ0LCB0b3BdXG4gKi9cblxuXG5mdW5jdGlvbiBnZXRSZWxhdGl2ZVNjcm9sbE9mZnNldChlbCkge1xuICB2YXIgb2Zmc2V0TGVmdCA9IDAsXG4gICAgICBvZmZzZXRUb3AgPSAwLFxuICAgICAgd2luU2Nyb2xsZXIgPSBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCk7XG5cbiAgaWYgKGVsKSB7XG4gICAgZG8ge1xuICAgICAgdmFyIGVsTWF0cml4ID0gbWF0cml4KGVsKSxcbiAgICAgICAgICBzY2FsZVggPSBlbE1hdHJpeC5hLFxuICAgICAgICAgIHNjYWxlWSA9IGVsTWF0cml4LmQ7XG4gICAgICBvZmZzZXRMZWZ0ICs9IGVsLnNjcm9sbExlZnQgKiBzY2FsZVg7XG4gICAgICBvZmZzZXRUb3AgKz0gZWwuc2Nyb2xsVG9wICogc2NhbGVZO1xuICAgIH0gd2hpbGUgKGVsICE9PSB3aW5TY3JvbGxlciAmJiAoZWwgPSBlbC5wYXJlbnROb2RlKSk7XG4gIH1cblxuICByZXR1cm4gW29mZnNldExlZnQsIG9mZnNldFRvcF07XG59XG4vKipcbiAqIFJldHVybnMgdGhlIGluZGV4IG9mIHRoZSBvYmplY3Qgd2l0aGluIHRoZSBnaXZlbiBhcnJheVxuICogQHBhcmFtICB7QXJyYXl9IGFyciAgIEFycmF5IHRoYXQgbWF5IG9yIG1heSBub3QgaG9sZCB0aGUgb2JqZWN0XG4gKiBAcGFyYW0gIHtPYmplY3R9IG9iaiAgQW4gb2JqZWN0IHRoYXQgaGFzIGEga2V5LXZhbHVlIHBhaXIgdW5pcXVlIHRvIGFuZCBpZGVudGljYWwgdG8gYSBrZXktdmFsdWUgcGFpciBpbiB0aGUgb2JqZWN0IHlvdSB3YW50IHRvIGZpbmRcbiAqIEByZXR1cm4ge051bWJlcn0gICAgICBUaGUgaW5kZXggb2YgdGhlIG9iamVjdCBpbiB0aGUgYXJyYXksIG9yIC0xXG4gKi9cblxuXG5mdW5jdGlvbiBpbmRleE9mT2JqZWN0KGFyciwgb2JqKSB7XG4gIGZvciAodmFyIGkgaW4gYXJyKSB7XG4gICAgaWYgKCFhcnIuaGFzT3duUHJvcGVydHkoaSkpIGNvbnRpbnVlO1xuXG4gICAgZm9yICh2YXIga2V5IGluIG9iaikge1xuICAgICAgaWYgKG9iai5oYXNPd25Qcm9wZXJ0eShrZXkpICYmIG9ialtrZXldID09PSBhcnJbaV1ba2V5XSkgcmV0dXJuIE51bWJlcihpKTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gLTE7XG59XG5cbmZ1bmN0aW9uIGdldFBhcmVudEF1dG9TY3JvbGxFbGVtZW50KGVsLCBpbmNsdWRlU2VsZikge1xuICAvLyBza2lwIHRvIHdpbmRvd1xuICBpZiAoIWVsIHx8ICFlbC5nZXRCb3VuZGluZ0NsaWVudFJlY3QpIHJldHVybiBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCk7XG4gIHZhciBlbGVtID0gZWw7XG4gIHZhciBnb3RTZWxmID0gZmFsc2U7XG5cbiAgZG8ge1xuICAgIC8vIHdlIGRvbid0IG5lZWQgdG8gZ2V0IGVsZW0gY3NzIGlmIGl0IGlzbid0IGV2ZW4gb3ZlcmZsb3dpbmcgaW4gdGhlIGZpcnN0IHBsYWNlIChwZXJmb3JtYW5jZSlcbiAgICBpZiAoZWxlbS5jbGllbnRXaWR0aCA8IGVsZW0uc2Nyb2xsV2lkdGggfHwgZWxlbS5jbGllbnRIZWlnaHQgPCBlbGVtLnNjcm9sbEhlaWdodCkge1xuICAgICAgdmFyIGVsZW1DU1MgPSBjc3MoZWxlbSk7XG5cbiAgICAgIGlmIChlbGVtLmNsaWVudFdpZHRoIDwgZWxlbS5zY3JvbGxXaWR0aCAmJiAoZWxlbUNTUy5vdmVyZmxvd1ggPT0gJ2F1dG8nIHx8IGVsZW1DU1Mub3ZlcmZsb3dYID09ICdzY3JvbGwnKSB8fCBlbGVtLmNsaWVudEhlaWdodCA8IGVsZW0uc2Nyb2xsSGVpZ2h0ICYmIChlbGVtQ1NTLm92ZXJmbG93WSA9PSAnYXV0bycgfHwgZWxlbUNTUy5vdmVyZmxvd1kgPT0gJ3Njcm9sbCcpKSB7XG4gICAgICAgIGlmICghZWxlbS5nZXRCb3VuZGluZ0NsaWVudFJlY3QgfHwgZWxlbSA9PT0gZG9jdW1lbnQuYm9keSkgcmV0dXJuIGdldFdpbmRvd1Njcm9sbGluZ0VsZW1lbnQoKTtcbiAgICAgICAgaWYgKGdvdFNlbGYgfHwgaW5jbHVkZVNlbGYpIHJldHVybiBlbGVtO1xuICAgICAgICBnb3RTZWxmID0gdHJ1ZTtcbiAgICAgIH1cbiAgICB9XG4gICAgLyoganNoaW50IGJvc3M6dHJ1ZSAqL1xuXG4gIH0gd2hpbGUgKGVsZW0gPSBlbGVtLnBhcmVudE5vZGUpO1xuXG4gIHJldHVybiBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCk7XG59XG5cbmZ1bmN0aW9uIGV4dGVuZChkc3QsIHNyYykge1xuICBpZiAoZHN0ICYmIHNyYykge1xuICAgIGZvciAodmFyIGtleSBpbiBzcmMpIHtcbiAgICAgIGlmIChzcmMuaGFzT3duUHJvcGVydHkoa2V5KSkge1xuICAgICAgICBkc3Rba2V5XSA9IHNyY1trZXldO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIHJldHVybiBkc3Q7XG59XG5cbmZ1bmN0aW9uIGlzUmVjdEVxdWFsKHJlY3QxLCByZWN0Mikge1xuICByZXR1cm4gTWF0aC5yb3VuZChyZWN0MS50b3ApID09PSBNYXRoLnJvdW5kKHJlY3QyLnRvcCkgJiYgTWF0aC5yb3VuZChyZWN0MS5sZWZ0KSA9PT0gTWF0aC5yb3VuZChyZWN0Mi5sZWZ0KSAmJiBNYXRoLnJvdW5kKHJlY3QxLmhlaWdodCkgPT09IE1hdGgucm91bmQocmVjdDIuaGVpZ2h0KSAmJiBNYXRoLnJvdW5kKHJlY3QxLndpZHRoKSA9PT0gTWF0aC5yb3VuZChyZWN0Mi53aWR0aCk7XG59XG5cbnZhciBfdGhyb3R0bGVUaW1lb3V0O1xuXG5mdW5jdGlvbiB0aHJvdHRsZShjYWxsYmFjaywgbXMpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uICgpIHtcbiAgICBpZiAoIV90aHJvdHRsZVRpbWVvdXQpIHtcbiAgICAgIHZhciBhcmdzID0gYXJndW1lbnRzLFxuICAgICAgICAgIF90aGlzID0gdGhpcztcblxuICAgICAgaWYgKGFyZ3MubGVuZ3RoID09PSAxKSB7XG4gICAgICAgIGNhbGxiYWNrLmNhbGwoX3RoaXMsIGFyZ3NbMF0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY2FsbGJhY2suYXBwbHkoX3RoaXMsIGFyZ3MpO1xuICAgICAgfVxuXG4gICAgICBfdGhyb3R0bGVUaW1lb3V0ID0gc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgIF90aHJvdHRsZVRpbWVvdXQgPSB2b2lkIDA7XG4gICAgICB9LCBtcyk7XG4gICAgfVxuICB9O1xufVxuXG5mdW5jdGlvbiBjYW5jZWxUaHJvdHRsZSgpIHtcbiAgY2xlYXJUaW1lb3V0KF90aHJvdHRsZVRpbWVvdXQpO1xuICBfdGhyb3R0bGVUaW1lb3V0ID0gdm9pZCAwO1xufVxuXG5mdW5jdGlvbiBzY3JvbGxCeShlbCwgeCwgeSkge1xuICBlbC5zY3JvbGxMZWZ0ICs9IHg7XG4gIGVsLnNjcm9sbFRvcCArPSB5O1xufVxuXG5mdW5jdGlvbiBjbG9uZShlbCkge1xuICB2YXIgUG9seW1lciA9IHdpbmRvdy5Qb2x5bWVyO1xuICB2YXIgJCA9IHdpbmRvdy5qUXVlcnkgfHwgd2luZG93LlplcHRvO1xuXG4gIGlmIChQb2x5bWVyICYmIFBvbHltZXIuZG9tKSB7XG4gICAgcmV0dXJuIFBvbHltZXIuZG9tKGVsKS5jbG9uZU5vZGUodHJ1ZSk7XG4gIH0gZWxzZSBpZiAoJCkge1xuICAgIHJldHVybiAkKGVsKS5jbG9uZSh0cnVlKVswXTtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gZWwuY2xvbmVOb2RlKHRydWUpO1xuICB9XG59XG5cbmZ1bmN0aW9uIHNldFJlY3QoZWwsIHJlY3QpIHtcbiAgY3NzKGVsLCAncG9zaXRpb24nLCAnYWJzb2x1dGUnKTtcbiAgY3NzKGVsLCAndG9wJywgcmVjdC50b3ApO1xuICBjc3MoZWwsICdsZWZ0JywgcmVjdC5sZWZ0KTtcbiAgY3NzKGVsLCAnd2lkdGgnLCByZWN0LndpZHRoKTtcbiAgY3NzKGVsLCAnaGVpZ2h0JywgcmVjdC5oZWlnaHQpO1xufVxuXG5mdW5jdGlvbiB1bnNldFJlY3QoZWwpIHtcbiAgY3NzKGVsLCAncG9zaXRpb24nLCAnJyk7XG4gIGNzcyhlbCwgJ3RvcCcsICcnKTtcbiAgY3NzKGVsLCAnbGVmdCcsICcnKTtcbiAgY3NzKGVsLCAnd2lkdGgnLCAnJyk7XG4gIGNzcyhlbCwgJ2hlaWdodCcsICcnKTtcbn1cblxudmFyIGV4cGFuZG8gPSAnU29ydGFibGUnICsgbmV3IERhdGUoKS5nZXRUaW1lKCk7XG5cbmZ1bmN0aW9uIEFuaW1hdGlvblN0YXRlTWFuYWdlcigpIHtcbiAgdmFyIGFuaW1hdGlvblN0YXRlcyA9IFtdLFxuICAgICAgYW5pbWF0aW9uQ2FsbGJhY2tJZDtcbiAgcmV0dXJuIHtcbiAgICBjYXB0dXJlQW5pbWF0aW9uU3RhdGU6IGZ1bmN0aW9uIGNhcHR1cmVBbmltYXRpb25TdGF0ZSgpIHtcbiAgICAgIGFuaW1hdGlvblN0YXRlcyA9IFtdO1xuICAgICAgaWYgKCF0aGlzLm9wdGlvbnMuYW5pbWF0aW9uKSByZXR1cm47XG4gICAgICB2YXIgY2hpbGRyZW4gPSBbXS5zbGljZS5jYWxsKHRoaXMuZWwuY2hpbGRyZW4pO1xuICAgICAgY2hpbGRyZW4uZm9yRWFjaChmdW5jdGlvbiAoY2hpbGQpIHtcbiAgICAgICAgaWYgKGNzcyhjaGlsZCwgJ2Rpc3BsYXknKSA9PT0gJ25vbmUnIHx8IGNoaWxkID09PSBTb3J0YWJsZS5naG9zdCkgcmV0dXJuO1xuICAgICAgICBhbmltYXRpb25TdGF0ZXMucHVzaCh7XG4gICAgICAgICAgdGFyZ2V0OiBjaGlsZCxcbiAgICAgICAgICByZWN0OiBnZXRSZWN0KGNoaWxkKVxuICAgICAgICB9KTtcblxuICAgICAgICB2YXIgZnJvbVJlY3QgPSBfb2JqZWN0U3ByZWFkMih7fSwgYW5pbWF0aW9uU3RhdGVzW2FuaW1hdGlvblN0YXRlcy5sZW5ndGggLSAxXS5yZWN0KTsgLy8gSWYgYW5pbWF0aW5nOiBjb21wZW5zYXRlIGZvciBjdXJyZW50IGFuaW1hdGlvblxuXG5cbiAgICAgICAgaWYgKGNoaWxkLnRoaXNBbmltYXRpb25EdXJhdGlvbikge1xuICAgICAgICAgIHZhciBjaGlsZE1hdHJpeCA9IG1hdHJpeChjaGlsZCwgdHJ1ZSk7XG5cbiAgICAgICAgICBpZiAoY2hpbGRNYXRyaXgpIHtcbiAgICAgICAgICAgIGZyb21SZWN0LnRvcCAtPSBjaGlsZE1hdHJpeC5mO1xuICAgICAgICAgICAgZnJvbVJlY3QubGVmdCAtPSBjaGlsZE1hdHJpeC5lO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGNoaWxkLmZyb21SZWN0ID0gZnJvbVJlY3Q7XG4gICAgICB9KTtcbiAgICB9LFxuICAgIGFkZEFuaW1hdGlvblN0YXRlOiBmdW5jdGlvbiBhZGRBbmltYXRpb25TdGF0ZShzdGF0ZSkge1xuICAgICAgYW5pbWF0aW9uU3RhdGVzLnB1c2goc3RhdGUpO1xuICAgIH0sXG4gICAgcmVtb3ZlQW5pbWF0aW9uU3RhdGU6IGZ1bmN0aW9uIHJlbW92ZUFuaW1hdGlvblN0YXRlKHRhcmdldCkge1xuICAgICAgYW5pbWF0aW9uU3RhdGVzLnNwbGljZShpbmRleE9mT2JqZWN0KGFuaW1hdGlvblN0YXRlcywge1xuICAgICAgICB0YXJnZXQ6IHRhcmdldFxuICAgICAgfSksIDEpO1xuICAgIH0sXG4gICAgYW5pbWF0ZUFsbDogZnVuY3Rpb24gYW5pbWF0ZUFsbChjYWxsYmFjaykge1xuICAgICAgdmFyIF90aGlzID0gdGhpcztcblxuICAgICAgaWYgKCF0aGlzLm9wdGlvbnMuYW5pbWF0aW9uKSB7XG4gICAgICAgIGNsZWFyVGltZW91dChhbmltYXRpb25DYWxsYmFja0lkKTtcbiAgICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ2Z1bmN0aW9uJykgY2FsbGJhY2soKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICB2YXIgYW5pbWF0aW5nID0gZmFsc2UsXG4gICAgICAgICAgYW5pbWF0aW9uVGltZSA9IDA7XG4gICAgICBhbmltYXRpb25TdGF0ZXMuZm9yRWFjaChmdW5jdGlvbiAoc3RhdGUpIHtcbiAgICAgICAgdmFyIHRpbWUgPSAwLFxuICAgICAgICAgICAgdGFyZ2V0ID0gc3RhdGUudGFyZ2V0LFxuICAgICAgICAgICAgZnJvbVJlY3QgPSB0YXJnZXQuZnJvbVJlY3QsXG4gICAgICAgICAgICB0b1JlY3QgPSBnZXRSZWN0KHRhcmdldCksXG4gICAgICAgICAgICBwcmV2RnJvbVJlY3QgPSB0YXJnZXQucHJldkZyb21SZWN0LFxuICAgICAgICAgICAgcHJldlRvUmVjdCA9IHRhcmdldC5wcmV2VG9SZWN0LFxuICAgICAgICAgICAgYW5pbWF0aW5nUmVjdCA9IHN0YXRlLnJlY3QsXG4gICAgICAgICAgICB0YXJnZXRNYXRyaXggPSBtYXRyaXgodGFyZ2V0LCB0cnVlKTtcblxuICAgICAgICBpZiAodGFyZ2V0TWF0cml4KSB7XG4gICAgICAgICAgLy8gQ29tcGVuc2F0ZSBmb3IgY3VycmVudCBhbmltYXRpb25cbiAgICAgICAgICB0b1JlY3QudG9wIC09IHRhcmdldE1hdHJpeC5mO1xuICAgICAgICAgIHRvUmVjdC5sZWZ0IC09IHRhcmdldE1hdHJpeC5lO1xuICAgICAgICB9XG5cbiAgICAgICAgdGFyZ2V0LnRvUmVjdCA9IHRvUmVjdDtcblxuICAgICAgICBpZiAodGFyZ2V0LnRoaXNBbmltYXRpb25EdXJhdGlvbikge1xuICAgICAgICAgIC8vIENvdWxkIGFsc28gY2hlY2sgaWYgYW5pbWF0aW5nUmVjdCBpcyBiZXR3ZWVuIGZyb21SZWN0IGFuZCB0b1JlY3RcbiAgICAgICAgICBpZiAoaXNSZWN0RXF1YWwocHJldkZyb21SZWN0LCB0b1JlY3QpICYmICFpc1JlY3RFcXVhbChmcm9tUmVjdCwgdG9SZWN0KSAmJiAvLyBNYWtlIHN1cmUgYW5pbWF0aW5nUmVjdCBpcyBvbiBsaW5lIGJldHdlZW4gdG9SZWN0ICYgZnJvbVJlY3RcbiAgICAgICAgICAoYW5pbWF0aW5nUmVjdC50b3AgLSB0b1JlY3QudG9wKSAvIChhbmltYXRpbmdSZWN0LmxlZnQgLSB0b1JlY3QubGVmdCkgPT09IChmcm9tUmVjdC50b3AgLSB0b1JlY3QudG9wKSAvIChmcm9tUmVjdC5sZWZ0IC0gdG9SZWN0LmxlZnQpKSB7XG4gICAgICAgICAgICAvLyBJZiByZXR1cm5pbmcgdG8gc2FtZSBwbGFjZSBhcyBzdGFydGVkIGZyb20gYW5pbWF0aW9uIGFuZCBvbiBzYW1lIGF4aXNcbiAgICAgICAgICAgIHRpbWUgPSBjYWxjdWxhdGVSZWFsVGltZShhbmltYXRpbmdSZWN0LCBwcmV2RnJvbVJlY3QsIHByZXZUb1JlY3QsIF90aGlzLm9wdGlvbnMpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSAvLyBpZiBmcm9tUmVjdCAhPSB0b1JlY3Q6IGFuaW1hdGVcblxuXG4gICAgICAgIGlmICghaXNSZWN0RXF1YWwodG9SZWN0LCBmcm9tUmVjdCkpIHtcbiAgICAgICAgICB0YXJnZXQucHJldkZyb21SZWN0ID0gZnJvbVJlY3Q7XG4gICAgICAgICAgdGFyZ2V0LnByZXZUb1JlY3QgPSB0b1JlY3Q7XG5cbiAgICAgICAgICBpZiAoIXRpbWUpIHtcbiAgICAgICAgICAgIHRpbWUgPSBfdGhpcy5vcHRpb25zLmFuaW1hdGlvbjtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBfdGhpcy5hbmltYXRlKHRhcmdldCwgYW5pbWF0aW5nUmVjdCwgdG9SZWN0LCB0aW1lKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0aW1lKSB7XG4gICAgICAgICAgYW5pbWF0aW5nID0gdHJ1ZTtcbiAgICAgICAgICBhbmltYXRpb25UaW1lID0gTWF0aC5tYXgoYW5pbWF0aW9uVGltZSwgdGltZSk7XG4gICAgICAgICAgY2xlYXJUaW1lb3V0KHRhcmdldC5hbmltYXRpb25SZXNldFRpbWVyKTtcbiAgICAgICAgICB0YXJnZXQuYW5pbWF0aW9uUmVzZXRUaW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdGFyZ2V0LmFuaW1hdGlvblRpbWUgPSAwO1xuICAgICAgICAgICAgdGFyZ2V0LnByZXZGcm9tUmVjdCA9IG51bGw7XG4gICAgICAgICAgICB0YXJnZXQuZnJvbVJlY3QgPSBudWxsO1xuICAgICAgICAgICAgdGFyZ2V0LnByZXZUb1JlY3QgPSBudWxsO1xuICAgICAgICAgICAgdGFyZ2V0LnRoaXNBbmltYXRpb25EdXJhdGlvbiA9IG51bGw7XG4gICAgICAgICAgfSwgdGltZSk7XG4gICAgICAgICAgdGFyZ2V0LnRoaXNBbmltYXRpb25EdXJhdGlvbiA9IHRpbWU7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgICAgY2xlYXJUaW1lb3V0KGFuaW1hdGlvbkNhbGxiYWNrSWQpO1xuXG4gICAgICBpZiAoIWFuaW1hdGluZykge1xuICAgICAgICBpZiAodHlwZW9mIGNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSBjYWxsYmFjaygpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYW5pbWF0aW9uQ2FsbGJhY2tJZCA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgIGlmICh0eXBlb2YgY2FsbGJhY2sgPT09ICdmdW5jdGlvbicpIGNhbGxiYWNrKCk7XG4gICAgICAgIH0sIGFuaW1hdGlvblRpbWUpO1xuICAgICAgfVxuXG4gICAgICBhbmltYXRpb25TdGF0ZXMgPSBbXTtcbiAgICB9LFxuICAgIGFuaW1hdGU6IGZ1bmN0aW9uIGFuaW1hdGUodGFyZ2V0LCBjdXJyZW50UmVjdCwgdG9SZWN0LCBkdXJhdGlvbikge1xuICAgICAgaWYgKGR1cmF0aW9uKSB7XG4gICAgICAgIGNzcyh0YXJnZXQsICd0cmFuc2l0aW9uJywgJycpO1xuICAgICAgICBjc3ModGFyZ2V0LCAndHJhbnNmb3JtJywgJycpO1xuICAgICAgICB2YXIgZWxNYXRyaXggPSBtYXRyaXgodGhpcy5lbCksXG4gICAgICAgICAgICBzY2FsZVggPSBlbE1hdHJpeCAmJiBlbE1hdHJpeC5hLFxuICAgICAgICAgICAgc2NhbGVZID0gZWxNYXRyaXggJiYgZWxNYXRyaXguZCxcbiAgICAgICAgICAgIHRyYW5zbGF0ZVggPSAoY3VycmVudFJlY3QubGVmdCAtIHRvUmVjdC5sZWZ0KSAvIChzY2FsZVggfHwgMSksXG4gICAgICAgICAgICB0cmFuc2xhdGVZID0gKGN1cnJlbnRSZWN0LnRvcCAtIHRvUmVjdC50b3ApIC8gKHNjYWxlWSB8fCAxKTtcbiAgICAgICAgdGFyZ2V0LmFuaW1hdGluZ1ggPSAhIXRyYW5zbGF0ZVg7XG4gICAgICAgIHRhcmdldC5hbmltYXRpbmdZID0gISF0cmFuc2xhdGVZO1xuICAgICAgICBjc3ModGFyZ2V0LCAndHJhbnNmb3JtJywgJ3RyYW5zbGF0ZTNkKCcgKyB0cmFuc2xhdGVYICsgJ3B4LCcgKyB0cmFuc2xhdGVZICsgJ3B4LDApJyk7XG4gICAgICAgIHRoaXMuZm9yUmVwYWludER1bW15ID0gcmVwYWludCh0YXJnZXQpOyAvLyByZXBhaW50XG5cbiAgICAgICAgY3NzKHRhcmdldCwgJ3RyYW5zaXRpb24nLCAndHJhbnNmb3JtICcgKyBkdXJhdGlvbiArICdtcycgKyAodGhpcy5vcHRpb25zLmVhc2luZyA/ICcgJyArIHRoaXMub3B0aW9ucy5lYXNpbmcgOiAnJykpO1xuICAgICAgICBjc3ModGFyZ2V0LCAndHJhbnNmb3JtJywgJ3RyYW5zbGF0ZTNkKDAsMCwwKScpO1xuICAgICAgICB0eXBlb2YgdGFyZ2V0LmFuaW1hdGVkID09PSAnbnVtYmVyJyAmJiBjbGVhclRpbWVvdXQodGFyZ2V0LmFuaW1hdGVkKTtcbiAgICAgICAgdGFyZ2V0LmFuaW1hdGVkID0gc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgY3NzKHRhcmdldCwgJ3RyYW5zaXRpb24nLCAnJyk7XG4gICAgICAgICAgY3NzKHRhcmdldCwgJ3RyYW5zZm9ybScsICcnKTtcbiAgICAgICAgICB0YXJnZXQuYW5pbWF0ZWQgPSBmYWxzZTtcbiAgICAgICAgICB0YXJnZXQuYW5pbWF0aW5nWCA9IGZhbHNlO1xuICAgICAgICAgIHRhcmdldC5hbmltYXRpbmdZID0gZmFsc2U7XG4gICAgICAgIH0sIGR1cmF0aW9uKTtcbiAgICAgIH1cbiAgICB9XG4gIH07XG59XG5cbmZ1bmN0aW9uIHJlcGFpbnQodGFyZ2V0KSB7XG4gIHJldHVybiB0YXJnZXQub2Zmc2V0V2lkdGg7XG59XG5cbmZ1bmN0aW9uIGNhbGN1bGF0ZVJlYWxUaW1lKGFuaW1hdGluZ1JlY3QsIGZyb21SZWN0LCB0b1JlY3QsIG9wdGlvbnMpIHtcbiAgcmV0dXJuIE1hdGguc3FydChNYXRoLnBvdyhmcm9tUmVjdC50b3AgLSBhbmltYXRpbmdSZWN0LnRvcCwgMikgKyBNYXRoLnBvdyhmcm9tUmVjdC5sZWZ0IC0gYW5pbWF0aW5nUmVjdC5sZWZ0LCAyKSkgLyBNYXRoLnNxcnQoTWF0aC5wb3coZnJvbVJlY3QudG9wIC0gdG9SZWN0LnRvcCwgMikgKyBNYXRoLnBvdyhmcm9tUmVjdC5sZWZ0IC0gdG9SZWN0LmxlZnQsIDIpKSAqIG9wdGlvbnMuYW5pbWF0aW9uO1xufVxuXG52YXIgcGx1Z2lucyA9IFtdO1xudmFyIGRlZmF1bHRzID0ge1xuICBpbml0aWFsaXplQnlEZWZhdWx0OiB0cnVlXG59O1xudmFyIFBsdWdpbk1hbmFnZXIgPSB7XG4gIG1vdW50OiBmdW5jdGlvbiBtb3VudChwbHVnaW4pIHtcbiAgICAvLyBTZXQgZGVmYXVsdCBzdGF0aWMgcHJvcGVydGllc1xuICAgIGZvciAodmFyIG9wdGlvbiBpbiBkZWZhdWx0cykge1xuICAgICAgaWYgKGRlZmF1bHRzLmhhc093blByb3BlcnR5KG9wdGlvbikgJiYgIShvcHRpb24gaW4gcGx1Z2luKSkge1xuICAgICAgICBwbHVnaW5bb3B0aW9uXSA9IGRlZmF1bHRzW29wdGlvbl07XG4gICAgICB9XG4gICAgfVxuXG4gICAgcGx1Z2lucy5mb3JFYWNoKGZ1bmN0aW9uIChwKSB7XG4gICAgICBpZiAocC5wbHVnaW5OYW1lID09PSBwbHVnaW4ucGx1Z2luTmFtZSkge1xuICAgICAgICB0aHJvdyBcIlNvcnRhYmxlOiBDYW5ub3QgbW91bnQgcGx1Z2luIFwiLmNvbmNhdChwbHVnaW4ucGx1Z2luTmFtZSwgXCIgbW9yZSB0aGFuIG9uY2VcIik7XG4gICAgICB9XG4gICAgfSk7XG4gICAgcGx1Z2lucy5wdXNoKHBsdWdpbik7XG4gIH0sXG4gIHBsdWdpbkV2ZW50OiBmdW5jdGlvbiBwbHVnaW5FdmVudChldmVudE5hbWUsIHNvcnRhYmxlLCBldnQpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzO1xuXG4gICAgdGhpcy5ldmVudENhbmNlbGVkID0gZmFsc2U7XG5cbiAgICBldnQuY2FuY2VsID0gZnVuY3Rpb24gKCkge1xuICAgICAgX3RoaXMuZXZlbnRDYW5jZWxlZCA9IHRydWU7XG4gICAgfTtcblxuICAgIHZhciBldmVudE5hbWVHbG9iYWwgPSBldmVudE5hbWUgKyAnR2xvYmFsJztcbiAgICBwbHVnaW5zLmZvckVhY2goZnVuY3Rpb24gKHBsdWdpbikge1xuICAgICAgaWYgKCFzb3J0YWJsZVtwbHVnaW4ucGx1Z2luTmFtZV0pIHJldHVybjsgLy8gRmlyZSBnbG9iYWwgZXZlbnRzIGlmIGl0IGV4aXN0cyBpbiB0aGlzIHNvcnRhYmxlXG5cbiAgICAgIGlmIChzb3J0YWJsZVtwbHVnaW4ucGx1Z2luTmFtZV1bZXZlbnROYW1lR2xvYmFsXSkge1xuICAgICAgICBzb3J0YWJsZVtwbHVnaW4ucGx1Z2luTmFtZV1bZXZlbnROYW1lR2xvYmFsXShfb2JqZWN0U3ByZWFkMih7XG4gICAgICAgICAgc29ydGFibGU6IHNvcnRhYmxlXG4gICAgICAgIH0sIGV2dCkpO1xuICAgICAgfSAvLyBPbmx5IGZpcmUgcGx1Z2luIGV2ZW50IGlmIHBsdWdpbiBpcyBlbmFibGVkIGluIHRoaXMgc29ydGFibGUsXG4gICAgICAvLyBhbmQgcGx1Z2luIGhhcyBldmVudCBkZWZpbmVkXG5cblxuICAgICAgaWYgKHNvcnRhYmxlLm9wdGlvbnNbcGx1Z2luLnBsdWdpbk5hbWVdICYmIHNvcnRhYmxlW3BsdWdpbi5wbHVnaW5OYW1lXVtldmVudE5hbWVdKSB7XG4gICAgICAgIHNvcnRhYmxlW3BsdWdpbi5wbHVnaW5OYW1lXVtldmVudE5hbWVdKF9vYmplY3RTcHJlYWQyKHtcbiAgICAgICAgICBzb3J0YWJsZTogc29ydGFibGVcbiAgICAgICAgfSwgZXZ0KSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH0sXG4gIGluaXRpYWxpemVQbHVnaW5zOiBmdW5jdGlvbiBpbml0aWFsaXplUGx1Z2lucyhzb3J0YWJsZSwgZWwsIGRlZmF1bHRzLCBvcHRpb25zKSB7XG4gICAgcGx1Z2lucy5mb3JFYWNoKGZ1bmN0aW9uIChwbHVnaW4pIHtcbiAgICAgIHZhciBwbHVnaW5OYW1lID0gcGx1Z2luLnBsdWdpbk5hbWU7XG4gICAgICBpZiAoIXNvcnRhYmxlLm9wdGlvbnNbcGx1Z2luTmFtZV0gJiYgIXBsdWdpbi5pbml0aWFsaXplQnlEZWZhdWx0KSByZXR1cm47XG4gICAgICB2YXIgaW5pdGlhbGl6ZWQgPSBuZXcgcGx1Z2luKHNvcnRhYmxlLCBlbCwgc29ydGFibGUub3B0aW9ucyk7XG4gICAgICBpbml0aWFsaXplZC5zb3J0YWJsZSA9IHNvcnRhYmxlO1xuICAgICAgaW5pdGlhbGl6ZWQub3B0aW9ucyA9IHNvcnRhYmxlLm9wdGlvbnM7XG4gICAgICBzb3J0YWJsZVtwbHVnaW5OYW1lXSA9IGluaXRpYWxpemVkOyAvLyBBZGQgZGVmYXVsdCBvcHRpb25zIGZyb20gcGx1Z2luXG5cbiAgICAgIF9leHRlbmRzKGRlZmF1bHRzLCBpbml0aWFsaXplZC5kZWZhdWx0cyk7XG4gICAgfSk7XG5cbiAgICBmb3IgKHZhciBvcHRpb24gaW4gc29ydGFibGUub3B0aW9ucykge1xuICAgICAgaWYgKCFzb3J0YWJsZS5vcHRpb25zLmhhc093blByb3BlcnR5KG9wdGlvbikpIGNvbnRpbnVlO1xuICAgICAgdmFyIG1vZGlmaWVkID0gdGhpcy5tb2RpZnlPcHRpb24oc29ydGFibGUsIG9wdGlvbiwgc29ydGFibGUub3B0aW9uc1tvcHRpb25dKTtcblxuICAgICAgaWYgKHR5cGVvZiBtb2RpZmllZCAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgc29ydGFibGUub3B0aW9uc1tvcHRpb25dID0gbW9kaWZpZWQ7XG4gICAgICB9XG4gICAgfVxuICB9LFxuICBnZXRFdmVudFByb3BlcnRpZXM6IGZ1bmN0aW9uIGdldEV2ZW50UHJvcGVydGllcyhuYW1lLCBzb3J0YWJsZSkge1xuICAgIHZhciBldmVudFByb3BlcnRpZXMgPSB7fTtcbiAgICBwbHVnaW5zLmZvckVhY2goZnVuY3Rpb24gKHBsdWdpbikge1xuICAgICAgaWYgKHR5cGVvZiBwbHVnaW4uZXZlbnRQcm9wZXJ0aWVzICE9PSAnZnVuY3Rpb24nKSByZXR1cm47XG5cbiAgICAgIF9leHRlbmRzKGV2ZW50UHJvcGVydGllcywgcGx1Z2luLmV2ZW50UHJvcGVydGllcy5jYWxsKHNvcnRhYmxlW3BsdWdpbi5wbHVnaW5OYW1lXSwgbmFtZSkpO1xuICAgIH0pO1xuICAgIHJldHVybiBldmVudFByb3BlcnRpZXM7XG4gIH0sXG4gIG1vZGlmeU9wdGlvbjogZnVuY3Rpb24gbW9kaWZ5T3B0aW9uKHNvcnRhYmxlLCBuYW1lLCB2YWx1ZSkge1xuICAgIHZhciBtb2RpZmllZFZhbHVlO1xuICAgIHBsdWdpbnMuZm9yRWFjaChmdW5jdGlvbiAocGx1Z2luKSB7XG4gICAgICAvLyBQbHVnaW4gbXVzdCBleGlzdCBvbiB0aGUgU29ydGFibGVcbiAgICAgIGlmICghc29ydGFibGVbcGx1Z2luLnBsdWdpbk5hbWVdKSByZXR1cm47IC8vIElmIHN0YXRpYyBvcHRpb24gbGlzdGVuZXIgZXhpc3RzIGZvciB0aGlzIG9wdGlvbiwgY2FsbCBpbiB0aGUgY29udGV4dCBvZiB0aGUgU29ydGFibGUncyBpbnN0YW5jZSBvZiB0aGlzIHBsdWdpblxuXG4gICAgICBpZiAocGx1Z2luLm9wdGlvbkxpc3RlbmVycyAmJiB0eXBlb2YgcGx1Z2luLm9wdGlvbkxpc3RlbmVyc1tuYW1lXSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICBtb2RpZmllZFZhbHVlID0gcGx1Z2luLm9wdGlvbkxpc3RlbmVyc1tuYW1lXS5jYWxsKHNvcnRhYmxlW3BsdWdpbi5wbHVnaW5OYW1lXSwgdmFsdWUpO1xuICAgICAgfVxuICAgIH0pO1xuICAgIHJldHVybiBtb2RpZmllZFZhbHVlO1xuICB9XG59O1xuXG5mdW5jdGlvbiBkaXNwYXRjaEV2ZW50KF9yZWYpIHtcbiAgdmFyIHNvcnRhYmxlID0gX3JlZi5zb3J0YWJsZSxcbiAgICAgIHJvb3RFbCA9IF9yZWYucm9vdEVsLFxuICAgICAgbmFtZSA9IF9yZWYubmFtZSxcbiAgICAgIHRhcmdldEVsID0gX3JlZi50YXJnZXRFbCxcbiAgICAgIGNsb25lRWwgPSBfcmVmLmNsb25lRWwsXG4gICAgICB0b0VsID0gX3JlZi50b0VsLFxuICAgICAgZnJvbUVsID0gX3JlZi5mcm9tRWwsXG4gICAgICBvbGRJbmRleCA9IF9yZWYub2xkSW5kZXgsXG4gICAgICBuZXdJbmRleCA9IF9yZWYubmV3SW5kZXgsXG4gICAgICBvbGREcmFnZ2FibGVJbmRleCA9IF9yZWYub2xkRHJhZ2dhYmxlSW5kZXgsXG4gICAgICBuZXdEcmFnZ2FibGVJbmRleCA9IF9yZWYubmV3RHJhZ2dhYmxlSW5kZXgsXG4gICAgICBvcmlnaW5hbEV2ZW50ID0gX3JlZi5vcmlnaW5hbEV2ZW50LFxuICAgICAgcHV0U29ydGFibGUgPSBfcmVmLnB1dFNvcnRhYmxlLFxuICAgICAgZXh0cmFFdmVudFByb3BlcnRpZXMgPSBfcmVmLmV4dHJhRXZlbnRQcm9wZXJ0aWVzO1xuICBzb3J0YWJsZSA9IHNvcnRhYmxlIHx8IHJvb3RFbCAmJiByb290RWxbZXhwYW5kb107XG4gIGlmICghc29ydGFibGUpIHJldHVybjtcbiAgdmFyIGV2dCxcbiAgICAgIG9wdGlvbnMgPSBzb3J0YWJsZS5vcHRpb25zLFxuICAgICAgb25OYW1lID0gJ29uJyArIG5hbWUuY2hhckF0KDApLnRvVXBwZXJDYXNlKCkgKyBuYW1lLnN1YnN0cigxKTsgLy8gU3VwcG9ydCBmb3IgbmV3IEN1c3RvbUV2ZW50IGZlYXR1cmVcblxuICBpZiAod2luZG93LkN1c3RvbUV2ZW50ICYmICFJRTExT3JMZXNzICYmICFFZGdlKSB7XG4gICAgZXZ0ID0gbmV3IEN1c3RvbUV2ZW50KG5hbWUsIHtcbiAgICAgIGJ1YmJsZXM6IHRydWUsXG4gICAgICBjYW5jZWxhYmxlOiB0cnVlXG4gICAgfSk7XG4gIH0gZWxzZSB7XG4gICAgZXZ0ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoJ0V2ZW50Jyk7XG4gICAgZXZ0LmluaXRFdmVudChuYW1lLCB0cnVlLCB0cnVlKTtcbiAgfVxuXG4gIGV2dC50byA9IHRvRWwgfHwgcm9vdEVsO1xuICBldnQuZnJvbSA9IGZyb21FbCB8fCByb290RWw7XG4gIGV2dC5pdGVtID0gdGFyZ2V0RWwgfHwgcm9vdEVsO1xuICBldnQuY2xvbmUgPSBjbG9uZUVsO1xuICBldnQub2xkSW5kZXggPSBvbGRJbmRleDtcbiAgZXZ0Lm5ld0luZGV4ID0gbmV3SW5kZXg7XG4gIGV2dC5vbGREcmFnZ2FibGVJbmRleCA9IG9sZERyYWdnYWJsZUluZGV4O1xuICBldnQubmV3RHJhZ2dhYmxlSW5kZXggPSBuZXdEcmFnZ2FibGVJbmRleDtcbiAgZXZ0Lm9yaWdpbmFsRXZlbnQgPSBvcmlnaW5hbEV2ZW50O1xuICBldnQucHVsbE1vZGUgPSBwdXRTb3J0YWJsZSA/IHB1dFNvcnRhYmxlLmxhc3RQdXRNb2RlIDogdW5kZWZpbmVkO1xuXG4gIHZhciBhbGxFdmVudFByb3BlcnRpZXMgPSBfb2JqZWN0U3ByZWFkMihfb2JqZWN0U3ByZWFkMih7fSwgZXh0cmFFdmVudFByb3BlcnRpZXMpLCBQbHVnaW5NYW5hZ2VyLmdldEV2ZW50UHJvcGVydGllcyhuYW1lLCBzb3J0YWJsZSkpO1xuXG4gIGZvciAodmFyIG9wdGlvbiBpbiBhbGxFdmVudFByb3BlcnRpZXMpIHtcbiAgICBldnRbb3B0aW9uXSA9IGFsbEV2ZW50UHJvcGVydGllc1tvcHRpb25dO1xuICB9XG5cbiAgaWYgKHJvb3RFbCkge1xuICAgIHJvb3RFbC5kaXNwYXRjaEV2ZW50KGV2dCk7XG4gIH1cblxuICBpZiAob3B0aW9uc1tvbk5hbWVdKSB7XG4gICAgb3B0aW9uc1tvbk5hbWVdLmNhbGwoc29ydGFibGUsIGV2dCk7XG4gIH1cbn1cblxudmFyIF9leGNsdWRlZCA9IFtcImV2dFwiXTtcblxudmFyIHBsdWdpbkV2ZW50ID0gZnVuY3Rpb24gcGx1Z2luRXZlbnQoZXZlbnROYW1lLCBzb3J0YWJsZSkge1xuICB2YXIgX3JlZiA9IGFyZ3VtZW50cy5sZW5ndGggPiAyICYmIGFyZ3VtZW50c1syXSAhPT0gdW5kZWZpbmVkID8gYXJndW1lbnRzWzJdIDoge30sXG4gICAgICBvcmlnaW5hbEV2ZW50ID0gX3JlZi5ldnQsXG4gICAgICBkYXRhID0gX29iamVjdFdpdGhvdXRQcm9wZXJ0aWVzKF9yZWYsIF9leGNsdWRlZCk7XG5cbiAgUGx1Z2luTWFuYWdlci5wbHVnaW5FdmVudC5iaW5kKFNvcnRhYmxlKShldmVudE5hbWUsIHNvcnRhYmxlLCBfb2JqZWN0U3ByZWFkMih7XG4gICAgZHJhZ0VsOiBkcmFnRWwsXG4gICAgcGFyZW50RWw6IHBhcmVudEVsLFxuICAgIGdob3N0RWw6IGdob3N0RWwsXG4gICAgcm9vdEVsOiByb290RWwsXG4gICAgbmV4dEVsOiBuZXh0RWwsXG4gICAgbGFzdERvd25FbDogbGFzdERvd25FbCxcbiAgICBjbG9uZUVsOiBjbG9uZUVsLFxuICAgIGNsb25lSGlkZGVuOiBjbG9uZUhpZGRlbixcbiAgICBkcmFnU3RhcnRlZDogbW92ZWQsXG4gICAgcHV0U29ydGFibGU6IHB1dFNvcnRhYmxlLFxuICAgIGFjdGl2ZVNvcnRhYmxlOiBTb3J0YWJsZS5hY3RpdmUsXG4gICAgb3JpZ2luYWxFdmVudDogb3JpZ2luYWxFdmVudCxcbiAgICBvbGRJbmRleDogb2xkSW5kZXgsXG4gICAgb2xkRHJhZ2dhYmxlSW5kZXg6IG9sZERyYWdnYWJsZUluZGV4LFxuICAgIG5ld0luZGV4OiBuZXdJbmRleCxcbiAgICBuZXdEcmFnZ2FibGVJbmRleDogbmV3RHJhZ2dhYmxlSW5kZXgsXG4gICAgaGlkZUdob3N0Rm9yVGFyZ2V0OiBfaGlkZUdob3N0Rm9yVGFyZ2V0LFxuICAgIHVuaGlkZUdob3N0Rm9yVGFyZ2V0OiBfdW5oaWRlR2hvc3RGb3JUYXJnZXQsXG4gICAgY2xvbmVOb3dIaWRkZW46IGZ1bmN0aW9uIGNsb25lTm93SGlkZGVuKCkge1xuICAgICAgY2xvbmVIaWRkZW4gPSB0cnVlO1xuICAgIH0sXG4gICAgY2xvbmVOb3dTaG93bjogZnVuY3Rpb24gY2xvbmVOb3dTaG93bigpIHtcbiAgICAgIGNsb25lSGlkZGVuID0gZmFsc2U7XG4gICAgfSxcbiAgICBkaXNwYXRjaFNvcnRhYmxlRXZlbnQ6IGZ1bmN0aW9uIGRpc3BhdGNoU29ydGFibGVFdmVudChuYW1lKSB7XG4gICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgIHNvcnRhYmxlOiBzb3J0YWJsZSxcbiAgICAgICAgbmFtZTogbmFtZSxcbiAgICAgICAgb3JpZ2luYWxFdmVudDogb3JpZ2luYWxFdmVudFxuICAgICAgfSk7XG4gICAgfVxuICB9LCBkYXRhKSk7XG59O1xuXG5mdW5jdGlvbiBfZGlzcGF0Y2hFdmVudChpbmZvKSB7XG4gIGRpc3BhdGNoRXZlbnQoX29iamVjdFNwcmVhZDIoe1xuICAgIHB1dFNvcnRhYmxlOiBwdXRTb3J0YWJsZSxcbiAgICBjbG9uZUVsOiBjbG9uZUVsLFxuICAgIHRhcmdldEVsOiBkcmFnRWwsXG4gICAgcm9vdEVsOiByb290RWwsXG4gICAgb2xkSW5kZXg6IG9sZEluZGV4LFxuICAgIG9sZERyYWdnYWJsZUluZGV4OiBvbGREcmFnZ2FibGVJbmRleCxcbiAgICBuZXdJbmRleDogbmV3SW5kZXgsXG4gICAgbmV3RHJhZ2dhYmxlSW5kZXg6IG5ld0RyYWdnYWJsZUluZGV4XG4gIH0sIGluZm8pKTtcbn1cblxudmFyIGRyYWdFbCxcbiAgICBwYXJlbnRFbCxcbiAgICBnaG9zdEVsLFxuICAgIHJvb3RFbCxcbiAgICBuZXh0RWwsXG4gICAgbGFzdERvd25FbCxcbiAgICBjbG9uZUVsLFxuICAgIGNsb25lSGlkZGVuLFxuICAgIG9sZEluZGV4LFxuICAgIG5ld0luZGV4LFxuICAgIG9sZERyYWdnYWJsZUluZGV4LFxuICAgIG5ld0RyYWdnYWJsZUluZGV4LFxuICAgIGFjdGl2ZUdyb3VwLFxuICAgIHB1dFNvcnRhYmxlLFxuICAgIGF3YWl0aW5nRHJhZ1N0YXJ0ZWQgPSBmYWxzZSxcbiAgICBpZ25vcmVOZXh0Q2xpY2sgPSBmYWxzZSxcbiAgICBzb3J0YWJsZXMgPSBbXSxcbiAgICB0YXBFdnQsXG4gICAgdG91Y2hFdnQsXG4gICAgbGFzdER4LFxuICAgIGxhc3REeSxcbiAgICB0YXBEaXN0YW5jZUxlZnQsXG4gICAgdGFwRGlzdGFuY2VUb3AsXG4gICAgbW92ZWQsXG4gICAgbGFzdFRhcmdldCxcbiAgICBsYXN0RGlyZWN0aW9uLFxuICAgIHBhc3RGaXJzdEludmVydFRocmVzaCA9IGZhbHNlLFxuICAgIGlzQ2lyY3Vtc3RhbnRpYWxJbnZlcnQgPSBmYWxzZSxcbiAgICB0YXJnZXRNb3ZlRGlzdGFuY2UsXG4gICAgLy8gRm9yIHBvc2l0aW9uaW5nIGdob3N0IGFic29sdXRlbHlcbmdob3N0UmVsYXRpdmVQYXJlbnQsXG4gICAgZ2hvc3RSZWxhdGl2ZVBhcmVudEluaXRpYWxTY3JvbGwgPSBbXSxcbiAgICAvLyAobGVmdCwgdG9wKVxuX3NpbGVudCA9IGZhbHNlLFxuICAgIHNhdmVkSW5wdXRDaGVja2VkID0gW107XG4vKiogQGNvbnN0ICovXG5cbnZhciBkb2N1bWVudEV4aXN0cyA9IHR5cGVvZiBkb2N1bWVudCAhPT0gJ3VuZGVmaW5lZCcsXG4gICAgUG9zaXRpb25HaG9zdEFic29sdXRlbHkgPSBJT1MsXG4gICAgQ1NTRmxvYXRQcm9wZXJ0eSA9IEVkZ2UgfHwgSUUxMU9yTGVzcyA/ICdjc3NGbG9hdCcgOiAnZmxvYXQnLFxuICAgIC8vIFRoaXMgd2lsbCBub3QgcGFzcyBmb3IgSUU5LCBiZWNhdXNlIElFOSBEbkQgb25seSB3b3JrcyBvbiBhbmNob3JzXG5zdXBwb3J0RHJhZ2dhYmxlID0gZG9jdW1lbnRFeGlzdHMgJiYgIUNocm9tZUZvckFuZHJvaWQgJiYgIUlPUyAmJiAnZHJhZ2dhYmxlJyBpbiBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKSxcbiAgICBzdXBwb3J0Q3NzUG9pbnRlckV2ZW50cyA9IGZ1bmN0aW9uICgpIHtcbiAgaWYgKCFkb2N1bWVudEV4aXN0cykgcmV0dXJuOyAvLyBmYWxzZSB3aGVuIDw9IElFMTFcblxuICBpZiAoSUUxMU9yTGVzcykge1xuICAgIHJldHVybiBmYWxzZTtcbiAgfVxuXG4gIHZhciBlbCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3gnKTtcbiAgZWwuc3R5bGUuY3NzVGV4dCA9ICdwb2ludGVyLWV2ZW50czphdXRvJztcbiAgcmV0dXJuIGVsLnN0eWxlLnBvaW50ZXJFdmVudHMgPT09ICdhdXRvJztcbn0oKSxcbiAgICBfZGV0ZWN0RGlyZWN0aW9uID0gZnVuY3Rpb24gX2RldGVjdERpcmVjdGlvbihlbCwgb3B0aW9ucykge1xuICB2YXIgZWxDU1MgPSBjc3MoZWwpLFxuICAgICAgZWxXaWR0aCA9IHBhcnNlSW50KGVsQ1NTLndpZHRoKSAtIHBhcnNlSW50KGVsQ1NTLnBhZGRpbmdMZWZ0KSAtIHBhcnNlSW50KGVsQ1NTLnBhZGRpbmdSaWdodCkgLSBwYXJzZUludChlbENTUy5ib3JkZXJMZWZ0V2lkdGgpIC0gcGFyc2VJbnQoZWxDU1MuYm9yZGVyUmlnaHRXaWR0aCksXG4gICAgICBjaGlsZDEgPSBnZXRDaGlsZChlbCwgMCwgb3B0aW9ucyksXG4gICAgICBjaGlsZDIgPSBnZXRDaGlsZChlbCwgMSwgb3B0aW9ucyksXG4gICAgICBmaXJzdENoaWxkQ1NTID0gY2hpbGQxICYmIGNzcyhjaGlsZDEpLFxuICAgICAgc2Vjb25kQ2hpbGRDU1MgPSBjaGlsZDIgJiYgY3NzKGNoaWxkMiksXG4gICAgICBmaXJzdENoaWxkV2lkdGggPSBmaXJzdENoaWxkQ1NTICYmIHBhcnNlSW50KGZpcnN0Q2hpbGRDU1MubWFyZ2luTGVmdCkgKyBwYXJzZUludChmaXJzdENoaWxkQ1NTLm1hcmdpblJpZ2h0KSArIGdldFJlY3QoY2hpbGQxKS53aWR0aCxcbiAgICAgIHNlY29uZENoaWxkV2lkdGggPSBzZWNvbmRDaGlsZENTUyAmJiBwYXJzZUludChzZWNvbmRDaGlsZENTUy5tYXJnaW5MZWZ0KSArIHBhcnNlSW50KHNlY29uZENoaWxkQ1NTLm1hcmdpblJpZ2h0KSArIGdldFJlY3QoY2hpbGQyKS53aWR0aDtcblxuICBpZiAoZWxDU1MuZGlzcGxheSA9PT0gJ2ZsZXgnKSB7XG4gICAgcmV0dXJuIGVsQ1NTLmZsZXhEaXJlY3Rpb24gPT09ICdjb2x1bW4nIHx8IGVsQ1NTLmZsZXhEaXJlY3Rpb24gPT09ICdjb2x1bW4tcmV2ZXJzZScgPyAndmVydGljYWwnIDogJ2hvcml6b250YWwnO1xuICB9XG5cbiAgaWYgKGVsQ1NTLmRpc3BsYXkgPT09ICdncmlkJykge1xuICAgIHJldHVybiBlbENTUy5ncmlkVGVtcGxhdGVDb2x1bW5zLnNwbGl0KCcgJykubGVuZ3RoIDw9IDEgPyAndmVydGljYWwnIDogJ2hvcml6b250YWwnO1xuICB9XG5cbiAgaWYgKGNoaWxkMSAmJiBmaXJzdENoaWxkQ1NTW1wiZmxvYXRcIl0gJiYgZmlyc3RDaGlsZENTU1tcImZsb2F0XCJdICE9PSAnbm9uZScpIHtcbiAgICB2YXIgdG91Y2hpbmdTaWRlQ2hpbGQyID0gZmlyc3RDaGlsZENTU1tcImZsb2F0XCJdID09PSAnbGVmdCcgPyAnbGVmdCcgOiAncmlnaHQnO1xuICAgIHJldHVybiBjaGlsZDIgJiYgKHNlY29uZENoaWxkQ1NTLmNsZWFyID09PSAnYm90aCcgfHwgc2Vjb25kQ2hpbGRDU1MuY2xlYXIgPT09IHRvdWNoaW5nU2lkZUNoaWxkMikgPyAndmVydGljYWwnIDogJ2hvcml6b250YWwnO1xuICB9XG5cbiAgcmV0dXJuIGNoaWxkMSAmJiAoZmlyc3RDaGlsZENTUy5kaXNwbGF5ID09PSAnYmxvY2snIHx8IGZpcnN0Q2hpbGRDU1MuZGlzcGxheSA9PT0gJ2ZsZXgnIHx8IGZpcnN0Q2hpbGRDU1MuZGlzcGxheSA9PT0gJ3RhYmxlJyB8fCBmaXJzdENoaWxkQ1NTLmRpc3BsYXkgPT09ICdncmlkJyB8fCBmaXJzdENoaWxkV2lkdGggPj0gZWxXaWR0aCAmJiBlbENTU1tDU1NGbG9hdFByb3BlcnR5XSA9PT0gJ25vbmUnIHx8IGNoaWxkMiAmJiBlbENTU1tDU1NGbG9hdFByb3BlcnR5XSA9PT0gJ25vbmUnICYmIGZpcnN0Q2hpbGRXaWR0aCArIHNlY29uZENoaWxkV2lkdGggPiBlbFdpZHRoKSA/ICd2ZXJ0aWNhbCcgOiAnaG9yaXpvbnRhbCc7XG59LFxuICAgIF9kcmFnRWxJblJvd0NvbHVtbiA9IGZ1bmN0aW9uIF9kcmFnRWxJblJvd0NvbHVtbihkcmFnUmVjdCwgdGFyZ2V0UmVjdCwgdmVydGljYWwpIHtcbiAgdmFyIGRyYWdFbFMxT3BwID0gdmVydGljYWwgPyBkcmFnUmVjdC5sZWZ0IDogZHJhZ1JlY3QudG9wLFxuICAgICAgZHJhZ0VsUzJPcHAgPSB2ZXJ0aWNhbCA/IGRyYWdSZWN0LnJpZ2h0IDogZHJhZ1JlY3QuYm90dG9tLFxuICAgICAgZHJhZ0VsT3BwTGVuZ3RoID0gdmVydGljYWwgPyBkcmFnUmVjdC53aWR0aCA6IGRyYWdSZWN0LmhlaWdodCxcbiAgICAgIHRhcmdldFMxT3BwID0gdmVydGljYWwgPyB0YXJnZXRSZWN0LmxlZnQgOiB0YXJnZXRSZWN0LnRvcCxcbiAgICAgIHRhcmdldFMyT3BwID0gdmVydGljYWwgPyB0YXJnZXRSZWN0LnJpZ2h0IDogdGFyZ2V0UmVjdC5ib3R0b20sXG4gICAgICB0YXJnZXRPcHBMZW5ndGggPSB2ZXJ0aWNhbCA/IHRhcmdldFJlY3Qud2lkdGggOiB0YXJnZXRSZWN0LmhlaWdodDtcbiAgcmV0dXJuIGRyYWdFbFMxT3BwID09PSB0YXJnZXRTMU9wcCB8fCBkcmFnRWxTMk9wcCA9PT0gdGFyZ2V0UzJPcHAgfHwgZHJhZ0VsUzFPcHAgKyBkcmFnRWxPcHBMZW5ndGggLyAyID09PSB0YXJnZXRTMU9wcCArIHRhcmdldE9wcExlbmd0aCAvIDI7XG59LFxuXG4vKipcclxuICogRGV0ZWN0cyBmaXJzdCBuZWFyZXN0IGVtcHR5IHNvcnRhYmxlIHRvIFggYW5kIFkgcG9zaXRpb24gdXNpbmcgZW1wdHlJbnNlcnRUaHJlc2hvbGQuXHJcbiAqIEBwYXJhbSAge051bWJlcn0geCAgICAgIFggcG9zaXRpb25cclxuICogQHBhcmFtICB7TnVtYmVyfSB5ICAgICAgWSBwb3NpdGlvblxyXG4gKiBAcmV0dXJuIHtIVE1MRWxlbWVudH0gICBFbGVtZW50IG9mIHRoZSBmaXJzdCBmb3VuZCBuZWFyZXN0IFNvcnRhYmxlXHJcbiAqL1xuX2RldGVjdE5lYXJlc3RFbXB0eVNvcnRhYmxlID0gZnVuY3Rpb24gX2RldGVjdE5lYXJlc3RFbXB0eVNvcnRhYmxlKHgsIHkpIHtcbiAgdmFyIHJldDtcbiAgc29ydGFibGVzLnNvbWUoZnVuY3Rpb24gKHNvcnRhYmxlKSB7XG4gICAgdmFyIHRocmVzaG9sZCA9IHNvcnRhYmxlW2V4cGFuZG9dLm9wdGlvbnMuZW1wdHlJbnNlcnRUaHJlc2hvbGQ7XG4gICAgaWYgKCF0aHJlc2hvbGQgfHwgbGFzdENoaWxkKHNvcnRhYmxlKSkgcmV0dXJuO1xuICAgIHZhciByZWN0ID0gZ2V0UmVjdChzb3J0YWJsZSksXG4gICAgICAgIGluc2lkZUhvcml6b250YWxseSA9IHggPj0gcmVjdC5sZWZ0IC0gdGhyZXNob2xkICYmIHggPD0gcmVjdC5yaWdodCArIHRocmVzaG9sZCxcbiAgICAgICAgaW5zaWRlVmVydGljYWxseSA9IHkgPj0gcmVjdC50b3AgLSB0aHJlc2hvbGQgJiYgeSA8PSByZWN0LmJvdHRvbSArIHRocmVzaG9sZDtcblxuICAgIGlmIChpbnNpZGVIb3Jpem9udGFsbHkgJiYgaW5zaWRlVmVydGljYWxseSkge1xuICAgICAgcmV0dXJuIHJldCA9IHNvcnRhYmxlO1xuICAgIH1cbiAgfSk7XG4gIHJldHVybiByZXQ7XG59LFxuICAgIF9wcmVwYXJlR3JvdXAgPSBmdW5jdGlvbiBfcHJlcGFyZUdyb3VwKG9wdGlvbnMpIHtcbiAgZnVuY3Rpb24gdG9Gbih2YWx1ZSwgcHVsbCkge1xuICAgIHJldHVybiBmdW5jdGlvbiAodG8sIGZyb20sIGRyYWdFbCwgZXZ0KSB7XG4gICAgICB2YXIgc2FtZUdyb3VwID0gdG8ub3B0aW9ucy5ncm91cC5uYW1lICYmIGZyb20ub3B0aW9ucy5ncm91cC5uYW1lICYmIHRvLm9wdGlvbnMuZ3JvdXAubmFtZSA9PT0gZnJvbS5vcHRpb25zLmdyb3VwLm5hbWU7XG5cbiAgICAgIGlmICh2YWx1ZSA9PSBudWxsICYmIChwdWxsIHx8IHNhbWVHcm91cCkpIHtcbiAgICAgICAgLy8gRGVmYXVsdCBwdWxsIHZhbHVlXG4gICAgICAgIC8vIERlZmF1bHQgcHVsbCBhbmQgcHV0IHZhbHVlIGlmIHNhbWUgZ3JvdXBcbiAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICB9IGVsc2UgaWYgKHZhbHVlID09IG51bGwgfHwgdmFsdWUgPT09IGZhbHNlKSB7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgIH0gZWxzZSBpZiAocHVsbCAmJiB2YWx1ZSA9PT0gJ2Nsb25lJykge1xuICAgICAgICByZXR1cm4gdmFsdWU7XG4gICAgICB9IGVsc2UgaWYgKHR5cGVvZiB2YWx1ZSA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICByZXR1cm4gdG9Gbih2YWx1ZSh0bywgZnJvbSwgZHJhZ0VsLCBldnQpLCBwdWxsKSh0bywgZnJvbSwgZHJhZ0VsLCBldnQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdmFyIG90aGVyR3JvdXAgPSAocHVsbCA/IHRvIDogZnJvbSkub3B0aW9ucy5ncm91cC5uYW1lO1xuICAgICAgICByZXR1cm4gdmFsdWUgPT09IHRydWUgfHwgdHlwZW9mIHZhbHVlID09PSAnc3RyaW5nJyAmJiB2YWx1ZSA9PT0gb3RoZXJHcm91cCB8fCB2YWx1ZS5qb2luICYmIHZhbHVlLmluZGV4T2Yob3RoZXJHcm91cCkgPiAtMTtcbiAgICAgIH1cbiAgICB9O1xuICB9XG5cbiAgdmFyIGdyb3VwID0ge307XG4gIHZhciBvcmlnaW5hbEdyb3VwID0gb3B0aW9ucy5ncm91cDtcblxuICBpZiAoIW9yaWdpbmFsR3JvdXAgfHwgX3R5cGVvZihvcmlnaW5hbEdyb3VwKSAhPSAnb2JqZWN0Jykge1xuICAgIG9yaWdpbmFsR3JvdXAgPSB7XG4gICAgICBuYW1lOiBvcmlnaW5hbEdyb3VwXG4gICAgfTtcbiAgfVxuXG4gIGdyb3VwLm5hbWUgPSBvcmlnaW5hbEdyb3VwLm5hbWU7XG4gIGdyb3VwLmNoZWNrUHVsbCA9IHRvRm4ob3JpZ2luYWxHcm91cC5wdWxsLCB0cnVlKTtcbiAgZ3JvdXAuY2hlY2tQdXQgPSB0b0ZuKG9yaWdpbmFsR3JvdXAucHV0KTtcbiAgZ3JvdXAucmV2ZXJ0Q2xvbmUgPSBvcmlnaW5hbEdyb3VwLnJldmVydENsb25lO1xuICBvcHRpb25zLmdyb3VwID0gZ3JvdXA7XG59LFxuICAgIF9oaWRlR2hvc3RGb3JUYXJnZXQgPSBmdW5jdGlvbiBfaGlkZUdob3N0Rm9yVGFyZ2V0KCkge1xuICBpZiAoIXN1cHBvcnRDc3NQb2ludGVyRXZlbnRzICYmIGdob3N0RWwpIHtcbiAgICBjc3MoZ2hvc3RFbCwgJ2Rpc3BsYXknLCAnbm9uZScpO1xuICB9XG59LFxuICAgIF91bmhpZGVHaG9zdEZvclRhcmdldCA9IGZ1bmN0aW9uIF91bmhpZGVHaG9zdEZvclRhcmdldCgpIHtcbiAgaWYgKCFzdXBwb3J0Q3NzUG9pbnRlckV2ZW50cyAmJiBnaG9zdEVsKSB7XG4gICAgY3NzKGdob3N0RWwsICdkaXNwbGF5JywgJycpO1xuICB9XG59OyAvLyAjMTE4NCBmaXggLSBQcmV2ZW50IGNsaWNrIGV2ZW50IG9uIGZhbGxiYWNrIGlmIGRyYWdnZWQgYnV0IGl0ZW0gbm90IGNoYW5nZWQgcG9zaXRpb25cblxuXG5pZiAoZG9jdW1lbnRFeGlzdHMgJiYgIUNocm9tZUZvckFuZHJvaWQpIHtcbiAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbiAoZXZ0KSB7XG4gICAgaWYgKGlnbm9yZU5leHRDbGljaykge1xuICAgICAgZXZ0LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBldnQuc3RvcFByb3BhZ2F0aW9uICYmIGV2dC5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIGV2dC5zdG9wSW1tZWRpYXRlUHJvcGFnYXRpb24gJiYgZXZ0LnN0b3BJbW1lZGlhdGVQcm9wYWdhdGlvbigpO1xuICAgICAgaWdub3JlTmV4dENsaWNrID0gZmFsc2U7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9LCB0cnVlKTtcbn1cblxudmFyIG5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50ID0gZnVuY3Rpb24gbmVhcmVzdEVtcHR5SW5zZXJ0RGV0ZWN0RXZlbnQoZXZ0KSB7XG4gIGlmIChkcmFnRWwpIHtcbiAgICBldnQgPSBldnQudG91Y2hlcyA/IGV2dC50b3VjaGVzWzBdIDogZXZ0O1xuXG4gICAgdmFyIG5lYXJlc3QgPSBfZGV0ZWN0TmVhcmVzdEVtcHR5U29ydGFibGUoZXZ0LmNsaWVudFgsIGV2dC5jbGllbnRZKTtcblxuICAgIGlmIChuZWFyZXN0KSB7XG4gICAgICAvLyBDcmVhdGUgaW1pdGF0aW9uIGV2ZW50XG4gICAgICB2YXIgZXZlbnQgPSB7fTtcblxuICAgICAgZm9yICh2YXIgaSBpbiBldnQpIHtcbiAgICAgICAgaWYgKGV2dC5oYXNPd25Qcm9wZXJ0eShpKSkge1xuICAgICAgICAgIGV2ZW50W2ldID0gZXZ0W2ldO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIGV2ZW50LnRhcmdldCA9IGV2ZW50LnJvb3RFbCA9IG5lYXJlc3Q7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCA9IHZvaWQgMDtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbiA9IHZvaWQgMDtcblxuICAgICAgbmVhcmVzdFtleHBhbmRvXS5fb25EcmFnT3ZlcihldmVudCk7XG4gICAgfVxuICB9XG59O1xuXG52YXIgX2NoZWNrT3V0c2lkZVRhcmdldEVsID0gZnVuY3Rpb24gX2NoZWNrT3V0c2lkZVRhcmdldEVsKGV2dCkge1xuICBpZiAoZHJhZ0VsKSB7XG4gICAgZHJhZ0VsLnBhcmVudE5vZGVbZXhwYW5kb10uX2lzT3V0c2lkZVRoaXNFbChldnQudGFyZ2V0KTtcbiAgfVxufTtcbi8qKlxyXG4gKiBAY2xhc3MgIFNvcnRhYmxlXHJcbiAqIEBwYXJhbSAge0hUTUxFbGVtZW50fSAgZWxcclxuICogQHBhcmFtICB7T2JqZWN0fSAgICAgICBbb3B0aW9uc11cclxuICovXG5cblxuZnVuY3Rpb24gU29ydGFibGUoZWwsIG9wdGlvbnMpIHtcbiAgaWYgKCEoZWwgJiYgZWwubm9kZVR5cGUgJiYgZWwubm9kZVR5cGUgPT09IDEpKSB7XG4gICAgdGhyb3cgXCJTb3J0YWJsZTogYGVsYCBtdXN0IGJlIGFuIEhUTUxFbGVtZW50LCBub3QgXCIuY29uY2F0KHt9LnRvU3RyaW5nLmNhbGwoZWwpKTtcbiAgfVxuXG4gIHRoaXMuZWwgPSBlbDsgLy8gcm9vdCBlbGVtZW50XG5cbiAgdGhpcy5vcHRpb25zID0gb3B0aW9ucyA9IF9leHRlbmRzKHt9LCBvcHRpb25zKTsgLy8gRXhwb3J0IGluc3RhbmNlXG5cbiAgZWxbZXhwYW5kb10gPSB0aGlzO1xuICB2YXIgZGVmYXVsdHMgPSB7XG4gICAgZ3JvdXA6IG51bGwsXG4gICAgc29ydDogdHJ1ZSxcbiAgICBkaXNhYmxlZDogZmFsc2UsXG4gICAgc3RvcmU6IG51bGwsXG4gICAgaGFuZGxlOiBudWxsLFxuICAgIGRyYWdnYWJsZTogL15bdW9dbCQvaS50ZXN0KGVsLm5vZGVOYW1lKSA/ICc+bGknIDogJz4qJyxcbiAgICBzd2FwVGhyZXNob2xkOiAxLFxuICAgIC8vIHBlcmNlbnRhZ2U7IDAgPD0geCA8PSAxXG4gICAgaW52ZXJ0U3dhcDogZmFsc2UsXG4gICAgLy8gaW52ZXJ0IGFsd2F5c1xuICAgIGludmVydGVkU3dhcFRocmVzaG9sZDogbnVsbCxcbiAgICAvLyB3aWxsIGJlIHNldCB0byBzYW1lIGFzIHN3YXBUaHJlc2hvbGQgaWYgZGVmYXVsdFxuICAgIHJlbW92ZUNsb25lT25IaWRlOiB0cnVlLFxuICAgIGRpcmVjdGlvbjogZnVuY3Rpb24gZGlyZWN0aW9uKCkge1xuICAgICAgcmV0dXJuIF9kZXRlY3REaXJlY3Rpb24oZWwsIHRoaXMub3B0aW9ucyk7XG4gICAgfSxcbiAgICBnaG9zdENsYXNzOiAnc29ydGFibGUtZ2hvc3QnLFxuICAgIGNob3NlbkNsYXNzOiAnc29ydGFibGUtY2hvc2VuJyxcbiAgICBkcmFnQ2xhc3M6ICdzb3J0YWJsZS1kcmFnJyxcbiAgICBpZ25vcmU6ICdhLCBpbWcnLFxuICAgIGZpbHRlcjogbnVsbCxcbiAgICBwcmV2ZW50T25GaWx0ZXI6IHRydWUsXG4gICAgYW5pbWF0aW9uOiAwLFxuICAgIGVhc2luZzogbnVsbCxcbiAgICBzZXREYXRhOiBmdW5jdGlvbiBzZXREYXRhKGRhdGFUcmFuc2ZlciwgZHJhZ0VsKSB7XG4gICAgICBkYXRhVHJhbnNmZXIuc2V0RGF0YSgnVGV4dCcsIGRyYWdFbC50ZXh0Q29udGVudCk7XG4gICAgfSxcbiAgICBkcm9wQnViYmxlOiBmYWxzZSxcbiAgICBkcmFnb3ZlckJ1YmJsZTogZmFsc2UsXG4gICAgZGF0YUlkQXR0cjogJ2RhdGEtaWQnLFxuICAgIGRlbGF5OiAwLFxuICAgIGRlbGF5T25Ub3VjaE9ubHk6IGZhbHNlLFxuICAgIHRvdWNoU3RhcnRUaHJlc2hvbGQ6IChOdW1iZXIucGFyc2VJbnQgPyBOdW1iZXIgOiB3aW5kb3cpLnBhcnNlSW50KHdpbmRvdy5kZXZpY2VQaXhlbFJhdGlvLCAxMCkgfHwgMSxcbiAgICBmb3JjZUZhbGxiYWNrOiBmYWxzZSxcbiAgICBmYWxsYmFja0NsYXNzOiAnc29ydGFibGUtZmFsbGJhY2snLFxuICAgIGZhbGxiYWNrT25Cb2R5OiBmYWxzZSxcbiAgICBmYWxsYmFja1RvbGVyYW5jZTogMCxcbiAgICBmYWxsYmFja09mZnNldDoge1xuICAgICAgeDogMCxcbiAgICAgIHk6IDBcbiAgICB9LFxuICAgIHN1cHBvcnRQb2ludGVyOiBTb3J0YWJsZS5zdXBwb3J0UG9pbnRlciAhPT0gZmFsc2UgJiYgJ1BvaW50ZXJFdmVudCcgaW4gd2luZG93ICYmICFTYWZhcmksXG4gICAgZW1wdHlJbnNlcnRUaHJlc2hvbGQ6IDVcbiAgfTtcbiAgUGx1Z2luTWFuYWdlci5pbml0aWFsaXplUGx1Z2lucyh0aGlzLCBlbCwgZGVmYXVsdHMpOyAvLyBTZXQgZGVmYXVsdCBvcHRpb25zXG5cbiAgZm9yICh2YXIgbmFtZSBpbiBkZWZhdWx0cykge1xuICAgICEobmFtZSBpbiBvcHRpb25zKSAmJiAob3B0aW9uc1tuYW1lXSA9IGRlZmF1bHRzW25hbWVdKTtcbiAgfVxuXG4gIF9wcmVwYXJlR3JvdXAob3B0aW9ucyk7IC8vIEJpbmQgYWxsIHByaXZhdGUgbWV0aG9kc1xuXG5cbiAgZm9yICh2YXIgZm4gaW4gdGhpcykge1xuICAgIGlmIChmbi5jaGFyQXQoMCkgPT09ICdfJyAmJiB0eXBlb2YgdGhpc1tmbl0gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIHRoaXNbZm5dID0gdGhpc1tmbl0uYmluZCh0aGlzKTtcbiAgICB9XG4gIH0gLy8gU2V0dXAgZHJhZyBtb2RlXG5cblxuICB0aGlzLm5hdGl2ZURyYWdnYWJsZSA9IG9wdGlvbnMuZm9yY2VGYWxsYmFjayA/IGZhbHNlIDogc3VwcG9ydERyYWdnYWJsZTtcblxuICBpZiAodGhpcy5uYXRpdmVEcmFnZ2FibGUpIHtcbiAgICAvLyBUb3VjaCBzdGFydCB0aHJlc2hvbGQgY2Fubm90IGJlIGdyZWF0ZXIgdGhhbiB0aGUgbmF0aXZlIGRyYWdzdGFydCB0aHJlc2hvbGRcbiAgICB0aGlzLm9wdGlvbnMudG91Y2hTdGFydFRocmVzaG9sZCA9IDE7XG4gIH0gLy8gQmluZCBldmVudHNcblxuXG4gIGlmIChvcHRpb25zLnN1cHBvcnRQb2ludGVyKSB7XG4gICAgb24oZWwsICdwb2ludGVyZG93bicsIHRoaXMuX29uVGFwU3RhcnQpO1xuICB9IGVsc2Uge1xuICAgIG9uKGVsLCAnbW91c2Vkb3duJywgdGhpcy5fb25UYXBTdGFydCk7XG4gICAgb24oZWwsICd0b3VjaHN0YXJ0JywgdGhpcy5fb25UYXBTdGFydCk7XG4gIH1cblxuICBpZiAodGhpcy5uYXRpdmVEcmFnZ2FibGUpIHtcbiAgICBvbihlbCwgJ2RyYWdvdmVyJywgdGhpcyk7XG4gICAgb24oZWwsICdkcmFnZW50ZXInLCB0aGlzKTtcbiAgfVxuXG4gIHNvcnRhYmxlcy5wdXNoKHRoaXMuZWwpOyAvLyBSZXN0b3JlIHNvcnRpbmdcblxuICBvcHRpb25zLnN0b3JlICYmIG9wdGlvbnMuc3RvcmUuZ2V0ICYmIHRoaXMuc29ydChvcHRpb25zLnN0b3JlLmdldCh0aGlzKSB8fCBbXSk7IC8vIEFkZCBhbmltYXRpb24gc3RhdGUgbWFuYWdlclxuXG4gIF9leHRlbmRzKHRoaXMsIEFuaW1hdGlvblN0YXRlTWFuYWdlcigpKTtcbn1cblxuU29ydGFibGUucHJvdG90eXBlID1cbi8qKiBAbGVuZHMgU29ydGFibGUucHJvdG90eXBlICovXG57XG4gIGNvbnN0cnVjdG9yOiBTb3J0YWJsZSxcbiAgX2lzT3V0c2lkZVRoaXNFbDogZnVuY3Rpb24gX2lzT3V0c2lkZVRoaXNFbCh0YXJnZXQpIHtcbiAgICBpZiAoIXRoaXMuZWwuY29udGFpbnModGFyZ2V0KSAmJiB0YXJnZXQgIT09IHRoaXMuZWwpIHtcbiAgICAgIGxhc3RUYXJnZXQgPSBudWxsO1xuICAgIH1cbiAgfSxcbiAgX2dldERpcmVjdGlvbjogZnVuY3Rpb24gX2dldERpcmVjdGlvbihldnQsIHRhcmdldCkge1xuICAgIHJldHVybiB0eXBlb2YgdGhpcy5vcHRpb25zLmRpcmVjdGlvbiA9PT0gJ2Z1bmN0aW9uJyA/IHRoaXMub3B0aW9ucy5kaXJlY3Rpb24uY2FsbCh0aGlzLCBldnQsIHRhcmdldCwgZHJhZ0VsKSA6IHRoaXMub3B0aW9ucy5kaXJlY3Rpb247XG4gIH0sXG4gIF9vblRhcFN0YXJ0OiBmdW5jdGlvbiBfb25UYXBTdGFydChcbiAgLyoqIEV2ZW50fFRvdWNoRXZlbnQgKi9cbiAgZXZ0KSB7XG4gICAgaWYgKCFldnQuY2FuY2VsYWJsZSkgcmV0dXJuO1xuXG4gICAgdmFyIF90aGlzID0gdGhpcyxcbiAgICAgICAgZWwgPSB0aGlzLmVsLFxuICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zLFxuICAgICAgICBwcmV2ZW50T25GaWx0ZXIgPSBvcHRpb25zLnByZXZlbnRPbkZpbHRlcixcbiAgICAgICAgdHlwZSA9IGV2dC50eXBlLFxuICAgICAgICB0b3VjaCA9IGV2dC50b3VjaGVzICYmIGV2dC50b3VjaGVzWzBdIHx8IGV2dC5wb2ludGVyVHlwZSAmJiBldnQucG9pbnRlclR5cGUgPT09ICd0b3VjaCcgJiYgZXZ0LFxuICAgICAgICB0YXJnZXQgPSAodG91Y2ggfHwgZXZ0KS50YXJnZXQsXG4gICAgICAgIG9yaWdpbmFsVGFyZ2V0ID0gZXZ0LnRhcmdldC5zaGFkb3dSb290ICYmIChldnQucGF0aCAmJiBldnQucGF0aFswXSB8fCBldnQuY29tcG9zZWRQYXRoICYmIGV2dC5jb21wb3NlZFBhdGgoKVswXSkgfHwgdGFyZ2V0LFxuICAgICAgICBmaWx0ZXIgPSBvcHRpb25zLmZpbHRlcjtcblxuICAgIF9zYXZlSW5wdXRDaGVja2VkU3RhdGUoZWwpOyAvLyBEb24ndCB0cmlnZ2VyIHN0YXJ0IGV2ZW50IHdoZW4gYW4gZWxlbWVudCBpcyBiZWVuIGRyYWdnZWQsIG90aGVyd2lzZSB0aGUgZXZ0Lm9sZGluZGV4IGFsd2F5cyB3cm9uZyB3aGVuIHNldCBvcHRpb24uZ3JvdXAuXG5cblxuICAgIGlmIChkcmFnRWwpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoL21vdXNlZG93bnxwb2ludGVyZG93bi8udGVzdCh0eXBlKSAmJiBldnQuYnV0dG9uICE9PSAwIHx8IG9wdGlvbnMuZGlzYWJsZWQpIHtcbiAgICAgIHJldHVybjsgLy8gb25seSBsZWZ0IGJ1dHRvbiBhbmQgZW5hYmxlZFxuICAgIH0gLy8gY2FuY2VsIGRuZCBpZiBvcmlnaW5hbCB0YXJnZXQgaXMgY29udGVudCBlZGl0YWJsZVxuXG5cbiAgICBpZiAob3JpZ2luYWxUYXJnZXQuaXNDb250ZW50RWRpdGFibGUpIHtcbiAgICAgIHJldHVybjtcbiAgICB9IC8vIFNhZmFyaSBpZ25vcmVzIGZ1cnRoZXIgZXZlbnQgaGFuZGxpbmcgYWZ0ZXIgbW91c2Vkb3duXG5cblxuICAgIGlmICghdGhpcy5uYXRpdmVEcmFnZ2FibGUgJiYgU2FmYXJpICYmIHRhcmdldCAmJiB0YXJnZXQudGFnTmFtZS50b1VwcGVyQ2FzZSgpID09PSAnU0VMRUNUJykge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRhcmdldCA9IGNsb3Nlc3QodGFyZ2V0LCBvcHRpb25zLmRyYWdnYWJsZSwgZWwsIGZhbHNlKTtcblxuICAgIGlmICh0YXJnZXQgJiYgdGFyZ2V0LmFuaW1hdGVkKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKGxhc3REb3duRWwgPT09IHRhcmdldCkge1xuICAgICAgLy8gSWdub3JpbmcgZHVwbGljYXRlIGBkb3duYFxuICAgICAgcmV0dXJuO1xuICAgIH0gLy8gR2V0IHRoZSBpbmRleCBvZiB0aGUgZHJhZ2dlZCBlbGVtZW50IHdpdGhpbiBpdHMgcGFyZW50XG5cblxuICAgIG9sZEluZGV4ID0gaW5kZXgodGFyZ2V0KTtcbiAgICBvbGREcmFnZ2FibGVJbmRleCA9IGluZGV4KHRhcmdldCwgb3B0aW9ucy5kcmFnZ2FibGUpOyAvLyBDaGVjayBmaWx0ZXJcblxuICAgIGlmICh0eXBlb2YgZmlsdGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICBpZiAoZmlsdGVyLmNhbGwodGhpcywgZXZ0LCB0YXJnZXQsIHRoaXMpKSB7XG4gICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICBzb3J0YWJsZTogX3RoaXMsXG4gICAgICAgICAgcm9vdEVsOiBvcmlnaW5hbFRhcmdldCxcbiAgICAgICAgICBuYW1lOiAnZmlsdGVyJyxcbiAgICAgICAgICB0YXJnZXRFbDogdGFyZ2V0LFxuICAgICAgICAgIHRvRWw6IGVsLFxuICAgICAgICAgIGZyb21FbDogZWxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgcGx1Z2luRXZlbnQoJ2ZpbHRlcicsIF90aGlzLCB7XG4gICAgICAgICAgZXZ0OiBldnRcbiAgICAgICAgfSk7XG4gICAgICAgIHByZXZlbnRPbkZpbHRlciAmJiBldnQuY2FuY2VsYWJsZSAmJiBldnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgcmV0dXJuOyAvLyBjYW5jZWwgZG5kXG4gICAgICB9XG4gICAgfSBlbHNlIGlmIChmaWx0ZXIpIHtcbiAgICAgIGZpbHRlciA9IGZpbHRlci5zcGxpdCgnLCcpLnNvbWUoZnVuY3Rpb24gKGNyaXRlcmlhKSB7XG4gICAgICAgIGNyaXRlcmlhID0gY2xvc2VzdChvcmlnaW5hbFRhcmdldCwgY3JpdGVyaWEudHJpbSgpLCBlbCwgZmFsc2UpO1xuXG4gICAgICAgIGlmIChjcml0ZXJpYSkge1xuICAgICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgIHNvcnRhYmxlOiBfdGhpcyxcbiAgICAgICAgICAgIHJvb3RFbDogY3JpdGVyaWEsXG4gICAgICAgICAgICBuYW1lOiAnZmlsdGVyJyxcbiAgICAgICAgICAgIHRhcmdldEVsOiB0YXJnZXQsXG4gICAgICAgICAgICBmcm9tRWw6IGVsLFxuICAgICAgICAgICAgdG9FbDogZWxcbiAgICAgICAgICB9KTtcblxuICAgICAgICAgIHBsdWdpbkV2ZW50KCdmaWx0ZXInLCBfdGhpcywge1xuICAgICAgICAgICAgZXZ0OiBldnRcbiAgICAgICAgICB9KTtcbiAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfVxuICAgICAgfSk7XG5cbiAgICAgIGlmIChmaWx0ZXIpIHtcbiAgICAgICAgcHJldmVudE9uRmlsdGVyICYmIGV2dC5jYW5jZWxhYmxlICYmIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICByZXR1cm47IC8vIGNhbmNlbCBkbmRcbiAgICAgIH1cbiAgICB9XG5cbiAgICBpZiAob3B0aW9ucy5oYW5kbGUgJiYgIWNsb3Nlc3Qob3JpZ2luYWxUYXJnZXQsIG9wdGlvbnMuaGFuZGxlLCBlbCwgZmFsc2UpKSB7XG4gICAgICByZXR1cm47XG4gICAgfSAvLyBQcmVwYXJlIGBkcmFnc3RhcnRgXG5cblxuICAgIHRoaXMuX3ByZXBhcmVEcmFnU3RhcnQoZXZ0LCB0b3VjaCwgdGFyZ2V0KTtcbiAgfSxcbiAgX3ByZXBhcmVEcmFnU3RhcnQ6IGZ1bmN0aW9uIF9wcmVwYXJlRHJhZ1N0YXJ0KFxuICAvKiogRXZlbnQgKi9cbiAgZXZ0LFxuICAvKiogVG91Y2ggKi9cbiAgdG91Y2gsXG4gIC8qKiBIVE1MRWxlbWVudCAqL1xuICB0YXJnZXQpIHtcbiAgICB2YXIgX3RoaXMgPSB0aGlzLFxuICAgICAgICBlbCA9IF90aGlzLmVsLFxuICAgICAgICBvcHRpb25zID0gX3RoaXMub3B0aW9ucyxcbiAgICAgICAgb3duZXJEb2N1bWVudCA9IGVsLm93bmVyRG9jdW1lbnQsXG4gICAgICAgIGRyYWdTdGFydEZuO1xuXG4gICAgaWYgKHRhcmdldCAmJiAhZHJhZ0VsICYmIHRhcmdldC5wYXJlbnROb2RlID09PSBlbCkge1xuICAgICAgdmFyIGRyYWdSZWN0ID0gZ2V0UmVjdCh0YXJnZXQpO1xuICAgICAgcm9vdEVsID0gZWw7XG4gICAgICBkcmFnRWwgPSB0YXJnZXQ7XG4gICAgICBwYXJlbnRFbCA9IGRyYWdFbC5wYXJlbnROb2RlO1xuICAgICAgbmV4dEVsID0gZHJhZ0VsLm5leHRTaWJsaW5nO1xuICAgICAgbGFzdERvd25FbCA9IHRhcmdldDtcbiAgICAgIGFjdGl2ZUdyb3VwID0gb3B0aW9ucy5ncm91cDtcbiAgICAgIFNvcnRhYmxlLmRyYWdnZWQgPSBkcmFnRWw7XG4gICAgICB0YXBFdnQgPSB7XG4gICAgICAgIHRhcmdldDogZHJhZ0VsLFxuICAgICAgICBjbGllbnRYOiAodG91Y2ggfHwgZXZ0KS5jbGllbnRYLFxuICAgICAgICBjbGllbnRZOiAodG91Y2ggfHwgZXZ0KS5jbGllbnRZXG4gICAgICB9O1xuICAgICAgdGFwRGlzdGFuY2VMZWZ0ID0gdGFwRXZ0LmNsaWVudFggLSBkcmFnUmVjdC5sZWZ0O1xuICAgICAgdGFwRGlzdGFuY2VUb3AgPSB0YXBFdnQuY2xpZW50WSAtIGRyYWdSZWN0LnRvcDtcbiAgICAgIHRoaXMuX2xhc3RYID0gKHRvdWNoIHx8IGV2dCkuY2xpZW50WDtcbiAgICAgIHRoaXMuX2xhc3RZID0gKHRvdWNoIHx8IGV2dCkuY2xpZW50WTtcbiAgICAgIGRyYWdFbC5zdHlsZVsnd2lsbC1jaGFuZ2UnXSA9ICdhbGwnO1xuXG4gICAgICBkcmFnU3RhcnRGbiA9IGZ1bmN0aW9uIGRyYWdTdGFydEZuKCkge1xuICAgICAgICBwbHVnaW5FdmVudCgnZGVsYXlFbmRlZCcsIF90aGlzLCB7XG4gICAgICAgICAgZXZ0OiBldnRcbiAgICAgICAgfSk7XG5cbiAgICAgICAgaWYgKFNvcnRhYmxlLmV2ZW50Q2FuY2VsZWQpIHtcbiAgICAgICAgICBfdGhpcy5fb25Ecm9wKCk7XG5cbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH0gLy8gRGVsYXllZCBkcmFnIGhhcyBiZWVuIHRyaWdnZXJlZFxuICAgICAgICAvLyB3ZSBjYW4gcmUtZW5hYmxlIHRoZSBldmVudHM6IHRvdWNobW92ZS9tb3VzZW1vdmVcblxuXG4gICAgICAgIF90aGlzLl9kaXNhYmxlRGVsYXllZERyYWdFdmVudHMoKTtcblxuICAgICAgICBpZiAoIUZpcmVGb3ggJiYgX3RoaXMubmF0aXZlRHJhZ2dhYmxlKSB7XG4gICAgICAgICAgZHJhZ0VsLmRyYWdnYWJsZSA9IHRydWU7XG4gICAgICAgIH0gLy8gQmluZCB0aGUgZXZlbnRzOiBkcmFnc3RhcnQvZHJhZ2VuZFxuXG5cbiAgICAgICAgX3RoaXMuX3RyaWdnZXJEcmFnU3RhcnQoZXZ0LCB0b3VjaCk7IC8vIERyYWcgc3RhcnQgZXZlbnRcblxuXG4gICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICBzb3J0YWJsZTogX3RoaXMsXG4gICAgICAgICAgbmFtZTogJ2Nob29zZScsXG4gICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgIH0pOyAvLyBDaG9zZW4gaXRlbVxuXG5cbiAgICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0VsLCBvcHRpb25zLmNob3NlbkNsYXNzLCB0cnVlKTtcbiAgICAgIH07IC8vIERpc2FibGUgXCJkcmFnZ2FibGVcIlxuXG5cbiAgICAgIG9wdGlvbnMuaWdub3JlLnNwbGl0KCcsJykuZm9yRWFjaChmdW5jdGlvbiAoY3JpdGVyaWEpIHtcbiAgICAgICAgZmluZChkcmFnRWwsIGNyaXRlcmlhLnRyaW0oKSwgX2Rpc2FibGVEcmFnZ2FibGUpO1xuICAgICAgfSk7XG4gICAgICBvbihvd25lckRvY3VtZW50LCAnZHJhZ292ZXInLCBuZWFyZXN0RW1wdHlJbnNlcnREZXRlY3RFdmVudCk7XG4gICAgICBvbihvd25lckRvY3VtZW50LCAnbW91c2Vtb3ZlJywgbmVhcmVzdEVtcHR5SW5zZXJ0RGV0ZWN0RXZlbnQpO1xuICAgICAgb24ob3duZXJEb2N1bWVudCwgJ3RvdWNobW92ZScsIG5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50KTtcbiAgICAgIG9uKG93bmVyRG9jdW1lbnQsICdtb3VzZXVwJywgX3RoaXMuX29uRHJvcCk7XG4gICAgICBvbihvd25lckRvY3VtZW50LCAndG91Y2hlbmQnLCBfdGhpcy5fb25Ecm9wKTtcbiAgICAgIG9uKG93bmVyRG9jdW1lbnQsICd0b3VjaGNhbmNlbCcsIF90aGlzLl9vbkRyb3ApOyAvLyBNYWtlIGRyYWdFbCBkcmFnZ2FibGUgKG11c3QgYmUgYmVmb3JlIGRlbGF5IGZvciBGaXJlRm94KVxuXG4gICAgICBpZiAoRmlyZUZveCAmJiB0aGlzLm5hdGl2ZURyYWdnYWJsZSkge1xuICAgICAgICB0aGlzLm9wdGlvbnMudG91Y2hTdGFydFRocmVzaG9sZCA9IDQ7XG4gICAgICAgIGRyYWdFbC5kcmFnZ2FibGUgPSB0cnVlO1xuICAgICAgfVxuXG4gICAgICBwbHVnaW5FdmVudCgnZGVsYXlTdGFydCcsIHRoaXMsIHtcbiAgICAgICAgZXZ0OiBldnRcbiAgICAgIH0pOyAvLyBEZWxheSBpcyBpbXBvc3NpYmxlIGZvciBuYXRpdmUgRG5EIGluIEVkZ2Ugb3IgSUVcblxuICAgICAgaWYgKG9wdGlvbnMuZGVsYXkgJiYgKCFvcHRpb25zLmRlbGF5T25Ub3VjaE9ubHkgfHwgdG91Y2gpICYmICghdGhpcy5uYXRpdmVEcmFnZ2FibGUgfHwgIShFZGdlIHx8IElFMTFPckxlc3MpKSkge1xuICAgICAgICBpZiAoU29ydGFibGUuZXZlbnRDYW5jZWxlZCkge1xuICAgICAgICAgIHRoaXMuX29uRHJvcCgpO1xuXG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9IC8vIElmIHRoZSB1c2VyIG1vdmVzIHRoZSBwb2ludGVyIG9yIGxldCBnbyB0aGUgY2xpY2sgb3IgdG91Y2hcbiAgICAgICAgLy8gYmVmb3JlIHRoZSBkZWxheSBoYXMgYmVlbiByZWFjaGVkOlxuICAgICAgICAvLyBkaXNhYmxlIHRoZSBkZWxheWVkIGRyYWdcblxuXG4gICAgICAgIG9uKG93bmVyRG9jdW1lbnQsICdtb3VzZXVwJywgX3RoaXMuX2Rpc2FibGVEZWxheWVkRHJhZyk7XG4gICAgICAgIG9uKG93bmVyRG9jdW1lbnQsICd0b3VjaGVuZCcsIF90aGlzLl9kaXNhYmxlRGVsYXllZERyYWcpO1xuICAgICAgICBvbihvd25lckRvY3VtZW50LCAndG91Y2hjYW5jZWwnLCBfdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnKTtcbiAgICAgICAgb24ob3duZXJEb2N1bWVudCwgJ21vdXNlbW92ZScsIF90aGlzLl9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXIpO1xuICAgICAgICBvbihvd25lckRvY3VtZW50LCAndG91Y2htb3ZlJywgX3RoaXMuX2RlbGF5ZWREcmFnVG91Y2hNb3ZlSGFuZGxlcik7XG4gICAgICAgIG9wdGlvbnMuc3VwcG9ydFBvaW50ZXIgJiYgb24ob3duZXJEb2N1bWVudCwgJ3BvaW50ZXJtb3ZlJywgX3RoaXMuX2RlbGF5ZWREcmFnVG91Y2hNb3ZlSGFuZGxlcik7XG4gICAgICAgIF90aGlzLl9kcmFnU3RhcnRUaW1lciA9IHNldFRpbWVvdXQoZHJhZ1N0YXJ0Rm4sIG9wdGlvbnMuZGVsYXkpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgZHJhZ1N0YXJ0Rm4oKTtcbiAgICAgIH1cbiAgICB9XG4gIH0sXG4gIF9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXI6IGZ1bmN0aW9uIF9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXIoXG4gIC8qKiBUb3VjaEV2ZW50fFBvaW50ZXJFdmVudCAqKi9cbiAgZSkge1xuICAgIHZhciB0b3VjaCA9IGUudG91Y2hlcyA/IGUudG91Y2hlc1swXSA6IGU7XG5cbiAgICBpZiAoTWF0aC5tYXgoTWF0aC5hYnModG91Y2guY2xpZW50WCAtIHRoaXMuX2xhc3RYKSwgTWF0aC5hYnModG91Y2guY2xpZW50WSAtIHRoaXMuX2xhc3RZKSkgPj0gTWF0aC5mbG9vcih0aGlzLm9wdGlvbnMudG91Y2hTdGFydFRocmVzaG9sZCAvICh0aGlzLm5hdGl2ZURyYWdnYWJsZSAmJiB3aW5kb3cuZGV2aWNlUGl4ZWxSYXRpbyB8fCAxKSkpIHtcbiAgICAgIHRoaXMuX2Rpc2FibGVEZWxheWVkRHJhZygpO1xuICAgIH1cbiAgfSxcbiAgX2Rpc2FibGVEZWxheWVkRHJhZzogZnVuY3Rpb24gX2Rpc2FibGVEZWxheWVkRHJhZygpIHtcbiAgICBkcmFnRWwgJiYgX2Rpc2FibGVEcmFnZ2FibGUoZHJhZ0VsKTtcbiAgICBjbGVhclRpbWVvdXQodGhpcy5fZHJhZ1N0YXJ0VGltZXIpO1xuXG4gICAgdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnRXZlbnRzKCk7XG4gIH0sXG4gIF9kaXNhYmxlRGVsYXllZERyYWdFdmVudHM6IGZ1bmN0aW9uIF9kaXNhYmxlRGVsYXllZERyYWdFdmVudHMoKSB7XG4gICAgdmFyIG93bmVyRG9jdW1lbnQgPSB0aGlzLmVsLm93bmVyRG9jdW1lbnQ7XG4gICAgb2ZmKG93bmVyRG9jdW1lbnQsICdtb3VzZXVwJywgdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnKTtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ3RvdWNoZW5kJywgdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnKTtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ3RvdWNoY2FuY2VsJywgdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnKTtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ21vdXNlbW92ZScsIHRoaXMuX2RlbGF5ZWREcmFnVG91Y2hNb3ZlSGFuZGxlcik7XG4gICAgb2ZmKG93bmVyRG9jdW1lbnQsICd0b3VjaG1vdmUnLCB0aGlzLl9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXIpO1xuICAgIG9mZihvd25lckRvY3VtZW50LCAncG9pbnRlcm1vdmUnLCB0aGlzLl9kZWxheWVkRHJhZ1RvdWNoTW92ZUhhbmRsZXIpO1xuICB9LFxuICBfdHJpZ2dlckRyYWdTdGFydDogZnVuY3Rpb24gX3RyaWdnZXJEcmFnU3RhcnQoXG4gIC8qKiBFdmVudCAqL1xuICBldnQsXG4gIC8qKiBUb3VjaCAqL1xuICB0b3VjaCkge1xuICAgIHRvdWNoID0gdG91Y2ggfHwgZXZ0LnBvaW50ZXJUeXBlID09ICd0b3VjaCcgJiYgZXZ0O1xuXG4gICAgaWYgKCF0aGlzLm5hdGl2ZURyYWdnYWJsZSB8fCB0b3VjaCkge1xuICAgICAgaWYgKHRoaXMub3B0aW9ucy5zdXBwb3J0UG9pbnRlcikge1xuICAgICAgICBvbihkb2N1bWVudCwgJ3BvaW50ZXJtb3ZlJywgdGhpcy5fb25Ub3VjaE1vdmUpO1xuICAgICAgfSBlbHNlIGlmICh0b3VjaCkge1xuICAgICAgICBvbihkb2N1bWVudCwgJ3RvdWNobW92ZScsIHRoaXMuX29uVG91Y2hNb3ZlKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIG9uKGRvY3VtZW50LCAnbW91c2Vtb3ZlJywgdGhpcy5fb25Ub3VjaE1vdmUpO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICBvbihkcmFnRWwsICdkcmFnZW5kJywgdGhpcyk7XG4gICAgICBvbihyb290RWwsICdkcmFnc3RhcnQnLCB0aGlzLl9vbkRyYWdTdGFydCk7XG4gICAgfVxuXG4gICAgdHJ5IHtcbiAgICAgIGlmIChkb2N1bWVudC5zZWxlY3Rpb24pIHtcbiAgICAgICAgLy8gVGltZW91dCBuZWNjZXNzYXJ5IGZvciBJRTlcbiAgICAgICAgX25leHRUaWNrKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICBkb2N1bWVudC5zZWxlY3Rpb24uZW1wdHkoKTtcbiAgICAgICAgfSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB3aW5kb3cuZ2V0U2VsZWN0aW9uKCkucmVtb3ZlQWxsUmFuZ2VzKCk7XG4gICAgICB9XG4gICAgfSBjYXRjaCAoZXJyKSB7fVxuICB9LFxuICBfZHJhZ1N0YXJ0ZWQ6IGZ1bmN0aW9uIF9kcmFnU3RhcnRlZChmYWxsYmFjaywgZXZ0KSB7XG5cbiAgICBhd2FpdGluZ0RyYWdTdGFydGVkID0gZmFsc2U7XG5cbiAgICBpZiAocm9vdEVsICYmIGRyYWdFbCkge1xuICAgICAgcGx1Z2luRXZlbnQoJ2RyYWdTdGFydGVkJywgdGhpcywge1xuICAgICAgICBldnQ6IGV2dFxuICAgICAgfSk7XG5cbiAgICAgIGlmICh0aGlzLm5hdGl2ZURyYWdnYWJsZSkge1xuICAgICAgICBvbihkb2N1bWVudCwgJ2RyYWdvdmVyJywgX2NoZWNrT3V0c2lkZVRhcmdldEVsKTtcbiAgICAgIH1cblxuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7IC8vIEFwcGx5IGVmZmVjdFxuXG4gICAgICAhZmFsbGJhY2sgJiYgdG9nZ2xlQ2xhc3MoZHJhZ0VsLCBvcHRpb25zLmRyYWdDbGFzcywgZmFsc2UpO1xuICAgICAgdG9nZ2xlQ2xhc3MoZHJhZ0VsLCBvcHRpb25zLmdob3N0Q2xhc3MsIHRydWUpO1xuICAgICAgU29ydGFibGUuYWN0aXZlID0gdGhpcztcbiAgICAgIGZhbGxiYWNrICYmIHRoaXMuX2FwcGVuZEdob3N0KCk7IC8vIERyYWcgc3RhcnQgZXZlbnRcblxuICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICBzb3J0YWJsZTogdGhpcyxcbiAgICAgICAgbmFtZTogJ3N0YXJ0JyxcbiAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICB9KTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5fbnVsbGluZygpO1xuICAgIH1cbiAgfSxcbiAgX2VtdWxhdGVEcmFnT3ZlcjogZnVuY3Rpb24gX2VtdWxhdGVEcmFnT3ZlcigpIHtcbiAgICBpZiAodG91Y2hFdnQpIHtcbiAgICAgIHRoaXMuX2xhc3RYID0gdG91Y2hFdnQuY2xpZW50WDtcbiAgICAgIHRoaXMuX2xhc3RZID0gdG91Y2hFdnQuY2xpZW50WTtcblxuICAgICAgX2hpZGVHaG9zdEZvclRhcmdldCgpO1xuXG4gICAgICB2YXIgdGFyZ2V0ID0gZG9jdW1lbnQuZWxlbWVudEZyb21Qb2ludCh0b3VjaEV2dC5jbGllbnRYLCB0b3VjaEV2dC5jbGllbnRZKTtcbiAgICAgIHZhciBwYXJlbnQgPSB0YXJnZXQ7XG5cbiAgICAgIHdoaWxlICh0YXJnZXQgJiYgdGFyZ2V0LnNoYWRvd1Jvb3QpIHtcbiAgICAgICAgdGFyZ2V0ID0gdGFyZ2V0LnNoYWRvd1Jvb3QuZWxlbWVudEZyb21Qb2ludCh0b3VjaEV2dC5jbGllbnRYLCB0b3VjaEV2dC5jbGllbnRZKTtcbiAgICAgICAgaWYgKHRhcmdldCA9PT0gcGFyZW50KSBicmVhaztcbiAgICAgICAgcGFyZW50ID0gdGFyZ2V0O1xuICAgICAgfVxuXG4gICAgICBkcmFnRWwucGFyZW50Tm9kZVtleHBhbmRvXS5faXNPdXRzaWRlVGhpc0VsKHRhcmdldCk7XG5cbiAgICAgIGlmIChwYXJlbnQpIHtcbiAgICAgICAgZG8ge1xuICAgICAgICAgIGlmIChwYXJlbnRbZXhwYW5kb10pIHtcbiAgICAgICAgICAgIHZhciBpbnNlcnRlZCA9IHZvaWQgMDtcbiAgICAgICAgICAgIGluc2VydGVkID0gcGFyZW50W2V4cGFuZG9dLl9vbkRyYWdPdmVyKHtcbiAgICAgICAgICAgICAgY2xpZW50WDogdG91Y2hFdnQuY2xpZW50WCxcbiAgICAgICAgICAgICAgY2xpZW50WTogdG91Y2hFdnQuY2xpZW50WSxcbiAgICAgICAgICAgICAgdGFyZ2V0OiB0YXJnZXQsXG4gICAgICAgICAgICAgIHJvb3RFbDogcGFyZW50XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgaWYgKGluc2VydGVkICYmICF0aGlzLm9wdGlvbnMuZHJhZ292ZXJCdWJibGUpIHtcbiAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgdGFyZ2V0ID0gcGFyZW50OyAvLyBzdG9yZSBsYXN0IGVsZW1lbnRcbiAgICAgICAgfVxuICAgICAgICAvKiBqc2hpbnQgYm9zczp0cnVlICovXG4gICAgICAgIHdoaWxlIChwYXJlbnQgPSBwYXJlbnQucGFyZW50Tm9kZSk7XG4gICAgICB9XG5cbiAgICAgIF91bmhpZGVHaG9zdEZvclRhcmdldCgpO1xuICAgIH1cbiAgfSxcbiAgX29uVG91Y2hNb3ZlOiBmdW5jdGlvbiBfb25Ub3VjaE1vdmUoXG4gIC8qKlRvdWNoRXZlbnQqL1xuICBldnQpIHtcbiAgICBpZiAodGFwRXZ0KSB7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgICBmYWxsYmFja1RvbGVyYW5jZSA9IG9wdGlvbnMuZmFsbGJhY2tUb2xlcmFuY2UsXG4gICAgICAgICAgZmFsbGJhY2tPZmZzZXQgPSBvcHRpb25zLmZhbGxiYWNrT2Zmc2V0LFxuICAgICAgICAgIHRvdWNoID0gZXZ0LnRvdWNoZXMgPyBldnQudG91Y2hlc1swXSA6IGV2dCxcbiAgICAgICAgICBnaG9zdE1hdHJpeCA9IGdob3N0RWwgJiYgbWF0cml4KGdob3N0RWwsIHRydWUpLFxuICAgICAgICAgIHNjYWxlWCA9IGdob3N0RWwgJiYgZ2hvc3RNYXRyaXggJiYgZ2hvc3RNYXRyaXguYSxcbiAgICAgICAgICBzY2FsZVkgPSBnaG9zdEVsICYmIGdob3N0TWF0cml4ICYmIGdob3N0TWF0cml4LmQsXG4gICAgICAgICAgcmVsYXRpdmVTY3JvbGxPZmZzZXQgPSBQb3NpdGlvbkdob3N0QWJzb2x1dGVseSAmJiBnaG9zdFJlbGF0aXZlUGFyZW50ICYmIGdldFJlbGF0aXZlU2Nyb2xsT2Zmc2V0KGdob3N0UmVsYXRpdmVQYXJlbnQpLFxuICAgICAgICAgIGR4ID0gKHRvdWNoLmNsaWVudFggLSB0YXBFdnQuY2xpZW50WCArIGZhbGxiYWNrT2Zmc2V0LngpIC8gKHNjYWxlWCB8fCAxKSArIChyZWxhdGl2ZVNjcm9sbE9mZnNldCA/IHJlbGF0aXZlU2Nyb2xsT2Zmc2V0WzBdIC0gZ2hvc3RSZWxhdGl2ZVBhcmVudEluaXRpYWxTY3JvbGxbMF0gOiAwKSAvIChzY2FsZVggfHwgMSksXG4gICAgICAgICAgZHkgPSAodG91Y2guY2xpZW50WSAtIHRhcEV2dC5jbGllbnRZICsgZmFsbGJhY2tPZmZzZXQueSkgLyAoc2NhbGVZIHx8IDEpICsgKHJlbGF0aXZlU2Nyb2xsT2Zmc2V0ID8gcmVsYXRpdmVTY3JvbGxPZmZzZXRbMV0gLSBnaG9zdFJlbGF0aXZlUGFyZW50SW5pdGlhbFNjcm9sbFsxXSA6IDApIC8gKHNjYWxlWSB8fCAxKTsgLy8gb25seSBzZXQgdGhlIHN0YXR1cyB0byBkcmFnZ2luZywgd2hlbiB3ZSBhcmUgYWN0dWFsbHkgZHJhZ2dpbmdcblxuICAgICAgaWYgKCFTb3J0YWJsZS5hY3RpdmUgJiYgIWF3YWl0aW5nRHJhZ1N0YXJ0ZWQpIHtcbiAgICAgICAgaWYgKGZhbGxiYWNrVG9sZXJhbmNlICYmIE1hdGgubWF4KE1hdGguYWJzKHRvdWNoLmNsaWVudFggLSB0aGlzLl9sYXN0WCksIE1hdGguYWJzKHRvdWNoLmNsaWVudFkgLSB0aGlzLl9sYXN0WSkpIDwgZmFsbGJhY2tUb2xlcmFuY2UpIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLl9vbkRyYWdTdGFydChldnQsIHRydWUpO1xuICAgICAgfVxuXG4gICAgICBpZiAoZ2hvc3RFbCkge1xuICAgICAgICBpZiAoZ2hvc3RNYXRyaXgpIHtcbiAgICAgICAgICBnaG9zdE1hdHJpeC5lICs9IGR4IC0gKGxhc3REeCB8fCAwKTtcbiAgICAgICAgICBnaG9zdE1hdHJpeC5mICs9IGR5IC0gKGxhc3REeSB8fCAwKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBnaG9zdE1hdHJpeCA9IHtcbiAgICAgICAgICAgIGE6IDEsXG4gICAgICAgICAgICBiOiAwLFxuICAgICAgICAgICAgYzogMCxcbiAgICAgICAgICAgIGQ6IDEsXG4gICAgICAgICAgICBlOiBkeCxcbiAgICAgICAgICAgIGY6IGR5XG4gICAgICAgICAgfTtcbiAgICAgICAgfVxuXG4gICAgICAgIHZhciBjc3NNYXRyaXggPSBcIm1hdHJpeChcIi5jb25jYXQoZ2hvc3RNYXRyaXguYSwgXCIsXCIpLmNvbmNhdChnaG9zdE1hdHJpeC5iLCBcIixcIikuY29uY2F0KGdob3N0TWF0cml4LmMsIFwiLFwiKS5jb25jYXQoZ2hvc3RNYXRyaXguZCwgXCIsXCIpLmNvbmNhdChnaG9zdE1hdHJpeC5lLCBcIixcIikuY29uY2F0KGdob3N0TWF0cml4LmYsIFwiKVwiKTtcbiAgICAgICAgY3NzKGdob3N0RWwsICd3ZWJraXRUcmFuc2Zvcm0nLCBjc3NNYXRyaXgpO1xuICAgICAgICBjc3MoZ2hvc3RFbCwgJ21velRyYW5zZm9ybScsIGNzc01hdHJpeCk7XG4gICAgICAgIGNzcyhnaG9zdEVsLCAnbXNUcmFuc2Zvcm0nLCBjc3NNYXRyaXgpO1xuICAgICAgICBjc3MoZ2hvc3RFbCwgJ3RyYW5zZm9ybScsIGNzc01hdHJpeCk7XG4gICAgICAgIGxhc3REeCA9IGR4O1xuICAgICAgICBsYXN0RHkgPSBkeTtcbiAgICAgICAgdG91Y2hFdnQgPSB0b3VjaDtcbiAgICAgIH1cblxuICAgICAgZXZ0LmNhbmNlbGFibGUgJiYgZXZ0LnByZXZlbnREZWZhdWx0KCk7XG4gICAgfVxuICB9LFxuICBfYXBwZW5kR2hvc3Q6IGZ1bmN0aW9uIF9hcHBlbmRHaG9zdCgpIHtcbiAgICAvLyBCdWcgaWYgdXNpbmcgc2NhbGUoKTogaHR0cHM6Ly9zdGFja292ZXJmbG93LmNvbS9xdWVzdGlvbnMvMjYzNzA1OFxuICAgIC8vIE5vdCBiZWluZyBhZGp1c3RlZCBmb3JcbiAgICBpZiAoIWdob3N0RWwpIHtcbiAgICAgIHZhciBjb250YWluZXIgPSB0aGlzLm9wdGlvbnMuZmFsbGJhY2tPbkJvZHkgPyBkb2N1bWVudC5ib2R5IDogcm9vdEVsLFxuICAgICAgICAgIHJlY3QgPSBnZXRSZWN0KGRyYWdFbCwgdHJ1ZSwgUG9zaXRpb25HaG9zdEFic29sdXRlbHksIHRydWUsIGNvbnRhaW5lciksXG4gICAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9uczsgLy8gUG9zaXRpb24gYWJzb2x1dGVseVxuXG4gICAgICBpZiAoUG9zaXRpb25HaG9zdEFic29sdXRlbHkpIHtcbiAgICAgICAgLy8gR2V0IHJlbGF0aXZlbHkgcG9zaXRpb25lZCBwYXJlbnRcbiAgICAgICAgZ2hvc3RSZWxhdGl2ZVBhcmVudCA9IGNvbnRhaW5lcjtcblxuICAgICAgICB3aGlsZSAoY3NzKGdob3N0UmVsYXRpdmVQYXJlbnQsICdwb3NpdGlvbicpID09PSAnc3RhdGljJyAmJiBjc3MoZ2hvc3RSZWxhdGl2ZVBhcmVudCwgJ3RyYW5zZm9ybScpID09PSAnbm9uZScgJiYgZ2hvc3RSZWxhdGl2ZVBhcmVudCAhPT0gZG9jdW1lbnQpIHtcbiAgICAgICAgICBnaG9zdFJlbGF0aXZlUGFyZW50ID0gZ2hvc3RSZWxhdGl2ZVBhcmVudC5wYXJlbnROb2RlO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGdob3N0UmVsYXRpdmVQYXJlbnQgIT09IGRvY3VtZW50LmJvZHkgJiYgZ2hvc3RSZWxhdGl2ZVBhcmVudCAhPT0gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50KSB7XG4gICAgICAgICAgaWYgKGdob3N0UmVsYXRpdmVQYXJlbnQgPT09IGRvY3VtZW50KSBnaG9zdFJlbGF0aXZlUGFyZW50ID0gZ2V0V2luZG93U2Nyb2xsaW5nRWxlbWVudCgpO1xuICAgICAgICAgIHJlY3QudG9wICs9IGdob3N0UmVsYXRpdmVQYXJlbnQuc2Nyb2xsVG9wO1xuICAgICAgICAgIHJlY3QubGVmdCArPSBnaG9zdFJlbGF0aXZlUGFyZW50LnNjcm9sbExlZnQ7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgZ2hvc3RSZWxhdGl2ZVBhcmVudCA9IGdldFdpbmRvd1Njcm9sbGluZ0VsZW1lbnQoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGdob3N0UmVsYXRpdmVQYXJlbnRJbml0aWFsU2Nyb2xsID0gZ2V0UmVsYXRpdmVTY3JvbGxPZmZzZXQoZ2hvc3RSZWxhdGl2ZVBhcmVudCk7XG4gICAgICB9XG5cbiAgICAgIGdob3N0RWwgPSBkcmFnRWwuY2xvbmVOb2RlKHRydWUpO1xuICAgICAgdG9nZ2xlQ2xhc3MoZ2hvc3RFbCwgb3B0aW9ucy5naG9zdENsYXNzLCBmYWxzZSk7XG4gICAgICB0b2dnbGVDbGFzcyhnaG9zdEVsLCBvcHRpb25zLmZhbGxiYWNrQ2xhc3MsIHRydWUpO1xuICAgICAgdG9nZ2xlQ2xhc3MoZ2hvc3RFbCwgb3B0aW9ucy5kcmFnQ2xhc3MsIHRydWUpO1xuICAgICAgY3NzKGdob3N0RWwsICd0cmFuc2l0aW9uJywgJycpO1xuICAgICAgY3NzKGdob3N0RWwsICd0cmFuc2Zvcm0nLCAnJyk7XG4gICAgICBjc3MoZ2hvc3RFbCwgJ2JveC1zaXppbmcnLCAnYm9yZGVyLWJveCcpO1xuICAgICAgY3NzKGdob3N0RWwsICdtYXJnaW4nLCAwKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAndG9wJywgcmVjdC50b3ApO1xuICAgICAgY3NzKGdob3N0RWwsICdsZWZ0JywgcmVjdC5sZWZ0KTtcbiAgICAgIGNzcyhnaG9zdEVsLCAnd2lkdGgnLCByZWN0LndpZHRoKTtcbiAgICAgIGNzcyhnaG9zdEVsLCAnaGVpZ2h0JywgcmVjdC5oZWlnaHQpO1xuICAgICAgY3NzKGdob3N0RWwsICdvcGFjaXR5JywgJzAuOCcpO1xuICAgICAgY3NzKGdob3N0RWwsICdwb3NpdGlvbicsIFBvc2l0aW9uR2hvc3RBYnNvbHV0ZWx5ID8gJ2Fic29sdXRlJyA6ICdmaXhlZCcpO1xuICAgICAgY3NzKGdob3N0RWwsICd6SW5kZXgnLCAnMTAwMDAwJyk7XG4gICAgICBjc3MoZ2hvc3RFbCwgJ3BvaW50ZXJFdmVudHMnLCAnbm9uZScpO1xuICAgICAgU29ydGFibGUuZ2hvc3QgPSBnaG9zdEVsO1xuICAgICAgY29udGFpbmVyLmFwcGVuZENoaWxkKGdob3N0RWwpOyAvLyBTZXQgdHJhbnNmb3JtLW9yaWdpblxuXG4gICAgICBjc3MoZ2hvc3RFbCwgJ3RyYW5zZm9ybS1vcmlnaW4nLCB0YXBEaXN0YW5jZUxlZnQgLyBwYXJzZUludChnaG9zdEVsLnN0eWxlLndpZHRoKSAqIDEwMCArICclICcgKyB0YXBEaXN0YW5jZVRvcCAvIHBhcnNlSW50KGdob3N0RWwuc3R5bGUuaGVpZ2h0KSAqIDEwMCArICclJyk7XG4gICAgfVxuICB9LFxuICBfb25EcmFnU3RhcnQ6IGZ1bmN0aW9uIF9vbkRyYWdTdGFydChcbiAgLyoqRXZlbnQqL1xuICBldnQsXG4gIC8qKmJvb2xlYW4qL1xuICBmYWxsYmFjaykge1xuICAgIHZhciBfdGhpcyA9IHRoaXM7XG5cbiAgICB2YXIgZGF0YVRyYW5zZmVyID0gZXZ0LmRhdGFUcmFuc2ZlcjtcbiAgICB2YXIgb3B0aW9ucyA9IF90aGlzLm9wdGlvbnM7XG4gICAgcGx1Z2luRXZlbnQoJ2RyYWdTdGFydCcsIHRoaXMsIHtcbiAgICAgIGV2dDogZXZ0XG4gICAgfSk7XG5cbiAgICBpZiAoU29ydGFibGUuZXZlbnRDYW5jZWxlZCkge1xuICAgICAgdGhpcy5fb25Ecm9wKCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBwbHVnaW5FdmVudCgnc2V0dXBDbG9uZScsIHRoaXMpO1xuXG4gICAgaWYgKCFTb3J0YWJsZS5ldmVudENhbmNlbGVkKSB7XG4gICAgICBjbG9uZUVsID0gY2xvbmUoZHJhZ0VsKTtcbiAgICAgIGNsb25lRWwucmVtb3ZlQXR0cmlidXRlKFwiaWRcIik7XG4gICAgICBjbG9uZUVsLmRyYWdnYWJsZSA9IGZhbHNlO1xuICAgICAgY2xvbmVFbC5zdHlsZVsnd2lsbC1jaGFuZ2UnXSA9ICcnO1xuXG4gICAgICB0aGlzLl9oaWRlQ2xvbmUoKTtcblxuICAgICAgdG9nZ2xlQ2xhc3MoY2xvbmVFbCwgdGhpcy5vcHRpb25zLmNob3NlbkNsYXNzLCBmYWxzZSk7XG4gICAgICBTb3J0YWJsZS5jbG9uZSA9IGNsb25lRWw7XG4gICAgfSAvLyAjMTE0MzogSUZyYW1lIHN1cHBvcnQgd29ya2Fyb3VuZFxuXG5cbiAgICBfdGhpcy5jbG9uZUlkID0gX25leHRUaWNrKGZ1bmN0aW9uICgpIHtcbiAgICAgIHBsdWdpbkV2ZW50KCdjbG9uZScsIF90aGlzKTtcbiAgICAgIGlmIChTb3J0YWJsZS5ldmVudENhbmNlbGVkKSByZXR1cm47XG5cbiAgICAgIGlmICghX3RoaXMub3B0aW9ucy5yZW1vdmVDbG9uZU9uSGlkZSkge1xuICAgICAgICByb290RWwuaW5zZXJ0QmVmb3JlKGNsb25lRWwsIGRyYWdFbCk7XG4gICAgICB9XG5cbiAgICAgIF90aGlzLl9oaWRlQ2xvbmUoKTtcblxuICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICBzb3J0YWJsZTogX3RoaXMsXG4gICAgICAgIG5hbWU6ICdjbG9uZSdcbiAgICAgIH0pO1xuICAgIH0pO1xuICAgICFmYWxsYmFjayAmJiB0b2dnbGVDbGFzcyhkcmFnRWwsIG9wdGlvbnMuZHJhZ0NsYXNzLCB0cnVlKTsgLy8gU2V0IHByb3BlciBkcm9wIGV2ZW50c1xuXG4gICAgaWYgKGZhbGxiYWNrKSB7XG4gICAgICBpZ25vcmVOZXh0Q2xpY2sgPSB0cnVlO1xuICAgICAgX3RoaXMuX2xvb3BJZCA9IHNldEludGVydmFsKF90aGlzLl9lbXVsYXRlRHJhZ092ZXIsIDUwKTtcbiAgICB9IGVsc2Uge1xuICAgICAgLy8gVW5kbyB3aGF0IHdhcyBzZXQgaW4gX3ByZXBhcmVEcmFnU3RhcnQgYmVmb3JlIGRyYWcgc3RhcnRlZFxuICAgICAgb2ZmKGRvY3VtZW50LCAnbW91c2V1cCcsIF90aGlzLl9vbkRyb3ApO1xuICAgICAgb2ZmKGRvY3VtZW50LCAndG91Y2hlbmQnLCBfdGhpcy5fb25Ecm9wKTtcbiAgICAgIG9mZihkb2N1bWVudCwgJ3RvdWNoY2FuY2VsJywgX3RoaXMuX29uRHJvcCk7XG5cbiAgICAgIGlmIChkYXRhVHJhbnNmZXIpIHtcbiAgICAgICAgZGF0YVRyYW5zZmVyLmVmZmVjdEFsbG93ZWQgPSAnbW92ZSc7XG4gICAgICAgIG9wdGlvbnMuc2V0RGF0YSAmJiBvcHRpb25zLnNldERhdGEuY2FsbChfdGhpcywgZGF0YVRyYW5zZmVyLCBkcmFnRWwpO1xuICAgICAgfVxuXG4gICAgICBvbihkb2N1bWVudCwgJ2Ryb3AnLCBfdGhpcyk7IC8vICMxMjc2IGZpeDpcblxuICAgICAgY3NzKGRyYWdFbCwgJ3RyYW5zZm9ybScsICd0cmFuc2xhdGVaKDApJyk7XG4gICAgfVxuXG4gICAgYXdhaXRpbmdEcmFnU3RhcnRlZCA9IHRydWU7XG4gICAgX3RoaXMuX2RyYWdTdGFydElkID0gX25leHRUaWNrKF90aGlzLl9kcmFnU3RhcnRlZC5iaW5kKF90aGlzLCBmYWxsYmFjaywgZXZ0KSk7XG4gICAgb24oZG9jdW1lbnQsICdzZWxlY3RzdGFydCcsIF90aGlzKTtcbiAgICBtb3ZlZCA9IHRydWU7XG5cbiAgICBpZiAoU2FmYXJpKSB7XG4gICAgICBjc3MoZG9jdW1lbnQuYm9keSwgJ3VzZXItc2VsZWN0JywgJ25vbmUnKTtcbiAgICB9XG4gIH0sXG4gIC8vIFJldHVybnMgdHJ1ZSAtIGlmIG5vIGZ1cnRoZXIgYWN0aW9uIGlzIG5lZWRlZCAoZWl0aGVyIGluc2VydGVkIG9yIGFub3RoZXIgY29uZGl0aW9uKVxuICBfb25EcmFnT3ZlcjogZnVuY3Rpb24gX29uRHJhZ092ZXIoXG4gIC8qKkV2ZW50Ki9cbiAgZXZ0KSB7XG4gICAgdmFyIGVsID0gdGhpcy5lbCxcbiAgICAgICAgdGFyZ2V0ID0gZXZ0LnRhcmdldCxcbiAgICAgICAgZHJhZ1JlY3QsXG4gICAgICAgIHRhcmdldFJlY3QsXG4gICAgICAgIHJldmVydCxcbiAgICAgICAgb3B0aW9ucyA9IHRoaXMub3B0aW9ucyxcbiAgICAgICAgZ3JvdXAgPSBvcHRpb25zLmdyb3VwLFxuICAgICAgICBhY3RpdmVTb3J0YWJsZSA9IFNvcnRhYmxlLmFjdGl2ZSxcbiAgICAgICAgaXNPd25lciA9IGFjdGl2ZUdyb3VwID09PSBncm91cCxcbiAgICAgICAgY2FuU29ydCA9IG9wdGlvbnMuc29ydCxcbiAgICAgICAgZnJvbVNvcnRhYmxlID0gcHV0U29ydGFibGUgfHwgYWN0aXZlU29ydGFibGUsXG4gICAgICAgIHZlcnRpY2FsLFxuICAgICAgICBfdGhpcyA9IHRoaXMsXG4gICAgICAgIGNvbXBsZXRlZEZpcmVkID0gZmFsc2U7XG5cbiAgICBpZiAoX3NpbGVudCkgcmV0dXJuO1xuXG4gICAgZnVuY3Rpb24gZHJhZ092ZXJFdmVudChuYW1lLCBleHRyYSkge1xuICAgICAgcGx1Z2luRXZlbnQobmFtZSwgX3RoaXMsIF9vYmplY3RTcHJlYWQyKHtcbiAgICAgICAgZXZ0OiBldnQsXG4gICAgICAgIGlzT3duZXI6IGlzT3duZXIsXG4gICAgICAgIGF4aXM6IHZlcnRpY2FsID8gJ3ZlcnRpY2FsJyA6ICdob3Jpem9udGFsJyxcbiAgICAgICAgcmV2ZXJ0OiByZXZlcnQsXG4gICAgICAgIGRyYWdSZWN0OiBkcmFnUmVjdCxcbiAgICAgICAgdGFyZ2V0UmVjdDogdGFyZ2V0UmVjdCxcbiAgICAgICAgY2FuU29ydDogY2FuU29ydCxcbiAgICAgICAgZnJvbVNvcnRhYmxlOiBmcm9tU29ydGFibGUsXG4gICAgICAgIHRhcmdldDogdGFyZ2V0LFxuICAgICAgICBjb21wbGV0ZWQ6IGNvbXBsZXRlZCxcbiAgICAgICAgb25Nb3ZlOiBmdW5jdGlvbiBvbk1vdmUodGFyZ2V0LCBhZnRlcikge1xuICAgICAgICAgIHJldHVybiBfb25Nb3ZlKHJvb3RFbCwgZWwsIGRyYWdFbCwgZHJhZ1JlY3QsIHRhcmdldCwgZ2V0UmVjdCh0YXJnZXQpLCBldnQsIGFmdGVyKTtcbiAgICAgICAgfSxcbiAgICAgICAgY2hhbmdlZDogY2hhbmdlZFxuICAgICAgfSwgZXh0cmEpKTtcbiAgICB9IC8vIENhcHR1cmUgYW5pbWF0aW9uIHN0YXRlXG5cblxuICAgIGZ1bmN0aW9uIGNhcHR1cmUoKSB7XG4gICAgICBkcmFnT3ZlckV2ZW50KCdkcmFnT3ZlckFuaW1hdGlvbkNhcHR1cmUnKTtcblxuICAgICAgX3RoaXMuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG5cbiAgICAgIGlmIChfdGhpcyAhPT0gZnJvbVNvcnRhYmxlKSB7XG4gICAgICAgIGZyb21Tb3J0YWJsZS5jYXB0dXJlQW5pbWF0aW9uU3RhdGUoKTtcbiAgICAgIH1cbiAgICB9IC8vIFJldHVybiBpbnZvY2F0aW9uIHdoZW4gZHJhZ0VsIGlzIGluc2VydGVkIChvciBjb21wbGV0ZWQpXG5cblxuICAgIGZ1bmN0aW9uIGNvbXBsZXRlZChpbnNlcnRpb24pIHtcbiAgICAgIGRyYWdPdmVyRXZlbnQoJ2RyYWdPdmVyQ29tcGxldGVkJywge1xuICAgICAgICBpbnNlcnRpb246IGluc2VydGlvblxuICAgICAgfSk7XG5cbiAgICAgIGlmIChpbnNlcnRpb24pIHtcbiAgICAgICAgLy8gQ2xvbmVzIG11c3QgYmUgaGlkZGVuIGJlZm9yZSBmb2xkaW5nIGFuaW1hdGlvbiB0byBjYXB0dXJlIGRyYWdSZWN0QWJzb2x1dGUgcHJvcGVybHlcbiAgICAgICAgaWYgKGlzT3duZXIpIHtcbiAgICAgICAgICBhY3RpdmVTb3J0YWJsZS5faGlkZUNsb25lKCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgYWN0aXZlU29ydGFibGUuX3Nob3dDbG9uZShfdGhpcyk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoX3RoaXMgIT09IGZyb21Tb3J0YWJsZSkge1xuICAgICAgICAgIC8vIFNldCBnaG9zdCBjbGFzcyB0byBuZXcgc29ydGFibGUncyBnaG9zdCBjbGFzc1xuICAgICAgICAgIHRvZ2dsZUNsYXNzKGRyYWdFbCwgcHV0U29ydGFibGUgPyBwdXRTb3J0YWJsZS5vcHRpb25zLmdob3N0Q2xhc3MgOiBhY3RpdmVTb3J0YWJsZS5vcHRpb25zLmdob3N0Q2xhc3MsIGZhbHNlKTtcbiAgICAgICAgICB0b2dnbGVDbGFzcyhkcmFnRWwsIG9wdGlvbnMuZ2hvc3RDbGFzcywgdHJ1ZSk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocHV0U29ydGFibGUgIT09IF90aGlzICYmIF90aGlzICE9PSBTb3J0YWJsZS5hY3RpdmUpIHtcbiAgICAgICAgICBwdXRTb3J0YWJsZSA9IF90aGlzO1xuICAgICAgICB9IGVsc2UgaWYgKF90aGlzID09PSBTb3J0YWJsZS5hY3RpdmUgJiYgcHV0U29ydGFibGUpIHtcbiAgICAgICAgICBwdXRTb3J0YWJsZSA9IG51bGw7XG4gICAgICAgIH0gLy8gQW5pbWF0aW9uXG5cblxuICAgICAgICBpZiAoZnJvbVNvcnRhYmxlID09PSBfdGhpcykge1xuICAgICAgICAgIF90aGlzLl9pZ25vcmVXaGlsZUFuaW1hdGluZyA9IHRhcmdldDtcbiAgICAgICAgfVxuXG4gICAgICAgIF90aGlzLmFuaW1hdGVBbGwoZnVuY3Rpb24gKCkge1xuICAgICAgICAgIGRyYWdPdmVyRXZlbnQoJ2RyYWdPdmVyQW5pbWF0aW9uQ29tcGxldGUnKTtcbiAgICAgICAgICBfdGhpcy5faWdub3JlV2hpbGVBbmltYXRpbmcgPSBudWxsO1xuICAgICAgICB9KTtcblxuICAgICAgICBpZiAoX3RoaXMgIT09IGZyb21Tb3J0YWJsZSkge1xuICAgICAgICAgIGZyb21Tb3J0YWJsZS5hbmltYXRlQWxsKCk7XG4gICAgICAgICAgZnJvbVNvcnRhYmxlLl9pZ25vcmVXaGlsZUFuaW1hdGluZyA9IG51bGw7XG4gICAgICAgIH1cbiAgICAgIH0gLy8gTnVsbCBsYXN0VGFyZ2V0IGlmIGl0IGlzIG5vdCBpbnNpZGUgYSBwcmV2aW91c2x5IHN3YXBwZWQgZWxlbWVudFxuXG5cbiAgICAgIGlmICh0YXJnZXQgPT09IGRyYWdFbCAmJiAhZHJhZ0VsLmFuaW1hdGVkIHx8IHRhcmdldCA9PT0gZWwgJiYgIXRhcmdldC5hbmltYXRlZCkge1xuICAgICAgICBsYXN0VGFyZ2V0ID0gbnVsbDtcbiAgICAgIH0gLy8gbm8gYnViYmxpbmcgYW5kIG5vdCBmYWxsYmFja1xuXG5cbiAgICAgIGlmICghb3B0aW9ucy5kcmFnb3ZlckJ1YmJsZSAmJiAhZXZ0LnJvb3RFbCAmJiB0YXJnZXQgIT09IGRvY3VtZW50KSB7XG4gICAgICAgIGRyYWdFbC5wYXJlbnROb2RlW2V4cGFuZG9dLl9pc091dHNpZGVUaGlzRWwoZXZ0LnRhcmdldCk7IC8vIERvIG5vdCBkZXRlY3QgZm9yIGVtcHR5IGluc2VydCBpZiBhbHJlYWR5IGluc2VydGVkXG5cblxuICAgICAgICAhaW5zZXJ0aW9uICYmIG5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50KGV2dCk7XG4gICAgICB9XG5cbiAgICAgICFvcHRpb25zLmRyYWdvdmVyQnViYmxlICYmIGV2dC5zdG9wUHJvcGFnYXRpb24gJiYgZXZ0LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgcmV0dXJuIGNvbXBsZXRlZEZpcmVkID0gdHJ1ZTtcbiAgICB9IC8vIENhbGwgd2hlbiBkcmFnRWwgaGFzIGJlZW4gaW5zZXJ0ZWRcblxuXG4gICAgZnVuY3Rpb24gY2hhbmdlZCgpIHtcbiAgICAgIG5ld0luZGV4ID0gaW5kZXgoZHJhZ0VsKTtcbiAgICAgIG5ld0RyYWdnYWJsZUluZGV4ID0gaW5kZXgoZHJhZ0VsLCBvcHRpb25zLmRyYWdnYWJsZSk7XG5cbiAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgc29ydGFibGU6IF90aGlzLFxuICAgICAgICBuYW1lOiAnY2hhbmdlJyxcbiAgICAgICAgdG9FbDogZWwsXG4gICAgICAgIG5ld0luZGV4OiBuZXdJbmRleCxcbiAgICAgICAgbmV3RHJhZ2dhYmxlSW5kZXg6IG5ld0RyYWdnYWJsZUluZGV4LFxuICAgICAgICBvcmlnaW5hbEV2ZW50OiBldnRcbiAgICAgIH0pO1xuICAgIH1cblxuICAgIGlmIChldnQucHJldmVudERlZmF1bHQgIT09IHZvaWQgMCkge1xuICAgICAgZXZ0LmNhbmNlbGFibGUgJiYgZXZ0LnByZXZlbnREZWZhdWx0KCk7XG4gICAgfVxuXG4gICAgdGFyZ2V0ID0gY2xvc2VzdCh0YXJnZXQsIG9wdGlvbnMuZHJhZ2dhYmxlLCBlbCwgdHJ1ZSk7XG4gICAgZHJhZ092ZXJFdmVudCgnZHJhZ092ZXInKTtcbiAgICBpZiAoU29ydGFibGUuZXZlbnRDYW5jZWxlZCkgcmV0dXJuIGNvbXBsZXRlZEZpcmVkO1xuXG4gICAgaWYgKGRyYWdFbC5jb250YWlucyhldnQudGFyZ2V0KSB8fCB0YXJnZXQuYW5pbWF0ZWQgJiYgdGFyZ2V0LmFuaW1hdGluZ1ggJiYgdGFyZ2V0LmFuaW1hdGluZ1kgfHwgX3RoaXMuX2lnbm9yZVdoaWxlQW5pbWF0aW5nID09PSB0YXJnZXQpIHtcbiAgICAgIHJldHVybiBjb21wbGV0ZWQoZmFsc2UpO1xuICAgIH1cblxuICAgIGlnbm9yZU5leHRDbGljayA9IGZhbHNlO1xuXG4gICAgaWYgKGFjdGl2ZVNvcnRhYmxlICYmICFvcHRpb25zLmRpc2FibGVkICYmIChpc093bmVyID8gY2FuU29ydCB8fCAocmV2ZXJ0ID0gcGFyZW50RWwgIT09IHJvb3RFbCkgLy8gUmV2ZXJ0aW5nIGl0ZW0gaW50byB0aGUgb3JpZ2luYWwgbGlzdFxuICAgIDogcHV0U29ydGFibGUgPT09IHRoaXMgfHwgKHRoaXMubGFzdFB1dE1vZGUgPSBhY3RpdmVHcm91cC5jaGVja1B1bGwodGhpcywgYWN0aXZlU29ydGFibGUsIGRyYWdFbCwgZXZ0KSkgJiYgZ3JvdXAuY2hlY2tQdXQodGhpcywgYWN0aXZlU29ydGFibGUsIGRyYWdFbCwgZXZ0KSkpIHtcbiAgICAgIHZlcnRpY2FsID0gdGhpcy5fZ2V0RGlyZWN0aW9uKGV2dCwgdGFyZ2V0KSA9PT0gJ3ZlcnRpY2FsJztcbiAgICAgIGRyYWdSZWN0ID0gZ2V0UmVjdChkcmFnRWwpO1xuICAgICAgZHJhZ092ZXJFdmVudCgnZHJhZ092ZXJWYWxpZCcpO1xuICAgICAgaWYgKFNvcnRhYmxlLmV2ZW50Q2FuY2VsZWQpIHJldHVybiBjb21wbGV0ZWRGaXJlZDtcblxuICAgICAgaWYgKHJldmVydCkge1xuICAgICAgICBwYXJlbnRFbCA9IHJvb3RFbDsgLy8gYWN0dWFsaXphdGlvblxuXG4gICAgICAgIGNhcHR1cmUoKTtcblxuICAgICAgICB0aGlzLl9oaWRlQ2xvbmUoKTtcblxuICAgICAgICBkcmFnT3ZlckV2ZW50KCdyZXZlcnQnKTtcblxuICAgICAgICBpZiAoIVNvcnRhYmxlLmV2ZW50Q2FuY2VsZWQpIHtcbiAgICAgICAgICBpZiAobmV4dEVsKSB7XG4gICAgICAgICAgICByb290RWwuaW5zZXJ0QmVmb3JlKGRyYWdFbCwgbmV4dEVsKTtcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgcm9vdEVsLmFwcGVuZENoaWxkKGRyYWdFbCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGNvbXBsZXRlZCh0cnVlKTtcbiAgICAgIH1cblxuICAgICAgdmFyIGVsTGFzdENoaWxkID0gbGFzdENoaWxkKGVsLCBvcHRpb25zLmRyYWdnYWJsZSk7XG5cbiAgICAgIGlmICghZWxMYXN0Q2hpbGQgfHwgX2dob3N0SXNMYXN0KGV2dCwgdmVydGljYWwsIHRoaXMpICYmICFlbExhc3RDaGlsZC5hbmltYXRlZCkge1xuICAgICAgICAvLyBJbnNlcnQgdG8gZW5kIG9mIGxpc3RcbiAgICAgICAgLy8gSWYgYWxyZWFkeSBhdCBlbmQgb2YgbGlzdDogRG8gbm90IGluc2VydFxuICAgICAgICBpZiAoZWxMYXN0Q2hpbGQgPT09IGRyYWdFbCkge1xuICAgICAgICAgIHJldHVybiBjb21wbGV0ZWQoZmFsc2UpO1xuICAgICAgICB9IC8vIGlmIHRoZXJlIGlzIGEgbGFzdCBlbGVtZW50LCBpdCBpcyB0aGUgdGFyZ2V0XG5cblxuICAgICAgICBpZiAoZWxMYXN0Q2hpbGQgJiYgZWwgPT09IGV2dC50YXJnZXQpIHtcbiAgICAgICAgICB0YXJnZXQgPSBlbExhc3RDaGlsZDtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICh0YXJnZXQpIHtcbiAgICAgICAgICB0YXJnZXRSZWN0ID0gZ2V0UmVjdCh0YXJnZXQpO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKF9vbk1vdmUocm9vdEVsLCBlbCwgZHJhZ0VsLCBkcmFnUmVjdCwgdGFyZ2V0LCB0YXJnZXRSZWN0LCBldnQsICEhdGFyZ2V0KSAhPT0gZmFsc2UpIHtcbiAgICAgICAgICBjYXB0dXJlKCk7XG5cbiAgICAgICAgICBpZiAoZWxMYXN0Q2hpbGQgJiYgZWxMYXN0Q2hpbGQubmV4dFNpYmxpbmcpIHtcbiAgICAgICAgICAgIC8vIHRoZSBsYXN0IGRyYWdnYWJsZSBlbGVtZW50IGlzIG5vdCB0aGUgbGFzdCBub2RlXG4gICAgICAgICAgICBlbC5pbnNlcnRCZWZvcmUoZHJhZ0VsLCBlbExhc3RDaGlsZC5uZXh0U2libGluZyk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGVsLmFwcGVuZENoaWxkKGRyYWdFbCk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgcGFyZW50RWwgPSBlbDsgLy8gYWN0dWFsaXphdGlvblxuXG4gICAgICAgICAgY2hhbmdlZCgpO1xuICAgICAgICAgIHJldHVybiBjb21wbGV0ZWQodHJ1ZSk7XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSBpZiAoZWxMYXN0Q2hpbGQgJiYgX2dob3N0SXNGaXJzdChldnQsIHZlcnRpY2FsLCB0aGlzKSkge1xuICAgICAgICAvLyBJbnNlcnQgdG8gc3RhcnQgb2YgbGlzdFxuICAgICAgICB2YXIgZmlyc3RDaGlsZCA9IGdldENoaWxkKGVsLCAwLCBvcHRpb25zLCB0cnVlKTtcblxuICAgICAgICBpZiAoZmlyc3RDaGlsZCA9PT0gZHJhZ0VsKSB7XG4gICAgICAgICAgcmV0dXJuIGNvbXBsZXRlZChmYWxzZSk7XG4gICAgICAgIH1cblxuICAgICAgICB0YXJnZXQgPSBmaXJzdENoaWxkO1xuICAgICAgICB0YXJnZXRSZWN0ID0gZ2V0UmVjdCh0YXJnZXQpO1xuXG4gICAgICAgIGlmIChfb25Nb3ZlKHJvb3RFbCwgZWwsIGRyYWdFbCwgZHJhZ1JlY3QsIHRhcmdldCwgdGFyZ2V0UmVjdCwgZXZ0LCBmYWxzZSkgIT09IGZhbHNlKSB7XG4gICAgICAgICAgY2FwdHVyZSgpO1xuICAgICAgICAgIGVsLmluc2VydEJlZm9yZShkcmFnRWwsIGZpcnN0Q2hpbGQpO1xuICAgICAgICAgIHBhcmVudEVsID0gZWw7IC8vIGFjdHVhbGl6YXRpb25cblxuICAgICAgICAgIGNoYW5nZWQoKTtcbiAgICAgICAgICByZXR1cm4gY29tcGxldGVkKHRydWUpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKHRhcmdldC5wYXJlbnROb2RlID09PSBlbCkge1xuICAgICAgICB0YXJnZXRSZWN0ID0gZ2V0UmVjdCh0YXJnZXQpO1xuICAgICAgICB2YXIgZGlyZWN0aW9uID0gMCxcbiAgICAgICAgICAgIHRhcmdldEJlZm9yZUZpcnN0U3dhcCxcbiAgICAgICAgICAgIGRpZmZlcmVudExldmVsID0gZHJhZ0VsLnBhcmVudE5vZGUgIT09IGVsLFxuICAgICAgICAgICAgZGlmZmVyZW50Um93Q29sID0gIV9kcmFnRWxJblJvd0NvbHVtbihkcmFnRWwuYW5pbWF0ZWQgJiYgZHJhZ0VsLnRvUmVjdCB8fCBkcmFnUmVjdCwgdGFyZ2V0LmFuaW1hdGVkICYmIHRhcmdldC50b1JlY3QgfHwgdGFyZ2V0UmVjdCwgdmVydGljYWwpLFxuICAgICAgICAgICAgc2lkZTEgPSB2ZXJ0aWNhbCA/ICd0b3AnIDogJ2xlZnQnLFxuICAgICAgICAgICAgc2Nyb2xsZWRQYXN0VG9wID0gaXNTY3JvbGxlZFBhc3QodGFyZ2V0LCAndG9wJywgJ3RvcCcpIHx8IGlzU2Nyb2xsZWRQYXN0KGRyYWdFbCwgJ3RvcCcsICd0b3AnKSxcbiAgICAgICAgICAgIHNjcm9sbEJlZm9yZSA9IHNjcm9sbGVkUGFzdFRvcCA/IHNjcm9sbGVkUGFzdFRvcC5zY3JvbGxUb3AgOiB2b2lkIDA7XG5cbiAgICAgICAgaWYgKGxhc3RUYXJnZXQgIT09IHRhcmdldCkge1xuICAgICAgICAgIHRhcmdldEJlZm9yZUZpcnN0U3dhcCA9IHRhcmdldFJlY3Rbc2lkZTFdO1xuICAgICAgICAgIHBhc3RGaXJzdEludmVydFRocmVzaCA9IGZhbHNlO1xuICAgICAgICAgIGlzQ2lyY3Vtc3RhbnRpYWxJbnZlcnQgPSAhZGlmZmVyZW50Um93Q29sICYmIG9wdGlvbnMuaW52ZXJ0U3dhcCB8fCBkaWZmZXJlbnRMZXZlbDtcbiAgICAgICAgfVxuXG4gICAgICAgIGRpcmVjdGlvbiA9IF9nZXRTd2FwRGlyZWN0aW9uKGV2dCwgdGFyZ2V0LCB0YXJnZXRSZWN0LCB2ZXJ0aWNhbCwgZGlmZmVyZW50Um93Q29sID8gMSA6IG9wdGlvbnMuc3dhcFRocmVzaG9sZCwgb3B0aW9ucy5pbnZlcnRlZFN3YXBUaHJlc2hvbGQgPT0gbnVsbCA/IG9wdGlvbnMuc3dhcFRocmVzaG9sZCA6IG9wdGlvbnMuaW52ZXJ0ZWRTd2FwVGhyZXNob2xkLCBpc0NpcmN1bXN0YW50aWFsSW52ZXJ0LCBsYXN0VGFyZ2V0ID09PSB0YXJnZXQpO1xuICAgICAgICB2YXIgc2libGluZztcblxuICAgICAgICBpZiAoZGlyZWN0aW9uICE9PSAwKSB7XG4gICAgICAgICAgLy8gQ2hlY2sgaWYgdGFyZ2V0IGlzIGJlc2lkZSBkcmFnRWwgaW4gcmVzcGVjdGl2ZSBkaXJlY3Rpb24gKGlnbm9yaW5nIGhpZGRlbiBlbGVtZW50cylcbiAgICAgICAgICB2YXIgZHJhZ0luZGV4ID0gaW5kZXgoZHJhZ0VsKTtcblxuICAgICAgICAgIGRvIHtcbiAgICAgICAgICAgIGRyYWdJbmRleCAtPSBkaXJlY3Rpb247XG4gICAgICAgICAgICBzaWJsaW5nID0gcGFyZW50RWwuY2hpbGRyZW5bZHJhZ0luZGV4XTtcbiAgICAgICAgICB9IHdoaWxlIChzaWJsaW5nICYmIChjc3Moc2libGluZywgJ2Rpc3BsYXknKSA9PT0gJ25vbmUnIHx8IHNpYmxpbmcgPT09IGdob3N0RWwpKTtcbiAgICAgICAgfSAvLyBJZiBkcmFnRWwgaXMgYWxyZWFkeSBiZXNpZGUgdGFyZ2V0OiBEbyBub3QgaW5zZXJ0XG5cblxuICAgICAgICBpZiAoZGlyZWN0aW9uID09PSAwIHx8IHNpYmxpbmcgPT09IHRhcmdldCkge1xuICAgICAgICAgIHJldHVybiBjb21wbGV0ZWQoZmFsc2UpO1xuICAgICAgICB9XG5cbiAgICAgICAgbGFzdFRhcmdldCA9IHRhcmdldDtcbiAgICAgICAgbGFzdERpcmVjdGlvbiA9IGRpcmVjdGlvbjtcbiAgICAgICAgdmFyIG5leHRTaWJsaW5nID0gdGFyZ2V0Lm5leHRFbGVtZW50U2libGluZyxcbiAgICAgICAgICAgIGFmdGVyID0gZmFsc2U7XG4gICAgICAgIGFmdGVyID0gZGlyZWN0aW9uID09PSAxO1xuXG4gICAgICAgIHZhciBtb3ZlVmVjdG9yID0gX29uTW92ZShyb290RWwsIGVsLCBkcmFnRWwsIGRyYWdSZWN0LCB0YXJnZXQsIHRhcmdldFJlY3QsIGV2dCwgYWZ0ZXIpO1xuXG4gICAgICAgIGlmIChtb3ZlVmVjdG9yICE9PSBmYWxzZSkge1xuICAgICAgICAgIGlmIChtb3ZlVmVjdG9yID09PSAxIHx8IG1vdmVWZWN0b3IgPT09IC0xKSB7XG4gICAgICAgICAgICBhZnRlciA9IG1vdmVWZWN0b3IgPT09IDE7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgX3NpbGVudCA9IHRydWU7XG4gICAgICAgICAgc2V0VGltZW91dChfdW5zaWxlbnQsIDMwKTtcbiAgICAgICAgICBjYXB0dXJlKCk7XG5cbiAgICAgICAgICBpZiAoYWZ0ZXIgJiYgIW5leHRTaWJsaW5nKSB7XG4gICAgICAgICAgICBlbC5hcHBlbmRDaGlsZChkcmFnRWwpO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB0YXJnZXQucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoZHJhZ0VsLCBhZnRlciA/IG5leHRTaWJsaW5nIDogdGFyZ2V0KTtcbiAgICAgICAgICB9IC8vIFVuZG8gY2hyb21lJ3Mgc2Nyb2xsIGFkanVzdG1lbnQgKGhhcyBubyBlZmZlY3Qgb24gb3RoZXIgYnJvd3NlcnMpXG5cblxuICAgICAgICAgIGlmIChzY3JvbGxlZFBhc3RUb3ApIHtcbiAgICAgICAgICAgIHNjcm9sbEJ5KHNjcm9sbGVkUGFzdFRvcCwgMCwgc2Nyb2xsQmVmb3JlIC0gc2Nyb2xsZWRQYXN0VG9wLnNjcm9sbFRvcCk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgcGFyZW50RWwgPSBkcmFnRWwucGFyZW50Tm9kZTsgLy8gYWN0dWFsaXphdGlvblxuICAgICAgICAgIC8vIG11c3QgYmUgZG9uZSBiZWZvcmUgYW5pbWF0aW9uXG5cbiAgICAgICAgICBpZiAodGFyZ2V0QmVmb3JlRmlyc3RTd2FwICE9PSB1bmRlZmluZWQgJiYgIWlzQ2lyY3Vtc3RhbnRpYWxJbnZlcnQpIHtcbiAgICAgICAgICAgIHRhcmdldE1vdmVEaXN0YW5jZSA9IE1hdGguYWJzKHRhcmdldEJlZm9yZUZpcnN0U3dhcCAtIGdldFJlY3QodGFyZ2V0KVtzaWRlMV0pO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIGNoYW5nZWQoKTtcbiAgICAgICAgICByZXR1cm4gY29tcGxldGVkKHRydWUpO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIGlmIChlbC5jb250YWlucyhkcmFnRWwpKSB7XG4gICAgICAgIHJldHVybiBjb21wbGV0ZWQoZmFsc2UpO1xuICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBmYWxzZTtcbiAgfSxcbiAgX2lnbm9yZVdoaWxlQW5pbWF0aW5nOiBudWxsLFxuICBfb2ZmTW92ZUV2ZW50czogZnVuY3Rpb24gX29mZk1vdmVFdmVudHMoKSB7XG4gICAgb2ZmKGRvY3VtZW50LCAnbW91c2Vtb3ZlJywgdGhpcy5fb25Ub3VjaE1vdmUpO1xuICAgIG9mZihkb2N1bWVudCwgJ3RvdWNobW92ZScsIHRoaXMuX29uVG91Y2hNb3ZlKTtcbiAgICBvZmYoZG9jdW1lbnQsICdwb2ludGVybW92ZScsIHRoaXMuX29uVG91Y2hNb3ZlKTtcbiAgICBvZmYoZG9jdW1lbnQsICdkcmFnb3ZlcicsIG5lYXJlc3RFbXB0eUluc2VydERldGVjdEV2ZW50KTtcbiAgICBvZmYoZG9jdW1lbnQsICdtb3VzZW1vdmUnLCBuZWFyZXN0RW1wdHlJbnNlcnREZXRlY3RFdmVudCk7XG4gICAgb2ZmKGRvY3VtZW50LCAndG91Y2htb3ZlJywgbmVhcmVzdEVtcHR5SW5zZXJ0RGV0ZWN0RXZlbnQpO1xuICB9LFxuICBfb2ZmVXBFdmVudHM6IGZ1bmN0aW9uIF9vZmZVcEV2ZW50cygpIHtcbiAgICB2YXIgb3duZXJEb2N1bWVudCA9IHRoaXMuZWwub3duZXJEb2N1bWVudDtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ21vdXNldXAnLCB0aGlzLl9vbkRyb3ApO1xuICAgIG9mZihvd25lckRvY3VtZW50LCAndG91Y2hlbmQnLCB0aGlzLl9vbkRyb3ApO1xuICAgIG9mZihvd25lckRvY3VtZW50LCAncG9pbnRlcnVwJywgdGhpcy5fb25Ecm9wKTtcbiAgICBvZmYob3duZXJEb2N1bWVudCwgJ3RvdWNoY2FuY2VsJywgdGhpcy5fb25Ecm9wKTtcbiAgICBvZmYoZG9jdW1lbnQsICdzZWxlY3RzdGFydCcsIHRoaXMpO1xuICB9LFxuICBfb25Ecm9wOiBmdW5jdGlvbiBfb25Ecm9wKFxuICAvKipFdmVudCovXG4gIGV2dCkge1xuICAgIHZhciBlbCA9IHRoaXMuZWwsXG4gICAgICAgIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7IC8vIEdldCB0aGUgaW5kZXggb2YgdGhlIGRyYWdnZWQgZWxlbWVudCB3aXRoaW4gaXRzIHBhcmVudFxuXG4gICAgbmV3SW5kZXggPSBpbmRleChkcmFnRWwpO1xuICAgIG5ld0RyYWdnYWJsZUluZGV4ID0gaW5kZXgoZHJhZ0VsLCBvcHRpb25zLmRyYWdnYWJsZSk7XG4gICAgcGx1Z2luRXZlbnQoJ2Ryb3AnLCB0aGlzLCB7XG4gICAgICBldnQ6IGV2dFxuICAgIH0pO1xuICAgIHBhcmVudEVsID0gZHJhZ0VsICYmIGRyYWdFbC5wYXJlbnROb2RlOyAvLyBHZXQgYWdhaW4gYWZ0ZXIgcGx1Z2luIGV2ZW50XG5cbiAgICBuZXdJbmRleCA9IGluZGV4KGRyYWdFbCk7XG4gICAgbmV3RHJhZ2dhYmxlSW5kZXggPSBpbmRleChkcmFnRWwsIG9wdGlvbnMuZHJhZ2dhYmxlKTtcblxuICAgIGlmIChTb3J0YWJsZS5ldmVudENhbmNlbGVkKSB7XG4gICAgICB0aGlzLl9udWxsaW5nKCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBhd2FpdGluZ0RyYWdTdGFydGVkID0gZmFsc2U7XG4gICAgaXNDaXJjdW1zdGFudGlhbEludmVydCA9IGZhbHNlO1xuICAgIHBhc3RGaXJzdEludmVydFRocmVzaCA9IGZhbHNlO1xuICAgIGNsZWFySW50ZXJ2YWwodGhpcy5fbG9vcElkKTtcbiAgICBjbGVhclRpbWVvdXQodGhpcy5fZHJhZ1N0YXJ0VGltZXIpO1xuXG4gICAgX2NhbmNlbE5leHRUaWNrKHRoaXMuY2xvbmVJZCk7XG5cbiAgICBfY2FuY2VsTmV4dFRpY2sodGhpcy5fZHJhZ1N0YXJ0SWQpOyAvLyBVbmJpbmQgZXZlbnRzXG5cblxuICAgIGlmICh0aGlzLm5hdGl2ZURyYWdnYWJsZSkge1xuICAgICAgb2ZmKGRvY3VtZW50LCAnZHJvcCcsIHRoaXMpO1xuICAgICAgb2ZmKGVsLCAnZHJhZ3N0YXJ0JywgdGhpcy5fb25EcmFnU3RhcnQpO1xuICAgIH1cblxuICAgIHRoaXMuX29mZk1vdmVFdmVudHMoKTtcblxuICAgIHRoaXMuX29mZlVwRXZlbnRzKCk7XG5cbiAgICBpZiAoU2FmYXJpKSB7XG4gICAgICBjc3MoZG9jdW1lbnQuYm9keSwgJ3VzZXItc2VsZWN0JywgJycpO1xuICAgIH1cblxuICAgIGNzcyhkcmFnRWwsICd0cmFuc2Zvcm0nLCAnJyk7XG5cbiAgICBpZiAoZXZ0KSB7XG4gICAgICBpZiAobW92ZWQpIHtcbiAgICAgICAgZXZ0LmNhbmNlbGFibGUgJiYgZXZ0LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICFvcHRpb25zLmRyb3BCdWJibGUgJiYgZXZ0LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgfVxuXG4gICAgICBnaG9zdEVsICYmIGdob3N0RWwucGFyZW50Tm9kZSAmJiBnaG9zdEVsLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoZ2hvc3RFbCk7XG5cbiAgICAgIGlmIChyb290RWwgPT09IHBhcmVudEVsIHx8IHB1dFNvcnRhYmxlICYmIHB1dFNvcnRhYmxlLmxhc3RQdXRNb2RlICE9PSAnY2xvbmUnKSB7XG4gICAgICAgIC8vIFJlbW92ZSBjbG9uZShzKVxuICAgICAgICBjbG9uZUVsICYmIGNsb25lRWwucGFyZW50Tm9kZSAmJiBjbG9uZUVsLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoY2xvbmVFbCk7XG4gICAgICB9XG5cbiAgICAgIGlmIChkcmFnRWwpIHtcbiAgICAgICAgaWYgKHRoaXMubmF0aXZlRHJhZ2dhYmxlKSB7XG4gICAgICAgICAgb2ZmKGRyYWdFbCwgJ2RyYWdlbmQnLCB0aGlzKTtcbiAgICAgICAgfVxuXG4gICAgICAgIF9kaXNhYmxlRHJhZ2dhYmxlKGRyYWdFbCk7XG5cbiAgICAgICAgZHJhZ0VsLnN0eWxlWyd3aWxsLWNoYW5nZSddID0gJyc7IC8vIFJlbW92ZSBjbGFzc2VzXG4gICAgICAgIC8vIGdob3N0Q2xhc3MgaXMgYWRkZWQgaW4gZHJhZ1N0YXJ0ZWRcblxuICAgICAgICBpZiAobW92ZWQgJiYgIWF3YWl0aW5nRHJhZ1N0YXJ0ZWQpIHtcbiAgICAgICAgICB0b2dnbGVDbGFzcyhkcmFnRWwsIHB1dFNvcnRhYmxlID8gcHV0U29ydGFibGUub3B0aW9ucy5naG9zdENsYXNzIDogdGhpcy5vcHRpb25zLmdob3N0Q2xhc3MsIGZhbHNlKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRvZ2dsZUNsYXNzKGRyYWdFbCwgdGhpcy5vcHRpb25zLmNob3NlbkNsYXNzLCBmYWxzZSk7IC8vIERyYWcgc3RvcCBldmVudFxuXG4gICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICBzb3J0YWJsZTogdGhpcyxcbiAgICAgICAgICBuYW1lOiAndW5jaG9vc2UnLFxuICAgICAgICAgIHRvRWw6IHBhcmVudEVsLFxuICAgICAgICAgIG5ld0luZGV4OiBudWxsLFxuICAgICAgICAgIG5ld0RyYWdnYWJsZUluZGV4OiBudWxsLFxuICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICB9KTtcblxuICAgICAgICBpZiAocm9vdEVsICE9PSBwYXJlbnRFbCkge1xuICAgICAgICAgIGlmIChuZXdJbmRleCA+PSAwKSB7XG4gICAgICAgICAgICAvLyBBZGQgZXZlbnRcbiAgICAgICAgICAgIF9kaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICAgICAgcm9vdEVsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgbmFtZTogJ2FkZCcsXG4gICAgICAgICAgICAgIHRvRWw6IHBhcmVudEVsLFxuICAgICAgICAgICAgICBmcm9tRWw6IHJvb3RFbCxcbiAgICAgICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgICAgICB9KTsgLy8gUmVtb3ZlIGV2ZW50XG5cblxuICAgICAgICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgICBzb3J0YWJsZTogdGhpcyxcbiAgICAgICAgICAgICAgbmFtZTogJ3JlbW92ZScsXG4gICAgICAgICAgICAgIHRvRWw6IHBhcmVudEVsLFxuICAgICAgICAgICAgICBvcmlnaW5hbEV2ZW50OiBldnRcbiAgICAgICAgICAgIH0pOyAvLyBkcmFnIGZyb20gb25lIGxpc3QgYW5kIGRyb3AgaW50byBhbm90aGVyXG5cblxuICAgICAgICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgICByb290RWw6IHBhcmVudEVsLFxuICAgICAgICAgICAgICBuYW1lOiAnc29ydCcsXG4gICAgICAgICAgICAgIHRvRWw6IHBhcmVudEVsLFxuICAgICAgICAgICAgICBmcm9tRWw6IHJvb3RFbCxcbiAgICAgICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgICBzb3J0YWJsZTogdGhpcyxcbiAgICAgICAgICAgICAgbmFtZTogJ3NvcnQnLFxuICAgICAgICAgICAgICB0b0VsOiBwYXJlbnRFbCxcbiAgICAgICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBwdXRTb3J0YWJsZSAmJiBwdXRTb3J0YWJsZS5zYXZlKCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgaWYgKG5ld0luZGV4ICE9PSBvbGRJbmRleCkge1xuICAgICAgICAgICAgaWYgKG5ld0luZGV4ID49IDApIHtcbiAgICAgICAgICAgICAgLy8gZHJhZyAmIGRyb3Agd2l0aGluIHRoZSBzYW1lIGxpc3RcbiAgICAgICAgICAgICAgX2Rpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgICAgIHNvcnRhYmxlOiB0aGlzLFxuICAgICAgICAgICAgICAgIG5hbWU6ICd1cGRhdGUnLFxuICAgICAgICAgICAgICAgIHRvRWw6IHBhcmVudEVsLFxuICAgICAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgICAgICAgc29ydGFibGU6IHRoaXMsXG4gICAgICAgICAgICAgICAgbmFtZTogJ3NvcnQnLFxuICAgICAgICAgICAgICAgIHRvRWw6IHBhcmVudEVsLFxuICAgICAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoU29ydGFibGUuYWN0aXZlKSB7XG4gICAgICAgICAgLyoganNoaW50IGVxbnVsbDp0cnVlICovXG4gICAgICAgICAgaWYgKG5ld0luZGV4ID09IG51bGwgfHwgbmV3SW5kZXggPT09IC0xKSB7XG4gICAgICAgICAgICBuZXdJbmRleCA9IG9sZEluZGV4O1xuICAgICAgICAgICAgbmV3RHJhZ2dhYmxlSW5kZXggPSBvbGREcmFnZ2FibGVJbmRleDtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBfZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgICBzb3J0YWJsZTogdGhpcyxcbiAgICAgICAgICAgIG5hbWU6ICdlbmQnLFxuICAgICAgICAgICAgdG9FbDogcGFyZW50RWwsXG4gICAgICAgICAgICBvcmlnaW5hbEV2ZW50OiBldnRcbiAgICAgICAgICB9KTsgLy8gU2F2ZSBzb3J0aW5nXG5cblxuICAgICAgICAgIHRoaXMuc2F2ZSgpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuXG4gICAgdGhpcy5fbnVsbGluZygpO1xuICB9LFxuICBfbnVsbGluZzogZnVuY3Rpb24gX251bGxpbmcoKSB7XG4gICAgcGx1Z2luRXZlbnQoJ251bGxpbmcnLCB0aGlzKTtcbiAgICByb290RWwgPSBkcmFnRWwgPSBwYXJlbnRFbCA9IGdob3N0RWwgPSBuZXh0RWwgPSBjbG9uZUVsID0gbGFzdERvd25FbCA9IGNsb25lSGlkZGVuID0gdGFwRXZ0ID0gdG91Y2hFdnQgPSBtb3ZlZCA9IG5ld0luZGV4ID0gbmV3RHJhZ2dhYmxlSW5kZXggPSBvbGRJbmRleCA9IG9sZERyYWdnYWJsZUluZGV4ID0gbGFzdFRhcmdldCA9IGxhc3REaXJlY3Rpb24gPSBwdXRTb3J0YWJsZSA9IGFjdGl2ZUdyb3VwID0gU29ydGFibGUuZHJhZ2dlZCA9IFNvcnRhYmxlLmdob3N0ID0gU29ydGFibGUuY2xvbmUgPSBTb3J0YWJsZS5hY3RpdmUgPSBudWxsO1xuICAgIHNhdmVkSW5wdXRDaGVja2VkLmZvckVhY2goZnVuY3Rpb24gKGVsKSB7XG4gICAgICBlbC5jaGVja2VkID0gdHJ1ZTtcbiAgICB9KTtcbiAgICBzYXZlZElucHV0Q2hlY2tlZC5sZW5ndGggPSBsYXN0RHggPSBsYXN0RHkgPSAwO1xuICB9LFxuICBoYW5kbGVFdmVudDogZnVuY3Rpb24gaGFuZGxlRXZlbnQoXG4gIC8qKkV2ZW50Ki9cbiAgZXZ0KSB7XG4gICAgc3dpdGNoIChldnQudHlwZSkge1xuICAgICAgY2FzZSAnZHJvcCc6XG4gICAgICBjYXNlICdkcmFnZW5kJzpcbiAgICAgICAgdGhpcy5fb25Ecm9wKGV2dCk7XG5cbiAgICAgICAgYnJlYWs7XG5cbiAgICAgIGNhc2UgJ2RyYWdlbnRlcic6XG4gICAgICBjYXNlICdkcmFnb3Zlcic6XG4gICAgICAgIGlmIChkcmFnRWwpIHtcbiAgICAgICAgICB0aGlzLl9vbkRyYWdPdmVyKGV2dCk7XG5cbiAgICAgICAgICBfZ2xvYmFsRHJhZ092ZXIoZXZ0KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGJyZWFrO1xuXG4gICAgICBjYXNlICdzZWxlY3RzdGFydCc6XG4gICAgICAgIGV2dC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBicmVhaztcbiAgICB9XG4gIH0sXG5cbiAgLyoqXHJcbiAgICogU2VyaWFsaXplcyB0aGUgaXRlbSBpbnRvIGFuIGFycmF5IG9mIHN0cmluZy5cclxuICAgKiBAcmV0dXJucyB7U3RyaW5nW119XHJcbiAgICovXG4gIHRvQXJyYXk6IGZ1bmN0aW9uIHRvQXJyYXkoKSB7XG4gICAgdmFyIG9yZGVyID0gW10sXG4gICAgICAgIGVsLFxuICAgICAgICBjaGlsZHJlbiA9IHRoaXMuZWwuY2hpbGRyZW4sXG4gICAgICAgIGkgPSAwLFxuICAgICAgICBuID0gY2hpbGRyZW4ubGVuZ3RoLFxuICAgICAgICBvcHRpb25zID0gdGhpcy5vcHRpb25zO1xuXG4gICAgZm9yICg7IGkgPCBuOyBpKyspIHtcbiAgICAgIGVsID0gY2hpbGRyZW5baV07XG5cbiAgICAgIGlmIChjbG9zZXN0KGVsLCBvcHRpb25zLmRyYWdnYWJsZSwgdGhpcy5lbCwgZmFsc2UpKSB7XG4gICAgICAgIG9yZGVyLnB1c2goZWwuZ2V0QXR0cmlidXRlKG9wdGlvbnMuZGF0YUlkQXR0cikgfHwgX2dlbmVyYXRlSWQoZWwpKTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gb3JkZXI7XG4gIH0sXG5cbiAgLyoqXHJcbiAgICogU29ydHMgdGhlIGVsZW1lbnRzIGFjY29yZGluZyB0byB0aGUgYXJyYXkuXHJcbiAgICogQHBhcmFtICB7U3RyaW5nW119ICBvcmRlciAgb3JkZXIgb2YgdGhlIGl0ZW1zXHJcbiAgICovXG4gIHNvcnQ6IGZ1bmN0aW9uIHNvcnQob3JkZXIsIHVzZUFuaW1hdGlvbikge1xuICAgIHZhciBpdGVtcyA9IHt9LFxuICAgICAgICByb290RWwgPSB0aGlzLmVsO1xuICAgIHRoaXMudG9BcnJheSgpLmZvckVhY2goZnVuY3Rpb24gKGlkLCBpKSB7XG4gICAgICB2YXIgZWwgPSByb290RWwuY2hpbGRyZW5baV07XG5cbiAgICAgIGlmIChjbG9zZXN0KGVsLCB0aGlzLm9wdGlvbnMuZHJhZ2dhYmxlLCByb290RWwsIGZhbHNlKSkge1xuICAgICAgICBpdGVtc1tpZF0gPSBlbDtcbiAgICAgIH1cbiAgICB9LCB0aGlzKTtcbiAgICB1c2VBbmltYXRpb24gJiYgdGhpcy5jYXB0dXJlQW5pbWF0aW9uU3RhdGUoKTtcbiAgICBvcmRlci5mb3JFYWNoKGZ1bmN0aW9uIChpZCkge1xuICAgICAgaWYgKGl0ZW1zW2lkXSkge1xuICAgICAgICByb290RWwucmVtb3ZlQ2hpbGQoaXRlbXNbaWRdKTtcbiAgICAgICAgcm9vdEVsLmFwcGVuZENoaWxkKGl0ZW1zW2lkXSk7XG4gICAgICB9XG4gICAgfSk7XG4gICAgdXNlQW5pbWF0aW9uICYmIHRoaXMuYW5pbWF0ZUFsbCgpO1xuICB9LFxuXG4gIC8qKlxyXG4gICAqIFNhdmUgdGhlIGN1cnJlbnQgc29ydGluZ1xyXG4gICAqL1xuICBzYXZlOiBmdW5jdGlvbiBzYXZlKCkge1xuICAgIHZhciBzdG9yZSA9IHRoaXMub3B0aW9ucy5zdG9yZTtcbiAgICBzdG9yZSAmJiBzdG9yZS5zZXQgJiYgc3RvcmUuc2V0KHRoaXMpO1xuICB9LFxuXG4gIC8qKlxyXG4gICAqIEZvciBlYWNoIGVsZW1lbnQgaW4gdGhlIHNldCwgZ2V0IHRoZSBmaXJzdCBlbGVtZW50IHRoYXQgbWF0Y2hlcyB0aGUgc2VsZWN0b3IgYnkgdGVzdGluZyB0aGUgZWxlbWVudCBpdHNlbGYgYW5kIHRyYXZlcnNpbmcgdXAgdGhyb3VnaCBpdHMgYW5jZXN0b3JzIGluIHRoZSBET00gdHJlZS5cclxuICAgKiBAcGFyYW0gICB7SFRNTEVsZW1lbnR9ICBlbFxyXG4gICAqIEBwYXJhbSAgIHtTdHJpbmd9ICAgICAgIFtzZWxlY3Rvcl0gIGRlZmF1bHQ6IGBvcHRpb25zLmRyYWdnYWJsZWBcclxuICAgKiBAcmV0dXJucyB7SFRNTEVsZW1lbnR8bnVsbH1cclxuICAgKi9cbiAgY2xvc2VzdDogZnVuY3Rpb24gY2xvc2VzdCQxKGVsLCBzZWxlY3Rvcikge1xuICAgIHJldHVybiBjbG9zZXN0KGVsLCBzZWxlY3RvciB8fCB0aGlzLm9wdGlvbnMuZHJhZ2dhYmxlLCB0aGlzLmVsLCBmYWxzZSk7XG4gIH0sXG5cbiAgLyoqXHJcbiAgICogU2V0L2dldCBvcHRpb25cclxuICAgKiBAcGFyYW0gICB7c3RyaW5nfSBuYW1lXHJcbiAgICogQHBhcmFtICAgeyp9ICAgICAgW3ZhbHVlXVxyXG4gICAqIEByZXR1cm5zIHsqfVxyXG4gICAqL1xuICBvcHRpb246IGZ1bmN0aW9uIG9wdGlvbihuYW1lLCB2YWx1ZSkge1xuICAgIHZhciBvcHRpb25zID0gdGhpcy5vcHRpb25zO1xuXG4gICAgaWYgKHZhbHVlID09PSB2b2lkIDApIHtcbiAgICAgIHJldHVybiBvcHRpb25zW25hbWVdO1xuICAgIH0gZWxzZSB7XG4gICAgICB2YXIgbW9kaWZpZWRWYWx1ZSA9IFBsdWdpbk1hbmFnZXIubW9kaWZ5T3B0aW9uKHRoaXMsIG5hbWUsIHZhbHVlKTtcblxuICAgICAgaWYgKHR5cGVvZiBtb2RpZmllZFZhbHVlICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBvcHRpb25zW25hbWVdID0gbW9kaWZpZWRWYWx1ZTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIG9wdGlvbnNbbmFtZV0gPSB2YWx1ZTtcbiAgICAgIH1cblxuICAgICAgaWYgKG5hbWUgPT09ICdncm91cCcpIHtcbiAgICAgICAgX3ByZXBhcmVHcm91cChvcHRpb25zKTtcbiAgICAgIH1cbiAgICB9XG4gIH0sXG5cbiAgLyoqXHJcbiAgICogRGVzdHJveVxyXG4gICAqL1xuICBkZXN0cm95OiBmdW5jdGlvbiBkZXN0cm95KCkge1xuICAgIHBsdWdpbkV2ZW50KCdkZXN0cm95JywgdGhpcyk7XG4gICAgdmFyIGVsID0gdGhpcy5lbDtcbiAgICBlbFtleHBhbmRvXSA9IG51bGw7XG4gICAgb2ZmKGVsLCAnbW91c2Vkb3duJywgdGhpcy5fb25UYXBTdGFydCk7XG4gICAgb2ZmKGVsLCAndG91Y2hzdGFydCcsIHRoaXMuX29uVGFwU3RhcnQpO1xuICAgIG9mZihlbCwgJ3BvaW50ZXJkb3duJywgdGhpcy5fb25UYXBTdGFydCk7XG5cbiAgICBpZiAodGhpcy5uYXRpdmVEcmFnZ2FibGUpIHtcbiAgICAgIG9mZihlbCwgJ2RyYWdvdmVyJywgdGhpcyk7XG4gICAgICBvZmYoZWwsICdkcmFnZW50ZXInLCB0aGlzKTtcbiAgICB9IC8vIFJlbW92ZSBkcmFnZ2FibGUgYXR0cmlidXRlc1xuXG5cbiAgICBBcnJheS5wcm90b3R5cGUuZm9yRWFjaC5jYWxsKGVsLnF1ZXJ5U2VsZWN0b3JBbGwoJ1tkcmFnZ2FibGVdJyksIGZ1bmN0aW9uIChlbCkge1xuICAgICAgZWwucmVtb3ZlQXR0cmlidXRlKCdkcmFnZ2FibGUnKTtcbiAgICB9KTtcblxuICAgIHRoaXMuX29uRHJvcCgpO1xuXG4gICAgdGhpcy5fZGlzYWJsZURlbGF5ZWREcmFnRXZlbnRzKCk7XG5cbiAgICBzb3J0YWJsZXMuc3BsaWNlKHNvcnRhYmxlcy5pbmRleE9mKHRoaXMuZWwpLCAxKTtcbiAgICB0aGlzLmVsID0gZWwgPSBudWxsO1xuICB9LFxuICBfaGlkZUNsb25lOiBmdW5jdGlvbiBfaGlkZUNsb25lKCkge1xuICAgIGlmICghY2xvbmVIaWRkZW4pIHtcbiAgICAgIHBsdWdpbkV2ZW50KCdoaWRlQ2xvbmUnLCB0aGlzKTtcbiAgICAgIGlmIChTb3J0YWJsZS5ldmVudENhbmNlbGVkKSByZXR1cm47XG4gICAgICBjc3MoY2xvbmVFbCwgJ2Rpc3BsYXknLCAnbm9uZScpO1xuXG4gICAgICBpZiAodGhpcy5vcHRpb25zLnJlbW92ZUNsb25lT25IaWRlICYmIGNsb25lRWwucGFyZW50Tm9kZSkge1xuICAgICAgICBjbG9uZUVsLnBhcmVudE5vZGUucmVtb3ZlQ2hpbGQoY2xvbmVFbCk7XG4gICAgICB9XG5cbiAgICAgIGNsb25lSGlkZGVuID0gdHJ1ZTtcbiAgICB9XG4gIH0sXG4gIF9zaG93Q2xvbmU6IGZ1bmN0aW9uIF9zaG93Q2xvbmUocHV0U29ydGFibGUpIHtcbiAgICBpZiAocHV0U29ydGFibGUubGFzdFB1dE1vZGUgIT09ICdjbG9uZScpIHtcbiAgICAgIHRoaXMuX2hpZGVDbG9uZSgpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgaWYgKGNsb25lSGlkZGVuKSB7XG4gICAgICBwbHVnaW5FdmVudCgnc2hvd0Nsb25lJywgdGhpcyk7XG4gICAgICBpZiAoU29ydGFibGUuZXZlbnRDYW5jZWxlZCkgcmV0dXJuOyAvLyBzaG93IGNsb25lIGF0IGRyYWdFbCBvciBvcmlnaW5hbCBwb3NpdGlvblxuXG4gICAgICBpZiAoZHJhZ0VsLnBhcmVudE5vZGUgPT0gcm9vdEVsICYmICF0aGlzLm9wdGlvbnMuZ3JvdXAucmV2ZXJ0Q2xvbmUpIHtcbiAgICAgICAgcm9vdEVsLmluc2VydEJlZm9yZShjbG9uZUVsLCBkcmFnRWwpO1xuICAgICAgfSBlbHNlIGlmIChuZXh0RWwpIHtcbiAgICAgICAgcm9vdEVsLmluc2VydEJlZm9yZShjbG9uZUVsLCBuZXh0RWwpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgcm9vdEVsLmFwcGVuZENoaWxkKGNsb25lRWwpO1xuICAgICAgfVxuXG4gICAgICBpZiAodGhpcy5vcHRpb25zLmdyb3VwLnJldmVydENsb25lKSB7XG4gICAgICAgIHRoaXMuYW5pbWF0ZShkcmFnRWwsIGNsb25lRWwpO1xuICAgICAgfVxuXG4gICAgICBjc3MoY2xvbmVFbCwgJ2Rpc3BsYXknLCAnJyk7XG4gICAgICBjbG9uZUhpZGRlbiA9IGZhbHNlO1xuICAgIH1cbiAgfVxufTtcblxuZnVuY3Rpb24gX2dsb2JhbERyYWdPdmVyKFxuLyoqRXZlbnQqL1xuZXZ0KSB7XG4gIGlmIChldnQuZGF0YVRyYW5zZmVyKSB7XG4gICAgZXZ0LmRhdGFUcmFuc2Zlci5kcm9wRWZmZWN0ID0gJ21vdmUnO1xuICB9XG5cbiAgZXZ0LmNhbmNlbGFibGUgJiYgZXZ0LnByZXZlbnREZWZhdWx0KCk7XG59XG5cbmZ1bmN0aW9uIF9vbk1vdmUoZnJvbUVsLCB0b0VsLCBkcmFnRWwsIGRyYWdSZWN0LCB0YXJnZXRFbCwgdGFyZ2V0UmVjdCwgb3JpZ2luYWxFdmVudCwgd2lsbEluc2VydEFmdGVyKSB7XG4gIHZhciBldnQsXG4gICAgICBzb3J0YWJsZSA9IGZyb21FbFtleHBhbmRvXSxcbiAgICAgIG9uTW92ZUZuID0gc29ydGFibGUub3B0aW9ucy5vbk1vdmUsXG4gICAgICByZXRWYWw7IC8vIFN1cHBvcnQgZm9yIG5ldyBDdXN0b21FdmVudCBmZWF0dXJlXG5cbiAgaWYgKHdpbmRvdy5DdXN0b21FdmVudCAmJiAhSUUxMU9yTGVzcyAmJiAhRWRnZSkge1xuICAgIGV2dCA9IG5ldyBDdXN0b21FdmVudCgnbW92ZScsIHtcbiAgICAgIGJ1YmJsZXM6IHRydWUsXG4gICAgICBjYW5jZWxhYmxlOiB0cnVlXG4gICAgfSk7XG4gIH0gZWxzZSB7XG4gICAgZXZ0ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoJ0V2ZW50Jyk7XG4gICAgZXZ0LmluaXRFdmVudCgnbW92ZScsIHRydWUsIHRydWUpO1xuICB9XG5cbiAgZXZ0LnRvID0gdG9FbDtcbiAgZXZ0LmZyb20gPSBmcm9tRWw7XG4gIGV2dC5kcmFnZ2VkID0gZHJhZ0VsO1xuICBldnQuZHJhZ2dlZFJlY3QgPSBkcmFnUmVjdDtcbiAgZXZ0LnJlbGF0ZWQgPSB0YXJnZXRFbCB8fCB0b0VsO1xuICBldnQucmVsYXRlZFJlY3QgPSB0YXJnZXRSZWN0IHx8IGdldFJlY3QodG9FbCk7XG4gIGV2dC53aWxsSW5zZXJ0QWZ0ZXIgPSB3aWxsSW5zZXJ0QWZ0ZXI7XG4gIGV2dC5vcmlnaW5hbEV2ZW50ID0gb3JpZ2luYWxFdmVudDtcbiAgZnJvbUVsLmRpc3BhdGNoRXZlbnQoZXZ0KTtcblxuICBpZiAob25Nb3ZlRm4pIHtcbiAgICByZXRWYWwgPSBvbk1vdmVGbi5jYWxsKHNvcnRhYmxlLCBldnQsIG9yaWdpbmFsRXZlbnQpO1xuICB9XG5cbiAgcmV0dXJuIHJldFZhbDtcbn1cblxuZnVuY3Rpb24gX2Rpc2FibGVEcmFnZ2FibGUoZWwpIHtcbiAgZWwuZHJhZ2dhYmxlID0gZmFsc2U7XG59XG5cbmZ1bmN0aW9uIF91bnNpbGVudCgpIHtcbiAgX3NpbGVudCA9IGZhbHNlO1xufVxuXG5mdW5jdGlvbiBfZ2hvc3RJc0ZpcnN0KGV2dCwgdmVydGljYWwsIHNvcnRhYmxlKSB7XG4gIHZhciByZWN0ID0gZ2V0UmVjdChnZXRDaGlsZChzb3J0YWJsZS5lbCwgMCwgc29ydGFibGUub3B0aW9ucywgdHJ1ZSkpO1xuICB2YXIgc3BhY2VyID0gMTA7XG4gIHJldHVybiB2ZXJ0aWNhbCA/IGV2dC5jbGllbnRYIDwgcmVjdC5sZWZ0IC0gc3BhY2VyIHx8IGV2dC5jbGllbnRZIDwgcmVjdC50b3AgJiYgZXZ0LmNsaWVudFggPCByZWN0LnJpZ2h0IDogZXZ0LmNsaWVudFkgPCByZWN0LnRvcCAtIHNwYWNlciB8fCBldnQuY2xpZW50WSA8IHJlY3QuYm90dG9tICYmIGV2dC5jbGllbnRYIDwgcmVjdC5sZWZ0O1xufVxuXG5mdW5jdGlvbiBfZ2hvc3RJc0xhc3QoZXZ0LCB2ZXJ0aWNhbCwgc29ydGFibGUpIHtcbiAgdmFyIHJlY3QgPSBnZXRSZWN0KGxhc3RDaGlsZChzb3J0YWJsZS5lbCwgc29ydGFibGUub3B0aW9ucy5kcmFnZ2FibGUpKTtcbiAgdmFyIHNwYWNlciA9IDEwO1xuICByZXR1cm4gdmVydGljYWwgPyBldnQuY2xpZW50WCA+IHJlY3QucmlnaHQgKyBzcGFjZXIgfHwgZXZ0LmNsaWVudFggPD0gcmVjdC5yaWdodCAmJiBldnQuY2xpZW50WSA+IHJlY3QuYm90dG9tICYmIGV2dC5jbGllbnRYID49IHJlY3QubGVmdCA6IGV2dC5jbGllbnRYID4gcmVjdC5yaWdodCAmJiBldnQuY2xpZW50WSA+IHJlY3QudG9wIHx8IGV2dC5jbGllbnRYIDw9IHJlY3QucmlnaHQgJiYgZXZ0LmNsaWVudFkgPiByZWN0LmJvdHRvbSArIHNwYWNlcjtcbn1cblxuZnVuY3Rpb24gX2dldFN3YXBEaXJlY3Rpb24oZXZ0LCB0YXJnZXQsIHRhcmdldFJlY3QsIHZlcnRpY2FsLCBzd2FwVGhyZXNob2xkLCBpbnZlcnRlZFN3YXBUaHJlc2hvbGQsIGludmVydFN3YXAsIGlzTGFzdFRhcmdldCkge1xuICB2YXIgbW91c2VPbkF4aXMgPSB2ZXJ0aWNhbCA/IGV2dC5jbGllbnRZIDogZXZ0LmNsaWVudFgsXG4gICAgICB0YXJnZXRMZW5ndGggPSB2ZXJ0aWNhbCA/IHRhcmdldFJlY3QuaGVpZ2h0IDogdGFyZ2V0UmVjdC53aWR0aCxcbiAgICAgIHRhcmdldFMxID0gdmVydGljYWwgPyB0YXJnZXRSZWN0LnRvcCA6IHRhcmdldFJlY3QubGVmdCxcbiAgICAgIHRhcmdldFMyID0gdmVydGljYWwgPyB0YXJnZXRSZWN0LmJvdHRvbSA6IHRhcmdldFJlY3QucmlnaHQsXG4gICAgICBpbnZlcnQgPSBmYWxzZTtcblxuICBpZiAoIWludmVydFN3YXApIHtcbiAgICAvLyBOZXZlciBpbnZlcnQgb3IgY3JlYXRlIGRyYWdFbCBzaGFkb3cgd2hlbiB0YXJnZXQgbW92ZW1lbmV0IGNhdXNlcyBtb3VzZSB0byBtb3ZlIHBhc3QgdGhlIGVuZCBvZiByZWd1bGFyIHN3YXBUaHJlc2hvbGRcbiAgICBpZiAoaXNMYXN0VGFyZ2V0ICYmIHRhcmdldE1vdmVEaXN0YW5jZSA8IHRhcmdldExlbmd0aCAqIHN3YXBUaHJlc2hvbGQpIHtcbiAgICAgIC8vIG11bHRpcGxpZWQgb25seSBieSBzd2FwVGhyZXNob2xkIGJlY2F1c2UgbW91c2Ugd2lsbCBhbHJlYWR5IGJlIGluc2lkZSB0YXJnZXQgYnkgKDEgLSB0aHJlc2hvbGQpICogdGFyZ2V0TGVuZ3RoIC8gMlxuICAgICAgLy8gY2hlY2sgaWYgcGFzdCBmaXJzdCBpbnZlcnQgdGhyZXNob2xkIG9uIHNpZGUgb3Bwb3NpdGUgb2YgbGFzdERpcmVjdGlvblxuICAgICAgaWYgKCFwYXN0Rmlyc3RJbnZlcnRUaHJlc2ggJiYgKGxhc3REaXJlY3Rpb24gPT09IDEgPyBtb3VzZU9uQXhpcyA+IHRhcmdldFMxICsgdGFyZ2V0TGVuZ3RoICogaW52ZXJ0ZWRTd2FwVGhyZXNob2xkIC8gMiA6IG1vdXNlT25BeGlzIDwgdGFyZ2V0UzIgLSB0YXJnZXRMZW5ndGggKiBpbnZlcnRlZFN3YXBUaHJlc2hvbGQgLyAyKSkge1xuICAgICAgICAvLyBwYXN0IGZpcnN0IGludmVydCB0aHJlc2hvbGQsIGRvIG5vdCByZXN0cmljdCBpbnZlcnRlZCB0aHJlc2hvbGQgdG8gZHJhZ0VsIHNoYWRvd1xuICAgICAgICBwYXN0Rmlyc3RJbnZlcnRUaHJlc2ggPSB0cnVlO1xuICAgICAgfVxuXG4gICAgICBpZiAoIXBhc3RGaXJzdEludmVydFRocmVzaCkge1xuICAgICAgICAvLyBkcmFnRWwgc2hhZG93ICh0YXJnZXQgbW92ZSBkaXN0YW5jZSBzaGFkb3cpXG4gICAgICAgIGlmIChsYXN0RGlyZWN0aW9uID09PSAxID8gbW91c2VPbkF4aXMgPCB0YXJnZXRTMSArIHRhcmdldE1vdmVEaXN0YW5jZSAvLyBvdmVyIGRyYWdFbCBzaGFkb3dcbiAgICAgICAgOiBtb3VzZU9uQXhpcyA+IHRhcmdldFMyIC0gdGFyZ2V0TW92ZURpc3RhbmNlKSB7XG4gICAgICAgICAgcmV0dXJuIC1sYXN0RGlyZWN0aW9uO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBpbnZlcnQgPSB0cnVlO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICAvLyBSZWd1bGFyXG4gICAgICBpZiAobW91c2VPbkF4aXMgPiB0YXJnZXRTMSArIHRhcmdldExlbmd0aCAqICgxIC0gc3dhcFRocmVzaG9sZCkgLyAyICYmIG1vdXNlT25BeGlzIDwgdGFyZ2V0UzIgLSB0YXJnZXRMZW5ndGggKiAoMSAtIHN3YXBUaHJlc2hvbGQpIC8gMikge1xuICAgICAgICByZXR1cm4gX2dldEluc2VydERpcmVjdGlvbih0YXJnZXQpO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIGludmVydCA9IGludmVydCB8fCBpbnZlcnRTd2FwO1xuXG4gIGlmIChpbnZlcnQpIHtcbiAgICAvLyBJbnZlcnQgb2YgcmVndWxhclxuICAgIGlmIChtb3VzZU9uQXhpcyA8IHRhcmdldFMxICsgdGFyZ2V0TGVuZ3RoICogaW52ZXJ0ZWRTd2FwVGhyZXNob2xkIC8gMiB8fCBtb3VzZU9uQXhpcyA+IHRhcmdldFMyIC0gdGFyZ2V0TGVuZ3RoICogaW52ZXJ0ZWRTd2FwVGhyZXNob2xkIC8gMikge1xuICAgICAgcmV0dXJuIG1vdXNlT25BeGlzID4gdGFyZ2V0UzEgKyB0YXJnZXRMZW5ndGggLyAyID8gMSA6IC0xO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiAwO1xufVxuLyoqXHJcbiAqIEdldHMgdGhlIGRpcmVjdGlvbiBkcmFnRWwgbXVzdCBiZSBzd2FwcGVkIHJlbGF0aXZlIHRvIHRhcmdldCBpbiBvcmRlciB0byBtYWtlIGl0XHJcbiAqIHNlZW0gdGhhdCBkcmFnRWwgaGFzIGJlZW4gXCJpbnNlcnRlZFwiIGludG8gdGhhdCBlbGVtZW50J3MgcG9zaXRpb25cclxuICogQHBhcmFtICB7SFRNTEVsZW1lbnR9IHRhcmdldCAgICAgICBUaGUgdGFyZ2V0IHdob3NlIHBvc2l0aW9uIGRyYWdFbCBpcyBiZWluZyBpbnNlcnRlZCBhdFxyXG4gKiBAcmV0dXJuIHtOdW1iZXJ9ICAgICAgICAgICAgICAgICAgIERpcmVjdGlvbiBkcmFnRWwgbXVzdCBiZSBzd2FwcGVkXHJcbiAqL1xuXG5cbmZ1bmN0aW9uIF9nZXRJbnNlcnREaXJlY3Rpb24odGFyZ2V0KSB7XG4gIGlmIChpbmRleChkcmFnRWwpIDwgaW5kZXgodGFyZ2V0KSkge1xuICAgIHJldHVybiAxO1xuICB9IGVsc2Uge1xuICAgIHJldHVybiAtMTtcbiAgfVxufVxuLyoqXHJcbiAqIEdlbmVyYXRlIGlkXHJcbiAqIEBwYXJhbSAgIHtIVE1MRWxlbWVudH0gZWxcclxuICogQHJldHVybnMge1N0cmluZ31cclxuICogQHByaXZhdGVcclxuICovXG5cblxuZnVuY3Rpb24gX2dlbmVyYXRlSWQoZWwpIHtcbiAgdmFyIHN0ciA9IGVsLnRhZ05hbWUgKyBlbC5jbGFzc05hbWUgKyBlbC5zcmMgKyBlbC5ocmVmICsgZWwudGV4dENvbnRlbnQsXG4gICAgICBpID0gc3RyLmxlbmd0aCxcbiAgICAgIHN1bSA9IDA7XG5cbiAgd2hpbGUgKGktLSkge1xuICAgIHN1bSArPSBzdHIuY2hhckNvZGVBdChpKTtcbiAgfVxuXG4gIHJldHVybiBzdW0udG9TdHJpbmcoMzYpO1xufVxuXG5mdW5jdGlvbiBfc2F2ZUlucHV0Q2hlY2tlZFN0YXRlKHJvb3QpIHtcbiAgc2F2ZWRJbnB1dENoZWNrZWQubGVuZ3RoID0gMDtcbiAgdmFyIGlucHV0cyA9IHJvb3QuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ2lucHV0Jyk7XG4gIHZhciBpZHggPSBpbnB1dHMubGVuZ3RoO1xuXG4gIHdoaWxlIChpZHgtLSkge1xuICAgIHZhciBlbCA9IGlucHV0c1tpZHhdO1xuICAgIGVsLmNoZWNrZWQgJiYgc2F2ZWRJbnB1dENoZWNrZWQucHVzaChlbCk7XG4gIH1cbn1cblxuZnVuY3Rpb24gX25leHRUaWNrKGZuKSB7XG4gIHJldHVybiBzZXRUaW1lb3V0KGZuLCAwKTtcbn1cblxuZnVuY3Rpb24gX2NhbmNlbE5leHRUaWNrKGlkKSB7XG4gIHJldHVybiBjbGVhclRpbWVvdXQoaWQpO1xufSAvLyBGaXhlZCAjOTczOlxuXG5cbmlmIChkb2N1bWVudEV4aXN0cykge1xuICBvbihkb2N1bWVudCwgJ3RvdWNobW92ZScsIGZ1bmN0aW9uIChldnQpIHtcbiAgICBpZiAoKFNvcnRhYmxlLmFjdGl2ZSB8fCBhd2FpdGluZ0RyYWdTdGFydGVkKSAmJiBldnQuY2FuY2VsYWJsZSkge1xuICAgICAgZXZ0LnByZXZlbnREZWZhdWx0KCk7XG4gICAgfVxuICB9KTtcbn0gLy8gRXhwb3J0IHV0aWxzXG5cblxuU29ydGFibGUudXRpbHMgPSB7XG4gIG9uOiBvbixcbiAgb2ZmOiBvZmYsXG4gIGNzczogY3NzLFxuICBmaW5kOiBmaW5kLFxuICBpczogZnVuY3Rpb24gaXMoZWwsIHNlbGVjdG9yKSB7XG4gICAgcmV0dXJuICEhY2xvc2VzdChlbCwgc2VsZWN0b3IsIGVsLCBmYWxzZSk7XG4gIH0sXG4gIGV4dGVuZDogZXh0ZW5kLFxuICB0aHJvdHRsZTogdGhyb3R0bGUsXG4gIGNsb3Nlc3Q6IGNsb3Nlc3QsXG4gIHRvZ2dsZUNsYXNzOiB0b2dnbGVDbGFzcyxcbiAgY2xvbmU6IGNsb25lLFxuICBpbmRleDogaW5kZXgsXG4gIG5leHRUaWNrOiBfbmV4dFRpY2ssXG4gIGNhbmNlbE5leHRUaWNrOiBfY2FuY2VsTmV4dFRpY2ssXG4gIGRldGVjdERpcmVjdGlvbjogX2RldGVjdERpcmVjdGlvbixcbiAgZ2V0Q2hpbGQ6IGdldENoaWxkXG59O1xuLyoqXHJcbiAqIEdldCB0aGUgU29ydGFibGUgaW5zdGFuY2Ugb2YgYW4gZWxlbWVudFxyXG4gKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gZWxlbWVudCBUaGUgZWxlbWVudFxyXG4gKiBAcmV0dXJuIHtTb3J0YWJsZXx1bmRlZmluZWR9ICAgICAgICAgVGhlIGluc3RhbmNlIG9mIFNvcnRhYmxlXHJcbiAqL1xuXG5Tb3J0YWJsZS5nZXQgPSBmdW5jdGlvbiAoZWxlbWVudCkge1xuICByZXR1cm4gZWxlbWVudFtleHBhbmRvXTtcbn07XG4vKipcclxuICogTW91bnQgYSBwbHVnaW4gdG8gU29ydGFibGVcclxuICogQHBhcmFtICB7Li4uU29ydGFibGVQbHVnaW58U29ydGFibGVQbHVnaW5bXX0gcGx1Z2lucyAgICAgICBQbHVnaW5zIGJlaW5nIG1vdW50ZWRcclxuICovXG5cblxuU29ydGFibGUubW91bnQgPSBmdW5jdGlvbiAoKSB7XG4gIGZvciAodmFyIF9sZW4gPSBhcmd1bWVudHMubGVuZ3RoLCBwbHVnaW5zID0gbmV3IEFycmF5KF9sZW4pLCBfa2V5ID0gMDsgX2tleSA8IF9sZW47IF9rZXkrKykge1xuICAgIHBsdWdpbnNbX2tleV0gPSBhcmd1bWVudHNbX2tleV07XG4gIH1cblxuICBpZiAocGx1Z2luc1swXS5jb25zdHJ1Y3RvciA9PT0gQXJyYXkpIHBsdWdpbnMgPSBwbHVnaW5zWzBdO1xuICBwbHVnaW5zLmZvckVhY2goZnVuY3Rpb24gKHBsdWdpbikge1xuICAgIGlmICghcGx1Z2luLnByb3RvdHlwZSB8fCAhcGx1Z2luLnByb3RvdHlwZS5jb25zdHJ1Y3Rvcikge1xuICAgICAgdGhyb3cgXCJTb3J0YWJsZTogTW91bnRlZCBwbHVnaW4gbXVzdCBiZSBhIGNvbnN0cnVjdG9yIGZ1bmN0aW9uLCBub3QgXCIuY29uY2F0KHt9LnRvU3RyaW5nLmNhbGwocGx1Z2luKSk7XG4gICAgfVxuXG4gICAgaWYgKHBsdWdpbi51dGlscykgU29ydGFibGUudXRpbHMgPSBfb2JqZWN0U3ByZWFkMihfb2JqZWN0U3ByZWFkMih7fSwgU29ydGFibGUudXRpbHMpLCBwbHVnaW4udXRpbHMpO1xuICAgIFBsdWdpbk1hbmFnZXIubW91bnQocGx1Z2luKTtcbiAgfSk7XG59O1xuLyoqXHJcbiAqIENyZWF0ZSBzb3J0YWJsZSBpbnN0YW5jZVxyXG4gKiBAcGFyYW0ge0hUTUxFbGVtZW50fSAgZWxcclxuICogQHBhcmFtIHtPYmplY3R9ICAgICAgW29wdGlvbnNdXHJcbiAqL1xuXG5cblNvcnRhYmxlLmNyZWF0ZSA9IGZ1bmN0aW9uIChlbCwgb3B0aW9ucykge1xuICByZXR1cm4gbmV3IFNvcnRhYmxlKGVsLCBvcHRpb25zKTtcbn07IC8vIEV4cG9ydFxuXG5cblNvcnRhYmxlLnZlcnNpb24gPSB2ZXJzaW9uO1xuXG52YXIgYXV0b1Njcm9sbHMgPSBbXSxcbiAgICBzY3JvbGxFbCxcbiAgICBzY3JvbGxSb290RWwsXG4gICAgc2Nyb2xsaW5nID0gZmFsc2UsXG4gICAgbGFzdEF1dG9TY3JvbGxYLFxuICAgIGxhc3RBdXRvU2Nyb2xsWSxcbiAgICB0b3VjaEV2dCQxLFxuICAgIHBvaW50ZXJFbGVtQ2hhbmdlZEludGVydmFsO1xuXG5mdW5jdGlvbiBBdXRvU2Nyb2xsUGx1Z2luKCkge1xuICBmdW5jdGlvbiBBdXRvU2Nyb2xsKCkge1xuICAgIHRoaXMuZGVmYXVsdHMgPSB7XG4gICAgICBzY3JvbGw6IHRydWUsXG4gICAgICBmb3JjZUF1dG9TY3JvbGxGYWxsYmFjazogZmFsc2UsXG4gICAgICBzY3JvbGxTZW5zaXRpdml0eTogMzAsXG4gICAgICBzY3JvbGxTcGVlZDogMTAsXG4gICAgICBidWJibGVTY3JvbGw6IHRydWVcbiAgICB9OyAvLyBCaW5kIGFsbCBwcml2YXRlIG1ldGhvZHNcblxuICAgIGZvciAodmFyIGZuIGluIHRoaXMpIHtcbiAgICAgIGlmIChmbi5jaGFyQXQoMCkgPT09ICdfJyAmJiB0eXBlb2YgdGhpc1tmbl0gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpc1tmbl0gPSB0aGlzW2ZuXS5iaW5kKHRoaXMpO1xuICAgICAgfVxuICAgIH1cbiAgfVxuXG4gIEF1dG9TY3JvbGwucHJvdG90eXBlID0ge1xuICAgIGRyYWdTdGFydGVkOiBmdW5jdGlvbiBkcmFnU3RhcnRlZChfcmVmKSB7XG4gICAgICB2YXIgb3JpZ2luYWxFdmVudCA9IF9yZWYub3JpZ2luYWxFdmVudDtcblxuICAgICAgaWYgKHRoaXMuc29ydGFibGUubmF0aXZlRHJhZ2dhYmxlKSB7XG4gICAgICAgIG9uKGRvY3VtZW50LCAnZHJhZ292ZXInLCB0aGlzLl9oYW5kbGVBdXRvU2Nyb2xsKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGlmICh0aGlzLm9wdGlvbnMuc3VwcG9ydFBvaW50ZXIpIHtcbiAgICAgICAgICBvbihkb2N1bWVudCwgJ3BvaW50ZXJtb3ZlJywgdGhpcy5faGFuZGxlRmFsbGJhY2tBdXRvU2Nyb2xsKTtcbiAgICAgICAgfSBlbHNlIGlmIChvcmlnaW5hbEV2ZW50LnRvdWNoZXMpIHtcbiAgICAgICAgICBvbihkb2N1bWVudCwgJ3RvdWNobW92ZScsIHRoaXMuX2hhbmRsZUZhbGxiYWNrQXV0b1Njcm9sbCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgb24oZG9jdW1lbnQsICdtb3VzZW1vdmUnLCB0aGlzLl9oYW5kbGVGYWxsYmFja0F1dG9TY3JvbGwpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSxcbiAgICBkcmFnT3ZlckNvbXBsZXRlZDogZnVuY3Rpb24gZHJhZ092ZXJDb21wbGV0ZWQoX3JlZjIpIHtcbiAgICAgIHZhciBvcmlnaW5hbEV2ZW50ID0gX3JlZjIub3JpZ2luYWxFdmVudDtcblxuICAgICAgLy8gRm9yIHdoZW4gYnViYmxpbmcgaXMgY2FuY2VsZWQgYW5kIHVzaW5nIGZhbGxiYWNrIChmYWxsYmFjayAndG91Y2htb3ZlJyBhbHdheXMgcmVhY2hlZClcbiAgICAgIGlmICghdGhpcy5vcHRpb25zLmRyYWdPdmVyQnViYmxlICYmICFvcmlnaW5hbEV2ZW50LnJvb3RFbCkge1xuICAgICAgICB0aGlzLl9oYW5kbGVBdXRvU2Nyb2xsKG9yaWdpbmFsRXZlbnQpO1xuICAgICAgfVxuICAgIH0sXG4gICAgZHJvcDogZnVuY3Rpb24gZHJvcCgpIHtcbiAgICAgIGlmICh0aGlzLnNvcnRhYmxlLm5hdGl2ZURyYWdnYWJsZSkge1xuICAgICAgICBvZmYoZG9jdW1lbnQsICdkcmFnb3ZlcicsIHRoaXMuX2hhbmRsZUF1dG9TY3JvbGwpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgb2ZmKGRvY3VtZW50LCAncG9pbnRlcm1vdmUnLCB0aGlzLl9oYW5kbGVGYWxsYmFja0F1dG9TY3JvbGwpO1xuICAgICAgICBvZmYoZG9jdW1lbnQsICd0b3VjaG1vdmUnLCB0aGlzLl9oYW5kbGVGYWxsYmFja0F1dG9TY3JvbGwpO1xuICAgICAgICBvZmYoZG9jdW1lbnQsICdtb3VzZW1vdmUnLCB0aGlzLl9oYW5kbGVGYWxsYmFja0F1dG9TY3JvbGwpO1xuICAgICAgfVxuXG4gICAgICBjbGVhclBvaW50ZXJFbGVtQ2hhbmdlZEludGVydmFsKCk7XG4gICAgICBjbGVhckF1dG9TY3JvbGxzKCk7XG4gICAgICBjYW5jZWxUaHJvdHRsZSgpO1xuICAgIH0sXG4gICAgbnVsbGluZzogZnVuY3Rpb24gbnVsbGluZygpIHtcbiAgICAgIHRvdWNoRXZ0JDEgPSBzY3JvbGxSb290RWwgPSBzY3JvbGxFbCA9IHNjcm9sbGluZyA9IHBvaW50ZXJFbGVtQ2hhbmdlZEludGVydmFsID0gbGFzdEF1dG9TY3JvbGxYID0gbGFzdEF1dG9TY3JvbGxZID0gbnVsbDtcbiAgICAgIGF1dG9TY3JvbGxzLmxlbmd0aCA9IDA7XG4gICAgfSxcbiAgICBfaGFuZGxlRmFsbGJhY2tBdXRvU2Nyb2xsOiBmdW5jdGlvbiBfaGFuZGxlRmFsbGJhY2tBdXRvU2Nyb2xsKGV2dCkge1xuICAgICAgdGhpcy5faGFuZGxlQXV0b1Njcm9sbChldnQsIHRydWUpO1xuICAgIH0sXG4gICAgX2hhbmRsZUF1dG9TY3JvbGw6IGZ1bmN0aW9uIF9oYW5kbGVBdXRvU2Nyb2xsKGV2dCwgZmFsbGJhY2spIHtcbiAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG5cbiAgICAgIHZhciB4ID0gKGV2dC50b3VjaGVzID8gZXZ0LnRvdWNoZXNbMF0gOiBldnQpLmNsaWVudFgsXG4gICAgICAgICAgeSA9IChldnQudG91Y2hlcyA/IGV2dC50b3VjaGVzWzBdIDogZXZ0KS5jbGllbnRZLFxuICAgICAgICAgIGVsZW0gPSBkb2N1bWVudC5lbGVtZW50RnJvbVBvaW50KHgsIHkpO1xuICAgICAgdG91Y2hFdnQkMSA9IGV2dDsgLy8gSUUgZG9lcyBub3Qgc2VlbSB0byBoYXZlIG5hdGl2ZSBhdXRvc2Nyb2xsLFxuICAgICAgLy8gRWRnZSdzIGF1dG9zY3JvbGwgc2VlbXMgdG9vIGNvbmRpdGlvbmFsLFxuICAgICAgLy8gTUFDT1MgU2FmYXJpIGRvZXMgbm90IGhhdmUgYXV0b3Njcm9sbCxcbiAgICAgIC8vIEZpcmVmb3ggYW5kIENocm9tZSBhcmUgZ29vZFxuXG4gICAgICBpZiAoZmFsbGJhY2sgfHwgdGhpcy5vcHRpb25zLmZvcmNlQXV0b1Njcm9sbEZhbGxiYWNrIHx8IEVkZ2UgfHwgSUUxMU9yTGVzcyB8fCBTYWZhcmkpIHtcbiAgICAgICAgYXV0b1Njcm9sbChldnQsIHRoaXMub3B0aW9ucywgZWxlbSwgZmFsbGJhY2spOyAvLyBMaXN0ZW5lciBmb3IgcG9pbnRlciBlbGVtZW50IGNoYW5nZVxuXG4gICAgICAgIHZhciBvZ0VsZW1TY3JvbGxlciA9IGdldFBhcmVudEF1dG9TY3JvbGxFbGVtZW50KGVsZW0sIHRydWUpO1xuXG4gICAgICAgIGlmIChzY3JvbGxpbmcgJiYgKCFwb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbCB8fCB4ICE9PSBsYXN0QXV0b1Njcm9sbFggfHwgeSAhPT0gbGFzdEF1dG9TY3JvbGxZKSkge1xuICAgICAgICAgIHBvaW50ZXJFbGVtQ2hhbmdlZEludGVydmFsICYmIGNsZWFyUG9pbnRlckVsZW1DaGFuZ2VkSW50ZXJ2YWwoKTsgLy8gRGV0ZWN0IGZvciBwb2ludGVyIGVsZW0gY2hhbmdlLCBlbXVsYXRpbmcgbmF0aXZlIERuRCBiZWhhdmlvdXJcblxuICAgICAgICAgIHBvaW50ZXJFbGVtQ2hhbmdlZEludGVydmFsID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyIG5ld0VsZW0gPSBnZXRQYXJlbnRBdXRvU2Nyb2xsRWxlbWVudChkb2N1bWVudC5lbGVtZW50RnJvbVBvaW50KHgsIHkpLCB0cnVlKTtcblxuICAgICAgICAgICAgaWYgKG5ld0VsZW0gIT09IG9nRWxlbVNjcm9sbGVyKSB7XG4gICAgICAgICAgICAgIG9nRWxlbVNjcm9sbGVyID0gbmV3RWxlbTtcbiAgICAgICAgICAgICAgY2xlYXJBdXRvU2Nyb2xscygpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBhdXRvU2Nyb2xsKGV2dCwgX3RoaXMub3B0aW9ucywgbmV3RWxlbSwgZmFsbGJhY2spO1xuICAgICAgICAgIH0sIDEwKTtcbiAgICAgICAgICBsYXN0QXV0b1Njcm9sbFggPSB4O1xuICAgICAgICAgIGxhc3RBdXRvU2Nyb2xsWSA9IHk7XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIC8vIGlmIERuRCBpcyBlbmFibGVkIChhbmQgYnJvd3NlciBoYXMgZ29vZCBhdXRvc2Nyb2xsaW5nKSwgZmlyc3QgYXV0b3Njcm9sbCB3aWxsIGFscmVhZHkgc2Nyb2xsLCBzbyBnZXQgcGFyZW50IGF1dG9zY3JvbGwgb2YgZmlyc3QgYXV0b3Njcm9sbFxuICAgICAgICBpZiAoIXRoaXMub3B0aW9ucy5idWJibGVTY3JvbGwgfHwgZ2V0UGFyZW50QXV0b1Njcm9sbEVsZW1lbnQoZWxlbSwgdHJ1ZSkgPT09IGdldFdpbmRvd1Njcm9sbGluZ0VsZW1lbnQoKSkge1xuICAgICAgICAgIGNsZWFyQXV0b1Njcm9sbHMoKTtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBhdXRvU2Nyb2xsKGV2dCwgdGhpcy5vcHRpb25zLCBnZXRQYXJlbnRBdXRvU2Nyb2xsRWxlbWVudChlbGVtLCBmYWxzZSksIGZhbHNlKTtcbiAgICAgIH1cbiAgICB9XG4gIH07XG4gIHJldHVybiBfZXh0ZW5kcyhBdXRvU2Nyb2xsLCB7XG4gICAgcGx1Z2luTmFtZTogJ3Njcm9sbCcsXG4gICAgaW5pdGlhbGl6ZUJ5RGVmYXVsdDogdHJ1ZVxuICB9KTtcbn1cblxuZnVuY3Rpb24gY2xlYXJBdXRvU2Nyb2xscygpIHtcbiAgYXV0b1Njcm9sbHMuZm9yRWFjaChmdW5jdGlvbiAoYXV0b1Njcm9sbCkge1xuICAgIGNsZWFySW50ZXJ2YWwoYXV0b1Njcm9sbC5waWQpO1xuICB9KTtcbiAgYXV0b1Njcm9sbHMgPSBbXTtcbn1cblxuZnVuY3Rpb24gY2xlYXJQb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbCgpIHtcbiAgY2xlYXJJbnRlcnZhbChwb2ludGVyRWxlbUNoYW5nZWRJbnRlcnZhbCk7XG59XG5cbnZhciBhdXRvU2Nyb2xsID0gdGhyb3R0bGUoZnVuY3Rpb24gKGV2dCwgb3B0aW9ucywgcm9vdEVsLCBpc0ZhbGxiYWNrKSB7XG4gIC8vIEJ1ZzogaHR0cHM6Ly9idWd6aWxsYS5tb3ppbGxhLm9yZy9zaG93X2J1Zy5jZ2k/aWQ9NTA1NTIxXG4gIGlmICghb3B0aW9ucy5zY3JvbGwpIHJldHVybjtcbiAgdmFyIHggPSAoZXZ0LnRvdWNoZXMgPyBldnQudG91Y2hlc1swXSA6IGV2dCkuY2xpZW50WCxcbiAgICAgIHkgPSAoZXZ0LnRvdWNoZXMgPyBldnQudG91Y2hlc1swXSA6IGV2dCkuY2xpZW50WSxcbiAgICAgIHNlbnMgPSBvcHRpb25zLnNjcm9sbFNlbnNpdGl2aXR5LFxuICAgICAgc3BlZWQgPSBvcHRpb25zLnNjcm9sbFNwZWVkLFxuICAgICAgd2luU2Nyb2xsZXIgPSBnZXRXaW5kb3dTY3JvbGxpbmdFbGVtZW50KCk7XG4gIHZhciBzY3JvbGxUaGlzSW5zdGFuY2UgPSBmYWxzZSxcbiAgICAgIHNjcm9sbEN1c3RvbUZuOyAvLyBOZXcgc2Nyb2xsIHJvb3QsIHNldCBzY3JvbGxFbFxuXG4gIGlmIChzY3JvbGxSb290RWwgIT09IHJvb3RFbCkge1xuICAgIHNjcm9sbFJvb3RFbCA9IHJvb3RFbDtcbiAgICBjbGVhckF1dG9TY3JvbGxzKCk7XG4gICAgc2Nyb2xsRWwgPSBvcHRpb25zLnNjcm9sbDtcbiAgICBzY3JvbGxDdXN0b21GbiA9IG9wdGlvbnMuc2Nyb2xsRm47XG5cbiAgICBpZiAoc2Nyb2xsRWwgPT09IHRydWUpIHtcbiAgICAgIHNjcm9sbEVsID0gZ2V0UGFyZW50QXV0b1Njcm9sbEVsZW1lbnQocm9vdEVsLCB0cnVlKTtcbiAgICB9XG4gIH1cblxuICB2YXIgbGF5ZXJzT3V0ID0gMDtcbiAgdmFyIGN1cnJlbnRQYXJlbnQgPSBzY3JvbGxFbDtcblxuICBkbyB7XG4gICAgdmFyIGVsID0gY3VycmVudFBhcmVudCxcbiAgICAgICAgcmVjdCA9IGdldFJlY3QoZWwpLFxuICAgICAgICB0b3AgPSByZWN0LnRvcCxcbiAgICAgICAgYm90dG9tID0gcmVjdC5ib3R0b20sXG4gICAgICAgIGxlZnQgPSByZWN0LmxlZnQsXG4gICAgICAgIHJpZ2h0ID0gcmVjdC5yaWdodCxcbiAgICAgICAgd2lkdGggPSByZWN0LndpZHRoLFxuICAgICAgICBoZWlnaHQgPSByZWN0LmhlaWdodCxcbiAgICAgICAgY2FuU2Nyb2xsWCA9IHZvaWQgMCxcbiAgICAgICAgY2FuU2Nyb2xsWSA9IHZvaWQgMCxcbiAgICAgICAgc2Nyb2xsV2lkdGggPSBlbC5zY3JvbGxXaWR0aCxcbiAgICAgICAgc2Nyb2xsSGVpZ2h0ID0gZWwuc2Nyb2xsSGVpZ2h0LFxuICAgICAgICBlbENTUyA9IGNzcyhlbCksXG4gICAgICAgIHNjcm9sbFBvc1ggPSBlbC5zY3JvbGxMZWZ0LFxuICAgICAgICBzY3JvbGxQb3NZID0gZWwuc2Nyb2xsVG9wO1xuXG4gICAgaWYgKGVsID09PSB3aW5TY3JvbGxlcikge1xuICAgICAgY2FuU2Nyb2xsWCA9IHdpZHRoIDwgc2Nyb2xsV2lkdGggJiYgKGVsQ1NTLm92ZXJmbG93WCA9PT0gJ2F1dG8nIHx8IGVsQ1NTLm92ZXJmbG93WCA9PT0gJ3Njcm9sbCcgfHwgZWxDU1Mub3ZlcmZsb3dYID09PSAndmlzaWJsZScpO1xuICAgICAgY2FuU2Nyb2xsWSA9IGhlaWdodCA8IHNjcm9sbEhlaWdodCAmJiAoZWxDU1Mub3ZlcmZsb3dZID09PSAnYXV0bycgfHwgZWxDU1Mub3ZlcmZsb3dZID09PSAnc2Nyb2xsJyB8fCBlbENTUy5vdmVyZmxvd1kgPT09ICd2aXNpYmxlJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIGNhblNjcm9sbFggPSB3aWR0aCA8IHNjcm9sbFdpZHRoICYmIChlbENTUy5vdmVyZmxvd1ggPT09ICdhdXRvJyB8fCBlbENTUy5vdmVyZmxvd1ggPT09ICdzY3JvbGwnKTtcbiAgICAgIGNhblNjcm9sbFkgPSBoZWlnaHQgPCBzY3JvbGxIZWlnaHQgJiYgKGVsQ1NTLm92ZXJmbG93WSA9PT0gJ2F1dG8nIHx8IGVsQ1NTLm92ZXJmbG93WSA9PT0gJ3Njcm9sbCcpO1xuICAgIH1cblxuICAgIHZhciB2eCA9IGNhblNjcm9sbFggJiYgKE1hdGguYWJzKHJpZ2h0IC0geCkgPD0gc2VucyAmJiBzY3JvbGxQb3NYICsgd2lkdGggPCBzY3JvbGxXaWR0aCkgLSAoTWF0aC5hYnMobGVmdCAtIHgpIDw9IHNlbnMgJiYgISFzY3JvbGxQb3NYKTtcbiAgICB2YXIgdnkgPSBjYW5TY3JvbGxZICYmIChNYXRoLmFicyhib3R0b20gLSB5KSA8PSBzZW5zICYmIHNjcm9sbFBvc1kgKyBoZWlnaHQgPCBzY3JvbGxIZWlnaHQpIC0gKE1hdGguYWJzKHRvcCAtIHkpIDw9IHNlbnMgJiYgISFzY3JvbGxQb3NZKTtcblxuICAgIGlmICghYXV0b1Njcm9sbHNbbGF5ZXJzT3V0XSkge1xuICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPD0gbGF5ZXJzT3V0OyBpKyspIHtcbiAgICAgICAgaWYgKCFhdXRvU2Nyb2xsc1tpXSkge1xuICAgICAgICAgIGF1dG9TY3JvbGxzW2ldID0ge307XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG5cbiAgICBpZiAoYXV0b1Njcm9sbHNbbGF5ZXJzT3V0XS52eCAhPSB2eCB8fCBhdXRvU2Nyb2xsc1tsYXllcnNPdXRdLnZ5ICE9IHZ5IHx8IGF1dG9TY3JvbGxzW2xheWVyc091dF0uZWwgIT09IGVsKSB7XG4gICAgICBhdXRvU2Nyb2xsc1tsYXllcnNPdXRdLmVsID0gZWw7XG4gICAgICBhdXRvU2Nyb2xsc1tsYXllcnNPdXRdLnZ4ID0gdng7XG4gICAgICBhdXRvU2Nyb2xsc1tsYXllcnNPdXRdLnZ5ID0gdnk7XG4gICAgICBjbGVhckludGVydmFsKGF1dG9TY3JvbGxzW2xheWVyc091dF0ucGlkKTtcblxuICAgICAgaWYgKHZ4ICE9IDAgfHwgdnkgIT0gMCkge1xuICAgICAgICBzY3JvbGxUaGlzSW5zdGFuY2UgPSB0cnVlO1xuICAgICAgICAvKiBqc2hpbnQgbG9vcGZ1bmM6dHJ1ZSAqL1xuXG4gICAgICAgIGF1dG9TY3JvbGxzW2xheWVyc091dF0ucGlkID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24gKCkge1xuICAgICAgICAgIC8vIGVtdWxhdGUgZHJhZyBvdmVyIGR1cmluZyBhdXRvc2Nyb2xsIChmYWxsYmFjayksIGVtdWxhdGluZyBuYXRpdmUgRG5EIGJlaGF2aW91clxuICAgICAgICAgIGlmIChpc0ZhbGxiYWNrICYmIHRoaXMubGF5ZXIgPT09IDApIHtcbiAgICAgICAgICAgIFNvcnRhYmxlLmFjdGl2ZS5fb25Ub3VjaE1vdmUodG91Y2hFdnQkMSk7IC8vIFRvIG1vdmUgZ2hvc3QgaWYgaXQgaXMgcG9zaXRpb25lZCBhYnNvbHV0ZWx5XG5cbiAgICAgICAgICB9XG5cbiAgICAgICAgICB2YXIgc2Nyb2xsT2Zmc2V0WSA9IGF1dG9TY3JvbGxzW3RoaXMubGF5ZXJdLnZ5ID8gYXV0b1Njcm9sbHNbdGhpcy5sYXllcl0udnkgKiBzcGVlZCA6IDA7XG4gICAgICAgICAgdmFyIHNjcm9sbE9mZnNldFggPSBhdXRvU2Nyb2xsc1t0aGlzLmxheWVyXS52eCA/IGF1dG9TY3JvbGxzW3RoaXMubGF5ZXJdLnZ4ICogc3BlZWQgOiAwO1xuXG4gICAgICAgICAgaWYgKHR5cGVvZiBzY3JvbGxDdXN0b21GbiA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgaWYgKHNjcm9sbEN1c3RvbUZuLmNhbGwoU29ydGFibGUuZHJhZ2dlZC5wYXJlbnROb2RlW2V4cGFuZG9dLCBzY3JvbGxPZmZzZXRYLCBzY3JvbGxPZmZzZXRZLCBldnQsIHRvdWNoRXZ0JDEsIGF1dG9TY3JvbGxzW3RoaXMubGF5ZXJdLmVsKSAhPT0gJ2NvbnRpbnVlJykge1xuICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgc2Nyb2xsQnkoYXV0b1Njcm9sbHNbdGhpcy5sYXllcl0uZWwsIHNjcm9sbE9mZnNldFgsIHNjcm9sbE9mZnNldFkpO1xuICAgICAgICB9LmJpbmQoe1xuICAgICAgICAgIGxheWVyOiBsYXllcnNPdXRcbiAgICAgICAgfSksIDI0KTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBsYXllcnNPdXQrKztcbiAgfSB3aGlsZSAob3B0aW9ucy5idWJibGVTY3JvbGwgJiYgY3VycmVudFBhcmVudCAhPT0gd2luU2Nyb2xsZXIgJiYgKGN1cnJlbnRQYXJlbnQgPSBnZXRQYXJlbnRBdXRvU2Nyb2xsRWxlbWVudChjdXJyZW50UGFyZW50LCBmYWxzZSkpKTtcblxuICBzY3JvbGxpbmcgPSBzY3JvbGxUaGlzSW5zdGFuY2U7IC8vIGluIGNhc2UgYW5vdGhlciBmdW5jdGlvbiBjYXRjaGVzIHNjcm9sbGluZyBhcyBmYWxzZSBpbiBiZXR3ZWVuIHdoZW4gaXQgaXMgbm90XG59LCAzMCk7XG5cbnZhciBkcm9wID0gZnVuY3Rpb24gZHJvcChfcmVmKSB7XG4gIHZhciBvcmlnaW5hbEV2ZW50ID0gX3JlZi5vcmlnaW5hbEV2ZW50LFxuICAgICAgcHV0U29ydGFibGUgPSBfcmVmLnB1dFNvcnRhYmxlLFxuICAgICAgZHJhZ0VsID0gX3JlZi5kcmFnRWwsXG4gICAgICBhY3RpdmVTb3J0YWJsZSA9IF9yZWYuYWN0aXZlU29ydGFibGUsXG4gICAgICBkaXNwYXRjaFNvcnRhYmxlRXZlbnQgPSBfcmVmLmRpc3BhdGNoU29ydGFibGVFdmVudCxcbiAgICAgIGhpZGVHaG9zdEZvclRhcmdldCA9IF9yZWYuaGlkZUdob3N0Rm9yVGFyZ2V0LFxuICAgICAgdW5oaWRlR2hvc3RGb3JUYXJnZXQgPSBfcmVmLnVuaGlkZUdob3N0Rm9yVGFyZ2V0O1xuICBpZiAoIW9yaWdpbmFsRXZlbnQpIHJldHVybjtcbiAgdmFyIHRvU29ydGFibGUgPSBwdXRTb3J0YWJsZSB8fCBhY3RpdmVTb3J0YWJsZTtcbiAgaGlkZUdob3N0Rm9yVGFyZ2V0KCk7XG4gIHZhciB0b3VjaCA9IG9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXMgJiYgb3JpZ2luYWxFdmVudC5jaGFuZ2VkVG91Y2hlcy5sZW5ndGggPyBvcmlnaW5hbEV2ZW50LmNoYW5nZWRUb3VjaGVzWzBdIDogb3JpZ2luYWxFdmVudDtcbiAgdmFyIHRhcmdldCA9IGRvY3VtZW50LmVsZW1lbnRGcm9tUG9pbnQodG91Y2guY2xpZW50WCwgdG91Y2guY2xpZW50WSk7XG4gIHVuaGlkZUdob3N0Rm9yVGFyZ2V0KCk7XG5cbiAgaWYgKHRvU29ydGFibGUgJiYgIXRvU29ydGFibGUuZWwuY29udGFpbnModGFyZ2V0KSkge1xuICAgIGRpc3BhdGNoU29ydGFibGVFdmVudCgnc3BpbGwnKTtcbiAgICB0aGlzLm9uU3BpbGwoe1xuICAgICAgZHJhZ0VsOiBkcmFnRWwsXG4gICAgICBwdXRTb3J0YWJsZTogcHV0U29ydGFibGVcbiAgICB9KTtcbiAgfVxufTtcblxuZnVuY3Rpb24gUmV2ZXJ0KCkge31cblxuUmV2ZXJ0LnByb3RvdHlwZSA9IHtcbiAgc3RhcnRJbmRleDogbnVsbCxcbiAgZHJhZ1N0YXJ0OiBmdW5jdGlvbiBkcmFnU3RhcnQoX3JlZjIpIHtcbiAgICB2YXIgb2xkRHJhZ2dhYmxlSW5kZXggPSBfcmVmMi5vbGREcmFnZ2FibGVJbmRleDtcbiAgICB0aGlzLnN0YXJ0SW5kZXggPSBvbGREcmFnZ2FibGVJbmRleDtcbiAgfSxcbiAgb25TcGlsbDogZnVuY3Rpb24gb25TcGlsbChfcmVmMykge1xuICAgIHZhciBkcmFnRWwgPSBfcmVmMy5kcmFnRWwsXG4gICAgICAgIHB1dFNvcnRhYmxlID0gX3JlZjMucHV0U29ydGFibGU7XG4gICAgdGhpcy5zb3J0YWJsZS5jYXB0dXJlQW5pbWF0aW9uU3RhdGUoKTtcblxuICAgIGlmIChwdXRTb3J0YWJsZSkge1xuICAgICAgcHV0U29ydGFibGUuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG4gICAgfVxuXG4gICAgdmFyIG5leHRTaWJsaW5nID0gZ2V0Q2hpbGQodGhpcy5zb3J0YWJsZS5lbCwgdGhpcy5zdGFydEluZGV4LCB0aGlzLm9wdGlvbnMpO1xuXG4gICAgaWYgKG5leHRTaWJsaW5nKSB7XG4gICAgICB0aGlzLnNvcnRhYmxlLmVsLmluc2VydEJlZm9yZShkcmFnRWwsIG5leHRTaWJsaW5nKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5zb3J0YWJsZS5lbC5hcHBlbmRDaGlsZChkcmFnRWwpO1xuICAgIH1cblxuICAgIHRoaXMuc29ydGFibGUuYW5pbWF0ZUFsbCgpO1xuXG4gICAgaWYgKHB1dFNvcnRhYmxlKSB7XG4gICAgICBwdXRTb3J0YWJsZS5hbmltYXRlQWxsKCk7XG4gICAgfVxuICB9LFxuICBkcm9wOiBkcm9wXG59O1xuXG5fZXh0ZW5kcyhSZXZlcnQsIHtcbiAgcGx1Z2luTmFtZTogJ3JldmVydE9uU3BpbGwnXG59KTtcblxuZnVuY3Rpb24gUmVtb3ZlKCkge31cblxuUmVtb3ZlLnByb3RvdHlwZSA9IHtcbiAgb25TcGlsbDogZnVuY3Rpb24gb25TcGlsbChfcmVmNCkge1xuICAgIHZhciBkcmFnRWwgPSBfcmVmNC5kcmFnRWwsXG4gICAgICAgIHB1dFNvcnRhYmxlID0gX3JlZjQucHV0U29ydGFibGU7XG4gICAgdmFyIHBhcmVudFNvcnRhYmxlID0gcHV0U29ydGFibGUgfHwgdGhpcy5zb3J0YWJsZTtcbiAgICBwYXJlbnRTb3J0YWJsZS5jYXB0dXJlQW5pbWF0aW9uU3RhdGUoKTtcbiAgICBkcmFnRWwucGFyZW50Tm9kZSAmJiBkcmFnRWwucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChkcmFnRWwpO1xuICAgIHBhcmVudFNvcnRhYmxlLmFuaW1hdGVBbGwoKTtcbiAgfSxcbiAgZHJvcDogZHJvcFxufTtcblxuX2V4dGVuZHMoUmVtb3ZlLCB7XG4gIHBsdWdpbk5hbWU6ICdyZW1vdmVPblNwaWxsJ1xufSk7XG5cbnZhciBsYXN0U3dhcEVsO1xuXG5mdW5jdGlvbiBTd2FwUGx1Z2luKCkge1xuICBmdW5jdGlvbiBTd2FwKCkge1xuICAgIHRoaXMuZGVmYXVsdHMgPSB7XG4gICAgICBzd2FwQ2xhc3M6ICdzb3J0YWJsZS1zd2FwLWhpZ2hsaWdodCdcbiAgICB9O1xuICB9XG5cbiAgU3dhcC5wcm90b3R5cGUgPSB7XG4gICAgZHJhZ1N0YXJ0OiBmdW5jdGlvbiBkcmFnU3RhcnQoX3JlZikge1xuICAgICAgdmFyIGRyYWdFbCA9IF9yZWYuZHJhZ0VsO1xuICAgICAgbGFzdFN3YXBFbCA9IGRyYWdFbDtcbiAgICB9LFxuICAgIGRyYWdPdmVyVmFsaWQ6IGZ1bmN0aW9uIGRyYWdPdmVyVmFsaWQoX3JlZjIpIHtcbiAgICAgIHZhciBjb21wbGV0ZWQgPSBfcmVmMi5jb21wbGV0ZWQsXG4gICAgICAgICAgdGFyZ2V0ID0gX3JlZjIudGFyZ2V0LFxuICAgICAgICAgIG9uTW92ZSA9IF9yZWYyLm9uTW92ZSxcbiAgICAgICAgICBhY3RpdmVTb3J0YWJsZSA9IF9yZWYyLmFjdGl2ZVNvcnRhYmxlLFxuICAgICAgICAgIGNoYW5nZWQgPSBfcmVmMi5jaGFuZ2VkLFxuICAgICAgICAgIGNhbmNlbCA9IF9yZWYyLmNhbmNlbDtcbiAgICAgIGlmICghYWN0aXZlU29ydGFibGUub3B0aW9ucy5zd2FwKSByZXR1cm47XG4gICAgICB2YXIgZWwgPSB0aGlzLnNvcnRhYmxlLmVsLFxuICAgICAgICAgIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7XG5cbiAgICAgIGlmICh0YXJnZXQgJiYgdGFyZ2V0ICE9PSBlbCkge1xuICAgICAgICB2YXIgcHJldlN3YXBFbCA9IGxhc3RTd2FwRWw7XG5cbiAgICAgICAgaWYgKG9uTW92ZSh0YXJnZXQpICE9PSBmYWxzZSkge1xuICAgICAgICAgIHRvZ2dsZUNsYXNzKHRhcmdldCwgb3B0aW9ucy5zd2FwQ2xhc3MsIHRydWUpO1xuICAgICAgICAgIGxhc3RTd2FwRWwgPSB0YXJnZXQ7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgbGFzdFN3YXBFbCA9IG51bGw7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocHJldlN3YXBFbCAmJiBwcmV2U3dhcEVsICE9PSBsYXN0U3dhcEVsKSB7XG4gICAgICAgICAgdG9nZ2xlQ2xhc3MocHJldlN3YXBFbCwgb3B0aW9ucy5zd2FwQ2xhc3MsIGZhbHNlKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBjaGFuZ2VkKCk7XG4gICAgICBjb21wbGV0ZWQodHJ1ZSk7XG4gICAgICBjYW5jZWwoKTtcbiAgICB9LFxuICAgIGRyb3A6IGZ1bmN0aW9uIGRyb3AoX3JlZjMpIHtcbiAgICAgIHZhciBhY3RpdmVTb3J0YWJsZSA9IF9yZWYzLmFjdGl2ZVNvcnRhYmxlLFxuICAgICAgICAgIHB1dFNvcnRhYmxlID0gX3JlZjMucHV0U29ydGFibGUsXG4gICAgICAgICAgZHJhZ0VsID0gX3JlZjMuZHJhZ0VsO1xuICAgICAgdmFyIHRvU29ydGFibGUgPSBwdXRTb3J0YWJsZSB8fCB0aGlzLnNvcnRhYmxlO1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7XG4gICAgICBsYXN0U3dhcEVsICYmIHRvZ2dsZUNsYXNzKGxhc3RTd2FwRWwsIG9wdGlvbnMuc3dhcENsYXNzLCBmYWxzZSk7XG5cbiAgICAgIGlmIChsYXN0U3dhcEVsICYmIChvcHRpb25zLnN3YXAgfHwgcHV0U29ydGFibGUgJiYgcHV0U29ydGFibGUub3B0aW9ucy5zd2FwKSkge1xuICAgICAgICBpZiAoZHJhZ0VsICE9PSBsYXN0U3dhcEVsKSB7XG4gICAgICAgICAgdG9Tb3J0YWJsZS5jYXB0dXJlQW5pbWF0aW9uU3RhdGUoKTtcbiAgICAgICAgICBpZiAodG9Tb3J0YWJsZSAhPT0gYWN0aXZlU29ydGFibGUpIGFjdGl2ZVNvcnRhYmxlLmNhcHR1cmVBbmltYXRpb25TdGF0ZSgpO1xuICAgICAgICAgIHN3YXBOb2RlcyhkcmFnRWwsIGxhc3RTd2FwRWwpO1xuICAgICAgICAgIHRvU29ydGFibGUuYW5pbWF0ZUFsbCgpO1xuICAgICAgICAgIGlmICh0b1NvcnRhYmxlICE9PSBhY3RpdmVTb3J0YWJsZSkgYWN0aXZlU29ydGFibGUuYW5pbWF0ZUFsbCgpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSxcbiAgICBudWxsaW5nOiBmdW5jdGlvbiBudWxsaW5nKCkge1xuICAgICAgbGFzdFN3YXBFbCA9IG51bGw7XG4gICAgfVxuICB9O1xuICByZXR1cm4gX2V4dGVuZHMoU3dhcCwge1xuICAgIHBsdWdpbk5hbWU6ICdzd2FwJyxcbiAgICBldmVudFByb3BlcnRpZXM6IGZ1bmN0aW9uIGV2ZW50UHJvcGVydGllcygpIHtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIHN3YXBJdGVtOiBsYXN0U3dhcEVsXG4gICAgICB9O1xuICAgIH1cbiAgfSk7XG59XG5cbmZ1bmN0aW9uIHN3YXBOb2RlcyhuMSwgbjIpIHtcbiAgdmFyIHAxID0gbjEucGFyZW50Tm9kZSxcbiAgICAgIHAyID0gbjIucGFyZW50Tm9kZSxcbiAgICAgIGkxLFxuICAgICAgaTI7XG4gIGlmICghcDEgfHwgIXAyIHx8IHAxLmlzRXF1YWxOb2RlKG4yKSB8fCBwMi5pc0VxdWFsTm9kZShuMSkpIHJldHVybjtcbiAgaTEgPSBpbmRleChuMSk7XG4gIGkyID0gaW5kZXgobjIpO1xuXG4gIGlmIChwMS5pc0VxdWFsTm9kZShwMikgJiYgaTEgPCBpMikge1xuICAgIGkyKys7XG4gIH1cblxuICBwMS5pbnNlcnRCZWZvcmUobjIsIHAxLmNoaWxkcmVuW2kxXSk7XG4gIHAyLmluc2VydEJlZm9yZShuMSwgcDIuY2hpbGRyZW5baTJdKTtcbn1cblxudmFyIG11bHRpRHJhZ0VsZW1lbnRzID0gW10sXG4gICAgbXVsdGlEcmFnQ2xvbmVzID0gW10sXG4gICAgbGFzdE11bHRpRHJhZ1NlbGVjdCxcbiAgICAvLyBmb3Igc2VsZWN0aW9uIHdpdGggbW9kaWZpZXIga2V5IGRvd24gKFNISUZUKVxubXVsdGlEcmFnU29ydGFibGUsXG4gICAgaW5pdGlhbEZvbGRpbmcgPSBmYWxzZSxcbiAgICAvLyBJbml0aWFsIG11bHRpLWRyYWcgZm9sZCB3aGVuIGRyYWcgc3RhcnRlZFxuZm9sZGluZyA9IGZhbHNlLFxuICAgIC8vIEZvbGRpbmcgYW55IG90aGVyIHRpbWVcbmRyYWdTdGFydGVkID0gZmFsc2UsXG4gICAgZHJhZ0VsJDEsXG4gICAgY2xvbmVzRnJvbVJlY3QsXG4gICAgY2xvbmVzSGlkZGVuO1xuXG5mdW5jdGlvbiBNdWx0aURyYWdQbHVnaW4oKSB7XG4gIGZ1bmN0aW9uIE11bHRpRHJhZyhzb3J0YWJsZSkge1xuICAgIC8vIEJpbmQgYWxsIHByaXZhdGUgbWV0aG9kc1xuICAgIGZvciAodmFyIGZuIGluIHRoaXMpIHtcbiAgICAgIGlmIChmbi5jaGFyQXQoMCkgPT09ICdfJyAmJiB0eXBlb2YgdGhpc1tmbl0gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpc1tmbl0gPSB0aGlzW2ZuXS5iaW5kKHRoaXMpO1xuICAgICAgfVxuICAgIH1cblxuICAgIGlmICghc29ydGFibGUub3B0aW9ucy5hdm9pZEltcGxpY2l0RGVzZWxlY3QpIHtcbiAgICAgIGlmIChzb3J0YWJsZS5vcHRpb25zLnN1cHBvcnRQb2ludGVyKSB7XG4gICAgICAgIG9uKGRvY3VtZW50LCAncG9pbnRlcnVwJywgdGhpcy5fZGVzZWxlY3RNdWx0aURyYWcpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgb24oZG9jdW1lbnQsICdtb3VzZXVwJywgdGhpcy5fZGVzZWxlY3RNdWx0aURyYWcpO1xuICAgICAgICBvbihkb2N1bWVudCwgJ3RvdWNoZW5kJywgdGhpcy5fZGVzZWxlY3RNdWx0aURyYWcpO1xuICAgICAgfVxuICAgIH1cblxuICAgIG9uKGRvY3VtZW50LCAna2V5ZG93bicsIHRoaXMuX2NoZWNrS2V5RG93bik7XG4gICAgb24oZG9jdW1lbnQsICdrZXl1cCcsIHRoaXMuX2NoZWNrS2V5VXApO1xuICAgIHRoaXMuZGVmYXVsdHMgPSB7XG4gICAgICBzZWxlY3RlZENsYXNzOiAnc29ydGFibGUtc2VsZWN0ZWQnLFxuICAgICAgbXVsdGlEcmFnS2V5OiBudWxsLFxuICAgICAgYXZvaWRJbXBsaWNpdERlc2VsZWN0OiBmYWxzZSxcbiAgICAgIHNldERhdGE6IGZ1bmN0aW9uIHNldERhdGEoZGF0YVRyYW5zZmVyLCBkcmFnRWwpIHtcbiAgICAgICAgdmFyIGRhdGEgPSAnJztcblxuICAgICAgICBpZiAobXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoICYmIG11bHRpRHJhZ1NvcnRhYmxlID09PSBzb3J0YWJsZSkge1xuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQsIGkpIHtcbiAgICAgICAgICAgIGRhdGEgKz0gKCFpID8gJycgOiAnLCAnKSArIG11bHRpRHJhZ0VsZW1lbnQudGV4dENvbnRlbnQ7XG4gICAgICAgICAgfSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgZGF0YSA9IGRyYWdFbC50ZXh0Q29udGVudDtcbiAgICAgICAgfVxuXG4gICAgICAgIGRhdGFUcmFuc2Zlci5zZXREYXRhKCdUZXh0JywgZGF0YSk7XG4gICAgICB9XG4gICAgfTtcbiAgfVxuXG4gIE11bHRpRHJhZy5wcm90b3R5cGUgPSB7XG4gICAgbXVsdGlEcmFnS2V5RG93bjogZmFsc2UsXG4gICAgaXNNdWx0aURyYWc6IGZhbHNlLFxuICAgIGRlbGF5U3RhcnRHbG9iYWw6IGZ1bmN0aW9uIGRlbGF5U3RhcnRHbG9iYWwoX3JlZikge1xuICAgICAgdmFyIGRyYWdnZWQgPSBfcmVmLmRyYWdFbDtcbiAgICAgIGRyYWdFbCQxID0gZHJhZ2dlZDtcbiAgICB9LFxuICAgIGRlbGF5RW5kZWQ6IGZ1bmN0aW9uIGRlbGF5RW5kZWQoKSB7XG4gICAgICB0aGlzLmlzTXVsdGlEcmFnID0gfm11bHRpRHJhZ0VsZW1lbnRzLmluZGV4T2YoZHJhZ0VsJDEpO1xuICAgIH0sXG4gICAgc2V0dXBDbG9uZTogZnVuY3Rpb24gc2V0dXBDbG9uZShfcmVmMikge1xuICAgICAgdmFyIHNvcnRhYmxlID0gX3JlZjIuc29ydGFibGUsXG4gICAgICAgICAgY2FuY2VsID0gX3JlZjIuY2FuY2VsO1xuICAgICAgaWYgKCF0aGlzLmlzTXVsdGlEcmFnKSByZXR1cm47XG5cbiAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgbXVsdGlEcmFnQ2xvbmVzLnB1c2goY2xvbmUobXVsdGlEcmFnRWxlbWVudHNbaV0pKTtcbiAgICAgICAgbXVsdGlEcmFnQ2xvbmVzW2ldLnNvcnRhYmxlSW5kZXggPSBtdWx0aURyYWdFbGVtZW50c1tpXS5zb3J0YWJsZUluZGV4O1xuICAgICAgICBtdWx0aURyYWdDbG9uZXNbaV0uZHJhZ2dhYmxlID0gZmFsc2U7XG4gICAgICAgIG11bHRpRHJhZ0Nsb25lc1tpXS5zdHlsZVsnd2lsbC1jaGFuZ2UnXSA9ICcnO1xuICAgICAgICB0b2dnbGVDbGFzcyhtdWx0aURyYWdDbG9uZXNbaV0sIHRoaXMub3B0aW9ucy5zZWxlY3RlZENsYXNzLCBmYWxzZSk7XG4gICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzW2ldID09PSBkcmFnRWwkMSAmJiB0b2dnbGVDbGFzcyhtdWx0aURyYWdDbG9uZXNbaV0sIHRoaXMub3B0aW9ucy5jaG9zZW5DbGFzcywgZmFsc2UpO1xuICAgICAgfVxuXG4gICAgICBzb3J0YWJsZS5faGlkZUNsb25lKCk7XG5cbiAgICAgIGNhbmNlbCgpO1xuICAgIH0sXG4gICAgY2xvbmU6IGZ1bmN0aW9uIGNsb25lKF9yZWYzKSB7XG4gICAgICB2YXIgc29ydGFibGUgPSBfcmVmMy5zb3J0YWJsZSxcbiAgICAgICAgICByb290RWwgPSBfcmVmMy5yb290RWwsXG4gICAgICAgICAgZGlzcGF0Y2hTb3J0YWJsZUV2ZW50ID0gX3JlZjMuZGlzcGF0Y2hTb3J0YWJsZUV2ZW50LFxuICAgICAgICAgIGNhbmNlbCA9IF9yZWYzLmNhbmNlbDtcbiAgICAgIGlmICghdGhpcy5pc011bHRpRHJhZykgcmV0dXJuO1xuXG4gICAgICBpZiAoIXRoaXMub3B0aW9ucy5yZW1vdmVDbG9uZU9uSGlkZSkge1xuICAgICAgICBpZiAobXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoICYmIG11bHRpRHJhZ1NvcnRhYmxlID09PSBzb3J0YWJsZSkge1xuICAgICAgICAgIGluc2VydE11bHRpRHJhZ0Nsb25lcyh0cnVlLCByb290RWwpO1xuICAgICAgICAgIGRpc3BhdGNoU29ydGFibGVFdmVudCgnY2xvbmUnKTtcbiAgICAgICAgICBjYW5jZWwoKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sXG4gICAgc2hvd0Nsb25lOiBmdW5jdGlvbiBzaG93Q2xvbmUoX3JlZjQpIHtcbiAgICAgIHZhciBjbG9uZU5vd1Nob3duID0gX3JlZjQuY2xvbmVOb3dTaG93bixcbiAgICAgICAgICByb290RWwgPSBfcmVmNC5yb290RWwsXG4gICAgICAgICAgY2FuY2VsID0gX3JlZjQuY2FuY2VsO1xuICAgICAgaWYgKCF0aGlzLmlzTXVsdGlEcmFnKSByZXR1cm47XG4gICAgICBpbnNlcnRNdWx0aURyYWdDbG9uZXMoZmFsc2UsIHJvb3RFbCk7XG4gICAgICBtdWx0aURyYWdDbG9uZXMuZm9yRWFjaChmdW5jdGlvbiAoY2xvbmUpIHtcbiAgICAgICAgY3NzKGNsb25lLCAnZGlzcGxheScsICcnKTtcbiAgICAgIH0pO1xuICAgICAgY2xvbmVOb3dTaG93bigpO1xuICAgICAgY2xvbmVzSGlkZGVuID0gZmFsc2U7XG4gICAgICBjYW5jZWwoKTtcbiAgICB9LFxuICAgIGhpZGVDbG9uZTogZnVuY3Rpb24gaGlkZUNsb25lKF9yZWY1KSB7XG4gICAgICB2YXIgX3RoaXMgPSB0aGlzO1xuXG4gICAgICB2YXIgc29ydGFibGUgPSBfcmVmNS5zb3J0YWJsZSxcbiAgICAgICAgICBjbG9uZU5vd0hpZGRlbiA9IF9yZWY1LmNsb25lTm93SGlkZGVuLFxuICAgICAgICAgIGNhbmNlbCA9IF9yZWY1LmNhbmNlbDtcbiAgICAgIGlmICghdGhpcy5pc011bHRpRHJhZykgcmV0dXJuO1xuICAgICAgbXVsdGlEcmFnQ2xvbmVzLmZvckVhY2goZnVuY3Rpb24gKGNsb25lKSB7XG4gICAgICAgIGNzcyhjbG9uZSwgJ2Rpc3BsYXknLCAnbm9uZScpO1xuXG4gICAgICAgIGlmIChfdGhpcy5vcHRpb25zLnJlbW92ZUNsb25lT25IaWRlICYmIGNsb25lLnBhcmVudE5vZGUpIHtcbiAgICAgICAgICBjbG9uZS5wYXJlbnROb2RlLnJlbW92ZUNoaWxkKGNsb25lKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgICBjbG9uZU5vd0hpZGRlbigpO1xuICAgICAgY2xvbmVzSGlkZGVuID0gdHJ1ZTtcbiAgICAgIGNhbmNlbCgpO1xuICAgIH0sXG4gICAgZHJhZ1N0YXJ0R2xvYmFsOiBmdW5jdGlvbiBkcmFnU3RhcnRHbG9iYWwoX3JlZjYpIHtcbiAgICAgIHZhciBzb3J0YWJsZSA9IF9yZWY2LnNvcnRhYmxlO1xuXG4gICAgICBpZiAoIXRoaXMuaXNNdWx0aURyYWcgJiYgbXVsdGlEcmFnU29ydGFibGUpIHtcbiAgICAgICAgbXVsdGlEcmFnU29ydGFibGUubXVsdGlEcmFnLl9kZXNlbGVjdE11bHRpRHJhZygpO1xuICAgICAgfVxuXG4gICAgICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50KSB7XG4gICAgICAgIG11bHRpRHJhZ0VsZW1lbnQuc29ydGFibGVJbmRleCA9IGluZGV4KG11bHRpRHJhZ0VsZW1lbnQpO1xuICAgICAgfSk7IC8vIFNvcnQgbXVsdGktZHJhZyBlbGVtZW50c1xuXG4gICAgICBtdWx0aURyYWdFbGVtZW50cyA9IG11bHRpRHJhZ0VsZW1lbnRzLnNvcnQoZnVuY3Rpb24gKGEsIGIpIHtcbiAgICAgICAgcmV0dXJuIGEuc29ydGFibGVJbmRleCAtIGIuc29ydGFibGVJbmRleDtcbiAgICAgIH0pO1xuICAgICAgZHJhZ1N0YXJ0ZWQgPSB0cnVlO1xuICAgIH0sXG4gICAgZHJhZ1N0YXJ0ZWQ6IGZ1bmN0aW9uIGRyYWdTdGFydGVkKF9yZWY3KSB7XG4gICAgICB2YXIgX3RoaXMyID0gdGhpcztcblxuICAgICAgdmFyIHNvcnRhYmxlID0gX3JlZjcuc29ydGFibGU7XG4gICAgICBpZiAoIXRoaXMuaXNNdWx0aURyYWcpIHJldHVybjtcblxuICAgICAgaWYgKHRoaXMub3B0aW9ucy5zb3J0KSB7XG4gICAgICAgIC8vIENhcHR1cmUgcmVjdHMsXG4gICAgICAgIC8vIGhpZGUgbXVsdGkgZHJhZyBlbGVtZW50cyAoYnkgcG9zaXRpb25pbmcgdGhlbSBhYnNvbHV0ZSksXG4gICAgICAgIC8vIHNldCBtdWx0aSBkcmFnIGVsZW1lbnRzIHJlY3RzIHRvIGRyYWdSZWN0LFxuICAgICAgICAvLyBzaG93IG11bHRpIGRyYWcgZWxlbWVudHMsXG4gICAgICAgIC8vIGFuaW1hdGUgdG8gcmVjdHMsXG4gICAgICAgIC8vIHVuc2V0IHJlY3RzICYgcmVtb3ZlIGZyb20gRE9NXG4gICAgICAgIHNvcnRhYmxlLmNhcHR1cmVBbmltYXRpb25TdGF0ZSgpO1xuXG4gICAgICAgIGlmICh0aGlzLm9wdGlvbnMuYW5pbWF0aW9uKSB7XG4gICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICAgICAgaWYgKG11bHRpRHJhZ0VsZW1lbnQgPT09IGRyYWdFbCQxKSByZXR1cm47XG4gICAgICAgICAgICBjc3MobXVsdGlEcmFnRWxlbWVudCwgJ3Bvc2l0aW9uJywgJ2Fic29sdXRlJyk7XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgdmFyIGRyYWdSZWN0ID0gZ2V0UmVjdChkcmFnRWwkMSwgZmFsc2UsIHRydWUsIHRydWUpO1xuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgICAgIGlmIChtdWx0aURyYWdFbGVtZW50ID09PSBkcmFnRWwkMSkgcmV0dXJuO1xuICAgICAgICAgICAgc2V0UmVjdChtdWx0aURyYWdFbGVtZW50LCBkcmFnUmVjdCk7XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgZm9sZGluZyA9IHRydWU7XG4gICAgICAgICAgaW5pdGlhbEZvbGRpbmcgPSB0cnVlO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIHNvcnRhYmxlLmFuaW1hdGVBbGwoZnVuY3Rpb24gKCkge1xuICAgICAgICBmb2xkaW5nID0gZmFsc2U7XG4gICAgICAgIGluaXRpYWxGb2xkaW5nID0gZmFsc2U7XG5cbiAgICAgICAgaWYgKF90aGlzMi5vcHRpb25zLmFuaW1hdGlvbikge1xuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgICAgIHVuc2V0UmVjdChtdWx0aURyYWdFbGVtZW50KTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgfSAvLyBSZW1vdmUgYWxsIGF1eGlsaWFyeSBtdWx0aWRyYWcgaXRlbXMgZnJvbSBlbCwgaWYgc29ydGluZyBlbmFibGVkXG5cblxuICAgICAgICBpZiAoX3RoaXMyLm9wdGlvbnMuc29ydCkge1xuICAgICAgICAgIHJlbW92ZU11bHRpRHJhZ0VsZW1lbnRzKCk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH0sXG4gICAgZHJhZ092ZXI6IGZ1bmN0aW9uIGRyYWdPdmVyKF9yZWY4KSB7XG4gICAgICB2YXIgdGFyZ2V0ID0gX3JlZjgudGFyZ2V0LFxuICAgICAgICAgIGNvbXBsZXRlZCA9IF9yZWY4LmNvbXBsZXRlZCxcbiAgICAgICAgICBjYW5jZWwgPSBfcmVmOC5jYW5jZWw7XG5cbiAgICAgIGlmIChmb2xkaW5nICYmIH5tdWx0aURyYWdFbGVtZW50cy5pbmRleE9mKHRhcmdldCkpIHtcbiAgICAgICAgY29tcGxldGVkKGZhbHNlKTtcbiAgICAgICAgY2FuY2VsKCk7XG4gICAgICB9XG4gICAgfSxcbiAgICByZXZlcnQ6IGZ1bmN0aW9uIHJldmVydChfcmVmOSkge1xuICAgICAgdmFyIGZyb21Tb3J0YWJsZSA9IF9yZWY5LmZyb21Tb3J0YWJsZSxcbiAgICAgICAgICByb290RWwgPSBfcmVmOS5yb290RWwsXG4gICAgICAgICAgc29ydGFibGUgPSBfcmVmOS5zb3J0YWJsZSxcbiAgICAgICAgICBkcmFnUmVjdCA9IF9yZWY5LmRyYWdSZWN0O1xuXG4gICAgICBpZiAobXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoID4gMSkge1xuICAgICAgICAvLyBTZXR1cCB1bmZvbGQgYW5pbWF0aW9uXG4gICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLmZvckVhY2goZnVuY3Rpb24gKG11bHRpRHJhZ0VsZW1lbnQpIHtcbiAgICAgICAgICBzb3J0YWJsZS5hZGRBbmltYXRpb25TdGF0ZSh7XG4gICAgICAgICAgICB0YXJnZXQ6IG11bHRpRHJhZ0VsZW1lbnQsXG4gICAgICAgICAgICByZWN0OiBmb2xkaW5nID8gZ2V0UmVjdChtdWx0aURyYWdFbGVtZW50KSA6IGRyYWdSZWN0XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgdW5zZXRSZWN0KG11bHRpRHJhZ0VsZW1lbnQpO1xuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnQuZnJvbVJlY3QgPSBkcmFnUmVjdDtcbiAgICAgICAgICBmcm9tU29ydGFibGUucmVtb3ZlQW5pbWF0aW9uU3RhdGUobXVsdGlEcmFnRWxlbWVudCk7XG4gICAgICAgIH0pO1xuICAgICAgICBmb2xkaW5nID0gZmFsc2U7XG4gICAgICAgIGluc2VydE11bHRpRHJhZ0VsZW1lbnRzKCF0aGlzLm9wdGlvbnMucmVtb3ZlQ2xvbmVPbkhpZGUsIHJvb3RFbCk7XG4gICAgICB9XG4gICAgfSxcbiAgICBkcmFnT3ZlckNvbXBsZXRlZDogZnVuY3Rpb24gZHJhZ092ZXJDb21wbGV0ZWQoX3JlZjEwKSB7XG4gICAgICB2YXIgc29ydGFibGUgPSBfcmVmMTAuc29ydGFibGUsXG4gICAgICAgICAgaXNPd25lciA9IF9yZWYxMC5pc093bmVyLFxuICAgICAgICAgIGluc2VydGlvbiA9IF9yZWYxMC5pbnNlcnRpb24sXG4gICAgICAgICAgYWN0aXZlU29ydGFibGUgPSBfcmVmMTAuYWN0aXZlU29ydGFibGUsXG4gICAgICAgICAgcGFyZW50RWwgPSBfcmVmMTAucGFyZW50RWwsXG4gICAgICAgICAgcHV0U29ydGFibGUgPSBfcmVmMTAucHV0U29ydGFibGU7XG4gICAgICB2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucztcblxuICAgICAgaWYgKGluc2VydGlvbikge1xuICAgICAgICAvLyBDbG9uZXMgbXVzdCBiZSBoaWRkZW4gYmVmb3JlIGZvbGRpbmcgYW5pbWF0aW9uIHRvIGNhcHR1cmUgZHJhZ1JlY3RBYnNvbHV0ZSBwcm9wZXJseVxuICAgICAgICBpZiAoaXNPd25lcikge1xuICAgICAgICAgIGFjdGl2ZVNvcnRhYmxlLl9oaWRlQ2xvbmUoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGluaXRpYWxGb2xkaW5nID0gZmFsc2U7IC8vIElmIGxlYXZpbmcgc29ydDpmYWxzZSByb290LCBvciBhbHJlYWR5IGZvbGRpbmcgLSBGb2xkIHRvIG5ldyBsb2NhdGlvblxuXG4gICAgICAgIGlmIChvcHRpb25zLmFuaW1hdGlvbiAmJiBtdWx0aURyYWdFbGVtZW50cy5sZW5ndGggPiAxICYmIChmb2xkaW5nIHx8ICFpc093bmVyICYmICFhY3RpdmVTb3J0YWJsZS5vcHRpb25zLnNvcnQgJiYgIXB1dFNvcnRhYmxlKSkge1xuICAgICAgICAgIC8vIEZvbGQ6IFNldCBhbGwgbXVsdGkgZHJhZyBlbGVtZW50cydzIHJlY3RzIHRvIGRyYWdFbCdzIHJlY3Qgd2hlbiBtdWx0aS1kcmFnIGVsZW1lbnRzIGFyZSBpbnZpc2libGVcbiAgICAgICAgICB2YXIgZHJhZ1JlY3RBYnNvbHV0ZSA9IGdldFJlY3QoZHJhZ0VsJDEsIGZhbHNlLCB0cnVlLCB0cnVlKTtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50KSB7XG4gICAgICAgICAgICBpZiAobXVsdGlEcmFnRWxlbWVudCA9PT0gZHJhZ0VsJDEpIHJldHVybjtcbiAgICAgICAgICAgIHNldFJlY3QobXVsdGlEcmFnRWxlbWVudCwgZHJhZ1JlY3RBYnNvbHV0ZSk7IC8vIE1vdmUgZWxlbWVudChzKSB0byBlbmQgb2YgcGFyZW50RWwgc28gdGhhdCBpdCBkb2VzIG5vdCBpbnRlcmZlcmUgd2l0aCBtdWx0aS1kcmFnIGNsb25lcyBpbnNlcnRpb24gaWYgdGhleSBhcmUgaW5zZXJ0ZWRcbiAgICAgICAgICAgIC8vIHdoaWxlIGZvbGRpbmcsIGFuZCBzbyB0aGF0IHdlIGNhbiBjYXB0dXJlIHRoZW0gYWdhaW4gYmVjYXVzZSBvbGQgc29ydGFibGUgd2lsbCBubyBsb25nZXIgYmUgZnJvbVNvcnRhYmxlXG5cbiAgICAgICAgICAgIHBhcmVudEVsLmFwcGVuZENoaWxkKG11bHRpRHJhZ0VsZW1lbnQpO1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIGZvbGRpbmcgPSB0cnVlO1xuICAgICAgICB9IC8vIENsb25lcyBtdXN0IGJlIHNob3duIChhbmQgY2hlY2sgdG8gcmVtb3ZlIG11bHRpIGRyYWdzKSBhZnRlciBmb2xkaW5nIHdoZW4gaW50ZXJmZXJpbmcgbXVsdGlEcmFnRWxlbWVudHMgYXJlIG1vdmVkIG91dFxuXG5cbiAgICAgICAgaWYgKCFpc093bmVyKSB7XG4gICAgICAgICAgLy8gT25seSByZW1vdmUgaWYgbm90IGZvbGRpbmcgKGZvbGRpbmcgd2lsbCByZW1vdmUgdGhlbSBhbnl3YXlzKVxuICAgICAgICAgIGlmICghZm9sZGluZykge1xuICAgICAgICAgICAgcmVtb3ZlTXVsdGlEcmFnRWxlbWVudHMoKTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBpZiAobXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoID4gMSkge1xuICAgICAgICAgICAgdmFyIGNsb25lc0hpZGRlbkJlZm9yZSA9IGNsb25lc0hpZGRlbjtcblxuICAgICAgICAgICAgYWN0aXZlU29ydGFibGUuX3Nob3dDbG9uZShzb3J0YWJsZSk7IC8vIFVuZm9sZCBhbmltYXRpb24gZm9yIGNsb25lcyBpZiBzaG93aW5nIGZyb20gaGlkZGVuXG5cblxuICAgICAgICAgICAgaWYgKGFjdGl2ZVNvcnRhYmxlLm9wdGlvbnMuYW5pbWF0aW9uICYmICFjbG9uZXNIaWRkZW4gJiYgY2xvbmVzSGlkZGVuQmVmb3JlKSB7XG4gICAgICAgICAgICAgIG11bHRpRHJhZ0Nsb25lcy5mb3JFYWNoKGZ1bmN0aW9uIChjbG9uZSkge1xuICAgICAgICAgICAgICAgIGFjdGl2ZVNvcnRhYmxlLmFkZEFuaW1hdGlvblN0YXRlKHtcbiAgICAgICAgICAgICAgICAgIHRhcmdldDogY2xvbmUsXG4gICAgICAgICAgICAgICAgICByZWN0OiBjbG9uZXNGcm9tUmVjdFxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGNsb25lLmZyb21SZWN0ID0gY2xvbmVzRnJvbVJlY3Q7XG4gICAgICAgICAgICAgICAgY2xvbmUudGhpc0FuaW1hdGlvbkR1cmF0aW9uID0gbnVsbDtcbiAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGFjdGl2ZVNvcnRhYmxlLl9zaG93Q2xvbmUoc29ydGFibGUpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sXG4gICAgZHJhZ092ZXJBbmltYXRpb25DYXB0dXJlOiBmdW5jdGlvbiBkcmFnT3ZlckFuaW1hdGlvbkNhcHR1cmUoX3JlZjExKSB7XG4gICAgICB2YXIgZHJhZ1JlY3QgPSBfcmVmMTEuZHJhZ1JlY3QsXG4gICAgICAgICAgaXNPd25lciA9IF9yZWYxMS5pc093bmVyLFxuICAgICAgICAgIGFjdGl2ZVNvcnRhYmxlID0gX3JlZjExLmFjdGl2ZVNvcnRhYmxlO1xuICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICBtdWx0aURyYWdFbGVtZW50LnRoaXNBbmltYXRpb25EdXJhdGlvbiA9IG51bGw7XG4gICAgICB9KTtcblxuICAgICAgaWYgKGFjdGl2ZVNvcnRhYmxlLm9wdGlvbnMuYW5pbWF0aW9uICYmICFpc093bmVyICYmIGFjdGl2ZVNvcnRhYmxlLm11bHRpRHJhZy5pc011bHRpRHJhZykge1xuICAgICAgICBjbG9uZXNGcm9tUmVjdCA9IF9leHRlbmRzKHt9LCBkcmFnUmVjdCk7XG4gICAgICAgIHZhciBkcmFnTWF0cml4ID0gbWF0cml4KGRyYWdFbCQxLCB0cnVlKTtcbiAgICAgICAgY2xvbmVzRnJvbVJlY3QudG9wIC09IGRyYWdNYXRyaXguZjtcbiAgICAgICAgY2xvbmVzRnJvbVJlY3QubGVmdCAtPSBkcmFnTWF0cml4LmU7XG4gICAgICB9XG4gICAgfSxcbiAgICBkcmFnT3ZlckFuaW1hdGlvbkNvbXBsZXRlOiBmdW5jdGlvbiBkcmFnT3ZlckFuaW1hdGlvbkNvbXBsZXRlKCkge1xuICAgICAgaWYgKGZvbGRpbmcpIHtcbiAgICAgICAgZm9sZGluZyA9IGZhbHNlO1xuICAgICAgICByZW1vdmVNdWx0aURyYWdFbGVtZW50cygpO1xuICAgICAgfVxuICAgIH0sXG4gICAgZHJvcDogZnVuY3Rpb24gZHJvcChfcmVmMTIpIHtcbiAgICAgIHZhciBldnQgPSBfcmVmMTIub3JpZ2luYWxFdmVudCxcbiAgICAgICAgICByb290RWwgPSBfcmVmMTIucm9vdEVsLFxuICAgICAgICAgIHBhcmVudEVsID0gX3JlZjEyLnBhcmVudEVsLFxuICAgICAgICAgIHNvcnRhYmxlID0gX3JlZjEyLnNvcnRhYmxlLFxuICAgICAgICAgIGRpc3BhdGNoU29ydGFibGVFdmVudCA9IF9yZWYxMi5kaXNwYXRjaFNvcnRhYmxlRXZlbnQsXG4gICAgICAgICAgb2xkSW5kZXggPSBfcmVmMTIub2xkSW5kZXgsXG4gICAgICAgICAgcHV0U29ydGFibGUgPSBfcmVmMTIucHV0U29ydGFibGU7XG4gICAgICB2YXIgdG9Tb3J0YWJsZSA9IHB1dFNvcnRhYmxlIHx8IHRoaXMuc29ydGFibGU7XG4gICAgICBpZiAoIWV2dCkgcmV0dXJuO1xuICAgICAgdmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnMsXG4gICAgICAgICAgY2hpbGRyZW4gPSBwYXJlbnRFbC5jaGlsZHJlbjsgLy8gTXVsdGktZHJhZyBzZWxlY3Rpb25cblxuICAgICAgaWYgKCFkcmFnU3RhcnRlZCkge1xuICAgICAgICBpZiAob3B0aW9ucy5tdWx0aURyYWdLZXkgJiYgIXRoaXMubXVsdGlEcmFnS2V5RG93bikge1xuICAgICAgICAgIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKCk7XG4gICAgICAgIH1cblxuICAgICAgICB0b2dnbGVDbGFzcyhkcmFnRWwkMSwgb3B0aW9ucy5zZWxlY3RlZENsYXNzLCAhfm11bHRpRHJhZ0VsZW1lbnRzLmluZGV4T2YoZHJhZ0VsJDEpKTtcblxuICAgICAgICBpZiAoIX5tdWx0aURyYWdFbGVtZW50cy5pbmRleE9mKGRyYWdFbCQxKSkge1xuICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLnB1c2goZHJhZ0VsJDEpO1xuICAgICAgICAgIGRpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgc29ydGFibGU6IHNvcnRhYmxlLFxuICAgICAgICAgICAgcm9vdEVsOiByb290RWwsXG4gICAgICAgICAgICBuYW1lOiAnc2VsZWN0JyxcbiAgICAgICAgICAgIHRhcmdldEVsOiBkcmFnRWwkMSxcbiAgICAgICAgICAgIG9yaWdpbmFsRXZlbnQ6IGV2dFxuICAgICAgICAgIH0pOyAvLyBNb2RpZmllciBhY3RpdmF0ZWQsIHNlbGVjdCBmcm9tIGxhc3QgdG8gZHJhZ0VsXG5cbiAgICAgICAgICBpZiAoZXZ0LnNoaWZ0S2V5ICYmIGxhc3RNdWx0aURyYWdTZWxlY3QgJiYgc29ydGFibGUuZWwuY29udGFpbnMobGFzdE11bHRpRHJhZ1NlbGVjdCkpIHtcbiAgICAgICAgICAgIHZhciBsYXN0SW5kZXggPSBpbmRleChsYXN0TXVsdGlEcmFnU2VsZWN0KSxcbiAgICAgICAgICAgICAgICBjdXJyZW50SW5kZXggPSBpbmRleChkcmFnRWwkMSk7XG5cbiAgICAgICAgICAgIGlmICh+bGFzdEluZGV4ICYmIH5jdXJyZW50SW5kZXggJiYgbGFzdEluZGV4ICE9PSBjdXJyZW50SW5kZXgpIHtcbiAgICAgICAgICAgICAgLy8gTXVzdCBpbmNsdWRlIGxhc3RNdWx0aURyYWdTZWxlY3QgKHNlbGVjdCBpdCksIGluIGNhc2UgbW9kaWZpZWQgc2VsZWN0aW9uIGZyb20gbm8gc2VsZWN0aW9uXG4gICAgICAgICAgICAgIC8vIChidXQgcHJldmlvdXMgc2VsZWN0aW9uIGV4aXN0ZWQpXG4gICAgICAgICAgICAgIHZhciBuLCBpO1xuXG4gICAgICAgICAgICAgIGlmIChjdXJyZW50SW5kZXggPiBsYXN0SW5kZXgpIHtcbiAgICAgICAgICAgICAgICBpID0gbGFzdEluZGV4O1xuICAgICAgICAgICAgICAgIG4gPSBjdXJyZW50SW5kZXg7XG4gICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgaSA9IGN1cnJlbnRJbmRleDtcbiAgICAgICAgICAgICAgICBuID0gbGFzdEluZGV4ICsgMTtcbiAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgIGZvciAoOyBpIDwgbjsgaSsrKSB7XG4gICAgICAgICAgICAgICAgaWYgKH5tdWx0aURyYWdFbGVtZW50cy5pbmRleE9mKGNoaWxkcmVuW2ldKSkgY29udGludWU7XG4gICAgICAgICAgICAgICAgdG9nZ2xlQ2xhc3MoY2hpbGRyZW5baV0sIG9wdGlvbnMuc2VsZWN0ZWRDbGFzcywgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMucHVzaChjaGlsZHJlbltpXSk7XG4gICAgICAgICAgICAgICAgZGlzcGF0Y2hFdmVudCh7XG4gICAgICAgICAgICAgICAgICBzb3J0YWJsZTogc29ydGFibGUsXG4gICAgICAgICAgICAgICAgICByb290RWw6IHJvb3RFbCxcbiAgICAgICAgICAgICAgICAgIG5hbWU6ICdzZWxlY3QnLFxuICAgICAgICAgICAgICAgICAgdGFyZ2V0RWw6IGNoaWxkcmVuW2ldLFxuICAgICAgICAgICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgbGFzdE11bHRpRHJhZ1NlbGVjdCA9IGRyYWdFbCQxO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIG11bHRpRHJhZ1NvcnRhYmxlID0gdG9Tb3J0YWJsZTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5zcGxpY2UobXVsdGlEcmFnRWxlbWVudHMuaW5kZXhPZihkcmFnRWwkMSksIDEpO1xuICAgICAgICAgIGxhc3RNdWx0aURyYWdTZWxlY3QgPSBudWxsO1xuICAgICAgICAgIGRpc3BhdGNoRXZlbnQoe1xuICAgICAgICAgICAgc29ydGFibGU6IHNvcnRhYmxlLFxuICAgICAgICAgICAgcm9vdEVsOiByb290RWwsXG4gICAgICAgICAgICBuYW1lOiAnZGVzZWxlY3QnLFxuICAgICAgICAgICAgdGFyZ2V0RWw6IGRyYWdFbCQxLFxuICAgICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgIH0gLy8gTXVsdGktZHJhZyBkcm9wXG5cblxuICAgICAgaWYgKGRyYWdTdGFydGVkICYmIHRoaXMuaXNNdWx0aURyYWcpIHtcbiAgICAgICAgZm9sZGluZyA9IGZhbHNlOyAvLyBEbyBub3QgXCJ1bmZvbGRcIiBhZnRlciBhcm91bmQgZHJhZ0VsIGlmIHJldmVydGVkXG5cbiAgICAgICAgaWYgKChwYXJlbnRFbFtleHBhbmRvXS5vcHRpb25zLnNvcnQgfHwgcGFyZW50RWwgIT09IHJvb3RFbCkgJiYgbXVsdGlEcmFnRWxlbWVudHMubGVuZ3RoID4gMSkge1xuICAgICAgICAgIHZhciBkcmFnUmVjdCA9IGdldFJlY3QoZHJhZ0VsJDEpLFxuICAgICAgICAgICAgICBtdWx0aURyYWdJbmRleCA9IGluZGV4KGRyYWdFbCQxLCAnOm5vdCguJyArIHRoaXMub3B0aW9ucy5zZWxlY3RlZENsYXNzICsgJyknKTtcbiAgICAgICAgICBpZiAoIWluaXRpYWxGb2xkaW5nICYmIG9wdGlvbnMuYW5pbWF0aW9uKSBkcmFnRWwkMS50aGlzQW5pbWF0aW9uRHVyYXRpb24gPSBudWxsO1xuICAgICAgICAgIHRvU29ydGFibGUuY2FwdHVyZUFuaW1hdGlvblN0YXRlKCk7XG5cbiAgICAgICAgICBpZiAoIWluaXRpYWxGb2xkaW5nKSB7XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5hbmltYXRpb24pIHtcbiAgICAgICAgICAgICAgZHJhZ0VsJDEuZnJvbVJlY3QgPSBkcmFnUmVjdDtcbiAgICAgICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICAgICAgICAgIG11bHRpRHJhZ0VsZW1lbnQudGhpc0FuaW1hdGlvbkR1cmF0aW9uID0gbnVsbDtcblxuICAgICAgICAgICAgICAgIGlmIChtdWx0aURyYWdFbGVtZW50ICE9PSBkcmFnRWwkMSkge1xuICAgICAgICAgICAgICAgICAgdmFyIHJlY3QgPSBmb2xkaW5nID8gZ2V0UmVjdChtdWx0aURyYWdFbGVtZW50KSA6IGRyYWdSZWN0O1xuICAgICAgICAgICAgICAgICAgbXVsdGlEcmFnRWxlbWVudC5mcm9tUmVjdCA9IHJlY3Q7IC8vIFByZXBhcmUgdW5mb2xkIGFuaW1hdGlvblxuXG4gICAgICAgICAgICAgICAgICB0b1NvcnRhYmxlLmFkZEFuaW1hdGlvblN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgdGFyZ2V0OiBtdWx0aURyYWdFbGVtZW50LFxuICAgICAgICAgICAgICAgICAgICByZWN0OiByZWN0XG4gICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSAvLyBNdWx0aSBkcmFnIGVsZW1lbnRzIGFyZSBub3QgbmVjZXNzYXJpbHkgcmVtb3ZlZCBmcm9tIHRoZSBET00gb24gZHJvcCwgc28gdG8gcmVpbnNlcnRcbiAgICAgICAgICAgIC8vIHByb3Blcmx5IHRoZXkgbXVzdCBhbGwgYmUgcmVtb3ZlZFxuXG5cbiAgICAgICAgICAgIHJlbW92ZU11bHRpRHJhZ0VsZW1lbnRzKCk7XG4gICAgICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50KSB7XG4gICAgICAgICAgICAgIGlmIChjaGlsZHJlblttdWx0aURyYWdJbmRleF0pIHtcbiAgICAgICAgICAgICAgICBwYXJlbnRFbC5pbnNlcnRCZWZvcmUobXVsdGlEcmFnRWxlbWVudCwgY2hpbGRyZW5bbXVsdGlEcmFnSW5kZXhdKTtcbiAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBwYXJlbnRFbC5hcHBlbmRDaGlsZChtdWx0aURyYWdFbGVtZW50KTtcbiAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgIG11bHRpRHJhZ0luZGV4Kys7XG4gICAgICAgICAgICB9KTsgLy8gSWYgaW5pdGlhbCBmb2xkaW5nIGlzIGRvbmUsIHRoZSBlbGVtZW50cyBtYXkgaGF2ZSBjaGFuZ2VkIHBvc2l0aW9uIGJlY2F1c2UgdGhleSBhcmUgbm93XG4gICAgICAgICAgICAvLyB1bmZvbGRpbmcgYXJvdW5kIGRyYWdFbCwgZXZlbiB0aG91Z2ggZHJhZ0VsIG1heSBub3QgaGF2ZSBoaXMgaW5kZXggY2hhbmdlZCwgc28gdXBkYXRlIGV2ZW50XG4gICAgICAgICAgICAvLyBtdXN0IGJlIGZpcmVkIGhlcmUgYXMgU29ydGFibGUgd2lsbCBub3QuXG5cbiAgICAgICAgICAgIGlmIChvbGRJbmRleCA9PT0gaW5kZXgoZHJhZ0VsJDEpKSB7XG4gICAgICAgICAgICAgIHZhciB1cGRhdGUgPSBmYWxzZTtcbiAgICAgICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICAgICAgICAgIGlmIChtdWx0aURyYWdFbGVtZW50LnNvcnRhYmxlSW5kZXggIT09IGluZGV4KG11bHRpRHJhZ0VsZW1lbnQpKSB7XG4gICAgICAgICAgICAgICAgICB1cGRhdGUgPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgaWYgKHVwZGF0ZSkge1xuICAgICAgICAgICAgICAgIGRpc3BhdGNoU29ydGFibGVFdmVudCgndXBkYXRlJyk7XG4gICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9IC8vIE11c3QgYmUgZG9uZSBhZnRlciBjYXB0dXJpbmcgaW5kaXZpZHVhbCByZWN0cyAoc2Nyb2xsIGJhcilcblxuXG4gICAgICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICAgICAgdW5zZXRSZWN0KG11bHRpRHJhZ0VsZW1lbnQpO1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIHRvU29ydGFibGUuYW5pbWF0ZUFsbCgpO1xuICAgICAgICB9XG5cbiAgICAgICAgbXVsdGlEcmFnU29ydGFibGUgPSB0b1NvcnRhYmxlO1xuICAgICAgfSAvLyBSZW1vdmUgY2xvbmVzIGlmIG5lY2Vzc2FyeVxuXG5cbiAgICAgIGlmIChyb290RWwgPT09IHBhcmVudEVsIHx8IHB1dFNvcnRhYmxlICYmIHB1dFNvcnRhYmxlLmxhc3RQdXRNb2RlICE9PSAnY2xvbmUnKSB7XG4gICAgICAgIG11bHRpRHJhZ0Nsb25lcy5mb3JFYWNoKGZ1bmN0aW9uIChjbG9uZSkge1xuICAgICAgICAgIGNsb25lLnBhcmVudE5vZGUgJiYgY2xvbmUucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChjbG9uZSk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0sXG4gICAgbnVsbGluZ0dsb2JhbDogZnVuY3Rpb24gbnVsbGluZ0dsb2JhbCgpIHtcbiAgICAgIHRoaXMuaXNNdWx0aURyYWcgPSBkcmFnU3RhcnRlZCA9IGZhbHNlO1xuICAgICAgbXVsdGlEcmFnQ2xvbmVzLmxlbmd0aCA9IDA7XG4gICAgfSxcbiAgICBkZXN0cm95R2xvYmFsOiBmdW5jdGlvbiBkZXN0cm95R2xvYmFsKCkge1xuICAgICAgdGhpcy5fZGVzZWxlY3RNdWx0aURyYWcoKTtcblxuICAgICAgb2ZmKGRvY3VtZW50LCAncG9pbnRlcnVwJywgdGhpcy5fZGVzZWxlY3RNdWx0aURyYWcpO1xuICAgICAgb2ZmKGRvY3VtZW50LCAnbW91c2V1cCcsIHRoaXMuX2Rlc2VsZWN0TXVsdGlEcmFnKTtcbiAgICAgIG9mZihkb2N1bWVudCwgJ3RvdWNoZW5kJywgdGhpcy5fZGVzZWxlY3RNdWx0aURyYWcpO1xuICAgICAgb2ZmKGRvY3VtZW50LCAna2V5ZG93bicsIHRoaXMuX2NoZWNrS2V5RG93bik7XG4gICAgICBvZmYoZG9jdW1lbnQsICdrZXl1cCcsIHRoaXMuX2NoZWNrS2V5VXApO1xuICAgIH0sXG4gICAgX2Rlc2VsZWN0TXVsdGlEcmFnOiBmdW5jdGlvbiBfZGVzZWxlY3RNdWx0aURyYWcoZXZ0KSB7XG4gICAgICBpZiAodHlwZW9mIGRyYWdTdGFydGVkICE9PSBcInVuZGVmaW5lZFwiICYmIGRyYWdTdGFydGVkKSByZXR1cm47IC8vIE9ubHkgZGVzZWxlY3QgaWYgc2VsZWN0aW9uIGlzIGluIHRoaXMgc29ydGFibGVcblxuICAgICAgaWYgKG11bHRpRHJhZ1NvcnRhYmxlICE9PSB0aGlzLnNvcnRhYmxlKSByZXR1cm47IC8vIE9ubHkgZGVzZWxlY3QgaWYgdGFyZ2V0IGlzIG5vdCBpdGVtIGluIHRoaXMgc29ydGFibGVcblxuICAgICAgaWYgKGV2dCAmJiBjbG9zZXN0KGV2dC50YXJnZXQsIHRoaXMub3B0aW9ucy5kcmFnZ2FibGUsIHRoaXMuc29ydGFibGUuZWwsIGZhbHNlKSkgcmV0dXJuOyAvLyBPbmx5IGRlc2VsZWN0IGlmIGxlZnQgY2xpY2tcblxuICAgICAgaWYgKGV2dCAmJiBldnQuYnV0dG9uICE9PSAwKSByZXR1cm47XG5cbiAgICAgIHdoaWxlIChtdWx0aURyYWdFbGVtZW50cy5sZW5ndGgpIHtcbiAgICAgICAgdmFyIGVsID0gbXVsdGlEcmFnRWxlbWVudHNbMF07XG4gICAgICAgIHRvZ2dsZUNsYXNzKGVsLCB0aGlzLm9wdGlvbnMuc2VsZWN0ZWRDbGFzcywgZmFsc2UpO1xuICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5zaGlmdCgpO1xuICAgICAgICBkaXNwYXRjaEV2ZW50KHtcbiAgICAgICAgICBzb3J0YWJsZTogdGhpcy5zb3J0YWJsZSxcbiAgICAgICAgICByb290RWw6IHRoaXMuc29ydGFibGUuZWwsXG4gICAgICAgICAgbmFtZTogJ2Rlc2VsZWN0JyxcbiAgICAgICAgICB0YXJnZXRFbDogZWwsXG4gICAgICAgICAgb3JpZ2luYWxFdmVudDogZXZ0XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0sXG4gICAgX2NoZWNrS2V5RG93bjogZnVuY3Rpb24gX2NoZWNrS2V5RG93bihldnQpIHtcbiAgICAgIGlmIChldnQua2V5ID09PSB0aGlzLm9wdGlvbnMubXVsdGlEcmFnS2V5KSB7XG4gICAgICAgIHRoaXMubXVsdGlEcmFnS2V5RG93biA9IHRydWU7XG4gICAgICB9XG4gICAgfSxcbiAgICBfY2hlY2tLZXlVcDogZnVuY3Rpb24gX2NoZWNrS2V5VXAoZXZ0KSB7XG4gICAgICBpZiAoZXZ0LmtleSA9PT0gdGhpcy5vcHRpb25zLm11bHRpRHJhZ0tleSkge1xuICAgICAgICB0aGlzLm11bHRpRHJhZ0tleURvd24gPSBmYWxzZTtcbiAgICAgIH1cbiAgICB9XG4gIH07XG4gIHJldHVybiBfZXh0ZW5kcyhNdWx0aURyYWcsIHtcbiAgICAvLyBTdGF0aWMgbWV0aG9kcyAmIHByb3BlcnRpZXNcbiAgICBwbHVnaW5OYW1lOiAnbXVsdGlEcmFnJyxcbiAgICB1dGlsczoge1xuICAgICAgLyoqXHJcbiAgICAgICAqIFNlbGVjdHMgdGhlIHByb3ZpZGVkIG11bHRpLWRyYWcgaXRlbVxyXG4gICAgICAgKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gZWwgICAgVGhlIGVsZW1lbnQgdG8gYmUgc2VsZWN0ZWRcclxuICAgICAgICovXG4gICAgICBzZWxlY3Q6IGZ1bmN0aW9uIHNlbGVjdChlbCkge1xuICAgICAgICB2YXIgc29ydGFibGUgPSBlbC5wYXJlbnROb2RlW2V4cGFuZG9dO1xuICAgICAgICBpZiAoIXNvcnRhYmxlIHx8ICFzb3J0YWJsZS5vcHRpb25zLm11bHRpRHJhZyB8fCB+bXVsdGlEcmFnRWxlbWVudHMuaW5kZXhPZihlbCkpIHJldHVybjtcblxuICAgICAgICBpZiAobXVsdGlEcmFnU29ydGFibGUgJiYgbXVsdGlEcmFnU29ydGFibGUgIT09IHNvcnRhYmxlKSB7XG4gICAgICAgICAgbXVsdGlEcmFnU29ydGFibGUubXVsdGlEcmFnLl9kZXNlbGVjdE11bHRpRHJhZygpO1xuXG4gICAgICAgICAgbXVsdGlEcmFnU29ydGFibGUgPSBzb3J0YWJsZTtcbiAgICAgICAgfVxuXG4gICAgICAgIHRvZ2dsZUNsYXNzKGVsLCBzb3J0YWJsZS5vcHRpb25zLnNlbGVjdGVkQ2xhc3MsIHRydWUpO1xuICAgICAgICBtdWx0aURyYWdFbGVtZW50cy5wdXNoKGVsKTtcbiAgICAgIH0sXG5cbiAgICAgIC8qKlxyXG4gICAgICAgKiBEZXNlbGVjdHMgdGhlIHByb3ZpZGVkIG11bHRpLWRyYWcgaXRlbVxyXG4gICAgICAgKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gZWwgICAgVGhlIGVsZW1lbnQgdG8gYmUgZGVzZWxlY3RlZFxyXG4gICAgICAgKi9cbiAgICAgIGRlc2VsZWN0OiBmdW5jdGlvbiBkZXNlbGVjdChlbCkge1xuICAgICAgICB2YXIgc29ydGFibGUgPSBlbC5wYXJlbnROb2RlW2V4cGFuZG9dLFxuICAgICAgICAgICAgaW5kZXggPSBtdWx0aURyYWdFbGVtZW50cy5pbmRleE9mKGVsKTtcbiAgICAgICAgaWYgKCFzb3J0YWJsZSB8fCAhc29ydGFibGUub3B0aW9ucy5tdWx0aURyYWcgfHwgIX5pbmRleCkgcmV0dXJuO1xuICAgICAgICB0b2dnbGVDbGFzcyhlbCwgc29ydGFibGUub3B0aW9ucy5zZWxlY3RlZENsYXNzLCBmYWxzZSk7XG4gICAgICAgIG11bHRpRHJhZ0VsZW1lbnRzLnNwbGljZShpbmRleCwgMSk7XG4gICAgICB9XG4gICAgfSxcbiAgICBldmVudFByb3BlcnRpZXM6IGZ1bmN0aW9uIGV2ZW50UHJvcGVydGllcygpIHtcbiAgICAgIHZhciBfdGhpczMgPSB0aGlzO1xuXG4gICAgICB2YXIgb2xkSW5kaWNpZXMgPSBbXSxcbiAgICAgICAgICBuZXdJbmRpY2llcyA9IFtdO1xuICAgICAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCkge1xuICAgICAgICBvbGRJbmRpY2llcy5wdXNoKHtcbiAgICAgICAgICBtdWx0aURyYWdFbGVtZW50OiBtdWx0aURyYWdFbGVtZW50LFxuICAgICAgICAgIGluZGV4OiBtdWx0aURyYWdFbGVtZW50LnNvcnRhYmxlSW5kZXhcbiAgICAgICAgfSk7IC8vIG11bHRpRHJhZ0VsZW1lbnRzIHdpbGwgYWxyZWFkeSBiZSBzb3J0ZWQgaWYgZm9sZGluZ1xuXG4gICAgICAgIHZhciBuZXdJbmRleDtcblxuICAgICAgICBpZiAoZm9sZGluZyAmJiBtdWx0aURyYWdFbGVtZW50ICE9PSBkcmFnRWwkMSkge1xuICAgICAgICAgIG5ld0luZGV4ID0gLTE7XG4gICAgICAgIH0gZWxzZSBpZiAoZm9sZGluZykge1xuICAgICAgICAgIG5ld0luZGV4ID0gaW5kZXgobXVsdGlEcmFnRWxlbWVudCwgJzpub3QoLicgKyBfdGhpczMub3B0aW9ucy5zZWxlY3RlZENsYXNzICsgJyknKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICBuZXdJbmRleCA9IGluZGV4KG11bHRpRHJhZ0VsZW1lbnQpO1xuICAgICAgICB9XG5cbiAgICAgICAgbmV3SW5kaWNpZXMucHVzaCh7XG4gICAgICAgICAgbXVsdGlEcmFnRWxlbWVudDogbXVsdGlEcmFnRWxlbWVudCxcbiAgICAgICAgICBpbmRleDogbmV3SW5kZXhcbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICAgIHJldHVybiB7XG4gICAgICAgIGl0ZW1zOiBfdG9Db25zdW1hYmxlQXJyYXkobXVsdGlEcmFnRWxlbWVudHMpLFxuICAgICAgICBjbG9uZXM6IFtdLmNvbmNhdChtdWx0aURyYWdDbG9uZXMpLFxuICAgICAgICBvbGRJbmRpY2llczogb2xkSW5kaWNpZXMsXG4gICAgICAgIG5ld0luZGljaWVzOiBuZXdJbmRpY2llc1xuICAgICAgfTtcbiAgICB9LFxuICAgIG9wdGlvbkxpc3RlbmVyczoge1xuICAgICAgbXVsdGlEcmFnS2V5OiBmdW5jdGlvbiBtdWx0aURyYWdLZXkoa2V5KSB7XG4gICAgICAgIGtleSA9IGtleS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgICAgIGlmIChrZXkgPT09ICdjdHJsJykge1xuICAgICAgICAgIGtleSA9ICdDb250cm9sJztcbiAgICAgICAgfSBlbHNlIGlmIChrZXkubGVuZ3RoID4gMSkge1xuICAgICAgICAgIGtleSA9IGtleS5jaGFyQXQoMCkudG9VcHBlckNhc2UoKSArIGtleS5zdWJzdHIoMSk7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4ga2V5O1xuICAgICAgfVxuICAgIH1cbiAgfSk7XG59XG5cbmZ1bmN0aW9uIGluc2VydE11bHRpRHJhZ0VsZW1lbnRzKGNsb25lc0luc2VydGVkLCByb290RWwpIHtcbiAgbXVsdGlEcmFnRWxlbWVudHMuZm9yRWFjaChmdW5jdGlvbiAobXVsdGlEcmFnRWxlbWVudCwgaSkge1xuICAgIHZhciB0YXJnZXQgPSByb290RWwuY2hpbGRyZW5bbXVsdGlEcmFnRWxlbWVudC5zb3J0YWJsZUluZGV4ICsgKGNsb25lc0luc2VydGVkID8gTnVtYmVyKGkpIDogMCldO1xuXG4gICAgaWYgKHRhcmdldCkge1xuICAgICAgcm9vdEVsLmluc2VydEJlZm9yZShtdWx0aURyYWdFbGVtZW50LCB0YXJnZXQpO1xuICAgIH0gZWxzZSB7XG4gICAgICByb290RWwuYXBwZW5kQ2hpbGQobXVsdGlEcmFnRWxlbWVudCk7XG4gICAgfVxuICB9KTtcbn1cbi8qKlxyXG4gKiBJbnNlcnQgbXVsdGktZHJhZyBjbG9uZXNcclxuICogQHBhcmFtICB7W0Jvb2xlYW5dfSBlbGVtZW50c0luc2VydGVkICBXaGV0aGVyIHRoZSBtdWx0aS1kcmFnIGVsZW1lbnRzIGFyZSBpbnNlcnRlZFxyXG4gKiBAcGFyYW0gIHtIVE1MRWxlbWVudH0gcm9vdEVsXHJcbiAqL1xuXG5cbmZ1bmN0aW9uIGluc2VydE11bHRpRHJhZ0Nsb25lcyhlbGVtZW50c0luc2VydGVkLCByb290RWwpIHtcbiAgbXVsdGlEcmFnQ2xvbmVzLmZvckVhY2goZnVuY3Rpb24gKGNsb25lLCBpKSB7XG4gICAgdmFyIHRhcmdldCA9IHJvb3RFbC5jaGlsZHJlbltjbG9uZS5zb3J0YWJsZUluZGV4ICsgKGVsZW1lbnRzSW5zZXJ0ZWQgPyBOdW1iZXIoaSkgOiAwKV07XG5cbiAgICBpZiAodGFyZ2V0KSB7XG4gICAgICByb290RWwuaW5zZXJ0QmVmb3JlKGNsb25lLCB0YXJnZXQpO1xuICAgIH0gZWxzZSB7XG4gICAgICByb290RWwuYXBwZW5kQ2hpbGQoY2xvbmUpO1xuICAgIH1cbiAgfSk7XG59XG5cbmZ1bmN0aW9uIHJlbW92ZU11bHRpRHJhZ0VsZW1lbnRzKCkge1xuICBtdWx0aURyYWdFbGVtZW50cy5mb3JFYWNoKGZ1bmN0aW9uIChtdWx0aURyYWdFbGVtZW50KSB7XG4gICAgaWYgKG11bHRpRHJhZ0VsZW1lbnQgPT09IGRyYWdFbCQxKSByZXR1cm47XG4gICAgbXVsdGlEcmFnRWxlbWVudC5wYXJlbnROb2RlICYmIG11bHRpRHJhZ0VsZW1lbnQucGFyZW50Tm9kZS5yZW1vdmVDaGlsZChtdWx0aURyYWdFbGVtZW50KTtcbiAgfSk7XG59XG5cblNvcnRhYmxlLm1vdW50KG5ldyBBdXRvU2Nyb2xsUGx1Z2luKCkpO1xuU29ydGFibGUubW91bnQoUmVtb3ZlLCBSZXZlcnQpO1xuXG5leHBvcnQgZGVmYXVsdCBTb3J0YWJsZTtcbmV4cG9ydCB7IE11bHRpRHJhZ1BsdWdpbiBhcyBNdWx0aURyYWcsIFNvcnRhYmxlLCBTd2FwUGx1Z2luIGFzIFN3YXAgfTtcbiIsICJpbXBvcnQgU29ydGFibGUgZnJvbSAnc29ydGFibGVqcydcblxud2luZG93LlNvcnRhYmxlID0gU29ydGFibGVcblxuZXhwb3J0IGRlZmF1bHQgKEFscGluZSkgPT4ge1xuICAgIEFscGluZS5kaXJlY3RpdmUoJ3NvcnRhYmxlJywgKGVsKSA9PiB7XG4gICAgICAgIGVsLnNvcnRhYmxlID0gU29ydGFibGUuY3JlYXRlKGVsLCB7XG4gICAgICAgICAgICBkcmFnZ2FibGU6ICdbeC1zb3J0YWJsZS1pdGVtXScsXG4gICAgICAgICAgICBoYW5kbGU6ICdbeC1zb3J0YWJsZS1oYW5kbGVdJyxcbiAgICAgICAgICAgIGRhdGFJZEF0dHI6ICd4LXNvcnRhYmxlLWl0ZW0nLFxuICAgICAgICB9KVxuICAgIH0pXG59XG4iLCAiaW1wb3J0IEFscGluZUZsb2F0aW5nVUkgZnJvbSAnQGF3Y29kZXMvYWxwaW5lLWZsb2F0aW5nLXVpJ1xuaW1wb3J0IEFscGluZUxhenlMb2FkQXNzZXRzIGZyb20gJ2FscGluZS1sYXp5LWxvYWQtYXNzZXRzJ1xuaW1wb3J0IEZvY3VzIGZyb20gJ0BhbHBpbmVqcy9mb2N1cydcbmltcG9ydCBTb3J0YWJsZSBmcm9tICcuL3NvcnRhYmxlJ1xuXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdhbHBpbmU6aW5pdCcsICgpID0+IHtcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihGb2N1cylcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihTb3J0YWJsZSlcbiAgICB3aW5kb3cuQWxwaW5lLnBsdWdpbihBbHBpbmVGbG9hdGluZ1VJKVxuICAgIHdpbmRvdy5BbHBpbmUucGx1Z2luKEFscGluZUxhenlMb2FkQXNzZXRzKVxufSlcblxuLy8gaHR0cHM6Ly9naXRodWIuY29tL2xhcmF2ZWwvZnJhbWV3b3JrL2Jsb2IvNTI5OWMyMjMyMWMwZjFlYThmZjc3MGI4NGE2YzY0NjljNGQ2ZWRlYy9zcmMvSWxsdW1pbmF0ZS9UcmFuc2xhdGlvbi9NZXNzYWdlU2VsZWN0b3IucGhwI0wxNVxuY29uc3QgcGx1cmFsaXplID0gZnVuY3Rpb24gKHRleHQsIG51bWJlciwgdmFyaWFibGVzKSB7XG4gICAgZnVuY3Rpb24gZXh0cmFjdChzZWdtZW50cywgbnVtYmVyKSB7XG4gICAgICAgIGZvciAoY29uc3QgcGFydCBvZiBzZWdtZW50cykge1xuICAgICAgICAgICAgY29uc3QgbGluZSA9IGV4dHJhY3RGcm9tU3RyaW5nKHBhcnQsIG51bWJlcilcblxuICAgICAgICAgICAgaWYgKGxpbmUgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gbGluZVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZXh0cmFjdEZyb21TdHJpbmcocGFydCwgbnVtYmVyKSB7XG4gICAgICAgIGNvbnN0IG1hdGNoZXMgPSBwYXJ0Lm1hdGNoKC9eW1xce1xcW10oW15cXFtcXF1cXHtcXH1dKilbXFx9XFxdXSguKikvcylcblxuICAgICAgICBpZiAobWF0Y2hlcyA9PT0gbnVsbCB8fCBtYXRjaGVzLmxlbmd0aCAhPT0gMykge1xuICAgICAgICAgICAgcmV0dXJuIG51bGxcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IGNvbmRpdGlvbiA9IG1hdGNoZXNbMV1cblxuICAgICAgICBjb25zdCB2YWx1ZSA9IG1hdGNoZXNbMl1cblxuICAgICAgICBpZiAoY29uZGl0aW9uLmluY2x1ZGVzKCcsJykpIHtcbiAgICAgICAgICAgIGNvbnN0IFtmcm9tLCB0b10gPSBjb25kaXRpb24uc3BsaXQoJywnLCAyKVxuXG4gICAgICAgICAgICBpZiAodG8gPT09ICcqJyAmJiBudW1iZXIgPj0gZnJvbSkge1xuICAgICAgICAgICAgICAgIHJldHVybiB2YWx1ZVxuICAgICAgICAgICAgfSBlbHNlIGlmIChmcm9tID09PSAnKicgJiYgbnVtYmVyIDw9IHRvKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHZhbHVlXG4gICAgICAgICAgICB9IGVsc2UgaWYgKG51bWJlciA+PSBmcm9tICYmIG51bWJlciA8PSB0bykge1xuICAgICAgICAgICAgICAgIHJldHVybiB2YWx1ZVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIGNvbmRpdGlvbiA9PSBudW1iZXIgPyB2YWx1ZSA6IG51bGxcbiAgICB9XG5cbiAgICBmdW5jdGlvbiB1Y2ZpcnN0KHN0cmluZykge1xuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgc3RyaW5nLnRvU3RyaW5nKCkuY2hhckF0KDApLnRvVXBwZXJDYXNlKCkgK1xuICAgICAgICAgICAgc3RyaW5nLnRvU3RyaW5nKCkuc2xpY2UoMSlcbiAgICAgICAgKVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHJlcGxhY2UobGluZSwgcmVwbGFjZSkge1xuICAgICAgICBpZiAocmVwbGFjZS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIHJldHVybiBsaW5lXG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCBzaG91bGRSZXBsYWNlID0ge31cblxuICAgICAgICBmb3IgKGxldCBba2V5LCB2YWx1ZV0gb2YgT2JqZWN0LmVudHJpZXMocmVwbGFjZSkpIHtcbiAgICAgICAgICAgIHNob3VsZFJlcGxhY2VbJzonICsgdWNmaXJzdChrZXkgPz8gJycpXSA9IHVjZmlyc3QodmFsdWUgPz8gJycpXG4gICAgICAgICAgICBzaG91bGRSZXBsYWNlWyc6JyArIGtleS50b1VwcGVyQ2FzZSgpXSA9IHZhbHVlXG4gICAgICAgICAgICAgICAgLnRvU3RyaW5nKClcbiAgICAgICAgICAgICAgICAudG9VcHBlckNhc2UoKVxuICAgICAgICAgICAgc2hvdWxkUmVwbGFjZVsnOicgKyBrZXldID0gdmFsdWVcbiAgICAgICAgfVxuXG4gICAgICAgIE9iamVjdC5lbnRyaWVzKHNob3VsZFJlcGxhY2UpLmZvckVhY2goKFtrZXksIHZhbHVlXSkgPT4ge1xuICAgICAgICAgICAgbGluZSA9IGxpbmUucmVwbGFjZUFsbChrZXksIHZhbHVlKVxuICAgICAgICB9KVxuXG4gICAgICAgIHJldHVybiBsaW5lXG4gICAgfVxuXG4gICAgZnVuY3Rpb24gc3RyaXBDb25kaXRpb25zKHNlZ21lbnRzKSB7XG4gICAgICAgIHJldHVybiBzZWdtZW50cy5tYXAoKHBhcnQpID0+XG4gICAgICAgICAgICBwYXJ0LnJlcGxhY2UoL15bXFx7XFxbXShbXlxcW1xcXVxce1xcfV0qKVtcXH1cXF1dLywgJycpLFxuICAgICAgICApXG4gICAgfVxuXG4gICAgbGV0IHNlZ21lbnRzID0gdGV4dC5zcGxpdCgnfCcpXG5cbiAgICBjb25zdCB2YWx1ZSA9IGV4dHJhY3Qoc2VnbWVudHMsIG51bWJlcilcblxuICAgIGlmICh2YWx1ZSAhPT0gbnVsbCAmJiB2YWx1ZSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIHJldHVybiByZXBsYWNlKHZhbHVlLnRyaW0oKSwgdmFyaWFibGVzKVxuICAgIH1cblxuICAgIHNlZ21lbnRzID0gc3RyaXBDb25kaXRpb25zKHNlZ21lbnRzKVxuXG4gICAgcmV0dXJuIHJlcGxhY2UoXG4gICAgICAgIHNlZ21lbnRzLmxlbmd0aCA+IDEgJiYgbnVtYmVyID4gMSA/IHNlZ21lbnRzWzFdIDogc2VnbWVudHNbMF0sXG4gICAgICAgIHZhcmlhYmxlcyxcbiAgICApXG59XG5cbndpbmRvdy5wbHVyYWxpemUgPSBwbHVyYWxpemVcbiJdLAogICJtYXBwaW5ncyI6ICI7O0FBQ0EsV0FBUyxRQUFRLFdBQVc7QUFDMUIsV0FBTyxVQUFVLE1BQU0sR0FBRyxFQUFFLENBQUM7QUFBQSxFQUMvQjtBQUNBLFdBQVMsYUFBYSxXQUFXO0FBQy9CLFdBQU8sVUFBVSxNQUFNLEdBQUcsRUFBRSxDQUFDO0FBQUEsRUFDL0I7QUFDQSxXQUFTLHlCQUF5QixXQUFXO0FBQzNDLFdBQU8sQ0FBQyxPQUFPLFFBQVEsRUFBRSxTQUFTLFFBQVEsU0FBUyxDQUFDLElBQUksTUFBTTtBQUFBLEVBQ2hFO0FBQ0EsV0FBUyxrQkFBa0IsTUFBTTtBQUMvQixXQUFPLFNBQVMsTUFBTSxXQUFXO0FBQUEsRUFDbkM7QUFDQSxXQUFTLDJCQUEyQixNQUFNLFdBQVcsS0FBSztBQUN4RCxRQUFJO0FBQUEsTUFDRjtBQUFBLE1BQ0E7QUFBQSxJQUNGLElBQUk7QUFDSixVQUFNLFVBQVUsVUFBVSxJQUFJLFVBQVUsUUFBUSxJQUFJLFNBQVMsUUFB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QUMsSUFBSSxTQUFTLENBQUM7QUFBQSxNQUM1RDtBQUFBLElBQ0o7QUFBQSxFQUNKO0FBRUEsU0FBTyxZQUFZOyIsCiAgIm5hbWVzIjogWyJjc3MiLCAibW9kdWxlX2RlZmF1bHQiLCAic3JjX2RlZmF1bHQiLCAid2l0aGluIiwgIm1vZHVsZV9kZWZhdWx0IiwgIm93bktleXMiLCAiX29iamVjdFNwcmVhZDIiLCAiX2RlZmluZVByb3BlcnR5IiwgIm9iaiIsICJtYXRjaGVzIiwgImluZGV4IiwgIl9vYmplY3RTcHJlYWQyIiwgIm9wdGlvbiIsICJkZWZhdWx0cyIsICJyb290RWwiLCAiY2xvbmVFbCIsICJvbGRJbmRleCIsICJuZXdJbmRleCIsICJvbGREcmFnZ2FibGVJbmRleCIsICJuZXdEcmFnZ2FibGVJbmRleCIsICJwdXRTb3J0YWJsZSIsICJwbHVnaW5FdmVudCIsICJfZGV0ZWN0RGlyZWN0aW9uIiwgIl9kcmFnRWxJblJvd0NvbHVtbiIsICJfZGV0ZWN0TmVhcmVzdEVtcHR5U29ydGFibGUiLCAiX3ByZXBhcmVHcm91cCIsICJkcmFnRWwiLCAiX2hpZGVHaG9zdEZvclRhcmdldCIsICJfdW5oaWRlR2hvc3RGb3JUYXJnZXQiLCAibmVhcmVzdEVtcHR5SW5zZXJ0RGV0ZWN0RXZlbnQiLCAiX2NoZWNrT3V0c2lkZVRhcmdldEVsIiwgImRyYWdTdGFydEZuIiwgInRhcmdldCIsICJhZnRlciIsICJlbCIsICJwbHVnaW5zIiwgImRyb3AiLCAiYXV0b1Njcm9sbCIsICJvblNwaWxsIiwgIm1vZHVsZV9kZWZhdWx0IiwgInNlZ21lbnRzIiwgIm51bWJlciIsICJtYXRjaGVzIiwgInZhbHVlIiwgInJlcGxhY2UiXQp9Cg==
