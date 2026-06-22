import { Component } from '@angular/core';
import { ErrorCard } from '../../shared/components/error-card/error-card';

@Component({
  selector: 'app-page500',
  standalone: true,
  imports: [ErrorCard],
  template: `<app-error-card type="500" />`,
  host: { class: 'block' },
})
export class Page500 {}
