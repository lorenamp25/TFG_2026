// Importa las clases necesarias desde Angular: Component para crear el componente,
// EventEmitter para emitir eventos hacia el padre, Input/Output para comunicación de componentes.
import { Component, EventEmitter, Input, Output } from '@angular/core';

// Importa CommonModule para que la plantilla pueda usar directivas comunes como *ngFor, *ngIf.
import { CommonModule } from '@angular/common';

// Importa el modelo de Categoria para tipar correctamente los datos recibidos y enviados.
import { Categoria } from '../../models/categoria.model';

// Decorador que define las propiedades del componente.
@Component({
  standalone: true,                     // Permite usar el componente sin módulo (Angular standalone).
  selector: 'app-categoria-tabla',      // Nombre del selector que se usa en la plantilla del padre.
  imports: [CommonModule],              // Módulos necesarios dentro de este componente.
  templateUrl: './categoria-tabla.html',// Archivo HTML asociado.
  styleUrls: ['./categoria-tabla.css']  // Archivo CSS del componente.
})
export class CategoriaTablaComponent {

  // Recibe desde el componente padre un array de categorías para mostrar en la tabla.
  @Input() categorias: Categoria[] = []

  // Evento que el componente dispara cuando el usuario quiere editar una categoría.
  @Output() editarCategoriaEvent = new EventEmitter<Categoria>()

  // Evento que el componente dispara cuando el usuario quiere eliminar una categoría.
  @Output() eliminarCategoriaEvent = new EventEmitter<Categoria>()

  // Método que se ejecuta cuando se hace clic en "Editar".
  // Emite la categoría hacia el componente padre.
  onEditar(categoria: Categoria): void {
    this.editarCategoriaEvent.emit(categoria)
  }

  // Método que se ejecuta cuando se hace clic en "Eliminar".
  // También envía la categoría al componente padre.
  onEliminar(categoria: Categoria): void {
    this.eliminarCategoriaEvent.emit(categoria)
  }
}
