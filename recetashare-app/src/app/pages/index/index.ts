import { AfterViewInit, Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-index',
  imports: [],
  templateUrl: './index.html',
  styleUrls: ['./index.css']
})
export class Index implements AfterViewInit {

  ngAfterViewInit(): void {
    this.inicializar()
  }

  inicializar() {
    const botonTema = document.getElementById("boton-tema");
    if (!botonTema) {
      console.warn('boton-tema no encontrado en el DOM');
      return;
    }
    const cuerpo = document.documentElement;

    botonTema.addEventListener("click", () => {
      const temaActual = cuerpo.getAttribute("data-tema") || "claro";
      cuerpo.setAttribute("data-tema", temaActual === "claro" ? "oscuro" : "claro");
    });
  }
}
