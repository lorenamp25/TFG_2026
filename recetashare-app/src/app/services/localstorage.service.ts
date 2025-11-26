import { Injectable } from "@angular/core";

@Injectable({
    providedIn: 'root'
})
export class StorageService {
  constructor() { }

  isLoggedIn() {
    return localStorage.getItem("usuario") !== null;
  }

  isAdmin() {
    const usuarioStr = localStorage.getItem("usuario");
    if (usuarioStr) {
      const usuario = JSON.parse(usuarioStr);
      return usuario.es_admin === true;
    }
    return false;
  }

  getUserId() {
    const usuarioStr = localStorage.getItem("usuario");
    if (usuarioStr) {
      const usuario = JSON.parse(usuarioStr);
      return usuario.id;
    }
    return null;
  }

  getUsuario() {
    const usuarioStr = localStorage.getItem("usuario");
    if (usuarioStr) {
      const usuario = JSON.parse(usuarioStr);
      return usuario;
    }
    return null;
  }

  nombreUsuario() {
    const usuarioStr = localStorage.getItem("usuario");
    if (usuarioStr) {
      const usuario = JSON.parse(usuarioStr);
      return usuario.nombre || "Usuario";
    }
    return "Usuario";
  }

  logout() {
    localStorage.removeItem("usuario");
    // Elimina la información del usuario del almacenamiento local
    window.location.href = "/";
    // Recarga la página para reflejar el cambio de estado
  }

}
