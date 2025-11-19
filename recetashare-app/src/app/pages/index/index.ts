import { AfterViewInit, Component } from '@angular/core';
import { Categoria } from '../../models/categoria.model';
import { CategoriaService } from '../../services/categoria.service';
import { Cardcategoria } from '../../components/cardcategoria/cardcategoria';
import { SlicePipe } from '@angular/common';
import { RecetaService } from '../../services/receta.service';
import { Cardreceta } from '../../components/cardreceta/cardreceta';
import { Minicardreceta } from '../../components/minicardreceta/minicardreceta';

@Component({
  selector: 'app-index',                             // Nombre del componente usado en HTML
  imports: [Cardcategoria, Cardreceta, Minicardreceta, SlicePipe], // Módulos y componentes incluidos
  templateUrl: './index.html',                       // Plantilla HTML asociada
  styleUrls: ['./index.css']                         // Estilos del componente
})
export class Index {
  categorias: Categoria[] = []                       // Array donde se guardan las categorías cargadas
  cargandoCategorias: boolean = true                 // Flag para mostrar "Cargando..." hasta que lleguen

  recetasDestacadas: any[] = []                      // Lista de todas las recetas destacadas
  recetasInicio: any[] = []                          // Solo las primeras 3 recetas destacadas para la portada
  cargandoRecetas: boolean = true                    // Flag de carga para recetas

  constructor(
    private categoriaService: CategoriaService,      // Inyección del servicio de categorías
    private recetaService: RecetaService             // Inyección del servicio de recetas
  ) { }

  ngOnInit(): void {
    this.cargarDatos()                               // Al iniciar el componente, se cargan los datos
  }

  cargarDatos() {
    // === CARGAR CATEGORÍAS DE LA API ===
    this.categoriaService.listarCategorias().subscribe(
      (response: any) => {
        this.categorias = response                   // Guarda las categorías traídas del backend
        this.cargandoCategorias = false              // Oculta el cargando
      }
    )

    // === CARGAR RECETAS DE LA API ===
    this.recetaService.listarRecetas().subscribe(
      (response: any) => {

        // Filtra solo las recetas que tienen "destacada = true"
        this.recetasDestacadas = response.filter(
          (receta: any) => receta.destacada === true
        );

        // Toma solo las 3 primeras para mostrarlas en la parte superior
        this.recetasInicio = this.recetasDestacadas.slice(0, 3);

        this.cargandoRecetas = false                 // Termina la carga de recetas
      }
    )
  }
}
