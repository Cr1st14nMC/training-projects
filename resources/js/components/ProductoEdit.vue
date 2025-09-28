<template>
  <div class="container py-4">
    <h1 class="mb-4">Editar Producto</h1>

    <form @submit.prevent="updateProduct">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input
          type="text"
          id="nombre"
          class="form-control"
          v-model="form.nombre"
          required
        />
      </div>

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea
          id="descripcion"
          class="form-control"
          v-model="form.descripcion"
        ></textarea>
      </div>

      <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input
          type="number"
          step="0.01"
          id="precio"
          class="form-control"
          v-model="form.precio"
          required
        />
      </div>

      <button type="submit" class="btn btn-warning me-2">Actualizar</button>
      <button type="button" @click="goBack" class="btn btn-secondary">Cancelar</button>
    </form>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import axios from 'axios'

// Recibe props (por ejemplo desde Blade al renderizar la página)
const props = defineProps({
  producto: {
    type: Object,
    required: true
  }
})

// Creamos el formulario con datos iniciales del producto
const form = reactive({
  nombre: props.producto.nombre || '',
  descripcion: props.producto.descripcion || '',
  precio: props.producto.precio || 0
})

// Método para actualizar producto
const updateProduct = async () => {
  try {
    await axios.put(`/productos/${props.producto.id}`, form)
    alert('Producto actualizado correctamente')
    window.location.href = '/productos' // Redirige al index
  } catch (error) {
    console.error(error)
    alert('Hubo un error al actualizar el producto')
  }
}

// Botón cancelar
const goBack = () => {
  window.location.href = '/productos'
}
</script>
