import { Component, Input } from '@angular/core';
import { Categoria } from '../../models/categoria.model';
import { CommonModule } from '@angular/common';
import { Route, Router } from '@angular/router';

@Component({
  standalone: true,
  selector: 'app-cardcategoria',
  imports: [CommonModule],
  templateUrl: './cardcategoria.html',
  styleUrl: './cardcategoria.css',
})
export class Cardcategoria {
  @Input() categoria!: Categoria

  constructor(private router: Router) {}

  verRecetasCategoria() {
    // Lógica para navegar a la página de recetas de esta categoría
    this.router.navigate(['receta', encodeURIComponent(this.categoria.id)]);
  }

}
