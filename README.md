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

## Installation und Updates

1. Lade `MgdBarrierefrei-<Version>.zip` aus dem passenden GitHub-Release.
2. Öffne in Shopware **Erweiterungen → Meine Erweiterungen → Apps** und lade das ZIP hoch.
3. Installiere und aktiviere das Plugin.
4. Konfiguriere die Angaben unter **Erweiterungen → MGD Barrierefrei → Konfiguration**.
5. Lege im Navigationsbereich einen Link auf `/barrierefreiheit` an.

Jedes GitHub-Release enthält bereits das fertige Storefront-Paket. Ein manuelles Bauen der Administration ist nicht nötig. Der Admin-Endpunkt `POST /api/_action/mgd-barrierefrei/update/check` prüft ausschließlich die Release-Metadaten und erfordert die Shopware-Berechtigung `system_config:read`. Die Installation eines Updates wird weiterhin bewusst über Shopware ausgelöst. Vor einer Aktualisierung gehören Sicherung und Wartungsfenster zum Ablauf.

## Erklärung zur Barrierefreiheit

Die erzeugte Seite ist eine verständliche technische Vorlage, keine Rechtsberatung oder Konformitätszusage. Prüfe vor Veröffentlichung insbesondere Geltungsbereich, Kontakt, tatsächlichen Stand, bekannte Barrieren und die zuständige Durchsetzungsstelle. Das Plugin trifft keine automatische rechtliche Bewertung.

## Entwicklung und Release

```sh
composer validate --strict
find src -name '*.php' -print0 | xargs -0 -n1 php -l
sh scripts/build-release.sh
```

Das Paket hat genau einen Plugin-Ordner `MgdBarrierefrei/` als ZIP-Wurzel. Die SHA-256-Datei im Release dokumentiert das gebaute Paket.

## Sicherheit und Datenschutz

Die Bedienhilfen speichern ausschließlich Einstellungen wie Kontrast oder Schriftgröße in `localStorage` auf dem jeweiligen Gerät. Keine personenbezogenen Daten, Tokens oder Shop-Zugangsdaten gelangen an MGD oder GitHub. Details stehen in [SECURITY.md](SECURITY.md) und im [Wiki](docs/wiki/Home.md).

## Wiki

- [Start](docs/wiki/Home.md)
- [Konfiguration](docs/wiki/Konfiguration.md)
- [Erklärung prüfen](docs/wiki/Erklaerung-pruefen.md)
- [Updates](docs/wiki/Updates.md)
- [Datenschutz und Sicherheit](docs/wiki/Datenschutz-und-Sicherheit.md)
