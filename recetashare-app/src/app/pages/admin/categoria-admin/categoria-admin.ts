// Importa Component para definir el componente
import { Component } from '@angular/core';

// Importa el modelo de Categoria
import { Categoria } from '../../../models/categoria.model';

// Importa el enum con los estados posibles (Listando, Agregando, Editando, Borrando)
import { EstadoAccion } from '../../../models/estadoaccion.enum';

// Importa el servicio que maneja las peticiones HTTP de categorías
import { CategoriaService } from '../../../services/categoria.service';

// Importa CommonModule para directivas estándar (*ngIf, *ngFor, etc.)
import { CommonModule } from '@angular/common';

// Importa el componente de tabla de categorías
import { CategoriaTablaComponent } from '../../../components/categoria-tabla/categoria-tabla';

// Importa el formulario de categorías
import { CategoriaForm } from '../../../components/categoria-form/categoria-form';

@Component({
  selector: 'app-categoria-admin',              // Nombre del selector del componente
  imports: [CommonModule, CategoriaTablaComponent, CategoriaForm], // Módulos y componentes usados
  templateUrl: './categoria-admin.html',        // Vista HTML del componente
  styleUrl: './categoria-admin.css'             // Estilos del componente
})
export class CategoriaAdmin {
  categorias: Categoria[] = []                 // Lista de categorías cargadas desde el backend
  categoria: Categoria | null = null           // Categoría seleccionada para editar/borrar
  estado: EstadoAccion = EstadoAccion.Listando // Estado inicial: solo muestra la lista

  // Inyecta el servicio para gestionar categorías
  constructor(private categoriaService: CategoriaService) { }

  // Se ejecuta cuando el componente inicia (similar a ngOnLoad)
  ngOnInit(): void {
    this.cargarCategorias()                    // Carga las categorías al iniciar
  }

  // Obtiene todas las categorías desde el backend
  cargarCategorias() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response             // Guarda el resultado en el array
        this.estado = EstadoAccion.Listando    // Vuelve al modo listado
      }
    )
  }

  // Cambia a estado de agregar una categoría
  agregarCategoria() {
    this.estado = EstadoAccion.Agregando
  }

  // Maneja la acción de guardar, dependiendo del estado actual
  onGuardar(categoria: Categoria) {
    switch (this.estado) {

      // Crear nueva categoría
      case EstadoAccion.Agregando:
        this.categoriaService.crearCategoria(categoria)
          .subscribe((categoria) => {
            this.categoria = null              // Limpia la categoría seleccionada
            this.cargarCategorias()            // Recarga la lista
          })
        break

      // Actualizar una categoría existente
      case EstadoAccion.Editando:
        this.categoriaService.actualizarCategoria(categoria.id, categoria)
          .subscribe((categoria) => {
            this.categoria = null
            this.cargarCategorias()
          })
        break

      // Eliminar categoría
      case EstadoAccion.Borrando:
        this.categoriaService.eliminarCategoria(categoria.id)
          .subscribe(() => {
            this.categoria = null
            this.cargarCategorias()
          })
        break
    }
  }

  // Cancelar edición/agregado/borrado y volver al listado
  onCancelar() {
    this.categoria = null
    this.cargarCategorias()
  }

  // Cuando el usuario hace clic en editar desde la tabla
  onEditarCategoria(categoria: any) {
    this.categoria = categoria                 // Guarda la categoría elegida
    this.estado = EstadoAccion.Editando        // Cambia al modo edición
  }

  // Cuando el usuario hace clic en eliminar desde la tabla
  onEliminarCategoria(categoria: any) {
    this.categoria = categoria                 // Guarda la categoría a borrar
    this.estado = EstadoAccion.Borrando        // Cambia al modo borrado
  }
}
