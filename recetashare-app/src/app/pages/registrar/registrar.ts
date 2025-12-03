// CommonModule: permite usar directivas como *ngIf, *ngFor, etc.
import { CommonModule } from '@angular/common';

// Component: decorador para declarar un componente Angular
import { Component } from '@angular/core';

// Formularios reactivos: FormGroup, FormControl y validadores
import {
  FormControl,
  FormGroup,
  ReactiveFormsModule,
  Validators
} from '@angular/forms';

// Servicio de autenticación para registrar usuarios
import { AuthService } from '../../services/auth.service';

// Router para navegar entre pantallas
import { Router } from '@angular/router';
import { ThisReceiver } from '@angular/compiler';

@Component({
  selector: 'app-registrar',                 // Nombre del componente en HTML
  standalone: true,                          // Componente independiente
  imports: [CommonModule, ReactiveFormsModule], // Módulos que necesita
  templateUrl: './registrar.html',           // Vista HTML asociada
  styleUrl: './registrar.css'                // Estilos del componente
})
export class Registrar {
  errorMessage: string | null = null;
  infoMessage: string | null = null;
  hoy = new Date().toISOString().split('T')[0];

  // Método para ir a la página de inicio de sesión
  iriniciosesion() {
    this.router.navigate(['/login']);
  }

  // ============================================================
  // FORMULARIO REACTIVO DE REGISTRO
  // ============================================================
  registerForm = new FormGroup({
    // Nickname (obligatorio)
    nickname: new FormControl("", [Validators.required]),

    // Nombre (opcional)
    nombre: new FormControl("", [Validators.required]),

    // Apellido (opcional)
    apellido: new FormControl("", [Validators.required]),

    // Email obligatorio y válido
    email: new FormControl("", [
      Validators.required,
      Validators.email
    ]),

    // Contraseña obligatoria y mínimo 6 caracteres
    password: new FormControl("", [
      Validators.required,
      Validators.minLength(6),
      // Al menos una letra y un número y un carácter especial (!@#$%^&*.-)
      Validators.pattern(/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*.-])[A-Za-z\d!@#$%^&*.-]{6,}$/)
    ]),

    // Fecha de nacimiento (opcional)
    fecha_nacimiento: new FormControl("")
  });

  constructor(
    private router: Router,        // Navegación
    private authService: AuthService // Servicio para registrar
  ) { }

  // ============================================================
  // MÉTODO onRegister()
  // Se ejecuta cuando el usuario pulsa "Registrarse"
  // ============================================================
  onRegister() {

    // Si el formulario NO es válido, avisamos y detenemos el proceso
    if (this.registerForm.invalid) {
      this.errorMessage = "Revisa los datos del formulario.";
      setTimeout(() => (this.errorMessage = null), 4000);
      return;
    }

    // Recuperamos los datos del formulario
    const data = this.registerForm.value;

    // Llamamos al servicio para registrar al usuario
    this.authService.register({
      nickname: data.nickname,
      nombre: data.nombre,
      apellido: data.apellido,
      email: data.email,
      password: data.password,
      fecha_nacimiento: data.fecha_nacimiento
    }).subscribe({
      // Si el registro es exitoso
      next: (response: any) => {
        this.infoMessage = "Usuario registrado correctamente, redirigiendo al login...";
        setTimeout(() => {
          this.infoMessage = null
          // Redirige a la página de login
          this.router.navigate(['/login']);
        }, 4000);

        // Si quisieras loguear automáticamente:
        // localStorage.setItem("usuario", JSON.stringify(response.usuario));
      },

      // Si hubo un error desde la API
      error: (error) => {
        this.errorMessage = error.error?.error || "Error al registrar el usuario";
        setTimeout(() => (this.errorMessage = null), 4000);
      }
    });
  }
}
