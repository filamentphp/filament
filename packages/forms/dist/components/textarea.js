// packages/forms/resources/js/components/textarea.js
function textareaFormComponent({ initialHeight }) {
  return {
    init: function() {
      this.render();
    },
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvdGV4dGFyZWEuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHRleHRhcmVhRm9ybUNvbXBvbmVudCh7IGluaXRpYWxIZWlnaHQgfSkge1xuICAgIHJldHVybiB7XG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRoaXMucmVuZGVyKClcbiAgICAgICAgfSxcblxuICAgICAgICByZW5kZXI6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLiRlbC5zY3JvbGxIZWlnaHQgPiAwKSB7XG4gICAgICAgICAgICAgICAgdGhpcy4kZWwuc3R5bGUuaGVpZ2h0ID0gaW5pdGlhbEhlaWdodCArICdyZW0nXG4gICAgICAgICAgICAgICAgdGhpcy4kZWwuc3R5bGUuaGVpZ2h0ID0gdGhpcy4kZWwuc2Nyb2xsSGVpZ2h0ICsgJ3B4J1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgIH1cbn1cbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBZSxTQUFSLHNCQUF1QyxFQUFFLGNBQWMsR0FBRztBQUM3RCxTQUFPO0FBQUEsSUFDSCxNQUFNLFdBQVk7QUFDZCxXQUFLLE9BQU87QUFBQSxJQUNoQjtBQUFBLElBRUEsUUFBUSxXQUFZO0FBQ2hCLFVBQUksS0FBSyxJQUFJLGVBQWUsR0FBRztBQUMzQixhQUFLLElBQUksTUFBTSxTQUFTLGdCQUFnQjtBQUN4QyxhQUFLLElBQUksTUFBTSxTQUFTLEtBQUssSUFBSSxlQUFlO0FBQUEsTUFDcEQ7QUFBQSxJQUNKO0FBQUEsRUFDSjtBQUNKOyIsCiAgIm5hbWVzIjogW10KfQo=
