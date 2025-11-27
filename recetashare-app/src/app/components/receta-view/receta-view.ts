import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Receta } from '../../models/receta.model';

@Component({
  standalone: true,
  selector: 'app-receta-view',
  imports: [],
  templateUrl: './receta-view.html',
  styleUrl: './receta-view.css'
})
export class RecetaView {
  @Input() receta: Receta | null = null
  @Output() cerrarVista = new EventEmitter<void>();

  onCerrar() {
    this.cerrarVista.emit();
  }




}
