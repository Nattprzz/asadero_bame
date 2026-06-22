import { Component, computed, inject, input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { LanguageService } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';

export type ErrorType = '401' | '403' | '404' | '500' | '503';

@Component({
  selector: 'app-error-card',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './error-card.html',
  styleUrl: './error-card.css',
})
export class ErrorCard {
  private readonly lang = inject(LanguageService);

  readonly type = input.required<ErrorType>();

  readonly t = computed(() => getUiStrings(this.lang.currentLang()).errors);
  readonly authT = computed(() => getUiStrings(this.lang.currentLang()).auth);

  readonly title = computed(() => {
    const e = this.t();
    switch (this.type()) {
      case '401': return e.unauthorized;
      case '403': return e.forbidden;
      case '404': return e.notFound;
      case '500': return e.server;
      case '503': return e.service;
    }
  });

  readonly desc = computed(() => {
    const e = this.t();
    switch (this.type()) {
      case '401': return e.unauthorizedDesc;
      case '403': return e.forbiddenDesc;
      case '404': return e.notFoundDesc;
      case '500': return e.serverDesc;
      case '503': return e.serviceDesc;
    }
  });

  readonly icon = computed(() => {
    switch (this.type()) {
      case '401': return 'login';
      case '403': return 'shield-check';
      case '404': return 'search-off';
      case '500': return 'alert-triangle';
      case '503': return 'rotate-clockwise';
    }
  });

  goBack(): void {
    history.back();
  }

  reload(): void {
    window.location.reload();
  }
}
