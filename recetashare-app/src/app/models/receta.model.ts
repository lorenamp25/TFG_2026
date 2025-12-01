// Importa el modelo Categoria (aunque no se usa directamente aquí)
import { Categoria } from "./categoria.model"

// Importa el modelo Ingrediente
import { Ingrediente } from "./ingrediente.model"
import { Usuario } from "./usuario.model"

// Clase que representa una instrucción dentro de una receta
export class Instruccion {

  // Número de paso dentro de la preparación
  orden: number

  // Texto que describe lo que hay que hacer en ese paso
  descripcion: string

  // Constructor que inicializa los valores de la instrucción
  constructor(orden: number, descripcion: string) {
    this.orden = orden          // Asigna el número de paso
    this.descripcion = descripcion  // Asigna el texto descriptivo
  }
}

export class RecetaIngrediente {
  cantidad: number
  unidad: string
  ingrediente: Ingrediente

  constructor(cantidad: number, unidad: string, ingrediente: Ingrediente) {
    this.cantidad = cantidad
    this.unidad = unidad
    this.ingrediente = ingrediente
  }
}


// Clase principal que representa una receta completa
export class Receta {

  // Identificador único de la receta
  id: number

  // Título de la receta
  titulo: string

  // Descripción general de la receta
  descripcion: string

  // Lista de ingredientes usados en la receta
  ingredientes: RecetaIngrediente[]

  // Lista de instrucciones paso a paso
  instrucciones: Instruccion[]

  // Tiempo estimado de preparación (string o vacío)
  tiempo_preparacion: number | undefined

  // Nivel de dificultad (fácil, media, alta, etc.)
  dificultad: string | undefined

  // ID numérico de la categoría asociada
  categoria: number | undefined

  // URL de la imagen de la receta
  imagen_url: string | undefined

  imagen_preview: string | undefined

  imagen_file?: File

  // ID del usuario autor de la receta
  usuario: Usuario | undefined

  // Indicador de si la receta es destacada o especial
  destacada: boolean | undefined

  // Número de votos positivos
  votos_positivos: number | 0

  // Número de votos negativos
  votos_negativos: number | 0

  // Constructor que recibe todos los campos necesarios para crear una receta
  constructor(
    id: number = 0,
    titulo: string = '',
    descripcion: string = '',
    ingredientes: RecetaIngrediente[] = [],
    instrucciones: Instruccion[] = [],
    tiempo_preparacion: number = 0,
    dificultad: string = '',
    categoria: number = 0,
    imagen_url: string = '',
    usuario: Usuario = new Usuario(),
    destacada: boolean = false,
    votos_positivos: number = 0,
    votos_negativos: number = 0
  ) {
    this.id = id                                   // Asigna el ID
    this.titulo = titulo                           // Asigna el título
    this.descripcion = descripcion                 // Asigna la descripción
    this.ingredientes = ingredientes               // Asigna el array de ingredientes
    this.instrucciones = instrucciones             // Asigna el array de instrucciones
    this.tiempo_preparacion = tiempo_preparacion   // Asigna el tiempo
    this.dificultad = dificultad                   // Asigna la dificultad
    this.categoria = categoria                     // Asigna la categoría
    this.imagen_url = imagen_url                   // Asigna la imagen
    this.usuario = usuario                         // Asigna el autor
    this.destacada = destacada                     // Marca si es destacada
    this.votos_positivos = votos_positivos         // Asigna votos positivos
    this.votos_negativos = votos_negativos         // Asigna votos negativos
  }

}
