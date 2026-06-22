// ─────────────────────────────────────────────────────────────────────────────
// user-menu.ts — menú de usuario.
//
// Este componente gestiona el menú de usuario de la barra de navegación.
// Controla el desplegable principal, el panel de perfil, el menú lateral y los
// modales de inicio de sesión, registro y verificación de correo.
//
// También se encarga de bloquear el scroll cuando hay un modal abierto y de
// devolver el foco al elemento correcto al cerrar cada panel.
// ─────────────────────────────────────────────────────────────────────────────

import {
  Component,
  computed,
  HostListener,
  inject,
  type ElementRef,
  type OnDestroy,
  viewChild,
} from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { A11yModule } from '@angular/cdk/a11y';
import { OverlayModule, type ConnectedPosition } from '@angular/cdk/overlay';
import { AuthService } from '../../../core/services/auth-service';
import { LanguageService } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';
import { TablerIconComponent } from '@tabler/icons-angular';
import { LoginForm } from '../../../features/auth/login-form/login-form';
import { RegisterForm } from '../../../features/auth/register-form/register-form';
import { EmailVerification } from '../../../features/auth/email-verification/email-verification';

@Component({
  selector: 'app-user-menu',
  standalone: true,
  imports: [
    RouterLink,
    A11yModule,
    OverlayModule,
    LoginForm,
    RegisterForm,
    EmailVerification,
    TablerIconComponent,
  ],
  templateUrl: './user-menu.html',
})
export class UserMenuComponent implements OnDestroy {
  readonly auth = inject(AuthService);
  private readonly lang = inject(LanguageService);
  private readonly router = inject(Router);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));

  // Estados de apertura de los distintos paneles y modales.
  isOpen = false;
  isProfileOpen = false;
  isSidebarOpen = false;
  isLoginOpen = false;
  isRegisterOpen = false;
  isVerifyOpen = false;

  // Elemento que abrió el panel actual para poder devolverle el foco al cerrar.
  private triggerElement: HTMLElement | null = null;
  readonly menuButton = viewChild<ElementRef<HTMLElement>>('menuButton');

  // Posiciones del desplegable principal respecto al icono de usuario.
  readonly dropdownPositions: ConnectedPosition[] = [
    {
      originX: 'end',
      originY: 'bottom',
      overlayX: 'end',
      overlayY: 'top',
      offsetY: 10,
      offsetX: 0,
    },
    {
      originX: 'end',
      originY: 'top',
      overlayX: 'end',
      overlayY: 'bottom',
      offsetY: -8,
      offsetX: 0,
    },
  ];

  // Guarda el elemento que tenía el foco antes de abrir un panel.
  private saveTrigger(): void {
    this.triggerElement = document.activeElement as HTMLElement | null;
  }

  // Devuelve el foco al disparador original o al botón principal como fallback.
  private restoreFocus(): void {
    if (this.triggerElement && document.contains(this.triggerElement)) {
      this.triggerElement.focus();
    } else {
      this.menuButton()?.nativeElement.focus();
    }

    this.triggerElement = null;
  }

  // Abre o cierra el desplegable principal de usuario.
  toggleDropdown(): void {
    if (this.isOpen) {
      this.closeDropdown();
      return;
    }

    this.saveTrigger();
    this.isOpen = true;
  }

  // Cierra el desplegable principal y restaura el foco.
  closeDropdown(): void {
    this.isOpen = false;
    this.restoreFocus();
  }

  // Cierra sesión y oculta el menú.
  logout(): void {
    this.auth.logout();
    this.closeDropdown();
  }

  // Abre el panel de perfil.
  openProfile(): void {
    this.saveTrigger();
    this.isOpen = false;
    this.isProfileOpen = true;
    this.lockScroll();
  }

  // Cierra el panel de perfil.
  closeProfile(): void {
    this.isProfileOpen = false;
    this.unlockScroll();
    this.restoreFocus();
  }

  // Abre o cierra el menú lateral.
  toggleSidebar(): void {
    this.isSidebarOpen = !this.isSidebarOpen;
  }

  // Cierra el menú lateral.
  closeSidebar(): void {
    this.isSidebarOpen = false;
  }

  // Navega a la página de inicio de sesión cerrando el dropdown.
  navigateToLogin(): void {
    this.closeDropdown();
    this.router.navigateByUrl('/home/login');
  }

  // Navega a la página de registro cerrando el dropdown.
  navigateToRegister(): void {
    this.closeDropdown();
    this.router.navigateByUrl('/home/login?mode=register');
  }

  // Abre el modal de inicio de sesión.
  openLogin(): void {
    this.saveTrigger();
    this.closeAll();
    this.isLoginOpen = true;
    this.lockScroll();
  }

  // Cierra el modal de inicio de sesión.
  closeLogin(): void {
    if (this.isLoginOpen) {
      this.isLoginOpen = false;
      this.unlockScroll();
      this.restoreFocus();
    }
  }

  // Abre el modal de registro.
  openRegister(): void {
    this.saveTrigger();
    this.closeAll();
    this.isRegisterOpen = true;
    this.lockScroll();
  }

  // Cierra el modal de registro.
  closeRegister(): void {
    if (this.isRegisterOpen) {
      this.isRegisterOpen = false;
      this.unlockScroll();
      this.restoreFocus();
    }
  }

  // Abre el modal de verificación de correo.
  openVerify(): void {
    this.saveTrigger();
    this.closeAll();
    this.isVerifyOpen = true;
    this.lockScroll();
  }

  // Cierra el modal de verificación de correo.
  closeVerify(): void {
    if (this.isVerifyOpen) {
      this.isVerifyOpen = false;
      this.unlockScroll();
      this.restoreFocus();
    }
  }

  // Cambia del login al registro manteniendo el foco de origen guardado.
  switchToRegister(): void {
    this.isLoginOpen = false;
    this.closeAllExceptTrigger();
    this.isRegisterOpen = true;
  }

  // Cambia del registro al login manteniendo el foco de origen guardado.
  switchToLogin(): void {
    this.isRegisterOpen = false;
    this.closeAllExceptTrigger();
    this.isLoginOpen = true;
  }

  // Vuelve desde la verificación al formulario de registro.
  backToRegister(): void {
    this.isVerifyOpen = false;
    this.closeAllExceptTrigger();
    this.isRegisterOpen = true;
  }

  // Cierra la verificación cuando el proceso termina correctamente.
  onVerified(): void {
    this.closeVerify();
  }

  // Bloquea el scroll del documento cuando hay un modal abierto.
  private lockScroll(): void {
    document.body.style.overflow = 'hidden';
  }

  // Restaura el scroll del documento.
  private unlockScroll(): void {
    document.body.style.overflow = '';
  }

  // Cierra todos los paneles y modales.
  private closeAll(): void {
    this.isOpen = false;
    this.isProfileOpen = false;
    this.isLoginOpen = false;
    this.isRegisterOpen = false;
    this.isVerifyOpen = false;
    this.isSidebarOpen = false;
  }

  // Cierra paneles durante cambios entre modales sin borrar el foco original.
  private closeAllExceptTrigger(): void {
    this.isOpen = false;
    this.isProfileOpen = false;
    this.isLoginOpen = false;
    this.isRegisterOpen = false;
    this.isVerifyOpen = false;
    this.isSidebarOpen = false;
  }

  // Cierra el panel activo cuando el usuario pulsa Escape.
  @HostListener('document:keydown.escape')
  onEscape(): void {
    if (this.isSidebarOpen) {
      this.closeSidebar();
      return;
    }

    if (this.isVerifyOpen) {
      this.closeVerify();
      return;
    }

    if (this.isRegisterOpen) {
      this.closeRegister();
      return;
    }

    if (this.isLoginOpen) {
      this.closeLogin();
      return;
    }

    if (this.isProfileOpen) {
      this.closeProfile();
      return;
    }

    if (this.isOpen) {
      this.closeDropdown();
    }
  }

  // Limpia estilos globales si el componente se destruye con un modal abierto.
  ngOnDestroy(): void {
    document.body.style.overflow = '';
  }
}
