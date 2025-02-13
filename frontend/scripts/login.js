import { BASE_URL } from './config.js';

const app = Vue.createApp({
    data() {
        return {
            username: '',
            password: '',
            message: '',
            showRegister: false,
            registerUsername: '',
            registerPassword: '',
            registerRole: 'operario',
            showLogin: true
        };
    },
    mounted() {
        // Comprobar si venimos de un registro exitoso
        if (localStorage.getItem("registrationSuccess")) {
            this.message = "Registro exitoso. Redirigiendo al login...";
            
            // Eliminar el mensaje después de 2 segundos
            setTimeout(() => {
                this.message = "";
                localStorage.removeItem("registrationSuccess"); // Se elimina para que no vuelva a aparecer
            }, 2000);
        }
    },
    methods: {
        async login() {
            this.message = "";
            try {
                const response = await fetch(BASE_URL + '/loginRegister/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: this.username, password: this.password })
                });
                const data = await response.json();
                
                if (data.success) {
                    localStorage.setItem('user', JSON.stringify(data.user));
                    this.message = 'Login exitoso';

                    // Redirigir según el rol
                    if (data.user.role === 'admin') {
                        window.location.href = "admin.html";  
                    } else {
                        window.location.href = "operario.html";
                    }
                } else {
                    this.message = data.message;
                }
            } catch (error) {
                this.message = "Error al conectar con el servidor";
            }
        },
        async register() {
            this.message = "";
            try {
                const response = await fetch(BASE_URL + '/loginRegister/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        username: this.registerUsername,
                        password: this.registerPassword,
                        role: this.registerRole
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    localStorage.setItem("registrationSuccess", "true"); // Guardamos el estado de registro
                    this.message = "Registro exitoso. Redirigiendo al login...";

                    setTimeout(() => {
                        this.showRegister = false;
                        this.showLogin = true;
                        window.location.href = "index.html"; // Redirigir al login
                    }, 2000);
                } else {
                    this.message = data.message;
                }
            } catch (error) {
                this.message = "Error al conectar con el servidor";
            }
        },
        showRegisterForm() {
            this.message = "";
            this.showRegister = true;
            this.showLogin = false;
        }
    }
});

app.mount('#app');
