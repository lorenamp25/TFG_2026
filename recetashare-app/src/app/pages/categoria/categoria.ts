import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CategoriaService } from '../../services/categoria.service';
import { Categoria } from '../../models/categoria.model';
import { CategoriaComponent } from '../../components/categoria-index/categoria-index';

@Component({
  standalone: true,
  selector: 'app-categoria',
  imports: [CommonModule, CategoriaComponent],
  templateUrl: './categoria.html',
  styleUrls: ['./categoria.css']
})
export class CategoriaPage {
  categorias: Categoria[] = []

  constructor(private categoriaService: CategoriaService) { }

  ngOnInit(): void {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response
      }
    )
  }

  onEditCategoria(categoria: any) {
    console.log("Editando")
    console.log(categoria)
  }

  onDeleteCategoria(categoria: any) {
    console.log("Borrando")
    console.log(categoria)
  }
}
