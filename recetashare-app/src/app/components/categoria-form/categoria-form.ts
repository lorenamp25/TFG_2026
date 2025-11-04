import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Categoria } from '../../models/categoria.model';

@Component({
  standalone: true,
  selector: 'app-categoria-form',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './categoria-form.html',
  styleUrls: ['./categoria-form.css']
})
export class CategoriaForm {
  @Input() categoria: Categoria | null = null
  @Output() guardarCategoria = new EventEmitter<Categoria>();
  @Output() cancelarAccion = new EventEmitter<void>();

  form = new FormGroup({
    id: new FormControl(0),
    nombre: new FormControl('', [Validators.required, Validators.minLength(3)])
  })

  ngOnInit() {
    if (this.categoria) {
      this.form.patchValue(this.categoria)
    }
  }

  onGrabar() {
    if (this.form.valid) {
      this.guardarCategoria.emit(this.form.value as Categoria)
      this.form.reset()
    }
  }

  onCancelar() {
    this.cancelarAccion.emit()
    this.form.reset()
  }
}
