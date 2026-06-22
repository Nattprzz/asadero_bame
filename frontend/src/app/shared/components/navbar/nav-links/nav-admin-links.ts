// ─────────────────────────────────────────────────────────────────────────────
// nav-admin-links.ts — enlaces de navegación para administradores.
//
// Este componente agrupa los accesos rápidos disponibles para usuarios con
// rol de administrador. Se utiliza dentro de la barra de navegación principal
// y muestra únicamente las opciones relacionadas con la gestión global de la
// plataforma.
//
// Al seleccionar cualquier enlace se notifica al componente padre para cerrar
// automáticamente el menú en dispositivos móviles.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, output } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { ExactLinkActive } from '@shared/directives/exact-link-active';

@Component({
  selector: 'app-nav-admin-links',
  standalone: true,
  imports: [
    RouterLink,
    RouterLinkActive,
    ExactLinkActive,
    TablerIconComponent,
  ],

  // Evita generar un elemento contenedor adicional en el DOM.
  host: {
    '[style.display]': '"contents"',
  },

  template: `
    <!-- Panel -->
    <li class="w-full md:w-auto">
      <a
        routerLink="/admin"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-[#241F16] text-white"
        [routerLinkActiveOptions]="{ exact: true }"
        class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-sm font-bold text-stone-400 transition hover:bg-[#241F16] hover:text-white md:w-auto"
      >
        <tabler-icon
          icon="gauge"
          [size]="16"
          class="shrink-0"
          aria-hidden="true"
        />
        Panel
      </a>
    </li>

    <!-- Locales -->
    <li class="w-full md:w-auto">
      <a
        routerLink="/admin/locales"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-[#241F16] text-white"
        class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-sm font-bold text-stone-400 transition hover:bg-[#241F16] hover:text-white md:w-auto"
      >
        <tabler-icon
          icon="building-store"
          [size]="16"
          class="shrink-0"
          aria-hidden="true"
        />
        Locales
      </a>
    </li>

    <!-- Productos -->
    <li class="w-full md:w-auto">
      <a
        routerLink="/admin/productos"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-[#241F16] text-white"
        class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-sm font-bold text-stone-400 transition hover:bg-[#241F16] hover:text-white md:w-auto"
      >
        <tabler-icon
          icon="package"
          [size]="16"
          class="shrink-0"
          aria-hidden="true"
        />
        Productos
      </a>
    </li>
  `,
})
export class NavAdminLinks {
  // Evento utilizado para solicitar el cierre del menú de navegación.
  readonly closeMenu = output<void>();
}