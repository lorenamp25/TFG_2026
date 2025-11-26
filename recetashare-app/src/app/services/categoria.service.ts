import { HttpClient } from '@angular/common/http';
// Importa el servicio HttpClient para hacer peticiones HTTP al backend

import { Injectable } from '@angular/core';
// Marca la clase como un servicio inyectable en Angular

import { Observable } from 'rxjs';
// Permite trabajar con respuestas asíncronas tipo Observable

import { Categoria } from '../models/categoria.model';
// Modelo de datos de Categoria para tipar correctamente

import { environment } from '../environments/environment';
// Importa la configuración del entorno para obtener la URL base del backend

@Injectable({
  providedIn: 'root',
  // Hace que este servicio esté disponible en toda la app sin necesitar declararlo en providers
})
export class CategoriaService {
  private base = environment.apiUrl;
  // Guarda la URL base del backend, por ejemplo: http://localhost:8000

  constructor(private http: HttpClient) {}
  // Inyecta HttpClient para poder hacer solicitudes HTTP

  leerCategoria(id: number): Observable<any> {
    // Obtiene una categoría específica por ID
    return this.http.get(`${this.base}/categorias/${id}`);
  }

  listarCategorias() {
    // Trae todas las categorías del backend
    return this.http.get(`${this.base}/categorias/`);
  }

  crearCategoria(categoria: Categoria): Observable<any> {
    // Envía una categoría nueva al backend para guardarla
    return this.http.post(`${this.base}/categorias/`, categoria);
  }

  eliminarCategoria(id: number): Observable<any> {
    // Elimina una categoría por su ID
    return this.http.delete(`${this.base}/categorias/${id}`);
  }

  actualizarCategoria(id: number, categoria: Categoria): Observable<any> {
    // Actualiza los datos de una categoría existente
    return this.http.put(`${this.base}/categorias/${id}`, categoria);
  }
}
