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
    id: new FormControl(0), // Campo ID (normalmente oculto; 0 para nuevas recetas)
    titulo: new FormControl('', [
      Validators.required,
      Validators.minLength(3), // Título mínimo de 3 caracteres
    ]),
    tiempo_preparacion: new FormControl(0), // Minutos de preparación
    dificultad: new FormControl(''),        // baja / media / alta
    categoria: new FormControl(),           // id de categoría
    descripcion: new FormControl('', [
      Validators.required,
      Validators.minLength(10), // Descripción con mínimo de caracteres
    ]),
  });

  // Inyección de servicios necesarios:
  // - categoriaService: para obtener categorías desde la API
  // - ingredienteService: para obtener ingredientes desde la API
  // - storageService: para recuperar el usuario logueado desde el localStorage
  constructor(
    private categoriaService: CategoriaService,
    private ingredienteService: IngredienteService,
    private storageService: StorageService
  ) { }

  // Ciclo de vida: se ejecuta cuando inicia el componente
  ngOnInit() {
    // Si llega una receta (modo edición), carga los datos en el formulario
    if (this.receta) {
      // Rellena los controles del formulario con los datos de la receta
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
    // Petición al servicio de categorías
    this.categoriaService.listarCategorias().subscribe((response: any) => {
      this.categorias = response; // Guarda las categorías para el desplegable
    });

    // Petición al servicio de ingredientes
    this.ingredienteService.listarIngredientes().subscribe((response: any) => {
      this.ingredientes = response; // Guarda los ingredientes para las tablas dinámicas
    });
  }

  // ============================
  //  Guardar receta si el formulario es válido
  // ============================
  onGrabar() {
    // Solo se procesa si el formulario pasa las validaciones
    if (this.form.valid) {


      // Convertimos el valor del formulario al tipo Receta
      let receta = this.form.value as Receta;

      // Aseguramos que la receta tenga usuario:
      // - Si viene de this.receta, se respeta
      // - Si no, se toma el usuario logueado desde el storage
      receta.usuario = this.receta?.usuario || this.storageService.getUsuario();

      // Ingredientes e instrucciones se cogen del objeto receta original (que se usa en el HTML)
      receta.ingredientes = this.receta?.ingredientes || [];
      receta.instrucciones = this.receta?.instrucciones || [];

      // Imagen (fichero) también se arrastra desde la receta original
      receta.imagen_file = this.receta?.imagen_file;

      // Validación adicional: todos los ingredientes deben tener cantidad > 0
      if (!this.validarIngredientes()) {
        // Marca el formulario con un error y guarda mensaje para la UI
        this.form.setErrors({ ingredientesInvalid: true })
        return
      } else {
        // Limpia posible error previo
        this.ingredientesError = null;
        // Si no quedan otros errores, limpia errores del form
        if (this.form.errors && this.form.errors['ingredientesInvalid']) {
          this.form.setErrors(null)
        }
      }


      // Emitimos la receta al componente padre para que la guarde (crear/editar)
      this.guardarReceta.emit(receta);

      // Reseteamos el formulario (limpia campos y estado)
      this.form.reset();
    }
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
    // Avisamos al componente padre de que se ha cancelado
    this.cancelarAccion.emit();

    // Reseteamos el formulario
    this.form.reset();
  }

  // ============================
  //  Selección de imagen para la receta
  // ============================
  onImagenSeleccionada($event: Event) {
    // Obtenemos el input que lanzó el evento
    const input = $event.target as HTMLInputElement;

    // Comprobamos que haya fichero seleccionado
    if (input.files && input.files[0]) {
      const file = input.files[0];

      // Si existe un objeto receta, le asignamos el fichero
      if (this.receta) {
        this.receta.imagen_file = file;

        // Creamos un lector de archivos para generar la vista previa
        const reader = new FileReader();
        reader.onload = (e: any) => {
          // Guardamos el resultado como imagen_preview para mostrarla en el HTML
          this.receta!.imagen_preview = e.target.result;
        };
        // Leemos el fichero como DataURL (base64)
        reader.readAsDataURL(file);
      }
    }
  }

  // ============================
  //  Gestión dinámica de INGREDIENTES
  // ============================
  agregarIngrediente() {
    // Añade una nueva fila de ingrediente vacío a la lista de la receta
    this.receta?.ingredientes.push({
      cantidad: 0,
      unidad: '',
      ingrediente: { id: 0, nombre: '' },
    });
  }

  eliminarIngrediente(index: number) {
    // Elimina el ingrediente en la posición 'index'
    this.receta?.ingredientes.splice(index, 1);
  }

  // ============================
  //  Gestión dinámica de INSTRUCCIONES
  // ============================
  agregarInstruccion() {
    // Calcula el siguiente número de paso según la longitud actual
    const nuevoPaso = this.receta ? this.receta.instrucciones.length + 1 : 1;

    // Añade una nueva instrucción vacía
    this.receta?.instrucciones.push({
      orden: nuevoPaso,
      descripcion: '',
    });
  }

  eliminarInstruccion(index: number) {
    // Elimina la instrucción en la posición 'index'
    this.receta?.instrucciones.splice(index, 1);
  }

  // Manejo de imagen asociada a una instrucción concreta (pendiente de completar)
  onInstruccionImagenSeleccionada(event: Event, instruccion: any) {
    const input = event.target as HTMLInputElement;
    // Aquí se podría implementar la lógica de subir/preview por cada instrucción
  }
}
