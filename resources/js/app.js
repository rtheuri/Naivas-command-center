import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Global helper for showing quick toast notifications
window.notifyToast = function(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast-notify', {
        detail: { message, type }
    }));
};

Alpine.start();
