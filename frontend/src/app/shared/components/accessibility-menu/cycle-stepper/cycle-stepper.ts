// ─────────────────────────────────────────────────────────────────────────────
// cycle-stepper.ts — selector cíclico de opciones.
//
// Este componente permite recorrer una lista de opciones hacia delante o hacia
// atrás, volviendo al inicio o al final cuando se alcanza un extremo. Está
// pensado para controles compactos donde no interesa mostrar un select completo.
//
// También incluye soporte de teclado y avisos accesibles para lectores de
// pantalla mediante LiveAnnouncer.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, inject, input, model } from '@angular/core';
import { LiveAnnouncer } from '@angular/cdk/a11y';
import { TablerIconComponent } from '@tabler/icons-angular';

export interface StepperOption {
  value: string;
  label: string;
}

@Component({
  selector: 'app-cycle-stepper',
  standalone: true,
  imports: [TablerIconComponent],
  templateUrl: './cycle-stepper.html',
})
export class CycleStepper {
  // Servicio utilizado para anunciar cambios a tecnologías asistivas.
  private readonly liveAnnouncer = inject(LiveAnnouncer);

  // Texto mostrado en el componente y usado como etiqueta accesible.
  readonly category = input.required<string>();

  // Opciones disponibles para recorrer.
  readonly options = input.required<StepperOption[]>();

  // Valor seleccionado, preparado para two-way binding.
  readonly selected = model.required<string>();

  // Selecciona la opción anterior, volviendo al final si está en la primera.
  prev(): void {
    const opts = this.options();
    const idx = this.currentIndex();
    const newIdx = idx <= 0 ? opts.length - 1 : idx - 1;

    this.selected.set(opts[newIdx].value);
    this.announce(opts[newIdx].label);
  }

  // Selecciona la opción siguiente, volviendo al inicio si está en la última.
  next(): void {
    const opts = this.options();
    const idx = this.currentIndex();
    const newIdx = idx >= opts.length - 1 ? 0 : idx + 1;

    this.selected.set(opts[newIdx].value);
    this.announce(opts[newIdx].label);
  }

  // Localiza la opción seleccionada dentro del listado actual.
  private currentIndex(): number {
    return this.options().findIndex((option) => option.value === this.selected());
  }

  // Devuelve la etiqueta visible de la opción seleccionada.
  currentLabel(): string {
    return this.options()[this.currentIndex()]?.label ?? '';
  }

  // Anuncia el cambio de opción de forma no intrusiva.
  private announce(text: string): void {
    this.liveAnnouncer.announce(text, 'polite');
  }

  // Gestiona la navegación por teclado del control.
  onKeydown(event: KeyboardEvent): void {
    const opts = this.options();
    const idx = this.currentIndex();

    switch (event.key) {
      case 'ArrowRight':
      case 'ArrowDown':
        event.preventDefault();
        this.next();
        break;

      case 'ArrowLeft':
      case 'ArrowUp':
        event.preventDefault();
        this.prev();
        break;

      case 'Home':
        event.preventDefault();

        if (idx !== 0) {
          this.selected.set(opts[0].value);
          this.announce(opts[0].label);
        }

        break;

      case 'End': {
        event.preventDefault();

        const last = opts.length - 1;

        if (idx !== last) {
          this.selected.set(opts[last].value);
          this.announce(opts[last].label);
        }

        break;
      }
    }
  }

  // Etiqueta accesible utilizada por la plantilla.
  ariaLabel(): string {
    return this.category();
  }
}