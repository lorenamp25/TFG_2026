import { HttpClient } from "@angular/common/http";
// Importa HttpClient para poder realizar peticiones HTTP al backend

import { Injectable } from "@angular/core";
// Permite que este servicio pueda inyectarse en otros componentes o servicios

import { Observable } from "rxjs";
// Define tipos Observable para manejar respuestas asíncronas

import { Categoria } from "../models/categoria.model";
// (No se usa aquí, pero se importa el modelo de Categoria)

import { environment } from '../environments/environment';
// Importa las variables de entorno (como la URL base del backend)

import { Usuario } from "../models/usuario.model";
// Importa el modelo Usuario para tipar correctamente los métodos


@Injectable({
    providedIn: 'root'
    // Hace que el servicio esté disponible globalmente sin necesidad de declararlo en módulos
})
export class UsuarioService {
    private base = environment.apiUrl;
    // Guarda la URL base del backend para reutilizarla en todas las peticiones

    constructor(private http: HttpClient) { }
    // Inyecta HttpClient para poder enviar solicitudes HTTP al backend

    leerUsuario(id: number): Observable<any> {
        // Obtiene un usuario por su ID → GET /usuarios/{id}
        return this.http.get(`${this.base}/usuarios/${id}`);
    }

    leerUsuarioPorEmail(email: string): Observable<any> {
        // Obtiene un usuario usando su email → GET /usuarios/{email}
        return this.http.get(`${this.base}/usuarios/${email}`);
    }

    listarUsuario() {
        // Lista todos los usuarios → GET /usuarios
        return this.http.get(`${this.base}/usuarios/`);
    }

    crearUsuario(usuario: Usuario): Observable<any> {
        // Crea un usuario nuevo → POST /usuarios
        return this.http.post(`${this.base}/usuarios/`, usuario);
    }

    eliminarUsuario(id: number): Observable<any> {
        // Elimina un usuario por ID → DELETE /usuarios/{id}
        return this.http.delete(`${this.base}/usuarios/${id}`);
    }

    actualizarUsuario(id: number, usuario: Usuario): Observable<any> {
        // Actualiza un usuario existente → PUT /usuarios/{id}
        return this.http.put(`${this.base}/usuarios/${id}`, usuario);
    }
}
