import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Categoria } from '../../models/categoria.model';

@Component({
  standalone: true,
  selector: 'app-categoria-index',
  imports: [CommonModule],
  templateUrl: './categoria-index.html',
  styleUrls: ['./categoria-index.css']
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
