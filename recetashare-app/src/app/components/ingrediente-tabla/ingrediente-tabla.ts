import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Ingrediente } from '../../models/ingrediente.model'
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-ingrediente-tabla',
  imports: [CommonModule],
  templateUrl: './ingrediente-tabla.html',
  styleUrl: './ingrediente-tabla.css',
})
export class IngredienteTabla {

  // Recibe desde el componente padre un array de categorías para mostrar en la tabla.
  @Input() ingredientes: Ingrediente[] = []

  // Evento que el componente dispara cuando el usuario quiere editar una categoría.
  @Output() editarIngredienteEvent = new EventEmitter<Ingrediente>()

  // Evento que el componente dispara cuando el usuario quiere eliminar una categoría.
  @Output() eliminarIngredienteEvent = new EventEmitter<Ingrediente>()
  

  // Método que se ejecuta cuando se hace clic en "Editar".
  // Emite la categoría hacia el componente padre.
  onEditar(ingrediente: Ingrediente): void {
    this.editarIngredienteEvent.emit(ingrediente)
  }

  // Método que se ejecuta cuando se hace clic en "Eliminar".
  // También envía la categoría al componente padre.
  onEliminar(ingrediente: Ingrediente): void {
    this.eliminarIngredienteEvent.emit(ingrediente)
  }
}
