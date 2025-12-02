import { Component, Input } from '@angular/core';
import { Categoria } from '../../models/categoria.model';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  // Marcamos este componente como standalone (no necesita ir declarado en un módulo)
  standalone: true,

  // Nombre de la etiqueta HTML que usaremos en las plantillas
  selector: 'app-cardcategoria',

  // Módulos que este componente necesita 
  imports: [CommonModule],

  // Plantilla HTML asociada al componente
  templateUrl: './cardcategoria.html',

  // Hoja de estilos específica del componente
  styleUrl: './cardcategoria.css',
})
export class Cardcategoria {
  // Input: el componente padre nos pasa una categoría
  // El "!" indica a TypeScript que confiamos en que vendrá definida
  @Input() categoria!: Categoria;

  // Inyectamos el Router para poder navegar por rutas desde el código
  constructor(private router: Router) {}

  // Método que se llamará (por ejemplo, desde un botón en la tarjeta)
  // para ver las recetas de esta categoría
  verRecetasCategoria() {
    // Navega a la ruta 'receta/:id' pasando el id de la categoría
    // encodeURIComponent se usa para evitar problemas si el id tuviera caracteres especiales
    this.router.navigate(['receta', encodeURIComponent(this.categoria.id)]);
  }
}

