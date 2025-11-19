// Importa decoradores y utilidades de Angular para componentes, inputs y outputs
import { Component, EventEmitter, Input, Output } from '@angular/core';

// Importa clases para formularios reactivos: FormControl, FormGroup, validadores...
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';

// Importa CommonModule para usar directivas como *ngIf, *ngFor, etc.
import { CommonModule } from '@angular/common';

// Importa el modelo Receta para usarlo tipado
import { Receta } from '../../models/receta.model';

// Decorador que define los metadatos del componente
@Component({
  standalone: true,                        // El componente funciona sin necesidad de un módulo
  selector: 'app-receta-form',             // Nombre del selector para usar el componente
  imports: [CommonModule, ReactiveFormsModule], // Módulos que este componente necesita
  templateUrl: './receta-form.html',       // Archivo HTML asociado
  styleUrl: './receta-form.css',           // Archivo CSS asociado
})
export class RecetaForm {

  // Recibe una receta desde el componente padre (para editar)
  @Input() receta: Receta | null = null

  // Evento que se emite al guardar una receta
  @Output() guardarReceta = new EventEmitter<Receta>();

  // Evento que se emite al cancelar la acción
  @Output() cancelarAccion = new EventEmitter<void>();

  // Formulario reactivo con sus controles
  form = new FormGroup({
    id: new FormControl(0),                                    // Campo ID (oculto normalmente)
    titulo: new FormControl('', [Validators.required, Validators.minLength(3)])  
    // TODO: completar el formulario con los demás campos de Receta
  })

  // Ciclo de vida: se ejecuta cuando inicia el componente
  ngOnInit() {
    // Si llega una receta (modo edición), carga los datos en el formulario
    if (this.receta) {
      this.form.patchValue(this.receta)
    }
  }

  // Guardar receta si el formulario es válido
  onGrabar() {
    if (this.form.valid) {
      this.guardarReceta.emit(this.form.value as Receta) // Emite la receta al padre
      this.form.reset()                                   // Limpia el formulario
    }
  }

  // Cancelar acción: avisa al padre y resetea el formulario
  onCancelar() {
    this.cancelarAccion.emit()
    this.form.reset()
  }
}
