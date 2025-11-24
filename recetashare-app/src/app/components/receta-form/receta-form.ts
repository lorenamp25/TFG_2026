// Importa decoradores y utilidades de Angular para componentes, inputs y outputs
import { Component, EventEmitter, Input, Output } from '@angular/core';

// Importa clases para formularios reactivos: FormControl, FormGroup, validadores...
import {
  FormControl,
  FormGroup,
  FormsModule,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';

// Importa CommonModule para usar directivas como *ngIf, *ngFor, etc.
import { CommonModule } from '@angular/common';

// Importa el modelo Receta para usarlo tipado
import { Receta } from '../../models/receta.model';
import { CategoriaService } from '../../services/categoria.service';
import { Categoria } from '../../models/categoria.model';
import { Ingrediente } from '../../models/ingrediente.model';
import { IngredienteService } from '../../services/ingrediente.service';
import { StorageService } from '../../services/localstorage.service';

// Decorador que define los metadatos del componente
@Component({
  standalone: true, // El componente funciona sin necesidad de un módulo
  selector: 'app-receta-form', // Nombre del selector para usar el componente
  imports: [CommonModule, FormsModule, ReactiveFormsModule], // Módulos que este componente necesita
  templateUrl: './receta-form.html', // Archivo HTML asociado
  styleUrl: './receta-form.css', // Archivo CSS asociado
})
export class RecetaForm {
  // Recibe una receta desde el componente padre (para editar)
  @Input() receta: Receta | null = null;

  // Evento que se emite al guardar una receta
  @Output() guardarReceta = new EventEmitter<Receta>();

  // Evento que se emite al cancelar la acción
  @Output() cancelarAccion = new EventEmitter<void>();

  categorias: Categoria[] = []; // Lista de categorías para el select
  ingredientes: Ingrediente[] = []; // Lista de ingredientes
  imagenPreview: any;

  // Formulario reactivo con sus controles
  form = new FormGroup({
    id: new FormControl(0), // Campo ID (oculto normalmente)
    titulo: new FormControl('', [Validators.required, Validators.minLength(3)]),
    tiempoPreparacion: new FormControl(0),
    dificultad: new FormControl(''),
    categoria: new FormControl(),
    descripcion: new FormControl('', [Validators.required, Validators.minLength(10)]),
  });

  constructor(
    private categoriaService: CategoriaService,
    private ingredienteService: IngredienteService,
    private storageService: StorageService
  ) {}

  // Ciclo de vida: se ejecuta cuando inicia el componente
  ngOnInit() {
    // Si llega una receta (modo edición), carga los datos en el formulario
    if (this.receta) {
      this.form.patchValue(this.receta);
      if (this.receta.id === 0) {
        this.receta.usuario = this.storageService.getUsuario();
      }
    }
    this.cargarDatos();
  }

  cargarDatos() {
    this.categoriaService.listarCategorias().subscribe((response: any) => {
      this.categorias = response; // Guarda las categorías
    });
    this.ingredienteService.listarIngredientes().subscribe((response: any) => {
      this.ingredientes = response; // Guarda los ingredientes
    });
  }

  // Guardar receta si el formulario es válido
  onGrabar() {
    if (this.form.valid) {
      let receta = this.form.value as Receta
      receta.usuario = this.receta?.usuario || this.storageService.getUsuario()
      receta.ingredientes = []
      for (let i = 0; i < receta.ingredientes.length; i++) {

        const ing = this.ingredientes.find(ing => ing.id === receta.ingredientes[i].ingrediente.id)
        if (ing) {
          receta.ingredientes.push({
            cantidad: receta.ingredientes[i].cantidad,
            unidad: receta.ingredientes[i].unidad,
            ingrediente: ing
          })
        }
      }

      receta.instrucciones = this.receta?.instrucciones || []

      console.log('Receta a guardar:', receta);
      this.guardarReceta.emit(receta); // Emite la receta al padre
      this.form.reset(); // Limpia el formulario
    }
  }

  // Cancelar acción: avisa al padre y resetea el formulario
  onCancelar() {
    this.cancelarAccion.emit();
    this.form.reset();
  }

  agregarIngrediente() {
    console.log('Agregar ingrediente');
    this.receta?.ingredientes.push({ cantidad: 0, unidad: '', ingrediente: { id: 0, nombre: '' } });
  }

  eliminarIngrediente(index: number) {
    this.receta?.ingredientes.splice(index, 1);
  }

  // agregarIngrediente() {
  //   const tablaIngredientes = document.getElementById('tabla-ingredientes') as HTMLTableElement;
  //   const nuevaFila = tablaIngredientes.insertRow();
  //   const celdaCantidad = nuevaFila.insertCell(0);
  //   const celdaUnidad = nuevaFila.insertCell(1);
  //   const celdaNombre = nuevaFila.insertCell(2);
  //   const celdaAcciones = nuevaFila.insertCell(3);
  //   celdaCantidad.innerHTML = '<input type="text" name="cantidad" placeholder="Cantidad">';
  //   celdaUnidad.innerHTML = '<input type="text" name="unidad" placeholder="Unidad">';
  //   celdaNombre.innerHTML = '<input type="text" name="nombre" placeholder="Nombre del ingrediente">';

  //   const btn = document.createElement("button");
  //   btn.type = "button";
  //   btn.innerText = "Eliminar";
  //   btn.onclick = () => this.eliminarIngrediente(nuevaFila);
  //   celdaAcciones.appendChild(btn);
  // }

  // eliminarIngrediente(fila: any) {
  //   fila.remove();
  // }

  onImagenSeleccionada($event: Event) {
    throw new Error('Method not implemented.');
  }
}
