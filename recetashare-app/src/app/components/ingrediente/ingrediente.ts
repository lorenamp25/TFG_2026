import { Component, OnInit } from '@angular/core';
import { IngredienteService } from '../../services/ingrediente.service';

@Component({
  standalone: true,
  selector: 'app-ingrediente',
  imports: [],
  templateUrl: './ingrediente.html',
  styleUrls: ['./ingrediente.css']
})
export class IngredientesComponent implements OnInit {

  ingredientes: any[] = [];   // aquí se guardarán los ingredientes reales

  constructor(private ingredienteService: IngredienteService) {}

  ngOnInit(): void {
    this.cargarIngredientes();
  }

  cargarIngredientes() {
    this.ingredienteService.listarIngredientes().subscribe({
      next: (resp: any) => {
        console.log("Ingredientes recibidos:", resp);
        
        // Si la API devuelve un array directo → funciona así:
        this.ingredientes = resp;

        // Si tu backend devuelve { data: [...] } usa:
        // this.ingredientes = resp.data;
      },
      error: (err: any) => {
        console.error("Error cargando ingredientes:", err);
      }
    });
  }

  onEditar(ingrediente: any) {
    console.log("Editar ingrediente:", ingrediente);
    // Aquí va tu lógica de edición
  }

  onEliminar(ingrediente: any) {
    console.log("Eliminar ingrediente:", ingrediente);
    // Aquí va tu lógica de eliminación
  }
}