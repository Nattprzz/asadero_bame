// ─────────────────────────────────────────────────────────────────────────────
// background.ts — fondo visual de la aplicación.
//
// Este componente renderiza un canvas fijo detrás del contenido principal.
// También adapta automáticamente el tamaño del canvas cuando cambia el tamaño
// de la ventana para que el fondo siempre ocupe toda la pantalla.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, inject, viewChild, type AfterViewInit, type ElementRef } from '@angular/core';
import { ViewportRuler } from '@angular/cdk/scrolling';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-background',
  standalone: true,
  templateUrl: './background.html',
  styles: `
    :host {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: -1;
      pointer-events: none;
      background: var(--color-surface-page);
    }

    canvas {
      display: block;
      width: 100%;
      height: 100%;
    }
  `,
})
export class Background implements AfterViewInit {
  // Referencia al canvas utilizado como fondo.
  private readonly canvasRef = viewChild.required<ElementRef<HTMLCanvasElement>>('bgCanvas');

  // Servicio del CDK usado para detectar cambios de tamaño en la ventana.
  private readonly viewportRuler = inject(ViewportRuler);

  constructor() {
    this.viewportRuler
      .change(100)
      .pipe(takeUntilDestroyed())
      .subscribe(() => this.resizeCanvas());
  }

  // Ajusta el canvas cuando la vista ya está disponible.
  ngAfterViewInit(): void {
    this.resizeCanvas();
  }

  // Sincroniza el tamaño real del canvas con el tamaño visible del viewport.
  protected resizeCanvas(): void {
    const canvas = this.canvasRef()?.nativeElement;

    if (!canvas) return;

    const { width, height } = this.viewportRuler.getViewportSize();

    canvas.width = width;
    canvas.height = height;
  }
}