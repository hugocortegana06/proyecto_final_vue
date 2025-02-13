import { BASE_URL } from './config.js';

const app = Vue.createApp({
  data() {
    return {
      sessionLogs: [],
      actionLogs: []
    };
  },
  methods: {
    async fetchSessionLogs() {
      try {
        const response = await fetch(`${BASE_URL}/admin/logs.php?type=sesion`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        this.sessionLogs = await response.json();
        this.$nextTick(() => {
          if ($.fn.DataTable.isDataTable('#sessionLogsTable')) {
            $('#sessionLogsTable').DataTable().destroy();
          }
          $('#sessionLogsTable').DataTable();
        });
      } catch (error) {
        console.error("Error al obtener logs de sesión:", error);
      }
    },
    async fetchActionLogs() {
      try {
        const response = await fetch(`${BASE_URL}/admin/logs.php?type=accion`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        this.actionLogs = await response.json();
        this.$nextTick(() => {
          if ($.fn.DataTable.isDataTable('#actionLogsTable')) {
            $('#actionLogsTable').DataTable().destroy();
          }
          $('#actionLogsTable').DataTable();
        });
      } catch (error) {
        console.error("Error al obtener logs de acciones:", error);
      }
    }
  },
  mounted() {
    this.fetchSessionLogs();
    this.fetchActionLogs();
  }
});

app.mount('#app');
