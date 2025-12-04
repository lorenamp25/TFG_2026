import { Component } from '@angular/core';
import { EstadoAccion } from '../../../models/estadoaccion.enum';
import { Ingrediente } from '../../../models/ingrediente.model';
import { CommonModule } from '@angular/common';
import { IngredienteTabla } from '../../../components/ingrediente-tabla/ingrediente-tabla';
import { IngredienteForm } from '../../../components/ingrediente-form/ingrediente-form';
import { IngredienteService } from '../../../services/ingrediente.service';
import { ModalService } from '../../../components/modal-component/modal.service';

@Component({
  selector: 'app-ingrediente-admin',
  imports: [CommonModule, IngredienteTabla, IngredienteForm],
  templateUrl: './ingrediente-admin.html',
  styleUrl: './ingrediente-admin.css'
})
export class IngredienteAdmin {
  ingredientes: Ingrediente[] = []                 // Lista de categorías cargadas desde el backend
  ingrediente: Ingrediente | null = null           // Categoría seleccionada para editar/borrar
  estado: EstadoAccion = EstadoAccion.Listando // Estado inicial: solo muestra la lista

  // Inyecta el servicio para gestionar categorías
  constructor(private ingredienteService: IngredienteService, private modalService: ModalService) { }

  // Se ejecuta cuando el componente inicia (similar a ngOnLoad)
  ngOnInit(): void {
    this.cargarIngredientes()                    // Carga las categorías al iniciar
  }

  // Obtiene todas las categorías desde el backend
  cargarIngredientes() {
    this.estado = EstadoAccion.Procesando
    this.ingredienteService.listarIngredientes().subscribe(
      (response: any) => {
        this.ingredientes = response             // Guarda el resultado en el array
        this.estado = EstadoAccion.Listando    // Vuelve al modo listado
      }
    )
  }

  // Cambia a estado de agregar una categoría
  agregarIngrediente() {
    this.estado = EstadoAccion.Agregando
  }

  // Maneja la acción de guardar, dependiendo del estado actual
  onGuardar(ingrediente: Ingrediente) {
    switch (this.estado) {

      // Crear nueva categoría
      case EstadoAccion.Agregando:
        this.ingredienteService.crearIngrediente(ingrediente)
          .subscribe((ingrediente) => {
            this.ingrediente = null              // Limpia la categoría seleccionada
            this.cargarIngredientes()            // Recarga la lista
            this.modalService.success('Ingrediente creado', 'El ingrediente ha sido creado correctamente.');
          })
        break

      // Actualizar una ingrediente existente
      case EstadoAccion.Editando:
        this.ingredienteService.actualizarIngrediente(ingrediente.id, ingrediente)
          .subscribe((ingrediente) => {
            this.ingrediente = null
            this.cargarIngredientes()
            this.modalService.success('Ingrediente actualizado', 'El ingrediente ha sido actualizado correctamente.');
          })
        break

      // Eliminar categoría
      case EstadoAccion.Borrando:
        this.ingredienteService.eliminarIngrediente(ingrediente.id)
          .subscribe(() => {
            this.ingrediente = null
            this.cargarIngredientes()
            this.modalService.success('Ingrediente eliminado', 'El ingrediente ha sido eliminado correctamente.');
          })
        break
    }
  }

  // Cancelar edición/agregado/borrado y volver al listado
  onCancelar() {
    this.ingrediente = null
    this.cargarIngredientes()
  }

  // Cuando el usuario hace clic en editar desde la tabla
  onEditarIngrediente(ingrediente: any) {
    this.ingrediente = this.ingrediente                 // Guarda la categoría elegida
    this.estado = EstadoAccion.Editando        // Cambia al modo edición
  }

  // Cuando el usuario hace clic en eliminar desde la tabla
  onEliminarIngrediente(ingrediente: any) {
    this.ingrediente = ingrediente                 // Guarda la categoría a borrar
    this.estado = EstadoAccion.Borrando        // Cambia al modo borrado
  }
}
