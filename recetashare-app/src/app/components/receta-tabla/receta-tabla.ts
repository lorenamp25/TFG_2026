import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Receta } from '../../models/receta.model';
import { StorageService } from '../../services/localstorage.service';
import { getImageUrl } from '../../modules/common';

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
  @Output() clickOnRecetaEvent = new EventEmitter<Receta>()
  getImageUrl = getImageUrl

  constructor(public storageService: StorageService) { }

  onEditar(receta: Receta): void {
    this.editarRecetaEvent.emit(receta)
  }

  onEliminar(receta: Receta): void {
    this.eliminarRecetaEvent.emit(receta)
  }

  onClickReceta(receta: Receta): void {
    this.clickOnRecetaEvent.emit(receta)
  }
}
