<template>
  <div class="container-fluid">
    <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-danger', 'alert-dismissible']" role="alert">
      {{ alert.message }}
      <button type="button" class="btn-close" @click="alert.show = false"></button>
    </div>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input
              id="nombre"
              type="text"
              class="form-control"
              v-model="form.nombre"
              :class="{ 'is-invalid': errors.nombre }"
              placeholder="Coca, Pepsi, Fanta..."
              required
            />
            <div v-if="errors.nombre" class="invalid-feedback d-block">
              {{ errors.nombre[0] }}
            </div>
          </div>

           <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea
          v-model="form.descripcion"
          class="form-control"
          placeholder="Bebidas refrescantes..."
        ></textarea>
      </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
              <i v-else class="bi bi-check-circle me-1"></i>
              {{ loading ? 'Guardando...' : 'Guardar' }}
            </button>
            <button type="button" @click="goBack" class="btn btn-secondary">
              <i class="bi bi-x-circle me-1"></i> Cancelar
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
  name: 'categoria-create',
  props: {
    storeUrl: { type: String, required: true },
    cancelUrl: { type: String, required: true },
  },
  data() {
    return {
      form: {
        nombre: '',
        descripcion: ""
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
      this.alert.show = false;

      try {
        const res = await axios.post(this.storeUrl, this.form);
        
        if (res.data && res.data.success) {
          this.showAlert('Categoría creada correctamente', 'success');
          setTimeout(() => {
            window.location.href = this.cancelUrl;
          }, 1500);
        }
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.errors = err.response.data.errors || {};
        } else {
          const msg = err?.response?.data?.message || 'Error al guardar la categoría.';
          this.showAlert(msg, 'error');
        }
      } finally {
        this.loading = false;
      }
    },

    goBack() {
      window.location.href = this.cancelUrl;
    },

    showAlert(message, type = 'success') {
      this.alert.message = message;
      this.alert.type = type;
      this.alert.show = true;
    }
  }
};
</script>