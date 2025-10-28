import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: 'categoria',
    loadComponent: () =>
      import('./pages/categoria/categoria').then((m) => m.CategoriaPage),
  },
];
