import { Component } from '@angular/core';
import { ErrorCard } from '../../shared/components/error-card/error-card';

@Component({
  selector: 'app-page401',
  standalone: true,
  imports: [ErrorCard],
  template: `<app-error-card type="401" />`,
  host: { class: 'block' },
})
export class Page401 {}
