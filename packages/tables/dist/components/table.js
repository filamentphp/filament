// packages/tables/resources/js/components/table.js
function table() {
  return {
    collapsedGroups: [],
    isLoading: false,
    selectedRecords: [],
    shouldCheckUniqueSelection: true,
    init: function() {
      this.$wire.$on(
        "deselectAllTableRecords",
        () => this.deselectAllRecords()
      );
      this.$watch("selectedRecords", () => {
        if (!this.shouldCheckUniqueSelection) {
          this.shouldCheckUniqueSelection = true;
          return;
        }
        this.selectedRecords = [...new Set(this.selectedRecords)];
        this.shouldCheckUniqueSelection = false;
      });
    },
    mountBulkAction: function(name) {
      this.$wire.set("selectedTableRecords", this.selectedRecords, false);
      this.$wire.mountTableBulkAction(name);
    },
    toggleSelectRecordsOnPage: function() {
      const keys = this.getRecordsOnPage();
      if (this.areRecordsSelected(keys)) {
        this.deselectRecords(keys);
        return;
      }
      this.selectRecords(keys);
    },
    toggleSelectRecordsInGroup: async function(group) {
      this.isLoading = true;
      if (this.areRecordsSelected(this.getRecordsInGroupOnPage(group))) {
        this.deselectRecords(await this.$wire.getGroupedSelectableTableRecordKeys(group));
        return;
      }
      this.selectedRecords = await this.$wire.getGroupedSelectableTableRecordKeys(group);
      this.isLoading = false;
    },
    getRecordsInGroupOnPage: function(group) {
      const keys = [];
      for (let checkbox of this.$root.getElementsByClassName("fi-ta-record-checkbox")) {
        if (checkbox.dataset.group !== group) {
          continue;
        }
        keys.push(checkbox.value);
      }
      return keys;
    },
    getRecordsOnPage: function() {
      const keys = [];
      for (let checkbox of this.$root.getElementsByClassName("fi-ta-record-checkbox")) {
        keys.push(checkbox.value);
      }
      return keys;
    },
    selectRecords: function(keys) {
      for (let key of keys) {
        if (this.isRecordSelected(key)) {
          continue;
        }
        this.selectedRecords.push(key);
      }
    },
    deselectRecords: function(keys) {
      for (let key of keys) {
        let index = this.selectedRecords.indexOf(key);
        if (index === -1) {
          continue;
        }
        this.selectedRecords.splice(index, 1);
      }
    },
    selectAllRecords: async function() {
      this.isLoading = true;
      this.selectedRecords = await this.$wire.getAllSelectableTableRecordKeys();
      this.isLoading = false;
    },
    deselectAllRecords: function() {
      this.selectedRecords = [];
    },
    isRecordSelected: function(key) {
      return this.selectedRecords.includes(key);
    },
    areRecordsSelected: function(keys) {
      return keys.every((key) => this.isRecordSelected(key));
    },
    toggleCollapseGroup: function(group) {
      if (this.isGroupCollapsed(group)) {
        this.collapsedGroups.splice(this.collapsedGroups.indexOf(group), 1);
        return;
      }
      this.collapsedGroups.push(group);
    },
    isGroupCollapsed: function(group) {
      return this.collapsedGroups.includes(group);
    },
    resetCollapsedGroups: function() {
      this.collapsedGroups = [];
    }
  };
}
export {
  table as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvdGFibGUuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHRhYmxlKCkge1xuICAgIHJldHVybiB7XG4gICAgICAgIGNvbGxhcHNlZEdyb3VwczogW10sXG5cbiAgICAgICAgaXNMb2FkaW5nOiBmYWxzZSxcblxuICAgICAgICBzZWxlY3RlZFJlY29yZHM6IFtdLFxuXG4gICAgICAgIHNob3VsZENoZWNrVW5pcXVlU2VsZWN0aW9uOiB0cnVlLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRoaXMuJHdpcmUuJG9uKCdkZXNlbGVjdEFsbFRhYmxlUmVjb3JkcycsICgpID0+XG4gICAgICAgICAgICAgICAgdGhpcy5kZXNlbGVjdEFsbFJlY29yZHMoKSxcbiAgICAgICAgICAgIClcblxuICAgICAgICAgICAgdGhpcy4kd2F0Y2goJ3NlbGVjdGVkUmVjb3JkcycsICgpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoISB0aGlzLnNob3VsZENoZWNrVW5pcXVlU2VsZWN0aW9uKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2hvdWxkQ2hlY2tVbmlxdWVTZWxlY3Rpb24gPSB0cnVlXG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3RlZFJlY29yZHMgPSBbLi4ubmV3IFNldCh0aGlzLnNlbGVjdGVkUmVjb3JkcyldXG5cbiAgICAgICAgICAgICAgICB0aGlzLnNob3VsZENoZWNrVW5pcXVlU2VsZWN0aW9uID0gZmFsc2VcbiAgICAgICAgICAgIH0pXG4gICAgICAgIH0sXG5cbiAgICAgICAgbW91bnRCdWxrQWN0aW9uOiBmdW5jdGlvbiAobmFtZSkge1xuICAgICAgICAgICAgdGhpcy4kd2lyZS5zZXQoJ3NlbGVjdGVkVGFibGVSZWNvcmRzJywgdGhpcy5zZWxlY3RlZFJlY29yZHMsIGZhbHNlKVxuICAgICAgICAgICAgdGhpcy4kd2lyZS5tb3VudFRhYmxlQnVsa0FjdGlvbihuYW1lKVxuICAgICAgICB9LFxuXG4gICAgICAgIHRvZ2dsZVNlbGVjdFJlY29yZHNPblBhZ2U6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGNvbnN0IGtleXMgPSB0aGlzLmdldFJlY29yZHNPblBhZ2UoKVxuXG4gICAgICAgICAgICBpZiAodGhpcy5hcmVSZWNvcmRzU2VsZWN0ZWQoa2V5cykpIHtcbiAgICAgICAgICAgICAgICB0aGlzLmRlc2VsZWN0UmVjb3JkcyhrZXlzKVxuXG4gICAgICAgICAgICAgICAgcmV0dXJuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHRoaXMuc2VsZWN0UmVjb3JkcyhrZXlzKVxuICAgICAgICB9LFxuXG4gICAgICAgIHRvZ2dsZVNlbGVjdFJlY29yZHNJbkdyb3VwOiBhc3luYyBmdW5jdGlvbiAoZ3JvdXApIHtcbiAgICAgICAgICAgIHRoaXMuaXNMb2FkaW5nID0gdHJ1ZVxuXG4gICAgICAgICAgICBpZiAodGhpcy5hcmVSZWNvcmRzU2VsZWN0ZWQodGhpcy5nZXRSZWNvcmRzSW5Hcm91cE9uUGFnZShncm91cCkpKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5kZXNlbGVjdFJlY29yZHMoYXdhaXQgdGhpcy4kd2lyZS5nZXRHcm91cGVkU2VsZWN0YWJsZVRhYmxlUmVjb3JkS2V5cyhncm91cCkpXG5cbiAgICAgICAgICAgICAgICByZXR1cm5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdGhpcy5zZWxlY3RlZFJlY29yZHMgPSBhd2FpdCB0aGlzLiR3aXJlLmdldEdyb3VwZWRTZWxlY3RhYmxlVGFibGVSZWNvcmRLZXlzKGdyb3VwKVxuXG4gICAgICAgICAgICB0aGlzLmlzTG9hZGluZyA9IGZhbHNlXG4gICAgICAgIH0sXG5cbiAgICAgICAgZ2V0UmVjb3Jkc0luR3JvdXBPblBhZ2U6IGZ1bmN0aW9uIChncm91cCkge1xuICAgICAgICAgICAgY29uc3Qga2V5cyA9IFtdXG5cbiAgICAgICAgICAgIGZvciAobGV0IGNoZWNrYm94IG9mIHRoaXMuJHJvb3QuZ2V0RWxlbWVudHNCeUNsYXNzTmFtZSgnZmktdGEtcmVjb3JkLWNoZWNrYm94JykpIHtcbiAgICAgICAgICAgICAgICBpZiAoY2hlY2tib3guZGF0YXNldC5ncm91cCAhPT0gZ3JvdXApIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGludWVcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBrZXlzLnB1c2goY2hlY2tib3gudmFsdWUpXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBrZXlzXG4gICAgICAgIH0sXG5cbiAgICAgICAgZ2V0UmVjb3Jkc09uUGFnZTogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgY29uc3Qga2V5cyA9IFtdXG5cbiAgICAgICAgICAgIGZvciAobGV0IGNoZWNrYm94IG9mIHRoaXMuJHJvb3QuZ2V0RWxlbWVudHNCeUNsYXNzTmFtZSgnZmktdGEtcmVjb3JkLWNoZWNrYm94JykpIHtcbiAgICAgICAgICAgICAgICBrZXlzLnB1c2goY2hlY2tib3gudmFsdWUpXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBrZXlzXG4gICAgICAgIH0sXG5cbiAgICAgICAgc2VsZWN0UmVjb3JkczogZnVuY3Rpb24gKGtleXMpIHtcbiAgICAgICAgICAgIGZvciAobGV0IGtleSBvZiBrZXlzKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuaXNSZWNvcmRTZWxlY3RlZChrZXkpKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3RlZFJlY29yZHMucHVzaChrZXkpXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICAgICAgZGVzZWxlY3RSZWNvcmRzOiBmdW5jdGlvbiAoa2V5cykge1xuICAgICAgICAgICAgZm9yIChsZXQga2V5IG9mIGtleXMpIHtcbiAgICAgICAgICAgICAgICBsZXQgaW5kZXggPSB0aGlzLnNlbGVjdGVkUmVjb3Jkcy5pbmRleE9mKGtleSlcblxuICAgICAgICAgICAgICAgIGlmIChpbmRleCA9PT0gLTEpIHtcbiAgICAgICAgICAgICAgICAgICAgY29udGludWVcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLnNlbGVjdGVkUmVjb3Jkcy5zcGxpY2UoaW5kZXgsIDEpXG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICAgICAgc2VsZWN0QWxsUmVjb3JkczogYXN5bmMgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdGhpcy5pc0xvYWRpbmcgPSB0cnVlXG5cbiAgICAgICAgICAgIHRoaXMuc2VsZWN0ZWRSZWNvcmRzID0gYXdhaXQgdGhpcy4kd2lyZS5nZXRBbGxTZWxlY3RhYmxlVGFibGVSZWNvcmRLZXlzKClcblxuICAgICAgICAgICAgdGhpcy5pc0xvYWRpbmcgPSBmYWxzZVxuICAgICAgICB9LFxuXG4gICAgICAgIGRlc2VsZWN0QWxsUmVjb3JkczogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdGhpcy5zZWxlY3RlZFJlY29yZHMgPSBbXVxuICAgICAgICB9LFxuXG4gICAgICAgIGlzUmVjb3JkU2VsZWN0ZWQ6IGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLnNlbGVjdGVkUmVjb3Jkcy5pbmNsdWRlcyhrZXkpXG4gICAgICAgIH0sXG5cbiAgICAgICAgYXJlUmVjb3Jkc1NlbGVjdGVkOiBmdW5jdGlvbiAoa2V5cykge1xuICAgICAgICAgICAgcmV0dXJuIGtleXMuZXZlcnkoKGtleSkgPT4gdGhpcy5pc1JlY29yZFNlbGVjdGVkKGtleSkpXG4gICAgICAgIH0sXG5cbiAgICAgICAgdG9nZ2xlQ29sbGFwc2VHcm91cDogZnVuY3Rpb24gKGdyb3VwKSB7XG4gICAgICAgICAgICBpZiAodGhpcy5pc0dyb3VwQ29sbGFwc2VkKGdyb3VwKSkge1xuICAgICAgICAgICAgICAgIHRoaXMuY29sbGFwc2VkR3JvdXBzLnNwbGljZSh0aGlzLmNvbGxhcHNlZEdyb3Vwcy5pbmRleE9mKGdyb3VwKSwgMSlcblxuICAgICAgICAgICAgICAgIHJldHVyblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLmNvbGxhcHNlZEdyb3Vwcy5wdXNoKGdyb3VwKVxuICAgICAgICB9LFxuXG4gICAgICAgIGlzR3JvdXBDb2xsYXBzZWQ6IGZ1bmN0aW9uIChncm91cCkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuY29sbGFwc2VkR3JvdXBzLmluY2x1ZGVzKGdyb3VwKVxuICAgICAgICB9LFxuXG4gICAgICAgIHJlc2V0Q29sbGFwc2VkR3JvdXBzOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0aGlzLmNvbGxhcHNlZEdyb3VwcyA9IFtdXG4gICAgICAgIH0sXG4gICAgfVxuIH1cbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBZSxTQUFSLFFBQXlCO0FBQzVCLFNBQU87QUFBQSxJQUNILGlCQUFpQixDQUFDO0FBQUEsSUFFbEIsV0FBVztBQUFBLElBRVgsaUJBQWlCLENBQUM7QUFBQSxJQUVsQiw0QkFBNEI7QUFBQSxJQUU1QixNQUFNLFdBQVk7QUFDZCxXQUFLLE1BQU07QUFBQSxRQUFJO0FBQUEsUUFBMkIsTUFDdEMsS0FBSyxtQkFBbUI7QUFBQSxNQUM1QjtBQUVBLFdBQUssT0FBTyxtQkFBbUIsTUFBTTtBQUNqQyxZQUFJLENBQUUsS0FBSyw0QkFBNEI7QUFDbkMsZUFBSyw2QkFBNkI7QUFFbEM7QUFBQSxRQUNKO0FBRUEsYUFBSyxrQkFBa0IsQ0FBQyxHQUFHLElBQUksSUFBSSxLQUFLLGVBQWUsQ0FBQztBQUV4RCxhQUFLLDZCQUE2QjtBQUFBLE1BQ3RDLENBQUM7QUFBQSxJQUNMO0FBQUEsSUFFQSxpQkFBaUIsU0FBVSxNQUFNO0FBQzdC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
