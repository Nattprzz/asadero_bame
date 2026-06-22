import { Injectable, effect, signal, inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { DOCUMENT } from '@angular/common';

export type Language = 'es' | 'en' | 'fr' | 'it' | 'de';

export const SUPPORTED_LANGUAGES: Language[] = ['es', 'en', 'fr', 'it', 'de'];

const LANG_KEY = 'bame_language';

@Injectable({ providedIn: 'root' })
export class LanguageService {
  private readonly platformId = inject(PLATFORM_ID);
  private readonly document = inject(DOCUMENT);

  readonly currentLang = signal<Language>(this.loadLang());

  constructor() {
    effect(() => {
      const lang = this.currentLang();
      if (isPlatformBrowser(this.platformId)) {
        localStorage.setItem(LANG_KEY, lang);
      }
      this.document.documentElement.lang = lang;
    });
  }

  setLang(lang: Language): void {
    this.currentLang.set(lang);
  }

  private loadLang(): Language {
    if (!isPlatformBrowser(this.platformId)) return 'es';
    try {
      const stored = localStorage.getItem(LANG_KEY) as Language | null;
      return SUPPORTED_LANGUAGES.includes(stored as Language) ? (stored as Language) : 'es';
    } catch {
      return 'es';
    }
  }
}
