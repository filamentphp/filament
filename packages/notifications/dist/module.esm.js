// packages/notifications/resources/js/components/notification.js
var notification_default = (Alpine) => {
  Alpine.data("notificationComponent", ({$wire, notification}) => ({
    isEntering: true,
    isClosing: false,
    computedStyle: null,
    top: null,
    init: function() {
      this.computedStyle = window.getComputedStyle(this.$el);
      this.$nextTick(() => this.isEntering = false);
      if (notification.duration !== null) {
        setTimeout(() => this.close(), notification.duration);
      }
      Livewire.hook("message.received", (_, component) => {
        if (component.fingerprint.name !== "notifications") {
          return;
        }
        this.top = this.getTop();
      });
      Livewire.hook("message.processed", (_, component) => {
        if (component.fingerprint.name !== "notifications") {
          return;
        }
        if (this.isClosing) {
          return;
        }
        this.animate();
      });
    },
    animate: function() {
      const top = this.getTop();
      this.$el.animate([
        {transform: `translateY(${this.top - top}px)`},
        {transform: "translateY(0px)"}
      ], {
        duration: this.getTransitionDuration(),
        easing: this.computedStyle.transitionTimingFunction
      });
      this.top = top;
    },
    close: function() {
      this.isClosing = true;
      setTimeout(() => $wire.close(notification.id), this.getTransitionDuration());
    },
    getTop: function() {
      return this.$el.getBoundingClientRect().top;
    },
    getTransitionDuration: function() {
      return parseFloat(this.computedStyle.transitionDuration) * 1e3;
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
