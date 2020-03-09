# TikiSpeiseplan
Speiseplan für INRO und Umgebung.

## config.php
Diese Enthält die Einstellungen zur Datenbank-Verbindung und Login. Muss angepasst werden!

## Einträge abholen
Um Einträge von der API abzufragen, wird gibt es zwei Überagbeparameter an die URL

_mode_

- archive: Zeigt alle verfügbaren Einträge
- nosy: Diese und nächste Woche
- today: Nur die heutigen Einträge

Wird *mode* nicht übergeben, wird nur der Speiseplan dieser Woche angezeigt.

_restaurant_ lässt das Ergebnis nach Restaurant filtern

```
http://.../api.php?restaurant=kunzmann&mode=archive
```

## Einen Eintrag hinzufügen (POST)
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
  "side" : "Beilagensalat,
  "price" : "4.20€"
}
```

## Einen Eintrag entfernen

_todo_