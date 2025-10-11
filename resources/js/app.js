// resources/js/app.js
import { createApp } from 'vue';
import producto_edit from './components/producto-edit.vue';
import producto_create from './components/producto-create.vue';
import producto_index from './components/producto-index.vue';
import categoria_create from './components/categoria-create.vue';
import categoria_edit from './components/categoria-edit.vue';
import categoria_index from './components/categoria-index.vue';
import authPage from './components/auth-page.vue';
import venta_create from './components/venta-create.vue';
import venta_index from './components/venta-index.vue';


// Importar axios
import axios from 'axios';
window.axios = axios;

// Configuración global de Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

// Token CSRF
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Montar Vue solo si existe el elemento #app
const appElement = document.getElementById('app');
if (appElement && !appElement.__vue_app__) {
  const app = createApp({});

  // Registrar componentes
  app.component('auth-page', authPage);
  app.component('producto-index', producto_index);
  app.component('producto-edit', producto_edit);
  app.component('producto-create', producto_create);
  app.component('categoria-create', categoria_create);
  app.component('categoria-edit', categoria_edit);
  app.component('categoria-index', categoria_index);
  app.component('venta-create', venta_create);
  app.component('venta-index', venta_index);
  

  app.mount('#app');
}