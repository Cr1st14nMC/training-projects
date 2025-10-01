<template>
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <button @click="goCreate" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i> Agregar
      </button>
    </div>

    <div v-if="alert.show" :class="['alert', alert.type === 'success' ? 'alert-success' : 'alert-danger', 'alert-dismissible']" role="alert">
      {{ alert.message }}
      <button type="button" class="btn-close" @click="alert.show = false"></button>
    </div>

    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cat in categories" :key="cat.id">
          <td>{{ cat.id }}</td>
          <td>{{ cat.nombre }}</td>
          <td>{{ cat.descripcion }}</td>
          <td>
            <button
              @click="goEdit(cat.id)"
              class="btn btn-sm btn-warning me-2"
            >
              <i class="bi bi-pencil-square me-1"></i>Editar
            </button>

            <button
              class="btn btn-sm btn-danger"
              @click="deleteCategory(cat.id)"
              :disabled="loadingId === cat.id"
            >
              <span
                v-if="loadingId === cat.id"
                class="spinner-border spinner-border-sm me-1"
              ></span>
              <i v-else class="bi bi-trash me-1"></i>Eliminar
            </button>
          </td>
        </tr>
        <tr v-if="loading">
          <td colspan="4" class="text-center">Cargando...</td>
        </tr>
        <tr v-if="!loading && categories.length === 0">
          <td colspan="4" class="text-center">No hay categorías.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "categoria-index",
  props: {
    fetchUrl: { type: String, required: true },
    createUrl: { type: String, required: true },
    editBaseUrl: { type: String, required: true },
    destroyBaseUrl: { type: String, required: true }
  },
  data() {
    return {
      categories: [],
      alert: { show: false, message: "", type: "success" },
      loadingId: null,
      loading: false
    };
  },
  mounted() {
    this.getCategories();
  },
  methods: {
    async getCategories() {
      this.loading = true;
      try {
        const res = await axios.get(this.fetchUrl, {
          headers: { Accept: "application/json" }
        });
        this.categories = res.data.data ?? res.data;
      } catch (err) {
        this.showAlert("Error al cargar categorías", "error");
      } finally {
        this.loading = false;
      }
    },

    goCreate() {
      window.location.href = this.createUrl;
    },

    goEdit(id) {
      window.location.href = `${this.editBaseUrl}/${id}/edit`;
    },

    async deleteCategory(id) {
      if (!confirm("¿Eliminar categoría?")) return;

      this.loadingId = id;
      try {
        const res = await axios.delete(`${this.destroyBaseUrl}/${id}`);
        if (res.data.success) {
          this.categories = this.categories.filter(c => c.id !== id);
          this.showAlert("Categoría eliminada correctamente", "success");
        }
      } catch (err) {
        const msg = err?.response?.data?.message || "Error al eliminar";
        this.showAlert(msg, "error");
      } finally {
        this.loadingId = null;
      }
    },

    showAlert(message, type = "success") {
      this.alert.message = message;
      this.alert.type = type;
      this.alert.show = true;
      setTimeout(() => (this.alert.show = false), 4000);
    }
  }
};
</script>