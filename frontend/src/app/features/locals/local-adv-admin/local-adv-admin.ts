import { Component, inject, signal, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import {
  AbstractControl,
  FormBuilder,
  ReactiveFormsModule,
  type FormGroup,
  Validators,
} from '@angular/forms';
import { TablerIconComponent } from '@tabler/icons-angular';
import { forkJoin } from 'rxjs';
import { LocalService } from '@core/services/local.service';
import { ProductService } from '@core/services/product.service';
import type { HoursMap, Local, ProductosPorCategoria } from '@core/models';

interface Product {
  id: number;
  name: string;
  defaultPrice: number;
  isSoldHere: boolean;
  localPrice: number | null;
}

interface Notification {
  type: 'success' | 'error';
  text: string;
}

@Component({
  selector: 'app-local-adv-admin',
  standalone: true,
  imports: [ReactiveFormsModule, TablerIconComponent],
  templateUrl: './local-adv-admin.html',
})
export class LocalAdvAdmin implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly route = inject(ActivatedRoute);
  private readonly localService = inject(LocalService);
  private readonly productService = inject(ProductService);

  id = 0;
  isLoading = true;
  loadError: string | null = null;
  notification = signal<Notification | null>(null);

  readonly weekDays = [
    { key: 'monday', label: 'Lun', full: 'Lunes' },
    { key: 'tuesday', label: 'Mar', full: 'Martes' },
    { key: 'wednesday', label: 'Mie', full: 'Miércoles' },
    { key: 'thursday', label: 'Jue', full: 'Jueves' },
    { key: 'friday', label: 'Vie', full: 'Viernes' },
    { key: 'saturday', label: 'Sáb', full: 'Sábado' },
    { key: 'sunday', label: 'Dom', full: 'Domingo' },
  ];

  readonly localForm: FormGroup = this.fb.group({
    storeName: ['', Validators.required],
    openingTime: ['', Validators.required],
    closingTime: ['', Validators.required],
    reservationOpeningTime: ['', Validators.required],
    reservationClosingTime: [
      '',
      [Validators.required, this.reservationTimeValidator.bind(this)],
    ],
    coordinates: [''],
    address: ['', Validators.required],
    phone: ['', [Validators.required, Validators.pattern(/^\d{9}$/)]],
    monday: [false],
    tuesday: [false],
    wednesday: [false],
    thursday: [false],
    friday: [false],
    saturday: [false],
    sunday: [false],
  });

  readonly products = signal<Product[]>([]);

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id') ?? '0');
    this.loadData();
  }

  private loadData(): void {
    this.isLoading = true;
    this.loadError = null;

    forkJoin({
      local: this.localService.getLocal(this.id),
      groups: this.productService.getProductsGroupedByCategory(),
    }).subscribe({
      next: ({ local, groups }) => {
        if (local) this.applyLocal(local);
        this.applyProducts(groups);
        this.isLoading = false;
      },
      error: () => {
        this.loadError = 'No se pudieron cargar los datos del local.';
        this.isLoading = false;
      },
    });
  }

  private applyLocal(local: Local): void {
    const hours = local.hours ?? {};
    const { openTime, closeTime, openDays } = this.parseHours(hours);
    const coords =
      local.latitud != null && local.longitud != null
        ? `${local.latitud}, ${local.longitud}`
        : '';
    const phone = (local.telefono ?? '').replace(/\D/g, '').slice(-9);
    const dayValues = Object.fromEntries(
      this.weekDays.map((d) => [d.key, openDays.includes(d.key)]),
    );

    this.localForm.patchValue({
      storeName: local.nombre,
      openingTime: openTime,
      closingTime: closeTime,
      reservationOpeningTime: openTime,
      reservationClosingTime: closeTime,
      coordinates: coords,
      address: local.ubicacion ?? '',
      phone,
      ...dayValues,
    });
  }

  private parseHours(hours: HoursMap): {
    openTime: string;
    closeTime: string;
    openDays: string[];
  } {
    const openDays: string[] = [];
    let openTime = '';
    let closeTime = '';
    let found = false;

    for (const [day, slots] of Object.entries(hours)) {
      if (slots && slots.length > 0) {
        openDays.push(day);
        if (!found) {
          openTime = slots[0].open;
          closeTime = slots[slots.length - 1].close;
          found = true;
        }
      }
    }

    return { openTime, closeTime, openDays };
  }

  private applyProducts(groups: ProductosPorCategoria[]): void {
    this.products.set(
      groups.flatMap((grupo) =>
        grupo.productos.map((p) => ({
          id: p.id,
          name: p.nombre,
          defaultPrice: p.precio,
          isSoldHere: true,
          localPrice: p.precio,
        })),
      ),
    );
  }

  retryLoad(): void {
    this.loadData();
  }

  reservationTimeValidator(control: AbstractControl): { afterClosingTime: boolean } | null {
    if (!control.parent) return null;
    const reservationTime = control.value;
    const closingTime = control.parent.get('closingTime')?.value;
    if (closingTime && reservationTime > closingTime) {
      return { afterClosingTime: true };
    }
    return null;
  }

  updateReservationValidation(): void {
    this.localForm.get('reservationClosingTime')?.updateValueAndValidity();
  }

  toggleProduct(product: Product, event: Event): void {
    const isChecked = (event.target as HTMLInputElement).checked;
    product.isSoldHere = isChecked;
    if (isChecked && product.localPrice === null) {
      product.localPrice = product.defaultPrice;
    } else if (!isChecked) {
      product.localPrice = null;
    }
    this.products.update((current) => [...current]);
  }

  updatePrice(product: Product, event: Event): void {
    const inputValue = (event.target as HTMLInputElement).value;
    product.localPrice = inputValue ? parseFloat(inputValue) : null;
    this.products.update((current) => [...current]);
  }

  showNotification(type: 'success' | 'error', text: string): void {
    this.notification.set({ type, text });
    setTimeout(() => {
      this.notification.set(null);
    }, 3000);
  }

  getFieldError(fieldName: string): string {
    const field = this.localForm.get(fieldName);
    if (!field || !field.touched || !field.invalid) return '';
    if (field.hasError('required')) return 'Este campo es requerido';
    if (field.hasError('pattern')) return 'Formato de teléfono inválido';
    if (field.hasError('afterClosingTime')) {
      return 'No puede ser posterior a la hora de cierre del local';
    }
    return '';
  }

  hasFieldError(fieldName: string): boolean {
    const field = this.localForm.get(fieldName);
    return !!(field && field.touched && field.invalid);
  }

  hasAnyDaySelected(): boolean {
    return this.weekDays.some((day) => this.localForm.get(day.key)?.value === true);
  }

  toggleDay(dayKey: string, event: Event): void {
    const isChecked = (event.target as HTMLInputElement).checked;
    this.localForm.get(dayKey)?.setValue(isChecked);
  }

  saveChanges(): void {
    this.localForm.markAllAsTouched();

    if (!this.hasAnyDaySelected()) {
      this.showNotification('error', 'Selecciona al menos un día de apertura');
      return;
    }

    if (this.localForm.invalid) {
      this.showNotification('error', 'Por favor, corrige los errores en el formulario');
      return;
    }

    const formValue = this.localForm.value;
    const selectedDays = this.weekDays
      .filter((day) => this.localForm.get(day.key)?.value === true)
      .map((day) => day.key);

    const payload = {
      storeName: formValue.storeName,
      openingTime: formValue.openingTime,
      closingTime: formValue.closingTime,
      reservationOpeningTime: formValue.reservationOpeningTime,
      reservationClosingTime: formValue.reservationClosingTime,
      coordinates: formValue.coordinates,
      address: formValue.address,
      phone: formValue.phone,
      openingDays: selectedDays,
      productsConfig: this.products().map((product) => ({
        productId: product.id,
        isSoldHere: product.isSoldHere,
        localPrice: product.localPrice,
      })),
    };

    // TODO: enviar payload al endpoint /api/v1/admin/local/${this.id}
    console.info('[LocalAdvAdmin] Payload listo para backend:', payload);
    this.showNotification('success', 'Cambios guardados correctamente');
  }
}
