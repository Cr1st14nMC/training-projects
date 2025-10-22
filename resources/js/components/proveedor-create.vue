<template>
  <div class="container mt-4">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Nuevo Proveedor</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="guardarProveedor">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nombre" class="form-label">Nombre de Proveedor *</label>
              <input 
                type="text" 
                id="nombre"
                class="form-control" 
                v-model="form.nombre" 
                :class="{ 'is-invalid': errors.nombre }"
                required
                placeholder="Nombre del contacto"
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
                placeholder="Nombre de la empresa"
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
                placeholder="correo@ejemplo.com"
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
                placeholder="(123) 456-7890"
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
                placeholder="XAXX010101000"
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
              placeholder="Dirección completa del proveedor"
            ></textarea>
            <div v-if="errors.direccion" class="invalid-feedback">{{ errors.direccion[0] }}</div>
          </div>

          <!-- Alerta de errores -->
          <div v-if="alert.show" :class="`alert alert-${alert.type} alert-dismissible fade show`" role="alert">
            {{ alert.message }}
            <button type="button" class="btn-close" @click="alert.show = false"></button>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a href="/proveedores" class="btn btn-secondary">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button 
              type="submit" 
              class="btn btn-primary"
              :disabled="loading"
            >
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-check-circle"></i>
              {{ loading ? 'Guardando...' : 'Guardar Proveedor' }}
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
  name: 'proveedor-create',
  data() {
    return {
      form: {
        nombre: '',
        empresa: '',
        email: '',
        telefono: '',
        direccion: '',
        rfc: '',
        activo: true
      },
      loading: false,
      errors: {},
      alert: { show: false, message: '', type: 'success' }
    };
  },
  mounted() {
    console.log('Componente proveedor-create montado correctamente');
  },
  methods: {
    async guardarProveedor() {
      this.loading = true;
      this.errors = {};
      this.alert.show = false;
      
      console.log('Datos a enviar:', this.form);
      
      try {
        const response = await axios.post('/api/proveedores', this.form);
        console.log('Respuesta del servidor:', response.data);
        
        if (response.data.success) {
          this.showAlert('¡Proveedor registrado exitosamente!', 'primary');
          setTimeout(() => {
            window.location.href = '/proveedores';
          }, 1000);
        }
      } catch (error) {
        console.error('Error completo:', error);
        console.error('Respuesta del error:', error.response);
        
        if (error.response && error.response.status === 422) {
          // Errores de validación
          this.errors = error.response.data.errors || {};
          this.showAlert('Por favor corrige los errores en el formulario.', 'danger');
        } else {
          const message = error.response?.data?.message || 'Error al guardar el proveedor';
          this.showAlert(message, 'danger');
        }
      } finally {
        this.loading = false;
      }
    },
    
    showAlert(message, type = 'success') {
      this.alert = { show: true, message, type };
      if (type === 'success') {
        setTimeout(() => this.alert.show = false, 4000);
      }
    }
  }
};
</script>

<style scoped>
.card {
  border-radius: 10px;
}
</style>