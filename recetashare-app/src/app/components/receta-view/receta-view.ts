import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Receta } from '../../models/receta.model';
import { getImageUrl } from '../../modules/common';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-receta-view',
  imports: [CommonModule],
  templateUrl: './receta-view.html',
  styleUrl: './receta-view.css'
})
export class RecetaView {
  @Input() receta: Receta | null = null
  @Output() cerrarVista = new EventEmitter<void>();
  getImageUrl = getImageUrl

  onCerrar() {
    console.log(this.receta)
    this.cerrarVista.emit()
  }

}
