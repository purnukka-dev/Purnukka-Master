# ⚡ Purnukka Stack - BACKLOG.md

Tämä on dynaaminen kehityskartta. Prioriteetti 1 sisältää kriittiset tekniset korjaukset ja SaaS-automaation peruspilarit.

---

## 🚀 PIKASYÖTTÖ (Lisää uudet ideat tähän)
* [ ] 

---

## 🏗️ RAKENNUSJONO (Priorisoidut tehtävät)

### 🔴 Prioriteetti 1: Kriittiset korjaukset & Turvallisuus (Välittömästi)
* [cite_start][x] **Admin Menu Consolidation:** Yhdistetty keskistetysti `Core.php`-tiedostoon. 
* [cite_start][x] **Path Constants:** `PURNUKKA_STACK_PATH` ja `PURNUKKA_STACK_URL` lisätty loaderiin. 
* [cite_start][x] **Dynaaminen Villa-logiikka:** MB + WC linkitys toteutettu `hub-sync.php` moduulilla. 
* [cite_start][x] **Hardcode Cleanup:** ID `276` poistettu ja korvattu dynaamisella tunnistuksella. 
* [cite_start][ ] **REST API Security:** Lisää Bearer token -autentikaatio `hub-sync.php` endpointiin (KRIITTINEN). 
* [cite_start][ ] **Price Dynamization:** Siirrä `checkin-ui.php` kovakoodatut vierashinnat (30€, 20€, jne.) `context.json`-tiedostoon. 
* [cite_start][ ] **Context Protection Fix:** Päivitä `deploy.yml` varmistamaan, ettei olemassa olevaa `context.json` -tiedostoa ylikirjoiteta. 
* [cite_start][ ] **Stability Fix:** Lisää `null-check` ennen näkymien include-kutsuja (erityisesti `views/tier-info.php`). 

### 🟡 Prioriteetti 2: Hub & SaaS (Seuraava vaihe)
* [cite_start][x] **Admin Dashboard:** Visualisointi toteutettu (`views/admin-dashboard.php`). 
* [cite_start][ ] **Consistency Refactor:** Toteuta `constructor injection` ($core-parametri) kaikille moduuleille poistaaksesi `$GLOBALS['purnukka']` -riippuvuudet (Estää Fatal Errors). 
* [cite_start][ ] **Feature Switch Whitelist:** Lisää `core.php` handle_feature_switch -funktioon tarkistus sallituista moduuleista. 
* [cite_start][ ] **REST API Implementation:** Laajenna `/sync` endpoint kattamaan koko stackin tila. 
* [cite_start][ ] **Tier Access Implementation:** Toteuta varsinainen uudelleenohjaus tai poista tyhjä `check_tier_access()` funktiosta `tier-manager.php`. 

### 🔵 Prioriteetti 3: Kehitysideat & Visio
* [cite_start][ ] **Security Hardening:** Lisää SRI-hashit (integrity) ulkoisille CDN-latauksille (Font Awesome, Google Fonts) `checkin-ui.php` -tiedostossa. 
* [cite_start][ ] **Repo-siivous:** `.vscode/` poisto ja turhien json-templatetien siivous. 
* [cite_start][ ] **AI-integraatio:** Laajenna `ai-connector.php` vastaamaan vieraiden kysymyksiin dynaamisesti. 
* [cite_start][ ] **HDMI-Purnukka:** TV-integraatio ja brändätty tervetuloa-näkymä. 

---

## ⚓ MASTER-SÄÄNNÖT (Muista aina)
1. **Accumulative Logic:** Ei karsita koodia tai poisteta toiminnallisuuksia ilman lupaa. [cite_start]Pidetään backlog kertyvänä. 
2. [cite_start]**Context-First:** Kaikki dynaaminen data luetaan `context.json` -tiedostosta tai dynaamisista CPT-kentistä. 
3. [cite_start]**Hybridimalli:** Brooklyn-demot ajetaan vain Child-tasolla, Master pidetään teknisenä ytimenä. 
4. [cite_start]**Visuaalisuus:** Älä muuta ikoneita tai tekstejä ilman erillistä lupaa.