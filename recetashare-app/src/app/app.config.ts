// Importa utilidades de configuración global de Angular
import {
  ApplicationConfig,
  provideBrowserGlobalErrorListeners,  // Permite capturar errores globales del navegador
  provideZoneChangeDetection           // Configura cómo Angular detecta cambios
} from '@angular/core';

// Router: necesario para habilitar la navegación de rutas
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';    // Archivo donde defines tus rutas

// Cliente HTTP: permite realizar peticiones al backend
import { provideHttpClient } from '@angular/common/http';

// ============================================================
// CONFIGURACIÓN GLOBAL DE LA APLICACIÓN ANGULAR
// ============================================================

export const appConfig: ApplicationConfig = {
  providers: [
    // Habilita escuchas de errores globales del navegador (ayuda al debugging)
    provideBrowserGlobalErrorListeners(),

    // Optimización de detección de cambios de Angular:
    // eventCoalescing junta varios eventos en uno → menos carga en la UI
    provideZoneChangeDetection({ eventCoalescing: true }),

    // Proveedor del enrutador con la lista de rutas de la app
    provideRouter(routes),

    // Registra HttpClient para poder llamar al backend desde servicios
    provideHttpClient()
  ]
};
