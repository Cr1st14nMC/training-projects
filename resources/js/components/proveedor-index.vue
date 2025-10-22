<template>
  <div class="container mt-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
      <input 
        v-model="q" 
        @input="onSearch" 
        class="form-control form-control-sm" 
        placeholder="Buscar por nombre, empresa, RFC..." 
        style="max-width: 400px;"
      >
      <a href="/proveedores/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i> Agregar Proveedor
      </a>
    </div>

    <!-- Alerta -->
    <div v-if="alert.show" :class="`alert alert-${alert.type} alert-dismissible fade show`" role="alert">
      {{ alert.message }}
      <button type="button" class="btn-close" @click="alert.show = false"></button>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="text-muted mt-3">Cargando proveedores...</p>
        </div>

        <div v-else-if="proveedores.length === 0" class="text-center py-5">
          <i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
          <p class="text-muted mt-3">No se encontraron proveedores</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Empresa</th>
                <th>Teléfono</th>
                <th>RFC</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="prov in proveedores" :key="prov.id">
                <td>{{ prov.id }}</td>
                <td>
                  {{ prov.nombre }}<br>
                  <small class="text-muted">{{ prov.email || 'Sin email' }}</small>
                </td>
                <td>{{ prov.empresa || 'N/A' }}</td>
                <td>{{ prov.telefono || 'N/A' }}</td>
                <td>{{ prov.rfc || 'N/A' }}</td>
                <td>
                  <span v-if="prov.activo" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td>
                  <button @click="editarProveedor(prov.id)" class="btn btn-warning btn-sm me-1" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button @click="confirmarEliminar(prov.id)" class="btn btn-danger btn-sm" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
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
  name: 'ProveedorIndex',
  data() {
    return {
      proveedores: [],
      q: '',
      loading: false,
      loadingUpdate: false,
      proveedorEdit: {},
      alert: { show: false, message: '', type: 'success' },
      searchTimeout: null
    };
  },
  mounted() {
    console.log('Componente ProveedorIndex montado');
    this.fetchProveedores();
  },
  methods: {
    async fetchProveedores() {
      this.loading = true;
      try {
        console.log('Intentando cargar proveedores...');
        const res = await axios.get('/api/proveedores', {
          params: { q: this.q }
        });
        console.log('Respuesta:', res.data);
        
        // Manejar diferentes estructuras de respuesta
        if (Array.isArray(res.data)) {
          this.proveedores = res.data;
        } else if (res.data.data && Array.isArray(res.data.data)) {
          this.proveedores = res.data.data;
        } else {
          this.proveedores = [];
        }
        
        console.log('Proveedores cargados:', this.proveedores);
      } catch (err) {
        console.error('Error al cargar proveedores:', err);
        this.showAlert('Error al cargar proveedores', 'danger');
      } finally {
        this.loading = false;
      }
    },
    
    onSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => this.fetchProveedores(), 300);
    },
    
    editarProveedor(id) {
      window.location.href = `/proveedores/${id}/edit`;
    },
    
    async actualizarProveedor() {
      this.loadingUpdate = true;
      try {
        await axios.put(`/api/proveedores/${this.proveedorEdit.id}`, this.proveedorEdit);
        this.showAlert('Proveedor actualizado exitosamente', 'success');
        this.fetchProveedores();
      } catch (error) {
        console.error('Error al actualizar proveedor:', error);
        this.showAlert('Error al actualizar el proveedor', 'danger');
      } finally {
        this.loadingUpdate = false;
      }
    },
    
    async confirmarEliminar(id) {
      if (!confirm('¿Estás seguro de eliminar este proveedor?')) return;
      
      try {
        await axios.delete(`/api/proveedores/${id}`);
        this.proveedores = this.proveedores.filter(p => p.id !== id);
        this.showAlert('Proveedor eliminado exitosamente', 'success');
      } catch (error) {
        console.error('Error al eliminar proveedor:', error);
        this.showAlert('Error al eliminar el proveedor', 'danger');
      }
    },
    
    
    showAlert(message, type = 'success') {
      this.alert = { show: true, message, type };
      setTimeout(() => this.alert.show = false, 4000);
    }
  }
};
</script>
