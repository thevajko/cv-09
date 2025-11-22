VAII Cvičenie 09

Momentálne je otvorená vetva __MASTER__, ktorá obsahuje _štartér_. Riešenie obsahuje vetva __SOLUTION__.

## Úlohy na cvičenie

### Ako funguje http/https protokol?

1. Otvorte si **Web developer tool** a prepnite sa na kartu **Sieť**.
2. Načítajte ľubovoľný web z internetu a pozrite si, ako vyzerá komunikácia medzi prehliadačom a web serverom.
3. Akú verziu protokolu a aké metódy najčastejšie používajú? Čo všetko vieme z tejto komunikácie zistiť?

### Jednoduchá práca s dátami vo formáte JSON vo frameworku Vaííčko

1. Preštudujte si metódu `loopbackMessage()` v kontroléri `HomeController` a pohľad `loopbackMessage.view.php`.
   Ako funguje posielanie a prijímanie dát? Ako framework vie, že boli dáta poslané pomocou AJAX volania?

### Tvorba API pre chat aplikáciu

Cieľom tohto cvičenia je vytvoriť __webové API__ k serverovej časti čet aplikácie s použitím [frameworku vajiíčko](https://github.com/thevajko/vaiicko).
Samotný čet budeme vytvárať na ďalšom cvičení.

Preštudujte si súbory v štartéri. Štartér obsahuje Vaííčko framework. V adresári `docker\sql` sa nachádza súbor `ddl.sql`, ktorý Vám vytvorí DB tabuľky,
potrebné pre tento projekt a naplní DB niekoľkými záznamami. Projekt obsahuje aj `SimpleAuthenticator` na jednoduché overenie mena a hesla. V projekte sa
nachádzajú aj pripravené kontroléry `API\AuthController` a `API\MessageController`. V nich budete implementovať jednotlivé akcie.
V projekte nájdeme tiež modely `User` a `Message`, ktoré zodpovedajú tabuľkám v DB. Všimnite si implementáciu metód `isActive()` a `getAllActive()` v
triede `User`.

Nakoniec si prezrite testy v súbore `\test\Tests.http`. Skúste ich spustiť. 22 testov skončí chybou. Úlohou cvičenia bude implementovať metódy kontrolérov tak,
aby všetky testy prebehli úspešne.

1. `API\AuthController` - Má na starosti operácie týkajúce sa používateľa a vracia informácie o ňom. Obsahuje tieto už implementované akcie:
    - `index` - Keďže ide o API, akcia `index` bude vracať HTTP kód _501 Not Implemented_.
    - `login` - Akcia prihlasovacie údaje v JSON objekte s dvomi atribútmi `login` a `password`. V prípade, ak ich objekt neobsahuje, alebo budú mať prázdnu
      hodnotu, vráti HTTP kód _400 Bad Request_. Ak sa podarí prihlásenie, vytvorí sa nový model `User` s aktuálnym časom. Ak už prihlásenie existuje,
      upraví sa jeho atribút `last_action`. Po úspešnom overení používateľa sa vráti klientovi prázdna odpoveď (_empty response_).

   Je potrebné vytvoriť tieto akcie:
    - `logout` - Skontroluje, či je používateľ prihlásený. Ak áno, odhlási ho a zmaže záznam o jeho logine z DB. Ak nie je prihlásený, neurobí nič. Na konci
      vždy vráti klientovi _empty response_.
    - `status` - Ak je používateľ prihlásený, klient dostane JSON odpoveď s objektom, ktorý obsahuje atribút `login` a má hodnotu aktuálne mena prihláseného
      používateľa. Neprihlásenému používateľovi vráti HTTP kód _401 Unauthorized_.
    - `activeUsers` - Ak je používateľ prihlásený, klient dostane JSON odpoveď v podobe zoznamu používateľov (pole objektov typu `User`), ktorí sú aktívni.
      Aktívny používateľ je každý používateľ po dobu 30 sekúnd od poslednej žiadosti o získanie správ. Pokiaľ žiadnych aktívnych používateľov nenájde, vráti
      prázdne pole. Ak je používateľ neprihlásený, vráti HTTP kód _401 Unauthorized_.

2. `API\MessageController` - Poskytuje API pre odosielanie a získavanie správ. Všetky akcie tohto kontroléra sú určené iba pre prihlásených používateľov.
   Obsahuje tieto už implementované akcie:
    - `index` - Keďže sa jedná o API, index bude vracať HTTP kód _501 Not Implemented_.
    - `receiveMessage` - Očakáva odoslanie správy s dvoma povinnými atribútmi: `recipient` a `message`. Ak ich objekt neobsahuje, klientovi sa zašle HTTP
      odpoveď
      _400 Bad Request_. Ďalšie podrobnosti:
        * Atribút `recipient` obsahuje `null`, ak je správa verejná. Ak je správa privátna, tento atribút obsahuje login používateľa, komu je určená.
        * Atribút `message` nesmie obsahovať prázdnu hodnotu.
        * Ak atribút `recipient` obsahuje hodnotu a daný používateľ nie je aktívny, klientovi sa zašle HTTP odpoveď _400 The recipient is not available_.
        * Po uložení správy na serveri, sa klientovi zašle _empty response_.

   Je potrebné vytvoriť túto akciu:
    - `getAllMessages` - Získa všetky správy. Pošlú sa správy, ktoré používateľ odoslal, verejné správy a jemu poslané privátne správy. Metóda môže prijať
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
