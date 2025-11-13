import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'Sistema de Turnos';
  isAdmin = true;  // TODO: TOMAR EL VALOR AL INGRESAR AL SISTEMA

  constructor () {
    let temaActual = localStorage.getItem("data-tema") || "claro"
    const cuerpo = document.documentElement
    cuerpo.setAttribute("data-tema", temaActual)
  }

  toggleTheme() {
    const cuerpo = document.documentElement
    let temaActual = localStorage.getItem("data-tema") || "claro"

    temaActual = temaActual === "claro" ? "oscuro" : "claro"
    cuerpo.setAttribute("data-tema", temaActual)

    localStorage.setItem("data-tema", temaActual)
  }

}

