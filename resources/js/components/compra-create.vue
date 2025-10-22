<template>
  <div class="container mt-4">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Nueva Compra</h5>
      </div>
      <div class="card-body">
        <!-- Alerta -->
        <div v-if="alert.show" :class="`alert alert-${alert.type} alert-dismissible fade show`" role="alert">
          {{ alert.message }}
          <button type="button" class="btn-close" @click="alert.show = false"></button>
        </div>

        <form @submit.prevent="guardarCompra">
          <!-- Fecha y Proveedor -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Fecha de Compra</label>
              <input 
                type="date" 
                class="form-control" 
                v-model="form.fecha_compra" 
                :class="{ 'is-invalid': errors.fecha_compra }"
                required
              >
              <div v-if="errors.fecha_compra" class="invalid-feedback">{{ errors.fecha_compra[0] }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Proveedor</label>
              <select 
                class="form-select" 
                v-model="form.proveedor_id" 
                :class="{ 'is-invalid': errors.proveedor_id }"
                required
              >
                <option value="">Seleccionar proveedor</option>
                <option 
                  v-for="proveedor in proveedores" 
                  :key="proveedor.id" 
                  :value="proveedor.id"
                >
                  {{ proveedor.nombre }} - {{ proveedor.empresa || 'Sin empresa' }}
                </option>
              </select>
              <div v-if="errors.proveedor_id" class="invalid-feedback">{{ errors.proveedor_id[0] }}</div>
            </div>
          </div>

          <!-- Agregar Productos -->
          <div class="card mb-3">
            <div class="card-header bg-light">
              <strong>Agregar Productos</strong>
            </div>
            <div class="card-body">
              <div class="row align-items-end">
                <div class="col-md-5">
                  <label class="form-label">Producto</label>
                  <select 
                    class="form-select" 
                    v-model="productoSeleccionado"
                  >
                    <option :value="null">Seleccionar producto...</option>
                    <option 
                      v-for="producto in productos" 
                      :key="producto.id" 
                      :value="producto"
                    >
                      {{ producto.nombre }} (Stock: {{ producto.stock || producto.cantidad || 0 }})
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
                  >
                </div>

                <div class="col-md-3">
                  <label class="form-label">Costo Unitario</label>
                  <input 
                    type="number" 
                    class="form-control" 
                    v-model.number="costoTemporal" 
                    min="0" 
                    step="0.01"
                    placeholder="0.00"
                  >
                </div>

                <div class="col-md-2">
                  <button 
                    type="button"
                    class="btn btn-primary w-100" 
                    @click="agregarProducto"
                    :disabled="!productoSeleccionado || cantidadTemporal <= 0 || costoTemporal <= 0"
                  >
                    <i class="bi bi-plus-circle"></i> Agregar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Lista de Productos Agregados -->
          <div class="mb-3">
            <label class="form-label fw-bold">Productos de la Compra</label>
            <div v-if="errors.productos" class="alert alert-danger">{{ errors.productos[0] }}</div>
            
            <div v-if="form.productos.length === 0" class="alert alert-info">
              <i class="bi bi-info-circle"></i> No hay productos agregados. Use el formulario arriba para agregar productos.
            </div>

            <table v-else class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Producto</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-end">Costo Unitario</th>
                  <th class="text-end">Subtotal</th>
                  <th class="text-center" style="width: 80px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in form.productos" :key="index">
                  <td>{{ item.nombre }}</td>
                  <td class="text-center">{{ item.cantidad }}</td>
                  <td class="text-end">${{ Number(item.costo_unitario).toFixed(2) }}</td>
                  <td class="text-end">${{ (item.cantidad * item.costo_unitario).toFixed(2) }}</td>
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
                  <td class="text-end fs-5">${{ calcularTotal().toFixed(2) }}</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Notas -->
          <div class="mb-3">
            <label class="form-label">Notas (opcional)</label>
            <textarea 
              class="form-control" 
              v-model="form.notas" 
              rows="3"
              placeholder="Notas adicionales sobre la compra..."
            ></textarea>
          </div>

          <!-- Botones -->
          <div class="d-flex gap-2 justify-content-end">
            <a href="/compras" class="btn btn-secondary">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button 
              type="submit" 
              class="btn btn-primary"
              :disabled="loading || form.productos.length === 0 || !form.proveedor_id"
            >
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-check-circle"></i>
              {{ loading ? 'Guardando...' : 'Registrar Compra' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'compra-create',
  data() {
    return {
      form: {
        proveedor_id: '',
        fecha_compra: new Date().toISOString().split('T')[0],
        notas: '',
        productos: [] // { producto_id, nombre, cantidad, costo_unitario }
      },
      proveedores: [],
      productos: [],
      productoSeleccionado: null,
      cantidadTemporal: 1,
      costoTemporal: 0,
      loading: false,
      errors: {},
      alert: { show: false, message: '', type: 'success' }
    };
  },
  mounted() {
    console.log('Componente compra-create montado correctamente');
    this.cargarProveedores();
    this.cargarProductos();
  },
  methods: {
    async cargarProveedores() {
      try {
        console.log('Cargando proveedores...');
        const response = await axios.get('/api/proveedores');
        
        if (Array.isArray(response.data)) {
          this.proveedores = response.data.filter(p => p.activo);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          this.proveedores = response.data.data.filter(p => p.activo);
        } else {
          this.proveedores = [];
        }
        
        console.log('Proveedores cargados:', this.proveedores);
      } catch (error) {
        console.error('Error al cargar proveedores:', error);
        this.showAlert('Error al cargar proveedores', 'danger');
      }
    },
    
    async cargarProductos() {
      try {
        console.log('Cargando productos...');
        const response = await axios.get('/api/productos');
        
        if (Array.isArray(response.data)) {
          this.productos = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          this.productos = response.data.data;
        } else {
          this.productos = [];
        }
        
        console.log('Productos cargados:', this.productos);
      } catch (error) {
        console.error('Error al cargar productos:', error);
        this.showAlert('Error al cargar productos', 'danger');
      }
    },
    
    agregarProducto() {
      if (!this.productoSeleccionado || this.cantidadTemporal <= 0 || this.costoTemporal <= 0) {
        this.showAlert('Por favor complete todos los campos correctamente', 'warning');
        return;
      }

      // Verificar si el producto ya está en la lista
      const existente = this.form.productos.find(p => p.producto_id === this.productoSeleccionado.id);
      
      if (existente) {
        existente.cantidad += this.cantidadTemporal;
        this.showAlert('Cantidad actualizada para el producto existente', 'info');
      } else {
        this.form.productos.push({
          producto_id: this.productoSeleccionado.id,
          nombre: this.productoSeleccionado.nombre,
          cantidad: this.cantidadTemporal,
          costo_unitario: this.costoTemporal  // Cambiado de 'costo' a 'costo_unitario'
        });
        this.showAlert('Producto agregado correctamente', 'primary');
      }
      
      // Limpiar campos
      this.productoSeleccionado = null;
      this.cantidadTemporal = 1;
      this.costoTemporal = 0;
    },
    
    quitarProducto(index) {
      this.form.productos.splice(index, 1);
      this.showAlert('Producto eliminado', 'info');
    },
    
    calcularTotal() {
      return this.form.productos.reduce((total, item) => {
        return total + (item.cantidad * item.costo_unitario);  // Cambiado de 'costo' a 'costo_unitario'
      }, 0);
    },
    
    async guardarCompra() {
      if (!this.form.proveedor_id) {
        this.showAlert('Por favor selecciona un proveedor', 'warning');
        return;
      }

      if (this.form.productos.length === 0) {
        this.showAlert('Debes agregar al menos un producto', 'warning');
        return;
      }

      this.loading = true;
      this.errors = {};
      
      console.log('Datos a enviar:', this.form);
      
      try {
        const response = await axios.post('/api/compras', this.form);
        console.log('Respuesta del servidor:', response.data);
        
        if (response.data.success) {
          this.showAlert('¡Compra registrada exitosamente! El stock ha sido actualizado.', 'primary');
          setTimeout(() => {
            window.location.href = '/compras';
          }, 1500);
        }
      } catch (error) {
        console.error('Error completo:', error);
        console.error('Respuesta del error:', error.response);
        
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          this.showAlert('Por favor corrige los errores en el formulario', 'danger');
        } else {
          const message = error.response?.data?.message || 'Error al guardar la compra';
          this.showAlert(message, 'danger');
        }
      } finally {
        this.loading = false;
      }
    },
    
    showAlert(message, type = 'success') {
      this.alert = { show: true, message, type };
      window.scrollTo({ top: 0, behavior: 'smooth' });
      
      if (type === 'success' || type === 'info') {
        setTimeout(() => this.alert.show = false, 3000);
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