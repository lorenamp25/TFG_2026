// HttpClient: necesario para hacer peticiones HTTP al backend
import { HttpClient } from '@angular/common/http';

// Injectable: permite usar este servicio mediante inyección de dependencias
import { Injectable } from '@angular/core';

// Observable: para manejar respuestas asíncronas de HTTP
import { Observable } from 'rxjs';

// Modelo Receta (tipado de datos)
import { Receta } from '../models/receta.model';

// Variables de entorno (url API, etc.)
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'              // Servicio accesible en toda la app
})
export class RecetaService {

  // URL base del backend, tomada del environment
  private base = environment.apiUrl;

  constructor(private http: HttpClient) { }
  // Se inyecta HttpClient para hacer las peticiones REST al backend

  // ============================================================
  //  LEER UNA RECETA POR ID
  // ============================================================
  leerReceta(id: number): Observable<any> {
    // GET /recetas/{id}
    return this.http.get(`${this.base}/recetas/${id}`);
  }

  // ============================================================
  //  LISTAR TODAS LAS RECETAS
  // ============================================================
  listarRecetas() {
    // GET /recetas
    return this.http.get(`${this.base}/recetas/`);
  }

  // ============================================================
  //  CREAR NUEVA RECETA
  //  Envía la receta al backend utilizando FormData
  //  (esto permite incluir imágenes)
  // ============================================================
  crearReceta(Receta: Receta): Observable<any> {
    // POST /recetas con FormData
    return this.http.post(`${this.base}/recetas/`, this.createFormData(Receta));
  }

  // ============================================================
  //  ELIMINAR RECETA POR ID
  // ============================================================
  eliminarReceta(id: number): Observable<any> {
    // DELETE /recetas/{id}
    return this.http.delete(`${this.base}/recetas/${id}`);
  }

  // ============================================================
  //  ACTUALIZAR RECETA EXISTENTE
  // ============================================================
  actualizarReceta(id: number, Receta: Receta): Observable<any> {
    // PUT /recetas/{id}
    // Aquí no se usa FormData porque esta llamada no sube imágenes
    return this.http.put(`${this.base}/recetas/${id}`, Receta);
  }

  // ============================================================
  //  MÉTODO INTERNO PARA CREAR FormData
  //  Sirve para poder enviar texto + imágenes al backend
  // ============================================================
  private createFormData(receta: Receta): FormData {
    const formData = new FormData();

    // Campos básicos
    formData.append('titulo', receta.titulo);
    formData.append('descripcion', receta.descripcion);

    // Se envían listas convertidas a JSON
    formData.append('ingredientes', JSON.stringify(receta.ingredientes));
    formData.append('instrucciones', JSON.stringify(receta.instrucciones));

    // Datos numéricos como texto
    formData.append('tiempo_preparacion', receta.tiempo_preparacion?.toString() || '0');

    // Campos opcionales
    formData.append('dificultad', receta.dificultad || '');
    formData.append('categoria', receta.categoria?.toString() || '0');

    // Usuario completo en JSON
    formData.append('usuario', JSON.stringify(receta.usuario));

    // Imagen (solo si el usuario seleccionó una)
    if (receta.imagen_file) {
      formData.append('imagen_principal', receta.imagen_file);
    }

    return formData;
  }
}
