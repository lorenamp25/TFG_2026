import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: '',
    loadComponent: () => import('./pages/index/index').then((m) => m.Index),
  },
  {
    path: 'categoria',
    loadComponent: () =>
      import('./pages/categoria/categoria').then((m) => m.CategoriaPage),
  },
  {
    path: 'receta/:categoria',
    loadComponent: () =>
      import('./pages/receta/receta').then((m) => m.RecetaPage),
  },
  {
    path: 'receta',
    loadComponent: () =>
      import('./pages/receta/receta').then((m) => m.RecetaPage),
  },

  {
    path: 'login',
    loadComponent: () =>
      import('./pages/login/login').then((m) => m.LoginPage),
  },
  {
    path: 'admin-categoria',
    loadComponent: () =>
      import('./pages/admin/categoria-admin/categoria-admin').then((m) => m.CategoriaAdmin),
  },
  {
    path: 'admin-ingrediente',
    loadComponent: () =>
      import('./pages/admin/ingrediente-admin/ingrediente-admin').then((m) => m.IngredienteAdmin),
  },
];
