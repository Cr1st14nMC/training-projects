<template>
    <!-- parte superior del template -->
    <div class="mb-3 d-flex justify-content-between">
        <div class="d-flex flex-grow-1 me-3">
            <input
                v-model="q"
                @input="onSearch"
                class="form-control form-control-sm me-2"
                placeholder="Buscar por ID o nombre"
                style="min-width: 300px; max-width: 600px"
            />
            <div class="dropdown me-2">
                <button
                    class="btn btn-outline-success btn-sm dropdown-toggle me-2"
                    type="button"
                    id="dropdownFiltro"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                >
                    Filtrar
                </button>
                <ul
                    class="dropdown-menu p-2"
                    aria-labelledby="dropdownFiltro"
                    style="max-height: 300px; overflow-y: auto"
                >
                    <li v-for="cat in categories" :key="cat.id">
                        <div class="form-check dropdown-item px-3 m-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                :value="cat.id"
                                :id="'cat-' + cat.id"
                                v-model="selectedCategories"
                                @change="applySearch"
                            />
                            <label
                                class="form-check-label fx-3"
                                :for="'cat-' + cat.id"
                            >
                                {{ cat.nombre }}
                            </label>
                        </div>
                    </li>
                    <li v-if="categories.length === 0">
                        <span class="dropdown-item-text text-muted"
                            >No hay categorías</span
                        >
                    </li>
                </ul>
            </div>

            <button
                @click="clearSearch"
                class="btn btn-outline-secondary btn-sm"
            >
                Limpiar
            </button>
        </div>
    </div>

    <div>
        <!-- Alerts -->
        <div
            v-if="alert.show"
            :class="[
                'alert',
                alert.type === 'success' ? 'alert-success' : 'alert-danger',
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
                aria-label="Close"
            ></button>
        </div>

        <!-- Botón Crear -->
        <div class="mb-3 d-flex justify-content-between">
            <div>
                <button @click="goCreate" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-2"></i> Agregar
                </button>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Categoria</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="producto in productos" :key="producto.id">
                    <td>{{ producto.id }}</td>
                    <td>{{ producto.nombre }}</td>
                    <td>{{ producto.descripcion }}</td>
                    <td>${{ Number(producto.precio).toFixed(2) }}</td>
                    <td>
                        <span
                            v-if="
                                producto.categories &&
                                producto.categories.length > 0
                            "
                        >
                            <span
                                v-for="(cat, index) in producto.categories"
                                :key="cat.id"
                                class="badge bg-secondary me-1"
                            >
                                {{ cat.nombre }}
                            </span>
                        </span>
                        <span v-else class="text-muted fst-italic">
                            Sin categoría
                        </span>
                    </td>
                    <td>
                        <button
                            @click="goEdit(producto.id)"
                            class="btn btn-warning btn-sm me-2"
                        >
                            <i class="bi bi-pencil-square me-1"></i>Editar
                        </button>
                        <button
                            @click="confirmDelete(producto.id)"
                            class="btn btn-danger btn-sm"
                        >
                            <i class="bi bi-trash me-1"></i>Eliminar
                        </button>
                    </td>
                </tr>

                <tr v-if="loading">
                    <td colspan="6" class="text-center">Cargando...</td>
                </tr>

                <tr v-if="!loading && productos.length === 0">
                    <td colspan="6" class="text-center">
                        {{
                            q
                                ? "No se encontraron resultados"
                                : "No hay productos."
                        }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "producto-index",
    data() {
        return {
            productos: [],
            categories: [],
            selectedCategories: [],
            q: this.initialQuery ?? "",
            alert: { show: false, message: "", type: "success" },
            loading: false,
        };
    },
    props: {
        initialProducts: { type: Array, default: () => [] },
        initialQuery: { type: String, default: "" },
    },
    mounted() {
        this.fetchCategories();
        // usa los pasados por blade inicialmente si existen
        if (this.initialProducts && this.initialProducts.length) {
            this.productos = this.initialProducts;
        } else {
            this.fetchProductos();
        }
    },

    methods: {
        async fetchCategories() {
            try {
                const res = await axios.get("/categories", {
                    headers: { Accept: "application/json" },
                });
                this.categories = res.data.data || res.data;
            } catch (err) {
                console.error("Error al cargar categorías:", err);
            }
        },
        async fetchProductos() {
            this.loading = true;
            try {
                const params = {};
                if (this.q) params.q = this.q;

                if (this.selectedCategories.length > 0) {
                    params.categories = this.selectedCategories.join(",");
                }

                const res = await axios.get("/productos", {
                    headers: { Accept: "application/json" },
                    params: params,
                });

                // Manejar ambos formatos de respuesta
                if (res.data) {
                    // Si viene en formato {success: true, data: [...]}
                    if (res.data.data) {
                        this.productos = res.data.data;
                    }
                    // Si viene directamente como array
                    else if (Array.isArray(res.data)) {
                        this.productos = res.data;
                    }
                    // Si es objeto con productos
                    else {
                        this.productos = res.data;
                    }
                }
            } catch (err) {
                console.error("Error al cargar productos:", err);
                this.showAlert("Error al cargar productos", "error");
            } finally {
                this.loading = false;
            }
        },

        onSearch() {
            // debounce simple: espera 300ms antes de buscar
            clearTimeout(this._searchTimeout);
            this._searchTimeout = setTimeout(() => this.fetchProductos(), 300);
        },

        applySearch() {
            this.fetchProductos();
        },

        clearSearch() {
            this.q = "";
            this.fetchProductos();
            this.selectedCategories = [];
        },

        goCreate() {
            window.location.href = "/productos/create";
        },

        goEdit(id) {
            window.location.href = `/productos/${id}/edit`;
        },

        async confirmDelete(id) {
            if (!confirm("¿Estás seguro de eliminar este producto?")) return;

            try {
                const res = await axios.delete(`/productos/${id}`);
                if (res.data && res.data.success) {
                    this.productos = this.productos.filter((p) => p.id !== id);
                    this.showAlert(
                        "Producto eliminado correctamente",
                        "success"
                    );
                } else {
                    this.showAlert(
                        res.data.message || "No se pudo eliminar",
                        "error"
                    );
                }
            } catch (err) {
                console.error(err);
                const msg =
                    err?.response?.data?.message ||
                    "Error al eliminar producto";
                this.showAlert(msg, "error");
            }
        },

        showAlert(message, type = "success") {
            this.alert.message = message;
            this.alert.type = type === "success" ? "success" : "danger";
            this.alert.show = true;
            setTimeout(() => (this.alert.show = false), 4000);
        },
    },
};
</script>
