import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
// NON avviare Alpine qui - viene avviato in app.js dopo la registrazione dei componenti