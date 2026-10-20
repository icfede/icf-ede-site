#!/usr/bin/env python3
"""Bouwt playground/blueprint.json: WordPress + Polylang + ICF-thema + plugin + pagina's + rooster.

Gebruik:  python3 playground/build.py   (of REPO=https://github.com/org/repo python3 playground/build.py)
Daarna:   open playground/preview-link.txt of het preview-artifact.
"""
import base64, html, json, os, re, sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEME = os.path.join(ROOT, "wp-content/themes/icf-ede")
PLUGIN = os.path.join(ROOT, "wp-content/plugins/icf-ede-bijeenkomsten")
CONTENT = os.path.join(ROOT, "content")

# Rooster zoals op de huidige icfede.nl (verleden datums laat de site vanzelf weg).
BIJEENKOMSTEN = [
    "2026-10-18", "2026-11-01", "2026-11-15", "2026-11-29",
    "2026-12-13", "2026-12-27", "2027-01-10", "2027-01-24",
]
LOGO = "https://icfede.nl/assets/images/Logo-ICF-Ede-2048x1629.png"


def tree(path):
    out = {}
    for name in sorted(os.listdir(path)):
        full = os.path.join(path, name)
        if name.startswith("."):
            continue
        if os.path.isdir(full):
            out[name] = tree(full)
        else:
            with open(full, encoding="utf-8") as f:
                out[name] = f.read()
    return out


PHP_STUBS = (
    "<?php function esc_url($s){return htmlspecialchars($s, ENT_QUOTES);}"
    " function esc_attr($s){return htmlspecialchars($s, ENT_QUOTES);}"
    " function esc_html($s){return htmlspecialchars($s, ENT_NOQUOTES);}"
    " function __($s){return $s;}"
    " function get_template_directory(){return %r;} function get_template_directory_uri(){return '{{thema}}';} ?>"
) % THEME


def pattern_markup(slug, lang="nl"):
    """Voert het patroon uit met PHP (zodat lussen werken) en geeft de blokmarkup terug."""
    import subprocess, tempfile
    path = os.path.join(THEME, "patterns", slug + ".php")
    with tempfile.NamedTemporaryFile("w", suffix=".php", delete=False) as tmp:
        tmp.write(PHP_STUBS + "<?php $GLOBALS['icf_lang'] = %r; include %r;" % (lang, path))
    out = subprocess.run(["php", tmp.name], capture_output=True, text=True, check=True).stdout
    os.unlink(tmp.name)
    return out.strip()


def inline(text):
    text = html.escape(text, quote=False)
    text = re.sub(r"\*\*(.+?)\*\*", r"<strong>\1</strong>", text)
    return re.sub(r"\[([^\]]+)\]\(([^)]+)\)", lambda m: '<a href="%s">%s</a>' % (html.escape(m.group(2)), m.group(1)), text)


RASTER_OPEN = ('<!-- wp:group {"align":"wide","className":"icf-waarden","layout":{"type":"grid","minimumColumnWidth":"17rem"}} -->\n'
               '<div class="wp-block-group alignwide icf-waarden">')
RASTER_DICHT = '</div>\n<!-- /wp:group -->'


def beeld_blok(naam, klasse):
    """Illustratie uit het thema. {{thema}} vervangt de installatieknop door het thema-adres."""
    return ('<!-- wp:image {"sizeSlug":"full","className":"%s"} -->\n<figure class="wp-block-image size-full %s">'
            '<img src="{{thema}}/assets/illustraties/%s.svg" alt=""/></figure>\n<!-- /wp:image -->') % (klasse, klasse, naam)


def to_blocks(body, lang="nl"):
    if body.lstrip().startswith("@raw"):
        return body.lstrip()[4:].strip()
    blocks, lines = [], body.strip().split("\n")
    i = 0
    while i < len(lines):
        line = lines[i].rstrip()
        if not line:
            i += 1
            continue
        if line.startswith("@beeld ") or line.startswith("@icoon "):
            # Een sectie (of kaart) met illustratie: alles tot de volgende @-regel of kop.
            soort, naam = line.split()[:2]
            j, kop_gezien = i + 1, False
            while j < len(lines) and not lines[j].startswith("@"):
                if lines[j].startswith("#"):
                    if kop_gezien and (soort == "@icoon" or lines[j].startswith("## ")):
                        break
                    kop_gezien = True
                j += 1
            binnen = to_blocks("\n".join(lines[i + 1:j]), lang)
            img = beeld_blok(naam if soort == "@beeld" else "waarde-" + naam, "icf-illustratie" if soort == "@beeld" else "icf-waarde-icoon")
            if soort == "@beeld":
                blocks.append(
                    '<!-- wp:columns {"verticalAlignment":"center","className":"icf-beeld-sectie"} -->\n'
                    '<div class="wp-block-columns are-vertically-aligned-center icf-beeld-sectie"><!-- wp:column {"verticalAlignment":"center","width":"34%"} -->\n'
                    '<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:34%">' + img + '</div>\n<!-- /wp:column -->\n\n'
                    '<!-- wp:column {"verticalAlignment":"center"} -->\n<div class="wp-block-column is-vertically-aligned-center">' + binnen + '</div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->')
            else:
                kaart = ('<!-- wp:group {"className":"icf-waarde","layout":{"type":"default"}} -->\n<div class="wp-block-group icf-waarde">'
                         + img + "\n\n" + binnen + '</div>\n<!-- /wp:group -->')
                if blocks and blocks[-1].startswith(RASTER_OPEN):
                    blocks[-1] = blocks[-1][: -len(RASTER_DICHT)] + "\n\n" + kaart + RASTER_DICHT
                else:
                    blocks.append(RASTER_OPEN + kaart + RASTER_DICHT)
            i = j
            continue
        if line.startswith("@pattern "):
            blocks.append(pattern_markup(line.split()[1], lang))
        elif line.startswith("### "):
            blocks.append('<!-- wp:heading {"level":3} -->\n<h3 class="wp-block-heading">%s</h3>\n<!-- /wp:heading -->' % inline(line[4:]))
        elif line.startswith("## "):
            blocks.append('<!-- wp:heading -->\n<h2 class="wp-block-heading">%s</h2>\n<!-- /wp:heading -->' % inline(line[3:]))
        elif line.startswith("- "):
            items = []
            while i < len(lines) and lines[i].startswith("- "):
                items.append("<!-- wp:list-item -->\n<li>%s</li>\n<!-- /wp:list-item -->" % inline(lines[i][2:].strip()))
                i += 1
            blocks.append('<!-- wp:list -->\n<ul class="wp-block-list">%s</ul>\n<!-- /wp:list -->' % "\n\n".join(items))
            continue
        else:
            blocks.append("<!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph -->" % inline(line))
        i += 1
    return "\n\n".join(blocks)


TALEN = ["nl", "en", "fa", "ar"]


def read_pages():
    pages = []
    for lang in TALEN:
        folder = os.path.join(CONTENT, lang)
        for name in sorted(os.listdir(folder)):
            if not name.endswith(".txt"):
                continue
            with open(os.path.join(folder, name), encoding="utf-8") as f:
                head, body = f.read().split("\n---\n", 1)
            meta = dict(l.split(": ", 1) for l in head.strip().split("\n"))
            meta["lang"] = lang
            regels = [r.strip() for r in body.strip().split("\n") if r.strip()]
            if regels and all(r.startswith("@pattern ") for r in regels):
                meta["patterns"] = [r.split()[1] for r in regels]   # WordPress bouwt dit zelf op
                meta["content"] = ""
            else:
                meta["content"] = to_blocks(body, lang)
            meta["order"] = int(meta.get("order", 0))
            pages.append(meta)
    return pages


def php_payload(obj):
    data = json.dumps(obj, ensure_ascii=False, separators=(",", ":"))
    assert "\nJSON" not in data
    return "json_decode(<<<'JSON'\n%s\nJSON\n, true)" % data


def build():
    pages = read_pages()

    # Inhoud gaat mee in de plugin: de installatieknop (Gereedschap › ICF Ede installeren) gebruikt dit.
    inhoud = {
        "pages": pages,
        "rooster": BIJEENKOMSTEN,
        "ondertitel": {
            "nl": "Interculturele Christelijke Familie Ede",
            "en": "Intercultural Christian Family Ede",
            "fa": "خانوادهٔ مسیحی بین‌فرهنگی اِده",
            "ar": "العائلة المسيحية متعددة الثقافات في إده",
        },
    }
    os.makedirs(os.path.join(PLUGIN, "inhoud"), exist_ok=True)
    with open(os.path.join(PLUGIN, "inhoud", "inhoud.json"), "w", encoding="utf-8") as f:
        json.dump(inhoud, f, ensure_ascii=False, separators=(",", ":"))

    # De preview doet precies wat de knop op de echte site doet.
    setup_php = """<?php
define( 'WP_ADMIN', true );
require '/wordpress/wp-load.php';
wp_set_current_user( 1 );
try {
	$log = icf_installeer( array( 'rooster' => true ) );
} catch ( Throwable $e ) {
	$log = array( 'fout: ' . $e->getMessage() );
}
update_option( 'icf_setup_log', $log );
echo implode( "\\n", $log );
"""

    blueprint = {
        "$schema": "https://playground.wordpress.net/blueprint-schema.json",
        "meta": {"title": "ICF Ede – preview", "author": "ICF Ede"},
        "landingPage": "/",
        "preferredVersions": {"php": "8.3", "wp": "latest"},
        "features": {"networking": True},
        "login": True,
        "steps": [
            {"step": "setSiteOptions", "options": {
                "blogname": "ICF Ede",
                "blogdescription": "Interculturele Christelijke Familie Ede",
                "timezone_string": "Europe/Amsterdam",
                "date_format": "j F Y",
                "time_format": "H:i",
                "permalink_structure": "/%postname%/",
            }},
            {"step": "installPlugin", "pluginData": {"resource": "wordpress.org/plugins", "slug": "polylang"}, "options": {"activate": True}},
            {"step": "mkdir", "path": "/wordpress/wp-content/themes/icf-ede"},
            {"step": "writeFiles", "writeToPath": "/wordpress/wp-content/themes/icf-ede",
             "filesTree": {"resource": "literal:directory", "name": "icf-ede", "files": tree(THEME)}},
            {"step": "activateTheme", "themeFolderName": "icf-ede"},
            {"step": "mkdir", "path": "/wordpress/wp-content/plugins/icf-ede-bijeenkomsten"},
            {"step": "writeFiles", "writeToPath": "/wordpress/wp-content/plugins/icf-ede-bijeenkomsten",
             "filesTree": {"resource": "literal:directory", "name": "icf-ede-bijeenkomsten", "files": tree(PLUGIN)}},
            {"step": "activatePlugin", "pluginPath": "icf-ede-bijeenkomsten/icf-ede-bijeenkomsten.php"},
            {"step": "runPHP", "code": setup_php},
        ],
    }

    out = os.path.join(ROOT, "playground")
    # Variant die thema en plugin rechtstreeks uit GitHub haalt (vul REPO in).
    repo = os.environ.get("REPO", "https://github.com/icfede/icf-ede-site")
    gh = json.loads(json.dumps(blueprint))
    gh["meta"]["title"] = "ICF Ede – preview vanaf GitHub"
    for step in gh["steps"]:
        if step["step"] == "writeFiles":
            sub = "wp-content/" + step["writeToPath"].split("/wp-content/")[1]
            step["filesTree"] = {"resource": "git:directory", "url": repo, "ref": "main", "refType": "branch", "path": sub}
    with open(os.path.join(out, "blueprint.github.json"), "w", encoding="utf-8") as f:
        json.dump(gh, f, ensure_ascii=False, indent=1)
    with open(os.path.join(out, "blueprint.json"), "w", encoding="utf-8") as f:
        json.dump(blueprint, f, ensure_ascii=False, indent=1)
    raw = json.dumps(blueprint, ensure_ascii=False, separators=(",", ":"))
    link = "https://playground.wordpress.net/#" + base64.b64encode(raw.encode("utf-8")).decode()
    with open(os.path.join(out, "preview-link.txt"), "w") as f:
        f.write(link + "\n")
    # LINT=1 schrijft de PHP-stappen los weg om ze met php -l te controleren
    if os.environ.get("LINT"):
        for naam, code in ((".setup.php", setup_php),):
            with open(os.path.join(out, naam), "w") as f:
                f.write(code)
    # Uploadbare zips voor WordPress (Plugins/Thema's › Uploaden)
    import zipfile
    dist = os.path.join(ROOT, "dist")
    os.makedirs(dist, exist_ok=True)
    for bron, map_naam, zip_naam in ((THEME, "icf-ede", "icf-ede-thema.zip"), (PLUGIN, "icf-ede-bijeenkomsten", "icf-ede-plugin.zip")):
        with zipfile.ZipFile(os.path.join(dist, zip_naam), "w", zipfile.ZIP_DEFLATED) as z:
            for root, dirs, files in os.walk(bron):
                dirs[:] = [d for d in dirs if not d.startswith(".")]
                for fn in files:
                    if fn.startswith("."):
                        continue
                    full = os.path.join(root, fn)
                    z.write(full, os.path.join(map_naam, os.path.relpath(full, bron)))
    print("blueprint.json", len(raw), "bytes; link", len(link), "tekens; zips in dist/")


if __name__ == "__main__":
    build()
