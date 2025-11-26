import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { Categoria } from "../models/categoria.model";
import { environment } from '../environments/environment';
import { Usuario } from "../models/usuario.model";

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private base = environment.apiUrl;

  constructor(private http: HttpClient) { }

  // === LOGIN ===
  login(email: string, password: string): Observable<any> {
    return this.http.post(`${this.base}/login`, { email, password });
  }

  // === REGISTRO ===
  register(data: any): Observable<any> {
    return this.http.post(`${this.base}/registrar`, data);
  }
}
