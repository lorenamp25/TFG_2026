// Injectable: permite que este servicio pueda ser inyectado en otros componentes
import { Injectable } from "@angular/core";

@Injectable({
  providedIn: 'root'   // Disponible en toda la aplicación
})
export class StorageService {

  constructor() { }

  // ============================================================
  // ¿ESTÁ LOGUEADO EL USUARIO?
  // Comprueba si existe un usuario guardado en localStorage
  // ============================================================
  isLoggedIn() {
    return localStorage.getItem("usuario") !== null;
  }

  // ============================================================
  // ¿EL USUARIO ES ADMIN?
  // Lee el usuario desde localStorage y revisa el campo es_admin
  // ============================================================
  isAdmin() {
    const usuarioStr = localStorage.getItem("usuario");

    if (usuarioStr) {
      const usuario = JSON.parse(usuarioStr);
      return usuario.es_admin === true;   // Devuelve true si es admin
    }

    return false; // No hay usuario → no es admin
  }

  // ============================================================
  // OBTENER ID DEL USUARIO LOGUEADO
  // Devuelve el ID almacenado en el usuario de localStorage
  // ============================================================
  getUserId() {
    const usuarioStr = localStorage.getItem("usuario");

    if (usuarioStr) {
      const usuario = JSON.parse(usuarioStr);
      return usuario.id;
    }

    return null;
  }

  // ============================================================
  // OBTENER OBJETO USUARIO COMPLETO
  // Devuelve todos los datos del usuario guardado
  // ============================================================
  getUsuario() {
    const usuarioStr = localStorage.getItem("usuario");

    if (usuarioStr) {
      return JSON.parse(usuarioStr);
    }

    return null;
  }

  // ============================================================
  // OBTENER NOMBRE DEL USUARIO
  // Si no lo encuentra, devuelve "Usuario"
  // ============================================================
  nombreUsuario() {
    const usuarioStr = localStorage.getItem("usuario");

    if (usuarioStr) {
      const usuario = JSON.parse(usuarioStr);
      return usuario.nombre || "Usuario";
    }

    return "Usuario";
  }

  // ============================================================
  // LOGOUT
  // Borra los datos del usuario y recarga la app
  // ============================================================
  logout() {
    localStorage.removeItem("usuario");  // Elimina usuario del storage
    window.location.href = "/";          // Recarga la página y vuelve al inicio
  }

}
