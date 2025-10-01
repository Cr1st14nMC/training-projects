<template>
  <div class="container py-4">
    <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
      {{ alert.message }}
    </div>

    <form @submit.prevent="updateCategories" novalidate>
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

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea
          id="descripcion"
          class="form-control"
          rows="3"
          v-model="form.descripcion"
          placeholder="Descripción de la categoría..."
        ></textarea>
      </div>

      <button type="submit" class="btn btn-warning me-2" :disabled="loading">
        <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        <i class="bi bi-check-circle me-1"> Actualizar </i>
        
      </button>
      <a :href="cancelUrl" class="btn btn-secondary">
        <i class="bi bi-x-circle me-1"></i> Cancelar</a>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'categoria-edit',
  props: {
    category: { type: Object, required: true },
    updateUrl: { type: String, required: true },
    cancelUrl: { type: String, required: true }
  },
  data() {
    return {
      form: {
        nombre: this.category.nombre || '',
        descripcion: this.category.descripcion || ''
      },
      errors: {},
      loading: false,
      alert: { show: false, message: '', type: 'success' }
    };
  },
  mounted() {
    // Debug: verifica que los datos lleguen correctamente
    console.log('Categories data:', this.category);
    console.log('Form initialized:', this.form);
  },
  methods: {
    async updateCategories() {
      this.loading = true;
      this.errors = {};
      this.alert.show = false;
      
      try {
        const res = await axios.put(this.updateUrl, this.form);
        if (res.data && res.data.success) {
          this.alert.show = true;
          this.alert.type = 'success';
          this.alert.message = 'Categoría actualizada correctamente';
          
          setTimeout(() => {
            window.location.href = this.cancelUrl;
          }, 1500);
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
/* estilos personalizados si necesitas */
</style>