{{-- Line-art illustration: delivery boxes, a sneaker and a map pin (static SVG). --}}
<svg viewBox="0 0 480 400" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Shoe distribution illustration" fill="none" stroke="#111827" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
    <!-- floor -->
    <path d="M30 352 H450" stroke-width="2"/>
    <path d="M70 352 L200 300 H440" stroke-width="1.6" stroke="#9ca3af"/>

    <!-- dashed route -->
    <path d="M120 120 C 200 40, 320 40, 380 110" stroke-dasharray="6 9" stroke="#2563eb" stroke-width="2"/>
    <circle cx="384" cy="118" r="5" stroke="#2563eb"/>

    <!-- map pin -->
    <path d="M110 70 c-20 0 -34 15 -34 33 c0 25 34 58 34 58 s34 -33 34 -58 c0 -18 -14 -33 -34 -33z" fill="#111827"/>
    <circle cx="110" cy="102" r="12" fill="#fff" stroke="#fff"/>

    <!-- big open shoe box -->
    <path d="M150 230 L270 205 L370 228 L250 256 Z" fill="#fff"/>
    <path d="M150 230 V330 L250 356 V256" fill="#fff"/>
    <path d="M250 256 V356 L370 326 V228" fill="#f3f4f6"/>
    <path d="M150 230 L118 200 L238 176 L270 205" fill="#fff"/>
    <path d="M370 228 L402 196 L300 176 L270 205" fill="#fff"/>
    <rect x="270" y="280" width="62" height="26" rx="3" transform="rotate(-14 270 280)" fill="#fff"/>
    <path d="M280 286 v14 M287 284 v14 M294 282 v14 M301 280 v14 M308 278 v14 M315 276 v14" stroke-width="1.6" transform="rotate(-0 0 0)"/>

    <!-- sneaker coming out of the box -->
    <g transform="translate(150 118)">
        <path d="M6 92 C 4 70, 12 58, 30 56 L 70 52 C 82 40, 90 28, 104 26 C 112 25, 116 32, 122 40 C 140 60, 175 66, 196 74 C 210 80, 214 92, 206 100 L 18 104 C 10 104, 6 99, 6 92 Z" fill="#fff"/>
        <path d="M6 92 L 206 98" />
        <path d="M10 104 C 60 112, 160 110, 206 100" stroke-width="2"/>
        <path d="M78 50 L 92 64 M 88 42 L 102 58 M 98 34 L 112 50"/>
        <path d="M30 56 C 36 70, 52 76, 70 72" />
        <path d="M150 70 C 160 80, 180 84, 200 82" stroke="#2563eb" stroke-width="3"/>
        <path d="M104 26 C 108 14, 120 10, 128 14" />
    </g>

    <!-- stacked delivery boxes -->
    <g transform="translate(360 238)">
        <path d="M0 18 L40 6 L80 18 L40 30 Z" fill="#fff"/>
        <path d="M0 18 V88 L40 100 V30" fill="#fff"/>
        <path d="M40 30 V100 L80 88 V18" fill="#e5e7eb"/>
        <path d="M18 12 L58 24 V36" stroke-width="2"/>
    </g>
    <g transform="translate(372 168)">
        <path d="M0 16 L30 6 L60 16 L30 26 Z" fill="#fff"/>
        <path d="M0 16 V64 L30 74 V26" fill="#fff"/>
        <path d="M30 26 V74 L60 64 V16" fill="#e5e7eb"/>
        <path d="M12 12 L42 22 V30" stroke-width="2"/>
    </g>

    <!-- sparkles -->
    <path d="M60 220 v10 M55 225 h10" stroke-width="1.8"/>
    <path d="M420 70 v10 M415 75 h10" stroke-width="1.8"/>
    <path d="M300 60 l8 6 l-10 2 z" stroke-width="1.8"/>
    <circle cx="80" cy="300" r="4" stroke-width="1.8"/>
</svg>
