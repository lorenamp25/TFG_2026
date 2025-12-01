import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-registrar',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './registrar.html',
  styleUrl: './registrar.css'
})
export class Registrar {
  irinciosesion() {
    this.router.navigate(['/LoginPage']);
  }
  registerForm = new FormGroup({
    nickname: new FormControl("", [Validators.required]),
    nombre: new FormControl(""),
    apellido: new FormControl(""),
    email: new FormControl("", [Validators.required, Validators.email]),
    password: new FormControl("", [Validators.required, Validators.minLength(6)]),
    fecha_nacimiento: new FormControl("")
  });

  constructor(private router: Router, private authService: AuthService) { }

  onRegister() {
    if (this.registerForm.invalid) {
      alert("Rellena al menos nickname, email y contraseña");
      return;
    }

    const data = this.registerForm.value;

    this.authService.register({
      nickname: data.nickname,
      nombre: data.nombre,
      apellido: data.apellido,
      email: data.email,
      password: data.password,
      fecha_nacimiento: data.fecha_nacimiento
    }).subscribe({
      next: (response: any) => {
        alert("Usuario registrado correctamente");
        // Si quieres, guardas el usuario en localStorage y logueas directamente:
        // localStorage.setItem("usuario", JSON.stringify(response.usuario));
        this.router.navigate(['/login']); // o donde quieras
      },
      error: (error) => {
        console.error("Error al registrar:", error);
        alert(error.error?.error || "Error al registrar el usuario");
      }
    });
  }
}
