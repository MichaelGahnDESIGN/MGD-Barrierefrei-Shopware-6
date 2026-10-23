# Sicherheitsrichtlinie

## Melden

Bitte melde Sicherheitslücken vertraulich über das Sicherheitsformular des Repositories. Keine Zugangsdaten, Bestelldaten, Screenshots mit Kundendaten oder Tokens in Issues veröffentlichen.

## Schutzprinzipien

- Der Update-Endpunkt ist nur für authentifizierte Shopware-Administratoren mit `system_config:read` erreichbar.
- Die GitHub-Prüfung liest nur öffentliche Release-Metadaten und lädt kein Archiv automatisch herunter.
- Das Bedienpanel arbeitet lokal im Browser; Einstellungen liegen nur in `localStorage`.
- Konfigurierter Text wird beim Rendern durch Twig escaped.

## Unterstützte Version

Die jeweils aktuelle stabile Release-Version erhält Sicherheitskorrekturen. Vor Updates bitte Shop- und Datenbanksicherung erstellen.
