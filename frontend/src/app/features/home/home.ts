import {
  Component,
  ElementRef,
  OnDestroy,
  OnInit,
  PLATFORM_ID,
  ViewChild,
  computed,
  inject,
  signal,
} from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { Meta, Title } from '@angular/platform-browser';
import { forkJoin } from 'rxjs';
import { LocalService } from '../../core/services/local.service';
import { ProductService } from '../../core/services/product.service';
import { LanguageService } from '../../core/services/language.service';
import { getUiStrings } from '../../core/i18n/ui-strings';
import { localizeText } from '../../core/i18n/localize';
import type { HoursMap, ProductosPorCategoria } from '../../core/models';

interface HomeCat {
  label: string;
  img: string;
  count: number;
}

interface HomePopular {
  id: number;
  name: string;
  ini: string;
  img: string;
  desc: string;
  priceStr: string;
}

interface HomeLocal {
  id: number;
  name: string;
  addr: string;
  phone: string;
  telHref: string;
  reservaPath: string;
  hours: HoursMap;
  latitude: number;
  longitude: number;
}

interface HourRow {
  d: string;
  t: string;
}

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './home.html',
})
export class Home implements OnInit, OnDestroy {
  private readonly meta = inject(Meta);
  private readonly title = inject(Title);
  private readonly lang = inject(LanguageService);
  private readonly localService = inject(LocalService);
  private readonly productService = inject(ProductService);
  private readonly platformId = inject(PLATFORM_ID);

  private leaflet?: typeof import('leaflet');
  private map?: import('leaflet').Map;
  private marker?: import('leaflet').Marker;
  private mapElement?: HTMLElement;
  private destroyed = false;

  @ViewChild('localMap')
  set localMapContainer(element: ElementRef<HTMLElement> | undefined) {
    if (!element || !isPlatformBrowser(this.platformId)) return;
    this.mapElement = element.nativeElement;
    void this.initializeMap();
  }

  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));

  readonly cats = signal<HomeCat[]>([]);
  readonly populars = signal<HomePopular[]>([]);
  readonly locals = signal<HomeLocal[]>([]);
  readonly selectedIdx = signal(0);

  readonly selectedLocal = computed<HomeLocal | null>(() => this.locals()[this.selectedIdx()] ?? null);

  readonly selectedHours = computed<HourRow[]>(() => {
    const l = this.selectedLocal();
    if (!l?.hours) return [];
    return this.formatHours(l.hours);
  });

  readonly steps = computed(() => [
    { n: '1', title: this.t().home.homeStep1Title, body: this.t().home.homeStep1Desc },
    { n: '2', title: this.t().home.homeStep2Title, body: this.t().home.homeStep2Desc },
    { n: '3', title: this.t().home.homeStep3Title, body: this.t().home.homeStep3Desc },
  ]);

  private readonly DAY_ORDER = [
    'monday','mon','lunes','tuesday','tue','martes',
    'wednesday','wed','miercoles','miércoles','thursday','thu','jueves',
    'friday','fri','viernes','saturday','sat','sabado','sábado','sunday','sun','domingo',
  ];

  private dayLabel(day: string): string {
    const days = this.t().days;
    const key = day.toLowerCase() as keyof typeof days;
    return days[key] ?? day;
  }

  ngOnInit(): void {
    this.title.setTitle('Inicio | Bame — Reserva tu pollo asado online');
    this.meta.updateTag({ name: 'description', content: 'Bame es la plataforma de reservas online para asaderos de pollo tradicionales. Elige tu asadero, consulta la carta y reserva sin colas.' });
    this.meta.updateTag({ name: 'robots', content: 'index, follow' });
    this.meta.updateTag({ property: 'og:type', content: 'website' });
    this.meta.updateTag({ property: 'og:title', content: 'Bame — Reserva tu pollo asado online' });
    this.meta.updateTag({ property: 'og:description', content: 'Reserva online en los mejores asaderos de pollo tradicionales. Carta completa, precios actualizados y recogida sin esperas.' });
    this.meta.updateTag({ property: 'og:image', content: '/branding/og-image.jpg' });
    this.meta.updateTag({ property: 'og:locale', content: 'es_ES' });
    this.meta.updateTag({ name: 'twitter:card', content: 'summary_large_image' });
    this.meta.updateTag({ name: 'twitter:title', content: 'Bame — Reserva tu pollo asado online' });
    this.meta.updateTag({ name: 'twitter:description', content: 'Reserva online en los mejores asaderos de pollo tradicionales. Sin colas, sin esperas.' });
    this.meta.updateTag({ name: 'twitter:image', content: '/branding/og-image.jpg' });

    forkJoin({
      groups: this.productService.getProductsGroupedByCategory(),
      locals: this.localService.getLocales(),
    }).subscribe({
      next: ({ groups, locals }) => {
        this.cats.set(this.mapCats(groups));
        this.populars.set(this.mapPopulars(groups));
        this.locals.set(locals.map((l) => {
          const coordinates = this.localCoordinates(l.nombre, l.latitud, l.longitud);
          return {
            id: l.id,
            name: l.nombre,
            addr: l.ubicacion,
            phone: l.telefono,
            telHref: 'tel:+34' + l.telefono.replace(/\s/g, ''),
            reservaPath: '/home/locales/' + this.slugify(l.nombre) + '/reserva',
            hours: l.hours ?? {},
            latitude: coordinates.latitude,
            longitude: coordinates.longitude,
          };
        }));
      },
    });
  }

  ngOnDestroy(): void {
    this.destroyed = true;
    this.map?.remove();
    this.map = undefined;
    this.marker = undefined;
    this.mapElement = undefined;
  }

  setSelected(i: number): void {
    this.selectedIdx.set(i);
    const local = this.locals()[i];
    if (local) this.updateMap(local);
  }

  private async initializeMap(): Promise<void> {
    if (this.destroyed || this.map || !this.mapElement || !isPlatformBrowser(this.platformId)) return;

    const local = this.selectedLocal();
    if (!local) return;

    const L = await import('leaflet');
    if (this.destroyed || this.map || !this.mapElement) return;

    this.leaflet = L;
    const coordinates: import('leaflet').LatLngExpression = [local.latitude, local.longitude];
    this.map = L.map(this.mapElement, {
      zoomControl: true,
      attributionControl: true,
    }).setView(coordinates, 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors',
    }).addTo(this.map);

    this.marker = L.marker(coordinates, {
      icon: L.divIcon({
        className: 'bame-map-marker',
        html: '<span aria-hidden="true"></span>',
        iconSize: [34, 44],
        iconAnchor: [17, 44],
        popupAnchor: [0, -38],
      }),
    }).addTo(this.map);

    this.updateMap(local, false);
    requestAnimationFrame(() => this.map?.invalidateSize());
  }

  private updateMap(local: HomeLocal, animate = true): void {
    if (!this.map || !this.marker || !this.leaflet) return;

    const coordinates: import('leaflet').LatLngExpression = [local.latitude, local.longitude];
    this.map.setView(coordinates, 14, { animate });
    this.marker.setLatLng(coordinates).bindPopup(this.popupContent(local)).openPopup();
    requestAnimationFrame(() => this.map?.invalidateSize());
  }

  private popupContent(local: HomeLocal): HTMLElement {
    const content = document.createElement('div');
    content.className = 'bame-map-popup';
    const name = document.createElement('strong');
    name.textContent = local.name;
    const address = document.createElement('span');
    address.textContent = local.addr;
    content.append(name, address);
    return content;
  }

  private localCoordinates(
    name: string,
    latitude: number | null | undefined,
    longitude: number | null | undefined,
  ): { latitude: number; longitude: number } {
    if (latitude != null && longitude != null && Number.isFinite(latitude) && Number.isFinite(longitude)) {
      return { latitude, longitude };
    }

    const normalized = name.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const fallbacks = [
      { match: 'alcantarilla', latitude: 37.9694, longitude: -1.2171 },
      { match: 'esparragal', latitude: 38.0475, longitude: -1.0438 },
      { match: 'fortuna', latitude: 38.1814, longitude: -1.1259 },
      { match: 'puerto de mazarron', latitude: 37.5657, longitude: -1.2650 },
      { match: 'ronda norte', latitude: 37.9996, longitude: -1.1372 },
    ];
    const fallback = fallbacks.find(({ match }) => normalized.includes(match));

    return fallback ?? { latitude: 37.9922, longitude: -1.1307 };
  }

  private mapCats(groups: ProductosPorCategoria[]): HomeCat[] {
    return groups.map((g) => ({
      label: localizeText(g.categoria.nombre, { en: g.categoria.nombreEn, fr: g.categoria.nombreFr, it: g.categoria.nombreIt, de: g.categoria.nombreDe }, this.lang.currentLang()),
      img: this.categoryImage(g.categoria.slug ?? ''),
      count: g.productos.length,
    }));
  }

  private categoryImage(slug: string): string {
    const images: Record<string, string> = {
      'chicken-sides': 'chickens.jpg',
      croquettes: 'croquettes.jpg',
      'hot-dishes': 'hot.jpg',
      'cold-dishes': 'cold.jpg',
      'murcian-specialties': 'murcian.jpg',
      sauces: 'sauces.jpg',
      bread: 'breads.jpg',
      desserts: 'desserts.jpg',
      drinks: 'drinks.jpg',
    };

    return `/img/${images[slug] ?? 'chickens.jpg'}`;
  }

  private mapPopulars(groups: ProductosPorCategoria[]): HomePopular[] {
    return groups
      .flatMap((g) => g.productos)
      .filter((p) => p.destacado)
      .slice(0, 6)
      .map((p) => ({
        id: p.id,
        name: localizeText(p.nombre, { en: p.nombreEn, fr: p.nombreFr, it: p.nombreIt, de: p.nombreDe }, this.lang.currentLang()),
        ini: this.initials(p.nombre),
        img: p.foto ?? '',
        desc: localizeText(p.descripcion, { en: p.descripcionEn, fr: p.descripcionFr, it: p.descripcionIt, de: p.descripcionDe }, this.lang.currentLang()),
        priceStr: '€' + p.precio.toFixed(2).replace('.', ','),
      }));
  }

  private initials(name: string): string {
    const words = name.trim().split(/\s+/).filter((w) => w.length > 1);
    return ((words[0]?.[0] ?? '') + (words[1]?.[0] ?? '')).toUpperCase();
  }

  private slugify(value: string): string {
    return value
      .toLowerCase()
      .normalize('NFD')
      .replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '');
  }

  private formatHours(hours: HoursMap): HourRow[] {
    const entries = Object.entries(hours)
      .filter(([, slots]) => slots && slots.length > 0)
      .sort(([a], [b]) => {
        const ai = this.DAY_ORDER.indexOf(a.toLowerCase());
        const bi = this.DAY_ORDER.indexOf(b.toLowerCase());
        return (ai === -1 ? 99 : ai) - (bi === -1 ? 99 : bi);
      });

    return entries.map(([day, slots]) => ({
      d: this.dayLabel(day),
      t: slots.map((s) => s.open + ' – ' + s.close).join(' · '),
    }));
  }
}
