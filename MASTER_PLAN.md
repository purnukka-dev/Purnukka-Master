# ⚓ Purnukka Group Oy: MASTER_PLAN.md

**Päivitetty:** 5.3.2026 (Päivä 1: Perustus ja Strategia)

## 🏢 Yritys & Operatiivinen hallinto
- **Yhtiö:** Purnukka Group Oy (3600777-3).
- **Omistus:** Perustaja 25%, Kati 25%, Isäukko 50%.
- **Hallinto:** Kati hoitaa laskutuksen (Revolut Business) ja muiden osakkaiden valtuutuksella maksuliikenteen.
- **Kirjanpito:** Kitsas (Työpöytäsovellus).
- **Strategia:** 0 € investointitavoite. Kulut vain perustellusti (esim. 20i Reseller 25 -paketti).

## 🎯 Liiketoimintayksiköt (The Trinity)

### 1. Purnukka Rental (Majoitusratkaisut)
- **Lippulaiva:** `villapurnukka.com` (Ensimmäinen "Solo"-asiakas, 200 €/kk).
- **Tekniikka:** WordPress + MotoPress Booking + WooCommerce + Stripe.
- **Tavoite:** Muuttaa yksittäiset majoituskohteet automaattisiksi kassakoneiksi.

### 2. Purnukka Property (Kiinteistösijoittaminen)
- **Malli:** Lead Generation & Affiliate -bisnes.
- **Kumppanit:** [Brokla](https://brokla.com/) ja [Montenegro Real Estate](https://montenegro-real-estate.com/).
- **Suhdeverkosto:** Perustajan oma sijoituskokemus ja kontaktit Montenegrossa.

### 3. Purnukka Hosting (Infrastruktuuri)
- **Alusta:** 20i Reseller (Whitelabel).
- **Domainit:** `purnukka.com` (Landing page), `purnukka.fi`.
- **Infrarakenne:**
    - `master.purnukka.com`: Kehitysympäristö ("Kulta-instanssi").
    - `hub.purnukka.com`: **MainWP** (Komentokeskus kaikkien saittien hallintaan).

## 🏗 Tekninen arkkitehtuuri & Säännöt
1. **Ei huttua:** Jos jokin ei edistä bisnestä tai säästä rahaa/aikaa, se skipataan.
2. **Context-First:** Koodi on vakioitu (Git), mutta yksilöllisyys (Branding, Tier, API-avaimet) luetaan `purnukka-config/context.json` -tiedostosta.
3. **Control:** SMTP-asetukset ja kriittiset MU-pluginit lukitaan Coren kautta.
4. **MainWP-yhteensopivuus:** `access-control.php` pidetään sellaisena, että Hub pääsee hallitsemaan Child-saitteja.

## 🚀 Päivän opit & Muistiinpanot (AI-muisti)
- **Taso-nimeämiset:** Solo, Growth, Unlimited (työnimet koodissa).
- **Deploy:** GitHub Actionit hoitavat koodin, mutta eivät koske asiakaskohtaiseen configiin.
- **Bisneslogiikka:** Purnukka Group Oy on välikäsi, joka automatisoi teknologian ja rahaliikenteen asiakkaan puolesta.