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
export class AuthService {
    private base = environment.apiUrl;


constructor(private http: HttpClient) { }
    // Inyecta HttpClient para poder enviar solicitudes HTTP al backend

    login(email: string, password: string): Observable<any> {
        return this.http.post(`${this.base}/auth/login`, { email, password });
    }

}
