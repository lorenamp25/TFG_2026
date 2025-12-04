import { Component } from '@angular/core';
// Importa el decorador Component para definir un componente Angular

import { RouterOutlet } from '@angular/router';
import { StorageService } from './services/localstorage.service';
// Importa RouterOutlet para poder renderizar rutas dentro del componente raíz


@Component({
  selector: 'app-root',
  // Nombre de la etiqueta HTML donde se renderiza este componente

  imports: [RouterOutlet],
  // Permite usar <router-outlet> dentro del template del componente

  templateUrl: './app.component.html',
  // Archivo HTML asociado a este componente

  styleUrl: './app.component.css'
  // Archivo CSS asociado a este componente
})
export class AppComponent {
  title = 'Sistema de Turnos';

  menuAbierto = false;
  // Título de la aplicación (puede usarse en el template)

  constructor(public storageService: StorageService) {
    // Se ejecuta cuando se crea el componente

    let temaActual = localStorage.getItem("data-tema") || "claro";
    // Obtiene el tema guardado en localStorage, si no existe usa "claro"

    const cuerpo = document.documentElement;
    // Selecciona el elemento raíz <html>

    cuerpo.setAttribute("data-tema", temaActual);
    // Aplica el tema actual como atributo HTML para que el CSS lo use
  }

  toggleMenu() { this.menuAbierto = !this.menuAbierto; }
  cerrarMenu() {
  this.menuAbierto = false;
  this.adminMenuAbierto = false; // opcional pero recomendado
}

  toggleTheme() {
    // Método para alternar entre tema claro y oscuro

    const cuerpo = document.documentElement;
    // Selecciona el elemento raíz del documento

    let temaActual = localStorage.getItem("data-tema") || "claro";
    // Lee el tema que está guardado o usa "claro" como predeterminado

    temaActual = temaActual === "claro" ? "oscuro" : "claro";
    // Cambia el valor: si está en claro pasa a oscuro, y viceversa

    cuerpo.setAttribute("data-tema", temaActual);
    // Aplica el nuevo tema al HTML para que el CSS lo detecte

    localStorage.setItem("data-tema", temaActual);
    // Guarda el nuevo tema en localStorage para recordarlo al recargar
  }

  ir(ruta: string) {
    this.menuAbierto = false;
    window.location.href = ruta;
  }
  adminMenuAbierto = false;

toggleAdminMenu() {
  this.adminMenuAbierto = !this.adminMenuAbierto;
}


}


