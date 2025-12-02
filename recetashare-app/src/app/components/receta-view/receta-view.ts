// Importa decoradores fundamentales de Angular:
// - Component para definir un componente
// - Input para recibir datos desde el componente padre
// - Output + EventEmitter para emitir eventos hacia el padre
import { Component, EventEmitter, Input, Output } from '@angular/core';

// Modelo tipado de Receta (para usar en el @Input)
import { Receta } from '../../models/receta.model';

// Función que genera una URL válida para cargar imágenes del backend
import { getImageUrl } from '../../modules/common';

// CommonModule permite usar directivas como *ngIf, *ngFor, etc.
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,                     // Componente sin necesidad de módulo
  selector: 'app-receta-view',          // Etiqueta HTML para este componente
  imports: [CommonModule],              // Módulos que utiliza
  templateUrl: './receta-view.html',    // Plantilla HTML asociada
  styleUrl: './receta-view.css'         // Estilos específicos del componente
})
export class RecetaView {

  // Recibe desde el componente padre la receta que debe visualizarse
  @Input() receta: Receta | null = null;

  // Evento que se emite cuando el usuario pulsa "Cerrar" o "Volver"
  @Output() cerrarVista = new EventEmitter<void>();

  // Se expone la función importada para usarla directamente en el HTML
  getImageUrl = getImageUrl;

  // ==========================================================
  // Método ejecutado al pulsar el botón de volver/cerrar vista
  // ==========================================================
  onCerrar() {
    console.log(this.receta);  // Útil para debug
    this.cerrarVista.emit();   // Notifica al componente padre que debe cerrar la vista
  }
}
