import { Component, Input } from '@angular/core';
import { Categoria } from '../../models/categoria.model';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-cardcategoria',
  imports: [CommonModule],
  templateUrl: './cardcategoria.html',
  styleUrl: './cardcategoria.css',
})
export class Cardcategoria {
  @Input() categoria!: Categoria

  verRecetasCategoria() {
    // Lógica para navegar a la página de recetas de esta categoría
    console.log(`Navegar a recetas de la categoría: ${this.categoria.nombre}`);
    //TODO: Implementar navegación: Ir a la página de recetas filtradas por esta categoría
  }

}
