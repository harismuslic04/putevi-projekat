# DRŽAVNI UNIVERZITET U NOVOM PAZARU

## INFORMACIONI SISTEMI
### - DOKUMENTACIJA PROJEKTA -

**INFORMACIONI SISTEM ZA ODRŽAVANJE PUTNE INFRASTRUKTURE I SAOBRAĆAJNE SIGNALIZACIJE**
Haris Muslic
**Novi Pazar, 2026.**

---

## SADRŽAJ

1. Korisnički zahtev
2. Strukturna sistemska analiza – SSA
   2.1. Dijagram konteksta
   2.2. Prvi nivo dekompozicije
   2.3. Drugi nivo dekompozicije
      2.3.1. Upravljanje prijavama oštećenja
      2.3.2. Upravljanje radnim nalozima i resursima
      2.3.3. Generisanje analitičkih izveštaja
3. Rečnik podataka
   3.1. Korisnik
   3.2. Deonica
   3.3. Objekat
   3.4. Prijava
   3.5. Nalog
   3.6. Utrošak
   3.7. Nezgoda
4. EER model
5. Relacioni model

---

## 1. Korisnički zahtev

**1. Upravljanje korisničkim ulogama i pristupom:**
*   Sistem koristi unapred definisane korisničke naloge (bez javne registracije).
*   Jasna podela na četiri uloge: Vozač (građanin), Terenski radnik, Dispečer, Menadžer. Svaka uloga ima svoj specifičan kontrolni panel (Dashboard).

**2. Evidencija putne infrastrukture i deonica:**
*   Unos deonica puta (dužina, kategorija, tip asfalta, GPS početka i kraja).
*   Evidentiranje infrastrukturnih objekata (znakovi, semafori, mostovi) uz tačne GPS koordinate, datum postavljanja i period garancije.

**3. Upravljanje prijavama oštećenja:**
*   Mogućnost vozača da na interaktivnoj mapi prijavi problem (udarna rupa, oboren znak).
*   Sistem detekcije duplikata u radijusu od 50m.
*   Praćenje statusa prijave (Prijavljeno, Verifikovano, Nalog izdat, Sanirano).

**4. Upravljanje radnim nalozima i terenskim radom:**
*   Verifikacija prijava od strane dispečera i njihovo pretvaranje u radne naloge.
*   Automatsko dodeljivanje najvišeg prioriteta ("Kritično") za oštećenja na autoputevima.
*   Kreiranje redovnih i vanrednih naloga za terenske radnike.

**5. Evidencija utroška resursa (materijala i mehanizacije):**
*   Pri zatvaranju naloga, radnik unosi tačne količine utrošenog asfalta, znakova, kao i sate rada kamiona i bagera.
*   Sistem automatski izračunava ukupnu cenu intervencije.

**6. Generisanje izveštaja (Menadžerski panel):**
*   Generisanje izveštaja o ukupnim troškovima po kilometru puta.
*   Praćenje godišnjeg budžeta za održavanje.
*   Toplotna mapa oštećenja i saobraćajnih nezgoda (Black spots).

---

## 2. Strukturna sistemska analiza – SSA

### 2.1. Dijagram konteksta

```mermaid
%%{init: {'theme': 'base', 'themeVariables': {'primaryColor': '#ffffff', 'primaryTextColor': '#000000', 'primaryBorderColor': '#000000', 'lineColor': '#000000', 'secondaryColor': '#ffffff', 'tertiaryColor': '#ffffff', 'edgeLabelBackground': '#ffffff'}}}%%
flowchart LR
    Vozac[Vozač / Građanin]
    Radnik[Terenski Radnik]
    Dispecer[Dispečer]
    Menadzer[Menadžer]
    IS((Informacioni Sistem za održavanje puteva))

    Vozac -->|Prijava oštećenja, GPS| IS
    IS -->|Status prijave| Vozac

    Dispecer -->|Verifikacija, Dodela naloga| IS
    IS -->|Prikaz mapa, Liste prijava| Dispecer

    Radnik -->|Utrošak materijala, Status rada| IS
    IS -->|Zaduženi radni nalozi| Radnik

    Menadzer -->|Zahtev za izveštaj| IS
    IS -->|Finansije, KPI, Heatmap| Menadzer
```

### 2.2. Prvi nivo dekompozicije

```mermaid
%%{init: {'theme': 'base', 'themeVariables': {'primaryColor': '#ffffff', 'primaryTextColor': '#000000', 'primaryBorderColor': '#000000', 'lineColor': '#000000', 'secondaryColor': '#ffffff', 'tertiaryColor': '#ffffff', 'edgeLabelBackground': '#ffffff'}}}%%
flowchart TD
    Vozac[Vozač]
    Radnik[Radnik]
    Dispecer[Dispečer]
    Menadzer[Menadžer]

    P1((1.0 Upravljanje prijavama))
    P2((2.0 Upravljanje nalozima))
    P3((3.0 Evidencija infrastrukture))
    P4((4.0 Poslovna analitika))

    D1[(D1: Prijave)]
    D2[(D2: Nalozi i Resursi)]
    D3[(D3: Putevi i Objekti)]

    Vozac -->|Unos nove prijave| P1
    P1 -->|Snimanje prijave| D1
    
    Dispecer -->|Verifikacija prijava| P1
    Dispecer -->|Dodeljivanje naloga| P2
    Dispecer -->|Upravljanje putevima| P3
    
    Radnik -->|Izvršavanje posla| P2
    P2 -->|Snimanje troškova| D2
    P3 -->|Čuvanje objekata| D3
    
    Menadzer -->|Zahteva izveštaje| P4
    
    D1 -->|Podaci o prijavama| P4
    D2 -->|Finansijski podaci| P4
    D3 -->|Podaci o infrastrukturi| P4
```

### 2.3. Drugi nivo dekompozicije

#### 2.3.1. Upravljanje prijavama oštećenja

```mermaid
%%{init: {'theme': 'base', 'themeVariables': {'primaryColor': '#ffffff', 'primaryTextColor': '#000000', 'primaryBorderColor': '#000000', 'lineColor': '#000000', 'secondaryColor': '#ffffff', 'tertiaryColor': '#ffffff', 'edgeLabelBackground': '#ffffff'}}}%%
flowchart LR
    Vozac[Vozač]
    Dispecer[Dispečer]
    
    P11((1.1 Prijem GPS lokacije))
    P12((1.2 Provera duplikat prijava))
    P13((1.3 Verifikacija prijave))

    D1[(D1: Prijave)]

    Vozac -->|GPS i Opis| P11
    P11 --> P12
    P12 -->|Snimanje ako nije duplikat| D1
    D1 -->|Čitanje neobrađenih| P13
    Dispecer -->|Potvrda validnosti| P13
    P13 -->|Ažuriranje statusa| D1
```

#### 2.3.2. Upravljanje radnim nalozima i resursima

```mermaid
%%{init: {'theme': 'base', 'themeVariables': {'primaryColor': '#ffffff', 'primaryTextColor': '#000000', 'primaryBorderColor': '#000000', 'lineColor': '#000000', 'secondaryColor': '#ffffff', 'tertiaryColor': '#ffffff', 'edgeLabelBackground': '#ffffff'}}}%%
flowchart TD
    Dispecer[Dispečer]
    Radnik[Radnik]

    P21((2.1 Generisanje naloga))
    P22((2.2 Dodela radnika))
    P23((2.3 Evidentiranje utroška resursa))

    D1[(D1: Prijave)]
    D2[(D2: Nalozi)]
    D4[(D4: Utrošak)]

    D1 -->|Podaci za nalog| P21
    Dispecer -->|Određuje prioritet| P21
    P21 --> D2
    Dispecer -->|Zadužuje| P22
    P22 --> D2
    D2 -->|Prikaz dodele| Radnik
    Radnik -->|Sati rada, Asfalt| P23
    P23 --> D4
    P23 -->|Zatvaranje| D2
```

#### 2.3.3. Generisanje analitičkih izveštaja (Menadžment)

```mermaid
%%{init: {'theme': 'base', 'themeVariables': {'primaryColor': '#ffffff', 'primaryTextColor': '#000000', 'primaryBorderColor': '#000000', 'lineColor': '#000000', 'secondaryColor': '#ffffff', 'tertiaryColor': '#ffffff', 'edgeLabelBackground': '#ffffff'}}}%%
flowchart LR
    Menadzer[Menadžer]

    P41((4.1 Računanje troškova po km))
    P42((4.2 Generisanje toplotne mape))

    D2[(D2: Nalozi i Utrošak)]
    D3[(D3: Putevi)]
    D5[(D5: Nezgode)]
    D1[(D1: Prijave)]

    D2 & D3 --> P41
    D1 & D5 --> P42
    
    P41 -->|Finansijski Izveštaj| Menadzer
    P42 -->|Pregled 'Crnih Tačaka'| Menadzer
```

---

## 3. Rečnik podataka

### 3.1. Korisnik
Korisnik `<id, ime, email, sifra, uloga>`
| Polje | Tip podatka | Ograničenje |
|---|---|---|
| id | integer | Not null, jedinstveno, PK |
| ime | varchar(50) | Not null |
| email | varchar(100) | Not null, jedinstveno, email format |
| sifra | varchar(255) | Not null |
| uloga | varchar(20) | Not null (vozac/radnik/dispecer/menadzer) |

### 3.2. Deonica
Deonica `<id, naziv, kategorija, duzina_km, tip_asfalta, status>`
| Polje | Tip podatka | Ograničenje |
|---|---|---|
| id | integer | Not null, PK |
| naziv | varchar(100) | Not null |
| kategorija | varchar(20) | Not null (lokalni/magistralni/autoput) |
| duzina_km | decimal(8,2) | Not null, > 0 |
| status | varchar(20) | Not null |

### 3.3. Objekat (Infrastruktura)
Objekat `<id, tip, gps_lat, gps_lng, deonica_id, garancija_do>`
| Polje | Tip podatka | Ograničenje |
|---|---|---|
| id | integer | Not null, PK |
| tip | varchar(50) | Not null (semafor, znak, bankina...) |
| gps_lat | decimal(10,8) | Not null |
| deonica_id | integer | FK -> Deonica |

### 3.4. Prijava
Prijava `<id, korisnik_id, deonica_id, opis, gps_lat, gps_lng, status, datum_prijave>`
| Polje | Tip podatka | Ograničenje |
|---|---|---|
| id | integer | Not null, PK |
| korisnik_id | integer | FK -> Korisnik |
| status | varchar(20) | Not null (prijavljeno, verifikovano...) |

### 3.5. Nalog
Nalog `<id, prijava_id, radnik_id, deonica_id, opis, prioritet, status, kreirano_u>`
| Polje | Tip podatka | Ograničenje |
|---|---|---|
| id | integer | Not null, PK |
| radnik_id | integer | FK -> Korisnik |
| prioritet | varchar(20) | normalan, visok, kritican |

### 3.6. Utrošak
Utrošak `<id, nalog_id, naziv_resursa, kolicina, cena_po_jedinici, ukupna_cena>`
| Polje | Tip podatka | Ograničenje |
|---|---|---|
| id | integer | Not null, PK |
| nalog_id | integer | FK -> Nalog |
| kolicina | decimal(8,2) | Not null, > 0 |

---

## 4. EER model (Entiteti i Relacije)

```mermaid
%%{init: {'theme': 'base', 'themeVariables': {'primaryColor': '#ffffff', 'primaryTextColor': '#000000', 'primaryBorderColor': '#000000', 'lineColor': '#000000', 'secondaryColor': '#ffffff', 'tertiaryColor': '#ffffff', 'edgeLabelBackground': '#ffffff'}}}%%
flowchart TD

    %% ===== ENTITETI - Pravougaonici =====
    KORISNIK[KORISNIK]
    DEONICA[DEONICA]
    OBJEKAT[OBJEKAT]
    PRIJAVA[PRIJAVA]
    NALOG[NALOG]
    UTROSAK[UTROSAK]
    NEZGODA[NEZGODA]

    %% ===== ATRIBUTI - Jajasti krugovi =====
    K1([id PK])
    K2([uloga])
    K3([email])
    KORISNIK --- K1
    KORISNIK --- K2
    KORISNIK --- K3

    D1([id PK])
    D2([kategorija])
    D3([duzina_km])
    DEONICA --- D1
    DEONICA --- D2
    DEONICA --- D3

    O1([id PK])
    O2([tip])
    O3([garancija_do])
    OBJEKAT --- O1
    OBJEKAT --- O2
    OBJEKAT --- O3

    P1([id PK])
    P2([status])
    P3([gps_lat])
    P4([gps_lng])
    PRIJAVA --- P1
    PRIJAVA --- P2
    PRIJAVA --- P3
    PRIJAVA --- P4

    N1([id PK])
    N2([prioritet])
    N3([status])
    NALOG --- N1
    NALOG --- N2
    NALOG --- N3

    U1([id PK])
    U2([naziv_resursa])
    U3([kolicina])
    U4([ukupna_cena])
    UTROSAK --- U1
    UTROSAK --- U2
    UTROSAK --- U3
    UTROSAK --- U4

    NZ1([id PK])
    NZ2([tezina])
    NEZGODA --- NZ1
    NEZGODA --- NZ2

    %% ===== VEZE - Rombovi sa kardinalitetima =====
    R1{podnosi}
    KORISNIK ---|1| R1 ---|N| PRIJAVA

    R2{izvrsava}
    KORISNIK ---|1| R2 ---|N| NALOG

    R3{sadrzi}
    DEONICA ---|1| R3 ---|N| OBJEKAT

    R4{ima ostecenje}
    DEONICA ---|1| R4 ---|N| PRIJAVA

    R5{belezi nezgodu}
    DEONICA ---|1| R5 ---|N| NEZGODA

    R6{rezultuje kreiranjem}
    PRIJAVA ---|1| R6 ---|1| NALOG

    R7{koristi materijal}
    NALOG ---|1| R7 ---|N| UTROSAK

    R8{odrzava se}
    DEONICA ---|1| R8 ---|N| NALOG

    R9{odnosi se na}
    PRIJAVA ---|N| R9 ---|1| OBJEKAT
```

---

## 5. Relacioni model

*   **Korisnici** (<u>id</u>, ime, email, lozinka, uloga, created_at)
*   **Deonice** (<u>id</u>, naziv, kategorija, duzina_km, tip_asfalta, status, start_lat, start_lng, end_lat, end_lng)
*   **Infrastruktura** (<u>id</u>, tip, status, gps_lat, gps_lng, svojstva_json, <i>deonica_id</i>, ugradjeno, garancija_do)
*   **Prijave** (<u>id</u>, tip_problema, opis, gps_lat, gps_lng, fotografija, status, <i>korisnik_id</i>, <i>deonica_id</i>, prijavljeno_u)
*   **Nalozi** (<u>id</u>, tip, prioritet, status, opis, <i>prijava_id</i>, <i>deonica_id</i>, <i>radnik_id</i>, kreirano_u, zavrseno_u)
*   **Resursi_Utrosak** (<u>id</u>, <i>nalog_id</i>, tip_resursa, naziv_resursa, kolicina, cena_po_jedinici, ukupna_cena, zabelezeno_u)
*   **Nezgode** (<u>id</u>, opis, tezina, gps_lat, gps_lng, <i>deonica_id</i>, prijavljeno_u)
