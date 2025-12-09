import { Component, OnInit } from '@angular/core';
import { IngredienteService } from '../../services/ingrediente.service';

@Component({
  // Componente standalone (no necesita declararse en un módulo)
  standalone: true,

  // Nombre de la etiqueta HTML para usar este componente
  selector: 'app-ingrediente',

  // Módulos que el componente requiere (ninguno por ahora)
  imports: [],

  // Plantilla HTML asociada
  templateUrl: './ingrediente.html',

  // Hoja de estilos del componente
  styleUrls: ['./ingrediente.css']
})
export class IngredientesComponent implements OnInit {

  // Aquí se cargará el listado real de ingredientes que viene del backend
  ingredientes: any[] = [];

  // Inyección del servicio que consulta la API
  constructor(private ingredienteService: IngredienteService) {}

  // Método del ciclo de vida de Angular: se ejecuta al iniciar el componente
  ngOnInit(): void {
    this.cargarIngredientes();
  }

  // ============================
  // Cargar ingredientes desde la API
  // ============================
  cargarIngredientes() {
    this.ingredienteService.listarIngredientes().subscribe({
      next: (resp: any) => {
        // Caso 1: el backend devuelve un array directamente
        this.ingredientes = resp;

        // Caso 2 (alternativo): el backend devuelve un objeto con { data: [] }
        // this.ingredientes = resp.data;
      },
      error: (err: any) => {
        console.error("Error cargando ingredientes:", err);
      }
    });
  }

}
