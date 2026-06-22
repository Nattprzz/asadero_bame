// ─────────────────────────────────────────────────────────────────────────────
// local-admin.ts — panel de administración de un local.
//
// Este componente muestra la vista de gestión de un local concreto. Obtiene el
// identificador desde la ruta, carga la información básica del local mediante
// el servicio correspondiente y prepara accesos a las secciones de gestión.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, inject, OnInit } from '@angular/core';
import { RouterLink, ActivatedRoute } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { AuthService } from '../../../core/services/auth-service';
import { LocalService } from '../../../core/services/local.service';

@Component({
  selector: 'app-local-admin',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './local-admin.html',
})
export class LocalAdmin implements OnInit {
  readonly auth = inject(AuthService);

  // Servicios utilizados para leer la ruta y cargar los datos del local.
  private readonly route = inject(ActivatedRoute);
  private readonly localService = inject(LocalService);

  // Datos básicos del local seleccionado.
  id = 0;
  localName = 'Local';

  // Obtiene el identificador de la ruta y carga el nombre del local.
  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id') ?? '0');

    if (this.id) {
      this.localService.getLocal(this.id).subscribe({
        next: (local) => {
          if (local) {
            this.localName = local.nombre;
          }
        },
        error: () => {
          this.localName = 'Local no encontrado';
        },
      });
    }
  }

  // Contador preparado para mostrar pedidos pendientes del local.
  getContadorPendientes(): number {
    return 0;
  }

  // Contador preparado para mostrar productos agotados del local.
  getContadorAgotados(): number {
    return 0;
  }
}