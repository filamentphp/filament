// packages/notifications/resources/js/components/notification.js
var notification_default = (Alpine) => {
  Alpine.data("notificationComponent", ({
    notification
  }) => ({
    init: function() {
      if (notification.duration !== null) {
        setTimeout(() => this.close(), notification.duration);
      }
    },
    close: function() {
      this.$wire.close(notification.id);
    }
  }));
};

// packages/notifications/resources/js/index.js
var js_default = (Alpine) => {
  Alpine.plugin(notification_default);
};
export {
  notification_default as NotificationComponentAlpinePlugin,
  js_default as default
};
