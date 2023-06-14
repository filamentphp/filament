// packages/forms/resources/js/components/tags-input.js
function tagsInputFormComponent({ state }) {
  return {
    newTag: "",
    state,
    createTag: function() {
      this.newTag = this.newTag.trim();
      if (this.newTag === "") {
        return;
      }
      if (this.state.includes(this.newTag)) {
        this.newTag = "";
        return;
      }
      this.state.push(this.newTag);
      this.newTag = "";
    },
    deleteTag: function(tagToDelete) {
      this.state = this.state.filter((tag) => tag !== tagToDelete);
    }
  };
}
export {
  tagsInputFormComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvdGFncy1pbnB1dC5qcyJdLAogICJzb3VyY2VzQ29udGVudCI6IFsiZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gdGFnc0lucHV0Rm9ybUNvbXBvbmVudCh7IHN0YXRlIH0pIHtcbiAgICByZXR1cm4ge1xuICAgICAgICBuZXdUYWc6ICcnLFxuXG4gICAgICAgIHN0YXRlLFxuXG4gICAgICAgIGNyZWF0ZVRhZzogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdGhpcy5uZXdUYWcgPSB0aGlzLm5ld1RhZy50cmltKClcblxuICAgICAgICAgICAgaWYgKHRoaXMubmV3VGFnID09PSAnJykge1xuICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAodGhpcy5zdGF0ZS5pbmNsdWRlcyh0aGlzLm5ld1RhZykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLm5ld1RhZyA9ICcnXG5cbiAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5zdGF0ZS5wdXNoKHRoaXMubmV3VGFnKVxuXG4gICAgICAgICAgICB0aGlzLm5ld1RhZyA9ICcnXG4gICAgICAgIH0sXG5cbiAgICAgICAgZGVsZXRlVGFnOiBmdW5jdGlvbiAodGFnVG9EZWxldGUpIHtcbiAgICAgICAgICAgIHRoaXMuc3RhdGUgPSB0aGlzLnN0YXRlLmZpbHRlcigodGFnKSA9PiB0YWcgIT09IHRhZ1RvRGVsZXRlKVxuICAgICAgICB9LFxuICAgIH1cbn1cbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBZSxTQUFSLHVCQUF3QyxFQUFFLE1BQU0sR0FBRztBQUN0RCxTQUFPO0FBQUEsSUFDSCxRQUFRO0FBQUEsSUFFUjtBQUFBLElBRUEsV0FBVyxXQUFZO0FBQ25CLFdBQUssU0FBUyxLQUFLLE9BQU8sS0FBSztBQUUvQixVQUFJLEtBQUssV0FBVyxJQUFJO0FBQ3BCO0FBQUEsTUFDSjtBQUVBLFVBQUksS0FBSyxNQUFNLFNBQVMsS0FBSyxNQUFNLEdBQUc7QUFDbEMsYUFBSyxTQUFTO0FBRWQ7QUFBQSxNQUNKO0FBRUEsV0FBSyxNQUFNLEtBQUssS0FBSyxNQUFNO0FBRTNCLFdBQUssU0FBUztBQUFBLElBQ2xCO0FBQUEsSUFFQSxXQUFXLFNBQVUsYUFBYTtBQUM5QixXQUFLLFFBQVEsS0FBSyxNQUFNLE9BQU8sQ0FBQyxRQUFRLFFBQVEsV0FBVztBQUFBLElBQy9EO0FBQUEsRUFDSjtBQUNKOyIsCiAgIm5hbWVzIjogW10KfQo=
