# TikiSpeiseplan
Speiseplan für INRO und Umgebung

## Einen Eintrag hinzufügen
Um einen Eintrag in den Speiseplan vorzunehmen, wird folgendes JSON-Element an die API gesendet.

Der Eintrag _passwort_ entspricht dem Datenbankpasswort. Ohne gültiges Passwort kann kein Eintrag vorgenommen werden.

_operation_ muss _add_ sein, um einen Eintrag hinzuzufügen.

Das _restaurant_ muss in der Datenbank bereits existieren und richtig geschrieben (case-insesitive) sein, damit der Eintrag hinzugefügt wird.

```javascript
{
  "password" : "****",
  "operation": "add",
  "restaurant": "Kunzmann",
  "day": "2020-03-02",
  "description" : "Leggumes",
  "additional_description" : "mit Soße",
  "price" : "4.20€"
}
```
