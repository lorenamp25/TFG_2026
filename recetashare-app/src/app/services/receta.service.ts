import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { Receta } from "../models/receta.model";
import { environment } from '../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class RecetaService {
    private base = environment.apiUrl;

    constructor(private http: HttpClient) { }

    leerReceta(id: number): Observable<any> {
        return this.http.get(`${this.base}/recetas/${id}`);
    }

    listarRecetas() {
        return this.http.get(`${this.base}/recetas/`);
    }

    crearReceta(Receta: Receta): Observable<any> {
        return this.http.post(`${this.base}/recetas/`, Receta);
    }

    eliminarReceta(id: number): Observable<any> {
        return this.http.delete(`${this.base}/recetas/${id}`);
    }

    actualizarReceta(id: number, Receta: Receta): Observable<any> {
        return this.http.put(`${this.base}/recetas/${id}`, Receta);
    }
}
