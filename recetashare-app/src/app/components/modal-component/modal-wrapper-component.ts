import { Component, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { ModalService, ModalConfig } from './modal.service';
import { ModalComponent } from './modal.component';

@Component({
  selector: 'app-modal-wrapper',
  standalone: true,
  imports: [ModalComponent],
  template: `
    <app-modal
      [title]="modalConfig?.title || ''"
      [message]="modalConfig?.message || ''"
      [type]="modalConfig?.type || 'success'"
      [visible]="!!modalConfig"
      (closed)="onModalClosed()"
    ></app-modal>
  `
})
export class ModalWrapperComponent implements OnDestroy {
  // Aquí guardamos la configuración del modal enviada por el servicio
  modalConfig: ModalConfig | null = null;
  // Suscripción al observable del servicio
  private subscription: Subscription;

  constructor(private modalService: ModalService) {

    // Nos suscribimos para escuchar cuando el servicio quiere mostrar un modal.
    // Cada vez que modal$ emite, actualizamos la config.
    this.subscription = this.modalService.modal$.subscribe(config => {
      this.modalConfig = config;
    });
  }

  // Cuando el usuario cierra el modal,
  // simplemente llamamos al servicio para ocultarlo.
  onModalClosed() {
    this.modalService.hide();
  }

  // Importante: limpiar la suscripción para evitar memory leaks
  ngOnDestroy() {
    this.subscription.unsubscribe();
  }
}