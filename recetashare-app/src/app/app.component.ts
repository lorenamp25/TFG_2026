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


  toggleTheme() {
    const cuerpo = document.documentElement;
    const temaActual = cuerpo.getAttribute("data-tema") || "claro";
    cuerpo.setAttribute("data-tema", temaActual === "claro" ? "oscuro" : "claro");
  }

}

