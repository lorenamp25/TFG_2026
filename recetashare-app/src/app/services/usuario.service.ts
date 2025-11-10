import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { Categoria } from "../models/categoria.model";
import { environment } from '../environments/environment';
import { Usuario } from "../models/usuario.model";

@Injectable({
    providedIn: 'root'
})
export class UsuarioService {
    private base = environment.apiUrl;

    constructor(private http: HttpClient) { }

    leerUsuario(id: number): Observable<any> {
        return this.http.get(`${this.base}/usuarios/${id}`);
    }

    leerUsuarioPorEmail(email: string): Observable<any> {
        return this.http.get(`${this.base}/usuarios/${email}`);
    }

    listarUsuario() {
        return this.http.get(`${this.base}/usuarios/`);
    }

    crearUsuario(usuario: Usuario): Observable<any> {
        return this.http.post(`${this.base}/usuarios/`, usuario);
    }

    eliminarUsuario(id: number): Observable<any> {
        return this.http.delete(`${this.base}/usuarios/${id}`);
    }

    actualizarUsuario(id: number, usuario: Usuario): Observable<any> {
        return this.http.put(`${this.base}/usuarios/${id}`, usuario);
    }
}
