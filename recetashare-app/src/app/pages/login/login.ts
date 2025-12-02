// Importa módulos esenciales de Angular:
// - CommonModule: directivas básicas como *ngIf, *ngFor
import { CommonModule } from '@angular/common';
// - Component: decorador para declarar un componente
import { Component } from '@angular/core';
// - FormControl, FormGroup…: para construir formularios reactivos
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
// - Router: para navegar entre rutas
import { Router } from '@angular/router';
// - AuthService: servicio que gestiona la autenticación en el backend
import { AuthService } from '../../services/auth.service';

@Component({
  standalone: true,                                    // Componente independiente (sin módulo)
  selector: 'app-login',                               // Nombre del selector HTML
  imports: [CommonModule, ReactiveFormsModule],        // Módulos necesarios para el HTML
  templateUrl: './login.html',                         // Plantilla asociada
  styleUrl: './login.css'                              // Estilos asociados
})
export class LoginPage {

  // ============================================================
  //  FORMULARIO REACTIVO DE LOGIN
  // ============================================================
  // Usa FormGroup para manejar los inputs con validación
  loginForm = new FormGroup({
    email: new FormControl(
      "",
      [Validators.required, Validators.email]          // Email obligatorio y válido
    ),
    password: new FormControl(
      "",
      [Validators.required, Validators.minLength(6)]   // Contraseña mínima de 6 caracteres
    )
  });

  constructor(
    private router: Router,              // Router para navegar entre páginas
    private authService: AuthService     // Servicio para realizar el login
  ) { }

  // Mensaje de error para mostrar debajo del formulario
  // (se usa en el HTML con *ngIf errorMessage)
  errorMessage: string | null = null;

  // ============================================================
  //  MÉTODO onLogin()
  //  Se ejecuta cuando el usuario envía el formulario
  // ============================================================
  onLogin() {
    // Validación del formulario
    if (this.loginForm.valid) {

      // Recuperamos los valores del formulario
      const email = this.loginForm.value.email;
      const password = this.loginForm.value.password;

      // Llamamos al servicio de autenticación
      this.authService.login(email!, password!).subscribe({
        next: (response) => {
          // Guardamos el usuario en localStorage para mantener la sesión
          localStorage.setItem("usuario", JSON.stringify(response.usuario));

          // Redirigir al usuario a la página principal
          this.router.navigate(['/']);
        },

        error: (error) => {
          console.error("Error de login:", error);

          // Se muestra un mensaje de error bonito en pantalla
          this.errorMessage =
            "Credenciales incorrectas. Por favor, inténtalo de nuevo.";

          // El mensaje desaparece automáticamente tras 4 segundos
          setTimeout(() => (this.errorMessage = null), 4000);
        }
      });
    }
  }

  // ============================================================
  //  Redirige a la página de registro
  // ============================================================
  irARegistro() {
    this.router.navigate(['/registrar']);
  }
}
