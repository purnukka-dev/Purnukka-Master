# ⚡ Purnukka Stack - BACKLOG.md

Tämä on dynaaminen kehityskartta. Prioriteetti 1 sisältää kriittiset tekniset korjaukset, jotta "Puhdas Master-ydin" toteutuu.

---

## 🚀 PIKASYÖTTÖ (Lisää uudet ideat tähän)
* [ ] 

---

## 🏗️ RAKENNUSJONO (Priorisoidut tehtävät)

### 🔴 Prioriteetti 1: Kriittiset korjaukset (Välittömästi)
* [ ] **Admin Menu Consolidation:** Yhdistä kaikki `admin_menu` -rekisteröinnit yhteen pisteeseen (`Core.php`).
* [ ] **Path Constants:** Lisää `PURNUKKA_STACK_PATH` ja `PURNUKKA_STACK_URL` määritelmät loader-tiedostoon (`mu-plugins/purnukka-stack-loader.php`).
* [ ] **Stability Fix:** Luo `views/tier-info.php` tai lisää `null-check` ennen include-kutsuja (estää PHP-virheet, jos tiedosto puuttuu).
* [ ] **Hardcode Cleanup:** Siirrä tuote-ID `276` pois koodista ja sijoita se `purnukka-config/context.json` -tiedostoon dynaamisesti luettavaksi.
* [ ] **Context Protection:** Varmista, ettei deploy (`deploy.yml`) ylikirjoita asiakkaan `context.json` asetuksia palvelimella.

### 🟡 Prioriteetti 2: Hub & SaaS (Seuraava vaihe)
* [ ] **REST API Implementation:** Toteuta `/wp-json/purnukka/v1/sync` endpoint (Mainittu README:ssä, mutta puuttuu koodista). Tämä on kriittinen SaaS-toiminnan kannalta.
* [ ] **Consistency Refactor:** Toteuta `constructor injection` ($core-parametri) kaikille moduuleille — poista epäkonsistentti riippuvuus `$GLOBALS['purnukka']` -muuttujasta.
* [ ] **Package Templates Update:** Laajenna `package-*.json` tiedostoja `features`-avaimilla, jotta moduulien automaattinen on/off -logiikka toimii.
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
4. **Visuaalisuus:**