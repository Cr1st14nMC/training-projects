<template>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-cart-check"></i> Nueva Venta
                </h5>
            </div>
            <div class="card-body">
                <!-- Alerta -->
                <div
                    v-if="alert.show"
                    :class="`alert alert-${alert.type} alert-dismissible fade show`"
                    role="alert"
                >
                    {{ alert.message }}
                    <button
                        type="button"
                        class="btn-close"
                        @click="alert.show = false"
                    ></button>
                </div>

                <form @submit.prevent="guardarVenta">
                    <!-- Fecha de Venta -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Venta *</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="venta.fecha_venta"
                                :class="{ 'is -invalid': errors.fecha_venta }"
                                required
                            />
                            <div
                                v-if="errors.fecha_venta"
                                class="invalid-feedback"
                            >
                                {{ errors.fecha_venta[0] }}
                            </div>
                        </div>
                    </div>

                    <!-- Agregar Productos -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>Agregar Productos</strong>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Producto</label>
                                    <select
                                        class="form-select"
                                        v-model="productoSeleccionado"
                                    >
                                        <option :value="null">
                                            Seleccionar producto...
                                        </option>
                                        <option
                                            v-for="producto in productos"
                                            :key="producto.id"
                                            :value="producto"
                                        >
                                            {{ producto.nombre }} (Stock:
                                            {{
                                                producto.stock ||
                                                producto.cantidad ||
                                                0
                                            }}) - ${{ producto.precio }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Cantidad</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model.number="cantidadTemporal"
                                        min="1"
                                        :max="
                                            productoSeleccionado
                                                ? productoSeleccionado.stock ||
                                                  productoSeleccionado.cantidad ||
                                                  0
                                                : 0
                                        "
                                    />
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label"
                                        >Precio Unit.</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="
                                            '$' +
                                            (productoSeleccionado
                                                ? Number(
                                                      productoSeleccionado.precio
                                                  ).toFixed(2)
                                                : '0.00')
                                        "
                                        readonly
                                    />
                                </div>

                                <div class="col-md-2">
                                    <button
                                        type="button"
                                        class="btn btn-primary w-100"
                                        @click="agregarProducto"
                                        :disabled="
                                            !productoSeleccionado ||
                                            cantidadTemporal <= 0
                                        "
                                    >
                                        <i class="bi bi-plus-circle"></i>
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Productos Agregados -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"
                            >Productos de la Venta</label
                        >
                        <div v-if="errors.productos" class="alert alert-danger">
                            {{ errors.productos[0] }}
                        </div>

                        <div
                            v-if="venta.productos.length === 0"
                            class="alert alert-info"
                        >
                            <i class="bi bi-info-circle"></i> No hay productos
                            agregados. Use el formulario arriba para agregar
                            productos.
                        </div>

                        <table v-else class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center" style="width: 80px">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in venta.productos"
                                    :key="index"
                                >
                                    <td>{{ item.nombre }}</td>
                                    <td class="text-center">
                                        {{ item.cantidad }}
                                    </td>
                                    <td class="text-end">
                                        ${{
                                            Number(
                                                item.precio_unitario
                                            ).toFixed(2)
                                        }}
                                    </td>
                                    <td class="text-end">
                                        ${{
                                            (
                                                item.cantidad *
                                                item.precio_unitario
                                            ).toFixed(2)
                                        }}
                                    </td>
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            @click="quitarProducto(index)"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary fw-bold">
                                    <td colspan="3" class="text-end">Total:</td>
                                    <td class="text-end fs-5">
                                        ${{ calcularTotal().toFixed(2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label class="form-label"
                            >Observaciones (opcional)</label
                        >
                        <textarea
                            class="form-control"
                            v-model="venta.observaciones"
                            rows="3"
                            placeholder="Notas adicionales sobre la venta..."
                        ></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="/ventas" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="loading || venta.productos.length === 0"
                        >
                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm me-2"
                            ></span>
                            <i v-else class="bi bi-check-circle"></i>
                            {{ loading ? "Guardando..." : "Guardar Venta" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "VentaCreate",
    data() {
        return {
            venta: {
                fecha_venta: new Date().toISOString().split("T")[0],
                observaciones: "",
                productos: [], // { producto_id, nombre, cantidad, precio_unitario }
            },
            productos: [],
            productoSeleccionado: null,
            cantidadTemporal: 0,
            loading: false,
            errors: {},
            alert: { show: false, message: "", type: "success" },
        };
    },
    mounted() {
        console.log("Componente VentaCreate montado correctamente");
        this.cargarProductos();
    },
    methods: {
        async cargarProductos() {
            try {
                console.log("Cargando productos...");
                const response = await axios.get("/api/productos");
                console.log("Respuesta completa:", response);

                if (Array.isArray(response.data)) {
                    this.productos = response.data;
                } else if (
                    response.data.data &&
                    Array.isArray(response.data.data)
                ) {
                    this.productos = response.data.data;
                } else {
                    this.productos = [];
                }

                console.log("Productos cargados:", this.productos);
            } catch (error) {
                console.error("Error al cargar productos:", error);
                this.showAlert("Error al cargar productos", "danger");
            }
        },

        agregarProducto() {
            if (!this.productoSeleccionado || this.cantidadTemporal <= 0) {
                this.showAlert(
                    "Por favor complete todos los campos correctamente",
                    "warning"
                );
                return;
            }

            const stockDisponible =
                this.productoSeleccionado.stock ||
                this.productoSeleccionado.cantidad ||
                0;

            if (this.cantidadTemporal > stockDisponible) {
                this.showAlert(
                    `No hay suficiente stock. Disponible: ${stockDisponible}`,
                    "danger"
                );
                return;
            }

            // Verificar si el producto ya está en la lista
            const existente = this.venta.productos.find(
                (p) => p.producto_id === this.productoSeleccionado.id
            );

            if (existente) {
                const totalCantidad =
                    existente.cantidad + this.cantidadTemporal;
                if (totalCantidad > stockDisponible) {
                    this.showAlert(
                        `No hay suficiente stock. Ya tienes ${existente.cantidad} en la venta. Disponible: ${stockDisponible}`,
                        "danger"
                    );
                    return;
                }
                existente.cantidad += this.cantidadTemporal;
                this.showAlert(
                    "Cantidad actualizada para el producto existente",
                    "info"
                );
            } else {
                this.venta.productos.push({
                    producto_id: this.productoSeleccionado.id,
                    nombre: this.productoSeleccionado.nombre,
                    cantidad: this.cantidadTemporal,
                    precio_unitario: parseFloat(
                        this.productoSeleccionado.precio
                    ),
                });
                this.showAlert("Producto agregado correctamente", "primary");
            }

            // Limpiar campos
            this.productoSeleccionado = null;
            this.cantidadTemporal = 0;
        },

        quitarProducto(index) {
            this.venta.productos.splice(index, 1);
            this.showAlert("Producto eliminado", "info");
        },

        calcularTotal() {
            return this.venta.productos.reduce((total, item) => {
                return total + item.cantidad * item.precio_unitario;
            }, 0);
        },

        async guardarVenta() {
            if (this.venta.productos.length === 0) {
                this.showAlert("Debes agregar al menos un producto", "warning");
                return;
            }

            this.loading = true;
            this.errors = {};

            console.log("Datos a enviar:", this.venta);

            try {
                const response = await axios.post("/api/ventas", this.venta);
                console.log("Respuesta del servidor:", response.data);

                this.showAlert(
                    "¡Venta registrada exitosamente! El stock ha sido actualizado.",
                    "primary"
                );
                setTimeout(() => {
                    window.location.href = "/ventas";
                }, 1500);
            } catch (error) {
                console.error("Error completo:", error);
                console.error("Respuesta del error:", error.response);
                console.error("Datos del error:", error.response?.data);

                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.showAlert(
                        "Por favor corrige los errores en el formulario",
                        "danger"
                    );
                } else {
                    const message =
                        error.response?.data?.message ||
                        error.response?.data?.error ||
                        "Error al guardar la venta";
                    this.showAlert(message, "danger");
                }
            } finally {
                this.loading = false;
            }
        },

        showAlert(message, type = "success") {
            this.alert = { show: true, message, type };
            window.scrollTo({ top: 0, behavior: "smooth" });

            if (type === "success" || type === "info") {
                setTimeout(() => (this.alert.show = false), 3000);
            }
        },
    },
};
</script>

<style scoped>
.card {
    border-radius: 10px;
}
</style>
