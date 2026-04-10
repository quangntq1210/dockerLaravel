import './bootstrap';
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
window.bootstrap = bootstrap?.default ?? bootstrap;
import './helpers/formatTimeAgo.js';
import './helpers/showToast.js';
