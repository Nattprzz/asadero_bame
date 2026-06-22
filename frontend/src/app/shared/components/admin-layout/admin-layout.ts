import { Component, computed, inject, signal } from '@angular/core';
import { NavigationEnd, Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { filter } from 'rxjs';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { TablerIconComponent } from '@tabler/icons-angular';
import { AuthService } from '../../../core/services/auth-service';
import { AccessibilityMenu } from '../accessibility-menu/accessibility-menu';

@Component({
  selector: 'app-admin-layout',
  standalone: true,
  imports: [RouterOutlet, RouterLink, RouterLinkActive, TablerIconComponent, AccessibilityMenu],
  templateUrl: './admin-layout.html',
})
export class AdminLayout {
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);

  readonly user = this.auth.user;
  // localId del usuario autenticado (null para admin sin local asignado)
  readonly localId = this.auth.currentLocalId;
  readonly role = this.auth.currentRole;
  readonly sidebarOpen = signal(false);

  // localId extraído de la URL actual (/admin/local/:id/*)
  // Permite que admin vea la sidebar del local que está visitando
  private readonly routeLocalId = signal<number | null>(null);
  readonly isStandalonePanel = signal(false);

  // Usa el localId del usuario; si es null (admin), usa el de la URL
  readonly sidebarLocalId = computed(() => this.localId() ?? this.routeLocalId());
  readonly hasLocal = computed(() => this.sidebarLocalId() != null);
  readonly canAccessProducts = computed(() =>
    ['admin', 'manager', 'store'].includes(this.role()),
  );

  constructor() {
    this.extractRouteLocalId(this.router.url);

    this.router.events
      .pipe(
        filter((e): e is NavigationEnd => e instanceof NavigationEnd),
        takeUntilDestroyed(),
      )
      .subscribe((e) => this.extractRouteLocalId(e.urlAfterRedirects));
  }

  private extractRouteLocalId(url: string): void {
    const match = url.match(/\/admin\/local\/(\d+)/);
    this.routeLocalId.set(match ? Number(match[1]) : null);
    this.isStandalonePanel.set(
      /^\/admin\/local\/\d+\/(?:gerente|resumen-dia)(?:[/?#]|$)/.test(url),
    );
  }

  toggle(): void {
    this.sidebarOpen.update((v) => !v);
  }

  close(): void {
    this.sidebarOpen.set(false);
  }

  logout(): void {
    this.auth.logout();
  }
}
