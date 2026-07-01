# Webprogrammierung: Gruppe F

## Beteiligte
- Maren Schaa
- Lena Schaake
- Fabian Rosenberg

## Ausgelassene Teilaufgaben


## Umsetzung der Teilaufgaben
- Es gibt beim Bilderupload nun drag and drop
- Während man eine Form einträgt, erhält man live feedback



## Bekannte Fehler und Mängel
- Bei der Form wird nur geprüft, ob ein User name schon vergeben ist.
- Es gibt kein Feedback zum User, wenn sein Änderungen zu Profil oder Post erfolgreich waren.



------------------------
- Die Id Speicherungen zum User sind unterschiedlich zwischen den Speichersystemen. einmal ist es ein random int mit 4 Stellen und einmal ein aufzählendes int.

------------------------
- Die Aufteilung der Funktionen zu Posts in root ist unschön und muss refactored werden zu einer Datei.

- Dem User fehlt die Möglichkeit der Änderung seiner Daten und das Löschen seines Kontos.
- Den Beiträgen fehlt auch eine Möglichkeit zur Änderung und zum Löschen.
- Die Suche exisitert auch nur im Modell, der Controller und der Einbau auf der Seite fehlt.
- Wir nutzen noch nicht überall nur exceptions, sondern auch error strings.

------------------------
- Es gibt unterschiedliche Navigationen. Eine für beitragsbezogene Seiten und eine für die Anderen (Footer, Profil, Nutzerliste).
- Die Beitragssuchleiste hat kein Label und bekommt auch keins.
- Links sind lila auf grau und schwierig lesbar.


## Besonderheiten des Projektes
- Speichersystem ist wechselbar durch Änderung der Datei config.php in Root.
	- Nach dem Ändern müssen die Websitedaten im Browser gelöscht werden, um starke Fehler zu vermeiden.


## Userdaten Dokumentation
Für Datenbank
- user1, user1@web.com, test1
- user2, user2@web.com, test2
- user3, user@web.com, test3
- Marius, zeichen123

Für das Filesystem
- user4, user4@web.com, test4
- user5, user5web.com, test5
- user6, user6web.com, test6

## Aufgaben Blatt 6
Für das Impressum, Datenschutzerklärung etc. wurden Generatoren verwendet. Weiter wurden Musternamen bzw. Musterdaten verwendet, da wir unsere eigenen Daten in dem Kontext ungern preisgeben möchten. B

Genutzer Generator für das Impressum: https://impressum-generator.de/
Genutzer Generator für Datenschutzerklärung: https://datenschutz-generator.de/?dsgo=free


