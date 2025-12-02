// HttpClient: permite hacer peticiones HTTP al backend
import { HttpClient } from "@angular/common/http";

// Injectable: indica que esta clase es un servicio inyectable
import { Injectable } from "@angular/core";

// Observable: tipo para manejar respuestas asíncronas de la API
import { Observable } from "rxjs";

// Importa modelos (si se quieren usar tipados en un futuro)
import { Categoria } from "../models/categoria.model";
import { Usuario } from "../models/usuario.model";

// Importa variables de entorno, incluido apiUrl
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'   // El servicio queda disponible en toda la app
})
export class AuthService {

  // URL base del backend, obtenida del environment
  private base = environment.apiUrl;

  // Inyección del cliente HTTP
  constructor(private http: HttpClient) { }

  // ============================================================
  //  MÉTODO LOGIN
  //  Envía email + password al backend para iniciar sesión
  // ============================================================
  login(email: string, password: string): Observable<any> {
    // POST hacia /login con las credenciales
    return this.http.post(`${this.base}/login`, { email, password });
  }

  // ============================================================
  //  MÉTODO REGISTER
  //  Envía datos para crear un nuevo usuario en la base de datos
  // ============================================================
  register(data: any): Observable<any> {
    // POST hacia /registrar con los datos del formulario
    return this.http.post(`${this.base}/registrar`, data);
  }
}
