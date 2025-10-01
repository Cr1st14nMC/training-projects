<template>
  <div class="container py-4">
    <!-- Alert -->
    <div
      v-if="alert.show"
      :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-danger', 'alert-dismissible', 'fade', 'show']"
      role="alert"
    >
      {{ alert.message }}
      <button type="button" class="btn-close" @click="alert.show = false"></button>
    </div>

    <form @submit.prevent="submitForm">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input
          type="text"
          id="nombre"
          v-model="form.nombre"
          class="form-control"
          :class="{ 'is-invalid': errors.nombre }"
          placeholder="Coca..."
          required
        />
        <div v-if="errors.nombre" class="invalid-feedback d-block">
          {{ errors.nombre[0] }}
        </div>
      </div>

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea
          id="descripcion"
          v-model="form.descripcion"
          class="form-control"
          :class="{ 'is-invalid': errors.descripcion }"
          placeholder="600 ml..."
          rows="3"
        ></textarea>
        <div v-if="errors.descripcion" class="invalid-feedback d-block">
          {{ errors.descripcion[0] }}
        </div>
      </div>

      <div class="mb-3">
        <label for="categoria_id" class="form-label">Categoría</label>
        <select
          id="categoria_id"
          v-model="form.categoria_id"
          class="form-select"
          :class="{ 'is-invalid': errors.categoria_id }"
        >
          <option value="">Seleccionar categoría...</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.nombre }}
          </option>
        </select>
        <div v-if="errors.categoria_id" class="invalid-feedback d-block">
          {{ errors.categoria_id[0] }}
        </div>
      </div>

      <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input
          type="number"
          id="precio"
          step="0.01"
          v-model="form.precio"
          class="form-control"
          :class="{ 'is-invalid': errors.precio }"
          placeholder="19.00"
          required
          min="0"
        />
        <div v-if="errors.precio" class="invalid-feedback d-block">
          {{ errors.precio[0] }}
        </div>
      </div>

      <button type="submit" class="btn btn-primary me-2" :disabled="loading">
        <span v-if="loading" class="spinner-border spinner-border-sm me-1 "></span>
        <i class="bi bi-check-circle me-1"></i>
        {{ loading ? 'Guardando...' : 'Guardar' }}
      </button>
      <a :href="cancelUrl" class="btn btn-secondary"> <i class="bi bi-x-circle me-1"></i> Cancelar</a>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "producto-create",
  props: {
    categories: { type: Array, default: () => [] },
    storeUrl: { type: String, required: true },
    cancelUrl: { type: String, required: true },
  },
  data() {
    return {
      form: {
        nombre: "",
        descripcion: "",
        categoria_id: "",
        precio: "",
      },
      errors: {},
      alert: {
        show: false,
        message: '',
        type: 'success'
      },
      loading: false
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
          this.showAlert('Producto creado correctamente', 'success');
          setTimeout(() => {
            window.location.href = this.cancelUrl;
          }, 1500);
        } else {
          window.location.href = this.cancelUrl;
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          this.showAlert('Por favor corrige los errores en el formulario', 'error');
        } else {
          const msg = error?.response?.data?.message || 'Hubo un error al guardar el producto.';
          this.showAlert(msg, 'error');
        }
      } finally {
        this.loading = false;
      }
    },

    showAlert(message, type = 'success') {
      this.alert.message = message;
      this.alert.type = type;
      this.alert.show = true;
    }
  },
};
</script>