import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { LocalService } from '../../core/services/local.service';
import type { Local } from '../../core/models';

@Component({
  selector: 'app-admin',
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './admin.html',
  styles: ``,
})
export class Admin implements OnInit {
  private readonly localService = inject(LocalService);

  readonly locals = signal<Local[]>([]);
  readonly loading = signal(true);
  readonly error = signal<string | null>(null);

  ngOnInit(): void {
    this.localService.getLocales().subscribe({
      next: (data) => {
        this.locals.set(data);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('No se pudieron cargar los locales.');
        this.loading.set(false);
      },
    });
  }
}
