// ─────────────────────────────────────────────────────────────────────────────
// localize-text.ts — selección de textos según idioma.
//
// Esta utilidad devuelve el texto más adecuado según el idioma activo de la
// aplicación. Si no existe traducción para el idioma solicitado, aplica una
// cadena de alternativas para garantizar que siempre se muestre contenido.
// ─────────────────────────────────────────────────────────────────────────────

import type { Language } from "../services/language.service";

export interface MultiLangSlots {
	en?: string | null;
	fr?: string | null;
	it?: string | null;
	de?: string | null;
}

// Devuelve el texto más adecuado para el idioma seleccionado.
export function localizeText(
	es: string,
	slots: MultiLangSlots,
	lang: Language,
): string {
	// El español actúa como idioma principal de la aplicación.
	if (lang === "es") {
		return es || "";
	}

	const direct = slots[lang as keyof MultiLangSlots];

	// Utiliza la traducción disponible para el idioma solicitado.
	if (direct?.trim()) {
		return direct;
	}

	// Si no existe traducción, utiliza el texto en español.
	if (es?.trim()) {
		return es;
	}

	// Como último recurso, devuelve la primera traducción disponible.
	for (const v of Object.values(slots)) {
		if (v?.trim()) {
			return v;
		}
	}

	return "";
}
