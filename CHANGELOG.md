# Changelog

## Unreleased

## 1.2.0 – 2026-11-11

- **Neu: Bedienhilfe komplett überarbeitet.** Neues Symbol (Person im Kreis) statt Rollstuhl. Der Text daneben erscheint nur, wenn die Maus darüber fährt oder die Tastatur den Button erreicht.
- **Neu:** Besucher wählen selbst **DE oder EN** und **Hell oder Dunkel**. Was beim ersten Besuch gilt, stellst du im Backend ein (Standard-Sprache, Standard-Design). Dazu: englische Beschriftung und „Abstand nach oben“, falls der Button einen Chat-Button verdeckt.
- **Neu:** Jede Funktion hat ein Symbol und ein **(?)**, das kurz erklärt, was sie tut.
- **Neu:** 10 Funktionen, die wirklich wirken: Schriftgröße (90–150 %, auch bei festen Pixel-Schriften), hoher Kontrast, Graustufen, mehr Abstand, lesbare Schrift, Links hervorheben, Fokus sichtbar, Bewegung stoppen (auch Videos), großer Mauszeiger, Lese-Lineal.
- Die Bedienhilfe liegt in einem abgeschirmten Bereich (Shadow DOM). Theme- oder Builder-CSS kann sie nicht mehr verändern.
- Gespeichert wird weiter nur im Browser des Besuchers (localStorage). Frühere Einstellungen der Besucher werden einmalig zurückgesetzt.
- Nach dem Update bitte Cache leeren und das Theme neu kompilieren (`bin/console cache:clear` und `bin/console theme:compile`).

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
