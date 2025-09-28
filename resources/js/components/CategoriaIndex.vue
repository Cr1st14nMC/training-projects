<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="mb-0">Categorías</h1>
      <a :href="createUrl" class="btn btn-primary btn-sm">Crear Categoría</a>
    </div>

    <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
      {{ alert.message }}
    </div>

    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Productos</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cat in categories" :key="cat.id">
          <td>{{ cat.id }}</td>
          <td>{{ cat.nombre }}</td>
          <td>{{ cat.productos_count ?? 0 }}</td>
          <td>
            <a :href="`${editBaseUrl}/${cat.id}/edit`" class="btn btn-sm btn-warning me-2">Editar</a>

            <button
              v-if="cat.productos_count > 0"
              class="btn btn-sm btn-secondary"
              disabled
              title="No se puede eliminar: tiene productos asignados"
            >
              Eliminar
            </button>

            <button
              v-else
              class="btn btn-sm btn-danger"
              @click="deleteCategory(cat.id)"
              :disabled="loadingId === cat.id"
            >
              <span
                v-if="loadingId === cat.id"
                class="spinner-border spinner-border-sm me-1"
                role="status"
                aria-hidden="true"
              ></span>
              Eliminar
            </button>
          </td>
        </tr>
        <tr v-if="categories.length === 0">
          <td colspan="4" class="text-center">No hay categorías.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CategoryIndex",
  props: {
    fetchUrl: { type: String, required: true },   // api o ruta normal
    createUrl: { type: String, required: true },
    editBaseUrl: { type: String, required: true }, // ej: /categories
    destroyBaseUrl: { type: String, required: true } // ej: /categories
  },
  data() {
    return {
      categories: [],
      alert: { show: false, message: "", type: "success" },
      loadingId: null,
    };
  },
  mounted() {
    this.getCategories();
  },
  methods: {
    async getCategories() {
      try {
        const res = await axios.get(this.fetchUrl);
        if (res.data && res.data.data) {
          this.categories = res.data.data;
        } else {
          this.categories = res.data;
        }
      } catch (err) {
        this.alert.show = true;
        this.alert.type = "danger";
        this.alert.message = "Error al cargar categorías.";
      }
    },
    async deleteCategory(id) {
      if (!confirm("¿Eliminar categoría?")) return;

      this.loadingId = id;
      try {
        await axios.delete(`${this.destroyBaseUrl}/${id}`);
        this.categories = this.categories.filter(c => c.id !== id);
        this.alert.show = true;
        this.alert.type = "success";
        this.alert.message = "Categoría eliminada correctamente.";
      } catch (err) {
        this.alert.show = true;
        this.alert.type = "danger";
        this.alert.message = err?.response?.data?.message || "Error al eliminar.";
      } finally {
        this.loadingId = null;
      }
    }
  }
};
</script>
