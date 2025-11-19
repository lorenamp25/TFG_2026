import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Receta } from '../../models/receta.model';

@Component({
  standalone: true,
  selector: 'app-receta-tabla',
  imports: [CommonModule],
  templateUrl: './receta-tabla.html',
  styleUrl: './receta-tabla.css',
})
export class RecetaTabla {
  @Input() recetas: Receta[] = []
  @Output() editarRecetaEvent = new EventEmitter<Receta>()
  @Output() eliminarRecetaEvent = new EventEmitter<Receta>()

  onEditar(receta: Receta): void {
    this.editarRecetaEvent.emit(receta)
  }

  onEliminar(receta: Receta): void {
    this.eliminarRecetaEvent.emit(receta)
  }
}

