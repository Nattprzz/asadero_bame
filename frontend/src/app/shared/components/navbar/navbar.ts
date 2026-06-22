// ─────────────────────────────────────────────────────────────────────────────
// navbar.ts — barra de navegación principal.
//
// Este componente gestiona la navegación principal de la aplicación. Muestra
// diferentes enlaces según el rol del usuario, permite cambiar entre tema
// claro y oscuro, y controla la apertura de los distintos menús desplegables.
//
// También incorpora mejoras de accesibilidad para la navegación mediante
// teclado.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, computed, HostListener, inject, ViewChild } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { A11yModule } from '@angular/cdk/a11y';
import { AuthService } from '@core/services/auth-service';
import { UserPrefService } from '@core/services/user-pref-service';
import { LanguageService } from '@core/services/language.service';
import { getUiStrings } from '@core/i18n/ui-strings';
import { TablerIconComponent } from '@tabler/icons-angular';
import { IconSun, IconMoon } from '@tabler/icons-angular';
import { NavUserLinks } from '@shared/components/navbar/nav-links/nav-user-links';
import { NavStoreLinks } from '@shared/components/navbar/nav-links/nav-store-links';
import { NavResponsableLinks } from '@shared/components/navbar/nav-links/nav-responsable-links';
import { NavManagerLinks } from '@shared/components/navbar/nav-links/nav-manager-links';
import { NavAdminLinks } from '@shared/components/navbar/nav-links/nav-admin-links';
import { AccessibilityMenu } from '@shared/components/accessibility-menu/accessibility-menu';
import { UserMenuComponent } from '@shared/components/user-menu/user-menu';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [
    RouterLink,
    RouterLinkActive,
    A11yModule,
    TablerIconComponent,
    NavUserLinks,
    NavStoreLinks,
    NavResponsableLinks,
    NavManagerLinks,
    NavAdminLinks,
    AccessibilityMenu,
    UserMenuComponent,
  ],
  templateUrl: './navbar.html',
})
export class NavbarComponent {
  readonly auth = inject(AuthService);
  readonly userPref = inject(UserPrefService);
  private readonly lang = inject(LanguageService);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));
  protected readonly icons = {
    sun: IconSun,
    moon: IconMoon,
  };

  // Estado del menú móvil.
  isMenuOpen = false;

  // Referencias a otros menús para evitar que permanezcan abiertos a la vez.
  @ViewChild(AccessibilityMenu) protected a11yMenu?: AccessibilityMenu;
  @ViewChild(UserMenuComponent) protected userMenuComp?: UserMenuComponent;

  // Determina qué conjunto de enlaces debe mostrarse según el rol actual.
  readonly navRole = computed<'user' | 'store' | 'responsable' | 'manager' | 'admin'>(() => {
    const role = this.auth.currentRole();

    if (role === 'admin') return 'admin';
    if (role === 'manager') return 'manager';
    if (role === 'responsable') return 'responsable';
    if (role === 'store') return 'store';

    return 'user';
  });

  // Alterna entre tema claro y oscuro.
  toggleTheme(): void {
    const current = this.userPref.effectiveTheme();

    this.userPref.setTheme(current === 'dark' ? 'light' : 'dark');
  }

  // Abre o cierra el menú principal y cierra otros desplegables activos.
  toggleMenu(): void {
    this.a11yMenu?.close();
    this.userMenuComp?.closeDropdown();

    this.isMenuOpen = !this.isMenuOpen;
  }

  // Cierra el menú principal.
  closeMenu(): void {
    this.isMenuOpen = false;
  }

  // Permite cerrar el menú usando la tecla Escape.
  onMenuKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
      this.closeMenu();
    }
  }

  // Escucha Escape a nivel global para cerrar el menú si está abierto.
  @HostListener('document:keydown.escape')
  onEscape(): void {
    if (this.isMenuOpen) {
      this.closeMenu();
    }
  }
}