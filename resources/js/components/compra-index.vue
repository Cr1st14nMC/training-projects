<template>
  <div class="container mt-4">
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="text-muted mt-3">Cargando compras...</p>
        </div>

        <div v-else-if="compras.length === 0" class="text-center py-5">
          <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
          <p class="text-muted mt-3">No hay compras registradas</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Total</th>
                <th>Productos</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="compra in compras" :key="compra.id">
                <tr>
                  <td>#{{ compra.id }}</td>
                  <td>{{ formatearFecha(compra.fecha_compra) }}</td>
                  <td>
                    <strong>{{ compra.proveedor?.nombre || 'Sin proveedor' }}</strong><br>
                    <small class="text-muted">{{ compra.proveedor?.empresa || '-' }}</small>
                  </td>
                  <td class="text-primary fw-bold">${{ Number(compra.total).toFixed(2) }}</td>
                  <td>
                    <button 
                      class="btn btn-sm btn-outline-info"
                      @click="toggleDetalles(compra.id)"
                    >
                      <i class="bi bi-eye"></i> Ver detalles
                    </button>
                  </td>
                  <td>
                    <button 
                      class="btn btn-sm btn-danger" 
                      @click="eliminarCompra(compra.id)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>

                <tr v-if="compraExpandida === compra.id">
                  <td colspan="6" class="bg-light">
                    <div class="p-3">
                      <h6 class="mb-3">Productos de la compra:</h6>
                      <table class="table table-sm table-bordered">
                        <thead>
                          <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Costo Unitario</th>
                            <th>Subtotal</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="producto in compra.productos" :key="producto.id">
                            <td>{{ producto.nombre }}</td>
                            <td>{{ producto.pivot?.cantidad || '-' }}</td>
                            <td>${{ Number(producto.pivot?.costo_unitario || 0).toFixed(2) }}</td>
                            <td>${{ ((producto.pivot?.cantidad || 0) * (producto.pivot?.costo_unitario || 0)).toFixed(2) }}</td>
                          </tr>
                        </tbody>
                      </table>
                      <div v-if="compra.notas">
                        <strong>Notas:</strong> {{ compra.notas }}
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
  name: 'CompraIndex',
  data() {
    return {
      compras: [],
      compraExpandida: null,
      loading: false
    };
  },
  mounted() {
    console.log('Componente CompraIndex montado correctamente');
    this.cargarCompras();
  },
  methods: {
    async cargarCompras() {
      this.loading = true;
      try {
        console.log('Cargando compras...');
        const response = await axios.get('/api/compras');
        console.log('Respuesta completa:', response);
        console.log('Datos recibidos:', response.data);
        
        // Manejar diferentes estructuras de respuesta
        if (Array.isArray(response.data)) {
          this.compras = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          this.compras = response.data.data;
        } else {
          this.compras = [];
        }
        
        console.log('Compras cargadas:', this.compras);
      } catch (error) {
        console.error('Error al cargar compras:', error);
        alert('Error al cargar las compras');
      } finally {
        this.loading = false;
      }
    },
    
    async eliminarCompra(id) {
      if (!confirm('¿Estás seguro de eliminar esta compra? Esto reducirá el stock de los productos.')) return;
      
      try {
        await axios.delete(`/api/compras/${id}`);
        this.compras = this.compras.filter(c => c.id !== id);
        alert('Compra eliminada exitosamente');
      } catch (error) {
        console.error('Error al eliminar compra:', error);
        alert('Error al eliminar la compra');
      }
    },
    
    toggleDetalles(id) {
      this.compraExpandida = this.compraExpandida === id ? null : id;
    },
    
   formatearFecha(fecha) {
    const options = {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true 
    };
    
    // ✅ CAMBIO AQUÍ:
    // Quita "Date" para que sea "toLocaleString"
    return new Date(fecha).toLocaleString('es-MX', options);
  }
  }
};
</script>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f8f9fa;
}
</style>