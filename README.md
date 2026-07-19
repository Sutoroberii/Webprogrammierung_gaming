# Webprogrammierung: Gruppe F

## Beteiligte
- Maren Schaa
- Lena Schaake
- Fabian Rosenberg

## Voraussetzungen zum Betrieb
- php ist nötig, js ist optional

## Funktionalitäten
- Nutzer erstellen, ändern, löschen
- Posts erstellen, ändern, löschen, einsehen

## Javascript, Ajax Funktionalitäten
- Bei Registrierung wird der Nutzername auf Verfügbarkeit geprüft
- Bei Bildupload ist drag and drop möglich

## Nicht umgesetzte Teilaufgaben
- Wir haben kein "extra Feature", also weder Kommenare noch Likes, noch Suche

## Bekannte Fehler und Mängel
- Die Suche exisitert nur im Modell, der Controller und der Einbau auf der Seite fehlt. Die Suchleiste auf der Seite selbst ist Deko.
- Der Bildupload überprüft nicht richtig die Dateitypen.
- Die Beispieldaten werden nicht bei Neuerstellen der Datenbank wieder eingefügt.
- Usernamenänderungen nicht direkt möglich, nur durch manuelles neu erstellen

## Besonderheiten des Projektes
- Speichersystem ist wechselbar durch Änderung der Datei config.php in Root.
	- Nach dem Ändern müssen die Websitedaten im Browser gelöscht werden, um starke Fehler zu vermeiden.

## Userdaten Dokumentation
Für Datenbank
- user1, user1@web.com, test1
- user2, user2@web.com, test2

Für das Filesystem
- user4, user4@web.com, test4
- user5, user5web.com, test5
- user6, user6web.com, test6