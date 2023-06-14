// packages/forms/resources/js/components/textarea.js
function textareaFormComponent() {
  return {
    init: function() {
      this.$nextTick(() => {
        this.render();
      });
    },
    render: function() {
      if (this.$el.scrollHeight > 0) {
        this.$el.style.height = "150px";
        this.$el.style.height = this.$el.scrollHeight + 2 + "px";
      }
    }
  };
}
export {
  textareaFormComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvdGV4dGFyZWEuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHRleHRhcmVhRm9ybUNvbXBvbmVudCgpIHtcbiAgICByZXR1cm4ge1xuICAgICAgICBpbml0OiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLiRuZXh0VGljaygoKSA9PiB7XG4gICAgICAgICAgICAgICAgdGhpcy5yZW5kZXIoKVxuICAgICAgICAgICAgfSlcbiAgICAgICAgfSxcblxuICAgICAgICByZW5kZXI6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLiRlbC5zY3JvbGxIZWlnaHQgPiAwKSB7XG4gICAgICAgICAgICAgICAgdGhpcy4kZWwuc3R5bGUuaGVpZ2h0ID0gJzE1MHB4J1xuICAgICAgICAgICAgICAgIHRoaXMuJGVsLnN0eWxlLmhlaWdodCA9IHRoaXMuJGVsLnNjcm9sbEhlaWdodCArIDIgKyAncHgnXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG4gICAgfVxufVxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFlLFNBQVIsd0JBQXlDO0FBQzVDLFNBQU87QUFBQSxJQUNILE1BQU0sV0FBWTtBQUNkLFdBQUssVUFBVSxNQUFNO0FBQ2pCLGFBQUssT0FBTztBQUFBLE1BQ2hCLENBQUM7QUFBQSxJQUNMO0FBQUEsSUFFQSxRQUFRLFdBQVk7QUFDaEIsVUFBSSxLQUFLLElBQUksZUFBZSxHQUFHO0FBQzNCLGFBQUssSUFBSSxNQUFNLFNBQVM7QUFDeEIsYUFBSyxJQUFJLE1BQU0sU0FBUyxLQUFLLElBQUksZUFBZSxJQUFJO0FBQUEsTUFDeEQ7QUFBQSxJQUNKO0FBQUEsRUFDSjtBQUNKOyIsCiAgIm5hbWVzIjogW10KfQo=
