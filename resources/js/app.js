// import { createApp } from 'vue';
// import ExampleComponent from './components/ExampleComponent.vue';

// const app = createApp({});
// app.component('example-component', ExampleComponent);

// app.mount('#app');


// import { createApp } from 'vue'; // Vue 3
// import edit from './components/edit.vue';
// import create from './components/create.vue';


// const app = createApp({});
// app.component('index', index);
// app.component('edit', edit);
// app.component('create', create);
// app.mount('#app');

// resources/js/app.js
import { createApp } from 'vue';
import Index from './components/Index.vue';
import Edit from './components/Edit.vue';
import Create from './components/Create.vue';

// 👈 importamos axios
import axios from 'axios';  
window.axios = axios;

// Configuración global de Axios
// Configuración global de Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';


// Si quieres incluir el token CSRF automáticamente
let token = document.querySelector('meta[name="csrf-token"]');
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// si usas api.php
axios.get('productos')
  .then(res => {
    // en la versión del controlador que devolvía { success: true, data: [...] }
    const productos = res.data.data ?? res.data;
    console.log(productos);
  })
  .catch(err => console.error(err));


// montar la app
const app = createApp({});
app.component('Index', Index);
app.component('Edit', Edit);
app.component('Create', Create);
app.mount('#app');