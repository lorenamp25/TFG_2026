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
  modalConfig: ModalConfig | null = null;
  private subscription: Subscription;

  constructor(private modalService: ModalService) {
    this.subscription = this.modalService.modal$.subscribe(config => {
      this.modalConfig = config;
    });
  }

  onModalClosed() {
    this.modalService.hide();
  }

  ngOnDestroy() {
    this.subscription.unsubscribe();
  }
}
