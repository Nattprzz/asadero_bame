// ─────────────────────────────────────────────────────────────────────────────
// accessibility-menu.ts — menú de accesibilidad.
//
// Este componente permite cambiar opciones generales de accesibilidad de la
// aplicación, como el idioma de la interfaz y el tamaño del texto. Los cambios
// se preparan dentro del panel y solo se aplican cuando el usuario los acepta.
//
// También gestiona la apertura mediante CDK Overlay y mantiene el foco en el
// botón principal al cerrar el menú.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, computed, inject, signal, viewChild, PLATFORM_ID, type ElementRef } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { DOCUMENT } from '@angular/common';
import { OverlayModule, type ConnectedPosition } from '@angular/cdk/overlay';
import { A11yModule } from '@angular/cdk/a11y';
import { TablerIconComponent } from '@tabler/icons-angular';
import { LanguageService, type Language } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';

type FontSize = 'sm' | 'base' | 'lg';
type ColorBlindMode = 'none' | 'rg' | 'by';

const FS_KEY = 'bame_fontsize';
const CB_KEY = 'bame_cb';

const CB_PALETTES: Record<ColorBlindMode, Record<string, string>> = {
  none: {},
  rg:   { '--green': '#3a86ff' },
  by:   { '--yellow': '#ef6db3', '--green': '#11a8bd' },
};

@Component({
  selector: 'app-accessibility-menu',
  standalone: true,
  imports: [OverlayModule, A11yModule, TablerIconComponent],
  templateUrl: './accessibility-menu.html',
  styleUrl: './accessibility-menu.css',
})
export class AccessibilityMenu {
  private readonly langService = inject(LanguageService);
  private readonly platformId = inject(PLATFORM_ID);
  private readonly doc = inject(DOCUMENT);

  // Estado de apertura del menú y referencia al botón que lo activa.
  readonly isOpen = signal(false);
  readonly triggerButton = viewChild<ElementRef<HTMLElement>>('triggerButton');

  // Textos de la interfaz recalculados cuando cambia el idioma.
  readonly t = computed(() => getUiStrings(this.langService.currentLang()).accessibility);

  // Idiomas disponibles dentro del selector de accesibilidad.
  readonly languages: { code: Language; name: string; flag: string }[] = [
    { code: 'es', name: 'Español', flag: '/flags/es_flag.png' },
    { code: 'en', name: 'English', flag: '/flags/uk_flag.png' },
    { code: 'fr', name: 'Français', flag: '/flags/fr_flag.png' },
    { code: 'it', name: 'Italiano', flag: '/flags/it_flag.png' },
    { code: 'de', name: 'Deutsch', flag: '/flags/de_flag.png' },
  ];

  // Tamaños de texto disponibles para toda la aplicación.
  readonly fontSizeOptions: { value: FontSize; label: string }[] = [
    { value: 'sm', label: 'A-' },
    { value: 'base', label: 'A' },
    { value: 'lg', label: 'A+' },
  ];

  // Modos de daltonismo disponibles.
  readonly colorBlindOptions: { value: ColorBlindMode; labelKey: 'cbNone' | 'cbRG' | 'cbBY' }[] = [
    { value: 'none', labelKey: 'cbNone' },
    { value: 'rg',   labelKey: 'cbRG' },
    { value: 'by',   labelKey: 'cbBY' },
  ];

  // Posiciones posibles del panel cuando se abre con CDK Overlay.
  readonly positions: ConnectedPosition[] = [
    {
      originX: 'start',
      originY: 'bottom',
      overlayX: 'start',
      overlayY: 'top',
      offsetY: 18,
      offsetX: -205,
    },
    {
      originX: 'start',
      originY: 'top',
      overlayX: 'start',
      overlayY: 'bottom',
      offsetY: -8,
    },
  ];

  // Cambios temporales que solo se guardan al pulsar aceptar.
  readonly pendingLang = signal<Language>(this.langService.currentLang());
  readonly pendingFontSize = signal<FontSize>(this.loadFontSize());
  readonly pendingColorBlind = signal<ColorBlindMode>(this.loadColorBlind());

  private savedFontSize: FontSize = this.loadFontSize();
  private savedColorBlind: ColorBlindMode = this.loadColorBlind();

  constructor() {
    this.applyFontSize(this.savedFontSize);
    this.applyColorBlind(this.savedColorBlind);

    // Precarga las banderas para que aparezcan sin parpadeos al abrir el panel.
    if (isPlatformBrowser(this.platformId)) {
      this.languages.forEach(({ flag }) => {
        new Image().src = flag;
      });
    }
  }

  // Carga el tamaño de texto guardado previamente en el navegador.
  private loadFontSize(): FontSize {
    if (!isPlatformBrowser(this.platformId)) return 'base';
    const value = localStorage.getItem(FS_KEY);
    return value === 'sm' || value === 'base' || value === 'lg' ? value : 'base';
  }

  // Aplica el tamaño de texto seleccionado sobre el elemento raíz.
  // Usa px en style.fontSize (no clases Tailwind) para evitar purga y referencias
  // circulares: rem en <html> se resuelve contra el valor inicial del browser.
  // También actualiza --bame-fs para que los estilos inline de las páginas
  // puedan escalar usando font-size: var(--bame-fs).
  private applyFontSize(size: FontSize): void {
    if (!isPlatformBrowser(this.platformId)) return;
    const html = this.doc.documentElement;
    const px: Record<FontSize, string> = { sm: '13px', base: '16px', lg: '19px' };
    html.style.fontSize = px[size];
    html.style.setProperty('--bame-fs', px[size]);
  }

  // Carga el modo de daltonismo guardado en el navegador.
  private loadColorBlind(): ColorBlindMode {
    if (!isPlatformBrowser(this.platformId)) return 'none';
    const v = localStorage.getItem(CB_KEY);
    return v === 'rg' || v === 'by' ? v : 'none';
  }

  // Aplica los overrides de color para el modo de daltonismo seleccionado.
  private applyColorBlind(mode: ColorBlindMode): void {
    if (!isPlatformBrowser(this.platformId)) return;
    const html = this.doc.documentElement;
    const allVars = Object.values(CB_PALETTES).flatMap(Object.keys);

    for (const v of allVars) {
      html.style.removeProperty(v);
    }
    for (const [v, color] of Object.entries(CB_PALETTES[mode])) {
      html.style.setProperty(v, color);
    }
  }

  // Devuelve el texto descriptivo de cada tamaño de fuente.
  fontSizeTitle(value: FontSize): string {
    const text = this.t();

    if (value === 'sm') return text.small;
    if (value === 'lg') return text.large;

    return text.medium;
  }

  // Guarda los cambios pendientes y los aplica en la interfaz.
  onAccept(): void {
    const lang = this.pendingLang();
    const fontSize = this.pendingFontSize();
    const colorBlind = this.pendingColorBlind();

    this.langService.setLang(lang);
    if (isPlatformBrowser(this.platformId)) {
      localStorage.setItem(FS_KEY, fontSize);
      localStorage.setItem(CB_KEY, colorBlind);
    }

    this.savedFontSize = fontSize;
    this.savedColorBlind = colorBlind;
    this.applyFontSize(fontSize);
    this.applyColorBlind(colorBlind);
    this.close();
  }

  // Abre el panel restaurando los valores guardados como punto de partida.
  open(): void {
    this.pendingLang.set(this.langService.currentLang());
    this.pendingFontSize.set(this.savedFontSize);
    this.pendingColorBlind.set(this.savedColorBlind);
    this.isOpen.set(true);
  }

  // Cierra el panel descartando cambios no aceptados y devolviendo el foco.
  close(): void {
    this.isOpen.set(false);
    this.pendingLang.set(this.langService.currentLang());
    this.pendingFontSize.set(this.savedFontSize);
    this.pendingColorBlind.set(this.savedColorBlind);

    requestAnimationFrame(() => this.triggerButton()?.nativeElement.focus());
  }

  // Alterna entre abrir y cerrar el menú.
  toggle(): void {
    if (this.isOpen()) {
      this.close();
    } else {
      this.open();
    }
  }

  // Permite abrir el menú con teclado desde el botón principal.
  onTriggerKeydown(event: KeyboardEvent): void {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      event.stopPropagation();
      this.toggle();
    }
  }

  // Permite cerrar el panel con Escape desde su interior.
  onPanelKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
      event.preventDefault();
      event.stopPropagation();
      this.close();
    }
  }
}
