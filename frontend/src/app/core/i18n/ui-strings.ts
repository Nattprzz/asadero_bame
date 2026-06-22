// ─────────────────────────────────────────────────────────────────────────────
// ui-strings.ts — traducciones y textos de la interfaz.
//
// Este archivo contiene las cadenas de texto utilizadas por los distintos
// componentes de la aplicación. Los textos se organizan por idioma para
// permitir la internacionalización y garantizar una experiencia coherente
// para todos los usuarios.
//
// Centralizar estos recursos facilita la traducción, el mantenimiento y la
// escalabilidad de la plataforma.
// ─────────────────────────────────────────────────────────────────────────────

import type { Language } from '../services/language.service';

export interface UiStrings {
  // ─── Navbar ──────────────────────────────────────────────────────────────────
  nav: {
    inicio: string; locales: string; nosotros: string; contacto: string; menu: string;
    pedidos: string; perfil: string;
  };

  // ─── Footer ──────────────────────────────────────────────────────────────────
  footer: {
    rights: string; privacidad: string; terminos: string; legal: string;
    locales: string; nosotros: string; contacto: string;
    navigation: string; legalSection: string;
  };

  // ─── Menú de Accesibilidad ──────────────────────────────────────────────────────────────────
  accessibility: {
    title: string; language: string; fontSize: string; accept: string;
    small: string; medium: string; large: string;
    themeLight: string; themeDark: string;
    colorBlind: string; cbNone: string; cbRG: string; cbBY: string;
    modeDark: string; modeLight: string;
  };

  // ─── Días de la Semana ──────────────────────────────────────────────────────────────────
  days: {
    monday: string; tuesday: string; wednesday: string; thursday: string;
    friday: string; saturday: string; sunday: string;
    mon: string; tue: string; wed: string; thu: string; fri: string; sat: string; sun: string;
    closed: string;
  };

  // ─── Comunes ──────────────────────────────────────────────────────────────────
  common: {
    back: string; close: string; loading: string; error: string; retry: string; accept: string;
    cancel: string; save: string; send: string; required: string;
  };

  // ─── Reserva ──────────────────────────────────────────────────────────────────
  reservation: {
    loading: string; errorLoad: string; carta: string;
    addUnit: string; removeUnit: string;
    cart: string; cartEmpty: string; subtotal: string;
    units: string; unit: string; total: string;
    checkout: string; allergens: string; traces: string;
    sinStock: string; quedan: string; maximo10: string;
    confirmarPedido: string;
  };

  // ─── Checkout ──────────────────────────────────────────────────────────────────
  checkout: {
    title: string; yourOrder: string; total: string;
    pay: string; back: string; processing: string;
    errorConnect: string; errorGeneric: string; testMode: string; security: string;
    paymentMethod: string;
    onlineCard: string; onlineCardDesc: string;
    payAtStore: string; payAtStorePayDesc: string;
    payWithStripe: string; confirmOrder: string;
    stripeNote: string; payAtStoreNote: string;
    authRequired: string; loginLink: string;
    connectingStripe: string; confirmingOrder: string;
    orderItems: string; asaderoLabel: string; unitPrice: string;
    reviewOrder: string;
  };

  // ─── Autenticación ──────────────────────────────────────────────────────────────────
  auth: {
    login: string; register: string; logout: string;
    email: string; password: string; remember: string;
    forgotPassword: string; submit: string; noAccount: string; hasAccount: string;
    registerHere: string; loginHere: string;
    loginAccessLabel: string; loginSubtitle: string;
    emailPlaceholder: string;
    enteringSession: string; loginBtn: string;
    loginWithGoogle: string;
    emailRequired: string; emailInvalid: string; passwordRequired: string;
    credentialsError: string;
    navAria: string; brandAria: string; brandSub: string; a11yAria: string;
    themeLightAria: string; themeDarkAria: string; themeLight: string; themeDark: string;
    loyaltyEyebrow: string; loyaltyTitle: string; loyaltyDesc: string;
    sectionAria: string; successRegisterTitle: string; successLoginTitle: string;
    successRegisterDesc: string; successLoginDesc: string;
    continueBtn: string; tabsAria: string; tabLogin: string; tabRegister: string;
    headerRegister: string; headerLogin: string; headerDescRegister: string; headerDescLogin: string;
    requiredLegend: string; nameLabel: string; namePlaceholder: string; phoneLabel: string;
    hidePasswordAria: string; showPasswordAria: string; hide: string; show: string;
    passwordStrengthAria: string; securityLabel: string; terms1: string; termsLink: string;
    terms2: string; privacyLink: string; loadingAuth: string;
    orContinueWith: string; continueWith: string; guestLink: string;
    forgotPageTitle: string; forgotPageDesc: string; forgotPageBtn: string;
    forgotPageSuccess: string; forgotPageError: string; backToLogin: string;
  };

  // ─── Errores ──────────────────────────────────────────────────────────────────
  errors: {
    notFound: string; notFoundDesc: string;
    unauthorized: string; unauthorizedDesc: string;
    forbidden: string; forbiddenDesc: string;
    server: string; serverDesc: string;
    service: string; serviceDesc: string;
    backHome: string; goBack: string; retry: string;
  };

  // ─── Locales ──────────────────────────────────────────────────────────────────
  locales: {
    title: string; subtitle: string; viewMenu: string; reserve: string;
    schedules: string; closedToday: string; today: string; phone: string;
    statusOpen: string; statusClosed: string; statusSoon: string; statusClosingSoon: string;
    loading: string; noLocals: string;
    ctaTitle: string; ctaDesc: string; ctaBtn: string; subtitle2: string;
    noLocalsError: string; noLocalsErrorSub: string;
    todayHours: string; directions: string; orderHere: string;
    noResultsTitle: string; noResultsSub: string;
    stripTitle1: string; stripDesc1: string;
    stripTitle2: string; stripDesc2: string;
    stripTitle3: string; stripDesc3a: string; stripDesc3b: string;
    searchPlaceholder: string; localsSubtitle: string;
  };

  // ─── Home ──────────────────────────────────────────────────────────────────
  home: {
    hero: string; heroSub: string; viewLocals: string; seeMenu: string;
    heroPart1: string; heroHighlight: string; heroPart2: string;
    heroSubDetail: string; howItWorks: string;
    howTitle: string; howSubtitle: string;
    step1Title: string; step1Desc: string;
    step2Title: string; step2Desc: string;
    step3Title: string; step3Desc: string;
    valueTagline: string; valueTitle: string; valueDesc: string;
    benefit1: string; benefit1Desc: string;
    benefit2: string; benefit2Desc: string;
    benefit3: string; benefit3Desc: string;
    ctaTitle: string; ctaDesc: string; ctaBtn: string;
    waitLabel: string; waitValue: string;
    heroTitle: string; heroDesc: string; seeLocal: string;
    exploreMenu: string; viewAll: string; dishes: string;
    topOrders: string; topFavs: string; howOrderTitle: string;
    chooseLocal: string; localsRegion: string; hoursLabel: string;
    callBtn: string; orderHereBtn: string;
    ctaHungryTitle: string; ctaHungrySub: string; startOrderBtn: string;
    homeStep1Title: string; homeStep1Desc: string;
    homeStep2Title: string; homeStep2Desc: string;
    homeStep3Title: string; homeStep3Desc: string;
  };

  // ─── Contacto ──────────────────────────────────────────────────────────────────
  contact: {
    title: string; subtitle: string; send: string; name: string; subject: string; message: string;
    successTitle: string; successDesc: string;
    heroTitle: string; heroDesc: string; formTitle: string; requiredFields: string;
    namePlaceholder: string; phoneLabel: string; emailLabel: string;
    subjectLabel: string; messagePlaceholder: string; privacyCheck: string;
    successPart1: string; successPart2: string; successPart3: string;
    anotherMsg: string; supportLabel: string; supportHours: string; supportNote: string;
    localContactTitle: string; localsCount: string; faqTitle: string;
    faq1q: string; faq1a: string;
    faq2q: string; faq2a: string;
    faq3q: string; faq3a: string;
    faq4q: string; faq4a: string;
    subject1: string; subject2: string; subject3: string; subject4: string; subject5: string;
    callUs: string; emailSend: string;
    errName: string; errEmail: string; errMsg: string; errPrivacy: string;
  };

  // ─── Sobre Bame ──────────────────────────────────────────────────────────────────
  about: { title: string; };
  legal: { privacy: string; terms: string; cookies: string; };

  // ─── Pagar ──────────────────────────────────────────────────────────────────
  payment: {
    successTitle: string; successDesc: string;
    cancelTitle: string; cancelDesc: string; retry: string;
    successStripeDesc: string;
    testModeTitle: string; testModeDesc: string;
    payAtStoreSuccessTitle: string; payAtStoreSuccessDesc: string;
    payAtStorePickup: string; payAtStoreContact: string;
    payAtStoreMethodTitle: string; payAtStoreMethodDesc: string;
    cancelDesc2: string; retryBtn: string; viewLocals: string;
    moreLocals: string; backHome: string; successQuestion: string; testModeNote: string;
  };

  // ─── Perfil ──────────────────────────────────────────────────────────────────
  profile: {
    title: string; loading: string;
    personalInfo: string; nameLabel: string; emailLabel: string; roleLabel: string;
    roleCustomer: string;
    recentOrders: string; noOrders: string;
    preferences: string; language: string;
    reserveBtn: string; orderTotal: string; orderStatus: string;
    memberSince: string; pointsTitle: string; pointsUnit: string;
    pointsToGo: string; pointsForFree: string;
    personalData: string; editBtn: string; cancelBtn: string;
    nameField: string; emailField: string; phoneField: string;
    saveChanges: string; paymentMethod: string; defaultCard: string; addCard: string;
    tabCuenta: string; tabPedidos: string; tabFavoritos: string; tabAjustes: string;
    pastOrders: string; repeatOrder: string; noOrders2: string; viewMenuLink: string;
    favLocalLabel: string; favLocalDesc: string; viewLocalsLink: string;
    savedDishes: string; noDishes: string; noDishesDesc: string;
    notifOrders: string; notifOrdersDesc: string;
    notifOffers: string; notifOffersDesc: string;
    notifSms: string; notifSmsDesc: string;
    darkTheme: string; darkThemeDesc: string;
    languageLabel: string; languageDesc: string; logoutBtn: string;
    loadingOrders: string;
    stepReceived: string; stepPreparing: string; stepReady: string;
  };

  // ─── Menú de Usuario ──────────────────────────────────────────────────────────────────
  userMenu: {
    ariaLabel: string; profile: string; logout: string;
    loginLink: string; registerLink: string;
  };
}

// ─── Español ──────────────────────────────────────────────────────────────────
const ES: UiStrings = {
  // ─── Navbar ──────────────────────────────────────────────────────────────────
  nav: { inicio: 'Inicio', locales: 'Locales', nosotros: 'Nosotros', contacto: 'Contacto', menu: 'Menú', pedidos: 'Pedidos', perfil: 'Perfil' },
  
  // ─── Footer ──────────────────────────────────────────────────────────────────
  footer: { rights: 'Todos los derechos reservados', privacidad: 'Política de privacidad', terminos: 'Términos de uso', legal: 'Aviso legal', locales: 'Nuestros locales', nosotros: 'Acerca de Bame', contacto: 'Contacto', navigation: 'Navegación', legalSection: 'Legal' },
  
  // ─── Menú de Accesibilidad ──────────────────────────────────────────────────────────────────
  accessibility: { title: 'Accesibilidad', language: 'Idioma', fontSize: 'Tamaño de texto', accept: 'Aceptar', small: 'Pequeño', medium: 'Mediano', large: 'Grande', themeLight: 'Cambiar a tema claro', themeDark: 'Cambiar a tema oscuro', colorBlind: 'Visión · Daltonismo', cbNone: 'Sin filtro', cbRG: 'Rojo–verde', cbBY: 'Azul–amarillo', modeDark: 'Modo oscuro', modeLight: 'Modo claro' },
  
  // ─── Días de la Semana ──────────────────────────────────────────────────────────────────
  days: { monday: 'Lunes', tuesday: 'Martes', wednesday: 'Miércoles', thursday: 'Jueves', friday: 'Viernes', saturday: 'Sábado', sunday: 'Domingo', mon: 'Lunes', tue: 'Martes', wed: 'Miércoles', thu: 'Jueves', fri: 'Viernes', sat: 'Sábado', sun: 'Domingo', closed: 'Cerrado' },

  // ─── Comunes ──────────────────────────────────────────────────────────────────
  common: { back: 'Volver', close: 'Cerrar', loading: 'Cargando…', error: 'Error', retry: 'Reintentar', accept: 'Aceptar', cancel: 'Cancelar', save: 'Guardar', send: 'Enviar', required: 'obligatorio' },
  
  // ─── Reserva ──────────────────────────────────────────────────────────────────
  reservation: { loading: 'Cargando carta…', errorLoad: 'No se pudo cargar la carta.', carta: 'Carta', addUnit: 'Añadir unidad', removeUnit: 'Quitar unidad', cart: 'Tu pedido', cartEmpty: 'Aún no has añadido nada.', subtotal: 'Subtotal', units: 'unidades', unit: 'unidad', total: 'Total', checkout: 'Continuar con el pedido', allergens: 'Alérgenos', traces: 'Trazas', sinStock: 'Sin stock', quedan: 'Quedan', maximo10: 'Máximo 10 unidades', confirmarPedido: 'Confirmar pedido' },
  
  // ─── Checkout ──────────────────────────────────────────────────────────────────
  checkout: {
    title: 'Confirmar pedido', yourOrder: 'Tu pedido', total: 'Total', pay: 'Pagar con Stripe', back: 'Volver a la carta', processing: 'Procesando…', errorConnect: 'No se pudo conectar con el servidor.', errorGeneric: 'Error al procesar el pago. Inténtalo de nuevo.', testMode: 'Modo test: no se realizarán cargos reales.', security: 'Pago seguro gestionado por Stripe.',
    paymentMethod: 'Método de pago',
    onlineCard: 'Tarjeta online', onlineCardDesc: 'Pago seguro con Stripe',
    payAtStore: 'Pagar en el local', payAtStorePayDesc: 'Abonas al recoger',
    payWithStripe: 'Pagar con Stripe', confirmOrder: 'Confirmar pedido',
    stripeNote: 'El pago se procesará de forma segura a través de Stripe. No almacenamos datos de tu tarjeta.',
    payAtStoreNote: 'Pago presencial. Abonarás el pedido cuando lo recojas en el local.',
    authRequired: 'Inicia sesión para continuar.', loginLink: 'iniciar sesión',
    connectingStripe: 'Conectando con Stripe…', confirmingOrder: 'Confirmando pedido…',
    orderItems: 'Artículos', asaderoLabel: 'Asadero', unitPrice: '/ ud.',
    reviewOrder: 'Revisa tu pedido y elige cómo quieres pagarlo.',
  },
  
  // ─── Autenticación ──────────────────────────────────────────────────────────────────
  auth: {
    login: 'Iniciar sesión', register: 'Registrarse', logout: 'Cerrar sesión', email: 'Correo electrónico', password: 'Contraseña', remember: 'Recuérdame', forgotPassword: '¿Olvidaste tu contraseña?', submit: 'Entrar', noAccount: '¿Aún no tienes cuenta?', hasAccount: '¿Ya tienes cuenta?', registerHere: 'Regístrate aquí', loginHere: 'Iniciar sesión',
    loginAccessLabel: 'Acceso', loginSubtitle: 'Accede a tu cuenta para gestionar tus reservas.',
    emailPlaceholder: 'nombre@ejemplo.com',
    enteringSession: 'Iniciando sesión…', loginBtn: 'Entrar',
    loginWithGoogle: 'Iniciar sesión con Google',
    emailRequired: 'El correo es obligatorio.', emailInvalid: 'Introduce un correo válido.', passwordRequired: 'La contraseña es obligatoria.',
    credentialsError: 'Credenciales incorrectas. Comprueba tu correo y contraseña.',
    // Nuevas variables del formulario completo
    navAria: 'Navegación de autenticación', brandAria: 'BAME, ir al inicio', brandSub: 'ASADOR · MURCIA', a11yAria: 'Opciones de accesibilidad',
    themeLightAria: 'Activar modo claro', themeDarkAria: 'Activar modo oscuro', themeLight: 'Modo claro', themeDark: 'Modo oscuro',
    loyaltyEyebrow: 'Puntos BAME', loyaltyTitle: 'Pide más rápido<br />cada vez.', loyaltyDesc: 'Guarda tus datos, repite pedidos en un toque y acumula puntos canjeables por pollo asado gratis.',
    sectionAria: 'Acceso a BAME', successRegisterTitle: '¡Cuenta creada!', successLoginTitle: '¡Bienvenido de nuevo!',
    successRegisterDesc: 'Ya formas parte de BAME. Empieza a sumar puntos con tu primer pedido.', successLoginDesc: 'Has accedido correctamente a tu cuenta.',
    continueBtn: 'Continuar →', tabsAria: 'Tipo de acceso', tabLogin: 'Acceder', tabRegister: 'Crear cuenta',
    headerRegister: 'Crea tu cuenta', headerLogin: 'Accede a tu cuenta', headerDescRegister: 'Solo te llevará un minuto.', headerDescLogin: 'Introduce tus datos para continuar.',
    requiredLegend: 'Indica un campo obligatorio', nameLabel: 'Nombre', namePlaceholder: 'Tu nombre', phoneLabel: 'Número de teléfono',
    hidePasswordAria: 'Ocultar contraseña', showPasswordAria: 'Mostrar contraseña', hide: 'Ocultar', show: 'Ver',
    passwordStrengthAria: 'Fuerza de la contraseña', securityLabel: 'Seguridad:', terms1: 'Acepto los', termsLink: 'términos', terms2: 'y la', privacyLink: 'política de privacidad',
    loadingAuth: 'Un momento…', orContinueWith: 'o continúa con', continueWith: 'Continuar con ', guestLink: 'Continuar como invitado →',
    forgotPageTitle: 'Recuperar contraseña', forgotPageDesc: 'Introduce tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.', forgotPageBtn: 'Enviar instrucciones',
    forgotPageSuccess: 'Si el correo existe en nuestra base de datos, recibirás instrucciones para restablecer tu contraseña.', forgotPageError: 'No se ha podido enviar la solicitud. Inténtalo de nuevo más tarde.', backToLogin: 'Volver a iniciar sesión'
  },
  
  // ─── Errores ──────────────────────────────────────────────────────────────────
  errors: { notFound: 'Página no encontrada', notFoundDesc: 'La página que buscas no existe o ha sido movida.', unauthorized: 'No autorizado', unauthorizedDesc: 'Debes iniciar sesión para acceder.', forbidden: 'Acceso denegado', forbiddenDesc: 'No tienes permiso para ver esta página.', server: 'Error del servidor', serverDesc: 'Algo ha ido mal. Inténtalo más tarde.', backHome: 'Volver al inicio', service: 'Servicio no disponible', serviceDesc: 'Estamos realizando tareas de mantenimiento. El servicio volverá pronto.', goBack: 'Volver atrás', retry: 'Reintentar' },
  
  // ─── Locales ──────────────────────────────────────────────────────────────────
  locales: {
    title: 'Nuestros asaderos', subtitle: 'Elige tu local favorito', viewMenu: 'Ver carta', reserve: 'Reservar', schedules: 'Horarios', closedToday: 'Cerrado hoy', today: 'Hoy', phone: 'Teléfono', statusOpen: 'Abierto', statusClosed: 'Cerrado', statusSoon: 'Abre pronto', statusClosingSoon: 'Cierra pronto',
    loading: 'Cargando locales…', noLocals: 'No hay locales disponibles en este momento.',
    ctaTitle: '¿Ya sabes dónde reservar?', ctaDesc: 'Elige tu asadero favorito y te dejamos el pollo listo para recoger.', ctaBtn: 'Reservar ahora',
    subtitle2: 'Selecciona el asadero más cercano, reserva tu pollo en segundos y recógelo sin esperas.',
    noLocalsError: 'No se pudieron cargar los locales', noLocalsErrorSub: 'Comprueba tu conexión e inténtalo de nuevo.',
    todayHours: 'Hoy:', directions: 'Cómo llegar', orderHere: 'Pedir aquí',
    noResultsTitle: 'Sin resultados', noResultsSub: 'No encontramos locales para',
    stripTitle1: 'Recogida sin colas', stripDesc1: 'Pide online y recoge tu pedido caliente en 20–30 min en cualquiera de nuestros locales.',
    stripTitle2: 'Mismo sabor, misma carta', stripDesc2: 'Todos los asaderos comparten nuestra carta y recetas tradicionales murcianas.',
    stripTitle3: '¿Dudas?', stripDesc3a: 'Escríbenos desde la página de', stripDesc3b: 'o llama a tu local.',
    searchPlaceholder: 'Busca por localidad o código postal…',
    localsSubtitle: 'asaderos repartidos por la Región de Murcia. Elige el más cercano, haz tu pedido y recógelo caliente.',
  },
  
  // ─── Home ──────────────────────────────────────────────────────────────────
  home: {
    hero: 'El mejor asadero de Murcia', heroSub: 'Pollo a l\'ast y especialidades murcianas', viewLocals: 'Ver locales', seeMenu: 'Ver carta',
    heroPart1: 'Pollo asado,', heroHighlight: 'brasa', heroPart2: 'y sabor de verdad',
    heroSubDetail: 'Reserva tu pollo en los mejores asaderos y food trucks de la región. Listo cuando llegues, sin esperas ni colas.',
    howItWorks: 'Cómo funciona',
    howTitle: 'Cómo funciona', howSubtitle: 'Tres pasos para tener tu pollo asado listo sin complicaciones.',
    step1Title: 'Elige tu asadero', step1Desc: 'Encuentra el asadero o food truck más cercano y consulta la disponibilidad en tiempo real.',
    step2Title: 'Reserva tu pollo', step2Desc: 'Elige tus productos y confirma tu pedido al instante. Sin pago online, sin complicaciones.',
    step3Title: 'Recógelo sin esperas', step3Desc: 'Llega al asadero y recoge tu pedido directamente. Sin colas, sin sorpresas, sin perder el tiempo.',
    valueTagline: 'Para los que valoran el tiempo', valueTitle: 'Tu pollo listo cuando llegues',
    valueDesc: 'Evita colas, asegura disponibilidad y encuentra el asadero más cercano. Con Bame, tu tiempo es lo primero — reserva en segundos y disfruta sin esperas.',
    benefit1: 'Sin colas ni esperas', benefit1Desc: 'Tu pedido estará listo exactamente cuando llegues al asadero.',
    benefit2: 'Disponibilidad en tiempo real', benefit2Desc: 'Consulta si tu asadero favorito está abierto antes de salir de casa.',
    benefit3: 'Confirmación inmediata', benefit3Desc: 'Tu reserva queda confirmada al instante, sin necesidad de pago previo.',
    ctaTitle: '¿Listo para reservar?', ctaDesc: 'Elige tu asadero favorito, reserva en segundos y recoge sin esperar.',
    ctaBtn: 'Ver locales disponibles', waitLabel: 'Tiempo de espera', waitValue: 'Listo al llegar',
    heroTitle: 'Pollo asado, recién hecho.', heroDesc: 'Asado lentamente con especias en su propio jugo. Pide online y recógelo caliente en tu local de Murcia. Sin colas, sin gastos de envío.',
    seeLocal: 'Ver el local', exploreMenu: 'Explora la carta', viewAll: 'Ver todo →', dishes: 'platos',
    topOrders: 'Lo más pedido', topFavs: 'Los favoritos del barrio', howOrderTitle: 'Pedir es así de fácil',
    chooseLocal: 'Elige tu asador', localsRegion: 'locales en la Región de Murcia', hoursLabel: 'HORARIO',
    callBtn: 'Llamar', orderHereBtn: 'Pedir aquí →',
    ctaHungryTitle: '¿Tienes hambre ya?', ctaHungrySub: 'Tu pollo asado puede estar listo en 20 minutos.', startOrderBtn: 'Empezar mi pedido →',
    homeStep1Title: 'Elige tus platos', homeStep1Desc: 'Explora la carta y añade lo que más te apetezca al carrito.',
    homeStep2Title: 'Paga como prefieras', homeStep2Desc: 'Tarjeta online, Bizum o en efectivo al recoger. Tú decides.',
    homeStep3Title: 'Recoge caliente', homeStep3Desc: 'En 20–30 min tu pedido está listo. Sin colas ni esperas.',
  },
  
  // ─── Contacto ──────────────────────────────────────────────────────────────────
  contact: {
    title: '¿Necesitas ayuda?', subtitle: 'Escríbenos si tienes dudas.', send: 'Enviar mensaje', name: 'Nombre', subject: 'Asunto', message: 'Mensaje', successTitle: 'Mensaje enviado', successDesc: 'Gracias por contactar con Bame. Te responderemos lo antes posible.',
    heroTitle: 'Hablemos', heroDesc: '¿Tienes una duda sobre tu pedido, una sugerencia o quieres organizar un evento? Escríbenos o contacta directamente con tu asador.',
    formTitle: 'Envíanos un mensaje', requiredFields: 'Campos obligatorios',
    namePlaceholder: 'Tu nombre', phoneLabel: 'TELÉFONO', emailLabel: 'CORREO ELECTRÓNICO',
    subjectLabel: 'ASUNTO', messagePlaceholder: 'Cuéntanos en qué podemos ayudarte…',
    privacyCheck: 'He leído y acepto la política de privacidad y el tratamiento de mis datos.',
    successPart1: 'Gracias, ', successPart2: '. Te responderemos a ', successPart3: ' en menos de 24 horas.',
    anotherMsg: 'Enviar otro mensaje', supportLabel: 'ATENCIÓN AL CLIENTE',
    supportHours: 'Lunes a domingo, de 09:30 a 13:30.',
    supportNote: 'Para pedidos en curso, contacta directamente con tu local.',
    localContactTitle: 'Contacta con tu local', localsCount: 'asaderos en la Región de Murcia',
    faqTitle: 'Preguntas frecuentes',
    faq1q: '¿Cuánto tarda en estar listo mi pedido?', faq1a: 'Los pedidos para recoger suelen estar listos en 20–30 minutos. Te avisamos cuando puedas pasar a recogerlo.',
    faq2q: '¿Puedo pagar en el local?', faq2a: 'Sí. Puedes pagar online con tarjeta o Bizum, o bien en efectivo o tarjeta al recoger tu pedido en el asador.',
    faq3q: '¿Hacéis pedidos para eventos o grupos grandes?', faq3a: 'Por supuesto. Escríbenos con el asunto "Eventos y catering" indicando fecha, número de personas y local, y te preparamos un presupuesto.',
    faq4q: '¿Dónde consulto los alérgenos de cada plato?', faq4a: 'Cada producto de la carta muestra sus alérgenos y posibles trazas al abrir su ficha. Si tienes dudas, pregunta a tu local.',
    subject1: 'Reservas y pedidos', subject2: 'Sugerencias', subject3: 'Reclamaciones', subject4: 'Eventos y catering', subject5: 'Trabaja con nosotros',
    callUs: 'LLÁMANOS', emailSend: 'Enviar email',
    errName: 'Indica tu nombre.', errEmail: 'Introduce un email válido.', errMsg: 'Escribe tu mensaje.', errPrivacy: 'Debes aceptar la política de privacidad.',
  },
  
  // ─── Acerca de Bame ──────────────────────────────────────────────────────────────────
  about: { title: 'Acerca de Bame' },
  
  // ─── Legal ──────────────────────────────────────────────────────────────────
  legal: { privacy: 'Política de privacidad', terms: 'Términos de uso', cookies: 'Política de cookies' },
  
  // ─── Pagar ──────────────────────────────────────────────────────────────────
  payment: {
    successTitle: '¡Pago completado!', successDesc: 'Tu pago se ha procesado correctamente.', cancelTitle: 'Pago cancelado', cancelDesc: 'No se ha realizado ningún cargo. Tu pedido sigue guardado en el carrito.', retry: 'Reintentar pago',
    successStripeDesc: 'Tu pago se ha procesado correctamente a través de Stripe.',
    testModeTitle: 'Modo test:', testModeDesc: 'No se realizará ningún cargo real. Este es un entorno de pruebas.',
    payAtStoreSuccessTitle: '¡Pedido confirmado!', payAtStoreSuccessDesc: 'Tu pedido se ha registrado correctamente.',
    payAtStorePickup: 'Recuerda que abonarás el importe cuando lo recojas en el local.',
    payAtStoreContact: 'Si tienes alguna duda, contacta con el asadero directamente.',
    payAtStoreMethodTitle: 'Método: Pagar en el local.', payAtStoreMethodDesc: 'El local recibirá tu pedido y estará listo para cuando llegues.',
    cancelDesc2: 'Puedes volver a intentar el pago o modificar tu selección antes de continuar.',
    retryBtn: 'Reintentar pago', viewLocals: 'Ver asaderos',
    moreLocals: 'Ver más asaderos', backHome: 'Volver al inicio',
    successQuestion: 'Si tienes alguna duda, contacta con el local directamente.',
    testModeNote: 'Usa la tarjeta de prueba',
  },
  
  // ─── Perfil ──────────────────────────────────────────────────────────────────
  profile: {
    title: 'Mi perfil', loading: 'Cargando tu perfil…',
    personalInfo: 'Información personal', nameLabel: 'Nombre', emailLabel: 'Correo', roleLabel: 'Rol',
    roleCustomer: 'Cliente',
    recentOrders: 'Pedidos recientes', noOrders: 'Aún no tienes pedidos.',
    preferences: 'Preferencias', language: 'Idioma',
    reserveBtn: 'Hacer un pedido', orderTotal: 'Total', orderStatus: 'Estado',
    memberSince: 'Miembro desde', pointsTitle: 'Puntos BAME', pointsUnit: 'puntos',
    pointsToGo: 'Te faltan', pointsForFree: 'puntos para conseguir medio pollo asado gratis. Ganas 1 punto por cada €.',
    personalData: 'Datos personales', editBtn: 'Editar', cancelBtn: 'Cancelar',
    nameField: 'Nombre', emailField: 'Email', phoneField: 'Teléfono',
    saveChanges: 'Guardar cambios', paymentMethod: 'Método de pago', defaultCard: 'Por defecto', addCard: '+ Añadir tarjeta',
    tabCuenta: 'Cuenta', tabPedidos: 'Pedidos', tabFavoritos: 'Favoritos', tabAjustes: 'Ajustes',
    pastOrders: 'Pedidos anteriores', repeatOrder: '↺ Repetir pedido',
    noOrders2: 'Aún no has realizado ningún pedido.', viewMenuLink: 'Ver la carta →',
    favLocalLabel: 'TU LOCAL FAVORITO', favLocalDesc: 'Guarda un local favorito desde la selección de locales.',
    viewLocalsLink: 'Ver locales →', savedDishes: 'Platos guardados',
    noDishes: 'Todavía no tienes platos guardados', noDishesDesc: 'Añade los que más te gusten desde la carta.',
    notifOrders: 'Avisos de pedido', notifOrdersDesc: 'Notificaciones del estado de tu pedido',
    notifOffers: 'Ofertas y novedades', notifOffersDesc: 'Promociones por email',
    notifSms: 'SMS', notifSmsDesc: 'Avisos por mensaje de texto',
    darkTheme: 'Tema oscuro', darkThemeDesc: 'Cambia la apariencia de la app',
    languageLabel: 'Idioma', languageDesc: 'Idioma de la aplicación',
    logoutBtn: 'Cerrar sesión', loadingOrders: 'Cargando pedidos…',
    stepReceived: 'Recibido', stepPreparing: 'En preparación', stepReady: 'Listo',
  },
  
  // ─── Menú de Usuario ──────────────────────────────────────────────────────────────────
  userMenu: { ariaLabel: 'Menú de usuario', profile: 'Mi perfil', logout: 'Cerrar sesión', loginLink: 'Iniciar sesión', registerLink: 'Registrarse' },
};

// ─── English ──────────────────────────────────────────────────────────────────
const EN: UiStrings = {
  // ─── Navbar ──────────────────────────────────────────────────────────────────
  nav: { inicio: 'Home', locales: 'Locations', nosotros: 'About Us', contacto: 'Contact', menu: 'Menu', pedidos: 'Orders', perfil: 'Profile' },
  
  // ─── Footer ──────────────────────────────────────────────────────────────────
  footer: { rights: 'All rights reserved', privacidad: 'Privacy Policy', terminos: 'Terms of Use', legal: 'Legal Notice', locales: 'Our Locations', nosotros: 'About Bame', contacto: 'Contact', navigation: 'Navigation', legalSection: 'Legal' },
  
  // ─── Menú de Accesibilidad ──────────────────────────────────────────────────────────────────
  accessibility: { title: 'Accessibility', language: 'Language', fontSize: 'Text Size', accept: 'Accept', small: 'Small', medium: 'Medium', large: 'Large', themeLight: 'Switch to light theme', themeDark: 'Switch to dark theme', colorBlind: 'Vision · Color Blindness', cbNone: 'No filter', cbRG: 'Red–green', cbBY: 'Blue–yellow', modeDark: 'Dark mode', modeLight: 'Light mode' },
  
  // ─── Días de la Semana ──────────────────────────────────────────────────────────────────
  days: { monday: 'Monday', tuesday: 'Tuesday', wednesday: 'Wednesday', thursday: 'Thursday', friday: 'Friday', saturday: 'Saturday', sunday: 'Sunday', mon: 'Mon', tue: 'Tue', wed: 'Wed', thu: 'Thu', fri: 'Fri', sat: 'Sat', sun: 'Sun', closed: 'Closed' },

  // ─── Comunes ──────────────────────────────────────────────────────────────────
  common: { back: 'Back', close: 'Close', loading: 'Loading…', error: 'Error', retry: 'Retry', accept: 'Accept', cancel: 'Cancel', save: 'Save', send: 'Send', required: 'required' },
  
  // ─── Reserva ──────────────────────────────────────────────────────────────────
  reservation: { loading: 'Loading menu…', errorLoad: 'Could not load the menu.', carta: 'Menu', addUnit: 'Add unit', removeUnit: 'Remove unit', cart: 'Your order', cartEmpty: 'You haven\'t added anything yet.', subtotal: 'Subtotal', units: 'units', unit: 'unit', total: 'Total', checkout: 'Continue to checkout', allergens: 'Allergens', traces: 'Traces', sinStock: 'Out of stock', quedan: 'Remaining', maximo10: 'Maximum 10 units', confirmarPedido: 'Confirm order' },
  
  // ─── Checkout ──────────────────────────────────────────────────────────────────
  checkout: {
    title: 'Confirm order', yourOrder: 'Your order', total: 'Total', pay: 'Pay with Stripe', back: 'Back to menu', processing: 'Processing…', errorConnect: 'Could not connect to the server.', errorGeneric: 'Error processing payment. Please try again.', testMode: 'Test mode: no real charges will be made.', security: 'Secure payment powered by Stripe.',
    paymentMethod: 'Payment method',
    onlineCard: 'Online card', onlineCardDesc: 'Secure payment with Stripe',
    payAtStore: 'Pay at store', payAtStorePayDesc: 'Pay upon pickup',
    payWithStripe: 'Pay with Stripe', confirmOrder: 'Confirm order',
    stripeNote: 'The payment will be securely processed via Stripe. We do not store your card details.',
    payAtStoreNote: 'In-person payment. You will pay for the order when you pick it up at the location.',
    authRequired: 'Log in to continue.', loginLink: 'log in',
    connectingStripe: 'Connecting to Stripe…', confirmingOrder: 'Confirming order…',
    orderItems: 'Items', asaderoLabel: 'Location', unitPrice: '/ unit',
    reviewOrder: 'Review your order and choose how you want to pay.',
  },
  
  // ─── Autenticación ──────────────────────────────────────────────────────────────────
  auth: {
    login: 'Log in', register: 'Sign up', logout: 'Log out', email: 'Email address', password: 'Password', remember: 'Remember me', forgotPassword: 'Forgot your password?', submit: 'Enter', noAccount: 'Don\'t have an account yet?', hasAccount: 'Already have an account?', registerHere: 'Sign up here', loginHere: 'Log in',
    loginAccessLabel: 'Access', loginSubtitle: 'Log into your account to manage your reservations.',
    emailPlaceholder: 'name@example.com',
    enteringSession: 'Logging in…', loginBtn: 'Log in',
    loginWithGoogle: 'Log in with Google',
    emailRequired: 'Email is required.', emailInvalid: 'Enter a valid email.', passwordRequired: 'Password is required.',
    credentialsError: 'Incorrect credentials. Check your email and password.',
    // Nuevas variables del formulario completo
    navAria: 'Authentication navigation', brandAria: 'BAME, go to home', brandSub: 'ROTISSERIE · MURCIA', a11yAria: 'Accessibility options',
    themeLightAria: 'Enable light mode', themeDarkAria: 'Enable dark mode', themeLight: 'Light mode', themeDark: 'Dark mode',
    loyaltyEyebrow: 'BAME Points', loyaltyTitle: 'Order faster<br />every time.', loyaltyDesc: 'Save your details, repeat orders in one tap, and earn points for free roast chicken.',
    sectionAria: 'BAME Login', successRegisterTitle: 'Account created!', successLoginTitle: 'Welcome back!',
    successRegisterDesc: 'You are now part of BAME. Start earning points with your first order.', successLoginDesc: 'You have successfully logged into your account.',
    continueBtn: 'Continue →', tabsAria: 'Access type', tabLogin: 'Log in', tabRegister: 'Create account',
    headerRegister: 'Create your account', headerLogin: 'Log into your account', headerDescRegister: 'It will only take a minute.', headerDescLogin: 'Enter your details to continue.',
    requiredLegend: 'Indicates a required field', nameLabel: 'Name', namePlaceholder: 'Your name', phoneLabel: 'Phone number',
    hidePasswordAria: 'Hide password', showPasswordAria: 'Show password', hide: 'Hide', show: 'Show',
    passwordStrengthAria: 'Password strength', securityLabel: 'Security:', terms1: 'I accept the', termsLink: 'terms', terms2: 'and', privacyLink: 'privacy policy',
    loadingAuth: 'Just a moment…', orContinueWith: 'or continue with', continueWith: 'Continue with ', guestLink: 'Continue as guest →',
    forgotPageTitle: 'Recover password', forgotPageDesc: 'Enter your email address and we will send you instructions to reset your password.', forgotPageBtn: 'Send instructions',
    forgotPageSuccess: 'If the email exists in our database, you will receive instructions to reset your password.', forgotPageError: 'The request could not be sent. Please try again later.', backToLogin: 'Back to login'
  },
  
  // ─── Errores ──────────────────────────────────────────────────────────────────
  errors: { notFound: 'Page not found', notFoundDesc: 'The page you are looking for does not exist or has been moved.', unauthorized: 'Unauthorized', unauthorizedDesc: 'You must log in to access.', forbidden: 'Access denied', forbiddenDesc: 'You do not have permission to view this page.', server: 'Server error', serverDesc: 'Something went wrong. Try again later.', backHome: 'Back to home', service: 'Service unavailable', serviceDesc: 'We are performing maintenance. The service will be back shortly.', goBack: 'Go back', retry: 'Retry' },
  
  // ─── Locales ──────────────────────────────────────────────────────────────────
  locales: {
    title: 'Our rotisseries', subtitle: 'Choose your favorite location', viewMenu: 'View menu', reserve: 'Order', schedules: 'Hours', closedToday: 'Closed today', today: 'Today', phone: 'Phone', statusOpen: 'Open', statusClosed: 'Closed', statusSoon: 'Opening soon', statusClosingSoon: 'Closing soon',
    loading: 'Loading locations…', noLocals: 'No locations available right now.',
    ctaTitle: 'Already know where to order?', ctaDesc: 'Choose your favorite location and we will have your chicken ready for pickup.', ctaBtn: 'Order now',
    subtitle2: 'Select the nearest rotisserie, order your chicken in seconds, and pick it up without waiting.',
    noLocalsError: 'Could not load locations', noLocalsErrorSub: 'Check your connection and try again.',
    todayHours: 'Today:', directions: 'Directions', orderHere: 'Order here',
    noResultsTitle: 'No results', noResultsSub: 'We couldn\'t find locations for',
    stripTitle1: 'Skip the line pickup', stripDesc1: 'Order online and pick up your hot order in 20–30 min at any of our locations.',
    stripTitle2: 'Same taste, same menu', stripDesc2: 'All locations share our menu and traditional Murcian recipes.',
    stripTitle3: 'Questions?', stripDesc3a: 'Write to us from the', stripDesc3b: 'page or call your location.',
    searchPlaceholder: 'Search by city or zip code…',
    localsSubtitle: 'locations spread across the Region of Murcia. Choose the closest one, place your order, and pick it up hot.',
  },
  
  // ─── Home ──────────────────────────────────────────────────────────────────
  home: {
    hero: 'The best rotisserie in Murcia', heroSub: 'Roast chicken and Murcian specialties', viewLocals: 'View locations', seeMenu: 'View menu',
    heroPart1: 'Roast chicken,', heroHighlight: 'grilled', heroPart2: 'and real flavor',
    heroSubDetail: 'Order your chicken at the best rotisseries and food trucks in the region. Ready when you arrive, no waiting, no lines.',
    howItWorks: 'How it works',
    howTitle: 'How it works', howSubtitle: 'Three steps to have your roast chicken ready without complications.',
    step1Title: 'Choose your location', step1Desc: 'Find the nearest rotisserie or food truck and check availability in real time.',
    step2Title: 'Order your chicken', step2Desc: 'Choose your products and confirm your order instantly. No online payment, no hassle.',
    step3Title: 'Pick up without waiting', step3Desc: 'Arrive at the location and pick up your order directly. No lines, no surprises, no wasted time.',
    valueTagline: 'For those who value their time', valueTitle: 'Your chicken ready when you arrive',
    valueDesc: 'Avoid lines, ensure availability, and find the closest location. With Bame, your time comes first — order in seconds and enjoy without waiting.',
    benefit1: 'No lines or waiting', benefit1Desc: 'Your order will be ready exactly when you arrive at the location.',
    benefit2: 'Real-time availability', benefit2Desc: 'Check if your favorite location is open before leaving home.',
    benefit3: 'Immediate confirmation', benefit3Desc: 'Your reservation is confirmed instantly, no prepayment required.',
    ctaTitle: 'Ready to order?', ctaDesc: 'Choose your favorite location, order in seconds, and pick up without waiting.',
    ctaBtn: 'View available locations', waitLabel: 'Wait time', waitValue: 'Ready upon arrival',
    heroTitle: 'Freshly roast chicken.', heroDesc: 'Slowly roasted with spices in its own juices. Order online and pick it up hot at your Murcia location. No lines, no delivery fees.',
    seeLocal: 'View location', exploreMenu: 'Explore the menu', viewAll: 'View all →', dishes: 'dishes',
    topOrders: 'Most ordered', topFavs: 'Neighborhood favorites', howOrderTitle: 'Ordering is this easy',
    chooseLocal: 'Choose your rotisserie', localsRegion: 'locations in the Region of Murcia', hoursLabel: 'HOURS',
    callBtn: 'Call', orderHereBtn: 'Order here →',
    ctaHungryTitle: 'Hungry already?', ctaHungrySub: 'Your roast chicken can be ready in 20 minutes.', startOrderBtn: 'Start my order →',
    homeStep1Title: 'Choose your dishes', homeStep1Desc: 'Explore the menu and add what you crave most to the cart.',
    homeStep2Title: 'Pay as you prefer', homeStep2Desc: 'Online card, Bizum, or cash upon pickup. You decide.',
    homeStep3Title: 'Pick up hot', homeStep3Desc: 'Your order is ready in 20–30 min. No lines or waiting.',
  },
  
  // ─── Contacto ──────────────────────────────────────────────────────────────────
  contact: {
    title: 'Need help?', subtitle: 'Write to us if you have any questions.', send: 'Send message', name: 'Name', subject: 'Subject', message: 'Message', successTitle: 'Message sent', successDesc: 'Thank you for contacting Bame. We will reply as soon as possible.',
    heroTitle: 'Let\'s talk', heroDesc: 'Do you have a question about your order, a suggestion, or want to organize an event? Write to us or contact your location directly.',
    formTitle: 'Send us a message', requiredFields: 'Required fields',
    namePlaceholder: 'Your name', phoneLabel: 'PHONE', emailLabel: 'EMAIL',
    subjectLabel: 'SUBJECT', messagePlaceholder: 'Tell us how we can help you…',
    privacyCheck: 'I have read and accept the privacy policy and the processing of my data.',
    successPart1: 'Thank you, ', successPart2: '. We will reply to ', successPart3: ' in less than 24 hours.',
    anotherMsg: 'Send another message', supportLabel: 'CUSTOMER SUPPORT',
    supportHours: 'Monday to Sunday, from 09:30 to 13:30.',
    supportNote: 'For ongoing orders, contact your location directly.',
    localContactTitle: 'Contact your location', localsCount: 'locations in the Region of Murcia',
    faqTitle: 'Frequently asked questions',
    faq1q: 'How long does my order take to be ready?', faq1a: 'Pickup orders are usually ready in 20–30 minutes. We\'ll let you know when you can come pick it up.',
    faq2q: 'Can I pay at the location?', faq2a: 'Yes. You can pay online with a card or Bizum, or in cash or card when picking up your order at the location.',
    faq3q: 'Do you do orders for events or large groups?', faq3a: 'Of course. Write to us with the subject "Events and catering" indicating the date, number of people, and location, and we will prepare a quote for you.',
    faq4q: 'Where can I check the allergens of each dish?', faq4a: 'Each product on the menu shows its allergens and possible traces when opening its details. If you have any questions, ask your location.',
    subject1: 'Reservations and orders', subject2: 'Suggestions', subject3: 'Complaints', subject4: 'Events and catering', subject5: 'Work with us',
    callUs: 'CALL US', emailSend: 'Send email',
    errName: 'Enter your name.', errEmail: 'Enter a valid email address.', errMsg: 'Write your message.', errPrivacy: 'You must accept the privacy policy.',
  },
  
  // ─── Acerca de Bame ──────────────────────────────────────────────────────────────────
  about: { title: 'About Bame' },
  
  // ─── Legal ──────────────────────────────────────────────────────────────────
  legal: { privacy: 'Privacy Policy', terms: 'Terms of Use', cookies: 'Cookies Policy' },
  
  // ─── Pagar ──────────────────────────────────────────────────────────────────
  payment: {
    successTitle: 'Payment completed!', successDesc: 'Your payment has been successfully processed.', cancelTitle: 'Payment canceled', cancelDesc: 'No charge was made. Your order is still saved in the cart.', retry: 'Retry payment',
    successStripeDesc: 'Your payment has been successfully processed through Stripe.',
    testModeTitle: 'Test mode:', testModeDesc: 'No real charge will be made. This is a testing environment.',
    payAtStoreSuccessTitle: 'Order confirmed!', payAtStoreSuccessDesc: 'Your order has been successfully registered.',
    payAtStorePickup: 'Remember that you will pay the amount when you pick it up at the location.',
    payAtStoreContact: 'If you have any questions, contact the location directly.',
    payAtStoreMethodTitle: 'Method: Pay at location.', payAtStoreMethodDesc: 'The location will receive your order and it will be ready when you arrive.',
    cancelDesc2: 'You can retry the payment or modify your selection before continuing.',
    retryBtn: 'Retry payment', viewLocals: 'View locations',
    moreLocals: 'View more locations', backHome: 'Back to home',
    successQuestion: 'If you have any questions, contact the location directly.',
    testModeNote: 'Use the test card',
  },
  
  // ─── Perfil ──────────────────────────────────────────────────────────────────
  profile: {
    title: 'My profile', loading: 'Loading your profile…',
    personalInfo: 'Personal information', nameLabel: 'Name', emailLabel: 'Email', roleLabel: 'Role',
    roleCustomer: 'Customer',
    recentOrders: 'Recent orders', noOrders: 'You don\'t have any orders yet.',
    preferences: 'Preferences', language: 'Language',
    reserveBtn: 'Place an order', orderTotal: 'Total', orderStatus: 'Status',
    memberSince: 'Member since', pointsTitle: 'BAME Points', pointsUnit: 'points',
    pointsToGo: 'You need', pointsForFree: 'more points to get half a free roast chicken. You earn 1 point for every €.',
    personalData: 'Personal data', editBtn: 'Edit', cancelBtn: 'Cancel',
    nameField: 'Name', emailField: 'Email', phoneField: 'Phone',
    saveChanges: 'Save changes', paymentMethod: 'Payment method', defaultCard: 'Default', addCard: '+ Add card',
    tabCuenta: 'Account', tabPedidos: 'Orders', tabFavoritos: 'Favorites', tabAjustes: 'Settings',
    pastOrders: 'Past orders', repeatOrder: '↺ Repeat order',
    noOrders2: 'You haven\'t placed any orders yet.', viewMenuLink: 'View menu →',
    favLocalLabel: 'YOUR FAVORITE LOCATION', favLocalDesc: 'Save a favorite location from the locations list.',
    viewLocalsLink: 'View locations →', savedDishes: 'Saved dishes',
    noDishes: 'You don\'t have any saved dishes yet', noDishesDesc: 'Add the ones you like the most from the menu.',
    notifOrders: 'Order alerts', notifOrdersDesc: 'Notifications about your order status',
    notifOffers: 'Offers and news', notifOffersDesc: 'Promotions by email',
    notifSms: 'SMS', notifSmsDesc: 'Text message alerts',
    darkTheme: 'Dark theme', darkThemeDesc: 'Change the appearance of the app',
    languageLabel: 'Language', languageDesc: 'App language',
    logoutBtn: 'Log out', loadingOrders: 'Loading orders…',
    stepReceived: 'Received', stepPreparing: 'Preparing', stepReady: 'Ready',
  },
  
  // ─── Menú de Usuario ──────────────────────────────────────────────────────────────────
  userMenu: { ariaLabel: 'User menu', profile: 'My profile', logout: 'Log out', loginLink: 'Log in', registerLink: 'Sign up' },
};

// ─── Français ─────────────────────────────────────────────────────────────────
const FR: UiStrings = {
  // ─── Navbar ──────────────────────────────────────────────────────────────────
  nav: { inicio: 'Accueil', locales: 'Établissements', nosotros: 'À propos', contacto: 'Contact', menu: 'Menu', pedidos: 'Commandes', perfil: 'Profil' },
  
  // ─── Footer ──────────────────────────────────────────────────────────────────
  footer: { rights: 'Tous droits réservés', privacidad: 'Politique de confidentialité', terminos: 'Conditions d\'utilisation', legal: 'Mentions légales', locales: 'Nos établissements', nosotros: 'À propos de Bame', contacto: 'Contact', navigation: 'Navigation', legalSection: 'Légal' },
  
  // ─── Menú de Accesibilidad ──────────────────────────────────────────────────────────────────
  accessibility: { title: 'Accessibilité', language: 'Langue', fontSize: 'Taille du texte', accept: 'Accepter', small: 'Petit', medium: 'Moyen', large: 'Grand', themeLight: 'Passer au thème clair', themeDark: 'Passer au thème sombre', colorBlind: 'Vision · Daltonisme', cbNone: 'Sans filtre', cbRG: 'Rouge–vert', cbBY: 'Bleu–jaune', modeDark: 'Mode sombre', modeLight: 'Mode clair' },
  
  // ─── Días de la Semana ──────────────────────────────────────────────────────────────────
  days: { monday: 'Lundi', tuesday: 'Mardi', wednesday: 'Mercredi', thursday: 'Jeudi', friday: 'Vendredi', saturday: 'Samedi', sunday: 'Dimanche', mon: 'Lun', tue: 'Mar', wed: 'Mer', thu: 'Jeu', fri: 'Ven', sat: 'Sam', sun: 'Dim', closed: 'Fermé' },

  // ─── Comunes ──────────────────────────────────────────────────────────────────
  common: { back: 'Retour', close: 'Fermer', loading: 'Chargement…', error: 'Erreur', retry: 'Réessayer', accept: 'Accepter', cancel: 'Annuler', save: 'Enregistrer', send: 'Envoyer', required: 'obligatoire' },
  
  // ─── Reserva ──────────────────────────────────────────────────────────────────
  reservation: { loading: 'Chargement de la carte…', errorLoad: 'Impossible de charger la carte.', carta: 'Carte', addUnit: 'Ajouter une unité', removeUnit: 'Retirer une unité', cart: 'Votre commande', cartEmpty: 'Vous n\'avez encore rien ajouté.', subtotal: 'Sous-total', units: 'unités', unit: 'unité', total: 'Total', checkout: 'Poursuivre la commande', allergens: 'Allergènes', traces: 'Traces', sinStock: 'Épuisé', quedan: 'Restant', maximo10: 'Maximum 10 unités', confirmarPedido: 'Confirmer la commande' },
  
  // ─── Checkout ──────────────────────────────────────────────────────────────────
  checkout: {
    title: 'Confirmer la commande', yourOrder: 'Votre commande', total: 'Total', pay: 'Payer avec Stripe', back: 'Retour à la carte', processing: 'Traitement en cours…', errorConnect: 'Impossible de se connecter au serveur.', errorGeneric: 'Erreur lors du traitement du paiement. Veuillez réessayer.', testMode: 'Mode test : aucun prélèvement réel ne sera effectué.', security: 'Paiement sécurisé géré par Stripe.',
    paymentMethod: 'Moyen de paiement',
    onlineCard: 'Carte en ligne', onlineCardDesc: 'Paiement sécurisé avec Stripe',
    payAtStore: 'Payer sur place', payAtStorePayDesc: 'Vous payez au retrait',
    payWithStripe: 'Payer avec Stripe', confirmOrder: 'Confirmer la commande',
    stripeNote: 'Le paiement sera traité en toute sécurité via Stripe. Nous ne stockons pas les données de votre carte.',
    payAtStoreNote: 'Paiement sur place. Vous réglerez la commande lorsque vous viendrez la chercher.',
    authRequired: 'Connectez-vous pour continuer.', loginLink: 'se connecter',
    connectingStripe: 'Connexion à Stripe…', confirmingOrder: 'Confirmation de la commande…',
    orderItems: 'Articles', asaderoLabel: 'Rôtisserie', unitPrice: '/ u.',
    reviewOrder: 'Vérifiez votre commande et choisissez comment vous souhaitez payer.',
  },
  
  // ─── Autenticación ──────────────────────────────────────────────────────────────────
  auth: {
    login: 'Se connecter', register: 'S\'inscrire', logout: 'Se déconnecter', email: 'Adresse e-mail', password: 'Mot de passe', remember: 'Se souvenir de moi', forgotPassword: 'Mot de passe oublié ?', submit: 'Entrer', noAccount: 'Vous n\'avez pas encore de compte ?', hasAccount: 'Vous avez déjà un compte ?', registerHere: 'Inscrivez-vous ici', loginHere: 'Se connecter',
    loginAccessLabel: 'Accès', loginSubtitle: 'Accédez à votre compte pour gérer vos réservations.',
    emailPlaceholder: 'nom@exemple.com',
    enteringSession: 'Connexion en cours…', loginBtn: 'Entrer',
    loginWithGoogle: 'Se connecter avec Google',
    emailRequired: 'L\'e-mail est obligatoire.', emailInvalid: 'Saisissez un e-mail valide.', passwordRequired: 'Le mot de passe est obligatoire.',
    credentialsError: 'Identifiants incorrects. Vérifiez votre e-mail et votre mot de passe.',
    // Nuevas variables del formulario completo
    navAria: 'Navigation d\'authentification', brandAria: 'BAME, aller à l\'accueil', brandSub: 'RÔTISSERIE · MURCIA', a11yAria: 'Options d\'accessibilité',
    themeLightAria: 'Activer le mode clair', themeDarkAria: 'Activer le mode sombre', themeLight: 'Mode clair', themeDark: 'Mode sombre',
    loyaltyEyebrow: 'Points BAME', loyaltyTitle: 'Commandez plus vite<br />à chaque fois.', loyaltyDesc: 'Enregistrez vos données, répétez vos commandes en un clic et cumulez des points pour du poulet rôti gratuit.',
    sectionAria: 'Connexion BAME', successRegisterTitle: 'Compte créé !', successLoginTitle: 'Bon retour !',
    successRegisterDesc: 'Vous faites maintenant partie de BAME. Commencez à cumuler des points avec votre première commande.', successLoginDesc: 'Vous êtes connecté à votre compte avec succès.',
    continueBtn: 'Continuer →', tabsAria: 'Type d\'accès', tabLogin: 'Se connecter', tabRegister: 'Créer un compte',
    headerRegister: 'Créez votre compte', headerLogin: 'Connectez-vous à votre compte', headerDescRegister: 'Cela ne prendra qu\'une minute.', headerDescLogin: 'Entrez vos informations pour continuer.',
    requiredLegend: 'Indique un champ obligatoire', nameLabel: 'Nom', namePlaceholder: 'Votre nom', phoneLabel: 'Numéro de téléphone',
    hidePasswordAria: 'Masquer le mot de passe', showPasswordAria: 'Afficher le mot de passe', hide: 'Masquer', show: 'Voir',
    passwordStrengthAria: 'Force du mot de passe', securityLabel: 'Sécurité :', terms1: 'J\'accepte les', termsLink: 'conditions', terms2: 'et la', privacyLink: 'politique de confidentialité',
    loadingAuth: 'Un instant…', orContinueWith: 'ou continuer avec', continueWith: 'Continuer avec ', guestLink: 'Continuer en tant qu\'invité →',
    forgotPageTitle: 'Récupérer le mot de passe', forgotPageDesc: 'Entrez votre adresse e-mail et nous vous enverrons des instructions pour réinitialiser votre mot de passe.', forgotPageBtn: 'Envoyer les instructions',
    forgotPageSuccess: 'Si l\'e-mail existe dans notre base de données, vous recevrez des instructions pour réinitialiser votre mot de passe.', forgotPageError: 'La demande n\'a pas pu être envoyée. Veuillez réessayer plus tard.', backToLogin: 'Retour à la connexion'
  },
  
  // ─── Errores ──────────────────────────────────────────────────────────────────
  errors: { notFound: 'Page introuvable', notFoundDesc: 'La page que vous recherchez n\'existe pas ou a été déplacée.', unauthorized: 'Non autorisé', unauthorizedDesc: 'Vous devez vous connecter pour y accéder.', forbidden: 'Accès refusé', forbiddenDesc: 'Vous n\'avez pas la permission de voir cette page.', server: 'Erreur du serveur', serverDesc: 'Un problème est survenu. Réessayez plus tard.', backHome: 'Retour à l\'accueil', service: 'Service indisponible', serviceDesc: 'Maintenance en cours. Le service reviendra bientôt.', goBack: 'Retour', retry: 'Réessayer' },
  
  // ─── Locales ──────────────────────────────────────────────────────────────────
  locales: {
    title: 'Nos rôtisseries', subtitle: 'Choisissez votre établissement préféré', viewMenu: 'Voir la carte', reserve: 'Réserver', schedules: 'Horaires', closedToday: 'Fermé aujourd\'hui', today: 'Aujourd\'hui', phone: 'Téléphone', statusOpen: 'Ouvert', statusClosed: 'Fermé', statusSoon: 'Ouvre bientôt', statusClosingSoon: 'Ferme bientôt',
    loading: 'Chargement des établissements…', noLocals: 'Aucun établissement disponible pour le moment.',
    ctaTitle: 'Vous savez déjà où réserver ?', ctaDesc: 'Choisissez votre rôtisserie préférée et nous préparerons le poulet pour vous.', ctaBtn: 'Réserver maintenant',
    subtitle2: 'Sélectionnez la rôtisserie la plus proche, réservez votre poulet en quelques secondes et récupérez-le sans attendre.',
    noLocalsError: 'Impossible de charger les établissements', noLocalsErrorSub: 'Vérifiez votre connexion et réessayez.',
    todayHours: 'Aujourd\'hui :', directions: 'Itinéraire', orderHere: 'Commander ici',
    noResultsTitle: 'Aucun résultat', noResultsSub: 'Nous n\'avons pas trouvé d\'établissement pour',
    stripTitle1: 'Retrait sans file d\'attente', stripDesc1: 'Commandez en ligne et récupérez votre commande chaude en 20–30 min dans l\'un de nos établissements.',
    stripTitle2: 'Même goût, même carte', stripDesc2: 'Toutes les rôtisseries partagent notre carte et les recettes traditionnelles murciennes.',
    stripTitle3: 'Des questions ?', stripDesc3a: 'Écrivez-nous depuis la page de', stripDesc3b: 'ou appelez votre établissement.',
    searchPlaceholder: 'Rechercher par ville ou code postal…',
    localsSubtitle: 'rôtisseries réparties dans la région de Murcie. Choisissez la plus proche, passez votre commande et récupérez-la bien chaude.',
  },
  
  // ─── Home ──────────────────────────────────────────────────────────────────
  home: {
    hero: 'La meilleure rôtisserie de Murcie', heroSub: 'Poulet rôti et spécialités murciennes', viewLocals: 'Voir les établissements', seeMenu: 'Voir la carte',
    heroPart1: 'Poulet rôti,', heroHighlight: 'braise', heroPart2: 'et vrai goût',
    heroSubDetail: 'Réservez votre poulet dans les meilleures rôtisseries et food trucks de la région. Prêt à votre arrivée, sans attente ni file.',
    howItWorks: 'Comment ça marche',
    howTitle: 'Comment ça marche', howSubtitle: 'Trois étapes pour que votre poulet rôti soit prêt sans complications.',
    step1Title: 'Choisissez votre rôtisserie', step1Desc: 'Trouvez la rôtisserie ou le food truck le plus proche et consultez la disponibilité en temps réel.',
    step2Title: 'Réservez votre poulet', step2Desc: 'Choisissez vos produits et confirmez votre commande instantanément. Sans paiement en ligne, sans complications.',
    step3Title: 'Récupérez-le sans attendre', step3Desc: 'Arrivez à la rôtisserie et récupérez directement votre commande. Sans files d\'attente, sans surprises, sans perdre de temps.',
    valueTagline: 'Pour ceux qui valorisent leur temps', valueTitle: 'Votre poulet prêt à votre arrivée',
    valueDesc: 'Évitez les files d\'attente, assurez-vous de la disponibilité et trouvez la rôtisserie la plus proche. Avec Bame, votre temps passe avant tout — réservez en quelques secondes et profitez sans attendre.',
    benefit1: 'Pas de files d\'attente ni d\'attente', benefit1Desc: 'Votre commande sera prête exactement quand vous arriverez à la rôtisserie.',
    benefit2: 'Disponibilité en temps réel', benefit2Desc: 'Vérifiez si votre rôtisserie préférée est ouverte avant de quitter la maison.',
    benefit3: 'Confirmation immédiate', benefit3Desc: 'Votre réservation est confirmée instantanément, sans paiement préalable.',
    ctaTitle: 'Prêt à réserver ?', ctaDesc: 'Choisissez votre rôtisserie préférée, réservez en quelques secondes et récupérez sans attendre.',
    ctaBtn: 'Voir les établissements disponibles', waitLabel: 'Temps d\'attente', waitValue: 'Prêt à votre arrivée',
    heroTitle: 'Poulet rôti, fraîchement préparé.', heroDesc: 'Rôti lentement avec des épices dans son propre jus. Commandez en ligne et récupérez-le chaud dans votre établissement à Murcie. Pas de files d\'attente, pas de frais de livraison.',
    seeLocal: 'Voir l\'établissement', exploreMenu: 'Explorer la carte', viewAll: 'Tout voir →', dishes: 'plats',
    topOrders: 'Les plus commandés', topFavs: 'Les favoris du quartier', howOrderTitle: 'Commander est aussi simple que ça',
    chooseLocal: 'Choisissez votre rôtisserie', localsRegion: 'établissements dans la région de Murcie', hoursLabel: 'HORAIRES',
    callBtn: 'Appeler', orderHereBtn: 'Commander ici →',
    ctaHungryTitle: 'Déjà faim ?', ctaHungrySub: 'Votre poulet rôti peut être prêt en 20 minutes.', startOrderBtn: 'Commencer ma commande →',
    homeStep1Title: 'Choisissez vos plats', homeStep1Desc: 'Explorez la carte et ajoutez ce qui vous fait le plus envie au panier.',
    homeStep2Title: 'Payez comme vous préférez', homeStep2Desc: 'Carte en ligne, Bizum ou en espèces au retrait. Vous décidez.',
    homeStep3Title: 'Récupérez chaud', homeStep3Desc: 'En 20–30 min votre commande est prête. Pas de files ni d\'attente.',
  },
  
  // ─── Contacto ──────────────────────────────────────────────────────────────────
  contact: {
    title: 'Besoin d\'aide ?', subtitle: 'Écrivez-nous si vous avez des questions.', send: 'Envoyer le message', name: 'Nom', subject: 'Sujet', message: 'Message', successTitle: 'Message envoyé', successDesc: 'Merci de contacter Bame. Nous vous répondrons dans les plus brefs délais.',
    heroTitle: 'Parlons-en', heroDesc: 'Vous avez une question sur votre commande, une suggestion ou vous souhaitez organiser un événement ? Écrivez-nous ou contactez directement votre rôtisserie.',
    formTitle: 'Envoyez-nous un message', requiredFields: 'Champs obligatoires',
    namePlaceholder: 'Votre nom', phoneLabel: 'TÉLÉPHONE', emailLabel: 'ADRESSE E-MAIL',
    subjectLabel: 'SUJET', messagePlaceholder: 'Dites-nous comment nous pouvons vous aider…',
    privacyCheck: 'J\'ai lu et j\'accepte la politique de confidentialité et le traitement de mes données.',
    successPart1: 'Merci, ', successPart2: '. Nous vous répondrons à ', successPart3: ' en moins de 24 heures.',
    anotherMsg: 'Envoyer un autre message', supportLabel: 'SERVICE CLIENT',
    supportHours: 'Du lundi au dimanche, de 09:30 à 13:30.',
    supportNote: 'Pour les commandes en cours, contactez directement votre établissement.',
    localContactTitle: 'Contactez votre établissement', localsCount: 'rôtisseries dans la Région de Murcie',
    faqTitle: 'Foire aux questions',
    faq1q: 'Combien de temps faut-il pour que ma commande soit prête ?', faq1a: 'Les commandes à emporter sont généralement prêtes en 20–30 minutes. Nous vous prévenons quand vous pouvez passer la récupérer.',
    faq2q: 'Puis-je payer sur place ?', faq2a: 'Oui. Vous pouvez payer en ligne par carte ou Bizum, ou bien en espèces ou par carte lors du retrait de votre commande à la rôtisserie.',
    faq3q: 'Prenez-vous des commandes pour des événements ou de grands groupes ?', faq3a: 'Bien sûr. Écrivez-nous avec pour objet "Événements et traiteur" en indiquant la date, le nombre de personnes et l\'établissement, et nous vous préparerons un devis.',
    faq4q: 'Où puis-je consulter les allergènes de chaque plat ?', faq4a: 'Chaque produit de la carte affiche ses allergènes et traces possibles lorsque vous ouvrez sa fiche. En cas de doute, demandez à votre établissement.',
    subject1: 'Réservations et commandes', subject2: 'Suggestions', subject3: 'Réclamations', subject4: 'Événements et traiteur', subject5: 'Travaillez avec nous',
    callUs: 'APPELEZ-NOUS', emailSend: 'Envoyer un e-mail',
    errName: 'Indiquez votre nom.', errEmail: 'Saisissez un e-mail valide.', errMsg: 'Écrivez votre message.', errPrivacy: 'Vous devez accepter la politique de confidentialité.',
  },
  
  // ─── Acerca de Bame ──────────────────────────────────────────────────────────────────
  about: { title: 'À propos de Bame' },
  
  // ─── Legal ──────────────────────────────────────────────────────────────────
  legal: { privacy: 'Politique de confidentialité', terms: 'Conditions d\'utilisation', cookies: 'Politique en matière de cookies' },
  
  // ─── Pagar ──────────────────────────────────────────────────────────────────
  payment: {
    successTitle: 'Paiement réussi !', successDesc: 'Votre paiement a été traité avec succès.', cancelTitle: 'Paiement annulé', cancelDesc: 'Aucun prélèvement n\'a été effectué. Votre commande est toujours sauvegardée dans le panier.', retry: 'Réessayer le paiement',
    successStripeDesc: 'Votre paiement a été traité avec succès via Stripe.',
    testModeTitle: 'Mode test :', testModeDesc: 'Aucun prélèvement réel ne sera effectué. Il s\'agit d\'un environnement de test.',
    payAtStoreSuccessTitle: 'Commande confirmée !', payAtStoreSuccessDesc: 'Votre commande a été enregistrée avec succès.',
    payAtStorePickup: 'N\'oubliez pas que vous paierez le montant lors du retrait dans l\'établissement.',
    payAtStoreContact: 'Si vous avez des questions, contactez directement la rôtisserie.',
    payAtStoreMethodTitle: 'Méthode : Payer sur place.', payAtStoreMethodDesc: 'L\'établissement recevra votre commande et elle sera prête à votre arrivée.',
    cancelDesc2: 'Vous pouvez réessayer le paiement ou modifier votre sélection avant de continuer.',
    retryBtn: 'Réessayer le paiement', viewLocals: 'Voir les rôtisseries',
    moreLocals: 'Voir plus de rôtisseries', backHome: 'Retour à l\'accueil',
    successQuestion: 'Si vous avez des questions, contactez directement l\'établissement.',
    testModeNote: 'Utilisez la carte de test',
  },
  
  // ─── Perfil ──────────────────────────────────────────────────────────────────
  profile: {
    title: 'Mon profil', loading: 'Chargement de votre profil…',
    personalInfo: 'Informations personnelles', nameLabel: 'Nom', emailLabel: 'E-mail', roleLabel: 'Rôle',
    roleCustomer: 'Client',
    recentOrders: 'Commandes récentes', noOrders: 'Vous n\'avez pas encore de commandes.',
    preferences: 'Préférences', language: 'Langue',
    reserveBtn: 'Passer une commande', orderTotal: 'Total', orderStatus: 'Statut',
    memberSince: 'Membre depuis', pointsTitle: 'Points BAME', pointsUnit: 'points',
    pointsToGo: 'Il vous manque', pointsForFree: 'points pour obtenir un demi-poulet rôti gratuit. Vous gagnez 1 point pour chaque € dépensé.',
    personalData: 'Données personnelles', editBtn: 'Modifier', cancelBtn: 'Annuler',
    nameField: 'Nom', emailField: 'E-mail', phoneField: 'Téléphone',
    saveChanges: 'Enregistrer les modifications', paymentMethod: 'Moyen de paiement', defaultCard: 'Par défaut', addCard: '+ Ajouter une carte',
    tabCuenta: 'Compte', tabPedidos: 'Commandes', tabFavoritos: 'Favoris', tabAjustes: 'Paramètres',
    pastOrders: 'Commandes précédentes', repeatOrder: '↺ Répéter la commande',
    noOrders2: 'Vous n\'avez pas encore passé de commande.', viewMenuLink: 'Voir la carte →',
    favLocalLabel: 'VOTRE ÉTABLISSEMENT FAVORI', favLocalDesc: 'Enregistrez un établissement favori depuis la liste des établissements.',
    viewLocalsLink: 'Voir les établissements →', savedDishes: 'Plats sauvegardés',
    noDishes: 'Vous n\'avez pas encore de plats sauvegardés', noDishesDesc: 'Ajoutez ceux que vous préférez depuis la carte.',
    notifOrders: 'Alertes de commande', notifOrdersDesc: 'Notifications de l\'état de votre commande',
    notifOffers: 'Offres et nouveautés', notifOffersDesc: 'Promotions par e-mail',
    notifSms: 'SMS', notifSmsDesc: 'Alertes par message texte',
    darkTheme: 'Thème sombre', darkThemeDesc: 'Changer l\'apparence de l\'application',
    languageLabel: 'Langue', languageDesc: 'Langue de l\'application',
    logoutBtn: 'Se déconnecter', loadingOrders: 'Chargement des commandes…',
    stepReceived: 'Reçue', stepPreparing: 'En préparation', stepReady: 'Prête',
  },
  
  // ─── Menú de Usuario ──────────────────────────────────────────────────────────────────
  userMenu: { ariaLabel: 'Menu utilisateur', profile: 'Mon profil', logout: 'Se déconnecter', loginLink: 'Se connecter', registerLink: 'S\'inscrire' },
};

// ─── Italiano ─────────────────────────────────────────────────────────────────
const IT: UiStrings = {
  // ─── Navbar ──────────────────────────────────────────────────────────────────
  nav: { inicio: 'Home', locales: 'Locali', nosotros: 'Chi siamo', contacto: 'Contatti', menu: 'Menu', pedidos: 'Ordini', perfil: 'Profilo' },
  
  // ─── Footer ──────────────────────────────────────────────────────────────────
  footer: { rights: 'Tutti i diritti riservati', privacidad: 'Informativa sulla privacy', terminos: 'Termini di utilizzo', legal: 'Note legali', locales: 'I nostri locali', nosotros: 'Informazioni su Bame', contacto: 'Contatti', navigation: 'Navigazione', legalSection: 'Legale' },
  
  // ─── Menú de Accesibilidad ──────────────────────────────────────────────────────────────────
  accessibility: { title: 'Accessibilità', language: 'Lingua', fontSize: 'Dimensione testo', accept: 'Accetta', small: 'Piccolo', medium: 'Medio', large: 'Grande', themeLight: 'Passa al tema chiaro', themeDark: 'Passa al tema scuro', colorBlind: 'Visione · Daltonismo', cbNone: 'Nessun filtro', cbRG: 'Rosso–verde', cbBY: 'Blu–giallo', modeDark: 'Modalità scura', modeLight: 'Modalità chiara' },
  
  // ─── Días de la Semana ──────────────────────────────────────────────────────────────────
  days: { monday: 'Lunedì', tuesday: 'Martedì', wednesday: 'Mercoledì', thursday: 'Giovedì', friday: 'Venerdì', saturday: 'Sabato', sunday: 'Domenica', mon: 'Lun', tue: 'Mar', wed: 'Mer', thu: 'Gio', fri: 'Ven', sat: 'Sab', sun: 'Dom', closed: 'Chiuso' },

  // ─── Comunes ──────────────────────────────────────────────────────────────────
  common: { back: 'Indietro', close: 'Chiudi', loading: 'Caricamento…', error: 'Errore', retry: 'Riprova', accept: 'Accetta', cancel: 'Annulla', save: 'Salva', send: 'Invia', required: 'obbligatorio' },
  
  // ─── Reserva ──────────────────────────────────────────────────────────────────
  reservation: { loading: 'Caricamento menu…', errorLoad: 'Impossibile caricare il menu.', carta: 'Menu', addUnit: 'Aggiungi unità', removeUnit: 'Rimuovi unità', cart: 'Il tuo ordine', cartEmpty: 'Non hai ancora aggiunto nulla.', subtotal: 'Subtotale', units: 'unità', unit: 'unità', total: 'Totale', checkout: 'Procedi all\'ordine', allergens: 'Allergeni', traces: 'Tracce', sinStock: 'Esaurito', quedan: 'Rimangono', maximo10: 'Massimo 10 unità', confirmarPedido: 'Conferma ordine' },
  
  // ─── Checkout ──────────────────────────────────────────────────────────────────
  checkout: {
    title: 'Conferma ordine', yourOrder: 'Il tuo ordine', total: 'Totale', pay: 'Paga con Stripe', back: 'Torna al menu', processing: 'Elaborazione…', errorConnect: 'Impossibile connettersi al server.', errorGeneric: 'Errore durante l\'elaborazione del pagamento. Riprova.', testMode: 'Modalità test: non verranno effettuati addebiti reali.', security: 'Pagamento sicuro gestito da Stripe.',
    paymentMethod: 'Metodo di pagamento',
    onlineCard: 'Carta online', onlineCardDesc: 'Pagamento sicuro con Stripe',
    payAtStore: 'Paga nel locale', payAtStorePayDesc: 'Paghi al ritiro',
    payWithStripe: 'Paga con Stripe', confirmOrder: 'Conferma ordine',
    stripeNote: 'Il pagamento verrà elaborato in modo sicuro tramite Stripe. Non memorizziamo i dati della tua carta.',
    payAtStoreNote: 'Pagamento di persona. Pagherai l\'ordine quando lo ritirerai nel locale.',
    authRequired: 'Accedi per continuare.', loginLink: 'accedi',
    connectingStripe: 'Connessione a Stripe…', confirmingOrder: 'Conferma dell\'ordine…',
    orderItems: 'Articoli', asaderoLabel: 'Rosticceria', unitPrice: '/ pz.',
    reviewOrder: 'Controlla il tuo ordine e scegli come vuoi pagare.',
  },
  
  // ─── Autenticación ──────────────────────────────────────────────────────────────────
  auth: {
    login: 'Accedi', register: 'Registrati', logout: 'Esci', email: 'Indirizzo e-mail', password: 'Password', remember: 'Ricordami', forgotPassword: 'Hai dimenticato la password?', submit: 'Entra', noAccount: 'Non hai ancora un account?', hasAccount: 'Hai già un account?', registerHere: 'Registrati qui', loginHere: 'Accedi',
    loginAccessLabel: 'Accesso', loginSubtitle: 'Accedi al tuo account per gestire le tue prenotazioni.',
    emailPlaceholder: 'nome@esempio.com',
    enteringSession: 'Accesso in corso…', loginBtn: 'Entra',
    loginWithGoogle: 'Accedi con Google',
    emailRequired: 'L\'e-mail è obbligatoria.', emailInvalid: 'Inserisci un\'e-mail valida.', passwordRequired: 'La password è obbligatoria.',
    credentialsError: 'Credenziali errate. Controlla e-mail e password.',
    // Nuevas variables del formulario completo
    navAria: 'Navigazione di autenticazione', brandAria: 'BAME, vai alla home', brandSub: 'GRILL · MURCIA', a11yAria: 'Opzioni di accessibilità',
    themeLightAria: 'Attiva la modalità chiara', themeDarkAria: 'Attiva la modalità scura', themeLight: 'Modalità chiara', themeDark: 'Modalità scura',
    loyaltyEyebrow: 'Punti BAME', loyaltyTitle: 'Ordina più velocemente<br />ogni volta.', loyaltyDesc: 'Salva i tuoi dati, ripeti gli ordini con un tocco e accumula punti per pollo arrosto gratis.',
    sectionAria: 'Accesso BAME', successRegisterTitle: 'Account creato!', successLoginTitle: 'Bentornato!',
    successRegisterDesc: 'Ora fai parte di BAME. Inizia ad accumulare punti con il tuo primo ordine.', successLoginDesc: 'Hai effettuato l\'accesso al tuo account con successo.',
    continueBtn: 'Continua →', tabsAria: 'Tipo di accesso', tabLogin: 'Accedi', tabRegister: 'Crea account',
    headerRegister: 'Crea il tuo account', headerLogin: 'Accedi al tuo account', headerDescRegister: 'Ci vorrà solo un minuto.', headerDescLogin: 'Inserisci i tuoi dati per continuare.',
    requiredLegend: 'Indica un campo obbligatorio', nameLabel: 'Nome', namePlaceholder: 'Il tuo nome', phoneLabel: 'Numero di telefono',
    hidePasswordAria: 'Nascondi password', showPasswordAria: 'Mostra password', hide: 'Nascondi', show: 'Mostra',
    passwordStrengthAria: 'Forza della password', securityLabel: 'Sicurezza:', terms1: 'Accetto i', termsLink: 'termini', terms2: 'e l\'', privacyLink: 'informativa sulla privacy',
    loadingAuth: 'Un attimo…', orContinueWith: 'o continua con', continueWith: 'Continua con ', guestLink: 'Continua come ospite →',
    forgotPageTitle: 'Recupera password', forgotPageDesc: 'Inserisci il tuo indirizzo e-mail e ti invieremo le istruzioni per reimpostare la password.', forgotPageBtn: 'Invia istruzioni',
    forgotPageSuccess: 'Se l\'e-mail esiste nel nostro database, riceverai le istruzioni per reimpostare la password.', forgotPageError: 'Impossibile inviare la richiesta. Riprova più tardi.', backToLogin: 'Torna al login'
  },
  
  // ─── Errores ──────────────────────────────────────────────────────────────────
  errors: { notFound: 'Pagina non trovata', notFoundDesc: 'La pagina che cerchi non esiste o è stata spostata.', unauthorized: 'Non autorizzato', unauthorizedDesc: 'Devi accedere per continuare.', forbidden: 'Accesso negato', forbiddenDesc: 'Non hai i permessi per visualizzare questa pagina.', server: 'Errore del server', serverDesc: 'Qualcosa è andato storto. Riprova più tardi.', backHome: 'Torna alla home', service: 'Servizio non disponibile', serviceDesc: 'Manutenzione in corso. Il servizio tornerà presto.', goBack: 'Torna indietro', retry: 'Riprova' },
  
  // ─── Locales ──────────────────────────────────────────────────────────────────
  locales: {
    title: 'Le nostre rosticcerie', subtitle: 'Scegli il tuo locale preferito', viewMenu: 'Vedi menu', reserve: 'Prenota', schedules: 'Orari', closedToday: 'Chiuso oggi', today: 'Oggi', phone: 'Telefono', statusOpen: 'Aperto', statusClosed: 'Chiuso', statusSoon: 'Apre presto', statusClosingSoon: 'Chiude presto',
    loading: 'Caricamento locali…', noLocals: 'Nessun locale disponibile al momento.',
    ctaTitle: 'Sai già dove prenotare?', ctaDesc: 'Scegli la tua rosticceria preferita e ti prepareremo il pollo da ritirare.', ctaBtn: 'Prenota ora',
    subtitle2: 'Seleziona la rosticceria più vicina, prenota il tuo pollo in pochi secondi e ritiralo senza attese.',
    noLocalsError: 'Impossibile caricare i locali', noLocalsErrorSub: 'Controlla la tua connessione e riprova.',
    todayHours: 'Oggi:', directions: 'Come arrivare', orderHere: 'Ordina qui',
    noResultsTitle: 'Nessun risultato', noResultsSub: 'Non abbiamo trovato locali per',
    stripTitle1: 'Ritiro senza fila', stripDesc1: 'Ordina online e ritira il tuo ordine caldo in 20-30 min in uno qualsiasi dei nostri locali.',
    stripTitle2: 'Stesso sapore, stesso menu', stripDesc2: 'Tutte le rosticcerie condividono il nostro menu e le ricette tradizionali murciane.',
    stripTitle3: 'Dubbi?', stripDesc3a: 'Scrivici dalla pagina dei', stripDesc3b: 'o chiama il tuo locale.',
    searchPlaceholder: 'Cerca per città o CAP…',
    localsSubtitle: 'rosticcerie sparse per la Regione di Murcia. Scegli la più vicina, fai il tuo ordine e ritiralo caldo.',
  },
  
  // ─── Home ──────────────────────────────────────────────────────────────────
  home: {
    hero: 'La migliore rosticceria di Murcia', heroSub: 'Pollo arrosto e specialità murciane', viewLocals: 'Vedi locali', seeMenu: 'Vedi menu',
    heroPart1: 'Pollo arrosto,', heroHighlight: 'alla brace', heroPart2: 'e sapore vero',
    heroSubDetail: 'Prenota il tuo pollo nelle migliori rosticcerie e food truck della regione. Pronto quando arrivi, senza attese né file.',
    howItWorks: 'Come funziona',
    howTitle: 'Come funziona', howSubtitle: 'Tre passaggi per avere il tuo pollo arrosto pronto senza complicazioni.',
    step1Title: 'Scegli la tua rosticceria', step1Desc: 'Trova la rosticceria o food truck più vicino e controlla la disponibilità in tempo reale.',
    step2Title: 'Prenota il tuo pollo', step2Desc: 'Scegli i tuoi prodotti e conferma il tuo ordine all\'istante. Nessun pagamento online, nessuna complicazione.',
    step3Title: 'Ritiralo senza attese', step3Desc: 'Arriva in rosticceria e ritira direttamente il tuo ordine. Senza file, senza sorprese, senza perdere tempo.',
    valueTagline: 'Per chi dà valore al proprio tempo', valueTitle: 'Il tuo pollo pronto quando arrivi',
    valueDesc: 'Evita le file, assicurati la disponibilità e trova la rosticceria più vicina. Con Bame, il tuo tempo viene prima di tutto — prenota in pochi secondi e divertiti senza aspettare.',
    benefit1: 'Niente file o attese', benefit1Desc: 'Il tuo ordine sarà pronto esattamente quando arriverai nel locale.',
    benefit2: 'Disponibilità in tempo reale', benefit2Desc: 'Controlla se la tua rosticceria preferita è aperta prima di uscire di casa.',
    benefit3: 'Conferma immediata', benefit3Desc: 'La tua prenotazione è confermata all\'istante, senza necessità di pagamento anticipato.',
    ctaTitle: 'Pronto per prenotare?', ctaDesc: 'Scegli la tua rosticceria preferita, prenota in pochi secondi e ritira senza aspettare.',
    ctaBtn: 'Vedi locali disponibili', waitLabel: 'Tempo di attesa', waitValue: 'Pronto al tuo arrivo',
    heroTitle: 'Pollo arrosto, appena fatto.', heroDesc: 'Arrostito lentamente con spezie nel proprio succo. Ordina online e ritiralo caldo nel tuo locale di Murcia. Niente file, niente spese di spedizione.',
    seeLocal: 'Vedi il locale', exploreMenu: 'Esplora il menu', viewAll: 'Vedi tutto →', dishes: 'piatti',
    topOrders: 'I più ordinati', topFavs: 'I preferiti del quartiere', howOrderTitle: 'Ordinare è così facile',
    chooseLocal: 'Scegli la tua rosticceria', localsRegion: 'locali nella Regione di Murcia', hoursLabel: 'ORARI',
    callBtn: 'Chiama', orderHereBtn: 'Ordina qui →',
    ctaHungryTitle: 'Hai già fame?', ctaHungrySub: 'Il tuo pollo arrosto può essere pronto in 20 minuti.', startOrderBtn: 'Inizia il mio ordine →',
    homeStep1Title: 'Scegli i tuoi piatti', homeStep1Desc: 'Esplora il menu e aggiungi ciò che più ti piace al carrello.',
    homeStep2Title: 'Paga come preferisci', homeStep2Desc: 'Carta online, Bizum o in contanti al ritiro. Decidi tu.',
    homeStep3Title: 'Ritira caldo', homeStep3Desc: 'In 20-30 min il tuo ordine è pronto. Niente file o attese.',
  },
  
  // ─── Contacto ──────────────────────────────────────────────────────────────────
  contact: {
    title: 'Hai bisogno di aiuto?', subtitle: 'Scrivici se hai dubbi.', send: 'Invia messaggio', name: 'Nome', subject: 'Oggetto', message: 'Messaggio', successTitle: 'Messaggio inviato', successDesc: 'Grazie per aver contattato Bame. Ti risponderemo il prima possibile.',
    heroTitle: 'Parliamo', heroDesc: 'Hai una domanda sul tuo ordine, un suggerimento o vuoi organizzare un evento? Scrivici o contatta direttamente la tua rosticceria.',
    formTitle: 'Inviaci un messaggio', requiredFields: 'Campi obbligatori',
    namePlaceholder: 'Il tuo nome', phoneLabel: 'TELEFONO', emailLabel: 'INDIRIZZO E-MAIL',
    subjectLabel: 'OGGETTO', messagePlaceholder: 'Dicci come possiamo aiutarti…',
    privacyCheck: 'Ho letto e accetto l\'informativa sulla privacy e il trattamento dei miei dati.',
    successPart1: 'Grazie, ', successPart2: '. Ti risponderemo a ', successPart3: ' in meno di 24 ore.',
    anotherMsg: 'Invia un altro messaggio', supportLabel: 'SERVIZIO CLIENTI',
    supportHours: 'Dal lunedì alla domenica, dalle 09:30 alle 13:30.',
    supportNote: 'Per ordini in corso, contatta direttamente il tuo locale.',
    localContactTitle: 'Contatta il tuo locale', localsCount: 'rosticcerie nella Regione di Murcia',
    faqTitle: 'Domande frequenti',
    faq1q: 'Quanto tempo ci vuole prima che il mio ordine sia pronto?', faq1a: 'Gli ordini da asporto sono di solito pronti in 20-30 minuti. Ti avviseremo quando potrai passare a ritirarlo.',
    faq2q: 'Posso pagare nel locale?', faq2a: 'Sì. Puoi pagare online con carta o Bizum, oppure in contanti o con carta al ritiro del tuo ordine in rosticceria.',
    faq3q: 'Fate ordini per eventi o gruppi numerosi?', faq3a: 'Certo. Scrivici con oggetto "Eventi e catering" indicando data, numero di persone e locale, e ti prepareremo un preventivo.',
    faq4q: 'Dove posso consultare gli allergeni di ogni piatto?', faq4a: 'Ogni prodotto nel menu mostra i suoi allergeni e le possibili tracce quando apri la sua scheda. Se hai dubbi, chiedi al tuo locale.',
    subject1: 'Prenotazioni e ordini', subject2: 'Suggerimenti', subject3: 'Reclami', subject4: 'Eventi e catering', subject5: 'Lavora con noi',
    callUs: 'CHIAMACI', emailSend: 'Invia e-mail',
    errName: 'Inserisci il tuo nome.', errEmail: 'Inserisci un\'e-mail valida.', errMsg: 'Scrivi il tuo messaggio.', errPrivacy: 'Devi accettare l\'informativa sulla privacy.',
  },
  
  // ─── Acerca de Bame ──────────────────────────────────────────────────────────────────
  about: { title: 'Informazioni su Bame' },
  
  // ─── Legal ──────────────────────────────────────────────────────────────────
  legal: { privacy: 'Informativa sulla privacy', terms: 'Termini di utilizzo', cookies: 'Informativa sui cookie' },
  
  // ─── Pagar ──────────────────────────────────────────────────────────────────
  payment: {
    successTitle: 'Pagamento completato!', successDesc: 'Il tuo pagamento è stato elaborato correttamente.', cancelTitle: 'Pagamento annullato', cancelDesc: 'Nessun addebito è stato effettuato. Il tuo ordine è ancora salvato nel carrello.', retry: 'Riprova il pagamento',
    successStripeDesc: 'Il tuo pagamento è stato elaborato correttamente tramite Stripe.',
    testModeTitle: 'Modalità test:', testModeDesc: 'Nessun addebito reale verrà effettuato. Questo è un ambiente di test.',
    payAtStoreSuccessTitle: 'Ordine confermato!', payAtStoreSuccessDesc: 'Il tuo ordine è stato registrato correttamente.',
    payAtStorePickup: 'Ricorda che pagherai l\'importo al momento del ritiro nel locale.',
    payAtStoreContact: 'Se hai dubbi, contatta direttamente la rosticceria.',
    payAtStoreMethodTitle: 'Metodo: Paga nel locale.', payAtStoreMethodDesc: 'Il locale riceverà il tuo ordine e sarà pronto al tuo arrivo.',
    cancelDesc2: 'Puoi riprovare il pagamento o modificare la tua selezione prima di continuare.',
    retryBtn: 'Riprova il pagamento', viewLocals: 'Vedi rosticcerie',
    moreLocals: 'Vedi altre rosticcerie', backHome: 'Torna alla home',
    successQuestion: 'Se hai dubbi, contatta direttamente il locale.',
    testModeNote: 'Usa la carta di test',
  },
  
  // ─── Perfil ──────────────────────────────────────────────────────────────────
  profile: {
    title: 'Il mio profilo', loading: 'Caricamento del tuo profilo…',
    personalInfo: 'Informazioni personali', nameLabel: 'Nome', emailLabel: 'E-mail', roleLabel: 'Ruolo',
    roleCustomer: 'Cliente',
    recentOrders: 'Ordini recenti', noOrders: 'Non hai ancora ordini.',
    preferences: 'Preferenze', language: 'Lingua',
    reserveBtn: 'Fai un ordine', orderTotal: 'Totale', orderStatus: 'Stato',
    memberSince: 'Membro dal', pointsTitle: 'Punti BAME', pointsUnit: 'punti',
    pointsToGo: 'Ti mancano', pointsForFree: 'punti per avere mezzo pollo arrosto gratis. Guadagni 1 punto per ogni €.',
    personalData: 'Dati personali', editBtn: 'Modifica', cancelBtn: 'Annulla',
    nameField: 'Nome', emailField: 'E-mail', phoneField: 'Telefono',
    saveChanges: 'Salva le modifiche', paymentMethod: 'Metodo di pagamento', defaultCard: 'Predefinito', addCard: '+ Aggiungi carta',
    tabCuenta: 'Conto', tabPedidos: 'Ordini', tabFavoritos: 'Preferiti', tabAjustes: 'Impostazioni',
    pastOrders: 'Ordini precedenti', repeatOrder: '↺ Ripeti ordine',
    noOrders2: 'Non hai ancora effettuato ordini.', viewMenuLink: 'Vedi il menu →',
    favLocalLabel: 'IL TUO LOCALE PREFERITO', favLocalDesc: 'Salva un locale preferito dalla selezione dei locali.',
    viewLocalsLink: 'Vedi locali →', savedDishes: 'Piatti salvati',
    noDishes: 'Non hai ancora piatti salvati', noDishesDesc: 'Aggiungi i tuoi preferiti dal menu.',
    notifOrders: 'Avvisi sugli ordini', notifOrdersDesc: 'Notifiche sullo stato del tuo ordine',
    notifOffers: 'Offerte e novità', notifOffersDesc: 'Promozioni via e-mail',
    notifSms: 'SMS', notifSmsDesc: 'Avvisi via messaggio di testo',
    darkTheme: 'Tema scuro', darkThemeDesc: 'Cambia l\'aspetto dell\'app',
    languageLabel: 'Lingua', languageDesc: 'Lingua dell\'applicazione',
    logoutBtn: 'Esci', loadingOrders: 'Caricamento ordini…',
    stepReceived: 'Ricevuto', stepPreparing: 'In preparazione', stepReady: 'Pronto',
  },
  
  // ─── Menú de Usuario ──────────────────────────────────────────────────────────────────
  userMenu: { ariaLabel: 'Menu utente', profile: 'Il mio profilo', logout: 'Esci', loginLink: 'Accedi', registerLink: 'Registrati' },
};

// ─── Deutsch ──────────────────────────────────────────────────────────────────
const DE: UiStrings = {
  // ─── Navbar ──────────────────────────────────────────────────────────────────
  nav: { inicio: 'Startseite', locales: 'Standorte', nosotros: 'Über uns', contacto: 'Kontakt', menu: 'Menü', pedidos: 'Bestellungen', perfil: 'Profil' },
  
  // ─── Footer ──────────────────────────────────────────────────────────────────
  footer: { rights: 'Alle Rechte vorbehalten', privacidad: 'Datenschutzrichtlinie', terminos: 'Nutzungsbedingungen', legal: 'Impressum', locales: 'Unsere Standorte', nosotros: 'Über Bame', contacto: 'Kontakt', navigation: 'Navigation', legalSection: 'Rechtliches' },
  
  // ─── Menú de Accesibilidad ──────────────────────────────────────────────────────────────────
  accessibility: { title: 'Barrierefreiheit', language: 'Sprache', fontSize: 'Textgröße', accept: 'Akzeptieren', small: 'Klein', medium: 'Mittel', large: 'Groß', themeLight: 'Zum hellen Design wechseln', themeDark: 'Zum dunklen Design wechseln', colorBlind: 'Sehvermögen · Farbenblindheit', cbNone: 'Kein Filter', cbRG: 'Rot–Grün', cbBY: 'Blau–Gelb', modeDark: 'Dunkelmodus', modeLight: 'Heller Modus' },
  
  // ─── Días de la Semana ──────────────────────────────────────────────────────────────────
  days: { monday: 'Montag', tuesday: 'Dienstag', wednesday: 'Mittwoch', thursday: 'Donnerstag', friday: 'Freitag', saturday: 'Samstag', sunday: 'Sonntag', mon: 'Mo', tue: 'Di', wed: 'Mi', thu: 'Do', fri: 'Fr', sat: 'Sa', sun: 'So', closed: 'Geschlossen' },

  // ─── Comunes ──────────────────────────────────────────────────────────────────
  common: { back: 'Zurück', close: 'Schließen', loading: 'Wird geladen…', error: 'Fehler', retry: 'Erneut versuchen', accept: 'Akzeptieren', cancel: 'Abbrechen', save: 'Speichern', send: 'Senden', required: 'erforderlich' },
  
  // ─── Reserva ──────────────────────────────────────────────────────────────────
  reservation: { loading: 'Speisekarte wird geladen…', errorLoad: 'Die Speisekarte konnte nicht geladen werden.', carta: 'Speisekarte', addUnit: 'Einheit hinzufügen', removeUnit: 'Einheit entfernen', cart: 'Deine Bestellung', cartEmpty: 'Du hast noch nichts hinzugefügt.', subtotal: 'Zwischensumme', units: 'Einheiten', unit: 'Einheit', total: 'Gesamt', checkout: 'Zur Kasse gehen', allergens: 'Allergene', traces: 'Spuren', sinStock: 'Ausverkauft', quedan: 'Verbleibend', maximo10: 'Maximal 10 Einheiten', confirmarPedido: 'Bestellung bestätigen' },
  
  // ─── Checkout ──────────────────────────────────────────────────────────────────
  checkout: {
    title: 'Bestellung bestätigen', yourOrder: 'Deine Bestellung', total: 'Gesamt', pay: 'Mit Stripe bezahlen', back: 'Zurück zur Speisekarte', processing: 'Wird bearbeitet…', errorConnect: 'Verbindung zum Server fehlgeschlagen.', errorGeneric: 'Fehler bei der Zahlungsabwicklung. Bitte versuche es erneut.', testMode: 'Testmodus: Es werden keine echten Abbuchungen vorgenommen.', security: 'Sichere Zahlung unterstützt von Stripe.',
    paymentMethod: 'Zahlungsmethode',
    onlineCard: 'Online-Karte', onlineCardDesc: 'Sichere Zahlung mit Stripe',
    payAtStore: 'Vor Ort bezahlen', payAtStorePayDesc: 'Du bezahlst bei Abholung',
    payWithStripe: 'Mit Stripe bezahlen', confirmOrder: 'Bestellung bestätigen',
    stripeNote: 'Die Zahlung wird sicher über Stripe abgewickelt. Wir speichern deine Kartendaten nicht.',
    payAtStoreNote: 'Zahlung vor Ort. Du bezahlst die Bestellung, wenn du sie im Standort abholst.',
    authRequired: 'Melde dich an, um fortzufahren.', loginLink: 'anmelden',
    connectingStripe: 'Verbindung zu Stripe wird hergestellt…', confirmingOrder: 'Bestellung wird bestätigt…',
    orderItems: 'Artikel', asaderoLabel: 'Grill', unitPrice: '/ Stk.',
    reviewOrder: 'Überprüfe deine Bestellung und wähle aus, wie du bezahlen möchtest.',
  },
  
  // ─── Autenticación ──────────────────────────────────────────────────────────────────
  auth: {
    login: 'Anmelden', register: 'Registrieren', logout: 'Abmelden', email: 'E-Mail-Adresse', password: 'Passwort', remember: 'Angemeldet bleiben', forgotPassword: 'Passwort vergessen?', submit: 'Anmelden', noAccount: 'Noch kein Konto?', hasAccount: 'Hast du bereits ein Konto?', registerHere: 'Hier registrieren', loginHere: 'Anmelden',
    loginAccessLabel: 'Zugang', loginSubtitle: 'Melde dich an, um deine Reservierungen zu verwalten.',
    emailPlaceholder: 'name@beispiel.com',
    enteringSession: 'Anmeldung läuft…', loginBtn: 'Anmelden',
    loginWithGoogle: 'Mit Google anmelden',
    emailRequired: 'E-Mail ist erforderlich.', emailInvalid: 'Gib eine gültige E-Mail-Adresse ein.', passwordRequired: 'Passwort ist erforderlich.',
    credentialsError: 'Falsche Zugangsdaten. Überprüfe E-Mail und Passwort.',
    // Nuevas variables del formulario completo
    navAria: 'Authentifizierungsnavigation', brandAria: 'BAME, zur Startseite', brandSub: 'GRILL · MURCIA', a11yAria: 'Barrierefreiheitsoptionen',
    themeLightAria: 'Hellen Modus aktivieren', themeDarkAria: 'Dunklen Modus aktivieren', themeLight: 'Heller Modus', themeDark: 'Dunkler Modus',
    loyaltyEyebrow: 'BAME Punkte', loyaltyTitle: 'Jedes Mal<br />schneller bestellen.', loyaltyDesc: 'Speichere deine Daten, wiederhole Bestellungen mit einem Klick und sammle Punkte für gratis Brathähnchen.',
    sectionAria: 'BAME Login', successRegisterTitle: 'Konto erstellt!', successLoginTitle: 'Willkommen zurück!',
    successRegisterDesc: 'Du bist jetzt Teil von BAME. Sammle Punkte mit deiner ersten Bestellung.', successLoginDesc: 'Du hast dich erfolgreich in dein Konto eingeloggt.',
    continueBtn: 'Weiter →', tabsAria: 'Zugangsart', tabLogin: 'Anmelden', tabRegister: 'Konto erstellen',
    headerRegister: 'Erstelle dein Konto', headerLogin: 'Melde dich in deinem Konto an', headerDescRegister: 'Es dauert nur eine Minute.', headerDescLogin: 'Gib deine Daten ein, um fortzufahren.',
    requiredLegend: 'Kennzeichnet ein Pflichtfeld', nameLabel: 'Name', namePlaceholder: 'Dein Name', phoneLabel: 'Telefonnummer',
    hidePasswordAria: 'Passwort verbergen', showPasswordAria: 'Passwort anzeigen', hide: 'Verbergen', show: 'Zeigen',
    passwordStrengthAria: 'Passwortstärke', securityLabel: 'Sicherheit:', terms1: 'Ich akzeptiere die', termsLink: 'Bedingungen', terms2: 'und die', privacyLink: 'Datenschutzrichtlinie',
    loadingAuth: 'Einen Moment…', orContinueWith: 'oder weiter mit', continueWith: 'Weiter mit ', guestLink: 'Als Gast fortfahren →',
    forgotPageTitle: 'Passwort wiederherstellen', forgotPageDesc: 'Gib deine E-Mail-Adresse ein und wir senden dir Anweisungen zum Zurücksetzen deines Passworts.', forgotPageBtn: 'Anweisungen senden',
    forgotPageSuccess: 'Wenn die E-Mail in unserer Datenbank vorhanden ist, erhältst du Anweisungen zum Zurücksetzen deines Passworts.', forgotPageError: 'Die Anfrage konnte nicht gesendet werden. Bitte versuche es später erneut.', backToLogin: 'Zurück zur Anmeldung'
  },
  
  // ─── Errores ──────────────────────────────────────────────────────────────────
  errors: { notFound: 'Seite nicht gefunden', notFoundDesc: 'Die gesuchte Seite existiert nicht oder wurde verschoben.', unauthorized: 'Nicht autorisiert', unauthorizedDesc: 'Du musst dich anmelden, um fortzufahren.', forbidden: 'Zugriff verweigert', forbiddenDesc: 'Du hast keine Berechtigung, diese Seite anzuzeigen.', server: 'Serverfehler', serverDesc: 'Etwas ist schiefgelaufen. Versuche es später erneut.', backHome: 'Zurück zur Startseite', service: 'Dienst nicht verfügbar', serviceDesc: 'Wir führen Wartungsarbeiten durch. Der Dienst ist bald wieder verfügbar.', goBack: 'Zurück', retry: 'Erneut versuchen' },
  
  // ─── Locales ──────────────────────────────────────────────────────────────────
  locales: {
    title: 'Unsere Grills', subtitle: 'Wähle deinen Lieblingsstandort', viewMenu: 'Speisekarte ansehen', reserve: 'Bestellen', schedules: 'Öffnungszeiten', closedToday: 'Heute geschlossen', today: 'Heute', phone: 'Telefon', statusOpen: 'Geöffnet', statusClosed: 'Geschlossen', statusSoon: 'Öffnet bald', statusClosingSoon: 'Schließt bald',
    loading: 'Standorte werden geladen…', noLocals: 'Zurzeit sind keine Standorte verfügbar.',
    ctaTitle: 'Weißt du schon, wo du bestellen möchtest?', ctaDesc: 'Wähle deinen Lieblingsstandort und wir bereiten dein Hähnchen zur Abholung vor.', ctaBtn: 'Jetzt bestellen',
    subtitle2: 'Wähle den nächstgelegenen Standort, bestelle dein Hähnchen in Sekunden und hole es ohne Wartezeit ab.',
    noLocalsError: 'Standorte konnten nicht geladen werden', noLocalsErrorSub: 'Überprüfe deine Verbindung und versuche es erneut.',
    todayHours: 'Heute:', directions: 'Route', orderHere: 'Hier bestellen',
    noResultsTitle: 'Keine Ergebnisse', noResultsSub: 'Wir haben keine Standorte gefunden für',
    stripTitle1: 'Abholung ohne Warteschlange', stripDesc1: 'Bestelle online und hole deine heiße Bestellung in 20–30 Min an einem unserer Standorte ab.',
    stripTitle2: 'Gleicher Geschmack, gleiche Speisekarte', stripDesc2: 'Alle Standorte teilen unsere Speisekarte und die traditionellen murcianischen Rezepte.',
    stripTitle3: 'Fragen?', stripDesc3a: 'Schreibe uns über die', stripDesc3b: 'Seite oder rufe deinen Standort an.',
    searchPlaceholder: 'Suche nach Stadt oder Postleitzahl…',
    localsSubtitle: 'Standorte in der gesamten Region Murcia. Wähle den nächstgelegenen aus, gib deine Bestellung auf und hole sie heiß ab.',
  },
  
  // ─── Home ──────────────────────────────────────────────────────────────────
  home: {
    hero: 'Der beste Grill in Murcia', heroSub: 'Brathähnchen und murcianische Spezialitäten', viewLocals: 'Standorte ansehen', seeMenu: 'Speisekarte ansehen',
    heroPart1: 'Brathähnchen,', heroHighlight: 'vom Grill', heroPart2: 'und echter Geschmack',
    heroSubDetail: 'Bestelle dein Hähnchen bei den besten Grills und Foodtrucks der Region. Bereit, wenn du ankommst, ohne Warten oder Schlangestehen.',
    howItWorks: 'Wie es funktioniert',
    howTitle: 'Wie es funktioniert', howSubtitle: 'Drei Schritte, um dein Brathähnchen ohne Komplikationen bereit zu haben.',
    step1Title: 'Wähle deinen Standort', step1Desc: 'Finde den nächstgelegenen Grill oder Foodtruck und prüfe die Verfügbarkeit in Echtzeit.',
    step2Title: 'Bestelle dein Hähnchen', step2Desc: 'Wähle deine Produkte und bestätige deine Bestellung sofort. Keine Online-Zahlung, keine Komplikationen.',
    step3Title: 'Ohne Wartezeit abholen', step3Desc: 'Komme am Standort an und hole deine Bestellung direkt ab. Keine Schlangen, keine Überraschungen, keine Zeitverschwendung.',
    valueTagline: 'Für alle, die ihre Zeit schätzen', valueTitle: 'Dein Hähnchen ist bereit, wenn du ankommst',
    valueDesc: 'Vermeide Schlangen, sichere die Verfügbarkeit und finde den nächstgelegenen Standort. Mit Bame steht deine Zeit an erster Stelle — bestelle in Sekunden und genieße ohne Warten.',
    benefit1: 'Keine Schlangen oder Wartezeiten', benefit1Desc: 'Deine Bestellung ist genau dann fertig, wenn du am Standort ankommst.',
    benefit2: 'Echtzeit-Verfügbarkeit', benefit2Desc: 'Prüfe, ob dein Lieblingsstandort geöffnet ist, bevor du das Haus verlässt.',
    benefit3: 'Sofortige Bestätigung', benefit3Desc: 'Deine Reservierung wird sofort bestätigt, ohne Vorauszahlung.',
    ctaTitle: 'Bereit zur Bestellung?', ctaDesc: 'Wähle deinen Lieblingsstandort, bestelle in Sekunden und hole es ohne Wartezeit ab.',
    ctaBtn: 'Verfügbare Standorte ansehen', waitLabel: 'Wartezeit', waitValue: 'Bereit bei Ankunft',
    heroTitle: 'Brathähnchen, frisch zubereitet.', heroDesc: 'Langsam mit Gewürzen im eigenen Saft gebraten. Bestelle online und hole es heiß an deinem Standort in Murcia ab. Keine Schlangen, keine Liefergebühren.',
    seeLocal: 'Standort ansehen', exploreMenu: 'Speisekarte erkunden', viewAll: 'Alle ansehen →', dishes: 'Gerichte',
    topOrders: 'Meistbestellt', topFavs: 'Nachbarschaftsfavoriten', howOrderTitle: 'Bestellen ist so einfach',
    chooseLocal: 'Wähle deinen Grill', localsRegion: 'Standorte in der Region Murcia', hoursLabel: 'ÖFFNUNGSZEITEN',
    callBtn: 'Anrufen', orderHereBtn: 'Hier bestellen →',
    ctaHungryTitle: 'Schon hungrig?', ctaHungrySub: 'Dein Brathähnchen kann in 20 Minuten fertig sein.', startOrderBtn: 'Meine Bestellung starten →',
    homeStep1Title: 'Wähle deine Gerichte', homeStep1Desc: 'Erkunde die Speisekarte und füge das hinzu, worauf du am meisten Lust hast.',
    homeStep2Title: 'Bezahle, wie du möchtest', homeStep2Desc: 'Online-Karte, Bizum oder bar bei Abholung. Du entscheidest.',
    homeStep3Title: 'Heiß abholen', homeStep3Desc: 'In 20–30 Min ist deine Bestellung fertig. Keine Schlangen oder Wartezeiten.',
  },
  
  // ─── Contacto ──────────────────────────────────────────────────────────────────
  contact: {
    title: 'Brauchst du Hilfe?', subtitle: 'Schreib uns, wenn du Fragen hast.', send: 'Nachricht senden', name: 'Name', subject: 'Betreff', message: 'Nachricht', successTitle: 'Nachricht gesendet', successDesc: 'Danke, dass du Bame kontaktiert hast. Wir werden dir so schnell wie möglich antworten.',
    heroTitle: 'Lass uns reden', heroDesc: 'Hast du eine Frage zu deiner Bestellung, einen Vorschlag oder möchtest du ein Event organisieren? Schreib uns oder kontaktiere deinen Standort direkt.',
    formTitle: 'Sende uns eine Nachricht', requiredFields: 'Pflichtfelder',
    namePlaceholder: 'Dein Name', phoneLabel: 'TELEFON', emailLabel: 'E-MAIL',
    subjectLabel: 'BETREFF', messagePlaceholder: 'Sag uns, wie wir dir helfen können…',
    privacyCheck: 'Ich habe die Datenschutzrichtlinie und die Verarbeitung meiner Daten gelesen und akzeptiert.',
    successPart1: 'Danke, ', successPart2: '. Wir antworten dir an ', successPart3: ' in weniger als 24 Stunden.',
    anotherMsg: 'Weitere Nachricht senden', supportLabel: 'KUNDENSERVICE',
    supportHours: 'Montag bis Sonntag, von 09:30 bis 13:30 Uhr.',
    supportNote: 'Für laufende Bestellungen kontaktiere bitte direkt deinen Standort.',
    localContactTitle: 'Kontaktiere deinen Standort', localsCount: 'Standorte in der Region Murcia',
    faqTitle: 'Häufig gestellte Fragen',
    faq1q: 'Wie lange dauert es, bis meine Bestellung fertig ist?', faq1a: 'Abholbestellungen sind in der Regel in 20–30 Minuten fertig. Wir sagen dir Bescheid, wann du sie abholen kannst.',
    faq2q: 'Kann ich vor Ort bezahlen?', faq2a: 'Ja. Du kannst online per Karte oder Bizum bezahlen, oder bar bzw. mit Karte, wenn du deine Bestellung am Grill abholst.',
    faq3q: 'Nehmt ihr Bestellungen für Events oder große Gruppen an?', faq3a: 'Natürlich. Schreib uns mit dem Betreff "Events und Catering" unter Angabe von Datum, Personenanzahl und Standort, und wir erstellen dir ein Angebot.',
    faq4q: 'Wo kann ich die Allergene der einzelnen Gerichte einsehen?', faq4a: 'Jedes Produkt auf der Speisekarte zeigt seine Allergene und möglichen Spuren an, wenn du seine Details öffnest. Wenn du Fragen hast, frag an deinem Standort.',
    subject1: 'Reservierungen und Bestellungen', subject2: 'Vorschläge', subject3: 'Beschwerden', subject4: 'Events und Catering', subject5: 'Arbeite mit uns',
    callUs: 'RUF UNS AN', emailSend: 'E-Mail senden',
    errName: 'Gib deinen Namen ein.', errEmail: 'Gib eine gültige E-Mail-Adresse ein.', errMsg: 'Schreibe deine Nachricht.', errPrivacy: 'Du musst die Datenschutzrichtlinie akzeptieren.',
  },
  
  // ─── Acerca de Bame ──────────────────────────────────────────────────────────────────
  about: { title: 'Über Bame' },
  
  // ─── Legal ──────────────────────────────────────────────────────────────────
  legal: { privacy: 'Datenschutzrichtlinie', terms: 'Nutzungsbedingungen', cookies: 'Cookie-Richtlinie' },
  
  // ─── Pagar ──────────────────────────────────────────────────────────────────
  payment: {
    successTitle: 'Zahlung abgeschlossen!', successDesc: 'Deine Zahlung wurde erfolgreich verarbeitet.', cancelTitle: 'Zahlung abgebrochen', cancelDesc: 'Es wurde keine Abbuchung vorgenommen. Deine Bestellung ist noch im Warenkorb gespeichert.', retry: 'Zahlung erneut versuchen',
    successStripeDesc: 'Deine Zahlung wurde erfolgreich über Stripe abgewickelt.',
    testModeTitle: 'Testmodus:', testModeDesc: 'Es wird keine echte Abbuchung vorgenommen. Dies ist eine Testumgebung.',
    payAtStoreSuccessTitle: 'Bestellung bestätigt!', payAtStoreSuccessDesc: 'Deine Bestellung wurde erfolgreich registriert.',
    payAtStorePickup: 'Denk daran, dass du den Betrag bei der Abholung vor Ort bezahlst.',
    payAtStoreContact: 'Wenn du Fragen hast, wende dich direkt an den Standort.',
    payAtStoreMethodTitle: 'Methode: Vor Ort bezahlen.', payAtStoreMethodDesc: 'Der Standort erhält deine Bestellung und sie wird bereit sein, wenn du ankommst.',
    cancelDesc2: 'Du kannst die Zahlung erneut versuchen oder deine Auswahl ändern, bevor du fortfährst.',
    retryBtn: 'Zahlung erneut versuchen', viewLocals: 'Standorte ansehen',
    moreLocals: 'Weitere Standorte ansehen', backHome: 'Zurück zur Startseite',
    successQuestion: 'Wenn du Fragen hast, kontaktiere den Standort direkt.',
    testModeNote: 'Nutze die Testkarte',
  },
  
  // ─── Perfil ──────────────────────────────────────────────────────────────────
  profile: {
    title: 'Mein Profil', loading: 'Dein Profil wird geladen…',
    personalInfo: 'Persönliche Informationen', nameLabel: 'Name', emailLabel: 'E-Mail', roleLabel: 'Rolle',
    roleCustomer: 'Kunde',
    recentOrders: 'Letzte Bestellungen', noOrders: 'Du hast noch keine Bestellungen.',
    preferences: 'Präferenzen', language: 'Sprache',
    reserveBtn: 'Eine Bestellung aufgeben', orderTotal: 'Gesamt', orderStatus: 'Status',
    memberSince: 'Mitglied seit', pointsTitle: 'BAME Punkte', pointsUnit: 'Punkte',
    pointsToGo: 'Dir fehlen', pointsForFree: 'Punkte für ein halbes gratis Brathähnchen. Du erhältst 1 Punkt für jeden €.',
    personalData: 'Persönliche Daten', editBtn: 'Bearbeiten', cancelBtn: 'Abbrechen',
    nameField: 'Name', emailField: 'E-Mail', phoneField: 'Telefon',
    saveChanges: 'Änderungen speichern', paymentMethod: 'Zahlungsmethode', defaultCard: 'Standard', addCard: '+ Karte hinzufügen',
    tabCuenta: 'Konto', tabPedidos: 'Bestellungen', tabFavoritos: 'Favoriten', tabAjustes: 'Einstellungen',
    pastOrders: 'Bisherige Bestellungen', repeatOrder: '↺ Bestellung wiederholen',
    noOrders2: 'Du hast noch keine Bestellungen aufgegeben.', viewMenuLink: 'Speisekarte ansehen →',
    favLocalLabel: 'DEIN LIEBLINGSSTANDORT', favLocalDesc: 'Speichere einen Lieblingsstandort aus der Standortauswahl.',
    viewLocalsLink: 'Standorte ansehen →', savedDishes: 'Gespeicherte Gerichte',
    noDishes: 'Du hast noch keine Gerichte gespeichert', noDishesDesc: 'Füge deine Favoriten aus der Speisekarte hinzu.',
    notifOrders: 'Bestellbenachrichtigungen', notifOrdersDesc: 'Benachrichtigungen über deinen Bestellstatus',
    notifOffers: 'Angebote und Neuigkeiten', notifOffersDesc: 'Aktionen per E-Mail',
    notifSms: 'SMS', notifSmsDesc: 'Benachrichtigungen per Textnachricht',
    darkTheme: 'Dunkles Design', darkThemeDesc: 'Ändere das Erscheinungsbild der App',
    languageLabel: 'Sprache', languageDesc: 'App-Sprache',
    logoutBtn: 'Abmelden', loadingOrders: 'Bestellungen werden geladen…',
    stepReceived: 'Erhalten', stepPreparing: 'In Vorbereitung', stepReady: 'Bereit',
  },
  
  // ─── Menú de Usuario ──────────────────────────────────────────────────────────────────
  userMenu: { ariaLabel: 'Benutzermenü', profile: 'Mein Profil', logout: 'Abmelden', loginLink: 'Anmelden', registerLink: 'Registrieren' },
};

export const UI_STRINGS: Record<Language, UiStrings> = { es: ES, en: EN, fr: FR, it: IT, de: DE };

export function getUiStrings(lang: Language): UiStrings {
  return UI_STRINGS[lang] ?? ES;
}
