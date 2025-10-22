<template>
    <div class="container-fluid mt-4">
        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Movimiento</label>
                        <select
                            class="form-select"
                            v-model="filtros.tipo"
                            @change="cargarMovimientos"
                        >
                            <option value="">Todos</option>
                            <option value="entrada">Entradas</option>
                            <option value="salida">Salidas</option>
                            <option value="ajuste">Ajustes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Desde</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="filtros.fecha_desde"
                            @change="cargarMovimientos"
                        />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="filtros.fecha_hasta"
                            @change="cargarMovimientos"
                        />
                    </div>
                    <div class="col-md-3">
                        <button
                            class="btn btn-secondary w-100"
                            @click="limpiarFiltros"
                        >
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                            Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="card">
            <div class="card-body">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Cargando movimientos...</p>
                </div>

                <div
                    v-else-if="movimientos.length === 0"
                    class="text-center py-5"
                >
                    <i
                        class="bi bi-inbox"
                        style="font-size: 4rem; color: #ccc"
                    ></i>
                    <p class="text-muted mt-3">
                        No hay movimientos registrados
                    </p>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Stock Anterior</th>
                                <th class="text-center">Stock Nuevo</th>
                                <th>Referencia</th>
                                <th>Usuario</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="mov in movimientos" :key="mov.id">
                                <td>{{ mov.display_date || "-" }}</td>
                                <td>
                                    <strong>{{ mov.producto?.nombre }}</strong
                                    ><br />
                                    <small class="text-muted"
                                        >ID: {{ mov.producto_id }}</small
                                    >
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge"
                                        :class="{
                                            'bg-success':
                                                mov.tipo === 'entrada',
                                            'bg-danger': mov.tipo === 'salida',
                                            'bg-info': mov.tipo === 'ajuste',
                                        }"
                                    >
                                        <i
                                            class="bi"
                                            :class="{
                                                'bi-arrow-down-circle':
                                                    mov.tipo === 'entrada',
                                                'bi-arrow-up-circle':
                                                    mov.tipo === 'salida',
                                                'bi-pencil-square':
                                                    mov.tipo === 'ajuste',
                                            }"
                                        ></i>
                                        {{ mov.tipo.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ mov.cantidad }}</strong>
                                </td>
                                <td class="text-center">
                                    {{ mov.stock_anterior }}
                                </td>
                                <td class="text-center">
                                    <strong
                                        :class="{
                                            'text-success':
                                                mov.stock_nuevo >
                                                mov.stock_anterior,
                                            'text-danger':
                                                mov.stock_nuevo <
                                                mov.stock_anterior,
                                        }"
                                    >
                                        {{ mov.stock_nuevo }}
                                    </strong>
                                </td>
                                <td>
                                    <small>
                                        {{ mov.referencia_tipo || "-" }}
                                        <span v-if="mov.referencia_id"
                                            >#{{ mov.referencia_id }}</span
                                        >
                                    </small>
                                </td>
                                <td>{{ mov.user?.name || "-" }}</td>
                                <td>
                                    <small class="text-muted">{{
                                        mov.observaciones || "-"
                                    }}</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div
                        v-if="paginacion.total > paginacion.per_page"
                        class="d-flex justify-content-between align-items-center mt-3"
                    >
                        <div>
                            Mostrando {{ paginacion.from }} a
                            {{ paginacion.to }} de
                            {{ paginacion.total }} movimientos
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: paginacion.current_page === 1,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        @click="
                                            cambiarPagina(
                                                paginacion.current_page - 1
                                            )
                                        "
                                        href="#"
                                        >Anterior</a
                                    >
                                </li>
                                <li
                                    v-for="pagina in paginasVisibles"
                                    :key="pagina"
                                    class="page-item"
                                    :class="{
                                        active:
                                            pagina === paginacion.current_page,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        @click="cambiarPagina(pagina)"
                                        href="#"
                                        >{{ pagina }}</a
                                    >
                                </li>
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled:
                                            paginacion.current_page ===
                                            paginacion.last_page,
                                    }"
                                >
                                    <a
                                        class="page-link"
                                        @click="
                                            cambiarPagina(
                                                paginacion.current_page + 1
                                            )
                                        "
                                        href="#"
                                        >Siguiente</a
                                    >
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "inventario-movimientos",
    data() {
        return {
            movimientos: [],
            filtros: {
                tipo: "",
                fecha_desde: "",
                fecha_hasta: "",
            },
            paginacion: {
                current_page: 1,
                last_page: 1,
                per_page: 50,
                total: 0,
                from: 0,
                to: 0,
            },
            loading: false,
        };
    },
    computed: {
        paginasVisibles() {
            const paginas = [];
            const total = this.paginacion.last_page;
            const actual = this.paginacion.current_page;

            let inicio = Math.max(1, actual - 2);
            let fin = Math.min(total, actual + 2);

            for (let i = inicio; i <= fin; i++) {
                paginas.push(i);
            }

            return paginas;
        },
    },
    mounted() {
        console.log("Componente inventario-movimientos montado");
        this.cargarMovimientos();
    },
    methods: {
        async cargarMovimientos(pagina = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                params.append("page", pagina);
                if (this.filtros.tipo) params.append("tipo", this.filtros.tipo);
                if (this.filtros.fecha_desde)
                    params.append("fecha_desde", this.filtros.fecha_desde);
                if (this.filtros.fecha_hasta)
                    params.append("fecha_hasta", this.filtros.fecha_hasta);

                console.log(
                    "GET /api/inventario/movimientos?" + params.toString()
                );
                const response = await axios.get(
                    `/api/inventario/movimientos?${params.toString()}`
                );
                console.log("Respuesta completa:", response);

                // Detectar estructura: paginator de Laravel (response.data.data) o array
                let items = [];
                if (
                    response.data &&
                    response.data.data &&
                    Array.isArray(response.data.data)
                ) {
                    items = response.data.data;
                    // poblar paginación desde la respuesta del paginator
                    this.paginacion = {
                        current_page: response.data.current_page || 1,
                        last_page: response.data.last_page || 1,
                        per_page: response.data.per_page || 50,
                        total: response.data.total || 0,
                        from: response.data.from || 0,
                        to: response.data.to || 0,
                    };
                } else if (Array.isArray(response.data)) {
                    items = response.data;
                    this.paginacion = {
                        current_page: 1,
                        last_page: 1,
                        per_page: items.length
                            ? items.length
                            : this.paginacion.per_page,
                        total: items.length,
                        from: items.length ? 1 : 0,
                        to: items.length,
                    };
                } else if (
                    response.data &&
                    Array.isArray(response.data.movimientos)
                ) {
                    items = response.data.movimientos;
                } else {
                    // caso no esperado: intentar tomar response.data como colección única
                    console.warn(
                        "Estructura inesperada en response.data; intentando usar response.data"
                    );
                    if (response.data && typeof response.data === "object") {
                        // Si es objeto con keys, intentar convertir en array si es posible
                        items = Array.isArray(response.data)
                            ? response.data
                            : [];
                    }
                }

                // Logging para depuración: revisar primer elemento
                if (items.length) {
                    console.log(
                        "Primer movimiento recibido (raw):",
                        JSON.parse(JSON.stringify(items[0]))
                    );
                } else {
                    console.log("No se recibieron items desde el backend.");
                }

                // Normalizar cada movimiento y crear 'display_date'
                this.movimientos = items.map((mov) => {
                    // buscar la primera fecha válida posible
                    const posiblesFechas = [
                        mov.created_at,
                        mov.fecha,
                        mov.fecha_compra,
                        mov.fecha_venta,
                        mov.fecha_movimiento,
                        mov.createdAt,
                        mov.updated_at,
                    ];

                    // encontrar candidato no vacío
                    const candidato = posiblesFechas.find(
                        (f) =>
                            f !== undefined &&
                            f !== null &&
                            String(f).trim() !== ""
                    );
                    let display_date = "-";

                    if (candidato) {
                        // Si es numérico (timestamp)
                        if (!isNaN(Number(candidato))) {
                            const dnum = new Date(Number(candidato));
                            if (!Number.isNaN(dnum.getTime())) {
                                display_date = dnum.toLocaleString("es-MX", {
                                    year: "numeric",
                                    month: "2-digit",
                                    day: "2-digit",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                });
                            } else {
                                display_date = String(candidato);
                            }
                        } else {
                            const d = new Date(candidato);
                            if (!Number.isNaN(d.getTime())) {
                                display_date = d.toLocaleString("es-MX", {
                                    year: "numeric",
                                    month: "2-digit",
                                    day: "2-digit",
                                    hour: "2-digit",
                                    minute: "2-digit",
                                });
                            } else {
                                display_date = String(candidato);
                            }
                        }
                    }

                    // Garantizar que mov tenga producto y user para evitar errores en template
                    mov.producto =
                        mov.producto || mov.producto_id
                            ? {
                                  nombre:
                                      mov.producto?.nombre ||
                                      mov.producto_nombre ||
                                      "—",
                              }
                            : mov.producto || null;
                    mov.user =
                        mov.user || mov.user_id
                            ? { name: mov.user?.name || mov.user_name || "-" }
                            : mov.user || null;

                    // Añadir campo display_date (ya formateado)
                    mov.display_date = display_date;

                    return mov;
                });

                console.log("Movimientos procesados:", this.movimientos.length);
            } catch (error) {
                console.error("Error al cargar movimientos:", error);
                console.error("Error.response:", error.response);
                const message =
                    error.response?.data?.message ||
                    "Error al cargar los movimientos. Revisa servidor/console.";
                // mostrar alerta ligera
                alert(message);
            } finally {
                this.loading = false;
            }
        },

        cambiarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.paginacion.last_page) {
                this.cargarMovimientos(pagina);
            }
        },

        limpiarFiltros() {
            this.filtros = {
                tipo: "",
                fecha_desde: "",
                fecha_hasta: "",
            };
            this.cargarMovimientos();
        },

        // ya no necesitamos formatearFecha en template porque usamos mov.display_date,
        // pero dejo una función util por si la quieres usar en otros lugares
        formatearFechaRaw(valor) {
            if (!valor) return "-";
            const d = new Date(valor);
            if (!Number.isNaN(d.getTime())) {
                return d.toLocaleString("es-MX", {
                    year: "numeric",
                    month: "2-digit",
                    day: "2-digit",
                    hour: "2-digit",
                    minute: "2-digit",
                });
            }
            return String(valor);
        },
    },
};
</script>

<style scoped>
.page-link {
    cursor: pointer;
}
</style>
