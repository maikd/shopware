# easymarketing Shopware Module

## Installation des Moduls

Falls Sie bereits das Modul installiert haben, so deaktivieren Sie dieses bevor Sie die nächsten Schritte durchführen.

1. [Das Modul hier herunterladen und entpacken.](https://github.com/EASYMARKETING/shopware/archive/master.zip)

2. Den Ordner SwpEasymarketing per FTP auf den Server kopieren unter: `[ROOT]/engine/Shopware/Plugins/Community/Frontend`
 
3. Dann im Backend einfach unter Einstellungen Plugin-Manager das Modul installieren und aktivieren.

## Konfiguration vom Modul
		
Um die Konfiguration vom Modul vornehmen zu können, laden Sie das Backend neu. 
Gehen Sie nun über das Menü `Marketing`
Hier gibt es nun einen weiteren Eintrag mit dem Titel `Easymarketing`, welchen Sie nun anklicken.

Das Modul wird nun in einem Fenster innerhalb des Backends geöffnet. 
Klicken Sie dort den Reiter `Einstellungen` an und nehmen Sie folgende Einstellungen vor:

* API Token
Um den API Token zu erhalten, Ã¶ffnen Sie bitte dazu Ihre API Einstellungen in Ihrem easymarketing Account unter `Meine Daten -> API`.
Dort kopieren Sie sich den API Token und fügen Sie diesen in das Feld ein.

* Root Kategorie
Hier haben Sie eine Liste von sämtlichen Kategorien in Ihrem Shop. 
Die hier ausgewählte Kategorie wird an easymarketing übermittelt. 
Dadurch werden von easymarketing nur Kategorien und Produkte unterhalb dieser festgelegten Kategorie abgefragt.

* Facebook Button anzeigen
Mit dieser Einstellung haben Sie die Möglichkeit zu entscheiden, ob der Facebook Button von easymarketing in Ihrem Checkout auf der Bestellbestätigungsseite angezeigt werden soll.

Sind alle oben genannten Punkte konfiguriert, klicken Sie nun auf den Button `Speichern`.
Im Hintergrund wird die Installation und Konfiguration vom Modul durchgeführt.

Nachdem die Einrichtung abgeschlossen wurde, sehen Sie unter dem Tab `Übersicht`, ob diese erfolgreich war.

## Änderungen am Shop
Sollten Sie einmal die Domain oder das Verzeichnis von Ihrem Shop wechseln, dann können Sie die Änderungen problemlos und einfach über das erneute Betätigen des Buttons `Speichern` im Tab `Einstellungen` easymarketing übertragen.

## Zukünfige Updates vom Modul
Bei zukünftigen Updates vom Modul, gehen Sie die Schritte 1 und 2 aus dem Punkt `Installation des Moduls` aus dieser Anleitung nochmals durch.
Die Versionsnummer vom Modul wird bei jedem Update erneuert, sodass letzendlich neben dem Icon für das Löschen des Moduls ein blauer runder Pfeil erscheint.
Diesen klicken Sie bitte einmal an. Dadurch wird das Update durchgeführt.
Nach Abschluss des Updates wird eine höhere Versionsnummer in der Spalte `Version` angezeigt.

## Für Entwickler

* Im `master` gucken ob es nicht bereits bestehende bug-fixes gibt.

* Im `issue tracker` gucken ob das Feature bzw. der Bug schon behoben wurde.

* Forke das Projekt.

* Starte einen Feature/Bugfix branch.

* Commite so lange bis Du zufrieden bist mit der Arbeit.

* Erstelle einen Pull-Request mit dem erstellten Branch.
