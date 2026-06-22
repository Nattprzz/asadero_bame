import { Component, input, output } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';

@Component({
  selector: 'app-nav-responsable-links',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, TablerIconComponent],

  // Evita generar un elemento contenedor adicional en el DOM.
  host: {
    '[style.display]': '"contents"',
  },

  template: `
    @let localId = localIdInput();

    @if (localId != null) {
      <!-- Resumen del día -->
      <li class="w-full md:w-auto">
        <a
          [routerLink]="['/admin/local', localId, 'resumen-dia']"
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
          Resumen del día
        </a>
      </li>

      <!-- Pedidos -->
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
          Pedidos
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
export class NavResponsableLinks {
  readonly localIdInput = input<number | null | undefined>(null, {
    alias: 'localId',
  });

  readonly closeMenu = output<void>();
}