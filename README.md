# Krilo Recipes

## Projekt: Krilo Recipes
Webová stránka s širokou ponukou receptov, navrhnutá pre používateľov s cieľom jednoducho a efektívne nájsť, pridať alebo hodnotiť recepty.

---

## Technológie
- **Frontend**: HTML, CSS, JavaScript

---

## Hlavné funkcie
- Prezeranie a vyhľadávanie receptov
- Vytváranie receptov
- Pridávanie receptov do "obľúbených"
- Hodnotenie receptov
- Registrácia/prihlásenie používateľa
- Filtrácia receptov podľa surovín

---

## Cieľové skupiny (Personas)

### 1. **Hladný človek** (Neprihlásený používateľ)

#### Scenáre:
1. Chce pripraviť jedlo z určitých ingrediencií, ale nevie, aké jedlo by sa z nich dalo pripraviť. 
   - Potrebuje inšpiráciu a rýchly výber receptov.
2. Vie, čo si chce pripraviť, ale nevie ako.
   - Hľadá jasný a zrozumiteľný postup prípravy jedla.
3. Nevie, čo chce uvariť, je znudený z opakovaných jedál.
   - Hľadá nové a zaujímavé recepty, rozhoduje sa podľa obrázkov.

#### Možnosti:
- Prehľadávať už hotové recepty

### 2. **Dobrý kuchár** (Prihlásený používateľ)

#### Scenáre:
1. Chce sa podeliť o svoje recepty a zdieľať ich s komunitou.
   - Chce inšpirovať ostatných svojimi unikátnymi receptami.
2. Chce pomôcť ostatným svojou konštruktívnou kritikou.
   - Poukazuje na možné vylepšenia alebo chváli dobré prvky receptu.

#### Možnosti:
- Všetky funkcie neprihláseného používateľa
- Pridávanie hodnotenia a komentárov k receptom
- Vytváranie vlastných receptov
- Pridávanie receptov do "obľúbených"

---

## Stránky a ich funkcie

### 1. **Hlavná stránka**
- Možnosť prihlásenia/registrácie vpravo hore
- Vyhľadávanie receptov podľa surovín alebo názvu
- Zobrazenie niekoľkých receptov vedľa seba na šírke stránky (PC)
- Kliknúť na recept (vyskočí popup)

### Popup
**Prihlasovacie vyskakovacie okno**
- Zadanie prihlasovacích údajov

**Registračné vyskakovacie okno**
- Zadanie prezývky, e-mailu a hesla
- Registrácia nového používateľa

### 2. **Profil používateľa** (Prihlásený používateľ)
- Zobrazenie uložených "obľúbených" receptov
- Zobrazenie receptov pridaných používateľom
- Pridávanie nových receptov 
- Zobrazenie počtu sledovateľov a sledovaných 
- Možnosť sledovania iných používateľov

### 3. **Stránka receptu**
- Obrázky jedla, postup prípravy a ingrediencie
- Hodnotenie receptu, recenzie a možnosť pridať hodnotenie

### 4. **Stránka na vytváranie receptov**
- Pridanie obrázka
- Zadanie názvu receptu
- Pridanie a upravovanie ingrediencií
- Napísanie postupu

---

## Use Cases

### 1. Vyhľadanie receptov podľa surovín
- Používatelia zadajú suroviny, ktoré majú k dispozícii.
- Zobrazia sa recepty, ktoré môžu pripraviť s týmito ingredienciami.
1. Používateľ sa prihlási.
2. Klikne na tlačidlo pridať recenziu a napíše recenziu/  vyberie si počet hviezdičiek ktoré chce receptu dať a klikne na toľkú hviezdičku zľava, koľko ich chce dať
3. Hodnotenie sa uloží do databázy a zväčší sa počet hodnotení/recenzia sa uloží do databázy.

### 2. Vytvorenie receptu
1. Používateľ sa prihlási.
2. Klikne na tlačidlo "Create" a presunie sa na stránku na vytváranie receptov.
3. Vyplní názov, ingrediencie, postup a pridá obrázok.
4. Recept sa uloží do databázy a sprístupní na stránke.

### 3. Hodnotenie a recenzie receptov
   - Len prihlásení používatelia môžu:
   - pridávať hodnotenia
   - pridávať recenzie

---

## Problém, ktorý projekt rieši
- Tradičné stránky s receptami sú často neprehľadné a preplnené nepotrebným obsahom.
- Užívatelia si recepty nechávajú vo fyzickej podobe, čo môže viesť k ich strate.

**Krilo Recipes** poskytuje:
- Prehľadnú a estetickú stránku, kde sú recepty jednoducho dostupné.
- Praktické riešenie, ktoré spája inšpiráciu, tvorbu a hodnotenie receptov.

---

## Klonujte tento repozitár:
   ```bash
   git clone https://github.com/David-ssnd/WSE-Project.git
   ```
---

## Prispievanie
Radi privítame nové nápady a vylepšenia! Pre príspevky prosím vytvorte **fork** repozitára a pošlite **pull request**.
