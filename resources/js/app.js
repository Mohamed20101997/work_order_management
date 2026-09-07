import Alpine from 'alpinejs';
import { imageUploader } from './image-upload.js';

window.Alpine = Alpine;
Alpine.data('imageUploader', imageUploader);
Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js');
    });
}
