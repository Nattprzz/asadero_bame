import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { TablerIconComponent } from '@tabler/icons-angular';
import { LocalService } from '@core/services/local.service';
import { LanguageService } from '@core/services/language.service';
import { getUiStrings } from '@core/i18n/ui-strings';
import type { Local } from '@core/models';

interface ContactLocal {
  id: number;
  ini: string;
  name: string;
  addr: string;
  phone: string;
  email: string;
  telHref: string;
  mailHref: string;
}

interface FaqItem {
  q: string;
  a: string;
}

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [TablerIconComponent],
  templateUrl: './contact.html',
})
export class ContactPage implements OnInit {
  private readonly localService = inject(LocalService);
  private readonly lang = inject(LanguageService);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));

  readonly name = signal('');
  readonly email = signal('');
  readonly phone = signal('');
  readonly subject = signal('');
  readonly message = signal('');
  readonly agree = signal(false);

  readonly sent = signal(false);
  readonly sentName = signal('');
  readonly sentEmail = signal('');

  readonly errName = signal('');
  readonly errEmail = signal('');
  readonly errMessage = signal('');
  readonly errAgree = signal('');

  readonly faqOpen = signal(-1);
  readonly locals = signal<ContactLocal[]>([]);

  readonly SUBJECTS = computed(() => {
    const c = this.t().contact;
    return [c.subject1, c.subject2, c.subject3, c.subject4, c.subject5];
  });

  readonly FAQS = computed<FaqItem[]>(() => {
    const c = this.t().contact;
    return [
      { q: c.faq1q, a: c.faq1a },
      { q: c.faq2q, a: c.faq2a },
      { q: c.faq3q, a: c.faq3a },
      { q: c.faq4q, a: c.faq4a },
    ];
  });

  readonly channels = computed(() => {
    const c = this.t().contact;
    return [
      { type: 'phone', label: c.callUs, value: '968 966 890', href: 'tel:+34968966890' },
      { type: 'whatsapp', label: 'WHATSAPP', value: '668 966 890', href: 'https://wa.me/34668966890' },
      { type: 'email', label: c.emailSend, value: 'info@bame.com', href: 'mailto:info@bame.com' },
    ];
  });

  ngOnInit(): void {
    this.subject.set(this.t().contact.subject1);
    this.localService.getLocales().subscribe((ls) => {
      this.locals.set(ls.map((l) => this.mapLocal(l)));
    });
  }

  private mapLocal(l: Local): ContactLocal {
    return {
      id: l.id,
      ini: this.initials(l.nombre),
      name: l.nombre,
      addr: l.ubicacion,
      phone: l.telefono,
      email: l.email ?? '',
      telHref: 'tel:+34' + l.telefono.replace(/\s/g, ''),
      mailHref: 'mailto:' + (l.email ?? ''),
    };
  }

  private initials(name: string): string {
    const words = name
      .trim()
      .split(/\s+/)
      .filter((w) => w.length > 1);
    return ((words[0]?.[0] ?? '') + (words[1]?.[0] ?? '')).toUpperCase();
  }

  setSubject(s: string): void {
    this.subject.set(s);
  }

  toggleAgree(): void {
    this.agree.update((v) => !v);
  }

  toggleFaq(i: number): void {
    this.faqOpen.update((cur) => (cur === i ? -1 : i));
  }

  submit(): void {
    let ok = true;
    this.errName.set('');
    this.errEmail.set('');
    this.errMessage.set('');
    this.errAgree.set('');

    const c = this.t().contact;
    if (!this.name().trim()) {
      this.errName.set(c.errName);
      ok = false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email().trim())) {
      this.errEmail.set(c.errEmail);
      ok = false;
    }
    if (this.message().trim().length < 5) {
      this.errMessage.set(c.errMsg);
      ok = false;
    }
    if (!this.agree()) {
      this.errAgree.set(c.errPrivacy);
      ok = false;
    }

    if (!ok) return;

    this.sentName.set(this.name().trim().split(' ')[0] ?? '');
    this.sentEmail.set(this.email().trim());
    this.sent.set(true);
  }

  reset(): void {
    this.name.set('');
    this.email.set('');
    this.phone.set('');
    this.subject.set(this.t().contact.subject1);
    this.message.set('');
    this.agree.set(false);
    this.errName.set('');
    this.errEmail.set('');
    this.errMessage.set('');
    this.errAgree.set('');
    this.sent.set(false);
  }
}
