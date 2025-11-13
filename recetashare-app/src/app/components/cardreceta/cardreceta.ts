import { Component, Input } from '@angular/core';
import { Receta } from '../../models/receta.model';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-cardreceta',
  imports: [CommonModule],
  templateUrl: './cardreceta.html',
  styleUrl: './cardreceta.css',
})
export class Cardreceta {
  @Input() receta!: Receta

  constructor() {}
}
