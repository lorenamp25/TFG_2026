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
  standalone: true,
  selector: 'app-receta',
  imports: [CommonModule, RecetaTabla, RecetaForm],
  templateUrl: './receta.html',
  styleUrls: ['./receta.css']
})
export class RecetaPage {
  recetas: Receta[] = []
  receta: Receta | null = null
  estado: EstadoAccion = EstadoAccion.Listando
  categorias: Categoria[] = []

  constructor(private recetaService: RecetaService, private categoriaService: CategoriaService) { }

  ngOnInit(): void {
    this.cargarRecetas()
    this.cargarCategorias()
  }

  cargarRecetas() {
    this.estado = EstadoAccion.Procesando
    this.recetaService.listarRecetas().subscribe(
      (response: any) => {
        this.recetas = response
        this.estado = EstadoAccion.Listando
      }
    )
  }

  cargarCategorias() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response
      }
    )
  }

  filtrarPorCategoria(categoria: any) {
    this.estado = EstadoAccion.Procesando
    if (categoria && categoria.value) {
      this.recetaService.listarRecetas().subscribe(
        (response: any) => {
          console.log(response);
          this.recetas = response.filter((receta: Receta) => receta.categoria === parseInt(categoria.value, 10));
          this.estado = EstadoAccion.Listando
        }
      )
    } else {
      this.cargarRecetas()
    }
  }

  agregarReceta() {
    this.estado = EstadoAccion.Agregando
  }

  onGuardar(receta: Receta) {
    switch (this.estado) {
      case EstadoAccion.Agregando:
        this.estado = EstadoAccion.Procesando
        this.recetaService.crearReceta(receta)
          .subscribe((receta) => {
          this.receta = null
          this.cargarRecetas()
          })
        break

      case EstadoAccion.Editando:
        this.estado = EstadoAccion.Procesando
        this.recetaService.actualizarReceta(receta.id, receta)
          .subscribe((receta) => {
          this.receta = null
          this.cargarRecetas()
          })
        break

      case EstadoAccion.Borrando:
        this.estado = EstadoAccion.Procesando
        this.recetaService.eliminarReceta(receta.id)
          .subscribe(() => {
          this.receta = null
          this.cargarRecetas()
          })
        break
    }
  }

  onCancelar() {
    this.receta = null
    this.cargarRecetas()
  }

  onEditarReceta(receta: any) {
    this.receta = receta
    this.estado = EstadoAccion.Editando
  }

  onEliminarReceta(receta: any) {
    this.receta = receta
    this.estado = EstadoAccion.Borrando
  }
}
