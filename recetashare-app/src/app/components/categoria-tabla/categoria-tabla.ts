import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Categoria } from '../../models/categoria.model';

@Component({
  standalone: true,
  selector: 'app-categoria-tabla',
  imports: [CommonModule],
  templateUrl: './categoria-tabla.html',
  styleUrls: ['./categoria-tabla.css']
})
export class CategoriaTablaComponent {
  @Input() categorias: Categoria[] = []
  @Output() editarCategoriaEvent = new EventEmitter<Categoria>()
  @Output() eliminarCategoriaEvent = new EventEmitter<Categoria>()

  onEditar(categoria: Categoria): void {
    this.editarCategoriaEvent.emit(categoria)
  }

  onEliminar(categoria: Categoria): void {
    this.eliminarCategoriaEvent.emit(categoria)
  }
}
