import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CategoriaService } from '../../services/categoria.service';
import { Categoria } from '../../models/categoria.model';
import { CategoriaForm } from '../../components/categoria-form/categoria-form';
import { EstadoAccion } from '../../models/estadoaccion.enum';
import { CategoriaTablaComponent } from '../../components/categoria-tabla/categoria-tabla';

@Component({
  standalone: true,
  selector: 'app-categoria',
  imports: [CommonModule, CategoriaTablaComponent, CategoriaForm],
  templateUrl: './categoria.html',
  styleUrls: ['./categoria.css']
})
export class CategoriaPage {
  categorias: Categoria[] = []
  categoria: Categoria | null = null
  estado: EstadoAccion = EstadoAccion.Listando

  constructor(private categoriaService: CategoriaService) { }

  ngOnInit(): void {
    this.cargarCategorias()
  }

  cargarCategorias() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response
        this.estado = EstadoAccion.Listando
      }
    )
  }

  agregarCategoria() {
    this.estado = EstadoAccion.Agregando
  }

  onGuardar(categoria: Categoria) {
    switch (this.estado) {
      case EstadoAccion.Agregando:
        this.categoriaService.crearCategoria(categoria)
          .subscribe((categoria) => {
          this.categoria = null
          this.cargarCategorias()
          })
        break

      case EstadoAccion.Editando:
        this.categoriaService.actualizarCategoria(categoria.id, categoria)
          .subscribe((categoria) => {
          this.categoria = null
          this.cargarCategorias()
          })
        break

      case EstadoAccion.Borrando:
        this.categoriaService.eliminarCategoria(categoria.id)
          .subscribe(() => {
          this.categoria = null
          this.cargarCategorias()
          })
        break
    }
  }

  onCancelar() {
    this.categoria = null
    this.cargarCategorias()
  }

  onEditarCategoria(categoria: any) {
    this.categoria = categoria
    this.estado = EstadoAccion.Editando
  }

  onEliminarCategoria(categoria: any) {
    this.categoria = categoria
    this.estado = EstadoAccion.Borrando
  }
}
