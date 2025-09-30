// resources/js/app.js
import { createApp } from 'vue';
import ProductoEdit from './components/ProductoEdit.vue';
import ProductoCreate from './components/ProductoCreate.vue';
import producto_index from './components/producto-index.vue';
import CategoriaCreate from './components/CategoriaCreate.vue';
import CategoriaEdit from './components/CategoriaEdit.vue';
import CategoriaIndex from './components/CategoriaIndex.vue';

// Importar axios
import axios from 'axios';
window.axios = axios;

// Configuración global de Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Token CSRF
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Montar la app Vue
const app = createApp({});
app.component('producto-index', producto_index);
app.component('ProductoEdit', ProductoEdit);
app.component('ProductoCreate', ProductoCreate);
app.component('CategoriaCreate', CategoriaCreate);
app.component('CategoriaEdit', CategoriaEdit);
app.component('CategoriaIndex', CategoriaIndex);
app.mount('#app');