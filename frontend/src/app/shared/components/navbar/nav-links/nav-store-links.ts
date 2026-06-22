// ─────────────────────────────────────────────────────────────────────────────
// nav-store-links.ts — enlaces de navegación para locales.
//
// Este componente muestra las opciones disponibles para usuarios asociados a
// un local concreto. Las rutas se construyen dinámicamente utilizando el
// identificador del local recibido desde el componente padre.
//
// Al seleccionar cualquier opción se emite un evento para cerrar el menú de
// navegación, especialmente útil en dispositivos móviles.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, input, output } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { ExactLinkActive } from '@shared/directives/exact-link-active';

@Component({
  selector: 'app-nav-store-links',
  standalone: true,
  imports: [
    RouterLink,
    RouterLinkActive,
    ExactLinkActive,
    TablerIconComponent,
  ],

  // Evita añadir un contenedor adicional al DOM.
  host: {
    '[style.display]': '"contents"',
  },

  template: `
    @let localId = localIdInput();

    @if (localId != null) {
      <!-- Local -->
      <li class="w-full md:w-auto">
        <a
          [routerLink]="['/admin/local', localId]"
          (click)="closeMenu.emit()"
          routerLinkActive="bg-[#241F16] text-white"
          [routerLinkActiveOptions]="{ exact: true }"
          class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-sm font-bold text-stone-400 transition hover:bg-[#241F16] hover:text-white md:w-auto"
        >
          <tabler-icon
            icon="building-store"
            [size]="16"
            class="shrink-0"
            aria-hidden="true"
          />
          Local
        </a>
      </li>

      <!-- Reservas -->
      <li class="w-full md:w-auto">
        <a
          [routerLink]="['/admin/local', localId, 'reservas']"
          (click)="closeMenu.emit()"
          routerLinkActive="bg-[#241F16] text-white"
          class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-sm font-bold text-stone-400 transition hover:bg-[#241F16] hover:text-white md:w-auto"
        >
          <tabler-icon
            icon="file-description"
            [size]="16"
            class="shrink-0"
            aria-hidden="true"
          />
          Reservas
        </a>
      </li>

      <!-- Disponibilidad -->
      <li class="w-full md:w-auto">
        <a
          [routerLink]="['/admin/local', localId, 'disponibilidad']"
          (click)="closeMenu.emit()"
          routerLinkActive="bg-[#241F16] text-white"
          class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-sm font-bold text-stone-400 transition hover:bg-[#241F16] hover:text-white md:w-auto"
        >
          <tabler-icon
            icon="checklist"
            [size]="16"
            class="shrink-0"
            aria-hidden="true"
          />
          Disponibilidad
        </a>
      </li>
    }
  `,
})
export class NavStoreLinks {
  // Identificador del local utilizado para construir las rutas.
  readonly localIdInput = input<number | null | undefined>(null, {
    alias: 'localId',
  });

  // Evento utilizado para solicitar el cierre del menú de navegación.
  readonly closeMenu = output<void>();
}