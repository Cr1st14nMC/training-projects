<template>
  <div class="container mt-4">
    <div class="card">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Nueva Venta</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="guardarVenta">
          <!-- Fecha de Venta -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Fecha de Venta</label>
              <input 
                type="date" 
                class="form-control" 
                v-model="venta.fecha_venta" 
                required
              >
            </div>
          </div>

          <!-- Productos -->
          <div class="mb-3">
            <label class="form-label fw-bold">Productos</label>
            
            <!-- Debug: Mostrar cantidad de productos cargados -->
            <div class="alert alert-info mb-2">
              <small>Productos disponibles: {{ productos.length }}</small>
            </div>
            
            <div v-for="(item, index) in venta.productos" :key="index" class="row mb-2 align-items-end">
              <div class="col-md-8">
                <label class="form-label">Producto</label>
                <select 
                  class="form-select" 
                  v-model="item.producto_id" 
                  @change="actualizarPrecio(index)"
                  required
                >
                  <option value="">Seleccionar producto</option>
                  <option 
                    v-for="producto in productos" 
                    :key="producto.id" 
                    :value="producto.id"
                  >
                    {{ producto.nombre }} - ${{ producto.precio }}
                  </option>
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label">Precio</label>
                <input 
                  type="text" 
                  class="form-control" 
                  :value="'$' + (item.precio_unitario || 0).toFixed(2)" 
                  readonly
                >
              </div>

              <div class="col-md-1">
                <button 
                  type="button" 
                  class="btn btn-danger btn-sm w-100" 
                  @click="eliminarProducto(index)"
                  v-if="venta.productos.length > 1"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>

            <button 
              type="button" 
              class="btn btn-outline-primary btn-sm mt-2" 
              @click="agregarProducto"
            >
              <i class="bi bi-plus-circle"></i> Agregar otro producto
            </button>
          </div>

          <!-- Observaciones -->
          <div class="mb-3">
            <label class="form-label">Observaciones (opcional)</label>
            <textarea 
              class="form-control" 
              v-model="venta.observaciones" 
              rows="3"
              placeholder="Notas adicionales sobre la venta..."
            ></textarea>
          </div>

          <!-- Total -->
          <div class="row mb-3">
            <div class="col-md-12 text-end">
              <h3 class="text-success mb-0">
                Total: ${{ calcularTotalGeneral().toFixed(2) }}
              </h3>
            </div>
          </div>

          <!-- Botones -->
          <div class="d-flex gap-2 justify-content-end">
            <a href="/ventas" class="btn btn-secondary">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button 
              type="submit" 
              class="btn btn-success"
              :disabled="loading"
            >
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-check-circle"></i>
              {{ loading ? 'Guardando...' : 'Guardar Venta' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VentaCreate',
  data() {
    return {
      venta: {
        fecha_venta: new Date().toISOString().split('T')[0],
        observaciones: '',
        productos: [
          {
            producto_id: '',
            precio_unitario: 0
          }
        ]
      },
      productos: [],
      loading: false
    };
  },
  mounted() {
    this.cargarProductos();
  },
  methods: {
    async cargarProductos() {
      try {
        console.log('Intentando cargar productos...');
        const response = await axios.get('/api/productos');
        console.log('Respuesta completa:', response);
        console.log('Datos recibidos:', response.data);
        
        // Si la respuesta tiene estructura {success: true, data: []}
        if (response.data.data && Array.isArray(response.data.data)) {
          this.productos = response.data.data;
        } else if (Array.isArray(response.data)) {
          this.productos = response.data;
        } else {
          this.productos = [];
        }
        
        console.log('Productos asignados:', this.productos);
        console.log('Cantidad de productos:', this.productos.length);
      } catch (error) {
        console.error('Error completo:', error);
        console.error('Error response:', error.response);
        alert('Error al cargar los productos: ' + (error.response?.data?.message || error.message));
      }
    },
    
    agregarProducto() {
      this.venta.productos.push({
        producto_id: '',
        precio_unitario: 0
      });
    },
    
    eliminarProducto(index) {
      this.venta.productos.splice(index, 1);
    },
    
    actualizarPrecio(index) {
      const productoId = this.venta.productos[index].producto_id;
      const producto = this.productos.find(p => p.id == productoId);
      
      console.log('Producto seleccionado:', producto);
      
      if (producto) {
        this.venta.productos[index].precio_unitario = parseFloat(producto.precio);
        console.log('Precio actualizado:', this.venta.productos[index].precio_unitario);
      }
    },
    
    calcularTotalGeneral() {
      return this.venta.productos.reduce((total, item) => {
        return total + (parseFloat(item.precio_unitario) || 0);
      }, 0);
    },
    
    async guardarVenta() {
      // Validar que todos los productos tengan producto seleccionado
      const productosSinSeleccionar = this.venta.productos.some(p => !p.producto_id);
      if (productosSinSeleccionar) {
        alert('Por favor selecciona un producto para cada ítem');
        return;
      }

      console.log('Datos a enviar:', this.venta);
      console.log('Datos JSON:', JSON.stringify(this.venta, null, 2));

      this.loading = true;
      try {
        const response = await axios.post('/api/ventas', this.venta);
        console.log('Respuesta del servidor:', response.data);
        alert('¡Venta registrada exitosamente!');
        window.location.href = '/ventas';
      } catch (error) {
        console.error('Error completo:', error);
        console.error('Respuesta del error:', error.response);
        console.error('Datos del error:', error.response?.data);
        
        const message = error.response?.data?.message || 'Error al guardar la venta';
        const errors = error.response?.data?.errors;
        const errorDetails = error.response?.data?.error;
        
        let errorMessage = message;
        
        if (errorDetails) {
          errorMessage += '\n\nDetalles técnicos:\n' + errorDetails;
        }
        
        if (errors) {
          const errorMessages = Object.values(errors).flat().join('\n');
          errorMessage += '\n\nErrores de validación:\n' + errorMessages;
        }
        
        alert(errorMessage);
        console.error('Mensaje de error completo:', errorMessage);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.card {
  border-radius: 10px;
}
</style>