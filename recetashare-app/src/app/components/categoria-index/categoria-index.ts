import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Categoria } from '../../models/categoria.model';

@Component({
  selector: 'app-categoria',
  imports: [],
  templateUrl: './categoria.html',
  styleUrl: './categoria.css'
})
export class CategoriaComponent {
  @Input() categorias: Categoria[] = []
  @Output() editCategoriaEvent = new EventEmitter<Categoria>()
  @Output() deleteCategoriaEvent = new EventEmitter<Categoria>()

  onEdit(categoria: Categoria): void {
    this.editCategoriaEvent.emit(categoria)
  }

  onDelete(categoria: Categoria): void {
    this.deleteCategoriaEvent.emit(categoria)
  }
}
