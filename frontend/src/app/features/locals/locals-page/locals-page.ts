import { Component, DestroyRef, OnInit, computed, inject, signal } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Meta, Title } from '@angular/platform-browser';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { LocalService } from '@core/services/local.service';
import { LanguageService } from '@core/services/language.service';
import { getUiStrings } from '@core/i18n/ui-strings';
import type { HoursMap, Local } from '@core/models';

interface LocalCard {
  id: number;
  name: string;
  image: string;
  tag: string;
  addr: string;
  phone: string;
  telHref: string;
  email: string;
  mailHref: string;
  today: string;
  mapsHref: string;
  reservaPath: string;
  isOpen: boolean;
}

@Component({
  selector: 'app-locals-page',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './locals-page.html',
})
export class LocalsPage implements OnInit {
  private readonly meta = inject(Meta);
  private readonly title = inject(Title);
  private readonly localService = inject(LocalService);
  private readonly lang = inject(LanguageService);
  private readonly destroyRef = inject(DestroyRef);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));

  readonly query = signal('');
  readonly locals = signal<LocalCard[]>([]);
  readonly loading = signal(true);
  readonly error = signal(false);
  readonly searchFocused = signal(false);

  readonly filtered = computed<LocalCard[]>(() => {
    const q = this.norm(this.query().trim());
    if (!q) return this.locals();
    return this.locals().filter((l) => this.norm(l.name + ' ' + l.addr).includes(q));
  });

  readonly openCount = computed(() => this.locals().filter((l) => l.isOpen).length);
  readonly hasResults = computed(() => this.filtered().length > 0);
  readonly noResults = computed(
    () => !this.loading() && this.filtered().length === 0 && this.query().trim().length > 0,
  );

  private readonly DAYS = [
    'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday',
  ] as const;

  ngOnInit(): void {
    this.title.setTitle('Nuestros locales | Bame — Asaderos en Murcia');
    this.meta.updateTag({ name: 'description', content: 'Encuentra el asadero BAME más cercano en la Región de Murcia. Pide online y recoge tu pollo asado caliente en 20 minutos.' });
    this.meta.updateTag({ name: 'robots', content: 'index, follow' });

    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.error.set(false);

    this.localService.getLocales().pipe(takeUntilDestroyed(this.destroyRef)).subscribe({
      next: (list) => {
        this.locals.set(list.map((l) => this.mapCard(l)));
        this.loading.set(false);
      },
      error: () => {
        this.loading.set(false);
        this.error.set(true);
      },
    });
  }

  setQuery(event: Event): void {
    this.query.set((event.target as HTMLInputElement).value);
  }

  private mapCard(l: Local): LocalCard {
    const today = this.DAYS[new Date().getDay()];
    return {
      id: l.id,
      name: l.nombre,
      image: l.foto,
      tag: l.nombre.replace(/^asadero\s+/i, '').toUpperCase(),
      addr: l.ubicacion,
      phone: l.telefono,
      telHref: 'tel:+34' + l.telefono.replace(/\s/g, ''),
      email: l.email ?? '',
      mailHref: l.email ? 'mailto:' + l.email : '',
      today: this.formatToday(l.hours ?? {}, today),
      mapsHref: this.mapsUrl(l),
      reservaPath: '/home/locales/' + this.slugify(l.nombre) + '/reserva',
      isOpen: l.estado === '1' || l.estado === '3' || l.estado === '4',
    };
  }

  private formatToday(hours: HoursMap, day: string): string {
    const slots = hours[day];
    if (!slots || slots.length === 0) return this.t().locales.closedToday;
    return slots.map((s) => s.open + ' – ' + s.close).join(' · ');
  }

  private mapsUrl(l: Local): string {
    if (l.latitud && l.longitud) {
      return `https://www.google.com/maps?q=${l.latitud},${l.longitud}`;
    }
    return 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(l.nombre + ' ' + l.ubicacion);
  }

  private norm(text: string): string {
    return text.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
  }

  private slugify(value: string): string {
    return value
      .toLowerCase()
      .normalize('NFD')
      .replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '');
  }
}
