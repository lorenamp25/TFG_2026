import { Component } from '@angular/core';
import { CategoriaService } from '../../services/categoria.service';

@Component({
  selector: 'app-categoria',
  imports: [],
  templateUrl: './categoria.html',
  styleUrl: './categoria.css'
})
export class Categoria {
  categorias: Categoria[] = []

  constructor(private categoriaService: CategoriaService) { }

  ngOnInit(): void {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response
      }
    )
  }

  onEditCategoria(categoria: Categoria) {
    console.log("Editando")
    console.log(categoria)
  }

  onDeleteCategoria(categoria: Categoria) {
    console.log("Borrando")
    console.log(categoria)
  }
}
