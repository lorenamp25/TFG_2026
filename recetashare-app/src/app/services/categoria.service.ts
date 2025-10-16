import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { Categoria } from "../models/categoria.model";

@Injectable({
    providedIn: 'root'
})
export class CategoriaService {
    constructor(private http: HttpClient) { }

    listarCategorias() {
        return this.http.get('http://localhost/api/categorias/')
    }

    crearCategoria(categoria: Categoria): Observable<any> {
        return this.http.post('http://localhost/api/categorias/', categoria)
    }

    eliminarCategoria(id: number): Observable<any> {
        return this.http.delete(`http://localhost/api/categorias/${id}`)
    }

    actualizarCategoria(id: number, categoria: Categoria): Observable<any> {
        return this.http.put(`http://localhost/api/categorias/${id}`, categoria)
    }
}
