<template>
  <div class="container py-4">
    <h1 class="mb-4">Crear Categoría</h1>

    <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
      {{ alert.message }}
    </div>

    <form @submit.prevent="submitForm" novalidate>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input
          id="nombre"
          type="text"
          class="form-control"
          v-model="form.nombre"
          :class="{ 'is-invalid': errors.nombre }"
          placeholder="Ej. Bebidas"
          required
        />
        <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre[0] }}</div>
      </div>

      <button type="submit" class="btn btn-primary me-2" :disabled="loading">
        <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        Guardar
      </button>
      <a :href="cancelUrl" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CategoryCreate',
  props: {
    storeUrl: { type: String, required: true },
    cancelUrl: { type: String, required: true },
  },
  data() {
    return {
      form: {
        nombre: ''
      },
      errors: {},
      loading: false,
      alert: { show: false, message: '', type: 'success' }
    };
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.errors = {};
      try {
        const res = await axios.post(this.storeUrl, this.form);
        // Si la respuesta es JSON con éxito o si el POST redirige, navegamos al index
        if (res.data && res.data.success) {
          window.location.href = this.cancelUrl;
        } else {
          // fallback: redirigir
          window.location.href = this.cancelUrl;
        }
      } catch (err) {
        if (err.response && err.response.status === 422) {
          // Validación de Laravel
          this.errors = err.response.data.errors || {};
        } else {
          this.alert.show = true;
          this.alert.type = 'danger';
          this.alert.message = err?.response?.data?.message || 'Error al guardar la categoría.';
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
