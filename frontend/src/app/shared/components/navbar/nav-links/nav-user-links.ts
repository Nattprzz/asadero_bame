import { Component, computed, inject, output } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { ExactLinkActive } from '@shared/directives/exact-link-active';
import { LanguageService } from '@core/services/language.service';
import { getUiStrings } from '@core/i18n/ui-strings';
import { AuthService } from '@core/services/auth-service';

@Component({
  selector: 'app-nav-user-links',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, ExactLinkActive, TablerIconComponent],
  host: { '[style.display]': '"contents"' },
  template: `
    <li class="w-full md:w-auto">
      <a
        routerLink="home"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-white/10 text-accent font-medium"
        [routerLinkActiveOptions]="{ exact: true }"
        class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm uppercase tracking-wide hover:bg-white/10 transition-all duration-200"
      >
        <tabler-icon icon="home" [size]="16" class="shrink-0" aria-hidden="true" />
        {{ t().nav.inicio }}
      </a>
    </li>

    <li class="w-full md:w-auto">
      <a
        routerLink="home/carta"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-white/10 text-accent font-medium"
        class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm uppercase tracking-wide hover:bg-white/10 transition-all duration-200"
      >
        <tabler-icon icon="clipboard-list" [size]="16" class="shrink-0" aria-hidden="true" />
        {{ t().nav.menu }}
      </a>
    </li>

    <li class="w-full md:w-auto">
      <a
        routerLink="home/locales"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-white/10 text-accent font-medium"
        class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm uppercase tracking-wide hover:bg-white/10 transition-all duration-200"
      >
        <tabler-icon icon="building-store" [size]="16" class="shrink-0" aria-hidden="true" />
        {{ t().nav.locales }}
      </a>
    </li>

    @if (auth.isLoggedIn()) {
      <li class="w-full md:w-auto">
        <a
          routerLink="home/pedidos"
          (click)="closeMenu.emit()"
          routerLinkActive="bg-white/10 text-accent font-medium"
          class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm uppercase tracking-wide hover:bg-white/10 transition-all duration-200"
        >
          <tabler-icon icon="file-description" [size]="16" class="shrink-0" aria-hidden="true" />
          {{ t().nav.pedidos }}
        </a>
      </li>
    }

    <li class="w-full md:w-auto">
      <a
        routerLink="home/acerca-de-nosotros"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-white/10 text-accent font-medium"
        class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm uppercase tracking-wide hover:bg-white/10 transition-all duration-200"
      >
        <tabler-icon icon="meat" [size]="16" class="shrink-0" aria-hidden="true" />
        {{ t().nav.nosotros }}
      </a>
    </li>

    <li class="w-full md:w-auto">
      <a
        routerLink="home/contacto"
        (click)="closeMenu.emit()"
        routerLinkActive="bg-white/10 text-accent font-medium"
        class="flex items-center justify-center md:justify-start gap-2 w-full text-center px-5 py-2.5 rounded-xl text-sm uppercase tracking-wide hover:bg-white/10 transition-all duration-200"
      >
        <tabler-icon icon="mail" [size]="16" class="shrink-0" aria-hidden="true" />
        {{ t().nav.contacto }}
      </a>
    </li>
  `,
})
export class NavUserLinks {
  private readonly lang = inject(LanguageService);
  protected readonly auth = inject(AuthService);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));
  readonly closeMenu = output();
}
