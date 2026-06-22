// ─────────────────────────────────────────────────────────────────────────────
// language-selector.ts — selector de idioma.
//
// Este componente muestra un selector desplegable de idiomas. Recibe desde el
// componente padre el listado de idiomas disponibles y permite cambiar el
// idioma seleccionado mediante two-way binding.
//
// También incluye navegación por teclado y búsqueda rápida por inicial para
// mejorar la accesibilidad del control.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, computed, HostListener, input, model, signal } from '@angular/core';
import { TablerIconComponent } from '@tabler/icons-angular';

export interface LanguageOption {
  code: string;
  nativeName: string;
}

@Component({
  selector: 'app-language-selector',
  standalone: true,
  imports: [TablerIconComponent],
  templateUrl: './language-selector.html',
})
export class LanguageSelector {
  // Idiomas disponibles recibidos desde el componente padre.
  readonly languages = input.required<LanguageOption[]>();

  // Código del idioma seleccionado, preparado para two-way binding.
  readonly selected = model.required<string>();

  // Estado de apertura del desplegable.
  readonly isOpen = signal(false);

  // Índice de la opción resaltada actualmente.
  readonly activeIndex = signal(0);

  // Texto mostrado en el botón según el idioma seleccionado.
  readonly currentLabel = computed(() => {
    const code = this.selected();

    return this.languages().find((lang) => lang.code === code)?.nativeName ?? code;
  });

  // Abre o cierra el selector y sitúa el foco visual en el idioma actual.
  toggle(): void {
    this.isOpen.update((value) => !value);

    if (this.isOpen()) {
      const index = this.languages().findIndex((lang) => lang.code === this.selected());

      this.activeIndex.set(index >= 0 ? index : 0);
    }
  }

  // Selecciona un idioma y cierra el desplegable.
  select(code: string): void {
    this.selected.set(code);
    this.isOpen.set(false);
  }

  // Cierra el desplegable sin cambiar el idioma.
  close(): void {
    this.isOpen.set(false);
  }

  // Gestiona la navegación por teclado desde el botón principal.
  onTriggerKeydown(event: KeyboardEvent): void {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      this.toggle();
    }

    if (event.key === 'ArrowDown') {
      event.preventDefault();

      if (!this.isOpen()) {
        this.toggle();
      }

      this.moveHighlight(1);
    }

    if (event.key === 'ArrowUp') {
      event.preventDefault();

      if (!this.isOpen()) {
        this.toggle();
      }

      this.moveHighlight(-1);
    }

    if (event.key === 'Escape') {
      this.close();
    }
  }

  // Gestiona la navegación por teclado dentro del listado de idiomas.
  onListKeydown(event: KeyboardEvent): void {
    switch (event.key) {
      case 'ArrowDown':
        event.preventDefault();
        this.moveHighlight(1);
        break;

      case 'ArrowUp':
        event.preventDefault();
        this.moveHighlight(-1);
        break;

      case 'Enter':
        event.preventDefault();
        this.select(this.languages()[this.activeIndex()].code);
        break;

      case 'Escape':
        event.preventDefault();
        this.close();
        break;
    }
  }

  // Mueve la opción resaltada permitiendo volver al inicio o al final.
  private moveHighlight(delta: number): void {
    const length = this.languages().length;

    this.activeIndex.update((index) => ((index + delta) % length + length) % length);
  }

  // Permite saltar a un idioma escribiendo su primera letra.
  @HostListener('keydown', ['$event'])
  onKeydown(event: KeyboardEvent): void {
    if (!this.isOpen()) return;

    if (event.key.length === 1 && /[a-zA-Z]/.test(event.key)) {
      const char = event.key.toLowerCase();

      const index = this.languages().findIndex((language, i) => {
        return i > this.activeIndex() && language.nativeName.toLowerCase().startsWith(char);
      });

      if (index >= 0) {
        event.preventDefault();
        this.activeIndex.set(index);
      } else {
        const wrapIndex = this.languages().findIndex((language) => {
          return language.nativeName.toLowerCase().startsWith(char);
        });

        if (wrapIndex >= 0) {
          event.preventDefault();
          this.activeIndex.set(wrapIndex);
        }
      }
    }
  }

  // Optimiza el renderizado de opciones usando el código del idioma.
  trackByCode(_: number, language: LanguageOption): string {
    return language.code;
  }
}