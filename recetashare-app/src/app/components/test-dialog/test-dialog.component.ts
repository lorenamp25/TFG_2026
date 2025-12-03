import { Component, Input, Renderer2 } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-dialog-info',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './test-dialog.component.html',
  styleUrls: ['./test-dialog.component.css']
})
export class DialogInfoComponent {
  @Input() message: string  | null = '';
  visible = false

  constructor(private renderer: Renderer2) {}

  open() {
    this.visible = true;
    this.renderer.addClass(document.body, 'no-scroll');
  }

  close() {
    this.visible = false;
    this.renderer.removeClass(document.body, 'no-scroll');
  }

  toggle() {
    this.visible ? this.close() : this.open();
  }
}
