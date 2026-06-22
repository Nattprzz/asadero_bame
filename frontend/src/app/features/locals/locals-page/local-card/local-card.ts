// ─────────────────────────────────────────────────────────────────────────────
// local-card.ts — tarjetas de locales.
//
// Este componente recibe el listado de locales y se encarga de mostrar su
// información principal en formato tarjeta. También adapta el estado visual
// de cada local, el enlace de teléfono y el horario mostrado según los datos
// disponibles.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, Input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import type { Local, EstadoLocal, HoursMap } from '../../../../core/models';

@Component({
  selector: 'app-local-card',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './local-card.html',
})
export class LocalCard {
  // Listado de locales recibido desde el componente padre.
  @Input() locals: Local[] = [];

  // Relaciona cada estado del backend con el texto mostrado en la tarjeta.
  getStatusText(estado: EstadoLocal): string {
    const labels: Record<EstadoLocal, string> = {
      '1': 'ABIERTO',
      '2': 'CERRADO',
      '3': 'ABRE PRONTO',
      '4': 'CIERRA PRONTO',
      '5': 'AGOTADO',
      '6': 'NO DISPONIBLE',
    };

    return labels[estado] ?? '';
  }

  // Devuelve las clases visuales del distintivo según el estado del local.
  getStatusBadgeClass(estado: EstadoLocal): string {
    if (estado === '1') return 'bg-green-600 text-white';
    if (estado === '2') return 'bg-red-600 text-white';
    if (estado === '3') return 'bg-orange-500 text-white';
    if (estado === '4') return 'bg-yellow-500 text-black';
    if (estado === '5') return 'bg-gray-800 text-white';
    if (estado === '6') return 'bg-slate-500 text-white';

    return 'bg-gray-400 text-white';
  }

  // Indica si el local debe bloquear sus acciones principales.
  isDisabled(estado: EstadoLocal): boolean {
    return estado === '2' || estado === '5' || estado === '6';
  }

  // Genera el enlace telefónico solo cuando el local está disponible.
  phoneHref(telefono: string, estado: EstadoLocal): string | null {
    return this.isDisabled(estado) ? null : 'tel:' + telefono;
  }

  // Orden usado por JavaScript para obtener el día actual con getDay().
  private static readonly DAYS = [
    'sunday',
    'monday',
    'tuesday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
  ] as const;

  // Muestra el horario del día actual o usa datos antiguos como alternativa.
  horarioText(local: Local): string {
    const today = LocalCard.DAYS[new Date().getDay()];

    if (local.hours && Object.keys(local.hours).length > 0) {
      return this.formatTodaySlots(local.hours, today);
    }

    if (local.horarios?.length) return local.horarios[0];
    if (local.horario?.trim()) return local.horario;

    return '';
  }

  // Formatea los tramos horarios del día actual.
  private formatTodaySlots(hours: HoursMap, day: string): string {
    const slots = hours[day];

    if (!slots || slots.length === 0) return 'Cerrado hoy';

    const formatted = slots.map((slot) => `${slot.open} - ${slot.close}`).join(' · ');

    return `Hoy: ${formatted}`;
  }
}