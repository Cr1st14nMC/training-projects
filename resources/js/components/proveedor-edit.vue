<template>
  <div class="container py-4">
    <div class="card">
      <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Proveedor</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="actualizarProveedor">
          <div v-if="alert.show" :class="`alert alert-${alert.type}`" role="alert">
            {{ alert.message }}
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nombre" class="form-label">Nombre de Contacto</label>
              <input 
                type="text" 
                id="nombre"
                class="form-control" 
                v-model="form.nombre" 
                :class="{ 'is-invalid': errors.nombre }"
                required
              >
              <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre[0] }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="empresa" class="form-label">Empresa</label>
              <input 
                type="text" 
                id="empresa"
                class="form-control" 
                v-model="form.empresa"
                :class="{ 'is-invalid': errors.empresa }"
              >
              <div v-if="errors.empresa" class="invalid-feedback">{{ errors.empresa[0] }}</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input 
                type="email" 
                id="email"
                class="form-control" 
                v-model="form.email"
                :class="{ 'is-invalid': errors.email }"
              >
              <div v-if="errors.email" class="invalid-feedback">{{ errors.email[0] }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="telefono" class="form-label">Teléfono</label>
              <input 
                type="tel" 
                id="telefono"
                class="form-control" 
                v-model="form.telefono"
                :class="{ 'is-invalid': errors.telefono }"
              >
              <div v-if="errors.telefono" class="invalid-feedback">{{ errors.telefono[0] }}</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="rfc" class="form-label">RFC</label>
              <input 
                type="text" 
                id="rfc"
                class="form-control" 
                v-model="form.rfc"
                :class="{ 'is-invalid': errors.rfc }"
                maxlength="13"
              >
              <div v-if="errors.rfc" class="invalid-feedback">{{ errors.rfc[0] }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="activo" class="form-label">Estado</label>
              <select id="activo" class="form-select" v-model="form.activo">
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea 
              id="direccion"
              class="form-control" 
              v-model="form.direccion" 
              :class="{ 'is-invalid': errors.direccion }"
              rows="3"
            ></textarea>
            <div v-if="errors.direccion" class="invalid-feedback">{{ errors.direccion[0] }}</div>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a :href="cancelUrl" class="btn btn-secondary">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-warning" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-check-circle"></i>
              {{ loading ? 'Actualizando...' : 'Actualizar Proveedor' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'proveedor-edit',
  props: {
    proveedor: { type: Object, required: true },
    updateUrl: { type: String, required: true },
    cancelUrl: { type: String, required: true }
  },
  data() {
    return {
      // Inicializamos el formulario directamente con los datos del prop
      form: {
        nombre: this.proveedor.nombre || '',
        empresa: this.proveedor.empresa || '',
        email: this.proveedor.email || '',
        telefono: this.proveedor.telefono || '',
        direccion: this.proveedor.direccion || '',
        rfc: this.proveedor.rfc || '',
        activo: this.proveedor.activo !== undefined ? this.proveedor.activo : true
      },
      loading: false,
      errors: {},
      alert: { show: false, message: '', type: 'success' }
    };
  },
  methods: {
    async actualizarProveedor() {
      this.loading = true;
      this.errors = {};
      this.alert.show = false;
      
      try {
        // Usamos la URL que recibimos del prop
        const response = await axios.put(this.updateUrl, this.form);
        
        if (response.data.success) {
          this.showAlert('¡Proveedor actualizado exitosamente!', 'success');
          setTimeout(() => {
            window.location.href = this.cancelUrl;
          }, 1000);
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          this.showAlert('Por favor corrige los errores en el formulario.', 'danger');
        } else {
          const message = error.response?.data?.message || 'Error al actualizar el proveedor';
          this.showAlert(message, 'danger');
        }
      } finally {
        this.loading = false;
      }
    },
    
    showAlert(message, type = 'success') {
      this.alert = { show: true, message, type };
      setTimeout(() => this.alert.show = false, 4000);
    }
  }
};
</script>