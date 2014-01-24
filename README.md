# EASYMARKETING Shopware Module

## Installation des Moduls

1. [Das Modul hier herunterladen und entpacken.](https://github.com/EASYMARKETING/shopware/archive/master.zip)

2. Den Ordner SwpEasymarketing per FTP auf den Server kopieren unter: `[ROOT]/engine/Shopware/Plugins/Community/Frontend`
 
3. Dann im Backend einfach unter Einstellungen Plugin-Manager das Modul installieren und aktivieren.

## Konfiguration der Endpunkte
		
Jetzt müssen noch die EASYMARKETING Endpunkte eingetragen werden in Ihrem EASYMARKETING Account. Über die Endpunkte kann EASYMARKETING entsprechende Produkte und Kategorien extrahieren aus Ihrem Shopsystem. Diese Endpunkte sind je Shopsystem unterschiedlich.

Dazu öffnen Sie bitte Ihre API Einstellungen in Ihrem EASYMARKETING Account unter `Meine Daten -> API`

Ändern Sie `example.com` in den Beispiel unten in Ihre Domain.

Produkte API Endpunkt

	https://example.com/easymarketing_api/products
	
Beste Produkte API Endpunkt

	https://example.com/easymarketing_api/best_products
	
Neue Produkte API Endpunkt

	https://example.com/easymarketing_api/new_products

Produkt via ID Endpunkt

	https://example.com/easymarketing_api/product_by_id

Kategorien API Endpunkt

	https://example.com/easymarketing_api/categories
	
**Produkt ID zum testen** 

Hier wird einfach zufällig eine Produkt ID aus Ihrem Shop eingetragen. Diese wird nur zu Test-Zwecken mit angegeben. EASYMARKETING testet dann, ob dieses einzelne Produkt erfolgreich extrahiert werden kann.

Wenn Sie in Ihrem Shop ein Produkt mit der ID `1` haben könnte dies z.B. sein:

	1

**ID der Root Kategorie**

Das ist die ID der höchsten Kategorie in Ihrem Shop. Die `Ober-Kategorie` bzw. `Root-Kategorie` enthält alle Unter-Kategorien Ihres Shopsystem. EASYMARKETING wird dann alle Unter-Kategorien versuchen zu extrahieren. In Ihrem `Kategorie-Verwalter` steht die ID typischerweise in dem Link wenn Sie mit der Maus über die `Ober-Kategorie` navigieren.


**Konfiguration des Shop Token**

Der Shop Token ist ein Passwort Ihres Shops. Dieses Passwort kann auf der Modul-Seite des Plugins definiert werden. EASYMARKETING übermittelt bei jeder Anfrage diesen `Shop Token`. Nur falls der `Shop Token` Ihrem eingegebenem Token entspricht, werden die Anfragen autorisiert. Sie müssen hier also genau den von Ihnen definierten `Shop Token` eingeben.

Beispiel:

Sie haben in Ihrem Backend auf der Modulseite den Token wie folgt definiert:

	  Shop Token: 123123123123
	  
Dann muss genau dieser Token auch in Ihrem EASYMARKETING Account eingegeben werden.


      Shop token: 123123123123
			

## Für Entwickler

* Im `master` gucken ob es nicht bereits bestehende bug-fixes gibt.

* Im `issue tracker` gucken ob das Feature bzw. der Bug schon behoben wurde.

* Forke das Projekt.

* Starte einen Feature/Bugfix branch.

* Commite so lange bis Du zufrieden bist mit der Arbeit.

* Erstelle einen Pull-Request mit dem erstellten Branch.
