VAII Cvičenie 09

Momentálne je otvorená vetva __SOLUTION__, ktorá obsahuje riešenie. _Štartér_ obsahuje vetva __MASTER__.

## Úlohy na cvičenie

### Jednoduchá práca s JSON vo frameworku Vaííčko

1. Vytvorte metódu `showJson()` v kontroléri `HomeController`. Metóda vytvorí jednu správu (model `Message`) a zobrazí ju vo formáte JSON.
2. Preštudujte si pohľad `sendJson.view.php`. Čo robí?
3. Vytvorte metódu `receiveJson()` v kontroléri `HomeController`. Metóda príjme JSON data správy a pošle ich späť.

### Tvorba API pre chat aplikáciu

Cieľom tohto cvičenia je vytvoriť __webové API__ k serverovej časti chatovacej aplikácie s použitím [frameworku vajiíčko](https://github.com/thevajko/vaiicko).
Samotný chat budeme vytvárať na ďalšom cvičení.

Preštudujte si súbory v štartéri. V adresári `docker\sql` sa nachádza súbor `ddl.sql`, ktorý Vám vytvorí DB tabuľky, potrebné pre tento projekt. Takisto
štartér obsahuje Vaííčko framework. Projekt obsahuje aj `SimpleAuthenticator` na jednoduché overenie mena a hesla. V projekte sa takisto nachádzajú aj
pripravené kontroléry `AuthApiController` a `MessageApiController`. V nich budete implementovať jednotlivé akcie. V projekte sú aj modely `Login` a `Message`,
ktoré zodpovedajú tabuľkám v DB. Všimnite si implementáciu metód `isActive()` a `getAllActive()` v triede `Login`. Prezrite si aj testy v
súbore `\test\Tests.http`. Skúste ich spustiť. Úlohou cvičenia bude implementovať metódy kontrolérov tak, aby všetky testy prebehli úspešne.

1. `AuthApiController` - Má na starosti operácie týkajúce sa používateľa a informácií o ňom. Obsahuje nasledovné akcie:
    1. `index` - Keďže ide o API, akcia `index` bude vracať HTTP kód _501 Not Implemented_.
    2. `login` - Akcia bude očakávať odoslanie prihlasovacích údajov v JSON objekte s dvomi atribútmi `login` a `password`. V prípade, ak ich objekt
       nebude obsahovať, alebo budú mať prázdnu hodnotu, vráti HTTP kód _400 Bad Request_. Ak sa podarí prihlásenie, vytvorí sa nový model `Login` s
       aktuálnym časom. Ak už prihlásenie existuje, upraví sa jeho atribút `last_action`. Po úspešnom overení pou69vate+la sa vráti klientovi prázdna odpoveď (
       _empty response_).
    3. `logout` - Skontroluje, či je používateľ prihlásený. Ak áno odhlási ho a zmaže záznam o jeho logine z DB. Ak nie je prihlásený, neurobí nič. Na konci
       vždy vráti klientovi _empty response_.
    4. `status` - Ak je používateľ prihlásený, klient dostane JSON odpoveď s objektom, ktorý obsahuje atribút `login` a má hodnotu aktuálne mena prihláseného
       používateľa. Neprihlásenému používateľovi vráti HTTP kód _401 Unauthorized_.
    5. `activeUsers` - Ak je používateľ prihlásený, klient dostane JSON odpoveď v podobe poľa objektov typu `Login` používateľov, ktorí sú aktívni. Aktívny
       používateľ je každý používateľ po dobu 30 sekúnd od posledného dopytu na získanie správ. Pokiaľ žiadnych aktívnych používateľov nenájde, vráti prázdne
       pole. Ak je používateľ neprihlásený, vráti HTTP kód _401 Unauthorized_.
3. `MessageApiController` - Poskytuje API pre odosielanie a získavanie správ. Všetky akcie tohto kontroléra sú určené iba pre prihlásených používateľov.
    1. `index` - Keďže sa jedná o API, index bude vracať HTTP kód _501 Not Implemented_.
    2. `sendMessage` - Očakáva odoslanie správy s dvoma povinnými atribútmi: `recipient` a `message`. Ak ich objekt neobsahuje, klientovi sa zašle HTTP odpoveď
       _400 Bad Request_. Ďalšie podrobnosti:
        * Atribút `recipient` obsahuje `null`, ak je správa verejná. Ak je správa privátna, tento atribút obsahuje login používateľa, komu je určená.
        * Atribút `message` nesmie obsahovať prázdnu hodnotu.
        * Ak atribút `recipient` obsahuje hodnotu a daný používateľ nie je aktívny, klientovi sa zašle HTTP odpoveď _400 The recipient is not available_.
        * Po uložení správy na serveri, sa klientovi zašle _empty response_.
    3. `getMessages` - Vráti všetky správy. Pošlú sa správy, ktoré používateľ odoslal, verejné správy a jemu poslané privátne správy. Metóda môže prijať
       parameter `lastId`, ktorý klientovi zašle správy od zadaného id.

Pár poznámok k testom:

* Testy needitujte.
* Vytvorte API tak, aby všetky testy prebehli úspešne.
* Jednotlivé HTTP dopyty sa dajú púšťať jednotlivo, niektoré sú závislé ne iných testoch, preto nemusia byť úspešné.

## Ako nájdem vetvu môjho cvičenia?

Pokiaľ sa chcete dostať k riešeniu z cvičenia je potrebné otvoriť si príslušnú _vetvu_, ktorej názov sa skladá:

__MIESTNOST__ + "-" + __HODINA ZAČIATKU__ + "-" + __DEN__

Ak teda navštevujete cvičenie pondelok o 08:00 v RA323, tak sa vaša vetva bude volať: __RA323-08-PON__

# Použitý framework

Cvičenie používa framework vaííčko dostupný v repozitári [https://github.com/thevajko/vaiicko](https://github.com/thevajko/vaiicko). Pre úspešné riešenie
projektu je potrebné spustiť docker konfiguráciu zo súboru `docker\docker-compose.yml`.  
