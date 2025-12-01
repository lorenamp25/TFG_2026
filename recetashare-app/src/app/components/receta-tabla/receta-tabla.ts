import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Receta } from '../../models/receta.model';
import { StorageService } from '../../services/localstorage.service';

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

  constructor(public storageService: StorageService) {}

  onEditar(receta: Receta): void {
    this.editarRecetaEvent.emit(receta)
  }

  onEliminar(receta: Receta): void {
    this.eliminarRecetaEvent.emit(receta)
  }

  onClickReceta(receta: Receta): void {
    this.clickOnRecetaEvent.emit(receta)
  }

getImageUrl(receta: Receta | null): string {
  if (!receta) {
    return 'assets/img/placeholder-receta.jpg';
  }

  const path =
    (receta as any).imagen_url ||
    (receta as any).imagenUrl ||
    (receta as any).imagen ||
    '';

  if (!path) {
    return 'assets/img/placeholder-receta.jpg';
  }

  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }

  const limpio = path.replace(/^\/+/, '');
  const codificado = encodeURI(limpio);

  // Si las sirves desde el mismo host de Angular:
  return '/' + codificado;

  // Si las sirves desde el backend en otro puerto:
  // return 'http://localhost:8000/' + codificado;
}


}
