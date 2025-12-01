import { CommonModule } from '@angular/common';               // Módulo básico con directivas comunes (ngIf, ngFor…)
import { Component } from '@angular/core';                    // Decorador para definir un componente
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
// ReactiveFormsModule: permite usar formularios reactivos (FormGroup, FormControl)

@Component({
  standalone: true,                                           // Componente independiente (no requiere módulo)
  selector: 'app-login',                                      // Nombre del selector HTML
  imports: [CommonModule, ReactiveFormsModule],               // Módulos necesarios que este componente puede usar
  templateUrl: './login.html',                                // Archivo HTML asociado al componente
  styleUrl: './login.css'                                     // Archivo CSS asociado al componente
})
export class LoginPage {


  // === Formulario reactivo ===
  loginForm = new FormGroup({
    email: new FormControl("", [Validators.required, Validators.email]),
    password: new FormControl("", [Validators.required, Validators.minLength(6)])
  })

  constructor(private router: Router, private authService: AuthService) { }

  // Método que se ejecuta al enviar el formulario (ngSubmit)
  onLogin() {
    if (this.loginForm.valid) {
      const email = this.loginForm.value.email;
      const password = this.loginForm.value.password;

      this.authService.login(email!, password!).subscribe({
        next: (response) => {
          localStorage.setItem("usuario", JSON.stringify(response.usuario));
          this.router.navigate(['/']); // Redirige a la página principal
        },
        error: (error) => {
          console.error("Error de login:", error);
          alert("Credenciales incorrectas. Por favor, inténtalo de nuevo.");
        }
      })
    }
  }
  irARegistro() {
    this.router.navigate(['/registrar']);
  }
}
