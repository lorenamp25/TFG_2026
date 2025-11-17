import { Categoria } from "./categoria.model"
import { Ingrediente } from "./ingrediente.model"

export class Instruccion {
    paso: number
    descripcion: string
    imagen_url?: string

    constructor(paso: number, descripcion: string, imagen_url?: string) {
        this.paso = paso
        this.descripcion = descripcion
        this.imagen_url = imagen_url
    }
}

export class Receta {
    id: number
    titulo: string
    descripcion: string
    ingredientes: Ingrediente[] | []
    instrucciones: Instruccion[] | []
    tiempo_preparacion: string | undefined
    dificultad: string | undefined
    categoria: number | undefined
    imagen_url: string | undefined
    usuario_id: number | undefined
    destacada: boolean | undefined
    votos_positivos: number | 0
    votos_negativos: number | 0

    constructor(
        id: number,
        titulo: string,
        descripcion: string,
        ingredientes: Ingrediente[],
        instrucciones: Instruccion[],
        tiempo_preparacion: string,
        dificultad: string,
        categoria: number,
        imagen_url: string,
        usuario_id: number,
        destacada: boolean,
        votos_positivos: number = 0,
        votos_negativos: number = 0
    ) {
        this.id = id
        this.titulo = titulo
        this.descripcion = descripcion
        this.ingredientes = ingredientes
        this.instrucciones = instrucciones
        this.tiempo_preparacion = tiempo_preparacion
        this.dificultad = dificultad
        this.categoria = categoria
        this.imagen_url = imagen_url
        this.usuario_id = usuario_id
        this.destacada = destacada,
        this.votos_positivos = votos_positivos
        this.votos_negativos = votos_negativos
    }

}
