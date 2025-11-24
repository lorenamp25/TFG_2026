import { Component, EventEmitter, Input, Output } from '@angular/core';

import { Ingrediente } from '../../models/ingrediente.model';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,  
  selector: 'app-ingrediente-form',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './ingrediente-form.html',
  styleUrl: './ingrediente-form.css',
})
export class IngredienteForm {

  // Recibe una categoría desde el componente padre (null si estamos creando una nueva).
  @Input() ingrediente: Ingrediente | null = null

  // Evento que envía la categoría al guardar.
  @Output() guardarIngrediente = new EventEmitter<Ingrediente>();

  // Evento para avisar al padre de que se ha cancelado la acción.
  @Output() cancelarAccion = new EventEmitter<void>();

  // Definición del formulario reactivo con sus controles y validaciones.
  form = new FormGroup({
    id: new FormControl(0),                                            // ID oculto
    nombre: new FormControl('', [Validators.required, Validators.minLength(3)]), // Nombre obligatorio, mínimo 3 letras
    descripcion: new FormControl('', [Validators.required, Validators.minLength(5)]), // Descripción obligatoria, mínimo 5 letras
    icono: new FormControl('', [Validators.required])                  // Icono obligatorio
  })

  // Método que se ejecuta al inicializar el componente.
  ngOnInit() {
    // Si viene una categoría del padre, carga sus datos en el formulario.
    if (this.ingrediente) {
      this.form.patchValue(this.ingrediente)
    }
  }

  // Método que se ejecuta al pulsar "Guardar".
  onGrabar() {
    // Solo ejecuta si el formulario es válido.
    console.log(this.form.value);
    if (this.form.valid) {
      // Emite los datos completos del formulario al componente padre.
      this.guardarIngrediente.emit(this.form.value as Ingrediente)

      // Limpia el formulario después de guardar.
      this.form.reset()
    }
  }

  // Método que se ejecuta al pulsar "Cancelar".
  onCancelar() {
    // Emite el evento para avisar al padre.
    this.cancelarAccion.emit()

    // Limpia el formulario.
    this.form.reset()
  }
}
