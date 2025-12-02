// Importa las variables del entorno (apiUrl, etc.)
// environment.apiUrl contiene la dirección base del backend
import { environment } from "../environments/environment";

/**
 * Genera la URL final de una imagen almacenada en el backend.
 * Evita problemas con rutas relativas y facilita mostrar imágenes externas o internas.
 */
export function getImageUrl(imagePath: string): string {
  
  // Si no hay imagen, devuelve una imagen por defecto
  if (!imagePath) return '/default-image.jpg';

  // Si la ruta ya es un enlace completo (HTTP/HTTPS), se devuelve tal cual
  if (imagePath.startsWith('http')) return imagePath;

  // Si es una ruta interna del sistema, se construye la URL completa al endpoint de imágenes
  const baseUrl = environment.apiUrl;

  // encodeURIComponent evita errores con espacios, acentos o símbolos raros en las rutas
  return `${baseUrl}/images?path=${encodeURIComponent(imagePath)}`;
}
