import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { ModalType } from './modal.component';

// Interfaz que define la estructura que debe tener un modal:
// título, mensaje y tipo (success, error, warning…)
export interface ModalConfig {
  title: string;
  message: string;
  type: ModalType;
}

@Injectable({
  providedIn: 'root' 
})
export class ModalService {

  // guarda el último valor emitido y lo entrega a cualquier suscriptor.
  // Aquí guardamos la configuración del modal (o null si no debe mostrarse).
  private modalSubject = new BehaviorSubject<ModalConfig | null>(null);

  // Observable al que se suscriben los componentes que deben reaccionar
  // cuando se muestra u oculta un modal.
  modal$ = this.modalSubject.asObservable();

  // -----------------------------------------------
  // MÉTODO PRINCIPAL: Mostrar un modal
  // -----------------------------------------------
  // Recibe una configuración y la emite por el BehaviorSubject.//visible o culto 
  // El wrapper la recibirá y mostrará el modal.
  show(config: ModalConfig) {
    this.modalSubject.next(config);
  }

  // -----------------------------------------------
  // Ocultar el modal (simplemente emitir null)
  // -----------------------------------------------
  hide() {
    this.modalSubject.next(null);
  }

  // -----------------------------------------------
  // Métodos de conveniencia (atajos)
  // -----------------------------------------------
  // Permiten mostrar distintos tipos de modal sin repetir código:

  success(title: string, message: string) {
    this.show({ title, message, type: 'success' });
  }

  error(title: string, message: string) {
    this.show({ title, message, type: 'error' });
  }

  warning(title: string, message: string) {
    this.show({ title, message, type: 'warning' });
  }
}
