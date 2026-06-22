// ─────────────────────────────────────────────────────────────────────────────
// exact-link-active.directive.ts — activación exacta de enlaces.
//
// Esta directiva fuerza que RouterLinkActive utilice coincidencia exacta en
// todas las rutas donde se aplique. De esta forma se evita que enlaces padre
// aparezcan activos cuando el usuario navega a rutas hijas.
// ─────────────────────────────────────────────────────────────────────────────

import { Directive, inject, type OnInit } from '@angular/core';
import { RouterLinkActive } from '@angular/router';

@Directive({
  selector: '[routerLinkActive]',
  standalone: true,
})
export class ExactLinkActive implements OnInit {
  // Referencia a la directiva RouterLinkActive aplicada en el mismo elemento.
  private readonly rla = inject(RouterLinkActive, { self: true });

  // Configura la coincidencia exacta de rutas durante la inicialización.
  ngOnInit(): void {
    this.rla.routerLinkActiveOptions = { exact: true };
  }
}