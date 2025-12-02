/// <reference types="@angular/localize" />
// Esta línea permite usar las funciones de internacionalización de Angular
// como $localize para traducciones.

// bootstrapApplication: función moderna de Angular
// que permite iniciar la aplicación SIN necesidad de AppModule.
import { bootstrapApplication } from '@angular/platform-browser';

// Configuración global de la aplicación (providers, router, http…)
import { appConfig } from './app/app.config';

// Componente raíz de la aplicación: el primer componente que se carga
import { AppComponent } from './app/app.component';

// ============================================================
// ARRANQUE (BOOTSTRAP) DE LA APLICACIÓN ANGULAR
// ============================================================

bootstrapApplication(AppComponent, appConfig)
  // Se inicia la app con AppComponent + la configuración personalizada
  .catch((err) => console.error(err));
// Si ocurre algún error durante el arranque, se muestra en consola
