{{--
    Partial : carte KPI réutilisable.
    Variables attendues (via @include) :
      $kpiLabel   string  — libellé court
      $kpiValue   string  — valeur principale (déjà formatée)
      $kpiSub     string  — sous-texte (optionnel, defaut null)
      $kpiIcon    string  — nom Font Awesome sans "fa-"
      $kpiColor   string  — indigo | green | red | amber | blue | slate | orange
      $kpiPct     float   — pourcentage pour la barre (optionnel, null = pas de barre)
      $kpiBarColor string — couleur CSS override pour la barre (optionnel)
--}}
@php
    $kpiSub      ??= null;
    $kpiPct      ??= null;
    $kpiBarColor ??= null;
    $pctVal       = $kpiPct !== null ? min(100, max(0, (float)$kpiPct)) : null;
    $barC         = $kpiBarColor ?? ($pctVal !== null
        ? ($pctVal >= 75 ? '#22c55e' : ($pctVal >= 50 ? '#f59e0b' : '#ef4444'))
        : null);
@endphp
<div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition group relative overflow-hidden">
    <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full opacity-[0.06] bg-{{ $kpiColor }}-500 group-hover:opacity-[0.10] transition"></div>
    <div class="relative">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide leading-tight pr-2">{{ $kpiLabel }}</p>
            <span class="w-9 h-9 rounded-xl bg-{{ $kpiColor }}-50 group-hover:bg-{{ $kpiColor }}-100 flex items-center justify-center shrink-0 transition">
                <i class="fas fa-{{ $kpiIcon }} text-{{ $kpiColor }}-500 text-sm"></i>
            </span>
        </div>
        <div class="text-3xl font-bold text-gray-900 leading-none">{{ $kpiValue }}</div>
        @if($kpiSub)
        <p class="text-xs text-gray-400 mt-1">{{ $kpiSub }}</p>
        @endif
        @if($pctVal !== null)
        <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700" style="width:{{ $pctVal }}%; background:{{ $barC }}"></div>
        </div>
        @endif
    </div>
</div>
