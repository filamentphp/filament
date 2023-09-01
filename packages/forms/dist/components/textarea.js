// packages/forms/resources/js/components/textarea.js
function textareaFormComponent({ initialHeight }) {
  return {
    render: function() {
      if (this.$el.scrollHeight > 0) {
        this.$el.style.height = initialHeight + "rem";
        this.$el.style.height = this.$el.scrollHeight + "px";
      }
    }
  };
}
export {
  textareaFormComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvdGV4dGFyZWEuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHRleHRhcmVhRm9ybUNvbXBvbmVudCh7IGluaXRpYWxIZWlnaHQgfSkge1xuICAgIHJldHVybiB7XG4gICAgICAgIHJlbmRlcjogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYgKHRoaXMuJGVsLnNjcm9sbEhlaWdodCA+IDApIHtcbiAgICAgICAgICAgICAgICB0aGlzLiRlbC5zdHlsZS5oZWlnaHQgPSBpbml0aWFsSGVpZ2h0ICsgJ3JlbSdcbiAgICAgICAgICAgICAgICB0aGlzLiRlbC5zdHlsZS5oZWlnaHQgPSB0aGlzLiRlbC5zY3JvbGxIZWlnaHQgKyAncHgnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgfVxufVxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFlLFNBQVIsc0JBQXVDLEVBQUUsY0FBYyxHQUFHO0FBQzdELFNBQU87QUFBQSxJQUNILFFBQVEsV0FBWTtBQUNoQixVQUFJLEtBQUssSUFBSSxlQUFlLEdBQUc7QUFDM0IsYUFBSyxJQUFJLE1BQU0sU0FBUyxnQkFBZ0I7QUFDeEMsYUFBSyxJQUFJLE1BQU0sU0FBUyxLQUFLLElBQUksZUFBZTtBQUFBLE1BQ3BEO0FBQUEsSUFDSjtBQUFBLEVBQ0o7QUFDSjsiLAogICJuYW1lcyI6IFtdCn0K
