

export class Categoria {
    id: number
    nombre: string
    descripcion: string
    icono: string

    constructor(id: number, nombre: string, descripcion: string, icono: string) {
        this.id = id
        this.nombre = nombre
        this.descripcion = descripcion
        this.icono = icono
    }
}
