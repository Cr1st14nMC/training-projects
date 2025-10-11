<template>
  <div>
    <form @submit.prevent="handleLogin">
      
      <div class="form-floating mb-3">
        <input 
          id="email" 
          type="email" 
          class="form-control" 
          v-model="form.email" 
          required 
          autocomplete="email" 
          placeholder="nombre@ejemplo.com"
        >
        <label for="email">Correo Electrónico</label>
      </div>

      <div class="form-floating mb-3">
        <input 
          id="password" 
          type="password" 
          class="form-control" 
          v-model="form.password" 
          required 
          autocomplete="current-password" 
          placeholder="Contraseña"
        >
        <label for="password">Contraseña</label>
      </div>

      <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          {{ loading ? 'Iniciando...' : 'Iniciar Sesión' }}
        </button>
        <a href="#" @click.prevent="$emit('show-register')" class="btn btn-link text-center">
          ¿No tienes cuenta? Regístrate
        </a>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'LoginForm',
  emits: ['show-register'],
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      loading: false
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      try {
        // Primero obtener el cookie CSRF
        await axios.get('/sanctum/csrf-cookie');
        
        // Luego hacer login
        const response = await axios.post('/api/login', this.form);
        console.log('¡Login exitoso!', response.data);

        
        // Redirigir a productos
        window.location.href = '/productos';
      
      } catch (error) {
        console.error('Error en login:', error.response?.data);
        const message = error.response?.data?.message || 'Las credenciales son incorrectas';
        alert(message);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style >
.form-control {
  border-radius: 0.5rem;
}
</style>