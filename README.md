VAII Cvičenie 09

Momentálne je otvorená vetva __MAIN__, ktorá obsahuje _štartér_. Riešenie obsahuje vetva __SOLUTION__.

## Úlohy na cvičenie

Cieľom tohto cvičenia je vytvoriť __webové API__ k serverovej časti chatovacej aplikácie s použitím [frameworku vajiíčko](https://github.com/thevajko/vaiicko).
Samotný chat budeme vytvárať na ďalšom cvičení.

Preštudujte si súbory v štartéri. V adresári `docker\sql` sa nachádza súbor `ddl.sql`, ktorý Vám vytvorí DB tabuľky, potrebné pre tento projekt. Takisto
štartér obsahuje Vaííčko framework. Projekt obsahuje aj `SimpleAuthenticator` na jednoduché overenie mena a hesla. V projekte sa takisto nachádzajú aj
pripravené kontroléry `AuthApiController` a `MessageApiController`. V nich budete implementovať jednotlivé akcie. V projekte sú aj modely `Login` a `Message`,
ktoré zodpovedajú tabuľkám v DB. Všimnite si implementáciu metód `isActive()` a `getAllActive()` v triede `Login`.

1. `AuthApiController` - má na starosti operácie týkajúce sa používateľa a informácií o ňom. Obsahuje nasledovné akcie:
    1. `index` - keďže ide o API, akcia `index` bude vracať HTTP kód _501 Not Implemented_.
    2. `login` - akcia bude očakávať odoslanie prihlasovacích údajov v JSON objekte s dvomi atribútmi `login` a `password`. V prípade, ak ich objekt
       nebude obsahovať, alebo budú mať prázdnu hodnotu, vráti HTTP kód _400 Bad Request_. Ak sa podarí prihlásenie, vytvorí sa nový model `Login` s
       aktuálnym časom. Ak už prihlásenie existuje, upraví sa jeho atribút `last_action`. Pri úspešnom overení loginu sa vráti klientovi prázdna odpoveď (_empty
       response_).
    3. `logout` - skontroluje, či je používateľ prihlásený, ak áno odhlási ho a zmaže záznam o logine s tabuľky `logins`. Ak nie, nič neurobí. Na konci vždy
       vrácia klientovi _empty response_.
    4. `status` - Ak je používateľ prihlásený klient dostane JSON odpoveď s objektom, ktorý ma iba jeden atribút `login`
       a obsahuje login aktuálne prihláseného používateľa. Neprihlásenému používateľovi vráti HTTP kód _401
       Unauthorized_.
    5. `activeUsers` - Ak je používateľ prihlásený klient dostane JSON odpoveď v podobe poľa objektov typu `Login`
       používateľov, ktorý sú aktívny. Ako aktívny používateľ je každý používateľ po dobu 30 sekúnd od posledného dopytu
       na získanie správ, pokiaľ žiadnych aktívnych používateľov nenájde vracia prázdne pole. Ak je používateľ
       neprihlásený vracia HTTP kód _401 Unauthorized_.
3. `MessageApiController` - poskytuje API pre odosielanie a získavanie správ. Všetky akcie tohto kontrolera sú iba pre
   prihlásených používateľov.
    1. `index` - keďže sa jedná o API, index bude vracať HTTP kód _501 Not Implemented_
    2. `sendMessage` - očakáva odoslanie potrebných dát v JSON objekte s dvoma povinnými atribútmi: `recipient`
       a `message`. Ak ich objekt neobsahuje klientovi sa zašle HTTP odpoveď _400 Bad Request_. Ďalej platí:
        * `recipient` obsahuje `null` ak správa nie je privátna. Ak je privátna tento atribút obsahuje login
          používateľa, komu je určená.
        * atribút `message` nesmie obsahovať prázdnu hodnotu.
        * ak atribút `recipient` obsahuje hodnotu a daný používateľ nie je aktívny, klientovi sa zašle HTTP odpoveď _400
          Bad Request_
        * Ak sa správa uloží klientovi sa zašle _empty response_.
    3. `getMessages` - vráti vždy posledných maximálne 30 správ. Klientovi sa posielajú správy, ktorá odoslal, verejné
       správy a jemu poslané privátne správy. Metóde sa môže zaslať atribút `lastId`, ktorý zašle klientovi jemu
       prístupné správy od daného Id. API teda umožňuje prebrať aj neskoršie správy.

Nakoľko náležitostí, ktoré je potrebne kontrolovať je veľa, pridali sme k tomuto projektu __HTTP testy__. Tie sa
nachádzajú v `<root>/test/Tests.http`. Pár poznámok:

* Testy needitujte.
* Vytvorte API tak, aby všetky testy boli úspešne.
* Jednotlivé HTTP dopyty sa dajú púšťať jednotlivo ale je potrebné chápať s akým kontextom pracujú.

# Docker

Framework ma v adresári `<root>/docker` základnú konfiguráciu pre spustenie a debug web aplikácie. Všetky potrebné
služby sú v `docker-compose.yml`. Po ich spustení sa vytvorí:

- __WWW document root__ je nastavený adresár riešenia, čiže web bude dostupný na
  adrese [http://localhost/](http://localhost/). Server má pridaný modul pre
  ladenie móde" (`xdebug.start_with_request=yes`).
- webový server beží na __PHP 8.2__ s [__Xdebug 3__](https://xdebug.org/) nastavený na port __9003__ v "auto-štart" móde
- PHP ma doinštalované rozšírenie __PDO__
- databázový server s vytvorenou _databázou_ a tabuľkami `messages` a `users` na porte __3306__ a bude dostupný
  na `localhost:3306`. Prihlasovacie údaje sú:
    - MYSQL_ROOT_PASSWORD: db_user_pass
    - MYSQL_DATABASE: databaza
    - MYSQL_USER: db_user
    - MYSQL_PASSWORD: db_user_pass
- phpmyadmin server, ktorý sa automatický nastavený na databázový server na porte __8080__ a bude dostupný na
  adrese [http://localhost:8080/](http://localhost:8080/)
