import { Component, inject, signal } from '@angular/core';
import { ViewportScroller } from '@angular/common';
import { NavigationEnd, Router, RouterOutlet } from '@angular/router';
import { filter } from 'rxjs';
import { Footer } from './shared/components/footer/footer';
import { NavbarComponent } from './shared/components/navbar/navbar';
import { Background } from './shared/components/background/background';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, Footer, NavbarComponent, Background],
  templateUrl: './app.html',
  styleUrl: './app.css',
})
export class App {
  readonly hidePublicChrome = signal(false);

  constructor() {
    const router = inject(Router);

    inject(ViewportScroller).setOffset(() => {
      const nav = document.querySelector<HTMLElement>('app-navbar nav');
      return [0, nav ? nav.getBoundingClientRect().bottom + 16 : 96];
    });

    router.events
      .pipe(filter((e): e is NavigationEnd => e instanceof NavigationEnd))
      .subscribe((e) => {
        this.hidePublicChrome.set(this.shouldHidePublicChrome(e.urlAfterRedirects));
      });

    this.hidePublicChrome.set(this.shouldHidePublicChrome(router.url));
  }

  private shouldHidePublicChrome(url: string): boolean {
    return url.startsWith('/admin') || url === '/home/login' || url.startsWith('/home/login?');
  }
}
