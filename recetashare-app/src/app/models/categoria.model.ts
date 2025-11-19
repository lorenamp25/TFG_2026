

// Se define una clase llamada Categoria
export class Categoria {

    // Propiedad numérica que representa el ID de la categoría
    id: number

    // Propiedad que almacena el nombre de la categoría
    nombre: string

    // Propiedad que guarda una descripción de la categoría
    descripcion: string

    // Propiedad que contiene el icono asociado a la categoría (emoji o string)
    icono: string

    // Constructor que recibe los valores iniciales de la clase
    constructor(id: number, nombre: string, descripcion: string, icono: string) {
        // Asigna el valor recibido a la propiedad id
        this.id = id
        
        // Asigna el valor recibido a la propiedad nombre
        this.nombre = nombre

        // Asigna el valor recibido a la propiedad descripcion
        this.descripcion = descripcion

        // Asigna el valor recibido a la propiedad icono
        this.icono = icono
    }
}
