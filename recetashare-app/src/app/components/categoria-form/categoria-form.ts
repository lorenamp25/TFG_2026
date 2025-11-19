// Importa herramientas de Angular para componentes y comunicación entre ellos.
import { Component, EventEmitter, Input, Output } from '@angular/core';

// Importa CommonModule para usar directivas básicas (*ngIf, *ngFor, etc.)
import { CommonModule } from '@angular/common';

// Importa los módulos para trabajar con formularios reactivos.
import { FormControl, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';

// Importa el modelo Categoria para tipar los datos correctamente.
import { Categoria } from '../../models/categoria.model';

// Decorador que define las características del componente.
@Component({
  standalone: true,                       // Permite usar el componente sin un módulo tradicional.
  selector: 'app-categoria-form',         // Nombre con el que se usa en la plantilla.
  imports: [CommonModule, ReactiveFormsModule], // Módulos necesarios dentro del componente.
  templateUrl: './categoria-form.html',   // Archivo HTML con la vista.
  styleUrls: ['./categoria-form.css']     // Archivo CSS asociado.
})
export class CategoriaForm {

  // Recibe una categoría desde el componente padre (null si estamos creando una nueva).
  @Input() categoria: Categoria | null = null

  // Evento que envía la categoría al guardar.
  @Output() guardarCategoria = new EventEmitter<Categoria>();

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
    if (this.categoria) {
      this.form.patchValue(this.categoria)
    }
  }

  // Método que se ejecuta al pulsar "Guardar".
  onGrabar() {
    // Solo ejecuta si el formulario es válido.
    if (this.form.valid) {
      // Emite los datos completos del formulario al componente padre.
      this.guardarCategoria.emit(this.form.value as Categoria)

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
