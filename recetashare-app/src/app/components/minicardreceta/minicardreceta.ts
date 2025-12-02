import { Component, computed, Input } from '@angular/core';
import { Receta } from '../../models/receta.model';
import { CommonModule } from '@angular/common';
import { getImageUrl } from '../../modules/common';

@Component({
  // Componente standalone sin necesidad de ir en un módulo
  standalone: true,

  // Nombre del selector HTML que representa este componente
  selector: 'app-minicardreceta',

  // Módulos que necesita para funcionar (ngIf, ngFor, etc.)
  imports: [CommonModule],

  // Plantilla HTML del componente
  templateUrl: './minicardreceta.html',

  // Hoja de estilos asociada
  styleUrl: './minicardreceta.css',
})
export class Minicardreceta {

  // Receta que se va a mostrar en la minitarjeta
  // El "!" indica a TypeScript que estamos seguros de que vendrá definida
  @Input() receta!: Receta;

  // Orden o posición de esta tarjeta (útil para animaciones)
  @Input() orden: number = 0;

  // Se asigna la función importada para usarla directamente en la plantilla
  getImageUrl = getImageUrl;


  // ===================================================
  //   Cálculo de la puntuación media (0–5)
  // ===================================================
  puntuacion() {
    // Total de votos (positivos + negativos)
    let totalVotos = this.receta.votos_positivos + this.receta.votos_negativos;

    // Evitamos división por cero si la receta no tiene votos
    totalVotos = totalVotos > 0 ? totalVotos : 1;

    // Convertimos la proporción de votos positivos a una escala de 0 a 5
    return ((this.receta.votos_positivos / totalVotos) * 5).toFixed(1);
  }


  // ===================================================
  //   Generación de estrellas según la puntuación
  //   Ejemplo: ⭐⭐⭐⭐☆ o ⭐⭐⭐⭐️☆
  // ===================================================
  estrellas() {
    // Convertimos la puntuación a número
    const puntuacionNum = parseFloat(this.puntuacion());

    // Número de estrellas completas (enteros)
    const estrellasCompletas = Math.floor(puntuacionNum);

    // Determinamos si hay media estrella
    const mitadEstrella = puntuacionNum - estrellasCompletas >= 0.5 ? 1 : 0;

    // El resto hasta 5 se completa con estrellas vacías
    const estrellasVacias = 5 - estrellasCompletas - mitadEstrella;

    // Devolvemos el string final como "⭐⭐⭐️⭐☆"
    return (
      '⭐'.repeat(estrellasCompletas) +
      (mitadEstrella ? '⭐️' : '') +
      '☆'.repeat(estrellasVacias)
    );
  }
}
