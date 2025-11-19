import { Component } from '@angular/core';
// Importa CommonModule para usar directivas comunes como *ngIf, *ngFor, etc.
import { CommonModule } from '@angular/common';
// Servicio que maneja las peticiones relacionadas con categorías
import { CategoriaService } from '../../services/categoria.service';
// Modelo que define la estructura de una categoría
import { Categoria } from '../../models/categoria.model';
// Componente que muestra la tarjeta visual de una categoría
import { Cardcategoria } from '../../components/cardcategoria/cardcategoria';

@Component({
  standalone: true,                       // Indica que el componente es standalone (no depende de un módulo)
  selector: 'app-categoria',              // Nombre de la etiqueta HTML para usar este componente
  imports: [CommonModule, Cardcategoria], // Módulos y componentes que este componente necesita
  templateUrl: './categoria.html',        // Template HTML asociado
  styleUrls: ['./categoria.css']          // Archivo(s) de estilos del componente
})
export class CategoriaPage {
  // Array donde se guardarán las categorías cargadas desde el backend
  categorias: Categoria[] = []

  // Inyecta el servicio de categorías para hacer llamadas a la API
  constructor(private categoriaService: CategoriaService) { }

  // Se ejecuta automáticamente cuando el componente se inicializa
  ngOnInit(): void {
    this.cargarCategorias()               // Llama al método que obtiene las categorías
  }

  // Método que llama al servicio para obtener todas las categorías
  cargarCategorias() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        // Cuando llega la respuesta, se guarda en el array 'categorias'
        this.categorias = response
      }
    )
  }

}
