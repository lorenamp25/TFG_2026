// Se declara la clase Ingrediente
export class Ingrediente {

    // Propiedad que almacena el identificador único del ingrediente
    id: number;

    // Propiedad que guarda el nombre del ingrediente
    nombre: string;

    // Constructor que recibe los valores iniciales del ingrediente
    constructor(id: number, nombre: string) {

        // Asigna el valor proporcionado al atributo id
        this.id = id;

        // Asigna el valor proporcionado al atributo nombre
        this.nombre = nombre;
    }
}
