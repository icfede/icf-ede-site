import math, os
# Tekent de illustraties (visie, missie, kernwaarden) in de kleuren van het logo. Draai: python3 playground/teken-illustraties.py
D = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "wp-content", "themes", "icf-ede", "assets", "illustraties")
ROOM, ZAND, TERRA, HON, OLIJF, KOF = "#FFF9F1", "#FFF0DD", "#6A8F65", "#E7AE3F", "#395A45", "#23362A"
PAARS, LILA = "#8E5A9B", "#D7BEDF"  # paarse accenten, spaarzaam gebruikt
HONL, TERRAL, OLIJFL, OLIJFLL = "#F5D48A", "#A9C29F", "#6A8F65", "#8FB089"
S = 'stroke="%s" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"' % KOF

def svg(vb, body, title):
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="%s" role="img" aria-label="%s">\n%s\n</svg>\n' % (vb, title, body))

def rays(cx, cy, r1, r2, a0, a1, step, color, w):
    out = []
    a = a0
    while a <= a1 + 0.01:
        t = math.radians(a)
        out.append('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>' % (cx + r1*math.cos(t), cy + r1*math.sin(t), cx + r2*math.cos(t), cy + r2*math.sin(t)))
        a += step
    return '<g stroke="%s" stroke-width="%s" stroke-linecap="round">%s</g>' % (color, w, "".join(out))

# ---------- VISIE: zon boven een dorp van verschillende huizen ----------
visie = f'''<defs><clipPath id="c"><circle cx="240" cy="180" r="170"/></clipPath></defs>
<circle cx="240" cy="180" r="170" fill="{ZAND}"/>
<g clip-path="url(#c)">
{rays(240, 132, 74, 98, -180, 0, 22.5, HON, 8)}
<circle cx="240" cy="132" r="58" fill="{HON}"/>
<circle cx="226" cy="118" r="20" fill="{HONL}" opacity=".7"/>
<path d="M118 150 q9 -9 18 0 q9 -9 18 0" fill="none" {S}/>
<path d="M330 168 q7 -7 14 0 q7 -7 14 0" fill="none" {S}/>
<path d="M40 252 C120 200 185 214 245 232 C315 252 370 200 450 222 V380 H40 Z" fill="{OLIJFL}"/>
<g transform="translate(240 286) scale(.84) translate(-246 -286)">
<!-- huis met trapgevel -->
<path d="M140 286 V228 H146 V216 H154 V204 H161 V194 H169 V204 H176 V216 H184 V228 H190 V286 Z" fill="{TERRA}" {S}/>
<rect x="152" y="236" width="10" height="14" rx="2" fill="{HONL}"/><rect x="168" y="236" width="10" height="14" rx="2" fill="{HONL}"/>
<rect x="158" y="262" width="14" height="24" rx="2" fill="{KOF}"/>
<!-- klein huis links -->
<path d="M96 286 V252 L115 236 L134 252 V286 Z" fill="{HONL}" {S}/>
<rect x="109" y="262" width="12" height="12" rx="2" fill="{ROOM}" {S}/>
<!-- kerkje in het midden -->
<path d="M212 286 V206 H268 V286 Z" fill="{ROOM}" {S}/>
<path d="M204 208 L240 172 L276 208 Z" fill="{PAARS}" {S}/>
<path d="M240 146 V172 M231 156 H249" fill="none" stroke="{KOF}" stroke-width="4" stroke-linecap="round"/>
<circle cx="240" cy="226" r="8" fill="{HON}" {S}/>
<path d="M229 286 V262 a11 11 0 0 1 22 0 V286 Z" fill="{HON}" {S}/>
<!-- huis met koepel -->
<path d="M288 286 V236 H348 V286 Z" fill="#F7EBD6" {S}/>
<path d="M294 236 a24 24 0 0 1 48 0 Z" fill="{TERRAL}" {S}/>
<path d="M318 212 V202" stroke="{KOF}" stroke-width="3" stroke-linecap="round"/>
<path d="M300 286 V266 a7 7 0 0 1 14 0 V286" fill="{HON}" {S}/>
<path d="M324 260 V250 a6 6 0 0 1 12 0 V260 Z" fill="{HONL}" {S}/>
<!-- plat dak -->
<path d="M352 286 V252 H396 V286 Z" fill="{HONL}" {S}/>
<path d="M348 252 H400" {S}/>
<rect x="362" y="262" width="10" height="10" rx="2" fill="{ROOM}" {S}/><rect x="378" y="262" width="10" height="10" rx="2" fill="{ROOM}" {S}/>
</g>
<path d="M30 296 C150 274 330 274 450 296 V380 H30 Z" fill="{OLIJF}"/>
<path d="M120 318 C200 306 280 306 360 318" fill="none" stroke="{OLIJFL}" stroke-width="4" stroke-linecap="round"/>
</g>'''

# ---------- MISSIE: open deur, licht naar buiten, tafel met thee ----------
missie = f'''<defs><clipPath id="c"><circle cx="240" cy="180" r="170"/></clipPath>
<linearGradient id="l" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{HON}"/><stop offset="1" stop-color="{HONL}"/></linearGradient></defs>
<circle cx="240" cy="180" r="170" fill="{ZAND}"/>
<g clip-path="url(#c)">
<rect x="0" y="0" width="480" height="300" fill="#F4E9D8"/>
<path d="M40 300 H440 V380 H40 Z" fill="{TERRAL}"/>
<path d="M176 300 L304 300 L360 380 L120 380 Z" fill="{HONL}" opacity=".75"/>
<!-- deurkozijn met boog -->
<path d="M164 300 V140 a76 76 0 0 1 152 0 V300 Z" fill="{TERRA}" {S}/>
<path d="M180 300 V144 a60 60 0 0 1 120 0 V300 Z" fill="url(#l)" {S}/>
<!-- opengezwaaide deur -->
<path d="M300 300 V144 L338 128 V316 Z" fill="{OLIJF}" {S}/>
<circle cx="328" cy="226" r="4" fill="{HON}"/>
<!-- plant naast de deur -->
<path d="M110 300 l6 -40 h32 l6 40 Z" fill="{TERRA}" {S}/>
<path d="M132 260 C120 230 100 222 92 214 M132 260 C134 226 128 206 136 186 M132 260 C150 236 166 230 174 226" fill="none" stroke="{OLIJF}" stroke-width="5" stroke-linecap="round"/>
<ellipse cx="96" cy="214" rx="12" ry="7" fill="{OLIJFL}" transform="rotate(-30 96 214)"/>
<ellipse cx="136" cy="188" rx="7" ry="13" fill="{OLIJFL}"/>
<ellipse cx="170" cy="226" rx="12" ry="7" fill="{OLIJFL}" transform="rotate(20 170 226)"/>
<!-- tafel -->
<rect x="196" y="300" width="186" height="12" rx="6" fill="{KOF}"/>
<path d="M212 312 V352 M366 312 V352" stroke="{KOF}" stroke-width="6" stroke-linecap="round"/>
<!-- theepot -->
<path d="M214 300 C208 274 222 262 238 262 C254 262 268 274 262 300 Z" fill="{PAARS}" {S}/>
<path d="M262 280 C274 276 278 266 284 262" fill="none" {S}/>
<path d="M214 278 C202 276 202 290 214 292" fill="none" {S}/>
<path d="M230 262 a8 6 0 0 1 16 0" fill="{LILA}" {S}/>
<!-- theeglaasjes -->
<path d="M292 296 c-5 -6 -3 -12 0 -15 c-3 -3 -3 -9 1 -12 h12 c4 3 4 9 1 12 c3 3 5 9 0 15 Z" fill="#F7EBD6" {S}/><path d="M292 293 c-2 -4 -1 -8 1 -10 h12 c2 2 3 6 1 10 Z" fill="{TERRA}"/><ellipse cx="299" cy="298" rx="13" ry="3" fill="{ROOM}" {S}/><path d="M322 296 c-5 -6 -3 -12 0 -15 c-3 -3 -3 -9 1 -12 h12 c4 3 4 9 1 12 c3 3 5 9 0 15 Z" fill="#F7EBD6" {S}/><path d="M322 293 c-2 -4 -1 -8 1 -10 h12 c2 2 3 6 1 10 Z" fill="{TERRA}"/><ellipse cx="329" cy="298" rx="13" ry="3" fill="{ROOM}" {S}/>
<!-- koffiekop -->
<path d="M348 282 h24 v10 a8 8 0 0 1 -8 8 h-8 a8 8 0 0 1 -8 -8 Z" fill="{ROOM}" {S}/>
<path d="M372 286 a5 5 0 0 1 0 10" fill="none" {S}/>
<path d="M300 256 c-4 -6 4 -10 0 -16 M330 256 c-4 -6 4 -10 0 -16 M360 274 c-4 -6 4 -10 0 -16" fill="none" stroke="{KOF}" stroke-width="2.5" stroke-linecap="round" opacity=".55"/>
</g>'''

# ---------- WAARDEN: boom met wortels en negen vruchten ----------
fruit = [(176,140,PAARS),(214,108,HON),(262,104,PAARS),(306,134,HON),(196,184,HON),(244,152,PAARS),(292,180,HON),(230,206,HON),(330,178,PAARS)]
waarden = f'''<defs><clipPath id="c"><circle cx="240" cy="180" r="170"/></clipPath></defs>
<circle cx="240" cy="180" r="170" fill="{ZAND}"/>
<g clip-path="url(#c)">
<path d="M40 262 C150 250 330 250 440 262 V380 H40 Z" fill="#C99A6B"/>
<path d="M240 262 C236 290 214 304 186 314 M240 262 C246 292 272 302 300 312 M238 270 C232 304 236 330 226 352 M244 276 C258 300 250 330 262 350 M212 304 C196 316 186 334 160 340 M276 304 C296 318 318 324 330 340" fill="none" stroke="{KOF}" stroke-width="5" stroke-linecap="round" opacity=".8"/>
<path d="M226 266 C230 236 228 214 222 192 L236 196 L240 178 L246 196 L260 190 C252 214 250 236 256 266 Z" fill="#7A4A2E" {S}/>
<path d="M226 218 C206 206 196 196 188 182 M256 214 C276 204 288 192 296 178" fill="none" stroke="#7A4A2E" stroke-width="8" stroke-linecap="round"/>
<g {S}>
<circle cx="190" cy="160" r="46" fill="{OLIJF}"/>
<circle cx="292" cy="160" r="48" fill="{OLIJF}"/>
<circle cx="240" cy="118" r="56" fill="{OLIJFL}"/>
<circle cx="206" cy="112" r="34" fill="{OLIJFL}"/>
<circle cx="280" cy="114" r="36" fill="{OLIJFL}"/>
<circle cx="242" cy="176" r="42" fill="{OLIJFL}"/>
</g>
<path d="M212 92 C224 82 240 80 252 84" fill="none" stroke="{OLIJFLL}" stroke-width="5" stroke-linecap="round"/>
{"".join('<circle cx="%d" cy="%d" r="10" fill="%s" %s/>' % (x, y, c, S) for x, y, c in fruit)}
<path d="M120 262 l6 -16 l6 16 M340 262 l5 -14 l5 14 M362 262 l4 -10 l4 10" fill="none" stroke="{OLIJF}" stroke-width="3" stroke-linecap="round"/>
</g>'''

# ---------- Iconen kernwaarden (96x96) ----------
def icoon(inner):
    return f'<circle cx="48" cy="48" r="46" fill="{ZAND}"/>\n' + inner

S2 = 'stroke="%s" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"' % KOF
iconen = {
 "evangelie": icoon(rays(48, 44, 22, 32, -180, 0, 30, HON, 4) + f'<path d="M44 22 h8 v14 h12 v8 h-12 v28 h-8 v-28 h-12 v-8 h12 Z" fill="{TERRA}" {S2}/>'),
 "bijbel": icoon(f'<path d="M48 34 C40 28 28 27 20 29 V68 C28 66 40 67 48 73 Z" fill="{ROOM}" {S2}/><path d="M48 34 C56 28 68 27 76 29 V68 C68 66 56 67 48 73 Z" fill="{ROOM}" {S2}/><path d="M26 38 C32 37 38 38 42 40 M26 46 C32 45 38 46 42 48 M54 40 C58 38 64 37 70 38 M54 48 C58 46 64 45 70 46" fill="none" stroke="{TERRA}" stroke-width="2.5" stroke-linecap="round"/><path d="M58 29 V44 l4 -3 l4 3 V28" fill="{TERRA}" {S2}/>'),
 "groei": icoon(f'<path d="M22 70 C36 62 60 62 74 70 Z" fill="#C99A6B" {S2}/><path d="M48 66 V42" {S2}/><path d="M48 50 C48 36 36 30 24 32 C24 44 34 52 48 50 Z" fill="{OLIJFL}" {S2}/><path d="M48 44 C48 28 60 20 74 22 C74 36 62 46 48 44 Z" fill="{OLIJF}" {S2}/>'),
 "liefde": icoon(f'<path d="M48 70 C30 58 20 48 20 37 C20 28 27 22 35 22 C41 22 46 26 48 31 C50 26 55 22 61 22 C69 22 76 28 76 37 C76 48 66 58 48 70 Z" fill="{PAARS}" {S2}/><path d="M32 34 C33 30 36 28 39 28" fill="none" stroke="{LILA}" stroke-width="3" stroke-linecap="round"/>'),
 "heiligheid": icoon(f'<path d="M48 18 C54 26 58 32 58 38 C58 44 53 48 48 48 C43 48 38 44 38 38 C38 32 42 26 48 18 Z" fill="{HON}" {S2}/><path d="M48 32 C50 35 52 37 52 40 C52 43 50 44 48 44 C46 44 44 43 44 40 C44 37 46 35 48 32 Z" fill="{ROOM}"/><path d="M20 56 C26 52 34 50 44 52 H62 C70 52 76 50 80 46 C80 58 72 68 58 70 H38 C28 70 22 64 20 56 Z" fill="{TERRA}" {S2}/><path d="M38 70 l-4 6 h28 l-4 -6" fill="{TERRAL}" {S2}/>'),
 "familie": icoon(f'<path d="M20 46 L48 22 L76 46" fill="none" {S2}/><path d="M26 42 V74 H70 V42 L48 24 Z" fill="{HONL}" {S2}/><path d="M48 66 C40 60 36 56 36 51 C36 47 39 45 42 45 C45 45 47 47 48 49 C49 47 51 45 54 45 C57 45 60 47 60 51 C60 56 56 60 48 66 Z" fill="{TERRA}" {S2}/>'),
 "veilig": icoon(f'<path d="M22 50 C30 42 40 40 50 44 C56 34 66 28 76 30 C70 34 66 40 64 46 C70 50 74 56 74 62 C64 64 52 62 44 58 L30 66 L34 56 C28 56 24 54 22 50 Z" fill="{ROOM}" {S2}/><path d="M50 44 C46 52 48 58 56 60" fill="none" {S2}/><circle cx="68" cy="36" r="2" fill="{KOF}"/><path d="M76 30 L82 31 L76 34" fill="{HON}" {S2}/><path d="M62 64 C60 70 56 72 52 72 M66 62 C68 68 72 70 76 70" fill="none" stroke="{OLIJF}" stroke-width="3" stroke-linecap="round"/><ellipse cx="54" cy="72" rx="4" ry="2.5" fill="{OLIJFL}"/>'),
 "zending": icoon(f'<circle cx="46" cy="50" r="24" fill="{OLIJFL}" {S2}/><path d="M30 40 C36 42 38 36 44 38 C48 40 46 46 52 46 C56 46 56 40 62 40 M28 56 C34 54 38 58 42 62 C44 66 40 70 42 74 M56 56 C60 54 66 58 68 56" fill="none" stroke="{OLIJF}" stroke-width="3" stroke-linecap="round"/><path d="M22 32 C34 16 60 14 74 26" fill="none" stroke="{TERRA}" stroke-width="3.5" stroke-linecap="round" stroke-dasharray="1 7"/><path d="M68 18 L80 28 L66 32 Z" fill="{TERRA}" {S2}/>'),
 "vrijgevig": icoon(f'<path d="M30 42 C30 34 38 30 44 34 C46 26 56 26 58 34 C64 30 72 34 70 42 Z" fill="{HON}" {S2}/><path d="M40 36 l2 4 M52 32 l1 4 M62 36 l-1 4" stroke="{TERRA}" stroke-width="2.5" stroke-linecap="round"/><path d="M20 44 H76 L70 70 C69 74 66 76 62 76 H34 C30 76 27 74 26 70 Z" fill="{TERRAL}" {S2}/><path d="M24 54 H72 M27 64 H69" fill="none" stroke="{TERRA}" stroke-width="2.5" stroke-linecap="round"/>'),
}

# visie.svg, missie.svg en waarden.svg zijn nu eigen iconen van ICF (aangeleverd beeld);
# dit script tekent alleen nog de negen kleine iconen bij de kernwaarden.
files = {}
for k, v in iconen.items():
    files["waarde-%s.svg" % k] = svg("0 0 96 96", v, k)
for n, c in files.items():
    open(os.path.join(D, n), "w").write(c)
print(sorted(files))
