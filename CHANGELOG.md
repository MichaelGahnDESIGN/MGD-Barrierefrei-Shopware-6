# Changelog

## [2.1.1] - 2026-11-25

- Aktueller Baukasten, einheitliche Version.

## 1.6.1 – 2026-11-24

- **Neue Ansicht „Weitere Plugins“** auf der Seite „MGD Barrierefrei“: mit Bildern, Kategorien, Suche, Preis-Filter und einem Fenster „Details“, im Design der „MGD Plugin-Kollektion“.
- Die Bilder holt dein Shop-Server selbst von theme.michael-gahn.de und speichert sie geschützt. Der Browser lädt keine Bilder von fremden Seiten.
- Klappt nach „Jetzt aktualisieren“ der letzte Schritt nicht, steht jetzt ein klarer Hinweis da, was zu tun ist.
- Technik: Baukasten neu erzeugt (Pro-Repo ab a2005ea).

## 1.6.0 – 2026-11-23

Diese Version ist eine Brückenversion. Deine Einstellungen bleiben genau so, wie sie sind.

- **Neue Seite „MGD Barrierefrei“** unter **Einstellungen → Erweiterungen**. Dort siehst du Version, Update-Stand, weitere Plugins und den Schalter „Installation melden“.
- **Updates über theme.michael-gahn.de:** Gibt es eine neue Version, zeigt die Seite den Knopf **„Jetzt aktualisieren“**. Vorher werden Herkunft (GitHub), Größe und Prüfsumme der Datei geprüft. Es gibt kein automatisches Update.
- Den Knopf sehen nur Admins mit den Rechten „Erweiterungen hochladen“ und „Erweiterungen verwalten“. Klappt das Update nicht, zeigt die Seite einen Download-Link und 3 einfache Schritte.
- **Weitere Plugins** stehen jetzt auf dieser Seite. Die alte Seite „MGD Weitere Plugins“ und der alte Update-Check über GitHub sind abgeschaltet.
- **Löschen:** Deinstallierst du das Plugin, wird die Installation bei theme.michael-gahn.de abgemeldet.
- Die tägliche Meldung läuft jetzt über eine eingeplante Aufgabe aus dem Baukasten (`mgd_kit.mgd_barrierefrei_shopware.ping`). Die alte Aufgabe `mgd_barrierefrei.install_report` ist raus.
- Technik: gemeinsamer Plugin-Baukasten aus dem Pro-Repo unter `src/Kit`. Der alte Install-Client unter `src/Install` ist raus.

## 1.5.1 – 2026-11-18

- Die Anmeldung nutzt jetzt den eigenen Produktnamen **mgd-barrierefrei-shopware**. So zählt theme.michael-gahn.de Shopware-Installationen getrennt von JTL und WordPress. Wer 1.5.0 hatte, wird einmal neu angemeldet.

## 1.5.0 – 2026-11-18

- **Neu: Anmeldung der Installation (Free-Lizenz).** Das Plugin meldet sich einmal bei theme.michael-gahn.de an und danach höchstens einmal am Tag. Gesendet werden nur: Plugin-Name, Version, Shopsystem, die Domain deines Shops und eine zufällige Installations-ID. Keine Besucher-, Kunden- oder Bestelldaten.
- Gemeldet wird beim Installieren, Aktivieren und Updaten und danach einmal am Tag über eine geplante Aufgabe (Scheduled Task). Nie im Storefront.
- Ab Werk eingeschaltet. Abschalten in der Plugin-Konfiguration unter **Anmeldung der Installation**. Dann wird der Eintrag auf der Plattform sofort gelöscht. Das Plugin funktioniert genauso weiter.

## Unreleased

## 1.4.0 – 2026-11-14

- Gemeinsame Versionsnummer **1.4.0** für alle Produkte von Michael Gahn DESIGN. So sieht man auf einen Blick, welche Fassungen zusammengehören.
- Keine Änderung an den Funktionen.
- Nach dem Update bitte Cache leeren (`bin/console cache:clear`).

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
