import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CategoriaService } from '../../services/categoria.service';
import { Categoria } from '../../models/categoria.model';
import { Cardcategoria } from '../../components/cardcategoria/cardcategoria';

@Component({
  standalone: true,
  selector: 'app-categoria',
  imports: [CommonModule, Cardcategoria],
  templateUrl: './categoria.html',
  styleUrls: ['./categoria.css']
})
export class CategoriaPage {
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
