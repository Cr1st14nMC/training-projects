<template>
  <div>
    <form @submit.prevent="handleRegister">

      <div class="form-floating mb-3">
        <input 
          id="name" 
          type="text" 
          class="form-control" 
          v-model="form.name" 
          required 
          autocomplete="name" 
          autofocus 
          placeholder="Tu Nombre"
        >
        <label for="name">Nombre</label>
      </div>

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
          autocomplete="new-password" 
          placeholder="Contraseña"
        >
        <label for="password">Contraseña</label>
      </div>

      <div class="form-floating mb-3">
        <input 
          id="password-confirm" 
          type="password" 
          class="form-control" 
          v-model="form.password_confirmation" 
          required 
          autocomplete="new-password" 
          placeholder="Confirmar Contraseña"
        >
        <label for="password-confirm">Confirmar Contraseña</label>
      </div>

      <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          {{ loading ? 'Registrando...' : 'Registrarse' }}
        </button>
        <a href="#" @click.prevent="$emit('show-login')" class="btn btn-link text-center">
          ¿Ya tienes una cuenta? Inicia Sesión
        </a>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'RegisterForm',
  emits: ['show-login'],
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      loading: false
    };
  },
  methods: {
    async handleRegister() {
      this.loading = true;
      try {
        // Primero obtener el cookie CSRF
        await axios.get('/sanctum/csrf-cookie');
        
        // Luego registrar
        const response = await axios.post('/api/register', this.form);
        console.log('¡Registro exitoso!', response.data);
        
        alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
        this.$emit('show-login');

      } catch (error) {
        console.error('Error en registro:', error.response?.data);
        const errors = error.response?.data?.errors;
        let errorMessage = error.response?.data?.message || 'Hubo un problema con tu registro.';
        
        if (errors) {
          errorMessage = Object.values(errors).flat().join('\n');
        }
        alert(errorMessage);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>