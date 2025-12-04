import { Component, Input, Output, EventEmitter } from '@angular/core';

export type ModalType = 'success' | 'error' | 'warning';

@Component({
  selector: 'app-modal',
  standalone: true,
  templateUrl: './modal.component.html',
  styleUrls: ['./modal.component.css']
})
export class ModalComponent {
  @Input() title: string = '';
  @Input() message: string = '';
  @Input() type: ModalType = 'success';
  @Input() visible: boolean = false;
  @Output() closed = new EventEmitter<void>();

  get modalClass(): string {
    return `modal-${this.type}`;
  }

  get icon(): string {
    switch (this.type) {
      case 'success': return '✓';
      case 'error': return '✕';
      case 'warning': return '⚠';
      default: return '✓';
    }
  }

  close() {
    this.visible = false;
    this.closed.emit();
  }

  // Cerrar modal haciendo click fuera del contenido
  onBackdropClick(event: MouseEvent) {
    if ((event.target as HTMLElement).classList.contains('modal-backdrop')) {
      this.close();
    }
  }
}
