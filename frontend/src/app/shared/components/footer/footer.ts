// ─────────────────────────────────────────────────────────────────────────────
// footer.ts — pie de página de la aplicación.
//
// Este componente muestra la información fija situada al final de todas las
// páginas de la aplicación, incluyendo enlaces útiles, información legal y
// el año actual mostrado de forma automática.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, computed, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { LanguageService } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './footer.html',
})
export class Footer {
  private readonly lang = inject(LanguageService);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));
  readonly currentYear = new Date().getFullYear();
}