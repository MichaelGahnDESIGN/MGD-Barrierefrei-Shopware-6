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
- Admin-Seite **MGD Barrierefrei** (ab 1.6.0) mit Info, Updates (Knopf „Jetzt aktualisieren“), weiteren Plugins und dem Schalter „Installation melden“

## Installation und Updates

1. Lade `MgdBarrierefrei-<Version>.zip` aus dem passenden GitHub-Release.
2. Öffne in Shopware **Erweiterungen → Meine Erweiterungen → Apps** und lade das ZIP hoch.
3. Installiere und aktiviere das Plugin.
4. Konfiguriere die Angaben unter **Erweiterungen → MGD Barrierefrei → Konfiguration**.
5. Lege im Navigationsbereich einen Link auf `/barrierefreiheit` an.

Jedes GitHub-Release enthält das fertige Storefront-Paket (Ordner `src/Resources/app/storefront/dist`). Ein manuelles Bauen der Administration oder Storefront ist nicht nötig, du brauchst kein Node. Nach der Installation einmal **Einstellungen → System → Caches & Indizes → Cache leeren** (bzw. `bin/console theme:compile`), damit Shopware das Theme neu zusammenstellt. Vor einer Aktualisierung gehören Sicherung und Wartungsfenster zum Ablauf.

### Updates ab 1.6.0

1. Öffne **Einstellungen → Erweiterungen → MGD Barrierefrei**.
2. Steht dort „Version X ist verfügbar“, klicke **Jetzt aktualisieren**.
3. Das Plugin lädt die ZIP-Datei aus dem GitHub-Release. Es prüft Größe und Prüfsumme (SHA-256). Nur wenn alles stimmt, spielt Shopware sie ein.
4. Danach steht dort „Fertig“. Lade die Seite neu.
5. Klappt es nicht (zum Beispiel fehlen Schreibrechte), zeigt die Seite einen Link zur ZIP-Datei. Lade sie dann selbst unter **Erweiterungen → Meine Erweiterungen → Erweiterung hochladen** hoch und klicke beim Plugin auf **Aktualisieren**.

Den Knopf sehen nur Admins mit den Rechten „Erweiterungen hochladen“ und „Erweiterungen verwalten“.

## Weitere Plugins (ab 1.6.0)

Die Liste steht jetzt auf der Seite **Einstellungen → Erweiterungen → MGD Barrierefrei**. Die alte Seite „MGD Weitere Plugins“ gibt es nicht mehr.

- Die Liste kommt von theme.michael-gahn.de und wird 15 Minuten zwischengespeichert. Antwortet der Server nicht, siehst du die letzte gute Liste.
- Es wird nichts automatisch installiert.
- Im Shop für Besucher gibt es keine Anfrage an theme.michael-gahn.de.

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

## Anmeldung der Installation (ab 1.5.0)

Meldet sich höchstens einmal am Tag bei theme.michael-gahn.de (Plugin, Version, Shopsystem, Domain, zufällige ID). Abschaltbar in den Einstellungen.

1. Die Meldung ist ab Werk eingeschaltet.
2. Gemeldet wird beim Installieren, Aktivieren und Updaten und danach höchstens einmal am Tag über eine geplante Aufgabe. Nie für Besucher im Storefront.
3. Abschalten: Auf der Seite **Einstellungen → Erweiterungen → MGD Barrierefrei** (ab 1.6.0) oder in **Erweiterungen → Meine Erweiterungen → MGD Barrierefrei → Konfigurieren** den Schalter **Installation bei Michael Gahn DESIGN melden** ausschalten und speichern. Dann wird der Eintrag auf der Plattform gelöscht.
4. Deinstallierst du das Plugin (ab 1.6.0), wird der Eintrag auf der Plattform ebenfalls gelöscht.
5. Das Plugin funktioniert immer voll, auch ohne Meldung oder wenn die Plattform nicht erreichbar ist.
- Technik (ab 1.6.0): gemeinsamer Plugin-Baukasten aus dem Pro-Repo (`php bin/build-plugin-kit.php … --platform=shopware6 --namespace='MgdBarrierefrei\Kit' --product=mgd-barrierefrei-shopware --technical-name=MgdBarrierefrei --name='MGD Barrierefrei' --catalog-slug=plugin-barrierefrei-shopware`), hier unter `src/Kit`. Diesen Ordner nicht von Hand ändern, er wird neu erzeugt.
