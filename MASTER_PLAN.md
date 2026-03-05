# ⚓ Purnukka Group Oy: MASTER_PLAN.md

**Päivitetty:** 5.3.2026 (Päivä 1: Generisointi ja Solo-muotti)

## 🏢 Yritys & Operatiivinen hallinto
- **Yhtiö:** Purnukka Group Oy (3600777-3).
- **Omistus:** Perustaja 25%, Kati 25%, Isäukko 50%.
- **Hallinto:** Kati hoitaa laskutuksen (Revolut Business) ja muiden osakkaiden valtuutuksella maksuliikenteen.
- **Kirjanpito:** Kitsas (Työpöytäsovellus).
- **Strategia:** 0 € investointitavoite. Kulut vain perustellusti (esim. 20i Reseller 25 -paketti).

## 🎯 Liiketoimintayksiköt (The Trinity)

### 1. Purnukka Rental (Majoitusratkaisut)
- **Malli:** Solo-optimoitu geneerinen core.
- **Tekniikka:** WordPress + MotoPress Booking + WooCommerce + Stripe.
- **Tavoite:** Automatisoida yksittäiset kohteet (kuten pilotit) skaalautuvaksi tuotteeksi.

### 2. Purnukka Property (Kiinteistösijoittaminen)
- **Malli:** Lead Generation & Affiliate -bisnes.
- **Kumppanit:** Brokla ja Montenegro Real Estate.
- **Suhdeverkosto:** Perustajan oma sijoituskokemus ja kontaktit Montenegrossa.

### 3. Purnukka Hosting (Infrastruktuuri)
- **Alusta:** 20i Reseller (Whitelabel).
- **Infrarakenne:**
    - `master.purnukka.com`: Kehitysympäristö ("Kulta-instanssi").
    - `hub.purnukka.com`: **MainWP** (Komentokeskus hallintaan). Tulevaisuudessa API-lähde asiakas-JSONeille.

## 🏗 Tekninen arkkitehtuuri & Säännöt (Master-muotti)
1. **Generisointi:** Koodi ei sisällä kiinteitä nimiä, värejä tai sääntöjä. Kaikki asiakaskohtainen data tulee JSON-rakenteesta.
2. **Context-First:** Moduulit tottelevat `purnukka-config/context.json` -tiedostoa. API-valmius on olemassa `core.php`-tasolla.
3. **Design System:** Värit, logot ja yritystiedot injektoidaan dynaamisesti (Branding-moduuli).
4. **Asiakaskohtaisuus:** JSON on aina asiakas- tai instanssikohtainen. Master-koodi vain lukee ja toteuttaa sen.

## 🚀 Päivän opit & Muistiinpanot (AI-muisti)
- **Master-taso:** Pidetään koodi agnostisena. Ei kytketä sitä yhteen kohteeseen edes puheessa.
- **Fail-safe:** Core sisältää oletusarvot, jotta saitti ei kaadu vaikka JSON puuttuisi.
- **API-polku:** API-integraatio on vain noutolohko Coreen; moduulit pysyvät muuttumattomina.