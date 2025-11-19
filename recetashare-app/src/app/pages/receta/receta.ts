import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RecetaService } from '../../services/receta.service';
import { RecetaTabla } from '../../components/receta-tabla/receta-tabla';
import { EstadoAccion } from '../../models/estadoaccion.enum';
import { Receta } from '../../models/receta.model';
import { RecetaForm } from '../../components/receta-form/receta-form';
import { CategoriaService } from '../../services/categoria.service';
import { Categoria } from '../../models/categoria.model';

@Component({
  standalone: true,                               // Componente independiente
  selector: 'app-receta',                          // Selector para usar este componente
  imports: [CommonModule, RecetaTabla, RecetaForm],// Importa módulos y componentes necesarios
  templateUrl: './receta.html',                    // Archivo HTML asociado
  styleUrls: ['./receta.css']                      // Archivo CSS asociado
})
export class RecetaPage {
  recetas: Receta[] = []                           // Lista de recetas obtenidas del backend
  receta: Receta | null = null                     // Receta seleccionada para editar/eliminar
  estado: EstadoAccion = EstadoAccion.Listando     // Estado actual de la interfaz (listando/agregando/etc.)
  categorias: Categoria[] = []                     // Lista de categorías para filtrar

  constructor(
    private recetaService: RecetaService,          // Servicio para hacer peticiones relacionadas a recetas
    private categoriaService: CategoriaService     // Servicio de categorías
  ) { }

  ngOnInit(): void {
    this.cargarRecetas()                           // Al inicio, carga todas las recetas
    this.cargarCategorias()                        // Y también carga las categorías
  }

  cargarRecetas() {
    this.estado = EstadoAccion.Procesando          // Cambia el estado a “cargando”
    this.recetaService.listarRecetas().subscribe(
      (response: any) => {
        this.recetas = response                    // Guarda las recetas recibidas
        this.estado = EstadoAccion.Listando        // Vuelve al estado de listado
      }
    )
  }

  cargarCategorias() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response                 // Guarda las categorías
      }
    )
  }

  filtrarPorCategoria(categoria: any) {
    this.estado = EstadoAccion.Procesando          // Cambia a modo “procesando”

    // Si existe un valor seleccionado
    if (categoria && categoria.value) {
      this.recetaService.listarRecetas().subscribe(
        (response: any) => {
          console.log(response);
          // Filtra recetas por categoría seleccionada
          this.recetas = response.filter(
            (receta: Receta) => receta.categoria === parseInt(categoria.value, 10)
          );
          this.estado = EstadoAccion.Listando
        }
      )
    } else {
      // Si se selecciona “todas”, carga todas de nuevo
      this.cargarRecetas()
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
            this.cargarRecetas()                    // Recarga datos
          })
        break

      case EstadoAccion.Editando:
        this.estado = EstadoAccion.Procesando       // Procesando actualización
        this.recetaService.actualizarReceta(receta.id, receta)
          .subscribe((receta) => {
            this.receta = null
            this.cargarRecetas()
          })
        break

      case EstadoAccion.Borrando:
        this.estado = EstadoAccion.Procesando       // Procesando borrado
        this.recetaService.eliminarReceta(receta.id)
          .subscribe(() => {
            this.receta = null
            this.cargarRecetas()
          })
        break
    }
  }

  onCancelar() {
    this.receta = null                              // Limpia la receta seleccionada
    this.cargarRecetas()                            // Regresa al listado
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
