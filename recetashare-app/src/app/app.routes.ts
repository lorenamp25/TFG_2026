import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: 'categoria',
    loadComponent: () =>
      import('./components/categoria-index/categoria-index').then((m) => m.CategoriaComponent),
  },
];
