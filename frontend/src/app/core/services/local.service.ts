import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, type Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import type { EstadoLocal, HoraTramo, HoursMap, Local } from '../models';

interface BackendLocal {
  id: number;
  name: string;
  address: string | null;
  city: string | null;
  postalCode: string | null;
  phone: string | null;
  email: string | null;
  latitude: number | null;
  longitude: number | null;
  hours: Record<string, { open: string; close: string }[]> | null;
  reservationHours: Record<string, { open: string; close: string }[]> | null;
  active: boolean;
  status: string | null;
  whatsapp: string | null;
}

interface ApiResponse<T> {
  success: boolean;
  data: T;
}

export interface UpdateLocalDto {
  name: string;
  address: string;
  phone: string;
  active: boolean;
  status: 'open' | 'closed';
  hours: HoursMap;
  reservationHours: HoursMap;
}

@Injectable({ providedIn: 'root' })
export class LocalService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl.replace(/\/$/, '')}/api/v1`;

  getLocales(): Observable<Local[]> {
    return this.http
      .get<ApiResponse<BackendLocal[]>>(`${this.base}/locales`)
      .pipe(map((res) => res.data.map((row) => this.mapLocal(row))));
  }

  getLocal(id: number): Observable<Local | undefined> {
    return this.http
      .get<ApiResponse<BackendLocal>>(`${this.base}/locales/${id}`)
      .pipe(map((res) => this.mapLocal(res.data)));
  }

  getLocalByName(nombre: string): Observable<Local | undefined> {
    return this.getLocales().pipe(
      map((locals) =>
        locals.find((local) => this.slug(local.nombre) === this.slug(nombre)),
      ),
    );
  }

  updateLocal(id: number, dto: UpdateLocalDto): Observable<Local> {
    return this.http
      .patch<ApiResponse<BackendLocal>>(`${this.base}/admin/locales/${id}`, dto)
      .pipe(map((res) => this.mapLocal(res.data)));
  }

  private mapLocal(row: BackendLocal): Local {
    const addressParts = [row.address, row.city, row.postalCode].filter(Boolean);
    return {
      id: row.id,
      nombre: row.name,
      ubicacion: addressParts.join(', '),
      horario: '',
      hours: this.normalizeHours(row.hours),
      reservationHours: this.normalizeHours(row.reservationHours),
      foto: '/img/locals.jpg',
      telefono: row.phone ?? '',
      estado: this.mapStatus(row.status),
      email: row.email,
      whatsapp: row.whatsapp,
      latitud: row.latitude,
      longitud: row.longitude,
    };
  }

  private normalizeHours(raw: BackendLocal['hours']): HoursMap {
    if (!raw || typeof raw !== 'object') return {};
    const result: HoursMap = {};
    for (const [day, slots] of Object.entries(raw)) {
      if (Array.isArray(slots)) {
        result[day] = slots.filter((s) => s.open && s.close) as HoraTramo[];
      }
    }
    return result;
  }

  private mapStatus(status: string | null): EstadoLocal {
    const s = (status ?? '').toLowerCase();
    if (s === 'closed') return '2';
    if (s === 'closing_soon') return '4';
    if (s === 'temporarily_closed') return '6';
    return '1';
  }

  private slug(value: string): string {
    return value
      .toLowerCase()
      .normalize('NFD')
      .replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '');
  }
}
