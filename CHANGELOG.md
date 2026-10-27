# Changelog

## Unreleased

## 1.1.0 – 2026-10-27

- **Neu:** Admin-Seite **Einstellungen → Erweiterungen → MGD Weitere Plugins**. Sie zeigt weitere kostenlose Plugins für Shopware 6 aus dem Katalog von theme.michael-gahn.de.
- Die Liste wird sechs Stunden zwischengespeichert. Ist der Katalog nicht erreichbar, läuft der Shop normal weiter. Es wird nichts automatisch installiert.
- Bilder werden nur auf dieser Admin-Seite geladen und nur, wenn der Katalog welche liefert (derzeit nicht). Im Shop für Besucher wird nichts von theme.michael-gahn.de geladen.
- **Fix:** Die Admin-Seite wird jetzt auch in Shopware 6.7 geladen (neue Datei `administration/.vite/entrypoints.json`).
- **Fix:** Das Release-ZIP enthält jetzt das fertige Storefront-Paket (`dist/storefront/js/mgd-barrierefrei/mgd-barrierefrei.js`). Vorher fehlte es, dadurch ging das Bedienpanel im Shop ohne eigenen Storefront-Build nicht.
- Nach der Installation oder dem Update bitte den Cache leeren (`bin/console cache:clear`).

## 1.0.0 – 2026-10-23

- Erste kostenlose Version mit lokalem Bedienpanel und Storefront-Erklärung.
- Konfigurierbare Kontakt- und Statusangaben in den Shopware-Einstellungen.
- Geschützter, rein lesender GitHub-Release-Check und reproduzierbares ZIP-Paket.
