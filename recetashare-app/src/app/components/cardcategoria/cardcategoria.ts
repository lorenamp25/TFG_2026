import { Component, Input } from '@angular/core';
import { Categoria } from '../../models/categoria.model';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-cardcategoria',
  imports: [CommonModule],
  templateUrl: './cardcategoria.html',
  styleUrl: './cardcategoria.css',
})
export class Cardcategoria {
  @Input() categoria!: Categoria


}
