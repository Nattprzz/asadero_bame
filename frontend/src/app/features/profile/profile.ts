import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { RouterLink, Router } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { AuthService } from '../../core/services/auth-service';
import { PedidoService } from '../../core/services/pedido.service';
import { LanguageService, type Language } from '../../core/services/language.service';
import { getUiStrings } from '../../core/i18n/ui-strings';
import { UserPrefService } from '../../core/services/user-pref-service';
import type { Pedido } from '../../core/models';

type Tab = 'cuenta' | 'pedidos' | 'favoritos' | 'ajustes';

interface OrderStep { label: string; done: boolean; }

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './profile.html',
})
export class ProfilePage implements OnInit {
  readonly auth = inject(AuthService);
  private readonly pedidoSvc = inject(PedidoService);
  readonly lang = inject(LanguageService);
  readonly userPref = inject(UserPrefService);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));
  private readonly router = inject(Router);

  readonly tab = signal<Tab>('cuenta');
  readonly editing = signal(false);
  readonly editName = signal('');
  readonly editEmail = signal('');
  readonly editPhone = signal('');

  readonly orders = signal<Pedido[]>([]);
  readonly loadingOrders = signal(true);

  readonly notifPedidos = signal(true);
  readonly notifOfertas = signal(true);
  readonly notifSms = signal(false);

  readonly initials = computed(() => {
    const w = (this.auth.user()?.name ?? '').trim().split(/\s+/).filter(Boolean);
    return ((w[0]?.[0] ?? '') + (w[1]?.[0] ?? '')).toUpperCase() || '?';
  });

  readonly points = computed(() =>
    Math.floor(this.orders().filter((o) => o.estado === 'Entregado').reduce((s, o) => s + o.total, 0)),
  );

  readonly pointsPct = computed(() => Math.min(100, Math.round((this.points() / 500) * 100)) + '%');
  readonly pointsLeft = computed(() => Math.max(0, 500 - this.points()));

  readonly activeOrder = computed<Pedido | null>(() =>
    this.orders().find((o) => o.estado === 'Pendiente' || o.estado === 'Confirmado' || o.estado === 'En preparación' || o.estado === 'Listo') ?? null,
  );

  readonly pastOrders = computed<Pedido[]>(() =>
    this.orders().filter((o) => o.estado === 'Entregado' || o.estado === 'Cancelado'),
  );

  readonly isDark = computed(() => this.userPref.effectiveTheme() === 'dark');

  readonly LANGS: { value: Language; label: string }[] = [
    { value: 'es', label: 'Español' },
    { value: 'en', label: 'English' },
    { value: 'fr', label: 'Français' },
    { value: 'it', label: 'Italiano' },
    { value: 'de', label: 'Deutsch' },
  ];

  readonly TABS = computed(() => {
    const p = this.t().profile;
    return [
      { id: 'cuenta' as Tab, label: p.tabCuenta },
      { id: 'pedidos' as Tab, label: p.tabPedidos },
      { id: 'favoritos' as Tab, label: p.tabFavoritos },
      { id: 'ajustes' as Tab, label: p.tabAjustes },
    ];
  });

  ngOnInit(): void {
    if (!this.auth.isLoggedIn()) {
      this.auth.setRedirectUrl('/home/perfil');
      this.router.navigateByUrl('/home/login');
      return;
    }
    const user = this.auth.user();
    this.editName.set(user?.name ?? '');
    this.editEmail.set(user?.email ?? '');

    this.pedidoSvc.getMisPedidos().subscribe({
      next: (list) => { this.orders.set(list); this.loadingOrders.set(false); },
      error: () => { this.loadingOrders.set(false); },
    });
  }

  setTab(t: Tab): void { this.tab.set(t); }

  toggleEdit(): void {
    if (this.editing()) {
      this.editing.set(false);
    } else {
      this.editName.set(this.auth.user()?.name ?? '');
      this.editEmail.set(this.auth.user()?.email ?? '');
      this.editing.set(true);
    }
  }

  saveEdit(): void { this.editing.set(false); }

  toggleTheme(): void { this.userPref.setTheme(this.isDark() ? 'light' : 'dark'); }

  setLang(event: Event): void {
    this.lang.setLang((event.target as HTMLSelectElement).value as Language);
  }

  logout(): void { this.auth.logout(); }

  moneyStr(n: number): string { return '€' + n.toFixed(2).replace('.', ','); }

  orderRef(o: Pedido): string { return 'BME-' + String(o.reserva ?? o.id).padStart(4, '0'); }

  orderItems(o: Pedido): string {
    return o.productos.map((p) => p.nombre + (p.cantidad > 1 ? ` ×${p.cantidad}` : '')).join(' · ');
  }

  orderSteps(o: Pedido): OrderStep[] {
    const idx = { 'Pendiente': 0, 'Confirmado': 1, 'En preparación': 1, 'Listo': 2, 'Entregado': 3, 'Cancelado': -1 }[o.estado] ?? 0;
    const p = this.t().profile;
    return [p.stepReceived, p.stepPreparing, p.stepReady].map((label, i) => ({ label, done: i <= idx }));
  }

  private readonly LOCALE_MAP: Record<Language, string> = {
    es: 'es-ES', en: 'en-GB', fr: 'fr-FR', it: 'it-IT', de: 'de-DE',
  };

  formatDate(fecha: string): string {
    if (!fecha) return '';
    const locale = this.LOCALE_MAP[this.lang.currentLang()] ?? 'es-ES';
    try { return new Date(fecha).toLocaleDateString(locale, { day: 'numeric', month: 'short', year: 'numeric' }); }
    catch { return fecha; }
  }

  statusColor(o: Pedido): string {
    if (o.estado === 'Entregado') return 'var(--green)';
    if (o.estado === 'Cancelado') return 'var(--red)';
    return 'var(--orange)';
  }
}
