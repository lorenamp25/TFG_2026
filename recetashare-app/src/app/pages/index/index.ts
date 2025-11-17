import { AfterViewInit, Component } from '@angular/core';
import { Categoria } from '../../models/categoria.model';
import { CategoriaService } from '../../services/categoria.service';
import { Cardcategoria } from '../../components/cardcategoria/cardcategoria';
import { SlicePipe } from '@angular/common';
import { RecetaService } from '../../services/receta.service';
import { Cardreceta } from '../../components/cardreceta/cardreceta';
import { Minicardreceta } from '../../components/minicardreceta/minicardreceta';

@Component({
  selector: 'app-index',
  imports: [Cardcategoria, Cardreceta, Minicardreceta, SlicePipe],
  templateUrl: './index.html',
  styleUrls: ['./index.css']
})
export class Index {
  categorias: Categoria[] = []
  cargandoCategorias: boolean = true

  recetasDestacadas: any[] = []
  recetasInicio: any[] = []
  cargandoRecetas: boolean = true

  constructor(private categoriaService: CategoriaService, private recetaService: RecetaService) { }

  ngOnInit(): void {
    this.cargarDatos()
  }

  cargarDatos() {
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response
        this.cargandoCategorias = false
      }
    )

    this.recetaService.listarRecetas().subscribe(
      (response: any) => {
        this.recetasDestacadas = response.filter((receta: any) => receta.destacada === true);
        this.recetasInicio = this.recetasDestacadas.slice(0, 3);

        this.cargandoRecetas = false
      }
    )
  }
}
