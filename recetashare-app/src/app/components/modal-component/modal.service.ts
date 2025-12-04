import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { ModalType } from './modal.component';

export interface ModalConfig {
  title: string;
  message: string;
  type: ModalType;
}

@Injectable({
  providedIn: 'root'
})
export class ModalService {
  private modalSubject = new BehaviorSubject<ModalConfig | null>(null);
  modal$ = this.modalSubject.asObservable();

  show(config: ModalConfig) {
    this.modalSubject.next(config);
  }

  hide() {
    this.modalSubject.next(null);
  }

  // Métodos de conveniencia
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
