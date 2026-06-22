import { Injectable, inject, signal, effect, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { DOCUMENT } from '@angular/common';

export type Theme = 'system' | 'light' | 'dark';

const STORAGE_KEY = 'a11y_theme';

@Injectable({ providedIn: 'root' })
export class UserPrefService {
  private readonly platformId = inject(PLATFORM_ID);
  private readonly document = inject(DOCUMENT);

  private get isBrowser(): boolean {
    return isPlatformBrowser(this.platformId);
  }

  readonly theme = signal<Theme>(this.load());
  readonly effectiveTheme = signal<'light' | 'dark'>(this.resolveTheme(this.theme()));

  constructor() {
    this.applyTheme(this.effectiveTheme());

    if (this.isBrowser) {
      const mq = window.matchMedia('(prefers-color-scheme: dark)');
      mq.addEventListener('change', () => {
        if (this.theme() === 'system') {
          const resolved = this.resolveTheme('system');
          this.effectiveTheme.set(resolved);
          this.applyTheme(resolved);
        }
      });
    }

    effect(() => {
      const theme = this.theme();
      if (this.isBrowser) {
        localStorage.setItem(STORAGE_KEY, theme);
      }
      const resolved = this.resolveTheme(theme);
      this.effectiveTheme.set(resolved);
      this.applyTheme(resolved);
    });
  }

  setTheme(theme: Theme): void {
    this.theme.set(theme);
  }

  private load(): Theme {
    if (!this.isBrowser) return 'system';
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored === 'light' || stored === 'dark' || stored === 'system') return stored;
    } catch {
      // ignore
    }
    return 'system';
  }

  private resolveTheme(theme: Theme): 'light' | 'dark' {
    if (theme === 'system') {
      if (!this.isBrowser) return 'light';
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return theme;
  }

  private applyTheme(theme: 'light' | 'dark'): void {
    const root = this.document.documentElement;
    if (theme === 'dark') {
      root.setAttribute('data-theme', 'dark');
    } else {
      root.removeAttribute('data-theme');
    }
  }
}
