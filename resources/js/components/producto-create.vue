<template>
  <div class="container py-4">
    <!-- Alert -->
    <div
      v-if="alert.show"
      :class="[
        'alert',
        alert.type === 'success' ? 'alert-primary' : 'alert-danger',
        'alert-dismissible',
        'fade',
        'show',
      ]"
      role="alert"
    >
      {{ alert.message }}
      <button
        type="button"
        class="btn-close"
        @click="alert.show = false"
      ></button>
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
        <label for="categories" class="form-label">Categorías</label>
        <select
          id="categories"
          v-model="form.categories"
          class="form-select"
          multiple
          style="height: 150px"
        >
          <option
            v-for="cat in categoriesData"
            :key="cat.id"
            :value="cat.id"
          >
            {{ cat.nombre }}
          </option>
        </select>
        <small class="text-muted">Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples categorías</small>
      </div>

      <div class="mb-3">
        <label for="proveedor_id" class="form-label">Proveedor</label>
        <select
          id="proveedor_id"
          v-model="form.proveedor_id"
          class="form-select"
          :class="{ 'is-invalid': errors.proveedor_id }"
        >
          <option :value="null">-- Sin proveedor --</option>
          <option
            v-for="prov in proveedoresData"
            :key="prov.id"
            :value="prov.id"
          >
            {{ prov.nombre }} {{ prov.empresa ? `- ${prov.empresa}` : '' }}
          </option>
        </select>
        <div v-if="errors.proveedor_id" class="invalid-feedback d-block">
          {{ errors.proveedor_id[0] }}
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

      <div class="mb-3">
        <label for="cantidad" class="form-label">Cantidad Inicial</label>
        <input
          type="number"
          id="cantidad"
          v-model="form.cantidad"
          class="form-control"
          :class="{ 'is-invalid': errors.cantidad }"
          placeholder="100"
          required
          min="0"
        />
        <div v-if="errors.cantidad" class="invalid-feedback d-block">
          {{ errors.cantidad[0] }}
        </div>
      </div>

      <button
        type="submit"
        class="btn btn-primary me-2"
        :disabled="loading"
      >
        <span
          v-if="loading"
          class="spinner-border spinner-border-sm me-1"
        ></span>
        <i class="bi bi-check-circle me-1"></i>
        {{ loading ? "Guardando..." : "Guardar" }}
      </button>
      <a href="/productos" class="btn btn-secondary">
        <i class="bi bi-x-circle me-1"></i> Cancelar
      </a>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "producto-create",
  data() {
    return {
      form: {
        nombre: "",
        descripcion: "",
        precio: "",
        cantidad: 0,
        categories: [],
        proveedor_id: null,
      },
      categoriesData: [],
      proveedoresData: [],
      errors: {},
      alert: {
        show: false,
        message: "",
        type: "success",
      },
      loading: false,
    };
  },
  mounted() {
    console.log('Componente producto-create montado');
    this.cargarCategorias();
    this.cargarProveedores();
  },
  methods: {
    async cargarCategorias() {
      try {
        console.log('Cargando categorías...');
        const response = await axios.get('/api/categories');
        
        if (Array.isArray(response.data)) {
          this.categoriesData = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          this.categoriesData = response.data.data;
        } else {
          this.categoriesData = [];
        }
        
        console.log('Categorías cargadas:', this.categoriesData.length);
      } catch (error) {
        console.error('Error al cargar categorías:', error);
      }
    },

    async cargarProveedores() {
      try {
        console.log('Cargando proveedores...');
        const response = await axios.get('/api/proveedores');
        
        if (Array.isArray(response.data)) {
          this.proveedoresData = response.data.filter(p => p.activo);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          this.proveedoresData = response.data.data.filter(p => p.activo);
        } else {
          this.proveedoresData = [];
        }
        
        console.log('Proveedores cargados:', this.proveedoresData.length);
      } catch (error) {
        console.error('Error al cargar proveedores:', error);
      }
    },

    async submitForm() {
      this.loading = true;
      this.errors = {};
      this.alert.show = false;

      console.log('Datos a enviar:', this.form);

      try {
        const res = await axios.post('/api/productos', this.form);

        if (res.data && res.data.success) {
          this.showAlert("Producto creado correctamente", "success");
          setTimeout(() => {
            window.location.href = '/productos';
          }, 1500);
        } else {
          window.location.href = '/productos';
        }
      } catch (error) {
        console.error('Error al guardar:', error);
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          this.showAlert(
            "Por favor corrige los errores en el formulario",
            "error"
          );
        } else {
          const msg =
            error?.response?.data?.message ||
            "Hubo un error al guardar el producto.";
          this.showAlert(msg, "error");
        }
      } finally {
        this.loading = false;
      }
    },

    showAlert(message, type = "success") {
      this.alert.message = message;
      this.alert.type = type;
      this.alert.show = true;
    },
  },
};
</script>