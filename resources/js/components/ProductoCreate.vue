<template>
  <div class="container py-4">
    <h1 class="mb-4">Agregar Producto</h1>

    <form @submit.prevent="submitForm">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input
          type="text"
          v-model="form.nombre"
          class="form-control"
          placeholder="Coca..."
          required
        />
      </div>

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea
          v-model="form.descripcion"
          class="form-control"
          placeholder="600 ml..."
        ></textarea>
      </div>

      <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input
          type="number"
          v-model="form.precio"
          class="form-control"
          placeholder="19"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary me-2">Guardar</button>
      <a :href="cancelUrl" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "AddProduct",
  props: {
    storeUrl: { type: String, required: true },
    cancelUrl: { type: String, required: true },
  },
  data() {
    return {
      form: {
        nombre: "",
        descripcion: "",
        precio: "",
      },
    };
  },
  methods: {
    async submitForm() {
      try {
        await axios.post(this.storeUrl, this.form);
        window.location.href = this.cancelUrl; // redirige al index
      } catch (error) {
        console.error(error);
        alert("Hubo un error al guardar el producto.");
      }
    },
  },
};
</script>
