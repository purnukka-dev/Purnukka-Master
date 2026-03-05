# ⚓ Purnukka Group Oy: MASTER_PLAN.md

**Päivitetty:** 5.3.2026 (Päivä 1: Generisointi ja Pomminvarma muotti)

## 🏢 Yritys & Operatiivinen hallinto
- **Yhtiö:** Purnukka Group Oy (3600777-3).
- **Omistus:** Perustaja 25%, Kati 25%, Isäukko 50%.
- **Hallinto:** Kati hoitaa laskutuksen (Revolut Business) ja muiden osakkaiden valtuutuksella maksuliikenteen.
- **Kirjanpito:** Kitsas (Työpöytäsovellus).
- **Strategia:** 0 € investointitavoite. Kulut vain perustellusti.

## 🏗 Tekninen arkkitehtuuri & Säännöt (Master-muotti)

### 1. Accumulative Logic (EI KARSINTAA)
- **Sääntö:** Koodia ei saa koskaan vähentää tai "yksinkertaistaa" poistamalla olemassa olevaa toiminnallisuutta (kuten näkymiä, hookkeja tai logiikkaa).
- **Toteutus:** Uudet ominaisuudet, kuten "lukot" tai geneerisyys, rakennetaan olemassa olevan koodin ympärille tai lisätään siihen.
- **Varmistus:** Jokainen tiedostopäivitys on peilattava GitHub-repon nykyiseen tilaan, jotta mitään ei häviä.

### 2. Generisointi & Solo-muotti
- **Sääntö:** Master-koodi ei sisällä kiinteitä nimiä, värejä tai sääntöjä. 
- **Toteutus:** Kaikki dynaaminen data (branding, email, AI-säännöt) luetaan `context.json` -tiedostosta.
- **Solo-optimoitu:** Master on valmis tuotemalli, joka skaalautuu JSON-muuttujilla.

### 3. Pomminvarmuus (Fault Tolerance)
- **Sääntö:** Yksittäinen moduuli tai virheellinen data ei saa kaataa sivustoa.
- **Toteutus:** Moduulien on käytettävä tyyppitarkistuksia ja fail-safe oletusarvoja, jos JSON-kenttä tai ulkoinen API puuttuu.

## 🎯 Liiketoimintayksiköt
- **Purnukka Rental:** Majoitusautomaatio (WordPress + MotoPress + Stripe).
- **Purnukka Property:** Lead Generation (Brokla & Montenegro Real Estate).
- **Purnukka Hosting:** 20i Reseller + MainWP Hub (tuleva API-keskus).

## 🚀 Päivän opit & Muistiinpanot
- **Master-taso:** Pidetään koodi agnostisena. Se on tehdas, ei yksittäinen tuote.
- **Context-First:** JSON on asiakkaan sielu. Hub (API) ampuu tämän datan jokaiselle instanssille omana vastauksenaan.