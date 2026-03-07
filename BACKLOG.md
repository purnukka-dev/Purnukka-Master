# ⚡ Purnukka Stack - BACKLOG.md

Tämä on dynaaminen kehityskartta. Prioriteetti 1 sisältää kriittiset tekniset korjaukset, jotta "Puhdas Master-ydin" toteutuu.

---

## 🚀 PIKASYÖTTÖ (Lisää uudet ideat tähän)
* [ ] 

---

## 🏗️ RAKENNUSJONO (Priorisoidut tehtävät)

### 🔴 Prioriteetti 1: Kriittiset korjaukset (Välittömästi)
* [x] **Admin Menu Consolidation:** Yhdistetty keskistetysti `Core.php`-tiedostoon.
* [x] **Path Constants:** `PURNUKKA_STACK_PATH` ja `PURNUKKA_STACK_URL` lisätty loaderiin.
* [ ] **Hardcode Cleanup:** Siirrä tuote-ID `276` pois `checkout-logic.php`:sta ja lue se dynaamisesti `context.json`:sta.
* [ ] **Stability Fix:** Lisää `null-check` ennen näkymien include-kutsuja (erityisesti `views/tier-info.php`).
* [ ] **Context Protection:** Varmista, ettei deploy (`deploy.yml`) ylikirjoita asiakkaan `context.json` asetuksia palvelimella.

### 🟡 Prioriteetti 2: Hub & SaaS (Seuraava vaihe)
* [ ] **REST API Implementation:** Toteuta `/wp-json/purnukka/v1/sync` endpoint. Kriittinen SaaS-toiminnalle.
* [ ] **Consistency Refactor:** Toteuta `constructor injection` ($core-parametri) kaikille moduuleille — poista riippuvuus `$GLOBALS['purnukka']` -muuttujasta.
* [ ] **Package Templates Update:** Laajenna `package-*.json` tiedostoja `features`-avaimilla automaatiota varten.
* [ ] **Admin Dashboard:** Visualisoi tilastot `views/admin-dashboard.php` tiedostoon.

### 🔵 Prioriteetti 3: Kehitysideat & Visio
* [ ] **Repo-siivous:** `.vscode/` poisto ja turhien json-templatetien (`package-unlimited.json` jne.) siivous.
* [ ] **AI-integraatio:** Laajenna `ai-connector.php` vastaamaan vieraiden kysymyksiin dynaamisesti.
* [ ] **HDMI-Purnukka:** TV-integraatio, session nollaus ja brändätty tervetuloa-näkymä.
* [ ] **Smart Lock:** Automaattinen koodin lähetys uloskirjautumisen jälkeen.

---

## ⚓ MASTER-SÄÄNNÖT (Muista aina)
1. **Accumulative Logic:** Ei karsita koodia tai poisteta toiminnallisuuksia ilman lupaa. Pidetään backlog kertyvänä.
2. **Context-First:** Kaikki dynaaminen data (ID:t, värit, tekstit) luetaan `context.json` -tiedostosta.
3. **Hybridimalli:** Brooklyn-demot ajetaan vain Child-tasolla, Master pidetään teknisenä ytimenä.
4. **Visuaalisuus:** Älä muuta ikoneita tai tekstejä ilman erillistä lupaa.