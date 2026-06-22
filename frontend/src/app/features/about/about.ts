// ─────────────────────────────────────────────────────────────────────────────
// about.ts — página "Quiénes somos".
//
// Este componente centraliza todo el contenido estático de la página
// informativa de Bame. Incluye las características principales del servicio,
// el flujo de uso de la plataforma, estadísticas destacadas y los valores
// que representan la identidad del proyecto.
//
// También actualiza la descripción SEO de la página para mejorar su
// indexación en buscadores.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, inject, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { Meta } from '@angular/platform-browser';
import { TablerIconComponent } from '@tabler/icons-angular';
import { BreadcrumbBar } from '../../shared/components/breadcrumb-bar/breadcrumb-bar';

@Component({
  selector: 'app-about',
  standalone: true,
  imports: [RouterLink, TablerIconComponent, BreadcrumbBar],
  templateUrl: './about.html',
})
export class AboutPage implements OnInit {
  // Servicio utilizado para actualizar metadatos SEO dinámicamente.
  private readonly meta = inject(Meta);

  // Permite detectar errores de carga en la imagen principal de la página.
  heroImageFailed = false;

  // Características principales que ofrece la plataforma.
  readonly features = [
    {
      icon: 'meat',
      title: 'Pollo asado tradicional',
      description:
        'Asaderos especializados con recetas de toda la vida, elaboradas con productos frescos y métodos tradicionales.',
    },
    {
      icon: 'bookmark',
      title: 'Reservas online',
      description:
        'Reserva tu pedido con antelación desde cualquier dispositivo. Sin colas, sin esperas, sin complicaciones.',
    },
    {
      icon: 'basket',
      title: 'Pedidos para recoger',
      description:
        'Tu comida estará lista cuando llegues al local. Solo tienes que pasar a recogerla.',
    },
    {
      icon: 'circle-check',
      title: 'Información actualizada',
      description:
        'Consulta la carta, los precios y los horarios de cada local. Siempre actualizado.',
    },
  ] as const;

  // Pasos que sigue un cliente desde que consulta la carta
  // hasta que recoge su pedido.
  readonly steps = [
    {
      number: '1',
      icon: 'building-store',
      title: 'Selecciona un asadero',
      description: 'Explora los locales disponibles y elige el que mejor se adapte a ti.',
    },
    {
      number: '2',
      icon: 'file-description',
      title: 'Consulta la carta',
      description: 'Revisa todos los productos, precios y alérgenos de cada plato.',
    },
    {
      number: '3',
      icon: 'shopping-cart',
      title: 'Realiza tu pedido',
      description: 'Selecciona tus platos favoritos y confirma tu reserva en segundos.',
    },
    {
      number: '4',
      icon: 'check',
      title: 'Recógelo sin esperas',
      description: 'Llega al local y recoge tu pedido ya preparado. Sin colas.',
    },
  ] as const;

  // Datos utilizados para mostrar cifras destacadas de la plataforma.
  readonly stats = [
    { value: '5', label: 'Asaderos' },
    { value: '+50', label: 'Productos' },
    { value: '365', label: 'Días disponibles' },
    { value: '100%', label: 'Comida preparada con antelación' },
  ] as const;

  // Valores que representan la filosofía y objetivos del proyecto.
  readonly values = [
    {
      icon: 'meat',
      title: 'Tradición',
      description: 'Respetamos la cocina tradicional y el sabor auténtico de siempre.',
    },
    {
      icon: 'shield-check',
      title: 'Calidad',
      description: 'Trabajamos con establecimientos comprometidos con los más altos estándares.',
    },
    {
      icon: 'bookmark',
      title: 'Comodidad',
      description:
        'Facilitamos el proceso de reserva y recogida para que solo te preocupes de disfrutar.',
    },
  ] as const;

  // Configura la descripción SEO específica de esta página.
  ngOnInit(): void {
    const description = 'Conoce Bame, la plataforma que conecta a los clientes con los mejores asaderos especializados en pollo asado. Nuestra historia, valores y misión.';
    this.meta.updateTag({ name: 'description', content: description });
    this.meta.updateTag({ property: 'og:title', content: 'Acerca de Bame | Quiénes somos' });
    this.meta.updateTag({ property: 'og:description', content: description });
    this.meta.updateTag({ property: 'og:type', content: 'website' });
    this.meta.updateTag({ name: 'twitter:card', content: 'summary' });
    this.meta.updateTag({ name: 'twitter:title', content: 'Acerca de Bame | Quiénes somos' });
    this.meta.updateTag({ name: 'twitter:description', content: description });
  }
}