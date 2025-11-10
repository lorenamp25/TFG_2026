export class Usuario {
    id: number
    nickname: string
    nombre: string
    apellido: string
    email: string
    password: string
    fecha_nacimiento: Date
    puntuacion: number

    constructor(
      id: number,
      nombre: string,
      nickname: string,
      apellido: string,
      email: string,
      password: string,
      fecha_nacimiento: Date,
      puntuacion: number
    ) {
      this.id = id
      this.nombre = nombre
      this.nickname = nickname
      this.apellido = apellido
      this.email = email
      this.password = password
      this.fecha_nacimiento = fecha_nacimiento
      this.puntuacion = puntuacion
    }
}
