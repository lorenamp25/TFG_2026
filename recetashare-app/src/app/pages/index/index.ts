import { AfterViewInit, Component } from '@angular/core';
import { Categoria } from '../../models/categoria.model';
import { CategoriaService } from '../../services/categoria.service';
import { Cardcategoria } from '../../components/cardcategoria/cardcategoria';
import { SlicePipe } from '@angular/common';

@Component({
  selector: 'app-index',
  imports: [Cardcategoria, SlicePipe],
  templateUrl: './index.html',
  styleUrls: ['./index.css']
})
export class Index {
  categorias: Categoria[] = []

  constructor(private categoriaService: CategoriaService) { }

  ngOnInit(): void {
    this.cargarCategorias()
  }

  cargarCategorias() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response
      }
    )
  }


}
