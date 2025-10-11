<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <a href="/ventas/create" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nueva Venta
      </a>
    </div>

    <!-- Lista de ventas -->
    <div class="card">
      <div class="card-body">
        <div v-if="ventas.length === 0" class="text-center py-5">
          <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
          <p class="text-muted mt-3">No hay ventas registradas</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Total</th>
                <th>Productos</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="venta in ventas" :key="venta.id">
                <tr>
                  <td>{{ venta.id }}</td>
                  <td>{{ formatearFecha(venta.fecha_venta) }}</td>
                  <td>{{ venta.user.name }}</td>
                  <td class="text-success fw-bold">${{ venta.total }}</td>
                  <td>
                    <button 
                      class="btn btn-sm btn-outline-info"
                      @click="toggleDetalles(venta.id)"
                    >
                      <i class="bi bi-eye"></i> Ver Detalles
                    </button>
                  </td>
                  <td>
                    <button 
                      class="btn btn-sm btn-danger" 
                      @click="eliminarVenta(venta.id)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>

                <tr v-if="ventaExpandida === venta.id">
                  <td colspan="6" class="bg-light">
                    <div class="p-3">
                      <h6 class="mb-3">Productos de la venta:</h6>
                      <table class="table table-sm table-bordered">
                        <thead>
                          <tr>
                            <th>Producto</th>
                            <th>Precio Unit.</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="detalle in venta.detalles" :key="detalle.id">
                            <td>{{ detalle.producto.nombre }}</td>
                            <td>${{ detalle.precio_unitario }}</td>
                          </tr>
                        </tbody>
                      </table>
                      <div v-if="venta.observaciones">
                        <strong>Observaciones:</strong> {{ venta.observaciones }}
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VentaIndex',
  data() {
    return {
      ventas: [],
      ventaExpandida: null
    };
  },
  mounted() {
    this.cargarVentas();
  },
  methods: {
    async cargarVentas() {
      try {
        const response = await axios.get('/api/ventas');
        this.ventas = response.data;
      } catch (error) {
        console.error('Error al cargar ventas:', error);
        alert('Error al cargar las ventas');
      }
    },
    
    async eliminarVenta(id) {
      if (!confirm('¿Estás seguro de eliminar esta venta?')) return;
      
      try {
        await axios.delete(`/api/ventas/${id}`);
        this.ventas = this.ventas.filter(v => v.id !== id);
        alert('Venta eliminada exitosamente');
      } catch (error) {
        console.error('Error al eliminar venta:', error);
        alert('Error al eliminar la venta');
      }
    },
    
    toggleDetalles(id) {
      this.ventaExpandida = this.ventaExpandida === id ? null : id;
    },
    
    formatearFecha(fecha) {
      return new Date(fecha).toLocaleDateString('es-MX');
    }
  }
};
</script>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f8f9fa;
}
</style>