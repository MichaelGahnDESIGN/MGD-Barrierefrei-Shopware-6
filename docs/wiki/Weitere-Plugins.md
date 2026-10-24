# Weitere Plugins

Auf dieser Seite im Shopware-Admin siehst du weitere kostenlose Plugins von Michael Gahn DESIGN, die zu deinem Shop passen können.

## So öffnest du die Seite

1. Melde dich im Shopware-Admin an.
2. Klicke links auf **Einstellungen**.
3. Scrolle zum Bereich **Erweiterungen**.
4. Klicke auf **MGD Weitere Plugins**.

Du brauchst dafür das Recht, Systemeinstellungen zu lesen (`system_config:read`). Hast du es nicht, fehlt der Eintrag.

## Was siehst du dort?

- Eine Karte pro Plugin mit Name, Beschreibung und Version.
- Das Wort **Empfohlen**, wenn wir das Plugin zu MGD Barrierefrei besonders empfehlen. Diese Plugins stehen ganz vorn.
- **Kostenlos** oder **Kostenpflichtig** und **Demnächst**, wenn ein Plugin noch nicht fertig ist.
- Links: **ZIP herunterladen** (nur bei fertigen, kostenlosen Plugins), **Mehr erfahren** und **Quellcode auf GitHub**.

Dieses Plugin selbst steht nicht in der Liste.

## Ein Plugin installieren

Es wird nie etwas automatisch installiert. Du entscheidest selbst:

1. Klicke auf **ZIP herunterladen**.
2. Öffne in Shopware **Erweiterungen → Meine Erweiterungen → Apps**.
3. Lade das ZIP hoch, installiere und aktiviere das Plugin.

Mach vorher eine Sicherung von Datenbank und Dateien.

## Liste neu laden

- Die Liste wird 6 Stunden gespeichert.
- Mit **Liste aktualisieren** holst du sie sofort neu. Das geht höchstens einmal pro Minute.

## Wenn die Liste nicht erscheint

- Du siehst den Hinweis, dass die Liste nicht erreichbar ist. Das ist nicht schlimm. Dein Shop läuft ganz normal weiter.
- Warte ein paar Minuten und klicke dann auf **Liste aktualisieren**.
- Prüfe, ob dein Server Verbindungen nach `https://theme.michael-gahn.de` aufbauen darf (Firewall).
- Fehlt der Menüpunkt ganz, führe auf dem Server aus: `bin/console assets:install` und `bin/console cache:clear`. Lade die Admin-Seite danach neu (Strg+F5).

## Datenschutz

- Dein **Shop-Server** ruft die öffentliche Liste von `theme.michael-gahn.de` ab. Der Server dort sieht die IP-Adresse deines Shop-Servers. Es werden keine Shop-, Kunden- oder Zugangsdaten gesendet.
- Die **Bilder** lädt dein **Browser**, und nur auf dieser Admin-Seite. Dort sieht der Server deine IP-Adresse.
- **Besucher deines Shops** sind nicht betroffen. Im Shop wird nichts von theme.michael-gahn.de geladen.
- Trage diese Verbindungen bei Bedarf in deine Datenschutzdokumentation ein.
