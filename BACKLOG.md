# ⚡ Purnukka Stack - BACKLOG.md

Tämä on dynaaminen kehityskartta. Prioriteetti 1 sisältää kriittiset tekniset korjaukset ja SaaS-automaation peruspilarit.

---

## 🚀 PIKASYÖTTÖ (Lisää uudet ideat tähän)
* [ ] 

---

## 🏗️ RAKENNUSJONO (Priorisoidut tehtävät)

### 🔴 Prioriteetti 1: Kriittiset korjaukset & Dynaamisuus (Välittömästi)
* [x] **Admin Menu Consolidation:** Yhdistetty keskistetysti `Core.php`-tiedostoon.
* [x] **Path Constants:** `PURNUKKA_STACK_PATH` ja `PURNUKKA_STACK_URL` lisätty loaderiin.
* [ ] **Dynaaminen Villa-logiikka:** Toteuta MB (Meta Box) + WC (WooCommerce) linkitys, jossa kohteet luodaan APIn kautta juoksevalla slugilla (`villa-1`, `villa-2`).
* [ ] **MB-kentät dynaamisiksi:** API-ohjaus kentille: nimi, kapasiteetti, max-henkilöt, lisähenkilöraja ja perushinta.
* [ ] **Hardcode Cleanup:** Poista ID `276` koodista ja vaihda se dynaamiseen hakuun, joka tunnistaa linkitetyn WC-tuotteen villan slugin perusteella.
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
2. **Context-First:** Kaikki dynaaminen data (ID:t, värit, tekstit) luetaan `context.json` -tiedostosta tai dynaamisista CPT-kentistä.
3. **Hybridimalli:** Brooklyn-demot ajetaan vain Child-tasolla, Master pidetään teknisenä ytimenä.
4. **Visuaalisuus:** Älä muuta ikoneita tai tekstejä ilman erillistä lupaa.