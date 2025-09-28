<template>
  <div class="container py-4">
    <h1 class="mb-4">Editar Categoría</h1>

    <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
      {{ alert.message }}
    </div>

    <form @submit.prevent="updateCategory" novalidate>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input
          id="nombre"
          type="text"
          class="form-control"
          v-model="form.nombre"
          :class="{ 'is-invalid': errors.nombre }"
          required
        />
        <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre[0] }}</div>
      </div>

      <button type="submit" class="btn btn-warning me-2" :disabled="loading">
        <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        Actualizar
      </button>
      <a :href="cancelUrl" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
</template>

<script>
import { reactive } from 'vue';
import axios from 'axios';

export default {
  name: 'CategoryEdit',
  props: {
    category: { type: Object, required: true }, // objeto categoría pasado desde Blade
    updateUrl: { type: String, required: true }, // ruta PUT
    cancelUrl: { type: String, required: true }
  },
  data() {
    return {
      form: reactive({
        nombre: this.category.nombre ?? ''
      }),
      errors: {},
      loading: false,
      alert: { show: false, message: '', type: 'success' }
    };
  },
  methods: {
    async updateCategory() {
      this.loading = true;
      this.errors = {};
      try {
        const res = await axios.put(this.updateUrl, this.form);
        if (res.data && res.data.success) {
          window.location.href = this.cancelUrl;
        } else {
          window.location.href = this.cancelUrl;
        }
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.errors = err.response.data.errors || {};
        } else {
          this.alert.show = true;
          this.alert.type = 'danger';
          this.alert.message = err?.response?.data?.message || 'Error al actualizar la categoría.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
/* estilos si quieres */
</style>
