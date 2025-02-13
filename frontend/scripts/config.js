/* config.js
   Aquí definimos variables globales para entorno y rutas de API */

/**
 * Define el entorno actual: 'development' o 'production'.
 * Puedes cambiarlo a 'production' cuando subas tu proyecto a un servidor.
 */
// Define el entorno
export const ENVIRONMENT = 'development';

// Define la URL base
export const BASE_URL = (ENVIRONMENT === 'development')
  ? 'http://localhost/SERVER_JS/proyecto_final_vue/backend/api'
  : 'https://ieslamarisma.net/proyectos/2025/hugomuniz/proyecto_final_vue/backend/api';
/* Exportamos las constantes en caso de que uses módulos ES6,
   o simplemente quedarán en scope global si lo cargas en <script>. */


//const baseURL = "http://localhost/proyecto_final_vue/backend/api/loginRegister";  // Ajusta la ruta según tu configuración
