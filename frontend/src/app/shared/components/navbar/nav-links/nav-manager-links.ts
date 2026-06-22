// ─────────────────────────────────────────────────────────────────────────────
// nav-manager-links.ts — enlaces de navegación para gerentes.
//
// Este componente muestra los accesos disponibles para usuarios con rol de
// gerente. Los enlaces se generan dinámicamente utilizando el identificador
// del local asignado al usuario.
//
// Al pulsar cualquier opción se notifica al componente padre para cerrar el
// menú de navegación, especialmente útil en dispositivos móviles.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, input, output } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { ExactLinkActive } from '@shared/directives/exact-link-active';

@Component({
  selector: 'app-nav-manager-links',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, ExactLinkActive, TablerIconComponent],

  // Evita añadir un contenedor extra al DOM.
  host: { '[style.display]': '"contents"' },

  template: `
    @let localId = localIdInput();

    @if (localId != null) {
      <li class="w-full md:w-auto">
        <a
          [routerLink]="['/admin/local', localId]"
          (click)="closeMenu.emit()"
          routerLinkActive="bg-white/10 font-medium"
          class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm tracking-wide hover:bg-white/10 transition-all duration-200"
        >
          <tabler-icon icon="building-store" [size]="16" class="shrink-0" aria-hidden="true" />
          Local
        </a>
      </li>

      <li class="w-full md:w-auto">
        <a
          [routerLink]="['/admin/local', localId, 'reservas']"
          (click)="closeMenu.emit()"
          routerLinkActive="bg-white/10 text-accent font-medium"
          class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm tracking-wide hover:bg-white/10 transition-all duration-200"
        >
          <tabler-icon icon="file-description" [size]="16" class="shrink-0" aria-hidden="true" />
          Reservas
        </a>
      </li>

      <li class="w-full md:w-auto">
        <a
          [routerLink]="['/admin/local', localId, 'disponibilidad']"
          (click)="closeMenu.emit()"
          routerLinkActive="bg-white/10 text-accent font-medium"
          class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm tracking-wide hover:bg-white/10 transition-all duration-200"
        >
          <tabler-icon icon="meat" [size]="16" class="shrink-0" aria-hidden="true" />
          Disponibilidad
        </a>
      </li>
    }
  `,
})
export class NavManagerLinks {
  // Identificador del local gestionado por el usuario.
  readonly localIdInput = input<number | null | undefined>(null, {
    alias: 'localId',
  });

  // Evento utilizado para solicitar el cierre del menú de navegación.
  readonly closeMenu = output();
}