# ⚡ Purnukka Stack - BACKLOG.md (Päivitetty 2026-03-07)

Kaikki ydinmoduulit on refaktoroitu v1.6.0 MASTER -tasolle (Dependency Injection & Whitelist).

---

## ✅ VALMIIT (Viimeisin päivitys)
* [x] **Core Refactor:** `core.php` hallitsee nyt moduuleja whitelistin kautta.
* [x] **Consistency Refactor:** Kaikki 9 moduulia siirretty luokkapohjaiseen rakenteeseen ($core injection).
* [x] **REST API Security:** Bearer token -suojaus aktivoitu `hub-sync.php` moduulissa.
* [x] **Hardcode Cleanup:** `checkout-logic.php` dynaaminen tuotehaku valmisteltu.
* [x] **Stability Fixes:** `tier-manager.php` ja `checkin-ui.php` vikasietoisuus varmistettu (null-checkit).

---

## 🏗️ RAKENNUSJONO (Seuraavat askeleet)

### 🔴 Prioriteetti 1: Toiminnallinen viimeistely
* [ ] **Tier Access Logic:** Kirjoita varsinainen rajoituslogiikka `tier-manager.php` -tiedoston `check_tier_access()` funktioon (estää pääsyn sivuille, jos taso ei riitä).
* [ ] **Price Dynamization:** (Valinnainen) Siirrä `checkin-ui.php` hinnat koodista `context.json`-tiedostoon.

### 🟡 Prioriteetti 2: SaaS & Skaalaus
* [ ] **REST API Expansion:** Laajenna `/sync` endpointia kattamaan laajemmat asetusmuutokset.
* [ ] **Feature Whitelist Expansion:** Lisää uusia sallittuja moduuleja siten, että ne eivät vaadi ytimen muokkausta.

---

## ⚓ MASTER-SÄÄNNÖT (Muista aina)
1. **Accumulative Logic:** Ei karsita koodia tai poisteta toiminnallisuuksia ilman lupaa.
2. **Context-First:** Kaikki dynaaminen data luetaan `context.json` -tiedostosta.
3. **Visuaalisuus:** Pidetään kiinni sovitusta brändi-ilmeestä ja ikoneista.