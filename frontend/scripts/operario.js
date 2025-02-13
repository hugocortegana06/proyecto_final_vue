import { BASE_URL } from './config.js';

const app = Vue.createApp({
  data() {
    return {
      // Control de vistas
      vista: 'vehiculos',

      // Datos de vehículos y retiradas
      vehiculos: [],
      retiradas: [],

      // Modal de edición de Vehículos
      mostrarFormularioEdicion: false,
      vehiculoSeleccionado: {
        id: '',
        fechaentrada: '',
        fechasalida: '',
        lugar: '',
        direccion: '',
        agente: '',
        matricula: '',
        marca: '',
        modelo: '',
        color: '',
        motivo: '',
        tipovehiculo: '',
        grua: '',
        estado: ''
      },

      // Objeto para la Retirada (se usará dentro del modal de edición de vehículo)
      retiradaSeleccionada: {
        idvehiculos: '',
        nombre: '',
        nif: '',
        domicilio: '',
        poblacion: '',
        provincia: '',
        permiso: '',
        fecha: '',
        agente: '',
        importeretirada: 0,
        importedeposito: 0,
        total: 0,
        opcionespago: ''
      },

      // Modal para agregar nuevo vehículo
      mostrarFormularioNuevoVehiculo: false,
      nuevoVehiculo: {
        fechaentrada: '', // Se inicializará en created
        fechasalida: '',
        lugar: '',
        direccion: '',
        agente: '',
        matricula: '',
        marca: '',
        modelo: '',
        color: '',
        motivo: '',
        tipovehiculo: '',
        grua: '',
        estado: 'En depósito'
      },

      // Modal para agregar nueva retirada (vista de retiradas)
      mostrarFormularioNuevaRetirada: false,
      nuevaRetirada: {
        idvehiculos: '',
        nombre: '',
        nif: '',
        domicilio: '',
        poblacion: '',
        provincia: '',
        permiso: '',
        fecha: '',
        agente: '',
        importeretirada: '',
        importedeposito: '',
        total: '',
        opcionespago: ''
      }
    };
  },
  created() {
    // Asignar la fecha y hora actual al nuevo vehículo
    this.nuevoVehiculo.fechaentrada = this.getFechaHoraActual();
  },
  methods: {
    // Método para obtener la fecha y hora actuales (formato YYYY-MM-DDTHH:mm)
    getFechaHoraActual() {
      const now = new Date();
      const offset = now.getTimezoneOffset() * 60000;
      const localTime = new Date(now.getTime() - offset);
      return localTime.toISOString().slice(0, 16);
    },

    // ===========================
    // Métodos de Vehículos
    // ===========================
    async fetchVehiculos() {
      try {
        const response = await fetch(`${BASE_URL}/operario/vehiculos.php`);
        this.vehiculos = await response.json();

        this.$nextTick(() => {
          $('#vehiculosTable').DataTable({
            destroy: true,
            data: this.vehiculos,
            columns: [
              { data: 'id' },
              { data: 'fechaentrada' },
              { data: 'matricula' },
              { data: 'marca' },
              { data: 'modelo' },
              { data: 'lugar' },
              { data: 'direccion' },
              { data: 'tipovehiculo' },
              { data: 'estado' },
              {
                data: 'id',
                render: (data, type, row) => {
                  return `
                    <button class="edit-btn action-btn" onclick="editarVehiculo('${data}')">Modificar</button>
                    <button class="delete-btn action-btn" onclick="eliminarVehiculo('${data}')">Eliminar</button>
                  `;
                }
              }
            ]
          });
        });
      } catch (error) {
        console.error('Error al obtener vehículos:', error);
      }
    },

    // Al cambiar el estado en el modal de edición, si es "Liquidado" se inicializa la retirada
    onEstadoChange() {
      if (this.vehiculoSeleccionado.estado === 'Liquidado') {
        // Si ya se conoce el ID del vehículo, se asigna al campo correspondiente
        this.retiradaSeleccionada.idvehiculos = this.vehiculoSeleccionado.id;
      } else {
        // Si se cambia a otro estado, se limpian los datos de retirada
        this.retiradaSeleccionada = {
          idvehiculos: '',
          nombre: '',
          nif: '',
          domicilio: '',
          poblacion: '',
          provincia: '',
          permiso: '',
          fecha: '',
          agente: '',
          importeretirada: 0,
          importedeposito: 0,
          total: 0,
          opcionespago: ''
        };
      }
    },

    // Cargar datos de un vehículo para edición
    async cargarVehiculoParaEdicion(id) {
      try {
        const resp = await fetch(`${BASE_URL}/operario/obtenerVehiculo.php?id=${id}`);
        const result = await resp.json();

        if (result.success) {
          this.vehiculoSeleccionado = { ...result.vehiculo };
          // Si el vehículo está liquidado, asignamos su ID a la retirada
          if (this.vehiculoSeleccionado.estado === 'Liquidado') {
            this.retiradaSeleccionada.idvehiculos = this.vehiculoSeleccionado.id;
          }
          this.mostrarFormularioEdicion = true;
        } else {
          alert(result.message || 'No se pudo obtener el vehículo.');
        }
      } catch (error) {
        console.error('Error al obtener vehículo:', error);
      }
    },

    // Guardar cambios del vehículo y, si está liquidado, registrar la retirada asociada
    async guardarVehiculo() {
      try {
        const resp = await fetch(`${BASE_URL}/operario/modificarVehiculo.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.vehiculoSeleccionado)
        });
        const result = await resp.json();

        if (result.success) {
          if (this.vehiculoSeleccionado.estado === 'Liquidado') {
            await this.registrarRetirada();
          }
          alert('Vehículo actualizado correctamente');
          this.fetchVehiculos();
          this.cerrarFormularioEdicion();
        } else {
          alert('Error al modificar el vehículo');
        }
      } catch (error) {
        console.error('Error al guardar vehículo:', error);
      }
    },

    // Registrar la retirada (desde el modal de edición de vehículo)
    async registrarRetirada() {
      try {
        const response = await fetch(`${BASE_URL}/operario/insertarRetirada.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.retiradaSeleccionada)
        });
        const result = await response.json();

        if (result.success) {
          alert('Retirada registrada correctamente');
        } else {
          alert('Error al registrar la retirada');
        }
      } catch (error) {
        console.error('Error al registrar la retirada:', error);
      }
    },

    // Cerrar el formulario de edición de vehículo y reiniciar los datos
    cerrarFormularioEdicion() {
      this.mostrarFormularioEdicion = false;
      this.vehiculoSeleccionado = {
        id: '',
        fechaentrada: '',
        fechasalida: '',
        lugar: '',
        direccion: '',
        agente: '',
        matricula: '',
        marca: '',
        modelo: '',
        color: '',
        motivo: '',
        tipovehiculo: '',
        grua: '',
        estado: ''
      };
      this.retiradaSeleccionada = {
        idvehiculos: '',
        nombre: '',
        nif: '',
        domicilio: '',
        poblacion: '',
        provincia: '',
        permiso: '',
        fecha: '',
        agente: '',
        importeretirada: 0,
        importedeposito: 0,
        total: 0,
        opcionespago: ''
      };
    },

    // ===========================
    // Métodos de Retiradas (vista de retiradas)
    // ===========================
    async fetchRetiradas() {
      try {
        const response = await fetch(`${BASE_URL}/operario/vehiculos_retirados.php`);
        this.retiradas = await response.json();

        this.$nextTick(() => {
          $('#retiradasTable').DataTable({
            destroy: true,
            data: this.retiradas,
            columns: [
              { data: 'idvehiculos' },
              { data: 'nombre' },
              { data: 'nif' },
              { data: 'domicilio' },
              { data: 'poblacion' },
              { data: 'provincia' },
              { data: 'permiso' },
              { data: 'fecha' },
              { data: 'agente' },
              { data: 'importeretirada' },
              { data: 'importedeposito' },
              { data: 'total' },
              { data: 'opcionespago' },
              {
                data: 'idvehiculos',
                render: (data, type, row) => {
                  return `
                    <button class="edit-btn action-btn" onclick="editarRetirada('${data}')">Modificar</button>
                    <button class="delete-btn action-btn" onclick="eliminarRetirada('${data}')">Eliminar</button>
                  `;
                }
              }
            ]
          });
        });
      } catch (error) {
        console.error('Error al obtener retiradas:', error);
      }
    },

    // Mostrar el formulario para agregar un nuevo vehículo
    // (se activa directamente asignando a la variable en el HTML)
    
    // Guardar el nuevo vehículo
    async guardarNuevoVehiculo() {
      try {
        if (!this.nuevoVehiculo.id) {
          delete this.nuevoVehiculo.id;
        }

        const response = await fetch(`${BASE_URL}/operario/insertarVehiculo.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.nuevoVehiculo)
        });
        const data = await response.json();
        if (data.success) {
          alert("Vehículo agregado correctamente");
          this.fetchVehiculos();
          this.cerrarFormularioNuevoVehiculo();
        } else {
          alert("Error al agregar el vehículo");
        }
      } catch (error) {
        console.error("Error al guardar vehículo:", error);
      }
    },

    // Cerrar el formulario de nuevo vehículo y reiniciar los campos
    cerrarFormularioNuevoVehiculo() {
      this.mostrarFormularioNuevoVehiculo = false;
      this.nuevoVehiculo = {
        fechaentrada: this.getFechaHoraActual(),
        fechasalida: '',
        lugar: '',
        direccion: '',
        agente: '',
        matricula: '',
        marca: '',
        modelo: '',
        color: '',
        motivo: '',
        tipovehiculo: '',
        grua: '',
        estado: 'En depósito'
      };
    },

    // Mostrar el formulario para agregar una nueva retirada (vista de retiradas)
    // (se activa directamente asignando a la variable en el HTML)

    // Guardar la nueva retirada
    async guardarNuevaRetirada() {
      try {
        const response = await fetch(`${BASE_URL}/operario/insertarRetirada.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.nuevaRetirada)
        });
        const data = await response.json();
        if (data.success) {
          alert("Retirada agregada correctamente");
          this.fetchRetiradas();
          this.cerrarFormularioNuevaRetirada();
        } else {
          alert("Error al agregar la retirada");
        }
      } catch (error) {
        console.error("Error al guardar retirada:", error);
      }
    },

    // Cerrar el formulario de nueva retirada y reiniciar los campos
    cerrarFormularioNuevaRetirada() {
      this.mostrarFormularioNuevaRetirada = false;
      this.nuevaRetirada = {
        idvehiculos: '',
        nombre: '',
        nif: '',
        domicilio: '',
        poblacion: '',
        provincia: '',
        permiso: '',
        fecha: '',
        agente: '',
        importeretirada: '',
        importedeposito: '',
        total: '',
        opcionespago: ''
      };
    }
  },

  watch: {
    vista(nuevaVista) {
      if (nuevaVista === 'vehiculos') {
        this.fetchVehiculos();
      } else if (nuevaVista === 'retiradas') {
        this.fetchRetiradas();
      }
    }
  },

  mounted() {
    this.fetchVehiculos();
  }
});

// Montar la aplicación en #app
const vm = app.mount('#app');

// Funciones GLOBALES para la vista de vehículos
window.editarVehiculo = function(id) {
  vm.cargarVehiculoParaEdicion(id);
};

window.eliminarVehiculo = function(id) {
  if (confirm(`¿Estás seguro de que deseas eliminar el vehículo con ID ${id}?`)) {
    fetch(`${BASE_URL}/operario/eliminarVehiculo.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          alert('Vehículo eliminado correctamente.');
          vm.fetchVehiculos();
        } else {
          alert('Error al eliminar el vehículo.');
        }
      })
      .catch(error => console.error('Error:', error));
  }
};

// Funciones GLOBALES para la vista de retiradas
window.editarRetirada = function(idvehiculos) {
  // Aquí podrías implementar la edición de retiradas en la vista correspondiente.
  alert('Función editarRetirada no implementada aún.');
};

window.eliminarRetirada = function(idvehiculos) {
  if (confirm(`¿Estás seguro de que deseas eliminar la retirada asociada al vehículo con ID ${idvehiculos}?`)) {
    fetch(`${BASE_URL}/operario/eliminarRetirada.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ idvehiculos })
    })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          alert('Retirada eliminada correctamente.');
          vm.fetchRetiradas();
        } else {
          alert('Error al eliminar la retirada.');
        }
      })
      .catch(error => console.error('Error:', error));
  }
};

document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('logoutBtn')?.addEventListener('click', function() {
    localStorage.removeItem('user');
    window.location.href = 'index.html';
  });
});
