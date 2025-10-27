import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { Categoria } from "../models/categoria.model";
import { environment } from '../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class CategoriaService {
    private base = environment.apiUrl;

    constructor(private http: HttpClient) { }

    listarCategorias() {
        return this.http.get(`${this.base}/categorias/`);
    }

    crearCategoria(categoria: Categoria): Observable<any> {
        return this.http.post(`${this.base}/categorias/`, categoria);
    }

    eliminarCategoria(id: number): Observable<any> {
        return this.http.delete(`${this.base}/categorias/${id}`);
    }

    actualizarCategoria(id: number, categoria: Categoria): Observable<any> {
        return this.http.put(`${this.base}/categorias/${id}`, categoria);
    }
}
