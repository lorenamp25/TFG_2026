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
        // Si la API devuelve un array directo:
        this.ingredientes = resp;

        // Si tu backend devuelve { data: [...] }:
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

  // ============================
  //  Normalizar nombre (tildes, mayúsculas...)
  // ============================
// Normaliza el nombre (quita tildes y mayúsculas)
private normalizarNombre(nombre: any): string {
  return (nombre || '')
    .toString()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim();
}

getIconoIngrediente(ingrediente: any): string {
  const n = this.normalizarNombre(ingrediente?.nombre);

  // Base original
  if (n.includes('harina')) return '🥣';
  if (n.includes('azucar')) return '🍬';
  if (n === 'sal') return '🧂';
  if (n.includes('pimienta')) return '🌶️';
  if (n.includes('aceite')) return '🫒';
  if (n.includes('huevo')) return '🥚';
  if (n === 'leche' || n.includes('leche')) return '🥛';
  if (n.includes('mantequilla')) return '🧈';
  if (n.includes('ajo')) return '🧄';
  if (n.includes('cebolla')) return '🧅';
  if (n.includes('tomate')) return '🍅';
  if (n === 'queso') return '🧀';
  if (n.includes('pollo')) return '🍗';
  if (n.includes('ternera') || n.includes('carne')) return '🥩';
  if (n.includes('perejil')) return '🌿';
  if (n.includes('albahaca')) return '🌿';
  if (n === 'arroz') return '🍚';
  if (n.includes('pasta')) return '🍝';
  if (n.includes('patata')) return '🥔';
  if (n.includes('zanahoria')) return '🥕';
  if (n.includes('azucar')) return '🧂';
  // 👉 Nuevos ingredientes que no te estaba detectando
  if (n.includes('yogur')) return '🥛';
  if (n.includes('vinagre')) return '🍾';
  if (n.includes('vainilla')) return '🌼';
  if (n.includes('tofu')) return '🍱';
  if (n.includes('salmon')) return '🐟';
  if (n.includes('romero')) return '🌿';
  if (n.includes('parmesano')) return '🧀';
  if (n.includes('mozzarella')) return '🧀';
  if (n.includes('cheddar')) return '🧀';
  if (n.includes('platano') || n.includes('banana')) return '🍌';

  if (n.includes('pimiento verde')) return '🫑';
  if (n.includes('pimiento rojo')) return '🫑';
  if (n.includes('pimiento')) return '🫑';

  if (n.includes('pechuga')) return '🍗';
  if (n.includes('pan rallado')) return '🍞';
  if (n.includes('oregano')) return '🌿';
  if (n.includes('nata')) return '🥛';
  if (n.includes('miel')) return '🍯';
  if (n.includes('merluza')) return '🐟';
  if (n.includes('manzana')) return '🍎';
  if (n.includes('limon')) return '🍋';
  if (n.includes('lenteja')) return '🫘';
  if (n.includes('lechuga')) return '🥬';
  if (n.includes('harina de trigo')) return '🥣';
  if (n.includes('gamba') || n.includes('camaron')) return '🦐';
  if (n.includes('fresa')) return '🍓';
  if (n.includes('filete')) return '🥩';
  if (n.includes('espinaca')) return '🥬';
  if (n.includes('chocolate')) return '🍫';
  if (n.includes('canela')) return '🌰';
  if (n.includes('calabacin')) return '🥒';
  if (n.includes('berenjena')) return '🍆';
  if (n.includes('avena')) return '🌾';
  if (n.includes('arroz integral')) return '🍚';
  if (n.includes('arroz blanco')) return '🍚';

  // Por defecto
  return '🧂';
}


}
