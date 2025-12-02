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

  // ============================
  //  Botón EDITAR ingrediente
  // ============================
  onEditar(ingrediente: any) {
    console.log("Editar ingrediente:", ingrediente);
    // Aquí irá tu lógica para abrir diálogo o navegar a un formulario
  }

  // ============================
  //  Botón ELIMINAR ingrediente
  // ============================
  onEliminar(ingrediente: any) {
    console.log("Eliminar ingrediente:", ingrediente);
    // Aquí implementarás confirmación + llamada al servicio para borrar
  }

  // ============================
  //  Normalizar nombre (tildes y mayúsculas)
  //  Esto permite comparar palabras sin errores
  // ============================
  private normalizarNombre(nombre: any): string {
    return (nombre || '')
      .toString()
      .toLowerCase()                     // Convierte en minúsculas
      .normalize('NFD')                  // Separa letras y tildes
      .replace(/[\u0300-\u036f]/g, '')   // Elimina las tildes
      .trim();                           // Quita espacios extra
  }

  // ============================
  //  Obtener el icono según el ingrediente
  //  Lógica basada en coincidencias dentro del nombre normalizado
  // ============================
  getIconoIngrediente(ingrediente: any): string {
    const n = this.normalizarNombre(ingrediente?.nombre);

    // Base original (detecciones más comunes)
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

    // Nuevos ingredientes (no detectados antes)
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

    // Variantes de pimientos
    if (n.includes('pimiento verde')) return '🫑';
    if (n.includes('pimiento rojo')) return '🫑';
    if (n.includes('pimiento')) return '🫑';

    // Más detecciones útiles
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

    // Icono por defecto si ninguno coincide
    return '🧂';
  }

}
