// ─────────────────────────────────────────────────────────────────────────────
// breadcrumb-bar.ts — navegación jerárquica.
//
// Este componente muestra la ruta de navegación actual dentro de la aplicación
// mediante una cadena de enlaces. Facilita al usuario conocer su ubicación y
// regresar rápidamente a niveles anteriores.
//
// El icono de inicio se añade automáticamente desde la plantilla.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';

// Elemento individual de la ruta de navegación.
export interface Breadcrumb {
  label: string;
  route: string;
}

@Component({
  selector: 'app-breadcrumb-bar',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './breadcrumb-bar.html',
})
export class BreadcrumbBar {
  // Lista de elementos que forman la ruta de navegación actual.
  readonly crumbs = input<Breadcrumb[]>([]);
}