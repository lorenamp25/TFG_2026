import { CommonModule } from '@angular/common';               // Módulo básico con directivas comunes (ngIf, ngFor…)
import { Component } from '@angular/core';                    // Decorador para definir un componente
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms'; 
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
  form = new FormGroup({
    id: new FormControl(0)                                    // Control simple llamado "id", inicializado a 0
  })

  // Método que se ejecuta al enviar el formulario (ngSubmit)
  onLogin() {
    // Aquí irá la lógica para manejar el login
  }

}
