/**
 * Notification System
 * - Create and manage notifications
 * - Support different types: success, error, info, warning
 * - Auto-dismiss after timeout
 * - RTL support
 */

class NotificationSystem {
  constructor() {
    this.container = null;
    this.notifications = [];
    this.init();
  }

  init() {
    // Create notification container if it doesn't exist
    if (!document.getElementById('notification-container')) {
      this.container = document.createElement('div');
      this.container.id = 'notification-container';
      this.container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        pointer-events: none;
      `;
      document.body.appendChild(this.container);
    } else {
      this.container = document.getElementById('notification-container');
    }
  }

  show(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.pointerEvents = 'auto';
    
    // Add to container
    this.container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
      notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto dismiss
    const dismissTimer = setTimeout(() => {
      this.dismiss(notification);
    }, duration);
    
    // Store reference
    this.notifications.push({
      element: notification,
      timer: dismissTimer
    });
    
    return notification;
  }

  dismiss(notification) {
    // Find and clear timer
    const index = this.notifications.findIndex(n => n.element === notification);
    if (index !== -1) {
      clearTimeout(this.notifications[index].timer);
      this.notifications.splice(index, 1);
    }
    
    // Add hide class
    notification.classList.add('hide');
    
    // Remove from DOM after animation
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }

  // Convenience methods
  success(message, duration = 5000) {
    return this.show(message, 'success', duration);
  }

  error(message, duration = 7000) {
    return this.show(message, 'error', duration);
  }

  info(message, duration = 5000) {
    return this.show(message, 'info', duration);
  }

  warning(message, duration = 6000) {
    return this.show(message, 'warning', duration);
  }

  // Clear all notifications
  clear() {
    this.notifications.forEach(n => {
      clearTimeout(n.timer);
      this.dismiss(n.element);
    });
  }
}

// Global instance
window.notifications = new NotificationSystem();

// Helper functions for backward compatibility
window.showNotification = function(message, type = 'info', duration = 5000) {
  return window.notifications.show(message, type, duration);
};

window.showSuccess = function(message, duration = 5000) {
  return window.notifications.success(message, duration);
};

window.showError = function(message, duration = 7000) {
  return window.notifications.error(message, duration);
};

window.showInfo = function(message, duration = 5000) {
  return window.notifications.info(message, duration);
};

window.showWarning = function(message, duration = 6000) {
  return window.notifications.warning(message, duration);
};
