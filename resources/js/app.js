// resources/js/app.js
import { createApp } from 'vue';
import producto_edit from './components/producto-edit.vue';
import producto_create from './components/producto-create.vue';
import producto_index from './components/producto-index.vue';

import categoria_create from './components/categoria-create.vue';
import categoria_edit from './components/categoria-edit.vue';
import categoria_index from './components/categoria-index.vue';

import authPage from './components/auth-page.vue';

import inventario_index from './components/inventario-index.vue';
import inventario_movimientos from './components/inventario-movimientos.vue';

import venta_create from './components/venta-create.vue';
import venta_index from './components/venta-index.vue';

import compra_index from './components/compra-index.vue';
import compra_create from './components/compra-create.vue';

import proveedor_index from './components/proveedor-index.vue';
import proveedor_create from './components/proveedor-create.vue';
import proveedor_edit from './components/proveedor-edit.vue';


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

  app.component('inventario-index', inventario_index);
  app.component('inventario-movimientos', inventario_movimientos);

  app.component('producto-index', producto_index);
  app.component('producto-edit', producto_edit);
  app.component('producto-create', producto_create);

  app.component('categoria-create', categoria_create);
  app.component('categoria-edit', categoria_edit);
  app.component('categoria-index', categoria_index);

  app.component('venta-create', venta_create);
  app.component('venta-index', venta_index);

  app.component('compra-index', compra_index);
  app.component('compra-create', compra_create);

  app.component('proveedor-index', proveedor_index);
  app.component('proveedor-create', proveedor_create);
  app.component('proveedor-edit', proveedor_edit);
  

  app.mount('#app');
}