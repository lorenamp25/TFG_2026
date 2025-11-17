import { Component, computed, Input } from '@angular/core';
import { Receta } from '../../models/receta.model';
import { CommonModule } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-minicardreceta',
  imports: [CommonModule],
  templateUrl: './minicardreceta.html',
  styleUrl: './minicardreceta.css'
})
export class Minicardreceta {
  @Input() receta!: Receta;
  @Input() orden: number = 0;

  puntuacion() {
    let totalVotos = this.receta.votos_positivos + this.receta.votos_negativos;
    totalVotos = totalVotos > 0 ? totalVotos : 1; // Evitar división por cero

    return (this.receta.votos_positivos / totalVotos * 5).toFixed(1);
  }

  estrellas() {
    const puntuacionNum = parseFloat(this.puntuacion());
    const estrellasCompletas = Math.floor(puntuacionNum);
    const mitadEstrella = puntuacionNum - estrellasCompletas >= 0.5 ? 1 : 0;
    const estrellasVacias = 5 - estrellasCompletas - mitadEstrella;
    return '⭐'.repeat(estrellasCompletas) + (mitadEstrella ? '⭐️' : '') + '☆'.repeat(estrellasVacias);
  }

}
