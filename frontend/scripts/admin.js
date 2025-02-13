import { BASE_URL } from './config.js';

const app = Vue.createApp({
    data() {
        return {
            vehiculos: [],
            users: [],
            logs: [],
            filterMatricula: '',
            filterFecha: '',
            filterEstado: '',
            newMatricula: '',
            newFechaEntrada: '',
            newEstado: 'En depósito',
            newUsername: '',
            newPassword: '',
            newRole: 'operario',
            showUpdateForm: false,
            updateVehiculo: {},
            showLogs: false, // Para controlar la visualización de logs
        };
    },
    computed: {
        filteredVehiculos() {
            return this.vehiculos.filter(vehiculo =>
                (!this.filterMatricula || vehiculo.matricula.includes(this.filterMatricula)) &&
                (!this.filterFecha || vehiculo.fechaentrada.startsWith(this.filterFecha)) &&
                (!this.filterEstado || vehiculo.estado === this.filterEstado)
            );
        }
    },
    methods: {
        // Obtener los vehículos
        async getVehiculos() {
            try {
                const response = await fetch(BASE_URL + '/admin/vehiculos.php');
                if (!response.ok) throw new Error(`Error ${response.status}: ${response.statusText}`);
                this.vehiculos = await response.json();
            } catch (error) {
                console.error("Error al obtener vehículos retirados:", error);
            }
        },

        // Registrar un nuevo vehículo
        async registerVehiculo() {
            if (!this.newMatricula || !this.newFechaEntrada) {
                alert("Por favor, completa todos los campos.");
                return;
            }
            try {
                const response = await fetch(BASE_URL + '/admin/vehiculos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        matricula: this.newMatricula,
                        fechaentrada: this.newFechaEntrada,
                        estado: this.newEstado
                    })
                });

                const data = await response.json();
                if (data.success) {
                    alert("Vehículo registrado con éxito.");
                    this.getVehiculos();
                    this.newMatricula = '';
                    this.newFechaEntrada = '';
                    this.newEstado = 'En depósito';
                } else {
                    alert("Error al registrar vehículo.");
                }
            } catch (error) {
                console.error("Error al registrar vehículo:", error);
            }
        },

        // Editar vehículo
        editVehiculo(vehiculo) {
            this.updateVehiculo = { ...vehiculo };
            this.showUpdateForm = true;
        },

        // Actualizar vehículo
        async updateVehiculoInfo() {
            if (!this.updateVehiculo.fechasalida) {
                alert("Debes ingresar una fecha de salida.");
                return;
            }
            try {
                const response = await fetch(BASE_URL + '/admin/vehiculos.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.updateVehiculo)
                });

                const data = await response.json();
                if (data.success) {
                    alert("Vehículo actualizado correctamente.");
                    this.getVehiculos();
                    this.showUpdateForm = false;
                } else {
                    alert("Error al actualizar vehículo.");
                }
            } catch (error) {
                console.error("Error al actualizar vehículo:", error);
            }
        },

        // Eliminar vehículo
        async deleteVehiculo(id) {
            if (!confirm("¿Estás seguro de eliminar este vehículo?")) return;

            try {
                const response = await fetch(BASE_URL + '/admin/vehiculos.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const data = await response.json();
                if (data.success) {
                    alert("Vehículo eliminado correctamente.");
                    this.getVehiculos();
                } else {
                    alert("Error al eliminar vehículo.");
                }
            } catch (error) {
                console.error("Error al eliminar vehículo:", error);
            }
        },

        // Obtener usuarios
        async getUsers() {
            try {
                const response = await fetch(BASE_URL + '/admin/users.php');
                if (!response.ok) throw new Error(`Error ${response.status}: ${response.statusText}`);
                this.users = await response.json();
            } catch (error) {
                console.error("Error al obtener usuarios:", error);
            }
        },

        // Registrar un nuevo usuario
        async registerUser() {
            if (!this.newUsername || !this.newPassword) {
                alert("Por favor, completa todos los campos.");
                return;
            }
            try {
                const response = await fetch(BASE_URL + '/admin/users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        username: this.newUsername,
                        password: this.newPassword,
                        role: this.newRole
                    })
                });

                const data = await response.json();
                if (data.success) {
                    alert("Usuario registrado correctamente.");
                    this.getUsers();
                    this.newUsername = '';
                    this.newPassword = '';
                    this.newRole = 'operario';
                } else {
                    alert("Error al registrar usuario.");
                }
            } catch (error) {
                console.error("Error al registrar usuario:", error);
            }
        },

        // Eliminar usuario
        async deleteUser(id) {
            if (!confirm("¿Estás seguro de eliminar este usuario?")) return;

            try {
                const response = await fetch(BASE_URL + '/admin/users.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const data = await response.json();
                if (data.success) {
                    alert("Usuario eliminado correctamente.");
                    this.getUsers();
                } else {
                    alert("Error al eliminar usuario.");
                }
            } catch (error) {
                console.error("Error al eliminar usuario:", error);
            }
        },

        // Obtener logs (actividad y sesiones)
        async getLogs(logType) {
            try {
                let response;
                if (logType === 'vehiculo') {
                    response = await fetch(BASE_URL + '/admin/logs.php?type=vehiculo');
                } else if (logType === 'sesion') {
                    response = await fetch(BASE_URL + '/admin/logs.php?type=sesion');
                }

                if (!response.ok) throw new Error(`Error ${response.status}: ${response.statusText}`);
                this.logs = await response.json();
            } catch (error) {
                console.error("Error al obtener logs:", error);
            }
        },

        // Cambiar entre logs de actividad y de sesión
        verLogsTipo(tipo) {
            this.showLogs = true;
            this.getLogs(tipo);
        },

        // Ver usuarios
        verUsuarios() {
            window.location.href = "usuarios.html";
        },

        // Ver logs
        verLogs() {
            window.location.href = "logs.html";
        },
    },
    mounted() {
        this.getVehiculos();
        this.getUsers();
        this.getLogs('vehiculo'); // Cargar logs de vehículos por defecto
    }
});

app.mount('#app');
