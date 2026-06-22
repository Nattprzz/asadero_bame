import { Component, computed, inject, OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { BreadcrumbBar } from '../../../shared/components/breadcrumb-bar/breadcrumb-bar';
import { CartService } from '../../../core/services/cart.service';
import { LanguageService } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';

@Component({
  selector: 'app-payment-success',
  standalone: true,
  imports: [RouterLink, TablerIconComponent, BreadcrumbBar],
  templateUrl: './payment-success.html',
})
export class PaymentSuccessPage implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly cartService = inject(CartService);
  private readonly lang = inject(LanguageService);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));

  localId = '';
  // true cuando el cliente eligió "Pagar en el local" (query param ?via=local)
  viaLocal = false;

  ngOnInit(): void {
    this.localId = this.route.snapshot.paramMap.get('id') ?? '';
    this.viaLocal = this.route.snapshot.queryParamMap.get('via') === 'local';
    this.cartService.clear();
  }
}
