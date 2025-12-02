// Importa CommonModule para usar directivas básicas (ngIf, ngFor...)
import { CommonModule } from '@angular/common';

// Importa decoradores y utilidades de Angular
import { Component, EventEmitter, Input, Output } from '@angular/core';

// Modelo tipado de Receta
import { Receta } from '../../models/receta.model';

// Servicio para extraer datos del usuario almacenado en localStorage
import { StorageService } from '../../services/localstorage.service';

// Función que genera una URL válida para imágenes almacenadas en el backend
import { getImageUrl } from '../../modules/common';

@Component({
  standalone: true,                           // Componente desacoplado de módulos
  selector: 'app-receta-tabla',               // Etiqueta HTML para usar este componente
  imports: [CommonModule],                    // Módulos necesarios
  templateUrl: './receta-tabla.html',         // Vista HTML
  styleUrl: './receta-tabla.css',             // Estilos asociados
})
export class RecetaTabla {

  // Lista de recetas que viene desde el componente padre
  @Input() recetas: Receta[] = [];

  // Evento emitido cuando el usuario hace clic en editar una receta
  @Output() editarRecetaEvent = new EventEmitter<Receta>();

  // Evento emitido cuando se solicita eliminar una receta
  @Output() eliminarRecetaEvent = new EventEmitter<Receta>();

  // Evento emitido cuando se hace clic sobre una receta (ver detalle o abrir edición)
  @Output() clickOnRecetaEvent = new EventEmitter<Receta>();

  // Función importada para generar URLs de imágenes
  getImageUrl = getImageUrl;

  // Inyectamos el StorageService para saber qué usuario está logueado
  constructor(public storageService: StorageService) {}

  // ============================
  //  Métodos para emitir eventos
  // ============================

  // Emitir evento para editar una receta concreta
  onEditar(receta: Receta): void {
    this.editarRecetaEvent.emit(receta);
  }

  // Emitir evento para eliminar la receta seleccionada
  onEliminar(receta: Receta): void {
    this.eliminarRecetaEvent.emit(receta);
  }

  // Emitir evento cuando se hace clic sobre la receta
  onClickReceta(receta: Receta): void {
    this.clickOnRecetaEvent.emit(receta);
  }
}
