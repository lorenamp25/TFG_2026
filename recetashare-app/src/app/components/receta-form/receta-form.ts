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

// Importa CommonModule para usar directivas de angular comunes
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
  standalone: true,                       // El componente funciona sin necesidad de un módulo
  selector: 'app-receta-form',            // Nombre del selector para usar el componente
  imports: [CommonModule, FormsModule, ReactiveFormsModule], // Módulos que este componente necesita
  templateUrl: './receta-form.html',      // Archivo HTML asociado
  styleUrl: './receta-form.css',          // Archivo CSS asociado
})
export class RecetaForm {
  // Recibe una receta desde el componente padre (para editar o crear partiendo de un objeto)
  @Input() receta: Receta | null = null;

  // Evento que se emite al guardar una receta (devuelve la receta al componente padre)
  @Output() guardarReceta = new EventEmitter<Receta>();

  // Evento que se emite al cancelar la acción (para que el padre cierre el formulario, por ejemplo)
  @Output() cancelarAccion = new EventEmitter<void>();

  // Lista de categorías para el <select>
  categorias: Categoria[] = [];

  // Lista de ingredientes para los selects de ingredientes de la receta
  ingredientes: Ingrediente[] = [];

  // Variable opcional para gestionar vista previa de imagen (si la necesitas)
  imagenPreview: any;
  // Mensaje de error específico para ingredientes
  ingredientesError: string | null = null;

  // Formulario reactivo con sus controles y validaciones
  form = new FormGroup({
    id: new FormControl(0),

    titulo: new FormControl('', [
      Validators.required,
      Validators.minLength(3),
    ]),

    tiempo_preparacion: new FormControl<number | null>(null, [
      Validators.required,
      Validators.min(1),
    ]),

    dificultad: new FormControl('', [
      Validators.required,
    ]),

    categoria: new FormControl<number | null>(null, [
      Validators.required,
    ]),

    descripcion: new FormControl('', [
      Validators.required,
      Validators.minLength(10),
    ]),
  });

  // Inyección de servicios necesarios
  constructor(
    private categoriaService: CategoriaService,
    private ingredienteService: IngredienteService,
    private storageService: StorageService
  ) { }

  // Ciclo de vida: se ejecuta cuando inicia el componente
  ngOnInit() {
    // Si llega una receta (modo edición), carga los datos en el formulario
    if (this.receta) {
      this.form.patchValue(this.receta);

      // Si la receta es nueva (id = 0), se asigna el usuario actual
      if (this.receta.id === 0) {
        this.receta.usuario = this.storageService.getUsuario();
      }
    }

    // Carga de datos auxiliares (categorías e ingredientes para los selects)
    this.cargarDatos();
  }

  // ============================
  //  Carga de categorías e ingredientes
  // ============================
  cargarDatos() {
    this.categoriaService.listarCategorias().subscribe((response: any) => {
      this.categorias = response;
    });

    this.ingredienteService.listarIngredientes().subscribe((response: any) => {
      this.ingredientes = response;
    });
  }

  // ============================
  //  Comprobación global de validez (opcional para el botón)
  // ============================
  esFormularioCompletamenteValido(): boolean {
    if (this.form.invalid || !this.receta) return false;

    // Imagen obligatoria (fichero o preview ya cargada)
    if (!this.receta.imagen_file && !this.receta.imagen_preview) return false;

    // Ingredientes
    if (!this.receta.ingredientes || this.receta.ingredientes.length === 0) return false;

    const ingredientesInvalidos = this.receta.ingredientes.some((ing) =>
      !ing.ingrediente ||
      !ing.ingrediente.id ||
      ing.cantidad == null ||
      ing.cantidad <= 0 ||
      !ing.unidad ||
      ing.unidad.trim() === ''
    );

    if (ingredientesInvalidos) return false;

    // Instrucciones
    if (!this.receta.instrucciones || this.receta.instrucciones.length === 0) return false;

    const instruccionesInvalidas = this.receta.instrucciones.some((inst) =>
      !inst.descripcion || inst.descripcion.trim() === ''
    );

    if (instruccionesInvalidas) return false;

    return true;
  }

  // ============================
  //  Guardar receta si el formulario es válido
  // ============================
  onGrabar() {
    // 1) Validación de campos del formulario
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      alert('Rellena todos los campos obligatorios del formulario');
      return;
    }

    if (!this.receta) {
      alert('Error interno: falta el objeto receta');
      return;
    }

    // 2) Imagen obligatoria
    if (!this.receta.imagen_file && !this.receta.imagen_preview) {
      alert('Debes seleccionar una imagen para la receta');
      return;
    }

    // 3) Validación de INGREDIENTES
    if (!this.receta.ingredientes || this.receta.ingredientes.length === 0) {
      this.ingredientesError = 'La receta debe tener al menos un ingrediente.';
      return;
    }

    const ingredientesInvalidos = this.receta.ingredientes.some((ing) =>
      !ing.ingrediente ||
      !ing.ingrediente.id ||                // sin ingrediente seleccionado
      ing.cantidad == null ||
      ing.cantidad <= 0 ||                  // cantidad 0 o negativa
      !ing.unidad ||
      ing.unidad.trim() === ''              // unidad vacía
    );

    if (ingredientesInvalidos) {
      this.ingredientesError = 'La receta debe tener al menos un ingrediente.';
      return;
    }

    // 4) Validación de INSTRUCCIONES
    if (!this.receta.instrucciones || this.receta.instrucciones.length === 0) {
      alert('Añade al menos una instrucción');
      return;
    }

    const instruccionesInvalidas = this.receta.instrucciones.some((inst) =>
      !inst.descripcion || inst.descripcion.trim() === ''
    );

    if (instruccionesInvalidas) {
      alert('Todas las instrucciones deben tener una descripción.');
      return;
    }

    // 5) Si todo OK, montamos la receta final
    let receta = this.form.value as Receta;

    receta.usuario = this.receta.usuario || this.storageService.getUsuario();
    receta.ingredientes = this.receta.ingredientes;
    receta.instrucciones = this.receta.instrucciones;
    receta.imagen_file = this.receta.imagen_file;

    // Emitimos la receta al componente padre
    this.guardarReceta.emit(receta);

    // Reseteamos el formulario
    this.form.reset();
  }

  // Comprueba que la receta tenga ingredientes válidos (cantidad > 0)
  validarIngredientes(): boolean {
    if (!this.receta) return true;

    if (this.receta.ingredientes.length === 0) {
      this.ingredientesError = 'La receta debe tener al menos un ingrediente.';
      return false;
    }

    for (const ing of this.receta.ingredientes) {
      // Si cantidad no es numérica o es menor o igual a 0 => inválido
      const cantidad = Number(ing.cantidad);
      if (!cantidad || cantidad <= 0) {
        this.ingredientesError = 'Cada ingrediente debe tener una cantidad mayor que 0.';
        return false;
      }
    }
    this.ingredientesError = null;
    return true;
  }

  // ============================
  //  Cancelar acción
  // ============================
  onCancelar() {
    this.cancelarAccion.emit();
    this.form.reset();
  }

  // ============================
  //  Selección de imagen para la receta
  // ============================
  onImagenSeleccionada($event: Event) {
    const input = $event.target as HTMLInputElement;

    if (input.files && input.files[0]) {
      const file = input.files[0];

      if (this.receta) {
        this.receta.imagen_file = file;

        const reader = new FileReader();
        reader.onload = (e: any) => {
          this.receta!.imagen_preview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    }
  }

  // ============================
  //  Gestión dinámica de INGREDIENTES
  // ============================
  agregarIngrediente() {
    this.receta?.ingredientes.push({
      cantidad: 0,
      unidad: '',
      ingrediente: { id: 0, nombre: '' },
    });
  }

  eliminarIngrediente(index: number) {
    this.receta?.ingredientes.splice(index, 1);
  }

  // ============================
  //  Gestión dinámica de INSTRUCCIONES
  // ============================
  agregarInstruccion() {
    const nuevoPaso = this.receta ? this.receta.instrucciones.length + 1 : 1;

    this.receta?.instrucciones.push({
      orden: nuevoPaso,
      descripcion: '',
    });
  }

  eliminarInstruccion(index: number) {
    this.receta?.instrucciones.splice(index, 1);
  }

  // Manejo de imagen asociada a una instrucción concreta (pendiente de completar)
  onInstruccionImagenSeleccionada(event: Event, instruccion: any) {
    const input = event.target as HTMLInputElement;
    // Aquí se podría implementar la lógica de subir/preview por cada instrucción
  }
}
