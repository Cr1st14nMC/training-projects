<template>
    <div class="container-fluid">
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

        <div class="card">
            <div class="card-body">
                <form @submit.prevent="updateProduct">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input
                            type="text"
                            id="nombre"
                            class="form-control"
                            v-model="form.nombre"
                            :class="{ 'is-invalid': errors.nombre }"
                            required
                        />
                        <div
                            v-if="errors.nombre"
                            class="invalid-feedback d-block"
                        >
                            {{ errors.nombre[0] }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label"
                            >Descripción</label
                        >
                        <textarea
                            id="descripcion"
                            class="form-control"
                            rows="3"
                            v-model="form.descripcion"
                            :class="{ 'is-invalid': errors.descripcion }"
                        ></textarea>
                        <div
                            v-if="errors.descripcion"
                            class="invalid-feedback d-block"
                        >
                            {{ errors.descripcion[0] }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="categories" class="form-label"
                            >Categorías</label
                        >
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
                    </div>
                    <div class="mb-3">
                        <label for="proveedor_id" class="form-label"
                            >Proveedor</label
                        >
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
                                {{ prov.nombre }}
                                {{ prov.empresa ? `- ${prov.empresa}` : "" }}
                            </option>
                        </select>
                        <div
                            v-if="errors.proveedor_id"
                            class="invalid-feedback d-block"
                        >
                            {{ errors.proveedor_id[0] }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input
                            type="number"
                            step="0.01"
                            id="precio"
                            class="form-control"
                            v-model="form.precio"
                            :class="{ 'is-invalid': errors.precio }"
                            required
                            min="0"
                        />
                        <div
                            v-if="errors.precio"
                            class="invalid-feedback d-block"
                        >
                            {{ errors.precio[0] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label"
                            >Cantidad en Stock</label
                        >
                        <input
                            type="number"
                            id="cantidad"
                            class="form-control"
                            v-model="form.cantidad"
                            :class="{ 'is-invalid': errors.cantidad }"
                            required
                            min="0"
                        />
                        <div
                            v-if="errors.cantidad"
                            class="invalid-feedback d-block"
                        >
                            {{ errors.cantidad[0] }}
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="submit"
                            class="btn btn-warning"
                            :disabled="loading"
                        >
                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>
                            <i v-else class="bi bi-check-circle me-1"></i>
                            {{ loading ? "Actualizando..." : "Actualizar" }}
                        </button>
                        <button
                            type="button"
                            @click="goBack"
                            class="btn btn-secondary"
                        >
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "producto-edit",
    props: {
        producto: {
            type: Object,
            required: true,
        },
        // categories: {
        //     type: Array,
        //     default: () => [],
        // },
        // proveedores: { type: Array, default: () => [] }, // <-- AÑADIR ESTA PROP
        updateUrl: {
            type: String,
            required: true,
        },
        cancelUrl: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            form: {
                nombre: this.producto.nombre || "",
                descripcion: this.producto.descripcion || "",
                precio: this.producto.precio || 0,
                cantidad: this.producto.cantidad || 0,
                categories: this.producto.categories
                    ? this.producto.categories.map((cat) => cat.id)
                    : [],
                proveedor_id: this.producto.proveedor_id || null, // <-- AÑADIR ESTO
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
        console.log("Producto:", this.producto);

        console.log("Categorías:", this.categories);
        console.log("Form initialized:", this.form);

        this.cargarCategorias();
        this.cargarProveedores();
    },
    methods: {
        async cargarCategorias() {
            try {
                console.log("Cargando categorías...");
                const response = await axios.get("/api/categories");

                if (Array.isArray(response.data)) {
                    this.categoriesData = response.data;
                } else if (
                    response.data.data &&
                    Array.isArray(response.data.data)
                ) {
                    this.categoriesData = response.data.data;
                } else {
                    this.categoriesData = [];
                }

                console.log("Categorías cargadas:", this.categoriesData.length);
            } catch (error) {
                console.error("Error al cargar categorías:", error);
            }
        },

        async cargarProveedores() {
            try {
                console.log("Cargando proveedores...");
                const response = await axios.get("/api/proveedores");

                if (Array.isArray(response.data)) {
                    this.proveedoresData = response.data.filter(
                        (p) => p.activo
                    );
                } else if (
                    response.data.data &&
                    Array.isArray(response.data.data)
                ) {
                    this.proveedoresData = response.data.data.filter(
                        (p) => p.activo
                    );
                } else {
                    this.proveedoresData = [];
                }

                console.log(
                    "Proveedores cargados:",
                    this.proveedoresData.length
                );
            } catch (error) {
                console.error("Error al cargar proveedores:", error);
            }
        },

        async updateProduct() {
            this.loading = true;
            this.errors = {};
            this.alert.show = false;

            try {
                const res = await axios.put(this.updateUrl, this.form);

                if (res.data.success) {
                    this.showAlert(
                        "Producto actualizado correctamente",
                        "success"
                    );
                    setTimeout(() => {
                        window.location.href = this.cancelUrl;
                    }, 1500);
                }
            } catch (error) {
                console.error(error);

                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.showAlert(
                        "Por favor corrige los errores en el formulario",
                        "error"
                    );
                } else {
                    const msg =
                        error?.response?.data?.message ||
                        "Error al actualizar el producto";
                    this.showAlert(msg, "error");
                }
            } finally {
                this.loading = false;
            }
        },

        goBack() {
            window.location.href = this.cancelUrl;
        },

        showAlert(message, type = "success") {
            this.alert.message = message;
            this.alert.type = type;
            this.alert.show = true;
            setTimeout(() => (this.alert.show = false), 4000);
        },
    },
};
</script>
