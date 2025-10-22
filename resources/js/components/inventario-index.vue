<template>
    <div class="container-fluid mt-4">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Productos</h5>
                        <h2>{{ estadisticas.total_productos }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Stock Bajo</h5>
                        <h2>{{ estadisticas.stock_bajo }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Sin Stock</h5>
                        <h2>{{ estadisticas.sin_stock }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Valor Inventario</h5>
                        <h2>
                            ${{
                                Number(estadisticas.valor_inventario).toFixed(2)
                            }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Buscar Producto</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="filtros.buscar"
                            @input="buscar"
                            placeholder="Nombre del producto..."
                        />
                    </div>
                    <div class="col-md-2">
                        <button
                            class="btn w-100"
                            :class="
                                filtros.stock_bajo
                                    ? 'btn-warning'
                                    : 'btn-outline-warning'
                            "
                            @click="toggleFiltro('stock_bajo')"
                        >
                            <i class="bi bi-exclamation-triangle"></i> Stock
                            Bajo
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button
                            class="btn w-100"
                            :class="
                                filtros.sin_stock
                                    ? 'btn-danger'
                                    : 'btn-outline-danger'
                            "
                            @click="toggleFiltro('sin_stock')"
                        >
                            <i class="bi bi-x-circle"></i> Sin Stock
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button
                            class="btn btn-secondary w-100"
                            @click="limpiarFiltros"
                        >
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Inventario -->
        <div class="card">
            <div class="card-body">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Cargando inventario...</p>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-center">Cantidad Actual</th>
                                <th class="text-center">Proveedor</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Valor Total</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="producto in productos"
                                :key="producto.id"
                            >
                                <td>
                                    <strong>{{ producto.nombre }}</strong>
                                    <br />
                                    <small class="text-muted"
                                        >ID: {{ producto.id }}</small
                                    >
                                </td>

                                <!-- Categorias -->
                                <td>
                                    <span
                                        v-if="
                                            producto.categories &&
                                            producto.categories.length > 0
                                        "
                                    >
                                        <span
                                            v-for="(
                                                cat, index
                                            ) in producto.categories"
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

                                <!-- Cantidad actual -->
                                <td class="text-center">
                                    <span
                                        class="badge fs-6"
                                        :class="{
                                            'bg-danger':
                                                producto.cantidad === 0,
                                            'bg-warning':
                                                producto.cantidad >
                                                    0 /* <-- CORREGIDO */ &&
                                                producto.cantidad <= 10,
                                            'bg-success':
                                                producto.cantidad >
                                                producto.stock_minimo,
                                        }"
                                    >
                                        {{ producto.cantidad || 0 }}
                                    </span>
                                </td>

                                <!-- Proveedor -->

                                <td class="text-center">
                                    <span v-if="producto.proveedor">{{
                                        producto.proveedor.nombre
                                    }}</span>
                                    <span v-else class="text-muted fst-italic"
                                        >Sin proveedor</span
                                    >
                                </td>

                                <!-- Precio -->
                                <td class="text-end">
                                    ${{ Number(producto.precio).toFixed(2) }}
                                </td>

                                <!-- Valor total -->
                                <td class="text-end">
                                    ${{
                                        (
                                            (producto.cantidad || 0) *
                                            producto.precio
                                        ).toFixed(2)
                                    }}
                                </td>
                                <!-- Estado -->
                                <td class="text-center">
                                    <span
                                        v-if="producto.cantidad === 0"
                                        class="badge bg-danger"
                                        >Sin Stock</span
                                    >
                                    <span
                                        v-else-if="
                                            producto.cantidad <=
                                            producto.stock_minimo
                                        "
                                        class="badge bg-warning text-dark"
                                        >Stock Bajo</span
                                    >
                                    <span v-else class="badge bg-success"
                                        >Disponible</span
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "inventario-index",
    data() {
        return {
            productos: [],
            estadisticas: {
                total_productos: 0,
                stock_bajo: 0,
                sin_stock: 0,
                valor_inventario: 0,
            },
            filtros: {
                buscar: "",
                stock_bajo: false,
                sin_stock: false,
            },
            modalAjuste: false,
            productoAjuste: {},
            ajuste: {
                nuevo_stock: 0,
                observaciones: "",
            },
            loading: false,
            loadingAjuste: false,
            searchTimeout: null,
        };
    },
    mounted() {
        console.log("Componente inventario-index montado");
        this.cargarInventario();
    },
    methods: {
        async cargarInventario() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filtros.buscar)
                    params.append("buscar", this.filtros.buscar);
                if (this.filtros.stock_bajo) params.append("stock_bajo", "1");
                if (this.filtros.sin_stock) params.append("sin_stock", "1");

                const response = await axios.get(
                    `/api/inventario?${params.toString()}`
                );

                this.productos = response.data.productos;
                this.estadisticas = response.data.estadisticas;

                console.log(
                    "Inventario cargado:",
                    this.productos.length,
                    "productos"
                );
            } catch (error) {
                console.error("Error al cargar inventario:", error);
                alert("Error al cargar el inventario");
            } finally {
                this.loading = false;
            }
        },

        buscar() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.cargarInventario(), 500);
        },

        toggleFiltro(filtro) {
            this.filtros[filtro] = !this.filtros[filtro];
            this.cargarInventario();
        },

        limpiarFiltros() {
            this.filtros = {
                buscar: "",
                stock_bajo: false,
                sin_stock: false,
            };
            this.cargarInventario();
        },

        async ajustarStock() {
            if (!this.ajuste.observaciones.trim()) {
                alert("Por favor ingresa el motivo del ajuste");
                return;
            }

            this.loadingAjuste = true;
            try {
                const response = await axios.post("/api/inventario/ajustar", {
                    producto_id: this.productoAjuste.id,
                    nuevo_stock: this.ajuste.nuevo_stock,
                    observaciones: this.ajuste.observaciones,
                });

                if (response.data.success) {
                    alert("Stock ajustado correctamente");
                    this.cerrarModal();
                    this.cargarInventario();
                }
            } catch (error) {
                console.error("Error al ajustar stock:", error);
                alert(
                    error.response?.data?.message || "Error al ajustar el stock"
                );
            } finally {
                this.loadingAjuste = false;
            }
        },

        cerrarModal() {
            this.modalAjuste = false;
            this.productoAjuste = {};
            this.ajuste = { nuevo_stock: 0, observaciones: "" };
        },
    },
};
</script>

<style scoped>
.modal.show {
    display: block;
}
</style>
