// Загружаем Bootstrap (CommonJS синтаксис)
require('bootstrap');

// Загружаем jQuery
const $ = require('jquery');
window.$ = window.jQuery = $;

// Загружаем Alpine.js (от Breeze)
const Alpine = require('alpinejs');
window.Alpine = Alpine;
Alpine.start();

// Импортируем SASS стили
require('../sass/app.scss');

// Инициализация Bootstrap компонентов
document.addEventListener('DOMContentLoaded', function() {
    // Импортируем bootstrap для доступа к конструкторам
    const bootstrap = require('bootstrap');
    
    // Инициализация всех Bootstrap компонентов
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Инициализация popover
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.forEach(function (popoverTriggerEl) {
        new bootstrap.Popover(popoverTriggerEl);
    });
    
    console.log('Приложение загружено');
    
    // Обработка уведомлений Bootstrap
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-dismissible')) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            }
        });
    }, 1000);
});