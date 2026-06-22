// ─────────────────────────────────────────────────────────────────────────────
// legal-page.ts — página de textos legales.
//
// Este componente muestra las distintas secciones legales de la aplicación:
// términos y condiciones, política de privacidad y política de cookies.
// El contenido se define en el propio componente para poder pintarlo de forma
// dinámica en la plantilla y cambiar de sección según la ruta activa.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, computed, DestroyRef, inject, signal, type OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { TablerIconComponent } from '@tabler/icons-angular';
import { BreadcrumbBar } from '../../../shared/components/breadcrumb-bar/breadcrumb-bar';

// Bloque individual de contenido dentro de una sección legal.
interface LegalContentBlock {
  id: string;
  title: string;
  text: string;
}

// Sección legal completa accesible desde el menú lateral.
interface LegalSection {
  slug: string;
  label: string;
  content: LegalContentBlock[];
}

@Component({
  selector: 'app-legal-page',
  standalone: true,
  imports: [RouterLink, TablerIconComponent, BreadcrumbBar],
  templateUrl: './legal-page.html',
})
export class LegalPage implements OnInit {
  // Servicios utilizados para leer la ruta activa y limpiar suscripciones.
  private readonly route = inject(ActivatedRoute);
  private readonly destroyRef = inject(DestroyRef);

  // Textos legales mostrados en la página.
  readonly sections: LegalSection[] = [
    {
      slug: 'terminos-y-condiciones',
      label: 'Términos y Condiciones',
      content: [
        {
          id: 'objeto-y-alcance',
          title: 'Objeto y alcance',
          text:
            'Los presentes Términos y Condiciones regulan el acceso y uso del sitio web de Bame, así como los servicios de consulta de carta, reservas y pedidos ofrecidos a través de la plataforma.',
        },
        {
          id: 'uso-de-la-plataforma',
          title: 'Uso de la plataforma',
          text:
            'Al utilizar este sitio, el usuario acepta estas condiciones de uso y se compromete a utilizar la plataforma de forma adecuada, sin realizar acciones que puedan dañar el servicio, interferir en su funcionamiento o vulnerar derechos de terceros.',
        },
        {
          id: 'reservas-y-pedidos',
          title: 'Reservas y pedidos',
          text:
            'Bame permite consultar la carta, realizar reservas y gestionar pedidos ofrecidos por los establecimientos disponibles en la plataforma. La disponibilidad de productos, horarios y precios puede variar según el local seleccionado.',
        },
        {
          id: 'modificaciones-del-servicio',
          title: 'Modificaciones del servicio',
          text:
            'Bame se reserva el derecho de modificar la información, servicios, precios, disponibilidad de productos o contenidos del sitio cuando sea necesario.',
        },
        {
          id: 'legislacion-y-contacto',
          title: 'Legislación y contacto',
          text:
            'Para cualquier consulta relacionada con estas condiciones, el usuario podrá contactar con el responsable del sitio a través de los canales habilitados.',
        },
      ],
    },
    {
      slug: 'politica-de-privacidad',
      label: 'Política de Privacidad',
      content: [
        {
          id: 'responsable-del-tratamiento',
          title: 'Responsable del tratamiento',
          text:
            'Bame trata los datos personales facilitados por los usuarios con la finalidad de gestionar reservas, pedidos, comunicaciones y servicios relacionados con la actividad del establecimiento.',
        },
        {
          id: 'datos-personales-tratados',
          title: 'Datos personales tratados',
          text:
            'Los datos tratados pueden incluir nombre, apellidos, correo electrónico, teléfono, dirección de entrega y cualquier información necesaria para prestar correctamente el servicio solicitado.',
        },
        {
          id: 'finalidad-del-tratamiento',
          title: 'Finalidad del tratamiento',
          text:
            'Los datos se utilizan para gestionar reservas, tramitar pedidos, atender consultas, mejorar el servicio y cumplir con las obligaciones legales aplicables.',
        },
        {
          id: 'cesion-de-datos',
          title: 'Cesión de datos',
          text:
            'Bame no venderá ni cederá datos personales a terceros salvo obligación legal, necesidad operativa del servicio o consentimiento expreso del usuario.',
        },
        {
          id: 'derechos-del-usuario',
          title: 'Derechos del usuario',
          text:
            'El usuario podrá solicitar el acceso, rectificación o eliminación de sus datos contactando con el responsable del sitio.',
        },
      ],
    },
    {
      slug: 'politica-de-cookies',
      label: 'Política de Cookies',
      content: [
        {
          id: 'que-son-las-cookies',
          title: 'Qué son las cookies',
          text:
            'Este sitio web puede utilizar cookies técnicas necesarias para su correcto funcionamiento y, en su caso, cookies de análisis destinadas a mejorar la experiencia de navegación.',
        },
        {
          id: 'cookies-utilizadas',
          title: 'Cookies utilizadas',
          text:
            'Las cookies permiten recordar determinadas preferencias del usuario, mantener sesiones activas, facilitar el uso de la plataforma y recopilar información estadística anónima.',
        },
        {
          id: 'finalidad-de-las-cookies',
          title: 'Finalidad de las cookies',
          text:
            'La finalidad principal de las cookies es facilitar el uso de la plataforma, mejorar la navegación y obtener información estadística sobre el uso del sitio.',
        },
        {
          id: 'configuracion-del-navegador',
          title: 'Configuración del navegador',
          text:
            'El usuario puede configurar su navegador para bloquear o eliminar cookies en cualquier momento.',
        },
        {
          id: 'limitaciones',
          title: 'Limitaciones',
          text:
            'Si se bloquean o eliminan determinadas cookies, algunas funcionalidades del sitio podrían no funcionar correctamente.',
        },
      ],
    },
  ];

  // Slug de la sección que se está mostrando actualmente.
  readonly activeSlug = signal<string>(this.sections[0]?.slug ?? '');

  // Sección legal activa calculada a partir del slug seleccionado.
  readonly activeSection = computed(() => {
    const slug = this.activeSlug();

    return this.sections.find((section) => section.slug === slug) ?? this.sections[0];
  });

  // Datos derivados utilizados directamente por la plantilla.
  readonly activeContent = computed(() => this.activeSection()?.content ?? []);
  readonly activeLabel = computed(() => this.activeSection()?.label ?? '');

  // Sincroniza la sección activa con el parámetro recibido por la ruta.
  ngOnInit(): void {
    this.syncActiveSectionFromRoute();

    this.route.paramMap.pipe(takeUntilDestroyed(this.destroyRef)).subscribe(() => {
      this.syncActiveSectionFromRoute();
    });
  }

  // Comprueba si una sección coincide con la sección actualmente visible.
  isActive(slug: string): boolean {
    return this.activeSlug() === slug;
  }

  // Lee la ruta actual y activa la sección correspondiente.
  // Si el slug no existe, se muestra por defecto la primera sección.
  private syncActiveSectionFromRoute(): void {
    const sectionSlug = this.route.snapshot.paramMap.get('section');
    const sectionExists = this.sections.some((section) => section.slug === sectionSlug);

    this.activeSlug.set(sectionExists && sectionSlug ? sectionSlug : this.sections[0]?.slug ?? '');
  }
}