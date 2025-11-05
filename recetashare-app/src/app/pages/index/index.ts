import { AfterViewInit, Component } from '@angular/core';

@Component({
  selector: 'app-index',
  imports: [],
  templateUrl: './index.html',
  styleUrls: ['./index.css']
})
export class Index {

  toggleTheme() {
    const cuerpo = document.documentElement;
    const temaActual = cuerpo.getAttribute("data-tema") || "claro";
    cuerpo.setAttribute("data-tema", temaActual === "claro" ? "oscuro" : "claro");
  }


}
