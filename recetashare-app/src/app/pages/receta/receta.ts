import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RecetaService } from '../../services/receta.service';
import { RecetaTabla } from '../../components/receta-tabla/receta-tabla';
import { EstadoAccion } from '../../models/estadoaccion.enum';
import { Receta } from '../../models/receta.model';
import { RecetaForm } from '../../components/receta-form/receta-form';
import { CategoriaService } from '../../services/categoria.service';
import { Categoria } from '../../models/categoria.model';
import { ActivatedRoute } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

@Component({
  standalone: true,                               // Componente independiente
  selector: 'app-receta',                          // Selector para usar este componente
  imports: [CommonModule, RecetaTabla, RecetaForm, FormsModule],// Importa módulos y componentes necesarios
  templateUrl: './receta.html',                    // Archivo HTML asociado
  styleUrls: ['./receta.css']                      // Archivo CSS asociado
})
export class RecetaPage {
seleccionarCategoria(arg0: string) {
throw new Error('Method not implemented.');
}
dropdownAbierto: any;
toggleDropdown() {
throw new Error('Method not implemented.');
}
  recetas: Receta[] = []                           // Lista de recetas obtenidas del backend
  receta: Receta | null = null                     // Receta seleccionada para editar/eliminar
  estado: EstadoAccion = EstadoAccion.Listando     // Estado actual de la interfaz (listando/agregando/etc.)
  categorias: Categoria[] = []                     // Lista de categorías para filtrar
  idCategoriaSeleccionada: String | null = null;   // ID de la categoría seleccionada para filtrar
  selectedCategoria: any = 'all';                  // valor por defecto -> "Todas las Categorías"

  constructor(
    private recetaService: RecetaService,          // Servicio para hacer peticiones relacionadas a recetas
    private categoriaService: CategoriaService,    // Servicio de categorías
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    this.idCategoriaSeleccionada = this.route.snapshot.paramMap.get('categoria');
    if (this.idCategoriaSeleccionada) {
      this.selectedCategoria = this.idCategoriaSeleccionada
      this.cargarRecetas({
        value: this.idCategoriaSeleccionada
      }); // Filtra recetas por categoría si se proporciona en la ruta
    }
    else {
      this.idCategoriaSeleccionada = null;
      this.cargarRecetas(null)                     // Carga todas las recetas si no hay categoría seleccionada
    }

    this.cargarCategorias()                        // Y también carga las categorías
  }

  cargarCategorias() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response                 // Guarda las categorías
      }
    )
  }

  cargarRecetas(categoria: any) {
    this.estado = EstadoAccion.Procesando          // Cambia a modo “procesando”

    // Si existe un valor seleccionado
    if (categoria && categoria.value && categoria.value !== 'all') {
      this.recetaService.listarRecetas().subscribe(
        (response: any) => {
          // Filtra recetas por categoría seleccionada
          this.recetas = response.filter(
            (receta: Receta) => receta.categoria === parseInt(categoria.value, 10)
          );
          this.estado = EstadoAccion.Listando
        }
      )
    } else {
      // Si se selecciona “todas”, carga todas de nuevo
      this.recetaService.listarRecetas().subscribe(
        (response: any) => {
          this.selectedCategoria = "all"
          this.recetas = response                    // Guarda las recetas recibidas
          this.estado = EstadoAccion.Listando        // Vuelve al estado de listado
        }
      )
    }
  }

  agregarReceta() {
    this.estado = EstadoAccion.Agregando           // Cambia a modo "agregando receta"
  }

  onGuardar(receta: Receta) {
    switch (this.estado) {

      case EstadoAccion.Agregando:
        this.estado = EstadoAccion.Procesando       // Procesando creación
        this.recetaService.crearReceta(receta)
          .subscribe((receta) => {
            this.receta = null                      // Limpia selección
            this.cargarRecetas(null)                    // Recarga datos
          })
        break

      case EstadoAccion.Editando:
        this.estado = EstadoAccion.Procesando       // Procesando actualización
        this.recetaService.actualizarReceta(receta.id, receta)
          .subscribe((receta) => {
            this.receta = null
            this.cargarRecetas(null)
          })
        break

      case EstadoAccion.Borrando:
        this.estado = EstadoAccion.Procesando       // Procesando borrado
        this.recetaService.eliminarReceta(receta.id)
          .subscribe(() => {
            this.receta = null
            this.cargarRecetas(null)
          })
        break
    }
  }

  onCancelar() {
    this.receta = null                              // Limpia la receta seleccionada
    this.cargarRecetas(null)                            // Regresa al listado
  }

  onEditarReceta(receta: any) {
    this.receta = receta                             // Carga la receta a editar
    this.estado = EstadoAccion.Editando             // Cambia a estado de edición
  }

  onEliminarReceta(receta: any) {
    this.receta = receta                             // Carga la receta a eliminar
    this.estado = EstadoAccion.Borrando             // Cambia a estado de borrado
  }
}
