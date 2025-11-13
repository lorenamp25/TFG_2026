import { Component, EventEmitter, Input, Output } from '@angular/core';
import { Receta } from '../../pages/receta/receta';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-receta-form',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './receta-form.html',
  styleUrl: './receta-form.css',
})
export class RecetaForm {

  @Input() receta: Receta | null = null
  @Output() guardarReceta = new EventEmitter<Receta>();
  @Output() cancelarAccion = new EventEmitter<void>();

  form = new FormGroup({
    id: new FormControl(0),
    nombre: new FormControl('', [Validators.required, Validators.minLength(3)])
  })

  ngOnInit() {
    if (this.receta) {
      this.form.patchValue(this.receta)
    }
  }

  onGrabar() {
    if (this.form.valid) {
      this.guardarReceta.emit(this.form.value as Receta)
      this.form.reset()
    }
  }

  onCancelar() {
    this.cancelarAccion.emit()
    this.form.reset()
  }
}


