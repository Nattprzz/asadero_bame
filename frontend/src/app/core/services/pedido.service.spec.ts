import { TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { environment } from '../../../environments/environment';
import { PedidoService } from './pedido.service';
import type { CrearPedidoDto } from '../models';

describe('PedidoService', () => {
  let service: PedidoService;
  let http: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });

    service = TestBed.inject(PedidoService);
    http = TestBed.inject(HttpTestingController);
  });

  afterEach(() => http.verify());

  it('envía type y lines con los nombres exigidos por la API', () => {
    const payload: CrearPedidoDto = {
      localId: 2,
      type: 'takeaway',
      paymentMethod: 'pay_at_store',
      lines: [
        { productId: 1, quantity: 2 },
        { productId: 5, quantity: 1 },
      ],
    };

    service.crearPedido(payload).subscribe();

    const request = http.expectOne(
      `${environment.apiUrl.replace(/\/$/, '')}/api/v1/pedidos`,
    );
    expect(request.request.method).toBe('POST');
    expect(request.request.body).toEqual(payload);
    expect(request.request.body.type).toBe('takeaway');
    expect(request.request.body.lines).toEqual(payload.lines);

    request.flush({ success: true, data: {} });
  });
});
