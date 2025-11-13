import { Component } from '@angular/core';
import { Categoria } from '../../../models/categoria.model';
import { EstadoAccion } from '../../../models/estadoaccion.enum';
import { CategoriaService } from '../../../services/categoria.service';
import { CommonModule } from '@angular/common';
import { CategoriaTablaComponent } from '../../../components/categoria-tabla/categoria-tabla';
import { CategoriaForm } from '../../../components/categoria-form/categoria-form';

@Component({
  selector: 'app-categoria-admin',
  imports: [CommonModule, CategoriaTablaComponent, CategoriaForm],
  templateUrl: './categoria-admin.html',
  styleUrl: './categoria-admin.css'
})
export class CategoriaAdmin {
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
