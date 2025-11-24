// Clase que representa un usuario dentro de la aplicación
export class Usuario {

    // Identificador único del usuario
    id: number

    // Apodo o nombre público del usuario
    nickname: string

    // Nombre real del usuario
    nombre: string

    // Apellido del usuario
    apellido: string

    // Correo electrónico del usuario
    email: string

    // Contraseña del usuario (habitualmente debe guardarse hasheada)
    password: string

    // Fecha de nacimiento del usuario
    fecha_nacimiento: string

    // Puntuación o reputación del usuario dentro de la app
    puntuacion: number

    // Indica si el usuario tiene privilegios de administrador
    es_admin: boolean = false;

    // Constructor que inicializa todos los campos al crear un usuario
    constructor(
      id: number = 0,               // ID único
      nombre: string = '',           // Nombre del usuario
      nickname: string = '',         // Apodo
      apellido: string = '',         // Apellido
      email: string = '',            // Email
      password: string = '',         // Contraseña
      fecha_nacimiento: string = '',   // Fecha de nacimiento
      puntuacion: number = 0,        // Puntuación/reputación
      es_admin: boolean = false   // Indica si es admin (por defecto false)
    ) {
      this.id = id                                 // Asigna el ID
      this.nombre = nombre                         // Asigna el nombre
      this.nickname = nickname                     // Asigna el apodo
      this.apellido = apellido                     // Asigna el apellido
      this.email = email                           // Asigna el correo
      this.password = password                     // Asigna la contraseña
      this.fecha_nacimiento = fecha_nacimiento     // Asigna la fecha de nacimiento
      this.puntuacion = puntuacion                 // Asigna la puntuación
      this.es_admin = es_admin                     // Asigna si es admin
    }
}
