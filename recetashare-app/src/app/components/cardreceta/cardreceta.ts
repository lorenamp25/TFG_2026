import { Component, Input } from '@angular/core';
import { Receta } from '../../models/receta.model';
import { CommonModule } from '@angular/common';
import { getImageUrl } from '../../modules/common';

@Component({
  standalone: true,
  selector: 'app-cardreceta',
  imports: [CommonModule],
  templateUrl: './cardreceta.html',
  styleUrl: './cardreceta.css',
})
export class Cardreceta {
  @Input() receta!: Receta
  getImageUrl = getImageUrl

  constructor() {}
}
