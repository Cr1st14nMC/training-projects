<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-8">
        <div class="card shadow-lg border-0 rounded-lg">
          <div class="card-header bg-primary text-white text-center">
            <h3 class="fw-light my-4">{{ isRegistering ? 'Crear Cuenta' : 'Iniciar Sesión' }}</h3>
          </div>
          <div class="card-body p-4">
            <Login 
              v-if="!isRegistering" 
              @show-register="showRegisterForm" 
            />
            <Register 
              v-else 
              @show-login="showLoginForm" 
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// 1. Importamos los dos componentes que creamos
import Login from '../components/componente-login.vue';
import Register from '../components/componente-registro.vue';

export default {
  name: 'auth-page',
  components: {
    Login,
    Register,
  },
  data() {
    return {
      // Este estado controla qué formulario se muestra
      isRegistering: false,
    };
  },
  methods: {
    // Este método se activa cuando el componente Login emite 'show-register'
    showRegisterForm() {
      this.isRegistering = true;
    },
    // Este método se activa cuando el componente Register emite 'show-login'
    showLoginForm() {
      this.isRegistering = false;
    },
  },
};
</script>

<style scoped>
.card {
  border-radius: 40px;
  min-height: 300px;
}

.card-body {
  min-height: 350px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.min-vh-100 {
  min-height: 100vh;
}
</style>