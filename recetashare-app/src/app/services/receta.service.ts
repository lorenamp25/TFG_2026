import { HttpClient } from '@angular/common/http';
// Importa HttpClient para poder hacer peticiones HTTP al backend

import { Injectable } from '@angular/core';
// Marca esta clase como un servicio inyectable dentro de Angular

import { Observable } from 'rxjs';
// Permite manejar respuestas asíncronas tipo Observable

import { Receta } from '../models/receta.model';
// Modelo de Receta para tipar los métodos correctamente

import { environment } from '../environments/environment';
// Importa las variables de entorno (como la URL del backend)

@Injectable({
  providedIn: 'root',
  // Hace que el servicio esté disponible en toda la aplicación automáticamente
})
export class RecetaService {
  private base = environment.apiUrl;
  // Guarda la URL base del backend para no repetirla en cada llamada

  constructor(private http: HttpClient) {}
  // Inyecta HttpClient mediante el constructor

  leerReceta(id: number): Observable<any> {
    // Obtiene una receta por su ID (GET /recetas/{id})
    return this.http.get(`${this.base}/recetas/${id}`);
  }

  listarRecetas() {
    // Trae todas las recetas desde el backend (GET /recetas)
    return this.http.get(`${this.base}/recetas/`);
  }

  crearReceta(Receta: Receta): Observable<any> {
    // Envía una receta nueva al backend para guardarla (POST /recetas)
    return this.http.post(`${this.base}/recetas/`, this.createFormData(Receta));
  }

  eliminarReceta(id: number): Observable<any> {
    // Elimina una receta por su ID (DELETE /recetas/{id})
    return this.http.delete(`${this.base}/recetas/${id}`);
  }

  actualizarReceta(id: number, Receta: Receta): Observable<any> {
    // Actualiza una receta existente (PUT /recetas/{id})
    return this.http.put(`${this.base}/recetas/${id}`, Receta);
  }

  private createFormData(receta: Receta): FormData {
    const formData = new FormData();
    formData.append('titulo', receta.titulo);
    formData.append('descripcion', receta.descripcion);
    formData.append('ingredientes', JSON.stringify(receta.ingredientes));
    formData.append('instrucciones', JSON.stringify(receta.instrucciones));
    formData.append('tiempo_preparacion', receta.tiempo_preparacion?.toString() || '0');
    formData.append('dificultad', receta.dificultad || '');
    formData.append('categoria', receta.categoria?.toString() || '0');
    formData.append('usuario', JSON.stringify(receta.usuario));

    if (receta.imagen_file) {
      formData.append('imagen_principal', receta.imagen_file);
    }

    // receta.instrucciones.forEach((inst, index) => {
    //   if (inst.imagen_file) {
    //     formData.append(`instruccion_imagen_${index}`, inst.imagen_file);
    //   }
    // });
    console.log('FormData creada para la receta:', formData);

    return formData;
  }
}
