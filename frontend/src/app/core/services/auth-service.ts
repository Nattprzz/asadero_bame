import { Injectable, signal, computed, inject, isDevMode, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { tap, type Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import type { LoginDto, RegisterDto, AuthResponse, UsuarioAutenticado } from '../models/auth.model';

export type UserRole = 'anonymous' | 'customer' | 'store' | 'responsable' | 'manager' | 'admin';

export const ROUTE_ROLES = {
  ANONYMOUS: 'anonymous' as UserRole,
  USER: 'customer' as UserRole,
  STORE: 'store' as UserRole,
  RESPONSABLE: 'responsable' as UserRole,
  MANAGER: 'manager' as UserRole,
  ADMIN: 'admin' as UserRole,
} as const;

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  localId?: number;
}

const ROLE_HIERARCHY: UserRole[] = ['anonymous', 'customer', 'store', 'responsable', 'manager', 'admin'];

export function resolveRouteRole(role: string): UserRole | undefined {
  return (ROUTE_ROLES as Record<string, UserRole>)[role];
}

const TOKEN_KEY = 'bame_token';
const USER_KEY = 'bame_user';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly apiBase = environment.apiUrl.replace(/\/$/, '');
  private readonly router = inject(Router);
  private readonly http = inject(HttpClient);
  private readonly platformId = inject(PLATFORM_ID);

  private readonly userSignal = signal<User | null>(this.loadStoredUser());
  private readonly tokenSignal = signal<string | null>(this.loadStoredToken());

  readonly user = this.userSignal.asReadonly();
  readonly token = this.tokenSignal.asReadonly();
  readonly isLoggedIn = computed(() => this.userSignal() !== null);
  readonly currentRole = computed<UserRole>(() => this.userSignal()?.role ?? 'anonymous');
  readonly currentLocalId = computed(() => this.userSignal()?.localId ?? null);

  private redirectUrl: string | null = null;

  setRedirectUrl(url: string): void {
    this.redirectUrl = url;
  }

  consumeRedirectUrl(): string | null {
    const url = this.redirectUrl;
    this.redirectUrl = null;
    return url;
  }

  hasRole(role: UserRole): boolean {
    return this.currentRole() === role;
  }

  hasRoleOrAbove(minRole: UserRole): boolean {
    return ROLE_HIERARCHY.indexOf(this.currentRole()) >= ROLE_HIERARCHY.indexOf(minRole);
  }

  canAccessLocal(localId: number): boolean {
    const user = this.userSignal();
    if (!user) return false;
    if (user.role === 'admin') return true;
    return (user.role === 'store' || user.role === 'responsable' || user.role === 'manager') && user.localId === localId;
  }

  canAccessAdvanced(): boolean {
    return this.hasRoleOrAbove('manager');
  }

  canAccessAdmin(): boolean {
    return this.hasRole('admin');
  }

  getRedirectUrl(role: UserRole): string {
    switch (role) {
      case 'admin':
        return '/admin';
      case 'manager': {
        const localId = this.currentLocalId();
        return localId != null ? `/admin/local/${localId}/gerente` : '/home';
      }
      case 'store': {
        const localId = this.currentLocalId();
        return localId != null ? `/admin/local/${localId}` : '/home';
      }
      case 'responsable': {
        const localId = this.currentLocalId();
        return localId != null ? `/admin/local/${localId}/resumen-dia` : '/home';
      }
      case 'customer':
        return this.consumeRedirectUrl() ?? '/home';
      default:
        return '/home';
    }
  }

  navigateAfterLogin(): void {
    this.router.navigateByUrl(this.getRedirectUrl(this.currentRole()));
  }

  login(dto: LoginDto): Observable<AuthResponse> {
    return this.http
      .post<AuthResponse>(`${this.apiBase}/api/v1/auth/login`, dto)
      .pipe(tap((res) => this.saveSession(res)));
  }

  register(dto: RegisterDto): Observable<AuthResponse> {
    return this.http
      .post<AuthResponse>(`${this.apiBase}/api/v1/auth/register`, dto)
      .pipe(tap((res) => this.saveSession(res)));
  }

  requestPasswordReset(email: string): Observable<void> {
    return this.http.post<void>(`${this.apiBase}/api/v1/auth/forgot-password`, { email });
  }

  logout(): void {
    this.clearSession();
    if (isDevMode()) console.log('[Auth] Logged out');
    this.router.navigateByUrl('/home');
  }

  clearSession(): void {
    if (isPlatformBrowser(this.platformId)) {
      localStorage.removeItem(TOKEN_KEY);
      localStorage.removeItem(USER_KEY);
    }
    this.tokenSignal.set(null);
    this.userSignal.set(null);
  }

  private saveSession(res: AuthResponse): void {
    const user = this.mapUser(res.data.user);
    const token = res.data.token;

    if (isPlatformBrowser(this.platformId)) {
      localStorage.setItem(TOKEN_KEY, token);
      localStorage.setItem(USER_KEY, JSON.stringify(user));
    }

    this.tokenSignal.set(token);
    this.userSignal.set(user);

    if (isDevMode()) console.log('[Auth] Session saved:', user.role, 'localId:', user.localId);
  }

  private loadStoredToken(): string | null {
    if (!isPlatformBrowser(this.platformId)) return null;
    try {
      return localStorage.getItem(TOKEN_KEY);
    } catch {
      return null;
    }
  }

  private loadStoredUser(): User | null {
    if (!isPlatformBrowser(this.platformId)) return null;
    try {
      const stored = localStorage.getItem(USER_KEY);
      return stored ? (JSON.parse(stored) as User) : null;
    } catch {
      return null;
    }
  }

  private mapUser(apiUser: UsuarioAutenticado): User {
    return {
      id: apiUser.id,
      name: apiUser.name,
      email: apiUser.email,
      role: this.mapRole(apiUser.roles),
      localId: apiUser.localId ?? undefined,
    };
  }

  private mapRole(roles: string[]): UserRole {
    if (!Array.isArray(roles)) return 'customer';
    if (roles.includes('ROLE_ADMIN')) return 'admin';
    if (roles.includes('ROLE_MANAGER') || roles.includes('ROLE_GERENTE')) return 'manager';
    if (roles.includes('ROLE_RESPONSABLE')) return 'responsable';
    if (roles.includes('ROLE_STORE')) return 'store';
    return 'customer';
  }
}
