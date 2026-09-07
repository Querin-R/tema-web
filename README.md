# Ren Studio — WordPress Block Theme

A minimalist, high-contrast (black + one accent color) full-site-editing theme for a graphic designer/photographer, with a portfolio built as its own custom post type and a clean blog.

## Install

1. Zip the `ren` folder (already done if you received `ren.zip`).
2. In wp-admin: **Appearance → Themes → Add New → Upload Theme**, upload the zip, activate.
3. Go to **Appearance → Theme Options** and:
   - Upload your logo
   - Pick an accent color (Electric Violet / Acid Green / Hot Coral, or Custom)
   - Set the heading/body font — pick a Google Font from the curated list, or upload your own local `.woff2` file
   - Optionally add custom CSS/JS and your social profile URLs
4. Go to **Portfolio → Add New Project** in wp-admin and add a few projects (see "Adding a project" below).
5. Set your homepage: **Settings → Reading → "A static page"** isn't needed — `front-page.html` is used automatically since this is a block theme, but if you've set a static front page, make sure **Appearance → Editor → Templates → Front Page** is assigned.
6. Create a page and assign it the **Portfolio Landing** template (Page → Template dropdown) if you'd rather have the grid live at `/portfolio/` instead of the CPT archive at `/work/`.

## What's included

- `theme.json` — color palette (Ink Black background + Electric Violet/Acid Green/Hot Coral accents), type scale, spacing scale, button/link styles.
- `templates/` — `front-page`, `index`, `archive` (blog), `archive-portfolio` (portfolio grid), `single` (blog post), `single-portfolio` (case study), `page`, `page-portfolio`, `404`.
- `parts/` — `header`, `footer`, `sidebar-blog` (categories, recent posts, search).
- `patterns/` — `hero`, `services-grid`, `portfolio-alternating`, `newsletter-cta`, `portfolio-filter-bar`.
- `inc/custom-post-types.php` — registers the `portfolio` CPT (`/work/` archive), `portfolio_category` + `portfolio_tag` taxonomies, and the Project Specs meta box (Client, Role, Year, Tools, Live URL).
- `inc/theme-options.php` — the **Appearance → Theme Options** panel (logo, accent color, fonts).
- `assets/js/portfolio-filter.js` — client-side category + text filtering for the portfolio grid (no page reload; filter state is kept in the URL so it survives sharing/back-forward).

## Adding a project (portfolio item)

1. **Portfolio → Add New Project**.
2. Set a **Cover Image** (featured image) — used in the grid and as the case-study hero.
3. Write the case study in the main editor — add a **Gallery** block wherever you want the large image gallery to appear.
4. Assign one or more **Project Categories** (used by the filter dropdown) and **Project Tags**.
5. Fill in **Project Specs** in the sidebar meta box (Client, Role, Year, Tools, Live Project URL).
6. Use the **Order** field (Page Attributes panel) to control manual ordering in the grid and the homepage's "Recent Work" section (lower numbers first).

## Fonts

Each of Heading and Body has two independent modes, switchable in **Appearance → Theme Options → Typography**:
- **Google Font** — pick from a curated, categorized list (serif, sans, display, monospace). Loaded via a `<link>` tag with preconnect, front end and editor alike.
- **Upload Local Font** — type a font-family name and upload a `.woff2` file from the Media Library (Heading: one file at the weight you specify; Body: separate Regular and Bold files). Only WOFF2 is supported — it covers every browser still in meaningful use, so there's no legacy woff/ttf/eot fallback to manage.

Either way, the resolved font becomes the `--wp--preset--font-family--display` / `--body` CSS variable, so every block using those presets (headings, buttons, etc.) updates automatically.

## Ported from the "Ren" (GeneratePress) theme

At René's request, a few pieces of the existing Ren theme's admin panel were carried over into Ren Studio's simpler options page:
- **Typography** (above) — same curated Google Fonts list and local-upload concept, simplified to WOFF2-only and folded into the standard Settings API form (no separate AJAX endpoints).
- **Custom CSS / Custom JS (head + footer)** — plain textareas, output verbatim (CSS stripped of tags; JS only has a stray closing `</script>` removed) — paste only code you trust.
- **Social Links** — Instagram/Flickr/500px/X/LinkedIn/GitHub URL fields, rendered by the `[ren_social_links]` shortcode (already wired into `parts/footer.html`) and available with `ren_get_social_links()` / `ren_social_icon()` if you want to place them elsewhere.
- **Decorative shortcodes**:
  - `[ren_hover variant="fade|accent|gradient|zoom|blur|slide|border" title="…" category="…" url="…"]<img .../>[/ren_hover]` — wraps an image with a hover overlay.
  - `[ren_counter value="40" prefix="" suffix="k+" decimals="0"]` — animated count-up number (triggers once scrolled into view).
  - `[ren_separator type="wave|triangle|slash|curve" color="#0a0a0a" position="top|bottom" height="60"]` — decorative SVG divider between sections.

Left out on purpose (per your choice): the preloader, scroll-to-top button, page-transition mask, button-based archive filter, and the 5-layout archive picker — these assume a classic PHP template structure and would fight with Ren Studio's Query Loop–based templates. Ren Studio's own dropdown+search filter (see below) already covers portfolio filtering.

## Larghezza del logo

In **Appearance → Theme Options → Branding**, sotto il campo Logo, trovi ora **Logo Width** (in px, default 160). Sovrascrive qualunque larghezza sia impostata sul blocco Site Logo nell'header — utile perché quel blocco di default parte molto piccolo (48px) e il logo risulta quasi invisibile.

## Anteprima colori nell'Editor del sito

L'anteprima dentro l'Editor del sito (Aspetto → Editor) ora riflette correttamente lo schema colori/font impostato in Theme Options — prima mostrava sempre i colori di base scritti nel tema (nero) perché quell'anteprima vive in un iframe separato dalla pagina di amministrazione, che non riceveva le variabili CSS generate dal pannello. Corretto agganciando il filtro `block_editor_settings_all`, lo stesso meccanismo che WordPress usa per caricare gli stili di `theme.json` dentro l'iframe.

## Editor di codice autoospitato (CodeMirror)

In **Theme Options → CSS/JS → "Code Editor (CodeMirror)"** trovi un interruttore per attivare, sempre autoospitato (niente CDN), un editor di codice reale — con numeri di riga che restano corretti anche quando una riga va a capo su più righe visive (una `<textarea>` semplice non può farlo in modo affidabile).

**Come usarlo** in un blocco HTML personalizzato: basta scrivere
```html
<textarea class="ren-codemirror" data-mode="xml"></textarea>
```
e diventa automaticamente un editor. Da JavaScript, leggi il contenuto con `document.querySelector('textarea').renCodeMirror.getValue()` invece di `.value`.

## Font Awesome aggiornato a 7.3.1 (era 4.7.0)

Sostituita la versione autoospitata: ora è **Font Awesome 7.3.1**, che include le icone di **X, TikTok, Threads e Bluesky** (assenti nella 4, troppo vecchia). Il pacchetto include tre set completi: **Brands** (loghi di marchi/social), **Solid** e **Regular** (icone generiche: stelle, frecce, spunte, cuori, ecc.) — pronte per essere usate in qualsiasi articolo o pagina, non solo nella tabella colori social.

**Come usarle in un articolo/pagina:** in un blocco HTML personalizzato, scrivi ad esempio:
```html
<i class="fa-solid fa-star"></i>       <!-- icona piena -->
<i class="fa-regular fa-star"></i>     <!-- icona outline -->
<i class="fa-brands fa-instagram"></i> <!-- logo di un brand -->
```
Sfoglia l'elenco completo su [fontawesome.com/search](https://fontawesome.com/search?o=r&m=free) (filtra per "Free") per trovare il nome esatto di ogni icona.

**Cambia la sintassi delle classi**: la vecchia FA4 usava `fa fa-facebook`; da FA6 in poi si usa `fa-brands fa-facebook` (o `fa-solid`/`fa-regular` a seconda dello stile). Se hai contenuti esistenti con la vecchia sintassi, le icone smetterebbero di apparire — la tabella colori social è già aggiornata con le classi corrette.

## Font Awesome autoospitato (niente CDN esterno)

In **Appearance → Theme Options → CSS/JS** trovi ora **"Font Awesome Icons"**: attivalo per caricare Font Awesome 4 (le classi `fa fa-*`, incluse quelle usate dalla tabella colori social) direttamente dai file inclusi nel tema — nessuna richiesta verso server esterni. Spento di default per non appesantire chi non usa icone.

Perché autoospitare: caricare font/librerie da un CDN esterno trasmette l'indirizzo IP del visitatore a quel server terzo. Un tribunale tedesco ha già stabilito che questo, per i Google Fonts caricati da CDN senza consenso, viola il GDPR — lo stesso principio si applica a qualunque risorsa esterna simile. Con questa opzione attiva non parte alcuna richiesta esterna, quindi il problema non si pone. Se avevi seguito il mio consiglio precedente di aggiungere l'`@import` di Font Awesome nel Custom CSS, ora puoi rimuoverlo e usare questo interruttore al suo posto.

## Font locali: più formati (WOFF2 + WOFF + TTF + OTF)

L'upload dei font personalizzati (Typography → Upload Local Font) ora accetta, per ciascuno slot, fino a 4 file: **WOFF2** (consigliato, unico obbligatorio), più **WOFF**, **TTF**, **OTF** come fallback opzionali — stessa filosofia del vecchio tema Ren. Basta caricare almeno il WOFF2; gli altri formati servono solo se hai già i file (es. un font acquistato che arriva solo in `.ttf` o `.otf`) e vuoi includerli senza convertirli.

## Tabelle responsive: classe ".ren-table"

Nuova classe riutilizzabile per qualunque tabella del sito, con lo stesso sistema usato per i plugin RNA (righe che diventano card sotto i 680px, con etichette al posto delle intestazioni nascoste) — ma qui i colori arrivano dal pannello, non da CSS scritto a mano.

**Come usarla:** aggiungi un blocco Tabella nativo di Gutenberg, poi in **Impostazioni blocco → Avanzate → Classi CSS aggiuntive** scrivi `ren-table`. Fatto — non serve scrivere `data-label` a mano sulle celle: un piccolo script (`assets/js/responsive-tables.js`) le legge automaticamente dalle intestazioni al caricamento della pagina.

**Colori dedicati** in *Theme Options → Colors*: Table Header Background, Table Header Text, Table Row Color 1, Table Row Color 2 — questi ultimi due alternano automaticamente per creare l'effetto zebra, senza bordi tra le righe (tutti "Auto" di default: rispettivamente Accent, Background, Background, Surface).

## Portate dal vecchio tema Ren: Header, Tipografia avanzata, Blog, Accent Hover

Quattro aggiunte dal pannello del tema Ren originale, adattate all'architettura a blocchi di Ren Studio:

**Header** (nuova tab): spaziatura logo (margine superiore/inferiore in px) e font del menu di navigazione (dimensione, peso, spaziatura lettere) con anteprima live.

**Typography → Heading Sizes & Weights**: tabella con dimensione (px) + peso + altezza riga per H1, H2, H3, H4, H5, H6, Body, Lead (paragrafi introduttivi grandi) e Small — più granulare del semplice "Font Sizes" già presente. "Lead" e "Small" si applicano rispettivamente ai testi con classe `has-large-font-size` e `has-small-font-size` usate in tutto il tema.

**Blog → Archive Layout**: scegli tra **Grid** (griglia a 3 colonne, quella attuale) o **List** (immagine accanto al testo, riga per riga). Cambia solo il CSS — nessun nuovo template richiesto. Le altre 3 opzioni del vecchio tema (Masonry, Metro, Justified) richiedevano una libreria JS esterna (Isotope) incompatibile con l'architettura a Query Loop di questo tema, quindi non sono state portate.

**Branding → Accent Hover**: colore usato dai bottoni al passaggio del mouse, al posto di passare al colore principale del testo. Lascialo vuoto per il comportamento di default.

> **Nota v2.0.1**: la voce "Body" in Heading Sizes & Weights ora copre anche il testo degli articoli (`.ren-article-body`) e i paragrafi in generale, non solo il tag `&lt;body&gt;` — prima l'ereditarietà CSS non riusciva a raggiungere il corpo degli articoli, che ha una regola di leggibilità propria più specifica.

## Velocità di ricomparsa dell'header

In **Theme Options → Header → Reveal Speed** (default 250ms) controlli quanto tempo impiega l'header a scorrere completamente in vista quando risali. Imposta a 0 per uno scatto istantaneo senza animazione. Riguarda solo la ricomparsa — la scomparsa scendendo resta sempre 1:1 con lo scroll, senza transizione temporizzata (è proprio quello che evita lo scatto durante la discesa).

## Larghezza testo articolo: 910px (era 680px)

Su tua indicazione (riferimento a speckyboy.com), ho allargato la colonna di lettura da 680px a **910px**, con la colonna dell'articolo che passa dal 70% al 72% e la sidebar dal 30% al 28% — meno spazio vuoto sprecato tra il testo e la sidebar. Vale sia per il template standard sia per "Magazine Hero".

## Coerenza allineamento tra header, hero e corpo articolo

Corretto un disallineamento strutturale: header, hero ed corpo testo usavano larghezze diverse **centrate ciascuna in modo indipendente**, creando "scalini" visivi invece di un unico margine sinistro condiviso. Ora:
- Il template "Magazine Hero" usa la stessa larghezza dell'header e del resto del sito (1280px, era 800px) — titolo e logo si allineano
- Il corpo dell'articolo (larghezza di lettura 680px, per la leggibilità) parte dallo stesso bordo sinistro della colonna invece di centrarsi al suo interno

**Larghezza generale del sito**: portata da 1300px a **1280px** — il limite superiore esatto del range raccomandato per il 2026 (1140–1280px). 680px per la larghezza di lettura del testo resta invariato, già dentro il range consigliato (640–760px).

## Allineamento titolo articolo

In **Theme Options → Blog → Post Title Alignment** scegli Sinistra/Centro/Destra come valore **generale** per il titolo e la riga data/autore/tempo di lettura sui singoli articoli.

Puoi anche **sovrascriverlo per il singolo articolo**: apri l'articolo nell'editor, nel pannello laterale destro trovi il box **"Title Alignment"** con "Auto" (segue l'impostazione generale) oppure Sinistra/Centro/Destra specifici per quell'articolo. Il resto del corpo articolo resta sempre allineato a sinistra in ogni caso.

## Altezza header: valore diretto, non più calcolato automaticamente

In **Theme Options → Header → Header Height** trovi ora un campo in px (default 135) che imposta direttamente lo spazio riservato sotto l'header fisso — al posto del calcolo automatico via JavaScript, che si è dimostrato inaffidabile in alcuni casi (probabilmente per interferenza della barra di amministrazione durante i test da loggati). Se noti uno spazio vuoto o una sovrapposizione tra header e contenuto, ispeziona l'header col browser (altezza nel box model) e imposta qui lo stesso valore esatto.

## Header che scompare/riappare allo scroll

In **Theme Options → Header → Scroll Behavior**, attiva **"Sticky header: hide when scrolling down, reappear when scrolling up"**. L'header resta fisso in cima (sempre, indipendentemente da questa opzione), si nasconde scorrendo verso il basso e torna visibile scorrendo verso l'alto, ovunque tu sia nella pagina. Aggiunge anche una leggera ombra quando l'header è "sopra" contenuto già scorso (sempre attiva, indipendente dal toggle).

Tecnicamente usa `position: fixed` (non `sticky`): `sticky` costringe il browser a ricalcolare a ogni fotogramma di scroll se l'elemento è "agganciato", un calcolo che entra in conflitto con l'aggiornamento continuo della posizione via JavaScript e causa scatti. `fixed` evita quel ricalcolo, lasciando il movimento fluido. Lo spazio che l'header occuperebbe nel flusso normale della pagina (essendo `fixed`, ne esce) viene compensato con un padding-top sul `&lt;body&gt;`, calcolato dinamicamente sull'altezza reale dell'header.

## Classi CSS personalizzate sul menu

Il blocco Navigazione, come qualsiasi blocco, ha un campo "Classi CSS aggiuntive" nel pannello Avanzate dell'editor — assegna una classe lì, poi stilizzala da Theme Options → CSS/JS → Custom CSS.

## Modello alternativo per gli articoli: "Magazine Hero"

Ora anche i singoli Articoli hanno un modello alternativo selezionabile (prima era una possibilità solo per le Pagine): apri un articolo → pannello laterale destro → **Impostazioni articolo → Modello → "Magazine Hero"**.

Cosa cambia rispetto al modello standard:
- Sopra il titolo: data + categoria centrate
- Il titolo si trova dentro una **banda colorata a piena larghezza** (colore impostabile in *Theme Options → Colors → Post Hero Background*; di default segue l'Accent Color)
- Subito sotto, l'**immagine in evidenza** occupa tutta la larghezza del container (1300px, non del browser) — non serve fare nulla, la mette in automatico
- Il resto (corpo articolo, tag, navigazione, commenti, sidebar) resta identico al modello standard

## Colori per singolo elemento (Titoli, Testo, Link)

Nuova tab **Colors** in Appearance → Theme Options: colore per **Titoli**, **Testo**, **Link** e **Link (hover)**, indipendenti dal Color Scheme generale. Ogni campo di default è "automatico" (segue lo schema Dark/Light) — imposta un colore per sovrascriverlo, clicca **"Auto"** per tornare al comportamento automatico.

## Dimensioni testo (Font Sizes)

In **Typography** trovi anche **Font Sizes**: un campo in pixel per ciascuna delle 6 taglie usate in tutto il tema (Small/Medium/Large/XL/XXL/Hero). Lasciando un campo vuoto resta il comportamento responsive "fluido" di default (che si adatta automaticamente alla larghezza schermo); inserendo un valore, quella taglia diventa fissa su tutti gli schermi.

## Sfondo chiaro come base del tema

**Il tema ora nasce chiaro (bianco) di default**, sia in `theme.json` sia come impostazione predefinita in Theme Options. Il toggle **Color Scheme** (Dark/Light) resta disponibile se in futuro vuoi provare la versione scura, ma non serve più configurarlo per avere il sito chiaro — è già così appena installato.

Perché il cambiamento: le **miniature di anteprima** nell'elenco "Tutti i template" (Aspetto → Editor → Modelli) vengono generate da WordPress leggendo direttamente i colori scritti in `theme.json`, senza passare dal pannello Opzioni Tema — per questo restavano nere anche impostando "Light" da lì. Rendendo il chiaro la base vera del tema (non solo un'opzione sovrapposta a runtime), anche quelle miniature ora sono corrette.

## Sfondo chiaro o scuro

In **Appearance → Theme Options → Branding** trovi ora **Color Scheme**: Dark (sfondo nero, come da impostazione iniziale) o Light (sfondo bianco, testo scuro). Cambia 5 variabili CSS (background, surface, foreground, muted, border) usate da tutto il tema — non serve modificare nulla altrove.

Una cosa a cui fare attenzione passando a Light: **Acid Green** come colore d'accento ha un contrasto debole su testi piccoli su sfondo bianco (è pensato per un accento su sfondo nero). Su Light conviene usare **Electric Violet** o **Hot Coral**, oppure un colore Custom più scuro/saturo.

## Sfondi (colore o immagine) su Hero, Blog e Portfolio

L'Hero in homepage e le intestazioni degli archivi Blog/Portfolio (`archive.html`, `archive-portfolio.html`) sono ora blocchi Gruppo a **larghezza piena** (edge-to-edge), con il testo che resta comunque leggibile e centrato all'interno. Per ognuno di questi puoi impostare, direttamente dall'editor, senza toccare il codice:
- Seleziona il blocco Gruppo (Hero, o l'intestazione dell'archivio)
- Pannello **Stili** (icona a goccia) → **Sfondo**: qui trovi sia il **Colore** sia una nuova opzione **Immagine** per caricare uno sfondo fotografico
- Il testo sopra resta leggibile grazie al contenuto interno "constrained" (larghezza massima 1300px, centrato)

Se vuoi lo stesso trattamento anche su altre sezioni, chiedimelo pure — basta avvolgerle in un blocco Gruppo a larghezza piena come ho fatto qui.

## Nuove pagine: About Me, Services, Skills & Experience

Tre nuovi pattern originali (non copiati da template di terzi), pensati per pagine standalone create da **Pagine → Aggiungi nuova**:
- **About Me** (`ren/about-me`) — ritratto + bio + "quick facts" (dove sei, focus, disponibilità)
- **Services (Detailed)** (`ren/services-detail`) — lista estesa dei servizi, più dettagliata del teaser a 3 colonne della homepage
- **Skills & Experience** (`ren/skills-experience`) — barre percentuali animate (si riempiono quando scorri la pagina) + timeline di esperienza lavorativa e formazione

Per usarli: crea una nuova Pagina, apri l'inseritore di blocchi (+) → scheda **Pattern** → categoria **"Ren Studio: Pages"**. Tutti e tre hanno testo segnaposto — sostituiscilo con i tuoi contenuti (nome, bio, servizi reali, percentuali skill, esperienze).

## Newsletter form

The CTA on the homepage (`patterns/newsletter-cta.php`) is a plain HTML form with `action="#"`. Point it at your email provider:
- **Mailchimp / Brevo / ConvertKit etc.**: replace the `action` URL and `name="email"` attribute with the ones from your provider's embed/API docs, or swap the block for their official WordPress plugin's form block.
- For a fully custom flow, wire it to a REST endpoint registered in `inc/rest-fields.php` (a stub folder is already loaded from `functions.php`).

## Filtering & search — how it works

`archive-portfolio.html` renders the full portfolio grid server-side (good for SEO — every project is real HTML on load). `assets/js/portfolio-filter.js` then hides/shows cards client-side based on the search box and category `<select>` in the filter bar pattern. Each card's `data-title` / `data-categories` attributes are injected server-side by a `render_block` filter in `inc/template-tags.php` — no custom block or JS framework needed.

## Notes

- `screenshot.png` (1200×900px) isn't included — add one so the theme shows a preview under Appearance → Themes.
- Colors, spacing, and type scale are all editable non-destructively from **Appearance → Editor → Styles** as well as the Theme Options panel — theme.json is the source of truth; the panel only overrides specific CSS variables on top of it.
