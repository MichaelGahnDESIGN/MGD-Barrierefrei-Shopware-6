# Changelog

## Unreleased

- **Fix:** Das Release-ZIP enthält jetzt das fertige Storefront-Paket (`dist/storefront/js/mgd-barrierefrei/mgd-barrierefrei.js`). Vorher fehlte es, dadurch ging das Bedienpanel im Shop ohne eigenen Storefront-Build nicht. Neues Skript `scripts/build-storefront.sh`, die CI prüft, dass `dist` zur Quelle passt.
- Neue Admin-Seite **Einstellungen → Erweiterungen → MGD Weitere Plugins**: zeigt weitere kostenlose Plugins für Shopware 6 aus dem Katalog von theme.michael-gahn.de.
- Die Liste wird sechs Stunden zwischengespeichert. Ist der Katalog nicht erreichbar, läuft der Shop normal weiter.
- Es wird nichts automatisch installiert. Bilder werden nur auf dieser Admin-Seite geladen.
- Die Admin-Seite wird jetzt auch in Shopware 6.7 geladen (neue Datei `administration/.vite/entrypoints.json`).

## 1.0.0 – 2026-10-23

- Erste kostenlose Version mit lokalem Bedienpanel und Storefront-Erklärung.
- Konfigurierbare Kontakt- und Statusangaben in den Shopware-Einstellungen.
- Geschützter, rein lesender GitHub-Release-Check und reproduzierbares ZIP-Paket.
