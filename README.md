# MGD Barrierefrei für Shopware 6

Kostenlose, lokal arbeitende Bedienhilfe mit einer konfigurierbaren Erklärung zur Barrierefreiheit. Das Plugin überträgt keine Nutzungsdaten und verwendet weder Tracking noch externe Schriftarten.

![Kompatibilität](https://img.shields.io/badge/Shopware-6.6%20%7C%206.7-0d6efd) ![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-1f883d)

## Funktionen

- zugängliches Bedienpanel mit Kontrast, Graustufen, sichtbarem Fokus, Schriftgröße und reduzierter Bewegung
- Speicherung der persönlichen Auswahl ausschließlich lokal im Browser
- konfigurierbare Schaltfläche, Position und Akzentfarbe
- öffentliche Route `/barrierefreiheit` mit Text aus den Plugin-Einstellungen
- Kontakt, verantwortliche Stelle, eigener Status und bekannte Barrieren frei pflegbar
- sichtbarer Hinweis auf [Michael-Gahn.de](https://michael-gahn.de) im Bedienpanel, stets in einem neuen Tab mit `noopener noreferrer`
- geschützter GitHub-Release-Check über die Shopware-Admin-API
- Admin-Seite **Weitere Plugins** mit weiteren kostenlosen Plugins für Shopware 6 (nur im Admin, nichts wird automatisch installiert)

## Installation und Updates

1. Lade `MgdBarrierefrei-<Version>.zip` aus dem passenden GitHub-Release.
2. Öffne in Shopware **Erweiterungen → Meine Erweiterungen → Apps** und lade das ZIP hoch.
3. Installiere und aktiviere das Plugin.
4. Konfiguriere die Angaben unter **Erweiterungen → MGD Barrierefrei → Konfiguration**.
5. Lege im Navigationsbereich einen Link auf `/barrierefreiheit` an.

Jedes GitHub-Release enthält das fertige Storefront-Paket (Ordner `src/Resources/app/storefront/dist`). Ein manuelles Bauen der Administration oder Storefront ist nicht nötig, du brauchst kein Node. Nach der Installation einmal **Einstellungen → System → Caches & Indizes → Cache leeren** (bzw. `bin/console theme:compile`), damit Shopware das Theme neu zusammenstellt. Der Admin-Endpunkt `POST /api/_action/mgd-barrierefrei/update/check` prüft ausschließlich die Release-Metadaten und erfordert die Shopware-Berechtigung `system_config:read`. Die Installation eines Updates wird weiterhin bewusst über Shopware ausgelöst. Vor einer Aktualisierung gehören Sicherung und Wartungsfenster zum Ablauf.

## Weitere Plugins (Admin-Seite)

Unter **Einstellungen → Erweiterungen → MGD Weitere Plugins** zeigt das Plugin weitere kostenlose Plugins von Michael Gahn DESIGN für Shopware 6.

- Die Liste kommt von `https://theme.michael-gahn.de/api/catalog/plugins.json` und wird 6 Stunden zwischengespeichert.
- Der Button **Liste aktualisieren** lädt sie neu (höchstens einmal pro Minute).
- Es wird nichts automatisch installiert. Du lädst das ZIP herunter und lädst es selbst unter **Erweiterungen → Meine Erweiterungen → Apps** hoch.
- Ist der Katalog nicht erreichbar, steht dort nur ein Hinweis. Dein Shop ist nie betroffen.
- Bilder und Symbole lädt dein Browser nur auf dieser Admin-Seite. Im Shop für Besucher gibt es keine Anfrage an theme.michael-gahn.de.
- Admin-Endpunkt: `POST /api/_action/mgd-barrierefrei/catalog` (Berechtigung `system_config:read`).

Mehr dazu im [Wiki: Weitere Plugins](docs/wiki/Weitere-Plugins.md).

## Erklärung zur Barrierefreiheit

Die erzeugte Seite ist eine verständliche technische Vorlage, keine Rechtsberatung oder Konformitätszusage. Prüfe vor Veröffentlichung insbesondere Geltungsbereich, Kontakt, tatsächlichen Stand, bekannte Barrieren und die zuständige Durchsetzungsstelle. Das Plugin trifft keine automatische rechtliche Bewertung.

## Entwicklung und Release

```sh
composer validate --strict
find src -name '*.php' -print0 | xargs -0 -n1 php -l
php tests/catalog-check.php
node --check src/Resources/public/administration/js/mgd-barrierefrei.js
sh scripts/build-storefront.sh --check
sh scripts/build-release.sh
```

Änderst du das Storefront-JavaScript (`src/Resources/app/storefront/src/js`), danach `sh scripts/build-storefront.sh` ausführen und die geänderte `dist`-Datei mit committen. Das Release-Skript baut sie sowieso frisch, die CI schlägt bei veraltetem `dist` an.

Das Paket hat genau einen Plugin-Ordner `MgdBarrierefrei/` als ZIP-Wurzel. Die SHA-256-Datei im Release dokumentiert das gebaute Paket.

## Sicherheit und Datenschutz

Die Bedienhilfen speichern ausschließlich Einstellungen wie Kontrast oder Schriftgröße in `localStorage` auf dem jeweiligen Gerät. Keine personenbezogenen Daten, Tokens oder Shop-Zugangsdaten gelangen an MGD oder GitHub. Einzige Ausnahme ist die Admin-Seite **Weitere Plugins**: Dort verbindet sich der Shop-Server mit theme.michael-gahn.de (IP-Adresse des Servers sichtbar, keine Shop- oder Kundendaten) und der Browser lädt Bilder (IP-Adresse der angemeldeten Person sichtbar). Details stehen in [SECURITY.md](SECURITY.md) und im [Wiki](docs/wiki/Home.md).

## Wiki

- [Start](docs/wiki/Home.md)
- [Konfiguration](docs/wiki/Konfiguration.md)
- [Erklärung prüfen](docs/wiki/Erklaerung-pruefen.md)
- [Updates](docs/wiki/Updates.md)
- [Weitere Plugins](docs/wiki/Weitere-Plugins.md)
- [Datenschutz und Sicherheit](docs/wiki/Datenschutz-und-Sicherheit.md)
